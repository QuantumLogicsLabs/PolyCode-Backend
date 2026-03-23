# ES Modules in JavaScript

## Overview
**ES Modules** (ECMAScript Modules) are the standard way to organize and share code between different JavaScript files. They allow you to define reusable pieces of code and only share what's necessary with other parts of your application.

## Key Module Types
1.  **Named Exports**:
    - **Exporting**: Use `export` to share specific variables, functions, or classes.
    - **Importing**: Use curly braces `{}` to import the specific members you need.
    - **Multiple Exports**: You can have multiple named exports in a single file.
2.  **Default Exports**:
    - **Exporting**: Use `export default` to share a single, primary member.
    - **Importing**: Use a name of your choice (no curly braces) to import the default export.
    - **Single Default Export**: Only one default export is allowed per file.
3.  **Import Aliasing**:
    - **Custom Names**: Use `as` to rename an imported member (e.g., `import { longName as short } from './file.js'`).
4.  **Re-exporting**:
    - **Aggregating**: Use `export { name } from './file.js'` to export members from another file without importing them into the current one.

## Basic Syntax
```js
// math.js
export const PI = 3.14;
export default function add(a, b) { return a + b; }

// app.js
import add, { PI } from './math.js';
console.log(add(1, 2)); // 3
console.log(PI); // 3.14
```

## Best Practices
- Use ES Modules instead of CommonJS (`require`) for modern web and Node.js projects.
- Use named exports for sharing multiple utilities and default exports for main classes or functions.

[02) ES Modules.js](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/JavaScript/data/10)%20Advanced%20Topics/02)%20ES%20Modules.js)