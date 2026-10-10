const { assertCourseId } = require("../constants/courseIds");
const dailyXpService = require("./dailyXpService");
const reviewService = require("./reviewService");
const {
  findLearnerDoc,
  getOrCreateLearnerDoc,
  courseToProgress,
  ensureCourseEntry,
  saveLearnerDoc,
} = require("./learnerProgressStore");

function touchStreak(progress) {
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  if (!progress.lastActiveDate) {
    progress.currentStreak = 1;
  } else {
    const last = new Date(progress.lastActiveDate);
    last.setHours(0, 0, 0, 0);
    const diffDays = Math.floor((today - last) / (1000 * 60 * 60 * 24));

    if (diffDays === 1) progress.currentStreak += 1;
    else if (diffDays > 1) progress.currentStreak = 1;
  }

  progress.lastActiveDate = new Date();
}

function recalcTotalXp(progress) {
  progress.totalXp = (progress.completedLessons || []).reduce(
    (sum, lesson) => sum + (Number(lesson.xp) || 0),
    0,
  );
}

function emptyCourseProgress(userId, courseId) {
  return courseToProgress(
    {
      courseId,
      completedLessons: [],
      savedCode: [],
      notes: [],
      lessonEngagement: [],
      bookmarks: [],
      lastLessonId: null,
      totalXp: 0,
      totalMinutesSpent: 0,
      currentStreak: 0,
      lastActiveDate: null,
    },
    userId,
  );
}

const MAX_SAVE_ATTEMPTS = 8;

/**
 * Saves that hit the same learner doc at once (lesson complete, engagement,
 * code autosave, time tracking) fail with a VersionError, or with a duplicate
 * key when two requests create the doc together. Both are safe to retry,
 * because every attempt reloads the doc and reapplies the mutator to it.
 */
function isRetryableSaveError(error) {
  return error?.name === "VersionError" || error?.code === 11000;
}

