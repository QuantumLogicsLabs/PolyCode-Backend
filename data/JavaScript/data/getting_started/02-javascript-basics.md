# Module 2: JavaScript Basics

## 🎯 Learning Objectives

By the end of this module, you will:
- Master JavaScript variables and data types
- Understand type conversion and coercion
- Learn about operators and expressions
- Practice working with strings and numbers
- Understand JavaScript's dynamic typing system

## 📚 Topics Covered

### 1. Variables and Declarations
- `var`, `let`, and `const` differences
- Variable naming conventions
- Scope and hoisting
- Constants and immutable values

### 2. Data Types
- Primitive types (string, number, boolean, null, undefined, symbol, bigint)
- Reference types (object, array, function)
- Type checking with `typeof`
- Type conversion and coercion

### 3. Operators
- Arithmetic operators
- Assignment operators
- Comparison operators
- Logical operators
- Unary and ternary operators

### 4. Strings and Numbers
- String methods and properties
- Number methods and properties
- Template literals
- Math operations

## 💻 Practical Examples

### Example 1: Variables and Data Types

```javascript
// Variable declarations
var oldWay = "Using var"; // Function-scoped, can be redeclared
let modernWay = "Using let"; // Block-scoped, cannot be redeclared
const constant = "Cannot be reassigned"; // Block-scoped, immutable

// Primitive types
let str = "Hello, JavaScript!";
let num = 42;
let bool = true;
let nothing = null;
let notDefined;
let sym = Symbol("unique");
let bigInt = 9007199254740991n;

// Reference types
let obj = { name: "John", age: 30 };
let arr = [1, 2, 3, 4, 5];
let func = function() { return "Hello"; };

// Type checking
console.log(typeof str);        // "string"
console.log(typeof num);        // "number"
console.log(typeof bool);       // "boolean"
console.log(typeof nothing);    // "object" (this is a known JavaScript quirk)
console.log(typeof notDefined); // "undefined"
console.log(typeof sym);        // "symbol"
console.log(typeof bigInt);     // "bigint"
console.log(typeof obj);        // "object"
console.log(typeof arr);        // "object"
console.log(typeof func);       // "function"
```

### Example 2: Type Conversion and Coercion

```javascript
// Explicit type conversion
let strNum = "123";
let num = Number(strNum);       // 123
let str = String(num);          // "123"
let bool = Boolean("hello");    // true

// Type coercion (automatic conversion)
console.log("5" + 3);          // "53" (string concatenation)
console.log("5" - 3);          // 2 (numeric subtraction)
console.log("5" * 3);          // 15 (numeric multiplication)
console.log("5" / 3);          // 1.666... (numeric division)

// Truthy and falsy values
if ("") console.log("Empty string is falsy");
if (0) console.log("Zero is falsy");
if (null) console.log("Null is falsy");
if (undefined) console.log("Undefined is falsy");
if (NaN) console.log("NaN is falsy");
if (false) console.log("False is falsy");

if ("hello") console.log("Non-empty string is truthy");
if (42) console.log("Non-zero number is truthy");
if (true) console.log("True is truthy");
if ({}) console.log("Object is truthy");
if ([]) console.log("Array is truthy");
```

### Example 3: Operators in Action

```javascript
// Arithmetic operators
let a = 10, b = 3;
console.log(a + b);    // 13 (addition)
console.log(a - b);    // 7 (subtraction)
console.log(a * b);    // 30 (multiplication)
console.log(a / b);    // 3.333... (division)
console.log(a % b);    // 1 (modulo/remainder)
console.log(a ** b);   // 1000 (exponentiation)

// Assignment operators
let x = 5;
x += 3;  // x = x + 3 = 8
x -= 2;  // x = x - 2 = 6
x *= 4;  // x = x * 4 = 24
x /= 3;  // x = x / 3 = 8
x %= 5;  // x = x % 5 = 3

// Comparison operators
console.log(5 == "5");   // true (loose equality)
console.log(5 === "5");  // false (strict equality)
console.log(5 != "5");   // false (loose inequality)
console.log(5 !== "5");  // true (strict inequality)
console.log(10 > 5);     // true
console.log(10 >= 10);   // true
console.log(5 < 10);     // true
console.log(10 <= 5);    // false

// Logical operators
console.log(true && false);  // false (logical AND)
console.log(true || false);  // true (logical OR)
console.log(!true);          // false (logical NOT)

// Short-circuit evaluation
let name = null;
let displayName = name || "Guest"; // "Guest"
let isLoggedIn = true;
isLoggedIn && console.log("Welcome!"); // Logs "Welcome!"
```

### Example 4: String Manipulation

