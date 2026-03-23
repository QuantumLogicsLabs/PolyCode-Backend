# Parallel Processing in Ruby
# Comprehensive guide to parallel computation and distributed processing

## 🎯 Overview

Parallel processing enables executing multiple tasks simultaneously to improve performance and utilize multi-core systems effectively. This guide covers parallel processing techniques, frameworks, and best practices in Ruby.

## 🚀 Parallel Processing Concepts

### 1. Process-Based Parallelism

Using multiple processes for true parallelism:

```ruby
require 'parallel'

# Parallel map with processes
def parallel_map_with_processes(data, &block)
  Parallel.map(data, in_processes: 4, &block)
end

# Usage
numbers = (1..100).to_a
results = parallel_map_with_processes(numbers) do |n|
  n ** 2
  puts "Process #{Process.pid} processing #{n}"
  n ** 2
end

puts "Results: #{results.first(5)}"
```

### 2. Thread-Based Parallelism

Using threads for concurrent execution:

```ruby
require 'parallel'

# Parallel map with threads
def parallel_map_with_threads(data, &block)
  Parallel.map(data, in_threads: 8, &block)
end

# Usage
words = %w[hello world ruby parallel processing]
results = parallel_map_with_threads(words) do |word|
  word.upcase
  puts "Thread #{Thread.current.object_id} processing #{word}"
  word.upcase
end

puts "Results: #{results}"
```

### 3. Process Pool

Reusable pool of worker processes:

```ruby
class ProcessPool
  def initialize(size)
    @size = size
    @workers = []
    @queue = Queue.new
    @results = {}
    @next_id = 0
    @mutex = Mutex.new
    
    start_workers
  end
  
  def submit(&block)
    @mutex.synchronize do
      id = @next_id
      @next_id += 1
      @queue.push({ id: id, block: block })
      id
    end
  end
  
  def result(id, timeout = nil)
    start_time = Time.now
    
    loop do
      @mutex.synchronize do
        return @results[id] if @results.key?(id)
      end
      
      if timeout && (Time.now - start_time) > timeout
        raise TimeoutError, "Timeout waiting for result #{id}"
      end
      
      sleep(0.01)
    end
  end
  
  def shutdown
    @size.times { @queue.push({ shutdown: true }) }
    @workers.each(&:join)
  end
  
  private
  
  def start_workers
    @size.times do |i|
      @workers << Thread.new { worker_loop(i) }
    end
  end
  
  def worker_loop(worker_id)
    loop do
      task = @queue.pop
      break if task[:shutdown]
      
      begin
        result = task[:block].call
        @mutex.synchronize do
          @results[task[:id]] = { success: true, result: result, error: nil }
        end
      rescue => e
        @mutex.synchronize do
          @results[task[:id]] = { success: false, result: nil, error: e.message }
        end
      end
    end
  end
end

# Usage
pool = ProcessPool.new(4)

# Submit tasks
task_ids = []
10.times do |i|
  id = pool.submit do
    sleep(rand(0.1..0.5))
    "Task #{i} completed by worker #{Process.pid}"
  end
  task_ids << id
end

# Get results
task_ids.each do |id|
  result = pool.result(id)
  puts "Result: #{result[:result]}" if result[:success]
end

pool.shutdown
```

## 🔄 Parallel Algorithms

### 1. Parallel Sorting

```ruby
class ParallelSort
  def self.parallel_sort(array, threshold = 1000)
    return array.sort if array.size <= threshold
    
    # Split array
    mid = array.size / 2
    left = array[0...mid]
    right = array[mid..-1]
    
    # Sort halves in parallel
    sorted_left, sorted_right = Parallel.map([left, right]) do |part|
      parallel_sort(part, threshold)
    end
    
    # Merge sorted halves
    merge(sorted_left, sorted_right)
  end
  
  private
  
  def self.merge(left, right)
    result = []
    i = j = 0
    
    while i < left.size && j < right.size
      if left[i] <= right[j]
        result << left[i]
        i += 1
      else
        result << right[j]
        j += 1
      end
    end
    
    result.concat(left[i..-1]).concat(right[j..-1])
  end
end

# Usage
data = (1..10000).to_a.shuffle
sorted_data = ParallelSort.parallel_sort(data)
puts "Sorted: #{sorted_data.first(10)}"
```

