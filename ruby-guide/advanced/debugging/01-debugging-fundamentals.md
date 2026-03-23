# Debugging Fundamentals in Ruby
# Comprehensive guide to debugging techniques and strategies

## 🎯 Overview

Debugging is an essential skill for every Ruby developer. This guide covers fundamental debugging techniques, Ruby's debugging tools, and systematic approaches to finding and fixing bugs.

## 🔍 Ruby Debugging Basics

### 1. Understanding Ruby Errors

Common Ruby error types and how to handle them:

```ruby
class ErrorAnalysis
  def self.demonstrate_common_errors
    puts "Common Ruby Errors:"
    puts "=" * 40
    
    # 1. NoMethodError
    begin
      "hello".nonexistent_method
    rescue NoMethodError => e
      puts "NoMethodError: #{e.message}"
      puts "Backtrace: #{e.backtrace.first}"
    end
    
    # 2. TypeError
    begin
      "hello" + 123
    rescue TypeError => e
      puts "TypeError: #{e.message}"
      puts "Backtrace: #{e.backtrace.first}"
    end
    
    # 3. ArgumentError
    begin
      Integer("not_a_number")
    rescue ArgumentError => e
      puts "ArgumentError: #{e.message}"
      puts "Backtrace: #{e.backtrace.first}"
    end
    
    # 4. NameError
    begin
      undefined_variable
    rescue NameError => e
      puts "NameError: #{e.message}"
      puts "Backtrace: #{e.backtrace.first}"
    end
    
    # 5. ZeroDivisionError
    begin
      10 / 0
    rescue ZeroDivisionError => e
      puts "ZeroDivisionError: #{e.message}"
      puts "Backtrace: #{e.backtrace.first}"
    end
  end
  
  def self.error_handling_strategies
    puts "\nError Handling Strategies:"
    puts "=" * 40
    
    # Strategy 1: Specific exception handling
    def safe_divide(a, b)
      begin
        a / b
      rescue ZeroDivisionError
        puts "Cannot divide by zero"
        nil
      end
    end
    
    # Strategy 2: Multiple exception handling
    def parse_number(input)
      begin
        Integer(input)
      rescue ArgumentError
        puts "Invalid number format"
        nil
      rescue TypeError
        puts "Input must be a string"
        nil
      end
    end
    
    # Strategy 3: Ensure cleanup
    def process_file(filename)
      file = nil
      begin
        file = File.open(filename, 'r')
        content = file.read
        content.upcase
      rescue Errno::ENOENT
        puts "File not found: #{filename}"
        nil
      ensure
        file&.close
      end
    end
    
    # Test strategies
    puts "Safe division: #{safe_divide(10, 2)}"
    puts "Safe division: #{safe_divide(10, 0)}"
    
    puts "Parse number: #{parse_number("123")}"
    puts "Parse number: #{parse_number("abc")}"
    
    # Note: File operations would require actual files
    puts "File processing requires actual file"
  end
  
  def self.custom_exceptions
    puts "\nCustom Exceptions:"
    puts "=" * 40
    
    # Define custom exceptions
    class BusinessError < StandardError
      attr_reader :code, :details
      
      def initialize(message, code = nil, details = nil)
        super(message)
        @code = code
        @details = details
      end
    end
    
    class ValidationError < BusinessError
      def initialize(field, value)
        super("Invalid #{field}: #{value}", "VALIDATION_ERROR", { field: field, value: value })
      end
    end
    
    class InsufficientStockError < BusinessError
      def initialize(product, requested, available)
        super("Insufficient stock for #{product}", "INSUFFICIENT_STOCK", {
          product: product,
          requested: requested,
          available: available
        })
      end
    end
    
    # Use custom exceptions
    def validate_age(age)
      raise ValidationError.new("age", age) unless age.is_a?(Integer) && age >= 0
      true
    end
    
    def check_stock(product, quantity, available)
      raise InsufficientStockError.new(product, quantity, available) if quantity > available
      true
    end
    
    # Test custom exceptions
    begin
      validate_age(-5)
    rescue ValidationError => e
      puts "Validation error: #{e.message}"
      puts "Error code: #{e.code}"
      puts "Details: #{e.details}"
    end
    
    begin
      check_stock("Widget", 10, 5)
    rescue InsufficientStockError => e
      puts "Stock error: #{e.message}"
      puts "Error code: #{e.code}"
      puts "Details: #{e.details}"
    end
  end
end

# Run error analysis examples
ErrorAnalysis.demonstrate_common_errors
ErrorAnalysis.error_handling_strategies
ErrorAnalysis.custom_exceptions
```

