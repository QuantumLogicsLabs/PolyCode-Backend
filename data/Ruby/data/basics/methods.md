# Ruby Methods and Functions

## Method Definition

### Basic Method Syntax
```ruby
# Simple method definition
def greet
  puts "Hello, World!"
end

# Method with parameters
def greet_person(name)
  puts "Hello, #{name}!"
end

# Method with default parameters
def greet_with_default(name = "Guest")
  puts "Hello, #{name}!"
end

# Method with multiple parameters
def calculate_sum(a, b)
  a + b
end

# Method with keyword arguments
def create_user(name:, age:, email:)
  "User: #{name}, Age: #{age}, Email: #{email}"
end

# Method with optional keyword arguments
def process_data(data:, timeout: 30, retries: 3)
  "Processing #{data} with timeout: #{timeout}, retries: #{retries}"
end

# Method with mixed parameters
def complex_method(required, optional = "default", keyword:)
  "Required: #{required}, Optional: #{optional}, Keyword: #{keyword}"
end
```

### Method Return Values
```ruby
# Implicit return (last expression)
def add_numbers(a, b)
  a + b  # This value is returned automatically
end

# Explicit return
def multiply_numbers(a, b)
  return a * b
end

# Multiple return values
def get_coordinates
  x = 10
  y = 20
  [x, y]  # Returns array
end

# Conditional return
def get_grade(score)
  return "A" if score >= 90
  return "B" if score >= 80
  return "C" if score >= 70
  return "D" if score >= 60
  "F"
end

# Early return
def process_user(user)
  return "Invalid user" unless user
  
  return "User is inactive" unless user.active?
  
  "User #{user.name} processed successfully"
end

# Return nil explicitly
def do_nothing
  return nil
end
```

### Method Parameters

#### Required Parameters
```ruby
# All parameters are required by default
def full_name(first_name, last_name)
  "#{first_name} #{last_name}"
end

full_name("John", "Doe")  # "John Doe"
full_name("John")         # ArgumentError: wrong number of arguments
```

#### Optional Parameters
```ruby
# Default values
def greet(name = "Guest", greeting = "Hello")
  "#{greeting}, #{name}!"
end

greet                    # "Hello, Guest!"
greet("Alice")          # "Hello, Alice!"
greet("Bob", "Hi")      # "Hi, Bob!"

# Optional parameters with defaults
def create_file(filename, content = "", mode = "w")
  File.open(filename, mode) { |f| f.write(content) }
end
```

#### Variable Arguments
```ruby
# Splat operator for variable number of arguments
def sum(*numbers)
  numbers.sum
end

sum(1, 2, 3, 4, 5)  # 15
sum(10, 20)         # 30
sum()               # 0

# Mixed with regular parameters
def process_data(format, *data_items)
  puts "Format: #{format}"
  data_items.each_with_index do |item, index|
    puts "Item #{index + 1}: #{item}"
  end
end

process_data("json", {name: "John"}, {name: "Jane"})

# Double splat for keyword arguments
def print_info(**info)
  info.each do |key, value|
    puts "#{key}: #{value}"
  end
end

print_info(name: "John", age: 30, city: "NYC")
```

#### Keyword Arguments
```ruby
# Required keyword arguments
def create_person(name:, age:, email:)
  "#{name} (#{age}) - #{email}"
end

create_person(name: "John", age: 30, email: "john@example.com")

# Optional keyword arguments
def configure_system(timeout: 30, retries: 3, debug: false)
  "Timeout: #{timeout}, Retries: #{retries}, Debug: #{debug}"
end

configure_system()
configure_system(timeout: 60)
configure_system(debug: true, retries: 5)
```

