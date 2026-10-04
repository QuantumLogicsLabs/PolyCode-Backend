const Certificate = require("../models/Certificate");
const CERTIFICATE_COURSES = require("../data/courseLessons.json");
const User = require("../../auth/models/User");
const { assertCourseId } = require("../../auth/constants/courseIds");
const { findLearnerDoc } = require("../../auth/services/learnerProgressStore");

const UUID_RE = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;

function httpError(statusCode, message, extra = {}) {
  const err = new Error(message);
  err.statusCode = statusCode;
  Object.assign(err, extra);
  return err;
}

/** Fields anyone may see; the same data the verify page shows. */
function toPublicCertificate(doc) {
  return {
    id: doc.certificateId,
    recipientName: doc.recipientName,
    courseId: doc.courseId,
    courseName: doc.courseName,
    issuedAt: doc.issuedAt,
    lessonsCompleted: doc.lessonsCompleted,
    xp: doc.xp,
  };
}

function recipientNameFor(user) {
  const name = String(user.name || "").trim().replace(/\s+/g, " ");
  return name || user.username;
}

/**
 * Return the learner's certificate for courseId, issuing it if they have
 * completed every lesson of the course. Fails closed: a course without a
 * lesson list never gets a certificate.
 */
async function issueCertificate(userId, rawCourseId) {
  const courseId = assertCourseId(rawCourseId);

  const existing = await Certificate.findOne({ userId, courseId }).lean();
  if (existing) return { certificate: toPublicCertificate(existing), created: false };

  const course = CERTIFICATE_COURSES[courseId];
  if (!course) {
    throw httpError(400, "Certificates are not available for this course");
  }

  const user = await User.findById(userId).select("name username").lean();
  if (!user) throw httpError(401, "User not found");

  const learner = await findLearnerDoc(userId);
  const entry = (learner?.courses || []).find((c) => c.courseId === courseId);
  const done = new Set((entry?.completedLessons || []).map((l) => l.lessonId));
  const completed = course.lessonIds.filter((id) => done.has(id)).length;
  const required = course.lessonIds.length;
  if (completed < required) {
    throw httpError(403, "Course is not complete yet", {
      code: "COURSE_INCOMPLETE",
      completed,
      required,
    });
  }

  try {
    const created = await Certificate.create({
      userId,
      courseId,
      courseName: course.name,
      recipientName: recipientNameFor(user),
      lessonsCompleted: required,
      xp: course.totalXp,
    });
    return { certificate: toPublicCertificate(created), created: true };
  } catch (error) {
    // Two requests raced; the other one issued it first.
    if (error?.code === 11000) {
      const raced = await Certificate.findOne({ userId, courseId }).lean();
      if (raced) return { certificate: toPublicCertificate(raced), created: false };
    }
    throw error;
  }
}

async function getCertificate(certificateId) {
  const id = String(certificateId || "").trim().toLowerCase();
  if (!UUID_RE.test(id)) return null;
  const doc = await Certificate.findOne({ certificateId: id }).lean();
  return doc ? toPublicCertificate(doc) : null;
}

/** Issued certificates for a public profile, newest first; null if no such user. */
async function listCertificatesForUsername(rawUsername) {
  const username = String(rawUsername || "").trim().toLowerCase();
  const user = await User.findOne({ username, isActive: { $ne: false } })
    .select("_id")
    .lean();
  if (!user) return null;
  const docs = await Certificate.find({ userId: user._id }).sort({ issuedAt: -1 }).lean();
  return docs.map(toPublicCertificate);
}

module.exports = {
  issueCertificate,
  getCertificate,
  listCertificatesForUsername,
};
