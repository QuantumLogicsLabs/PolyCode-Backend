# Ruby Loops and Iteration

## While Loops

### Basic While Loop
```ruby
# Simple while loop
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

# While loop with boolean condition
user_input = ""
while user_input != "quit"
  print "Enter command (or 'quit' to exit): "
  user_input = gets.chomp.downcase
  puts "You entered: #{user_input}"
end

# While loop with complex condition
temperature = 70
humidity = 80

while temperature > 60 && humidity > 70
  puts "Temperature: #{temperature}°F, Humidity: #{humidity}%"
  temperature -= 2
  humidity -= 1
  sleep 1
end
```

### While Loop Modifiers
```ruby
# While as modifier
count = 0
count += 1 while count < 5
puts "Final count: #{count}"

# While modifier with method call
numbers = [1, 2, 3, 4, 5]
numbers.shift while numbers.length > 3
puts "Remaining numbers: #{numbers.inspect}"

# While modifier with block
lines = []
lines << gets.chomp while !lines.include?("quit")
puts "Lines collected: #{lines.inspect}"
```

### Until Loops
```ruby
# Until loop (opposite of while)
count = 0
until count >= 5
  puts "Count: #{count}"
  count += 1
end

# Until loop with condition
user_authenticated = false
attempts = 0

until user_authenticated || attempts >= 3
  print "Enter password: "
  password = gets.chomp
  
  if password == "secret"
    user_authenticated = true
    puts "Access granted!"
  else
    attempts += 1
    puts "Access denied. Attempts left: #{3 - attempts}"
  end
end

# Until as modifier
count = 0
count += 1 until count >= 5
puts "Final count: #{count}"
```

## For Loops

### Basic For Loop
```ruby
# For loop with range
for i in 1..5
  puts "Number: #{i}"
end

# For loop with exclusive range
for i in 1...5
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

# For loop with string
for char in "hello"
  puts "Character: #{char}"
end
```

### Nested For Loops
```ruby
# Nested for loops
for i in 1..3
  for j in 1..3
    puts "#{i} x #{j} = #{i * j}"
  end
end

# Multiplication table
for i in 1..10
  row = ""
  for j in 1..10
    row += "#{i * j}\t"
  end
  puts row
end

# Processing 2D array
matrix = [[1, 2, 3], [4, 5, 6], [7, 8, 9]]
for i in 0...matrix.length
  for j in 0...matrix[i].length
    print "#{matrix[i][j]}\t"
  end
  puts
end
```

### For Loop with Control
```ruby
# For loop with break
for i in 1..10
  break if i > 5
  puts "Number: #{i}"
end

# For loop with next (continue)
for i in 1..10
  next if i.even?
  puts "Odd number: #{i}"
end

# For loop with redo
for i in 1..5
  puts "Number: #{i}"
  redo if i == 3  # Restart loop when i is 3
end

# For loop with labels
outer_loop:
for i in 1..3
  for j in 1..3
    puts "#{i}, #{j}"
    break outer_loop if i == 2 && j == 2
  end
end
```

## Iterator Methods

### Each Iterator
```ruby
# Array each
numbers = [1, 2, 3, 4, 5]
numbers.each do |number|
  puts "Number: #{number}"
end

# Each with index
fruits = ["apple", "banana", "cherry"]
fruits.each_with_index do |fruit, index|
  puts "#{index}: #{fruit}"
end

# Hash each
person = { name: "John", age: 30, city: "NYC" }
person.each do |key, value|
  puts "#{key}: #{value}"
end

# Each with object (for building collections)
words = ["hello", "world", "ruby"]
word_lengths = words.each_with_object({}) do |word, hash|
  hash[word] = word.length
end
puts word_lengths.inspect
```

### Map and Collect
```ruby
# Map (alias for collect)
numbers = [1, 2, 3, 4, 5]
doubled = numbers.map { |number| number * 2 }
puts doubled.inspect  # [2, 4, 6, 8, 10]

# Map with transformation
words = ["hello", "world", "ruby"]
capitalized = words.map(&:upcase)
puts capitalized.inspect  # ["HELLO", "WORLD", "RUBY"]

# Map with index
words = ["hello", "world", "ruby"]
numbered = words.map.with_index { |word, index| "#{index + 1}. #{word}" }
puts numbered.inspect  # ["1. hello", "2. world", "3. ruby"]

# Map on hash
person = { name: "John", age: 30 }
transformed = person.map { |key, value| [key.to_s.upcase, value] }.to_h
puts transformed.inspect  # {"NAME"=>"John", "AGE"=>30}

# Collect with multiple arrays
numbers1 = [1, 2, 3]
numbers2 = [4, 5, 6]
combined = numbers1.zip(numbers2).map { |a, b| a + b }
puts combined.inspect  # [5, 7, 9]
```

