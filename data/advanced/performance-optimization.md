# JavaScript Performance Optimization

## Performance Measurement

### Performance APIs
```javascript
// Performance API for measuring performance
function measurePerformance() {
    // Mark performance points
    performance.mark('start-operation');
    
    // Perform operation
    const result = heavyComputation();
    
    performance.mark('end-operation');
    
    // Measure the duration
    performance.measure('operation-duration', 'start-operation', 'end-operation');
    
    // Get performance entries
    const entries = performance.getEntriesByName('operation-duration');
    const duration = entries[0].duration;
    
    console.log(`Operation took ${duration.toFixed(2)}ms`);
    
    return result;
}

function heavyComputation() {
    let sum = 0;
    for (let i = 0; i < 1000000; i++) {
        sum += Math.sqrt(i);
    }
    return sum;
}

// Performance observer
const observer = new PerformanceObserver((list) => {
    for (const entry of list.getEntries()) {
        console.log('Performance entry:', entry.name, entry.duration);
    }
});

observer.observe({ entryTypes: ['measure', 'navigation', 'resource'] });
```

### Console Performance Tools
```javascript
// Console timing methods
console.time('loop-timer');
for (let i = 0; i < 10000; i++) {
    // Some operation
}
console.timeEnd('loop-timer');

// Console table for performance comparison
function comparePerformance() {
    const results = [];
    
    // Method 1
    console.time('method1');
    let sum1 = 0;
    for (let i = 0; i < 100000; i++) {
        sum1 += i;
    }
    console.timeEnd('method1');
    results.push({ method: 'for loop', time: performance.now() });
    
    // Method 2
    console.time('method2');
    const sum2 = Array.from({ length: 100000 }, (_, i) => i).reduce((a, b) => a + b, 0);
    console.timeEnd('method2');
    results.push({ method: 'reduce', time: performance.now() });
    
    console.table(results);
}

comparePerformance();
```

### Memory Profiling
```javascript
// Memory usage monitoring
function monitorMemoryUsage() {
    if (performance.memory) {
        const memory = performance.memory;
        
        console.log('Memory Usage:');
        console.log(`Used: ${(memory.usedJSHeapSize / 1024 / 1024).toFixed(2)} MB`);
        console.log(`Total: ${(memory.totalJSHeapSize / 1024 / 1024).toFixed(2)} MB`);
        console.log(`Limit: ${(memory.jsHeapSizeLimit / 1024 / 1024).toFixed(2)} MB`);
    }
}

// Memory leak detection
function detectMemoryLeaks() {
    const elements = [];
    
    // Simulate potential memory leak
    setInterval(() => {
        const element = document.createElement('div');
        element.innerHTML = 'Large content'.repeat(1000);
        elements.push(element);
        
        // Not removing elements - potential leak
        if (elements.length > 100) {
            console.warn(`Memory leak detected: ${elements.length} elements`);
        }
    }, 100);
}

// Force garbage collection (if available)
function forceGarbageCollection() {
    if (window.gc) {
        window.gc();
    } else {
        console.warn('Garbage collection not available');
    }
}
```

## DOM Performance

### Efficient DOM Manipulation
```javascript
// Bad: Multiple DOM operations
function badDOMManipulation() {
    const container = document.getElementById('container');
    
    // Triggers reflow multiple times
    container.innerHTML = '';
    container.appendChild(createElement('div', 'Item 1'));
    container.appendChild(createElement('div', 'Item 2'));
    container.appendChild(createElement('div', 'Item 3'));
    container.style.color = 'red';
    container.style.fontSize = '16px';
    container.style.padding = '10px';
}

// Good: Batch DOM operations
function goodDOMManipulation() {
    const container = document.getElementById('container');
    
    // Use document fragment
    const fragment = document.createDocumentFragment();
    
    // Create all elements first
    const elements = [
        createElement('div', 'Item 1'),
        createElement('div', 'Item 2'),
        createElement('div', 'Item 3')
    ];
    
    // Add to fragment
    elements.forEach(element => fragment.appendChild(element));
    
    // Single DOM operation
    container.innerHTML = '';
    container.appendChild(fragment);
    
    // Batch style updates
    Object.assign(container.style, {
        color: 'red',
        fontSize: '16px',
        padding: '10px'
    });
}

function createElement(tag, content) {
    const element = document.createElement(tag);
    element.textContent = content;
    return element;
}
```

