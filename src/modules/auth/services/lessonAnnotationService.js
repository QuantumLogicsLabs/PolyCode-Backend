const { assertCourseId } = require("../constants/courseIds");
const {
  findLearnerDoc,
  getOrCreateLearnerDoc,
  saveLearnerDoc,
} = require("./learnerProgressStore");
const { KEY_RE: IMAGE_KEY_RE } = require("./annotationImageService");

const MAX_PAYLOAD_BYTES = 100 * 1024;
const MAX_IMAGES = 50;

function estimateSize(payload) {
  return Buffer.byteLength(JSON.stringify(payload || {}), "utf8");
}

function downsampleStroke(stroke, stride = 2) {
  const points = Array.isArray(stroke?.points) ? stroke.points : [];
  if (points.length <= 40 || stride <= 1) {
    return {
      color: stroke?.color || "",
      width: Number(stroke?.width) || 3,
      points,
    };
  }
  const next = [];
  for (let index = 0; index < points.length; index += stride) {
    next.push(points[index]);
  }
  const last = points[points.length - 1];
  const prev = next[next.length - 1];
  if (!prev || prev[0] !== last[0] || prev[1] !== last[1]) {
    next.push(last);
  }
  return {
    color: stroke?.color || "",
    width: Number(stroke?.width) || 3,
    points: next,
  };
}

function sanitizeAnnotation(raw = {}) {
  const strokes = Array.isArray(raw.strokes)
    ? raw.strokes.map((stroke) => downsampleStroke(stroke, 1))
    : [];
  const labels = Array.isArray(raw.labels)
    ? raw.labels.map((label, index) => ({
        id: String(label?.id || `label-${index}`),
        x: Number(label?.x) || 0,
        y: Number(label?.y) || 0,
        text: String(label?.text || "").slice(0, 2000),
      }))
    : [];
  // Only uploaded images (imageId) are stored; inline data URLs stay local.
  const images = Array.isArray(raw.images)
    ? raw.images
        .filter((image) => IMAGE_KEY_RE.test(String(image?.imageId || "")))
        .slice(0, MAX_IMAGES)
        .map((image, index) => ({
          id: String(image.id || `image-${index}`).slice(0, 64),
          x: Number(image.x) || 0,
          y: Number(image.y) || 0,
          width: Math.min(4000, Math.max(24, Number(image.width) || 240)),
          height: Math.min(4000, Math.max(24, Number(image.height) || 180)),
          imageId: String(image.imageId),
        }))
    : [];
  return { strokes, labels, images };
}

function fitWithinLimit(payload) {
  let current = sanitizeAnnotation(payload);
  let size = estimateSize(current);
  if (size <= MAX_PAYLOAD_BYTES) {
    return { data: current, size, downsampled: false };
  }

  let stride = 2;
  while (size > MAX_PAYLOAD_BYTES && stride <= 16) {
    current = {
      strokes: (payload.strokes || []).map((stroke) =>
        downsampleStroke(stroke, stride),
      ),
      labels: current.labels,
      images: current.images,
    };
    size = estimateSize(current);
    stride *= 2;
  }

  if (size > MAX_PAYLOAD_BYTES) {
    const error = new Error(
      `Annotation payload exceeds ${MAX_PAYLOAD_BYTES} bytes even after downsampling`,
    );
    error.statusCode = 413;
    throw error;
  }

  return { data: current, size, downsampled: true };
}

function normalizeTab(tab) {
  return tab === "challenge" ? "challenge" : "theory";
}

function findAnnotation(learner, courseId, lessonId, tab) {
  return (learner.annotations || []).find(
    (row) =>
      row.courseId === courseId &&
      row.lessonId === lessonId &&
      row.tab === tab,
  );
}

async function getAnnotation(userId, courseId, lessonId, tab = "theory") {
  const id = assertCourseId(courseId);
  const cleanLessonId = String(lessonId || "").trim();
  const cleanTab = normalizeTab(tab);
  const learner = await findLearnerDoc(userId);
  const doc = learner
    ? findAnnotation(learner, id, cleanLessonId, cleanTab)
    : null;

  if (!doc) {
    return {
      courseId: id,
      lessonId: cleanLessonId,
      tab: cleanTab,
      strokes: [],
      labels: [],
      images: [],
      updatedAt: null,
    };
  }

  return {
    courseId: doc.courseId,
    lessonId: doc.lessonId,
    tab: doc.tab,
    strokes: doc.strokes || [],
    labels: doc.labels || [],
    images: doc.images || [],
    updatedAt: doc.updatedAt || null,
  };
}

async function putAnnotation(userId, courseId, lessonId, tab, payload = {}) {
  const id = assertCourseId(courseId);
  const cleanLessonId = String(lessonId || "").trim();
  if (!cleanLessonId) {
    const error = new Error("lessonId is required");
    error.statusCode = 400;
    throw error;
  }

  const cleanTab = normalizeTab(tab);
  const fitted = fitWithinLimit(payload);
  const learner = await getOrCreateLearnerDoc(userId);
  let doc = findAnnotation(learner, id, cleanLessonId, cleanTab);

  if (!doc) {
    learner.annotations.push({
      courseId: id,
      lessonId: cleanLessonId,
      tab: cleanTab,
      strokes: fitted.data.strokes,
      labels: fitted.data.labels,
      images: fitted.data.images,
      updatedAt: new Date(),
    });
    doc = learner.annotations[learner.annotations.length - 1];
  } else {
    doc.strokes = fitted.data.strokes;
    doc.labels = fitted.data.labels;
    doc.images = fitted.data.images;
    doc.updatedAt = new Date();
  }

  await saveLearnerDoc(learner);

  return {
    courseId: doc.courseId,
    lessonId: doc.lessonId,
    tab: doc.tab,
    strokes: doc.strokes || [],
    labels: doc.labels || [],
    images: doc.images || [],
    updatedAt: doc.updatedAt || null,
    downsampled: fitted.downsampled,
    size: fitted.size,
  };
}

async function mergeLocalAnnotations(userId, items = []) {
  const results = [];
  for (const item of items) {
    try {
      const saved = await putAnnotation(
        userId,
        item.courseId,
        item.lessonId,
        item.tab,
        { strokes: item.strokes, labels: item.labels, images: item.images },
      );
      results.push({ ok: true, ...saved });
    } catch (error) {
      results.push({
        ok: false,
        courseId: item.courseId,
        lessonId: item.lessonId,
        tab: item.tab,
        error: error.message,
      });
    }
  }
  return results;
}

module.exports = {
  getAnnotation,
  putAnnotation,
  mergeLocalAnnotations,
  MAX_PAYLOAD_BYTES,
};
