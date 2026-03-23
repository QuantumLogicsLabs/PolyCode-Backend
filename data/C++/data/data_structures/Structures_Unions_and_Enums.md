# Module 8: Structures, Unions, and Enums

## Learning Objectives
- Understand structures and how to define them
- Learn about member access and structure initialization
- Master nested structures and arrays of structures
- Understand unions and their memory-saving benefits
- Learn about enums and enum classes
- Understand bit fields and packed structures

## Structures (struct)

### Basic Structure Definition and Usage

A structure is a user-defined data type that groups together variables of different data types under a single name.

```cpp
#include <iostream>
#include <string>

// Structure definition
struct Person {
    std::string name;
    int age;
    double height;
    char gender;
};

int main() {
    // Structure variable declaration
    Person person1;
    Person person2;
    
    // Member assignment
    person1.name = "John Doe";
    person1.age = 30;
    person1.height = 5.9;
    person1.gender = 'M';
    
    person2.name = "Jane Smith";
    person2.age = 25;
    person2.height = 5.6;
    person2.gender = 'F';
    
    // Accessing structure members
    std::cout << "Person 1 Information:" << std::endl;
    std::cout << "Name: " << person1.name << std::endl;
    std::cout << "Age: " << person1.age << std::endl;
    std::cout << "Height: " << person1.height << std::endl;
    std::cout << "Gender: " << person1.gender << std::endl;
    
    return 0;
}
```

### Structure Initialization

```cpp
#include <iostream>
#include <string>

struct Point {
    double x;
    double y;
};

struct Rectangle {
    Point topLeft;
    Point bottomRight;
    std::string color;
};

int main() {
    // Method 1: Member-wise initialization
    Point p1;
    p1.x = 10.5;
    p1.y = 20.3;
    
    // Method 2: Aggregate initialization
    Point p2 = {15.7, 25.9};
    
    // Method 3: Designated initializers (C++20)
    Point p3 = {.x = 5.5, .y = 10.10};
    
    // Rectangle initialization
    Rectangle rect1 = {{0, 0}, {100, 200}, "Red"};
    
    std::cout << "Point p1: (" << p1.x << ", " << p1.y << ")" << std::endl;
    std::cout << "Point p2: (" << p2.x << ", " << p2.y << ")" << std::endl;
    std::cout << "Point p3: (" << p3.x << ", " << p3.y << ")" << std::endl;
    
    std::cout << "Rectangle: " << rect1.color << std::endl;
    std::cout << "Top-left: (" << rect1.topLeft.x << ", " << rect1.topLeft.y << ")" << std::endl;
    std::cout << "Bottom-right: (" << rect1.bottomRight.x << ", " << rect1.bottomRight.y << ")" << std::endl;
    
    return 0;
}
```

### Structures and Functions

```cpp
#include <iostream>
#include <string>
#include <cmath>

struct Circle {
    double radius;
    double centerX;
    double centerY;
};

// Function that takes a structure by value
double calculateArea(Circle c) {
    return M_PI * c.radius * c.radius;
}

// Function that takes a structure by reference
void scaleCircle(Circle& c, double factor) {
    c.radius *= factor;
}

// Function that returns a structure
Circle createCircle(double r, double x, double y) {
    Circle c;
    c.radius = r;
    c.centerX = x;
    c.centerY = y;
    return c;
}

// Function that modifies structure through pointer
void moveCircle(Circle* c, double dx, double dy) {
    c->centerX += dx;
    c->centerY += dy;
}

int main() {
    Circle myCircle = createCircle(5.0, 0.0, 0.0);
    
    std::cout << "Initial circle:" << std::endl;
    std::cout << "Radius: " << myCircle.radius << std::endl;
    std::cout << "Center: (" << myCircle.centerX << ", " << myCircle.centerY << ")" << std::endl;
    std::cout << "Area: " << calculateArea(myCircle) << std::endl;
    
    scaleCircle(myCircle, 2.0);
    std::cout << "\nAfter scaling by 2:" << std::endl;
    std::cout << "Radius: " << myCircle.radius << std::endl;
    std::cout << "Area: " << calculateArea(myCircle) << std::endl;
    
    moveCircle(&myCircle, 3.0, 4.0);
    std::cout << "\nAfter moving by (3, 4):" << std::endl;
    std::cout << "Center: (" << myCircle.centerX << ", " << myCircle.centerY << ")" << std::endl;
    
    return 0;
}
```

