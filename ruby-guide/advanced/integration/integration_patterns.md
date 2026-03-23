# Integration Patterns in Ruby

## Overview

Integration patterns focus on connecting different systems, services, and components effectively. This guide covers APIs, messaging, event-driven architecture, microservices, and system integration strategies.

## API Integration

### REST API Client

```ruby
require 'net/http'
require 'json'
require 'uri'

class APIClient
  def initialize(base_url, options = {})
    @base_url = URI(base_url)
    @http = Net::HTTP.new(@base_url.host, @base_url.port)
    @http.use_ssl = @base_url.scheme == 'https'
    @http.read_timeout = options[:timeout] || 30
    @http.open_timeout = options[:timeout] || 10
    
    @headers = {
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
      'User-Agent' => 'Ruby API Client 1.0'
    }.merge(options[:headers] || {})
    
    @rate_limiter = RateLimiter.new(options[:rate_limit] || 100)
    @retry_handler = RetryHandler.new(options[:max_retries] || 3)
  end
  
  def get(path, params = {})
    uri = build_uri(path, params)
    request = Net::HTTP::Get.new(uri)
    add_headers(request)
    
    execute_request(request)
  end
  
  def post(path, data = {})
    uri = build_uri(path)
    request = Net::HTTP::Post.new(uri)
    add_headers(request)
    request.body = data.to_json
    
    execute_request(request)
  end
  
  def put(path, data = {})
    uri = build_uri(path)
    request = Net::HTTP::Put.new(uri)
    add_headers(request)
    request.body = data.to_json
    
    execute_request(request)
  end
  
  def delete(path)
    uri = build_uri(path)
    request = Net::HTTP::Delete.new(uri)
    add_headers(request)
    
    execute_request(request)
  end
  
  def batch_requests(requests)
    threads = []
    results = []
    mutex = Mutex.new
    
    requests.each do |request|
      threads << Thread.new do
        result = execute_request(request[:method], request[:path], request[:data])
        mutex.synchronize { results << result }
      end
    end
    
    threads.each(&:join)
    results
  end
  
  private
  
  def build_uri(path, params = {})
    uri = URI("#{@base_url}#{path}")
    uri.query = URI.encode_www_form(params) unless params.empty?
    uri
  end
  
  def add_headers(request)
    @headers.each { |key, value| request[key] = value }
  end
  
  def execute_request(request)
    @rate_limiter.wait
    
    @retry_handler.with_retry do
      response = @http.request(request)
      
      case response
      when Net::HTTPSuccess
        parse_response(response)
      when Net::HTTPUnauthorized
        raise AuthenticationError, "Authentication failed"
      when Net::HTTPForbidden
        raise AuthorizationError, "Access forbidden"
      when Net::HTTPNotFound
        raise NotFoundError, "Resource not found"
      when Net::HTTPUnprocessableEntity
        raise ValidationError, "Invalid request data"
      when Net::HTTPTooManyRequests
        raise RateLimitError, "Rate limit exceeded"
      else
        raise APIError, "HTTP #{response.code}: #{response.message}"
      end
    end
  end
  
  def parse_response(response)
    {
      status: response.code.to_i,
      headers: response.to_hash,
      body: response.body.empty? ? {} : JSON.parse(response.body),
      success: response.is_a?(Net::HTTPSuccess)
    }
  rescue JSON::ParserError
    {
      status: response.code.to_i,
      headers: response.to_hash,
      body: response.body,
      success: false
    }
  end
end

# Usage
client = APIClient.new('https://jsonplaceholder.typicode.com')

# Single request
response = client.get('/posts/1')
puts "Post: #{response[:body]['title']}"

# Batch requests
requests = [
  { method: :get, path: '/posts/1' },
  { method: :get, path: '/posts/2' },
  { method: :get, path: '/posts/3' }
]

results = client.batch_requests(requests)
puts "Batch results: #{results.length}"
```

### Rate Limiting

