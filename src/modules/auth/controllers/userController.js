const userService = require("../services/userService");

/**
 * POST /api/auth/register - Register a new user
 */
async function register(req, res) {
  try {
    const { email, username, password, firstName, lastName } = req.body;

    if (!email || !username || !password) {
      return res
        .status(400)
        .json({ error: "Email, username, and password are required" });
    }

    const user = await userService.registerUser({
      email,
      username,
      password,
      firstName,
      lastName,
    });

    res.status(201).json({
      message: "User registered successfully",
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
      return res
        .status(400)
        .json({ error: "Email and password are required" });
    }

    const user = await userService.loginUser(email, password);

    // In production, you would create a JWT token here
    // For now, we'll return the user and the client can store it
    res.json({
      message: "Login successful",
      user,
      // token: createToken(user._id), // Uncomment when JWT is configured
    });
  } catch (error) {
    console.error("Login error:", error.message);
    res.status(401).json({ error: error.message });
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
 * PUT /api/auth/user/:id - Update user profile
 */
async function updateProfile(req, res) {
  try {
    const { id } = req.params;
    const updateData = req.body;

    const user = await userService.updateUserProfile(id, updateData);

    res.json({
      message: "Profile updated successfully",
      user,
    });
  } catch (error) {
    console.error("Update profile error:", error.message);
    res.status(400).json({ error: error.message });
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
      newPassword
    );

    res.json({
      message: "Password changed successfully",
      user,
    });
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
  getUserProfile,
  updateProfile,
  changePasswordHandler,
  deleteAccount,
};
