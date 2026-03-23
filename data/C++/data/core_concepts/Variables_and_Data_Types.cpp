// Module 2: Variables and Data Types - Real-Life Examples
// This file demonstrates practical applications of variables and data types

#include <iostream>
#include <string>
#include <vector>
#include <iomanip>

// Example 1: Employee Management System
class Employee {
private:
    // Different data types for employee information
    int employeeId;           // Integer for unique ID
    std::string firstName;    // String for name
    std::string lastName;     // String for name
    double salary;            // Double for precise salary
    char department;          // Char for department code
    bool isActive;            // Boolean for employment status
    int yearsOfService;       // Integer for years worked
    
public:
    Employee(int id, std::string first, std::string last, 
             double sal, char dept, bool active, int years)
        : employeeId(id), firstName(first), lastName(last), 
          salary(sal), department(dept), isActive(active), yearsOfService(years) {}
    
    void displayEmployeeInfo() {
        std::cout << "Employee ID: " << employeeId << std::endl;
        std::cout << "Name: " << firstName << " " << lastName << std::endl;
        std::cout << "Salary: $" << std::fixed << std::setprecision(2) << salary << std::endl;
        std::cout << "Department: " << department << std::endl;
        std::cout << "Status: " << (isActive ? "Active" : "Inactive") << std::endl;
        std::cout << "Years of Service: " << yearsOfService << std::endl;
        std::cout << "----------------------------------------" << std::endl;
    }
    
    double calculateAnnualBonus() {
        // Bonus calculation based on years of service
        double bonusRate = 0.0;
        if (yearsOfService >= 10) bonusRate = 0.10;
        else if (yearsOfService >= 5) bonusRate = 0.07;
        else if (yearsOfService >= 2) bonusRate = 0.05;
        else bonusRate = 0.03;
        
        return salary * bonusRate;
    }
    
    // Getters for various data types
    int getId() const { return employeeId; }
    std::string getFullName() const { return firstName + " " + lastName; }
    double getSalary() const { return salary; }
    char getDepartment() const { return department; }
    bool getStatus() const { return isActive; }
};

// Example 2: Product Information System
class Product {
private:
    std::string productName;      // String for product name
    int productId;               // Integer for unique ID
    double price;                // Double for precise pricing
    int quantityInStock;         // Integer for inventory count
    float weight;                // Float for weight (less precision needed)
    bool isPerishable;           // Boolean for storage requirements
    char sizeCategory;           // Char for size (S, M, L, XL)
    
public:
    Product(std::string name, int id, double pr, int qty, 
            float wt, bool perish, char size)
        : productName(name), productId(id), price(pr), quantityInStock(qty),
          weight(wt), isPerishable(perish), sizeCategory(size) {}
    
    void displayProductInfo() {
        std::cout << "Product: " << productName << " (ID: " << productId << ")" << std::endl;
        std::cout << "Price: $" << std::fixed << std::setprecision(2) << price << std::endl;
        std::cout << "Stock: " << quantityInStock << " units" << std::endl;
        std::cout << "Weight: " << std::fixed << std::setprecision(2) << weight << " kg" << std::endl;
        std::cout << "Perishable: " << (isPerishable ? "Yes" : "No") << std::endl;
        std::cout << "Size: " << sizeCategory << std::endl;
        std::cout << "----------------------------------------" << std::endl;
    }
    
    double calculateTotalValue() {
        return price * quantityInStock;
    }
    
    bool needsReorder() {
        return quantityInStock < 10; // Reorder if less than 10 units
    }
};

// Example 3: Weather Data Tracking
class WeatherData {
private:
    std::string date;           // String for date
    double temperature;         // Double for precise temperature
    double humidity;            // Double for humidity percentage
    float windSpeed;            // Float for wind speed
    int pressure;               // Integer for atmospheric pressure
    bool isRaining;             // Boolean for rain status
    char weatherCondition;      // Char for weather code (S=Sunny, C=Cloudy, R=Rainy)
    
public:
    WeatherData(std::string dt, double temp, double humid, 
                float wind, int press, bool rain, char condition)
        : date(dt), temperature(temp), humidity(humidity), 
          windSpeed(wind), pressure(press), isRaining(rain), weatherCondition(condition) {}
    