```ruby
class RateLimiter
  def initialize(requests_per_second)
    @requests_per_second = requests_per_second
    @tokens = requests_per_second
    @last_refill = Time.now
    @mutex = Mutex.new
  end
  
  def wait
    @mutex.synchronize do
      refill_tokens
      
      while @tokens <= 0
        sleep_time = 1.0 / @requests_per_second
        sleep(sleep_time)
        refill_tokens
      end
      
      @tokens -= 1
    end
  end
  
  private
  
  def refill_tokens
    now = Time.now
    elapsed = now - @last_refill
    tokens_to_add = (elapsed * @requests_per_second).to_i
    
    @tokens = [@tokens + tokens_to_add, @requests_per_second].min
    @last_refill = now
  end
end

# Usage
rate_limiter = RateLimiter.new(10)

10.times do |i|
  rate_limiter.wait
  puts "Request #{i + 1} at #{Time.now.strftime('%H:%M:%S')}"
end
```

### Retry Handler

```ruby
class RetryHandler
  def initialize(max_retries = 3, backoff_factor = 2)
    @max_retries = max_retries
    @backoff_factor = backoff_factor
  end
  
  def with_retry(&block)
    retries = 0
    
    begin
      yield
    rescue => e
      if retries < @max_retries && retryable_error?(e)
        retries += 1
        delay = @backoff_factor ** retries
        sleep(delay)
        retry
      else
        raise e
      end
    end
  end
  
  private
  
  def retryable_error?(error)
    error.is_a?(Net::TimeoutError) ||
    error.is_a?(Net::ReadTimeout) ||
    error.is_a?(Net::OpenTimeout) ||
    error.is_a?(RateLimitError)
  end
end

# Usage
retry_handler = RetryHandler.new(3)

retry_handler.with_retry do
  # Make API call
  puts "Attempting request..."
  raise Net::TimeoutError if rand < 0.7
  puts "Request succeeded!"
end
```

## Message Queuing

### Message Queue Implementation

```ruby
class MessageQueue
  def initialize
    @queue = Queue.new
    @subscribers = []
    @mutex = Mutex.new
  end
  
  def publish(message)
    @mutex.synchronize do
      @queue.push({
        id: SecureRandom.uuid,
        message: message,
        timestamp: Time.now,
        attempts: 0
      })
      
      notify_subscribers(message)
    end
  end
  
  def subscribe(&block)
    @mutex.synchronize do
      @subscribers << block
    end
  end
  
  def consume
    message = @queue.pop
    return nil unless message
    
    @mutex.synchronize do
      message[:attempts] += 1
    end
    
    message
  end
  
  def size
    @queue.size
  end
  
  def empty?
    @queue.empty?
  end
  
  private
  
  def notify_subscribers(message)
    @subscribers.each { |subscriber| subscriber.call(message) }
  end
end

# Usage
queue = MessageQueue.new

# Subscribe to messages
queue.subscribe do |message|
  puts "Received: #{message[:message]} (#{message[:id]})"
end

# Publish messages
queue.publish("Hello, World!")
queue.publish("Another message")

# Consume messages
message = queue.consume
puts "Consumed: #{message[:message]}" if message
```

### Dead Letter Queue

