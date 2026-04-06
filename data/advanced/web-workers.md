# JavaScript Web Workers

## Web Worker Basics

### Creating a Basic Web Worker
```javascript
// main.js - Main thread code
const worker = new Worker('worker.js');

// Send message to worker
worker.postMessage({
    type: 'start',
    data: 'Hello from main thread!'
});

// Listen for messages from worker
worker.onmessage = function(event) {
    console.log('Message from worker:', event.data);
};

// Handle worker errors
worker.onerror = function(error) {
    console.error('Worker error:', error.message);
    console.error('Error line:', error.lineno);
    console.error('Error file:', error.filename);
};

// Terminate worker
function terminateWorker() {
    worker.terminate();
    console.log('Worker terminated');
}

// worker.js - Worker thread code
self.onmessage = function(event) {
    const message = event.data;
    
    console.log('Message from main thread:', message);
    
    // Send message back to main thread
    self.postMessage({
        type: 'response',
        data: 'Hello from worker!'
    });
};

// Handle errors in worker
self.onerror = function(error) {
    console.error('Worker internal error:', error.message);
    self.postMessage({
        type: 'error',
        error: error.message
    });
};
```

### Worker with Parameters
```javascript
// main.js
function createWorkerWithParams(scriptUrl, params = {}) {
    const workerCode = `
        self.params = ${JSON.stringify(params)};
        ${await fetch(scriptUrl).then(response => response.text())}
    `;
    
    const blob = new Blob([workerCode], { type: 'application/javascript' });
    const workerUrl = URL.createObjectURL(blob);
    
    const worker = new Worker(workerUrl);
    
    // Clean up blob URL when worker terminates
    worker.addEventListener('terminate', () => {
        URL.revokeObjectURL(workerUrl);
    });
    
    return worker;
}

// Usage
const worker = createWorkerWithParams('worker.js', {
    apiUrl: 'https://api.example.com',
    timeout: 5000,
    retries: 3
});

worker.postMessage({ type: 'init' });
```

## Communication Between Threads

### Message Passing
```javascript
// main.js
class WorkerCommunicator {
    constructor(workerScript) {
        this.worker = new Worker(workerScript);
        this.messageQueue = [];
        this.isReady = false;
        
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        this.worker.onmessage = (event) => {
            const message = event.data;
            
            if (message.type === 'ready') {
                this.isReady = true;
                this.processMessageQueue();
            } else {
                this.handleWorkerMessage(message);
            }
        };
        
        this.worker.onerror = (error) => {
            console.error('Worker error:', error);
        };
    }
    
    postMessage(message) {
        if (this.isReady) {
            this.worker.postMessage(message);
        } else {
            this.messageQueue.push(message);
        }
    }
    
    processMessageQueue() {
        while (this.messageQueue.length > 0) {
            const message = this.messageQueue.shift();
            this.worker.postMessage(message);
        }
    }
    
    handleWorkerMessage(message) {
        console.log('Worker message:', message);
    }
    
    terminate() {
        this.worker.terminate();
    }
}

// worker.js
self.onmessage = function(event) {
    const message = event.data;
    
    // Process message
    const response = {
        type: 'response',
        id: message.id,
        data: processMessage(message.data)
    };
    
    self.postMessage(response);
};

function processMessage(data) {
    // Simulate processing
    return `Processed: ${data}`;
}

// Signal worker is ready
self.postMessage({ type: 'ready' });
```

### Bidirectional Communication
```javascript
// main.js
class BidirectionalWorker {
    constructor(workerScript) {
        this.worker = new Worker(workerScript);
        this.messageHandlers = new Map();
        this.messageId = 0;
        this.pendingMessages = new Map();
        
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        this.worker.onmessage = (event) => {
            const message = event.data;
            
            if (message.type === 'response') {
                const pending = this.pendingMessages.get(message.id);
                if (pending) {
                    pending.resolve(message.data);
                    this.pendingMessages.delete(message.id);
                }
            } else if (message.type === 'request') {
                this.handleWorkerRequest(message);
            }
        };
    }
    
    async sendMessage(data, transfer = []) {
        const id = ++this.messageId;
        
        return new Promise((resolve, reject) => {
            this.pendingMessages.set(id, { resolve, reject });
            
            this.worker.postMessage({
                type: 'message',
                id,
                data
            }, transfer);
            
            // Timeout after 30 seconds
            setTimeout(() => {
                if (this.pendingMessages.has(id)) {
                    const pending = this.pendingMessages.get(id);
                    pending.reject(new Error('Message timeout'));
                    this.pendingMessages.delete(id);
                }
            }, 30000);
        });
    }
    
    handleWorkerRequest(message) {
        const handler = this.messageHandlers.get(message.type);
        
        if (handler) {
            const response = handler(message.data);
            
            this.worker.postMessage({
                type: 'response',
                id: message.id,
                data: response
            });
        } else {
            this.worker.postMessage({
                type: 'error',
                id: message.id,
                error: `No handler for message type: ${message.type}`
            });
        }
    }
    
    onMessage(type, handler) {
        this.messageHandlers.set(type, handler);
    }
    
    terminate() {
        this.worker.terminate();
    }
}

// worker.js
self.onmessage = function(event) {
    const message = event.data;
    
    if (message.type === 'message') {
        // Process message and send response
        const response = {
            type: 'response',
            id: message.id,
            data: processMessage(message.data)
        };
        
        self.postMessage(response);
    }
};

function processMessage(data) {
    // Simulate async processing
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve(`Worker processed: ${data}`);
        }, 100);
    });
}

// Request data from main thread
function requestData(type, data) {
    return new Promise((resolve) => {
        const id = Date.now().toString();
        
        self.postMessage({
            type: 'request',
            id,
            requestType: type,
            data
        });
        
        // Handle response
        const handler = function(event) {
            if (event.data.id === id) {
                resolve(event.data.data);
                self.removeEventListener('message', handler);
            }
        };
        
        self.addEventListener('message', handler);
    });
}
```

