# Service Communication in Microservices
# Comprehensive guide to inter-service communication patterns

## 🎯 Overview

Service communication is the backbone of microservices architecture. This guide covers synchronous and asynchronous communication patterns, message queues, event-driven architecture, and Ruby implementations.

## 🔄 Synchronous Communication

### 1. REST API Communication

HTTP-based REST APIs are the most common synchronous communication pattern:

```ruby
# Base HTTP client for service communication
class ServiceClient
  def initialize(base_url, timeout: 5, retries: 3)
    @base_url = base_url
    @timeout = timeout
    @retries = retries
  end
  
  def get(endpoint, params = {})
    make_request(:get, endpoint, nil, params)
  end
  
  def post(endpoint, data = {}, params = {})
    make_request(:post, endpoint, data, params)
  end
  
  def put(endpoint, data = {}, params = {})
    make_request(:put, endpoint, data, params)
  end
  
  def delete(endpoint, params = {})
    make_request(:delete, endpoint, nil, params)
  end
  
  private
  
  def make_request(method, endpoint, data, params)
    url = build_url(endpoint, params)
    options = build_options(data)
    
    retries = 0
    begin
      response = HTTP.timeout(@timeout).send(method, url, options)
      handle_response(response)
    rescue => e
      retries += 1
      if retries <= @retries
        sleep(0.1 * retries)  # Exponential backoff
        retry
      else
        raise CommunicationError, "Failed after #{@retries} retries: #{e.message}"
      end
    end
  end
  
  def build_url(endpoint, params)
    url = "#{@base_url}#{endpoint}"
    url += "?#{URI.encode_www_form(params)}" unless params.empty?
    url
  end
  
  def build_options(data)
    return {} unless data
    
    {
      headers: { 'Content-Type' => 'application/json' },
      body: data.to_json
    }
  end
  
  def handle_response(response)
    case response.code
    when 200..299
      response.parse
    when 400..499
      raise ClientError, "Client error: #{response.code} - #{response.body}"
    when 500..599
      raise ServerError, "Server error: #{response.code} - #{response.body}"
    else
      raise CommunicationError, "Unknown error: #{response.code}"
    end
  end
end

# Usage examples
class UserServiceClient
  def initialize
    @client = ServiceClient.new('http://user-service:3001')
  end
  
  def get_user(user_id)
    @client.get("/users/#{user_id}")
  end
  
  def create_user(user_data)
    @client.post('/users', user_data)
  end
  
  def update_user(user_id, updates)
    @client.put("/users/#{user_id}", updates)
  end
  
  def delete_user(user_id)
    @client.delete("/users/#{user_id}")
  end
end

class OrderServiceClient
  def initialize
    @client = ServiceClient.new('http://order-service:3002')
    @user_client = UserServiceClient.new
  end
  
  def create_order(user_id, items)
    # Validate user exists
    user = @user_client.get_user(user_id)
    raise UserNotFoundError unless user
    
    # Create order
    order_data = {
      user_id: user_id,
      items: items,
      total: calculate_total(items)
    }
    
    @client.post('/orders', order_data)
  end
  
  def get_order(order_id)
    @client.get("/orders/#{order_id}")
  end
  
  def update_order_status(order_id, status)
    @client.put("/orders/#{order_id}/status", { status: status })
  end
  
  private
  
  def calculate_total(items)
    items.sum { |item| item[:price] * item[:quantity] }
  end
end

# Usage in application
user_client = UserServiceClient.new
order_client = OrderServiceClient.new

# Create a new order
begin
  order = order_client.create_order(
    123,
    [
      { name: "Laptop", price: 999.99, quantity: 1 },
      { name: "Mouse", price: 29.99, quantity: 2 }
    ]
  )
  puts "Order created: #{order[:id]}"
rescue UserNotFoundError => e
  puts "Error: #{e.message}"
rescue CommunicationError => e
  puts "Communication error: #{e.message}"
end
```

### 2. GraphQL Communication

GraphQL provides more efficient data fetching between services:

```ruby
# GraphQL client for service communication
class GraphQLServiceClient
  def initialize(base_url, timeout: 5)
    @base_url = base_url
    @timeout = timeout
  end
  
  def query(query_string, variables = {})
    make_request('query', query_string, variables)
  end
  
  def mutation(mutation_string, variables = {})
    make_request('mutation', mutation_string, variables)
  end
  
  private
  
  def make_request(operation_type, query_string, variables)
    payload = {
      query: "#{operation_type} { #{query_string} }",
      variables: variables
    }
    
    response = HTTP.timeout(@timeout)
                   .post(@base_url, 
                         headers: { 'Content-Type' => 'application/json' },
                         body: payload.to_json)
    
    handle_response(response)
  end
  
  def handle_response(response)
    case response.code
    when 200..299
      data = response.parse
      if data['errors']
        raise GraphQLError, data['errors'].map { |e| e['message'] }.join(', ')
      end
      data['data']
    when 400..499
      raise ClientError, "Client error: #{response.body}"
    when 500..599
      raise ServerError, "Server error: #{response.body}"
    else
      raise CommunicationError, "Unknown error: #{response.code}"
    end
  end
end

# Usage examples
class UserGraphQLClient
  def initialize
    @client = GraphQLServiceClient.new('http://user-service:3001/graphql')
  end
  
  def get_user(user_id)
    query = <<~GQL
      user(id: $userId) {
        id
        name
        email
        createdAt
        orders {
          id
          total
          status
          createdAt
        }
      }
    GQL
    
    @client.query(query, userId: user_id)
  end
  
  def create_user(user_data)
    mutation = <<~GQL
      createUser(input: $input) {
        user {
          id
          name
          email
          createdAt
        }
      }
    GQL
    
    @client.mutation(mutation, input: user_data)
  end
end

# Usage
user_client = UserGraphQLClient.new

# Get user with their orders
user = user_client.get_user(123)
puts "User: #{user['user']['name']}"
puts "Orders: #{user['user']['orders'].length}"

# Create new user
new_user = user_client.create_user(
  name: "John Doe",
  email: "john@example.com",
  password: "securepassword"
)
puts "Created user: #{new_user['user']['id']}"
```

## 📨 Asynchronous Communication

### 1. Message Queue Implementation

Message queues enable asynchronous, decoupled communication:

```ruby
# Message queue implementation
class MessageQueue
  def initialize
    @queues = {}
    @mutex = Mutex.new
  end
  
  def publish(queue_name, message)
    @mutex.synchronize do
      @queues[queue_name] ||= []
      @queues[queue_name] << {
        id: SecureRandom.uuid,
        payload: message,
        timestamp: Time.now,
        attempts: 0
      }
    end
    
    puts "Published message to #{queue_name}: #{message[:type]}"
  end
  
  def subscribe(queue_name, &block)
    Thread.new do
      loop do
        message = receive_message(queue_name)
        if message
          begin
            block.call(message)
            acknowledge_message(queue_name, message)
          rescue => e
            handle_failed_message(queue_name, message, e)
          end
        else
          sleep(0.1)  # Wait for messages
        end
      end
    end
  end
  
  def receive_message(queue_name)
    @mutex.synchronize do
      queue = @queues[queue_name]
      return nil unless queue
      
      message = queue.shift
      message
    end
  end
  
  def acknowledge_message(queue_name, message)
    @mutex.synchronize do
      puts "Acknowledged message #{message[:id]} from #{queue_name}"
    end
  end
  
  def handle_failed_message(queue_name, message, error)
    @mutex.synchronize do
      message[:attempts] += 1
      message[:last_error] = error.message
      
      if message[:attempts] < 3
        # Retry the message
        @queues[queue_name].unshift(message)
        puts "Retrying message #{message[:id]} (attempt #{message[:attempts]})"
      else
        # Move to dead letter queue
        dead_letter_queue(queue_name, message)
      end
    end
  end
  
  def dead_letter_queue(queue_name, message)
    dead_queue_name = "#{queue_name}_dead"
    @mutex.synchronize do
      @queues[dead_queue_name] ||= []
      @queues[dead_queue_name] << message
    end
    puts "Message #{message[:id]} moved to dead letter queue"
  end
  
  def queue_stats(queue_name)
    @mutex.synchronize do
      {
        queue_size: @queues[queue_name]&.length || 0,
        dead_letter_size: @queues["#{queue_name}_dead"]&.length || 0
      }
    end
  end
end

# Event publisher
class EventPublisher
  def initialize(message_queue)
    @queue = message_queue
  end
  
  def publish_event(event_type, data, routing_key = nil)
    event = {
      id: SecureRandom.uuid,
      type: event_type,
      data: data,
      routing_key: routing_key,
      timestamp: Time.now.iso8601,
      source: self.class.name
    }
    
    queue_name = routing_key || event_type
    @queue.publish(queue_name, event)
  end
end

# Event subscriber
class EventSubscriber
  def initialize(message_queue)
    @queue = message_queue
    @handlers = {}
  end
  
  def subscribe(event_type, &handler)
    @handlers[event_type] ||= []
    @handlers[event_type] << handler
  end
  
  def start
    @handlers.each do |event_type, handlers|
      @queue.subscribe(event_type) do |message|
        handle_event(message, handlers)
      end
    end
  end
  
  private
  
  def handle_event(message, handlers)
    puts "Processing event: #{message[:payload][:type]}"
    
    handlers.each do |handler|
      begin
        handler.call(message[:payload])
      rescue => e
        puts "Handler error: #{e.message}"
        raise  # Re-raise to trigger retry logic
      end
    end
  end
end
```

