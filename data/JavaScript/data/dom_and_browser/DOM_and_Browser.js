// ============================================================
//  JavaScript DOM & Browser APIs — Complete Examples
//  Note: DOM examples show the patterns used in real pages.
//        Run in a browser console or with jsdom in Node.js.
// ============================================================

// ─────────────────────────────────────────────
// SECTION 1: DOM Manipulation Utilities
// ─────────────────────────────────────────────

/**
 * $ — shorthand querySelector
 */
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

/**
 * createElement with options
 */
function createElement(tag, { className, text, html, attrs, children } = {}) {
    const el = document.createElement(tag);
    if (className) el.className = className;
    if (text)      el.textContent = text;
    if (html)      el.innerHTML = html;
    if (attrs)     Object.entries(attrs).forEach(([k, v]) => el.setAttribute(k, v));
    if (children)  children.forEach(c => el.appendChild(c));
    return el;
}

// ─────────────────────────────────────────────
// SECTION 2: Todo App (DOM-driven)
// ─────────────────────────────────────────────

class TodoApp {
    constructor(containerId) {
        this.todos   = JSON.parse(localStorage.getItem('todos') || '[]');
        this.nextId  = this.todos.length ? Math.max(...this.todos.map(t => t.id)) + 1 : 1;
        this.container = document.getElementById(containerId);
        if (this.container) this.render();
    }

    add(text) {
        if (!text.trim()) return;
        this.todos.push({ id: this.nextId++, text: text.trim(), done: false });
        this.save();
        this.render();
    }

    toggle(id) {
        const todo = this.todos.find(t => t.id === id);
        if (todo) { todo.done = !todo.done; this.save(); this.render(); }
    }

    delete(id) {
        this.todos = this.todos.filter(t => t.id !== id);
        this.save();
        this.render();
    }

    save() { localStorage.setItem('todos', JSON.stringify(this.todos)); }

    render() {
        if (!this.container) return;
        this.container.innerHTML = `
            <div class="todo-app">
                <h2>My Tasks (${this.todos.filter(t => !t.done).length} remaining)</h2>
                <form id="todo-form">
                    <input id="todo-input" type="text" placeholder="Add a task..." autocomplete="off"/>
                    <button type="submit">Add</button>
                </form>
                <ul id="todo-list">
                    ${this.todos.map(t => `
                        <li class="${t.done ? 'done' : ''}" data-id="${t.id}">
                            <input type="checkbox" ${t.done ? 'checked' : ''}/>
                            <span>${t.text}</span>
                            <button class="delete-btn">✕</button>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;

        // Attach events using delegation
        this.container.querySelector('#todo-form').addEventListener('submit', e => {
            e.preventDefault();
            const input = this.container.querySelector('#todo-input');
            this.add(input.value);
            input.value = '';
        });

        this.container.querySelector('#todo-list').addEventListener('click', e => {
            const li = e.target.closest('li');
            if (!li) return;
            const id = parseInt(li.dataset.id);
            if (e.target.matches('.delete-btn'))  this.delete(id);
            if (e.target.matches('input[type=checkbox]')) this.toggle(id);
        });
    }
}

// ─────────────────────────────────────────────
// SECTION 3: Fetch Utilities
// ─────────────────────────────────────────────

async function apiFetch(url, options = {}) {
    const defaults = {
        headers: { 'Content-Type': 'application/json' },
        ...options
    };
    if (defaults.body && typeof defaults.body === 'object')
        defaults.body = JSON.stringify(defaults.body);

    const res = await fetch(url, defaults);
    if (!res.ok) throw new Error(`API Error ${res.status}: ${res.statusText}`);
    return res.json();
}

const api = {
    get:    (url)         => apiFetch(url),
    post:   (url, data)   => apiFetch(url, { method: 'POST',   body: data }),
    put:    (url, data)   => apiFetch(url, { method: 'PUT',    body: data }),
    delete: (url)         => apiFetch(url, { method: 'DELETE' }),
};

// Demo: fetch and render user cards
async function renderUserCards(containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
        // Node.js demo fallback
        console.log('[Demo] Would render user cards from API');
        return;
    }
    container.innerHTML = '<p>Loading...</p>';
    try {
        const users = await api.get('https://jsonplaceholder.typicode.com/users');
        container.innerHTML = users.map(u => `
            <div class="user-card">
                <h3>${u.name}</h3>
                <p>📧 ${u.email}</p>
                <p>🌐 ${u.website}</p>
                <p>🏢 ${u.company.name}</p>
            </div>
        `).join('');
    } catch (err) {
        container.innerHTML = `<p class="error">Failed: ${err.message}</p>`;
    }
}

// ─────────────────────────────────────────────
// SECTION 4: Event System (Custom EventEmitter)
// ─────────────────────────────────────────────

class EventEmitter {
    #listeners = new Map();

    on(event, fn) {
        if (!this.#listeners.has(event)) this.#listeners.set(event, []);
        this.#listeners.get(event).push(fn);
        return this;
    }

    once(event, fn) {
        const wrapper = (...args) => { fn(...args); this.off(event, wrapper); };
        return this.on(event, wrapper);
    }

    off(event, fn) {
        const fns = this.#listeners.get(event);
        if (fns) this.#listeners.set(event, fns.filter(f => f !== fn));
        return this;
    }

    emit(event, ...args) {
        (this.#listeners.get(event) || []).forEach(fn => fn(...args));
        return this;
    }
}

// ─────────────────────────────────────────────
// SECTION 5: LocalStorage Store
// ─────────────────────────────────────────────

class Store {
    constructor(key, defaultValue = {}) {
        this.key = key;
        this.defaultValue = defaultValue;
    }
    get()          { return JSON.parse(localStorage.getItem(this.key) || JSON.stringify(this.defaultValue)); }
    set(value)     { localStorage.setItem(this.key, JSON.stringify(value)); return this; }
    update(fn)     { this.set(fn(this.get())); return this; }
    clear()        { localStorage.removeItem(this.key); return this; }
}

// ─────────────────────────────────────────────
// Node.js / Non-DOM Demo
// ─────────────────────────────────────────────

if (typeof window === 'undefined') {
    console.log('===== DOM & Browser APIs Demo (Non-DOM Mode) =====\n');

    // EventEmitter demo
    console.log('--- Custom EventEmitter ---');
    const emitter = new EventEmitter();
    emitter.on('login',  user => console.log(`  User logged in: ${user.name}`));
    emitter.on('login',  user => console.log(`  Sending welcome email to: ${user.email}`));
    emitter.once('first-visit', () => console.log('  First visit tour started!'));

    emitter.emit('login', { name: 'Alice', email: 'alice@example.com' });
    emitter.emit('first-visit');
    emitter.emit('first-visit'); // won't fire again

    console.log('\n--- Utility Functions (createElement pattern) ---');
    console.log('createElement("div", { className:"card", text:"Hello" }) →', '{ tag: "div", class: "card", text: "Hello" }');

    console.log('\n--- fetch API usage pattern ---');
    console.log('api.get(url)       → GET  request');
    console.log('api.post(url,data) → POST request with JSON body');
    console.log('api.put(url,data)  → PUT  request');
    console.log('api.delete(url)    → DELETE request');

    console.log('\n✅ DOM patterns demonstrated (run in browser for full DOM functionality)');
}