## Transferable Objects

### Using Transferable Objects
```javascript
// main.js
class TransferableWorker {
    constructor(workerScript) {
        this.worker = new Worker(workerScript);
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        this.worker.onmessage = (event) => {
            console.log('Worker response:', event.data);
        };
    }
    
    // Send array buffer (transferable)
    sendArrayBuffer(buffer) {
        this.worker.postMessage(
            { type: 'array-buffer', data: buffer },
            [buffer] // Transfer ownership
        );
        
        // buffer is now empty (transferred)
        console.log('Buffer length after transfer:', buffer.byteLength);
    }
    
    // Send message port (transferable)
    sendMessagePort(port) {
        this.worker.postMessage(
            { type: 'message-port', data: port },
            [port] // Transfer ownership
        );
    }
    
    // Send canvas (transferable in some browsers)
    sendCanvas(canvas) {
        // Note: Canvas transferability depends on browser support
        try {
            this.worker.postMessage(
                { type: 'canvas', data: canvas },
                [canvas] // Transfer ownership
            );
        } catch (error) {
            console.error('Canvas transfer not supported:', error);
        }
    }
    
    terminate() {
        this.worker.terminate();
    }
}

// worker.js
self.onmessage = function(event) {
    const message = event.data;
    
    switch (message.type) {
        case 'array-buffer':
            handleArrayBuffer(message.data);
            break;
        case 'message-port':
            handleMessagePort(message.data);
            break;
        case 'canvas':
            handleCanvas(message.data);
            break;
    }
};

function handleArrayBuffer(buffer) {
    console.log('Received array buffer:', buffer.byteLength);
    
    // Process the buffer
    const view = new Uint8Array(buffer);
    const sum = view.reduce((acc, val) => acc + val, 0);
    
    // Send back result (buffer is now owned by worker)
    self.postMessage({
        type: 'array-result',
        sum: sum
    });
}

function handleMessagePort(port) {
    console.log('Received message port');
    
    // Start listening on the port
    port.onmessage = function(event) {
        console.log('Port message:', event.data);
        port.postMessage('Port response');
    };
    
    port.start();
}

function handleCanvas(canvas) {
    console.log('Received canvas');
    
    // Get canvas context
    const ctx = canvas.getContext('2d');
    
    // Draw on canvas
    ctx.fillStyle = 'blue';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Send canvas back (if transferable)
    self.postMessage({
        type: 'canvas-result',
        canvas: canvas
    }, [canvas]);
}
```

### SharedArrayBuffer
```javascript
// main.js
function createSharedWorker() {
    // Create shared array buffer
    const sharedBuffer = new SharedArrayBuffer(1024);
    const sharedArray = new Int32Array(sharedBuffer);
    
    // Create worker with shared buffer
    const worker = new Worker('shared-worker.js');
    
    // Send shared buffer (not transferred, shared)
    worker.postMessage({
        type: 'shared-buffer',
        data: sharedBuffer
    });
    
    // Listen for worker messages
    worker.onmessage = function(event) {
        console.log('Worker message:', event.data);
    };
    
    // Modify shared array from main thread
    setInterval(() => {
        const currentValue = Atomics.load(sharedArray, 0);
        const newValue = currentValue + 1;
        Atomics.store(sharedArray, 0, newValue);
        
        console.log('Main thread updated value:', newValue);
    }, 1000);
    
    return { worker, sharedArray };
}

// worker.js
self.onmessage = function(event) {
    const message = event.data;
    
    if (message.type === 'shared-buffer') {
        const sharedBuffer = message.data;
        const sharedArray = new Int32Array(sharedBuffer);
        
        // Monitor shared array changes
        setInterval(() => {
            const currentValue = Atomics.load(sharedArray, 0);
            console.log('Worker sees value:', currentValue);
            
            // Respond to main thread
            self.postMessage({
                type: 'shared-update',
                value: currentValue,
                timestamp: Date.now()
            });
        }, 500);
    }
};
```

## Worker Pools

