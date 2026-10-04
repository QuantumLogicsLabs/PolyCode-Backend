/**
 * Smoke-test the certificate endpoints against a running backend.
 * Usage: node scripts/smoke-certificates.js [apiBaseUrl]
 *        (default http://localhost:5000/api — start it with `npm start` first)
 *
 * Uses backend/.env (MONGODB_URI, JWT_SECRET) to create a temp smoke user and
 * set up lesson progress directly; everything else goes through HTTP.
 * Cleans up the user, its learner_progress and its certificates afterwards.
 */
require("dotenv").config({ path: require("path").join(__dirname, "..", ".env") });

const mongoose = require("mongoose");
const { connectToMongoDB, getMongoUri } = require("../src/config/database");
const User = require("../src/modules/auth/models/User");
const LearnerProgress = require("../src/modules/auth/models/LearnerProgress");
const Certificate = require("../src/modules/certificates/models/Certificate");
const courseProgressService = require("../src/modules/auth/services/courseProgressService");
const COURSES = require("../src/modules/certificates/data/courseLessons.json");
const { signAccessToken } = require("../src/utils/jwt");

const API = (process.argv[2] || "http://localhost:5000/api").replace(/\/$/, "");
const COURSE_A = "sql-indexes";
const COURSE_B = "sql-projects";
const UUID_RE = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/;

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

async function completeLessons(userId, courseId, lessonIds) {
  for (const lessonId of lessonIds) {
    // Inflated xp: the certificate must use curriculum XP, not this.
    await courseProgressService.completeLesson(userId, courseId, { lessonId, xp: 9999 });
  }
}

async function main() {
  if (!getMongoUri()) throw new Error("No MongoDB URI configured in backend/.env");
  if (!(await connectToMongoDB())) throw new Error("Could not connect to MongoDB");

  const stamp = Date.now().toString(36);
  const user = await User.create({
    email: `smoke-certificates-${stamp}@example.com`,
    username: `smoke_cert_${stamp}`,
    password: "SmokeTest123!",
    name: "  Smoke   Learner ",
  });
  const token = signAccessToken(String(user._id));
  console.log(`Created smoke user ${user.username} → ${API}`);

  try {
    const a = COURSES[COURSE_A];
    const b = COURSES[COURSE_B];

    console.log("\nIssue: POST /certificates");
    let r = await call("POST", "/certificates", { body: { courseId: COURSE_A } });
    check(r.status === 401, "signed out → 401", r.status);

    r = await call("POST", "/certificates", { token, body: { courseId: "not-a-course" } });
    check(r.status === 400, "unknown courseId → 400", r.status);

    r = await call("POST", "/certificates", { token, body: {} });
    check(r.status === 400, "missing courseId → 400", r.status);

    r = await call("POST", "/certificates", { token, body: { courseId: COURSE_A } });
    check(
      r.status === 403 && r.json?.code === "COURSE_INCOMPLETE" && r.json.completed === 0,
      "no progress → 403 COURSE_INCOMPLETE, completed 0",
      r,
    );

    await completeLessons(user._id, COURSE_A, a.lessonIds.slice(0, -1));
    await completeLessons(user._id, COURSE_A, ["made-up-lesson-1", "made-up-lesson-2"]);
    r = await call("POST", "/certificates", { token, body: { courseId: COURSE_A } });
    check(
      r.status === 403 &&
        r.json?.completed === a.lessonIds.length - 1 &&
        r.json.required === a.lessonIds.length,
      "all but one lesson (plus made-up ids) → 403 with counts",
      r,
    );

    await completeLessons(user._id, COURSE_A, a.lessonIds.slice(-1));
    r = await call("POST", "/certificates", {
      token,
      body: { courseId: COURSE_A, recipientName: "Someone Else", xp: 1, lessonsCompleted: 1 },
    });
    const certA = r.json?.certificate;
    check(r.status === 201 && certA, "complete → 201 issued", r);
    check(UUID_RE.test(certA.id), "id is a random UUID", certA.id);
    check(certA.recipientName === "Smoke Learner", "name from user record, not body", certA.recipientName);
    check(certA.courseName === a.name && certA.courseId === COURSE_A, "course name from server list", certA);
    check(certA.lessonsCompleted === a.lessonIds.length, "lessonsCompleted = course lessons", certA.lessonsCompleted);
    check(certA.xp === a.totalXp, `xp = curriculum total (${a.totalXp}), not stored 9999s`, certA.xp);
    check(!("userId" in certA), "no userId in the response", Object.keys(certA));

    r = await call("POST", "/certificates", { token, body: { courseId: COURSE_A } });
    check(r.status === 200 && r.json?.certificate?.id === certA.id, "issue again → 200, same certificate", r);

    await completeLessons(user._id, COURSE_B, b.lessonIds);
    const racers = await Promise.all(
      Array.from({ length: 5 }, () =>
        call("POST", "/certificates", { token, body: { courseId: COURSE_B } }),
      ),
    );
    const raceIds = new Set(racers.map((x) => x.json?.certificate?.id));
    check(
      racers.every((x) => x.status === 200 || x.status === 201) && raceIds.size === 1,
      "5 parallel issues → one certificate",
      racers.map((x) => [x.status, x.json?.certificate?.id]),
    );
    check(
      (await Certificate.countDocuments({ userId: user._id, courseId: COURSE_B })) === 1,
      "one stored document for the course",
    );

    console.log("\nVerify: GET /certificates/:id");
    r = await call("GET", `/certificates/${certA.id}`);
    check(
      r.status === 200 && JSON.stringify(r.json?.certificate) === JSON.stringify(certA),
      "known id → 200 with stored details",
      r,
    );
    r = await call("GET", `/certificates/${certA.id}?name=Fake&course=Fake&xp=1`);
    check(r.json?.certificate?.recipientName === "Smoke Learner", "query parameters ignored", r);
    r = await call("GET", `/certificates/${certA.id.toUpperCase()}`);
    check(r.status === 200 && r.json?.certificate?.id === certA.id, "id is case-insensitive", r.status);
    r = await call("GET", "/certificates/00000000-0000-4000-8000-000000000000");
    check(r.status === 404, "unknown UUID → 404", r.status);
    r = await call("GET", "/certificates/123");
    check(r.status === 404, "malformed id → 404", r.status);

    await User.updateOne({ _id: user._id }, { name: "Renamed Learner" });
    r = await call("GET", `/certificates/${certA.id}`);
    check(r.json?.certificate?.recipientName === "Smoke Learner", "name is fixed at issue time", r.json);

    console.log("\nProfile: GET /certificates/user/:username");
    r = await call("GET", `/certificates/user/${user.username.toUpperCase()}`);
    check(
      r.status === 200 &&
        r.json?.certificates?.length === 2 &&
        r.json.certificates.map((c) => c.courseId).sort().join() === [COURSE_A, COURSE_B].sort().join(),
      "lists both issued certificates",
      r,
    );
    r = await call("GET", `/certificates/user/no_such_user_${stamp}`);
    check(r.status === 404, "unknown username → 404", r.status);

    console.log(`\nSmoke certificates: PASS (${passed} checks)`);
  } finally {
    await Certificate.deleteMany({ userId: user._id });
    await LearnerProgress.deleteOne({ userId: user._id });
    await User.deleteOne({ _id: user._id });
    console.log("Cleaned smoke user, learner_progress and certificates");
    await mongoose.connection.close();
  }
}

main().catch(async (err) => {
  console.error("\nSmoke certificates: FAIL", err.message);
  try {
    await mongoose.connection.close();
  } catch {
    /* ignore */
  }
  process.exit(1);
});
