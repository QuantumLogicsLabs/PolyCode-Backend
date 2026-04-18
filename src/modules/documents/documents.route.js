const express = require("express");
const router = express.Router();
const fs = require("fs").promises;
const path = require("path");
const { spawn } = require("child_process");

// Try to import Worker threads, but don't fail if not available
let Worker;
try {
  Worker = require("worker_threads").Worker;
} catch (e) {
  console.warn(
    "⚠️  Worker threads not available, using main thread for heavy operations",
  );
}

// Path to your folders
const DATA_BASE_PATH = path.join(__dirname, "../../../data");
const PYTHON_DATA_PATH = path.join(DATA_BASE_PATH, "Python", "data");
const RUN_TIMEOUT_MS = 8000;
let resolvedPythonCommand = null;

// ─── Enhanced Caching Implementation ──────────────────────────────────────────────────
const CACHE_TTL = 5 * 60 * 1000; // 5 minutes cache (increased from 1 minute)
const cache = {
  languages: { data: null, timestamp: 0 },
  trees: new Map(), // language -> { data, timestamp }
  stats: new Map(), // language -> { data, timestamp }
  documents: new Map(), // language -> { data, timestamp }
  fileIndex: new Map(), // path -> { data, timestamp }
};

// Memory management - clear old cache entries periodically
setInterval(() => {
  const now = Date.now();
  for (const [key, map] of [
    ["trees", cache.trees],
    ["stats", cache.stats],
    ["documents", cache.documents],
    ["fileIndex", cache.fileIndex],
  ]) {
    for (const [innerKey, value] of map.entries()) {
      if (now - value.timestamp >= CACHE_TTL) {
        map.delete(innerKey);
      }
    }
  }
}, CACHE_TTL);

function getFromCache(cacheObj, key) {
  const item = key ? cacheObj.get(key) : cacheObj.data;
  if (item && Date.now() - item.timestamp < CACHE_TTL) {
    return item.data;
  }
  return null;
}

function setInCache(cacheObj, key, data) {
  const item = { data, timestamp: Date.now() };
  if (key) {
    cacheObj.set(key, item);
  } else {
    cacheObj.data = item;
  }
}

function runSpawn(command, args = [], options = {}) {
  return new Promise((resolve, reject) => {
    const child = spawn(command, args, options);
    child.once("error", reject);
    child.once("spawn", () => resolve(child));
  });
}

async function resolvePythonCommand() {
  if (resolvedPythonCommand) return resolvedPythonCommand;
  const candidates =
    process.platform === "win32"
      ? [process.env.PYTHON_EXECUTABLE, "py -3", "python", "python3"]
      : [process.env.PYTHON_EXECUTABLE, "python3", "python"];

  for (const candidate of candidates.filter(Boolean)) {
    const [cmd, ...args] = candidate.split(" ");
    try {
      const probe = await runSpawn(cmd, [...args, "--version"], {
        stdio: ["ignore", "pipe", "pipe"],
      });
      await new Promise((resolve, reject) => {
        probe.once("exit", (code) =>
          code === 0 ? resolve() : reject(new Error("non-zero exit")),
        );
        probe.once("error", reject);
      });
      resolvedPythonCommand = candidate;
      return resolvedPythonCommand;
    } catch (_) {
      // Try next candidate
    }
  }
  throw new Error(
    "No Python runtime found on server. Install Python or set PYTHON_EXECUTABLE.",
  );
}

