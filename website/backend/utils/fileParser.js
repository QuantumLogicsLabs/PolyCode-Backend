const fs = require('fs');
const path = require('path');
const { marked } = require('marked');

/**
 * Parse the monolithic Java.txt dump file OR a real folder.
 * Since the user placed everything in one .txt export, we parse that.
 */

const SUPPORTED_EXTENSIONS = ['.md', '.py', '.txt'];

/**
 * Extract category from file path
 */
function getCategory(filePath) {
  const parts = filePath.replace(/\\/g, '/').split('/').filter(Boolean);
  if (parts.length === 0) return 'root';
  if (parts[0] === 'documentation') return parts[1] || 'documentation';
  if (parts[0] === 'src') return parts[1] || 'src';
  return parts[0] || 'root';
}

function getSubcategory(filePath) {
  const parts = filePath.replace(/\\/g, '/').split('/').filter(Boolean);
  if (parts.length >= 3) return parts[2];
  return '';
}

function getFileType(filePath) {
  const ext = path.extname(filePath).toLowerCase();
  if (ext === '.md') return 'md';
  if (ext === '.py') return 'py';
  if (ext === '.txt') return 'txt';
  return 'other';
}

function extractTitle(content, filePath) {
  // Try first H1
  const h1Match = content.match(/^#\s+(.+)/m);
  if (h1Match) return h1Match[1].trim();
  // Try filename
  const base = path.basename(filePath, path.extname(filePath));
  return base.replace(/[_-]/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function extractCodeBlocks(content, fileType) {
  const blocks = [];
  if (fileType === 'py') {
    blocks.push({ language: 'java', code: content });
    return blocks;
  }
  // Extract fenced code blocks from markdown
  const fenceRegex = /```(\w*)\n([\s\S]*?)```/g;
  let match;
  while ((match = fenceRegex.exec(content)) !== null) {
    blocks.push({
      language: match[1] || 'java',
      code: match[2].trim()
    });
  }
  return blocks;
}

function extractTags(content, filePath) {
  const tags = new Set();
  const pathParts = filePath.replace(/\\/g, '/').split('/');
  pathParts.forEach(p => { if (p && !p.includes('.')) tags.add(p.toLowerCase()); });

  // Common Java keywords
  const keywords = ['java', 'algorithm', 'data structure', 'function', 'class',
    'loop', 'recursion', 'sorting', 'searching', 'graph', 'tree', 'dynamic programming',
    'api', 'flask', 'fastapi', 'machine learning', 'numpy', 'pandas'];
  keywords.forEach(kw => {
    if (content.toLowerCase().includes(kw)) tags.add(kw);
  });
  return [...tags].slice(0, 10);
}

/**
 * Convert markdown content to HTML with syntax highlighting classes
 */
function markdownToHtml(content, fileType) {
  if (fileType === 'py') {
    // Wrap entire Java file in a code block
    return `<pre class="code-block language-java"><code>${escapeHtml(content)}</code></pre>`;
  }
  try {
    return marked(content);
  } catch {
    return `<pre>${escapeHtml(content)}</pre>`;
  }
}

function escapeHtml(str) {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

/**
 * Parse the single exported Java.txt file into an array of document objects
 */
function parseJavaTxtExport(txtFilePath) {
  const raw = fs.readFileSync(txtFilePath, 'utf-8');
  const documents = [];

  // Split on the FILE: header markers
  const fileRegex = /^-{80}\nFILE: (.+?)\n-{80}\n([\s\S]*?)(?=(?:\n-{80}\n(?:FILE:|={80})))/gm;

  let match;
  while ((match = fileRegex.exec(raw)) !== null) {
    const filePath = match[1].trim();
    const content = match[2].trim();

    if (!content) continue;

    const ext = path.extname(filePath).toLowerCase();
    if (!['.md', '.py', '.txt'].includes(ext)) continue;

    const fileType = getFileType(filePath);
    const title = extractTitle(content, filePath);
    const category = getCategory(filePath);
    const subcategory = getSubcategory(filePath);
    const codeBlocks = extractCodeBlocks(content, fileType);
    const tags = extractTags(content, filePath);
    const htmlContent = markdownToHtml(content, fileType);
    const wordCount = content.split(/\s+/).length;

    documents.push({
      path: filePath,
      title,
      content,
      htmlContent,
      category,
      subcategory,
      fileType,
      codeBlocks,
      wordCount,
      tags,
      lastModified: new Date(),
    });
  }

  return documents;
}

/**
 * Parse a real directory (if Java folder is present)
 */
function parseDirectory(dirPath, baseDir = null) {
  if (!baseDir) baseDir = dirPath;
  const documents = [];

  const entries = fs.readdirSync(dirPath, { withFileTypes: true });
  for (const entry of entries) {
    const fullPath = path.join(dirPath, entry.name);
    if (entry.isDirectory()) {
      if (entry.name.startsWith('.')) continue;
      documents.push(...parseDirectory(fullPath, baseDir));
    } else if (entry.isFile()) {
      const ext = path.extname(entry.name).toLowerCase();
      if (!SUPPORTED_EXTENSIONS.includes(ext)) continue;

      const content = fs.readFileSync(fullPath, 'utf-8');
      const relativePath = '/' + path.relative(baseDir, fullPath).replace(/\\/g, '/');
      const fileType = getFileType(fullPath);

      documents.push({
        path: relativePath,
        title: extractTitle(content, relativePath),
        content,
        htmlContent: markdownToHtml(content, fileType),
        category: getCategory(relativePath),
        subcategory: getSubcategory(relativePath),
        fileType,
        codeBlocks: extractCodeBlocks(content, fileType),
        wordCount: content.split(/\s+/).length,
        tags: extractTags(content, relativePath),
        lastModified: fs.statSync(fullPath).mtime,
      });
    }
  }
  return documents;
}

module.exports = { parseJavaTxtExport, parseDirectory, markdownToHtml };
