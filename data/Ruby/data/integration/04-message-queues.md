# Message Queues in Ruby
# Comprehensive guide to asynchronous messaging and queue systems

# 📦 Message Queue Fundamentals

### 1. Message Queue Concepts

Core messaging principles:

```ruby
class MessageQueueFundamentals
  def self.explain_queue_concepts
    puts "Message Queue Fundamentals:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Message Queue",
        description: "Asynchronous communication system for messages",
        purpose: ["Decoupling", "Scalability", "Reliability", "Load balancing"],
        benefits: ["Loose coupling", "Fault tolerance", "Buffering", "Load distribution"],
        components: ["Producer", "Consumer", "Queue", "Message"]
      },
      {
        concept: "Publish-Subscribe",
        description: "Messaging pattern for one-to-many communication",
        pattern: ["Publisher", "Topic", "Subscriber", "Message broker"],
        benefits: ["Decoupling", "Scalability", "Flexibility", "Event-driven"],
        types: ["Topic-based", "Content-based", "Hierarchical"]
      },
      {
        concept: "Message Broker",
        description: "Middleware for message routing and delivery",
        responsibilities: ["Message routing", "Queue management", "Persistence", "Delivery guarantees"],
        features: ["Message persistence", "Routing", "Filtering", "Transformation"],
        examples: ["RabbitMQ", "Apache Kafka", "Redis", "ActiveMQ"]
      },
      {
        concept: "Delivery Guarantees",
        description: "Levels of message delivery reliability",
        levels: ["At most once", "At least once", "Exactly once"],
        tradeoffs: ["Performance", "Reliability", "Complexity", "Overhead"],
        implementations: ["Acknowledgments", "Idempotency", "Deduplication"]
      },
      {
        concept: "Message Patterns",
        description: "Common messaging patterns",
        patterns: ["Request-Reply", "Fire-and-Forget", "Competing Consumers", "Message Router"],
        use_cases: ["Synchronous communication", "Event notification", "Load distribution", "Content routing"],
        benefits: ["Flexibility", "Scalability", "Reliability", "Maintainability"]
      },
      {
        concept: "Queue Management",
        description: "Managing message queues and processing",
        aspects: ["Queue creation", "Message ordering", "Priority queues", "Dead letter queues"],
        strategies: ["FIFO", "Priority", "Round-robin", "Weighted"],
        monitoring: ["Queue depth", "Processing rate", "Error rate", "Latency"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Purpose: #{concept[:purpose].join(', ')}" if concept[:purpose]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Pattern: #{concept[:pattern].join(', ')}" if concept[:pattern]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Responsibilities: #{concept[:responsibilities].join(', ')}" if concept[:responsibilities]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Levels: #{concept[:levels].join(', ')}" if concept[:levels]
      puts "  Tradeoffs: #{concept[:tradeoffs].join(', ')}" if concept[:tradeoffs]
      puts "  Implementations: #{concept[:implementations].join(', ')}" if concept[:implementations]
      puts "  Use Cases: #{concept[:use_cases].join(', ')}" if concept[:use_cases]
      puts "  Aspects: #{concept[:aspects].join(', ')}" if concept[:aspects]
      puts "  Strategies: #{concept[:strategies].join(', ')}" if concept[:strategies]
      puts "  Monitoring: #{concept[:monitoring].join(', ')}" if concept[:monitoring]
      puts
    end
  end
  
  def self.message_lifecycle
    puts "\nMessage Lifecycle:"
    puts "=" * 50
    
    lifecycle = [
      {
        phase: "1. Production",
        description: "Producer creates and sends message",
        steps: ["Message creation", "Message serialization", "Queue selection", "Message sending"],
        considerations: ["Message format", "Routing keys", "Priority", "TTL"],
        validation: ["Message validation", "Size limits", "Format compliance"]
      },
      {
        phase: "2. Queuing",
        description: "Message is stored in queue",
        steps: ["Message reception", "Queue storage", "Persistence", "Ordering"],
        features: ["Message persistence", "FIFO ordering", "Priority handling", "Dead lettering"],
        management: ["Queue depth", "Message TTL", "Queue limits", "Storage optimization"]
      },
      {
        phase: "3. Routing",
        description: "Message is routed to appropriate consumer",
        methods: ["Direct routing", "Topic routing", "Pattern matching", "Content-based"],
        strategies: ["Load balancing", "Message filtering", "Content routing", "Dynamic routing"],
        optimization: ["Routing efficiency", "Load distribution", "Latency minimization"]
      },
      {
        phase: "4. Consumption",
        description: "Consumer receives and processes message",
        steps: ["Message delivery", "Deserialization", "Processing", "Acknowledgment"],
        patterns: ["Pull model", "Push model", "Batch processing", "Streaming"],
        considerations: ["Processing time", "Error handling", "Acknowledgment strategy"]
      },
      {
        phase: "5. Acknowledgment",
        description: "Consumer acknowledges message processing",
        types: ["Automatic acknowledgment", "Manual acknowledgment", "Negative acknowledgment"],
        strategies: ["Immediate ack", "Batch ack", "Delayed ack", "Conditional ack"],
        implications: ["Message removal", "Retry behavior", "Delivery guarantee"]
      },
      {
        phase: "6. Cleanup",
        description: "Message cleanup and maintenance",
        activities: ["Message deletion", "Queue cleanup", "Dead letter handling", "Storage optimization"],
        policies: ["TTL expiration", "Size limits", "Priority aging", "Archive policies"],
        monitoring: ["Queue health", "Storage usage", "Processing metrics"]
      }
    ]
    
    lifecycle.each do |phase|
      puts "#{phase[:phase]}: #{phase[:description]}"
      puts "  Steps: #{phase[:steps].join(', ')}" if phase[:steps]
      puts "  Considerations: #{phase[:considerations].join(', ')}" if phase[:considerations]
      puts "  Validation: #{phase[:validation].join(', ')}" if phase[:validation]
      puts "  Features: #{phase[:features].join(', ')}" if phase[:features]
      puts "  Management: #{phase[:management].join(', ')}" if phase[:management]
      puts "  Methods: #{phase[:methods].join(', ')}" if phase[:methods]
      puts "  Strategies: #{phase[:strategies].join(', ')}" if phase[:strategies]
      puts "  Optimization: #{phase[:optimization].join(', ')}" if phase[:optimization]
      puts "  Patterns: #{phase[:patterns].join(', ')}" if phase[:patterns]
      puts "  Types: #{phase[:types].join(', ')}" if phase[:types]
      puts "  Activities: #{phase[:activities].join(', ')}" if phase[:activities]
      puts "  Policies: #{phase[:policies].join(', ')}" if phase[:policies]
      puts "  Implications: #{phase[:implications].join(', ')}" if phase[:implications]
      puts
    end
  end
  
  def self.queue_types
    puts "\nQueue Types:"
    puts "=" * 50
    
    types = [
      {
        type: "Point-to-Point Queue",
        description: "One producer, one consumer",
        characteristics: ["Single consumer", "Message ordering", "Load distribution"],
        use_cases: ["Task processing", "Command processing", "Sequential processing"],
        examples: ["Task queue", "Command queue", "Work queue"]
      },
      {
        type: "Publish-Subscribe",
        description: "One producer, multiple consumers",
        characteristics: ["Multiple consumers", "Decoupled", "Event-driven"],
        use_cases: ["Event notification", "Broadcasting", "Fan-out"],
        examples: ["Event bus", "Notification system", "News feed"]
      },
      {
        type: "Priority Queue",
        description: "Messages processed by priority",
        characteristics: ["Priority ordering", "High priority first", "Weighted processing"],
        use_cases: ["Urgent tasks", "VIP processing", "Resource allocation"],
        examples: ["Emergency queue", "VIP queue", "Resource queue"]
      },
      {
        type: "Dead Letter Queue",
        description: "Failed messages for later processing",
        characteristics: ["Error handling", "Retry management", "Manual inspection"],
        use_cases: ["Error recovery", "Debugging", "Manual processing"],
        examples: ["Error queue", "Retry queue", "Inspection queue"]
      },
      {
        type: "Delay Queue",
        description: "Delayed message processing",
        characteristics: ["Delayed delivery", "Scheduled processing", "Time-based"],
        use_cases: ["Scheduled tasks", "Delayed notifications", "Batch processing"],
        examples: ["Scheduler queue", "Delay queue", "Timer queue"]
      },
      {
        type: "Ring Buffer Queue",
        description: "Fixed-size circular buffer",
        characteristics: ["Fixed size", "Overwrite old", "Memory efficient"],
        use_cases: ["Real-time data", "Streaming", "High throughput"],
        examples: ["Stream queue", "Real-time queue", "Buffer queue"]
      }
    ]
    
    types.each do |type|
      puts "#{type[:type]}:"
      puts "  Description: #{type[:description]}"
      puts "  Characteristics: #{type[:characteristics].join(', ')}"
      puts "  Use Cases: #{type[:use_cases].join(', ')}"
      puts "  Examples: #{type[:examples].join(', ')}"
      puts
    end
  end
  
  def self.messaging_best_practices
    puts "\nMessaging Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Message Design",
        description: "Design effective message structures",
        guidelines: [
          "Keep messages small",
          "Use consistent format",
          "Include metadata",
          "Version messages",
          "Avoid binary data"
        ],
        benefits: ["Performance", "Maintainability", "Compatibility", "Debugging"]
      },
      {
        practice: "Error Handling",
        description: "Handle errors gracefully",
        guidelines: [
          "Use dead letter queues",
          "Implement retry logic",
          "Log errors",
          "Monitor failures",
          "Have fallback strategies"
        ],
        benefits: ["Reliability", "Debugging", "Recovery", "Monitoring"]
      },
      {
        practice: "Performance",
        description: "Optimize message processing",
        guidelines: [
          "Batch processing",
          "Compression",
          "Connection pooling",
          "Async processing",
          "Monitor metrics"
        ],
        benefits: ["Speed", "Scalability", "Efficiency", "Resource usage"]
      },
      {
        practice: "Security",
        description: "Secure message communication",
        guidelines: [
          "Encrypt sensitive data",
          "Use authentication",
          "Implement authorization",
          "Validate messages",
          "Audit trails"
        ],
        benefits: ["Security", "Compliance", "Trust", "Auditability"]
      },
      {
        practice: "Monitoring",
        description: "Monitor queue health and performance",
        guidelines: [
          "Track queue depth",
          "Monitor processing time",
          "Alert on failures",
          "Log metrics",
          "Dashboard visibility"
        ],
        benefits: ["Visibility", "Proactive management", "Performance", "Reliability"]
      },
      {
        practice: "Testing",
        description: "Test messaging systems thoroughly",
        guidelines: [
          "Unit test message processing",
          "Integration test flows",
          "Load test performance",
          "Test error scenarios",
          "Monitor test coverage"
        ],
        benefits: ["Quality", "Reliability", "Performance", "Confidence"]
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
  
  # Run queue fundamentals
  explain_queue_concepts
  message_lifecycle
  queue_types
  messaging_best_practices
end
```

