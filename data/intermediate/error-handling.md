# JavaScript Error Handling

## Error Types

### Built-in Error Types
```javascript
// Error - Base error class
const error = new Error('This is a generic error');
console.log(error.name); // "Error"
console.log(error.message); // "This is a generic error"

// TypeError - Invalid type operation
try {
    const result = null.toUpperCase();
} catch (error) {
    console.log(error.name); // "TypeError"
    console.log(error.message); // "Cannot read property 'toUpperCase' of null"
}

// ReferenceError - Invalid reference
try {
    console.log(undefinedVariable);
} catch (error) {
    console.log(error.name); // "ReferenceError"
    console.log(error.message); // "undefinedVariable is not defined"
}

// RangeError - Value out of range
try {
    const array = new Array(-1);
} catch (error) {
    console.log(error.name); // "RangeError"
    console.log(error.message); // "Invalid array length"
}

// SyntaxError - Invalid syntax
try {
    eval('const x = ;');
} catch (error) {
    console.log(error.name); // "SyntaxError"
    console.log(error.message); // "Unexpected token ';'"
}

// URIError - URI encoding/decoding error
try {
    decodeURIComponent('%E0%A4%A1');
} catch (error) {
    console.log(error.name); // "URIError"
    console.log(error.message); // "URI malformed"
}

// EvalError - Error in eval() (rare in modern JS)
try {
    eval('throw new Error("Eval error")');
} catch (error) {
    console.log(error.name); // "Error"
    console.log(error.message); // "Eval error"
}
```

### Custom Error Classes
```javascript
// Creating custom error classes
class ValidationError extends Error {
    constructor(message, field) {
        super(message);
        this.name = 'ValidationError';
        this.field = field;
    }
}

class NetworkError extends Error {
    constructor(message, statusCode) {
        super(message);
        this.name = 'NetworkError';
        this.statusCode = statusCode;
    }
}

class DatabaseError extends Error {
    constructor(message, query) {
        super(message);
        this.name = 'DatabaseError';
        this.query = query;
    }
}

// Using custom errors
function validateEmail(email) {
    if (!email) {
        throw new ValidationError('Email is required', 'email');
    }
    
    if (!email.includes('@')) {
        throw new ValidationError('Invalid email format', 'email');
    }
    
    return true;
}

function makeApiCall(url) {
    if (!url.startsWith('https://')) {
        throw new NetworkError('HTTPS required', 400);
    }
    
    return 'API response';
}

try {
    validateEmail('invalid-email');
} catch (error) {
    if (error instanceof ValidationError) {
        console.error(`Validation failed for field '${error.field}': ${error.message}`);
    }
}

try {
    makeApiCall('http://example.com');
} catch (error) {
    if (error instanceof NetworkError) {
        console.error(`Network error (${error.statusCode}): ${error.message}`);
    }
}
```

## Try-Catch-Finally

### Basic Error Handling
```javascript
// Basic try-catch
function divide(a, b) {
    try {
        if (b === 0) {
            throw new Error('Division by zero');
        }
        return a / b;
    } catch (error) {
        console.error('Error:', error.message);
        return null;
    }
}

console.log(divide(10, 2)); // 5
console.log(divide(10, 0)); // null, logs error

// Try-catch-finally
function processFile(filename) {
    let fileContent;
    
    try {
        // Simulate file reading
        if (filename === 'nonexistent.txt') {
            throw new Error('File not found');
        }
        
        fileContent = 'File content';
        console.log('File processed successfully');
        
        return fileContent;
        
    } catch (error) {
        console.error('File processing failed:', error.message);
        fileContent = 'Default content';
        return fileContent;
        
    } finally {
        console.log('File processing completed');
        // Cleanup code here
    }
}

console.log(processFile('existing.txt')); // File processed successfully, File processing completed
console.log(processFile('nonexistent.txt')); // File processing failed, File processing completed
```