```ruby
class DeadLetterQueue
  def initialize(max_retries = 3)
    @queue = Queue.new
    @max_retries = max_retries
  end
  
  def add_message(message)
    @queue.push(message)
  end
  
  def get_messages
    messages = []
    
    while !@queue.empty?
      messages << @queue.pop
    end
    
    messages
  end
  
  def should_retry?(message)
    message[:attempts] < @max_retries
  end
  
  def size
    @queue.size
  end
end

class MessageProcessor
  def initialize(queue, dead_letter_queue)
    @queue = queue
    @dead_letter_queue = dead_letter_queue
  end
  
  def process_messages
    while !@queue.empty?
      message = @queue.consume
      next unless message
      
      begin
        process_message(message)
        puts "Processed message: #{message[:id]}"
      rescue => e
        puts "Failed to process message: #{e.message}"
        
        if @dead_letter_queue.should_retry?(message)
          @queue.publish(message[:message])
        else
          @dead_letter_queue.add_message(message)
          puts "Moved to dead letter queue: #{message[:id]}"
        end
      end
    end
  end
  
  private
  
  def process_message(message)
    # Simulate processing
    raise "Processing error" if rand < 0.3
    puts "Successfully processed: #{message[:message]}"
  end
end

# Usage
queue = MessageQueue.new
dead_letter_queue = DeadLetterQueue.new
processor = MessageProcessor.new(queue, dead_letter_queue)

# Add messages to queue
5.times { |i| queue.publish("Message #{i}") }

# Process messages
processor.process_messages

# Check dead letter queue
puts "Dead letter queue size: #{dead_letter_queue.size}"
```

## Event-Driven Architecture

### Event Bus

```ruby
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
  
  def unsubscribe(event_type, handler)
    @mutex.synchronize do
      @handlers[event_type].delete(handler)
    end
  end
  
  def publish(event)
    @mutex.synchronize do
      handlers = @handlers[event.type] || []
      
      handlers.each do |handler|
        begin
          handler.call(event)
        rescue => e
          puts "Error in event handler: #{e.message}"
        end
      end
    end
  end
  
  def publish_async(event)
    Thread.new { publish(event) }
  end
end

class Event
  attr_reader :type, :data, :timestamp
  
  def initialize(type, data = {})
    @type = type
    @data = data
    @timestamp = Time.now
  end
end

# Usage
event_bus = EventBus.new

# Subscribe to events
event_bus.subscribe(:user_created) do |event|
  user = event.data
  puts "User created: #{user[:name]} (#{user[:id]})"
end

event_bus.subscribe(:order_placed) do |event|
  order = event.data
  puts "Order placed: #{order[:id]} for #{order[:amount]}"
end

# Publish events
event_bus.publish(Event.new(:user_created, { id: 1, name: "John" }))
event_bus.publish(Event.new(:order_placed, { id: 1, amount: 99.99 }))
```

### Event Sourcing

```ruby
class EventStore
  def initialize
    @events = []
    @mutex = Mutex.new
  end
  
  def save_event(event)
    @mutex.synchronize do
      @events << {
        id: SecureRandom.uuid,
        type: event.type,
        data: event.data,
        timestamp: event.timestamp,
        sequence_number: @events.length + 1
      }
    end
  end
  
  def get_events(aggregate_id = nil)
    @mutex.synchronize do
      if aggregate_id
        @events.select { |e| e[:data][:aggregate_id] == aggregate_id }
      else
        @events.dup
      end
    end
  end
  
  def replay_events(aggregate_id)
    events = get_events(aggregate_id)
    
    events.reduce(nil) do |state, event|
      apply_event(state, event)
    end
  end
  
  private
  
  def apply_event(state, event)
    case event[:type]
    when :user_created
      UserCreatedEvent.new(event[:data])
    when :user_updated
      UserUpdatedEvent.new(event[:data])
    else
      state
    end
  end
end

class UserCreatedEvent
  attr_reader :id, :name, :email
  
  def initialize(data)
    @id = data[:id]
    @name = data[:name]
    @email = data[:email]
  end
end

# Usage
event_store = EventStore.new

# Save events
event_store.save_event(Event.new(:user_created, { id: 1, name: "John", email: "john@example.com" }))
event_store.save_event(Event.new(:user_updated, { id: 1, name: "John Updated" }))

# Replay events
state = event_store.replay_events(1)
puts "Final state: #{state.inspect}"
```

## Microservices Communication

### Service Registry

