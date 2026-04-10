# Ruby Conditionals and Control Flow

## Conditional Statements

### If Statement
```ruby
# Basic if statement
age = 25

if age >= 18
  puts "You are an adult"
end

# If-else statement
if age >= 18
  puts "You are an adult"
else
  puts "You are a minor"
end

# If-elsif-else statement
grade = 85

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

# One-line if statement
puts "Welcome" if user_signed_in?

# One-line if-else statement (ternary operator)
message = age >= 18 ? "Adult" : "Minor"
puts message

# Nested if statements
def can_drive?(age, has_license)
  if age >= 18
    if has_license
      true
    else
      false
    end
  else
    false
  end
end
```

### Unless Statement
```ruby
# Unless is the opposite of if
logged_in = false

unless logged_in
  puts "Please log in"
end

# Unless with else
unless logged_in
  puts "Please log in"
else
  puts "Welcome back!"
end

# One-line unless
puts "Access denied" unless admin_user?

# Using unless as modifier
def process_data(data)
  return nil unless data
  # Process data
end
```

### Case Statement
```ruby
# Basic case statement
day = "Monday"

case day
when "Monday", "Tuesday", "Wednesday", "Thursday", "Friday"
  puts "Weekday"
when "Saturday", "Sunday"
  puts "Weekend"
else
  puts "Unknown day"
end

# Case with ranges
score = 85

case score
when 90..100
  puts "A"
when 80..89
  puts "B"
when 70..79
  puts "C"
when 60..69
  puts "D"
else
  puts "F"
end

# Case with regex
email = "user@example.com"

case email
when /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
  puts "Valid email"
else
  puts "Invalid email"
end

# Case with classes
value = "hello"

case value
when String
  puts "It's a string"
when Integer
  puts "It's an integer"
when Array
  puts "It's an array"
else
  puts "Unknown type"
end

# Case with no value (when expressions)
status = "active"

case
when status == "active" && user_signed_in?
  puts "User is active and signed in"
when status == "inactive"
  puts "User is inactive"
when user_signed_in?
  puts "User is signed in"
else
  puts "User status unknown"
end
```

## Logical Operators

### Basic Logical Operators
```ruby
# AND operator (&&)
age = 25
has_license = true

can_drive = age >= 18 && has_license
puts "Can drive: #{can_drive}"

# OR operator (||)
is_weekend = true
is_holiday = false

can_relax = is_weekend || is_holiday
puts "Can relax: #{can_relax}"

# NOT operator (!)
is_admin = false

is_regular_user = !is_admin
puts "Is regular user: #{is_regular_user}"

# Combined logical expressions
username = "admin"
password = "secret"
is_active = true

can_login = username && password && is_active
puts "Can login: #{can_login}"
```

### Short-Circuit Evaluation
```ruby
# AND short-circuit
def expensive_check
  puts "Expensive check called"
  true
end

# Second condition not evaluated if first is false
result = false && expensive_check
puts "Result: #{result}"  # "Result: false" (no "Expensive check called")

# OR short-circuit
# Second condition not evaluated if first is true
result = true || expensive_check
puts "Result: #{result}"  # "Result: true" (no "Expensive check called")

# Practical example
def user_can_access?(user)
  # Check if user exists before checking permissions
  user && user.permissions.include?(:read)
end

def safe_divide(a, b)
  # Check for zero before dividing
  b != 0 && a / b
end
```

### Operator Precedence
```ruby
# Operator precedence (highest to lowest):
# 1. !, ~
# 2. **
# 3. *, /, %
# 4. +, -
# 5. <<, >>
# 6. &
# 7. |, ^
# 8. <=, <, >, >=
# 9. ==, ===, !=, =~, !~
# 10. &&
# 11. ||
# 12. .., ...
# 13. ?:
# 14. =, +=, -=, etc.

# Use parentheses for clarity
result = (a > b) && (c < d) || (e == f)
# Equivalent to: ((a > b) && (c < d)) || (e == f)

# Complex logical expression
def is_eligible_for_discount?(customer, order)
  (customer.member? && order.total > 100) ||
  (customer.vip? && order.total > 50) ||
  (order.contains_promotional_items? && customer.loyalty_points > 1000)
end
```

