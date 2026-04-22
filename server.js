require("dotenv").config();
const express = require("express");
const cors = require("cors");
const path = require("path");

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
      paths: {
        "/api/health": {
          get: {
            summary: "Health check",
            tags: ["System"],
            responses: {
              200: {
                description: "Server is healthy",
                content: {
                  "application/json": {
                    schema: {
                      type: "object",
                      properties: {
                        status: { type: "string", example: "OK" },
                        timestamp: { type: "string", format: "date-time" },
                        message: { type: "string" },
                      },
                    },
                  },
                },
              },
            },
          },
        },
        "/api/documents": {
          get: {
            summary: "List documents",
            tags: ["Documents"],
            parameters: [
              {
                name: "language",
                in: "query",
                schema: { type: "string" },
                description: "Language folder (e.g. Python)",
              },
              { name: "category", in: "query", schema: { type: "string" } },
              {
                name: "fileType",
                in: "query",
                schema: { type: "string", enum: ["py", "md", "js", "txt"] },
              },
              { name: "search", in: "query", schema: { type: "string" } },
              {
                name: "page",
                in: "query",
                schema: { type: "integer", default: 1 },
              },
              {
                name: "limit",
                in: "query",
                schema: { type: "integer", default: 20 },
              },
              {
                name: "ungrouped",
                in: "query",
                schema: { type: "string", enum: ["true", "false"] },
              },
            ],
            responses: {
              200: {
                description: "Paginated list of documents",
                content: {
                  "application/json": {
                    schema: {
                      type: "object",
                      properties: {
                        documents: {
                          type: "array",
                          items: { $ref: "#/components/schemas/Document" },
                        },
                        total: { type: "integer" },
                        page: { type: "integer" },
                        pages: { type: "integer" },
                      },
                    },
                  },
                },
              },
            },
          },
        },
        "/api/documents/stats": {
          get: {
            summary: "Document statistics",
            tags: ["Documents"],
            parameters: [
              { name: "language", in: "query", schema: { type: "string" } },
            ],
            responses: {
              200: {
                description: "Stats",
                content: {
                  "application/json": {
                    schema: {
                      type: "object",
                      properties: {
                        totalDocuments: { type: "integer" },
                        totalWords: { type: "integer" },
                        byCategory: {
                          type: "array",
                          items: { type: "object" },
                        },
                        byFileType: {
                          type: "array",
                          items: { type: "object" },
                        },
                      },
                    },
                  },
                },
              },
            },
          },
        },
        "/api/documents/categories": {
          get: {
            summary: "List categories",
            tags: ["Documents"],
            parameters: [
              { name: "language", in: "query", schema: { type: "string" } },
            ],
            responses: {
              200: {
                description: "Category names",
                content: {
                  "application/json": {
                    schema: { type: "array", items: { type: "string" } },
                  },
                },
              },
            },
          },
        },
        "/api/documents/languages": {
          get: {
            summary: "List available language folders",
            tags: ["Documents"],
            responses: {
              200: {
                description: "Languages",
                content: {
                  "application/json": {
                    schema: {
                      type: "object",
                      properties: {
                        languages: { type: "array", items: { type: "string" } },
                      },
                    },
                  },
                },
              },
            },
          },
        },
        "/api/documents/tree": {
          get: {
            summary: "Get folder/file tree",
            tags: ["Documents"],
            parameters: [
              { name: "language", in: "query", schema: { type: "string" } },
            ],
            responses: {
              200: {
                description: "Nested tree",
                content: {
                  "application/json": {
                    schema: {
                      type: "object",
                      properties: {
                        tree: { type: "array", items: { type: "object" } },
                      },
                    },
                  },
                },
              },
            },
          },
        },
        "/api/documents/run-python": {
          post: {
            summary: "Execute Python code",
            tags: ["Execution"],
            requestBody: {
              required: true,
              content: {
                "application/json": {
                  schema: {
                    type: "object",
                    required: ["code"],
                    properties: {
                      code: { type: "string", example: "print('Hello World')" },
                      stdin: { type: "string", example: "" },
                    },
                  },
                },
              },
            },
            responses: {
              200: {
                description: "Execution result",
                content: {
                  "application/json": {
                    schema: {
                      type: "object",
                      properties: {
                        stdout: { type: "string" },
                        stderr: { type: "string" },
                        error: { type: "string", nullable: true },
                        exitCode: { type: "integer" },
                      },
                    },
                  },
                },
              },
            },
          },
        },
        "/api/documents/{filePath}": {
          get: {
            summary: "Get a specific file",
            tags: ["Documents"],
            parameters: [
              {
                name: "filePath",
                in: "path",
                required: true,
                description: "e.g. Python/data/sorting/bubble_sort.py",
                schema: { type: "string" },
              },
              { name: "language", in: "query", schema: { type: "string" } },
            ],
            responses: {
              200: {
                description: "File content",
                content: {
                  "application/json": {
                    schema: { $ref: "#/components/schemas/Document" },
                  },
                },
              },
              403: { description: "Access denied" },
              404: { description: "Not found" },
            },
          },
        },
        "/api/playground": {
          post: {
            summary: "Execute JS or Python in the playground",
            tags: ["Execution"],
            requestBody: {
              required: true,
              content: {
                "application/json": {
                  schema: {
                    type: "object",
                    required: ["language", "code"],
                    properties: {
                      language: {
                        type: "string",
                        enum: ["javascript", "python", "js", "py"],
                        example: "javascript",
                      },
                      code: {
                        type: "string",
                        example: "console.log('Hello World')",
                      },
                    },
                  },
                },
              },
            },
            responses: {
              200: {
                description: "Execution result",
                content: {
                  "application/json": {
                    schema: {
                      type: "object",
                      properties: {
                        output: { type: "string" },
                        error: { type: "string" },
                        exitCode: { type: "integer" },
                      },
                    },
                  },
                },
              },
              400: { description: "Bad request" },
            },
          },
        },
      },
    },
    apis: [],
  });
  console.log("✅ API spec ready");
}

// ─── App ──────────────────────────────────────────────────────────────────────
const app = express();

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

app.use(express.json({ limit: "50mb" }));
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
const documentRoutes = require("./src/modules/documents/documents.route");
app.use("/api/documents", documentRoutes);

const playgroundRoutes = require("./src/modules/playground/playground.route");
app.use("/api/playground", playgroundRoutes);

app.get("/api/health", (req, res) => {
  res.json({
    status: "OK",
    timestamp: new Date().toISOString(),
    message: "Backend is running",
  });
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