### 2. In-Memory Queue

Simple queue implementation:

```ruby
class InMemoryQueue
  def initialize(name, options = {})
    @name = name
    @max_size = options[:max_size] || 1000
    @messages = []
    @consumers = []
    @mutex = Mutex.new
    @stats = {
      enqueued: 0,
      dequeued: 0,
      rejected: 0,
      errors: 0
    }
  end
  
  attr_reader :name, :stats
  
  def enqueue(message, priority = 0)
    @mutex.synchronize do
      if @messages.length >= @max_size
        @stats[:rejected] += 1
        return false
      end
      
      message_wrapper = {
        id: SecureRandom.uuid,
        payload: message,
        priority: priority,
        enqueued_at: Time.now,
        attempts: 0
      }
      
      # Insert by priority (higher priority first)
      insert_index = @messages.find_index { |msg| msg[:priority] < priority }
      if insert_index
        @messages.insert(insert_index, message_wrapper)
      else
        @messages << message_wrapper
      end
      
      @stats[:enqueued] += 1
      
      # Notify consumers
      notify_consumers
      
      true
    end
  end
  
  def dequeue(timeout = nil)
    @mutex.synchronize do
      message = @messages.shift
      
      if message
        @stats[:dequeued] += 1
        message
      else
        nil
      end
    end
  end
  
  def peek
    @mutex.synchronize do
      @messages.first
    end
  end
  
  def size
    @mutex.synchronize do
      @messages.length
    end
  end
  
  def empty?
    size == 0
  end
  
  def full?
    size >= @max_size
  end
  
  def subscribe(consumer)
    @mutex.synchronize do
      @consumers << consumer
    end
  end
  
  def unsubscribe(consumer)
    @mutex.synchronize do
      @consumers.delete(consumer)
    end
  end
  
  def clear
    @mutex.synchronize do
      @messages.clear
      @stats[:dequeued] = 0
    end
  end
  
  def self.demonstrate_in_memory_queue
    puts "In-Memory Queue Demonstration:"
    puts "=" * 50
    
    # Create queue
    queue = InMemoryQueue.new('task_queue', max_size: 5)
    
    # Create consumers
    consumer1 = QueueConsumer.new('consumer1')
    consumer2 = QueueConsumer.new('consumer2')
    
    queue.subscribe(consumer1)
    queue.subscribe(consumer2)
    
    # Enqueue messages
    puts "Enqueuing messages:"
    
    messages = [
      { task: 'send_email', data: { to: 'user@example.com', subject: 'Hello' } },
      { task: 'process_payment', data: { amount: 99.99, currency: 'USD' } },
      { task: 'generate_report', data: { type: 'monthly', user_id: 123 } },
      { task: 'update_cache', data: { key: 'user_123', value: 'updated' } },
      { task: 'send_notification', data: { message: 'Task completed' } },
      { task: 'cleanup_temp', data: { older_than: 3600 } }
    ]
    
    messages.each_with_index do |message, i|
      priority = i < 2 ? 10 : 5 # First two messages have higher priority
      success = queue.enqueue(message, priority)
      puts "  Message #{i + 1}: #{success ? 'Enqueued' : 'Rejected'} (priority: #{priority})"
    end
    
    # Process messages
    puts "\nProcessing messages:"
    
    3.times do |i|
      message = queue.dequeue
      if message
        puts "  Dequeued: #{message[:payload][:task]} (priority: #{message[:priority]})"
        puts "    Enqueued at: #{message[:enqueued_at]}"
      else
        puts "  Queue is empty"
      end
    end
    
    # Check queue status
    puts "\nQueue Status:"
    puts "  Size: #{queue.size}"
    puts "  Empty: #{queue.empty?}"
    puts "  Full: #{queue.full?}"
    puts "  Stats: #{queue.stats}"
    
    # Peek at next message
    next_message = queue.peek
    if next_message
      puts "  Next message: #{next_message[:payload][:task]}"
    else
      puts "  No next message"
    end
    
    puts "\nIn-Memory Queue Features:"
    puts "- Priority-based ordering"
    puts "- Thread-safe operations"
    puts "- Consumer notification"
    puts "- Queue size limits"
    puts "- Statistics tracking"
    puts "- Message metadata"
  end
  
  private
  
  def notify_consumers
    return if @consumers.empty?
    
    @consumers.each do |consumer|
      Thread.new do
        begin
          consumer.message_available(@name)
        rescue => e
          @stats[:errors] += 1
        end
      end
    end
  end
end

class QueueConsumer
  def initialize(name)
    @name = name
    @processed = 0
  end
  
  attr_reader :name, :processed
  
  def message_available(queue_name)
    puts "  #{@name}: Message available in #{queue_name}"
    @processed += 1
  end
end

class PriorityQueue
  def initialize(name, options = {})
    @name = name
    @max_size = options[:max_size] || 1000
    @heap = []
    @mutex = Mutex.new
    @stats = {
      enqueued: 0,
      dequeued: 0,
      rejected: 0
    }
  end
  
  attr_reader :name, :stats
  
  def enqueue(message, priority = 0)
    @mutex.synchronize do
      if @heap.length >= @max_size
        @stats[:rejected] += 1
        return false
      end
      
      # Min-heap based on priority (lower priority number = higher priority)
      heap_entry = {
        priority: priority,
        message: message,
        enqueued_at: Time.now,
        id: SecureRandom.uuid
      }
      
      @heap << heap_entry
      heapify_up(@heap.length - 1)
      
      @stats[:enqueued] += 1
      true
    end
  end
  
  def dequeue
    @mutex.synchronize do
      return nil if @heap.empty?
      
      # Swap root with last element
      root = @heap[0]
      last = @heap.pop
      
      unless @heap.empty?
        @heap[0] = last
        heapify_down(0)
      end
      
      @stats[:dequeued] += 1
      root[:message]
    end
  end
  
  def size
    @mutex.synchronize do
      @heap.length
    end
  end
  
  def empty?
    size == 0
  end
  
  def self.demonstrate_priority_queue
    puts "Priority Queue Demonstration:"
    puts "=" * 50
    
    # Create priority queue
    queue = PriorityQueue.new('priority_queue')
    
    # Enqueue messages with different priorities
    puts "Enqueuing messages:"
    
    tasks = [
      { task: 'urgent_email', priority: 1 },
      { task: 'normal_report', priority: 5 },
      { task: 'critical_payment', priority: 0 },
      { task: 'low_priority_cleanup', priority: 10 },
      { task: 'urgent_notification', priority: 2 },
      { task: 'normal_backup', priority: 5 }
    ]
    
    tasks.each do |task|
      success = queue.enqueue(task[:task], task[:priority])
      puts "  #{task[:task]}: #{success ? 'Enqueued' : 'Rejected'} (priority: #{task[:priority]})"
    end
    
    # Dequeue messages (should come out in priority order)
    puts "\nDequeuing messages (priority order):"
    
    while !queue.empty?
      message = queue.dequeue
      puts "  #{message} (priority determined by heap)"
    end
    
    puts "\nPriority Queue Features:"
    puts "- Heap-based implementation"
    puts "- Priority ordering"
    puts "- Efficient operations"
    puts "- Thread-safe"
    puts "- Statistics tracking"
  end
  
  private
  
  def heapify_up(index)
    return if index == 0
    
    parent_index = (index - 1) / 2
    
    if @heap[index][:priority] < @heap[parent_index][:priority]
      @heap[index], @heap[parent_index] = @heap[parent_index], @heap[index]
      heapify_up(parent_index)
    end
  end
  
  def heapify_down(index)
    left_child = 2 * index + 1
    right_child = 2 * index + 2
    smallest = index
    
    if left_child < @heap.length && @heap[left_child][:priority] < @heap[smallest][:priority]
      smallest = left_child
    end
    
    if right_child < @heap.length && @heap[right_child][:priority] < @heap[smallest][:priority]
      smallest = right_child
    end
    
    if smallest != index
      @heap[index], @heap[smallest] = @heap[smallest], @heap[index]
      heapify_down(smallest)
    end
  end
end

class DelayQueue
  def initialize(name, options = {})
    @name = name
    @max_size = options[:max_size] || 1000
    @messages = []
    @mutex = Mutex.new
    @stats = {
      enqueued: 0,
      dequeued: 0,
      rejected: 0
    }
    @processing_thread = nil
    @running = false
  end
  
  attr_reader :name, :stats
  
  def enqueue(message, delay_seconds)
    @mutex.synchronize do
      if @messages.length >= @max_size
        @stats[:rejected] += 1
        return false
      end
      
      execute_at = Time.now + delay_seconds
      
      message_wrapper = {
        id: SecureRandom.uuid,
        payload: message,
        delay: delay_seconds,
        execute_at: execute_at,
        enqueued_at: Time.now
      }
      
      # Insert in order of execution time
      insert_index = @messages.find_index { |msg| msg[:execute_at] > execute_at }
      if insert_index
        @messages.insert(insert_index, message_wrapper)
      else
        @messages << message_wrapper
      end
      
      @stats[:enqueued] += 1
      true
    end
  end
  
  def start_processing
    return if @running
    
    @running = true
    @processing_thread = Thread.new { process_delayed_messages }
    
    puts "Delay queue #{@name} started"
  end
  
  def stop_processing
    @running = false
    @processing_thread&.join
    puts "Delay queue #{@name} stopped"
  end
  
  def size
    @mutex.synchronize do
      @messages.length
    end
  end
  
  def ready_messages
    @mutex.synchronize do
      now = Time.now
      @messages.select { |msg| msg[:execute_at] <= now }
    end
  end
  
  def self.demonstrate_delay_queue
    puts "Delay Queue Demonstration:"
    puts "=" * 50
    
    # Create delay queue
    queue = DelayQueue.new('delay_queue')
    
    # Start processing
    queue.start_processing
    
    # Enqueue messages with different delays
    puts "Enqueuing delayed messages:"
    
    tasks = [
      { task: 'send_welcome_email', delay: 5 },
      { task: 'generate_report', delay: 10 },
      { task: 'cleanup_temp_files', delay: 3 },
      { task: 'send_reminder', delay: 15 },
      { task: 'update_statistics', delay: 8 }
    ]
    
    tasks.each do |task|
      success = queue.enqueue(task[:task], task[:delay])
      puts "  #{task[:task]}: #{success ? 'Enqueued' : 'Rejected'} (delay: #{task[:delay]}s)"
    end
    
    # Wait for messages to be ready
    puts "\nWaiting for messages to become ready..."
    sleep(20)
    
    # Check ready messages
    ready = queue.ready_messages
    puts "\nReady messages: #{ready.length}"
    ready.each do |msg|
      puts "  #{msg[:payload][:task]} (delayed for #{msg[:delay]}s)"
    end
    
    # Stop processing
    queue.stop_processing
    
    puts "\nDelay Queue Features:"
    puts "- Time-based delivery"
    puts "- Delayed processing"
    puts "- Automatic processing"
    puts "- Thread-safe operations"
    puts "- Statistics tracking"
  end
  
  private
  
  def process_delayed_messages
    while @running
      begin
        ready_messages = get_ready_messages
        
        ready_messages.each do |message|
          process_message(message)
        end
        
        sleep(1) # Check every second
      rescue => e
        puts "Error processing delayed messages: #{e.message}"
      end
    end
  end
  
  def get_ready_messages
    @mutex.synchronize do
      now = Time.now
      ready_messages = @messages.select { |msg| msg[:execute_at] <= now }
      
      # Remove ready messages from queue
      @messages.reject! { |msg| msg[:execute_at] <= now }
      
      ready_messages
    end
  end
  
  def process_message(message)
    puts "Processing delayed message: #{message[:payload][:task]}"
    puts "  Enqueued: #{message[:enqueued_at]}"
    puts "  Delayed: #{message[:delay]}s"
    puts "  Executed: #{Time.now}"
    
    @stats[:dequeued] += 1
  end
end
```