### Nested Try-Catch
```javascript
// Nested error handling
function complexOperation(data) {
    try {
        // First operation
        const result1 = validateData(data);
        
        try {
            // Second operation
            const result2 = processData(result1);
            
            try {
                // Third operation
                const result3 = saveData(result2);
                return result3;
                
            } catch (saveError) {
                console.error('Save failed:', saveError.message);
                throw new Error('Data saving failed');
            }
            
        } catch (processError) {
            console.error('Processing failed:', processError.message);
            throw new Error('Data processing failed');
        }
        
    } catch (validationError) {
        console.error('Validation failed:', validationError.message);
        throw new Error('Data validation failed');
    }
}

function validateData(data) {
    if (!data) {
        throw new Error('No data provided');
    }
    return { ...data, validated: true };
}

function processData(data) {
    if (!data.validated) {
        throw new Error('Data not validated');
    }
    return { ...data, processed: true };
}

function saveData(data) {
    if (!data.processed) {
        throw new Error('Data not processed');
    }
    return { ...data, saved: true };
}

try {
    const result = complexOperation({ data: 'test' });
    console.log(result);
} catch (error) {
    console.error('Operation failed:', error.message);
}
```

## Error Handling Patterns

### Centralized Error Handling
```javascript
// Error handler class
class ErrorHandler {
    constructor() {
        this.errorLog = [];
    }
    
    log(error, context = {}) {
        const errorInfo = {
            timestamp: new Date().toISOString(),
            message: error.message,
            stack: error.stack,
            context,
            type: error.constructor.name
        };
        
        this.errorLog.push(errorInfo);
        console.error('Error logged:', errorInfo);
    }
    
    handle(error, context = {}) {
        this.log(error, context);
        
        // Different handling based on error type
        if (error instanceof ValidationError) {
            return this.handleValidationError(error, context);
        } else if (error instanceof NetworkError) {
            return this.handleNetworkError(error, context);
        } else if (error instanceof DatabaseError) {
            return this.handleDatabaseError(error, context);
        } else {
            return this.handleGenericError(error, context);
        }
    }
    
    handleValidationError(error, context) {
        console.error('Validation Error:', error.message);
        return {
            success: false,
            error: 'Validation failed',
            field: error.field
        };
    }
    
    handleNetworkError(error, context) {
        console.error('Network Error:', error.message);
        return {
            success: false,
            error: 'Network request failed',
            statusCode: error.statusCode
        };
    }
    
    handleDatabaseError(error, context) {
        console.error('Database Error:', error.message);
        return {
            success: false,
            error: 'Database operation failed',
            query: error.query
        };
    }
    
    handleGenericError(error, context) {
        console.error('Generic Error:', error.message);
        return {
            success: false,
            error: 'Unexpected error occurred'
        };
    }
}

// Usage
const errorHandler = new ErrorHandler();

function riskyOperation(data) {
    try {
        if (!data.email) {
            throw new ValidationError('Email is required', 'email');
        }
        
        return { success: true, data };
        
    } catch (error) {
        return errorHandler.handle(error, { operation: 'riskyOperation' });
    }
}

console.log(riskyOperation({}));
console.log(riskyOperation({ email: 'test@example.com' }));
```

### Error Boundaries (React-like pattern)
```javascript
// Error boundary pattern for handling errors in components
class ErrorBoundary {
    constructor() {
        this.hasError = false;
        this.error = null;
        this.errorInfo = null;
    }
    
    execute(callback) {
        try {
            this.hasError = false;
            this.error = null;
            this.errorInfo = null;
            
            return callback();
            
        } catch (error) {
            this.hasError = true;
            this.error = error;
            this.errorInfo = {
                message: error.message,
                stack: error.stack,
                timestamp: new Date().toISOString()
            };
            
            return this.handleError(error);
        }
    }
    
    handleError(error) {
        console.error('Error caught by boundary:', error);
        
        // Fallback behavior
        return {
            success: false,
            error: 'Component error occurred',
            timestamp: this.errorInfo.timestamp
        };
    }
    
    reset() {
        this.hasError = false;
        this.error = null;
        this.errorInfo = null;
    }
}

// Usage
const errorBoundary = new ErrorBoundary();

function componentA(data) {
    if (!data.required) {
        throw new Error('Required data missing');
    }
    return { success: true, data };
}

function componentB(data) {
    if (data.value < 0) {
        throw new Error('Invalid value');
    }
    return { success: true, processed: data.value * 2 };
}

// Execute with error boundary
const result1 = errorBoundary.execute(() => componentA({ required: true }));
console.log(result1);

const result2 = errorBoundary.execute(() => componentA({}));
console.log(result2);

// Reset boundary and try again
errorBoundary.reset();
const result3 = errorBoundary.execute(() => componentA({ required: true }));
console.log(result3);
```

