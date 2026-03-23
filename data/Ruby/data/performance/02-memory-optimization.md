# Memory Optimization in Ruby
# Comprehensive guide to memory management and optimization techniques

## 🎯 Overview

Memory optimization is crucial for Ruby applications, especially in production environments. This guide covers Ruby's memory model, garbage collection, and optimization techniques for efficient memory usage.

## 🧠 Ruby Memory Model

### 1. Understanding Ruby Objects

Ruby's object allocation and memory management:

```ruby
# Object allocation analysis
require 'objspace'

class ObjectAnalyzer
  def self.analyze_object_creation
    GC.start
    before = ObjectSpace.count_objects
    
    # Create different types of objects
    strings = []
    arrays = []
    hashes = []
    
    1000.times { |i| strings << "string_#{i}" }
    500.times { |i| arrays << [i, i * 2] }
    200.times { |i| hashes[i] = i * 3 }
    
    GC.start
    after = ObjectSpace.count_objects
    
    delta = after.transform_values { |after_val, before_val| after_val - before_val }
    
    puts "Object Creation Analysis:"
    puts "  Strings created: #{delta[:T_STRING]}"
    puts "  Arrays created: #{delta[:T_ARRAY]}"
    puts "  Hashes created: #{delta[:T_HASH]}"
    puts "  Total objects: #{delta[:TOTAL]}"
    
    delta
  end
  
  def self.analyze_object_sizes
    objects = []
    
    # Create objects of different sizes
    small_string = "small"
    large_string = "x" * 10000
    small_array = [1, 2, 3]
    large_array = (1..1000).to_a
    small_hash = { a: 1, b: 2 }
    large_hash = {}
    100.times { |i| large_hash["key_#{i}"] = "value_#{i}" }
    
    objects << small_string << large_string << small_array << large_array << small_hash << large_hash
    
    puts "Object Size Analysis:"
    objects.each_with_index do |obj, index|
      puts "  Object #{index + 1}: #{obj.class} - #{obj.inspect.length} characters"
    end
  end
  
  def self.analyze_memory_usage
    # Different data structures and their memory impact
    
    # String vs Symbol
    string_memory = []
    symbol_memory = []
    
    1000.times { |i| string_memory << "string_#{i}" }
    1000.times { |i| symbol_memory << "symbol_#{i}".to_sym }
    
    GC.start
    before_strings = ObjectSpace.count_objects[:T_STRING]
    before_symbols = ObjectSpace.count_objects[:T_SYMBOL]
    
    string_memory.clear
    symbol_memory.clear
    
    GC.start
    after_strings = ObjectSpace.count_objects[:T_STRING]
    after_symbols = ObjectSpace.count_objects[:T_SYMBOL]
    
    puts "Memory Usage Analysis:"
    puts "  String memory: #{before_strings - after_strings} objects"
    puts "  Symbol memory: #{before_symbols - after_symbols} objects"
    puts "  Note: Symbols are not garbage collected"
  end
end

# Run analysis
ObjectAnalyzer.analyze_object_creation
ObjectAnalyzer.analyze_object_sizes
ObjectAnalyzer.analyze_memory_usage
```

### 2. Garbage Collection

Understanding Ruby's garbage collector:

```ruby
class GarbageCollectionAnalyzer
  def self.analyze_gc_behavior
    puts "Garbage Collection Analysis:"
    puts "=" * 40
    
    # Initial GC state
    GC.start
    initial_stats = GC.stat
    
    puts "Initial GC Stats:"
    puts "  Collections: #{initial_stats[:count]}"
    puts "  Heap pages: #{initial_stats[:heap_used_pages]}"
    puts "  Total allocated: #{initial_stats[:total_allocated_objects]}"
    
    # Create objects
    objects = []
    10000.times { |i| objects << "object_#{i}" }
    
    # Force GC
    GC.start
    after_creation_stats = GC.stat
    
    puts "\nAfter Object Creation:"
    puts "  Collections: #{after_creation_stats[:count]}"
    puts "  Heap pages: #{after_creation_stats[:heap_used_pages]}"
    puts "  Total allocated: #{after_creation_stats[:total_allocated_objects]}"
    
    # Clear references
    objects.clear
    
    # Force GC again
    GC.start
    after_cleanup_stats = GC.stat
    
    puts "\nAfter Cleanup:"
    puts "  Collections: #{after_cleanup_stats[:count]}"
    puts "  Heap pages: #{after_cleanup_stats[:heap_used_pages]}"
    puts "  Total allocated: #{after_cleanup_stats[:total_allocated_objects]}"
    
    # Calculate GC impact
    collections = after_cleanup_stats[:count] - initial_stats[:count]
    allocated = after_cleanup_stats[:total_allocated_objects] - initial_stats[:total_allocated_objects]
    
    puts "\nGC Impact:"
    puts "  Collections performed: #{collections}"
    puts "  Objects allocated: #{allocated}"
    puts "  Average objects per collection: #{allocated / collections}" if collections > 0
  end
  
  def self.analyze_gc_tuning
    puts "\nGarbage Collection Tuning:"
    puts "=" * 40
    
    # Test different GC settings
    original_gc_stress = GC.stress
    
    # High stress GC
    GC.stress = true
    high_stress_time = measure_gc_performance
    
    # Normal GC
    GC.stress = false
    normal_time = measure_gc_performance
    
    # Low stress GC
    GC.stress = false
    low_stress_time = measure_gc_performance
    
    puts "GC Performance Comparison:"
    puts "  High stress GC: #{high_stress_time.round(4)}s"
    puts "  Normal GC: #{normal_time.round(4)}s"
    puts "  Low stress GC: #{low_stress_time.round(4)}s"
    
    # Restore original setting
    GC.stress = original_gc_stress
  end
  
  def self.measure_gc_performance
    # Create objects
    objects = []
    1000.times { |i| objects << "test_object_#{i}" }
    
    # Measure GC time
    start_time = Time.now
    GC.start
    end_time = Time.now
    
    end_time - start_time
  end
end

# Run GC analysis
GarbageCollectionAnalyzer.analyze_gc_behavior
GarbageCollectionAnalyzer.analyze_gc_tuning
```

## 🚀 Memory Optimization Techniques

### 1. String Optimization

Optimize string usage and memory:

```ruby
class StringOptimizer
  def self.demonstrate_string_memory
    puts "String Memory Optimization:"
    puts "=" * 40
    
    # String concatenation methods
    methods = {
      'String + operator' => -> {
        str = ""
        1000.times { |i| str += "item_#{i}" }
        str
      },
      
      'Array.join' => -> {
        items = 1000.times.map { |i| "item_#{i}" }
        items.join
      },
      
      'StringIO' => -> {
        io = StringIO.new
        1000.times { |i| io.write("item_#{i}") }
        io.string
      },
      
      'String.concat' => -> {
        str = ""
        1000.times { |i| str.concat("item_#{i}") }
        str
      }
    }
    
    # Test each method
    methods.each do |name, method|
      GC.start
      before = ObjectSpace.count_objects[:T_STRING]
      
      result = method.call
      
      GC.start
      after = ObjectSpace.count_objects[:T_STRING]
      
      puts "#{name}: #{after - before} strings created"
    end
  end
  
  def self.demonstrate_symbol_vs_string
    puts "\nSymbol vs String Memory:"
    puts "=" * 40
    
    # String usage
    GC.start
    before_strings = ObjectSpace.count_objects[:T_STRING]
    
    string_keys = []
    1000.times { |i| string_keys << "key_#{i}" }
    
    GC.start
    after_strings = ObjectSpace.count_objects[:T_STRING]
    
    # Symbol usage
    GC.start
    before_symbols = ObjectSpace.count_objects[:T_SYMBOL]
    
    symbol_keys = []
    1000.times { |i| symbol_keys << "key_#{i}".to_sym }
    
    GC.start
    after_symbols = ObjectSpace.count_objects[:T_SYMBOL]
    
    puts "String keys: #{after_strings - before_strings} strings"
    puts "Symbol keys: #{after_symbols - before_symbols} symbols"
    puts "Note: Symbols are not garbage collected"
  end
  
  def self.demonstrate_string_interning
    puts "\nString Interning:"
    puts "=" * 40
    
    # Without interning
    GC.start
    before = ObjectSpace.count_objects[:T_STRING]
    
    strings = []
    1000.times { |i| strings << "common_string_#{i % 100}" }
    
    GC.start
    after = ObjectSpace.count_objects[:T_STRING]
    
    puts "Without interning: #{after - before} strings"
    
    # With interning
    GC.start
    before_interned = ObjectSpace.count_objects[:T_STRING]
    
    interned_strings = []
    1000.times { |i| interned_strings << "common_string_#{i % 100}".intern }
    
    GC.start
    after_interned = ObjectSpace.count_objects[:T_STRING]
    
    puts "With interning: #{after_interned - before_interned} strings"
    puts "Interning reduces memory for repeated strings"
  end
end

# Run string optimization examples
StringOptimizer.demonstrate_string_memory
StringOptimizer.demonstrate_symbol_vs_string
StringOptimizer.demonstrate_string_interning
```

