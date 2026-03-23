# Error Handling in Ruby
# Comprehensive guide to robust error handling and exception management

## 🎯 Overview

Effective error handling is crucial for building robust Ruby applications. This guide covers exception hierarchies, error handling patterns, and strategies for creating resilient applications.

## 🚨 Ruby Exception Hierarchy

### 1. Understanding Exception Classes

Ruby's built-in exception hierarchy:

```ruby
class ExceptionHierarchy
  def self.explore_exception_hierarchy
    puts "Ruby Exception Hierarchy:"
    puts "=" * 50
    
    # Show exception hierarchy
    puts "Exception"
    puts "├── NoMemoryError"
    puts "├── ScriptError"
    puts "│   ├── LoadError"
    puts "│   ├── NotImplementedError"
    puts "│   └── SyntaxError"
    puts "├── SecurityError"
    puts "├── SignalException"
    puts "│   ├── Interrupt"
    puts "├── StandardError"
    puts "│   ├── ArgumentError"
    puts "│   ├── IOError"
    puts "│   │   └── EOFError"
    puts "│   ├── IndexError"
    puts "│   ├── LocalJumpError"
    puts "│   ├── NameError"
    puts "│   ├── RangeError"
    puts "│   ├── RegexpError"
    puts "│   ├── RuntimeError"
    puts "│   ├── SystemCallError"
    puts "│   ├── ThreadError"
    puts "│   ├── TypeError"
    puts "│   └── ZeroDivisionError"
    puts "├── SystemExit"
    puts "└── fatal"
    
    puts "\nNote: You should rescue StandardError, not Exception"
    puts "Rescuing Exception can hide serious errors like SystemExit"
  end
  
  def self.demonstrate_common_exceptions
    puts "\nCommon Ruby Exceptions:"
    puts "=" * 50
    
    exceptions = [
      {
        type: "ArgumentError",
        code: -> { Integer("not_a_number") },
        description: "Invalid arguments to method"
      },
      {
        type: "TypeError",
        code: -> { "hello" + 123 },
        description: "Wrong type for operation"
      },
      {
        type: "NoMethodError",
        code: -> { "hello".nonexistent_method },
        description: "Method doesn't exist"
      },
      {
        type: "NameError",
        code: -> { undefined_variable },
        description: "Undefined variable or method"
      },
      {
        type: "ZeroDivisionError",
        code: -> { 10 / 0 },
        description: "Division by zero"
      },
      {
        type: "IndexError",
        code: -> { [1, 2, 3][10] },
        description: "Index out of range"
      },
      {
        type: "RangeError",
        code: -> { 1.step(100, 0.1).to_a },
        description: "Value out of range"
      }
    ]
    
    exceptions.each do |exception|
      puts "#{exception[:type]}:"
      puts "  Description: #{exception[:description]}"
      puts "  Code: #{exception[:code].source_location.first}"
      
      begin
        exception[:code].call
      rescue => e
        puts "  Error: #{e.message}"
      end
      puts
    end
  end
  
  def self.exception_inspection
    puts "\nException Inspection:"
    puts "=" * 50
    
    begin
      # Create an exception
      raise StandardError, "Test exception"
    rescue => e
      puts "Exception object inspection:"
      puts "  Class: #{e.class}"
      puts "  Message: #{e.message}"
      puts "  Backtrace: #{e.backtrace.first(3)}"
      puts "  Backtrace locations:"
      e.backtrace_locations.first(3).each_with_index do |loc, i|
        puts "    #{i + 1}: #{loc}"
      end
      
      # Exception hierarchy
      puts "\nException hierarchy:"
      current_class = e.class
      while current_class
        puts "  #{current_class}"
        current_class = current_class.superclass
      end
    end
  end
end

# Run exception hierarchy examples
ExceptionHierarchy.explore_exception_hierarchy
ExceptionHierarchy.demonstrate_common_exceptions
ExceptionHierarchy.exception_inspection
```

### 2. Custom Exception Classes

Creating meaningful custom exceptions:

```ruby
class CustomExceptions
  # Base custom exception
  class ApplicationError < StandardError
    attr_reader :code, :context, :timestamp
    
    def initialize(message, code = nil, context = {})
      super(message)
      @code = code
      @context = context
      @timestamp = Time.now
    end
    
    def to_s
      "#{self.class.name}: #{message} (#{code}) at #{timestamp}"
    end
    
    def to_h
      {
        error: self.class.name,
        message: message,
        code: code,
        context: context,
        timestamp: timestamp,
        backtrace: backtrace&.first(5)
      }
    end
  end
  
  # Domain-specific exceptions
  class ValidationError < ApplicationError
    def initialize(field, value, rule)
      message = "Validation failed for #{field}: #{value} (#{rule})"
      code = "VALIDATION_ERROR"
      context = { field: field, value: value, rule: rule }
      super(message, code, context)
    end
  end
  
  class BusinessError < ApplicationError
    def initialize(operation, reason, details = {})
      message = "Business error in #{operation}: #{reason}"
      code = "BUSINESS_ERROR"
      context = { operation: operation, reason: reason }.merge(details)
      super(message, code, context)
    end
  end
  
  class DataError < ApplicationError
    def initialize(operation, data_type, details = {})
      message = "Data error in #{operation}: #{data_type}"
      code = "DATA_ERROR"
      context = { operation: operation, data_type: data_type }.merge(details)
      super(message, code, context)
    end
  end
  
  class ConfigurationError < ApplicationError
    def initialize(setting, value, expected_type)
      message = "Configuration error: #{setting} must be #{expected_type}, got #{value.class}"
      code = "CONFIGURATION_ERROR"
      context = { setting: setting, value: value, expected_type: expected_type }
      super(message, code, context)
    end
  end
  
  def self.demonstrate_custom_exceptions
    puts "Custom Exception Examples:"
    puts "=" * 50
    
    # Validation error
    begin
      raise ValidationError.new("age", -5, "must be positive")
    rescue ValidationError => e
      puts "Validation Error:"
      puts "  #{e.to_s}"
      puts "  Context: #{e.context}"
      puts "  Hash: #{e.to_h}"
    end
    
    # Business error
    begin
      raise BusinessError.new("order_creation", "insufficient_stock", { product: "Widget", requested: 10, available: 5 })
    rescue BusinessError => e
      puts "\nBusiness Error:"
      puts "  #{e.to_s}"
      puts "  Context: #{e.context}"
    end
    
    # Data error
    begin
      raise DataError.new("user_save", "duplicate_email", { email: "test@example.com" })
    rescue DataError => e
      puts "\nData Error:"
      puts "  #{e.to_s}"
      puts "  Context: #{e.context}"
    end
    
    # Configuration error
    begin
      raise ConfigurationError.new("database_timeout", "slow", "Integer")
    rescue ConfigurationError => e
      puts "\nConfiguration Error:"
      puts "  #{e.to_s}"
      puts "  Context: #{e.context}"
    end
  end
  
  def self.exception_with_causes
    puts "\nException with Causes:"
    puts "=" * 50
    
    def inner_operation
      raise ArgumentError, "Invalid argument: negative number"
    end
    
    def outer_operation
      begin
        inner_operation
      rescue => e
        raise BusinessError.new("calculation", "inner operation failed", { original_error: e.message })
      end
    end
    
    begin
      outer_operation
    rescue => e
      puts "Outer exception: #{e.message}"
      puts "Exception cause: #{e.cause&.message}"
      puts "Full backtrace:"
      puts e.full_message
    end
  end
  
  # Run custom exception examples
  demonstrate_custom_exceptions
  exception_with_causes
end
```

## 🛡️ Error Handling Patterns

### 1. Basic Error Handling

Fundamental error handling techniques:

```ruby
class BasicErrorHandling
  def self.simple_rescue
    puts "Simple Rescue Patterns:"
    puts "=" * 50
    
    def safe_divide(a, b)
      begin
        a / b
      rescue ZeroDivisionError
        puts "Cannot divide by zero"
        0
      end
    end
    
    def safe_array_access(array, index)
      begin
        array[index]
      rescue IndexError
        puts "Index #{index} out of range"
        nil
      end
    end
    
    def safe_string_to_int(str)
      begin
        Integer(str)
      rescue ArgumentError
        puts "Cannot convert '#{str}' to integer"
        0
      end
    end
    
    # Test basic rescues
    puts "Division: #{safe_divide(10, 2)}"
    puts "Division: #{safe_divide(10, 0)}"
    
    puts "Array access: #{safe_array_access([1, 2, 3], 1)}"
    puts "Array access: #{safe_array_access([1, 2, 3], 10)}"
    
    puts "String to int: #{safe_string_to_int("123")}"
    puts "String to int: #{safe_string_to_int("abc")}"
  end
  
  def self.multiple_rescue
    puts "\nMultiple Rescue Patterns:"
    puts "=" * 50
    
    def robust_operation(input)
      begin
        case input
        when Integer
          100 / input
        when String
          Integer(input)
        when Array
          input[100]
        else
          raise ArgumentError, "Unsupported input type: #{input.class}"
        end
      rescue ZeroDivisionError
        "Division by zero"
      rescue ArgumentError
        "Invalid argument"
      rescue IndexError
        "Index out of range"
      end
    end
    
    # Test multiple rescues
    test_inputs = [2, 0, "123", "abc", [1, 2, 3]]
    
    test_inputs.each do |input|
      result = robust_operation(input)
      puts "Input #{input.inspect}: #{result}"
    end
  end
  
  def self.else_and_ensure
    puts "\nElse and Ensure Patterns:"
    puts "=" * 50
    
    def file_operation(filename)
      file = nil
      begin
        file = File.open(filename, 'r')
        content = file.read
        content.upcase
      rescue Errno::ENOENT
        "File not found: #{filename}"
      else
        puts "File operation successful"
        content
      ensure
        file&.close
        puts "File closed"
      end
    end
    
    # Test ensure behavior
    puts "Testing with existing file:"
    result = file_operation(__FILE__)
    puts "Result: #{result.length} characters"
    
    puts "\nTesting with non-existent file:"
    result = file_operation("non_existent_file.txt")
    puts "Result: #{result}"
  end
  
  def self.retry_patterns
    puts "\nRetry Patterns:"
    puts "=" * 50
    
    def retry_operation(max_retries = 3)
      attempts = 0
      
      begin
        attempts += 1
        puts "Attempt #{attempts}"
        
        # Simulate operation that might fail
        if attempts < 3
          raise "Temporary failure"
        end
        
        "Success after #{attempts} attempts"
        
      rescue => e
        if attempts < max_retries
          puts "Retrying... (#{max_retries - attempts} attempts left)"
          sleep(0.1)  # Wait before retry
          retry
        else
          puts "Max retries reached"
          raise e
        end
      end
    end
    
    # Test retry pattern
    begin
      result = retry_operation(3)
      puts "Final result: #{result}"
    rescue => e
      puts "Operation failed: #{e.message}"
    end
  end
  
  def self.rescue_modifiers
    puts "\nRescue Modifiers:"
    puts "=" * 50
    
    # Rescue with => to capture exception
    def capture_exception
      begin
        raise "Test exception"
      rescue => e
        puts "Captured exception: #{e.class} - #{e.message}"
        puts "Exception object: #{e.inspect}"
      end
    end
    
    # Rescue with splat to capture multiple exceptions
    def rescue_multiple
      begin
        "hello" + 123
      rescue ArgumentError, TypeError => e
        puts "Caught #{e.class}: #{e.message}"
      end
    end
    
    # Rescue with inline rescue
    def inline_rescue
      "hello" + 123 rescue "Default value"
    end
    
    # Test rescue modifiers
    capture_exception
    rescue_multiple
    puts "Inline rescue result: #{inline_rescue}"
  end
  
  # Run basic error handling examples
  simple_rescue
  multiple_rescue
  else_and_ensure
  retry_patterns
  rescue_modifiers
end
```

### 2. Advanced Error Handling

Sophisticated error handling patterns:

```ruby
class AdvancedErrorHandling
  def self.contextual_error_handling
    puts "Contextual Error Handling:"
    puts "=" * 50
    
    class ContextualError < StandardError
      attr_reader :context
      
      def initialize(message, context = {})
        super(message)
        @context = context
      end
    end
    
    def process_user_data(user_data)
      begin
        validate_user_data(user_data)
        transform_user_data(user_data)
      rescue => e
        # Add context to the error
        raise ContextualError.new(e.message, {
          operation: "process_user_data",
          user_id: user_data[:id],
          timestamp: Time.now,
          data_size: user_data.to_s.length
        })
      end
    end
    
    def validate_user_data(user_data)
      raise "Missing required field: name" unless user_data[:name]
      raise "Invalid email format" unless user_data[:email]&.include?("@")
    end
    
    def transform_user_data(user_data)
      user_data.transform_keys(&:to_sym)
    end
    
    # Test contextual error handling
    test_data = [
      { id: 1, name: "John", email: "john@example.com" },
      { id: 2, email: "invalid-email" },
      { id: 3, name: "Jane" }
    ]
    
    test_data.each do |data|
      begin
        result = process_user_data(data)
        puts "Success: #{data[:id]}"
      rescue ContextualError => e
        puts "Error for user #{data[:id]}:"
        puts "  Message: #{e.message}"
        puts "  Context: #{e.context}"
      end
    end
  end
  
  def self.error_aggregation
    puts "\nError Aggregation:"
    puts "=" * 50
    
    class ErrorCollector
      def initialize
        @errors = []
      end
      
      def add_error(error, context = {})
        @errors << {
          error: error,
          context: context,
          timestamp: Time.now
        }
      end
      
      def has_errors?
        @errors.any?
      end
      
      def errors
        @errors.dup
      end
      
      def summary
        {
          total_errors: @errors.length,
          error_types: @errors.map { |e| e[:error].class.name }.uniq,
          first_error: @errors.first
        }
      end
    end
    
    def batch_process(items)
      collector = ErrorCollector.new
      
      items.each_with_index do |item, index|
        begin
          process_item(item)
        rescue => e
          collector.add_error(e, { item_index: index, item: item })
        end
      end
      
      if collector.has_errors?
        raise "Batch processing failed with #{collector.errors.length} errors"
      end
      
      "All items processed successfully"
    end
    
    def process_item(item)
      case item
      when String
        raise "String too long" if item.length > 10
        item.upcase
      when Integer
        raise "Number too large" if item > 100
        item * 2
      else
        raise "Unsupported type: #{item.class}"
      end
    end
    
    # Test error aggregation
    test_items = ["short", "this is too long", 50, 150, :symbol]
    
    begin
      result = batch_process(test_items)
      puts "Result: #{result}"
    rescue => e
      puts "Batch processing failed: #{e.message}"
      # In real application, you'd access the error collector
    end
  end
  
  def self.fallback_strategies
    puts "\nFallback Strategies:"
    puts "=" * 50
    
    def primary_operation(data)
      # Primary operation that might fail
      if data[:simulate_failure]
        raise "Primary operation failed"
      end
      "Primary result: #{data[:value]}"
    end
    
    def fallback_operation(data)
      # Fallback operation
      "Fallback result: #{data[:value]}"
    end
    
    def ultimate_fallback(data)
      # Ultimate fallback
      "Default result"
    end
    
    def robust_operation(data)
      primary_operation(data)
    rescue => e
      puts "Primary failed: #{e.message}, trying fallback"
      begin
        fallback_operation(data)
      rescue => fallback_error
        puts "Fallback failed: #{fallback_error.message}, using ultimate fallback"
        ultimate_fallback(data)
      end
    end
    
    # Test fallback strategies
    test_cases = [
      { value: "test", simulate_failure: false },
      { value: "test", simulate_failure: true }
    ]
    
    test_cases.each do |test_case|
      result = robust_operation(test_case)
      puts "Result: #{result}"
    end
  end
  
  def self.circuit_breaker_pattern
    puts "\nCircuit Breaker Pattern:"
    puts "=" * 50
    
    class CircuitBreaker
      def initialize(failure_threshold: 5, timeout: 60)
        @failure_threshold = failure_threshold
        @timeout = timeout
        @failure_count = 0
        @last_failure_time = nil
        @state = :closed  # :closed, :open, :half_open
      end
      
      def call(&block)
        case @state
        when :closed
          execute_with_circuit(&block)
        when :open
          if Time.now - @last_failure_time > @timeout
            @state = :half_open
            execute_with_circuit(&block)
          else
            raise CircuitOpenError, "Circuit is open"
          end
        when :half_open
          execute_with_circuit(&block)
        end
      end
      
      private
      
      def execute_with_circuit(&block)
        begin
          result = yield
          reset_circuit if @state == :half_open
          result
        rescue => e
          record_failure
          raise e
        end
      end
      
      def record_failure
        @failure_count += 1
        @last_failure_time = Time.now
        
        if @failure_count >= @failure_threshold
          @state = :open
        end
      end
      
      def reset_circuit
        @failure_count = 0
        @state = :closed
      end
      
      def state
        @state
      end
    end
    
    class CircuitOpenError < StandardError; end
    
    # Test circuit breaker
    circuit_breaker = CircuitBreaker.new(failure_threshold: 3, timeout: 2)
    
    10.times do |i|
      begin
        result = circuit_breaker.call do
          if i < 5
            raise "Simulated failure"
          else
            "Success #{i}"
          end
        end
        puts "Attempt #{i + 1}: #{result}"
      rescue CircuitOpenError => e
        puts "Attempt #{i + 1}: #{e.message}"
      rescue => e
        puts "Attempt #{i + 1}: #{e.message}"
      end
      
      sleep(0.5) if i == 4  # Wait for circuit timeout
    end
  end
  
  # Run advanced error handling examples
  contextual_error_handling
  error_aggregation
  fallback_strategies
  circuit_breaker_pattern
end
```

