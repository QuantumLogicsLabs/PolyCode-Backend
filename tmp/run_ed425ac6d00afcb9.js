/**
 * Advanced Promises in JavaScript
 * This tutorial covers Promise.all, Promise.race, and Promise.allSettled.
 */

// Simulated async operations
const fetchUser = (id) => new Promise((resolve) => {
    setTimeout(() => resolve({ id, name: "User " + id }), 1000);
});

const fetchPosts = (id) => new Promise((resolve) => {
    setTimeout(() => resolve(["Post 1", "Post 2", "Post 3"]), 1500);
});

const fetchComments = (id) => new Promise((_, reject) => {
    setTimeout(() => reject("Comments API failed"), 500);
});

async function main() {
    console.log("--- Promise.all ---");
    // Runs multiple promises in parallel and waits for all to succeed
    try {
        const results = await Promise.all([fetchUser(1), fetchPosts(1)]);
        console.log("Results:", results);
    } catch (error) {
        console.error("Error in Promise.all:", error);
    }

    console.log("\n--- Promise.race ---");
    // Returns the result of the first promise to settle (either resolve or reject)
    try {
        const fastest = await Promise.race([fetchUser(1), fetchComments(1)]);
        console.log("Fastest result:", fastest);
    } catch (error) {
        console.error("Error in Promise.race:", error);
    }

    console.log("\n--- Promise.allSettled ---");
    // Returns the status and value/reason for all promises, even if some fail
    const statuses = await Promise.allSettled([fetchUser(1), fetchComments(1)]);
    console.log("Statuses:", statuses);

    console.log("\n--- Promise.any ---");
    // Returns the result of the first promise to resolve (ignoring rejections)
    try {
        const anySuccess = await Promise.any([fetchComments(1), fetchUser(1)]);
        console.log("First successful result:", anySuccess);
    } catch (error) {
        console.error("Error in Promise.any:", error);
    }
}

main();