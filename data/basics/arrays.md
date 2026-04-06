# JavaScript Arrays

## Creating Arrays

### Array Literal Syntax
```javascript
// Empty array
const emptyArray = [];

// Array with elements
const fruits = ["apple", "banana", "orange"];
console.log(fruits);

// Mixed type array
const mixedArray = [1, "hello", true, null, undefined];
console.log(mixedArray);

// Array with trailing comma
const numbers = [1, 2, 3, 4, 5,];
console.log(numbers);
```

### Array Constructor
```javascript
// Using Array constructor
const array1 = new Array();
const array2 = new Array(3); // Creates array with 3 empty slots
const array3 = new Array("red", "green", "blue");

console.log(array1);
console.log(array2);
console.log(array3);

// Array.of() (ES6)
const arrayOf = Array.of(1, 2, 3);
console.log(arrayOf);

// Array.from() (ES6)
const arrayFrom = Array.from("hello"); // ["h", "e", "l", "l", "o"]
console.log(arrayFrom);

const arrayFromSet = Array.from(new Set([1, 2, 3])); // [1, 2, 3]
console.log(arrayFromSet);
```

## Accessing Array Elements

### Index Access
```javascript
const colors = ["red", "green", "blue", "yellow"];

// Access by index (0-based)
console.log(colors[0]); // "red"
console.log(colors[2]); // "blue"

// Access last element
console.log(colors[colors.length - 1]); // "yellow"

// Access out of bounds
console.log(colors[10]); // undefined
```

### Array Length
```javascript
const items = [1, 2, 3, 4, 5];
console.log(items.length); // 5

// Length can be modified
items.length = 10;
console.log(items.length); // 10
console.log(items); // [1, 2, 3, 4, 5, <5 empty items>]

// Truncate array
items.length = 3;
console.log(items); // [1, 2, 3]
```

## Modifying Arrays

### Adding Elements
```javascript
const languages = ["JavaScript", "Python"];

// Push to end
languages.push("Java");
console.log(languages); // ["JavaScript", "Python", "Java"]

// Push multiple elements
languages.push("C++", "Ruby");
console.log(languages); // ["JavaScript", "Python", "Java", "C++", "Ruby"]

// Unshift to beginning
languages.unshift("Go");
console.log(languages); // ["Go", "JavaScript", "Python", "Java", "C++", "Ruby"]

// Spread operator (ES6)
const newLanguages = ["Rust", "Swift"];
const allLanguages = [...newLanguages, ...languages];
console.log(allLanguages);
```

### Removing Elements
```javascript
const numbers = [1, 2, 3, 4, 5];

// Pop from end
const lastElement = numbers.pop();
console.log(lastElement); // 5
console.log(numbers); // [1, 2, 3, 4]

// Shift from beginning
const firstElement = numbers.shift();
console.log(firstElement); // 1
console.log(numbers); // [2, 3, 4]

// Splice (remove by index)
const removed = numbers.splice(1, 1); // Remove 1 element at index 1
console.log(removed); // [3]
console.log(numbers); // [2, 4]
```

### Updating Elements
```javascript
let mutable = [1, 2, 3];

// Update by index
mutable[1] = 20;
console.log(mutable); // [1, 20, 3]

// Update multiple elements
mutable[0] = 10;
mutable[2] = 30;
console.log(mutable); // [10, 20, 30]

// Spread operator for updates
const updated = [...mutable.slice(0, 1), 25, ...mutable.slice(2)];
console.log(updated); // [10, 25, 30]
```

## Array Methods

### Iteration Methods
```javascript
const numbers = [1, 2, 3, 4, 5];

// forEach()
numbers.forEach((element, index) => {
    console.log(`Index ${index}: ${element}`);
});

// map() - transform elements
const doubled = numbers.map(x => x * 2);
console.log(doubled); // [2, 4, 6, 8, 10]

// filter() - select elements
const evens = numbers.filter(x => x % 2 === 0);
console.log(evens); // [2, 4]

// find() - find first matching element
const found = numbers.find(x => x > 3);
console.log(found); // 4

// findIndex() - find index of first matching element
const foundIndex = numbers.findIndex(x => x > 3);
console.log(foundIndex); // 3
```

