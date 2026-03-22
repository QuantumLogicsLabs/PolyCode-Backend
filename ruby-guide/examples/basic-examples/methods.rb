# Methods Examples
# Demonstrating method definition, parameters, and advanced concepts in Ruby

puts "=== BASIC METHOD DEFINITION ==="

# Simple method
def greet
  "Hello, World!"
end

puts "Greeting: #{greet}"

# Method with parameters
def greet_person(name)
  "Hello, #{name}!"
end

puts "Personal greeting: #{greet_person('Alice')}"

# Method with multiple parameters
def create_full_name(first_name, last_name)
  "#{first_name} #{last_name}"
end

puts "Full name: #{create_full_name('John', 'Doe')}"

# Method with default parameters
def greet_with_default(name = "Guest")
  "Hello, #{name}!"
end

puts "Default greeting: #{greet_with_default}"
puts "Named greeting: #{greet_with_default('Bob')}"

puts "\n=== VARIABLE ARGUMENTS ==="

# Variable number of arguments
def sum(*numbers)
  numbers.reduce(:+)
end

puts "Sum of 1,2,3: #{sum(1, 2, 3)}"
puts "Sum of 1,2,3,4,5: #{sum(1, 2, 3, 4, 5)}"
puts "Sum of no numbers: #{sum}"

# Mixed arguments
def describe_person(name, age, *hobbies)
  "#{name} is #{age} years old and enjoys #{hobbies.join(', ')}"
end

puts describe_person("Alice", 25, "reading", "swimming", "coding")

puts "\n=== KEYWORD ARGUMENTS ==="

# Required keyword arguments
def create_user(name:, age:)
  "User: #{name}, Age: #{age}"
end

puts create_user(name: "John", age: 30)

# Optional keyword arguments
def create_profile(name:, age:, city: "Unknown", email: nil)
  profile = "Name: #{name}, Age: #{age}, City: #{city}"
  profile += ", Email: #{email}" if email
  profile
end

puts create_profile(name: "Alice", age: 25)
puts create_profile(name: "Bob", age: 30, city: "New York", email: "bob@example.com")

# Double splat for keyword arguments
def process_data(**data)
  "Processing: #{data.keys.join(', ')}"
end

puts process_data(name: "John", age: 30, active: true)

puts "\n=== RETURN VALUES ==="

# Implicit return
def add(a, b)
  a + b
end

puts "Add 2 + 3: #{add(2, 3)}"

# Explicit return
def find_first_even(numbers)
  numbers.each do |number|
    return number if number.even?
  end
  nil
end

puts "First even in [1,3,5,2,4]: #{find_first_even([1, 3, 5, 2, 4])}"
puts "First even in [1,3,5]: #{find_first_even([1, 3, 5])}"

# Multiple return values
def min_max(numbers)
  [numbers.min, numbers.max]
end

min_val, max_val = min_max([3, 1, 4, 1, 5, 9])
puts "Min: #{min_val}, Max: #{max_val}"

puts "\n=== METHOD NAMING CONVENTIONS ==="

# Predicate methods (end with ?)
def even?(number)
  number.even?
end

def empty?(collection)
  collection.empty?
end

puts "Is 4 even? #{even?(4)}"
puts "Is 3 even? #{even?(3)}"
puts "Is [] empty? #{empty?([])}"
puts "Is [1] empty? #{empty?([1])}"

# Destructive methods (end with !)
def sort!(array)
  array.sort!
  array
end

numbers = [3, 1, 4, 1, 5]
puts "Original: #{numbers}"
sort!(numbers)
puts "After sort!: #{numbers}"

# Setter methods (end with =)
class Person
  def name
    @name
  end

  def name=(new_name)
    @name = new_name
  end
end

person = Person.new
person.name = "Alice"
puts "Person's name: #{person.name}"

puts "\n=== BLOCKS, PROCS, AND LAMBDAS ==="

# Using blocks with yield
def twice
  yield
  yield
end

puts "Calling twice:"
twice { puts "Hello!" }

# Block with parameters
def process_numbers(numbers)
  numbers.each { |number| yield number }
end

puts "Processing numbers:"
process_numbers([1, 2, 3]) { |n| puts n * 2 }