### 2. Array and Hash Optimization

Optimize collection memory usage:

```ruby
class CollectionOptimizer
  def self.demonstrate_array_optimization
    puts "Array Memory Optimization:"
    puts "=" * 40
    
    # Array creation methods
    methods = {
      'Array.new with size' => -> {
        arr = Array.new(1000)
        1000.times { |i| arr[i] = i }
        arr
      },
      
      'Array.new with block' => -> {
        Array.new(1000) { |i| i }
      },
      
      'Range.to_a' => -> {
        (0..999).to_a
      },
      
      'Manual push' => -> {
        arr = []
        1000.times { |i| arr << i }
        arr
      }
    }
    
    # Test each method
    methods.each do |name, method|
      GC.start
      before = ObjectSpace.count_objects[:T_ARRAY]
      
      result = method.call
      
      GC.start
      after = ObjectSpace.count_objects[:T_ARRAY]
      
      puts "#{name}: #{after - before} arrays created"
    end
  end
  
  def self.demonstrate_hash_optimization
    puts "\nHash Memory Optimization:"
    puts "=" * 40
    
    # Hash creation methods
    methods = {
      'Hash.new with default' => -> {
        hash = Hash.new(0)
        1000.times { |i| hash[i] = i * 2 }
        hash
      },
      
      'Hash literal' => -> {
        hash = {}
        1000.times { |i| hash[i] = i * 2 }
        hash
      },
      
      'Hash with block' => -> {
        Hash.new { |h, k| h[k] = k * 2 }
      end
    }
    
    # Test each method
    methods.each do |name, method|
      GC.start
      before = ObjectSpace.count_objects[:T_HASH]
      
      result = method.call
      
      GC.start
      after = ObjectSpace.count_objects[:T_HASH]
      
      puts "#{name}: #{after - before} hashes created"
    end
  end
  
  def self.demonstrate_collection_cleanup
    puts "\nCollection Cleanup:"
    puts "=" * 40
    
    # Test collection cleanup
    GC.start
    before = ObjectSpace.count_objects
    
    # Create collections
    arrays = []
    hashes = []
    
    100.times do |i|
      arrays << Array.new(100) { |j| "#{i}_#{j}" }
      hashes << Hash.new { |h, k| h[k] = "value_#{k}" }
    end
    
    GC.start
    after_creation = ObjectSpace.count_objects
    
    # Clear references
    arrays.clear
    hashes.clear
    
    GC.start
    after_cleanup = ObjectSpace.count_objects
    
    puts "Before creation: #{before[:TOTAL]} objects"
    puts "After creation: #{after_creation[:TOTAL]} objects"
    puts "After cleanup: #{after_cleanup[:TOTAL]} objects"
    puts "Objects freed: #{after_creation[:TOTAL] - after_cleanup[:TOTAL]}"
  end
  
  def self.demonstrate_lazy_evaluation
    puts "\nLazy Evaluation:"
    puts "=" * 40
    
    # Eager evaluation
    GC.start
    before_eager = ObjectSpace.count_objects[:T_ARRAY]
    
    eager_array = (1..10000).map { |i| i * 2 }
    eager_sum = eager_array.sum
    
    GC.start
    after_eager = ObjectSpace.count_objects[:T_ARRAY]
    
    # Lazy evaluation
    GC.start
    before_lazy = ObjectSpace.count_objects[:T_ARRAY]
    
    lazy_array = (1..10000).lazy.map { |i| i * 2 }
    lazy_sum = lazy_array.sum
    
    GC.start
    after_lazy = ObjectSpace.count_objects[:T_ARRAY]
    
    puts "Eager evaluation: #{after_eager - before_eager} arrays"
    puts "Lazy evaluation: #{after_lazy - before_lazy} arrays"
    puts "Lazy evaluation reduces memory usage"
  end
end

# Run collection optimization examples
CollectionOptimizer.demonstrate_array_optimization
CollectionOptimizer.demonstrate_hash_optimization
CollectionOptimizer.demonstrate_collection_cleanup
CollectionOptimizer.demonstrate_lazy_evaluation
```

