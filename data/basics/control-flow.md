# JavaScript Control Flow

## Conditional Statements

### if Statement
```javascript
// Basic if statement
let age = 18;

if (age >= 18) {
    console.log("You are an adult");
}

// if-else statement
if (age >= 18) {
    console.log("You can vote");
} else {
    console.log("You cannot vote");
}

// if-else if-else statement
let score = 85;

if (score >= 90) {
    console.log("Grade: A");
} else if (score >= 80) {
    console.log("Grade: B");
} else if (score >= 70) {
    console.log("Grade: C");
} else if (score >= 60) {
    console.log("Grade: D");
} else {
    console.log("Grade: F");
}

// Nested if statements
let isLoggedIn = true;
let hasPermission = false;

if (isLoggedIn) {
    if (hasPermission) {
        console.log("Access granted");
    } else {
        console.log("Access denied - no permission");
    }
} else {
    console.log("Please log in");
}
```

### Ternary Operator
```javascript
// Basic ternary operator
let message = age >= 18 ? "Adult" : "Minor";
console.log(message);

// Nested ternary operator
let grade = score >= 90 ? "A" : score >= 80 ? "B" : "C";
console.log(grade);

// Ternary with function calls
function isAdmin(user) {
    return user.role === 'admin';
}

let user = { name: "John", role: "user" };
let access = isAdmin(user) ? "Full access" : "Limited access";
console.log(access);
```

### switch Statement
```javascript
// Basic switch statement
let day = "Monday";

switch (day) {
    case "Monday":
        console.log("Start of the week");
        break;
    case "Friday":
        console.log("TGIF!");
        break;
    case "Saturday":
    case "Sunday":
        console.log("Weekend!");
        break;
    default:
        console.log("Regular day");
}

// Switch with fall-through
let fruit = "apple";

switch (fruit) {
    case "apple":
    case "banana":
    case "orange":
        console.log("It's a fruit");
        break;
    case "carrot":
    case "broccoli":
        console.log("It's a vegetable");
        break;
    default:
        console.log("Unknown food");
}

// Switch with expressions
let operation = "add";
let a = 10, b = 5;
let result;

switch (operation) {
    case "add":
        result = a + b;
        break;
    case "subtract":
        result = a - b;
        break;
    case "multiply":
        result = a * b;
        break;
    case "divide":
        result = b !== 0 ? a / b : "Cannot divide by zero";
        break;
    default:
        result = "Unknown operation";
}

console.log(result);
```

## Loops

### for Loop
```javascript
// Basic for loop
for (let i = 0; i < 5; i++) {
    console.log("Iteration:", i);
}

// For loop with initialization, condition, and increment
for (let i = 10; i >= 0; i -= 2) {
    console.log("Counting down:", i);
}

// For loop with multiple variables
for (let i = 0, j = 10; i < 5; i++, j--) {
    console.log(`i: ${i}, j: ${j}`);
}

// Infinite loop with break
let count = 0;
for (;;) {
    if (count >= 3) break;
    console.log("Count:", count);
    count++;
}
```

### while Loop
```javascript
// Basic while loop
let counter = 0;
while (counter < 5) {
    console.log("Counter:", counter);
    counter++;
}

// While loop with condition based on user input (simulated)
let userInput = "";
while (userInput !== "quit") {
    console.log("Processing input:", userInput);
    userInput = "quit"; // Simulate user input
}

// While loop for validation
let number;
while (!Number.isInteger(number)) {
    number = Math.floor(Math.random() * 10);
}
console.log("Valid number:", number);
```

### do-while Loop
```javascript
// Basic do-while loop
let i = 0;
do {
    console.log("Do-while iteration:", i);
    i++;
} while (i < 3);

// Do-while loop for menu-driven programs
let choice;
do {
    console.log("1. Option 1");
    console.log("2. Option 2");
    console.log("3. Exit");
    choice = 3; // Simulate user choice
} while (choice !== 3);
```

### for...in Loop
```javascript
// Iterate over object properties
const person = {
    name: "John",
    age: 30,
    city: "New York"
};

for (let key in person) {
    console.log(`${key}: ${person[key]}`);
}

// Iterate over array indices
const fruits = ["apple", "banana", "orange"];

for (let index in fruits) {
    console.log(`Index ${index}: ${fruits[index]}`);
}

// Check for own properties
for (let key in person) {
    if (person.hasOwnProperty(key)) {
        console.log(`Own property: ${key}`);
    }
}
```