async function executePythonCode(code, stdin = "") {
  const command = await resolvePythonCommand();
  const [cmd, ...baseArgs] = command.split(" ");
  const autoInputPrelude = `
import builtins, re

def __polycode_auto_input(prompt=''):
    p = '' if prompt is None else str(prompt).lower()
    # yes/no prompts
    if 'y/n' in p or 'y / n' in p or re.search(r'\\by/n\\b', p):
        return 'n'
    # menu/choice prompts
    if 'select' in p or 'choice' in p or 'option' in p or re.search(r'\\(1\\s*-\\s*\\d+\\)', p):
        return '1'
    # common values
    if 'password' in p:
        return 'password'
    if 'name' in p:
        return 'Bob'
    if 'id' in p:
        return '1'
    if 'url' in p:
        return 'http://example.com'
    if 'command' in p:
        return 'echo hello'
    if 'file' in p or 'filename' in p or 'path' in p:
        return 'input.txt'
    if 'directory' in p or 'folder' in p:
        return '.'
    if 'target host' in p or 'host' in p:
        return 'localhost'
    if 'port' in p:
        return '80'
    if 'age' in p:
        return '20'
    # numeric-ish
    if re.search(r'(age|hours|minutes|rpm|degrees|score|rate|amount|quantity|number)', p):
        return '0'
    return ''

builtins.input = __polycode_auto_input
`;

  const args = [...baseArgs, "-c", `${autoInputPrelude}\n${code}`];

  return new Promise(async (resolve, reject) => {
    let child;
    try {
      child = await runSpawn(cmd, args, {
        cwd: PYTHON_DATA_PATH,
        env: { ...process.env, PYTHONIOENCODING: "utf-8" },
        stdio: ["pipe", "pipe", "pipe"],
      });
    } catch (e) {
      reject(e);
      return;
    }

    let stdout = "";
    let stderr = "";
    const timer = setTimeout(() => {
      child.kill("SIGKILL");
      reject(new Error(`Python execution timed out after ${RUN_TIMEOUT_MS}ms`));
    }, RUN_TIMEOUT_MS);

    child.stdout.on("data", (chunk) => {
      stdout += chunk.toString();
    });
    child.stderr.on("data", (chunk) => {
      stderr += chunk.toString();
    });
    child.on("error", (e) => {
      clearTimeout(timer);
      reject(e);
    });
    child.on("close", (code) => {
      clearTimeout(timer);
      resolve({
        stdout: stdout.trimEnd(),
        stderr: stderr.trimEnd(),
        error:
          code === 0
            ? null
            : stderr.trimEnd() || `Python exited with code ${code}`,
        exitCode: code,
      });
    });

    if (stdin) child.stdin.write(stdin);
    child.stdin.end();
  });
}

// ─── Worker Thread for Heavy Operations ───────────────────────────────────────────────
function createWorkerTask(operation, data) {
  if (!Worker) {
    // Fallback to main thread execution
    console.warn("⚠️  Worker threads not available, executing in main thread");
    return Promise.reject(new Error("Worker threads not available"));
  }

  return new Promise((resolve, reject) => {
    const worker = new Worker(__dirname + "/worker.js", {
      workerData: { operation, data },
    });

    worker.on("message", resolve);
    worker.on("error", reject);
    worker.on("exit", (code) => {
      if (code !== 0) {
        reject(new Error(`Worker stopped with exit code ${code}`));
      }
    });
  });
}

// ─── Optimized File Operations ───────────────────────────────────────────────────────
// Batch file reading for better performance
async function batchReadFiles(filePaths, options = {}) {
  const { readMetadata = true, readContent = false } = options;
  const batchSize = 10; // Process files in batches to avoid overwhelming the system

  const results = [];
  for (let i = 0; i < filePaths.length; i += batchSize) {
    const batch = filePaths.slice(i, i + batchSize);
    const batchPromises = batch.map((filePath) =>
      getFileInfo(filePath, path.relative(DATA_BASE_PATH, filePath), options),
    );
    const batchResults = await Promise.allSettled(batchPromises);

    batchResults.forEach((result) => {
      if (result.status === "fulfilled" && result.value) {
        results.push(result.value);
      }
    });
  }

  return results;
}