```ruby
class ServiceRegistry
  def initialize
    @services = {}
    @mutex = Mutex.new
  end
  
  def register(name, host, port, health_check_path = '/health')
    @mutex.synchronize do
      @services[name] = {
        host: host,
        port: port,
        health_check_path: health_check_path,
        healthy: true,
        last_check: Time.now
      }
    end
  end
  
  def unregister(name)
    @mutex.synchronize do
      @services.delete(name)
    end
  end
  
  def get_service(name)
    @mutex.synchronize do
      service = @services[name]
      return nil unless service
      
      if !service[:healthy] || Time.now - service[:last_check] > 30
        service[:healthy] = health_check(service)
        service[:last_check] = Time.now
      end
      
      service[:healthy] ? service : nil
    end
  end
  
  def get_all_services
    @mutex.synchronize do
      @services.dup
    end
  end
  
  def health_check_all
    @mutex.synchronize do
      @services.each do |name, service|
        service[:healthy] = health_check(service)
        service[:last_check] = Time.now
      end
    end
  end
  
  private
  
  def health_check(service)
    begin
      uri = URI("http://#{service[:host]}:#{service[:port]}#{service[:health_check_path]}")
      
      response = Net::HTTP.get_response(uri)
      response.is_a?(Net::HTTPSuccess)
    rescue
      false
    end
  end
end

# Usage
registry = ServiceRegistry.new

# Register services
registry.register('user-service', 'localhost', 3001)
registry.register('order-service', 'localhost', 3002)
registry.register('payment-service', 'localhost', 3003)

# Get service
user_service = registry.get_service('user-service')
puts "User service: #{user_service ? 'healthy' : 'unhealthy'}"

# Get all services
services = registry.get_all_services
puts "All services: #{services.keys.join(', ')}"
```

### Service Mesh

```ruby
class ServiceMesh
  def initialize
    @services = {}
    @interceptors = []
    @mutex = Mutex.new
  end
  
  def add_service(name, service)
    @mutex.synchronize do
      @services[name] = service
    end
  end
  
  def add_interceptor(&block)
    @interceptors << block
  end
  
  def call(service_name, method, *args)
    service = @services[service_name]
    raise "Service #{service_name} not found" unless service
    
    context = {
      service_name: service_name,
      method: method,
      args: args,
      start_time: Time.now
    }
    
    # Apply interceptors
    @interceptors.each do |interceptor|
      interceptor.call(context, :before)
    end
    
    begin
      result = service.send(method, *args)
      context[:result] = result
      context[:success] = true
    rescue => e
      context[:error] = e
      context[:success] = false
      raise
    ensure
      context[:end_time] = Time.now
      context[:duration] = context[:end_time] - context[:start_time]
      
      # Apply interceptors
      @interceptors.each do |interceptor|
        interceptor.call(context, :after)
      end
    end
    
    context[:result]
  end
  
  def call_async(service_name, method, *args, &block)
    Thread.new do
      result = call(service_name, method, *args)
      block.call(result) if block
    end
  end
end

# Usage
class UserService
  def create_user(data)
    { id: 1, name: data[:name], email: data[:email] }
  end
  
  def get_user(id)
    { id: id, name: "John", email: "john@example.com" }
  end
end

mesh = ServiceMesh.new
mesh.add_service('user-service', UserService.new)

# Add logging interceptor
mesh.add_interceptor do |context, phase|
  if phase == :before
    puts "Calling #{context[:service_name]}.#{context[:method]}"
  elsif phase == :after
    status = context[:success] ? "✅" : "❌"
    puts "#{status} #{context[:service_name]}.#{context[:method]} (#{context[:duration].round(3)}s)"
  end
end

# Add metrics interceptor
mesh.add_interceptor do |context, phase|
  if phase == :after
    # Record metrics
    puts "Metrics: #{context[:service_name]}.#{context[:method]} took #{context[:duration].round(3)}s"
  end
end

# Call service
result = mesh.call('user-service', :create_user, { name: "John", email: "john@example.com" })
puts "Result: #{result}"

# Async call
mesh.call_async('user-service', :get_user, 1) do |result|
  puts "Async result: #{result}"
end
```

## Data Integration

### ETL Pipeline

