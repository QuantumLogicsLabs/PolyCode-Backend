# Performance Optimization for Concurrent Systems
# Comprehensive guide to optimizing concurrent Ruby applications

## 🎯 Overview

Performance optimization in concurrent systems requires understanding bottlenecks, resource utilization, and optimization techniques. This guide covers profiling, optimization strategies, and best practices for concurrent Ruby applications.

## 📊 Performance Profiling

### 1. Thread Profiling

```ruby
require 'benchmark'
require 'thread'

class ThreadProfiler
  def initialize
    @thread_stats = {}
    @mutex = Mutex.new
  end
  
  def profile_thread(thread_id, &block)
    start_time = Time.now
    start_memory = get_thread_memory(thread_id)
    
    result = block.call
    
    end_time = Time.now
    end_memory = get_thread_memory(thread_id)
    
    @mutex.synchronize do
      @thread_stats[thread_id] = {
        duration: end_time - start_time,
        memory_delta: end_memory - start_memory,
        start_time: start_time,
        end_time: end_time
      }
    end
    
    result
  end
  
  def get_thread_stats(thread_id)
    @mutex.synchronize { @thread_stats[thread_id] }
  end
  
  def all_stats
    @mutex.synchronize { @thread_stats.dup }
  end
  
  def print_summary
    stats = all_stats
    
    puts "\nThread Performance Summary:"
    puts "=" * 40
    
    stats.each do |thread_id, stat|
      puts "Thread #{thread_id}:"
      puts "  Duration: #{stat[:duration].round(4)}s"
      puts "  Memory delta: #{stat[:memory_delta]} bytes"
      puts "  Start: #{stat[:start_time]}"
      puts "  End: #{stat[:end_time]}"
    end
    
    total_duration = stats.values.sum { |s| s[:duration] }
    avg_duration = total_duration / stats.size
    
    puts "\nTotal duration: #{total_duration.round(4)}s"
    puts "Average duration: #{avg_duration.round(4)}s"
  end
  
  private
  
  def get_thread_memory(thread_id)
    # Simplified memory tracking
    GC.stat[:total_allocated_objects]
  end
end

# Usage
profiler = ThreadProfiler.new

threads = 5.times.map do |i|
  Thread.new do
    profiler.profile_thread(i) do
      # Simulate work
      sleep(rand(0.1..0.5))
      (1..1000).map { |j| j * j }
    end
  end
end

threads.each(&:join)
profiler.print_summary
```

### 2. Contention Analysis

```ruby
class ContentionAnalyzer
  def initialize
    @lock_stats = {}
    @mutex = Mutex.new
  end
  
  def track_lock(lock_name, &block)
    start_time = Time.now
    thread_id = Thread.current.object_id
    
    # Track wait time
    wait_start = Time.now
    result = yield
    wait_time = Time.now - wait_start
    
    total_time = Time.now - start_time
    
    @mutex.synchronize do
      @lock_stats[lock_name] ||= {
        total_acquisitions: 0,
        total_wait_time: 0,
        total_hold_time: 0,
        max_wait_time: 0,
        threads: Set.new
      }
      
      stats = @lock_stats[lock_name]
      stats[:total_acquisitions] += 1
      stats[:total_wait_time] += wait_time
      stats[:total_hold_time] += total_time - wait_time
      stats[:max_wait_time] = [stats[:max_wait_time], wait_time].max
      stats[:threads] << thread_id
    end
    
    result
  end
  
  def print_contention_report
    @mutex.synchronize do
      puts "\nLock Contention Analysis:"
      puts "=" * 40
      
      @lock_stats.each do |lock_name, stats|
        avg_wait = stats[:total_wait_time] / stats[:total_acquisitions]
        avg_hold = stats[:total_hold_time] / stats[:total_acquisitions]
        
        puts "Lock: #{lock_name}"
        puts "  Acquisitions: #{stats[:total_acquisitions]}"
        puts "  Average wait: #{avg_wait.round(6)}s"
        puts "  Average hold: #{avg_hold.round(6)}s"
        puts "  Max wait: #{stats[:max_wait_time].round(6)}s"
        puts "  Unique threads: #{stats[:threads].size}"
        puts
      end
    end
  end
end

# Usage
analyzer = ContentionAnalyzer.new
shared_mutex = Mutex.new
shared_data = []

threads = 10.times.map do |i|
  Thread.new do
    10.times do |j|
      analyzer.track_lock("shared_mutex") do
        shared_mutex.synchronize do
          # Simulate work while holding lock
          sleep(0.01)
          shared_data << "Thread #{i} - Item #{j}"
        end
      end
    end
  end
end

threads.each(&:join)
analyzer.print_contention_report
```

