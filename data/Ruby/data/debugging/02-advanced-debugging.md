# Advanced Debugging in Ruby
# Comprehensive guide to sophisticated debugging techniques and tools

## 🎯 Overview

Advanced debugging requires sophisticated techniques and tools to solve complex issues. This guide covers advanced debugging strategies, performance debugging, and specialized debugging scenarios.

## 🔬 Advanced Ruby Debugging Tools

### 1. Ruby Debugger (debug gem)

Using the modern Ruby debugger:

```ruby
# First, install the debug gem: gem install debug
require 'debug'

class AdvancedDebuggerExample
  def self.demonstrate_debugger
    puts "Advanced Ruby Debugger (debug gem):"
    puts "=" * 50
    
    puts "To use the debugger:"
    puts "1. Add 'require \"debug\"' to your file"
    puts "2. Insert 'debugger' where you want to break"
    puts "3. Run with: ruby your_file.rb"
    puts ""
    puts "Advanced debugger features:"
    puts "- Thread debugging"
    puts "- Exception debugging"
    puts "- Post-mortem debugging"
    puts "- Remote debugging"
    puts "- Conditional breakpoints"
    
    # Example function that would benefit from debugging
    def complex_calculation(data, options = {})
      debug_breakpoint_here
      
      processed = data.map.with_index do |item, index|
        # Complex logic with potential issues
        multiplier = options[:multiplier] || 1
        offset = options[:offset] || 0
        
        result = (item * multiplier) + offset
        
        # Potential division by zero
        result = result / options[:divisor] if options[:divisor]
        
        # Potential overflow
        if result > 1000000
          raise "Result too large: #{result}"
        end
        
        result
      end
      
      processed
    end
    
    # Note: This would normally be called from debugger
    puts "Function ready for debugging"
  end
  
  def self.debugger_commands
    puts "\nAdvanced Debugger Commands:"
    puts "=" * 50
    
    commands = {
      "break" => "Set breakpoint",
      "catch" => "Set exception breakpoint",
      "watch" => "Set watchpoint",
      "info" => "Show information",
      "thread" => "Thread commands",
      "frame" => "Frame commands",
      "step" => "Step execution",
      "next" => "Next line",
      "finish" => "Finish current frame",
      "continue" => "Continue execution",
      "backtrace" => "Show backtrace",
      "info locals" => "Show local variables",
      "info instance" => "Show instance variables",
      "info globals" => "Show global variables",
      "pp" => "Pretty print",
      "display" => "Display expression",
      "undisplay" => "Stop displaying expression"
    }
    
    commands.each { |cmd, desc| puts "#{cmd.ljust(18)}: #{desc}" }
  end
  
  def self.conditional_breakpoints
    puts "\nConditional Breakpoints:"
    puts "=" * 50
    
    puts "Example of conditional breakpoints:"
    puts ""
    puts "# Break when condition is met"
    puts "break if user.id == 123"
    puts ""
    puts "# Break on specific line in specific file"
    puts "break /path/to/file.rb:45 if condition"
    puts ""
    puts "# Break when exception occurs"
    puts "catch StandardError"
    puts ""
    puts "# Watch for variable changes"
    puts "watch @user_name"
    puts ""
    puts "# In debugger session:"
    puts "break condition: user.active?"
    puts "continue"
  end
  
  def self.thread_debugging
    puts "\nThread Debugging:"
    puts "=" * 50
    
    puts "Thread debugging commands:"
    puts ""
    puts "# List all threads"
    puts "info threads"
    puts ""
    puts "# Switch to specific thread"
    puts "thread switch 2"
    puts ""
    puts "# Show thread backtrace"
    puts "thread backtrace"
    puts ""
    puts "# Stop thread"
    puts "thread stop"
    
    # Example multi-threaded code that benefits from debugging
    def process_with_threads(data)
      threads = []
      
      data.each_with_index do |item, index|
        threads << Thread.new do
          # This would be a good place for a breakpoint
          result = process_item(item, index)
          puts "Thread #{Thread.current.object_id} processed item #{index}"
          result
        end
      end
      
      threads.each(&:join)
    end
    
    def process_item(item, index)
      # Complex processing logic
      sleep(rand(0.1..0.5))
      "#{item}_processed_#{index}"
    end
    
    # Example usage
    data = ["item1", "item2", "item3"]
    puts "Multi-threaded processing ready for debugging"
    # process_with_threads(data)  # Uncomment to test
  end
end

# Run advanced debugger examples
AdvancedDebuggerExample.demonstrate_debugger
AdvancedDebuggerExample.debugger_commands
AdvancedDebuggerExample.conditional_breakpoints
AdvancedDebuggerExample.thread_debugging
```

### 2. Byebug Debugger

Using Byebug for advanced debugging:

