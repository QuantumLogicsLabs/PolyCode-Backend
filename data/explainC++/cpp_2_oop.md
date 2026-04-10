# Object-Oriented Programming in C++

## Core Principles

- Encapsulation
- Inheritance
- Polymorphism
- Abstraction

---

## Example

```cpp
class Animal {
public:
    virtual void speak() { cout << "Animal"; }
};

class Dog : public Animal {
public:
    void speak() override { cout << "Bark"; }
};
```

---

## Virtual Functions & vtable

C++ uses a vtable for runtime polymorphism.

---

## Pitfalls

- Object slicing
- Missing virtual destructor

---

## Key Takeaways

- Use `override`
- Use virtual destructors
