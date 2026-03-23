# Logging Strategies in Ruby
# Comprehensive guide to effective logging and monitoring

## 🎯 Overview

Effective logging is crucial for debugging, monitoring, and maintaining Ruby applications. This guide covers logging best practices, structured logging, and advanced logging strategies.

## 📝 Basic Logging Concepts

### 1. Ruby's Built-in Logging

Understanding Ruby's logging capabilities:

```ruby
class BasicLogging
  def self.simple_logging
    puts "Simple Logging Examples:"
    puts "=" * 50
    
    # Basic puts logging
    puts "This is a simple log message using puts"
    puts "Timestamp: #{Time.now}"
    puts "Process ID: #{Process.pid}"
    puts "Thread ID: #{Thread.current.object_id}"
    
    # Using p for debugging
    debug_info = {
      user_id: 123,
      action: "login",
      timestamp: Time.now
    }
    p debug_info
    
    # Using pp for pretty printing
    require 'pp'
    complex_data = {
      user: {
        id: 123,
        name: "John Doe",
        email: "john@example.com",
        preferences: {
          theme: "dark",
          language: "en",
          notifications: true
        }
      },
      session: {
        id: "sess_abc123",
        created_at: Time.now,
        last_activity: Time.now
      }
    }
    pp complex_data
  end
  
  def self.logger_levels
    puts "\nLogger Levels:"
    puts "=" * 50
    
    require 'logger'
    
    # Create logger with different levels
    logger = Logger.new(STDOUT)
    
    levels = {
      Logger::DEBUG => "DEBUG",
      Logger::INFO => "INFO",
      Logger::WARN => "WARN",
      Logger::ERROR => "ERROR",
      Logger::FATAL => "FATAL",
      Logger::UNKNOWN => "UNKNOWN"
    }
    
    levels.each do |level, name|
      puts "#{name}: #{level}"
    end
    
    # Demonstrate different log levels
    puts "\nLogging at different levels:"
    logger.level = Logger::DEBUG
    
    logger.debug("Debug message - detailed information")
    logger.info("Info message - general information")
    logger.warn("Warning message - potential issue")
    logger.error("Error message - error occurred")
    logger.fatal("Fatal message - critical error")
    logger.unknown("Unknown message - custom level")
  end
  
  def self.logger_configuration
    puts "\nLogger Configuration:"
    puts "=" * 50
    
    require 'logger'
    
    # File logging
    file_logger = Logger.new('application.log')
    file_logger.level = Logger::INFO
    
    # Console logging with formatting
    console_logger = Logger.new(STDOUT)
    console_logger.level = Logger::DEBUG
    
    # Custom formatter
    console_logger.formatter = proc do |severity, datetime, progname, msg|
      "#{datetime.strftime('%Y-%m-%d %H:%M:%S')} #{severity} -- #{msg}\n"
    end
    
    # Log to multiple destinations
    multi_logger = Logger.new(STDOUT)
    file_logger = Logger.new('multi.log')
    
    # Test loggers
    puts "File logging:"
    file_logger.info("This goes to file")
    
    puts "\nConsole logging with custom format:"
    console_logger.debug("Debug message with custom format")
    console_logger.info("Info message with custom format")
    
    puts "\nMultiple destinations:"
    multi_logger.info("Console message")
    file_logger.info("File message")
    
    # Check if files were created
    puts "\nLog files created:"
    puts "application.log exists: #{File.exist?('application.log')}"
    puts "multi.log exists: #{File.exist?('multi.log')}"
  end
  
  def self.contextual_logging
    puts "\nContextual Logging:"
    puts "=" * 50
    
    require 'logger'
    
    class ContextualLogger
      def initialize(base_logger)
        @logger = base_logger
        @context = {}
      end
      
      def with_context(context = {})
        old_context = @context.dup
        @context.merge!(context)
        
        yield
      ensure
        @context = old_context
      end
      
      def debug(message, extra_context = {})
        log_with_context(Logger::DEBUG, message, extra_context)
      end
      
      def info(message, extra_context = {})
        log_with_context(Logger::INFO, message, extra_context)
      end
      
      def warn(message, extra_context = {})
        log_with_context(Logger::WARN, message, extra_context)
      end
      
      def error(message, extra_context = {})
        log_with_context(Logger::ERROR, message, extra_context)
      end
      
      private
      
      def log_with_context(level, message, extra_context = {})
        full_context = @context.merge(extra_context)
        formatted_message = format_message(message, full_context)
        @logger.log(level, formatted_message)
      end
      
      def format_message(message, context)
        if context.empty?
          message
        else
          "#{message} | Context: #{context}"
        end
      end
    end
    
    # Test contextual logging
    base_logger = Logger.new(STDOUT)
    contextual_logger = ContextualLogger.new(base_logger)
    
    # Basic usage
    contextual_logger.info("User logged in", { user_id: 123, ip: "192.168.1.1" })
    
    # With context block
    contextual_logger.with_context(user_id: 123, session_id: "abc123") do
      contextual_logger.info("Processing user request")
      contextual_logger.debug("Validating user data")
      contextual_logger.info("Request processed successfully")
    end
    
    # Nested context
    contextual_logger.with_context(request_id: "req_456") do
      contextual_logger.info("Starting request processing")
      
      contextual_logger.with_context(operation: "database_query") do
        contextual_logger.debug("Executing SQL query")
        contextual_logger.info("Query completed")
      end
      
      contextual_logger.info("Request processing completed")
    end
  end
  
  # Run basic logging examples
  simple_logging
  logger_levels
  logger_configuration
  contextual_logging
end
```

