# System Design Interview Questions in Ruby

## Overview

System design interviews test your ability to design scalable, reliable, and maintainable systems. This guide covers common system design questions with Ruby implementations and architectural patterns.

## System Design Framework

### Design Process Template
```ruby
class SystemDesignFramework
  def self.design_approach
    {
      requirements: [
        "Clarify functional requirements",
        "Identify non-functional requirements",
        "Define constraints and assumptions",
        "Estimate scale and traffic"
      ],
      design: [
        "High-level architecture",
        "Component design",
        "Data models",
        "API design"
      ],
      deep_dive: [
        "Scalability considerations",
        "Reliability and availability",
        "Security considerations",
        "Performance optimization"
      ],
      evaluation: [
        "Trade-offs analysis",
        "Bottleneck identification",
        "Cost considerations",
        "Future improvements"
      ]
    }
  end

  def self.question_template
    {
      step1_requirements: "What are the functional and non-functional requirements?",
      step2_scale: "What is the expected scale and traffic?",
      step3_components: "What are the main components of the system?",
      step4_data_model: "How should we structure the data?",
      step5_api: "What are the key APIs and interfaces?",
      step6_scalability: "How will the system scale?",
      step7_reliability: "How do we ensure reliability and availability?",
      step8_tradeoffs: "What are the trade-offs and alternatives?"
    }
  end
end

# Usage example
puts "System Design Framework:"
framework = SystemDesignFramework.design_approach
framework.each do |phase, items|
  puts "\n#{phase.to_s.capitalize}:"
  items.each { |item| puts "  - #{item}" }
end
```

## URL Shortener Design

