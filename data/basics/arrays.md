# Ruby Arrays and Collections

## Arrays

### Array Creation
```ruby
# Array literals
empty_array = []
numbers = [1, 2, 3, 4, 5]
mixed = [1, "hello", true, 3.14, :symbol]

# Array with range
range_array = (1..5).to_a  # [1, 2, 3, 4, 5]
exclusive_range = (1...5).to_a  # [1, 2, 3, 4]

# Array.new with size
fixed_array = Array.new(5)  # [nil, nil, nil, nil, nil]
filled_array = Array.new(5, 0)  # [0, 0, 0, 0, 0]

# Array.new with block
generated_array = Array.new(5) { |i| i * 2 }  # [0, 2, 4, 6, 8]

# Array from string
char_array = "hello".chars  # ["h", "e", "l", "l", "o"]
split_array = "a,b,c,d".split(',')  # ["a", "b", "c", "d"]

# Array from hash keys
hash = { a: 1, b: 2, c: 3 }
keys_array = hash.keys  # [:a, :b, :c]
values_array = hash.values  # [1, 2, 3]
```

### Array Access and Modification
```ruby
# Access by index
numbers = [10, 20, 30, 40, 50]
first = numbers[0]     # 10
second = numbers[1]    # 20
last = numbers[-1]     # 50
second_last = numbers[-2]  # 40

# Access with range
subarray = numbers[1..3]   # [20, 30, 40]
partial = numbers[1...3]  # [20, 30]

# Assignment
numbers[0] = 15
numbers[-1] = 55
numbers[1..3] = [25, 35, 45]

# Push and pop
numbers.push(60)
numbers << 70  # Shorthand for push
last_element = numbers.pop
first_element = numbers.shift

# Insert and delete
numbers.insert(2, 25)
numbers.delete_at(2)
numbers.delete(25)  # Delete by value

# Clear array
numbers.clear
```

### Array Operations
```ruby
# Array length and emptiness
numbers = [1, 2, 3, 4, 5]
size = numbers.length
empty = numbers.empty?
not_empty = !numbers.empty?

# Include and member
numbers.include?(3)     # true
numbers.member?(3)      # true (alias)

# First and last
numbers.first           # 1
numbers.last            # 5

# Sample and shuffle
sample = numbers.sample(3)
shuffled = numbers.shuffle

# Reverse and sort
reversed = numbers.reverse
sorted = numbers.sort
sorted_desc = numbers.sort { |a, b| b <=> a }

# Join and split
joined = numbers.join(", ")
split_string = "a,b,c".split(',')

# Compact and uniq
with_nils = [1, nil, 2, nil, 3]
compacted = with_nils.compact  # [1, 2, 3]

with_duplicates = [1, 2, 2, 3, 3, 3]
unique = with_duplicates.uniq  # [1, 2, 3]

# Flatten nested arrays
nested = [[1, 2], [3, 4], [5, 6]]
flattened = nested.flatten  # [1, 2, 3, 4, 5, 6]

# Transpose 2D array
matrix = [[1, 2, 3], [4, 5, 6]]
transposed = matrix.transpose  # [[1, 4], [2, 5], [3, 6]]
```

### Array Iteration
```ruby
# Basic each loop
numbers = [1, 2, 3, 4, 5]
numbers.each { |number| puts number }

# Each with index
numbers.each_with_index { |number, index| puts "#{index}: #{number}" }

# Map/collect
doubled = numbers.map { |number| number * 2 }
squared = numbers.collect { |number| number ** 2 }

# Select/reject
evens = numbers.select { |number| number.even? }
odds = numbers.reject { |number| number.even? }

# Find/detect
first_even = numbers.find { |number| number.even? }
all_evens = numbers.find_all { |number| number.even? }

# Any/all/none
has_even = numbers.any? { |number| number.even? }
all_even = numbers.all? { |number| number.even? }
no_even = numbers.none? { |number| number.even? }

# Reduce/inject
sum = numbers.reduce(0) { |total, number| total + number }
product = numbers.reduce(1) { |total, number| total * number }

# Group by
grouped = numbers.group_by { |number| number.even? ? :even : :odd }

# Chunk and slice
chunks = numbers.each_slice(2).to_a  # [[1, 2], [3, 4], [5]]
pairs = numbers.each_cons(2).to_a   # [[1, 2], [2, 3], [3, 4], [4, 5]]
```

