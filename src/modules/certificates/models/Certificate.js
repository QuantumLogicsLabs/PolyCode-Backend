const crypto = require("crypto");
const mongoose = require("mongoose");

/**
 * A course completion certificate. Issued only by the server after it checks
 * the learner's own progress; the verify page loads it by certificateId.
 */
const certificateSchema = new mongoose.Schema(
  {
    // Public, unguessable id used in verify links (never the Mongo _id).
    certificateId: {
      type: String,
      required: true,
      unique: true,
      default: () => crypto.randomUUID(),
    },
    userId: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
    },
    courseId: { type: String, required: true },
    courseName: { type: String, required: true },
    // Learner's name when the certificate was issued, from the user record.
    recipientName: { type: String, required: true },
    issuedAt: { type: Date, required: true, default: Date.now },
    lessonsCompleted: { type: Number, required: true },
    xp: { type: Number, required: true },
  },
  { timestamps: true },
);

// One certificate per learner per course.
certificateSchema.index({ userId: 1, courseId: 1 }, { unique: true });

module.exports = mongoose.model("Certificate", certificateSchema, "certificates");
