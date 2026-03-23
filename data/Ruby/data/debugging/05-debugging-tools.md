# Debugging Tools in Ruby
# Comprehensive guide to debugging tools and utilities

## 🎯 Overview

Effective debugging requires the right tools and utilities. This guide covers Ruby debugging tools, external debugging utilities, and building custom debugging tools.

## 🛠️ Ruby Built-in Tools

### 1. IRB and Pry

Interactive debugging with Ruby's REPL tools:

```ruby
class InteractiveDebugging
  def self.irb_debugging
    puts "IRB Debugging:"
    puts "=" * 50
    
    puts "IRB (Interactive Ruby) basics:"
    puts "1. Start IRB: irb"
    puts "2. Execute Ruby code interactively"
    puts "3. Inspect objects and methods"
    puts "4. Test code snippets"
    puts "5. Debug with puts and p"
    
    # Demonstrate IRB-like functionality
    puts "\nIRB-like session simulation:"
    
    # Simulate IRB session
    irb_session = [
      "irb(main):001:0> x = 10",
      "=> 10",
      "irb(main):002:0> y = 20",
      "=> 20",
      "irb(main):003:0> x + y",
      "=> 30",
      "irb(main):004:0> \"hello\".upcase",
      "=> \"HELLO\"",
      "irb(main):005:0> [1, 2, 3].map { |i| i * 2 }",
      "=> [2, 4, 6]"
    ]
    
    irb_session.each { |line| puts line }
    
    # Show IRB debugging techniques
    puts "\nIRB debugging techniques:"
    puts "- Use .methods to see available methods"
    puts "- Use .class to check object type"
    puts "- Use .inspect to see object representation"
    puts "- Use .instance_variables to see instance variables"
    puts "- Use binding.irb to drop into IRB at any point"
  end
  
  def self.pry_debugging
    puts "\nPry Debugging:"
    puts "=" * 50
    
    puts "Pry features:"
    puts "1. Syntax highlighting"
    puts "2. Code navigation"
    puts "3. Method source viewing"
    puts "4. Shell commands"
    puts "5. Goto and break commands"
    puts "6. Exception handling"
    puts "7. Runtime invocation"
    
    # Simulate Pry session
    puts "\nPry session simulation:"
    
    pry_session = [
      "From: /app/user.rb:12 Object#process:",
      "    12: def process(data)",
      "    13:   binding.pry",
      " => 14: end",
      "",
      "[1] pry(#<User>)> data",
      "=> \"test data\"",
      "[2] pry(#<User>)> self.class",
      "=> User",
      "[3] pry(#<User>)> whereami",
      "=> Object#process at line 13",
      "[4] pry(#<User>)> ls",
      "=> self.methods: [:process, :validate, :save]",
      "    instance variables: [:@data, @:@processed]",
      "[5] pry(#<User>)> @data",
      "=> \"test data\"",
      "[6] pry(#<User>)> exit"
    ]
    
    pry_session.each { |line| puts line }
    
    # Show Pry debugging commands
    puts "\nPry debugging commands:"
    commands = {
      "whereami" => "Show current location",
      "ls" => "List methods and variables",
      "cd" => "Change context",
      "show-method" => "Show method source",
      "edit-method" => "Edit method",
      "play" => "Replay code",
      "hist" => "Show command history",
      "!" => "Run shell command",
      "exit" => "Exit Pry"
    }
    
    commands.each { |cmd, desc| puts "  #{cmd.ljust(15)}: #{desc}" }
  end
  
  def self.binding_irb_usage
    puts "\nBinding.irb Usage:"
    puts "=" * 50
    
    def demonstrate_binding_irb
      x = 10
      y = 20
      
      # This would normally drop into IRB
      puts "At this point, binding.irb would allow you to:"
      puts "- Inspect local variables: x = #{x}, y = #{y}"
      puts "- Modify variables"
      puts "- Test expressions"
      puts "- Call methods"
      
      result = x + y
      puts "Result: #{result}"
      
      result
    end
    
    # Demonstrate binding.irb concept
    puts "Binding.irb allows you to:"
    puts "1. Drop into IRB at any point in code"
    puts "2. Inspect local variables and context"
    puts "3. Modify variables and continue execution"
    puts "4. Test code in the current context"
    
    result = demonstrate_binding_irb
    puts "Function result: #{result}"
  end
  
  def self.runtime_invocation
    puts "\nRuntime Invocation:"
    puts "=" * 50
    
    puts "Runtime invocation allows you to:"
    puts "1. Start debugging from anywhere"
    puts "2. Access current execution context"
    puts "3. Inspect local variables"
    puts "4. Modify program state"
    
    # Demonstrate runtime invocation concept
    def complex_calculation(a, b)
      x = a * 2
      y = b / 3
      z = x + y
      
      # In real debugging, you could use:
      # require 'debug'
      # debugger
      # or
      # binding.irb
      
      puts "Complex calculation variables:"
      puts "  x = #{x}"
      puts "  y = #{y}"
      puts "  z = #{z}"
      
      z
    end
    
    result = complex_calculation(15, 9)
    puts "Final result: #{result}"
  end
  
  # Run interactive debugging examples
  irb_debugging
  pry_debugging
  binding_irb_usage
  runtime_invocation
end
```