### 3. Object Pool Pattern

Reuse objects to reduce allocation:

```ruby
class ObjectPool
  def initialize(create_proc, reset_proc = nil, max_size = 10)
    @create_proc = create_proc
    @reset_proc = reset_proc
    @max_size = max_size
    @pool = []
    @mutex = Mutex.new
  end
  
  def with_object
    obj = checkout
    begin
      yield obj
    ensure
      checkin(obj)
    end
  end
  
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
      if @pool.length < @max_size
        @reset_proc.call(obj) if @reset_proc
        @pool.push(obj)
      end
    end
  end
  
  def pool_size
    @mutex.synchronize { @pool.length }
  end
  
  def clear
    @mutex.synchronize { @pool.clear }
  end
end

# Example: Database connection pool
class DatabaseConnection
  attr_reader :id, :created_at
  
  def initialize
    @id = rand(1000)
    @created_at = Time.now
    @connected = false
  end
  
  def connect
    @connected = true
    puts "Connection #{@id} connected"
  end
  
  def disconnect
    @connected = false
    puts "Connection #{@id} disconnected"
  end
  
  def execute(query)
    raise "Not connected" unless @connected
    "Result for: #{query}"
  end
  
  def reset
    disconnect if @connected
  end
end

# Usage of object pool
class DatabaseConnectionPool
  def initialize(size = 5)
    @pool = ObjectPool.new(
      -> { DatabaseConnection.new },
      ->(conn) { conn.reset },
      size
    )
  end
  
  def execute_query(query)
    @pool.with_object do |connection|
      connection.connect
      connection.execute(query)
    end
  end
  
  def pool_stats
    {
      pool_size: @pool.pool_size,
      max_size: 5
    }
  end
end

# Example: String buffer pool
class StringBuffer
  def initialize
    @buffer = ""
    @length = 0
  end
  
  def append(str)
    @buffer += str
    @length += str.length
  end
  
  def to_s
    @buffer
  end
  
  def length
    @length
  end
  
  def reset
    @buffer = ""
    @length = 0
  end
end

class StringBufferPool
  def initialize(size = 10)
    @pool = ObjectPool.new(
      -> { StringBuffer.new },
      ->(buf) { buf.reset },
      size
    )
  end
  
  def build_string(&block)
    @pool.with_object do |buffer|
      yield buffer
      buffer.to_s
    end
  end
end

# Usage examples
puts "Object Pool Example:"
puts "=" * 40

# Database connection pool
db_pool = DatabaseConnectionPool.new(3)

# Execute queries using pooled connections
5.times do |i|
  result = db_pool.execute_query("SELECT * FROM users WHERE id = #{i}")
  puts "Query #{i} result: #{result}"
end

puts "Pool stats: #{db_pool.pool_stats}"

# String buffer pool
string_pool = StringBufferPool.new(5)

# Build strings using pooled buffers
3.times do |i|
  result = string_pool.build_string do |buffer|
    100.times { |j| buffer.append("Item #{i}-#{j}, ") }
  end
  puts "String #{i} length: #{result.length}"
end

puts "String buffer pool size: #{string_pool.pool_size}"
```

