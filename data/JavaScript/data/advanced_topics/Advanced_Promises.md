# Advanced Promises in JavaScript

## Overview
**Advanced Promises** are powerful methods for handling multiple asynchronous operations simultaneously. While basic `async/await` is great for simple sequential operations, these methods allow for complex and highly efficient concurrency patterns.

## Key Promise Methods
1.  **`Promise.all(promises)`**:
    - **Wait for All Success**: Returns a single promise that resolves when all the input promises have resolved.
    - **Fails Fast**: If any promise in the list rejects, the entire `Promise.all` immediately rejects with that error.
    - **Use Case**: Fetching multiple related datasets (e.g., user info and their posts).
2.  **`Promise.race(promises)`**:
    - **First to Settle**: Returns a promise that resolves or rejects as soon as *any* of the input promises settle.
    - **Winner Takes All**: The result is the value (or error) from the first promise to finish.
    - **Use Case**: Implementing timeouts for network requests.
3.  **`Promise.allSettled(promises)`**:
    - **Wait for All**: Returns a promise that resolves after all the input promises have settled (either resolved or rejected).
    - **Detailed Results**: The result is an array of objects describing the status and value/reason for each promise.
    - **Use Case**: Performing multiple independent operations where you need to know the result of each, regardless of success.
4.  **`Promise.any(promises)`**:
    - **First to Resolve**: Returns a promise that resolves as soon as *any* of the input promises resolve.
    - **Ignore Rejections**: It only rejects if *all* input promises reject.
    - **Use Case**: Requesting the same data from multiple mirrors and taking the first successful result.

## Basic Syntax
```js
// Promise.all
const results = await Promise.all([p1, p2, p3]);

// Promise.race
const fastest = await Promise.race([p1, p2]);

// Promise.allSettled
const statuses = await Promise.allSettled([p1, p2]);
```

## Best Practices
- Use `Promise.all` for dependent operations that must all succeed.
- Use `Promise.allSettled` for independent operations where failure of one shouldn't stop the others.
- Be aware that `Promise.race` and `Promise.any` can leave some promises "dangling" (still running in the background) even after the main promise has settled.

[03) Advanced Promises.js](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/JavaScript/data/10)%20Advanced%20Topics/03)%20Advanced%20Promises.js)