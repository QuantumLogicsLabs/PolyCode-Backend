# Control Flow in Ruby

## Overview

Control flow statements allow your program to make decisions and repeat actions. Ruby provides several ways to control the flow of execution, including conditional statements and loops.

## Conditional Statements

### 1. If Statement

The `if` statement executes code when a condition is true.

```ruby
age = 18

if age >= 18
  puts "You are an adult"
end

# One-line if
puts "Welcome!" if age >= 18
```

### 2. If-Else Statement

The `else` clause executes when the `if` condition is false.

```ruby
temperature = 25

if temperature > 30
  puts "It's hot outside"
else
  puts "It's comfortable outside"
end
```

### 3. If-Elsif-Else Statement

The `elsif` clause allows multiple conditions to be checked.

```ruby
grade = 85

if grade >= 90
  puts "A"
elsif grade >= 80
  puts "B"
elsif grade >= 70
  puts "C"
elsif grade >= 60
  puts "D"
else
  puts "F"
end
```

### 4. Unless Statement

The `unless` statement is the opposite of `if` - it executes when the condition is false.

```ruby
logged_in = false

unless logged_in
  puts "Please log in to continue"
end

# One-line unless
puts "Access denied" unless logged_in
```

### 5. Case Statement

The `case` statement is useful for comparing a value against multiple possibilities.

```ruby
day = "Monday"

case day
when "Monday"
  puts "Start of the work week"
when "Friday"
  puts "TGIF!"
when "Saturday", "Sunday"
  puts "Weekend!"
else
  puts "Midweek"
end

# Case with ranges
score = 85

case score
when 90..100
  puts "Excellent"
when 80..89
  puts "Good"
when 70..79
  puts "Average"
else
  puts "Needs improvement"
end

# Case with no value (like if-elsif)
age = 25

case
when age < 13
  puts "Child"
when age < 18
  puts "Teenager"
when age < 65
  puts "Adult"
else
  puts "Senior"
end
```

### 6. Ternary Operator

The ternary operator is a compact way to write simple if-else statements.

```ruby
age = 20
status = age >= 18 ? "adult" : "minor"
puts status  # => "adult"

# Equivalent to:
if age >= 18
  status = "adult"
else
  status = "minor"
end
```

## Loops

### 1. While Loop

The `while` loop continues as long as the condition is true.

```ruby
count = 0

while count < 5
  puts "Count: #{count}"
  count += 1
end

# Output:
# Count: 0
# Count: 1
# Count: 2
# Count: 3
# Count: 4
```

### 2. Until Loop

The `until` loop continues until the condition becomes true (opposite of while).

```ruby
count = 0

until count >= 5
  puts "Count: #{count}"
  count += 1
end
```

### 3. For Loop

The `for` loop iterates over a collection.

```ruby
# Array iteration
fruits = ["apple", "banana", "cherry"]

for fruit in fruits
  puts "I like #{fruit}"
end

# Range iteration
for i in 1..5
  puts "Number: #{i}"
end

# Hash iteration
person = { name: "John", age: 30, city: "NYC" }

for key, value in person
  puts "#{key}: #{value}"
end
```

### 4. Loop Methods (Preferred in Ruby)

Ruby provides more idiomatic ways to iterate over collections.

#### Each Method
```ruby
# Array each
numbers = [1, 2, 3, 4, 5]

numbers.each do |number|
  puts "Number: #{number}"
end

# Hash each
person = { name: "Alice", age: 25 }

person.each do |key, value|
  puts "#{key}: #{value}"
end

# With index
fruits = ["apple", "banana", "cherry"]

fruits.each_with_index do |fruit, index|
  puts "#{index}: #{fruit}"
end
```

#### Times Method
```ruby
5.times do |i|
  puts "Iteration #{i}"
end

# One-line version
3.times { puts "Hello!" }
```

#### Upto and Downto Methods
```ruby
1.upto(5) do |i|
  puts "Counting up: #{i}"
end

5.downto(1) do |i|
  puts "Counting down: #{i}"
end
```

#### Step Method
```ruby
0.step(10, 2) do |i|
  puts "Even number: #{i}"
end
```

## Loop Control

### 1. Break

The `break` statement exits the loop immediately.

```ruby
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

numbers.each do |number|
  break if number > 5
  puts "Number: #{number}"
end

# Output:
# Number: 1
# Number: 2
# Number: 3
# Number: 4
# Number: 5
```

### 2. Next

The `next` statement skips to the next iteration.

```ruby
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

numbers.each do |number|
  next if number.even?
  puts "Odd number: #{number}"
end

# Output:
# Odd number: 1
# Odd number: 3
# Odd number: 5
# Odd number: 7
# Odd number: 9
```

### 3. Redo

The `redo` statement restarts the current iteration.

```ruby
count = 0

loop do
  count += 1
  puts "Count: #{count}"
  
  if count < 3
    redo
  else
    break
  end
end

# Output:
# Count: 1
# Count: 2
# Count: 3
```

## Exception Handling

### 1. Begin-Rescue Block

Handle exceptions that might occur during execution.

