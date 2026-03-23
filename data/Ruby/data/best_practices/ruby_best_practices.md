# Ruby Best Practices Guide

## Overview

This guide covers comprehensive best practices for Ruby development, including code style, architecture patterns, security practices, performance optimization, and maintainability guidelines.

## Code Style and Conventions

### Naming Conventions

```ruby
# ✅ GOOD: Clear, descriptive names
class UserAuthenticationService
  def initialize(user_repository, token_generator)
    @user_repository = user_repository
    @token_generator = token_generator
  end
  
  def authenticate_user(email, password)
    user = @user_repository.find_by_email(email)
    return false unless user
    
    user.password_matches?(password)
  end
  
  def generate_authentication_token(user_id)
    @token_generator.create_token(user_id, expires_in: 24.hours)
  end
end

# ❌ BAD: Unclear or abbreviated names
class UAS
  def initialize(ur, tg)
    @ur = ur
    @tg = tg
  end
  
  def auth_user(em, pw)
    u = @ur.find_by_email(em)
    return false unless u
    
    u.pw_match?(pw)
  end
  
  def gen_auth_token(uid)
    @tg.create_token(uid, exp: 24.hours)
  end
end

# ✅ GOOD: Method names that clearly express intent
class OrderProcessor
  def calculate_total_price(order)
    subtotal = calculate_subtotal(order)
    tax = calculate_tax(subtotal)
    shipping = calculate_shipping(order)
    
    subtotal + tax + shipping
  end
  
  def apply_discount_rules(order, customer)
    return order unless customer.loyalty_member?
    
    discounted_order = apply_loyalty_discount(order)
    discounted_order = apply_bulk_discount(discounted_order) if order.items.length > 10
    
    discounted_order
  end
  
  private
  
  def calculate_subtotal(order)
    order.items.sum { |item| item.price * item.quantity }
  end
  
  def calculate_tax(subtotal)
    subtotal * 0.08  # 8% tax rate
  end
  
  def calculate_shipping(order)
    order.weight > 10 ? 15.0 : 5.0
  end
  
  def apply_loyalty_discount(order)
    # Apply 10% loyalty discount
    discounted_items = order.items.map do |item|
      item.new(price: item.price * 0.9)
    end
    
    order.new(items: discounted_items)
  end
  
  def apply_bulk_discount(order)
    # Apply 5% bulk discount
    discounted_items = order.items.map do |item|
      item.new(price: item.price * 0.95)
    end
    
    order.new(items: discounted_items)
  end
end
```

### Constant and Variable Naming

```ruby
# ✅ GOOD: Constants use SCREAMING_SNAKE_CASE
module Configuration
  MAX_RETRY_ATTEMPTS = 3
  DEFAULT_TIMEOUT_SECONDS = 30
  API_BASE_URL = "https://api.example.com"
  SUPPORTED_FILE_FORMATS = %w[json xml csv].freeze
end

# ✅ GOOD: Variables use snake_case
def process_user_data(user_id, options = {})
  timeout = options[:timeout] || Configuration::DEFAULT_TIMEOUT_SECONDS
  max_attempts = options[:max_attempts] || Configuration::MAX_RETRY_ATTEMPTS
  
  current_attempt = 0
  user_data = nil
  
  while current_attempt < max_attempts
    begin
      user_data = fetch_user_data(user_id, timeout: timeout)
      break if user_data
    rescue NetworkError => e
      current_attempt += 1
      sleep(2 ** current_attempt)  # Exponential backoff
      raise e if current_attempt >= max_attempts
    end
  end
  
  user_data
end

# ❌ BAD: Inconsistent naming
module Config
  maxRetry = 3
  defaultTimeout = 30
  apiUrl = "https://api.example.com"
end

def processData(userId, opts = {})
  timeout = opts[:timeout] || Config.defaultTimeout
  maxAttempts = opts[:maxAttempts] || Config.maxRetry
  
  currentAttempt = 0
  userData = nil
  
  while currentAttempt < maxAttempts
    begin
      userData = fetchUserData(userId, timeout: timeout)
      break if userData
    rescue NetworkError => e
      currentAttempt += 1
      sleep(2 ** currentAttempt)
      raise e if currentAttempt >= maxAttempts
    end
  end
  
  userData
end
```

### Class and Module Structure

