// ============================================================
//  C++ Design Patterns — Complete Examples
// ============================================================

#include <iostream>
#include <vector>
#include <memory>
#include <string>
#include <functional>

// ─────────────────────────────────────────────
// PATTERN 1: Singleton
// ─────────────────────────────────────────────

class DatabaseConnection {
private:
    static DatabaseConnection* instance;
    std::string host;
    int queryCount = 0;
    DatabaseConnection(std::string h) : host(h) {
        std::cout << "[DB] Connected to " << host << "\n";
    }
public:
    static DatabaseConnection* getInstance(std::string h = "localhost") {
        if (!instance) instance = new DatabaseConnection(h);
        return instance;
    }
    void query(const std::string& sql) {
        std::cout << "[DB:" << host << "] Query #" << ++queryCount << ": " << sql << "\n";
    }
    int getQueryCount() { return queryCount; }
};
DatabaseConnection* DatabaseConnection::instance = nullptr;

// ─────────────────────────────────────────────
// PATTERN 2: Factory
// ─────────────────────────────────────────────

class Shape {
public:
    virtual void draw() = 0;
    virtual double area() = 0;
    virtual ~Shape() {}
};

class Circle : public Shape {
    double r;
public:
    Circle(double r) : r(r) {}
    void draw() override { std::cout << "○ Circle (r=" << r << ")\n"; }
    double area() override { return 3.14159 * r * r; }
};

class Square : public Shape {
    double s;
public:
    Square(double s) : s(s) {}
    void draw() override { std::cout << "□ Square (s=" << s << ")\n"; }
    double area() override { return s * s; }
};

class Triangle : public Shape {
    double b, h;
public:
    Triangle(double b, double h) : b(b), h(h) {}
    void draw() override { std::cout << "△ Triangle (b=" << b << " h=" << h << ")\n"; }
    double area() override { return 0.5 * b * h; }
};

class ShapeFactory {
public:
    static std::unique_ptr<Shape> create(const std::string& type, double a, double b = 0) {
        if (type == "circle")   return std::make_unique<Circle>(a);
        if (type == "square")   return std::make_unique<Square>(a);
        if (type == "triangle") return std::make_unique<Triangle>(a, b);
        return nullptr;
    }
};

// ─────────────────────────────────────────────
// PATTERN 3: Builder
// ─────────────────────────────────────────────

struct Pizza {
    std::string size, crust, sauce;
    std::vector<std::string> toppings;
    void show() const {
        std::cout << "  [" << size << "] " << crust << " crust, " << sauce << " sauce";
        for (auto& t : toppings) std::cout << " +" << t;
        std::cout << "\n";
    }
};

class PizzaBuilder {
    Pizza p;
public:
    PizzaBuilder& setSize(std::string s)   { p.size = s; return *this; }
    PizzaBuilder& setCrust(std::string c)  { p.crust = c; return *this; }
    PizzaBuilder& setSauce(std::string s)  { p.sauce = s; return *this; }
    PizzaBuilder& addTopping(std::string t){ p.toppings.push_back(t); return *this; }
    Pizza build() { return p; }
};

// ─────────────────────────────────────────────
// PATTERN 4: Adapter
// ─────────────────────────────────────────────

class LegacyLogger {
public:
    void writeLog(int level, std::string msg) {
        std::cout << "[LVL" << level << "] " << msg << "\n";
    }
};

class Logger {
public:
    virtual void info(const std::string& msg) = 0;
    virtual void error(const std::string& msg) = 0;
    virtual ~Logger() {}
};

class LoggerAdapter : public Logger {
    LegacyLogger legacy;
public:
    void info(const std::string& msg) override  { legacy.writeLog(1, msg); }
    void error(const std::string& msg) override { legacy.writeLog(3, msg); }
};

// ─────────────────────────────────────────────
// PATTERN 5: Decorator
// ─────────────────────────────────────────────

class Coffee {
public:
    virtual double cost() const { return 2.0; }
    virtual std::string name() const { return "Coffee"; }
    virtual ~Coffee() {}
};

class Milk : public Coffee {
    const Coffee& base;
public:
    Milk(const Coffee& c) : base(c) {}
    double cost() const override { return base.cost() + 0.5; }
    std::string name() const override { return base.name() + " + Milk"; }
};

class Sugar : public Coffee {
    const Coffee& base;
public:
    Sugar(const Coffee& c) : base(c) {}
    double cost() const override { return base.cost() + 0.25; }
    std::string name() const override { return base.name() + " + Sugar"; }
};

