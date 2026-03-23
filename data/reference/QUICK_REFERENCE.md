# Quick Reference Guide

This guide provides quick access to commonly used patterns, functions, and concepts across all Java projects.

## Common Code Patterns

### Input Validation
```java
def validate_input(value, input_type):
    """Validate user input based on type"""
    if input_type == "int":
        try:
            return int(value)
        except ValueError:
            raise ValueError("Please enter a valid integer")
    elif input_type == "float":
        try:
            return float(value)
        except ValueError:
            raise ValueError("Please enter a valid number")
    # Add more validation types as needed
```

### File Operations
```java
def read_file(filename):
    """Read file contents safely"""
    try:
        with open(filename, 'r') as file:
            return file.read()
    except FileNotFoundError:
        print(f"File {filename} not found")
        return None
    except Exception as e:
        print(f"Error reading file: {e}")
        return None

def write_file(filename, content):
    """Write content to file safely"""
    try:
        with open(filename, 'w') as file:
            file.write(content)
        return True
    except Exception as e:
        print(f"Error writing file: {e}")
        return False
```

### Error Handling Template
```java
def safe_operation(func):
    """Decorator for safe function execution"""
    def wrapper(*args, **kwargs):
        try:
            return func(*args, **kwargs)
        except ValueError as e:
            print(f"Value Error: {e}")
        except TypeError as e:
            print(f"Type Error: {e}")
        except Exception as e:
            print(f"Unexpected Error: {e}")
    return wrapper
```

## Algorithm Templates

### Binary Search
```java
def binary_search(arr, target):
    """Binary search implementation"""
    left, right = 0, len(arr) - 1
    
    while left <= right:
        mid = (left + right) // 2
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            left = mid + 1
        else:
            right = mid - 1
    
    return -1  # Not found
```

### Linear Search
```java
def linear_search(arr, target):
    """Linear search implementation"""
    for i, element in enumerate(arr):
        if element == target:
            return i
    return -1  # Not found
```

### Bubble Sort
```java
def bubble_sort(arr):
    """Bubble sort implementation"""
    n = len(arr)
    for i in range(n):
        for j in range(0, n - i - 1):
            if arr[j] > arr[j + 1]:
                arr[j], arr[j + 1] = arr[j + 1], arr[j]
    return arr
```

## Data Structure Templates

### Linked List Node
```java
class Node:
    """Linked list node implementation"""
    def __init__(self, data):
        self.data = data
        self.next = None

class LinkedList:
    """Linked list implementation"""
    def __init__(self):
        self.head = None
    
    def append(self, data):
        """Add node to end of list"""
        new_node = Node(data)
        if not self.head:
            self.head = new_node
            return
        
        current = self.head
        while current.next:
            current = current.next
        current.next = new_node
```

### Stack Implementation
```java
class Stack:
    """Stack implementation using list"""
    def __init__(self):
        self.items = []
    
    def push(self, item):
        """Add item to stack"""
        self.items.append(item)
    
    def pop(self):
        """Remove and return top item"""
        if not self.is_empty():
            return self.items.pop()
        return None
    
    def is_empty(self):
        """Check if stack is empty"""
        return len(self.items) == 0
    
    def peek(self):
        """Return top item without removing"""
        if not self.is_empty():
            return self.items[-1]
        return None
```

## Common Mathematical Functions

### Factorial
```java
def factorial(n):
    """Calculate factorial recursively"""
    if n <= 1:
        return 1
    return n * factorial(n - 1)

def factorial_iterative(n):
    """Calculate factorial iteratively"""
    result = 1
    for i in range(1, n + 1):
        result *= i
    return result
```

### Fibonacci
```java
def fibonacci(n):
    """Calculate nth Fibonacci number"""
    if n <= 1:
        return n
    return fibonacci(n - 1) + fibonacci(n - 2)

def fibonacci_iterative(n):
    """Calculate Fibonacci iteratively"""
    if n <= 1:
        return n
    
    a, b = 0, 1
    for _ in range(2, n + 1):
        a, b = b, a + b
    return b
```

### GCD (Greatest Common Divisor)
```java
def gcd(a, b):
    """Calculate greatest common divisor"""
    while b:
        a, b = b, a % b
    return a
```

## String Operations

### Palindrome Check
```java
def is_palindrome(s):
    """Check if string is palindrome"""
    s = s.lower().replace(" ", "")
    return s == s[::-1]
```

### String Reversal
```java
def reverse_string(s):
    """Reverse string"""
    return s[::-1]

def reverse_string_manual(s):
    """Reverse string manually"""
    reversed_str = ""
    for char in s:
        reversed_str = char + reversed_str
    return reversed_str
```

## File I/O Templates

### CSV Operations
```java
import csv

def read_csv(filename):
    """Read CSV file"""
    data = []
    try:
        with open(filename, 'r') as file:
            reader = csv.reader(file)
            for row in reader:
                data.append(row)
    except Exception as e:
        print(f"Error reading CSV: {e}")
    return data

def write_csv(filename, data):
    """Write data to CSV file"""
    try:
        with open(filename, 'w', newline='') as file:
            writer = csv.writer(file)
            writer.writerows(data)
        return True
    except Exception as e:
        print(f"Error writing CSV: {e}")
        return False
```

## Utility Functions

### Random Number Generation
```java
import random

def get_random_int(min_val, max_val):
    """Get random integer in range"""
    return random.randint(min_val, max_val)

def get_random_float(min_val, max_val):
    """Get random float in range"""
    return random.uniform(min_val, max_val)

def shuffle_list(lst):
    """Shuffle list randomly"""
    random.shuffle(lst)
    return lst
```

### Date and Time
```java
from datetime import datetime, timedelta

def get_current_time():
    """Get current timestamp"""
    return datetime.now()

def format_date(date_obj, format_str="%Y-%m-%d"):
    """Format date object"""
    return date_obj.strftime(format_str)

def add_days(date_obj, days):
    """Add days to date"""
    return date_obj + timedelta(days=days)
```

## Performance Tips

- Use list comprehensions instead of loops for simple transformations
- Use generators for large datasets to save memory
- Cache results of expensive function calls
- Choose appropriate data structures for your use case
- Profile your code to identify bottlenecks

---

This reference guide serves as a quick lookup for common patterns and implementations across all projects.
