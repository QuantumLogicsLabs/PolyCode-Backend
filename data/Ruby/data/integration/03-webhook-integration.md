# Webhook Integration in Ruby
# Comprehensive guide to webhook implementation and management

## 🪝 Webhook Fundamentals

### 1. Webhook Concepts

Core webhook principles:

```ruby
class WebhookFundamentals
  def self.explain_webhook_concepts
    puts "Webhook Fundamentals:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Webhook",
        description: "HTTP callback that notifies events",
        purpose: ["Real-time notifications", "Event-driven architecture", "System integration"],
        benefits: ["Immediate updates", "Decoupled systems", "Event-driven"],
        components: ["Event source", "Webhook URL", "Payload", "Authentication"]
      },
      {
        concept: "Event-Driven Architecture",
        description: "Systems that communicate through events",
        patterns: ["Publish-subscribe", "Event sourcing", "CQRS"],
        benefits: ["Scalability", "Flexibility", "Loose coupling"],
        challenges: ["Event ordering", "Event versioning", "Event schema"]
      },
      {
        concept: "Webhook Delivery",
        description: "Process of delivering webhook events",
        methods: ["HTTP POST", "Retry mechanisms", "Queuing", "Batching"],
        considerations: ["Reliability", "Performance", "Security", "Scalability"],
        strategies: ["Immediate delivery", "Batch delivery", "Scheduled delivery"]
      },
      {
        concept: "Webhook Security",
        description: "Securing webhook communications",
        methods: ["HMAC signatures", "API keys", "HTTPS", "IP whitelisting"],
        best_practices: ["Signature verification", "HTTPS only", "Rate limiting"],
        threats: ["Replay attacks", "Man-in-the-middle", "Spoofing"]
      },
      {
        concept: "Webhook Processing",
        description: "Handling incoming webhook events",
        steps: ["Verification", "Parsing", "Processing", "Response"],
        patterns: ["Synchronous processing", "Asynchronous processing", "Queue-based"],
        considerations: ["Idempotency", "Error handling", "Performance"]
      },
      {
        concept: "Webhook Management",
        description: "Managing webhook subscriptions",
        operations: ["Registration", "Verification", "Deactivation", "Testing"],
        features: ["Webhook logs", "Delivery status", "Retry configuration"],
        monitoring: ["Success rates", "Failure rates", "Latency", "Errors"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Purpose: #{concept[:purpose].join(', ')}" if concept[:purpose]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Patterns: #{concept[:patterns].join(', ')}" if concept[:patterns]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Methods: #{concept[:methods].join(', ')}" if concept[:methods]
      puts "  Considerations: #{concept[:considerations].join(', ')}" if concept[:considerations]
      puts "  Strategies: #{concept[:strategies].join(', ')}" if concept[:strategies]
      puts "  Best Practices: #{concept[:best_practices].join(', ')}" if concept[:best_practices]
      puts "  Threats: #{concept[:threats].join(', ')}" if concept[:threats]
      puts "  Steps: #{concept[:steps].join(', ')}" if concept[:steps]
      puts "  Operations: #{concept[:operations].join(', ')}" if concept[:operations]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts "  Monitoring: #{concept[:monitoring].join(', ')}" if concept[:monitoring]
      puts
    end
  end
  
  def self.webhook_lifecycle
    puts "\nWebhook Lifecycle:"
    puts "=" * 50
    
    lifecycle = [
      {
        phase: "1. Registration",
        description: "Register webhook URL",
        steps: ["Provide webhook URL", "Select events", "Configure options", "Verify ownership"],
        validation: ["URL validation", "Ownership verification", "Permission check"],
        output: ["Webhook ID", "Secret key", "Configuration"]
      },
      {
        phase: "2. Event Generation",
        description: "Generate webhook event",
        triggers: ["User action", "System event", "Scheduled event", "External trigger"],
        processing: ["Event creation", "Payload generation", "Subscriber selection"],
        enrichment: ["Event metadata", "User context", "System state"]
      },
      {
        phase: "3. Delivery",
        description: "Deliver webhook to subscriber",
        methods: ["HTTP POST", "Retry logic", "Queue management", "Batch processing"],
        security: ["Signature generation", "Authentication", "Encryption"],
        tracking: ["Delivery attempts", "Response codes", "Latency"]
      },
      {
        phase: "4. Processing",
        description: "Process webhook payload",
        steps: ["Signature verification", "Payload parsing", "Event processing", "Response"],
        patterns: ["Synchronous", "Asynchronous", "Queue-based"],
        considerations: ["Idempotency", "Error handling", "Performance"]
      },
      {
        phase: "5. Monitoring",
        description: "Monitor webhook performance",
        metrics: ["Success rate", "Failure rate", "Latency", "Error types"],
        alerts: ["Delivery failures", "High latency", "Error spikes"],
        maintenance: ["Retry configuration", "Subscriber management", "Event updates"]
      }
    ]
    
    lifecycle.each do |phase|
      puts "#{phase[:phase]}: #{phase[:description]}"
      puts "  Steps: #{phase[:steps].join(', ')}" if phase[:steps]
      puts "  Validation: #{phase[:validation].join(', ')}" if phase[:validation]
      puts "  Output: #{phase[:output].join(', ')}" if phase[:output]
      puts "  Triggers: #{phase[:triggers].join(', ')}" if phase[:triggers]
      puts "  Processing: #{phase[:processing].join(', ')}" if phase[:processing]
      puts "  Enrichment: #{phase[:enrichment].join(', ')}" if phase[:enrichment]
      puts "  Methods: #{phase[:methods].join(', ')}" if phase[:methods]
      puts "  Security: #{phase[:security].join(', ')}" if phase[:security]
      puts "  Tracking: #{phase[:tracking].join(', ')}" if phase[:tracking]
      puts "  Patterns: #{phase[:patterns].join(', ')}" if phase[:patterns]
      puts "  Considerations: #{phase[:considerations].join(', ')}" if phase[:considerations]
      puts "  Metrics: #{phase[:metrics].join(', ')}" if phase[:metrics]
      puts "  Alerts: #{phase[:alerts].join(', ')}" if phase[:alerts]
      puts "  Maintenance: #{phase[:maintenance].join(', ')}" if phase[:maintenance]
      puts
    end
  end
  
  def self.webhook_best_practices
    puts "\nWebhook Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Security First",
        description: "Implement robust security measures",
        guidelines: [
          "Use HTTPS for all webhook URLs",
          "Implement HMAC signature verification",
          "Validate webhook payloads",
          "Use secret keys for authentication",
          "Implement rate limiting"
        ],
        benefits: ["Security", "Trust", "Compliance", "Data protection"]
      },
      {
        practice: "Reliable Delivery",
        description: "Ensure reliable webhook delivery",
        guidelines: [
          "Implement retry mechanisms",
          "Use exponential backoff",
          "Provide delivery status",
          "Handle timeouts gracefully",
          "Monitor delivery failures"
        ],
        benefits: ["Reliability", "User experience", "Debugging", "Monitoring"]
      },
      {
        practice: "Idempotency",
        description: "Make webhook processing idempotent",
        guidelines: [
          "Use unique event IDs",
          "Track processed events",
          "Implement deduplication",
          "Handle duplicate events",
          "Provide event history"
        ],
        benefits: ["Consistency", "Reliability", "Error handling", "Recovery"]
      },
      {
        practice: "Performance",
        description: "Optimize webhook performance",
        guidelines: [
          "Process events asynchronously",
          "Use queue-based processing",
          "Implement timeouts",
          "Monitor processing time",
          "Optimize payload size"
        ],
        benefits: ["Speed", "Scalability", "Resource efficiency", "User experience"]
      },
      {
        practice: "Documentation",
        description: "Provide comprehensive documentation",
        guidelines: [
          "Document event schemas",
          "Provide examples",
          "Explain authentication",
          "Include error codes",
          "Provide testing tools"
        ],
        benefits: ["Adoption", "Support", "Developer experience", "Reduced support"]
      },
      {
        practice: "Monitoring",
        description: "Implement comprehensive monitoring",
        guidelines: [
          "Track delivery metrics",
          "Monitor processing time",
          "Alert on failures",
          "Provide webhook logs",
          "Analyze success rates"
        ],
        benefits: ["Visibility", "Proactive management", "Debugging", "Optimization"]
      }
    ]
    
    practices.each do |practice|
      puts "#{practice[:practice]}:"
      puts "  Description: #{practice[:description]}"
      puts "  Guidelines: #{practice[:guidelines].join(', ')}"
      puts "  Benefits: #{practice[:benefits].join(', ')}"
      puts
    end
  end
  
  # Run webhook fundamentals
  explain_webhook_concepts
  webhook_lifecycle
  webhook_best_practices
end
```

