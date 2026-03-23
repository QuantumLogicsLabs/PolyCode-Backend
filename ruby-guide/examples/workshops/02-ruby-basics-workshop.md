# Ruby Basics Workshop

## Overview

This workshop covers the fundamental concepts of Ruby programming. Perfect for beginners who want to learn Ruby from scratch or experienced developers who want to solidify their understanding of Ruby basics.

## Workshop Structure

### Part 1: Ruby Fundamentals
- Ruby syntax and basic concepts
- Variables, data types, and operators
- Control flow and conditional logic
- Methods and function basics

### Part 2: Object-Oriented Programming
- Classes and objects
- Inheritance and modules
- Instance vs class methods
- Encapsulation and access modifiers

### Part 3: Ruby Collections
- Arrays and their methods
- Hashes and key-value pairs
- Ranges and their applications
- Enumeration and iteration patterns

### Part 4: Advanced Basics
- Blocks, procs, and lambdas
- Exception handling
- File I/O operations
- Basic metaprogramming concepts

## Part 1: Ruby Fundamentals

### Exercise 1: Hello World and Variables
```ruby
# Exercise: Create a program that greets a user and stores their information

# Your task: Complete the following code
def greet_user(name, age, location)
  # Create a greeting message
  greeting = "Hello, #{name}!"
  
  # Store user information in variables
  user_info = {
    name: name,
    age: age,
    location: location,
    greeting: greeting
  }
  
  # Return the user information
  user_info
end

# Test your solution
result = greet_user("Alice", 30, "New York")
puts result[:greeting]  # Should print: "Hello, Alice!"
puts "Age: #{result[:age]}"
puts "Location: #{result[:location]}"
```

### Exercise 2: Data Types and Type Conversion
```ruby
# Exercise: Work with different data types and conversions

def process_data_types
  # String operations
  text = "Ruby Programming"
  reversed = text.reverse
  uppercase = text.upcase
  length = text.length
  
  # Number operations
  number = 42
  float_number = 3.14159
  squared = number ** 2
  
  # Boolean operations
  is_ruby_fun = true
  is_hard = false
  
  # Type conversions
  string_to_number = "123".to_i
  number_to_string = 456.to_s
  float_to_int = 7.89.to_i
  
  {
    text_info: { original: text, reversed: reversed, uppercase: uppercase, length: length },
    number_info: { original: number, squared: squared },
    float_info: { original: float_number },
    boolean_info: { ruby_fun: is_ruby_fun, hard: is_hard },
    conversions: { str_to_num: string_to_number, num_to_str: number_to_string, float_to_int: float_to_int }
  }
end

# Test the function
result = process_data_types
puts "Text: #{result[:text_info][:original]} -> #{result[:text_info][:reversed]}"
puts "Number squared: #{result[:number_info][:squared]}"
```

### Exercise 3: Control Flow
```ruby
# Exercise: Implement conditional logic and loops

def analyze_number(number)
  result = []
  
  # Conditional statements
  if number > 0
    result << "#{number} is positive"
  elsif number < 0
    result << "#{number} is negative"
  else
    result << "#{number} is zero"
  end
  
  # Multiple conditions
  case number
  when 1..10
    result << "#{number} is between 1 and 10"
  when 11..20
    result << "#{number} is between 11 and 20"
  else
    result << "#{number} is outside the range 1-20"
  end
  
  # Loop example
  result << "Counting up to #{number}:"
  (1..[number, 5].min).each do |i|
    result << "  #{i}"
  end
  
  result
end

# Test the function
puts analyze_number(5).join("\n")
puts "\n" + analyze_number(15).join("\n")
```

## Part 2: Object-Oriented Programming

