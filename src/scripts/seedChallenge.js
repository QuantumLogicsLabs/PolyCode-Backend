require('dotenv').config();
const mongoose = require('mongoose');
const Challenge = require('../models/challenge');

mongoose.connect(process.env.MONGODB_URI).then(async () => {
    await Challenge.create({
        title: "Two Sum",
        slug: "two-sum",
        description: "Given an array of integers `nums` and an integer `target`, return indices of the two numbers that add up to target.",
        difficulty: "Easy",
        tags: ["Array", "Hash Map"],
        examples: [{ input: "nums = [2,7,11,15], target = 9", output: "[0,1]", explanation: "nums[0] + nums[1] = 9" }],
        constraints: ["2 <= nums.length <= 10^4", "Each input has exactly one solution"],
        testCases: [
            { input: "[2,7,11,15]\n9", expectedOutput: "[0,1]" },
            { input: "[3,2,4]\n6", expectedOutput: "[1,2]", isHidden: true }
        ],
        starterCode: {
            python: "def two_sum(nums, target):\n    pass",
            javascript: "function twoSum(nums, target) {\n    \n}"
        },
        scheduledDate: new Date()  // today
    });
    console.log('Seeded!');
    process.exit(0);
});