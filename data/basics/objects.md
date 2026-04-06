# JavaScript Objects

## Creating Objects

### Object Literal Syntax
```javascript
// Empty object
const emptyObject = {};

// Object with properties
const person = {
    name: "John Doe",
    age: 30,
    city: "New York"
};

console.log(person);

// Object with different property types
const mixedObject = {
    string: "Hello",
    number: 42,
    boolean: true,
    array: [1, 2, 3],
    nestedObject: {
        key: "value"
    },
    method: function() {
        return "I'm a method";
    }
};
```

### Object Constructor
```javascript
// Using Object constructor
const obj1 = new Object();
obj1.name = "Alice";
obj1.age = 25;

// Using constructor function
function Person(name, age, city) {
    this.name = name;
    this.age = age;
    this.city = city;
}

const person1 = new Person("Bob", 30, "Los Angeles");
console.log(person1);
```

### ES6 Classes
```javascript
class User {
    constructor(name, email) {
        this.name = name;
        this.email = email;
    }
    
    greet() {
        return `Hello, I'm ${this.name}`;
    }
    
    get emailDomain() {
        return this.email.split('@')[1];
    }
}

const user = new User("Charlie", "charlie@example.com");
console.log(user.greet());
console.log(user.emailDomain);
```

## Object Properties

### Property Access
```javascript
const car = {
    make: "Toyota",
    model: "Camry",
    year: 2020,
    color: "blue"
};

// Dot notation
console.log(car.make); // "Toyota"
console.log(car.year); // 2020

// Bracket notation
console.log(car["model"]); // "Camry"
console.log(car["color"]); // "blue"

// Bracket notation with variables
const property = "make";
console.log(car[property]); // "Toyota"

// Bracket notation for special characters
const specialObject = {
    "first-name": "John",
    "last name": "Doe",
    "123": "numeric key"
};

console.log(specialObject["first-name"]); // "John"
console.log(specialObject["123"]); // "numeric key"
```

### Property Assignment
```javascript
const student = {
    name: "Alice",
    grade: "A"
};

// Add new properties
student.age = 20;
student["major"] = "Computer Science";

// Update existing properties
student.grade = "A+";
student["age"] = 21;

console.log(student);
```

### Computed Property Names (ES6)
```javascript
const prefix = "user";
const id = 123;

const computedObject = {
    [`${prefix}_${id}`]: "value",
    [`${prefix}_name`]: "John",
    [Symbol("description")]: "symbol value"
};

console.log(computedObject);
```

## Object Methods

### Method Definition
```javascript
const calculator = {
    // Method shorthand (ES6)
    add(a, b) {
        return a + b;
    },
    
    // Traditional method
    subtract: function(a, b) {
        return a - b;
    },
    
    // Arrow function (this context issues)
    multiply: (a, b) => a * b,
    
    // Method with this
    divide(a, b) {
        if (b === 0) {
            throw new Error("Division by zero");
        }
        return a / b;
    }
};

console.log(calculator.add(5, 3)); // 8
console.log(calculator.subtract(10, 4)); // 6
```

### this Keyword
```javascript
const person = {
    name: "John",
    age: 30,
    
    greet() {
        return `Hello, I'm ${this.name}`;
    },
    
    celebrateBirthday() {
        this.age++;
        return `Happy ${this.age}th birthday!`;
    }
};

console.log(person.greet()); // "Hello, I'm John"
console.log(person.celebrateBirthday()); // "Happy 31st birthday!"
console.log(person.age); // 31
```

### Method Chaining
```javascript
class StringBuilder {
    constructor() {
        this.parts = [];
    }
    
    append(text) {
        this.parts.push(text);
        return this; // Return this for chaining
    }
    
    prepend(text) {
        this.parts.unshift(text);
        return this;
    }
    
    toString() {
        return this.parts.join('');
    }
}

const builder = new StringBuilder();
const result = builder
    .append("World")
    .prepend("Hello, ")
    .append("!")
    .toString();

console.log(result); // "Hello, World!"
```

## Object Prototypes

### Prototype Chain
```javascript
function Animal(name) {
    this.name = name;
}

// Add method to prototype
Animal.prototype.speak = function() {
    return `${this.name} makes a sound`;
};

// Inheritance
function Dog(name, breed) {
    Animal.call(this, name);
    this.breed = breed;
}

