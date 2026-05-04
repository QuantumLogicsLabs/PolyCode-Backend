// Module 10: Object-Oriented Programming - Real-Life Examples
// This file demonstrates practical applications of OOP concepts

#include <iostream>
#include <string>
#include <vector>
#include <memory>
#include <iomanip>

// Example 1: Bank Account System (Encapsulation, Constructors, Destructors)
class BankAccount {
private:
    std::string accountNumber;
    std::string accountHolder;
    double balance;
    static double interestRate;
    static int totalAccounts;
    
public:
    // Default constructor
    BankAccount() : accountNumber(""), accountHolder(""), balance(0.0) {
        totalAccounts++;
        std::cout << "Default account created. Total accounts: " << totalAccounts << std::endl;
    }
    
    // Parameterized constructor
    BankAccount(const std::string& accNum, const std::string& holder, double initialBalance)
        : accountNumber(accNum), accountHolder(holder), balance(initialBalance) {
        totalAccounts++;
        std::cout << "Account for " << accountHolder << " created. Total accounts: " << totalAccounts << std::endl;
    }
    
    // Copy constructor
    BankAccount(const BankAccount& other)
        : accountNumber(other.accountNumber), accountHolder(other.accountHolder), balance(other.balance) {
        totalAccounts++;
        std::cout << "Account copied. Total accounts: " << totalAccounts << std::endl;
    }
    
    // Destructor
    ~BankAccount() {
        totalAccounts--;
        std::cout << "Account for " << accountHolder << " destroyed. Remaining accounts: " << totalAccounts << std::endl;
    }
    
    // Getter methods (encapsulation)
    std::string getAccountNumber() const { return accountNumber; }
    std::string getAccountHolder() const { return accountHolder; }
    double getBalance() const { return balance; }
    
    // Setter methods with validation
    void setAccountHolder(const std::string& holder) {
        if (!holder.empty()) {
            accountHolder = holder;
        }
    }
    
    // Public interface methods
    void deposit(double amount) {
        if (amount > 0) {
            balance += amount;
            std::cout << "Deposited $" << amount << ". New balance: $" << balance << std::endl;
        } else {
            std::cout << "Invalid deposit amount." << std::endl;
        }
    }
    
    bool withdraw(double amount) {
        if (amount > 0 && amount <= balance) {
            balance -= amount;
            std::cout << "Withdrew $" << amount << ". New balance: $" << balance << std::endl;
            return true;
        } else {
            std::cout << "Invalid withdrawal amount or insufficient funds." << std::endl;
            return false;
        }
    }
    
    void applyInterest() {
        double interest = balance * interestRate;
        balance += interest;
        std::cout << "Interest applied: $" << interest << ". New balance: $" << balance << std::endl;
    }
    
    void displayAccountInfo() const {
        std::cout << "\n=== Account Information ===" << std::endl;
        std::cout << "Account Number: " << accountNumber << std::endl;
        std::cout << "Account Holder: " << accountHolder << std::endl;
        std::cout << "Balance: $" << std::fixed << std::setprecision(2) << balance << std::endl;
        std::cout << "Interest Rate: " << (interestRate * 100) << "%" << std::endl;
    }
    
    // Static methods
    static void setInterestRate(double rate) {
        if (rate >= 0 && rate <= 0.20) {
            interestRate = rate;
            std::cout << "Interest rate set to " << (rate * 100) << "%" << std::endl;
        }
    }
    
    static double getInterestRate() { return interestRate; }
    static int getTotalAccounts() { return totalAccounts; }
};

// Initialize static members
double BankAccount::interestRate = 0.03;
int BankAccount::totalAccounts = 0;

// Example 2: Shape Hierarchy (Inheritance, Polymorphism, Virtual Functions)
class Shape {
protected:
    std::string name;
    std::string color;
    
public:
    Shape(const std::string& name, const std::string& color) 
        : name(name), color(color) {}
    
    // Virtual destructor for proper cleanup
    virtual ~Shape() {
        std::cout << "Shape " << name << " destroyed" << std::endl;
    }
    
    // Pure virtual function (abstract class)
    virtual double calculateArea() const = 0;
    virtual double calculatePerimeter() const = 0;
    
    // Virtual function with default implementation
    virtual void displayInfo() const {
        std::cout << "Shape: " << name << ", Color: " << color << std::endl;
    }
    
    // Getter methods
    std::string getName() const { return name; }
    std::string getColor() const { return color; }
    
    // Setter methods
    void setColor(const std::string& newColor) { color = newColor; }
};

