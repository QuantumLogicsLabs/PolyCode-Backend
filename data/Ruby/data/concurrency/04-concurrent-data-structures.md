# Concurrent Data Structures in Ruby
# Comprehensive guide to thread-safe data structures and algorithms

## 🎯 Overview

Concurrent data structures are designed for safe access from multiple threads without explicit synchronization. This guide covers various concurrent data structures, their implementations, and use cases in Ruby.

## 🔒 Thread-Safe Collections

### 1. Thread-Safe Array

```ruby
require 'thread'

class ThreadSafeArray
  def initialize
    @array = []
    @mutex = Mutex.new
  end
  
  def <<(item)
    @mutex.synchronize { @array << item }
  end
  
  def [](index)
    @mutex.synchronize { @array[index] }
  end
  
  def []=(index, value)
    @mutex.synchronize { @array[index] = value }
  end
  
  def size
    @mutex.synchronize { @array.size }
  end
  
  def empty?
    @mutex.synchronize { @array.empty? }
  end
  
  def first
    @mutex.synchronize { @array.first }
  end
  
  def last
    @mutex.synchronize { @array.last }
  end
  
  def pop
    @mutex.synchronize { @array.pop }
  end
  
  def shift
    @mutex.synchronize { @array.shift }
  end
  
  def each(&block)
    @mutex.synchronize { @array.dup.each(&block) }
  end
  
  def map(&block)
    @mutex.synchronize { @array.dup.map(&block) }
  end
  
  def select(&block)
    @mutex.synchronize { @array.dup.select(&block) }
  end
  
  def reject(&block)
    @mutex.synchronize { @array.dup.reject(&block) }
  end
  
  def include?(item)
    @mutex.synchronize { @array.include?(item) }
  end
  
  def clear
    @mutex.synchronize { @array.clear }
  end
  
  def to_a
    @mutex.synchronize { @array.dup }
  end
end

# Usage
safe_array = ThreadSafeArray.new
threads = 5.times.map do |i|
  Thread.new do
    10.times { |j| safe_array << "Item #{i}-#{j}" }
  end
end

threads.each(&:join)
puts "Array size: #{safe_array.size}"
puts "First item: #{safe_array.first}"
puts "Last item: #{safe_array.last}"
```

### 2. Thread-Safe Hash

```ruby
class ThreadSafeHash
  def initialize
    @hash = {}
    @mutex = Mutex.new
  end
  
  def [](key)
    @mutex.synchronize { @hash[key] }
  end
  
  def []=(key, value)
    @mutex.synchronize { @hash[key] = value }
  end
  
  def key?(key)
    @mutex.synchronize { @hash.key?(key) }
  end
  
  def delete(key)
    @mutex.synchronize { @hash.delete(key) }
  end
  
  def keys
    @mutex.synchronize { @hash.keys }
  end
  
  def values
    @mutex.synchronize { @hash.values }
  end
  
  def each(&block)
    @mutex.synchronize { @hash.dup.each(&block) }
  end
  
  def each_key(&block)
    @mutex.synchronize { @hash.keys.each(&block) }
  end
  
  def each_value(&block)
    @mutex.synchronize { @hash.values.each(&block) }
  end
  
  def map(&block)
    @mutex.synchronize { @hash.dup.map(&block) }
  end
  
  def select(&block)
    @mutex.synchronize { @hash.dup.select(&block) }
  end
  
  def reject(&block)
    @mutex.synchronize { @hash.dup.reject(&block) }
  end
  
  def merge(other_hash)
    @mutex.synchronize do
      @hash = @hash.merge(other_hash)
    end
  end
  
  def update(&block)
    @mutex.synchronize { @hash.update(&block) }
  end
  
  def clear
    @mutex.synchronize { @hash.clear }
  end
  
  def size
    @mutex.synchronize { @hash.size }
  end
  
  def empty?
    @mutex.synchronize { @hash.empty? }
  end
  
  def to_h
    @mutex.synchronize { @hash.dup }
  end
end

# Usage
safe_hash = ThreadSafeHash.new
threads = 5.times.map do |i|
  Thread.new do
    10.times { |j| safe_hash["key_#{i}_#{j}"] = "value_#{i}_#{j}" }
  end
end

threads.each(&:join)
puts "Hash size: #{safe_hash.size}"
puts "Keys: #{safe_hash.keys.first(5)}"
```

### 3. Thread-Safe Queue