### Event Delegation
```javascript
// Bad: Individual event listeners
function badEventHandling() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Button clicked:', this.textContent);
        });
    });
    
    // Problem: Each button has its own listener
    // New buttons need new listeners
}

// Good: Event delegation
function goodEventHandling() {
    const container = document.getElementById('container');
    
    // Single listener for all buttons
    container.addEventListener('click', function(event) {
        if (event.target.classList.contains('btn')) {
            console.log('Button clicked:', event.target.textContent);
        }
    });
    
    // Benefits:
    // - Single listener for all buttons
    // - Works for dynamically added buttons
    // - Better memory usage
}

// Advanced event delegation with event delegation library
class EventDelegator {
    constructor(container) {
        this.container = container;
        this.handlers = new Map();
        this.setupListener();
    }
    
    setupListener() {
        this.container.addEventListener('click', this.handleEvent.bind(this));
    }
    
    handleEvent(event) {
        const target = event.target;
        
        for (const [selector, handler] of this.handlers) {
            if (target.matches(selector)) {
                handler(event);
                return;
            }
        }
    }
    
    on(selector, handler) {
        this.handlers.set(selector, handler);
    }
    
    off(selector) {
        this.handlers.delete(selector);
    }
}

// Usage
const delegator = new EventDelegator(document.body);
delegator.on('.btn', (event) => {
    console.log('Button clicked:', event.target.textContent);
});
```

### Virtual Scrolling
```javascript
// Virtual scrolling implementation for large lists
class VirtualScroller {
    constructor(container, itemHeight, renderItem) {
        this.container = container;
        this.itemHeight = itemHeight;
        this.renderItem = renderItem;
        this.items = [];
        this.visibleStart = 0;
        this.visibleEnd = 0;
        this.scrollTop = 0;
        
        this.setupScrollListener();
    }
    
    setItems(items) {
        this.items = items;
        this.updateVisibleRange();
        this.render();
    }
    
    setupScrollListener() {
        this.container.addEventListener('scroll', () => {
            this.handleScroll();
        });
    }
    
    handleScroll() {
        const newScrollTop = this.container.scrollTop;
        
        if (Math.abs(newScrollTop - this.scrollTop) > this.itemHeight) {
            this.scrollTop = newScrollTop;
            this.updateVisibleRange();
            this.render();
        }
    }
    
    updateVisibleRange() {
        const containerHeight = this.container.clientHeight;
        const startIndex = Math.floor(this.scrollTop / this.itemHeight);
        const endIndex = Math.min(
            startIndex + Math.ceil(containerHeight / this.itemHeight) + 1,
            this.items.length
        );
        
        if (startIndex !== this.visibleStart || endIndex !== this.visibleEnd) {
            this.visibleStart = startIndex;
            this.visibleEnd = endIndex;
        }
    }
    
    render() {
        const fragment = document.createDocumentFragment();
        
        for (let i = this.visibleStart; i < this.visibleEnd; i++) {
            const item = this.items[i];
            const element = this.renderItem(item, i);
            element.style.position = 'absolute';
            element.style.top = `${i * this.itemHeight}px`;
            fragment.appendChild(element);
        }
        
        this.container.innerHTML = '';
        this.container.appendChild(fragment);
        this.container.style.height = `${this.items.length * this.itemHeight}px`;
    }
}

// Usage
const virtualScroller = new VirtualScroller(
    document.getElementById('list-container'),
    50,
    (item, index) => {
        const div = document.createElement('div');
        div.className = 'list-item';
        div.textContent = `Item ${index + 1}: ${item}`;
        return div;
    }
);

// Load large dataset
const largeDataset = Array.from({ length: 10000 }, (_, i) => `Item ${i + 1}`);
virtualScroller.setItems(largeDataset);
```

## Algorithm Optimization

### Efficient Algorithms
```javascript
// Algorithm performance comparison
function compareSearchAlgorithms() {
    const data = Array.from({ length: 100000 }, (_, i) => i);
    const target = 99999;
    
    // Linear search
    console.time('Linear Search');
    let linearResult = -1;
    for (let i = 0; i < data.length; i++) {
        if (data[i] === target) {
            linearResult = i;
            break;
        }
    }
    console.timeEnd('Linear Search');
    
    // Binary search (requires sorted data)
    const sortedData = [...data].sort((a, b) => a - b);
    console.time('Binary Search');
    let binaryResult = -1;
    let left = 0;
    let right = sortedData.length - 1;
    
    while (left <= right) {
        const mid = Math.floor((left + right) / 2);
        if (sortedData[mid] === target) {
            binaryResult = mid;
            break;
        } else if (sortedData[mid] < target) {
            left = mid + 1;
        } else {
            right = mid - 1;
        }
    }
    console.timeEnd('Binary Search');
    
    console.log(`Linear search result: ${linearResult}`);
    console.log(`Binary search result: ${binaryResult}`);
}

// Efficient data structures
function efficientDataStructures() {
    // Using Set for O(1) lookups
    const largeArray = Array.from({ length: 100000 }, (_, i) => i);
    
    console.time('Array Lookup');
    const arrayResult = largeArray.includes(99999);
    console.timeEnd('Array Lookup');
    
    const largeSet = new Set(largeArray);
    console.time('Set Lookup');
    const setResult = largeSet.has(99999);
    console.timeEnd('Set Lookup');
    
    console.log(`Array result: ${arrayResult}, Set result: ${setResult}`);
}

// Memoization for expensive computations
function createMemoizedFunction(fn) {
    const cache = new Map();
    
    return function(...args) {
        const key = JSON.stringify(args);
        
        if (cache.has(key)) {
            return cache.get(key);
        }
        
        const result = fn(...args);
        cache.set(key, result);
        return result;
    };
}

// Expensive function
function fibonacci(n) {
    if (n <= 1) return n;
    return fibonacci(n - 1) + fibonacci(n - 2);
}

// Memoized version
const memoizedFibonacci = createMemoizedFunction(fibonacci);

// Performance comparison
function compareMemoization() {
    console.time('Regular Fibonacci');
    const result1 = fibonacci(30);
    console.timeEnd('Regular Fibonacci');
    
    console.time('Memoized Fibonacci');
    const result2 = memoizedFibonacci(30);
    console.timeEnd('Memoized Fibonacci');
    
    console.log(`Results match: ${result1 === result2}`);
}
```