### 2. Structured Logging

Implementing structured logging with JSON output:

```ruby
class StructuredLogging
  def self.json_logging
    puts "Structured JSON Logging:"
    puts "=" * 50
    
    require 'logger'
    require 'json'
    
    class StructuredLogger
      def initialize(output = STDOUT)
        @logger = Logger.new(output)
        @logger.formatter = method(:json_formatter)
      end
      
      def debug(message, context = {})
        log(Logger::DEBUG, message, context)
      end
      
      def info(message, context = {})
        log(Logger::INFO, message, context)
      end
      
      def warn(message, context = {})
        log(Logger::WARN, message, context)
      end
      
      def error(message, context = {})
        log(Logger::ERROR, message, context)
      end
      
      def fatal(message, context = {})
        log(Logger::FATAL, message, context)
      end
      
      private
      
      def log(level, message, context)
        log_entry = {
          timestamp: Time.now.iso8601,
          level: Logger::SEV_LABEL[level],
          message: message,
          process_id: Process.pid,
          thread_id: Thread.current.object_id
        }.merge(context)
        
        @logger.log(level, log_entry.to_json)
      end
      
      def json_formatter(severity, datetime, progname, msg)
        msg + "\n"
      end
    end
    
    # Test structured logging
    logger = StructuredLogger.new
    
    logger.info("User logged in", {
      user_id: 123,
      email: "john@example.com",
      ip_address: "192.168.1.1",
      user_agent: "Mozilla/5.0..."
    })
    
    logger.warn("Database connection slow", {
      query_time: 2.5,
      query: "SELECT * FROM users WHERE id = 123",
      database: "production"
    })
    
    logger.error("Payment processing failed", {
      order_id: "order_456",
      payment_method: "credit_card",
      error_code: "CARD_DECLINED",
      amount: 99.99
    })
  end
  
  def self.structured_logging_patterns
    puts "\nStructured Logging Patterns:"
    puts "=" * 50
    
    class ApplicationLogger
      def initialize(service_name, version = "1.0.0")
        @service_name = service_name
        @version = version
        @logger = Logger.new(STDOUT)
        @logger.formatter = method(:structured_formatter)
      end
      
      def log_event(event_name, data = {})
        log_entry = build_log_entry(:info, event_name, data)
        @logger.info(log_entry.to_json)
      end
      
      def log_error(error, context = {})
        log_entry = build_log_entry(:error, "error_occurred", context.merge(
          error_class: error.class.name,
          error_message: error.message,
          backtrace: error.backtrace&.first(5)
        ))
        @logger.error(log_entry.to_json)
      end
      
      def log_performance(operation, duration, context = {})
        log_entry = build_log_entry(:info, "performance_metric", context.merge(
          operation: operation,
          duration_ms: (duration * 1000).round(2)
        ))
        @logger.info(log_entry.to_json)
      end
      
      def log_business_event(event_name, data = {})
        log_entry = build_log_entry(:info, "business_event", data.merge(
          event_name: event_name,
          event_type: "business"
        ))
        @logger.info(log_entry.to_json)
      end
      
      private
      
      def build_log_entry(level, event_name, data)
        {
          timestamp: Time.now.iso8601,
          level: level.to_s,
          service: @service_name,
          version: @version,
          event: event_name,
          data: data,
          metadata: {
            process_id: Process.pid,
            thread_id: Thread.current.object_id,
            hostname: Socket.gethostname,
            ruby_version: RUBY_VERSION
          }
        }
      end
      
      def structured_formatter(severity, datetime, progname, msg)
        msg + "\n"
      end
    end
    
    # Test structured logging patterns
    app_logger = ApplicationLogger.new("user-service", "2.1.0")
    
    # Event logging
    app_logger.log_event("user_registered", {
      user_id: 123,
      email: "john@example.com",
      registration_source: "web"
    })
    
    # Error logging
    begin
      raise "Simulated error"
    rescue => e
      app_logger.log_error(e, { user_id: 123, action: "profile_update" })
    end
    
    # Performance logging
    start_time = Time.now
    sleep(0.1)  # Simulate work
    duration = Time.now - start_time
    app_logger.log_performance("database_query", duration, {
      query: "SELECT * FROM users",
      table: "users",
      rows_returned: 50
    })
    
    # Business event logging
    app_logger.log_business_event("order_completed", {
      order_id: "order_789",
      user_id: 123,
      total_amount: 99.99,
      payment_method: "credit_card",
      items: [
        { product_id: "prod_1", quantity: 2, price: 29.99 },
        { product_id: "prod_2", quantity: 1, price: 39.99 }
      ]
    })
  end
  
  def self.log_aggregation
    puts "\nLog Aggregation:"
    puts "=" * 50
    
    class LogAggregator
      def initialize
        @logs = []
        @mutex = Mutex.new
      end
      
      def add_log(log_entry)
        @mutex.synchronize do
          @logs << log_entry
          
          # Keep only last 1000 logs
          @logs.shift if @logs.length > 1000
        end
      end
      
      def query_logs(filters = {})
        @mutex.synchronize do
          filtered_logs = @logs.dup
          
          filters.each do |key, value|
            case key
            when :level
              filtered_logs = filtered_logs.select { |log| log[:level] == value }
            when :service
              filtered_logs = filtered_logs.select { |log| log[:service] == value }
            when :time_range
              start_time, end_time = value
              filtered_logs = filtered_logs.select do |log|
                log_time = Time.parse(log[:timestamp])
                log_time >= start_time && log_time <= end_time
              end
            when :search
              search_term = value
              filtered_logs = filtered_logs.select do |log|
                log[:message].include?(search_term) || 
                log[:data].to_s.include?(search_term)
              end
            end
          end
          
          filtered_logs
        end
      end
      
      def get_log_stats
        @mutex.synchronize do
          {
            total_logs: @logs.length,
            levels: @logs.group_by { |log| log[:level] }.transform_values(&:count),
            services: @logs.group_by { |log| log[:service] }.transform_values(&:count),
            time_range: {
              earliest: @logs.first&.dig(:timestamp),
              latest: @logs.last&.dig(:timestamp)
            }
          }
        end
      end
      
      def export_logs(format = :json)
        @mutex.synchronize do
          case format
          when :json
            @logs.to_json
          when :csv
            export_to_csv
          else
            @logs.inspect
          end
        end
      end
      
      private
      
      def export_to_csv
        return "" if @logs.empty?
        
        headers = @logs.first.keys
        csv_lines = [headers.join(',')]
        
        @logs.each do |log|
          values = headers.map { |header| log[header]&.to_s || "" }
          csv_lines << values.join(',')
        end
        
        csv_lines.join("\n")
      end
    end
    
    # Test log aggregation
    aggregator = LogAggregator.new
    
    # Add sample logs
    10.times do |i|
      log_entry = {
        timestamp: Time.now.iso8601,
        level: ['INFO', 'WARN', 'ERROR'].sample,
        service: "service_#{i % 3}",
        message: "Log message #{i}",
        data: { index: i }
      }
      aggregator.add_log(log_entry)
    end
    
    # Query logs
    error_logs = aggregator.query_logs(level: 'ERROR')
    puts "Error logs: #{error_logs.length}"
    
    service_logs = aggregator.query_logs(service: 'service_1')
    puts "Service 1 logs: #{service_logs.length}"
    
    # Get stats
    stats = aggregator.get_log_stats
    puts "\nLog Statistics:"
    puts "Total logs: #{stats[:total_logs]}"
    puts "By level: #{stats[:levels]}"
    puts "By service: #{stats[:services]}"
  end
  
  # Run structured logging examples
  json_logging
  structured_logging_patterns
  log_aggregation
end
```

