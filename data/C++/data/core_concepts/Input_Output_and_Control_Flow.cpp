// Module 4: Input/Output and Control Flow - Real-Life Examples
// This file demonstrates practical applications of I/O and control structures

#include <iostream>
#include <string>
#include <vector>
#include <iomanip>
#include <limits>

// Example 1: ATM Machine Simulator
class ATMSimulator {
private:
    double balance;
    std::string accountHolder;
    int pin;
    bool isAuthenticated;
    
public:
    ATMSimulator(std::string holder, double initialBalance, int accountPin)
        : accountHolder(holder), balance(initialBalance), pin(accountPin), isAuthenticated(false) {}
    
    bool authenticate() {
        int attempts = 0;
        int enteredPin;
        
        while (attempts < 3) {
            std::cout << "Enter PIN (attempt " << (attempts + 1) << " of 3): ";
            std::cin >> enteredPin;
            
            if (enteredPin == pin) {
                isAuthenticated = true;
                std::cout << "Authentication successful!" << std::endl;
                return true;
            } else {
                attempts++;
                std::cout << "Incorrect PIN. ";
                if (attempts < 3) {
                    std::cout << "Please try again." << std::endl;
                } else {
                    std::cout << "Account locked. Too many failed attempts." << std::endl;
                }
            }
        }
        return false;
    }
    
    void displayMenu() {
        if (!isAuthenticated) {
            std::cout << "Please authenticate first." << std::endl;
            return;
        }
        
        int choice;
        do {
            std::cout << "\n=== ATM Menu ===" << std::endl;
            std::cout << "1. Check Balance" << std::endl;
            std::cout << "2. Withdraw Cash" << std::endl;
            std::cout << "3. Deposit Cash" << std::endl;
            std::cout << "4. Transfer Funds" << std::endl;
            std::cout << "5. Change PIN" << std::endl;
            std::cout << "6. Exit" << std::endl;
            std::cout << "Enter your choice (1-6): ";
            std::cin >> choice;
            
            switch (choice) {
                case 1:
                    checkBalance();
                    break;
                case 2:
                    withdrawCash();
                    break;
                case 3:
                    depositCash();
                    break;
                case 4:
                    transferFunds();
                    break;
                case 5:
                    changePIN();
                    break;
                case 6:
                    std::cout << "Thank you for using ATM. Goodbye!" << std::endl;
                    break;
                default:
                    std::cout << "Invalid choice. Please enter a number between 1 and 6." << std::endl;
            }
        } while (choice != 6);
    }
    
    void checkBalance() {
        std::cout << "\nAccount Balance: $" << std::fixed << std::setprecision(2) << balance << std::endl;
    }
    
    void withdrawCash() {
        double amount;
        std::cout << "Enter amount to withdraw: $";
        std::cin >> amount;
        
        // Input validation
        if (std::cin.fail() || amount <= 0) {
            std::cout << "Invalid amount. Please enter a positive number." << std::endl;
            std::cin.clear();
            std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
            return;
        }
        
        // Business logic with nested if-else
        if (amount > balance) {
            std::cout << "Insufficient funds. Current balance: $" << balance << std::endl;
        } else if (amount > 500) {
            std::cout << "Daily withdrawal limit is $500. Please enter a smaller amount." << std::endl;
        } else {
            balance -= amount;
            std::cout << "Successfully withdrew $" << amount << std::endl;
            std::cout << "New balance: $" << balance << std::endl;
            
            // Additional logic based on amount
            if (amount >= 100) {
                std::cout << "Large withdrawal detected. Please count your cash carefully." << std::endl;
            } else if (amount < 20) {
                std::cout << "Small withdrawal. Consider using debit card for small purchases." << std::endl;
            }
        }
    }
    
    void depositCash() {
        double amount;
        std::cout << "Enter amount to deposit: $";
        std::cin >> amount;
        
        if (std::cin.fail() || amount <= 0) {
            std::cout << "Invalid amount. Please enter a positive number." << std::endl;
            std::cin.clear();
            std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
            return;
        }
        
        if (amount > 10000) {
            std::cout << "Large deposit detected. Please see a teller for deposits over $10,000." << std::endl;
        } else {
            balance += amount;
            std::cout << "Successfully deposited $" << amount << std::endl;
            std::cout << "New balance: $" << balance << std::endl;
        }
    }
    
