const express = require('express');
const router = express.Router();
const Challenge = require('../models/challenge');
const Submission = require('../models/submission');
const User = require('../modules/auth/models/User');

// GET today's challenge
router.get('/today', async (req, res) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const tomorrow = new Date(today); tomorrow.setDate(today.getDate() + 1);

    const challenge = await Challenge.findOne({
        scheduledDate: { $gte: today, $lt: tomorrow }
    }).select('-testCases');  // don't expose hidden test cases

    if (!challenge) return res.status(404).json({ message: 'No challenge today' });
    res.json(challenge);
});

// GET challenge by slug
router.get('/:slug', async (req, res) => {
    const challenge = await Challenge.findOne({ slug: req.params.slug }).select('-testCases');
    if (!challenge) return res.status(404).json({ message: 'Not found' });
    res.json(challenge);
});

// GET past challenges (last 30 days)
router.get('/', async (req, res) => {
    const challenges = await Challenge.find()
        .sort({ scheduledDate: -1 })
        .limit(30)
        .select('title slug difficulty tags scheduledDate');
    res.json(challenges);
});

const { 
    executePythonCode, 
    executeJavaScriptCode,
    executeJavaCode,
    executeCppCode 
} = require('../services/executionService');

// POST submit a solution
router.post('/:id/submit', async (req, res) => {
    try {
        const { userId, language, code } = req.body;
        const challenge = await Challenge.findById(req.params.id);
        if (!challenge) return res.status(404).json({ message: 'Not found' });

        const testResults = [];
        let allPassed = true;
        const startTime = Date.now();

        // ── 1. Dynamic Test Case Generation ──
        const activeTestCases = [...challenge.testCases];
        if (challenge.testCaseGenerator && challenge.testCaseGenerator[language]) {
            try {
                for (let i = 0; i < 3; i++) {
                    const genResult = await (language === 'python' ? executePythonCode(challenge.testCaseGenerator.python) : executeJavaScriptCode(challenge.testCaseGenerator.javascript));
                    if (genResult.stdout) {
                        const parts = genResult.stdout.trim().split('\n');
                        if (parts.length >= 2) {
                            const expected = parts.pop();
                            const input = parts.join('\n');
                            activeTestCases.push({ input, expectedOutput: expected, isHidden: true, isRandom: true });
                        }
                    }
                }
            } catch (err) {
                console.error("Generator Error:", err);
            }
        }

        // ── 2. Execution ──
        if (language === 'python' || language === 'javascript') {
            for (const testCase of activeTestCases) {
                let executionResult;
                let testCode = code;

                if (language === 'python') {
                    // Support both snake_case and camelCase
                    const snakeName = challenge.slug.replace(/-/g, '_');
                    const camelName = challenge.slug.replace(/-./g, x => x[1].toUpperCase());
                    const inputs = testCase.input.split('\n').join(', ');
                    
                    testCode += `
import json
try:
    if '${snakeName}' in globals():
        result = ${snakeName}(${inputs})
    elif '${camelName}' in globals():
        result = ${camelName}(${inputs})
    else:
        raise NameError("Could not find function '${snakeName}' or '${camelName}'")
    print(json.dumps(result))
except Exception as e:
    import sys
    print(str(e), file=sys.stderr)
    sys.exit(1)
`;
                    executionResult = await executePythonCode(testCode);
                } else if (language === 'javascript') {
                    // Simple driver for JS
                    const functionName = challenge.slug.replace(/-./g, x => x[1].toUpperCase());
                    const inputs = testCase.input.split('\n').join(', ');
                    testCode += `\nconsole.log(JSON.stringify(${functionName}(${inputs})))`;
                    executionResult = await executeJavaScriptCode(testCode);
                } else if (language === 'java') {
                    // Java driver: Wrap user code in a Solution class if not already, then add a main method
                    const camelName = challenge.slug.replace(/-./g, x => x[1].toUpperCase());
                    const inputs = testCase.input.split('\n').join(', ');
                    
                    testCode += `
import java.util.*;
public class Solution {
    ${code}
    public static void main(String[] args) {
        Solution sol = new Solution();
        Object res = sol.${camelName}(${inputs});
        // Very basic JSON-like output
        if (res instanceof int[]) {
            System.out.println(Arrays.toString((int[])res));
        } else {
            System.out.println(res);
        }
    }
}
`;
                    executionResult = await executeJavaCode(testCode);
                } else if (language === 'cpp') {
                    // C++ driver
                    const snakeName = challenge.slug.replace(/-/g, '_');
                    const inputs = testCase.input.split('\n').join(', ');
                    testCode += `
#include <iostream>
#include <vector>
#include <string>
#include <algorithm>
using namespace std;

${code}

int main() {
    auto res = ${snakeName}(${inputs});
    // Very basic output for arrays
    cout << "[";
    for(size_t i=0; i<res.size(); ++i) {
        cout << res[i] << (i == res.size()-1 ? "" : ",");
    }
    cout << "]" << endl;
    return 0;
}
`;
                    executionResult = await executeCppCode(testCode);
                }

                const actualOutput = executionResult.stdout.trim();
                
                // Smart comparison: try JSON comparison first, then fallback to normalized string
                let passed = false;
                try {
                    const actualObj = JSON.parse(actualOutput);
                    const expectedObj = JSON.parse(testCase.expectedOutput);
                    passed = JSON.stringify(actualObj) === JSON.stringify(expectedObj);
                } catch (e) {
                    // Fallback to normalized string comparison (remove all whitespace)
                    const normalize = (s) => s.replace(/\s+/g, '');
                    passed = normalize(actualOutput) === normalize(testCase.expectedOutput);
                }

                if (!passed) allPassed = false;

                testResults.push({
                    input: testCase.input,
                    expected: testCase.expectedOutput,
                    actual: actualOutput,
                    passed,
                    error: executionResult.error,
                    isHidden: testCase.isHidden,
                    isRandom: testCase.isRandom
                });
            }
        } else {
            // Mock result for unsupported languages
            testResults.push({ status: 'Skipped', message: 'Execution not supported for this language' });
            allPassed = false;
        }

        const duration = Date.now() - startTime;
        const status = allPassed ? 'Accepted' : (testResults.some(r => r.error) ? 'Runtime Error' : 'Wrong Answer');

        const submission = await Submission.create({
            challengeId: challenge._id,
            userId,
            language,
            code,
            status,
            results: testResults,
            executionTime: duration
        });

        // ── 4. Calculate Streak ──
        let streak = 0;
        if (allPassed) {
            const userSubmissions = await Submission.find({ 
                userId, 
                status: 'Accepted' 
            }).sort({ submittedAt: -1 });

            if (userSubmissions.length > 0) {
                const uniqueDates = new Set();
                userSubmissions.forEach(sub => {
                    uniqueDates.add(sub.submittedAt.toISOString().split('T')[0]);
                });

                const sortedDates = Array.from(uniqueDates).sort().reverse();
                const todayStr = new Date().toISOString().split('T')[0];
                const yesterday = new Date(); yesterday.setDate(yesterday.getDate() - 1);
                const yesterdayStr = yesterday.toISOString().split('T')[0];

                if (sortedDates[0] === todayStr || sortedDates[0] === yesterdayStr) {
                    streak = 1;
                    let lastIdxDate = new Date(sortedDates[0]);
                    for (let i = 1; i < sortedDates.length; i++) {
                        const nextDate = new Date(sortedDates[i]);
                        const diffDays = Math.round(Math.abs(lastIdxDate - nextDate) / (1000 * 60 * 60 * 24));
                        if (diffDays === 1) {
                            streak++;
                            lastIdxDate = nextDate;
                        } else break;
                    }
                }
                
                // Update User model if userId is a valid ObjectId
                try {
                    await User.findByIdAndUpdate(userId, { 
                        currentStreak: streak, 
                        $max: { highestStreak: streak },
                        lastChallengeDate: new Date()
                    });
                } catch (e) {}
            }
        }

        res.json({ 
            status, 
            passedCount: testResults.filter(r => r.passed).length,
            totalCount: testResults.length,
            results: testResults,
            executionTime: duration,
            submissionId: submission._id,
            streak: streak
        });
    } catch (err) {
        console.error("Submission Error:", err);
        res.status(500).json({ status: 'Error', message: err.message });
    }
});

module.exports = router;