## Hashes

### Hash Creation
```ruby
# Hash literal
empty_hash = {}
person = { name: "John", age: 30, city: "NYC" }

# Hash with string keys
string_keys = { "name" => "John", "age" => 30 }

# Hash.new
default_hash = Hash.new(0)  # Default value for missing keys
hash_with_block = Hash.new { |h, k| h[k] = "default for #{k}" }

# Hash from arrays
keys = [:name, :age, :city]
values = ["John", 30, "NYC"]
person = keys.zip(values).to_h

# Hash from two arrays
keys = [1, 2, 3]
values = ["one", "two", "three"]
number_hash = Hash[keys.zip(values)]

# Hash with default proc
word_count = Hash.new(0)
text = "hello world hello"
text.split.each { |word| word_count[word] += 1 }
```

### Hash Access and Modification
```ruby
# Access values
person = { name: "John", age: 30, city: "NYC" }
name = person[:name]
age = person[:age]
missing = person[:missing]  # nil

# Access with default
person.fetch(:name, "Unknown")
person.fetch(:missing, "Default")

# Access with block
person.fetch(:missing) { |key| "No #{key} found" }

# Assignment
person[:email] = "john@example.com"
person[:age] = 31

# Merge hashes
additional = { email: "john@example.com", phone: "555-1234" }
merged = person.merge(additional)

# Merge with block for conflicts
hash1 = { a: 1, b: 2 }
hash2 = { b: 3, c: 4 }
merged = hash1.merge(hash2) { |key, old, new| old + new }
# { a: 1, b: 5, c: 4 }

# Delete keys
person.delete(:email)
deleted_value = person.delete(:age)  # Returns deleted value

# Clear hash
person.clear
```

### Hash Operations
```ruby
# Hash size and emptiness
person = { name: "John", age: 30, city: "NYC" }
size = person.size
empty = person.empty?
not_empty = !person.empty?

# Key and value existence
person.key?(:name)      # true
person.value?(30)       # true
person.has_key?(:name)  # true
person.has_value?(30)   # true

# Get keys and values
keys = person.keys        # [:name, :age, :city]
values = person.values    # ["John", 30, "NYC"]

# Invert hash
numbers = { one: 1, two: 2, three: 3 }
inverted = numbers.invert  # {1=>"one", 2=>"two", 3=>"three"}

# Select and reject
person = { name: "John", age: 30, active: true, score: 85}
selected = person.select { |key, value| value.is_a?(TrueClass) }
rejected = person.reject { |key, value| value.is_a?(TrueClass) }

# Transform keys and values
person = { name: "John", age: 30 }
upper_keys = person.transform_keys { |key| key.to_s.upcase }  # {"NAME"=>"John", "AGE"=>30}
doubled_values = person.transform_values { |value| value * 2 }  # {:name=> "JohnJohn", :age=>60}

# Hash iteration
person.each { |key, value| puts "#{key}: #{value}" }
person.each_key { |key| puts key }
person.each_value { |value| puts value }
person.each_pair { |key, value| puts "#{key}: #{value}" }

# Convert to array
person.to_a  # [[:name, "John"], [:age, 30], [:city, "NYC"]]
```

## Sets

