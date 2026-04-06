# JavaScript DOM Manipulation

## DOM Basics

### Understanding the DOM
```javascript
// DOM (Document Object Model) represents the HTML document as a tree structure
// The document object is the entry point to the DOM

console.log(document); // The entire document
console.log(document.documentElement); // The <html> element
console.log(document.head); // The <head> element
console.log(document.body); // The <body> element

// DOM tree structure
/*
Document
├── html
    ├── head
    │   ├── title
    │   ├── meta
    │   └── link
    └── body
        ├── header
        ├── main
        │   ├── section
        │   └── article
        └── footer
*/
```

### Node Types
```javascript
// Different types of nodes in the DOM
console.log(document.ELEMENT_NODE); // 1
console.log(document.TEXT_NODE); // 3
console.log(document.COMMENT_NODE); // 8
console.log(document.DOCUMENT_NODE); // 9

// Check node type
const element = document.body;
console.log(element.nodeType); // 1 (Element)
console.log(element.nodeName); // "BODY"
```

## Selecting Elements

### Basic Selectors
```html
<!-- Sample HTML for examples -->
<div id="container">
    <h1 class="title">Hello World</h1>
    <p class="paragraph">This is a paragraph.</p>
    <ul class="list">
        <li class="item">Item 1</li>
        <li class="item">Item 2</li>
        <li class="item">Item 3</li>
    </ul>
    <div class="box">Box 1</div>
    <div class="box">Box 2</div>
</div>
```

```javascript
// getElementById - returns single element
const container = document.getElementById('container');
console.log(container);

// getElementsByClassName - returns HTMLCollection (live)
const paragraphs = document.getElementsByClassName('paragraph');
console.log(paragraphs);

// getElementsByTagName - returns HTMLCollection (live)
const listItems = document.getElementsByTagName('li');
console.log(listItems);

// querySelector - returns first matching element
const firstItem = document.querySelector('.item');
console.log(firstItem);

// querySelectorAll - returns NodeList (static)
const allItems = document.querySelectorAll('.item');
console.log(allItems);
```

### Advanced Selectors
```javascript
// Complex CSS selectors
const firstListItem = document.querySelector('ul.list > li:first-child');
const lastListItem = document.querySelector('ul.list > li:last-child');
const evenItems = document.querySelectorAll('ul.list > li:nth-child(even)');
const oddItems = document.querySelectorAll('ul.list > li:nth-child(odd)');

// Attribute selectors
const dataElements = document.querySelectorAll('[data-id]');
const specificData = document.querySelectorAll('[data-id="123"]');
const startsWith = document.querySelectorAll('[class^="para"]');
const contains = document.querySelectorAll('[class*="item"]');

// Pseudo-selectors
const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');
const disabledElements = document.querySelectorAll(':disabled');
const visibleElements = document.querySelectorAll(':not([style*="display: none"])');
```

## Creating and Modifying Elements

### Creating Elements
```javascript
// Create element
const newDiv = document.createElement('div');
newDiv.textContent = 'New element';
newDiv.className = 'new-element';
newDiv.id = 'my-element';

// Create text node
const textNode = document.createTextNode('Some text content');

// Create comment
const comment = document.createComment('This is a comment');

// Create document fragment (for performance)
const fragment = document.createDocumentFragment();
fragment.appendChild(newDiv);
fragment.appendChild(textNode);

// Add to DOM
document.body.appendChild(fragment);
```

### Modifying Elements
```javascript
// Get existing element
const box = document.querySelector('.box');

// Modify content
box.textContent = 'Updated content';
box.innerHTML = '<strong>Bold content</strong>';

// Modify attributes
box.setAttribute('data-role', 'container');
box.id = 'unique-id';
box.className = 'updated-box another-class';

// Modify styles
box.style.color = 'red';
box.style.backgroundColor = 'lightblue';
box.style.padding = '10px';
box.style.border = '1px solid #333';

// Multiple styles at once
Object.assign(box.style, {
    fontSize: '16px',
    fontWeight: 'bold',
    textTransform: 'uppercase'
});
```

