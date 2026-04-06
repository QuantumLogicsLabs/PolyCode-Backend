# JavaScript Async Programming

## Understanding Asynchronous JavaScript

### Synchronous vs Asynchronous
```javascript
// Synchronous code - executes line by line
console.log("Start");
console.log("Middle");
console.log("End");
// Output: Start, Middle, End

// Asynchronous code - doesn't block execution
console.log("Start");
setTimeout(() => {
    console.log("Async operation completed");
}, 1000);
console.log("End");
// Output: Start, End, Async operation completed (after 1 second)
```

### The Event Loop
```javascript
// Understanding the event loop
console.log("1");

setTimeout(() => {
    console.log("2"); // Callback queue
}, 0);

Promise.resolve().then(() => {
    console.log("3"); // Microtask queue
});

console.log("4");
// Output: 1, 4, 3, 2
// Explanation: Microtasks execute before macrotasks
```

## Callbacks

### Basic Callbacks
```javascript
// Simple callback function
function greet(name, callback) {
    console.log(`Hello, ${name}!`);
    callback();
}

function sayGoodbye() {
    console.log("Goodbye!");
}

greet("Alice", sayGoodbye);

// Callback with parameters
function calculate(a, b, operation, callback) {
    const result = operation(a, b);
    callback(result);
}

function add(a, b) {
    return a + b;
}

function displayResult(result) {
    console.log(`Result: ${result}`);
}

calculate(5, 3, add, displayResult);
```

### Callback Hell
```javascript
// Callback hell (pyramid of doom)
function getUser(id, callback) {
    setTimeout(() => {
        callback({ id: id, name: "John" });
    }, 1000);
}

function getPosts(user, callback) {
    setTimeout(() => {
        callback(["Post 1", "Post 2", "Post 3"]);
    }, 1000);
}

function getComments(post, callback) {
    setTimeout(() => {
        callback(["Comment 1", "Comment 2"]);
    }, 1000);
}

// Callback hell
getUser(1, function(user) {
    console.log("User:", user.name);
    getPosts(user, function(posts) {
        console.log("Posts:", posts);
        getComments(posts[0], function(comments) {
            console.log("Comments:", comments);
        });
    });
});
```

## Promises

### Creating Promises
```javascript
// Basic promise
const promise = new Promise((resolve, reject) => {
    // Asynchronous operation
    setTimeout(() => {
        const success = true;
        if (success) {
            resolve("Operation successful!");
        } else {
            reject("Operation failed!");
        }
    }, 1000);
});

// Consuming promises
promise
    .then(result => console.log(result))
    .catch(error => console.error(error))
    .finally(() => console.log("Operation completed"));

// Promise constructor with error handling
function fetchData(url) {
    return new Promise((resolve, reject) => {
        // Simulate API call
        setTimeout(() => {
            if (url.startsWith("https://")) {
                resolve(`Data from ${url}`);
            } else {
                reject(new Error("Invalid URL"));
            }
        }, 1000);
    });
}

fetchData("https://api.example.com/data")
    .then(data => console.log(data))
    .catch(error => console.error(error));
```

### Promise Methods
```javascript
// Promise.resolve() and Promise.reject()
const resolvedPromise = Promise.resolve("Already resolved");
const rejectedPromise = Promise.reject("Already rejected");

resolvedPromise.then(console.log); // "Already resolved"
rejectedPromise.catch(console.error); // "Already rejected"

// Promise.all() - all promises must resolve
const promise1 = Promise.resolve(1);
const promise2 = Promise.resolve(2);
const promise3 = Promise.resolve(3);

Promise.all([promise1, promise2, promise3])
    .then(results => console.log(results)) // [1, 2, 3]
    .catch(error => console.error(error));

// Promise.allSettled() - waits for all promises to settle
const promise4 = Promise.resolve(1);
const promise5 = Promise.reject("Error");
const promise6 = Promise.resolve(3);

Promise.allSettled([promise4, promise5, promise6])
    .then(results => {
        results.forEach(result => {
            if (result.status === 'fulfilled') {
                console.log('Fulfilled:', result.value);
            } else {
                console.log('Rejected:', result.reason);
            }
        });
    });

// Promise.race() - first promise to settle wins
const race1 = new Promise(resolve => setTimeout(() => resolve("Race 1"), 1000));
const race2 = new Promise(resolve => setTimeout(() => resolve("Race 2"), 500));

Promise.race([race1, race2])
    .then(winner => console.log(winner)); // "Race 2"

// Promise.any() - first promise to resolve wins
const any1 = Promise.reject("Error 1");
const any2 = Promise.resolve("Success 2");
const any3 = Promise.resolve("Success 3");

Promise.any([any1, any2, any3])
    .then(winner => console.log(winner)) // "Success 2"
    .catch(error => console.error(error));
```

