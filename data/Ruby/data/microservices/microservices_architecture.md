# Microservices Architecture in Ruby

## Overview

This guide covers building microservices with Ruby, including service design, inter-service communication, data management, deployment strategies, and operational concerns.

## Service Design Principles

### Single Responsibility Service

```ruby
# User Service - handles only user-related operations
class UserService
  def initialize(user_repository, event_publisher)
    @user_repository = user_repository
    @event_publisher = event_publisher
  end
  
  def create_user(user_data)
    user = User.new(user_data)
    
    if user.valid?
      @user_repository.save(user)
      
      # Publish event
      @event_publisher.publish(UserCreatedEvent.new(user))
      
      user
    else
      raise ValidationError, user.errors.full_messages
    end
  end
  
  def get_user(user_id)
    @user_repository.find(user_id)
  end
  
  def update_user(user_id, updates)
    user = @user_repository.find(user_id)
    raise NotFoundError, "User not found" unless user
    
    user.update(updates)
    
    if user.valid?
      @user_repository.save(user)
      @event_publisher.publish(UserUpdatedEvent.new(user))
      user
    else
      raise ValidationError, user.errors.full_messages
    end
  end
  
  def delete_user(user_id)
    user = @user_repository.find(user_id)
    raise NotFoundError, "User not found" unless user
    
    @user_repository.delete(user)
    @event_publisher.publish(UserDeletedEvent.new(user))
    
    true
  end
end

# Order Service - handles only order-related operations
class OrderService
  def initialize(order_repository, user_service_client, payment_service_client, event_publisher)
    @order_repository = order_repository
    @user_service_client = user_service_client
    @payment_service_client = payment_service_client
    @event_publisher = event_publisher
  end
  
  def create_order(user_id, order_data)
    # Verify user exists
    user = @user_service_client.get_user(user_id)
    raise NotFoundError, "User not found" unless user
    
    order = Order.new(user_id: user_id, items: order_data[:items])
    
    if order.valid?
      # Calculate total
      total = calculate_total(order)
      order.total = total
      
      # Process payment
      payment_result = @payment_service_client.process_payment(
        user_id: user_id,
        amount: total,
        order_id: order.id
      )
      
      if payment_result[:success]
        order.status = :confirmed
        @order_repository.save(order)
        
        @event_publisher.publish(OrderCreatedEvent.new(order))
        order
      else
        raise PaymentError, payment_result[:error]
      end
    else
      raise ValidationError, order.errors.full_messages
    end
  end
  
  def get_order(order_id)
    @order_repository.find(order_id)
  end
  
  def get_user_orders(user_id)
    @order_repository.find_by_user_id(user_id)
  end
  
  private
  
  def calculate_total(order)
    order.items.sum { |item| item[:price] * item[:quantity] }
  end
end
```

### Domain-Driven Design

```ruby
# Domain Models
class User
  attr_reader :id, :name, :email, :created_at
  
  def initialize(data)
    @id = data[:id] || SecureRandom.uuid
    @name = data[:name]
    @email = data[:email]
    @created_at = data[:created_at] || Time.now
    @errors = {}
  end
  
  def valid?
    validate_name
    validate_email
    @errors.empty?
  end
  
  def update(updates)
    @name = updates[:name] if updates[:name]
    @email = updates[:email] if updates[:email]
    validate_name if updates[:name]
    validate_email if updates[:email]
  end
  
  def errors
    @errors
  end
  
  private
  
  def validate_name
    @errors[:name] = ["Name can't be blank"] if @name.nil? || @name.strip.empty?
    @errors[:name] = ["Name is too short"] if @name && @name.length < 2
  end
  
  def validate_email
    @errors[:email] = ["Email can't be blank"] if @email.nil? || @email.strip.empty?
    @errors[:email] = ["Email is invalid"] unless @email&.match?(/\A[^@\s]+@[^@\s]+\z/)
  end
end

class Order
  attr_reader :id, :user_id, :items, :total, :status, :created_at
  
  def initialize(data)
    @id = data[:id] || SecureRandom.uuid
    @user_id = data[:user_id]
    @items = data[:items] || []
    @total = data[:total] || 0
    @status = data[:status] || :pending
    @created_at = data[:created_at] || Time.now
    @errors = {}
  end
  
  def valid?
    validate_user_id
    validate_items
    @errors.empty?
  end
  
  def confirm!
    @status = :confirmed
  end
  
  def cancel!
    @status = :cancelled
  end
  
  def errors
    @errors
  end
  
  private
  
  def validate_user_id
    @errors[:user_id] = ["User ID can't be blank"] if @user_id.nil? || @user_id.empty?
  end
  
  def validate_items
    @errors[:items] = ["Items can't be empty"] if @items.empty?
    
    @items.each_with_index do |item, index|
      if item[:price].nil? || item[:price] <= 0
        @errors["items_#{index}_price"] = ["Price must be greater than 0"]
      end
      
      if item[:quantity].nil? || item[:quantity] <= 0
        @errors["items_#{index}_quantity"] = ["Quantity must be greater than 0"]
      end
    end
  end
end
```