### Arrays of Structures

```cpp
#include <iostream>
#include <string>

struct Student {
    std::string name;
    int id;
    double gpa;
};

void printStudent(const Student& s) {
    std::cout << "ID: " << s.id << ", Name: " << s.name 
              << ", GPA: " << s.gpa << std::endl;
}

int main() {
    // Array of structures
    Student students[5] = {
        {"Alice Johnson", 1001, 3.8},
        {"Bob Smith", 1002, 3.5},
        {"Charlie Brown", 1003, 3.9},
        {"Diana Prince", 1004, 4.0},
        {"Eve Wilson", 1005, 3.2}
    };
    
    std::cout << "All Students:" << std::endl;
    for (int i = 0; i < 5; i++) {
        printStudent(students[i]);
    }
    
    // Find student with highest GPA
    int topStudentIndex = 0;
    for (int i = 1; i < 5; i++) {
        if (students[i].gpa > students[topStudentIndex].gpa) {
            topStudentIndex = i;
        }
    }
    
    std::cout << "\nTop Student:" << std::endl;
    printStudent(students[topStudentIndex]);
    
    return 0;
}
```

### Nested Structures

```cpp
#include <iostream>
#include <string>

struct Address {
    std::string street;
    std::string city;
    std::string state;
    std::string zipCode;
};

struct Contact {
    std::string name;
    std::string email;
    std::string phone;
    Address address;
};

void printContact(const Contact& c) {
    std::cout << "Name: " << c.name << std::endl;
    std::cout << "Email: " << c.email << std::endl;
    std::cout << "Phone: " << c.phone << std::endl;
    std::cout << "Address:" << std::endl;
    std::cout << "  " << c.address.street << std::endl;
    std::cout << "  " << c.address.city << ", " << c.address.state << " " << c.address.zipCode << std::endl;
}

int main() {
    Contact contact1 = {
        "John Doe",
        "john.doe@email.com",
        "555-1234",
        {"123 Main St", "Anytown", "CA", "12345"}
    };
    
    printContact(contact1);
    
    return 0;
}
```

### Structure Member Alignment and Size

```cpp
#include <iostream>

struct Example1 {
    char a;     // 1 byte
    int b;      // 4 bytes (aligned to 4-byte boundary)
    char c;     // 1 byte
}; // Total: 12 bytes due to padding

struct Example2 {
    char a;     // 1 byte
    char c;     // 1 byte
    int b;      // 4 bytes
}; // Total: 8 bytes (better packing)

struct Example3 {
    char a;     // 1 byte
    char c;     // 1 byte
    short d;    // 2 bytes
    int b;      // 4 bytes
}; // Total: 8 bytes

int main() {
    std::cout << "Size of Example1: " << sizeof(Example1) << " bytes" << std::endl;
    std::cout << "Size of Example2: " << sizeof(Example2) << " bytes" << std::endl;
    std::cout << "Size of Example3: " << sizeof(Example3) << " bytes" << std::endl;
    
    return 0;
}
```

## Unions (union)

### Basic Union Usage

A union is a special data type that allows storing different data types in the same memory location.

```cpp
#include <iostream>

union Data {
    int intValue;
    float floatValue;
    char charValue[4];
};

int main() {
    Data data;
    
    // Store integer
    data.intValue = 0x41424344;  // Hex values for ASCII "DCBA"
    std::cout << "As integer: " << data.intValue << std::endl;
    std::cout << "As float: " << data.floatValue << std::endl;
    std::cout << "As chars: " << data.charValue[0] << data.charValue[1] 
              << data.charValue[2] << data.charValue[3] << std::endl;
    
    // Store float
    data.floatValue = 3.14159f;
    std::cout << "\nAs float: " << data.floatValue << std::endl;
    std::cout << "As integer: " << data.intValue << std::endl;
    
    return 0;
}
```

### Union with Structure Members