### Chaining Promises
```javascript
// Promise chaining
function fetchUser(id) {
    return new Promise(resolve => {
        setTimeout(() => resolve({ id, name: "John" }), 1000);
    });
}

function fetchPosts(user) {
    return new Promise(resolve => {
        setTimeout(() => resolve([{ title: "Post 1" }, { title: "Post 2" }]), 1000);
    });
}

function fetchComments(post) {
    return new Promise(resolve => {
        setTimeout(() => resolve(["Comment 1", "Comment 2"]), 1000);
    });
}

// Clean promise chain
fetchUser(1)
    .then(user => {
        console.log("User:", user);
        return fetchPosts(user);
    })
    .then(posts => {
        console.log("Posts:", posts);
        return fetchComments(posts[0]);
    })
    .then(comments => {
        console.log("Comments:", comments);
    })
    .catch(error => {
        console.error("Error:", error);
    });
```

## Async/Await

### Basic Async/Await
```javascript
// Async function syntax
async function fetchData() {
    try {
        const response = await fetch('https://api.example.com/data');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// Using async function
fetchData()
    .then(data => console.log(data))
    .catch(error => console.error(error));

// Async arrow function
const fetchUserData = async (userId) => {
    try {
        const user = await fetchUser(userId);
        const posts = await fetchPosts(user);
        return { user, posts };
    } catch (error) {
        console.error('Error fetching user data:', error);
        throw error;
    }
};

// Helper functions for demonstration
function fetchUser(id) {
    return Promise.resolve({ id, name: "John" });
}

function fetchPosts(user) {
    return Promise.resolve([{ title: "Post 1" }]);
}
```

### Error Handling in Async/Await
```javascript
// Try-catch error handling
async function exampleWithErrorHandling() {
    try {
        const result = await riskyOperation();
        console.log("Success:", result);
        return result;
    } catch (error) {
        console.error("Error occurred:", error.message);
        return null;
    } finally {
        console.log("Operation completed");
    }
}

function riskyOperation() {
    return Promise.reject(new Error("Something went wrong"));
}

// Multiple error handling strategies
async function multipleStrategies() {
    // Strategy 1: Try-catch with fallback
    try {
        const data = await fetchData();
        return data;
    } catch (error) {
        console.log("Primary failed, using fallback");
        return { fallback: true };
    }
    
    // Strategy 2: Handle specific errors
    try {
        const data = await fetchData();
        return data;
    } catch (error) {
        if (error instanceof NetworkError) {
            console.log("Network error, retrying...");
            return await retryFetchData();
        } else {
            throw error;
        }
    }
}

// Custom error classes
class NetworkError extends Error {
    constructor(message) {
        super(message);
        this.name = "NetworkError";
    }
}

async function retryFetchData(maxRetries = 3) {
    for (let i = 0; i < maxRetries; i++) {
        try {
            return await fetchData();
        } catch (error) {
            if (i === maxRetries - 1) throw error;
            console.log(`Retry ${i + 1}/${maxRetries}`);
        }
    }
}
```

