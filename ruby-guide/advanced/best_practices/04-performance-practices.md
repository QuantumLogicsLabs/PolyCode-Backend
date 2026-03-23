# Performance Practices in Ruby
# Comprehensive guide to high-performance Ruby application development

## 🎯 Overview

Performance is crucial for Ruby applications. This guide covers performance optimization techniques, profiling tools, and best practices for building high-performance Ruby applications.

## ⚡ Performance Fundamentals

### 1. Ruby Performance Characteristics

Understanding Ruby's performance characteristics:

```ruby
class PerformanceFundamentals
  def self.ruby_performance_basics
    puts "Ruby Performance Basics:"
    puts "=" * 50
    
    basics = [
      {
        aspect: "Interpreted Language",
        description: "Ruby is interpreted, not compiled",
        impact: "Slower execution than compiled languages",
        optimization: "Use native extensions for performance-critical code"
      },
      {
        aspect: "Dynamic Typing",
        description: "Ruby uses dynamic typing",
        impact: "Runtime type checking overhead",
        optimization: "Use type hints when possible, avoid excessive type checking"
      },
      {
        aspect: "Garbage Collection",
        description: "Ruby uses automatic garbage collection",
        impact: "GC pauses can affect performance",
        optimization: "Minimize object creation, use object pooling"
      },
      {
        aspect: "GIL (Global Interpreter Lock)",
        description: "Ruby has a GIL for thread safety",
        impact: "Limited parallel execution with threads",
        optimization: "Use processes for CPU-bound tasks"
      },
      {
        aspect: "Memory Management",
        description: "Ruby manages memory automatically",
        impact: "Memory usage can grow quickly",
        optimization: "Monitor memory usage, use efficient data structures"
      }
    ]
    
    basics.each do |basic|
      puts "#{basic[:aspect]}:"
      puts "  Description: #{basic[:description]}"
      puts "  Impact: #{basic[:impact]}"
      puts "  Optimization: #{basic[:optimization]}"
      puts
    end
  end
  
  def self.performance_metrics
    puts "\nPerformance Metrics:"
    puts "=" * 50
    
    metrics = [
      {
        metric: "Response Time",
        description: "Time to process a request",
        target: "< 200ms for web requests",
        measurement: "Use benchmarking tools"
      },
      {
        metric: "Throughput",
        description: "Requests per second",
        target: "> 1000 req/s for APIs",
        measurement: "Load testing tools"
      },
      {
        metric: "Memory Usage",
        description: "Memory consumption",
        target: "< 512MB for typical apps",
        measurement: "Memory profiling tools"
      },
      {
        metric: "CPU Usage",
        description: "CPU utilization",
        target: "< 80% under normal load",
        measurement: "System monitoring"
      },
      {
        metric: "Database Queries",
        description: "Database query count and time",
        target: "< 10 queries per request",
        measurement: "Query analysis tools"
      }
    ]
    
    metrics.each do |metric|
      puts "#{metric[:metric]}:"
      puts "  Description: #{metric[:description]}"
      puts "  Target: #{metric[:target]}"
      puts "  Measurement: #{metric[:measurement]}"
      puts
    end
  end
  
  def self.benchmarking_basics
    puts "\nBenchmarking Basics:"
    puts "=" * 50
    
    benchmarking_example = <<~RUBY
      require 'benchmark'
      
      class PerformanceBenchmark
        def self.compare_string_operations
          puts "Comparing String Operations:"
          puts "=" * 40
          
          Benchmark.bm(20) do |x|
            x.report("String concatenation") do
              str = ""
              10000.times { |i| str += "item_#{i}" }
            end
            
            x.report("Array.join") do
              items = 10000.times.map { |i| "item_#{i}" }
              items.join
            end
            
            x.report("StringIO") do
              io = StringIO.new
              10000.times { |i| io.write("item_#{i}") }
              io.string
            end
          end
        end
        
        def self.compare_data_structures
          puts "\nComparing Data Structures:"
          puts "=" * 40
          
          data = (1..10000).to_a
          
          Benchmark.bm(20) do |x|
            x.report("Array lookup") do
              1000.times { data.sample }
            end
            
            x.report("Hash lookup") do
              hash = data.each_with_index.to_h
              1000.times { hash.values.sample }
            end
            
            x.report("Set lookup") do
              set = data.to_set
              1000.times { set.to_a.sample }
            end
          end
        end
        
        def self.compare_iteration_methods
          puts "\nComparing Iteration Methods:"
          puts "=" * 40
          
          data = (1..10000).to_a
          
          Benchmark.bm(20) do |x|
            x.report("each") do
              result = []
              data.each { |item| result << item * 2 }
            end
            
            x.report("map") do
              data.map { |item| item * 2 }
            end
            
            x.report("collect") do
              data.collect { |item| item * 2 }
            end
          end
        end
      end
      
      # Run benchmarks
      PerformanceBenchmark.compare_string_operations
      PerformanceBenchmark.compare_data_structures
      PerformanceBenchmark.compare_iteration_methods
    RUBY
    
    puts "Benchmarking Example:"
    puts benchmarking_example
  end
  
  # Run performance fundamentals examples
  ruby_performance_basics
  performance_metrics
  benchmarking_basics
end
```

### 2. Performance Profiling

Profiling Ruby applications for performance analysis:

