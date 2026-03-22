# Error Handling in Ruby

## Overview

Error handling is crucial for building robust applications. Ruby provides a comprehensive exception handling system that allows you to gracefully handle errors and unexpected situations.

## Exception Hierarchy

Ruby has a well-defined exception hierarchy:

```
Exception
├── NoMemoryError
├── ScriptError
│   ├── LoadError
│   ├── NotImplementedError
│   └── SyntaxError
├── SecurityError
├── SignalException
├── SystemExit
├── SystemStackError
└── StandardError
    ├── ArgumentError
    ├── IOError
    │   └── EOFError
    ├── IndexError
    │   └── StopIteration
    ├── LocalJumpError
    ├── NameError
    │   └── NoMethodError
    ├── RangeError
    │   ├── FloatDomainError
    │   └── RegexpError
    ├── RegexpError
    ├── RuntimeError
    ├── StandardError (base for most exceptions)
    ├── TypeError
    └── ZeroDivisionError
```

## Basic Exception Handling

### Begin-Rescue Block

The basic structure for handling exceptions:

```ruby
begin
  # Code that might raise an exception
  risky_operation
rescue StandardError => e
  # Handle the exception
  puts "Error occurred: #{e.message}"
  puts "Error type: #{e.class}"
end
```

### Simple Example

```ruby
def divide(a, b)
  begin
    result = a / b
    puts "Result: #{result}"
    result
  rescue ZeroDivisionError => e
    puts "Cannot divide by zero!"
    puts "Error: #{e.message}"
    nil
  end
end

divide(10, 2)   # => Result: 5
divide(10, 0)   # => Cannot divide by zero!
```

## Multiple Exception Types

### Handling Different Exceptions

```ruby
def process_file(filename)
  begin
    file = File.open(filename, 'r')
    content = file.read
    puts "File content: #{content[0..50]}..."
    content
  rescue Errno::ENOENT
    puts "Error: File '#{filename}' not found"
    nil
  rescue Errno::EACCES
    puts "Error: Permission denied for file '#{filename}'"
    nil
  rescue IOError => e
    puts "I/O Error: #{e.message}"
    nil
  rescue StandardError => e
    puts "Unexpected error: #{e.message}"
    nil
  ensure
    file.close if file && !file.closed?
    puts "File handle closed"
  end
end

process_file("existing_file.txt")
process_file("nonexistent_file.txt")
```

### Compact Multiple Rescue Syntax

```ruby
def handle_multiple_errors
  begin
    # Some risky operation
    raise ArgumentError, "Invalid argument"
  rescue ArgumentError, TypeError => e
    puts "Argument or Type Error: #{e.message}"
  rescue StandardError => e
    puts "Other Error: #{e.message}"
  end
end

handle_multiple_errors
```

## The Else Clause

The `else` clause executes when no exception occurs:

```ruby
def safe_divide(a, b)
  begin
    result = a / b
  rescue ZeroDivisionError
    puts "Cannot divide by zero"
    nil
  else
    puts "Division successful: #{result}"
    result
  ensure
    puts "Operation completed"
  end
end

safe_divide(10, 2)  # Division successful: 5
safe_divide(10, 0)  # Cannot divide by zero
```

## The Ensure Clause

The `ensure` clause always executes, whether an exception occurred or not:

```ruby
def database_operation
  connection = nil
  begin
    connection = DatabaseConnection.new
    connection.connect
    result = connection.query("SELECT * FROM users")
    puts "Query executed successfully"
    result
  rescue DatabaseError => e
    puts "Database error: #{e.message}"
    nil
  ensure
    connection&.disconnect
    puts "Database connection closed"
  end
end

database_operation
```

## Raising Exceptions

### Basic Raise

```ruby
def validate_age(age)
  if age < 0
    raise "Age cannot be negative"
  elsif age > 150
    raise "Age seems unrealistic"
  end
  puts "Age is valid: #{age}"
end

begin
  validate_age(-5)
rescue => e
  puts "Validation error: #{e.message}"
end

begin
  validate_age(200)
rescue => e
  puts "Validation error: #{e.message}"
end
```

### Raising Specific Exception Types

