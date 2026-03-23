# Level 1: Java Fundamentals - Building Strong Foundations

**Duration:** 4 Weeks | **Difficulty:** Beginner | **Prerequisites:** Level 0 Complete

## 🎯 Week 3: Control Flow and Decision Making

### Day 15: If/Else Statements

#### Understanding Conditional Logic
Conditional statements allow your program to make decisions based on conditions.

#### Basic If Statement
```java
age = 18

if age >= 18:
    print("You are an adult!")
    print("You can vote!")
```

#### If-Else Statement
```java
age = 16

if age >= 18:
    print("You are an adult!")
else:
    print("You are a minor!")
```

#### If-Elif-Else Chain
```java
grade = 85

if grade >= 90:
    letter = "A"
elif grade >= 80:
    letter = "B"
elif grade >= 70:
    letter = "C"
elif grade >= 60:
    letter = "D"
else:
    letter = "F"

print(f"Your grade: {grade} = {letter}")
```

#### Nested Conditions
```java
age = 25
has_license = True

if age >= 18:
    if has_license:
        print("You can drive!")
    else:
        print("You need to get a license first!")
else:
    print("You're too young to drive!")
```

### Day 16: Logical Operators

#### AND Operator
Both conditions must be True:
```java
age = 25
has_license = True

if age >= 18 and has_license:
    print("You can drive!")
else:
    print("You cannot drive!")

# More examples
temperature = 75
weather = "sunny"

if temperature > 70 and weather == "sunny":
    print("Perfect day for a picnic!")
```

#### OR Operator
At least one condition must be True:
```java
day = "Saturday"
is_holiday = True

if day == "Saturday" or day == "Sunday" or is_holiday:
    print("No work today!")
else:
    print("Time to work!")

# Practical example
has_car = False
has_bike = True

if has_car or has_bike:
    print("You can get around!")
else:
    print("You need transportation!")
```

#### NOT Operator
Reverses the condition:
```java
is_raining = False

if not is_raining:
    print("Let's go outside!")
else:
    print("Stay indoors!")

# Combining with other operators
age = 17
has_parent_permission = True

if age >= 18 or (age < 18 and has_parent_permission):
    print("You can attend the concert!")
else:
    print("Sorry, you cannot attend!")
```

#### Complex Conditions
```java
# Student eligibility example
age = 20
gpa = 3.5
has_extracurricular = True
citizen = True

if age >= 18 and age <= 25 and gpa >= 3.0 and has_extracurricular and citizen:
    print("Eligible for scholarship!")
else:
    print("Not eligible for scholarship.")
    
    # Provide specific reasons
    if age < 18 or age > 25:
        print("- Age requirement not met")
    if gpa < 3.0:
        print("- GPA too low")
    if not has_extracurricular:
        print("- No extracurricular activities")
    if not citizen:
        print("- Not a citizen")
```

### Day 17: Nested Conditions and Complex Logic

#### Multi-Level Decision Making
```java
def classify_person(age, student_status, employment_status):
    """Classify person based on multiple factors"""
    
    if age < 18:
        return "Minor"
    else:
        if student_status == "full-time":
            return "Full-time Student"
        elif student_status == "part-time":
            if employment_status == "full-time":
                return "Working Part-time Student"
            else:
                return "Part-time Student"
        else:
            if employment_status == "full-time":
                return "Full-time Employee"
            elif employment_status == "part-time":
                return "Part-time Employee"
            else:
                return "Unemployed"

# Test the function
print(classify_person(16, "none", "none"))           # Minor
print(classify_person(20, "full-time", "none"))       # Full-time Student
print(classify_person(22, "part-time", "full-time"))  # Working Part-time Student
```

