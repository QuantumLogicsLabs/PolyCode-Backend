# JavaScript Functions

## Function Declaration

### Function Declaration
```javascript
// Function declaration
function greet(name) {
    return "Hello, " + name + "!";
}

console.log(greet("John")); // "Hello, John!"

// Function with multiple parameters
function add(a, b) {
    return a + b;
}

console.log(add(5, 3)); // 8

// Function with default parameters (ES6)
function greetWithDefault(name = "Guest") {
    return "Hello, " + name + "!";
}

console.log(greetWithDefault()); // "Hello, Guest!"
console.log(greetWithDefault("Alice")); // "Hello, Alice!"
```

### Function Expression
```javascript
// Function expression
const multiply = function(a, b) {
    return a * b;
};

console.log(multiply(4, 5)); // 20

// Anonymous function
const divide = function(a, b) {
    if (b === 0) {
        throw new Error("Division by zero");
    }
    return a / b;
};

console.log(divide(10, 2)); // 5
```

### Arrow Functions (ES6)
```javascript
// Basic arrow function
const square = (x) => x * x;
console.log(square(5)); // 25

// Arrow function with multiple parameters
const add = (a, b) => a + b;
console.log(add(3, 4)); // 7

// Arrow function with multiple statements
const calculate = (a, b, operation) => {
    switch (operation) {
        case 'add':
            return a + b;
        case 'subtract':
            return a - b;
        case 'multiply':
            return a * b;
        case 'divide':
            return a / b;
        default:
            throw new Error("Unknown operation");
    }
};

console.log(calculate(10, 5, 'add')); // 15

// Arrow functions and 'this'
const person = {
    name: "John",
    age: 30,
    
    // Regular function - 'this' refers to the object
    greetRegular: function() {
        return "Hello, I'm " + this.name;
    },
    
    // Arrow function - 'this' inherits from surrounding scope
    greetArrow: () => {
        return "Hello, I'm " + this.name; // 'this' might not be person
    }
};

console.log(person.greetRegular()); // "Hello, I'm John"
console.log(person.greetArrow()); // Might not work as expected
```

## Function Parameters

### Default Parameters
```javascript
// ES6 default parameters
function createButton(text = "Click me", color = "blue", size = "medium") {
    return {
        text: text,
        color: color,
        size: size
    };
}

const button1 = createButton();
console.log(button1); // {text: "Click me", color: "blue", size: "medium"}

const button2 = createButton("Submit");
console.log(button2); // {text: "Submit", color: "blue", size: "medium"}

const button3 = createButton("Cancel", "red", "small");
console.log(button3); // {text: "Cancel", color: "red", size: "small"}

// Default parameters with expressions
function log(message, timestamp = new Date().toISOString()) {
    console.log(`[${timestamp}] ${message}`);
}

log("Hello"); // Logs with current timestamp
```

### Rest Parameters
```javascript
// Rest parameters (ES6)
function sum(...numbers) {
    return numbers.reduce((total, num) => total + num, 0);
}

console.log(sum(1, 2, 3, 4, 5)); // 15
console.log(sum(10, 20)); // 30
console.log(sum()); // 0

// Rest parameters with regular parameters
function greet(greeting, ...names) {
    return greeting + ", " + names.join(", ") + "!";
}

console.log(greet("Hello", "John", "Jane", "Bob")); // "Hello, John, Jane, Bob!"

// Rest parameter must be last
// function invalid(a, ...rest, b) {} // Error: Rest parameter must be last
```

### Arguments Object
```javascript
// Arguments object (pre-ES6)
function sumAll() {
    let total = 0;
    for (let i = 0; i < arguments.length; i++) {
        total += arguments[i];
    }
    return total;
}

console.log(sumAll(1, 2, 3, 4, 5)); // 15

// Arguments with named parameters
function logArguments(a, b) {
    console.log("a:", a);
    console.log("b:", b);
    console.log("All arguments:", arguments);
    console.log("Length:", arguments.length);
}

logArguments(1, 2, 3, 4, 5);
```

## Function Scope and Closures

### Function Scope
```javascript
function outerFunction() {
    const outerVar = "I'm outside";
    
    function innerFunction() {
        const innerVar = "I'm inside";
        console.log(outerVar); // Can access outer variable
        console.log(innerVar); // Can access inner variable
    }
    
    innerFunction();
    // console.log(innerVar); // Error: innerVar is not defined
}

outerFunction();
```