```ruby
class ThreadSafeQueue
  def initialize
    @queue = []
    @mutex = Mutex.new
    @not_empty = ConditionVariable.new
  end
  
  def push(item)
    @mutex.synchronize do
      @queue << item
      @not_empty.signal
    end
  end
  
  def pop(non_blocking = false)
    @mutex.synchronize do
      if @queue.empty?
        if non_blocking
          raise ThreadError, "queue empty"
        else
          @not_empty.wait(@mutex)
        end
      end
      
      @queue.shift
    end
  end
  
  def empty?
    @mutex.synchronize { @queue.empty? }
  end
  
  def size
    @mutex.synchronize { @queue.size }
  end
  
  def clear
    @mutex.synchronize { @queue.clear }
  end
end

# Producer-Consumer example
queue = ThreadSafeQueue.new

producers = 3.times.map do |i|
  Thread.new do
    10.times do |j|
      item = "Producer #{i} - Item #{j}"
      queue.push(item)
      puts "Produced: #{item}"
      sleep(0.1)
    end
  end
end

consumers = 2.times.map do |i|
  Thread.new do
    loop do
      begin
        item = queue.pop
        puts "Consumer #{i} consumed: #{item}"
        sleep(0.2)
      rescue ThreadError
        break
      end
    end
  end
end

producers.each(&:join)
sleep(2)  # Let consumers finish
```

## 🚀 Lock-Free Data Structures

### 1. Lock-Free Stack

```ruby
require 'concurrent-ruby'

class LockFreeStack
  def initialize
    @head = Concurrent::AtomicReference.new(nil)
  end
  
  def push(item)
    loop do
      current_head = @head.value
      new_node = Node.new(item, current_head)
      
      if @head.compare_and_set(current_head, new_node)
        break
      end
    end
  end
  
  def pop
    loop do
      current_head = @head.value
      return nil unless current_head
      
      new_head = current_head.next
      
      if @head.compare_and_set(current_head, new_head)
        return current_head.value
      end
    end
  end
  
  def empty?
    @head.value.nil?
  end
  
  private
  
  class Node
    attr_reader :value, :next
    
    def initialize(value, next_node)
      @value = value
      @next = next_node
    end
  end
end

# Usage
stack = LockFreeStack.new
threads = 5.times.map do |i|
  Thread.new do
    10.times do |j|
      stack.push("Item #{i}-#{j}")
      popped = stack.pop
      puts "Thread #{i} pushed and popped: #{popped}"
    end
  end
end

threads.each(&:join)
```

### 2. Lock-Free Queue

```ruby
class LockFreeQueue
  def initialize
    @head = Concurrent::AtomicReference.new(Node.new(nil, nil))
    @tail = Concurrent::AtomicReference.new(@head.value)
  end
  
  def enqueue(item)
    new_node = Node.new(item, nil)
    
    loop do
      current_tail = @tail.value
      current_tail_next = current_tail.next
      
      if current_tail_next.nil?
        if current_tail.compare_and_set_next(nil, new_node)
          @tail.compare_and_set(current_tail, new_node)
          break
        end
      else
        @tail.compare_and_set(current_tail, current_tail_next)
      end
    end
  end
  
  def dequeue
    loop do
      current_head = @head.value
      current_tail = @tail.value
      current_head_next = current_head.next
      
      if current_head == current_tail
        return nil if current_head_next.nil?
        @tail.compare_and_set(current_tail, current_head_next)
      else
        value = current_head_next.value
        if @head.compare_and_set(current_head, current_head_next)
          return value
        end
      end
    end
  end
  
  def empty?
    @head.value == @tail.value
  end
  
  private
  
  class Node
    extend Forwardable
    
    def initialize(value, next_node)
      @value = Concurrent::AtomicReference.new(value)
      @next = Concurrent::AtomicReference.new(next_node)
    end
    
    def_delegators :@value, :value, :value=
    def_delegators :@next, :next, :next=
    
    def compare_and_set_next(expected, new_value)
      @next.compare_and_set(expected, new_value)
    end
  end
end
```

## 🔄 Concurrent Collections

### 1. Concurrent Set