#### Practical Example: Loan Approval
```java
def approve_loan(credit_score, income, employment_years, debt_ratio):
    """Determine loan approval status"""
    
    # Basic eligibility
    if credit_score < 600:
        return "Denied: Credit score too low"
    
    if income < 20000:
        return "Denied: Income too low"
    
    if employment_years < 1:
        return "Denied: Insufficient employment history"
    
    if debt_ratio > 0.4:
        return "Denied: Debt-to-income ratio too high"
    
    # Determine loan amount and interest rate
    if credit_score >= 750 and income >= 50000:
        return "Approved: Maximum amount, Prime rate"
    elif credit_score >= 700 and income >= 35000:
        return "Approved: Standard amount, Good rate"
    else:
        return "Approved: Limited amount, Standard rate"

# Test cases
print(approve_loan(780, 75000, 5, 0.2))  # Best case
print(approve_loan(650, 30000, 2, 0.3))  # Average case
print(approve_loan(550, 25000, 3, 0.5))  # Denied
```

### Day 18: Practice Problems

#### Exercise 1: Grade Calculator
Create a program that calculates letter grades and GPA.

```java
def calculate_grade(score):
    """Convert numeric score to letter grade"""
    if score >= 97:
        return "A+", 4.0
    elif score >= 93:
        return "A", 4.0
    elif score >= 90:
        return "A-", 3.7
    elif score >= 87:
        return "B+", 3.3
    elif score >= 83:
        return "B", 3.0
    elif score >= 80:
        return "B-", 2.7
    elif score >= 77:
        return "C+", 2.3
    elif score >= 73:
        return "C", 2.0
    elif score >= 70:
        return "C-", 1.7
    elif score >= 67:
        return "D+", 1.3
    elif score >= 65:
        return "D", 1.0
    else:
        return "F", 0.0

def calculate_gpa(grades_list):
    """Calculate GPA from a list of grades"""
    total_points = 0
    for grade in grades_list:
        letter, points = calculate_grade(grade)
        total_points += points
    return total_points / len(grades_list)

# Test the functions
scores = [92, 85, 78, 95, 88]
for score in scores:
    letter, points = calculate_grade(score)
    print(f"Score {score}: Grade {letter} ({points} points)")

gpa = calculate_gpa(scores)
print(f"Overall GPA: {gpa:.2f}")
```

#### Exercise 2: Ticket Pricing System
Calculate ticket prices based on age and membership.

```java
def calculate_ticket_price(age, is_member, is_student, day_of_week):
    """Calculate ticket price based on multiple factors"""
    
    base_price = 10.00
    
    # Age discounts
    if age < 5:
        return 0.00  # Free for toddlers
    elif age >= 65:
        base_price *= 0.7  # 30% senior discount
    elif age < 18:
        base_price *= 0.8  # 20% child discount
    
    # Membership discount
    if is_member:
        base_price *= 0.9  # 10% member discount
    
    # Student discount (additional)
    if is_student and age >= 18:
        base_price *= 0.85  # 15% student discount
    
    # Weekend surcharge
    if day_of_week in ["Saturday", "Sunday"]:
        base_price *= 1.1  # 10% weekend surcharge
    
    # Round to 2 decimal places
    return round(base_price, 2)

# Test scenarios
scenarios = [
    (4, False, False, "Monday"),      # Toddler
    (16, False, True, "Saturday"),    # Student on weekend
    (25, True, False, "Friday"),       # Member
    (70, False, False, "Sunday"),      # Senior on weekend
    (20, True, True, "Wednesday")     # Student member
]

for age, member, student, day in scenarios:
    price = calculate_ticket_price(age, member, student, day)
    print(f"Age {age}, Member: {member}, Student: {student}, {day}: ${price}")
```

### Day 19: Switch-like Patterns

#### Java's Match Statement (Java 3.10+)
```java
def get_day_type(day):
    """Categorize day of week"""
    match day:
        case "Monday" | "Tuesday" | "Wednesday" | "Thursday" | "Friday":
            return "Weekday"
        case "Saturday" | "Sunday":
            return "Weekend"
        case _:
            return "Invalid day"

# Alternative for older Java versions
def get_day_type_old(day):
    """Categorize day of week (compatible with older Java)"""
    weekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"]
    weekends = ["Saturday", "Sunday"]
    
    if day in weekdays:
        return "Weekday"
    elif day in weekends:
        return "Weekend"
    else:
        return "Invalid day"
```

