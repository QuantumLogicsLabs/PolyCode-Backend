# Ruby Variables and Data Types

## Variables in Ruby

### Variable Declaration and Assignment
```ruby
# Local variables (start with lowercase or underscore)
name = "Ruby"
age = 30
is_active = true
price = 19.99

# Multiple assignment
first_name, last_name = "John", "Doe"
x, y, z = 1, 2, 3

# Parallel assignment with splat operator
a, *rest = 1, 2, 3, 4, 5
# a = 1, rest = [2, 3, 4, 5]

# Instance variables (start with @)
class Person
  def initialize(name, age)
    @name = name
    @age = age
  end
  
  def display_info
    puts "Name: #{@name}, Age: #{@age}"
  end
end

# Class variables (start with @@)
class Counter
  @@count = 0
  
  def initialize
    @@count += 1
  end
  
  def self.count
    @@count
  end
end

# Global variables (start with $)
$global_variable = "I am global"

# Constants (start with uppercase)
PI = 3.141592653589793
MAX_USERS = 1000
APP_VERSION = "1.0.0"
```

### Variable Naming Conventions
```ruby
# Local variables - snake_case
first_name = "John"
last_name = "Doe"
user_age = 30
is_admin = true

# Constants - SCREAMING_SNAKE_CASE
MAX_LOGIN_ATTEMPTS = 3
DEFAULT_TIMEOUT = 30
API_BASE_URL = "https://api.example.com"

# Instance variables - snake_case with @ prefix
@user_name = "Alice"
@user_email = "alice@example.com"
@is_verified = true

# Class variables - snake_case with @@ prefix
@@instance_count = 0
@@connection_pool = []

# Global variables - snake_case with $ prefix
$debug_mode = true
$global_config = {}

# Method names - snake_case
def calculate_total(price, tax_rate)
  price * (1 + tax_rate)
end

def is_valid_email?(email)
  email.include?('@')
end

# Class names - CamelCase
class UserAccount
end

class PaymentProcessor
end

# Module names - CamelCase
module Authentication
end

module DataValidation
end
```

### Variable Scope
```ruby
class ScopeExample
  # Class variable - accessible throughout the class hierarchy
  @@class_variable = "I am class-scoped"
  
  # Instance variable - accessible within instance methods
  def initialize
    @instance_variable = "I am instance-scoped"
  end
  
  def demonstrate_scope
    # Local variable - accessible only within this method
    local_variable = "I am method-scoped"
    
    puts local_variable
    puts @instance_variable
    puts @@class_variable
    puts $global_variable
  end
  
  def self.class_method
    # Local variable in class method
    class_local = "I am class method local"
    
    puts class_local
    puts @@class_variable
    puts $global_variable
    # @instance_variable is not accessible here
  end
end

# Global variable - accessible everywhere
$global_variable = "I am global-scoped"

# Constant scope
module MyModule
  MODULE_CONSTANT = "Module constant"
  
  class MyClass
    CLASS_CONSTANT = "Class constant"
    
    def show_constants
      puts MODULE_CONSTANT  # Accessible
      puts CLASS_CONSTANT   # Accessible
    end
  end
end
```

## Data Types

