# Python Functions

## What are Functions?
A **function** is a reusable block of code that performs a specific task. Instead of writing the same code multiple times, you define it once and call it whenever needed.

### Benefits
- Reusability — write once, use many times
- Readability — code is organized and easier to understand
- Maintainability — fix a bug in one place

---

## 1. Defining and Calling a Function

```python
def function_name(parameters):
    """Docstring — describes the function"""
    # code
    return value   # optional
```

### Example
```python
def greet():
    print("Hello, World!")

greet()    # Calling the function
greet()    # Can call multiple times
```

---

## 2. Function with Parameters

```python
def greet(name):
    print(f"Hello, {name}!")

greet("Alice")
greet("Bob")
```

---

## 3. Function with Return Value

```python
def add(a, b):
    return a + b

result = add(5, 3)
print(result)   # 8
```

---

## 4. Types of Function Arguments

### 4.1 — Positional Arguments
Arguments matched by position.
```python
def power(base, exponent):
    return base ** exponent

print(power(2, 10))   # 1024
```

### 4.2 — Keyword Arguments
Arguments matched by name (order doesn't matter).
```python
def describe_person(name, age, city):
    print(f"{name}, {age}, from {city}")

describe_person(age=25, city="Paris", name="Alice")
```

### 4.3 — Default Arguments
Parameters with default values (used if not provided).
```python
def greet(name, greeting="Hello"):
    print(f"{greeting}, {name}!")

greet("Alice")              # Hello, Alice!
greet("Bob", "Hi there")    # Hi there, Bob!
```

### 4.4 — `*args` — Variable Positional Arguments
Accept any number of positional arguments (stored as a tuple).
```python
def total(*numbers):
    return sum(numbers)

print(total(1, 2, 3))          # 6
print(total(10, 20, 30, 40))   # 100
```

### 4.5 — `**kwargs` — Variable Keyword Arguments
Accept any number of keyword arguments (stored as a dict).
```python
def show_info(**details):
    for key, value in details.items():
        print(f"{key}: {value}")

show_info(name="Alice", age=25, job="Developer")
```

### 4.6 — Combining All Types
```python
def mixed(a, b, *args, key="default", **kwargs):
    print(f"a={a}, b={b}")
    print(f"args={args}")
    print(f"key={key}")
    print(f"kwargs={kwargs}")

mixed(1, 2, 3, 4, key="hello", x=10, y=20)
```

---

## 5. Return Values

### Return a Single Value
```python
def square(n):
    return n * n

print(square(7))   # 49
```

### Return Multiple Values (as tuple)
```python
def min_max(numbers):
    return min(numbers), max(numbers)

low, high = min_max([3, 1, 7, 4, 9, 2])
print(f"Min: {low}, Max: {high}")
```

### Return Nothing (None)
```python
def say_hi():
    print("Hi!")
    # no return → returns None implicitly

result = say_hi()
print(result)   # None
```

---

## 6. Lambda Functions (Anonymous Functions)

Short, one-line functions without a name.

```python
lambda parameters: expression
```

### Examples
```python
square = lambda x: x ** 2
print(square(5))   # 25

add = lambda a, b: a + b
print(add(3, 4))   # 7

# Common use: with sorted(), map(), filter()
numbers = [5, 2, 9, 1, 7]
sorted_nums = sorted(numbers, key=lambda x: x)
print(sorted_nums)   # [1, 2, 5, 7, 9]

doubled = list(map(lambda x: x * 2, numbers))
print(doubled)   # [10, 4, 18, 2, 14]

evens = list(filter(lambda x: x % 2 == 0, numbers))
print(evens)   # [2]
```

---

## 7. Recursive Functions

A function that **calls itself**.

```python
def factorial(n):
    if n == 0 or n == 1:    # base case
        return 1
    return n * factorial(n - 1)   # recursive call

print(factorial(5))   # 120
```

### Fibonacci with Recursion
```python
def fibonacci(n):
    if n <= 1:
        return n
    return fibonacci(n - 1) + fibonacci(n - 2)

for i in range(8):
    print(fibonacci(i), end=" ")   # 0 1 1 2 3 5 8 13
```

---

## 8. Scope — Local vs Global Variables

```python
x = 10   # global variable

def my_func():
    y = 20   # local variable — only exists inside function
    print(f"Inside: x={x}, y={y}")

my_func()
print(f"Outside: x={x}")
# print(y)   # ERROR — y doesn't exist here
```

### `global` Keyword
```python
counter = 0

def increment():
    global counter    # access the global variable
    counter += 1

increment()
increment()
print(counter)   # 2
```

---

## 9. Docstrings

Document your functions with docstrings.

```python
def calculate_area(length, width):
    """
    Calculate the area of a rectangle.

    Args:
        length (float): The length of the rectangle.
        width (float): The width of the rectangle.

    Returns:
        float: The area.
    """
    return length * width

help(calculate_area)   # shows the docstring
```

---

## 10. Higher-Order Functions

Functions that accept other functions as arguments.

```python
# map() — apply function to each element
numbers = [1, 2, 3, 4, 5]
squares = list(map(lambda x: x**2, numbers))
print(squares)   # [1, 4, 9, 16, 25]

# filter() — keep elements where function returns True
evens = list(filter(lambda x: x % 2 == 0, numbers))
print(evens)   # [2, 4]

# reduce() — combine elements
from functools import reduce
total = reduce(lambda a, b: a + b, numbers)
print(total)   # 15
```

---

## Summary

| Function Type    | Description                                  |
|-----------------|----------------------------------------------|
| Regular function | `def` with parameters and return             |
| Positional args  | Matched by position                          |
| Keyword args     | Matched by name                              |
| Default args     | Has default value                            |
| `*args`          | Variable number of positional arguments      |
| `**kwargs`       | Variable number of keyword arguments         |
| Lambda           | One-line anonymous function                  |
| Recursive        | Calls itself with a base case                |
| Higher-order     | Takes function as argument or returns one    |

> 💡 **Key Tip**: Every function should do **one thing well**. Keep them small and focused.