    void displayWeatherReport() {
        std::cout << "Weather Report for " << date << std::endl;
        std::cout << "Temperature: " << std::fixed << std::setprecision(1) 
                  << temperature << "°C" << std::endl;
        std::cout << "Humidity: " << std::fixed << std::setprecision(1) 
                  << humidity << "%" << std::endl;
        std::cout << "Wind Speed: " << std::fixed << std::setprecision(1) 
                  << windSpeed << " km/h" << std::endl;
        std::cout << "Pressure: " << pressure << " hPa" << std::endl;
        std::cout << "Raining: " << (isRaining ? "Yes" : "No") << std::endl;
        
        // Decode weather condition
        std::string conditionStr;
        switch (weatherCondition) {
            case 'S': conditionStr = "Sunny"; break;
            case 'C': conditionStr = "Cloudy"; break;
            case 'R': conditionStr = "Rainy"; break;
            case 'W': conditionStr = "Windy"; break;
            default: conditionStr = "Unknown"; break;
        }
        std::cout << "Condition: " << conditionStr << std::endl;
        std::cout << "----------------------------------------" << std::endl;
    }
    
    // Weather analysis methods
    bool isComfortable() {
        return (temperature >= 18.0 && temperature <= 26.0) && 
               (humidity >= 30.0 && humidity <= 60.0);
    }
    
    std::string getWeatherAdvice() {
        if (temperature > 30.0) return "Stay hydrated and avoid prolonged sun exposure";
        else if (temperature < 5.0) return "Wear warm clothing and be cautious of ice";
        else if (isRaining) return "Carry an umbrella and drive carefully";
        else if (windSpeed > 50.0) return "Secure loose objects and avoid outdoor activities";
        else return "Weather is pleasant for outdoor activities";
    }
};

// Example 4: Banking Account System
class BankAccount {
private:
    std::string accountNumber;   // String for account number
    std::string accountHolder;   // String for account holder name
    double balance;             // Double for precise money amount
    char accountType;           // Char for account type (S=Savings, C=Checking)
    bool isOverdrawn;           // Boolean for overdraft status
    int transactionCount;       // Integer for transaction tracking
    
public:
    BankAccount(std::string accNum, std::string holder, double initialBalance, char type)
        : accountNumber(accNum), accountHolder(holder), balance(initialBalance),
          accountType(type), isOverdrawn(false), transactionCount(0) {
        checkOverdraft();
    }
    
    void deposit(double amount) {
        if (amount > 0) {
            balance += amount;
            transactionCount++;
            checkOverdraft();
            std::cout << "Deposited $" << std::fixed << std::setprecision(2) 
                      << amount << " to account " << accountNumber << std::endl;
            std::cout << "New balance: $" << balance << std::endl;
        } else {
            std::cout << "Invalid deposit amount!" << std::endl;
        }
    }
    
    void withdraw(double amount) {
        if (amount > 0 && amount <= balance) {
            balance -= amount;
            transactionCount++;
            checkOverdraft();
            std::cout << "Withdrew $" << std::fixed << std::setprecision(2) 
                      << amount << " from account " << accountNumber << std::endl;
            std::cout << "New balance: $" << balance << std::endl;
        } else {
            std::cout << "Invalid withdrawal amount or insufficient funds!" << std::endl;
        }
    }
    
    void checkOverdraft() {
        isOverdrawn = balance < 0;
    }
    
    void displayAccountInfo() {
        std::cout << "Account Number: " << accountNumber << std::endl;
        std::cout << "Account Holder: " << accountHolder << std::endl;
        std::cout << "Account Type: " << (accountType == 'S' ? "Savings" : "Checking") << std::endl;
        std::cout << "Balance: $" << std::fixed << std::setprecision(2) << balance << std::endl;
        std::cout << "Status: " << (isOverdrawn ? "OVERDRAWN" : "Good") << std::endl;
        std::cout << "Transactions: " << transactionCount << std::endl;
        std::cout << "----------------------------------------" << std::endl;
    }
    