### Select and Reject
```ruby
# Select (alias for find_all)
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
evens = numbers.select { |number| number.even? }
puts evens.inspect  # [2, 4, 6, 8, 10]

# Select with multiple conditions
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
filtered = numbers.select { |n| n.even? && n > 4 }
puts filtered.inspect  # [6, 8, 10]

# Reject (opposite of select)
odds = numbers.reject { |number| number.even? }
puts odds.inspect  # [1, 3, 5, 7, 9]

# Select on hash
person = { name: "John", age: 30, active: true, score: 85 }
active_attributes = person.select { |key, value| value.is_a?(TrueClass) }
puts active_attributes.inspect  # {:active=>true}

# Reject on hash
numeric_attributes = person.reject { |key, value| value.is_a?(TrueClass) }
puts numeric_attributes.inspect  # {:name=>"John", :age=>30, :score=>85}
```

### Find and Detect
```ruby
# Find (alias for detect)
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
first_even = numbers.find { |number| number.even? }
puts "First even: #{first_even}"  # 2

# Find with condition
words = ["apple", "banana", "cherry", "date"]
first_long = words.find { |word| word.length > 5 }
puts "First long word: #{first_long}"  # banana

# Find all (alias for select)
all_evens = numbers.find_all { |number| number.even? }
puts "All evens: #{all_evens.inspect}"  # [2, 4, 6, 8, 10]

# Find index
numbers = [10, 20, 30, 40, 50]
index_of_30 = numbers.find_index { |number| number == 30 }
puts "Index of 30: #{index_of_30}"  # 2

# Find with index
words = ["apple", "banana", "cherry"]
index_of_long = words.find_index { |word| word.length > 5 }
puts "Index of first long word: #{index_of_long}"  # 1
```

### Any, All, None
```ruby
# Any? (alias for any)
numbers = [1, 2, 3, 4, 5]
has_even = numbers.any? { |number| number.even? }
puts "Has even: #{has_even}"  # true

# Any with condition
words = ["apple", "banana", "cherry"]
has_long = words.any? { |word| word.length > 6 }
puts "Has long word: #{has_long}"  # false

# All? (alias for all)
numbers = [2, 4, 6, 8, 10]
all_even = numbers.all? { |number| number.even? }
puts "All even: #{all_even}"  # true

# All with condition
words = ["apple", "banana", "cherry"]
all_long = words.all? { |word| word.length > 4 }
puts "All long: #{all_long}"  # true

# None? (opposite of any?)
numbers = [1, 3, 5, 7, 9]
no_even = numbers.none? { |number| number.even? }
puts "No even: #{no_even}"  # true

# None with condition
words = ["apple", "banana", "cherry"]
no_short = words.none? { |word| word.length < 3 }
puts "No short: #{no_short}"  # true
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
word_info = words.reduce({}) do |hash, word|
  hash[word] = { length: word.length, first_char: word[0] }
  hash
end
puts word_info.inspect

# Reduce with symbol method
numbers = [1, 2, 3, 4, 5]
sum = numbers.reduce(:+)
product = numbers.reduce(:*)
puts "Sum: #{sum}, Product: #{product}"  # Sum: 15, Product: 120

# Reduce with complex logic
numbers = [1, 2, 3, 4, 5]
result = numbers.reduce({ sum: 0, product: 1 }) do |acc, number|
  acc[:sum] += number
  acc[:product] *= number
  acc
end
puts result.inspect  # {:sum=>15, :product=>120}
```