```ruby
# First, install byebug: gem install byebug
require 'byebug'

class ByebugExample
  def self.demonstrate_byebug
    puts "Byebug Debugger:"
    puts "=" * 50
    
    puts "Byebug features:"
    puts "- Step debugging"
    puts "- Breakpoint management"
    "- Exception handling"
    "- Thread debugging"
    "- Variable inspection"
    "- Method navigation"
    
    # Example function with debugging hooks
    def recursive_function(n, depth = 0)
      byebug if depth == 5  # Break at specific depth
      
      if n <= 1
        return 1
      else
        return n + recursive_function(n - 1, depth + 1)
      end
    end
    
    # Example usage
    puts "Recursive function ready for debugging"
    # result = recursive_function(10)  # Uncomment to test
  end
  
  def self.exception_debugging
    puts "\nException Debugging:"
    puts "=" * 50
    
    def risky_operation(data)
      begin
        # This might raise exceptions
        result = data.map do |item|
          if item.nil?
            raise ArgumentError, "Item cannot be nil"
          end
          
          if item == "error"
            raise StandardError, "Simulated error"
          end
          
          item.upcase
        end
        
        result
      rescue ArgumentError => e
        puts "ArgumentError caught: #{e.message}"
        []
      rescue StandardError => e
        puts "StandardError caught: #{e.message}"
        ["error"]
      end
    end
    
    # Test exception handling
    test_data = ["hello", nil, "world", "error", "ruby"]
    result = risky_operation(test_data)
    puts "Result: #{result}"
  end
  
  def self.variable_inspection
    puts "\nVariable Inspection:"
    puts "=" * 50
    
    def inspect_variables
      local_var = "local value"
      @instance_var = "instance value"
      @@class_var = "class value"
      
      # In byebug, you can inspect these with:
      # local_var
      # instance_variables
      # self.class_variables
      # self.methods
      
      puts "Variables ready for inspection:"
      puts "Local: #{local_var}"
      puts "Instance: #{@instance_var}"
      puts "Class: #{@@class_var}"
      
      # Show available methods
      puts "Available methods: #{self.methods.length}"
      puts "Public methods: #{self.public_methods.length}"
    end
    
    inspect_variables
  end
end

# Run byebug examples
ByebugExample.demonstrate_byebug
ByebugExample.exception_debugging
ByebugExample.variable_inspection
```

## 🔍 Performance Debugging

### 1. Performance Profiling for Debugging

Debug performance issues:

```ruby
class PerformanceDebugger
  def self.profile_slow_operations
    puts "Performance Profiling for Debugging:"
    puts "=" * 50
    
    require 'benchmark'
    
    # Profile different approaches to find performance issues
    methods = {
      "String concatenation" => -> {
        str = ""
        1000.times { |i| str += "item_#{i}" }
        str
      },
      
      "Array.join" => -> {
        items = 1000.times.map { |i| "item_#{i}" }
        items.join
      },
      
      "StringIO" => -> {
        io = StringIO.new
        1000.times { |i| io.write("item_#{i}") }
        io.string
      }
    }
    
    puts "Performance comparison:"
    Benchmark.bm(20) do |x|
      methods.each do |name, method|
        x.report(name) do
          method.call
        end
      end
    end
    
    # Identify potential performance issues
    puts "\nPerformance Issues to Watch For:"
    puts "- String concatenation in loops"
    puts "- Excessive object creation"
    puts "- Inefficient data structures"
    puts "- Blocking I/O operations"
    puts "- Unnecessary computations"
  end
  
  def self.memory_profiling
    puts "\nMemory Profiling:"
    puts "=" * 50
    
    require 'objspace'
    
    def analyze_memory_usage
      GC.start
      before = ObjectSpace.count_objects
      
      # Create objects
      strings = []
      arrays = []
      hashes = []
      
      1000.times { |i| strings << "string_#{i}" }
      100.times { |i| arrays << (1..100).to_a }
      100.times { |i| hashes[i] = "value_#{i}" }
      
      GC.start
      after = ObjectSpace.count_objects
      
      delta = after.transform_values { |after_val, before_val| after_val - before_val }
      
      puts "Memory allocation:"
      puts "  Strings: #{delta[:T_STRING]}"
      puts "  Arrays: #{delta[:T_ARRAY]}"
      puts "  Hashes: #{delta[:T_HASH]}"
      puts "  Total objects: #{delta[:TOTAL]}"
      
      # Check for potential memory leaks
      strings = nil  # Clear reference
      GC.start
      after_clear = ObjectSpace.count_objects[:T_STRING]
      
      puts "Strings after cleanup: #{after_clear}"
      puts "Potential leak: #{after_clear > 0 ? 'Yes' : 'No'}"
    end
    
    analyze_memory_usage
  end
  
  def self.bottleneck_identification
    puts "\nBottleneck Identification:"
    puts "=" * 50
    
    # Simulate a complex operation with potential bottlenecks
    def complex_operation(data)
      # Potential bottleneck 1: Synchronous processing
      results = []
      data.each do |item|
        # Simulate processing time
        sleep(0.01)
        processed = process_item(item)
        results << processed
      end
      
      # Potential bottleneck 2: Inefficient sorting
      results.sort_by { |r| r.length }
    end
    
    def process_item(item)
      # Potential bottleneck 3: String operations
      processed = item.upcase.reverse
      processed.gsub(/[AEIOU]/, '')
    end
    
    # Profile the operation
    data = 100.times.map { |i| "item_#{i}" }
    
    time = Benchmark.measure do
      complex_operation(data)
    end
    
    puts "Operation took: #{time.real.round(4)}s"
    
    # Identify bottlenecks
    puts "Potential bottlenecks:"
    puts "1. Synchronous processing - consider parallel processing"
    puts "2. Inefficient sorting - consider custom sort"
    puts "3. String operations - consider optimization"
  end
  
  def self.concurrent_performance_analysis
    puts "\nConcurrent Performance Analysis:"
    puts "=" * 50
    
    require 'thread'
    
    def sequential_processing(data)
      data.map { |item| expensive_operation(item) }
    end
    
    def parallel_processing(data, thread_count = 4)
      chunk_size = (data.length / thread_count.to_f).ceil
      chunks = data.each_slice(chunk_size).to_a
      
      threads = chunks.map.with_index do |chunk, index|
        Thread.new do
          chunk.map { |item| expensive_operation(item) }
        end
      end
      
      threads.flat_map(&:value)
    end
    
    def expensive_operation(item)
      # Simulate expensive computation
      sleep(0.01)
      "#{item}_processed"
    end
    
    # Compare sequential vs parallel
    data = 100.times.map { |i| "item_#{i}" }
    
    sequential_time = Benchmark.measure do
      sequential_processing(data)
    end
    
    parallel_time = Benchmark.measure do
      parallel_processing(data)
    end
    
    puts "Sequential time: #{sequential_time.real.round(4)}s"
    puts "Parallel time: #{parallel_time.real.round(4)}s"
    puts "Speedup: #{(sequential_time.real / parallel_time.real).round(2)}x"
  end
  
  # Run performance debugging examples
  profile_slow_operations
  memory_profiling
  bottleneck_identification
  concurrent_performance_analysis
end
```

