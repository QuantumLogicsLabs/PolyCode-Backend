# Advanced Debugging Techniques in Ruby

## Overview

This guide covers advanced debugging techniques in Ruby, including debugging tools, strategies for complex issues, performance debugging, and automated debugging approaches.

## Ruby Debugger

### Using the Ruby Debugger

```ruby
# Enable debugging in your application
require 'debug'

class Calculator
  def add(a, b)
    result = a + b
    debugger  # Breakpoint
    result
  end
  
  def divide(a, b)
    debugger  # Breakpoint
    a / b
  end
  
  def complex_calculation(x, y, z)
    step1 = x * y
    debugger  # Inspect intermediate values
    step2 = step1 + z
    step3 = step2 / 2
    debugger  # Final result
    step3
  end
end

# Usage
calc = Calculator.new
result = calc.add(5, 3)
puts "Addition result: #{result}"

begin
  result = calc.divide(10, 0)
rescue ZeroDivisionError => e
  puts "Error: #{e.message}"
end

result = calc.complex_calculation(10, 20, 30)
puts "Complex calculation result: #{result}"
```

### Debugger Commands and Techniques

```ruby
class DebuggingExample
  def process_data(data)
    processed = []
    
    data.each_with_index do |item, index|
      processed_item = transform_item(item)
      processed << processed_item
      
      # Conditional breakpoint
      debugger if processed_item.nil?
    end
    
    processed
  end
  
  def transform_item(item)
    # Complex transformation that might have bugs
    return nil if item.nil?
    
    result = item.to_s.upcase
    result = result.reverse if item.respond_to?(:even?) && item.even?
    result
  end
  
  def find_duplicates(array)
    seen = {}
    duplicates = []
    
    array.each_with_index do |item, index|
      if seen[item]
        duplicates << { item: item, first_index: seen[item], duplicate_index: index }
        debugger  # Break when duplicate found
      else
        seen[item] = index
      end
    end
    
    duplicates
  end
end

# Advanced debugging session
debugger = DebuggingExample.new

# Test with problematic data
test_data = [1, 2, 3, 4, 5, 2, 6, nil, 7, 8, 4]
processed = debugger.process_data(test_data)

duplicates = debugger.find_duplicates([10, 20, 30, 20, 40, 50, 30])
puts "Duplicates found: #{duplicates.length}"
```

## Logging and Tracing

### Structured Logging

```ruby
require 'logger'
require 'json'

class StructuredLogger
  def initialize(output = STDOUT, level = Logger::INFO)
    @logger = Logger.new(output)
    @logger.level = level
    @logger.formatter = proc do |severity, datetime, progname, msg|
      {
        timestamp: datetime.iso8601,
        level: severity,
        message: msg,
        service: 'ruby-app'
      }.to_json + "\n"
    end
  end
  
  def info(message, context = {})
    @logger.info({ message: message, context: context }.to_json)
  end
  
  def error(message, context = {}, error = nil)
    log_entry = {
      message: message,
      context: context
    }
    
    if error
      log_entry[:error] = {
        class: error.class.name,
        message: error.message,
        backtrace: error.backtrace&.first(5)
      }
    end
    
    @logger.error(log_entry.to_json)
  end
  
  def debug(message, context = {})
    @logger.debug({ message: message, context: context }.to_json)
  end
  
  def warn(message, context = {})
    @logger.warn({ message: message, context: context }.to_json)
  end
end

# Usage
logger = StructuredLogger.new

logger.info("Application started", { version: "1.0.0", environment: "development" })
logger.debug("Processing request", { request_id: "req-123", user_id: 456 })

begin
  # Simulate an error
  raise StandardError, "Something went wrong"
rescue => e
  logger.error("Error occurred", { request_id: "req-123" }, e)
end
```

### Request Tracing

