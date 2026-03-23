// ============================================================
//  JavaScript Testing — Patterns & Examples
//  Run: jest Testing.test.js   (or rename to .test.js)
// ============================================================

// ─────────────────────────────────────────────
// Code Under Test (would normally be separate files)
// ─────────────────────────────────────────────

// --- math.js ---
function add(a, b) { return a + b; }
function subtract(a, b) { return a - b; }
function multiply(a, b) { return a * b; }
function divide(a, b) {
    if (b === 0) throw new Error('Division by zero');
    return a / b;
}

// --- stringUtils.js ---
function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

function truncate(str, maxLength, suffix = '...') {
    if (str.length <= maxLength) return str;
    return str.slice(0, maxLength - suffix.length) + suffix;
}

function countWords(str) {
    return str.trim().split(/\s+/).filter(Boolean).length;
}

// --- validator.js ---
function isEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isStrongPassword(password) {
    return password.length >= 8 &&
           /[A-Z]/.test(password) &&
           /[a-z]/.test(password) &&
           /\d/.test(password);
}

// --- cart.js ---
class ShoppingCart {
    #items = [];

    add(product, quantity = 1) {
        const existing = this.#items.find(i => i.id === product.id);
        if (existing) existing.quantity += quantity;
        else this.#items.push({ ...product, quantity });
        return this;
    }

    remove(productId) {
        this.#items = this.#items.filter(i => i.id !== productId);
        return this;
    }

    getTotal() {
        return this.#items.reduce((sum, i) => sum + i.price * i.quantity, 0);
    }

    getCount() {
        return this.#items.reduce((sum, i) => sum + i.quantity, 0);
    }

