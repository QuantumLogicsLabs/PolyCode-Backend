# Data Types and Variables in Ruby

## Overview

Ruby is a dynamically typed language where everything is an object. This means that every value has a type, and you can call methods on any value.

## Basic Data Types

### 1. Numbers

#### Integers
```ruby
# Positive integers
age = 25
count = 100

# Negative integers
temperature = -10
balance = -500

# Different bases
decimal = 42        # Base 10
binary = 0b1010     # Base 2 (10)
octal = 0o52        # Base 8 (42)
hexadecimal = 0x2A  # Base 16 (42)

# Integer methods
puts 42.class           # => Integer
puts 42.abs            # => 42 (absolute value)
puts 42.even?          # => true
puts 42.odd?           # => false
puts 42.zero?          # => false
puts 42.next           # => 43
puts 42.pred           # => 41
```

#### Floats
```ruby
# Float literals
pi = 3.14159
price = 19.99
scientific = 1.5e3     # 1500.0

# Float methods
puts 3.14.class        # => Float
puts 3.14.round        # => 3
puts 3.14.ceil         # => 4
puts 3.14.floor        # => 3
puts 3.14.to_i         # => 3 (convert to integer)
puts 3.14.to_s         # => "3.14" (convert to string)

# Precision
puts 0.1 + 0.2         # => 0.30000000000000004 (floating point precision)
```

### 2. Strings

#### Creating Strings
```ruby
# Single quotes (no interpolation)
name1 = 'John Doe'
path1 = 'C:\Users\John'

# Double quotes (with interpolation)
name2 = "Alice"
greeting = "Hello, #{name2}!"

# Multi-line strings
poem = "Roses are red
Violets are blue
Ruby is awesome
And so are you"

# Here documents
long_text = <<~TEXT
  This is a multi-line string
  using the here document syntax.
  The ~ removes leading whitespace.
TEXT
```

#### String Methods
```ruby
text = "Hello, Ruby World!"

# Information
puts text.length        # => 18
puts text.size          # => 18
puts text.empty?        # => false

# Case manipulation
puts text.upcase        # => "HELLO, RUBY WORLD!"
puts text.downcase      # => "hello, ruby world!"
puts text.capitalize    # => "Hello, ruby world!"
puts text.swapcase      # => "hELLO, rUBY wORLD!"

# Substrings and searching
puts text[0, 5]         # => "Hello"
puts text.include?("Ruby")  # => true
puts text.start_with?("Hello")  # => true
puts text.end_with?("World!")   # => true

# Modification
puts text.gsub("Ruby", "Ruby")  # => "Hello, Ruby World!"
puts text.strip        # => "Hello, Ruby World!" (removes whitespace)
puts text.split(" ")   # => ["Hello,", "Ruby", "World!"]

# Concatenation
first = "Hello"
last = "World"
puts first + " " + last          # => "Hello World"
puts "#{first} #{last}"           # => "Hello World"
puts first.concat(" ", last)     # => "Hello World"
```

### 3. Symbols

Symbols are immutable, lightweight identifiers commonly used as hash keys or for method names.

```ruby
# Creating symbols
:name
:age
:"hello world"

# Symbol characteristics
puts :name.class        # => Symbol
puts :name.object_id    # => Same ID for same symbol
puts :name.to_s         # => "name"
puts "name".to_sym      # => :name

# Common uses
# Hash keys (more efficient than strings)
person = {
  name: "John",      # :name symbol
  age: 30,           # :age symbol
  :city => "NYC"     # Old syntax
}

# Method names
puts :methods         # List of all methods
puts :respond_to?     # Check if object responds to method
```

### 4. Booleans

```ruby
# Boolean values
true
false

# Boolean methods
puts true.class       # => TrueClass
puts false.class      # => FalseClass

# Truthiness in Ruby
# Only false and nil are falsy, everything else is truthy
puts true && "hello"  # => "hello"
puts false && "hello" # => false
puts nil && "hello"   # => nil
puts 0 && "hello"     # => "hello" (0 is truthy!)
puts "" && "hello"    # => "" (empty string is truthy!)

# Boolean operations
puts !true            # => false
puts !!true           # => true
puts true || false    # => true
```

### 5. Nil

