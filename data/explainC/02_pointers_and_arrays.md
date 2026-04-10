# Pointers and Arrays in C

## Pointer Fundamentals

A pointer stores the **memory address** of another variable. Understanding pointers is essential — they underpin arrays, strings, dynamic memory, and function interfaces in C.

```c
int x = 10;
int *p = &x;      // p holds the address of x

printf("%d\n", x);    // 10  — value of x
printf("%p\n", p);    // address of x (e.g. 0x7ffd...)
printf("%d\n", *p);   // 10  — dereference: value at that address

*p = 99;
printf("%d\n", x);    // 99  — x was changed via pointer
```

### Pointer Types Matter

The type tells the compiler **how many bytes** to read when dereferencing.

```c
int   *ip;   // reads 4 bytes
char  *cp;   // reads 1 byte
double *dp;  // reads 8 bytes
void  *vp;   // generic pointer — must be cast before dereferencing
```

---

## Pointer Arithmetic

When you add an integer to a pointer, it advances by **that many elements** (not bytes).

```c
int arr[] = {10, 20, 30, 40, 50};
int *p = arr;  // points to arr[0]

p++;           // now points to arr[1]
printf("%d\n", *p);   // 20

printf("%d\n", *(p + 2));  // 40  — arr[3]

// Difference between pointers (ptrdiff_t)
int *start = &arr[1];
int *end   = &arr[4];
printf("%td\n", end - start);  // 3
```

---

## Arrays and Pointers: The Relationship

An array name **decays** to a pointer to its first element in most expressions.

```c
int arr[5] = {1, 2, 3, 4, 5};

// These are equivalent:
arr[2]        // subscript notation
*(arr + 2)    // pointer arithmetic

// And these are equivalent:
&arr[2]
arr + 2
```

**Key distinction:** `arr` is not a pointer variable — you cannot do `arr++`. It is an address constant.

```c
sizeof(arr)   // 20 (5 * 4 bytes) — actual array size
int *p = arr;
sizeof(p)     // 8 (size of pointer on 64-bit) — just a pointer now
```

---

## Multi-Dimensional Arrays

```c
int matrix[3][4];
// matrix[i][j] is equivalent to *(*(matrix + i) + j)

// Row-major layout in memory:
// [0][0] [0][1] [0][2] [0][3] [1][0] [1][1] ...

// Pointer to a row (array of 4 ints):
int (*row_ptr)[4] = matrix;
row_ptr++;        // advances by 4 * sizeof(int) = 16 bytes
```

Passing 2D arrays to functions:
```c
void print_matrix(int rows, int cols, int mat[rows][cols]) {
    for (int i = 0; i < rows; i++)
        for (int j = 0; j < cols; j++)
            printf("%d ", mat[i][j]);
}
```

---

## Pointers to Pointers

```c
int x = 5;
int *p = &x;
int **pp = &p;

printf("%d\n", **pp);  // 5

// Common use: dynamic 2D arrays
int rows = 3, cols = 4;
int **mat = malloc(rows * sizeof(int *));
for (int i = 0; i < rows; i++)
    mat[i] = malloc(cols * sizeof(int));

mat[2][3] = 99;

// Free in reverse order
for (int i = 0; i < rows; i++) free(mat[i]);
free(mat);
```

---

## Function Pointers

Functions have addresses too. Function pointers enable callbacks, dispatch tables, and plugin architectures.

```c
int add(int a, int b) { return a + b; }
int sub(int a, int b) { return a - b; }

// Declare a function pointer
int (*op)(int, int);

op = add;
printf("%d\n", op(3, 4));  // 7

op = sub;
printf("%d\n", op(3, 4));  // -1

// Array of function pointers (dispatch table)
int (*ops[])(int, int) = {add, sub};
printf("%d\n", ops[0](10, 5));  // 15
printf("%d\n", ops[1](10, 5));  // 5
```

---

## `const` and Pointers

Four combinations — each has different semantics:

```c
int x = 10, y = 20;

int *p = &x;               // mutable pointer, mutable data
const int *p = &x;         // mutable pointer, const data  (*p = 5 is illegal)
int * const p = &x;        // const pointer, mutable data  (p = &y is illegal)
const int * const p = &x;  // const pointer, const data    (neither allowed)
```

---

## Pointer Pitfalls

### Wild Pointer
```c
int *p;         // uninitialized — points to garbage address
*p = 5;         // UNDEFINED BEHAVIOR
```
Always initialize: `int *p = NULL;`

### Pointer Invalidation
```c
int *p;
{
    int x = 10;
    p = &x;
}
// x is now out of scope — p is dangling
printf("%d\n", *p);  // UB
```

### Strict Aliasing Violation
```c
float f = 3.14f;
int *ip = (int *)&f;  // violates strict aliasing — UB
// Use memcpy or a union for type-punning instead
```