## 🔄 Redis Queue

### 3. Redis-based Queue

Redis queue implementation:

```ruby
class RedisQueue
  def initialize(name, redis_client = nil, options = {})
    @name = name
    @redis = redis_client || Redis.new
    @key_prefix = options[:key_prefix] || 'queue'
    @max_size = options[:max_size] || 1000
    @visibility_timeout = options[:visibility_timeout] || 30
    @stats = {
      enqueued: 0,
      dequeued: 0,
      rejected: 0,
      errors: 0
    }
  end
  
  attr_reader :name, :stats
  
  def enqueue(message, priority = 0)
    begin
      # Check queue size
      current_size = @redis.llen(queue_key).to_i
      
      if current_size >= @max_size
        @stats[:rejected] += 1
        return false
      end
      
      # Create message wrapper
      message_wrapper = {
        id: SecureRandom.uuid,
        payload: message,
        priority: priority,
        enqueued_at: Time.now.to_f,
        attempts: 0
      }
      
      # Add to priority queue (sorted set)
      @redis.zadd(priority_queue_key, priority, message_wrapper.to_json)
      
      @stats[:enqueued] += 1
      true
    rescue => e
      @stats[:errors] += 1
      puts "Error enqueuing message: #{e.message}"
      false
    end
  end
  
  def dequeue(timeout = nil)
    begin
      # Get highest priority message
      result = @redis.zrange(priority_queue_key, 0, 0, withscores: true)
      
      return nil if result.empty?
      
      message_json, priority = result.first
      message = JSON.parse(message_json)
      
      # Remove from queue
      @redis.zrem(priority_queue_key, message_json)
      
      @stats[:dequeued] += 1
      message
    rescue => e
      @stats[:errors] += 1
      puts "Error dequeuing message: #{e.message}"
      nil
    end
  end
  
  def size
    @redis.zcard(priority_queue_key).to_i
  end
  
  def empty?
    size == 0
  end
  
  def clear
    @redis.del(priority_queue_key)
    @stats[:dequeued] = 0
  end
  
  def peek
    result = @redis.zrange(priority_queue_key, 0, 0, withscores: true)
    return nil if result.empty?
    
    message_json, priority = result.first
    JSON.parse(message_json)
  end
  
  def self.demonstrate_redis_queue
    puts "Redis Queue Demonstration:"
    puts "=" * 50
    
    # Create Redis queue (simulated)
    queue = RedisQueue.new('redis_queue', nil, max_size: 10)
    
    # Enqueue messages
    puts "Enqueuing messages:"
    
    messages = [
      { task: 'send_email', data: { to: 'user@example.com' } },
      { task: 'process_payment', data: { amount: 99.99 } },
      { task: 'generate_report', data: { type: 'monthly' } },
      { task: 'update_cache', data: { key: 'user_123' } },
      { task: 'send_notification', data: { message: 'Task completed' } }
    ]
    
    messages.each_with_index do |message, i|
      priority = i < 2 ? 10 : 5
      success = queue.enqueue(message, priority)
      puts "  Message #{i + 1}: #{success ? 'Enqueued' : 'Rejected'} (priority: #{priority})"
    end
    
    # Dequeue messages
    puts "\nDequeuing messages:"
    
    while !queue.empty?
      message = queue.dequeue
      puts "  Dequeued: #{message['task']} (priority determined by sorted set)"
    end
    
    # Check queue status
    puts "\nQueue Status:"
    puts "  Size: #{queue.size}"
    puts "  Empty: #{queue.empty?}"
    puts "  Stats: #{queue.stats}"
    
    puts "\nRedis Queue Features:"
    puts "- Redis-based storage"
    puts "- Priority sorting (sorted set)"
    puts "- Persistent storage"
    puts "- Atomic operations"
    puts "- Statistics tracking"
    puts "- Thread-safe operations"
  end
  
  private
  
  def queue_key
    "#{@key_prefix}:#{@name}"
  end
  
  def priority_queue_key
    "#{queue_key}:priority"
  end
end

class RedisReliableQueue
  def initialize(name, redis_client = nil, options = {})
    @name = name
    @redis = redis_client || Redis.new
    @key_prefix = options[:key_prefix] || 'queue'
    @visibility_timeout = options[:visibility_timeout] || 30
    @max_attempts = options[:max_attempts] || 3
    @dead_letter_key = "#{@key_prefix}:#{@name}:dead_letter"
    @stats = {
      enqueued: 0,
      dequeued: 0,
      failed: 0,
      dead_lettered: 0
    }
  end
  
  attr_reader :name, :stats
  
  def enqueue(message)
    begin
      message_wrapper = {
        id: SecureRandom.uuid,
        payload: message,
        enqueued_at: Time.now.to_f,
        attempts: 0
      }
      
      # Add to queue
      @redis.lpush(queue_key, message_wrapper.to_json)
      
      @stats[:enqueued] += 1
      true
    rescue => e
      puts "Error enqueuing message: #{e.message}"
      false
    end
  end
  
  def dequeue(timeout = nil)
    begin
      # Pop from queue with timeout
      result = @redis.brpoplpush(queue_key, processing_key, timeout || 1)
      
      return nil unless result
      
      message_json = result
      message = JSON.parse(message_json)
      
      # Update attempts
      message['attempts'] += 1
      
      # Check if max attempts reached
      if message['attempts'] >= @max_attempts
        # Move to dead letter queue
        @redis.lpush(@dead_letter_key, message.to_json)
        @stats[:dead_lettered] += 1
        @stats[:failed] += 1
        return nil
      end
      
      # Return message for processing
      @stats[:dequeued] += 1
      message
    rescue => e
      puts "Error dequeuing message: #{e.message}"
      nil
    end
  end
  
  def acknowledge(message_id)
    begin
      # Remove from processing queue
      @redis.lrem(processing_key, 1, lambda { |msg| JSON.parse(msg)['id'] == message_id })
      true
    rescue => e
      puts "Error acknowledging message: #{e.message}"
      false
    end
  end
  
  def size
    @redis.llen(queue_key).to_i
  end
  
  def processing_size
    @redis.llen(processing_key).to_i
  end
  
  def dead_letter_size
    @redis.llen(@dead_letter_key).to_i
  end
  
  def self.demonstrate_reliable_queue
    puts "Redis Reliable Queue Demonstration:"
    puts "=" * 50
    
    # Create reliable queue
    queue = RedisReliableQueue.new('reliable_queue', nil, {
      visibility_timeout: 5,
      max_attempts: 3
    })
    
    # Enqueue messages
    puts "Enqueuing messages:"
    
    messages = [
      { task: 'send_email', data: { to: 'user@example.com' } },
      { task: 'process_payment', data: { amount: 99.99 } },
      { task: 'generate_report', data: { type: 'monthly' } }
    ]
    
    messages.each_with_index do |message, i|
      success = queue.enqueue(message)
      puts "  Message #{i + 1}: #{success ? 'Enqueued' : 'Failed'}"
    end
    
    # Dequeue and process messages
    puts "\nProcessing messages:"
    
    3.times do |i|
      message = queue.dequeue(1)
      if message
        puts "  Processing: #{message['task']}"
        puts "    Attempts: #{message['attempts']}"
        
        # Simulate processing (50% success rate)
        if rand > 0.5
          queue.acknowledge(message['id'])
          puts "    ✓ Acknowledged"
        else
          puts "    ✗ Processing failed (will retry)"
        end
      else
        puts "  No message available"
      end
    end
    
    # Check queue status
    puts "\nQueue Status:"
    puts "  Queue size: #{queue.size}"
    puts "  Processing size: #{queue.processing_size}"
    puts "  Dead letter size: #{queue.dead_letter_size}"
    puts "  Stats: #{queue.stats}"
    
    puts "\nReliable Queue Features:"
    puts "- Reliable delivery"
    puts "- Visibility timeout"
    puts "- Retry mechanism"
    puts "- Dead letter queue"
    puts "- Message acknowledgment"
    puts "- Failure handling"
  end
  
  private
  
  def queue_key
    "#{@key_prefix}:#{@name}"
  end
  
  def processing_key
    "#{@key_prefix}:#{@name}:processing"
  end
end

class RedisPubSub
  def initialize(redis_client = nil, options = {})
    @redis = redis_client || Redis.new
    @subscribers = {}
    @running = false
    @subscription_thread = nil
    @stats = {
      published: 0,
      received: 0,
      errors: 0
    }
  end
  
  attr_reader :stats
  
  def publish(channel, message)
    begin
      @redis.publish(channel, message.to_json)
      @stats[:published] += 1
      true
    rescue => e
      @stats[:errors] += 1
      puts "Error publishing message: #{e.message}"
      false
    end
  end
  
  def subscribe(channel, &block)
    @subscribers[channel] ||= []
    @subscribers[channel] << block
    
    start_subscription_thread unless @running
  end
  
  def unsubscribe(channel, block = nil)
    if block
      @subscribers[channel]&.delete(block)
    else
      @subscribers.delete(channel)
    end
  end
  
  def self.demonstrate_pub_sub
    puts "Redis Pub/Sub Demonstration:"
    puts "=" * 50
    
    # Create pub/sub system
    pubsub = RedisPubSub.new
    
    # Subscribe to channels
    pubsub.subscribe('notifications') do |message|
      puts "  Received notification: #{message['message']}"
    end
    
    pubsub.subscribe('events') do |message|
      puts "  Received event: #{message['event_type']}"
    end
    
    # Publish messages
    puts "\nPublishing messages:"
    
    pubsub.publish('notifications', {
      message: 'New user registered',
      user_id: 123,
      timestamp: Time.now
    })
    
    pubsub.publish('events', {
      event_type: 'user_created',
      data: { user_id: 123, name: 'John Doe' },
      timestamp: Time.now
    })
    
    pubsub.publish('notifications', {
      message: 'Order completed',
      order_id: 456,
      timestamp: Time.now
    })
    
    # Wait for messages to be processed
    sleep(1)
    
    puts "\nPub/Sub Features:"
    puts "- Channel-based messaging"
    puts "- Multiple subscribers"
    puts "- Real-time delivery"
    puts "- Message broadcasting"
    puts "- Statistics tracking"
  end
  
  private
  
  def start_subscription_thread
    @running = true
    @subscription_thread = Thread.new { listen_for_messages }
  end
  
  def listen_for_messages
    while @running
      begin
        # Subscribe to all channels
        @subscribers.keys.each do |channel|
          @redis.subscribe(channel) do |on, channel, message|
            if on == 'message'
              begin
                parsed_message = JSON.parse(message)
                @subscribers[channel].each { |block| block.call(parsed_message) }
                @stats[:received] += 1
              rescue => e
                @stats[:errors] += 1
                puts "Error processing message: #{e.message}"
              end
            end
          end
        end
        
        sleep(1) # Prevent busy loop
      rescue => e
        puts "Error in subscription thread: #{e.message}"
        sleep(5) # Wait before retrying
      end
    end
  end
end
```