### 2. Parallel Map-Reduce

```ruby
class ParallelMapReduce
  def self.map_reduce(data, map_func, reduce_func, options = {})
    chunk_size = options[:chunk_size] || 1000
    num_processes = options[:processes] || 4
    
    # Split data into chunks
    chunks = data.each_slice(chunk_size).to_a
    
    # Map phase - parallel
    mapped_results = Parallel.map(chunks, in_processes: num_processes) do |chunk|
      chunk.map(&map_func)
    end
    
    # Reduce phase
    mapped_results.reduce(&reduce_func)
  end
end

# Word count example
def word_count_parallel(text)
  words = text.split(/\s+/)
  
  ParallelMapReduce.map_reduce(
    words,
    ->(word) { [word, 1] },           # Map function
    ->(acc, pair) { acc.merge(pair[0] => (acc[pair[0]] || 0) + 1) }, # Reduce function
    chunk_size: 1000,
    processes: 4
  )
end

# Usage
text = "hello world hello ruby world parallel processing hello"
word_counts = word_count_parallel(text)
puts "Word counts: #{word_counts}"
```

### 3. Parallel Matrix Operations

```ruby
class ParallelMatrix
  def self.parallel_multiply(a, b)
    rows = a.size
    cols = b[0].size
    
    # Create result matrix
    result = Array.new(rows) { Array.new(cols, 0) }
    
    # Multiply rows in parallel
    Parallel.each((0...rows).to_a, in_threads: 4) do |i|
      (0...cols).each do |j|
        (0...a[0].size).each do |k|
          result[i][j] += a[i][k] * b[k][j]
        end
      end
    end
    
    result
  end
  
  def self.parallel_transpose(matrix)
    rows, cols = matrix.size, matrix[0].size
    result = Array.new(cols) { Array.new(rows) }
    
    Parallel.each((0...cols).to_a, in_threads: 4) do |j|
      (0...rows).each do |i|
        result[j][i] = matrix[i][j]
      end
    end
    
    result
  end
end

# Usage
matrix_a = [[1, 2], [3, 4], [5, 6]]
matrix_b = [[7, 8], [9, 10]]

result = ParallelMatrix.parallel_multiply(matrix_a, matrix_b)
puts "Matrix multiplication result: #{result}"
```

## 🌐 Distributed Processing

### 1. DRb (Distributed Ruby)

```ruby
require 'drb/drb'

# Server side
class CalculationServer
  def initialize
    @results = {}
  end
  
  def expensive_calculation(n)
    # Simulate expensive computation
    sleep(1)
    result = n * n
    @results[n] = result
    result
  end
  
  def batch_calculation(numbers)
    Parallel.map(numbers, in_processes: 4) { |n| expensive_calculation(n) }
  end
  
  def get_results
    @results
  end
end

# Start DRb server
server_uri = 'druby://localhost:8787'
DRb.start_service(server_uri, CalculationServer.new)
puts "DRb server running at #{server_uri}"

# Client side (in separate process)
class CalculationClient
  def initialize(server_uri)
    @server = DRbObject.new_with_uri(server_uri)
  end
  
  def calculate(n)
    @server.expensive_calculation(n)
  end
  
  def batch_calculate(numbers)
    @server.batch_calculation(numbers)
  end
  
  def get_all_results
    @server.get_results
  end
end

# Usage
client = CalculationClient.new('druby://localhost:8787')
result = client.calculate(42)
puts "Calculation result: #{result}"

batch_results = client.batch_calculate([1, 2, 3, 4, 5])
puts "Batch results: #{batch_results}"
```

### 2. Redis Job Queue