## Comparison Operators

### Basic Comparisons
```ruby
# Equality operators
5 == 5      # true
5 == 5.0    # true (type coercion)
"5" == 5    # false
5.eql?(5.0) # false (no type coercion)
5.equal?(5)  # true (same object id)

# Inequality operators
5 != 3      # true
"hello" != "world"  # true

# Comparison operators
5 > 3       # true
5 >= 5      # true
5 < 10      # true
5 <= 5      # true

# Spaceship operator (combined comparison)
5 <=> 3     # 1 (greater)
5 <=> 5     # 0 (equal)
3 <=> 5     # -1 (less)

# Useful for sorting
numbers = [5, 2, 8, 1, 9]
sorted_numbers = numbers.sort { |a, b| a <=> b }
puts sorted_numbers.inspect  # [1, 2, 5, 8, 9]
```

### String Comparisons
```ruby
# String comparison
"apple" == "Apple"     # false (case-sensitive)
"apple".casecmp("Apple")  # 0 (case-insensitive)

# String comparison with spaceship
"apple" <=> "banana"   # -1
"banana" <=> "apple"   # 1
"apple" <=> "apple"    # 0

# Natural sorting
files = ["file1.txt", "file10.txt", "file2.txt"]
sorted_files = files.sort { |a, b| a.to_i <=> b.to_i }
puts sorted_files.inspect  # ["file1.txt", "file2.txt", "file10.txt"]
```

### Range Comparisons
```ruby
# Include operator
(1..10).include?(5)    # true
(1..10).include?(0)    # false
(1..10).include?(10)   # true
(1...10).include?(10)  # false

# Cover operator
(1..10).cover?(5)      # true
(1..10).cover?(0)      # false
(1..10).cover?(10)     # true
(1...10).cover?(10)    # false

# Member operator
[1, 2, 3, 4, 5].include?(3)  # true
[1, 2, 3, 4, 5].member?(6)   # false

# Hash key checking
{a: 1, b: 2}.key?(:a)    # true
{a: 1, b: 2}.key?(:c)    # false
{a: 1, b: 2}.value?(1)   # true
{a: 1, b: 2}.value?(3)   # false
```

## Loops and Iteration

### While Loop
```ruby
# Basic while loop
count = 0
while count < 5
  puts "Count: #{count}"
  count += 1
end

# While loop with condition
numbers = [1, 2, 3, 4, 5]
index = 0

while index < numbers.length
  puts "Number: #{numbers[index]}"
  index += 1
end

# While as modifier
count = 0
count += 1 while count < 5
puts "Final count: #{count}"

# Until loop (opposite of while)
count = 0
until count >= 5
  puts "Count: #{count}"
  count += 1
end

# Until as modifier
count = 0
count += 1 until count >= 5
puts "Final count: #{count}"
```

### For Loop
```ruby
# Basic for loop with range
for i in 1..5
  puts "Number: #{i}"
end

# For loop with array
fruits = ["apple", "banana", "cherry"]
for fruit in fruits
  puts "Fruit: #{fruit}"
end

# For loop with hash
person = { name: "John", age: 30, city: "NYC" }
for key, value in person
  puts "#{key}: #{value}"
end

# For loop with each (more idiomatic Ruby)
(1..5).each do |i|
  puts "Number: #{i}"
end

fruits.each do |fruit|
  puts "Fruit: #{fruit}"
end

person.each do |key, value|
  puts "#{key}: #{value}"
end
```

### Loop Control
```ruby
# Break statement
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

for number in numbers
  break if number > 5
  puts "Number: #{number}"
end

# Continue (next) statement
for number in numbers
  next if number.even?
  puts "Odd number: #{number}"
end

# Redo statement
count = 0
while count < 5
  count += 1
  redo if count == 3  # Restart the loop when count is 3
  puts "Count: #{count}"
end

# Retry statement
attempts = 0
begin
  # Some operation that might fail
  raise "Failed" if attempts < 3
  puts "Success!"
rescue => e
  attempts += 1
  retry if attempts < 3
  puts "Failed after #{attempts} attempts"
end
```

