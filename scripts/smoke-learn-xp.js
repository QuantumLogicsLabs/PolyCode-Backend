/**
 * Smoke-test completeLesson persistence for the given courses.
 * Usage: node scripts/smoke-learn-xp.js [courseId ...]
 *
 * Requires backend/.env with MONGODB_URI (and creates a temp smoke user).
 */
require("dotenv").config({ path: require("path").join(__dirname, "..", ".env") });

const mongoose = require("mongoose");
const { connectToMongoDB, getMongoUri } = require("../src/config/database");
const User = require("../src/modules/auth/models/User");
const LearnerProgress = require("../src/modules/auth/models/LearnerProgress");
const courseProgressService = require("../src/modules/auth/services/courseProgressService");

const SMOKE_EMAIL = `smoke-learn-xp-${Date.now()}@example.com`;
const DEFAULT_COURSES = ["pointers-cpp", "java-spring-boot"];
const COURSES = process.argv.slice(2).length
  ? process.argv.slice(2)
  : DEFAULT_COURSES;

async function main() {
  const uri = getMongoUri();
  if (!uri) {
    throw new Error("No MongoDB URI configured in backend/.env");
  }
  const conn = await connectToMongoDB();
  if (!conn) throw new Error("Could not connect to MongoDB");

  const user = await User.create({
    email: SMOKE_EMAIL,
    username: `smoke_${Date.now().toString(36)}`,
    password: "SmokeTest123!",
    name: "Smoke Learner",
  });

  console.log(`Created smoke user ${user._id} (${SMOKE_EMAIL})`);

  try {
    for (const courseId of COURSES) {
      const before = await LearnerProgress.findOne({ userId: user._id }).lean();
      console.log(
        `\n[${courseId}] learner_progress before: ${before ? "exists" : "missing"}`,
      );

      const progress = await courseProgressService.completeLesson(
        user._id,
        courseId,
        {
          lessonId: `${courseId}-smoke-l1`,
          title: "Smoke lesson 1",
          chapterId: "ch1",
          chapterTitle: "Chapter 1",
          xp: 25,
        },
      );

      const doc = await LearnerProgress.findOne({ userId: user._id }).lean();
      if (!doc) throw new Error("learner_progress was not created");

      const course = (doc.courses || []).find((c) => c.courseId === courseId);
      if (!course) throw new Error(`course ${courseId} missing from doc`);
      if (!course.completedLessons?.length) {
        throw new Error("completedLessons empty");
      }
      if (!(course.totalXp >= 25)) {
        throw new Error(`totalXp expected >= 25, got ${course.totalXp}`);
      }
      if (!(doc.dailyXp?.totalXp >= 25)) {
        throw new Error(
          `dailyXp.totalXp expected >= 25, got ${doc.dailyXp?.totalXp}`,
        );
      }
      if (!(doc.dailyXp?.days?.length > 0)) {
        throw new Error("dailyXp.days empty");
      }

      console.log(
        `[${courseId}] OK completed=${course.completedLessons.length} totalXp=${course.totalXp} dailyXp=${doc.dailyXp.totalXp} days=${doc.dailyXp.days.length}`,
      );
      console.log(`[${courseId}] API progress.totalXp=${progress.totalXp}`);
    }
    console.log("\nSmoke XP persistence: PASS");
  } finally {
    await LearnerProgress.deleteOne({ userId: user._id });
    await User.deleteOne({ _id: user._id });
    console.log("Cleaned smoke user + learner_progress");
    await mongoose.connection.close();
  }
}

main().catch(async (err) => {
  console.error("Smoke XP persistence: FAIL", err);
  try {
    await mongoose.connection.close();
  } catch {
    /* ignore */
  }
  process.exit(1);
});
