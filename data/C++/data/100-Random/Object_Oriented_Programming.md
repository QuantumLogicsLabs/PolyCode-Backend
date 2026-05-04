# Module 10: Object-Oriented Programming with Classes

## Learning Objectives
- Understand the principles of Object-Oriented Programming (OOP)
- Master class definition and implementation
- Learn about encapsulation, inheritance, and polymorphism
- Understand constructors and destructors
- Master access specifiers and member functions
- Learn about static members and friend functions

## Introduction to OOP

Object-Oriented Programming is a programming paradigm based on the concept of "objects" which contain data (attributes) and code (methods). The main principles are:

1. **Encapsulation**: Bundling data and methods together
2. **Inheritance**: Creating new classes from existing ones
3. **Polymorphism**: Using objects of different classes through the same interface
4. **Abstraction**: Hiding implementation details while showing essential features

## Basic Class Definition

### Simple Class Example
```cpp
#include <iostream>
#include <string>

class Rectangle {
private:
    double width;
    double height;
    
public:
    // Constructor
    Rectangle(double w, double h) {
        width = w;
        height = h;
        std::cout << "Rectangle created with width " << width << " and height " << height << std::endl;
    }
    
    // Destructor
    ~Rectangle() {
        std::cout << "Rectangle destroyed" << std::endl;
    }
    
    // Member functions
    double getArea() {
        return width * height;
    }
    
    double getPerimeter() {
        return 2 * (width + height);
    }
    
    // Setter methods
    void setWidth(double w) {
        if (w > 0) {
            width = w;
        }
    }
    
    void setHeight(double h) {
        if (h > 0) {
            height = h;
        }
    }
    
    // Getter methods
    double getWidth() {
        return width;
    }
    
    double getHeight() {
        return height;
    }
    
    // Display function
    void display() {
        std::cout << "Rectangle: " << width << " x " << height << std::endl;
        std::cout << "Area: " << getArea() << std::endl;
        std::cout << "Perimeter: " << getPerimeter() << std::endl;
    }
};

int main() {
    // Create objects
    Rectangle rect1(5.0, 3.0);
    Rectangle rect2(10.0, 4.0);
    
    // Use member functions
    std::cout << "\nRectangle 1:" << std::endl;
    rect1.display();
    
    std::cout << "\nRectangle 2:" << std::endl;
    rect2.display();
    
    // Modify object
    rect1.setWidth(7.0);
    rect1.setHeight(4.0);
    
    std::cout << "\nModified Rectangle 1:" << std::endl;
    rect1.display();
    
    return 0;
}
```

### Class Declaration and Implementation Separation

#### Rectangle.h (Header File)
```cpp
#ifndef RECTANGLE_H
#define RECTANGLE_H

class Rectangle {
private:
    double width;
    double height;
    
public:
    // Constructor
    Rectangle(double w, double h);
    
    // Destructor
    ~Rectangle();
    
    // Member functions
    double getArea();
    double getPerimeter();
    
    // Setter methods
    void setWidth(double w);
    void setHeight(double h);
    
    // Getter methods
    double getWidth();
    double getHeight();
    
    // Display function
    void display();
};

#endif // RECTANGLE_H
```

#### Rectangle.cpp (Implementation File)
```cpp
#include "Rectangle.h"
#include <iostream>

// Constructor implementation
Rectangle::Rectangle(double w, double h) {
    width = w;
    height = h;
    std::cout << "Rectangle created with width " << width << " and height " << height << std::endl;
}

// Destructor implementation
Rectangle::~Rectangle() {
    std::cout << "Rectangle destroyed" << std::endl;
}

// Member function implementations
double Rectangle::getArea() {
    return width * height;
}

double Rectangle::getPerimeter() {
    return 2 * (width + height);
}

void Rectangle::setWidth(double w) {
    if (w > 0) {
        width = w;
    }
}

void Rectangle::setHeight(double h) {
    if (h > 0) {
        height = h;
    }
}

double Rectangle::getWidth() {
    return width;
}

double Rectangle::getHeight() {
    return height;
}

void Rectangle::display() {
    std::cout << "Rectangle: " << width << " x " << height << std::endl;
    std::cout << "Area: " << getArea() << std::endl;
    std::cout << "Perimeter: " << getPerimeter() << std::endl;
}
```