// Set up prototype chain
Dog.prototype = Object.create(Animal.prototype);
Dog.prototype.constructor = Dog;

// Add method to Dog prototype
Dog.prototype.bark = function() {
    return `${this.name} barks!`;
};

const myDog = new Dog("Buddy", "Golden Retriever");
console.log(myDog.speak()); // "Buddy makes a sound"
console.log(myDog.bark()); // "Buddy barks!"
```

### Object.create()
```javascript
// Create object with specific prototype
const animalPrototype = {
    speak() {
        return `${this.name} makes a sound`;
    }
};

const cat = Object.create(animalPrototype);
cat.name = "Whiskers";
console.log(cat.speak()); // "Whiskers makes a sound"

// Create object with null prototype (no inheritance)
const pureObject = Object.create(null);
pureObject.key = "value";
console.log(pureObject.key); // "value"
console.log(pureObject.toString); // undefined
```

## Object Methods

### Static Object Methods
```javascript
const user = {
    name: "John",
    age: 30,
    city: "New York",
    email: "john@example.com"
};

// Object.keys() - get all property names
const keys = Object.keys(user);
console.log(keys); // ["name", "age", "city", "email"]

// Object.values() - get all property values
const values = Object.values(user);
console.log(values); // ["John", 30, "New York", "john@example.com"]

// Object.entries() - get key-value pairs
const entries = Object.entries(user);
console.log(entries);
// [["name", "John"], ["age", 30], ["city", "New York"], ["email", "john@example.com"]]

// Object.assign() - copy properties
const target = { a: 1, b: 2 };
const source = { b: 3, c: 4 };
const result = Object.assign(target, source);
console.log(result); // {a: 1, b: 3, c: 4}

// Object.freeze() - make object immutable
const frozen = Object.freeze({ x: 1, y: 2 });
frozen.x = 3; // Fails silently in non-strict mode
console.log(frozen.x); // 1

// Object.seal() - prevent adding/removing properties
const sealed = Object.seal({ a: 1, b: 2 });
sealed.c = 3; // Fails
delete sealed.a; // Fails
sealed.b = 3; // Works
console.log(sealed); // {a: 1, b: 3}
```

### Property Descriptors
```javascript
const obj = {};

// Define property with descriptor
Object.defineProperty(obj, 'name', {
    value: 'John',
    writable: true,
    enumerable: true,
    configurable: true
});

// Define multiple properties
Object.defineProperties(obj, {
    age: {
        value: 30,
        writable: false,
        enumerable: true
    },
    email: {
        value: 'john@example.com',
        writable: true,
        enumerable: false
    }
});

console.log(obj); // {name: "John", age: 30}
console.log(Object.keys(obj)); // ["name", "age"] (email not enumerable)

// Get property descriptor
const descriptor = Object.getOwnPropertyDescriptor(obj, 'name');
console.log(descriptor);
```

## Object Destructuring

### Basic Destructuring
```javascript
const person = {
    name: "Alice",
    age: 25,
    city: "Boston"
};

// Destructure properties
const { name, age, city } = person;
console.log(name); // "Alice"
console.log(age); // 25
console.log(city); // "Boston"

// Destructure with different variable names
const { name: fullName, age: years, city: location } = person;
console.log(fullName); // "Alice"
console.log(years); // 25
console.log(location); // "Boston"
```

### Destructuring with Default Values
```javascript
const user = {
    name: "Bob",
    email: "bob@example.com"
};

// Destructure with defaults
const { name, email, age = 30, city = "Unknown" } = user;
console.log(name); // "Bob"
console.log(email); // "bob@example.com"
console.log(age); // 30 (default)
console.log(city); // "Unknown" (default)
```

### Nested Destructuring
```javascript
const employee = {
    id: 123,
    personal: {
        name: "Charlie",
        contact: {
            email: "charlie@example.com",
            phone: "555-1234"
        }
    },
    department: {
        name: "Engineering",
        location: "Building A"
    }
};

// Nested destructuring
const {
    personal: {
        name: employeeName,
        contact: { email, phone }
    },
    department: { name: deptName }
} = employee;

console.log(employeeName); // "Charlie"
console.log(email); // "charlie@example.com"
console.log(phone); // "555-1234"
console.log(deptName); // "Engineering"
```

### Rest Operator in Destructuring
```javascript
const config = {
    apiUrl: "https://api.example.com",
    timeout: 5000,
    retries: 3,
    debug: true,
    logging: false
};

