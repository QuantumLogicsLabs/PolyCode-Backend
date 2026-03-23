# Performance Testing in Ruby

## Overview

Performance testing is crucial for ensuring that Ruby applications meet performance requirements, handle expected load, and maintain responsiveness under various conditions. This guide covers comprehensive performance testing strategies, tools, and best practices for Ruby applications.

## Benchmarking and Profiling

### Basic Benchmarking
```ruby
require 'benchmark'

class PerformanceBenchmark
  def self.compare_algorithms
    data = Array.new(10000) { rand(10000) }
    
    Benchmark.bm(20) do |x|
      x.report("Array#sort:") do
        data.sort
      end
      
      x.report("QuickSort:") do
        quick_sort(data.dup)
      end
      
      x.report("MergeSort:") do
        merge_sort(data.dup)
      end
      
      x.report("BubbleSort:") do
        bubble_sort(data.dup)
      end
    end
  end

  def self.benchmark_string_operations
    strings = Array.new(1000) { "test_string_#{rand(1000)}" }
    
    Benchmark.bm(25) do |x|
      x.report("String concatenation (+):") do
        result = ""
        strings.each { |s| result += s }
        result
      end
      
      x.report("String concatenation (<<):") do
        result = ""
        strings.each { |s| result << s }
        result
      end
      
      x.report("Array#join:") do
        strings.join
      end
      
      x.report("String interpolation:") do
        "#{strings.join(' ')}"
      end
    end
  end

  def self.benchmark_memory_usage
    require 'objspace'
    
    initial_objects = ObjectSpace.count_objects
    
    # Create objects
    objects = Array.new(10000) { Object.new }
    
    after_creation = ObjectSpace.count_objects
    
    # Clean up
    objects = nil
    GC.start
    
    after_gc = ObjectSpace.count_objects
    
    puts "Object counts:"
    puts "  Initial: #{initial_objects[:TOTAL]}"
    puts "  After creation: #{after_creation[:TOTAL]}"
    puts "  After GC: #{after_gc[:TOTAL]}"
    puts "  Net increase: #{after_gc[:TOTAL] - initial_objects[:TOTAL]}"
  end

  def self.profile_method_performance
    require 'profiler'
    
    Profiler__.start_profile
    
    # Code to profile
    1000.times do
      data = Array.new(100) { rand(100) }
      data.sort.reverse.first(10)
    end
    
    Profiler__.stop_profile
    
    # Print results
    Profiler__.print_profile($stdout)
  end

  private

  def self.quick_sort(array)
    return array if array.length <= 1
    
    pivot = array[array.length / 2]
    left = array.select { |x| x < pivot }
    middle = array.select { |x| x == pivot }
    right = array.select { |x| x > pivot }
    
    quick_sort(left) + middle + quick_sort(right)
  end

  def self.merge_sort(array)
    return array if array.length <= 1
    
    mid = array.length / 2
    left = merge_sort(array[0...mid])
    right = merge_sort(array[mid...array.length])
    
    merge(left, right)
  end

  def self.merge(left, right)
    result = []
    
    while left.any? && right.any?
      if left.first <= right.first
        result << left.shift
      else
        result << right.shift
      end
    end
    
    result + left + right
  end

  def self.bubble_sort(array)
    n = array.length
    
    n.times do |i|
      (n - i - 1).times do |j|
        array[j], array[j + 1] = array[j + 1], array[j] if array[j] > array[j + 1]
      end
    end
    
    array
  end
end

# Usage examples
if __FILE__ == $0
  puts "=== Algorithm Comparison ==="
  PerformanceBenchmark.compare_algorithms
  
  puts "\n=== String Operations ==="
  PerformanceBenchmark.benchmark_string_operations
  
  puts "\n=== Memory Usage ==="
  PerformanceBenchmark.benchmark_memory_usage
end
```