### Closures
```javascript
// Closure example
function createCounter() {
    let count = 0;
    
    return function() {
        count++;
        return count;
    };
}

const counter1 = createCounter();
const counter2 = createCounter();

console.log(counter1()); // 1
console.log(counter1()); // 2
console.log(counter2()); // 1 (separate closure)
console.log(counter1()); // 3

// Closure with parameters
function createMultiplier(factor) {
    return function(number) {
        return number * factor;
    };
}

const double = createMultiplier(2);
const triple = createMultiplier(3);

console.log(double(5)); // 10
console.log(triple(5)); // 15

// Closure in loops
function createFunctions() {
    const functions = [];
    
    for (let i = 0; i < 3; i++) {
        functions.push(function() {
            return i; // Each function captures its own i
        });
    }
    
    return functions;
}

const funcs = createFunctions();
console.log(funcs[0]()); // 0
console.log(funcs[1]()); // 1
console.log(funcs[2]()); // 2
```

## Higher-Order Functions

### Functions as Arguments
```javascript
// Function that takes another function as argument
function applyOperation(a, b, operation) {
    return operation(a, b);
}

function add(a, b) {
    return a + b;
}

function multiply(a, b) {
    return a * b;
}

console.log(applyOperation(5, 3, add)); // 8
console.log(applyOperation(5, 3, multiply)); // 15

// Array methods using functions
const numbers = [1, 2, 3, 4, 5];

const doubled = numbers.map(x => x * 2);
console.log(doubled); // [2, 4, 6, 8, 10]

const evens = numbers.filter(x => x % 2 === 0);
console.log(evens); // [2, 4]

const sum = numbers.reduce((total, num) => total + num, 0);
console.log(sum); // 15
```

### Functions as Return Values
```javascript
// Function that returns another function
function createGreeter(greeting) {
    return function(name) {
        return greeting + ", " + name + "!";
    };
}

const sayHello = createGreeter("Hello");
const sayHi = createGreeter("Hi");

console.log(sayHello("John")); // "Hello, John!"
console.log(sayHi("Jane")); // "Hi, Jane!"

// Function factory
function createCalculator(operation) {
    switch (operation) {
        case 'add':
            return (a, b) => a + b;
        case 'subtract':
            return (a, b) => a - b;
        case 'multiply':
            return (a, b) => a * b;
        case 'divide':
            return (a, b) => a / b;
        default:
            throw new Error("Unknown operation");
    }
}

const adder = createCalculator('add');
const multiplier = createCalculator('multiply');

console.log(adder(10, 5)); // 15
console.log(multiplier(10, 5)); // 50
```

## Methods

### Object Methods
```javascript
const person = {
    firstName: "John",
    lastName: "Doe",
    age: 30,
    
    // Method shorthand (ES6)
    fullName() {
        return this.firstName + " " + this.lastName;
    },
    
    // Method with parameters
    greet(greeting = "Hello") {
        return greeting + ", I'm " + this.fullName();
    },
    
    // Method that modifies the object
    haveBirthday() {
        this.age++;
        return "Happy " + this.age + "th birthday!";
    }
};

console.log(person.fullName()); // "John Doe"
console.log(person.greet()); // "Hello, I'm John Doe"
console.log(person.haveBirthday()); // "Happy 31st birthday!"
console.log(person.age); // 31
```

### Prototype Methods
```javascript
// Constructor function
function Person(firstName, lastName, age) {
    this.firstName = firstName;
    this.lastName = lastName;
    this.age = age;
}

// Add method to prototype
Person.prototype.fullName = function() {
    return this.firstName + " " + this.lastName;
};

Person.prototype.greet = function(greeting = "Hello") {
    return greeting + ", I'm " + this.fullName();
};

const john = new Person("John", "Doe", 30);
console.log(john.fullName()); // "John Doe"
console.log(john.greet()); // "Hello, I'm John Doe"

// All Person instances share the same methods
const jane = new Person("Jane", "Smith", 25);
console.log(john.fullName === jane.fullName); // true (same function)
```

## Function Properties and Methods

### Function Properties
```javascript
function example() {
    return "Hello";
}

// Add properties to function
example.description = "This is an example function";
example.version = "1.0";

console.log(example.description); // "This is an example function"
console.log(example.version); // "1.0"

// Function properties for caching
function fibonacci(n) {
    // Check cache first
    if (fibonacci.cache[n] !== undefined) {
        return fibonacci.cache[n];
    }
    
    // Calculate and cache result
    if (n <= 1) {
        return n;
    }
    
    const result = fibonacci(n - 1) + fibonacci(n - 2);
    fibonacci.cache[n] = result;
    return result;
}

// Initialize cache
fibonacci.cache = {};

console.log(fibonacci(10)); // 55
console.log(fibonacci.cache); // Contains cached results
```

