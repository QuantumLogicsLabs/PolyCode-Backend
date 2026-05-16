const mongoose = require('mongoose');

const submissionSchema = new mongoose.Schema({
    challengeId: { type: mongoose.Schema.Types.ObjectId, ref: 'Challenge' },
    userId: String,
    language: String,
    code: String,
    status: { 
        type: String, 
        enum: ['Accepted', 'Wrong Answer', 'Runtime Error', 'Error', 'Time Limit Exceeded'] 
    },
    results: [{
        input: String,
        expected: String,
        actual: String,
        passed: Boolean,
        error: String,
        isHidden: Boolean,
        isRandom: Boolean
    }],
    executionTime: Number,
    submittedAt: { type: Date, default: Date.now }
});

module.exports = mongoose.model('Submission', submissionSchema);