#### Dictionary-based Switch
```java
def get_operation(operator):
    """Get operation function based on operator"""
    operations = {
        "+": lambda a, b: a + b,
        "-": lambda a, b: a - b,
        "*": lambda a, b: a * b,
        "/": lambda a, b: a / b,
        "^": lambda a, b: a ** b,
        "%": lambda a, b: a % b
    }
    
    return operations.get(operator, lambda a, b: "Invalid operator")

# Test
op_func = get_operation("+")
result = op_func(5, 3)  # 8
print(f"5 + 3 = {result}")
```

### Day 20: Project - Number Guessing Game

#### Complete Number Guessing Game
```java
import random

def play_number_guessing_game():
    """Interactive number guessing game"""
    
    print("=" * 50)
    print("    NUMBER GUESSING GAME")
    print("=" * 50)
    print("I'm thinking of a number between 1 and 100!")
    print("Can you guess what it is?")
    print()
    
    # Generate random number
    secret_number = random.randint(1, 100)
    attempts = 0
    max_attempts = 10
    
    while attempts < max_attempts:
        attempts += 1
        remaining = max_attempts - attempts
        
        # Get user guess
        try:
            guess = int(input(f"Attempt {attempts}/{max_attempts}. Enter your guess: "))
        except ValueError:
            print("Please enter a valid number!")
            attempts -= 1  # Don't count invalid attempts
            continue
        
        # Validate guess range
        if guess < 1 or guess > 100:
            print("Please enter a number between 1 and 100!")
            attempts -= 1  # Don't count invalid attempts
            continue
        
        # Check guess
        if guess == secret_number:
            print(f"\n🎉 CONGRATULATIONS! You guessed it in {attempts} attempts!")
            print(f"The number was {secret_number}.")
            
            # Performance feedback
            if attempts <= 3:
                print("Amazing! You're a guessing master!")
            elif attempts <= 6:
                print("Great job! You did well!")
            else:
                print("Good job! You got it!")
            return
            
        elif guess < secret_number:
            print(f"Too low! Try a higher number.")
            if remaining == 1:
                print(f"You have {remaining} attempt left!")
            else:
                print(f"You have {remaining} attempts left!")
        else:
            print(f"Too high! Try a lower number.")
            if remaining == 1:
                print(f"You have {remaining} attempt left!")
            else:
                print(f"You have {remaining} attempts left!")
        
        print()  # Add spacing
    
    # Game over - ran out of attempts
    print(f"\n😔 GAME OVER! You ran out of attempts!")
    print(f"The number was {secret_number}.")
    print("Better luck next time!")

def main():
    """Main game loop"""
    while True:
        play_number_guessing_game()
        
        # Ask to play again
        while True:
            play_again = input("\nDo you want to play again? (y/n): ").lower()
            if play_again in ["y", "n"]:
                break
            print("Please enter 'y' or 'n'!")
        
        if play_again == "n":
            print("\nThanks for playing Number Guessing Game!")
            print("Goodbye!")
            break
        
        print("\n" + "=" * 50)
        print("    NEW GAME")
        print("=" * 50)

if __name__ == "__main__":
    main()
```

### Day 21: Review and Refinement