```ruby
class ETLProcessor
  def initialize
    @extractors = []
    @transformers = []
    @loaders = []
  end
  
  def add_extractor(&block)
    @extractors << block
  end
  
  def add_transformer(&block)
    @transformers << block
  end
  
  def add_loader(&block)
    @loaders << block
  end
  
  def process
    puts "Starting ETL process..."
    
    # Extract
    puts "Extract phase..."
    extracted_data = []
    
    @extractors.each do |extractor|
      data = extractor.call
      extracted_data.concat(Array(data))
    end
    
    puts "Extracted #{extracted_data.length} records"
    
    # Transform
    puts "Transform phase..."
    transformed_data = extracted_data
    
    @transformers.each do |transformer|
      transformed_data = transformed_data.map(&transformer)
    end
    
    puts "Transformed #{transformed_data.length} records"
    
    # Load
    puts "Load phase..."
    @loaders.each do |loader|
      loader.call(transformed_data)
    end
    
    puts "ETL process completed"
  end
end

# Usage
etl = ETLProcessor.new

# Add extractor
etl.add_extractor do
  # Simulate database extraction
  [
    { id: 1, name: "John", age: 30, city: "New York" },
    { id: 2, name: "Jane", age: 25, city: "Boston" },
    { id: 3, name: "Bob", age: 35, city: "Chicago" }
  ]
end

# Add transformer
etl.add_transformer do |record|
  # Transform data
  {
    id: record[:id],
    full_name: record[:name],
    age: record[:age],
    location: record[:city],
    age_group: record[:age] < 30 ? "Young" : "Adult"
  }
end

# Add loader
etl.add_loader do |data|
  # Simulate loading to database
  puts "Loading #{data.length} records to database"
  data.each { |record| puts "  #{record[:full_name]} (#{record[:age_group]})" }
end

# Run ETL process
etl.process
```

### Data Synchronization