### Creating a Worker Pool
```javascript
// main.js
class WorkerPool {
    constructor(workerScript, poolSize = 4) {
        this.workerScript = workerScript;
        this.poolSize = poolSize;
        this.workers = [];
        this.taskQueue = [];
        this.busyWorkers = new Set();
        
        this.initializePool();
    }
    
    initializePool() {
        for (let i = 0; i < this.poolSize; i++) {
            const worker = new Worker(this.workerScript);
            worker.id = i;
            worker.busy = false;
            
            worker.onmessage = (event) => {
                this.handleWorkerMessage(worker, event);
            };
            
            worker.onerror = (error) => {
                console.error(`Worker ${worker.id} error:`, error);
                this.releaseWorker(worker);
            };
            
            this.workers.push(worker);
        }
    }
    
    async execute(task, transfer = []) {
        return new Promise((resolve, reject) => {
            this.taskQueue.push({
                task,
                transfer,
                resolve,
                reject
            });
            
            this.processQueue();
        });
    }
    
    processQueue() {
        if (this.taskQueue.length === 0) return;
        
        const availableWorker = this.getAvailableWorker();
        
        if (availableWorker) {
            const taskItem = this.taskQueue.shift();
            this.assignTask(availableWorker, taskItem);
        }
    }
    
    getAvailableWorker() {
        return this.workers.find(worker => !worker.busy);
    }
    
    assignTask(worker, taskItem) {
        worker.busy = true;
        this.busyWorkers.add(worker);
        
        const message = {
            id: Date.now() + Math.random(),
            task: taskItem.task
        };
        
        worker.currentTask = {
            ...taskItem,
            message
        };
        
        worker.postMessage(message, taskItem.transfer);
    }
    
    handleWorkerMessage(worker, event) {
        const taskItem = worker.currentTask;
        
        if (taskItem) {
            taskItem.resolve(event.data);
            this.releaseWorker(worker);
        }
    }
    
    releaseWorker(worker) {
        worker.busy = false;
        worker.currentTask = null;
        this.busyWorkers.delete(worker);
        this.processQueue();
    }
    
    terminate() {
        this.workers.forEach(worker => worker.terminate());
        this.workers = [];
        this.taskQueue = [];
        this.busyWorkers.clear();
    }
    
    getStats() {
        return {
            totalWorkers: this.poolSize,
            busyWorkers: this.busyWorkers.size,
            availableWorkers: this.poolSize - this.busyWorkers.size,
            queuedTasks: this.taskQueue.length
        };
    }
}

// worker.js
self.onmessage = function(event) {
    const message = event.data;
    
    // Execute task
    executeTask(message.task)
        .then(result => {
            self.postMessage({
                id: message.id,
                result: result
            });
        })
        .catch(error => {
            self.postMessage({
                id: message.id,
                error: error.message
            });
        });
};

async function executeTask(task) {
    switch (task.type) {
        case 'compute':
            return computeHeavyTask(task.data);
        case 'process':
            return processData(task.data);
        case 'fetch':
            return fetchData(task.data);
        default:
            throw new Error(`Unknown task type: ${task.type}`);
    }
}

function computeHeavyTask(data) {
    // Simulate heavy computation
    let result = 0;
    for (let i = 0; i < data.iterations; i++) {
        result += Math.sqrt(i) * Math.sin(i);
    }
    return result;
}

function processData(data) {
    // Simulate data processing
    return data.map(item => ({
        ...item,
        processed: true,
        timestamp: Date.now()
    }));
}

async function fetchData(data) {
    // Simulate API call
    const response = await fetch(data.url);
    return response.json();
}
```

### Dynamic Worker Pool
```javascript
// main.js
class DynamicWorkerPool {
    constructor(workerScript, options = {}) {
        this.workerScript = workerScript;
        this.minWorkers = options.minWorkers || 2;
        this.maxWorkers = options.maxWorkers || 8;
        this.workers = [];
        this.taskQueue = [];
        this.busyWorkers = new Set();
        this.idleTimeout = options.idleTimeout || 30000;
        
        this.initializePool();
    }
    
    initializePool() {
        for (let i = 0; i < this.minWorkers; i++) {
            this.createWorker();
        }
    }
    
    createWorker() {
        const worker = new Worker(this.workerScript);
        worker.id = Date.now() + Math.random();
        worker.busy = false;
        worker.lastUsed = Date.now();
        
        worker.onmessage = (event) => {
            this.handleWorkerMessage(worker, event);
        };
        
        worker.onerror = (error) => {
            console.error(`Worker ${worker.id} error:`, error);
            this.removeWorker(worker);
        };
        
        this.workers.push(worker);
        this.scheduleIdleCheck();
        
        return worker;
    }
    
    removeWorker(worker) {
        const index = this.workers.indexOf(worker);
        if (index > -1) {
            this.workers.splice(index, 1);
            this.busyWorkers.delete(worker);
            worker.terminate();
        }
    }
    
    async execute(task, transfer = []) {
        return new Promise((resolve, reject) => {
            this.taskQueue.push({
                task,
                transfer,
                resolve,
                reject,
                timestamp: Date.now()
            });
            
            this.processQueue();
            this.checkWorkerCount();
        });
    }
    
    processQueue() {
        if (this.taskQueue.length === 0) return;
        
        const availableWorker = this.getAvailableWorker();
        
        if (availableWorker) {
            const taskItem = this.taskQueue.shift();
            this.assignTask(availableWorker, taskItem);
        }
    }
    
    getAvailableWorker() {
        return this.workers.find(worker => !worker.busy);
    }
    
    assignTask(worker, taskItem) {
        worker.busy = true;
        worker.lastUsed = Date.now();
        this.busyWorkers.add(worker);
        
        const message = {
            id: Date.now() + Math.random(),
            task: taskItem.task
        };
        
        worker.currentTask = {
            ...taskItem,
            message
        };
        
        worker.postMessage(message, taskItem.transfer);
    }
    
    handleWorkerMessage(worker, event) {
        const taskItem = worker.currentTask;
        
        if (taskItem) {
            taskItem.resolve(event.data);
            this.releaseWorker(worker);
        }
    }
    
    releaseWorker(worker) {
        worker.busy = false;
        worker.currentTask = null;
        worker.lastUsed = Date.now();
        this.busyWorkers.delete(worker);
        this.processQueue();
    }
    
    checkWorkerCount() {
        const queueLength = this.taskQueue.length;
        const availableWorkers = this.workers.length - this.busyWorkers.size;
        
        // Need more workers
        if (queueLength > availableWorkers && this.workers.length < this.maxWorkers) {
            const workersToAdd = Math.min(
                queueLength - availableWorkers,
                this.maxWorkers - this.workers.length
            );
            
            for (let i = 0; i < workersToAdd; i++) {
                this.createWorker();
            }
        }
    }
    
    scheduleIdleCheck() {
        setTimeout(() => {
            this.checkIdleWorkers();
        }, this.idleTimeout);
    }
    
    checkIdleWorkers() {
        const now = Date.now();
        const idleWorkers = this.workers.filter(worker => 
            !worker.busy && (now - worker.lastUsed) > this.idleTimeout
        );
        
        // Remove idle workers, but keep minimum
        idleWorkers.forEach(worker => {
            if (this.workers.length > this.minWorkers) {
                this.removeWorker(worker);
            }
        });
        
        // Schedule next check
        if (this.workers.length > 0) {
            this.scheduleIdleCheck();
        }
    }
    
    getStats() {
        return {
            totalWorkers: this.workers.length,
            busyWorkers: this.busyWorkers.size,
            availableWorkers: this.workers.length - this.busyWorkers.size,
            queuedTasks: this.taskQueue.length,
            minWorkers: this.minWorkers,
            maxWorkers: this.maxWorkers
        };
    }
    
    terminate() {
        this.workers.forEach(worker => worker.terminate());
        this.workers = [];
        this.taskQueue = [];
        this.busyWorkers.clear();
    }
}
```

