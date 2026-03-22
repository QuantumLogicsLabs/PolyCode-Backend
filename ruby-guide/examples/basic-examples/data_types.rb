# Data Types Examples
# Demonstrating Ruby's basic data types and operations

# Numbers
puts "=== NUMBERS ==="
integer = 42
float_num = 3.14159

puts "Integer: #{integer} (#{integer.class})"
puts "Float: #{float_num} (#{float_num.class})"
puts "Integer methods: even? #{integer.even?}, odd? #{integer.odd?}, next #{integer.next}"
puts "Float methods: round #{float_num.round}, ceil #{float_num.ceil}, floor #{float_num.floor}"

# Strings
puts "\n=== STRINGS ==="
name = "Ruby Programming"
single_quoted = 'Hello, World!'
multi_line = "This is a\nmulti-line\nstring"

puts "String: #{name} (#{name.class})"
puts "Length: #{name.length}"
puts "Uppercase: #{name.upcase}"
puts "Lowercase: #{name.downcase}"
puts "Include 'Ruby'? #{name.include?('Ruby')}"
puts "Split: #{name.split(' ')}"
puts "Single quoted: #{single_quoted}"
puts "Multi-line:\n#{multi_line}"

# Symbols
puts "\n=== SYMBOLS ==="
symbol = :my_symbol
another_symbol = :my_symbol

puts "Symbol: #{symbol} (#{symbol.class})"
puts "Same symbol? #{symbol == another_symbol}"
puts "Same object ID? #{symbol.object_id == another_symbol.object_id}"
puts "To string: #{symbol.to_s}"
puts "String to symbol: #{'hello'.to_sym}"

# Booleans and Nil
puts "\n=== BOOLEANS AND NIL ==="
truthy = true
falsy = false
nothing = nil

puts "True: #{truthy} (#{truthy.class})"
puts "False: #{falsy} (#{falsy.class})"
puts "Nil: #{nothing} (#{nothing.class})"
puts "Nil is nil? #{nothing.nil?}"
puts "False is nil? #{falsy.nil?}"
puts "0 is truthy? #{!!0}"
puts "Empty string is truthy? #{!!''}"

# Arrays
puts "\n=== ARRAYS ==="
numbers = [1, 2, 3, 4, 5]
mixed = [1, "hello", true, 3.14, :symbol]
words = %w[apple banana cherry]

puts "Numbers array: #{numbers}"
puts "Mixed array: #{mixed}"
puts "Words array: #{words}"
puts "First element: #{numbers.first}"
puts "Last element: #{numbers.last}"
puts "Include 3? #{numbers.include?(3)}"
puts "Map (doubled): #{numbers.map { |n| n * 2 }}"
puts "Select (evens): #{numbers.select { |n| n.even? }}"
puts "Reduce (sum): #{numbers.reduce(:+)}"

# Hashes
puts "\n=== HASHES ==="
person = {
  name: "John Doe",
  age: 30,
  city: "New York",
  email: "john@example.com"
}

puts "Person hash: #{person}"
puts "Name: #{person[:name]}"
puts "Age: #{person[:age]}"
puts "Keys: #{person.keys}"
puts "Values: #{person.values}"
puts "Has email? #{person.has_key?(:email)}"
puts "Transform to string keys: #{person.map { |k, v| [k.to_s, v] }.to_h}"

# Ranges
puts "\n=== RANGES ==="
inclusive_range = 1..5
exclusive_range = 1...5
letter_range = 'a'..'e'

puts "Inclusive range: #{inclusive_range.to_a}"
puts "Exclusive range: #{exclusive_range.to_a}"
puts "Letter range: #{letter_range.to_a}"
puts "Include 5? #{inclusive_range.include?(5)}"
puts "Include 5? #{exclusive_range.include?(5)}"

# Type Conversion
puts "\n=== TYPE CONVERSION ==="
string_num = "42"
float_str = "3.14"

puts "String '#{string_num}' to integer: #{string_num.to_i}"
puts "String '#{float_str}' to float: #{float_str.to_f}"
puts "Integer 42 to string: #{42.to_s}"
puts "Float 3.14 to integer: #{3.14.to_i}"
puts "Array [[:a, 1], [:b, 2]] to hash: #{[[:a, 1], [:b, 2]].to_h}"

# Type Checking
puts "\n=== TYPE CHECKING ==="
value = "hello"

puts "Value: #{value}"
puts "Class: #{value.class}"
puts "Is String? #{value.is_a?(String)}"
puts "Is Object? #{value.is_a?(Object)}"
puts "Instance of String? #{value.instance_of?(String)}"
puts "Responds to upcase? #{value.respond_to?(:upcase)}"
puts "Responds to length? #{value.respond_to?(:length)}"

# Dynamic Typing Demonstration
puts "\n=== DYNAMIC TYPING ==="
variable = 42
puts "Variable is now: #{variable} (#{variable.class})"

variable = "hello"
puts "Variable is now: #{variable} (#{variable.class})"

variable = [1, 2, 3]
puts "Variable is now: #{variable} (#{variable.class})"

variable = { key: "value" }
puts "Variable is now: #{variable} (#{variable.class})"