### Set Operations
```ruby
require 'set'

# Set creation
empty_set = Set.new
numbers = Set.new([1, 2, 3, 4, 5])
words = Set.new(%w[apple banana cherry])

# Add and remove elements
numbers.add(6)
numbers << 7  # Shorthand for add
numbers.delete(1)
numbers.delete(2)

# Set operations
set1 = Set.new([1, 2, 3, 4, 5])
set2 = Set.new([4, 5, 6, 7, 8])

# Union
union = set1 | set2  # Set.new([1, 2, 3, 4, 5, 6, 7, 8])
union = set1.union(set2)

# Intersection
intersection = set1 & set2  # Set.new([4, 5])
intersection = set1.intersection(set2)

# Difference
difference = set1 - set2  # Set.new([1, 2, 3])
difference = set1.difference(set2)

# Symmetric difference
sym_diff = set1 ^ set2  # Set.new([1, 2, 3, 6, 7, 8])
sym_diff = set1.symmetric_difference(set2)

# Subset and superset
subset = Set.new([1, 2])
superset = Set.new([1, 2, 3, 4, 5])

subset.subset?(superset)  # true
superset.superset?(subset)  # true

# Set membership
numbers.include?(3)  # true
numbers.member?(3)   # true

# Set size and emptiness
numbers.size
numbers.empty?
```

## Ranges

### Range Creation
```ruby
# Inclusive range
inclusive = 1..5  # 1, 2, 3, 4, 5

# Exclusive range
exclusive = 1...5  # 1, 2, 3, 4

# Character ranges
letters = 'a'..'d'  # a, b, c, d

# Date ranges
require 'date'
start_date = Date.new(2023, 1, 1)
end_date = Date.new(2023, 12, 31)
year_range = start_date..end_date

# Range with step
numbers = (1..10).step(2)  # 1, 3, 5, 7, 9

# Range from array
array = [1, 2, 3, 4, 5]
range = array[0..2]  # 1, 2, 3
```

### Range Operations
```ruby
# Range properties
range = 1..5
range.begin      # 1
range.end        # 5
range.exclude_end?  # false

# Range methods
range.to_a        # [1, 2, 3, 4, 5]
range.size        # 5
range.first       # 1
range.last        # 5

# Range membership
range.include?(3)  # true
range.cover?(3)    # true
range.member?(3)   # true

# Range comparison
(1..5) === 3  # true
(1..5) === 6  # false

# Range iteration
(1..5).each { |i| puts i }

# Range operations
range = 1..10
filtered = range.select { |i| i.even? }  # [2, 4, 6, 8, 10]
mapped = range.map { |i| i * 2 }        # [2, 4, 6, 8, 10, 12, 14, 16, 18, 20]
```

## Structs

### Struct Definition
```ruby
# Basic struct
Person = Struct.new(:name, :age, :email) do
  def adult?
    age >= 18
  end
  
  def greet
    "Hello, I'm #{name}"
  end
end

# Create struct instances
person1 = Person.new("John", 30, "john@example.com")
person2 = Person.new(name: "Jane", age: 25, email: "jane@example.com")

# Access attributes
person1.name    # "John"
person1.age     # 30
person1.email    # "john@example.com"

# Call methods
person1.adult?  # true
person1.greet    # "Hello, I'm John"

# Struct with default values
User = Struct.new(:name, :age, :email) do
  def initialize(name, age = 18, email = nil)
    super(name, age, email)
  end
end

# Struct with methods
Point = Struct.new(:x, :y) do
  def distance(other)
    Math.sqrt((x - other.x)**2 + (y - other.y)**2)
  end
  
  def to_s
    "(#{x}, #{y})"
  end
end

point1 = Point.new(0, 0)
point2 = Point.new(3, 4)
point1.distance(point2)  # 5.0
```

## Enumerable Module

### Enumerable Methods
```ruby
# Any class can include Enumerable
class MyCollection
  include Enumerable
  
  def initialize(items)
    @items = items
  end
  
  def each(&block)
    @items.each(&block)
  end
end

# Now we can use all Enumerable methods
collection = MyCollection.new([1, 2, 3, 4, 5])
collection.map { |i| i * 2 }  # [2, 4, 6, 8, 10]
collection.select { |i| i.even? }  # [2, 4]
collection.reduce(:+)  # 15
```