## 🌐 Distributed Queue

### 4. Distributed Queue System

Multi-node queue implementation:

```ruby
class DistributedQueue
  def initialize(name, options = {})
    @name = name
    @nodes = []
    @current_node = 0
    @partition_count = options[:partition_count] || 3
    @replication_factor = options[:replication_factor] || 2
    @hash_ring = nil
    @stats = {
      enqueued: 0,
      dequeued: 0,
      failed: 0
    }
    @mutex = Mutex.new
  end
  
  def add_node(node_id, redis_client)
    node = QueueNode.new(node_id, redis_client)
    @nodes << node
    rebuild_hash_ring
  end
  
  def remove_node(node_id)
    @nodes.reject! { |node| node.id == node_id }
    rebuild_hash_ring
  end
  
  def enqueue(message, partition_key = nil)
    @mutex.synchronize do
      # Determine partition
      partition = determine_partition(partition_key)
      
      # Get primary and replica nodes
      primary_node, replica_nodes = get_nodes_for_partition(partition)
      
      begin
        # Enqueue to primary node
        success = primary_node.enqueue(message, partition)
        
        if success
          # Replicate to replica nodes
          replica_nodes.each do |replica|
            replica.enqueue(message, partition)
          end
          
          @stats[:enqueued] += 1
          return true
        else
          @stats[:failed] += 1
          return false
        end
      rescue => e
        @stats[:failed] += 1
        puts "Error enqueuing message: #{e.message}"
        false
      end
    end
  end
  
  def dequeue(partition = nil)
    @mutex.synchronize do
      # Select partition if not specified
      partition ||= rand(@partition_count)
      
      # Get primary node for partition
      primary_node, = get_nodes_for_partition(partition)
      
      begin
        message = primary_node.dequeue(partition)
        @stats[:dequeued] += 1 if message
        message
      rescue => e
        @stats[:failed] += 1
        puts "Error dequeuing message: #{e.message}"
        nil
      end
    end
  end
  
  def size
    @nodes.map { |node| node.size }.sum
  end
  
  def get_cluster_stats
    @mutex.synchronize do
      node_stats = @nodes.map do |node|
        {
          node_id: node.id,
          size: node.size,
          stats: node.stats
        }
      end
      
      {
        cluster_size: @nodes.length,
        partition_count: @partition_count,
        replication_factor: @replication_factor,
        nodes: node_stats,
        cluster_stats: @stats
      }
    end
  end
  
  def self.demonstrate_distributed_queue
    puts "Distributed Queue Demonstration:"
    puts "=" * 50
    
    # Create distributed queue
    queue = DistributedQueue.new('distributed_queue', {
      partition_count: 3,
      replication_factor: 2
    })
    
    # Add nodes
    puts "Adding nodes:"
    3.times do |i|
      node_id = "node_#{i + 1}"
      redis_client = MockRedisClient.new(node_id)
      queue.add_node(node_id, redis_client)
      puts "  Added node: #{node_id}"
    end
    
    # Enqueue messages with different partition keys
    puts "\nEnqueuing messages:"
    
    messages = [
      { task: 'send_email', user_id: 123 },
      { task: 'process_payment', user_id: 456 },
      { task: 'generate_report', user_id: 789 },
      { task: 'update_cache', user_id: 123 },
      { task: 'send_notification', user_id: 456 }
    ]
    
    messages.each_with_index do |message, i|
      partition_key = message[:user_id]
      success = queue.enqueue(message, partition_key)
      puts "  Message #{i + 1}: #{success ? 'Enqueued' : 'Failed'} (user_id: #{partition_key})"
    end
    
    # Dequeue messages
    puts "\nDequeuing messages:"
    
    3.times do |i|
      message = queue.dequeue
      if message
        puts "  Dequeued: #{message['task']}"
      else
        puts "  No message available"
      end
    end
    
    # Get cluster stats
    puts "\nCluster Statistics:"
    stats = queue.get_cluster_stats
    puts "  Cluster size: #{stats[:cluster_size]}"
    puts "  Partition count: #{stats[:partition_count]}"
    puts "  Replication factor: #{stats[:replication_factor]}"
    puts "  Total size: #{queue.size}"
    puts "  Cluster stats: #{stats[:cluster_stats]}"
    
    puts "\nDistributed Queue Features:"
    puts "- Multi-node support"
    puts "- Partitioning by key"
    "- Replication factor"
    "- Consistent hashing"
    "- Fault tolerance"
    "- Load balancing"
  end
  
  private
  
  def determine_partition(partition_key)
    if partition_key
      # Use consistent hashing to determine partition
      @hash_ring.node_for(partition_key.to_s).to_i % @partition_count
    else
      # Round-robin
      @current_node = (@current_node + 1) % @partition_count
      @current_node
    end
  end
  
  def get_nodes_for_partition(partition)
    # Get all nodes for this partition
    partition_nodes = @nodes.select.with_index { |node, i| i % @partition_count == partition }
    
    # First node is primary, rest are replicas
    primary_node = partition_nodes.first
    replica_nodes = partition_nodes[1..-1]
    
    [primary_node, replica_nodes]
  end
  
  def rebuild_hash_ring
    return if @nodes.empty?
    
    # Create consistent hash ring
    @hash_ring = ConsistentHashRing.new(@nodes.map(&:id))
  end
end

class QueueNode
  def initialize(id, redis_client)
    @id = id
    @redis = redis_client
    @stats = {
      enqueued: 0,
      dequeued: 0,
      failed: 0
    }
  end
  
  attr_reader :id, :stats
  
  def enqueue(message, partition)
    begin
      key = "queue:#{partition}:#{@id}"
      message_wrapper = {
        id: SecureRandom.uuid,
        payload: message,
        node_id: @id,
        partition: partition,
        enqueued_at: Time.now.to_f
      }
      
      @redis.lpush(key, message_wrapper.to_json)
      @stats[:enqueued] += 1
      true
    rescue => e
      @stats[:failed] += 1
      false
    end
  end
  
  def dequeue(partition)
    begin
      key = "queue:#{partition}:#{@id}"
      message_json = @redis.rpop(key)
      
      return nil unless message_json
      
      message = JSON.parse(message_json)
      @stats[:dequeued] += 1
      message
    rescue => e
      @stats[:failed] += 1
      nil
    end
  end
  
  def size
    @redis.keys("queue:*:#{@id}").sum do |key|
      @redis.llen(key)
    end
  end
end

class ConsistentHashRing
  def initialize(nodes)
    @ring = []
    @node_positions = {}
    
    # Create virtual nodes for better distribution
    virtual_nodes_per_node = 100
    
    nodes.each do |node|
      virtual_nodes_per_node.times do |i|
        virtual_node = "#{node}:#{i}"
        position = hash(virtual_node)
        
        @ring << { node: node, virtual_node: virtual_node, position: position }
        @node_positions[virtual_node] = node
      end
    end
    
    # Sort by position
    @ring.sort_by! { |entry| entry[:position] }
  end
  
  def node_for(key)
    hash_value = hash(key)
    
    # Find the first node with position >= hash_value
    @ring.each do |entry|
      if entry[:position] >= hash_value
        return entry[:node]
      end
    end
    
    # Wrap around to first node
    @ring.first[:node]
  end
  
  private
  
  def hash(key)
    # Simple hash function (in production, use better hash like SHA-256)
    key.sum { |char| char.ord } % 2**32
  end
end

class MockRedisClient
  def initialize(node_id)
    @node_id = node_id
    @data = {}
  end
  
  def lpush(key, value)
    @data[key] ||= []
    @data[key] << value
    @data[key].length
  end
  
  def rpop(key)
    @data[key] ||= []
    @data[key].shift
  end
  
  def llen(key)
    @data[key] ? @data[key].length : 0
  end
  
  def keys(pattern)
    @data.keys.select { |key| key.match?(pattern.gsub('*', '.*')) }
  end
  
  def publish(channel, message)
    # Simulate publish
    puts "  [#{@node_id}] Published to #{channel}: #{message}"
    true
  end
  
  def subscribe(channel, &block)
    # Simulate subscription
    puts "  [#{@node_id}] Subscribed to #{channel}"
    # In real implementation, this would block and receive messages
  end
end
```

