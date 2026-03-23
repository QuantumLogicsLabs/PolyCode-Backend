# Microservices Basics in Ruby
# Comprehensive guide to microservices architecture and implementation

## 🎯 Overview

Microservices architecture is an approach to developing a single application as a suite of small, independent services. This guide covers the fundamentals of microservices, their benefits and challenges, and Ruby-specific implementations.

## 🏗️ What Are Microservices?

Microservices are small, autonomous services that:
- Focus on a specific business capability
- Communicate via lightweight protocols
- Are independently deployable
- Have their own data stores
- Can be written in different programming languages

```ruby
# Monolithic approach (what we're moving away from)
class MonolithicApp
  def initialize
    @user_service = UserService.new
    @order_service = OrderService.new
    @payment_service = PaymentService.new
    @inventory_service = InventoryService.new
    @notification_service = NotificationService.new
  end
  
  def process_order(user_id, items)
    user = @user_service.find(user_id)
    order = @order_service.create(user, items)
    payment = @payment_service.process(order)
    inventory = @inventory_service.check_items(items)
    @notification_service.send_confirmation(user, order)
  end
end

# Microservices approach (what we're moving toward)
# Each service is independent and communicates via APIs
class UserService
  def find(user_id)
    # Database query specific to users
    puts "Finding user #{user_id} in user database"
    { id: user_id, name: "John Doe", email: "john@example.com" }
  end
end

class OrderService
  def create(user, items)
    # Order-specific logic and database
    puts "Creating order for user #{user[:id]} with items #{items}"
    { id: 123, user_id: user[:id], items: items, total: calculate_total(items) }
  end
  
  private
  
  def calculate_total(items)
    items.sum { |item| item[:price] * item[:quantity] }
  end
end

class PaymentService
  def process(order)
    # Payment processing logic
    puts "Processing payment for order #{order[:id]}"
    { status: "success", transaction_id: "txn_#{Time.now.to_i}" }
  end
end
```

## 🎯 Microservices Characteristics

### 1. Single Responsibility

Each microservice handles one business capability:

```ruby
# User Service - handles user management
class UserService
  def initialize(database_url)
    @database_url = database_url
  end
  
  def create_user(user_data)
    # User creation logic
    validate_user_data(user_data)
    save_to_database(user_data)
    publish_user_created_event(user_data)
  end
  
  def find_user(user_id)
    # User retrieval logic
    query_database("SELECT * FROM users WHERE id = #{user_id}")
  end
  
  def update_user(user_id, updates)
    # User update logic
    validate_updates(updates)
    update_database(user_id, updates)
    publish_user_updated_event(user_id, updates)
  end
  
  private
  
  def validate_user_data(data)
    # User validation rules
    raise ArgumentError, "Email is required" unless data[:email]
    raise ArgumentError, "Name is required" unless data[:name]
    raise ArgumentError, "Invalid email format" unless data[:email].include?("@")
  end
end

# Order Service - handles order management
class OrderService
  def initialize(database_url, user_service_url)
    @database_url = database_url
    @user_service_url = user_service_url
  end
  
  def create_order(user_id, items)
    # Order creation logic
    validate_items(items)
    user = fetch_user(user_id)
    order = build_order(user, items)
    save_order(order)
    publish_order_created_event(order)
    order
  end
  
  def get_order(order_id)
    # Order retrieval logic
    query_database("SELECT * FROM orders WHERE id = #{order_id}")
  end
  
  def update_order_status(order_id, status)
    # Order status update logic
    validate_status(status)
    update_database(order_id, status: status)
    publish_order_status_changed_event(order_id, status)
  end
  
  private
  
  def validate_items(items)
    raise ArgumentError, "No items provided" if items.empty?
    raise ArgumentError, "Items cannot exceed 10" if items.length > 10
  end
  
  def fetch_user(user_id)
    # Call user service via API
    HTTP.get("#{@user_service_url}/users/#{user_id}")
  end
  
  def build_order(user, items)
    {
      user_id: user_id,
      items: items,
      total: calculate_total(items),
      status: "pending",
      created_at: Time.now
    }
  end
end
```

### 2. Independent Deployment

Each service can be deployed independently:

```ruby
# Dockerfile for User Service
# Dockerfile.user
FROM ruby:3.1-alpine

WORKDIR /app
COPY Gemfile Gemfile.lock ./
RUN bundle install

COPY . .
EXPOSE 3000

CMD ["bundle", "exec", "rails", "server", "-b", "0.0.0.0"]

# Docker Compose for orchestrating services
# docker-compose.yml
version: '3.8'

services:
  user-service:
    build:
      context: ./user-service
      dockerfile: Dockerfile.user
    ports:
      - "3001:3000"
    environment:
      - DATABASE_URL=postgresql://user-db:5432/users
    depends_on:
      - user-db
    networks:
      - microservices

  order-service:
    build:
      context: ./order-service
      dockerfile: Dockerfile.order
    ports:
      - "3002:3000"
    environment:
      - DATABASE_URL=postgresql://order-db:5432/orders
      - USER_SERVICE_URL=http://user-service:3001
    depends_on:
      - order-db
      - user-service
    networks:
      - microservices

  payment-service:
    build:
      context: ./payment-service
      dockerfile: Dockerfile.payment
    ports:
      - "3003:3000"
    environment:
      - DATABASE_URL=postgresql://payment-db:5432/payments
    depends_on:
      - payment-db
    networks:
      - microservices

  user-db:
    image: postgres:15-alpine
    environment:
      - POSTGRES_DB=users
      - POSTGRES_USER=user
      - POSTGRES_PASSWORD=password
    volumes:
      - user_data:/var/lib/postgresql/data
    networks:
      - microservices

  order-db:
    image: postgres:15-alpine
    environment:
      - POSTGRES_DB=orders
      - POSTGRES_USER=order
      - POSTGRES_PASSWORD=password
    volumes:
      - order_data:/var/lib/postgresql/data
    networks:
      - microservices

  payment-db:
    image: postgres:15-alpine
    environment:
      - POSTGRES_DB=payments
      - POSTGRES_USER=payment
      - POSTGRES_PASSWORD=password
    volumes:
      - payment_data:/var/lib/postgresql/data
    networks:
      - microservices

networks:
  microservices:
    driver: bridge

volumes:
  user_data:
  order_data:
  payment_data:
```

### 3. Data Separation

Each service manages its own data:

```ruby
# User Service Database Schema
# user-service/db/migrate/001_create_users.rb
class CreateUsers < ActiveRecord::Migration[7.0]
  def change
    create_table :users do |t|
      t.string :name, null: false
      t.string :email, null: false
      t.string :password_digest, null: false
      t.string :role, null: false, default: 'user'
      t.timestamps
    end
    
    add_index :users, :email, unique: true
  end
end

# Order Service Database Schema
# order-service/db/migrate/001_create_orders.rb
class CreateOrders < ActiveRecord::Migration[7.0]
  def change
    create_table :orders do |t|
      t.references :user_id, null: false, foreign_key: false
      t.string :status, null: false, default: 'pending'
      t.decimal :total, precision: 10, scale: 2, null: false
      t.timestamps
    end
    
    create_table :order_items do |t|
      t.references :order_id, null: false, foreign_key: false
      t.string :product_name, null: false
      t.integer :quantity, null: false
      t.decimal :price, precision: 10, scale: 2, null: false
      t.timestamps
    end
  end
end

# Payment Service Database Schema
# payment-service/db/migrate/001_create_payments.rb
class CreatePayments < ActiveRecord::Migration[7.0]
  def change
    create_table :payments do |t|
      t.references :order_id, null: false, foreign_key: false
      t.string :status, null: false, default: 'pending'
      t.decimal :amount, precision: 10, scale: 2, null: false
      t.string :payment_method, null: false
      t.string :transaction_id
      t.timestamps
    end
  end
end
```

## 🔄 Service Communication

### 1. Synchronous Communication

Direct API calls between services:

```ruby
# HTTP Client for service communication
class ServiceClient
  def initialize(base_url)
    @base_url = base_url
    @timeout = 5
  end
  
  def get(endpoint)
    response = HTTP.get("#{@base_url}#{endpoint}", timeout: @timeout)
    handle_response(response)
  end
  
  def post(endpoint, data)
    response = HTTP.post("#{@base_url}#{endpoint}", 
                     json: data, 
                     timeout: @timeout)
    handle_response(response)
  end
  
  def put(endpoint, data)
    response = HTTP.put("#{@base_url}#{endpoint}", 
                    json: data, 
                    timeout: @timeout)
    handle_response(response)
  end
  
  def delete(endpoint)
    response = HTTP.delete("#{@base_url}#{endpoint}", timeout: @timeout)
    handle_response(response)
  end
  
  private
  
  def handle_response(response)
    case response.code
    when 200..299
      response.parse
    when 400..499
      raise ClientError, "Client error: #{response.code} - #{response.parse}"
    when 500..599
      raise ServerError, "Server error: #{response.code} - #{response.parse}"
    else
      raise CommunicationError, "Unknown error: #{response.code}"
    end
  end
end

# Service communication example
class OrderService
  def initialize
    @user_client = ServiceClient.new('http://user-service:3001')
    @payment_client = ServiceClient.new('http://payment-service:3003')
  end
  
  def create_order_with_payment(user_id, items, payment_info)
    # Get user information
    user = @user_client.get("/users/#{user_id}")
    
    # Create order
    order_data = {
      user_id: user_id,
      items: items,
      total: calculate_total(items)
    }
    
    order = post('/orders', order_data)
    
    # Process payment
    payment_data = {
      order_id: order[:id],
      amount: order[:total],
      payment_method: payment_info[:method],
      card_number: payment_info[:card_number]
    }
    
    payment = @payment_client.post('/payments', payment_data)
    
    # Update order status
    if payment[:status] == 'success'
      put("/orders/#{order[:id]}", status: 'paid')
    else
      put("/orders/#{order[:id]}", status: 'payment_failed')
    end
    
    order
  end
  
  private
  
  def calculate_total(items)
    items.sum { |item| item[:price] * item[:quantity] }
  end
end
```

### 2. Asynchronous Communication

Event-driven communication with message queues:

```ruby
# Event publisher
class EventPublisher
  def initialize(message_queue)
    @queue = message_queue
  end
  
  def publish(event_type, data)
    event = {
      id: SecureRandom.uuid,
      type: event_type,
      data: data,
      timestamp: Time.now.iso8601,
      source: self.class.name
    }
    
    @queue.publish(event)
    puts "Published event: #{event_type}"
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
  
  def start_listening
    @queue.subscribe do |event|
      handlers = @handlers[event[:type]] || []
      handlers.each { |handler| handler.call(event) }
    end
  end
end

# Message queue implementation
class MessageQueue
  def initialize
    @subscribers = []
    @queue = []
    @mutex = Mutex.new
  end
  
  def publish(event)
    @mutex.synchronize do
      @queue << event
      notify_subscribers(event)
    end
  end
  
  def subscribe(&block)
    @subscribers << block
  end
  
  private
  
  def notify_subscribers(event)
    @subscribers.each { |subscriber| subscriber.call(event) }
  end
end

# Usage example
# In Order Service
class OrderService
  def initialize
    @event_publisher = EventPublisher.new(MessageQueue.new)
  end
  
  def create_order(user_id, items)
    order = build_order(user_id, items)
    save_order(order)
    
    @event_publisher.publish('order.created', {
      order_id: order[:id],
      user_id: user_id,
      items: items,
      total: order[:total]
    })
    
    order
  end
  
  private
  
  def build_order(user_id, items)
    {
      id: SecureRandom.uuid,
      user_id: user_id,
      items: items,
      total: items.sum { |item| item[:price] * item[:quantity] },
      status: 'pending',
      created_at: Time.now
    }
  end
end

# In Notification Service
class NotificationService
  def initialize
    @event_subscriber = EventSubscriber.new(MessageQueue.new)
    setup_handlers
  end
  
  def start
    @event_subscriber.start_listening
  end
  
  private
  
  def setup_handlers
    @event_subscriber.subscribe('order.created') do |event|
      handle_order_created(event)
    end
    
    @event_subscriber.subscribe('order.paid') do |event|
      handle_order_paid(event)
    end
  end
  
  def handle_order_created(event)
    puts "Notification: Order #{event[:data][:order_id]} created"
    # Send notification logic
  end
  
  def handle_order_paid(event)
    puts "Notification: Order #{event[:data][:order_id]} paid"
    # Send payment confirmation logic
  end
end
```

## 🔍 Service Discovery

### 1. Service Registry

Central registry for service locations:

```ruby
class ServiceRegistry
  def initialize
    @services = {}
    @mutex = Mutex.new
  end
  
  def register(name, url, health_check_path = '/health')
    @mutex.synchronize do
      @services[name] = {
        url: url,
        health_check_path: health_check_path,
        last_check: nil,
        healthy: false
      }
    end
    puts "Registered service: #{name} at #{url}"
  end
  
  def get_service(name)
    @mutex.synchronize { @services[name] }
  end
  
  def get_service_url(name)
    service = get_service(name)
    return nil unless service
    
    check_health(name) unless service[:healthy]
    service[:url]
  end
  
  def check_health(name)
    service = get_service(name)
    return false unless service
    
    begin
      response = HTTP.get("#{service[:url]}#{service[:health_check_path]}", timeout: 2)
      healthy = response.code == 200
    rescue => e
      puts "Health check failed for #{name}: #{e.message}"
      healthy = false
    end
    
    @mutex.synchronize do
      service[:last_check] = Time.now
      service[:healthy] = healthy
    end
    
    healthy
  end
  
  def check_all_services
    @services.keys.each { |name| check_health(name) }
  end
  
  def list_services
    @mutex.synchronize do
      @services.map do |name, info|
        {
          name: name,
          url: info[:url],
          healthy: info[:healthy],
          last_check: info[:last_check]
        }
      end
    end
  end
end

# Usage
registry = ServiceRegistry.new
registry.register('user-service', 'http://user-service:3001')
registry.register('order-service', 'http://order-service:3002')
registry.register('payment-service', 'http://payment-service:3003')

user_url = registry.get_service_url('user-service')
puts "User service URL: #{user_url}"

# Health check
registry.check_all_services
puts "Service status:"
registry.list_services.each do |service|
  puts "#{service[:name]}: #{service[:healthy] ? 'Healthy' : 'Unhealthy'}"
end
```

### 2. Load Balancer

Simple load balancer for service instances:

```ruby
class LoadBalancer
  def initialize
    @instances = {}
    @current_index = {}
    @mutex = Mutex.new
  end
  
  def register_instance(service_name, url)
    @mutex.synchronize do
      @instances[service_name] ||= []
      @instances[service_name] << url
      @current_index[service_name] = 0
    end
    
    puts "Registered instance #{url} for service #{service_name}"
  end
  
  def get_instance(service_name)
    @mutex.synchronize do
      instances = @instances[service_name]
      return nil unless instances
      
      # Round-robin selection
      instance = instances[@current_index[service_name]]
      @current_index[service_name] = (@current_index[service_name] + 1) % instances.length
      instance
    end
  end
  
  def get_all_instances(service_name)
    @mutex.synchronize { @instances[service_name]&.dup }
  end
  
  def remove_instance(service_name, url)
    @mutex.synchronize do
      if @instances[service_name]
        @instances[service_name].delete(url)
        @current_index[service_name] = 0
      end
    end
    
    puts "Removed instance #{url} from service #{service_name}"
  end
  
  def list_services
    @mutex.synchronize do
      @instances.map do |name, instances|
        {
          name: name,
          instances: instances.dup,
          count: instances.length
        }
      end
    end
  end
end

# Usage
load_balancer = LoadBalancer.new

# Register multiple instances
load_balancer.register_instance('user-service', 'http://user-service-1:3001')
load_balancer.register_instance('user-service', 'http://user-service-2:3001')
load_balancer.register_instance('user-service', 'http://user-service-3:3001')

# Get instances
3.times do |i|
  instance = load_balancer.get_instance('user-service')
  puts "Request #{i + 1}: #{instance}"
end

# List all services
load_balancer.list_services.each do |service|
  puts "#{service[:name]}: #{service[:count]} instances"
end
```

## 🎯 Benefits and Challenges

### Benefits

1. **Independent Deployment**: Services can be deployed independently
2. **Technology Diversity**: Different services can use different technologies
3. **Scalability**: Individual services can be scaled independently
4. **Fault Isolation**: Failure in one service doesn't affect others
5. **Team Autonomy**: Teams can work on services independently

### Challenges

1. **Network Complexity**: More complex communication between services
2. **Data Consistency**: Maintaining data consistency across services
3. **Testing Complexity**: Integration testing is more complex
4. **Monitoring**: Need to monitor many services
5. **Deployment Complexity**: More complex deployment pipelines

## 🎓 Exercises

### Beginner Exercises

1. **Service Creation**: Create a simple microservice
2. **Service Communication**: Implement HTTP communication between services
3. **Docker Setup**: Create Docker containers for services

### Intermediate Exercises

1. **Event System**: Implement event-driven communication
2. **Service Discovery**: Build a service registry
3. **Load Balancing**: Create a simple load balancer

### Advanced Exercises

1. **Data Consistency**: Implement saga pattern for distributed transactions
2. **Circuit Breaker**: Add resilience patterns
3. **Monitoring**: Create centralized monitoring system

---

## 🎯 Summary

Microservices architecture provides:

- **Independent Services** - Autonomous business capabilities
- **Flexible Deployment** - Independent deployment and scaling
- **Technology Diversity** - Multiple programming languages and frameworks
- **Fault Isolation** - Resilience to failures
- **Team Autonomy** - Independent team development

Master microservices to build scalable, maintainable distributed systems!
