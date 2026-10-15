const courseProgress = require("../services/courseProgressService");
const User = require("../models/User");

function statusFromError(error) {
  return error.statusCode || 400;
}

async function getProgress(req, res) {
  try {
    const progress = await courseProgress.getProgress(
      req.userId,
      req.params.courseId,
    );
    res.json({ progress });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function listMyProgress(req, res) {
  try {
    const courses = await courseProgress.listProgressForUser(req.userId, {
      includePrivate: true,
    });
    res.json({ courses });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function listPublicProgress(req, res) {
  try {
    const username = String(req.params.username || "")
      .trim()
      .toLowerCase();
    const user = await User.findOne({ username, isActive: { $ne: false } }).select("_id");
    if (!user) {
      return res.status(404).json({ error: "User not found" });
    }
    const courses = await courseProgress.listProgressForUser(user._id, {
      includePrivate: false,
    });
    res.json({ courses });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function setLastLesson(req, res) {
  try {
    const { lessonId } = req.body;
    if (!lessonId) return res.status(400).json({ error: "lessonId is required" });

    const progress = await courseProgress.setLastLesson(
      req.userId,
      req.params.courseId,
      lessonId,
    );
    res.json({ progress });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function completeLesson(req, res) {
  try {
    const { lesson } = req.body;
    if (!lesson?.id && !lesson?.lessonId) {
      return res.status(400).json({ error: "lesson metadata is required" });
    }

    const progress = await courseProgress.completeLesson(
      req.userId,
      req.params.courseId,
      lesson,
    );
    res.json({ progress });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function saveCode(req, res) {
  try {
    const { lessonId, code } = req.body;
    if (!lessonId) return res.status(400).json({ error: "lessonId is required" });

    const progress = await courseProgress.saveCode(
      req.userId,
      req.params.courseId,
      lessonId,
      code || "",
    );
    res.json({ progress });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function saveNote(req, res) {
  try {
    const { lessonId, note } = req.body;
    if (!lessonId) return res.status(400).json({ error: "lessonId is required" });

    const progress = await courseProgress.saveNote(
      req.userId,
      req.params.courseId,
      lessonId,
      note || "",
    );
    res.json({ progress });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function toggleBookmark(req, res) {
  try {
    const { lessonId } = req.body;
    if (!lessonId) return res.status(400).json({ error: "lessonId is required" });

    const progress = await courseProgress.toggleBookmark(
      req.userId,
      req.params.courseId,
      lessonId,
    );
    res.json({ progress });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function addTime(req, res) {
  try {
    const { minutes } = req.body;
    const progress = await courseProgress.addTime(
      req.userId,
      req.params.courseId,
      minutes,
    );
    res.json({ progress });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function upsertEngagement(req, res) {
  try {
    const {
      lessonId,
      read,
      confidence,
      quizAttempts,
      challengeAttempts,
      challengeLastResult,
      lastTab,
      incrementChallengeAttempts,
    } = req.body;
    if (!lessonId) {
      return res.status(400).json({ error: "lessonId is required" });
    }
    const progress = await courseProgress.upsertLessonEngagement(
      req.userId,
      req.params.courseId,
      {
        lessonId,
        read,
        confidence,
        quizAttempts,
        challengeAttempts,
        challengeLastResult,
        lastTab,
        incrementChallengeAttempts,
      },
    );
    res.json({ progress });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function getDashboard(req, res) {
  try {
    const dashboard = await courseProgress.getLearnDashboard(req.userId);
    res.json(dashboard);
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function getPublicDashboard(req, res) {
  try {
    const username = String(req.params.username || "")
      .trim()
      .toLowerCase();
    const user = await User.findOne({ username, isActive: { $ne: false } }).select("_id");
    if (!user) {
      return res.status(404).json({ error: "User not found" });
    }
    const dashboard = await courseProgress.getLearnDashboard(user._id);
    // Public: course list + XP/time/streak aggregates + per-day lesson counts;
    // no engagement counts.
    const overview = { ...(dashboard.overview || {}) };
    delete overview.lessonsRead;
    delete overview.quizAnswered;
    delete overview.quizCorrect;
    delete overview.challengeFails;
    res.json({
      overview,
      courses: (dashboard.courses || []).map((row) => {
        const next = { ...row };
        delete next.lessonsRead;
        delete next.quizAnswered;
        delete next.quizCorrect;
        delete next.challengeFails;
        return next;
      }),
      activity: dashboard.activity || [],
    });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function mergeLocal(req, res) {
  try {
    const { courses } = req.body;
    if (!courses || typeof courses !== "object") {
      return res.status(400).json({ error: "courses object is required" });
    }
    const results = await courseProgress.mergeManyLocalProgress(
      req.userId,
      courses,
    );
    res.json({ results });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

module.exports = {
  getProgress,
  listMyProgress,
  listPublicProgress,
  setLastLesson,
  completeLesson,
  saveCode,
  saveNote,
  toggleBookmark,
  addTime,
  upsertEngagement,
  getDashboard,
  getPublicDashboard,
  mergeLocal,
};