### Exercise 4: Creating Your First Class
```ruby
# Exercise: Create a Person class with attributes and methods

class Person
  attr_accessor :name, :age, :hobbies
  
  def initialize(name, age)
    @name = name
    @age = age
    @hobbies = []
  end
  
  def add_hobby(hobby)
    @hobbies << hobby
  end
  
  def remove_hobby(hobby)
    @hobbies.delete(hobby)
  end
  
  def introduce_yourself
    "Hi, I'm #{@name} and I'm #{@age} years old."
  end
  
  def list_hobbies
    if @hobbies.empty?
      "I don't have any hobbies yet."
    else
      "My hobbies are: #{@hobbies.join(', ')}."
    end
  end
  
  def celebrate_birthday
    @age += 1
    "Happy birthday! I'm now #{@age} years old."
  end
end

# Test the Person class
person = Person.new("Bob", 25)
puts person.introduce_yourself
person.add_hobby("Reading")
person.add_hobby("Programming")
puts person.list_hobbies
puts person.celebrate_birthday
puts person.introduce_yourself
```

### Exercise 5: Inheritance and Polymorphism
```ruby
# Exercise: Create a hierarchy of animal classes

class Animal
  attr_accessor :name, :age
  
  def initialize(name, age)
    @name = name
    @age = age
  end
  
  def speak
    "Some animal sound"
  end
  
  def eat
    "#{@name} is eating"
  end
  
  def sleep
    "#{@name} is sleeping"
  end
end

class Dog < Animal
  def speak
    "Woof! Woof!"
  end
  
  def fetch
    "#{@name} is fetching the ball"
  end
end

class Cat < Animal
  def speak
    "Meow!"
  end
  
  def purr
    "#{@name} is purring"
  end
end

class Bird < Animal
  def speak
    "Tweet! Tweet!"
  end
  
  def fly
    "#{@name} is flying"
  end
end

# Test the animal hierarchy
animals = [
  Dog.new("Rex", 3),
  Cat.new("Whiskers", 2),
  Bird.new("Tweety", 1)
]

animals.each do |animal|
  puts "#{animal.class.name}: #{animal.name}"
  puts "  Sound: #{animal.speak}"
  puts "  Eating: #{animal.eat}"
  
  # Polymorphic behavior based on class
  if animal.respond_to?(:fetch)
    puts "  #{animal.fetch}"
  elsif animal.respond_to?(:purr)
    puts "  #{animal.purr}"
  elsif animal.respond_to?(:fly)
    puts "  #{animal.fly}"
  end
  
  puts
end
```

### Exercise 6: Modules and Mixins
```ruby
# Exercise: Create modules to share functionality

module Swimmable
  def swim
    "#{self.class.name} is swimming"
  end
  
  def dive(depth)
    "#{self.class.name} is diving to #{depth} feet"
  end
end

module Flyable
  def fly
    "#{self.class.name} is flying"
  end
  
  def land
    "#{self.class.name} is landing"
  end
end

module Trainable
  def learn_trick(trick)
    @tricks ||= []
    @tricks << trick
    "#{self.class.name} learned #{trick}!"
  end
  
  def perform_tricks
    if @tricks && @tricks.any?
      "#{self.class.name} can perform: #{@tricks.join(', ')}"
    else
      "#{self.class.name} hasn't learned any tricks yet."
    end
  end
end

class Duck
  include Swimmable
  include Flyable
  include Trainable
  
  attr_accessor :name
  
  def initialize(name)
    @name = name
  end
  
  def quack
    "Quack! Quack!"
  end
end

class Fish
  include Swimmable
  
  attr_accessor :name
  
  def initialize(name)
    @name = name
  end
  
  def breathe_underwater
    "#{@name} is breathing underwater"
  end
end

# Test the modules
duck = Duck.new("Donald")
fish = Fish.new("Nemo")

puts duck.swim
puts duck.fly
puts duck.learn_trick("sit")
puts duck.learn_trick("roll over")
puts duck.perform_tricks

puts "\n" + fish.swim
puts fish.breathe_underwater
```

## Part 3: Ruby Collections

