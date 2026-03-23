# Performance Optimization in Ruby

## Overview

This guide covers performance optimization techniques in Ruby, including profiling, memory management, caching strategies, code optimization, and benchmarking.

## Profiling and Measurement

### Built-in Profiling

```ruby
require 'benchmark'

# Basic benchmarking
def slow_method
  sleep(0.1)
  "result"
end

def fast_method
  "result"
end

puts "Basic Benchmarking:"
puts Benchmark.measure { 1000.times { slow_method } }
puts Benchmark.measure { 1000.times { fast_method } }

# Detailed benchmarking
puts "\nDetailed Benchmarking:"
Benchmark.bm(20) do |x|
  x.report("Slow Method:") { 1000.times { slow_method } }
  x.report("Fast Method:") { 1000.times { fast_method } }
  x.report("String Concatenation:") { 1000.times { "a" + "b" } }
  x.report("String Interpolation:") { 1000.times { "#{a}#{b}" } }
end

# Memory profiling
def memory_intensive_operation
  large_array = []
  100_000.times { |i| large_array << "Item #{i}" }
  large_array.length
end

puts "\nMemory Usage:"
puts "Before: #{`ps -o rss= -p #{Process.pid}`.strip} KB"
result = memory_intensive_operation
puts "After: #{`ps -o rss= -p #{Process.pid}`.strip} KB"
puts "Array length: #{result}"
```

### Custom Profiler

```ruby
class MethodProfiler
  def initialize
    @calls = Hash.new(0)
    @times = Hash.new(0)
    @enabled = false
  end
  
  def enable
    @enabled = true
  end
  
  def disable
    @enabled = false
  end
  
  def profile(method_name)
    return yield unless @enabled
    
    start_time = Time.now
    @calls[method_name] += 1
    
    result = yield
    
    @times[method_name] += Time.now - start_time
    result
  end
  
  def report
    puts "Method Performance Report:"
    puts "=" * 50
    
    @calls.each do |method, count|
      total_time = @times[method]
      avg_time = total_time / count
      
      puts "#{method}:"
      puts "  Calls: #{count}"
      puts "  Total time: #{total_time.round(4)}s"
      puts "  Average time: #{avg_time.round(6)}s"
      puts
    end
  end
  
  def reset
    @calls.clear
    @times.clear
  end
end

# Usage
profiler = MethodProfiler.new
profiler.enable

class Calculator
  def initialize
    @profiler = MethodProfiler.new
  end
  
  def add(a, b)
    @profiler.profile(:add) do
      sleep(0.001)  # Simulate work
      a + b
    end
  end
  
  def multiply(a, b)
    @profiler.profile(:multiply) do
      sleep(0.002)  # Simulate work
      a * b
    end
  end
  
  def complex_calculation(x, y, z)
    @profiler.profile(:complex_calculation) do
      result = add(x, y)
      multiply(result, z)
    end
  end
  
  def report
    @profiler.report
  end
end

calc = Calculator.new

# Perform calculations
100.times { calc.add(rand(100), rand(100)) }
50.times { calc.multiply(rand(100), rand(100)) }
25.times { calc.complex_calculation(rand(100), rand(100), rand(100)) }

calc.report
```

### Memory Profiler

```ruby
class MemoryProfiler
  def self.profile(&block)
    GC.start  # Clean up before profiling
    
    before_memory = get_memory_usage
    before_objects = get_object_count
    
    result = block.call
    
    GC.start  # Clean up after profiling
    after_memory = get_memory_usage
    after_objects = get_object_count
    
    {
      result: result,
      memory_delta: after_memory - before_memory,
      objects_delta: after_objects - before_objects,
      before_memory: before_memory,
      after_memory: after_memory,
      before_objects: before_objects,
      after_objects: after_objects
    }
  end
  
  private
  
  def self.get_memory_usage
    `ps -o rss= -p #{Process.pid}`.strip.to_i
  end
  
  def self.get_object_count
    ObjectSpace.count_objects[:TOTAL]
  end
end

# Usage
puts "Memory Profiling Example:"

result = MemoryProfiler.profile do
  # Create many objects
  data = []
  10_000.times do |i|
    data << {
      id: i,
      name: "Item #{i}",
      value: rand(1000),
      created_at: Time.now
    }
  end
  
  data.length
