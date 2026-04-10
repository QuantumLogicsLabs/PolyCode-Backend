# The C Preprocessor and Macros

## How the Build Pipeline Works

Before compilation, the **preprocessor** transforms your source code textually:

```
Source (.c)
    │
    ▼
[Preprocessor]  ← handles #include, #define, #if, etc.
    │
    ▼
Translation Unit (pure C, no directives)
    │
    ▼
[Compiler]  → Object file (.o)
    │
    ▼
[Linker]  → Executable
```

You can inspect preprocessor output:
```bash
gcc -E source.c -o source.i
```

---

## `#include` and Header Guards

### Preventing Double Inclusion

```c
// mylib.h
#ifndef MYLIB_H
#define MYLIB_H

typedef struct {
    int x, y;
} Point;

Point point_add(Point a, Point b);

#endif // MYLIB_H
```

Modern alternative — `#pragma once` (non-standard but universally supported):
```c
#pragma once

typedef struct { int x, y; } Point;
```

### Angle Brackets vs Quotes

```c
#include <stdio.h>    // searches system include paths
#include "mylib.h"    // searches current directory first, then system paths
```

---

## Object-Like Macros (`#define`)

```c
#define PI          3.14159265358979
#define MAX_BUFSIZE 4096
#define NEWLINE     '\n'
```

These are **textual substitutions** — the preprocessor replaces every occurrence before compilation. They have no type and no scope.

```c
// Avoid magic numbers:
char buf[MAX_BUFSIZE];   // becomes: char buf[4096];
```

---

## Function-Like Macros

```c
#define SQUARE(x)   ((x) * (x))
#define MAX(a, b)   ((a) > (b) ? (a) : (b))
#define ABS(x)      ((x) < 0 ? -(x) : (x))
```

### The Parenthesis Rule

Always wrap arguments and the entire macro in parentheses:

```c
#define BAD_SQUARE(x)   x * x
int r = BAD_SQUARE(3 + 1);
// expands to: 3 + 1 * 3 + 1 = 7  (wrong! expected 16)

#define GOOD_SQUARE(x)  ((x) * (x))
int r = GOOD_SQUARE(3 + 1);
// expands to: ((3 + 1) * (3 + 1)) = 16  (correct)
```

### Side Effect Problem

```c
int i = 3;
int r = SQUARE(i++);
// expands to: ((i++) * (i++))  — i is incremented TWICE — UB
```

For this reason, prefer `inline` functions over macros when possible:
```c
static inline int square(int x) { return x * x; }
```

---

## Stringification and Token Pasting

### `#` — Stringification

```c
#define STRINGIFY(x) #x
printf("%s\n", STRINGIFY(hello world));  // prints: hello world

// Useful for debugging:
#define DEBUG_VAR(x) printf(#x " = %d\n", (x))
int count = 42;
DEBUG_VAR(count);  // prints: count = 42
```

### `##` — Token Pasting

```c
#define MAKE_FUNC(type) type##_init
// MAKE_FUNC(list) → list_init

#define DECLARE_PAIR(T) \
    typedef struct { T first; T second; } T##_pair

DECLARE_PAIR(int);    // creates: int_pair struct
DECLARE_PAIR(float);  // creates: float_pair struct
```

---

## Conditional Compilation

```c
#ifdef DEBUG
    #define LOG(msg) fprintf(stderr, "[DEBUG] %s\n", msg)
#else
    #define LOG(msg)  // expands to nothing in release
#endif

// Check compiler or platform:
#if defined(_WIN32)
    #define PLATFORM "Windows"
#elif defined(__linux__)
    #define PLATFORM "Linux"
#elif defined(__APPLE__)
    #define PLATFORM "macOS"
#endif

// Version guards:
#if __STDC_VERSION__ >= 199901L
    // C99 and above features available
    #include <stdbool.h>
#endif
```

---

## Variadic Macros

```c
#define LOG(fmt, ...) fprintf(stderr, fmt "\n", ##__VA_ARGS__)

LOG("Starting up");
LOG("Value: %d, Name: %s", 42, "foo");
```

The `##` before `__VA_ARGS__` is a GCC extension that removes the trailing comma when no variadic arguments are provided.

---

## X-Macro Pattern

A powerful technique for keeping enums and string tables in sync:

```c
// Define the data once:
#define COLOR_LIST \
    X(RED,   "red",   0xFF0000) \
    X(GREEN, "green", 0x00FF00) \
    X(BLUE,  "blue",  0x0000FF)

// Generate enum:
typedef enum {
    #define X(name, str, hex) name,
    COLOR_LIST
    #undef X
    COLOR_COUNT
} Color;

// Generate name lookup array:
const char *color_names[] = {
    #define X(name, str, hex) str,
    COLOR_LIST
    #undef X
};

// Generate hex value array:
int color_hex[] = {
    #define X(name, str, hex) hex,
    COLOR_LIST
    #undef X
};
```

Adding a new color only requires one line in `COLOR_LIST` — all generated arrays update automatically.

---

## Predefined Macros

```c
__FILE__       // current filename as string literal
__LINE__       // current line number as integer
__func__       // current function name (C99)
__DATE__       // compilation date: "Apr 10 2026"
__TIME__       // compilation time: "14:32:01"
__STDC_VERSION__ // C standard version (e.g. 201112L for C11)

// Useful assertion macro:
#define ASSERT(cond) \
    do { \
        if (!(cond)) { \
            fprintf(stderr, "Assertion failed: %s, file %s, line %d\n", \
                    #cond, __FILE__, __LINE__); \
            abort(); \
        } \
    } while (0)
```

---

## `do { ... } while (0)` Idiom

When writing multi-statement macros, always wrap in `do { } while (0)`:

```c
// WRONG — breaks with if/else:
#define SWAP(a, b)  int tmp = a; a = b; b = tmp;

if (x > 0)
    SWAP(x, y);   // only first statement is in the if body!
else
    ...

// CORRECT:
#define SWAP(a, b) do { int tmp = (a); (a) = (b); (b) = tmp; } while (0)
```

This forces the macro to behave as a single statement and work correctly in all control flow contexts.