// Destructure with rest
const { apiUrl, timeout, ...otherOptions } = config;
console.log(apiUrl); // "https://api.example.com"
console.log(timeout); // 5000
console.log(otherOptions); // {retries: 3, debug: true, logging: false}
```

## Object Spread Operator

### Spread Operator (ES6)
```javascript
const obj1 = { a: 1, b: 2 };
const obj2 = { c: 3, d: 4 };

// Spread to create new object
const combined = { ...obj1, ...obj2 };
console.log(combined); // {a: 1, b: 2, c: 3, d: 4}

// Spread with overrides
const overridden = { ...obj1, b: 10, e: 5 };
console.log(overridden); // {a: 1, b: 10, e: 5}

// Spread with array
const numbers = [1, 2, 3];
const withArray = { ...obj1, numbers };
console.log(withArray); // {a: 1, b: 2, numbers: [1, 2, 3]}

// Spread array into object keys
const arrayToObject = { ...numbers };
console.log(arrayToObject); // {0: 1, 1: 2, 2: 3}
```

### Cloning Objects
```javascript
const original = {
    name: "John",
    age: 30,
    address: {
        street: "123 Main St",
        city: "New York"
    }
};

// Shallow clone with spread
const shallowClone = { ...original };
shallowClone.name = "Jane";
shallowClone.address.city = "Boston";

console.log(original.name); // "John" (unchanged)
console.log(original.address.city); // "Boston" (changed - shallow clone)

// Deep clone
const deepClone = JSON.parse(JSON.stringify(original));
deepClone.name = "Bob";
deepClone.address.city = "Chicago";

console.log(original.name); // "John"
console.log(original.address.city); // "Boston" (unchanged)
```

## Object Iteration

### Iterating Over Objects
```javascript
const product = {
    name: "Laptop",
    price: 999.99,
    category: "Electronics",
    inStock: true
};

// for...in loop (iterates over enumerable properties)
console.log("for...in loop:");
for (const key in product) {
    console.log(`${key}: ${product[key]}`);
}

// Object.keys() with forEach
console.log("\nObject.keys():");
Object.keys(product).forEach(key => {
    console.log(`${key}: ${product[key]}`);
});

// Object.entries() with for...of
console.log("\nObject.entries():");
for (const [key, value] of Object.entries(product)) {
    console.log(`${key}: ${value}`);
}

// Object.values() with map
console.log("\nObject.values():");
const values = Object.values(product).map(value => {
    return typeof value === 'string' ? value.toUpperCase() : value;
});
console.log(values);
```

### Filtering Objects
```javascript
const user = {
    name: "Alice",
    age: 25,
    email: "alice@example.com",
    password: "secret123",
    isAdmin: false,
    lastLogin: "2023-01-15"
};

// Filter object properties
function filterObject(obj, predicate) {
    const result = {};
    
    for (const [key, value] of Object.entries(obj)) {
        if (predicate(key, value)) {
            result[key] = value;
        }
    }
    
    return result;
}

// Filter sensitive data
const publicUser = filterObject(user, (key, value) => 
    !['password', 'isAdmin'].includes(key)
);

console.log(publicUser);
// {name: "Alice", age: 25, email: "alice@example.com", lastLogin: "2023-01-15"}

// Filter by value type
const stringProperties = filterObject(user, (key, value) => 
    typeof value === 'string'
);

console.log(stringProperties);
// {name: "Alice", email: "alice@example.com", password: "secret123", lastLogin: "2023-01-15"}
```

## Object Comparison

### Shallow Comparison
```javascript
function shallowEqual(obj1, obj2) {
    const keys1 = Object.keys(obj1);
    const keys2 = Object.keys(obj2);
    
    if (keys1.length !== keys2.length) {
        return false;
    }
    
    for (const key of keys1) {
        if (obj1[key] !== obj2[key]) {
            return false;
        }
    }
    
    return true;
}

const objA = { a: 1, b: 2 };
const objB = { a: 1, b: 2 };
const objC = { a: 1, b: 3 };

