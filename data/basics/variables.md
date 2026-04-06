# JavaScript Variables

## Variable Declaration

### var, let, and const
```javascript
// var - function scoped, can be redeclared and reassigned
var name = "John";
var name = "Jane"; // Can be redeclared
name = "Bob";     // Can be reassigned

// let - block scoped, cannot be redeclared, can be reassigned
let age = 25;
// let age = 30; // Error: Cannot redeclare
age = 30;        // Can be reassigned

// const - block scoped, cannot be redeclared or reassigned
const PI = 3.14159;
// const PI = 3.14; // Error: Cannot reassign
```

### Variable Naming Rules
```javascript
// Valid names
let firstName = "John";
let lastName = "Doe";
let age = 25;
let isStudent = true;
let _private = "private";
let $special = "special";

// Invalid names
// let 123name = "invalid"; // Cannot start with number
// let first-name = "invalid"; // Cannot contain hyphen
// let class = "invalid"; // Cannot use reserved keyword
```

## Data Types

### Primitive Types
```javascript
// String
let text = "Hello, World!";
let text2 = 'Single quotes';
let text3 = `Template literals with ${text}`;

// Number
let integer = 42;
let float = 3.14;
let scientific = 1.23e-4;
let infinity = Infinity;
let notANumber = NaN;

// Boolean
let isTrue = true;
let isFalse = false;

// Undefined
let undefinedVar;
console.log(undefinedVar); // undefined

// Null
let nullVar = null;
console.log(nullVar); // null

// Symbol (ES6)
let symbol = Symbol('description');

// BigInt (ES2020)
let bigNumber = 9007199254740991n;
```

### Reference Types
```javascript
// Object
let person = {
    name: "John",
    age: 30,
    city: "New York"
};

// Array
let numbers = [1, 2, 3, 4, 5];
let mixed = [1, "hello", true, null];

// Function
function greet(name) {
    return "Hello, " + name + "!";
}

// Date
let today = new Date();

// RegExp
let pattern = /ab+c/;
```

## Type Checking and Conversion

### Type Checking
```javascript
// typeof operator
console.log(typeof "hello");        // "string"
console.log(typeof 42);             // "number"
console.log(typeof true);           // "boolean"
console.log(typeof undefined);      // "undefined"
console.log(typeof null);           // "object" (known issue)
console.log(typeof {});             // "object"
console.log(typeof []);             // "object"
console.log(typeof function() {});  // "function"

// Array.isArray()
console.log(Array.isArray([]));      // true
console.log(Array.isArray({}));     // false
```

### Type Conversion
```javascript
// String conversion
let num = 42;
let str = String(num);      // "42"
let str2 = num.toString();   // "42"
let str3 = "" + num;        // "42"

// Number conversion
let strNum = "123";
let num1 = Number(strNum);   // 123
let num2 = parseInt(strNum);  // 123
let num3 = parseFloat("3.14"); // 3.14

// Boolean conversion
console.log(Boolean(1));      // true
console.log(Boolean(0));      // false
console.log(Boolean(""));     // false
console.log(Boolean("hello")); // true

// Implicit conversion
console.log(5 + "5"); // "55" (string concatenation)
console.log(5 - "5"); // 0 (numeric subtraction)
```

## Variable Scope

### Global Scope
```javascript
// Global variable (can be accessed anywhere)
var globalVar = "I am global";

function showGlobal() {
    console.log(globalVar); // "I am global"
}

showGlobal();
console.log(globalVar); // "I am global"
```

### Function Scope
```javascript
function testScope() {
    // Function-scoped with var
    var functionVar = "I am function scoped";
    
    if (true) {
        var functionVar2 = "Still function scoped";
    }
    
    console.log(functionVar2); // "Still function scoped"
}

testScope();
// console.log(functionVar); // Error: functionVar is not defined
```

### Block Scope
```javascript
function testBlockScope() {
    // Block-scoped with let and const
    if (true) {
        let blockVar = "I am block scoped";
        const blockConst = "I am also block scoped";
        console.log(blockVar); // "I am block scoped"
    }
    
    // console.log(blockVar); // Error: blockVar is not defined
}

testBlockScope();
```

## Hoisting

### var Hoisting
```javascript
console.log(hoistedVar); // undefined (not error)
var hoistedVar = "I am hoisted";

// This is equivalent to:
var hoistedVar;
console.log(hoistedVar); // undefined
hoistedVar = "I am hoisted";
```

### let and const Hoisting
```javascript
// console.log(hoistedLet); // Error: Cannot access before initialization
let hoistedLet = "I am not hoisted in the same way";

// console.log(hoistedConst); // Error: Cannot access before initialization
const hoistedConst = "I am also not hoisted";
```

## Destructuring

### Object Destructuring
```javascript
const person = {
    name: "John",
    age: 30,
    city: "New York"
};

// Basic destructuring
const { name, age } = person;
console.log(name); // "John"
console.log(age);  // 30

// With default values
const { name: personName, country = "USA" } = person;
console.log(personName); // "John"
console.log(country);   // "USA"

// Nested destructuring
const user = {
    profile: {
        firstName: "John",
        lastName: "Doe"
    }
};

const { profile: { firstName, lastName } } = user;
console.log(firstName); // "John"
console.log(lastName);  // "Doe"
```