class Circle : public Shape {
private:
    double radius;
    
public:
    Circle(const std::string& name, const std::string& color, double radius)
        : Shape(name, color), radius(radius) {}
    
    // Override pure virtual functions
    double calculateArea() const override {
        return 3.14159 * radius * radius;
    }
    
    double calculatePerimeter() const override {
        return 2 * 3.14159 * radius;
    }
    
    // Override virtual function
    void displayInfo() const override {
        Shape::displayInfo();
        std::cout << "Type: Circle, Radius: " << radius << std::endl;
        std::cout << "Area: " << calculateArea() << ", Perimeter: " << calculatePerimeter() << std::endl;
    }
    
    // Circle-specific method
    void setRadius(double newRadius) {
        if (newRadius > 0) {
            radius = newRadius;
        }
    }
    
    double getRadius() const { return radius; }
};

class Rectangle : public Shape {
private:
    double width;
    double height;
    
public:
    Rectangle(const std::string& name, const std::string& color, double width, double height)
        : Shape(name, color), width(width), height(height) {}
    
    double calculateArea() const override {
        return width * height;
    }
    
    double calculatePerimeter() const override {
        return 2 * (width + height);
    }
    
    void displayInfo() const override {
        Shape::displayInfo();
        std::cout << "Type: Rectangle, Width: " << width << ", Height: " << height << std::endl;
        std::cout << "Area: " << calculateArea() << ", Perimeter: " << calculatePerimeter() << std::endl;
    }
    
    void setDimensions(double newWidth, double newHeight) {
        if (newWidth > 0 && newHeight > 0) {
            width = newWidth;
            height = newHeight;
        }
    }
    
    double getWidth() const { return width; }
    double getHeight() const { return height; }
};

class Triangle : public Shape {
private:
    double side1, side2, side3;
    
public:
    Triangle(const std::string& name, const std::string& color, 
             double s1, double s2, double s3)
        : Shape(name, color), side1(s1), side2(s2), side3(s3) {}
    
    double calculateArea() const override {
        // Heron's formula
        double s = (side1 + side2 + side3) / 2;
        return std::sqrt(s * (s - side1) * (s - side2) * (s - side3));
    }
    
    double calculatePerimeter() const override {
        return side1 + side2 + side3;
    }
    
    void displayInfo() const override {
        Shape::displayInfo();
        std::cout << "Type: Triangle, Sides: " << side1 << ", " << side2 << ", " << side3 << std::endl;
        std::cout << "Area: " << calculateArea() << ", Perimeter: " << calculatePerimeter() << std::endl;
    }
    
    bool isValidTriangle() const {
        return (side1 + side2 > side3) && 
               (side1 + side3 > side2) && 
               (side2 + side3 > side1);
    }
};

// Example 3: Animal Hierarchy (Multiple Inheritance, Virtual Functions)
class Animal {
protected:
    std::string name;
    int age;
    
public:
    Animal(const std::string& name, int age) : name(name), age(age) {}
    
    virtual ~Animal() {
        std::cout << "Animal " << name << " destroyed" << std::endl;
    }
    
    virtual void makeSound() const = 0;
    virtual void move() const {
        std::cout << name << " is moving" << std::endl;
    }
    
    void displayInfo() const {
        std::cout << "Animal: " << name << ", Age: " << age << " years" << std::endl;
    }
    
    std::string getName() const { return name; }
    int getAge() const { return age; }
};

class Mammal : virtual public Animal {
protected:
    bool hasFur;
    int gestationPeriod;
    
public:
    Mammal(const std::string& name, int age, bool fur, int gestation)
        : Animal(name, age), hasFur(fur), gestationPeriod(gestation) {}
    
    virtual void makeSound() const override {
        std::cout << name << " makes a mammal sound" << std::endl;
    }
    
    void nurse() const {
        std::cout << name << " is nursing young" << std::endl;
    }
    
    bool getHasFur() const { return hasFur; }
    int getGestationPeriod() const { return gestationPeriod; }
};

class Bird : virtual public Animal {
protected:
    bool canFly;
    double wingspan;
    
public:
    Bird(const std::string& name, int age, bool fly, double wingspan)
        : Animal(name, age), canFly(fly), wingspan(wingspan) {}
    
    virtual void makeSound() const override {
        std::cout << name << " makes a bird sound" << std::endl;
    }
    
    void fly() const {
        if (canFly) {
            std::cout << name << " is flying with " << wingspan << "m wingspan" << std::endl;
        } else {
            std::cout << name << " cannot fly" << std::endl;
        }
    }
    
