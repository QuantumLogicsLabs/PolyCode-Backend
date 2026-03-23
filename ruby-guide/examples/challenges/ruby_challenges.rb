#!/usr/bin/env ruby
# Ruby Programming Challenges
# Test your Ruby skills with these coding challenges

require 'json'
require 'benchmark'

class RubyChallenges
  def initialize
    @solved_challenges = []
    @current_challenge = 1
    @total_challenges = 20
  end
  
  def start_challenges
    puts "🎯 Ruby Programming Challenges"
    puts "=" * 35
    puts "Test your Ruby skills with these coding challenges!"
    puts "Difficulty: Beginner to Advanced"
    puts ""
    
    show_challenge_menu
  end
  
  def show_challenge_menu
    puts "\n📋 Challenge Categories:"
    puts "1. Beginner Challenges (1-5)"
    puts "2. Intermediate Challenges (6-10)"
    puts "3. Advanced Challenges (11-15)"
    puts "4. Expert Challenges (16-20)"
    puts "5. Random Challenge"
    puts "6. View Progress"
    puts "0. Exit"
    
    print "\nChoose an option (0-6): "
    choice = gets.chomp.to_i
    
    case choice
    when 1
      show_beginner_challenges
    when 2
      show_intermediate_challenges
    when 3
      show_advanced_challenges
    when 4
      show_expert_challenges
    when 5
      show_random_challenge
    when 6
      show_progress
    when 0
      puts "\n👋 Thanks for completing the challenges!"
      exit
    else
      puts "Invalid choice. Please try again."
      show_challenge_menu
    end
  end
  
  def show_beginner_challenges
    challenges = [
      { id: 1, title: "Hello World Variations", difficulty: "Beginner" },
      { id: 2, title: "Basic Calculator", difficulty: "Beginner" },
      { id: 3, title: "String Reverser", difficulty: "Beginner" },
      { id: 4, title: "Number Guesser", difficulty: "Beginner" },
      { id: 5, title: "Simple To-Do List", difficulty: "Beginner" }
    ]
    
    puts "\n🌱 Beginner Challenges:"
    challenges.each do |challenge|
      status = @solved_challenges.include?(challenge[:id]) ? "✅" : "⭕"
      puts "#{status} #{challenge[:id]}. #{challenge[:title]}"
    end
    
    print "\nChoose a challenge (1-5): "
    choice = gets.chomp.to_i
    
    if choice >= 1 && choice <= 5
      run_challenge(choice)
    else
      puts "Invalid choice."
      show_beginner_challenges
    end
  end
  
  def show_intermediate_challenges
    challenges = [
      { id: 6, title: "Palindrome Checker", difficulty: "Intermediate" },
      { id: 7, title: "Fibonacci Sequence", difficulty: "Intermediate" },
      { id: 8, title: "Prime Number Finder", difficulty: "Intermediate" },
      { id: 9, title: "Basic Sorting Algorithm", difficulty: "Intermediate" },
      { id: 10, title: "Simple File Parser", difficulty: "Intermediate" }
    ]
    
    puts "\n🚀 Intermediate Challenges:"
    challenges.each do |challenge|
      status = @solved_challenges.include?(challenge[:id]) ? "✅" : "⭕"
      puts "#{status} #{challenge[:id]}. #{challenge[:title]}"
    end
    
    print "\nChoose a challenge (6-10): "
    choice = gets.chomp.to_i
    
    if choice >= 6 && choice <= 10
      run_challenge(choice)
    else
      puts "Invalid choice."
      show_intermediate_challenges
    end
  end
  
  def show_advanced_challenges
    challenges = [
      { id: 11, title: "Binary Search Tree", difficulty: "Advanced" },
      { id: 12, title: "Linked List Implementation", difficulty: "Advanced" },
      { id: 13, title: "Simple Web Server", difficulty: "Advanced" },
      { id: 14, title: "JSON Parser", difficulty: "Advanced" },
      { id: 15, title: "Basic Encryption", difficulty: "Advanced" }
    ]
    
    puts "\n🔥 Advanced Challenges:"
    challenges.each do |challenge|
      status = @solved_challenges.include?(challenge[:id]) ? "✅" : "⭕"
      puts "#{status} #{challenge[:id]}. #{challenge[:title]}"
    end
    
    print "\nChoose a challenge (11-15): "
    choice = gets.chomp.to_i
    
    if choice >= 11 && choice <= 15
      run_challenge(choice)
    else
      puts "Invalid choice."
      show_advanced_challenges
    end
  end
  
  def show_expert_challenges
    challenges = [
      { id: 16, title: "Simple Database", difficulty: "Expert" },
      { id: 17, title: "Basic Web Framework", difficulty: "Expert" },
      { id: 18, title: "Template Engine", difficulty: "Expert" },
      { id: 19, title: "ORM Implementation", difficulty: "Expert" },
      { id: 20, title: "Mini Language Parser", difficulty: "Expert" }
    ]
    
    puts "\n🏆 Expert Challenges:"
    challenges.each do |challenge|
      status = @solved_challenges.include?(challenge[:id]) ? "✅" : "⭕"
      puts "#{status} #{challenge[:id]}. #{challenge[:title]}"
    end
    
    print "\nChoose a challenge (16-20): "
    choice = gets.chomp.to_i
    
    if choice >= 16 && choice <= 20
      run_challenge(choice)
    else
      puts "Invalid choice."
      show_expert_challenges
    end
  end
  
  def show_random_challenge
    challenge_id = rand(1..20)
    puts "\n🎲 Random Challenge: #{challenge_id}"
    run_challenge(challenge_id)
  end
  
  def run_challenge(challenge_id)
    case challenge_id
    when 1
      challenge_1_hello_variations
    when 2
      challenge_2_calculator
    when 3
      challenge_3_string_reverser
    when 4
      challenge_4_number_guesser
    when 5
      challenge_5_todo_list
    when 6
      challenge_6_palindrome
    when 7
      challenge_7_fibonacci
    when 8
      challenge_8_primes
    when 9
      challenge_9_sorting
    when 10
      challenge_10_file_parser
    when 11
      challenge_11_binary_tree
    when 12
      challenge_12_linked_list
    when 13
      challenge_13_web_server
    when 14
      challenge_14_json_parser
    when 15
      challenge_15_encryption
    when 16
      challenge_16_database
    when 17
      challenge_17_web_framework
    when 18
      challenge_18_template_engine
    when 19
      challenge_19_orm
    when 20
      challenge_20_language_parser
    end
  end
  
  def challenge_1_hello_variations
    puts "\n🌱 Challenge 1: Hello World Variations"
    puts "=" * 40
    puts "Create different ways to output 'Hello, World!'"
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Use puts"
    puts "2. Use print"
    puts "3. Use string interpolation"
    puts "4. Use string concatenation"
    puts "5. Use a method"
    puts ""
    
    puts "💻 Example solution:"
    puts 'puts "Hello, World!"'
    puts 'print "Hello, World!\n"'
    puts 'name = "World"'
    puts 'puts "Hello, #{name}!"'
    puts 'puts "Hello, " + "World!"'
    puts 'def hello'
    puts '  "Hello, World!"'
    puts 'end'
    puts 'puts hello'
    puts ""
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(1)
  end
  
  def challenge_2_calculator
    puts "\n🌱 Challenge 2: Basic Calculator"
    puts "=" * 35
    puts "Create a simple calculator that can add, subtract, multiply, and divide."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Create methods for each operation"
    puts "2. Handle division by zero"
    puts "3. Return results as numbers"
    puts "4. Test with sample inputs"
    puts ""
    
    puts "💻 Example solution:"
    puts 'def add(a, b) a + b end'
    puts 'def subtract(a, b) a - b end'
    puts 'def multiply(a, b) a * b end'
    puts 'def divide(a, b)'
    puts '  return "Cannot divide by zero" if b == 0'
    puts '  a / b.to_f'
    puts 'end'
    puts ""
    
    puts "🎯 Test:"
    puts "add(5, 3) = #{add(5, 3)}"
    puts "subtract(10, 4) = #{subtract(10, 4)}"
    puts "multiply(6, 7) = #{multiply(6, 7)}"
    puts "divide(15, 3) = #{divide(15, 3)}"
    puts "divide(10, 0) = #{divide(10, 0)}"
    puts ""
    
    # Define methods for demonstration
    def add(a, b) a + b end
    def subtract(a, b) a - b end
    def multiply(a, b) a * b end
    def divide(a, b)
      return "Cannot divide by zero" if b == 0
      a / b.to_f
    end
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(2)
  end
  
  def challenge_3_string_reverser
    puts "\n🌱 Challenge 3: String Reverser"
    puts "=" * 35
    puts "Create a method that reverses a string in different ways."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Use reverse method"
    puts "2. Use manual loop"
    puts "3. Use recursion"
    puts "4. Handle edge cases"
    puts ""
    
    puts "💻 Example solution:"
    puts 'def reverse_builtin(str) str.reverse end'
    puts 'def reverse_manual(str)'
    puts '  reversed = ""'
    puts '  str.chars.each { |char| reversed = char + reversed }'
    puts '  reversed'
    puts 'end'
    puts 'def reverse_recursive(str)'
    puts '  return str if str.length <= 1'
    puts '  reverse_recursive(str[1..-1]) + str[0]'
    puts 'end'
    puts ""
    
    # Demonstrate solutions
    def reverse_builtin(str) str.reverse end
    def reverse_manual(str)
      reversed = ""
      str.chars.each { |char| reversed = char + reversed }
      reversed
    end
    def reverse_recursive(str)
      return str if str.length <= 1
      reverse_recursive(str[1..-1]) + str[0]
    end
    
    test_string = "Ruby"
    puts "🎯 Test with '#{test_string}':"
    puts "Builtin: #{reverse_builtin(test_string)}"
    puts "Manual: #{reverse_manual(test_string)}"
    puts "Recursive: #{reverse_recursive(test_string)}"
    puts ""
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(3)
  end
  
  def challenge_4_number_guesser
    puts "\n🌱 Challenge 4: Number Guesser"
    puts "=" * 35
    puts "Create a number guessing game."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Generate random number (1-100)"
    puts "2. Get user input"
    puts "3. Provide hints (too high/too low)"
    puts "4. Track number of guesses"
    puts "5. Handle invalid input"
    puts ""
    
    puts "💻 Example solution:"
    puts 'def play_game'
    puts '  secret = rand(1..100)'
    puts '  guesses = 0'
    puts '  loop do'
    puts '    print "Guess (1-100): "' 
    puts '    guess = gets.chomp.to_i'
    puts '    guesses += 1'
    puts '    break if guess == secret'
    puts '    puts guess < secret ? "Too low!" : "Too high!"'
    puts '  end'
    puts '  puts "You won in #{guesses} guesses!"'
    puts 'end'
    puts ""
    
    puts "🎮 Let's play a quick demo:"
    secret = rand(1..10)
    puts "I'm thinking of a number between 1 and 10..."
    
    3.times do |attempt|
      print "Guess #{attempt + 1}: "
      guess = gets.chomp.to_i
      
      if guess == secret
        puts "🎉 You got it! The number was #{secret}!"
        break
      else
        hint = guess < secret ? "higher" : "lower"
        puts "Try #{hint}!"
      end
    end
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(4)
  end
  
  def challenge_5_todo_list
    puts "\n🌱 Challenge 5: Simple To-Do List"
    puts "=" * 35
    puts "Create a command-line to-do list application."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Add tasks"
    puts "2. List all tasks"
    puts "3. Mark tasks as complete"
    puts "4. Delete tasks"
    puts "5. Save to file"
    puts ""
    
    puts "💻 Example solution:"
    puts 'class TodoList'
    puts '  def initialize'
    puts '    @tasks = []'
    puts '  end'
    puts '  def add_task(task)'
    puts '    @tasks << { text: task, completed: false }'
    puts '  end'
    puts '  def list_tasks'
    puts '    @tasks.each_with_index do |task, i|'
    puts '      status = task[:completed] ? "✅" : "⭕"'
    puts '      puts "#{i + 1}. #{status} #{task[:text]}"'
    puts '    end'
    puts '  end'
    puts 'end'
    puts ""
    
    # Demonstrate the TodoList
    class TodoList
      def initialize
        @tasks = []
      end
      
      def add_task(task)
        @tasks << { text: task, completed: false }
      end
      
      def list_tasks
        @tasks.each_with_index do |task, i|
          status = task[:completed] ? "✅" : "⭕"
          puts "#{i + 1}. #{status} #{task[:text]}"
        end
      end
      
      def complete_task(index)
        if index >= 1 && index <= @tasks.length
          @tasks[index - 1][:completed] = true
          puts "✅ Task completed!"
        else
          puts "Invalid task number."
        end
      end
    end
    
    puts "🎯 Demo:"
    todo = TodoList.new
    todo.add_task("Learn Ruby")
    todo.add_task("Build a project")
    todo.add_task("Contribute to open source")
    
    puts "Your tasks:"
    todo.list_tasks
    
    todo.complete_task(2)
    puts "\nAfter completing task 2:"
    todo.list_tasks
    puts ""
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(5)
  end
  
  def challenge_6_palindrome
    puts "\n🚀 Challenge 6: Palindrome Checker"
    puts "=" * 40
    puts "Create a method to check if a string is a palindrome."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Ignore case"
    puts "2. Ignore punctuation and spaces"
    puts "3. Handle empty strings"
    puts "4. Return boolean"
    puts ""
    
    puts "💻 Example solution:"
    puts 'def palindrome?(str)'
    puts '  cleaned = str.downcase.gsub(/[^a-z0-9]/, "")'
    puts '  cleaned == cleaned.reverse'
    puts 'end'
    puts ""
    
    def palindrome?(str)
      cleaned = str.downcase.gsub(/[^a-z0-9]/, "")
      cleaned == cleaned.reverse
    end
    
    puts "🎯 Test cases:"
    test_cases = [
      "racecar",
      "A man, a plan, a canal: Panama",
      "Hello",
      "",
      "12321"
    ]
    
    test_cases.each do |test|
      result = palindrome?(test)
      puts "'#{test}' => #{result}"
    end
    puts ""
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(6)
  end
  
  def challenge_7_fibonacci
    puts "\n🚀 Challenge 7: Fibonacci Sequence"
    puts "=" * 40
    puts "Generate Fibonacci numbers using different approaches."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Iterative approach"
    puts "2. Recursive approach"
    puts "3. Memoized approach"
    puts "4. Handle large numbers"
    puts ""
    
    puts "💻 Example solution:"
    puts 'def fib_iterative(n)'
    puts '  return n if n <= 1'
    puts '  a, b = 0, 1'
    puts '  (n - 1).times { a, b = b, a + b }'
    puts '  b'
    puts 'end'
    puts ""
    
    def fib_iterative(n)
      return n if n <= 1
      a, b = 0, 1
      (n - 1).times { a, b = b, a + b }
      b
    end
    
    puts "🎯 First 10 Fibonacci numbers:"
    10.times do |i|
      puts "F(#{i}) = #{fib_iterative(i)}"
    end
    puts ""
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(7)
  end
  
  def challenge_8_primes
    puts "\n🚀 Challenge 8: Prime Number Finder"
    puts "=" * 40
    puts "Create methods to find and test prime numbers."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Test if number is prime"
    puts "2. Find primes up to n"
    puts "3. Optimize for performance"
    puts "4. Handle edge cases"
    puts ""
    
    puts "💻 Example solution:"
    puts 'def prime?(n)'
    puts '  return false if n <= 1'
    puts '  return true if n <= 3'
    puts '  return false if n % 2 == 0 || n % 3 == 0'
    puts '  i = 5'
    puts '  while i * i <= n'
    puts '    return false if n % i == 0 || n % (i + 2) == 0'
    puts '    i += 6'
    puts '  end'
    puts '  true'
    puts 'end'
    puts ""
    
    def prime?(n)
      return false if n <= 1
      return true if n <= 3
      return false if n % 2 == 0 || n % 3 == 0
      i = 5
      while i * i <= n
        return false if n % i == 0 || n % (i + 2) == 0
        i += 6
      end
      true
    end
    
    puts "🎯 First 20 primes:"
    primes = []
    num = 2
    while primes.length < 20
      primes << num if prime?(num)
      num += 1
    end
    puts primes.join(", ")
    puts ""
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(8)
  end
  
  def challenge_9_sorting
    puts "\n🚀 Challenge 9: Basic Sorting Algorithm"
    puts "=" * 45
    puts "Implement a sorting algorithm from scratch."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Don't use Array#sort"
    puts "2. Implement bubble sort"
    puts "3. Test with different arrays"
    puts "4. Measure performance"
    puts ""
    
    puts "💻 Example solution:"
    puts 'def bubble_sort(arr)'
    puts '  n = arr.length'
    puts '  n.times do |i|'
    puts '    (n - i - 1).times do |j|'
    puts '      arr[j], arr[j + 1] = arr[j + 1], arr[j] if arr[j] > arr[j + 1]'
    puts '    end'
    puts '  end'
    puts '  arr'
    puts 'end'
    puts ""
    
    def bubble_sort(arr)
      n = arr.length
      n.times do |i|
        (n - i - 1).times do |j|
          arr[j], arr[j + 1] = arr[j + 1], arr[j] if arr[j] > arr[j + 1]
        end
      end
      arr
    end
    
    puts "🎯 Test bubble sort:"
    test_arrays = [
      [5, 3, 8, 4, 2],
      [1, 2, 3, 4, 5],
      [5, 4, 3, 2, 1],
      [10, -1, 2, 8, 0]
    ]
    
    test_arrays.each do |arr|
      sorted = bubble_sort(arr.dup)
      puts "Original: #{arr}"
      puts "Sorted:   #{sorted}"
      puts ""
    end
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(9)
  end
  
  def challenge_10_file_parser
    puts "\n🚀 Challenge 10: Simple File Parser"
    puts "=" * 40
    puts "Parse a CSV-like file and extract data."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Read file line by line"
    puts "2. Parse comma-separated values"
    puts "3. Handle quoted fields"
    puts "4. Return structured data"
    puts ""
    
    puts "💻 Example solution:"
    puts 'def parse_csv(content)'
    puts '  lines = content.split("\n")'
    puts '  headers = lines.first.split(",").map(&:strip)'
    puts '  data = []'
    puts '  lines[1..-1].each do |line|'
    puts '    values = line.split(",").map(&:strip)'
    puts '    data << Hash[headers.zip(values)]'
    puts '  end'
    puts '  data'
    puts 'end'
    puts ""
    
    def parse_csv(content)
      lines = content.split("\n")
      headers = lines.first.split(",").map(&:strip)
      data = []
      lines[1..-1].each do |line|
        values = line.split(",").map(&:strip)
        data << Hash[headers.zip(values)]
      end
      data
    end
    
    puts "🎯 Test CSV parsing:"
    csv_content = "name,age,city\nAlice,25,New York\nBob,30,Chicago\nCharlie,35,Los Angeles"
    parsed = parse_csv(csv_content)
    
    parsed.each do |row|
      puts "#{row['name']} is #{row['age']} years old and lives in #{row['city']}"
    end
    puts ""
    
    puts "✅ Try it yourself!"
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(10)
  end
  
  # Advanced challenges (simplified for demonstration)
  def challenge_11_binary_tree
    puts "\n🔥 Challenge 11: Binary Search Tree"
    puts "=" * 40
    puts "Implement a binary search tree data structure."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Node class with value and left/right children"
    puts "2. Insert method"
    puts "3. Search method"
    puts "4. In-order traversal"
    puts ""
    
    puts "💻 Example solution:"
    puts 'class Node'
    puts '  attr_accessor :value, :left, :right'
    puts '  def initialize(value)'
    puts '    @value = value'
    puts '    @left = nil'
    puts '    @right = nil'
    puts '  end'
    puts 'end'
    puts ""
    
    puts 'class BinarySearchTree'
    puts '  def initialize'
    puts '    @root = nil'
    puts '  end'
    puts '  def insert(value)'
    puts '    @root = insert_recursive(@root, value)'
    puts '  end'
    puts 'end'
    puts ""
    
    puts "✅ This is an advanced challenge!"
    puts "Research binary search trees and try implementing one."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(11)
  end
  
  def challenge_12_linked_list
    puts "\n🔥 Challenge 12: Linked List Implementation"
    puts "=" * 45
    puts "Implement a singly linked list from scratch."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Node class with value and next pointer"
    puts "2. Append method"
    puts "3. Prepend method"
    puts "4. Search method"
    puts "5. Delete method"
    puts ""
    
    puts "✅ This is an advanced challenge!"
    puts "Research linked lists and try implementing one."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(12)
  end
  
  def challenge_13_web_server
    puts "\n🔥 Challenge 13: Simple Web Server"
    puts "=" * 40
    puts "Create a basic HTTP server using sockets."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Listen on port 8080"
    puts "2. Handle GET requests"
    puts "3. Return HTTP responses"
    puts "4. Serve HTML content"
    puts ""
    
    puts "✅ This is an advanced challenge!"
    puts "Research Ruby sockets and HTTP protocol."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(13)
  end
  
  def challenge_14_json_parser
    puts "\n🔥 Challenge 14: JSON Parser"
    puts "=" * 35
    puts "Parse JSON strings without using the JSON library."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Parse objects {key: value}"
    puts "2. Parse arrays [1, 2, 3]"
    puts "3. Handle strings, numbers, booleans"
    puts "4. Return Ruby objects"
    puts ""
    
    puts "✅ This is an advanced challenge!"
    puts "Research JSON format and parsing techniques."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(14)
  end
  
  def challenge_15_encryption
    puts "\n🔥 Challenge 15: Basic Encryption"
    puts "=" * 40
    puts "Implement a simple encryption/decryption system."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Caesar cipher"
    puts "2. XOR encryption"
    puts "3. Key generation"
    puts "4. Encrypt/decrypt methods"
    puts ""
    
    puts "✅ This is an advanced challenge!"
    puts "Research encryption algorithms and implement one."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(15)
  end
  
  # Expert challenges (simplified descriptions)
  def challenge_16_database
    puts "\n🏆 Challenge 16: Simple Database"
    puts "=" * 35
    puts "Create a basic key-value database with persistence."
    puts ""
    puts "💡 Requirements:"
    puts "1. Store data in memory"
    puts "2. Save to disk"
    puts "3. Load from disk"
    puts "4. CRUD operations"
    puts ""
    
    puts "✅ This is an expert challenge!"
    puts "Research database concepts and try implementing one."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(16)
  end
  
  def challenge_17_web_framework
    puts "\n🏆 Challenge 17: Basic Web Framework"
    puts "=" * 40
    puts "Create a minimal web framework similar to Sinatra."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Route handling"
    puts "2. Request/response objects"
    puts "3. Template rendering"
    puts "4. Middleware support"
    puts ""
    
    puts "✅ This is an expert challenge!"
    puts "Research web frameworks and HTTP protocol."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(17)
  end
  
  def challenge_18_template_engine
    puts "\n🏆 Challenge 18: Template Engine"
    puts "=" * 40
    puts "Create a template engine for rendering dynamic content."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Variable substitution"
    puts "2. Loop constructs"
    puts "3. Conditionals"
    puts "4. Template inheritance"
    puts ""
    
    puts "✅ This is an expert challenge!"
    puts "Research template engines and parsing techniques."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(18)
  end
  
  def challenge_19_orm
    puts "\n🏆 Challenge 19: ORM Implementation"
    puts "=" * 40
    puts "Create a simple Object-Relational Mapping system."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Model classes"
    puts "2. Query builder"
    puts "3. Database connections"
    puts "4. Validation"
    puts ""
    
    puts "✅ This is an expert challenge!"
    puts "Research ORM patterns and database interactions."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(19)
  end
  
  def challenge_20_language_parser
    puts "\n🏆 Challenge 20: Mini Language Parser"
    puts "=" * 45
    puts "Create a parser for a simple programming language."
    puts ""
    
    puts "💡 Requirements:"
    puts "1. Lexer (tokenizer)"
    puts "2. Parser (AST generation)"
    puts "3. Interpreter (execution)"
    puts "4. Error handling"
    puts ""
    
    puts "✅ This is the ultimate expert challenge!"
    puts "Research compiler design and parsing theory."
    puts "Press Enter when you're ready to mark as complete..."
    gets
    
    mark_challenge_complete(20)
  end
  
  def mark_challenge_complete(challenge_id)
    unless @solved_challenges.include?(challenge_id)
      @solved_challenges << challenge_id
      puts "✅ Challenge #{challenge_id} marked as complete!"
      save_progress
    else
      puts "✅ Challenge #{challenge_id} was already completed."
    end
    
    puts "\n📊 Progress: #{@solved_challenges.length}/#{@total_challenges} challenges completed"
    
    if @solved_challenges.length == @total_challenges
      puts "\n🎉 CONGRATULATIONS! You've completed all challenges!"
      puts "You're a Ruby master! 🏆"
    end
    
    puts "\nPress Enter to continue..."
    gets
    
    show_challenge_menu
  end
  
  def show_progress
    puts "\n📊 Your Progress"
    puts "=" * 20
    puts "Completed: #{@solved_challenges.length}/#{@total_challenges}"
    puts "Percentage: #{(@solved_challenges.length.to_f / @total_challenges * 100).round(1)}%"
    puts ""
    
    if @solved_challenges.any?
      puts "✅ Completed challenges:"
      @solved_challenges.sort.each { |id| puts "  • Challenge #{id}" }
    else
      puts "⭕ No challenges completed yet."
    end
    
    puts "\n🎯 Keep going! You're doing great!"
    puts "Press Enter to continue..."
    gets
    
    show_challenge_menu
  end
  
  def save_progress
    progress = {
      solved_challenges: @solved_challenges,
      total_challenges: @total_challenges,
      completed_at: Time.now
    }
    
    File.write('challenges_progress.json', JSON.pretty_generate(progress))
  end
  
  def load_progress
    if File.exist?('challenges_progress.json')
      progress = JSON.parse(File.read('challenges_progress.json'))
      @solved_challenges = progress['solved_challenges'] || []
    end
  end
end

# Start the challenges
if __FILE__ == $0
  challenges = RubyChallenges.new
  challenges.load_progress
  challenges.start_challenges
end
