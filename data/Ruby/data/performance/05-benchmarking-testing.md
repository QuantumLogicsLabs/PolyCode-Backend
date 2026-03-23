# Benchmarking and Testing in Ruby
# Comprehensive guide to performance testing and benchmarking

## 🎯 Overview

Benchmarking and testing are essential for ensuring Ruby application performance. This guide covers performance testing methodologies, benchmarking tools, and optimization validation techniques.

## 📊 Benchmarking Fundamentals

### 1. Ruby Benchmark Library

Using Ruby's built-in benchmarking capabilities:

```ruby
require 'benchmark'

class RubyBenchmarkExamples
  def self.basic_benchmarking
    puts "Basic Ruby Benchmarking:"
    puts "=" * 50
    
    # Simple benchmark
    time = Benchmark.measure do
      # Code to benchmark
      result = []
      10000.times { |i| result << i * 2 }
    end
    
    puts "Benchmark result:"
    puts "  User CPU time: #{time.utime.round(6)}s"
    puts "  System CPU time: #{time.stime.round(6)}s"
    puts "  Total CPU time: #{time.total.round(6)}s"
    puts "  Real time: #{time.real.round(6)}s"
  end
  
  def self.comparative_benchmarking
    puts "\nComparative Benchmarking:"
    puts "=" * 50
    
    # Compare different approaches
    data = (1..10000).to_a
    
    Benchmark.bm(20) do |x|
      x.report("Array.map") do
        data.map { |n| n * 2 }
      end
      
      x.report("Array.collect") do
        data.collect { |n| n * 2 }
      end
      
      x.report("Each with new array") do
        result = []
        data.each { |n| result << n * 2 }
        result
      end
      
      x.report("For loop") do
        result = []
        for i in 0...data.length
          result << data[i] * 2
        end
        result
      end
    end
  end
  
  def self.memory_benchmarking
    puts "\nMemory Benchmarking:"
    puts "=" * 50
    
    require 'objspace'
    
    # Memory usage comparison
    methods = {
      'String concatenation' => -> {
        str = ""
        1000.times { |i| str += "item_#{i}" }
        str
      },
      
      'Array.join' => -> {
        items = 1000.times.map { |i| "item_#{i}" }
        items.join
      },
      
      'StringIO' => -> {
        io = StringIO.new
        1000.times { |i| io.write("item_#{i}") }
        io.string
      }
    }
    
    methods.each do |name, method|
      GC.start
      before = ObjectSpace.count_objects[:T_STRING]
      
      result = method.call
      
      GC.start
      after = ObjectSpace.count_objects[:T_STRING]
      
      puts "#{name}: #{after - before} strings created"
    end
  end
  
  def self.iterative_benchmarking
    puts "\nIterative Benchmarking:"
    puts "=" * 50
    
    # Benchmark with multiple iterations
    iterations = 100
    
    Benchmark.bm(20) do |x|
      x.report("Hash lookup") do
        hash = {}
        1000.times { |i| hash[i] = i * 2 }
        
        iterations.times do
          hash[rand(1000)]
        end
      end
      
      x.report("Array include?") do
        array = (1..1000).to_a
        
        iterations.times do
          array.include?(rand(1000))
        end
      end
      
      x.report("Set include?") do
        set = Set.new((1..1000).to_a)
        
        iterations.times do
          set.include?(rand(1000))
        end
      end
    end
  end
end

# Run basic benchmarking examples
RubyBenchmarkExamples.basic_benchmarking
RubyBenchmarkExamples.comparative_benchmarking
RubyBenchmarkExamples.memory_benchmarking
RubyBenchmarkExamples.iterative_benchmarking
```

### 2. Custom Benchmark Framework

Build a comprehensive benchmarking framework:

```ruby
class BenchmarkFramework
  def initialize(name)
    @name = name
    @results = []
    @setup_proc = nil
    @teardown_proc = nil
  end
  
  def setup(&block)
    @setup_proc = block
  end
  
  def teardown(&block)
    @teardown_proc = block
  end
  
  def benchmark(name, &block)
    # Warm up
    3.times { block.call }
    
    # Run benchmark
    times = []
    iterations = 10
    
    iterations.times do
      @setup_proc.call if @setup_proc
      
      time = Benchmark.measure do
        block.call
      end
      
      times << time.real
      
      @teardown_proc.call if @teardown_proc
    end
    
    # Calculate statistics
    avg_time = times.sum / times.length
    min_time = times.min
    max_time = times.max
    std_dev = calculate_std_dev(times, avg_time)
    
    @results << {
      name: name,
      avg_time: avg_time,
      min_time: min_time,
      max_time: max_time,
      std_dev: std_dev,
      iterations: iterations
    }
  end
  
  def compare(*names)
    puts "\n#{@name} - Comparison:"
    puts "=" * 50
    
    comparison_results = @results.select { |r| names.include?(r[:name]) }
    
    # Find fastest
    fastest = comparison_results.min_by { |r| r[:avg_time] }
    
    comparison_results.each do |result|
      relative_speed = result[:avg_time] / fastest[:avg_time]
      speedup = relative_speed == 1 ? "baseline" : "#{relative_speed.round(2)}x slower"
      
      puts "#{result[:name]}:"
      puts "  Average: #{result[:avg_time].round(6)}s"
      puts "  Min: #{result[:min_time].round(6)}s"
      puts "  Max: #{result[:max_time].round(6)}s"
      puts "  Std Dev: #{result[:std_dev].round(6)}s"
      puts "  Speed: #{speedup}"
      puts
    end
  end
  
  def report
    puts "\n#{@name} - Full Report:"
    puts "=" * 50
    
    @results.each do |result|
      puts "#{result[:name]}:"
      puts "  Average: #{result[:avg_time].round(6)}s"
      puts "  Min: #{result[:min_time].round(6)}s"
      puts "  Max: #{result[:max_time].round(6)}s"
      puts "  Std Dev: #{result[:std_dev].round(6)}s"
      puts "  Iterations: #{result[:iterations]}"
      puts
    end
  end
  
  private
  
  def calculate_std_dev(values, mean)
    variance = values.sum { |v| (v - mean) ** 2 } / values.length
    Math.sqrt(variance)
  end
end

# Usage example
framework = BenchmarkFramework.new("String Operations")

framework.setup do
  @data = (1..1000).to_a
end

framework.benchmark("String interpolation") do
  @data.map { |i| "Number #{i}" }
end

framework.benchmark("String concatenation") do
  @data.map { |i| "Number " + i.to_s }
end

framework.benchmark("String.format") do
  @data.map { |i| format("Number %d", i) }
end

framework.compare("String interpolation", "String concatenation", "String.format")
framework.report
```

## 🧪 Performance Testing

### 1. Load Testing

Simulate load and stress testing:

```ruby
class LoadTester
  def initialize(base_url, max_concurrent: 10, duration: 60)
    @base_url = base_url
    @max_concurrent = max_concurrent
    @duration = duration
    @results = []
    @mutex = Mutex.new
  end
  
  def run_load_test
    puts "Load Test Configuration:"
    puts "  URL: #{@base_url}"
    puts "  Max concurrent: #{@max_concurrent}"
    puts "  Duration: #{@duration}s"
    puts "=" * 50
    
    start_time = Time.now
    threads = []
    
    @max_concurrent.times do |i|
      threads << Thread.new do
        run_user_simulation(i, start_time)
      end
    end
    
    threads.each(&:join)
    
    analyze_results
  end
  
  def run_stress_test
    puts "Stress Test - Gradual Load Increase"
    puts "=" * 50
    
    start_time = Time.now
    threads = []
    current_concurrent = 1
    
    while Time.now - start_time < @duration
      # Add more threads gradually
      if current_concurrent <= @max_concurrent
        threads << Thread.new do
          run_user_simulation(current_concurrent, start_time)
        end
        current_concurrent += 1
        sleep(2)  # Wait before adding more load
      end
      
      sleep(1)
    end
    
    threads.each(&:join)
    analyze_results
  end
  
  private
  
  def run_user_simulation(user_id, start_time)
    end_time = start_time + @duration
    
    while Time.now < end_time
      request_start = Time.now
      
      begin
        # Simulate HTTP request
        response = simulate_http_request
        
        request_end = Time.now
        duration = request_end - request_start
        
        record_result(user_id, duration, response[:status])
        
        # Think time between requests
        sleep(rand(0.1..1.0))
        
      rescue => e
        record_result(user_id, 0, 500)  # Error status
      end
    end
  end
  
  def simulate_http_request
    # Simulate different response times and statuses
    case rand(100)
    when 0..5
      { status: 500, response_time: rand(0.5..2.0) }  # 5% errors
    when 6..10
      { status: 404, response_time: rand(0.1..0.5) }  # 5% not found
    else
      { status: 200, response_time: rand(0.05..0.3) }  # 90% success
    end
  end
  
  def record_result(user_id, duration, status)
    @mutex.synchronize do
      @results << {
        user_id: user_id,
        duration: duration,
        status: status,
        timestamp: Time.now
      }
    end
  end
  
  def analyze_results
    return if @results.empty?
    
    puts "\nLoad Test Results:"
    puts "=" * 50
    
    total_requests = @results.length
    successful_requests = @results.count { |r| r[:status] == 200 }
    error_requests = total_requests - successful_requests
    
    avg_response_time = @results.map { |r| r[:duration] }.sum / total_requests
    min_response_time = @results.map { |r| r[:duration] }.min
    max_response_time = @results.map { |r| r[:duration] }.max
    
    # Calculate percentiles
    sorted_times = @results.map { |r| r[:duration] }.sort
    p50 = sorted_times[sorted_times.length * 0.5]
    p95 = sorted_times[sorted_times.length * 0.95]
    p99 = sorted_times[sorted_times.length * 0.99]
    
    # Calculate requests per second
    test_duration = @results.last[:timestamp] - @results.first[:timestamp]
    rps = total_requests / test_duration
    
    puts "Total requests: #{total_requests}"
    puts "Successful requests: #{successful_requests} (#{(successful_requests.to_f / total_requests * 100).round(2)}%)"
    puts "Error requests: #{error_requests} (#{(error_requests.to_f / total_requests * 100).round(2)}%)"
    puts "Requests per second: #{rps.round(2)}"
    puts "Average response time: #{avg_response_time.round(4)}s"
    puts "Min response time: #{min_response_time.round(4)}s"
    puts "Max response time: #{max_response_time.round(4)}s"
    puts "50th percentile: #{p50.round(4)}s"
    puts "95th percentile: #{p95.round(4)}s"
    puts "99th percentile: #{p99.round(4)}s"
  end
end

# Usage example
load_tester = LoadTester.new("http://example.com/api", max_concurrent: 20, duration: 30)
load_tester.run_load_test

# Stress test
stress_tester = LoadTester.new("http://example.com/api", max_concurrent: 50, duration: 60)
stress_tester.run_stress_test
```

### 2. Database Performance Testing

Test database query performance:

```ruby
class DatabasePerformanceTester
  def initialize(db_config)
    @db_config = db_config
    @results = []
  end
  
  def setup_test_data
    puts "Setting up test data..."
    
    # Create test tables
    setup_tables
    
    # Insert test data
    insert_test_data
    
    puts "Test data setup complete"
  end
  
  def benchmark_queries
    puts "\nDatabase Query Benchmark:"
    puts "=" * 50
    
    queries = [
      {
        name: "Simple SELECT",
        sql: "SELECT * FROM users WHERE id = ?",
        params: [1]
      },
      {
        name: "SELECT with WHERE clause",
        sql: "SELECT * FROM users WHERE age > ? AND status = ?",
        params: [25, 'active']
      },
      {
        name: "JOIN query",
        sql: "SELECT u.*, o.order_count FROM users u LEFT JOIN (SELECT user_id, COUNT(*) as order_count FROM orders GROUP BY user_id) o ON u.id = o.user_id WHERE u.status = ?",
        params: ['active']
      },
      {
        name: "Aggregate query",
        sql: "SELECT status, COUNT(*) as count, AVG(age) as avg_age FROM users GROUP BY status",
        params: []
      },
      {
        name: "Complex subquery",
        sql: "SELECT * FROM users WHERE id IN (SELECT user_id FROM orders WHERE total > ?)",
        params: [100]
      }
    ]
    
    queries.each do |query|
      benchmark_query(query)
    end
    
    print_query_results
  end
  
  def test_concurrent_access
    puts "\nConcurrent Database Access Test:"
    puts "=" * 50
    
    threads = []
    results = []
    mutex = Mutex.new
    
    10.times do |i|
      threads << Thread.new do
        thread_results = []
        
        100.times do
          start_time = Time.now
          
          # Simulate database operation
          result = simulate_database_operation
          
          end_time = Time.now
          duration = end_time - start_time
          
          thread_results << duration
        end
        
        mutex.synchronize do
          results.concat(thread_results)
        end
      end
    end
    
    threads.each(&:join)
    
    # Analyze concurrent results
    avg_time = results.sum / results.length
    min_time = results.min
    max_time = results.max
    
    puts "Concurrent operations: #{results.length}"
    puts "Average time: #{avg_time.round(4)}s"
    puts "Min time: #{min_time.round(4)}s"
    puts "Max time: #{max_time.round(4)}s"
  end
  
  private
  
  def setup_tables
    # Simulate table creation
    puts "Creating users table..."
    puts "Creating orders table..."
  end
  
  def insert_test_data
    # Simulate data insertion
    puts "Inserting 10,000 users..."
    puts "Inserting 50,000 orders..."
  end
  
  def benchmark_query(query)
    # Warm up
    3.times { simulate_database_operation(query) }
    
    # Benchmark
    times = []
    iterations = 100
    
    iterations.times do
      start_time = Time.now
      
      result = simulate_database_operation(query)
      
      end_time = Time.now
      times << (end_time - start_time)
    end
    
    avg_time = times.sum / times.length
    min_time = times.min
    max_time = times.max
    
    @results << {
      name: query[:name],
      avg_time: avg_time,
      min_time: min_time,
      max_time: max_time,
      iterations: iterations
    }
  end
  
  def simulate_database_operation(query = nil)
    # Simulate database operation with varying response times
    case rand(100)
    when 0..5
      sleep(0.1)  # Slow query
    when 6..20
      sleep(0.05)  # Medium query
    else
      sleep(0.01)  # Fast query
    end
    
    { status: 'success', rows: rand(1..100) }
  end
  
  def print_query_results
    @results.sort_by(&:avg_time).each do |result|
      puts "#{result[:name]}:"
      puts "  Average: #{result[:avg_time].round(6)}s"
      puts "  Min: #{result[:min_time].round(6)}s"
      puts "  Max: #{result[:max_time].round(6)}s"
      puts "  Iterations: #{result[:iterations]}"
      puts
    end
  end
end

# Usage example
db_config = {
  adapter: 'postgresql',
  host: 'localhost',
  database: 'test_db'
}

db_tester = DatabasePerformanceTester.new(db_config)
db_tester.setup_test_data
db_tester.benchmark_queries
db_tester.test_concurrent_access
```

## 🔍 Performance Monitoring

### 1. Real-time Performance Monitoring

Monitor application performance in real-time:

```ruby
class PerformanceMonitor
  def initialize(interval = 1)
    @interval = interval
    @metrics = {}
    @alerts = []
    @monitoring = false
    @monitor_thread = nil
  end
  
  def start_monitoring
    @monitoring = true
    @monitor_thread = Thread.new { monitoring_loop }
    puts "Performance monitoring started"
  end
  
  def stop_monitoring
    @monitoring = false
    @monitor_thread&.join
    puts "Performance monitoring stopped"
  end
  
  def add_metric(name, &block)
    @metrics[name] = block
  end
  
  def add_alert(name, condition, &block)
    @alerts << {
      name: name,
      condition: condition,
      action: block,
      triggered: false
    }
  end
  
  def get_current_metrics
    current_metrics = {}
    
    @metrics.each do |name, block|
      begin
        current_metrics[name] = block.call
      rescue => e
        current_metrics[name] = "Error: #{e.message}"
      end
    end
    
    current_metrics
  end
  
  def get_metric_history(name, duration = 300)
    # Return metrics for the last duration seconds
    # This is a simplified implementation
    []
  end
  
  private
  
  def monitoring_loop
    while @monitoring
      begin
        current_metrics = get_current_metrics
        check_alerts(current_metrics)
        log_metrics(current_metrics)
        sleep(@interval)
      rescue => e
        puts "Monitoring error: #{e.message}"
        sleep(@interval)
      end
    end
  end
  
  def check_alerts(metrics)
    @alerts.each do |alert|
      begin
        if alert[:condition].call(metrics)
          unless alert[:triggered]
            alert[:action].call(metrics)
            alert[:triggered] = true
          end
        else
          alert[:triggered] = false
        end
      rescue => e
        puts "Alert error: #{e.message}"
      end
    end
  end
  
  def log_metrics(metrics)
    timestamp = Time.now.strftime("%Y-%m-%d %H:%M:%S")
    
    puts "[#{timestamp}] Metrics:"
    metrics.each do |name, value|
      puts "  #{name}: #{value}"
    end
  end
end

# Usage example
monitor = PerformanceMonitor.new(2)

# Add metrics
monitor.add_metric("CPU Usage") do
  # Simulate CPU usage
  rand(10..90)
end

monitor.add_metric("Memory Usage") do
  # Simulate memory usage
  rand(20..80)
end

monitor.add_metric("Response Time") do
  # Simulate response time
  rand(10..500)
end

monitor.add_metric("Active Connections") do
  # Simulate active connections
  rand(1..100)
end

# Add alerts
monitor.add_alert("High CPU", ->(metrics) { metrics["CPU Usage"] > 80 }) do |metrics|
  puts "ALERT: High CPU usage - #{metrics["CPU Usage"]}%"
end

monitor.add_alert("High Memory", ->(metrics) { metrics["Memory Usage"] > 75 }) do |metrics|
  puts "ALERT: High memory usage - #{metrics["Memory Usage"]}%"
end

monitor.add_alert("Slow Response", ->(metrics) { metrics["Response Time"] > 400 }) do |metrics|
  puts "ALERT: Slow response time - #{metrics["Response Time"]}ms"
end

# Start monitoring
monitor.start_monitoring

# Let it run for a while
sleep(10)

# Stop monitoring
monitor.stop_monitoring
```

### 2. Performance Profiler

Continuous performance profiling:

```ruby
class PerformanceProfiler
  def initialize
    @profiles = {}
    @current_profile = nil
    @mutex = Mutex.new
  end
  
  def start_profile(name)
    @mutex.synchronize do
      @current_profile = {
        name: name,
        start_time: Time.now,
        samples: [],
        call_stack: []
      }
    end
  end
  
  def stop_profile
    @mutex.synchronize do
      return nil unless @current_profile
      
      @current_profile[:end_time] = Time.now
      @current_profile[:duration] = @current_profile[:end_time] - @current_profile[:start_time]
      
      profile = @current_profile
      @profiles[profile[:name]] = profile
      @current_profile = nil
      
      profile
    end
  end
  
  def profile_method(klass, method_name)
    original_method = klass.instance_method(method_name)
    
    klass.define_method(method_name) do |*args, &block|
      start_time = Time.now
      result = original_method.bind(self).call(*args, &block)
      end_time = Time.now
      
      duration = end_time - start_time
      
      # Record the sample
      if Thread.current[:profiler]
        Thread.current[:profiler][:samples] << {
          method: "#{klass.name}##{method_name}",
          duration: duration,
          timestamp: start_time
        }
      end
      
      result
    end
  end
  
  def with_profile(name, &block)
    start_profile(name)
    begin
      yield
    ensure
      stop_profile
    end
  end
  
  def get_profile(name)
    @profiles[name]
  end
  
  def get_all_profiles
    @profiles.dup
  end
  
  def analyze_profile(name)
    profile = @profiles[name]
    return nil unless profile
    
    samples = profile[:samples]
    return nil if samples.empty?
    
    # Calculate statistics
    durations = samples.map { |s| s[:duration] }
    
    {
      name: profile[:name],
      duration: profile[:duration],
      sample_count: samples.length,
      avg_time: durations.sum / durations.length,
      min_time: durations.min,
      max_time: durations.max,
      total_time: durations.sum,
      methods: analyze_methods(samples)
    }
  end
  
  def print_profile_report(name)
    analysis = analyze_profile(name)
    return unless analysis
    
    puts "\nProfile Report: #{analysis[:name]}"
    puts "=" * 50
    puts "Total duration: #{analysis[:duration].round(4)}s"
    puts "Sample count: #{analysis[:sample_count]}"
    puts "Average time per sample: #{analysis[:avg_time].round(6)}s"
    puts "Min time: #{analysis[:min_time].round(6)}s"
    puts "Max time: #{analysis[:max_time].round(6)}s"
    puts "Total sampled time: #{analysis[:total_time].round(4)}s"
    
    puts "\nMethod breakdown:"
    analysis[:methods].each do |method, stats|
      puts "  #{method}:"
      puts "    Calls: #{stats[:count]}"
      puts "    Total time: #{stats[:total_time].round(4)}s"
      puts "    Avg time: #{stats[:avg_time].round(6)}s"
      puts "    Percentage: #{stats[:percentage].round(2)}%"
    end
  end
  
  private
  
  def analyze_methods(samples)
    method_stats = Hash.new { |h, k| h[k] = { count: 0, total_time: 0 } }
    
    samples.each do |sample|
      method = sample[:method]
      duration = sample[:duration]
      
      method_stats[method][:count] += 1
      method_stats[method][:total_time] += duration
    end
    
    # Calculate averages and percentages
    total_time = method_stats.values.sum { |s| s[:total_time] }
    
    method_stats.each do |method, stats|
      stats[:avg_time] = stats[:total_time] / stats[:count]
      stats[:percentage] = (stats[:total_time] / total_time * 100) if total_time > 0
    end
    
    # Sort by total time
    method_stats.sort_by { |_, stats| -stats[:total_time] }.to_h
  end
end

# Usage example
class DataProcessor
  def initialize(data)
    @data = data
  end
  
  def process_data
    validate_data
    transform_data
    aggregate_data
  end
  
  def validate_data
    @data.each { |item| raise "Invalid data" unless item.is_a?(String) }
  end
  
  def transform_data
    @data.map(&:upcase)
  end
  
  def aggregate_data
    @data.group_by { |item| item[0] }
  end
end

# Set up profiler
profiler = PerformanceProfiler.new

# Profile specific methods
profiler.profile_method(DataProcessor, :process_data)
profiler.profile_method(DataProcessor, :validate_data)
profiler.profile_method(DataProcessor, :transform_data)
profiler.profile_method(DataProcessor, :aggregate_data)

# Run profiling
data = (1..1000).map { |i| "item_#{i}" }
processor = DataProcessor.new(data)

# Profile the entire operation
profiler.with_profile("data_processing") do
  10.times { processor.process_data }
end

# Print profile report
profiler.print_profile_report("data_processing")
```

## 🎯 Performance Testing Best Practices

### 1. Testing Guidelines

