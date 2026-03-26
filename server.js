require("dotenv").config();
const express = require("express");
const cors = require("cors");
const path = require("path");

// Try to import compression, but don't fail if it's not installed yet
let compression;
try {
  compression = require('compression');
} catch (e) {
  console.warn('⚠️  Compression module not found. Run: npm install compression');
}

// Try to import rate limiting, but don't fail if it's not installed yet  
let rateLimit;
try {
  rateLimit = require('express-rate-limit');
} catch (e) {
  console.warn('⚠️  Rate limiting module not found. Run: npm install express-rate-limit');
}

const app = express();

// ─── Performance Middleware ────────────────────────────────────────────────────────
// Enable gzip compression if available
if (compression) {
  app.use(compression({
    level: 6,
    threshold: 1024,
    filter: (req, res) => {
      if (req.headers['x-no-compression']) {
        return false;
      }
      return compression.filter(req, res);
    }
  }));
  console.log('✅ Compression enabled');
} else {
  console.log('⚠️  Compression disabled (missing dependency)');
}

// Enhanced CORS configuration
app.use(
  cors({
    origin: ["http://localhost:3000", "http://127.0.0.1:3000"], // Allow frontend
    credentials: true,
    methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    allowedHeaders: ["Content-Type", "Authorization"],
  }),
);

app.use(express.json({ limit: "50mb" }));
app.use(express.urlencoded({ extended: true }));

// Add performance monitoring
app.use((req, res, next) => {
  const start = Date.now();
  
  res.on('finish', () => {
    const duration = Date.now() - start;
    if (duration > 1000) {
      console.warn(`🐌 Slow request: ${req.method} ${req.path} - ${duration}ms`);
    } else {
      console.log(`⚡ ${req.method} ${req.path} - ${duration}ms`);
    }
  });
  
  next();
});

// Rate limiting for API endpoints (if available)
if (rateLimit) {
  const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100, // limit each IP to 100 requests per windowMs
    message: 'Too many requests from this IP, please try again later.',
    standardHeaders: true,
    legacyHeaders: false,
  });
  
  app.use('/api/', limiter);
  console.log('✅ Rate limiting enabled');
} else {
  console.log('⚠️  Rate limiting disabled (missing dependency)');
}

// ─── Routes ───────────────────────────────────────────────────────────────────
const documentRoutes = require("./routes/documents");
app.use("/api/documents", documentRoutes);

// Health check endpoint
app.get("/api/health", (req, res) => {
  res.json({
    status: "OK",
    timestamp: new Date().toISOString(),
    message: "Backend is running",
  });
});

// Serve React build in production
if (process.env.NODE_ENV === "production") {
  app.use(express.static(path.join(__dirname, "../frontend/build")));
  app.get("*", (req, res) => {
    res.sendFile(path.join(__dirname, "../frontend/build/index.html"));
  });
}

// ─── Startup ──────────────────────────────────────────────────────────────────
const PORT = process.env.PORT || 5000;

app.listen(PORT, () => {
  console.log(`🚀  Server running on http://localhost:${PORT}`);
  console.log(
    `📁  Reading Python files from: ${path.join(__dirname, "data/Python")}`,
  );
  console.log(`🔍  API endpoints available:`);
  console.log(`     GET /api/documents - List all Python files`);
  console.log(`     GET /api/documents/stats - Statistics`);
  console.log(`     GET /api/documents/categories - Categories`);
  console.log(`     GET /api/documents/*path - Get specific file`);
});