### Class Manipulation
```javascript
const element = document.querySelector('.title');

// Add classes
element.classList.add('highlight', 'large');
console.log(element.className);

// Remove classes
element.classList.remove('highlight');
console.log(element.className);

// Toggle classes
element.classList.toggle('active');
console.log(element.classList.contains('active'));

// Replace class
element.classList.replace('title', 'heading');

// Check if class exists
if (element.classList.contains('large')) {
    console.log('Element has large class');
}

// Add multiple classes with spread operator
const classesToAdd = ['primary', 'bold'];
element.classList.add(...classesToAdd);
```

## DOM Traversal

### Parent and Child Relationships
```javascript
// Get parent element
const listItem = document.querySelector('.item');
const parent = listItem.parentElement;
console.log(parent);

// Get children
const list = document.querySelector('.list');
const children = list.children;
console.log(children);

// First and last child
console.log(list.firstElementChild);
console.log(list.lastElementChild);

// Child nodes (includes text nodes, comments, etc.)
const childNodes = list.childNodes;
console.log(childNodes);

// Element children only (ignores text nodes, comments)
const elementChildren = list.children;
console.log(elementChildren);
```

### Sibling Relationships
```javascript
// Get siblings
const middleItem = document.querySelectorAll('.item')[1];

// Previous and next siblings (including text nodes)
console.log(middleItem.previousSibling);
console.log(middleItem.nextSibling);

// Previous and next element siblings only
console.log(middleItem.previousElementSibling);
console.log(middleItem.nextElementSibling);

// All siblings
const allSiblings = Array.from(middleItem.parentElement.children)
    .filter(child => child !== middleItem);
console.log(allSiblings);
```

### Finding Elements
```javascript
// Find closest ancestor matching selector
const item = document.querySelector('.item');
const closestDiv = item.closest('div');
console.log(closestDiv);

// Find elements within context
const container = document.getElementById('container');
const itemsInContainer = container.querySelectorAll('.item');
console.log(itemsInContainer);

// Find by text content
function findByText(selector, text) {
    const elements = document.querySelectorAll(selector);
    return Array.from(elements).find(el => el.textContent.includes(text));
}

const foundElement = findByText('.item', 'Item 2');
console.log(foundElement);
```

## Adding and Removing Elements

### Adding Elements
```javascript
// Append to parent
const parent = document.querySelector('.list');
const newItem = document.createElement('li');
newItem.textContent = 'New item';
newItem.className = 'item';
parent.appendChild(newItem);

// Insert at specific position
const secondItem = parent.children[1];
const insertedItem = document.createElement('li');
insertedItem.textContent = 'Inserted item';
parent.insertBefore(insertedItem, secondItem);

// Insert after (helper function)
function insertAfter(newElement, targetElement) {
    targetElement.parentNode.insertBefore(
        newElement, 
        targetElement.nextSibling
    );
}

const afterItem = document.createElement('li');
afterItem.textContent = 'After item';
insertAfter(afterItem, secondItem);

// Prepend (insert at beginning)
const firstItem = parent.firstElementChild;
const prependedItem = document.createElement('li');
prependedItem.textContent = 'First item';
parent.insertBefore(prependedItem, firstItem);
```

### Removing Elements
```javascript
// Remove element
const itemToRemove = document.querySelector('.item:last-child');
itemToRemove.remove();

// Remove all children
const container = document.getElementById('container');
while (container.firstChild) {
    container.removeChild(container.firstChild);
}

// Alternative: set innerHTML to empty
container.innerHTML = '';

// Remove with condition
const allBoxes = document.querySelectorAll('.box');
allBoxes.forEach(box => {
    if (box.textContent === 'Box 1') {
        box.remove();
    }
});
```

### Replacing Elements
```javascript
// Replace element
const oldElement = document.querySelector('.title');
const newElement = document.createElement('h2');
newElement.textContent = 'New Title';
newElement.className = 'title';

oldElement.parentNode.replaceChild(newElement, oldElement);

// Replace with innerHTML
const container = document.querySelector('.container');
container.innerHTML = `
    <h2>New Content</h2>
    <p>This replaces all existing content</p>
`;
```

## Event Handling

