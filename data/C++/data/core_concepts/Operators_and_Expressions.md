# Module 3: Operators and Expressions

## Learning Objectives
- Understand different types of operators in C++
- Learn about operator precedence and associativity
- Master arithmetic, relational, and logical operators
- Understand assignment and bitwise operators
- Learn to build complex expressions

## Arithmetic Operators

### Basic Arithmetic Operations
```cpp
#include <iostream>

int main() {
    int a = 10, b = 3;
    
    std::cout << "Addition: " << (a + b) << std::endl;      // 13
    std::cout << "Subtraction: " << (a - b) << std::endl;   // 7
    std::cout << "Multiplication: " << (a * b) << std::endl; // 30
    std::cout << "Division: " << (a / b) << std::endl;      // 3 (integer division)
    std::cout << "Modulus: " << (a % b) << std::endl;       // 1 (remainder)
    
    // Floating-point division
    double x = 10.0, y = 3.0;
    std::cout << "Floating division: " << (x / y) << std::endl; // 3.33333
    
    return 0;
}
```

### Increment and Decrement Operators
```cpp
#include <iostream>

int main() {
    int num = 5;
    
    // Prefix increment
    std::cout << "Prefix ++num: " << ++num << std::endl; // 6, num becomes 6
    std::cout << "After prefix: " << num << std::endl;   // 6
    
    num = 5; // Reset
    // Postfix increment
    std::cout << "Postfix num++: " << num++ << std::endl; // 5, then num becomes 6
    std::cout << "After postfix: " << num << std::endl;   // 6
    
    // Prefix decrement
    std::cout << "Prefix --num: " << --num << std::endl; // 5, num becomes 5
    
    num = 5; // Reset
    // Postfix decrement
    std::cout << "Postfix num--: " << num-- << std::endl; // 5, then num becomes 4
    std::cout << "After postfix: " << num << std::endl;   // 4
    
    return 0;
}
```

## Relational Operators

Relational operators compare two values and return a boolean result.

```cpp
#include <iostream>

int main() {
    int a = 10, b = 20;
    
    std::cout << "Equal to (a == b): " << (a == b) << std::endl;       // false (0)
    std::cout << "Not equal to (a != b): " << (a != b) << std::endl;   // true (1)
    std::cout << "Greater than (a > b): " << (a > b) << std::endl;     // false (0)
    std::cout << "Less than (a < b): " << (a < b) << std::endl;       // true (1)
    std::cout << "Greater than or equal (a >= b): " << (a >= b) << std::endl; // false (0)
    std::cout << "Less than or equal (a <= b): " << (a <= b) << std::endl;   // true (1)
    
    return 0;
}
```

## Logical Operators

Logical operators are used to combine multiple conditions.

```cpp
#include <iostream>

int main() {
    bool a = true, b = false;
    
    std::cout << "Logical AND (a && b): " << (a && b) << std::endl;   // false (0)
    std::cout << "Logical OR (a || b): " << (a || b) << std::endl;    // true (1)
    std::cout << "Logical NOT (!a): " << (!a) << std::endl;           // false (0)
    
    // Practical example
    int age = 25;
    bool hasLicense = true;
    
    bool canDrive = (age >= 18) && hasLicense;
    std::cout << "Can drive: " << canDrive << std::endl; // true
    
    return 0;
}
```

## Assignment Operators

### Basic Assignment and Compound Assignment
```cpp
#include <iostream>

int main() {
    int x = 10;
    
    // Basic assignment
    x = 20;
    std::cout << "After assignment: " << x << std::endl; // 20
    
    // Compound assignment operators
    x += 5;  // x = x + 5;  // 25
    std::cout << "After +=: " << x << std::endl;
    
    x -= 3;  // x = x - 3;  // 22
    std::cout << "After -=: " << x << std::endl;
    
    x *= 2;  // x = x * 2;  // 44
    std::cout << "After *=: " << x << std::endl;
    
    x /= 4;  // x = x / 4;  // 11
    std::cout << "After /=: " << x << std::endl;
    
    x %= 3;  // x = x % 3;  // 2
    std::cout << "After %=: " << x << std::endl;
    
    return 0;
}
```

## Bitwise Operators

Bitwise operators work on individual bits of integer values.

```cpp
#include <iostream>

int main() {
    unsigned int a = 12;  // Binary: 1100
    unsigned int b = 10;  // Binary: 1010
    
    std::cout << "Bitwise AND (a & b): " << (a & b) << std::endl;   // 8 (1000)
    std::cout << "Bitwise OR (a | b): " << (a | b) << std::endl;    // 14 (1110)
    std::cout << "Bitwise XOR (a ^ b): " << (a ^ b) << std::endl;   // 6 (0110)
    std::cout << "Bitwise NOT (~a): " << (~a) << std::endl;          // Complement
    std::cout << "Left shift (a << 1): " << (a << 1) << std::endl;  // 24 (11000)
    std::cout << "Right shift (a >> 1): " << (a >> 1) << std::endl; // 6 (110)
    
    return 0;
}
```

## Special Operators