# Procs
add_proc = Proc.new { |a, b| a + b }
multiply_proc = proc { |a, b| a * b }

puts "Proc add 2 + 3: #{add_proc.call(2, 3)}"
puts "Proc multiply 4 * 5: #{multiply_proc.call(4, 5)}"

# Using procs as method arguments
def apply_operation(a, b, operation)
  operation.call(a, b)
end

puts "Apply operation: #{apply_operation(10, 5, add_proc)}"
puts "Apply operation: #{apply_operation(10, 5, multiply_proc)}"

# Lambdas
add_lambda = lambda { |a, b| a + b }
multiply_lambda = ->(a, b) { a * b }

puts "Lambda add 2 + 3: #{add_lambda.call(2, 3)}"
puts "Lambda multiply 4 * 5: #{multiply_lambda.(4, 5)}"

puts "\n=== METHOD VISIBILITY ==="

class VisibilityExample
  def public_method
    "Public method calling private method: #{private_method}"
  end

  private

  def private_method
    "This is private"
  end
end

example = VisibilityExample.new
puts example.public_method

puts "\n=== CLASS METHODS ==="

class Calculator
  def self.add(a, b)
    a + b
  end

  def self.multiply(a, b)
    a * b
  end

  def self.divide(a, b)
    a / b.to_f
  end
end

puts "Calculator add 2 + 3: #{Calculator.add(2, 3)}"
puts "Calculator multiply 4 * 5: #{Calculator.multiply(4, 5)}"
puts "Calculator divide 10 / 3: #{Calculator.divide(10, 3)}"

puts "\n=== METHOD CHAINING ==="

class NumberProcessor
  def initialize(number)
    @number = number
  end

  def add(value)
    @number += value
    self
  end

  def multiply(value)
    @number *= value
    self
  end

  def subtract(value)
    @number -= value
    self
  end

  def result
    @number
  end
end

result = NumberProcessor.new(10)
  .add(5)
  .multiply(2)
  .subtract(10)

puts "Method chaining result: #{result.result}"

puts "\n=== PRACTICAL EXAMPLES ==="

# String utilities
class StringUtilities
  def self.capitalize_words(string)
    string.split.map(&:capitalize).join(' ')
  end

  def self.truncate(string, length)
    return string if string.length <= length
    string[0...length-3] + "..."
  end

  def self.is_palindrome?(string)
    cleaned = string.downcase.gsub(/[^a-z0-9]/, '')
    cleaned == cleaned.reverse
  end
end

puts "Capitalize words: #{StringUtilities.capitalize_words('hello world')}"
puts "Truncate: #{StringUtilities.truncate('This is a long string', 10)}"
puts "Is palindrome: #{StringUtilities.is_palindrome?('A man, a plan, a canal: Panama')}"

# Array statistics
class ArrayStatistics
  def initialize(numbers)
    @numbers = numbers
  end

  def mean
    return 0 if @numbers.empty?
    @numbers.sum.to_f / @numbers.length
  end

  def median
    return 0 if @numbers.empty?
    
    sorted = @numbers.sort
    mid = sorted.length / 2
    
    if sorted.length.odd?
      sorted[mid]
    else
      (sorted[mid - 1] + sorted[mid]) / 2.0
    end
  end

  def mode
    return nil if @numbers.empty?
    
    frequency = @numbers.group_by(&:itself).transform_values(&:count)
    max_freq = frequency.values.max
    frequency.select { |_, v| v == max_freq }.keys
  end
end

stats = ArrayStatistics.new([1, 2, 2, 3, 4, 4, 4, 5])
puts "Mean: #{stats.mean}"
puts "Median: #{stats.median}"
puts "Mode: #{stats.mode}"

# Recursive method
def factorial(n)
  return 1 if n <= 1
  n * factorial(n - 1)
end

puts "Factorial of 5: #{factorial(5)}"
puts "Factorial of 6: #{factorial(6)}"

# Fibonacci sequence
def fibonacci(n)
  return 0 if n == 0
  return 1 if n == 1
  fibonacci(n - 1) + fibonacci(n - 2)
end

puts "Fibonacci sequence (first 10):"
(0..9).each { |i| print "#{fibonacci(i)} " }
puts