end

puts "Memory delta: #{result[:memory_delta]} KB"
puts "Objects delta: #{result[:objects_delta]}"
puts "Result: #{result[:result]}"
```

## Memory Management

### Object Pool Pattern

```ruby
class ObjectPool
  def initialize(size, &factory)
    @pool = Queue.new
    @factory = factory
    @created = 0
    @max_size = size
    
    # Pre-populate pool
    size.times { create_object }
  end
  
  def with_object
    obj = acquire
    begin
      yield obj
    ensure
      release(obj)
    end
  end
  
  def acquire
    if @pool.empty? && @created < @max_size
      create_object
    end
    
    @pool.pop
  end
  
  def release(obj)
    reset_object(obj)
    @pool.push(obj)
  end
  
  def size
    @pool.size
  end
  
  private
  
  def create_object
    obj = @factory.call
    @created += 1
    @pool.push(obj)
  end
  
  def reset_object(obj)
    # Reset object state if needed
    obj.clear if obj.respond_to?(:clear)
  end
end

# Usage: Database connection pool
class DatabaseConnection
  def initialize(id)
    @id = id
    @connected = true
  end
  
  def query(sql)
    "Result of #{sql} from connection #{@id}"
  end
  
  def clear
    # Reset connection state
  end
end

connection_pool = ObjectPool.new(5) { |i| DatabaseConnection.new(i) }

puts "Connection pool size: #{connection_pool.size}"

# Use connections
3.times do |i|
  connection_pool.with_object do |conn|
    result = conn.query("SELECT * FROM users WHERE id = #{i}")
    puts "Query #{i}: #{result}"
  end
end

puts "Connection pool size: #{connection_pool.size}"
```

### Memory-Efficient Data Structures

```ruby
# Lazy evaluation for large datasets
class LazyArray
  include Enumerable
  
  def initialize(&generator)
    @generator = generator
    @cache = []
    @exhausted = false
  end
  
  def [](index)
    while @cache.length <= index && !@exhausted
      item = @generator.call
      if item.nil?
        @exhausted = true
        break
      end
      @cache << item
    end
    
    @cache[index]
  end
  
  def each(&block)
    index = 0
    
    loop do
      item = self[index]
      break if item.nil?
      
      block.call(item)
      index += 1
    end
  end
  
  def first(n = 1)
    return self[0] if n == 1
    
    result = []
    n.times { |i| result << self[i]; break if result.last.nil? }
    result
  end
  
  def take_while(&block)
    result = []
    index = 0
    
    loop do
      item = self[index]
      break if item.nil?
      
      break unless block.call(item)
      result << item
      index += 1
    end
    
    result
  end
end

# Usage: Process large file without loading everything into memory
class LargeFileProcessor
  def initialize(filename)
    @filename = filename
  end
  
  def lines
    LazyArray.new do
      @file ||= File.open(@filename)
      line = @file.gets
      line&.chomp
    end
  end
  
  def process_lines(&block)
    lines.each(&block)
  end
  
  def close
    @file&.close
  end
end

# Create a large test file
File.open('large_test.txt', 'w') do |file|
  100_000.times { |i| file.puts "Line #{i}: This is test content" }
end

processor = LargeFileProcessor.new('large_test.txt')

# Process lines efficiently
puts "Processing first 10 lines:"
processor.lines.first(10).each_with_index do |line, i|
  puts "Line #{i}: #{line[0..30]}..." if line
end

# Process with condition
puts "\nLines containing '1000':"
matching_lines = processor.lines.take_while { |line| line && line.include?('1000') }
puts "Found #{matching_lines.length} matching lines"

processor.close
File.delete('large_test.txt') rescue nil
```

### Garbage Collection Optimization

```ruby
class GCOptimizer
  def self.optimize_gc
    # Configure GC settings
    GC::Profiler.enable
    
    # Adjust GC parameters for better performance
    old_gc_heap_slots = GC.stat[:heap_used_slots]
    
    yield
    
    # Force garbage collection
    GC.start
    
    # Report GC statistics
    puts "GC Statistics:"
    puts "Heap slots before: #{old_gc_heap_slots}"
    puts "Heap slots after: #{GC.stat[:heap_used_slots]}"
    puts "GC runs: #{GC.stat[:count]}"
    
    GC::Profiler.report
    GC::Profiler.disable
  end
  
  def self.memory_efficient_iteration(collection, &block)
    collection.each_slice(1000) do |chunk|
      chunk.each(&block)
      GC.start if GC.stat[:heap_used_slots] > 1_000_000
    end
  end