### Reduction Methods
```javascript
const numbers = [1, 2, 3, 4, 5];

// reduce() - reduce to single value
const sum = numbers.reduce((acc, curr) => acc + curr, 0);
console.log(sum); // 15

// reduceRight() - reduce from right
const concatenated = numbers.reduceRight((acc, curr) => curr + acc, "");
console.log(concatenated); // "54321"

// every() - check if all elements pass test
const allPositive = numbers.every(x => x > 0);
console.log(allPositive); // true

// some() - check if any element passes test
const hasEven = numbers.some(x => x % 2 === 0);
console.log(hasEven); // true
```

### Searching Methods
```javascript
const fruits = ["apple", "banana", "orange", "grape", "apple"];

// indexOf() - first occurrence
const firstApple = fruits.indexOf("apple");
console.log(firstApple); // 0

// lastIndexOf() - last occurrence
const lastApple = fruits.lastIndexOf("apple");
console.log(lastApple); // 4

// includes() - check if element exists
const hasBanana = fruits.includes("banana");
console.log(hasBanana); // true

// find() - find by condition
const largeFruit = fruits.find(fruit => fruit.length > 5);
console.log(largeFruit); // "banana"
```

### Transformation Methods
```javascript
const numbers = [1, 2, 3, 4, 5];

// slice() - extract portion
const slice1 = numbers.slice(1, 3);
console.log(slice1); // [2, 3]

const slice2 = numbers.slice(-2);
console.log(slice2); // [4, 5]

// splice() - remove/replace elements
const spliced = numbers.splice(1, 2, 20, 30);
console.log(numbers); // [1, 20, 30, 4, 5]
console.log(spliced); // [2, 3]

// concat() - join arrays
const arr1 = [1, 2];
const arr2 = [3, 4];
const combined = arr1.concat(arr2);
console.log(combined); // [1, 2, 3, 4]

// join() - convert to string
const joined = numbers.join(" - ");
console.log(joined); // "1 - 20 - 30 - 4 - 5"
```

### Sorting Methods
```javascript
const fruits = ["banana", "apple", "orange", "grape"];

// sort() - sorts in place
fruits.sort();
console.log(fruits); // ["apple", "banana", "grape", "orange"]

// sort with compare function
const numbers = [1, 10, 2, 20, 3];
numbers.sort((a, b) => a - b);
console.log(numbers); // [1, 2, 3, 10, 20]

// reverse()
fruits.reverse();
console.log(fruits); // ["orange", "grape", "banana", "apple"]

// toSorted() (ES2023) - returns new sorted array
const unsorted = [3, 1, 4, 2];
const sorted = unsorted.toSorted();
console.log(sorted); // [1, 2, 3, 4]
console.log(unsorted); // [3, 1, 4, 2]
```

## Array Properties

### length Property
```javascript
const arr = [1, 2, 3];
console.log(arr.length); // 3

// Modifying length
arr.length = 5;
console.log(arr.length); // 5
console.log(arr); // [1, 2, 3, <2 empty items>]

arr.length = 1;
console.log(arr); // [1]
```

### Prototype Methods
```javascript
const arr = [1, 2, 3];

// Check if array
console.log(Array.isArray(arr)); // true
console.log(Array.isArray("array")); // false

// toString()
console.log(arr.toString()); // "1,2,3"

// valueOf()
console.log(arr.valueOf()); // [1, 2, 3]
```

## Multidimensional Arrays

### 2D Arrays
```javascript
// Create 2D array
const matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

// Access elements
console.log(matrix[0][0]); // 1
console.log(matrix[1][2]); // 6

// Modify elements
matrix[2][1] = 80;
console.log(matrix[2]); // [7, 80, 9]

// Iterate 2D array
for (let i = 0; i < matrix.length; i++) {
    for (let j = 0; j < matrix[i].length; j++) {
        console.log(`matrix[${i}][${j}] = ${matrix[i][j]}`);
    }
}

// Flatten 2D array
const flat = matrix.flat();
console.log(flat); // [1, 2, 3, 4, 5, 6, 7, 80, 9]

// flatMap() (ES2019)
const doubled = matrix.flatMap(row => row.map(x => x * 2));
console.log(doubled); // [2, 4, 6, 8, 10, 12, 14, 160, 18]
```

