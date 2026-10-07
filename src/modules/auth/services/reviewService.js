const mongoose = require("mongoose");
const ReviewItem = require("../models/ReviewItem");
const { assertCourseId } = require("../constants/courseIds");

const REVIEW_INTERVAL_DAYS = 7;
const DAY_MS = 24 * 60 * 60 * 1000;
const MAX_DUE_ITEMS = 100;

function addDays(date, days) {
  return new Date(new Date(date).getTime() + days * DAY_MS);
}

function httpError(message, statusCode) {
  const err = new Error(message);
  err.statusCode = statusCode;
  return err;
}

function toValidDate(value) {
  const date = value ? new Date(value) : null;
  return date && !Number.isNaN(date.getTime()) ? date : null;
}

/**
 * Wrong first attempts in a course's stored engagement rows. Reads what was
 * saved (not the request body), because later picks are ignored on save.
 */
function findMissedQuizzes(lessonEngagement = [], lessonIds = null) {
  const only = lessonIds ? new Set(lessonIds.map(String)) : null;
  const missed = [];
  for (const row of lessonEngagement || []) {
    if (!row?.lessonId || (only && !only.has(row.lessonId))) continue;
    for (const [quizIndex, attempt] of Object.entries(row.quizAttempts || {})) {
      if (attempt && typeof attempt === "object" && attempt.correct === false) {
        missed.push({
          lessonId: row.lessonId,
          quizIndex: String(quizIndex),
          missedAt: toValidDate(attempt.answeredAt) || new Date(),
          questionHash:
            typeof attempt.questionHash === "string" ? attempt.questionHash : "",
        });
      }
    }
  }
  return missed;
}

/**
 * Adds a review item for each missed quiz. `$setOnInsert` + the unique index
 * mean a repeat call (save retry, login merge, backfill) never duplicates an
 * item or resets one the learner is already reviewing.
 */
async function scheduleMissedQuizzes(userId, courseId, lessonEngagement, lessonIds = null) {
  const missed = findMissedQuizzes(lessonEngagement, lessonIds);
  if (missed.length === 0) return 0;

  const result = await ReviewItem.bulkWrite(
    missed.map(({ lessonId, quizIndex, missedAt, questionHash }) => ({
      updateOne: {
        filter: { userId, courseId, lessonId, quizIndex },
        update: {
          $setOnInsert: {
            missedAt,
            questionHash,
            nextReviewAt: addDays(missedAt, REVIEW_INTERVAL_DAYS),
          },
        },
        upsert: true,
      },
    })),
    { ordered: false },
  );
  return result.upsertedCount || 0;
}

/** `courseId` is one id or a comma-separated list, e.g. "sql-joins,sql-views". */
function dueFilter(userId, { courseId, now = new Date() } = {}) {
  const filter = { userId, resolvedAt: null, nextReviewAt: { $lte: now } };
  const ids = String(courseId || "")
    .split(",")
    .map((id) => id.trim())
    .filter(Boolean);
  if (ids.length) filter.courseId = { $in: ids.map(assertCourseId) };
  return filter;
}

function formatItem(item) {
  return {
    id: String(item._id),
    courseId: item.courseId,
    lessonId: item.lessonId,
    quizIndex: item.quizIndex,
    questionHash: item.questionHash || "",
    missedAt: item.missedAt,
    nextReviewAt: item.nextReviewAt,
    reviewCount: item.reviewCount || 0,
    lastResult: item.lastResult ?? null,
    resolvedAt: item.resolvedAt || null,
    dismissedAt: item.dismissedAt || null,
  };
}

async function listDue(userId, options = {}) {
  const items = await ReviewItem.find(dueFilter(userId, options))
    .sort({ nextReviewAt: 1 })
    .limit(MAX_DUE_ITEMS)
    .lean();
  return items.map(formatItem);
}

async function countDue(userId, options = {}) {
  return ReviewItem.countDocuments(dueFilter(userId, options));
}

function assertItemId(itemId) {
  if (!mongoose.isValidObjectId(itemId)) {
    throw httpError("Invalid review item id", 400);
  }
}

async function findOwnItem(userId, itemId) {
  const existing = await ReviewItem.findOne({ _id: itemId, userId }).lean();
  if (!existing) throw httpError("Review item not found", 404);
  return existing;
}

/**
 * Right answer resolves the item; wrong answer brings it back in 7 days.
 * Only a due, unresolved item is updated, so a double submit or an old tab
 * can't move it twice.
 */
async function answerReview(userId, itemId, correct) {
  assertItemId(itemId);
  if (typeof correct !== "boolean") {
    throw httpError("correct must be true or false", 400);
  }

  const now = new Date();
  const update = {
    $inc: { reviewCount: 1 },
    $set: correct
      ? { lastReviewedAt: now, lastResult: true, resolvedAt: now }
      : {
          lastReviewedAt: now,
          lastResult: false,
          nextReviewAt: addDays(now, REVIEW_INTERVAL_DAYS),
        },
  };

  const updated = await ReviewItem.findOneAndUpdate(
    { _id: itemId, userId, resolvedAt: null, nextReviewAt: { $lte: now } },
    update,
    { new: true },
  ).lean();
  if (updated) return { accepted: true, item: formatItem(updated) };

  return { accepted: false, item: formatItem(await findOwnItem(userId, itemId)) };
}

/**
 * Drops an item whose question no longer exists or was rewritten since it
 * was missed (the Review page checks `questionHash`), so it stops counting.
 */
async function dismissReview(userId, itemId) {
  assertItemId(itemId);
  const now = new Date();
  const updated = await ReviewItem.findOneAndUpdate(
    { _id: itemId, userId, resolvedAt: null },
    { $set: { resolvedAt: now, dismissedAt: now } },
    { new: true },
  ).lean();
  if (updated) return { accepted: true, item: formatItem(updated) };
  return { accepted: false, item: formatItem(await findOwnItem(userId, itemId)) };
}

module.exports = {
  REVIEW_INTERVAL_DAYS,
  findMissedQuizzes,
  scheduleMissedQuizzes,
  listDue,
  countDue,
  answerReview,
  dismissReview,
};
