# Concurrency Optimization in Ruby
# Comprehensive guide to concurrent programming and optimization

## 🎯 Overview

Concurrency optimization is essential for building high-performance Ruby applications. This guide covers threading, parallel processing, async programming, and optimization techniques for concurrent systems.

## 🧵 Threading Optimization

### 1. Thread Pool Management

Efficient thread pool implementation:

```ruby
class ThreadPool
  def initialize(size = 4)
    @size = size
    @queue = Queue.new
    @threads = []
    @shutdown = false
    @mutex = Mutex.new
    @stats = {
      tasks_completed: 0,
      tasks_failed: 0,
      avg_task_time: 0,
      total_task_time: 0
    }
    
    start_threads
  end
  
  def submit(&block)
    return nil if @shutdown
    
    task = Task.new(block)
    @queue.push(task)
    task
  end
  
  def submit_with_timeout(timeout, &block)
    return nil if @shutdown
    
    task = Task.new(block)
    task.timeout = timeout
    @queue.push(task)
    task
  end
  
  def shutdown(timeout: 30)
    @shutdown = true
    @size.times { @queue.push(ShutdownTask.new) }
    
    @threads.each do |thread|
      thread.join(timeout)
    end
  end
  
  def stats
    @mutex.synchronize { @stats.dup }
  end
  
  def queue_size
    @queue.length
  end
  
  def active_threads
    @threads.count { |t| t.alive? }
  end
  
  private
  
  def start_threads
    @size.times do |i|
      thread = Thread.new { worker_loop(i) }
      @threads << thread
    end
  end
  
  def worker_loop(worker_id)
    loop do
      task = @queue.pop
      
      if task.is_a?(ShutdownTask)
        break
      end
      
      execute_task(task, worker_id)
    end
  end
  
  def execute_task(task, worker_id)
    start_time = Time.now
    
    begin
      # Handle timeout
      if task.timeout
        result = Timeout.timeout(task.timeout) do
          task.execute
        end
      else
        result = task.execute
      end
      
      task.complete(result)
      
    rescue => e
      task.fail(e)
    end
    
    end_time = Time.now
    duration = end_time - start_time
    
    update_stats(duration)
  end
  
  def update_stats(duration)
    @mutex.synchronize do
      @stats[:tasks_completed] += 1
      @stats[:total_task_time] += duration
      @stats[:avg_task_time] = @stats[:total_task_time] / @stats[:tasks_completed]
    end
  end
end

class Task
  attr_reader :result, :error
  attr_accessor :timeout
  
  def initialize(block)
    @block = block
    @result = nil
    @error = nil
    @completed = false
    @mutex = Mutex.new
    @condition = ConditionVariable.new
    @timeout = nil
  end
  
  def execute
    @block.call
  end
  
  def complete(result)
    @mutex.synchronize do
      @result = result
      @completed = true
      @condition.broadcast
    end
  end
  
  def fail(error)
    @mutex.synchronize do
      @error = error
      @completed = true
      @condition.broadcast
    end
  end
  
  def completed?
    @mutex.synchronize { @completed }
  end
  
  def wait
    @mutex.synchronize do
      @condition.wait(@mutex) unless @completed
    end
  end
  
  def wait_timeout(timeout)
    @mutex.synchronize do
      @condition.wait(@mutex, timeout) unless @completed
    end
    @completed
  end
end

class ShutdownTask
  def execute
    # No operation
  end
end

# Usage example
pool = ThreadPool.new(4)

# Submit tasks
tasks = []
10.times do |i|
  task = pool.submit do
    sleep(rand(0.1..0.5))
    "Task #{i} completed by #{Thread.current.object_id}"
  end
  tasks << task
end

# Wait for completion
tasks.each(&:wait)

# Get results
tasks.each_with_index do |task, i|
  if task.completed?
    puts "Task #{i}: #{task.result}"
  else
    puts "Task #{i}: Failed - #{task.error&.message}"
  end
end

# Print stats
puts "Pool stats: #{pool.stats}"
puts "Queue size: #{pool.queue_size}"
puts "Active threads: #{pool.active_threads}"

pool.shutdown
```

### 2. Work Stealing Queue

