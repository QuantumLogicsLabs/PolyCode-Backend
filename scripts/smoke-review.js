/**
 * Smoke-test "Review my mistakes" against a running backend.
 * Usage: node scripts/smoke-review.js [apiBaseUrl]
 *        (default http://localhost:5000/api — start it with `npm start` first)
 *
 * Uses backend/.env (MONGODB_URI, JWT_SECRET) to create temp smoke users and
 * to move review dates into the past; everything else goes through HTTP.
 * Cleans up the users, their learner_progress and review items afterwards.
 */
require("dotenv").config({ path: require("path").join(__dirname, "..", ".env") });

const mongoose = require("mongoose");
const { connectToMongoDB, getMongoUri } = require("../src/config/database");
const User = require("../src/modules/auth/models/User");
const LearnerProgress = require("../src/modules/auth/models/LearnerProgress");
const ReviewItem = require("../src/modules/auth/models/ReviewItem");
const reviewService = require("../src/modules/auth/services/reviewService");
const { signAccessToken } = require("../src/utils/jwt");

const API = (process.argv[2] || "http://localhost:5000/api").replace(/\/$/, "");
const COURSE_A = "sql-joins";
const COURSE_B = "sql-views";
const DAY_MS = 24 * 60 * 60 * 1000;

let passed = 0;
function check(condition, label, detail) {
  if (!condition) {
    throw new Error(`${label}${detail === undefined ? "" : ` — got ${JSON.stringify(detail)}`}`);
  }
  passed += 1;
  console.log(`  ok  ${label}`);
}

async function call(method, route, { token, body } = {}) {
  const headers = { "Content-Type": "application/json" };
  if (token) headers.Authorization = `Bearer ${token}`;
  const res = await fetch(`${API}${route}`, {
    method,
    headers,
    body: body === undefined ? undefined : JSON.stringify(body),
  });
  let json = null;
  try {
    json = await res.json();
  } catch {
    /* empty body */
  }
  return { status: res.status, json };
}

function answerQuiz(token, courseId, lessonId, quizIndex, selectedIndex, correct, questionHash) {
  return call("POST", `/auth/learn/${courseId}/progress/engagement`, {
    token,
    body: {
      lessonId,
      quizAttempts: {
        [quizIndex]: {
          selectedIndex,
          correct,
          answeredAt: new Date().toISOString(),
          questionHash,
        },
      },
    },
  });
}

async function makeDue(userId, filter = {}) {
  await ReviewItem.updateMany(
    { userId, ...filter },
    { nextReviewAt: new Date(Date.now() - 60 * 1000) },
  );
}

async function createUser(stamp, suffix) {
  return User.create({
    email: `smoke-review-${suffix}-${stamp}@example.com`,
    username: `smoke_rev_${suffix}_${stamp}`,
    password: "SmokeTest123!",
    name: "Smoke Learner",
  });
}