## Inter-Service Communication

### Synchronous Communication

```ruby
# Service Client Base Class
class ServiceClient
  def initialize(base_url, circuit_breaker = nil)
    @base_url = base_url
    @circuit_breaker = circuit_breaker
    @http = Net::HTTP.new(URI(base_url).host, URI(base_url).port)
    @http.use_ssl = base_url.start_with?('https')
    @http.read_timeout = 30
    @http.open_timeout = 10
  end
  
  protected
  
  def make_request(method, path, body = nil)
    if @circuit_breaker
      @circuit_breaker.call { perform_request(method, path, body) }
    else
      perform_request(method, path, body)
    end
  end
  
  private
  
  def perform_request(method, path, body)
    uri = URI("#{@base_url}#{path}")
    
    case method
    when :get
      request = Net::HTTP::Get.new(uri)
    when :post
      request = Net::HTTP::Post.new(uri)
      request['Content-Type'] = 'application/json'
      request.body = body.to_json
    when :put
      request = Net::HTTP::Put.new(uri)
      request['Content-Type'] = 'application/json'
      request.body = body.to_json
    when :delete
      request = Net::HTTP::Delete.new(uri)
    end
    
    response = @http.request(request)
    
    case response
    when Net::HTTPSuccess
      JSON.parse(response.body)
    when Net::HTTPNotFound
      raise ServiceError, "Resource not found"
    when Net::HTTPUnprocessableEntity
      raise ValidationError, JSON.parse(response.body)['errors']
    else
      raise ServiceError, "HTTP #{response.code}: #{response.message}"
    end
  rescue JSON::ParserError
    raise ServiceError, "Invalid response format"
  rescue => e
    raise ServiceError, "Connection error: #{e.message}"
  end
end

# User Service Client
class UserServiceClient < ServiceClient
  def get_user(user_id)
    make_request(:get, "/users/#{user_id}")
  end
  
  def create_user(user_data)
    make_request(:post, "/users", user_data)
  end
  
  def update_user(user_id, updates)
    make_request(:put, "/users/#{user_id}", updates)
  end
  
  def delete_user(user_id)
    make_request(:delete, "/users/#{user_id}")
  end
end

# Payment Service Client
class PaymentServiceClient < ServiceClient
  def process_payment(payment_data)
    make_request(:post, "/payments", payment_data)
  end
  
  def get_payment(payment_id)
    make_request(:get, "/payments/#{payment_id}")
  end
  
  def refund_payment(payment_id, amount)
    make_request(:post, "/payments/#{payment_id}/refund", { amount: amount })
  end
end
```

### Asynchronous Communication

```ruby
# Event Bus
class EventBus
  def initialize
    @handlers = Hash.new { |h, k| h[k] = [] }
    @mutex = Mutex.new
  end
  
  def subscribe(event_type, handler)
    @mutex.synchronize do
      @handlers[event_type] << handler
    end
  end
  
  def publish(event)
    @mutex.synchronize do
      handlers = @handlers[event.class] || []
      
      handlers.each do |handler|
        Thread.new { handler.call(event) }
      end
    end
  end
end

# Event Classes
class DomainEvent
  attr_reader :id, :timestamp, :data
  
  def initialize(data)
    @id = SecureRandom.uuid
    @timestamp = Time.now
    @data = data
  end
end

class UserCreatedEvent < DomainEvent
  def initialize(user)
    super({
      user_id: user.id,
      name: user.name,
      email: user.email
    })
  end
end

class OrderCreatedEvent < DomainEvent
  def initialize(order)
    super({
      order_id: order.id,
      user_id: order.user_id,
      total: order.total,
      items: order.items
    })
  end
end

# Event Publisher
class EventPublisher
  def initialize(event_bus, message_queue)
    @event_bus = event_bus
    @message_queue = message_queue
  end
  
  def publish(event)
    # Publish locally
    @event_bus.publish(event)
    
    # Publish to message queue for other services
    @message_queue.publish({
      event_type: event.class.name,
      event_id: event.id,
      timestamp: event.timestamp,
      data: event.data
    })
  end
end
```

