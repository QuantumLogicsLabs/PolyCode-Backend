# Classes and Inheritance in JavaScript

## Overview
Modern JavaScript (ES6+) introduced the `class` keyword to provide a clearer and more concise syntax for creating objects and dealing with inheritance, compared to the traditional prototype-based approach.

## Key Concepts
1.  **`class`**: Defines a new object template.
2.  **`constructor`**: A special method for creating and initializing an object instance.
3.  **`extends`**: Used to create a class that is a child of another class (inheritance).
4.  **`super()`**: Calls the constructor of the parent class.
5.  **Method Overriding**: Redefining a method in a child class that was already defined in the parent class.

## Basic Syntax
```js
class Parent {
    constructor() { /* ... */ }
    greet() { console.log('Hello'); }
}

class Child extends Parent {
    greet() { console.log('Hi from child'); }
}
```

[01) Classes and Inheritance.js](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/JavaScript/data/03)%20Object%20Oriented%20Programming/01)%20Classes%20and%20Inheritance.js)