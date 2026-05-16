const mongoose = require('mongoose');

const testCaseSchema = new mongoose.Schema({
    input: String,
    expectedOutput: String,
    isHidden: { type: Boolean, default: false }
});

const challengeSchema = new mongoose.Schema({
    title: { type: String, required: true },
    slug: { type: String, required: true, unique: true },
    description: String,        // markdown supported
    difficulty: { type: String, enum: ['Easy', 'Medium', 'Hard'] },
    tags: [String],
    examples: [{ input: String, output: String, explanation: String }],
    constraints: [String],
    testCases: [testCaseSchema],
    starterCode: {              // per-language starters
        python: String,
        javascript: String,
        java: String,
        cpp: String
    },
    testCaseGenerator: {
        python: String,
        javascript: String
    },
    scheduledDate: { type: Date, unique: true },  // which day this is the challenge
    createdAt: { type: Date, default: Date.now }
});

module.exports = mongoose.model('Challenge', challengeSchema);