```cpp
#include <iostream>
#include <string>

enum DataType { INT, FLOAT, STRING };

union Value {
    int intValue;
    float floatValue;
    char* stringValue;
};

struct Variant {
    DataType type;
    Value value;
};

void printVariant(const Variant& v) {
    switch (v.type) {
        case INT:
            std::cout << "Integer: " << v.value.intValue << std::endl;
            break;
        case FLOAT:
            std::cout << "Float: " << v.value.floatValue << std::endl;
            break;
        case STRING:
            std::cout << "String: " << v.value.stringValue << std::endl;
            break;
    }
}

int main() {
    Variant var1, var2, var3;
    
    // Integer variant
    var1.type = INT;
    var1.value.intValue = 42;
    
    // Float variant
    var2.type = FLOAT;
    var2.value.floatValue = 3.14159f;
    
    // String variant
    var3.type = STRING;
    var3.value.stringValue = new char[20];
    strcpy(var3.value.stringValue, "Hello World");
    
    printVariant(var1);
    printVariant(var2);
    printVariant(var3);
    
    // Clean up string memory
    delete[] var3.value.stringValue;
    
    return 0;
}
```

## Enumerations (enum)

### Traditional Enum

```cpp
#include <iostream>

enum Color { RED, GREEN, BLUE, YELLOW, ORANGE };
enum Day { MONDAY, TUESDAY, WEDNESDAY, THURSDAY, FRIDAY, SATURDAY, SUNDAY };

int main() {
    Color favoriteColor = BLUE;
    Day today = WEDNESDAY;
    
    std::cout << "Favorite color: " << favoriteColor << std::endl;
    std::cout << "Today is: " << today << std::endl;
    
    // Enum in switch statement
    switch (favoriteColor) {
        case RED:
            std::cout << "You like red!" << std::endl;
            break;
        case GREEN:
            std::cout << "You like green!" << std::endl;
            break;
        case BLUE:
            std::cout << "You like blue!" << std::endl;
            break;
        default:
            std::cout << "You like another color!" << std::endl;
    }
    
    return 0;
}
```

### Enum with Custom Values

```cpp
#include <iostream>

enum Month {
    JANUARY = 1,
    FEBRUARY = 2,
    MARCH = 3,
    APRIL = 4,
    MAY = 5,
    JUNE = 6,
    JULY = 7,
    AUGUST = 8,
    SEPTEMBER = 9,
    OCTOBER = 10,
    NOVEMBER = 11,
    DECEMBER = 12
};

enum Status {
    OK = 200,
    NOT_FOUND = 404,
    SERVER_ERROR = 500
};

int main() {
    Month currentMonth = MARCH;
    Status response = OK;
    
    std::cout << "Current month number: " << currentMonth << std::endl;
    std::cout << "HTTP status: " << response << std::endl;
    
    // Automatic increment
    enum Weekday { SUNDAY = 1, MONDAY, TUESDAY, WEDNESDAY, THURSDAY, FRIDAY, SATURDAY };
    Weekday today = WEDNESDAY;
    std::cout << "Today is weekday number: " << today << std::endl;
    
    return 0;
}
```

### Enum Class (C++11)

```cpp
#include <iostream>

enum class Color { RED, GREEN, BLUE };
enum class TrafficLight { RED, YELLOW, GREEN };

int main() {
    Color favoriteColor = Color::BLUE;
    TrafficLight currentLight = TrafficLight::GREEN;
    
    // Must use explicit casting to print
    std::cout << "Favorite color: " << static_cast<int>(favoriteColor) << std::endl;
    std::cout << "Traffic light: " << static_cast<int>(currentLight) << std::endl;
    
    // Type safety - cannot compare different enum classes
    if (favoriteColor == Color::RED) {
        std::cout << "Color is red" << std::endl;
    }
    
    // This would cause a compile error:
    // if (favoriteColor == TrafficLight::RED) { ... }
    
    return 0;
}
```

## Bit Fields

### Basic Bit Fields

