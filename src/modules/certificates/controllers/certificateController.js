const certificates = require("../services/certificateService");

function sendError(res, error) {
  const status = error.statusCode || 500;
  if (status >= 500) console.error("Certificate error:", error.message);
  const body = { error: status >= 500 ? "Could not process certificate" : error.message };
  if (error.code === "COURSE_INCOMPLETE") {
    Object.assign(body, {
      code: error.code,
      completed: error.completed,
      required: error.required,
    });
  }
  res.status(status).json(body);
}

async function issueCertificate(req, res) {
  try {
    const { certificate, created } = await certificates.issueCertificate(
      req.userId,
      req.body?.courseId,
    );
    res.status(created ? 201 : 200).json({ certificate });
  } catch (error) {
    sendError(res, error);
  }
}

async function getCertificate(req, res) {
  try {
    const certificate = await certificates.getCertificate(req.params.id);
    if (!certificate) return res.status(404).json({ error: "Certificate not found" });
    res.json({ certificate });
  } catch (error) {
    sendError(res, error);
  }
}

async function listUserCertificates(req, res) {
  try {
    const list = await certificates.listCertificatesForUsername(req.params.username);
    if (!list) return res.status(404).json({ error: "User not found" });
    res.json({ certificates: list });
  } catch (error) {
    sendError(res, error);
  }
}

module.exports = {
  issueCertificate,
  getCertificate,
  listUserCertificates,
};