### Custom Enumerable Methods
```ruby
# Custom enumerable class
class Fibonacci
  include Enumerable
  
  def initialize(limit)
    @limit = limit
  end
  
  def each
    a, b = 0, 1
    while a <= @limit
      yield a
      a, b = b, a + b
    end
  end
end

# Use Enumerable methods
fib = Fibonacci.new(20)
fib.take(10).to_a  # [0, 1, 1, 2, 3, 5, 8, 13]
fib.select { |n| n.even? }  # [0, 2, 8]
fib.find { |n| n > 10 }  # 13
```

## Performance Considerations

### Array Performance
```ruby
# Good: Use appropriate methods
numbers = [1, 2, 3, 4, 5]

# For transformation
doubled = numbers.map { |n| n * 2 }

# For filtering
evens = numbers.select { |n| n.even? }

# For checking existence
has_even = numbers.any? { |n| n.even? }

# Bad: Using each when other methods are better
doubled_bad = []
numbers.each { |n| doubled_bad << n * 2 }

# Good: Use bsearch for sorted arrays
sorted_numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
index = sorted_numbers.bsearch_index { |n| n >= 5 }

# Good: Use include? for membership testing
numbers.include?(5)  # O(n) for unsorted array
sorted_numbers.include?(5)  # O(log n) for sorted array with bsearch
```

### Hash Performance
```ruby
# Good: Use symbols for hash keys when possible
# Symbols are faster for hash keys than strings
person = { name: "John", age: 30 }  # Good
person = { "name" => "John", "age" => 30 }  # Slower

# Good: Use fetch for missing key handling
person.fetch(:name, "Unknown")  # Returns value or default
person[:name] || "Unknown"  # Returns value or default

# Good: Use has_key? instead of keys.include?
person.has_key?(:name)  # O(1)
person.keys.include?(:name)  # O(n)

# Good: Use values_at for multiple values
keys = [:name, :age, :email]
values = person.values_at(*keys)
```

### Memory Management
```ruby
# Good: Use lazy evaluation for large datasets
large_array = (1..1000000).to_a
lazy_array = (1..1000000).lazy

# Process large arrays without loading everything
File.foreach("large_file.txt") do |line|
  process_line(line)
end

# Good: Use each_slice for memory-efficient processing
large_array = (1..1000000).to_a
large_array.each_slice(1000) do |chunk|
  process_chunk(chunk)
end

# Good: Clear large arrays when done
large_array.clear if large_array
```

## Best Practices

### Collection Selection
```ruby
# Use Array for ordered collections with potential duplicates
numbers = [1, 2, 3, 4, 5]
numbers << 6  # Can add duplicates

# Use Set for unique collections
unique_numbers = Set.new([1, 2, 3, 4, 5])
unique_numbers << 5  # Won't add duplicate

# Use Hash for key-value associations
person = { name: "John", age: 30 }
person[:email] = "john@example.com"

# Use Range for sequential values
numbers = 1..10
letters = 'a'..'z'

# Use Struct for simple data containers
Point = Struct.new(:x, :y)
point = Point.new(10, 20)
```

### Idiomatic Ruby
```ruby
# Use each instead of for loops
numbers = [1, 2, 3, 4, 5]
numbers.each { |n| puts n }

# Use map for transformation
doubled = numbers.map { |n| n * 2 }

# Use select for filtering
evens = numbers.select { |n| n.even? }

# Use reduce for aggregation
sum = numbers.reduce(:+)

# Use symbols for hash keys
person = { name: "John", age: 30 }

# Use block form for file operations
File.open("file.txt", "r") do |file|
  file.each_line { |line| process_line(line) }
end

# Use method chaining
result = numbers
  .select { |n| n.even? }
  .map { |n| n * 2 }
  .take(5)
```

