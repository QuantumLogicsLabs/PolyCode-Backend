# Composition and Aggregation in C++

---

## First, Let's Understand the Idea

In real life, objects are made up of other objects. For example:
- A **Car** has an **Engine**
- A **School** has **Teachers**

In C++, we can model these "has-a" relationships in two ways:

| | Composition | Aggregation |
|---|---|---|
| Simple meaning | The part is **owned** by the whole | The part just **belongs** to the whole |
| What happens if the owner is destroyed? | The part is destroyed too | The part lives on |
| Real life example | Heart inside a Human | Teacher inside a School |

Think of it this way:
- **Composition** = If you destroy the car, the engine is gone too *(it was built into the car)*
- **Aggregation** = If the school closes, the teachers still exist *(they can go work elsewhere)*

---

## Part 1 — Composition ("The part dies with the owner")

In code, composition means the part is stored **directly inside** the owner as a member variable — not as a pointer.

```cpp
#include <iostream>
#include <string>
using namespace std;

// This is the "part"
class Engine {
public:
    int horsepower;

    Engine(int hp) : horsepower(hp) {
        cout << "Engine created (" << hp << " HP)\n";
    }

    ~Engine() {
        cout << "Engine destroyed\n";
    }

    void start() {
        cout << "Engine started!\n";
    }
};

// This is the "whole" — it OWNS the engine
class Car {
public:
    string brand;
    Engine engine;   // Engine is stored directly here (composition)

    Car(string b, int hp) : brand(b), engine(hp) {
        cout << brand << " car created\n";
    }

    ~Car() {
        cout << brand << " car destroyed\n";
    }

    void drive() {
        cout << brand << " is driving. ";
        engine.start();
    }
};

int main() {
    Car myCar("Toyota", 150);
    myCar.drive();

    cout << "\n(Car going out of scope now...)\n";
    // When myCar is destroyed, the engine inside is destroyed too
}
```

**Output:**
```
Engine created (150 HP)
Toyota car created
Toyota is driving. Engine started!

(Car going out of scope now...)
Toyota car destroyed
Engine destroyed
```

See how the engine was destroyed automatically when the car was destroyed? That's composition — the engine had no life outside the car.

---

## Part 2 — Aggregation ("The part lives on its own")

In aggregation, we store a **pointer** to the part instead of storing it directly. The part was created outside and just "connected" to the owner.

```cpp
#include <iostream>
#include <string>
using namespace std;

// Teacher exists on their own
class Teacher {
public:
    string name;

    Teacher(string n) : name(n) {
        cout << "Teacher " << name << " created\n";
    }

    ~Teacher() {
        cout << "Teacher " << name << " destroyed\n";
    }

    void teach() {
        cout << name << " is teaching\n";
    }
};

// School just uses teachers — it doesn't own them
class School {
public:
    string schoolName;
    Teacher* teacher;   // pointer — not a direct copy

    School(string name, Teacher* t) : schoolName(name), teacher(t) {
        cout << schoolName << " school opened\n";
    }

    ~School() {
        cout << schoolName << " school closed\n";
        // We do NOT delete the teacher here
        // The teacher exists outside and should keep living
    }

    void startClass() {
        teacher->teach();
    }
};

int main() {
    Teacher t("Mr. Ali");   // Teacher created outside the school

    {
        School s("Beaconhouse", &t);   // School just gets a reference to the teacher
        s.startClass();

        cout << "\n(School is closing now...)\n";
    }   // School is destroyed here

    // But the teacher is still alive!
    cout << "\nAfter school closed:\n";
    t.teach();   // Teacher still works fine
}
```

**Output:**
```
Teacher Mr. Ali created
Beaconhouse school opened
Mr. Ali is teaching

(School is closing now...)
Beaconhouse school closed

After school closed:
Mr. Ali is teaching
Teacher Mr. Ali destroyed
```

The school closed but Mr. Ali kept living — that's aggregation!

---

## Simple Trick to Tell Them Apart

Ask yourself: **"Can this part exist without the owner?"**

- ❌ No, it cannot → **Composition** (store it directly as a member variable)
- ✅ Yes, it can → **Aggregation** (store a pointer to it)

More examples:

| Part | Owner | Type |
|---|---|---|
| Pages | Book | Composition (no book = no pages) |
| Students | Club | Aggregation (club closes, students survive) |
| Rooms | House | Composition (house is gone = rooms gone) |
| Players | Team | Aggregation (team disbands, players survive) |

---

## Practice Problems

### Problem 1
Create a class `Battery` with a `capacity` (in mAh). Then create a `Smartphone` class that **owns** a `Battery` (composition). Give `Smartphone` a method `charge()` that prints the battery capacity. Show in `main()` that when the phone is destroyed, the battery is destroyed too.

---

### Problem 2
Create a class `Driver` with a name. Create a `Taxi` class that **uses** a driver (aggregation — store a pointer). Show that when the `Taxi` object is destroyed, the `Driver` still exists and can be used.

---

### Problem 3
You have a `Classroom`. Each classroom **owns** its `Whiteboard` (composition) but **uses** a `Teacher` from outside (aggregation). Write both classes with proper constructors and destructors, and show in `main()` that when the classroom is removed, the whiteboard is gone but the teacher lives on.