```ruby
class InvalidEmailError < StandardError; end
class InvalidAgeError < StandardError; end

def validate_user(email, age)
  raise InvalidEmailError, "Invalid email format" unless email.include?("@")
  raise InvalidAgeError, "Age must be between 0 and 150" unless (0..150).include?(age)
  
  puts "User is valid"
end

begin
  validate_user("invalid-email", 25)
rescue InvalidEmailError => e
  puts "Email error: #{e.message}"
rescue InvalidAgeError => e
  puts "Age error: #{e.message}"
end
```

### Raising with Custom Exception Classes

```ruby
class PaymentError < StandardError
  attr_reader :amount, :payment_method
  
  def initialize(message, amount: nil, payment_method: nil)
    super(message)
    @amount = amount
    @payment_method = payment_method
  end
end

class InsufficientFundsError < PaymentError; end
class PaymentDeclinedError < PaymentError; end

def process_payment(amount, payment_method, balance)
  raise InsufficientFundsError.new("Insufficient funds", amount: amount, payment_method: payment_method) if amount > balance
  raise PaymentDeclinedError.new("Payment declined", amount: amount, payment_method: payment_method) if payment_method == "expired_card"
  
  puts "Payment of $#{amount} processed via #{payment_method}"
end

begin
  process_payment(1000, "credit_card", 500)
rescue InsufficientFundsError => e
  puts "Cannot process $#{e.amount}: #{e.message}"
rescue PaymentDeclinedError => e
  puts "Payment declined: #{e.message}"
end
```

## Retry Mechanism

### Basic Retry

```ruby
def connect_to_server(max_attempts = 3)
  attempts = 0
  
  begin
    attempts += 1
    puts "Attempt #{attempts} to connect..."
    
    # Simulate connection failure
    raise "Connection failed" if attempts < 3
    
    puts "Connected successfully!"
    true
  rescue => e
    if attempts < max_attempts
      puts "Retrying... (#{e.message})"
      sleep(1)
      retry
    else
      puts "Max attempts reached. Giving up."
      false
    end
  end
end

connect_to_server
```

### Retry with Exponential Backoff

```ruby
def api_call_with_retry(max_retries = 3)
  retries = 0
  
  begin
    retries += 1
    puts "API call attempt #{retries}"
    
    # Simulate API failure
    raise "API unavailable" if retries < 3
    
    puts "API call successful"
    "API response data"
  rescue => e
    if retries < max_retries
      wait_time = 2 ** retries
      puts "API failed, retrying in #{wait_time} seconds..."
      sleep(wait_time)
      retry
    else
      puts "API call failed after #{max_retries} attempts"
      raise
    end
  end
end

begin
  result = api_call_with_retry
  puts "Result: #{result}"
rescue => e
  puts "Final error: #{e.message}"
end
```

## Custom Exception Classes

### Creating Custom Exceptions

```ruby
module MyApp
  class Error < StandardError; end
  
  class ValidationError < Error
    attr_reader :field, :value
    
    def initialize(message, field: nil, value: nil)
      super(message)
      @field = field
      @value = value
    end
  end
  
  class AuthenticationError < Error; end
  class AuthorizationError < Error; end
  class DatabaseError < Error
    attr_reader :query
    
    def initialize(message, query: nil)
      super(message)
      @query = query
    end
  end
end

class UserService
  def authenticate(username, password)
    raise MyApp::ValidationError.new("Username cannot be empty", field: :username) if username.nil? || username.empty?
    raise MyApp::ValidationError.new("Password cannot be empty", field: :password) if password.nil? || password.empty?
    
    # Simulate authentication
    if username == "admin" && password == "secret"
      puts "Authentication successful"
    else
      raise MyApp::AuthenticationError, "Invalid credentials"
    end
  end
  
  def authorize(user, resource)
    raise MyApp::AuthorizationError, "User not logged in" unless user
    raise MyApp::AuthorizationError, "Insufficient permissions for #{resource}" unless user.admin?
    
    puts "Access granted to #{resource}"
  end
end

service = UserService.new

begin
  service.authenticate("", "password")
rescue MyApp::ValidationError => e
  puts "Validation failed for #{e.field}: #{e.message}"
end

begin
  service.authenticate("user", "wrong")
rescue MyApp::AuthenticationError => e
  puts "Authentication failed: #{e.message}"
end
```

## Exception Handling Best Practices

### 1. Be Specific with Exception Types

```ruby
# Good
begin
  process_payment(amount)
rescue InsufficientFundsError => e
  handle_insufficient_funds(e)
rescue PaymentDeclinedError => e
  handle_payment_declined(e)
end

# Avoid this
begin
  process_payment(amount)
rescue => e
  # Too generic
end
```

