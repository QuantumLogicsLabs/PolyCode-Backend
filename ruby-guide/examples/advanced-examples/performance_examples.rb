# Performance Optimization Examples
# Demonstrating profiling, optimization techniques, and performance monitoring

puts "=== BENCHMARKING BASICS ==="

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

puts "\n=== DATA STRUCTURE PERFORMANCE ==="

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

puts "\n=== STRING OPTIMIZATION ==="

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

puts "\n=== LOOP OPTIMIZATION ==="

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

puts "\n=== MEMORY PROFILING ==="

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

puts "\n=== OBJECT POOLING ==="

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
  end
end

puts "Object pooling completed"

puts "\n=== MEMOIZATION ==="

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
    sleep(0.001)  # Simulate expensive work
    x * x
  end
end

# Built-in memoization with memoist gem (simulated here)
class MemoizedExample
  def initialize
    @cache = {}
  end
  
  def expensive_calculation(x)
    return @cache[x] if @cache[x]
    
    result = sleep(0.001) && x * x
    @cache[x] = result
    result
  end
end

# Test memoization performance
calc = ExpensiveCalculation.new
memo_calc = MemoizedExample.new

Benchmark.bm(15) do |x|
  x.report("Without memo:") do
    100.times { calc.calculate(rand(10)) }
  end
  
  x.report("With memo:") do
    100.times { memo_calc.expensive_calculation(rand(10)) }
  end
end

puts "\n=== LRU CACHE ==="

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

# Test LRU cache
cache = LRUCache.new(3)
cache.put(1, "one")
cache.put(2, "two")
cache.put(3, "three")
cache.get(1)  # Access 1, makes it most recently used
cache.put(4, "four")  # Should evict 2 (least recently used)

puts "Cache after operations:"
puts "1: #{cache.get(1)}"
puts "2: #{cache.get(2)}"
puts "3: #{cache.get(3)}"
puts "4: #{cache.get(4)}"

puts "\n=== METHOD OPTIMIZATION ==="

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

calc = Calculator.new
opt_calc = OptimizedCalculator.new

numbers = (1..10000).to_a

Benchmark.bm(15) do |x|
  x.report("Method calls:") do
    calc.calculate_sum(numbers)
  end
  
  x.report("Inline ops:") do
    opt_calc.calculate_sum(numbers)
  end
end

puts "\n=== OBJECT CREATION OPTIMIZATION ==="

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

test_strings = ["  hello world  ", "  ruby programming  ", "  performance testing  "]

Benchmark.bm(15) do |x|
  x.report("Many objects:") do
    1000.times { bad_string_processing(test_strings) }
  end
  
  x.report("Reuse objects:") do
    1000.times { good_string_processing(test_strings) }
  end
end

puts "\n=== BUILT-IN METHODS OPTIMIZATION ==="

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

test_array = (1..10000).to_a

Benchmark.bm(15) do |x|
  x.report("Custom reverse:") do
    100.times { custom_reverse(test_array) }
  end
  
  x.report("Built-in reverse:") do
    100.times { builtin_reverse(test_array) }
  end
end

puts "\n=== PERFORMANCE MONITORING ==="

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

monitor = PerformanceMonitor.new

1000.times do |i|
  monitor.time(:calculation) do
    Math.sqrt(i) * Math.sin(i)
  end
end

stats = monitor.stats(:calculation)
puts "Performance stats:"
puts "Count: #{stats[:count]}"
puts "Average: #{stats[:average].round(6)}s"
puts "Min: #{stats[:min].round(6)}s"
puts "Max: #{stats[:max].round(6)}s"

puts "\n=== GARBAGE COLLECTION TUNING ==="

require 'benchmark'

# Monitor GC
puts "GC stats before:"
puts GC.stat

# Test with different GC settings
GC::Profiler.enable

# Run some code that creates objects
10000.times { |i| "String #{i}" }

# View GC profile
puts "\nGC runs: #{GC.stat[:count]}"
puts "Objects created: #{ObjectSpace.count_objects[:TOTAL]}"

GC::Profiler.report
GC::Profiler.disable

puts "\n=== ALGORITHM OPTIMIZATION ==="

# O(n²) algorithm
def slow_find_duplicates(array)
  duplicates = []
  array.each_with_index do |item1, i|
    array.each_with_index do |item2, j|
      next if i == j
      duplicates << item1 if item1 == item2 && !duplicates.include?(item1)
    end
  end
  duplicates
end

# O(n) algorithm
def fast_find_duplicates(array)
  seen = {}
  duplicates = []
  
  array.each do |item|
    if seen[item]
      duplicates << item unless duplicates.include?(item)
    else
      seen[item] = true
    end
  end
  
  duplicates
end

# Create test data with duplicates
test_array = []
1000.times { test_array << rand(100) }
100.times { test_array << 42 }  # Add many duplicates

Benchmark.bm(15) do |x|
  x.report("O(n²) algorithm:") do
    slow_find_duplicates(test_array)
  end
  
  x.report("O(n) algorithm:") do
    fast_find_duplicates(test_array)
  end
end

puts "\n=== REGEX OPTIMIZATION ==="

# Slow: Complex regex
def slow_email_validation(emails)
  complex_regex = /\A[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}\z/
  emails.select { |email| email.match?(complex_regex) }
end

# Fast: Simple regex with pre-compilation
def fast_email_validation(emails)
  simple_regex = /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
  emails.select { |email| email.match?(simple_regex) }
end

test_emails = [
  "user@example.com",
  "user.name@domain.co.uk",
  "user+tag@example.org",
  "invalid-email",
  "user@",
  "@example.com"
] * 1000

Benchmark.bm(15) do |x|
  x.report("Complex regex:") do
    slow_email_validation(test_emails)
  end
  
  x.report("Simple regex:") do
    fast_email_validation(test_emails)
  end
end

puts "\n=== PERFORMANCE SUMMARY ==="
puts "- Benchmarking with Benchmark module"
puts "- Data structure optimization (Hash vs Array)"
puts "- String concatenation optimization"
puts "- Loop optimization techniques"
puts "- Memory profiling and object pooling"
puts "- Memoization and caching strategies"
puts "- Method inlining for performance"
puts "- Object creation optimization"
puts "- Built-in method advantages"
puts "- Performance monitoring and metrics"
puts "- Garbage collection tuning"
puts "- Algorithm complexity optimization"
puts "- Regular expression optimization"
puts "\nAll examples demonstrate Ruby performance optimization techniques!"
