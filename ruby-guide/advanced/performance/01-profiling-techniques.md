# Profiling Techniques in Ruby
# Comprehensive guide to performance profiling and analysis

## 🎯 Overview

Profiling is essential for identifying performance bottlenecks and optimizing Ruby applications. This guide covers various profiling techniques, tools, and best practices for Ruby performance analysis.

## 🔍 Built-in Ruby Profiling Tools

### 1. Ruby Profiler

Ruby's built-in profiler for method-level analysis:

```ruby
# Basic Ruby profiler usage
require 'profile'

class DataProcessor
  def initialize(data)
    @data = data
  end
  
  def process_data
    validate_data
    transform_data
    analyze_data
    generate_report
  end
  
  private
  
  def validate_data
    sleep(0.1)  # Simulate validation work
    @data.each { |item| raise "Invalid data" unless item.is_a?(String) }
  end
  
  def transform_data
    sleep(0.2)  # Simulate transformation work
    @data.map(&:upcase)
  end
  
  def analyze_data
    sleep(0.15)  # Simulate analysis work
    @data.length
  end
  
  def generate_report
    sleep(0.05)  # Simulate report generation
    "Report: #{@data.length} items processed"
  end
end

# Usage with profiler
data = (1..1000).map { |i| "item_#{i}" }
processor = DataProcessor.new(data)

# This will run the profiler automatically
result = processor.process_data
puts "Result: #{result}"
```

### 2. Memory Profiler

Track memory allocation and usage:

```ruby
require 'objspace'

class MemoryProfiler
  def self.profile(label = nil)
    GC.start
    before = ObjectSpace.count_objects
    
    result = yield
    
    GC.start
    after = ObjectSpace.count_objects
    
    delta = after.transform_values { |after_val, before_val| after_val - before_val }
    
    puts "#{label} Memory Profile:" if label
    puts "  Total allocated: #{delta[:TOTAL]}"
    puts "  T_STRING: #{delta[:T_STRING]}"
    puts "  T_ARRAY: #{delta[:T_ARRAY]}"
    puts "  T_HASH: #{delta[:T_HASH]}"
    puts "  T_DATA: #{delta[:T_DATA]}"
    
    result
  end
  
  def self.track_object_creation(&block)
    GC.start
    before = ObjectSpace.count_objects[:TOTAL]
    
    result = yield
    
    GC.start
    after = ObjectSpace.count_objects[:TOTAL]
    
    puts "Objects created: #{after - before}"
    result
  end
end

# Usage examples
class DataAnalyzer
  def analyze_large_dataset
    data = []
    10000.times { |i| data << "Data item #{i}" }
    
    processed = data.map(&:upcase)
    grouped = processed.group_by { |item| item[0] }
    
    grouped.length
  end
end

# Profile memory usage
MemoryProfiler.profile("Data Analysis") do
  analyzer = DataAnalyzer.new
  analyzer.analyze_large_dataset
end

# Track object creation
MemoryProfiler.track_object_creation do
  1000.times { |i| "String #{i}" }
end
```

### 3. Benchmark Library

Compare performance of different approaches:

```ruby
require 'benchmark'

class PerformanceComparison
  def self.compare_string_concatenation
    str = ""
    1000.times { |i| str += "item_#{i}" }
    str
  end
  
  def self.array_join_concatenation
    items = 1000.times.map { |i| "item_#{i}" }
    items.join
  end
  
  def self.stringio_concatenation
    io = StringIO.new
    1000.times { |i| io.write("item_#{i}") }
    io.string
  end
  
  def self.run_comparison
    puts "String Concatenation Comparison:"
    puts "=" * 40
    
    Benchmark.bm(20) do |x|
      x.report("String + operator") do
        100.times { compare_string_concatenation }
      end
      
      x.report("Array.join") do
        100.times { array_join_concatenation }
      end
      
      x.report("StringIO") do
        100.times { stringio_concatenation }
      end
    end
  end
  
  def self.compare_sorting_algorithms
    data = (1..10000).to_a.shuffle
    
    Benchmark.bm(15) do |x|
      x.report("Array.sort") do
        10.times { data.sort }
      end
      
      x.report("Array.sort_by") do
        10.times { data.sort_by { |n| n } }
      end
      
      x.report("Quick sort") do
        10.times { quick_sort(data.dup) }
      end
    end
  end
  
  private
  
  def self.quick_sort(array)
    return array if array.length <= 1
    
    pivot = array[array.length / 2]
    left = array.select { |x| x < pivot }
    middle = array.select { |x| x == pivot }
    right = array.select { |x| x > pivot }
    
    quick_sort(left) + middle + quick_sort(right)
  end
end

# Run comparisons
PerformanceComparison.run_comparison
PerformanceComparison.compare_sorting_algorithms
```