### Basic Event Handling
```javascript
// Add event listener
const button = document.createElement('button');
button.textContent = 'Click me';
document.body.appendChild(button);

button.addEventListener('click', function(event) {
    console.log('Button clicked!');
    console.log('Event object:', event);
    console.log('Target:', event.target);
});

// Event listener with arrow function (this is different)
button.addEventListener('mouseover', () => {
    button.style.backgroundColor = 'lightblue';
});

// Event listener with regular function (this refers to element)
button.addEventListener('mouseout', function() {
    this.style.backgroundColor = '';
});

// Remove event listener
function handleClick(event) {
    console.log('Handled click');
    button.removeEventListener('click', handleClick);
}

button.addEventListener('click', handleClick);
```

### Event Propagation
```javascript
// Event bubbling (from child to parent)
const outer = document.createElement('div');
outer.style.padding = '20px';
outer.style.backgroundColor = 'lightgray';
outer.textContent = 'Outer';

const inner = document.createElement('div');
inner.style.padding = '10px';
inner.style.backgroundColor = 'lightblue';
inner.textContent = 'Inner';

outer.appendChild(inner);
document.body.appendChild(outer);

outer.addEventListener('click', function(event) {
    console.log('Outer clicked');
    console.log('Current target:', event.currentTarget);
    console.log('Target:', event.target);
});

inner.addEventListener('click', function(event) {
    console.log('Inner clicked');
    // event.stopPropagation(); // Stop bubbling
});

// Event delegation (handle events on parent)
const list = document.querySelector('.list');
list.addEventListener('click', function(event) {
    if (event.target.classList.contains('item')) {
        console.log('Item clicked:', event.target.textContent);
    }
});
```

### Common Events
```javascript
const input = document.createElement('input');
input.type = 'text';
input.placeholder = 'Type here...';
document.body.appendChild(input);

// Keyboard events
input.addEventListener('keydown', function(event) {
    console.log('Key down:', event.key);
});

input.addEventListener('keyup', function(event) {
    console.log('Key up:', event.key);
});

input.addEventListener('keypress', function(event) {
    console.log('Key press:', event.key);
});

// Form events
const form = document.createElement('form');
form.innerHTML = `
    <input type="text" name="username" placeholder="Username">
    <button type="submit">Submit</button>
`;
document.body.appendChild(form);

form.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission
    console.log('Form submitted');
    const formData = new FormData(form);
    console.log('Username:', formData.get('username'));
});

// Window events
window.addEventListener('load', function() {
    console.log('Page fully loaded');
});

window.addEventListener('resize', function() {
    console.log('Window resized:', window.innerWidth, 'x', window.innerHeight);
});

window.addEventListener('scroll', function() {
    console.log('Page scrolled:', window.scrollY);
});

// Mouse events
document.addEventListener('mousemove', function(event) {
    // console.log('Mouse position:', event.clientX, event.clientY);
});

document.addEventListener('click', function(event) {
    console.log('Document clicked at:', event.clientX, event.clientY);
});
```

## Advanced DOM Manipulation

### Performance Optimization
```javascript
// Use document fragment for multiple additions
function addManyItems inefficient() {
    const list = document.querySelector('.list');
    
    for (let i = 0; i < 1000; i++) {
        const item = document.createElement('li');
        item.textContent = `Item ${i}`;
        list.appendChild(item); // Triggers reflow each time
    }
}

function addManyItems efficient() {
    const list = document.querySelector('.list');
    const fragment = document.createDocumentFragment();
    
    for (let i = 0; i < 1000; i++) {
        const item = document.createElement('li');
        item.textContent = `Item ${i}`;
        fragment.appendChild(item); // No reflow
    }
    
    list.appendChild(fragment); // Single reflow
}

// Batch DOM updates
function batchUpdates() {
    const element = document.querySelector('.title');
    
    // Bad: multiple reflows
    element.style.padding = '10px';
    element.style.margin = '10px';
    element.style.border = '1px solid red';
    
    // Good: single reflow
    Object.assign(element.style, {
        padding: '10px',
        margin: '10px',
        border: '1px solid red'
    });
}
```

