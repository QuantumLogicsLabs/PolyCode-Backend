/** Allowlisted learn course IDs (must match frontend recordLessonXp course strings). */
const COURSE_IDS = [
  "oops-cpp",
  "pointers-cpp",
  "numpy-py",
  "pandas-py",
  "fastapi-py",
  "pytorch-py",
  "huggingface-py",
  "opencv-py",
  "python-fundamentals",
  "python-oop-py",
  "python-file-handling-py",
  "matplotlib-py",
  "ai-ml-py",
  "cpp-fundamentals",
  "dsa-cpp",
  "cpp-data-structures",
  "c-fundamentals",
  "c-functions",
  "c-pointers",
  "c-data-structures",
  "c-memory-management",
  "c-file-handling",
  "c-projects",
  "js-fundamentals",
  "js-dom",
  "js-web-dev",
  "js-oops",
  "node-npm",
  "html-css-foundation",
  "php-fundamentals",
  "ruby-fundamentals",
  "ruby-gems",
  "csharp-fundamentals",
  "sql-fundamentals",
  "sql-queries",
  "sql-joins",
  "sql-subqueries",
  "sql-aggregate-functions",
  "sql-views",
  "sql-indexes",
  "sql-stored-procedures",
  "sql-projects",
  "go-fundamentals",
  "rust-fundamentals",
  "java-fundamentals",
  "java-intermediate",
  "java-advanced",
  "java-collections",
  "java-exception",
  "java-multithreading",
  "java-jdbc",
  "java-spring-boot",
  "java-projects",
  "software-engineering-fundamentals",
];

const COURSE_ID_SET = new Set(COURSE_IDS);

function isValidCourseId(courseId) {
  return COURSE_ID_SET.has(String(courseId || "").trim());
}

function assertCourseId(courseId) {
  const id = String(courseId || "").trim();
  if (!isValidCourseId(id)) {
    const err = new Error(`Unknown courseId: ${courseId}`);
    err.statusCode = 400;
    throw err;
  }
  return id;
}

module.exports = {
  COURSE_IDS,
  COURSE_ID_SET,
  isValidCourseId,
  assertCourseId,
};