Advanced work distribution with work stealing:

```ruby
class WorkStealingQueue
  def initialize
    @queues = []
    @workers = []
    @mutex = Mutex.new
  end
  
  def add_worker
    worker_id = @queues.length
    queue = Queue.new
    @queues << queue
    
    worker = Thread.new { worker_loop(worker_id) }
    @workers << worker
    
    worker_id
  end
  
  def submit(task, worker_id = nil)
    if worker_id
      @queues[worker_id].push(task)
    else
      @queues.first.push(task)
    end
  end
  
  def shutdown
    @workers.each do |worker|
      @queues.each { |queue| queue.push(ShutdownTask.new) }
    end
    
    @workers.each(&:join)
  end
  
  private
  
  def worker_loop(worker_id)
    loop do
      task = get_task(worker_id)
      break if task.is_a?(ShutdownTask)
      
      begin
        task.execute
      rescue => e
        puts "Worker #{worker_id} error: #{e.message}"
      end
    end
  end
  
  def get_task(worker_id)
    # Try to get work from own queue
    task = @queues[worker_id].pop(true)
    return task if task
    
    # Try to steal work from other queues
    @queues.each_with_index do |queue, index|
      next if index == worker_id
      
      begin
        task = queue.pop(true)
        return task if task
      rescue ThreadError
        # Queue empty, try next
        next
      end
    end
    
    # No work available, wait
    @queues[worker_id].pop
  end
end

# Usage
ws_queue = WorkStealingQueue.new

# Add workers
worker_ids = 4.times.map { ws_queue.add_worker }

# Submit tasks
20.times do |i|
  ws_queue.submit(proc do
    sleep(rand(0.1..0.3))
    puts "Task #{i} completed by worker #{Thread.current.object_id}"
  end)
end

sleep(2)
ws_queue.shutdown
```

## ⚡ Parallel Processing

### 1. Parallel Map Implementation

Parallel processing of collections:

```ruby
class ParallelProcessor
  def initialize(size = 4)
    @size = size
    @thread_pool = ThreadPool.new(size)
  end
  
  def parallel_map(collection, &block)
    return collection.map(&block) if collection.empty?
    
    # Split collection into chunks
    chunk_size = [collection.length / @size, 1].max
    chunks = collection.each_slice(chunk_size).to_a
    
    # Process chunks in parallel
    tasks = chunks.map do |chunk|
      @thread_pool.submit do
        chunk.map(&block)
      end
    end
    
    # Wait for completion and combine results
    tasks.flat_map(&:wait)
  end
  
  def parallel_each(collection, &block)
    parallel_map(collection, &block)
    collection
  end
  
  def parallel_select(collection, &block)
    parallel_map(collection, &block).zip(collection).select { |result, _| result }.map { |_, item| item }
  end
  
  def parallel_reduce(collection, initial, &block)
    return initial if collection.empty?
    
    # For reduction, we need to be careful about order
    # This is a simplified implementation
    parallel_map(collection, &block).reduce(initial, &:+)
  end
  
  def shutdown
    @thread_pool.shutdown
  end
end

# Usage examples
processor = ParallelProcessor.new(4)

# Parallel map
numbers = (1..100).to_a
squared = processor.parallel_map(numbers) { |n| n ** 2 }
puts "Squared numbers: #{squared.first(5)}"

# Parallel each
processor.parallel_each(numbers) do |n|
  puts "Processing #{n}"
end

# Parallel select
even_numbers = processor.parallel_select(numbers) { |n| n.even? }
puts "Even numbers count: #{even_numbers.length}"

# Parallel reduce
sum = processor.parallel_reduce(numbers, 0) { |n| n }
puts "Sum of numbers: #{sum}"

processor.shutdown
```

### 2. Parallel File Processing

Process multiple files concurrently:

```ruby
class ParallelFileProcessor
  def initialize(size = 4)
    @size = size
    @thread_pool = ThreadPool.new(size)
    @results = {}
    @mutex = Mutex.new
  end
  
  def process_files(file_patterns, &block)
    # Find matching files
    files = []
    file_patterns.each do |pattern|
      files.concat(Dir.glob(pattern))
    end
    
    # Process files in parallel
    tasks = files.map do |file|
      @thread_pool.submit do
        process_single_file(file, &block)
      end
    end
    
    # Wait for completion
    tasks.each(&:wait)
    
    @results
  end
  
  def process_directory(directory, pattern = "*", &block)
    file_pattern = File.join(directory, pattern)
    process_files([file_pattern], &block)
  end
  
  private
  
  def process_single_file(file, &block)
    begin
      start_time = Time.now
      
      # Read file
      content = File.read(file)
      
      # Process content
      result = block.call(file, content)
      
      end_time = Time.now
      duration = end_time - start_time
      
      # Store result
      @mutex.synchronize do
        @results[file] = {
          result: result,
          duration: duration,
          size: content.length,
          error: nil
        }
      end
      
    rescue => e
      @mutex.synchronize do
        @results[file] = {
          result: nil,
          duration: 0,
          size: 0,
          error: e.message
        }
      end
    end
  end
end

# Usage example
processor = ParallelFileProcessor.new(4)

# Process log files
results = processor.process_files(['*.log', '*.txt']) do |file, content|
  lines = content.lines.count
  words = content.split(/\s+/).length
  {
    lines: lines,
    words: words,
    file_size: content.length
  }
end

# Print results
results.each do |file, result|
  if result[:error]
    puts "#{file}: ERROR - #{result[:error]}"
  else
    puts "#{file}: #{result[:result][:lines]} lines, #{result[:result][:words]} words (#{result[:duration].round(3)}s)"
  end
end

processor.shutdown
```

## 🔄 Async Programming

### 1. Async/Await Pattern

Implement async/await in Ruby:

```ruby
class Promise
  def initialize(&block)
    @block = block
    @state = :pending
    @value = nil
    @reason = nil
    @callbacks = []
    @mutex = Mutex.new
  end
  
  def resolve(value)
    @mutex.synchronize do
      return unless @state == :pending
      
      @state = :fulfilled
      @value = value
      execute_callbacks
    end
  end
  
  def reject(reason)
    @mutex.synchronize do
      return unless @state == :pending
      
      @state = :rejected
      @reason = reason
      execute_callbacks
    end
  end
  
  def then(&block)
    promise = Promise.new
    
    @mutex.synchronize do
      case @state
      when :fulfilled
        execute_callback(block, @value, promise)
      when :rejected
        promise.reject(@reason)
      else
        @callbacks << { callback: block, promise: promise }
      end
    end
    
    promise
  end
  
  def rescue(&block)
    promise = Promise.new
    
    @mutex.synchronize do
      case @state
      when :fulfilled
        promise.resolve(@value)
      when :rejected
        execute_callback(block, @reason, promise)
      else
        @callbacks << { callback: block, promise: promise, type: :rescue }
      end
    end
    
    promise
  end
  
  def fulfilled?
    @mutex.synchronize { @state == :fulfilled }
  end
  
  def rejected?
    @mutex.synchronize { @state == :rejected }
  end
  
  def pending?
    @mutex.synchronize { @state == :pending }
  end
  
  def value
    @mutex.synchronize { @value }
  end
  
  def reason
    @mutex.synchronize { @reason }
  end
  
  def self.resolve(value)
    promise = Promise.new
    promise.resolve(value)
    promise
  end
  
  def self.reject(reason)
    promise = Promise.new
    promise.reject(reason)
    promise
  end
  
  def self.all(promises)
    results = []
    completed = 0
    final_promise = Promise.new
    mutex = Mutex.new
    
    promises.each_with_index do |promise, index|
      promise.then do |value|
        mutex.synchronize do
          results[index] = value
          completed += 1
          final_promise.resolve(results) if completed == promises.length
        end
      end.rescue do |reason|
        final_promise.reject(reason)
      end
    end
    
    final_promise
  end
  
  private
  
  def execute_callback(callback, value, promise)
    begin
      result = callback.call(value)
      promise.resolve(result)
    rescue => e
      promise.reject(e)
    end
  end
  
  def execute_callbacks
    @callbacks.each do |callback|
      if callback[:type] == :rescue
        execute_callback(callback[:callback], @reason, callback[:promise])
      else
        execute_callback(callback[:callback], @value, callback[:promise])
      end
    end
    @callbacks.clear
  end
end

# Async function
module Async
  def self.async(&block)
    promise = Promise.new
    
    Thread.new do
      begin
        result = block.call
        promise.resolve(result)
      rescue => e
        promise.reject(e)
      end
    end
    
    promise
  end
  
  def self.await(promise)
    # Wait for promise to complete
    while promise.pending?
      sleep(0.01)
    end
    
    if promise.fulfilled?
      promise.value
    else
      raise promise.reason
    end
  end
  
  def self.parallel(*promises)
    Promise.all(promises)
  end
end

# Usage examples
# Basic async operation
promise = Async.async do
  sleep(1)
  "Async operation completed"
end

promise.then { |result| puts "Result: #{result}" }

# Async function
def fetch_user_async(user_id)
  Async.async do
    sleep(0.5)  # Simulate API call
    { id: user_id, name: "User #{user_id}", email: "user#{user_id}@example.com" }
  end
end

def fetch_posts_async(user_id)
  Async.async do
    sleep(0.3)  # Simulate API call
    [
      { id: 1, title: "Post 1", user_id: user_id },
      { id: 2, title: "Post 2", user_id: user_id }
    ]
  end
end

# Async function with await
def get_user_with_posts(user_id)
  user = Async.await(fetch_user_async(user_id))
  posts = Async.await(fetch_posts_async(user_id))
  
  {
    user: user,
    posts: posts
  }
end

# Parallel async operations
def get_multiple_users(user_ids)
  promises = user_ids.map { |id| fetch_user_async(id) }
  Async.await(Async.parallel(*promises))
end

# Usage
result = get_user_with_posts(123)
puts "User: #{result[:user][:name]}"
puts "Posts: #{result[:posts].length}"

# Parallel operations
users = get_multiple_users([1, 2, 3, 4])
puts "Fetched #{users.length} users"
```

