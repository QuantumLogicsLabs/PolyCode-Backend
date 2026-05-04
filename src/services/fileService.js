const fs = require("fs").promises;
const path = require("path");
const {
  DATA_BASE_PATH,
  ALLOWED_EXTENSIONS,
  FILE_TYPE_MAP,
  IGNORE_DIRS,
} = require("../config/constants");
const { getFromCache, setInCache, cache } = require("../utils/cache");

/**
 * Batch read files with metadata
 * @param {string[]} filePaths - Array of file paths
 * @param {Object} options - Options for reading
 * @returns {Promise<Object[]>} Array of file info objects
 */
async function batchReadFiles(filePaths, options = {}) {
  const { readMetadata = true, readContent = false } = options;
  const batchSize = 10;

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

/**
 * Get file information with optional content reading
 * @param {string} filePath - Full file path
 * @param {string} relativePath - Relative path from data base
 * @param {Object} options - Reading options
 * @returns {Promise<Object>} File info object
 */
async function getFileInfo(filePath, relativePath, options = {}) {
  const { readMetadata = true, readContent = false } = options;

  try {
    const cacheKey = `${relativePath}:${readContent ? "full" : "meta"}`;
    const cached = getFromCache(cache.fileIndex, cacheKey);
    if (cached && !readContent) {
      return cached;
    }

    const stats = await fs.stat(filePath);
    const ext = path.extname(filePath).toLowerCase();
    const fileType = FILE_TYPE_MAP[ext] || "other";

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

    // Optimized metadata reading for large files
    if (stats.size > 100 * 1024) {
      const result = {
        ...baseInfo,
        lines: Math.floor(stats.size / 40),
        excerpt: "Large file - preview not available",
        wordCount: Math.floor(stats.size / 6),
      };
      setInCache(cache.fileIndex, cacheKey, result);
      return result;
    }

    // Read first 1KB for small files
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

/**
 * Scan directory recursively for documents
 * FIX: Removed the artificial directories.slice(0, 5) limit that was
 *      causing most data folder contents to be silently dropped.
 * @param {string} dirPath - Directory path to scan
 * @param {string} basePath - Base path for relative paths
 * @param {number} maxDepth - Maximum recursion depth
 * @param {number} currentDepth - Current recursion depth
 * @returns {Promise<Object[]>} Array of file info objects
 */
async function scanDirectory(
  dirPath,
  basePath = DATA_BASE_PATH,
  maxDepth = 10,
  currentDepth = 0,
) {
  if (currentDepth >= maxDepth) return [];

  try {
    const entries = await fs.readdir(dirPath, { withFileTypes: true });
    const files = [];
    const directories = [];

    for (const entry of entries) {
      if (entry.name.startsWith(".")) continue;
      if (IGNORE_DIRS.includes(entry.name)) continue;

      const fullPath = path.join(dirPath, entry.name);
      const relativePath = path.relative(basePath, fullPath);

      if (entry.isDirectory()) {
        directories.push({ fullPath, relativePath });
      } else if (entry.isFile()) {
        const ext = path.extname(entry.name).toLowerCase();
        if (ALLOWED_EXTENSIONS.includes(ext)) {
          files.push({ fullPath, relativePath });
        }
      }
    }

    // Process files in parallel batches
    const fileResults = await batchReadFiles(
      files.map((f) => f.fullPath),
      { readMetadata: true },
    );

    // FIX: Removed .slice(0, 5) — that limited scanning to only the first 5
    // subdirectories, silently dropping all others and causing "file not found" errors.
    // Process ALL subdirectories with limited concurrency instead.
    const CONCURRENCY = 8;
    const dirResults = [];
    for (let i = 0; i < directories.length; i += CONCURRENCY) {
      const batch = directories.slice(i, i + CONCURRENCY);
      const batchResults = await Promise.all(
        batch.map(async ({ fullPath }) => {
          try {
            return await scanDirectory(
              fullPath,
              basePath,
              maxDepth,
              currentDepth + 1,
            );
          } catch (error) {
            console.error(
              `Error scanning directory ${fullPath}:`,
              error.message,
            );
            return [];
          }
        }),
      );
      dirResults.push(...batchResults);
    }

    return [...fileResults.filter(Boolean), ...dirResults.flat()];
  } catch (error) {
    console.error(`Error scanning directory ${dirPath}:`, error.message);
    return [];
  }
}

/**
 * Build folder/file tree structure
 * @param {string} dirPath - Directory to build tree from
 * @param {string} basePath - Base path for tree
 * @param {number} maxDepth - Maximum tree depth
 * @param {number} currentDepth - Current depth
 * @returns {Promise<Object[]>} Tree structure array
 */
async function buildTree(dirPath, basePath, maxDepth = 8, currentDepth = 0) {
  if (currentDepth >= maxDepth) return [];
  const entries = await fs.readdir(dirPath, { withFileTypes: true });
  const children = [];

  for (const entry of entries) {
    if (entry.name.startsWith(".") || IGNORE_DIRS.includes(entry.name))
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
      if (ALLOWED_EXTENSIONS.includes(ext))
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

module.exports = {
  batchReadFiles,
  getFileInfo,
  scanDirectory,
  buildTree,
};
