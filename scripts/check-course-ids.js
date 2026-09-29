/**
 * Guard against COURSE_IDS drifting from the course IDs the frontend uses.
 * Usage: node scripts/check-course-ids.js
 *
 * Source of truth is the union of:
 *   - the frontend course registry (shared/courseRegistry.js), and
 *   - every `useCourseProgress({ courseId: "..." })` call under frontend src/.
 * Some courses call the hook without being in the registry, so reading the
 * registry alone misses them. Any ID missing from COURSE_IDS makes
 * completeCourseLesson() return 400, which silently drops server-side
 * progress and all XP for that course.
 */
const fs = require("fs");
const path = require("path");

const { COURSE_IDS } = require("../src/modules/auth/constants/courseIds");

const FRONTEND_SRC = path.resolve(__dirname, "..", "..", "PolyCode-Frontend", "src");
const LEARN_SHARED = path.join(FRONTEND_SRC, "features", "learn", "shared");
const REGISTRY_PATH = path.join(LEARN_SHARED, "courseRegistry.js");
const HOOK_DEFINITION_PATH = path.join(LEARN_SHARED, "useCourseProgress.js");
const SOURCE_EXTENSIONS = new Set([".js", ".jsx", ".ts", ".tsx"]);

function readRegistryIds(file) {
  const src = fs.readFileSync(file, "utf8");
  const ids = [...src.matchAll(/courseId:\s*["']([^"']+)["']/g)].map((m) => m[1]);
  if (!ids.length) {
    throw new Error(`No courseId entries found in ${file}`);
  }
  return ids;
}

function listSourceFiles(dir) {
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap((entry) => {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      return entry.name === "node_modules" ? [] : listSourceFiles(full);
    }
    return SOURCE_EXTENSIONS.has(path.extname(entry.name)) ? [full] : [];
  });
}

/** Return the argument text of the call whose "(" is at openIndex. */
function callArguments(src, openIndex) {
  let depth = 0;
  for (let i = openIndex; i < src.length; i++) {
    if (src[i] === "(") depth++;
    else if (src[i] === ")" && --depth === 0) return src.slice(openIndex + 1, i);
  }
  return src.slice(openIndex + 1);
}

/**
 * Collect courseId literals passed to useCourseProgress() across frontend src/.
 * Calls without a literal courseId can't be checked and are returned separately.
 */
function readHookIds(srcDir) {
  const ids = new Map(); // courseId -> [relative file paths]
  const unresolved = [];

  for (const file of listSourceFiles(srcDir)) {
    if (path.resolve(file) === HOOK_DEFINITION_PATH) continue;
    const src = fs.readFileSync(file, "utf8");
    const rel = path.relative(srcDir, file).split(path.sep).join("/");

    for (const call of src.matchAll(/\buseCourseProgress\s*\(/g)) {
      const args = callArguments(src, call.index + call[0].length - 1);
      const match = args.match(/courseId:\s*["']([^"']+)["']/);
      if (!match) {
        unresolved.push(rel);
        continue;
      }
      ids.set(match[1], [...(ids.get(match[1]) || []), rel]);
    }
  }

  return { ids, unresolved };
}

function main() {
  if (!fs.existsSync(REGISTRY_PATH)) {
    console.error(`Frontend registry not found at ${REGISTRY_PATH}`);
    console.error("Check out PolyCode-Frontend beside PolyCode-Backend and re-run.");
    process.exit(2);
  }

  const registryIds = readRegistryIds(REGISTRY_PATH);
  const registrySet = new Set(registryIds);
  const { ids: hookIds, unresolved } = readHookIds(FRONTEND_SRC);
  const hookOnly = [...hookIds.keys()].filter((id) => !registrySet.has(id));
  const frontendIds = [...registryIds, ...hookOnly];
  const frontendSet = new Set(frontendIds);
  const allowed = new Set(COURSE_IDS);

  const missing = frontendIds.filter((id) => !allowed.has(id));
  const extra = COURSE_IDS.filter((id) => !frontendSet.has(id));
  const duplicates = COURSE_IDS.filter((id, i) => COURSE_IDS.indexOf(id) !== i);

  console.log(`Frontend registry: ${registryIds.length} courses`);
  console.log(`useCourseProgress() callers: ${hookIds.size} courses`);
  console.log(`Frontend total (union): ${frontendSet.size} courses`);
  console.log(`Backend COURSE_IDS: ${COURSE_IDS.length} courses`);

  if (hookOnly.length) {
    console.log(
      `\nUsed by useCourseProgress() but not in courseRegistry.js (${hookOnly.length}):`
    );
    hookOnly.forEach((id) => console.log(`  ${id}  (${hookIds.get(id).join(", ")})`));
  }

  if (unresolved.length) {
    console.warn(
      `\nuseCourseProgress() calls without a literal courseId (${unresolved.length}) —` +
        " not checked, verify by hand:"
    );
    unresolved.forEach((file) => console.warn(`  ${file}`));
  }

  if (duplicates.length) {
    console.error(`\nDuplicate IDs in COURSE_IDS (${duplicates.length}):`);
    duplicates.forEach((id) => console.error(`  ${id}`));
  }

  if (missing.length) {
    console.error(
      `\nUsed by the frontend but NOT in COURSE_IDS (${missing.length}) —` +
        " lesson completion 400s and no XP is awarded for these:"
    );
    missing.forEach((id) => console.error(`  ${id}`));
  }

  if (extra.length) {
    console.warn(
      `\nIn COURSE_IDS but not used by the frontend (${extra.length}) —` +
        " stale entries, safe but worth pruning:"
    );
    extra.forEach((id) => console.warn(`  ${id}`));
  }

  if (missing.length || duplicates.length) {
    console.error("\nFAIL: COURSE_IDS is out of sync with the frontend.");
    process.exit(1);
  }

  console.log(
    `\nOK: all ${frontendSet.size} frontend course IDs are allowlisted` +
      ` (${COURSE_IDS.length - extra.length}/${frontendSet.size}).`
  );
}

main();