### Basic Data Types
```ruby
# Numbers
integer = 42
float = 3.14
negative = -10
scientific = 1.5e-4

# Arithmetic operations
sum = 10 + 5
difference = 10 - 5
product = 10 * 5
quotient = 10 / 5
remainder = 10 % 3
power = 2 ** 3

# String
single_quoted = 'Hello, World!'
double_quoted = "Hello, #{name}!"  # String interpolation
multiline = "This is a
multiline string"

# String methods
greeting = "Hello, Ruby!"
puts greeting.length
puts greeting.upcase
puts greeting.downcase
puts greeting.reverse
puts greeting.include?("Ruby")
puts greeting.gsub("Ruby", "World")

# Boolean
true_value = true
false_value = false
nil_value = nil

# Boolean operations
is_adult = age >= 18
has_permission = is_admin && is_active
can_access = is_admin || is_admin
is_invalid = !is_valid

# Nil handling
name = nil
result = name || "Default Name"  # Nil coalescing
puts result

# Symbols (immutable identifiers)
status = :active
role = :admin
method_name = :calculate_total

# Symbol advantages
# - Immutable
# - More memory efficient than strings
# - Faster comparison

# Arrays
numbers = [1, 2, 3, 4, 5]
mixed = [1, "hello", true, 3.14]
nested = [[1, 2], [3, 4], [5, 6]]

# Array operations
numbers << 6  # Add element
numbers.pop   # Remove last element
numbers.shift # Remove first element
numbers[0]    # Access by index
numbers[-1]   # Access from end
numbers[1..3] # Slice

# Hashes (key-value pairs)
person = {
  name: "John",
  age: 30,
  city: "New York"
}

# Alternative hash syntax
alternative_hash = {
  "name" => "John",
  "age" => 30,
  "city" => "New York"
}

# Hash operations
person[:email] = "john@example.com"  # Add key-value
person[:name]                        # Access value
person.key?(:age)                     # Check if key exists
person.keys                           # Get all keys
person.values                         # Get all values

# Ranges
inclusive_range = 1..5    # 1, 2, 3, 4, 5
exclusive_range = 1...5   # 1, 2, 3, 4
letter_range = 'a'..'d'   # a, b, c, d

# Range operations
inclusive_range.to_a     # Convert to array
inclusive_range.include?(3)  # Check if value in range
inclusive_range.first    # Get first value
inclusive_range.last     # Get last value
```

### Type Conversion and Casting
```ruby
# Implicit conversion
number = 42
text = "The number is #{number}"  # Number converted to string

# Explicit conversion
string_number = "123"
integer = string_number.to_i
float = string_number.to_f

# Integer conversion
"42".to_i      # 42
"abc".to_i     # 0
"123abc".to_i  # 123

# Float conversion
"3.14".to_f    # 3.14
"abc".to_f     # 0.0
"3.14abc".to_f # 3.14

# String conversion
42.to_s        # "42"
3.14.to_s      # "3.14"
true.to_s       # "true"
false.to_s      # "false"
nil.to_s       # ""

# Boolean conversion
# In Ruby, only nil and false are falsy
!!"hello"      # true
!!42           # true
!!0            # true
!!nil          # false
!!false        # false

# Array conversion
"1,2,3".split(',')        # ["1", "2", "3"]
"1,2,3".split(',').map(&:to_i)  # [1, 2, 3]

# Hash conversion
"key1=value1&key2=value2".split('&').map { |pair| pair.split('=') }.to_h
# {"key1"=>"value1", "key2"=>"value2"}
```

### Special Variables
```ruby
# Predefined global variables
$0  # Name of the script being executed
$*  # Command line arguments
$?  # Exit status of last executed child process
$$  # Process number of the Ruby script

# Special global variables for regex
$~  # Last match string
$`  # String before last match
$'  # String after last match
$1  # First capture group
$2  # Second capture group

# Special local variables
self     # Current object
true     # True value
false    # False value
nil      # Nil value
__FILE__ # Current file name
__LINE__ # Current line number

# Block parameters
def process_items
  items = [1, 2, 3, 4, 5]
  items.each do |item|
    puts item
  end
end

# Splat operator
def sum(*numbers)
  numbers.sum
end

sum(1, 2, 3, 4, 5)  # 15

# Double splat for keyword arguments
def create_user(name:, email:, **options)
  user = { name: name, email: email }
  user.merge(options)
end

create_user(name: "John", email: "john@example.com", age: 30, city: "NYC")
```

## Constants

### Constant Definition and Usage
```ruby
# Module constants
module Math
  PI = 3.141592653589793
  E = 2.718281828459045
  
  def self.circle_area(radius)
    PI * radius ** 2
  end
end

# Class constants
class User
  MIN_AGE = 13
  MAX_AGE = 120
  DEFAULT_ROLE = :user
  
  VALID_ROLES = [:user, :admin, :moderator]
  
  def initialize(age, role = DEFAULT_ROLE)
    @age = age
    @role = role
  end
  
  def valid_age?
    age >= MIN_AGE && age <= MAX_AGE
  end
  
  def valid_role?
    VALID_ROLES.include?(@role)
  end
end

# Global constants
APP_NAME = "MyApp"
APP_VERSION = "1.0.0"
DEBUG_MODE = true