    bool getCanFly() const { return canFly; }
    double getWingspan() const { return wingspan; }
};

class Bat : public Mammal, public Bird {
private:
    bool isNocturnal;
    
public:
    Bat(const std::string& name, int age, bool nocturnal)
        : Animal(name, age), Mammal(name, age, true, 180), Bird(name, age, true, 0.5), isNocturnal(nocturnal) {}
    
    void makeSound() const override {
        std::cout << name << " makes ultrasonic sounds" << std::endl;
    }
    
    void move() const override {
        std::cout << name << " is flying and crawling" << std::endl;
    }
    
    void useEcholocation() const {
        std::cout << name << " is using echolocation to navigate" << std::endl;
    }
    
    void displayInfo() const {
        Animal::displayInfo();
        std::cout << "Type: Bat, Nocturnal: " << (isNocturnal ? "Yes" : "No") << std::endl;
        std::cout << "Has Fur: " << (getHasFur() ? "Yes" : "No") << std::endl;
        std::cout << "Can Fly: " << (getCanFly() ? "Yes" : "No") << std::endl;
        std::cout << "Wingspan: " << getWingspan() << "m" << std::endl;
    }
};

// Example 4: Smart Home System (Composition, Aggregation)
class SmartDevice {
protected:
    std::string deviceId;
    std::string name;
    bool isOn;
    double powerConsumption;
    
public:
    SmartDevice(const std::string& id, const std::string& name, double power = 0.0)
        : deviceId(id), name(name), isOn(false), powerConsumption(power) {}
    
    virtual ~SmartDevice() {
        std::cout << "Smart device " << name << " destroyed" << std::endl;
    }
    
    virtual void turnOn() {
        isOn = true;
        std::cout << name << " turned on" << std::endl;
    }
    
    virtual void turnOff() {
        isOn = false;
        std::cout << name << " turned off" << std::endl;
    }
    
    virtual void displayStatus() const {
        std::cout << name << " is " << (isOn ? "ON" : "OFF") 
                  << " (Power: " << powerConsumption << "W)" << std::endl;
    }
    
    std::string getName() const { return name; }
    bool getIsOn() const { return isOn; }
    double getPowerConsumption() const { return powerConsumption; }
};

class SmartLight : public SmartDevice {
private:
    int brightness; // 0-100
    std::string color;
    
public:
    SmartLight(const std::string& id, const std::string& name)
        : SmartDevice(id, name, 10.0), brightness(50), color("white") {}
    
    void setBrightness(int level) {
        if (level >= 0 && level <= 100) {
            brightness = level;
            powerConsumption = 10.0 * (level / 100.0);
            std::cout << name << " brightness set to " << level << "%" << std::endl;
        }
    }
    
    void setColor(const std::string& newColor) {
        color = newColor;
        std::cout << name << " color set to " << color << std::endl;
    }
    
    void displayStatus() const override {
        SmartDevice::displayStatus();
        if (isOn) {
            std::cout << "  Brightness: " << brightness << "%, Color: " << color << std::endl;
        }
    }
};

class SmartThermostat : public SmartDevice {
private:
    double currentTemperature;
    double targetTemperature;
    std::string mode; // "heat", "cool", "auto"
    
public:
    SmartThermostat(const std::string& id, const std::string& name)
        : SmartDevice(id, name, 5.0), currentTemperature(20.0), targetTemperature(22.0), mode("auto") {}
    
    void setTargetTemperature(double temp) {
        targetTemperature = temp;
        std::cout << name << " target temperature set to " << temp << "°C" << std::endl;
        adjustTemperature();
    }
    
    void setMode(const std::string& newMode) {
        if (newMode == "heat" || newMode == "cool" || newMode == "auto") {
            mode = newMode;
            std::cout << name << " mode set to " << mode << std::endl;
        }
    }
    
    void adjustTemperature() {
        double difference = targetTemperature - currentTemperature;
        
        if (std::abs(difference) > 0.5) {
            if (difference > 0) {
                std::cout << name << " is heating" << std::endl;
            } else {
                std::cout << name << " is cooling" << std::endl;
            }
            currentTemperature += difference * 0.1; // Gradual adjustment
        } else {
            std::cout << name << " temperature is stable" << std::endl;
        }
    }
    
    void displayStatus() const override {
        SmartDevice::displayStatus();
        std::cout << "  Current: " << currentTemperature << "°C, Target: " << targetTemperature << "°C" << std::endl;
        std::cout << "  Mode: " << mode << std::endl;
    }
};