```ruby
# Nil represents absence of value
nothing = nil

puts nil.class        # => NilClass
puts nil.to_s         # => ""
puts nil.to_i         # => 0
puts nil.nil?         # => true

# Safe navigation operator
user = nil
puts user&.name       # => nil (no error)
# puts user.name      # => NoMethodError

# Nil coalescing
name = user&.name || "Guest"
puts name             # => "Guest"
```

## Collection Types

### 1. Arrays

#### Creating Arrays
```ruby
# Empty array
empty = []

# Array with elements
numbers = [1, 2, 3, 4, 5]
mixed = [1, "hello", true, 3.14, :symbol]

# Array shortcuts
words = %w[apple banana cherry]      # => ["apple", "banana", "cherry"]
symbols = %i[apple banana cherry]    # => [:apple, :banana, :cherry]

# Range to array
range_array = (1..5).to_a            # => [1, 2, 3, 4, 5]
```

#### Array Methods
```ruby
fruits = ["apple", "banana", "cherry", "date"]

# Accessing elements
puts fruits[0]           # => "apple"
puts fruits[-1]          # => "date"
puts fruits.first        # => "apple"
puts fruits.last         # => "date"

# Information
puts fruits.length       # => 4
puts fruits.size         # => 4
puts fruits.empty?       # => false
puts fruits.include?("banana")  # => true

# Adding elements
fruits.push("elderberry")       # => ["apple", "banana", "cherry", "date", "elderberry"]
fruits << "fig"                 # Same as push
fruits.insert(2, "grape")       # => ["apple", "banana", "grape", "cherry", "date", "elderberry", "fig"]

# Removing elements
fruits.pop               # => "fig" (removes last)
fruits.shift             # => "apple" (removes first)
fruits.delete("cherry")  # => "cherry"

# Iteration
fruits.each do |fruit|
  puts "I like #{fruit}"
end

# Transformation
numbers = [1, 2, 3, 4, 5]
doubled = numbers.map { |n| n * 2 }      # => [2, 4, 6, 8, 10]
evens = numbers.select { |n| n.even? }   # => [2, 4]
sum = numbers.reduce(:+)                 # => 15

# Searching
numbers.find { |n| n > 3 }               # => 4
numbers.find_index(3)                    # => 2
numbers.index { |n| n > 3 }              # => 3
```

### 2. Hashes

#### Creating Hashes
```ruby
# Empty hash
empty = {}

# Hash with string keys
string_hash = {
  "name" => "John",
  "age" => 30
}

# Hash with symbol keys (common)
symbol_hash = {
  name: "Alice",
  age: 25,
  email: "alice@example.com"
}

# Mixed keys
mixed_hash = {
  "string_key" => "value",
  :symbol_key => "another value",
  42 => "numeric key"
}
```

#### Hash Methods
```ruby
person = {
  name: "John",
  age: 30,
  city: "New York",
  country: "USA"
}

# Accessing values
puts person[:name]          # => "John"
puts person[:age]           # => 30
puts person[:salary]        # => nil (key doesn't exist)
puts person.fetch(:age)     # => 30
puts person.fetch(:salary, 0)  # => 0 (default value)

# Adding/updating values
person[:salary] = 50000
person.store(:department, "Engineering")

# Removing values
person.delete(:country)
removed = person.extract!(:city)  # => {:city=>"New York"}

# Information
puts person.keys          # => [:name, :age, :city, :country]
puts person.values        # => ["John", 30, "New York", "USA"]
puts person.length        # => 4
puts person.empty?        # => false
puts person.has_key?(:name)   # => true
puts person.key?(:age)       # => true
puts person.value?("John")   # => true

# Iteration
person.each do |key, value|
  puts "#{key}: #{value}"
end

# Transformation
person.map { |k, v| [k.to_s, v.to_s] }.to_h
# => {"name"=>"John", "age"=>"30", "city"=>"New York", "country"=>"USA"}

# Selecting
person.select { |k, v| v.is_a?(String) }
# => {:name=>"John", :city=>"New York", :country=>"USA"}
```

### 3. Ranges