### 2. Event-Driven Architecture

Implementing event-driven communication patterns:

```ruby
# Domain events
class DomainEvent
  attr_reader :id, :type, :data, :timestamp, :source
  
  def initialize(type, data, source = nil)
    @id = SecureRandom.uuid
    @type = type
    @data = data
    @timestamp = Time.now
    @source = source || self.class.name
  end
  
  def to_hash
    {
      id: @id,
      type: @type,
      data: @data,
      timestamp: @timestamp.iso8601,
      source: @source
    }
  end
end

# Specific domain events
class UserCreatedEvent < DomainEvent
  def initialize(user_data)
    super('user.created', user_data, 'UserService')
  end
end

class OrderCreatedEvent < DomainEvent
  def initialize(order_data)
    super('order.created', order_data, 'OrderService')
  end
end

class PaymentProcessedEvent < DomainEvent
  def initialize(payment_data)
    super('payment.processed', payment_data, 'PaymentService')
  end
end

# Event store
class EventStore
  def initialize
    @events = []
    @mutex = Mutex.new
  end
  
  def save_event(event)
    @mutex.synchronize do
      @events << event
    end
    puts "Stored event: #{event.type}"
  end
  
  def get_events(event_type = nil)
    @mutex.synchronize do
      if event_type
        @events.select { |e| e.type == event_type }
      else
        @events.dup
      end
    end
  end
  
  def get_events_for_aggregate(aggregate_id)
    @mutex.synchronize do
      @events.select { |e| e.data[:aggregate_id] == aggregate_id }
    end
  end
end

# Event bus
class EventBus
  def initialize
    @handlers = {}
    @mutex = Mutex.new
  end
  
  def subscribe(event_type, handler_id, &handler)
    @mutex.synchronize do
      @handlers[event_type] ||= {}
      @handlers[event_type][handler_id] = handler
    end
    puts "Subscribed handler #{handler_id} to #{event_type}"
  end
  
  def unsubscribe(event_type, handler_id)
    @mutex.synchronize do
      @handlers[event_type]&.delete(handler_id)
    end
    puts "Unsubscribed handler #{handler_id} from #{event_type}"
  end
  
  def publish(event)
    @mutex.synchronize do
      handlers = @handlers[event.type] || {}
      handlers.each do |handler_id, handler|
        Thread.new do
          begin
            handler.call(event)
          rescue => e
            puts "Handler #{handler_id} error: #{e.message}"
          end
        end
      end
    end
  end
end

# Usage in services
class UserService
  def initialize(event_store, event_bus)
    @event_store = event_store
    @event_bus = event_bus
    @users = {}
  end
  
  def create_user(user_data)
    # Validate and create user
    user_id = SecureRandom.uuid
    user = user_data.merge(id: user_id, created_at: Time.now)
    @users[user_id] = user
    
    # Create and publish event
    event = UserCreatedEvent.new(user)
    @event_store.save_event(event)
    @event_bus.publish(event)
    
    user
  end
  
  def get_user(user_id)
    @users[user_id]
  end
end

class NotificationService
  def initialize(event_bus)
    @event_bus = event_bus
    setup_handlers
  end
  
  private
  
  def setup_handlers
    @event_bus.subscribe('user.created', 'notification_handler') do |event|
      handle_user_created(event)
    end
    
    @event_bus.subscribe('order.created', 'notification_handler') do |event|
      handle_order_created(event)
    end
  end
  
  def handle_user_created(event)
    user = event.data
    puts "Sending welcome email to #{user[:email]}"
    # Email sending logic
  end
  
  def handle_order_created(event)
    order = event.data
    puts "Sending order confirmation for order #{order[:id]}"
    # Order notification logic
  end
end

# Usage
event_store = EventStore.new
event_bus = EventBus.new

user_service = UserService.new(event_store, event_bus)
notification_service = NotificationService.new(event_bus)

# Create user - triggers notification
user = user_service.create_user(
  name: "John Doe",
  email: "john@example.com",
  password: "securepassword"
)

puts "Created user: #{user[:id]}"
```

