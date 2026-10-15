const userService = require("../services/userService");
const {
  uploadProfileImage,
  isDriveConfigured,
  streamDriveFile,
} = require("../../../services/googleDriveService");
const {
  signAccessToken,
  renewAccessToken,
} = require("../../../utils/jwt");

/**
 * Only a genuinely missing account ends a session; every other failure here is
 * infrastructure (Mongo cold start, timeout) and must not log the user out.
 */
function isMissingUserError(error) {
  return /user not found|account no longer exists/i.test(error?.message || "");
}

/**
 * Helper: create a signed JWT for a user
 */
function createToken(userId) {
  return signAccessToken(userId);
}

/**
 * POST /api/auth/register - Register a new user
 */
async function register(req, res) {
  try {
    const {
      email,
      username,
      password,
      name,
      firstName,
      middleName,
      lastName,
    } = req.body;

    if (!email || !username || !password) {
      return res
        .status(400)
        .json({ error: "Email, username, and password are required" });
    }

    const user = await userService.registerUser({
      email,
      username,
      password,
      name,
      firstName,
      middleName,
      lastName,
    });

    const token = createToken(user._id);

    res.status(201).json({
      message: "User registered successfully",
      token,
      user,
    });
  } catch (error) {
    console.error("Register error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

/**
 * POST /api/auth/login - Login user
 */
async function login(req, res) {
  try {
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({ error: "Email and password are required" });
    }

    const user = await userService.loginUser(email, password);
    const token = createToken(user._id);

    res.json({
      message: "Login successful",
      token,
      user,
    });
  } catch (error) {
    console.error("Login error:", error.message);
    res.status(401).json({ error: error.message });
  }
}

/**
 * POST /api/auth/google - Continue with Google (ID token)
 */
async function googleAuth(req, res) {
  try {
    const { idToken } = req.body;
    if (!idToken) {
      return res.status(400).json({ error: "Google idToken is required" });
    }

    const clientId =
      process.env.GOOGLE_CLIENT_ID ||
      process.env.GOOGLE_DRIVE_OAUTH_CLIENT_ID ||
      "";

    if (!clientId) {
      return res.status(503).json({
        error:
          "Google Sign-In is not configured. Set GOOGLE_CLIENT_ID on the server.",
      });
    }

    const { OAuth2Client } = require("google-auth-library");
    const client = new OAuth2Client(clientId);
    const ticket = await client.verifyIdToken({
      idToken,
      audience: clientId,
    });
    const payload = ticket.getPayload();

    if (!payload?.sub || !payload?.email) {
      return res.status(401).json({ error: "Invalid Google token" });
    }

    if (payload.email_verified === false) {
      return res.status(401).json({ error: "Google email is not verified" });
    }

    const user = await userService.loginOrRegisterWithGoogle({
      googleId: payload.sub,
      email: payload.email,
      name: payload.name || "",
      firstName: payload.given_name || "",
      lastName: payload.family_name || "",
    });

    const token = createToken(user._id);

    res.json({
      message: "Google sign-in successful",
      token,
      user,
    });
  } catch (error) {
    console.error("Google auth error:", error.message);
    res.status(401).json({ error: error.message || "Google sign-in failed" });
  }
}

/**
 * GET /api/auth/user/:id - Get user by ID
 */
async function getUserProfile(req, res) {
  try {
    const { id } = req.params;
    const user = await userService.getUserById(id);
    res.json({ user });
  } catch (error) {
    console.error("Get user error:", error.message);
    res.status(404).json({ error: error.message });
  }
}

/**
 * GET /api/auth/username/:username - Get public user profile by username
 */
async function getUserByUsername(req, res) {
  try {
    const { username } = req.params;
    const user = await userService.getUserByUsername(username);
    res.json({ user });
  } catch (error) {
    console.error("Get user by username error:", error.message);
    res.status(404).json({ error: error.message });
  }
}

async function followUser(req, res) {
  try {
    const { username } = req.params;
    const result = await userService.setFollowRelationship(
      req.userId,
      username,
      true,
    );
    res.json({ message: "User followed", ...result });
  } catch (error) {
    console.error("Follow user error:", error.message);
    res.status(error.message === "You cannot follow yourself" ? 400 : 404).json({
      error: error.message,
    });
  }
}

async function unfollowUser(req, res) {
  try {
    const { username } = req.params;
    const result = await userService.setFollowRelationship(
      req.userId,
      username,
      false,
    );
    res.json({ message: "User unfollowed", ...result });
  } catch (error) {
    console.error("Unfollow user error:", error.message);
    res.status(error.message === "You cannot follow yourself" ? 400 : 404).json({
      error: error.message,
    });
  }
}

async function getFollowStatus(req, res) {
  try {
    const { username } = req.params;
    const isFollowing = await userService.isFollowingUser(req.userId, username);
    res.json({ isFollowing });
  } catch (error) {
    console.error("Follow status error:", error.message);
    res.status(404).json({ error: error.message });
  }
}

async function getFollowers(req, res) {
  try {
    const { username } = req.params;
    const users = await userService.listUserConnections(username, "followers");
    res.json({ users });
  } catch (error) {
    console.error("Followers list error:", error.message);
    res.status(404).json({ error: error.message });
  }
}

async function getFollowing(req, res) {
  try {
    const { username } = req.params;
    const users = await userService.listUserConnections(username, "following");
    res.json({ users });
  } catch (error) {
    console.error("Following list error:", error.message);
    res.status(404).json({ error: error.message });
  }
}

/**
 * GET /api/auth/me - Get current user from JWT (requireAuth sets req.userId)
 */
async function getMe(req, res) {
  try {
    const user = await userService.getUserById(req.userId);
    // Sliding session: renew before the token ages out so a signed-in user
    // stays signed in until they explicitly log out.
    const token = renewAccessToken(req.auth);
    res.json(token ? { user, token } : { user });
  } catch (error) {
    console.error("Get me error:", error.message);

    if (isMissingUserError(error)) {
      // The account is gone — the client should end the session.
      return res
        .status(401)
        .json({ error: "Account no longer exists", code: "SESSION_INVALID" });
    }

    // Database hiccup / cold start: the token is still good, so do NOT answer
    // with a status that would sign the user out. 503 means "ask again".
    return res.status(503).json({
      error: "Could not reach the account service. Please try again.",
      code: "SESSION_UNAVAILABLE",
    });
  }
}

function assertSelfOrThrow(req, targetUserId) {
  if (!req.userId || String(req.userId) !== String(targetUserId)) {
    const err = new Error("You can only edit your own profile");
    err.statusCode = 403;
    throw err;
  }
}

function stripProtectedFields(body = {}) {
  const { email, password, _id, ...rest } = body;
  return rest;
}

function extractDriveFileIdFromUrl(url = "") {
  const byQuery = url.match(/[?&]id=([^&]+)/);
  if (byQuery) return byQuery[1];
  const byPath = url.match(/\/d\/([^/]+)/);
  if (byPath) return byPath[1];
  return null;
}

/**
 * PUT /api/auth/user/:id - Update user profile (email cannot be changed)
 */
async function updateProfile(req, res) {
  try {
    const { id } = req.params;
    assertSelfOrThrow(req, id);
    const updateData = stripProtectedFields(req.body);
    const user = await userService.updateUserProfile(id, updateData);
    res.json({ message: "Profile updated successfully", user });
  } catch (error) {
    console.error("Update profile error:", error.message);
    res
      .status(error.statusCode || 400)
      .json({ error: error.message });
  }
}

/**
 * POST /api/auth/user/:id/avatar - Upload cropped profile picture to Google Drive
 * Body: { imageBase64: "data:image/jpeg;base64,..." }
 */
async function uploadAvatar(req, res) {
  try {
    const { id } = req.params;
    assertSelfOrThrow(req, id);

    if (!isDriveConfigured()) {
      return res.status(503).json({
        error:
          "Profile photo upload is not configured. Set GOOGLE_DRIVE_CREDENTIALS_PATH (or CLIENT_EMAIL + PRIVATE_KEY) and GOOGLE_DRIVE_FOLDER_ID in backend .env, then restart the server.",
      });
    }

    const { imageBase64 } = req.body;
    if (!imageBase64 || typeof imageBase64 !== "string") {
      return res.status(400).json({ error: "imageBase64 is required" });
    }

    const match = imageBase64.match(
      /^data:(image\/(?:jpeg|png|webp));base64,(.+)$/i,
    );
    if (!match) {
      return res
        .status(400)
        .json({ error: "Invalid image. Use JPEG, PNG, or WebP." });
    }

    const mimeType = match[1].toLowerCase();
    const buffer = Buffer.from(match[2], "base64");

    if (buffer.length > 2 * 1024 * 1024) {
      return res.status(400).json({ error: "Image must be under 2 MB" });
    }

    const ext = mimeType.includes("png")
      ? "png"
      : mimeType.includes("webp")
        ? "webp"
        : "jpg";
    const fileName = `polycode-avatar-${id}-${Date.now()}.${ext}`;

    const { url, fileId } = await uploadProfileImage({
      buffer,
      mimeType,
      fileName,
    });

    const user = await userService.setProfilePicture(id, {
      url,
      driveFileId: fileId,
    });

    res.json({
      message: "Profile picture uploaded",
      user,
      profilePicture: url,
    });
  } catch (error) {
    console.error("Upload avatar error:", error.message);
    res
      .status(error.statusCode || 400)
      .json({ error: error.message });
  }
}

/**
 * GET /api/auth/user/:id/avatar — stream profile image (fixes Drive hotlink blocks)
 */
async function getAvatarImage(req, res) {
  try {
    const { id } = req.params;
    const user = await userService.getUserById(id);

    if (!user.profilePictureDriveId && !user.profilePicture) {
      return res.status(404).json({ error: "No profile picture" });
    }

    const driveFileId =
      user.profilePictureDriveId ||
      extractDriveFileIdFromUrl(user.profilePicture);

    if (driveFileId) {
      const { stream, mimeType } = await streamDriveFile(driveFileId);
      res.setHeader("Content-Type", mimeType);
      res.setHeader("Cache-Control", "public, max-age=3600");
      stream.pipe(res);
      return;
    }

    return res.redirect(user.profilePicture);
  } catch (error) {
    console.error("Avatar image error:", error.message);
    res.status(404).json({ error: "Could not load profile picture" });
  }
}

/**
 * POST /api/auth/change-password - Change user password
 */
async function changePasswordHandler(req, res) {
  try {
    const { userId, oldPassword, newPassword } = req.body;

    if (!userId || !oldPassword || !newPassword) {
      return res
        .status(400)
        .json({ error: "userId, oldPassword, and newPassword are required" });
    }

    const user = await userService.changePassword(
      userId,
      oldPassword,
      newPassword,
    );
    res.json({ message: "Password changed successfully", user });
  } catch (error) {
    console.error("Change password error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

/**
 * DELETE /api/auth/user/:id - Delete user account
 */
async function deleteAccount(req, res) {
  try {
    const { id } = req.params;
    const result = await userService.deleteUserAccount(id);
    res.json(result);
  } catch (error) {
    console.error("Delete account error:", error.message);
    res.status(400).json({ error: error.message });
  }
}

module.exports = {
  register,
  login,
  googleAuth,
  getMe,
  getUserProfile,
  getUserByUsername,
  followUser,
  unfollowUser,
  getFollowStatus,
  getFollowers,
  getFollowing,
  updateProfile,
  uploadAvatar,
  getAvatarImage,
  changePasswordHandler,
  deleteAccount,
};