    void transferFunds() {
        std::string targetAccount;
        double amount;
        
        std::cout << "Enter target account number: ";
        std::cin.ignore();
        std::getline(std::cin, targetAccount);
        
        std::cout << "Enter amount to transfer: $";
        std::cin >> amount;
        
        if (std::cin.fail() || amount <= 0) {
            std::cout << "Invalid amount. Please enter a positive number." << std::endl;
            std::cin.clear();
            std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
            return;
        }
        
        if (amount > balance) {
            std::cout << "Insufficient funds for transfer." << std::endl;
        } else {
            balance -= amount;
            std::cout << "Transferred $" << amount << " to account " << targetAccount << std::endl;
            std::cout << "New balance: $" << balance << std::endl;
        }
    }
    
    void changePIN() {
        int oldPin, newPin, confirmPin;
        
        std::cout << "Enter current PIN: ";
        std::cin >> oldPin;
        
        if (oldPin != pin) {
            std::cout << "Incorrect current PIN." << std::endl;
            return;
        }
        
        std::cout << "Enter new PIN: ";
        std::cin >> newPin;
        
        std::cout << "Confirm new PIN: ";
        std::cin >> confirmPin;
        
        if (newPin == confirmPin) {
            pin = newPin;
            std::cout << "PIN successfully changed!" << std::endl;
        } else {
            std::cout << "PINs do not match. Please try again." << std::endl;
        }
    }
};

// Example 2: Student Registration System
class StudentRegistration {
private:
    struct Course {
        std::string code;
        std::string name;
        int credits;
        int maxStudents;
        int enrolledStudents;
        bool hasPrerequisites;
        std::string prerequisites;
    };
    
    std::vector<Course> courses;
    std::vector<std::string> registeredCourses;
    int totalCredits;
    double tuitionRate;
    
public:
    StudentRegistration(double rate = 150.0) : totalCredits(0), tuitionRate(rate) {
        initializeCourses();
    }
    
    void initializeCourses() {
        courses = {
            {"CS101", "Introduction to Programming", 3, 30, 25, false, ""},
            {"CS201", "Data Structures", 4, 25, 20, true, "CS101"},
            {"CS301", "Algorithms", 4, 20, 15, true, "CS201"},
            {"MATH101", "Calculus I", 4, 35, 30, false, ""},
            {"ENG101", "English Composition", 3, 40, 35, false, ""},
            {"PHYS101", "Physics I", 4, 30, 25, false, ""}
        };
    }
    
    void displayAvailableCourses() {
        std::cout << "\n=== AVAILABLE COURSES ===" << std::endl;
        std::cout << std::left << std::setw(8) << "Code" 
                  << std::setw(30) << "Name" 
                  << std::setw(8) << "Credits" 
                  << std::setw(15) << "Capacity" 
                  << "Prerequisites" << std::endl;
        std::cout << std::string(80, '-') << std::endl;
        
        for (const auto& course : courses) {
            std::cout << std::left << std::setw(8) << course.code
                      << std::setw(30) << course.name
                      << std::setw(8) << course.credits
                      << std::setw(15) << (std::to_string(course.enrolledStudents) + "/" + std::to_string(course.maxStudents))
                      << (course.hasPrerequisites ? course.prerequisites : "None") << std::endl;
        }
    }
    