### Group By
```ruby
# Group by
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
grouped = numbers.group_by { |number| number.even? ? :even : :odd }
puts grouped.inspect  # {:odd=>[1, 3, 5, 7, 9], :even=>[2, 4, 6, 8, 10]}

# Group by length
words = ["apple", "banana", "cherry", "date", "fig"]
by_length = words.group_by { |word| word.length }
puts by_length.inspect  # {5=>["apple"], 6=>["banana", "cherry"], 4=>["date"], 3=>["fig"]}

# Group by first character
words = ["apple", "banana", "cherry", "apricot", "blueberry"]
by_first_char = words.group_by { |word| word[0] }
puts by_first_char.inspect  # {"a"=>["apple", "apricot"], "b"=>["banana", "blueberry"], "c"=>["cherry"]}

# Group by complex condition
people = [
  { name: "John", age: 25, city: "NYC" },
  { name: "Jane", age: 30, city: "NYC" },
  { name: "Bob", age: 25, city: "LA" },
  { name: "Alice", age: 30, city: "LA" }
]
by_age_city = people.group_by { |person| "#{person[:age]}_#{person[:city]}" }
puts by_age_city.inspect
```

## Loop Control

### Break Statement
```ruby
# Break from while loop
count = 0
while count < 10
  puts "Count: #{count}"
  break if count == 5
  count += 1
end

# Break from for loop
for i in 1..10
  puts "Number: #{i}"
  break if i == 5
end

# Break from iterator
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
numbers.each do |number|
  puts "Number: #{number}"
  break if number > 5
end

# Break from nested loops
for i in 1..3
  for j in 1..3
    puts "#{i}, #{j}"
    break if i == 2 && j == 2
  end
end

# Break with label
outer_loop:
for i in 1..3
  for j in 1..3
    puts "#{i}, #{j}"
    break outer_loop if i == 2 && j == 2
  end
end
```

### Next Statement
```ruby
# Next in while loop
count = 0
while count < 10
  count += 1
  next if count.even?
  puts "Odd count: #{count}"
end

# Next in for loop
for i in 1..10
  next if i.even?
  puts "Odd number: #{i}"
end

# Next in iterator
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
numbers.each do |number|
  next if number.even?
  puts "Odd number: #{number}"
end

# Next with condition
words = ["apple", "banana", "cherry", "date", "fig"]
words.each do |word|
  next if word.length < 5
  puts "Long word: #{word}"
end
```

### Redo Statement
```ruby
# Redo in while loop
count = 0
while count < 5
  count += 1
  puts "Count: #{count}"
  redo if count == 3  # Restart loop when count is 3
end

# Redo in for loop
for i in 1..5
  puts "Number: #{i}"
  redo if i == 3  # Restart loop when i is 3
end

# Redo in iterator
numbers = [1, 2, 3, 4, 5]
numbers.each do |number|
  puts "Number: #{number}"
  redo if number == 3  # Restart iteration when number is 3
end

# Redo with condition
attempts = 0
max_attempts = 3

begin
  # Simulate user input
  input = attempts < 2 ? "wrong" : "correct"
  attempts += 1
  
  puts "Attempt #{attempts}: #{input}"
  redo unless input == "correct" || attempts >= max_attempts
end
```

### Retry Statement
```ruby
# Retry with exception handling
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

# Retry with different logic
def connect_to_database
  attempts = 0
  begin
    # Simulate database connection
    raise "Connection failed" if attempts < 2
    puts "Database connected"
  rescue => e
    attempts += 1
    if attempts < 3
      puts "Retrying connection..."
      sleep 2
      retry
    else
      puts "Failed to connect after 3 attempts"
      raise
    end
  end
end

connect_to_database
```

## Advanced Iteration

### Lazy Enumeration
```ruby
# Lazy enumerator
lazy_numbers = (1..Float::INFINITY).lazy
  .select { |n| n.even? }
  .map { |n| n * 2 }
  .take(5)

puts lazy_numbers.to_a.inspect  # [4, 8, 12, 16, 20]

# Lazy evaluation benefits
def fibonacci_numbers
  Enumerator.new do |yielder|
    a, b = 0, 1
    loop do
      yielder.yield(a)
      a, b = b, a + b
    end
  end
end

# Take first 10 Fibonacci numbers
fib_10 = fibonacci_numbers.lazy.take(10).to_a
puts fib_10.inspect  # [0, 1, 1, 2, 3, 5, 8, 13, 21, 34]
```

