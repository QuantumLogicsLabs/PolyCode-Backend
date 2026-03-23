// Module 1: Introduction to C++ - Real-Life Examples
// This file demonstrates practical applications of C++ concepts

#include <iostream>
#include <string>
#include <vector>

// Example 1: Personal Finance Tracker
class FinanceTracker {
private:
    std::string userName;
    double balance;
    std::vector<std::string> transactions;
    
public:
    FinanceTracker(std::string name, double initialBalance) 
        : userName(name), balance(initialBalance) {
        std::cout << "Welcome to Finance Tracker, " << userName << "!" << std::endl;
        std::cout << "Initial balance: $" << balance << std::endl;
    }
    
    void addTransaction(std::string description, double amount) {
        transactions.push_back(description + ": $" + std::to_string(amount));
        balance += amount;
        std::cout << "Transaction added: " << description << " ($" << amount << ")" << std::endl;
        std::cout << "New balance: $" << balance << std::endl;
    }
    
    void displaySummary() {
        std::cout << "\n=== Finance Summary for " << userName << " ===" << std::endl;
        std::cout << "Current balance: $" << balance << std::endl;
        std::cout << "Recent transactions:" << std::endl;
        for (const auto& transaction : transactions) {
            std::cout << "  - " << transaction << std::endl;
        }
    }
};

// Example 2: Simple Inventory Management System
class InventoryManager {
private:
    struct Product {
        std::string name;
        int quantity;
        double price;
    };
    
    std::vector<Product> products;
    
public:
    void addProduct(std::string name, int quantity, double price) {
        Product newProduct = {name, quantity, price};
        products.push_back(newProduct);
        std::cout << "Added product: " << name << " (Qty: " << quantity 
                  << ", Price: $" << price << ")" << std::endl;
    }
    
    void displayInventory() {
        std::cout << "\n=== Current Inventory ===" << std::endl;
        double totalValue = 0;
        
        for (const auto& product : products) {
            double productValue = product.quantity * product.price;
            totalValue += productValue;
            std::cout << product.name << ": " << product.quantity 
                      << " units @ $" << product.price 
                      << " (Total: $" << productValue << ")" << std::endl;
        }
        
        std::cout << "\nTotal inventory value: $" << totalValue << std::endl;
    }
    
    void findProduct(std::string name) {
        for (const auto& product : products) {
            if (product.name == name) {
                std::cout << "Found: " << product.name << " - " 
                          << product.quantity << " units available at $" 
                          << product.price << " each" << std::endl;
                return;
            }
        }
        std::cout << "Product '" << name << "' not found in inventory." << std::endl;
    }
};

// Example 3: Student Grade Management
class GradeManager {
private:
    std::string studentName;
    std::vector<std::pair<std::string, double>> grades; // subject, grade
    
public:
    GradeManager(std::string name) : studentName(name) {
        std::cout << "Grade system initialized for: " << studentName << std::endl;
    }
    
    void addGrade(std::string subject, double grade) {
        grades.push_back({subject, grade});
        std::cout << "Grade added: " << subject << " - " << grade << std::endl;
    }
    
    void calculateAverage() {
        if (grades.empty()) {
            std::cout << "No grades available for " << studentName << std::endl;
            return;
        }
        
        double sum = 0;
        for (const auto& grade : grades) {
            sum += grade.second;
        }
        
        double average = sum / grades.size();
        std::cout << "\nGrade Report for " << studentName << std::endl;
        std::cout << "Average: " << average << std::endl;
        
        // Grade classification
        if (average >= 90) std::cout << "Performance: Excellent (A)" << std::endl;
        else if (average >= 80) std::cout << "Performance: Good (B)" << std::endl;
        else if (average >= 70) std::cout << "Performance: Satisfactory (C)" << std::endl;
        else if (average >= 60) std::cout << "Performance: Needs Improvement (D)" << std::endl;
        else std::cout << "Performance: Failing (F)" << std::endl;
    }
    
    void displayAllGrades() {
        std::cout << "\n=== All Grades for " << studentName << " ===" << std::endl;
        for (const auto& grade : grades) {
            std::cout << grade.first << ": " << grade.second << std::endl;
        }
    }
};

int main() {
    std::cout << "=== C++ Introduction - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of C++ programming\n" << std::endl;
    
    // Example 1: Personal Finance Tracker
    std::cout << "\n=== PERSONAL FINANCE TRACKER ===" << std::endl;
    FinanceTracker myFinance("John Doe", 1000.0);
    myFinance.addTransaction("Salary", 2500.0);
    myFinance.addTransaction("Rent", -800.0);
    myFinance.addTransaction("Groceries", -150.0);
    myFinance.addTransaction("Freelance Work", 300.0);
    myFinance.displaySummary();
    
    // Example 2: Inventory Management System
    std::cout << "\n\n=== INVENTORY MANAGEMENT SYSTEM ===" << std::endl;
    InventoryManager inventory;
    inventory.addProduct("Laptop", 10, 999.99);
    inventory.addProduct("Mouse", 25, 29.99);
    inventory.addProduct("Keyboard", 15, 79.99);
    inventory.addProduct("Monitor", 8, 299.99);
    inventory.displayInventory();
    inventory.findProduct("Laptop");
    inventory.findProduct("Webcam"); // This should show "not found"
    
    // Example 3: Student Grade Management
    std::cout << "\n\n=== STUDENT GRADE MANAGEMENT ===" << std::endl;
    GradeManager studentGrades("Alice Smith");
    studentGrades.addGrade("Mathematics", 92.5);
    studentGrades.addGrade("Physics", 88.0);
    studentGrades.addGrade("Chemistry", 85.5);
    studentGrades.addGrade("English", 94.0);
    studentGrades.addGrade("Computer Science", 96.5);
    studentGrades.displayAllGrades();
    studentGrades.calculateAverage();
    
    std::cout << "\n\n=== CONCLUSION ===" << std::endl;
    std::cout << "These examples demonstrate how C++ can be used to solve real-world problems:" << std::endl;
    std::cout << "1. Finance tracking for personal budget management" << std::endl;
    std::cout << "2. Inventory management for businesses" << std::endl;
    std::cout << "3. Educational systems for grade tracking" << std::endl;
    std::cout << "\nC++ provides the structure and efficiency needed for practical applications!" << std::endl;
    
    return 0;
}