```ruby
class PerformanceProfiling
  def self.profiling_tools
    puts "Ruby Profiling Tools:"
    puts "=" * 50
    
    tools = [
      {
        name: "Ruby Profiler",
        description: "Built-in Ruby profiler",
        usage: "ruby -r profile your_script.rb",
        features: [
          "Method call profiling",
          "Execution time analysis",
          "Call stack information",
          "Built-in to Ruby"
        ],
        example: <<~RUBY
          require 'profiler'
          
          Profiler__.start_profile
          # Your code here
          Profiler__.print_profile($stdout)
        RUBY
      },
      {
        name: "Memory Profiler",
        description: "Memory usage profiling",
        usage: "gem install memory_profiler",
        features: [
          "Memory allocation tracking",
          "Object counting",
          "Memory leak detection",
          "GC statistics"
        ],
        example: <<~RUBY
          require 'memory_profiler'
          
          MemoryProfiler.report do
            # Your code here
          end
        RUBY
      },
      {
        name: "StackProf",
        description: "Sampling profiler",
        usage: "gem install stackprof",
        features: [
          "Low overhead profiling",
          "Flame graph generation",
          "Call stack sampling",
          "Production profiling"
        ],
        example: <<~RUBY
          require 'stackprof'
          
          StackProf.run(mode: :cpu, out: 'stackprof.dump') do
            # Your code here
          end
        RUBY
      },
      {
        name: "Ruby-Prof",
        description: "Comprehensive profiling tool",
        usage: "gem install ruby-prof",
        features: [
          "Multiple profiling modes",
          "Call graph generation",
          "HTML reports",
          "Thread profiling"
        ],
        example: <<~RUBY
          require 'ruby-prof'
          
          RubyProf.start
          # Your code here
          result = RubyProf.stop
          
          RubyProf::GraphHtmlPrinter.new(result).print(STDOUT)
        RUBY
      }
    ]
    
    tools.each do |tool|
      puts "#{tool[:name]}:"
      puts "  Description: #{tool[:description]}"
      puts "  Usage: #{tool[:usage]}"
      puts "  Features: #{tool[:features].join(', ')}"
      puts "  Example: #{tool[:example]}"
      puts
    end
  end
  
  def self.profiling_example
    puts "\nProfiling Example:"
    puts "=" * 50
    
    profiling_example = <<~RUBY
      require 'benchmark'
      require 'memory_profiler'
      
      class PerformanceProfiler
        def self.profile_method(method_name, &block)
          puts "Profiling method: #{method_name}"
          puts "=" * 40
          
          # Time profiling
          time = Benchmark.measure do
            block.call
          end
          
          puts "Execution time: #{time.real.round(4)}s"
          puts "User CPU time: #{time.utime.round(4)}s"
          puts "System CPU time: #{time.stime.round(4)}s"
          puts "Total CPU time: #{time.total.round(4)}s"
          
          # Memory profiling
          report = MemoryProfiler.report do
            block.call
          end
          
          puts "\nMemory Usage:"
          puts "  Total allocated: #{report.total_allocated_memsize} bytes"
          puts "  Total retained: #{report.total_retained_memsize} bytes"
          puts "  Allocated objects: #{report.total_allocated}"
          puts "  Retained objects: #{report.total_retained}"
          
          # Object allocation details
          puts "\nObject Allocation Details:"
          report.allocated_objects.each do |type, count|
            puts "  #{type}: #{count} objects"
          end
        end
        
        def self.compare_implementations(implementations)
          implementations.each do |name, implementation|
            puts "\n#{name}:"
            profile_method(name, &implementation)
          end
        end
      end
      
      # Example implementations to compare
      implementations = {
        "String concatenation" => -> {
          str = ""
          1000.times { |i| str += "item_#{i}" }
          str
        },
        
        "Array.join" => -> {
          items = 1000.times.map { |i| "item_#{i}" }
          items.join
        },
        
        "StringIO" => -> {
          io = StringIO.new
          1000.times { |i| io.write("item_#{i}") }
          io.string
        }
      }
      
      # Run comparison
      PerformanceProfiler.compare_implementations(implementations)
    RUBY
    
    puts "Profiling Example:"
    puts profiling_example
  end
  
  def self.hotspot_analysis
    puts "\nHotspot Analysis:"
    puts "=" * 50
    
    hotspot_analysis = <<~RUBY
      class HotspotAnalyzer
        def self.find_hotspots(&block)
          # Use Ruby's built-in profiler
          require 'profiler'
          
          # Create a custom profiler to capture method calls
          profiler = Profiler__.new
          
          # Start profiling
          Profiler__.start_profile
          
          # Execute the code block
          block.call
          
          # Stop profiling and get results
          Profiler__.stop_profile
          
          # Analyze results
          profile_data = Profiler__.profile_data
          
          # Find hotspots (methods called most frequently)
          hotspots = profile_data.sort_by { |method, data| data[:call_count] }.reverse.first(10)
          
          puts "Performance Hotspots:"
          puts "=" * 40
          
          hotspots.each do |method, data|
            puts "#{method}:"
            puts "  Call count: #{data[:call_count]}"
            puts "  Total time: #{data[:total_time].round(4)}s"
            puts "  Average time: #{(data[:total_time] / data[:call_count]).round(6)}s"
            puts
          end
          
          hotspots
        end
        
        def self.analyze_memory_hotspots(&block)
          # Use memory profiler to find memory hotspots
          require 'memory_profiler'
          
          report = MemoryProfiler.report do
            block.call
          end
          
          puts "Memory Hotspots:"
          puts "=" * 40
          
          # Find objects with most allocations
          allocation_hotspots = report.allocated_objects.sort_by { |_, count| -count }.first(10)
          
          puts "Most Allocated Objects:"
          allocation_hotspots.each do |type, count|
            puts "  #{type}: #{count} objects"
          end
          
          # Find objects with most memory usage
          memory_hotspots = report.allocated_objects_by_memory.first(10)
          
          puts "\nMost Memory Usage:"
          memory_hotspots.each do |type, memory|
            puts "  #{type}: #{memory} bytes"
          end
          
          report
        end
      end
      
      # Example usage
      class DataProcessor
        def self.process_data(data)
          # Simulate processing with potential hotspots
          processed = []
          
          data.each_with_index do |item, index|
            # Hotspot 1: String operations
            processed_item = item.to_s.upcase.reverse
            
            # Hotspot 2: Array operations
            processed << processed_item
            
            # Hotspot 3: Hash operations
            metadata = {
              index: index,
              length: processed_item.length,
              checksum: processed_item.sum
            }
            
            processed << metadata
          end
          
          processed
        end
      end
      
      # Analyze hotspots
      data = 1000.times.map { |i| "item_#{i}" }
      
      puts "Analyzing performance hotspots..."
      HotspotAnalyzer.find_hotspots do
        DataProcessor.process_data(data)
      end
      
      puts "\nAnalyzing memory hotspots..."
      HotspotAnalyzer.analyze_memory_hotspots do
        DataProcessor.process_data(data)
      end
    RUBY
    
    puts "Hotspot Analysis Example:"
    puts hotspot_analysis
  end
  
  # Run profiling examples
  profiling_tools
  profiling_example
  hotspot_analysis
end
```

## 🚀 Performance Optimization

### 1. Code Optimization

Optimizing Ruby code for better performance:

```ruby
class CodeOptimization
  def self.string_optimization
    puts "String Optimization:"
    puts "=" * 50
    
    optimizations = [
      {
        technique: "Use String#freeze",
        description: "Freeze strings to prevent modification",
        before: "CONFIG = 'production'",
        after: "CONFIG = 'production'.freeze",
        benefit: "Reduces memory allocation and GC pressure"
      },
      {
        technique: "Use String interpolation",
        description: "Use interpolation instead of concatenation",
        before: "result = 'Hello ' + name + '!'",
        after: "result = \"Hello #{name}!\"",
        benefit: "Faster and more readable"
      },
      {
        technique: "Use String#<< for building",
        description: "Use << for building strings",
        before: "str += 'more'",
        after: "str << 'more'",
        benefit: "Avoids creating new string objects"
      },
      {
        technique: "Use symbols for hash keys",
        description: "Use symbols instead of strings for hash keys",
        before: "hash = { 'name' => 'value' }",
        after: "hash = { name: 'value' }",
        benefit: "Symbols are reused, strings are created"
      }
    ]
    
    optimizations.each do |opt|
      puts "#{opt[:technique]}:"
      puts "  Description: #{opt[:description]}"
      puts "  Before: #{opt[:before]}"
      puts "  After: #{opt[:after]}"
      puts "  Benefit: #{opt[:benefit]}"
      puts
    end
    
    string_optimization_example = <<~RUBY
      class StringOptimizer
        def self.compare_string_operations
          puts "Comparing String Operations:"
          puts "=" * 40
          
          # Test data
          items = 1000.times.map { |i| "item_#{i}" }
          
          Benchmark.bm(20) do |x|
            # String concatenation
            x.report("String concatenation") do
              str = ""
              items.each { |item| str += item }
            end
            
            # String interpolation
            x.report("String interpolation") do
              str = ""
              items.each { |item| str = "#{str}#{item}" }
            end
            
            # String building with <<
            x.report("String building with <<") do
              str = ""
              items.each { |item| str << item }
            end
            
            # Array.join
            x.report("Array.join") do
              items.join
            end
            
            # StringIO
            x.report("StringIO") do
              io = StringIO.new
              items.each { |item| io.write(item) }
              io.string
            end
          end
        end
        
        def self.compare_hash_keys
          puts "\nComparing Hash Keys:"
          puts "=" * 40
          
          data = 1000.times.map { |i| ["key_#{i}", "value_#{i}"] }
          
          Benchmark.bm(20) do |x|
            # String keys
            x.report("String keys") do
              hash = {}
              data.each { |key, value| hash[key] = value }
            end
            
            # Symbol keys
            x.report("Symbol keys") do
              hash = {}
              data.each { |key, value| hash[key.to_sym] = value }
            end
            
            # Frozen string keys
            x.report("Frozen string keys") do
              hash = {}
              data.each { |key, value| hash[key.freeze] = value }
            end
          end
        end
      end
      
      # Run optimizations
      StringOptimizer.compare_string_operations
      StringOptimizer.compare_hash_keys
    RUBY
    
    puts "\nString Optimization Example:"
    puts string_optimization_example
  end
  
  def self.array_optimization
    puts "\nArray Optimization:"
    puts "=" * 50
    
    optimizations = [
      {
        technique: "Use Array#select instead of Array#find_all",
        description: "select is more efficient than find_all",
        before: "result = array.find_all { |item| item.active? }",
        after: "result = array.select { |item| item.active? }",
        benefit: "select is faster and more idiomatic"
      },
      {
        technique: "Use Array#map instead of Array#collect",
        description: "map is more efficient than collect",
        before: "result = array.collect { |item| item.name }",
        after: "result = array.map { |item| item.name }",
        benefit: "map is faster and more idiomatic"
      },
      {
        technique: "Use Array#any? and Array#all?",
        description: "Use specific methods for boolean checks",
        before: "array.select { |item| item.active? }.any?",
        after: "array.any? { |item| item.active? }",
        benefit: "Stops early, more efficient"
      },
      {
        technique: "Use Array#first and Array#last",
        description: "Use specific methods for first/last elements",
        before: "array[0] and array[-1]",
        after: "array.first and array.last",
        benefit: "More readable and efficient"
      }
    ]
    
    optimizations.each do |opt|
      puts "#{opt[:technique]}:"
      puts "  Description: #{opt[:description]}"
      puts "  Before: #{opt[:before]}"
      puts "  After: #{opt[:after]}"
      puts "  Benefit: #{opt[:benefit]}"
      puts
    end
    
    array_optimization_example = <<~RUBY
      class ArrayOptimizer
        def self.compare_array_operations
          puts "Comparing Array Operations:"
          puts "=" * 40
          
          data = (1..10000).to_a
          
          Benchmark.bm(20) do |x|
            # Array#select vs Array#find_all
            x.report("Array#select") do
              data.select { |item| item.even? }
            end
            
            x.report("Array#find_all") do
              data.find_all { |item| item.even? }
            end
            
            # Array#any? vs Array#select.any?
            x.report("Array#any?") do
              data.any? { |item| item.even? }
            end
            
            x.report("Array#select.any?") do
              data.select { |item| item.even? }.any?
            end
            
            # Array#first vs Array#[0]
            x.report("Array#first") do
              1000.times { data.first }
            end
            
            x.report("Array#[0]") do
              1000.times { data[0] }
            end
          end
        end
        
        def self.compare_iteration_methods
          puts "\nComparing Iteration Methods:"
          puts "=" * 40
          
          data = (1..10000).to_a
          
          Benchmark.bm(20) do |x|
            # Array#each
            x.report("Array#each") do
              result = []
              data.each { |item| result << item * 2 }
            end
            
            # Array#map
            x.report("Array#map") do
              data.map { |item| item * 2 }
            end
            
            # Array#map!
            x.report("Array#map!") do
              data.map! { |item| item * 2 }
            end
            
            # Array#collect
            x.report("Array#collect") do
              data.collect { |item| item * 2 }
            end
          end
        end
      end
      
      # Run optimizations
      ArrayOptimizer.compare_array_operations
      ArrayOptimizer.compare_iteration_methods
    RUBY
    
    puts "\nArray Optimization Example:"
    puts array_optimization_example
  end
  
  def self.hash_optimization
    puts "\nHash Optimization:"
    puts "=" * 50
    
    optimizations = [
      {
        technique: "Use symbol keys",
        description: "Symbols are more efficient than strings",
        before: "hash = { 'name' => 'John', 'age' => 30 }",
        after: "hash = { name: 'John', age: 30 }",
        benefit: "Symbols are reused, strings are created"
      },
      {
        technique: "Use Hash#fetch with default",
        description: "Use fetch with default value",
        before: "hash[:key] || 'default'",
        after: "hash.fetch(:key, 'default')",
        benefit: "More explicit and efficient"
      },
      {
        technique: "Use Hash#key? instead of Hash#has_key?",
        description: "key? is more efficient",
        before: "hash.has_key?(:key)",
        after: "hash.key?(:key)",
        benefit: "key? is faster and more idiomatic"
      },
      {
        technique: "Use Hash#transform_values",
        description: "Use transform_values for value transformation",
        before: "hash.transform_values { |v| v * 2 }",
        after: "hash.transform_values { |v| v * 2 }",
        benefit: "More efficient and readable"
      }
    ]
    
    optimizations.each do |opt|
      puts "#{opt[:technique]}:"
      puts "  Description: #{opt[:description]}"
      puts "  Before: #{opt[:before]}"
      puts "  After: #{opt[:after]}"
      puts "  Benefit: #{opt[:benefit]}"
      puts
    end
    
    hash_optimization_example = <<~RUBY
      class HashOptimizer
        def self.compare_hash_operations
          puts "Comparing Hash Operations:"
          puts "=" * 40
          
          data = 1000.times.map { |i| ["key_#{i}", "value_#{i}"] }.to_h
          
          Benchmark.bm(20) do |x|
            # Symbol keys vs String keys
            x.report("String keys") do
              hash = {}
              data.each { |key, value| hash[key] = value }
            end
            
            x.report("Symbol keys") do
              hash = {}
              data.each { |key, value| hash[key.to_sym] = value }
            end
            
            # Hash#fetch vs Hash#[] ||
            x.report("Hash#[] ||") do
              data.each { |key, value| hash[key] || 'default' }
            end
            
            x.report("Hash#fetch") do
              data.each { |key, value| hash.fetch(key, 'default') }
            end
            
            # Hash#key? vs Hash#has_key?
            x.report("Hash#has_key?") do
              data.each { |key, value| hash.has_key?(key) }
            end
            
            x.report("Hash#key?") do
              data.each { |key, value| hash.key?(key) }
            end
          end
        end
        
        def self.compare_hash_iterations
          puts "\nComparing Hash Iterations:"
          puts "=" * 40
          
          data = 1000.times.map { |i| ["key_#{i}", "value_#{i}"] }.to_h
          
          Benchmark.bm(20) do |x|
            # Hash#each
            x.report("Hash#each") do
              result = {}
              data.each { |key, value| result[key] = value.upcase }
            end
            
            # Hash#map
            x.report("Hash#map") do
              data.map { |key, value| [key, value.upcase] }.to_h
            end
            
            # Hash#transform_values
            x.report("Hash#transform_values") do
              data.transform_values { |value| value.upcase }
            end
            
            # Hash#transform_keys
            x.report("Hash#transform_keys") do
              data.transform_keys { |key| key.to_sym }
            end
          end
        end
      end
      
      # Run optimizations
      HashOptimizer.compare_hash_operations
      HashOptimizer.compare_hash_iterations
    RUBY
    
    puts "\nHash Optimization Example:"
    puts hash_optimization_example
  end
  
  # Run code optimization examples
  string_optimization
  array_optimization
  hash_optimization
end
```