### Jagged Arrays
```javascript
// Jagged array (different row lengths)
const jagged = [
    [1, 2],
    [3, 4, 5],
    [6],
    [7, 8, 9, 10]
];

console.log(jagged[0].length); // 2
console.log(jagged[1].length); // 3
console.log(jagged[2].length); // 1
console.log(jagged[3].length); // 4

// Iterate jagged array
jagged.forEach((row, i) => {
    console.log(`Row ${i}:`, row);
});
```

## Array Destructuring

### Basic Destructuring
```javascript
const colors = ["red", "green", "blue"];

// Destructure array
const [first, second, third] = colors;
console.log(first); // "red"
console.log(second); // "green"
console.log(third); // "blue"

// Skip elements
const [primary, , secondary] = colors;
console.log(primary); // "red"
console.log(secondary); // "blue"
```

### Rest Operator in Destructuring
```javascript
const numbers = [1, 2, 3, 4, 5];

// Rest operator
const [first, ...rest] = numbers;
console.log(first); // 1
console.log(rest); // [2, 3, 4, 5]

// Last element with rest
const [...allButLast, last] = numbers;
console.log(allButLast); // [1, 2, 3, 4]
console.log(last); // 5
```

### Swapping Variables
```javascript
let a = 1, b = 2;

// Swap with destructuring
[a, b] = [b, a];
console.log(a); // 2
console.log(b); // 1
```

## Array Performance

### Performance Considerations
```javascript
// Performance test: push vs unshift
const largeArray = [];

console.time("push");
for (let i = 0; i < 10000; i++) {
    largeArray.push(i);
}
console.timeEnd("push");

console.time("unshift");
for (let i = 0; i < 10000; i++) {
    largeArray.unshift(i);
}
console.timeEnd("unshift");

// Performance test: for vs forEach
const testArray = Array.from({ length: 10000 }, (_, i) => i);

console.time("for loop");
let sum1 = 0;
for (let i = 0; i < testArray.length; i++) {
    sum1 += testArray[i];
}
console.timeEnd("for loop");

console.time("forEach");
let sum2 = 0;
testArray.forEach(x => sum2 += x);
console.timeEnd("forEach");
```

### Memory Efficiency
```javascript
// Sparse arrays
const sparseArray = [];
sparseArray[1000] = "sparse";
console.log(sparseArray.length); // 1001
console.log(sparseArray); // [<1000 empty items>, "sparse"]

// Dense arrays
const denseArray = new Array(1001).fill(null);
denseArray[1000] = "dense";
console.log(denseArray.length); // 1001

// Typed arrays for performance
const int8Array = new Int8Array([1, 2, 3, 4]);
const float64Array = new Float64Array([1.1, 2.2, 3.3]);

console.log(int8Array); // Int8Array [1, 2, 3, 4]
console.log(float64Array); // Float64Array [1.1, 2.2, 3.3]
```

## Array Algorithms

### Common Algorithms
```javascript
// Bubble sort
function bubbleSort(arr) {
    const sorted = [...arr];
    
    for (let i = 0; i < sorted.length - 1; i++) {
        for (let j = 0; j < sorted.length - i - 1; j++) {
            if (sorted[j] > sorted[j + 1]) {
                [sorted[j], sorted[j + 1]] = [sorted[j + 1], sorted[j]];
            }
        }
    }
    
    return sorted;
}

// Binary search
function binarySearch(arr, target) {
    let left = 0;
    let right = arr.length - 1;
    
    while (left <= right) {
        const mid = Math.floor((left + right) / 2);
        
        if (arr[mid] === target) {
            return mid;
        } else if (arr[mid] < target) {
            left = mid + 1;
        } else {
            right = mid - 1;
        }
    }
    
    return -1; // Not found
}

// Test algorithms
const unsorted = [5, 2, 8, 1, 9];
const sorted = bubbleSort(unsorted);
console.log(sorted); // [1, 2, 5, 8, 9]

const index = binarySearch(sorted, 8);
console.log(index); // 3
```