    double calculateInterest() {
        if (accountType == 'S' && balance > 0) {
            return balance * 0.02; // 2% interest for savings
        }
        return 0.0;
    }
};

int main() {
    std::cout << "=== Variables and Data Types - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of different data types\n" << std::endl;
    
    // Example 1: Employee Management
    std::cout << "=== EMPLOYEE MANAGEMENT SYSTEM ===" << std::endl;
    Employee emp1(1001, "John", "Smith", 75000.0, 'I', true, 7);
    Employee emp2(1002, "Sarah", "Johnson", 82000.0, 'M', true, 12);
    Employee emp3(1003, "Mike", "Wilson", 68000.0, 'S', false, 3);
    
    emp1.displayEmployeeInfo();
    std::cout << "Annual Bonus: $" << emp1.calculateAnnualBonus() << std::endl;
    
    emp2.displayEmployeeInfo();
    std::cout << "Annual Bonus: $" << emp2.calculateAnnualBonus() << std::endl;
    
    // Example 2: Product Information
    std::cout << "\n=== PRODUCT INFORMATION SYSTEM ===" << std::endl;
    Product laptop("Dell XPS 13", 1001, 1299.99, 25, 1.2f, false, 'M');
    Product milk("Organic Milk", 2001, 3.99, 50, 1.0f, true, 'L');
    Product shirt("Cotton T-Shirt", 3001, 19.99, 100, 0.2f, false, 'S');
    
    laptop.displayProductInfo();
    std::cout << "Total Value: $" << laptop.calculateTotalValue() << std::endl;
    std::cout << "Needs Reorder: " << (laptop.needsReorder() ? "Yes" : "No") << std::endl;
    
    milk.displayProductInfo();
    std::cout << "Total Value: $" << milk.calculateTotalValue() << std::endl;
    std::cout << "Needs Reorder: " << (milk.needsReorder() ? "No" : "Yes") << std::endl;
    
    // Example 3: Weather Data
    std::cout << "\n=== WEATHER DATA TRACKING ===" << std::endl;
    WeatherData today("2024-03-18", 22.5, 45.0, 12.5f, 1013, false, 'S');
    WeatherData yesterday("2024-03-17", 15.2, 78.0, 25.3f, 1008, true, 'R');
    
    today.displayWeatherReport();
    std::cout << "Comfortable: " << (today.isComfortable() ? "Yes" : "No") << std::endl;
    std::cout << "Advice: " << today.getWeatherAdvice() << std::endl;
    
    yesterday.displayWeatherReport();
    std::cout << "Comfortable: " << (yesterday.isComfortable() ? "Yes" : "No") << std::endl;
    std::cout << "Advice: " << yesterday.getWeatherAdvice() << std::endl;
    
    // Example 4: Banking System
    std::cout << "\n=== BANKING ACCOUNT SYSTEM ===" << std::endl;
    BankAccount savings("SAV001", "Alice Brown", 5000.0, 'S');
    BankAccount checking("CHK001", "Bob Davis", 1500.0, 'C');
    
    savings.displayAccountInfo();
    savings.deposit(1000.0);
    std::cout << "Monthly Interest: $" << savings.calculateInterest() << std::endl;
    
    checking.displayAccountInfo();
    checking.withdraw(200.0);
    checking.withdraw(1400.0); // This will leave a small balance
    
    std::cout << "\n\n=== DATA TYPES SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates how different data types are used:" << std::endl;
    std::cout << "• int: Employee IDs, quantities, years of service" << std::endl;
    std::cout << "• double: Precise financial amounts, scientific measurements" << std::endl;
    std::cout << "• float: Less precise measurements like weight" << std::endl;
    std::cout << "• char: Single-character codes and categories" << std::endl;
    std::cout << "• bool: True/false conditions and status flags" << std::endl;
    std::cout << "• string: Names, descriptions, and text data" << std::endl;
    std::cout << "\nChoosing the right data type is crucial for efficient and accurate programming!" << std::endl;
    
    return 0;
}