## Specialized Workers

### Image Processing Worker
```javascript
// main.js
class ImageProcessor {
    constructor() {
        this.worker = new Worker('image-worker.js');
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        this.worker.onmessage = (event) => {
            const message = event.data;
            
            switch (message.type) {
                case 'processed':
                    this.handleProcessedImage(message);
                    break;
                case 'progress':
                    this.handleProgress(message);
                    break;
                case 'error':
                    this.handleError(message);
                    break;
            }
        };
    }
    
    processImage(imageFile, options = {}) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            
            reader.onload = (event) => {
                const imageData = event.target.result;
                
                this.worker.postMessage({
                    type: 'process',
                    imageData,
                    options
                });
                
                // Store promise resolution
                this.currentPromise = { resolve, reject };
            };
            
            reader.onerror = reject;
            reader.readAsDataURL(imageFile);
        });
    }
    
    handleProcessedImage(message) {
        if (this.currentPromise) {
            this.currentPromise.resolve(message.data);
            this.currentPromise = null;
        }
    }
    
    handleProgress(message) {
        console.log(`Processing progress: ${message.progress}%`);
    }
    
    handleError(message) {
        if (this.currentPromise) {
            this.currentPromise.reject(new Error(message.error));
            this.currentPromise = null;
        }
    }
    
    terminate() {
        this.worker.terminate();
    }
}

// worker.js
self.onmessage = function(event) {
    const message = event.data;
    
    if (message.type === 'process') {
        processImage(message.imageData, message.options);
    }
};

async function processImage(imageData, options) {
    try {
        // Convert data URL to ImageBitmap
        const imageBitmap = await createImageBitmap(imageData);
        
        // Create offscreen canvas
        const canvas = new OffscreenCanvas(imageBitmap.width, imageBitmap.height);
        const ctx = canvas.getContext('2d');
        
        // Draw image
        ctx.drawImage(imageBitmap, 0, 0);
        
        // Apply filters
        if (options.grayscale) {
            applyGrayscale(ctx, canvas.width, canvas.height);
        }
        
        if (options.blur) {
            applyBlur(ctx, canvas.width, canvas.height);
        }
        
        if (options.brightness) {
            applyBrightness(ctx, canvas.width, canvas.height, options.brightness);
        }
        
        // Convert back to blob
        const blob = await canvas.convertToBlob({ type: 'image/jpeg' });
        
        self.postMessage({
            type: 'processed',
            data: blob
        });
        
    } catch (error) {
        self.postMessage({
            type: 'error',
            error: error.message
        });
    }
}

function applyGrayscale(ctx, width, height) {
    const imageData = ctx.getImageData(0, 0, width, height);
    const data = imageData.data;
    
    for (let i = 0; i < data.length; i += 4) {
        const gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
        data[i] = gray;     // Red
        data[i + 1] = gray; // Green
        data[i + 2] = gray; // Blue
    }
    
    ctx.putImageData(imageData, 0, 0);
}

function applyBlur(ctx, width, height) {
    ctx.filter = 'blur(5px)';
    const imageData = ctx.getImageData(0, 0, width, height);
    ctx.filter = 'none';
    ctx.putImageData(imageData, 0, 0);
}

function applyBrightness(ctx, width, height, brightness) {
    const imageData = ctx.getImageData(0, 0, width, height);
    const data = imageData.data;
    
    for (let i = 0; i < data.length; i += 4) {
        data[i] = Math.min(255, data[i] * brightness);     // Red
        data[i + 1] = Math.min(255, data[i + 1] * brightness); // Green
        data[i + 2] = Math.min(255, data[i + 2] * brightness); // Blue
    }
    
    ctx.putImageData(imageData, 0, 0);
}
```