### 2. Memory Optimization

Optimizing memory usage in Ruby applications:

```ruby
class MemoryOptimization
  def self.memory_management
    puts "Memory Management:"
    puts "=" * 50
    
    management = [
      {
        technique: "Minimize object creation",
        description: "Avoid creating unnecessary objects",
        examples: [
          "Reuse objects when possible",
          "Use object pools",
          "Avoid string concatenation in loops"
        ]
      },
      {
        technique: "Use appropriate data structures",
        description: "Choose efficient data structures",
        examples: [
          "Use Set for unique values",
          "Use Array for ordered data",
          "Use Hash for key-value pairs"
        ]
      },
      {
        technique: "Optimize garbage collection",
        description: "Reduce GC pressure",
        examples: [
          "Use object pooling",
          "Minimize temporary objects",
          "Use GC tuning parameters"
        ]
      },
      {
        technique: "Monitor memory usage",
        description: "Track memory consumption",
        examples: [
          "Use memory profiling tools",
          "Monitor GC statistics",
          "Set memory limits"
        ]
      }
    ]
    
    management.each do |mgt|
      puts "#{mgt[:technique]}:"
      puts "  Description: #{mgt[:description]}"
      puts "  Examples: #{mgt[:examples].join(', ')}"
      puts
    end
  end
  
  def self.object_pooling
    puts "\nObject Pooling:"
    puts "=" * 50
    
    object_pooling_example = <<~RUBY
      class ObjectPool
        def initialize(create_proc, reset_proc = nil)
          @create_proc = create_proc
          @reset_proc = reset_proc
          @pool = []
          @mutex = Mutex.new
        end
        
        def with_object(&block)
          obj = checkout
          begin
            yield obj
          ensure
            checkin(obj)
          end
        end
        
        private
        
        def checkout
          @mutex.synchronize do
            if @pool.empty?
              @create_proc.call
            else
              @pool.pop
            end
          end
        end
        
        def checkin(obj)
          @mutex.synchronize do
            if @reset_proc
              @reset_proc.call(obj)
            end
            @pool.push(obj)
          end
        end
      end
      
      # Example: Database connection pool
      class DatabaseConnectionPool
        def initialize(size = 5)
          @pool = ObjectPool.new(
            -> { create_connection },
            ->(conn) { reset_connection(conn) }
          )
          @size = size
        end
        
        def with_connection(&block)
          @pool.with_object(&block)
        end
        
        private
        
        def create_connection
          # Simulate database connection creation
          puts "Creating new database connection"
          { id: SecureRandom.hex(8), active: true }
        end
        
        def reset_connection(conn)
          # Reset connection state
          conn[:active] = true
        end
      end
      
      # Example: String buffer pool
      class StringBufferPool
        def initialize(size = 10)
          @pool = ObjectPool.new(
            -> { StringIO.new },
            ->(buffer) { buffer.rewind }
          )
          @size = size
        end
        
        def with_buffer(&block)
          @pool.with_object(&block)
        end
      end
      
      # Usage examples
      def self.demonstrate_object_pooling
        puts "Demonstrating Object Pooling:"
        puts "=" * 40
        
        # Database connection pool
        db_pool = DatabaseConnectionPool.new(3)
        
        puts "Using database connection pool:"
        5.times do |i|
          db_pool.with_connection do |conn|
            puts "Using connection #{conn[:id]} for operation #{i}"
            sleep(0.1)  # Simulate work
          end
        end
        
        # String buffer pool
        string_pool = StringBufferPool.new(5)
        
        puts "\nUsing string buffer pool:"
        5.times do |i|
          string_pool.with_buffer do |buffer|
            buffer.write("Operation #{i} data")
            puts "Buffer content: #{buffer.string}"
          end
        end
      end
      
      # Performance comparison
      def self.compare_pooling_performance
        puts "\nComparing Pooling Performance:"
        puts "=" * 40
        
        # Without pooling
        Benchmark.bm(20) do |x|
          x.report("Without pooling") do
            1000.times do
              conn = { id: SecureRandom.hex(8), active: true }
              # Simulate work
              sleep(0.001)
            end
          end
          
          x.report("With pooling") do
            pool = DatabaseConnectionPool.new(10)
            1000.times do
              pool.with_connection do |conn|
                # Simulate work
                sleep(0.001)
              end
            end
          end
        end
      end
    RUBY
    
    puts "Object Pooling Example:"
    puts object_pooling_example
  end
  
  def self.memory_profiling
    puts "\nMemory Profiling:"
    puts "=" * 50
    
    memory_profiling_example = <<~RUBY
      require 'memory_profiler'
      require 'objspace'
      
      class MemoryProfiler
        def self.profile_memory_usage(&block)
          puts "Profiling Memory Usage:"
          puts "=" * 40
          
          # Take initial snapshot
          GC.start
          before = ObjectSpace.count_objects
          
          # Profile memory
          report = MemoryProfiler.report do
            block.call
          end
          
          # Take final snapshot
          GC.start
          after = ObjectSpace.count_objects
          
          # Calculate differences
          delta = after.transform_values { |after_val, before_val| after_val - before_val }
          
          puts "Memory Usage Analysis:"
          puts "  Total allocated: #{report.total_allocated_memsize} bytes"
          puts "  Total retained: #{report.total_retained_memsize} bytes"
          puts "  Allocated objects: #{report.total_allocated}"
          puts "  Retained objects: #{report.total_retained}"
          
          puts "\nObject Creation:"
          delta.each do |type, count|
            next unless count > 0
            puts "  #{type}: +#{count} objects"
          end
          
          # Show most allocated types
          puts "\nMost Allocated Types:"
          report.allocated_objects.sort_by { |_, count| -count }.first(10).each do |type, count|
            puts "  #{type}: #{count} objects"
          end
          
          # Show most memory usage
          puts "\nMost Memory Usage:"
          report.allocated_objects_by_memory.first(10).each do |type, memory|
            puts "  #{type}: #{memory} bytes"
          end
          
          report
        end
        
        def self.find_memory_leaks(&block)
          puts "Finding Memory Leaks:"
          puts "=" * 40
          
          # Run code multiple times to detect leaks
          reports = []
          
          5.times do |i|
            puts "Run #{i + 1}:"
            report = profile_memory_usage(&block)
            reports << report
            sleep(0.1)  # Allow GC to run
          end
          
          # Analyze for leaks
          puts "\nMemory Leak Analysis:"
          
          # Check if memory usage is increasing
          allocated_sizes = reports.map(&:total_allocated_memsize)
          retained_sizes = reports.map(&:total_retained_memsize)
          
          if allocated_sizes.last > allocated_sizes.first * 1.5
            puts "  Potential memory leak detected in allocated memory"
          end
          
          if retained_sizes.last > retained_sizes.first * 1.5
            puts "  Potential memory leak detected in retained memory"
          end
          
          # Check object counts
          object_counts = reports.map(&:total_allocated)
          if object_counts.last > object_counts.first * 1.5
            puts "  Potential memory leak detected in object creation"
          end
        end
      end
      
      # Example usage
      class DataProcessor
        def self.process_data(data)
          # Create temporary objects
          results = []
          
          data.each do |item|
            # Create string objects
            processed = item.to_s.upcase.reverse
            
            # Create array objects
            metadata = [item.length, item.sum, item.hash]
            
            # Create hash objects
            result = {
              original: item,
              processed: processed,
              metadata: metadata
            }
            
            results << result
          end
          
          results
        end
      end
      
      # Profile memory usage
      data = 1000.times.map { |i| "item_#{i}" }
      
      MemoryProfiler.profile_memory_usage do
        DataProcessor.process_data(data)
      end
      
      # Check for memory leaks
      MemoryProfiler.find_memory_leaks do
        10.times do
          DataProcessor.process_data(data)
        end
      end
    RUBY
    
    puts "Memory Profiling Example:"
    puts memory_profiling_example
  end
  
  # Run memory optimization examples
  memory_management
  object_pooling
  memory_profiling
end
```

