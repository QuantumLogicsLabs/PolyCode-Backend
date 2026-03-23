# Module 2: Variables and Data Types

## Learning Objectives
- Understand what variables are and how to declare them
- Learn about different data types in C++
- Understand type conversion and casting
- Learn about constants and literals

## What are Variables?

A variable is a named storage location in memory that holds a value. In C++, you must declare a variable before using it.

### Variable Declaration Syntax
```cpp
data_type variable_name;
data_type variable_name = initial_value;
```

## Basic Data Types

### 1. Integer Types
```cpp
#include <iostream>

int main() {
    // Different integer types
    short short_var = 32767;        // 2 bytes, range: -32,768 to 32,767
    int int_var = 2147483647;       // 4 bytes, range: -2,147,483,648 to 2,147,483,647
    long long_var = 2147483647L;    // 4 or 8 bytes, larger range
    long long long_long_var = 9223372036854775807LL; // 8 bytes
    
    // Unsigned integers (only non-negative values)
    unsigned int unsigned_int = 4294967295U;
    
    std::cout << "Short: " << short_var << std::endl;
    std::cout << "Int: " << int_var << std::endl;
    std::cout << "Long: " << long_var << std::endl;
    std::cout << "Long Long: " << long_long_var << std::endl;
    std::cout << "Unsigned Int: " << unsigned_int << std::endl;
    
    return 0;
}
```

### 2. Floating-Point Types
```cpp
#include <iostream>
#include <iomanip> // for std::setprecision

int main() {
    float float_var = 3.14159f;      // 4 bytes, ~7 decimal digits
    double double_var = 3.141592653589793; // 8 bytes, ~15 decimal digits
    long double long_double_var = 3.14159265358979323846L; // 10+ bytes
    
    std::cout << std::fixed << std::setprecision(10);
    std::cout << "Float: " << float_var << std::endl;
    std::cout << "Double: " << double_var << std::endl;
    std::cout << "Long Double: " << long_double_var << std::endl;
    
    return 0;
}
```

### 3. Character Type
```cpp
#include <iostream>

int main() {
    char char_var = 'A';             // Single character
    char ascii_var = 65;             // ASCII value for 'A'
    
    std::cout << "Character: " << char_var << std::endl;
    std::cout << "ASCII value: " << static_cast<int>(char_var) << std::endl;
    std::cout << "From ASCII: " << ascii_var << std::endl;
    
    return 0;
}
```

### 4. Boolean Type
```cpp
#include <iostream>
#include <string>

int main() {
    bool is_true = true;             // true = 1
    bool is_false = false;           // false = 0
    
    std::cout << "True: " << is_true << std::endl;
    std::cout << "False: " << is_false << std::endl;
    std::cout << "True as string: " << std::boolalpha << is_true << std::endl;
    std::cout << "False as string: " << is_false << std::endl;
    
    return 0;
}
```

## Variable Naming Rules

1. **Valid characters**: Letters (a-z, A-Z), digits (0-9), and underscore (_)
2. **First character**: Must be a letter or underscore
3. **Case sensitive**: `age`, `Age`, and `AGE` are different variables
4. **Reserved words**: Cannot use C++ keywords

### Examples of Valid and Invalid Names
```cpp
// Valid names
int age;
int student_age;
int age1;
int _age;

// Invalid names
int 1age;        // Cannot start with digit
int student-age; // Cannot use hyphen
int class;       // Cannot use keyword
```

## Constants

### 1. const Keyword
```cpp
#include <iostream>

int main() {
    const int MAX_STUDENTS = 100;
    const double PI = 3.14159;
    const char GRADE = 'A';
    
    // MAX_STUDENTS = 200; // Error: cannot modify const variable
    
    std::cout << "Max students: " << MAX_STUDENTS << std::endl;
    std::cout << "PI: " << PI << std::endl;
    
    return 0;
}
```

### 2. #define Preprocessor
```cpp
#include <iostream>

#define MAX_SIZE 1000
#define PI 3.14159

int main() {
    std::cout << "Max size: " << MAX_SIZE << std::endl;
    std::cout << "PI: " << PI << std::endl;
    
    return 0;
}
```

