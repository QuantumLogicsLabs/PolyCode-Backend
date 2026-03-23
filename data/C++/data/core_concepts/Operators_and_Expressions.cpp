// Module 3: Operators and Expressions - Real-Life Examples
// This file demonstrates practical applications of operators and expressions

#include <iostream>
#include <string>
#include <vector>
#include <cmath>

// Example 1: Shopping Cart Calculator
class ShoppingCart {
private:
    struct Item {
        std::string name;
        double price;
        int quantity;
        double weight; // in kg
        bool isTaxable;
    };
    
    std::vector<Item> items;
    const double TAX_RATE = 0.08; // 8% sales tax
    const double SHIPPING_RATE = 5.99; // Flat shipping rate
    
public:
    void addItem(std::string name, double price, int qty, double weight, bool taxable) {
        items.push_back({name, price, qty, weight, taxable});
        std::cout << "Added to cart: " << name << " (" << qty << "x $" << price << ")" << std::endl;
    }
    
    double calculateSubtotal() {
        double subtotal = 0.0;
        for (const auto& item : items) {
            subtotal += item.price * item.quantity; // Arithmetic operator *
        }
        return subtotal;
    }
    
    double calculateTax() {
        double taxableAmount = 0.0;
        for (const auto& item : items) {
            if (item.isTaxable) {
                taxableAmount += item.price * item.quantity;
            }
        }
        return taxableAmount * TAX_RATE; // Arithmetic operator *
    }
    
    double calculateShipping() {
        double totalWeight = 0.0;
        for (const auto& item : items) {
            totalWeight += item.weight * item.quantity;
        }
        
        // Free shipping for orders over $50 or weight over 10kg
        double subtotal = calculateSubtotal();
        if (subtotal > 50.0 || totalWeight > 10.0) {
            return 0.0; // Logical operator OR
        }
        return SHIPPING_RATE;
    }
    
    void displayCartSummary() {
        double subtotal = calculateSubtotal();
        double tax = calculateTax();
        double shipping = calculateShipping();
        double total = subtotal + tax + shipping; // Arithmetic operators + and +
        
        std::cout << "\n=== SHOPPING CART SUMMARY ===" << std::endl;
        std::cout << "Subtotal: $" << subtotal << std::endl;
        std::cout << "Tax: $" << tax << std::endl;
        std::cout << "Shipping: $" << shipping << std::endl;
        std::cout << "Total: $" << total << std::endl;
        
        // Comparison operators
        std::cout << "\nOrder Status: ";
        if (total > 100.0) {
            std::cout << "Large order - Eligible for VIP discount!" << std::endl;
        } else if (total >= 50.0 && total <= 100.0) {
            std::cout << "Medium order - Standard processing" << std::endl;
        } else {
            std::cout << "Small order - Express processing" << std::endl;
        }
    }
};

// Example 2: Fitness Tracker
class FitnessTracker {
private:
    int steps;
    double distance; // in km
    int activeMinutes;
    int heartRate;
    double caloriesBurned;
    bool isWorkoutComplete;
    
public:
    FitnessTracker() : steps(0), distance(0.0), activeMinutes(0), 
                      heartRate(70), caloriesBurned(0.0), isWorkoutComplete(false) {}
    
    void addSteps(int newSteps, double stepDistance = 0.0008) { // Average step length
        steps += newSteps; // Compound assignment operator +=
        distance += newSteps * stepDistance; // Arithmetic operators
        caloriesBurned += newSteps * 0.04; // Approximate calories per step
    }
    
    void addActivity(int minutes, int avgHeartRate) {
        activeMinutes += minutes;
        heartRate = (heartRate + avgHeartRate) / 2; // Arithmetic operators
        
        // Calculate calories based on heart rate and duration
        if (avgHeartRate > 120) {
            caloriesBurned += minutes * 8.5; // High intensity
        } else if (avgHeartRate > 100) {
            caloriesBurned += minutes * 5.5; // Medium intensity
        } else {
            caloriesBurned += minutes * 3.5; // Low intensity
        }
    }
    
    bool checkGoalProgress(int dailyStepGoal = 10000, int dailyMinuteGoal = 30) {
        // Logical operators AND
        isWorkoutComplete = (steps >= dailyStepGoal) && (activeMinutes >= dailyMinuteGoal);
        return isWorkoutComplete;
    }
    
