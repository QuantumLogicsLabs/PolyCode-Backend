# Python I/O Statements

## What is I/O?
**Input/Output (I/O)** refers to how a Python program communicates with the outside world — reading data from users or files, and displaying results.

---

## 1. Output — `print()`

The `print()` function displays information to the screen.

### Basic Syntax
```python
print(value1, value2, ..., sep=' ', end='\n')
```

### Types of Output

#### Simple Print
```python
print("Hello, World!")
print(42)
print(3.14)
print(True)
```

#### Print with Variables
```python
name = "Alice"
age = 25
print(name)
print(age)
```

#### Print Multiple Values
```python
print("Name:", name, "Age:", age)
```

#### Print with `sep` (custom separator)
```python
print("Python", "is", "awesome", sep="-")
# Output: Python-is-awesome
```

#### Print with `end` (custom ending)
```python
print("Hello", end=" ")
print("World")
# Output: Hello World  (on same line)
```

#### Formatted Output — f-strings (Recommended)
```python
name = "Alice"
age = 25
print(f"Name: {name}, Age: {age}")
```

#### Formatted Output — `.format()` method
```python
print("Name: {}, Age: {}".format(name, age))
print("Name: {0}, Age: {1}".format(name, age))
```

#### Formatted Output — `%` operator (old style)
```python
print("Name: %s, Age: %d" % (name, age))
```

#### Printing Special Characters
```python
print("Line1\nLine2")      # newline
print("Col1\tCol2")        # tab
print("He said \"Hello\"") # quotes
print("Path: C:\\Users")   # backslash
```

---

## 2. Input — `input()`

The `input()` function reads a line of text entered by the user.

### Basic Syntax
```python
variable = input(prompt)
```

> ⚠️ `input()` **always returns a string**. You must convert it for numbers.

### Types of Input

#### Simple String Input
```python
name = input("Enter your name: ")
print(f"Hello, {name}!")
```

#### Integer Input
```python
age = int(input("Enter your age: "))
print(f"You are {age} years old.")
```

#### Float Input
```python
height = float(input("Enter your height in meters: "))
print(f"Your height is {height}m")
```

#### Multiple Inputs on One Line (split)
```python
x, y = input("Enter two numbers separated by space: ").split()
x, y = int(x), int(y)
print(f"Sum = {x + y}")
```

#### Multiple Inputs with map()
```python
a, b, c = map(int, input("Enter three numbers: ").split())
print(a, b, c)
```

---

## 3. Type Conversion for Input

| Function | Converts To |
|----------|------------|
| `int()`  | Integer    |
| `float()`| Float      |
| `str()`  | String     |
| `bool()` | Boolean    |

```python
num_str = input("Enter a number: ")   # "42" (string)
num_int = int(num_str)                 # 42   (integer)
num_float = float(num_str)             # 42.0 (float)
```

---

## 4. File I/O

### Writing to a File
```python
with open("output.txt", "w") as file:
    file.write("Hello, File!\n")
    file.write("Second line\n")
```

### Reading from a File
```python
with open("output.txt", "r") as file:
    content = file.read()
    print(content)
```

### Reading Line by Line
```python
with open("output.txt", "r") as file:
    for line in file:
        print(line.strip())
```

### File Modes

| Mode | Description        |
|------|--------------------|
| `r`  | Read (default)     |
| `w`  | Write (overwrite)  |
| `a`  | Append             |
| `r+` | Read and Write     |

---

## Summary

| Feature | Function | Returns |
|---------|----------|---------|
| Display output | `print()` | Nothing |
| Read user input | `input()` | String  |
| Read file | `open()` + `.read()` | String |
| Write file | `open()` + `.write()` | Nothing |

> 💡 **Key Tip**: Always convert `input()` to the required type (`int`, `float`) before doing arithmetic.