### Template Literals for HTML
```javascript
// Using template literals for HTML generation
function generateUserCard(user) {
    return `
        <div class="user-card">
            <img src="${user.avatar}" alt="${user.name}">
            <h3>${user.name}</h3>
            <p>${user.email}</p>
            <p>Age: ${user.age}</p>
            <button class="btn btn-primary" data-user-id="${user.id}">
                View Profile
            </button>
        </div>
    `;
}

const user = {
    id: 1,
    name: 'John Doe',
    email: 'john@example.com',
    age: 30,
    avatar: 'https://picsum.photos/100/100'
};

const container = document.querySelector('.container');
container.innerHTML = generateUserCard(user);

// Sanitize HTML to prevent XSS
function sanitizeHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

const userInput = '<script>alert("XSS")</script>';
const safeHTML = sanitizeHTML(userInput);
console.log(safeHTML); // &lt;script&gt;alert("XSS")&lt;/script&gt;
```

### Dynamic Content Loading
```javascript
// Load content dynamically
async function loadContent(url) {
    try {
        const response = await fetch(url);
        const html = await response.text();
        
        const container = document.getElementById('content');
        container.innerHTML = html;
        
        // Re-initialize event listeners for new content
        initializeEventListeners();
        
    } catch (error) {
        console.error('Error loading content:', error);
        document.getElementById('content').innerHTML = 
            '<p>Error loading content</p>';
    }
}

// Initialize event listeners
function initializeEventListeners() {
    // Add event listeners to dynamically loaded content
    document.querySelectorAll('.dynamic-button').forEach(button => {
        button.addEventListener('click', handleDynamicClick);
    });
}

function handleDynamicClick(event) {
    console.log('Dynamic button clicked:', event.target);
}

// Lazy loading images
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}
```

## Modern DOM APIs

### Shadow DOM
```javascript
// Create custom element with Shadow DOM
class CustomCard extends HTMLElement {
    constructor() {
        super();
        
        // Create shadow DOM
        this.attachShadow({ mode: 'open' });
        
        // Add styles and content
        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    border: 2px solid #333;
                    padding: 16px;
                    margin: 10px;
                    border-radius: 8px;
                }
                
                .card-content {
                    color: #333;
                }
            </style>
            
            <div class="card-content">
                <slot name="title"></slot>
                <slot name="content"></slot>
            </div>
        `;
    }
}

// Register custom element
customElements.define('custom-card', CustomCard);

// Use custom element
const card = document.createElement('custom-card');
card.innerHTML = `
    <h3 slot="title">Card Title</h3>
    <p slot="content">This is the card content.</p>
`;
document.body.appendChild(card);
```

### Mutation Observer
```javascript
// Observe DOM changes
const observer = new MutationObserver((mutations) => {
    mutations.forEach(mutation => {
        console.log('Mutation detected:', mutation.type);
        
        if (mutation.type === 'childList') {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    console.log('Element added:', node);
                }
            });
            
            mutation.removedNodes.forEach(node => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    console.log('Element removed:', node);
                }
            });
        }
    });
});

// Start observing
observer.observe(document.body, {
    childList: true,    // Observe child nodes
    subtree: true,      // Observe all descendants
    attributes: true,   // Observe attribute changes
    characterData: true // Observe text changes
});

// Stop observing
// observer.disconnect();
```

### Resize Observer
```javascript
// Observe element size changes
const resizeObserver = new ResizeObserver((entries) => {
    entries.forEach(entry => {
        const { width, height } = entry.contentRect;
        console.log(`Element resized: ${width}x${height}`);
        
        // Respond to size changes
        if (width < 300) {
            entry.target.classList.add('compact');
        } else {
            entry.target.classList.remove('compact');
        }
    });
});