### 2. Ruby Debugger

Using Ruby's built-in debugger:

```ruby
class RubyDebuggerTools
  def self.debugger_setup
    puts "Ruby Debugger Setup:"
    puts "=" * 50
    
    puts "Installation:"
    puts "1. For Ruby 3.0+: gem install debug"
    puts "2. For older Ruby: gem install byebug"
    puts "3. For Rails: Add to Gemfile"
    
    puts "\nBasic usage:"
    puts "1. Add 'require \"debug\"' to your file"
    puts "2. Insert 'debugger' where you want to break"
    puts "3. Run with: ruby your_file.rb"
    puts "4. Use debugger commands to debug"
    
    # Show debugger configuration
    puts "\nDebugger configuration:"
    config = {
      "autoeval" => "Automatically evaluate expressions",
      "basename" => "Show only file basename",
      "listsize" => "Number of lines to list",
      "width" => "Width of listing",
      "tracing" => "Enable tracing"
    }
    
    config.each { |setting, desc| puts "  #{setting}: #{desc}" }
  end
  
  def self.debugger_commands
    puts "\nDebugger Commands:"
    puts "=" * 50
    
    commands = {
      "Navigation" => {
        "step" => "Execute next line",
        "next" => "Execute next line (skip method calls)",
        "finish" => "Finish current method",
        "continue" => "Continue execution",
        "up" => "Move up the call stack",
        "down" => "Move down the call stack"
      },
      "Inspection" => {
        "info locals" => "Show local variables",
        "info instance" => "Show instance variables",
        "info globals" => "Show global variables",
        "where" => "Show call stack",
        "list" => "List source code",
        "method" => "Show method information"
      },
      "Control" => {
        "break" => "Set breakpoint",
        "delete" => "Delete breakpoint",
        "catch" => "Set exception breakpoint",
        "watch" => "Set watchpoint",
        "condition" => "Set conditional breakpoint"
      },
      "Display" => {
        "display" => "Display expression",
        "undisplay" => "Stop displaying expression",
        "print" => "Print expression",
        "pp" => "Pretty print expression"
      }
    }
    
    commands.each do |category, cmds|
      puts "\n#{category}:"
      cmds.each { |cmd, desc| puts "  #{cmd.ljust(20)}: #{desc}" }
    end
  end
  
  def self.breakpoint_strategies
    puts "\nBreakpoint Strategies:"
    puts "=" * 50
    
    strategies = [
      {
        name: "Conditional breakpoints",
        description: "Break only when condition is met",
        example: "break if user.id == 123"
      },
      {
        name: "Method breakpoints",
        description: "Break when method is called",
        example: "break User#save"
      },
      {
        name: "Exception breakpoints",
        description: "Break when exception occurs",
        example: "catch StandardError"
      },
      {
        name: "Watchpoints",
        description: "Break when variable changes",
        example: "watch @user_name"
      },
      {
        name: "Line breakpoints",
        description: "Break at specific line",
        example: "break /path/to/file.rb:45"
      }
    ]
    
    strategies.each do |strategy|
      puts "#{strategy[:name]}:"
      puts "  #{strategy[:description]}"
      puts "  Example: #{strategy[:example]}"
      puts
    end
  end
  
  def self.debugger_workflow
    puts "\nDebugger Workflow:"
    puts "=" * 50
    
    workflow = [
      "1. Identify the problem area",
      "2. Set breakpoints at key locations",
      "3. Run the program",
      "4. Step through code execution",
      "5. Inspect variables at each step",
      "6. Identify where logic differs from expectations",
      "7. Fix the issue",
      "8. Test the fix",
      "9. Remove breakpoints"
    ]
    
    workflow.each { |step| puts step }
    
    puts "\nDebugging tips:"
    tips = [
      "Start with minimal breakpoints",
      "Use conditional breakpoints to reduce noise",
      "Inspect variables before and after operations",
      "Use the call stack to understand execution flow",
      "Test fixes while still in debugger",
      "Document findings for future reference"
    ]
    
    tips.each { |tip| puts "• #{tip}" }
  end
  
  # Run debugger tools examples
  debugger_setup
  debugger_commands
  breakpoint_strategies
  debugger_workflow
end
```

## 🔧 External Debugging Tools

### 1. IDE Debugging

Debugging with popular IDEs:

```ruby
class IDEDebugging
  def self.vscode_debugging
    puts "VS Code Ruby Debugging:"
    puts "=" * 50
    
    puts "Setup:"
    puts "1. Install Ruby extension (rebornix/Ruby)"
    puts "2. Install debugger gem (debug or byebug)"
    puts "3. Configure launch.json"
    puts "4. Set breakpoints in code"
    puts "5. Start debugging session"
    
    puts "\nlaunch.json configuration:"
    config = {
      "type" => "ruby",
      "name" => "Debug Ruby File",
      "request" => "launch",
      "program" => "${workspaceFolder}/${fileBasenameNoExtension}.rb",
      "args" => [],
      "env" => {
        "RUBY_DEBUG" => "1"
      }
    }
    
    puts JSON.pretty_generate(config)
    
    puts "\nVS Code debugging features:"
    features = [
      "Breakpoint management",
      "Variable inspection",
      "Call stack navigation",
      "Step debugging",
      "Watch expressions",
      "Debug console",
      "Multi-thread debugging",
      "Exception handling"
    ]
    
    features.each { |feature| puts "• #{feature}" }
  end
  
  def self.rubymine_debugging
    puts "\nRubyMine Debugging:"
    puts "=" * 50
    
    puts "RubyMine debugging features:"
    features = [
      "Integrated debugger",
      "Visual breakpoint editor",
      "Variable watches",
      "Expression evaluation",
      "Step filtering",
      "Remote debugging",
      "Rails debugging support",
      "Test debugging"
    ]
    
    features.each { |feature| puts "• #{feature}" }
    
    puts "\nRubyMine debugging workflow:"
    workflow = [
      "1. Set breakpoints by clicking in the gutter",
      "2. Right-click and select 'Debug'",
      "3. Use debugging toolbar",
      "4. Inspect variables in Variables panel",
      "5. Navigate call stack",
      "6. Use Watches for expressions",
      "7. Evaluate expressions in Console"
    ]
    
    workflow.each { |step| puts "  #{step}" }
  end
  
  def self.sublime_text_debugging
    puts "\nSublime Text Ruby Debugging:"
    puts "=" * 50
    
    puts "Setup:"
    setup = [
      "1. Install Sublime Debugger package",
      "2. Install debugger gem",
      "3. Configure debugger settings",
      "4. Set breakpoints with comments",
      "5. Start debugging session"
    ]
    
    setup.each { |step| puts "  #{step}" }
    
    puts "\nSublime Text debugging features:"
    features = [
      "Breakpoint management",
      "Variable inspection",
      "Step debugging",
      "Console integration",
      "Stack trace viewing"
    ]
    
    features.each { |feature| puts "• #{feature}" }
  end
  
  # Run IDE debugging examples
  vscode_debugging
  rubymine_debugging
  sublime_text_debugging
end
```

### 2. Command Line Tools

Command-line debugging utilities:

```ruby
class CommandLineDebugging
  def self.ruby_debug_command
    puts "Ruby Debug Command:"
    puts "=" * 50
    
    puts "Basic usage:"
    puts "  ruby debug your_file.rb"
    puts "  ruby debug -r debug your_file.rb"
    puts "  ruby debug -- your_file.rb"
    
    puts "\nAdvanced options:"
    options = {
      "--stop" => "Stop at the first line",
      "--no-stop" => "Don't stop at the first line",
      "--script" => "Run script file",
      "--command" => "Run command",
      "--post-mortem" => "Enable post-mortem debugging",
      "--nonstop" => "Non-stop mode"
    }
    
    options.each { |opt, desc| puts "  #{opt.ljust(15)}: #{desc}" }
    
    puts "\nExample usage:"
    examples = [
      "ruby debug --stop app.rb",
      "ruby debug --command 'break User#save' app.rb",
      "ruby debug --nonstop app.rb"
    ]
    
    examples.each { |example| puts "  #{example}" }
  end
  
  def self.ruby_profiler
    puts "\nRuby Profiler:"
    puts "=" * 50
    
    puts "Built-in profiler usage:"
    puts "  ruby -r profile your_file.rb"
    puts "  ruby -r profile -p your_file.rb"
    
    puts "\nProfile options:"
    options = {
      "-r profile" => "Enable profiler",
      "-p" => "Print profile results",
      "-p graph_printer" => "Print graph profile",
      "-p flat_printer" => "Print flat profile",
      "-p call_stack_printer" => "Print call stack profile"
    }
    
    options.each { |opt, desc| puts "  #{opt.ljust(25)}: #{desc}" }
    
    # Demonstrate profiling
    puts "\nProfiling example:"
    
    def profile_example
      # Simulate code to profile
      data = (1..1000).to_a
      
      # Operation 1: Map
      mapped = data.map { |n| n * 2 }
      
      # Operation 2: Select
      selected = data.select { |n| n.even? }
      
      # Operation 3: Reduce
      sum = data.reduce(0, :+)
      
      { mapped: mapped.length, selected: selected.length, sum: sum }
    end
    
    puts "To profile this code:"
    puts "ruby -r profile -p graph_printer -e 'require_relative \"debugging_tools\"; CommandLineDebugging.profile_example'"
  end
  
  def self.ruby_trace
    puts "\nRuby Trace:"
    puts "=" * 50
    
    puts "Ruby Trace usage:"
    puts "  ruby -r trace your_file.rb"
    puts "  ruby -r trace --trace your_file.rb"
    
    puts "\nTrace options:"
    options = {
      "--trace" => "Enable tracing",
      "--trace=method" => "Trace method calls",
      "--trace=class" => "Trace class definitions",
      "--trace=all" => "Trace everything"
    }
    
    options.each { |opt, desc| puts "  #{opt.ljust(20)}: #{desc}" }
    
    puts "\nTrace filtering:"
    filters = [
      "Trace specific methods",
      "Trace specific classes",
      "Exclude methods from trace",
      "Set trace depth"
    ]
    
    filters.each { |filter| puts "• #{filter}" }
  end
  
  def self.memory_profiler
    puts "\nMemory Profiler:"
    puts "=" * 50
    
    puts "Memory profiling tools:"
    tools = [
      {
        name: "memory_profiler gem",
        usage: "require 'memory_profiler'; MemoryProfiler.report",
        features: "Memory allocation tracking, object counting"
      },
      {
        name: "ObjectSpace",
        usage: "require 'objspace'; ObjectSpace.count_objects",
        features: "Built-in object counting, memory analysis"
      },
      {
        name: "GC::Profiler",
        usage: "GC::Profiler.enable; GC::Profiler.report",
        features: "Garbage collection profiling"
      }
    ]
    
    tools.each do |tool|
      puts "#{tool[:name]}:"
      puts "  Usage: #{tool[:usage]}"
      puts "  Features: #{tool[:features]}"
      puts
    end
    
    # Demonstrate ObjectSpace
    puts "\nObjectSpace example:"
    
    def objectspace_example
      require 'objspace'
      
      # Create some objects
      strings = []
      100.times { |i| strings << "string_#{i}" }
      
      arrays = []
      50.times { |i| arrays << [i, i * 2] }
      
      hashes = {}
      25.times { |i| hashes[i] = "value_#{i}" }
      
      # Count objects
      counts = ObjectSpace.count_objects
      
      puts "Object counts:"
      counts.each do |type, count|
        next unless type.to_s.start_with?('T_')
        puts "  #{type}: #{count}"
      end
    end
    
    objectspace_example
  end
  
  # Run command line debugging examples
  ruby_debug_command
  ruby_profiler
  ruby_trace
  memory_profiler
end
```