// Enhanced file info with better error handling and performance
async function getFileInfo(filePath, relativePath, options = {}) {
  const { readMetadata = true, readContent = false } = options;

  try {
    // Check cache first for individual files
    const cacheKey = `${relativePath}:${readContent ? "full" : "meta"}`;
    const cached = getFromCache(cache.fileIndex, cacheKey);
    if (cached && !readContent) {
      return cached;
    }

    const stats = await fs.stat(filePath);
    const ext = path.extname(filePath).toLowerCase();

    let fileType = "other";
    const fileTypeMap = {
      ".py": "python",
      ".md": "markdown",
      ".txt": "text",
      ".js": "javascript",
      ".jsx": "javascript",
      ".ts": "typescript",
      ".tsx": "typescript",
      ".html": "html",
      ".css": "css",
      ".c": "c",
      ".cpp": "cpp",
      ".java": "java",
      ".go": "go",
      ".rs": "rust",
      ".php": "php",
      ".rb": "ruby",
      ".cs": "csharp",
      ".sql": "sql",
      ".sh": "shell",
      ".bash": "shell",
    };

    fileType = fileTypeMap[ext] || "other";

    const normalizedPath = relativePath.replace(/\\/g, "/");
    let category = path.dirname(normalizedPath);
    if (category === ".") category = "general";
    category =
      category.replace(/^data\//i, "").replace(/^data$/i, "general") ||
      "general";

    const baseInfo = {
      title: path.basename(filePath, ext),
      path: relativePath,
      category: category,
      fileType: fileType,
      size: stats.size,
      createdAt: stats.birthtime,
      modifiedAt: stats.mtime,
    };

    if (!readMetadata && !readContent) {
      setInCache(cache.fileIndex, cacheKey, baseInfo);
      return baseInfo;
    }

    if (readContent) {
      const content = await fs.readFile(filePath, "utf8");
      const result = {
        ...baseInfo,
        content: content,
        lines: content.split("\n").length,
        excerpt:
          content.split("\n")[0].replace(/[#"']/g, "").trim() ||
          "No description available",
        wordCount: content.split(/\s+/).length,
      };
      setInCache(cache.fileIndex, cacheKey, result);
      return result;
    }

    // Optimized metadata reading - use file size estimates for large files
    if (stats.size > 100 * 1024) {
      // Files larger than 100KB
      const result = {
        ...baseInfo,
        lines: Math.floor(stats.size / 40),
        excerpt: "Large file - preview not available",
        wordCount: Math.floor(stats.size / 6),
      };
      setInCache(cache.fileIndex, cacheKey, result);
      return result;
    }

    // Small files - read first 1KB
    const buffer = Buffer.alloc(1024);
    const fd = await fs.open(filePath, "r");
    try {
      const { bytesRead } = await fd.read(buffer, 0, 1024, 0);
      const chunk = buffer.toString("utf8", 0, bytesRead);
      const lines = chunk.split("\n");
      const firstLine = lines[0].replace(/[#"']/g, "").trim();

      const result = {
        ...baseInfo,
        lines: Math.floor(stats.size / 40),
        excerpt: firstLine || "No description available",
        wordCount: Math.floor(stats.size / 6),
      };
      setInCache(cache.fileIndex, cacheKey, result);
      return result;
    } finally {
      await fd.close();
    }
  } catch (error) {
    console.error(`Error reading file ${filePath}:`, error.message);
    return null;
  }
}

// Optimized directory scanning with parallel processing
async function scanDirectory(
  dirPath,
  basePath = DATA_BASE_PATH,
  maxDepth = 10,
  currentDepth = 0,
) {
  if (currentDepth >= maxDepth) return [];

  try {
    const entries = await fs.readdir(dirPath, { withFileTypes: true });
    const allowedExtensions = [
      ".py",
      ".md",
      ".txt",
      ".js",
      ".jsx",
      ".ts",
      ".tsx",
      ".html",
      ".css",
      ".go",
      ".java",
      ".c",
      ".cpp",
      ".rs",
      ".php",
      ".rb",
      ".sql",
      ".sh",
      ".bash",
    ];

    // Filter and categorize entries
    const files = [];
    const directories = [];

    for (const entry of entries) {
      if (entry.name.startsWith(".")) continue;
      if (
        [
          "node_modules",
          "venv",
          "__pycache__",
          ".git",
          "dist",
          "build",
        ].includes(entry.name)
      )
        continue;

      const fullPath = path.join(dirPath, entry.name);
      const relativePath = path.relative(basePath, fullPath);

      if (entry.isDirectory()) {
        directories.push({ fullPath, relativePath });
      } else if (entry.isFile()) {
        const ext = path.extname(entry.name).toLowerCase();
        if (allowedExtensions.includes(ext)) {
          files.push({ fullPath, relativePath });
        }
      }
    }

    // Process files in parallel batches
    const fileResults = await batchReadFiles(
      files.map((f) => f.fullPath),
      { readMetadata: true },
    );

    // Recursively scan directories with limited parallelism
    const dirPromises = directories
      .slice(0, 5)
      .map(async ({ fullPath, relativePath }) => {
        try {
          return await scanDirectory(
            fullPath,
            basePath,
            maxDepth,
            currentDepth + 1,
          );
        } catch (error) {
          console.error(`Error scanning directory ${fullPath}:`, error.message);
          return [];
        }
      });

    const dirResults = await Promise.all(dirPromises);

    return [...fileResults.filter(Boolean), ...dirResults.flat()];
  } catch (error) {
    console.error(`Error scanning directory ${dirPath}:`, error.message);
    return [];
  }
}

// GET /api/documents - list all with optional filters
router.get("/", async (req, res) => {
  try {
    const {
      language = "all",
      category,
      fileType,
      search,
      page = 1,
      limit = 20,
    } = req.query;

    let finalDocs = getFromCache(cache.documents, language);

    if (!finalDocs) {
      let scanPath =
        language === "all"
          ? DATA_BASE_PATH
          : path.join(DATA_BASE_PATH, language);
      let allDocuments = [];

      if (
        await fs
          .access(scanPath)
          .then(() => true)
          .catch(() => false)
      ) {
        allDocuments = await scanDirectory(scanPath, scanPath);
      }

      const uniqueDocs = allDocuments.reduce((acc, doc) => {
        const key = `${doc.path}-${doc.title}`;
        if (!acc.has(key)) acc.set(key, doc);
        return acc;
      }, new Map());

      finalDocs = Array.from(uniqueDocs.values());
      setInCache(cache.documents, language, finalDocs);
    }

    const topicDocs = finalDocs.filter((doc) => doc.fileType === "markdown");
    const codeDocs = finalDocs.filter((doc) => doc.fileType !== "markdown");

    const groupedDocs = topicDocs.map((topic) => {
      const topicKeywords = topic.title.toLowerCase().replace(/[^a-z0-9]/g, "");
      const topicKeywordsNormalized = topicKeywords.endsWith("s")
        ? topicKeywords.slice(0, -1)
        : topicKeywords;
      const relatedCode = codeDocs.filter((code) => {
        const codeKey = code.title.toLowerCase().replace(/[^a-z0-9]/g, "");
        const codeKeyNormalized = codeKey.endsWith("s")
          ? codeKey.slice(0, -1)
          : codeKey;
        return (
          codeKeyNormalized.includes(topicKeywordsNormalized) ||
          topicKeywordsNormalized.includes(codeKeyNormalized)
        );
      });
      return { ...topic, isTopic: true, relatedCode };
    });

    const matchedCodePaths = new Set();
    groupedDocs.forEach((topic) =>
      topic.relatedCode.forEach((c) => matchedCodePaths.add(c.path)),
    );
    const standaloneCode = codeDocs
      .filter((c) => !matchedCodePaths.has(c.path))
      .map((c) => ({ ...c, relatedCode: [] }));

    let filteredDocs =
      req.query.ungrouped === "true"
        ? finalDocs
        : [...groupedDocs, ...standaloneCode];

    if (category && category !== "all") {
      filteredDocs = filteredDocs.filter(
        (doc) => doc.category.toLowerCase() === category.toLowerCase(),
      );
    }

    if (fileType && fileType !== "all") {
      let targetType =
        fileType === "md"
          ? "markdown"
          : fileType === "py"
            ? "python"
            : fileType;
      filteredDocs = filteredDocs.filter(
        (doc) =>
          doc.fileType === targetType ||
          (doc.relatedCode &&
            doc.relatedCode.some((c) => c.fileType === targetType)),
      );
    }

    if (search) {
      const s = search.toLowerCase();
      filteredDocs = filteredDocs.filter(
        (doc) =>
          doc.title.toLowerCase().includes(s) ||
          (doc.excerpt && doc.excerpt.toLowerCase().includes(s)) ||
          (doc.content && doc.content.toLowerCase().includes(s)) ||
          (doc.relatedCode &&
            doc.relatedCode.some(
              (c) =>
                c.title.toLowerCase().includes(s) ||
                (c.content && c.content.toLowerCase().includes(s)),
            )),
      );
    }

    filteredDocs.sort((a, b) =>
      a.isTopic === b.isTopic
        ? a.category === b.category
          ? a.title.localeCompare(b.title)
          : a.category.localeCompare(b.category)
        : a.isTopic
          ? -1
          : 1,
    );

    const skip = (parseInt(page) - 1) * parseInt(limit);
    const paginatedDocs = filteredDocs.slice(skip, skip + parseInt(limit));
    const listDocs = paginatedDocs.map(({ content, relatedCode, ...rest }) => ({
      ...rest,
      relatedCode: relatedCode
        ? relatedCode.map(({ content: cc, ...cr }) => cr)
        : [],
    }));

    res.json({
      documents: listDocs,
      total: filteredDocs.length,
      page: parseInt(page),
      pages: Math.ceil(filteredDocs.length / limit),
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// GET /api/documents/stats
router.get("/stats", async (req, res) => {
  try {
    const { language = "all" } = req.query;
    let stats = getFromCache(cache.stats, language);

    if (!stats) {
      let scanPath =
        language === "all"
          ? DATA_BASE_PATH
          : path.join(DATA_BASE_PATH, language);
      let docs = (await fs
        .access(scanPath)
        .then(() => true)
        .catch(() => false))
        ? await scanDirectory(scanPath, scanPath)
        : [];

      const uniqueDocs = Array.from(
        docs
          .reduce((acc, d) => {
            const k = `${d.path}-${d.title}`;
            if (!acc.has(k)) acc.set(k, d);
            return acc;
          }, new Map())
          .values(),
      );

      const byCategory = {};
      const byFileType = {};
      uniqueDocs.forEach((d) => {
        byCategory[d.category] = (byCategory[d.category] || 0) + 1;
        byFileType[d.fileType] = (byFileType[d.fileType] || 0) + 1;
      });

      stats = {
        totalDocuments: uniqueDocs.length,
        byCategory: Object.entries(byCategory)
          .map(([name, count]) => ({ _id: name, count }))
          .sort((a, b) => b.count - a.count),
        byFileType: Object.entries(byFileType).map(([name, count]) => ({
          _id: name,
          count,
        })),
        totalWords: uniqueDocs.reduce((sum, d) => sum + d.wordCount, 0),
      };
      setInCache(cache.stats, language, stats);
    }
    res.json(stats);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// GET /api/documents/languages
router.get("/languages", async (req, res) => {
  try {
    let languages = getFromCache(cache, null);
    if (!languages) {
      const entries = await fs.readdir(DATA_BASE_PATH, { withFileTypes: true });
      languages = entries
        .filter((e) => e.isDirectory() && !e.name.startsWith("."))
        .map((e) => e.name)
        .sort();
      setInCache(cache, null, languages);
    }
    res.json({ languages });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Helper: build folder/file tree
async function buildTree(dirPath, basePath, maxDepth = 8, currentDepth = 0) {
  if (currentDepth >= maxDepth) return [];
  const entries = await fs.readdir(dirPath, { withFileTypes: true });
  const children = [];

  for (const entry of entries) {
    if (
      entry.name.startsWith(".") ||
      ["node_modules", "venv", "__pycache__"].includes(entry.name)
    )
      continue;
    const fullPath = path.join(dirPath, entry.name);
    const relativePath = path.relative(basePath, fullPath).replace(/\\/g, "/");

    if (entry.isDirectory()) {
      const sub = await buildTree(
        fullPath,
        basePath,
        maxDepth,
        currentDepth + 1,
      );
      if (sub.length > 0)
        children.push({
          type: "folder",
          name: entry.name,
          path: relativePath,
          children: sub,
        });
    } else {
      const ext = path.extname(entry.name).toLowerCase();
      const allowed = [
        ".py",
        ".md",
        ".txt",
        ".js",
        ".jsx",
        ".ts",
        ".tsx",
        ".html",
        ".css",
        ".go",
        ".java",
        ".c",
        ".cpp",
        ".rs",
        ".php",
        ".rb",
        ".sql",
        ".sh",
        ".bash",
      ];
      if (allowed.includes(ext))
        children.push({
          type: "file",
          name: entry.name,
          ext,
          path: relativePath,
        });
    }
  }
  return children.sort((a, b) =>
    a.type === b.type
      ? a.name.localeCompare(b.name)
      : a.type === "folder"
        ? -1
        : 1,
  );
}

// GET /api/documents/tree
router.get("/tree", async (req, res) => {
  try {
    const { language = "all" } = req.query;
    let tree = getFromCache(cache.trees, language);

    if (!tree) {
      let scanPath =
        language === "all"
          ? DATA_BASE_PATH
          : path.join(DATA_BASE_PATH, language);
      if (
        !(await fs
          .access(scanPath)
          .then(() => true)
          .catch(() => false))
      )
        return res.json({ tree: [] });

      const rawTree = await buildTree(scanPath, scanPath);
      tree = [];
      for (const node of rawTree) {
        if (node.type === "folder" && node.name.toLowerCase() === "data")
          tree.push(...(node.children || []));
        else tree.push(node);
      }
      tree.sort((a, b) =>
        a.type === b.type
          ? a.name.localeCompare(b.name)
          : a.type === "folder"
            ? -1
            : 1,
      );
      setInCache(cache.trees, language, tree);
    }
    res.json({ tree });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// GET /api/documents/categories
router.get("/categories", async (req, res) => {
  try {
    const { language = "all" } = req.query;
    let categories = getFromCache(cache.documents, language + "_cats");

    if (!categories) {
      let scanPath =
        language === "all"
          ? DATA_BASE_PATH
          : path.join(DATA_BASE_PATH, language);
      let docs = (await fs
        .access(scanPath)
        .then(() => true)
        .catch(() => false))
        ? await scanDirectory(scanPath, scanPath)
        : [];
      categories = [...new Set(docs.map((d) => d.category))].sort();
      setInCache(cache.documents, language + "_cats", categories);
    }
    res.json(categories);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// POST /api/documents/run-python - execute Python code from examples safely (timeout)
router.post("/run-python", async (req, res) => {
  try {
    const { code, stdin = "" } = req.body || {};
    if (!code || typeof code !== "string") {
      return res
        .status(400)
        .json({ error: "Request body must include a Python code string." });
    }

    const result = await executePythonCode(
      code,
      typeof stdin === "string" ? stdin : "",
    );
    return res.json(result);
  } catch (err) {
    return res
      .status(500)
      .json({ stdout: "", stderr: err.message, error: err.message });
  }
});

// GET /api/documents/* - single document
router.get("/*path", async (req, res) => {
  try {
    let requestedPath = Array.isArray(req.params.path)
      ? req.params.path.join("/")
      : req.params.path;
    const { language = "all" } = req.query;
    let fullPath = path.join(
      language === "all" ? DATA_BASE_PATH : path.join(DATA_BASE_PATH, language),
      requestedPath,
    );

    if (!path.resolve(fullPath).startsWith(path.resolve(DATA_BASE_PATH)))
      return res.status(403).json({ error: "Access denied" });

    const fileInfo = await getFileInfo(fullPath, requestedPath, {
      readContent: true,
    });
    if (!fileInfo) return res.status(404).json({ error: "Document not found" });

    if (fileInfo.fileType === "markdown") {
      const scanPath =
        language === "all"
          ? DATA_BASE_PATH
          : path.join(DATA_BASE_PATH, language);
      const allDocs = await scanDirectory(scanPath, scanPath);
      const codeDocs = allDocs.filter((d) => d.fileType !== "markdown");
      const k = fileInfo.title
        .toLowerCase()
        .replace(/[^a-z0-9]/g, "")
        .replace(/s$/, "");
      fileInfo.relatedCode = codeDocs.filter((c) => {
        const ck = c.title
          .toLowerCase()
          .replace(/[^a-z0-9]/g, "")
          .replace(/s$/, "");
        return ck.includes(k) || k.includes(ck);
      });
      fileInfo.isTopic = true;
    }
    res.json(fileInfo);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