### Error Handling
```ruby
# Handle missing hash keys gracefully
person = { name: "John", age: 30 }
name = person.fetch(:name, "Unknown")
age = person.fetch(:age, 0)

# Use default values
email = person[:email] || "no-email@example.com"

# Validate array indices
array = [1, 2, 3]
value = array[index] if index && index < array.length

# Handle empty collections
result = collection.first if collection.any?
result = collection.first || default_value
```

## Common Pitfalls

### Array Pitfalls
```ruby
# Pitfall: Modifying array while iterating
numbers = [1, 2, 3, 4, 5]
numbers.each do |number|
  numbers.delete(number) if number.even?  # Bad: modifies while iterating
end

# Solution: Use reject or create new array
numbers = [1, 2, 3, 4, 5]
numbers = numbers.reject { |number| number.even? }

# Pitfall: Using wrong method for the job
numbers = [1, 2, 3, 4, 5]
has_even = false
numbers.each { |n| has_even = true if n.even? }  # Bad: use any?

# Solution: Use appropriate method
has_even = numbers.any? { |n| n.even? }

# Pitfall: Array vs string confusion
"hello"[0]  # "h" (character)
["hello"][0]  # "hello" (string)
```

### Hash Pitfalls
```ruby
# Pitfall: String vs symbol keys
hash = { "name" => "John" }
hash[:name]  # nil (different key types)

# Solution: Be consistent with key types
hash = { name: "John" }
hash[:name]  # "John"

# Pitfall: Default value confusion
hash = Hash.new(0)
hash[:missing]  # 0
hash[:missing] += 1
hash[:missing]  # 1 (same key)

# Pitfall: Hash modification during iteration
person = { name: "John", age: 30 }
person.each do |key, value|
  person[key] = value.upcase if key == :name  # Modifying while iterating
end
```

### Collection Pitfalls
```ruby
# Pitfall: Not understanding copy vs reference
original = [1, 2, 3]
copy = original
copy << 4
original  # [1, 2, 3, 4] (same object)

# Solution: Use dup for shallow copy
original = [1, 2, 3]
copy = original.dup
copy << 4
original  # [1, 2, 3] (different object)

# Pitfall: Freezing collections
array = [1, 2, 3]
array.freeze
array << 4  # FrozenError (can't modify frozen array)

# Pitfall: Nested collection operations
nested = [[1, 2], [3, 4]]
flattened = nested.flatten  # [1, 2, 3, 4]
nested.flatten!  # Modifies original array
```

## Summary

Ruby arrays and collections provide:

**Arrays:**
- Flexible ordered collections
- Multiple creation methods
- Rich set of operations and iterators
- Efficient indexing and manipulation

**Hashes:**
- Key-value associations
- Fast lookups and modifications
- Various iteration methods
- Default value handling

**Sets:**
- Unique element collections
- Mathematical set operations
- Efficient membership testing
- Union, intersection, difference operations

**Ranges:**
- Sequential value ranges
- Inclusive and exclusive ranges
- Character and date ranges
- Enumerable integration

**Structs:**
- Simple data containers
- Attribute access and methods
- Memory-efficient alternative to classes
- Value-like semantics

**Enumerable Module:**
- Rich set of iteration methods
- Custom collection support
- Method chaining capabilities
- Lazy evaluation support

**Performance:**
- Choose appropriate collection type
- Use efficient methods for operations
- Consider memory usage for large datasets
- Handle edge cases gracefully

**Best Practices:**
- Use idiomatic Ruby methods
- Handle missing keys and indices
- Be consistent with key types
- Use blocks for resource management

**Common Pitfalls:**
- Modifying collections during iteration
- Wrong method selection
- Copy vs reference confusion
- Key type inconsistencies
- Nested collection complexity

Ruby's rich collection ecosystem provides powerful tools for data manipulation, making it easy to write clean, efficient code for processing and organizing data in various ways.