end

# Usage
GCOptimizer.optimize_gc do
  # Create many temporary objects
  data = []
  100_000.times do |i|
    data << {
      id: i,
      data: "x" * 100,  # Create some memory pressure
      timestamp: Time.now
    }
    
    # Periodically trigger GC
    GC.start if i % 10_000 == 0
  end
  
  puts "Created #{data.length} objects"
end

# Memory-efficient processing
large_collection = (1..100_000).to_a

GCOptimizer.memory_efficient_iteration(large_collection) do |item|
  # Process item
  item_squared = item ** 2
end
```

## Caching Strategies

### In-Memory Cache

```ruby
class MemoryCache
  def initialize(max_size = 1000)
    @cache = {}
    @access_times = {}
    @max_size = max_size
    @mutex = Mutex.new
  end
  
  def get(key)
    @mutex.synchronize do
      if @cache.key?(key)
        @access_times[key] = Time.now
        @cache[key]
      else
        nil
      end
    end
  end
  
  def set(key, value, ttl = nil)
    @mutex.synchronize do
      @cache[key] = value
      @access_times[key] = Time.now
      
      if ttl
        # Schedule expiration
        Thread.new do
          sleep(ttl)
          delete(key)
        end
      end
      
      evict_if_necessary
    end
  end
  
  def delete(key)
    @mutex.synchronize do
      @cache.delete(key)
      @access_times.delete(key)
    end
  end
  
  def clear
    @mutex.synchronize do
      @cache.clear
      @access_times.clear
    end
  end
  
  def size
    @cache.size
  end
  
  private
  
  def evict_if_necessary
    return if @cache.size <= @max_size
    
    # LRU eviction
    oldest_key = @access_times.min_by { |_, time| time }.first
    delete(oldest_key)
  end
end

# Usage
cache = MemoryCache.new

# Set values
cache.set("user:1", { name: "John", age: 30 })
cache.set("user:2", { name: "Jane", age: 25 })

# Get values
user1 = cache.get("user:1")
puts "User 1: #{user1[:name]}" if user1

# Set with TTL
cache.set("temp:1", "Temporary data", 2)
puts "Temporary data: #{cache.get('temp:1')}"
sleep(3)
puts "After TTL: #{cache.get('temp:1')}"

puts "Cache size: #{cache.size}"
```

### Multi-Level Cache

```ruby
class MultiLevelCache
  def initialize(memory_size = 100, disk_cache_dir = 'cache')
    @memory_cache = MemoryCache.new(memory_size)
    @disk_cache_dir = disk_cache_dir
    FileUtils.mkdir_p(@disk_cache_dir)
  end
  
  def get(key)
    # Try memory cache first
    value = @memory_cache.get(key)
    return value if value
    
    # Try disk cache
    disk_value = get_from_disk(key)
    if disk_value
      # Promote to memory cache
      @memory_cache.set(key, disk_value)
      return disk_value
    end
    
    nil
  end
  
  def set(key, value, memory_ttl = nil, disk_ttl = nil)
    # Store in both caches
    @memory_cache.set(key, value, memory_ttl)
    set_to_disk(key, value, disk_ttl)
  end
  
  def delete(key)
    @memory_cache.delete(key)
    delete_from_disk(key)
  end
  
  def clear
    @memory_cache.clear
    clear_disk_cache
  end
  
  def stats
    {
      memory_size: @memory_cache.size,
      disk_size: Dir.glob("#{@disk_cache_dir}/*").length
    }
  end
  
  private
  
  def disk_key_path(key)
    File.join(@disk_cache_dir, Digest::MD5.hexdigest(key.to_s))
  end
  
  def get_from_disk(key)
    path = disk_key_path(key)
    return nil unless File.exist?(path)
    
    begin
      data = File.read(path)
      Marshal.load(data)
    rescue
      nil
    end
  end
  
  def set_to_disk(key, value, ttl = nil)
    path = disk_key_path(key)
    
    File.write(path, Marshal.dump(value))
    
    if ttl
      Thread.new do
        sleep(ttl)
        delete_from_disk(key)
      end
    end
  end
  
  def delete_from_disk(key)
    path = disk_key_path(key)
    File.delete(path) if File.exist?(path)
  end
  
  def clear_disk_cache
    Dir.glob("#{@disk_cache_dir}/*").each { |file| File.delete(file) }
  end
