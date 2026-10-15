const mongoose = require("mongoose");

/**
 * One MCQ a learner got wrong on the first attempt. It comes back on the
 * Review page at `nextReviewAt`. Review answers are stored here only, so the
 * first attempt in learner_progress (and the lesson score) never changes.
 */
const reviewItemSchema = new mongoose.Schema(
  {
    userId: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
    },
    courseId: { type: String, required: true },
    lessonId: { type: String, required: true },
    quizIndex: { type: String, required: true },
    // Fingerprint of the question when it was missed ("" for older answers).
    questionHash: { type: String, default: "" },
    missedAt: { type: Date, required: true },
    nextReviewAt: { type: Date, required: true },
    reviewCount: { type: Number, default: 0 },
    lastReviewedAt: { type: Date, default: null },
    lastResult: { type: Boolean, default: null },
    resolvedAt: { type: Date, default: null },
    // Set with resolvedAt when the question changed and can't be reviewed.
    dismissedAt: { type: Date, default: null },
  },
  { timestamps: true },
);

reviewItemSchema.index(
  { userId: 1, courseId: 1, lessonId: 1, quizIndex: 1 },
  { unique: true },
);
reviewItemSchema.index({ userId: 1, resolvedAt: 1, nextReviewAt: 1 });

module.exports = mongoose.model("ReviewItem", reviewItemSchema, "review_items");