## 🔧 Advanced Logging Strategies

### 1. Performance Logging

Logging for performance monitoring:

```ruby
class PerformanceLogging
  def self.method_timing
    puts "Method Timing Logging:"
    puts "=" * 50
    
    class PerformanceLogger
      def initialize(logger)
        @logger = logger
      end
      
      def time_method(klass, method_name)
        original_method = klass.instance_method(method_name)
        
        klass.define_method(method_name) do |*args, &block|
          start_time = Time.now
          
          begin
            result = original_method.bind(self).call(*args, &block)
            
            end_time = Time.now
            duration = end_time - start_time
            
            @logger.log_performance("#{klass.name}##{method_name}", duration, {
              args_count: args.length,
              has_block: block_given?,
              success: true
            })
            
            result
            
          rescue => e
            end_time = Time.now
            duration = end_time - start_time
            
            @logger.log_performance("#{klass.name}##{method_name}", duration, {
              args_count: args.length,
              has_block: block_given?,
              success: false,
              error: e.message
            })
            
            raise e
          end
        end
      end
      
      def time_block(description, &block)
        start_time = Time.now
        
        begin
          result = yield
          
          end_time = Time.now
          duration = end_time - start_time
          
          @logger.log_performance(description, duration, { success: true })
          
          result
          
        rescue => e
          end_time = Time.now
          duration = end_time - start_time
          
          @logger.log_performance(description, duration, { 
            success: false, 
            error: e.message 
          })
          
          raise e
        end
      end
    end
    
    # Example class to instrument
    class DataProcessor
      def initialize(logger)
        @logger = logger
      end
      
      def process_data(data)
        # Simulate processing
        sleep(rand(0.01..0.1))
        data.map(&:upcase)
      end
      
      def validate_data(data)
        # Simulate validation
        sleep(rand(0.005..0.02))
        data.all? { |item| item.is_a?(String) }
      end
      
      def save_data(data)
        # Simulate save
        sleep(rand(0.02..0.05))
        true
      end
    end
    
    # Test performance logging
    require 'logger'
    logger = Logger.new(STDOUT)
    app_logger = ApplicationLogger.new("data-processor")
    perf_logger = PerformanceLogger.new(app_logger)
    
    processor = DataProcessor.new(logger)
    
    # Instrument methods
    perf_logger.time_method(DataProcessor, :process_data)
    perf_logger.time_method(DataProcessor, :validate_data)
    perf_logger.time_method(DataProcessor, :save_data)
    
    # Test instrumented methods
    test_data = ["item1", "item2", "item3"]
    
    perf_logger.time_block("full_pipeline") do
      processor.validate_data(test_data)
      processed = processor.process_data(test_data)
      processor.save_data(processed)
    end
  end
  
  def self.database_query_logging
    puts "\nDatabase Query Logging:"
    puts "=" * 50
    
    class DatabaseQueryLogger
      def initialize(logger)
        @logger = logger
        @query_stats = {}
      end
      
      def log_query(query, duration, result = nil)
        query_type = extract_query_type(query)
        
        log_entry = {
          query: query,
          query_type: query_type,
          duration_ms: (duration * 1000).round(2),
          timestamp: Time.now.iso8601
        }
        
        if result
          log_entry[:rows_returned] = result.respond_to?(:count) ? result.count : result.length
          log_entry[:success] = true
        end
        
        @logger.log_event("database_query", log_entry)
        
        # Update stats
        update_query_stats(query_type, duration)
      end
      
      def get_query_stats
        @query_stats
      end
      
      def slow_queries(threshold_ms = 100)
        slow_queries = []
        
        @query_stats.each do |query_type, stats|
          if stats[:avg_duration_ms] > threshold_ms
            slow_queries << {
              query_type: query_type,
              avg_duration_ms: stats[:avg_duration_ms],
              count: stats[:count]
            }
          end
        end
        
        slow_queries
      end
      
      private
      
      def extract_query_type(query)
        case query.upcase
        when /^SELECT/
          "SELECT"
        when /^INSERT/
          "INSERT"
        when /^UPDATE/
          "UPDATE"
        when /^DELETE/
          "DELETE"
        else
          "OTHER"
        end
      end
      
      def update_query_stats(query_type, duration)
        @query_stats[query_type] ||= {
          count: 0,
          total_duration: 0,
          avg_duration_ms: 0
        }
        
        stats = @query_stats[query_type]
        stats[:count] += 1
        stats[:total_duration] += duration
        stats[:avg_duration_ms] = (stats[:total_duration] / stats[:count] * 1000).round(2)
      end
    end
    
    # Simulate database operations
    class SimulatedDatabase
      def initialize(logger)
        @logger = logger
      end
      
      def execute_query(query)
        start_time = Time.now
        
        # Simulate query execution
        case query.upcase
        when /^SELECT/
          sleep(rand(0.01..0.05))
          result = [{ id: 1, name: "Test" }]
        when /^INSERT/
          sleep(rand(0.02..0.08))
          result = { affected_rows: 1 }
        when /^UPDATE/
          sleep(rand(0.03..0.1))
          result = { affected_rows: rand(1..5) }
        when /^DELETE/
          sleep(rand(0.02..0.06))
          result = { affected_rows: 1 }
        else
          sleep(rand(0.01..0.03))
          result = nil
        end
        
        end_time = Time.now
        duration = end_time - start_time
        
        @logger.log_query(query, duration, result)
        
        result
      end
    end
    
    # Test database query logging
    app_logger = ApplicationLogger.new("database-service")
    query_logger = DatabaseQueryLogger.new(app_logger)
    db = SimulatedDatabase.new(query_logger)
    
    # Execute various queries
    queries = [
      "SELECT * FROM users WHERE id = 1",
      "INSERT INTO users (name, email) VALUES ('John', 'john@example.com')",
      "UPDATE users SET name = 'Jane' WHERE id = 1",
      "DELETE FROM users WHERE id = 1",
      "SELECT COUNT(*) FROM orders",
      "SELECT * FROM products WHERE price > 100"
    ]
    
    queries.each { |query| db.execute_query(query) }
    
    # Show query statistics
    stats = query_logger.get_query_stats
    puts "\nQuery Statistics:"
    stats.each do |query_type, stat|
      puts "#{query_type}: #{stat[:count]} queries, avg #{stat[:avg_duration_ms]}ms"
    end
    
    # Show slow queries
    slow_queries = query_logger.slow_queries(50)
    puts "\nSlow Queries (>50ms):"
    slow_queries.each do |slow_query|
      puts "#{slow_query[:query_type]}: avg #{slow_query[:avg_duration_ms]}ms"
    end
  end
  
  def self.request_logging
    puts "\nRequest Logging:"
    puts "=" * 50
    
    class RequestLogger
      def initialize(logger)
        @logger = logger
      end
      
      def log_request(request_id, method, path, headers = {})
        @logger.log_event("request_started", {
          request_id: request_id,
          method: method,
          path: path,
          headers: headers,
          timestamp: Time.now.iso8601
        })
      end
      
      def log_response(request_id, status, response_time, response_size = nil)
        @logger.log_event("request_completed", {
          request_id: request_id,
          status: status,
          response_time_ms: (response_time * 1000).round(2),
          response_size: response_size,
          timestamp: Time.now.iso8601
        })
      end
      
      def log_error(request_id, error, context = {})
        @logger.log_error(error, context.merge(
          request_id: request_id,
          event: "request_error"
        ))
      end
      
      def log_performance_metrics(request_id, metrics)
        @logger.log_event("performance_metrics", metrics.merge(
          request_id: request_id
        ))
      end
    end
    
    # Simulate web request processing
    class WebApplication
      def initialize(logger)
        @logger = RequestLogger.new(logger)
      end
      
      def handle_request(request_id, method, path, headers = {})
        @logger.log_request(request_id, method, path, headers)
        
        start_time = Time.now
        
        begin
          # Simulate request processing
          sleep(rand(0.05..0.2))
          
          # Simulate different responses
          case path
          when '/health'
            status = 200
            response = { status: "healthy" }
          when '/api/users'
            status = 200
            response = [{ id: 1, name: "John" }]
          when '/api/error'
            raise "Simulated error"
          else
            status = 404
            response = { error: "Not found" }
          end
          
          response_time = Time.now - start_time
          response_size = response.to_s.length
          
          @logger.log_response(request_id, status, response_time, response_size)
          
          # Log additional performance metrics
          @logger.log_performance_metrics(request_id, {
            memory_usage: rand(50..200),
            cpu_usage: rand(10..80),
            database_queries: rand(1..5)
          })
          
          { status: status, body: response }
          
        rescue => e
          response_time = Time.now - start_time
          @logger.log_error(request_id, e, {
            path: path,
            method: method,
            response_time: response_time
          })
          
          { status: 500, body: { error: "Internal server error" } }
        end
      end
    end
    
    # Test request logging
    app_logger = ApplicationLogger.new("web-app")
    app = WebApplication.new(app_logger)
    
    # Simulate requests
    requests = [
      { id: "req_1", method: "GET", path: "/health" },
      { id: "req_2", method: "GET", path: "/api/users" },
      { id: "req_3", method: "GET", path: "/api/error" },
      { id: "req_4", method: "GET", path: "/nonexistent" }
    ]
    
    requests.each do |request|
      response = app.handle_request(
        request[:id],
        request[:method],
        request[:path],
        { "User-Agent" => "TestClient/1.0" }
      )
      
      puts "Request #{request[:id]}: #{response[:status]}"
    end
  end
  
  # Run performance logging examples
  method_timing
  database_query_logging
  request_logging
end
```