### Problem: Design a URL Shortener
```ruby
class URLShortener
  def self.requirements
    {
      functional: [
        "Shorten long URLs to short codes",
        "Redirect short URLs to original URLs",
        "Custom short URLs for premium users",
        "Analytics on URL usage",
        "Expiration dates for short URLs"
      ],
      non_functional: [
        "High availability (99.9% uptime)",
        "Low latency (redirects < 100ms)",
        "Scalable to billions of URLs",
        "Distributed system",
        "Security against malicious URLs"
      ],
      scale: {
        daily_requests: "100 million URL shortens",
        daily_redirects: "1 billion redirects",
        storage: "10 billion URLs",
        growth_rate: "10% per month"
      }
    }
  end

  def self.system_architecture
    {
      components: [
        "Load Balancer",
        "Web Servers",
        "URL Shortening Service",
        "Redirect Service",
        "Database Cluster",
        "Cache Layer",
        "Analytics Service"
      ],
      data_flow: [
        "Client → Load Balancer → Web Server → URL Service",
        "URL Service → Database + Cache",
        "Redirect: Client → Load Balancer → Redirect Service → Cache"
      ]
    }
  end

  def self.database_design
    {
      url_table: {
        table: "urls",
        columns: {
          id: "BIGINT PRIMARY KEY AUTO_INCREMENT",
          short_code: "VARCHAR(10) UNIQUE",
          original_url: "TEXT",
          created_at: "TIMESTAMP",
          expires_at: "TIMESTAMP",
          user_id: "BIGINT",
          click_count: "INT DEFAULT 0"
        },
        indexes: ["short_code", "user_id", "created_at"]
      },
      analytics_table: {
        table: "url_analytics",
        columns: {
          id: "BIGINT PRIMARY KEY AUTO_INCREMENT",
          short_code: "VARCHAR(10)",
          ip_address: "VARCHAR(45)",
          user_agent: "TEXT",
          referrer: "TEXT",
          country: "VARCHAR(2)",
          timestamp: "TIMESTAMP"
        },
        indexes: ["short_code", "timestamp", "country"]
      }
    }
  end

  def self.api_design
    {
      shorten_url: {
        method: "POST",
        endpoint: "/api/v1/urls",
        request_body: {
          url: "string (required)",
          custom_code: "string (optional)",
          expires_at: "datetime (optional)"
        },
        response: {
          short_url: "string",
          short_code: "string",
          expires_at: "datetime"
        }
      },
      redirect_url: {
        method: "GET",
        endpoint: "/{short_code}",
        response: "HTTP 301 redirect to original URL"
      },
      get_analytics: {
        method: "GET",
        endpoint: "/api/v1/urls/{short_code}/analytics",
        response: {
          total_clicks: "integer",
          unique_visitors: "integer",
          top_countries: "array",
          daily_stats: "array"
        }
      }
    }
  end

  def self.short_code_generation
    {
      approaches: [
        "Hash function (MD5, SHA1) + base62 encoding",
        "Counter-based approach with base62 encoding",
        "Random string generation with collision detection"
      ],
      implementation: {
        base62_chars: "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",
        counter_approach: "Convert database ID to base62",
        collision_handling: "Retry with different hash or increment counter"
      }
    }
  end

  def self.scalability_considerations
    {
      database: [
        "Database sharding by short code ranges",
        "Read replicas for analytics queries",
        "Connection pooling"
      ],
      caching: [
        "Redis for hot URLs",
        "CDN for redirect endpoints",
        "Application-level caching"
      ],
      load_balancing: [
        "Multiple web servers behind load balancer",
        "Geographic distribution",
        "Health checks and failover"
      ]
    }
  end
end

# Implementation example
class URLShortenerService
  def initialize
    @counter = 1000000
    @url_store = {}
    @cache = {}
    @base62 = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
  end

  def shorten_url(original_url, custom_code = nil)
    short_code = custom_code || generate_short_code
    
    # Check for collision
    while @url_store.key?(short_code)
      short_code = generate_short_code
    end
    
    @url_store[short_code] = {
      original_url: original_url,
      created_at: Time.now,
      click_count: 0
    }
    
    {
      short_code: short_code,
      short_url: "https://short.ly/#{short_code}"
    }
  end

  def get_original_url(short_code)
    # Check cache first
    return @cache[short_code][:original_url] if @cache[short_code]
    
    url_data = @url_store[short_code]
    return nil unless url_data
    
    # Update click count
    url_data[:click_count] += 1
    
    # Cache the result
    @cache[short_code] = url_data
    
    url_data[:original_url]
  end

  def get_analytics(short_code)
    url_data = @url_store[short_code]
    return nil unless url_data
    
    {
      click_count: url_data[:click_count],
      created_at: url_data[:created_at]
    }
  end

  private

  def generate_short_code
    @counter += 1
    encode_base62(@counter)
  end

  def encode_base62(num)
    return @base62[0] if num == 0
    
    result = ""
    while num > 0
      result = @base62[num % 62] + result
      num /= 62
    end
    
    result
  end
end

# Usage example
puts "\nURL Shortener Requirements:"
URLShortener.requirements.each do |category, items|
  puts "\n#{category.to_s.capitalize}:"
  if items.is_a?(Hash)
    items.each { |key, value| puts "  #{key}: #{value}" }
  else
    items.each { |item| puts "  - #{item}" }
  end
end

# Demo implementation
service = URLShortenerService.new
result = service.shorten_url("https://www.example.com/very/long/url")
puts "\nShortened URL: #{result[:short_url]}"

original = service.get_original_url(result[:short_code])
puts "Original URL: #{original}"

analytics = service.get_analytics(result[:short_code])
puts "Analytics: #{analytics}"
```

## Design a Twitter Clone

