
---

## **basics/loops.md**
```md
# Loops in C

C supports different types of loops:

- `for` → repeat fixed number of times
- `while` → repeat while condition is true
- `do-while` → executes at least once, then checks condition

## Example
```c
#include <stdio.h>

int main() {
    printf("Numbers from 1 to 5 using for loop:\n");
    for(int i = 1; i <= 5; i++) {
        printf("%d\n", i);
    }

    printf("Numbers from 1 to 5 using while loop:\n");
    int j = 1;
    while(j <= 5) {
        printf("%d\n", j);
        j++;
    }
##Practice

Write a program to print all even numbers between 1 and 20 using any loop.

    return 0;
}