### 2. Security Logging

Logging for security monitoring:

```ruby
class SecurityLogging
  def self.authentication_logging
    puts "Authentication Logging:"
    puts "=" * 50
    
    class SecurityLogger
      def initialize(logger)
        @logger = logger
      end
      
      def log_login_attempt(user_id, email, ip_address, success, failure_reason = nil)
        log_entry = {
          event: "login_attempt",
          user_id: user_id,
          email: email,
          ip_address: ip_address,
          success: success,
          timestamp: Time.now.iso8601
        }
        
        log_entry[:failure_reason] = failure_reason unless success
        
        if success
          @logger.log_event("login_success", log_entry)
        else
          @logger.log_event("login_failure", log_entry)
        end
      end
      
      def log_password_change(user_id, ip_address, success)
        @logger.log_event("password_change", {
          user_id: user_id,
          ip_address: ip_address,
          success: success,
          timestamp: Time.now.iso8601
        })
      end
      
      def log_suspicious_activity(user_id, activity_type, details)
        @logger.log_event("suspicious_activity", {
          user_id: user_id,
          activity_type: activity_type,
          details: details,
          timestamp: Time.now.iso8601,
          severity: "high"
        })
      end
      
      def log_permission_denied(user_id, resource, action, ip_address)
        @logger.log_event("permission_denied", {
          user_id: user_id,
          resource: resource,
          action: action,
          ip_address: ip_address,
          timestamp: Time.now.iso8601,
          severity: "medium"
        })
      end
    end
    
    # Simulate authentication events
    app_logger = ApplicationLogger.new("auth-service")
    sec_logger = SecurityLogger.new(app_logger)
    
    # Login attempts
    sec_logger.log_login_attempt(123, "john@example.com", "192.168.1.1", true)
    sec_logger.log_login_attempt(124, "jane@example.com", "192.168.1.2", false, "invalid_password")
    sec_logger.log_login_attempt(125, "admin@example.com", "192.168.1.3", false, "account_locked")
    
    # Password changes
    sec_logger.log_password_change(123, "192.168.1.1", true)
    sec_logger.log_password_change(124, "192.168.1.2", false)
    
    # Suspicious activities
    sec_logger.log_suspicious_activity(124, "multiple_failed_logins", {
      attempts: 5,
      time_window: "5 minutes",
      ip_addresses: ["192.168.1.2", "192.168.1.5"]
    })
    
    # Permission denied
    sec_logger.log_permission_denied(124, "admin_panel", "delete", "192.168.1.2")
  end
  
  def self.audit_logging
    puts "\nAudit Logging:"
    puts "=" * 50
    
    class AuditLogger
      def initialize(logger)
        @logger = logger
      end
      
      def log_data_access(user_id, resource, action, data_id = nil)
        @logger.log_event("data_access", {
          user_id: user_id,
          resource: resource,
          action: action,
          data_id: data_id,
          timestamp: Time.now.iso8601,
          audit_type: "data_access"
        })
      end
      
      def log_configuration_change(user_id, component, setting, old_value, new_value)
        @logger.log_event("configuration_change", {
          user_id: user_id,
          component: component,
          setting: setting,
          old_value: old_value,
          new_value: new_value,
          timestamp: Time.now.iso8601,
          audit_type: "configuration"
        })
      end
      
      def log_admin_action(user_id, action, target, details = {})
        @logger.log_event("admin_action", {
          user_id: user_id,
          action: action,
          target: target,
          details: details,
          timestamp: Time.now.iso8601,
          audit_type: "admin"
        })
      end
      
      def log_system_event(event_type, details = {})
        @logger.log_event("system_event", {
          event_type: event_type,
          details: details,
          timestamp: Time.now.iso8601,
          audit_type: "system"
        })
      end
    end
    
    # Simulate audit events
    app_logger = ApplicationLogger.new("audit-service")
    audit_logger = AuditLogger.new(app_logger)
    
    # Data access
    audit_logger.log_data_access(123, "user_profile", "read", 456)
    audit_logger.log_data_access(123, "user_profile", "update", 456)
    audit_logger.log_data_access(124, "financial_records", "read", 789)
    
    # Configuration changes
    audit_logger.log_configuration_change(1, "database", "max_connections", 100, 150)
    audit_logger.log_configuration_change(1, "security", "session_timeout", 3600, 7200)
    
    # Admin actions
    audit_logger.log_admin_action(1, "user_deletion", "user_456", {
      reason: "policy_violation",
      deleted_by: "admin"
    })
    
    audit_logger.log_admin_action(1, "role_change", "user_789", {
      old_role: "user",
      new_role: "admin",
      changed_by: "super_admin"
    })
    
    # System events
    audit_logger.log_system_event("backup_completed", {
      backup_size: "2.5GB",
      duration: "15 minutes",
      backup_type: "incremental"
    })
    
    audit_logger.log_system_event("security_scan", {
      scan_type: "vulnerability",
      vulnerabilities_found: 3,
      scan_duration: "5 minutes"
    })
  end
  
  def self.compliance_logging
    puts "\nCompliance Logging:"
    puts "=" * 50
    
    class ComplianceLogger
      def initialize(logger)
        @logger = logger
      end
      
      def log_gdpr_event(event_type, user_id, data_type, action, legal_basis = nil)
        @logger.log_event("gdpr_event", {
          event_type: event_type,
          user_id: user_id,
          data_type: data_type,
          action: action,
          legal_basis: legal_basis,
          timestamp: Time.now.iso8601,
          regulation: "GDPR"
        })
      end
      
      def log_pci_event(event_type, transaction_id, amount, payment_method)
        @logger.log_event("pci_event", {
          event_type: event_type,
          transaction_id: transaction_id,
          amount: amount,
          currency: "USD",
          payment_method: payment_method,
          timestamp: Time.now.iso8601,
          regulation: "PCI_DSS"
        })
      end
      
      def log_hipaa_event(event_type, patient_id, data_type, access_reason)
        @logger.log_event("hipaa_event", {
          event_type: event_type,
          patient_id: patient_id,
          data_type: data_type,
          access_reason: access_reason,
          timestamp: Time.now.iso8601,
          regulation: "HIPAA"
        })
      end
      
      def log_retention_event(data_type, record_id, action, retention_period)
        @logger.log_event("retention_event", {
          data_type: data_type,
          record_id: record_id,
          action: action,
          retention_period: retention_period,
          timestamp: Time.now.iso8601,
          compliance_type: "data_retention"
        })
      end
    end
    
    # Simulate compliance events
    app_logger = ApplicationLogger.new("compliance-service")
    compliance_logger = ComplianceLogger.new(app_logger)
    
    # GDPR events
    compliance_logger.log_gdpr_event("data_processing", 123, "personal_data", "profile_update", "consent")
    compliance_logger.log_gdpr_event("data_deletion", 124, "user_profile", "account_deletion", "right_to_erasure")
    compliance_logger.log_gdpr_event("data_export", 125, "user_data", "data_portability", "user_request")
    
    # PCI events
    compliance_logger.log_pci_event("payment_processed", "txn_12345", 99.99, "credit_card")
    compliance_logger.log_pci_event("refund_processed", "txn_12346", 49.99, "credit_card")
    compliance_logger.log_pci_event("payment_failed", "txn_12347", 199.99, "debit_card")
    
    # HIPAA events
    compliance_logger.log_hipaa_event("patient_record_access", "patient_789", "medical_record", "treatment")
    compliance_logger.log_hipaa_event("prescription_issued", "patient_789", "prescription", "medication_management")
    
    # Retention events
    compliance_logger.log_retention_event("user_data", "user_123", "archived", "7_years")
    compliance_logger.log_retention_event("financial_records", "order_456", "deleted", "10_years")
  end
  
  # Run security logging examples
  authentication_logging
  audit_logging
  compliance_logging
end
```