### 2. Resource Usage Debugging

Debug resource consumption issues:

```ruby
class ResourceDebugger
  def self.cpu_usage_analysis
    puts "CPU Usage Analysis:"
    puts "=" * 50
    
    def cpu_intensive_task(duration)
      start_time = Time.now
      
      # CPU intensive operation
      result = 0
      (1..1000000).each { |i| result += i }
      
      end_time = Time.now
      actual_duration = end_time - start_time
      
      puts "Requested duration: #{duration}s"
      puts "Actual duration: #{actual_duration.round(4)}s"
      puts "Result: #{result}"
      
      result
    end
    
    def io_intensive_task(duration)
      start_time = Time.now
      
      # I/O intensive operation
      file = Tempfile.new('cpu_analysis')
      
      duration.to_i.times do |i|
        file.puts("Line #{i}: Some data for testing I/O")
        file.flush
        sleep(0.01) if i % 10 == 0  # Simulate I/O wait
      end
      
      file.close
      
      end_time = Time.now
      actual_duration = end_time - start_time
      
      puts "I/O task duration: #{actual_duration.round(4)}s"
    end
    
    def mixed_task(duration)
      start_time = Time.now
      
      # Mixed CPU and I/O operations
      file = Tempfile.new('mixed_analysis')
      
      duration.to_i.times do |i|
        # CPU work
        result = i * 2
        
        # I/O work
        file.puts("Result: #{result}")
        
        # Mixed processing
        if i.odd?
          # More CPU work
          (1..1000).each { |j| result += j }
        else
          # More I/O work
          file.flush
          sleep(0.005)
        end
      end
      
      file.close
      
      end_time = Time.now
      actual_duration = end_time - start_time
      
      puts "Mixed task duration: #{actual_duration.round(4)}s"
    end
    
    # Test different task types
    puts "Testing CPU-intensive task:"
    cpu_intensive_task(2)
    
    puts "\nTesting I/O-intensive task:"
    io_intensive_task(2)
    
    puts "\nTesting mixed task:"
    mixed_task(2)
  end
  
  def self.memory_usage_tracking
    puts "\nMemory Usage Tracking:"
    puts "=" * 50
    
    def track_memory_usage(label, &block)
      GC.start
      before = ObjectSpace.count_objects
      
      result = block.call
      
      GC.start
      after = ObjectSpace.count_objects
      
      delta = after.transform_values { |after_val, before_val| after_val - before_val }
      
      puts "#{label}:"
      puts "  Total objects: #{delta[:TOTAL]}"
      puts "  Strings: #{delta[:T_STRING]}"
      puts "  Arrays: #{delta[:T_ARRAY]}"
      puts "  Hashes: #{delta[:T_HASH]}"
      
      result
    end
    
    # Track different memory usage patterns
    track_memory_usage("String creation") do
      strings = []
      1000.times { |i| strings << "string_#{i}" }
      strings
    end
    
    track_memory_usage("Array creation") do
      arrays = []
      100.times { |i| arrays << (1..1000).to_a }
      arrays
    end
    
    track_memory_usage("Hash creation") do
      hashes = {}
      100.times { |i| hashes[i] = "value_#{i}" }
      hashes
    end
    
    track_memory_usage("Object graph") do
      root = create_object_graph(100)
      root
    end
  end
  
  def self.create_object_graph(size)
    root = { id: 0, children: [] }
    
    (1...size).each do |i|
      parent_id = (i - 1) / 10
      node = { id: i, children: [] }
      
      if parent_id < size / 10
        root[:children][parent_id] ||= { id: parent_id, children: [] }
        root[:children][parent_id][:children] << node
      else
        root[:children] << node
      end
    end
    
    root
  end
  
  def self.resource_leak_detection
    puts "\nResource Leak Detection:"
    puts "=" * 50
    
    def simulate_potential_leaks
      puts "Simulating potential resource leaks..."
      
      # Potential leak 1: Unclosed files
      files = []
      10.times { |i| files << File.open("temp_#{i}.txt", 'w') }
      
      # Don't close files - potential leak
      # files.each(&:close)  # This would prevent the leak
      
      # Potential leak 2: Thread references
      threads = []
      5.times do |i|
        threads << Thread.new do
          loop do
            sleep(1)  # Thread never exits
          end
        end
      end
      
      # Don't join threads - potential leak
      # threads.each(&:join)  # This would prevent the leak
      
      # Potential leak 3: Large object references
      @large_objects = []
      100.times { |i| @large_objects << "Large object #{i}" * 1000 }
      
      puts "Created potential leaks"
      puts "Files: #{files.length} (unclosed)"
      puts "Threads: #{threads.length} (running)"
      puts "Large objects: #{@large_objects.length}"
    end
    
    def check_for_leaks
      puts "Checking for resource leaks..."
      
      # Check file handles
      open_files = Dir.glob("temp_*")
      puts "Open temp files: #{open_files.length}"
      
      # Check thread count
      thread_count = Thread.list.length
      puts "Active threads: #{thread_count}"
      
      # Check object counts
      GC.start
      object_count = ObjectSpace.count_objects[:TOTAL]
      puts "Total objects: #{object_count}"
      
      # Cleanup
      open_files.each { |file| File.delete(file) }
    end
    
    simulate_potential_leaks
    check_for_leaks
  end
  
  # Run resource usage examples
  cpu_usage_analysis
  memory_usage_tracking
  resource_leak_detection
end
```