### Lazy Loading
```javascript
// Lazy loading for images
class LazyImageLoader {
    constructor() {
        this.observer = new IntersectionObserver(this.handleIntersection.bind(this));
        this.loadedImages = new Set();
    }
    
    observe(image) {
        this.observer.observe(image);
    }
    
    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const image = entry.target;
                this.loadImage(image);
                this.observer.unobserve(image);
            }
        });
    }
    
    loadImage(image) {
        if (this.loadedImages.has(image)) {
            return;
        }
        
        const src = image.dataset.src;
        if (!src) return;
        
        image.src = src;
        image.classList.add('loaded');
        this.loadedImages.add(image);
    }
}

// Lazy loading for components
class LazyComponentLoader {
    constructor() {
        this.components = new Map();
        this.loadedComponents = new Set();
    }
    
    register(id, loader) {
        this.components.set(id, loader);
    }
    
    async load(id) {
        if (this.loadedComponents.has(id)) {
            return this.components.get(id);
        }
        
        const loader = this.components.get(id);
        if (!loader) {
            throw new Error(`Component ${id} not registered`);
        }
        
        const component = await loader();
        this.loadedComponents.add(id);
        return component;
    }
}

// Usage
const imageLoader = new LazyImageLoader();
document.querySelectorAll('img[data-src]').forEach(img => {
    imageLoader.observe(img);
});

const componentLoader = new LazyComponentLoader();
componentLoader.register('user-profile', async () => {
    const response = await fetch('/api/user-profile');
    const data = await response.json();
    return renderUserProfile(data);
});
```

## Memory Management

### Memory Optimization Techniques
```javascript
// Object pooling for memory efficiency
class ObjectPool {
    constructor(createFn, resetFn, maxSize = 10) {
        this.createFn = createFn;
        this.resetFn = resetFn;
        this.maxSize = maxSize;
        this.pool = [];
        this.created = 0;
    }
    
    acquire() {
        if (this.pool.length > 0) {
            return this.pool.pop();
        }
        
        if (this.created < this.maxSize) {
            this.created++;
            return this.createFn();
        }
        
        throw new Error('Pool exhausted');
    }
    
    release(obj) {
        if (this.pool.length < this.maxSize) {
            this.resetFn(obj);
            this.pool.push(obj);
        }
    }
    
    clear() {
        this.pool = [];
        this.created = 0;
    }
}

// Usage example
const particlePool = new ObjectPool(
    () => ({
        x: 0,
        y: 0,
        vx: Math.random() * 2 - 1,
        vy: Math.random() * 2 - 1,
        life: 100
    }),
    (obj) => {
        obj.x = 0;
        obj.y = 0;
        obj.life = 100;
    }
);

// WeakMap for memory-efficient caching
class WeakCache {
    constructor() {
        this.cache = new WeakMap();
    }
    
    get(key) {
        return this.cache.get(key);
    }
    
    set(key, value) {
        this.cache.set(key, value);
    }
    
    has(key) {
        return this.cache.has(key);
    }
}

// Memory leak prevention
class MemoryLeakPreventer {
    constructor() {
        this.timers = new Set();
        this.listeners = new Set();
        this.observers = new Set();
    }
    
    setTimeout(callback, delay) {
        const timerId = setTimeout(() => {
            callback();
            this.timers.delete(timerId);
        }, delay);
        
        this.timers.add(timerId);
        return timerId;
    }
    
    clearTimeout(timerId) {
        clearTimeout(timerId);
        this.timers.delete(timerId);
    }
    
    addEventListener(element, event, handler, options) {
        element.addEventListener(event, handler, options);
        
        const cleanup = () => {
            element.removeEventListener(event, handler, options);
            this.listeners.delete(cleanup);
        };
        
        this.listeners.add(cleanup);
        return cleanup;
    }
    
    addObserver(target, callback, options) {
        const observer = new IntersectionObserver(callback, options);
        observer.observe(target);
        
        const cleanup = () => {
            observer.disconnect();
            this.observers.delete(cleanup);
        };
        
        this.observers.add(cleanup);
        return cleanup;
    }
    
    cleanup() {
        // Clear all timers
        this.timers.forEach(timerId => clearTimeout(timerId));
        this.timers.clear();
        
        // Remove all event listeners
        this.listeners.forEach(cleanup => cleanup());
        this.listeners.clear();
        
        // Disconnect all observers
        this.observers.forEach(cleanup => cleanup());
        this.observers.clear();
    }
}
```

