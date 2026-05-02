const UserProgress = require("../models/UserProgress");
const User = require("../models/User");

/**
 * Get or create user progress for a language
 * @param {string} userId - User ID
 * @param {string} language - Language name
 * @returns {Promise<Object>} UserProgress object
 */
async function getOrCreateProgress(userId, language) {
  try {
    let progress = await UserProgress.findOne({ userId, language });

    if (!progress) {
      progress = new UserProgress({
        userId,
        language,
        status: "not-started",
      });
      await progress.save();
    }

    return progress;
  } catch (error) {
    throw error;
  }
}

/**
 * Mark a module as completed
 * @param {string} userId - User ID
 * @param {string} language - Language name
 * @param {string} moduleName - Module name (e.g., "1-Getting Started")
 * @returns {Promise<Object>} Updated UserProgress object
 */
async function markModuleComplete(userId, language, moduleName) {
  try {
    const progress = await getOrCreateProgress(userId, language);

    if (!progress.completedModules.includes(moduleName)) {
      progress.completedModules.push(moduleName);
      progress.status = "in-progress";
      progress.updatedAt = Date.now();
      await progress.save();
    }

    return progress;
  } catch (error) {
    throw error;
  }
}

/**
 * Mark a document as completed
 * @param {string} userId - User ID
 * @param {string} language - Language name
 * @param {Object} documentInfo - Document info { path, title }
 * @returns {Promise<Object>} Updated UserProgress object
 */
async function markDocumentComplete(userId, language, documentInfo) {
  try {
    let progress = await getOrCreateProgress(userId, language);

    // Check if document is already completed
    const existingDoc = progress.completedDocuments.find(
      (doc) => doc.path === documentInfo.path
    );

    if (!existingDoc) {
      progress.completedDocuments.push({
        path: documentInfo.path,
        title: documentInfo.title,
        completedAt: new Date(),
      });
      progress.updatedAt = Date.now();
    }

    // Update progress percentage
    progress = await updateProgressPercentage(progress);
    progress.lastAccessedModule = documentInfo.path;
    progress.lastAccessedAt = new Date();
    progress.status = "in-progress";

    await progress.save();
    return progress;
  } catch (error) {
    throw error;
  }
}

/**
 * Add/remove bookmark
 * @param {string} userId - User ID
 * @param {string} language - Language name
 * @param {Object} bookmarkInfo - Bookmark info { path, title, note }
 * @param {boolean} remove - Whether to remove bookmark
 * @returns {Promise<Object>} Updated UserProgress object
 */
async function toggleBookmark(userId, language, bookmarkInfo, remove = false) {
  try {
    const progress = await getOrCreateProgress(userId, language);

    if (remove) {
      progress.bookmarks = progress.bookmarks.filter(
        (b) => b.path !== bookmarkInfo.path
      );
    } else {
      const existingBookmark = progress.bookmarks.find(
        (b) => b.path === bookmarkInfo.path
      );

      if (!existingBookmark) {
        progress.bookmarks.push({
          path: bookmarkInfo.path,
          title: bookmarkInfo.title,
          note: bookmarkInfo.note || "",
          addedAt: new Date(),
        });
      }
    }

    progress.updatedAt = Date.now();
    await progress.save();
    return progress;
  } catch (error) {
    throw error;
  }
}

/**
 * Update time spent learning
 * @param {string} userId - User ID
 * @param {string} language - Language name
 * @param {number} minutes - Minutes to add
 * @returns {Promise<Object>} Updated UserProgress object
 */
async function addTimeSpent(userId, language, minutes) {
  try {
    const progress = await getOrCreateProgress(userId, language);

    progress.totalMinutesSpent += minutes;
    progress.lastAccessedAt = new Date();

    // Update streak
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (progress.lastStreakDate) {
      const lastDate = new Date(progress.lastStreakDate);
      lastDate.setHours(0, 0, 0, 0);

      const daysDiff = Math.floor((today - lastDate) / (1000 * 60 * 60 * 24));

      if (daysDiff === 1) {
        // Consecutive day
        progress.currentStreak += 1;
      } else if (daysDiff > 1) {
        // Streak broken
        progress.currentStreak = 1;
      }
    } else {
      progress.currentStreak = 1;
    }

    progress.lastStreakDate = new Date();
    progress.updatedAt = Date.now();

    await progress.save();
    return progress;
  } catch (error) {
    throw error;
  }
}

/**
 * Get all progress for a user
 * @param {string} userId - User ID
 * @returns {Promise<Array>} Array of UserProgress objects
 */
async function getUserAllProgress(userId) {
  try {
    const progressList = await UserProgress.find({ userId });
    return progressList;
  } catch (error) {
    throw error;
  }
}

/**
 * Get progress for specific language
 * @param {string} userId - User ID
 * @param {string} language - Language name
 * @returns {Promise<Object>} UserProgress object
 */
async function getUserLanguageProgress(userId, language) {
  try {
    const progress = await getOrCreateProgress(userId, language);
    return progress;
  } catch (error) {
    throw error;
  }
}

/**
 * Calculate and update progress percentage
 * @param {Object} progress - UserProgress object
 * @returns {Promise<Object>} Updated progress object
 */
async function updateProgressPercentage(progress) {
  try {
    // This is a simplified calculation
    // In production, you'd need to know total modules/documents for each language
    const totalModules = 13; // Placeholder: total number of modules across all languages

    const completedPercentage = (
      (progress.completedModules.length / totalModules) *
      100
    ).toFixed(2);

    progress.progressPercentage = Math.min(100, completedPercentage);
    return progress;
  } catch (error) {
    throw error;
  }
}

/**
 * Mark language as completed
 * @param {string} userId - User ID
 * @param {string} language - Language name
 * @returns {Promise<Object>} Updated UserProgress object
 */
async function markLanguageComplete(userId, language) {
  try {
    const progress = await getOrCreateProgress(userId, language);
    progress.status = "completed";
    progress.progressPercentage = 100;
    progress.updatedAt = Date.now();
    await progress.save();
    return progress;
  } catch (error) {
    throw error;
  }
}

/**
 * Get user dashboard stats
 * @param {string} userId - User ID
 * @returns {Promise<Object>} Dashboard stats
 */
async function getUserDashboardStats(userId) {
  try {
    const user = await User.findById(userId);
    const allProgress = await UserProgress.find({ userId });

    const stats = {
      user: user ? user.toJSON() : null,
      totalLanguagesStarted: allProgress.length,
      languagesCompleted: allProgress.filter(
        (p) => p.status === "completed"
      ).length,
      languagesInProgress: allProgress.filter(
        (p) => p.status === "in-progress"
      ).length,
      totalMinutesSpent: allProgress.reduce(
        (sum, p) => sum + p.totalMinutesSpent,
        0
      ),
      totalDocumentsCompleted: allProgress.reduce(
        (sum, p) => sum + p.completedDocuments.length,
        0
      ),
      currentStreak: Math.max(...allProgress.map((p) => p.currentStreak), 0),
      languages: allProgress.map((p) => ({
        language: p.language,
        status: p.status,
        progressPercentage: p.progressPercentage,
        completedModules: p.completedModules.length,
        completedDocuments: p.completedDocuments.length,
        timeSpent: p.totalMinutesSpent,
      })),
    };

    return stats;
  } catch (error) {
    throw error;
  }
}

module.exports = {
  getOrCreateProgress,
  markModuleComplete,
  markDocumentComplete,
  toggleBookmark,
  addTimeSpent,
  getUserAllProgress,
  getUserLanguageProgress,
  updateProgressPercentage,
  markLanguageComplete,
  getUserDashboardStats,
};