end

# Usage
cache = MultiLevelCache.new

# Store data
cache.set("config:database", {
  host: "localhost",
  port: 5432,
  name: "myapp"
})

# Retrieve data
config = cache.get("config:database")
puts "Database host: #{config[:host]}" if config

# Show stats
puts "Cache stats: #{cache.stats}"
```

### Memoization

```ruby
module Memoization
  def memoize(method_name)
    original_method = instance_method(method_name)
    
    define_method(method_name) do |*args|
      cache_key = "#{method_name}_#{args.hash}"
      
      @memoization_cache ||= {}
      
      if @memoization_cache.key?(cache_key)
        @memoization_cache[cache_key]
      else
        result = original_method.bind(self).call(*args)
        @memoization_cache[cache_key] = result
        result
      end
    end
  end
end

# Usage
class Calculator
  extend Memoization
  
  def initialize
    @call_count = Hash.new(0)
  end
  
  def fibonacci(n)
    @call_count[:fibonacci] += 1
    return n if n <= 1
    fibonacci(n - 1) + fibonacci(n - 2)
  end
  
  memoize :fibonacci
  
  def expensive_calculation(x, y)
    @call_count[:expensive_calculation] += 1
    sleep(0.1)  # Simulate expensive operation
    x * y + x + y
  end
  
  memoize :expensive_calculation
  
  def call_counts
    @call_count
  end
end

calc = Calculator.new

# Test memoization
puts "Fibonacci(30): #{calc.fibonacci(30)}"
puts "Fibonacci(30) again: #{calc.fibonacci(30)}"
puts "Fibonacci calls: #{calc.call_counts[:fibonacci]}"

puts "\nExpensive calculation:"
puts "Result: #{calc.expensive_calculation(10, 20)}"
puts "Result again: #{calc.expensive_calculation(10, 20)}"
puts "Expensive calculation calls: #{calc.call_counts[:expensive_calculation]}"
```

## Code Optimization

### String Optimization

```ruby
class StringOptimizer
  def self.benchmark_string_operations
    str = "Hello"
    
    Benchmark.bm(20) do |x|
      x.report("String concatenation:") do
        100_000.times { str + " World" }
      end
      
      x.report("String interpolation:") do
        100_000.times { "#{str} World" }
      end
      
      x.report("Array join:") do
        100_000.times { [str, " World"].join }
      end
      
      x.report("String << (mutating):") do
        100_000.times { str.dup << " World" }
      end
    end
  end
  
  def self.optimize_string_building
    # Bad: Creates many intermediate strings
    def build_string_bad(items)
      result = ""
      items.each { |item| result += item.to_s + ", " }
      result
    end
    
    # Good: Uses array join
    def build_string_good(items)
      items.map(&:to_s).join(", ")
    end
    
    # Better: Uses StringIO for large strings
    def build_string_better(items)
      require 'stringio'
      
      io = StringIO.new
      items.each_with_index do |item, i|
        io << item.to_s
        io << ", " if i < items.length - 1
      end
      io.string
    end
    
    items = (1..1000).to_a
    
    Benchmark.bm(25) do |x|
      x.report("Bad method:") { 100.times { build_string_bad(items) } }
      x.report("Good method:") { 100.times { build_string_good(items) } }
      x.report("Better method:") { 100.times { build_string_better(items) } }
    end
  end
end