### for...of Loop (ES6)
```javascript
// Iterate over array values
const colors = ["red", "green", "blue"];

for (let color of colors) {
    console.log("Color:", color);
}

// Iterate over string characters
const text = "Hello";

for (let char of text) {
    console.log("Character:", char);
}

// Iterate over Map
const map = new Map([
    ["a", 1],
    ["b", 2],
    ["c", 3]
]);

for (let [key, value] of map) {
    console.log(`${key}: ${value}`);
}

// Iterate over Set
const set = new Set([1, 2, 3, 2, 1]);

for (let value of set) {
    console.log("Set value:", value);
}
```

## Loop Control Statements

### break Statement
```javascript
// Break in for loop
for (let i = 0; i < 10; i++) {
    if (i === 5) {
        break; // Exit loop when i is 5
    }
    console.log(i);
} // Output: 0, 1, 2, 3, 4

// Break in while loop
let j = 0;
while (j < 10) {
    if (j === 7) {
        break;
    }
    console.log(j);
    j++;
} // Output: 0, 1, 2, 3, 4, 5, 6

// Break in nested loops
outer: for (let i = 0; i < 3; i++) {
    for (let j = 0; j < 3; j++) {
        if (i === 1 && j === 1) {
            break outer; // Break outer loop
        }
        console.log(`i: ${i}, j: ${j}`);
    }
} // Output: i: 0, j: 0, i: 0, j: 1, i: 0, j: 2, i: 1, j: 0

// Break in switch
let value = 2;
switch (value) {
    case 1:
        console.log("One");
        break;
    case 2:
        console.log("Two");
        break; // Exit switch
    default:
        console.log("Other");
}
```

### continue Statement
```javascript
// Continue in for loop
for (let i = 0; i < 10; i++) {
    if (i % 2 === 0) {
        continue; // Skip even numbers
    }
    console.log(i); // Output: 1, 3, 5, 7, 9
}

// Continue in while loop
let k = 0;
while (k < 10) {
    k++;
    if (k % 3 === 0) {
        continue; // Skip multiples of 3
    }
    console.log(k); // Output: 1, 2, 4, 5, 7, 8, 10
}

// Continue in nested loops
outer: for (let i = 0; i < 3; i++) {
    for (let j = 0; j < 3; j++) {
        if (i === 1 && j === 1) {
            continue outer; // Skip to next iteration of outer loop
        }
        console.log(`i: ${i}, j: ${j}`);
    }
}
```

## Error Handling

### try-catch Statement
```javascript
// Basic try-catch
try {
    let result = riskyOperation();
    console.log(result);
} catch (error) {
    console.error("Error occurred:", error.message);
}

function riskyOperation() {
    throw new Error("Something went wrong!");
}

// try-catch-finally
try {
    console.log("Before error");
    throw new Error("Test error");
} catch (error) {
    console.error("Caught error:", error.message);
} finally {
    console.log("Finally block - always executes");
}

// Multiple catch blocks
try {
    processFile("nonexistent.txt");
} catch (error) {
    if (error instanceof TypeError) {
        console.error("Type error:", error.message);
    } else if (error instanceof ReferenceError) {
        console.error("Reference error:", error.message);
    } else {
        console.error("General error:", error.message);
    }
}

function processFile(filename) {
    if (filename === "nonexistent.txt") {
        throw new TypeError("File not found");
    }
    return "File processed";
}
```

### throw Statement
```javascript
// Throw different types of errors
function validateAge(age) {
    if (typeof age !== 'number') {
        throw new TypeError("Age must be a number");
    }
    if (age < 0) {
        throw new RangeError("Age cannot be negative");
    }
    if (age > 120) {
        throw new Error("Age seems unrealistic");
    }
    return true;
}

// Catch and re-throw
try {
    validateAge(-5);
} catch (error) {
    console.error("Validation error:", error.message);
    throw error; // Re-throw the error
}

// Custom error objects
class ValidationError extends Error {
    constructor(message) {
        super(message);
        this.name = "ValidationError";
    }
}

function validateEmail(email) {
    if (!email.includes('@')) {
        throw new ValidationError("Invalid email format");
    }
    return true;
}

try {
    validateEmail("invalid-email");
} catch (error) {
    console.error(`${error.name}: ${error.message}`);
}
```

## Modern Control Flow (ES6+)

### Destructuring in Control Flow
```javascript
// Destructuring in conditional statements
const user = { name: "John", age: 30, isAdmin: false };

const { name, age, isAdmin } = user;

if (isAdmin) {
    console.log(`${name} is an admin`);
} else {
    console.log(`${name} is a regular user (${age} years old)`);
}

// Destructuring in loops
const users = [
    { name: "Alice", score: 85 },
    { name: "Bob", score: 92 },
    { name: "Charlie", score: 78 }
];

for (const { name, score } of users) {
    if (score >= 90) {
        console.log(`${name} scored ${score} - Excellent!`);
    } else if (score >= 80) {
        console.log(`${name} scored ${score} - Good!`);
    } else {
        console.log(`${name} scored ${score} - Needs improvement`);
    }
}
```