#### Mixed Parameter Types
```ruby
# Combining different parameter types
def complex_method(required1, required2, optional = "default", *args, keyword1:, keyword2: "default", **kwargs)
  puts "Required: #{required1}, #{required2}"
  puts "Optional: #{optional}"
  puts "Args: #{args.inspect}"
  puts "Keyword1: #{keyword1}"
  puts "Keyword2: #{keyword2}"
  puts "Kwargs: #{kwargs.inspect}"
end

complex_method("a", "b", "c", 1, 2, 3, keyword1: "value", extra: "data")
```

## Method Visibility

### Public, Private, and Protected Methods
```ruby
class MyClass
  # Public method (default)
  def public_method
    "This is public"
  end
  
  # Private method
  private
  
  def private_method
    "This is private"
  end
  
  # Protected method
  protected
  
  def protected_method
    "This is protected"
  end
end

# Usage
obj = MyClass.new
obj.public_method      # "This is public"
obj.private_method    # NoMethodError: private method `private_method'
obj.protected_method  # NoMethodError: protected method `protected_method'
```

### Visibility Modifiers
```ruby
class VisibilityExample
  # Public methods
  def method1
    "Public method 1"
  end
  
  def method2
    "Public method 2"
  end
  
  # Make following methods private
  private
  
  def method3
    "Private method 3"
  end
  
  def method4
    "Private method 4"
  end
  
  # Make following methods protected
  protected
  
  def method5
    "Protected method 5"
  end
  
  def method6
    "Protected method 6"
  end
  
  # Make following methods public again
  public
  
  def method7
    "Public method 7"
  end
end
```

### Private Class Methods
```ruby
class MyClass
  def self.public_class_method
    "Public class method"
  end
  
  private
  
  def self.private_class_method
    "Private class method"
  end
end

MyClass.public_class_method    # "Public class method"
MyClass.private_class_method  # NoMethodError: private method `private_class_method'
```

### Singleton Methods
```ruby
# Define method on specific object
obj = "Hello"
def obj.shout
  upcase + "!"
end

obj.shout  # "HELLO!"

# Define singleton method using class << self
class MyClass
  class << self
    def singleton_method
      "This is a singleton method"
    end
  end
end

MyClass.singleton_method  # "This is a singleton method"
```

## Advanced Method Features

### Method Aliasing
```ruby
class Calculator
  def add(a, b)
    a + b
  end
  
  # Create alias
  alias plus add
  
  # Create alias with symbol
  alias_method :sum, :add
end

calc = Calculator.new
calc.plus(5, 3)  # 8
calc.sum(10, 2)  # 12
```

### Method Missing
```ruby
class DynamicMethods
  def method_missing(method_name, *args)
    puts "Method #{method_name} called with args: #{args.inspect}"
    
    # Define the method for future calls
    define_singleton_method(method_name) do |*method_args|
      puts "Now defined: #{method_name} with args: #{method_args.inspect}"
    end
    
    # Call the newly defined method
    send(method_name, *args)
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.start_with?('dynamic_')
  end
end

obj = DynamicMethods.new
obj.dynamic_test(1, 2, 3)  # Creates and calls the method
obj.dynamic_test(4, 5, 6)  # Uses the defined method
```

### Define Method
```ruby
class DynamicMethodExample
  # Define method dynamically
  define_method :greet do |name|
    "Hello, #{name}!"
  end
  
  # Define method with parameters
  define_method(:calculate_area) do |width, height|
    width * height
  end
  
  # Define class method dynamically
  define_singleton_method(:version) do
    "1.0.0"
  end
end

obj = DynamicMethodExample.new
obj.greet("World")        # "Hello, World!"
obj.calculate_area(5, 4)   # 20
DynamicMethodExample.version  # "1.0.0"
```

### Method Chaining
```ruby
class NumberChain
  def initialize(value)
    @value = value
  end
  
  def add(number)
    @value += number
    self  # Return self to enable chaining
  end
  
  def multiply(number)
    @value *= number
    self  # Return self to enable chaining
  end
  
  def result
    @value
  end
end

# Usage
result = NumberChain.new(10)
                .add(5)
                .multiply(2)
                .add(3)
                .result  # 36