puts "String Optimization:"
StringOptimizer.benchmark_string_operations
StringOptimizer.optimize_string_building
```

### Loop Optimization

```ruby
class LoopOptimizer
  def self.benchmark_loops
    data = (1..100_000).to_a
    
    Benchmark.bm(25) do |x|
      x.report("Each with block:") do
        sum = 0
        data.each { |n| sum += n }
      end
      
      x.report("Each with symbol proc:") do
        sum = 0
        data.each(&method(:add_to_sum))
      end
      
      x.report("While loop:") do
        sum = 0
        i = 0
        while i < data.length
          sum += data[i]
          i += 1
        end
      end
      
      x.report("For loop:") do
        sum = 0
        for n in data
          sum += n
        end
      end
      
      x.report("Reduce:") do
        data.reduce(0, :+)
      end
    end
  end
  
  def self.add_to_sum(n, sum)
    sum + n
  end
  
  def self.optimize_array_operations
    data = (1..10_000).to_a
    
    Benchmark.bm(30) do |x|
      x.report("Array#select + Array#map:") do
        1000.times do
          data.select { |n| n.even? }.map { |n| n * 2 }
        end
      end
      
      x.report("Array#map + Array#compact:") do
        1000.times do
          data.map { |n| n.even? ? n * 2 : nil }.compact
        end
      end
      
      x.report("Single loop:") do
        1000.times do
          result = []
          data.each do |n|
            result << n * 2 if n.even?
          end
          result
        end
      end
    end
  end
end

puts "\nLoop Optimization:"
LoopOptimizer.benchmark_loops
LoopOptimizer.optimize_array_operations
```

### Algorithm Optimization

```ruby
class AlgorithmOptimizer
  def self.search_algorithms
    data = (1..1_000_000).to_a
    target = 999_999
    
    Benchmark.bm(20) do |x|
      x.report("Linear search:") do
        100.times { data.include?(target) }
      end
      
      x.report("Binary search (array):") do
        100.times { data.bsearch { |n| n >= target } == target }
      end
      
      x.report("Set lookup:") do
        set = data.to_set
        100.times { set.include?(target) }
      end
      
      x.report("Hash lookup:") do
        hash = data.each_with_index.to_h
        100.times { hash.key?(target) }
      end
    end
  end
  
  def self.sorting_algorithms
    data = (1..1000).to_a.shuffle
    
    Benchmark.bm(20) do |x|
      x.report("Array#sort:") do
        100.times { data.dup.sort }
      end
      
      x.report("Array#sort! (in-place):") do
        100.times { data.dup.sort! }
      end
      
      x.report("Sort by specific key:") do
        objects = data.map { |n| { value: n } }
        100.times { objects.dup.sort_by { |obj| obj[:value] } }
      end
    end
  end
end

puts "\nAlgorithm Optimization:"
AlgorithmOptimizer.search_algorithms
AlgorithmOptimizer.sorting_algorithms
```

## Performance Monitoring

### Real-time Performance Monitor

```ruby
class PerformanceMonitor
  def initialize
    @metrics = {}
    @alerts = []
    @running = false
    @thread = nil
  end
  
  def start(interval = 1)
    return if @running
    
    @running = true
    @thread = Thread.new { monitor_loop(interval) }
  end
  
  def stop
    @running = false
    @thread&.join
  end
  
  def add_alert(metric, threshold, condition = :greater_than, &block)
    @alerts << {
      metric: metric,
      threshold: threshold,
      condition: condition,
      callback: block
    }
  end
  
  def get_metric(metric)
    @metrics[metric]
  end
  
  def all_metrics
    @metrics.dup
  end
  
  private
  
  def monitor_loop(interval)
    while @running
      collect_metrics
      check_alerts
      sleep(interval)
    end
  end
  
  def collect_metrics
    @metrics[:cpu_usage] = get_cpu_usage
    @metrics[:memory_usage] = get_memory_usage
    @metrics[:object_count] = get_object_count
    @metrics[:gc_count] = GC.stat[:count]
    @metrics[:thread_count] = Thread.list.size
    @metrics[:timestamp] = Time.now
  end
  
  def check_alerts
    @alerts.each do |alert|
      current_value = @metrics[alert[:metric]]
      next unless current_value
      
      triggered = case alert[:condition]
                 when :greater_than
                   current_value > alert[:threshold]
                 when :less_than
                   current_value < alert[:threshold]
                 else
                   false
                 end
      
      if triggered
        alert[:callback]&.call(alert[:metric], current_value, alert[:threshold])
      end
    end
  end
  
  def get_cpu_usage
    # Simplified CPU usage calculation
    # In real implementation, you'd use system-specific methods
    rand(0.1..0.8) * 100
  end
  
  def get_memory_usage
    `ps -o rss= -p #{Process.pid}`.strip.to_i
  end
  
  def get_object_count
    ObjectSpace.count_objects[:TOTAL]
  end