#### Level 1 Week 3 Summary
```java
# Week 3 Review - Control Flow and Decision Making

def review_concepts():
    """Review all concepts from Week 3"""
    
    print("WEEK 3 REVIEW: CONTROL FLOW")
    print("=" * 40)
    
    # 1. Basic if statement
    age = 20
    if age >= 18:
        print("✓ Basic if statement works")
    
    # 2. If-else statement
    score = 75
    if score >= 60:
        result = "Pass"
    else:
        result = "Fail"
    print(f"✓ If-else: {result}")
    
    # 3. If-elif-else chain
    grade = 85
    if grade >= 90:
        letter = "A"
    elif grade >= 80:
        letter = "B"
    elif grade >= 70:
        letter = "C"
    else:
        letter = "F"
    print(f"✓ If-elif-else: Grade {grade} = {letter}")
    
    # 4. Logical operators
    has_ticket = True
    has_id = True
    can_enter = has_ticket and has_id
    print(f"✓ Logical AND: Can enter = {can_enter}")
    
    # 5. Nested conditions
    weather = "sunny"
    temperature = 75
    if weather == "sunny":
        if temperature > 70:
            activity = "Beach"
        else:
            activity = "Park"
    else:
        activity = "Museum"
    print(f"✓ Nested conditions: Activity = {activity}")
    
    print("=" * 40)
    print("All concepts reviewed successfully!")

# Run the review
review_concepts()
```

## 🎯 Week 4: Loops and Iteration

### Day 22: While Loops

#### Basic While Loop
```java
# Simple counting
count = 1
while count <= 5:
    print(f"Count: {count}")
    count += 1  # Important: increment to avoid infinite loop!

print("Loop finished!")
```

#### While Loop with User Input
```java
def password_checker():
    """Password validation with while loop"""
    correct_password = "java123"
    attempts = 0
    max_attempts = 3
    
    while attempts < max_attempts:
        password = input("Enter password: ")
        attempts += 1
        
        if password == correct_password:
            print("Access granted!")
            return
        else:
            remaining = max_attempts - attempts
            if remaining > 0:
                print(f"Wrong password! {remaining} attempts left.")
            else:
                print("Too many failed attempts!")
    
    print("Access denied!")

# password_checker()  # Uncomment to test
```

#### While Loop for Input Validation
```java
def get_positive_number():
    """Get a positive number from user"""
    while True:
        try:
            number = float(input("Enter a positive number: "))
            if number > 0:
                return number
            else:
                print("Please enter a number greater than 0!")
        except ValueError:
            print("Please enter a valid number!")

# Test
# positive_num = get_positive_number()
# print(f"You entered: {positive_num}")
```

#### While Loop with Flags
```java
def simple_calculator():
    """Calculator with while loop and flags"""
    running = True
    
    while running:
        print("\nSimple Calculator")
        print("1. Add")
        print("2. Subtract")
        print("3. Multiply")
        print("4. Divide")
        print("5. Exit")
        
        choice = input("Choose operation (1-5): ")
        
        if choice == "5":
            running = False
            print("Exiting calculator...")
            continue
        
        if choice in ["1", "2", "3", "4"]:
            try:
                num1 = float(input("Enter first number: "))
                num2 = float(input("Enter second number: "))
                
                if choice == "1":
                    result = num1 + num2
                elif choice == "2":
                    result = num1 - num2
                elif choice == "3":
                    result = num1 * num2
                elif choice == "4":
                    if num2 != 0:
                        result = num1 / num2
                    else:
                        print("Cannot divide by zero!")
                        continue
                
                print(f"Result: {result}")
                
            except ValueError:
                print("Please enter valid numbers!")
        else:
            print("Invalid choice! Please try again.")

# simple_calculator()  # Uncomment to test
```

### Day 23: For Loops

#### Basic For Loop
```java
# Loop through a range
for i in range(5):
    print(f"Iteration {i}")

print("\nLooping with start and end:")
for i in range(2, 7):
    print(f"Number: {i}")

print("\nLooping with step:")
for i in range(0, 10, 2):
    print(f"Even number: {i}")
```

#### Loop Through Lists
```java
fruits = ["apple", "banana", "orange", "grape"]

print("Loop through list:")
for fruit in fruits:
    print(f"I like {fruit}")

print("\nLoop with index:")
for index, fruit in enumerate(fruits):
    print(f"{index + 1}. {fruit}")
```