### 2. Basic Debugging Techniques

Essential debugging methods and approaches:

```ruby
class BasicDebugging
  def self.print_debugging
    puts "Print Debugging Techniques:"
    puts "=" * 40
    
    # 1. Simple print statements
    def process_data(data)
      puts "Starting data processing with #{data.length} items"
      
      processed = data.map.with_index do |item, index|
        puts "Processing item #{index}: #{item}"
        item.upcase
      end
      
      puts "Data processing completed"
      processed
    end
    
    # 2. Conditional debugging
    def complex_calculation(x, y, debug = false)
      puts "Input: x=#{x}, y=#{y}" if debug
      
      step1 = x * 2
      puts "Step 1 result: #{step1}" if debug
      
      step2 = step1 + y
      puts "Step 2 result: #{step2}" if debug
      
      result = Math.sqrt(step2)
      puts "Final result: #{result}" if debug
      
      result
    end
    
    # 3. Variable inspection
    def inspect_variables
      local_var = "local value"
      @instance_var = "instance value"
      @@class_var = "class value"
      
      puts "Local variables:"
      local_variables.each { |name, value| puts "  #{name}: #{value.inspect}" }
      
      puts "Instance variables:"
      instance_variables.each { |name, value| puts "  #{name}: #{value.inspect}" }
      
      puts "Class variables:"
      self.class_variables.each { |name, value| puts "  #{name}: #{value.inspect}" }
    end
    
    # Test techniques
    data = ["item1", "item2", "item3"]
    process_data(data)
    
    result = complex_calculation(4, 9, true)
    puts "Complex calculation result: #{result}"
    
    inspect_variables
  end
  
  def self.object_inspection
    puts "\nObject Inspection Techniques:"
    puts "=" * 40
    
    # 1. Object introspection
    class SampleClass
      attr_accessor :name, :value
      
      def initialize(name, value)
        @name = name
        @value = value
        @internal_data = "secret"
      end
      
      def public_method
        "I'm public"
      end
      
      private
      
      def private_method
        "I'm private"
      end
    end
    
    obj = SampleClass.new("test", 42)
    
    puts "Object class: #{obj.class}"
    puts "Object methods: #{obj.methods.length}"
    puts "Public methods: #{obj.public_methods.length}"
    puts "Instance variables: #{obj.instance_variables}"
    puts "Object ID: #{obj.object_id}"
    
    # 2. Method introspection
    puts "\nMethod inspection:"
    obj.methods.each do |method|
      puts "  #{method}" if method.to_s.include?("name")
    end
    
    # 3. Variable inspection
    puts "\nVariable inspection:"
    obj.instance_variables.each do |var|
      value = obj.instance_variable_get(var)
      puts "  #{var}: #{value.inspect}"
    end
    
    # 4. Respond to check
    puts "\nRespond to checks:"
    puts "Respond to name?: #{obj.respond_to?(:name)}"
    puts "Respond to value?: #{obj.respond_to?(:value)}"
    puts "Respond to public_method?: #{obj.respond_to?(:public_method)}"
    puts "Respond to private_method?: #{obj.respond_to?(:private_method)}"
  end
  
  def self.call_stack_analysis
    puts "\nCall Stack Analysis:"
    puts "=" * 40
    
    def deep_function(level = 0)
      puts "Level #{level}: #{caller_locations.length} frames in call stack"
      
      if level < 5
        deep_function(level + 1)
      else
        puts "Maximum depth reached"
        puts "Call stack:"
        caller_locations.each_with_index do |location, index|
          puts "  #{index}: #{location}"
        end
      end
    end
    
    deep_function
  end
end

# Run basic debugging examples
BasicDebugging.print_debugging
BasicDebugging.object_inspection
BasicDebugging.call_stack_analysis
```