### Garbage Collection Optimization
```javascript
// Garbage collection optimization techniques
function optimizeGarbageCollection() {
    // Avoid creating unnecessary objects in loops
    function badObjectCreation() {
        const items = [];
        for (let i = 0; i < 1000; i++) {
            // Creates new object each iteration
            items.push({
                id: i,
                value: i * 2,
                processed: false
            });
        }
        return items;
    }
    
    function goodObjectCreation() {
        const items = [];
        const temp = {};
        
        for (let i = 0; i < 1000; i++) {
            // Reuse temporary object
            temp.id = i;
            temp.value = i * 2;
            temp.processed = false;
            
            items.push({ ...temp });
        }
        return items;
    }
    
    // Avoid unnecessary closures
    function badClosures() {
        const elements = document.querySelectorAll('.item');
        
        elements.forEach((element, index) => {
            // Creates new function for each element
            element.addEventListener('click', function() {
                console.log(`Item ${index} clicked`);
            });
        });
    }
    
    function goodClosures() {
        const elements = document.querySelectorAll('.item');
        
        // Single function reused
        function handleClick(event) {
            const index = Array.from(elements).indexOf(event.target);
            console.log(`Item ${index} clicked`);
        }
        
        elements.forEach(element => {
            element.addEventListener('click', handleClick);
        });
    }
    
    // Clear references when done
    function clearReferences() {
        const largeObject = {
            data: new Array(1000000).fill(0),
            metadata: {
                created: new Date(),
                version: '1.0'
            }
        };
        
        // Use large object
        console.log(largeObject.data.length);
        
        // Clear reference when done
        largeObject.data = null;
        largeObject.metadata = null;
        
        // Set to null to help GC
        return null;
    }
}
```

## Network Performance

### Efficient Data Fetching
```javascript
// Efficient data fetching with caching and batching
class DataFetcher {
    constructor(baseURL) {
        this.baseURL = baseURL;
        this.cache = new Map();
        this.batchQueue = [];
        this.batchTimeout = null;
    }
    
    async fetch(endpoint, options = {}) {
        const cacheKey = `${endpoint}:${JSON.stringify(options)}`;
        
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }
        
        try {
            const response = await fetch(`${this.baseURL}${endpoint}`, options);
            const data = await response.json();
            
            this.cache.set(cacheKey, data);
            return data;
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    }
    
    batchFetch(endpoints) {
        return new Promise((resolve, reject) => {
            this.batchQueue.push({ endpoints, resolve, reject });
            
            if (!this.batchTimeout) {
                this.batchTimeout = setTimeout(() => {
                    this.processBatch();
                }, 10);
            }
        });
    }
    
    async processBatch() {
        const batch = [...this.batchQueue];
        this.batchQueue = [];
        this.batchTimeout = null;
        
        try {
            const response = await fetch(`${this.baseURL}/batch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    requests: batch.map(item => item.endpoints)
                })
            });
            
            const data = await response.json();
            
            batch.forEach((item, index) => {
                if (data.errors[index]) {
                    item.reject(new Error(data.errors[index]));
                } else {
                    item.resolve(data.results[index]);
                }
            });
            
        } catch (error) {
            batch.forEach(item => item.reject(error));
        }
    }
}

// Request deduplication
class RequestDeduplicator {
    constructor() {
        this.pendingRequests = new Map();
    }
    
    async request(key, requestFn) {
        if (this.pendingRequests.has(key)) {
            return this.pendingRequests.get(key);
        }
        
        const promise = requestFn().finally(() => {
            this.pendingRequests.delete(key);
        });
        
        this.pendingRequests.set(key, promise);
        return promise;
    }
}

