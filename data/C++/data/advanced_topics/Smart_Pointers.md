# Smart Pointers in C++

## Overview
Memory management is one of the most powerful yet challenging aspects of C++. **Smart Pointers** are template classes that manage the lifecycle of objects on the heap, ensuring that memory is automatically released when it's no longer needed, preventing common issues like memory leaks and dangling pointers.

## Key Smart Pointers
1.  **`std::unique_ptr<T>`**:
    - **Exclusive Ownership**: Only one `unique_ptr` can point to a resource at any time.
    - **Automatic Cleanup**: When the `unique_ptr` goes out of scope, the resource is deleted.
    - **Efficient**: No overhead compared to raw pointers.
2.  **`std::shared_ptr<T>`**:
    - **Shared Ownership**: Multiple `shared_ptr` instances can point to the same resource.
    - **Reference Counting**: The resource is only deleted when the *last* `shared_ptr` pointing to it is destroyed.
    - **Thread-Safe Counting**: The reference count is managed atomically.
3.  **`std::weak_ptr<T>`**:
    - **Non-owning reference**: Points to a resource managed by a `shared_ptr` without increasing the reference count.
    - **Break Circular Dependencies**: Useful for cases where objects need to reference each other without causing a memory leak.

## Basic Syntax
```cpp
#include <memory>

// unique_ptr
std::unique_ptr<int> p1 = std::make_unique<int>(10);

// shared_ptr
std::shared_ptr<int> p2 = std::make_shared<int>(20);
std::shared_ptr<int> p3 = p2; // Shared ownership
```

## Best Practices
- Prefer `std::make_unique` and `std::make_shared` over raw `new` for better performance and safety.
- Use `unique_ptr` by default unless shared ownership is explicitly required.

[02) Smart Pointers.cpp](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/Cplusplus/data/10)%20Advanced%20Topics/02)%20Smart%20Pointers.cpp)