### Advanced Profiling
```ruby
require 'benchmark'
require 'memory_profiler'
require 'ruby-prof'

class AdvancedProfiler
  def self.profile_memory_usage
    puts "=== Memory Profiling ==="
    
    report = MemoryProfiler.report do
      # Code to profile
      data = Array.new(10000) { "test_string_#{rand(1000)}" }
      processed = data.map(&:upcase).sort.uniq
      processed.each { |s| s.reverse }
    end
    
    puts "Total allocated: #{report.total_allocated_memsize} bytes"
    puts "Total retained: #{report.total_retained_memsize} bytes"
    puts "Objects allocated: #{report.total_allocated}"
    puts "Objects retained: #{report.total_retained}"
    
    puts "\nTop memory users:"
    report.allocated_memory_by_class.first(5).each do |class_name, data|
      puts "  #{class_name}: #{data[:total]} bytes (#{data[:count]} objects)"
    end
  end

  def self.profile_cpu_usage
    puts "=== CPU Profiling ==="
    
    RubyProf.start
    
    # Code to profile
    1000.times do
      data = Array.new(100) { rand(1000) }
      result = data.select(&:even?).map { |x| x * 2 }.sum
      Math.sqrt(result)
    end
    
    result = RubyProf.stop
    
    # Print different profiling reports
    puts "\nFlat Profile:"
    printer = RubyProf::FlatPrinter.new(result)
    printer.print(STDOUT)
    
    puts "\nCall Graph:"
    call_graph_printer = RubyProf::CallGraphPrinter.new(result)
    call_graph_printer.print(STDOUT)
    
    puts "\nGraph HTML:"
    graph_printer = RubyProf::GraphHtmlPrinter.new(result)
    graph_printer.print(File.open('profile_graph.html', 'w'))
    puts "Graph profile saved to profile_graph.html"
  end

  def self.profile_method_calls
    puts "=== Method Call Profiling ==="
    
    RubyProf.start
    
    # Example with method calls
    processor = DataProcessor.new
    
    100.times do
      processor.process_data(generate_test_data(100))
      processor.calculate_statistics
      processor.generate_report
    end
    
    result = RubyProf.stop
    
    printer = RubyProf::CallStackPrinter.new(result)
    printer.print(STDOUT)
  end

  def self.benchmark_with_warmup
    puts "=== Benchmark with Warm-up ==="
    
    # Warm-up
    10.times do
      Array.new(1000) { rand(10000) }.sort
    end
    
    # Actual benchmark
    Benchmark.bm(20) do |x|
      x.report("Sort 1000 elements:") do
        1000.times { Array.new(1000) { rand(10000) }.sort }
      end
      
      x.report("Sort 5000 elements:") do
        100.times { Array.new(5000) { rand(10000) }.sort }
      end
      
      x.report("Sort 10000 elements:") do
        10.times { Array.new(10000) { rand(10000) }.sort }
      end
    end
  end

  private

  def self.generate_test_data(size)
    Array.new(size) do
      {
        id: rand(10000),
        name: "Item #{rand(1000)}",
        value: rand(1000.0),
        category: %w[A B C D E].sample,
        active: rand < 0.8
      }
    end
  end
end

# Example class for profiling
class DataProcessor
  def initialize
    @data = []
    @statistics = {}
  end

  def process_data(data)
    @data = data.map do |item|
      {
        id: item[:id],
        name: item[:name].upcase,
        processed_value: item[:value] * 1.1,
        category_code: item[:category].ord,
        timestamp: Time.now
      }
    end
  end

  def calculate_statistics
    @statistics = {
      total_items: @data.length,
      active_items: @data.count { |item| item[:active] },
      average_value: @data.map { |item| item[:processed_value] }.sum / @data.length,
      categories: @data.map { |item| item[:category_code] }.uniq.sort
    }
  end

  def generate_report
    {
      summary: @statistics,
      items: @data.first(10),
      generated_at: Time.now
    }
  end
end
```

## Load Testing

