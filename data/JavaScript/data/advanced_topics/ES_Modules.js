/**
 * ES Modules in JavaScript
 * This tutorial covers importing and exporting modules in modern JavaScript.
 * Note: These examples use the ES Modules (.mjs or type: "module") syntax.
 */

// --- Exporting ---
// Named Export
export const PI = 3.14159;

export function add(a, b) {
    return a + b;
}

// Default Export
export default class MathHelper {
    static multiply(a, b) {
        return a * b;
    }
}

// --- Importing (Commented out to prevent execution errors in a single-file environment) ---
/*
import MathHelper, { PI, add } from './math.js';

console.log("PI:", PI);
console.log("Addition:", add(10, 20));
console.log("Multiplication:", MathHelper.multiply(10, 20));
*/

console.log("ES Modules allow you to organize code into reusable files.");
console.log("Use 'export' to share functions/classes and 'import' to use them.");