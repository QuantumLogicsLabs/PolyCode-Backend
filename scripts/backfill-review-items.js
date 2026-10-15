/**
 * Adds review items for wrong first MCQ attempts saved before Review existed.
 * Usage: node scripts/backfill-review-items.js [--dry-run]
 *
 * Safe to run more than once: existing review items are never changed.
 * An old miss gets nextReviewAt = answeredAt + 7 days, so it may be due now.
 */
require("dotenv").config({ path: require("path").join(__dirname, "..", ".env") });

const mongoose = require("mongoose");
const { connectToMongoDB, getMongoUri } = require("../src/config/database");
const LearnerProgress = require("../src/modules/auth/models/LearnerProgress");
const ReviewItem = require("../src/modules/auth/models/ReviewItem");
const reviewService = require("../src/modules/auth/services/reviewService");

const DRY_RUN = process.argv.includes("--dry-run");

async function main() {
  if (!getMongoUri()) throw new Error("No MongoDB URI configured in backend/.env");
  if (!(await connectToMongoDB())) throw new Error("Could not connect to MongoDB");

  // The unique index stops duplicates, so build it before any upserts.
  if (!DRY_RUN) await ReviewItem.syncIndexes();

  let learners = 0;
  let missed = 0;
  let added = 0;

  const cursor = LearnerProgress.find({}, { userId: 1, courses: 1 }).lean().cursor();
  for await (const learner of cursor) {
    learners += 1;
    for (const course of learner.courses || []) {
      const rows = course.lessonEngagement || [];
      const count = reviewService.findMissedQuizzes(rows).length;
      if (count === 0) continue;
      missed += count;
      if (!DRY_RUN) {
        added += await reviewService.scheduleMissedQuizzes(
          learner.userId,
          course.courseId,
          rows,
        );
      }
    }
  }

  console.log(`Learners scanned: ${learners}`);
  console.log(`Wrong first attempts found: ${missed}`);
  console.log(DRY_RUN ? "Dry run: nothing written" : `Review items added: ${added}`);
  await mongoose.connection.close();
}

main().catch(async (err) => {
  console.error("Backfill review items: FAIL", err.message);
  try {
    await mongoose.connection.close();
  } catch {
    /* ignore */
  }
  process.exit(1);
});