    void registerForCourse() {
        std::string courseCode;
        
        displayAvailableCourses();
        std::cout << "\nEnter course code to register (or 'done' to finish): ";
        std::cin >> courseCode;
        
        while (courseCode != "done") {
            bool found = false;
            bool alreadyRegistered = false;
            
            // Check if course exists and student is not already registered
            for (const auto& course : courses) {
                if (course.code == courseCode) {
                    found = true;
                    
                    // Check if already registered
                    for (const auto& registered : registeredCourses) {
                        if (registered == courseCode) {
                            alreadyRegistered = true;
                            break;
                        }
                    }
                    
                    if (alreadyRegistered) {
                        std::cout << "You are already registered for " << course.name << std::endl;
                        break;
                    }
                    
                    // Check enrollment capacity
                    if (course.enrolledStudents >= course.maxStudents) {
                        std::cout << "Course " << course.name << " is full." << std::endl;
                        break;
                    }
                    
                    // Check prerequisites
                    if (course.hasPrerequisites) {
                        bool hasPrereq = false;
                        for (const auto& prereq : registeredCourses) {
                            if (prereq == course.prerequisites) {
                                hasPrereq = true;
                                break;
                            }
                        }
                        
                        if (!hasPrereq) {
                            std::cout << "Prerequisite " << course.prerequisites 
                                      << " required for " << course.name << std::endl;
                            break;
                        }
                    }
                    
                    // Check credit limit
                    if (totalCredits + course.credits > 18) {
                        std::cout << "Cannot register: would exceed 18 credit limit." << std::endl;
                        break;
                    }
                    
                    // All checks passed - register student
                    registeredCourses.push_back(courseCode);
                    totalCredits += course.credits;
                    
                    // Update course enrollment (in real system, this would be persistent)
                    for (auto& c : courses) {
                        if (c.code == courseCode) {
                            c.enrolledStudents++;
                            break;
                        }
                    }
                    
                    std::cout << "Successfully registered for " << course.name 
                              << " (" << course.credits << " credits)" << std::endl;
                    break;
                }
            }
            
            if (!found) {
                std::cout << "Course " << courseCode << " not found." << std::endl;
            }
            
            std::cout << "\nEnter next course code (or 'done' to finish): ";
            std::cin >> courseCode;
        }
    }
    
    void displayRegistrationSummary() {
        std::cout << "\n=== REGISTRATION SUMMARY ===" << std::endl;
        std::cout << "Total Credits: " << totalCredits << std::endl;
        std::cout << "Tuition: $" << (totalCredits * tuitionRate) << std::endl;
        
        std::cout << "\nRegistered Courses:" << std::endl;
        for (const auto& courseCode : registeredCourses) {
            for (const auto& course : courses) {
                if (course.code == courseCode) {
                    std::cout << "  " << course.code << " - " << course.name 
                              << " (" << course.credits << " credits)" << std::endl;
                    break;
                }
            }
        }
        
        // Registration status
        if (totalCredits >= 12) {
            std::cout << "\nStatus: Full-time student" << std::endl;
        } else if (totalCredits >= 6) {
            std::cout << "\nStatus: Part-time student" << std::endl;
        } else {
            std::cout << "\nStatus: Below minimum credit load" << std::endl;
        }
    }
};

// Example 3: Menu-Driven Restaurant Order System
class RestaurantOrderSystem {
private:
    struct MenuItem {
        std::string name;
        double price;
        std::string category;
        bool isAvailable;
    };
    
    std::vector<MenuItem> menu;
    std::vector<std::pair<std::string, int>> order; // item name, quantity
    double totalAmount;
    
public:
    RestaurantOrderSystem() : totalAmount(0.0) {
        initializeMenu();
    }
    
    void initializeMenu() {
        menu = {
            {"Burger", 8.99, "Main Course", true},
            {"Pizza", 12.99, "Main Course", true},
            {"Salad", 6.99, "Appetizer", true},
            {"Soup", 4.99, "Appetizer", true},
            {"Fries", 3.99, "Side", true},
            {"Soda", 2.99, "Beverage", true},
            {"Coffee", 3.49, "Beverage", true},
            {"Cake", 5.99, "Dessert", false}, // Out of stock
            {"Ice Cream", 4.99, "Dessert", true}
        };
    }
    
    void displayMenu() {
        std::cout << "\n=== RESTAURANT MENU ===" << std::endl;
        
        std::string currentCategory = "";
        for (const auto& item : menu) {
            if (item.category != currentCategory) {
                currentCategory = item.category;
                std::cout << "\n--- " << currentCategory << " ---" << std::endl;
            }
            
            std::cout << std::left << std::setw(20) << item.name
                      << "$" << std::fixed << std::setprecision(2) << std::setw(8) << item.price;
            
            if (!item.isAvailable) {
                std::cout << " (OUT OF STOCK)";
            }
            std::cout << std::endl;
        }
    }
    