### Circuit Breaker Pattern

```ruby
class CircuitBreaker
  def initialize(failure_threshold = 5, timeout = 60)
    @failure_threshold = failure_threshold
    @timeout = timeout
    @failure_count = 0
    @last_failure_time = nil
    @state = :closed
    @mutex = Mutex.new
  end
  
  def call(&block)
    @mutex.synchronize do
      case @state
      when :open
        if Time.now - @last_failure_time > @timeout
          @state = :half_open
        else
          raise CircuitBreakerError, "Circuit breaker is open"
        end
      when :half_open
        execute_with_circuit_breaker(&block)
      else # :closed
        execute_with_circuit_breaker(&block)
      end
    end
  end
  
  private
  
  def execute_with_circuit_breaker(&block)
    begin
      result = block.call
      reset
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
  
  def reset
    @failure_count = 0
    @state = :closed
  end
end

class CircuitBreakerError < StandardError; end
```

## Data Management

### Database per Service

```ruby
# Repository Pattern
class UserRepository
  def initialize(database_connection)
    @db = database_connection
  end
  
  def save(user)
    if user.id && find(user.id)
      update_user(user)
    else
      create_user(user)
    end
  end
  
  def find(user_id)
    result = @db.exec_params(
      "SELECT id, name, email, created_at FROM users WHERE id = $1",
      [user_id]
    )
    
    return nil if result.ntuples == 0
    
    row = result[0]
    User.new(
      id: row['id'],
      name: row['name'],
      email: row['email'],
      created_at: row['created_at']
    )
  end
  
  def find_all
    result = @db.exec("SELECT id, name, email, created_at FROM users")
    
    result.map do |row|
      User.new(
        id: row['id'],
        name: row['name'],
        email: row['email'],
        created_at: row['created_at']
      )
    end
  end
  
  def delete(user)
    @db.exec_params("DELETE FROM users WHERE id = $1", [user.id])
  end
  
  private
  
  def create_user(user)
    result = @db.exec_params(
      "INSERT INTO users (id, name, email, created_at) VALUES ($1, $2, $3, $4) RETURNING id",
      [user.id, user.name, user.email, user.created_at]
    )
    
    user.id = result[0]['id']
    user
  end
  
  def update_user(user)
    @db.exec_params(
      "UPDATE users SET name = $1, email = $2 WHERE id = $3",
      [user.name, user.email, user.id]
    )
    
    user
  end
end

# Order Repository
class OrderRepository
  def initialize(database_connection)
    @db = database_connection
  end
  
  def save(order)
    if order.id && find(order.id)
      update_order(order)
    else
      create_order(order)
    end
  end
  
  def find(order_id)
    result = @db.exec_params(
      "SELECT id, user_id, items, total, status, created_at FROM orders WHERE id = $1",
      [order_id]
    )
    
    return nil if result.ntuples == 0
    
    row = result[0]
    Order.new(
      id: row['id'],
      user_id: row['user_id'],
      items: JSON.parse(row['items']),
      total: row['total'].to_f,
      status: row['status'].to_sym,
      created_at: row['created_at']
    )
  end
  
  def find_by_user_id(user_id)
    result = @db.exec_params(
      "SELECT id, user_id, items, total, status, created_at FROM orders WHERE user_id = $1",
      [user_id]
    )
    
    result.map do |row|
      Order.new(
        id: row['id'],
        user_id: row['user_id'],
        items: JSON.parse(row['items']),
        total: row['total'].to_f,
        status: row['status'].to_sym,
        created_at: row['created_at']
      )
    end
  end
  
  def delete(order)
    @db.exec_params("DELETE FROM orders WHERE id = $1", [order.id])
  end
  
  private
  
  def create_order(order)
    result = @db.exec_params(
      "INSERT INTO orders (id, user_id, items, total, status, created_at) VALUES ($1, $2, $3, $4, $5, $6) RETURNING id",
      [order.id, order.user_id, order.items.to_json, order.total, order.status.to_s, order.created_at]
    )
    
    order.id = result[0]['id']
    order
  end
  
  def update_order(order)
    @db.exec_params(
      "UPDATE orders SET items = $1, total = $2, status = $3 WHERE id = $4",
      [order.items.to_json, order.total, order.status.to_s, order.id]
    )
    
    order
  end
end
```

### Event Sourcing