## 🌐 Network and I/O Debugging

### 1. Network Request Debugging

Debug network-related issues:

```ruby
class NetworkDebugger
  def self.http_request_debugging
    puts "HTTP Request Debugging:"
    puts "=" * 50
    
    require 'net/http'
    require 'uri'
    require 'json'
    
    def debug_http_request(url, options = {})
      uri = URI(url)
      
      puts "Request details:"
      puts "  URL: #{url}"
      puts "  Method: #{options[:method] || 'GET'}"
      puts "  Headers: #{options[:headers]}"
      puts "  Body: #{options[:body]}" if options[:body]
      
      start_time = Time.now
      
      begin
        http = Net::HTTP.new(uri.host, uri.port)
        http.use_ssl = uri.scheme == 'https'
        
        request_class = case options[:method]
                       when 'POST'
                         Net::HTTP::Post
                       when 'PUT'
                         Net::HTTP::Put
                       when 'DELETE'
                         Net::HTTP::Delete
                       else
                         Net::HTTP::Get
                       end
        
        request = request_class.new(uri)
        
        # Add headers
        options[:headers]&.each { |key, value| request[key] = value }
        
        # Add body
        request.body = options[:body] if options[:body]
        
        response = http.request(request)
        
        end_time = Time.now
        duration = end_time - start_time
        
        puts "Response details:"
        puts "  Status: #{response.code}"
        puts "  Headers: #{response.to_hash}"
        puts "  Body length: #{response.body.length}"
        puts "  Duration: #{duration.round(4)}s"
        
        # Parse response if JSON
        if response['Content-Type']&.include?('application/json')
          begin
            json_body = JSON.parse(response.body)
            puts "  JSON keys: #{json_body.keys}"
            puts "  JSON preview: #{json_body.inspect[0..100]}..."
          rescue JSON::ParserError => e
            puts "  JSON parse error: #{e.message}"
          end
        end
        
        response
        
      rescue => e
        puts "Request failed: #{e.class}: #{e.message}"
        puts "Backtrace: #{e.backtrace.first(3)}"
        nil
      end
    end
    
    # Test different request scenarios
    puts "Testing successful request:"
    debug_http_request('https://httpbin.org/get')
    
    puts "\nTesting POST request:"
    debug_http_request('https://httpbin.org/post', {
      method: 'POST',
      headers: { 'Content-Type' => 'application/json' },
      body: { key: 'value' }.to_json
    })
    
    puts "\nTesting error case:"
    debug_http_request('https://httpbin.org/status/404')
  end
  
  def self.connection_pool_debugging
    puts "\nConnection Pool Debugging:"
    puts "=" * 50
    
    class SimpleConnectionPool
      def initialize(size = 5)
        @size = size
        @pool = Queue.new
        @created = 0
        @mutex = Mutex.new
        
        # Pre-populate pool
        size.times { create_connection }
      end
      
      def with_connection(&block)
        connection = checkout
        begin
          yield connection
        ensure
          checkin(connection)
        end
      end
      
      private
      
      def checkout
        @pool.pop(timeout: 5)
      rescue ThreadError
        create_connection if @created < @size * 2
        retry
      end
      
      def checkin(connection)
        @pool.push(connection)
      end
      
      def create_connection
        @mutex.synchronize do
          @created += 1
          puts "Creating connection #{@created}"
          "Connection #{@created}"
        end
      end
      
      def stats
        @mutex.synchronize do
          {
            created: @created,
            available: @pool.length,
            in_use: @created - @pool.length
          }
        end
      end
    end
    
    # Test connection pool
    pool = SimpleConnectionPool.new(3)
    
    puts "Initial pool stats: #{pool.stats}"
    
    # Simulate concurrent usage
    threads = 10.times.map do |i|
      Thread.new do
        pool.with_connection do |connection|
          puts "Thread #{i} using #{connection}"
          sleep(rand(0.1..0.5))
        end
      end
    end
    
    threads.each(&:join)
    
    puts "Final pool stats: #{pool.stats}"
  end
  
  def self.timeout_debugging
    puts "\nTimeout Debugging:"
    puts "=" * 50
    
    def operation_with_timeout(timeout, &block)
      result = nil
      
      thread = Thread.new do
        result = block.call
      end
      
      if thread.join(timeout)
        result
      else
        thread.kill
        raise TimeoutError, "Operation timed out after #{timeout}s"
      end
    end
    
    # Test timeout scenarios
    puts "Testing fast operation:"
    begin
      result = operation_with_timeout(2) do
        sleep(0.5)
        "Fast operation completed"
      end
      puts "Result: #{result}"
    rescue TimeoutError => e
      puts "Timeout: #{e.message}"
    end
    
    puts "\nTesting slow operation:"
    begin
      result = operation_with_timeout(1) do
        sleep(2)
        "Slow operation completed"
      end
      puts "Result: #{result}"
    rescue TimeoutError => e
      puts "Timeout: #{e.message}"
    end
    
    puts "\nTesting error operation:"
    begin
      result = operation_with_timeout(2) do
        raise "Simulated error"
      end
      puts "Result: #{result}"
    rescue TimeoutError => e
      puts "Timeout: #{e.message}"
    rescue => e
      puts "Error: #{e.message}"
    end
  end
  
  # Run network debugging examples
  http_request_debugging
  connection_pool_debugging
  timeout_debugging
end
```