### 3. Memory Profiling for Concurrent Apps

```ruby
require 'objspace'
require 'memory_profiler'

class ConcurrentMemoryProfiler
  def initialize
    @snapshots = []
    @mutex = Mutex.new
  end
  
  def take_snapshot(label = nil)
    GC.start
    snapshot = {
      label: label,
      timestamp: Time.now,
      object_counts: ObjectSpace.count_objects,
      gc_stats: GC.stat,
      thread_count: Thread.list.size
    }
    
    @mutex.synchronize { @snapshots << snapshot }
    snapshot
  end
  
  def compare_snapshots(index1, index2)
    snap1 = @snapshots[index1]
    snap2 = @snapshots[index2]
    
    puts "\nMemory Comparison: #{snap1[:label]} vs #{snap2[:label]}"
    puts "=" * 50
    
    # Object count differences
    puts "Object Count Changes:"
    snap1[:object_counts].each do |type, count1|
      count2 = snap2[:object_counts][type]
      delta = count2 - count1
      
      if delta != 0
        percentage = (delta.to_f / count1 * 100).round(2)
        puts "  #{type}: #{count1} -> #{count2} (#{delta > 0 ? '+' : ''}#{delta}, #{percentage}%)"
      end
    end
    
    # GC statistics
    puts "\nGC Statistics:"
    gc1 = snap1[:gc_stats]
    gc2 = snap2[:gc_stats]
    
    puts "  Collections: #{gc2[:count] - gc1[:count]}"
    puts "  Total allocated: #{gc2[:total_allocated_objects] - gc1[:total_allocated_objects]}"
    puts "  Total freed: #{gc2[:total_freed_objects] - gc1[:total_freed_objects]}"
    
    # Thread count
    puts "\nThread Count: #{snap1[:thread_count]} -> #{snap2[:thread_count]}"
  end
  
  def print_memory_timeline
    puts "\nMemory Usage Timeline:"
    puts "=" * 40
    
    @snapshots.each_with_index do |snapshot, index|
      puts "#{index}. #{snapshot[:label]} (#{snapshot[:timestamp]})"
      puts "   Total objects: #{snapshot[:object_counts].values.sum}"
      puts "   Threads: #{snapshot[:thread_count]}"
      puts "   GC collections: #{snapshot[:gc_stats][:count]}"
    end
  end
end

# Usage
profiler = ConcurrentMemoryProfiler.new

# Baseline
profiler.take_snapshot("baseline")

# Create concurrent workload
threads = 5.times.map do |i|
  Thread.new do
    1000.times do |j|
      # Allocate objects
      data = "Thread #{i} - Data #{j}"
      processed = data.upcase.reverse
    end
  end
end

threads.each(&:join)

# After workload
profiler.take_snapshot("after_workload")

# Compare snapshots
profiler.compare_snapshots(0, 1)
profiler.print_memory_timeline
```

## ⚡ Optimization Techniques

### 1. Lock Optimization

```ruby
class OptimizedLockManager
  def initialize
    @locks = {}
    @lock_stats = {}
    @global_mutex = Mutex.new
  end
  
  def get_lock(key)
    @global_mutex.synchronize do
      @locks[key] ||= Mutex.new
    end
  end
  
  def with_lock(key, &block)
    lock = get_lock(key)
    lock.synchronize(&block)
  end
  
  # Read-write lock for better concurrency
  def with_read_write_lock(key, read: false, &block)
    @global_mutex.synchronize do
      @locks[key] ||= ReadWriteLock.new
    end
    
    lock = @locks[key]
    
    if read
      lock.with_read_lock(&block)
    else
      lock.with_write_lock(&block)
    end
  end
  
  # Try-lock with timeout
  def try_lock(key, timeout: 5, &block)
    lock = get_lock(key)
    
    if lock.try_lock
      begin
        yield
      ensure
        lock.unlock
      end
    else
      # Wait for lock with timeout
      start_time = Time.now
      while !lock.try_lock
        raise TimeoutError if Time.now - start_time > timeout
        sleep(0.01)
      end
      
      begin
        yield
      ensure
        lock.unlock
      end
    end
  end
  
  private
  
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
end

# Usage
lock_manager = OptimizedLockManager.new

# Multiple readers
readers = 5.times.map do |i|
  Thread.new do
    lock_manager.with_read_write_lock("shared_data", read: true) do
      puts "Reader #{i} reading data"
      sleep(0.1)
    end
  end
end

# Single writer
writer = Thread.new do
  lock_manager.with_read_write_lock("shared_data", read: false) do
    puts "Writer updating data"
    sleep(0.2)
  end
end

[readers, writer].flatten.each(&:join)
```

