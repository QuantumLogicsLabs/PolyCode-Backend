# Concurrency and Threading in Ruby

## Overview

Ruby provides several ways to achieve concurrency, from basic threading to more advanced patterns. This guide covers threading, synchronization, and concurrent programming techniques.

## Basic Threading

### Creating Threads

```ruby
# Basic thread creation
thread1 = Thread.new do
  puts "Thread 1 starting"
  sleep(2)
  puts "Thread 1 finished"
end

thread2 = Thread.new do
  puts "Thread 2 starting"
  sleep(1)
  puts "Thread 2 finished"
end

# Wait for threads to complete
thread1.join
thread2.join
puts "Main thread finished"
```

### Thread with Arguments

```ruby
def worker(name, delay)
  puts "#{name} started"
  delay.times do |i|
    puts "#{name}: #{i}"
    sleep(0.1)
  end
  puts "#{name} finished"
end

threads = []
["Alice", "Bob", "Charlie"].each_with_index do |name, index|
  threads << Thread.new(name, index + 1) { |n, d| worker(n, d) }
end

threads.each(&:join)
puts "All workers completed"
```

### Thread Lifecycle

```ruby
thread = Thread.new do
  5.times do |i|
    puts "Working... #{i}"
    sleep(0.5)
  end
end

puts "Thread status: #{thread.status}"
puts "Thread alive: #{thread.alive?}"

thread.join
puts "Thread status after join: #{thread.status}"
```

## Thread Synchronization

### Mutex (Mutual Exclusion)

```ruby
require 'thread'

class Counter
  def initialize
    @counter = 0
    @mutex = Mutex.new
  end
  
  def increment
    @mutex.synchronize do
      @counter += 1
      # Critical section - only one thread can execute this at a time
    end
  end
  
  def value
    @mutex.synchronize { @counter }
  end
end

counter = Counter.new
threads = []

10.times do
  threads << Thread.new do
    1000.times { counter.increment }
  end
end

threads.each(&:join)
puts "Final counter value: #{counter.value}"  # Should be 10000
```

### Condition Variables

```ruby
require 'thread'

class ProducerConsumer
  def initialize
    @buffer = []
    @mutex = Mutex.new
    @condition = ConditionVariable.new
  end
  
  def produce(item)
    @mutex.synchronize do
      @buffer << item
      puts "Produced: #{item}"
      @condition.signal  # Notify waiting consumer
    end
  end
  
  def consume
    @mutex.synchronize do
      while @buffer.empty?
        puts "Consumer waiting..."
        @condition.wait(@mutex)  # Wait for producer to signal
      end
      
      item = @buffer.shift
      puts "Consumed: #{item}"
      item
    end
  end
end

pc = ProducerConsumer.new

producer = Thread.new do
  5.times do |i|
    pc.produce("Item #{i}")
    sleep(0.5)
  end
end

consumer = Thread.new do
  5.times { pc.consume }
end

producer.join
consumer.join
```

### Queues for Thread-Safe Communication

```ruby
require 'thread'

class TaskQueue
  def initialize
    @queue = Queue.new
    @workers = []
  end
  
  def add_task(task)
    @queue.push(task)
  end
  
  def start_workers(num_workers)
    num_workers.times do
      @workers << Thread.new do
        while task = @queue.pop  # Blocks until task is available
          process_task(task)
        end
      end
    end
  end
  
  def shutdown
    @workers.size.times { @queue.push(nil) }  # Signal workers to stop
    @workers.each(&:join)
  end
  
  private
  
  def process_task(task)
    puts "Processing: #{task}"
    sleep(0.5)  # Simulate work
    puts "Completed: #{task}"
  end
end

queue = TaskQueue.new
queue.start_workers(3)

# Add tasks
10.times { |i| queue.add_task("Task #{i}") }

sleep(2)  # Let tasks process
queue.shutdown
```

## Advanced Threading Patterns

### Thread Pool

```ruby
require 'thread'

class ThreadPool
  def initialize(size)
    @size = size
    @queue = Queue.new
    @workers = []
    @shutdown = false
    
    @size.times { create_worker }
  end
  
  def execute(&block)
    raise "ThreadPool is shutdown" if @shutdown
    @queue.push(block)
  end
  
  def shutdown
    return if @shutdown
    
    @shutdown = true
    @size.times { @queue.push(nil) }
    @workers.each(&:join)
  end
  
  private
  
  def create_worker
    @workers << Thread.new do
      while task = @queue.pop
        break if task.nil?  # Shutdown signal
        task.call
      end
    end
  end
end

# Usage
pool = ThreadPool.new(4)

10.times do |i|
  pool.execute do
    puts "Task #{i} processed by #{Thread.current.object_id}"
    sleep(0.5)
  end
end

sleep(3)  # Wait for tasks to complete
pool.shutdown
```