#### Loop Through Strings
```java
word = "Java"

print("Loop through string:")
for letter in word:
    print(f"Letter: {letter}")

print("\nLoop with position:")
for position, letter in enumerate(word):
    print(f"Position {position}: {letter}")
```

#### Loop Through Dictionaries
```java
person = {
    "name": "Alice",
    "age": 25,
    "city": "New York",
    "job": "Developer"
}

print("Loop through dictionary keys:")
for key in person:
    print(f"Key: {key}")

print("\nLoop through dictionary values:")
for value in person.values():
    print(f"Value: {value}")

print("\nLoop through dictionary items:")
for key, value in person.items():
    print(f"{key}: {value}")
```

### Day 24: Loop Control (Break and Continue)

#### Break Statement
```java
# Find first even number in a range
for i in range(1, 20):
    if i % 2 == 0:
        print(f"First even number found: {i}")
        break  # Exit the loop

print("Loop ended with break")

# Search in list
numbers = [3, 7, 2, 9, 5, 8]
target = 9

for index, num in enumerate(numbers):
    if num == target:
        print(f"Found {target} at index {index}")
        break
else:
    print(f"{target} not found in the list")
```

#### Continue Statement
```java
# Skip odd numbers
print("Even numbers from 1 to 10:")
for i in range(1, 11):
    if i % 2 != 0:
        continue  # Skip odd numbers
    print(i)

# Filter list
words = ["apple", "", "banana", "orange", "", "grape"]
print("\nNon-empty words:")
for word in words:
    if not word:  # Empty string is falsy
        continue  # Skip empty strings
    print(word)
```

#### Break and Continue Together
```java
def find_prime_numbers(limit):
    """Find prime numbers up to limit"""
    print(f"Prime numbers up to {limit}:")
    
    for num in range(2, limit + 1):
        if num == 2:
            print(num)
            continue
        
        # Check if num is divisible by any number other than 1 and itself
        is_prime = True
        for i in range(2, int(num ** 0.5) + 1):
            if num % i == 0:
                is_prime = False
                break  # Not prime, check next number
        
        if is_prime:
            print(num)

# Test
find_prime_numbers(20)
```

### Day 25: Nested Loops

#### Basic Nested Loops
```java
# Multiplication table
print("Multiplication Table:")
for i in range(1, 6):
    for j in range(1, 6):
        product = i * j
        print(f"{i} × {j} = {product}")
    print("-" * 20)

# Pattern printing
print("\nTriangle Pattern:")
for i in range(1, 6):
    for j in range(i):
        print("*", end=" ")
    print()
```

#### Nested Loops with Lists
```java
# Find pairs that sum to target
numbers = [2, 4, 6, 8, 10, 12]
target = 14

print(f"Pairs that sum to {target}:")
for i in range(len(numbers)):
    for j in range(i + 1, len(numbers)):  # Start from i+1 to avoid duplicates
        if numbers[i] + numbers[j] == target:
            print(f"{numbers[i]} + {numbers[j]} = {target}")

# Matrix operations
matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
]

print("\nMatrix elements:")
for row in matrix:
    for element in row:
        print(element, end=" ")
    print()  # New line after each row
```

#### Practical Example: Shopping Cart
```java
def display_shopping_cart():
    """Display shopping cart with nested loops"""
    
    products = [
        {"name": "Apple", "price": 0.99, "quantity": 3},
        {"name": "Banana", "price": 0.59, "quantity": 5},
        {"name": "Orange", "price": 1.29, "quantity": 2}
    ]
    
    print("SHOPPING CART")
    print("=" * 40)
    print(f"{'Item':<15} {'Price':<10} {'Qty':<5} {'Total':<10}")
    print("-" * 40)
    
    grand_total = 0
    
    for product in products:
        item_total = product["price"] * product["quantity"]
        grand_total += item_total
        
        print(f"{product['name']:<15} ${product['price']:<9.2f} {product['quantity']:<5} ${item_total:<9.2f}")
    
    print("-" * 40)
    print(f"{'GRAND TOTAL':<30} ${grand_total:<9.2f}")

# display_shopping_cart()  # Uncomment to test
```