```ruby
require 'redis'
require 'json'

class RedisJobQueue
  def initialize(redis_url = 'redis://localhost:6379')
    @redis = Redis.new(url: redis_url)
    @queue_key = 'job_queue'
    @result_key = 'job_results'
  end
  
  def enqueue(job_data)
    job_id = SecureRandom.uuid
    job = { id: job_id, data: job_data, status: 'queued' }
    @redis.lpush(@queue_key, job.to_json)
    job_id
  end
  
  def dequeue
    job_json = @redis.brpop(@queue_key, timeout: 1)
    return nil unless job_json
    
    job = JSON.parse(job_json[1])
    job['status'] = 'processing'
    job
  end
  
  def complete_job(job_id, result)
    result_data = { id: job_id, result: result, status: 'completed' }
    @redis.hset(@result_key, job_id, result_data.to_json)
  end
  
  def get_result(job_id)
    result_json = @redis.hget(@result_key, job_id)
    return nil unless result_json
    
    JSON.parse(result_json)
  end
end

# Worker process
class JobWorker
  def initialize(queue)
    @queue = queue
  end
  
  def start
    loop do
      job = @queue.dequeue
      break unless job
      
      puts "Processing job: #{job['id']}"
      
      # Process the job
      result = process_job(job['data'])
      
      # Mark as complete
      @queue.complete_job(job['id'], result)
      puts "Completed job: #{job['id']}"
    end
  end
  
  private
  
  def process_job(data)
    case data['type']
    when 'calculation'
      data['number'] ** 2
    when 'string_processing'
      data['text'].upcase
    else
      "Unknown job type: #{data['type']}"
    end
  end
end

# Usage
queue = RedisJobQueue.new

# Enqueue jobs
job_ids = []
5.times do |i|
  job_id = queue.enqueue(type: 'calculation', number: i * 10)
  job_ids << job_id
end

# Start workers (in separate processes)
workers = 3.times.map { JobWorker.new(queue) }

# Wait for completion
sleep(5)

# Get results
job_ids.each do |job_id|
  result = queue.get_result(job_id)
  puts "Job #{job_id}: #{result}" if result
end
```

## ⚡ Performance Optimization

### 1. Load Balancing

```ruby
class LoadBalancer
  def initialize(workers)
    @workers = workers
    @current_index = 0
    @mutex = Mutex.new
  end
  
  def next_worker
    @mutex.synchronize do
      worker = @workers[@current_index]
      @current_index = (@current_index + 1) % @workers.size
      worker
    end
  end
  
  def execute(&block)
    worker = next_worker
    worker.submit(&block)
  end
end

# Usage
workers = 4.times.map { ProcessPool.new(2) }
balancer = LoadBalancer.new(workers)

# Distribute tasks
task_ids = []
20.times do |i|
  task_id = balancer.execute do
    sleep(rand(0.1..0.5))
    "Task #{i} completed"
  end
  task_ids << task_id
end

# Get results
task_ids.each do |task_id|
  result = workers.first.result(task_id)
  puts result[:result] if result[:success]
end
```

### 2. Work Stealing

```ruby
class WorkStealingQueue
  def initialize
    @queues = []
    @workers = []
    @mutex = Mutex.new
  end
  
  def add_worker
    worker_id = @queues.size
    queue = Queue.new
    @queues << queue
    
    worker = Thread.new do
      worker_loop(worker_id)
    end
    
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
  
  private
  
  def worker_loop(worker_id)
    loop do
      # Try to get work from own queue
      task = @queues[worker_id].pop(true)
      
      # If no work, try to steal from other queues
      if task.nil?
        task = steal_work(worker_id)
      end
      
      break unless task
      
      begin
        task.call
      rescue => e
        puts "Worker #{worker_id} error: #{e.message}"
      end
    end
  end
  
  def steal_work(thief_id)
    @queues.each_with_index do |queue, worker_id|
      next if worker_id == thief_id
      
      begin
        return queue.pop(true)
      rescue ThreadError
        # Queue empty, try next
        next
      end
    end
    
    nil
  end
end

# Usage
ws_queue = WorkStealingQueue.new
worker_ids = 4.times.map { ws_queue.add_worker }

# Submit tasks
20.times do |i|
  ws_queue.submit(proc do
    sleep(rand(0.1..0.3))
    puts "Task #{i} completed by worker #{Thread.current.object_id}"
  end)
end

sleep(5)
```