## Constructors and Destructors

### Multiple Constructors
```cpp
#include <iostream>
#include <string>

class Student {
private:
    std::string name;
    int age;
    double gpa;
    
public:
    // Default constructor
    Student() {
        name = "Unknown";
        age = 0;
        gpa = 0.0;
        std::cout << "Default constructor called" << std::endl;
    }
    
    // Parameterized constructor
    Student(const std::string& n, int a, double g) {
        name = n;
        age = a;
        gpa = g;
        std::cout << "Parameterized constructor called for " << name << std::endl;
    }
    
    // Copy constructor
    Student(const Student& other) {
        name = other.name;
        age = other.age;
        gpa = other.gpa;
        std::cout << "Copy constructor called for " << name << std::endl;
    }
    
    // Destructor
    ~Student() {
        std::cout << "Destructor called for " << name << std::endl;
    }
    
    // Display function
    void display() {
        std::cout << "Student: " << name << ", Age: " << age << ", GPA: " << gpa << std::endl;
    }
};

int main() {
    Student s1;  // Default constructor
    s1.display();
    
    Student s2("John Doe", 20, 3.8);  // Parameterized constructor
    s2.display();
    
    Student s3 = s2;  // Copy constructor
    s3.display();
    
    Student s4(s2);   // Another way to call copy constructor
    s4.display();
    
    return 0;
}
```

### Constructor Initialization List
```cpp
#include <iostream>
#include <string>

class Person {
private:
    std::string name;
    int age;
    const int id;  // const member must be initialized
    
public:
    // Using initialization list
    Person(const std::string& n, int a, int i) 
        : name(n), age(a), id(i) {
        std::cout << "Person " << name << " created with ID " << id << std::endl;
    }
    
    // Default constructor with initialization list
    Person() : name("Unknown"), age(0), id(0) {
        std::cout << "Default Person created" << std::endl;
    }
    
    void display() {
        std::cout << "ID: " << id << ", Name: " << name << ", Age: " << age << std::endl;
    }
};

int main() {
    Person p1("Alice", 25, 1001);
    Person p2;
    
    p1.display();
    p2.display();
    
    return 0;
}
```

## Access Specifiers and Encapsulation

### Public, Private, and Protected Members
```cpp
#include <iostream>
#include <string>

class BankAccount {
private:
    std::string accountNumber;
    double balance;
    std::string ownerName;
    
    // Private helper method
    bool isValidAmount(double amount) {
        return amount > 0;
    }
    
public:
    // Public constructor
    BankAccount(const std::string& accNum, const std::string& owner, double initialBalance = 0.0) {
        accountNumber = accNum;
        ownerName = owner;
        balance = initialBalance;
    }
    
    // Public interface methods
    bool deposit(double amount) {
        if (!isValidAmount(amount)) {
            std::cout << "Invalid deposit amount!" << std::endl;
            return false;
        }
        
        balance += amount;
        std::cout << "Deposited $" << amount << ". New balance: $" << balance << std::endl;
        return true;
    }
    
    bool withdraw(double amount) {
        if (!isValidAmount(amount) || amount > balance) {
            std::cout << "Invalid withdrawal amount or insufficient funds!" << std::endl;
            return false;
        }
        
        balance -= amount;
        std::cout << "Withdrew $" << amount << ". New balance: $" << balance << std::endl;
        return true;
    }
    
    // Getter methods (read-only access to private data)
    double getBalance() const {
        return balance;
    }
    
    std::string getAccountNumber() const {
        return accountNumber;
    }
    
    std::string getOwnerName() const {
        return ownerName;
    }
    
    // Display account information
    void displayAccountInfo() const {
        std::cout << "Account Number: " << accountNumber << std::endl;
        std::cout << "Owner: " << ownerName << std::endl;
        std::cout << "Balance: $" << balance << std::endl;
    }
    
protected:
    // Protected members can be accessed by derived classes
    void setBalance(double newBalance) {
        balance = newBalance;
    }
};

int main() {
    BankAccount account("123456789", "John Doe", 1000.0);
    
    account.displayAccountInfo();
    
    account.deposit(500.0);
    account.withdraw(200.0);
    
    // These would cause compilation errors because members are private:
    // account.balance = 1000000.0;  // Error: private member
    // account.accountNumber = "999"; // Error: private member
    
    // Use public methods instead
    std::cout << "Current balance: $" << account.getBalance() << std::endl;
    
    return 0;
}
```

