require("dotenv").config();
const express = require("express");
const cors = require("cors");
const path = require("path");
const { connectToMongoDB } = require("./src/config/database");

let compression;
try {
  compression = require("compression");
} catch (e) {
  console.warn("⚠️  Compression module not found. Run: npm install compression");
}

let rateLimit;
try {
  rateLimit = require("express-rate-limit");
} catch (e) {
  console.warn("⚠️  Rate limiting module not found. Run: npm install express-rate-limit");
}

// ─── OpenAPI Spec ─────────────────────────────────────────────────────────────
let swaggerJsdoc;
try {
  swaggerJsdoc = require("swagger-jsdoc");
} catch (e) {
  console.warn("⚠️  swagger-jsdoc not found. Run: npm install swagger-jsdoc");
}

let __swaggerSpec = null;
if (swaggerJsdoc) {
  __swaggerSpec = swaggerJsdoc({
    definition: {
      openapi: "3.0.0",
      info: {
        title: "PolyCode API",
        version: "1.0.0",
        description: "API reference for PolyCode Backend",
      },
      servers: [
        {
          url: `http://localhost:${process.env.PORT || 5000}`,
          description: "Dev server",
        },
      ],
      components: {
        schemas: {
          Document: {
            type: "object",
            properties: {
              title: { type: "string" },
              path: { type: "string" },
              category: { type: "string" },
              fileType: { type: "string" },
              size: { type: "number" },
              excerpt: { type: "string" },
              lines: { type: "number" },
              wordCount: { type: "number" },
            },
          },
          ErrorResponse: {
            type: "object",
            properties: { error: { type: "string" } },
          },
        },
      },
      // paths: {} routers was define here and now they are on their own files
    },
    apis: [],
  });
  console.log("✅ API spec ready");
}

// ─── App ──────────────────────────────────────────────────────────────────────
const app = express();
app.disable("x-powered-by");

app.use((req, res, next) => {
  res.setHeader("X-Content-Type-Options", "nosniff");
  res.setHeader("X-Frame-Options", "DENY");
  res.setHeader("Referrer-Policy", "no-referrer");
  res.setHeader("Cross-Origin-Opener-Policy", "same-origin");
  next();
});

if (compression) {
  app.use(
    compression({
      level: 6,
      threshold: 1024,
      filter: (req, res) => {
        if (req.headers["x-no-compression"]) return false;
        return compression.filter(req, res);
      },
    }),
  );
  console.log("✅ Compression enabled");
}

app.use(
  cors({
    origin: ["http://localhost:3000", "http://127.0.0.1:3000"],
    credentials: true,
    methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    allowedHeaders: ["Content-Type", "Authorization"],
  }),
);

app.use(express.json({ limit: "1mb" }));
app.use(express.urlencoded({ extended: true }));

app.use((req, res, next) => {
  const start = Date.now();
  res.on("finish", () => {
    const d = Date.now() - start;
    if (d > 1000) console.warn(`🐌 Slow: ${req.method} ${req.path} - ${d}ms`);
    else console.log(`⚡ ${req.method} ${req.path} - ${d}ms`);
  });
  next();
});

if (rateLimit) {
  app.use(
    "/api/",
    rateLimit({
      windowMs: 15 * 60 * 1000,
      max: 100,
      message: "Too many requests from this IP, please try again later.",
      standardHeaders: true,
      legacyHeaders: false,
    }),
  );
  console.log("✅ Rate limiting enabled");
}

// ─── Docs routes ──────────────────────────────────────────────────────────────
// Serve logo.png from the project root
app.get("/logo.png", (req, res) => {
  res.sendFile(path.join(__dirname, "logo.png"));
});

// Raw OpenAPI JSON spec
app.get("/api-docs.json", (req, res) => {
  if (!__swaggerSpec)
    return res.status(503).json({ error: "Spec not available" });
  res.setHeader("Content-Type", "application/json");
  res.send(__swaggerSpec);
});

// Custom docs HTML page
app.get("/api-docs", (req, res) => {
  res.sendFile(path.join(__dirname, "api-docs.html"));
});

// ─── API Routes ───────────────────────────────────────────────────────────────
// Initialize MongoDB connection
connectToMongoDB().catch((err) => {
  console.error("MongoDB initialization error:", err.message);
});

// Auth Routes (User & Progress)
const authRoutes = require("./src/modules/auth/auth.router");
app.use("/api/auth", authRoutes);

const documentRoutes = require("./src/modules/documents/documents.router");
app.use("/api/documents", documentRoutes);

const playgroundRoutes = require("./src/modules/playground/playground.route");
app.use("/api/playground", playgroundRoutes);

// Backward compatibility for older frontend builds requesting /languages directly
app.get("/languages", (req, res) => {
  return res.redirect(307, "/api/documents/languages");
});

app.get("/api/health", (req, res) => {
  res.json({ status: "OK", timestamp: new Date().toISOString(), message: "Backend is running" });
});

if (process.env.NODE_ENV === "production") {
  app.use(express.static(path.join(__dirname, "../PolyCode-Frontend/build")));
  app.get("*", (req, res) => {
    res.sendFile(path.join(__dirname, "../PolyCode-Frontend/build/index.html"));
  });
}

// ─── Start ────────────────────────────────────────────────────────────────────
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`🚀  Server:    http://localhost:${PORT}`);
  console.log(`📖  API Docs:  http://localhost:${PORT}/api-docs`);
});