    getItems() { return [...this.#items]; }

    clear() { this.#items = []; return this; }
}

// --- async service (simulated) ---
async function fetchUserById(id) {
    if (typeof id !== 'number' || id < 1) throw new Error('Invalid ID');
    // Simulated async DB call
    const users = { 1: { id: 1, name: 'Alice', email: 'alice@example.com' },
                    2: { id: 2, name: 'Bob',   email: 'bob@example.com' } };
    const user = users[id];
    if (!user) throw new Error('User not found');
    return user;
}

// ─────────────────────────────────────────────
// Mini Test Runner (no framework needed for demo)
// ─────────────────────────────────────────────

let passed = 0, failed = 0;

function test(description, fn) {
    try {
        fn();
        console.log(`  ✅ ${description}`);
        passed++;
    } catch (err) {
        console.log(`  ❌ ${description}`);
        console.log(`     → ${err.message}`);
        failed++;
    }
}

function expect(actual) {
    return {
        toBe:           (expected) => { if (actual !== expected) throw new Error(`Expected ${JSON.stringify(expected)}, got ${JSON.stringify(actual)}`); },
        toEqual:        (expected) => { if (JSON.stringify(actual) !== JSON.stringify(expected)) throw new Error(`Expected ${JSON.stringify(expected)}, got ${JSON.stringify(actual)}`); },
        toBeTruthy:     ()         => { if (!actual) throw new Error(`Expected truthy, got ${actual}`); },
        toBeFalsy:      ()         => { if (actual)  throw new Error(`Expected falsy, got ${actual}`); },
        toContain:      (item)     => { if (!actual.includes(item)) throw new Error(`Expected ${JSON.stringify(actual)} to contain ${JSON.stringify(item)}`); },
        toHaveLength:   (len)      => { if (actual.length !== len) throw new Error(`Expected length ${len}, got ${actual.length}`); },
        toBeGreaterThan:(n)        => { if (actual <= n) throw new Error(`Expected ${actual} > ${n}`); },
        toThrow:        (msg)      => { try { actual(); throw new Error('NO_THROW'); } catch(e) { if (e.message === 'NO_THROW') throw new Error('Expected function to throw'); if (msg && !e.message.includes(msg)) throw new Error(`Expected error "${msg}", got "${e.message}"`); } },
        not: {
            toBe:       (expected) => { if (actual === expected) throw new Error(`Expected NOT ${JSON.stringify(expected)}`); },
            toBeTruthy: ()         => { if (actual) throw new Error(`Expected falsy`); },
        }
    };
}

// ─────────────────────────────────────────────
// TESTS
// ─────────────────────────────────────────────

console.log('\n===== JavaScript Tests =====\n');

console.log('--- Math Functions ---');
test('add(2, 3) returns 5',           () => expect(add(2, 3)).toBe(5));
test('add(-1, 1) returns 0',          () => expect(add(-1, 1)).toBe(0));
test('subtract(10, 4) returns 6',     () => expect(subtract(10, 4)).toBe(6));
test('multiply(-2, 5) returns -10',   () => expect(multiply(-2, 5)).toBe(-10));
test('divide(10, 2) returns 5',       () => expect(divide(10, 2)).toBe(5));
test('divide by zero throws',         () => expect(() => divide(5, 0)).toThrow('Division by zero'));

console.log('\n--- String Utilities ---');
test('capitalize("hello") → "Hello"',     () => expect(capitalize('hello')).toBe('Hello'));
test('capitalize("WORLD") → "World"',     () => expect(capitalize('WORLD')).toBe('World'));
test('capitalize("") → ""',               () => expect(capitalize('')).toBe(''));
test('truncate short string unchanged',   () => expect(truncate('Hello', 10)).toBe('Hello'));
test('truncate long string with ...',     () => expect(truncate('Hello World', 8)).toBe('Hello...'));
test('countWords("hello world") → 2',     () => expect(countWords('hello world')).toBe(2));
test('countWords with extra spaces → 3',  () => expect(countWords('  a  b   c  ')).toBe(3));

console.log('\n--- Validator ---');
test('valid email passes',            () => expect(isEmail('user@example.com')).toBeTruthy());
test('invalid email (no @) fails',    () => expect(isEmail('userexample.com')).toBeFalsy());
test('invalid email (no dot) fails',  () => expect(isEmail('user@example')).toBeFalsy());
test('strong password passes',        () => expect(isStrongPassword('SecureP4ss')).toBeTruthy());
test('short password fails',          () => expect(isStrongPassword('abc1')).toBeFalsy());
test('no uppercase fails',            () => expect(isStrongPassword('password1')).toBeFalsy());

console.log('\n--- Shopping Cart ---');
const laptop  = { id: 1, name: 'Laptop', price: 999 };
const mouse   = { id: 2, name: 'Mouse',  price: 29  };
const monitor = { id: 3, name: 'Monitor',price: 299 };

test('cart starts empty',             () => { const c = new ShoppingCart(); expect(c.getCount()).toBe(0); });
test('add item increases count',      () => { const c = new ShoppingCart(); c.add(laptop); expect(c.getCount()).toBe(1); });
test('add same item accumulates qty', () => { const c = new ShoppingCart(); c.add(laptop); c.add(laptop, 2); expect(c.getCount()).toBe(3); });
test('getTotal calculates correctly', () => { const c = new ShoppingCart(); c.add(laptop).add(mouse); expect(c.getTotal()).toBe(1028); });
test('remove item',                   () => { const c = new ShoppingCart(); c.add(laptop).add(mouse).remove(1); expect(c.getCount()).toBe(1); });
test('clear empties cart',            () => { const c = new ShoppingCart(); c.add(laptop).add(mouse).clear(); expect(c.getCount()).toBe(0); });

console.log('\n--- Async (with Promises) ---');
const asyncTests = [
    fetchUserById(1).then(u => { test('fetchUser(1) returns Alice', () => expect(u.name).toBe('Alice')); }),
    fetchUserById(2).then(u => { test('fetchUser(2) returns Bob',   () => expect(u.name).toBe('Bob'));   }),
    fetchUserById(-1).catch(e  => { test('fetchUser(-1) throws',    () => expect(e.message).toBe('Invalid ID')); }),
];

Promise.all(asyncTests).then(() => {
    console.log(`\n${'─'.repeat(40)}`);
    console.log(`Results: ${passed} passed, ${failed} failed`);
    if (failed === 0) console.log('✅ All tests passed!');
    else console.log(`❌ ${failed} test(s) failed`);
});