## 🔍 Memory Leak Detection

### 1. Memory Leak Detection Tools

Identify and prevent memory leaks:

```ruby
class MemoryLeakDetector
  def initialize
    @snapshots = []
    @leak_threshold = 1000  # Objects considered leak if > 1000
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
  
  def detect_leaks
    return nil if @snapshots.length < 2
    
    latest = @snapshots.last
    previous = @snapshots[-2]
    
    leaks = {}
    
    latest[:object_counts].each do |type, count|
      previous_count = previous[:object_counts][type] || 0
      delta = count - previous_count
      
      if delta > @leak_threshold
        leaks[type] = delta
      end
    end
    
    leaks
  end
  
  def analyze_growth
    return nil if @snapshots.length < 2
    
    first = @snapshots.first
    latest = @snapshots.last
    
    growth = {}
    
    latest[:object_counts].each do |type, count|
      first_count = first[:object_counts][type] || 0
      growth[type] = count - first_count
    end
    
    # Sort by growth
    growth.sort_by { |_, count| -count }
  end
  
  def print_leak_report
    leaks = detect_leaks
    
    if leaks.empty?
      puts "No significant memory leaks detected"
    else
      puts "Memory Leak Report:"
      puts "=" * 40
      
      leaks.each do |type, count|
        puts "  #{type}: #{count} objects (potential leak)"
      end
    end
  end
  
  def print_growth_report
    growth = analyze_growth
    
    if growth.empty?
      puts "No memory growth detected"
    else
      puts "Memory Growth Report:"
      puts "=" * 40
      
      growth.first(10).each do |type, count|
        puts "  #{type}: +#{count} objects"
      end
    end
  end
  
  def clear_snapshots
    @snapshots.clear
  end
end

# Memory leak examples
class MemoryLeakExamples
  def self.leaky_method
    @leaky_array ||= []
    1000.times { |i| @leaky_array << "leaky_item_#{i}" }
  end
  
  def self.non_leaky_method
    array = []
    1000.times { |i| array << "item_#{i}" }
    array  # Array will be garbage collected
  end
  
  def self.reference_leak
    @objects ||= []
    100.times do
      obj = Object.new
      @objects << obj
    end
    # @objects array prevents garbage collection
  end
  
  def self.block_leak
    @blocks ||= []
    100.times do |i|
      @blocks << proc { puts "Block #{i}" }
    end
    # Blocks are kept in memory
  end
  
  def self.cleanup_leaks
    @leaky_array = nil
    @objects = nil
    @blocks = nil
    GC.start
  end
end

# Usage
detector = MemoryLeakDetector.new

# Take initial snapshot
detector.take_snapshot("Initial")

# Create leaks
puts "Creating memory leaks..."
10.times { MemoryLeakExamples.leaky_method }
detector.take_snapshot("After leaky method")

# Create non-leaking objects
puts "Creating non-leaking objects..."
10.times { MemoryLeakExamples.non_leaky_method }
detector.take_snapshot("After non-leaky method")

# Create reference leaks
puts "Creating reference leaks..."
MemoryLeakExamples.reference_leak
detector.take_snapshot("After reference leak")

# Create block leaks
puts "Creating block leaks..."
MemoryLeakExamples.block_leak
detector.take_snapshot("After block leak")

# Analyze
detector.print_leak_report
detector.print_growth_report

# Cleanup
puts "Cleaning up leaks..."
MemoryLeakExamples.cleanup_leaks
detector.take_snapshot("After cleanup")
detector.print_growth_report
```

## 🎯 Memory Optimization Best Practices

### 1. Memory Management Guidelines

