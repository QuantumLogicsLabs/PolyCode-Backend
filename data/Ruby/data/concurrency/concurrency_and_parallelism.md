# Concurrency and Parallelism in Ruby

## Overview

This guide covers advanced concurrency concepts in Ruby, including threading, synchronization, parallel processing, actor model, and concurrent design patterns.

## Threading Basics

### Thread Creation and Management

```ruby
# Basic thread creation
threads = []

5.times do |i|
  threads << Thread.new do
    puts "Thread #{i} started"
    sleep(rand(1..3))
    puts "Thread #{i} finished"
  end
end

threads.each(&:join)

# Thread with return value
def calculate_fibonacci(n)
  return n if n <= 1
  calculate_fibonacci(n - 1) + calculate_fibonacci(n - 2)
end

fib_threads = []
numbers = [35, 36, 37, 38, 39]

numbers.each do |num|
  fib_threads << Thread.new do
    result = calculate_fibonacci(num)
    puts "Fibonacci(#{num}) = #{result}"
    result
  end
end

fib_results = fib_threads.map(&:value)
puts "All Fibonacci calculations completed"
```

### Thread Pool

```ruby
class ThreadPool
  def initialize(size)
    @size = size
    @queue = Queue.new
    @threads = []
    @shutdown = false
    
    create_threads
  end
  
  def submit(&block)
    raise "ThreadPool is shutdown" if @shutdown
    
    @queue.push(block)
  end
  
  def shutdown
    @shutdown = true
    
    @size.times { @queue.push(nil) }
    @threads.each(&:join)
  end
  
  private
  
  def create_threads
    @size.times do
      @threads << Thread.new do
        loop do
          task = @queue.pop
          break if task.nil? || @shutdown
          
          begin
            task.call
          rescue => e
            puts "Task error: #{e.message}"
          end
        end
      end
    end
  end
end

# Usage
pool = ThreadPool.new(4)

# Submit tasks
10.times do |i|
  pool.submit do
    puts "Processing task #{i}"
    sleep(rand(0.5..2))
    puts "Completed task #{i}"
  end
end

# Wait for all tasks to complete
sleep(5)
pool.shutdown
```

### Thread-Safe Data Structures

```ruby
require 'thread'

class ThreadSafeCounter
  def initialize
    @counter = 0
    @mutex = Mutex.new
  end
  
  def increment
    @mutex.synchronize do
      @counter += 1
    end
  end
  
  def decrement
    @mutex.synchronize do
      @counter -= 1
    end
  end
  
  def value
    @mutex.synchronize { @counter }
  end
end

class ThreadSafeQueue
  def initialize
    @queue = []
    @mutex = Mutex.new
    @condition = ConditionVariable.new
  end
  
  def push(item)
    @mutex.synchronize do
      @queue.push(item)
      @condition.signal
    end
  end
  
  def pop(timeout = nil)
    @mutex.synchronize do
      if @queue.empty?
        if timeout
          @condition.wait(@mutex, timeout)
        else
          @condition.wait(@mutex)
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
end

# Usage
counter = ThreadSafeCounter.new
queue = ThreadSafeQueue.new

# Producer threads
producers = Array.new(3) do |i|
  Thread.new do
    10.times do |j|
      item = "Producer #{i} - Item #{j}"
      queue.push(item)
      counter.increment
      puts "#{item} added to queue"
      sleep(rand(0.1..0.5))
    end
  end
end

# Consumer threads
consumers = Array.new(2) do |i|
  Thread.new do
    loop do
      item = queue.pop(1)
      break if item.nil?
      
      puts "Consumer #{i} processed: #{item}"
      sleep(rand(0.2..0.8))
    end
  end
end

producers.each(&:join)
consumers.each(&:join)

puts "Final counter value: #{counter.value}"
```

## Synchronization Primitives

### Mutex and Monitor

```ruby
class BankAccount
  def initialize(balance)
    @balance = balance
    @mutex = Mutex.new
  end
  
  def deposit(amount)
    @mutex.synchronize do
      @balance += amount
      puts "Deposited #{amount}, new balance: #{@balance}"
    end
  end
  
  def withdraw(amount)
    @mutex.synchronize do
      if @balance >= amount
        @balance -= amount
        puts "Withdrew #{amount}, new balance: #{@balance}"
        true
      else
        puts "Insufficient funds for withdrawal of #{amount}"
        false
      end
    end
  end
  
  def balance
    @mutex.synchronize { @balance }
  end
end

# Demonstrate thread safety
account = BankAccount.new(1000)

threads = []
10.times do |i|
  threads << Thread.new do
    if i.even?
      account.deposit(100)
    else
      account.withdraw(50)
    end
  end
end

threads.each(&:join)
puts "Final balance: #{account.balance}"
```

