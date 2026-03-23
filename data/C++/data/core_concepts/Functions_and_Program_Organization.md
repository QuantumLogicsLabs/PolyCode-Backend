# Module 5: Functions and Program Organization

## Learning Objectives
- Understand what functions are and why they're important
- Learn to declare, define, and call functions
- Master function parameters and return values
- Understand function overloading
- Learn about recursion and scope
- Organize code with header files

## What are Functions?

A function is a reusable block of code that performs a specific task. Functions help organize code, reduce repetition, and make programs more modular and maintainable.

### Basic Function Structure
```cpp
return_type function_name(parameter_list) {
    // Function body
    // Code to perform the task
    return value; // optional
}
```

## Function Declaration and Definition

### Function Declaration (Prototype)
```cpp
#include <iostream>

// Function declarations (prototypes)
int add(int a, int b);
double multiply(double x, double y);
void greet(std::string name);

int main() {
    int result1 = add(5, 3);
    double result2 = multiply(2.5, 4.0);
    greet("Alice");
    
    std::cout << "Addition result: " << result1 << std::endl;
    std::cout << "Multiplication result: " << result2 << std::endl;
    
    return 0;
}

// Function definitions
int add(int a, int b) {
    return a + b;
}

double multiply(double x, double y) {
    return x * y;
}

void greet(std::string name) {
    std::cout << "Hello, " << name << "!" << std::endl;
}
```

## Function Parameters

### Pass by Value
```cpp
#include <iostream>

// Parameters are passed by value (copies)
void modifyValue(int num) {
    num = 100;  // Only modifies the copy
    std::cout << "Inside function: " << num << std::endl;
}

int main() {
    int original = 10;
    std::cout << "Before function call: " << original << std::endl;
    
    modifyValue(original);
    
    std::cout << "After function call: " << original << std::endl;
    return 0;
}
```

### Pass by Reference
```cpp
#include <iostream>

// Parameters are passed by reference
void modifyReference(int& num) {
    num = 100;  // Modifies the original variable
    std::cout << "Inside function: " << num << std::endl;
}

void swapNumbers(int& a, int& b) {
    int temp = a;
    a = b;
    b = temp;
}

int main() {
    int original = 10;
    std::cout << "Before function call: " << original << std::endl;
    
    modifyReference(original);
    
    std::cout << "After function call: " << original << std::endl;
    
    // Example with swap function
    int x = 5, y = 10;
    std::cout << "\nBefore swap: x = " << x << ", y = " << y << std::endl;
    swapNumbers(x, y);
    std::cout << "After swap: x = " << x << ", y = " << y << std::endl;
    
    return 0;
}
```

### Pass by Constant Reference
```cpp
#include <iostream>
#include <string>

// Pass by constant reference (efficient for large objects)
void printInfo(const std::string& name, const int& age) {
    std::cout << "Name: " << name << ", Age: " << age << std::endl;
    // name = "New Name"; // Error: cannot modify const reference
}

int main() {
    std::string personName = "John Doe";
    int personAge = 25;
    
    printInfo(personName, personAge);
    
    return 0;
}
```

## Default Parameters

```cpp
#include <iostream>
#include <string>

// Function with default parameters
void displayMessage(const std::string& message, int times = 1, bool uppercase = false) {
    for (int i = 0; i < times; i++) {
        if (uppercase) {
            std::string upperMessage = message;
            for (char& c : upperMessage) {
                c = toupper(c);
            }
            std::cout << upperMessage << std::endl;
        } else {
            std::cout << message << std::endl;
        }
    }
}

int main() {
    // Using all default parameters
    displayMessage("Hello");
    
    // Overriding one default parameter
    displayMessage("Hello", 3);
    
    // Overriding all parameters
    displayMessage("Hello", 2, true);
    
    return 0;
}
```

## Function Overloading

```cpp
#include <iostream>

// Function overloading - same name, different parameters
int add(int a, int b) {
    std::cout << "Adding integers: ";
    return a + b;
}

double add(double a, double b) {
    std::cout << "Adding doubles: ";
    return a + b;
}

std::string add(const std::string& a, const std::string& b) {
    std::cout << "Concatenating strings: ";
    return a + b;
}

int add(int a, int b, int c) {
    std::cout << "Adding three integers: ";
    return a + b + c;
}

int main() {
    std::cout << add(5, 3) << std::endl;
    std::cout << add(2.5, 3.7) << std::endl;
    std::cout << add("Hello, ", "World!") << std::endl;
    std::cout << add(1, 2, 3) << std::endl;
    
    return 0;
}
```

## Inline Functions

```cpp
#include <iostream>

// Inline function - suggestion to compiler to replace function call with code
inline int square(int x) {
    return x * x;
}

inline double max(double a, double b) {
    return (a > b) ? a : b;
}

int main() {
    int num = 5;
    std::cout << "Square of " << num << " is " << square(num) << std::endl;
    
    double d1 = 3.14, d2 = 2.71;
    std::cout << "Maximum of " << d1 << " and " << d2 << " is " << max(d1, d2) << std::endl;
    
    return 0;
}
```