## 🔄 Communication Patterns

### 1. Circuit Breaker Pattern

Prevent cascading failures with circuit breakers:

```ruby
class CircuitBreaker
  def initialize(service_name, failure_threshold: 5, timeout: 60)
    @service_name = service_name
    @failure_threshold = failure_threshold
    @timeout = timeout
    @failure_count = 0
    @last_failure_time = nil
    @state = :closed  # :closed, :open, :half_open
    @mutex = Mutex.new
  end
  
  def call(&block)
    @mutex.synchronize do
      case @state
      when :open
        if Time.now - @last_failure_time > @timeout
          @state = :half_open
        else
          raise CircuitBreakerOpenError, "Circuit breaker is OPEN for #{@service_name}"
        end
      end
    end
    
    begin
      result = yield
      success
      result
    rescue => e
      failure
      raise e
    end
  end
  
  def state
    @mutex.synchronize { @state }
  end
  
  def stats
    @mutex.synchronize do
      {
        service: @service_name,
        state: @state,
        failure_count: @failure_count,
        last_failure_time: @last_failure_time
      }
    end
  end
  
  private
  
  def success
    @mutex.synchronize do
      @failure_count = 0
      @state = :closed if @state == :half_open
    end
  end
  
  def failure
    @mutex.synchronize do
      @failure_count += 1
      @last_failure_time = Time.now
      
      if @failure_count >= @failure_threshold
        @state = :open
      end
    end
  end
end

# Circuit breaker wrapper for service clients
class ResilientServiceClient
  def initialize(base_url, circuit_breaker_options = {})
    @client = ServiceClient.new(base_url)
    @circuit_breaker = CircuitBreaker.new(
      base_url,
      **circuit_breaker_options
    )
  end
  
  def get(endpoint, params = {})
    @circuit_breaker.call do
      @client.get(endpoint, params)
    end
  end
  
  def post(endpoint, data = {}, params = {})
    @circuit_breaker.call do
      @client.post(endpoint, data, params)
    end
  end
  
  def put(endpoint, data = {}, params = {})
    @circuit_breaker.call do
      @client.put(endpoint, data, params)
    end
  end
  
  def delete(endpoint, params = {})
    @circuit_breaker.call do
      @client.delete(endpoint, params)
    end
  end
  
  def circuit_breaker_stats
    @circuit_breaker.stats
  end
end

# Usage
user_client = ResilientServiceClient.new(
  'http://user-service:3001',
  failure_threshold: 3,
  timeout: 30
)

begin
  user = user_client.get('/users/123')
  puts "User: #{user[:name]}"
rescue CircuitBreakerOpenError => e
  puts "Circuit breaker is open: #{e.message}"
rescue CommunicationError => e
  puts "Communication error: #{e.message}"
end

puts "Circuit breaker stats: #{user_client.circuit_breaker_stats}"
```

### 2. Retry Pattern

Automatic retry for transient failures:

```ruby
class RetryPolicy
  def initialize(max_attempts: 3, base_delay: 1, max_delay: 30, backoff: :exponential)
    @max_attempts = max_attempts
    @base_delay = base_delay
    @max_delay = max_delay
    @backoff = backoff
  end
  
  def execute(&block)
    attempts = 0
    
    begin
      attempts += 1
      result = yield
      return result
    rescue => e
      if attempts < @max_attempts && retryable?(e)
        delay = calculate_delay(attempts)
        puts "Attempt #{attempts} failed, retrying in #{delay}s: #{e.message}"
        sleep(delay)
        retry
      else
        raise e
      end
    end
  end
  
  private
  
  def retryable?(error)
    # Define which errors are retryable
    retryable_errors = [
      Timeout::Error,
      Net::TimeoutError,
      CommunicationError,
      ServerError
    ]
    
    retryable_errors.any? { |error_class| error.is_a?(error_class) }
  end
  
  def calculate_delay(attempt)
    case @backoff
    when :exponential
      delay = @base_delay * (2 ** (attempt - 1))
    when :linear
      delay = @base_delay * attempt
    else
      delay = @base_delay
    end
    
    [delay, @max_delay].min
  end
end

# Usage with service client
class RetryServiceClient
  def initialize(base_url, retry_options = {})
    @client = ServiceClient.new(base_url)
    @retry_policy = RetryPolicy.new(**retry_options)
  end
  
  def get(endpoint, params = {})
    @retry_policy.execute do
      @client.get(endpoint, params)
    end
  end
  
  def post(endpoint, data = {}, params = {})
    @retry_policy.execute do
      @client.post(endpoint, data, params)
    end
  end
end

# Usage
retry_client = RetryServiceClient.new(
  'http://user-service:3001',
  max_attempts: 3,
  base_delay: 1,
  backoff: :exponential
)

begin
  user = retry_client.get('/users/123')
  puts "User: #{user[:name]}"
rescue => e
  puts "Failed after retries: #{e.message}"
end
```