### 2. Thread Pool Optimization

```ruby
class OptimizedThreadPool
  def initialize(size: 4, queue_size: 100)
    @size = size
    @queue_size = queue_size
    @queue = Queue.new
    @workers = []
    @stats = {
      tasks_completed: 0,
      tasks_failed: 0,
      avg_task_time: 0,
      max_task_time: 0
    }
    @mutex = Mutex.new
    @shutdown = false
    
    start_workers
  end
  
  def submit(priority = :normal, &block)
    raise "Pool is shutdown" if @shutdown
    
    task = {
      block: block,
      priority: priority,
      created_at: Time.now,
      id: SecureRandom.uuid
    }
    
    # Priority queue implementation
    if priority == :high
      # Insert at front for high priority
      temp_queue = []
      temp_queue << task
      while !@queue.empty?
        temp_queue << @queue.pop
      end
      temp_queue.each { |t| @queue.push(t) }
    else
      @queue.push(task)
    end
    
    task[:id]
  end
  
  def shutdown(timeout: 30)
    @shutdown = true
    @size.times { @queue.push({ shutdown: true }) }
    
    @workers.each do |worker|
      worker.join(timeout)
    end
  end
  
  def stats
    @mutex.synchronize { @stats.dup }
  end
  
  private
  
  def start_workers
    @size.times do |i|
      worker = Thread.new { worker_loop(i) }
      @workers << worker
    end
  end
  
  def worker_loop(worker_id)
    loop do
      task = @queue.pop
      
      if task[:shutdown]
        break
      end
      
      start_time = Time.now
      
      begin
        task[:block].call
        task_time = Time.now - start_time
        
        @mutex.synchronize do
          @stats[:tasks_completed] += 1
          update_task_time_stats(task_time)
        end
        
      rescue => e
        @mutex.synchronize { @stats[:tasks_failed] += 1 }
        puts "Worker #{worker_id} error: #{e.message}"
      end
    end
  end
  
  def update_task_time_stats(task_time)
    completed = @stats[:tasks_completed]
    current_avg = @stats[:avg_task_time]
    
    # Update running average
    @stats[:avg_task_time] = (current_avg * (completed - 1) + task_time) / completed
    @stats[:max_task_time] = [@stats[:max_task_time], task_time].max
  end
end

# Usage
pool = OptimizedThreadPool.new(size: 4, queue_size: 100)

# Submit tasks with different priorities
high_priority_tasks = 5.times.map do |i|
  pool.submit(:high) do
    puts "High priority task #{i}"
    sleep(0.1)
  end
end

normal_tasks = 10.times.map do |i|
  pool.submit(:normal) do
    puts "Normal task #{i}"
    sleep(0.2)
  end
end

sleep(2)
stats = pool.stats
puts "Pool stats: #{stats}"

pool.shutdown
```

### 3. Memory Optimization

```ruby
class MemoryOptimizedConcurrentCache
  def initialize(max_size: 1000, cleanup_interval: 60)
    @max_size = max_size
    @cleanup_interval = cleanup_interval
    @cache = {}
    @access_times = {}
    @mutex = Mutex.new
    
    start_cleanup_thread
  end
  
  def get(key)
    @mutex.synchronize do
      value = @cache[key]
      if value
        @access_times[key] = Time.now
        value
      else
        nil
      end
    end
  end
  
  def put(key, value)
    @mutex.synchronize do
      @cache[key] = value
      @access_times[key] = Time.now
      
      # Immediate cleanup if over limit
      cleanup_if_over_limit
    end
  end
  
  def remove(key)
    @mutex.synchronize do
      @cache.delete(key)
      @access_times.delete(key)
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
  
  def start_cleanup_thread
    Thread.new do
      loop do
        sleep(@cleanup_interval)
        cleanup_expired_items
      end
    end
  end
  
  def cleanup_if_over_limit
    return if @cache.size <= @max_size
    
    # Remove least recently used items
    sorted_items = @access_times.sort_by { |_, time| time }
    items_to_remove = sorted_items.first(@cache.size - @max_size)
    
    items_to_remove.each do |key, _|
      @cache.delete(key)
      @access_times.delete(key)
    end
  end
  
  def cleanup_expired_items
    @mutex.synchronize do
      cutoff_time = Time.now - (@cleanup_interval * 2)
      expired_keys = @access_times.select { |_, time| time < cutoff_time }.keys
      
      expired_keys.each do |key|
        @cache.delete(key)
        @access_times.delete(key)
      end
    end
  end
end

# Usage
cache = MemoryOptimizedConcurrentCache.new(max_size: 100, cleanup_interval: 5)

threads = 10.times.map do |i|
  Thread.new do
    50.times do |j|
      key = "key_#{i}_#{j % 10}"
      cache.put(key, "value_#{i}_#{j}")
      value = cache.get(key)
    end
  end
end

threads.each(&:join)
puts "Cache size: #{cache.size}"
```

