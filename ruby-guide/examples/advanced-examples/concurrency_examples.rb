# Concurrency and Threading Examples
# Demonstrating threading, synchronization, and concurrent programming

puts "=== BASIC THREADING ==="

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

puts "\n=== THREAD WITH ARGUMENTS ==="

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

puts "\n=== THREAD LIFECYCLE ==="

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

puts "\n=== MUTEX SYNCHRONIZATION ==="

require 'thread'

class Counter
  def initialize
    @counter = 0
    @mutex = Mutex.new
  end
  
  def increment
    @mutex.synchronize do
      @counter += 1
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
puts "Final counter value: #{counter.value}"

puts "\n=== PRODUCER-CONSUMER PATTERN ==="

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
      @condition.signal
    end
  end
  
  def consume
    @mutex.synchronize do
      while @buffer.empty?
        puts "Consumer waiting..."
        @condition.wait(@mutex)
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

puts "\n=== THREAD-SAFE QUEUE ==="

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
        while task = @queue.pop
          process_task(task)
        end
      end
    end
  end
  
  def shutdown
    @workers.size.times { @queue.push(nil) }
    @workers.each(&:join)
  end
  
  private
  
  def process_task(task)
    puts "Processing: #{task}"
    sleep(0.5)
    puts "Completed: #{task}"
  end
end

queue = TaskQueue.new
queue.start_workers(3)

10.times { |i| queue.add_task("Task #{i}") }

sleep(3)
queue.shutdown

puts "\n=== THREAD POOL ==="

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
        break if task.nil?
        task.call
      end
    end
  end
end

pool = ThreadPool.new(4)

10.times do |i|
  pool.execute do
    puts "Task #{i} processed by #{Thread.current.object_id}"
    sleep(0.5)
  end
end

sleep(3)
pool.shutdown

puts "\n=== FUTURE/PROMISE PATTERN ==="

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

future1 = Future.new do
  sleep(2)
  "Result from future 1"
end

future2 = Future.new do
  sleep(1)
  "Result from future 2"
end

puts "Futures started, waiting for results..."
puts future1.value
puts future2.value

puts "\n=== THREAD-SAFE DATA STRUCTURES ==="

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

safe_array = ThreadSafeArray.new

threads = 10.times.map do |i|
  Thread.new do
    100.times { safe_array << i }
  end
end

threads.each(&:join)
puts "Array size: #{safe_array.size}"

puts "\n=== CONCURRENT COUNTER ==="

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

counter = ConcurrentCounter.new

threads = 100.times.map do |i|
  Thread.new do
    1000.times { counter.increment(i % 10) }
  end
end

threads.each(&:join)
puts "Counter results: #{counter.all}"

puts "\n=== ACTOR MODEL ==="

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

counter_actor = CounterActor.new

10.times { counter_actor.send_message(:increment) }
counter_actor.send_message(:get)

sleep(1)
counter_actor.stop

puts "\n=== PARALLEL MAP ==="

class ParallelProcessor
  def self.map(collection, num_threads: 4, &block)
    return [] if collection.empty?
    
    results = Array.new(collection.size)
    queue = Queue.new
    mutex = Mutex.new
    
    collection.each_with_index do |item, index|
      queue.push([item, index])
    end
    
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

numbers = (1..10).to_a
results = ParallelProcessor.map(numbers, num_threads: 4) do |n|
  sleep(0.1)
  n * n
end

puts "Parallel map results: #{results}"

puts "\n=== DEADLOCK PREVENTION ==="

# Potential deadlock example (commented out to avoid hanging)
# class PotentialDeadlock
#   def initialize
#     @mutex1 = Mutex.new
#     @mutex2 = Mutex.new
#   end
#   
#   def method1
#     @mutex1.synchronize do
#       sleep(0.1)
#       @mutex2.synchronize { puts "Method 1 completed" }
#     end
#   end
#   
#   def method2
#     @mutex2.synchronize do
#       sleep(0.1)
#       @mutex1.synchronize { puts "Method 2 completed" }
#     end
#   end
# end

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

safe = DeadlockSafe.new

thread1 = Thread.new { safe.method1 }
thread2 = Thread.new { safe.method2 }

thread1.join
thread2.join

puts "\n=== THREAD SAFETY COMPARISON ==="

# Unsafe counter
class UnsafeCounter
  def initialize
    @counter = 0
  end
  
  def increment
    @counter += 1
  end
  
  def value
    @counter
  end
end

# Safe counter
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

# Test unsafe counter
unsafe = UnsafeCounter.new
unsafe_threads = 10.times.map do
  Thread.new do
    1000.times { unsafe.increment }
  end
end

unsafe_threads.each(&:join)
puts "Unsafe counter result: #{unsafe.value}"

# Test safe counter
safe = SafeCounter.new
safe_threads = 10.times.map do
  Thread.new do
    1000.times { safe.increment }
  end
end

safe_threads.each(&:join)
puts "Safe counter result: #{safe.value}"

puts "\n=== CONCURRENCY SUMMARY ==="
puts "- Basic threading with Thread.new"
puts "- Thread synchronization with Mutex"
puts "- Producer-consumer pattern with ConditionVariable"
puts "- Thread-safe data structures"
puts "- Thread pools for efficient resource management"
puts "- Future/Promise pattern for async operations"
puts "- Actor model for message passing"
puts "- Parallel processing patterns"
puts "- Deadlock prevention techniques"
puts "\nAll examples demonstrate Ruby's concurrency capabilities!"