### 2. Webhook Provider

Webhook event generation and delivery:

```ruby
class WebhookProvider
  def initialize(name, options = {})
    @name = name
    @subscribers = {}
    @events = []
    @delivery_queue = Queue.new
    @delivery_thread = nil
    @running = false
    @secret_generator = SecretGenerator.new
    @signature_generator = SignatureGenerator.new
    @logger = options[:logger]
    @retry_policy = options[:retry_policy] || RetryPolicy.new
  end
  
  def subscribe(url, events = [], options = {})
    subscription_id = SecureRandom.uuid
    secret = @secret_generator.generate_secret
    
    subscription = {
      id: subscription_id,
      url: url,
      events: events,
      secret: secret,
      active: true,
      created_at: Time.now,
      options: options
    }
    
    @subscribers[subscription_id] = subscription
    
    puts "Webhook subscription created: #{subscription_id}"
    puts "URL: #{url}"
    puts "Events: #{events.join(', ')}"
    
    subscription
  end
  
  def unsubscribe(subscription_id)
    subscription = @subscribers[subscription_id]
    return false unless subscription
    
    subscription[:active] = false
    puts "Webhook subscription deactivated: #{subscription_id}"
    
    true
  end
  
  def emit_event(event_type, data = {})
    event = {
      id: SecureRandom.uuid,
      type: event_type,
      data: data,
      timestamp: Time.now,
      provider: @name
    }
    
    @events << event
    
    # Queue for delivery
    @subscribers.each do |id, subscription|
      next unless subscription[:active]
      next unless subscription[:events].include?(event_type) || subscription[:events].empty?
      
      @delivery_queue << {
        subscription: subscription,
        event: event,
        attempts: 0,
        created_at: Time.now
      }
    end
    
    puts "Event emitted: #{event_type} (#{event[:id]})"
    
    event
  end
  
  def start_delivery
    return if @running
    
    @running = true
    @delivery_thread = Thread.new { process_delivery_queue }
    
    puts "Webhook delivery started for #{@name}"
  end
  
  def stop_delivery
    @running = false
    @delivery_thread&.join
    puts "Webhook delivery stopped for #{@name}"
  end
  
  def get_subscription(subscription_id)
    @subscribers[subscription_id]
  end
  
  def get_subscriptions
    @subscribers.values
  end
  
  def get_events(limit = 50)
    @events.last(limit)
  end
  
  def get_delivery_status(subscription_id)
    subscription = @subscribers[subscription_id]
    return nil unless subscription
    
    {
      subscription_id: subscription_id,
      url: subscription[:url],
      active: subscription[:active],
      events_subscribed: subscription[:events],
      created_at: subscription[:created_at]
    }
  end
  
  def self.demonstrate_webhook_provider
    puts "Webhook Provider Demonstration:"
    puts "=" * 50
    
    # Create webhook provider
    provider = WebhookProvider.new('myapp', {
      logger: Logger.new(STDOUT)
    })
    
    # Start delivery
    provider.start_delivery
    
    # Subscribe to events
    subscription1 = provider.subscribe('https://webhook.example.com/endpoint1', [
      'user.created',
      'user.updated',
      'user.deleted'
    ])
    
    subscription2 = provider.subscribe('https://webhook.example.com/endpoint2', [
      'order.created',
      'order.updated'
    ])
    
    # Emit events
    puts "\nEmitting events:"
    
    provider.emit_event('user.created', {
      user_id: 123,
      name: 'John Doe',
      email: 'john@example.com'
    })
    
    provider.emit_event('order.created', {
      order_id: 456,
      user_id: 123,
      total: 99.99,
      items: ['item1', 'item2']
    })
    
    provider.emit_event('user.updated', {
      user_id: 123,
      name: 'John Smith',
      email: 'john.smith@example.com'
    })
    
    # Wait for delivery
    sleep(2)
    
    # Get subscription info
    puts "\nSubscription status:"
    subscription1_status = provider.get_delivery_status(subscription1[:id])
    puts "Subscription 1: #{subscription1_status[:url]} (#{subscription1_status[:active] ? 'Active' : 'Inactive'})"
    
    # Get events
    puts "\nRecent events:"
    events = provider.get_events(5)
    events.each do |event|
      puts "  #{event[:type]}: #{event[:id]}"
    end
    
    # Stop delivery
    provider.stop_delivery
    
    puts "\nWebhook Provider Features:"
    puts "- Event subscription"
    puts "- Event emission"
    puts "- Asynchronous delivery"
    puts "- Retry mechanisms"
    puts "- Signature generation"
    puts "- Subscription management"
  end
  
  private
  
  def process_delivery_queue
    while @running
      begin
        delivery = @delivery_queue.pop(true) # Non-blocking pop
        deliver_webhook(delivery)
      rescue ThreadError
        # Queue is empty, wait a bit
        sleep(0.1)
      rescue => e
        log_error("Delivery error: #{e.message}")
      end
    end
  end
  
  def deliver_webhook(delivery)
    subscription = delivery[:subscription]
    event = delivery[:event]
    
    # Generate signature
    payload = event.to_json
    signature = @signature_generator.generate_signature(payload, subscription[:secret])
    
    # Prepare request
    headers = {
      'Content-Type' => 'application/json',
      'X-Webhook-Signature' => signature,
      'X-Webhook-Event' => event[:type],
      'X-Webhook-ID' => event[:id],
      'User-Agent' => "#{@name}-webhook/1.0"
    }
    
    # Deliver webhook
    success = deliver_http_request(subscription[:url], headers, payload)
    
    if success
      log_delivery_success(subscription[:id], event[:id])
    else
      handle_delivery_failure(delivery)
    end
  end
  
  def deliver_http_request(url, headers, payload)
    # Simulate HTTP request
    puts "  Delivering webhook to: #{url}"
    puts "  Event: #{headers['X-Webhook-Event']}"
    puts "  Payload size: #{payload.length} bytes"
    
    # Simulate success/failure
    success = rand > 0.2 # 80% success rate
    
    if success
      puts "  ✓ Delivery successful"
      true
    else
      puts "  ✗ Delivery failed"
      false
    end
  end
  
  def handle_delivery_failure(delivery)
    delivery[:attempts] += 1
    
    if @retry_policy.should_retry?(delivery[:attempts])
      delay = @retry_policy.calculate_delay(delivery[:attempts])
      
      log_delivery_retry(delivery[:subscription][:id], delivery[:event][:id], delivery[:attempts], delay)
      
      # Re-queue with delay
      Thread.new do
        sleep(delay)
        @delivery_queue << delivery
      end
    else
      log_delivery_failure(delivery[:subscription][:id], delivery[:event][:id], delivery[:attempts])
    end
  end
  
  def log_delivery_success(subscription_id, event_id)
    return unless @logger
    
    @logger.info("Webhook delivered successfully", {
      subscription_id: subscription_id,
      event_id: event_id
    })
  end
  
  def log_delivery_retry(subscription_id, event_id, attempts, delay)
    return unless @logger
    
    @logger.warn("Webhook delivery retry", {
      subscription_id: subscription_id,
      event_id: event_id,
      attempts: attempts,
      retry_delay: delay
    })
  end
  
  def log_delivery_failure(subscription_id, event_id, attempts)
    return unless @logger
    
    @logger.error("Webhook delivery failed", {
      subscription_id: subscription_id,
      event_id: event_id,
      attempts: attempts
    })
  end
  
  def log_error(message)
    return unless @logger
    
    @logger.error("Webhook provider error", { message: message })
  end
end

class SecretGenerator
  def generate_secret(length = 32)
    SecureRandom.hex(length)
  end
end

class SignatureGenerator
  def generate_signature(payload, secret)
    OpenSSL::HMAC.hexdigest('sha256', secret, payload)
  end
end

class RetryPolicy
  def initialize(options = {})
    @max_attempts = options[:max_attempts] || 5
    @base_delay = options[:base_delay] || 1
    @max_delay = options[:max_delay] || 300
    @backoff_multiplier = options[:backoff_multiplier] || 2
  end
  
  def should_retry?(attempts)
    attempts < @max_attempts
  end
  
  def calculate_delay(attempts)
    delay = @base_delay * (@backoff_multiplier ** (attempts - 1))
    [delay, @max_delay].min
  end
end
```