## 🎯 Performance Tuning Strategies

### 1. Thread Count Optimization

```ruby
class ThreadCountOptimizer
  def initialize(work_items, min_threads: 1, max_threads: 16)
    @work_items = work_items
    @min_threads = min_threads
    @max_threads = max_threads
    @results = {}
  end
  
  def find_optimal_thread_count
    cpu_count = Concurrent.processor_count
    puts "CPU count: #{cpu_count}"
    
    # Test different thread counts
    thread_counts = [
      @min_threads,
      cpu_count,
      cpu_count * 2,
      @max_threads
    ].uniq.sort
    
    thread_counts.each do |thread_count|
      result = benchmark_thread_count(thread_count)
      @results[thread_count] = result
      
      puts "Threads: #{thread_count}, Time: #{result[:duration].round(4)}s, " \
           "Memory: #{result[:memory]} objects"
    end
    
    # Find optimal thread count
    optimal = @results.min_by { |_, result| result[:duration] }
    puts "\nOptimal thread count: #{optimal[0]} (#{optimal[1][:duration].round(4)}s)"
    
    optimal
  end
  
  private
  
  def benchmark_thread_count(thread_count)
    GC.start
    start_objects = GC.stat[:total_allocated_objects]
    start_time = Time.now
    
    # Execute work with specified thread count
    work_per_thread = (@work_items / thread_count.to_f).ceil
    
    threads = thread_count.times.map do |i|
      Thread.new do
        start_item = i * work_per_thread
        end_item = [start_item + work_per_thread, @work_items].min
        
        (start_item...end_item).each do |item|
          # Simulate work
          Math.sqrt(item) * Math.sin(item)
        end
      end
    end
    
    threads.each(&:join)
    
    end_time = Time.now
    end_objects = GC.stat[:total_allocated_objects]
    
    {
      duration: end_time - start_time,
      memory: end_objects - start_objects,
      throughput: @work_items / (end_time - start_time)
    }
  end
end

# Usage
optimizer = ThreadCountOptimizer.new(10000)
optimal = optimizer.find_optimal_thread_count
```

### 2. Batch Processing Optimization

```ruby
class BatchProcessor
  def initialize(batch_size: 100, max_concurrent: 4)
    @batch_size = batch_size
    @max_concurrent = max_concurrent
  end
  
  def process_batched(items, &block)
    # Split items into batches
    batches = items.each_slice(@batch_size).to_a
    
    # Process batches concurrently
    semaphore = Mutex.new
    results = []
    
    batches.each_slice(@max_concurrent) do |batch_group|
      threads = batch_group.map do |batch|
        Thread.new do
          result = block.call(batch)
          
          semaphore.synchronize do
            results.concat(result)
          end
        end
      end
      
      threads.each(&:join)
    end
    
    results
  end
  
  def process_streaming(items, &block)
    queue = Queue.new
    results = Queue.new
    
    # Producer thread
    producer = Thread.new do
      items.each_slice(@batch_size) do |batch|
        queue.push(batch)
      end
      queue.push(nil)  # Signal end
    end
    
    # Consumer threads
    consumers = @max_concurrent.times.map do
      Thread.new do
        loop do
          batch = queue.pop
          break if batch.nil?
          
          result = block.call(batch)
          results.push(result)
        end
      end
    end
    
    # Wait for completion
    producer.join
    consumers.each(&:join)
    
    # Collect results
    all_results = []
    all_results << results.pop until results.empty?
    all_results.flatten
  end
end

# Usage
processor = BatchProcessor.new(batch_size: 50, max_concurrent: 4)

# Large dataset
large_dataset = (1..10000).to_a

# Batch processing
results = processor.process_batched(large_dataset) do |batch|
  batch.map { |item| item * 2 }
end

puts "Processed #{results.size} items"
```

### 3. Cache Optimization