## 🔧 Error Recovery Strategies

### 1. Graceful Degradation

Implementing graceful error recovery:

```ruby
class GracefulDegradation
  def self.feature_degradation
    puts "Feature Degradation:"
    puts "=" * 50
    
    class FeatureManager
      def initialize
        @features = {
          advanced_search: true,
          real_time_updates: true,
          file_upload: true,
          notifications: true
        }
      end
      
      def execute_feature(feature_name, &block)
        if @features[feature_name]
          begin
            result = yield
            puts "#{feature_name}: Success"
            result
          rescue => e
            puts "#{feature_name}: Failed - #{e.message}"
            degrade_feature(feature_name)
            fallback_result(feature_name)
          end
        else
          puts "#{feature_name}: Feature disabled"
          fallback_result(feature_name)
        end
      end
      
      private
      
      def degrade_feature(feature_name)
        @features[feature_name] = false
        puts "#{feature_name}: Feature degraded"
      end
      
      def fallback_result(feature_name)
        case feature_name
        when :advanced_search
          "Basic search results"
        when :real_time_updates
          "Cached data"
        when :file_upload
          "Upload failed"
        when :notifications
          "Notification disabled"
        else
          "Feature unavailable"
        end
      end
    end
    
    # Test feature degradation
    feature_manager = FeatureManager.new
    
    # Simulate feature usage
    features = [:advanced_search, :real_time_updates, :file_upload, :notifications]
    
    features.each do |feature|
      result = feature_manager.execute_feature(feature) do
        # Simulate random failure for demonstration
        if rand < 0.3
          raise "Simulated failure in #{feature}"
        end
        "#{feature} result"
      end
      puts "Result: #{result}"
    end
    
    # Try degraded features again
    puts "\nTrying degraded features:"
    features.each do |feature|
      result = feature_manager.execute_feature(feature) do
        "This shouldn't execute"
      end
      puts "Result: #{result}"
    end
  end
  
  def self.service_degradation
    puts "\nService Degradation:"
    puts "=" * 50
    
    class ServiceManager
      def initialize
        @services = {
          database: :healthy,
          cache: :healthy,
          email: :healthy,
          storage: :healthy
        }
      end
      
      def call_service(service_name, &block)
        case @services[service_name]
        when :healthy
          begin
            result = yield
            puts "#{service_name}: Service healthy"
            result
          rescue => e
            puts "#{service_name}: Service failed - #{e.message}"
            degrade_service(service_name)
            fallback_service(service_name)
          end
        when :degraded
          puts "#{service_name}: Service degraded"
          fallback_service(service_name)
        when :unavailable
          puts "#{service_name}: Service unavailable"
          raise ServiceUnavailableError, "#{service_name} is unavailable"
        end
      end
      
      private
      
      def degrade_service(service_name)
        @services[service_name] = :degraded
      end
      
      def fallback_service(service_name)
        case service_name
        when :database
          { status: "cached", data: "cached_data" }
        when :cache
          nil  # No cache fallback
        when :email
          puts "Email queued for later"
          { status: "queued" }
        when :storage
          { status: "local_storage" }
        end
      end
    end
    
    class ServiceUnavailableError < StandardError; end
    
    # Test service degradation
    service_manager = ServiceManager.new
    
    services = [:database, :cache, :email, :storage]
    
    services.each do |service|
      result = service_manager.call_service(service) do
        # Simulate service behavior
        if rand < 0.4
          raise "#{service} service error"
        end
        "#{service} result"
      end
      puts "Result: #{result}"
    end
  end
  
  def self.data_degradation
    puts "\nData Degradation:"
    puts "=" * 50
    
    def get_user_data(user_id, quality = :high)
      case quality
      when :high
        begin
          # Simulate high-quality data fetch
          if rand < 0.3
            raise "Database connection failed"
          end
          
          {
            id: user_id,
            name: "User #{user_id}",
            email: "user#{user_id}@example.com",
            profile: {
              age: rand(18..65),
              location: "City #{user_id}",
              preferences: Array.new(5) { "Pref #{rand(100)}" }
            },
            activity_history: Array.new(50) { "Activity #{rand(1000)}" }
          }
        rescue => e
          puts "High-quality data failed: #{e.message}"
          get_user_data(user_id, :medium)
        end
        
      when :medium
        begin
          # Medium-quality data
          if rand < 0.2
            raise "Cache service failed"
          end
          
          {
            id: user_id,
            name: "User #{user_id}",
            email: "user#{user_id}@example.com",
            profile: {
              age: rand(18..65),
              location: "City #{user_id}"
            }
          }
        rescue => e
          puts "Medium-quality data failed: #{e.message}"
          get_user_data(user_id, :low)
        end
        
      when :low
        # Low-quality data (always available)
        {
          id: user_id,
          name: "User #{user_id}",
          email: "user#{user_id}@example.com"
        }
      end
    end
    
    # Test data degradation
    user_ids = [1, 2, 3, 4, 5]
    
    user_ids.each do |user_id|
      puts "\nFetching data for user #{user_id}:"
      data = get_user_data(user_id)
      
      puts "Data keys: #{data.keys}"
      puts "Data size: #{data.to_s.length} characters"
    end
  end
  
  # Run graceful degradation examples
  feature_degradation
  service_degradation
  data_degradation
end
```