```ruby
class RequestTracer
  def initialize
    @current_trace = nil
    @spans = {}
    @mutex = Mutex.new
  end
  
  def start_trace(trace_id = nil)
    trace_id ||= generate_trace_id
    
    @mutex.synchronize do
      @current_trace = {
        trace_id: trace_id,
        started_at: Time.now,
        spans: []
      }
    end
    
    trace_id
  end
  
  def start_span(operation_name, parent_span_id = nil)
    return nil unless @current_trace
    
    span_id = generate_span_id
    span = {
      span_id: span_id,
      parent_span_id: parent_span_id,
      operation_name: operation_name,
      started_at: Time.now,
      tags: {}
    }
    
    @mutex.synchronize do
      @spans[span_id] = span
      @current_trace[:spans] << span
    end
    
    span_id
  end
  
  def finish_span(span_id, error = nil)
    return unless @spans[span_id]
    
    @mutex.synchronize do
      span = @spans[span_id]
      span[:finished_at] = Time.now
      span[:duration] = span[:finished_at] - span[:started_at]
      
      if error
        span[:error] = true
        span[:error_message] = error.message
      end
    end
  end
  
  def add_tag(span_id, key, value)
    return unless @spans[span_id]
    
    @mutex.synchronize do
      @spans[span_id][:tags][key] = value
    end
  end
  
  def finish_trace
    return nil unless @current_trace
    
    @mutex.synchronize do
      @current_trace[:finished_at] = Time.now
      @current_trace[:duration] = @current_trace[:finished_at] - @current_trace[:started_at]
      
      trace = @current_trace.dup
      @current_trace = nil
      @spans.clear
      
      trace
    end
  end
  
  def current_trace_id
    @current_trace ? @current_trace[:trace_id] : nil
  end
  
  private
  
  def generate_trace_id
    "trace_#{Time.now.to_f}_#{rand(1000)}"
  end
  
  def generate_span_id
    "span_#{Time.now.to_f}_#{rand(1000)}"
  end
end

# Usage
tracer = RequestTracer.new

# Start a trace
trace_id = tracer.start_trace

# Create spans
db_span = tracer.start_span("database_query", nil)
tracer.add_tag(db_span, "table", "users")
tracer.add_tag(db_span, "operation", "select")
sleep(0.1)  # Simulate database work
tracer.finish_span(db_span)

api_span = tracer.start_span("api_call", db_span)
tracer.add_tag(api_span, "endpoint", "/users")
tracer.add_tag(api_span, "method", "GET")
sleep(0.05)  # Simulate API work
tracer.finish_span(api_span)

# Finish trace
trace = tracer.finish_trace
puts "Trace completed: #{trace[:trace_id]}"
puts "Duration: #{trace[:duration].round(3)}s"
puts "Spans: #{trace[:spans].length}"
```

## Performance Debugging

### Memory Leak Detection

```ruby
class MemoryLeakDetector
  def initialize
    @snapshots = []
    @objects_history = []
  end
  
  def take_snapshot(label = nil)
    GC.start  # Force garbage collection
    
    snapshot = {
      timestamp: Time.now,
      label: label,
      memory_usage: get_memory_usage,
      object_counts: get_object_counts,
      total_objects: ObjectSpace.count_objects[:TOTAL]
    }
    
    @snapshots << snapshot
    snapshot
  end
  
  def detect_leaks(threshold = 0.1)
    return [] if @snapshots.length < 2
    
    leaks = []
    
    (1...@snapshots.length).each do |i|
      prev = @snapshots[i - 1]
      curr = @snapshots[i]
      
      # Check for memory growth
      memory_growth = (curr[:memory_usage] - prev[:memory_usage]) / prev[:memory_usage].to_f
      
      if memory_growth > threshold
        leaks << {
          type: :memory,
          from: prev[:timestamp],
          to: curr[:timestamp],
          growth: memory_growth,
          from_mb: prev[:memory_usage],
          to_mb: curr[:memory_usage]
        }
      end
      
      # Check for object count growth
      object_growth = (curr[:total_objects] - prev[:total_objects]) / prev[:total_objects].to_f
      
      if object_growth > threshold
        leaks << {
          type: :objects,
          from: prev[:timestamp],
          to: curr[:timestamp],
          growth: object_growth,
          from_count: prev[:total_objects],
          to_count: curr[:total_objects]
        }
      end
    end
    
    leaks
  end
  
  def find_object_growth
    return {} if @snapshots.length < 2
    
    growth = {}
    
    (1...@snapshots.length).each do |i|
      prev = @snapshots[i - 1][:object_counts]
      curr = @snapshots[i][:object_counts]
      
      prev.each_key do |type|
        prev_count = prev[type] || 0
        curr_count = curr[type] || 0
        
        if curr_count > prev_count
          growth[type] ||= 0
          growth[type] += curr_count - prev_count
        end
      end
    end
    
    growth.sort_by { |_, count| -count }.first(10).to_h
  end
  
  def generate_report
    return "No snapshots available" if @snapshots.empty?
    
    report = []
    report << "Memory Leak Detection Report"
    report << "=" * 40
    report << "Snapshots: #{@snapshots.length}"
    report << "Time range: #{@snapshots.first[:timestamp]} - #{@snapshots.last[:timestamp]}"
    report << ""
    
    # Memory usage trend
    if @snapshots.length > 1
      first_memory = @snapshots.first[:memory_usage]
      last_memory = @snapshots.last[:memory_usage]
      memory_change = last_memory - first_memory
      
      report << "Memory Usage:"
      report << "  Initial: #{first_memory} KB"
      report << "  Final: #{last_memory} KB"
      report << "  Change: #{memory_change > 0 ? '+' : ''}#{memory_change} KB"
      report << ""
    end
    
    # Object growth
    object_growth = find_object_growth
    unless object_growth.empty?
      report << "Object Growth (Top 10):"
      object_growth.each do |type, count|
        report << "  #{type}: +#{count}"
      end
      report << ""
    end
    
    # Potential leaks
    leaks = detect_leaks
    unless leaks.empty?
      report << "Potential Leaks:"
      leaks.each do |leak|
        if leak[:type] == :memory
          report << "  Memory: #{(leak[:growth] * 100).round(2)}% growth"
        else
          report << "  Objects: #{(leak[:growth] * 100).round(2)}% growth"
        end
      end
    end
    
    report.join("\n")
  end
  
  private
  
  def get_memory_usage
    `ps -o rss= -p #{Process.pid}`.strip.to_i
  end
  
  def get_object_counts
    ObjectSpace.count_objects
  end