## 🔄 Concurrency Performance

### 1. Thread Performance

Optimizing thread-based concurrency:

```ruby
class ThreadPerformance
  def self.thread_optimization
    puts "Thread Optimization:"
    puts "=" * 50
    
    optimizations = [
      {
        technique: "Use thread pools",
        description: "Reuse threads instead of creating new ones",
        benefit: "Reduces thread creation overhead"
      },
      {
        technique: "Minimize thread contention",
        description: "Reduce shared resource usage",
        benefit: "Improves parallel execution"
      },
      {
        technique: "Use concurrent data structures",
        description: "Use thread-safe collections",
        benefit: "Reduces synchronization overhead"
      },
      {
        technique: "Optimize thread count",
        description: "Use optimal number of threads",
        benefit: "Maximizes CPU utilization"
      }
    ]
    
    optimizations.each do |opt|
      puts "#{opt[:technique]}:"
      puts "  Description: #{opt[:description]}"
      puts "  Benefit: #{opt[:benefit]}"
      puts
    end
  end
  
  def self.thread_pool_implementation
    puts "\nThread Pool Implementation:"
    puts "=" * 50
    
    thread_pool_example = <<~RUBY
      class ThreadPool
        def initialize(size = 4)
          @size = size
          @queue = Queue.new
          @threads = []
          @shutdown = false
          
          start_threads
        end
        
        def submit(&block)
          return nil if @shutdown
          
          task = Task.new(block)
          @queue.push(task)
          task
        end
        
        def shutdown
          @shutdown = true
          
          @threads.each(&:join)
        end
        
        private
        
        def start_threads
          @size.times do
            @threads << Thread.new do
              worker_loop
            end
          end
        end
        
        def worker_loop
          until @shutdown
            task = @queue.pop
            task.execute
          end
        end
      end
      
      class Task
        def initialize(block)
          @block = block
          @result = nil
          @exception = nil
          @completed = false
          @mutex = Mutex.new
          @condition = ConditionVariable.new
        end
        
        def execute
          begin
            @result = @block.call
          rescue => e
            @exception = e
          ensure
            @mutex.synchronize do
              @completed = true
              @condition.broadcast
            end
          end
        end
        
        def result
          wait_for_completion
          @exception ? raise(@exception) : @result
        end
        
        def completed?
          @completed
        end
        
        private
        
        def wait_for_completion
          @mutex.synchronize do
            @condition.wait(@mutex) until @completed
          end
        end
      end
      
      # Usage example
      def self.demonstrate_thread_pool
        puts "Demonstrating Thread Pool:"
        puts "=" * 40
        
        pool = ThreadPool.new(4)
        
        # Submit tasks
        tasks = []
        10.times do |i|
          task = pool.submit do
            puts "Processing task #{i}"
            sleep(rand(0.1..0.5))
            "Result #{i}"
          end
          tasks << task
        end
        
        # Wait for completion
        results = tasks.map(&:result)
        puts "All tasks completed: #{results.join(', ')}"
        
        pool.shutdown
      end
      
      # Performance comparison
      def self.compare_thread_performance
        puts "\nComparing Thread Performance:"
        puts "=" * 40
        
        tasks = 100.times.map { |i| -> { sleep(0.01); "Task #{i}" } }
        
        Benchmark.bm(20) do |x|
          # Without thread pool
          x.report("Without thread pool") do
            threads = []
            tasks.each do |task|
              threads << Thread.new(&task)
            end
            threads.each(&:join)
          end
          
          # With thread pool
          x.report("With thread pool") do
            pool = ThreadPool.new(4)
            
            futures = tasks.map do |task|
              pool.submit(&task)
            end
            
            futures.each(&:result)
            pool.shutdown
          end
        end
      end
    RUBY
    
    puts "Thread Pool Example:"
    puts thread_pool_example
  end
  
  def self.concurrent_data_structures
    puts "\nConcurrent Data Structures:"
    puts "=" * 50
    
    concurrent_structures_example = <<~RUBY
      require 'concurrent-ruby'
      
      # Thread-safe array
      class ConcurrentArray
        def initialize
          @array = []
          @mutex = Mutex.new
        end
        
        def push(item)
          @mutex.synchronize do
            @array.push(item)
          end
        end
        
        def pop
          @mutex.synchronize do
            @array.pop
          end
        end
        
        def each(&block)
          @mutex.synchronize do
            @array.each(&block)
          end
        end
        
        def size
          @mutex.synchronize { @array.size }
        end
      end
      
      # Thread-safe hash
      class ConcurrentHash
        def initialize
          @hash = {}
          @mutex = Mutex.new
        end
        
        def put(key, value)
          @mutex.synchronize do
            @hash[key] = value
          end
        end
        
        def get(key)
          @mutex.synchronize do
            @hash[key]
          end
        end
        
        def delete(key)
          @mutex.synchronize do
            @hash.delete(key)
          end
        end
        
        def keys
          @mutex.synchronize { @hash.keys }
        end
        
        def size
          @mutex.synchronize { @hash.size }
        end
      end
      
      # Thread-safe queue
      class ConcurrentQueue
        def initialize
          @queue = Queue.new
        end
        
        def push(item)
          @queue.push(item)
        end
        
        def pop
          @queue.pop
        end
        
        def empty?
          @queue.empty?
        end
        
        def size
          @queue.size
        end
      end
      
      # Usage examples
      def self.demonstrate_concurrent_structures
        puts "Demonstrating Concurrent Data Structures:"
        puts "=" * 40
        
        # Concurrent array
        array = ConcurrentArray.new
        threads = []
        
        5.times do |i|
          threads << Thread.new do
            10.times { |j| array.push("item_#{i}_#{j}") }
          end
        end
        
        threads.each(&:join)
        puts "Concurrent array size: #{array.size}"
        
        # Concurrent hash
        hash = ConcurrentHash.new
        threads = []
        
        5.times do |i|
          threads << Thread.new do
            10.times { |j| hash.put("key_#{i}_#{j}", "value_#{i}_#{j}") }
          end
        end
        
        threads.each(&:join)
        puts "Concurrent hash size: #{hash.size}"
        
        # Concurrent queue
        queue = ConcurrentQueue.new
        threads = []
        
        # Producer threads
        3.times do |i|
          threads << Thread.new do
            10.times { |j| queue.push("item_#{i}_#{j}") }
          end
        end
        
        # Consumer threads
        3.times do |i|
          threads << Thread.new do
            10.times { queue.pop }
          end
        end
        
        threads.each(&:join)
        puts "Concurrent queue size: #{queue.size}"
      end
      
      # Performance comparison
      def self.compare_concurrent_structures
        puts "\nComparing Concurrent Structures:"
        puts "=" * 40
        
        Benchmark.bm(20) do |x|
          # Regular array vs Concurrent array
          regular_array = []
          concurrent_array = ConcurrentArray.new
          
          x.report("Regular array") do
            threads = []
            5.times do
              threads << Thread.new do
                1000.times { |i| regular_array << i }
              end
            end
            threads.each(&:join)
          end
          
          x.report("Concurrent array") do
            threads = []
            5.times do
              threads << Thread.new do
                1000.times { |i| concurrent_array.push(i) }
              end
            end
            threads.each(&:join)
          end
        end
      end
    RUBY
    
    puts "Concurrent Data Structures Example:"
    puts concurrent_structures_example
  end
  
  # Run thread performance examples
  thread_optimization
  thread_pool_implementation
  concurrent_data_structures
end
```

