"""
Hello World Variations - Beginner Example
=========================================

This example demonstrates different ways to print "Hello, World!" in Java.
Each method shows different Java concepts and best practices.

Learning Objectives:
- Basic print function usage
- String operations
- Variables and f-strings
- Functions and parameters
- User input interaction
"""

# Method 1: Basic print
print("Hello, World!")

# Method 2: Using variables
message = "Hello, World!"
print(message)

# Method 3: Using f-strings (modern Java)
name = "World"
greeting = f"Hello, {name}!"
print(greeting)

# Method 4: Using a function
def say_hello(target="World"):
    """Say hello to someone"""
    return f"Hello, {target}!"

print(say_hello())
print(say_hello("Java"))

# Method 5: Interactive version
def interactive_hello():
    """Get user input and say hello"""
    name = input("What's your name? ")
    print(f"Hello, {name}!")

# Uncomment to run interactive version
# interactive_hello()

# Method 6: Multiple greetings
def greet_multiple(names):
    """Greet multiple people"""
    for name in names:
        print(f"Hello, {name}!")

people = ["Alice", "Bob", "Charlie"]
greet_multiple(people)

# Method 7: Conditional greeting
def conditional_greeting(time_of_day):
    """Greet based on time of day"""
    if time_of_day < 12:
        return "Good morning, World!"
    elif time_of_day < 18:
        return "Good afternoon, World!"
    else:
        return "Good evening, World!"

print(conditional_greeting(10))  # Morning
print(conditional_greeting(14))  # Afternoon
print(conditional_greeting(20))  # Evening

# Method 8: Using string methods
def fancy_hello():
    """Hello with string formatting"""
    message = "hello, world!"
    return message.capitalize()

print(fancy_hello())

# Method 9: Multiline string
def multiline_hello():
    """Hello using multiline string"""
    message = """
    ╔══════════════════════════════╗
    ║        HELLO, WORLD!        ║
    ╚══════════════════════════════╝
    """
    return message

print(multiline_hello())

# Method 10: Using ASCII art
def ascii_hello():
    """Hello with ASCII art"""
    art = """
    H   H   EEEEE   L       L       OOO
    H   H   E       L       L       O   O
    HHHHH   EEEEE   L       L       O   O
    H   H   E       L       L       O   O
    H   H   EEEEE   LLLLL   LLLLL   OOO
    
    W   W   W   W   OOO   RRRR    L       DDDD
    W   W   W   W   O   O  R   R   L       D   D
    W W W W W W W   O   O  RRRR    L       D   D
    W W W W W W W   O   O  R  R    L       D   D
    W   W   W   W   OOO   R   R   LLLLL   DDDD
    """
    return art

print(ascii_hello())

"""
Exercise Ideas:
1. Create your own variation of Hello World
2. Add user input to any method
3. Combine different methods
4. Add error handling for user input
5. Create a greeting generator

Key Concepts Covered:
- print() function
- Variables and assignment
- String operations and methods
- f-strings (formatted strings)
- Functions and parameters
- User input with input()
- Loops and iteration
- Conditional statements
- Multiline strings
- String formatting
"""