```ruby
class ConcurrentSet
  def initialize
    @hash = Concurrent::Hash.new
  end
  
  def add(item)
    @hash[item] = true
    self
  end
  
  def add?(item)
    exists = @hash.key?(item)
    @hash[item] = true unless exists
    !exists
  end
  
  def delete(item)
    @hash.delete(item)
    self
  end
  
  def delete?(item)
    @hash.key?(item) && @hash.delete(item)
  end
  
  def include?(item)
    @hash.key?(item)
  end
  
  def each(&block)
    @hash.keys.each(&block)
  end
  
  def to_a
    @hash.keys
  end
  
  def size
    @hash.size
  end
  
  def empty?
    @hash.empty?
  end
  
  def clear
    @hash.clear
  end
  
  def merge(other_set)
    result = self.class.new
    result.merge!(self)
    result.merge!(other_set)
    result
  end
  
  def merge!(other_set)
    other_set.each { |item| add(item) }
    self
  end
end

# Usage
set = ConcurrentSet.new
threads = 5.times.map do |i|
  Thread.new do
    10.times { |j| set.add("Item #{i}-#{j}") }
  end
end

threads.each(&:join)
puts "Set size: #{set.size}"
puts "Contains 'Item 0-0': #{set.include?('Item 0-0')}"
```

### 2. Concurrent Cache

```ruby
class ConcurrentCache
  def initialize(max_size = 1000)
    @max_size = max_size
    @cache = Concurrent::Hash.new
    @access_times = Concurrent::Hash.new
    @mutex = Mutex.new
  end
  
  def get(key)
    value = @cache[key]
    if value
      @access_times[key] = Time.now
    end
    value
  end
  
  def put(key, value)
    @cache[key] = value
    @access_times[key] = Time.now
    
    evict_if_needed
  end
  
  def put_if_absent(key, value)
    existing = @cache[key]
    return existing if existing
    
    @cache[key] = value
    @access_times[key] = Time.now
    evict_if_needed
    value
  end
  
  def remove(key)
    value = @cache.delete(key)
    @access_times.delete(key)
    value
  end
  
  def size
    @cache.size
  end
  
  def clear
    @cache.clear
    @access_times.clear
  end
  
  def keys
    @cache.keys
  end
  
  def each(&block)
    @cache.each(&block)
  end
  
  private
  
  def evict_if_needed
    return if @cache.size <= @max_size
    
    @mutex.synchronize do
      return if @cache.size <= @max_size
      
      # LRU eviction
      oldest_key = @access_times.min_by { |_, time| time }.first
      @cache.delete(oldest_key)
      @access_times.delete(oldest_key)
    end
  end
end

# Usage
cache = ConcurrentCache.new(5)

threads = 10.times.map do |i|
  Thread.new do
    5.times do |j|
      key = "key_#{j}"
      cache.put(key, "value_#{i}_#{j}")
      value = cache.get(key)
      puts "Thread #{i} got #{key}: #{value}"
    end
  end
end

threads.each(&:join)
puts "Cache size: #{cache.size}"
```

## 🎯 Specialized Concurrent Structures

### 1. Concurrent Counter

```ruby
class ConcurrentCounter
  def initialize(initial_value = 0)
    @counter = Concurrent::AtomicFixnum.new(initial_value)
  end
  
  def increment
    @counter.increment
  end
  
  def decrement
    @counter.decrement
  end
  
  def add(value)
    @counter.update { |current| current + value }
  end
  
  def subtract(value)
    @counter.update { |current| current - value }
  end
  
  def value
    @counter.value
  end
  
  def reset(new_value = 0)
    @counter.set(new_value)
  end
end

# Usage
counter = ConcurrentCounter.new
threads = 10.times.map do
  Thread.new do
    1000.times { counter.increment }
  end
end

threads.each(&:join)
puts "Final counter value: #{counter.value}"
```

### 2. Concurrent Bitmap

```ruby
class ConcurrentBitmap
  def initialize(size = 1024)
    @size = size
    @bits = Concurrent::Array.new(size / 64 + 1) { 0 }
  end
  
  def set(index)
    word_index = index / 64
    bit_index = index % 64
    
    loop do
      current_word = @bits[word_index]
      new_word = current_word | (1 << bit_index)
      
      if @bits.compare_and_set(word_index, current_word, new_word)
        break
      end
    end
  end
  
  def clear(index)
    word_index = index / 64
    bit_index = index % 64
    
    loop do
      current_word = @bits[word_index]
      new_word = current_word & ~(1 << bit_index)
      
      if @bits.compare_and_set(word_index, current_word, new_word)
        break
      end
    end
  end
  
  def set?(index)
    word_index = index / 64
    bit_index = index % 64
    
    (@bits[word_index] & (1 << bit_index)) != 0
  end
  
  def flip(index)
    if set?(index)
      clear(index)
    else
      set(index)
    end
  end
  
  def count_set_bits
    @bits.reduce(0) { |count, word| count + word.to_s(2).count('1') }
  end
  
  def clear_all
    @size.times { |i| clear(i) }
  end
  
  def set_all
    @size.times { |i| set(i) }
  end
end

# Usage
bitmap = ConcurrentBitmap.new(1000)
threads = 10.times.map do |i|
  Thread.new do
    100.times do |j|
      index = i * 100 + j
      bitmap.set(index)
    end
  end
end

threads.each(&:join)
puts "Bits set: #{bitmap.count_set_bits}"
```