```javascript
// String properties and methods
let text = "Hello, World!";

console.log(text.length);        // 13
console.log(text.toUpperCase()); // "HELLO, WORLD!"
console.log(text.toLowerCase()); // "hello, world!"
console.log(text.includes("World")); // true
console.log(text.startsWith("Hello")); // true
console.log(text.endsWith("!")); // true
console.log(text.slice(0, 5)); // "Hello"
console.log(text.substring(7, 12)); // "World"
console.log(text.replace("World", "JavaScript")); // "Hello, JavaScript!"
console.log(text.split(", ")); // ["Hello", "World!"]
console.log(text.trim()); // "Hello, World!" (removes whitespace)

// Template literals
let firstName = "John";
let lastName = "Doe";
let age = 30;

// String concatenation (old way)
let oldWay = "My name is " + firstName + " " + lastName + " and I am " + age + " years old.";

// Template literals (modern way)
let modernWay = `My name is ${firstName} ${lastName} and I am ${age} years old.`;

// Multi-line strings
let multiLine = `
This is a
multi-line
string using
template literals.
`;

console.log(modernWay);
console.log(multiLine);
```

### Example 5: Number Operations

```javascript
// Number properties and methods
let num = 123.456;

console.log(num.toFixed(2));    // "123.46"
console.log(num.toPrecision(4)); // "123.5"
console.log(num.toString());     // "123.456"
console.log(num.valueOf());      // 123.456

// Math object
console.log(Math.PI);            // 3.141592653589793
console.log(Math.E);             // 2.718281828459045
console.log(Math.sqrt(16));      // 4
console.log(Math.pow(2, 3));     // 8
console.log(Math.abs(-5));       // 5
console.log(Math.round(4.7));    // 5
console.log(Math.floor(4.7));    // 4
console.log(Math.ceil(4.2));     // 5
console.log(Math.random());      // Random number between 0 and 1
console.log(Math.max(1, 2, 3));  // 3
console.log(Math.min(1, 2, 3));  // 1

// Special number values
console.log(Number.MAX_VALUE);   // Largest number
console.log(Number.MIN_VALUE);   // Smallest positive number
console.log(Number.POSITIVE_INFINITY); // Infinity
console.log(Number.NEGATIVE_INFINITY); // -Infinity
console.log(Number.NaN);         // NaN (Not a Number)

// Checking for special values
console.log(isNaN(NaN));         // true
console.log(isFinite(42));       // true
console.log(isFinite(Infinity)); // false
```

## 🎮 Interactive Exercises

### Exercise 1: Variable Practice
Create variables for:
- Your name (string)
- Your age (number)
- Your height in centimeters (number)
- Whether you're a student (boolean)
- Your favorite color (string)

Then display them using template literals.

```javascript
// Your solution here
```

### Exercise 2: Type Conversion
Convert between different data types:
- Convert string "123" to number
- Convert number 456 to string
- Convert 0 to boolean
- Convert "hello" to boolean

```javascript
// Your solution here
```

### Exercise 3: Calculator Functions
Create functions for:
- Addition
- Subtraction
- Multiplication
- Division
- Modulo operation

```javascript
// Your solution here
```

### Exercise 4: String Manipulation
Given the string "JavaScript is awesome!":
- Get the length
- Convert to uppercase
- Check if it contains "awesome"
- Replace "awesome" with "powerful"
- Split into words

```javascript
// Your solution here
```

## 🌐 Real-World Applications

### 1. User Input Validation

```javascript
// Form validation function
function validateEmail(email) {
    // Check if email contains @ and .
    const hasAtSymbol = email.includes('@');
    const hasDot = email.includes('.');
    const hasValidFormat = email.indexOf('@') < email.lastIndexOf('.');
    
    return hasAtSymbol && hasDot && hasValidFormat;
}

function validatePassword(password) {
    const hasMinLength = password.length >= 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*]/.test(password);
    
    return hasMinLength && hasUpperCase && hasLowerCase && hasNumber && hasSpecialChar;
}

// Test validation
const email = "user@example.com";
const password = "SecurePass123!";

console.log("Email valid:", validateEmail(email));
console.log("Password valid:", validatePassword(password));
```

### 2. Shopping Cart Price Calculator

```javascript
class ShoppingCart {
    constructor() {
        this.items = [];
        this.taxRate = 0.08; // 8% tax
    }
    
    addItem(name, price, quantity = 1) {
        this.items.push({
            name,
            price,
            quantity
        });
    }
    
    getSubtotal() {
        return this.items.reduce((total, item) => {
            return total + (item.price * item.quantity);
        }, 0);
    }
    
    getTax() {
        return this.getSubtotal() * this.taxRate;
    }
    
    getTotal() {
        return this.getSubtotal() + this.getTax();
    }
    
    formatPrice(price) {
        return `$${price.toFixed(2)}`;
    }
    
    displayReceipt() {
        console.log("=== RECEIPT ===");
        this.items.forEach(item => {
            const itemTotal = item.price * item.quantity;
            console.log(`${item.name} - ${this.formatPrice(item.price)} x ${item.quantity} = ${this.formatPrice(itemTotal)}`);
        });
        console.log(`Subtotal: ${this.formatPrice(this.getSubtotal())}`);
        console.log(`Tax: ${this.formatPrice(this.getTax())}`);
        console.log(`Total: ${this.formatPrice(this.getTotal())}`);
    }
}

// Usage
const cart = new ShoppingCart();
cart.addItem("Laptop", 999.99, 1);
cart.addItem("Mouse", 29.99, 2);
cart.addItem("Keyboard", 79.99, 1);
cart.displayReceipt();
```