class SmartHome {
private:
    std::string homeName;
    std::vector<std::unique_ptr<SmartDevice>> devices;
    
public:
    SmartHome(const std::string& name) : homeName(name) {}
    
    void addDevice(std::unique_ptr<SmartDevice> device) {
        devices.push_back(std::move(device));
        std::cout << "Device added to " << homeName << std::endl;
    }
    
    void turnOnAllDevices() {
        std::cout << "\nTurning on all devices in " << homeName << std::endl;
        for (auto& device : devices) {
            device->turnOn();
        }
    }
    
    void turnOffAllDevices() {
        std::cout << "\nTurning off all devices in " << homeName << std::endl;
        for (auto& device : devices) {
            device->turnOff();
        }
    }
    
    void displayAllStatus() const {
        std::cout << "\n=== " << homeName << " Status ===" << std::endl;
        for (const auto& device : devices) {
            device->displayStatus();
        }
    }
    
    double calculateTotalPower() const {
        double total = 0;
        for (const auto& device : devices) {
            if (device->getIsOn()) {
                total += device->getPowerConsumption();
            }
        }
        return total;
    }
    
    void displayPowerConsumption() const {
        double total = calculateTotalPower();
        std::cout << "\nTotal power consumption: " << total << "W" << std::endl;
        std::cout << "Estimated monthly cost: $" << std::fixed << std::setprecision(2) 
                  << (total * 24 * 30 * 0.12 / 1000) << std::endl; // $0.12/kWh
    }
};

// Example 5: Friend Functions and Classes
class Vector3D;

class Matrix3D {
private:
    double data[3][3];
    
public:
    Matrix3D() {
        // Initialize as identity matrix
        for (int i = 0; i < 3; i++) {
            for (int j = 0; j < 3; j++) {
                data[i][j] = (i == j) ? 1.0 : 0.0;
            }
        }
    }
    
    double get(int row, int col) const {
        return data[row][col];
    }
    
    void set(int row, int col, double value) {
        data[row][col] = value;
    }
    
    void display() const {
        std::cout << "Matrix3D:" << std::endl;
        for (int i = 0; i < 3; i++) {
            for (int j = 0; j < 3; j++) {
                std::cout << std::setw(8) << std::fixed << std::setprecision(2) << data[i][j];
            }
            std::cout << std::endl;
        }
    }
    
    // Friend function declaration
    friend Vector3D operator*(const Matrix3D& matrix, const Vector3D& vector);
    
    // Friend class declaration
    friend class MatrixOperations;
};

class Vector3D {
private:
    double x, y, z;
    
public:
    Vector3D(double x = 0, double y = 0, double z = 0) : x(x), y(y), z(z) {}
    
    double getX() const { return x; }
    double getY() const { return y; }
    double getZ() const { return z; }
    
    void display() const {
        std::cout << "Vector3D(" << x << ", " << y << ", " << z << ")" << std::endl;
    }
    
    // Friend function
    friend Vector3D operator*(const Matrix3D& matrix, const Vector3D& vector);
    
    // Friend class
    friend class MatrixOperations;
};

// Friend function implementation
Vector3D operator*(const Matrix3D& matrix, const Vector3D& vector) {
    Vector3D result;
    result.x = matrix.get(0, 0) * vector.x + matrix.get(0, 1) * vector.y + matrix.get(0, 2) * vector.z;
    result.y = matrix.get(1, 0) * vector.x + matrix.get(1, 1) * vector.y + matrix.get(1, 2) * vector.z;
    result.z = matrix.get(2, 0) * vector.x + matrix.get(2, 1) * vector.y + matrix.get(2, 2) * vector.z;
    return result;
}

// Friend class
class MatrixOperations {
public:
    static void scaleMatrix(Matrix3D& matrix, double scaleFactor) {
        for (int i = 0; i < 3; i++) {
            for (int j = 0; j < 3; j++) {
                matrix.data[i][j] *= scaleFactor;
            }
        }
    }
    
    static void scaleVector(Vector3D& vector, double scaleFactor) {
        vector.x *= scaleFactor;
        vector.y *= scaleFactor;
        vector.z *= scaleFactor;
    }
};