## 🔗 Webhook Consumer

### 3. Webhook Receiver

Webhook event processing:

```ruby
class WebhookReceiver
  def initialize(options = {})
    @routes = {}
    @middleware = []
    @event_processors = {}
    @signature_verifier = SignatureVerifier.new
    @logger = options[:logger]
    @event_store = EventStore.new
    @idempotency_manager = IdempotencyManager.new
  end
  
  def route(event_type, &block)
    @routes[event_type] = block
  end
  
  def use(middleware)
    @middleware << middleware
  end
  
  def process_webhook(event_type, payload, headers = {})
    # Verify signature
    unless verify_signature(payload, headers)
      return { success: false, error: 'Invalid signature' }
    end
    
    # Check idempotency
    event_id = headers['X-Webhook-ID']
    if event_id && @idempotency_manager.processed?(event_id)
      return { success: true, message: 'Event already processed' }
    end
    
    # Parse event
    event = parse_event(payload, headers)
    
    # Store event
    @event_store.store(event)
    
    # Process through middleware
    context = {
      event: event,
      headers: headers,
      processed: false
    }
    
    @middleware.each do |middleware|
      context = middleware.call(context)
      break unless context[:processed]
    end
    
    # Route to handler
    handler = @routes[event_type]
    if handler
      begin
        result = handler.call(event)
        @idempotency_manager.mark_processed(event_id) if event_id
        
        {
          success: true,
          result: result,
          event_id: event_id
        }
      rescue => e
        log_error("Webhook processing error", e)
        
        {
          success: false,
          error: e.message,
          event_id: event_id
        }
      end
    else
      {
        success: false,
        error: "No handler for event type: #{event_type}",
        event_id: event_id
      }
    end
  end
  
  def add_event_processor(event_type, processor)
    @event_processors[event_type] = processor
  end
  
  def get_event(event_id)
    @event_store.get(event_id)
  end
  
  def get_events(limit = 50)
    @event_store.get_all(limit)
  end
  
  def self.demonstrate_webhook_receiver
    puts "Webhook Receiver Demonstration:"
    puts "=" * 50
    
    # Create webhook receiver
    receiver = WebhookReceiver.new({
      logger: Logger.new(STDOUT)
    })
    
    # Add middleware
    receiver.use(LoggingMiddleware.new)
    receiver.use(AuthenticationMiddleware.new)
    receiver.use(RateLimitingMiddleware.new)
    
    # Define event handlers
    receiver.route('user.created') do |event|
      puts "Processing user.created event"
      puts "User ID: #{event[:data][:user_id]}"
      puts "Name: #{event[:data][:name]}"
      
      # Process user creation
      { status: 'processed', user_id: event[:data][:user_id] }
    end
    
    receiver.route('order.created') do |event|
      puts "Processing order.created event"
      puts "Order ID: #{event[:data][:order_id]}"
      puts "Total: #{event[:data][:total]}"
      
      # Process order creation
      { status: 'processed', order_id: event[:data][:order_id] }
    end
    
    # Simulate webhook processing
    puts "\nProcessing webhooks:"
    
    # User created event
    user_payload = {
      id: 'evt_123',
      type: 'user.created',
      data: {
        user_id: 123,
        name: 'John Doe',
        email: 'john@example.com'
      },
      timestamp: Time.now
    }
    
    user_headers = {
      'X-Webhook-Signature' => 'signature123',
      'X-Webhook-Event' => 'user.created',
      'X-Webhook-ID' => 'evt_123'
    }
    
    result1 = receiver.process_webhook('user.created', user_payload, user_headers)
    puts "Result: #{result1[:success] ? 'Success' : 'Failed'}"
    
    # Order created event
    order_payload = {
      id: 'evt_456',
      type: 'order.created',
      data: {
        order_id: 456,
        user_id: 123,
        total: 99.99,
        items: ['item1', 'item2']
      },
      timestamp: Time.now
    }
    
    order_headers = {
      'X-Webhook-Signature' => 'signature456',
      'X-Webhook-Event' => 'order.created',
      'X-Webhook-ID' => 'evt_456'
    }
    
    result2 = receiver.process_webhook('order.created', order_payload, order_headers)
    puts "Result: #{result2[:success] ? 'Success' : 'Failed'}"
    
    # Test idempotency (duplicate event)
    puts "\nTesting idempotency:"
    result3 = receiver.process_webhook('user.created', user_payload, user_headers)
    puts "Duplicate result: #{result3[:success] ? 'Success' : 'Failed'}"
    puts "Message: #{result3[:message]}"
    
    # Get events
    puts "\nStored events:"
    events = receiver.get_events(5)
    events.each do |event|
      puts "  #{event[:type]}: #{event[:id]}"
    end
    
    puts "\nWebhook Receiver Features:"
    puts "- Event routing"
    puts "- Middleware support"
    puts "- Signature verification"
    puts "- Idempotency handling"
    puts "- Event storage"
    puts "- Error handling"
  end
  
  private
  
  def verify_signature(payload, headers)
    signature = headers['X-Webhook-Signature']
    return false unless signature
    
    # In real implementation, verify with stored secret
    # For demo, we'll just check if signature exists
    !signature.nil?
  end
  
  def parse_event(payload, headers)
    case payload
    when Hash
      payload
    when String
      JSON.parse(payload)
    else
      { error: 'Invalid payload format' }
    end
  rescue JSON::ParserError
    { error: 'Invalid JSON payload' }
  end
  
  def log_error(message, error)
    return unless @logger
    
    @logger.error(message, {
      error: error.message,
      backtrace: error.backtrace
    })
  end
end

class EventStore
  def initialize
    @events = {}
    @mutex = Mutex.new
  end
  
  def store(event)
    @mutex.synchronize do
      @events[event[:id]] = event
    end
  end
  
  def get(event_id)
    @mutex.synchronize do
      @events[event_id]
    end
  end
  
  def get_all(limit = 50)
    @mutex.synchronize do
      @events.values.last(limit)
    end
  end
end

class IdempotencyManager
  def initialize
    @processed_events = {}
    @mutex = Mutex.new
  end
  
  def processed?(event_id)
    @mutex.synchronize do
      @processed_events.key?(event_id)
    end
  end
  
  def mark_processed(event_id)
    @mutex.synchronize do
      @processed_events[event_id] = Time.now
    end
  end
  
  def cleanup(older_than = 3600)
    @mutex.synchronize do
      cutoff = Time.now - older_than
      @processed_events.delete_if { |_, time| time < cutoff }
    end
  end
end

# Middleware classes
class LoggingMiddleware
  def call(context)
    event = context[:event]
    
    puts "Middleware: Processing event #{event[:type]} (#{event[:id]})"
    
    context
  end
end

class AuthenticationMiddleware
  def call(context)
    headers = context[:headers]
    
    # Simulate authentication check
    auth_header = headers['Authorization']
    if auth_header.nil?
      context[:processed] = true
      context[:error] = 'Missing authentication'
    else
      puts "Middleware: Authentication successful"
    end
    
    context
  end
end

class RateLimitingMiddleware
  def initialize(rate_limit = 100)
    @rate_limit = rate_limit
    @requests = {}
  end
  
  def call(context)
    client_ip = context[:headers]['X-Client-IP'] || 'unknown'
    
    @requests[client_ip] ||= []
    @requests[client_ip] << Time.now
    
    # Clean old requests (older than 1 minute)
    @requests[client_ip].reject! { |time| Time.now - time > 60 }
    
    if @requests[client_ip].length > @rate_limit
      context[:processed] = true
      context[:error] = 'Rate limit exceeded'
    else
      puts "Middleware: Rate limit OK (#{@requests[client_ip].length}/#{@rate_limit})"
    end
    
    context
  end
end
```

