# Thread Synchronization in Ruby
# Comprehensive guide to synchronizing concurrent operations

## 🎯 Overview

Thread synchronization is crucial for preventing race conditions, ensuring data consistency, and coordinating concurrent operations in Ruby. This guide covers various synchronization mechanisms and best practices.

## 🔒 Basic Synchronization Primitives

### 1. Mutex (Mutual Exclusion)

Mutex ensures only one thread can execute a critical section at a time:

```ruby
require 'thread'

class Counter
  def initialize
    @count = 0
    @mutex = Mutex.new
  end
  
  def increment
    @mutex.synchronize do
      @count += 1
      puts "Count incremented to #{@count} by #{Thread.current.object_id}"
    end
  end
  
  def count
    @mutex.synchronize { @count }
  end
end

# Usage
counter = Counter.new
threads = []

5.times do |i|
  thread = Thread.new do
    10.times { counter.increment }
  end
  threads << thread
end

threads.each(&:join)
puts "Final count: #{counter.count}"
```

### 2. Condition Variables

Condition variables allow threads to wait for specific conditions:

```ruby
class Buffer
  def initialize(size)
    @buffer = []
    @size = size
    @mutex = Mutex.new
    @not_full = ConditionVariable.new
    @not_empty = ConditionVariable.new
  end
  
  def produce(item)
    @mutex.synchronize do
      @not_full.wait(@mutex) while @buffer.size >= @size
      @buffer << item
      puts "Produced: #{item}, Buffer size: #{@buffer.size}"
      @not_empty.signal
    end
  end
  
  def consume
    @mutex.synchronize do
      @not_empty.wait(@mutex) while @buffer.empty?
      item = @buffer.shift
      puts "Consumed: #{item}, Buffer size: #{@buffer.size}"
      @not_full.signal
      item
    end
  end
end

# Producer-Consumer example
buffer = Buffer.new(5)

producer = Thread.new do
  10.times do |i|
    buffer.produce("Item #{i}")
    sleep(0.1)
  end
end

consumer = Thread.new do
  10.times do
    buffer.consume
    sleep(0.2)
  end
end

[producer, consumer].each(&:join)
```

### 3. Queue

Thread-safe queue for producer-consumer patterns:

```ruby
require 'thread'

class WorkerPool
  def initialize(size)
    @size = size
    @queue = Queue.new
    @workers = []
    @shutdown = false
    
    @size.times do |i|
      @workers << Thread.new { worker_loop(i) }
    end
  end
  
  def submit(&block)
    raise "Pool is shutdown" if @shutdown
    @queue.push(block)
  end
  
  def shutdown
    @shutdown = true
    @size.times { @queue.push(nil) }
    @workers.each(&:join)
  end
  
  private
  
  def worker_loop(worker_id)
    loop do
      task = @queue.pop
      break if @shutdown && task.nil?
      
      begin
        puts "Worker #{worker_id} executing task"
        task.call
      rescue => e
        puts "Worker #{worker_id} error: #{e.message}"
      end
    end
  end
end

# Usage
pool = WorkerPool.new(3)

10.times do |i|
  pool.submit do
    puts "Task #{i} running"
    sleep(0.1)
    puts "Task #{i} completed"
  end
end

sleep(2)
pool.shutdown
```

## 🔄 Advanced Synchronization Patterns

### 1. Read-Write Locks

Allow multiple readers but exclusive writer access:

```ruby
class ReadWriteLock
  def initialize
    @read_count = 0
    @writing = false
    @mutex = Mutex.new
    @read_ready = ConditionVariable.new
    @write_ready = ConditionVariable.new
  end
  
  def with_read_lock
    @mutex.synchronize do
      @read_ready.wait(@mutex) while @writing
      @read_count += 1
    end
    
    yield
  ensure
    @mutex.synchronize do
      @read_count -= 1
      @write_ready.signal if @read_count == 0
    end
  end
  
  def with_write_lock
    @mutex.synchronize do
      @write_ready.wait(@mutex) while @read_count > 0 || @writing
      @writing = true
    end
    
    yield
  ensure
    @mutex.synchronize do
      @writing = false
      @read_ready.broadcast
    end
  end
end

# Usage
rw_lock = ReadWriteLock.new
shared_data = []

# Multiple readers
readers = 5.times.map do |i|
  Thread.new do
    rw_lock.with_read_lock do
      puts "Reader #{i} reading: #{shared_data}"
      sleep(0.1)
    end
  end
end

# Single writer
writer = Thread.new do
  rw_lock.with_write_lock do
    puts "Writer updating data"
    shared_data << "New item #{Time.now}"
    sleep(0.5)
  end
end

readers.each(&:join)
writer.join
```

