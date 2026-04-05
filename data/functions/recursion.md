
---

### **functions/recursion.md**
```md
# Recursion in C

Recursion is when a function calls itself.

## Example
```c
#include <stdio.h>

int factorial(int n) {
    if(n == 0) return 1;
    return n * factorial(n - 1);
}

int main() {
    printf("Factorial of 5 is %d\n", factorial(5));
    return 0;
}
##Practice

Write a recursive function to calculate the sum of first n natural numbers.