### Data Processing Worker
```javascript
// main.js
class DataProcessor {
    constructor(workerScript) {
        this.worker = new Worker(workerScript);
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        this.worker.onmessage = (event) => {
            const message = event.data;
            
            switch (message.type) {
                case 'result':
                    this.handleResult(message);
                    break;
                case 'progress':
                    this.handleProgress(message);
                    break;
                case 'chunk':
                    this.handleChunk(message);
                    break;
            }
        };
    }
    
    async processData(data, operation, options = {}) {
        return new Promise((resolve, reject) => {
            this.currentOperation = { resolve, reject };
            
            this.worker.postMessage({
                type: 'process',
                data,
                operation,
                options
            });
        });
    }
    
    handleResult(message) {
        if (this.currentOperation) {
            this.currentOperation.resolve(message.data);
            this.currentOperation = null;
        }
    }
    
    handleProgress(message) {
        console.log(`Processing progress: ${message.progress}%`);
    }
    
    handleChunk(message) {
        console.log('Received chunk:', message.data);
    }
    
    terminate() {
        this.worker.terminate();
    }
}

// worker.js
self.onmessage = function(event) {
    const message = event.data;
    
    if (message.type === 'process') {
        processData(message.data, message.operation, message.options);
    }
};

async function processData(data, operation, options) {
    try {
        let result;
        
        switch (operation) {
            case 'sort':
                result = await sortData(data, options);
                break;
            case 'filter':
                result = await filterData(data, options);
                break;
            case 'aggregate':
                result = await aggregateData(data, options);
                break;
            case 'transform':
                result = await transformData(data, options);
                break;
            default:
                throw new Error(`Unknown operation: ${operation}`);
        }
        
        self.postMessage({
            type: 'result',
            data: result
        });
        
    } catch (error) {
        self.postMessage({
            type: 'error',
            error: error.message
        });
    }
}

async function sortData(data, options) {
    const { key, direction = 'asc', chunkSize = 1000 } = options;
    
    // Process in chunks for large datasets
    const chunks = [];
    for (let i = 0; i < data.length; i += chunkSize) {
        const chunk = data.slice(i, i + chunkSize);
        const sortedChunk = chunk.sort((a, b) => {
            const aVal = a[key];
            const bVal = b[key];
            return direction === 'asc' ? aVal - bVal : bVal - aVal;
        });
        chunks.push(sortedChunk);
        
        // Send progress update
        self.postMessage({
            type: 'progress',
            progress: Math.round((i + chunkSize) / data.length * 100)
        });
    }
    
    // Merge sorted chunks
    return mergeSortedChunks(chunks, key, direction);
}

function mergeSortedChunks(chunks, key, direction) {
    const result = [];
    const indices = new Array(chunks.length).fill(0);
    
    while (result.length < getTotalLength(chunks)) {
        let minValue = null;
        let minChunkIndex = -1;
        
        for (let i = 0; i < chunks.length; i++) {
            if (indices[i] < chunks[i].length) {
                const value = chunks[i][indices[i]][key];
                
                if (minValue === null || 
                    (direction === 'asc' && value < minValue) ||
                    (direction === 'desc' && value > minValue)) {
                    minValue = value;
                    minChunkIndex = i;
                }
            }
        }
        
        if (minChunkIndex !== -1) {
            result.push(chunks[minChunkIndex][indices[minChunkIndex]]);
            indices[minChunkIndex]++;
        }
    }
    
    return result;
}

function getTotalLength(chunks) {
    return chunks.reduce((total, chunk) => total + chunk.length, 0);
}

async function filterData(data, options) {
    const { condition, chunkSize = 1000 } = options;
    const result = [];
    
    for (let i = 0; i < data.length; i += chunkSize) {
        const chunk = data.slice(i, i + chunkSize);
        const filteredChunk = chunk.filter(item => evaluateCondition(item, condition));
        result.push(...filteredChunk);
        
        // Send progress update
        self.postMessage({
            type: 'progress',
            progress: Math.round((i + chunkSize) / data.length * 100)
        });
    }
    
    return result;
}

function evaluateCondition(item, condition) {
    // Simple condition evaluation
    const { field, operator, value } = condition;
    
    switch (operator) {
        case 'equals':
            return item[field] === value;
        case 'greater':
            return item[field] > value;
        case 'less':
            return item[field] < value;
        case 'contains':
            return item[field].includes(value);
        default:
            return true;
    }
}

async function aggregateData(data, options) {
    const { groupBy, aggregations } = options;
    const groups = {};
    
    for (const item of data) {
        const groupKey = item[groupBy];
        
        if (!groups[groupKey]) {
            groups[groupKey] = [];
        }
        
        groups[groupKey].push(item);
    }
    
    const result = {};
    
    for (const [groupKey, groupItems] of Object.entries(groups)) {
        result[groupKey] = {};
        
        for (const aggregation of aggregations) {
            const { field, operation } = aggregation;
            
            switch (operation) {
                case 'sum':
                    result[groupKey][field] = groupItems.reduce((sum, item) => sum + item[field], 0);
                    break;
                case 'avg':
                    result[groupKey][field] = groupItems.reduce((sum, item) => sum + item[field], 0) / groupItems.length;
                    break;
                case 'count':
                    result[groupKey][field] = groupItems.length;
                    break;
                case 'min':
                    result[groupKey][field] = Math.min(...groupItems.map(item => item[field]));
                    break;
                case 'max':
                    result[groupKey][field] = Math.max(...groupItems.map(item => item[field]));
                    break;
            }
        }
    }
    
    return result;
}

async function transformData(data, options) {
    const { transformations } = options;
    
    return data.map(item => {
        const transformed = { ...item };
        
        for (const transformation of transformations) {
            const { field, operation, params } = transformation;
            
            switch (operation) {
                case 'multiply':
                    transformed[field] = item[field] * params.factor;
                    break;
                case 'add':
                    transformed[field] = item[field] + params.value;
                    break;
                case 'format':
                    transformed[field] = formatValue(item[field], params.format);
                    break;
                case 'calculate':
                    transformed[field] = calculateValue(item, params.expression);
                    break;
            }
        }
        
        return transformed;
    });
}

function formatValue(value, format) {
    switch (format) {
        case 'currency':
            return `$${value.toFixed(2)}`;
        case 'percentage':
            return `${(value * 100).toFixed(1)}%`;
        case 'date':
            return new Date(value).toLocaleDateString();
        default:
            return value.toString();
    }
}

function calculateValue(item, expression) {
    // Simple expression evaluation
    // In production, use a proper expression parser
    return eval(`with(item) { ${expression} }`);
}
```