## Promise Error Handling

### Promise Error Handling Patterns
```javascript
// Promise rejection handling
function fetchData(url) {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            if (url.startsWith('https://')) {
                resolve({ url, data: 'success' });
            } else {
                reject(new Error('Invalid URL protocol'));
            }
        }, 1000);
    });
}

// Basic promise error handling
fetchData('http://example.com')
    .then(data => console.log(data))
    .catch(error => console.error('Promise rejected:', error.message));

// Multiple promises with error handling
function fetchMultipleData(urls) {
    const promises = urls.map(url => 
        fetchData(url)
            .catch(error => {
                console.error(`Failed to fetch ${url}:`, error.message);
                return { url, error: error.message, failed: true };
            })
    );
    
    return Promise.all(promises);
}

fetchMultipleData([
    'https://api.example.com/users',
    'http://invalid.example.com/data',
    'https://api.example.com/posts'
]).then(results => {
    console.log('Results:', results);
});
```

### Async/Await Error Handling
```javascript
// Async/await error handling
async function fetchUserData(userId) {
    try {
        const user = await fetchUser(userId);
        const posts = await fetchUserPosts(userId);
        const profile = await fetchUserProfile(userId);
        
        return {
            user,
            posts,
            profile
        };
        
    } catch (error) {
        console.error('Failed to fetch user data:', error.message);
        
        // Fallback data
        return {
            user: null,
            posts: [],
            profile: null,
            error: error.message
        };
    }
}

// Helper functions
async function fetchUser(id) {
    // Simulate API call
    if (id === 999) {
        throw new Error('User not found');
    }
    return { id, name: 'John' };
}

async function fetchUserPosts(userId) {
    return [{ title: 'Post 1' }, { title: 'Post 2' }];
}

async function fetchUserProfile(userId) {
    return { bio: 'User bio', avatar: 'avatar.jpg' };
}

// Usage
fetchUserData(1).then(data => console.log(data));
fetchUserData(999).then(data => console.log(data));
```

### Promise Error Handling Utilities
```javascript
// Utility functions for promise error handling
class PromiseUtils {
    static withTimeout(promise, timeout) {
        return Promise.race([
            promise,
            new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Timeout')), timeout)
            )
        ]);
    }
    
    static withRetry(promiseFn, maxRetries = 3, delay = 1000) {
        return new Promise((resolve, reject) => {
            let attempts = 0;
            
            const tryAgain = () => {
                attempts++;
                
                promiseFn()
                    .then(resolve)
                    .catch(error => {
                        if (attempts >= maxRetries) {
                            reject(error);
                        } else {
                            console.log(`Attempt ${attempts} failed, retrying in ${delay}ms...`);
                            setTimeout(tryAgain, delay);
                        }
                    });
            };
            
            tryAgain();
        });
    }
    
    static allSettled(promises) {
        return Promise.allSettled(promises);
    }
    
    static any(promises) {
        return Promise.any(promises);
    }
}

// Usage examples
async function exampleUsage() {
    // With timeout
    try {
        const result = await PromiseUtils.withTimeout(
            fetchData('https://api.example.com'),
            2000
        );
        console.log('Success:', result);
    } catch (error) {
        console.error('Timeout or error:', error.message);
    }
    
    // With retry
    try {
        const result = await PromiseUtils.withRetry(
            () => fetchData('https://api.example.com'),
            3,
            1000
        );
        console.log('Success with retry:', result);
    } catch (error) {
        console.error('Failed after retries:', error.message);
    }
}
```

## Global Error Handling

