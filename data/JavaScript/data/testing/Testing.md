# JavaScript Testing

Writing tests is what separates professional code from scripts. This guide covers unit testing with Jest, integration patterns, and best practices.

---

## Why Test?

- **Catch bugs early** — before they reach production
- **Refactor confidently** — tests tell you when you broke something
- **Document behavior** — tests show how code is meant to be used
- **Reduce manual QA** — automate what you used to click through

---

## 1. Jest Basics

Install: `npm install --save-dev jest`

### Test File Structure
```js
// math.test.js
import { add, subtract, multiply } from './math.js';

describe('Math Functions', () => {
    test('adds two numbers', () => {
        expect(add(2, 3)).toBe(5);
    });

    test('subtracts correctly', () => {
        expect(subtract(10, 4)).toBe(6);
    });

    it('multiplies with negative numbers', () => {
        expect(multiply(-2, 5)).toBe(-10);
    });
});
```

### Common Matchers
```js
expect(value).toBe(42);               // strict equality (===)
expect(value).toEqual({ a: 1 });      // deep equality (objects/arrays)
expect(value).toBeTruthy();           // truthy value
expect(value).toBeFalsy();            // falsy value
expect(value).toBeNull();
expect(value).toBeUndefined();
expect(value).toBeGreaterThan(5);
expect(value).toBeLessThanOrEqual(10);
expect(str).toContain('hello');
expect(arr).toHaveLength(3);
expect(fn).toThrow('error message');
expect(fn).toThrow(TypeError);
```

---

## 2. Async Testing

```js
// async/await
test('fetches user data', async () => {
    const user = await fetchUser(1);
    expect(user.id).toBe(1);
    expect(user.name).toBeDefined();
});

// Testing errors
test('throws on invalid ID', async () => {
    await expect(fetchUser(-1)).rejects.toThrow('Invalid ID');
});
```

---

## 3. Mocking

### Mock Functions
```js
const mockFn = jest.fn();
mockFn('hello');
mockFn.mockReturnValue(42);

expect(mockFn).toHaveBeenCalledTimes(1);
expect(mockFn).toHaveBeenCalledWith('hello');
expect(mockFn()).toBe(42);
```

### Mock Modules
```js
// __mocks__/api.js  or inline:
jest.mock('./api', () => ({
    getUser: jest.fn().mockResolvedValue({ id: 1, name: 'Alice' }),
    saveUser: jest.fn().mockResolvedValue({ success: true }),
}));
```

---

## 4. Test-Driven Development (TDD)

1. **Red** — Write a failing test
2. **Green** — Write minimum code to pass
3. **Refactor** — Clean up, tests still pass

```js
// Step 1: Write the test FIRST
test('formats currency', () => {
    expect(formatCurrency(1234.5, 'USD')).toBe('$1,234.50');
    expect(formatCurrency(0, 'EUR')).toBe('€0.00');
});

// Step 2: Write the function
function formatCurrency(amount, currency) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency', currency
    }).format(amount);
}
```

---

## 5. Coverage

```bash
jest --coverage
```

| Metric | Meaning |
|---|---|
| **Statements** | % of code lines executed |
| **Branches** | % of if/else paths taken |
| **Functions** | % of functions called |
| **Lines** | % of logical lines covered |

> 💡 Aim for 80%+ coverage on business logic. Don't obsess over 100% — some code is trivial or infrastructure.

---

## Summary

| Concept | Tool/Pattern |
|---|---|
| Unit test | `describe/test/expect` |
| Async test | `async/await` + `resolves/rejects` |
| Mock functions | `jest.fn()` |
| Mock modules | `jest.mock('./module')` |
| TDD | Red → Green → Refactor |
| Coverage | `jest --coverage` |
