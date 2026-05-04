const User = require("../models/User");

/**
 * Register a new user
 * @param {Object} userData - User data (email, username, password, firstName, lastName)
 * @returns {Promise<Object>} User object
 */
async function registerUser(userData) {
  try {
    const { email, username, password, firstName, lastName } = userData;

    // Check if user already exists
    const existingUser = await User.findOne({
      $or: [{ email }, { username }],
    });

    if (existingUser) {
      throw new Error("Email or username already in use");
    }

    const user = new User({
      email,
      username,
      password,
      firstName,
      lastName,
    });

    await user.save();
    return user.toJSON();
  } catch (error) {
    throw error;
  }
}

/**
 * Login user - verify credentials
 * @param {string} email - User email
 * @param {string} password - User password
 * @returns {Promise<Object>} User object
 */
async function loginUser(email, password) {
  try {
    const user = await User.findOne({ email }).select("+password");

    if (!user) {
      throw new Error("Invalid email or password");
    }

    const isPasswordValid = await user.comparePassword(password);

    if (!isPasswordValid) {
      throw new Error("Invalid email or password");
    }

    // Update last login
    user.lastLogin = new Date();
    await user.save();

    return user.toJSON();
  } catch (error) {
    throw error;
  }
}

/**
 * Get user by ID
 * @param {string} userId - User ID
 * @returns {Promise<Object>} User object
 */
async function getUserById(userId) {
  try {
    const user = await User.findById(userId);
    if (!user) {
      throw new Error("User not found");
    }
    return user.toJSON();
  } catch (error) {
    throw error;
  }
}

/**
 * Get user by email
 * @param {string} email - User email
 * @returns {Promise<Object>} User object
 */
async function getUserByEmail(email) {
  try {
    const user = await User.findOne({ email });
    if (!user) {
      throw new Error("User not found");
    }
    return user.toJSON();
  } catch (error) {
    throw error;
  }
}

/**
 * Update user profile
 * @param {string} userId - User ID
 * @param {Object} updateData - Data to update
 * @returns {Promise<Object>} Updated user object
 */
async function updateUserProfile(userId, updateData) {
  try {
    const allowedFields = [
      "firstName",
      "lastName",
      "bio",
      "profilePicture",
      "preferredLanguages",
    ];
    const filteredData = {};

    allowedFields.forEach((field) => {
      if (updateData[field] !== undefined) {
        filteredData[field] = updateData[field];
      }
    });

    const user = await User.findByIdAndUpdate(
      userId,
      { ...filteredData, updatedAt: Date.now() },
      { new: true, runValidators: true }
    );

    if (!user) {
      throw new Error("User not found");
    }

    return user.toJSON();
  } catch (error) {
    throw error;
  }
}

/**
 * Change user password
 * @param {string} userId - User ID
 * @param {string} oldPassword - Current password
 * @param {string} newPassword - New password
 * @returns {Promise<Object>} User object
 */
async function changePassword(userId, oldPassword, newPassword) {
  try {
    const user = await User.findById(userId).select("+password");

    if (!user) {
      throw new Error("User not found");
    }

    const isPasswordValid = await user.comparePassword(oldPassword);

    if (!isPasswordValid) {
      throw new Error("Current password is incorrect");
    }

    user.password = newPassword;
    await user.save();

    return user.toJSON();
  } catch (error) {
    throw error;
  }
}

/**
 * Delete user account
 * @param {string} userId - User ID
 * @returns {Promise<Object>} Deleted user object
 */
async function deleteUserAccount(userId) {
  try {
    const user = await User.findByIdAndDelete(userId);

    if (!user) {
      throw new Error("User not found");
    }

    return { message: "Account deleted successfully" };
  } catch (error) {
    throw error;
  }
}

module.exports = {
  registerUser,
  loginUser,
  getUserById,
  getUserByEmail,
  updateUserProfile,
  changePassword,
  deleteUserAccount,
};