## Static Members

### Static Data Members and Functions
```cpp
#include <iostream>
#include <string>

class Employee {
private:
    std::string name;
    int employeeId;
    static int totalEmployees;  // Static data member
    static const double MINIMUM_WAGE;  // Static constant
    
public:
    Employee(const std::string& n) : name(n) {
        employeeId = ++totalEmployees;
        std::cout << "Employee " << name << " created with ID " << employeeId << std::endl;
    }
    
    ~Employee() {
        std::cout << "Employee " << name << " destroyed" << std::endl;
        totalEmployees--;
    }
    
    // Static member function
    static int getTotalEmployees() {
        return totalEmployees;
    }
    
    // Static member function
    static double getMinimumWage() {
        return MINIMUM_WAGE;
    }
    
    void display() const {
        std::cout << "ID: " << employeeId << ", Name: " << name << std::endl;
    }
};

// Initialize static data members
int Employee::totalEmployees = 0;
const double Employee::MINIMUM_WAGE = 15.0;

int main() {
    std::cout << "Initial employee count: " << Employee::getTotalEmployees() << std::endl;
    std::cout << "Minimum wage: $" << Employee::getMinimumWage() << std::endl;
    
    Employee emp1("Alice Smith");
    Employee emp2("Bob Johnson");
    Employee emp3("Charlie Brown");
    
    std::cout << "\nCurrent employee count: " << Employee::getTotalEmployees() << std::endl;
    
    std::cout << "\nEmployee list:" << std::endl;
    emp1.display();
    emp2.display();
    emp3.display();
    
    {
        Employee emp4("Diana Prince");
        std::cout << "\nEmployee count with temp employee: " << Employee::getTotalEmployees() << std::endl;
    } // emp4 goes out of scope and is destroyed
    
    std::cout << "Final employee count: " << Employee::getTotalEmployees() << std::endl;
    
    return 0;
}
```

## Inheritance

