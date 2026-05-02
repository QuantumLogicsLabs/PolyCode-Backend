const mongoose = require("mongoose");

const userProgressSchema = new mongoose.Schema(
  {
    userId: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
      index: true,
    },
    language: {
      type: String,
      required: true,
      enum: [
        "Python",
        "JavaScript",
        "Java",
        "C++",
        "C",
        "C#",
        "Go",
        "Rust",
        "Ruby",
        "PHP",
        "SQL",
        "Batchfile",
        "Powershell",
        "Q#",
      ],
      index: true,
    },
    // Track completed modules/categories
    completedModules: {
      type: [String], // Array of module names like "1-Getting Started", "2-Core Concepts", etc.
      default: [],
    },
    // Track individual document/lesson completion
    completedDocuments: {
      type: [
        {
          path: String,
          title: String,
          completedAt: { type: Date, default: Date.now },
        },
      ],
      default: [],
    },
    // Overall progress percentage for this language
    progressPercentage: {
      type: Number,
      min: 0,
      max: 100,
      default: 0,
    },
    // Time spent learning this language (in minutes)
    totalMinutesSpent: {
      type: Number,
      default: 0,
    },
    // Last module accessed
    lastAccessedModule: {
      type: String,
      default: null,
    },
    // Last accessed at
    lastAccessedAt: {
      type: Date,
      default: null,
    },
    // Streak counter (consecutive days of learning)
    currentStreak: {
      type: Number,
      default: 0,
    },
    // Last streak date
    lastStreakDate: {
      type: Date,
      default: null,
    },
    // Notes/bookmarks for this language
    bookmarks: {
      type: [
        {
          path: String,
          title: String,
          note: String,
          addedAt: { type: Date, default: Date.now },
        },
      ],
      default: [],
    },
    // Status: "not-started", "in-progress", "completed"
    status: {
      type: String,
      enum: ["not-started", "in-progress", "completed"],
      default: "not-started",
    },
    createdAt: {
      type: Date,
      default: Date.now,
    },
    updatedAt: {
      type: Date,
      default: Date.now,
    },
  },
  {
    timestamps: true,
  }
);

// Compound index for unique user-language progress
userProgressSchema.index({ userId: 1, language: 1 }, { unique: true });

module.exports = mongoose.model("UserProgress", userProgressSchema);