### Custom Iterators
```ruby
# Custom iterator class
class Counter
  def initialize(start, finish)
    @start = start
    @finish = finish
  end
  
  def each
    current = @start
    while current <= @finish
      yield current
      current += 1
    end
  end
end

# Use custom iterator
counter = Counter.new(1, 5)
counter.each { |i| puts "Count: #{i}" }

# Iterator with include Enumerable
class Squares
  include Enumerable
  
  def initialize(limit)
    @limit = limit
  end
  
  def each
    (1..@limit).each { |i| yield i * i }
  end
end

squares = Squares.new(5)
squares.each { |square| puts "Square: #{square}" }

# Can use all Enumerable methods
puts squares.select { |n| n > 10 }.inspect  # [16, 25]
```

### Parallel Iteration
```ruby
# Parallel processing with threads
require 'thread'

def parallel_each(collection, &block)
  threads = []
  collection.each do |item|
    threads << Thread.new { block.call(item) }
  end
  threads.each(&:join)
end

numbers = [1, 2, 3, 4, 5]
parallel_each(numbers) do |number|
  puts "Processing #{number} on thread #{Thread.current.object_id}"
  sleep 1
end

# Parallel map
def parallel_map(collection, &block)
  threads = []
  results = []
  
  collection.each_with_index do |item, index|
    threads << Thread.new do
      results[index] = block.call(item)
    end
  end
  
  threads.each(&:join)
  results
end

numbers = [1, 2, 3, 4, 5]
doubled = parallel_map(numbers) { |n| n * 2 }
puts doubled.inspect  # [2, 4, 6, 8, 10]
```

## Performance Considerations

### Efficient Iteration
```ruby
# Good: Use appropriate iterator method
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

# For transformation
doubled = numbers.map { |n| n * 2 }

# For filtering
evens = numbers.select { |n| n.even? }

# For finding
first_even = numbers.find { |n| n.even? }

# For checking condition
has_even = numbers.any? { |n| n.even? }

# Bad: Using each when other methods are better
doubled_bad = []
numbers.each { |n| doubled_bad << n * 2 }

evens_bad = []
numbers.each { |n| evens_bad << n if n.even? }

# Good: Use lazy evaluation for large datasets
large_numbers = (1..1000000).lazy
  .select { |n| n.even? }
  .map { |n| n * 2 }
  .take(10)

result = large_numbers.to_a
```

### Memory Management
```ruby
# Good: Use lazy evaluation to avoid loading everything into memory
def process_large_file(filename)
  File.foreach(filename) do |line|
    yield line
  end
end

# Usage
process_large_file("large_file.txt") do |line|
  puts line
end

# Good: Use each_cons for overlapping windows
numbers = [1, 2, 3, 4, 5]
pairs = numbers.each_cons(2).to_a
puts pairs.inspect  # [[1, 2], [2, 3], [3, 4], [4, 5]]

# Good: Use each_slice for non-overlapping windows
triples = numbers.each_slice(3).to_a
puts triples.inspect  # [[1, 2, 3], [4, 5]]

# Good: Use chunk for grouping
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
chunks = numbers.chunk { |n| n / 3 }.to_a
puts chunks.inspect  # [[0, [1, 2, 3]], [1, [4, 5, 6]], [2, [7, 8, 9]], [3, [10]]]
```

## Best Practices

### Loop Selection
```ruby
# Use while for condition-based loops
count = 0
while count < 10
  # Process while condition is true
  count += 1
end

# Use until for negative condition loops
until user_authenticated
  # Process until condition is true
  authenticate_user
end

# Use each for collection iteration (idiomatic Ruby)
numbers = [1, 2, 3, 4, 5]
numbers.each { |n| puts n }

# Use for loops only when necessary (rare in Ruby)
for i in 1..5
  puts i
end

# Use appropriate iterator methods
# For transformation
transformed = collection.map { |item| transform(item) }

# For filtering
filtered = collection.select { |item| condition?(item) }

# For finding
found = collection.find { |item| condition?(item) }

# For checking
any_match = collection.any? { |item| condition?(item) }
all_match = collection.all? { |item| condition?(item) }
```

### Code Organization
```ruby
# Good: Use block for resource management
File.open("file.txt", "r") do |file|
  file.each_line do |line|
    process_line(line)
  end
end  # File automatically closed

# Good: Use method chaining for complex operations
result = numbers
  .select { |n| n.even? }
  .map { |n| n * 2 }
  .reject { |n| n > 10 }
  .take(5)

# Good: Use meaningful variable names in blocks
users.each do |user|
  user.process if user.active?
end

# Good: Keep blocks short and focused
numbers.each do |number|
  processed = process_number(number)
  save_result(processed)
end
```