function wait(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function withCourse(userId, courseId, mutator, { createIfMissing = true } = {}) {
  const id = assertCourseId(courseId);

  for (let attempt = 1; ; attempt += 1) {
    try {
      let learner = null;

      if (createIfMissing) {
        learner = await getOrCreateLearnerDoc(userId);
      } else {
        learner = await findLearnerDoc(userId);
        if (!learner) {
          return emptyCourseProgress(userId, id);
        }
      }

      const course = ensureCourseEntry(learner, id);
      await mutator(course, learner);
      await saveLearnerDoc(learner);
      return courseToProgress(course, userId);
    } catch (error) {
      if (!isRetryableSaveError(error) || attempt >= MAX_SAVE_ATTEMPTS) {
        throw error;
      }
      // Growing, random backoff so the colliding requests spread out instead
      // of retrying in lockstep.
      await wait(Math.floor(Math.random() * 30 * 2 ** Math.min(attempt, 5)));
    }
  }
}

/**
 * Read-only: never creates learner_progress or empty course slots.
 */
async function getOrCreateProgress(userId, courseId) {
  const id = assertCourseId(courseId);
  const learner = await findLearnerDoc(userId);
  const course = learner?.courses?.find((item) => item.courseId === id) || null;

  if (!course) {
    return emptyCourseProgress(userId, id);
  }
  return courseToProgress(course, userId);
}

async function getProgress(userId, courseId) {
  return getOrCreateProgress(userId, courseId);
}

async function listProgressForUser(userId, { includePrivate = true } = {}) {
  const learner = await findLearnerDoc(userId);
  if (!learner) {
    return [];
  }
  const docs = (learner.courses || []).map((course) =>
    courseToProgress(course, userId),
  );

  if (!includePrivate) {
    return docs.map((doc) => ({
      courseId: doc.courseId,
      completedLessons: (doc.completedLessons || []).map((lesson) => ({
        lessonId: lesson.lessonId,
        title: lesson.title,
        chapterId: lesson.chapterId,
        chapterTitle: lesson.chapterTitle,
        xp: lesson.xp,
        completedAt: lesson.completedAt,
      })),
      bookmarks: doc.bookmarks || [],
      lastLessonId: doc.lastLessonId || null,
      totalXp: doc.totalXp || 0,
      totalMinutesSpent: doc.totalMinutesSpent || 0,
      currentStreak: doc.currentStreak || 0,
      lastActiveDate: doc.lastActiveDate || null,
    }));
  }

  return docs;
}

function countQuizAnswers(quizAttempts = {}) {
  return Object.keys(quizAttempts || {}).length;
}

function countQuizCorrect(quizAttempts = {}) {
  let correct = 0;
  for (const value of Object.values(quizAttempts || {})) {
    if (value && typeof value === "object" && value.correct === true) {
      correct += 1;
    }
  }
  return correct;
}

const MAX_QUESTION_HASH_LENGTH = 64;

// Short fingerprint of the question the learner saw, so Review can tell when
// the question at this position has since been edited or replaced.
function normalizeQuestionHash(value) {
  if (typeof value !== "string") return null;
  const hash = value.trim();
  return hash && hash.length <= MAX_QUESTION_HASH_LENGTH ? hash : null;
}

function normalizeQuizAttemptValue(value) {
  if (value == null) return null;
  if (typeof value === "number") {
    return {
      selectedIndex: value,
      correct: null,
      answeredAt: null,
      questionHash: null,
    };
  }
  if (typeof value === "object") {
    const selectedIndex =
      value.selectedIndex !== undefined
        ? value.selectedIndex
        : value.selected !== undefined
          ? value.selected
          : null;
    return {
      selectedIndex,
      correct:
        value.correct === undefined || value.correct === null
          ? null
          : Boolean(value.correct),
      answeredAt: value.answeredAt || null,
      questionHash: normalizeQuestionHash(value.questionHash),
    };
  }
  return null;
}

function hasAnswer(attempt) {
  return attempt?.selectedIndex !== null && attempt?.selectedIndex !== undefined;
}

/**
 * Only the learner's first answer to each question counts. Once a question
 * has an answer, later picks (a second click, "Solve again", an old tab, a
 * login merge) can't replace it. The one exception: an older answer saved
 * without `correct` gets it filled in when the same option is sent again.
 */
function mergeQuizAttempts(existing = {}, incoming = {}) {
  const next = { ...(existing || {}) };
  for (const [key, raw] of Object.entries(incoming || {})) {
    const normalized = normalizeQuizAttemptValue(raw);
    if (!normalized) continue;
    const prev = normalizeQuizAttemptValue(next[key]);
    if (hasAnswer(prev)) {
      const fillsMissingResult =
        prev.correct === null &&
        normalized.correct !== null &&
        normalized.selectedIndex === prev.selectedIndex;
      if (fillsMissingResult) {
        next[key] = {
          ...prev,
          correct: normalized.correct,
          questionHash: prev.questionHash || normalized.questionHash,
        };
      }
      continue;
    }
    next[key] = {
      selectedIndex:
        normalized.selectedIndex !== null && normalized.selectedIndex !== undefined
          ? normalized.selectedIndex
          : prev?.selectedIndex ?? null,
      correct:
        normalized.correct !== null && normalized.correct !== undefined
          ? normalized.correct
          : prev?.correct ?? null,
      answeredAt: normalized.answeredAt || prev?.answeredAt || new Date(),
      questionHash: normalized.questionHash || prev?.questionHash || null,
    };
  }
  return next;
}

function upsertEngagementEntry(progress, payload = {}) {
  const lessonId = String(payload.lessonId || "").trim();
  if (!lessonId) {
    throw new Error("lessonId is required");
  }

  if (!Array.isArray(progress.lessonEngagement)) {
    progress.lessonEngagement = [];
  }

  let entry = progress.lessonEngagement.find((item) => item.lessonId === lessonId);
  if (!entry) {
    progress.lessonEngagement.push({
      lessonId,
      read: false,
      confidence: "",
      quizAttempts: {},
      challengeAttempts: 0,
      challengeLastResult: "",
      lastTab: "",
      updatedAt: new Date(),
    });
    // Mongoose stores a copy of the pushed object, so edit the stored entry,
    // otherwise the first write for a new lesson is lost.
    entry = progress.lessonEngagement[progress.lessonEngagement.length - 1];
  }

  if (payload.read !== undefined) {
    entry.read = Boolean(payload.read) || entry.read;
  }
  if (payload.confidence !== undefined && payload.confidence !== null) {
    entry.confidence = String(payload.confidence);
  }
  if (payload.quizAttempts && typeof payload.quizAttempts === "object") {
    entry.quizAttempts = mergeQuizAttempts(
      entry.quizAttempts || {},
      payload.quizAttempts,
    );
  }
  if (payload.incrementChallengeAttempts) {
    entry.challengeAttempts = (Number(entry.challengeAttempts) || 0) + 1;
  } else if (payload.challengeAttempts !== undefined) {
    entry.challengeAttempts = Math.max(
      0,
      Number(payload.challengeAttempts) || 0,
    );
  }
  if (payload.challengeLastResult !== undefined && payload.challengeLastResult !== null) {
    entry.challengeLastResult = String(payload.challengeLastResult);
  }
  if (payload.lastTab !== undefined && payload.lastTab !== null) {
    entry.lastTab = String(payload.lastTab);
  }

  entry.updatedAt = new Date();
  return entry;
}

/**
 * Queues wrong first answers for Review once the progress save has gone
 * through. A failure here only logs: the answer itself is already saved, and
 * the backfill script can add the item later.
 */
async function scheduleReviews(userId, progress, lessonIds) {
  if (!progress?.courseId || lessonIds.length === 0) return;
  try {
    await reviewService.scheduleMissedQuizzes(
      userId,
      progress.courseId,
      progress.lessonEngagement,
      lessonIds,
    );
  } catch (error) {
    console.warn("Review scheduling failed:", error.message);
  }
}

async function upsertLessonEngagement(userId, courseId, payload = {}) {
  const progress = await withCourse(
    userId,
    courseId,
    async (course) => {
      upsertEngagementEntry(course, payload);
      touchStreak(course);
    },
    // A quiz answer is a real result, so save it even if this is the
    // learner's first progress write. Other engagement saves still don't
    // create a learner doc on their own.
    { createIfMissing: Boolean(payload.quizAttempts) },
  );
  if (payload.quizAttempts) {
    await scheduleReviews(userId, progress, [String(payload.lessonId).trim()]);
  }
  return progress;
}

async function completeLesson(userId, courseId, lesson) {
  return withCourse(userId, courseId, async (course, learner) => {
    const lessonId = lesson.lessonId || lesson.id;
    if (!lessonId) {
      throw new Error("lessonId is required");
    }

    const xpAmount = Math.max(0, Number(lesson.xp) || 0);
    const existing = course.completedLessons.find(
      (item) => item.lessonId === lessonId,
    );

    if (!existing) {
      const completedAt = new Date();
      course.completedLessons.push({
        lessonId,
        title: lesson.title || "",
        chapterId: lesson.chapterId || "",
        chapterTitle: lesson.chapterTitle || "",
        xp: xpAmount,
        completedAt,
      });
      recalcTotalXp(course);
      dailyXpService.applyLessonXp(learner, {
        course: courseId,
        lessonId,
        title: lesson.title || "",
        xp: xpAmount,
        recordedAt: completedAt,
      });
    } else {
      // Backfill XP if lesson was marked complete earlier without points.
      const prevXp = Math.max(0, Number(existing.xp) || 0);
      if (xpAmount > prevXp) {
        existing.xp = xpAmount;
        if (lesson.title) existing.title = lesson.title;
        recalcTotalXp(course);
      }
      dailyXpService.applyLessonXp(learner, {
        course: courseId,
        lessonId,
        title: lesson.title || existing.title || "",
        xp: Math.max(xpAmount, prevXp),
        recordedAt: existing.completedAt || new Date(),
      });
    }

    course.lastLessonId = lessonId;
    touchStreak(course);
  });
}

async function setLastLesson(userId, courseId, lessonId) {
  return withCourse(
    userId,
    courseId,
    async (course) => {
      course.lastLessonId = lessonId;
      touchStreak(course);
    },
    { createIfMissing: false },
  );
}

async function saveCode(userId, courseId, lessonId, code) {
  return withCourse(userId, courseId, async (course) => {
    const existing = course.savedCode.find((item) => item.lessonId === lessonId);

    if (existing) {
      existing.code = code;
      existing.updatedAt = new Date();
    } else {
      course.savedCode.push({ lessonId, code, updatedAt: new Date() });
    }

    course.lastLessonId = lessonId;
    touchStreak(course);
  });
}

async function saveNote(userId, courseId, lessonId, note) {
  return withCourse(userId, courseId, async (course) => {
    const existing = course.notes.find((item) => item.lessonId === lessonId);

    if (existing) {
      existing.note = note;
      existing.updatedAt = new Date();
    } else {
      course.notes.push({ lessonId, note, updatedAt: new Date() });
    }

    course.lastLessonId = lessonId;
    touchStreak(course);
  });
}

async function toggleBookmark(userId, courseId, lessonId) {
  return withCourse(userId, courseId, async (course) => {
    if (course.bookmarks.includes(lessonId)) {
      course.bookmarks = course.bookmarks.filter((id) => id !== lessonId);
    } else {
      course.bookmarks.push(lessonId);
    }

    course.lastLessonId = lessonId;
    touchStreak(course);
  });
}

async function addTime(userId, courseId, minutes) {
  return withCourse(
    userId,
    courseId,
    async (course) => {
      course.totalMinutesSpent += Math.max(0, Number(minutes) || 0);
      touchStreak(course);
    },
    { createIfMissing: false },
  );
}

async function mergeLocalProgress(userId, courseId, localPayload = {}) {
  const progress = await withCourse(userId, courseId, async (course, learner) => {
    const completedMap = localPayload.completedMap || {};
    const savedCodeMap = localPayload.savedCodeMap || {};
    const notesMap = localPayload.notesMap || {};
    const engagementMap = localPayload.engagementMap || {};
    const bookmarks = Array.isArray(localPayload.bookmarks)
      ? localPayload.bookmarks
      : [];

    for (const [lessonId, meta] of Object.entries(completedMap)) {
      const existing = course.completedLessons.find(
        (item) => item.lessonId === lessonId,
      );
      const localAt = meta?.at ? new Date(meta.at) : null;
      const incomingXp = Math.max(0, Number(meta?.xp) || 0);
      if (!existing) {
        const completedAt =
          localAt && !Number.isNaN(localAt.getTime()) ? localAt : new Date();
        course.completedLessons.push({
          lessonId,
          title: meta?.title || "",
          chapterId: meta?.chapterId || "",
          chapterTitle: meta?.chapterTitle || "",
          xp: incomingXp,
          completedAt,
        });
        dailyXpService.applyLessonXp(learner, {
          course: courseId,
          lessonId,
          title: meta?.title || "",
          xp: incomingXp,
          recordedAt: completedAt,
        });
      } else {
        const prevXp = Math.max(0, Number(existing.xp) || 0);
        if (incomingXp > prevXp) {
          existing.xp = incomingXp;
        }
        if (meta?.title && !existing.title) existing.title = meta.title;
        if (
          localAt &&
          !Number.isNaN(localAt.getTime()) &&
          existing.completedAt &&
          localAt > new Date(existing.completedAt)
        ) {
          existing.completedAt = localAt;
        }
        dailyXpService.applyLessonXp(learner, {
          course: courseId,
          lessonId,
          title: meta?.title || existing.title || "",
          xp: Math.max(incomingXp, prevXp),
          recordedAt: existing.completedAt || localAt || new Date(),
        });
      }
    }

    for (const [lessonId, code] of Object.entries(savedCodeMap)) {
      const existing = course.savedCode.find((item) => item.lessonId === lessonId);
      if (existing) {
        if (!existing.code && code) {
          existing.code = code;
          existing.updatedAt = new Date();
        }
      } else if (typeof code === "string") {
        course.savedCode.push({ lessonId, code, updatedAt: new Date() });
      }
    }

    for (const [lessonId, note] of Object.entries(notesMap)) {
      const existing = course.notes.find((item) => item.lessonId === lessonId);
      if (existing) {
        if (!existing.note && note) {
          existing.note = note;
          existing.updatedAt = new Date();
        }
      } else if (typeof note === "string") {
        course.notes.push({ lessonId, note, updatedAt: new Date() });
      }
    }

    for (const [lessonId, engagement] of Object.entries(engagementMap)) {
      upsertEngagementEntry(course, {
        lessonId,
        read: engagement?.read,
        confidence: engagement?.confidence,
        quizAttempts: engagement?.quizAttempts,
      });
    }

    const bookmarkSet = new Set([...(course.bookmarks || []), ...bookmarks]);
    course.bookmarks = Array.from(bookmarkSet);

    if (localPayload.lastLessonId && !course.lastLessonId) {
      course.lastLessonId = localPayload.lastLessonId;
    }

    recalcTotalXp(course);
    touchStreak(course);
  });
  const quizLessonIds = Object.entries(localPayload.engagementMap || {})
    .filter(([, engagement]) => engagement?.quizAttempts)
    .map(([lessonId]) => lessonId);
  await scheduleReviews(userId, progress, quizLessonIds);
  return progress;
}

function dayKey(date = new Date()) {
  return new Date(date).toISOString().slice(0, 10);
}

function isActiveStreakDate(lastActiveDate) {
  if (!lastActiveDate) return false;
  const last = dayKey(lastActiveDate);
  const today = dayKey(new Date());
  const yesterdayDate = new Date();
  yesterdayDate.setDate(yesterdayDate.getDate() - 1);
  const yesterday = dayKey(yesterdayDate);
  return last === today || last === yesterday;
}

function summarizeEngagement(docs = []) {
  let lessonsRead = 0;
  let quizAnswered = 0;
  let quizCorrect = 0;
  let challengeFails = 0;

  for (const doc of docs) {
    for (const row of doc.lessonEngagement || []) {
      if (row.read) lessonsRead += 1;
      quizAnswered += countQuizAnswers(row.quizAttempts);
      quizCorrect += countQuizCorrect(row.quizAttempts);
      challengeFails += Number(row.challengeAttempts) || 0;
    }
  }

  return { lessonsRead, quizAnswered, quizCorrect, challengeFails };
}

async function getLearnDashboard(userId) {
  const courses = await listProgressForUser(userId, { includePrivate: true });
  const dailyXp = await dailyXpService.getDailyXp(userId);

  let totalXp = 0;
  let totalMinutesSpent = 0;
  let bestStreak = 0;
  let activeStreak = 0;
  let coursesStarted = 0;

  const courseRows = courses.map((doc) => {
    const completedCount = (doc.completedLessons || []).length;
    const started =
      completedCount > 0 ||
      (doc.bookmarks || []).length > 0 ||
      Boolean(doc.lastLessonId) ||
      (doc.lessonEngagement || []).length > 0;
    if (started) coursesStarted += 1;

    totalXp += Number(doc.totalXp) || 0;
    totalMinutesSpent += Number(doc.totalMinutesSpent) || 0;
    bestStreak = Math.max(bestStreak, Number(doc.currentStreak) || 0);
    if (isActiveStreakDate(doc.lastActiveDate)) {
      activeStreak = Math.max(activeStreak, Number(doc.currentStreak) || 0);
    }

    return {
      courseId: doc.courseId,
      totalXp: doc.totalXp || 0,
      completedCount,
      bookmarks: (doc.bookmarks || []).length,
      minutes: doc.totalMinutesSpent || 0,
      streak: doc.currentStreak || 0,
      lastLessonId: doc.lastLessonId || null,
      lastActiveDate: doc.lastActiveDate || null,
      lessonsRead: (doc.lessonEngagement || []).filter((row) => row.read).length,
      quizAnswered: (doc.lessonEngagement || []).reduce(
        (sum, row) => sum + countQuizAnswers(row.quizAttempts),
        0,
      ),
      quizCorrect: (doc.lessonEngagement || []).reduce(
        (sum, row) => sum + countQuizCorrect(row.quizAttempts),
        0,
      ),
      challengeFails: (doc.lessonEngagement || []).reduce(
        (sum, row) => sum + (Number(row.challengeAttempts) || 0),
        0,
      ),
    };
  });

  courseRows.sort((a, b) => {
    const aTime = a.lastActiveDate ? new Date(a.lastActiveDate).getTime() : 0;
    const bTime = b.lastActiveDate ? new Date(b.lastActiveDate).getTime() : 0;
    return bTime - aTime;
  });

  const { lessonsRead, quizAnswered, quizCorrect, challengeFails } =
    summarizeEngagement(courses);
  const coursesCompleted = courseRows.filter((row) => row.completedCount > 0).length;
  const dailyXpTotal = dailyXp?.totalXp || 0;

  return {
    overview: {
      totalXp,
      dailyXpTotal,
      totalMinutesSpent,
      coursesStarted,
      coursesCompleted,
      bestStreak,
      activeStreak,
      lessonsRead,
      quizAnswered,
      quizCorrect,
      challengeFails,
    },
    courses: courseRows,
  };
}

async function mergeManyLocalProgress(userId, courses = {}) {
  const results = {};
  for (const [courseId, payload] of Object.entries(courses)) {
    const hasData =
      Object.keys(payload?.completedMap || {}).length > 0 ||
      Object.keys(payload?.savedCodeMap || {}).length > 0 ||
      Object.keys(payload?.notesMap || {}).length > 0 ||
      Object.keys(payload?.engagementMap || {}).length > 0 ||
      (payload?.bookmarks || []).length > 0 ||
      Boolean(payload?.lastLessonId);
    if (!hasData) {
      results[courseId] = { skipped: true, reason: "empty payload" };
      continue;
    }
    try {
      results[courseId] = await mergeLocalProgress(userId, courseId, payload);
    } catch (error) {
      results[courseId] = { error: error.message };
    }
  }
  return results;
}

module.exports = {
  getProgress,
  listProgressForUser,
  completeLesson,
  setLastLesson,
  saveCode,
  saveNote,
  toggleBookmark,
  addTime,
  upsertLessonEngagement,
  mergeLocalProgress,
  mergeManyLocalProgress,
  getOrCreateProgress,
  getLearnDashboard,
};
