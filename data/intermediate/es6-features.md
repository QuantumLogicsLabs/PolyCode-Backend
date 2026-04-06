# JavaScript ES6+ Features

## ECMAScript 2015 (ES6)

### Let and Const
```javascript
// let - block-scoped variables
function letExample() {
    if (true) {
        let x = 10;
        console.log(x); // 10
    }
    // console.log(x); // ReferenceError: x is not defined
}

// const - block-scoped constants
function constExample() {
    const PI = 3.14159;
    // PI = 3.14; // TypeError: Assignment to constant variable
    
    const obj = { name: "John" };
    obj.name = "Jane"; // Allowed - object properties can change
    // obj = {}; // TypeError: Assignment to constant variable
}

// Temporal Dead Zone
function temporalDeadZone() {
    // console.log(x); // ReferenceError
    let x = 10;
    console.log(x); // 10
}
```

### Arrow Functions
```javascript
// Basic arrow functions
const add = (a, b) => a + b;
const square = x => x * x;
const greet = () => "Hello, World!";

console.log(add(5, 3)); // 8
console.log(square(4)); // 16
console.log(greet()); // "Hello, World!"

// Arrow functions with multiple statements
const calculate = (a, b, operation) => {
    switch (operation) {
        case 'add':
            return a + b;
        case 'subtract':
            return a - b;
        case 'multiply':
            return a * b;
        default:
            throw new Error('Unknown operation');
    }
};

// Arrow functions and 'this'
const person = {
    name: "John",
    age: 30,
    
    // Regular function - 'this' refers to person
    greetRegular: function() {
        return `Hello, I'm ${this.name}`;
    },
    
    // Arrow function - 'this' inherits from surrounding scope
    greetArrow: () => {
        return `Hello, I'm ${this.name}`; // 'this' might not be person
    }
};

console.log(person.greetRegular()); // "Hello, I'm John"
console.log(person.greetArrow()); // Might not work as expected
```

### Template Literals
```javascript
// Basic template literals
const name = "John";
const age = 30;
const message = `Hello, my name is ${name} and I'm ${age} years old.`;
console.log(message);

// Multi-line strings
const multiLine = `
This is a
multi-line
string
with template literals
`;
console.log(multiLine);

// Tagged template literals
function highlight(strings, ...values) {
    return strings.reduce((result, string, i) => {
        return result + string + (values[i] ? `<strong>${values[i]}</strong>` : '');
    }, '');
}

const highlighted = highlight`Name: ${name}, Age: ${age}`;
console.log(highlighted); // Name: <strong>John</strong>, Age: <strong>30</strong>
```

### Destructuring
```javascript
// Object destructuring
const person = {
    name: "John",
    age: 30,
    city: "New York",
    country: "USA"
};

const { name, age, city } = person;
console.log(name, age, city); // "John", 30, "New York"

// With default values
const { name: fullName, age: years, profession = "Unknown" } = person;
console.log(fullName, years, profession); // "John", 30, "Unknown"

// Array destructuring
const numbers = [1, 2, 3, 4, 5];
const [first, second, ...rest] = numbers;
console.log(first, second, rest); // 1, 2, [3, 4, 5]

// Swap variables
let a = 1, b = 2;
[a, b] = [b, a];
console.log(a, b); // 2, 1

// Function parameter destructuring
function createUser({ name, age, city = "Unknown" }) {
    return { name, age, city };
}

const user = createUser({ name: "Alice", age: 25 });
console.log(user); // { name: "Alice", age: 25, city: "Unknown" }
```

### Default Parameters
```javascript
// Function with default parameters
function greet(name = "Guest", greeting = "Hello") {
    return `${greeting}, ${name}!`;
}

console.log(greet()); // "Hello, Guest!"
console.log(greet("Bob")); // "Hello, Bob!"
console.log(greet("Charlie", "Hi")); // "Hi, Charlie!"

// Default parameters with destructuring
function createPerson({ name = "Anonymous", age = 0, city = "Unknown" } = {}) {
    return { name, age, city };
}

console.log(createPerson()); // { name: "Anonymous", age: 0, city: "Unknown" }
console.log(createPerson({ name: "John" })); // { name: "John", age: 0, city: "Unknown" }
```

### Rest and Spread Operators
```javascript
// Rest parameters
function sum(...numbers) {
    return numbers.reduce((total, num) => total + num, 0);
}