### Error Handling
```ruby
# Good: Handle exceptions in loops
begin
  items.each do |item|
    begin
      process_item(item)
    rescue => e
      logger.error "Error processing #{item}: #{e.message}"
      next  # Continue with next item
    end
  end
rescue => e
  logger.error "Fatal error in loop: #{e.message}"
  raise
end

# Good: Use retry with limit
max_retries = 3
attempts = 0

begin
  risky_operation
rescue => e
  attempts += 1
  if attempts < max_retries
    sleep 1
    retry
  else
    raise "Operation failed after #{max_retries} attempts"
  end
end
```

## Common Pitfalls

### Loop Control Issues
```ruby
# Pitfall: Infinite loop
count = 0
while count < 10
  puts count
  # Forgot to increment count
end

# Solution: Ensure loop termination
count = 0
while count < 10
  puts count
  count += 1
end

# Pitfall: Modifying collection while iterating
numbers = [1, 2, 3, 4, 5]
numbers.each do |number|
  numbers.delete(number) if number.even?  # Bad: modifies while iterating
end

# Solution: Use reject or create new collection
numbers = [1, 2, 3, 4, 5]
numbers = numbers.reject { |number| number.even? }

# Pitfall: Using break incorrectly
numbers = [1, 2, 3, 4, 5]
numbers.each do |number|
  break if number > 3
  puts number
end
# Only prints 1, 2, 3

# Solution: Use next to skip items
numbers = [1, 2, 3, 4, 5]
numbers.each do |number|
  next if number > 3
  puts number
end
```

### Performance Issues
```ruby
# Pitfall: Inefficient iteration
words = ["apple", "banana", "cherry", "date", "fig"]
long_words = []

words.each do |word|
  long_words << word if word.length > 4
end

# Solution: Use select directly
long_words = words.select { |word| word.length > 4 }

# Pitfall: Unnecessary array creation
numbers = [1, 2, 3, 4, 5]
sum = 0
numbers.each { |n| sum += n }

# Solution: Use reduce
sum = numbers.reduce(0, :+)

# Pitfall: Loading large datasets into memory
# Bad: Loads everything into memory
all_lines = File.readlines("large_file.txt")
all_lines.each { |line| process_line(line) }

# Solution: Process line by line
File.foreach("large_file.txt") do |line|
  process_line(line)
end
```

### Iterator Method Misuse
```ruby
# Pitfall: Using map when you don't need the return value
numbers = [1, 2, 3, 4, 5]
numbers.map { |n| puts n }  # Returns array of nil values

# Solution: Use each
numbers.each { |n| puts n }

# Pitfall: Using select when you need to find first match
numbers = [1, 2, 3, 4, 5]
first_even = numbers.select { |n| n.even? }.first

# Solution: Use find
first_even = numbers.find { |n| n.even? }

# Pitfall: Using each when you need to check condition
numbers = [1, 2, 3, 4, 5]
has_even = false
numbers.each { |n| has_even = true if n.even? }

# Solution: Use any?
has_even = numbers.any? { |n| n.even? }
```

## Summary

Ruby loops and iteration provide:

**Loop Types:**
- while loops for condition-based iteration
- until loops for negative condition iteration
- for loops for range and collection iteration
- iterator methods (idiomatic Ruby approach)

**Iterator Methods:**
- each for basic iteration
- map/collect for transformation
- select/reject for filtering
- find/detect for searching
- any/all/none for condition checking
- reduce/inject for aggregation
- group_by for categorization

**Loop Control:**
- break to exit loops early
- next to skip iterations
- redo to restart iterations
- retry to retry failed operations

**Advanced Features:**
- lazy enumeration for memory efficiency
- custom iterators with Enumerable
- parallel iteration with threads
- chunk, slice, and cons methods

**Performance Considerations:**
- Choose appropriate iterator methods
- Use lazy evaluation for large datasets
- Avoid modifying collections during iteration
- Process files line by line when possible

**Best Practices:**
- Prefer iterator methods over manual loops
- Use blocks for resource management
- Handle exceptions gracefully in loops
- Keep blocks short and focused

**Common Pitfalls:**
- Infinite loops
- Modifying collections while iterating
- Using wrong iterator methods
- Performance issues with large datasets
- Incorrect loop control usage

Ruby's rich set of iteration methods and flexible loop constructs make it easy to write clean, efficient code for processing collections and implementing complex algorithms.