### Problem: Design a Twitter-like Service
```ruby
class TwitterClone
  def self.requirements
    {
      core_features: [
        "User registration and authentication",
        "Post tweets (280 character limit)",
        "Follow/unfollow users",
        "Timeline (home feed)",
        "User profiles",
        "Search tweets",
        "Trending topics"
      ],
      scale: {
        users: "500 million monthly active users",
        tweets: "500 million tweets per day",
        reads: "10 billion tweet reads per day",
        growth: "20% year over year"
      },
      constraints: [
        "Low latency for timeline generation (< 200ms)",
        "High availability (99.9%)",
        "Real-time feed updates",
        "Global distribution"
      ]
    }
  end

  def self.system_architecture
    {
      services: [
        "User Service (authentication, profiles)",
        "Tweet Service (posting, storage)",
        "Timeline Service (feed generation)",
        "Follow Service (social graph)",
        "Search Service (tweet search)",
        "Notification Service (real-time updates)"
      ],
      data_stores: [
        "User Database (PostgreSQL)",
        "Tweet Database (Cassandra)",
        "Social Graph (Neo4j)",
        "Timeline Cache (Redis)",
        "Search Index (Elasticsearch)"
      ],
      message_queue: "Kafka for real-time processing"
    }
  end

  def self.data_models
    {
      user: {
        table: "users",
        fields: {
          id: "UUID PRIMARY KEY",
          username: "VARCHAR(50) UNIQUE",
          email: "VARCHAR(255) UNIQUE",
          password_hash: "VARCHAR(255)",
          display_name: "VARCHAR(100)",
          bio: "TEXT",
          followers_count: "INT DEFAULT 0",
          following_count: "INT DEFAULT 0",
          tweets_count: "INT DEFAULT 0",
          created_at: "TIMESTAMP"
        }
      },
      tweet: {
        table: "tweets",
        fields: {
          id: "UUID PRIMARY KEY",
          user_id: "UUID",
          content: "VARCHAR(280)",
          created_at: "TIMESTAMP",
          likes_count: "INT DEFAULT 0",
          retweets_count: "INT DEFAULT 0",
          replies_count: "INT DEFAULT 0"
        }
      },
      follow: {
        table: "follows",
        fields: {
          follower_id: "UUID",
          following_id: "UUID",
          created_at: "TIMESTAMP",
          PRIMARY_KEY: "(follower_id, following_id)"
        }
      }
    }
  end

  def self.timeline_generation
    {
      approaches: [
        "Pull-based (client requests timeline)",
        "Push-based (pre-compute timelines)",
        "Hybrid (recent tweets push, older pull)"
      ],
      push_based: {
        process: [
          "When user posts tweet, push to all followers",
          "Store pre-computed timelines in Redis",
          "Timeline contains most recent N tweets",
          "Older tweets pulled on demand"
        ],
        advantages: ["Fast timeline loading", "Consistent experience"],
        challenges: ["Write amplification", "Storage overhead"]
      }
    }
  end

  def self.scalability_strategies
    {
      horizontal_scaling: [
        "Microservices architecture",
        "Database sharding",
        "Load balancing",
        "Auto-scaling based on load"
      ],
      caching: [
        "Redis for hot data",
        "CDN for static assets",
        "Application-level caching",
        "Database query caching"
      ],
      performance: [
        "Async processing with message queues",
        "Read replicas for read-heavy operations",
        "Connection pooling",
        "Batch processing for analytics"
      ]
    }
  end
end

# Implementation example
class TwitterService
  def initialize
    @users = {}
    @tweets = {}
    @follows = {}
    @timelines = {}
  end

  def create_user(username, email, password)
    user_id = generate_id
    @users[user_id] = {
      id: user_id,
      username: username,
      email: email,
      password_hash: hash_password(password),
      followers_count: 0,
      following_count: 0,
      tweets_count: 0,
      created_at: Time.now
    }
    user_id
  end

  def post_tweet(user_id, content)
    return nil unless @users[user_id]
    return nil if content.length > 280
    
    tweet_id = generate_id
    @tweets[tweet_id] = {
      id: tweet_id,
      user_id: user_id,
      content: content,
      created_at: Time.now,
      likes_count: 0,
      retweets_count: 0,
      replies_count: 0
    }
    
    # Update user's tweet count
    @users[user_id][:tweets_count] += 1
    
    # Push to followers' timelines
    followers = get_followers(user_id)
    followers.each do |follower_id|
      @timelines[follower_id] ||= []
      @timelines[follower_id].unshift(tweet_id)
      @timelines[follower_id] = @timelines[follower_id].first(100)  # Keep last 100
    end
    
    tweet_id
  end

  def follow_user(follower_id, following_id)
    return false unless @users[follower_id] && @users[following_id]
    return false if follower_id == following_id
    
    @follows[follower_id] ||= Set.new
    @follows[follower_id] << following_id
    
    # Update counts
    @users[follower_id][:following_count] += 1
    @users[following_id][:followers_count] += 1
    
    true
  end

  def get_timeline(user_id)
    @timelines[user_id] ||= []
    timeline_tweets = @timelines[user_id].map { |tweet_id| @tweets[tweet_id] }
    timeline_tweets.compact
  end

  def get_user_tweets(user_id, limit = 20)
    user_tweets = @tweets.values.select { |tweet| tweet[:user_id] == user_id }
    user_tweets.sort_by { |tweet| -tweet[:created_at].to_i }.first(limit)
  end

  def get_followers(user_id)
    followers = []
    @follows.each do |follower_id, following_set|
      followers << follower_id if following_set.include?(user_id)
    end
    followers
  end

  private

  def generate_id
    Time.now.to_f.to_s.gsub('.', '')
  end

  def hash_password(password)
    # Simple hash - in production, use proper password hashing
    Digest::SHA256.hexdigest(password)
  end
end

# Usage example
puts "\nTwitter Clone Requirements:"
TwitterClone.requirements.each do |category, items|
  puts "\n#{category.to_s.capitalize}:"
  if items.is_a?(Hash)
    items.each { |key, value| puts "  #{key}: #{value}" }
  else
    items.each { |item| puts "  - #{item}" }
  end
end

# Demo implementation
twitter = TwitterService.new

# Create users
user1 = twitter.create_user("alice", "alice@example.com", "password123")
user2 = twitter.create_user("bob", "bob@example.com", "password456")

# Follow relationship
twitter.follow_user(user2, user1)

# Post tweets
tweet1 = twitter.post_tweet(user1, "Hello Twitter! This is my first tweet.")
tweet2 = twitter.post_tweet(user2, "Hey Alice! Great to see you here.")

# Get timeline
puts "\nBob's Timeline:"
timeline = twitter.get_timeline(user2)
timeline.each { |tweet| puts "#{tweet[:content]}" }
```