### Parallel Execution
```javascript
// Sequential vs parallel execution
async function sequentialExecution() {
    console.time("Sequential");
    
    const user = await fetchUser(1);
    const posts = await fetchPosts(user);
    const comments = await fetchComments(posts[0]);
    
    console.timeEnd("Sequential");
    return { user, posts, comments };
}

async function parallelExecution() {
    console.time("Parallel");
    
    const [user, posts, comments] = await Promise.all([
        fetchUser(1),
        fetchPosts({ id: 1 }),
        fetchComments({ title: "Post 1" })
    ]);
    
    console.timeEnd("Parallel");
    return { user, posts, comments };
}

// Parallel execution with dependencies
async function parallelWithDependencies() {
    // Fetch user and posts in parallel
    const [user, posts] = await Promise.all([
        fetchUser(1),
        fetchPosts({ id: 1 })
    ]);
    
    // Then fetch comments (depends on posts)
    const comments = await fetchComments(posts[0]);
    
    return { user, posts, comments };
}

// Concurrent execution with limited concurrency
async function limitedConcurrency(items, concurrency = 3) {
    const results = [];
    const executing = [];
    
    for (const item of items) {
        const promise = processItem(item).then(result => {
            results.push(result);
            executing.splice(executing.indexOf(promise), 1);
        });
        
        executing.push(promise);
        
        if (executing.length >= concurrency) {
            await Promise.race(executing);
        }
    }
    
    await Promise.all(executing);
    return results;
}

function processItem(item) {
    return new Promise(resolve => {
        setTimeout(() => resolve(`Processed ${item}`), 1000);
    });
}
```

## Fetch API

### Basic Fetch Usage
```javascript
// GET request
async function getData(url) {
    try {
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

// POST request
async function postData(url, data) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

// PUT request
async function updateData(url, data) {
    try {
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

// DELETE request
async function deleteData(url) {
    try {
        const response = await fetch(url, {
            method: 'DELETE'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.ok;
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}
```

### Advanced Fetch Options
```javascript
// Request with custom headers
async function fetchWithHeaders(url) {
    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer your-token',
            'Accept': 'application/json',
            'X-Custom-Header': 'custom-value'
        }
    });
    
    return response.json();
}

// Request with query parameters
function buildUrl(baseUrl, params) {
    const url = new URL(baseUrl);
    Object.keys(params).forEach(key => {
        url.searchParams.append(key, params[key]);
    });
    return url.toString();
}

async function fetchWithParams(baseUrl, params) {
    const url = buildUrl(baseUrl, params);
    const response = await fetch(url);
    return response.json();
}

// Request with timeout
async function fetchWithTimeout(url, timeout = 5000) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), timeout);
    
    try {
        const response = await fetch(url, {
            signal: controller.signal
        });
        clearTimeout(timeoutId);
        return response.json();
    } catch (error) {
        clearTimeout(timeoutId);
        if (error.name === 'AbortError') {
            throw new Error('Request timed out');
        }
        throw error;
    }
}

// Upload file
async function uploadFile(url, file) {
    const formData = new FormData();
    formData.append('file', file);
    
    const response = await fetch(url, {
        method: 'POST',
        body: formData
    });
    
    return response.json();
}

// Download file
async function downloadFile(url) {
    const response = await fetch(url);
    const blob = await response.blob();
    
    // Create download link
    const downloadUrl = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = 'filename.ext';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(downloadUrl);
}
```

## Web Workers

### Basic Web Worker
```javascript
// main.js
const worker = new Worker('worker.js');

worker.onmessage = function(event) {
    console.log('Worker said:', event.data);
};

worker.postMessage('Hello Worker!');

// worker.js
self.onmessage = function(event) {
    console.log('Main thread said:', event.data);
    
    // Perform heavy computation
    const result = heavyComputation(1000000);
    
    // Send result back to main thread
    self.postMessage(result);
};

function heavyComputation(n) {
    let sum = 0;
    for (let i = 0; i < n; i++) {
        sum += i;
    }
    return sum;
}
```