console.log(sum(1, 2, 3, 4, 5)); // 15

// Spread operator for arrays
const arr1 = [1, 2, 3];
const arr2 = [4, 5, 6];
const combined = [...arr1, ...arr2];
console.log(combined); // [1, 2, 3, 4, 5, 6]

// Spread operator for objects
const obj1 = { a: 1, b: 2 };
const obj2 = { c: 3, d: 4 };
const merged = { ...obj1, ...obj2 };
console.log(merged); // { a: 1, b: 2, c: 3, d: 4 }

// Spread operator for function arguments
const numbers2 = [1, 2, 3];
console.log(Math.max(...numbers2)); // 3
```

### Enhanced Object Literals
```javascript
// Property shorthand
const name = "John";
const age = 30;

const person = {
    name, // same as name: name
    age,  // same as age: age
    greet() {
        return `Hello, I'm ${this.name}`;
    }
};

console.log(person.greet()); // "Hello, I'm John"

// Computed property names
const propName = "dynamic";
const dynamicObject = {
    [propName]: "value",
    [`${propName}_2`]: "value2"
};

console.log(dynamicObject.dynamic); // "value"
console.log(dynamicObject.dynamic_2); // "value2"
```

### Classes
```javascript
// Basic class
class Animal {
    constructor(name, species) {
        this.name = name;
        this.species = species;
    }
    
    speak() {
        return `${this.name} makes a sound`;
    }
    
    // Static method
    static kingdom() {
        return "Animalia";
    }
}

class Dog extends Animal {
    constructor(name, breed) {
        super(name, "Dog");
        this.breed = breed;
    }
    
    speak() {
        return `${this.name} barks!`;
    }
    
    // Getter
    get description() {
        return `${this.name} is a ${this.breed}`;
    }
    
    // Setter
    set nickname(nick) {
        this._nickname = nick;
    }
    
    get nickname() {
        return this._nickname;
    }
}

const dog = new Dog("Buddy", "Golden Retriever");
console.log(dog.speak()); // "Buddy barks!"
console.log(dog.description); // "Buddy is a Golden Retriever"
dog.nickname = "Bud";
console.log(dog.nickname); // "Bud"
console.log(Animal.kingdom()); // "Animalia"
```

### Modules
```javascript
// math.js (exporting)
export const PI = 3.14159;

export function add(a, b) {
    return a + b;
}

export function multiply(a, b) {
    return a * b;
}

export default function divide(a, b) {
    return a / b;
}

// main.js (importing)
import divide, { PI, add, multiply } from './math.js';

console.log(PI); // 3.14159
console.log(add(5, 3)); // 8
console.log(multiply(4, 5)); // 20
console.log(divide(10, 2)); // 5

// Import all
import * as math from './math.js';
console.log(math.PI); // 3.14159
```

### Symbols
```javascript
// Creating symbols
const idSymbol = Symbol('id');
const anotherIdSymbol = Symbol('id');

console.log(idSymbol === anotherIdSymbol); // false

// Using symbols as object keys
const obj = {
    [idSymbol]: 123,
    name: "John"
};

console.log(obj[idSymbol]); // 123
console.log(Object.keys(obj)); // ["name"] - symbols are not enumerable

// Well-known symbols
const myIterable = {
    [Symbol.iterator]() {
        let step = 0;
        return {
            next() {
                step++;
                return { value: step, done: step > 3 };
            }
        };
    }
};

for (const value of myIterable) {
    console.log(value); // 1, 2, 3
}
```

## ECMAScript 2016 (ES7)

### Exponentiation Operator
```javascript
// Exponentiation operator (**)
console.log(2 ** 3); // 8
console.log(10 ** 2); // 100

// Equivalent to Math.pow()
console.log(Math.pow(2, 3)); // 8

// With variables
const base = 2;
const exponent = 4;
console.log(base ** exponent); // 16

// Combined with assignment
let result = 2;
result **= 3;
console.log(result); // 8
```

### Array.prototype.includes()
```javascript
// Array.prototype.includes()
const numbers = [1, 2, 3, 4, 5];

console.log(numbers.includes(3)); // true
console.log(numbers.includes(6)); // false

// With fromIndex parameter
console.log(numbers.includes(3, 3)); // false (starts searching from index 3)
console.log(numbers.includes(4, 3)); // true