## Design a Chat System

### Problem: Design a Real-time Chat Application
```ruby
class ChatSystem
  def self.requirements
    {
      features: [
        "One-on-one messaging",
        "Group chats",
        "Message history",
        "Online status",
        "Read receipts",
        "File sharing",
        "Push notifications"
      ],
      scale: {
        users: "100 million daily active users",
        messages: "10 billion messages per day",
        groups: "50 million active groups",
        concurrent_connections: "50 million"
      },
      constraints: [
        "Real-time delivery (< 100ms)",
        "Message ordering guarantee",
        "Offline message delivery",
        "99.99% availability",
        "End-to-end encryption"
      ]
    }
  end

  def self.system_architecture
    {
      components: [
        "WebSocket Servers (real-time connections)",
        "Message Service (message processing)",
        "Presence Service (online status)",
        "Push Notification Service",
        "File Storage Service",
        "Authentication Service"
      ],
      technologies: [
        "WebSocket for real-time communication",
        "Redis for connection management",
        "Kafka for message queuing",
        "MongoDB for message storage",
        "CDN for file delivery"
      ]
    }
  end

  def self.message_flow
    {
      send_message: [
        "Client → WebSocket Server",
        "WebSocket Server → Message Service",
        "Message Service → Database",
        "Message Service → Kafka",
        "Message Service → Recipient WebSocket Servers",
        "WebSocket Servers → Connected Clients"
      ],
      offline_delivery: [
        "Message Service → Push Notification Service",
        "Push Notification Service → Mobile Devices",
        "Client syncs messages when online"
      ]
    }
  end

  def self.data_models
    {
      message: {
        table: "messages",
        fields: {
          id: "UUID PRIMARY KEY",
          sender_id: "UUID",
          receiver_id: "UUID",  # For direct messages
          group_id: "UUID",      # For group messages
          content: "TEXT",
          message_type: "ENUM('text', 'image', 'file')",
          timestamp: "TIMESTAMP",
          read_at: "TIMESTAMP",
          delivered_at: "TIMESTAMP"
        },
        indexes: ["sender_id", "receiver_id", "group_id", "timestamp"]
      },
      conversation: {
        table: "conversations",
        fields: {
          id: "UUID PRIMARY KEY",
          type: "ENUM('direct', 'group')",
          participants: "JSONB",
          created_at: "TIMESTAMP",
          updated_at: "TIMESTAMP",
          last_message_id: "UUID"
        }
      }
    }
  end

  def self.scalability_considerations
    {
      connection_management: [
        "Connection pooling per WebSocket server",
        "Redis for tracking user connections",
        "Load balancing WebSocket connections",
        "Graceful connection failover"
      ],
      message_delivery: [
        "Message queuing with Kafka",
        "At-least-once delivery guarantee",
        "Duplicate message detection",
        "Message ordering per conversation"
      ],
      storage: [
        "Message sharding by conversation ID",
        "Hot message caching",
        "Archive old messages",
        "Read replicas for message history"
      ]
    }
  end
end

# Implementation example
class ChatService
  def initialize
    @users = {}
    @connections = {}
    @messages = {}
    @conversations = {}
  end

  def connect_user(user_id, websocket)
    @connections[user_id] = websocket
    @users[user_id] = { online: true, connected_at: Time.now }
    broadcast_user_status(user_id, :online)
  end

  def disconnect_user(user_id)
    @connections.delete(user_id)
    @users[user_id][:online] = false
    @users[user_id][:disconnected_at] = Time.now
    broadcast_user_status(user_id, :offline)
  end

  def send_message(sender_id, receiver_id, content)
    message_id = generate_id
    message = {
      id: message_id,
      sender_id: sender_id,
      receiver_id: receiver_id,
      content: content,
      timestamp: Time.now,
      read: false
    }
    
    @messages[message_id] = message
    
    # Deliver to recipient if online
    if @connections[receiver_id]
      deliver_message(receiver_id, message)
    else
    
      # Store for offline delivery
      store_offline_message(receiver_id, message)
    end
    
    message_id
  end

  def create_group_chat(creator_id, participant_ids)
    group_id = generate_id
    @conversations[group_id] = {
      id: group_id,
      type: :group,
      participants: participant_ids + [creator_id],
      created_at: Time.now
    }
    group_id
  end

  def send_group_message(sender_id, group_id, content)
    conversation = @conversations[group_id]
    return nil unless conversation && conversation[:participants].include?(sender_id)
    
    message_id = generate_id
    message = {
      id: message_id,
      sender_id: sender_id,
      group_id: group_id,
      content: content,
      timestamp: Time.now,
      read: false
    }
    
    @messages[message_id] = message
    
    # Deliver to all online participants
    conversation[:participants].each do |participant_id|
      next if participant_id == sender_id
      
      if @connections[participant_id]
        deliver_message(participant_id, message)
      else
        store_offline_message(participant_id, message)
      end
    end
    
    message_id
  end

  def mark_message_read(user_id, message_id)
    message = @messages[message_id]
    return false unless message
    
    message[:read] = true
    message[:read_at] = Time.now
    
    # Notify sender
    notify_message_read(message[:sender_id], message_id)
    true
  end

  def get_conversation_history(user_id, other_user_id, limit = 50)
    messages = @messages.values.select do |msg|
      (msg[:sender_id] == user_id && msg[:receiver_id] == other_user_id) ||
      (msg[:sender_id] == other_user_id && msg[:receiver_id] == user_id)
    end
    
    messages.sort_by { |msg| msg[:timestamp] }.last(limit)
  end

  private

  def deliver_message(user_id, message)
    websocket = @connections[user_id]
    return unless websocket
    
    # Simulate WebSocket delivery
    puts "Delivering message to #{user_id}: #{message[:content]}"
  end

  def store_offline_message(user_id, message)
    @users[user_id][:offline_messages] ||= []
    @users[user_id][:offline_messages] << message
  end

  def broadcast_user_status(user_id, status)
    puts "User #{user_id} is now #{status}"
  end

  def notify_message_read(sender_id, message_id)
    puts "Notified #{sender_id} that message #{message_id} was read"
  end

  def generate_id
    Time.now.to_f.to_s.gsub('.', '')
  end
end

# Usage example
puts "\nChat System Requirements:"
ChatSystem.requirements.each do |category, items|
  puts "\n#{category.to_s.capitalize}:"
  if items.is_a?(Hash)
    items.each { |key, value| puts "  #{key}: #{value}" }
  else
    items.each { |item| puts "  - #{item}" }
  end
end

# Demo implementation
chat = ChatService.new

# Simulate user connections
chat.connect_user("user1", "websocket1")
chat.connect_user("user2", "websocket2")

# Send messages
message1 = chat.send_message("user1", "user2", "Hello! How are you?")
message2 = chat.send_message("user2", "user1", "I'm doing great, thanks!")

# Create group chat
group_id = chat.create_group_chat("user1", ["user2", "user3"])
group_message = chat.send_group_message("user1", group_id, "Welcome to the group!")

# Mark as read
chat.mark_message_read("user1", message2)

# Get conversation history
history = chat.get_conversation_history("user1", "user2")
puts "\nConversation history:"
history.each { |msg| puts "#{msg[:sender_id]}: #{msg[:content]}" }
```