```ruby
class EventStore
  def initialize(database_connection)
    @db = database_connection
  end
  
  def save_events(aggregate_id, events)
    events.each do |event|
      @db.exec_params(
        "INSERT INTO events (id, aggregate_id, event_type, event_data, timestamp) VALUES ($1, $2, $3, $4, $5)",
        [event.id, aggregate_id, event.class.name, event.data.to_json, event.timestamp]
      )
    end
  end
  
  def get_events(aggregate_id)
    result = @db.exec_params(
      "SELECT event_type, event_data, timestamp FROM events WHERE aggregate_id = $1 ORDER BY timestamp",
      [aggregate_id]
    )
    
    result.map do |row|
      event_class = Object.const_get(row['event_type'])
      event_class.new(JSON.parse(row['event_data']))
    end
  end
  
  def get_all_events(event_type = nil)
    query = "SELECT aggregate_id, event_type, event_data, timestamp FROM events"
    params = []
    
    if event_type
      query += " WHERE event_type = $1"
      params << event_type
    end
    
    query += " ORDER BY timestamp"
    
    result = @db.exec_params(query, params)
    
    result.map do |row|
      {
        aggregate_id: row['aggregate_id'],
        event_type: row['event_type'],
        event_data: JSON.parse(row['event_data']),
        timestamp: row['timestamp']
      }
    end
  end
end

class AggregateRoot
  def initialize(id)
    @id = id
    @events = []
    @version = 0
  end
  
  attr_reader :id, :version
  
  def uncommitted_events
    @events.dup
  end
  
  def mark_events_as_committed
    @events.clear
  end
  
  protected
  
  def apply_event(event)
    @events << event
    apply(event)
    @version += 1
  end
  
  def load_from_history(events)
    events.each { |event| apply(event) }
    @version = events.length
  end
  
  private
  
  def apply(event)
    # Override in subclasses
  end
end

class UserAggregate < AggregateRoot
  attr_reader :name, :email, :created_at
  
  def initialize(id, data = {})
    super(id)
    @name = data[:name]
    @email = data[:email]
    @created_at = data[:created_at]
  end
  
  def change_name(new_name)
    apply_event(UserNameChangedEvent.new(@id, new_name))
  end
  
  def change_email(new_email)
    apply_event(UserEmailChangedEvent.new(@id, new_email))
  end
  
  private
  
  def apply(event)
    case event
    when UserCreatedEvent
      @name = event.data[:name]
      @email = event.data[:email]
      @created_at = event.timestamp
    when UserNameChangedEvent
      @name = event.data[:new_name]
    when UserEmailChangedEvent
      @email = event.data[:new_email]
    end
  end
end
```

## API Gateway