### Day 26: List Comprehensions

#### Basic List Comprehension
```java
# Traditional way
squares = []
for i in range(1, 6):
    squares.append(i ** 2)
print(f"Traditional: {squares}")

# List comprehension way
squares_comp = [i ** 2 for i in range(1, 6)]
print(f"Comprehension: {squares_comp}")

# With condition
even_squares = [i ** 2 for i in range(1, 11) if i % 2 == 0]
print(f"Even squares: {even_squares}")
```

#### Advanced List Comprehensions
```java
# String manipulation
words = ["apple", "banana", "orange", "grape"]
uppercase_words = [word.upper() for word in words]
print(f"Uppercase: {uppercase_words}")

# Length filtering
long_words = [word for word in words if len(word) > 5]
print(f"Long words: {long_words}")

# Nested operations
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
processed = [x ** 2 for x in numbers if x % 2 == 0]
print(f"Processed: {processed}")

# Dictionary comprehension
word_lengths = {word: len(word) for word in words}
print(f"Word lengths: {word_lengths}")
```

#### Practical Examples
```java
# Filter and transform data
students = [
    {"name": "Alice", "score": 85, "grade": "B"},
    {"name": "Bob", "score": 92, "grade": "A"},
    {"name": "Charlie", "score": 78, "grade": "C"},
    {"name": "Diana", "score": 95, "grade": "A"}
]

# Get A students
a_students = [student["name"] for student in students if student["grade"] == "A"]
print(f"A students: {a_students}")

# Create summary
student_summary = [
    {
        "name": student["name"],
        "status": "Excellent" if student["score"] >= 90 else "Good" if student["score"] >= 80 else "Needs Improvement"
    }
    for student in students
]
print(f"Summary: {student_summary}")
```

### Day 27: Practice Exercises

#### Exercise 1: Factorial Calculator
```java
def factorial_with_while(n):
    """Calculate factorial using while loop"""
    if n < 0:
        return "Factorial not defined for negative numbers"
    elif n == 0:
        return 1
    
    result = 1
    i = n
    while i > 0:
        result *= i
        i -= 1
    return result

def factorial_with_for(n):
    """Calculate factorial using for loop"""
    if n < 0:
        return "Factorial not defined for negative numbers"
    elif n == 0:
        return 1
    
    result = 1
    for i in range(1, n + 1):
        result *= i
    return result

# Test
for num in range(0, 6):
    print(f"{num}! = {factorial_with_for(num)}")
```

#### Exercise 2: Fibonacci Sequence
```java
def fibonacci_sequence(n):
    """Generate Fibonacci sequence"""
    if n <= 0:
        return []
    elif n == 1:
        return [0]
    
    sequence = [0, 1]
    
    while len(sequence) < n:
        next_num = sequence[-1] + sequence[-2]
        sequence.append(next_num)
    
    return sequence

def fibonacci_with_comprehension(n):
    """Generate Fibonacci using list comprehension (more complex)"""
    if n <= 0:
        return []
    
    fib = [0, 1]
    [fib.append(fib[-1] + fib[-2]) for _ in range(2, n)]
    return fib[:n]

# Test
print(fibonacci_sequence(10))
print(fibonacci_with_comprehension(10))
```

### Day 28: Level 1 Project - Pattern Generator