### Condition Variables

```ruby
class ProducerConsumer
  def initialize(buffer_size)
    @buffer = []
    @buffer_size = buffer_size
    @mutex = Mutex.new
    @not_full = ConditionVariable.new
    @not_empty = ConditionVariable.new
  end
  
  def produce(item)
    @mutex.synchronize do
      while @buffer.size >= @buffer_size
        puts "Buffer full, producer waiting..."
        @not_full.wait(@mutex)
      end
      
      @buffer.push(item)
      puts "Produced: #{item}, buffer size: #{@buffer.size}"
      @not_empty.signal
    end
  end
  
  def consume
    @mutex.synchronize do
      while @buffer.empty?
        puts "Buffer empty, consumer waiting..."
        @not_empty.wait(@mutex)
      end
      
      item = @buffer.shift
      puts "Consumed: #{item}, buffer size: #{@buffer.size}"
      @not_full.signal
      item
    end
  end
end

# Usage
pc = ProducerConsumer.new(5)

producer_thread = Thread.new do
  10.times do |i|
    pc.produce("Item #{i}")
    sleep(rand(0.1..0.5))
  end
end

consumer_thread = Thread.new do
  10.times do
    item = pc.consume
    sleep(rand(0.1..0.5))
  end
end

producer_thread.join
consumer_thread.join
```

### Read-Write Lock

```ruby
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
      while @writer
        @read_ready.wait(@mutex)
      end
      @readers += 1
    end
    
    yield
  ensure
    @mutex.synchronize do
      @readers -= 1
      @write_ready.signal if @readers == 0
    end
  end
  
  def with_write_lock
    @mutex.synchronize do
      while @writer || @readers > 0
        @write_ready.wait(@mutex)
      end
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

class ThreadSafeCache
  def initialize
    @cache = {}
    @lock = ReadWriteLock.new
  end
  
  def get(key)
    @lock.with_read_lock do
      @cache[key]
    end
  end
  
  def set(key, value)
    @lock.with_write_lock do
      @cache[key] = value
    end
  end
  
  def size
    @lock.with_read_lock do
      @cache.size
    end
  end
end

# Usage
cache = ThreadSafeCache.new

# Writer threads
writers = Array.new(3) do |i|
  Thread.new do
    10.times do |j|
      key = "key_#{j}"
      value = "value_#{i}_#{j}"
      cache.set(key, value)
      puts "Writer #{i} set #{key} = #{value}"
      sleep(rand(0.1..0.3))
    end
  end
end

# Reader threads
readers = Array.new(5) do |i|
  Thread.new do
    20.times do |j|
      key = "key_#{j % 10}"
      value = cache.get(key)
      puts "Reader #{i} got #{key} = #{value}"
      sleep(rand(0.05..0.2))
    end
  end
end

writers.each(&:join)
readers.each(&:join)
```

## Parallel Processing

### Parallel Map

```ruby
class ParallelProcessor
  def self.map(array, &block)
    return array.map(&block) if array.size < 2
    
    threads = []
    results = Array.new(array.size)
    mutex = Mutex.new
    
    array.each_with_index do |item, index|
      threads << Thread.new do
        result = block.call(item)
        
        mutex.synchronize do
          results[index] = result
        end
      end
    end
    
    threads.each(&:join)
    results
  end
  
  def self.each(array, &block)
    threads = []
    
    array.each do |item|
      threads << Thread.new do
        block.call(item)
      end
    end
    
    threads.each(&:join)
  end
  
  def self.select(array, &block)
    threads = []
    results = []
    mutex = Mutex.new
    
    array.each do |item|
      threads << Thread.new do
        if block.call(item)
          mutex.synchronize { results << item }
        end
      end
    end
    
    threads.each(&:join)
    results
  end
end

# Usage
numbers = (1..100).to_a

# Parallel map
squared = ParallelProcessor.map(numbers) { |n| n ** 2 }
puts "First 10 squared numbers: #{squared.first(10)}"

# Parallel each
ParallelProcessor.each(numbers) do |n|
  next unless n % 10 == 0
  puts "Processing #{n}"
end

# Parallel select
primes = ParallelProcessor.select(numbers) do |n|
  next true if n == 2
  next false if n.even?
  
  (3..Math.sqrt(n).to_i).step(2).none? { |i| n % i == 0 }
end

puts "Found #{primes.length} prime numbers"
```