## Security Considerations

### Secure Worker Communication
```javascript
// main.js
class SecureWorker {
    constructor(workerScript, options = {}) {
        this.workerScript = workerScript;
        this.options = options;
        this.worker = null;
        this.messageQueue = [];
        this.isReady = false;
        
        this.initializeWorker();
    }
    
    initializeWorker() {
        // Create worker with proper security headers
        const worker = new Worker(this.workerScript, {
            type: 'module',
            credentials: 'same-origin'
        });
        
        worker.onmessage = (event) => {
            this.handleMessage(event);
        };
        
        worker.onerror = (error) => {
            this.handleError(error);
        };
        
        this.worker = worker;
    }
    
    async sendMessage(message, transfer = []) {
        // Validate message before sending
        if (!this.validateMessage(message)) {
            throw new Error('Invalid message format');
        }
        
        return new Promise((resolve, reject) => {
            const messageWrapper = {
                id: this.generateMessageId(),
                timestamp: Date.now(),
                data: message
            };
            
            this.messageQueue.push({
                message: messageWrapper,
                transfer,
                resolve,
                reject
            });
            
            this.processQueue();
        });
    }
    
    validateMessage(message) {
        // Implement message validation
        if (typeof message !== 'object' || message === null) {
            return false;
        }
        
        // Check for forbidden properties
        const forbidden = ['__proto__', 'constructor', 'prototype'];
        for (const prop of forbidden) {
            if (prop in message) {
                return false;
            }
        }
        
        return true;
    }
    
    generateMessageId() {
        return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    }
    
    processQueue() {
        if (this.messageQueue.length === 0 || !this.worker) return;
        
        const queueItem = this.messageQueue.shift();
        
        try {
            this.worker.postMessage(queueItem.message, queueItem.transfer);
            
            // Store promise resolution
            this.pendingMessages = this.pendingMessages || new Map();
            this.pendingMessages.set(queueItem.message.id, {
                resolve: queueItem.resolve,
                reject: queueItem.reject
            });
            
        } catch (error) {
            queueItem.reject(error);
        }
    }
    
    handleMessage(event) {
        const message = event.data;
        
        if (!this.validateMessage(message)) {
            console.error('Invalid message from worker');
            return;
        }
        
        const pending = this.pendingMessages?.get(message.id);
        
        if (pending) {
            pending.resolve(message.data);
            this.pendingMessages.delete(message.id);
        }
    }
    
    handleError(error) {
        console.error('Worker error:', error);
        
        // Reject all pending messages
        if (this.pendingMessages) {
            for (const [id, pending] of this.pendingMessages) {
                pending.reject(error);
            }
            this.pendingMessages.clear();
        }
    }
    
    terminate() {
        if (this.worker) {
            this.worker.terminate();
            this.worker = null;
        }
        
        this.messageQueue = [];
        this.pendingMessages?.clear();
    }
}

// worker.js
// Secure worker implementation
class SecureWorkerHandler {
    constructor() {
        this.allowedMessageTypes = ['compute', 'fetch', 'process'];
        this.maxMessageSize = 1024 * 1024; // 1MB
    }
    
    initialize() {
        self.onmessage = (event) => {
            this.handleMessage(event);
        };
        
        self.onerror = (error) => {
            console.error('Worker error:', error);
        };
        
        // Signal ready
        self.postMessage({ type: 'ready' });
    }
    
    handleMessage(event) {
        try {
            const message = event.data;
            
            if (!this.validateMessage(message)) {
                throw new Error('Invalid message');
            }
            
            this.processMessage(message);
            
        } catch (error) {
            this.sendErrorResponse(message.id, error.message);
        }
    }
    
    validateMessage(message) {
        // Check message structure
        if (!message || typeof message !== 'object') {
            return false;
        }
        
        if (!message.id || !message.data) {
            return false;
        }
        
        // Check message size
        const messageSize = JSON.stringify(message).length;
        if (messageSize > this.maxMessageSize) {
            return false;
        }
        
        // Check message type
        if (message.data.type && !this.allowedMessageTypes.includes(message.data.type)) {
            return false;
        }
        
        return true;
    }
    
    processMessage(message) {
        const { data } = message;
        
        switch (data.type) {
            case 'compute':
                this.handleCompute(message);
                break;
            case 'fetch':
                this.handleFetch(message);
                break;
            case 'process':
                this.handleProcess(message);
                break;
            default:
                throw new Error(`Unknown message type: ${data.type}`);
        }
    }
    
    async handleCompute(message) {
        const { data } = message.data;
        const result = await this.performComputation(data);
        
        this.sendSuccessResponse(message.id, result);
    }
    
    async handleFetch(message) {
        const { url, options } = message.data;
        
        try {
            const response = await fetch(url, options);
            const result = await response.json();
            
            this.sendSuccessResponse(message.id, result);
        } catch (error) {
            this.sendErrorResponse(message.id, error.message);
        }
    }
    
    async handleProcess(message) {
        const { data, operation } = message.data;
        const result = await this.performOperation(data, operation);
        
        this.sendSuccessResponse(message.id, result);
    }
    
    performComputation(data) {
        // Implement secure computation
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve(`Computed: ${data}`);
            }, 100);
        });
    }
    
    performOperation(data, operation) {
        // Implement secure operation
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve(`Processed: ${data} with ${operation}`);
            }, 100);
        });
    }
    
    sendSuccessResponse(id, data) {
        self.postMessage({
            id,
            type: 'success',
            data,
            timestamp: Date.now()
        });
    }
    
    sendErrorResponse(id, error) {
        self.postMessage({
            id,
            type: 'error',
            error,
            timestamp: Date.now()
        });
    }
}

// Initialize secure worker
const secureHandler = new SecureWorkerHandler();
secureHandler.initialize();
```