## 📊 External Profiling Tools

### 1. Memory Profiler Gem

Detailed memory analysis:

```ruby
require 'memory_profiler'

class MemoryIntensiveOperation
  def self.process_large_data
    # Create large data structures
    users = []
    1000.times do |i|
      users << {
        id: i,
        name: "User #{i}",
        email: "user#{i}@example.com",
        profile: {
          age: rand(18..65),
          preferences: Array.new(10) { |j| "Preference_#{j}" },
          metadata: Hash.new { |h, k| h[k] = "value_#{k}" }
        }
      }
    end
    
    # Process data
    processed = users.map do |user|
      {
        id: user[:id],
        full_name: "#{user[:name]} (#{user[:email]})",
        age_group: user[:profile][:age] < 30 ? 'young' : 'senior'
      }
    end
    
    # Group results
    processed.group_by { |user| user[:age_group] }
  end
  
  def self.memory_intensive_operation
    # Create many objects
    data = []
    10000.times do |i|
      data << {
        id: i,
        values: Array.new(100) { |j| j * i },
        metadata: {
          created_at: Time.now,
          tags: Array.new(20) { |k| "tag_#{k}" }
        }
      }
    end
    
    # Process with transformations
    data.map do |item|
      {
        id: item[:id],
        sum: item[:values].sum,
        tag_count: item[:metadata][:tags].length
      }
    end
  end
end

# Memory profiling
puts "Memory Profile for Large Data Processing:"
report = MemoryProfiler.report do
  MemoryIntensiveOperation.process_large_data
end

puts "Total allocated: #{report.total_allocated_memsize}"
puts "Total retained: #{report.total_retained_memsize}"
puts "Objects allocated: #{report.total_allocated}"
puts "Objects freed: #{report.total_freed}"

puts "\nMemory Profile for Intensive Operation:"
report2 = MemoryProfiler.report do
  MemoryIntensiveOperation.memory_intensive_operation
end

puts "Total allocated: #{report2.total_allocated_memsize}"
puts "Total retained: #{report2.total_retained_memsize}"
```

### 2. StackProf Gem

Stack-based profiling for CPU analysis:

```ruby
require 'stackprof'

class StackProfilerExample
  def self.cpu_intensive_operation
    result = []
    1000.times do |i|
      result << fibonacci(30)
    end
    result
  end
  
  def self.io_intensive_operation
    files = []
    100.times do |i|
      content = generate_large_content(i)
      files << content
    end
    files
  end
  
  def self.mixed_operation
    data = []
    1000.times do |i|
      # CPU intensive
      processed = fibonacci(20)
      
      # IO intensive
      content = generate_large_content(i)
      
      data << { id: i, result: processed, content: content }
    end
    data
  end
  
  def self.fibonacci(n)
    return n if n <= 1
    fibonacci(n - 1) + fibonacci(n - 2)
  end
  
  def self.generate_large_content(id)
    content = "Content for item #{id}\n"
    1000.times { |i| content += "Line #{i}: Data for item #{id}\n" }
    content
  end
  
  def self.profile_cpu_intensive
    StackProf.run(mode: :cpu, out: 'cpu_profile.txt') do
      cpu_intensive_operation
    end
    puts "CPU profile saved to cpu_profile.txt"
  end
  
  def self.profile_io_intensive
    StackProf.run(mode: :wall, out: 'wall_profile.txt') do
      io_intensive_operation
    end
    puts "Wall clock profile saved to wall_profile.txt"
  end
  
  def self.profile_mixed
    StackProf.run(mode: :cpu, out: 'mixed_profile.txt') do
      mixed_operation
    end
    puts "Mixed operation profile saved to mixed_profile.txt"
  end
end

# Run stack profiling
StackProfilerExample.profile_cpu_intensive
StackProfilerExample.profile_io_intensive
StackProfilerExample.profile_mixed
```

### 3. Ruby-Prof Gem

Comprehensive profiling with multiple modes:

```ruby
require 'ruby-prof'
require 'ruby-prof/graph_printer'
require 'ruby-prof/call_stack_printer'

class RubyProfExample
  def self.complex_operation
    data = (1..1000).to_a
    
    # Multiple operations
    filtered = filter_data(data)
    transformed = transform_data(filtered)
    aggregated = aggregate_data(transformed)
    
    aggregated
  end
  
  def self.filter_data(data)
    data.select { |x| x.even? }.map { |x| x * 2 }
  end
  
  def self.transform_data(data)
    data.map { |x| x ** 2 }.sort
  end
  
  def self.aggregate_data(data)
    data.group_by { |x| x % 10 }
  end
  
  def self.profile_with_flat_printer
    RubyProf.start
    
    complex_operation
    
    result = RubyProf.stop
    
    printer = RubyProf::FlatPrinter.new(result)
    printer.print(STDOUT)
  end
  
  def self.profile_with_graph_printer
    RubyProf.start
    
    complex_operation
    
    result = RubyProf.stop
    
    printer = RubyProf::GraphPrinter.new(result)
    printer.print(STDOUT)
  end
  
  def self.profile_with_call_stack_printer
    RubyProf.start
    
    complex_operation
    
    result = RubyProf.stop
    
    printer = RubyProf::CallStackPrinter.new(result)
    printer.print(STDOUT)
  end
  
  def self.profile_and_save
    RubyProf.start
    
    complex_operation
    
    result = RubyProf.stop
    
    # Save to different formats
    File.open('flat_profile.txt', 'w') do |file|
      printer = RubyProf::FlatPrinter.new(result)
      printer.print(file)
    end
    
    File.open('graph_profile.txt', 'w') do |file|
      printer = RubyProf::GraphPrinter.new(result)
      printer.print(file)
    end
    
    File.open('call_stack_profile.txt', 'w') do |file|
      printer = RubyProf::CallStackPrinter.new(result)
      printer.print(file)
    end
    
    puts "Profiles saved to files"
  end
end

# Run RubyProf profiling
puts "Flat Profile:"
RubyProfExample.profile_with_flat_printer

puts "\nGraph Profile:"
RubyProfExample.profile_with_graph_printer

puts "\nCall Stack Profile:"
RubyProfExample.profile_with_call_stack_printer

# Save all profiles
RubyProfExample.profile_and_save
```

## 🔧 Custom Profiling Tools

### 1. Method Timer

Custom timing for specific methods:

```ruby
class MethodTimer
  def self.time_method(klass, method_name)
    original_method = klass.instance_method(method_name)
    
    klass.define_method(method_name) do |*args, &block|
      start_time = Time.now
      result = original_method.bind(self).call(*args, &block)
      end_time = Time.now
      
      duration = (end_time - start_time) * 1000
      puts "#{klass.name}##{method_name} took #{duration.round(2)}ms"
      
      result
    end
  end
  
  def self.time_class_methods(klass, method_names)
    method_names.each { |name| time_method(klass, name) }
  end
  
  def self.time_all_methods(klass)
    instance_methods = klass.instance_methods(false)
    time_class_methods(klass, instance_methods)
  end
end

class DataProcessor
  def initialize(data)
    @data = data
  end
  
  def process
    validate
    transform
    analyze
  end
  
  def validate
    sleep(0.1)
    @data.each { |item| raise "Invalid" unless item.is_a?(String) }
    true
  end
  
  def transform
    sleep(0.2)
    @data.map(&:upcase)
  end
  
  def analyze
    sleep(0.15)
    @data.length
  end
end

# Usage
data = (1..100).map { |i| "item_#{i}" }
processor = DataProcessor.new(data)

# Time all methods
MethodTimer.time_all_methods(DataProcessor)

# Run the methods to see timing
processor.process
```

### 2. Performance Monitor

Continuous performance monitoring:

```ruby
class PerformanceMonitor
  def initialize
    @measurements = {}
    @mutex = Mutex.new
  end
  
  def time(operation_name, &block)
    @mutex.synchronize do
      @measurements[operation_name] ||= []
    end
    
    start_time = Time.now
    result = yield
    end_time = Time.now
    
    duration = (end_time - start_time) * 1000
    
    @mutex.synchronize do
      @measurements[operation_name] << {
        duration: duration,
        timestamp: start_time
      }
    end
    
    result
  end
  
  def measure_memory(operation_name, &block)
    GC.start
    before_objects = ObjectSpace.count_objects[:TOTAL]
    
    result = yield
    
    GC.start
    after_objects = ObjectSpace.count_objects[:TOTAL]
    
    @mutex.synchronize do
      @measurements[operation_name] ||= []
      @measurements[operation_name] << {
        memory_allocated: after_objects - before_objects,
        timestamp: Time.now
      }
    end
    
    result
  end
  
  def get_stats(operation_name)
    @mutex.synchronize do
      measurements = @measurements[operation_name] || []
      return nil if measurements.empty?
      
      if measurements.first[:duration]
        # Time-based measurements
        durations = measurements.map { |m| m[:duration] }
        {
          count: durations.length,
          min: durations.min,
          max: durations.max,
          avg: durations.sum / durations.length,
          total: durations.sum
        }
      else
        # Memory-based measurements
        memory_allocations = measurements.map { |m| m[:memory_allocated] }
        {
          count: memory_allocations.length,
          min: memory_allocations.min,
          max: memory_allocations.max,
          avg: memory_allocations.sum / memory_allocations.length,
          total: memory_allocations.sum
        }
      end
    end
  end
  
  def get_all_stats
    @mutex.synchronize do
      @measurements.keys.map do |operation_name|
        [operation_name, get_stats(operation_name)]
      end.to_h
    end
  end
  
  def reset
    @mutex.synchronize do
      @measurements.clear
    end
  end
  
  def print_report
    stats = get_all_stats
    
    puts "Performance Report"
    puts "=" * 50
    
    stats.each do |operation, stat|
      puts "\n#{operation}:"
      puts "  Count: #{stat[:count]}"
      puts "  Average: #{stat[:avg].round(2)}"
      puts "  Min: #{stat[:min].round(2)}"
      puts "  Max: #{stat[:max].round(2)}"
      puts "  Total: #{stat[:total].round(2)}"
    end
  end
end

# Usage
monitor = PerformanceMonitor.new

class Application
  def initialize
    @monitor = monitor
  end
  
  def process_data(data)
    @monitor.time('validate_data') do
      validate_data(data)
    end
    
    @monitor.time('transform_data') do
      transform_data(data)
    end
    
    @monitor.time('analyze_data') do
      analyze_data(data)
    end
  end
  
  def process_with_memory_tracking(data)
    @monitor.measure_memory('process_data') do
      process_data(data)
    end
  end
  
  private
  
  def validate_data(data)
    data.each { |item| raise "Invalid" unless item.is_a?(String) }
  end
  
  def transform_data(data)
    data.map(&:upcase)
  end
  
  def analyze_data(data)
    data.length
  end
end

# Run performance monitoring
app = Application.new(monitor)

# Process data multiple times
10.times do |i|
  data = (1..100).map { |j| "item_#{i}_#{j}" }
  app.process_data(data)
end

# Process with memory tracking
data = (1..1000).map { |i| "large_item_#{i}" }
app.process_with_memory_tracking(data)

# Print report
monitor.print_report
```

### 3. Hotspot Detector

Identify performance hotspots:

```ruby
class HotspotDetector
  def initialize
    @call_counts = Hash.new(0)
    @call_times = Hash.new(0)
    @mutex = Mutex.new
  end
  
  def profile(method_name, &block)
    @mutex.synchronize do
      @call_counts[method_name] += 1
    end
    
    start_time = Time.now
    result = yield
    end_time = Time.now
    
    duration = (end_time - start_time) * 1000
    
    @mutex.synchronize do
      @call_times[method_name] += duration
    end
    
    result
  end
  
  def get_hotspots(limit = 10)
    @mutex.synchronize do
      # Calculate average call time
      avg_times = {}
      @call_counts.each do |method, count|
        avg_times[method] = @call_times[method] / count
      end
      
      # Sort by total time spent
      total_times = @call_times.sort_by { |_, time| -time }
      
      total_times.first(limit).map do |method, total_time|
        {
          method: method,
          total_time: total_time,
          call_count: @call_counts[method],
          avg_time: avg_times[method]
        }
      end
    end
  end
  
  def print_hotspots(limit = 10)
    spots = get_hotspots(limit)
    
    puts "Performance Hotspots (Top #{limit})"
    puts "=" * 50
    
    spots.each_with_index do |spot, index|
      puts "#{index + 1}. #{spot[:method]}"
      puts "   Total time: #{spot[:total_time].round(2)}ms"
      puts "   Call count: #{spot[:call_count]}"
      puts "   Avg time: #{spot[:avg_time].round(2)}ms"
      puts
    end
  end
  
  def reset
    @mutex.synchronize do
      @call_counts.clear
      @call_times.clear
    end
  end
end

# Usage
detector = HotspotDetector.new

class DataProcessor
  def initialize(detector)
    @detector = detector
  end
  
  def process_data(data)
    @detector.profile('load_data') do
      load_data(data)
    end
    
    @detector.profile('validate_data') do
      validate_data(data)
    end
    
    @detector.profile('transform_data') do
      transform_data(data)
    end
    
    @detector.profile('save_data') do
      save_data(data)
    end
  end
  
  private
  
  def load_data(data)
    sleep(0.1)  # Simulate loading
    data
  end
  
  def validate_data(data)
    sleep(0.05)  # Simulate validation
    data.each { |item| raise "Invalid" unless item.is_a?(String) }
  end
  
  def transform_data(data)
    sleep(0.2)  # Simulate transformation
    data.map(&:upcase)
  end
  
  def save_data(data)
    sleep(0.15)  # Simulate saving
    "Saved #{data.length} items"
  end
end

# Run hotspot detection
processor = DataProcessor.new(detector)

# Process data multiple times
10.times do |i|
  data = (1..100).map { |j| "item_#{i}_#{j}" }
  processor.process_data(data)
end

# Print hotspots
detector.print_hotspots
```

