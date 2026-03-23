# Data Management in Microservices
# Comprehensive guide to data consistency and management patterns

## 🎯 Overview

Data management in microservices presents unique challenges due to distributed nature. This guide covers data consistency patterns, database per service, event sourcing, and distributed transactions.

## 🗄️ Database Per Service Pattern

Each microservice owns its own database:

```ruby
# User Service Database Configuration
# user-service/config/database.yml
development:
  adapter: postgresql
  encoding: unicode
  database: users_development
  pool: 5
  username: user_service
  password: password
  host: localhost
  port: 5432

test:
  adapter: postgresql
  encoding: unicode
  database: users_test
  pool: 5
  username: user_service
  password: password
  host: localhost
  port: 5432

production:
  adapter: postgresql
  encoding: unicode
  database: users_production
  pool: 5
  username: user_service
  password: <%= ENV['USER_SERVICE_DB_PASSWORD'] %>
  host: <%= ENV['USER_SERVICE_DB_HOST'] %>
  port: 5432

# User Service Model
# user-service/app/models/user.rb
class User < ApplicationRecord
  self.table_name = 'users'
  
  validates :name, presence: true
  validates :email, presence: true, uniqueness: true
  validates :email, format: { with: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i }
  
  has_many :user_addresses, dependent: :destroy
  
  def self.create_with_addresses(user_data, addresses_data = [])
    user = nil
    ActiveRecord::Base.transaction do
      user = create!(user_data)
      
      addresses_data.each do |address_data|
        user.user_addresses.create!(address_data)
      end
    end
    
    user
  end
  
  def to_api_hash
    {
      id: id,
      name: name,
      email: email,
      created_at: created_at.iso8601,
      addresses: user_addresses.map(&:to_api_hash)
    }
  end
end

# user-service/app/models/user_address.rb
class UserAddress < ApplicationRecord
  self.table_name = 'user_addresses'
  
  belongs_to :user
  
  validates :street, presence: true
  validates :city, presence: true
  validates :state, presence: true
  validates :zip_code, presence: true
  
  def to_api_hash
    {
      id: id,
      street: street,
      city: city,
      state: state,
      zip_code: zip_code,
      is_primary: is_primary
    }
  end
end

# Order Service Database Configuration
# order-service/config/database.yml
development:
  adapter: postgresql
  encoding: unicode
  database: orders_development
  pool: 5
  username: order_service
  password: password
  host: localhost
  port: 5433

# Order Service Model
# order-service/app/models/order.rb
class Order < ApplicationRecord
  self.table_name = 'orders'
  
  validates :user_id, presence: true
  validates :status, presence: true, inclusion: { in: %w[pending paid shipped delivered cancelled] }
  validates :total, presence: true, numericality: { greater_than: 0 }
  
  has_many :order_items, dependent: :destroy
  
  def self.create_with_items(order_data, items_data)
    order = nil
    ActiveRecord::Base.transaction do
      order = create!(order_data)
      
      items_data.each do |item_data|
        order.order_items.create!(item_data)
      end
    end
    
    order
  end
  
  def update_status(new_status)
    update!(status: new_status)
    publish_status_changed_event
  end
  
  def calculate_total
    order_items.sum { |item| item.price * item.quantity }
  end
  
  def to_api_hash
    {
      id: id,
      user_id: user_id,
      status: status,
      total: total,
      created_at: created_at.iso8601,
      items: order_items.map(&:to_api_hash)
    }
  end
  
  private
  
  def publish_status_changed_event
    event = OrderStatusChangedEvent.new(
      order_id: id,
      user_id: user_id,
      old_status: status_was,
      new_status: status
    )
    EventPublisher.publish(event)
  end
end

# order-service/app/models/order_item.rb
class OrderItem < ApplicationRecord
  self.table_name = 'order_items'
  
  belongs_to :order
  
  validates :product_name, presence: true
  validates :quantity, presence: true, numericality: { greater_than: 0 }
  validates :price, presence: true, numericality: { greater_than: 0 }
  
  def to_api_hash
    {
      id: id,
      product_name: product_name,
      quantity: quantity,
      price: price
    }
  end
end
```

## 🔄 Data Consistency Patterns

### 1. Eventual Consistency

Accept that data will eventually become consistent:

```ruby
# Eventual consistency manager
class EventualConsistencyManager
  def initialize
    @consistency_checks = {}
    @mutex = Mutex.new
  end
  
  def register_consistency_check(name, &block)
    @mutex.synchronize do
      @consistency_checks[name] = block
    end
  end
  
  def verify_consistency(check_name, data)
    @mutex.synchronize do
      check = @consistency_checks[check_name]
      return { consistent: true } unless check
      
      begin
        result = check.call(data)
        { consistent: result[:consistent], details: result[:details] }
      rescue => e
        { consistent: false, error: e.message }
      end
    end
  end
  
  def run_all_checks(data)
    results = {}
    @mutex.synchronize do
      @consistency_checks.each do |name, check|
        begin
          result = check.call(data)
          results[name] = result
        rescue => e
          results[name] = { consistent: false, error: e.message }
        end
      end
    end
    results
  end
end

# Consistency checks for user and order data
class ConsistencyChecks
  def self.user_order_consistency(user_id)
    # Check if user exists and has valid orders
    user_service = UserServiceClient.new
    order_service = OrderServiceClient.new
    
    begin
      user = user_service.get_user(user_id)
      orders = order_service.get_user_orders(user_id)
      
      {
        consistent: !user.nil?,
        details: {
          user_exists: !user.nil?,
          order_count: orders.length,
          user_name: user&.dig(:name)
        }
      }
    rescue => e
      {
        consistent: false,
        error: e.message
      }
    end
  end
  
  def self.order_payment_consistency(order_id)
    # Check if order and payment status match
    order_service = OrderServiceClient.new
    payment_service = PaymentServiceClient.new
    
    begin
      order = order_service.get_order(order_id)
      payment = payment_service.get_payment_for_order(order_id)
      
      status_match = case order[:status]
                    when 'paid'
                      payment[:status] == 'success'
                    when 'pending'
                      payment[:status] == 'pending'
                    else
                      true
                    end
      
      {
        consistent: status_match,
        details: {
          order_status: order[:status],
          payment_status: payment[:status],
          status_match: status_match
        }
      }
    rescue => e
      {
        consistent: false,
        error: e.message
      }
    end
  end
end

# Usage
consistency_manager = EventualConsistencyManager.new
consistency_manager.register_consistency_check('user_order', &ConsistencyChecks.method(:user_order_consistency))
consistency_manager.register_consistency_check('order_payment', &ConsistencyChecks.method(:order_payment_consistency))

# Verify consistency
result = consistency_manager.verify_consistency('user_order', 123)
puts "User-Order consistency: #{result[:consistent]}"

# Run all checks
all_results = consistency_manager.run_all_checks(order_id: 456)
all_results.each do |check_name, result|
  puts "#{check_name}: #{result[:consistent] ? 'Consistent' : 'Inconsistent'}"
end
```

### 2. Saga Pattern

Distributed transaction management:

```ruby
# Saga orchestrator
class SagaOrchestrator
  def initialize(saga_id)
    @saga_id = saga_id
    @steps = []
    @compensations = []
    @current_step = 0
    @status = :pending
    @errors = []
  end
  
  def add_step(step_name, &execute_block, &compensate_block)
    @steps << {
      name: step_name,
      execute: execute_block,
      compensate: compensate_block
    }
  end
  
  def execute
    @status = :running
    
    @steps.each_with_index do |step, index|
      @current_step = index
      
      begin
        puts "Executing step: #{step[:name]}"
        result = step[:execute].call
        puts "Step #{step[:name]} completed successfully"
        
        # Store compensation data
        @compensations << {
          step: step,
          data: result
        }
        
      rescue => e
        puts "Step #{step[:name]} failed: #{e.message}"
        @errors << { step: step[:name], error: e.message }
        execute_compensations
        @status = :failed
        return false
      end
    end
    
    @status = :completed
    true
  end
  
  def execute_compensations
    puts "Executing compensations..."
    
    @compensations.reverse_each do |compensation|
      step = compensation[:step]
      data = compensation[:data]
      
      begin
        puts "Compensating step: #{step[:name]}"
        step[:compensate].call(data)
        puts "Compensation for #{step[:name]} completed"
      rescue => e
        puts "Compensation for #{step[:name]} failed: #{e.message}"
        @errors << { step: "#{step[:name]}_compensation", error: e.message }
      end
    end
  end
  
  def status
    @status
  end
  
  def errors
    @errors
  end
end

# Order processing saga
class OrderProcessingSaga
  def initialize(order_data, items_data, payment_data)
    @saga_id = SecureRandom.uuid
    @order_data = order_data
    @items_data = items_data
    @payment_data = payment_data
    @order_id = nil
    @payment_id = nil
  end
  
  def execute
    orchestrator = SagaOrchestrator.new(@saga_id)
    
    # Step 1: Create order
    orchestrator.add_step('create_order') do
      order_service = OrderServiceClient.new
      @order_id = order_service.create_order(@order_data, @items_data)[:id]
      { order_id: @order_id }
    end.compensate do |data|
      order_service = OrderServiceClient.new
      order_service.cancel_order(data[:order_id])
      puts "Compensated: Cancelled order #{data[:order_id]}"
    end
    
    # Step 2: Reserve inventory
    orchestrator.add_step('reserve_inventory') do
      inventory_service = InventoryServiceClient.new
      @items_data.each do |item|
        inventory_service.reserve_item(item[:product_id], item[:quantity])
      end
      { items: @items_data }
    end.compensate do |data|
      inventory_service = InventoryServiceClient.new
      data[:items].each do |item|
        inventory_service.release_reservation(item[:product_id], item[:quantity])
      end
      puts "Compensated: Released inventory reservations"
    end
    
    # Step 3: Process payment
    orchestrator.add_step('process_payment') do
      payment_service = PaymentServiceClient.new
      @payment_id = payment_service.process_payment(@payment_data)[:id]
      { payment_id: @payment_id }
    end.compensate do |data|
      payment_service = PaymentServiceClient.new
      payment_service.refund_payment(data[:payment_id])
      puts "Compensated: Refunded payment #{data[:payment_id]}"
    end
    
    # Step 4: Confirm order
    orchestrator.add_step('confirm_order') do
      order_service = OrderServiceClient.new
      order_service.update_status(@order_id, 'confirmed')
      { order_id: @order_id }
    end.compensate do |data|
      order_service = OrderServiceClient.new
      order_service.update_status(data[:order_id], 'cancelled')
      puts "Compensated: Cancelled order #{data[:order_id]}"
    end
    
    # Execute the saga
    success = orchestrator.execute
    
    if success
      puts "Order processing saga completed successfully"
      publish_order_processed_event
    else
      puts "Order processing saga failed"
      publish_order_failed_event(orchestrator.errors)
    end
    
    success
  end
  
  private
  
  def publish_order_processed_event
    event = OrderProcessedEvent.new(
      order_id: @order_id,
      payment_id: @payment_id,
      saga_id: @saga_id
    )
    EventPublisher.publish(event)
  end
  
  def publish_order_failed_event(errors)
    event = OrderFailedEvent.new(
      order_id: @order_id,
      saga_id: @saga_id,
      errors: errors
    )
    EventPublisher.publish(event)
  end
end

# Usage
order_data = { user_id: 123, total: 299.97 }
items_data = [
  { product_id: 'prod_1', quantity: 1, price: 199.99 },
  { product_id: 'prod_2', quantity: 2, price: 49.99 }
]
payment_data = { method: 'credit_card', amount: 299.97 }

saga = OrderProcessingSaga.new(order_data, items_data, payment_data)
success = saga.execute
puts "Saga result: #{success ? 'Success' : 'Failed'}"
```

## 📊 Event Sourcing

Store events as the source of truth:

```ruby
# Event store implementation
class EventStore
  def initialize
    @events = []
    @mutex = Mutex.new
  end
  
  def save_event(aggregate_id, event)
    @mutex.synchronize do
      event_data = {
        id: SecureRandom.uuid,
        aggregate_id: aggregate_id,
        event_type: event.class.name,
        event_data: event.to_hash,
        version: get_next_version(aggregate_id),
        timestamp: Time.now
      }
      
      @events << event_data
      puts "Stored event: #{event.class.name} for aggregate #{aggregate_id}"
    end
  end
  
  def get_events(aggregate_id, from_version: nil)
    @mutex.synchronize do
      events = @events.select { |e| e[:aggregate_id] == aggregate_id }
      
      if from_version
        events = events.select { |e| e[:version] > from_version }
      end
      
      events.sort_by { |e| e[:version] }
    end
  end
  
  def get_all_events
    @mutex.synchronize { @events.dup }
  end
  
  def get_snapshot(aggregate_id)
    events = get_events(aggregate_id)
    return nil if events.empty?
    
    # Rebuild aggregate state from events
    aggregate = rebuild_aggregate(aggregate_id, events)
    aggregate.to_snapshot
  end
  
  private
  
  def get_next_version(aggregate_id)
    events = @events.select { |e| e[:aggregate_id] == aggregate_id }
    events.empty? ? 1 : events.map { |e| e[:version] }.max + 1
  end
  
  def rebuild_aggregate(aggregate_id, events)
    # Implementation depends on aggregate type
    case events.first[:event_type]
    when 'UserCreatedEvent'
      rebuild_user(aggregate_id, events)
    when 'OrderCreatedEvent'
      rebuild_order(aggregate_id, events)
    else
      raise "Unknown aggregate type: #{events.first[:event_type]}"
    end
  end
  
  def rebuild_user(aggregate_id, events)
    user = UserAggregate.new(aggregate_id)
    events.each { |event| user.apply_event(event[:event_data]) }
    user
  end
  
  def rebuild_order(aggregate_id, events)
    order = OrderAggregate.new(aggregate_id)
    events.each { |event| order.apply_event(event[:event_data]) }
    order
  end
end

# Aggregate root base class
class AggregateRoot
  attr_reader :id, :version
  
  def initialize(id)
    @id = id
    @version = 0
    @uncommitted_events = []
  end
  
  def apply_event(event)
    # Apply event to aggregate state
    event_handler = "handle_#{event[:type].downcase}"
    send(event_handler, event[:data]) if respond_to?(event_handler)
    
    @version += 1
  end
  
  def raise_event(event)
    @uncommitted_events << event
    apply_event(event.to_hash)
  end
  
  def get_uncommitted_events
    @uncommitted_events.dup
  end
  
  def mark_events_committed
    @uncommitted_events.clear
  end
  
  def to_snapshot
    {
      id: @id,
      version: @version,
      data: to_hash
    }
  end
  
  def to_hash
    raise NotImplementedError, "Subclasses must implement to_hash"
  end
end

# User aggregate
class UserAggregate < AggregateRoot
  attr_reader :name, :email, :created_at, :updated_at
  
  def initialize(id)
    super(id)
    @name = nil
    @email = nil
    @created_at = nil
    @updated_at = nil
  end
  
  def create_user(name, email)
    raise_event(UserCreatedEvent.new(id, name, email))
  end
  
  def update_email(new_email)
    raise_event(UserEmailUpdatedEvent.new(id, new_email))
  end
  
  def handle_user_created_event(event_data)
    @name = event_data[:name]
    @email = event_data[:email]
    @created_at = Time.parse(event_data[:created_at])
    @updated_at = @created_at
  end
  
  def handle_user_email_updated_event(event_data)
    @email = event_data[:new_email]
    @updated_at = Time.parse(event_data[:updated_at])
  end
  
  def to_hash
    {
      name: @name,
      email: @email,
      created_at: @created_at&.iso8601,
      updated_at: @updated_at&.iso8601
    }
  end
end

# User events
class UserCreatedEvent
  attr_reader :id, :name, :email, :created_at
  
  def initialize(id, name, email)
    @id = id
    @name = name
    @email = email
    @created_at = Time.now.iso8601
  end
  
  def to_hash
    {
      type: 'UserCreatedEvent',
      id: @id,
      name: @name,
      email: @email,
      created_at: @created_at
    }
  end
end

class UserEmailUpdatedEvent
  attr_reader :id, :new_email, :updated_at
  
  def initialize(id, new_email)
    @id = id
    @new_email = new_email
    @updated_at = Time.now.iso8601
  end
  
  def to_hash
    {
      type: 'UserEmailUpdatedEvent',
      id: @id,
      new_email: @new_email,
      updated_at: @updated_at
    }
  end
end

# Event sourced repository
class EventSourcedRepository
  def initialize(event_store)
    @event_store = event_store
  end
  
  def save(aggregate)
    # Save uncommitted events
    aggregate.get_uncommitted_events.each do |event|
      @event_store.save_event(aggregate.id, event)
    end
    
    # Mark events as committed
    aggregate.mark_events_committed
  end
  
  def load(aggregate_id, aggregate_class)
    events = @event_store.get_events(aggregate_id)
    return nil if events.empty?
    
    # Rebuild aggregate from events
    aggregate = aggregate_class.new(aggregate_id)
    events.each do |event|
      aggregate.apply_event(event)
    end
    
    aggregate
  end
  
  def get_snapshot(aggregate_id)
    @event_store.get_snapshot(aggregate_id)
  end
end

# Usage
event_store = EventStore.new
repository = EventSourcedRepository.new(event_store)

# Create new user
user = UserAggregate.new(SecureRandom.uuid)
user.create_user("John Doe", "john@example.com")
repository.save(user)

# Load user from events
loaded_user = repository.load(user.id, UserAggregate)
puts "Loaded user: #{loaded_user.name} (#{loaded_user.email})"

# Update user email
loaded_user.update_email("john.doe@example.com")
repository.save(loaded_user)

# Get snapshot
snapshot = repository.get_snapshot(user.id)
puts "User snapshot: #{snapshot}"
```