### Future/Promise Pattern

```ruby
require 'thread'

class Future
  def initialize(&block)
    @mutex = Mutex.new
    @condition = ConditionVariable.new
    @value = nil
    @exception = nil
    @completed = false
    
    @thread = Thread.new do
      begin
        @value = block.call
      rescue => e
        @exception = e
      ensure
        @mutex.synchronize do
          @completed = true
          @condition.broadcast
        end
      end
    end
  end
  
  def value
    wait_for_completion
    raise @exception if @exception
    @value
  end
  
  def completed?
    @mutex.synchronize { @completed }
  end
  
  def wait
    wait_for_completion
    self
  end
  
  private
  
  def wait_for_completion
    @mutex.synchronize do
      until @completed
        @condition.wait(@mutex)
      end
    end
  end
end

# Usage
future1 = Future.new do
  sleep(2)
  "Result from future 1"
end

future2 = Future.new do
  sleep(1)
  "Result from future 2"
end

puts "Futures started, waiting for results..."
puts future1.value  # Blocks until complete
puts future2.value
```

## Concurrent Data Structures

### Thread-Safe Array

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
  
  def size
    @mutex.synchronize { @array.size }
  end
  
  def each(&block)
    @mutex.synchronize { @array.each(&block) }
  end
  
  def shift
    @mutex.synchronize { @array.shift }
  end
  
  def empty?
    @mutex.synchronize { @array.empty? }
  end
end

# Usage
safe_array = ThreadSafeArray.new

threads = 10.times.map do |i|
  Thread.new do
    100.times { safe_array << i }
  end
end

threads.each(&:join)
puts "Array size: #{safe_array.size}"  # Should be 1000
```

### Concurrent Counter

```ruby
require 'thread'

class ConcurrentCounter
  def initialize
    @counters = Hash.new(0)
    @mutex = Mutex.new
  end
  
  def increment(key)
    @mutex.synchronize do
      @counters[key] += 1
    end
  end
  
  def get(key)
    @mutex.synchronize { @counters[key] }
  end
  
  def all
    @mutex.synchronize { @counters.dup }
  end
end

# Usage
counter = ConcurrentCounter.new

threads = 100.times.map do |i|
  Thread.new do
    1000.times { counter.increment(i % 10) }
  end
end

threads.each(&:join)
puts counter.all.inspect
```

## Actor Model

### Simple Actor Implementation

```ruby
require 'thread'

class Actor
  def initialize
    @mailbox = Queue.new
    @thread = Thread.new { process_messages }
  end
  
  def send_message(message)
    @mailbox.push(message)
  end
  
  def stop
    @mailbox.push(:stop)
    @thread.join
  end
  
  private
  
  def process_messages
    while message = @mailbox.pop
      break if message == :stop
      handle_message(message)
    end
  end
  
  def handle_message(message)
    raise NotImplementedError, "Subclasses must implement handle_message"
  end
end

class CounterActor < Actor
  def initialize
    super
    @count = 0
  end
  
  private
  
  def handle_message(message)
    case message
    when :increment
      @count += 1
      puts "Count: #{@count}"
    when :get
      puts "Current count: #{@count}"
    else
      puts "Unknown message: #{message}"
    end
  end
end

# Usage
counter = CounterActor.new

10.times { counter.send_message(:increment) }
counter.send_message(:get)

sleep(1)  # Let messages process
counter.stop
```

## Parallel Processing

### Parallel Map

```ruby
require 'thread'

class ParallelProcessor
  def self.map(collection, num_threads: 4, &block)
    return [] if collection.empty?
    
    results = Array.new(collection.size)
    queue = Queue.new
    mutex = Mutex.new
    
    # Add all items to queue
    collection.each_with_index do |item, index|
      queue.push([item, index])
    end
    
    # Add stop signals
    num_threads.times { queue.push(:stop) }
    
    threads = num_threads.times.map do
      Thread.new do
        while true
          item, index = queue.pop
          break if item == :stop
          
          result = block.call(item)
          
          mutex.synchronize do
            results[index] = result
          end
        end
      end
    end
    
    threads.each(&:join)
    results
  end
end

# Usage
numbers = (1..10).to_a
results = ParallelProcessor.map(numbers, num_threads: 4) do |n|
  sleep(0.1)  # Simulate work
  n * n
end

puts results.inspect
```

### Parallel File Processing

```ruby
require 'thread'

class ParallelFileProcessor
  def self.process_files(file_paths, num_threads: 4, &block)
    results = {}
    mutex = Mutex.new
    queue = Queue.new
    
    # Add files to queue
    file_paths.each { |file| queue.push(file) }
    
    # Add stop signals
    num_threads.times { queue.push(:stop) }
    
    threads = num_threads.times.map do
      Thread.new do
        while true
          file = queue.pop
          break if file == :stop
          
          begin
            result = block.call(file)
            mutex.synchronize { results[file] = result }
          rescue => e
            mutex.synchronize { results[file] = "Error: #{e.message}" }
          end
        end
      end
    end
    
    threads.each(&:join)
    results
  end