## 🎯 Best Practices

### 1. Communication Guidelines

```ruby
# Communication guidelines implementation
class CommunicationGuidelines
  TIMEOUTS = {
    critical: 2,    # User authentication
    normal: 5,      # Order processing
    background: 10  # Reporting
  }.freeze
  
  RETRY_POLICIES = {
    critical: { max_attempts: 1, backoff: :none },
    normal: { max_attempts: 3, backoff: :exponential },
    background: { max_attempts: 5, backoff: :linear }
  }.freeze
  
  def self.client_for_service(service_name, priority = :normal)
    base_url = get_service_url(service_name)
    timeout = TIMEOUTS[priority]
    retry_policy = RETRY_POLICIES[priority]
    
    ResilientServiceClient.new(
      base_url,
      timeout: timeout,
      **retry_policy
    )
  end
  
  def self.get_service_url(service_name)
    # Service discovery logic
    service_registry = ServiceRegistry.new
    service_registry.get_service_url(service_name)
  end
end

# Usage
# Critical service call
auth_client = CommunicationGuidelines.client_for_service('auth-service', :critical)

# Normal service call
order_client = CommunicationGuidelines.client_for_service('order-service', :normal)

# Background service call
report_client = CommunicationGuidelines.client_for_service('report-service', :background)
```

### 2. Error Handling

```ruby
# Comprehensive error handling
class ServiceErrorHandler
  def initialize(service_name)
    @service_name = service_name
    @error_counts = Hash.new(0)
    @last_errors = []
  end
  
  def handle_error(error, context = {})
    error_type = error.class.name
    @error_counts[error_type] += 1
    
    error_info = {
      service: @service_name,
      error_type: error_type,
      message: error.message,
      context: context,
      timestamp: Time.now
    }
    
    @last_errors << error_info
    @last_errors.shift if @last_errors.length > 100
    
    log_error(error_info)
    alert_if_needed(error_type, error_info)
  end
  
  def error_stats
    {
      service: @service_name,
      error_counts: @error_counts,
      recent_errors: @last_errors.last(10)
    }
  end
  
  private
  
  def log_error(error_info)
    puts "ERROR [#{error_info[:service]}] #{error_info[:error_type]}: #{error_info[:message]}"
  end
  
  def alert_if_needed(error_type, error_info)
    # Alert on critical errors or high error rates
    if error_type == 'ServerError' || @error_counts[error_type] > 10
      send_alert(error_info)
    end
  end
  
  def send_alert(error_info)
    puts "ALERT: High error rate for #{error_info[:service]} - #{error_info[:error_type]}"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **REST Client**: Create a REST client for service communication
2. **Message Queue**: Implement a simple message queue
3. **Event Publisher**: Create an event publishing system

### Intermediate Exercises

1. **GraphQL Client**: Implement GraphQL communication
2. **Circuit Breaker**: Add circuit breaker pattern
3. **Retry Logic**: Implement retry with backoff

### Advanced Exercises

1. **Event Store**: Build an event store for event sourcing
2. **Service Mesh**: Create a service mesh for communication
3. **Observability**: Add metrics and tracing to communication

---

## 🎯 Summary

Service communication patterns provide:

- **Synchronous Communication** - Direct API calls and GraphQL
- **Asynchronous Communication** - Message queues and events
- **Resilience Patterns** - Circuit breakers and retries
- **Error Handling** - Comprehensive error management
- **Performance Optimization** - Efficient data transfer

Master these patterns to build robust, scalable microservices communication!
