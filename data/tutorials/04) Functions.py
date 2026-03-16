# ============================================================
#  Python Functions — Complete Examples
# ============================================================

# ─────────────────────────────────────────────
# SECTION 1: Basic Function (no args, no return)
# ─────────────────────────────────────────────
print("=" * 50)
print("SECTION 1: Basic Functions")
print("=" * 50)

def say_hello():
    """Simple function with no parameters."""
    print("Hello, World!")

def print_separator():
    print("-" * 30)

say_hello()
say_hello()    # reuse it!
print_separator()

# ─────────────────────────────────────────────
# SECTION 2: Functions with Parameters
# ─────────────────────────────────────────────
print("\n--- Functions with Parameters ---")

def greet(name):
    print(f"Hello, {name}!")

def introduce(name, age):
    print(f"I'm {name} and I'm {age} years old.")

greet("Alice")
greet("Bob")
introduce("Charlie", 30)

# ─────────────────────────────────────────────
# SECTION 3: Functions with Return Values
# ─────────────────────────────────────────────
print("\n--- Functions with Return Values ---")

def add(a, b):
    return a + b

def multiply(a, b):
    return a * b

def is_even(n):
    return n % 2 == 0

def circle_area(radius):
    import math
    return math.pi * radius ** 2

print(f"5 + 3 = {add(5, 3)}")
print(f"4 × 6 = {multiply(4, 6)}")
print(f"Is 8 even? {is_even(8)}")
print(f"Circle area (r=5): {circle_area(5):.2f}")

# Multiple return values
def min_max(numbers):
    """Return both min and max of a list."""
    return min(numbers), max(numbers)

data = [3, 1, 7, 4, 9, 2, 8]
low, high = min_max(data)
print(f"Data: {data}")
print(f"Min: {low}, Max: {high}")

# ─────────────────────────────────────────────
# SECTION 4: Positional Arguments
# ─────────────────────────────────────────────
print("\n--- Positional Arguments ---")

def power(base, exponent):
    return base ** exponent

def rectangle_area(length, width):
    return length * width

print(f"2^10 = {power(2, 10)}")
print(f"Rectangle 5×8 = {rectangle_area(5, 8)}")

# ─────────────────────────────────────────────
# SECTION 5: Keyword Arguments
# ─────────────────────────────────────────────
print("\n--- Keyword Arguments ---")

def describe_person(name, age, city):
    print(f"{name} is {age} years old, lives in {city}")

# Order doesn't matter with keyword args
describe_person(name="Alice", age=25, city="Paris")
describe_person(age=30, city="Tokyo", name="Bob")
describe_person("Charlie", city="London", age=22)

# ─────────────────────────────────────────────
# SECTION 6: Default Arguments
# ─────────────────────────────────────────────
print("\n--- Default Arguments ---")

def greet_person(name, greeting="Hello", punctuation="!"):
    print(f"{greeting}, {name}{punctuation}")

greet_person("Alice")                          # uses defaults
greet_person("Bob", "Hi there")               # custom greeting
greet_person("Charlie", "Howdy", "!!!")       # custom both

def create_email(username, domain="gmail.com"):
    return f"{username}@{domain}"

print(create_email("alice"))
print(create_email("bob", "yahoo.com"))
print(create_email("charlie", "company.org"))

# ─────────────────────────────────────────────
# SECTION 7: *args — Variable Positional Arguments
# ─────────────────────────────────────────────
print("\n--- *args ---")

def total(*numbers):
    """Sum any number of values."""
    print(f"Received: {numbers}")
    return sum(numbers)

print(f"Sum: {total(1, 2, 3)}")
print(f"Sum: {total(10, 20, 30, 40, 50)}")
print(f"Sum: {total(5)}")

def display_items(label, *items):
    print(f"{label}:")
    for item in items:
        print(f"  - {item}")

display_items("Fruits", "apple", "banana", "mango")
display_items("Numbers", 1, 2, 3, 4)

# ─────────────────────────────────────────────
# SECTION 8: **kwargs — Variable Keyword Arguments
# ─────────────────────────────────────────────
print("\n--- **kwargs ---")

def show_profile(**info):
    """Show any key-value information."""
    print("Profile:")
    for key, value in info.items():
        print(f"  {key}: {value}")

show_profile(name="Alice", age=25, job="Developer", city="NYC")
show_profile(brand="Toyota", model="Camry", year=2022)

def build_url(base_url, **params):
    query = "&".join(f"{k}={v}" for k, v in params.items())
    return f"{base_url}?{query}" if query else base_url

url = build_url("https://api.example.com/search", q="python", page=1, limit=10)
print(f"URL: {url}")

# ─────────────────────────────────────────────
# SECTION 9: Combining *args and **kwargs
# ─────────────────────────────────────────────
print("\n--- Combining *args and **kwargs ---")