### Basic Inheritance
```cpp
#include <iostream>
#include <string>

// Base class
class Animal {
protected:
    std::string name;
    int age;
    
public:
    Animal(const std::string& n, int a) : name(n), age(a) {
        std::cout << "Animal constructor called" << std::endl;
    }
    
    virtual ~Animal() {
        std::cout << "Animal destructor called" << std::endl;
    }
    
    void eat() {
        std::cout << name << " is eating" << std::endl;
    }
    
    void sleep() {
        std::cout << name << " is sleeping" << std::endl;
    }
    
    // Virtual function (can be overridden)
    virtual void makeSound() {
        std::cout << name << " makes a sound" << std::endl;
    }
    
    // Pure virtual function (makes this an abstract class)
    virtual void move() = 0;
    
    void displayInfo() {
        std::cout << "Name: " << name << ", Age: " << age << std::endl;
    }
};

// Derived class
class Dog : public Animal {
private:
    std::string breed;
    
public:
    Dog(const std::string& n, int a, const std::string& b) 
        : Animal(n, a), breed(b) {
        std::cout << "Dog constructor called" << std::endl;
    }
    
    ~Dog() {
        std::cout << "Dog destructor called" << std::endl;
    }
    
    // Override base class method
    void makeSound() override {
        std::cout << name << " barks: Woof!" << std::endl;
    }
    
    // Implement pure virtual function
    void move() override {
        std::cout << name << " runs on four legs" << std::endl;
    }
    
    // Dog-specific method
    void wagTail() {
        std::cout << name << " wags tail happily" << std::endl;
    }
    
    void displayDogInfo() {
        displayInfo();
        std::cout << "Breed: " << breed << std::endl;
    }
};

// Another derived class
class Cat : public Animal {
private:
    bool isIndoor;
    
public:
    Cat(const std::string& n, int a, bool indoor = true) 
        : Animal(n, a), isIndoor(indoor) {
        std::cout << "Cat constructor called" << std::endl;
    }
    
    ~Cat() {
        std::cout << "Cat destructor called" << std::endl;
    }
    
    void makeSound() override {
        std::cout << name << " meows: Meow!" << std::endl;
    }
    
    void move() override {
        std::cout << name << " walks gracefully" << std::endl;
    }
    
    void purr() {
        std::cout << name << " purrs contentedly" << std::endl;
    }
};

int main() {
    // Create derived class objects
    Dog dog("Buddy", 3, "Golden Retriever");
    Cat cat("Whiskers", 2, true);
    
    std::cout << "\n=== Dog Actions ===" << std::endl;
    dog.displayDogInfo();
    dog.eat();
    dog.makeSound();
    dog.move();
    dog.wagTail();
    
    std::cout << "\n=== Cat Actions ===" << std::endl;
    cat.displayInfo();
    cat.eat();
    cat.makeSound();
    cat.move();
    cat.purr();
    
    // Polymorphism through base class pointers
    std::cout << "\n=== Polymorphism Demo ===" << std::endl;
    Animal* animals[2];
    animals[0] = &dog;
    animals[1] = &cat;
    
    for (int i = 0; i < 2; i++) {
        animals[i]->makeSound();
        animals[i]->move();
        std::cout << std::endl;
    }
    
    return 0;
}
```

### Multiple Inheritance
```cpp
#include <iostream>
#include <string>

class Printable {
public:
    virtual void print() = 0;
    virtual ~Printable() = default;
};

class Serializable {
public:
    virtual std::string serialize() = 0;
    virtual ~Serializable() = default;
};

class Document : public Printable, public Serializable {
private:
    std::string title;
    std::string content;
    
public:
    Document(const std::string& t, const std::string& c) 
        : title(t), content(c) {}
    
    void print() override {
        std::cout << "Document: " << title << std::endl;
        std::cout << "Content: " << content << std::endl;
    }
    
    std::string serialize() override {
        return "Title: " + title + "\nContent: " + content;
    }
};

int main() {
    Document doc("My Report", "This is the content of my report.");
    
    doc.print();
    std::cout << "\nSerialized:\n" << doc.serialize() << std::endl;
    
    return 0;
}
```

## Polymorphism

