// Module 5: Functions and Program Organization - Real-Life Examples
// This file demonstrates practical applications of functions and program organization

#include <iostream>
#include <string>
#include <vector>
#include <cmath>
#include <algorithm>
#include <iomanip>

// Example 1: Financial Calculator Library
namespace Finance {
    // Utility functions for financial calculations
    
    double calculateCompoundInterest(double principal, double rate, int years, int compoundsPerYear = 1) {
        return principal * std::pow(1 + (rate / compoundsPerYear), compoundsPerYear * years);
    }
    
    double calculateLoanPayment(double principal, double annualRate, int years) {
        double monthlyRate = annualRate / 12;
        int totalMonths = years * 12;
        return principal * (monthlyRate * std::pow(1 + monthlyRate, totalMonths)) / 
                          (std::pow(1 + monthlyRate, totalMonths) - 1);
    }
    
    double calculateRetirementSavings(double currentAge, double retirementAge, 
                                     double currentSavings, double monthlyContribution, 
                                     double annualReturn = 0.07) {
        int yearsToRetirement = retirementAge - currentAge;
        double futureValue = currentSavings;
        
        for (int year = 0; year < yearsToRetirement; ++year) {
            futureValue = futureValue * (1 + annualReturn) + (monthlyContribution * 12);
        }
        
        return futureValue;
    }
    
    // Function overloading example
    std::string formatCurrency(double amount) {
        return "$" + std::to_string(static_cast<int>(amount * 100 + 0.5) / 100.0);
    }
    
    std::string formatCurrency(double amount, std::string currency) {
        return currency + std::to_string(static_cast<int>(amount * 100 + 0.5) / 100.0);
    }
    
    // Recursive function for investment growth projection
    double projectInvestmentGrowth(double initialAmount, double annualGrowth, int years) {
        if (years == 0) return initialAmount;
        return projectInvestmentGrowth(initialAmount * (1 + annualGrowth), annualGrowth, years - 1);
    }
}

// Example 2: Healthcare Management System
class PatientManager {
private:
    struct Patient {
        int id;
        std::string name;
        int age;
        double weight; // kg
        double height; // meters
        std::string bloodType;
        std::vector<std::string> medications;
        std::vector<std::string> allergies;
    };
    
    std::vector<Patient> patients;
    
public:
    // Function templates for generic operations
    template<typename T>
    T findMax(std::vector<T> values) {
        if (values.empty()) return T();
        
        T maxVal = values[0];
        for (const T& val : values) {
            if (val > maxVal) {
                maxVal = val;
            }
        }
        return maxVal;
    }
    
    template<typename T>
    T calculateAverage(std::vector<T> values) {
        if (values.empty()) return T();
        
        T sum = T();
        for (const T& val : values) {
            sum += val;
        }
        return sum / values.size();
    }
    
    // Patient management functions
    void addPatient(int id, std::string name, int age, double weight, double height, std::string bloodType) {
        patients.push_back({id, name, age, weight, height, bloodType, {}, {}});
        std::cout << "Patient " << name << " added to system." << std::endl;
    }
    
    double calculateBMI(double weight, double height) {
        return weight / (height * height);
    }
    
    std::string getBMICategory(double bmi) {
        if (bmi < 18.5) return "Underweight";
        else if (bmi < 25) return "Normal weight";
        else if (bmi < 30) return "Overweight";
        else return "Obese";
    }
    
    void addMedication(int patientId, std::string medication) {
        for (auto& patient : patients) {
            if (patient.id == patientId) {
                patient.medications.push_back(medication);
                std::cout << "Medication added for patient " << patient.name << std::endl;
                return;
            }
        }
        std::cout << "Patient ID " << patientId << " not found." << std::endl;
    }
    
    void addAllergy(int patientId, std::string allergy) {
        for (auto& patient : patients) {
            if (patient.id == patientId) {
                patient.allergies.push_back(allergy);
                std::cout << "Allergy recorded for patient " << patient.name << std::endl;
                return;
            }
        }
        std::cout << "Patient ID " << patientId << " not found." << std::endl;
    }
    