### 3. Concurrent Ring Buffer

```ruby
class ConcurrentRingBuffer
  def initialize(size)
    @size = size
    @buffer = Concurrent::Array.new(size)
    @head = Concurrent::AtomicFixnum.new(0)
    @tail = Concurrent::AtomicFixnum.new(0)
  end
  
  def push(item)
    loop do
      current_head = @head.value
      current_tail = @tail.value
      next_head = (current_head + 1) % @size
      
      # Check if buffer is full
      if next_head == current_tail
        raise BufferFullError, "Ring buffer is full"
      end
      
      if @head.compare_and_set(current_head, next_head)
        @buffer[current_head] = item
        break
      end
    end
  end
  
  def pop
    loop do
      current_head = @head.value
      current_tail = @tail.value
      
      # Check if buffer is empty
      if current_head == current_tail
        raise BufferEmptyError, "Ring buffer is empty"
      end
      
      next_tail = (current_tail + 1) % @size
      item = @buffer[current_tail]
      
      if @tail.compare_and_set(current_tail, next_tail)
        return item
      end
    end
  end
  
  def size
    head = @head.value
    tail = @tail.value
    
    if head >= tail
      head - tail
    else
      @size - (tail - head)
    end
  end
  
  def empty?
    @head.value == @tail.value
  end
  
  def full?
    (@head.value + 1) % @size == @tail.value
  end
  
  class BufferFullError < StandardError; end
  class BufferEmptyError < StandardError; end
end

# Usage
buffer = ConcurrentRingBuffer.new(10)

producer = Thread.new do
  20.times do |i|
    begin
      buffer.push("Item #{i}")
      puts "Produced: Item #{i}"
      sleep(0.1)
    rescue BufferFullError
      puts "Buffer full, waiting..."
      sleep(0.2)
      retry
    end
  end
end

consumer = Thread.new do
  20.times do
    begin
      item = buffer.pop
      puts "Consumed: #{item}"
      sleep(0.15)
    rescue BufferEmptyError
      puts "Buffer empty, waiting..."
      sleep(0.1)
      retry
    end
  end
end

[producer, consumer].each(&:join)
```

## 🎯 Performance Considerations

### 1. Benchmarking Concurrent Structures

```ruby
require 'benchmark'

def benchmark_concurrent_structures
  # Thread-safe array vs regular array with mutex
  safe_array = ThreadSafeArray.new
  regular_array = []
  regular_mutex = Mutex.new
  
  Benchmark.bm(20) do |x|
    x.report("ThreadSafeArray") do
      threads = 10.times.map do |i|
        Thread.new do
          1000.times { |j| safe_array << "item_#{i}_#{j}" }
        end
      end
      threads.each(&:join)
    end
    
    x.report("Array + Mutex") do
      threads = 10.times.map do |i|
        Thread.new do
          1000.times do |j|
            regular_mutex.synchronize do
              regular_array << "item_#{i}_#{j}"
            end
          end
        end
      end
      threads.each(&:join)
    end
  end
end

# Usage
benchmark_concurrent_structures
```

### 2. Memory Usage Analysis

```ruby
def analyze_memory_usage
  require 'objspace'
  
  # Compare memory usage
  GC.start
  before_objects = ObjectSpace.count_objects
  
  # Create concurrent structures
  safe_array = ThreadSafeArray.new
  safe_hash = ThreadSafeHash.new
  concurrent_set = ConcurrentSet.new
  
  10000.times { |i| safe_array << "item_#{i}" }
  10000.times { |i| safe_hash["key_#{i}"] = "value_#{i}" }
  10000.times { |i| concurrent_set.add("item_#{i}") }
  
  GC.start
  after_objects = ObjectSpace.count_objects
  
  allocated = after_objects - before_objects
  puts "Objects allocated: #{allocated}"
  
  # Break down by type
  allocated.each do |type, count|
    next if count <= 0
    puts "  #{type}: #{count}"
  end
end

# Usage
analyze_memory_usage
```