### Work Distribution

```ruby
class WorkDistributor
  def initialize(workers_count)
    @workers_count = workers_count
    @work_queue = Queue.new
    @result_queue = Queue.new
    @workers = []
    @mutex = Mutex.new
    @completed = 0
    @total_work = 0
  end
  
  def add_work(work_item)
    @work_queue.push(work_item)
    @total_work += 1
  end
  
  def start(&block)
    create_workers(&block)
    self
  end
  
  def wait_for_completion
    @workers.each(&:join)
    
    results = []
    results << @result_queue.pop until @result_queue.empty?
    results
  end
  
  def progress
    @mutex.synchronize { @completed.to_f / @total_work }
  end
  
  private
  
  def create_workers(&block)
    @workers_count.times do |i|
      @workers << Thread.new do
        worker_loop(i, &block)
      end
    end
  end
  
  def worker_loop(worker_id, &block)
    loop do
      work_item = @work_queue.pop
      break if work_item == :stop
      
      begin
        result = block.call(work_item, worker_id)
        @result_queue.push(result)
        
        @mutex.synchronize do
          @completed += 1
          puts "Worker #{worker_id} completed work. Progress: #{progress * 100}%"
        end
      rescue => e
        puts "Worker #{worker_id} error: #{e.message}"
      end
    end
  end
end

# Usage
distributor = WorkDistributor.new(4)

# Add work items
100.times do |i|
  distributor.add_work(i)
end

# Start processing
distributor.start do |work_item, worker_id|
  # Simulate work
  sleep(rand(0.1..0.5))
  result = work_item ** 2
  puts "Worker #{worker_id} processed #{work_item} -> #{result}"
  result
end

# Wait for completion
results = distributor.wait_for_completion
puts "Processed #{results.length} items"
```

## Actor Model

### Simple Actor Implementation

```ruby
class Actor
  def initialize(name)
    @name = name
    @mailbox = Queue.new
    @running = false
    @thread = nil
  end
  
  def start
    return if @running
    
    @running = true
    @thread = Thread.new { run }
  end
  
  def stop
    @running = false
    @mailbox.push(:stop)
    @thread&.join
  end
  
  def send_message(message)
    @mailbox.push(message)
  end
  
  private
  
  def run
    while @running
      message = @mailbox.pop
      
      case message
      when :stop
        @running = false
      else
        handle_message(message)
      end
    end
  end
  
  def handle_message(message)
    # Override in subclasses
    puts "#{@name} received: #{message}"
  end
end

class CalculatorActor < Actor
  def initialize
    super("Calculator")
    @result = 0
  end
  
  private
  
  def handle_message(message)
    case message[:type]
    when :add
      @result += message[:value]
      puts "Calculator: Added #{message[:value]}, result: #{@result}"
    when :multiply
      @result *= message[:value]
      puts "Calculator: Multiplied by #{message[:value]}, result: #{@result}"
    when :get_result
      puts "Calculator: Current result: #{@result}"
    else
      super
    end
  end
end

class LoggerActor < Actor
  def initialize
    super("Logger")
    @logs = []
  end
  
  private
  
  def handle_message(message)
    case message[:type]
    when :log
      log_entry = "[#{Time.now}] #{message[:level].upcase}: #{message[:message]}"
      @logs << log_entry
      puts "Logger: #{log_entry}"
    when :get_logs
      puts "Logger: All logs: #{@logs.join(', ')}"
    else
      super
    end
  end
end

# Usage
calculator = CalculatorActor.new
logger = LoggerActor.new

calculator.start
logger.start

# Send messages
calculator.send_message({ type: :add, value: 10 })
calculator.send_message({ type: :multiply, value: 5 })
calculator.send_message({ type: :get_result })

logger.send_message({ type: :log, level: :info, message: "Calculation started" })
logger.send_message({ type: :log, level: :info, message: "Calculation completed" })
logger.send_message({ type: :get_logs })

sleep(1)

calculator.stop
logger.stop
```

### Actor System

