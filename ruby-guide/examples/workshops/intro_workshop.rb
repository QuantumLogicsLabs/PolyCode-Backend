#!/usr/bin/env ruby
# Introduction Workshop - Your First Ruby Program
# Interactive workshop for complete beginners

require 'json'

class IntroWorkshop
  def initialize
    @exercises = []
    @progress = {}
  end
  
  def start_workshop
    puts "🎓 Welcome to Your First Ruby Workshop!"
    puts "=" * 50
    puts "This interactive workshop will teach you Ruby basics step by step."
    puts ""
    
    show_menu
  end
  
  def show_menu
    puts "\n📚 Workshop Menu:"
    puts "1. Hello World - Your first program"
    puts "2. Variables and Data Types"
    puts "3. Methods and Functions"
    puts "4. Control Flow"
    puts "5. Arrays and Hashes"
    puts "6. Object-Oriented Basics"
    puts "7. File Operations"
    puts "8. Error Handling"
    puts "9. Putting It All Together"
    puts "10. Workshop Summary"
    puts "0. Exit"
    
    print "\nChoose an exercise (1-10): "
    choice = gets.chomp.to_i
    
    case choice
    when 1
      exercise_1_hello_world
    when 2
      exercise_2_variables
    when 3
      exercise_3_methods
    when 4
      exercise_4_control_flow
    when 5
      exercise_5_arrays_hashes
    when 6
      exercise_6_oop_basics
    when 7
      exercise_7_file_operations
    when 8
      exercise_8_error_handling
    when 9
      exercise_9_final_project
    when 10
      show_summary
    when 0
      puts "\n👋 Thanks for completing the workshop!"
      exit
    else
      puts "Invalid choice. Please try again."
      show_menu
    end
  end
  
  def exercise_1_hello_world
    puts "\n🌟 Exercise 1: Hello World"
    puts "=" * 30
    puts "Let's write your first Ruby program!"
    puts ""
    
    puts "Ruby makes it easy to display text. The 'puts' command prints text to the screen."
    puts ""
    
    # Show the code
    puts "💻 Code:"
    puts 'puts "Hello, Ruby World!"'
    puts ""
    
    # Execute the code
    puts "🎯 Output:"
    puts "Hello, Ruby World!"
    puts ""
    
    puts "🎉 Congratulations! You've written your first Ruby program!"
    puts ""
    
    # Interactive part
    puts "💬 Now you try! Type your own message:"
    user_input = gets.chomp
    puts "🎯 Your output:"
    puts user_input
    puts ""
    
    puts "✅ Great job! Let's move to the next exercise."
    puts "Press Enter to continue..."
    gets
    
    show_menu
  end
  
  def exercise_2_variables
    puts "\n🌟 Exercise 2: Variables and Data Types"
    puts "=" * 40
    puts "Variables store data that can change during program execution."
    puts ""
    
    puts "💻 Code examples:"
    puts 'name = "Alice"'
    puts 'age = 25'
    puts 'height = 5.7'
    puts 'is_student = true'
    puts ""
    
    # Execute the code
    name = "Alice"
    age = 25
    height = 5.7
    is_student = true
    
    puts "🎯 Output:"
    puts "Name: #{name} (String)"
    puts "Age: #{age} (Integer)"
    puts "Height: #{height} (Float)"
    puts "Student: #{is_student} (Boolean)"
    puts ""
    
    puts "📚 Ruby Data Types:"
    puts "• String: Text in quotes"
    puts "• Integer: Whole numbers"
    puts "• Float: Decimal numbers"
    puts "• Boolean: true/false"
    puts "• Array: Lists of values"
    puts "• Hash: Key-value pairs"
    puts ""
    
    # Interactive exercise
    puts "💬 Try it yourself!"
    puts "Create a variable called 'city' with your city name:"
    city = gets.chomp
    
    puts "Create a variable called 'population' with a number:"
    population = gets.chomp.to_i
    
    puts "🎯 Your variables:"
    puts "City: #{city} (#{city.class})"
    puts "Population: #{population} (#{population.class})"
    puts ""
    
    puts "✅ Excellent! Variables are fundamental to programming."
    puts "Press Enter to continue..."
    gets
    
    show_menu
  end
  
  def exercise_3_methods
    puts "\n🌟 Exercise 3: Methods and Functions"
    puts "=" * 35
    puts "Methods are reusable blocks of code that perform specific tasks."
    puts ""
    
    puts "💻 Code examples:"
    puts 'def greet(name)'
    puts '  "Hello, #{name}!"'
    puts 'end'
    puts ''
    puts 'def add(a, b)'
    puts '  a + b'
    puts 'end'
    puts ""
    
    # Define and use methods
    def greet(name)
      "Hello, #{name}!"
    end
    
    def add(a, b)
      a + b
    end
    
    puts "🎯 Output:"
    puts "Greeting: #{greet('Ruby')}"
    puts "Addition: #{add(5, 3)}"
    puts ""
    
    puts "📚 Method Benefits:"
    puts "• Reusability - Use the same code multiple times"
    puts "• Organization - Break complex problems into small pieces"
    puts "• Testing - Test each method separately"
    puts ""
    
    # Interactive exercise
    puts "💬 Create your own method!"
    puts "Define a method called 'calculate_area' that takes width and height:"
    
    puts "def calculate_area(width, height)"
    puts "  # Your code here"
    puts "end"
    puts ""
    puts "Now run it:"
    puts "area = calculate_area(10, 5)"
    puts "puts \"Area: #{area}\""
    puts ""
    
    puts "✅ Methods are the building blocks of Ruby programs!"
    puts "Press Enter to continue..."
    gets
    
    show_menu
  end
  
  def exercise_4_control_flow
    puts "\n🌟 Exercise 4: Control Flow"
    puts "=" * 30
    puts "Control flow lets your program make decisions and repeat actions."
    puts ""
    
    puts "💻 Code examples:"
    puts 'if age >= 18'
    puts '  puts "You can vote!"'
    puts 'else'
    puts '  puts "Too young to vote"'
    puts 'end'
    puts ''
    puts '5.times do |i|'
    puts '  puts "Iteration #{i}"'
    puts 'end'
    puts ""
    
    # Execute examples
    age = 20
    if age >= 18
      puts "🎯 If/Else: You can vote!"
    else
      puts "🎯 If/Else: Too young to vote"
    end
    
    puts ""
    puts "🎯 Loop:"
    3.times do |i|
      puts "Iteration #{i}"
    end
    puts ""
    
    puts "📚 Control Flow Types:"
    puts "• if/elsif/else - Conditional logic"
    puts "• case/when - Multiple conditions"
    puts "• while/until - Repeat while condition is true"
    puts "• for/each - Iterate over collections"
    puts "• break/next - Control loop behavior"
    puts ""
    
    # Interactive exercise
    puts "💬 Try a loop!"
    puts "Create a loop that counts from 1 to 5:"
    
    puts "1.upto(5) do |number|"
    puts "  puts \"Count: #{number}\""
    puts "end"
    puts ""
    
    # Execute the loop
    1.upto(3) do |number|
      puts "Count: #{number}"
    end
    puts ""
    
    puts "✅ Control flow gives your programs intelligence!"
    puts "Press Enter to continue..."
    gets
    
    show_menu
  end
  
  def exercise_5_arrays_hashes
    puts "\n🌟 Exercise 5: Arrays and Hashes"
    puts "=" * 35
    puts "Arrays store ordered lists, while hashes store key-value pairs."
    puts ""
    
    puts "💻 Code examples:"
    puts 'fruits = ["apple", "banana", "orange"]'
    puts 'puts fruits[0]  # First element'
    puts 'fruits << "grape"  # Add element'
    puts ''
    puts 'person = {'
    puts '  name: "John",'
    puts '  age: 30,'
    puts '  city: "New York"'
    puts '}'
    puts 'puts person[:name]  # Access by key'
    puts ""
    
    # Execute examples
    fruits = ["apple", "banana", "orange"]
    person = { name: "John", age: 30, city: "New York" }
    
    puts "🎯 Array:"
    puts "First fruit: #{fruits[0]}"
    puts "All fruits: #{fruits.join(', ')}"
    puts ""
    
    puts "🎯 Hash:"
    puts "Name: #{person[:name]}"
    puts "Age: #{person[:age]}"
    puts "City: #{person[:city]}"
    puts ""
    
    puts "📚 Array Methods:"
    puts "• [] - Create empty array"
    puts "• << - Add element"
    puts "• .each - Iterate over elements"
    puts "• .map - Transform elements"
    puts "• .select - Filter elements"
    puts ""
    
    puts "📚 Hash Methods:"
    puts "• {} - Create empty hash"
    puts "• [:key] - Access value"
    puts "• .keys - Get all keys"
    puts "• .values - Get all values"
    puts "• .each - Iterate over key-value pairs"
    puts ""
    
    # Interactive exercise
    puts "💬 Create your own array and hash!"
    
    favorite_colors = ["red", "blue", "green"]
    puts "Your colors: #{favorite_colors.join(', ')}"
    
    puts "Add another color:"
    new_color = gets.chomp
    favorite_colors << new_color
    puts "Updated colors: #{favorite_colors.join(', ')}"
    puts ""
    
    puts "✅ Arrays and hashes are essential data structures!"
    puts "Press Enter to continue..."
    gets
    
    show_menu
  end
  
  def exercise_6_oop_basics
    puts "\n🌟 Exercise 6: Object-Oriented Basics"
    puts "=" * 40
    puts "Object-oriented programming (OOP) organizes code into objects."
    puts ""
    
    puts "💻 Code examples:"
    puts 'class Animal'
    puts '  def initialize(name)'
    puts '    @name = name'
    puts '  end'
    puts ''
    puts '  def speak'
    puts '    "#{@name} makes a sound!"'
    puts '  end'
    puts 'end'
    puts ''
    puts 'dog = Animal.new("Buddy")'
    puts 'dog.speak'
    puts ""
    
    # Define and use class
    class Animal
      def initialize(name)
        @name = name
      end
      
      def speak
        "#{@name} makes a sound!"
      end
    end
    
    dog = Animal.new("Buddy")
    
    puts "🎯 Output:"
    puts dog.speak
    puts ""
    
    puts "📚 OOP Concepts:"
    puts "• Class - Blueprint for objects"
    puts "• Object - Instance of a class"
    puts "• Method - Behavior of an object"
    puts "• Instance Variable - Data stored in an object"
    puts "• Inheritance - Classes can inherit from others"
    puts "• Polymorphism - Objects can take different forms"
    puts ""
    
    # Interactive exercise
    puts "💬 Create your own class!"
    puts "Define a 'Car' class with make and model:"
    
    class Car
      def initialize(make, model)
        @make = make
        @model = model
      end
      
      def description
        "#{@make} #{@model}"
      end
    end
    
    my_car = Car.new("Toyota", "Camry")
    puts "Your car: #{my_car.description}"
    puts ""
    
    puts "✅ OOP helps organize complex programs!"
    puts "Press Enter to continue..."
    gets
    
    show_menu
  end
  
  def exercise_7_file_operations
    puts "\n🌟 Exercise 7: File Operations"
    puts "=" * 35
    puts "Ruby makes it easy to read from and write to files."
    puts ""
    
    puts "💻 Code examples:"
    puts '# Write to file'
    puts 'File.write("hello.txt", "Hello, File!")'
    puts ''
    puts '# Read from file'
    puts 'content = File.read("hello.txt")'
    puts 'puts content'
    puts ''
    puts '# Check if file exists'
    puts 'if File.exist?("hello.txt")'
    puts '  puts "File exists!"'
    puts 'end'
    puts ""
    
    # Demonstrate file operations
    filename = "workshop_test.txt"
    
    # Write to file
    File.write(filename, "Hello from the workshop!")
    puts "🎯 Wrote to #{filename}"
    
    # Read from file
    content = File.read(filename)
    puts "🎯 File content: #{content}"
    
    # Check existence
    if File.exist?(filename)
      puts "🎯 File exists: #{filename}"
    end
    
    # Clean up
    File.delete(filename)
    puts "🎯 Cleaned up: deleted #{filename}"
    puts ""
    
    puts "📚 File Operations:"
    puts "• File.read(path) - Read entire file"
    puts "• File.write(path, content) - Write to file"
    puts "• File.exist?(path) - Check if file exists"
    puts "• File.delete(path) - Delete file"
    puts "• File.open(path, mode) - Open file for reading/writing"
    puts ""
    
    puts "⚠️  File modes:"
    puts "• 'r' - Read only"
    puts "• 'w' - Write (overwrite)"
    puts "• 'a' - Append"
    puts "• 'r+' - Read and write"
    puts ""
    
    # Interactive exercise
    puts "💬 Create your own file!"
    puts "Enter a filename for your notes file:"
    notes_file = gets.chomp
    
    puts "Write a note to save:"
    note = gets.chomp
    
    File.write(notes_file, note)
    puts "✅ Saved your note to #{notes_file}"
    
    puts "Press Enter to continue..."
    gets
    
    show_menu
  end
  
  def exercise_8_error_handling
    puts "\n🌟 Exercise 8: Error Handling"
    puts "=" * 35
    puts "Error handling makes your programs robust and user-friendly."
    puts ""
    
    puts "💻 Code examples:"
    puts 'begin'
    puts '  result = 10 / 0'
    puts 'rescue ZeroDivisionError'
    puts '  puts "Cannot divide by zero!"'
    puts 'end'
    puts ''
    puts 'def divide(a, b)'
    puts '  a / b'
    puts 'rescue ZeroDivisionError => e'
    puts '  "Error: #{e.message}"'
    puts 'end'
    puts ""
    
    # Demonstrate error handling
    def safe_divide(a, b)
      a / b
    rescue ZeroDivisionError => e
      "Error: #{e.message}"
    end
    
    puts "🎯 Safe division examples:"
    puts "10 / 2 = #{safe_divide(10, 2)}"
    puts "10 / 0 = #{safe_divide(10, 0)}"
    puts ""
    
    puts "📚 Error Types:"
    puts "• StandardError - Base class for all errors"
    puts "• ZeroDivisionError - Division by zero"
    puts "• NoMethodError - Method doesn't exist"
    puts "• TypeError - Wrong type for operation"
    puts "• ArgumentError - Invalid arguments"
    puts ""
    
    puts "📚 Exception Handling:"
    puts "• begin - Start error handling block"
    puts "• rescue - Handle specific errors"
    puts "• ensure - Code that always runs"
    puts "• raise - Manually trigger an error"
    puts ""
    
    # Interactive exercise
    puts "💬 Try error handling!"
    puts "What happens if we try to access an array element that doesn't exist?"
    
    array = [1, 2, 3]
    begin
      puts "Accessing element at index 10:"
      puts array[10]
    rescue IndexError => e
      puts "Caught error: #{e.message}"
    end
    puts ""
    
    puts "✅ Error handling makes programs reliable!"
    puts "Press Enter to continue..."
    gets
    
    show_menu
  end
  
  def exercise_9_final_project
    puts "\n🌟 Exercise 9: Final Project"
    puts "=" * 30
    puts "Let's build a simple contact book application!"
    puts ""
    
    puts "💻 We'll create a ContactBook class with methods to:"
    puts "• Add contacts"
    puts "• List all contacts"
    puts "• Find contacts"
    puts "• Delete contacts"
    puts ""
    
    # Define ContactBook class
    class ContactBook
      def initialize
        @contacts = []
      end
      
      def add_contact(name, phone, email)
        contact = {
          name: name,
          phone: phone,
          email: email,
          created_at: Time.now
        }
        @contacts << contact
        puts "✅ Added contact: #{name}"
      end
      
      def list_contacts
        if @contacts.empty?
          puts "No contacts found."
        else
          puts "📋 All Contacts:"
          @contacts.each_with_index do |contact, index|
            puts "#{index + 1}. #{contact[:name]} - #{contact[:phone]} - #{contact[:email]}"
          end
        end
      end
      
      def find_contact(name)
        found = @contacts.select { |c| c[:name].downcase.include?(name.downcase) }
        
        if found.empty?
          puts "No contacts found matching '#{name}'."
        else
          puts "🔍 Found #{found.length} contact(s):"
          found.each do |contact|
            puts "  • #{contact[:name]} - #{contact[:phone]}"
          end
        end
      end
      
      def delete_contact(index)
        if index >= 1 && index <= @contacts.length
          removed = @contacts.delete_at(index - 1)
          puts "✅ Deleted contact: #{removed[:name]}"
        else
          puts "❌ Invalid contact number."
        end
      end
    end
    
    # Demonstrate the ContactBook
    contact_book = ContactBook.new
    
    puts "🎯 Creating sample contacts..."
    contact_book.add_contact("Alice Johnson", "555-0123", "alice@email.com")
    contact_book.add_contact("Bob Smith", "555-0456", "bob@email.com")
    contact_book.add_contact("Charlie Brown", "555-0789", "charlie@email.com")
    
    puts "\n📋 Contact List:"
    contact_book.list_contacts
    
    puts "\n🔍 Searching for 'Alice':"
    contact_book.find_contact("Alice")
    
    puts "\n🗑️ Deleting contact #2:"
    contact_book.delete_contact(2)
    
    puts "\n📋 Updated Contact List:"
    contact_book.list_contacts
    puts ""
    
    puts "🎉 Congratulations! You've built your first Ruby application!"
    puts "This project used:"
    puts "• Classes and objects"
    puts "• Arrays and hashes"
    puts "• Methods"
    puts "• Error handling"
    puts "• String interpolation"
    puts "• Time objects"
    puts ""
    
    # Interactive part
    puts "💬 Try adding your own contact!"
    puts "Enter name:"
    name = gets.chomp
    
    puts "Enter phone:"
    phone = gets.chomp
    
    puts "Enter email:"
    email = gets.chomp
    
    contact_book.add_contact(name, phone, email)
    
    puts "\n📋 Updated Contact List:"
    contact_book.list_contacts
    
    puts "✅ Amazing work! You've completed the final project!"
    puts "Press Enter to continue..."
    gets
    
    show_menu
  end
  
  def show_summary
    puts "\n🎓 Workshop Summary"
    puts "=" * 25
    puts "Congratulations! You've completed the Ruby Introduction Workshop!"
    puts ""
    
    puts "🌟 What You Learned:"
    puts "✅ Ruby basics - Hello World"
    puts "✅ Variables and data types"
    puts "✅ Methods and functions"
    puts "✅ Control flow (if/else, loops)"
    puts "✅ Arrays and hashes"
    puts "✅ Object-oriented programming"
    puts "✅ File operations"
    puts "✅ Error handling"
    puts "✅ Built a complete application"
    puts ""
    
    puts "🚀 What's Next:"
    puts "📚 Continue with docs/02-basic-syntax.md"
    puts "💻 Try examples/basic-examples/"
    puts "🎮 Build your own projects"
    puts "🌐 Join the Ruby community"
    puts ""
    
    puts "💎 Ruby Philosophy:"
    puts "\"Ruby is designed to make programmers happy.\""
    puts "• Elegant syntax"
    puts "• Powerful features"
    puts "• Friendly community"
    puts "• Joyful programming"
    puts ""
    
    puts "🎊 You're now a Ruby programmer!"
    puts "Keep practicing and building amazing things!"
    puts ""
    
    puts "👋 Thanks for completing the workshop!"
    puts "Happy coding with Ruby! 💎"
    
    # Save progress
    save_progress
  end
  
  def save_progress
    progress = {
      completed_at: Time.now,
      exercises_completed: 9,
      total_exercises: 9,
      workshop_completed: true
    }
    
    File.write('workshop_progress.json', JSON.pretty_generate(progress))
    puts "💾 Progress saved to workshop_progress.json"
  end
end

# Start the workshop
if __FILE__ == $0
  workshop = IntroWorkshop.new
  workshop.start_workshop
end