### 2. Barrier Synchronization

Wait for multiple threads to reach a point:

```ruby
class Barrier
  def initialize(parties)
    @parties = parties
    @waiting = 0
    @mutex = Mutex.new
    @condition = ConditionVariable.new
  end
  
  def wait
    @mutex.synchronize do
      @waiting += 1
      
      if @waiting == @parties
        @condition.broadcast
      else
        @condition.wait(@mutex) while @waiting < @parties
      end
    end
  end
end

# Usage
barrier = Barrier.new(3)
threads = []

3.times do |i|
  thread = Thread.new do
    puts "Thread #{i} starting work"
    sleep(rand(0.1..0.5))
    puts "Thread #{i} reached barrier"
    barrier.wait
    puts "Thread #{i} passed barrier"
  end
  threads << thread
end

threads.each(&:join)
```

### 3. Semaphore

Control access to limited resources:

```ruby
class Semaphore
  def initialize(count)
    @count = count
    @mutex = Mutex.new
    @condition = ConditionVariable.new
  end
  
  def acquire
    @mutex.synchronize do
      @condition.wait(@mutex) while @count == 0
      @count -= 1
    end
  end
  
  def release
    @mutex.synchronize do
      @count += 1
      @condition.signal
    end
  end
  
  def with_permit
    acquire
    yield
  ensure
    release
  end
end

# Usage: Limit concurrent database connections
db_semaphore = Semaphore.new(3)

threads = 10.times.map do |i|
  Thread.new do
    db_semaphore.with_permit do
      puts "Thread #{i} using database connection"
      sleep(1)
      puts "Thread #{i} released database connection"
    end
  end
end

threads.each(&:join)
```

## 🚀 Atomic Operations

### 1. Atomic Counters

Thread-safe counter without explicit locking:

```ruby
require 'concurrent-ruby'

class AtomicCounter
  def initialize
    @counter = Concurrent::AtomicFixnum.new(0)
  end
  
  def increment
    @counter.increment
  end
  
  def decrement
    @counter.decrement
  end
  
  def value
    @counter.value
  end
  
  def update(new_value)
    @counter.set(new_value)
  end
end

# Usage
counter = AtomicCounter.new
threads = 10.times.map do
  Thread.new do
    1000.times { counter.increment }
  end
end

threads.each(&:join)
puts "Final counter value: #{counter.value}"
```

### 2. Atomic References

Thread-safe reference updates:

```ruby
class AtomicReference
  def initialize(initial_value = nil)
    @reference = Concurrent::AtomicReference.new(initial_value)
  end
  
  def get
    @reference.value
  end
  
  def set(new_value)
    @reference.set(new_value)
  end
  
  def compare_and_set(expected, new_value)
    @reference.compare_and_set(expected, new_value)
  end
  
  def update(&block)
    @reference.update(&block)
  end
end

# Usage
config = AtomicReference.new({ timeout: 30 })

# Thread-safe configuration update
config.update do |current|
  current.merge(retries: 3)
end

puts "Updated config: #{config.get}"
```

## 🔄 Thread-Safe Data Structures

### 1. Thread-Safe Array

```ruby
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
  
  def each(&block)
    @mutex.synchronize { @array.dup.each(&block) }
  end
  
  def size
    @mutex.synchronize { @array.size }
  end
  
  def empty?
    @mutex.synchronize { @array.empty? }
  end
  
  def clear
    @mutex.synchronize { @array.clear }
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
  
  def each(&block)
    @mutex.synchronize { @hash.dup.each(&block) }
  end
  
  def keys
    @mutex.synchronize { @hash.keys }
  end
  
  def values
    @mutex.synchronize { @hash.values }
  end
  
  def clear
    @mutex.synchronize { @hash.clear }
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
puts "Hash size: #{safe_hash.keys.size}"
```

## 🎯 Best Practices

