const express = require('express');
const router = express.Router();
const fs = require('fs').promises;
const path = require('path');

// Path to your Python folders
const CPP_BASE_PATH = path.join(__dirname, '../../../data');
const COMBINED_PATH = path.join(__dirname, '../../../data');

// Helper function to get file info
async function getFileInfo(filePath, relativePath) {
  try {
    const stats = await fs.stat(filePath);
    const content = await fs.readFile(filePath, 'utf8');
    
    // Extract basic info from file content
    const lines = content.split('\n').length;
    const firstLine = content.split('\n')[0].replace(/[#"']/g, '').trim();
    
    // Determine file type
    const ext = path.extname(filePath).toLowerCase();
    let fileType = 'other';
    if (['.cpp', '.h', '.hpp', '.c'].includes(ext)) fileType = 'cpp';
    else if (ext === '.md') fileType = 'markdown';
    else if (ext === '.txt') fileType = 'text';
    else if (ext === '.js') fileType = 'javascript';
    else if (ext === '.html') fileType = 'html';
    else if (ext === '.css') fileType = 'css';
    else if (ext === '.py') fileType = 'python';
    else if (['.c', '.cpp', '.java'].includes(ext)) fileType = 'code';
    
    // Extract category from path - top level folder after data/
    const normalizedPath = relativePath.replace(/\\/g, '/');
    const pathParts = normalizedPath.split('/');
    let category = pathParts.length > 1 ? pathParts[0] : 'general';
    
    return {
      title: path.basename(filePath, ext),
      path: relativePath,
      category: category,
      fileType: fileType,
      size: stats.size,
      lines: lines,
      description: firstLine || 'No description available',
      content: content,
      createdAt: stats.birthtime,
      modifiedAt: stats.mtime,
      wordCount: content.split(/\s+/).length
    };
  } catch (error) {
    return null;
  }
}

// Helper function to copy directory
async function copyDirectory(src, dest) {
  try {
    await fs.mkdir(dest, { recursive: true });
    const entries = await fs.readdir(src, { withFileTypes: true });
    
    for (const entry of entries) {
      const srcPath = path.join(src, entry.name);
      const destPath = path.join(dest, entry.name);
      
      if (entry.isDirectory()) {
        await copyDirectory(srcPath, destPath);
      } else {
        await fs.copyFile(srcPath, destPath);
      }
    }
  } catch (error) {
    console.error(`Error copying directory ${src} to ${dest}:`, error.message);
  }
}

// Function to check data folder structure
async function checkDataStructure() {
  try {
    console.log('Checking data folder structure...');
    
    // Check if data folder exists
    if (await fs.access(CPP_BASE_PATH).then(() => true).catch(() => false)) {
      console.log('✓ Data folder found at:', CPP_BASE_PATH);
    } else {
      console.log('✗ Data folder not found at:', CPP_BASE_PATH);
    }
  } catch (error) {
    console.error('Error checking data structure:', error.message);
  }
}

// Helper function to recursively scan directories
async function scanDirectory(dirPath, basePath = CPP_BASE_PATH, maxDepth = 3, currentDepth = 0) {
  const items = [];
  
  if (currentDepth >= maxDepth) return items;
  
  try {
    const entries = await fs.readdir(dirPath, { withFileTypes: true });
    
    for (const entry of entries) {
      const fullPath = path.join(dirPath, entry.name);
      const relativePath = path.relative(basePath, fullPath);
      
      // Skip hidden files and directories
      if (entry.name.startsWith('.')) continue;
      
      // Skip node_modules and other common exclude directories
      if (entry.name === 'node_modules' || entry.name === 'venv' || entry.name === '__pycache__') continue;
      
      if (entry.isDirectory()) {
        // Recursively scan subdirectories
        const subItems = await scanDirectory(fullPath, basePath, maxDepth, currentDepth + 1);
        items.push(...subItems);
      } else if (entry.isFile()) {
        // Only include certain file types
        const ext = path.extname(entry.name).toLowerCase();
        if (['.cpp', '.h', '.hpp', '.c', '.md', '.txt', '.js', '.html', '.css'].includes(ext)) {
          const fileInfo = await getFileInfo(fullPath, relativePath);
          if (fileInfo) items.push(fileInfo);
        }
      }
    }
  } catch (error) {
    console.error(`Error scanning directory ${dirPath}:`, error.message);
  }
  
  return items;
}

// GET /api/documents - list all with optional filters
router.get('/', async (req, res) => {
  try {
    const { category, fileType, search, page = 1, limit = 20 } = req.query;
    
    // First check data folder structure
    await checkDataStructure();
    
    // Scan data directory
    let allDocuments = [];
    
    // Scan data directory
    if (await fs.access(CPP_BASE_PATH).then(() => true).catch(() => false)) {
      const dataDocs = await scanDirectory(CPP_BASE_PATH);
      allDocuments.push(...dataDocs);
    }
    
    // Remove duplicates (same file from multiple sources)
    const uniqueDocs = allDocuments.reduce((acc, doc) => {
      const key = `${doc.path}-${doc.title}`;
      if (!acc.has(key)) {
        acc.set(key, doc);
      }
      return acc;
    }, new Map());
    
    const finalDocs = Array.from(uniqueDocs.values());
    
    // Group documents into Topics (.md) and Code (.py)
    const topicDocs = finalDocs.filter(doc => doc.fileType === 'markdown');
    const codeDocs  = finalDocs.filter(doc => doc.fileType !== 'markdown');

    // Attach code docs to topic docs
    const groupedDocs = topicDocs.map(topic => {
      const topicKeywords = topic.title.toLowerCase().replace(/[^a-z0-9]/g, '');
      const topicKeywordsNormalized = topicKeywords.endsWith('s') ? topicKeywords.slice(0, -1) : topicKeywords;

      const relatedCode = codeDocs.filter(code => {
        const codeKey = code.title.toLowerCase().replace(/[^a-z0-9]/g, '');
        const codeKeyNormalized = codeKey.endsWith('s') ? codeKey.slice(0, -1) : codeKey;

        return codeKeyNormalized.includes(topicKeywordsNormalized) || 
               topicKeywordsNormalized.includes(codeKeyNormalized);
      });

      return {
        ...topic,
        isTopic: true,
        relatedCode
      };
    });

    // Also include code docs that didn't match any topic as standalone items
    const matchedCodePaths = new Set();
    groupedDocs.forEach(topic => {
      topic.relatedCode.forEach(c => matchedCodePaths.add(c.path));
    });
    
    const standaloneCode = codeDocs.filter(c => !matchedCodePaths.has(c.path)).map(c => ({
      ...c,
      relatedCode: [] // empty for standalone
    }));

    const unifiedDocs = [...groupedDocs, ...standaloneCode];

    // Apply filters
    let filteredDocs = req.query.ungrouped === 'true' ? finalDocs : unifiedDocs;
    
    if (category && category !== 'all') {
      const categoryLower = category.toLowerCase();
      filteredDocs = filteredDocs.filter(doc => doc.category.toLowerCase() === categoryLower);
    }
    
    if (fileType && fileType !== 'all') {
      let targetType = fileType;
      if (fileType === 'md') targetType = 'markdown';
      if (fileType === 'cpp') targetType = 'cpp';

      filteredDocs = filteredDocs.filter(doc => {
        if (doc.fileType === targetType) return true;
        if (doc.relatedCode && doc.relatedCode.some(c => c.fileType === targetType)) return true;
        return false;
      });
    }
    
    if (search) {
      const searchLower = search.toLowerCase();
      filteredDocs = filteredDocs.filter(doc => {
        const matchesMain = doc.title.toLowerCase().includes(searchLower) ||
                            doc.description.toLowerCase().includes(searchLower) ||
                            doc.content.toLowerCase().includes(searchLower);
        if (matchesMain) return true;
        // Search inside related code
        if (doc.relatedCode) {
          return doc.relatedCode.some(c => 
            c.title.toLowerCase().includes(searchLower) ||
            c.description.toLowerCase().includes(searchLower) ||
            c.content.toLowerCase().includes(searchLower)
          );
        }
        return false;
      });
    }
    
    // Sort by priority (topics first), then category then title
    filteredDocs.sort((a, b) => {
      // First priority: Topics (Markdown guides)
      if (a.isTopic && !b.isTopic) return -1;
      if (!a.isTopic && b.isTopic) return 1;
      
      // Second priority: Category
      if (a.category !== b.category) {
        return a.category.localeCompare(b.category);
      }
      
      // Third priority: Title
      return a.title.localeCompare(b.title);
    });
    
    // Pagination
    const skip = (parseInt(page) - 1) * parseInt(limit);
    const paginatedDocs = filteredDocs.slice(skip, skip + parseInt(limit));
    
    // Remove content for list view (lightweight)
    const listDocs = paginatedDocs.map(doc => {
      const { content, ...docWithoutContent } = doc;
      if (docWithoutContent.relatedCode) {
        docWithoutContent.relatedCode = docWithoutContent.relatedCode.map(c => {
          const { content: codeContent, ...cWithoutContent } = c;
          return cWithoutContent;
        });
      }
      return docWithoutContent;
    });
    
    res.json({ 
      documents: listDocs, 
      total: filteredDocs.length, 
      page: parseInt(page), 
      pages: Math.ceil(filteredDocs.length / limit) 
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// GET /api/documents/stats - overall statistics
router.get('/stats', async (req, res) => {
  try {
    // Check data folder structure
    await checkDataStructure();
    
    let allDocuments = [];
    
    // Scan data directory
    if (await fs.access(CPP_BASE_PATH).then(() => true).catch(() => false)) {
      const dataDocs = await scanDirectory(CPP_BASE_PATH);
      allDocuments.push(...dataDocs);
    }
    
    // Remove duplicates
    const uniqueDocs = allDocuments.reduce((acc, doc) => {
      const key = `${doc.path}-${doc.title}`;
      if (!acc.has(key)) {
        acc.set(key, doc);
      }
      return acc;
    }, new Map());
    
    const finalDocs = Array.from(uniqueDocs.values());
    
    // Calculate statistics
    const total = finalDocs.length;
    
    const byCategory = {};
    finalDocs.forEach(doc => {
      byCategory[doc.category] = (byCategory[doc.category] || 0) + 1;
    });
    
    const byFileType = {};
    finalDocs.forEach(doc => {
      byFileType[doc.fileType] = (byFileType[doc.fileType] || 0) + 1;
    });
    
    const totalWords = finalDocs.reduce((sum, doc) => sum + doc.wordCount, 0);
    
    // Convert to expected format
    const categoryStats = Object.entries(byCategory).map(([name, count]) => ({ _id: name, count }));
    const fileTypeStats = Object.entries(byFileType).map(([name, count]) => ({ _id: name, count }));
    
    res.json({
      totalDocuments: total,
      byCategory: categoryStats.sort((a, b) => b.count - a.count),
      byFileType: fileTypeStats,
      totalWords: totalWords,
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// GET /api/documents/categories - distinct categories
router.get('/categories', async (req, res) => {
  try {
    // Check data folder structure
    await checkDataStructure();
    
    let allDocuments = [];
    
    // Scan data directory
    if (await fs.access(CPP_BASE_PATH).then(() => true).catch(() => false)) {
      const dataDocs = await scanDirectory(CPP_BASE_PATH);
      allDocuments.push(...dataDocs);
    }
    
    // Remove duplicates
    const uniqueDocs = allDocuments.reduce((acc, doc) => {
      const key = `${doc.path}-${doc.title}`;
      if (!acc.has(key)) {
        acc.set(key, doc);
      }
      return acc;
    }, new Map());
    
    const finalDocs = Array.from(uniqueDocs.values());
    const categories = [...new Set(finalDocs.map(doc => doc.category))];
    res.json(categories.sort());
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// GET /api/documents/* - single document with full content
router.get('/*path', async (req, res) => {
  try {
    // Get the full path from the request
    let requestedPath = req.params.path; // Get everything after /api/documents/
    if (Array.isArray(requestedPath)) {
      requestedPath = requestedPath.join('/');
    }
    
    // Try data folder directly
    let fullPath = path.join(CPP_BASE_PATH, requestedPath);
    
    // Security check - ensure the path is within the data directory
    const resolvedPath = path.resolve(fullPath);
    const resolvedBase = path.resolve(CPP_BASE_PATH);
    
    console.log('Requested Path:', requestedPath);
    console.log('Resolved Path:', resolvedPath);
    
    if (!resolvedPath.startsWith(resolvedBase)) {
      console.warn('Access denied for path:', resolvedPath);
      return res.status(403).json({ error: 'Access denied' });
    }
    
    // On Windows, paths might come in with forward slashes but the fs needs backslashes
    // or vice versa. path.join/resolve usually handle this, but let's be explicit if needed.
    // However, the main issue is likely the file not being found due to extension or exact name.

    
    const fileInfo = await getFileInfo(fullPath, requestedPath);
    if (!fileInfo) {
      return res.status(404).json({ error: 'Document not found' });
    }
    
    // If it's a markdown file, attach related code
    if (fileInfo.fileType === 'markdown') {
      let allDocuments = [];
      if (await fs.access(CPP_BASE_PATH).then(() => true).catch(() => false)) {
        allDocuments = await scanDirectory(CPP_BASE_PATH);
      }
      
      const uniqueDocs = allDocuments.reduce((acc, doc) => {
        const key = `${doc.path}-${doc.title}`;
        if (!acc.has(key)) acc.set(key, doc);
        return acc;
      }, new Map());
      
      const codeDocs = Array.from(uniqueDocs.values()).filter(doc => doc.fileType !== 'markdown');
      const topicKeywords = fileInfo.title.toLowerCase().replace(/[^a-z0-9]/g, '');
      const topicKeywordsNormalized = topicKeywords.endsWith('s') ? topicKeywords.slice(0, -1) : topicKeywords;
      
      const relatedCode = codeDocs.filter(code => {
        const codeKey = code.title.toLowerCase().replace(/[^a-z0-9]/g, '');
        const codeKeyNormalized = codeKey.endsWith('s') ? codeKey.slice(0, -1) : codeKey;

        return codeKeyNormalized.includes(topicKeywordsNormalized) || 
               topicKeywordsNormalized.includes(codeKeyNormalized);
      });
      
      fileInfo.relatedCode = relatedCode;
      fileInfo.isTopic = true;
    } else {
      fileInfo.relatedCode = [];
      fileInfo.isTopic = false;
    }
    
    res.json(fileInfo);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