## 📊 Queue Monitoring

### 5. Queue Monitoring

Performance monitoring and analytics:

```ruby
class QueueMonitor
  def initialize
    @queues = {}
    @metrics = {}
    @alerts = []
    @dashboard = QueueDashboard.new
    @mutex = Mutex.new
    @running = false
    @monitor_thread = nil
  end
  
  def register_queue(queue)
    @mutex.synchronize do
      @queues[queue.name] = queue
      @metrics[queue.name] = {
        total_messages: 0,
        processed_messages: 0,
        failed_messages: 0,
        average_processing_time: 0,
        queue_depth: 0,
        throughput: 0,
        error_rate: 0,
        last_updated: Time.now
      }
    end
  end
  
  def start_monitoring(interval = 5)
    return if @running
    
    @running = true
    @monitor_thread = Thread.new { monitor_loop(interval) }
    
    puts "Queue monitoring started"
  end
  
  def stop_monitoring
    @running = false
    @monitor_thread&.join
    puts "Queue monitoring stopped"
  end
  
  def get_metrics(queue_name = nil)
    @mutex.synchronize do
      if queue_name
        @metrics[queue_name]
      else
        @metrics.dup
      end
    end
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
    puts "Queue Monitoring Report:"
    puts "=" * 50
    
    @mutex.synchronize do
      puts "Total Queues: #{@queues.length}"
      
      @metrics.each do |queue_name, metrics|
        puts "\nQueue: #{queue_name}"
        puts "  Total Messages: #{metrics[:total_messages]}"
        puts "  Processed Messages: #{metrics[:processed_messages]}"
        puts "  Failed Messages: #{metrics[:failed_messages]}"
        puts "  Queue Depth: #{metrics[:queue_depth]}"
        puts "  Throughput: #{metrics[:throughput].round(2)} msg/s"
        puts "  Error Rate: #{metrics[:error_rate].round(2)}%"
        puts "  Avg Processing Time: #{metrics[:average_processing_time].round(2)}ms"
        puts "  Last Updated: #{metrics[:last_updated]}"
      end
      
      puts "\nAlerts Triggered: #{@alerts.count { |a| a[:triggered] }}"
    end
  end
  
  def self.demonstrate_monitoring
    puts "Queue Monitoring Demonstration:"
    puts "=" * 50
    
    monitor = QueueMonitor.new
    
    # Create mock queues
    queue1 = MockQueue.new('high_priority')
    queue2 = MockQueue.new('normal_priority')
    
    monitor.register_queue(queue1)
    monitor.register_queue(queue2)
    
    # Add alerts
    monitor.add_alert('high_queue_depth') do |queue_name, metrics|
      metrics[:queue_depth] > 100
    end
    
    monitor.add_alert('high_error_rate') do |queue_name, metrics|
      metrics[:error_rate] > 10
    end
    
    # Start monitoring
    monitor.start_monitoring(2)
    
    # Simulate queue activity
    puts "Simulating queue activity:"
    
    10.times do |i|
      queue1.enqueue("High priority task #{i}")
      queue2.enqueue("Normal task #{i}")
      
      if i % 3 == 0
        queue1.dequeue
        queue2.dequeue
      end
      
      sleep(0.5)
    end
    
    # Stop monitoring
    monitor.stop_monitoring
    
    # Generate report
    monitor.generate_report
    
    puts "\nMonitoring Features:"
    puts "- Real-time metrics"
    puts "- Performance tracking"
    puts "- Alert management"
    puts "- Dashboard visualization"
    puts "- Historical data"
    puts "- Automated monitoring"
  end
  
  private
  
  def monitor_loop(interval)
    while @running
      begin
        collect_metrics
        check_alerts
        update_dashboard
        sleep(interval)
      rescue => e
        puts "Error in monitoring loop: #{e.message}"
        sleep(5)
      end
    end
  end
  
  def collect_metrics
    @mutex.synchronize do
      @queues.each do |name, queue|
        current_metrics = @metrics[name]
        
        # Collect queue metrics
        queue_depth = queue.size
        total_messages = queue.total_messages
        processed_messages = queue.processed_messages
        failed_messages = queue.failed_messages
        avg_processing_time = queue.average_processing_time
        
        # Calculate derived metrics
        throughput = calculate_throughput(name, current_metrics)
        error_rate = calculate_error_rate(processed_messages, failed_messages)
        
        # Update metrics
        current_metrics.merge!(
          queue_depth: queue_depth,
          total_messages: total_messages,
          processed_messages: processed_messages,
          failed_messages: failed_messages,
          average_processing_time: avg_processing_time,
          throughput: throughput,
          error_rate: error_rate,
          last_updated: Time.now
        )
      end
    end
  end
  
  def check_alerts
    @metrics.each do |queue_name, metrics|
      @alerts.each do |alert|
        if alert[:condition].call(queue_name, metrics)
          unless alert[:triggered]
            alert[:action].call(queue_name, metrics)
            alert[:triggered] = true
            alert[:last_triggered] = Time.now
          end
        else
          alert[:triggered] = false
        end
      end
    end
  end
  
  def update_dashboard
    @dashboard.update(@metrics)
  end
  
  def calculate_throughput(queue_name, current_metrics)
    return 0 unless current_metrics[:last_updated]
    
    time_diff = Time.now - current_metrics[:last_updated]
    return 0 if time_diff < 1
    
    messages_processed = current_metrics[:processed_messages]
    previous_metrics = get_previous_metrics(queue_name)
    
    if previous_metrics
      messages_diff = messages_processed - previous_metrics[:processed_messages]
      messages_diff / time_diff
    else
      0
    end
  end
  
  def calculate_error_rate(processed, failed)
    total = processed + failed
    return 0 if total == 0
    (failed.to_f / total * 100).round(2)
  end
  
  def get_previous_metrics(queue_name)
    # In real implementation, this would store historical metrics
    @metrics[queue_name]
  end
end

class QueueDashboard
  def initialize
    @widgets = []
    @metrics = {}
  end
  
  def update(metrics)
    @metrics = metrics
    render
  end
  
  def render
    puts "\nQueue Dashboard:"
    puts "=" * 30
    
    @metrics.each do |queue_name, metrics|
      puts "#{queue_name}:"
      puts "  Depth: #{metrics[:queue_depth]}"
      puts "  Throughput: #{metrics[:throughput].round(2)} msg/s"
      puts "  Error Rate: #{metrics[:error_rate]}%"
      puts "  Avg Time: #{metrics[:average_processing_time].round(2)}ms"
    end
  end
end

class MockQueue
  def initialize(name)
    @name = name
    @messages = []
    @stats = {
      total_messages: 0,
      processed_messages: 0,
      failed_messages: 0,
      processing_times: []
    }
  end
  
  attr_reader :name, :stats
  
  def enqueue(message)
    @messages << message
    @stats[:total_messages] += 1
  end
  
  def dequeue
    message = @messages.shift
    if message
      processing_time = rand(10..100)
      @stats[:processing_times] << processing_time
      @stats[:processed_messages] += 1
      message
    end
  end
  
  def size
    @messages.length
  end
  
  def total_messages
    @stats[:total_messages]
  end
  
  def processed_messages
    @stats[:processed_messages]
  end
  
  def failed_messages
    @stats[:failed_messages]
  end
  
  def average_processing_time
    times = @stats[:processing_times]
    return 0 if times.empty?
    times.sum.to_f / times.length
  end
end
```