```ruby
# ✅ GOOD: Well-organized class with clear responsibilities
class InvoiceGenerator
  attr_reader :template_engine, :calculator, :formatter
  
  def initialize(template_engine:, calculator:, formatter:)
    @template_engine = template_engine
    @calculator = calculator
    @formatter = formatter
  end
  
  def generate_invoice(order, customer)
    invoice_data = build_invoice_data(order, customer)
    
    template = @template_engine.load_template(:invoice)
    rendered_template = @template_engine.render(template, invoice_data)
    
    @formatter.format_pdf(rendered_template)
  end
  
  private
  
  def build_invoice_data(order, customer)
    {
      invoice_number: generate_invoice_number,
      date: Date.current,
      customer: customer_data(customer),
      order: order_data(order),
      totals: calculate_totals(order)
    }
  end
  
  def generate_invoice_number
    "INV-#{Time.now.strftime('%Y%m%d')}-#{SecureRandom.hex(3).upcase}"
  end
  
  def customer_data(customer)
    {
      name: customer.full_name,
      email: customer.email,
      billing_address: format_address(customer.billing_address)
    }
  end
  
  def order_data(order)
    {
      items: order.items.map { |item| item_data(item) },
      order_date: order.created_at,
      order_number: order.order_number
    }
  end
  
  def item_data(item)
    {
      description: item.description,
      quantity: item.quantity,
      unit_price: @formatter.format_currency(item.unit_price),
      total_price: @formatter.format_currency(item.total_price)
    }
  end
  
  def calculate_totals(order)
    {
      subtotal: @formatter.format_currency(@calculator.subtotal(order)),
      tax: @formatter.format_currency(@calculator.tax(order)),
      shipping: @formatter.format_currency(@calculator.shipping(order)),
      total: @formatter.format_currency(@calculator.total(order))
    }
  end
  
  def format_address(address)
    "#{address.street}\n#{address.city}, #{address.state} #{address.zip_code}"
  end
end

# ✅ GOOD: Module with clear purpose and well-documented methods
module ValidationHelpers
  def self.validate_email(email)
    return false if email.nil? || email.strip.empty?
    
    # Basic email validation regex
    email_regex = /\A[^@\s]+@[^@\s]+\z/
    email.match?(email_regex)
  end
  
  def self.validate_phone_number(phone)
    return false if phone.nil?
    
    # Remove all non-digit characters
    digits = phone.gsub(/\D/, '')
    
    # Check if it's a valid phone number (10 digits for US)
    digits.length == 10
  end
  
  def self.validate_zip_code(zip_code)
    return false if zip_code.nil?
    
    # US zip code validation (5 digits or 5+4 digits)
    zip_code.match?(/\A\d{5}(-\d{4})?\z/)
  end
  
  def self.validate_credit_card(card_number)
    return false if card_number.nil?
    
    # Remove spaces and dashes
    digits = card_number.gsub(/[\s-]/, '')
    
    # Check if it's all digits and valid length
    return false unless digits.match?(/\A\d{13,19}\z/)
    
    # Luhn algorithm validation
    luhn_check(digits)
  end
  
  private
  
  def self.luhn_check(number)
    sum = 0
    double = false
    
    number.reverse.each_char do |digit|
      int_digit = digit.to_i
      
      if double
        int_digit *= 2
        int_digit -= 9 if int_digit > 9
      end
      
      sum += int_digit
      double = !double
    end
    
    sum % 10 == 0
  end
end
```

## Architecture Patterns

### Single Responsibility Principle

```ruby
# ✅ GOOD: Each class has a single responsibility
class UserRegistration
  def initialize(user_repository, email_service, password_validator)
    @user_repository = user_repository
    @email_service = email_service
    @password_validator = password_validator
  end
  
  def register(user_data)
    validate_user_data(user_data)
    
    user = create_user(user_data)
    saved_user = @user_repository.save(user)
    
    send_welcome_email(saved_user)
    
    saved_user
  end
  
  private
  
  def validate_user_data(user_data)
    raise ValidationError, "Email is required" if user_data[:email].nil?
    raise ValidationError, "Password is required" if user_data[:password].nil?
    raise ValidationError, "Invalid email format" unless ValidationHelpers.validate_email(user_data[:email])
    raise ValidationError, "Invalid password" unless @password_validator.valid?(user_data[:password])
  end
  
  def create_user(user_data)
    User.new(
      email: user_data[:email],
      password_hash: @password_validator.hash_password(user_data[:password]),
      created_at: Time.now
    )
  end
  
  def send_welcome_email(user)
    @email_service.send_welcome_email(user)
  end
end

class UserRepository
  def initialize(database)
    @database = database
  end
  
  def save(user)
    # Save user to database
    result = @database.exec_params(
      "INSERT INTO users (email, password_hash, created_at) VALUES ($1, $2, $3) RETURNING id",
      [user.email, user.password_hash, user.created_at]
    )
    
    user.id = result[0]['id']
    user
  end
  
  def find_by_email(email)
    # Find user by email
    result = @database.exec_params(
      "SELECT * FROM users WHERE email = $1",
      [email]
    )
    
    return nil if result.ntuples == 0
    
    row = result[0]
    User.new(
      id: row['id'],
      email: row['email'],
      password_hash: row['password_hash'],
      created_at: row['created_at']
    )
  end
end

class EmailService
  def initialize(smtp_client)
    @smtp_client = smtp_client
  end
  
  def send_welcome_email(user)
    template = load_email_template(:welcome)
    content = render_template(template, user: user)
    
    @smtp_client.send_email(
      to: user.email,
      subject: "Welcome to our service!",
      body: content
    )
  end
  
  private
  
  def load_email_template(template_name)
    # Load email template from file or database
    "Welcome {{user.name}}! Thank you for signing up."
  end
  
  def render_template(template, variables)
    # Simple template rendering
    content = template.dup
    variables.each do |key, value|
      content.gsub!("{{#{key}}}", value.to_s)
    end
    content
  end
end
```