### 2. Database Debugging

Debug database-related issues:

```ruby
class DatabaseDebugger
  def self.query_debugging
    puts "Database Query Debugging:"
    puts "=" * 50
    
    # Simulate database operations
    def execute_query(sql, params = [])
      puts "Executing query:"
      puts "  SQL: #{sql}"
      puts "  Parameters: #{params}"
      
      start_time = Time.now
      
      begin
        # Simulate query execution
        sleep(0.1)  # Simulate database work
        
        # Simulate different query types
        result = case sql
                when /SELECT/i
                  { rows: [{ id: 1, name: 'Test' }], affected_rows: 0 }
                when /INSERT/i
                  { rows: [], affected_rows: 1 }
                when /UPDATE/i
                  { rows: [], affected_rows: 5 }
                when /DELETE/i
                  { rows: [], affected_rows: 3 }
                else
                  { rows: [], affected_rows: 0 }
                end
        
        end_time = Time.now
        duration = end_time - start_time
        
        puts "Query results:"
        puts "  Rows returned: #{result[:rows].length}"
        puts "  Rows affected: #{result[:affected_rows]}"
        puts "  Duration: #{duration.round(4)}s"
        
        result
        
      rescue => e
        puts "Query failed: #{e.class}: #{e.message}"
        { rows: [], affected_rows: 0, error: e.message }
      end
    end
    
    # Test different query types
    execute_query("SELECT * FROM users WHERE id = ?", [1])
    execute_query("INSERT INTO users (name) VALUES (?)", ['John'])
    execute_query("UPDATE users SET name = ? WHERE id = ?", ['Jane', 1])
    execute_query("DELETE FROM users WHERE id = ?", [1])
    execute_query("INVALID SQL QUERY")
  end
  
  def self.connection_debugging
    puts "\nDatabase Connection Debugging:"
    puts "=" * 50
    
    class DatabaseConnection
      def initialize(connection_string)
        @connection_string = connection_string
        @connected = false
        @query_count = 0
      end
      
      def connect
        puts "Connecting to database: #{@connection_string}"
        
        # Simulate connection
        sleep(0.5)
        @connected = true
        puts "Connected successfully"
      end
      
      def disconnect
        puts "Disconnecting from database"
        @connected = false
      end
      
      def execute_query(sql, params = [])
        raise "Not connected" unless @connected
        
        @query_count += 1
        puts "Executing query #{@query_count}: #{sql}"
        
        # Simulate query execution
        sleep(0.1)
        "Query #{@query_count} result"
      end
      
      def connection_info
        {
          connected: @connected,
          query_count: @query_count,
          connection_string: @connection_string
        }
      end
    end
    
    # Test connection management
    db = DatabaseConnection.new("postgresql://localhost/test")
    
    puts "Initial connection info: #{db.connection_info}"
    
    begin
      db.connect
      puts "Connection info after connect: #{db.connection_info}"
      
      5.times { |i| db.execute_query("SELECT * FROM table_#{i}") }
      
      puts "Connection info after queries: #{db.connection_info}"
      
    ensure
      db.disconnect
      puts "Connection info after disconnect: #{db.connection_info}"
    end
  end
  
  def self.transaction_debugging
    puts "\nTransaction Debugging:"
    puts "=" * 50
    
    class TransactionManager
      def initialize
        @transactions = []
        @current_transaction = nil
      end
      
      def transaction(&block)
        transaction_id = @transactions.length + 1
        
        puts "Starting transaction #{transaction_id}"
        @current_transaction = transaction_id
        @transactions << transaction_id
        
        begin
          result = yield
          
          puts "Committing transaction #{transaction_id}"
          result
          
        rescue => e
          puts "Rolling back transaction #{transaction_id}: #{e.message}"
          raise
        ensure
          @current_transaction = nil
        end
      end
      
      def nested_transaction(&block)
        puts "Nested transaction in transaction #{@current_transaction}"
        transaction(&block)
      end
      
      def transaction_info
        {
          total_transactions: @transactions.length,
          current_transaction: @current_transaction,
          transaction_history: @transactions
        }
      end
    end
    
    # Test transaction management
    transaction_manager = TransactionManager.new
    
    puts "Initial transaction info: #{transaction_manager.transaction_info}"
    
    begin
      transaction_manager.transaction do
        puts "Inside transaction"
        
        transaction_manager.nested_transaction do
          puts "Inside nested transaction"
        end
        
        puts "Back in main transaction"
      end
      
    rescue => e
      puts "Transaction failed: #{e.message}"
    end
    
    puts "Final transaction info: #{transaction_manager.transaction_info}"
  end
  
  def self.performance_debugging
    puts "\nDatabase Performance Debugging:"
    puts "=" * 50
    
    def profile_query(query_name, &block)
      start_time = Time.now
      
      result = block.call
      
      end_time = Time.now
      duration = end_time - start_time
      
      puts "#{query_name} performance:"
      puts "  Duration: #{duration.round(4)}s"
      
      if duration > 1.0
        puts "  WARNING: Slow query detected"
      end
      
      result
    end
    
    # Test query performance
    profile_query("Fast query") do
      sleep(0.1)
      "Fast result"
    end
    
    profile_query("Slow query") do
      sleep(1.5)
      "Slow result"
    end
    
    profile_query("Error query") do
      sleep(0.2)
      raise "Simulated database error"
    end
  end
  
  # Run database debugging examples
  query_debugging
  connection_debugging
  transaction_debugging
  performance_debugging
end
```