```ruby
class APIGateway
  def initialize
    @routes = {}
    @middleware = []
    @services = {}
  end
  
  def register_service(name, url)
    @services[name] = ServiceRegistry.new(name, url)
  end
  
  def add_route(method, path, service_name, service_path)
    @routes["#{method.upcase} #{path}"] = {
      service: service_name,
      path: service_path
    }
  end
  
  def use(middleware)
    @middleware << middleware
  end
  
  def handle_request(env)
    request = Rack::Request.new(env)
    route_key = "#{request.request_method} #{request.path_info}"
    
    route = @routes[route_key]
    return [404, {}, ['Not Found']] unless route
    
    service = @services[route[:service]]
    return [503, {}, ['Service Unavailable']] unless service
    
    # Apply middleware
    context = {
      request: request,
      service: service,
      service_path: route[:path]
    }
    
    @middleware.reduce(context) do |ctx, middleware|
      middleware.call(ctx)
    end
  end
  
  def call(env)
    handle_request(env)
  end
end

# Service Registry
class ServiceRegistry
  def initialize(name, base_url)
    @name = name
    @base_url = base_url
    @instances = []
    @current_index = 0
  end
  
  def add_instance(url)
    @instances << url
  end
  
  def get_instance
    return nil if @instances.empty?
    
    # Round-robin load balancing
    instance = @instances[@current_index]
    @current_index = (@current_index + 1) % @instances.length
    instance
  end
  
  def health_check
    @instances.each do |instance|
      begin
        response = Net::HTTP.get_response(URI("#{instance}/health"))
        return false unless response.is_a?(Net::HTTPSuccess)
      rescue
        return false
      end
    end
    
    true
  end
end

# Middleware
class AuthenticationMiddleware
  def call(context)
    request = context[:request]
    
    # Skip authentication for health endpoints
    return context if request.path_info == '/health'
    
    token = request.get_header('HTTP_AUTHORIZATION')
    
    unless token && valid_token?(token)
      return [401, {}, ['Unauthorized']]
    end
    
    user_id = extract_user_id(token)
    context[:user_id] = user_id
    
    context
  end
  
  private
  
  def valid_token?(token)
    # Implement token validation logic
    token.start_with?('Bearer ')
  end
  
  def extract_user_id(token)
    # Implement user ID extraction logic
    'user_123'
  end
end

class LoggingMiddleware
  def call(context)
    request = context[:request]
    start_time = Time.now
    
    puts "[#{start_time}] #{request.request_method} #{request.path_info}"
    
    result = context
    
    end_time = Time.now
    duration = end_time - start_time
    
    puts "[#{end_time}] Completed in #{duration.round(3)}s"
    
    result
  end
end

class RateLimitingMiddleware
  def initialize(requests_per_minute = 100)
    @requests_per_minute = requests_per_minute
    @clients = {}
  end
  
  def call(context)
    client_ip = context[:request].ip
    
    @clients[client_ip] ||= []
    requests = @clients[client_ip]
    
    # Remove old requests (older than 1 minute)
    requests.reject! { |time| Time.now - time > 60 }
    
    if requests.length >= @requests_per_minute
      return [429, {}, ['Too Many Requests']]
    end
    
    requests << Time.now
    context
  end
end

# Usage
gateway = APIGateway.new

# Register services
gateway.register_service('user-service', 'http://localhost:3001')
gateway.register_service('order-service', 'http://localhost:3002')
gateway.register_service('payment-service', 'http://localhost:3003')

# Add routes
gateway.add_route('GET', '/users/:id', 'user-service', '/users/:id')
gateway.add_route('POST', '/users', 'user-service', '/users')
gateway.add_route('GET', '/orders/:id', 'order-service', '/orders/:id')
gateway.add_route('POST', '/orders', 'order-service', '/orders')
gateway.add_route('POST', '/payments', 'payment-service', '/payments')

# Add middleware
gateway.use(LoggingMiddleware.new)
gateway.use(AuthenticationMiddleware.new)
gateway.use(RateLimitingMiddleware.new(100))
```

## Service Discovery

```ruby
class ServiceDiscovery
  def initialize
    @services = {}
    @watchers = {}
    @mutex = Mutex.new
  end
  
  def register_service(name, instance)
    @mutex.synchronize do
      @services[name] ||= []
      @services[name] << instance
      
      # Notify watchers
      notify_watchers(name, :registered, instance)
    end
  end
  
  def unregister_service(name, instance_id)
    @mutex.synchronize do
      @services[name]&.reject! { |instance| instance[:id] == instance_id }
      
      # Notify watchers
      notify_watchers(name, :unregistered, instance_id)
    end
  end
  
  def discover_service(name)
    @mutex.synchronize do
      instances = @services[name] || []
      
      # Return healthy instances only
      healthy_instances = instances.select { |instance| instance[:healthy] }
      
      # Simple round-robin selection
      return nil if healthy_instances.empty?
      
      healthy_instances[rand(healthy_instances.length)]
    end
  end
  
  def watch_service(name, &block)
    @mutex.synchronize do
      @watchers[name] ||= []
      @watchers[name] << block
    end
  end
  
  def health_check_all
    @mutex.synchronize do
      @services.each do |name, instances|
        instances.each do |instance|
          healthy = health_check_instance(instance)
          instance[:healthy] = healthy
          instance[:last_check] = Time.now
        end
      end
    end
  end
  
  private
  
  def notify_watchers(name, action, data)
    watchers = @watchers[name] || []
    watchers.each { |watcher| watcher.call(action, data) }
  end
  
  def health_check_instance(instance)
    begin
      uri = URI("#{instance[:url]}/health")
      response = Net::HTTP.get_response(uri)
      response.is_a?(Net::HTTPSuccess)
    rescue
      false
    end
  end
end

# Service Instance
class ServiceInstance
  def initialize(name, url, health_check_path = '/health')
    @name = name
    @url = url
    @health_check_path = health_check_path
    @id = SecureRandom.uuid
    @healthy = true
    @last_check = Time.now
  end
  
  attr_reader :name, :url, :id, :healthy, :last_check
  
  def healthy?
    @healthy
  end
  
  def to_hash
    {
      id: @id,
      name: @name,
      url: @url,
      healthy: @healthy,
      last_check: @last_check
    }
  end
end

# Usage
discovery = ServiceDiscovery.new

# Register services
user_service = ServiceInstance.new('user-service', 'http://localhost:3001')
order_service = ServiceInstance.new('order-service', 'http://localhost:3002')

discovery.register_service('user-service', user_service.to_hash)
discovery.register_service('order-service', order_service.to_hash)

# Watch for service changes
discovery.watch_service('user-service') do |action, data|
  puts "User service #{action}: #{data[:url]}"
end

# Discover services
user_instance = discovery.discover_service('user-service')
puts "Discovered user service: #{user_instance[:url]}" if user_instance

# Health checking
Thread.new do
  loop do
    discovery.health_check_all
    sleep(30)
  end
end
```