### HTTP Load Testing
```ruby
require 'net/http'
require 'uri'
require 'thread'
require 'benchmark'

class HTTPLoadTester
  def initialize(base_url, options = {})
    @base_url = base_url
    @threads = options[:threads] || 10
    @requests_per_thread = options[:requests_per_thread] || 100
    @timeout = options[:timeout] || 30
    @results = []
    @mutex = Mutex.new
  end

  def run_load_test
    puts "Starting load test: #{@threads} threads, #{@requests_per_thread} requests per thread"
    
    threads = []
    start_time = Time.now
    
    @threads.times do |thread_id|
      threads << Thread.new do
        thread_results = []
        
        @requests_per_thread.times do |request_id|
          result = make_request(thread_id, request_id)
          thread_results << result
        end
        
        @mutex.synchronize do
          @results.concat(thread_results)
        end
      end
    end
    
    threads.each(&:join)
    end_time = Time.now
    
    generate_load_test_report(start_time, end_time)
  end

  def run_stress_test(duration_seconds = 60)
    puts "Starting stress test for #{duration_seconds} seconds"
    
    threads = []
    results = []
    mutex = Mutex.new
    start_time = Time.now
    
    @threads.times do |thread_id|
      threads << Thread.new do
        thread_results = []
        
        while Time.now - start_time < duration_seconds
          result = make_request(thread_id, rand(10000))
          thread_results << result
          sleep(0.1)  # Small delay between requests
        end
        
        mutex.synchronize do
          results.concat(thread_results)
        end
      end
    end
    
    threads.each(&:join)
    end_time = Time.now
    
    @results = results
    generate_stress_test_report(start_time, end_time, duration_seconds)
  end

  def run_spike_test(normal_duration = 30, spike_duration = 10, spike_multiplier = 5)
    puts "Starting spike test: #{normal_duration}s normal, #{spike_duration}s spike (#{spike_multiplier}x)"
    
    # Normal load phase
    puts "Normal load phase..."
    normal_results = run_phase(normal_duration, @threads)
    
    # Spike phase
    puts "Spike phase..."
    spike_threads = @threads * spike_multiplier
    spike_results = run_phase(spike_duration, spike_threads)
    
    # Recovery phase
    puts "Recovery phase..."
    recovery_results = run_phase(normal_duration, @threads)
    
    @results = normal_results + spike_results + recovery_results
    generate_spike_test_report(normal_results, spike_results, recovery_results)
  end

  private

  def make_request(thread_id, request_id)
    start_time = Time.now
    
    begin
      uri = URI("#{@base_url}/api/test")
      http = Net::HTTP.new(uri.host, uri.port)
      http.use_ssl = uri.scheme == 'https'
      http.read_timeout = @timeout
      
      request = Net::HTTP::Get.new(uri)
      request['User-Agent'] = "LoadTester/#{thread_id}/#{request_id}"
      
      response = http.request(request)
      end_time = Time.now
      
      {
        thread_id: thread_id,
        request_id: request_id,
        status_code: response.code.to_i,
        response_time: (end_time - start_time) * 1000,  # in milliseconds
        success: response.code.to_i < 400,
        timestamp: start_time
      }
    rescue => e
      end_time = Time.now
      
      {
        thread_id: thread_id,
        request_id: request_id,
        status_code: 0,
        response_time: (end_time - start_time) * 1000,
        success: false,
        error: e.message,
        timestamp: start_time
      }
    end
  end

  def run_phase(duration, threads)
    phase_results = []
    threads_mutex = Mutex.new
    
    thread_list = []
    start_time = Time.now
    
    threads.times do |thread_id|
      thread_list << Thread.new do
        thread_results = []
        
        while Time.now - start_time < duration
          result = make_request(thread_id, rand(10000))
          thread_results << result
          sleep(0.1)
        end
        
        threads_mutex.synchronize do
          phase_results.concat(thread_results)
        end
      end
    end
    
    thread_list.each(&:join)
    phase_results
  end

  def generate_load_test_report(start_time, end_time)
    total_requests = @results.length
    successful_requests = @results.count { |r| r[:success] }
    failed_requests = total_requests - successful_requests
    
    response_times = @results.map { |r| r[:response_time] }
    avg_response_time = response_times.sum / response_times.length
    min_response_time = response_times.min
    max_response_time = response_times.max
    
    # Percentiles
    sorted_times = response_times.sort
    p50 = sorted_times[sorted_times.length * 0.5]
    p95 = sorted_times[sorted_times.length * 0.95]
    p99 = sorted_times[sorted_times.length * 0.99]
    
    total_duration = end_time - start_time
    requests_per_second = total_requests / total_duration
    
    puts "\n=== Load Test Results ==="
    puts "Test Duration: #{total_duration.round(2)} seconds"
    puts "Total Requests: #{total_requests}"
    puts "Successful: #{successful_requests} (#{(successful_requests.to_f / total_requests * 100).round(2)}%)"
    puts "Failed: #{failed_requests} (#{(failed_requests.to_f / total_requests * 100).round(2)}%)"
    puts "Requests/Second: #{requests_per_second.round(2)}"
    puts ""
    puts "Response Times (ms):"
    puts "  Average: #{avg_response_time.round(2)}"
    puts "  Min: #{min_response_time.round(2)}"
    puts "  Max: #{max_response_time.round(2)}"
    puts "  50th percentile: #{p50.round(2)}"
    puts "  95th percentile: #{p95.round(2)}"
    puts "  99th percentile: #{p99.round(2)}"
    
    # Error analysis
    errors = @results.select { |r| !r[:success] && r[:error] }
    if errors.any?
      puts "\nErrors:"
      errors.group_by { |e| e[:error] }.each do |error, occurrences|
        puts "  #{error}: #{occurrences.length} times"
      end
    end
  end

  def generate_stress_test_report(start_time, end_time, duration)
    # Group results by time intervals
    interval = 5  # 5-second intervals
    time_series = {}
    
    @results.each do |result|
      time_bucket = ((result[:timestamp] - start_time) / interval).floor * interval
      time_series[time_bucket] ||= []
      time_series[time_bucket] << result
    end
    
    puts "\n=== Stress Test Results ==="
    puts "Test Duration: #{duration} seconds"
    puts "Total Requests: #{@results.length}"
    
    puts "\nTime Series Analysis (#{interval}s intervals):"
    time_series.sort.each do |time, results|
      success_rate = results.count { |r| r[:success] }.to_f / results.length * 100
      avg_response = results.map { |r| r[:response_time] }.sum / results.length
      rps = results.length / interval
      
      puts "  #{time.round(0)}-#{(time + interval).round(0)}s: #{rps.round(1)} RPS, #{success_rate.round(1)}% success, #{avg_response.round(1)}ms avg"
    end
  end

  def generate_spike_test_report(normal_results, spike_results, recovery_results)
    normal_rps = normal_results.length / 30.0
    spike_rps = spike_results.length / 10.0
    recovery_rps = recovery_results.length / 30.0
    
    normal_success_rate = normal_results.count { |r| r[:success] }.to_f / normal_results.length * 100
    spike_success_rate = spike_results.count { |r| r[:success] }.to_f / spike_results.length * 100
    recovery_success_rate = recovery_results.count { |r| r[:success] }.to_f / recovery_results.length * 100
    
    puts "\n=== Spike Test Results ==="
    puts "Normal Phase: #{normal_rps.round(1)} RPS, #{normal_success_rate.round(1)}% success"
    puts "Spike Phase: #{spike_rps.round(1)} RPS, #{spike_success_rate.round(1)}% success"
    puts "Recovery Phase: #{recovery_rps.round(1)} RPS, #{recovery_success_rate.round(1)}% success"
    puts ""
    puts "Spike Multiplier: #{(spike_rps / normal_rps).round(2)}x"
    puts "Performance Degradation: #{(100 - spike_success_rate).round(1)}%"
    puts "Recovery: #{(recovery_success_rate >= normal_success_rate * 0.9) ? 'Successful' : 'Incomplete'}"
  end
end

# Usage examples
if __FILE__ == $0
  # Example usage (replace with actual URL)
  # tester = HTTPLoadTester.new('http://localhost:3000', threads: 20, requests_per_thread: 50)
  # tester.run_load_test
  
  puts "HTTP Load Tester ready. Uncomment example usage to run tests."
end
```