    void displayPatientReport(int patientId) {
        for (const auto& patient : patients) {
            if (patient.id == patientId) {
                double bmi = calculateBMI(patient.weight, patient.height);
                
                std::cout << "\n=== PATIENT REPORT ===" << std::endl;
                std::cout << "ID: " << patient.id << std::endl;
                std::cout << "Name: " << patient.name << std::endl;
                std::cout << "Age: " << patient.age << std::endl;
                std::cout << "Weight: " << patient.weight << " kg" << std::endl;
                std::cout << "Height: " << patient.height << " m" << std::endl;
                std::cout << "BMI: " << std::fixed << std::setprecision(1) << bmi << std::endl;
                std::cout << "Category: " << getBMICategory(bmi) << std::endl;
                std::cout << "Blood Type: " << patient.bloodType << std::endl;
                
                std::cout << "\nMedications:" << std::endl;
                if (patient.medications.empty()) {
                    std::cout << "None" << std::endl;
                } else {
                    for (const auto& med : patient.medications) {
                        std::cout << "  - " << med << std::endl;
                    }
                }
                
                std::cout << "\nAllergies:" << std::endl;
                if (patient.allergies.empty()) {
                    std::cout << "None" << std::endl;
                } else {
                    for (const auto& allergy : patient.allergies) {
                        std::cout << "  - " << allergy << std::endl;
                    }
                }
                
                return;
            }
        }
        std::cout << "Patient ID " << patientId << " not found." << std::endl;
    }
    
    void displayStatistics() {
        if (patients.empty()) {
            std::cout << "No patients in system." << std::endl;
            return;
        }
        
        std::vector<int> ages;
        std::vector<double> bmis;
        
        for (const auto& patient : patients) {
            ages.push_back(patient.age);
            bmis.push_back(calculateBMI(patient.weight, patient.height));
        }
        
        std::cout << "\n=== CLINIC STATISTICS ===" << std::endl;
        std::cout << "Total Patients: " << patients.size() << std::endl;
        std::cout << "Average Age: " << calculateAverage(ages) << std::endl;
        std::cout << "Average BMI: " << std::fixed << std::setprecision(1) << calculateAverage(bmis) << std::endl;
        std::cout << "Oldest Patient: " << findMax(ages) << " years" << std::endl;
    }
};

// Example 3: E-commerce Order Processing
class OrderProcessor {
private:
    struct Product {
        int id;
        std::string name;
        double price;
        int stock;
        double weight;
    };
    
    struct Order {
        int orderId;
        std::vector<std::pair<int, int>> items; // product id, quantity
        std::string status;
        double totalAmount;
        double shippingCost;
        std::string shippingAddress;
    };
    
    std::vector<Product> products;
    std::vector<Order> orders;
    int nextOrderId;
    
public:
    OrderProcessor() : nextOrderId(1001) {
        initializeProducts();
    }
    
    void initializeProducts() {
        products = {
            {1, "Laptop", 999.99, 25, 2.5},
            {2, "Mouse", 29.99, 100, 0.2},
            {3, "Keyboard", 79.99, 50, 1.0},
            {4, "Monitor", 299.99, 30, 5.0},
            {5, "Headphones", 149.99, 40, 0.5}
        };
    }
    
    // Higher-order function example
    std::function<bool(int)> createStockChecker(int minStock) {
        return [this, minStock](int productId) {
            for (const auto& product : products) {
                if (product.id == productId) {
                    return product.stock >= minStock;
                }
            }
            return false;
        };
    }
    
    // Lambda function for price calculation
    auto calculateDiscount = [](double originalPrice, double discountPercentage) {
        return originalPrice * (1 - discountPercentage / 100);
    };
    
    bool createOrder(std::vector<std::pair<int, int>> items, std::string address) {
        Order newOrder;
        newOrder.orderId = nextOrderId++;
        newOrder.items = items;
        newOrder.shippingAddress = address;
        newOrder.status = "Processing";
        
        // Validate all items are in stock
        for (const auto& item : items) {
            bool found = false;
            for (const auto& product : products) {
                if (product.id == item.first) {
                    found = true;
                    if (product.stock < item.second) {
                        std::cout << "Insufficient stock for product ID " << item.first << std::endl;
                        return false;
                    }
                    break;
                }
            }
            if (!found) {
                std::cout << "Product ID " << item.first << " not found." << std::endl;
                return false;
            }
        }
        
        // Calculate total amount
        double subtotal = 0.0;
        double totalWeight = 0.0;
        
        for (const auto& item : items) {
            for (const auto& product : products) {
                if (product.id == item.first) {
                    subtotal += product.price * item.second;
                    totalWeight += product.weight * item.second;
                    break;
                }
            }
        }
        
        // Apply bulk discount
        if (subtotal > 1000) {
            subtotal = calculateDiscount(subtotal, 10); // 10% discount
        } else if (subtotal > 500) {
            subtotal = calculateDiscount(subtotal, 5); // 5% discount
        }
        
        // Calculate shipping
        newOrder.shippingCost = calculateShippingCost(totalWeight, subtotal);
        newOrder.totalAmount = subtotal + newOrder.shippingCost;
        
        orders.push_back(newOrder);
        
        // Update stock
        for (const auto& item : items) {
            for (auto& product : products) {
                if (product.id == item.first) {
                    product.stock -= item.second;
                    break;
                }
            }
        }
        
        std::cout << "Order " << newOrder.orderId << " created successfully!" << std::endl;
        return true;
    }
    