### Ternary Operator (Conditional Operator)
```cpp
#include <iostream>

int main() {
    int age = 20;
    std::string message;
    
    // Using ternary operator
    message = (age >= 18) ? "You are an adult" : "You are a minor";
    std::cout << message << std::endl;
    
    // Nested ternary operator
    int score = 85;
    char grade = (score >= 90) ? 'A' : 
                 (score >= 80) ? 'B' : 
                 (score >= 70) ? 'C' : 'F';
    
    std::cout << "Grade: " << grade << std::endl;
    
    return 0;
}
```

### Comma Operator
```cpp
#include <iostream>

int main() {
    int a, b, c;
    
    // Comma operator evaluates from left to right
    a = (b = 5, c = 10, b + c); // a = 15
    
    std::cout << "a: " << a << ", b: " << b << ", c: " << c << std::endl;
    
    return 0;
}
```

## Operator Precedence and Associativity

Operators have specific precedence levels that determine the order of evaluation.

### Precedence Example
```cpp
#include <iostream>

int main() {
    int result = 5 + 3 * 2;      // 5 + (3 * 2) = 11
    std::cout << "5 + 3 * 2 = " << result << std::endl;
    
    result = (5 + 3) * 2;        // (5 + 3) * 2 = 16
    std::cout << "(5 + 3) * 2 = " << result << std::endl;
    
    bool condition = 5 > 3 && 2 < 4; // (5 > 3) && (2 < 4) = true
    std::cout << "5 > 3 && 2 < 4 = " << condition << std::endl;
    
    return 0;
}
```

### Common Precedence Order (Highest to Lowest)
1. `()` `[]` `.` `->` `++` `--` (postfix)
2. `++` `--` `+` `-` `!` `~` (prefix)
3. `*` `/` `%`
4. `+` `-`
5. `<<` `>>`
6. `<` `<=` `>` `>=`
7. `==` `!=`
8. `&`
9. `^`
10. `|`
11. `&&`
12. `||`
13. `?:`
14. `=` `+=` `-=` `*=` `/=` `%=` etc.
15. `,`

## Type Promotion in Expressions

```cpp
#include <iostream>

int main() {
    char c = 'A';        // 65
    int i = 10;
    float f = 3.14f;
    double d = 2.71828;
    
    // Type promotion: smaller types are promoted to larger types
    auto result1 = c + i;     // int (char promoted to int)
    auto result2 = i + f;     // float (int promoted to float)
    auto result3 = f + d;     // double (float promoted to double)
    
    std::cout << "c + i = " << result1 << " (type: " << typeid(result1).name() << ")" << std::endl;
    std::cout << "i + f = " << result2 << " (type: " << typeid(result2).name() << ")" << std::endl;
    std::cout << "f + d = " << result3 << " (type: " << typeid(result3).name() << ")" << std::endl;
    
    return 0;
}
```

## Complete Example: Calculator Program

```cpp
#include <iostream>
#include <cmath>

int main() {
    double num1, num2, result;
    char operation;
    
    std::cout << "=== Simple Calculator ===" << std::endl;
    std::cout << "Enter first number: ";
    std::cin >> num1;
    
    std::cout << "Enter operation (+, -, *, /, %, ^): ";
    std::cin >> operation;
    
    std::cout << "Enter second number: ";
    std::cin >> num2;
    
    // Perform operation based on user input
    switch (operation) {
        case '+':
            result = num1 + num2;
            break;
        case '-':
            result = num1 - num2;
            break;
        case '*':
            result = num1 * num2;
            break;
        case '/':
            if (num2 != 0) {
                result = num1 / num2;
            } else {
                std::cout << "Error: Division by zero!" << std::endl;
                return 1;
            }
            break;
        case '%':
            result = static_cast<int>(num1) % static_cast<int>(num2);
            break;
        case '^':
            result = pow(num1, num2);
            break;
        default:
            std::cout << "Error: Invalid operation!" << std::endl;
            return 1;
    }
    
    std::cout << "Result: " << num1 << " " << operation << " " << num2 << " = " << result << std::endl;
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Basic Arithmetic
Write a program that takes two numbers and performs all arithmetic operations on them, displaying the results.

### Exercise 2: Grade Calculator
Write a program that takes a student's score (0-100) and determines their grade using:
- A: 90-100
- B: 80-89
- C: 70-79
- D: 60-69
- F: 0-59

Use relational and logical operators.

### Exercise 3: Bit Manipulation
Write a program that:
1. Takes an integer as input
2. Displays its binary representation
3. Toggles a specific bit
4. Counts the number of set bits

### Exercise 4: Expression Evaluation
Create a program that evaluates complex expressions and shows the importance of operator precedence. Include examples with and without parentheses.

## Key Takeaways
- C++ provides various operators: arithmetic, relational, logical, assignment, and bitwise
- Operator precedence determines the order of evaluation
- Use parentheses to override default precedence and improve readability
- Type promotion occurs automatically in mixed-type expressions
- The ternary operator provides a compact way to write conditional expressions

## Next Module
In the next module, we'll explore input/output operations and control flow statements in C++.