async function main() {
  if (!getMongoUri()) throw new Error("No MongoDB URI configured in backend/.env");
  if (!(await connectToMongoDB())) throw new Error("Could not connect to MongoDB");
  await ReviewItem.syncIndexes();

  const stamp = Date.now().toString(36);
  const user = await createUser(stamp, "a");
  const other = await createUser(stamp, "b");
  const token = signAccessToken(String(user._id));
  const otherToken = signAccessToken(String(other._id));
  console.log(`Created smoke users ${user.username}, ${other.username} → ${API}`);

  try {
    console.log("\nAuth");
    let r = await call("GET", "/auth/learn/review/due");
    check(r.status === 401, "due signed out → 401", r.status);
    r = await call("GET", "/auth/learn/review/count");
    check(r.status === 401, "count signed out → 401", r.status);

    console.log("\nScheduling from lesson answers");
    r = await answerQuiz(token, COURSE_A, "l1", 0, 2, false, "q-abc123");
    check(r.status === 200, "wrong first answer saved", r.status);
    r = await answerQuiz(token, COURSE_A, "l1", 1, 0, true);
    check(r.status === 200, "right first answer saved", r.status);

    let items = await ReviewItem.find({ userId: user._id }).lean();
    check(
      items.length === 1 && items[0].lessonId === "l1" && items[0].quizIndex === "0",
      "only the wrong answer becomes a review item",
      items,
    );
    const days = (items[0].nextReviewAt - items[0].missedAt) / DAY_MS;
    check(Math.abs(days - 7) < 0.001, "due 7 days after the miss", days);
    check(items[0].questionHash === "q-abc123", "question fingerprint kept", items[0].questionHash);
    const firstDue = items[0].nextReviewAt.getTime();

    r = await call("GET", "/auth/learn/review/count", { token });
    check(r.status === 200 && r.json?.count === 0, "not due yet → count 0", r);
    r = await call("GET", "/auth/learn/review/due", { token });
    check(r.status === 200 && r.json?.items?.length === 0, "not due yet → empty list", r);

    await answerQuiz(token, COURSE_A, "l1", 0, 1, true, "q-other");
    await answerQuiz(token, COURSE_A, "l1", 0, 2, false, "q-other");
    items = await ReviewItem.find({ userId: user._id }).lean();
    check(
      items.length === 1 &&
        items[0].nextReviewAt.getTime() === firstDue &&
        items[0].questionHash === "q-abc123",
      "later picks and repeat saves don't duplicate or reset it",
      items,
    );

    const legacyRows = [{ lessonId: "l9", quizAttempts: { 0: 3, 1: { selectedIndex: 1 } } }];
    check(
      reviewService.findMissedQuizzes(legacyRows).length === 0,
      "answers saved without a result are skipped",
    );

    const rows = [{ lessonId: "l5", quizAttempts: { 0: { selectedIndex: 1, correct: false } } }];
    await Promise.all(
      Array.from({ length: 5 }, () =>
        reviewService.scheduleMissedQuizzes(user._id, COURSE_A, rows),
      ),
    );
    check(
      (await ReviewItem.countDocuments({ userId: user._id, lessonId: "l5" })) === 1,
      "5 parallel schedules → one item",
    );
    await ReviewItem.deleteMany({ userId: user._id, lessonId: "l5" });

    console.log("\nScheduling from login merge");
    r = await call("POST", "/auth/learn/progress/merge", {
      token,
      body: {
        courses: {
          [COURSE_B]: {
            engagementMap: {
              v1: { quizAttempts: { 1: { selectedIndex: 3, correct: false } } },
            },
          },
        },
      },
    });
    check(r.status === 200 && !r.json?.results?.[COURSE_B]?.error, "merge saved", r);
    check(
      (await ReviewItem.countDocuments({ userId: user._id, courseId: COURSE_B, lessonId: "v1" })) === 1,
      "wrong answer from merge becomes a review item",
    );

    console.log("\nDue list and count");
    await makeDue(user._id);
    r = await call("GET", "/auth/learn/review/count", { token });
    check(r.json?.count === 2, "both items due → count 2", r.json);
    r = await call("GET", `/auth/learn/review/count?courseId=${COURSE_A}`, { token });
    check(r.json?.count === 1, "course filter on count", r.json);
    r = await call("GET", `/auth/learn/review/count?courseId=${COURSE_A},${COURSE_B}`, { token });
    check(r.json?.count === 2, "comma-separated course filter", r.json);
    r = await call("GET", `/auth/learn/review/count?courseId=${COURSE_A},nope`, { token });
    check(r.status === 400, "unknown id in the list → 400", r.status);
    r = await call("GET", `/auth/learn/review/due?courseId=${COURSE_A}`, { token });
    const itemA = r.json?.items?.[0];
    check(
      r.status === 200 &&
        r.json.items.length === 1 &&
        itemA.courseId === COURSE_A &&
        itemA.lessonId === "l1" &&
        itemA.quizIndex === "0" &&
        itemA.questionHash === "q-abc123" &&
        r.json.intervalDays === 7,
      "course filter on due list",
      r.json,
    );
    check(!("userId" in itemA), "no userId in the response", Object.keys(itemA));
    r = await call("GET", "/auth/learn/review/due?courseId=not-a-course", { token });
    check(r.status === 400, "unknown courseId → 400", r.status);
    r = await call("GET", "/auth/learn/review/count", { token: otherToken });
    check(r.json?.count === 0, "other learner sees none of them", r.json);

    console.log("\nAnswering");
    const answerRoute = `/auth/learn/review/${itemA.id}/answer`;
    r = await call("POST", answerRoute, { token, body: {} });
    check(r.status === 400, "missing correct → 400", r.status);
    r = await call("POST", "/auth/learn/review/123/answer", { token, body: { correct: true } });
    check(r.status === 400, "malformed id → 400", r.status);
    r = await call("POST", answerRoute, { token: otherToken, body: { correct: true } });
    check(r.status === 404, "another learner's item → 404", r.status);

    r = await call("POST", answerRoute, { token, body: { correct: false } });
    const backIn = (new Date(r.json?.item?.nextReviewAt) - Date.now()) / DAY_MS;
    check(
      r.status === 200 && r.json.accepted === true && r.json.item.reviewCount === 1 && r.json.item.lastResult === false,
      "wrong review answer accepted",
      r.json,
    );
    check(Math.abs(backIn - 7) < 0.01, "wrong answer → back in 7 days", backIn);
    r = await call("POST", answerRoute, { token, body: { correct: true } });
    check(r.json?.accepted === false && r.json.item.reviewCount === 1, "not due again → ignored", r.json);
    r = await call("GET", "/auth/learn/review/count", { token });
    check(r.json?.count === 1, "count drops to 1", r.json);

    await makeDue(user._id, { lessonId: "l1" });
    r = await call("POST", answerRoute, { token, body: { correct: true } });
    check(
      r.json?.accepted === true && r.json.item.resolvedAt && r.json.item.reviewCount === 2,
      "right review answer resolves it",
      r.json,
    );
    await makeDue(user._id, { lessonId: "l1" });
    r = await call("POST", answerRoute, { token, body: { correct: false } });
    check(r.json?.accepted === false, "resolved item can't be answered again", r.json);
    r = await call("GET", `/auth/learn/review/count?courseId=${COURSE_A}`, { token });
    check(r.json?.count === 0, "resolved item leaves the count", r.json);

    console.log("\nDismissing a changed question");
    r = await call("GET", `/auth/learn/review/due?courseId=${COURSE_B}`, { token });
    const itemB = r.json?.items?.[0];
    check(itemB?.questionHash === "", "answer saved without a fingerprint → empty hash", itemB);
    r = await call("POST", `/auth/learn/review/${itemB.id}/dismiss`, { token: otherToken });
    check(r.status === 404, "another learner's item → 404", r.status);
    r = await call("POST", `/auth/learn/review/${itemB.id}/dismiss`, { token });
    check(
      r.json?.accepted === true && r.json.item.dismissedAt && r.json.item.resolvedAt,
      "dismiss resolves it",
      r.json,
    );
    r = await call("POST", `/auth/learn/review/${itemB.id}/dismiss`, { token });
    check(r.json?.accepted === false, "dismiss twice → ignored", r.json);
    r = await call("GET", "/auth/learn/review/count", { token });
    check(r.json?.count === 0, "nothing left due", r.json);

    console.log("\nFirst attempt untouched");
    r = await call("GET", `/auth/learn/${COURSE_A}/progress`, { token });
    const row = r.json?.progress?.lessonEngagement?.find((x) => x.lessonId === "l1");
    check(
      row?.quizAttempts?.["0"]?.selectedIndex === 2 &&
        row.quizAttempts["0"].correct === false &&
        row.quizAttempts["0"].questionHash === "q-abc123",
      "lesson still has the original wrong first answer",
      row?.quizAttempts,
    );

    console.log(`\nSmoke review: PASS (${passed} checks)`);
  } finally {
    const ids = [user._id, other._id];
    await ReviewItem.deleteMany({ userId: { $in: ids } });
    await LearnerProgress.deleteMany({ userId: { $in: ids } });
    await User.deleteMany({ _id: { $in: ids } });
    console.log("Cleaned smoke users, learner_progress and review items");
    await mongoose.connection.close();
  }
}

main().catch(async (err) => {
  console.error("\nSmoke review: FAIL", err.message);
  try {
    await mongoose.connection.close();
  } catch {
    /* ignore */
  }
  process.exit(1);
});