### Web Worker with Async Operations
```javascript
// main.js
const worker = new Worker('async-worker.js');

worker.onmessage = function(event) {
    const { type, data } = event.data;
    
    switch (type) {
        case 'progress':
            console.log('Progress:', data);
            break;
        case 'result':
            console.log('Result:', data);
            break;
        case 'error':
            console.error('Error:', data);
            break;
    }
};

worker.postMessage({
    type: 'start',
    data: { url: 'https://api.example.com/data' }
});

// async-worker.js
self.onmessage = async function(event) {
    const { type, data } = event.data;
    
    try {
        if (type === 'start') {
            // Simulate progress updates
            for (let i = 0; i <= 100; i += 10) {
                self.postMessage({
                    type: 'progress',
                    data: i
                });
                await new Promise(resolve => setTimeout(resolve, 100));
            }
            
            // Fetch data
            const response = await fetch(data.url);
            const result = await response.json();
            
            self.postMessage({
                type: 'result',
                data: result
            });
        }
    } catch (error) {
        self.postMessage({
            type: 'error',
            data: error.message
        });
    }
};
```

## Streams API

### Readable Streams
```javascript
// Reading from a stream
async function readStream(stream) {
    const reader = stream.getReader();
    const decoder = new TextDecoder();
    let result = '';
    
    try {
        while (true) {
            const { done, value } = await reader.read();
            
            if (done) {
                break;
            }
            
            result += decoder.decode(value, { stream: true });
        }
    } finally {
        reader.releaseLock();
    }
    
    return result;
}

// Fetch with streaming
async function fetchWithStreaming(url) {
    const response = await fetch(url);
    const reader = response.body.getReader();
    
    while (true) {
        const { done, value } = await reader.read();
        
        if (done) {
            break;
        }
        
        console.log('Received chunk:', value);
    }
}
```

### Writable Streams
```javascript
// Writing to a stream
async function writeToStream(stream, data) {
    const writer = stream.getWriter();
    const encoder = new TextEncoder();
    
    try {
        await writer.write(encoder.encode(data));
        await writer.close();
    } finally {
        writer.releaseLock();
    }
}

// Transform stream
class UppercaseTransform {
    constructor() {
        this.reader = new ReadableStream({
            start(controller) {
                this.controller = controller;
            }
        });
        
        this.writer = new WritableStream({
            write(chunk) {
                console.log('Transformed:', chunk.toUpperCase());
            }
        });
    }
    
    transform(chunk) {
        const upper = chunk.toUpperCase();
        this.controller.enqueue(upper);
    }
}
```

## Practical Examples

### API Client Class
```javascript
class ApiClient {
    constructor(baseURL, options = {}) {
        this.baseURL = baseURL;
        this.defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
            },
            ...options
        };
    }
    
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            ...this.defaultOptions,
            ...options,
            headers: {
                ...this.defaultOptions.headers,
                ...options.headers
            }
        };
        
        try {
            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`API Error: ${response.status} ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }
    
    async get(endpoint, params = {}) {
        const url = new URL(`${this.baseURL}${endpoint}`);
        Object.keys(params).forEach(key => {
            url.searchParams.append(key, params[key]);
        });
        
        return this.request(url);
    }
    
    async post(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }
    
    async put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }
    
    async delete(endpoint) {
        return this.request(endpoint, {
            method: 'DELETE'
        });
    }
}

// Usage
const api = new ApiClient('https://api.example.com');

async function exampleUsage() {
    try {
        const users = await api.get('/users');
        const newUser = await api.post('/users', {
            name: 'John',
            email: 'john@example.com'
        });
        const updatedUser = await api.put(`/users/${newUser.id}`, {
            name: 'John Doe'
        });
        await api.delete(`/users/${newUser.id}`);
    } catch (error) {
        console.error('API error:', error);
    }
}
```

### Caching Layer
```javascript
class CacheManager {
    constructor(maxSize = 100) {
        this.cache = new Map();
        this.maxSize = maxSize;
    }
    
    set(key, value, ttl = 300000) { // 5 minutes default TTL
        // Remove oldest item if cache is full
        if (this.cache.size >= this.maxSize) {
            const firstKey = this.cache.keys().next().value;
            this.cache.delete(firstKey);
        }
        
        this.cache.set(key, {
            value,
            expires: Date.now() + ttl
        });
    }
    