## Best Practices

### Web Worker Best Practices
```javascript
// 1. Use transferable objects for large data
function useTransferableObjects() {
    const largeArray = new Uint8Array(1024 * 1024); // 1MB
    
    // Good: transfer ownership
    worker.postMessage({
        type: 'process',
        data: largeArray
    }, [largeArray.buffer]);
    
    // Bad: copy data
    worker.postMessage({
        type: 'process',
        data: largeArray
    });
}

// 2. Implement proper error handling
function implementErrorHandling() {
    const worker = new Worker('worker.js');
    
    worker.onmessage = (event) => {
        try {
            const result = processWorkerMessage(event.data);
            console.log('Success:', result);
        } catch (error) {
            console.error('Processing error:', error);
        }
    };
    
    worker.onerror = (error) => {
        console.error('Worker error:', error.message);
        // Implement fallback or retry logic
    };
}

// 3. Use worker pools for multiple tasks
function useWorkerPools() {
    const pool = new WorkerPool('worker.js', {
        minWorkers: 2,
        maxWorkers: 8
    });
    
    // Execute multiple tasks concurrently
    const tasks = [
        { type: 'compute', data: largeDataset1 },
        { type: 'compute', data: largeDataset2 },
        { type: 'process', data: largeDataset3 }
    ];
    
    const promises = tasks.map(task => pool.execute(task));
    
    Promise.all(promises)
        .then(results => console.log('All tasks completed'))
        .catch(error => console.error('Task failed:', error));
}

// 4. Implement proper cleanup
function implementCleanup() {
    const workers = [];
    
    function createWorker() {
        const worker = new Worker('worker.js');
        workers.push(worker);
        return worker;
    }
    
    function cleanup() {
        workers.forEach(worker => {
            worker.terminate();
        });
        workers.length = 0;
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', cleanup);
}

// 5. Use SharedArrayBuffer for shared state
function useSharedArrayBuffer() {
    const sharedBuffer = new SharedArrayBuffer(1024);
    const sharedArray = new Int32Array(sharedBuffer);
    
    const worker = new Worker('shared-worker.js');
    
    worker.postMessage({
        type: 'shared-buffer',
        data: sharedBuffer
    });
    
    // Access shared data
    const value = Atomics.load(sharedArray, 0);
    Atomics.store(sharedArray, 0, value + 1);
}

// 6. Implement message validation
function implementMessageValidation() {
    function validateMessage(message) {
        if (!message || typeof message !== 'object') {
            return false;
        }
        
        if (!message.type || !message.data) {
            return false;
        }
        
        // Check for malicious content
        const forbidden = ['eval', 'Function', 'constructor'];
        const messageString = JSON.stringify(message);
        
        for (const forbiddenWord of forbidden) {
            if (messageString.includes(forbiddenWord)) {
                return false;
            }
        }
        
        return true;
    }
    
    worker.onmessage = (event) => {
        if (validateMessage(event.data)) {
            processMessage(event.data);
        } else {
            console.error('Invalid message received');
        }
    };
}

// 7. Use proper timeout handling
function implementTimeoutHandling() {
    function sendMessageWithTimeout(worker, message, timeout = 30000) {
        return new Promise((resolve, reject) => {
            const timeoutId = setTimeout(() => {
                reject(new Error('Message timeout'));
            }, timeout);
            
            const messageId = Date.now().toString();
            
            const handleMessage = (event) => {
                if (event.data.id === messageId) {
                    clearTimeout(timeoutId);
                    worker.removeEventListener('message', handleMessage);
                    resolve(event.data);
                }
            };
            
            worker.addEventListener('message', handleMessage);
            worker.postMessage({ ...message, id: messageId });
        });
    }
}

// 8. Monitor worker performance
function monitorWorkerPerformance() {
    const workerMetrics = {
        messagesSent: 0,
        messagesReceived: 0,
        averageResponseTime: 0,
        errors: 0
    };
    
    function sendMessage(worker, message) {
        const startTime = performance.now();
        workerMetrics.messagesSent++;
        
        return new Promise((resolve) => {
            const handleMessage = (event) => {
                const endTime = performance.now();
                const responseTime = endTime - startTime;
                
                workerMetrics.messagesReceived++;
                workerMetrics.averageResponseTime = 
                    (workerMetrics.averageResponseTime + responseTime) / 2;
                
                worker.removeEventListener('message', handleMessage);
                resolve(event.data);
            };
            
            worker.addEventListener('message', handleMessage);
            worker.postMessage(message);
        });
    }
}
```

