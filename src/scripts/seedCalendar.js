require('dotenv').config();
const mongoose = require('mongoose');
const Challenge = require('../models/challenge');

const challenges = [
    {
        title: "Two Sum",
        slug: "two-sum",
        description: "Given an array of integers `nums` and an integer `target`, return indices of the two numbers that add up to target.",
        difficulty: "Easy",
        tags: ["Array", "Hash Map"],
        examples: [{ input: "nums = [2,7,11,15], target = 9", output: "[0,1]", explanation: "2 + 7 = 9" }],
        constraints: ["2 <= nums.length <= 10^4", "Only one valid answer exists."],
        testCases: [
            { input: "[2,7,11,15]\n9", expectedOutput: "[0,1]" },
            { input: "[3,2,4]\n6", expectedOutput: "[1,2]" }
        ],
        starterCode: {
            python: "def two_sum(nums, target):\n    # Your code here\n    pass",
            javascript: "function twoSum(nums, target) {\n    // Your code here\n}",
            java: "public int[] twoSum(int[] nums, int target) {\n    // Your code here\n    return new int[0];\n}",
            cpp: "vector<int> two_sum(vector<int>& nums, int target) {\n    // Your code here\n    return {};\n}"
        },
        testCaseGenerator: {
            python: "import random, json\nsize = random.randint(2, 20)\nnums = random.sample(range(-100, 100), size)\ni, j = random.sample(range(size), 2)\ntarget = nums[i] + nums[j]\nprint(json.dumps(nums))\nprint(target)\nprint(json.dumps(sorted([i, j])))"
        }
    },
    {
        title: "Palindrome Number",
        slug: "palindrome-number",
        description: "Given an integer `x`, return `true` if `x` is a palindrome, and `false` otherwise.",
        difficulty: "Easy",
        tags: ["Math"],
        examples: [{ input: "x = 121", output: "true", explanation: "121 reads as 121 from left to right and from right to left." }],
        constraints: ["-2^31 <= x <= 2^31 - 1"],
        testCases: [
            { input: "121", expectedOutput: "true" },
            { input: "-121", expectedOutput: "false" },
            { input: "10", expectedOutput: "false" }
        ],
        starterCode: {
            python: "def is_palindrome(x):\n    # Your code here\n    pass",
            javascript: "function isPalindrome(x) {\n    // Your code here\n}",
            java: "public boolean isPalindrome(int x) {\n    // Your code here\n    return false;\n}",
            cpp: "bool is_palindrome(int x) {\n    // Your code here\n    return false;\n}"
        },
        testCaseGenerator: {
            python: "import random\nx = random.choice([random.randint(0, 1000), random.randint(-1000, 0)])\nif random.random() > 0.5:\n    s = str(abs(x))\n    x = int(s + s[::-1])\nprint(x)\nprint('true' if str(x) == str(x)[::-1] else 'false')"
        }
    },
    {
        title: "Valid Parentheses",
        slug: "valid-parentheses",
        description: "Given a string `s` containing just the characters '(', ')', '{', '}', '[' and ']', determine if the input string is valid.",
        difficulty: "Easy",
        tags: ["Stack", "String"],
        examples: [{ input: "s = '()[]{}'", output: "true" }],
        constraints: ["1 <= s.length <= 10^4"],
        testCases: [
            { input: "'()'", expectedOutput: "true" },
            { input: "'()[]{}'", expectedOutput: "true" },
            { input: "'(]'", expectedOutput: "false" }
        ],
        starterCode: {
            python: "def is_valid(s):\n    # Your code here\n    pass",
            javascript: "function isValid(s) {\n    // Your code here\n}"
        },
        testCaseGenerator: {
            python: "import random\ndef gen(n):\n    if n == 0: return ''\n    pair = random.choice([('(',')'), ('[',']'), ('{','}')])\n    return pair[0] + gen(n-1) + pair[1]\ns = gen(random.randint(1, 5))\nif random.random() > 0.5: s += random.choice('([{')\nprint(f'\"{s}\"')\nstack = []; mapping = {')': '(', '}': '{', ']': '['}; valid = 'true'\nfor char in s:\n    if char in mapping:\n        if not stack or stack.pop() != mapping[char]: valid = 'false'; break\n    else: stack.append(char)\nif stack: valid = 'false'\nprint(valid)"
        }
    },
    {
        title: "Reverse String",
        slug: "reverse-string",
        description: "Write a function that reverses a string. The input string is given as an array of characters `s`.",
        difficulty: "Easy",
        tags: ["Two Pointers", "String"],
        examples: [{ input: 's = ["h","e","l","l","o"]', output: '["o","l","l","e","h"]' }],
        constraints: ["1 <= s.length <= 10^5"],
        testCases: [
            { input: '["h","e","l","l","o"]', expectedOutput: '["o","l","l","e","h"]' },
            { input: '["H","a","n","n","a","h"]', expectedOutput: '["h","a","n","n","a","H"]' }
        ],
        starterCode: {
            python: "def reverse_string(s):\n    # Do not return anything, modify s in-place instead.\n    pass",
            javascript: "function reverseString(s) {\n    // Modify s in-place\n}"
        },
        testCaseGenerator: {
            python: "import random, json\ns = list(''.join(random.choices('abcdefghijklmnopqrstuvwxyz', k=random.randint(2, 10))))\nprint(json.dumps(s))\n# Simulate in-place modification\nrev = s[::-1]\nprint(json.dumps(rev))"
        }
    },
    {
        title: "Contains Duplicate",
        slug: "contains-duplicate",
        description: "Given an integer array `nums`, return `true` if any value appears at least twice in the array, and return `false` if every element is distinct.",
        difficulty: "Easy",
        tags: ["Array", "Hash Table"],
        examples: [{ input: "nums = [1,2,3,1]", output: "true" }],
        constraints: ["1 <= nums.length <= 10^5"],
        testCases: [
            { input: "[1,2,3,1]", expectedOutput: "true" },
            { input: "[1,2,3,4]", expectedOutput: "false" }
        ],
        starterCode: {
            python: "def contains_duplicate(nums):\n    # Your code here\n    pass",
            javascript: "function containsDuplicate(nums) {\n    // Your code here\n}"
        },
        testCaseGenerator: {
            python: "import random, json\nsize = random.randint(2, 20)\nnums = [random.randint(1, 50) for _ in range(size)]\nif random.random() > 0.5: nums[0] = nums[1]\nprint(json.dumps(nums))\nprint('true' if len(set(nums)) < len(nums) else 'false')"
        }
    }
];

mongoose.connect(process.env.MONGODB_URI).then(async () => {
    console.log('Cleaning existing challenges...');
    await Challenge.deleteMany({});

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let i = 0; i < challenges.length; i++) {
        const scheduledDate = new Date(today);
        scheduledDate.setDate(today.getDate() + i);
        
        await Challenge.create({
            ...challenges[i],
            scheduledDate
        });
        console.log(`Seeded: ${challenges[i].title} for ${scheduledDate.toDateString()}`);
    }

    console.log('Done!');
    process.exit(0);
});
