const express = require("express");
const requireAuth = require("../../middleware/requireAuth");
const certificateController = require("./controllers/certificateController");

const router = express.Router();

// Issue (or return) the signed-in learner's certificate: body { courseId }.
router.post("/", requireAuth, certificateController.issueCertificate);

// Public: certificates issued to a user, for their profile page.
router.get("/user/:username", certificateController.listUserCertificates);

// Public: verify a certificate by its id.
router.get("/:id", certificateController.getCertificate);

module.exports = router;