```

## Functional Programming in Ruby

### Blocks and Procs
```ruby
# Block as parameter
def process_array(array)
  array.each { |item| puts item }
end

process_array([1, 2, 3, 4, 5])

# Block with yield
def greet_people(names)
  names.each { |name| yield name }
end

greet_people(["Alice", "Bob", "Charlie"]) do |name|
  puts "Hello, #{name}!"
end

# Converting block to Proc
def method_with_block(&block)
  block.call("Hello from Proc")
end

method_with_block { |message| puts message }

# Creating Proc objects
add_proc = Proc.new { |a, b| a + b }
multiply_proc = proc { |a, b| a * b }

add_proc.call(5, 3)        # 8
multiply_proc.call(4, 6)   # 24
```

### Lambdas
```ruby
# Creating lambda
add_lambda = lambda { |a, b| a + b }
multiply_lambda = ->(a, b) { a * b }

add_lambda.call(5, 3)      # 8
multiply_lambda.call(4, 6) # 24

# Lambda vs Proc differences
# 1. Argument checking
proc_example = proc { |a, b| a + b }
lambda_example = lambda { |a, b| a + b }

proc_example.call(1)        # No error, b is nil
# lambda_example.call(1)   # ArgumentError: wrong number of arguments

# 2. Return behavior
def proc_method
  proc = proc { return "from proc" }
  proc.call
  "after proc"
end

def lambda_method
  lambda = lambda { return "from lambda" }
  lambda.call
  "after lambda"
end

proc_method    # "from proc"
lambda_method  # "after lambda"
```

### Higher-Order Methods
```ruby
# Method that accepts a block
def transform_numbers(numbers, &transformer)
  numbers.map(&transformer)
end

# Usage
doubled = transform_numbers([1, 2, 3, 4, 5]) { |n| n * 2 }
squared = transform_numbers([1, 2, 3, 4, 5]) { |n| n ** 2 }

# Method that returns a lambda
def create_multiplier(factor)
  lambda { |number| number * factor }
end

double = create_multiplier(2)
triple = create_multiplier(3)

double.call(10)  # 20
triple.call(10)  # 30

# Method composition
def compose(f, g)
  lambda { |x| f.call(g.call(x)) }
end

add_one = ->(x) { x + 1 }
multiply_by_two = ->(x) { x * 2 }

add_then_multiply = compose(multiply_by_two, add_one)
multiply_then_add = compose(add_one, multiply_by_two)

add_then_multiply.call(5)   # 12
multiply_then_add.call(5)  # 11
```

## Method Introspection

### Method Information
```ruby
class MethodInfo
  def instance_method
    "instance method"
  end
  
  def self.class_method
    "class method"
  end
  
  private
  
  def private_method
    "private method"
  end
end

# Get method information
obj = MethodInfo.new

# Method objects
instance_method_obj = obj.method(:instance_method)
class_method_obj = MethodInfo.method(:class_method)

puts instance_method_obj.name      # :instance_method
puts instance_method_obj.owner     # MethodInfo
puts instance_method_obj.arity     # 0
puts instance_method_obj.parameters # []

# Method list
puts MethodInfo.instance_methods(false)  # [:instance_method]
puts MethodInfo.methods(false)           # [:class_method]
puts MethodInfo.private_instance_methods(false) # [:private_method]

# Check method existence
obj.respond_to?(:instance_method)   # true
obj.respond_to?(:private_method)    # false
MethodInfo.respond_to?(:class_method) # true
```

### Method Aliases and Overrides
```ruby
class OverrideExample
  def original_method
    "Original implementation"
  end
  
  # Override method
  def original_method
    "Overridden implementation"
  end
  
  # Call original method using alias
  alias_method :original_implementation, :original_method
  
  def original_method
    "New implementation, but can call: #{original_implementation}"
  end
  
  # Use super to call parent method
  def parent_method
    "Child implementation"
  end