end

# Usage
detector = MemoryLeakDetector.new

# Take initial snapshot
detector.take_snapshot("initial")

# Simulate memory usage
data = []
1000.times { |i| data << "Item #{i}" * 100 }

detector.take_snapshot("after_data_creation")

# Create more objects
objects = []
500.times { |i| objects << Object.new }

detector.take_snapshot("after_object_creation")

# Clean up some objects
data.clear
GC.start

detector.take_snapshot("after_cleanup")

# Generate report
puts detector.generate_report

# Detect leaks
leaks = detector.detect_leaks(0.05)
puts "\nDetected leaks: #{leaks.length}"
```

### Performance Profiler

```ruby
class PerformanceProfiler
  def initialize
    @profiles = {}
    @current_profile = nil
  end
  
  def start_profile(name)
    @current_profile = {
      name: name,
      started_at: Time.now,
      method_calls: {},
      memory_snapshots: []
    }
    
    # Take initial memory snapshot
    GC.start
    @current_profile[:memory_snapshots] << {
      timestamp: Time.now,
      memory_usage: get_memory_usage,
      object_counts: get_object_counts
    }
  end
  
  def profile_method(method_name, &block)
    return block.call unless @current_profile
    
    method_stats = @current_profile[:method_calls][method_name] ||= {
      call_count: 0,
      total_time: 0,
      avg_time: 0,
      max_time: 0,
      min_time: Float::INFINITY
    }
    
    start_time = Time.now
    result = block.call
    end_time = Time.now
    
    duration = end_time - start_time
    method_stats[:call_count] += 1
    method_stats[:total_time] += duration
    method_stats[:avg_time] = method_stats[:total_time] / method_stats[:call_count]
    method_stats[:max_time] = [method_stats[:max_time], duration].max
    method_stats[:min_time] = [method_stats[:min_time], duration].min
    
    result
  end
  
  def take_memory_snapshot(label = nil)
    return unless @current_profile
    
    GC.start
    @current_profile[:memory_snapshots] << {
      timestamp: Time.now,
      label: label,
      memory_usage: get_memory_usage,
      object_counts: get_object_counts
    }
  end
  
  def finish_profile
    return nil unless @current_profile
    
    @current_profile[:finished_at] = Time.now
    @current_profile[:duration] = @current_profile[:finished_at] - @current_profile[:started_at]
    
    profile = @current_profile.dup
    @profiles[@current_profile[:name]] = profile
    @current_profile = nil
    
    profile
  end
  
  def get_profile(name)
    @profiles[name]
  end
  
  def generate_report(name)
    profile = @profiles[name]
    return "Profile '#{name}' not found" unless profile
    
    report = []
    report << "Performance Profile: #{name}"
    report << "=" * 40
    report << "Duration: #{profile[:duration].round(3)}s"
    report << "Time range: #{profile[:started_at]} - #{profile[:finished_at]}"
    report << ""
    
    # Method calls
    if profile[:method_calls].any?
      report << "Method Calls:"
      sorted_methods = profile[:method_calls].sort_by { |_, stats| -stats[:total_time] }
      
      sorted_methods.each do |method, stats|
        report << "  #{method}:"
        report << "    Calls: #{stats[:call_count]}"
        report << "    Total: #{stats[:total_time].round(4)}s"
        report << "    Average: #{stats[:avg_time].round(6)}s"
        report << "    Max: #{stats[:max_time].round(6)}s"
        report << "    Min: #{stats[:min_time].round(6)}s"
      end
      report << ""
    end
    
    # Memory usage
    if profile[:memory_snapshots].length > 1
      first_snapshot = profile[:memory_snapshots].first
      last_snapshot = profile[:memory_snapshots].last
      
      memory_change = last_snapshot[:memory_usage] - first_snapshot[:memory_usage]
      object_change = last_snapshot[:object_counts][:TOTAL] - first_snapshot[:object_counts][:TOTAL]
      
      report << "Memory Usage:"
      report << "  Initial: #{first_snapshot[:memory_usage]} KB"
      report << "  Final: #{last_snapshot[:memory_usage]} KB"
      report << "  Change: #{memory_change > 0 ? '+' : ''}#{memory_change} KB"
      report << ""
      report << "Object Count:"
      report << "  Initial: #{first_snapshot[:object_counts][:TOTAL]}"
      report << "  Final: #{last_snapshot[:object_counts][:TOTAL]}"
      report << "  Change: #{object_change > 0 ? '+' : ''}#{object_change}"
    end
    
    report.join("\n")
  end
  
  private
  
  def get_memory_usage
    `ps -o rss= -p #{Process.pid}`.strip.to_i
  end
  
  def get_object_counts
    ObjectSpace.count_objects
  end
end

# Usage
profiler = PerformanceProfiler.new

# Start profiling
profiler.start_profile("data_processing")

# Profile some methods
profiler.profile_method(:load_data) do
  sleep(0.1)  # Simulate data loading
  (1..1000).to_a
end

profiler.take_memory_snapshot("after_load")

profiler.profile_method(:process_data) do
  sleep(0.05)  # Simulate processing
  data = (1..500).map { |i| i * 2 }
  data.select(&:even?)