### Database Load Testing
```ruby
require 'sqlite3'
require 'benchmark'
require 'thread'

class DatabaseLoadTester
  def initialize(database_path = ':memory:')
    @db = SQLite3::Database.new(database_path)
    setup_database
  end

  def setup_database
    @db.execute <<-SQL
      CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
      )
    SQL
    
    @db.execute <<-SQL
      CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        status TEXT DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users (id)
      )
    SQL
    
    @db.execute <<-SQL
      CREATE INDEX idx_orders_user_id ON orders(user_id)
      CREATE INDEX idx_users_email ON users(email)
    SQL
  end

  def test_insert_performance(record_count = 10000)
    puts "Testing insert performance with #{record_count} records"
    
    times = Benchmark.measure do
      @db.transaction do
        record_count.times do |i|
          @db.execute(
            "INSERT INTO users (name, email) VALUES (?, ?)",
            ["User #{i}", "user#{i}@example.com"]
          )
        end
      end
    end
    
    puts "Inserted #{record_count} records in #{times.real.round(3)}s"
    puts "Rate: #{(record_count / times.real).round(0)} records/second"
  end

  def test_query_performance
    # Insert test data
    1000.times do |i|
      @db.execute(
        "INSERT INTO users (name, email) VALUES (?, ?)",
        ["User #{i}", "user#{i}@example.com"]
      )
      
      @db.execute(
        "INSERT INTO orders (user_id, total, status) VALUES (?, ?, ?)",
        [i + 1, rand(100.0), ['pending', 'completed', 'cancelled'].sample]
      )
    end
    
    puts "Testing query performance"
    
    Benchmark.bm(30) do |x|
      x.report("Simple SELECT:") do
        1000.times { @db.execute("SELECT * FROM users LIMIT 10") }
      end
      
      x.report("SELECT with WHERE:") do
        1000.times { @db.execute("SELECT * FROM users WHERE id = ?", rand(1000)) }
      end
      
      x.report("SELECT with JOIN:") do
        100.times do
          @db.execute <<-SQL
            SELECT u.name, o.total, o.status 
            FROM users u 
            JOIN orders o ON u.id = o.user_id 
            WHERE u.id = ?
          SQL, rand(1000)
        end
      end
      
      x.report("Aggregate query:") do
        100.times do
          @db.execute <<-SQL
            SELECT status, COUNT(*), AVG(total) 
            FROM orders 
            GROUP BY status
          SQL
        end
      end
      
      x.report("Complex query:") do
        50.times do
          @db.execute <<-SQL
            SELECT u.name, COUNT(o.id) as order_count, COALESCE(SUM(o.total), 0) as total_spent
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            WHERE u.created_at > datetime('now', '-30 days')
            GROUP BY u.id
            HAVING order_count > 0
            ORDER BY total_spent DESC
            LIMIT 10
          SQL
        end
      end
    end
  end

  def test_concurrent_access
    puts "Testing concurrent database access"
    
    threads = []
    results = []
    mutex = Mutex.new
    
    10.times do |thread_id|
      threads << Thread.new do
        thread_results = []
        
        100.times do |i|
          start_time = Time.now
          
          # Perform various operations
          @db.execute("INSERT INTO users (name, email) VALUES (?, ?)", 
                     ["Thread#{thread_id}User#{i}", "thread#{thread_id}user#{i}@example.com"])
          
          user_id = @db.last_insert_row_id
          @db.execute("SELECT * FROM users WHERE id = ?", user_id)
          
          thread_results << Time.now - start_time
        end
        
        mutex.synchronize do
          results.concat(thread_results)
        end
      end
    end
    
    threads.each(&:join)
    
    avg_time = results.sum / results.length
    max_time = results.max
    
    puts "Concurrent access results:"
    puts "  Average operation time: #{(avg_time * 1000).round(2)}ms"
    puts "  Maximum operation time: #{(max_time * 1000).round(2)}ms"
    puts "  Total operations: #{results.length}"
  end

  def test_transaction_performance
    puts "Testing transaction performance"
    
    Benchmark.bm(25) do |x|
      x.report("Without transaction:") do
        1000.times do |i|
          @db.execute("INSERT INTO users (name, email) VALUES (?, ?)", 
                     ["User #{i}", "user#{i}@example.com"])
        end
      end
      
      x.report("With transaction:") do
        @db.transaction do
          1000.times do |i|
            @db.execute("INSERT INTO users (name, email) VALUES (?, ?)", 
                       ["User #{i}", "user#{i}@example.com"])
          end
        end
      end
    end
  end

  def cleanup
    @db.execute("DELETE FROM orders")
    @db.execute("DELETE FROM users")
  end

  def close
    @db.close
  end
end

# Usage examples
if __FILE__ == $0
  tester = DatabaseLoadTester.new
  
  tester.test_insert_performance(10000)
  tester.test_query_performance
  tester.test_concurrent_access
  tester.test_transaction_performance
  
  tester.close
end
```