### Function Methods
```javascript
function greet(name) {
    return "Hello, " + name + "!";
}

// call() method
console.log(greet.call(null, "John")); // "Hello, John!"

// apply() method
console.log(greet.apply(null, ["Jane"])); // "Hello, Jane!"

// bind() method
const greetJohn = greet.bind(null, "John");
console.log(greetJohn()); // "Hello, John!"

// Using bind with context
const person = {
    name: "Alice",
    greet: function(greeting) {
        return greeting + ", I'm " + this.name;
    }
};

const greetAlice = person.greet.bind(person);
console.log(greetAlice("Hi")); // "Hi, I'm Alice"
```

## Recursion

### Recursive Functions
```javascript
// Factorial function
function factorial(n) {
    if (n <= 1) {
        return 1;
    }
    return n * factorial(n - 1);
}

console.log(factorial(5)); // 120

// Fibonacci sequence
function fibonacci(n) {
    if (n <= 1) {
        return n;
    }
    return fibonacci(n - 1) + fibonacci(n - 2);
}

console.log(fibonacci(10)); // 55

// Tree traversal (recursive)
const tree = {
    value: 1,
    left: {
        value: 2,
        left: { value: 4 },
        right: { value: 5 }
    },
    right: {
        value: 3,
        left: { value: 6 },
        right: { value: 7 }
    }
};

function traverseTree(node) {
    if (!node) return;
    
    console.log(node.value); // Pre-order traversal
    traverseTree(node.left);
    traverseTree(node.right);
}

traverseTree(tree); // 1, 2, 4, 5, 3, 6, 7
```

### Memoization
```javascript
// Memoized factorial
function memoizedFactorial() {
    const cache = {};
    
    return function factorial(n) {
        if (cache[n] !== undefined) {
            return cache[n];
        }
        
        if (n <= 1) {
            return 1;
        }
        
        const result = n * factorial(n - 1);
        cache[n] = result;
        return result;
    };
}

const fastFactorial = memoizedFactorial();
console.log(fastFactorial(20)); // Much faster than regular recursion
```

## Immediately Invoked Function Expressions (IIFE)

### Basic IIFE
```javascript
// Traditional IIFE
(function() {
    const message = "IIFE executed!";
    console.log(message);
})();

// IIFE with parameters
(function(name) {
    console.log("Hello, " + name + "!");
})("World");

// Arrow function IIFE
(() => {
    console.log("Arrow function IIFE");
})();

// IIFE returning value
const result = (function() {
    return "IIFE result";
})();

console.log(result); // "IIFE result"
```

### IIFE Use Cases
```javascript
// Module pattern
const calculator = (function() {
    let total = 0;
    
    return {
        add: function(num) {
            total += num;
            return total;
        },
        subtract: function(num) {
            total -= num;
            return total;
        },
        getTotal: function() {
            return total;
        }
    };
})();

console.log(calculator.add(10)); // 10
console.log(calculator.add(5));  // 15
console.log(calculator.subtract(3)); // 12
console.log(calculator.getTotal()); // 12

// Private variables
const counter = (function() {
    let count = 0;
    
    return {
        increment: function() {
            count++;
            return count;
        },
        decrement: function() {
            count--;
            return count;
        },
        getCount: function() {
            return count;
        }
    };
})();

console.log(counter.increment()); // 1
console.log(counter.increment()); // 2
console.log(counter.getCount()); // 2
```

## Generators

### Generator Functions
```javascript
// Basic generator function
function* numberGenerator() {
    yield 1;
    yield 2;
    yield 3;
}

const gen = numberGenerator();
console.log(gen.next().value); // 1
console.log(gen.next().value); // 2
console.log(gen.next().value); // 3
console.log(gen.next().value); // undefined

// Generator with parameters
function* range(start, end) {
    for (let i = start; i < end; i++) {
        yield i;
    }
}

const rangeGen = range(1, 5);
console.log(rangeGen.next().value); // 1
console.log(rangeGen.next().value); // 2
console.log(rangeGen.next().value); // 3
console.log(rangeGen.next().value); // 4
console.log(rangeGen.next().value); // undefined

// Infinite generator
function* fibonacci() {
    let a = 0, b = 1;
    
    while (true) {
        yield a;
        [a, b] = [b, a + b];
    }
}

const fibGen = fibonacci();
console.log(fibGen.next().value); // 0
console.log(fibGen.next().value); // 1
console.log(fibGen.next().value); // 1
console.log(fibGen.next().value); // 2
console.log(fibGen.next().value); // 3
```