end

class ChildClass < OverrideExample
  def parent_method
    "Child: " + super
  end
end
```

## Best Practices

### Method Design
```ruby
# Good: Single responsibility
class UserValidator
  def valid_email?(email)
    email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
  end
  
  def valid_age?(age)
    age.is_a?(Integer) && age.between?(0, 150)
  end
end

# Good: Descriptive method names
def calculate_user_age(birth_date)
  ((Time.now - birth_date) / 365.25).to_i
end

def is_admin_user?(user)
  user.role == 'admin'
end

# Good: Default parameter values
def send_notification(message, priority = :normal, delay = 0)
  # Implementation
end

# Bad: Too many parameters
def create_user(name, email, age, address, phone, role, department, salary)
  # Too many parameters - consider using options hash
end

# Good: Use options hash for many parameters
def create_user(name:, email:, age: nil, address: nil, phone: nil, role: :user, department: nil, salary: nil)
  user = { name: name, email: email }
  user[:age] = age if age
  user[:address] = address if address
  user[:phone] = phone if phone
  user[:role] = role
  user[:department] = department if department
  user[:salary] = salary if salary
  user
end
```

### Error Handling
```ruby
# Good: Validate inputs
def divide_numbers(numerator, denominator)
  raise ArgumentError, "Denominator cannot be zero" if denominator == 0
  numerator / denominator
end

# Good: Handle exceptions gracefully
def safe_divide(numerator, denominator)
  numerator / denominator
rescue ZeroDivisionError
  0  # Return default value
rescue => e
  puts "Error: #{e.message}"
  nil
end

# Good: Use custom exceptions
class InvalidUserError < StandardError; end

def create_user(name, email)
  raise InvalidUserError, "Invalid name" if name.nil? || name.empty?
  raise InvalidUserError, "Invalid email" unless valid_email?(email)
  
  User.new(name: name, email: email)
end
```

### Performance Considerations
```ruby
# Good: Use symbols for frequently used method names
def call_method_dynamically(object, method_name)
  object.send(method_name) if object.respond_to?(method_name)
end

# Better: Cache method lookups
class MethodCache
  def initialize
    @method_cache = {}
  end
  
  def call_method(object, method_name)
    method = @method_cache[object.class] ||= {}
    method[method_name] ||= object.method(method_name)
    method.call
  end
end

# Good: Avoid unnecessary method calls
def expensive_calculation
  @cached_result ||= perform_expensive_calculation
end

# Good: Use blocks for resource management
def process_file(filename)
  File.open(filename, 'r') do |file|
    file.each_line do |line|
      yield line.strip
    end
  end  # File automatically closed
end
```

### Documentation
```ruby
# Good: Document methods with comments
# Calculates the area of a rectangle
#
# @param width [Integer] the width of the rectangle
# @param height [Integer] the height of the rectangle
# @return [Integer] the area of the rectangle
# @raise [ArgumentError] if width or height is negative
def rectangle_area(width, height)
  raise ArgumentError, "Width cannot be negative" if width < 0
  raise ArgumentError, "Height cannot be negative" if height < 0
  
  width * height
end

# Good: Use YARD documentation format
# Represents a user in the system
#
# @attr_reader [String] name the user's name
# @attr_reader [String] email the user's email
# @attr_reader [Integer] age the user's age
class User
  attr_reader :name, :email, :age
  
  # Creates a new user
  #
  # @param name [String] the user's name
  # @param email [String] the user's email
  # @param age [Integer] the user's age
  def initialize(name:, email:, age:)
    @name = name
    @email = email
    @age = age
  end
  
  # Returns whether the user is an adult
  #
  # @return [Boolean] true if the user is 18 or older
  def adult?
    @age >= 18
  end
end
```

## Common Pitfalls

### Method Definition Issues
```ruby
# Pitfall: Method name conflicts with built-in methods
class String
  def length
    100  # This will override the built-in length method
  end
