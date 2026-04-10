# Memory Management in C++

## The Memory Layout of a C++ Program

When a C++ program runs, memory is divided into segments:

```
High Address
┌─────────────────┐
│      Stack      │  ← Local variables, function frames
├─────────────────┤
│        ↓        │
│                 │
│        ↑        │
├─────────────────┤
│      Heap       │  ← Dynamic allocations (new/delete)
├─────────────────┤
│   BSS Segment   │  ← Uninitialized globals/statics
├─────────────────┤
│  Data Segment   │  ← Initialized globals/statics
├─────────────────┤
│  Text Segment   │  ← Program instructions
└─────────────────┘
Low Address
```

---

## Dynamic Memory Allocation

### `new` and `delete`

```cpp
int *p = new int(10);
delete p;
```

### Arrays

```cpp
int *arr = new int[5];
delete[] arr;
```

---

## RAII (Resource Acquisition Is Initialization)

```cpp
#include <memory>

std::unique_ptr<int> p = std::make_unique<int>(10);
```

---

## Common Bugs

### Memory Leak
```cpp
int *p = new int(5);
// forgot delete
```

### Dangling Pointer
```cpp
int *p = new int;
delete p;
*p = 10; // undefined
```

---

## Smart Pointers

| Type | Description |
|------|------------|
| unique_ptr | Single owner |
| shared_ptr | Shared ownership |
| weak_ptr | Non-owning |

---

## Key Rules

1. Prefer smart pointers
2. Avoid raw new/delete
3. Follow RAII