## Memory and Resource Testing

### Memory Leak Detection
```ruby
require 'objspace'
require 'benchmark'

class MemoryLeakDetector
  def initialize
    @baseline = nil
    @snapshots = []
  end

  def capture_baseline
    GC.start
    @baseline = ObjectSpace.count_objects
    puts "Baseline captured: #{@baseline[:TOTAL]} objects"
  end

  def capture_snapshot(label)
    GC.start
    snapshot = {
      label: label,
      timestamp: Time.now,
      objects: ObjectSpace.count_objects,
      memory_usage: get_memory_usage
    }
    
    @snapshots << snapshot
    
    if @baseline
      delta = snapshot[:objects][:TOTAL] - @baseline[:TOTAL]
      puts "#{label}: #{snapshot[:objects][:TOTAL]} objects (#{delta >= 0 ? '+' : ''}#{delta} from baseline)"
    else
      puts "#{label}: #{snapshot[:objects][:TOTAL]} objects"
    end
  end

  def analyze_memory_growth
    return if @snapshots.length < 2
    
    puts "\n=== Memory Growth Analysis ==="
    
    @snapshots.each_cons(2) do |prev, current|
      time_diff = current[:timestamp] - prev[:timestamp]
      object_diff = current[:objects][:TOTAL] - prev[:objects][:TOTAL]
      memory_diff = current[:memory_usage] - prev[:memory_usage]
      
      objects_per_second = object_diff / time_diff
      memory_per_second = memory_diff / time_diff
      
      puts "#{prev[:label]} → #{current[:label]}:"
      puts "  Time: #{time_diff.round(2)}s"
      puts "  Objects: #{object_diff >= 0 ? '+' : ''}#{object_diff} (#{objects_per_second.round(1)}/s)"
      puts "  Memory: #{memory_diff >= 0 ? '+' : ''}#{(memory_diff / 1_000_000).round(2)}MB (#{(memory_per_second / 1_000_000).round(2)}MB/s)"
    end
  end

  def detect_potential_leaks
    return if @snapshots.empty?
    
    latest = @snapshots.last
    return unless @baseline
    
    puts "\n=== Potential Memory Leaks ==="
    
    # Compare object counts by class
    baseline_classes = get_object_classes(@baseline)
    latest_classes = get_object_classes(latest)
    
    potential_leaks = []
    
    latest_classes.each do |class_name, count|
      baseline_count = baseline_classes[class_name] || 0
      growth = count - baseline_count
      
      if growth > 1000  # Threshold for potential leak
        potential_leaks << {
          class: class_name,
          baseline: baseline_count,
          current: count,
          growth: growth
        }
      end
    end
    
    if potential_leaks.any?
      puts "Classes with significant growth:"
      potential_leaks.sort_by { |leak| -leak[:growth] }.each do |leak|
        puts "  #{leak[:class]}: #{leak[:baseline]} → #{leak[:growth]} (+#{leak[:growth]})"
      end
    else
      puts "No significant memory leaks detected"
    end
  end

  def generate_memory_report
    return if @snapshots.empty?
    
    puts "\n=== Memory Report ==="
    
    initial_snapshot = @snapshots.first
    final_snapshot = @snapshots.last
    
    total_time = final_snapshot[:timestamp] - initial_snapshot[:timestamp]
    object_growth = final_snapshot[:objects][:TOTAL] - initial_snapshot[:objects][:TOTAL]
    memory_growth = final_snapshot[:memory_usage] - initial_snapshot[:memory_usage]
    
    puts "Test Duration: #{total_time.round(2)}s"
    puts "Object Growth: #{object_growth} (#{(object_growth / total_time).round(1)}/s)"
    puts "Memory Growth: #{(memory_growth / 1_000_000).round(2)}MB (#{(memory_growth / total_time / 1_000_000).round(2)}MB/s)"
    
    # Detailed breakdown
    puts "\nDetailed Object Counts:"
    final_snapshot[:objects].sort_by { |type, count| -count }.first(10).each do |type, count|
      puts "  #{type}: #{count}"
    end
  end

  private

  def get_memory_usage
    # Simple memory usage approximation
    GC.stat[:heap_allocated_pages] * GC.stat[:heap_page_size]
  end

  def get_object_classes(snapshot)
    # This is a simplified version - in practice you'd need more sophisticated tracking
    {
      'STRING' => snapshot[:T_STRING] || 0,
      'ARRAY' => snapshot[:T_ARRAY] || 0,
      'HASH' => snapshot[:T_HASH] || 0,
      'OBJECT' => snapshot[:T_DATA] || 0
    }
  end
end

# Memory leak test example
class MemoryLeakTest
  def self.test_potential_leaks
    detector = MemoryLeakDetector.new
    
    detector.capture_baseline
    
    # Test 1: Normal operations
    detector.capture_snapshot("Before operations")
    
    1000.times do |i|
      data = "test_data_#{i}"
      processed = data.upcase.reverse
      processed.length
    end
    
    detector.capture_snapshot("After operations")
    
    # Test 2: Potential leak (intentional)
    detector.capture_snapshot("Before leak test")
    
    @leaky_array = []
    1000.times do |i|
      @leaky_array << "leaky_data_#{i}" * 100  # Large strings
    end
    
    detector.capture_snapshot("After leak test")
    
    # Analysis
    detector.analyze_memory_growth
    detector.detect_potential_leaks
    detector.generate_memory_report
    
    # Clean up
    @leaky_array = nil
    GC.start
    detector.capture_snapshot("After cleanup")
  end
end
```