## Common System Design Patterns

### Design Patterns and Solutions
```ruby
class SystemDesignPatterns
  def self.cache_patterns
    {
      cache_aside: {
        description: "Application manages cache explicitly",
        use_case: "User profiles, product catalogs",
        flow: [
          "Check cache first",
          "Cache hit: return cached data",
          "Cache miss: fetch from database, update cache, return data"
        ],
        advantages: ["Simple to implement", "Flexible cache invalidation"],
        challenges: ["Cache stampede risk", "Stale data issues"]
      },
      write_through: {
        description: "Write to cache and database simultaneously",
        use_case: "Critical data that must be consistent",
        flow: [
          "Application writes to cache",
          "Cache writes to database",
          "Return success after both writes complete"
        ],
        advantages: ["Data consistency", "No stale data"],
        challenges: ["Higher latency", "Complex implementation"]
      },
      write_behind: {
        description: "Write to cache immediately, database asynchronously",
        use_case: "High-write workloads",
        flow: [
          "Write to cache immediately",
          "Queue database write operation",
          "Background process updates database"
        ],
        advantages: ["Low latency", "Better throughput"],
        challenges: ["Data loss risk", "Complex error handling"]
      }
    }
  end

  def self.load_balancing_strategies
    {
      round_robin: {
        description: "Distribute requests evenly across servers",
        advantages: ["Simple", "Predictable distribution"],
        disadvantages: ["Ignores server capacity", "No health checking"]
      },
      least_connections: {
        description: "Route to server with fewest active connections",
        advantages: ["Better resource utilization", "Adaptive"],
        disadvantages: ["Requires connection tracking", "More complex"]
      },
      weighted_round_robin: {
        description: "Weight servers based on capacity",
        advantages: ["Accounts for server differences", "Flexible"],
        disadvantages: ["Manual weight configuration", "Static weights"]
      }
    }
  end

  def self.database_patterns
    {
      read_replicas: {
        description: "Multiple read-only copies of primary database",
        use_case: "Read-heavy applications",
        benefits: ["Improved read performance", "Better availability"],
        challenges: ["Replication lag", "Complex consistency"]
      },
      sharding: {
        description: "Horizontal partitioning of data across multiple databases",
        use_case: "Large-scale applications",
        strategies: ["Range-based", "Hash-based", "Directory-based"],
        benefits: ["Scalability", "Performance"],
        challenges: ["Complex queries", "Rebalancing"]
      },
      cqrs: {
        description: "Command Query Responsibility Segregation",
        use_case: "Complex business logic",
        benefits: ["Optimized reads/writes", "Scalability"],
        challenges: ["Complexity", "Eventual consistency"]
      }
    }
  end

  def self.message_queue_patterns
    {
      point_to_point: {
        description: "One producer, one consumer",
        use_case: "Task distribution",
        benefits: ["Simple", "Reliable delivery"],
        challenges: ["Single point of failure", "Limited scalability"]
      },
      publish_subscribe: {
        description: "One producer, multiple consumers",
        use_case: "Event notification",
        benefits: ["Decoupling", "Flexibility"],
        challenges: ["Complex routing", "Message ordering"]
      },
      competing_consumers: {
        description: "Multiple consumers processing same queue",
        use_case: "Load balancing",
        benefits: ["Scalability", "Fault tolerance"],
        challenges: ["Message ordering", "Duplicate processing"]
      }
    }
  end
end

# Usage example
puts "\nCache Patterns:"
SystemDesignPatterns.cache_patterns.each do |pattern, details|
  puts "\n#{pattern.to_s.gsub('_', ' ').capitalize}:"
  puts "  Description: #{details[:description]}"
  puts "  Use case: #{details[:use_case]}"
  puts "  Advantages: #{details[:advantages].join(', ')}"
end
```