```cpp
#include <iostream>

struct Flags {
    unsigned int isReadOnly : 1;    // 1 bit
    unsigned int isHidden : 1;     // 1 bit
    unsigned int isSystem : 1;     // 1 bit
    unsigned int isArchive : 1;    // 1 bit
    unsigned int reserved : 28;     // 28 bits
};

struct Date {
    unsigned int day : 5;       // 1-31 (5 bits)
    unsigned int month : 4;     // 1-12 (4 bits)
    unsigned int year : 12;     // 0-4095 (12 bits)
};

int main() {
    Flags fileFlags;
    fileFlags.isReadOnly = 1;
    fileFlags.isHidden = 0;
    fileFlags.isSystem = 1;
    fileFlags.isArchive = 1;
    
    std::cout << "Size of Flags: " << sizeof(Flags) << " bytes" << std::endl;
    std::cout << "Read-only: " << fileFlags.isReadOnly << std::endl;
    std::cout << "Hidden: " << fileFlags.isHidden << std::endl;
    std::cout << "System: " << fileFlags.isSystem << std::endl;
    std::cout << "Archive: " << fileFlags.isArchive << std::endl;
    
    Date today;
    today.day = 18;
    today.month = 3;
    today.year = 2026;
    
    std::cout << "\nSize of Date: " << sizeof(Date) << " bytes" << std::endl;
    std::cout << "Date: " << today.month << "/" << today.day << "/" << today.year << std::endl;
    
    return 0;
}
```

## Complete Example: Employee Management System