## Configuration Management

```ruby
class ConfigurationManager
  def initialize
    @config = {}
    @watchers = {}
    @mutex = Mutex.new
  end
  
  def load_from_file(file_path)
    @mutex.synchronize do
      @config = YAML.load_file(file_path)
      notify_watchers
    end
  end
  
  def load_from_env(prefix = 'APP')
    @mutex.synchronize do
      ENV.each do |key, value|
        if key.start_with?(prefix + '_')
          config_key = key[prefix.length + 1..-1].downcase
          @config[config_key] = parse_value(value)
        end
      end
      
      notify_watchers
    end
  end
  
  def get(key, default = nil)
    @mutex.synchronize do
      value = @config[key]
      return default if value.nil?
      
      # Handle nested keys with dot notation
      keys = key.split('.')
      keys.reduce(@config) { |hash, k| hash&.dig(k) } || default
    end
  end
  
  def set(key, value)
    @mutex.synchronize do
      keys = key.split('.')
      last_key = keys.pop
      
      target = keys.reduce(@config) do |hash, k|
        hash[k] ||= {}
        hash[k]
      end
      
      target[last_key] = value
      notify_watchers(key, value)
    end
  end
  
  def watch(key, &block)
    @mutex.synchronize do
      @watchers[key] ||= []
      @watchers[key] << block
    end
  end
  
  private
  
  def parse_value(value)
    case value.downcase
    when 'true'
      true
    when 'false'
      false
    when /^\d+$/
      value.to_i
    when /^\d+\.\d+$/
      value.to_f
    else
      value
    end
  end
  
  def notify_watchers(key = nil, value = nil)
    @watchers.each do |watch_key, watchers|
      if key.nil? || watch_key == key
        watchers.each { |watcher| watcher.call(key || watch_key, value || get(key)) }
      end
    end
  end
end

# Usage
config = ConfigurationManager.new

# Load from file
config.load_from_file('config/services.yml')

# Load from environment
config.load_from_env('SERVICE')

# Watch for changes
config.watch('database_url') do |key, value|
  puts "Database URL changed: #{value}"
end

# Get configuration
db_url = config.get('database.url', 'sqlite://default.db')
max_connections = config.get('database.max_connections', 10)
```

## Deployment Strategies

### Containerization

