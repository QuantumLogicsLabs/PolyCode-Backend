# Inheritance in C++

---

## What Is Inheritance?

Imagine you are writing a program about animals. You need a `Dog` class and a `Cat` class. Both animals have a name, an age, and they both eat and sleep. Would you write the same code twice?

No! That's where **inheritance** comes in.

Inheritance lets one class **get all the features of another class** and then add its own on top. The class that shares its features is called the **base class** (or parent class). The class that receives them is called the **derived class** (or child class).

**Real life analogy:** A child inherits traits from their parents (eye color, height), but also has their own unique traits (hobbies, personality).

---

## The "Is-A" Relationship

Inheritance models an **"is-a"** relationship:
- A Dog **is an** Animal ✅
- A Car **is a** Vehicle ✅
- A Mango **is a** Fruit ✅

If the "is-a" sentence sounds natural, inheritance is a good fit.

---

## Part 1 — Basic Inheritance

```cpp
#include <iostream>
#include <string>
using namespace std;

// Parent class (base class)
class Animal {
public:
    string name;
    int age;

    Animal(string n, int a) : name(n), age(a) {
        cout << "Animal created: " << name << "\n";
    }

    ~Animal() {
        cout << "Animal destroyed: " << name << "\n";
    }

    void eat() {
        cout << name << " is eating.\n";
    }

    void sleep() {
        cout << name << " is sleeping.\n";
    }
};

// Child class (derived class)
// Dog gets everything Animal has, plus its own stuff
class Dog : public Animal {
public:
    string breed;

    // We must call the parent constructor to set name and age
    Dog(string n, int a, string b) : Animal(n, a), breed(b) {
        cout << "Dog created: " << name << "\n";
    }

    ~Dog() {
        cout << "Dog destroyed: " << name << "\n";
    }

    // Dog's own method — Animal doesn't have this
    void bark() {
        cout << name << " says: Woof!\n";
    }
};

int main() {
    Dog d("Rex", 3, "Labrador");

    d.eat();     // from Animal — Dog inherited this
    d.sleep();   // from Animal — Dog inherited this
    d.bark();    // Dog's own method

    cout << "Name: " << d.name << "\n";    // from Animal
    cout << "Breed: " << d.breed << "\n";  // Dog's own
}
```

**Output:**
```
Animal created: Rex
Dog created: Rex
Rex is eating.
Rex is sleeping.
Rex says: Woof!
Name: Rex
Breed: Labrador
Dog destroyed: Rex
Animal destroyed: Rex
```

Notice the constructor and destructor order:
- **Creating:** Parent first, then child
- **Destroying:** Child first, then parent

This always happens in C++ inheritance.

---

## Part 2 — Access: What the Child Can See

Not all members of the parent are available to the child. It depends on whether they are `public`, `protected`, or `private`.

```cpp
class Animal {
private:
    int secretCode = 999;   // child CANNOT access this

protected:
    int internalAge = 5;    // child CAN access, but outsiders cannot

public:
    string name = "Generic";   // everyone can access this
};

class Dog : public Animal {
public:
    void showInfo() {
        // cout << secretCode;   // ERROR — private, cannot access
        cout << internalAge;     // OK — protected
        cout << name;            // OK — public
    }
};

int main() {
    Dog d;
    // cout << d.internalAge;   // ERROR — protected, outsiders cannot access
    cout << d.name;              // OK — public
}
```

Simple rule to remember:

| | Inside the class | Child class | Outside (main etc.) |
|---|---|---|---|
| `private` | ✅ | ❌ | ❌ |
| `protected` | ✅ | ✅ | ❌ |
| `public` | ✅ | ✅ | ✅ |

Use `protected` when you want child classes to use a member but don't want outsiders touching it.

---

## Part 3 — Overriding a Method

A child class can **replace** a parent's method with its own version. This is called **overriding**.

```cpp
#include <iostream>
using namespace std;

class Animal {
public:
    void speak() {
        cout << "Some animal sound...\n";
    }
};

class Dog : public Animal {
public:
    void speak() {   // This OVERRIDES Animal's speak()
        cout << "Woof!\n";
    }
};

class Cat : public Animal {
public:
    void speak() {   // This OVERRIDES Animal's speak()
        cout << "Meow!\n";
    }
};

int main() {
    Dog d;
    Cat c;

    d.speak();   // Woof!
    c.speak();   // Meow!
}
```

The child's version takes over when you call the method on a child object.

---

## Part 4 — Multilevel Inheritance

Inheritance can go more than one level deep. A child can itself be a parent to another class.

```cpp
#include <iostream>
using namespace std;

class Vehicle {
public:
    void move() {
        cout << "Vehicle is moving\n";
    }
};

class Car : public Vehicle {
public:
    void honk() {
        cout << "Beep beep!\n";
    }
};

class ElectricCar : public Car {
public:
    void charge() {
        cout << "Charging the battery...\n";
    }
};

int main() {
    ElectricCar ec;
    ec.move();    // from Vehicle (grandparent)
    ec.honk();    // from Car (parent)
    ec.charge();  // ElectricCar's own
}
```

`ElectricCar` inherits from `Car`, which inherits from `Vehicle`. So `ElectricCar` gets everything from both!

---

## Part 5 — Multiple Inheritance

A class can also inherit from **more than one parent**.

```cpp
#include <iostream>
using namespace std;

class Flyable {
public:
    void fly() {
        cout << "Flying high!\n";
    }
};

class Swimmable {
public:
    void swim() {
        cout << "Swimming fast!\n";
    }
};

// Duck can both fly and swim
class Duck : public Flyable, public Swimmable {
public:
    void quack() {
        cout << "Quack!\n";
    }
};

int main() {
    Duck d;
    d.fly();    // from Flyable
    d.swim();   // from Swimmable
    d.quack();  // Duck's own
}
```

---

## Part 6 — The `override` Keyword (Good Habit)

When you override a method, write the word `override` after it. This tells the compiler to check that you're actually overriding something — it catches spelling mistakes.

```cpp
class Animal {
public:
    virtual void speak() {
        cout << "...\n";
    }
};

class Dog : public Animal {
public:
    void speak() override {   // "override" checks that Animal::speak() exists
        cout << "Woof!\n";
    }
};
```

We will cover `virtual` in the next module — for now just know that `override` is a safety net.

---

## Practice Problems

### Problem 1
Create a base class `Person` with `name` and `age`. Derive a class `Student` from it that adds a `studentId` and a method `study()`. Derive another class `Teacher` that adds a `subject` and a method `teach()`. In `main()`, create one student and one teacher and call all their methods.

---

### Problem 2
Create a class `Vehicle` with a method `fuelType()` that prints `"Petrol"`. Derive a class `Car` that overrides `fuelType()` to print `"Petrol or Diesel"`. Derive another class `Bicycle` that overrides `fuelType()` to print `"No fuel needed!"`. Show the different outputs in `main()`.

---

### Problem 3
Build a 3-level chain: `LivingThing` → `Plant` → `FloweringPlant`. Each class should add one new attribute and one new method. In `main()`, create a `FloweringPlant` object and show that it can use methods from all three levels.