### Optional Chaining (ES2020)
```javascript
// Optional chaining in conditions
const user = {
    profile: {
        settings: {
            theme: "dark"
        }
    }
};

// Safe property access
if (user.profile?.settings?.theme === "dark") {
    console.log("Dark theme is enabled");
}

// Optional chaining in loops
const users = [
    { name: "Alice", address: { city: "New York" } },
    { name: "Bob" }, // No address
    { name: "Charlie", address: { city: "Los Angeles" } }
];

for (const person of users) {
    const city = person.address?.city || "Unknown";
    console.log(`${name} lives in ${city}`);
}
```

### Nullish Coalescing (ES2020)
```javascript
// Nullish coalescing in conditions
const config = {
    theme: null,
    fontSize: undefined,
    language: "en"
};

const theme = config.theme ?? "light";
const fontSize = config.fontSize ?? 16;
const language = config.language ?? "en";

console.log(`Theme: ${theme}, Font size: ${fontSize}, Language: ${language}`);

// In conditional statements
function displaySettings(settings) {
    const theme = settings.theme ?? "default";
    const fontSize = settings.fontSize ?? 12;
    
    if (theme === "dark" && fontSize > 14) {
        console.log("Large dark theme enabled");
    } else {
        console.log("Using default settings");
    }
}

displaySettings({ theme: "dark", fontSize: 16 });
displaySettings({});
```

## Pattern Matching (Proposal)

### Object Pattern Matching
```javascript
// Note: Pattern matching is a Stage 3 proposal
// This shows how it might work when available

// Current approach with object matching
function processEvent(event) {
    switch (event.type) {
        case 'click':
            if (event.target.tagName === 'BUTTON') {
                console.log('Button clicked');
            } else {
                console.log('Element clicked');
            }
            break;
        case 'keydown':
            if (event.key === 'Enter') {
                console.log('Enter key pressed');
            }
            break;
        default:
            console.log('Unknown event');
    }
}

// Simulated pattern matching with objects
function handleApiResponse(response) {
    if (response.status === 200 && response.data) {
        const { data } = response;
        if (data.type === 'user') {
            console.log(`User: ${data.name}`);
        } else if (data.type === 'post') {
            console.log(`Post: ${data.title}`);
        }
    } else if (response.status === 404) {
        console.log('Not found');
    } else {
        console.log('Error:', response.status);
    }
}
```

## Practical Examples

### Input Validation
```javascript
function validateForm(formData) {
    // Check if all required fields are present
    const requiredFields = ['name', 'email', 'password'];
    
    for (const field of requiredFields) {
        if (!formData[field]) {
            throw new Error(`${field} is required`);
        }
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        throw new Error('Invalid email format');
    }
    
    // Validate password strength
    if (formData.password.length < 8) {
        throw new Error('Password must be at least 8 characters');
    }
    
    return true;
}

try {
    const formData = {
        name: "John Doe",
        email: "john@example.com",
        password: "password123"
    };
    
    validateForm(formData);
    console.log("Form is valid");
} catch (error) {
    console.error("Validation error:", error.message);
}
```

### Menu System
```javascript
function showMenu() {
    console.log("=== Main Menu ===");
    console.log("1. View Profile");
    console.log("2. Edit Profile");
    console.log("3. Settings");
    console.log("4. Logout");
    console.log("5. Exit");
}

function handleMenuChoice(choice) {
    switch (choice) {
        case "1":
            console.log("Viewing profile...");
            break;
        case "2":
            console.log("Editing profile...");
            break;
        case "3":
            showSettings();
            break;
        case "4":
            console.log("Logging out...");
            return false; // Exit menu loop
        case "5":
            console.log("Exiting...");
            return false;
        default:
            console.log("Invalid choice. Please try again.");
    }
    return true; // Continue menu loop
}

function showSettings() {
    console.log("=== Settings ===");
    console.log("1. Theme");
    console.log("2. Notifications");
    console.log("3. Privacy");
    console.log("4. Back");
    
    // Handle settings choice
    // Implementation details...
}

function runMenu() {
    let running = true;
    
    while (running) {
        showMenu();
        
        // Simulate user input
        const choice = "1"; // In real app, get from user input
        
        running = handleMenuChoice(choice);
    }
}

// runMenu(); // Uncomment to test
```