### Exercise 7: Working with Arrays
```ruby
# Exercise: Master array operations

def array_operations
  # Creating arrays
  numbers = [1, 2, 3, 4, 5]
  mixed = [1, "hello", true, 3.14]
  range_array = (1..10).to_a
  
  # Array methods
  fruits = ["apple", "banana", "orange", "grape"]
  
  results = []
  
  # Basic operations
  results << "First fruit: #{fruits.first}"
  results << "Last fruit: #{fruits.last}"
  results << "Array length: #{fruits.length}"
  
  # Adding and removing elements
  fruits << "kiwi"
  results << "After adding kiwi: #{fruits.join(', ')}"
  
  removed = fruits.pop
  results << "Removed: #{removed}, remaining: #{fruits.join(', ')}"
  
  # Array transformations
  numbers = [1, 2, 3, 4, 5]
  doubled = numbers.map { |n| n * 2 }
  results << "Doubled numbers: #{doubled.join(', ')}"
  
  evens = numbers.select { |n| n.even? }
  results << "Even numbers: #{evens.join(', ')}"
  
  # Array iteration
  sum = numbers.reduce(0) { |total, n| total + n }
  results << "Sum of numbers: #{sum}"
  
  # Checking contents
  results << "Contains 3: #{numbers.include?(3)}"
  results << "Contains 10: #{numbers.include?(10)}"
  
  results
end

# Test array operations
array_operations.each { |result| puts result }
```

### Exercise 8: Working with Hashes
```ruby
# Exercise: Master hash operations

def hash_operations
  # Creating hashes
  person = {
    name: "Alice",
    age: 30,
    city: "New York",
    country: "USA"
  }
  
  results = []
  
  # Accessing values
  results << "Name: #{person[:name]}"
  results << "Age: #{person[:age]}"
  
  # Adding and modifying
  person[:email] = "alice@example.com"
  person[:age] = 31
  results << "Updated age: #{person[:age]}"
  results << "Email: #{person[:email]}"
  
  # Hash methods
  results << "Keys: #{person.keys.join(', ')}"
  results << "Values: #{person.values.join(', ')}"
  
  # Iterating over hashes
  person.each do |key, value|
    results << "#{key}: #{value}"
  end
  
  # Checking for keys
  results << "Has name key: #{person.key?(:name)}"
  results << "Has phone key: #{person.key?(:phone)}"
  
  # Default values
  settings = Hash.new("default")
  settings[:theme] = "dark"
  results << "Theme: #{settings[:theme]}"
  results << "Font: #{settings[:font]}"  # Uses default value
  
  results
end

# Test hash operations
hash_operations.each { |result| puts result }
```

### Exercise 9: Ranges and Iteration
```ruby
# Exercise: Work with ranges and iteration patterns

def range_operations
  results = []
  
  # Creating ranges
  inclusive_range = 1..5
  exclusive_range = 1...5
  letter_range = 'a'..'e'
  
  results << "Inclusive range (1..5): #{inclusive_range.to_a.join(', ')}"
  results << "Exclusive range (1...5): #{exclusive_range.to_a.join(', ')}"
  results << "Letter range: #{letter_range.to_a.join(', ')}"
  
  # Range methods
  numbers = 1..10
  results << "Range includes 5: #{numbers.include?(5)}"
  results << "Range includes 11: #{numbers.include?(11)}"
  results << "Range first: #{numbers.first}"
  results << "Range last: #{numbers.last}"
  
  # Iteration patterns
  results << "Counting with each:"
  (1..5).each { |i| results << "  #{i}" }
  
  results << "Selecting even numbers:"
  evens = (1..10).select { |n| n.even? }
  results << "  #{evens.join(', ')}"
  
  results << "Mapping to squares:"
  squares = (1..5).map { |n| n ** 2 }
  results << "  #{squares.join(', ')}"
  
  # Step ranges
  results << "Every 2nd number (2..10 step 2):"
  step_range = (2..10).step(2)
  results << "  #{step_range.to_a.join(', ')}"
  
  results
end

# Test range operations
range_operations.each { |result| puts result }
```

## Part 4: Advanced Basics