### 3. Temperature Converter

```javascript
class TemperatureConverter {
    static celsiusToFahrenheit(celsius) {
        return (celsius * 9/5) + 32;
    }
    
    static fahrenheitToCelsius(fahrenheit) {
        return (fahrenheit - 32) * 5/9;
    }
    
    static celsiusToKelvin(celsius) {
        return celsius + 273.15;
    }
    
    static kelvinToCelsius(kelvin) {
        return kelvin - 273.15;
    }
    
    static convert(value, fromUnit, toUnit) {
        // First convert to Celsius
        let celsius;
        switch (fromUnit.toLowerCase()) {
            case 'celsius':
            case 'c':
                celsius = value;
                break;
            case 'fahrenheit':
            case 'f':
                celsius = this.fahrenheitToCelsius(value);
                break;
            case 'kelvin':
            case 'k':
                celsius = this.kelvinToCelsius(value);
                break;
            default:
                throw new Error('Unknown temperature unit');
        }
        
        // Then convert from Celsius to target unit
        switch (toUnit.toLowerCase()) {
            case 'celsius':
            case 'c':
                return celsius;
            case 'fahrenheit':
            case 'f':
                return this.celsiusToFahrenheit(celsius);
            case 'kelvin':
            case 'k':
                return this.celsiusToKelvin(celsius);
            default:
                throw new Error('Unknown temperature unit');
        }
    }
}

// Usage
const temp = 25;
console.log(`${temp}°C = ${TemperatureConverter.celsiusToFahrenheit(temp)}°F`);
console.log(`${temp}°C = ${TemperatureConverter.celsiusToKelvin(temp)}K`);
console.log(`77°F = ${TemperatureConverter.convert(77, 'F', 'C')}°C`);
```

## 🛠️ Common Pitfalls and Solutions

### 1. Type Coercion Issues
```javascript
// Problem: Unexpected string concatenation
let result = "10" + 5; // "105"

// Solution: Explicit type conversion
let correctResult = Number("10") + 5; // 15
```

### 2. Equality Comparison
```javascript
// Problem: Loose equality can be confusing
console.log(0 == false);  // true
console.log("" == false);  // true
console.log(null == undefined); // true

// Solution: Use strict equality
console.log(0 === false); // false
console.log("" === false); // false
console.log(null === undefined); // false
```

### 3. Variable Hoisting
```javascript
// Problem: var hoisting
console.log(hoistedVar); // undefined (not an error)
var hoistedVar = "I'm hoisted";

// Solution: Use let/const
// console.log(notHoisted); // ReferenceError
let notHoisted = "I'm not hoisted";
```

### 4. Floating Point Precision
```javascript
// Problem: Floating point arithmetic
console.log(0.1 + 0.2); // 0.30000000000000004

// Solution: Use toFixed() for currency
console.log((0.1 + 0.2).toFixed(2)); // "0.30"
```

## 📝 Best Practices

### 1. Variable Naming
- Use descriptive names
- Follow camelCase convention
- Avoid single-letter variables (except for counters)
- Use meaningful abbreviations

### 2. Type Safety
- Use `===` and `!==` for comparisons
- Be explicit about type conversions
- Check for `null` and `undefined`
- Use TypeScript for better type safety

### 3. Code Organization
- Group related variables
- Use comments to explain complex logic
- Keep variable scope minimal
- Initialize variables when declared

## 🎯 Key Takeaways

1. **JavaScript is dynamically typed** but has type coercion rules
2. **Use `let` and `const`** instead of `var` for better scoping
3. **Be careful with type coercion** - use explicit conversions
4. **Template literals** make string formatting much easier
5. **Math object** provides useful mathematical functions

## 🚀 Next Steps

1. Practice with different data types and operators
2. Build small programs using variables and functions
3. Learn about control flow and conditional logic
4. Explore more complex data structures
5. Practice debugging and error handling

## 📚 Additional Resources

### Documentation
- [MDN: Data Types](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Data_structures)
- [MDN: Operators](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators)
- [JavaScript.info: Types](https://javascript.info/types)

### Interactive Practice
- [Type Coercion Quiz](https://dorey.github.io/JavaScript-Equality-Table/)
- [JavaScript Exercises](https://www.w3resource.com/javascript-exercises/)
- [Codewars JavaScript Kata](https://www.codewars.com/?language=javascript)

---

**Ready to master JavaScript basics?** Let's move to the next module and learn about control flow! 🚀