```ruby
# Dockerfile Generator
class DockerfileGenerator
  def initialize(service_name, options = {})
    @service_name = service_name
    @ruby_version = options[:ruby_version] || '3.1'
    @base_image = options[:base_image] || 'ruby'
    @port = options[:port] || 3000
    @dependencies = options[:dependencies] || []
    @environment_vars = options[:environment_vars] || {}
  end
  
  def generate
    <<~DOCKERFILE
      FROM #{@base_image}:#{@ruby_version}
      
      WORKDIR /app
      
      # Install system dependencies
      #{system_dependencies}
      
      # Copy gem files
      COPY Gemfile Gemfile.lock ./
      
      # Install ruby dependencies
      RUN bundle config set --local without 'development test'
      RUN bundle install
      
      # Copy application code
      COPY . .
      
      # Create non-root user
      RUN groupadd -r app && useradd -r -g app app
      RUN chown -R app:app /app
      USER app
      
      # Expose port
      EXPOSE #{@port}
      
      # Set environment variables
      #{environment_variables}
      
      # Start command
      CMD ["bundle", "exec", "ruby", "server.rb"]
    DOCKERFILE
  end
  
  private
  
  def system_dependencies
    return '' if @dependencies.empty?
    
    deps = @dependencies.map { |dep| "    #{dep}" }.join("\n")
    "RUN apt-get update && apt-get install -y \\\n#{deps} && rm -rf /var/lib/apt/lists/*"
  end
  
  def environment_variables
    return '' if @environment_vars.empty?
    
    vars = @environment_vars.map { |key, value| "ENV #{key}=#{value}" }.join("\n")
    vars
  end
end

# Docker Compose Generator
class DockerComposeGenerator
  def initialize(services, options = {})
    @services = services
    @network_name = options[:network_name] || 'microservices'
    @volumes = options[:volumes] || {}
  end
  
  def generate
    <<~COMPOSE
      version: '3.8'
      
      networks:
        #{@network_name}:
          driver: bridge
      
      #{volumes_section}
      
      services:
        #{services_section}
    COMPOSE
  end
  
  private
  
  def volumes_section
    return '' if @volumes.empty?
    
    volumes = @volumes.map { |name, config| "    #{name}:\n      #{config}" }.join("\n")
    "  volumes:\n#{volumes}"
  end
  
  def services_section
    @services.map do |name, config|
      <<~SERVICE
        #{name}:
          image: #{config[:image]}
          ports:
            - "#{config[:port]}:#{config[:internal_port] || 3000}"
          environment:
            #{environment_variables(config[:environment] || {})}
          networks:
            - #{@network_name}
          depends_on:
            #{dependencies(config[:depends_on] || [])}
      SERVICE
    end.join("\n")
  end
  
  def environment_variables(vars)
    vars.map { |key, value| "            - #{key}=#{value}" }.join("\n")
  end
  
  def dependencies(deps)
    deps.map { |dep| "            - #{dep}" }.join("\n")
  end
end

# Usage
# Generate Dockerfile
dockerfile_generator = DockerfileGenerator.new('user-service', {
  ruby_version: '3.1',
  port: 3001,
  dependencies: ['postgresql-client'],
  environment_vars: {
    'RACK_ENV' => 'production',
    'DATABASE_URL' => 'postgresql://user:pass@db:5432/users'
  }
})

File.write('user-service/Dockerfile', dockerfile_generator.generate)

# Generate Docker Compose
services = {
  'user-service' => {
    image: 'user-service:latest',
    port: 3001,
    environment: {
      'DATABASE_URL' => 'postgresql://user:pass@postgres:5432/users',
      'REDIS_URL' => 'redis://redis:6379'
    },
    depends_on: ['postgres', 'redis']
  },
  'order-service' => {
    image: 'order-service:latest',
    port: 3002,
    environment: {
      'DATABASE_URL' => 'postgresql://order:pass@postgres:5432/orders',
      'USER_SERVICE_URL' => 'http://user-service:3001'
    },
    depends_on: ['postgres', 'user-service']
  },
  'postgres' => {
    image: 'postgres:14',
    port: 5432,
    environment: {
      'POSTGRES_DB' => 'microservices',
      'POSTGRES_USER' => 'postgres',
      'POSTGRES_PASSWORD' => 'password'
    }
  },
  'redis' => {
    image: 'redis:7',
    port: 6379
  }
}

compose_generator = DockerComposeGenerator.new(services, {
  volumes: {
    'postgres_data' => 'driver: local'
  }
})

File.write('docker-compose.yml', compose_generator.generate)
```

### Blue-Green Deployment

```ruby
class BlueGreenDeployment
  def initialize(service_name, load_balancer, deployment_config)
    @service_name = service_name
    @load_balancer = load_balancer
    @config = deployment_config
  end
  
  def deploy(new_version)
    puts "Starting blue-green deployment for #{@service_name} v#{new_version}"
    
    # Determine which environment is currently active
    current_env = @load_balancer.current_environment(@service_name)
    new_env = current_env == :blue ? :green : :blue
    
    puts "Current environment: #{current_env}"
    puts "Deploying to: #{new_env}"
    
    begin
      # Deploy to inactive environment
      deploy_to_environment(new_env, new_version)
      
      # Health check
      if health_check_passed?(new_env)
        puts "Health check passed for #{new_env}"
        
        # Switch traffic
        @load_balancer.switch_traffic(@service_name, new_env)
        
        puts "Traffic switched to #{new_env}"
        
        # Wait and verify
        sleep(30)
        
        if production_health_check_passed?
          puts "Deployment successful!"
          
          # Clean up old version
          cleanup_environment(current_env)
        else
          puts "Production health check failed, rolling back..."
          rollback(current_env)
        end
      else
        puts "Health check failed for #{new_env}"
        raise DeploymentError, "Health check failed"
      end
      
    rescue => e
      puts "Deployment failed: #{e.message}"
      rollback(current_env)
      raise
    end
  end
  
  private
  
  def deploy_to_environment(env, version)
    puts "Deploying version #{version} to #{env} environment"
    
    # Scale up new environment
    scale_service(env, @config[:instances])
    
    # Wait for deployment to complete
    wait_for_deployment(env, version)
  end
  
  def health_check_passed?(env)
    puts "Running health check for #{env}"
    
    attempts = 0
    max_attempts = 10
    
    while attempts < max_attempts
      if @load_balancer.health_check(@service_name, env)
        return true
      end
      
      attempts += 1
      puts "Health check attempt #{attempts} failed, retrying..."
      sleep(10)
    end
    
    false
  end
  
  def production_health_check_passed?
    # Check metrics, error rates, etc.
    true
  end
  
  def rollback(env)
    puts "Rolling back to #{env}"
    @load_balancer.switch_traffic(@service_name, env)
  end
  
  def scale_service(env, instances)
    # Implementation would call container orchestration API
    puts "Scaling #{env} to #{instances} instances"
  end
  
  def wait_for_deployment(env, version)
    # Wait for all instances to be ready
    puts "Waiting for #{env} deployment to complete"
    sleep(30)
  end
  
  def cleanup_environment(env)
    puts "Cleaning up #{env} environment"
    scale_service(env, 0)
  end
end

class DeploymentError < StandardError; end
```

