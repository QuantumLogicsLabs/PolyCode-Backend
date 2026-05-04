# Polymorphism in C++

---

## What Does Polymorphism Mean?

The word "polymorphism" sounds scary but it just means **"many forms"**.

In C++, polymorphism means: **one thing behaving differently depending on the situation.**

A simple real-life example: The word "open" means different things in different situations:
- Open a **door** → push it
- Open a **file** → click it
- Open a **bank account** → fill a form

Same word, different behavior. That's polymorphism.

In C++, there are two types:

| Type | When is it decided? | How? |
|---|---|---|
| **Compile-time** (Static) | Before the program runs | Function overloading, templates |
| **Runtime** (Dynamic) | While the program is running | Virtual functions |

---

## Part 1 — Compile-Time Polymorphism

### 1.1 Function Overloading

You can have **multiple functions with the same name** as long as they take different parameters. The compiler picks the right one based on what you pass in.

```cpp
#include <iostream>
using namespace std;

// Same function name "add" — three different versions
int add(int a, int b) {
    return a + b;
}

double add(double a, double b) {
    return a + b;
}

int add(int a, int b, int c) {
    return a + b + c;
}

int main() {
    cout << add(2, 3) << "\n";          // calls int version → 5
    cout << add(2.5, 1.5) << "\n";      // calls double version → 4.0
    cout << add(1, 2, 3) << "\n";       // calls 3-param version → 6
}
```

The compiler looks at what you're passing in and chooses the right `add()`. You don't have to think about it — it just works.

---

### 1.2 Operator Overloading

You can also teach C++ what `+`, `-`, `==`, etc. mean for your own classes.

Imagine you have a `Point` class. What should `point1 + point2` do? By default, C++ doesn't know. You can tell it:

```cpp
#include <iostream>
using namespace std;

class Point {
public:
    int x, y;

    Point(int x, int y) : x(x), y(y) {}

    // Teach C++ what "+" means for two Points
    Point operator+(const Point& other) {
        return Point(x + other.x, y + other.y);
    }

    // Teach C++ what "==" means for two Points
    bool operator==(const Point& other) {
        return x == other.x && y == other.y;
    }

    void print() {
        cout << "(" << x << ", " << y << ")\n";
    }
};

int main() {
    Point p1(1, 2);
    Point p2(3, 4);

    Point p3 = p1 + p2;   // Uses our operator+
    p3.print();            // (4, 6)

    cout << (p1 == p2) << "\n";   // 0 (false)
    cout << (p1 == p1) << "\n";   // 1 (true)
}
```

Now `+` works naturally with your own class, just like it does with `int` or `double`.

---

## Part 2 — Runtime Polymorphism

This is the most powerful and most important type of polymorphism in OOP.

### The Big Idea

You have many different types of objects (Dog, Cat, Bird). You store them all as `Animal*` pointers. When you call `speak()`, each one does its own thing — **automatically**, without you needing to check the type.

```cpp
#include <iostream>
#include <string>
using namespace std;

class Animal {
public:
    string name;

    Animal(string n) : name(n) {}

    virtual void speak() = 0;   // every animal must speak somehow
    virtual void move() {
        cout << name << " is moving\n";   // default movement
    }

    virtual ~Animal() {}
};

class Dog : public Animal {
public:
    Dog(string n) : Animal(n) {}

    void speak() override {
        cout << name << " says: Woof!\n";
    }

    void move() override {
        cout << name << " runs on 4 legs\n";
    }
};

class Cat : public Animal {
public:
    Cat(string n) : Animal(n) {}

    void speak() override {
        cout << name << " says: Meow!\n";
    }
};

class Bird : public Animal {
public:
    Bird(string n) : Animal(n) {}

    void speak() override {
        cout << name << " says: Tweet!\n";
    }

    void move() override {
        cout << name << " flies with wings\n";
    }
};

// This function works with ANY animal — past, present, future
void introduceAnimal(Animal* a) {
    a->speak();
    a->move();
    cout << "---\n";
}

int main() {
    Dog d("Rex");
    Cat c("Whiskers");
    Bird b("Tweety");

    // We treat them all as Animal* — same interface!
    Animal* animals[] = { &d, &c, &b };

    for (int i = 0; i < 3; i++) {
        introduceAnimal(animals[i]);
    }
}
```

**Output:**
```
Rex says: Woof!
Rex runs on 4 legs
---
Whiskers says: Meow!
Whiskers is moving
---
Tweety says: Tweet!
Tweety flies with wings
---
```