### Window Error Handlers
```javascript
// Global error handlers (browser environment)
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
    console.error('Filename:', event.filename);
    console.error('Line:', event.lineno);
    console.error('Column:', event.colno);
    
    // Prevent default browser error dialog
    event.preventDefault();
});

// Unhandled promise rejection handler
window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
    
    // Prevent default console warning
    event.preventDefault();
});

// Rejection handled handler
window.addEventListener('rejectionhandled', (event) => {
    console.log('Promise rejection was handled:', event.reason);
});

// Node.js global error handlers (if running in Node.js)
if (typeof process !== 'undefined') {
    process.on('uncaughtException', (error) => {
        console.error('Uncaught exception:', error);
        process.exit(1);
    });
    
    process.on('unhandledRejection', (reason, promise) => {
        console.error('Unhandled rejection at:', promise, 'reason:', reason);
        process.exit(1);
    });
}
```

### Error Reporting Service
```javascript
// Error reporting service
class ErrorReportingService {
    constructor(apiUrl, apiKey) {
        this.apiUrl = apiUrl;
        this.apiKey = apiKey;
        this.queue = [];
        this.isOnline = navigator.onLine;
    }
    
    report(error, context = {}) {
        const errorReport = {
            message: error.message,
            stack: error.stack,
            type: error.constructor.name,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent,
            url: window.location.href,
            context,
            severity: this.determineSeverity(error)
        };
        
        if (this.isOnline) {
            this.sendReport(errorReport);
        } else {
            this.queue.push(errorReport);
        }
    }
    
    determineSeverity(error) {
        if (error instanceof CriticalError) {
            return 'critical';
        } else if (error instanceof WarningError) {
            return 'warning';
        } else {
            return 'error';
        }
    }
    
    async sendReport(report) {
        try {
            const response = await fetch(`${this.apiUrl}/errors`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.apiKey}`
                },
                body: JSON.stringify(report)
            });
            
            if (!response.ok) {
                throw new Error('Failed to send error report');
            }
            
            return await response.json();
            
        } catch (error) {
            console.error('Failed to report error:', error);
            // Add back to queue for retry
            this.queue.push(report);
        }
    }
    
    flushQueue() {
        if (this.queue.length > 0 && this.isOnline) {
            const reports = [...this.queue];
            this.queue = [];
            
            return Promise.all(
                reports.map(report => this.sendReport(report))
            );
        }
    }
    
    // Listen for online/offline status
    setupNetworkListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.flushQueue();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
        });
    }
}

// Custom error types for severity
class CriticalError extends Error {
    constructor(message) {
        super(message);
        this.name = 'CriticalError';
    }
}

class WarningError extends Error {
    constructor(message) {
        super(message);
        this.name = 'WarningError';
    }
}

// Usage
const errorReporter = new ErrorReportingService(
    'https://api.example.com',
    'your-api-key'
);

errorReporter.setupNetworkListeners();

try {
    riskyOperation();
} catch (error) {
    errorReporter.report(error, {
        component: 'UserForm',
        action: 'submit'
    });
}
```

## Debugging Error Handling

### Error Logging and Debugging
```javascript
// Advanced error logging
class ErrorLogger {
    constructor(options = {}) {
        this.options = {
            logLevel: 'error', // debug, info, warn, error
            includeStackTrace: true,
            includeContext: true,
            maxLogSize: 1000,
            ...options
        };
        
        this.logs = [];
        this.levels = {
            debug: 0,
            info: 1,
            warn: 2,
            error: 3
        };
    }
    
    log(level, message, error = null, context = {}) {
        const logLevel = this.levels[level];
        const configLevel = this.levels[this.options.logLevel];
        
        if (logLevel < configLevel) {
            return;
        }
        
        const logEntry = {
            timestamp: new Date().toISOString(),
            level,
            message,
            error: error ? {
                name: error.name,
                message: error.message,
                stack: this.options.includeStackTrace ? error.stack : null
            } : null,
            context: this.options.includeContext ? context : null
        };
        
        this.logs.push(logEntry);
        
        // Maintain log size
        if (this.logs.length > this.options.maxLogSize) {
            this.logs.shift();
        }
        
        // Output to console
        this.outputToConsole(logEntry);
    }
    
