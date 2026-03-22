# Basic Hello World Example
# This is your first Ruby program

puts "Hello, Ruby World!"
puts "Welcome to Ruby programming!"

# Using variables
name = "Ruby Programmer"
puts "Hello, #{name}!"

# Basic arithmetic
x = 10
y = 5
puts "#{x} + #{y} = #{x + y}"
puts "#{x} * #{y} = #{x * y}"

# String manipulation
greeting = "hello"
puts greeting.upcase
puts greeting.capitalize

# Array example
fruits = ["apple", "banana", "cherry"]
puts "I like #{fruits.join(', ')}"

# Hash example
person = {
  name: "John",
  age: 30,
  city: "New York"
}

puts "#{person[:name]} is #{person[:age]} years old and lives in #{person[:city]}."

# Simple method definition
def greet(name)
  "Hello, #{name}!"
end

puts greet("World")
puts greet("Ruby")
