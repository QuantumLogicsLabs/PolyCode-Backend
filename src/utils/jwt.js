const jwt = require("jsonwebtoken");

function getJwtSecret() {
  const secret = process.env.JWT_SECRET;
  if (!secret && process.env.NODE_ENV === "production") {
    throw new Error("JWT_SECRET must be set in production");
  }
  return secret || "dev_secret";
}

/** Long-lived by default: sessions end when the user signs out, not on a timer. */
const DEFAULT_EXPIRES_IN = "30d";

function getExpiresIn() {
  return process.env.JWT_EXPIRES_IN || DEFAULT_EXPIRES_IN;
}

function signAccessToken(userId) {
  return jwt.sign({ id: userId }, getJwtSecret(), {
    expiresIn: getExpiresIn(),
  });
}

/**
 * True once a token is past the halfway point of its lifetime, so an active
 * user keeps getting a fresh token and is never expired out mid-use.
 */
function shouldRenewToken(decoded) {
  if (!decoded || typeof decoded.exp !== "number") return false;

  const nowSeconds = Math.floor(Date.now() / 1000);
  const remaining = decoded.exp - nowSeconds;
  if (remaining <= 0) return false;

  const lifetime =
    typeof decoded.iat === "number" ? decoded.exp - decoded.iat : 0;
  if (lifetime > 0) return remaining < lifetime / 2;

  return remaining < 7 * 24 * 60 * 60;
}

/** New token for a still-valid session that is past half its life, else null. */
function renewAccessToken(decoded) {
  if (!shouldRenewToken(decoded)) return null;
  const userId = decoded.id || decoded.userId || decoded.sub;
  if (!userId) return null;
  return signAccessToken(userId);
}

function verifyAccessToken(token) {
  return jwt.verify(token, getJwtSecret());
}

module.exports = {
  getJwtSecret,
  getExpiresIn,
  signAccessToken,
  shouldRenewToken,
  renewAccessToken,
  verifyAccessToken,
};
