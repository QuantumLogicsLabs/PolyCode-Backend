/**
 * Guard against the certificate lesson lists drifting from the frontend.
 * Usage: node scripts/check-certificate-lessons.js
 *
 * src/modules/certificates/data/courseLessons.json is generated in the
 * frontend repo (scripts/generate-certificate-courses.mjs) and copied here.
 * The server only issues a certificate when every lesson listed for the course
 * is complete, so a stale list either blocks honest learners (lesson added) or
 * lets a course count as complete too early (lesson removed).
 *
 * Fails when:
 *   - a course in the list is not in COURSE_IDS, or
 *   - the list differs from the frontend's scripts/certificates/courseLessons.json.
 * Regenerate with:
 *   node scripts/generate-certificate-courses.mjs --backend ../PolyCode-Backend
 * (run in PolyCode-Frontend).
 */
const fs = require("fs");
const path = require("path");

const { COURSE_ID_SET } = require("../src/modules/auth/constants/courseIds");

const BACKEND_FILE = path.resolve(
  __dirname, "..", "src", "modules", "certificates", "data", "courseLessons.json",
);
const FRONTEND_FILE = path.resolve(
  __dirname, "..", "..", "PolyCode-Frontend", "scripts", "certificates", "courseLessons.json",
);

function main() {
  const backend = JSON.parse(fs.readFileSync(BACKEND_FILE, "utf8"));
  let failed = false;

  const unknown = Object.keys(backend).filter((id) => !COURSE_ID_SET.has(id));
  if (unknown.length) {
    failed = true;
    console.error(`\nIn courseLessons.json but NOT in COURSE_IDS (${unknown.length}):`);
    unknown.forEach((id) => console.error(`  ${id}`));
  }

  if (!fs.existsSync(FRONTEND_FILE)) {
    console.warn(`\nFrontend list not found at ${FRONTEND_FILE} — skipped the drift check.`);
  } else {
    const frontend = JSON.parse(fs.readFileSync(FRONTEND_FILE, "utf8"));
    const ids = [...new Set([...Object.keys(backend), ...Object.keys(frontend)])].sort();
    const differ = ids.filter(
      (id) => JSON.stringify(backend[id]) !== JSON.stringify(frontend[id]),
    );
    if (differ.length) {
      failed = true;
      console.error(`\nDiffers from the frontend list (${differ.length}):`);
      differ.forEach((id) => {
        const where = !backend[id] ? "missing here" : !frontend[id] ? "not in frontend" : "changed";
        console.error(`  ${id}  (${where})`);
      });
    }
  }

  if (failed) {
    console.error("\nFAIL: certificate lesson lists are out of sync.");
    process.exit(1);
  }

  const lessons = Object.values(backend).reduce((n, c) => n + c.lessonIds.length, 0);
  console.log(`OK: ${Object.keys(backend).length} courses, ${lessons} lessons, in sync.`);
}

main();