### Virtual Functions and Runtime Polymorphism
```cpp
#include <iostream>
#include <vector>
#include <memory>

class Shape {
protected:
    std::string name;
    
public:
    Shape(const std::string& n) : name(n) {}
    
    virtual ~Shape() {
        std::cout << "Shape destructor" << std::endl;
    }
    
    // Virtual function
    virtual double getArea() const = 0;
    virtual double getPerimeter() const = 0;
    
    // Virtual function with default implementation
    virtual void display() const {
        std::cout << "Shape: " << name << std::endl;
        std::cout << "Area: " << getArea() << std::endl;
        std::cout << "Perimeter: " << getPerimeter() << std::endl;
    }
    
    std::string getName() const { return name; }
};

class Circle : public Shape {
private:
    double radius;
    
public:
    Circle(double r) : Shape("Circle"), radius(r) {}
    
    double getArea() const override {
        return 3.14159 * radius * radius;
    }
    
    double getPerimeter() const override {
        return 2 * 3.14159 * radius;
    }
    
    void display() const override {
        std::cout << "Circle with radius " << radius << std::endl;
        Shape::display();
    }
};

class Rectangle : public Shape {
private:
    double width, height;
    
public:
    Rectangle(double w, double h) : Shape("Rectangle"), width(w), height(h) {}
    
    double getArea() const override {
        return width * height;
    }
    
    double getPerimeter() const override {
        return 2 * (width + height);
    }
    
    void display() const override {
        std::cout << "Rectangle " << width << " x " << height << std::endl;
        Shape::display();
    }
};

class Triangle : public Shape {
private:
    double base, height, side1, side2;
    
public:
    Triangle(double b, double h, double s1, double s2) 
        : Shape("Triangle"), base(b), height(h), side1(s1), side2(s2) {}
    
    double getArea() const override {
        return 0.5 * base * height;
    }
    
    double getPerimeter() const override {
        return base + side1 + side2;
    }
    
    void display() const override {
        std::cout << "Triangle (base=" << base << ", height=" << height << ")" << std::endl;
        Shape::display();
    }
};

int main() {
    // Using smart pointers for automatic memory management
    std::vector<std::unique_ptr<Shape>> shapes;
    
    shapes.push_back(std::make_unique<Circle>(5.0));
    shapes.push_back(std::make_unique<Rectangle>(4.0, 6.0));
    shapes.push_back(std::make_unique<Triangle>(3.0, 4.0, 5.0, 5.0));
    
    std::cout << "=== Shape Information ===" << std::endl;
    
    // Polymorphic behavior
    for (const auto& shape : shapes) {
        shape->display();
        std::cout << std::endl;
    }
    
    // Calculate total area
    double totalArea = 0.0;
    for (const auto& shape : shapes) {
        totalArea += shape->getArea();
    }
    
    std::cout << "Total area of all shapes: " << totalArea << std::endl;
    
    return 0;
}
```

## Friend Functions and Classes

### Friend Function Example
```cpp
#include <iostream>

class Box {
private:
    double width;
    double height;
    double depth;
    
public:
    Box(double w, double h, double d) : width(w), height(h), depth(d) {}
    
    // Friend function declaration
    friend double getVolume(const Box& box);
    
    // Friend class declaration
    friend class BoxPrinter;
    
    // Member function
    void displayDimensions() {
        std::cout << "Dimensions: " << width << " x " << height << " x " << depth << std::endl;
    }
};

// Friend function definition
double getVolume(const Box& box) {
    // Can access private members
    return box.width * box.height * box.depth;
}

// Friend class
class BoxPrinter {
public:
    static void printBoxInfo(const Box& box) {
        std::cout << "=== Box Information ===" << std::endl;
        std::cout << "Width: " << box.width << std::endl;  // Access private member
        std::cout << "Height: " << box.height << std::endl; // Access private member
        std::cout << "Depth: " << box.depth << std::endl;   // Access private member
        std::cout << "Volume: " << getVolume(box) << std::endl;
    }
};

int main() {
    Box box(3.0, 4.0, 5.0);
    
    box.displayDimensions();
    
    // Using friend function
    std::cout << "Volume (via friend function): " << getVolume(box) << std::endl;
    
    // Using friend class
    BoxPrinter::printBoxInfo(box);
    
    return 0;
}
```

## Complete Example: Banking System