## Recursion

### Basic Recursion Example
```cpp
#include <iostream>

// Recursive function to calculate factorial
int factorial(int n) {
    // Base case
    if (n <= 1) {
        return 1;
    }
    // Recursive case
    return n * factorial(n - 1);
}

// Recursive function to calculate Fibonacci numbers
int fibonacci(int n) {
    if (n <= 1) {
        return n;
    }
    return fibonacci(n - 1) + fibonacci(n - 2);
}

int main() {
    int num = 5;
    std::cout << "Factorial of " << num << " is " << factorial(num) << std::endl;
    
    std::cout << "Fibonacci sequence (first 10 numbers): ";
    for (int i = 0; i < 10; i++) {
        std::cout << fibonacci(i) << " ";
    }
    std::cout << std::endl;
    
    return 0;
}
```

### Advanced Recursion: Tower of Hanoi
```cpp
#include <iostream>

void towerOfHanoi(int n, char from_rod, char to_rod, char aux_rod) {
    if (n == 1) {
        std::cout << "Move disk 1 from rod " << from_rod << " to rod " << to_rod << std::endl;
        return;
    }
    
    towerOfHanoi(n - 1, from_rod, aux_rod, to_rod);
    std::cout << "Move disk " << n << " from rod " << from_rod << " to rod " << to_rod << std::endl;
    towerOfHanoi(n - 1, aux_rod, to_rod, from_rod);
}

int main() {
    int n = 3; // Number of disks
    std::cout << "Tower of Hanoi solution for " << n << " disks:" << std::endl;
    towerOfHanoi(n, 'A', 'C', 'B');
    return 0;
}
```

## Variable Scope

### Local and Global Variables
```cpp
#include <iostream>

// Global variable
int globalVar = 100;

void demonstrateScope() {
    // Local variable
    int localVar = 50;
    
    std::cout << "Inside function - Global: " << globalVar << std::endl;
    std::cout << "Inside function - Local: " << localVar << std::endl;
    
    // Modify global variable
    globalVar = 200;
}

int main() {
    int localVar = 25; // Different from function's localVar
    
    std::cout << "Before function call - Global: " << globalVar << std::endl;
    std::cout << "Before function call - Local: " << localVar << std::endl;
    
    demonstrateScope();
    
    std::cout << "After function call - Global: " << globalVar << std::endl;
    std::cout << "After function call - Local: " << localVar << std::endl;
    
    return 0;
}
```

### Static Variables
```cpp
#include <iostream>

void counter() {
    static int count = 0; // Static variable retains its value between function calls
    count++;
    std::cout << "Function called " << count << " times" << std::endl;
}

int main() {
    for (int i = 0; i < 5; i++) {
        counter();
    }
    return 0;
}
```

## Header Files and Program Organization

### math_utils.h (Header File)
```cpp
#ifndef MATH_UTILS_H
#define MATH_UTILS_H

// Function declarations
int add(int a, int b);
int subtract(int a, int b);
int multiply(int a, int b);
double divide(double a, double b);
int power(int base, int exponent);

#endif // MATH_UTILS_H
```

### math_utils.cpp (Implementation File)
```cpp
#include "math_utils.h"
#include <stdexcept>

int add(int a, int b) {
    return a + b;
}

int subtract(int a, int b) {
    return a - b;
}

int multiply(int a, int b) {
    return a * b;
}

double divide(double a, double b) {
    if (b == 0.0) {
        throw std::runtime_error("Division by zero!");
    }
    return a / b;
}

int power(int base, int exponent) {
    if (exponent < 0) {
        return 0; // Simplified for this example
    }
    
    int result = 1;
    for (int i = 0; i < exponent; i++) {
        result *= base;
    }
    return result;
}
```

### main.cpp (Main Program)
```cpp
#include <iostream>
#include "math_utils.h"

int main() {
    std::cout << "Math Operations:" << std::endl;
    std::cout << "5 + 3 = " << add(5, 3) << std::endl;
    std::cout << "10 - 4 = " << subtract(10, 4) << std::endl;
    std::cout << "6 * 7 = " << multiply(6, 7) << std::endl;
    std::cout << "15 / 3 = " << divide(15.0, 3.0) << std::endl;
    std::cout << "2^5 = " << power(2, 5) << std::endl;
    
    return 0;
}
```

## Complete Example: Student Management System

### student.h
```cpp
#ifndef STUDENT_H
#define STUDENT_H

#include <string>
#include <vector>

struct Student {
    std::string name;
    int age;
    double gpa;
    std::vector<int> grades;
};

// Function declarations
void addStudent(std::vector<Student>& students);
void displayStudents(const std::vector<Student>& students);
double calculateAverage(const Student& student);
char getGradeLetter(double gpa);
Student findTopStudent(const std::vector<Student>& students);

#endif // STUDENT_H
```