end

# Usage
monitor = PerformanceMonitor.new

# Add alerts
monitor.add_alert(:memory_usage, 100_000, :greater_than) do |metric, value, threshold|
  puts "🚨 ALERT: #{metric} is #{value} (threshold: #{threshold})"
end

monitor.add_alert(:cpu_usage, 80, :greater_than) do |metric, value, threshold|
  puts "🚨 ALERT: #{metric} is #{value.round(2)}% (threshold: #{threshold}%)"
end

monitor.start(2)

# Monitor for 10 seconds
sleep(10)

# Show metrics
puts "\nFinal Metrics:"
monitor.all_metrics.each do |metric, value|
  puts "#{metric}: #{value}"
end

monitor.stop
```

### Performance Dashboard

```ruby
class PerformanceDashboard
  def initialize
    @data_points = []
    @max_points = 100
  end
  
  def record_metrics(metrics)
    @data_points << metrics.dup
    
    # Keep only recent data points
    @data_points.shift if @data_points.length > @max_points
  end
  
  def generate_report
    return "No data available" if @data_points.empty?
    
    report = []
    report << "Performance Dashboard Report"
    report << "=" * 40
    report << "Data points: #{@data_points.length}"
    report << "Time range: #{@data_points.first[:timestamp]} - #{@data_points.last[:timestamp]}"
    report << ""
    
    # Calculate statistics for each metric
    metrics = @data_points.first.keys - [:timestamp]
    
    metrics.each do |metric|
      values = @data_points.map { |dp| dp[metric] }.compact
      
      if values.any?
        min = values.min
        max = values.max
        avg = values.sum.to_f / values.length
        
        report << "#{metric}:"
        report << "  Min: #{min.round(2)}"
        report << "  Max: #{max.round(2)}"
        report << "  Avg: #{avg.round(2)}"
        report << ""
      end
    end
    
    report.join("\n")
  end
  
  def export_csv(filename)
    return if @data_points.empty?
    
    CSV.open(filename, 'w') do |csv|
      # Header
      csv << ['timestamp'] + @data_points.first.keys.reject { |k| k == :timestamp }
      
      # Data rows
      @data_points.each do |dp|
        row = [dp[:timestamp]]
        dp.each { |key, value| row << value unless key == :timestamp }
        csv << row
      end
    end
  end
end

# Usage
dashboard = PerformanceDashboard.new

# Simulate data collection
10.times do |i|
  metrics = {
    timestamp: Time.now - (9 - i) * 60,  # Last 10 minutes
    response_time: 100 + rand(50),
    throughput: 1000 + rand(200),
    error_rate: rand(5),
    cpu_usage: 50 + rand(30),
    memory_usage: 80_000 + rand(20_000)
  }
  
  dashboard.record_metrics(metrics)
end

puts dashboard.generate_report

# Export to CSV
dashboard.export_csv('performance_data.csv')
puts "Performance data exported to performance_data.csv"
```

## Best Practices

### 1. Performance Guidelines

```ruby
# Do's and Don'ts for Ruby Performance