### 2. Process Performance

Optimizing process-based concurrency:

```ruby
class ProcessPerformance
  def self.process_optimization
    puts "Process Optimization:"
    puts "=" * 50
    
    optimizations = [
      {
        technique: "Use process pools",
        description: "Reuse processes instead of creating new ones",
        benefit: "Reduces process creation overhead"
      },
      {
        technique: "Optimize inter-process communication",
        description: "Use efficient IPC mechanisms",
        benefit: "Reduces communication overhead"
      },
      {
        technique: "Use shared memory",
        description: "Share memory between processes",
        benefit: "Reduces data transfer overhead"
      },
      {
        technique: "Optimize process count",
        description: "Use optimal number of processes",
        benefit: "Maximizes CPU utilization"
      }
    ]
    
    optimizations.each do |opt|
      puts "#{opt[:technique]}:"
      puts "  Description: #{opt[:description]}"
      puts "  Benefit: #{opt[:benefit]}"
      puts
    end
  end
  
  def self.process_pool_implementation
    puts "\nProcess Pool Implementation:"
    puts "=" * 50
    
    process_pool_example = <<~RUBY
      class ProcessPool
        def initialize(size = 4)
          @size = size
          @workers = []
          @available_workers = Queue.new
          @shutdown = false
          
          start_workers
        end
        
        def submit(&block)
          return nil if @shutdown
          
          worker = @available_workers.pop
          worker.execute(block)
        end
        
        def shutdown
          @shutdown = true
          
          @workers.each(&:shutdown)
        end
        
        private
        
        def start_workers
          @size.times do
            worker = Worker.new
            @workers << worker
            @available_workers.push(worker)
          end
        end
      end
      
      class Worker
        def initialize
          @read_pipe, @write_pipe = IO.pipe
          @pid = fork do
            worker_loop
          end
        end
        
        def execute(block)
          Marshal.dump(block, @write_pipe)
          @read_pipe.gets
        end
        
        def shutdown
          Process.kill('TERM', @pid)
          Process.wait(@pid)
        end
        
        private
        
        def worker_loop
          loop do
            begin
              block = Marshal.load(@read_pipe)
              result = block.call
              Marshal.dump(result, STDOUT)
            rescue => e
              Marshal.dump(e, STDOUT)
            end
          end
        end
      end
      
      # Usage example
      def self.demonstrate_process_pool
        puts "Demonstrating Process Pool:"
        puts "=" * 40
        
        pool = ProcessPool.new(4)
        
        # Submit tasks
        10.times do |i|
          pool.submit do
            puts "Processing task #{i} in process #{Process.pid}"
            sleep(rand(0.1..0.5))
            "Result #{i}"
          end
        end
        
        sleep(2)  # Wait for completion
        pool.shutdown
      end
      
      # Performance comparison
      def self.compare_process_performance
        puts "\nComparing Process Performance:"
        puts "=" * 40
        
        tasks = 100.times.map { |i| -> { sleep(0.01); "Task #{i}" } }
        
        Benchmark.bm(20) do |x|
          # Without process pool
          x.report("Without process pool") do
            processes = []
            tasks.each do |task|
              processes << fork(&task)
            end
            processes.each { |pid| Process.wait(pid) }
          end
          
          # With process pool
          x.report("With process pool") do
            pool = ProcessPool.new(4)
            
            tasks.each do |task|
              pool.submit(&task)
            end
            
            sleep(2)  # Wait for completion
            pool.shutdown
          end
        end
      end
    RUBY
    
    puts "Process Pool Example:"
    puts process_pool_example
  end
  
  def self.parallel_processing
    puts "\nParallel Processing:"
    puts "=" * 50
    
    parallel_processing_example = <<~RUBY
      class ParallelProcessor
        def self.map(data, &block)
          return data.map(&block) if data.size < 2
          
          # Divide data into chunks
          chunk_size = (data.size.to_f / processor_count).ceil
          chunks = data.each_slice(chunk_size).to_a
          
          # Process chunks in parallel
          results = chunks.map.with_index do |chunk, index|
            Thread.new do
              chunk.map(&block)
            end
          end
          
          # Wait for completion and combine results
          results.map(&:value).flatten
        end
        
        def self.each(data, &block)
          return data.each(&block) if data.size < 2
          
          # Divide data into chunks
          chunk_size = (data.size.to_f / processor_count).ceil
          chunks = data.each_slice(chunk_size).to_a
          
          # Process chunks in parallel
          threads = chunks.map do |chunk|
            Thread.new do
              chunk.each(&block)
            end
          end
          
          # Wait for completion
          threads.each(&:join)
        end
        
        def self.reduce(data, initial = nil, &block)
          return data.reduce(initial, &block) if data.size < 2
          
          # Divide data into chunks
          chunk_size = (data.size.to_f / processor_count).ceil
          chunks = data.each_slice(chunk_size).to_a
          
          # Process chunks in parallel
          results = chunks.map do |chunk|
            Thread.new do
              chunk.reduce(initial, &block)
            end
          end
          
          # Combine results
          results.map(&:value).reduce(initial, &block)
        end
        
        private
        
        def self.processor_count
          [processor_count, data.size].min
        end
      end
      
      # Usage examples
      def self.demonstrate_parallel_processing
        puts "Demonstrating Parallel Processing:"
        puts "=" * 40
        
        data = (1..1000).to_a
        
        # Parallel map
        results = ParallelProcessor.map(data) do |item|
          item * 2
        end
        puts "Parallel map result: #{results.first(5)}..."
        
        # Parallel each
        ParallelProcessor.each(data) do |item|
          # Process item
        end
        puts "Parallel each completed"
        
        # Parallel reduce
        sum = ParallelProcessor.reduce(data, 0) do |acc, item|
          acc + item
        end
        puts "Parallel reduce result: #{sum}"
      end
      
      # Performance comparison
      def self.compare_parallel_performance
        puts "\nComparing Parallel Performance:"
        puts "=" * 40
        
        data = (1..10000).to_a
        
        Benchmark.bm(20) do |x|
          # Sequential map
          x.report("Sequential map") do
            data.map { |item| item * 2 }
          end
          
          # Parallel map
          x.report("Parallel map") do
            ParallelProcessor.map(data) { |item| item * 2 }
          end
          
          # Sequential each
          x.report("Sequential each") do
            data.each { |item| item * 2 }
          end
          
          # Parallel each
          x.report("Parallel each") do
            ParallelProcessor.each(data) { |item| item * 2 }
          end
        end
      end
    RUBY
    
    puts "Parallel Processing Example:"
    puts parallel_processing_example
  end
  
  # Run process performance examples
  process_optimization
  process_pool_implementation
  parallel_processing
end
```

