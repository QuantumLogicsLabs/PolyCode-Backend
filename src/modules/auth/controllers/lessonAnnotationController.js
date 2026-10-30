const lessonAnnotation = require("../services/lessonAnnotationService");
const annotationImages = require("../services/annotationImageService");

function statusFromError(error) {
  return error.statusCode || 400;
}

async function getAnnotation(req, res) {
  try {
    const annotation = await lessonAnnotation.getAnnotation(
      req.userId,
      req.params.courseId,
      req.params.lessonId,
      req.query.tab,
    );
    res.json({ annotation });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function putAnnotation(req, res) {
  try {
    const { strokes, labels, images, tab } = req.body || {};
    const annotation = await lessonAnnotation.putAnnotation(
      req.userId,
      req.params.courseId,
      req.params.lessonId,
      tab || req.query.tab,
      { strokes, labels, images },
    );
    res.json({ annotation });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function mergeAnnotations(req, res) {
  try {
    const items = Array.isArray(req.body?.annotations)
      ? req.body.annotations
      : [];
    const results = await lessonAnnotation.mergeLocalAnnotations(
      req.userId,
      items,
    );
    res.json({ results });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function uploadImage(req, res) {
  try {
    const image = await annotationImages.uploadImage(
      req.userId,
      req.body?.imageBase64,
    );
    res.status(201).json({ image });
  } catch (error) {
    res.status(statusFromError(error)).json({ error: error.message });
  }
}

async function getImage(req, res) {
  try {
    const image = await annotationImages.getImage(req.params.imageId);
    if (!image) {
      res.status(404).json({ error: "Image not found" });
      return;
    }
    res.set("Content-Type", image.mimeType);
    // Keys are random and images never change, so cache forever.
    res.set("Cache-Control", "public, max-age=31536000, immutable");
    res.send(Buffer.from(image.data.buffer || image.data));
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
}

module.exports = {
  getAnnotation,
  putAnnotation,
  mergeAnnotations,
  uploadImage,
  getImage,
};