class PerformanceGuidelines
  # ✅ DO: Use appropriate data structures
  def good_data_structures
    # Use Set for membership testing
    elements = Set.new([1, 2, 3, 4, 5])
    puts elements.include?(3)  # O(1)
    
    # Use Hash for key-value lookups
    lookup = { 'a' => 1, 'b' => 2, 'c' => 3 }
    puts lookup['b']  # O(1)
  end
  
  # ❌ DON'T: Use Array for membership testing on large datasets
  def bad_data_structures
    elements = [1, 2, 3, 4, 5]
    puts elements.include?(3)  # O(n)
  end
  
  # ✅ DO: Use symbols for hash keys when possible
  def good_hash_keys
    user = { name: "John", age: 30, email: "john@example.com" }
    puts user[:name]
  end
  
  # ❌ DON'T: Use strings for hash keys unnecessarily
  def bad_hash_keys
    user = { "name" => "John", "age" => 30, "email" => "john@example.com" }
    puts user["name"]
  end
  
  # ✅ DO: Use lazy enumeration for large datasets
  def good_enumeration
    large_range = (1..1_000_000)
    
    # Process without creating intermediate arrays
    result = large_range.lazy
                     .select { |n| n.even? }
                     .map { |n| n * 2 }
                     .first(10)
    
    puts result.length
  end
  
  # ❌ DON'T: Create unnecessary intermediate arrays
  def bad_enumeration
    large_range = (1..1_000_000)
    
    # Creates multiple intermediate arrays
    result = large_range.select { |n| n.even? }
                           .map { |n| n * 2 }
                           .first(10)
    
    puts result.length
  end
end
```

### 2. Memory Management Best Practices

```ruby
class MemoryBestPractices
  # ✅ DO: Use objects efficiently
  def efficient_object_usage
    # Reuse objects when possible
    buffer = String.new
    
    1000.times do |i|
      buffer.clear
      buffer << "Processing item #{i}"
      puts buffer
    end
  end
  
  # ✅ DO: Clean up resources
  def resource_cleanup
    file = File.open('temp.txt', 'w')
    
    begin
      file.puts "Temporary data"
    ensure
      file.close  # Always close resources
      File.delete('temp.txt') rescue nil
    end
  end
  
  # ✅ DO: Use weak references for caches
  def weak_references
    require 'weakref'
    
    cache = {}
    obj = Object.new
    
    # Use weak reference to allow GC
    cache[:temp] = WeakRef.new(obj)
    
    # Object can be garbage collected
    obj = nil
    GC.start
    
    puts "Weak ref still valid: #{cache[:temp].weakref_alive?}"
  end
end
```

### 3. Profiling Workflow

```ruby
class ProfilingWorkflow
  def self.optimize_method
    # 1. Benchmark current performance
    puts "1. Benchmarking current implementation..."
    baseline = benchmark_current_implementation
    
    # 2. Profile to find bottlenecks
    puts "2. Profiling bottlenecks..."
    bottlenecks = profile_implementation
    
    # 3. Implement optimizations
    puts "3. Implementing optimizations..."
    optimized_time = benchmark_optimized_implementation
    
    # 4. Compare results
    puts "4. Comparing results..."
    improvement = (baseline - optimized_time) / baseline * 100
    
    puts "Baseline: #{baseline.round(4)}s"
    puts "Optimized: #{optimized_time.round(4)}s"
    puts "Improvement: #{improvement.round(2)}%"
  end
  
  private
  
  def self.benchmark_current_implementation
    Benchmark.realtime do
      slow_implementation
    end
  end
  
  def self.benchmark_optimized_implementation
    Benchmark.realtime do
      optimized_implementation
    end
  end
  
  def self.slow_implementation
    result = []
    100_000.times { |i| result << i * 2 }
    result
  end
  
  def self.optimized_implementation
    # Use more efficient approach
    (1..100_000).map { |i| i * 2 }
  end
  
  def self.profile_implementation
    # Implementation would use actual profiling tools
    puts "Bottleneck found in array allocation"
  end
end

puts "\nProfiling Workflow:"
ProfilingWorkflow.optimize_method
```

## Practice Exercises

### Exercise 1: Performance Profiler
Create a comprehensive performance profiler with:
- Method-level timing
- Memory usage tracking
- Call graph visualization
- Performance recommendations

### Exercise 2: Intelligent Cache
Build an adaptive cache system with:
- LRU eviction
- Size-based eviction
- Access pattern analysis
- Automatic tuning

### Exercise 3: Memory Optimizer
Develop a memory optimization tool with:
- Object usage analysis
- Memory leak detection
- Garbage collection optimization
- Memory pressure monitoring

### Exercise 4: Performance Dashboard
Create a real-time performance dashboard with:
- Live metrics display
- Historical data visualization
- Alert system
- Export capabilities

---

**Ready to explore more advanced Ruby topics? Let's continue! ⚡**
