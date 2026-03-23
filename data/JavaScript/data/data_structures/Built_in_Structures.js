/**
 * Data Structures in JavaScript
 * This tutorial covers Arrays and Objects (the primary JS structures) 
 * and modern ES6 Map and Set.
 */

// Array (Ordered List)
const fruits = ['Apple', 'Banana', 'Cherry'];
fruits.push('Date');

console.log('--- Array ---');
console.log(fruits.join(', '));

// Object (Key-Value Pairs - traditional)
const person = {
    name: 'Alice',
    age: 25
};
console.log('\n--- Object ---');
console.log(`${person.name} is ${person.age} years old.`);

// Map (Key-Value Pairs - modern)
const scores = new Map();
scores.set('Bob', 88);
scores.set('Charlie', 92);

console.log('\n--- Map ---');
for (const [name, score] of scores) {
    console.log(`${name}: ${score}`);
}

// Set (Unique Values)
const uniqueNumbers = new Set([1, 2, 2, 3, 3, 3]);
console.log('\n--- Set ---');
console.log('Unique Count:', uniqueNumbers.size);
console.log('Values:', [...uniqueNumbers]);