The function `introduceAnimal()` doesn't know or care whether it's dealing with a Dog, Cat, or Bird. It just calls `speak()` and `move()` — and C++ figures out the right version at runtime.

This is the heart of runtime polymorphism.

---

### Why Is This So Useful?

Without polymorphism, you'd have to write code like this:

```cpp
// Ugly — you have to check the type manually
if (type == "Dog") {
    dogSpeak();
} else if (type == "Cat") {
    catSpeak();
} else if (type == "Bird") {
    birdSpeak();
}
// And every time you add a new animal, you must update this code!
```

With polymorphism, you just call `animal->speak()` and it works for every type — even types you add later. You never need to update the calling code.

---

### Adding a New Type Without Changing Anything

Here's the power. Let's say later you add a `Snake`:

```cpp
class Snake : public Animal {
public:
    Snake(string n) : Animal(n) {}

    void speak() override {
        cout << name << " says: Hisssss!\n";
    }

    void move() override {
        cout << name << " slithers on the ground\n";
    }
};
```

Your `introduceAnimal()` function works **immediately** with `Snake` — zero changes needed. That's the beauty of polymorphism.

---

## A Real-World Style Example — Notification System

Let's say you're building an app that sends notifications. You want to support email, SMS, and push notifications — all with the same interface.

```cpp
#include <iostream>
#include <string>
using namespace std;

class Notification {
public:
    virtual void send(string message) = 0;
    virtual string getType() = 0;
    virtual ~Notification() {}
};

class EmailNotification : public Notification {
    string email;
public:
    EmailNotification(string e) : email(e) {}

    void send(string message) override {
        cout << "[EMAIL to " << email << "]: " << message << "\n";
    }

    string getType() override { return "Email"; }
};

class SMSNotification : public Notification {
    string phone;
public:
    SMSNotification(string p) : phone(p) {}

    void send(string message) override {
        cout << "[SMS to " << phone << "]: " << message << "\n";
    }

    string getType() override { return "SMS"; }
};

class PushNotification : public Notification {
    string deviceId;
public:
    PushNotification(string id) : deviceId(id) {}

    void send(string message) override {
        cout << "[PUSH to device " << deviceId << "]: " << message << "\n";
    }

    string getType() override { return "Push"; }
};

// Works with any notification type
void notifyUser(Notification* n, string msg) {
    cout << "Sending via " << n->getType() << "...\n";
    n->send(msg);
}

int main() {
    EmailNotification email("user@gmail.com");
    SMSNotification sms("+923001234567");
    PushNotification push("device_abc123");

    notifyUser(&email, "Your order has been placed!");
    notifyUser(&sms,   "Your OTP is 4829");
    notifyUser(&push,  "You have a new message");
}
```

**Output:**
```
Sending via Email...
[EMAIL to user@gmail.com]: Your order has been placed!
Sending via SMS...
[SMS to +923001234567]: Your OTP is 4829
Sending via Push...
[PUSH to device device_abc123]: You have a new message
```

Tomorrow you can add `WhatsAppNotification` and the `notifyUser()` function needs **zero changes**.

---

## Summary — Both Types at a Glance

```
Polymorphism
│
├── Compile-Time (decided before running)
│   ├── Function overloading  →  same name, different parameters
│   └── Operator overloading  →  custom meaning for +, ==, << etc.
│
└── Runtime (decided while running)
    ├── virtual functions      →  right version picked automatically
    └── abstract classes       →  enforce that child classes implement methods
```

---

## Practice Problems

### Problem 1
Create an abstract class `Shape` with pure virtual methods `area()` and `draw()`. Make three shapes: `Circle`, `Rectangle`, and `Triangle`. Store all three in an array of `Shape*` pointers. Write a loop that calls `draw()` and prints `area()` for each — without any if/else or type checking.

---

### Problem 2
Overload the `+` operator for a class `Money` that has `amount` (double) and `currency` (string). Adding two `Money` objects should add their amounts (assume same currency). Also overload `<<` so you can print a `Money` object nicely (e.g., `PKR 500.00`). Demonstrate both in `main()`.

---

### Problem 3
Build a simple payment system. Create an abstract class `PaymentMethod` with pure virtual methods `pay(double amount)` and `getMethodName()`. Derive `CashPayment`, `CardPayment`, and `WalletPayment`. Write a `checkout()` function that accepts any `PaymentMethod*` and processes the payment. Test all three types from `main()` without changing the `checkout()` function between tests.
