# Multithreading in C++

## Thread Creation

```cpp
#include <thread>

void func() {}

std::thread t(func);
t.join();
```

---

## Mutex

```cpp
std::mutex m;
m.lock();
m.unlock();
```

---

## Deadlocks

Avoid circular waits.

---

## Key Takeaways

- Use `lock_guard`
- Avoid shared mutable state