## 🔍 Custom Debugging Tools

### 1. Debugging Utilities

Building custom debugging utilities:

```ruby
class CustomDebuggingTools
  def self.method_tracer
    puts "Method Tracer:"
    puts "=" * 50
    
    class MethodTracer
      def initialize
        @traces = []
        @mutex = Mutex.new
      end
      
      def trace_method(klass, method_name)
        original_method = klass.instance_method(method_name)
        
        klass.define_method(method_name) do |*args, &block|
          start_time = Time.now
          
          result = original_method.bind(self).call(*args, &block)
          
          end_time = Time.now
          duration = end_time - start_time
          
          trace_entry = {
            class: klass.name,
            method: method_name,
            args: args,
            result: result,
            duration: duration,
            timestamp: start_time,
            thread_id: Thread.current.object_id
          }
          
          # Store trace (in real implementation, would use thread-safe storage)
          puts "TRACE: #{klass.name}##{method_name}(#{args.map(&:inspect).join(', ')}) -> #{result.inspect} (#{duration.round(6)}s)"
          
          result
        end
      end
      
      def get_traces
        @mutex.synchronize { @threads.dup }
      end
      
      def clear_traces
        @mutex.synchronize { @traces.clear }
      end
    end
    
    # Test method tracing
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
    
    tracer = MethodTracer.new
    
    # Trace methods
    tracer.trace_method(Calculator, :add)
    tracer.trace_method(Calculator, :multiply)
    tracer.trace_method(Calculator, :complex_operation)
    
    # Test traced methods
    calc = Calculator.new
    
    puts "Testing traced methods:"
    result1 = calc.add(5, 3)
    puts "Add result: #{result1}"
    
    result2 = calc.multiply(4, 7)
    puts "Multiply result: #{result2}"
    
    result3 = calc.complex_operation(2, 3, 4)
    puts "Complex operation result: #{result3}"
  end
  
  def self.variable_watcher
    puts "\nVariable Watcher:"
    puts "=" * 50
    
    class VariableWatcher
      def initialize
        @watched_variables = {}
        @mutex = Mutex.new
      end
      
      def watch(object, variable_name)
        @mutex.synchronize do
          @watched_variables[object.object_id] ||= {}
          @watched_variables[object.object_id][variable_name] = {
            old_value: object.instance_variable_get("@#{variable_name}"),
            changed: false
          }
        end
      end
      
      def check_changes(object)
        @mutex.synchronize do
          object_vars = @watched_variables[object.object_id]
          return unless object_vars
          
          changes = []
          
          object_vars.each do |var_name, var_info|
            current_value = object.instance_variable_get("@#{var_name}")
            
            if current_value != var_info[:old_value]
              changes << {
                variable: var_name,
                old_value: var_info[:old_value],
                new_value: current_value,
                timestamp: Time.now
              }
              
              var_info[:old_value] = current_value
              var_info[:changed] = true
            end
          end
          
          changes
        end
      end
      
      def unwatch(object, variable_name = nil)
        @mutex.synchronize do
          if variable_name
            @watched_variables[object.object_id]&.delete(variable_name)
          else
            @watched_variables.delete(object.object_id)
          end
        end
      end
    end
    
    # Test variable watching
    class TestObject
      attr_accessor :name, :value, :status
      
      def initialize
        @name = "initial"
        @value = 0
        @status = "active"
      end
    end
    
    watcher = VariableWatcher.new
    test_obj = TestObject.new
    
    # Watch variables
    watcher.watch(test_obj, :name)
    watcher.watch(test_obj, :value)
    watcher.watch(test_obj, :status)
    
    # Modify variables
    puts "Modifying variables:"
    test_obj.name = "updated"
    changes = watcher.check_changes(test_obj)
    
    if changes.any?
      changes.each do |change|
        puts "  #{change[:variable]}: #{change[:old_value]} -> #{change[:new_value]}"
      end
    else
      puts "  No changes detected"
    end
    
    test_obj.value = 42
    changes = watcher.check_changes(test_obj)
    
    if changes.any?
      changes.each do |change|
        puts "  #{change[:variable]}: #{change[:old_value]} -> #{change[:new_value]}"
      end
    else
      puts "  No changes detected"
    end
  end
  
  def self.call_stack_analyzer
    puts "\nCall Stack Analyzer:"
    puts "=" * 50
    
    class CallStackAnalyzer
      def initialize
        @stack_depth = 0
        @max_depth = 0
        @method_calls = []
        @mutex = Mutex.new
      end
      
      def analyze_method_call(method_name, &block)
        @mutex.synchronize do
          @stack_depth += 1
          @max_depth = [@max_depth, @stack_depth].max
          
          call_info = {
            method: method_name,
            depth: @stack_depth,
            timestamp: Time.now,
            thread_id: Thread.current.object_id
          }
          
          @method_calls << call_info
          
          puts "CALL: #{'  ' * (@stack_depth - 1)}#{method_name} (depth: #{@stack_depth})"
        end
        
        begin
          result = yield
          
          @mutex.synchronize do
            @stack_depth -= 1
            puts "RETURN: #{'  ' * @stack_depth}#{method_name} (depth: #{@stack_depth})"
          end
          
          result
        rescue => e
          @mutex.synchronize do
            @stack_depth -= 1
            puts "ERROR: #{'  ' * @stack_depth}#{method_name} - #{e.message}"
          end
          
          raise e
        end
      end
      
      def get_stats
        @mutex.synchronize do
          {
            max_depth: @max_depth,
            total_calls: @method_calls.length,
            methods: @method_calls.group_by { |call| call[:method] }.transform_values(&:count),
            threads: @method_calls.group_by { |call| call[:thread_id] }.transform_values(&:count)
          }
        end
      end
    end
    
    # Test call stack analysis
    analyzer = CallStackAnalyzer.new
    
    def recursive_function(n, analyzer)
      analyzer.analyze_method_call("recursive_function(#{n})") do
        if n <= 1
          return 1
        end
        
        result = n + recursive_function(n - 1, analyzer)
        result
      end
    end
    
    def nested_operations(analyzer)
      analyzer.analyze_method_call("nested_operations") do
        analyzer.analyze_method_call("operation_1") do
          sleep(0.01)
          "result1"
        end
        
        analyzer.analyze_method_call("operation_2") do
          sleep(0.01)
          "result2"
        end
        
        analyzer.analyze_method_call("operation_3") do
          sleep(0.01)
          "result3"
        end
      end
    end
    
    # Test call stack analysis
    puts "Testing recursive function:"
    result = recursive_function(3, analyzer)
    puts "Result: #{result}"
    
    puts "\nTesting nested operations:"
    nested_operations(analyzer)
    
    # Show stats
    stats = analyzer.get_stats
    puts "\nCall Stack Statistics:"
    puts "  Max depth: #{stats[:max_depth]}"
    puts "  Total calls: #{stats[:total_calls]}"
    puts "  Method calls: #{stats[:methods]}"
    puts "  Thread usage: #{stats[:threads]}"
  end
  
  def self.performance_monitor
    puts "\nPerformance Monitor:"
    puts "=" * 50
    
    class PerformanceMonitor
      def initialize
        @measurements = {}
        @mutex = Mutex.new
      end
      
      def measure(operation_name, &block)
        start_time = Time.now
        
        begin
          result = yield
          
          end_time = Time.now
          duration = end_time - start_time
          
          record_measurement(operation_name, duration, true)
          
          result
        rescue => e
          end_time = Time.now
          duration = end_time - start_time
          
          record_measurement(operation_name, duration, false)
          
          raise e
        end
      end
      
      def get_stats(operation_name = nil)
        @mutex.synchronize do
          if operation_name
            @measurements[operation_name]
          else
            @measurements
          end
        end
      end
      
      def print_summary
        @mutex.synchronize do
          puts "Performance Summary:"
          
          @measurements.each do |operation, measurements|
            total_time = measurements[:total_time]
            call_count = measurements[:call_count]
            avg_time = total_time / call_count
            success_rate = (measurements[:success_count].to_f / call_count * 100).round(2)
            
            puts "  #{operation}:"
            puts "    Calls: #{call_count}"
            puts "    Average: #{avg_time.round(6)}s"
            puts "    Total: #{total_time.round(6)}s"
            puts "    Success rate: #{success_rate}%"
            puts
          end
        end
      end
      
      private
      
      def record_measurement(operation_name, duration, success)
        @mutex.synchronize do
          @measurements[operation_name] ||= {
            total_time: 0,
            call_count: 0,
            success_count: 0,
            failure_count: 0
          }
          
          measurements = @measurements[operation_name]
          measurements[:total_time] += duration
          measurements[:call_count] += 1
          
          if success
            measurements[:success_count] += 1
          else
            measurements[:failure_count] += 1
          end
        end
      end
    end
    
    # Test performance monitoring
    monitor = PerformanceMonitor.new
    
    # Measure different operations
    monitor.measure("string_concatenation") do
      str = ""
      1000.times { |i| str += "item_#{i}" }
      str
    end
    
    monitor.measure("array_join") do
      items = 1000.times.map { |i| "item_#{i}" }
      items.join
    end
    
    monitor.measure("hash_creation") do
      hash = {}
      1000.times { |i| hash[i] = "value_#{i}" }
      hash
    end
    
    # Test with failures
    3.times do |i|
      monitor.measure("error_operation") do
        if i < 2
          sleep(0.01)
          "success"
        else
          raise "Simulated error"
        end
      end
    rescue => e
      puts "Handled error: #{e.message}"
    end
    
    # Print summary
    monitor.print_summary
  end
  
  # Run custom debugging tools examples
  method_tracer
  variable_watcher
  call_stack_analyzer
  performance_monitor
end
```