def full_function(required, *args, optional="default", **kwargs):
    print(f"required={required}")
    print(f"args={args}")
    print(f"optional={optional}")
    print(f"kwargs={kwargs}")

full_function("first", "extra1", "extra2",
              optional="custom", name="Alice", age=25)

# ─────────────────────────────────────────────
# SECTION 10: Lambda Functions
# ─────────────────────────────────────────────
print("\n--- Lambda Functions ---")

# Basic lambdas
square = lambda x: x ** 2
add = lambda a, b: a + b
is_positive = lambda n: n > 0

print(f"square(7) = {square(7)}")
print(f"add(3, 4) = {add(3, 4)}")
print(f"is_positive(-5) = {is_positive(-5)}")

# Lambda with sorted()
students = [("Alice", 85), ("Bob", 92), ("Charlie", 78)]
by_score = sorted(students, key=lambda s: s[1], reverse=True)
print(f"By score: {by_score}")

# Lambda with map()
numbers = [1, 2, 3, 4, 5]
cubed = list(map(lambda x: x ** 3, numbers))
print(f"Cubed: {cubed}")

# Lambda with filter()
words = ["apple", "ant", "banana", "avocado", "cherry"]
a_words = list(filter(lambda w: w.startswith("a"), words))
print(f"Words starting with 'a': {a_words}")

# ─────────────────────────────────────────────
# SECTION 11: Recursive Functions
# ─────────────────────────────────────────────
print("\n--- Recursive Functions ---")

def factorial(n):
    """n! = n × (n-1) × ... × 1"""
    if n == 0 or n == 1:    # base case
        return 1
    return n * factorial(n - 1)

for i in range(8):
    print(f"{i}! = {factorial(i)}")

def fibonacci(n):
    """Returns nth Fibonacci number."""
    if n <= 1:
        return n
    return fibonacci(n - 1) + fibonacci(n - 2)

print("\nFibonacci sequence:")
fib_sequence = [fibonacci(i) for i in range(10)]
print(fib_sequence)

def sum_digits(n):
    """Sum all digits of a number recursively."""
    if n < 10:
        return n
    return n % 10 + sum_digits(n // 10)

print(f"\nSum of digits of 12345: {sum_digits(12345)}")

# ─────────────────────────────────────────────
# SECTION 12: Scope — Local vs Global
# ─────────────────────────────────────────────
print("\n--- Scope ---")

x = 100   # global

def local_example():
    y = 200   # local
    print(f"Inside function: x={x}, y={y}")

local_example()
print(f"Outside function: x={x}")
# y is not accessible here

# Global keyword
counter = 0

def increment():
    global counter
    counter += 1
    print(f"Counter: {counter}")

increment()
increment()
increment()
print(f"Final counter: {counter}")

# ─────────────────────────────────────────────
# SECTION 13: Higher-Order Functions
# ─────────────────────────────────────────────
print("\n--- Higher-Order Functions ---")

from functools import reduce

numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

total = reduce(lambda a, b: a + b, numbers)
print(f"Sum via reduce: {total}")

squares = list(map(lambda x: x**2, numbers))
print(f"Squares: {squares}")

evens = list(filter(lambda x: x % 2 == 0, numbers))
print(f"Evens: {evens}")

# Function as argument
def apply_twice(func, value):
    return func(func(value))

double = lambda x: x * 2
print(f"Apply double twice to 3: {apply_twice(double, 3)}")   # 12

# ─────────────────────────────────────────────
# SECTION 14: Docstrings
# ─────────────────────────────────────────────
print("\n--- Docstrings ---")

def calculate_bmi(weight, height):
    """
    Calculate Body Mass Index (BMI).

    Args:
        weight (float): Weight in kilograms.
        height (float): Height in meters.

    Returns:
        float: The BMI value.

    Example:
        >>> calculate_bmi(70, 1.75)
        22.86
    """
    return weight / (height ** 2)

bmi = calculate_bmi(70, 1.75)
print(f"BMI: {bmi:.2f}")
print(f"Docstring: {calculate_bmi.__doc__}")

# ─────────────────────────────────────────────
# SECTION 15: Real-World Mini Program
# ─────────────────────────────────────────────
print("\n--- Mini Program: Student Grade System ---")

def get_grade(score):
    if score >= 90: return "A"
    elif score >= 80: return "B"
    elif score >= 70: return "C"
    elif score >= 60: return "D"
    else: return "F"

def calculate_average(*scores):
    return sum(scores) / len(scores)

def print_report(name, *scores):
    avg = calculate_average(*scores)
    grade = get_grade(avg)
    print(f"\nStudent: {name}")
    print(f"Scores: {scores}")
    print(f"Average: {avg:.1f}")
    print(f"Final Grade: {grade}")

print_report("Alice", 88, 92, 85, 90, 87)
print_report("Bob", 72, 65, 78, 80, 70)
print_report("Charlie", 55, 60, 52, 48, 58)

print("\n✅ All Function examples completed!")