```cpp
#include <iostream>
#include <string>
#include <vector>
#include <memory>
#include <ctime>
#include <iomanip>

// Abstract base class
class Account {
protected:
    std::string accountNumber;
    std::string ownerName;
    double balance;
    static int accountCounter;
    
public:
    Account(const std::string& owner, double initialBalance = 0.0) 
        : ownerName(owner), balance(initialBalance) {
        accountNumber = "ACC" + std::to_string(++accountCounter);
    }
    
    virtual ~Account() {}
    
    // Pure virtual functions
    virtual bool withdraw(double amount) = 0;
    virtual void calculateInterest() = 0;
    virtual void displayAccountInfo() const = 0;
    
    // Common functions
    bool deposit(double amount) {
        if (amount > 0) {
            balance += amount;
            return true;
        }
        return false;
    }
    
    double getBalance() const { return balance; }
    std::string getAccountNumber() const { return accountNumber; }
    std::string getOwnerName() const { return ownerName; }
    
    void setOwnerName(const std::string& name) { ownerName = name; }
};

int Account::accountCounter = 0;

// Savings Account class
class SavingsAccount : public Account {
private:
    double interestRate;
    
public:
    SavingsAccount(const std::string& owner, double initialBalance, double rate)
        : Account(owner, initialBalance), interestRate(rate) {}
    
    bool withdraw(double amount) override {
        if (amount > 0 && balance >= amount) {
            balance -= amount;
            return true;
        }
        return false;
    }
    
    void calculateInterest() override {
        double interest = balance * (interestRate / 100.0);
        balance += interest;
        std::cout << "Interest calculated: $" << interest << std::endl;
    }
    
    void displayAccountInfo() const override {
        std::cout << "=== Savings Account ===" << std::endl;
        std::cout << "Account Number: " << accountNumber << std::endl;
        std::cout << "Owner: " << ownerName << std::endl;
        std::cout << "Balance: $" << std::fixed << std::setprecision(2) << balance << std::endl;
        std::cout << "Interest Rate: " << interestRate << "%" << std::endl;
    }
};

// Checking Account class
class CheckingAccount : public Account {
private:
    double overdraftLimit;
    static const double TRANSACTION_FEE;
    
public:
    CheckingAccount(const std::string& owner, double initialBalance, double overdraft = 500.0)
        : Account(owner, initialBalance), overdraftLimit(overdraft) {}
    
    bool withdraw(double amount) override {
        double totalAmount = amount + TRANSACTION_FEE;
        if (amount > 0 && balance + overdraftLimit >= totalAmount) {
            balance -= totalAmount;
            return true;
        }
        return false;
    }
    
    void calculateInterest() override {
        // Checking accounts typically don't earn interest
        std::cout << "No interest for checking accounts" << std::endl;
    }
    
    void displayAccountInfo() const override {
        std::cout << "=== Checking Account ===" << std::endl;
        std::cout << "Account Number: " << accountNumber << std::endl;
        std::cout << "Owner: " << ownerName << std::endl;
        std::cout << "Balance: $" << std::fixed << std::setprecision(2) << balance << std::endl;
        std::cout << "Overdraft Limit: $" << overdraftLimit << std::endl;
        std::cout << "Transaction Fee: $" << TRANSACTION_FEE << std::endl;
    }
};

const double CheckingAccount::TRANSACTION_FEE = 1.50;

// Bank class that manages accounts
class Bank {
private:
    std::string bankName;
    std::vector<std::unique_ptr<Account>> accounts;
    
public:
    Bank(const std::string& name) : bankName(name) {}
    
    void addSavingsAccount(const std::string& owner, double initialBalance, double interestRate) {
        accounts.push_back(std::make_unique<SavingsAccount>(owner, initialBalance, interestRate));
        std::cout << "Savings account created for " << owner << std::endl;
    }
    
    void addCheckingAccount(const std::string& owner, double initialBalance, double overdraftLimit) {
        accounts.push_back(std::make_unique<CheckingAccount>(owner, initialBalance, overdraftLimit));
        std::cout << "Checking account created for " << owner << std::endl;
    }
    
    void displayAllAccounts() const {
        std::cout << "\n=== " << bankName << " - All Accounts ===" << std::endl;
        for (const auto& account : accounts) {
            account->displayAccountInfo();
            std::cout << std::endl;
        }
    }
    
    void calculateAllInterest() {
        std::cout << "\n=== Calculating Interest for All Accounts ===" << std::endl;
        for (const auto& account : accounts) {
            std::cout << "Account " << account->getAccountNumber() << ": ";
            account->calculateInterest();
        }
    }
    
    Account* findAccount(const std::string& accountNumber) {
        for (const auto& account : accounts) {
            if (account->getAccountNumber() == accountNumber) {
                return account.get();
            }
        }
        return nullptr;
    }
    
    void displayBankStatistics() const {
        double totalBalance = 0.0;
        int savingsCount = 0, checkingCount = 0;
        
        for (const auto& account : accounts) {
            totalBalance += account->getBalance();
            
            if (dynamic_cast<SavingsAccount*>(account.get())) {
                savingsCount++;
            } else if (dynamic_cast<CheckingAccount*>(account.get())) {
                checkingCount++;
            }
        }
        
        std::cout << "\n=== Bank Statistics ===" << std::endl;
        std::cout << "Total Accounts: " << accounts.size() << std::endl;
        std::cout << "Savings Accounts: " << savingsCount << std::endl;
        std::cout << "Checking Accounts: " << checkingCount << std::endl;
        std::cout << "Total Balance: $" << std::fixed << std::setprecision(2) << totalBalance << std::endl;
        std::cout << "Average Balance: $" << std::fixed << std::setprecision(2) 
                  << (accounts.empty() ? 0.0 : totalBalance / accounts.size()) << std::endl;
    }
};

int main() {
    Bank myBank("First National Bank");
    
    // Create accounts
    myBank.addSavingsAccount("Alice Johnson", 1000.0, 2.5);
    myBank.addCheckingAccount("Bob Smith", 500.0, 1000.0);
    myBank.addSavingsAccount("Charlie Brown", 2000.0, 3.0);
    myBank.addCheckingAccount("Diana Prince", 1500.0, 500.0);
    
    // Display all accounts
    myBank.displayAllAccounts();
    
    // Perform some transactions
    Account* aliceAccount = myBank.findAccount("ACC1");
    if (aliceAccount) {
        aliceAccount->deposit(500.0);
        aliceAccount->withdraw(200.0);
        std::cout << "\nAfter transactions for Alice:" << std::endl;
        aliceAccount->displayAccountInfo();
    }
    
    Account* bobAccount = myBank.findAccount("ACC2");
    if (bobAccount) {
        bobAccount->withdraw(600.0); // Should work with overdraft
        std::cout << "\nAfter withdrawal for Bob:" << std::endl;
        bobAccount->displayAccountInfo();
    }
    
    // Calculate interest
    myBank.calculateAllInterest();
    
    // Display final statistics
    myBank.displayBankStatistics();
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Vehicle Hierarchy
Create a vehicle management system:
- Base Vehicle class with common properties
- Derived classes: Car, Motorcycle, Truck
- Virtual functions for start(), stop(), move()
- Polymorphic array of vehicles

### Exercise 2: Library System
Implement a library management system:
- Book, Member, and Library classes
- Inheritance for different book types
- Polymorphic borrowing system
- Friend functions for reporting

### Exercise 3: Shape Calculator
Build a shape calculator:
- Abstract Shape base class
- Various shape implementations
- Virtual area and perimeter calculations
- Shape comparison and sorting

### Exercise 4: Employee Management
Create an employee management system:
- Employee base class
- Derived classes: Manager, Developer, Intern
- Virtual salary calculation
- Static members for company-wide statistics

## Key Takeaways
- Classes encapsulate data and functions together
- Constructors initialize objects, destructors clean up
- Access specifiers control member visibility
- Inheritance enables code reuse and specialization
- Virtual functions enable runtime polymorphism
- Static members belong to the class, not instances
- Friend functions/classes can access private members
- Abstract classes define interfaces for derived classes

## Next Module
In the next module, we'll explore templates and the Standard Template Library (STL).