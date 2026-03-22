# Basic Ruby Syntax

## Comments

Comments are ignored by the Ruby interpreter and are used to document your code.

### Single-line Comments
```ruby
# This is a single-line comment
puts "Hello, World!"  # Comment after code
```

### Multi-line Comments
```ruby
=begin
This is a multi-line comment
that spans multiple lines.
=end

puts "Code continues here"
```

## Variables and Assignment

### Variable Assignment
```ruby
name = "John"        # String
age = 25            # Integer
height = 5.8        # Float
is_student = true   # Boolean
```

### Multiple Assignment
```ruby
# Parallel assignment
a, b, c = 1, 2, 3
puts a, b, c        # => 1 2 3

# Swapping variables
x, y = 10, 20
x, y = y, x         # x = 20, y = 10

# Assignment with splat operator
first, *rest = [1, 2, 3, 4, 5]
puts first          # => 1
puts rest.inspect   # => [2, 3, 4, 5]
```

## Literals

### Numeric Literals
```ruby
# Integers
42          # Decimal
0b1010      # Binary (10)
0o52        # Octal (42)
0x2A        # Hexadecimal (42)

# Floats
3.14        # Float
1.5e2       # Scientific notation (150.0)
```

### String Literals
```ruby
# Single quotes - no interpolation
puts 'Hello, World!'
puts 'Path: C:\\Users\\John'

# Double quotes - with interpolation
name = "Alice"
puts "Hello, #{name}!"      # => Hello, Alice!
puts "2 + 2 = #{2 + 2}"     # => 2 + 2 = 4

# Alternative string delimiters
%q(This is a string)        # Like single quotes
%Q(This is #{name})         # Like double quotes
%(Another #{name} string)   # Like double quotes
```

### Array Literals
```ruby
# Empty array
empty = []

# Array with elements
numbers = [1, 2, 3, 4, 5]
mixed = [1, "hello", true, 3.14]

# Array of words (shortcut)
words = %w[apple banana cherry]  # => ["apple", "banana", "cherry"]
```

### Hash Literals
```ruby
# Empty hash
empty = {}

# Hash with key-value pairs
person = {
  "name" => "John",
  "age" => 30,
  "city" => "New York"
}

# Symbol keys (common practice)
user = {
  name: "Alice",
  age: 25,
  email: "alice@example.com"
}

# Mixed keys
mixed = {
  "string_key" => "value",
  :symbol_key => "another value",
  42 => "numeric key"
}
```

### Range Literals
```ruby
# Inclusive range
numbers = 1..10       # => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10

# Exclusive range
letters = 'a'...'z'   # => a, b, c, ..., y (not z)
```

## Operators

### Arithmetic Operators
```ruby
a = 10
b = 3

puts a + b      # => 13 (addition)
puts a - b      # => 7  (subtraction)
puts a * b      # => 30 (multiplication)
puts a / b      # => 3  (integer division)
puts a / 3.0    # => 3.3333333333333335 (float division)
puts a % b      # => 1  (modulo)
puts a ** b     # => 1000 (exponentiation)
```

### Comparison Operators
```ruby
x = 10
y = 20

puts x == y     # => false (equal)
puts x != y     # => true  (not equal)
puts x < y      # => true  (less than)
puts x <= y     # => true  (less than or equal)
puts x > y      # => false (greater than)
puts x >= y     # => false (greater than or equal)
puts x <=> y    # => -1 (spaceship operator)
```

### Logical Operators
```ruby
a = true
b = false

puts a && b     # => false (logical AND)
puts a || b     # => true  (logical OR)
puts !a         # => false (logical NOT)

# Short-circuit evaluation
puts true || puts("This won't print")  # => true
puts false && puts("This won't print") # => false
```

### Assignment Operators
```ruby
x = 10
x += 5      # x = x + 5  => 15
x -= 3      # x = x - 3  => 12
x *= 2      # x = x * 2  => 24
x /= 4      # x = x / 4  => 6
x %= 4      # x = x % 4  => 2
```

## Method Calls

### Basic Method Calls
```ruby
puts "Hello"              # => Hello
puts "Hello".length       # => 5
puts "hello".upcase       # => HELLO
puts "WORLD".downcase     # => world
```

### Method Calls with Arguments
```ruby
puts "hello".insert(0, "greetings, ")  # => greetings, hello
puts "banana".sub('a', 'o')            # => bonana
puts [1, 2, 3].join(", ")              # => 1, 2, 3
```

