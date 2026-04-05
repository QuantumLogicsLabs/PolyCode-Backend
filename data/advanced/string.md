
---

### **advanced/strings.md**
```md
# Strings in C

Strings are arrays of characters ending with a null character `\0`.

## Example
```c
#include <stdio.h>
#include <string.h>

int main() {
    char name[20] = "Alice";
    printf("Length: %lu\n", strlen(name));
    return 0;
}
##Practice

Write a program that reverses a string input by the user.
