# Templates in C++

## Overview
**Templates** are a fundamental feature in C++ that allow for **Generic Programming**, where code is written in a way that is independent of any particular data type. This reduces code duplication and improves maintenance.

## Key Types of Templates
1.  **Function Templates**:
    - **Generic Functions**: Define a function that can operate on different data types.
    - **Type Deduction**: The compiler automatically determines the type based on the arguments passed.
2.  **Class Templates**:
    - **Generic Classes**: Define a class that can hold or operate on different data types.
    - **Type Specification**: The type must be specified when an object of the class is created (e.g., `Box<int>`).
3.  **Variable Templates** (C++14):
    - **Generic Variables**: Define a variable that can have different types (e.g., `pi<double>`).
4.  **Template Specialization**:
    - **Custom Behavior**: Provide a specific implementation for a particular data type when the generic implementation is not suitable.

## Basic Syntax
```cpp
// Function Template
template <typename T>
T add(T a, T b) { return a + b; }

// Class Template
template <typename T>
class Box {
    T value;
public:
    Box(T val) : value(val) {}
};

int main() {
    int sum = add(10, 20); // Deduces T as int
    Box<string> box("Hello"); // Specifies T as string
}
```

## Best Practices
- Use templates to write reusable and efficient code for various data types.
- Be aware of "code bloat" as the compiler generates a unique version of the code for each type used.

[03) Templates.cpp](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/Cplusplus/data/10)%20Advanced%20Topics/03)%20Templates.cpp)