## 🛠️ Ruby Debugging Tools

### 1. Using Ruby's Built-in Debugger

Leverage Ruby's built-in debugging capabilities:

```ruby
class RubyDebugger
  def self.using_debugger
    puts "Using Ruby's Built-in Debugger:"
    puts "=" * 40
    
    # Example code that would be debugged
    def buggy_function(arr)
      result = []
      
      arr.each_with_index do |item, index|
        # Intentional bug: off-by-one error
        if index <= arr.length  # Should be <
          result << item * index
        end
      end
      
      result
    end
    
    # How to debug this:
    puts "To debug this function:"
    puts "1. Add 'require \"debug\"' at the top of your file"
    puts "2. Insert 'debugger' where you want to break"
    puts "3. Run with: ruby -r debug your_file.rb"
    puts ""
    puts "Example debugging session:"
    puts "  (rdb:1) buggy_function([1, 2, 3, 4, 5])"
    puts "  (rdb:1) step"
    puts "  (rdb:1) p arr"
    puts "  (rdb:1) p index"
    puts "  (rdb:1) p result"
    puts "  (rdb:1) continue"
    
    # Demonstrate the bug
    test_array = [1, 2, 3, 4, 5]
    result = buggy_function(test_array)
    puts "Result: #{result}"
    puts "Expected: [0, 2, 6, 12, 20]"
    puts "Actual: #{result}"
  end
  
  def self.debugger_commands
    puts "\nCommon Debugger Commands:"
    puts "=" * 40
    
    commands = {
      "step" => "Execute the next line",
      "next" => "Execute the next line (skip method calls)",
      "continue" => "Continue execution until next breakpoint",
      "break" => "Set a breakpoint",
      "delete" => "Delete a breakpoint",
      "list" => "List source code",
      "where" => "Show call stack",
      "up" => "Move up the call stack",
      "down" => "Move down the call stack",
      "print" => "Print variable value",
      "display" => "Display variable value",
      "info" => "Show information",
      "quit" => "Exit debugger"
    }
    
    commands.each do |command, description|
      puts "#{command.ljust(12)}: #{description}"
    end
  end
  
  def self.debugger_workflow
    puts "\nDebugging Workflow:"
    puts "=" * 40
    
    workflow = [
      "1. Identify the problem area",
      "2. Set breakpoints at key locations",
      "3. Step through code execution",
      "4. Inspect variables at each step",
      "5. Identify where the logic differs from expectations",
      "6. Fix the issue",
      "7. Test the fix",
      "8. Remove debugging code"
    ]
    
    workflow.each { |step| puts step }
  end
end

# Run debugger examples
RubyDebugger.using_debugger
RubyDebugger.debugger_commands
RubyDebugger.debugger_workflow
```

### 2. Logging for Debugging

Use logging as a debugging technique:

```ruby
class DebuggingLogger
  def initialize(level = :debug)
    @level = level
    @logs = []
  end
  
  def debug(message)
    log(:debug, message)
  end
  
  def info(message)
    log(:info, message)
  end
  
  def warn(message)
    log(:warn, message)
  end
  
  def error(message)
    log(:error, message)
  end
  
  def log_with_context(level, message, context = {})
    log(level, message, context)
  end
  
  def print_logs
    puts "\nDebug Logs:"
    puts "=" * 40
    
    @logs.each do |log|
      puts "[#{log[:timestamp]}] #{log[:level].upcase}: #{log[:message]}"
      unless log[:context].empty?
        log[:context].each { |key, value| puts "  #{key}: #{value}" }
      end
      puts
    end
  end
  
  private
  
  def log(level, message, context = {})
    return unless should_log?(level)
    
    log_entry = {
      timestamp: Time.now.strftime("%Y-%m-%d %H:%M:%S"),
      level: level,
      message: message,
      context: context
    }
    
    @logs << log_entry
  end
  
  def should_log?(level)
    levels = { debug: 0, info: 1, warn: 2, error: 3 }
    levels[level] >= levels[@level]
  end
end

class DataProcessor
  def initialize(logger = nil)
    @logger = logger || DebuggingLogger.new
  end
  
  def process_data(data)
    @logger.debug("Starting data processing")
    @logger.debug("Data size: #{data.length}")
    
    processed = data.map.with_index do |item, index|
      @logger.debug("Processing item #{index}: #{item}")
      
      # Simulate processing
      result = item.upcase
      
      @logger.debug("Processed result: #{result}")
      
      result
    end
    
    @logger.info("Data processing completed")
    @logger.info("Processed #{processed.length} items")
    
    processed
  end
  
  def validate_data(data)
    @logger.debug("Starting data validation")
    
    errors = []
    data.each_with_index do |item, index|
      @logger.debug("Validating item #{index}: #{item}")
      
      unless item.is_a?(String)
        errors << "Item #{index} is not a string: #{item.class}"
        @logger.error("Validation error: Item #{index} is not a string")
      end
      
      if item.is_a?(String) && item.empty?
        errors << "Item #{index} is empty"
        @logger.warn("Validation warning: Item #{index} is empty")
      end
    end
    
    @logger.log_with_context(:info, "Data validation completed", {
      total_items: data.length,
      errors: errors.length,
      valid: errors.empty?
    })
    
    errors
  end
end

# Usage example
logger = DebuggingLogger.new(:debug)
processor = DataProcessor.new(logger)

data = ["item1", "item2", "", 123, "item5"]
processed_data = processor.process_data(data)
validation_errors = processor.validate_data(data)

logger.print_logs
```

## 🔧 Advanced Debugging Techniques

### 1. Tracing and Profiling

Advanced debugging with tracing:

```ruby
class TracingDebugger
  def initialize
    @traces = []
    @trace_enabled = false
  end
  
  def enable_tracing
    @trace_enabled = true
  end
  
  def disable_tracing
    @trace_enabled = false
  end
  
  def trace_method(klass, method_name)
    original_method = klass.instance_method(method_name)
    
    klass.define_method(method_name) do |*args, &block|
      if @trace_enabled
        trace_call(klass.name, method_name, args, block)
      end
      
      original_method.bind(self).call(*args, &block)
    end
  end
  
  def trace_call(class_name, method_name, args, block)
    call_info = {
      timestamp: Time.now,
      class: class_name,
      method: method_name,
      args: args,
      has_block: block_given?,
      thread_id: Thread.current.object_id
    }
    
    @traces << call_info
    
    puts "[TRACE] #{class_name}##{method_name}(#{args.map(&:inspect).join(', ')})"
    puts "  Thread: #{call_info[:thread_id]}"
    puts "  Block: #{call_info[:has_block]}"
  end
  
  def print_traces
    puts "\nMethod Traces:"
    puts "=" * 40
    
    @traces.each_with_index do |trace, index|
      puts "#{index + 1}. #{trace[:class]}##{trace[:method]}"
      puts "   Args: #{trace[:args]}"
      puts "   Thread: #{trace[:thread_id]}"
      puts "   Time: #{trace[:timestamp]}"
      puts
    end
  end
  
  def analyze_traces
    return if @traces.empty?
    
    puts "\nTrace Analysis:"
    puts "=" * 40
    
    # Most called methods
    method_counts = Hash.new(0)
    @traces.each { |trace| method_counts["#{trace[:class]}##{trace[:method]}"] += 1 }
    
    puts "Most called methods:"
    method_counts.sort_by { |_, count| -count }.first(5).each do |method, count|
      puts "  #{method}: #{count} calls"
    end
    
    # Thread analysis
    thread_usage = Hash.new(0)
    @traces.each { |trace| thread_usage[trace[:thread_id]] += 1 }
    
    puts "\nThread usage:"
    thread_usage.each do |thread_id, count|
      puts "  Thread #{thread_id}: #{count} calls"
    end
  end
end

# Usage example
class Calculator
  def add(a, b)
    a + b
  end
  
  def multiply(a, b)
    a * b
  end
  
  def complex_operation(x, y, z)
    result = add(x, y)
    multiply(result, z)
  end
end

class StringProcessor
  def process(text)
    text.upcase.strip
  end
  
  def reverse(text)
    text.reverse
  end
  
  def transform(text)
    processed = process(text)
    reversed = reverse(processed)
    reversed
  end
end

# Set up tracing
tracer = TracingDebugger.new
tracer.enable_tracing

# Trace methods
tracer.trace_method(Calculator, :add)
tracer.trace_method(Calculator, :multiply)
tracer.trace_method(Calculator, :complex_operation)
tracer.trace_method(StringProcessor, :process)
tracer.trace_method(StringProcessor, :reverse)
tracer.trace_method(StringProcessor, :transform)

# Run traced operations
calc = Calculator.new
processor = StringProcessor.new

# Single thread
calc.add(2, 3)
calc.multiply(4, 5)
calc.complex_operation(2, 3, 4)

processor.process("hello world")
processor.reverse("hello world")
processor.transform("hello world")

# Multi-threaded operations
threads = []
3.times do |i|
  threads << Thread.new do
    calc.add(i, i + 1)
    processor.process("thread #{i}")
  end
end

threads.each(&:join)

# Print results
tracer.print_traces
tracer.analyze_traces
```

### 2. Memory Debugging

Debug memory-related issues:

```ruby
class MemoryDebugger
  def initialize
    @snapshots = []
  end
  
  def take_snapshot(label = nil)
    GC.start
    snapshot = {
      label: label,
      timestamp: Time.now,
      object_counts: ObjectSpace.count_objects,
      total_objects: ObjectSpace.count_objects[:TOTAL]
    }
    
    @snapshots << snapshot
    snapshot
  end
  
  def analyze_memory_growth
    return if @snapshots.length < 2
    
    first = @snapshots.first
    last = @snapshots.last
    
    puts "\nMemory Growth Analysis:"
    puts "=" * 40
    
    puts "Time period: #{first[:timestamp]} to #{last[:timestamp]}"
    puts "Total objects growth: #{last[:total_objects] - first[:total_objects]}"
    
    # Analyze by type
    puts "\nObject type growth:"
    last[:object_counts].each do |type, count|
      first_count = first[:object_counts][type] || 0
      growth = count - first_count
      
      if growth > 0
        puts "  #{type}: +#{growth}"
      elsif growth < 0
        puts "  #{type}: #{growth}"
      end
    end
  end
  
  def find_memory_leaks
    puts "\nMemory Leak Detection:"
    puts "=" * 40
    
    # Create objects and check if they're collected
    objects_before = ObjectSpace.count_objects[:TOTAL]
    
    # Create potentially leaking objects
    leaky_objects = []
    1000.times do |i|
      leaky_objects << "Leaky object #{i}"
    end
    
    # Clear reference
    leaky_objects = nil
    
    # Force garbage collection
    GC.start
    
    objects_after = ObjectSpace.count_objects[:TOTAL]
    
    leaked = objects_after - objects_before
    
    puts "Objects created: 1000"
    puts "Objects remaining: #{leaked}"
    puts "Leaked objects: #{leaked}" if leaked > 100
    
    # Check for specific object types
    string_objects = []
    1000.times { |i| string_objects << "String #{i}" }
    
    strings_before = ObjectSpace.count_objects[:T_STRING]
    string_objects = nil
    GC.start
    strings_after = ObjectSpace.count_objects[:T_STRING]
    
    string_leak = strings_after - strings_before
    puts "String objects leak: #{string_leak}"
  end
  
  def analyze_object_references
    puts "\nObject Reference Analysis:"
    puts "=" * 40
    
    # Find objects with many references
    object_space = ObjectSpace.each_object
    
    # This is a simplified analysis
    # In practice, you'd use more sophisticated tools
    puts "Analyzing object references..."
    puts "Total objects: #{object_space.count}"
    
    # Group by class
    class_counts = Hash.new(0)
    object_space.each { |obj| class_counts[obj.class.name] += 1 }
    
    puts "\nObjects by class:"
    class_counts.sort_by { |_, count| -count }.first(10).each do |class_name, count|
      puts "  #{class_name}: #{count}"
    end
  end
  
  def print_snapshots
    puts "\nMemory Snapshots:"
    puts "=" * 40
    
    @snapshots.each_with_index do |snapshot, index|
      puts "#{index + 1}. #{snapshot[:label] || 'Snapshot'}"
      puts "   Time: #{snapshot[:timestamp]}"
      puts "   Total objects: #{snapshot[:total_objects]}"
      puts "   T_STRING: #{snapshot[:object_counts][:T_STRING]}"
      puts "   T_ARRAY: #{snapshot[:object_counts][:T_ARRAY]}"
      puts "   T_HASH: #{snapshot[:object_counts][:T_HASH]}"
      puts
    end
  end
end

# Usage example
memory_debugger = MemoryDebugger.new

# Take initial snapshot
memory_debugger.take_snapshot("Initial")

# Create some objects
strings = []
1000.times { |i| strings << "String #{i}" }

arrays = []
100.times { |i| arrays << (1..100).to_a }

hashes = {}
100.times { |i| hashes[i] = "value #{i}" }

# Take snapshot after object creation
memory_debugger.take_snapshot("After object creation")

# Clear references
strings = nil
arrays = nil
hashes = nil

# Force garbage collection
GC.start

# Take final snapshot
memory_debugger.take_snapshot("After GC")

# Analyze results
memory_debugger.print_snapshots
memory_debugger.analyze_memory_growth
memory_debugger.find_memory_leaks
memory_debugger.analyze_object_references
```

## 🎯 Debugging Best Practices

### 1. Systematic Debugging Approach

```ruby
class SystematicDebugger
  def self.debugging_checklist
    puts "Debugging Checklist:"
    puts "=" * 40
    
    checklist = [
      "1. Understand the problem",
      "2. Reproduce the issue",
      "3. Isolate the problematic code",
      "4. Formulate a hypothesis",
      "5. Test the hypothesis",
      "6. Fix the issue",
      "7. Verify the fix",
      "8. Prevent regression"
    ]
    
    checklist.each { |item| puts item }
  end
  
  def self.debugging_workflow
    puts "\nDebugging Workflow:"
    puts "=" * 40
    
    workflow = {
      "Understand" => "What is the expected vs actual behavior?",
      "Reproduce" => "Can you consistently reproduce the issue?",
      "Isolate" => "What is the smallest code that reproduces it?",
      "Hypothesize" => "What do you think is causing the issue?",
      "Test" => "How can you verify your hypothesis?",
      "Fix" => "What is the minimal change to fix it?",
      "Verify" => "Does the fix resolve the issue?",
      "Prevent" => "How can you prevent similar issues?"
    }
    
    workflow.each do |step, question|
      puts "#{step.ljust(12)}: #{question}"
    end
  end
  
  def self.debugging_tools
    puts "\nDebugging Tools and Techniques:"
    puts "=" * 40
    
    tools = {
      "Print statements" => "Quick and simple debugging",
      "Logger" => "Structured logging for debugging",
      "Debugger" => "Interactive debugging with breakpoints",
      "Binding.irb" => "Open IRB session at any point",
      "ObjectSpace" => "Inspect all objects in memory",
      "GC.start" => "Force garbage collection",
      "caller" => "Get the call stack",
      "method_missing" => "Catch undefined method calls",
      "trace" => "Trace method execution"
    }
    
    tools.each { |tool, description| puts "#{tool.ljust(20)}: #{description}" }
  end
end

# Run best practices
SystematicDebugger.debugging_checklist
SystematicDebugger.debugging_workflow
SystematicDebugger.debugging_tools
```