// Usage
const dataFetcher = new DataFetcher('https://api.example.com');
const deduplicator = new RequestDeduplicator();

// Individual requests (cached)
const user = await dataFetcher.fetch('/users/1');
const posts = await dataFetcher.fetch('/posts?userId=1');

// Batch requests
const [user2, posts2] = await Promise.all([
    dataFetcher.fetch('/users/2'),
    dataFetcher.fetch('/posts?userId=2')
]);

// Deduplicated requests
const user3 = await deduplicator.request('user-1', () => 
    dataFetcher.fetch('/users/1')
);
const user4 = await deduplicator.request('user-1', () => 
    dataFetcher.fetch('/users/1')
); // Returns same promise
```

### Resource Loading Optimization
```javascript
// Resource loading optimization
class ResourceLoader {
    constructor() {
        this.cache = new Map();
        this.loading = new Map();
    }
    
    async loadResource(url) {
        if (this.cache.has(url)) {
            return this.cache.get(url);
        }
        
        if (this.loading.has(url)) {
            return this.loading.get(url);
        }
        
        const promise = this.fetchResource(url);
        this.loading.set(url, promise);
        
        try {
            const resource = await promise;
            this.cache.set(url, resource);
            return resource;
        } finally {
            this.loading.delete(url);
        }
    }
    
    async fetchResource(url) {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Failed to load resource: ${url}`);
        }
        return response.blob();
    }
    
    preloadResources(urls) {
        return Promise.all(urls.map(url => this.loadResource(url)));
    }
    
    clearCache() {
        this.cache.clear();
    }
}

// Critical resource loading
class CriticalResourceLoader {
    constructor() {
        this.critical = new Set();
        this.deferred = new Set();
    }
    
    addCritical(url) {
        this.critical.add(url);
    }
    
    addDeferred(url) {
        this.deferred.add(url);
    }
    
    async loadCritical() {
        const promises = Array.from(this.critical).map(url => this.load(url));
        await Promise.all(promises);
    }
    
    loadDeferred() {
        const promises = Array.from(this.deferred).map(url => 
            this.load(url).catch(error => console.warn('Deferred load failed:', url, error))
        );
        return Promise.all(promises);
    }
    
    async load(url) {
        try {
            const response = await fetch(url);
            return await response.blob();
        } catch (error) {
            console.error('Failed to load resource:', url, error);
            throw error;
        }
    }
}

// Usage
const resourceLoader = new ResourceLoader();
const criticalLoader = new CriticalResourceLoader();

// Add critical resources
criticalLoader.addCritical('/css/main.css');
criticalLoader.addCritical('/js/main.js');

// Add deferred resources
criticalLoader.addDeferred('/images/logo.png');
criticalLoader.addDeferred('/fonts/main.woff2');

// Load critical resources first
await criticalLoader.loadCritical();

// Then load deferred resources
criticalLoader.loadDeferred();
```

## Animation Performance

### Efficient Animations
```javascript
// Efficient animation using requestAnimationFrame
class SmoothAnimator {
    constructor() {
        this.animations = new Map();
        this.isRunning = false;
        this.lastTime = 0;
    }
    
    animate(id, duration, updateFn, completeFn) {
        this.animations.set(id, {
            duration,
            updateFn,
            completeFn,
            startTime: performance.now(),
            progress: 0
        });
        
        if (!this.isRunning) {
            this.start();
        }
    }
    
    start() {
        this.isRunning = true;
        this.lastTime = performance.now();
        this.step();
    }
    
    step() {
        if (!this.isRunning) return;
        
        const currentTime = performance.now();
        const deltaTime = currentTime - this.lastTime;
        this.lastTime = currentTime;
        
        let hasActiveAnimations = false;
        
        for (const [id, animation] of this.animations) {
            const elapsed = currentTime - animation.startTime;
            animation.progress = Math.min(elapsed / animation.duration, 1);
            
            if (animation.progress < 1) {
                animation.updateFn(animation.progress, deltaTime);
                hasActiveAnimations = true;
            } else {
                animation.completeFn();
                this.animations.delete(id);
            }
        }
        
        if (hasActiveAnimations) {
            requestAnimationFrame(() => this.step());
        } else {
            this.isRunning = false;
        }
    }
    
    stop(id) {
        if (id) {
            const animation = this.animations.get(id);
            if (animation) {
                animation.completeFn();
                this.animations.delete(id);
            }
        } else {
            this.animations.clear();
            this.isRunning = false;
        }
    }
    
    isAnimating(id) {
        return this.animations.has(id);
    }
}

// CSS-based animation performance
class CSSAnimator {
    constructor() {
        this.elements = new Map();
    }
    
    animate(element, properties, duration, easing = 'ease') {
        const elementId = this.getElementId(element);
        
        // Cancel existing animation
        this.cancel(elementId);
        
        // Set CSS transitions
        const transition = `${Object.keys(properties).map(prop => `${prop} ${duration}ms ${easing}`).join(', ')}`;
        element.style.transition = transition;
        
        // Apply target properties
        Object.assign(element.style, properties);
        
        // Track animation
        this.elements.set(elementId, {
            element,
            timeout: setTimeout(() => {
                element.style.transition = '';
                this.elements.delete(elementId);
            }, duration)
        });
    }
    
    cancel(elementId) {
        const animation = this.elements.get(elementId);
        if (animation) {
            clearTimeout(animation.timeout);
            animation.element.style.transition = '';
            this.elements.delete(elementId);
        }
    }
    
    getElementId(element) {
        if (typeof element === 'string') {
            return element;
        }
        
        let id = element.dataset.animationId;
        if (!id) {
            id = `anim-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
            element.dataset.animationId = id;
        }
        