### Data Processing Pipeline
```javascript
function processItems(items) {
    const results = [];
    
    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        
        try {
            // Validate item
            if (!item || typeof item !== 'object') {
                throw new Error(`Invalid item at index ${i}`);
            }
            
            // Process item
            const processed = {
                id: item.id || i,
                name: item.name || 'Unknown',
                value: item.value || 0,
                category: categorizeItem(item)
            };
            
            results.push(processed);
            
        } catch (error) {
            console.error(`Error processing item ${i}:`, error.message);
            // Continue with next item
        }
    }
    
    return results;
}

function categorizeItem(item) {
    if (item.type === 'product') {
        return 'Products';
    } else if (item.type === 'service') {
        return 'Services';
    } else if (item.price > 100) {
        return 'Premium';
    } else {
        return 'Standard';
    }
}

const items = [
    { id: 1, name: 'Laptop', value: 999, type: 'product' },
    { id: 2, name: 'Consulting', value: 150, type: 'service' },
    { id: 3, name: 'Pen', value: 5, type: 'product' },
    null, // Invalid item
    { id: 4, name: 'Premium Service', value: 500, type: 'service' }
];

const processedItems = processItems(items);
console.log(processedItems);
```

## Best Practices

### Control Flow Best Practices
```javascript
// 1. Use meaningful variable names in conditions
const isUserLoggedIn = true;
const hasAdminPrivileges = false;

if (isUserLoggedIn && hasAdminPrivileges) {
    console.log("Admin access granted");
}

// 2. Keep conditions simple and readable
// Bad
if (x > 0 && y < 10 && z !== null && w === true && v.length > 0) {
    // Complex logic
}

// Good
const isValidInput = x > 0 && y < 10 && z !== null;
const hasValidData = w === true && v.length > 0;

if (isValidInput && hasValidData) {
    // Complex logic
}

// 3. Use early returns to reduce nesting
function processUser(user) {
    if (!user) {
        throw new Error("User is required");
    }
    
    if (!user.isActive) {
        return "User is not active";
    }
    
    if (!user.hasPermission) {
        return "User lacks permission";
    }
    
    // Main processing
    return "User processed successfully";
}

// 4. Use switch for multiple conditions on the same variable
function getDayType(day) {
    switch (day.toLowerCase()) {
        case 'monday':
        case 'tuesday':
        case 'wednesday':
        case 'thursday':
        case 'friday':
            return 'weekday';
        case 'saturday':
        case 'sunday':
            return 'weekend';
        default:
            return 'invalid';
    }
}

// 5. Handle errors appropriately
function divide(a, b) {
    try {
        if (typeof a !== 'number' || typeof b !== 'number') {
            throw new TypeError('Both arguments must be numbers');
        }
        
        if (b === 0) {
            throw new Error('Division by zero');
        }
        
        return a / b;
    } catch (error) {
        console.error('Division error:', error.message);
        return null;
    }
}

// 6. Use appropriate loop types
// Use for loop when you know the number of iterations
for (let i = 0; i < 10; i++) {
    console.log(i);
}

// Use while loop when you don't know the number of iterations
let random;
while (random !== 7) {
    random = Math.floor(Math.random() * 10) + 1;
    console.log('Trying again...');
}

// Use for...of for iterating over collections
const numbers = [1, 2, 3, 4, 5];
for (const number of numbers) {
    console.log(number * 2);
}

// Use for...in for iterating over object properties
const obj = { a: 1, b: 2, c: 3 };
for (const key in obj) {
    console.log(`${key}: ${obj[key]}`);
}
```

## Summary

JavaScript control flow provides comprehensive ways to direct program execution:

**Conditional Statements:**
- `if`, `else if`, `else` for branching logic
- Ternary operator for simple conditions
- `switch` for multiple value comparisons

**Loops:**
- `for` loop for counted iterations
- `while` loop for condition-based iterations
- `do-while` loop for at least one iteration
- `for...in` for object property iteration
- `for...of` for collection value iteration

**Control Statements:**
- `break` to exit loops early
- `continue` to skip to next iteration

**Error Handling:**
- `try-catch-finally` for exception handling
- `throw` for custom errors
- Custom error classes

**Modern Features:**
- Destructuring in control flow
- Optional chaining for safe property access
- Nullish coalescing for default values

**Best Practices:**
- Use meaningful variable names
- Keep conditions simple and readable
- Use early returns to reduce nesting
- Choose appropriate loop types
- Handle errors gracefully
- Validate inputs early

Control flow structures enable programs to make decisions, repeat operations, and handle errors effectively, forming the backbone of program logic.
