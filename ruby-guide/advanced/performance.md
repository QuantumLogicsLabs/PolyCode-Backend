# Performance Optimization in Ruby

## Overview

Performance optimization in Ruby involves understanding bottlenecks, choosing the right algorithms and data structures, and using profiling tools to identify and fix performance issues.

## Profiling Tools

### Built-in Profiler

```ruby
require 'profile'

def slow_method
  10000.times do |i|
    Math.sqrt(i) * Math.sin(i)
  end
end

def fast_method
  (0..9999).map { |i| Math.sqrt(i) * Math.sin(i) }
end

# Profile the methods
slow_method
fast_method

# Run with: ruby -r profile your_script.rb
```

### Benchmark Module

```ruby
require 'benchmark'

# Basic benchmarking
time = Benchmark.measure do
  1000000.times { |i| i + 1 }
end

puts "Time elapsed: #{time.real} seconds"

# Compare different approaches
Benchmark.bm(20) do |x|
  x.report("Array#each:") do
    100000.times { |i| [i].each { |n| n + 1 } }
  end
  
  x.report("Direct access:") do
    100000.times { |i| i + 1 }
  end
  
  x.report("Array#map:") do
    100000.times { [i].map { |n| n + 1 } }
  end
end
```

### Memory Profiling

```ruby
require 'objspace'

def memory_usage
  ObjectSpace.count_objects[:TOTAL]
end

def create_objects
  10000.times { |i| "String #{i}" }
end

before = memory_usage
create_objects
after = memory_usage

puts "Objects created: #{after - before}"
```

## Algorithm Optimization

### Efficient Data Structures

```ruby
# Slow: Array for lookups
class SlowLookup
  def initialize
    @data = []
    10000.times { |i| @data << [i, "value_#{i}"] }
  end
  
  def find(key)
    @data.find { |k, _| k == key }&.last
  end
end

# Fast: Hash for lookups
class FastLookup
  def initialize
    @data = {}
    10000.times { |i| @data[i] = "value_#{i}" }
  end
  
  def find(key)
    @data[key]
  end
end

# Benchmark
slow = SlowLookup.new
fast = FastLookup.new

Benchmark.bm(15) do |x|
  x.report("Array lookup:") do
    1000.times { slow.find(rand(10000)) }
  end
  
  x.report("Hash lookup:") do
    1000.times { fast.find(rand(10000)) }
  end
end
```

### String Optimization

```ruby
# Slow: String concatenation in loop
def slow_concat
  result = ""
  10000.times { |i| result += "item_#{i} " }
  result
end

# Fast: Array join
def fast_concat
  parts = []
  10000.times { |i| parts << "item_#{i}" }
  parts.join(" ")
end

# Faster: StringIO
def faster_concat
  require 'stringio'
  result = StringIO.new
  10000.times { |i| result << "item_#{i} " }
  result.string
end

Benchmark.bm(15) do |x|
  x.report("String +=:") { slow_concat }
  x.report("Array join:") { fast_concat }
  x.report("StringIO:") { faster_concat }
end
```

### Loop Optimization

```ruby
# Different loop types
array = (1..100000).to_a

Benchmark.bm(15) do |x|
  x.report("each:") do
    sum = 0
    array.each { |n| sum += n }
  end
  
  x.report("while:") do
    sum = 0
    i = 0
    while i < array.length
      sum += array[i]
      i += 1
    end
  end
  
  x.report("for:") do
    sum = 0
    for n in array
      sum += n
    end
  end
  
  x.report("inject:") do
    array.reduce(0, :+)
  end
end
```

## Memory Optimization

### Object Pooling

```ruby
class ObjectPool
  def initialize(&block)
    @create_proc = block
    @pool = []
    @mutex = Mutex.new
  end
  
  def with_object
    obj = @mutex.synchronize do
      @pool.empty? ? @create_proc.call : @pool.pop
    end
    
    begin
      yield obj
    ensure
      @mutex.synchronize { @pool.push(obj) }
    end
  end
end

# Usage: Pool expensive objects
string_builder_pool = ObjectPool.new { StringIO.new }

1000.times do
  string_builder_pool.with_object do |io|
    io.rewind
    io.truncate(0)
    io << "Some data"
    # Use the object
  end
end
```