    outputToConsole(logEntry) {
        const { timestamp, level, message, error, context } = logEntry;
        const logMessage = `[${timestamp}] ${level.toUpperCase()}: ${message}`;
        
        switch (level) {
            case 'debug':
                console.debug(logMessage, error, context);
                break;
            case 'info':
                console.info(logMessage, error, context);
                break;
            case 'warn':
                console.warn(logMessage, error, context);
                break;
            case 'error':
                console.error(logMessage, error, context);
                break;
        }
    }
    
    debug(message, context) {
        this.log('debug', message, null, context);
    }
    
    info(message, context) {
        this.log('info', message, null, context);
    }
    
    warn(message, context) {
        this.log('warn', message, null, context);
    }
    
    error(message, error, context) {
        this.log('error', message, error, context);
    }
    
    getLogs(level = null) {
        if (level) {
            return this.logs.filter(log => log.level === level);
        }
        return [...this.logs];
    }
    
    exportLogs() {
        return JSON.stringify(this.logs, null, 2);
    }
}

// Usage
const logger = new ErrorLogger({ logLevel: 'debug' });

function debugOperation(data) {
    logger.debug('Starting operation', { data });
    
    try {
        // Simulate operation
        if (data.value < 0) {
            throw new Error('Value cannot be negative');
        }
        
        const result = data.value * 2;
        logger.info('Operation completed', { result });
        return result;
        
    } catch (error) {
        logger.error('Operation failed', error, { data });
        throw error;
    }
}

try {
    debugOperation({ value: -5 });
} catch (error) {
    console.log('Caught error:', error.message);
}
```

## Best Practices

### Error Handling Best Practices
```javascript
// 1. Be specific with error types
function specificErrorHandling() {
    try {
        riskyOperation();
    } catch (error) {
        if (error instanceof ValidationError) {
            handleValidationError(error);
        } else if (error instanceof NetworkError) {
            handleNetworkError(error);
        } else {
            handleGenericError(error);
        }
    }
}

// 2. Always provide meaningful error messages
function meaningfulErrors() {
    // Bad: generic error message
    throw new Error('Something went wrong');
    
    // Good: specific error message
    throw new ValidationError('Email address is invalid. Expected format: user@domain.com');
}

// 3. Handle errors at appropriate levels
function handleAtRightLevel() {
    // Handle errors at the level where you can either:
    // - Fix the problem
    // - Provide a meaningful fallback
    // - Pass it up with additional context
    
    try {
        processData();
    } catch (error) {
        if (canFix(error)) {
            fixAndRetry();
        } else {
            throw new Error('Processing failed', { cause: error });
        }
    }
}

// 4. Use finally for cleanup
function useFinally() {
    let resource;
    
    try {
        resource = acquireResource();
        useResource(resource);
    } catch (error) {
        console.error('Error:', error);
    } finally {
        if (resource) {
            releaseResource(resource);
        }
    }
}

// 5. Don't swallow errors silently
function dontSilentErrors() {
    // Bad: silently ignore errors
    try {
        riskyOperation();
    } catch (error) {
        // Do nothing
    }
    
    // Good: log or handle errors
    try {
        riskyOperation();
    } catch (error) {
        console.error('Operation failed:', error);
        // Handle appropriately
    }
}

// 6. Use async/await with proper error handling
async function properAsyncHandling() {
    try {
        const result = await asyncOperation();
        return result;
    } catch (error) {
        console.error('Async operation failed:', error);
        throw error; // Re-throw if you can't handle it
    }
}

// 7. Create custom error classes for domain-specific errors
class DomainSpecificError extends Error {
    constructor(message, code, details) {
        super(message);
        this.name = 'DomainSpecificError';
        this.code = code;
        this.details = details;
    }
}

// 8. Implement error boundaries for components
function useErrorBoundaries() {
    const errorBoundary = new ErrorBoundary();
    
    return errorBoundary.execute(() => {
        // Component logic
    });
}

// 9. Provide fallback behavior
function provideFallback() {
    try {
        return primaryOperation();
    } catch (error) {
        console.warn('Primary operation failed, using fallback:', error);
        return fallbackOperation();
    }
}