### Unique Values
```javascript
// Remove duplicates using Set
function removeDuplicates(arr) {
    return [...new Set(arr)];
}

// Remove duplicates without Set
function removeDuplicatesManual(arr) {
    const unique = [];
    
    for (const item of arr) {
        if (!unique.includes(item)) {
            unique.push(item);
        }
    }
    
    return unique;
}

const duplicates = [1, 2, 2, 3, 3, 4, 5, 5];
console.log(removeDuplicates(duplicates)); // [1, 2, 3, 4, 5]
console.log(removeDuplicatesManual(duplicates)); // [1, 2, 3, 4, 5]
```

## Array Utilities

### Utility Functions
```javascript
// Chunk array into groups
function chunk(array, size) {
    const chunks = [];
    
    for (let i = 0; i < array.length; i += size) {
        chunks.push(array.slice(i, i + size));
    }
    
    return chunks;
}

const largeArray = [1, 2, 3, 4, 5, 6, 7, 8, 9];
console.log(chunk(largeArray, 3)); // [[1, 2, 3], [4, 5, 6], [7, 8, 9]]

// Shuffle array
function shuffle(array) {
    const shuffled = [...array];
    
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    
    return shuffled;
}

console.log(shuffle([1, 2, 3, 4, 5]));

// Check if arrays are equal
function arraysEqual(a, b) {
    if (a.length !== b.length) return false;
    
    for (let i = 0; i < a.length; i++) {
        if (a[i] !== b[i]) return false;
    }
    
    return true;
}

console.log(arraysEqual([1, 2, 3], [1, 2, 3])); // true
console.log(arraysEqual([1, 2, 3], [1, 2, 4])); // false
```

### Array Combinations
```javascript
// Cartesian product
function cartesianProduct(arrays) {
    return arrays.reduce((acc, curr) => 
        acc.flatMap(a => curr.map(b => [...a, b]))
    , [[]]);
}

const colors = ["red", "blue"];
const sizes = ["small", "large"];
const shapes = ["circle", "square"];

const combinations = cartesianProduct([colors, sizes, shapes]);
console.log(combinations);
// [["red", "small", "circle"], ["red", "small", "square"], 
//  ["red", "large", "circle"], ["red", "large", "square"],
//  ["blue", "small", "circle"], ["blue", "small", "square"],
//  ["blue", "large", "circle"], ["blue", "large", "square"]]

// Zip arrays
function zip(...arrays) {
    const length = Math.min(...arrays.map(arr => arr.length));
    
    return Array.from({ length }, (_, i) => 
        arrays.map(arr => arr[i])
    );
}

const names = ["Alice", "Bob", "Charlie"];
const ages = [25, 30, 35];
const cities = ["NYC", "LA", "Chicago"];

const zipped = zip(names, ages, cities);
console.log(zipped);
// [["Alice", 25, "NYC"], ["Bob", 30, "LA"], ["Charlie", 35, "Chicago"]]
```

## Best Practices