```ruby
class ActorSystem
  def initialize
    @actors = {}
    @mutex = Mutex.new
  end
  
  def register_actor(name, actor)
    @mutex.synchronize do
      @actors[name] = actor
      actor.start
    end
  end
  
  def unregister_actor(name)
    @mutex.synchronize do
      actor = @actors.delete(name)
      actor&.stop
    end
  end
  
  def send_message(actor_name, message)
    @mutex.synchronize do
      actor = @actors[actor_name]
      raise "Actor #{actor_name} not found" unless actor
      
      actor.send_message(message)
    end
  end
  
  def broadcast_message(message)
    @mutex.synchronize do
      @actors.each_value { |actor| actor.send_message(message) }
    end
  end
  
  def shutdown
    @mutex.synchronize do
      @actors.each_value(&:stop)
      @actors.clear
    end
  end
end

class WorkerActor < Actor
  def initialize(id)
    super("Worker-#{id}")
    @id = id
    @tasks_completed = 0
  end
  
  private
  
  def handle_message(message)
    case message[:type]
    when :task
      process_task(message[:task])
    when :get_stats
      puts "#{@name}: Completed #{@tasks_completed} tasks"
    else
      super
    end
  end
  
  def process_task(task)
    puts "#{@name}: Processing task #{task}"
    sleep(rand(0.5..2))
    @tasks_completed += 1
    puts "#{@name}: Completed task #{task}"
  end
end

# Usage
system = ActorSystem.new

# Register worker actors
5.times do |i|
  worker = WorkerActor.new(i)
  system.register_actor("worker_#{i}", worker)
end

# Distribute tasks
20.times do |i|
  worker_id = i % 5
  system.send_message("worker_#{worker_id}", { type: :task, task: i })
end

# Get stats
5.times do |i|
  system.send_message("worker_#{i}", { type: :get_stats })
end

sleep(10)

system.shutdown
```

## Concurrent Design Patterns

### Producer-Consumer Pattern

```ruby
class Pipeline
  def initialize(stages = [])
    @stages = stages
    @queues = stages.map { Queue.new }
    @queues << Queue.new  # Final output queue
    @workers = []
  end
  
  def start
    @stages.each_with_index do |stage, i|
      input_queue = @queues[i]
      output_queue = @queues[i + 1]
      
      worker = Thread.new do
        loop do
          item = input_queue.pop
          break if item == :stop
          
          begin
            result = stage.call(item)
            output_queue.push(result)
          rescue => e
            puts "Stage #{i} error: #{e.message}"
          end
        end
      end
      
      @workers << worker
    end
  end
  
  def process(item)
    @queues.first.push(item)
  end
  
  def get_result(timeout = nil)
    @queues.last.pop(timeout)
  end
  
  def shutdown
    @queues.first.push(:stop)
    @workers.each(&:join)
  end
end

# Usage
pipeline = Pipeline.new([
  ->(x) { x * 2 },           # Stage 1: Double
  ->(x) { x + 1 },           # Stage 2: Add 1
  ->(x) { x ** 2 }           # Stage 3: Square
])

pipeline.start

# Process items
10.times do |i|
  pipeline.process(i)
end

# Get results
10.times do
  result = pipeline.get_result(1)
  puts "Result: #{result}" if result
end

pipeline.shutdown
```

### Future/Promise Pattern

```ruby
class Future
  def initialize(&block)
    @value = nil
    @error = nil
    @completed = false
    @mutex = Mutex.new
    @condition = ConditionVariable.new
    @thread = Thread.new { compute(&block) }
  end
  
  def value(timeout = nil)
    @mutex.synchronize do
      unless @completed
        if timeout
          @condition.wait(@mutex, timeout)
        else
          @condition.wait(@mutex)
        end
      end
      
      raise @error if @error
      @value
    end
  end
  
  def completed?
    @mutex.synchronize { @completed }
  end
  
  def then(&block)
    Future.new do
      result = value
      block.call(result)
    end
  end
  
  def rescue(&block)
    Future.new do
      begin
        value
      rescue => e
        block.call(e)
      end
    end
  end
  
  private
  
  def compute(&block)
    begin
      result = block.call
      
      @mutex.synchronize do
        @value = result
        @completed = true
        @condition.broadcast
      end
    rescue => e
      @mutex.synchronize do
        @error = e
        @completed = true
        @condition.broadcast
      end
    end
  end
end

# Usage
# Create futures
future1 = Future.new do
  sleep(2)
  42
end

future2 = Future.new do
  sleep(1)
  "Hello"
end

future3 = Future.new do
  sleep(1.5)
  raise "Something went wrong"
end

# Chain futures
chained = future1.then { |x| x * 2 }
                 .then { |x| x + 8 }
                 .rescue { |e| "Error: #{e.message}" }

# Wait for results
puts "Future 1 result: #{future1.value}"
puts "Future 2 result: #{future2.value}"
puts "Chained result: #{chained.value}"

# Handle error
begin
  puts "Future 3 result: #{future3.value}"
rescue => e
  puts "Future 3 error: #{e.message}"
end
```

