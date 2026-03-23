# Integration Patterns Examples
# Demonstrating API integration, messaging, event-driven architecture, and microservices

puts "=== API INTEGRATION ==="

# Simple HTTP Client with Error Handling
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
    begin
      response = @http.request(request)
      
      case response
      when Net::HTTPSuccess
        {
          status: response.code.to_i,
          headers: response.to_hash,
          body: response.body.empty? ? {} : JSON.parse(response.body),
          success: true
        }
      when Net::HTTPNotFound
        {
          status: 404,
          headers: response.to_hash,
          body: { error: "Resource not found" },
          success: false
        }
      else
        {
          status: response.code.to_i,
          headers: response.to_hash,
          body: { error: response.message },
          success: false
        }
      end
    rescue => e
      {
        status: 0,
        headers: {},
        body: { error: e.message },
        success: false
      }
    end
  end
end

puts "API Client Example:"

# Mock API responses for demonstration
class MockAPIClient < APIClient
  def initialize
    super('https://jsonplaceholder.typicode.com')
  end
  
  def get(path, params = {})
    # Simulate API responses
    case path
    when '/posts/1'
      {
        status: 200,
        headers: {},
        body: { id: 1, title: "Sample Post", body: "Sample content" },
        success: true
      }
    when '/posts'
      {
        status: 200,
        headers: {},
        body: [
          { id: 1, title: "Post 1", body: "Content 1" },
          { id: 2, title: "Post 2", body: "Content 2" }
        ],
        success: true
      }
    else
      {
        status: 404,
        headers: {},
        body: { error: "Not found" },
        success: false
      }
    end
  end
  
  def post(path, data = {})
    {
      status: 201,
      headers: {},
      body: data.merge(id: rand(100..999)),
      success: true
    }
  end
end

client = MockAPIClient.new

# Single request
response = client.get('/posts/1')
puts "Get post 1: #{response[:success] ? 'Success' : 'Failed'}"
puts "Title: #{response[:body]['title']}" if response[:success]

# Batch requests
requests = [
  { method: :get, path: '/posts/1' },
  { method: :get, path: '/posts/2' }
]

results = client.batch_requests(requests)
puts "Batch requests: #{results.length} results"

# REST API Wrapper
class UserAPIClient
  def initialize(base_url)
    @client = APIClient.new(base_url)
  end
  
  def get_user(id)
    response = @client.get("/users/#{id}")
    return nil unless response[:success]
    
    User.new(response[:body])
  end
  
  def create_user(user_data)
    response = @client.post('/users', user_data)
    return nil unless response[:success]
    
    User.new(response[:body])
  end
  
  def update_user(id, user_data)
    response = @client.put("/users/#{id}", user_data)
    return nil unless response[:success]
    
    User.new(response[:body])
  end
  
  def delete_user(id)
    response = @client.delete("/users/#{id}")
    response[:success]
  end
  
  def list_users
    response = @client.get('/users')
    return [] unless response[:success]
    
    response[:body].map { |user_data| User.new(user_data) }
  end
end

class User
  attr_accessor :id, :name, :email, :username
  
  def initialize(data = {})
    @id = data['id']
    @name = data['name']
    @email = data['email']
    @username = data['username']
  end
  
  def to_h
    {
      id: @id,
      name: @name,
      email: @email,
      username: @username
    }
  end
end

puts "\nREST API Wrapper Example:"
user_client = UserAPIClient.new('https://jsonplaceholder.typicode.com')

