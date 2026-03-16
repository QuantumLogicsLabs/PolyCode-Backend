# Python Conditional Statements

## What are Conditional Statements?
Conditional statements allow your program to **make decisions** — execute different code depending on whether a condition is `True` or `False`.

---

## 1. `if` Statement

Executes a block of code **only if** the condition is True.

```python
if condition:
    # code runs only when condition is True
```

### Example
```python
age = 20
if age >= 18:
    print("You are an adult.")
```

---

## 2. `if-else` Statement

Executes one block if True, **another block if False**.

```python
if condition:
    # runs when True
else:
    # runs when False
```

### Example
```python
age = 15
if age >= 18:
    print("Adult")
else:
    print("Minor")
```

---

## 3. `if-elif-else` (Multiple Conditions)

Tests multiple conditions in sequence — the **first True** block runs.

```python
if condition1:
    # runs if condition1 is True
elif condition2:
    # runs if condition2 is True
elif condition3:
    # runs if condition3 is True
else:
    # runs if none are True
```

### Example: Grade Classification
```python
marks = 78

if marks >= 90:
    grade = "A"
elif marks >= 80:
    grade = "B"
elif marks >= 70:
    grade = "C"
elif marks >= 60:
    grade = "D"
else:
    grade = "F"

print(f"Grade: {grade}")
```

---

## 4. Nested `if` Statements

An `if` inside another `if`.

```python
age = 25
has_id = True

if age >= 18:
    if has_id:
        print("Entry allowed")
    else:
        print("Need ID")
else:
    print("Too young")
```

---

## 5. Ternary (One-Line) `if`

A compact way to write simple `if-else`.

```python
value = true_value if condition else false_value
```

### Example
```python
age = 20
status = "Adult" if age >= 18 else "Minor"
print(status)

# Find max of two numbers
a, b = 10, 20
maximum = a if a > b else b
print(f"Max: {maximum}")
```

---

## 6. `match-case` Statement (Python 3.10+)

Similar to switch-case in other languages.

```python
match variable:
    case value1:
        # code
    case value2:
        # code
    case _:
        # default (like else)
```

### Example
```python
day = "Monday"

match day:
    case "Monday" | "Tuesday" | "Wednesday" | "Thursday" | "Friday":
        print("Weekday")
    case "Saturday" | "Sunday":
        print("Weekend")
    case _:
        print("Invalid day")
```

---

## Comparison Operators

| Operator | Meaning               | Example         |
|----------|-----------------------|-----------------|
| `==`     | Equal to              | `5 == 5` → True |
| `!=`     | Not equal to          | `5 != 3` → True |
| `>`      | Greater than          | `5 > 3` → True  |
| `<`      | Less than             | `3 < 5` → True  |
| `>=`     | Greater than or equal | `5 >= 5` → True |
| `<=`     | Less than or equal    | `3 <= 5` → True |

---

## Logical Operators

| Operator | Meaning                   | Example                       |
|----------|---------------------------|-------------------------------|
| `and`    | Both must be True         | `5 > 3 and 10 > 7` → True     |
| `or`     | At least one must be True | `5 > 10 or 3 > 1` → True      |
| `not`    | Reverses the condition    | `not (5 > 3)` → False         |

---

## Special Conditions

```python
# Check if value is None
x = None
if x is None:
    print("No value")

# Check membership
fruits = ["apple", "banana"]
if "apple" in fruits:
    print("Apple found!")

# Check empty string/list
name = ""
if not name:
    print("Name is empty")
```

---

## Summary

| Statement       | Use Case                                |
|-----------------|-----------------------------------------|
| `if`            | One condition                           |
| `if-else`       | Two paths (True/False)                  |
| `if-elif-else`  | Multiple conditions                     |
| Nested `if`     | Condition within condition              |
| Ternary         | One-line simple condition               |
| `match-case`    | Match one value against many patterns   |

> 💡 **Key Tip**: Python uses **indentation** (spaces/tabs) to define code blocks — not curly braces `{}`.