## 🎯 Advanced Debugging Strategies

### 1. Heisenbug Debugging

Debugging issues that change when observed:

```ruby
class HeisenbugDebugger
  def self.heisenbug_examples
    puts "Heisenbug Examples:"
    puts "=" * 50
    
    # Heisenbug 1: Timing-dependent bugs
    def timing_dependent_bug
      # This bug only occurs under specific timing conditions
      if Time.now.to_f % 1 < 0.5
        raise "Timing-dependent bug triggered!"
      end
      
      "Normal result"
    end
    
    # Heisenbug 2: Thread-related bugs
    def thread_dependent_bug
      threads = []
      results = []
      
      3.times do |i|
        threads << Thread.new do
          # This bug only occurs when threads interleave in a specific way
          sleep(rand(0.01..0.1))
          result = "Thread #{i} result"
          results << result
        end
      end
      
      threads.each(&:join)
      
      # Bug: results order depends on thread scheduling
      if results.length == 3 && results.first == "Thread 1 result"
        puts "Bug occurred: Unexpected thread order"
      end
      
      results
    end
    
    # Heisenbug 3: Memory-dependent bugs
    def memory_dependent_bug
      # This bug only occurs under specific memory conditions
      if ObjectSpace.count_objects[:T_STRING] > 10000
        raise "Memory-dependent bug triggered!"
      end
      
      "Normal result"
    end
    
    # Demonstrate heisenbugs
    puts "Testing timing-dependent bug:"
    5.times do |i|
      begin
        result = timing_dependent_bug
        puts "Attempt #{i + 1}: #{result}"
      rescue => e
        puts "Attempt #{i + 1}: Bug triggered - #{e.message}"
      end
    end
    
    puts "\nTesting thread-dependent bug:"
    3.times do |i|
      results = thread_dependent_bug
      puts "Attempt #{i + 1}: #{results.join(', ')}"
    end
    
    puts "\nTesting memory-dependent bug:"
    # Create many strings to trigger memory condition
    10000.times { |i| "string_#{i}" }
    
    begin
      result = memory_dependent_bug
      puts "Result: #{result}"
    rescue => e
      puts "Bug triggered: #{e.message}"
    end
  end
  
  def self.heisenbug_debugging_techniques
    puts "\nHeisenbug Debugging Techniques:"
    puts "=" * 50
    
    techniques = [
      "1. Add logging instead of breakpoints",
      "2. Use non-intrusive debugging",
      "3. Reproduce conditions consistently",
      "4. Minimize environmental changes",
      "5. Use automated testing",
      "6. Record detailed context",
      "7. Test with different timing",
      "8. Isolate the problematic code"
    ]
    
    techniques.each { |technique| puts technique }
  end
  
  def self.debug_heisenbug
    puts "\nDebugging Heisenbugs:"
    puts "=" * 50
    
    def debug_with_logging
      puts "Debugging with detailed logging..."
      
      log_entry = {
        timestamp: Time.now,
        thread_id: Thread.current.object_id,
        memory_usage: ObjectSpace.count_objects[:TOTAL],
        environment: ENV['RUBY_ENV'] || 'development'
      }
      
      puts "Debug context: #{log_entry}"
      
      # Add more context
      puts "Thread count: #{Thread.list.length}"
      puts "Process ID: #{Process.pid}"
      
      begin
        timing_dependent_bug
      rescue => e
        puts "Bug occurred with context:"
        puts "  Timestamp: #{log_entry[:timestamp]}"
        puts "  Thread ID: #{log_entry[:thread_id]}"
        puts "  Memory usage: #{log_entry[:memory_usage]}"
        puts "  Environment: #{log_entry[:environment]}"
        raise e
      end
    end
    
    debug_with_logging
  end
  
  # Run heisenbug examples
  heisenbug_examples
  heisenbug_debugging_techniques
  debug_heisenbug
end
```

