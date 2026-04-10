# Memory Management in C

## The Memory Layout of a C Program

When a C program runs, the OS divides its memory into distinct segments:

```
High Address
┌─────────────────┐
│      Stack      │  ← Local variables, function call frames
├─────────────────┤
│        ↓        │
│    (grows down) │
│                 │
│    (grows up)   │
│        ↑        │
├─────────────────┤
│      Heap       │  ← Dynamically allocated memory
├─────────────────┤
│   BSS Segment   │  ← Uninitialized global/static variables
├─────────────────┤
│  Data Segment   │  ← Initialized global/static variables
├─────────────────┤
│  Text Segment   │  ← Compiled program code (read-only)
└─────────────────┘
Low Address
```

---

## Dynamic Memory Allocation

### `malloc` — Allocate Uninitialized Memory

```c
#include <stdlib.h>

int *arr = (int *)malloc(10 * sizeof(int));
if (arr == NULL) {
    fprintf(stderr, "malloc failed\n");
    exit(EXIT_FAILURE);
}
// Memory is uninitialized — contains garbage values
arr[0] = 42;
free(arr);
```

### `calloc` — Allocate Zero-Initialized Memory

```c
// Allocates 10 ints, all set to 0
int *arr = (int *)calloc(10, sizeof(int));
if (!arr) { /* handle error */ }
free(arr);
```

### `realloc` — Resize an Existing Allocation

```c
int *arr = (int *)malloc(5 * sizeof(int));

// Grow to 10 elements
int *tmp = (int *)realloc(arr, 10 * sizeof(int));
if (tmp == NULL) {
    free(arr);  // original pointer still valid on failure
    exit(EXIT_FAILURE);
}
arr = tmp;
free(arr);
```

> **Never assign `realloc` directly to the original pointer.** If it fails, it returns `NULL` and you lose the original pointer — a memory leak.

---

## Common Memory Bugs

### 1. Memory Leak
```c
void leak() {
    int *p = malloc(100);
    // forgot to call free(p)
    return;  // 100 bytes leaked
}
```

### 2. Dangling Pointer
```c
int *p = malloc(sizeof(int));
free(p);
*p = 5;  // UNDEFINED BEHAVIOR — p is dangling
p = NULL; // good practice: nullify after free
```

### 3. Double Free
```c
int *p = malloc(sizeof(int));
free(p);
free(p);  // UNDEFINED BEHAVIOR — heap corruption
```

### 4. Buffer Overflow
```c
int *arr = malloc(5 * sizeof(int));
arr[5] = 99;  // out-of-bounds write — UB, silent corruption
```

### 5. Use of Uninitialized Memory
```c
int *p = malloc(sizeof(int));
printf("%d\n", *p);  // garbage value — unpredictable
```

---

## Stack vs Heap: When to Use Which

| Criteria | Stack | Heap |
|---|---|---|
| Lifetime | Until function returns | Until `free()` is called |
| Size limit | Small (~1–8 MB) | Limited by RAM/swap |
| Allocation speed | Extremely fast (pointer decrement) | Slower (allocator overhead) |
| Use case | Local variables, small fixed arrays | Large/dynamic/long-lived data |

---

## Memory Debugging Tools

### Valgrind
```bash
gcc -g -o program program.c
valgrind --leak-check=full ./program
```

### AddressSanitizer (ASan)
```bash
gcc -fsanitize=address -g -o program program.c
./program
```

ASan catches: heap overflows, stack overflows, use-after-free, double-free, and memory leaks at runtime.

---

## Writing a Simple Memory Pool

For performance-critical code, a custom allocator avoids `malloc` overhead:

```c
#define POOL_SIZE 4096

typedef struct {
    char buf[POOL_SIZE];
    size_t offset;
} MemPool;

void pool_init(MemPool *p) { p->offset = 0; }

void *pool_alloc(MemPool *p, size_t size) {
    // align to 8 bytes
    size = (size + 7) & ~7;
    if (p->offset + size > POOL_SIZE) return NULL;
    void *ptr = p->buf + p->offset;
    p->offset += size;
    return ptr;
}

void pool_reset(MemPool *p) { p->offset = 0; }

// Usage
MemPool pool;
pool_init(&pool);
int *x = pool_alloc(&pool, sizeof(int) * 10);
// no individual free needed — reset the whole pool
pool_reset(&pool);
```

---

## Key Rules

1. Every `malloc`/`calloc`/`realloc` must have a matching `free`.
2. Always check the return value of allocation functions for `NULL`.
3. Set pointers to `NULL` after freeing them.
4. Never access memory after freeing it.
5. Use tools like Valgrind or ASan during development.