### Method Calls with Blocks
```ruby
# Using do...end syntax
[1, 2, 3, 4, 5].each do |number|
  puts number * 2
end

# Using curly brace syntax
[1, 2, 3, 4, 5].map { |n| n * 2 }
# => [2, 4, 6, 8, 10]

# Method chaining
"hello world".upcase.split(" ").join("-")
# => "HELLO-WORLD"
```

## Conditional Expressions

### Ternary Operator
```ruby
age = 18
status = age >= 18 ? "adult" : "minor"
puts status  # => "adult"
```

### Unless Modifier
```ruby
# Unless is the opposite of if
puts "Access granted" unless user.nil?

# Equivalent to:
puts "Access granted" if !user.nil?
```

## String Interpolation

### Basic Interpolation
```ruby
name = "Alice"
age = 25
puts "My name is #{name} and I'm #{age} years old."
# => My name is Alice and I'm 25 years old.
```

### Complex Expressions
```ruby
x = 10
y = 20
puts "The sum of #{x} and #{y} is #{x + y}."
# => The sum of 10 and 20 is 30.

puts "The result is #{x > y ? 'greater' : 'smaller'}."
# => The result is smaller.
```

## Method Definitions

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
```

### Method with Default Parameters
```ruby
def greet_with_default(name = "Guest")
  "Hello, #{name}!"
end

puts greet_with_default          # => "Hello, Guest!"
puts greet_with_default("Bob")    # => "Hello, Bob!"
```

### Method with Keyword Arguments
```ruby
def create_user(name:, age:, email: nil)
  user = { name: name, age: age }
  user[:email] = email if email
  user
end

user = create_user(name: "Alice", age: 25, email: "alice@example.com")
puts user.inspect
# => {:name=>"Alice", :age=>25, :email=>"alice@example.com"}
```

## Code Organization

### Class Definition
```ruby
class Person
  def initialize(name, age)
    @name = name
    @age = age
  end
  
  def introduce
    "Hi, I'm #{@name} and I'm #{@age} years old."
  end
end

person = Person.new("John", 30)
puts person.introduce
# => Hi, I'm John and I'm 30 years old.
```

### Module Definition
```ruby
module Greetings
  def hello
    "Hello!"
  end
  
  def goodbye
    "Goodbye!"
  end
end

class Person
  include Greetings
end

person = Person.new
puts person.hello    # => "Hello!"
puts person.goodbye  # => "Goodbye!"
```

## Best Practices

### 1. Use Descriptive Variable Names
```ruby
# Good
user_name = "John Doe"
user_age = 25

# Bad
n = "John Doe"
a = 25
```

### 2. Follow Ruby Conventions
```ruby
# Variables: snake_case
my_variable = "value"

# Classes: PascalCase
MyClass = Class.new

# Constants: UPPER_SNAKE_CASE
MY_CONSTANT = 42

# Methods: snake_case
def my_method
  # code here
end
```

### 3. Use Meaningful Method Names
```ruby
# Good
def calculate_total_price(items)
  # implementation
end

# Bad
def calc(items)
  # implementation
end
```

### 4. Keep Methods Small
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

## Common Syntax Errors and Solutions

### 1. Undefined Method
```ruby
# Error
puts "hello".lenght  # => NoMethodError: undefined method `lenght'

# Solution
puts "hello".length  # => 5
```

### 2. Syntax Error in Hash
```ruby
# Error (old Ruby syntax)
hash = { :name => "John", :age => 30 }

# Modern syntax
hash = { name: "John", age: 30 }
```

### 3. Missing End Statement
```ruby
# Error
def my_method
  puts "hello"  # Missing 'end'

# Solution
def my_method
  puts "hello"
end
```

## Practice Exercises

### Exercise 1: Basic Operations
Create a program that:
1. Defines variables for your name, age, and city
2. Prints a formatted string with this information
3. Calculates and prints the year you were born

### Exercise 2: Method Definition
Define a method that:
1. Takes two numbers as parameters
2. Returns their sum, difference, product, and quotient
3. Handles division by zero

### Exercise 3: String Manipulation
Write code that:
1. Takes a sentence as input
2. Converts it to uppercase
3. Reverses the string
4. Counts the number of characters

---

**Ready to learn about Ruby's data types? Let's continue! 📊**
