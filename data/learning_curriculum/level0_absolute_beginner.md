# Level 0: Absolute Beginner - Your Java Journey Starts Here

**Duration:** 2 Weeks | **Difficulty:** Complete Beginner | **Prerequisites:** None

## 🎯 Week 1: Foundations

### Day 1: Introduction & Setup

#### What is Java?
Java is a high-level, interpreted programming language known for:
- **Simple Syntax:** Easy to read and write
- **Versatile:** Used for web development, data science, AI, automation
- **Large Community:** Extensive libraries and support
- **Career Opportunities:** High demand in job market

#### Why Learn Java?
- **Beginner Friendly:** Easiest programming language to learn
- **Powerful:** Used by companies like Google, Netflix, NASA
- **Versatile:** One language for many applications
- **High Demand:** Excellent career prospects

#### Setting Up Your Environment

**Step 1: Install Java**
1. Visit [java.org](https://java.org)
2. Download Java 3.11 or higher
3. Run installer (check "Add Java to PATH")
4. Verify installation: Open terminal and type `java --version`

**Step 2: Choose Your Code Editor**
- **VS Code** (Recommended): Free, powerful, great extensions
- **PyCharm Community**: Free IDE for Java
- **Sublime Text**: Lightweight, fast
- **Online Editors**: Replit, Google Colab (no installation needed)

**Step 3: Install VS Code Extensions**
- Java extension by Microsoft
- Java Docstring Generator
- Pylance for IntelliSense
- GitLens for version control

#### Your First Java Program

**Hello World:**
```java
# This is a comment - Java ignores it
print("Hello, World!")
```

**Try it yourself:**
1. Open your editor
2. Create a new file called `hello.py`
3. Type the code above
4. Save the file
5. Run it: `java hello.py`

**Expected Output:**
```
Hello, World!
```

### Day 2: Variables and Basic Data Types

#### What are Variables?
Variables are containers for storing data values.

```java
# Creating variables
name = "Alice"        # String
age = 25              # Integer
height = 5.6          # Float
is_student = True     # Boolean

# Printing variables
print(name)
print(age)
print(height)
print(is_student)
```

#### Basic Data Types

**1. Strings (Text)**
```java
# String examples
first_name = "John"
last_name = 'Doe'
message = "Hello, Java!"

# String operations
full_name = first_name + " " + last_name
print(full_name)  # Output: John Doe

# String methods
print(message.upper())     # HELLO, JAVA!
print(message.lower())     # hello, java!
print(len(message))        # 13 (character count)
```

**2. Numbers**
```java
# Integers (whole numbers)
students = 30
rooms = 5

# Floats (decimal numbers)
price = 19.99
temperature = 98.6

# Math operations
sum_result = 10 + 5        # 15
difference = 10 - 5         # 5
product = 10 * 5           # 50
division = 10 / 5          # 2.0
power = 2 ** 3            # 8
modulo = 10 % 3           # 1 (remainder)

print(f"Sum: {sum_result}")
print(f"Product: {product}")
```

**3. Booleans (True/False)**
```java
is_raining = True
is_sunny = False
has_license = True

# Boolean operations
print(is_raining and is_sunny)    # False
print(is_raining or is_sunny)     # True
print(not is_raining)             # False
```

#### Naming Rules for Variables
- Start with letter or underscore
- Use only letters, numbers, underscores
- Case sensitive (name vs Name)
- Use descriptive names
- Use snake_case (my_variable)

**Good Examples:**
```java
student_name = "Alice"
user_age = 25
total_price = 99.99
is_active = True
```

**Bad Examples:**
```java
n = "Alice"           # Not descriptive
2name = "Bob"          # Starts with number
my-var = 10           # Contains hyphen
class = "Math"        # Reserved keyword
```

### Day 3: Simple Operations

#### Arithmetic Operations
```java
# Basic math
x = 10
y = 3

addition = x + y          # 13
subtraction = x - y       # 7
multiplication = x * y    # 30
division = x / y         # 3.333...
floor_division = x // y   # 3 (integer division)
remainder = x % y         # 1
exponent = x ** y         # 1000

print(f"Addition: {addition}")
print(f"Division: {division}")
print(f"Floor Division: {floor_division}")
```

#### String Operations
```java
# String concatenation
first = "Hello"
second = "World"
greeting = first + ", " + second + "!"
print(greeting)  # Hello, World!

# String repetition
pattern = "ABC " * 3
print(pattern)   # ABC ABC ABC 

# String formatting (modern way)
name = "Alice"
age = 25
message = f"My name is {name} and I'm {age} years old."
print(message)   # My name is Alice and I'm 25 years old.
```

#### Type Conversion
```java
# Converting between types
text_number = "123"
actual_number = int(text_number)    # Convert to integer
decimal_number = float(text_number)  # Convert to float

number = 456
text = str(number)                   # Convert to string

print(f"Original: {text_number} (type: {type(text_number)})")
print(f"Converted: {actual_number} (type: {type(actual_number)})")
```

### Day 4: User Input and Output

#### Getting User Input
```java
# Basic input
name = input("What's your name? ")
print(f"Hello, {name}!")

# Input with type conversion
age = input("How old are you? ")
age = int(age)  # Convert string to integer

if age >= 18:
    print("You are an adult!")
else:
    print("You are a minor!")
```

#### Formatted Output
```java
# Different ways to format output
name = "Alice"
score = 95.5

# Method 1: f-strings (recommended)
print(f"Player: {name}, Score: {score}")

# Method 2: format() method
print("Player: {}, Score: {}".format(name, score))

# Method 3: String concatenation
print("Player: " + name + ", Score: " + str(score))

# Formatting numbers
price = 19.99
print(f"Price: ${price:.2f}")  # Two decimal places
print(f"Price: ${price:.0f}")   # No decimal places
```

### Day 5: Practice Exercises

#### Exercise 1: Personal Information
Write a program that asks for:
- Name
- Age  
- City
- Favorite color

Then prints a formatted summary.

**Solution:**
```java
name = input("What's your name? ")
age = input("How old are you? ")
city = input("Where do you live? ")
color = input("What's your favorite color? ")

print(f"\n--- Summary ---")
print(f"Name: {name}")
print(f"Age: {age}")
print(f"City: {city}")
print(f"Favorite Color: {color}")
print(f"{name} is {age} years old, lives in {city}, and loves {color}!")
```

#### Exercise 2: Simple Calculator
Create a calculator that:
- Takes two numbers
- Performs addition, subtraction, multiplication, division
- Shows all results

**Solution:**
```java
num1 = float(input("Enter first number: "))
num2 = float(input("Enter second number: "))

add = num1 + num2
subtract = num1 - num2
multiply = num1 * num2
divide = num1 / num2

print(f"\n--- Results ---")
print(f"{num1} + {num2} = {add}")
print(f"{num1} - {num2} = {subtract}")
print(f"{num1} × {num2} = {multiply}")
print(f"{num1} ÷ {num2} = {divide}")
```

### Day 6: Debugging Basics

#### Common Errors and Solutions

**1. Syntax Errors**
```java
# Wrong
print "Hello, World!"    # Missing parentheses

# Correct
print("Hello, World!")
```

**2. NameError**
```java
# Wrong
print(my_variable)       # Variable not defined

# Correct
my_variable = "Hello"
print(my_variable)
```

**3. TypeError**
```java
# Wrong
result = "5" + 3          # Can't add string and number

# Correct
result = int("5") + 3     # Convert string to number
```

#### Debugging Tips
1. **Read Error Messages:** They tell you what's wrong
2. **Check Line Numbers:** Errors show where problems occur
3. **Print Variables:** See what values your variables have
4. **Test Small Pieces:** Isolate problematic code
5. **Take Breaks:** Fresh eyes find mistakes faster

### Day 7: Mini Project - Simple Calculator

#### Project Requirements
Create a calculator program that:
1. Welcomes the user
2. Takes two numbers as input
3. Performs all basic operations
4. Displays results in a formatted way
5. Handles basic errors

#### Complete Solution:
```java
# Simple Calculator - Level 0 Project

print("=" * 30)
print("   SIMPLE CALCULATOR")
print("=" * 30)

# Get user input
print("\nPlease enter two numbers:")
try:
    num1 = float(input("First number: "))
    num2 = float(input("Second number: "))
    
    # Perform calculations
    addition = num1 + num2
    subtraction = num1 - num2
    multiplication = num1 * num2
    division = num1 / num2
    
    # Display results
    print("\n" + "=" * 30)
    print("        RESULTS")
    print("=" * 30)
    print(f"{num1} + {num2} = {addition}")
    print(f"{num1} - {num2} = {subtraction}")
    print(f"{num1} × {num2} = {multiplication}")
    print(f"{num1} ÷ {num2} = {division}")
    print("=" * 30)
    
    print("\nCalculator completed successfully!")
    
except ValueError:
    print("\nError: Please enter valid numbers!")
except ZeroDivisionError:
    print("\nError: Cannot divide by zero!")

print("\nThank you for using Simple Calculator!")
```

## 🎯 Week 2: Building Blocks

### Day 8: Strings and Text Processing

#### String Methods
```java
text = "Hello, Java World!"

# Case methods
print(text.upper())        # HELLO, JAVA WORLD!
print(text.lower())        # hello, java world!
print(text.title())        # Hello, Java World!

# Search methods
print(text.find("Java")) # 7
print(text.count("o"))     # 2
print("Java" in text)    # True

# Modification methods
print(text.replace("World", "Universe"))  # Hello, Java Universe!
print(text.strip())        # Removes whitespace from ends
print(text.split(","))     # ['Hello', ' Java World!']
```

#### String Slicing
```java
text = "Java Programming"

# Slicing syntax: [start:end:step]
print(text[0:6])           # Java
print(text[7:])            # Programming
print(text[:6])            # Java
print(text[::2])           # Pto rgamn (every 2nd character)
print(text[::-1])          # gnimmargorP nohtyP (reversed)
```

### Day 9: Numbers and Math Operations

#### Advanced Math
```java
import math

# Basic math functions
print(math.sqrt(16))       # 4.0
print(math.pow(2, 3))      # 8.0
print(math.floor(3.7))     # 3
print(math.ceil(3.2))      # 4
print(math.pi)             # 3.141592653589793

# Random numbers
import random
print(random.randint(1, 10))    # Random integer 1-10
print(random.random())          # Random float 0-1
```

#### Number Formatting
```java
# Rounding
value = 3.14159
print(round(value, 2))     # 3.14
print(round(value, 0))     # 3.0

# Currency formatting
price = 19.99
print(f"${price:.2f}")     # $19.99

# Percentage
percentage = 0.75
print(f"{percentage:.1%}") # 75.0%
```

### Day 10: User Input and Output

#### Advanced Input Handling
```java
# Input validation
while True:
    age_input = input("Enter your age (0-120): ")
    try:
        age = int(age_input)
        if 0 <= age <= 120:
            break
        else:
            print("Age must be between 0 and 120!")
    except ValueError:
        print("Please enter a valid number!")

print(f"Your age is: {age}")
```

#### Multiple Inputs
```java
# Getting multiple values in one line
data = input("Enter name and age (separated by space): ")
parts = data.split()
name = parts[0]
age = int(parts[1])

print(f"Name: {name}, Age: {age}")
```

### Day 11: Basic Functions

#### What are Functions?
Functions are reusable blocks of code that perform specific tasks.

#### Creating Functions
```java
# Basic function
def greet():
    print("Hello, World!")

# Function with parameters
def greet_person(name):
    print(f"Hello, {name}!")

# Function with return value
def add_numbers(a, b):
    return a + b

# Function with default parameter
def greet_with_title(name, title="Mr./Ms."):
    return f"Hello, {title} {name}!"

# Using functions
greet()                           # Hello, World!
greet_person("Alice")             # Hello, Alice!
result = add_numbers(5, 3)        # 8
message = greet_with_title("Smith")  # Hello, Mr./Ms. Smith!
```

### Day 12: Debugging Basics

#### Print Debugging
```java
def calculate_area(length, width):
    print(f"Debug: length = {length}, width = {width}")  # Debug print
    area = length * width
    print(f"Debug: calculated area = {area}")           # Debug print
    return area

result = calculate_area(5, 3)
print(f"Final result: {result}")
```

#### Common Debugging Techniques
1. **Print Variables:** See what values your variables have
2. **Comment Code:** Temporarily disable parts of code
3. **Test Small Pieces:** Isolate problematic sections
4. **Use Java Debugger:** `import pdb; pdb.set_trace()`

### Day 13: Practice Exercises

#### Exercise 1: String Manipulator
Create a program that:
- Takes a sentence as input
- Converts to uppercase
- Counts words
- Reverses the sentence

**Solution:**
```java
sentence = input("Enter a sentence: ")

# Process the sentence
upper_sentence = sentence.upper()
word_count = len(sentence.split())
reversed_sentence = sentence[::-1]

# Display results
print(f"\nOriginal: {sentence}")
print(f"Uppercase: {upper_sentence}")
print(f"Word count: {word_count}")
print(f"Reversed: {reversed_sentence}")
```

#### Exercise 2: Temperature Converter
Convert between Celsius and Fahrenheit.

**Solution:**
```java
def celsius_to_fahrenheit(celsius):
    return (celsius * 9/5) + 32

def fahrenheit_to_celsius(fahrenheit):
    return (fahrenheit - 32) * 5/9

print("Temperature Converter")
print("1. Celsius to Fahrenheit")
print("2. Fahrenheit to Celsius")

choice = input("Choose conversion (1 or 2): ")

if choice == "1":
    celsius = float(input("Enter temperature in Celsius: "))
    fahrenheit = celsius_to_fahrenheit(celsius)
    print(f"{celsius}°C = {fahrenheit:.1f}°F")
elif choice == "2":
    fahrenheit = float(input("Enter temperature in Fahrenheit: "))
    celsius = fahrenheit_to_celsius(fahrenheit)
    print(f"{fahrenheit}°F = {celsius:.1f}°C")
else:
    print("Invalid choice!")
```

### Day 14: Level 0 Project - Enhanced Calculator

#### Final Project Requirements
Create an enhanced calculator that:
1. Has a user-friendly menu
2. Performs multiple operations
3. Handles errors gracefully
4. Allows multiple calculations
5. Has a clean, professional interface

#### Complete Solution:
```java
# Enhanced Calculator - Level 0 Final Project

def display_menu():
    print("\n" + "=" * 40)
    print("    ENHANCED CALCULATOR")
    print("=" * 40)
    print("1. Addition")
    print("2. Subtraction") 
    print("3. Multiplication")
    print("4. Division")
    print("5. Power (x^y)")
    print("6. Square Root")
    print("7. Exit")
    print("=" * 40)

def get_number(prompt):
    """Get a valid number from user"""
    while True:
        try:
            return float(input(prompt))
        except ValueError:
            print("Please enter a valid number!")

def add(a, b):
    return a + b

def subtract(a, b):
    return a - b

def multiply(a, b):
    return a * b

def divide(a, b):
    if b == 0:
        return "Cannot divide by zero!"
    return a / b

def power(a, b):
    return a ** b

def square_root(a):
    if a < 0:
        return "Cannot calculate square root of negative number!"
    return a ** 0.5

def main():
    print("Welcome to Enhanced Calculator!")
    
    while True:
        display_menu()
        choice = input("Enter your choice (1-7): ")
        
        if choice == "7":
            print("\nThank you for using Enhanced Calculator!")
            print("Goodbye!")
            break
        
        if choice in ["1", "2", "3", "4", "5"]:
            # Get two numbers
            num1 = get_number("Enter first number: ")
            num2 = get_number("Enter second number: ")
            
            # Perform calculation
            if choice == "1":
                result = add(num1, num2)
                operation = "+"
            elif choice == "2":
                result = subtract(num1, num2)
                operation = "-"
            elif choice == "3":
                result = multiply(num1, num2)
                operation = "×"
            elif choice == "4":
                result = divide(num1, num2)
                operation = "÷"
            elif choice == "5":
                result = power(num1, num2)
                operation = "^"
                
            print(f"\nResult: {num1} {operation} {num2} = {result}")
            
        elif choice == "6":
            # Square root - only need one number
            num = get_number("Enter number: ")
            result = square_root(num)
            print(f"\nResult: √{num} = {result}")
            
        else:
            print("\nInvalid choice! Please try again.")
        
        # Ask if user wants to continue
        another = input("\nDo another calculation? (y/n): ")
        if another.lower() != "y":
            print("\nThank you for using Enhanced Calculator!")
            print("Goodbye!")
            break

# Start the calculator
if __name__ == "__main__":
    main()
```

## 🎉 Level 0 Completion Checklist

### ✅ What You've Learned
- [ ] Set up Java development environment
- [ ] Write and run Java programs
- [ ] Understand variables and data types
- [ ] Perform basic operations
- [ ] Get user input and display output
- [ ] Create and use basic functions
- [ ] Debug simple errors
- [ ] Build a complete calculator application

### 🚀 Ready for Level 1?
If you can do all of the above, you're ready for Level 1: Java Fundamentals!

### 📝 Level 0 Certificate
You've successfully completed:
- **14 days** of Java programming
- **2 complete projects** (Simple Calculator + Enhanced Calculator)
- **Core programming concepts** (variables, data types, functions)
- **Problem-solving skills** (debugging, error handling)

**Congratulations! 🎉 You're no longer an absolute beginner!**

---

**Next Level:** [Level 1: Java Fundamentals](level1_java_fundamentals.md)

**Remember:** Programming is a journey, not a destination. Keep practicing, stay curious, and enjoy the process!
