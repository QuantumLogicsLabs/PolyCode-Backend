# Error Handling Examples
# Demonstrating exception handling, custom exceptions, and best practices in Ruby

puts "=== BASIC EXCEPTION HANDLING ==="

def divide(a, b)
  begin
    result = a / b
    puts "Result: #{result}"
    result
  rescue ZeroDivisionError => e
    puts "Cannot divide by zero!"
    puts "Error: #{e.message}"
    puts "Error class: #{e.class}"
    nil
  end
end

puts "Dividing 10 by 2:"
divide(10, 2)

puts "\nDividing 10 by 0:"
divide(10, 0)

puts "\n=== MULTIPLE EXCEPTION TYPES ==="

def process_file(filename)
  file = nil
  begin
    file = File.open(filename, 'r')
    content = file.read
    puts "File content (first 50 chars): #{content[0..49]}..."
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
    if file && !file.closed?
      file.close
      puts "File handle closed"
    end
  end
end

puts "Processing existing file:"
process_file(__FILE__)

puts "\nProcessing non-existent file:"
process_file("nonexistent_file.txt")

puts "\n=== ELSE AND ENSURE CLAUSES ==="

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

puts "Safe divide 10/2:"
safe_divide(10, 2)

puts "\nSafe divide 10/0:"
safe_divide(10, 0)

puts "\n=== RAISING EXCEPTIONS ==="

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

puts "\n=== SPECIFIC EXCEPTION TYPES ==="

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

puts "\n=== CUSTOM EXCEPTION CLASSES ==="

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

puts "\n=== RETRY MECHANISM ==="

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
      sleep(0.5)
      retry
    else
      puts "Max attempts reached. Giving up."
      false
    end
  end
end

puts "Connecting to server:"
connect_to_server

puts "\n=== RETRY WITH EXPONENTIAL BACKOFF ==="

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
      sleep(0.1) # Shortened for demo
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

puts "\n=== DOMAIN-SPECIFIC EXCEPTIONS ==="

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
    raise MyApp::AuthorizationError, "Insufficient permissions for #{resource}" unless user == "admin"
    
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

puts "\n=== VALIDATION FRAMEWORK ==="

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
    age: 15
  })
  validator.validate!
rescue ValidationError => e
  puts "Validation failed:"
  e.errors.messages.each { |msg| puts "  - #{msg}" }
end

puts "\n=== FILE PROCESSING WITH ERROR HANDLING ==="

class FileProcessor
  def process_files(file_paths)
    results = []
    
    file_paths.each do |file_path|
      begin
        result = process_single_file(file_path)
        results << { file: file_path, status: :success, data: result }
      rescue Errno::ENOENT
        puts "ERROR: File not found: #{file_path}"
        results << { file: file_path, status: :error, error: "File not found" }
      rescue Errno::EACCES
        puts "ERROR: Permission denied: #{file_path}"
        results << { file: file_path, status: :error, error: "Permission denied" }
      rescue => e
        puts "ERROR: Unexpected error processing #{file_path}: #{e.message}"
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
      { lines: content.lines.count, characters: content.length }
    ensure
      file&.close
    end
  end
end

# Create test files for demonstration
File.write("test_file1.txt", "Line 1\nLine 2\nLine 3")
File.write("test_file2.txt", "Short content")

processor = FileProcessor.new
files = ["test_file1.txt", "test_file2.txt", "nonexistent.txt"]
results = processor.process_files(files)

puts "\nProcessing results:"
results.each do |r|
  if r[:status] == :success
    puts "#{r[:file]}: #{r[:data][:lines]} lines, #{r[:data][:characters]} characters"
  else
    puts "#{r[:file]}: ERROR - #{r[:error]}"
  end
end

# Clean up test files
File.delete("test_file1.txt") if File.exist?("test_file1.txt")
File.delete("test_file2.txt") if File.exist?("test_file2.txt")

puts "\n=== NESTED EXCEPTION HANDLING ==="

def outer_operation
  begin
    inner_operation
  rescue => e
    puts "Outer rescue caught: #{e.message}"
    puts "Original exception: #{e.cause.class}" if e.cause
  end
end

def inner_operation
  begin
    raise "Inner error"
  rescue => e
    puts "Inner rescue caught: #{e.message}"
    raise StandardError, "Wrapped error", e.cause
  end
end

puts "Nested exception handling:"
outer_operation

puts "\n=== EXCEPTION OBJECT INSPECTION ==="

def demonstrate_exception_object
  begin
    raise ArgumentError, "Invalid argument provided", ["arg1", "arg2"]
  rescue ArgumentError => e
    puts "Exception class: #{e.class}"
    puts "Exception message: #{e.message}"
    puts "Backtrace:"
    e.backtrace.first(3).each { |line| puts "  #{line}" }
    puts "Exception inspect: #{e.inspect}"
  end
end

puts "Exception object inspection:"
demonstrate_exception_object