### student.cpp
```cpp
#include "student.h"
#include <iostream>
#include <algorithm>

void addStudent(std::vector<Student>& students) {
    Student newStudent;
    
    std::cout << "Enter student name: ";
    std::cin.ignore();
    std::getline(std::cin, newStudent.name);
    
    std::cout << "Enter student age: ";
    std::cin >> newStudent.age;
    
    std::cout << "Enter number of grades: ";
    int numGrades;
    std::cin >> numGrades;
    
    std::cout << "Enter " << numGrades << " grades: ";
    for (int i = 0; i < numGrades; i++) {
        int grade;
        std::cin >> grade;
        newStudent.grades.push_back(grade);
    }
    
    newStudent.gpa = calculateAverage(newStudent);
    students.push_back(newStudent);
    
    std::cout << "Student added successfully!" << std::endl;
}

void displayStudents(const std::vector<Student>& students) {
    if (students.empty()) {
        std::cout << "No students to display." << std::endl;
        return;
    }
    
    std::cout << "\n=== Student List ===" << std::endl;
    for (const auto& student : students) {
        std::cout << "Name: " << student.name << std::endl;
        std::cout << "Age: " << student.age << std::endl;
        std::cout << "GPA: " << student.gpa << " (" << getGradeLetter(student.gpa) << ")" << std::endl;
        std::cout << "Grades: ";
        for (int grade : student.grades) {
            std::cout << grade << " ";
        }
        std::cout << "\n" << std::endl;
    }
}

double calculateAverage(const Student& student) {
    if (student.grades.empty()) {
        return 0.0;
    }
    
    double sum = 0.0;
    for (int grade : student.grades) {
        sum += grade;
    }
    return sum / student.grades.size();
}

char getGradeLetter(double gpa) {
    if (gpa >= 90) return 'A';
    if (gpa >= 80) return 'B';
    if (gpa >= 70) return 'C';
    if (gpa >= 60) return 'D';
    return 'F';
}

Student findTopStudent(const std::vector<Student>& students) {
    if (students.empty()) {
        return Student{"", 0, 0.0, {}};
    }
    
    return *std::max_element(students.begin(), students.end(),
                           [](const Student& a, const Student& b) {
                               return a.gpa < b.gpa;
                           });
}
```

### main.cpp
```cpp
#include <iostream>
#include <vector>
#include "student.h"

void displayMenu() {
    std::cout << "\n=== Student Management System ===" << std::endl;
    std::cout << "1. Add Student" << std::endl;
    std::cout << "2. Display All Students" << std::endl;
    std::cout << "3. Find Top Student" << std::endl;
    std::cout << "4. Exit" << std::endl;
    std::cout << "Enter your choice: ";
}

int main() {
    std::vector<Student> students;
    int choice;
    
    do {
        displayMenu();
        std::cin >> choice;
        
        switch (choice) {
            case 1:
                addStudent(students);
                break;
            case 2:
                displayStudents(students);
                break;
            case 3: {
                Student topStudent = findTopStudent(students);
                if (!topStudent.name.empty()) {
                    std::cout << "\nTop Student:" << std::endl;
                    std::cout << "Name: " << topStudent.name << std::endl;
                    std::cout << "GPA: " << topStudent.gpa << std::endl;
                } else {
                    std::cout << "No students found." << std::endl;
                }
                break;
            }
            case 4:
                std::cout << "Exiting program..." << std::endl;
                break;
            default:
                std::cout << "Invalid choice!" << std::endl;
        }
    } while (choice != 4);
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Math Library
Create a math library with functions for:
- Power calculation
- Square root
- Absolute value
- Maximum/minimum of two numbers
- Factorial (recursive)

### Exercise 2: String Manipulation
Write functions for:
- String reversal
- Palindrome checking
- Counting words in a string
- Converting to uppercase/lowercase

### Exercise 3: Array Operations
Create functions for:
- Finding min/max in an array
- Sorting an array (bubble sort)
- Searching in an array (linear and binary search)
- Merging two sorted arrays

### Exercise 4: Recursive Problems
Implement recursive solutions for:
- Greatest Common Divisor (GCD)
- Binary to decimal conversion
- Printing numbers in reverse
- Tree traversal simulation

## Key Takeaways
- Functions organize code into reusable blocks
- Parameters can be passed by value, reference, or constant reference
- Function overloading allows multiple functions with the same name
- Recursion solves problems by breaking them into smaller subproblems
- Header files separate interface from implementation
- Static variables retain values between function calls
- Proper scope management prevents naming conflicts

## Next Module
In the next module, we'll explore arrays, strings, and basic data structures in C++.