## Iterator Methods

### Each Iterator
```ruby
# Array each
numbers = [1, 2, 3, 4, 5]
numbers.each { |number| puts number * 2 }

# Hash each
person = { name: "John", age: 30 }
person.each { |key, value| puts "#{key}: #{value}" }

# Each with index
fruits = ["apple", "banana", "cherry"]
fruits.each_with_index { |fruit, index| puts "#{index}: #{fruit}" }

# Each with object (inject)
numbers = [1, 2, 3, 4, 5]
sum = numbers.inject(0) { |total, number| total + number }
puts "Sum: #{sum}"
```

### Map and Collect
```ruby
# Map (alias for collect)
numbers = [1, 2, 3, 4, 5]
doubled = numbers.map { |number| number * 2 }
puts doubled.inspect  # [2, 4, 6, 8, 10]

# Map with index
words = ["hello", "world", "ruby"]
capitalized = words.map.with_index { |word, index| "#{index}: #{word.capitalize}" }
puts capitalized.inspect  # ["0: Hello", "1: World", "2: Ruby"]

# Map on hash
person = { name: "John", age: 30 }
transformed = person.map { |key, value| [key.to_s.upcase, value * 2] }.to_h
puts transformed.inspect  # {"NAME"=>"JohnJohn", "AGE"=>60}
```

### Select and Reject
```ruby
# Select (alias for find_all)
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
evens = numbers.select { |number| number.even? }
puts evens.inspect  # [2, 4, 6, 8, 10]

# Reject (opposite of select)
odds = numbers.reject { |number| number.even? }
puts odds.inspect  # [1, 3, 5, 7, 9]

# Select on hash
person = { name: "John", age: 30, active: true }
active_attributes = person.select { |key, value| value.is_a?(TrueClass) }
puts active_attributes.inspect  # {:active=>true}
```

### Find and Detect
```ruby
# Find (alias for detect)
numbers = [1, 2, 3, 4, 5]
first_even = numbers.find { |number| number.even? }
puts "First even: #{first_even}"  # 2

# Find all (alias for select)
all_evens = numbers.find_all { |number| number.even? }
puts "All evens: #{all_evens.inspect}"  # [2, 4]

# Any? (alias for any)
numbers = [1, 2, 3, 4, 5]
has_even = numbers.any? { |number| number.even? }
puts "Has even: #{has_even}"  # true

# All? (alias for all)
all_positive = numbers.all? { |number| number > 0 }
puts "All positive: #{all_positive}"  # true

# None?
all_positive = numbers.none? { |number| number < 0 }
puts "None negative: #{all_positive}"  # true
```

### Reduce and Inject
```ruby
# Reduce (alias for inject)
numbers = [1, 2, 3, 4, 5]
sum = numbers.reduce(0) { |total, number| total + number }
puts "Sum: #{sum}"  # 15

# Reduce without initial value
product = numbers.reduce { |total, number| total * number }
puts "Product: #{product}"  # 120

# Reduce to hash
words = ["apple", "banana", "cherry"]
word_lengths = words.reduce({}) do |hash, word|
  hash[word] = word.length
  hash
end
puts word_lengths.inspect  # {"apple"=>5, "banana"=>6, "cherry"=>6}

# Reduce with symbol method
numbers = [1, 2, 3, 4, 5]
sum = numbers.reduce(:+)
product = numbers.reduce(:*)
puts "Sum: #{sum}, Product: #{product}"  # Sum: 15, Product: 120
```

## Exception Handling

### Begin-Rescue-End
```ruby
# Basic exception handling
begin
  result = 10 / 0
rescue ZeroDivisionError
  puts "Cannot divide by zero"
end

# Multiple exception types
begin
  # Some operation that might fail
  file = File.open("nonexistent.txt", "r")
rescue Errno::ENOENT
  puts "File not found"
rescue IOError
  puts "IO error occurred"
rescue => e  # Catch all exceptions
  puts "Error: #{e.message}"
end

# Ensure block (always executed)
begin
  file = File.open("example.txt", "w")
  file.write("Hello, World!")
rescue => e
  puts "Error: #{e.message}"
ensure
  file.close if file
  puts "File closed"
end

# Else block (executed if no exception)
begin
  result = 10 / 2
rescue ZeroDivisionError
  puts "Division error"
else
  puts "Division successful: #{result}"
ensure
  puts "Operation completed"
end
```

