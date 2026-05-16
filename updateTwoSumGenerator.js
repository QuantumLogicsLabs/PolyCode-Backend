require('dotenv').config();
const mongoose = require('mongoose');
const Challenge = require('./src/models/challenge');

const generatorCode = `
import random
import json

def generate_two_sum():
    # Constraints: 2 <= nums.length <= 50, values -100 to 100
    size = random.randint(2, 50)
    # Use a set to keep numbers unique for simplicity in generating a single target
    nums = random.sample(range(-100, 100), size)
    
    # Pick two random indices
    i, j = random.sample(range(size), 2)
    target = nums[i] + nums[j]
    
    # Python indices are 0-based
    expected = sorted([i, j])
    
    # Output format:
    # nums_as_json
    # target
    # expected_as_json
    print(json.dumps(nums))
    print(target)
    print(json.dumps(expected))

generate_two_sum()
`;

mongoose.connect(process.env.MONGODB_URI).then(async () => {
    const result = await Challenge.findOneAndUpdate(
        { slug: 'two-sum' },
        { testCaseGenerator: { python: generatorCode } },
        { new: true }
    );
    if (result) {
        console.log('Successfully added generator to Two Sum!');
    } else {
        console.log('Challenge not found.');
    }
    process.exit(0);
});