// Observe element
const element = document.querySelector('.responsive-element');
resizeObserver.observe(element);
```

## DOM Utilities

### Helper Functions
```javascript
// DOM utility functions
const DOM = {
    // Find element with error handling
    find(selector, context = document) {
        const element = (context || document).querySelector(selector);
        if (!element) {
            throw new Error(`Element not found: ${selector}`);
        }
        return element;
    },
    
    // Find all elements
    findAll(selector, context = document) {
        return Array.from((context || document).querySelectorAll(selector));
    },
    
    // Create element with attributes
    create(tag, attributes = {}, textContent = '') {
        const element = document.createElement(tag);
        
        Object.entries(attributes).forEach(([key, value]) => {
            if (key === 'className') {
                element.className = value;
            } else if (key === 'style' && typeof value === 'object') {
                Object.assign(element.style, value);
            } else {
                element.setAttribute(key, value);
            }
        });
        
        if (textContent) {
            element.textContent = textContent;
        }
        
        return element;
    },
    
    // Add event listeners to multiple elements
    on(elements, event, handler, options = {}) {
        const nodeList = typeof elements === 'string' 
            ? document.querySelectorAll(elements)
            : elements.length !== undefined 
                ? elements 
                : [elements];
        
        Array.from(nodeList).forEach(element => {
            element.addEventListener(event, handler, options);
        });
    },
    
    // Remove element
    remove(element) {
        if (element && element.parentNode) {
            element.parentNode.removeChild(element);
        }
    },
    
    // Toggle class
    toggleClass(element, className, force) {
        if (force !== undefined) {
            return element.classList.toggle(className, force);
        }
        return element.classList.toggle(className);
    },
    
    // Check if element is in viewport
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= window.innerHeight &&
            rect.right <= window.innerWidth
        );
    },
    
    // Scroll to element
    scrollTo(element, options = {}) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
            inline: 'nearest',
            ...options
        });
    }
};

// Usage examples
try {
    const title = DOM.find('.title');
    console.log(title);
} catch (error) {
    console.error(error.message);
}

const button = DOM.create('button', {
    className: 'btn btn-primary',
    style: { backgroundColor: 'blue', color: 'white' }
}, 'Click me');

document.body.appendChild(button);

DOM.on('.btn', 'click', function(event) {
    console.log('Button clicked:', event.target);
});
```

### Animation Utilities
```javascript
// Animation utilities
const Animation = {
    // Fade in element
    fadeIn(element, duration = 300) {
        element.style.opacity = 0;
        element.style.display = 'block';
        
        const start = performance.now();
        
        function animate(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            element.style.opacity = progress;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        }
        
        requestAnimationFrame(animate);
    },
    
    // Fade out element
    fadeOut(element, duration = 300) {
        const start = performance.now();
        const initialOpacity = parseFloat(window.getComputedStyle(element).opacity);
        
        function animate(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            element.style.opacity = initialOpacity * (1 - progress);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                element.style.display = 'none';
            }
        }
        
        requestAnimationFrame(animate);
    },
    
    // Slide element
    slide(element, direction = 'down', duration = 300) {
        const isVertical = direction === 'down' || direction === 'up';
        const property = isVertical ? 'height' : 'width';
        const startValue = isVertical ? element.offsetHeight : element.offsetWidth;
        
        element.style.overflow = 'hidden';
        element.style[property] = '0px';
        element.style.display = 'block';
        
        const start = performance.now();
        
        function animate(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            element.style[property] = `${startValue * progress}px`;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                element.style.overflow = '';
                element.style[property] = '';
            }
        }
        
        requestAnimationFrame(animate);
    }
};

// Usage
const box = DOM.create('div', {
    className: 'animated-box',
    style: {
        width: '100px',
        height: '100px',
        backgroundColor: 'red',
        display: 'none'
    }
});

document.body.appendChild(box);

Animation.fadeIn(box);
setTimeout(() => Animation.fadeOut(box), 2000);
```

## Best Practices

### Performance Best Practices
```javascript
// 1. Minimize DOM manipulations
function badPerformance() {
    const list = document.querySelector('.list');
    
    for (let i = 0; i < 1000; i++) {
        const item = document.createElement('li');
        item.textContent = `Item ${i}`;
        list.appendChild(item); // Triggers reflow each time
    }
}

function goodPerformance() {
    const list = document.querySelector('.list');
    const fragment = document.createDocumentFragment();
    
    for (let i = 0; i < 1000; i++) {
        const item = document.createElement('li');
        item.textContent = `Item ${i}`;
        fragment.appendChild(item); // No reflow
    }
    
    list.appendChild(fragment); // Single reflow
}

// 2. Use event delegation
function badEventDelegation() {
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', handleClick);
    });
}

function goodEventDelegation() {
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('btn')) {
            handleClick(event);
        }
    });
}