    void takeOrder() {
        int choice;
        int quantity;
        
        do {
            displayMenu();
            std::cout << "\n=== ORDER MENU ===" << std::endl;
            std::cout << "1. Add item to order" << std::endl;
            std::cout << "2. View current order" << std::endl;
            std::cout << "3. Remove item" << std::endl;
            std::cout << "4. Checkout" << std::endl;
            std::cout << "5. Exit" << std::endl;
            std::cout << "Enter your choice (1-5): ";
            std::cin >> choice;
            
            switch (choice) {
                case 1:
                    addItemToOrder();
                    break;
                case 2:
                    viewOrder();
                    break;
                case 3:
                    removeItemFromOrder();
                    break;
                case 4:
                    checkout();
                    break;
                case 5:
                    std::cout << "Thank you for visiting!" << std::endl;
                    break;
                default:
                    std::cout << "Invalid choice. Please try again." << std::endl;
            }
        } while (choice != 5);
    }
    
    void addItemToOrder() {
        std::string itemName;
        int quantity;
        
        std::cout << "Enter item name: ";
        std::cin.ignore();
        std::getline(std::cin, itemName);
        
        std::cout << "Enter quantity: ";
        std::cin >> quantity;
        
        // Find item in menu
        bool found = false;
        for (const auto& item : menu) {
            if (item.name == itemName) {
                found = true;
                
                if (!item.isAvailable) {
                    std::cout << "Sorry, " << itemName << " is not available." << std::endl;
                    break;
                }
                
                if (quantity <= 0) {
                    std::cout << "Quantity must be positive." << std::endl;
                    break;
                }
                
                // Add to order
                order.push_back({itemName, quantity});
                totalAmount += item.price * quantity;
                
                std::cout << "Added " << quantity << " x " << itemName 
                          << " to order. Subtotal: $" << totalAmount << std::endl;
                break;
            }
        }
        
        if (!found) {
            std::cout << "Item '" << itemName << "' not found in menu." << std::endl;
        }
    }
    
    void viewOrder() {
        if (order.empty()) {
            std::cout << "Your order is empty." << std::endl;
            return;
        }
        
        std::cout << "\n=== CURRENT ORDER ===" << std::endl;
        std::cout << std::left << std::setw(20) << "Item" 
                  << std::setw(10) << "Quantity" 
                  << std::setw(10) << "Price" 
                  << "Total" << std::endl;
        std::cout << std::string(50, '-') << std::endl;
        
        for (const auto& orderItem : order) {
            // Find price from menu
            double itemPrice = 0;
            for (const auto& menuItem : menu) {
                if (menuItem.name == orderItem.first) {
                    itemPrice = menuItem.price;
                    break;
                }
            }
            
            double itemTotal = itemPrice * orderItem.second;
            std::cout << std::left << std::setw(20) << orderItem.first
                      << std::setw(10) << orderItem.second
                      << "$" << std::fixed << std::setprecision(2) << std::setw(9) << itemPrice
                      << "$" << std::setw(10) << itemTotal << std::endl;
        }
        
        std::cout << std::string(50, '-') << std::endl;
        std::cout << "Total Amount: $" << totalAmount << std::endl;
    }
    
    void removeItemFromOrder() {
        if (order.empty()) {
            std::cout << "Your order is empty." << std::endl;
            return;
        }
        
        std::string itemName;
        std::cout << "Enter item name to remove: ";
        std::cin.ignore();
        std::getline(std::cin, itemName);
        
        bool found = false;
        for (auto it = order.begin(); it != order.end(); ++it) {
            if (it->first == itemName) {
                found = true;
                
                // Find price to update total
                double itemPrice = 0;
                for (const auto& menuItem : menu) {
                    if (menuItem.name == itemName) {
                        itemPrice = menuItem.price;
                        break;
                    }
                }
                
                totalAmount -= itemPrice * it->second;
                order.erase(it);
                
                std::cout << "Removed " << itemName << " from order." << std::endl;
                std::cout << "New total: $" << totalAmount << std::endl;
                break;
            }
        }
        
        if (!found) {
            std::cout << "Item '" << itemName << "' not found in order." << std::endl;
        }
    }
    