### Dependency Injection

```ruby
# ✅ GOOD: Use dependency injection for testability and flexibility
class PaymentProcessor
  def initialize(gateway:, logger:, fraud_detector:)
    @gateway = gateway
    @logger = logger
    @fraud_detector = fraud_detector
  end
  
  def process_payment(payment_data)
    @logger.info("Processing payment for #{payment_data[:amount]}")
    
    # Fraud detection
    if @fraud_detector.suspicious?(payment_data)
      @logger.warn("Suspicious payment detected: #{payment_data}")
      raise FraudError, "Payment flagged as suspicious"
    end
    
    # Process payment
    begin
      result = @gateway.charge(
        amount: payment_data[:amount],
        token: payment_data[:payment_token],
        description: payment_data[:description]
      )
      
      @logger.info("Payment processed successfully: #{result[:transaction_id]}")
      result
      
    rescue GatewayError => e
      @logger.error("Payment failed: #{e.message}")
      raise PaymentError, "Payment processing failed"
    end
  end
end

# ❌ BAD: Hard-coded dependencies make testing difficult
class PaymentProcessorBad
  def process_payment(payment_data)
    # Hard-coded gateway
    gateway = StripeGateway.new(api_key: ENV['STRIPE_API_KEY'])
    
    # Hard-coded logger
    logger = Logger.new(STDOUT)
    
    # Hard-coded fraud detector
    fraud_detector = FraudDetector.new
    
    logger.info("Processing payment for #{payment_data[:amount]}")
    
    if fraud_detector.suspicious?(payment_data)
      logger.warn("Suspicious payment detected")
      raise FraudError, "Payment flagged as suspicious"
    end
    
    begin
      result = gateway.charge(
        amount: payment_data[:amount],
        token: payment_data[:payment_token],
        description: payment_data[:description]
      )
      
      logger.info("Payment processed successfully: #{result[:transaction_id]}")
      result
      
    rescue GatewayError => e
      logger.error("Payment failed: #{e.message}")
      raise PaymentError, "Payment processing failed"
    end
  end
end
```

### Factory Pattern

```ruby
# ✅ GOOD: Factory pattern for object creation
class ReportFactory
  def self.create_report(type, data, options = {})
    case type
    when :pdf
      PDFReport.new(data, options)
    when :excel
      ExcelReport.new(data, options)
    when :csv
      CSVReport.new(data, options)
    when :html
      HTMLReport.new(data, options)
    else
      raise ArgumentError, "Unknown report type: #{type}"
    end
  end
end

class PDFReport
  def initialize(data, options = {})
    @data = data
    @options = options
  end
  
  def generate
    # Generate PDF report
    "PDF report with #{@data.length} records"
  end
end

class ExcelReport
  def initialize(data, options = {})
    @data = data
    @options = options
  end
  
  def generate
    # Generate Excel report
    "Excel report with #{@data.length} records"
  end
end

# Usage
report = ReportFactory.create_report(:pdf, data, { title: "Monthly Report" })
output = report.generate
```

## Security Best Practices

### Input Validation