## 🎯 Logging Best Practices

### 1. Logging Guidelines

```ruby
class LoggingGuidelines
  def self.best_practices
    puts "Logging Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Use structured logging",
        description: "Log in a structured format (JSON) for easy parsing",
        example: "Include context, timestamps, and metadata"
      },
      {
        practice: "Log at appropriate levels",
        description: "Use DEBUG, INFO, WARN, ERROR, FATAL appropriately",
        example: "DEBUG for detailed info, ERROR for failures"
      },
      {
        practice: "Include relevant context",
        description: "Add user ID, request ID, IP address, etc.",
        example: "Context helps with debugging and monitoring"
      },
      {
        practice: "Avoid sensitive data",
        description: "Don't log passwords, tokens, or PII",
        example: "Mask or hash sensitive information"
      },
      {
        practice: "Use correlation IDs",
        description: "Track requests across services",
        example: "Include request ID in all log entries"
      },
      {
        practice: "Log performance metrics",
        description: "Include timing and resource usage",
        example: "Query time, memory usage, CPU usage"
      },
      {
        practice: "Log security events",
        description: "Record authentication, authorization, and access",
        example: "Login attempts, permission changes, data access"
      },
      {
        practice: "Handle logging errors",
        description: "Don't let logging failures crash your app",
        example: "Use try-catch around logging operations"
      }
    ]
    
    practices.each do |practice|
      puts "#{practice[:practice]}:"
      puts "  #{practice[:description]}"
      puts "  Example: #{practice[:example]}"
      puts
    end
  end
  
  def self.logging_anti_patterns
    puts "\nLogging Anti-Patterns:"
    puts "=" * 50
    
    anti_patterns = [
      {
        pattern: "Logging in production loops",
        problem: "Can cause performance issues and fill disk space",
        solution: "Use appropriate log levels and rotation"
      },
      {
        pattern: "Logging sensitive information",
        problem: "Security risk and privacy violation",
        solution: "Mask or exclude sensitive data"
      },
      {
        pattern: "Using puts instead of proper logging",
        problem: "No structure, levels, or context",
        solution: "Use a proper logging framework"
      },
      {
        pattern: "Excessive DEBUG logging in production",
        problem: "Performance impact and storage costs",
        solution: "Use environment-specific log levels"
      },
      {
        pattern: "Not logging errors with context",
        problem: "Difficult to debug and troubleshoot",
        solution: "Always include relevant context in error logs"
      },
      {
        pattern: "Logging without aggregation",
        problem: "Difficult to analyze and monitor",
        solution: "Use log aggregation and analysis tools"
      }
    ]
    
    anti_patterns.each do |anti_pattern|
      puts "#{anti_pattern[:pattern]}:"
      puts "  Problem: #{anti_pattern[:problem]}"
      puts "  Solution: #{anti_pattern[:solution]}"
      puts
    end
  end
  
  def self.logging_checklist
    puts "\nLogging Checklist:"
    puts "=" * 50
    
    checklist = [
      "□ Are you using structured logging format?",
      "□ Are you logging at appropriate levels?",
      "□ Are you including relevant context in logs?",
      "□ Are you avoiding sensitive data in logs?",
      "□ Are you using correlation IDs for request tracking?",
      "□ Are you logging performance metrics?",
      "□ Are you logging security events?",
      "□ Are you handling logging errors gracefully?",
      "□ Are you using log rotation?",
      "□ Are you monitoring log volume and costs?",
      "□ Are you aggregating and analyzing logs?",
      "□ Are you alerting on critical log events?"
    ]
    
    checklist.each { |item| puts item }
  end
end

# Run best practices examples
LoggingGuidelines.best_practices
LoggingGuidelines.logging_anti_patterns
LoggingGuidelines.logging_checklist
```