## 🔄 Webhook Integration Patterns

### 4. Integration Patterns

Common webhook integration patterns:

```ruby
class WebhookIntegrationPatterns
  def self.demonstrate_patterns
    puts "Webhook Integration Patterns:"
    puts "=" * 50
    
    # 1. Event Sourcing Pattern
    demonstrate_event_sourcing
    
    # 2. CQRS Pattern
    demonstrate_cqrs
    
    # 3. Saga Pattern
    demonstrate_saga
    
    # 4. Event Replay Pattern
    demonstrate_event_replay
    
    # 5. Fan-out Pattern
    demonstrate_fan_out
    
    # 6. Circuit Breaker Pattern
    demonstrate_circuit_breaker
  end
  
  def self.demonstrate_event_sourcing
    puts "\n1. Event Sourcing Pattern:"
    puts "=" * 30
    
    event_store = EventSourcingStore.new
    
    # Create aggregate
    user = UserAggregate.new(event_store)
    
    # Execute commands
    user.create('John Doe', 'john@example.com')
    user.update_name('John Smith')
    user.update_email('john.smith@example.com')
    
    # Replay events
    puts "Replaying events:"
    replayed_user = UserAggregate.new(event_store)
    replayed_user.replay_events('user-123')
    
    puts "Replayed state: #{replayed_user.state}"
    
    puts "\nEvent Sourcing Features:"
    puts "- Event storage"
    puts "- State reconstruction"
    puts "- Event replay"
    puts "- Immutable events"
    puts "- Audit trail"
  end
  
  def self.demonstrate_cqrs
    puts "\n2. CQRS Pattern:"
    puts "=" * 30
    
    command_bus = CommandBus.new
    event_bus = EventBus.new
    read_repository = ReadRepository.new
    
    # Command handlers
    command_bus.register(CreateUserCommand) do |command|
      user = User.new(command.name, command.email)
      event = UserCreatedEvent.new(user.id, user.name, user.email)
      event_bus.publish(event)
    end
    
    # Event handlers
    event_bus.subscribe(UserCreatedEvent) do |event|
      read_repository.save_user_projection(event)
    end
    
    # Execute command
    command = CreateUserCommand.new('Jane Doe', 'jane@example.com')
    command_bus.execute(command)
    
    # Query read model
    user_projection = read_repository.get_user_projection('user-456')
    puts "User projection: #{user_projection}"
    
    puts "\nCQRS Features:"
    puts "- Command/Query separation"
    puts "- Event-driven updates"
    puts "- Read model projections"
    puts "- Event sourcing"
    puts "- Scalability"
  end
  
  def self.demonstrate_saga
    puts "\n3. Saga Pattern:"
    puts "=" * 30
    
    saga_manager = SagaManager.new
    
    # Define saga
    order_saga = OrderSaga.new
    saga_manager.register_saga(order_saga)
    
    # Start saga
    saga_manager.start_saga('order-saga', {
      order_id: 'order-789',
      user_id: 'user-123',
      items: ['item1', 'item2'],
      total: 99.99
    })
    
    puts "\nSaga Features:"
    puts "- Long-running transactions"
    puts "- Compensation actions"
    puts "- Event orchestration"
    puts "- Error handling"
    puts "- State management"
  end
  
  def self.demonstrate_event_replay
    puts "\n4. Event Replay Pattern:"
    puts "=" * 30
    
    event_store = EventStore.new
    replay_service = EventReplayService.new(event_store)
    
    # Store events
    events = [
      UserCreatedEvent.new('user-001', 'Alice', 'alice@example.com'),
      UserUpdatedEvent.new('user-001', 'Alice Smith'),
      UserDeletedEvent.new('user-001')
    ]
    
    events.each { |event| event_store.store(event) }
    
    # Replay events
    puts "Replaying events:"
    replay_service.replay_events('user-001') do |event|
      puts "  #{event.class.name}: #{event.id}"
    end
    
    # Replay with transformation
    puts "\nReplaying with transformation:"
    replay_service.replay_and_transform('user-001') do |event|
      puts "  Transformed: #{event.class.name}"
    end
    
    puts "\nEvent Replay Features:"
    puts "- Event replay"
    puts "- State reconstruction"
    puts "- Event transformation"
    puts "- Bulk processing"
    puts "- Error recovery"
  end
  
  def self.demonstrate_fan_out
    puts "\n5. Fan-out Pattern:"
    puts "=" * 30
    
    fan_out = FanOutPattern.new
    
    # Add subscribers
    fan_out.subscribe('user.created', EmailSubscriber.new)
    fan_out.subscribe('user.created', AnalyticsSubscriber.new)
    fan_out.subscribe('user.created', NotificationSubscriber.new)
    
    fan_out.subscribe('order.created', EmailSubscriber.new)
    fan_out.subscribe('order.created', InventorySubscriber.new)
    
    # Publish events
    user_event = UserCreatedEvent.new('user-002', 'Bob', 'bob@example.com')
    fan_out.publish(user_event)
    
    order_event = OrderCreatedEvent.new('order-002', 'user-002', ['item1'], 49.99)
    fan_out.publish(order_event)
    
    puts "\nFan-out Features:"
    puts "- Multiple subscribers"
    puts "- Event routing"
    puts "- Parallel processing"
    puts "- Error isolation"
    puts "- Scalability"
  end
  
  def self.demonstrate_circuit_breaker
    puts "\n6. Circuit Breaker Pattern:"
    puts "=" * 30
    
    circuit_breaker = WebhookCircuitBreaker.new('external-api', {
      failure_threshold: 3,
      recovery_timeout: 60
    })
    
    # Simulate webhook calls
    5.times do |i|
      begin
        circuit_breaker.call do
          simulate_webhook_call(i)
        end
        puts "Call #{i + 1}: Success"
      rescue => e
        puts "Call #{i + 1}: Failed - #{e.message}"
      end
    end
    
    puts "\nCircuit Breaker Features:"
    puts "- Failure threshold"
    puts "- Automatic recovery"
    puts "- Error isolation"
    puts "- Health monitoring"
    puts "- Graceful degradation"
  end
  
  private
  
  def self.simulate_webhook_call(attempt)
    sleep(0.1)
    raise "Webhook failed" if attempt < 3
    "Success"
  end
end

# Supporting classes for patterns
class EventSourcingStore
  def initialize
    @events = {}
    @mutex = Mutex.new
  end
  
  def store_event(aggregate_id, event)
    @mutex.synchronize do
      @events[aggregate_id] ||= []
      @events[aggregate_id] << event
    end
  end
  
  def get_events(aggregate_id)
    @mutex.synchronize do
      @events[aggregate_id] || []
    end
  end
end

class UserAggregate
  attr_reader :state
  
  def initialize(event_store = nil)
    @event_store = event_store
    @state = {}
    @uncommitted_events = []
  end
  
  def create(name, email)
    apply_event(UserCreatedEvent.new(SecureRandom.uuid, name, email))
  end
  
  def update_name(name)
    apply_event(UserUpdatedEvent.new(@state[:id], name, @state[:email]))
  end
  
  def update_email(email)
    apply_event(UserUpdatedEvent.new(@state[:id], @state[:name], email))
  end
  
  def replay_events(aggregate_id)
    events = @event_store.get_events(aggregate_id)
    events.each { |event| apply_event(event, replay: true) }
  end
  
  private
  
  def apply_event(event, replay: false)
    case event
    when UserCreatedEvent
      @state = {
        id: event.id,
        name: event.name,
        email: event.email,
        version: 1
      }
    when UserUpdatedEvent
      @state[:name] = event.name
      @state[:email] = event.email
      @state[:version] += 1
    end
    
    @uncommitted_events << event unless replay
    @event_store&.store_event(@state[:id], event) unless replay
  end
end

class CommandBus
  def initialize
    @handlers = {}
  end
  
  def register(command_class, &block)
    @handlers[command_class] = block
  end
  
  def execute(command)
    handler = @handlers[command.class]
    raise "No handler for #{command.class}" unless handler
    
    handler.call(command)
  end
end

class EventBus
  def initialize
    @subscribers = {}
  end
  
  def subscribe(event_class, subscriber)
    @subscribers[event_class] ||= []
    @subscribers[event_class] << subscriber
  end
  
  def publish(event)
    subscribers = @subscribers[event.class] || []
    subscribers.each { |subscriber| subscriber.handle(event) }
  end
end

class ReadRepository
  def initialize
    @projections = {}
  end
  
  def save_user_projection(event)
    @projections[event.id] = {
      id: event.id,
      name: event.name,
      email: event.email,
      created_at: Time.now
    }
  end
  
  def get_user_projection(user_id)
    @projections[user_id]
  end
end

# Event classes
class UserCreatedEvent
  attr_reader :id, :name, :email
  
  def initialize(id, name, email)
    @id = id
    @name = name
    @email = email
  end
end

class UserUpdatedEvent
  attr_reader :id, :name, :email
  
  def initialize(id, name, email)
    @id = id
    @name = name
    @email = email
  end
end

class UserDeletedEvent
  attr_reader :id
  
  def initialize(id)
    @id = id
  end
end

class CreateUserCommand
  attr_reader :name, :email
  
  def initialize(name, email)
    @name = name
    @email = email
  end
end

# Saga implementation
class SagaManager
  def initialize
    @sagas = {}
    @running_sagas = {}
  end
  
  def register_saga(saga_class)
    @sagas[saga_class.name] = saga_class
  end
  
  def start_saga(saga_type, data)
    saga_class = @sagas[saga_type]
    return unless saga_class
    
    saga = saga_class.new
    saga.start(data)
    
    @running_sagas[saga.id] = saga
    
    puts "Started saga: #{saga_type} (#{saga.id})"
  end
end

class OrderSaga
  def initialize
    @id = SecureRandom.uuid
    @state = :started
  end
  
  attr_reader :id
  
  def start(data)
    @data = data
    @state = :processing
    
    # Simulate saga steps
    create_order
    reserve_inventory
    process_payment
    send_confirmation
    
    @state = :completed
    puts "Saga completed: #{@id}"
  end
  
  private
  
  def create_order
    puts "  Creating order: #{@data[:order_id]}"
  end
  
  def reserve_inventory
    puts "  Reserving inventory for: #{@data[:items]}"
  end
  
  def process_payment
    puts "  Processing payment: $#{@data[:total]}"
  end
  
  def send_confirmation
    puts "  Sending confirmation to user: #{@data[:user_id]}"
  end
end

# Event replay service
class EventReplayService
  def initialize(event_store)
    @event_store = event_store
  end
  
  def replay_events(aggregate_id, &block)
    events = @event_store.get_events(aggregate_id)
    events.each(&block)
  end
  
  def replay_and_transform(aggregate_id, &block)
    events = @event_store.get_events(aggregate_id)
    events.each do |event|
      transformed_event = transform_event(event)
      block.call(transformed_event)
    end
  end
  
  private
  
  def transform_event(event)
    # Simulate event transformation
    OpenStruct.new(
      id: event.id,
      type: event.class.name,
      transformed_at: Time.now,
      data: event
    )
  end
end

# Fan-out pattern
class FanOutPattern
  def initialize
    @subscribers = {}
  end
  
  def subscribe(event_type, subscriber)
    @subscribers[event_type] ||= []
    @subscribers[event_type] << subscriber
  end
  
  def publish(event)
    subscribers = @subscribers[event.class] || []
    
    subscribers.each do |subscriber|
      Thread.new do
        begin
          subscriber.handle(event)
        rescue => e
          puts "Subscriber error: #{e.message}"
        end
      end
    end
  end
end

# Subscriber classes
class EmailSubscriber
  def handle(event)
    puts "  Email: Sending notification for #{event.class.name}"
  end
end

class AnalyticsSubscriber
  def handle(event)
    puts "  Analytics: Tracking #{event.class.name}"
  end
end

class NotificationSubscriber
  def handle(event)
    puts "  Notification: Processing #{event.class.name}"
  end
end

class InventorySubscriber
  def handle(event)
    puts "  Inventory: Updating for #{event.class.name}"
  end
end

# Circuit breaker for webhooks
class WebhookCircuitBreaker
  def initialize(name, options = {})
    @name = name
    @failure_threshold = options[:failure_threshold] || 5
    @recovery_timeout = options[:recovery_timeout] || 60
    @state = :closed
    @failure_count = 0
    @last_failure_time = nil
    @mutex = Mutex.new
  end
  
  def call(&block)
    @mutex.synchronize do
      case @state
      when :open
        if Time.now - @last_failure_time > @recovery_timeout
          @state = :half_open
        else
          raise "Circuit breaker #{@name} is open"
        end
      end
    end
    
    begin
      result = yield
      reset
      result
    rescue => e
      record_failure
      raise
    end
  end
  
  private
  
  def record_failure
    @mutex.synchronize do
      @failure_count += 1
      @last_failure_time = Time.now
      
      if @failure_count >= @failure_threshold
        @state = :open
      end
    end
  end
  
  def reset
    @mutex.synchronize do
      @failure_count = 0
      @state = :closed
    end
  end
end

# Additional event classes
class OrderCreatedEvent
  attr_reader :id, :user_id, :items, :total
  
  def initialize(id, user_id, items, total)
    @id = id
    @user_id = user_id
    @items = items
    @total = total
  end
end
```

