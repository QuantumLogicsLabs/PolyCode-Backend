
---

## **basics/operators.md**
```md
# Operators in C

C has different types of operators:

- Arithmetic: `+`, `-`, `*`, `/`, `%`
- Relational: `==`, `!=`, `<`, `>`, `<=`, `>=`
- Logical: `&&`, `||`, `!`
- Assignment: `=`, `+=`, `-=`, `*=`, `/=`

## Example
```c
#include <stdio.h>

int main() {
    int a = 10, b = 5;

    printf("a + b = %d\n", a + b);
    printf("a - b = %d\n", a - b);
    printf("a * b = %d\n", a * b);
    printf("a / b = %d\n", a / b);
    printf("a %% b = %d\n", a % b);

    if (a > b && b > 0) {
        printf("Both conditions are true\n");
    }
##Practice

Write a program to check if a number is even or odd using operators.

    return 0;
}