    void displayFitnessReport() {
        std::cout << "\n=== FITNESS REPORT ===" << std::endl;
        std::cout << "Steps: " << steps << std::endl;
        std::cout << "Distance: " << std::fixed << std::setprecision(2) << distance << " km" << std::endl;
        std::cout << "Active Minutes: " << activeMinutes << std::endl;
        std::cout << "Average Heart Rate: " << heartRate << " bpm" << std::endl;
        std::cout << "Calories Burned: " << std::fixed << std::setprecision(1) << caloriesBurned << std::endl;
        
        // Conditional operators
        std::cout << "Workout Status: " << (isWorkoutComplete ? "✓ Complete" : "✗ Incomplete") << std::endl;
        
        // Ternary operator for achievement level
        std::string achievement = (steps > 15000) ? "Excellent" : 
                                 (steps > 10000) ? "Good" : 
                                 (steps > 5000) ? "Fair" : "Needs Improvement";
        std::cout << "Achievement Level: " << achievement << std::endl;
    }
};

// Example 3: Investment Calculator
class InvestmentCalculator {
private:
    double principal;
    double annualRate;
    int years;
    bool compoundMonthly;
    
public:
    InvestmentCalculator(double initial, double rate, int yrs, bool monthly = true)
        : principal(initial), annualRate(rate), years(yrs), compoundMonthly(monthly) {}
    
    double calculateFutureValue() {
        double futureValue = principal;
        
        if (compoundMonthly) {
            // Compound interest formula: A = P(1 + r/n)^(nt)
            double monthlyRate = annualRate / 12; // Division operator
            int totalMonths = years * 12; // Multiplication operator
            
            for (int i = 0; i < totalMonths; ++i) {
                futureValue = futureValue * (1 + monthlyRate); // Compound assignment
            }
        } else {
            // Simple interest: A = P(1 + rt)
            futureValue = principal * (1 + (annualRate * years));
        }
        
        return futureValue;
    }
    
    double calculateTotalInterest() {
        return calculateFutureValue() - principal; // Subtraction operator
    }
    
    void compareInvestments() {
        double simpleValue = principal * (1 + (annualRate * years));
        double compoundValue = calculateFutureValue();
        
        std::cout << "\n=== INVESTMENT COMPARISON ===" << std::endl;
        std::cout << "Principal: $" << principal << std::endl;
        std::cout << "Annual Rate: " << (annualRate * 100) << "%" << std::endl;
        std::cout << "Years: " << years << std::endl;
        std::cout << "Simple Interest Value: $" << simpleValue << std::endl;
        std::cout << "Compound Interest Value: $" << compoundValue << std::endl;
        
        // Comparison operators
        double difference = compoundValue - simpleValue;
        std::cout << "Difference: $" << difference << std::endl;
        
        if (compoundValue > simpleValue * 1.5) {
            std::cout << "Compound interest significantly outperforms simple interest!" << std::endl;
        } else if (compoundValue > simpleValue) {
            std::cout << "Compound interest provides better returns." << std::endl;
        }
    }
};

// Example 4: Grade Calculator with Bitwise Operations
class GradeCalculator {
private:
    int grades; // Using bits to store different grade categories
    
public:
    GradeCalculator() : grades(0) {}
    
    // Use bitwise operators to set grade flags
    void setGradeFlag(int gradeType) {
        grades |= (1 << gradeType); // Bitwise OR and left shift
    }
    
    void clearGradeFlag(int gradeType) {
        grades &= ~(1 << gradeType); // Bitwise AND, NOT, and left shift
    }
    
    bool hasGradeType(int gradeType) {
        return (grades & (1 << gradeType)) != 0; // Bitwise AND
    }
    
    void calculateGPA() {
        // Simulate different grade categories
        // 0: A, 1: B, 2: C, 3: D, 4: F
        int gradeCount = 0;
        double totalPoints = 0.0;
        
        for (int i = 0; i < 5; ++i) {
            if (hasGradeType(i)) {
                gradeCount++;
                switch (i) {
                    case 0: totalPoints += 4.0; break; // A
                    case 1: totalPoints += 3.0; break; // B
                    case 2: totalPoints += 2.0; break; // C
                    case 3: totalPoints += 1.0; break; // D
                    case 4: totalPoints += 0.0; break; // F
                }
            }
        }
        
        if (gradeCount > 0) {
            double gpa = totalPoints / gradeCount;
            std::cout << "GPA: " << std::fixed << std::setprecision(2) << gpa << std::endl;
            
            // Use modulus and division operators
            std::cout << "Grade Summary: ";
            std::cout << "Total Grades: " << gradeCount << std::endl;
            std::cout << "Grade Point Total: " << totalPoints << std::endl;
        }
    }
    