## 🎯 Performance Best Practices

### 1. Performance Guidelines

Comprehensive performance best practices:

```ruby
class PerformanceBestPractices
  def self.performance_guidelines
    puts "Performance Guidelines:"
    puts "=" * 50
    
    guidelines = [
      {
        category: "Code Optimization",
        practices: [
          "Use efficient algorithms and data structures",
          "Minimize object creation",
          "Use built-in methods instead of custom implementations",
          "Avoid unnecessary computations",
          "Use memoization for expensive operations"
        ]
      },
      {
        category: "Memory Management",
        practices: [
          "Monitor memory usage",
          "Use object pooling",
          "Minimize temporary objects",
          "Optimize garbage collection",
          "Use appropriate data structures"
        ]
      },
      {
        category: "Concurrency",
        practices: [
          "Use thread pools",
          "Minimize thread contention",
          "Use concurrent data structures",
          "Optimize thread/process count",
          "Use parallel processing for CPU-bound tasks"
        ]
      },
      {
        category: "Database Performance",
        practices: [
          "Use database connection pooling",
          "Optimize database queries",
          "Use database indexing",
          "Minimize database round trips",
          "Use database caching"
        ]
      },
      {
        category: "Network Performance",
        practices: [
          "Use connection pooling",
          "Minimize network round trips",
          "Use compression",
          "Use caching",
          "Optimize API responses"
        ]
      },
      {
        category: "Monitoring and Profiling",
        practices: [
          "Monitor performance metrics",
          "Profile regularly",
          "Set performance alerts",
          "Use APM tools",
          "Track performance trends"
        ]
      }
    ]
    
    guidelines.each do |guideline|
      puts "#{guideline[:category]}:"
      guideline[:practices].each { |practice| puts "  • #{practice}" }
      puts
    end
  end
  
  def self.performance_antipatterns
    puts "\nPerformance Anti-patterns:"
    puts "=" * 50
    
    antipatterns = [
      {
        pattern: "String concatenation in loops",
        problem: "Creates many temporary string objects",
        solution: "Use StringIO or Array.join"
      },
      {
        pattern: "Excessive object creation",
        problem: "Increases GC pressure",
        solution: "Reuse objects or use object pooling"
      },
      {
        pattern: "N+1 queries",
        problem: "Multiple database queries",
        solution: "Use eager loading or batch queries"
      },
      {
        pattern: "Inefficient algorithms",
        problem: "Poor time complexity",
        solution: "Use appropriate algorithms and data structures"
      },
      {
        pattern: "Blocking operations",
        problem: "Blocks execution",
        solution: "Use asynchronous operations"
      },
      {
        pattern: "Memory leaks",
        problem: "Memory usage grows over time",
        solution: "Proper memory management and cleanup"
      },
      {
        pattern: "Thread contention",
        problem: "Threads compete for resources",
        solution: "Use concurrent data structures and minimize shared state"
      },
      {
        pattern: "Inefficient caching",
        problem: "Cache misses and invalidation issues",
        solution: "Use appropriate caching strategies"
      }
    ]
    
    antipatterns.each do |antipattern|
      puts "#{antipattern[:pattern]}:"
      puts "  Problem: #{antipattern[:problem]}"
      puts "  Solution: #{antipattern[:solution]}"
      puts
    end
  end
  
  def self.performance_checklist
    puts "\nPerformance Checklist:"
    puts "=" * 50
    
    checklist = [
      "□ Profile code to identify bottlenecks",
      "□ Use efficient algorithms and data structures",
      "□ Minimize object creation",
      "□ Use object pooling for expensive objects",
      "□ Optimize database queries",
      "□ Use database connection pooling",
      "□ Implement caching strategies",
      "□ Use thread pools for concurrency",
      "□ Monitor memory usage",
      "□ Optimize garbage collection",
      "□ Use parallel processing for CPU-bound tasks",
      "□ Minimize network round trips",
      "□ Use connection pooling",
      "□ Monitor performance metrics",
      "□ Set performance alerts",
      "□ Regular performance testing"
    ]
    
    checklist.each { |item| puts item }
  end
  
  def self.performance_monitoring
    puts "\nPerformance Monitoring:"
    puts "=" * 50
    
    monitoring_example = <<~RUBY
      class PerformanceMonitor
        def self.monitor_method(method_name, &block)
          start_time = Time.now
          start_memory = get_memory_usage
          
          result = block.call
          
          end_time = Time.now
          end_memory = get_memory_usage
          
          duration = end_time - start_time
          memory_delta = end_memory - start_memory
          
          puts "Method: #{method_name}"
          puts "  Duration: #{duration.round(4)}s"
          puts "  Memory delta: #{memory_delta} bytes"
          puts "  Result: #{result}"
          
          result
        end
        
        def self.monitor_loop(iterations, &block)
          puts "Monitoring loop with #{iterations} iterations:"
          
          total_time = 0
          total_memory = 0
          
          iterations.times do |i|
            start_time = Time.now
            start_memory = get_memory_usage
            
            block.call(i)
            
            end_time = Time.now
            end_memory = get_memory_usage
            
            total_time += (end_time - start_time)
            total_memory += (end_memory - start_memory)
          end
          
          avg_time = total_time / iterations
          avg_memory = total_memory / iterations
          
          puts "  Average duration: #{avg_time.round(6)}s"
          puts "  Average memory delta: #{avg_memory} bytes"
          puts "  Total duration: #{total_time.round(4)}s"
          puts "  Total memory delta: #{total_memory} bytes"
        end
        
        private
        
        def self.get_memory_usage
          GC.start
          ObjectSpace.count_objects[:TOTAL]
        end
      end
      
      # Usage examples
      def self.demonstrate_monitoring
        puts "Demonstrating Performance Monitoring:"
        puts "=" * 40
        
        # Monitor single method
        PerformanceMonitor.monitor_method("string_concatenation") do
          str = ""
          1000.times { |i| str += "item_#{i}" }
          str
        end
        
        # Monitor loop
        PerformanceMonitor.monitor_loop(100) do |i|
          "item_#{i}"
        end
      end
    RUBY
    
    puts "Performance Monitoring Example:"
    puts monitoring_example
  end
  
  # Run best practices examples
  performance_guidelines
  performance_antipatterns
  performance_checklist
  performance_monitoring
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Benchmarking**: Use Ruby's Benchmark library
2. **Code Optimization**: Optimize simple Ruby code
3. **Memory Profiling**: Use memory_profiler gem

### Intermediate Exercises

1. **Thread Pool**: Implement thread pool
2. **Object Pooling**: Implement object pooling
3. **Parallel Processing**: Implement parallel algorithms

### Advanced Exercises

1. **Performance Profiling**: Build comprehensive profiling tool
2. **Memory Optimization**: Optimize memory usage
3. **Performance Monitoring**: Build performance monitoring system

---

## 🎯 Summary

Performance practices in Ruby provide:

- **Performance Fundamentals** - Understanding Ruby performance characteristics
- **Performance Profiling** - Using profiling tools and techniques
- **Code Optimization** - Optimizing strings, arrays, and hashes
- **Memory Optimization** - Managing memory efficiently
- **Concurrency Performance** - Optimizing threads and processes
- **Performance Best Practices** - Comprehensive performance guidelines

Master these practices to build high-performance Ruby applications!