### Exercise 10: Blocks and Iterators
```ruby
# Exercise: Master blocks and custom iterators

class CustomCollection
  def initialize(items)
    @items = items
  end
  
  def each
    @items.each { |item| yield item }
  end
  
  def select
    selected = []
    each { |item| selected << item if yield(item) }
    selected
  end
  
  def map
    mapped = []
    each { |item| mapped << yield(item) }
    mapped
  end
  
  def find
    each do |item|
      return item if yield(item)
    end
    nil
  end
end

# Test blocks and iterators
collection = CustomCollection.new([1, 2, 3, 4, 5])

puts "Original collection:"
collection.each { |item| puts "  #{item}" }

puts "\nSelect even numbers:"
evens = collection.select { |n| n.even? }
evens.each { |item| puts "  #{item}" }

puts "\nMap to squares:"
squares = collection.map { |n| n ** 2 }
squares.each { |item| puts "  #{item}" }

puts "\nFind first number > 3:"
found = collection.find { |n| n > 3 }
puts "  #{found}"
```

### Exercise 11: Exception Handling
```ruby
# Exercise: Implement proper exception handling

def safe_divide(a, b)
  begin
    result = a / b
    "Result: #{result}"
  rescue ZeroDivisionError => e
    "Error: Cannot divide by zero"
  rescue TypeError => e
    "Error: Invalid types for division"
  end
end

def process_file(filename)
  begin
    # Simulate file processing
    if filename.nil? || filename.empty?
      raise ArgumentError, "Filename cannot be empty"
    end
    
    if filename == "nonexistent.txt"
      raise Errno::ENOENT, "File not found"
    end
    
    "Successfully processed #{filename}"
  rescue ArgumentError => e
    "Argument Error: #{e.message}"
  rescue Errno::ENOENT => e
    "File Error: #{e.message}"
  rescue StandardError => e
    "Unexpected error: #{e.message}"
  end
end

# Test exception handling
puts safe_divide(10, 2)
puts safe_divide(10, 0)
puts safe_divide("10", 2)

puts "\nFile processing:"
puts process_file("test.txt")
puts process_file("")
puts process_file("nonexistent.txt")
```

### Exercise 12: File I/O Operations
```ruby
# Exercise: Basic file operations

def file_operations_demo
  # Write to file
  File.write('test_file.txt', "Hello, Ruby!\nThis is a test file.\nRuby is awesome!")
  
  # Read entire file
  content = File.read('test_file.txt')
  puts "File content:"
  puts content
  
  # Read line by line
  puts "\nReading line by line:"
  File.readlines('test_file.txt').each_with_index do |line, index|
    puts "Line #{index + 1}: #{line.strip}"
  end
  
  # Append to file
  File.open('test_file.txt', 'a') do |file|
    file.puts("Appended line")
    file.puts("Another appended line")
  end
  
  puts "\nAfter appending:"
  puts File.read('test_file.txt')
  
  # Check file information
  puts "\nFile information:"
  puts "Exists: #{File.exist?('test_file.txt')}"
  puts "Size: #{File.size('test_file.txt')} bytes"
  puts "Readable: #{File.readable?('test_file.txt')}"
  puts "Writable: #{File.writable?('test_file.txt')}"
  
  # Clean up
  File.delete('test_file.txt')
  puts "\nFile deleted."
end

# Test file operations
file_operations_demo
```

## Workshop Challenges

### Challenge 1: Build a Simple Calculator
```ruby
# Challenge: Create a calculator class with basic operations

class Calculator
  def add(a, b)
    a + b
  end
  
  def subtract(a, b)
    a - b
  end
  
  def multiply(a, b)
    a * b
  end
  
  def divide(a, b)
    raise ZeroDivisionError, "Cannot divide by zero" if b == 0
    a / b.to_f
  end
  
  def power(base, exponent)
    base ** exponent
  end
  
  def sqrt(number)
    raise ArgumentError, "Cannot calculate square root of negative number" if number < 0
    Math.sqrt(number)
  end
end

# Test your calculator
calc = Calculator.new
puts "2 + 3 = #{calc.add(2, 3)}"
puts "10 - 4 = #{calc.subtract(10, 4)}"
puts "6 * 7 = #{calc.multiply(6, 7)}"
puts "15 / 3 = #{calc.divide(15, 3)}"
puts "2 ^ 8 = #{calc.power(2, 8)}"
puts "sqrt(16) = #{calc.sqrt(16)}"
```