## Interview Preparation

### System Design Interview Tips
```ruby
class SystemDesignInterviewTips
  def self.preparation_checklist
    {
      technical_knowledge: [
        "Distributed systems concepts",
        "Database technologies and patterns",
        "Caching strategies",
        "Load balancing",
        "Message queues",
        "Microservices architecture",
        "API design principles",
        "Security best practices"
      ],
      problem_solving: [
        "Requirements gathering",
        "System decomposition",
        "Trade-off analysis",
        "Bottleneck identification",
        "Scalability planning",
        "Performance optimization"
      ],
      communication: [
        "Clear articulation of design decisions",
        "Drawing system diagrams",
        "Explaining trade-offs",
        "Asking clarifying questions",
        "Structuring the conversation"
      ]
    }
  end

  def self.common_mistakes
    [
      "Jumping into implementation without understanding requirements",
      "Not asking clarifying questions",
      "Ignoring non-functional requirements",
      "Not discussing trade-offs",
      "Over-engineering the solution",
      "Not considering scalability",
      "Ignoring security concerns",
      "Poor communication of design decisions"
    ]
  end

  def self.follow_up_questions
    [
      "How would you handle X scenario?",
      "What are the limitations of this design?",
      "How would you monitor this system?",
      "How would you test this system?",
      "What would happen if component X fails?",
      "How would you scale this to Y users?",
      "What are the security implications?",
      "How would you handle data consistency?"
    ]
  end

  def self.practice_problems
    [
      "Design a URL shortener",
      "Design a Twitter clone",
      "Design a chat system",
      "Design a ride-sharing app",
      "Design a video streaming service",
      "Design a social media platform",
      "Design an e-commerce platform",
      "Design a file storage service",
      "Design a real-time analytics system",
      "Design a content delivery network"
    ]
  end
end

# Usage example
puts "\nSystem Design Interview Preparation:"
tips = SystemDesignInterviewTips.preparation_checklist
tips.each do |category, items|
  puts "\n#{category.to_s.gsub('_', ' ').capitalize}:"
  items.each { |item| puts "  - #{item}" }
end
```

## Conclusion

System design interviews test your ability to think architecturally and make trade-offs. By understanding common patterns, practicing with real-world problems, and following a structured approach, you can effectively demonstrate your system design skills.

## Further Reading

- [System Design Interview](https://www.systemdesigninterview.com/)
- [Designing Data-Intensive Applications](https://dataintensive.net/)
- [Grokking the System Design Interview](https://www.educative.io/courses/grokking-the-system-design-interview)
- [System Design Primer](https://github.com/donnemartin/system-design-primer)
- [Alex Xu's System Design Blog](https://blog.bytebytego.com/)