    void displayGradeFlags() {
        std::cout << "Grade Categories: ";
        for (int i = 0; i < 5; ++i) {
            if (hasGradeType(i)) {
                char grade = 'A' + i;
                std::cout << grade << " ";
            }
        }
        std::cout << std::endl;
    }
};

int main() {
    std::cout << "=== Operators and Expressions - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of various operators\n" << std::endl;
    
    // Example 1: Shopping Cart Calculator
    std::cout << "=== SHOPPING CART CALCULATOR ===" << std::endl;
    ShoppingCart cart;
    cart.addItem("Laptop", 999.99, 1, 2.5, true);
    cart.addItem("Mouse", 29.99, 1, 0.2, true);
    cart.addItem("Book", 15.99, 3, 1.8, false);
    cart.addItem("Coffee", 8.99, 2, 0.5, false);
    cart.displayCartSummary();
    
    // Example 2: Fitness Tracker
    std::cout << "\n=== FITNESS TRACKER ===" << std::endl;
    FitnessTracker tracker;
    tracker.addSteps(2500); // Morning walk
    tracker.addActivity(15, 110); // Light exercise
    tracker.addSteps(3500); // Lunch break walk
    tracker.addActivity(20, 125); // Run
    tracker.addSteps(4000); // Evening walk
    tracker.addActivity(10, 95); // Cool down
    
    bool goalMet = tracker.checkGoalProgress();
    tracker.displayFitnessReport();
    
    // Example 3: Investment Calculator
    std::cout << "\n=== INVESTMENT CALCULATOR ===" << std::endl;
    InvestmentCalculator investment1(10000.0, 0.07, 10, true); // $10k at 7% for 10 years
    investment1.compareInvestments();
    
    InvestmentCalculator investment2(5000.0, 0.05, 5, true); // $5k at 5% for 5 years
    investment2.compareInvestments();
    
    // Example 4: Grade Calculator with Bitwise Operations
    std::cout << "\n=== GRADE CALCULATOR ===" << std::endl;
    GradeCalculator grades;
    grades.setGradeFlag(0); // A
    grades.setGradeFlag(1); // B
    grades.setGradeFlag(1); // Another B (duplicate)
    grades.setGradeFlag(2); // C
    grades.setGradeFlag(0); // Another A
    
    grades.displayGradeFlags();
    grades.calculateGPA();
    
    // Demonstrate bitwise operations
    std::cout << "\nBitwise Operations Demo:" << std::endl;
    int num1 = 12;  // Binary: 1100
    int num2 = 10;  // Binary: 1010
    
    std::cout << "Bitwise AND: " << num1 << " & " << num2 << " = " << (num1 & num2) << std::endl;
    std::cout << "Bitwise OR: " << num1 << " | " << num2 << " = " << (num1 | num2) << std::endl;
    std::cout << "Bitwise XOR: " << num1 << " ^ " << num2 << " = " << (num1 ^ num2) << std::endl;
    std::cout << "Left Shift: " << num1 << " << 2 = " << (num1 << 2) << std::endl;
    std::cout << "Right Shift: " << num1 << " >> 1 = " << (num1 >> 1) << std::endl;
    
    std::cout << "\n\n=== OPERATORS SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various operators in real-world contexts:" << std::endl;
    std::cout << "• Arithmetic: Shopping cart calculations, fitness tracking" << std::endl;
    std::cout << "• Comparison: Goal checking, investment analysis" << std::endl;
    std::cout << "• Logical: Conditional logic in fitness and shopping systems" << std::endl;
    std::cout << "• Assignment: Updating values in all systems" << std::endl;
    std::cout << "• Ternary: Quick conditional decisions" << std::endl;
    std::cout << "• Bitwise: Grade flag management, low-level operations" << std::endl;
    std::cout << "\nOperators are fundamental building blocks for creating practical applications!" << std::endl;
    
    return 0;
}