### 2. Production Debugging

Safe debugging in production environments:

```ruby
class ProductionDebugger
  def self.safe_production_debugging
    puts "Safe Production Debugging:"
    puts "=" * 50
    
    # Safe debugging configuration
    DEBUG_ENABLED = ENV['DEBUG_ENABLED'] == 'true'
    DEBUG_LEVEL = ENV['DEBUG_LEVEL'] || 'info'
    
    class SafeLogger
      def self.debug(message, context = {})
        return unless DEBUG_ENABLED
        return unless should_log?(:debug)
        
        log_entry = {
          timestamp: Time.now,
          level: :debug,
          message: message,
          context: context,
          thread_id: Thread.current.object_id
        }
        
        # Log to file instead of console
        log_to_file(log_entry)
      end
      
      def self.info(message, context = {})
        return unless DEBUG_ENABLED
        return unless should_log?(:info)
        
        log_entry = {
          timestamp: Time.now,
          level: :info,
          message: message,
          context: context,
          thread_id: Thread.current.object_id
        }
        
        log_to_file(log_entry)
      end
      
      def self.error(message, context = {})
        log_entry = {
          timestamp: Time.now,
          level: :error,
          message: message,
          context: context,
          thread_id: Thread.current.object_id
        }
        
        log_to_file(log_entry)
      end
      
      private
      
      def self.should_log?(level)
        levels = { debug: 0, info: 1, warn: 2, error: 3 }
        levels[level] >= levels[DEBUG_LEVEL.to_sym]
      end
      
      def self.log_to_file(log_entry)
        log_file = ENV['DEBUG_LOG_FILE'] || 'debug.log'
        
        begin
          File.open(log_file, 'a') do |file|
            file.puts(log_entry.to_json)
          end
        rescue => e
          # Fallback to console if file logging fails
          puts "Failed to log to file: #{e.message}"
          puts log_entry.to_json
        end
      end
    end
    
    # Safe debugging function
    def safe_debug_function(data)
      SafeLogger.debug("Starting safe_debug_function", { data_size: data.length })
      
      begin
        # Add detailed context
        SafeLogger.debug("Processing data", { 
          data_sample: data.first(3),
          data_type: data.class.name
        })
        
        result = data.map { |item| item.upcase }
        
        SafeLogger.debug("Function completed", { 
          result_size: result.length,
          processing_time: nil
        })
        
        result
        
      rescue => e
        SafeLogger.error("Function failed", { 
          error: e.message,
          backtrace: e.backtrace.first(3)
        })
        
        raise
      end
    end
    
    # Test safe debugging
    test_data = ["item1", "item2", "item3"]
    result = safe_debug_function(test_data)
    
    puts "Function result: #{result}"
    puts "Check debug.log for detailed logs"
  end
  
  def self.remote_debugging
    puts "\nRemote Debugging:"
    puts "=" * 50
    
    class RemoteDebugger
      def initialize(port = 8080)
        @port = port
        @server = nil
        @debug_info = []
        @mutex = Mutex.new
      end
      
      def start
        @server = TCPServer.new('localhost', @port)
        puts "Remote debugging server started on port #{@port}"
        
        loop do
          client = @server.accept
          Thread.new { handle_client(client) }
        end
      rescue => e
        puts "Server error: #{e.message}"
      ensure
        @server&.close
      end
    end
    
    def stop
      @server&.close
      puts "Remote debugging server stopped"
    end
    
    def add_debug_info(info)
      @mutex.synchronize do
        @debug_info << {
          timestamp: Time.now,
          info: info
        }
        
        # Keep only last 100 entries
        @debug_info.shift if @debug_info.length > 100
      end
    end
    
    def get_debug_info
      @mutex.synchronize { @debug_info.dup }
    end
    
    private
    
    def handle_client(client)
      request = client.gets
      return unless request
      
      case request.strip
      when 'info'
        info = get_debug_info
        client.print("Debug Info:\n")
        info.each { |entry| client.print("#{entry[:timestamp]}: #{entry[:info]}\n") }
        
      when 'status'
        status = {
          server_running: !@server.nil?,
          debug_entries: get_debug_info.length,
          timestamp: Time.now
        }
        client.print("Status: #{status.to_json}\n")
        
      when 'help'
        help_text = [
          "Available commands:",
          "  info - Show debug information",
          "  status - Show server status",
          "  help - Show this help"
        ]
        client.print(help_text.join("\n"))
        
      else
        client.print("Unknown command: #{request.strip}")
      end
      
      client.close
    end
    end
    
    # Test remote debugging
    debugger = RemoteDebugger.new.new
    
    # Start server in separate thread
    server_thread = Thread.new { debugger.start }
    
    # Add some debug info
    5.times do |i|
      debugger.add_debug_info("Debug entry #{i}")
    end
    
    puts "Remote debugging server is running"
    puts "Connect with: telnet localhost 8080"
    puts "Try commands: info, status, help"
    
    sleep(5)  # Let server run for a bit
    
    debugger.stop
    server_thread.join
  end
  
  def self.conditional_debugging
    puts "\nConditional Debugging:"
    puts "=" * 50
    
    # Environment-based debugging
    def debug_in_production?
      return false unless ENV['RAILS_ENV'] == 'production'
      return false unless ENV['DEBUG_USER_ID']
      return false unless ENV['DEBUG_USER_ID'] == ENV['CURRENT_USER_ID']
      
      true
    end
    
    # Feature flag debugging
    def debug_feature?(feature_name)
      ENV["DEBUG_#{feature_name.upcase}"] == 'true'
    end
    
    # Time-based debugging
    def debug_time_window?
      start_time = Time.parse(ENV['DEBUG_START'] || '00:00:00')
      end_time = Time.parse(ENV['DEBUG_END'] || '23:59:59')
      current_time = Time.now
      
      current_time >= start_time && current_time <= end_time
    end
    
    # User-based debugging
    def debug_for_user?(user_id)
      debug_users = (ENV['DEBUG_USERS'] || '').split(',')
      debug_users.include?(user_id.to_s)
    end
    
    # Test conditional debugging
    puts "Environment debugging: #{debug_in_production?}"
    puts "Feature debugging (test_feature): #{debug_feature?(:test_feature)}"
    puts "Time debugging: #{debug_time_window?}"
    puts "User debugging (user123): #{debug_for_user?(123)}"
    
    # Example usage in production
    def production_operation(user_id, data)
      debug_info = { user_id: user_id, data_size: data.length }
      
      if debug_in_production?
        puts "DEBUG: Production operation for user #{user_id}"
        puts "DEBUG: Data size: #{data.length}"
      end
      
      if debug_feature?(:advanced_processing)
        puts "DEBUG: Advanced processing enabled"
        # Enable advanced debugging
      end
      
      if debug_time_window?
        puts "DEBUG: Inside debug time window"
        # Enable time-based debugging
      end
      
      if debug_for_user?(user_id)
        puts "DEBUG: Debugging for user #{user_id}"
        # Enable user-specific debugging
      end
      
      # Normal operation
      data.map(&:upcase)
    end
    
    # Test conditional debugging
    ENV['RAILS_ENV'] = 'production'
    ENV['DEBUG_USER_ID'] = 'user123'
    ENV['CURRENT_USER_ID'] = 'user123'
    ENV['DEBUG_TEST_FEATURE'] = 'true'
    ENV['DEBUG_START'] = '00:00:00'
    ENV['DEBUG_END'] = '23:59:59'
    ENV['DEBUG_USERS'] = '123,456,789'
    
    result = production_operation(123, ['test', 'data'])
    puts "Result: #{result}"
  end
  
  # Run production debugging examples
  safe_production_debugging
  remote_debugging
  conditional_debugging
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Advanced Debugger**: Use debug gem features
2. **Performance Debugging**: Profile slow operations
3. **Network Debugging**: Debug HTTP requests

### Intermediate Exercises

1. **Resource Debugging**: Debug memory and CPU usage
2. **Database Debugging**: Debug database queries
3. **Heisenbug Debugging**: Handle timing-dependent bugs

### Advanced Exercises

1. **Production Debugging**: Safe debugging in production
2. **Remote Debugging**: Implement remote debugging
3. **Conditional Debugging**: Environment-based debugging

---

## 🎯 Summary

Advanced debugging in Ruby provides:

- **Advanced Tools** - debug gem, byebug, profilers
- **Performance Debugging** - CPU, memory, and resource profiling
- **Network Debugging** - HTTP requests and connections
- **Heisenbug Debugging** - Handle timing-dependent issues
- **Production Debugging** - Safe debugging in production

Master these advanced techniques to solve complex debugging challenges!