```ruby
class OptimizedConcurrentCache
  def initialize(shards: 16, shard_size: 1000)
    @shards = shards
    @shard_size = shard_size
    @caches = shards.times.map { ConcurrentCache.new(shard_size) }
  end
  
  def get(key)
    shard_index = key.hash % @shards
    @caches[shard_index].get(key)
  end
  
  def put(key, value)
    shard_index = key.hash % @shards
    @caches[shard_index].put(key, value)
  end
  
  def size
    @caches.sum(&:size)
  end
  
  def stats
    {
      total_size: size,
      avg_shard_size: size / @shards,
      shard_stats: @caches.map(&:size)
    }
  end
  
  private
  
  class ConcurrentCache
    def initialize(max_size)
      @max_size = max_size
      @cache = {}
      @access_times = {}
      @mutex = Mutex.new
    end
    
    def get(key)
      @mutex.synchronize do
        value = @cache[key]
        @access_times[key] = Time.now if value
        value
      end
    end
    
    def put(key, value)
      @mutex.synchronize do
        @cache[key] = value
        @access_times[key] = Time.now
        
        # LRU eviction if needed
        if @cache.size > @max_size
          oldest_key = @access_times.min_by { |_, time| time }.first
          @cache.delete(oldest_key)
          @access_times.delete(oldest_key)
        end
      end
    end
    
    def size
      @mutex.synchronize { @cache.size }
    end
  end
end

# Usage
cache = OptimizedConcurrentCache.new(shards: 8, shard_size: 500)

threads = 20.times.map do |i|
  Thread.new do
    100.times do |j|
      key = "key_#{i}_#{j}"
      value = "value_#{i}_#{j}"
      cache.put(key, value)
      retrieved = cache.get(key)
    end
  end
end

threads.each(&:join)
stats = cache.stats
puts "Cache stats: #{stats}"
```

## 🎯 Best Practices

### 1. Profile Before Optimizing

```ruby
# Always profile first
def profile_concurrent_code(&block)
  require 'benchmark'
  
  # Memory profiling
  MemoryProfiler.report do
    # CPU profiling
    Benchmark.measure do
      block.call
    end
  end
end

# Usage
profile_concurrent_code do
  # Your concurrent code here
end
```

### 2. Use Appropriate Synchronization

```ruby
# Choose the right synchronization primitive
class SynchronizationChooser
  def self.for_use_case(use_case)
    case use_case
    when :simple_counter
      Concurrent::AtomicFixnum.new(0)
    when :shared_data
      Mutex.new
    when :read_heavy
      ReadWriteLock.new
    when :producer_consumer
      Queue.new
    else
      Mutex.new  # Default
    end
  end
end
```

### 3. Monitor and Alert

```ruby
class PerformanceMonitor
  def initialize(alert_threshold: 5.0)
    @alert_threshold = alert_threshold
    @metrics = []
    @mutex = Mutex.new
  end
  
  def record_metric(name, value)
    @mutex.synchronize do
      @metrics << { name: name, value: value, timestamp: Time.now }
      
      # Keep only last 1000 metrics
      @metrics.shift if @metrics.size > 1000
      
      # Check for alerts
      check_alerts(name, value)
    end
  end
  
  def get_average(metric_name, duration: 300)
    cutoff = Time.now - duration
    
    @mutex.synchronize do
      relevant_metrics = @metrics.select do |m|
        m[:name] == metric_name && m[:timestamp] > cutoff
      end
      
      return 0 if relevant_metrics.empty?
      
      relevant_metrics.sum { |m| m[:value] } / relevant_metrics.size
    end
  end
  
  private
  
  def check_alerts(name, value)
    if value > @alert_threshold
      puts "ALERT: #{name} exceeded threshold (#{value} > #{@alert_threshold})"
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Thread Profiler**: Implement a basic thread profiler
2. **Lock Analyzer**: Create a lock contention analyzer
3. **Memory Tracker**: Build a memory usage tracker

### Intermediate Exercises

1. **Optimized Thread Pool**: Create an optimized thread pool
2. **Batch Processor**: Implement efficient batch processing
3. **Performance Monitor**: Build a performance monitoring system

### Advanced Exercises

1. **Adaptive Scheduling**: Create adaptive thread scheduling
2. **Cache Optimization**: Implement advanced caching strategies
3. **Auto-tuning System**: Build an auto-tuning concurrent system

---

## 🎯 Summary

Performance optimization for concurrent systems requires:

- **Profiling** - Identify bottlenecks before optimizing
- **Lock optimization** - Reduce contention and improve throughput
- **Memory management** - Efficient memory usage and cleanup
- **Thread tuning** - Optimal thread count and pool sizing
- **Continuous monitoring** - Track performance over time

Master these techniques to build high-performance, scalable concurrent Ruby applications!