### 2. Event Loop Implementation

Custom event loop for async operations:

```ruby
class EventLoop
  def initialize
    @tasks = []
    @timers = []
    @running = false
    @mutex = Mutex.new
  end
  
  def start
    @running = true
    
    while @running
      process_tasks
      process_timers
      sleep(0.001)  # Prevent CPU spinning
    end
  end
  
  def stop
    @running = false
  end
  
  def schedule(&block)
    @mutex.synchronize do
      @tasks << { type: :task, block: block }
    end
  end
  
  def schedule_delayed(delay, &block)
    @mutex.synchronize do
      @timers << { 
        type: :timer, 
        block: block, 
        execute_at: Time.now + delay 
      }
    end
  end
  
  def schedule_at(time, &block)
    @mutex.synchronize do
      @timers << { 
        type: :timer, 
        block: block, 
        execute_at: time 
      }
    end
  end
  
  private
  
  def process_tasks
    @mutex.synchronize do
      while @tasks.any?
        task = @tasks.shift
        task[:block].call
      end
    end
  end
  
  def process_timers
    @mutex.synchronize do
      current_time = Time.now
      
      ready_timers = @timers.select { |timer| timer[:execute_at] <= current_time }
      @timers.reject! { |timer| timer[:execute_at] <= current_time }
      
      ready_timers.each { |timer| timer[:block].call }
    end
  end
end

# Async operations with event loop
class AsyncOperation
  def initialize(event_loop)
    @event_loop = event_loop
  end
  
  def async_operation(&block)
    promise = Promise.new
    
    @event_loop.schedule do
      begin
        result = block.call
        promise.resolve(result)
      rescue => e
        promise.reject(e)
      end
    end
    
    promise
  end
  
  def async_delay(delay, &block)
    promise = Promise.new
    
    @event_loop.schedule_delayed(delay) do
      begin
        result = block.call
        promise.resolve(result)
      rescue => e
        promise.reject(e)
      end
    end
    
    promise
  end
  
  def async_http_get(url)
    promise = Promise.new
    
    @event_loop.schedule do
      begin
        # Simulate HTTP request
        sleep(0.1)
        response = "Response from #{url}"
        promise.resolve(response)
      rescue => e
        promise.reject(e)
      end
    end
    
    promise
  end
end

# Usage
event_loop = EventLoop.new
async_ops = AsyncOperation.new(event_loop)

# Schedule async operations
async_ops.async_operation do
  sleep(0.5)
  "Operation completed"
end.then { |result| puts "Result: #{result}" }

async_ops.async_delay(1) do
  "Delayed operation completed"
end.then { |result| puts "Result: #{result}" }

async_ops.async_http_get("https://api.example.com/data")
  .then { |response| puts "HTTP Response: #{response}" }

# Start event loop in separate thread
loop_thread = Thread.new { event_loop.start }

sleep(3)
event_loop.stop
loop_thread.join
```