## 🔐 Webhook Security

### 5. Security Implementation

Webhook security best practices:

```ruby
class WebhookSecurity
  def self.demonstrate_security
    puts "Webhook Security Demonstration:"
    puts "=" * 50
    
    # 1. Signature Generation and Verification
    demonstrate_signatures
    
    # 2. HTTPS Enforcement
    demonstrate_https_enforcement
    
    # 3. IP Whitelisting
    demonstrate_ip_whitelisting
    
    # 4. Rate Limiting
    demonstrate_security_rate_limiting
    
    # 5. Payload Encryption
    demonstrate_encryption
    
    # 6. Replay Attack Prevention
    demonstrate_replay_prevention
  end
  
  def self.demonstrate_signatures
    puts "\n1. Signature Generation and Verification:"
    puts "=" * 30
    
    # Generate signature
    payload = { user_id: 123, action: 'created' }
    secret = 'webhook_secret_123'
    
    signature_generator = WebhookSignatureGenerator.new
    signature = signature_generator.generate_signature(payload, secret)
    puts "Generated signature: #{signature}"
    
    # Verify signature
    signature_verifier = WebhookSignatureVerifier.new
    verification = signature_verifier.verify_signature(payload, secret, signature)
    puts "Signature verification: #{verification[:valid]}"
    
    # Test with wrong secret
    wrong_verification = signature_verifier.verify_signature(payload, 'wrong_secret', signature)
    puts "Wrong secret verification: #{wrong_verification[:valid]}"
    
    puts "\nSignature Features:"
    puts "- HMAC-based signatures"
    puts "- SHA-256 hashing"
    puts "- Secret key management"
    puts "- Tamper detection"
    puts "- Timestamp validation"
  end
  
  def self.demonstrate_https_enforcement
    puts "\n2. HTTPS Enforcement:"
    puts "=" * 30
    
    https_enforcer = WebhookHTTPSEnforcer.new
    
    # Test HTTP request
    http_request = OpenStruct.new(scheme: 'http', host: 'webhook.example.com')
    response = https_enforcer.check_request(http_request)
    puts "HTTP request: #{response[:allowed] ? 'Allowed' : 'Blocked'}"
    
    # Test HTTPS request
    https_request = OpenStruct.new(scheme: 'https', host: 'webhook.example.com')
    response = https_enforcer.check_request(https_request)
    puts "HTTPS request: #{response[:allowed] ? 'Allowed' : 'Blocked'}"
    
    # Get security headers
    headers = https_enforcer.get_security_headers
    puts "Security headers: #{headers}"
    
    puts "\nHTTPS Features:"
    puts "- Protocol enforcement"
    puts "- HSTS support"
    puts "- Certificate validation"
    puts "- Mixed content prevention"
    puts "- Security headers"
  end
  
  def self.demonstrate_ip_whitelisting
    puts "\n3. IP Whitelisting:"
    puts "=" * 30
    
    ip_whitelist = WebhookIPWhitelist.new([
      '192.168.1.0/24',
      '10.0.0.0/8',
      '203.0.113.5'
    ])
    
    # Test allowed IPs
    allowed_ips = ['192.168.1.100', '10.0.0.50', '203.0.113.5']
    allowed_ips.each do |ip|
      result = ip_whitelist.allowed?(ip)
      puts "IP #{ip}: #{result ? 'Allowed' : 'Blocked'}"
    end
    
    # Test blocked IPs
    blocked_ips = ['192.168.2.100', '172.16.0.1', '203.0.113.6']
    blocked_ips.each do |ip|
      result = ip_whitelist.allowed?(ip)
      puts "IP #{ip}: #{result ? 'Allowed' : 'Blocked'}"
    end
    
    puts "\nIP Whitelisting Features:"
    puts "- CIDR notation support"
    puts "- Individual IP addresses"
    puts "- Network range matching"
    puts "- Dynamic updates"
    puts "- Access logging"
  end
  
  def self.demonstrate_security_rate_limiting
    puts "\n4. Security Rate Limiting:"
    puts "=" * 30
    
    rate_limiter = WebhookSecurityRateLimiter.new(
      max_requests: 10,
      time_window: 60,
      burst_capacity: 5
    )
    
    # Test rate limiting
    15.times do |i|
      client_ip = "192.168.1.#{i % 3 + 1}"
      
      if rate_limiter.allow_request?(client_ip)
        puts "Request #{i + 1} from #{client_ip}: Allowed"
      else
        puts "Request #{i + 1} from #{client_ip}: Rate limited"
      end
    end
    
    puts "\nSecurity Rate Limiting Features:"
    puts "- Per-IP rate limiting"
    puts "- Burst capacity"
    puts "- Sliding window"
    puts "- Automatic reset"
    puts "- Violation logging"
  end
  
  def self.demonstrate_encryption
    puts "\n5. Payload Encryption:"
    puts "=" * 30
    
    encryptor = WebhookPayloadEncryptor.new('encryption_key_123')
    
    # Encrypt payload
    payload = { user_id: 123, sensitive_data: 'secret' }
    encrypted = encryptor.encrypt(payload)
    puts "Encrypted payload: #{encrypted[0..50]}..."
    
    # Decrypt payload
    decrypted = encryptor.decrypt(encrypted)
    puts "Decrypted payload: #{decrypted}"
    
    # Test with wrong key
    wrong_decryptor = WebhookPayloadEncryptor.new('wrong_key')
    begin
      wrong_decryptor.decrypt(encrypted)
    rescue => e
      puts "Decryption with wrong key failed: #{e.message}"
    end
    
    puts "\nEncryption Features:"
    puts "- AES-256 encryption"
    puts "- Key management"
    puts "- Secure random IV"
    puts "- Data integrity"
    puts "- Secure disposal"
  end
  
  def self.demonstrate_replay_prevention
    puts "\n6. Replay Attack Prevention:"
    puts "=" * 30
    
    replay_preventer = WebhookReplayPreventer.new
    
    # First event
    event_id = 'evt_123'
    payload = { user_id: 123, action: 'created' }
    
    result1 = replay_preventer.process_event(event_id, payload)
    puts "First processing: #{result1[:success] ? 'Success' : 'Failed'}"
    
    # Duplicate event (replay attack)
    result2 = replay_preventer.process_event(event_id, payload)
    puts "Duplicate processing: #{result2[:success] ? 'Success' : 'Failed'}"
    puts "Message: #{result2[:message]}"
    
    # Different event
    result3 = replay_preventer.process_event('evt_456', payload)
    puts "Different event: #{result3[:success] ? 'Success' : 'Failed'}"
    
    puts "\nReplay Prevention Features:"
    puts "- Event ID tracking"
    puts "- Timestamp validation"
    puts "- Duplicate detection"
    puts "- Automatic cleanup"
    puts "- Attack logging"
  end
end

class WebhookSignatureGenerator
  def initialize(algorithm = 'sha256')
    @algorithm = algorithm
  end
  
  def generate_signature(payload, secret)
    payload_string = payload.is_a?(String) ? payload : payload.to_json
    OpenSSL::HMAC.hexdigest(@algorithm, secret, payload_string)
  end
end

class WebhookSignatureVerifier
  def initialize(algorithm = 'sha256')
    @algorithm = algorithm
  end
  
  def verify_signature(payload, secret, signature)
    expected_signature = OpenSSL::HMAC.hexdigest(@algorithm, secret, payload.to_json)
    
    {
      valid: ActiveSupport::SecurityUtils.secure_compare(expected_signature, signature),
      algorithm: @algorithm
    }
  end
end

class WebhookHTTPSEnforcer
  def initialize
    @hsts_max_age = 31536000
    @hsts_include_subdomains = true
    @hsts_preload = false
  end
  
  def check_request(request)
    if request.scheme == 'https'
      { allowed: true }
    else
      { allowed: false, reason: 'HTTP not allowed' }
    end
  end
  
  def get_security_headers
    {
      'Strict-Transport-Security' => "max-age=#{@hsts_max_age}; includeSubDomains" if @hsts_include_subdomains,
      'X-Content-Type-Options' => 'nosniff',
      'X-Frame-Options' => 'DENY',
      'X-XSS-Protection' => '1; mode=block'
    }.compact
  end
end

class WebhookIPWhitelist
  def initialize(allowed_networks)
    @allowed_networks = allowed_networks.map { |network| IPAddr.new(network) }
  end
  
  def allowed?(ip_address)
    ip = IPAddr.new(ip_address)
    @allowed_networks.any? { |network| network.include?(ip) }
  end
  
  def add_network(network)
    @allowed_networks << IPAddr.new(network)
  end
  
  def remove_network(network)
    @allowed_networks.reject! { |n| n == IPAddr.new(network) }
  end
end

class WebhookSecurityRateLimiter
  def initialize(max_requests:, time_window:, burst_capacity:)
    @max_requests = max_requests
    @time_window = time_window
    @burst_capacity = burst_capacity
    @clients = {}
    @mutex = Mutex.new
  end
  
  def allow_request?(client_id)
    @mutex.synchronize do
      now = Time.now
      client_data = @clients[client_id] ||= { requests: [], tokens: @burst_capacity }
      
      # Clean old requests
      client_data[:requests].reject! { |time| now - time > @time_window }
      
      # Check if allowed
      if client_data[:requests].length < @max_requests && client_data[:tokens] > 0
        client_data[:requests] << now
        client_data[:tokens] -= 1
        true
      else
        false
      end
    end
  end
end

class WebhookPayloadEncryptor
  def initialize(key)
    @cipher = OpenSSL::Cipher.new('aes-256-gcm')
    @key = Digest::SHA256.digest(key)[0, 32]
  end
  
  def encrypt(payload)
    @cipher.encrypt
    @cipher.key = @key
    
    iv = @cipher.random_iv
    
    encrypted = @cipher.update(payload.to_json) + @cipher.final
    auth_tag = @cipher.auth_tag
    
    Base64.strict_encode64(iv + auth_tag + encrypted)
  end
  
  def decrypt(encrypted_data)
    decoded = Base64.strict_decode64(encrypted_data)
    
    iv = decoded[0, 12]
    auth_tag = decoded[12, 16]
    encrypted = decoded[28..-1]
    
    @cipher.decrypt
    @cipher.key = @key
    @cipher.iv = iv
    @cipher.auth_tag = auth_tag
    
    decrypted = @cipher.update(encrypted) + @cipher.final
    
    JSON.parse(decrypted)
  rescue => e
    raise "Decryption failed: #{e.message}"
  end
end

class WebhookReplayPreventer
  def initialize
    @processed_events = {}
    @mutex = Mutex.new
  end
  
  def process_event(event_id, payload)
    @mutex.synchronize do
      # Check if event already processed
      if @processed_events.key?(event_id)
        return {
          success: false,
          message: 'Event already processed (replay attack detected)'
        }
      end
      
      # Check timestamp (prevent old events)
      if payload[:timestamp] && Time.now - payload[:timestamp] > 300 # 5 minutes
        return {
          success: false,
          message: 'Event timestamp too old'
        }
      end
      
      # Mark as processed
      @processed_events[event_id] = {
        processed_at: Time.now,
        payload: payload
      }
      
      # Clean old events (older than 1 hour)
      cleanup_old_events
      
      {
        success: true,
        message: 'Event processed successfully'
      }
    end
  end
  
  private
  
  def cleanup_old_events
    cutoff = Time.now - 3600 # 1 hour
    @processed_events.delete_if { |_, data| data[:processed_at] < cutoff }
  end
end
```