end

# Usage
files = Dir.glob("*.rb").first(5)
results = ParallelFileProcessor.process_files(files) do |file|
  lines = File.readlines(file).size
  "#{file}: #{lines} lines"
end

results.each { |file, result| puts result }
```

## Thread Safety and Common Issues

### Race Conditions

```ruby
# Problem: Race condition
class UnsafeCounter
  def initialize
    @counter = 0
  end
  
  def increment
    @counter += 1  # Not thread-safe!
  end
  
  def value
    @counter
  end
end

# Solution: Use Mutex
class SafeCounter
  def initialize
    @counter = 0
    @mutex = Mutex.new
  end
  
  def increment
    @mutex.synchronize { @counter += 1 }
  end
  
  def value
    @mutex.synchronize { @counter }
  end
end
```

### Deadlock Prevention

```ruby
require 'thread'

# Potential deadlock
class PotentialDeadlock
  def initialize
    @mutex1 = Mutex.new
    @mutex2 = Mutex.new
  end
  
  def method1
    @mutex1.synchronize do
      sleep(0.1)
      @mutex2.synchronize do
        puts "Method 1 completed"
      end
    end
  end
  
  def method2
    @mutex2.synchronize do
      sleep(0.1)
      @mutex1.synchronize do
        puts "Method 2 completed"
      end
    end
  end
end

# Solution: Consistent locking order
class DeadlockSafe
  def initialize
    @mutex1 = Mutex.new
    @mutex2 = Mutex.new
  end
  
  def method1
    lock_both { puts "Method 1 completed" }
  end
  
  def method2
    lock_both { puts "Method 2 completed" }
  end
  
  private
  
  def lock_both(&block)
    @mutex1.synchronize do
      @mutex2.synchronize(&block)
    end
  end
end
```

## Performance Considerations

### Thread vs Process

```ruby
require 'benchmark'

# Thread-based parallelism
def thread_based_work
  threads = 4.times.map do
    Thread.new do
      10000.times { Math.sqrt(rand(1000)) }
    end
  end
  threads.each(&:join)
end

# Process-based parallelism (for comparison)
def process_based_work
  pids = 4.times.map do
    fork do
      10000.times { Math.sqrt(rand(1000)) }
    end
  end
  pids.each { |pid| Process.wait(pid) }
end

# Benchmark
puts Benchmark.measure { thread_based_work }
# puts Benchmark.measure { process_based_work }  # Unix only
```

### Thread Pool Sizing

```ruby
# Rule of thumb: number of threads = number of CPU cores
def optimal_thread_count
  # For I/O bound tasks: more threads
  # For CPU bound tasks: CPU core count
  
  cpu_cores = Etc.nprocessors rescue 4
  cpu_cores
end

puts "Optimal thread count: #{optimal_thread_count}"
```

## Best Practices

### 1. Use High-Level Abstractions

```ruby
# Good: Use Queue for thread-safe communication
queue = Queue.new
producer = Thread.new { 100.times { queue.push(item) } }
consumer = Thread.new { 100.times { item = queue.pop } }

# Avoid: Manual synchronization with shared variables
```

### 2. Keep Critical Sections Small

```ruby
# Good: Minimal locking
@mutex.synchronize do
  @shared_variable = new_value
end

# Avoid: Holding locks during long operations
@mutex.synchronize do
  @shared_variable = new_value
  expensive_operation()  # Don't do this while holding lock
end
```

### 3. Handle Exceptions in Threads

```ruby
# Good: Handle exceptions
Thread.new do
  begin
    risky_operation
  rescue => e
    puts "Thread error: #{e.message}"
  end
end

# Avoid: Unhandled exceptions in threads
Thread.new { risky_operation }  # Exception might be lost
```

## Practice Exercises

### Exercise 1: Web Crawler
Build a concurrent web crawler that:
- Uses multiple threads to fetch URLs
- Implements rate limiting
- Handles network errors gracefully
- Stores results thread-safely

### Exercise 2: Parallel Data Processing
Create a data processing pipeline that:
- Processes large files in parallel
- Uses thread-safe data structures
- Implements progress tracking
- Handles failures gracefully

### Exercise 3: Chat Server
Implement a simple chat server that:
- Handles multiple concurrent clients
- Uses thread-safe message broadcasting
- Manages client connections
- Implements proper cleanup

### Exercise 4: Task Scheduler
Build a task scheduler that:
- Executes tasks at scheduled times
- Supports recurring tasks
- Uses thread pools for execution
- Provides task status tracking

---

**Ready to explore performance optimization in Ruby? Let's continue! ⚡**
