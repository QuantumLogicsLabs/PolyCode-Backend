# JavaScript DOM & Browser APIs

The Document Object Model (DOM) is the interface between JavaScript and the webpage. Mastering it lets you build fully interactive applications without any framework.

---

## 1. Selecting Elements

```js
// By ID — returns one element or null
const btn = document.getElementById('submit-btn');

// CSS selectors — querySelector returns first match
const heading = document.querySelector('h1');
const card    = document.querySelector('.card.active');

// CSS selectors — querySelectorAll returns NodeList
const allCards = document.querySelectorAll('.card');
const navLinks = document.querySelectorAll('nav a');

// Older methods (still common)
const items = document.getElementsByClassName('item');   // HTMLCollection
const divs  = document.getElementsByTagName('div');       // HTMLCollection
```

---

## 2. Reading & Modifying Content

```js
// Text content (safe — no HTML parsing)
element.textContent = 'New text';
console.log(element.textContent);

// HTML content (use carefully — XSS risk)
element.innerHTML = '<strong>Bold text</strong>';

// Attributes
element.setAttribute('data-id', '42');
element.getAttribute('href');
element.removeAttribute('disabled');
element.toggleAttribute('hidden');

// CSS classes
element.classList.add('active', 'visible');
element.classList.remove('loading');
element.classList.toggle('open');
element.classList.contains('selected'); // boolean

// Inline styles
element.style.color          = '#fff';
element.style.backgroundColor = '#333';
element.style.display        = 'none';
```

---

## 3. Creating & Inserting Elements

```js
// Create
const div = document.createElement('div');
div.className = 'card';
div.textContent = 'Hello!';

// Insert
parent.appendChild(div);
parent.prepend(div);                          // first child
parent.insertBefore(div, referenceElement);
parent.insertAdjacentHTML('beforeend', '<p>HTML string</p>');

// Remove
element.remove();
parent.removeChild(child);
```

---

## 4. Events

```js
// addEventListener (preferred)
button.addEventListener('click', (e) => {
    e.preventDefault();     // stop default action (form submit, link follow)
    e.stopPropagation();    // stop event bubbling up the DOM
    console.log('clicked!', e.target);
});

// Event delegation — handle many children with one listener
document.querySelector('#list').addEventListener('click', (e) => {
    if (e.target.matches('li')) {
        e.target.classList.toggle('done');
    }
});

// Common events
// click, dblclick, mouseenter, mouseleave, mousemove
// keydown, keyup, keypress
// submit, input, change, focus, blur
// scroll, resize, load, DOMContentLoaded
```

---

## 5. Fetch API & Async Data

```js
// GET request
async function fetchUsers() {
    try {
        const res  = await fetch('https://jsonplaceholder.typicode.com/users');
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        return data;
    } catch (err) {
        console.error('Fetch failed:', err.message);
    }
}

// POST request
async function createPost(title, body) {
    const res = await fetch('https://jsonplaceholder.typicode.com/posts', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ title, body, userId: 1 })
    });
    return res.json();
}
```

---

## 6. LocalStorage & SessionStorage

```js
// Store (always stringify objects)
localStorage.setItem('theme', 'dark');
localStorage.setItem('user', JSON.stringify({ name: 'Alice', role: 'admin' }));

// Read
const theme = localStorage.getItem('theme');
const user  = JSON.parse(localStorage.getItem('user') || 'null');

// Remove
localStorage.removeItem('theme');
localStorage.clear(); // clear everything

// sessionStorage — same API, but clears on tab close
sessionStorage.setItem('token', 'abc123');
```

---

## 7. Intersection Observer (Lazy Load / Scroll Animations)

```js
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target); // stop watching
        }
    });
}, { threshold: 0.2 }); // trigger when 20% visible

document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
```

---

## Summary

| Task | API |
|---|---|
| Find elements | `querySelector`, `getElementById` |
| Change text | `textContent`, `innerHTML` |
| Toggle classes | `classList.add/remove/toggle` |
| Listen to events | `addEventListener` |
| HTTP requests | `fetch` + `async/await` |
| Persist data | `localStorage` |
| Scroll animations | `IntersectionObserver` |

> 💡 Always prefer `textContent` over `innerHTML` when inserting user data — innerHTML runs HTML and can cause XSS vulnerabilities.
