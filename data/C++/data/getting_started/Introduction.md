# Module 1: Introduction to C++

## Learning Objectives
- Understand what C++ is and its history
- Set up a C++ development environment
- Write and compile your first C++ program
- Understand the basic structure of a C++ program

## What is C++?

C++ is a high-level, general-purpose programming language created by Bjarne Stroustrup at Bell Labs in 1979. It is an extension of the C programming language with object-oriented features.

### Key Features:
- **Performance**: C++ is known for its high performance and efficiency
- **Object-Oriented**: Supports classes, inheritance, polymorphism
- **Low-level manipulation**: Direct memory access and pointer arithmetic
- **Standard Library**: Rich set of built-in functions and data structures
- **Cross-platform**: Code can be compiled on different operating systems

## Setting Up Your Development Environment

### Option 1: Using GCC/G++ (Linux/macOS)
```bash
# Install on Ubuntu/Debian
sudo apt-get install build-essential

# Install on macOS (using Homebrew)
brew install gcc

# Compile a program
g++ -o program_name source_file.cpp
```

### Option 2: Using Visual Studio (Windows)
1. Download Visual Studio Community Edition
2. Install the "Desktop development with C++" workload
3. Create a new C++ project

### Option 3: Using Online Compilers
- Replit.com
- OnlineGDB.com
- Compiler Explorer (godbolt.org)

## Your First C++ Program

### The Classic "Hello, World!" Program

```cpp
#include <iostream>

int main() {
    std::cout << "Hello, World!" << std::endl;
    return 0;
}
```

### Breaking Down the Code:

1. **`#include <iostream>`**: This line includes the input/output stream library, which allows us to use `std::cout`.

2. **`int main()`**: This is the main function where program execution begins. Every C++ program must have a `main` function.

3. **`std::cout << "Hello, World!" << std::endl;`**: 
   - `std::cout` is the standard output stream
   - `<<` is the insertion operator
   - `"Hello, World!"` is the string to be printed
   - `std::endl` adds a newline and flushes the output buffer

4. **`return 0;`**: This indicates successful program execution.

## Compiling and Running

### Using Command Line:
```bash
# Compile the program
g++ -o hello hello.cpp

# Run the executable
./hello
```

### Alternative Compilation Commands:
```bash
# With warnings enabled
g++ -Wall -o hello hello.cpp

# With debugging information
g++ -g -o hello hello.cpp

# With optimization
g++ -O2 -o hello hello.cpp
```

## Basic Program Structure

### Example: Simple Calculator
```cpp
#include <iostream>

int main() {
    // Variable declarations
    int num1, num2, sum;
    
    // Input
    std::cout << "Enter first number: ";
    std::cin >> num1;
    
    std::cout << "Enter second number: ";
    std::cin >> num2;
    
    // Processing
    sum = num1 + num2;
    
    // Output
    std::cout << "Sum: " << sum << std::endl;
    
    return 0;
}
```

## Common C++ Extensions
- `.cpp` - C++ source file
- `.h` or `.hpp` - Header file
- `.cc` - Alternative C++ source extension

## Practice Exercises

### Exercise 1: Personal Information
Write a program that displays your name, age, and favorite programming language.

### Exercise 2: Simple Math
Write a program that takes two numbers as input and displays their sum, difference, product, and quotient.

### Exercise 3: Temperature Converter
Write a program that converts temperature from Celsius to Fahrenheit.
Formula: `F = (C × 9/5) + 32`

## Key Takeaways
- C++ is a powerful, high-performance programming language
- Every C++ program must have a `main()` function
- Use `#include` to include necessary libraries
- `std::cout` is used for output, `std::cin` for input
- Compile with `g++` and run the resulting executable

## Next Module
In the next module, we'll dive deeper into variables, data types, and basic operators in C++.