### 2. Error Recovery Mechanisms

Implementing automatic error recovery:

```ruby
class ErrorRecoveryMechanisms
  def self.exponential_backoff
    puts "Exponential Backoff:"
    puts "=" * 50
    
    def retry_with_backoff(max_retries = 5, base_delay = 1)
      attempts = 0
      
      begin
        attempts += 1
        puts "Attempt #{attempts}"
        
        # Simulate operation
        if attempts < 4
          raise "Temporary failure"
        end
        
        "Success after #{attempts} attempts"
        
      rescue => e
        if attempts < max_retries
          delay = base_delay * (2 ** (attempts - 1))
          puts "Waiting #{delay}s before retry..."
          sleep(0.1)  # Simulate wait (in real code, use actual sleep)
          retry
        else
          raise e
        end
      end
    end
    
    # Test exponential backoff
    begin
      result = retry_with_backoff(5, 1)
      puts "Result: #{result}"
    rescue => e
      puts "Failed after all retries: #{e.message}"
    end
  end
  
  def self.health_check_recovery
    puts "\nHealth Check Recovery:"
    puts "=" * 50
    
    class ServiceWithHealthCheck
      def initialize
        @health = :healthy
        @failure_count = 0
      end
      
      def operation
        check_health
        perform_operation
      end
      
      private
      
      def check_health
        case @health
        when :healthy
          # Random health check failure
          if rand < 0.3
            @failure_count += 1
            @health = :unhealthy if @failure_count > 2
            raise "Health check failed"
          end
          
        when :unhealthy
          # Attempt recovery
          if rand < 0.5
            puts "Attempting recovery..."
            @health = :healthy
            @failure_count = 0
            puts "Recovery successful"
          else
            raise "Service unhealthy"
          end
        end
      end
      
      def perform_operation
        "Operation completed successfully"
      end
    end
    
    # Test health check recovery
    service = ServiceWithHealthCheck.new
    
    10.times do |i|
      begin
        result = service.operation
        puts "Attempt #{i + 1}: #{result}"
      rescue => e
        puts "Attempt #{i + 1}: #{e.message}"
      end
      
      sleep(0.1)
    end
  end
  
  def self.state_based_recovery
    puts "\nState-Based Recovery:"
    puts "=" * 50
    
    class StatefulService
      def initialize
        @state = :initializing
        @retry_count = 0
      end
      
      def process_request
        case @state
        when :initializing
          initialize_service
          process_request
          
        when :ready
          execute_request
          
        when :recovering
          attempt_recovery
          process_request
          
        when :failed
          raise "Service failed permanently"
        end
      end
      
      private
      
      def initialize_service
        puts "Initializing service..."
        sleep(0.1)  # Simulate initialization
        
        if rand < 0.3
          @state = :failed
          raise "Initialization failed"
        else
          @state = :ready
          puts "Service ready"
        end
      end
      
      def execute_request
        if rand < 0.2
          @state = :recovering
          @retry_count = 0
          raise "Request failed"
        end
        
        "Request processed successfully"
      end
      
      def attempt_recovery
        @retry_count += 1
        puts "Recovery attempt #{@retry_count}"
        
        if @retry_count > 3
          @state = :failed
          raise "Recovery failed"
        end
        
        if rand < 0.6
          @state = :ready
          @retry_count = 0
          puts "Recovery successful"
        else
          sleep(0.1)
        end
      end
    end
    
    # Test state-based recovery
    service = StatefulService.new
    
    10.times do |i|
      begin
        result = service.process_request
        puts "Request #{i + 1}: #{result}"
      rescue => e
        puts "Request #{i + 1}: #{e.message}"
      end
      
      sleep(0.1)
    end
  end
  
  def self.circuit_breaker_recovery
    puts "\nCircuit Breaker Recovery:"
    puts "=" * 50
    
    class RecoverableCircuitBreaker
      def initialize(failure_threshold: 3, recovery_timeout: 2)
        @failure_threshold = failure_threshold
        @recovery_timeout = recovery_timeout
        @failure_count = 0
        @last_failure_time = nil
        @state = :closed
      end
      
      def call(&block)
        case @state
        when :closed
          execute_closed(&block)
        when :open
          execute_open(&block)
        when :half_open
          execute_half_open(&block)
        end
      end
      
      private
      
      def execute_closed(&block)
        begin
          result = yield
          reset_on_success
          result
        rescue => e
          record_failure
          raise e
        end
      end
      
      def execute_open(&block)
        if Time.now - @last_failure_time > @recovery_timeout
          @state = :half_open
          puts "Circuit half-open - testing recovery"
          execute_half_open(&block)
        else
          raise CircuitOpenError, "Circuit open - waiting for recovery"
        end
      end
      
      def execute_half_open(&block)
        begin
          result = yield
          puts "Recovery successful - closing circuit"
          reset_on_success
          result
        rescue => e
          puts "Recovery failed - opening circuit"
          @state = :open
          @last_failure_time = Time.now
          raise e
        end
      end
      
      def record_failure
        @failure_count += 1
        @last_failure_time = Time.now
        
        if @failure_count >= @failure_threshold
          @state = :open
          puts "Circuit opened due to failures"
        end
      end
      
      def reset_on_success
        @failure_count = 0
        @state = :closed
      end
    end
    
    class CircuitOpenError < StandardError; end
    
    # Test circuit breaker recovery
    circuit_breaker = RecoverableCircuitBreaker.new(failure_threshold: 3, recovery_timeout: 1)
    
    15.times do |i|
      begin
        result = circuit_breaker.call do
          if i < 8
            raise "Service failure"
          else
            "Service success #{i}"
          end
        end
        puts "Request #{i + 1}: #{result}"
      rescue CircuitOpenError => e
        puts "Request #{i + 1}: #{e.message}"
      rescue => e
        puts "Request #{i + 1}: #{e.message}"
      end
      
      sleep(0.3)
    end
  end
  
  # Run error recovery examples
  exponential_backoff
  health_check_recovery
  state_based_recovery
  circuit_breaker_recovery
end
```

