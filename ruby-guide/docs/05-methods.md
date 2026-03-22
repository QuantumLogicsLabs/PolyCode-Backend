# Methods and Functions in Ruby

## Overview

In Ruby, methods are the primary way to organize and reuse code. Everything in Ruby is an object, and methods are how objects communicate and perform actions.

## Defining Methods

### Basic Method Definition

```ruby
def greet
  "Hello, World!"
end

puts greet  # => "Hello, World!"
```

### Method with Parameters

```ruby
def greet_person(name)
  "Hello, #{name}!"
end

puts greet_person("Alice")  # => "Hello, Alice!"
puts greet_person("Bob")    # => "Hello, Bob!"
```

### Method with Multiple Parameters

```ruby
def create_full_name(first_name, last_name)
  "#{first_name} #{last_name}"
end

puts create_full_name("John", "Doe")  # => "John Doe"
```

### Method with Default Parameters

```ruby
def greet_with_default(name = "Guest")
  "Hello, #{name}!"
end

puts greet_with_default          # => "Hello, Guest!"
puts greet_with_default("Alice")  # => "Hello, Alice!"
```

### Method with Variable Number of Arguments

```ruby
def sum(*numbers)
  numbers.reduce(:+)
end

puts sum(1, 2, 3)        # => 6
puts sum(1, 2, 3, 4, 5)  # => 15
puts sum                 # => 0
```

### Method with Keyword Arguments

```ruby
def create_user(name:, age:, email: nil, city: "Unknown")
  user = { name: name, age: age, city: city }
  user[:email] = email if email
  user
end

user1 = create_user(name: "Alice", age: 25, email: "alice@example.com")
user2 = create_user(name: "Bob", age: 30)

puts user1.inspect  # => {:name=>"Alice", :age=>25, :city=>"Unknown", :email=>"alice@example.com"}
puts user2.inspect  # => {:name=>"Bob", :age=>30, :city=>"Unknown"}
```

### Method with Mixed Arguments

```ruby
def complex_method(required, optional = "default", *args, keyword:, **options)
  {
    required: required,
    optional: optional,
    args: args,
    keyword: keyword,
    options: options
  }
end

result = complex_method(
  "req_value",
  "opt_value",
  "arg1", "arg2",
  keyword: "key_value",
  opt1: "value1",
  opt2: "value2"
)

puts result.inspect
# => {:required=>"req_value", :optional=>"opt_value", :args=>["arg1", "arg2"], :keyword=>"key_value", :options=>{:opt1=>"value1", :opt2=>"value2"}}
```

## Method Return Values

### Implicit Return

Ruby methods automatically return the value of the last evaluated expression.

```ruby
def add(a, b)
  a + b  # This value is returned automatically
end

puts add(2, 3)  # => 5
```

### Explicit Return

You can use the `return` keyword to exit a method early.

```ruby
def find_first_even(numbers)
  numbers.each do |number|
    return number if number.even?
  end
  nil  # Return nil if no even number found
end

puts find_first_even([1, 3, 5, 2, 4])  # => 2
puts find_first_even([1, 3, 5])         # => nil
```

### Multiple Return Values

```ruby
def min_max(numbers)
  [numbers.min, numbers.max]
end

min_val, max_val = min_max([3, 1, 4, 1, 5, 9])
puts "Min: #{min_val}, Max: #{max_val}"  # => Min: 1, Max: 9
```

## Method Naming Conventions

### Conventional Method Names

```ruby
# Regular methods
def calculate_total
  # implementation
end

# Predicate methods (return boolean, end with ?)
def empty?
  # implementation
end

# Destructive methods (modify object, end with !)
def sort!
  # implementation
end

# Setter methods (end with =)
def name=(new_name)
  # implementation
end
```

### Method Aliases

```ruby
class Array
  alias old_each each
  
  def each
    puts "Starting iteration"
    old_each { |item| yield item }
    puts "Iteration complete"
  end
end

[1, 2, 3].each { |n| puts n }
```