```ruby
class DataSynchronizer
  def initialize
    @sources = {}
    @targets = {}
    @conflict_resolver = nil
  end
  
  def add_source(name, &block)
    @sources[name] = block
  end
  
  def add_target(name, &block)
    @targets[name] = block
  end
  
  def set_conflict_resolver(&block)
    @conflict_resolver = block
  end
  
  def synchronize(source_name, target_name)
    puts "Synchronizing from #{source_name} to #{target_name}..."
    
    source_data = @sources[source_name].call
    target_data = @targets[target_name].call
    
    # Find differences
    additions, modifications, deletions = find_differences(source_data, target_data)
    
    puts "Found #{additions.length} additions, #{modifications.length} modifications, #{deletions.length} deletions"
    
    # Apply changes
    apply_changes(target_name, additions, modifications, deletions)
    
    puts "Synchronization completed"
  end
  
  def bidirectional_sync(source_name, target_name)
    puts "Bidirectional synchronization between #{source_name} and #{target_name}..."
    
    source_data = @sources[source_name].call
    target_data = @targets[target_name].call
    
    # Find conflicts
    conflicts = find_conflicts(source_data, target_data)
    
    if conflicts.any?
      puts "Found #{conflicts.length} conflicts"
      resolved_conflicts = resolve_conflicts(conflicts)
    else
      resolved_conflicts = {}
    end
    
    # Merge data
    merged_data = merge_data(source_data, target_data, resolved_conflicts)
    
    # Update both sides
    @targets[target_name].call(merged_data)
    
    puts "Bidirectional synchronization completed"
  end
  
  private
  
  def find_differences(source_data, target_data)
    source_ids = source_data.map { |item| item[:id] }.to_set
    target_ids = target_data.map { |item| item[:id] }.to_set
    
    additions = source_data.reject { |item| target_ids.include?(item[:id]) }
    modifications = source_data.select { |item| target_ids.include?(item[:id]) }
    deletions = target_data.reject { |item| source_ids.include?(item[:id]) }
    
    [additions, modifications, deletions]
  end
  
  def find_conflicts(source_data, target_data)
    common_ids = source_data.map { |item| item[:id] }.to_set & 
                  target_data.map { |item| item[:id] }.to_set
    
    conflicts = []
    
    common_ids.each do |id|
      source_item = source_data.find { |item| item[:id] == id }
      target_item = target_data.find { |item| item[:id] == id }
      
      if source_item != target_item
        conflicts << {
          id: id,
          source: source_item,
          target: target_item
        }
      end
    end
    
    conflicts
  end
  
  def resolve_conflicts(conflicts)
    return {} unless @conflict_resolver
    
    resolved = {}
    
    conflicts.each do |conflict|
      resolution = @conflict_resolver.call(conflict)
      resolved[conflict[:id]] = resolution
    end
    
    resolved
  end
  
  def merge_data(source_data, target_data, resolved_conflicts)
    all_ids = (source_data.map { |item| item[:id] } + 
               target_data.map { |item| item[:id] }).uniq
    
    merged = []
    
    all_ids.each do |id|
      if resolved_conflicts.key?(id)
        # Use resolved conflict
        merged << resolved_conflicts[id]
      elsif source_item = source_data.find { |item| item[:id] == id }
        merged << source_item
      else
        merged << target_data.find { |item| item[:id] == id }
      end
    end
    
    merged
  end
  
  def apply_changes(target_name, additions, modifications, deletions)
    target_loader = @targets[target_name]
    
    # Add new records
    additions.each { |record| target_loader.call(record, :create) }
    
    # Modify existing records
    modifications.each { |record| target_loader.call(record, :update) }
    
    # Delete records
    deletions.each { |record| target_loader.call(record, :delete) }
  end
end

# Usage
sync = DataSynchronizer.new

# Add source (database)
sync.add_source(:database) do
  [
    { id: 1, name: "John", email: "john@old.com" },
    { id: 2, name: "Jane", email: "jane@example.com" },
    { id: 3, name: "Bob", email: "bob@example.com" }
  ]
end

# Add target (API)
sync.add_target(:api) do
  data = [
    { id: 1, name: "John", email: "john@example.com" },  # Modified
    { id: 2, name: "Jane", email: "jane@example.com" }
  ]
  
  # Simulate CRUD operations
  lambda do |record, operation|
    case operation
    when :create
      puts "  Creating: #{record[:name]}"
      data << record
    when :update
      existing = data.find { |item| item[:id] == record[:id] }
      existing.merge!(record) if existing
      puts "  Updating: #{record[:name]}"
    when :delete
      data.delete_if { |item| item[:id] == record[:id] }
      puts "  Deleting: #{record[:name]}"
    end
  end
end

# Set conflict resolver
sync.set_conflict_resolver do |conflict|
  # Prefer source data but use target email
  conflict[:source].merge(email: conflict[:target][:email])
end

# Synchronize
sync.synchronize(:database, :api)
```

## Integration Testing

### Contract Testing

```ruby
class ContractTest
  def initialize(consumer_name, provider_name)
    @consumer_name = consumer_name
    @provider_name = provider_name
    @expectations = []
  end
  
  def expect_request(method, path, &block)
    @expectations << {
      type: :request,
      method: method,
      path: path,
      block: block
    }
  end
  
  def expect_response(status_code, &block)
    @expectations << {
      type: :response,
      status_code: status_code,
      block: block
    }
  end
  
  def verify(interactions)
    puts "Verifying contract between #{@consumer_name} and #{@provider_name}"
    
    @expectations.each do |expectation|
      interaction = find_interaction(interactions, expectation)
      
      if interaction
        if expectation[:type] == :request
          verify_request(interaction, expectation)
        else
          verify_response(interaction, expectation)
        end
      else
        puts "❌ Expected #{expectation[:type]} not found"
        return false
      end
    end
    
    puts "✅ All expectations verified"
    true
  end
  
  private
  
  def find_interaction(interactions, expectation)
    interactions.find do |interaction|
      case expectation[:type]
      when :request
        interaction[:method] == expectation[:method] &&
        interaction[:path] == expectation[:path]
      when :response
        interaction[:status_code] == expectation[:status_code]
      end
    end
  end
  
  def verify_request(interaction, expectation)
    expectation[:block].call(interaction) if expectation[:block]
    puts "✅ Request verified: #{interaction[:method]} #{interaction[:path]}"
  end
  
  def verify_response(interaction, expectation)
    expectation[:block].call(interaction) if expectation[:block]
    puts "✅ Response verified: #{interaction[:status_code]}"
  end
end

# Usage
contract_test = ContractTest.new('API Consumer', 'API Provider')

contract_test.expect_request(:get, '/users') do |request|
  request[:headers]['Authorization'].should_not be_nil
end

contract_test.expect_response(200) do |response|
  response[:body].should be_a(Array)
end

# Verify with mock interactions
interactions = [
  { method: :get, path: '/users', headers: { 'Authorization' => 'Bearer token' }, status_code: 200, body: [{ id: 1, name: 'John' }] }
]

contract_test.verify(interactions)
```