## 🎯 Performance Optimization

### 1. Thread Synchronization Optimization

Optimize thread synchronization patterns:

```ruby
class OptimizedSynchronization
  def initialize
    @counters = Hash.new(0)
    @mutex = Mutex.new
    @read_write_lock = ReadWriteLock.new
    @atomic_counter = Concurrent::AtomicFixnum.new(0)
  end
  
  def benchmark_synchronization
    puts "Synchronization Benchmark:"
    puts "=" * 40
    
    # Test different synchronization methods
    methods = {
      'Mutex' => -> { mutex_increment },
      'Concurrent::Atomic' => -> { atomic_increment },
      'No synchronization' => -> { unsynchronized_increment },
      'ReadWriteLock' => -> { read_write_increment }
    }
    
    methods.each do |name, method|
      time = Benchmark.measure do
        10000.times { method.call }
      end
      
      puts "#{name}: #{time.real.round(4)}s"
    end
  end
  
  def demonstrate_lock_free
    puts "\nLock-Free Operations:"
    puts "=" * 40
    
    # Lock-free counter
    counter = Concurrent::AtomicFixnum.new(0)
    
    threads = 10.times.map do
      Thread.new do
        1000.times { counter.increment }
      end
    end
    
    threads.each(&:join)
    
    puts "Lock-free counter result: #{counter.value}"
    
    # Compare with mutex
    @mutex.synchronize { @counters[:mutex] = 0 }
    
    threads = 10.times.map do
      Thread.new do
        1000.times { @mutex.synchronize { @counters[:mutex] += 1 } }
      end
    end
    
    threads.each(&:join)
    
    puts "Mutex counter result: #{@counters[:mutex]}"
  end
  
  def demonstrate_read_write_lock
    puts "\nRead-Write Lock:"
    puts "=" * 40
    
    @data = []
    
    # Reader threads
    readers = 5.times.map do |i|
      Thread.new do
        10.times do
          @read_write_lock.with_read_lock do
            # Read operation
            puts "Reader #{i}: #{@data.length} items"
            sleep(0.01)
          end
        end
      end
    end
    
    # Writer threads
    writers = 2.times.map do |i|
      Thread.new do
        5.times do
          @read_write_lock.with_write_lock do
            # Write operation
            @data << "Item #{i}_#{@data.length}"
            puts "Writer #{i}: Added item"
            sleep(0.02)
          end
        end
      end
    end
    
    (readers + writers).each(&:join)
    
    puts "Final data size: #{@data.length}"
  end
  
  private
  
  def mutex_increment
    @mutex.synchronize { @counters[:mutex] += 1 }
  end
  
  def atomic_increment
    @atomic_counter.increment
  end
  
  def unsynchronized_increment
    @counters[:unsync] += 1
  end
  
  def read_write_increment
    @read_write_lock.with_read_lock do
      @counters[:rwlock] += 1
    end
  end
end

# Simple read-write lock implementation
class ReadWriteLock
  def initialize
    @readers = 0
    @writer = false
    @mutex = Mutex.new
    @read_ready = ConditionVariable.new
    @write_ready = ConditionVariable.new
  end
  
  def with_read_lock
    @mutex.synchronize do
      @write_ready.wait(@mutex) while @writer
      @readers += 1
    end
    
    yield
  ensure
    @mutex.synchronize do
      @readers -= 1
      @read_ready.signal if @readers == 0
    end
  end
  
  def with_write_lock
    @mutex.synchronize do
      @write_ready.wait(@mutex) while @writer || @readers > 0
      @writer = true
    end
    
    yield
  ensure
    @mutex.synchronize do
      @writer = false
      @read_ready.broadcast
    end
  end
end

# Usage
sync_opt = OptimizedSynchronization.new
sync_opt.benchmark_synchronization
sync_opt.demonstrate_lock_free
sync_opt.demonstrate_read_write_lock
```