## Blocks, Procs, and Lambdas

### Blocks

Blocks are anonymous functions that can be passed to methods.

```ruby
# Using yield
def twice
  yield
  yield
end

twice { puts "Hello!" }

# Block with parameter
def process_numbers(numbers)
  numbers.each { |number| yield number }
end

process_numbers([1, 2, 3]) { |n| puts n * 2 }

# Checking if block is given
def conditional_yield
  yield if block_given?
end

conditional_yield { puts "Block was given" }
conditional_yield  # Does nothing
```

### Procs

Procs are objects that can store blocks of code.

```ruby
# Creating a proc
add_proc = Proc.new { |a, b| a + b }
multiply_proc = proc { |a, b| a * b }

puts add_proc.call(2, 3)        # => 5
puts multiply_proc.call(4, 5)   # => 20

# Using procs as method arguments
def apply_operation(a, b, operation)
  operation.call(a, b)
end

puts apply_operation(10, 5, add_proc)       # => 15
puts apply_operation(10, 5, multiply_proc)   # => 50
```

### Lambdas

Lambdas are similar to procs but have stricter argument checking.

```ruby
# Creating a lambda
add_lambda = lambda { |a, b| a + b }
multiply_lambda = ->(a, b) { a * b }  # Shorthand syntax

puts add_lambda.call(2, 3)      # => 5
puts multiply_lambda.(4, 5)    # => 50

# Difference between proc and lambda
def test_proc
  p = Proc.new { return "Proc return" }
  p.call
  "After proc call"
end

def test_lambda
  l = lambda { return "Lambda return" }
  l.call
  "After lambda call"
end

puts test_proc     # => "Proc return"
puts test_lambda   # => "After lambda call"
```

## Method Visibility

### Public Methods

Public methods can be called from anywhere.

```ruby
class MyClass
  def public_method
    "This is public"
  end
end

obj = MyClass.new
puts obj.public_method  # => "This is public"
```

### Private Methods

Private methods can only be called within the class.

```ruby
class MyClass
  def public_method
    private_method
  end

  private

  def private_method
    "This is private"
  end
end

obj = MyClass.new
puts obj.public_method   # => "This is private"
# puts obj.private_method  # => NoMethodError: private method `private_method' called
```

### Protected Methods

Protected methods can be called by instances of the same class.

```ruby
class Person
  def initialize(name)
    @name = name
  end

  def compare_names(other_person)
    # Can call protected method on other instance
    name == other_person.name
  end

  protected

  def name
    @name
  end
end

person1 = Person.new("Alice")
person2 = Person.new("Bob")
person3 = Person.new("Alice")

puts person1.compare_names(person2)  # => false
puts person1.compare_names(person3)  # => true
```

## Class Methods

### Defining Class Methods

```ruby
class Calculator
  def self.add(a, b)
    a + b
  end

  class << self
    def multiply(a, b)
      a * b
    end
  end

  def Calculator.divide(a, b)
    a / b.to_f
  end
end

puts Calculator.add(2, 3)      # => 5
puts Calculator.multiply(4, 5) # => 20
puts Calculator.divide(10, 3) # => 3.3333333333333335
```

## Method Missing

Ruby provides `method_missing` to handle calls to undefined methods.

```ruby
class DynamicMethodHandler
  def method_missing(method_name, *args)
    if method_name.to_s.start_with?("say_")
      phrase = method_name.to_s.sub("say_", "")
      phrase.gsub!("_", " ")
      "#{phrase.capitalize}!"
    else
      super
    end
  end

  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.start_with?("say_") || super
  end
end

handler = DynamicMethodHandler.new
puts handler.say_hello        # => "Hello!"
puts handler.say_good_morning # => "Good morning!"
# puts handler.unknown_method # => NoMethodError
```

## Practical Examples

### Example 1: String Utilities

```ruby
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

