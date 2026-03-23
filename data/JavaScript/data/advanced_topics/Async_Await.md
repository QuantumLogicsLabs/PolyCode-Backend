# Async/Await in JavaScript

## Overview
Asynchronous programming is fundamental to JavaScript, especially for network requests or time-consuming tasks. `async` and `await` provide a clean, readable way to handle asynchronous code that looks and behaves like synchronous code.

## Key Concepts
1.  **`Promise`**: An object representing the eventual completion (or failure) of an asynchronous operation and its resulting value.
2.  **`async` function**: A function that always returns a promise. It allows the use of the `await` keyword within its body.
3.  **`await`**: Pauses the execution of the `async` function until the promise is settled (resolved or rejected).
4.  **`try...catch`**: The standard way to handle errors in `async` functions.

## Basic Syntax
```js
async function fetchData() {
    try {
        const response = await fetch('https://api.example.com/data');
        const data = await response.json();
        console.log(data);
    } catch (error) {
        console.error('Failed to fetch:', error);
    }
}
```

[01) Async Await.js](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/JavaScript/data/10)%20Advanced%20Topics/01)%20Async%20Await.js)