require("dotenv").config();
const express = require("express");
const cors = require("cors");
const path = require("path");
const mongoose = require("mongoose");
const {
  connectToMongoDB,
  requireMongoConnection,
} = require("./src/config/database");

let compression;
try {
  compression = require("compression");
} catch (e) {
  console.warn(
    "⚠️  Compression module not found. Run: npm install compression",
  );
}

let rateLimit;
try {
  rateLimit = require("express-rate-limit");
} catch (e) {
  console.warn(
    "⚠️  Rate limiting module not found. Run: npm install express-rate-limit",
  );
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

function normalizeOrigin(origin = "") {
  // normalizeOrigin is a function that normalizes the origin
  return origin.trim().replace(/\/$/, "");
}

const defaultAllowedOrigins = [
  // defaultAllowedOrigins is an array of allowed origins
  "https://code.quantumlogicslimited.com",
  "https://www.code.quantumlogicslimited.com",
  "https://quantumlogicslimited.com",
  "https://www.quantumlogicslimited.com",
  "https://digital-logics-studio.vercel.app",
  "https://poly-code-frontend-iota.vercel.app",
  "http://localhost:3000",
  "http://127.0.0.1:3000",
];

const allowedOrigins = new Set( // Set is a collection of unique values
  [
    ...defaultAllowedOrigins,
    process.env.FRONTEND_URL,
    process.env.PROD_FRONTEND_URL,
    ...(process.env.CORS_ORIGINS || "")
      .split(",")
      .map((origin) => origin.trim())
      .filter(Boolean),
  ]
    .map(normalizeOrigin)
    .filter(Boolean),
);

const isAllowedOrigin = (origin) => {
  if (!origin) return true;

  const normalizedOrigin = normalizeOrigin(origin);
  let hostname = "";
  try {
    hostname = new URL(normalizedOrigin).hostname;
  } catch (error) {
    return false;
  }

  if (allowedOrigins.has(normalizedOrigin) || /\.vercel\.app$/.test(hostname)) {
    return true;
  }

  // Local dev from LAN IP (e.g. http://192.168.x.x:3000)
  if (process.env.NODE_ENV !== "production") {
    return /^(localhost|127\.0\.0\.1|192\.168\.|10\.|172\.(1[6-9]|2\d|3[01])\.)/.test(
      hostname,
    );
  }

  return false;
};

const CORS_METHODS = "GET,POST,PUT,DELETE,PATCH,OPTIONS";
const CORS_HEADERS = "Content-Type, Authorization, X-Requested-With";

function applyCorsHeaders(req, res) {
  const origin = req.headers.origin;
  if (!origin || !isAllowedOrigin(origin)) return false;

  const normalizedOrigin = normalizeOrigin(origin);
  res.setHeader("Access-Control-Allow-Origin", normalizedOrigin);
  res.setHeader("Access-Control-Allow-Credentials", "true");
  res.setHeader("Vary", "Origin");
  return true;
}

// Handle preflight and attach CORS headers before any other middleware.
app.use((req, res, next) => {
  applyCorsHeaders(req, res);

  if (req.method === "OPTIONS") {
    res.setHeader("Access-Control-Allow-Methods", CORS_METHODS);
    res.setHeader("Access-Control-Allow-Headers", CORS_HEADERS);
    res.setHeader("Access-Control-Max-Age", "86400");
    return res.sendStatus(204);
  }

  return next();
});

const corsOptions = {
  origin(origin, callback) {
    if (!origin) return callback(null, true);

    if (isAllowedOrigin(origin)) {
      return callback(null, normalizeOrigin(origin));
    }

    console.warn(`🚫 CORS blocked origin: ${origin}`);
    return callback(null, false);
  },
  credentials: true,
  methods: ["GET", "POST", "PUT", "DELETE", "PATCH", "OPTIONS"],
  allowedHeaders: ["Content-Type", "Authorization", "X-Requested-With"],
  optionsSuccessStatus: 204,
};

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

app.use(cors(corsOptions));
app.options(/.*/, cors(corsOptions));

app.use(express.json({ limit: "2mb" }));

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
  const isProd = process.env.NODE_ENV === "production";
  const rateLimitDisabled =
    process.env.RATE_LIMIT_DISABLED === "1" ||
    process.env.RATE_LIMIT_DISABLED === "true" ||
    (!isProd && process.env.RATE_LIMIT_DISABLED !== "0");

  if (rateLimitDisabled) {
    console.log("⚠️  Rate limiting disabled (dev / RATE_LIMIT_DISABLED)");
  } else {
    const windowMs = Number(process.env.RATE_LIMIT_WINDOW_MS) || 15 * 60 * 1000;
    const max =
      Number(process.env.RATE_LIMIT_MAX) || (isProd ? 500 : 5000);
    app.use(
      "/api/",
      rateLimit({
        windowMs,
        max,
        message: "Too many requests from this IP, please try again later.",
        standardHeaders: true,
        legacyHeaders: false,
      }),
    );
    console.log(`✅ Rate limiting enabled (${max} req / ${windowMs}ms)`);
  }
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
// Warm MongoDB on cold start (serverless); auth routes also await connection.
connectToMongoDB().catch((err) => {
  if (/bad auth|authentication failed/i.test(err.message)) {
    console.error("MongoDB initialization error:", err.message);
    console.error(
      "   → Run: npm run db:setup   (reset password in Atlas → Database Access first)",
    );
    return;
  }
  console.error("MongoDB initialization error:", err.message);
});

// Auth Routes (User & Progress) — require DB before register/login
const authRoutes = require("./src/modules/auth/auth.router");
app.use("/api/auth", requireMongoConnection, authRoutes);

const documentRoutes = require("./src/modules/documents/documents.router");
app.use("/api/documents", documentRoutes);

const playgroundRoutes = require("./src/modules/playground/playground.route");
app.use("/api/playground", playgroundRoutes);

const challengeRoutes = require("./src/routes/challenge");
app.use("/api/challenges", challengeRoutes);

const chatRoutes = require("./src/modules/chat/chat.router");
app.use("/api/chat", requireMongoConnection, chatRoutes);

const certificateRoutes = require("./src/modules/certificates/certificates.router");
app.use("/api/certificates", requireMongoConnection, certificateRoutes);

// Backward compatibility for older frontend builds requesting /languages directly
app.get("/languages", (req, res) => {
  return res.redirect(307, "/api/documents/languages");
});

app.get("/api/health", async (req, res) => {
  let mongo = "not_configured";
  try {
    if (!process.env.MONGODB_URI?.trim()) {
      mongo = "not_configured";
    } else {
      await connectToMongoDB();
      mongo =
        mongoose.connection.readyState === 1 ? "connected" : "disconnected";
    }
  } catch (error) {
    mongo = "error";
  }

  res.json({
    status: "OK",
    timestamp: new Date().toISOString(),
    message: "Backend is running",
    mongo,
  });
});

// Serve bundled frontend only when API and UI run on the same host (not on Vercel backend-only).
if (process.env.NODE_ENV === "production" && !process.env.VERCEL) {
  app.use(express.static(path.join(__dirname, "../PolyCode-Frontend/build")));
  app.get("*", (req, res) => {
    res.sendFile(path.join(__dirname, "../PolyCode-Frontend/build/index.html"));
  });
}

app.use((err, req, res, next) => {
  applyCorsHeaders(req, res);
  console.error("Unhandled error:", err?.message || err);

  if (res.headersSent) {
    return next(err);
  }

  const status = err?.status || 500;
  return res.status(status).json({
    error: err?.message || "Internal server error",
  });
});

// ─── Start ────────────────────────────────────────────────────────────────────
const PORT = process.env.PORT || 5000;

module.exports = app;

// Only bind a port when executed directly (`node server.js`), not on Vercel serverless.
if (require.main === module) {
  app.listen(PORT, async () => {
    console.log(`🚀  Server:    http://localhost:${PORT}`);
    console.log(`📖  API Docs:  http://localhost:${PORT}/api-docs`);

    try {
      const { isDriveConfigured, verifyOAuthConnection, useOAuthMode } =
        require("./src/services/googleDriveService");
      const { warnIfTokenMalformed, resolveRefreshToken } =
        require("./src/services/googleOAuthStore");

      if (isDriveConfigured() && useOAuthMode()) {
        warnIfTokenMalformed(resolveRefreshToken()?.refresh_token || "");
        const check = await verifyOAuthConnection();
        if (check.ok) {
          console.log("✅  Google Drive OAuth: refresh token OK");
        } else if (!check.skipped) {
          console.warn("⚠️  Google Drive OAuth failed on startup.");
          console.warn(check.error?.split("\n")[0] || check.error);
          console.warn("   Run: node scripts/google-drive-oauth-setup.js");
        }
      }
    } catch (driveCheckError) {
      console.warn(
        "⚠️  Google Drive startup check skipped:",
        driveCheckError.message,
      );
    }
  });
}
