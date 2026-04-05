# Data Types in C

C supports several basic data types:

- `int` → integer numbers
- `float` → floating-point numbers
- `double` → double precision floating numbers
- `char` → single characters

## Example
```c
#include <stdio.h>

int main() {
    int age = 25;
    float pi = 3.14;
    double e = 2.718281828;
    char grade = 'A';
    
    printf("Age: %d\n", age);
    printf("Pi: %f\n", pi);
    printf("E: %lf\n", e);
    printf("Grade: %c\n", grade);
##Practice

Create variables for your name (char array), age (int), height (float) and print them.
    
    return 0;
}
