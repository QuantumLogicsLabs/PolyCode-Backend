# Python Repetitive Statements (Loops)

## What are Loops?
Loops allow you to **repeat a block of code** multiple times without writing it again and again.

Python has two main loop types: `for` and `while`.

---

## 1. `for` Loop

Used to iterate over a **sequence** (list, string, range, tuple, etc.)

```python
for variable in sequence:
    # code to repeat
```

### 1.1 — `for` with `range()`

`range(start, stop, step)` generates a sequence of numbers.

```python
# range(stop) — 0 to stop-1
for i in range(5):
    print(i)   # 0, 1, 2, 3, 4

# range(start, stop)
for i in range(1, 6):
    print(i)   # 1, 2, 3, 4, 5

# range(start, stop, step)
for i in range(0, 11, 2):
    print(i)   # 0, 2, 4, 6, 8, 10

# Countdown
for i in range(5, 0, -1):
    print(i)   # 5, 4, 3, 2, 1
```

### 1.2 — `for` over a List
```python
fruits = ["apple", "banana", "mango"]
for fruit in fruits:
    print(fruit)
```

### 1.3 — `for` over a String
```python
for char in "Python":
    print(char)   # P, y, t, h, o, n
```

### 1.4 — `for` with `enumerate()`
Get both index and value.
```python
colors = ["red", "green", "blue"]
for index, color in enumerate(colors):
    print(f"{index}: {color}")
```

### 1.5 — `for` over a Dictionary
```python
student = {"name": "Alice", "age": 20, "grade": "A"}
for key, value in student.items():
    print(f"{key}: {value}")
```

---

## 2. `while` Loop

Repeats **as long as a condition is True**.

```python
while condition:
    # code to repeat
```

### 2.1 — Basic `while`
```python
count = 1
while count <= 5:
    print(count)
    count += 1    # Very important — update the condition!
```

### 2.2 — `while` with User Input
```python
password = ""
while password != "secret":
    password = input("Enter password: ")
print("Access granted!")
```

### 2.3 — Infinite `while` with `break`
```python
while True:
    user_input = input("Type 'quit' to exit: ")
    if user_input == "quit":
        break
    print(f"You typed: {user_input}")
```

---

## 3. Loop Control Statements

### `break` — Exit the loop immediately
```python
for i in range(10):
    if i == 5:
        break
    print(i)   # 0, 1, 2, 3, 4
```

### `continue` — Skip current iteration
```python
for i in range(10):
    if i % 2 == 0:
        continue   # skip even numbers
    print(i)   # 1, 3, 5, 7, 9
```

### `pass` — Do nothing (placeholder)
```python
for i in range(5):
    if i == 3:
        pass   # placeholder — no action
    print(i)
```

---

## 4. `else` with Loops

The `else` block runs when the loop **finishes normally** (without `break`).

```python
for i in range(5):
    print(i)
else:
    print("Loop completed!")

# With break — else does NOT run
for i in range(5):
    if i == 3:
        break
    print(i)
else:
    print("This won't print")
```

---

## 5. Nested Loops

A loop inside another loop.

```python
for i in range(1, 4):       # outer
    for j in range(1, 4):   # inner
        print(i, j)
```

### Multiplication Table
```python
for i in range(1, 6):
    for j in range(1, 6):
        print(f"{i*j:3}", end="")
    print()
```

---

## 6. List Comprehension (Compact Loop)

Create a new list from a loop in one line.

```python
# Regular way
squares = []
for n in range(1, 6):
    squares.append(n ** 2)

# List comprehension way
squares = [n ** 2 for n in range(1, 6)]
print(squares)   # [1, 4, 9, 16, 25]

# With condition
evens = [n for n in range(1, 11) if n % 2 == 0]
print(evens)   # [2, 4, 6, 8, 10]
```

---

## Summary

| Loop Type     | Use When                                  |
|---------------|-------------------------------------------|
| `for`         | You know how many times to loop           |
| `while`       | You loop until a condition changes        |
| `break`       | Exit the loop early                       |
| `continue`    | Skip current iteration                    |
| `pass`        | Placeholder (no operation)                |
| Nested loop   | Loop inside a loop (2D patterns, tables)  |

> ⚠️ **Warning**: Always ensure a `while` loop has a way to exit — otherwise it loops forever (infinite loop).