```ruby
# ✅ GOOD: Comprehensive input validation
class InputValidator
  def self.validate_user_input(input, rules)
    sanitized_input = sanitize_input(input)
    
    rules.each do |field, validations|
      value = sanitized_input[field]
      
      validations.each do |validation|
        case validation[:type]
        when :required
          raise ValidationError, "#{field} is required" if value.nil? || value.to_s.strip.empty?
          
        when :max_length
          raise ValidationError, "#{field} is too long (max #{validation[:max]} characters)" if value.to_s.length > validation[:max]
          
        when :min_length
          raise ValidationError, "#{field} is too short (min #{validation[:min]} characters)" if value.to_s.length < validation[:min]
          
        when :format
          raise ValidationError, "#{field} has invalid format" unless value.match?(Regexp.new(validation[:pattern]))
          
        when :allowed_values
          raise ValidationError, "#{field} must be one of: #{validation[:values].join(', ')}" unless validation[:values].include?(value)
          
        when :numeric
          raise ValidationError, "#{field} must be numeric" unless value.to_s.match?(/\A-?\d+(\.\d+)?\z/)
          
        when :email
          raise ValidationError, "#{field} must be a valid email" unless ValidationHelpers.validate_email(value)
          
        when :url
          raise ValidationError, "#{field} must be a valid URL" unless valid_url?(value)
        end
      end
    end
    
    sanitized_input
  end
  
  def self.sanitize_input(input)
    return input unless input.is_a?(Hash)
    
    sanitized = {}
    
    input.each do |key, value|
      case value
      when String
        # Remove potentially dangerous characters
        sanitized[key] = value.gsub(/[<>'"&]/, '')
      when Array
        sanitized[key] = value.map { |v| sanitize_value(v) }
      when Hash
        sanitized[key] = sanitize_input(value)
      else
        sanitized[key] = value
      end
    end
    
    sanitized
  end
  
  private
  
  def self.valid_url?(url)
    begin
      uri = URI.parse(url)
      uri.is_a?(URI::HTTP) || uri.is_a?(URI::HTTPS)
    rescue URI::InvalidURIError
      false
    end
  end
  
  def self.sanitize_value(value)
    case value
    when String
      value.gsub(/[<>'"&]/, '')
    else
      value
    end
  end
end

# Usage
user_input = {
  name: "John Doe",
  email: "john@example.com",
  age: "25",
  bio: "<script>alert('xss')</script>Hello world!"
}

validation_rules = {
  name: [
    { type: :required },
    { type: :max_length, max: 100 },
    { type: :format, pattern: /\A[a-zA-Z\s]+\z/ }
  ],
  email: [
    { type: :required },
    { type: :email }
  ],
  age: [
    { type: :required },
    { type: :numeric }
  ],
  bio: [
    { type: :max_length, max: 500 }
  ]
}

begin
  sanitized_input = InputValidator.validate_user_input(user_input, validation_rules)
  puts "Input validated successfully"
rescue ValidationError => e
  puts "Validation error: #{e.message}"
end
```

### SQL Injection Prevention

```ruby
# ✅ GOOD: Use parameterized queries
class UserRepository
  def initialize(database)
    @database = database
  end
  
  def find_by_email(email)
    # Safe: Uses parameterized query
    result = @database.exec_params(
      "SELECT * FROM users WHERE email = $1",
      [email]
    )
    
    return nil if result.ntuples == 0
    
    row = result[0]
    User.new(
      id: row['id'],
      email: row['email'],
      name: row['name']
    )
  end
  
  def search_users(query)
    # Safe: Uses parameterized query with LIKE
    search_pattern = "%#{query}%"
    
    result = @database.exec_params(
      "SELECT * FROM users WHERE name ILIKE $1 OR email ILIKE $1",
      [search_pattern]
    )
    
    result.map do |row|
      User.new(
        id: row['id'],
        email: row['email'],
        name: row['name']
      )
    end
  end
  
  def create_user(user_data)
    # Safe: Uses parameterized query
    result = @database.exec_params(
      "INSERT INTO users (name, email, created_at) VALUES ($1, $2, $3) RETURNING id",
      [user_data[:name], user_data[:email], Time.now]
    )
    
    result[0]['id']
  end
end

# ❌ BAD: Vulnerable to SQL injection
class UserRepositoryBad
  def initialize(database)
    @database = database
  end
  
  def find_by_email(email)
    # Dangerous: Interpolates user input directly into SQL
    query = "SELECT * FROM users WHERE email = '#{email}'"
    result = @database.exec(query)
    
    return nil if result.ntuples == 0
    
    row = result[0]
    User.new(
      id: row['id'],
      email: row['email'],
      name: row['name']
    )
  end
  
  def search_users(query)
    # Dangerous: Interpolates user input directly into SQL
    search_pattern = "%#{query}%"
    sql_query = "SELECT * FROM users WHERE name ILIKE '#{search_pattern}' OR email ILIKE '#{search_pattern}'"
    result = @database.exec(sql_query)
    
    result.map do |row|
      User.new(
        id: row['id'],
        email: row['email'],
        name: row['name']
      )
    end
  end
end
```

### Authentication and Authorization