### Worker Pool Pattern

```ruby
class WorkerPool
  def initialize(size, &worker_block)
    @size = size
    @worker_block = worker_block
    @work_queue = Queue.new
    @result_queue = Queue.new
    @workers = []
    @running = false
  end
  
  def start
    return if @running
    
    @running = true
    create_workers
  end
  
  def submit(work_item)
    raise "Worker pool not running" unless @running
    
    @work_queue.push(work_item)
    self
  end
  
  def submit_with_callback(work_item, &callback)
    submit(work_item)
    
    Thread.new do
      result = get_result
      callback.call(result) if callback
    end
    
    self
  end
  
  def get_result(timeout = nil)
    @result_queue.pop(timeout)
  end
  
  def shutdown
    return unless @running
    
    @running = false
    @size.times { @work_queue.push(:stop) }
    @workers.each(&:join)
  end
  
  def queue_size
    @work_queue.size
  end
  
  private
  
  def create_workers
    @size.times do |i|
      @workers << Thread.new do
        worker_loop(i)
      end
    end
  end
  
  def worker_loop(worker_id)
    loop do
      work_item = @work_queue.pop
      break if work_item == :stop || !@running
      
      begin
        result = @worker_block.call(work_item, worker_id)
        @result_queue.push(result)
      rescue => e
        @result_queue.push({ error: e.message, work_item: work_item })
      end
    end
  end
end

# Usage
pool = WorkerPool.new(4) do |work_item, worker_id|
  puts "Worker #{worker_id} processing: #{work_item}"
  sleep(rand(0.5..2))
  result = work_item * work_item
  puts "Worker #{worker_id} completed: #{work_item} -> #{result}"
  result
end

pool.start

# Submit work with callbacks
10.times do |i|
  pool.submit_with_callback(i) do |result|
    if result.is_a?(Hash) && result[:error]
      puts "Callback error: #{result[:error]}"
    else
      puts "Callback got result: #{result}"
    end
  end
end

# Get some results
5.times do
  result = pool.get_result(3)
  if result.is_a?(Hash) && result[:error]
    puts "Error: #{result[:error]}"
  else
    puts "Got result: #{result}"
  end
end

sleep(5)
pool.shutdown
```

## Performance Considerations

### GIL (Global Interpreter Lock)

```ruby
# CPU-bound tasks (limited by GIL)
def cpu_intensive_task(n)
  count = 0
  n.times { count += 1 }
  count
end

# I/O-bound tasks (can benefit from threads)
def io_intensive_task(delay)
  sleep(delay)
  "Completed after #{delay}s"
end

puts "CPU-bound task performance:"
start_time = Time.now

# Sequential
cpu_intensive_task(10_000_000)
cpu_intensive_task(10_000_000)

sequential_time = Time.now - start_time
puts "Sequential: #{sequential_time.round(3)}s"

# Parallel (limited by GIL)
start_time = Time.now

threads = []
2.times do
  threads << Thread.new { cpu_intensive_task(10_000_000) }
end

threads.each(&:join)

parallel_time = Time.now - start_time
puts "Parallel: #{parallel_time.round(3)}s"
puts "Speedup: #{(sequential_time / parallel_time).round(2)}x"

puts "\nI/O-bound task performance:"

# Sequential
start_time = Time.now

io_intensive_task(1)
io_intensive_task(1)

sequential_time = Time.now - start_time
puts "Sequential: #{sequential_time.round(3)}s"

# Parallel
start_time = Time.now

threads = []
2.times do
  threads << Thread.new { io_intensive_task(1) }
end

threads.each(&:join)

parallel_time = Time.now - start_time
puts "Parallel: #{parallel_time.round(3)}s"
puts "Speedup: #{(sequential_time / parallel_time).round(2)}x"
```

### Process-based Parallelism

