/**
 * JavaScript Classes and Inheritance
 * This tutorial covers the modern ES6+ class syntax.
 */

class Animal {
    constructor(name) {
        this.name = name;
    }

    speak() {
        console.log(`${this.name} makes a noise.`);
    }
}

class Dog extends Animal {
    constructor(name, breed) {
        super(name); // Call the parent constructor
        this.breed = breed;
    }

    speak() {
        console.log(`${this.name} barks.`);
    }

    displayInfo() {
        console.log(`${this.name} is a ${this.breed}.`);
    }
}

// Creating objects
const d = new Dog('Rex', 'German Shepherd');
d.speak();
d.displayInfo();