## 🔄 CQRS Pattern

Command Query Responsibility Segregation:

```ruby
# Command side - handles writes
class UserCommandService
  def initialize(event_store, event_bus)
    @event_store = event_store
    @event_bus = event_bus
  end
  
  def create_user(command)
    # Validate command
    validate_create_user_command(command)
    
    # Create aggregate
    user = UserAggregate.new(command[:id])
    user.create_user(command[:name], command[:email])
    
    # Save events
    repository = EventSourcedRepository.new(@event_store)
    repository.save(user)
    
    # Publish events
    user.get_uncommitted_events.each do |event|
      @event_bus.publish(event)
    end
    
    { success: true, user_id: user.id }
  end
  
  def update_user_email(command)
    # Load aggregate
    repository = EventSourcedRepository.new(@event_store)
    user = repository.load(command[:id], UserAggregate)
    raise UserNotFoundError unless user
    
    # Apply update
    user.update_email(command[:new_email])
    
    # Save events
    repository.save(user)
    
    # Publish events
    user.get_uncommitted_events.each do |event|
      @event_bus.publish(event)
    end
    
    { success: true, user_id: user.id }
  end
  
  private
  
  def validate_create_user_command(command)
    raise ArgumentError, "ID is required" unless command[:id]
    raise ArgumentError, "Name is required" unless command[:name]
    raise ArgumentError, "Email is required" unless command[:email]
    raise ArgumentError, "Invalid email format" unless command[:email].include?("@")
  end
end

# Query side - handles reads
class UserQueryService
  def initialize(read_database)
    @db = read_database
  end
  
  def get_user(user_id)
    user_data = @db[:users][user_id]
    raise UserNotFoundError unless user_data
    
    user_data
  end
  
  def search_users(criteria)
    users = @db[:users].values
    
    users.select do |user|
      matches_criteria?(user, criteria)
    end
  end
  
  def get_user_orders(user_id)
    @db[:user_orders][user_id] || []
  end
  
  private
  
  def matches_criteria?(user, criteria)
    return true if criteria.empty?
    
    criteria.all? do |key, value|
      user[key] == value
    end
  end
end

# Read model projector
class UserProjector
  def initialize(read_database)
    @db = read_database
    @db[:users] = {}
    @db[:user_orders] = Hash.new { |h, k| h[k] = [] }
  end
  
  def handle_event(event)
    case event[:type]
    when 'UserCreatedEvent'
      handle_user_created(event)
    when 'UserEmailUpdatedEvent'
      handle_user_email_updated(event)
    when 'OrderCreatedEvent'
      handle_order_created(event)
    end
  end
  
  private
  
  def handle_user_created(event)
    user_data = {
      id: event[:id],
      name: event[:name],
      email: event[:email],
      created_at: event[:created_at],
      updated_at: event[:created_at]
    }
    
    @db[:users][event[:id]] = user_data
    puts "Projected user: #{user_data[:name]}"
  end
  
  def handle_user_email_updated(event)
    if @db[:users][event[:id]]
      @db[:users][event[:id]][:email] = event[:new_email]
      @db[:users][event[:id]][:updated_at] = event[:updated_at]
      puts "Updated user email: #{event[:id]}"
    end
  end
  
  def handle_order_created(event)
    if @db[:users][event[:user_id]]
      order_data = {
        id: event[:id],
        total: event[:total],
        status: event[:status],
        created_at: event[:created_at]
      }
      
      @db[:user_orders][event[:user_id]] << order_data
      puts "Added order to user: #{event[:user_id]}"
    end
  end
end

# Usage
# Setup
event_store = EventStore.new
event_bus = EventBus.new
read_db = {}

# Services
command_service = UserCommandService.new(event_store, event_bus)
query_service = UserQueryService.new(read_db)
projector = UserProjector.new(read_db)

# Subscribe projector to events
event_bus.subscribe('UserCreatedEvent') { |event| projector.handle_event(event) }
event_bus.subscribe('UserEmailUpdatedEvent') { |event| projector.handle_event(event) }

# Create user
user_id = SecureRandom.uuid
create_command = {
  id: user_id,
  name: "John Doe",
  email: "john@example.com"
}

result = command_service.create_user(create_command)
puts "Command result: #{result}"

# Query user
user = query_service.get_user(user_id)
puts "Query result: #{user[:name]} (#{user[:email]})"
```

