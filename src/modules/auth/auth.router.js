const express = require("express");
const router = express.Router();
const userController = require("./controllers/userController");
const progressController = require("./controllers/progressController");
const dailyXpController = require("./controllers/dailyXpController");
const oopsCppProgressController = require("./controllers/oopsCppProgressController");
const courseProgressController = require("./controllers/courseProgressController");
const lessonAnnotationController = require("./controllers/lessonAnnotationController");
const reviewController = require("./controllers/reviewController");
const requireAuth = require("../../middleware/requireAuth");

// ── User Auth Routes ─────────────────────────────────────────────────────────

/** POST /api/auth/register */
router.post("/register", userController.register);

/** POST /api/auth/login */
router.post("/login", userController.login);

/** POST /api/auth/google — Continue with Google (ID token) */
router.post("/google", userController.googleAuth);

/** GET /api/auth/me  — returns current user from Bearer token */
router.get("/me", requireAuth, userController.getMe);

/** GET /api/auth/username/:username */
router.get("/username/:username", userController.getUserByUsername);

/** GET /api/auth/username/:username/follow-status */
router.get(
  "/username/:username/follow-status",
  requireAuth,
  userController.getFollowStatus,
);

/** GET /api/auth/username/:username/followers */
router.get("/username/:username/followers", userController.getFollowers);

/** GET /api/auth/username/:username/following */
router.get("/username/:username/following", userController.getFollowing);

/** GET /api/auth/username/:username/learn/progress — public course progress */
router.get(
  "/username/:username/learn/progress",
  courseProgressController.listPublicProgress,
);

/** GET /api/auth/username/:username/learn/dashboard — public learner dashboard */
router.get(
  "/username/:username/learn/dashboard",
  courseProgressController.getPublicDashboard,
);

/** POST /api/auth/username/:username/follow */
router.post(
  "/username/:username/follow",
  requireAuth,
  userController.followUser,
);

/** DELETE /api/auth/username/:username/follow */
router.delete(
  "/username/:username/follow",
  requireAuth,
  userController.unfollowUser,
);

/** GET /api/auth/user/:id */
router.get("/user/:id", userController.getUserProfile);

/** GET /api/auth/user/:id/avatar — profile picture image */
router.get("/user/:id/avatar", userController.getAvatarImage);

/** PUT /api/auth/user/:id */
router.put("/user/:id", requireAuth, userController.updateProfile);

/** POST /api/auth/user/:id/avatar — cropped image → Google Drive */
router.post(
  "/user/:id/avatar",
  express.json({ limit: "4mb" }),
  requireAuth,
  userController.uploadAvatar,
);

/** POST /api/auth/change-password */
router.post("/change-password", userController.changePasswordHandler);

/** DELETE /api/auth/user/:id */
router.delete("/user/:id", userController.deleteAccount);

// ── Progress Routes ───────────────────────────────────────────────────────────

router.get(
  "/progress/daily-xp",
  requireAuth,
  dailyXpController.getDailyXp,
);
router.post(
  "/progress/daily-xp/record",
  requireAuth,
  dailyXpController.recordDailyXp,
);
router.post(
  "/progress/daily-xp/mark-read",
  requireAuth,
  dailyXpController.markDailyXpRead,
);

/** GET /api/auth/polycoder/me/progress — progress for logged-in user */
router.get(
  "/polycoder/me/progress",
  requireAuth,
  progressController.getMyPolycoderProgress,
);

/** GET /api/auth/polycoder/me/daily-points — daily points for logged-in user */
router.get(
  "/polycoder/me/daily-points",
  requireAuth,
  progressController.getMyPolycoderDailyPoints,
);

/** GET /api/auth/polycoder/:username/progress — full progress JSON (Postman-friendly) */
router.get("/polycoder/:username/progress", progressController.getPolycoderProgress);

/** GET /api/auth/polycoder/:username/daily-points — daily points earned per day */
router.get(
  "/polycoder/:username/daily-points",
  progressController.getPolycoderDailyPoints,
);

router.get("/progress/dashboard/:userId", progressController.getDashboardStats);

// ── Learn: Shared Course Progress Routes ─────────────────────────────────────

router.get(
  "/learn/progress",
  requireAuth,
  courseProgressController.listMyProgress,
);
router.get(
  "/learn/dashboard",
  requireAuth,
  courseProgressController.getDashboard,
);
router.post(
  "/learn/progress/merge",
  requireAuth,
  courseProgressController.mergeLocal,
);

// ── Learn: Review my mistakes ────────────────────────────────────────────────

/** GET /api/auth/learn/review/due?courseId=a,b — missed MCQs due today */
router.get("/learn/review/due", requireAuth, reviewController.listDue);

/** GET /api/auth/learn/review/count?courseId=a,b — badge count */
router.get("/learn/review/count", requireAuth, reviewController.countDue);

/** POST /api/auth/learn/review/:itemId/answer — body { correct } */
router.post(
  "/learn/review/:itemId/answer",
  requireAuth,
  reviewController.answer,
);

/** POST /api/auth/learn/review/:itemId/dismiss — question changed, drop it */
router.post(
  "/learn/review/:itemId/dismiss",
  requireAuth,
  reviewController.dismiss,
);

router.get(
  "/learn/:courseId/progress",
  requireAuth,
  courseProgressController.getProgress,
);
router.post(
  "/learn/:courseId/progress/last-lesson",
  requireAuth,
  courseProgressController.setLastLesson,
);
router.post(
  "/learn/:courseId/progress/complete",
  requireAuth,
  courseProgressController.completeLesson,
);
router.post(
  "/learn/:courseId/progress/code",
  requireAuth,
  courseProgressController.saveCode,
);
router.post(
  "/learn/:courseId/progress/note",
  requireAuth,
  courseProgressController.saveNote,
);
router.post(
  "/learn/:courseId/progress/bookmark",
  requireAuth,
  courseProgressController.toggleBookmark,
);
router.post(
  "/learn/:courseId/progress/time",
  requireAuth,
  courseProgressController.addTime,
);
router.post(
  "/learn/:courseId/progress/engagement",
  requireAuth,
  courseProgressController.upsertEngagement,
);

router.get(
  "/learn/:courseId/annotations/:lessonId",
  requireAuth,
  lessonAnnotationController.getAnnotation,
);
router.put(
  "/learn/:courseId/annotations/:lessonId",
  requireAuth,
  lessonAnnotationController.putAnnotation,
);
router.post(
  "/learn/annotations/merge",
  requireAuth,
  lessonAnnotationController.mergeAnnotations,
);

// ── Learn: OOP C++ Progress Routes (legacy aliases → shared course API) ──

router.get(
  "/learn/oops-cpp/progress",
  requireAuth,
  oopsCppProgressController.getProgress,
);
router.post(
  "/learn/oops-cpp/progress/last-lesson",
  requireAuth,
  oopsCppProgressController.setLastLesson,
);
router.post(
  "/learn/oops-cpp/progress/complete",
  requireAuth,
  oopsCppProgressController.completeLesson,
);
router.post(
  "/learn/oops-cpp/progress/code",
  requireAuth,
  oopsCppProgressController.saveCode,
);
router.post(
  "/learn/oops-cpp/progress/note",
  requireAuth,
  oopsCppProgressController.saveNote,
);
router.post(
  "/learn/oops-cpp/progress/bookmark",
  requireAuth,
  oopsCppProgressController.toggleBookmark,
);
router.post(
  "/learn/oops-cpp/progress/time",
  requireAuth,
  oopsCppProgressController.addTime,
);

module.exports = router;