// Works with NaN
const values = [1, 2, NaN, 4];
console.log(values.includes(NaN)); // true (unlike indexOf)
console.log(values.indexOf(NaN)); // -1
```

## ECMAScript 2017 (ES8)

### Async/Await
```javascript
// Async functions
async function fetchData() {
    try {
        const response = await fetch('https://api.example.com/data');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// Using async function
fetchData()
    .then(data => console.log(data))
    .catch(error => console.error(error));

// Async arrow function
const fetchUserData = async (userId) => {
    const user = await fetchUser(userId);
    const posts = await fetchPosts(user.id);
    return { user, posts };
};

// Helper functions for demonstration
function fetchUser(id) {
    return Promise.resolve({ id, name: "John" });
}

function fetchPosts(userId) {
    return Promise.resolve([{ title: "Post 1" }]);
}
```

### Object.values()
```javascript
// Object.values()
const person = {
    name: "John",
    age: 30,
    city: "New York"
};

const values = Object.values(person);
console.log(values); // ["John", 30, "New York"]

// Works with arrays
const arrayLike = { 0: 'a', 1: 'b', 2: 'c', length: 3 };
console.log(Object.values(arrayLike)); // ['a', 'b', 'c']
```

### Object.entries()
```javascript
// Object.entries()
const person = {
    name: "John",
    age: 30,
    city: "New York"
};

const entries = Object.entries(person);
console.log(entries); // [["name", "John"], ["age", 30], ["city", "New York"]]

// Convert entries back to object
const fromEntries = Object.fromEntries(entries);
console.log(fromEntries); // { name: "John", age: 30, city: "New York" }

// Useful for object manipulation
const transformed = Object.entries(person)
    .filter(([key, value]) => typeof value === 'string')
    .reduce((obj, [key, value]) => {
        obj[key] = value;
        return obj;
    }, {});

console.log(transformed); // { name: "John", city: "New York" }
```

### String Padding
```javascript
// String.prototype.padStart()
const str = "5";
console.log(str.padStart(2, '0')); // "05"
console.log("hello".padStart(10, '*')); // "*****hello"

// String.prototype.padEnd()
console.log(str.padEnd(2, '0')); // "50"
console.log("hello".padEnd(10, '*')); // "hello*****"

// Practical usage
const numbers = [1, 2, 3, 10, 20];
const maxDigits = Math.max(...numbers.map(n => n.toString().length));
const paddedNumbers = numbers.map(n => n.toString().padStart(maxDigits, '0'));
console.log(paddedNumbers); // ["01", "02", "03", "10", "20"]
```

### Object.getOwnPropertyDescriptors()
```javascript
// Object.getOwnPropertyDescriptors()
const obj = {
    name: "John",
    get age() {
        return 30;
    }
};

const descriptors = Object.getOwnPropertyDescriptors(obj);
console.log(descriptors.name);
// { value: "John", writable: true, enumerable: true, configurable: true }

console.log(descriptors.age);
// { get: [Function: get age], set: undefined, enumerable: true, configurable: true }

// Shallow copy with descriptors
const copy = Object.create(
    Object.getPrototypeOf(obj),
    Object.getOwnPropertyDescriptors(obj)
);
```

## ECMAScript 2018 (ES9)

### Object Rest Properties
```javascript
// Object rest properties
const person = {
    name: "John",
    age: 30,
    city: "New York",
    country: "USA"
};

const { name, age, ...rest } = person;
console.log(name, age); // "John", 30
console.log(rest); // { city: "New York", country: "USA" }

// Use in function parameters
function processUser({ name, age, ...details }) {
    console.log(`User: ${name}, Age: ${age}`);
    console.log('Details:', details);
}

processUser(person);
```

### Async Iterators
```javascript
// Async iterators
async function* asyncGenerator() {
    yield await Promise.resolve(1);
    yield await Promise.resolve(2);
    yield await Promise.resolve(3);
}

const asyncGen = asyncGenerator();

// Using for-await-of
async function processAsyncGenerator() {
    for await (const value of asyncGen) {
        console.log(value); // 1, 2, 3
    }
}

processAsyncGenerator();

// Async iterator protocol
const asyncIterable = {
    [Symbol.asyncIterator]() {
        let i = 0;
        return {
            async next() {
                if (i < 3) {
                    return { value: await Promise.resolve(i++), done: false };
                }
                return { done: true };
            }
        };
    }
};

async function processAsyncIterable() {
    for await (const value of asyncIterable) {
        console.log(value); // 0, 1, 2
    }
}
```

### Promise.prototype.finally()
```javascript
// Promise.prototype.finally()
function fetchData() {
    return fetch('https://api.example.com/data')
        .then(response => response.json())
        .catch(error => {
            console.error('Fetch error:', error);
            throw error;
        })
        .finally(() => {
            console.log('Fetch completed (success or failure)');
        });
}

// Always executes, regardless of promise resolution
Promise.resolve('success')
    .then(value => console.log(value))
    .finally(() => console.log('Cleanup'));

Promise.reject('error')
    .catch(error => console.error(error))
    .finally(() => console.log('Cleanup'));
```

## ECMAScript 2019 (ES10)

### Array.prototype.flat()
```javascript
// Array.prototype.flat()
const nested = [1, [2, [3, [4, 5]]]];
console.log(nested.flat()); // [1, 2, [3, [4, 5]]]
console.log(nested.flat(2)); // [1, 2, 3, [4, 5]]
console.log(nested.flat(Infinity)); // [1, 2, 3, 4, 5]

// Remove empty slots
const sparse = [1, 2, , 4];
console.log(sparse.flat()); // [1, 2, 4]
```

### Array.prototype.flatMap()
```javascript
// Array.prototype.flatMap()
const numbers = [1, 2, 3, 4];

// Map then flatten
const doubled = numbers.flatMap(x => [x, x * 2]);
console.log(doubled); // [1, 2, 2, 4, 3, 6, 4, 8]

// Alternative to map + flat
const result = numbers
    .map(x => [x, x * 2])
    .flat();
console.log(result); // [1, 2, 2, 4, 3, 6, 4, 8]
```

### Object.fromEntries()
```javascript
// Object.fromEntries()
const entries = [
    ['name', 'John'],
    ['age', 30],
    ['city', 'New York']
];

const obj = Object.fromEntries(entries);
console.log(obj); // { name: "John", age: 30, city: "New York" }

// Convert Map to Object
const map = new Map([
    ['key1', 'value1'],
    ['key2', 'value2']
]);

const mapObj = Object.fromEntries(map);
console.log(mapObj); // { key1: "value1", key2: "value2" }
```

### String.prototype.trimStart() / trimEnd()
```javascript
// String.prototype.trimStart() and trimEnd()
const str = "   Hello, World!   ";

console.log(str.trimStart()); // "Hello, World!   "
console.log(str.trimEnd()); // "   Hello, World!"

// Alias methods
console.log(str.trimLeft()); // "Hello, World!   "
console.log(str.trimRight()); // "   Hello, World!"
```

### Optional Catch Binding
```javascript
// Optional catch binding (no need for error parameter)
try {
    const result = riskyOperation();
    console.log(result);
} catch {
    console.log('An error occurred');
}

function riskyOperation() {
    throw new Error('Something went wrong');
}
```

## ECMAScript 2020 (ES11)

### BigInt
```javascript
// BigInt for large integers
const bigNumber = 9007199254740991n;
const anotherBigNumber = BigInt("9007199254740992");

console.log(bigNumber + anotherBigNumber); // 18014398509481983n

// Operations with BigInt
console.log(10n ** 100n); // Very large number

// Cannot mix with regular numbers
// console.log(10n + 5); // TypeError
console.log(10n + BigInt(5)); // 15n

// Check if value is BigInt
console.log(typeof bigNumber); // "bigint"
console.log(typeof 10); // "number"
```

### Nullish Coalescing Operator
```javascript
// Nullish coalescing operator (??)
const value = null ?? 'default';
console.log(value); // 'default'

const value2 = undefined ?? 'default';
console.log(value2); // 'default'

const value3 = 0 ?? 'default';
console.log(value3); // 0 (only null/undefined trigger default)

const value4 = '' ?? 'default';
console.log(value4); // '' (only null/undefined trigger default)

// Comparison with || operator
const falsyValue = 0;
const result1 = falsyValue || 'default'; // 'default'
const result2 = falsyValue ?? 'default'; // 0

// Practical usage
function getConfig(config) {
    return {
        timeout: config.timeout ?? 5000,
        retries: config.retries ?? 3,
        debug: config.debug ?? false
    };
}
```

### Optional Chaining
```javascript
// Optional chaining (?.)
const user = {
    name: "John",
    address: {
        street: "123 Main St",
        city: "New York"
    }
};

console.log(user.address?.city); // "New York"
console.log(user.profile?.name); // undefined
console.log(user.profile?.address?.city); // undefined

// Method chaining
console.log(user.getName?.()); // undefined if getName doesn't exist

// Array indexing
const data = [null, undefined, { value: 1 }];
console.log(data[0]?.value); // undefined
console.log(data[2]?.value); // 1

// Practical usage
function getUserName(user) {
    return user?.profile?.name ?? 'Unknown';
}
```

### Promise.allSettled()
```javascript
// Promise.allSettled()
const promises = [
    Promise.resolve(1),
    Promise.reject('error'),
    Promise.resolve(3)
];

Promise.allSettled(promises)
    .then(results => {
        results.forEach(result => {
            if (result.status === 'fulfilled') {
                console.log('Success:', result.value);
            } else {
                console.log('Rejected:', result.reason);
            }
        });
    });

// Comparison with Promise.all
Promise.all(promises)
    .then(results => console.log(results))
    .catch(error => console.error(error)); // Fails if any promise rejects
```

### String.prototype.matchAll()
```javascript
// String.prototype.matchAll()
const text = "test1 test2 test3";
const regex = /test(\d+)/g;

const matches = text.matchAll(regex);
for (const match of matches) {
    console.log(match[0]); // "test1", "test2", "test3"
    console.log(match[1]); // "1", "2", "3"
    console.log(match.index); // 0, 6, 12
}

// Returns iterator, not array
const matchArray = Array.from(text.matchAll(regex));
console.log(matchArray);
```

### globalThis
```javascript
// globalThis - standard way to access global object
console.log(globalThis); // global object (window in browsers, global in Node.js)

// Works across environments
function getGlobal() {
    return globalThis;
}
```

## ECMAScript 2021 (ES12)

### String.prototype.replaceAll()
```javascript
// String.prototype.replaceAll()
const message = "Hello World! Hello Universe!";
const replaced = message.replaceAll("Hello", "Hi");
console.log(replaced); // "Hi World! Hi Universe!"

// With regex (global flag required)
const replacedRegex = message.replaceAll(/Hello/g, "Hi");
console.log(replaced); // "Hi World! Hi Universe!"

// Comparison with replace
const singleReplace = message.replace("Hello", "Hi");
console.log(singleReplace); // "Hi World! Hello Universe!"
```

### Logical Assignment Operators
```javascript
// Logical assignment operators
let x = null;
x ||= 'default';
console.log(x); // 'default'

let y = 'initial';
y ||= 'default';
console.log(y); // 'initial' (only assigns if falsy)

// Logical AND assignment
let a = null;
a &&= 'value';
console.log(a); // null

a = 'initial';
a &&= 'new value';
console.log(a); // 'new value'

// Logical nullish assignment
let b = null;
b ??= 'default';
console.log(b); // 'default'

let c = 0;
c ??= 'default';
console.log(c); // 0 (only assigns if null/undefined)
```

### Numeric Separators
```javascript
// Numeric separators for readability
const billion = 1_000_000_000;
const hexColor = 0xFF_00_FF;
const binary = 0b1010_1010_1010_1010;

console.log(billion); // 1000000000
console.log(hexColor); // 16711680
console.log(binary); // 43690
```

### Promise.any()
```javascript
// Promise.any() - first resolved promise wins
const promises = [
    Promise.reject('error1'),
    Promise.reject('error2'),
    Promise.resolve('success'),
    Promise.reject('error3')
];

Promise.any(promises)
    .then(result => console.log(result)) // "success"
    .catch(error => console.error(error));

// Comparison with Promise.race
Promise.race(promises)
    .then(result => console.log(result)) // "error1" (first to settle)
    .catch(error => console.error(error));
```

## ECMAScript 2022 (ES13)

### Object.hasOwn()
```javascript
// Object.hasOwn() - recommended over hasOwnProperty()
const obj = { name: "John", age: 30 };

console.log(Object.hasOwn(obj, 'name')); // true
console.log(Object.hasOwn(obj, 'toString')); // false

// Comparison with hasOwnProperty
console.log(obj.hasOwnProperty('name')); // true
console.log(obj.hasOwnProperty('toString')); // false

// Works with objects created with Object.create(null)
const nullObj = Object.create(null);
nullObj.prop = 'value';
console.log(Object.hasOwn(nullObj, 'prop')); // true
// console.log(nullObj.hasOwnProperty('prop')); // TypeError
```

### Error Cause
```javascript
// Error cause for better error handling
try {
    throw new Error('Database connection failed', {
        cause: { code: 'DB_ERROR', message: 'Connection timeout' }
    });
} catch (error) {
    console.error(error.message); // "Database connection failed"
    console.error(error.cause); // { code: 'DB_ERROR', message: 'Connection timeout' }
}

// Creating custom errors with cause
class CustomError extends Error {
    constructor(message, options = {}) {
        super(message, options);
        this.name = 'CustomError';
    }
}

throw new CustomError('Something went wrong', { cause: 'Detailed error info' });
```

### Array.findLast() and findLastIndex()
```javascript
// Array.findLast() - find last matching element
const numbers = [1, 2, 3, 4, 5];
const lastEven = numbers.findLast(x => x % 2 === 0);
console.log(lastEven); // 4

// Array.findLastIndex() - find index of last matching element
const lastEvenIndex = numbers.findLastIndex(x => x % 2 === 0);
console.log(lastEvenIndex); // 3

// Works with complex objects
const users = [
    { id: 1, name: 'John', active: true },
    { id: 2, name: 'Jane', active: false },
    { id: 3, name: 'Bob', active: true }
];

const lastActiveUser = users.findLast(user => user.active);
console.log(lastActiveUser); // { id: 3, name: 'Bob', active: true }
```

## ECMAScript 2023 (ES14)

### Array.prototype.toReversed()
```javascript
// Array.prototype.toReversed() - returns new reversed array
const numbers = [1, 2, 3, 4, 5];
const reversed = numbers.toReversed();

console.log(reversed); // [5, 4, 3, 2, 1]
console.log(numbers); // [1, 2, 3, 4, 5] (original unchanged)

// Comparison with reverse()
const original = [1, 2, 3];
original.reverse();
console.log(original); // [3, 2, 1] (mutated)
```

### Array.prototype.toSorted()
```javascript
// Array.prototype.toSorted() - returns new sorted array
const unsorted = [3, 1, 4, 2, 5];
const sorted = unsorted.toSorted();

console.log(sorted); // [1, 2, 3, 4, 5]
console.log(unsorted); // [3, 1, 4, 2, 5] (original unchanged)

// With compare function
const people = [
    { name: 'John', age: 30 },
    { name: 'Jane', age: 25 },
    { name: 'Bob', age: 35 }
];

const sortedByAge = people.toSorted((a, b) => a.age - b.age);
console.log(sortedByAge);
```

### Array.prototype.toSpliced()
```javascript
// Array.prototype.toSpliced() - returns new array with splicing
const original = [1, 2, 3, 4, 5];
const spliced = original.toSpliced(1, 2, 99, 100);

console.log(spliced); // [1, 99, 100, 4, 5]
console.log(original); // [1, 2, 3, 4, 5] (original unchanged)

// Comparison with splice()
const mutable = [1, 2, 3, 4, 5];
mutable.splice(1, 2, 99, 100);
console.log(mutable); // [1, 99, 100, 4, 5] (mutated)
```

### WeakMap enhancements
```javascript
// WeakMap now supports using symbols as keys
const weakMap = new WeakMap();
const symbolKey = Symbol('key');
const obj = { value: 'data' };

weakMap.set(symbolKey, obj);
console.log(weakMap.get(symbolKey)); // { value: 'data' }
```

## Modern JavaScript Features

### Practical Examples
```javascript
// Modern JavaScript function using multiple ES6+ features
const processUserData = async (userId) => {
    try {
        // Destructuring with default values
        const {
            data: { user, posts = [] } = {},
            error = null
        } = await Promise.allSettled([
            fetchUser(userId),
            fetchUserPosts(userId)
        ]);

        // Nullish coalescing and optional chaining
        const userName = user?.name ?? 'Unknown';
        const postCount = posts?.length ?? 0;

        // Template literals
        const message = `User ${userName} has ${postCount} posts`;

        // Object spread
        return {
            userName,
            postCount,
            message,
            timestamp: new Date().toISOString()
        };

    } catch (error) {
        // Error with cause
        throw new Error('Failed to process user data', { cause: error });
    }
};

// Helper functions
async function fetchUser(id) {
    return Promise.resolve({ id, name: 'John' });
}

async function fetchUserPosts(id) {
    return Promise.resolve([{ title: 'Post 1' }, { title: 'Post 2' }]);
}
```

### Feature Detection
```javascript
// Feature detection for modern JavaScript features
const features = {
    arrowFunctions: (() => { try { eval('() => {}'); return true; } catch { return false; } })(),
    destructuring: (() => { try { eval('const {a} = {a: 1}'); return true; } catch { return false; } })(),
    asyncAwait: (() => { try { eval('async () => {}'); return true; } catch { return false; } })(),
    optionalChaining: (() => { try { eval('const obj = {}; obj?.prop'); return true; } catch { return false; } })(),
    nullishCoalescing: (() => { try { eval('const x = null ?? "default";'); return true; } catch { return false; } })()
};

console.log('Feature support:', features);
```

## Best Practices

### Modern JavaScript Best Practices
```javascript
// 1. Use const and let instead of var
const API_URL = 'https://api.example.com';
let currentPage = 1;

// 2. Use arrow functions for short callbacks
const numbers = [1, 2, 3];
const doubled = numbers.map(x => x * 2);

// 3. Use destructuring for cleaner code
function processUser({ name, age, city = 'Unknown' }) {
    return { name, age, city };
}

// 4. Use template literals for strings
const greeting = `Hello, ${name}!`;

// 5. Use default parameters
function createButton(text = 'Click me', type = 'button') {
    return `<${type}>${text}</${type}>`;
}

// 6. Use async/await for asynchronous code
async function fetchData() {
    try {
        const response = await fetch(API_URL);
        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// 7. Use optional chaining and nullish coalescing
const userName = user?.profile?.name ?? 'Unknown';

// 8. Use array methods for data transformation
const activeUsers = users
    .filter(user => user.active)
    .map(user => ({ ...user, displayName: user.name.toUpperCase() }));

// 9. Use classes for object-oriented code
class UserManager {
    constructor(apiUrl) {
        this.apiUrl = apiUrl;
        this.users = new Map();
    }

    async loadUsers() {
        const users = await this.fetchUsers();
        users.forEach(user => this.users.set(user.id, user));
    }

    getUser(id) {
        return this.users.get(id);
    }
}

// 10. Use modules for code organization
// math.js
export const PI = 3.14159;
export function add(a, b) { return a + b; }

// main.js
import { PI, add } from './math.js';
```

## Summary

JavaScript ES6+ features provide powerful modern capabilities:

**ES6 (2015):**
- `let` and `const` for block scoping
- Arrow functions for concise syntax
- Template literals for string interpolation
- Destructuring for clean data extraction
- Default parameters and rest/spread operators
- Classes for object-oriented programming
- Modules for code organization
- Symbols for unique identifiers

**ES7 (2016):**
- Exponentiation operator (`**`)
- `Array.prototype.includes()`

**ES8 (2017):**
- Async/await for readable async code
- `Object.values()` and `Object.entries()`
- String padding methods
- `Object.getOwnPropertyDescriptors()`

**ES9 (2018):**
- Object rest properties
- Async iterators
- `Promise.prototype.finally()`

**ES10 (2019):**
- `Array.prototype.flat()` and `flatMap()`
- `Object.fromEntries()`
- String trim methods
- Optional catch binding

**ES11 (2020):**
- BigInt for large integers
- Nullish coalescing (`??`)
- Optional chaining (`?.`)
- `Promise.allSettled()`
- `String.prototype.matchAll()`
- `globalThis`

**ES12 (2021):**
- `String.prototype.replaceAll()`
- Logical assignment operators
- Numeric separators
- `Promise.any()`

**ES13 (2022):**
- `Object.hasOwn()`
- Error cause
- `Array.findLast()` and `findLastIndex()`

**ES14 (2023):**
- `Array.prototype.toReversed()`
- `Array.prototype.toSorted()`
- `Array.prototype.toSpliced()`

These features enable writing cleaner, more maintainable, and more powerful JavaScript code.