```ruby
require 'parallel'

# Using the parallel gem for process-based parallelism
def process_with_processes(items, &block)
  Parallel.map(items, in_processes: 4, &block)
end

# CPU-intensive task that benefits from multiple processes
def fibonacci(n)
  return n if n <= 1
  fibonacci(n - 1) + fibonacci(n - 2)
end

numbers = [35, 36, 37, 38]

puts "Process-based parallelism:"

# Sequential processing
start_time = Time.now
sequential_results = numbers.map { |n| fibonacci(n) }
sequential_time = Time.now - start_time

puts "Sequential time: #{sequential_time.round(3)}s"

# Parallel processing with processes
begin
  start_time = Time.now
  parallel_results = process_with_processes(numbers) { |n| fibonacci(n) }
  parallel_time = Time.now - start_time
  
  puts "Parallel time: #{parallel_time.round(3)}s"
  puts "Speedup: #{(sequential_time / parallel_time).round(2)}x"
rescue LoadError
  puts "Parallel gem not available. Install with: gem install parallel"
end
```

## Best Practices

### 1. Thread Safety Guidelines

```ruby
# Avoid shared mutable state when possible
class ImmutableData
  def initialize(data)
    @data = data.freeze
  end
  
  def get(key)
    @data[key]
  end
  
  def with_update(key, value)
    new_data = @data.dup
    new_data[key] = value
    ImmutableData.new(new_data.freeze)
  end
end

# Use thread-safe collections
require 'concurrent-ruby'

# Thread-safe hash
safe_hash = Concurrent::Hash.new

# Thread-safe array
safe_array = Concurrent::Array.new

# Thread-safe map
safe_map = Concurrent::Map.new
```

### 2. Deadlock Prevention

```ruby
class DeadlockSafeBankTransfer
  def initialize
    @accounts = {}
    @locks = {}
  end
  
  def add_account(id, balance)
    @accounts[id] = balance
    @locks[id] = Mutex.new
  end
  
  def transfer(from_id, to_id, amount)
    # Always acquire locks in the same order to prevent deadlock
    first_id = [from_id, to_id].min
    second_id = [from_id, to_id].max
    
    @locks[first_id].synchronize do
      @locks[second_id].synchronize do
        if @accounts[from_id] >= amount
          @accounts[from_id] -= amount
          @accounts[to_id] += amount
          true
        else
          false
        end
      end
    end
  end
end

# Usage
bank = DeadlockSafeBankTransfer.new
bank.add_account(1, 1000)
bank.add_account(2, 500)

success = bank.transfer(1, 2, 200)
puts "Transfer successful: #{success}"
```

### 3. Resource Management

```ruby
class ResourceManager
  def initialize
    @resources = Queue.new
    @available = 0
    @mutex = Mutex.new
    @condition = ConditionVariable.new
  end
  
  def add_resource(resource)
    @resources.push(resource)
    @mutex.synchronize do
      @available += 1
      @condition.signal
    end
  end
  
  def acquire(timeout = nil)
    @mutex.synchronize do
      while @available == 0
        return nil if timeout && !@condition.wait(@mutex, timeout)
        @condition.wait(@mutex)
      end
      
      @available -= 1
      @resources.pop
    end
  end
  
  def release(resource)
    @resources.push(resource)
    @mutex.synchronize do
      @available += 1
      @condition.signal
    end
  end
  
  def with_resource(timeout = nil, &block)
    resource = acquire(timeout)
    return nil unless resource
    
    begin
      block.call(resource)
    ensure
      release(resource)
    end
  end
end

# Usage
manager = ResourceManager.new

# Add resources
5.times { |i| manager.add_resource("Resource-#{i}") }

# Use resources with automatic cleanup
10.times do |i|
  Thread.new do
    manager.with_resource(2) do |resource|
      puts "Thread #{i} using #{resource}"
      sleep(rand(0.5..1.5))
      puts "Thread #{i} released #{resource}"
    end
  end
end

sleep(5)
```

## Practice Exercises

### Exercise 1: Concurrent Web Crawler
Build a web crawler that:
- Downloads multiple pages concurrently
- Respects rate limiting
- Handles errors gracefully
- Provides progress reporting

### Exercise 2: Parallel Data Processing
Create a data processing system that:
- Processes large files in parallel
- Uses worker pools
- Handles memory efficiently
- Provides real-time statistics

### Exercise 3: Concurrent Cache
Implement a thread-safe LRU cache with:
- Read/write locking
- Automatic expiration
- Size limits
- Performance monitoring

### Exercise 4: Actor-based Chat System
Build a chat system using the actor model with:
- Multiple chat rooms
- User actors
- Message routing
- Persistence

---

**Ready to explore more advanced Ruby topics? Let's continue! 🚀**