int main() {
    std::cout << "=== Object-Oriented Programming - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of OOP concepts\n" << std::endl;
    
    // Example 1: Bank Account System
    std::cout << "=== BANK ACCOUNT SYSTEM ===" << std::endl;
    BankAccount::setInterestRate(0.025);
    
    BankAccount acc1("ACC001", "John Doe", 5000.0);
    BankAccount acc2("ACC002", "Jane Smith", 3000.0);
    
    acc1.displayAccountInfo();
    acc2.displayAccountInfo();
    
    acc1.deposit(1000.0);
    acc1.withdraw(500.0);
    acc1.applyInterest();
    
    acc1.displayAccountInfo();
    std::cout << "Total accounts: " << BankAccount::getTotalAccounts() << std::endl;
    
    // Example 2: Shape Hierarchy
    std::cout << "\n=== SHAPE HIERARCHY ===" << std::endl;
    
    std::vector<std::unique_ptr<Shape>> shapes;
    shapes.push_back(std::make_unique<Circle>("Circle1", "Red", 5.0));
    shapes.push_back(std::make_unique<Rectangle>("Rect1", "Blue", 4.0, 6.0));
    shapes.push_back(std::make_unique<Triangle>("Tri1", "Green", 3.0, 4.0, 5.0));
    
    for (const auto& shape : shapes) {
        shape->displayInfo();
        std::cout << "---" << std::endl;
    }
    
    // Example 3: Animal Hierarchy
    std::cout << "\n=== ANIMAL HIERARCHY ===" << std::endl;
    
    Bat bat("Batty", 3, true);
    bat.displayInfo();
    bat.makeSound();
    bat.move();
    bat.fly();
    bat.useEcholocation();
    
    // Example 4: Smart Home System
    std::cout << "\n=== SMART HOME SYSTEM ===" << std::endl;
    
    SmartHome myHome("My Smart Home");
    
    myHome.addDevice(std::make_unique<SmartLight>("L001", "Living Room Light"));
    myHome.addDevice(std::make_unique<SmartLight>("L002", "Bedroom Light"));
    myHome.addDevice(std::make_unique<SmartThermostat>("T001", "Main Thermostat"));
    
    // Demonstrate polymorphism
    for (auto& device : myHome.devices) {
        device->turnOn();
    }
    
    // Downcast to access specific methods
    if (auto light = dynamic_cast<SmartLight*>(myHome.devices[0].get())) {
        light->setBrightness(75);
        light->setColor("warm white");
    }
    
    if (auto thermostat = dynamic_cast<SmartThermostat*>(myHome.devices[2].get())) {
        thermostat->setTargetTemperature(23.5);
    }
    
    myHome.displayAllStatus();
    myHome.displayPowerConsumption();
    
    // Example 5: Friend Functions and Classes
    std::cout << "\n=== FRIEND FUNCTIONS AND CLASSES ===" << std::endl;
    
    Matrix3D matrix;
    Vector3D vector(1.0, 2.0, 3.0);
    
    matrix.set(0, 0, 2.0);
    matrix.set(0, 1, 3.0);
    matrix.set(0, 2, 4.0);
    matrix.set(1, 0, 5.0);
    matrix.set(1, 1, 6.0);
    matrix.set(1, 2, 7.0);
    matrix.set(2, 0, 8.0);
    matrix.set(2, 1, 9.0);
    matrix.set(2, 2, 10.0);
    
    std::cout << "Original matrix:" << std::endl;
    matrix.display();
    
    std::cout << "\nOriginal vector:" << std::endl;
    vector.display();
    
    Vector3D result = matrix * vector;
    std::cout << "\nMatrix * Vector result:" << std::endl;
    result.display();
    
    // Using friend class
    MatrixOperations::scaleMatrix(matrix, 2.0);
    MatrixOperations::scaleVector(vector, 3.0);
    
    std::cout << "\nAfter scaling:" << std::endl;
    matrix.display();
    vector.display();
    
    std::cout << "\n\n=== OOP CONCEPTS SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various OOP concepts:" << std::endl;
    std::cout << "• Encapsulation: Private data with public interface" << std::endl;
    std::cout << "• Constructors/Destructors: Object lifecycle management" << std::endl;
    std::cout << "• Inheritance: Code reuse and hierarchical relationships" << std::endl;
    std::cout << "• Polymorphism: Virtual functions and dynamic binding" << std::endl;
    std::cout << "• Abstract classes: Pure virtual functions" << std::endl;
    std::cout << "• Multiple inheritance: Diamond problem solution" << std::endl;
    std::cout << "• Composition: Has-a relationships" << std::endl;
    std::cout << "• Friend functions/classes: Controlled access to private data" << std::endl;
    std::cout << "• Static members: Class-level data and behavior" << std::endl;
    std::cout << "\nOOP provides powerful tools for modeling real-world systems!" << std::endl;
    
    return 0;
}