        return id;
    }
}

// Web Animations API
class WebAnimator {
    constructor() {
        this.animations = new Map();
    }
    
    animate(element, keyframes, options = {}) {
        const elementId = this.getElementId(element);
        
        // Cancel existing animation
        this.cancel(elementId);
        
        const animation = element.animate(keyframes, {
            duration: 1000,
            easing: 'ease',
            fill: 'forwards',
            ...options
        });
        
        this.animations.set(elementId, animation);
        
        animation.onfinish = () => {
            this.animations.delete(elementId);
        };
        
        return animation;
    }
    
    cancel(elementId) {
        const animation = this.animations.get(elementId);
        if (animation) {
            animation.cancel();
            this.animations.delete(elementId);
        }
    }
    
    getElementId(element) {
        if (typeof element === 'string') {
            return element;
        }
        
        let id = element.dataset.webAnimationId;
        if (!id) {
            id = `web-anim-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
            element.dataset.webAnimationId = id;
        }
        
        return id;
    }
}
```

### Performance-Optimized Animations
```javascript
// Performance-optimized scrolling animation
class ScrollAnimator {
    constructor() {
        this.isAnimating = false;
        this.startY = 0;
        this.targetY = 0;
        this.duration = 0;
        this.startTime = 0;
        this.animationId = null;
    }
    
    scrollTo(targetY, duration = 300) {
        this.startY = window.pageYOffset;
        this.targetY = targetY;
        this.duration = duration;
        this.startTime = performance.now();
        
        if (!this.isAnimating) {
            this.isAnimating = true;
            this.animate();
        }
    }
    
    animate() {
        const currentTime = performance.now();
        const elapsed = currentTime - this.startTime;
        const progress = Math.min(elapsed / this.duration, 1);
        
        // Easing function (ease-out cubic)
        const easeProgress = 1 - Math.pow(1 - progress, 3);
        
        const currentY = this.startY + (this.targetY - this.startY) * easeProgress;
        
        window.scrollTo(0, currentY);
        
        if (progress < 1) {
            this.animationId = requestAnimationFrame(() => this.animate());
        } else {
            this.isAnimating = false;
        }
    }
    
    stop() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.isAnimating = false;
        }
    }
}

// GPU-accelerated animations
class GPUAnimator {
    constructor() {
        this.container = document.createElement('div');
        this.container.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 9999;
            will-change: transform;
        `;
        document.body.appendChild(this.container);
    }
    
    createElement(width, height, backgroundColor) {
        const element = document.createElement('div');
        element.style.cssText = `
            position: absolute;
            width: ${width}px;
            height: ${height}px;
            background-color: ${backgroundColor};
            will-change: transform;
            transform: translateZ(0);
        `;
        return element;
    }
    
    animate(element, keyframes, duration) {
        const animation = element.animate(keyframes, {
            duration,
            easing: 'ease-in-out',
            composite: 'accumulate'
        });
        
        return animation;
    }
    
    cleanup() {
        document.body.removeChild(this.container);
    }
}
```

## Performance Monitoring

### Real-time Performance Monitoring
```javascript
// Real-time performance monitoring
class PerformanceMonitor {
    constructor() {
        this.metrics = {
            fps: [],
            memory: [],
            network: [],
            render: []
        };
        
        this.fps = 0;
        this.frameCount = 0;
        this.lastFrameTime = performance.now();
        
        this.isMonitoring = false;
        this.observers = new Map();
        
        this.setupFPSMonitoring();
        this.setupMemoryMonitoring();
        this.setupNetworkMonitoring();
    }
    
    start() {
        this.isMonitoring = true;
        this.startFPSMonitoring();
        this.startMemoryMonitoring();
        this.startNetworkMonitoring();
    }
    