### Array Best Practices
```javascript
// 1. Use const for arrays that don't need reassignment
const immutableArray = [1, 2, 3];

// 2. Use descriptive names
const userNames = ["Alice", "Bob", "Charlie"];
const userAges = [25, 30, 35];

// 3. Use appropriate methods
// Bad: manual loop for filtering
const evens = [];
for (let i = 0; i < numbers.length; i++) {
    if (numbers[i] % 2 === 0) {
        evens.push(numbers[i]);
    }
}

// Good: use filter method
const evensBetter = numbers.filter(x => x % 2 === 0);

// 4. Avoid mutating original arrays when possible
const original = [1, 2, 3];
const transformed = original.map(x => x * 2);
console.log(original); // [1, 2, 3] (unchanged)

// 5. Use spread operator for copying
const copy = [...original];
copy[0] = 10;
console.log(original); // [1, 2, 3]
console.log(copy); // [10, 2, 3]

// 6. Use Array methods for common operations
// Bad: manual sum
let total = 0;
for (let i = 0; i < numbers.length; i++) {
    total += numbers[i];
}

// Good: use reduce
const totalBetter = numbers.reduce((sum, num) => sum + num, 0);

// 7. Check for array existence before using methods
function processArray(data) {
    if (!Array.isArray(data)) {
        throw new TypeError('Expected array');
    }
    
    return data.map(item => item * 2);
}

// 8. Use typed arrays for performance when appropriate
const pixels = new Uint8ClampedArray([255, 128, 0, 255]);
const coordinates = new Float32Array([1.1, 2.2, 3.3]);
```

## Common Pitfalls

### Common Array Mistakes
```javascript
// 1. Confusing indexOf with includes
const arr = [1, 2, 3];
console.log(arr.indexOf(4)); // -1 (not found)
console.log(arr.includes(4)); // false

// 2. Modifying array while iterating
const numbers = [1, 2, 3, 4, 5];
for (let i = 0; i < numbers.length; i++) {
    if (numbers[i] === 3) {
        numbers.splice(i, 1); // This can cause issues
    }
}

// Better: create new array or iterate backwards
for (let i = numbers.length - 1; i >= 0; i--) {
    if (numbers[i] === 3) {
        numbers.splice(i, 1);
        break;
    }
}

// 3. Using delete on arrays
const arr2 = [1, 2, 3];
delete arr2[1]; // Creates sparse array
console.log(arr2); // [1, <1 empty item>, 3]
console.log(arr2.length); // 3

// Better: use splice
arr2.splice(1, 1);
console.log(arr2); // [1, 3]

// 4. Comparing arrays with ===
const arr3 = [1, 2, 3];
const arr4 = [1, 2, 3];
console.log(arr3 === arr4); // false (different objects)

// Better: compare contents
function arraysEqual(a, b) {
    return a.length === b.length && a.every((val, index) => val === b[index]);
}

// 5. Forgetting that sort mutates the array
const original = [3, 1, 2];
const sorted = [...original].sort();
console.log(original); // [3, 1, 2] (mutated!)
console.log(sorted); // [1, 2, 3]

// 6. Using for...in on arrays
const arr5 = [10, 20, 30];
for (const index in arr5) {
    console.log(index); // "0", "1", "2" (strings)
    console.log(typeof index); // "string"
}

// Better: use for...of or for loop with index
for (let i = 0; i < arr5.length; i++) {
    console.log(i); // 0, 1, 2 (numbers)
}
```

## Summary

JavaScript arrays provide powerful data structures:

**Creation:**
- Array literal syntax: `[1, 2, 3]`
- Array constructor: `new Array(3)`
- ES6 methods: `Array.of()`, `Array.from()`

**Access & Modification:**
- Index-based access: `arr[0]`
- Length property: `arr.length`
- Methods: `push()`, `pop()`, `shift()`, `unshift()`, `splice()`

**Iteration:**
- `forEach()` for side effects
- `map()` for transformation
- `filter()` for selection
- `for...of` for value iteration
- `for...in` for index iteration (avoid)

**Search & Transform:**
- `find()`, `findIndex()`, `indexOf()`, `includes()`
- `slice()`, `concat()`, `join()`
- `sort()`, `reverse()`

**Reduction:**
- `reduce()`, `reduceRight()`
- `every()`, `some()`

**Modern Features:**
- Destructuring: `[a, b] = arr`
- Spread operator: `[...arr1, ...arr2]`
- `flat()`, `flatMap()`
- `toSorted()` (ES2023)

**Best Practices:**
- Use `const` for immutable arrays
- Prefer array methods over manual loops
- Avoid mutating while iterating
- Use spread operator for copying
- Choose appropriate iteration method

Arrays are fundamental JavaScript data structures that provide efficient methods for storing, accessing, and manipulating collections of data.