### Resource Usage Monitoring
```ruby
require 'benchmark'

class ResourceMonitor
  def initialize
    @cpu_samples = []
    @memory_samples = []
    @file_descriptors = []
    @thread_counts = []
  end

  def start_monitoring(interval = 1.0)
    @monitoring = true
    @monitor_thread = Thread.new do
      while @monitoring
        sample_resources
        sleep(interval)
      end
    end
  end

  def stop_monitoring
    @monitoring = false
    @monitor_thread&.join
  end

  def monitor_during_operation
    start_monitoring(0.5)
    
    start_time = Time.now
    
    begin
      yield
    ensure
      end_time = Time.now
      stop_monitoring
      
      generate_resource_report(start_time, end_time)
    end
  end

  def monitor_database_operations
    puts "=== Database Resource Monitoring ==="
    
    monitor_during_operation do
      # Simulate database operations
      1000.times do |i|
        # Simulate query
        sleep(0.001)
        
        # Simulate data processing
        data = Array.new(100) { rand(1000) }
        data.sort.select(&:even?)
        
        # Simulate network I/O
        sleep(0.0005)
      end
    end
  end

  def monitor_file_operations
    puts "=== File I/O Resource Monitoring ==="
    
    monitor_during_operation do
      # Simulate file operations
      100.times do |i|
        # Write operation
        file_content = "Test data #{i}" * 1000
        File.write("temp_file_#{i}.txt", file_content)
        
        # Read operation
        content = File.read("temp_file_#{i}.txt")
        content.length
        
        # Cleanup
        File.delete("temp_file_#{i}.txt")
      end
    end
  end

  def monitor_concurrent_operations
    puts "=== Concurrent Operations Resource Monitoring ==="
    
    monitor_during_operation do
      threads = []
      
      10.times do |thread_id|
        threads << Thread.new do
          100.times do |i|
            # CPU-intensive work
            result = (1..1000).sum { |n| n * n }
            Math.sqrt(result)
            
            # Memory allocation
            data = Array.new(100) { "thread_#{thread_id}_data_#{i}" }
            data.join('_').upcase.reverse
            
            sleep(0.01)
          end
        end
      end
      
      threads.each(&:join)
    end
  end

  private

  def sample_resources
    timestamp = Time.now
    
    # CPU usage (simplified)
    cpu_usage = get_cpu_usage
    @cpu_samples << { timestamp: timestamp, usage: cpu_usage }
    
    # Memory usage
    memory_usage = get_memory_usage
    @memory_samples << { timestamp: timestamp, usage: memory_usage }
    
    # File descriptors (Unix systems only)
    if RUBY_PLATFORM =~ /linux|darwin|bsd/
      fd_count = get_file_descriptor_count
      @file_descriptors << { timestamp: timestamp, count: fd_count }
    end
    
    # Thread count
    thread_count = Thread.list.length
    @thread_counts << { timestamp: timestamp, count: thread_count }
  end

  def get_cpu_usage
    # Simplified CPU usage calculation
    # In practice, you'd use system-specific methods
    start_time = Time.now
    
    # Perform some work to measure CPU time
    1000.times { Math.sqrt(rand(10000)) }
    
    end_time = Time.now
    work_time = end_time - start_time
    
    # This is a very rough approximation
    [work_time * 100, 100].min  # Cap at 100%
  end

  def get_memory_usage
    GC.stat[:heap_allocated_pages] * GC.stat[:heap_page_size]
  end

  def get_file_descriptor_count
    # Unix systems only
    Dir.glob("/proc/#{Process.pid}/fd/*").length
  rescue
    0
  end

  def generate_resource_report(start_time, end_time)
    duration = end_time - start_time
    
    puts "\n=== Resource Usage Report ==="
    puts "Operation Duration: #{duration.round(3)}s"
    puts ""
    
    # CPU usage
    if @cpu_samples.any?
      avg_cpu = @cpu_samples.map { |s| s[:usage] }.sum / @cpu_samples.length
      max_cpu = @cpu_samples.map { |s| s[:usage] }.max
      
      puts "CPU Usage:"
      puts "  Average: #{avg_cpu.round(2)}%"
      puts "  Maximum: #{max_cpu.round(2)}%"
    end
    
    # Memory usage
    if @memory_samples.any?
      initial_memory = @memory_samples.first[:usage]
      final_memory = @memory_samples.last[:usage]
      memory_growth = final_memory - initial_memory
      
      avg_memory = @memory_samples.map { |s| s[:usage] }.sum / @memory_samples.length
      max_memory = @memory_samples.map { |s| s[:usage] }.max
      
      puts "Memory Usage:"
      puts "  Initial: #{(initial_memory / 1_000_000).round(2)}MB"
      puts "  Final: #{(final_memory / 1_000_000).round(2)}MB"
      puts "  Growth: #{(memory_growth / 1_000_000).round(2)}MB"
      puts "  Average: #{(avg_memory / 1_000_000).round(2)}MB"
      puts "  Maximum: #{(max_memory / 1_000_000).round(2)}MB"
    end
    
    # File descriptors
    if @file_descriptors.any?
      initial_fd = @file_descriptors.first[:count]
      final_fd = @file_descriptors.last[:count]
      max_fd = @file_descriptors.map { |s| s[:count] }.max
      
      puts "File Descriptors:"
      puts "  Initial: #{initial_fd}"
      puts "  Final: #{final_fd}"
      puts "  Maximum: #{max_fd}"
    end
    
    # Thread count
    if @thread_counts.any?
      max_threads = @thread_counts.map { |s| s[:count] }.max
      
      puts "Thread Count:"
      puts "  Maximum: #{max_threads}"
    end
  end
end

# Usage examples
if __FILE__ == $0
  monitor = ResourceMonitor.new
  
  monitor.monitor_database_operations
  monitor.monitor_file_operations
  monitor.monitor_concurrent_operations
end
```

## Best Practices

1. **Baseline Establishment**: Establish performance baselines before optimization
2. **Consistent Testing**: Use consistent test environments and data
3. **Realistic Scenarios**: Test with realistic data volumes and user patterns
4. **Multiple Metrics**: Monitor CPU, memory, I/O, and network performance
5. **Automated Testing**: Integrate performance tests into CI/CD pipelines
6. **Regular Monitoring**: Continuously monitor performance in production
7. **Profiling Tools**: Use appropriate profiling tools for different scenarios

## Conclusion

Performance testing is essential for ensuring Ruby applications meet performance requirements and can handle expected loads. By using comprehensive benchmarking, load testing, and resource monitoring techniques, you can identify performance bottlenecks, optimize critical paths, and maintain application performance over time.

## Further Reading

- [Ruby Benchmark Library](https://ruby-doc.org/stdlib-3.0.0/libdoc/benchmark/rdoc/Benchmark.html)
- [Ruby Prof](https://github.com/ruby-prof/ruby-prof)
- [Memory Profiler](https://github.com/SamSaffron/memory_profiler)
- [Performance Testing Best Practices](https://www.guru99.com/performance-testing.html)
- [Load Testing Strategies](https://loadtestingtool.com/what-is-load-testing/)