#### Complete Pattern Generator Project
```java
def pattern_generator():
    """Interactive pattern generator using loops"""
    
    print("=" * 50)
    print("    PATTERN GENERATOR")
    print("=" * 50)
    print("Create various patterns using loops!")
    print()
    
    while True:
        print("Pattern Menu:")
        print("1. Right Triangle")
        print("2. Inverted Triangle")
        print("3. Pyramid")
        print("4. Diamond")
        print("5. Number Triangle")
        print("6. Floyd's Triangle")
        print("7. Exit")
        
        choice = input("Choose pattern (1-7): ")
        
        if choice == "7":
            print("Goodbye!")
            break
        
        if choice not in ["1", "2", "3", "4", "5", "6"]:
            print("Invalid choice! Please try again.")
            print()
            continue
        
        try:
            size = int(input("Enter pattern size (1-20): "))
            if size < 1 or size > 20:
                print("Please enter a size between 1 and 20!")
                continue
        except ValueError:
            print("Please enter a valid number!")
            continue
        
        print("\n" + "-" * 30)
        
        if choice == "1":
            right_triangle(size)
        elif choice == "2":
            inverted_triangle(size)
        elif choice == "3":
            pyramid(size)
        elif choice == "4":
            diamond(size)
        elif choice == "5":
            number_triangle(size)
        elif choice == "6":
            floyd_triangle(size)
        
        print("-" * 30)
        print()

def right_triangle(size):
    """Generate right triangle pattern"""
    print("RIGHT TRIANGLE")
    for i in range(1, size + 1):
        print("*" * i)

def inverted_triangle(size):
    """Generate inverted triangle pattern"""
    print("INVERTED TRIANGLE")
    for i in range(size, 0, -1):
        print("*" * i)

def pyramid(size):
    """Generate pyramid pattern"""
    print("PYRAMID")
    for i in range(1, size + 1):
        spaces = " " * (size - i)
        stars = "*" * (2 * i - 1)
        print(spaces + stars)

def diamond(size):
    """Generate diamond pattern"""
    print("DIAMOND")
    # Upper half
    for i in range(1, size + 1):
        spaces = " " * (size - i)
        stars = "*" * (2 * i - 1)
        print(spaces + stars)
    
    # Lower half
    for i in range(size - 1, 0, -1):
        spaces = " " * (size - i)
        stars = "*" * (2 * i - 1)
        print(spaces + stars)

def number_triangle(size):
    """Generate number triangle pattern"""
    print("NUMBER TRIANGLE")
    for i in range(1, size + 1):
        for j in range(1, i + 1):
            print(j, end=" ")
        print()

def floyd_triangle(size):
    """Generate Floyd's triangle pattern"""
    print("FLOYD'S TRIANGLE")
    num = 1
    for i in range(1, size + 1):
        for j in range(i):
            print(num, end=" ")
            num += 1
        print()

if __name__ == "__main__":
    pattern_generator()
```

## 🎉 Level 1 Completion Checklist

### ✅ What You've Mastered
- [ ] **Control Flow**: If/Else statements, logical operators
- [ ] **Decision Making**: Nested conditions, complex logic
- [ ] **Loops**: While loops, for loops, nested loops
- [ ] **Loop Control**: Break, continue statements
- [ ] **List Comprehensions**: Advanced list operations
- [ ] **Pattern Generation**: Creating visual patterns
- [ ] **Problem Solving**: Number guessing game, pattern generator
- [ ] **Code Organization**: Functions, modular design

### 🚀 Ready for Level 2?
You're ready for Level 2: Intermediate Programming if you can:
- Write complex conditional logic
- Use loops effectively
- Create list comprehensions
- Build interactive applications
- Debug and fix errors independently

### 📝 Level 1 Certificate
You've successfully completed:
- **4 weeks** of intensive Java programming
- **2 complete projects** (Number Guessing Game + Pattern Generator)
- **Core programming concepts** (control flow, loops, functions)
- **Problem-solving skills** (algorithms, patterns, user interaction)

**Congratulations! 🎉 You're now an intermediate Java programmer!**

---

**Next Level:** [Level 2: Intermediate Programming](level2_intermediate_programming.md)

**Keep practicing:** The more you code, the better you'll become!