```ruby
# ✅ GOOD: Secure authentication with proper password hashing
class AuthenticationService
  def initialize(user_repository, session_manager)
    @user_repository = user_repository
    @session_manager = session_manager
    @password_cost = 12  # bcrypt cost factor
  end
  
  def authenticate(email, password)
    user = @user_repository.find_by_email(email)
    return nil unless user
    
    if BCrypt::Password.new(user.password_hash) == password
      session_token = @session_manager.create_session(user.id)
      
      {
        user_id: user.id,
        session_token: session_token,
        expires_at: Time.now + 24.hours
      }
    else
      nil
    end
  end
  
  def register(user_data)
    # Validate input
    validate_registration_data(user_data)
    
    # Hash password securely
    password_hash = BCrypt::Password.create(user_data[:password], cost: @password_cost)
    
    user = User.new(
      email: user_data[:email],
      password_hash: password_hash,
      name: user_data[:name],
      created_at: Time.now
    )
    
    @user_repository.save(user)
  end
  
  def logout(session_token)
    @session_manager.destroy_session(session_token)
  end
  
  def validate_session(session_token)
    session = @session_manager.find_session(session_token)
    return nil unless session
    
    return nil if session[:expires_at] < Time.now
    
    user = @user_repository.find(session[:user_id])
    return nil unless user
    
    {
      user_id: user.id,
      email: user.email,
      name: user.name
    }
  end
  
  private
  
  def validate_registration_data(user_data)
    raise ValidationError, "Email is required" if user_data[:email].nil?
    raise ValidationError, "Password is required" if user_data[:password].nil?
    raise ValidationError, "Name is required" if user_data[:name].nil?
    
    raise ValidationError, "Invalid email format" unless ValidationHelpers.validate_email(user_data[:email])
    raise ValidationError, "Password must be at least 8 characters" if user_data[:password].length < 8
    
    # Check if user already exists
    existing_user = @user_repository.find_by_email(user_data[:email])
    raise ValidationError, "User already exists" if existing_user
  end
end

# ✅ GOOD: Role-based authorization
class AuthorizationService
  def initialize(user_repository)
    @user_repository = user_repository
  end
  
  def can_perform_action?(user_id, action, resource = nil)
    user = @user_repository.find(user_id)
    return false unless user
    
    case action
    when :read_users
      user.role.in?([:admin, :manager])
    when :create_user
      user.role == :admin
    when :delete_user
      user.role == :admin
    when :read_orders
      user.role.in?([:admin, :manager, :user])
    when :create_order
      user.role.in?([:admin, :manager, :user])
    when :delete_order
      user.role.in?([:admin, :manager])
    when :read_own_orders
      # Users can read their own orders
      return true if resource && resource[:user_id] == user_id
      user.role.in?([:admin, :manager])
    else
      false
    end
  end
  
  def authorize!(user_id, action, resource = nil)
    unless can_perform_action?(user_id, action, resource)
      raise AuthorizationError, "Not authorized to perform #{action}"
    end
    
    true
  end
end

class AuthorizationError < StandardError; end
```

## Performance Best Practices

### Memory Management

```ruby
# ✅ GOOD: Efficient memory usage
class DataProcessor
  def initialize
    @batch_size = 1000
  end
  
  def process_large_file(file_path)
    # Process file in chunks to avoid loading everything into memory
    File.open(file_path, 'r') do |file|
      batch = []
      
      file.each_line do |line|
        batch << line.chomp
        
        if batch.size >= @batch_size
          process_batch(batch)
          batch.clear  # Clear batch to free memory
          
          # Force garbage collection periodically
          GC.start if rand < 0.1
        end
      end
      
      # Process remaining lines
      process_batch(batch) unless batch.empty?
    end
  end
  
  def process_batch(batch)
    # Process batch of data
    batch.each do |line|
      # Process individual line
      processed_line = process_line(line)
      yield processed_line if block_given?
    end
  end
  
  private
  
  def process_line(line)
    # Process individual line
    "Processed: #{line}"
  end
end

# ✅ GOOD: Use objects efficiently
class ObjectPool
  def initialize(pool_size = 10, &factory)
    @pool = Queue.new
    @factory = factory
    @created = 0
    @max_size = pool_size
    
    # Pre-populate pool
    pool_size.times { create_object }
  end
  
  def with_object
    obj = acquire
    begin
      yield obj
    ensure
      release(obj)
    end
  end
  
  private
  
  def acquire
    if @pool.empty? && @created < @max_size
      create_object
    end
    
    @pool.pop
  end
  
  def release(obj)
    reset_object(obj)
    @pool.push(obj)
  end
  
  def create_object
    obj = @factory.call
    @created += 1
    @pool.push(obj)
  end
  
  def reset_object(obj)
    # Reset object state if needed
    obj.clear if obj.respond_to?(:clear)
  end
end

# Usage: Database connection pool
connection_pool = ObjectPool.new(5) { DatabaseConnection.new }

connection_pool.with_object do |connection|
  result = connection.execute("SELECT * FROM users")
  process_results(result)
end
```

### Caching Strategies