end

profiler.take_memory_snapshot("after_process")

profiler.profile_method(:save_data) do
  sleep(0.02)  # Simulate saving
  results = (1..200).map { |i| "Result #{i}" }
  results.join(", ")
end

# Finish profiling
profile = profiler.finish_profile

# Generate report
puts profiler.generate_report("data_processing")
```

## Error Analysis

### Exception Analyzer

```ruby
class ExceptionAnalyzer
  def initialize
    @exceptions = []
    @patterns = {}
  end
  
  def record_exception(exception, context = {})
    exception_data = {
      timestamp: Time.now,
      class_name: exception.class.name,
      message: exception.message,
      backtrace: exception.backtrace&.first(10),
      context: context
    }
    
    @exceptions << exception_data
    analyze_pattern(exception_data)
  end
  
  def get_error_rate(time_window = 3600)  # Default: 1 hour
    cutoff_time = Time.now - time_window
    recent_exceptions = @exceptions.select { |e| e[:timestamp] > cutoff_time }
    
    recent_exceptions.length.to_f / time_window
  end
  
  def get_top_errors(limit = 10)
    error_counts = Hash.new(0)
    
    @exceptions.each do |exception|
      key = "#{exception[:class_name]}: #{exception[:message]}"
      error_counts[key] += 1
    end
    
    error_counts.sort_by { |_, count| -count }.first(limit)
  end
  
  def get_error_trends(time_window = 3600, interval = 300)  # 5-minute intervals
    cutoff_time = Time.now - time_window
    recent_exceptions = @exceptions.select { |e| e[:timestamp] > cutoff_time }
    
    trends = {}
    current_time = Time.now
    
    while current_time > cutoff_time
      interval_start = current_time - interval
      interval_end = current_time
      
      interval_exceptions = recent_exceptions.select do |e|
        e[:timestamp] >= interval_start && e[:timestamp] < interval_end
      end
      
      trends[interval_end] = interval_exceptions.length
      current_time = interval_start
    end
    
    trends.sort_by { |time, _| time }.to_h
  end
  
  def find_error_patterns
    patterns = []
    
    @patterns.each do |pattern_key, pattern_data|
      if pattern_data[:frequency] > 5  # Pattern appears more than 5 times
        patterns << {
          pattern: pattern_key,
          frequency: pattern_data[:frequency],
          contexts: pattern_data[:contexts].uniq,
          first_seen: pattern_data[:first_seen],
          last_seen: pattern_data[:last_seen]
        }
      end
    end
    
    patterns.sort_by { |p| -p[:frequency] }
  end
  
  def generate_report
    report = []
    report << "Exception Analysis Report"
    report << "=" * 40
    report << "Total exceptions: #{@exceptions.length}"
    report << "Error rate: #{(get_error_rate * 3600).round(2)}/hour"
    report << ""
    
    # Top errors
    top_errors = get_top_errors(5)
    if top_errors.any?
      report << "Top Errors:"
      top_errors.each_with_index do |(error, count), i|
        report << "  #{i + 1}. #{error} (#{count} occurrences)"
      end
      report << ""
    end
    
    # Error patterns
    patterns = find_error_patterns
    if patterns.any?
      report << "Error Patterns:"
      patterns.each_with_index do |pattern, i|
        report << "  #{i + 1}. #{pattern[:pattern]}"
        report << "     Frequency: #{pattern[:frequency]}"
        report << "     Contexts: #{pattern[:contexts].join(', ')}"
        report << ""
      end
    end
    
    # Recent trends
    trends = get_error_trends
    if trends.any?
      report << "Recent Error Trends (last hour):"
      trends.each do |time, count|
        report << "  #{time.strftime('%H:%M')}: #{count} errors"
      end
    end
    
    report.join("\n")
  end
  
  private
  
  def analyze_pattern(exception_data)
    # Create pattern key based on exception class and common message patterns
    pattern_key = "#{exception_data[:class_name]}:#{extract_pattern(exception_data[:message])}"
    
    @patterns[pattern_key] ||= {
      frequency: 0,
      contexts: [],
      first_seen: exception_data[:timestamp],
      last_seen: exception_data[:timestamp]
    }
    
    pattern = @patterns[pattern_key]
    pattern[:frequency] += 1
    pattern[:contexts] << exception_data[:context]
    pattern[:last_seen] = exception_data[:timestamp]
  end
  
  def extract_pattern(message)
    # Extract common patterns from error messages
    pattern = message.dup
    
    # Replace numbers with placeholder
    pattern.gsub!(/\d+/, 'N')
    
    # Replace UUIDs with placeholder
    pattern.gsub!(/\b[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\b/i, 'UUID')
    
    # Replace file paths with placeholder
    pattern.gsub!(/\/[^\s]+/, 'PATH')
    
    pattern
  end
end

# Usage
analyzer = ExceptionAnalyzer.new

# Simulate various exceptions
begin
  raise StandardError, "User not found with ID: 12345"
rescue => e
  analyzer.record_exception(e, { action: "get_user", user_id: "12345" })
end

begin
  raise StandardError, "Database connection failed to localhost:5432"
rescue => e
  analyzer.record_exception(e, { action: "connect_db", host: "localhost" })
end

begin
  raise StandardError, "User not found with ID: 67890"
rescue => e
  analyzer.record_exception(e, { action: "get_user", user_id: "67890" })
end

begin
  raise StandardError, "Invalid input: parameter cannot be nil"
rescue => e
  analyzer.record_exception(e, { action: "validate_input", parameter: "email" })
end

begin
  raise StandardError, "Database connection failed to localhost:5432"
rescue => e
  analyzer.record_exception(e, { action: "connect_db", host: "localhost" })
end

# Generate analysis report
puts analyzer.generate_report
```

## Automated Debugging

### Self-Healing Code

```ruby
class SelfHealingCode
  def initialize
    @error_patterns = {}
    @healing_strategies = {}
    @retry_attempts = {}
  end
  
  def register_error_pattern(pattern, &healing_strategy)
    @error_patterns[pattern] = pattern
    @healing_strategies[pattern] = healing_strategy
  end
  
  def execute_with_healing(operation_name, max_retries = 3, &block)
    @retry_attempts[operation_name] ||= 0
    
    begin
      result = block.call
      @retry_attempts[operation_name] = 0  # Reset on success
      result
    rescue => e
      @retry_attempts[operation_name] += 1
      
      if @retry_attempts[operation_name] <= max_retries
        healing_strategy = find_healing_strategy(e)
        
        if healing_strategy
          puts "Applying healing strategy for: #{e.class.name}"
          healing_strategy.call(e, @retry_attempts[operation_name])
          
          # Retry the operation
          sleep(2 ** @retry_attempts[operation_name])  # Exponential backoff
          retry
        else
          puts "No healing strategy found for: #{e.class.name}"
          raise e
        end
      else
        puts "Max retries exceeded for: #{operation_name}"
        raise e
      end
    end
  end
  
  def get_retry_stats
    @retry_attempts.dup
  end
  
  private
  
  def find_healing_strategy(error)
    @error_patterns.each do |pattern, strategy|
      if error.class.name.match?(pattern) || error.message.match?(pattern)
        return @healing_strategies[pattern]
      end
    end
    
    nil
  end
end

# Usage
healer = SelfHealingCode.new

# Register healing strategies
healer.register_error_pattern(/ConnectionError/) do |error, attempt|
  puts "Healing connection error (attempt #{attempt})"
  # Reconnect to database
  reconnect_database
end

healer.register_error_pattern(/TimeoutError/) do |error, attempt|
  puts "Healing timeout error (attempt #{attempt})"
  # Increase timeout or retry with different parameters
  increase_timeout
end

healer.register_error_pattern(/ValidationError/) do |error, attempt|
  puts "Healing validation error (attempt #{attempt})"
  # Sanitize input or use default values
  sanitize_input
end

# Simulate operations with healing
def database_operation
  # Simulate connection error
  raise ConnectionError, "Database connection lost" if rand < 0.3
  "Database operation successful"
end

def api_call
  # Simulate timeout
  raise TimeoutError, "Request timed out" if rand < 0.2
  "API call successful"
end

def process_data(input)
  # Simulate validation error
  raise ValidationError, "Invalid input data" if rand < 0.1
  "Data processed successfully"
end

# Execute with self-healing
result1 = healer.execute_with_healing("database_op") do
  database_operation
end

result2 = healer.execute_with_healing("api_call") do
  api_call
end

result3 = healer.execute_with_healing("data_processing") do
  process_data(nil)
end

puts "Results: #{result1}, #{result2}, #{result3}"
puts "Retry stats: #{healer.get_retry_stats}"
```

### Automated Testing

```ruby
class AutomatedDebugger
  def initialize
    @test_cases = []
    @failed_tests = []
    @patterns = {}
  end
  
  def add_test_case(description, &test_block)
    @test_cases << {
      description: description,
      test: test_block,
      timestamp: Time.now
    }
  end
  
  def run_tests
    @failed_tests.clear
    
    @test_cases.each do |test_case|
      begin
        test_case[:test].call
        puts "✅ #{test_case[:description]}"
      rescue => e
        puts "❌ #{test_case[:description]}: #{e.message}"
        @failed_tests << {
          test: test_case,
          error: e,
          timestamp: Time.now
        }
        
        analyze_failure(test_case, e)
      end
    end
    
    generate_failure_report
  end
  
  def auto_fix_simple_failures
    fixes_applied = 0
    
    @failed_tests.each do |failed_test|
      fix = suggest_fix(failed_test)
      
      if fix && fix[:auto_applicable]
        puts "🔧 Auto-applying fix for: #{failed_test[:test][:description]}"
        apply_fix(fix)
        fixes_applied += 1
      end
    end
    
    fixes_applied
  end
  
  def generate_test_suite
    suite = []
    
    @failed_tests.each do |failed_test|
      fix = suggest_fix(failed_test)
      
      if fix
        suite << {
          description: "Regression test for: #{failed_test[:test][:description]}",
          test: generate_regression_test(failed_test, fix)
        }
      end
    end
    
    suite
  end
  
  private
  
  def analyze_failure(test_case, error)
    pattern_key = "#{error.class.name}:#{extract_error_pattern(error.message)}"
    
    @patterns[pattern_key] ||= {
      count: 0,
      test_cases: [],
      first_seen: Time.now
    end
    
    @patterns[pattern_key][:count] += 1
    @patterns[pattern_key][:test_cases] << test_case[:description]
  end
  
  def extract_error_pattern(message)
    # Extract pattern from error message
    pattern = message.dup
    pattern.gsub!(/\d+/, 'N')
    pattern.gsub!(/'[^']*'/, "'STRING'")
    pattern.gsub!(/"[^"]*"/, '"STRING"')
    pattern
  end
  
  def suggest_fix(failed_test)
    error = failed_test[:error]
    
    case error.class.name
    when 'NoMethodError'
      if error.message.include?('undefined method')
        method_name = error.message.match(/undefined method `([^']+)'/)[1]
        {
          type: :method_missing,
          suggestion: "Define method '#{method_name}' or check for typos",
          auto_applicable: false
        }
      end
    when 'ArgumentError'
      if error.message.include?('wrong number of arguments')
        {
          type: :argument_count,
          suggestion: "Check method signature and argument count",
          auto_applicable: false
        }
      end
    when 'TypeError'
      if error.message.include?('can\'t convert')
        {
          type: :type_conversion,
          suggestion: "Add type conversion or validation",
          auto_applicable: false
        }
      end
    end
  end
  
  def apply_fix(fix)
    # In a real implementation, this would modify the code
    puts "Applied fix: #{fix[:suggestion]}"
  end
  
  def generate_regression_test(failed_test, fix)
    # Generate a test to prevent regression
    lambda do
      # Test would verify the fix works
      puts "Running regression test for: #{failed_test[:test][:description]}"
    end
  end
  
  def generate_failure_report
    report = []
    report << "Automated Debugging Report"
    report << "=" * 40
    report << "Total tests: #{@test_cases.length}"
    report << "Failed tests: #{@failed_tests.length}"
    report << "Success rate: #{((@test_cases.length - @failed_tests.length).to_f / @test_cases.length * 100).round(2)}%"
    report << ""
    
    if @failed_tests.any?
      report << "Failed Tests:"
      @failed_tests.each_with_index do |failed, i|
        report << "  #{i + 1}. #{failed[:test][:description]}"
        report << "     Error: #{failed[:error].class.name}"
        report << "     Message: #{failed[:error].message}"
      end
      report << ""
    end
    
    if @patterns.any?
      report << "Error Patterns:"
      @patterns.each do |pattern, data|
        report << "  #{pattern}: #{data[:count]} occurrences"
        report << "    Affected tests: #{data[:test_cases].join(', ')}"
      end
    end
    
    report.join("\n")
  end
end

# Usage
debugger = AutomatedDebugger.new

# Add test cases
debugger.add_test_case("Array length calculation") do
  array = [1, 2, 3]
  array.length
end

debugger.add_test_case("String concatenation") do
  "hello" + " " + "world"
end

debugger.add_test_case("Method call with valid arguments") do
  Math.sqrt(16)
end

debugger.add_test_case("Method call with invalid arguments") do
  Math.sqrt("invalid")  # This will fail
end

debugger.add_test_case("Accessing array element") do
  array = [1, 2, 3]
  array[1]
end

debugger.add_test_case("Accessing invalid array element") do
  array = [1, 2, 3]
  array[10]  # This will fail
end

# Run tests
puts "Running automated tests..."
debugger.run_tests

# Try to auto-fix simple failures
fixes_applied = debugger.auto_fix_simple_failures
puts "\nApplied #{fixes_applied} automatic fixes"

# Generate regression tests
regression_tests = debugger.generate_test_suite
puts "\nGenerated #{regression_tests.length} regression tests"
```

## Remote Debugging

### Remote Debugging Server

```ruby
require 'socket'
require 'json'

class RemoteDebugServer
  def initialize(host = 'localhost', port = 4444)
    @host = host
    @port = port
    @server = nil
    @clients = []
    @debug_context = {}
    @running = false
  end
  
  def start
    @server = TCPServer.new(@host, @port)
    @running = true
    
    puts "Remote debug server started on #{@host}:#{@port}"
    
    # Accept connections in a separate thread
    @accept_thread = Thread.new { accept_connections }
    
    # Command loop
    command_loop
  end
  
  def stop
    @running = false
    @server&.close
    @accept_thread&.join
    @clients.each(&:close)
    puts "Remote debug server stopped"
  end
  
  def set_breakpoint(file, line, condition = nil)
    @debug_context[:breakpoints] ||= []
    @debug_context[:breakpoints] << {
      file: file,
      line: line,
      condition: condition,
      id: SecureRandom.uuid
    }
    
    notify_clients("breakpoint_set", {
      file: file,
      line: line,
      condition: condition
    })
  end
  
  def remove_breakpoint(breakpoint_id)
    @debug_context[:breakpoints]&.reject! { |bp| bp[:id] == breakpoint_id }
    
    notify_clients("breakpoint_removed", { breakpoint_id: breakpoint_id })
  end
  
  def evaluate_expression(expression, binding_context = nil)
    begin
      result = eval(expression, binding_context || TOPLEVEL_BINDING)
      {
        success: true,
        value: result.inspect,
        type: result.class.name
      }
    rescue => e
      {
        success: false,
        error: e.message
      }
    end
  end
  
  def get_variables(binding_context = nil)
    variables = {}
    
    if binding_context
      binding_context.local_variables.each do |name|
        begin
          variables[name] = binding_context.local_variable_get(name).inspect
        rescue
          variables[name] = "<unable to access>"
        end
      end
    end
    
    variables
  end
  
  def get_call_stack
    # This would need to be implemented with actual call stack inspection
    [
      { method: "main", file: "app.rb", line: 10 },
      { method: "process_data", file: "app.rb", line: 25 }
    ]
  end
  
  def step_into(binding_context = nil)
    notify_clients("step_into", {})
    # Implementation would step into the next line
  end
  
  def step_over(binding_context = nil)
    notify_clients("step_over", {})
    # Implementation would step over the next line
  end
  
  def continue(binding_context = nil)
    notify_clients("continue", {})
    # Implementation would continue execution
  end
  
  private
  
  def accept_connections
    while @running
      begin
        client = @server.accept_nonblock
        @clients << client
        handle_client(client)
      rescue IO::WaitReadable
        sleep(0.1)
      rescue => e
        puts "Error accepting connection: #{e.message}"
      end
    end
  end
  
  def handle_client(client)
    Thread.new do
      while @running
        begin
          data = client.gets
          break unless data
          
          command = JSON.parse(data)
          handle_command(client, command)
        rescue => e
          puts "Error handling client: #{e.message}"
          break
        end
      end
      
      @clients.delete(client)
      client.close
    end
  end
  
  def handle_command(client, command)
    case command['type']
    when 'set_breakpoint'
      set_breakpoint(command['file'], command['line'], command['condition'])
      send_response(client, { success: true })
      
    when 'remove_breakpoint'
      remove_breakpoint(command['breakpoint_id'])
      send_response(client, { success: true })
      
    when 'evaluate'
      result = evaluate_expression(command['expression'])
      send_response(client, result)
      
    when 'get_variables'
      variables = get_variables(command['binding_context'])
      send_response(client, { variables: variables })
      
    when 'get_call_stack'
      stack = get_call_stack
      send_response(client, { call_stack: stack })
      
    when 'step_into'
      step_into(command['binding_context'])
      send_response(client, { success: true })
      
    when 'step_over'
      step_over(command['binding_context'])
      send_response(client, { success: true })
      
    when 'continue'
      continue(command['binding_context'])
      send_response(client, { success: true })
      
    else
      send_response(client, { error: "Unknown command: #{command['type']}" })
    end
  end
  
  def send_response(client, response)
    client.puts(JSON.generate(response))
  end
  
  def notify_clients(event_type, data)
    message = JSON.generate({ type: event_type, data: data })
    
    @clients.each do |client|
      begin
        client.puts(message)
      rescue
        @clients.delete(client)
      end
    end
  end
  
  def command_loop
    while @running
      print "debug> "
      command = gets.chomp
      
      case command
      when 'stop'
        stop
        break
      when 'status'
        puts "Connected clients: #{@clients.length}"
        puts "Breakpoints: #{@debug_context[:breakpoints]&.length || 0}"
      when 'help'
        puts "Available commands:"
        puts "  stop    - Stop the debug server"
        puts "  status  - Show server status"
        puts "  help    - Show this help"
      else
        puts "Unknown command: #{command}"
      end
    end
  end
end

# Remote Debug Client
class RemoteDebugClient
  def initialize(host = 'localhost', port = 4444)
    @host = host
    @port = port
    @socket = nil
    @event_handlers = {}
  end
  
  def connect
    @socket = TCPSocket.new(@host, @port)
    puts "Connected to debug server at #{@host}:#{@port}"
    
    # Start listening for events
    Thread.new { listen_for_events }
  end
  
  def disconnect
    @socket&.close
    @socket = nil
    puts "Disconnected from debug server"
  end
  
  def set_breakpoint(file, line, condition = nil)
    send_command({
      type: 'set_breakpoint',
      file: file,
      line: line,
      condition: condition
    })
  end
  
  def remove_breakpoint(breakpoint_id)
    send_command({
      type: 'remove_breakpoint',
      breakpoint_id: breakpoint_id
    })
  end
  
  def evaluate_expression(expression)
    send_command({
      type: 'evaluate',
      expression: expression
    })
  end
  
  def get_variables
    send_command({ type: 'get_variables' })
  end
  
  def get_call_stack
    send_command({ type: 'get_call_stack' })
  end
  
  def step_into
    send_command({ type: 'step_into' })
  end
  
  def step_over
    send_command({ type: 'step_over' })
  end
  
  def continue
    send_command({ type: 'continue' })
  end
  
  def on_event(event_type, &handler)
    @event_handlers[event_type] = handler
  end
  
  private
  
  def send_command(command)
    return nil unless @socket
    
    @socket.puts(JSON.generate(command))
    
    # Wait for response
    response_data = @socket.gets
    return nil unless response_data
    
    JSON.parse(response_data)
  end
  
  def listen_for_events
    while @socket
      begin
        data = @socket.gets
        break unless data
        
        event = JSON.parse(data)
        handler = @event_handlers[event['type']]
        
        if handler
          handler.call(event['data'])
        else
          puts "Received event: #{event['type']}"
        end
      rescue => e
        puts "Error listening for events: #{e.message}"
        break
      end
    end
  end
end

# Usage example
# Start server in one process
server = RemoteDebugServer.new

# In another process, connect client
client = RemoteDebugClient.new
client.connect

# Set up event handlers
client.on_event('breakpoint_set') do |data|
  puts "Breakpoint set at #{data[:file]}:#{data[:line]}"
end

client.on_event('step_into') do |data|
  puts "Stepped into next line"
end

# Send commands
client.set_breakpoint('app.rb', 25)
result = client.evaluate_expression('x + 1')
puts "Expression result: #{result['value']}" if result['success']

client.disconnect
```

## Best Practices

### 1. Debugging Workflow

```ruby
class DebuggingWorkflow
  def self.debug_issue(description, &block)
    puts "🔍 Starting debugging: #{description}"
    
    # 1. Reproduce the issue
    puts "1. Reproducing issue..."
    begin
      result = block.call
      puts "   Issue reproduced successfully"
    rescue => e
      puts "   Issue reproduced with error: #{e.message}"
      raise e
    end
    
    # 2. Collect information
    puts "2. Collecting debugging information..."
    collect_debug_info
    
    # 3. Formulate hypothesis
    puts "3. Formulating hypothesis..."
    hypothesis = formulate_hypothesis
    puts "   Hypothesis: #{hypothesis}"
    
    # 4. Test hypothesis
    puts "4. Testing hypothesis..."
    test_result = test_hypothesis(hypothesis)
    puts "   Test result: #{test_result ? 'Confirmed' : 'Refuted'}"
    
    # 5. Implement fix
    if test_result
      puts "5. Implementing fix..."
      implement_fix
      puts "   Fix implemented"
    else
      puts "5. Hypothesis refuted, need to investigate further"
    end
    
    result
  end
  
  private
  
  def self.collect_debug_info
    puts "   Memory usage: #{get_memory_usage} KB"
    puts "   Object count: #{ObjectSpace.count_objects[:TOTAL]}"
    puts "   GC runs: #{GC.stat[:count]}"
  end
  
  def self.formulate_hypothesis
    # This would analyze the collected information
    "The issue is likely caused by a memory leak in the data processing loop"
  end
  
  def self.test_hypothesis(hypothesis)
    # This would implement a test to verify the hypothesis
    true
  end
  
  def self.implement_fix
    # This would implement the actual fix
    puts "   Added memory cleanup after processing"
  end
  
  def self.get_memory_usage
    `ps -o rss= -p #{Process.pid}`.strip.to_i
  end
end
```

### 2. Error Prevention

```ruby
class ErrorPrevention
  def self.validate_input(input, rules)
    errors = []
    
    rules.each do |field, validations|
      value = input[field]
      
      validations.each do |validation|
        case validation[:type]
        when :required
          errors << "#{field} is required" if value.nil? || value.to_s.empty?
        when :format
          errors << "#{field} has invalid format" unless value.match?(Regexp.new(validation[:pattern]))
        when :min_length
          errors << "#{field} is too short" if value.to_s.length < validation[:min]
        when :max_length
          errors << "#{field} is too long" if value.to_s.length > validation[:max]
        end
      end
    end
    
    raise ValidationError, errors if errors.any?
    
    true
  end
  
  def self.with_timeout(timeout_seconds, &block)
    result = nil
    
    thread = Thread.new do
      result = block.call
    end
    
    if thread.join(timeout_seconds)
      result
    else
      thread.kill
      raise TimeoutError, "Operation timed out after #{timeout_seconds} seconds"
    end
  end
  
  def self.with_retry(max_attempts = 3, delay = 1, &block)
    attempts = 0
    
    begin
      block.call
    rescue => e
      attempts += 1
      
      if attempts <= max_attempts
        sleep(delay * attempts)  # Exponential backoff
        retry
      else
        raise e
      end
    end
  end
end

class ValidationError < StandardError; end
class TimeoutError < StandardError; end
```

## Practice Exercises

### Exercise 1: Debugging Tool Suite
Create a comprehensive debugging tool with:
- Breakpoint management
- Variable inspection
- Call stack analysis
- Expression evaluation
- Remote debugging capabilities

### Exercise 2: Performance Analyzer
Build a performance analysis tool with:
- Memory usage tracking
- CPU profiling
- Method timing
- Bottleneck identification
- Performance recommendations

### Exercise 3: Error Monitoring System
Develop an error monitoring system with:
- Exception tracking
- Error pattern analysis
- Alert system
- Dashboard visualization
- Automated reporting

### Exercise 4: Automated Testing Framework
Create an automated testing framework with:
- Test discovery
- Failure analysis
- Auto-fix suggestions
- Regression test generation
- Continuous integration

---

**Ready to explore more advanced Ruby topics? Let's continue! 🐛**