```ruby
class MemoryManagementGuidelines
  def self.optimize_string_usage
    # Use symbols for frequently used strings
    STATUS_SYMBOLS = {
      success: :success,
      error: :error,
      pending: :pending
    }.freeze
    
    # Use string interpolation over concatenation
    def build_message(type, message)
      "#{type.upcase}: #{message}"
    end
    
    # Use string builder for large strings
    def build_large_string
      buffer = StringIO.new
      1000.times { |i| buffer.write("Line #{i}\n") }
      buffer.string
    end
  end
  
  def self.optimize_collection_usage
    # Pre-allocate collections when size is known
    def process_items(items)
      results = Array.new(items.length)
      items.each_with_index { |item, i| results[i] = item * 2 }
      results
    end
    
    # Use appropriate data structures
    def fast_lookup(items)
      # Use hash for O(1) lookup
      lookup = {}
      items.each { |item| lookup[item[:id]] = item }
      lookup
    end
    
    # Clear references when done
    def process_large_dataset
      data = load_large_dataset
      result = process_data(data)
      data = nil  # Clear reference
      GC.start
      result
    end
  end
  
  def self.monitor_memory_usage
    # Monitor memory usage periodically
    Thread.new do
      loop do
        memory_stats = get_memory_stats
        log_memory_stats(memory_stats)
        sleep(60)  # Check every minute
      end
    end
  end
  
  private
  
  def self.get_memory_stats
    GC.start
    stats = GC.stat
    
    {
      heap_used: stats[:heap_used_pages],
      heap_length: stats[:heap_length],
      total_allocated: stats[:total_allocated_objects],
      major_gc: stats[:major_gc_count],
      minor_gc: stats[:minor_gc_count]
    }
  end
  
  def self.log_memory_stats(stats)
    puts "Memory Stats - Heap: #{stats[:heap_used]}, Objects: #{stats[:total_allocated]}"
  end
end
```

### 2. Production Memory Monitoring

```ruby
class ProductionMemoryMonitor
  def initialize(alert_threshold = 1000000)  # 1GB
    @alert_threshold = alert_threshold
    @monitoring_thread = nil
  end
  
  def start
    @monitoring_thread = Thread.new do
      loop do
        check_memory_usage
        sleep(30)  # Check every 30 seconds
      end
    end
  end
  
  def stop
    @monitoring_thread&.kill
  end
  
  private
  
  def check_memory_usage
    stats = get_memory_stats
    memory_usage = calculate_memory_usage(stats)
    
    if memory_usage > @alert_threshold
      send_memory_alert(memory_usage, stats)
    end
    
    log_memory_usage(memory_usage, stats)
  end
  
  def get_memory_stats
    GC.start
    GC.stat
  end
  
  def calculate_memory_usage(stats)
    # Rough estimation of memory usage
    stats[:heap_used_pages] * 2 * 1024 * 1024  # Assuming 2MB per page
  end
  
  def send_memory_alert(usage, stats)
    puts "ALERT: High memory usage detected - #{usage / 1024 / 1024}MB"
    puts "Heap pages: #{stats[:heap_used_pages]}"
    puts "Total objects: #{stats[:total_allocated_objects]}"
    
    # Send to monitoring system
  end
  
  def log_memory_usage(usage, stats)
    puts "Memory usage: #{usage / 1024 / 1024}MB"
    puts "Heap: #{stats[:heap_used_pages]} pages"
    puts "Objects: #{stats[:total_allocated_objects]}"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Object Analysis**: Analyze Ruby object creation
2. **GC Analysis**: Understand garbage collection behavior
3. **String Optimization**: Optimize string usage

### Intermediate Exercises

1. **Collection Optimization**: Optimize arrays and hashes
2. **Object Pool**: Implement object pooling
3. **Memory Leak Detection**: Detect memory leaks

### Advanced Exercises

1. **Memory Profiler**: Build a memory profiler
2. **Production Monitoring**: Create production monitoring
3. **Memory Benchmark**: Compare memory optimization techniques

---

## 🎯 Summary

Memory optimization in Ruby provides:

- **Understanding Memory Model** - Ruby's object allocation and GC
- **Optimization Techniques** - String, array, and hash optimization
- **Object Pooling** - Reuse objects to reduce allocation
- **Leak Detection** - Identify and prevent memory leaks
- **Production Monitoring** - Monitor memory in production

Master these techniques to build memory-efficient Ruby applications!