## 🎯 Best Practices

### 1. Data Ownership Guidelines

```ruby
# Data ownership validator
class DataOwnershipValidator
  OWNERSHIP_RULES = {
    'UserService' => %w[users user_addresses user_preferences],
    'OrderService' => %w[orders order_items order_status_history],
    'PaymentService' => %w[payments payment_methods refunds],
    'InventoryService' => %w[products inventory inventory_reservations]
  }.freeze
  
  def self.validate_service_data_access(service_name, table_name)
    allowed_tables = OWNERSHIP_RULES[service_name]
    return false unless allowed_tables
    
    allowed_tables.include?(table_name)
  end
  
  def self.get_data_owner(table_name)
    OWNERSHIP_RULES.find { |_, tables| tables.include?(table_name) }&.first
  end
end

# Usage
puts DataOwnershipValidator.validate_service_data_access('UserService', 'users')  # true
puts DataOwnershipValidator.validate_service_data_access('UserService', 'orders')  # false
puts DataOwnershipValidator.get_data_owner('users')  # 'UserService'
```

### 2. Consistency Monitoring

```ruby
# Consistency monitor
class ConsistencyMonitor
  def initialize
    @consistency_checks = {}
    @alert_thresholds = {
      inconsistency_rate: 0.1,  # 10%
      max_inconsistency_age: 300  # 5 minutes
    }
  end
  
  def register_check(name, &block)
    @consistency_checks[name] = block
  end
  
  def run_checks
    results = {}
    
    @consistency_checks.each do |name, check|
      begin
        results[name] = check.call
      rescue => e
        results[name] = { consistent: false, error: e.message }
      end
    end
    
    analyze_results(results)
    results
  end
  
  private
  
  def analyze_results(results)
    inconsistent_count = results.count { |_, result| !result[:consistent] }
    total_count = results.length
    
    if total_count > 0
      inconsistency_rate = inconsistent_count.to_f / total_count
      
      if inconsistency_rate > @alert_thresholds[:inconsistency_rate]
        send_alert("High inconsistency rate: #{inconsistency_rate.round(2)}")
      end
    end
  end
  
  def send_alert(message)
    puts "ALERT: #{message}"
    # Send to monitoring system
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Database Per Service**: Set up separate databases for services
2. **Event Store**: Implement a simple event store
3. **Consistency Check**: Create data consistency checks

### Intermediate Exercises

1. **Saga Pattern**: Implement a distributed transaction saga
2. **Event Sourcing**: Build an event-sourced aggregate
3. **CQRS**: Implement command and query separation

### Advanced Exercises

1. **Event Replay**: Implement event replay functionality
2. **Snapshot Optimization**: Add snapshotting for performance
3. **Consistency Monitoring**: Build comprehensive monitoring

---

## 🎯 Summary

Data management in microservices provides:

- **Database Per Service** - Independent data ownership
- **Eventual Consistency** - Accept and manage eventual consistency
- **Saga Pattern** - Distributed transaction management
- **Event Sourcing** - Event-driven data persistence
- **CQRS** - Separation of read and write concerns

Master these patterns to handle data effectively in distributed systems!
