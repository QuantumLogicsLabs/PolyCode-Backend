const crypto = require("crypto");
const AnnotationImage = require("../models/AnnotationImage");

const MAX_IMAGE_BYTES = 1.5 * 1024 * 1024;
const MAX_IMAGES_PER_USER = 500;
const DATA_URL_RE = /^data:(image\/(?:jpeg|png|webp|gif));base64,([A-Za-z0-9+/=]+)$/;
const KEY_RE = /^[a-f0-9]{32}$/;

function fail(message, statusCode) {
  const error = new Error(message);
  error.statusCode = statusCode;
  return error;
}

async function uploadImage(userId, imageBase64) {
  const match = DATA_URL_RE.exec(String(imageBase64 || ""));
  if (!match) {
    throw fail("imageBase64 must be a JPEG, PNG, WebP or GIF data URL", 400);
  }

  const data = Buffer.from(match[2], "base64");
  if (!data.length) throw fail("Image is empty", 400);
  if (data.length > MAX_IMAGE_BYTES) {
    throw fail("Image is too large (max 1.5 MB after compression)", 413);
  }

  const count = await AnnotationImage.countDocuments({ userId });
  if (count >= MAX_IMAGES_PER_USER) {
    throw fail(`Image limit reached (${MAX_IMAGES_PER_USER} per account)`, 429);
  }

  const key = crypto.randomBytes(16).toString("hex");
  await AnnotationImage.create({
    userId,
    key,
    mimeType: match[1],
    size: data.length,
    data,
  });
  return { imageId: key, mimeType: match[1], size: data.length };
}

async function getImage(key) {
  if (!KEY_RE.test(String(key || ""))) return null;
  return AnnotationImage.findOne({ key }).select("mimeType data").lean();
}

module.exports = {
  uploadImage,
  getImage,
  KEY_RE,
};
