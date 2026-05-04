const progressService = require("../services/progressService");

/**
 * GET /api/progress/:userId/:language - Get progress for user language
 */
async function getLanguageProgress(req, res) {
  try {
    const { userId, language } = req.params;

    const progress = await progressService.getUserLanguageProgress(
      userId,
      language
    );

    res.json({ progress });
  } catch (error) {
    console.error("Get progress error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

/**
 * GET /api/progress/:userId - Get all progress for user
 */
async function getAllProgress(req, res) {
  try {
    const { userId } = req.params;

    const progressList = await progressService.getUserAllProgress(userId);

    res.json({ progress: progressList });
  } catch (error) {
    console.error("Get all progress error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

/**
 * POST /api/progress/mark-module - Mark module as completed
 */
async function markModuleComplete(req, res) {
  try {
    const { userId, language, moduleName } = req.body;

    if (!userId || !language || !moduleName) {
      return res.status(400).json({
        error: "userId, language, and moduleName are required",
      });
    }

    const progress = await progressService.markModuleComplete(
      userId,
      language,
      moduleName
    );

    res.json({
      message: "Module marked as completed",
      progress,
    });
  } catch (error) {
    console.error("Mark module error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

/**
 * POST /api/progress/mark-document - Mark document as completed
 */
async function markDocumentComplete(req, res) {
  try {
    const { userId, language, documentInfo } = req.body;

    if (!userId || !language || !documentInfo) {
      return res.status(400).json({
        error: "userId, language, and documentInfo are required",
      });
    }

    const progress = await progressService.markDocumentComplete(
      userId,
      language,
      documentInfo
    );

    res.json({
      message: "Document marked as completed",
      progress,
    });
  } catch (error) {
    console.error("Mark document error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

/**
 * POST /api/progress/bookmark - Add or remove bookmark
 */
async function toggleBookmark(req, res) {
  try {
    const { userId, language, bookmarkInfo, remove } = req.body;

    if (!userId || !language || !bookmarkInfo) {
      return res.status(400).json({
        error: "userId, language, and bookmarkInfo are required",
      });
    }

    const progress = await progressService.toggleBookmark(
      userId,
      language,
      bookmarkInfo,
      remove
    );

    res.json({
      message: remove ? "Bookmark removed" : "Bookmark added",
      progress,
    });
  } catch (error) {
    console.error("Bookmark error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

/**
 * POST /api/progress/add-time - Add time spent learning
 */
async function addTimeSpent(req, res) {
  try {
    const { userId, language, minutes } = req.body;

    if (!userId || !language || minutes === undefined) {
      return res.status(400).json({
        error: "userId, language, and minutes are required",
      });
    }

    const progress = await progressService.addTimeSpent(
      userId,
      language,
      minutes
    );

    res.json({
      message: "Time added successfully",
      progress,
    });
  } catch (error) {
    console.error("Add time error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

/**
 * POST /api/progress/mark-language-complete - Mark entire language as completed
 */
async function markLanguageComplete(req, res) {
  try {
    const { userId, language } = req.body;

    if (!userId || !language) {
      return res
        .status(400)
        .json({ error: "userId and language are required" });
    }

    const progress = await progressService.markLanguageComplete(
      userId,
      language
    );

    res.json({
      message: "Language marked as completed",
      progress,
    });
  } catch (error) {
    console.error("Mark language complete error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

/**
 * GET /api/progress/dashboard/:userId - Get dashboard stats
 */
async function getDashboardStats(req, res) {
  try {
    const { userId } = req.params;

    const stats = await progressService.getUserDashboardStats(userId);

    res.json(stats);
  } catch (error) {
    console.error("Dashboard stats error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

module.exports = {
  getLanguageProgress,
  getAllProgress,
  markModuleComplete,
  markDocumentComplete,
  toggleBookmark,
  addTimeSpent,
  markLanguageComplete,
  getDashboardStats,
};