## Type Conversion

### 1. Implicit Conversion (Automatic)
```cpp
#include <iostream>

int main() {
    int int_val = 10;
    double double_val = 5.5;
    
    // Implicit conversion from int to double
    double result = int_val + double_val; // 10.0 + 5.5 = 15.5
    
    // Implicit conversion from double to int (data loss)
    int truncated = double_val; // 5 (decimal part lost)
    
    std::cout << "Result: " << result << std::endl;
    std::cout << "Truncated: " << truncated << std::endl;
    
    return 0;
}
```

### 2. Explicit Conversion (Casting)
```cpp
#include <iostream>

int main() {
    double double_val = 3.99;
    int int_val;
    
    // C-style casting
    int_val = (int)double_val; // 3
    
    // C++ style casting (preferred)
    int_val = static_cast<int>(double_val); // 3
    
    std::cout << "Original: " << double_val << std::endl;
    std::cout << "Converted: " << int_val << std::endl;
    
    return 0;
}
```

## sizeof Operator

The `sizeof` operator returns the size of a data type in bytes.

```cpp
#include <iostream>

int main() {
    std::cout << "Size of char: " << sizeof(char) << " bytes" << std::endl;
    std::cout << "Size of int: " << sizeof(int) << " bytes" << std::endl;
    std::cout << "Size of float: " << sizeof(float) << " bytes" << std::endl;
    std::cout << "Size of double: " << sizeof(double) << " bytes" << std::endl;
    std::cout << "Size of bool: " << sizeof(bool) << " bytes" << std::endl;
    
    return 0;
}
```

## Auto Type Deduction (C++11)

```cpp
#include <iostream>

int main() {
    auto integer = 42;        // int
    auto decimal = 3.14;      // double
    auto character = 'A';     // char
    
    std::cout << "Integer: " << integer << std::endl;
    std::cout << "Decimal: " << decimal << std::endl;
    std::cout << "Character: " << character << std::endl;
    
    return 0;
}
```

## Complete Example: Student Information System

```cpp
#include <iostream>
#include <string>

int main() {
    // Student information variables
    const int MAX_AGE = 120;
    
    std::string student_name = "John Doe";
    int student_age = 20;
    double student_gpa = 3.85;
    char student_grade = 'A';
    bool is_honor_student = true;
    
    // Display information
    std::cout << "=== Student Information ===" << std::endl;
    std::cout << "Name: " << student_name << std::endl;
    std::cout << "Age: " << student_age << std::endl;
    std::cout << "GPA: " << student_gpa << std::endl;
    std::cout << "Grade: " << student_grade << std::endl;
    std::cout << "Honor Student: " << std::boolalpha << is_honor_student << std::endl;
    
    // Type conversion example
    int age_as_double = static_cast<int>(student_gpa * 10);
    std::cout << "Age equivalent from GPA: " << age_as_double << std::endl;
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Variable Declaration
Declare variables for the following and assign appropriate values:
- Your name (string)
- Your age (int)
- Your height in meters (double)
- Whether you're a student (bool)
- Your grade (char)

### Exercise 2: Type Conversion
Write a program that:
1. Takes a floating-point number as input
2. Converts it to integer
3. Displays both the original and converted values
4. Shows the difference between them

### Exercise 3: Size Calculator
Write a program that displays the size of all basic data types on your system using the `sizeof` operator.

### Exercise 4: Temperature Converter (Enhanced)
Modify the previous temperature converter to use constants for the conversion formula and handle both Celsius to Fahrenheit and Fahrenheit to Celsius conversions.

## Key Takeaways
- Variables must be declared with a specific data type
- C++ provides various data types: integers, floating-point, characters, and booleans
- Use `const` for variables that shouldn't change
- Type conversion can be implicit or explicit
- `sizeof` operator helps determine memory usage
- `auto` keyword allows automatic type deduction (C++11)

## Next Module
In the next module, we'll explore operators and expressions in C++.