### Memory-Efficient Data Structures

```ruby
# Use Set for unique elements instead of Array
require 'set'

# Memory-intensive: Array with many duplicates
array_with_duplicates = []
10000.times { array_with_duplicates << rand(100) }

# Memory-efficient: Set for unique values
unique_values = Set.new
10000.times { unique_values.add(rand(100)) }

puts "Array size: #{array_with_duplicates.size}"
puts "Set size: #{unique_values.size}"
```

### Garbage Collection Tuning

```ruby
# Monitor GC
GC.stat

# Tune GC parameters
GC::Profiler.enable

# Run code
10000.times { |i| "String #{i}" }

# View GC profile
GC::Profiler.report
GC::Profiler.disable

# Manual GC control
GC.start  # Force garbage collection
```

## Caching Strategies

### Memoization

```ruby
# Simple memoization
class ExpensiveCalculation
  def initialize
    @cache = {}
  end
  
  def calculate(x)
    @cache[x] ||= expensive_operation(x)
  end
  
  private
  
  def expensive_operation(x)
    sleep(0.1)  # Simulate expensive work
    x * x
  end
end

# Built-in memoization
require 'memoist'

class MemoizedExample
  extend Memoist
  
  def expensive_calculation(x)
    sleep(0.1)
    x * x
  end
  
  memoize :expensive_calculation
end
```

### LRU Cache

```ruby
class LRUCache
  def initialize(max_size)
    @max_size = max_size
    @cache = {}
    @order = []
  end
  
  def get(key)
    if @cache.key?(key)
      update_order(key)
      @cache[key]
    else
      nil
    end
  end
  
  def put(key, value)
    if @cache.key?(key)
      @cache[key] = value
      update_order(key)
    else
      evict if @cache.size >= @max_size
      @cache[key] = value
      @order << key
    end
  end
  
  private
  
  def update_order(key)
    @order.delete(key)
    @order << key
  end
  
  def evict
    oldest_key = @order.shift
    @cache.delete(oldest_key)
  end
end
```

## Database Optimization

### Efficient Queries

```ruby
# N+1 query problem
class BadUserQuery
  def users_with_posts
    users = User.all
    users.each do |user|
      user.posts.each { |post| puts post.title }  # N+1 queries!
    end
  end
end

# Solution: Eager loading
class GoodUserQuery
  def users_with_posts
    users = User.includes(:posts).all
    users.each do |user|
      user.posts.each { |post| puts post.title }  # Single query!
    end
  end
end
```

### Connection Pooling

```ruby
require 'pg'

class ConnectionPool
  def initialize(size, &block)
    @size = size
    @create_proc = block
    @pool = Queue.new
    @mutex = Mutex.new
    
    # Pre-populate pool
    size.times { @pool.push(@create_proc.call) }
  end
  
  def with_connection
    conn = @pool.pop
    begin
      yield conn
    ensure
      @pool.push(conn)
    end
  end
end

# Usage
pool = ConnectionPool.new(5) { PG.connect(dbname: 'myapp') }

pool.with_connection do |conn|
  result = conn.exec("SELECT * FROM users")
  # Use connection
end
```

## I/O Optimization

### Buffered I/O

```ruby
# Slow: Line by line reading
def slow_file_read(filename)
  File.open(filename, 'r') do |file|
    while line = file.gets
      process_line(line)
    end
  end
end

# Fast: Buffered reading
def fast_file_read(filename)
  buffer = ""
  File.open(filename, 'r') do |file|
    while file.read(4096, buffer)
      buffer.each_line { |line| process_line(line) }
    end
  end
end

# Fastest: Memory mapping for large files (if available)
def mmap_file_read(filename)
  # Requires 'mmap' gem
  require 'mmap'
  
  MMap.new(filename) do |mm|
    mm.each_line { |line| process_line(line) }
  end
end
```

### Asynchronous I/O

```ruby
require 'async'
require 'async/io'

class AsyncFileProcessor
  def process_files(file_paths)
    Async do
      tasks = file_paths.map do |file_path|
        Async.async do
          File.open(file_path, 'r') do |file|
            while line = file.gets
              process_line(line)
              Async.yield  # Yield control to other tasks
            end
          end
        end
      end
      
      tasks.each(&:wait)
    end
  end
  
  private
  
  def process_line(line)
    # Process line
  end
end
```

