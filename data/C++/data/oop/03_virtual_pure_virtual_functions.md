# Virtual and Pure Virtual Functions in C++

---

## The Problem We're Trying to Solve

Let's say you have a base class `Animal` and a derived class `Dog`. You override the `speak()` method in `Dog`. Now watch what happens when you use a **base class pointer**:

```cpp
#include <iostream>
using namespace std;

class Animal {
public:
    void speak() {
        cout << "Some animal sound\n";
    }
};

class Dog : public Animal {
public:
    void speak() {
        cout << "Woof!\n";
    }
};

int main() {
    Dog d;
    Animal* ptr = &d;   // pointer of type Animal, pointing to a Dog

    ptr->speak();   // Which speak() runs?
}
```

**Output:**
```
Some animal sound
```

Wait — `ptr` is pointing at a `Dog`, so why did `Animal::speak()` run?

Because C++ looks at the **type of the pointer** (`Animal*`) to decide which function to call — not the actual object. This is decided at compile time, before the program even runs.

The `virtual` keyword fixes this. It tells C++: "Don't decide at compile time — wait until the program runs and check what the actual object is."

---

## Part 1 — Virtual Functions

Just add the word `virtual` before the function in the **base class**:

```cpp
#include <iostream>
using namespace std;

class Animal {
public:
    virtual void speak() {   // <-- virtual
        cout << "Some animal sound\n";
    }

    virtual ~Animal() {}     // always make the destructor virtual too (explained below)
};

class Dog : public Animal {
public:
    void speak() override {
        cout << "Woof!\n";
    }
};

class Cat : public Animal {
public:
    void speak() override {
        cout << "Meow!\n";
    }
};

int main() {
    Dog d;
    Cat c;

    Animal* p1 = &d;
    Animal* p2 = &c;

    p1->speak();   // Woof!  -- correct!
    p2->speak();   // Meow!  -- correct!
}
```

**Output:**
```
Woof!
Meow!
```

Now C++ waits until runtime, checks that `p1` actually points to a `Dog`, and calls `Dog::speak()`. This is called **dynamic dispatch** (fancy way of saying "deciding at runtime").

---

## How Does C++ Know Which Function to Call?

Behind the scenes, every class with virtual functions has a hidden lookup table called a **vtable**. You don't write this — C++ creates it automatically.

Think of it like a restaurant menu. Every class has its own menu:

```
Animal's menu:
  speak → Animal::speak

Dog's menu:
  speak → Dog::speak    (replaced!)

Cat's menu:
  speak → Cat::speak    (replaced!)
```

When you call `ptr->speak()`, C++ opens the menu of the **actual object** (not the pointer type) and calls the right function.

---

## Why Make the Destructor Virtual?

This is a common mistake beginners make. Look at this:

```cpp
class Animal {
public:
    ~Animal() {   // NOT virtual
        cout << "Animal destroyed\n";
    }
};

class Dog : public Animal {
    int* data;
public:
    Dog() { data = new int[10]; }
    ~Dog() {
        delete[] data;   // This never runs if destructor is not virtual!
        cout << "Dog destroyed\n";
    }
};

int main() {
    Animal* ptr = new Dog();
    delete ptr;   // Only ~Animal() runs. ~Dog() is skipped. Memory leak!
}
```

If the destructor is not `virtual`, C++ looks at the pointer type (`Animal*`) and only calls `~Animal()`. The `Dog` destructor never runs — which can cause **memory leaks**.

**Fix: always make the destructor virtual in the base class:**

```cpp
virtual ~Animal() {
    cout << "Animal destroyed\n";
}
```

Now both destructors run correctly — `~Dog()` first, then `~Animal()`.

---

## Part 2 — Pure Virtual Functions

Sometimes a base class is so general that it doesn't make sense to give it a real function body. For example, what sound does a generic "Animal" make? We don't know — it depends on the specific animal.

In that case, we use a **pure virtual function**. It's declared with `= 0` at the end:

```cpp
virtual void speak() = 0;   // pure virtual — no body here
```

This means: **"Every child class MUST provide its own version of this function."**

---

## Part 3 — Abstract Classes

When a class has at least one pure virtual function, it becomes an **abstract class**. You **cannot create objects** from it directly — it's just a blueprint.

```cpp
#include <iostream>
using namespace std;

class Shape {
public:
    // Pure virtual — every shape MUST tell us its area
    virtual double area() = 0;

    // Pure virtual — every shape MUST be able to draw itself
    virtual void draw() = 0;

    virtual ~Shape() {}
};

class Circle : public Shape {
    double radius;
public:
    Circle(double r) : radius(r) {}

    double area() override {
        return 3.14 * radius * radius;
    }

    void draw() override {
        cout << "Drawing a circle with radius " << radius << "\n";
    }
};

class Rectangle : public Shape {
    double width, height;
public:
    Rectangle(double w, double h) : width(w), height(h) {}

    double area() override {
        return width * height;
    }

    void draw() override {
        cout << "Drawing a rectangle " << width << " x " << height << "\n";
    }
};

int main() {
    // Shape s;   // ERROR! Cannot create object of abstract class

    Circle c(5);
    Rectangle r(4, 6);

    c.draw();
    cout << "Area: " << c.area() << "\n";

    r.draw();
    cout << "Area: " << r.area() << "\n";
}
```

**Output:**
```
Drawing a circle with radius 5
Area: 78.5
Drawing a rectangle 4 x 6
Area: 24
```

---

## What If a Child Doesn't Implement All Pure Virtuals?

Then the child class also becomes abstract and you still can't create objects from it.

```cpp
class Shape {
public:
    virtual void draw() = 0;
    virtual double area() = 0;
};

class WeirdShape : public Shape {
public:
    void draw() override {
        cout << "Drawing something weird\n";
    }
    // area() is NOT implemented — WeirdShape is still abstract!
};

// WeirdShape ws;   // ERROR — still abstract
```

---

## Quick Summary

| Feature | What it does |
|---|---|
| `virtual void f()` | Allows child to override, uses runtime lookup |
| `virtual void f() = 0` | Pure virtual — child MUST implement it |
| Abstract class | Has at least one pure virtual — cannot create objects |
| `virtual ~Base()` | Makes sure the right destructor is called |
| `override` | Tells compiler "I'm overriding — please double-check" |

---

## Practice Problems

### Problem 1
Create an abstract class `Animal` with a pure virtual method `speak()` and a regular virtual method `move()` that prints `"Moving..."`. Derive `Dog`, `Cat`, and `Bird` from it. Each should override `speak()` with their own sound, and `Bird` should also override `move()` to print `"Flying..."`. Create objects of each and call both methods.

---

### Problem 2
Create an abstract class `Account` with a pure virtual method `calculateInterest()`. Derive `SavingsAccount` (5% interest on balance) and `FixedAccount` (8% interest on balance). Store both in `Account*` pointers and call `calculateInterest()` on each. Verify the right version runs for each.

---

### Problem 3
Create an abstract class `Employee` with pure virtual methods `getSalary()` and `getRole()`. Derive `FullTimeEmployee` (fixed monthly salary) and `FreelanceEmployee` (hourly rate × hours worked). Write a function `void printPayslip(Employee* e)` that prints the role and salary without knowing which type of employee it is. Test it with both types.