// 3. Cache DOM queries
function badCaching() {
    document.getElementById('my-element').style.color = 'red';
    document.getElementById('my-element').style.fontSize = '16px';
    document.getElementById('my-element').style.padding = '10px';
}

function goodCaching() {
    const element = document.getElementById('my-element');
    element.style.color = 'red';
    element.style.fontSize = '16px';
    element.style.padding = '10px';
}

// 4. Use requestAnimationFrame for animations
function badAnimation() {
    const element = document.getElementById('animated');
    let position = 0;
    
    setInterval(() => {
        position += 1;
        element.style.transform = `translateX(${position}px)`;
    }, 16); // Not synchronized with browser's refresh rate
}

function goodAnimation() {
    const element = document.getElementById('animated');
    let position = 0;
    
    function animate() {
        position += 1;
        element.style.transform = `translateX(${position}px)`;
        requestAnimationFrame(animate);
    }
    
    requestAnimationFrame(animate);
}
```

### Security Best Practices
```javascript
// 1. Sanitize user input
function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}

// 2. Use textContent instead of innerHTML when possible
function safeSetText(element, text) {
    element.textContent = text; // Safe
    // element.innerHTML = text; // Potentially dangerous
}

// 3. Validate and escape data
function escapeHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// 4. Use CSP headers and avoid eval()
function unsafeEval() {
    eval('alert("This is dangerous!")'); // Never use eval()
}

function safeAlternative() {
    // Use safer alternatives
    const data = JSON.parse('{"key": "value"}');
    console.log(data.key);
}
```

## Common Pitfalls

### Common DOM Mistakes
```javascript
// 1. Confusing live and static collections
const liveCollection = document.getElementsByClassName('item'); // Live
const staticCollection = document.querySelectorAll('.item'); // Static

// Adding/removing elements affects live collection but not static

// 2. Forgetting to prevent default behavior
document.querySelector('form').addEventListener('submit', function(event) {
    // event.preventDefault(); // Need this to prevent form submission
});

// 3. Memory leaks with event listeners
function addEventListeners() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('click', handleButtonClick);
        // Need to remove listeners when elements are removed
    });
}

function removeEventListeners() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.removeEventListener('click', handleButtonClick);
    });
}

// 4. Synchronous operations in event handlers
document.addEventListener('click', function() {
    // Long-running operation blocks UI
    for (let i = 0; i < 1000000; i++) {
        // Heavy computation
    }
});

// Better: Use Web Workers or break up work
document.addEventListener('click', async function() {
    // Use async/await or setTimeout
    await performAsyncOperation();
});

// 5. Not checking for element existence
function badElementAccess() {
    const element = document.getElementById('non-existent');
    element.textContent = 'Hello'; // Error!
}

function goodElementAccess() {
    const element = document.getElementById('non-existent');
    if (element) {
        element.textContent = 'Hello';
    }
}
```

## Summary

JavaScript DOM manipulation provides powerful capabilities:

**Element Selection:**
- `getElementById()`, `getElementsByClassName()`, `getElementsByTagName()`
- `querySelector()`, `querySelectorAll()` for CSS selectors
- Live vs static collections

**Element Creation & Modification:**
- `createElement()`, `createTextNode()`, `createDocumentFragment()`
- `innerHTML`, `textContent`, `setAttribute()`
- Class manipulation with `classList`

**DOM Traversal:**
- Parent/child relationships: `parentElement`, `children`
- Sibling relationships: `previousElementSibling`, `nextElementSibling`
- `closest()` for finding ancestors

**Event Handling:**
- `addEventListener()` for event binding
- Event propagation: bubbling and capturing
- Event delegation for performance

**Performance Optimization:**
- Document fragments for batch operations
- Event delegation for many elements
- `requestAnimationFrame` for animations
- DOM query caching

**Modern APIs:**
- Shadow DOM for encapsulation
- MutationObserver for change detection
- ResizeObserver for size monitoring
- IntersectionObserver for viewport detection

**Best Practices:**
- Minimize DOM manipulations
- Use event delegation
- Cache DOM queries
- Sanitize user input
- Handle errors gracefully

DOM manipulation is fundamental to creating interactive web applications, enabling dynamic content updates and user interactions.