# Constant usage
puts Math::PI
puts User::MIN_AGE
puts APP_VERSION
```

### Constant Scope and Inheritance
```ruby
module ParentModule
  PARENT_CONSTANT = "From parent"
  
  class ParentClass
    CLASS_CONSTANT = "From parent class"
  end
end

module ChildModule
  include ParentModule
  
  CHILD_CONSTANT = "From child"
  
  def show_constants
    puts PARENT_CONSTANT  # Accessible
    puts CHILD_CONSTANT   # Accessible
  end
end

class ChildClass < ParentModule::ParentClass
  CHILD_CLASS_CONSTANT = "From child class"
  
  def show_all_constants
    puts CLASS_CONSTANT        # From parent class
    puts CHILD_CLASS_CONSTANT  # From child class
  end
end
```

### Dynamic Constants
```ruby
class Config
  # Constants can be defined dynamically
  ENVIRONMENTS = %w[development test production]
  
  ENVIRONMENTS.each do |env|
    const_set("#{env.upcase}_URL", "https://#{env}.example.com")
  end
  
  def self.get_url(environment)
    const_get("#{environment.upcase}_URL")
  end
end

# Usage
puts Config::DEVELOPMENT_URL
puts Config::TEST_URL
puts Config::PRODUCTION_URL

puts Config.get_url(:development)
```

## Best Practices

### Variable Naming and Organization
```ruby
# Good: Descriptive variable names
user_name = "John"
user_age = 30
is_admin_user = true
max_login_attempts = 3

# Bad: Non-descriptive names
x = "John"
y = 30
z = true
a = 3

# Good: Group related variables
user = {
  name: "John",
  age: 30,
  email: "john@example.com",
  is_active: true
}

# Good: Use constants for magic numbers
class PriceCalculator
  TAX_RATE = 0.08
  DISCOUNT_THRESHOLD = 100
  DISCOUNT_RATE = 0.1
  
  def calculate_total(price, quantity)
    subtotal = price * quantity
    discount = subtotal >= DISCOUNT_THRESHOLD ? subtotal * DISCOUNT_RATE : 0
    tax = (subtotal - discount) * TAX_RATE
    subtotal - discount + tax
  end
end

# Good: Use meaningful method names
def calculate_user_age(birth_year)
  Time.now.year - birth_year
end

def is_valid_email_format?(email)
  email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
end

# Good: Use constants for configuration
class DatabaseConfig
  CONNECTION_TIMEOUT = 30
  MAX_CONNECTIONS = 100
  RETRY_ATTEMPTS = 3
end
```

### Type Safety and Validation
```ruby
# Good: Type checking and validation
class User
  attr_reader :name, :age, :email
  
  def initialize(name, age, email)
    @name = validate_name(name)
    @age = validate_age(age)
    @email = validate_email(email)
  end
  
  private
  
  def validate_name(name)
    raise ArgumentError, "Name cannot be empty" if name.nil? || name.strip.empty?
    raise ArgumentError, "Name too long" if name.length > 100
    name.strip
  end
  
  def validate_age(age)
    raise ArgumentError, "Age must be a number" unless age.is_a?(Numeric)
    raise ArgumentError, "Age must be positive" if age < 0
    raise ArgumentError, "Age unrealistic" if age > 150
    age.to_i
  end
  
  def validate_email(email)
    raise ArgumentError, "Invalid email format" unless email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
    email.downcase
  end
end

# Good: Use type annotations (Ruby 3.0+)
class Calculator
  # @param num1 [Integer]
  # @param num2 [Integer]
  # @return [Integer]
  def add(num1, num2)
    num1 + num2
  end
  
  # @param price [Float]
  # @param quantity [Integer]
  # @return [Float]
  def calculate_total(price, quantity)
    price * quantity
  end
end
```

### Memory Management
```ruby
# Good: Use symbols instead of strings for hash keys
# Bad (creates new string objects each time)
user = { "name" => "John", "age" => 30 }

# Good (reuses symbol objects)
user = { name: "John", age: 30 }

# Good: Freeze constants to prevent modification
class Config
  SETTINGS = {
    timeout: 30,
    retries: 3,
    debug: false
  }.freeze
  
  ROLES = [:admin, :user, :guest].freeze
end

# Good: Use immutable objects when possible
require 'immutable'