```ruby
class PerformanceTestingGuidelines
  def self.benchmarking_best_practices
    puts "Performance Testing Best Practices:"
    puts "=" * 50
    
    puts "1. Warm up the code before benchmarking"
    puts "2. Run multiple iterations and calculate statistics"
    puts "3. Test with realistic data sizes"
    puts "4. Consider different Ruby versions"
    puts "5. Test in isolation (no external dependencies)"
    puts "6. Use statistical analysis for results"
    puts "7. Document test conditions and environment"
  end
  
  def self.create_reproducible_benchmark
    puts "\nCreating Reproducible Benchmark:"
    puts "=" * 50
    
    class ReproducibleBenchmark
      def initialize(name, options = {})
        @name = name
        @options = options
        @results = {}
      end
      
      def run
        setup_environment
        
        @options[:test_cases].each do |test_case|
          @results[test_case[:name]] = run_test_case(test_case)
        end
        
        teardown_environment
        
        generate_report
      end
      
      private
      
      def setup_environment
        # Disable GC during benchmark
        GC.disable if @options[:disable_gc]
        
        # Set random seed for reproducibility
        srand(@options[:random_seed] || 12345) if @options[:random_seed]
        
        # Warm up Ruby interpreter
        warm_up if @options[:warm_up]
      end
      
      def teardown_environment
        # Re-enable GC
        GC.enable if @options[:disable_gc]
        
        # Force GC cleanup
        GC.start if @options[:force_gc]
      end
      
      def run_test_case(test_case)
        iterations = test_case[:iterations] || 100
        
        times = []
        
        iterations.times do
          GC.start if @options[:gc_between_iterations]
          
          time = Benchmark.measure do
            test_case[:block].call
          end
          
          times << time.real
        end
        
        {
          avg_time: times.sum / times.length,
          min_time: times.min,
          max_time: times.max,
          std_dev: calculate_std_dev(times),
          iterations: iterations
        }
      end
      
      def warm_up
        # Run some code to warm up the interpreter
        1000.times { |i| i * 2 }
      end
      
      def calculate_std_dev(values)
        mean = values.sum / values.length
        variance = values.sum { |v| (v - mean) ** 2 } / values.length
        Math.sqrt(variance)
      end
      
      def generate_report
        puts "\n#{@name} Benchmark Results"
        puts "=" * 50
        puts "Ruby version: #{RUBY_VERSION}"
        puts "Platform: #{RUBY_PLATFORM}"
        puts "Timestamp: #{Time.now}"
        puts
        
        @results.each do |name, result|
          puts "#{name}:"
          puts "  Average: #{result[:avg_time].round(6)}s"
          puts "  Min: #{result[:min_time].round(6)}s"
          puts "  Max: #{result[:max_time].round(6)}s"
          puts "  Std Dev: #{result[:std_dev].round(6)}s"
          puts "  Iterations: #{result[:iterations]}"
          puts
        end
      end
    end
    
    # Example usage
    benchmark = ReproducibleBenchmark.new("String Operations", {
      disable_gc: true,
      warm_up: true,
      random_seed: 12345
    })
    
    benchmark.instance_variable_set(:@options, {
      disable_gc: true,
      warm_up: true,
      random_seed: 12345,
      test_cases: [
        {
          name: "String interpolation",
          iterations: 1000,
          block: -> {
            100.times { |i| "Number #{i}" }
          }
        },
        {
          name: "String concatenation",
          iterations: 1000,
          block: -> {
            100.times { |i| "Number " + i.to_s }
          }
        }
      ]
    })
    
    benchmark.run
  end
end

# Run best practices
PerformanceTestingGuidelines.benchmarking_best_practices
PerformanceTestingGuidelines.create_reproducible_benchmark
```

### 2. Continuous Performance Testing

Integrate performance testing into CI/CD:

```ruby
class ContinuousPerformanceTester
  def initialize(baseline_file = "performance_baseline.json")
    @baseline_file = baseline_file
    @baseline = load_baseline
    @current_results = {}
  end
  
  def run_performance_tests
    puts "Running Continuous Performance Tests"
    puts "=" * 50
    
    # Run all performance tests
    test_results = {}
    
    test_results[:string_operations] = test_string_operations
    test_results[:array_operations] = test_array_operations
    test_results[:hash_operations] = test_hash_operations
    
    @current_results = test_results
    
    # Compare with baseline
    comparison = compare_with_baseline(test_results)
    
    # Generate report
    generate_report(comparison)
    
    # Return whether tests passed
    comparison[:passed]
  end
  
  def update_baseline
    puts "Updating performance baseline..."
    
    File.write(@baseline_file, @current_results.to_json)
    puts "Baseline updated to #{@baseline_file}"
  end
  
  private
  
  def load_baseline
    return {} unless File.exist?(@baseline_file)
    
    JSON.parse(File.read(@baseline_file))
  end
  
  def test_string_operations
    # Test string operations
    times = []
    
    100.times do
      time = Benchmark.measure do
        str = ""
        100.times { |i| str += "item_#{i}" }
      end
      
      times << time.real
    end
    
    {
      avg_time: times.sum / times.length,
      std_dev: calculate_std_dev(times)
    }
  end
  
  def test_array_operations
    # Test array operations
    times = []
    
    100.times do
      time = Benchmark.measure do
        array = []
        100.times { |i| array << i }
        array.sort
      end
      
      times << time.real
    end
    
    {
      avg_time: times.sum / times.length,
      std_dev: calculate_std_dev(times)
    }
  end
  
  def test_hash_operations
    # Test hash operations
    times = []
    
    100.times do
      time = Benchmark.measure do
        hash = {}
        100.times { |i| hash[i] = i * 2 }
        hash.values.sum
      end
      
      times << time.real
    end
    
    {
      avg_time: times.sum / times.length,
      std_dev: calculate_std_dev(times)
    }
  end
  
  def compare_with_baseline(current_results)
    comparison = {
      passed: true,
      regressions: [],
      improvements: [],
      unchanged: []
    }
    
    current_results.each do |test_name, current_result|
      baseline_result = @baseline[test_name]
      
      if baseline_result
        baseline_avg = baseline_result[:avg_time]
        current_avg = current_result[:avg_time]
        
        # Calculate percentage change
        change = ((current_avg - baseline_avg) / baseline_avg) * 100
        
        # Check if change is significant (> 5%)
        if change.abs > 5
          if change > 0
            comparison[:regressions] << {
              test: test_name,
              baseline: baseline_avg,
              current: current_avg,
              change: change
            }
            comparison[:passed] = false
          else
            comparison[:improvements] << {
              test: test_name,
              baseline: baseline_avg,
              current: current_avg,
              change: change
            }
          end
        else
          comparison[:unchanged] << {
            test: test_name,
            baseline: baseline_avg,
            current: current_avg,
            change: change
          }
        end
      else
        # No baseline exists
        comparison[:unchanged] << {
          test: test_name,
          baseline: nil,
          current: current_avg,
          change: nil
        }
      end
    end
    
    comparison
  end
  
  def generate_report(comparison)
    puts "\nPerformance Test Results"
    puts "=" * 50
    
    if comparison[:passed]
      puts "✅ All performance tests PASSED"
    else
      puts "❌ Performance tests FAILED - Regressions detected"
    end
    
    puts "\nRegressions (performance got worse):"
    comparison[:regressions].each do |regression|
      puts "  ❌ #{regression[:test]}: +#{regression[:change].round(2)}%"
      puts "     Baseline: #{regression[:baseline].round(6)}s"
      puts "     Current: #{regression[:current].round(6)}s"
    end
    
    puts "\nImprovements (performance got better):"
    comparison[:improvements].each do |improvement|
      puts "  ✅ #{improvement[:test]}: #{improvement[:change].round(2)}%"
      puts "     Baseline: #{improvement[:baseline].round(6)}s"
      puts "     Current: #{improvement[:current].round(6)}s"
    end
    
    puts "\nUnchanged (within threshold):"
    comparison[:unchanged].each do |unchanged|
      if unchanged[:baseline]
        puts "  ➖ #{unchanged[:test]}: #{unchanged[:change].round(2)}%"
        puts "     Baseline: #{unchanged[:baseline].round(6)}s"
        puts "     Current: #{unchanged[:current].round(6)}s"
      else
        puts "  ➖ #{unchanged[:test]}: No baseline"
        puts "     Current: #{unchanged[:current].round(6)}s"
      end
    end
  end
  
  def calculate_std_dev(values)
    mean = values.sum / values.length
    variance = values.sum { |v| (v - mean) ** 2 } / values.length
    Math.sqrt(variance)
  end
end

# Usage example
# In CI/CD pipeline:
# 1. Run performance tests
# 2. If tests fail, fail the build
# 3. If tests pass, consider updating baseline

tester = ContinuousPerformanceTester.new
passed = tester.run_performance_tests

if !passed
  puts "Performance regression detected! Build failed."
  exit 1
else
  puts "Performance tests passed! Build successful."
  
  # Optionally update baseline if this is a release
  # tester.update_baseline
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Benchmarking**: Use Ruby's Benchmark library
2. **Comparative Testing**: Compare different approaches
3. **Simple Load Testing**: Create basic load tests

### Intermediate Exercises

1. **Custom Framework**: Build a benchmarking framework
2. **Database Testing**: Test database performance
3. **Monitoring System**: Create performance monitoring

### Advanced Exercises

1. **CI Integration**: Integrate with CI/CD pipeline
2. **Regression Detection**: Build regression detection system
3. **Performance Profiler**: Create comprehensive profiler

---

## 🎯 Summary

Benchmarking and testing in Ruby provide:

- **Benchmarking Tools** - Measure and compare performance
- **Load Testing** - Test application under load
- **Performance Monitoring** - Real-time performance tracking
- **Continuous Testing** - Automated performance validation
- **Best Practices** - Reliable and reproducible testing

Master these techniques to ensure optimal Ruby application performance!