```ruby
# ✅ GOOD: Implement caching with proper invalidation
class CacheService
  def initialize(redis_client)
    @redis = redis_client
    @default_ttl = 3600  # 1 hour
  end
  
  def get(key)
    cached_data = @redis.get(key)
    return nil unless cached_data
    
    JSON.parse(cached_data, symbolize_names: true)
  end
  
  def set(key, data, ttl = nil)
    ttl ||= @default_ttl
    
    @redis.setex(key, ttl, data.to_json)
  end
  
  def delete(key)
    @redis.del(key)
  end
  
  def fetch(key, ttl = nil, &block)
    cached_data = get(key)
    return cached_data if cached_data
    
    data = block.call
    set(key, data, ttl)
    data
  end
  
  def invalidate_pattern(pattern)
    keys = @redis.keys(pattern)
    @redis.del(keys) if keys.any?
  end
end

class UserService
  def initialize(user_repository, cache_service)
    @user_repository = user_repository
    @cache = cache_service
  end
  
  def get_user(user_id)
    cache_key = "user:#{user_id}"
    
    @cache.fetch(cache_key, 1800) do  # 30 minutes TTL
      @user_repository.find(user_id)
    end
  end
  
  def update_user(user_id, updates)
    user = @user_repository.update(user_id, updates)
    
    # Invalidate cache
    cache_key = "user:#{user_id}"
    @cache.delete(cache_key)
    
    # Invalidate related caches
    @cache.invalidate_pattern("user:#{user_id}:*")
    
    user
  end
  
  def get_user_orders(user_id)
    cache_key = "user:#{user_id}:orders"
    
    @cache.fetch(cache_key, 600) do  # 10 minutes TTL
      @user_repository.find_user_orders(user_id)
    end
  end
end
```

### Database Optimization

```ruby
# ✅ GOOD: Efficient database queries
class OrderRepository
  def initialize(database)
    @database = database
  end
  
  def find_with_items(order_id)
    # Use JOIN instead of N+1 queries
    result = @database.exec_params(
      <<~SQL
        SELECT 
          o.id as order_id,
          o.order_number,
          o.created_at as order_created_at,
          o.total as order_total,
          i.id as item_id,
          i.name as item_name,
          i.quantity as item_quantity,
          i.price as item_price
        FROM orders o
        LEFT JOIN order_items i ON o.id = i.order_id
        WHERE o.id = $1
        ORDER BY i.id
      SQL
      [order_id]
    )
    
    return nil if result.ntuples == 0
    
    # Build order with items
    order_data = nil
    items = []
    
    result.each do |row|
      if order_data.nil?
        order_data = {
          id: row['order_id'],
          order_number: row['order_number'],
          created_at: row['order_created_at'],
          total: row['order_total'].to_f,
          items: []
        }
      end
      
      if row['item_id']
        items << {
          id: row['item_id'],
          name: row['item_name'],
          quantity: row['item_quantity'].to_i,
          price: row['item_price'].to_f
        }
      end
    end
    
    order_data[:items] = items if order_data
    order_data
  end
  
  def find_recent_orders(limit = 10, offset = 0)
    # Use pagination with LIMIT and OFFSET
    result = @database.exec_params(
      "SELECT * FROM orders ORDER BY created_at DESC LIMIT $1 OFFSET $2",
      [limit, offset]
    )
    
    result.map do |row|
      {
        id: row['id'],
        order_number: row['order_number'],
        created_at: row['created_at'],
        total: row['total'].to_f
      }
    end
  end
  
  def find_orders_by_date_range(start_date, end_date)
    # Use indexed date column for efficient filtering
    result = @database.exec_params(
      "SELECT * FROM orders WHERE created_at BETWEEN $1 AND $2 ORDER BY created_at",
      [start_date, end_date]
    )
    
    result.map do |row|
      {
        id: row['id'],
        order_number: row['order_number'],
        created_at: row['created_at'],
        total: row['total'].to_f
      }
    end
  end
  
  def bulk_update_status(order_ids, status)
    # Use bulk update instead of individual updates
    placeholders = order_ids.map.with_index { |_, i| "$#{i + 1}" }.join(',')
    
    @database.exec_params(
      "UPDATE orders SET status = $#{order_ids.length + 1} WHERE id IN (#{placeholders})",
      order_ids + [status]
    )
  end
end
```

## Error Handling Best Practices

### Custom Exceptions

```ruby
# ✅ GOOD: Create specific exception classes
module ApplicationExceptions
  class BaseError < StandardError
    attr_reader :error_code, :context
    
    def initialize(message, error_code: nil, context: {})
      super(message)
      @error_code = error_code
      @context = context
    end
    
    def to_hash
      {
        error: self.class.name,
        message: message,
        error_code: error_code,
        context: context
      }
    end
  end
  
  class ValidationError < BaseError; end
  class AuthenticationError < BaseError; end
  class AuthorizationError < BaseError; end
  class NotFoundError < BaseError; end
  class BusinessLogicError < BaseError; end
  class ExternalServiceError < BaseError; end
end

# Usage
class UserService
  def create_user(user_data)
    validate_user_data(user_data)
    
    begin
      user = User.new(user_data)
      saved_user = repository.save(user)
      saved_user
    rescue DatabaseError => e
      raise ApplicationExceptions::BusinessLogicError.new(
        "Failed to create user: #{e.message}",
        error_code: 'USER_CREATION_FAILED',
        context: { user_data: user_data.except(:password) }
      )
    end
  end
  
  private
  
  def validate_user_data(user_data)
    raise ApplicationExceptions::ValidationError.new(
      "Email is required",
      error_code: 'MISSING_EMAIL',
      context: { field: 'email' }
    ) if user_data[:email].nil?
    
    raise ApplicationExceptions::ValidationError.new(
      "Invalid email format",
      error_code: 'INVALID_EMAIL',
      context: { email: user_data[:email] }
    ) unless ValidationHelpers.validate_email(user_data[:email])
  end
end
```