    get(key) {
        const item = this.cache.get(key);
        
        if (!item) {
            return null;
        }
        
        if (Date.now() > item.expires) {
            this.cache.delete(key);
            return null;
        }
        
        return item.value;
    }
    
    has(key) {
        return this.get(key) !== null;
    }
    
    delete(key) {
        return this.cache.delete(key);
    }
    
    clear() {
        this.cache.clear();
    }
}

// Cached API client
class CachedApiClient extends ApiClient {
    constructor(baseURL, options = {}) {
        super(baseURL, options);
        this.cache = new CacheManager();
    }
    
    async get(endpoint, params = {}, useCache = true) {
        const cacheKey = `${endpoint}:${JSON.stringify(params)}`;
        
        if (useCache && this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }
        
        const data = await super.get(endpoint, params);
        
        if (useCache) {
            this.cache.set(cacheKey, data);
        }
        
        return data;
    }
}
```

### Retry Mechanism
```javascript
class RetryManager {
    static async execute(fn, options = {}) {
        const {
            maxRetries = 3,
            delay = 1000,
            backoff = 2,
            retryCondition = () => true
        } = options;
        
        let lastError;
        
        for (let attempt = 1; attempt <= maxRetries; attempt++) {
            try {
                return await fn();
            } catch (error) {
                lastError = error;
                
                if (attempt === maxRetries || !retryCondition(error)) {
                    throw error;
                }
                
                const waitTime = delay * Math.pow(backoff, attempt - 1);
                console.log(`Attempt ${attempt} failed, retrying in ${waitTime}ms...`);
                
                await new Promise(resolve => setTimeout(resolve, waitTime));
            }
        }
        
        throw lastError;
    }
}

// Usage with API client
class RobustApiClient extends ApiClient {
    async request(endpoint, options = {}) {
        return RetryManager.execute(
            () => super.request(endpoint, options),
            {
                maxRetries: 3,
                delay: 1000,
                retryCondition: (error) => 
                    error.message.includes('network') || 
                    error.message.includes('timeout')
            }
        );
    }
}
```

## Best Practices

### Error Handling Best Practices
```javascript
// 1. Always handle promise rejections
function badPromiseHandling() {
    fetchData().then(data => {
        console.log(data);
    });
    // Missing .catch() - unhandled rejection
}

function goodPromiseHandling() {
    fetchData()
        .then(data => console.log(data))
        .catch(error => console.error(error));
}

// 2. Use try-catch with async/await
async function badAsyncHandling() {
    const data = await fetchData(); // No error handling
    console.log(data);
}

async function goodAsyncHandling() {
    try {
        const data = await fetchData();
        console.log(data);
    } catch (error) {
        console.error(error);
    }
}

// 3. Handle specific errors
async function specificErrorHandling() {
    try {
        const data = await fetchData();
        return data;
    } catch (error) {
        if (error instanceof NetworkError) {
            console.error('Network error:', error.message);
        } else if (error instanceof ValidationError) {
            console.error('Validation error:', error.message);
        } else {
            console.error('Unknown error:', error.message);
        }
        throw error;
    }
}

// 4. Provide fallback values
async function withFallback() {
    try {
        return await fetchData();
    } catch (error) {
        console.error('Primary source failed, using fallback');
        return await fetchFallbackData();
    }
}

// 5. Timeout handling
async function withTimeout() {
    const timeoutPromise = new Promise((_, reject) => {
        setTimeout(() => reject(new Error('Timeout')), 5000);
    });
    
    try {
        return await Promise.race([fetchData(), timeoutPromise]);
    } catch (error) {
        if (error.message === 'Timeout') {
            console.error('Request timed out');
            return null;
        }
        throw error;
    }
}
```

### Performance Best Practices
```javascript
// 1. Avoid unnecessary awaits
async function sequential(data) {
    // Bad: sequential execution
    const result1 = await processItem(data[0]);
    const result2 = await processItem(data[1]);
    const result3 = await processItem(data[2]);
    return [result1, result2, result3];
}

async function parallel(data) {
    // Good: parallel execution
    const [result1, result2, result3] = await Promise.all([
        processItem(data[0]),
        processItem(data[1]),
        processItem(data[2])
    ]);
    return [result1, result2, result3];
}