## 🎯 Best Practices

### 1. Choose the Right Concurrency Model

```ruby
# CPU-bound tasks: Use processes
def cpu_intensive_task
  Parallel.map(data, in_processes: 4) do |item|
    expensive_computation(item)
  end
end

# I/O-bound tasks: Use threads
def io_intensive_task
  Parallel.map(data, in_threads: 8) do |item|
    network_request(item)
  end
end

# Mixed workload: Use combination
def mixed_workload
  cpu_workers = Parallel.map(cpu_data, in_processes: 2) { |item| cpu_task(item) }
  io_workers = Parallel.map(io_data, in_threads: 4) { |item| io_task(item) }
  cpu_workers + io_workers
end
```

### 2. Avoid Shared State

```ruby
# Bad: Shared state causes race conditions
class BadSharedState
  def initialize
    @counter = 0
  end
  
  def process
    Parallel.map(data, in_threads: 4) do |item|
      @counter += 1  # Race condition!
      process_item(item)
    end
  end
end

# Good: No shared state
class GoodImmutableState
  def process
    Parallel.map(data, in_threads: 4) do |item|
      process_item(item)  # Each thread works independently
    end
  end
end
```

### 3. Handle Failures Gracefully

```ruby
class RobustParallelProcessor
  def initialize(retry_count = 3)
    @retry_count = retry_count
  end
  
  def process_with_retry(data, &block)
    Parallel.map(data, in_threads: 4) do |item|
      attempts = 0
      
      begin
        block.call(item)
      rescue => e
        attempts += 1
        if attempts <= @retry_count
          sleep(0.1 * attempts)
          retry
        else
          { error: e.message, item: item }
        end
      end
    end
  end
end
```

## 🚨 Common Pitfalls

### 1. Over-Parallelization

```ruby
# Bad: Too many processes/threads
def over_parallelization
  Parallel.map(data, in_processes: 100) do |item|
    process(item)  # System overhead > benefit
  end
end

# Good: Optimal parallelization
def optimal_parallelization
  cpu_count = Concurrent.processor_count
  Parallel.map(data, in_processes: cpu_count) do |item|
    process(item)
  end
end
```

### 2. Memory Issues

```ruby
# Bad: Large data duplication
def memory_intensive_parallel
  Parallel.map(large_dataset, in_processes: 4) do |data|
    process(data)  # Each process gets full dataset copy
  end
end

# Good: Chunked processing
def memory_efficient_parallel
  Parallel.each(large_dataset.each_slice(1000), in_processes: 4) do |chunk|
    process(chunk)  # Process manageable chunks
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Parallel Map**: Implement parallel map using threads
2. **Process Pool**: Create a simple process pool
3. **Job Queue**: Build a basic job queue system

### Intermediate Exercises

1. **Parallel Sort**: Implement parallel merge sort
2. **Work Stealing**: Create a work-stealing queue
3. **Load Balancer**: Build a load balancer for workers

### Advanced Exercises

1. **Distributed Computing**: Implement distributed processing with DRb
2. **Fault Tolerance**: Add failure handling to parallel systems
3. **Performance Tuning**: Optimize parallel algorithms

---

## 🎯 Summary

Parallel processing in Ruby provides:

- **Process-based parallelism** - True parallel execution
- **Thread-based concurrency** - Concurrent I/O operations
- **Distributed processing** - Multi-machine computation
- **Load balancing** - Efficient resource utilization
- **Fault tolerance** - Robust parallel systems

Master parallel processing to build high-performance, scalable Ruby applications!