# Mock responses for demonstration
class MockUserAPIClient < UserAPIClient
  def get_user(id)
    User.new({
      'id' => id,
      'name' => "User #{id}",
      'email' => "user#{id}@example.com",
      'username' => "user#{id}"
    })
  end
  
  def create_user(user_data)
    User.new(user_data.merge('id' => rand(100..999)))
  end
  
  def list_users
    [
      User.new({ 'id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'username' => 'johndoe' }),
      User.new({ 'id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'username' => 'janesmith' })
    ]
  end
end

user_client = MockUserAPIClient.new

user = user_client.get_user(1)
puts "Get user: #{user.name}" if user

new_user = user_client.create_user({ name: "Alice", email: "alice@example.com", username: "alice" })
puts "Create user: #{new_user.name}" if new_user

users = user_client.list_users
puts "List users: #{users.length} users"
users.each { |u| puts "  - #{u.name} (#{u.email})" }

puts "\n=== MESSAGE QUEUING ==="

# Simple Message Queue Implementation
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

# Dead Letter Queue
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

# Message Processor with Error Handling
class MessageProcessor
  def initialize(queue, dead_letter_queue)
    @queue = queue
    @dead_letter_queue = dead_letter_queue
  end
  
  def process_messages
    processed = 0
    failed = 0
    
    while !@queue.empty?
      message = @queue.consume
      next unless message
      
      begin
        process_message(message)
        processed += 1
        puts "✅ Processed message: #{message[:id]}"
      rescue => e
        failed += 1
        puts "❌ Failed to process message: #{e.message}"
        
        if @dead_letter_queue.should_retry?(message)
          @queue.publish(message[:message])
        else
          @dead_letter_queue.add_message(message)
          puts "📦 Moved to dead letter queue: #{message[:id]}"
        end
      end
    end
    
    { processed: processed, failed: failed }
  end
  
  private
  
  def process_message(message)
    # Simulate processing with potential failure
    raise "Processing error" if rand < 0.3
    puts "Successfully processed: #{message[:message]}"
  end
end

puts "Message Queue Example:"

queue = MessageQueue.new
dead_letter_queue = DeadLetterQueue.new
processor = MessageProcessor.new(queue, dead_letter_queue)

# Subscribe to messages
queue.subscribe do |message|
  puts "📨 Received: #{message[:message]} (#{message[:id]})"
end

# Publish messages
5.times { |i| queue.publish("Message #{i}") }

# Process messages
results = processor.process_messages
puts "Processing results: #{results[:processed]} processed, #{results[:failed]} failed"

puts "\n=== EVENT-DRIVEN ARCHITECTURE ==="

# Event Bus Implementation
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

# Event Classes
class Event
  attr_reader :type, :data, :timestamp
  
  def initialize(type, data = {})
    @type = type
    @data = data
    @timestamp = Time.now
  end
end

class UserCreatedEvent < Event
  def initialize(user_data)
    super(:user_created, user_data)
  end
end

class OrderPlacedEvent < Event
  def initialize(order_data)
    super(:order_placed, order_data)
  end
end

puts "Event Bus Example:"

event_bus = EventBus.new

# Subscribe to events
event_bus.subscribe(:user_created) do |event|
  user = event.data
  puts "👤 User created: #{user[:name]} (#{user[:id]})"
end

event_bus.subscribe(:order_placed) do |event|
  order = event.data
  puts "🛒 Order placed: #{order[:id]} for $#{order[:amount]}"
end

# Publish events
event_bus.publish(UserCreatedEvent.new({ id: 1, name: "John", email: "john@example.com" }))
event_bus.publish(OrderPlacedEvent.new({ id: 1, amount: 99.99 }))

# Event Sourcing
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
      UserState.new(event[:data])
    when :user_updated
      state.update(event[:data])
    else
      state
    end
  end
end

class UserState
  attr_reader :id, :name, :email
  
  def initialize(data)
    @id = data[:id]
    @name = data[:name]
    @email = data[:email]
  end
  
  def update(data)
    @name = data[:name] if data[:name]
    @email = data[:email] if data[:email]
    self
  end
end

puts "\nEvent Sourcing Example:"

event_store = EventStore.new

# Save events
event_store.save_event(UserCreatedEvent.new({ id: 1, name: "John", email: "john@example.com", aggregate_id: 1 }))
event_store.save_event(Event.new(:user_updated, { id: 1, name: "John Updated", aggregate_id: 1 }))

# Replay events
state = event_store.replay_events(1)
puts "Final state: #{state.name} (#{state.email})" if state

puts "\n=== MICROSERVICES COMMUNICATION ==="

# Service Registry
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
  
  private
  
  def health_check(service)
    # Simulate health check
    rand > 0.1  # 90% success rate
  end
end

# Service Mesh
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
end

puts "Service Registry Example:"

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

puts "\nService Mesh Example:"

# Mock services
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

# Call service
result = mesh.call('user-service', :create_user, { name: "John", email: "john@example.com" })
puts "Result: #{result}"

puts "\n=== DATA INTEGRATION ==="

# ETL Pipeline
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
    transformed_data
  end
end

# Data Synchronization
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
  
  private
  
  def find_differences(source_data, target_data)
    source_ids = source_data.map { |item| item[:id] }.to_set
    target_ids = target_data.map { |item| item[:id] }.to_set
    
    additions = source_data.reject { |item| target_ids.include?(item[:id]) }
    modifications = source_data.select { |item| target_ids.include?(item[:id]) }
    deletions = target_data.reject { |item| source_ids.include?(item[:id]) }
    
    [additions, modifications, deletions]
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

puts "ETL Pipeline Example:"

etl = ETLProcessor.new

# Add extractor
etl.add_extractor do
  [
    { id: 1, name: "John", age: 30, city: "New York" },
    { id: 2, name: "Jane", age: 25, city: "Boston" },
    { id: 3, name: "Bob", age: 35, city: "Chicago" }
  ]
end

# Add transformer
etl.add_transformer do |record|
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
  puts "Loading #{data.length} records to database"
  data.each { |record| puts "  #{record[:full_name]} (#{record[:age_group]})" }
end

# Run ETL process
result = etl.process

puts "\nData Synchronization Example:"

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

puts "\n=== INTEGRATION PATTERNS SUMMARY ==="
puts "- API Integration: REST clients, error handling, batch requests"
puts "- Message Queuing: Pub/sub, dead letter queues, error handling"
puts "- Event-Driven Architecture: Event bus, event sourcing, async processing"
puts "- Microservices: Service registry, service mesh, interceptors"
puts "- Data Integration: ETL pipelines, data synchronization, conflict resolution"
puts "\nAll examples demonstrate comprehensive integration patterns in Ruby!"
