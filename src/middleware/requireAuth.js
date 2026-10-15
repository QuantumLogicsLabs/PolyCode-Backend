const { verifyAccessToken } = require("../utils/jwt");

/**
 * Verifies Bearer JWT and sets req.userId.
 */
function requireAuth(req, res, next) {
  try {
    const authHeader = req.headers.authorization;
    if (!authHeader || !authHeader.startsWith("Bearer ")) {
      return res.status(401).json({ error: "No token provided" });
    }

    const token = authHeader.split(" ")[1];
    const decoded = verifyAccessToken(token);
    req.userId = decoded.id;
    // Handlers use these to hand back a renewed token before this one ages out.
    req.auth = decoded;
    req.authToken = token;
    next();
  } catch {
    return res.status(401).json({ error: "Invalid or expired token" });
  }
}

module.exports = requireAuth;