### Retry and Raise
```ruby
# Retry on failure
attempts = 0
max_attempts = 3

begin
  # Simulate failing operation
  raise "Operation failed" if attempts < 2
  puts "Operation succeeded"
rescue => e
  attempts += 1
  if attempts < max_attempts
    puts "Attempt #{attempts} failed, retrying..."
    sleep 1
    retry
  else
    puts "Max attempts reached"
    raise
  end
end

# Raise custom exceptions
class CustomError < StandardError; end

def risky_operation
  raise CustomError, "Something went wrong"
end

begin
  risky_operation
rescue CustomError => e
  puts "Custom error caught: #{e.message}"
end
```

## Best Practices

### Conditional Best Practices
```ruby
# Good: Use guard clauses
def process_user(user)
  return nil unless user
  return nil unless user.active?
  return nil unless user.email_verified?
  
  # Process user
end

# Good: Use case for multiple conditions
def get_grade(score)
  case score
  when 90..100 then "A"
  when 80..89 then "B"
  when 70..79 then "C"
  when 60..69 then "D"
  else "F"
  end
end

# Good: Use early returns
def calculate_discount(price, customer)
  return 0 unless customer
  return price * 0.1 if customer.vip?
  return price * 0.05 if customer.member?
  
  0
end

# Bad: Deep nesting
def process_data(data)
  if data
    if data.valid?
      if data.processable?
        # Process data
        if data.success?
          # Handle success
        else
          # Handle failure
        end
      else
        # Handle not processable
      end
    else
      # Handle invalid
    end
  else
    # Handle nil
  end
end

# Good: Flatten with guard clauses
def process_data(data)
  return unless data
  return unless data.valid?
  return unless data.processable?
  
  # Process data
  if data.success?
    # Handle success
  else
    # Handle failure
  end
end
```

### Loop Best Practices
```ruby
# Good: Use each instead of for
numbers = [1, 2, 3, 4, 5]

# Good
numbers.each { |number| puts number }

# Avoid
for number in numbers
  puts number
end

# Good: Use appropriate iterator
# For transformation
doubled = numbers.map { |n| n * 2 }

# For filtering
evens = numbers.select { |n| n.even? }

# For finding
first_even = numbers.find { |n| n.even? }

# For checking condition
has_even = numbers.any? { |n| n.even? }

# Good: Use enumerable methods for complex operations
words = ["hello", "world", "ruby"]
word_counts = words.each_with_object({}) do |word, hash|
  hash[word] = word.length
end

# Good: Use break/next appropriately
numbers.each do |number|
  next if number.even?
  break if number > 7
  puts number
end
```

### Exception Handling Best Practices
```ruby
# Good: Be specific about exceptions
begin
  # Operation
rescue ZeroDivisionError
  # Handle division by zero
rescue IOError
  # Handle IO errors
end

# Good: Use ensure for cleanup
def process_file(filename)
  file = nil
  begin
    file = File.open(filename, 'r')
    # Process file
  rescue => e
    puts "Error: #{e.message}"
  ensure
    file&.close
  end
end

# Better: Use block form for automatic cleanup
def process_file(filename)
  File.open(filename, 'r') do |file|
    # Process file
  end  # File automatically closed
end

# Good: Create custom exceptions for domain errors
class ValidationError < StandardError; end
class AuthenticationError < StandardError; end

def validate_user(user)
  raise ValidationError, "Invalid user data" unless user.valid?
  raise AuthenticationError, "User not authenticated" unless user.authenticated?
end
```

## Common Pitfalls