### 2. Handle Exceptions at the Right Level

```ruby
# Good - Handle at business logic level
class OrderProcessor
  def process_order(order)
    begin
      payment_service.charge(order.total)
      inventory_service.reserve_items(order.items)
      shipping_service.schedule(order)
      "Order processed successfully"
    rescue PaymentError => e
      "Payment failed: #{e.message}"
    rescue InventoryError => e
      "Inventory issue: #{e.message}"
    end
  end
end

# Avoid handling at too low a level
```

### 3. Provide Meaningful Error Messages

```ruby
# Good
raise ValidationError.new("Email format is invalid", field: :email, value: email)

# Less helpful
raise "Invalid email"
```

### 4. Use Custom Exceptions for Domain Logic

```ruby
# Good
class InsufficientInventoryError < StandardError
  attr_reader :product_id, :requested, :available
  
  def initialize(product_id, requested, available)
    super("Insufficient inventory for product #{product_id}")
    @product_id = product_id
    @requested = requested
    @available = available
  end
end

# Generic
raise "Not enough items"
```

## Practical Examples

### Example 1: File Processing with Error Handling

```ruby
class FileProcessor
  def initialize(logger)
    @logger = logger
  end
  
  def process_files(file_paths)
    results = []
    
    file_paths.each do |file_path|
      begin
        result = process_single_file(file_path)
        results << { file: file_path, status: :success, data: result }
      rescue Errno::ENOENT
        @logger.error("File not found: #{file_path}")
        results << { file: file_path, status: :error, error: "File not found" }
      rescue Errno::EACCES
        @logger.error("Permission denied: #{file_path}")
        results << { file: file_path, status: :error, error: "Permission denied" }
      rescue => e
        @logger.error("Unexpected error processing #{file_path}: #{e.message}")
        results << { file: file_path, status: :error, error: e.message }
      end
    end
    
    results
  end
  
  private
  
  def process_single_file(file_path)
    file = nil
    begin
      file = File.open(file_path, 'r')
      content = file.read
      parse_content(content)
    ensure
      file&.close
    end
  end
  
  def parse_content(content)
    # Simulate parsing
    { lines: content.lines.count, characters: content.length }
  end
end

# Usage
class SimpleLogger
  def error(message)
    puts "ERROR: #{message}"
  end
end

processor = FileProcessor.new(SimpleLogger.new)
files = ["existing.txt", "missing.txt", "protected.txt"]
results = processor.process_files(files)
results.each { |r| puts "#{r[:file]}: #{r[:status]}" }
```

### Example 2: Network Service with Retry Logic

```ruby
class NetworkService
  def initialize(base_url, max_retries: 3, timeout: 30)
    @base_url = base_url
    @max_retries = max_retries
    @timeout = timeout
  end
  
  def make_request(endpoint, params = {})
    url = "#{@base_url}/#{endpoint}"
    retries = 0
    
    begin
      retries += 1
      puts "Making request to #{url} (attempt #{retries})"
      
      response = http_get(url, params)
      
      if response.success?
        parse_response(response.body)
      else
        raise NetworkError, "HTTP #{response.code}: #{response.message}"
      end
      
    rescue NetworkError, TimeoutError => e
      if retries < @max_retries
        wait_time = calculate_backoff(retries)
        puts "Request failed, retrying in #{wait_time} seconds: #{e.message}"
        sleep(wait_time)
        retry
      else
        raise ServiceUnavailableError, "Service unavailable after #{@max_retries} attempts: #{e.message}"
      end
    end
  end
  
  private
  
  def http_get(url, params)
    # Simulate HTTP request
    if url.include?("fail")
      raise NetworkError, "Connection refused"
    end
    
    if url.include?("timeout")
      raise TimeoutError, "Request timed out"
    end
    
    OpenStruct.new(success?: true, code: 200, body: '{"data": "success"}')
  end
  
  def parse_response(body)
    JSON.parse(body)
  rescue JSON::ParserError => e
    raise InvalidResponseError, "Invalid JSON response: #{e.message}"
  end
  
  def calculate_backoff(attempt)
    # Exponential backoff with jitter
    base = 2 ** attempt
    jitter = rand(0.1..0.5)
    base * jitter
  end
end

class NetworkError < StandardError; end
class TimeoutError < StandardError; end
class ServiceUnavailableError < StandardError; end
class InvalidResponseError < StandardError; end

# Usage
service = NetworkService.new("https://api.example.com")

begin
  result = service.make_request("success")
  puts "Request successful: #{result}"
rescue ServiceUnavailableError => e
  puts "Service error: #{e.message}"
rescue => e
  puts "Unexpected error: #{e.message}"
end
```

