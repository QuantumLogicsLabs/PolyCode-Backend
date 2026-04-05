# Defining Functions in C

Functions are blocks of code that perform specific tasks.  
They help make programs modular and reusable.

## Example
```c
#include <stdio.h>

void greet() {
    printf("Hello, C Programmer!\n");
}

int main() {
    greet();
    return 0;
}
Practice

Write a function sayHello that prints "Hello!" and call it twice in main.