### 2. Debugging Framework

Comprehensive debugging framework:

```ruby
class DebuggingFramework
  def self.framework_setup
    puts "Debugging Framework Setup:"
    puts "=" * 50
    
    class DebugFramework
      def initialize(options = {})
        @enabled = options.fetch(:enabled, true)
        @level = options.fetch(:level, :info)
        @output = options.fetch(:output, STDOUT)
        @tracers = {}
        @watchers = {}
        @monitors = {}
      end
      
      def enable
        @enabled = true
      end
      
      def disable
        @enabled = false
      end
      
      def enabled?
        @enabled
      end
      
      def trace_method(klass, method_name)
        return unless @enabled
        
        tracer = MethodTracer.new
        tracer.trace_method(klass, method_name)
        @tracers["#{klass.name}##{method_name}"] = tracer
      end
      
      def watch_variable(object, variable_name)
        return unless @enabled
        
        watcher = VariableWatcher.new
        watcher.watch(object, variable_name)
        @watchers[object.object_id] = watcher
      end
      
      def monitor_performance(operation_name, &block)
        return yield unless @enabled
        
        monitor = PerformanceMonitor.new
        monitor.measure(operation_name, &block)
      end
      
      def log_event(event_name, data = {})
        return unless @enabled
        
        log_entry = {
          timestamp: Time.now.iso8601,
          event: event_name,
          data: data,
          thread_id: Thread.current.object_id
        }
        
        @output.puts(log_entry.to_json)
      end
      
      def debug(message, context = {})
        return unless @enabled && should_log?(:debug)
        
        log_entry = {
          timestamp: Time.now.iso8601,
          level: :debug,
          message: message,
          context: context,
          thread_id: Thread.current.object_id
        }
        
        @output.puts(log_entry.to_json)
      end
      
      def info(message, context = {})
        return unless @enabled && should_log?(:info)
        
        log_entry = {
          timestamp: Time.now.iso8601,
          level: :info,
          message: message,
          context: context,
          thread_id: Thread.current.object_id
        }
        
        @output.puts(log_entry.to_json)
      end
      
      def warn(message, context = {})
        return unless @enabled && should_log?(:warn)
        
        log_entry = {
          timestamp: Time.now.iso8601,
          level: :warn,
          message: message,
          context: context,
          thread_id: Thread.current.object_id
        }
        
        @output.puts(log_entry.to_json)
      end
      
      def error(message, context = {})
        return unless @enabled && should_log?(:error)
        
        log_entry = {
          timestamp: Time.now.iso8601,
          level: :error,
          message: message,
          context: context,
          thread_id: Thread.current.object_id
        }
        
        @output.puts(log_entry.to_json)
      end
      
      def get_stats
        {
          tracers: @tracers.length,
          watchers: @watchers.length,
          monitors: @monitors.length,
          enabled: @enabled,
          level: @level
        }
      end
      
      private
      
      def should_log?(level)
        levels = { debug: 0, info: 1, warn: 2, error: 3 }
        levels[level] >= levels[@level]
      end
    end
    
    # Demonstrate framework usage
    puts "Creating debugging framework..."
    
    framework = DebugFramework.new(
      enabled: true,
      level: :debug,
      output: STDOUT
    )
    
    # Test framework
    framework.info("Framework initialized")
    framework.debug("Debug message", { user_id: 123 })
    framework.warn("Warning message", { operation: "test" })
    framework.error("Error message", { error: "test error" })
    
    # Show stats
    stats = framework.get_stats
    puts "\nFramework stats: #{stats}"
  end
  
  def self.integration_example
    puts "\nIntegration Example:"
    puts "=" * 50
    
    # Example application with debugging framework
    class UserApplication
      def initialize(debug_framework)
        @debug = debug_framework
        @users = {}
        @next_id = 1
      end
      
      def create_user(user_data)
        @debug.monitor_performance("create_user") do
          @debug.debug("Creating user", { user_data: user_data })
          
          # Validate user data
          validate_user_data(user_data)
          
          # Create user
          user_id = @next_id
          @next_id += 1
          
          user = {
            id: user_id,
            name: user_data[:name],
            email: user_data[:email],
            created_at: Time.now
          }
          
          @users[user_id] = user
          
          @debug.info("User created", { user_id: user_id, name: user[:name] })
          
          # Log event
          @debug.log_event("user_created", {
            user_id: user_id,
            name: user[:name],
            email: user[:email]
          })
          
          user
        end
      end
      
      def get_user(user_id)
        @debug.debug("Getting user", { user_id: user_id })
        
        user = @users[user_id]
        
        if user
          @debug.debug("User found", { user_id: user_id, name: user[:name] })
        else
          @debug.warn("User not found", { user_id: user_id })
        end
        
        user
      end
      
      def update_user(user_id, updates)
        @debug.monitor_performance("update_user") do
          @debug.debug("Updating user", { user_id: user_id, updates: updates })
          
          user = @users[user_id]
          
          unless user
            @debug.error("Cannot update non-existent user", { user_id: user_id })
            return nil
          end
          
          # Apply updates
          updates.each do |key, value|
            old_value = user[key]
            user[key] = value
            
            @debug.debug("User field updated", {
              user_id: user_id,
              field: key,
              old_value: old_value,
              new_value: value
            })
          end
          
          @debug.info("User updated", { user_id: user_id })
          
          # Log event
          @debug.log_event("user_updated", {
            user_id: user_id,
            updates: updates
          })
          
          user
        end
      end
      
      def delete_user(user_id)
        @debug.monitor_performance("delete_user") do
          @debug.debug("Deleting user", { user_id: user_id })
          
          user = @users.delete(user_id)
          
          if user
            @debug.info("User deleted", { user_id: user_id, name: user[:name] })
            
            # Log event
            @debug.log_event("user_deleted", {
              user_id: user_id,
              name: user[:name],
              email: user[:email]
            })
          else
            @debug.warn("Cannot delete non-existent user", { user_id: user_id })
          end
          
          user
        end
      end
      
      def get_stats
        @debug.info("Getting application stats")
        
        stats = {
          total_users: @users.length,
          next_id: @next_id,
          user_ids: @users.keys
        }
        
        @debug.info("Application stats", stats)
        stats
      end
      
      private
      
      def validate_user_data(user_data)
        @debug.debug("Validating user data")
        
        unless user_data[:name]
          raise ArgumentError, "Name is required"
        end
        
        unless user_data[:email]
          raise ArgumentError, "Email is required"
        end
        
        unless user_data[:email].include?("@")
          raise ArgumentError, "Invalid email format"
        end
        
        @debug.debug("User data validation passed")
      end
    end
    
    # Test integrated debugging
    puts "Testing integrated debugging framework:"
    
    framework = DebugFramework.new(enabled: true, level: :debug)
    app = UserApplication.new(framework)
    
    # Test user operations
    user = app.create_user({
      name: "John Doe",
      email: "john@example.com"
    })
    
    retrieved_user = app.get_user(user[:id])
    updated_user = app.update_user(user[:id], { name: "Jane Doe" })
    deleted_user = app.delete_user(user[:id])
    
    stats = app.get_stats
    puts "\nFinal application stats: #{stats}"
  end
  
  def self.advanced_features
    puts "\nAdvanced Features:"
    puts "=" * 50
    
    features = [
      {
        name: "Conditional debugging",
        description: "Enable debugging based on conditions",
        example: "debug_if(user.id == 123)"
      },
      {
        name: "Remote debugging",
        description: "Debug remotely running applications",
        example: "Remote debugging server and client"
      },
      {
        name: "Performance profiling",
        description: "Integrated performance monitoring",
        example: "Automatic method timing and analysis"
      },
      {
        name: "Memory debugging",
        description: "Memory usage tracking and leak detection",
        example: "Object allocation monitoring"
      },
      {
        name: "Thread debugging",
        description: "Multi-threaded application debugging",
        example: "Thread state and synchronization monitoring"
      },
      {
        name: "Production debugging",
        description: "Safe debugging in production",
        example: "Non-intrusive logging and monitoring"
      }
    ]
    
    features.each do |feature|
      puts "#{feature[:name]}:"
      puts "  #{feature[:description]}"
      puts "  Example: #{feature[:example]}"
      puts
    end
  end
  
  # Run debugging framework examples
  framework_setup
  integration_example
  advanced_features
end
```