```cpp
#include <iostream>
#include <string>
#include <vector>

enum class Department { ENGINEERING, MARKETING, SALES, HR, FINANCE };
enum class EmploymentStatus { FULL_TIME, PART_TIME, CONTRACT, INTERN };

struct Address {
    std::string street;
    std::string city;
    std::string state;
    std::string zipCode;
};

struct Date {
    int day;
    int month;
    int year;
};

struct Employee {
    int id;
    std::string firstName;
    std::string lastName;
    Date hireDate;
    Department department;
    EmploymentStatus status;
    double salary;
    Address address;
};

std::string departmentToString(Department dept) {
    switch (dept) {
        case Department::ENGINEERING: return "Engineering";
        case Department::MARKETING: return "Marketing";
        case Department::SALES: return "Sales";
        case Department::HR: return "HR";
        case Department::FINANCE: return "Finance";
        default: return "Unknown";
    }
}

std::string statusToString(EmploymentStatus status) {
    switch (status) {
        case EmploymentStatus::FULL_TIME: return "Full-time";
        case EmploymentStatus::PART_TIME: return "Part-time";
        case EmploymentStatus::CONTRACT: return "Contract";
        case EmploymentStatus::INTERN: return "Intern";
        default: return "Unknown";
    }
}

void printEmployee(const Employee& emp) {
    std::cout << "Employee ID: " << emp.id << std::endl;
    std::cout << "Name: " << emp.firstName << " " << emp.lastName << std::endl;
    std::cout << "Hire Date: " << emp.hireDate.month << "/" << emp.hireDate.day << "/" << emp.hireDate.year << std::endl;
    std::cout << "Department: " << departmentToString(emp.department) << std::endl;
    std::cout << "Status: " << statusToString(emp.status) << std::endl;
    std::cout << "Salary: $" << emp.salary << std::endl;
    std::cout << "Address: " << emp.address.street << ", " << emp.address.city 
              << ", " << emp.address.state << " " << emp.address.zipCode << std::endl;
    std::cout << "----------------------------------------" << std::endl;
}

void addEmployee(std::vector<Employee>& employees) {
    Employee emp;
    
    std::cout << "Enter employee ID: ";
    std::cin >> emp.id;
    std::cin.ignore();
    
    std::cout << "Enter first name: ";
    std::getline(std::cin, emp.firstName);
    
    std::cout << "Enter last name: ";
    std::getline(std::cin, emp.lastName);
    
    std::cout << "Enter hire date (day month year): ";
    std::cin >> emp.hireDate.day >> emp.hireDate.month >> emp.hireDate.year;
    
    int deptChoice;
    std::cout << "Select department (0-Engineering, 1-Marketing, 2-Sales, 3-HR, 4-Finance): ";
    std::cin >> deptChoice;
    emp.department = static_cast<Department>(deptChoice);
    
    int statusChoice;
    std::cout << "Select status (0-Full-time, 1-Part-time, 2-Contract, 3-Intern): ";
    std::cin >> statusChoice;
    emp.status = static_cast<EmploymentStatus>(statusChoice);
    
    std::cout << "Enter salary: ";
    std::cin >> emp.salary;
    std::cin.ignore();
    
    std::cout << "Enter street address: ";
    std::getline(std::cin, emp.address.street);
    
    std::cout << "Enter city: ";
    std::getline(std::cin, emp.address.city);
    
    std::cout << "Enter state: ";
    std::getline(std::cin, emp.address.state);
    
    std::cout << "Enter zip code: ";
    std::getline(std::cin, emp.address.zipCode);
    
    employees.push_back(emp);
    std::cout << "Employee added successfully!" << std::endl;
}

void findEmployeesByDepartment(const std::vector<Employee>& employees, Department dept) {
    std::cout << "Employees in " << departmentToString(dept) << ":" << std::endl;
    bool found = false;
    
    for (const auto& emp : employees) {
        if (emp.department == dept) {
            printEmployee(emp);
            found = true;
        }
    }
    
    if (!found) {
        std::cout << "No employees found in this department." << std::endl;
    }
}

double calculateAverageSalary(const std::vector<Employee>& employees) {
    if (employees.empty()) return 0.0;
    
    double total = 0.0;
    for (const auto& emp : employees) {
        total += emp.salary;
    }
    
    return total / employees.size();
}

int main() {
    std::vector<Employee> employees;
    
    // Add some sample employees
    Employee emp1 = {
        1001, "John", "Doe", {15, 1, 2020}, 
        Department::ENGINEERING, EmploymentStatus::FULL_TIME, 
        75000.0, {"123 Main St", "Anytown", "CA", "12345"}
    };
    
    Employee emp2 = {
        1002, "Jane", "Smith", {1, 3, 2021}, 
        Department::MARKETING, EmploymentStatus::FULL_TIME, 
        65000.0, {"456 Oak Ave", "Somecity", "NY", "67890"}
    };
    
    employees.push_back(emp1);
    employees.push_back(emp2);
    
    int choice;
    do {
        std::cout << "\n=== Employee Management System ===" << std::endl;
        std::cout << "1. Add Employee" << std::endl;
        std::cout << "2. Display All Employees" << std::endl;
        std::cout << "3. Find by Department" << std::endl;
        std::cout << "4. Calculate Average Salary" << std::endl;
        std::cout << "5. Exit" << std::endl;
        std::cout << "Enter your choice: ";
        std::cin >> choice;
        
        switch (choice) {
            case 1:
                addEmployee(employees);
                break;
            case 2:
                std::cout << "\nAll Employees:" << std::endl;
                for (const auto& emp : employees) {
                    printEmployee(emp);
                }
                break;
            case 3: {
                int deptChoice;
                std::cout << "Select department (0-Engineering, 1-Marketing, 2-Sales, 3-HR, 4-Finance): ";
                std::cin >> deptChoice;
                findEmployeesByDepartment(employees, static_cast<Department>(deptChoice));
                break;
            }
            case 4:
                std::cout << "Average salary: $" << calculateAverageSalary(employees) << std::endl;
                break;
            case 5:
                std::cout << "Exiting..." << std::endl;
                break;
            default:
                std::cout << "Invalid choice!" << std::endl;
        }
    } while (choice != 5);
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Student Record System
Create a student record system using structures:
- Student structure with name, ID, grades, GPA
- Array of students
- Functions to add, display, and search students
- Calculate class statistics

### Exercise 2: Graphics Library
Implement basic graphics structures:
- Point, Rectangle, Circle structures
- Functions for geometric operations
- Union for different shape types
- Enum for shape types

### Exercise 3: Configuration Parser
Create a configuration parser:
- Structure for configuration options
- Union for different value types
- Enum for option types
- Parse and validate configuration

### Exercise 4: Bit Manipulation
Implement a bit field system:
- Structure with various bit fields
- Functions to set, clear, and test bits
- Union to access bits as integers
- Practical applications (file permissions, network flags)

## Key Takeaways
- Structures group related data of different types
- Unions save memory by sharing storage space
- Enums provide type-safe named constants
- Enum classes offer better type safety than traditional enums
- Bit fields optimize memory usage for flags
- Structures can be nested and organized hierarchically
- Proper initialization and member access are crucial

## Next Module
In the next module, we'll explore file handling and streams in C++.