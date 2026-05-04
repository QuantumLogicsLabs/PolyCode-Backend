const fs = require("fs").promises;
const path = require("path");
const { DATA_BASE_PATH } = require("../../../config/constants");
const { scanDirectory, buildTree } = require("../../../services/fileService");
const { getFromCache, setInCache, cache } = require("../../../utils/cache");

/**
 * Get all documents with optional filters
 * @param {Object} filters - Filter options (language, category, fileType, search, etc.)
 * @returns {Promise<Object>} Paginated documents with metadata
 */
async function getDocuments(filters = {}) {
  const {
    language = "all",
    category,
    fileType,
    search,
    page = 1,
    limit = 20,
    ungrouped = false,
  } = filters;

  // FIX: ensure limit is always a number
  const limitNum = parseInt(limit) || 20;

  let finalDocs = getFromCache(cache.documents, language);

  if (!finalDocs) {
    let scanPath =
      language === "all" ? DATA_BASE_PATH : path.join(DATA_BASE_PATH, language);
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

  // Separate topics (markdown) and code
  const topicDocs = finalDocs.filter((doc) => doc.fileType === "markdown");
  const codeDocs = finalDocs.filter((doc) => doc.fileType !== "markdown");

  // Group code with related topics
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

  // Find standalone code not related to topics
  const matchedCodePaths = new Set();
  groupedDocs.forEach((topic) =>
    topic.relatedCode.forEach((c) => matchedCodePaths.add(c.path)),
  );
  const standaloneCode = codeDocs
    .filter((c) => !matchedCodePaths.has(c.path))
    .map((c) => ({ ...c, relatedCode: [] }));

  let filteredDocs = ungrouped
    ? finalDocs
    : [...groupedDocs, ...standaloneCode];

  // Apply filters
  if (category && category !== "all") {
    filteredDocs = filteredDocs.filter(
      (doc) => doc.category.toLowerCase() === category.toLowerCase(),
    );
  }

  if (fileType && fileType !== "all") {
    let targetType =
      fileType === "md" ? "markdown" : fileType === "py" ? "python" : fileType;
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

  // Sort documents
  filteredDocs.sort((a, b) =>
    a.isTopic === b.isTopic
      ? a.category === b.category
        ? a.title.localeCompare(b.title)
        : a.category.localeCompare(b.category)
      : a.isTopic
        ? -1
        : 1,
  );

  // Paginate - FIX: use limitNum (already parsed int)
  const skip = (parseInt(page) - 1) * limitNum;
  const paginatedDocs = filteredDocs.slice(skip, skip + limitNum);
  const listDocs = paginatedDocs.map(({ content, relatedCode, ...rest }) => ({
    ...rest,
    relatedCode: relatedCode
      ? relatedCode.map(({ content: cc, ...cr }) => cr)
      : [],
  }));

  return {
    documents: listDocs,
    total: filteredDocs.length,
    page: parseInt(page),
    pages: Math.ceil(filteredDocs.length / limitNum),
  };
}

/**
 * Get document statistics
 * @param {string} language - Language filter (optional)
 * @returns {Promise<Object>} Statistics object
 */
async function getDocumentStats(language = "all") {
  let stats = getFromCache(cache.stats, language);

  if (!stats) {
    let scanPath =
      language === "all" ? DATA_BASE_PATH : path.join(DATA_BASE_PATH, language);
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
  return stats;
}

/**
 * Get available languages (top-level folders in DATA_BASE_PATH)
 * FIX: was using getFromCache(cache, null) which is broken — cache root
 *      is not a Map. Now uses the dedicated cache.languages store.
 * @returns {Promise<string[]>} Array of language folder names
 */
async function getLanguages() {
  // FIX: use cache.languages (the dedicated { data, timestamp } store)
  const cached = getFromCache(cache.languages, null);
  if (cached) return cached;

  try {
    const entries = await fs.readdir(DATA_BASE_PATH, { withFileTypes: true });
    const languages = entries
      .filter((e) => e.isDirectory() && !e.name.startsWith("."))
      .map((e) => e.name)
      .sort();
    setInCache(cache.languages, null, languages);
    return languages;
  } catch (err) {
    console.error("Error reading DATA_BASE_PATH for languages:", err.message);
    return [];
  }
}

/**
 * Get document tree structure
 * @param {string} language - Language folder (optional)
 * @returns {Promise<Object[]>} Tree structure
 */
async function getDocumentTree(language = "all") {
  let tree = getFromCache(cache.trees, language);

  if (!tree) {
    let scanPath =
      language === "all" ? DATA_BASE_PATH : path.join(DATA_BASE_PATH, language);
    if (
      !(await fs
        .access(scanPath)
        .then(() => true)
        .catch(() => false))
    )
      return [];

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
  return tree;
}

/**
 * Get categories for documents
 * @param {string} language - Language folder (optional)
 * @returns {Promise<string[]>} Array of category names
 */
async function getDocumentCategories(language = "all") {
  let categories = getFromCache(cache.documents, language + "_cats");

  if (!categories) {
    let scanPath =
      language === "all" ? DATA_BASE_PATH : path.join(DATA_BASE_PATH, language);
    let docs = (await fs
      .access(scanPath)
      .then(() => true)
      .catch(() => false))
      ? await scanDirectory(scanPath, scanPath)
      : [];
    categories = [...new Set(docs.map((d) => d.category))].sort();
    setInCache(cache.documents, language + "_cats", categories);
  }
  return categories;
}

module.exports = {
  getDocuments,
  getDocumentStats,
  getLanguages,
  getDocumentTree,
  getDocumentCategories,
};