class WhipCream : public Coffee {
    const Coffee& base;
public:
    WhipCream(const Coffee& c) : base(c) {}
    double cost() const override { return base.cost() + 0.75; }
    std::string name() const override { return base.name() + " + Whip"; }
};

// ─────────────────────────────────────────────
// PATTERN 6: Observer
// ─────────────────────────────────────────────

class Observer { public: virtual void update(const std::string& event) = 0; virtual ~Observer() {} };

class EventBus {
    std::vector<Observer*> listeners;
public:
    void subscribe(Observer* o)           { listeners.push_back(o); }
    void publish(const std::string& evt)  { for (auto* o : listeners) o->update(evt); }
};

class Logger2 : public Observer {
    std::string name;
public:
    Logger2(std::string n) : name(n) {}
    void update(const std::string& event) override {
        std::cout << "  [" << name << "] received: " << event << "\n";
    }
};

// ─────────────────────────────────────────────
// PATTERN 7: Strategy
// ─────────────────────────────────────────────

class PricingStrategy { public: virtual double calculate(double base) = 0; virtual ~PricingStrategy() {} };
class RegularPrice  : public PricingStrategy { public: double calculate(double b) override { return b; } };
class DiscountPrice : public PricingStrategy { double pct; public: DiscountPrice(double p) : pct(p) {} double calculate(double b) override { return b * (1 - pct); } };
class PremiumPrice  : public PricingStrategy { public: double calculate(double b) override { return b * 1.2; } };

class Product {
    std::string name;
    double basePrice;
    PricingStrategy* strategy;
public:
    Product(std::string n, double p, PricingStrategy* s) : name(n), basePrice(p), strategy(s) {}
    void setStrategy(PricingStrategy* s) { strategy = s; }
    void showPrice() {
        std::cout << "  " << name << ": $" << strategy->calculate(basePrice) << "\n";
    }
};

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

int main() {
    std::cout << "===== C++ Design Patterns Demo =====\n\n";

    std::cout << "--- Singleton ---\n";
    auto* db1 = DatabaseConnection::getInstance("prod-db.example.com");
    auto* db2 = DatabaseConnection::getInstance();
    db1->query("SELECT * FROM users");
    db2->query("INSERT INTO logs VALUES(...)");
    std::cout << "Same instance? " << (db1 == db2 ? "Yes" : "No") << "\n";

    std::cout << "\n--- Factory ---\n";
    auto shapes = {
        ShapeFactory::create("circle",   5.0),
        ShapeFactory::create("square",   4.0),
        ShapeFactory::create("triangle", 6.0, 3.0)
    };
    for (auto& s : shapes) { s->draw(); std::cout << "  Area: " << s->area() << "\n"; }

    std::cout << "\n--- Builder ---\n";
    Pizza p1 = PizzaBuilder().setSize("Large").setCrust("Thin").setSauce("Tomato")
                             .addTopping("Cheese").addTopping("Pepperoni").build();
    Pizza p2 = PizzaBuilder().setSize("Small").setCrust("Thick").setSauce("BBQ")
                             .addTopping("Chicken").addTopping("Onion").build();
    p1.show(); p2.show();

    std::cout << "\n--- Adapter ---\n";
    LoggerAdapter logger;
    logger.info("Application started");
    logger.error("Failed to connect to cache");

    std::cout << "\n--- Decorator ---\n";
    Coffee base;
    Milk   withMilk(base);
    Sugar  withSugar(withMilk);
    WhipCream fancy(withSugar);
    std::cout << "  " << base.name()  << "  → $" << base.cost()  << "\n";
    std::cout << "  " << withMilk.name() << "  → $" << withMilk.cost() << "\n";
    std::cout << "  " << fancy.name() << "  → $" << fancy.cost() << "\n";

    std::cout << "\n--- Observer ---\n";
    EventBus bus;
    Logger2 emailSvc("EmailService");
    Logger2 analyticsSvc("Analytics");
    bus.subscribe(&emailSvc);
    bus.subscribe(&analyticsSvc);
    bus.publish("user.registered");
    bus.publish("order.placed");

    std::cout << "\n--- Strategy ---\n";
    RegularPrice  regular;
    DiscountPrice discount(0.20);
    PremiumPrice  premium;
    Product laptop("Laptop", 999.0, &regular);
    laptop.showPrice();
    laptop.setStrategy(&discount);
    laptop.showPrice();
    laptop.setStrategy(&premium);
    laptop.showPrice();

    std::cout << "\n✅ All patterns demonstrated!\n";
    return 0;
}