### 2. Concurrent Data Structures

Efficient concurrent data structures:

```ruby
class ConcurrentCache
  def initialize(max_size = 1000)
    @max_size = max_size
    @cache = {}
    @access_times = {}
    @mutex = Mutex.new
  end
  
  def get(key)
    @mutex.synchronize do
      value = @cache[key]
      if value
        @access_times[key] = Time.now
      end
      value
    end
  end
  
  def put(key, value)
    @mutex.synchronize do
      @cache[key] = value
      @access_times[key] = Time.now
      
      evict_if_needed
    end
  end
  
  def size
    @mutex.synchronize { @cache.size }
  end
  
  def clear
    @mutex.synchronize do
      @cache.clear
      @access_times.clear
    end
  end
  
  private
  
  def evict_if_needed
    return if @cache.size <= @max_size
    
    # LRU eviction
    oldest_key = @access_times.min_by { |_, time| time }.first
    @cache.delete(oldest_key)
    @access_times.delete(oldest_key)
  end
end

class ConcurrentQueue
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
  
  def pop
    @mutex.synchronize do
      while @queue.empty?
        @not_empty.wait(@mutex)
      end
      
      @queue.shift
    end
  end
  
  def pop_nonblocking
    @mutex.synchronize do
      @queue.shift
    end
  end
  
  def empty?
    @mutex.synchronize { @queue.empty? }
  end
  
  def size
    @mutex.synchronize { @queue.size }
  end
end

class ConcurrentSet
  def initialize
    @set = Set.new
    @mutex = Mutex.new
  end
  
  def add(item)
    @mutex.synchronize do
      @set.add(item)
    end
  end
  
  def include?(item)
    @mutex.synchronize { @set.include?(item) }
  end
  
  def delete(item)
    @mutex.synchronize { @set.delete(item) }
  end
  
  def size
    @mutex.synchronize { @set.size }
  end
  
  def to_a
    @mutex.synchronize { @set.to_a }
  end
end

# Usage examples
cache = ConcurrentCache.new

# Concurrent cache operations
threads = 10.times.map do |i|
  Thread.new do
    100.times do |j|
      cache.put("key_#{i}_#{j}", "value_#{i}_#{j}")
      value = cache.get("key_#{i}_#{j}")
    end
  end
end

threads.each(&:join)
puts "Cache size: #{cache.size}"

queue = ConcurrentQueue.new

# Producer-consumer pattern
producers = 3.times.map do |i|
  Thread.new do
    10.times do |j|
      queue.push("Item #{i}-#{j}")
      puts "Producer #{i}: Pushed Item #{i}-#{j}"
    end
  end
end

consumers = 2.times.map do |i|
  Thread.new do
    15.times do
      item = queue.pop
      puts "Consumer #{i}: Popped #{item}"
    end
  end
end

(producers + consumers).each(&:join)
```

## 🎓 Exercises

### Beginner Exercises

1. **Thread Pool**: Implement a basic thread pool
2. **Parallel Map**: Create parallel collection processing
3. **Async Operations**: Implement async/await pattern

### Intermediate Exercises

1. **Work Stealing**: Build a work-stealing queue
2. **Event Loop**: Create an event loop system
3. **Concurrent Cache**: Implement a thread-safe cache

### Advanced Exercises

1. **Lock-Free Data Structures**: Build lock-free structures
2. **Actor Model**: Implement actor-based concurrency
3. **Performance Benchmark**: Compare concurrency approaches

---

## 🎯 Summary

Concurrency optimization in Ruby provides:

- **Thread Pools** - Efficient thread management
- **Parallel Processing** - Concurrent computation
- **Async Programming** - Non-blocking operations
- **Synchronization** - Thread-safe coordination
- **Performance Optimization** - Efficient concurrent patterns

Master these techniques to build high-performance concurrent Ruby applications!