    void checkout() {
        if (order.empty()) {
            std::cout << "Your order is empty. Cannot checkout." << std::endl;
            return;
        }
        
        viewOrder();
        
        char confirm;
        std::cout << "\nConfirm order? (y/n): ";
        std::cin >> confirm;
        
        if (confirm == 'y' || confirm == 'Y') {
            // Apply discounts based on total
            double finalAmount = totalAmount;
            std::string discount = "";
            
            if (totalAmount > 50) {
                finalAmount *= 0.9; // 10% discount
                discount = "10% discount applied";
            } else if (totalAmount > 30) {
                finalAmount *= 0.95; // 5% discount
                discount = "5% discount applied";
            }
            
            std::cout << "\n=== CHECKOUT ===" << std::endl;
            std::cout << "Subtotal: $" << totalAmount << std::endl;
            if (!discount.empty()) {
                std::cout << discount << std::endl;
            }
            std::cout << "Final Amount: $" << finalAmount << std::endl;
            
            // Payment simulation
            double payment;
            std::cout << "Enter payment amount: $";
            std::cin >> payment;
            
            if (payment >= finalAmount) {
                std::cout << "Payment successful! Change: $" << (payment - finalAmount) << std::endl;
                std::cout << "Thank you for your order!" << std::endl;
                
                // Clear order
                order.clear();
                totalAmount = 0;
            } else {
                std::cout << "Insufficient payment. Order cancelled." << std::endl;
            }
        } else {
            std::cout << "Order cancelled." << std::endl;
        }
    }
};

int main() {
    std::cout << "=== Input/Output and Control Flow - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of I/O and control structures\n" << std::endl;
    
    int choice;
    
    do {
        std::cout << "\n=== MAIN MENU ===" << std::endl;
        std::cout << "1. ATM Simulator" << std::endl;
        std::cout << "2. Student Registration System" << std::endl;
        std::cout << "3. Restaurant Order System" << std::endl;
        std::cout << "4. Exit" << std::endl;
        std::cout << "Enter your choice (1-4): ";
        std::cin >> choice;
        
        switch (choice) {
            case 1: {
                std::cout << "\n=== ATM SIMULATOR ===" << std::endl;
                ATMSimulator atm("John Doe", 5000.0, 1234);
                if (atm.authenticate()) {
                    atm.displayMenu();
                }
                break;
            }
            case 2: {
                std::cout << "\n=== STUDENT REGISTRATION ===" << std::endl;
                StudentRegistration registration;
                registration.registerForCourse();
                registration.displayRegistrationSummary();
                break;
            }
            case 3: {
                std::cout << "\n=== RESTAURANT ORDER SYSTEM ===" << std::endl;
                RestaurantOrderSystem restaurant;
                restaurant.takeOrder();
                break;
            }
            case 4:
                std::cout << "Thank you for using this demo!" << std::endl;
                break;
            default:
                std::cout << "Invalid choice. Please enter a number between 1 and 4." << std::endl;
        }
    } while (choice != 4);
    
    std::cout << "\n\n=== CONTROL FLOW SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various control structures:" << std::endl;
    std::cout << "• if-else statements: Authentication, validation, business logic" << std::endl;
    std::cout << "• switch statements: Menu navigation and selection" << std::endl;
    std::cout << "• while loops: Authentication attempts, menu loops" << std::endl;
    std::cout << "• for loops: Iterating through collections" << std::endl;
    std::cout << "• do-while loops: Menu-driven interfaces" << std::endl;
    std::cout << "• break and continue: Loop control" << std::endl;
    std::cout << "• Input validation: Ensuring proper user input" << std::endl;
    std::cout << "\nControl flow structures are essential for creating interactive applications!" << std::endl;
    
    return 0;
}