## Best Practices

### 1. Error Handling

```ruby
class IntegrationError < StandardError
  attr_reader :service, :operation, :details
  
  def initialize(service, operation, message, details = {})
    @service = service
    @operation = operation
    @details = details
    super("#{service} - #{operation}: #{message}")
  end
end

class APIError < IntegrationError; end
class DatabaseError < IntegrationError; end
class MessageQueueError < IntegrationError; end
```

### 2. Circuit Breaker Pattern

```ruby
class CircuitBreaker
  def initialize(service_name, failure_threshold = 5, timeout = 60)
    @service_name = service_name
    @failure_threshold = failure_threshold
    @timeout = timeout
    @failure_count = 0
    @last_failure_time = nil
    @state = :closed
  end
  
  def call(&block)
    case @state
    when :open
      execute_with_circuit_breaker(&block)
    when :closed
      if Time.now - @last_failure_time > @timeout
        @state = :half_open
        execute_with_circuit_breaker(&block)
      else
        raise CircuitBreakerError, "Circuit breaker is open for #{@service_name}"
      end
    when :half_open
      execute_with_circuit_breaker(&block)
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
    @state = @open
  end
end

class CircuitBreakerError < StandardError; end
```

### 3. Monitoring and Logging

```ruby
class IntegrationLogger
  def initialize
    @logger = Logger.new(STDOUT)
    @metrics = {}
  end
  
  def log_request(service, operation, request)
    @logger.info("[#{service}] #{operation}: #{request[:method]} #{request[:path]}")
    
    increment_metric(service, operation, :requests)
  end
  
  def log_response(service, operation, response)
    status = response[:status] || response[:status_code]
    @logger.info("[#{service}] #{operation}: #{status}")
    
    increment_metric(service, operation, :responses)
    
    if status >= 400
      increment_metric(service, operation, :errors)
    end
  end
  
  def log_error(service, operation, error)
    @logger.error("[#{service}] #{operation}: #{error.message}")
    increment_metric(service, operation, :exceptions)
  end
  
  def get_metrics
    @metrics
  end
  
  private
  
  def increment_metric(service, operation, metric)
    key = "#{service}.#{operation}.#{metric}"
    @metrics[key] = (@metrics[key] || 0) + 1
  end
end
```

## Practice Exercises

### Exercise 1: API Gateway
Create an API gateway with:
- Request routing
- Authentication middleware
- Rate limiting
- Request/response transformation

### Exercise 2: Message Broker
Build a message broker with:
- Topic-based routing
- Message persistence
- Consumer groups
- Message acknowledgment

### Exercise 3: Service Discovery
Implement service discovery with:
- Health checking
- Load balancing
- Service registration/deregistration
- Failover handling

### Exercise 4: Data Pipeline
Create a data pipeline with:
- Multiple data sources
- Data transformation
- Validation and cleansing
- Multiple output targets

---

**Ready to explore more advanced Ruby topics? Let's continue! 🔗**