## 🎯 Best Practices

### 1. Choose the Right Structure

```ruby
# For high contention: Use lock-free structures
high_contention_queue = LockFreeQueue.new

# For general purpose: Use thread-safe with mutex
general_purpose_array = ThreadSafeArray.new

# For caching: Use concurrent cache
cache = ConcurrentCache.new(1000)

# For simple counters: Use atomic operations
counter = ConcurrentCounter.new
```

### 2. Minimize Contention

```ruby
# Bad: Single global lock
class BadGlobalLock
  def initialize
    @data = {}
    @mutex = Mutex.new
  end
  
  def get(key)
    @mutex.synchronize { @data[key] }
  end
end

# Good: Sharded locks
class GoodShardedLocks
  def initialize(shards: 16)
    @shards = shards.times.map { {} }
    @locks = shards.times.map { Mutex.new }
  end
  
  def get(key)
    shard_index = key.hash % @shards.size
    @locks[shard_index].synchronize { @shards[shard_index][key] }
  end
end
```

### 3. Handle Memory Leaks

```ruby
class MemoryEfficientConcurrentStructure
  def initialize(max_size: 1000)
    @max_size = max_size
    @data = Concurrent::Hash.new
    @access_times = Concurrent::Hash.new
  end
  
  def put(key, value)
    @data[key] = value
    @access_times[key] = Time.now
    
    # Periodic cleanup
    cleanup_if_needed
  end
  
  private
  
  def cleanup_if_needed
    return if @data.size <= @max_size
    
    # Remove least recently used items
    threshold = Time.now - 3600  # 1 hour
    @access_times.each do |key, time|
      if time < threshold
        @data.delete(key)
        @access_times.delete(key)
      end
    end
    
    # If still too large, remove by LRU
    while @data.size > @max_size
      oldest_key = @access_times.min_by { |_, time| time }.first
      @data.delete(oldest_key)
      @access_times.delete(oldest_key)
    end
  end
end
```

## 🚨 Common Pitfalls

### 1. False Sharing

```ruby
# Bad: Contended variables in same cache line
class BadFalseSharing
  def initialize
    @counter1 = Concurrent::AtomicFixnum.new(0)
    @counter2 = Concurrent::AtomicFixnum.new(0)
  end
  
  def increment1
    @counter1.increment
  end
  
  def increment2
    @counter2.increment
  end
end

# Good: Separate contended variables
class GoodNoFalseSharing
  def initialize
    # Pad to separate cache lines (64 bytes)
    @padding1 = Array.new(8, 0)
    @counter1 = Concurrent::AtomicFixnum.new(0)
    @padding2 = Array.new(8, 0)
    @counter2 = Concurrent::AtomicFixnum.new(0)
  end
end
```

### 2. ABA Problem

```ruby
# ABA problem can occur with lock-free structures
# Solution: Use version numbers or hazard pointers

class VersionedReference
  def initialize
    @reference = Concurrent::AtomicReference.new([nil, 0])
  end
  
  def get
    @reference.value
  end
  
  def compare_and_set(expected, new_value)
    current_value, current_version = expected
    new_version = current_version + 1
    @reference.compare_and_set(expected, [new_value, new_version])
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Thread-Safe Array**: Implement a thread-safe array from scratch
2. **Concurrent Queue**: Build a producer-consumer queue
3. **Atomic Counter**: Create a thread-safe counter

### Intermediate Exercises

1. **Lock-Free Stack**: Implement a lock-free stack
2. **Concurrent Cache**: Build an LRU cache with concurrency
3. **Ring Buffer**: Create a concurrent ring buffer

### Advanced Exercises

1. **Sharded Hash**: Implement a sharded hash for reduced contention
2. **Concurrent B-Tree**: Build a concurrent B-tree structure
3. **Memory Pool**: Create a concurrent memory pool allocator

---

## 🎯 Summary

Concurrent data structures in Ruby provide:

- **Thread-safe collections** - Safe access from multiple threads
- **Lock-free structures** - High-performance atomic operations
- **Specialized structures** - Optimized for specific use cases
- **Memory efficiency** - Reduced overhead and contention
- **Scalability** - Better performance under high concurrency

Master concurrent data structures to build thread-safe, high-performance Ruby applications!