```ruby
# Inclusive range
numbers = 1..10       # => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10

# Exclusive range
letters = 'a'...'z'   # => a, b, c, ..., y

# Range methods
puts (1..10).begin    # => 1
puts (1..10).end      # => 10
puts (1...10).end     # => 10 (exclusive end is still 10)
puts (1..10).include?(5)   # => true
puts (1..10).cover?(5)     # => true
puts (1..10).member?(11)   # => false

# Converting to array
puts (1..5).to_a      # => [1, 2, 3, 4, 5]

# Iterating
(1..5).each { |n| puts n * 2 }
# => 2, 4, 6, 8, 10

# Useful with arrays
array = [10, 20, 30, 40, 50]
puts array[1..3]      # => [20, 30, 40]
puts array[1...3]     # => [20, 30]
```

## Type Conversion

### Explicit Conversion
```ruby
# To string
puts 42.to_s          # => "42"
puts 3.14.to_s        # => "3.14"
puts true.to_s        # => "true"
puts nil.to_s         # => ""

# To integer
puts "42".to_i        # => 42
puts "3.14".to_i      # => 3
puts 3.14.to_i        # => 3
puts true.to_i        # => 1
puts false.to_i       # => 0

# To float
puts "3.14".to_f      # => 3.14
puts 42.to_f          # => 42.0

# To array
puts "hello".to_a     # => ["hello"]
puts 42.to_a          # => [42]

# To hash
puts [].to_h          # => {}
puts [[:a, 1], [:b, 2]].to_h  # => {:a=>1, :b=>2}
```

### Safe Conversion
```ruby
# Integer conversion with error handling
puts Integer("42")    # => 42
# puts Integer("hello")  # => ArgumentError

# Float conversion with error handling
puts Float("3.14")    # => 3.14
# puts Float("hello")  # => ArgumentError
```

## Variable Scope

### Local Variables
```ruby
def my_method
  local_var = "I'm local"
  puts local_var
end

my_method
# puts local_var  # => NameError: undefined local variable
```

### Instance Variables
```ruby
class Person
  def initialize(name)
    @name = name  # Instance variable
  end
  
  def name
    @name
  end
  
  def name=(new_name)
    @name = new_name
  end
end

person = Person.new("John")
puts person.name      # => "John"
```

### Class Variables
```ruby
class Counter
  @@count = 0  # Class variable
  
  def initialize
    @@count += 1
  end
  
  def self.count
    @@count
  end
end

Counter.new
Counter.new
puts Counter.count  # => 2
```

### Global Variables
```ruby
$global_var = "I'm global"

def show_global
  puts $global_var
end

show_global  # => "I'm global"
puts $global_var  # => "I'm global"
```

### Constants
```ruby
class Math
  PI = 3.14159
  
  def self.circle_area(radius)
    PI * radius ** 2
  end
end

puts Math::PI  # => 3.14159
puts Math.circle_area(5)  # => 78.53975

# Constants can be modified (with warning)
PI = 3.14  # => warning: already initialized constant Math::PI
```

## Type Checking

### Checking Types
```ruby
value = "hello"

puts value.class        # => String
puts value.is_a?(String)  # => true
puts value.is_a?(Object)  # => true
puts value.kind_of?(String)  # => true (alias for is_a?)
puts value.instance_of?(String)  # => true
puts value.instance_of?(Object)  # => false

# Responds to method?
puts value.respond_to?(:upcase)  # => true
puts value.respond_to?(:length)  # => true
puts value.respond_to?(:nonexistent)  # => false
```

### Duck Typing
```ruby
def process_collection(collection)
  collection.each { |item| puts item }
end

process_collection([1, 2, 3])        # Works with Array
process_collection({a: 1, b: 2})     # Works with Hash
process_collection("hello")          # Works with String
```

## Practice Exercises

### Exercise 1: Data Type Exploration
Create a program that:
1. Creates variables of each basic data type
2. Prints their class and some methods
3. Demonstrates type conversion

### Exercise 2: Array Manipulation
Write code that:
1. Creates an array of numbers
2. Filters even numbers
3. Maps to squares
4. Reduces to sum

### Exercise 3: Hash Operations
Create a program that:
1. Builds a contact book using hashes
2. Adds, updates, and deletes contacts
3. Searches for contacts

### Exercise 4: Type Checker
Implement a method that:
1. Takes any object as parameter
2. Returns a description of its type and capabilities
3. Handles nil values gracefully

---

**Ready to learn about control flow in Ruby? Let's continue! 🔀**
