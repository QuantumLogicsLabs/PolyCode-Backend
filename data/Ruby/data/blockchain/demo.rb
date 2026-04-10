# A method that takes a block
def greet_user(name)
  puts "Preparing greeting..."
  yield(name) if block_given?
  puts "Greeting sent!"
end

# Executing the method with a block
greet_user("User") do |n|
  puts "Hello, #{n}! Welcome to the Polycode project."
end