    double calculateShippingCost(double weight, double orderValue) {
        // Free shipping for orders over $100
        if (orderValue > 100) {
            return 0.0;
        }
        
        // Base shipping + weight-based cost
        double baseCost = 5.99;
        double weightCost = weight * 2.0; // $2 per kg
        
        return baseCost + weightCost;
    }
    
    void displayOrderDetails(int orderId) {
        for (const auto& order : orders) {
            if (order.orderId == orderId) {
                std::cout << "\n=== ORDER DETAILS ===" << std::endl;
                std::cout << "Order ID: " << order.orderId << std::endl;
                std::cout << "Status: " << order.status << std::endl;
                std::cout << "Shipping Address: " << order.shippingAddress << std::endl;
                
                std::cout << "\nItems:" << std::endl;
                for (const auto& item : order.items) {
                    for (const auto& product : products) {
                        if (product.id == item.first) {
                            std::cout << "  " << product.name << " x" << item.second 
                                      << " @ $" << product.price << " each" << std::endl;
                            break;
                        }
                    }
                }
                
                std::cout << "\nSubtotal: $" << (order.totalAmount - order.shippingCost) << std::endl;
                std::cout << "Shipping: $" << order.shippingCost << std::endl;
                std::cout << "Total: $" << order.totalAmount << std::endl;
                return;
            }
        }
        std::cout << "Order " << orderId << " not found." << std::endl;
    }
    
    void updateOrderStatus(int orderId, std::string newStatus) {
        for (auto& order : orders) {
            if (order.orderId == orderId) {
                order.status = newStatus;
                std::cout << "Order " << orderId << " status updated to: " << newStatus << std::endl;
                return;
            }
        }
        std::cout << "Order " << orderId << " not found." << std::endl;
    }
    
    void displayInventoryReport() {
        std::cout << "\n=== INVENTORY REPORT ===" << std::endl;
        std::cout << std::left << std::setw(8) << "ID" 
                  << std::setw(20) << "Name" 
                  << std::setw(10) << "Price" 
                  << std::setw(8) << "Stock" 
                  << "Weight" << std::endl;
        std::cout << std::string(60, '-') << std::endl;
        
        for (const auto& product : products) {
            std::cout << std::left << std::setw(8) << product.id
                      << std::setw(20) << product.name
                      << std::setw(10) << std::fixed << std::setprecision(2) << product.price
                      << std::setw(8) << product.stock
                      << product.weight << " kg" << std::endl;
        }
        
        // Check for low stock
        auto stockChecker = createStockChecker(10);
        std::cout << "\nLow Stock Alert:" << std::endl;
        for (const auto& product : products) {
            if (!stockChecker(product.id)) {
                std::cout << "  " << product.name << " (ID: " << product.id 
                          << ") - Only " << product.stock << " left" << std::endl;
            }
        }
    }
};

// Example 4: Utility Functions Library
namespace Utils {
    // String manipulation functions
    std::string capitalize(std::string text) {
        if (text.empty()) return text;
        
        text[0] = std::toupper(text[0]);
        for (size_t i = 1; i < text.length(); ++i) {
            if (text[i-1] == ' ') {
                text[i] = std::toupper(text[i]);
            } else {
                text[i] = std::tolower(text[i]);
            }
        }
        return text;
    }
    
    std::vector<std::string> splitString(std::string text, char delimiter) {
        std::vector<std::string> result;
        std::string current;
        
        for (char c : text) {
            if (c == delimiter) {
                result.push_back(current);
                current.clear();
            } else {
                current += c;
            }
        }
        
        if (!current.empty()) {
            result.push_back(current);
        }
        
        return result;
    }
    
    // Validation functions
    bool isValidEmail(std::string email) {
        size_t atPos = email.find('@');
        size_t dotPos = email.find('.', atPos);
        
        return atPos != std::string::npos && 
               dotPos != std::string::npos && 
               atPos > 0 && 
               dotPos > atPos + 1;
    }
    
    bool isValidPhoneNumber(std::string phone) {
        // Simple validation: check if phone contains only digits and optional symbols
        for (char c : phone) {
            if (!std::isdigit(c) && c != '-' && c != '(' && c != ')' && c != ' ') {
                return false;
            }
        }
        return phone.length() >= 10;
    }
    
