/**
 * JavaScript Variables and Strings
 * This example demonstrates basic data types and string manipulation.
 */

// Variable Declaration
const name = "JavaScript Learner";
let age = 25;
var city = "New York"; // Legacy way

// String Interpolation (Template Literals)
const greeting = `Hello, ${name}! You are ${age} years old and live in ${city}.`;

// String Manipulation
const uppercaseName = name.toUpperCase();
const firstChar = name.charAt(0);
const slicedName = name.slice(0, 10);

console.log(greeting);
console.log("Uppercase Name:", uppercaseName);
console.log("First Character:", firstChar);
console.log("Sliced Name:", slicedName);

// Arithmetic with Variables
age = age + 5;
console.log("Next Age:", age);