### Array Destructuring
```javascript
const numbers = [1, 2, 3, 4, 5];

// Basic destructuring
const [first, second] = numbers;
console.log(first);  // 1
console.log(second); // 2

// Skip elements
const [first, , third] = numbers;
console.log(first); // 1
console.log(third); // 3

// Rest operator
const [first, ...rest] = numbers;
console.log(first); // 1
console.log(rest);  // [2, 3, 4, 5]

// Default values
const [a, b, c = 3] = [1, 2];
console.log(a); // 1
console.log(b); // 2
console.log(c); // 3
```

## Template Literals

### String Interpolation
```javascript
const name = "John";
const age = 30;

// Template literals
const message = `Hello, my name is ${name} and I am ${age} years old.`;
console.log(message);

// Multi-line strings
const multiLine = `
This is a
multi-line
string
`;
console.log(multiLine);

// Expressions in template literals
const result = `The sum of 5 and 3 is ${5 + 3}`;
console.log(result); // "The sum of 5 and 3 is 8"
```

## Constants and Immutability

### const with Primitive Types
```javascript
const PI = 3.14159;
// PI = 3.14; // Error: Cannot reassign constant

const person = {
    name: "John",
    age: 30
};

// Can modify properties of const objects
person.age = 31;
console.log(person.age); // 31

// Cannot reassign the entire object
// person = {}; // Error: Cannot reassign constant
```

### Object.freeze()
```javascript
const config = {
    apiUrl: "https://api.example.com",
    timeout: 5000
};

Object.freeze(config);

// Cannot modify frozen object
config.timeout = 10000; // Fails silently in non-strict mode
console.log(config.timeout); // 5000

// Check if object is frozen
console.log(Object.isFrozen(config)); // true
```

## Best Practices

### Variable Declaration Best Practices
```javascript
// Use const by default
const API_URL = "https://api.example.com";
const MAX_ATTEMPTS = 3;

// Use let when you need to reassign
let attemptCount = 0;
attemptCount = 1;

// Avoid var in modern JavaScript
// var oldStyle = "avoid this";

// Use descriptive names
let userAge = 25;        // Good
let a = 25;             // Bad - not descriptive
let isUserLoggedIn = true; // Good
let flag = true;          // Bad - not descriptive

// Use camelCase for variables
let firstName = "John";
let lastName = "Doe";
let currentBalance = 1000.50;

// Use UPPER_SNAKE_CASE for constants
const API_ENDPOINT = "https://api.example.com";
const MAX_FILE_SIZE = 1048576;

// Initialize variables
let counter = 0;          // Good
let total;               // Bad - undefined
let userName = "";       // Good - empty string
let isActive = false;    // Good - default boolean
```

### Type Coercion Best Practices
```javascript
// Avoid implicit type coercion
// Bad
if (value) { /* ... */ }

// Good
if (value !== null && value !== undefined) { /* ... */ }

// Use strict equality
// Bad
if (value == null) { /* ... */ }

// Good
if (value === null) { /* ... */ }

// Explicit type conversion
const userInput = "123";
const number = Number(userInput);
const isValidNumber = !isNaN(number);

// Use type checking
if (typeof value === 'string') {
    // Handle string
}
```

## Common Pitfalls

### Common Variable Mistakes
```javascript
// 1. Using var instead of let/const
function example1() {
    if (true) {
        var x = 10;
    }
    console.log(x); // 10 (accessible outside block)
}

// 2. Not initializing variables
function example2() {
    let y;
    console.log(y); // undefined
}

// 3. Confusing undefined and null
function example3() {
    let a = undefined;
    let b = null;
    console.log(a === b); // false
    console.log(a == b);  // true (type coercion)
}

// 4. Modifying const objects
function example4() {
    const obj = { x: 1 };
    obj.x = 2; // This works
    // obj = {}; // This fails
}

// 5. Global variable pollution
function example5() {
    globalVar = "I'm global!"; // Creates global variable without var/let/const
}
```

## Advanced Concepts

### Variable Shadowing
```javascript
let x = "global";

function shadowExample() {
    let x = "local";
    console.log(x); // "local"
    
    if (true) {
        let x = "block";
        console.log(x); // "block"
    }
    
    console.log(x); // "local"
}

shadowExample();
console.log(x); // "global"
```

### Temporal Dead Zone
```javascript
// let and const have a temporal dead zone
function temporalDeadZoneExample() {
    // console.log(myVar); // ReferenceError
    let myVar = "value";
    console.log(myVar); // "value"
}

temporalDeadZoneExample();
```

### Property Shorthand
```javascript
const name = "John";
const age = 30;

// Property shorthand in object literals
const person = {
    name,      // equivalent to name: name
    age,       // equivalent to age: age
    greet() {  // method shorthand
        return `Hello, I'm ${this.name}`;
    }
};

console.log(person.greet()); // "Hello, I'm John"
```

## Summary

JavaScript variables provide flexible data storage:

**Declaration Keywords:**
- `var` - Function-scoped, can be redeclared/reassigned
- `let` - Block-scoped, cannot be redeclared, can be reassigned
- `const` - Block-scoped, cannot be redeclared/reassigned

**Data Types:**
- Primitive: String, Number, Boolean, Undefined, Null, Symbol, BigInt
- Reference: Object, Array, Function, Date, RegExp

**Best Practices:**
- Use `const` by default, `let` when reassignment is needed
- Avoid `var` in modern code
- Use descriptive, camelCase names
- Initialize variables explicitly
- Use strict equality (`===`) over loose equality (`==`)

**Advanced Features:**
- Destructuring for clean variable assignment
- Template literals for string interpolation
- Hoisting behavior differences
- Temporal dead zone for `let`/`const`

Understanding these concepts helps write clean, maintainable, and bug-free JavaScript code.
