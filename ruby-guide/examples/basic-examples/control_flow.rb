# Control Flow Examples
# Demonstrating conditional statements and loops in Ruby

puts "=== CONDITIONAL STATEMENTS ==="

# If statement
age = 18
puts "\n--- If Statement ---"
if age >= 18
  puts "You are an adult"
end

# If-else statement
temperature = 25
puts "\n--- If-Else Statement ---"
if temperature > 30
  puts "It's hot outside"
else
  puts "It's comfortable outside"
end

# If-elsif-else statement
grade = 85
puts "\n--- If-Elsif-Else Statement ---"
if grade >= 90
  puts "Grade: A"
elsif grade >= 80
  puts "Grade: B"
elsif grade >= 70
  puts "Grade: C"
elsif grade >= 60
  puts "Grade: D"
else
  puts "Grade: F"
end

# Unless statement
logged_in = false
puts "\n--- Unless Statement ---"
unless logged_in
  puts "Please log in to continue"
end

# Case statement
day = "Monday"
puts "\n--- Case Statement ---"
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
puts "\n--- Case with Ranges ---"
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

# Ternary operator
puts "\n--- Ternary Operator ---"
status = age >= 18 ? "adult" : "minor"
puts "Status: #{status}"

puts "\n=== LOOPS ==="

# While loop
puts "\n--- While Loop ---"
count = 0
while count < 5
  puts "Count: #{count}"
  count += 1
end

# Until loop
puts "\n--- Until Loop ---"
count = 0
until count >= 5
  puts "Count: #{count}"
  count += 1
end

# For loop with array
puts "\n--- For Loop with Array ---"
fruits = ["apple", "banana", "cherry"]
for fruit in fruits
  puts "Fruit: #{fruit}"
end

# For loop with range
puts "\n--- For Loop with Range ---"
for i in 1..5
  puts "Number: #{i}"
end

# Each method (preferred)
puts "\n--- Each Method ---"
numbers = [1, 2, 3, 4, 5]
numbers.each do |number|
  puts "Number: #{number}"
end

# Each with index
puts "\n--- Each with Index ---"
fruits.each_with_index do |fruit, index|
  puts "#{index}: #{fruit}"
end

# Times method
puts "\n--- Times Method ---"
3.times do |i|
  puts "Iteration #{i}"
end

# Upto method
puts "\n--- Upto Method ---"
1.upto(5) do |i|
  puts "Counting up: #{i}"
end

# Step method
puts "\n--- Step Method ---"
0.step(10, 2) do |i|
  puts "Even number: #{i}"
end

puts "\n=== LOOP CONTROL ==="

# Break example
puts "\n--- Break Example ---"
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
numbers.each do |number|
  break if number > 5
  puts "Number: #{number}"
end

# Next example
puts "\n--- Next Example ---"
numbers.each do |number|
  next if number.even?
  puts "Odd number: #{number}"
end

puts "\n=== EXCEPTION HANDLING ==="

# Basic exception handling
puts "\n--- Basic Exception Handling ---"
begin
  result = 10 / 0
rescue ZeroDivisionError
  puts "Cannot divide by zero!"
end

# Multiple exception types
puts "\n--- Multiple Exception Types ---"
begin
  # This would cause an error
  undefined_method
rescue NoMethodError
  puts "Method doesn't exist"
rescue StandardError => e
  puts "Standard error: #{e.message}"
end

# Ensure block
puts "\n--- Ensure Block ---"
begin
  puts "Doing something risky"
  # Simulate an error
  raise "Something went wrong"
rescue StandardError => e
  puts "Error caught: #{e.message}"
ensure
  puts "This always runs"
end

puts "\n=== PRACTICAL EXAMPLES ==="

# Number validation
puts "\n--- Number Validation ---"
def validate_number(input)
  case input
  when /^\d+$/
    num = input.to_i
    case num
    when 1..10
      "Valid small number: #{num}"
    when 11..100
      "Valid medium number: #{num}"
    else
      "Number out of range"
    end
  else
    "Not a valid number"
  end
end

puts validate_number("5")
puts validate_number("50")
puts validate_number("150")
puts validate_number("abc")

# Simple calculator
puts "\n--- Simple Calculator ---"
def simple_calculator(a, b, operation)
  case operation
  when "+", "add"
    a + b
  when "-", "subtract"
    a - b
  when "*", "multiply"
    a * b
  when "/", "divide"
    begin
      a / b.to_f
    rescue ZeroDivisionError
      "Cannot divide by zero"
    end
  else
    "Unknown operation"
  end
end

puts simple_calculator(10, 5, "+")
puts simple_calculator(10, 5, "-")
puts simple_calculator(10, 5, "*")
puts simple_calculator(10, 5, "/")
puts simple_calculator(10, 0, "/")

# Array filtering with conditions
puts "\n--- Array Filtering ---"
def process_numbers(numbers)
  result = []
  numbers.each do |num|
    next if num.negative?
    
    if num.even?
      result << num * 2
    else
      result << num * 3
    end
    
    break if result.size >= 5
  end
  result
end

test_numbers = [-2, -1, 1, 2, 3, 4, 5, 6, 7, 8]
puts "Input: #{test_numbers}"
puts "Output: #{process_numbers(test_numbers)}"

# Menu system simulation
puts "\n--- Menu System Simulation ---"
def simulate_menu_choice(choice)
  case choice
  when "1", "add"
    "Adding new item..."
  when "2", "view"
    "Viewing items..."
  when "3", "delete"
    "Deleting item..."
  when "4", "exit", "quit"
    "Goodbye!"
  else
    "Invalid option. Please try again."
  end
end

puts simulate_menu_choice("1")
puts simulate_menu_choice("view")
puts simulate_menu_choice("invalid")
puts simulate_menu_choice("4")