// 2. Use concurrency limits
async function limitedConcurrency(items, limit = 5) {
    const results = [];
    const executing = [];
    
    for (const item of items) {
        const promise = processItem(item).then(result => {
            results.push(result);
            executing.splice(executing.indexOf(promise), 1);
        });
        
        executing.push(promise);
        
        if (executing.length >= limit) {
            await Promise.race(executing);
        }
    }
    
    await Promise.all(executing);
    return results;
}

// 3. Cache results when appropriate
class CachedAsyncFunction {
    constructor() {
        this.cache = new Map();
    }
    
    async execute(key, fn) {
        if (this.cache.has(key)) {
            return this.cache.get(key);
        }
        
        const result = await fn();
        this.cache.set(key, result);
        return result;
    }
}

// 4. Stream large responses
async function streamLargeResponse(url) {
    const response = await fetch(url);
    const reader = response.body.getReader();
    
    while (true) {
        const { done, value } = await reader.read();
        
        if (done) break;
        
        // Process chunk
        processChunk(value);
    }
}
```

## Common Pitfalls

### Common Async Mistakes
```javascript
// 1. Mixing callbacks and promises
function mixedApproach() {
    // Bad: mixing paradigms
    someAsyncFunction((result) => {
        Promise.resolve(result)
            .then(data => console.log(data));
    });
    
    // Good: stick to one paradigm
    someAsyncFunction()
        .then(result => console.log(result));
}

// 2. Forgetting await in async functions
async function missingAwait() {
    // Bad: forgetting await
    const result = fetchData(); // Returns promise, not data
    console.log(result); // Logs promise object
    
    // Good: use await
    const data = await fetchData();
    console.log(data);
}

// 3. Creating async functions unnecessarily
function unnecessaryAsync() {
    // Bad: async for no reason
    return await Promise.resolve(42);
}

function betterApproach() {
    // Good: return promise directly
    return Promise.resolve(42);
}

// 4. Not handling promise rejections in Promise.all
function badPromiseAll() {
    // Bad: if any promise rejects, all fail
    return Promise.all([
        fetchData(),
        fetchMoreData(),
        fetchEvenMoreData()
    ]);
}

function goodPromiseAllSettled() {
    // Good: handle all results
    return Promise.allSettled([
        fetchData(),
        fetchMoreData(),
        fetchEvenMoreData()
    ]);
}

// 5. Memory leaks with event listeners
function memoryLeak() {
    // Bad: event listeners not cleaned up
    element.addEventListener('click', handler);
}

function noMemoryLeak() {
    // Good: cleanup event listeners
    const handler = () => console.log('clicked');
    element.addEventListener('click', handler);
    
    // Later...
    element.removeEventListener('click', handler);
}
```

## Summary

JavaScript async programming provides powerful tools for non-blocking operations:

**Core Concepts:**
- Event loop and execution order
- Callbacks and callback hell
- Promises for better error handling
- Async/await for readable code

**Promise Features:**
- Creation and consumption
- Chaining and composition
- `Promise.all()`, `Promise.race()`, `Promise.any()`
- Error handling with `.catch()`

**Async/Await:**
- Clean syntax for promises
- Try-catch error handling
- Parallel execution with `Promise.all()`
- Sequential execution with multiple awaits

**API Integration:**
- Fetch API for HTTP requests
- Request/response handling
- Error handling and retries
- File uploads and downloads

**Advanced Patterns:**
- Web Workers for background processing
- Streams API for data processing
- Caching mechanisms
- Retry strategies and timeouts

**Best Practices:**
- Always handle errors
- Use Promise.allSettled for independent operations
- Implement proper timeout handling
- Cache results when appropriate
- Use concurrency limits for many operations

**Common Pitfalls:**
- Mixing callbacks and promises
- Forgetting await in async functions
- Not handling promise rejections
- Creating unnecessary async functions
- Memory leaks with event listeners

Async programming is essential for modern JavaScript applications, enabling responsive user interfaces and efficient resource utilization.
