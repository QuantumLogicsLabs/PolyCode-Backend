const express = require("express");
const router = express.Router();
const userController = require("./controllers/userController");
const progressController = require("./controllers/progressController");

// ────────────────────────────────────────────────────────────────────────────
// User Management Routes
// ────────────────────────────────────────────────────────────────────────────

/**
 * POST /api/auth/register
 * Register a new user
 * Body: { email, username, password, firstName?, lastName? }
 */
router.post("/register", userController.register);

/**
 * POST /api/auth/login
 * Login user
 * Body: { email, password }
 */
router.post("/login", userController.login);

/**
 * GET /api/auth/user/:id
 * Get user profile by ID
 */
router.get("/user/:id", userController.getUserProfile);

/**
 * PUT /api/auth/user/:id
 * Update user profile
 * Body: { firstName?, lastName?, bio?, profilePicture?, preferredLanguages? }
 */
router.put("/user/:id", userController.updateProfile);

/**
 * POST /api/auth/change-password
 * Change user password
 * Body: { userId, oldPassword, newPassword }
 */
router.post("/change-password", userController.changePasswordHandler);

/**
 * DELETE /api/auth/user/:id
 * Delete user account
 */
router.delete("/user/:id", userController.deleteAccount);

// ────────────────────────────────────────────────────────────────────────────
// Progress Tracking Routes
// ────────────────────────────────────────────────────────────────────────────

/**
 * GET /api/auth/progress/:userId/:language
 * Get progress for specific language
 */
router.get("/progress/:userId/:language", progressController.getLanguageProgress);

/**
 * GET /api/auth/progress/:userId
 * Get all progress for user
 */
router.get("/progress/:userId", progressController.getAllProgress);

/**
 * POST /api/auth/progress/mark-module
 * Mark a module as completed
 * Body: { userId, language, moduleName }
 */
router.post("/progress/mark-module", progressController.markModuleComplete);

/**
 * POST /api/auth/progress/mark-document
 * Mark a document as completed
 * Body: { userId, language, documentInfo: { path, title } }
 */
router.post("/progress/mark-document", progressController.markDocumentComplete);

/**
 * POST /api/auth/progress/bookmark
 * Add or remove bookmark
 * Body: { userId, language, bookmarkInfo: { path, title, note? }, remove? }
 */
router.post("/progress/bookmark", progressController.toggleBookmark);

/**
 * POST /api/auth/progress/add-time
 * Add time spent learning
 * Body: { userId, language, minutes }
 */
router.post("/progress/add-time", progressController.addTimeSpent);

/**
 * POST /api/auth/progress/mark-language-complete
 * Mark entire language as completed
 * Body: { userId, language }
 */
router.post(
  "/progress/mark-language-complete",
  progressController.markLanguageComplete
);

/**
 * GET /api/auth/progress/dashboard/:userId
 * Get dashboard statistics
 */
router.get("/progress/dashboard/:userId", progressController.getDashboardStats);

module.exports = router;