### 2. Debugging Anti-Patterns

Common debugging mistakes to avoid:

```ruby
class DebuggingAntiPatterns
  def self.anti_patterns
    puts "Debugging Anti-Patterns:"
    puts "=" * 40
    
    anti_patterns = [
      {
        pattern: "Print statements in production",
        problem: "Leads to performance issues and security risks",
        solution: "Use proper logging with configurable levels"
      },
      {
        pattern: "Debugging by trial and error",
        problem: "Inefficient and doesn't build understanding",
        solution: "Use systematic approach and hypothesis testing"
      },
      {
        pattern: "Ignoring error messages",
        problem: "Error messages contain valuable information",
        solution: "Read and understand error messages"
      },
      {
        pattern: "Over-debugging",
        problem: "Too much debugging information obscures the issue",
        solution: "Start with minimal debugging and add as needed"
      },
      {
        pattern: "Not reproducing the issue",
        problem: "Fixing the wrong problem",
        solution: "Always reproduce the issue before fixing"
      }
    ]
    
    anti_patterns.each do |anti_pattern|
      puts "Anti-Pattern: #{anti_pattern[:pattern]}"
      puts "  Problem: #{anti_pattern[:problem]}"
      puts "  Solution: #{anti_pattern[:solution]}"
      puts
    end
  end
  
  def self.debugging_code_smells
    puts "\nDebugging Code Smells:"
    puts "=" * 40
    
    code_smells = [
      {
        smell: "Hardcoded debugging statements",
        example: "puts 'Debug: ' + variable.to_s",
        refactored: "logger.debug('Variable value', variable: variable)"
      },
      {
        smell: "Commented out debugging code",
        example: "# puts 'Debug: ' + variable",
        refactored: "Remove debugging code or use conditional logging"
      },
      {
        smell: "Exception swallowing",
        example: "rescue => e # Do nothing",
        refactored: "rescue => e => logger.error('Error occurred', error: e)"
      },
      {
        smell: "No error context",
        example: "rescue => e => raise e",
        refactored: "rescue => e => raise CustomError.new(e.message, context)"
      }
    ]
    
    code_smells.each do |smell|
      puts "Code Smell: #{smell[:smell]}"
      puts "  Bad: #{smell[:example]}"
      puts "  Good: #{smell[:refactored]}"
      puts
    end
  end
end

# Run anti-patterns examples
DebuggingAntiPatterns.anti_patterns
DebuggingAntiPatterns.debugging_code_smells
```

## 🎓 Exercises

### Beginner Exercises

1. **Error Analysis**: Analyze common Ruby errors
2. **Print Debugging**: Use print statements for debugging
3. **Basic Logging**: Implement simple logging

### Intermediate Exercises

1. **Ruby Debugger**: Use Ruby's built-in debugger
2. **Method Tracing**: Implement method tracing
3. **Memory Debugging**: Debug memory issues

### Advanced Exercises

1. **Systematic Debugging**: Implement systematic debugging approach
2. **Custom Debugger**: Build custom debugging tools
3. **Production Debugging**: Debug production issues safely

---

## 🎯 Summary

Debugging fundamentals in Ruby provide:

- **Error Understanding** - Common Ruby errors and handling
- **Basic Techniques** - Print debugging and inspection
- **Debugging Tools** - Ruby's built-in debugger and logging
- **Advanced Techniques** - Tracing and memory debugging
- **Best Practices** - Systematic debugging approaches

Master these fundamentals to effectively debug Ruby applications!