### Graceful Error Handling

```ruby
# ✅ GOOD: Graceful error handling with fallbacks
class ExternalAPIClient
  def initialize(base_url, timeout: 30, retries: 3)
    @base_url = base_url
    @timeout = timeout
    @retries = retries
  end
  
  def get_user(user_id)
    response = with_retries do
      make_request(:get, "/users/#{user_id}")
    end
    
    case response[:status]
    when 200
      response[:body]
    when 404
      raise ApplicationExceptions::NotFoundError.new(
        "User not found",
        error_code: 'USER_NOT_FOUND',
        context: { user_id: user_id }
      )
    when 500..599
      raise ApplicationExceptions::ExternalServiceError.new(
        "External service error: #{response[:status]}",
        error_code: 'EXTERNAL_SERVICE_ERROR',
        context: { status: response[:status] }
      )
    else
      raise ApplicationExceptions::ExternalServiceError.new(
        "Unexpected response: #{response[:status]}",
        error_code: 'UNEXPECTED_RESPONSE',
        context: { status: response[:status] }
      )
    end
  end
  
  def get_user_with_fallback(user_id, fallback_user = nil)
    get_user(user_id)
  rescue ApplicationExceptions::NotFoundError
    fallback_user
  rescue ApplicationExceptions::ExternalServiceError => e
    # Log error and return fallback
    Rails.logger.error "External service error: #{e.message}"
    fallback_user
  end
  
  private
  
  def with_retries(&block)
    attempts = 0
    
    begin
      block.call
    rescue Net::TimeoutError, Net::ReadTimeout, Net::OpenTimeout => e
      attempts += 1
      if attempts <= @retries
        sleep(2 ** attempts)  # Exponential backoff
        retry
      else
        raise ApplicationExceptions::ExternalServiceError.new(
          "Service unavailable after #{attempts} attempts: #{e.message}",
          error_code: 'SERVICE_UNAVAILABLE'
        )
      end
    end
  end
  
  def make_request(method, path)
    uri = URI("#{@base_url}#{path}")
    
    http = Net::HTTP.new(uri.host, uri.port)
    http.use_ssl = uri.scheme == 'https'
    http.read_timeout = @timeout
    http.open_timeout = @timeout
    
    case method
    when :get
      request = Net::HTTP::Get.new(uri)
    end
    
    request['Content-Type'] = 'application/json'
    request['User-Agent'] = 'RubyApp/1.0'
    
    response = http.request(request)
    
    {
      status: response.code.to_i,
      body: response.body.empty? ? nil : JSON.parse(response.body),
      headers: response.to_hash
    }
  rescue JSON::ParserError
    {
      status: response.code.to_i,
      body: response.body,
      headers: response.to_hash
    }
  end
end
```

## Testing Best Practices

### Test Organization

```ruby
# ✅ GOOD: Well-organized test structure
RSpec.describe UserService, type: :service do
  let(:user_repository) { instance_double(UserRepository) }
  let(:email_service) { instance_double(EmailService) }
  let(:password_validator) { instance_double(PasswordValidator) }
  
  let(:service) do
    UserService.new(
      user_repository: user_repository,
      email_service: email_service,
      password_validator: password_validator
    )
  end
  
  describe '#register' do
    let(:valid_user_data) do
      {
        email: 'test@example.com',
        password: 'SecurePassword123!',
        name: 'Test User'
      }
    end
    
    context 'with valid user data' do
      it 'creates a new user' do
        # Arrange
        expect(password_validator).to receive(:valid?).with('SecurePassword123!').and_return(true)
        expect(password_validator).to receive(:hash_password).with('SecurePassword123!').and_return('hashed_password')
        expect(user_repository).to receive(:save).with(
          have_attributes(
            email: 'test@example.com',
            password_hash: 'hashed_password',
            name: 'Test User'
          )
        ).and_return(double(id: 1, email: 'test@example.com', name: 'Test User'))
        expect(email_service).to receive(:send_welcome_email)
        
        # Act
        result = service.register(valid_user_data)
        
        # Assert
        expect(result.id).to eq(1)
        expect(result.email).to eq('test@example.com')
        expect(result.name).to eq('Test User')
      end
    end
    
    context 'with invalid email' do
      let(:invalid_user_data) { valid_user_data.merge(email: 'invalid-email') }
      
      it 'raises ValidationError' do
        expect(password_validator).to receive(:valid?).with('SecurePassword123!').and_return(true)
        
        expect {
          service.register(invalid_user_data)
        }.to raise_error(ApplicationExceptions::ValidationError, 'Invalid email format')
      end
    end
    
    context 'when repository fails' do
      it 'raises BusinessLogicError' do
        expect(password_validator).to receive(:valid?).with('SecurePassword123!').and_return(true)
        expect(password_validator).to receive(:hash_password).with('SecurePassword123!').and_return('hashed_password')
        expect(user_repository).to receive(:save).and_raise(DatabaseError, 'Connection failed')
        
        expect {
          service.register(valid_user_data)
        }.to raise_error(ApplicationExceptions::BusinessLogicError, /Failed to create user/)
      end
    end
  end
end
```