# Using immutable hash
user_data = Immutable::Hash[name: "John", age: 30]
new_user_data = user_data.put(:age, 31)  # Returns new hash, doesn't modify original

# Good: Clean up resources
class FileProcessor
  def process_file(filename)
    file = File.open(filename, 'r')
    begin
      # Process file
      file.each_line do |line|
        puts line
      end
    ensure
      file.close  # Always close file
    end
  end
end

# Better: Use block form for automatic cleanup
class FileProcessor
  def process_file(filename)
    File.open(filename, 'r') do |file|
      file.each_line do |line|
        puts line
      end
    end  # File automatically closed
  end
end
```

## Common Pitfalls

### Variable Scope Issues
```ruby
# Pitfall: Variable shadowing
def calculate_total(price, tax_rate)
  total = price * (1 + tax_rate)
  total = total + 10  # Shadowing the original total
  total
end

# Better: Use different variable names
def calculate_total(price, tax_rate)
  subtotal = price * (1 + tax_rate)
  total = subtotal + 10
  total
end

# Pitfall: Instance variable vs local variable confusion
class Example
  def initialize
    @value = 10  # Instance variable
    value = 20   # Local variable
  end
  
  def show_value
    puts @value  # 10
    puts value    # NameError: undefined local variable
  end
end

# Pitfall: Constant redefinition warning
class MyClass
  CONSTANT = "original"
  CONSTANT = "modified"  # Warning: already initialized constant
end

# Better: Use different constant name or freeze it
class MyClass
  ORIGINAL_CONSTANT = "original".freeze
  MODIFIED_CONSTANT = "modified"
end
```

### Type Conversion Issues
```ruby
# Pitfall: Unexpected string to integer conversion
"123abc".to_i  # Returns 123, not an error
"abc".to_i     # Returns 0, not an error

# Better: Validate before conversion
def safe_to_i(string)
  return 0 unless string.match?(/^\d+$/)
  string.to_i
end

# Pitfall: Float precision issues
0.1 + 0.2  # 0.30000000000000004

# Better: Use decimal for precise calculations
require 'bigdecimal'
BigDecimal('0.1') + BigDecimal('0.2')  # 0.3

# Pitfall: Nil method calls
user = nil
user.name  # NoMethodError: undefined method `name' for nil:NilClass

# Better: Use safe navigation operator
user&.name  # Returns nil instead of error

# Or use nil guard
user.name unless user.nil?
```

### Global Variable Abuse
```ruby
# Pitfall: Overusing global variables
$counter = 0

def increment_counter
  $counter += 1
end

def reset_counter
  $counter = 0
end

# Better: Use class variables or instance variables
class Counter
  @@counter = 0
  
  def self.increment
    @@counter += 1
  end
  
  def self.reset
    @@counter = 0
  end
  
  def self.value
    @@counter
  end
end

# Or use singleton pattern
class Counter
  include Singleton
  
  attr_accessor :value
  
  def initialize
    @value = 0
  end
  
  def increment
    @value += 1
  end
  
  def reset
    @value = 0
  end
end
```

## Summary

Ruby variables and data types provide:

**Variable Types:**
- Local variables (snake_case)
- Instance variables (@snake_case)
- Class variables (@@snake_case)
- Global variables ($snake_case)
- Constants (SCREAMING_SNAKE_CASE)

**Data Types:**
- Numbers (integers, floats)
- Strings (single/double quoted, interpolation)
- Booleans (true, false, nil)
- Symbols (immutable identifiers)
- Arrays (ordered collections)
- Hashes (key-value pairs)
- Ranges (inclusive/exclusive)

**Type Conversion:**
- Implicit conversion in string interpolation
- Explicit conversion with to_i, to_f, to_s
- Safe conversion methods
- Boolean conversion rules

**Best Practices:**
- Descriptive naming conventions
- Type safety and validation
- Memory management with symbols and freezing
- Proper scope management
- Resource cleanup

**Common Pitfalls:**
- Variable shadowing
- Scope confusion
- Type conversion issues
- Global variable abuse
- Constant redefinition

Ruby's dynamic typing and flexible variable system make it easy to write expressive code, but require attention to naming conventions, scope, and type safety to maintain clean, maintainable codebases.