## Common Pitfalls

### Common Web Worker Mistakes
```javascript
// 1. Accessing DOM from worker
function domAccessMistake() {
    // Worker code - THIS WILL NOT WORK
    // const element = document.getElementById('my-element');
    // element.textContent = 'Hello from worker';
    
    // Correct: Send data back to main thread
    self.postMessage({
        type: 'update-dom',
        elementId: 'my-element',
        text: 'Hello from worker'
    });
}

// 2. Not terminating workers
function workerLeak() {
    // Bad: creating workers without cleanup
    function createWorker() {
        const worker = new Worker('worker.js');
        return worker;
    }
    
    // Good: implement cleanup
    function createWorkerWithCleanup() {
        const worker = new Worker('worker.js');
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            worker.terminate();
        });
        
        return worker;
    }
}

// 3. Using blocking operations
function blockingOperations() {
    // Bad: blocking worker thread
    self.onmessage = function(event) {
        const result = heavySynchronousComputation();
        self.postMessage(result);
    };
    
    // Good: use async operations
    self.onmessage = async function(event) {
        const result = await heavyAsynchronousComputation();
        self.postMessage(result);
    };
}

// 4. Not handling worker errors
function errorHandlingMistake() {
    // Bad: no error handling
    const worker = new Worker('worker.js');
    worker.onmessage = (event) => console.log(event.data);
    
    // Good: proper error handling
    const worker = new Worker('worker.js');
    
    worker.onmessage = (event) => {
        try {
            processMessage(event.data);
        } catch (error) {
            console.error('Message processing error:', error);
        }
    };
    
    worker.onerror = (error) => {
        console.error('Worker error:', error);
        // Implement fallback or retry logic
    };
}

// 5. Sending large objects without transfer
function largeDataTransfer() {
    const largeData = new Uint8Array(10 * 1024 * 1024); // 10MB
    
    // Bad: copying large data
    worker.postMessage({ type: 'process', data: largeData });
    
    // Good: transferring ownership
    worker.postMessage(
        { type: 'process', data: largeData },
        [largeData.buffer]
    );
}

// 6. Not implementing message validation
function messageValidationMistake() {
    // Bad: accepting any message
    self.onmessage = function(event) {
        processData(event.data);
    };
    
    // Good: validate messages
    self.onmessage = function(event) {
        if (validateMessage(event.data)) {
            processData(event.data);
        } else {
            console.error('Invalid message format');
        }
    };
    
    function validateMessage(message) {
        return message && 
               typeof message === 'object' && 
               message.type &&
               message.data;
    }
}
```

## Summary

Web Workers provide powerful capabilities for:

**Core Features:**
- Separate thread execution
- Message-based communication
- Transferable objects for performance
- SharedArrayBuffer for shared state

**Communication Patterns:**
- Simple message passing
- Bidirectional communication
- Message validation and security
- Timeout and error handling

**Advanced Patterns:**
- Worker pools for scalability
- Dynamic worker management
- Specialized workers (image processing, data processing)
- Secure worker communication

**Performance Optimization:**
- Transferable objects for large data
- Parallel processing
- Background computations
- Non-blocking operations

**Security Considerations:**
- Message validation
- Secure communication protocols
- Error handling and cleanup
- Resource management

**Best Practices:**
- Use transferable objects for large data
- Implement proper error handling
- Use worker pools for multiple tasks
- Implement proper cleanup
- Validate messages
- Monitor performance

**Common Pitfalls:**
- DOM access from workers
- Worker memory leaks
- Blocking operations
- Poor error handling
- Inefficient data transfer

Web Workers enable creating high-performance, responsive web applications by offloading heavy computations to background threads.