## 📊 Webhook Monitoring

### 6. Monitoring and Analytics

Webhook performance tracking:

```ruby
class WebhookMonitor
  def initialize
    @metrics = {}
    @alerts = []
    @dashboard = WebhookDashboard.new
    @mutex = Mutex.new
  end
  
  def track_delivery(subscription_id, event_id, success, duration, error = nil)
    @mutex.synchronize do
      @metrics[subscription_id] ||= {
        total_deliveries: 0,
        successful_deliveries: 0,
        failed_deliveries: 0,
        total_duration: 0,
        errors: [],
        last_delivery: nil
      }
      
      metrics = @metrics[subscription_id]
      metrics[:total_deliveries] += 1
      metrics[:total_duration] += duration
      metrics[:last_delivery] = Time.now
      
      if success
        metrics[:successful_deliveries] += 1
      else
        metrics[:failed_deliveries] += 1
        metrics[:errors] << {
          event_id: event_id,
          error: error,
          timestamp: Time.now
        }
      end
      
      # Keep only last 100 errors
      metrics[:errors] = metrics[:errors].last(100)
      
      # Check for alerts
      check_alerts(subscription_id, metrics)
    end
  end
  
  def get_metrics(subscription_id = nil)
    @mutex.synchronize do
      if subscription_id
        @metrics[subscription_id]
      else
        @metrics.dup
      end
    end
  end
  
  def get_success_rate(subscription_id)
    metrics = get_metrics(subscription_id)
    return 0 unless metrics
    
    total = metrics[:total_deliveries]
    return 0 if total == 0
    
    (metrics[:successful_deliveries].to_f / total * 100).round(2)
  end
  
  def get_average_duration(subscription_id)
    metrics = get_metrics(subscription_id)
    return 0 unless metrics
    
    total = metrics[:total_deliveries]
    return 0 if total == 0
    
    (metrics[:total_duration].to_f / total).round(2)
  end
  
  def add_alert(condition, &block)
    alert = {
      id: SecureRandom.uuid,
      condition: condition,
      action: block,
      triggered: false,
      last_triggered: nil
    }
    
    @alerts << alert
    alert
  end
  
  def generate_report
    puts "Webhook Monitoring Report:"
    puts "=" * 50
    
    @mutex.synchronize do
      puts "Total Subscriptions: #{@metrics.length}"
      
      @metrics.each do |subscription_id, metrics|
        success_rate = get_success_rate(subscription_id)
        avg_duration = get_average_duration(subscription_id)
        
        puts "\nSubscription: #{subscription_id}"
        puts "  Total Deliveries: #{metrics[:total_deliveries]}"
        puts "  Success Rate: #{success_rate}%"
        puts "  Average Duration: #{avg_duration}ms"
        puts "  Failed Deliveries: #{metrics[:failed_deliveries]}"
        puts "  Last Delivery: #{metrics[:last_delivery]}"
        
        if metrics[:errors].any?
          puts "  Recent Errors:"
          metrics[:errors].last(3).each do |error|
            puts "    #{error[:timestamp]}: #{error[:error]}"
          end
        end
      end
      
      puts "\nAlerts Triggered: #{@alerts.count { |a| a[:triggered] }}"
    end
  end
  
  def self.demonstrate_monitoring
    puts "Webhook Monitoring Demonstration:"
    puts "=" * 50
    
    monitor = WebhookMonitor.new
    
    # Add alerts
    monitor.add_alert('high_failure_rate') do |subscription_id, metrics|
      failure_rate = (metrics[:failed_deliveries].to_f / metrics[:total_deliveries] * 100).round(2)
      failure_rate > 20
    end
    
    monitor.add_alert('slow_delivery') do |subscription_id, metrics|
      avg_duration = metrics[:total_duration].to_f / metrics[:total_deliveries]
      avg_duration > 5000 # 5 seconds
    end
    
    # Track deliveries
    subscription_id = 'sub_123'
    
    # Successful deliveries
    5.times do |i|
      monitor.track_delivery(subscription_id, "evt_#{i}", true, rand(100..500))
    end
    
    # Failed deliveries
    3.times do |i|
      monitor.track_delivery(subscription_id, "evt_#{i + 5}", false, rand(1000..2000), "Timeout error")
    end
    
    # Get metrics
    puts "\nMetrics for #{subscription_id}:"
    metrics = monitor.get_metrics(subscription_id)
    puts "Success rate: #{monitor.get_success_rate(subscription_id)}%"
    puts "Average duration: #{monitor.get_average_duration(subscription_id)}ms"
    
    # Generate report
    monitor.generate_report
    
    puts "\nMonitoring Features:"
    puts "- Delivery tracking"
    puts "- Success rate calculation"
    puts "- Performance metrics"
    puts "- Error tracking"
    puts "- Alert management"
    puts "- Real-time monitoring"
  end
  
  private
  
  def check_alerts(subscription_id, metrics)
    @alerts.each do |alert|
      if alert[:condition].call(subscription_id, metrics)
        unless alert[:triggered]
          alert[:action].call(subscription_id, metrics)
          alert[:triggered] = true
          alert[:last_triggered] = Time.now
        end
      else
        alert[:triggered] = false
      end
    end
  end
end

class WebhookDashboard
  def initialize
    @widgets = []
  end
  
  def add_widget(widget)
    @widgets << widget
  end
  
  def render
    puts "Webhook Dashboard:"
    puts "=" * 30
    
    @widgets.each do |widget|
      widget.render
    end
  end
end

class DashboardWidget
  def initialize(title, data_source)
    @title = title
    @data_source = data_source
  end
  
  def render
    puts "#{@title}: #{get_data}"
  end
  
  private
  
  def get_data
    case @data_source
    when :total_subscriptions
      rand(100..1000)
    when :success_rate
      rand(85..99)
    when :total_deliveries
      rand(1000..10000)
    when :active_webhooks
      rand(50..200)
    else
      'N/A'
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Webhook**: Create simple webhook system
2. **Event Emission**: Build event generator
3. **Webhook Receiver**: Create webhook handler
4. **Signature Verification**: Implement security

### Intermediate Exercises

1. **Webhook Provider**: Build complete provider
2. **Integration Patterns**: Implement patterns
3. **Security**: Add comprehensive security
4. **Monitoring**: Create monitoring system

### Advanced Exercises

1. **Enterprise Webhooks**: Production-ready system
2. **Multi-tenant**: Multi-tenant webhooks
3. **Real-time Processing**: Real-time event processing
4. **Analytics**: Advanced analytics and reporting

---

## 🎯 Summary

Webhook Integration in Ruby provides:

- **Webhook Fundamentals** - Core concepts and principles
- **Webhook Provider** - Event generation and delivery
- **Webhook Consumer** - Event processing and handling
- **Integration Patterns** - Common integration patterns
- **Webhook Security** - Security best practices
- **Webhook Monitoring** - Performance tracking and analytics

Master these webhook integration techniques for event-driven Ruby applications!