## 🎯 Message Patterns

### 6. Messaging Patterns

Common messaging patterns:

```ruby
class MessagePatterns
  def self.demonstrate_patterns
    puts "Message Patterns Demonstration:"
    puts "=" * 50
    
    # 1. Request-Reply Pattern
    demonstrate_request_reply
    
    # 2. Publish-Subscribe Pattern
    demonstrate_pub_sub
    
    # 3. Competing Consumers Pattern
    demonstrate_competing_consumers
    
    # 4. Message Router Pattern
    demonstrate_message_router
    
    # 5. Scatter-Gather Pattern
    demonstrate_scatter_gather
    
    # 6. Message Aggregator Pattern
    demonstrate_message_aggregator
  end
  
  def self.demonstrate_request_reply
    puts "\n1. Request-Reply Pattern:"
    puts "=" * 30
    
    request_reply = RequestReplyPattern.new
    
    # Send request and wait for reply
    request_id = request_reply.send_request('get_user_data', { user_id: 123 })
    reply = request_reply.wait_for_reply(request_id, 5)
    
    if reply
      puts "Reply received: #{reply[:data]}"
    else
      puts "Timeout waiting for reply"
    end
    
    puts "\nRequest-Reply Features:"
    puts "- Synchronous communication"
    puts "- Correlation tracking"
    puts "- Timeout handling"
    puts "- Reply routing"
    puts "- Request management"
  end
  
  def self.demonstrate_pub_sub
    puts "\n2. Publish-Subscribe Pattern:"
    puts "=" * 30
    
    pub_sub = PublishSubscribePattern.new
    
    # Subscribe to topics
    pub_sub.subscribe('user_events') do |message|
      puts "  User event: #{message[:event_type]}"
    end
    
    pub_sub.subscribe('order_events') do |message|
      puts "  Order event: #{message[:event_type]}"
    end
    
    # Publish events
    pub_sub.publish('user_events', {
      event_type: 'user_created',
      user_id: 123,
      name: 'John Doe'
    })
    
    pub_sub.publish('order_events', {
      event_type: 'order_placed',
      order_id: 456,
      amount: 99.99
    })
    
    puts "\nPublish-Subscribe Features:"
    puts "- Topic-based messaging"
    puts "- Multiple subscribers"
    puts "- Event broadcasting"
    puts "- Loose coupling"
    puts "- Scalability"
  end
  
  def self.demonstrate_competing_consumers
    puts "\n3. Competing Consumers Pattern:"
    puts "=" * 30
    
    competing = CompetingConsumersPattern.new
    
    # Add consumers
    3.times do |i|
      competing.add_consumer("consumer_#{i + 1}") do |message|
        puts "  Consumer #{i + 1}: Processing #{message[:task]}"
        sleep(0.5) # Simulate processing time
      end
    end
    
    # Add tasks
    5.times do |i|
      competing.add_task("task_#{i + 1}", {
        task_id: i + 1,
        description: "Process item #{i + 1}"
      })
    end
    
    # Start processing
    competing.start_processing
    
    # Wait for processing
    sleep(3)
    
    competing.stop_processing
    
    puts "\nCompeting Consumers Features:"
    puts "- Load distribution"
    puts "- Parallel processing"
    puts "- Task distribution"
    puts "- Consumer management"
    puts "- Fault tolerance"
  end
  
  def self.demonstrate_message_router
    puts "\n4. Message Router Pattern:"
    puts "=" * 30
    
    router = MessageRouterPattern.new
    
    # Add routes
    router.add_route('email', ->(message) { message[:type] == 'email' }) do |message|
      puts "  Email service: Sending to #{message[:to]}"
    end
    
    router.add_route('payment', ->(message) { message[:type] == 'payment' }) do |message|
      puts "  Payment service: Processing $#{message[:amount]}"
    end
    
    router.add_route('notification', ->(message) { message[:type] == 'notification' }) do |message|
      puts "  Notification service: #{message[:message]}"
    end
    
    # Route messages
    router.route_message({ type: 'email', to: 'user@example.com', subject: 'Hello' })
    router.route_message({ type: 'payment', amount: 99.99, currency: 'USD' })
    router.route_message({ type: 'notification', message: 'Task completed' })
    router.route_message({ type: 'unknown', data: 'test' })
    
    puts "\nMessage Router Features:"
    puts "- Content-based routing"
    puts "- Rule-based routing"
    puts "- Dynamic routing"
    puts "- Multiple handlers"
    puts "- Fallback handling"
  end
  
  def self.demonstrate_scatter_gather
    puts "\n5. Scatter-Gather Pattern:"
    puts "=" * 30
    
    scatter_gather = ScatterGatherPattern.new
    
    # Add scatter services
    3.times do |i|
      scatter_gather.add_service("service_#{i + 1}") do |message|
        puts "  Service #{i + 1}: Processing #{message[:query]}"
        sleep(0.5)
        { service: "service_#{i + 1}", result: "#{message[:query]}_#{i + 1}" }
      end
    end
    
    # Scatter request
    request_id = scatter_gather.scatter('get_user_info', {
      query: 'user_info',
      user_id: 123
    })
    
    # Gather results
    results = scatter_gather.gather(request_id, 5)
    
    puts "Gathered results:"
    results.each do |result|
      puts "  #{result[:service]}: #{result[:result]}"
    end
    
    puts "\nScatter-Gather Features:"
    puts "- Parallel processing"
    puts "- Request distribution"
    "- Result aggregation"
    puts "- Timeout handling"
    puts "- Fault tolerance"
  end
  
  def self.demonstrate_message_aggregator
    puts "\n6. Message Aggregator Pattern:"
    puts "=" * 30
    
    aggregator = MessageAggregatorPattern.new
    
    # Add aggregation rules
    aggregator.add_aggregator('daily_sales') do |messages|
      total = messages.sum { |msg| msg[:amount] }
      count = messages.length
      { total: total, count: count, average: total / count }
    end
    
    aggregator.add_aggregator('user_activity') do |messages|
      actions = messages.map { |msg| msg[:action] }
      { actions: actions, unique_actions: actions.uniq }
    end
    
    # Add messages
    5.times do |i|
      aggregator.add_message('daily_sales', {
        amount: rand(10..100),
        timestamp: Time.now,
        product_id: rand(1..10)
      })
    end
    
    3.times do |i|
      aggregator.add_message('user_activity', {
        action: ['login', 'logout', 'purchase'][i],
        user_id: rand(1..100),
        timestamp: Time.now
      })
    end
    
    # Get aggregated results
    sales_result = aggregator.get_aggregated('daily_sales')
    activity_result = aggregator.get_aggregated('user_activity')
    
    puts "Daily Sales Aggregation:"
    puts "  Total: #{sales_result[:total]}"
    puts "  Count: #{sales_result[:count]}"
    puts "  Average: #{sales_result[:average].round(2)}"
    
    puts "\nUser Activity Aggregation:"
    puts "  Actions: #{activity_result[:actions].join(', ')}"
    puts "  Unique Actions: #{activity_result[:unique_actions].join(', ')}"
    
    puts "\nMessage Aggregator Features:"
    puts "- Message grouping"
    puts "- Aggregation functions"
    puts "- Time windowing"
    puts "- Real-time aggregation"
    puts "- Result caching"
  end
  
  private
  
  def self.simulate_processing_time
    rand(10..100)
  end
end

# Pattern implementations
class RequestReplyPattern
  def initialize
    @pending_requests = {}
    @replies = {}
    @mutex = Mutex.new
  end
  
  def send_request(action, data)
    request_id = SecureRandom.uuid
    request = {
      id: request_id,
      action: action,
      data: data,
      timestamp: Time.now
    }
    
    @mutex.synchronize do
      @pending_requests[request_id] = request
    end
    
    # Simulate sending request
    puts "Sending request: #{action}"
    
    # Simulate reply after delay
    Thread.new do
      sleep(rand(1..3))
      reply = {
        request_id: request_id,
        data: "#{action}_result",
        timestamp: Time.now
      }
      
      @mutex.synchronize do
        @replies[request_id] = reply
        @pending_requests.delete(request_id)
      end
    end
    
    request_id
  end
  
  def wait_for_reply(request_id, timeout = 5)
    start_time = Time.now
    
    while Time.now - start_time < timeout
      @mutex.synchronize do
        reply = @replies.delete(request_id)
        return reply if reply
      end
      
      sleep(0.1)
    end
    
    nil
  end
end

class PublishSubscribePattern
  def initialize
    @subscribers = {}
    @mutex = Mutex.new
  end
  
  def subscribe(topic, &block)
    @mutex.synchronize do
      @subscribers[topic] ||= []
      @subscribers[topic] << block
    end
  end
  
  def publish(topic, message)
    @mutex.synchronize do
      subscribers = @subscribers[topic] || []
      
      subscribers.each do |subscriber|
        Thread.new do
          begin
            subscriber.call(message)
          rescue => e
            puts "Error in subscriber: #{e.message}"
          end
        end
      end
    end
  end
end

class CompetingConsumersPattern
  def initialize
    @queue = []
    @consumers = []
    @running = false
    @consumer_threads = []
    @mutex = Mutex.new
  end
  
  def add_consumer(name, &block)
    consumer = {
      name: name,
      handler: block,
      busy: false
    }
    
    @consumers << consumer
    consumer
  end
  
  def add_task(task_id, task_data)
    @mutex.synchronize do
      @queue << { id: task_id, data: task_data }
    end
  end
  
  def start_processing
    @running = true
    
    @consumers.each do |consumer|
      thread = Thread.new { process_consumer(consumer) }
      @consumer_threads << thread
    end
  end
  
  def stop_processing
    @running = false
    @consumer_threads.each(&:join)
  end
  
  private
  
  def process_consumer(consumer)
    while @running
      task = nil
      
      @mutex.synchronize do
        task = @queue.shift
        consumer[:busy] = true if task
      end
      
      if task
        begin
          consumer[:handler].call(task)
        rescue => e
          puts "Error in #{consumer[:name]}: #{e.message}"
        ensure
          @mutex.synchronize { consumer[:busy] = false }
        end
      else
        sleep(0.1)
      end
    end
  end
end

class MessageRouterPattern
  def initialize
    @routes = []
    @default_handler = nil
  end
  
  def add_route(name, condition, &block)
    @routes << {
      name: name,
      condition: condition,
      handler: block
    }
  end
  
  def set_default_handler(&block)
    @default_handler = block
  end
  
  def route_message(message)
    handler = @routes.find { |route| route[:condition].call(message) }&.handler
    
    if handler
      handler.call(message)
    elsif @default_handler
      @default_handler.call(message)
    else
      puts "No handler found for message: #{message}"
    end
  end
end

class ScatterGatherPattern
  def initialize
    @services = []
    @pending_requests = {}
    @results = {}
    @mutex = Mutex.new
  end
  
  def add_service(name, &block)
    @services << {
      name: name,
      handler: block
    }
  end
  
  def scatter(action, data)
    request_id = SecureRandom.uuid
    
    @mutex.synchronize do
      @pending_requests[request_id] = {
        action: action,
        data: data,
        services: @services.length,
        results: []
      }
    end
    
    # Send request to all services
    @services.each do |service|
      Thread.new do
        begin
          result = service[:handler].call(data)
          
          @mutex.synchronize do
            pending = @pending_requests[request_id]
            if pending
              pending[:results] << { service: service[:name], result: result }
              
              # Check if all services responded
              if pending[:results].length == pending[:services]
                @results[request_id] = pending[:results]
                @pending_requests.delete(request_id)
              end
            end
          end
        rescue => e
          puts "Error in service #{service[:name]}: #{e.message}"
        end
      end
    end
    
    request_id
  end
  
  def gather(request_id, timeout = 5)
    start_time = Time.now
    
    while Time.now - start_time < timeout
      @mutex.synchronize do
        results = @results.delete(request_id)
        return results if results
      end
      
      sleep(0.1)
    end
    
    []
  end
end

class MessageAggregatorPattern
  def initialize
    @aggregators = {}
    @message_buffer = {}
    @mutex = Mutex.new
  end
  
  def add_aggregator(name, &block)
    @aggregators[name] = block
  end
  
  def add_message(type, message)
    @mutex.synchronize do
      @message_buffer[type] ||= []
      @message_buffer[type] << message
      
      # Trigger aggregation
      aggregator = @aggregators[type]
      if aggregator
        result = aggregator.call(@message_buffer[type])
        @message_buffer[type] = [] # Clear buffer after aggregation
        result
      end
    end
  end
  
  def get_aggregated(type)
    @mutex.synchronize do
      aggregator = @aggregators[type]
      messages = @message_buffer[type] || []
      
      if aggregator && messages.any?
        aggregator.call(messages)
      else
        nil
      end
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Queue**: Create simple message queue
2. **Priority Queue**: Implement priority ordering
3. **Delay Queue**: Add delayed processing
4. **Queue Monitor**: Basic monitoring system

### Intermediate Exercises

1. **Redis Queue**: Redis-based queue system
2. **Distributed Queue**: Multi-node queue
3. **Message Patterns**: Implement patterns
4. **Queue Analytics**: Advanced analytics

### Advanced Exercises

1. **Enterprise Queue**: Production-ready system
2. **Message Broker**: Custom broker implementation
3. **Real-time Processing**: Stream processing
4. **Queue Orchestration**: Complex workflows

---

## 🎯 Summary

Message Queues in Ruby provide:

- **Queue Fundamentals** - Core concepts and principles
- **In-Memory Queue** - Simple queue implementation
- **Redis Queue** - Redis-based queue system
- **Distributed Queue** - Multi-node queue system
- **Queue Monitoring** - Performance tracking and analytics
- **Message Patterns** - Common messaging patterns

Master these message queue techniques for scalable Ruby applications!