### Test Data Management

```ruby
# ✅ GOOD: Use factories for test data
FactoryBot.define do
  factory :user do
    sequence(:email) { |n| "user#{n}@example.com" }
    name { 'Test User' }
    password { 'SecurePassword123!' }
    created_at { Time.current }
    
    trait :admin do
      role { 'admin' }
    end
    
    trait :with_orders do
      after(:create) do |user|
        create_list(:order, 3, user: user)
      end
    end
  end
  
  factory :order do
    sequence(:order_number) { |n| "ORD-#{n}" }
    total { 100.0 }
    status { 'pending' }
    created_at { Time.current }
    association :user
  end
end

# Usage in tests
RSpec.describe OrderService do
  let(:user) { create(:user, :admin) }
  let(:order) { create(:order, user: user) }
  
  it 'processes order successfully' do
    result = service.process_order(order)
    expect(result[:status]).to eq('processed')
  end
end
```

## Documentation Best Practices

### Code Documentation

```ruby
# ✅ GOOD: Comprehensive method documentation
class PaymentProcessor
  # Processes a payment using the configured payment gateway
  #
  # @param payment_data [Hash] The payment information
  # @option payment_data [Integer] :amount The payment amount in cents
  # @option payment_data [String] :payment_token The payment token from the client
  # @option payment_data [String] :description A description of the payment
  # @option payment_data [Hash] :metadata Additional metadata for the payment
  #
  # @return [Hash] The payment result
  #   * :transaction_id [String] The unique transaction identifier
  #   * :status [String] The payment status ('succeeded', 'failed', 'pending')
  #   * :amount [Integer] The processed amount
  #   * :created_at [Time] When the payment was created
  #
  # @raise [PaymentError] When payment processing fails
  # @raise [FraudError] When the payment is flagged as suspicious
  # @raise [ValidationError] When payment data is invalid
  #
  # @example Process a successful payment
  #   processor = PaymentProcessor.new(gateway: stripe_gateway)
  #   result = processor.process_payment(
  #     amount: 1000,
  #     payment_token: 'tok_123456',
  #     description: 'Test payment'
  #   )
  #   puts result[:transaction_id]
  #
  # @example Handle payment errors
  #   begin
  #     processor.process_payment(invalid_data)
  #   rescue PaymentError => e
  #     puts "Payment failed: #{e.message}"
  #   end
  #
  # @see https://stripe.com/docs/api/charges Stripe API documentation
  # @since 1.0.0
  def process_payment(payment_data)
    validate_payment_data(payment_data)
    
    # Implementation details...
  end
  
  private
  
  # Validates payment data before processing
  #
  # @param payment_data [Hash] The payment data to validate
  # @raise [ValidationError] When validation fails
  def validate_payment_data(payment_data)
    required_fields = [:amount, :payment_token, :description]
    missing_fields = required_fields.reject { |field| payment_data.key?(field) }
    
    raise ValidationError, "Missing required fields: #{missing_fields.join(', ')}" if missing_fields.any?
    
    raise ValidationError, "Amount must be positive" unless payment_data[:amount] > 0
    raise ValidationError, "Payment token cannot be empty" if payment_data[:payment_token].strip.empty?
  end
end
```

## Practice Exercises

### Exercise 1: Code Refactoring
Refactor a poorly structured codebase to follow Ruby best practices:
- Apply SOLID principles
- Improve naming conventions
- Add proper error handling
- Implement dependency injection

### Exercise 2: Security Audit
Perform a security audit on a Ruby application:
- Identify security vulnerabilities
- Implement proper input validation
- Add authentication and authorization
- Secure against common attacks

### Exercise 3: Performance Optimization
Optimize a slow Ruby application:
- Profile memory usage
- Optimize database queries
- Implement caching strategies
- Improve algorithm efficiency

### Exercise 4: Test Suite Improvement
Improve test coverage and quality:
- Add comprehensive unit tests
- Implement integration tests
- Add performance tests
- Improve test data management

---

**Ready to explore more advanced Ruby topics? Let's continue! 📚**