### Generator Methods
```javascript
function* generator() {
    yield 1;
    yield 2;
    yield 3;
    yield 4;
}

const gen = generator();

// next()
console.log(gen.next()); // {value: 1, done: false}
console.log(gen.next()); // {value: 2, done: false}

// return()
console.log(gen.return(10)); // {value: 10, done: true}

// throw()
function* errorGenerator() {
    try {
        yield 1;
        yield 2;
        yield 3;
    } catch (e) {
        console.log("Caught:", e);
        yield 4;
    }
}

const errorGen = errorGenerator();
console.log(errorGen.next()); // {value: 1, done: false}
console.log(errorGen.throw("Error")); // Caught: Error, {value: 4, done: false}
```

## Async Functions

### Basic Async/Await
```javascript
// Promise-based function
function fetchData() {
    return new Promise(resolve => {
        setTimeout(() => {
            resolve("Data fetched!");
        }, 1000);
    });
}

// Async function
async function getData() {
    try {
        const data = await fetchData();
        console.log(data);
    } catch (error) {
        console.error("Error:", error);
    }
}

getData(); // "Data fetched!" after 1 second

// Async function with return value
async function getValue() {
    await new Promise(resolve => setTimeout(resolve, 1000));
    return 42;
}

getValue().then(value => console.log(value)); // 42
```

### Async Functions with Error Handling
```javascript
async function fetchUserData(userId) {
    try {
        // Simulate API call
        const response = await new Promise((resolve, reject) => {
            setTimeout(() => {
                if (userId === 1) {
                    resolve({ id: 1, name: "John", age: 30 });
                } else {
                    reject(new Error("User not found"));
                }
            }, 1000);
        });
        
        return response;
    } catch (error) {
        console.error("Failed to fetch user:", error.message);
        return null;
    }
}

// Usage
fetchUserData(1).then(user => {
    if (user) {
        console.log("User:", user);
    }
});

fetchUserData(2).then(user => {
    if (user) {
        console.log("User:", user);
    }
});
```

## Function Best Practices

### Function Design Principles
```javascript
// 1. Single responsibility
function calculateArea(width, height) {
    return width * height;
}

function formatArea(area) {
    return area.toFixed(2) + " sq units";
}

// 2. Pure functions (no side effects)
function add(a, b) {
    return a + b;
}

// 3. Descriptive names
function calculateMonthlyPayment(principal, rate, years) {
    // Clear purpose
}

// 4. Parameter validation
function divide(a, b) {
    if (typeof a !== 'number' || typeof b !== 'number') {
        throw new TypeError('Both parameters must be numbers');
    }
    if (b === 0) {
        throw new Error('Division by zero');
    }
    return a / b;
}

// 5. Default parameters for flexibility
function createElement(tag, content = "", className = "") {
    const element = document.createElement(tag);
    element.textContent = content;
    if (className) {
        element.className = className;
    }
    return element;
}
```

### Performance Considerations
```javascript
// 1. Memoization for expensive operations
const memoizedExpensive = (() => {
    const cache = new Map();
    
    return (input) => {
        if (cache.has(input)) {
            return cache.get(input);
        }
        
        const result = /* expensive computation */;
        cache.set(input, result);
        return result;
    };
})();

// 2. Debouncing for frequent calls
function debounce(func, delay) {
    let timeoutId;
    
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

// 3. Caching in closures
function createCachedFunction() {
    const cache = new Map();
    
    return function(key, computeFn) {
        if (cache.has(key)) {
            return cache.get(key);
        }
        
        const result = computeFn();
        cache.set(key, result);
        return result;
    };
}
```

## Summary

JavaScript functions are first-class citizens that provide:

**Declaration Types:**
- Function declarations
- Function expressions
- Arrow functions (ES6)
- Generator functions
- Async functions

**Key Features:**
- Parameters and default values
- Rest parameters and arguments object
- Closures and lexical scope
- Higher-order functions
- Methods and prototypes

**Advanced Concepts:**
- IIFE for module patterns
- Generators for lazy evaluation
- Async/await for asynchronous code
- Memoization for performance
- Function binding and context

**Best Practices:**
- Use arrow functions for short callbacks
- Prefer const for function declarations
- Validate parameters
- Handle errors appropriately
- Use descriptive names
- Keep functions small and focused

Functions are fundamental building blocks in JavaScript, enabling modular, reusable, and maintainable code.