### 2. Log Rotation and Management

Managing log files and storage:

```ruby
class LogManagement
  def self.log_rotation
    puts "Log Rotation:"
    puts "=" * 50
    
    require 'logger'
    
    class RotatingLogger
      def initialize(filename, max_size = 1024 * 1024, keep_files = 5)
        @filename = filename
        @max_size = max_size
        @keep_files = keep_files
        @current_size = 0
        @logger = Logger.new(filename)
        @logger.formatter = proc { |severity, datetime, progname, msg| "#{msg}\n" }
      end
      
      def info(message)
        check_rotation
        @logger.info(message)
        @current_size += message.length + 1
      end
      
      def error(message)
        check_rotation
        @logger.error(message)
        @current_size += message.length + 1
      end
      
      private
      
      def check_rotation
        if @current_size > @max_size
          rotate_logs
        end
      end
      
      def rotate_logs
        # Move existing files
        (@keep_files - 1).downto(1) do |i|
          old_file = "#{@filename}.#{i}"
          new_file = "#{@filename}.#{i + 1}"
          
          if File.exist?(old_file)
            File.rename(old_file, new_file)
          end
        end
        
        # Move current file
        if File.exist?(@filename)
          File.rename(@filename, "#{@filename}.1")
        end
        
        # Reset current size
        @current_size = 0
        
        puts "Rotated log file: #{@filename}"
      end
    end
    
    # Test log rotation
    logger = RotatingLogger.new('test.log', 100, 3)
    
    # Generate log entries to trigger rotation
    50.times do |i|
      message = "Log entry #{i} - " + "x" * 20
      logger.info(message)
    end
    
    # Check rotated files
    puts "\nLog files after rotation:"
    Dir.glob('test.log*').each do |file|
      size = File.size(file)
      puts "#{file}: #{size} bytes"
    end
    
    # Cleanup
    Dir.glob('test.log*').each { |file| File.delete(file) }
  end
  
  def self.log_filtering
    puts "\nLog Filtering:"
    puts "=" * 50
    
    class FilteredLogger
      def initialize(base_logger, filters = {})
        @base_logger = base_logger
        @filters = filters
      end
      
      def debug(message, context = {})
        log_if_allowed(:debug, message, context)
      end
      
      def info(message, context = {})
        log_if_allowed(:info, message, context)
      end
      
      def warn(message, context = {})
        log_if_allowed(:warn, message, context)
      end
      
      def error(message, context = {})
        log_if_allowed(:error, message, context)
      end
      
      private
      
      def log_if_allowed(level, message, context)
        return unless should_log?(level, message, context)
        
        case level
        when :debug
          @base_logger.debug(format_message(message, context))
        when :info
          @base_logger.info(format_message(message, context))
        when :warn
          @base_logger.warn(format_message(message, context))
        when :error
          @base_logger.error(format_message(message, context))
        end
      end
      
      def should_log?(level, message, context)
        # Check level filter
        if @filters[:level]
          return false unless level_matches?(level, @filters[:level])
        end
        
        # Check message filter
        if @filters[:exclude_messages]
          return false if @filters[:exclude_messages].any? { |pattern| message.include?(pattern) }
        end
        
        # Check context filter
        if @filters[:include_context]
          return false unless context_matches?(context, @filters[:include_context])
        end
        
        true
      end
      
      def level_matches?(level, filter_level)
        levels = { debug: 0, info: 1, warn: 2, error: 3 }
        levels[level] >= levels[filter_level]
      end
      
      def context_matches?(context, required_context)
        required_context.all? { |key, value| context[key] == value }
      end
      
      def format_message(message, context)
        if context.empty?
          message
        else
          "#{message} | #{context}"
        end
      end
    end
    
    # Test log filtering
    require 'logger'
    base_logger = Logger.new(STDOUT)
    
    # Filter out debug messages and sensitive content
    filtered_logger = FilteredLogger.new(base_logger, {
      level: :info,
      exclude_messages: ["password", "secret"],
      include_context: { environment: "production" }
    })
    
    # Test filtering
    filtered_logger.debug("Debug message - should be filtered")
    filtered_logger.info("Info message - should be logged")
    filtered_logger.info("User password: secret123 - should be filtered")
    filtered_logger.info("Production message", { environment: "production" })
    filtered_logger.info("Development message", { environment: "development" })
  end
  
  def self.log_aggregation_setup
    puts "\nLog Aggregation Setup:"
    puts "=" * 50
    
    class LogShipper
      def initialize(log_file, destination_url)
        @log_file = log_file
        @destination_url = destination_url
        @buffer = []
        @buffer_size = 100
        @flush_interval = 30
        @running = false
      end
      
      def start
        @running = true
        @thread = Thread.new { shipping_loop }
        puts "Log shipper started"
      end
      
      def stop
        @running = false
        @thread&.join
        flush_buffer
        puts "Log shipper stopped"
      end
      
      def ship_log_entry(entry)
        @buffer << entry
        
        if @buffer.length >= @buffer_size
          flush_buffer
        end
      end
      
      private
      
      def shipping_loop
        last_flush = Time.now
        
        while @running
          if Time.now - last_flush > @flush_interval
            flush_buffer
            last_flush = Time.now
          end
          
          sleep(1)
        end
      end
      
      def flush_buffer
        return if @buffer.empty?
        
        # Simulate shipping to external service
        puts "Shipping #{@buffer.length} log entries to #{@destination_url}"
        
        # In real implementation, this would make HTTP request
        # response = HTTP.post(@destination_url, json: @buffer)
        
        @buffer.clear
      end
    end
    
    class LogCollector
      def initialize
        @logs = []
        @mutex = Mutex.new
      end
      
      def receive_log_batch(log_batch)
        @mutex.synchronize do
          @logs.concat(log_batch)
          
          # Keep only last 10000 logs
          @logs.shift if @logs.length > 10000
        end
      end
      
      def search_logs(query)
        @mutex.synchronize do
          @logs.select do |log|
            log[:message].include?(query) || 
            log[:context].values.any? { |v| v.to_s.include?(query) }
          end
        end
      end
      
      def get_log_stats
        @mutex.synchronize do
          {
            total_logs: @logs.length,
            levels: @logs.group_by { |log| log[:level] }.transform_values(&:count),
            time_range: {
              earliest: @logs.first&.dig(:timestamp),
              latest: @logs.last&.dig(:timestamp)
            }
          }
        end
      end
    end
    
    # Test log aggregation
    collector = LogCollector.new
    shipper = LogShipper.new('application.log', 'https://log-aggregator.example.com/logs')
    
    shipper.start
    
    # Simulate log shipping
    10.times do |i|
      log_entry = {
        timestamp: Time.now.iso8601,
        level: ['INFO', 'WARN', 'ERROR'].sample,
        message: "Log message #{i}",
        context: { service: "web-app", instance: i % 3 }
      }
      
      shipper.ship_log_entry(log_entry)
    end
    
    # Simulate collector receiving logs
    batch = [
      { timestamp: Time.now.iso8601, level: 'INFO', message: 'Test 1', context: {} },
      { timestamp: Time.now.iso8601, level: 'ERROR', message: 'Test 2', context: {} }
    ]
    
    collector.receive_log_batch(batch)
    
    # Search logs
    results = collector.search_logs('Test')
    puts "Search results: #{results.length} logs found"
    
    # Get stats
    stats = collector.get_log_stats
    puts "Log stats: #{stats}"
    
    shipper.stop
  end
  
  # Run log management examples
  log_rotation
  log_filtering
  log_aggregation_setup
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Logging**: Use Ruby's Logger class
2. **Log Levels**: Implement different log levels
3. **Contextual Logging**: Add context to log messages

### Intermediate Exercises

1. **Structured Logging**: Implement JSON logging
2. **Performance Logging**: Log performance metrics
3. **Security Logging**: Log security events

### Advanced Exercises

1. **Log Aggregation**: Build log aggregation system
2. **Log Rotation**: Implement log rotation
3. **Compliance Logging**: Implement compliance-specific logging

---

## 🎯 Summary

Logging strategies in Ruby provide:

- **Basic Logging** - Ruby's built-in Logger and levels
- **Structured Logging** - JSON format and context
- **Performance Logging** - Timing and metrics
- **Security Logging** - Authentication and audit trails
- **Log Management** - Rotation, filtering, and aggregation

Master these strategies to build observable and maintainable Ruby applications!
