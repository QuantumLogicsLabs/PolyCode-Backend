# C++ Design Patterns

Design patterns are proven, reusable solutions to common software design problems. Mastering them elevates your C++ from working code to professional-grade architecture.

---

## Categories

| Category | Purpose | Examples |
|---|---|---|
| **Creational** | Object creation | Singleton, Factory, Builder |
| **Structural** | Class composition | Adapter, Decorator, Facade |
| **Behavioral** | Object communication | Observer, Strategy, Command |

---

## Creational Patterns

### Singleton — One Instance Globally
```cpp
class DatabaseConnection {
private:
    static DatabaseConnection* instance;
    std::string host;
    DatabaseConnection(std::string h) : host(h) {}
public:
    static DatabaseConnection* getInstance(std::string host = "localhost") {
        if (!instance) instance = new DatabaseConnection(host);
        return instance;
    }
    void query(const std::string& sql) {
        std::cout << "[" << host << "] Executing: " << sql << "\n";
    }
};
DatabaseConnection* DatabaseConnection::instance = nullptr;
```

### Factory — Delegate Object Creation
```cpp
class Shape {
public:
    virtual void draw() = 0;
    virtual ~Shape() {}
};

class Circle : public Shape { public: void draw() override { std::cout << "Drawing Circle\n"; } };
class Square : public Shape { public: void draw() override { std::cout << "Drawing Square\n"; } };

class ShapeFactory {
public:
    static std::unique_ptr<Shape> create(const std::string& type) {
        if (type == "circle") return std::make_unique<Circle>();
        if (type == "square") return std::make_unique<Square>();
        return nullptr;
    }
};
```

### Builder — Step-by-Step Construction
```cpp
struct Pizza {
    std::string crust, sauce;
    std::vector<std::string> toppings;
    void show() {
        std::cout << "Pizza: " << crust << " crust, " << sauce << " sauce";
        for (auto& t : toppings) std::cout << ", " << t;
        std::cout << "\n";
    }
};

class PizzaBuilder {
    Pizza pizza;
public:
    PizzaBuilder& setCrust(std::string c)  { pizza.crust = c; return *this; }
    PizzaBuilder& setSauce(std::string s)  { pizza.sauce = s; return *this; }
    PizzaBuilder& addTopping(std::string t){ pizza.toppings.push_back(t); return *this; }
    Pizza build() { return pizza; }
};
```

---

## Structural Patterns

### Adapter — Bridge Incompatible Interfaces
```cpp
// Old payment system
class OldPayment { public: void processPayment(int amount) { std::cout << "Old: $" << amount << "\n"; } };

// New interface everyone expects
class PaymentProcessor { public: virtual void pay(double amount) = 0; };

class PaymentAdapter : public PaymentProcessor {
    OldPayment old;
public:
    void pay(double amount) override { old.processPayment((int)amount); }
};
```

### Decorator — Wrap to Extend Behavior
```cpp
class Coffee { public: virtual double cost() { return 2.0; } virtual std::string name() { return "Coffee"; } };

class Milk : public Coffee {
    Coffee* base;
public:
    Milk(Coffee* c) : base(c) {}
    double cost() override { return base->cost() + 0.5; }
    std::string name() override { return base->name() + " + Milk"; }
};

class Sugar : public Coffee {
    Coffee* base;
public:
    Sugar(Coffee* c) : base(c) {}
    double cost() override { return base->cost() + 0.25; }
    std::string name() override { return base->name() + " + Sugar"; }
};
```

---

## Behavioral Patterns

### Observer — Notify on Change
```cpp
class Observer { public: virtual void update(const std::string& event) = 0; };

class EventSystem {
    std::vector<Observer*> observers;
public:
    void subscribe(Observer* o)  { observers.push_back(o); }
    void notify(const std::string& event) { for (auto* o : observers) o->update(event); }
};

class Logger : public Observer {
public:
    void update(const std::string& event) override { std::cout << "[LOG] " << event << "\n"; }
};
```

### Strategy — Swap Algorithms at Runtime
```cpp
class SortStrategy { public: virtual void sort(std::vector<int>& v) = 0; };

class BubbleSortStrategy : public SortStrategy {
public:
    void sort(std::vector<int>& v) override {
        for (int i = 0; i < (int)v.size()-1; i++)
            for (int j = 0; j < (int)v.size()-i-1; j++)
                if (v[j] > v[j+1]) std::swap(v[j], v[j+1]);
    }
};

class Sorter {
    SortStrategy* strategy;
public:
    Sorter(SortStrategy* s) : strategy(s) {}
    void sort(std::vector<int>& v) { strategy->sort(v); }
};
```

---

## When to Use Which Pattern

| Situation | Pattern |
|---|---|
| Need exactly one instance | Singleton |
| Creating objects of different types | Factory |
| Complex object with many options | Builder |
| Wrapping an old/incompatible class | Adapter |
| Adding features without subclassing | Decorator |
| Multiple objects need state updates | Observer |
| Switching algorithms at runtime | Strategy |

> 💡 **Rule**: Prefer composition over inheritance. Most patterns exploit this principle.