puts StringUtilities.capitalize_words("hello world")  # => "Hello World"
puts StringUtilities.truncate("This is a long string", 10)  # => "This is..."
puts StringUtilities.is_palindrome?("A man, a plan, a canal: Panama")  # => true
```

### Example 2: Array Statistics

```ruby
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

  def standard_deviation
    return 0 if @numbers.empty?
    
    m = mean
    variance = @numbers.map { |n| (n - m) ** 2 }.sum / @numbers.length
    Math.sqrt(variance)
  end
end

stats = ArrayStatistics.new([1, 2, 2, 3, 4, 4, 4, 5])
puts "Mean: #{stats.mean}"                    # => 3.125
puts "Median: #{stats.median}"                # => 3.5
puts "Mode: #{stats.mode}"                    # => [4]
puts "Standard Deviation: #{stats.standard_deviation}"  # => 1.247...
```

### Example 3: Method Chaining

```ruby
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

  def divide(value)
    @number /= value.to_f
    self
  end

  def result
    @number
  end

  def to_s
    @number.to_s
  end
end

result = NumberProcessor.new(10)
  .add(5)
  .multiply(2)
  .subtract(10)
  .divide(3)

puts result.result  # => 6.666666666666667
```

## Method Documentation

### Documenting Methods

```ruby
# Calculates the area of a circle
#
# @param radius [Numeric] the radius of the circle
# @return [Float] the area of the circle
# @example
#   circle_area(5)  # => 78.53981633974483
def circle_area(radius)
  Math::PI * radius ** 2
end

# Validates an email address
#
# @param email [String] the email to validate
# @return [Boolean] true if valid, false otherwise
# @raise [ArgumentError] if email is not a string
def valid_email?(email)
  raise ArgumentError, "Email must be a string" unless email.is_a?(String)
  
  email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
end
```

## Performance Considerations

### Method Call Overhead

```ruby
# Method calls have some overhead
def slow_method
  1000000.times { |i| i + 1 }
end

# Using blocks can be faster
def fast_method
  1000000.times(&:+)
end
```

### Memoization

```ruby
class ExpensiveCalculation
  def expensive_operation(x)
    @cache ||= {}
    @cache[x] ||= compute_expensive_value(x)
  end

  private

  def compute_expensive_value(x)
    # Simulate expensive computation
    sleep(0.1)
    x ** 2
  end
end
```

## Best Practices

### 1. Use Descriptive Method Names

```ruby
# Good
def calculate_monthly_interest(principal, rate, months)
  # implementation
end

# Bad
def calc(p, r, m)
  # implementation
end
```

### 2. Keep Methods Small

```ruby
# Good
def process_order(order)
  validate_order(order)
  calculate_total(order)
  send_confirmation(order)
end

# Bad
def process_order(order)
  # 50 lines of code doing everything
end
```

### 3. Use Default Arguments Wisely

```ruby
# Good
def connect(host = "localhost", port = 8080, timeout = 30)
  # implementation
end

# Avoid too many optional parameters
```

### 4. Handle Edge Cases

```ruby
def divide(a, b)
  raise ArgumentError, "Cannot divide by zero" if b.zero?
  a / b.to_f
end
```

## Practice Exercises

### Exercise 1: String Manipulator
Create a class with methods to:
- Reverse a string
- Count vowels
- Remove duplicates
- Check if it's a palindrome

### Exercise 2: Number Calculator
Build a calculator class with methods for:
- Basic arithmetic operations
- Method chaining support
- History of operations

### Exercise 3: Array Processor
Write methods to:
- Find unique elements
- Group by condition
- Transform elements
- Filter and map simultaneously

### Exercise 4: Method Missing
Create a class that uses `method_missing` to:
- Handle dynamic method calls
- Generate methods on the fly
- Provide meaningful error messages

---

**Ready to explore object-oriented programming in Ruby? Let's continue! 🏗️**