console.log(shallowEqual(objA, objB)); // true
console.log(shallowEqual(objA, objC)); // false
```

### Deep Comparison
```javascript
function deepEqual(obj1, obj2) {
    if (obj1 === obj2) {
        return true;
    }
    
    if (obj1 == null || obj2 == null) {
        return false;
    }
    
    if (typeof obj1 !== typeof obj2) {
        return false;
    }
    
    if (typeof obj1 !== 'object') {
        return obj1 === obj2;
    }
    
    const keys1 = Object.keys(obj1);
    const keys2 = Object.keys(obj2);
    
    if (keys1.length !== keys2.length) {
        return false;
    }
    
    for (const key of keys1) {
        if (!keys2.includes(key) || !deepEqual(obj1[key], obj2[key])) {
            return false;
        }
    }
    
    return true;
}

const complexA = { a: 1, b: { c: 2, d: [3, 4] } };
const complexB = { a: 1, b: { c: 2, d: [3, 4] } };
const complexC = { a: 1, b: { c: 2, d: [3, 5] } };

console.log(deepEqual(complexA, complexB)); // true
console.log(deepEqual(complexA, complexC)); // false
```

## Object Utilities

### Utility Functions
```javascript
// Merge objects
function merge(target, ...sources) {
    return Object.assign({}, target, ...sources);
}

const merged = merge({ a: 1 }, { b: 2 }, { c: 3 });
console.log(merged); // {a: 1, b: 2, c: 3}

// Pick properties
function pick(obj, keys) {
    const result = {};
    
    for (const key of keys) {
        if (key in obj) {
            result[key] = obj[key];
        }
    }
    
    return result;
}

const picked = pick(user, ['name', 'email']);
console.log(picked); // {name: "Alice", email: "alice@example.com"}

// Omit properties
function omit(obj, keys) {
    const result = { ...obj };
    
    for (const key of keys) {
        delete result[key];
    }
    
    return result;
}

const omitted = omit(user, ['password', 'isAdmin']);
console.log(omitted); // {name: "Alice", age: 25, email: "alice@example.com", lastLogin: "2023-01-15"}

// Rename properties
function renameKeys(obj, keyMap) {
    const result = {};
    
    for (const [oldKey, value] of Object.entries(obj)) {
        const newKey = keyMap[oldKey] || oldKey;
        result[newKey] = value;
    }
    
    return result;
}

const renamed = renameKeys(user, { name: 'fullName', age: 'userAge' });
console.log(renamed);
// {fullName: "Alice", userAge: 25, email: "alice@example.com", password: "secret123", isAdmin: false, lastLogin: "2023-01-15"}
```

### Object Validation
```javascript
// Validate object against schema
function validateObject(obj, schema) {
    const errors = [];
    
    for (const [key, rules] of Object.entries(schema)) {
        const value = obj[key];
        
        // Check required
        if (rules.required && (value === undefined || value === null)) {
            errors.push(`${key} is required`);
            continue;
        }
        
        // Skip validation if value is optional and not provided
        if (!rules.required && (value === undefined || value === null)) {
            continue;
        }
        
        // Check type
        if (rules.type && typeof value !== rules.type) {
            errors.push(`${key} must be of type ${rules.type}`);
        }
        
        // Check custom validation
        if (rules.validate && !rules.validate(value)) {
            errors.push(`${key} is invalid`);
        }
    }
    
    return {
        isValid: errors.length === 0,
        errors
    };
}

const userSchema = {
    name: {
        required: true,
        type: 'string',
        validate: (val) => val.length > 0
    },
    age: {
        required: true,
        type: 'number',
        validate: (val) => val >= 0 && val <= 150
    },
    email: {
        required: true,
        type: 'string',
        validate: (val) => val.includes('@')
    }
};

const testUser = { name: "", age: 25, email: "invalid" };
const validation = validateObject(testUser, userSchema);
console.log(validation);
// {isValid: false, errors: ["name is invalid", "email is invalid"]}
```

## Best Practices

### Object Best Practices
```javascript
// 1. Use object literals for simple objects
const person = {
    name: "John",
    age: 30
};

// 2. Use classes for complex objects with methods
class Person {
    constructor(name, age) {
        this.name = name;
        this.age = age;
    }
    
    greet() {
        return `Hello, I'm ${this.name}`;
    }
}

// 3. Use const for object references
const config = { apiUrl: "https://api.example.com" };
config.timeout = 5000; // Can modify properties
// config = {}; // Error - cannot reassign const