### Conditional Pitfalls
```ruby
# Pitfall: Assignment in condition
if x = get_value()  # This assigns x and checks if x is truthy
  puts x
end

# Better: Separate assignment and condition
x = get_value()
if x
  puts x
end

# Pitfall: Confusing == and ===
case value
when String
  puts "It's a string"  # Uses === for case matching
end

# Regular comparison
if value.is_a?(String)
  puts "It's a string"
end

# Pitfall: Not handling nil properly
def process_name(name)
  if name.empty?  # Error if name is nil
    puts "Name is empty"
  end
end

# Better: Check for nil first
def process_name(name)
  return unless name
  if name.empty?
    puts "Name is empty"
  end
end

# Pitfall: Complex boolean expressions
def can_access?(user, resource, permissions)
  if user && user.active? && resource && resource.available? && permissions && permissions.include?(:read)
    true
  else
    false
  end
end

# Better: Break down complex conditions
def can_access?(user, resource, permissions)
  return false unless user&.active?
  return false unless resource&.available?
  return false unless permissions&.include?(:read)
  
  true
end
```

### Loop Pitfalls
```ruby
# Pitfall: Modifying collection while iterating
numbers = [1, 2, 3, 4, 5]
numbers.each do |number|
  numbers.delete(number) if number.even?  # Bad: modifies while iterating
end

# Better: Create new collection or use reject
numbers = [1, 2, 3, 4, 5]
evens = numbers.reject { |number| number.even? }
# or
numbers.delete_if { |number| number.even? }

# Pitfall: Infinite loop
count = 0
while count < 10
  puts count
  # Forgot to increment count
end

# Better: Ensure loop termination
count = 0
while count < 10
  puts count
  count += 1
end

# Pitfall: Using each when you need index
words = ["apple", "banana", "cherry"]
words.each do |word|
  puts "#{word} is at position"  # Can't get position easily
end

# Better: Use each_with_index
words.each_with_index do |word, index|
  puts "#{word} is at position #{index}"
end
```

### Exception Handling Pitfalls
```ruby
# Pitfall: Catching too broadly
begin
  # Operation
rescue => e  # Catches everything
  puts "Error occurred"
end

# Better: Catch specific exceptions
begin
  # Operation
rescue SpecificError => e
  puts "Specific error: #{e.message}"
rescue => e
  puts "Other error: #{e.message}"
end

# Pitfall: Swallowing exceptions
begin
  risky_operation
rescue => e
  # Do nothing - error is lost
end

# Better: Handle or re-raise
begin
  risky_operation
rescue => e
  logger.error "Operation failed: #{e.message}"
  raise  # Re-raise if you can't handle it
end

# Pitfall: Not using ensure for cleanup
def process_file(filename)
  file = File.open(filename, 'r')
  # Process file
  file.close  # Might not be reached if exception occurs
end

# Better: Use ensure
def process_file(filename)
  file = File.open(filename, 'r')
  begin
    # Process file
  ensure
    file.close
  end
end
```

## Summary

Ruby conditionals and control flow provide:

**Conditional Statements:**
- if, elsif, else statements
- unless statements (opposite of if)
- case statements for multiple conditions
- Ternary operator for simple conditions
- Guard clauses for early returns

**Logical Operators:**
- && (AND), || (OR), ! (NOT)
- Short-circuit evaluation
- Operator precedence
- Combined logical expressions

**Comparison Operators:**
- ==, !=, ===, !==
- <, <=, >, >=
- <=> (spaceship operator)
- String comparisons
- Range and collection membership

**Loops and Iteration:**
- while and until loops
- for loops (less common in Ruby)
- Loop control (break, next, redo, retry)
- Iterator methods (each, map, select, etc.)

**Exception Handling:**
- begin-rescue-end blocks
- Multiple exception handling
- ensure blocks for cleanup
- else blocks for success cases
- Custom exceptions

**Best Practices:**
- Use guard clauses to reduce nesting
- Prefer each over for loops
- Be specific with exception handling
- Use appropriate iterator methods
- Handle nil values properly

**Common Pitfalls:**
- Assignment in conditions
- Modifying collections while iterating
- Catching exceptions too broadly
- Complex boolean expressions
- Not handling nil properly

Ruby's expressive control flow and exception handling make it easy to write clean, readable code that handles both normal and exceptional cases effectively.
