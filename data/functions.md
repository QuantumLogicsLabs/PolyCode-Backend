# Functions in C++

## What is a Function?
A function is a block of code that performs a specific task. Functions help to organize code and avoid repetition.

## Key Points
- Functions can take parameters and return values
- Defined using a return type, name, and parameters
- Can be called multiple times from main or other functions

## Example
```cpp
#include <iostream>
using namespace std;

// Function to add two numbers
int add(int a, int b) {
    return a + b;
}

int main() {
    int sum = add(5, 3);
    cout << "Sum is: " << sum;
    return 0;
}
##Practice

Write a C++ function called multiply that takes two integers and returns their product.