// 10. Log errors for debugging
function logForDebugging(error, context) {
    const logData = {
        timestamp: new Date().toISOString(),
        error: {
            message: error.message,
            stack: error.stack,
            type: error.constructor.name
        },
        context,
        userAgent: navigator.userAgent,
        url: window.location.href
    };
    
    // Send to logging service
    sendToLoggingService(logData);
}
```

## Common Pitfalls

### Common Error Handling Mistakes
```javascript
// 1. Not handling promise rejections
function unhandledPromise() {
    // Bad: no error handling
    fetch('/api/data').then(data => console.log(data));
    
    // Good: handle errors
    fetch('/api/data')
        .then(data => console.log(data))
        .catch(error => console.error('Fetch error:', error));
}

// 2. Throwing non-Error objects
function throwNonError() {
    // Bad: throwing strings
    throw 'Something went wrong';
    
    // Good: throw Error objects
    throw new Error('Something went wrong');
}

// 3. Catching all errors with generic handler
function genericCatch() {
    try {
        riskyOperation();
    } catch (error) {
        // Bad: generic handling
        console.log('An error occurred');
        
        // Good: specific handling or re-throw
        if (error instanceof ValidationError) {
            handleValidationError(error);
        } else {
            console.error('Unexpected error:', error);
            throw error;
        }
    }
}

// 4. Forgetting to clean up in finally
function cleanupMistake() {
    let resource;
    
    try {
        resource = acquireResource();
        riskyOperation();
    } catch (error) {
        console.error(error);
        // Bad: forgetting cleanup
    } finally {
        // Good: always cleanup
        if (resource) {
            releaseResource(resource);
        }
    }
}

// 5. Using try-catch for flow control
function flowControlMistake() {
    // Bad: using exceptions for normal flow
    try {
        if (user.isLoggedIn) {
            return user.data;
        } else {
            throw new Error('Not logged in');
        }
    } catch (error) {
        return null;
    }
    
    // Good: use conditional logic
    if (user.isLoggedIn) {
        return user.data;
    }
    return null;
}

// 6. Not handling async errors properly
function asyncErrorMistake() {
    // Bad: not awaiting or catching errors
    asyncOperation();
    
    // Good: proper async/await with error handling
    try {
        const result = await asyncOperation();
        return result;
    } catch (error) {
        console.error('Async error:', error);
        throw error;
    }
}

// 7. Creating memory leaks with error handlers
function memoryLeak() {
    // Bad: creating circular references in error handlers
    const element = document.getElementById('myElement');
    
    element.addEventListener('click', function handler() {
        try {
            riskyOperation();
        } catch (error) {
            // This creates a circular reference
            element.error = error;
        }
    });
    
    // Good: clean up event listeners
    const handler = function() {
        try {
            riskyOperation();
        } catch (error) {
            console.error(error);
        }
    };
    
    element.addEventListener('click', handler);
    // Later: element.removeEventListener('click', handler);
}
```

## Summary

JavaScript error handling provides comprehensive tools:

**Error Types:**
- Built-in errors: Error, TypeError, ReferenceError, RangeError, SyntaxError
- Custom error classes with inheritance
- Error properties: name, message, stack, cause

**Handling Patterns:**
- Try-catch-finally blocks
- Nested error handling
- Error boundaries for component isolation
- Centralized error handling services

**Promise Error Handling:**
- Promise rejection with `.catch()`
- Async/await with try-catch
- Promise.allSettled() for independent operations
- Timeout and retry patterns

**Global Handling:**
- Window error event listeners
- Unhandled promise rejection handlers
- Error reporting and logging services
- Network status monitoring

**Best Practices:**
- Be specific with error types
- Provide meaningful error messages
- Handle errors at appropriate levels
- Use finally for cleanup
- Don't swallow errors silently
- Log errors for debugging

**Common Pitfalls:**
- Unhandled promise rejections
- Throwing non-Error objects
- Generic error catching
- Forgetting cleanup in finally
- Using exceptions for flow control
- Memory leaks in error handlers

Proper error handling makes applications more robust, maintainable, and user-friendly by gracefully managing unexpected situations.