    stop() {
        this.isMonitoring = false;
        this.stopFPSMonitoring();
        this.stopMemoryMonitoring();
        this.stopNetworkMonitoring();
    }
    
    setupFPSMonitoring() {
        const measureFPS = () => {
            if (!this.isMonitoring) return;
            
            this.frameCount++;
            const currentTime = performance.now();
            
            if (currentTime - this.lastFrameTime >= 1000) {
                this.fps = Math.round((this.frameCount * 1000) / (currentTime - this.lastFrameTime));
                this.metrics.fps.push({
                    timestamp: currentTime,
                    fps: this.fps
                });
                
                this.frameCount = 0;
                this.lastFrameTime = currentTime;
            }
            
            requestAnimationFrame(measureFPS);
        };
        
        this.fpsAnimationId = requestAnimationFrame(measureFPS);
    }
    
    startFPSMonitoring() {
        this.fpsAnimationId = requestAnimationFrame(() => this.measureFPS());
    }
    
    stopFPSMonitoring() {
        if (this.fpsAnimationId) {
            cancelAnimationFrame(this.fpsAnimationId);
        }
    }
    
    measureFPS() {
        this.frameCount++;
        const currentTime = performance.now();
        
        if (currentTime - this.lastFrameTime >= 1000) {
            this.fps = Math.round((this.frameCount * 1000) / (currentTime - this.lastFrameTime));
            this.metrics.fps.push({
                timestamp: currentTime,
                fps: this.fps
            });
            
            this.frameCount = 0;
            this.lastFrameTime = currentTime;
        }
    }
    
    setupMemoryMonitoring() {
        this.memoryInterval = setInterval(() => {
            if (!this.isMonitoring) return;
            
            if (performance.memory) {
                this.metrics.memory.push({
                    timestamp: performance.now(),
                    used: performance.memory.usedJSHeapSize,
                    total: performance.memory.totalJSHeapSize,
                    limit: performance.memory.jsHeapSizeLimit
                });
            }
        }, 1000);
    }
    
    startMemoryMonitoring() {
        this.setupMemoryMonitoring();
    }
    
    stopMemoryMonitoring() {
        if (this.memoryInterval) {
            clearInterval(this.memoryInterval);
        }
    }
    
    setupNetworkMonitoring() {
        // Monitor resource loading
        this.networkObserver = new PerformanceObserver((list) => {
            list.getEntries().forEach(entry => {
                if (entry.initiatorType === 'resource') {
                    this.metrics.network.push({
                        timestamp: entry.startTime,
                        name: entry.name,
                        duration: entry.duration,
                        size: entry.transferSize,
                        type: entry.initiatorType
                    });
                }
            });
        });
        
        this.networkObserver.observe({ entryTypes: ['resource'] });
    }
    
    startNetworkMonitoring() {
        // Network observer is already set up
    }
    
    stopNetworkMonitoring() {
        if (this.networkObserver) {
            this.networkObserver.disconnect();
        }
    }
    
    getMetrics() {
        return {
            fps: this.metrics.fps,
            memory: this.metrics.memory,
            network: this.metrics.network,
            currentFPS: this.fps
        };
    }
    
    getAverageFPS() {
        const recentFPS = this.metrics.fps.slice(-60); // Last 60 seconds
        return recentFPS.reduce((sum, metric) => sum + metric.fps, 0) / recentFPS.length;
    }
    
    getMemoryUsage() {
        if (!performance.memory) return null;
        
        return {
            used: performance.memory.usedJSHeapSize,
            total: performance.memory.totalJSHeapSize,
            limit: performance.memory.jsHeapSizeLimit,
            percentage: (performance.memory.usedJSHeapSize / performance.memory.jsHeapSizeLimit) * 100
        };
    }
}

// Performance profiling for specific operations
class OperationProfiler {
    constructor() {
        this.profiles = new Map();
    }
    
    profile(name, operation) {
        const startTime = performance.now();
        
        return operation().then(result => {
            const endTime = performance.now();
            const duration = endTime - startTime;
            
            this.addProfile(name, duration, true);
            return result;
        }).catch(error => {
            const endTime = performance.now();
            const duration = endTime - startTime;
            
            this.addProfile(name, duration, false);
            throw error;
        });
    }
    
    profileSync(name, operation) {
        const startTime = performance.now();
        
        try {
            const result = operation();
            const endTime = performance.now();
            const duration = endTime - startTime;
            
            this.addProfile(name, duration, true);
            return result;
        } catch (error) {
            const endTime = performance.now();
            const duration = endTime - startTime;
            
            this.addProfile(name, duration, false);
            throw error;
        }
    }
    
    addProfile(name, duration, success) {
        if (!this.profiles.has(name)) {
            this.profiles.set(name, []);
        }
        
        this.profiles.get(name).push({
            timestamp: performance.now(),
            duration,
            success
        });
    }
    