## Best Practices

### 1. Service Design

```ruby
# Service Design Guidelines
class ServiceDesignGuidelines
  # ✅ DO: Keep services focused on a single business capability
  def good_service_design
    # User Service handles only user-related operations
    UserService = Class.new
    # Order Service handles only order-related operations
    OrderService = Class.new
  end
  
  # ❌ DON'T: Create services with mixed responsibilities
  def bad_service_design
    # Mixed service handling users, orders, and payments
    UserOrderPaymentService = Class.new
  end
  
  # ✅ DO: Use asynchronous communication for non-critical operations
  def good_communication
    # Event-driven communication for order processing
    event_bus.publish(OrderCreatedEvent.new(order))
  end
  
  # ❌ DON'T: Use synchronous calls for everything
  def bad_communication
    # Synchronous calls create tight coupling
    user_service.get_user(order.user_id)
    payment_service.process_payment(order)
    inventory_service.check_stock(order.items)
  end
end
```

### 2. Data Management

```ruby
class DataManagementGuidelines
  # ✅ DO: Use database per service pattern
  def good_data_management
    # Each service owns its own database
    user_database = 'users_db'
    order_database = 'orders_db'
    payment_database = 'payments_db'
  end
  
  # ❌ DON'T: Share databases between services
  def bad_data_management
    # Shared database creates tight coupling
    shared_database = 'microservices_db'
  end
  
  # ✅ DO: Use event sourcing for critical business events
  def good_event_sourcing
    # Store all domain events
    event_store.save_events(order_id, events)
  end
  
  # ❌ DON'T: Lose important business events
  def bad_event_sourcing
    # Only store current state
    order_repository.save(order)
  end
end
```

### 3. Error Handling

```ruby
class ErrorHandlingGuidelines
  # ✅ DO: Implement circuit breakers for external service calls
  def good_error_handling
    circuit_breaker = CircuitBreaker.new(5, 60)
    
    circuit_breaker.call do
      external_service.get_data
    end
  end
  
  # ❌ DON'T: Call external services without protection
  def bad_error_handling
    external_service.get_data  # Can cause cascading failures
  end
  
  # ✅ DO: Implement retry policies with exponential backoff
  def good_retry_policy
    RetryPolicy.new(max_attempts: 3, backoff: :exponential).call do
      service.process_request
    end
  end
  
  # ❌ DON'T: Retry immediately without backoff
  def bad_retry_policy
    3.times do
      begin
        service.process_request
        break
      rescue
        # Immediate retry can overwhelm the service
      end
    end
  end
end
```

## Practice Exercises

### Exercise 1: Complete Microservice
Build a complete microservice with:
- REST API endpoints
- Database integration
- Event publishing/consuming
- Health checks
- Configuration management

### Exercise 2: Service Mesh
Implement a service mesh with:
- Service discovery
- Load balancing
- Circuit breaking
- Request tracing
- Metrics collection

### Exercise 3: Event-Driven Architecture
Create an event-driven system with:
- Event sourcing
- Event store
- Event handlers
- Event replay
- Snapshot management

### Exercise 4: Deployment Pipeline
Build a complete deployment pipeline with:
- Automated testing
- Containerization
- Blue-green deployment
- Monitoring
- Rollback capabilities

---

**Ready to explore more advanced Ruby topics? Let's continue! 🏗️**