## 🎯 Profiling Best Practices

### 1. Profiling Guidelines

```ruby
class ProfilingGuidelines
  def self.profile_in_production?
    # Generally false, but can be done with sampling profilers
    ENV['ENABLE_PROFILING'] == 'true'
  end
  
  def self.should_profile?(operation)
    # Profile based on conditions
    return false unless profile_in_production?
    
    # Sample 1% of requests
    rand < 0.01
  end
  
  def self.profile_with_sampling(operation_name, sample_rate = 0.01, &block)
    if rand < sample_rate
      start_profiling(operation_name, &block)
    else
      yield
    end
  end
  
  def self.start_profiling(operation_name, &block)
    # Choose appropriate profiler based on environment
    if ENV['RACK_ENV'] == 'production'
      # Use lightweight profiling in production
      LightweightProfiler.profile(operation_name, &block)
    else
      # Use comprehensive profiling in development
      ComprehensiveProfiler.profile(operation_name, &block)
    end
  end
end

# Lightweight profiler for production
class LightweightProfiler
  def self.profile(operation_name, &block)
    start_time = Time.now
    
    result = yield
    
    end_time = Time.now
    duration = (end_time - start_time) * 1000
    
    # Log only slow operations
    if duration > 100  # Log if slower than 100ms
      puts "[PROFILING] #{operation_name}: #{duration.round(2)}ms"
    end
    
    result
  end
end

# Comprehensive profiler for development
class ComprehensiveProfiler
  def self.profile(operation_name, &block)
    # Use detailed profiling
    require 'benchmark'
    
    result = nil
    time = Benchmark.measure do
      result = yield
    end
    
    puts "[PROFILING] #{operation_name}: #{time.real.round(2)}s real, #{time.utime.round(2)}s user, #{time.stime.round(2)}s system"
    
    result
  end
end
```

### 2. Profiling Middleware

Add profiling to web applications:

```ruby
class ProfilingMiddleware
  def initialize(app, profiler = nil)
    @app = app
    @profiler = profiler || PerformanceMonitor.new
  end
  
  def call(env)
    request_path = env['REQUEST_PATH']
    method = env['REQUEST_METHOD']
    operation_name = "#{method} #{request_path}"
    
    @profiler.time(operation_name) do
      @app.call(env)
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Profiling**: Use Ruby's built-in profiler
2. **Memory Profiling**: Track memory allocation
3. **Benchmarking**: Compare different approaches

### Intermediate Exercises

1. **StackProf**: Use StackProf for CPU profiling
2. **Ruby-Prof**: Implement comprehensive profiling
3. **Custom Timer**: Build method timing tool

### Advanced Exercises

1. **Hotspot Detection**: Create hotspot detector
2. **Performance Monitor**: Build continuous monitoring
3. **Production Profiling**: Implement production-safe profiling

---

## 🎯 Summary

Profiling techniques in Ruby provide:

- **Built-in Tools** - Ruby profiler and ObjectSpace
- **External Gems** - MemoryProfiler, StackProf, Ruby-Prof
- **Custom Tools** - Method timers and performance monitors
- **Hotspot Detection** - Identify performance bottlenecks
- **Production Profiling** - Safe profiling in production

Master these techniques to optimize Ruby application performance!