## 🎯 Debugging Best Practices

### 1. Debugging Guidelines

```ruby
class DebuggingBestPractices
  def self.guidelines
    puts "Debugging Best Practices:"
    puts "=" * 50
    
    guidelines = [
      {
        practice: "Start with reproduction",
        description: "Always reproduce the issue before debugging",
        tip: "Create minimal test case that reproduces the bug"
      },
      {
        practice: "Use systematic approach",
        description: "Follow a systematic debugging methodology",
        tip: "Formulate hypotheses and test them"
      },
      {
        practice: "Log appropriately",
        description: "Add meaningful logs for debugging",
        tip: "Include context and use appropriate log levels"
      },
      {
        practice: "Use breakpoints wisely",
        description: "Set breakpoints at strategic locations",
        tip: "Use conditional breakpoints to reduce noise"
      },
      {
        practice: "Inspect state",
        description: "Examine variable values and program state",
        tip: "Check both expected and actual values"
      },
      {
        practice: "Check assumptions",
        description: "Verify your assumptions about the code",
        tip: "Question everything, even obvious facts"
      },
      {
        practice: "Consider edge cases",
        description: "Test boundary conditions and edge cases",
        tip: "Empty inputs, nil values, maximum values"
      },
      {
        practice: "Document findings",
        description: "Document bugs and their solutions",
        tip: "Create knowledge base for future reference"
      }
    ]
    
    guidelines.each do |guideline|
      puts "#{guideline[:practice]}:"
      puts "  #{guideline[:description]}"
      puts "  Tip: #{guideline[:tip]}"
      puts
    end
  end
  
  def self.common_mistakes
    puts "\nCommon Debugging Mistakes:"
    puts "=" * 50
    
    mistakes = [
      {
        mistake: "Assuming without verification",
        problem: "Making assumptions about code behavior",
        solution: "Always verify assumptions with actual testing"
      },
      {
        mistake: "Over-debugging",
        problem: "Adding too many breakpoints and logs",
        solution: "Be strategic about where to place debugging code"
      },
      {
        mistake: "Ignoring error messages",
        problem: "Not reading or understanding error messages",
        solution: "Always read and understand error messages"
      },
      {
        mistake: "Debugging in production",
        problem: "Debugging code in production environment",
        solution: "Use safe debugging techniques in production"
      },
      {
        mistake: "Not isolating the problem",
        problem: "Trying to debug too much code at once",
        solution: "Isolate the problem to a minimal test case"
      },
      {
        mistake: "Forgetting to clean up",
        problem: "Leaving debugging code in production",
        solution: "Always clean up debugging code"
      }
    ]
    
    mistakes.each do |mistake|
      puts "#{mistake[:mistake]}:"
      puts "  Problem: #{mistake[:problem]}"
      puts "  Solution: #{mistake[:solution]}"
      puts
    end
  end
  
  def self.debugging_checklist
    puts "\nDebugging Checklist:"
    puts "=" * 50
    
    checklist = [
      "□ Can you reproduce the issue?",
      "□ Do you understand the expected vs actual behavior?",
      "□ Have you checked error messages?",
      "□ Have you examined relevant logs?",
      "□ Have you verified input data?",
      "□ Have you checked assumptions?",
      "□ Have you tested edge cases?",
      "□ Have you isolated the problem?",
      "□ Have you tried a minimal test case?",
      "□ Have you documented your findings?"
    ]
    
    checklist.each { |item| puts item }
  end
  
  def self.productivity_tips
    puts "\nDebugging Productivity Tips:"
    puts "=" * 50
    
    tips = [
      "Use keyboard shortcuts in your debugger",
      "Learn your IDE's debugging features",
      "Create debugging templates and snippets",
      "Use version control to track debugging progress",
      "Take breaks when stuck on a problem",
      "Pair program when debugging complex issues",
      "Use rubber duck debugging (explain the problem to someone else)",
      "Keep a debugging journal",
      "Automate repetitive debugging tasks",
      "Learn to read stack traces effectively"
    ]
    
    tips.each { |tip| puts "• #{tip}" }
  end
  
  # Run best practices examples
  guidelines
  common_mistakes
  debugging_checklist
  productivity_tips
end
```

## 🎓 Exercises

### Beginner Exercises

1. **IRB Debugging**: Use IRB for interactive debugging
2. **Basic Debugger**: Use Ruby's built-in debugger
3. **Breakpoints**: Set and use breakpoints effectively

### Intermediate Exercises

1. **IDE Debugging**: Use IDE debugging features
2. **Command Line Tools**: Use command-line debugging utilities
3. **Custom Tools**: Build simple debugging utilities

### Advanced Exercises

1. **Debugging Framework**: Build comprehensive debugging framework
2. **Performance Debugging**: Debug performance issues
3. **Production Debugging**: Implement safe production debugging

---

## 🎯 Summary

Debugging tools in Ruby provide:

- **Interactive Tools** - IRB, Pry, and Ruby Debugger
- **IDE Integration** - VS Code, RubyMine, Sublime Text
- **Command Line Tools** - Profilers, tracers, and memory tools
- **Custom Utilities** - Method tracers, variable watchers
- **Debugging Framework** - Comprehensive debugging solutions

Master these tools to efficiently debug and maintain Ruby applications!