    // Mathematical utility functions
    double roundToDecimal(double value, int decimals) {
        double factor = std::pow(10, decimals);
        return std::round(value * factor) / factor;
    }
    
    bool isEven(int number) {
        return number % 2 == 0;
    }
    
    bool isPrime(int number) {
        if (number <= 1) return false;
        if (number == 2) return true;
        if (number % 2 == 0) return false;
        
        for (int i = 3; i <= std::sqrt(number); i += 2) {
            if (number % i == 0) return false;
        }
        return true;
    }
}

int main() {
    std::cout << "=== Functions and Program Organization - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of functions and organization\n" << std::endl;
    
    // Example 1: Financial Calculator
    std::cout << "=== FINANCIAL CALCULATOR ===" << std::endl;
    std::cout << "Investment: $" << Finance::calculateCompoundInterest(10000, 0.07, 10, 12) << std::endl;
    std::cout << "Loan Payment: $" << Finance::calculateLoanPayment(200000, 0.04, 30) << std::endl;
    std::cout << "Retirement: $" << Finance::calculateRetirementSavings(25, 65, 5000, 500) << std::endl;
    std::cout << "Projected Growth: $" << Finance::projectInvestmentGrowth(1000, 0.08, 5) << std::endl;
    
    // Example 2: Healthcare Management
    std::cout << "\n=== HEALTHCARE MANAGEMENT ===" << std::endl;
    PatientManager clinic;
    clinic.addPatient(1001, "John Smith", 35, 80.5, 1.75, "O+");
    clinic.addPatient(1002, "Jane Doe", 28, 65.2, 1.68, "A+");
    clinic.addPatient(1003, "Bob Johnson", 45, 95.0, 1.82, "B+");
    
    clinic.addMedication(1001, "Lisinopril");
    clinic.addMedication(1001, "Metformin");
    clinic.addAllergy(1001, "Penicillin");
    clinic.addAllergy(1002, "Peanuts");
    
    clinic.displayPatientReport(1001);
    clinic.displayStatistics();
    
    // Example 3: E-commerce Order Processing
    std::cout << "\n=== E-COMMERCE ORDER PROCESSING ===" << std::endl;
    OrderProcessor processor;
    
    std::vector<std::pair<int, int>> order1 = {{1, 1}, {2, 2}, {3, 1}};
    processor.createOrder(order1, "123 Main St, City, State");
    
    std::vector<std::pair<int, int>> order2 = {{4, 2}, {5, 1}};
    processor.createOrder(order2, "456 Oak Ave, Town, State");
    
    processor.displayOrderDetails(1001);
    processor.updateOrderStatus(1001, "Shipped");
    processor.displayInventoryReport();
    
    // Example 4: Utility Functions
    std::cout << "\n=== UTILITY FUNCTIONS ===" << std::endl;
    std::string text = "hello world from c++";
    std::cout << "Original: " << text << std::endl;
    std::cout << "Capitalized: " << Utils::capitalize(text) << std::endl;
    
    std::string email = "user@example.com";
    std::cout << "Email " << email << " is " << (Utils::isValidEmail(email) ? "valid" : "invalid") << std::endl;
    
    std::string phone = "(555) 123-4567";
    std::cout << "Phone " << phone << " is " << (Utils::isValidPhoneNumber(phone) ? "valid" : "invalid") << std::endl;
    
    double value = 3.14159;
    std::cout << "Rounded: " << Utils::roundToDecimal(value, 2) << std::endl;
    
    std::cout << "17 is " << (Utils::isPrime(17) ? "prime" : "not prime") << std::endl;
    std::cout << "18 is " << (Utils::isEven(18) ? "even" : "odd") << std::endl;
    
    // String splitting example
    std::string sentence = "C++ is a powerful programming language";
    auto words = Utils::splitString(sentence, ' ');
    std::cout << "Words in sentence: ";
    for (const auto& word : words) {
        std::cout << "[" << word << "] ";
    }
    std::cout << std::endl;
    
    std::cout << "\n\n=== FUNCTIONS SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various function concepts:" << std::endl;
    std::cout << "• Namespaces: Organizing related functions" << std::endl;
    std::cout << "• Function overloading: Multiple versions with different parameters" << std::endl;
    std::cout << "• Template functions: Generic operations on different types" << std::endl;
    std::cout << "• Recursive functions: Self-referential calculations" << std::endl;
    std::cout << "• Lambda functions: Anonymous function objects" << std::endl;
    std::cout << "• Higher-order functions: Functions that return functions" << std::endl;
    std::cout << "• Utility libraries: Reusable helper functions" << std::endl;
    std::cout << "\nFunctions are essential for creating modular, reusable, and organized code!" << std::endl;
    
    return 0;
}