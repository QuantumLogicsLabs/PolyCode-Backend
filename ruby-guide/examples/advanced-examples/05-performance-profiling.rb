# Performance Profiling Examples in Ruby
# Demonstrating performance analysis and optimization techniques

require 'benchmark'
require 'memory_profiler'
require 'ruby-prof'
require 'objspace'

class PerformanceProfilingExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "📊 Performance Profiling Examples in Ruby"
    puts "========================================"
    puts "Explore performance analysis and optimization!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Performance Profiling Menu:"
      puts "1. Benchmarking"
      puts "2. Memory Profiling"
      puts "3. CPU Profiling"
      puts "4. Object Allocation Tracking"
      puts "5. Performance Optimization"
      puts "6. Hotspot Analysis"
      puts "7. Performance Monitoring"
      puts "8. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-8): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        benchmarking
      when 2
        memory_profiling
      when 3
        cpu_profiling
      when 4
        object_allocation_tracking
      when 5
        performance_optimization
      when 6
        hotspot_analysis
      when 7
        performance_monitoring
      when 8
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def benchmarking
    puts "\n⏱️ Example 1: Benchmarking"
    puts "=" * 50
    puts "Measuring and comparing code performance."
    puts ""
    
    # Basic benchmarking
    puts "⏱️ Basic Benchmarking:"
    
    def array_creation_methods
      n = 100000
      
      Benchmark.bm(20) do |x|
        x.report("Array.new with block:") do
          Array.new(n) { |i| i * 2 }
        end
        
        x.report("Range to_a:") do
          (0...n).map { |i| i * 2 }
        end
        
        x.report("Times loop:") do
          result = []
          n.times { |i| result << i * 2 }
          result
        end
        
        x.report("Each with object:") do
          result = []
          (0...n).each { |i| result << i * 2 }
          result
        end
      end
    end
    
    def string_concatenation_methods
      n = 10000
      
      Benchmark.bm(20) do |x|
        x.report("String + operator:") do
          result = ""
          n.times { |i| result += "item#{i}" }
          result
        end
        
        x.report("Array join:") do
          items = n.times.map { |i| "item#{i}" }
          items.join
        end
        
        x.report("StringIO:") do
          io = StringIO.new
          n.times { |i| io.write("item#{i}") }
          io.string
        end
        
        x.report("String concat:") do
          result = ""
          n.times { |i| result.concat("item#{i}") }
          result
        end
      end
    end
    
    def hash_access_methods
      hash = {}
      10000.times { |i| hash["key#{i}"] = "value#{i}" }
      
      Benchmark.bm(20) do |x|
        x.report("Hash key access:") do
          10000.times { |i| hash["key#{i}"] }
        end
        
        x.report("Hash fetch:") do
          10000.times { |i| hash.fetch("key#{i}") }
        end
        
        x.report("Hash dig:") do
          10000.times { |i| hash.dig("key#{i}") }
        end
        
        x.report("Hash values_at:") do
          10000.times { |i| hash.values_at("key#{i}") }
        end
      end
    end
    
    # Custom benchmark class
    puts "\n📊 Custom Benchmark Class:"
    
    class CustomBenchmark
      def self.measure(label, iterations = 1000, &block)
        GC.start
        start_time = Time.now
        start_memory = GC.stat[:total_allocated_objects]
        
        iterations.times(&block)
        
        end_time = Time.now
        end_memory = GC.stat[:total_allocated_objects]
        
        duration = end_time - start_time
        allocated_objects = end_memory - start_memory
        
        puts "#{label}:"
        puts "  Time: #{duration.round(4)}s"
        puts "  Objects: #{allocated_objects}"
        puts "  Avg per iteration: #{(duration / iterations * 1000).round(4)}ms"
        puts
      end
      
      def self.compare(methods, iterations = 1000)
        results = {}
        
        methods.each do |name, block|
          GC.start
          start_time = Time.now
          
          iterations.times(&block)
          
          end_time = Time.now
          duration = end_time - start_time
          results[name] = duration
        end
        
        puts "Performance Comparison (#{iterations} iterations):"
        sorted_results = results.sort_by { |_, time| time }
        
        sorted_results.each_with_index do |(name, time), index|
          percentage = (time / sorted_results.first[1] * 100).round(1)
          puts "  #{index + 1}. #{name}: #{time.round(4)}s (#{percentage}% of fastest)"
        end
      end
    end
    
    # Run benchmark examples
    puts "\nRunning Benchmark Examples:"
    
    array_creation_methods
    string_concatenation_methods
    hash_access_methods
    
    # Custom benchmark examples
    puts "\nCustom Benchmark Examples:"
    
    CustomBenchmark.measure("Fibonacci recursive", 100) do
      def fib(n)
        return n if n <= 1
        fib(n - 1) + fib(n - 2)
      end
      fib(20)
    end
    
    CustomBenchmark.measure("Fibonacci iterative", 100) do
      def fib_iter(n)
        return n if n <= 1
        a, b = 0, 1
        n.times { a, b = b, a + b }
        a
      end
      fib_iter(20)
    end
    
    # Performance comparison
    puts "\nPerformance Comparison:"
    
    CustomBenchmark.compare({
      "Array#map" => -> { (1..1000).map { |i| i * 2 } },
      "Array#collect" => -> { (1..1000).collect { |i| i * 2 } },
      "Each with push" => -> { result = []; (1..1000).each { |i| result << i * 2 }; result }
    }, 1000)
    
    @examples << {
      title: "Benchmarking",
      description: "Performance measurement and comparison techniques",
      code: <<~RUBY
        require 'benchmark'
        
        Benchmark.bm do |x|
          x.report("Method 1") { method1_call }
          x.report("Method 2") { method2_call }
        end
      RUBY
    }
    
    puts "\n✅ Benchmarking example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def memory_profiling
    puts "\n🧠 Example 2: Memory Profiling"
    puts "=" * 50
    puts "Analyzing memory usage and allocations."
    puts ""
    
    # Memory profiling implementation
    puts "🧠 Memory Profiling Implementation:"
    
    class MemoryProfiler
      def self.profile(label = nil, &block)
        GC.start
        start_stats = GC.stat
        start_objects = ObjectSpace.count_objects
        
        result = yield
        
        GC.start
        end_stats = GC.stat
        end_objects = ObjectSpace.count_objects
        
        allocated_objects = end_objects - start_objects
        allocated_bytes = end_stats[:total_allocated_bytes] - start_stats[:total_allocated_bytes]
        
        puts "#{label} Memory Profile:" if label
        puts "  Allocated objects: #{allocated_objects}"
        puts "  Allocated bytes: #{allocated_bytes}"
        puts "  GC collections: #{end_stats[:count] - start_stats[:count]}"
        
        # Show top allocated object types
        object_types = allocated_objects.select { |type, count| count > 0 }
        sorted_types = object_types.sort_by { |_, count| -count }.first(5)
        
        puts "  Top object types:"
        sorted_types.each do |type, count|
          puts "    #{type}: #{count}"
        end
        
        result
      end
      
      def self.track_object_creation(&block)
        objects_before = ObjectSpace.each_object.count
        yield
        objects_after = ObjectSpace.each_object.count
        objects_after - objects_before
      end
      
      def self.find_memory_leaks(&block)
        GC.start
        objects_before = ObjectSpace.count_objects
        
        yield
        
        GC.start
        objects_after = ObjectSpace.count_objects
        leaked_objects = objects_after - objects_before
        
        if leaked_objects > 0
          puts "Potential memory leak detected:"
          leaked_objects.each do |type, count|
            if count > 0
              puts "  #{type}: #{count} objects"
            end
          end
        else
          puts "No memory leaks detected"
        end
      end
    end
    
    # Memory usage examples
    puts "\nMemory Usage Examples:"
    
    # String allocation
    MemoryProfiler.profile("String allocation") do
      strings = 10000.times.map { |i| "string_#{i}" }
      strings.map(&:upcase)
    end
    
    # Array allocation
    MemoryProfiler.profile("Array allocation") do
      arrays = 1000.times.map { |i| (1..100).to_a }
      arrays.map(&:sum)
    end
    
    # Hash allocation
    MemoryProfiler.profile("Hash allocation") do
      hashes = 1000.times.map { |i| { key: i, value: i * 2 } }
      hashes.map { |h| h[:value] }
    end
    
    # Object tracking
    puts "\nObject Creation Tracking:"
    
    objects_created = MemoryProfiler.track_object_creation do
      users = 1000.times.map { |i| OpenStruct.new(name: "User#{i}", age: rand(18..65)) }
      users.select { |u| u.age >= 18 }
    end
    
    puts "Objects created: #{objects_created}"
    
    # Memory leak detection
    puts "\nMemory Leak Detection:"
    
    # Example without leak
    MemoryProfiler.find_memory_leaks do
      data = (1..1000).to_a
      processed = data.map { |x| x * 2 }
      sum = processed.sum
      # data and processed will be garbage collected
    end
    
    # Example with potential leak
    MemoryProfiler.find_memory_leaks do
      $global_cache ||= []
      1000.times { |i| $global_cache << "item_#{i}" }
      # $global_cache persists, causing leak
    end
    
    # Clean up
    $global_cache = nil if defined?($global_cache)
    
    # Memory efficiency comparison
    puts "\nMemory Efficiency Comparison:"
    
    def memory_efficient_string_processing
      # Inefficient approach
      MemoryProfiler.profile("Inefficient strings") do
        result = ""
        10000.times { |i| result += "item#{i}" }
      end
      
      # Efficient approach
      MemoryProfiler.profile("Efficient strings") do
        items = 10000.times.map { |i| "item#{i}" }
        items.join
      end
    end
    
    memory_efficient_string_processing
    
    @examples << {
      title: "Memory Profiling",
      description: "Memory usage analysis and leak detection",
      code: <<~RUBY
        class MemoryProfiler
          def self.profile(label = nil, &block)
            GC.start
            start_stats = GC.stat
            yield
            GC.start
            end_stats = GC.stat
            # Analyze memory differences
          end
        end
      RUBY
    }
    
    puts "\n✅ Memory Profiling example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def cpu_profiling
    puts "\n🔥 Example 3: CPU Profiling"
    puts "=" * 50
    puts "Analyzing CPU usage and method call patterns."
    puts ""
    
    # CPU profiling implementation
    puts "🔥 CPU Profiling Implementation:"
    
    class CPUProfiler
      def self.profile(method_name = nil, &block)
        RubyProf.start
        
        result = yield
        
        profiler_result = RubyProf.stop
        
        if method_name
          puts "#{method_name} CPU Profile:"
        else
          puts "CPU Profile:"
        end
        
        # Print different profiling views
        print_flat_profile(profiler_result)
        print_graph_profile(profiler_result)
        print_call_stack_profile(profiler_result)
        
        result
      end
      
      def self.print_flat_profile(result)
        puts "\nFlat Profile:"
        puts "-" * 50
        
        printer = RubyProf::FlatPrinter.new(result)
        output = StringIO.new
        printer.print(output)
        lines = output.string.split("\n")
        
        # Show top 10 methods
        lines[2..12].each { |line| puts "  #{line}" }
      end
      
      def self.print_graph_profile(result)
        puts "\nGraph Profile:"
        puts "-" * 50
        
        printer = RubyProf::GraphPrinter.new(result)
        output = StringIO.new
        printer.print(output)
        lines = output.string.split("\n")
        
        # Show top 10 methods
        lines[2..12].each { |line| puts "  #{line}" }
      end
      
      def self.print_call_stack_profile(result)
        puts "\nCall Stack Profile:"
        puts "-" * 50
        
        printer = RubyProf::CallStackPrinter.new(result)
        output = StringIO.new
        printer.print(output)
        lines = output.string.split("\n")
        
        # Show top 10 methods
        lines[2..12].each { |line| puts "  #{line}" }
      end
      
      def self.method_timing(iterations = 1000, &block)
        times = []
        
        iterations.times do
          start_time = Time.now
          yield
          end_time = Time.now
          times << (end_time - start_time)
        end
        
        avg_time = times.sum / times.length
        min_time = times.min
        max_time = times.max
        
        {
          average: avg_time,
          min: min_time,
          max: max_time,
          std_dev: calculate_std_dev(times, avg_time)
        }
      end
      
      private
      
      def self.calculate_std_dev(times, mean)
        variance = times.sum { |time| (time - mean) ** 2 } / times.length
        Math.sqrt(variance)
      end
    end
    
    # CPU profiling examples
    puts "\nCPU Profiling Examples:"
    
    # Profile a complex operation
    CPUProfiler.profile("Complex data processing") do
      data = (1..10000).to_a
      
      # Multiple operations
      filtered = data.select(&:even?)
      mapped = filtered.map { |x| x ** 2 }
      grouped = mapped.group_by { |x| x % 10 }
      summed = grouped.transform_values(&:sum)
      
      # Sorting
      sorted = summed.sort_by { |_, value| -value }
      
      # Final processing
      result = sorted.first(10)
    end
    
    # Method timing
    puts "\nMethod Timing Analysis:"
    
    def fibonacci_recursive(n)
      return n if n <= 1
      fibonacci_recursive(n - 1) + fibonacci_recursive(n - 2)
    end
    
    def fibonacci_iterative(n)
      return n if n <= 1
      a, b = 0, 1
      n.times { a, b = b, a + b }
      a
    end
    
    # Time both methods
    recursive_timing = CPUProfiler.method_timing(100) { fibonacci_recursive(20) }
    iterative_timing = CPUProfiler.method_timing(100) { fibonacci_iterative(20) }
    
    puts "Fibonacci Recursive (n=20, 100 iterations):"
    puts "  Average: #{recursive_timing[:average].round(6)}s"
    puts "  Min: #{recursive_timing[:min].round(6)}s"
    puts "  Max: #{recursive_timing[:max].round(6)}s"
    puts "  Std Dev: #{recursive_timing[:std_dev].round(6)}s"
    
    puts "\nFibonacci Iterative (n=20, 100 iterations):"
    puts "  Average: #{iterative_timing[:average].round(6)}s"
    puts "  Min: #{iterative_timing[:min].round(6)}s"
    puts "  Max: #{iterative_timing[:max].round(6)}s"
    puts "  Std Dev: #{iterative_timing[:std_dev].round(6)}s"
    
    speedup = recursive_timing[:average] / iterative_timing[:average]
    puts "\nSpeedup: #{speedup.round(2)}x faster"
    
    # Profile different sorting algorithms
    puts "\nSorting Algorithm Comparison:"
    
    def bubble_sort(array)
      n = array.length
      (0...n).each do |i|
        (0...n - i - 1).each do |j|
          array[j], array[j + 1] = array[j + 1], array[j] if array[j] > array[j + 1]
        end
      end
      array
    end
    
    def quick_sort(array)
      return array if array.length <= 1
      pivot = array[array.length / 2]
      left = array.select { |x| x < pivot }
      middle = array.select { |x| x == pivot }
      right = array.select { |x| x > pivot }
      quick_sort(left) + middle + quick_sort(right)
    end
    
    # Test sorting
    test_data = (1..1000).to_a.shuffle
    
    bubble_timing = CPUProfiler.method_timing(10) { bubble_sort(test_data.dup) }
    quick_timing = CPUProfiler.method_timing(10) { quick_sort(test_data.dup) }
    
    puts "Bubble Sort (1000 elements, 10 iterations):"
    puts "  Average: #{bubble_timing[:average].round(4)}s"
    
    puts "\nQuick Sort (1000 elements, 10 iterations):"
    puts "  Average: #{quick_timing[:average].round(4)}s"
    
    sorting_speedup = bubble_timing[:average] / quick_timing[:average]
    puts "\nSorting Speedup: #{sorting_speedup.round(2)}x faster"
    
    @examples << {
      title: "CPU Profiling",
      description: "CPU usage analysis and method timing",
      code: <<~RUBY
        require 'ruby-prof'
        
        class CPUProfiler
          def self.profile(&block)
            RubyProf.start
            yield
            result = RubyProf.stop
            printer = RubyProf::FlatPrinter.new(result)
            printer.print(STDOUT)
          end
        end
      RUBY
    }
    
    puts "\n✅ CPU Profiling example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Performance Profiling Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate performance profiling techniques!"
  end
end

if __FILE__ == $0
  examples = PerformanceProfilingExamples.new
  examples.start_examples
end
