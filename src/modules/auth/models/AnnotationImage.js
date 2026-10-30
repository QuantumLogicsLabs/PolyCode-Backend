const mongoose = require("mongoose");

/**
 * An image a learner placed on a lesson with the markup tools. The bytes live
 * here (not in learner_progress) so one user's annotations can't push that
 * document toward Mongo's 16 MB limit. `key` is a random id used in the public
 * image URL, so it can be loaded by a plain <img> tag.
 */
const annotationImageSchema = new mongoose.Schema(
  {
    userId: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
      index: true,
    },
    key: { type: String, required: true, unique: true },
    mimeType: { type: String, required: true },
    size: { type: Number, required: true },
    data: { type: Buffer, required: true },
  },
  { timestamps: true },
);

module.exports = mongoose.model(
  "AnnotationImage",
  annotationImageSchema,
  "annotation_images",
);
