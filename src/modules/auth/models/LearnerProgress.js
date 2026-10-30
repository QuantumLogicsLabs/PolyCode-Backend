const mongoose = require("mongoose");

const completedLessonSchema = new mongoose.Schema(
  {
    lessonId: { type: String, required: true },
    title: { type: String, default: "" },
    chapterId: { type: String, default: "" },
    chapterTitle: { type: String, default: "" },
    xp: { type: Number, default: 0 },
    completedAt: { type: Date, default: Date.now },
  },
  { _id: false },
);

const savedCodeSchema = new mongoose.Schema(
  {
    lessonId: { type: String, required: true },
    code: { type: String, default: "" },
    updatedAt: { type: Date, default: Date.now },
  },
  { _id: false },
);

const lessonNoteSchema = new mongoose.Schema(
  {
    lessonId: { type: String, required: true },
    note: { type: String, default: "" },
    updatedAt: { type: Date, default: Date.now },
  },
  { _id: false },
);

const lessonEngagementSchema = new mongoose.Schema(
  {
    lessonId: { type: String, required: true },
    read: { type: Boolean, default: false },
    confidence: { type: String, default: "" },
    quizAttempts: { type: mongoose.Schema.Types.Mixed, default: {} },
    challengeAttempts: { type: Number, default: 0 },
    challengeLastResult: { type: String, default: "" },
    lastTab: { type: String, default: "" },
    updatedAt: { type: Date, default: Date.now },
  },
  { _id: false },
);

const courseEntrySchema = new mongoose.Schema(
  {
    courseId: { type: String, required: true },
    completedLessons: { type: [completedLessonSchema], default: [] },
    savedCode: { type: [savedCodeSchema], default: [] },
    notes: { type: [lessonNoteSchema], default: [] },
    lessonEngagement: { type: [lessonEngagementSchema], default: [] },
    bookmarks: { type: [String], default: [] },
    lastLessonId: { type: String, default: null },
    totalXp: { type: Number, default: 0 },
    totalMinutesSpent: { type: Number, default: 0 },
    currentStreak: { type: Number, default: 0 },
    lastActiveDate: { type: Date, default: null },
  },
  { _id: false },
);

const dailyLessonSchema = new mongoose.Schema(
  {
    lessonId: { type: String, required: true },
    course: { type: String, default: "" },
    title: { type: String, default: "" },
    xp: { type: Number, default: 0 },
    recordedAt: { type: Date, default: Date.now },
  },
  { _id: false },
);

const dailyDaySchema = new mongoose.Schema(
  {
    dateKey: { type: String, required: true },
    lessons: { type: [dailyLessonSchema], default: [] },
    lessonXp: { type: Number, default: 0 },
    readBonusXp: { type: Number, default: 0 },
    read: { type: Boolean, default: false },
    readAt: { type: Date, default: null },
  },
  { _id: false },
);

const strokeSchema = new mongoose.Schema(
  {
    color: { type: String, default: "" },
    width: { type: Number, default: 3 },
    points: { type: mongoose.Schema.Types.Mixed, default: [] },
  },
  { _id: false },
);

const labelSchema = new mongoose.Schema(
  {
    id: { type: String, required: true },
    x: { type: Number, default: 0 },
    y: { type: Number, default: 0 },
    text: { type: String, default: "" },
  },
  { _id: false },
);

const annotationImageRefSchema = new mongoose.Schema(
  {
    id: { type: String, required: true },
    x: { type: Number, default: 0 },
    y: { type: Number, default: 0 },
    width: { type: Number, default: 240 },
    height: { type: Number, default: 180 },
    // Key of an AnnotationImage document (bytes are stored there).
    imageId: { type: String, required: true },
  },
  { _id: false },
);

const annotationEntrySchema = new mongoose.Schema(
  {
    courseId: { type: String, required: true },
    lessonId: { type: String, required: true },
    tab: {
      type: String,
      default: "theory",
      enum: ["theory", "challenge"],
    },
    strokes: { type: [strokeSchema], default: [] },
    labels: { type: [labelSchema], default: [] },
    images: { type: [annotationImageRefSchema], default: [] },
    updatedAt: { type: Date, default: Date.now },
  },
  { _id: false },
);

const learnerProgressSchema = new mongoose.Schema(
  {
    userId: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
      unique: true,
      index: true,
    },
    courses: {
      type: [courseEntrySchema],
      default: [],
    },
    dailyXp: {
      days: { type: [dailyDaySchema], default: [] },
      totalXp: { type: Number, default: 0 },
    },
    annotations: {
      type: [annotationEntrySchema],
      default: [],
    },
  },
  { timestamps: true },
);

/** Hard size guard (~10MB) — Mongo allows 16MB; leave headroom. */
learnerProgressSchema.statics.MAX_DOC_BYTES = 10 * 1024 * 1024;

module.exports = mongoose.model(
  "LearnerProgress",
  learnerProgressSchema,
  "learner_progress",
);