## Code Optimization Techniques

### Method Inlining

```ruby
# Method call overhead
class Calculator
  def add(a, b)
    a + b
  end
  
  def calculate_sum(numbers)
    sum = 0
    numbers.each { |n| sum = add(sum, n) }  # Method call overhead
    sum
  end
end

# Inline for performance
class OptimizedCalculator
  def calculate_sum(numbers)
    sum = 0
    numbers.each { |n| sum += n }  # Direct operation
    sum
  end
end
```

### Avoid Object Creation

```ruby
# Bad: Creates many temporary objects
def bad_string_processing(strings)
  strings.map { |s| s.upcase.strip.gsub(/\s+/, ' ') }
end

# Good: Reuse objects where possible
def good_string_processing(strings)
  result = []
  strings.each do |s|
    processed = s.dup
    processed.upcase!
    processed.strip!
    processed.gsub!(/\s+/, ' ')
    result << processed
  end
  result
end
```

### Use Built-in Methods

```ruby
# Slow: Custom implementation
def custom_reverse(array)
  result = []
  (array.length - 1).downto(0) { |i| result << array[i] }
  result
end

# Fast: Built-in method
def builtin_reverse(array)
  array.reverse
end
```

## Performance Monitoring

### Custom Metrics

```ruby
class PerformanceMonitor
  def initialize
    @metrics = {}
    @mutex = Mutex.new
  end
  
  def time(operation)
    start_time = Time.now
    result = yield
    duration = Time.now - start_time
    
    @mutex.synchronize do
      @metrics[operation] ||= []
      @metrics[operation] << duration
    end
    
    result
  end
  
  def stats(operation)
    @mutex.synchronize do
      times = @metrics[operation] || []
      return nil if times.empty?
      
      {
        count: times.size,
        total: times.sum,
        average: times.sum / times.size,
        min: times.min,
        max: times.max
      }
    end
  end
end

# Usage
monitor = PerformanceMonitor.new

1000.times do
  monitor.time(:calculation) do
    expensive_operation
  end
end

puts monitor.stats(:calculation)
```

### Memory Monitoring

```ruby
class MemoryMonitor
  def self.track_memory
    before = GC.stat
    before_objects = ObjectSpace.count_objects
    
    yield
    
    GC.start
    after = GC.stat
    after_objects = ObjectSpace.count_objects
    
    puts "GC runs: #{after[:count] - before[:count]}"
    puts "Objects created: #{after_objects[:TOTAL] - before_objects[:TOTAL]}"
  end
end

# Usage
MemoryMonitor.track_memory do
  10000.times { |i| "String #{i}" }
end
```

## Best Practices

### 1. Profile First, Optimize Second

```ruby
# Don't optimize without profiling
# Use benchmark tools to identify bottlenecks
# Focus on the 80/20 rule
```

### 2. Choose Right Data Structures

```ruby
# Use Hash for O(1) lookups
# Use Set for unique elements
# Use Array for ordered collections
# Consider specialized gems for large datasets
```

### 3. Minimize Object Allocation

```ruby
# Reuse objects when possible
# Use object pools for expensive objects
# Avoid unnecessary string creation
# Consider symbols for frequently used strings
```

### 4. Optimize Database Access

```ruby
# Use connection pooling
# Implement caching
# Avoid N+1 queries
# Use database indexes effectively
```

## Practice Exercises

### Exercise 1: Performance Analyzer
Create a tool that:
- Profiles Ruby code execution
- Identifies bottlenecks
- Provides optimization suggestions
- Generates performance reports

### Exercise 2: Cache Implementation
Build a caching system that:
- Supports multiple cache strategies (LRU, FIFO, TTL)
- Handles cache invalidation
- Provides cache statistics
- Thread-safe implementation

### Exercise 3: Data Structure Comparison
Implement a comparison tool that:
- Benchmarks different data structures
- Measures memory usage
- Provides performance recommendations
- Visualizes results

### Exercise 4: Memory Optimizer
Create a memory optimization tool that:
- Tracks object creation
- Identifies memory leaks
- Suggests optimizations
- Provides memory usage reports

---

**Ready to explore testing strategies in Ruby? Let's continue! 🧪**