### Challenge 2: Create a Todo List Manager
```ruby
# Challenge: Build a todo list application

class TodoList
  def initialize
    @todos = []
  end
  
  def add_task(task)
    @todos << { id: @todos.length + 1, task: task, completed: false, created_at: Time.now }
    "Task added: #{task}"
  end
  
  def complete_task(id)
    todo = @todos.find { |t| t[:id] == id }
    return "Task not found" unless todo
    
    todo[:completed] = true
    todo[:completed_at] = Time.now
    "Task completed: #{todo[:task]}"
  end
  
  def list_tasks
    if @todos.empty?
      "No tasks yet!"
    else
      @todos.map do |todo|
        status = todo[:completed] ? "✓" : "○"
        "#{status} [#{todo[:id]}] #{todo[:task]}"
      end.join("\n")
    end
  end
  
  def pending_tasks
    @todos.reject { |todo| todo[:completed] }.length
  end
  
  def completed_tasks
    @todos.select { |todo| todo[:completed] }.length
  end
end

# Test your todo list
todo = TodoList.new
puts todo.add_task("Learn Ruby basics")
puts todo.add_task("Build a Ruby app")
puts todo.add_task("Read Ruby documentation")
puts "\nAll tasks:"
puts todo.list_tasks
puts todo.complete_task(2)
puts "\nUpdated tasks:"
puts todo.list_tasks
puts "\nPending: #{todo.pending_tasks}, Completed: #{todo.completed_tasks}"
```

## Workshop Summary

### Key Concepts Covered
1. **Ruby Syntax**: Variables, data types, operators
2. **Control Flow**: Conditionals, loops, case statements
3. **OOP**: Classes, objects, inheritance, modules
4. **Collections**: Arrays, hashes, ranges, iteration
5. **Advanced Concepts**: Blocks, exception handling, file I/O

### Next Steps
1. Practice with real-world projects
2. Learn Ruby on Rails for web development
3. Explore Ruby gems and ecosystem
4. Study advanced topics like metaprogramming
5. Contribute to open source Ruby projects

### Resources for Continued Learning
- [Ruby Documentation](https://www.ruby-lang.org/en/documentation/)
- [Ruby Style Guide](https://rubystyle.guide/)
- [Learn Ruby the Hard Way](https://learncodethehardway.org/ruby/)
- [Ruby Koans](https://github.com/rubykoans/rubykoans)
- [Exercism Ruby Track](https://exercism.io/tracks/ruby)

## Workshop Exercises Solutions

### Self-Assessment
Test your understanding by completing these exercises:

1. **Variable Scoping**: Create a program that demonstrates local vs instance variables
2. **Method Chaining**: Build a fluent interface for a string processor
3. **Collection Mastery**: Implement a custom collection class with useful methods
4. **Error Handling**: Create robust methods with proper exception handling
5. **File Processing**: Build a simple log file analyzer

### Project Ideas
1. **Contact Manager**: Store and manage contact information
2. **Simple Blog**: Create a command-line blog system
3. **Weather App**: Fetch and display weather data
4. **Task Scheduler**: Build a simple task scheduling system
5. **Data Analyzer**: Analyze and visualize simple datasets

## Conclusion

Congratulations on completing the Ruby Basics Workshop! You now have a solid foundation in Ruby programming. Continue practicing and exploring the Ruby ecosystem to become a proficient Ruby developer.

Remember: The best way to learn programming is by building things. Start small, keep coding, and don't be afraid to make mistakes!