### Example 3: Validation Framework with Detailed Errors

```ruby
class ValidationErrors
  def initialize
    @errors = {}
  end
  
  def add(field, message, value = nil)
    @errors[field] ||= []
    @errors[field] << { message: message, value: value }
  end
  
  def any?
    @errors.any?
  end
  
  def empty?
    @errors.empty?
  end
  
  def messages
    @errors.flat_map { |field, errors| errors.map { |e| "#{field}: #{e[:message]}" } }
  end
  
  def to_h
    @errors
  end
end

class UserValidator
  def initialize(params)
    @params = params
    @errors = ValidationErrors.new
  end
  
  def validate!
    validate_name
    validate_email
    validate_age
    validate_password
    
    raise ValidationError, @errors if @errors.any?
    
    true
  end
  
  private
  
  def validate_name
    name = @params[:name]
    
    if name.nil? || name.to_s.strip.empty?
      @errors.add(:name, "cannot be blank")
    elsif name.length < 2
      @errors.add(:name, "must be at least 2 characters", name)
    elsif name.length > 50
      @errors.add(:name, "must be less than 50 characters", name)
    end
  end
  
  def validate_email
    email = @params[:email]
    
    if email.nil? || email.to_s.strip.empty?
      @errors.add(:email, "cannot be blank")
    elsif !email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
      @errors.add(:email, "is not a valid email format", email)
    end
  end
  
  def validate_age
    age = @params[:age]
    
    if age.nil?
      @errors.add(:age, "cannot be blank")
    elsif !age.is_a?(Integer)
      @errors.add(:age, "must be a number", age)
    elsif age < 18
      @errors.add(:age, "must be at least 18", age)
    elsif age > 120
      @errors.add(:age, "must be less than 120", age)
    end
  end
  
  def validate_password
    password = @params[:password]
    
    if password.nil? || password.to_s.strip.empty?
      @errors.add(:password, "cannot be blank")
    elsif password.length < 8
      @errors.add(:password, "must be at least 8 characters")
    elsif !password.match?(/[A-Z]/)
      @errors.add(:password, "must contain at least one uppercase letter")
    elsif !password.match?(/[a-z]/)
      @errors.add(:password, "must contain at least one lowercase letter")
    elsif !password.match?(/\d/)
      @errors.add(:password, "must contain at least one digit")
    end
  end
end

class ValidationError < StandardError
  attr_reader :errors
  
  def initialize(errors)
    super("Validation failed")
    @errors = errors
  end
end

# Usage
begin
  validator = UserValidator.new({
    name: "A",
    email: "invalid-email",
    age: 15,
    password: "weak"
  })
  validator.validate!
rescue ValidationError => e
  puts "Validation failed:"
  e.errors.messages.each { |msg| puts "  - #{msg}" }
end
```

## Performance Considerations

### Exception Handling Overhead

```ruby
# Exception handling has performance overhead
# Use it for exceptional cases, not normal flow control

# Bad - Using exceptions for flow control
def find_user_by_id(user_id)
  begin
    user = User.find(user_id)
    user
  rescue ActiveRecord::RecordNotFound
    nil
  end
end

# Good - Using normal flow control
def find_user_by_id(user_id)
  User.find_by(id: user_id)
end
```

## Practice Exercises

### Exercise 1: Calculator with Error Handling
Create a calculator that:
- Handles division by zero
- Validates input types
- Provides meaningful error messages
- Uses custom exception classes

### Exercise 2: File Processor with Validation
Build a file processor that:
- Validates file existence and permissions
- Handles different file formats
- Provides detailed error reporting
- Uses retry mechanism for temporary failures

### Exercise 3: API Client with Robust Error Handling
Implement an API client that:
- Handles network timeouts
- Implements retry logic with backoff
- Provides specific error types
- Logs errors appropriately

### Exercise 4: Form Validation Framework
Create a validation framework that:
- Collects multiple validation errors
- Provides detailed error messages
- Uses custom exception classes
- Supports field-specific validation

---

**Ready to learn about file I/O in Ruby? Let's continue! 📁**
