#!/usr/bin/env ruby
# Ruby Interview Preparation Guide
# Common Ruby interview questions with detailed answers

require 'json'

class RubyInterviewPrep
  def initialize
    @categories = {
      "Basics" => 1..5,
      "Object-Oriented" => 6..10,
      "Metaprogramming" => 11..15,
      "Concurrency" => 16..20,
      "Performance" => 21..25,
      "Advanced" => 26..30
    }
    @answered_questions = []
  end
  
  def start_prep
    puts "🎓 Ruby Interview Preparation"
    puts "=" * 35
    puts "Master Ruby interview questions with detailed answers!"
    puts ""
    
    show_menu
  end
  
  def show_menu
    puts "\n📋 Interview Categories:"
    @categories.each_with_index do |(category, range), index|
      puts "#{index + 1}. #{category} (Questions #{range.first}-#{range.last})"
    end
    puts "7. Random Question"
    puts "8. View Progress"
    puts "9. Practice Mode"
    puts "0. Exit"
    
    print "\nChoose an option (0-9): "
    choice = gets.chomp.to_i
    
    case choice
    when 1
      show_category_questions("Basics", @categories["Basics"])
    when 2
      show_category_questions("Object-Oriented", @categories["Object-Oriented"])
    when 3
      show_category_questions("Metaprogramming", @categories["Metaprogramming"])
    when 4
      show_category_questions("Concurrency", @categories["Concurrency"])
    when 5
      show_category_questions("Performance", @categories["Performance"])
    when 6
      show_category_questions("Advanced", @categories["Advanced"])
    when 7
      show_random_question
    when 8
      show_progress
    when 9
      start_practice_mode
    when 0
      puts "\n👋 Good luck with your interviews!"
      exit
    else
      puts "Invalid choice. Please try again."
      show_menu
    end
  end
  
  def show_category_questions(category, range)
    puts "\n📚 #{category} Questions"
    puts "=" * 30
    
    questions = get_questions_by_range(range)
    questions.each_with_index do |question, index|
      status = @answered_questions.include?(question[:id]) ? "✅" : "⭕"
      puts "#{status} #{range.first + index}. #{question[:question]}"
    end
    
    print "\nChoose a question (#{range.first}-#{range.last}): "
    choice = gets.chomp.to_i
    
    if range.include?(choice)
      show_question(choice)
    else
      puts "Invalid choice."
      show_category_questions(category, range)
    end
  end
  
  def show_question(question_id)
    question = get_question_by_id(question_id)
    return unless question
    
    puts "\n❓ Question #{question_id}: #{question[:question]}"
    puts "=" * 50
    puts ""
    
    puts "💡 Think about this question, then press Enter to see the answer..."
    gets
    
    puts "\n💎 Answer:"
    puts question[:answer]
    puts ""
    
    if question[:code_example]
      puts "💻 Code Example:"
      puts question[:code_example]
      puts ""
    end
    
    if question[:follow_up]
      puts "🔄 Follow-up Questions:"
      question[:follow_up].each { |fq| puts "  • #{fq}" }
      puts ""
    end
    
    if question[:key_points]
      puts "🎯 Key Points:"
      question[:key_points].each { |kp| puts "  • #{kp}" }
      puts ""
    end
    
    mark_question_answered(question_id)
    
    puts "\nOptions:"
    puts "1. Next question in category"
    puts "2. Random question"
    puts "3. Back to menu"
    
    print "Choose (1-3): "
    choice = gets.chomp.to_i
    
    case choice
    when 1
      next_in_category = question_id + 1
      if get_question_by_id(next_in_category)
        show_question(next_in_category)
      else
        puts "No more questions in this category."
        show_menu
      end
    when 2
      show_random_question
    when 3
      show_menu
    else
      show_menu
    end
  end
  
  def show_random_question
    question_id = rand(1..30)
    show_question(question_id)
  end
  
  def start_practice_mode
    puts "\n🎯 Practice Mode"
    puts "=" * 20
    puts "Test your knowledge with timed questions!"
    puts ""
    
    score = 0
    total_questions = 5
    
    puts "You'll get #{total_questions} random questions."
    puts "Type your answer and I'll provide feedback."
    puts "Press Enter to start..."
    gets
    
    total_questions.times do |i|
      question_id = rand(1..30)
      question = get_question_by_id(question_id)
      
      puts "\n❓ Question #{i + 1}/#{total_questions}: #{question[:question]}"
      puts "(You have 30 seconds to answer)"
      puts ""
      
      start_time = Time.now
      print "Your answer: "
      user_answer = gets.chomp
      end_time = Time.now
      
      time_taken = end_time - start_time
      
      puts "\n💎 Correct Answer:"
      puts question[:answer]
      puts ""
      
      # Simple feedback based on keywords
      if user_answer.downcase.include?(question[:answer].downcase.split.first)
        puts "✅ Good! Your answer contains key concepts."
        score += 1
      else
        puts "⭕ Not quite. Study the correct answer above."
      end
      
      puts "⏱️ Time taken: #{time_taken.round(1)} seconds"
      
      if time_taken > 30
        puts "⚠️  You took more than 30 seconds."
      end
      
      puts "\nPress Enter for next question..."
      gets
    end
    
    puts "\n📊 Practice Results:"
    puts "Score: #{score}/#{total_questions}"
    puts "Percentage: #{(score.to_f / total_questions * 100).round(1)}%"
    
    case score
    when total_questions
      puts "🏆 Perfect! You're ready for interviews!"
    when total_questions * 0.8..total_questions
      puts "🌟 Excellent work!"
    when total_questions * 0.6..total_questions * 0.8
      puts "👍 Good job! Keep studying."
    else
      puts "📚 Keep practicing and you'll improve!"
    end
    
    puts "\nPress Enter to continue..."
    gets
    show_menu
  end
  
  def show_progress
    puts "\n📊 Your Progress"
    puts "=" * 20
    puts "Answered: #{@answered_questions.length}/30"
    puts "Percentage: #{(@answered_questions.length.to_f / 30 * 100).round(1)}%"
    puts ""
    
    @categories.each do |category, range|
      answered_in_category = @answered_questions.select { |id| range.include?(id) }.length
      total_in_category = range.last - range.first + 1
      percentage = (answered_in_category.to_f / total_in_category * 100).round(1)
      
      puts "#{category}: #{answered_in_category}/#{total_in_category} (#{percentage}%)"
    end
    
    puts "\n🎯 Keep studying! You're doing great!"
    puts "Press Enter to continue..."
    gets
    show_menu
  end
  
  def mark_question_answered(question_id)
    unless @answered_questions.include?(question_id)
      @answered_questions << question_id
      save_progress
    end
  end
  
  def save_progress
    progress = {
      answered_questions: @answered_questions,
      total_questions: 30,
      last_practiced: Time.now
    }
    
    File.write('interview_progress.json', JSON.pretty_generate(progress))
  end
  
  def load_progress
    if File.exist?('interview_progress.json')
      progress = JSON.parse(File.read('interview_progress.json'))
      @answered_questions = progress['answered_questions'] || []
    end
  end
  
  private
  
  def get_question_by_id(id)
    all_questions.find { |q| q[:id] == id }
  end
  
  def get_questions_by_range(range)
    all_questions.select { |q| range.include?(q[:id]) }
  end
  
  def all_questions
    [
      {
        id: 1,
        question: "What is Ruby and who created it?",
        answer: "Ruby is a dynamic, object-oriented programming language created by Yukihiro 'Matz' Matsumoto in 1995. It was designed to be programmer-friendly and emphasizes simplicity and productivity.",
        key_points: [
          "Created by Yukihiro Matsumoto (Matz) in 1995",
          "Dynamic, object-oriented language",
          "Designed for programmer happiness",
          "Open source with a friendly community"
        ]
      },
      {
        id: 2,
        question: "What are the main data types in Ruby?",
        answer: "Ruby has several main data types: Numbers (Integers and Floats), Strings, Symbols, Arrays, Hashes, Booleans, and Nil. Ruby is dynamically typed, so variables don't need type declarations.",
        code_example: "# Different data types\nnumber = 42\nstring = \"Hello\"\nsymbol = :name\narray = [1, 2, 3]\nhash = { key: \"value\" }\nboolean = true\nnil_value = nil",
        key_points: [
          "Dynamic typing - no type declarations needed",
          "Everything is an object in Ruby",
          "Common types: Integer, Float, String, Symbol, Array, Hash, Boolean, Nil",
          "Symbols are immutable strings often used as keys"
        ]
      },
      {
        id: 3,
        question: "What is the difference between nil and false in Ruby?",
        answer: "Both nil and false are 'falsy' values, but they represent different concepts. nil represents 'no value' or 'undefined', while false represents the boolean value false. Only nil and false evaluate to false in boolean contexts.",
        code_example: "nil == false  # => false\n!nil          # => true\n!false        # => true\nnil.to_s      # => \"\" (empty string)\nfalse.to_s    # => \"false\"",
        key_points: [
          "Both are falsy values",
          "nil means 'no value', false means boolean false",
          "Only nil and false evaluate to false",
          "nil.to_s returns empty string, false.to_s returns 'false'"
        ]
      },
      {
        id: 4,
        question: "What are symbols and when should you use them?",
        answer: "Symbols are immutable, unique identifiers that start with a colon. They're often used as hash keys, method names, or identifiers. Unlike strings, symbols are immutable and only stored once in memory, making them efficient for repeated use.",
        code_example: "# Symbols vs Strings\nhash = { :name => \"Alice\" }\nhash = { name: \"Alice\" }  # Modern syntax\n\n# Symbols are immutable\n:name.object_id  # Same ID every time\n\"name\".object_id  # Different ID each time",
        key_points: [
          "Immutable and unique identifiers",
          "Start with colon (:name or name:)",
          "Efficient for repeated use",
          "Commonly used as hash keys"
        ]
      },
      {
        id: 5,
        question: "What is duck typing in Ruby?",
        answer: "Duck typing is a style of typing where an object's suitability is determined by the methods it responds to rather than its class. The principle is 'If it walks like a duck and quacks like a duck, it's a duck.'",
        code_example: "# Duck typing example\nclass Duck\n  def quack; puts \"Quack!\"; end\nend\n\nclass Person\n  def quack; puts \"I'm quacking!\"; end\nend\n\ndef make_it_quack(duck)\n  duck.quack  # Works if object responds to quack\nend",
        key_points: [
          "Type determined by methods, not class",
          "Focus on behavior over inheritance",
          "Enables flexible, polymorphic code",
          "Ruby's dynamic typing enables duck typing"
        ]
      },
      {
        id: 6,
        question: "What is the difference between a class and a module?",
        answer: "Classes are blueprints for creating objects and support inheritance. Modules are collections of methods and constants that can be mixed into classes using mixins. Modules cannot be instantiated but provide shared functionality.",
        code_example: "# Class\nclass Animal\n  def speak; puts \"Animal sound\"; end\nend\n\n# Module\nmodule Swimmable\n  def swim; puts \"Swimming\"; end\nend\n\nclass Fish < Animal\n  include Swimmable  # Mixin module\nend",
        key_points: [
          "Classes create objects, modules provide functionality",
          "Classes support inheritance, modules use mixins",
          "Modules cannot be instantiated",
          "Modules enable multiple inheritance-like behavior"
        ]
      },
      {
        id: 7,
        question: "What is inheritance in Ruby?",
        answer: "Inheritance allows a class to inherit methods and behavior from a parent class. Ruby supports single inheritance, meaning a class can only inherit from one parent class. Use the '<' symbol to inherit from a parent class.",
        code_example: "class Animal\n  def eat; puts \"Eating\"; end\nend\n\nclass Dog < Animal\n  def bark; puts \"Woof!\"; end\nend\n\ndog = Dog.new\ndog.eat  # Inherited method\ndog.bark # Own method",
        key_points: [
          "Single inheritance only",
          "Use '<' symbol for inheritance",
          "Child class inherits parent methods",
          "Can override parent methods"
        ]
      },
      {
        id: 8,
        question: "What are accessors (getters and setters) in Ruby?",
        answer: "Accessors are methods that get and set instance variable values. Ruby provides attr_reader, attr_writer, and attr_accessor to automatically generate these methods. Instance variables start with @ and are private by default.",
        code_example: "class Person\n  attr_accessor :name  # Creates getter and setter\n  attr_reader :age     # Creates only getter\n  attr_writer :email   # Creates only setter\n  \n  def initialize(name)\n    @name = name\n  end\nend",
        key_points: [
          "attr_accessor: getter and setter",
          "attr_reader: getter only",
          "attr_writer: setter only",
          "Instance variables (@var) are private"
        ]
      },
      {
        id: 9,
        question: "What is the difference between class methods and instance methods?",
        answer: "Instance methods operate on object instances and are defined normally. Class methods operate on the class itself and are defined with 'self.' or 'class << self'. Class methods don't need object instantiation.",
        code_example: "class Calculator\n  def add(a, b)        # Instance method\n    a + b\n  end\n  \n  def self.version     # Class method\n    \"1.0.0\"\n  end\nend\n\ncalc = Calculator.new\ncalc.add(2, 3)        # Instance method\nCalculator.version    # Class method",
        key_points: [
          "Instance methods: need object instance",
          "Class methods: called on class itself",
          "Class methods defined with 'self.'",
          "Class methods often used for utility functions"
        ]
      },
      {
        id: 10,
        question: "What is encapsulation in Ruby?",
        answer: "Encapsulation is the bundling of data (instance variables) and methods that operate on that data. In Ruby, instance variables are private by default, and public methods provide controlled access to the data.",
        code_example: "class BankAccount\n  def initialize(balance)\n    @balance = balance  # Private instance variable\n  end\n  def deposit(amount)   # Public method\n    @balance += amount if amount > 0\n  end\n  \n  private\n  \n  def validate_amount(amount)\n    amount > 0\n  end\nend",
        key_points: [
          "Data and methods bundled together",
          "Instance variables are private by default",
          "Public methods control data access",
          "Private methods for internal use"
        ]
      },
      {
        id: 11,
        question: "What is metaprogramming in Ruby?",
        answer: "Metaprogramming is writing code that writes or modifies other code at runtime. Ruby's dynamic nature makes metaprogramming powerful with features like define_method, method_missing, class_eval, and instance_eval.",
        code_example: "# Dynamic method definition\nclass Dynamic\n  define_method(\"greet_#{name}\") do |message|\n    puts \"#{name}: #{message}\"\n  end\nend\n  \n  dynamic = Dynamic.new\ndynamic.define_singleton_method(:custom_method) { puts \"Custom!\" }",
        key_points: [
          "Code that writes or modifies other code",
          "Ruby's dynamic nature enables powerful metaprogramming",
          "Key methods: define_method, method_missing, eval",
          "Used in frameworks like Rails"
        ]
      },
      {
        id: 12,
        question: "What is method_missing and when would you use it?",
        answer: "method_missing is called when an object receives a method it doesn't respond to. It's commonly used for dynamic method handling, delegation, and creating flexible APIs.",
        code_example: "class DynamicHandler\n  def method_missing(method_name, *args)\n    puts \"Handling unknown method: #{method_name}\"\n    super if method_name.to_s.start_with?('real_')\n  end\n  \n  def respond_to_missing?(method_name, include_private = false)\n    method_name.to_s.start_with?('handle_') || super\n  end\nend",
        key_points: [
          "Called for unknown method calls",
          "Override to handle dynamic methods",
          "Should implement respond_to_missing? too",
          "Used in delegation and dynamic APIs"
        ]
      },
      {
        id: 13,
        question: "What are Ruby's eval methods?",
        answer: "Ruby has several eval methods: eval executes strings as code, class_eval evaluates code in class context, instance_eval evaluates code in instance context, and define_method dynamically defines methods.",
        code_example: "# eval - execute string as code\neval('x = 5')\n\n# class_eval - add methods to class\nString.class_eval do\n  def shout\n    self.upcase + '!'\n  end\nend\n\n# define_method - dynamic method creation\nclass Example\n  define_method(:dynamic_method) { puts \"Dynamic!\" }\nend",
        key_points: [
          "eval: execute string as Ruby code",
          "class_eval: evaluate in class context",
          "instance_eval: evaluate in instance context",
          "define_method: dynamic method definition"
        ]
      },
      {
        id: 14,
        question: "What is the difference between class_eval and instance_eval?",
        answer: "class_eval evaluates code in the context of a class, useful for defining class methods. instance_eval evaluates code in the context of an object instance, useful for accessing private state or defining singleton methods.",
        code_example: "# class_eval - class context\nString.class_eval do\n  def self.class_method; \"Class method\"; end\nend\n\n# instance_eval - instance context\nstr = \"hello\"\nstr.instance_eval do\n  def shout; self.upcase; end\nend",
        key_points: [
          "class_eval: class context, for class methods",
          "instance_eval: instance context, for singleton methods",
          "Both can execute strings or blocks",
          "instance_eval can access private state"
        ]
      },
      {
        id: 15,
        question: "What are Ruby's reflection capabilities?",
        answer: "Ruby has strong reflection capabilities allowing programs to examine and modify their own structure at runtime. Methods like respond_to?, methods, instance_variables, and class allow introspection.",
        code_example: "# Reflection examples\nobj = \"hello\"\nobj.class                    # => String\nobj.respond_to?(:upcase)      # => true\nobj.methods                  # All methods\nobj.instance_variables        # Instance vars\nobj.class.superclass          # Parent class\nobj.class.ancestors           # All ancestor classes",
        key_points: [
          "Examine program structure at runtime",
          "Methods: class, respond_to?, methods, instance_variables",
          "Used in debugging and metaprogramming",
          "Ruby objects are highly introspectable"
        ]
      },
      {
        id: 16,
        question: "What are Ruby's concurrency options?",
        answer: "Ruby offers several concurrency options: Threads for parallel execution, Fibers for lightweight concurrency, Ractors (Ruby 3+) for parallel processing, and async libraries like Async. Global Interpreter Lock (GIL) limits true parallelism in CRuby.",
        code_example: "# Threads\nthreads = 5.times.map do |i|\n  Thread.new { puts \"Thread #{i}\" }\nend\nthreads.each(&:join)\n\n# Fibers (Ruby 1.9+)\nfiber = Fiber.new do\n  Fiber.yield \"First\"\n  Fiber.yield \"Second\"\nend",
        key_points: [
          "Threads: traditional concurrency",
          "Fibers: lightweight cooperative concurrency",
          "Ractors: Ruby 3+ parallel processing",
          "GIL limits true parallelism in CRuby"
        ]
      },
      {
        id: 17,
        question: "What is the Global Interpreter Lock (GIL)?",
        answer: "The GIL is a mutex that protects access to Ruby internals, allowing only one thread to execute Ruby code at a time. This means threads in CRuby run concurrently but not in parallel for CPU-bound tasks. I/O operations can run in parallel.",
        key_points: [
          "Only one thread executes Ruby code at a time",
          "Threads run concurrently, not in parallel",
          "I/O operations can run in parallel",
          "Alternative implementations (JRuby, Rubinius) don't have GIL"
        ]
      },
      {
        id: 18,
        question: "What are Mutex and Thread-safe operations?",
        answer: "Mutex (mutual exclusion) ensures only one thread can access shared resources at a time. Thread-safe operations prevent race conditions when multiple threads access shared data.",
        code_example: "# Mutex example\nmutex = Mutex.new\nshared_resource = 0\n\nthreads = 10.times.map do\n  Thread.new do\n    mutex.synchronize do\n      shared_resource += 1\n    end\n  end\nend\n\nthreads.each(&:join)",
        key_points: [
          "Mutex prevents concurrent access to shared resources",
          "synchronize block ensures thread safety",
          "Prevents race conditions",
          "Essential for multi-threaded programming"
        ]
      },
      {
        id: 19,
        question: "What are Ruby Fibers?",
        answer: "Fibers are lightweight, cooperative concurrency primitives. Unlike pre-emptive threads, fibers voluntarily yield control. They're more efficient than threads for certain use cases but require explicit yielding.",
        code_example: "# Fiber example\nfiber = Fiber.new do\n  puts \"Starting fiber\"\n  Fiber.yield \"Paused\"\n  puts \"Resumed\"\n  \"Completed\"\nend\n\nputs fiber.resume  # => \"Starting fiber\"\nputs fiber.resume  # => \"Resumed\"\nputs fiber.resume  # => \"Completed\"",
        key_points: [
          "Lightweight, cooperative concurrency",
          "Explicit yielding control",
          "More efficient than threads for some use cases",
          "Not pre-emptive like threads"
        ]
      },
      {
        id: 20,
        question: "What are Ractors in Ruby 3+?",
        answer: "Ractors are Ruby 3+'s parallel execution model that provides true parallelism without GIL limitations. Each Ractor has its own memory space and communicates via message passing, enabling safe parallel processing.",
        code_example: "# Ractor example (Ruby 3+)\nractor = Ractor.new do\n  data = receive\n  data.map { |x| x * 2 }\nend\n\nractor.send([1, 2, 3, 4, 5])\nresult = ractor.take  # => [2, 4, 6, 8, 10]",
        key_points: [
          "Ruby 3+ parallel execution model",
          "True parallelism without GIL",
          "Separate memory spaces",
          "Communication via message passing"
        ]
      },
      {
        id: 21,
        question: "How does Ruby garbage collection work?",
        answer: "Ruby uses mark-and-sweep garbage collection. The GC marks all reachable objects, then sweeps away unmarked objects. Ruby 2+ uses generational GC with multiple generations for better performance.",
        key_points: [
          "Mark-and-sweep algorithm",
          "Generational GC in Ruby 2+",
          "Automatic memory management",
          "GC.start can trigger manual collection"
        ]
      },
      {
        id: 22,
        question: "What are memory leaks in Ruby?",
        answer: "Memory leaks occur when objects are no longer needed but aren't garbage collected. Common causes include global variables, class variables, circular references, and event listeners that aren't cleaned up.",
        code_example: "# Memory leak examples\n$global_var = []  # Never GC'd\n@@class_var = {}  # Never GC'd\n\n# Circular references\nclass Node\n  attr_accessor :parent, :child\nend\n\n# Event listeners not cleaned up\n@listeners = []\n@listeners << proc { puts \"callback\" }",
        key_points: [
          "Objects not garbage collected when no longer needed",
          "Global and class variables never GC'd",
          "Circular references can prevent GC",
          "Event listeners and callbacks common culprits"
        ]
      },
      {
        id: 23,
        question: "How can you profile Ruby code?",
        answer: "Ruby provides several profiling tools: ruby-prof gem for detailed profiling, Benchmark module for performance measurement, memory_profiler gem for memory analysis, and built-in GC statistics.",
        code_example: "# Benchmarking\nrequire 'benchmark'\n\nBenchmark.bm do |x|\n  x.report(\"fast\") { 1000.times { \"test\" } }\n  x.report(\"slow\") { 1000.times { \"test\" * 10 } }\nend\n\n# GC stats\nGC.stat  # => {:count=>5, :heap_used=>123, ...}",
        key_points: [
          "ruby-prof gem for detailed profiling",
          "Benchmark module for performance measurement",
          "memory_profiler gem for memory analysis",
          "GC.stat for garbage collection statistics"
        ]
      },
      {
        id: 24,
        question: "What are Ruby's performance optimization techniques?",
        answer: "Performance optimization techniques include: using the right data structures, avoiding object creation in loops, using symbols instead of strings for keys, memoization, and leveraging built-in methods written in C.",
        code_example: "# Performance tips\n# Use symbols for hash keys\nhash = { key: \"value\" }  # Better than {\"key\" => \"value\"}\n\n# Avoid object creation in loops\n# Bad: strings.each { |s| s + \"x\" }\n# Good: strings.map { |s| \"#{s}x\" }\n\n# Memoization\ndef expensive_calculation\n  @result ||= complex_operation\nend",
        key_points: [
          "Use appropriate data structures",
          "Minimize object creation in loops",
          "Use symbols instead of strings for keys",
          "Memoize expensive calculations"
        ]
      },
      {
        id: 25,
        question: "What is N+1 query problem and how to solve it?",
        answer: "N+1 query problem occurs when loading associations results in N+1 database queries instead of 1. Solutions include eager loading (includes, preload, eager_load), batch loading, and caching.",
        key_points: [
          "N+1 queries: loading associations inefficiently",
          "Solutions: includes, preload, eager_load",
          "Batch loading for multiple associations",
          "Caching frequently accessed data"
        ]
      },
      {
        id: 26,
        question: "What is the difference between proc and lambda?",
        answer: "Both are Proc objects, but lambdas have stricter argument checking and return behavior. Lambdas return from themselves, while procs return from the enclosing method. Lambdas require correct arity, procs don't.",
        code_example: "# Lambda vs Proc\nlambda = ->(x) { x * 2 }\nproc = Proc.new { |x| x * 2 }\n\n# Lambda checks arguments\nlambda.call(1)     # Works\nlambda.call(1, 2)  # ArgumentError\n\n# Proc doesn't check\nproc.call(1)       # Works\nproc.call(1, 2)    # Ignores extra argument",
        key_points: [
          "Both are Proc objects",
          "Lambda: strict argument checking",
          "Lambda: returns from itself",
          "Proc: returns from enclosing method"
        ]
      },
      {
        id: 27,
        question: "What is the difference between block, proc, and lambda?",
        answer: "Blocks are anonymous functions passed to methods, not objects. Procs are objectified blocks. Lambdas are procs with stricter behavior. Blocks use yield, procs/lambdas use call.",
        code_example: "# Block\ndef method_with_block\n  yield if block_given?\nend\n\n# Proc\nmy_proc = Proc.new { |x| x * 2 }\n\n# Lambda\nmy_lambda = ->(x) { x * 2 }\n\n# Usage\nmethod_with_block { puts \"block\" }\nmy_proc.call(5)\nmy_lambda.call(5)",
        key_points: [
          "Block: not an object, passed to methods",
          "Proc: objectified block",
          "Lambda: stricter proc variant",
          "Blocks use yield, procs/lambdas use call"
        ]
      },
      {
        id: 28,
        question: "What is the difference between load and require?",
        answer: "require loads files only once and searches Ruby's load path. load loads files every time called and requires absolute path. require is for libraries, load is for configuration/reloading.",
        code_example: "# require - loads once, searches load path\nrequire 'json'  # Loads once\nrequire 'json'  # Skips\n\n# load - loads every time, needs path\nload 'config.rb'  # Loads\nclass MyConfig\n  load 'config.rb'  # Reloads configuration\nend",
        key_points: [
          "require: loads once, searches load path",
          "load: loads every time, needs absolute path",
          "require for libraries, load for configuration",
          "require returns false if already loaded"
        ]
      },
      {
        id: 29,
        question: "What is the Ruby object model?",
        answer: "Ruby's object model is based on single inheritance with modules (mixins). Every object has a class, and classes have superclasses. Modules can be included in classes to add functionality. Everything in Ruby is an object.",
        code_example: "# Object model example\nobj = \"hello\"\nobj.class                    # => String\nobj.class.superclass        # => Object\nobj.class.superclass.superclass  # => BasicObject\n\n# Module inclusion\nclass MyClass\n  include Enumerable  # Mixin module\nend",
        key_points: [
          "Everything is an object",
          "Single inheritance with modules (mixins)",
          "Object -> Class -> MyClass hierarchy",
          "Modules provide multiple inheritance-like behavior"
        ]
      },
      {
        id: 30,
        question: "What are Ruby's built-in exception types?",
        answer: "Ruby has a hierarchy of exception types. StandardError is the base for most exceptions. Common types include NoMethodError, ArgumentError, TypeError, RuntimeError, and custom exceptions can inherit from StandardError.",
        code_example: "# Exception hierarchy\nbegin\n  obj.unknown_method\nrescue NoMethodError => e\n  puts \"Method doesn't exist: #{e.message}\"\nrescue ArgumentError => e\n  puts \"Invalid argument: #{e.message}\"\nrescue StandardError => e\n  puts \"General error: #{e.message}\"\nend",
        key_points: [
          "StandardError is base for most exceptions",
          "Common types: NoMethodError, ArgumentError, TypeError",
          "Custom exceptions inherit from StandardError",
          "Exception is the ultimate base class (avoid catching)"
        ]
      }
    ]
  end
end

# Start the interview preparation
if __FILE__ == $0
  prep = RubyInterviewPrep.new
  prep.load_progress
  prep.start_prep
end