```ruby
begin
  # Code that might raise an exception
  result = 10 / 0
rescue ZeroDivisionError
  puts "Cannot divide by zero!"
rescue StandardError => e
  puts "An error occurred: #{e.message}"
end
```

### 2. Ensure Block

The `ensure` block always executes, whether an exception occurred or not.

```ruby
file = nil
begin
  file = File.open("example.txt", "r")
  content = file.read
  puts "File content: #{content}"
rescue Errno::ENOENT
  puts "File not found"
ensure
  file.close if file
  puts "File closed"
end
```

### 3. Else Block

The `else` block executes when no exception occurs.

```ruby
begin
  result = 10 / 2
rescue ZeroDivisionError
  puts "Division by zero"
else
  puts "Result: #{result}"
ensure
  puts "Operation complete"
end
```

## Conditional Modifiers

Ruby allows you to place conditions at the end of statements for more concise code.

```ruby
# If modifier
puts "Access granted" if user_authenticated?

# Unless modifier
puts "Please log in" unless user_authenticated?

# Ternary operator
message = age >= 18 ? "Welcome" : "Access denied"
```

## Practical Examples

### Example 1: Number Guessing Game

```ruby
def number_guessing_game
  secret_number = rand(1..100)
  attempts = 0
  max_attempts = 7

  puts "Guess a number between 1 and 100"

  loop do
    print "Enter your guess: "
    guess = gets.chomp.to_i
    attempts += 1

    if guess == secret_number
      puts "Congratulations! You guessed it in #{attempts} attempts!"
      break
    elsif guess < secret_number
      puts "Too low!"
    else
      puts "Too high!"
    end

    if attempts >= max_attempts
      puts "Game over! The number was #{secret_number}"
      break
    end
  end
end

# number_guessing_game
```

### Example 2: Menu System

```ruby
def menu_system
  loop do
    puts "\n=== Main Menu ==="
    puts "1. Add item"
    puts "2. View items"
    puts "3. Delete item"
    puts "4. Exit"
    print "Choose an option: "

    choice = gets.chomp

    case choice
    when "1"
      puts "Adding item..."
    when "2"
      puts "Viewing items..."
    when "3"
      puts "Deleting item..."
    when "4"
      puts "Goodbye!"
      break
    else
      puts "Invalid option. Please try again."
    end
  end
end

# menu_system
```

### Example 3: Data Validation

```ruby
def validate_user_input
  loop do
    print "Enter your age (18-120): "
    age = gets.chomp

    # Validate input is a number
    unless age.match?(/^\d+$/)
      puts "Please enter a valid number."
      next
    end

    age = age.to_i

    # Validate age range
    case age
    when 18..120
      puts "Valid age: #{age}"
      break
    when 0..17
      puts "You must be at least 18 years old."
    else
      puts "Please enter a realistic age."
    end
  end
end

# validate_user_input
```

## Best Practices

### 1. Use Idiomatic Ruby

```ruby
# Avoid this (C-style loops)
for i in 0..array.length-1
  puts array[i]
end

# Prefer this
array.each { |item| puts item }
```

### 2. Keep Conditions Simple

```ruby
# Avoid this
if user && user.active? && user.email && user.email.include?("@")
  # code
end

# Better
if user&.active? && user.email&.include?("@")
  # code
end
```

### 3. Use Guard Clauses

```ruby
# Avoid this
def process_data(data)
  if data
    if data.valid?
      # process data
    else
      puts "Invalid data"
    end
  else
    puts "No data provided"
  end
end

# Better
def process_data(data)
  return "No data provided" unless data
  return "Invalid data" unless data.valid?
  
  # process data
end
```

### 4. Handle Specific Exceptions

```ruby
# Avoid this
begin
  # risky code
rescue
  # handle all exceptions
end

# Better
begin
  # risky code
rescue SpecificError
  # handle specific error
rescue AnotherError
  # handle another error
end
```

## Performance Considerations

### 1. Loop Performance

```ruby
# For large collections, each is generally faster than for
large_array = (1..1_000_000).to_a

# Faster
large_array.each { |item| item * 2 }

# Slower
for item in large_array
  item * 2
end
```

### 2. Early Returns

```ruby
# Use early returns to avoid deep nesting
def process_user(user)
  return nil unless user
  return nil unless user.active?
  return nil unless user.email
  
  user.email.downcase
end
```

## Practice Exercises

### Exercise 1: Grade Calculator
Write a program that:
1. Takes a numerical grade as input
2. Uses a case statement to determine letter grade
3. Handles invalid input gracefully

### Exercise 2: Array Processing
Create a method that:
1. Takes an array of numbers
2. Uses each_with_index to process elements
3. Returns a new array with transformed values
4. Uses next to skip certain values

### Exercise 3: Menu System
Build a simple menu system that:
1. Displays options
2. Handles user input with case statement
3. Validates input
4. Loops until user chooses to exit

### Exercise 4: Exception Handling
Write code that:
1. Demonstrates different exception types
2. Uses begin-rescue-ensure blocks
3. Provides meaningful error messages

---

**Ready to learn about methods and functions in Ruby? Let's continue! 🔧**