end

# Better: Use different name or alias original method
class String
  alias_method :original_length, :length
  
  def length
    100
  end
end

# Pitfall: Forgetting return keyword in conditional methods
def find_user(users, id)
  users.each do |user|
    return user if user.id == id
  end
  # Missing return nil here
end

# Better: Explicit return
def find_user(users, id)
  users.each do |user|
    return user if user.id == id
  end
  nil  # Explicit return
end

# Pitfall: Mutable default arguments
def add_item(items = [])
  items << "new_item"
  items
end

list1 = add_item  # ["new_item"]
list2 = add_item  # ["new_item", "new_item"] - shares the same array!

# Better: Use nil default and create new object
def add_item(items = nil)
  items ||= []
  items << "new_item"
  items
end

# Pitfall: Method shadowing
class Example
  def calculate
    "First implementation"
  end
  
  def calculate
    calculate = "Local variable"  # This shadows the method
    calculate
  end
end

# Better: Use different variable name
class Example
  def calculate
    "First implementation"
  end
  
  def calculate
    result = "Local variable"
    result
  end
end
```

### Block and Proc Issues
```ruby
# Pitfall: Not handling block correctly
def process_data(data)
  data.each { |item| yield item } if block_given?
end

process_data([1, 2, 3])  # No block given - no error, but no processing

# Better: Require block or provide default behavior
def process_data(data, &block)
  if block_given?
    data.each(&block)
  else
    data.each { |item| puts "Processing: #{item}" }
  end
end

# Pitfall: Proc vs lambda return behavior
def test_method
  my_proc = proc { return "from proc" }
  my_lambda = lambda { return "from lambda" }
  
  result1 = my_proc.call
  result2 = my_lambda.call
  
  "method end"  # Never reached due to proc return
end

# Better: Understand the difference or use lambda consistently
def test_method
  my_lambda = lambda { return "from lambda" }
  result = my_lambda.call
  "method end: #{result}"
end
```

### Method Visibility Issues
```ruby
# Pitfall: Incorrect visibility setup
class VisibilityExample
  private
  
  def method1
    "Private method 1"
  end
  
  public  # This makes all following methods public
  
  def method2
    "Public method 2"
  end
  
  def method3
    "Public method 3"
  end
end

# Better: Group visibility properly
class VisibilityExample
  def method1
    "Public method 1"
  end
  
  def method2
    "Public method 2"
  end
  
  private
  
  def method3
    "Private method 3"
  end
end

# Pitfall: Trying to call private method from outside
class Example
  private
  
  def secret_method
    "Secret"
  end
end

obj = Example.new
obj.secret_method  # NoMethodError: private method `secret_method'

# Better: Use public interface or send (with caution)
class Example
  def public_interface
    secret_method
  end
  
  private
  
  def secret_method
    "Secret"
  end
end
```

## Summary

Ruby methods provide:

**Method Definition:**
- Basic syntax with def/end
- Parameter types (required, optional, variable, keyword)
- Default parameter values
- Return values (implicit and explicit)
- Multiple return values

**Method Visibility:**
- Public methods (default)
- Private methods (internal use)
- Protected methods (class hierarchy)
- Class methods and singleton methods

**Advanced Features:**
- Method aliasing and overriding
- Dynamic method definition
- Method missing and respond_to_missing?
- Method chaining
- Method introspection

**Functional Programming:**
- Blocks and yield
- Procs and lambdas
- Higher-order methods
- Method composition

**Best Practices:**
- Single responsibility principle
- Descriptive naming conventions
- Proper error handling
- Performance optimization
- Documentation standards

**Common Pitfalls:**
- Method name conflicts
- Mutable default arguments
- Method shadowing
- Block vs lambda confusion
- Visibility issues

Ruby's flexible method system supports both object-oriented and functional programming paradigms, enabling expressive and maintainable code when used with proper design principles.