// 4. Use descriptive property names
const userProfile = {
    firstName: "John",
    lastName: "Doe",
    emailAddress: "john@example.com",
    dateOfBirth: "1990-01-01"
};

// 5. Use method shorthand in object literals
const calculator = {
    add(a, b) {
        return a + b;
    },
    subtract(a, b) {
        return a - b;
    }
};

// 6. Use destructuring for cleaner code
function processUser({ name, age, email }) {
    console.log(`Processing ${name}, age ${age}, email ${email}`);
}

// 7. Use Object methods for common operations
const keys = Object.keys(obj);
const values = Object.values(obj);
const entries = Object.entries(obj);

// 8. Use spread operator for cloning and merging
const clone = { ...original };
const merged = { ...obj1, ...obj2 };

// 9. Validate object properties
function createUser(userData) {
    if (!userData.name || typeof userData.name !== 'string') {
        throw new Error('Name is required and must be a string');
    }
    
    return {
        name: userData.name,
        createdAt: new Date(),
        ...userData
    };
}

// 10. Use getters and setters for computed properties
class Rectangle {
    constructor(width, height) {
        this.width = width;
        this.height = height;
    }
    
    get area() {
        return this.width * this.height;
    }
    
    set diagonal(value) {
        const ratio = value / Math.sqrt(2);
        this.width = ratio;
        this.height = ratio;
    }
}
```

## Common Pitfalls

### Common Object Mistakes
```javascript
// 1. Comparing objects with ===
const obj1 = { a: 1 };
const obj2 = { a: 1 };
console.log(obj1 === obj2); // false (different objects)

// 2. Modifying objects while iterating
const data = { a: 1, b: 2, c: 3 };
for (const key in data) {
    if (key === 'b') {
        delete data[key]; // Can cause issues
    }
}

// 3. Using hasOwnProperty incorrectly
const obj3 = Object.create({ inherited: 'value' });
obj3.own = 'own value';

// Bad: doesn't check inherited properties
console.log('inherited' in obj3); // true

// Good: check own properties
console.log(obj3.hasOwnProperty('inherited')); // false
console.log(obj3.hasOwnProperty('own')); // true

// 4. Forgetting that Object.assign mutates the target
const target = { a: 1 };
Object.assign(target, { b: 2 });
console.log(target); // {a: 1, b: 2} (mutated)

// Better: create new object
const immutable = Object.assign({}, { a: 1 }, { b: 2 });

// 5. Using for...in on arrays (should use for...of)
const arr = [1, 2, 3];
for (const index in arr) {
    console.log(index); // "0", "1", "2" (strings)
}

// 6. Confusing Object.create() with {}
const obj4 = Object.create({ protoProp: 'value' });
console.log(obj4.protoProp); // 'value' (from prototype)
console.log(obj4.hasOwnProperty('protoProp')); // false

// 7. Not handling undefined/null in destructuring
const obj5 = { a: 1, b: 2 };
const { a, b, c } = obj5; // c is undefined
console.log(c); // undefined

// Better: use defaults
const { a, b, c = 'default' } = obj5;
console.log(c); // 'default'
```

## Summary

JavaScript objects provide flexible data structures:

**Creation:**
- Object literal: `{key: value}`
- Constructor functions: `new Object()`
- ES6 classes: `class Name {}`
- `Object.create()` for prototype-based creation

**Properties:**
- Dot notation: `obj.property`
- Bracket notation: `obj["property"]`
- Computed property names: `[expression]`
- Property descriptors for fine-grained control

**Methods:**
- Method shorthand: `method() {}`
- `this` keyword for context
- Method chaining with `return this`

**Prototypes:**
- Prototype chain for inheritance
- `Object.create()` for custom prototypes
- Constructor functions with prototypes

**ES6+ Features:**
- Destructuring: `{key} = obj`
- Spread operator: `{...obj}`
- Computed property names
- Class syntax

**Object Methods:**
- `Object.keys()`, `Object.values()`, `Object.entries()`
- `Object.assign()`, `Object.freeze()`, `Object.seal()`
- Property descriptors

**Best Practices:**
- Use descriptive property names
- Prefer `const` for object references
- Use destructuring for cleaner code
- Validate object properties
- Handle undefined/null appropriately

Objects are fundamental JavaScript data structures that enable organizing and manipulating related data as cohesive units.