### 1. Minimize Lock Scope

Keep critical sections as small as possible:

```ruby
# Bad: Large critical section
def bad_synchronization
  @mutex.synchronize do
    data = expensive_computation()  # Expensive operation inside lock
    @shared_variable = data
    other_operation()             # Another expensive operation
  end
end

# Good: Minimal critical section
def good_synchronization
  data = expensive_computation()  # Outside lock
  @mutex.synchronize { @shared_variable = data }
  other_operation()               # Outside lock
end
```

### 2. Avoid Nested Locks

Prevent deadlocks by avoiding nested locks:

```ruby
# Bad: Nested locks can cause deadlock
def bad_nested_locks
  @mutex1.synchronize do
    @mutex2.synchronize do
      # Critical section
    end
  end
end

# Good: Single lock or consistent ordering
def good_single_lock
  @mutex1.synchronize do
    # All operations under one lock
  end
end

# Or consistent lock ordering
def consistent_ordering
  if @mutex1.object_id < @mutex2.object_id
    @mutex1.synchronize { @mutex2.synchronize { /* work */ } }
  else
    @mutex2.synchronize { @mutex1.synchronize { /* work */ } }
  end
end
```

### 3. Use Higher-Level Abstractions

Prefer built-in concurrent data structures:

```ruby
# Instead of implementing your own, use concurrent-ruby
require 'concurrent-ruby'

# Thread-safe map
map = Concurrent::Map.new
map[:key] = "value"

# Thread-safe array
array = Concurrent::Array.new
array << "item"

# Thread-safe hash
hash = Concurrent::Hash.new
hash[:key] = "value"
```

## 🚨 Common Pitfalls

### 1. Race Conditions

```ruby
# Race condition example
class UnsafeCounter
  def initialize
    @count = 0
  end
  
  def increment
    @count += 1  # Not atomic!
  end
  
  def count
    @count
  end
end

# Fixed with mutex
class SafeCounter
  def initialize
    @count = 0
    @mutex = Mutex.new
  end
  
  def increment
    @mutex.synchronize { @count += 1 }
  end
  
  def count
    @mutex.synchronize { @count }
  end
end
```

### 2. Deadlocks

```ruby
# Deadlock example
def deadlock_example
  mutex1 = Mutex.new
  mutex2 = Mutex.new
  
  thread1 = Thread.new do
    mutex1.synchronize do
      sleep(0.1)
      mutex2.synchronize { puts "Thread 1" }
    end
  end
  
  thread2 = Thread.new do
    mutex2.synchronize do
      sleep(0.1)
      mutex1.synchronize { puts "Thread 2" }
    end
  end
  
  [thread1, thread2].each(&:join)
end
```

### 3. Missed Signals

```ruby
# Missed signal problem
def missed_signal_problem
  mutex = Mutex.new
  condition = ConditionVariable.new
  ready = false
  
  # Waiter
  waiter = Thread.new do
    mutex.synchronize do
      condition.wait(mutex) unless ready
      puts "Waiter woke up"
    end
  end
  
  # Signaler (might signal before waiter waits)
  sleep(0.1)
  mutex.synchronize do
    ready = true
    condition.signal
    puts "Signal sent"
  end
  
  waiter.join
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Thread-Safe Counter**: Implement a thread-safe counter using mutex
2. **Producer-Consumer**: Create a producer-consumer system with condition variables
3. **Worker Pool**: Build a simple thread pool with queue

### Intermediate Exercises

1. **Read-Write Lock**: Implement a read-write lock from scratch
2. **Barrier Synchronization**: Create a barrier for thread synchronization
3. **Atomic Operations**: Use atomic operations for lock-free programming

### Advanced Exercises

1. **Lock-Free Data Structure**: Implement a lock-free queue
2. **Distributed Lock**: Create a distributed lock using Redis
3. **Performance Comparison**: Compare different synchronization approaches

---

## 🎯 Summary

Thread synchronization in Ruby provides:

- **Mutex** - Basic mutual exclusion
- **Condition Variables** - Thread coordination
- **Queues** - Thread-safe data exchange
- **Atomic Operations** - Lock-free programming
- **Higher-Level Abstractions** - Concurrent data structures

Master synchronization to write safe, efficient concurrent Ruby applications!