    getStats(name) {
        const profiles = this.profiles.get(name) || [];
        
        if (profiles.length === 0) {
            return null;
        }
        
        const durations = profiles.map(p => p.duration);
        const successes = profiles.filter(p => p.success).length;
        
        return {
            count: profiles.length,
            average: durations.reduce((sum, d) => sum + d, 0) / durations.length,
            min: Math.min(...durations),
            max: Math.max(...durations),
            successRate: (successes / profiles.length) * 100
        };
    }
    
    getAllStats() {
        const stats = {};
        
        for (const [name] of this.profiles) {
            stats[name] = this.getStats(name);
        }
        
        return stats;
    }
}
```

## Best Practices

### Performance Best Practices
```javascript
// 1. Use requestAnimationFrame for animations
function goodAnimation() {
    let start;
    
    function animate(timestamp) {
        if (!start) start = timestamp;
        
        const progress = timestamp - start;
        
        if (progress < 1000) {
            // Animation logic
            requestAnimationFrame(animate);
        }
    }
    
    requestAnimationFrame(animate);
}

// 2. Debounce expensive operations
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// 3. Throttle rapid events
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// 4. Use Web Workers for heavy computations
function useWebWorker() {
    const worker = new Worker('heavy-computation.js');
    
    worker.onmessage = function(e) {
        console.log('Worker result:', e.data);
    };
    
    worker.postMessage({ type: 'compute', data: largeDataset });
}

// 5. Optimize loops
function optimizedLoop() {
    // Bad: creates new objects in loop
    for (let i = 0; i < 1000; i++) {
        const obj = { index: i, value: i * 2 };
        processObject(obj);
    }
    
    // Good: reuse object
    const temp = {};
    for (let i = 0; i < 1000; i++) {
        temp.index = i;
        temp.value = i * 2;
        processObject(temp);
    }
}

// 6. Use efficient data structures
function efficientDataStructures() {
    // Use Set for O(1) lookups
    const validIds = new Set([1, 2, 3, 4, 5]);
    const isValid = validIds.has(id);
    
    // Use Map for key-value pairs
    const cache = new Map();
    cache.set('key1', 'value1');
    const value = cache.get('key1');
}

// 7. Minimize DOM operations
function minimizeDOMOperations() {
    // Bad: multiple DOM operations
    container.innerHTML = '';
    container.appendChild(createElement('div'));
    container.appendChild(createElement('span'));
    container.style.color = 'red';
    
    // Good: batch operations
    const fragment = document.createDocumentFragment();
    fragment.appendChild(createElement('div'));
    fragment.appendChild(createElement('span'));
    
    container.innerHTML = '';
    container.appendChild(fragment);
    container.style.color = 'red';
}

// 8. Use CSS transforms instead of changing layout properties
function useCSSTransforms() {
    // Bad: changes layout
    element.style.width = '100px';
    element.style.height = '100px';
    element.style.left = '50px';
    element.style.top = '50px';
    
    // Good: use transforms
    element.style.transform = 'translate(50px, 50px) scale(1, 1)';
}

// 9. Lazy load resources
function lazyLoadResources() {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                imageObserver.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// 10. Profile and measure performance
function profilePerformance() {
    const profiler = new OperationProfiler();
    
    profiler.profile('database-query', () => {
        return database.query('SELECT * FROM users');
    });
    
    profiler.profile('api-call', () => {
        return fetch('/api/data');
    });
    
    setTimeout(() => {
        console.log('Performance stats:', profiler.getAllStats());
    }, 5000);
}
```

## Summary

JavaScript performance optimization covers:

**Measurement:**
- Performance API for timing
- Console performance tools
- Memory usage monitoring
- Custom profiling utilities

**DOM Performance:**
- Batch DOM operations
- Event delegation
- Virtual scrolling
- Efficient rendering

**Algorithm Optimization:**
- Efficient data structures
- Memoization
- Lazy loading
- Algorithm comparison

**Memory Management:**
- Object pooling
- WeakMap/WeakSet for caching
- Memory leak prevention
- Garbage collection optimization

**Network Performance:**
- Request caching and batching
- Request deduplication
- Resource loading optimization
- Critical resource loading

**Animation Performance:**
- requestAnimationFrame
- CSS transitions
- Web Animations API
- GPU acceleration

**Monitoring:**
- Real-time performance monitoring
- FPS and memory tracking
- Network performance
- Operation profiling

**Best Practices:**
- Use appropriate animation methods
- Debounce/throttle events
- Use Web Workers for heavy tasks
- Optimize loops and data structures
- Minimize DOM operations
- Profile and measure performance

Performance optimization is crucial for creating fast, responsive web applications that provide excellent user experiences.