## 🎯 Error Handling Best Practices

### 1. Error Handling Guidelines

```ruby
class ErrorHandlingGuidelines
  def self.best_practices
    puts "Error Handling Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Be specific with exceptions",
        description: "Catch specific exceptions, not generic ones",
        example: "rescue ZeroDivisionError instead of rescue"
      },
      {
        practice: "Don't swallow exceptions",
        description: "Always handle or re-raise exceptions",
        example: "Log the error before re-raising"
      },
      {
        practice: "Use meaningful error messages",
        description: "Provide context in error messages",
        example: "Include relevant data in error messages"
      },
      {
        practice: "Create custom exceptions",
        description: "Define domain-specific exceptions",
        example: "class ValidationError < StandardError"
      },
      {
        practice: "Use ensure for cleanup",
        description: "Always clean up resources",
        example: "Close files, database connections"
      },
      {
        practice: "Log errors appropriately",
        description: "Log errors with sufficient context",
        example: "Include timestamps, user info, request data"
      },
      {
        practice: "Handle errors at the right level",
        description: "Handle errors where they can be meaningfully handled",
        example: "Don't handle errors too low or too high"
      },
      {
        practice: "Provide fallback behavior",
        description: "Offer alternatives when operations fail",
        example: "Use cached data when database fails"
      }
    ]
    
    practices.each do |practice|
      puts "#{practice[:practice]}:"
      puts "  #{practice[:description]}"
      puts "  Example: #{practice[:example]}"
      puts
    end
  end
  
  def self.anti_patterns
    puts "\nError Handling Anti-Patterns:"
    puts "=" * 50
    
    anti_patterns = [
      {
        pattern: "Rescue Exception",
        problem: "Catches system-level exceptions",
        solution: "Rescue StandardError instead"
      },
      {
        pattern: "Empty rescue blocks",
        problem: "Silently ignores all errors",
        solution: "Always handle or log errors"
      },
      {
        pattern: "Rescue without specific exception",
        problem: "Catches unrelated exceptions",
        solution: "Be specific about what you rescue"
      },
      {
        pattern: "Using exceptions for flow control",
        problem: "Performance impact and unclear logic",
        solution: "Use proper control flow constructs"
      },
      {
        pattern: "Not cleaning up in ensure",
        problem: "Resource leaks",
        solution: "Always use ensure for cleanup"
      },
      {
        pattern: "Generic error messages",
        problem: "Hard to debug and understand",
        solution: "Provide specific, actionable error messages"
      }
    ]
    
    anti_patterns.each do |anti_pattern|
      puts "#{anti_pattern[:pattern]}:"
      puts "  Problem: #{anti_pattern[:problem]}"
      puts "  Solution: #{anti_pattern[:solution]}"
      puts
    end
  end
  
  def self.error_handling_checklist
    puts "\nError Handling Checklist:"
    puts "=" * 50
    
    checklist = [
      "□ Are you rescuing specific exceptions?",
      "□ Are you providing meaningful error messages?",
      "□ Are you logging errors with sufficient context?",
      "□ Are you cleaning up resources in ensure blocks?",
      "□ Are you handling errors at the appropriate level?",
      "□ Are you providing fallback behavior when appropriate?",
      "□ Are you using custom exceptions for domain errors?",
      "□ Are you avoiding exception swallowing?",
      "□ Are you testing error conditions?",
      "□ Are you documenting error handling behavior?"
    ]
    
    checklist.each { |item| puts item }
  end
end

# Run best practices examples
ErrorHandlingGuidelines.best_practices
ErrorHandlingGuidelines.anti_patterns
ErrorHandlingGuidelines.error_handling_checklist
```

## 🎓 Exercises

### Beginner Exercises

1. **Exception Hierarchy**: Understand Ruby's exception classes
2. **Basic Rescue**: Implement simple error handling
3. **Custom Exceptions**: Create domain-specific exceptions

### Intermediate Exercises

1. **Advanced Patterns**: Implement sophisticated error handling
2. **Graceful Degradation**: Build fallback mechanisms
3. **Error Recovery**: Implement recovery strategies

### Advanced Exercises

1. **Circuit Breaker**: Build circuit breaker pattern
2. **Error Aggregation**: Collect and analyze multiple errors
3. **Production Error Handling**: Safe error handling in production

---

## 🎯 Summary

Error handling in Ruby provides:

- **Exception Hierarchy** - Understanding Ruby's exception classes
- **Handling Patterns** - Basic and advanced error handling techniques
- **Recovery Strategies** - Graceful degradation and recovery mechanisms
- **Best Practices** - Guidelines for robust error handling
- **Production Patterns** - Safe error handling in production

Master these techniques to build resilient and reliable Ruby applications!
