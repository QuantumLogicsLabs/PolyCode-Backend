# Database Connections in Ruby
# Comprehensive guide to database connection management and pooling

## 🔌 Connection Fundamentals

### 1. Database Connection Concepts

Core database connection principles:

```ruby
class DatabaseConnectionFundamentals
  def self.explain_connection_concepts
    puts "Database Connection Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Database Connection",
        description: "Communication channel between application and database",
        components: ["Driver", "Connection string", "Authentication", "Session"],
        lifecycle: ["Establish", "Authenticate", "Execute", "Close"],
        importance: "Foundation for all database operations"
      },
      {
        concept: "Connection Pooling",
        description: "Reuse database connections to improve performance",
        benefits: ["Reduced overhead", "Better resource utilization", "Improved response time"],
        components: ["Pool manager", "Connection cache", "Validation", "Timeout handling"],
        challenges: ["Connection leaks", "Pool exhaustion", "Stale connections"]
      },
      {
        concept: "Connection Strings",
        description: "Configuration parameters for database connections",
        elements: ["Database type", "Server address", "Credentials", "Options"],
        examples: ["PostgreSQL", "MySQL", "SQLite", "SQL Server"],
        security: ["Encryption", "Environment variables", "Secret management"]
      },
      {
        concept: "Database Drivers",
        description: "Software that enables communication with specific databases",
        types: ["Native drivers", "ODBC", "JDBC", "ADO.NET"],
        ruby_drivers: ["pg", "mysql2", "sqlite3", "tiny_tds"],
        selection: ["Performance", "Features", "Compatibility", "Support"]
      },
      {
        concept: "Connection Management",
        description: "Strategies for managing database connections",
        strategies: ["Single connection", "Connection pooling", "Load balancing", "Failover"],
        considerations: ["Concurrency", "Scalability", "Reliability", "Performance"],
        best_practices: ["Connection reuse", "Proper cleanup", "Error handling", "Monitoring"]
      },
      {
        concept: "Transaction Management",
        description: "Managing database transactions and connection states",
        concepts: ["ACID properties", "Isolation levels", "Deadlock handling", "Rollback"],
        implementation: ["Auto-commit", "Manual transactions", "Nested transactions", "Savepoints"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Lifecycle: #{concept[:lifecycle].join(', ')}" if concept[:lifecycle]
      puts "  Importance: #{concept[:importance]}" if concept[:importance]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Elements: #{concept[:elements].join(', ')}" if concept[:elements]
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Security: #{concept[:security].join(', ')}" if concept[:security]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Ruby Drivers: #{concept[:ruby_drivers].join(', ')}" if concept[:ruby_drivers]
      puts "  Selection: #{concept[:selection].join(', ')}" if concept[:selection]
      puts "  Strategies: #{concept[:strategies].join(', ')}" if concept[:strategies]
      puts "  Considerations: #{concept[:considerations].join(', ')}" if concept[:considerations]
      puts "  Best Practices: #{concept[:best_practices].join(', ')}" if concept[:best_practices]
      puts "  Concepts: #{concept[:concepts].join(', ')}" if concept[:concepts]
      puts "  Implementation: #{concept[:implementation].join(', ')}" if concept[:implementation]
      puts
    end
  end
  
  def self.connection_lifecycle
    puts "\nDatabase Connection Lifecycle:"
    puts "=" * 50
    
    lifecycle = [
      {
        phase: "1. Connection Establishment",
        description: "Create new connection to database",
        steps: [
          "Load database driver",
          "Parse connection string",
          "Establish network connection",
          "Authenticate with database",
          "Initialize session"
        ],
        duration: "100-500ms",
        critical: true
      },
      {
        phase: "2. Query Execution",
        description: "Execute SQL queries and commands",
        steps: [
          "Parse SQL statement",
          "Prepare statement",
          "Bind parameters",
          "Execute query",
          "Fetch results"
        ],
        duration: "10-100ms",
        critical: false
      },
      {
        phase: "3. Connection Maintenance",
        description: "Keep connection alive and healthy",
        steps: [
          "Health checks",
          "Keep-alive queries",
          "Timeout management",
          "Error recovery"
        ],
        duration: "Ongoing",
        critical: false
      },
      {
        phase: "4. Connection Cleanup",
        description: "Properly close and cleanup connection",
        steps: [
          "Commit/rollback transactions",
          "Close statements",
          "Close connection",
          "Release resources"
        ],
        duration: "10-50ms",
        critical: true
      }
    ]
    
    lifecycle.each do |phase|
      puts "#{phase[:phase]}: #{phase[:description]}"
      puts "  Steps: #{phase[:steps].join(', ')}"
      puts "  Duration: #{phase[:duration]}"
      puts "  Critical: #{phase[:critical]}"
      puts
    end
  end
  
  def self.connection_pooling_benefits
    puts "\nConnection Pooling Benefits:"
    puts "=" * 50
    
    benefits = [
      {
        benefit: "Performance Improvement",
        description: "Reduced connection establishment overhead",
        metrics: ["50-90% faster query execution", "Lower latency", "Higher throughput"],
        impact: "Significant for web applications"
      },
      {
        benefit: "Resource Efficiency",
        description: "Better utilization of database connections",
        metrics: ["Reduced memory usage", "Lower CPU overhead", "Fewer file descriptors"],
        impact: "Better scalability"
      },
      {
        benefit: "Connection Reuse",
        description: "Reuse existing connections instead of creating new ones",
        metrics: ["Connection reuse rate", "Pool hit ratio", "Connection lifetime"],
        impact: "Reduced database load"
      },
      {
        benefit: "Load Distribution",
        description: "Distribute database load across connections",
        metrics: ["Query distribution", "Connection balance", "Load factor"],
        impact: "Better performance under load"
      }
    ]
    
    benefits.each do |benefit|
      puts "#{benefit[:benefit]}:"
      puts "  Description: #{benefit[:description]}"
      puts "  Metrics: #{benefit[:metrics].join(', ')}"
      puts "  Impact: #{benefit[:impact]}"
      puts
    end
  end
  
  # Run connection fundamentals
  explain_connection_concepts
  connection_lifecycle
  connection_pooling_benefits
end
```

### 2. Basic Database Connection

Simple database connection implementation:

```ruby
class BasicDatabaseConnection
  def initialize(connection_string)
    @connection_string = connection_string
    @connected = false
    @driver = nil
    @connection = nil
    @config = parse_connection_string(connection_string)
  end
  
  attr_reader :connected, :config
  
  def connect
    return true if @connected
    
    puts "Connecting to database..."
    
    begin
      # Load appropriate driver
      @driver = load_driver(@config[:adapter])
      
      # Establish connection
      @connection = @driver.connect(@config)
      
      @connected = true
      puts "Connected successfully to #{@config[:adapter]} database '#{@config[:database]}'"
      
      true
    rescue => e
      puts "Connection failed: #{e.message}"
      @connected = false
      false
    end
  end
  
  def disconnect
    return true unless @connected
    
    begin
      @connection.close if @connection
      @connection = nil
      @driver = nil
      @connected = false
      
      puts "Disconnected from database"
      true
    rescue => e
      puts "Disconnection failed: #{e.message}"
      false
    end
  end
  
  def execute_query(sql, params = [])
    raise "Not connected to database" unless @connected
    
    puts "Executing query: #{sql}"
    puts "Parameters: #{params}" if params.any?
    
    begin
      result = @connection.execute(sql, params)
      puts "Query executed successfully"
      result
    rescue => e
      puts "Query execution failed: #{e.message}"
      raise
    end
  end
  
  def execute_update(sql, params = [])
    raise "Not connected to database" unless @connected
    
    puts "Executing update: #{sql}"
    puts "Parameters: #{params}" if params.any?
    
    begin
      result = @connection.execute_update(sql, params)
      puts "Update executed successfully"
      result
    rescue => e
      puts "Update execution failed: #{e.message}"
      raise
    end
  end
  
  def begin_transaction
    raise "Not connected to database" unless @connected
    
    puts "Beginning transaction"
    @connection.begin_transaction
  end
  
  def commit_transaction
    raise "Not connected to database" unless @connected
    
    puts "Committing transaction"
    @connection.commit_transaction
  end
  
  def rollback_transaction
    raise "Not connected to database" unless @connected
    
    puts "Rolling back transaction"
    @connection.rollback_transaction
  end
  
  def self.demonstrate_basic_connection
    puts "Basic Database Connection Demonstration:"
    puts "=" * 50
    
    # Test different database connections
    databases = [
      {
        name: 'PostgreSQL',
        connection_string: 'postgresql://user:password@localhost:5432/myapp_development'
      },
      {
        name: 'MySQL',
        connection_string: 'mysql2://user:password@localhost:3306/myapp_development'
      },
      {
        name: 'SQLite',
        connection_string: 'sqlite3:db/development.sqlite3'
      }
    ]
    
    databases.each do |db|
      puts "\nTesting #{db[:name]} connection:"
      
      begin
        connection = BasicDatabaseConnection.new(db[:connection_string])
        
        # Connect
        if connection.connect
          puts "  Connection successful"
          
          # Execute simple query
          begin
            result = connection.execute_query("SELECT 1 as test")
            puts "  Query successful: #{result}"
          rescue => e
            puts "  Query failed: #{e.message}"
          end
          
          # Disconnect
          connection.disconnect
          puts "  Disconnected"
        else
          puts "  Connection failed"
        end
        
      rescue => e
        puts "  Error: #{e.message}"
      end
    end
    
    puts "\nBasic Connection Features:"
    puts "- Multiple database support"
    puts "- Connection string parsing"
    puts "- Query execution"
    puts "- Transaction management"
    puts "- Error handling"
    puts "- Connection lifecycle management"
  end
  
  private
  
  def parse_connection_string(connection_string)
    # Parse connection string format: adapter://user:password@host:port/database
    match = connection_string.match(/(\w+):\/\/([^:]+):([^@]*)@([^:]+):(\d+)\/(.+)/)
    
    if match
      {
        adapter: match[1],
        username: match[2],
        password: match[3],
        host: match[4],
        port: match[5].to_i,
        database: match[6]
      }
    else
      # Handle SQLite connection string: sqlite3:db/development.sqlite3
      if connection_string.start_with?('sqlite3:')
        {
          adapter: 'sqlite3',
          database: connection_string.split(':').last
        }
      else
        raise "Invalid connection string format"
      end
    end
  end
  
  def load_driver(adapter)
    case adapter
    when 'postgresql'
      PostgreSQLDriver.new
    when 'mysql2'
      MySQLDriver.new
    when 'sqlite3'
      SQLiteDriver.new
    else
      raise "Unsupported database adapter: #{adapter}"
    end
  end
end

# Mock database drivers for demonstration
class PostgreSQLDriver
  def connect(config)
    puts "  [PostgreSQL] Connecting to #{config[:host]}:#{config[:port]}/#{config[:database]}"
    DatabaseConnection.new(config)
  end
end

class MySQLDriver
  def connect(config)
    puts "  [MySQL] Connecting to #{config[:host]}:#{config[:port]}/#{config[:database]}"
    DatabaseConnection.new(config)
  end
end

class SQLiteDriver
  def connect(config)
    puts "  [SQLite] Connecting to #{config[:database]}"
    DatabaseConnection.new(config)
  end
end

class DatabaseConnection
  def initialize(config)
    @config = config
    @connected = true
  end
  
  def execute(sql, params = [])
    puts "    [Database] Executing: #{sql}"
    [{ 'test' => 1 }]
  end
  
  def execute_update(sql, params = [])
    puts "    [Database] Executing update: #{sql}"
    1
  end
  
  def close
    puts "    [Database] Closing connection"
    @connected = false
  end
  
  def begin_transaction
    puts "    [Database] Beginning transaction"
  end
  
  def commit_transaction
    puts "    [Database] Committing transaction"
  end
  
  def rollback_transaction
    puts "    [Database] Rolling back transaction"
  end
end
```

## 🏊 Connection Pooling

### 3. Connection Pool Implementation

Efficient connection pool management:

```ruby
class ConnectionPool
  def initialize(config, options = {})
    @config = config
    @max_connections = options[:max_connections] || 5
    @timeout = options[:timeout] || 5
    @idle_timeout = options[:idle_timeout] || 300
    @pool = []
    @active_connections = {}
    @waiting_queue = []
    @mutex = Mutex.new
    @condition = ConditionVariable.new
    @created_connections = 0
    @closed = false
    
    puts "Connection pool initialized: max_connections=#{@max_connections}, timeout=#{@timeout}s"
  end
  
  def with_connection
    connection = checkout
    begin
      yield connection
    ensure
      checkin(connection)
    end
  end
  
  def checkout
    @mutex.synchronize do
      raise "Connection pool is closed" if @closed
      
      # Try to get existing connection from pool
      connection = get_available_connection
      
      if connection
        mark_connection_active(connection)
        return connection
      end
      
      # Try to create new connection if under limit
      if @created_connections < @max_connections
        connection = create_new_connection
        mark_connection_active(connection)
        return connection
      end
      
      # Wait for available connection
      @waiting_queue << Thread.current
      @condition.wait(@mutex, @timeout)
      
      # Check if we got a connection
      connection = get_available_connection
      if connection
        mark_connection_active(connection)
        return connection
      else
        raise "Connection timeout after #{@timeout}s"
      end
    end
  end
  
  def checkin(connection)
    @mutex.synchronize do
      return unless @active_connections.key?(connection)
      
      mark_connection_inactive(connection)
      @pool << connection
      
      # Signal waiting thread
      if @waiting_queue.any?
        thread = @waiting_queue.shift
        @condition.signal
      end
    end
  end
  
  def close
    @mutex.synchronize do
      @closed = true
      
      # Close all connections
      @pool.each(&:close)
      @active_connections.each_value(&:close)
      
      @pool.clear
      @active_connections.clear
      @waiting_queue.clear
      
      puts "Connection pool closed"
    end
  end
  
  def status
    @mutex.synchronize do
      {
        total_connections: @created_connections,
        available_connections: @pool.length,
        active_connections: @active_connections.length,
        waiting_threads: @waiting_queue.length,
        closed: @closed
      }
    end
  end
  
  def self.demonstrate_connection_pool
    puts "Connection Pool Demonstration:"
    puts "=" * 50
    
    config = {
      adapter: 'postgresql',
      host: 'localhost',
      port: 5432,
      database: 'myapp_development'
    }
    
    pool = ConnectionPool.new(config, max_connections: 3)
    
    puts "Initial pool status:"
    pool_status = pool.status
    pool_status.each { |key, value| puts "  #{key}: #{value}" }
    
    # Simulate concurrent connections
    puts "\nSimulating concurrent connections:"
    
    threads = []
    5.times do |i|
      threads << Thread.new do
        begin
          pool.with_connection do |connection|
            puts "Thread #{i}: Got connection"
            sleep(1) # Simulate work
            puts "Thread #{i}: Releasing connection"
          end
        rescue => e
          puts "Thread #{i}: Error - #{e.message}"
        end
      end
    end
    
    threads.each(&:join)
    
    puts "\nFinal pool status:"
    pool_status = pool.status
    pool_status.each { |key, value| puts "  #{key}: #{value}" }
    
    pool.close
    
    puts "\nConnection Pool Features:"
    puts "- Concurrent connection management"
    puts "- Connection reuse"
    puts "- Timeout handling"
    puts "- Thread safety"
    puts "- Pool status monitoring"
    puts "- Graceful shutdown"
  end
  
  private
  
  def get_available_connection
    # Remove stale connections
    @pool.reject! do |connection|
      connection_stale?(connection)
    end
    
    @pool.first
  end
  
  def create_new_connection
    connection = BasicDatabaseConnection.new(build_connection_string)
    
    if connection.connect
      @created_connections += 1
      puts "Created new connection (total: #{@created_connections})"
      connection
    else
      raise "Failed to create database connection"
    end
  end
  
  def mark_connection_active(connection)
    @active_connections[connection] = {
      thread: Thread.current,
      checkout_time: Time.now
    }
  end
  
  def mark_connection_inactive(connection)
    @active_connections.delete(connection)
  end
  
  def connection_stale?(connection)
    # Check if connection is stale (simplified)
    false
  end
  
  def build_connection_string
    "#{@config[:adapter]}://#{@config[:username]}:#{@config[:password]}@#{@config[:host]}:#{@config[:port]}/#{@config[:database]}"
  end
end

class AdvancedConnectionPool
  def initialize(config, options = {})
    @config = config
    @max_connections = options[:max_connections] || 10
    @min_connections = options[:min_connections] || 2
    @idle_timeout = options[:idle_timeout] || 300
    @max_lifetime = options[:max_lifetime] || 3600
    @validation_interval = options[:validation_interval] || 30
    @retry_attempts = options[:retry_attempts] || 3
    @retry_delay = options[:retry_delay] || 1
    
    @pool = []
    @active_connections = {}
    @waiting_queue = []
    @mutex = Mutex.new
    @condition = ConditionVariable.new
    @closed = false
    @created_connections = 0
    
    # Initialize pool with minimum connections
    initialize_pool
    
    # Start maintenance thread
    start_maintenance_thread
    
    puts "Advanced connection pool initialized"
    puts "  Min connections: #{@min_connections}"
    puts "  Max connections: #{@max_connections}"
    puts "  Idle timeout: #{@idle_timeout}s"
    puts "  Max lifetime: #{@max_lifetime}s"
  end
  
  def with_connection(&block)
    connection = checkout
    begin
      yield connection
    ensure
      checkin(connection)
    end
  end
  
  def checkout
    @mutex.synchronize do
      raise "Connection pool is closed" if @closed
      
      connection = get_valid_connection
      
      if connection
        mark_connection_active(connection)
        return connection
      end
      
      if @created_connections < @max_connections
        connection = create_new_connection
        mark_connection_active(connection)
        return connection
      end
      
      wait_for_connection
    end
  end
  
  def checkin(connection)
    @mutex.synchronize do
      return unless @active_connections.key?(connection)
      
      @pool << connection
      mark_connection_inactive(connection)
      @condition.signal
    end
  end
  
  def close
    @mutex.synchronize do
      @closed = true
      @maintenance_thread&.kill
      
      close_all_connections
      clear_pool
      
      puts "Advanced connection pool closed"
    end
  end
  
  def statistics
    @mutex.synchronize do
      {
        total_connections: @created_connections,
        available_connections: @pool.length,
        active_connections: @active_connections.length,
        waiting_threads: @waiting_queue.length,
        closed: @closed,
        pool_utilization: (@active_connections.length.to_f / @max_connections * 100).round(2)
      }
    end
  end
  
  def self.demonstrate_advanced_pool
    puts "Advanced Connection Pool Demonstration:"
    puts "=" * 50
    
    config = {
      adapter: 'postgresql',
      host: 'localhost',
      port: 5432,
      database: 'myapp_development'
    }
    
    pool = AdvancedConnectionPool.new(config, {
      min_connections: 2,
      max_connections: 5,
      idle_timeout: 60,
      max_lifetime: 300
    })
    
    puts "Initial pool statistics:"
    stats = pool.statistics
    stats.each { |key, value| puts "  #{key}: #{value}" }
    
    # Simulate high load
    puts "\nSimulating high load:"
    
    threads = []
    10.times do |i|
      threads << Thread.new do
        begin
          pool.with_connection do |connection|
            puts "Thread #{i}: Working with connection"
            sleep(rand(0.5..2.0))
          end
        rescue => e
          puts "Thread #{i}: Error - #{e.message}"
        end
      end
    end
    
    threads.each(&:join)
    
    puts "\nFinal pool statistics:"
    stats = pool.statistics
    stats.each { |key, value| puts "  #{key}: #{value}" }
    
    pool.close
    
    puts "\nAdvanced Pool Features:"
    puts "- Minimum connection maintenance"
    puts "- Connection lifetime management"
    puts "- Connection validation"
    puts "- Automatic retry logic"
    puts "- Pool utilization monitoring"
    puts "- Maintenance thread"
    puts "- Graceful shutdown"
  end
  
  private
  
  def initialize_pool
    @min_connections.times do
      connection = create_new_connection
      @pool << connection
    end
    
    puts "Initialized pool with #{@pool.length} connections"
  end
  
  def start_maintenance_thread
    @maintenance_thread = Thread.new do
      loop do
        sleep(@validation_interval)
        maintenance_check unless @closed
        break if @closed
      end
    end
  end
  
  def maintenance_check
    @mutex.synchronize do
      # Remove stale connections
      @pool.reject! { |connection| connection_stale?(connection) }
      
      # Maintain minimum connections
      while @pool.length < @min_connections && @created_connections < @max_connections
        connection = create_new_connection
        @pool << connection
      end
      
      # Close excess connections
      while @pool.length > @min_connections
        connection = @pool.pop
        connection.close
        @created_connections -= 1
      end
    end
  end
  
  def get_valid_connection
    @pool.find do |connection|
      connection_valid?(connection)
    end
  end
  
  def wait_for_connection
    @waiting_queue << Thread.current
    @condition.wait(@mutex, @timeout)
    
    get_valid_connection
  end
  
  def create_new_connection
    connection = BasicDatabaseConnection.new(build_connection_string)
    
    @retry_attempts.times do |attempt|
      if connection.connect
        @created_connections += 1
        return connection
      else
        puts "Connection attempt #{attempt + 1} failed, retrying..."
        sleep(@retry_delay) if attempt < @retry_attempts - 1
      end
    end
    
    raise "Failed to create database connection after #{@retry_attempts} attempts"
  end
  
  def mark_connection_active(connection)
    @active_connections[connection] = {
      thread: Thread.current,
      checkout_time: Time.now,
      created_at: Time.now
    }
  end
  
  def mark_connection_inactive(connection)
    @active_connections.delete(connection)
  end
  
  def connection_valid?(connection)
    # Simple validation check
    connection && !connection_stale?(connection)
  end
  
  def connection_stale?(connection)
    info = @active_connections[connection]
    return false unless info
    
    # Check if connection is too old
    Time.now - info[:created_at] > @max_lifetime
  end
  
  def close_all_connections
    @pool.each(&:close)
    @active_connections.each_value(&:close)
  end
  
  def clear_pool
    @pool.clear
    @active_connections.clear
    @waiting_queue.clear
  end
  
  def build_connection_string
    "#{@config[:adapter]}://#{@config[:username]}:#{@config[:password]}@#{@config[:host]}:#{@config[:port]}/#{@config[:database]}"
  end
end
```

## 🔄 Transaction Management

### 4. Transaction Framework

Advanced transaction management:

```ruby
class TransactionManager
  def initialize(connection)
    @connection = connection
    @transactions = []
    @current_transaction = nil
  end
  
  def transaction(&block)
    begin_transaction
    result = yield
    commit_transaction
    result
  rescue => e
      rollback_transaction
    raise e
    ensure
      end_transaction
    end
  end
  
  def nested_transaction(&block)
    if @current_transaction
      # Use savepoint for nested transaction
      savepoint = create_savepoint
      begin
        result = yield
        release_savepoint(savepoint)
        result
      rescue => e
        rollback_to_savepoint(savepoint)
        raise e
      end
    else
      # Create new transaction
      transaction(&block)
    end
  end
  
  def begin_transaction
    @connection.begin_transaction
    @current_transaction = {
      id: SecureRandom.hex(8),
      savepoints: [],
      started_at: Time.now
    }
    @transactions << @current_transaction
  end
  
  def commit_transaction
    raise "No active transaction" unless @current_transaction
    
    @connection.commit_transaction
    @transactions.pop
    @current_transaction = @transactions.last
  end
  
  def rollback_transaction
    raise "No active transaction" unless @current_transaction
    
    @connection.rollback_transaction
    @transactions.pop
    @current_transaction = @transactions.last
  end
  
  def end_transaction
    @current_transaction = nil
  end
  
  def create_savepoint
    raise "No active transaction" unless @current_transaction
    
    savepoint_id = "sp_#{@current_transaction[:savepoints].length + 1}"
    
    begin
      @connection.execute("SAVEPOINT #{savepoint_id}")
      @current_transaction[:savepoints] << savepoint_id
      savepoint_id
    rescue => e
      puts "Failed to create savepoint: #{e.message}"
      raise
    end
  end
  
  def release_savepoint(savepoint_id)
    @connection.execute("RELEASE SAVEPOINT #{savepoint_id}")
    @current_transaction[:savepoints].delete(savepoint_id)
  end
  
  def rollback_to_savepoint(savepoint_id)
    @connection.execute("ROLLBACK TO SAVEPOINT #{savepoint_id}")
    
    # Remove savepoints created after this one
    index = @current_transaction[:savepoints].index(savepoint_id)
    @current_transaction[:savepoints] = @current_transaction[:savepoints][0..index]
  end
  
  def transaction_status
    {
      active: !@current_transaction.nil?,
      transaction_count: @transactions.length,
      current_id: @current_transaction&.dig(:id),
      savepoints: @current_transaction&.dig(:savepoints) || [],
      duration: @current_transaction ? Time.now - @current_transaction[:started_at] : 0
    }
  end
  
  def self.demonstrate_transactions
    puts "Transaction Management Demonstration:"
    puts "=" * 50
    
    # Create mock connection
    connection = MockConnection.new
    manager = TransactionManager.new(connection)
    
    puts "Basic Transaction:"
    manager.transaction do
      puts "  Executing operations in transaction"
      connection.execute("INSERT INTO users (name) VALUES ('Alice')")
      connection.execute("INSERT INTO users (name) VALUES ('Bob')")
      puts "  Operations completed successfully"
    end
    
    puts "\nNested Transaction:"
    manager.transaction do
      puts "  Outer transaction started"
      
      manager.nested_transaction do
        puts "  Inner transaction (savepoint) started"
        connection.execute("INSERT INTO posts (title) VALUES ('Post 1')")
        puts "  Inner transaction completed"
      end
      
      connection.execute("INSERT INTO posts (title) VALUES ('Post 2')")
      puts "  Outer transaction completed"
    end
    
    puts "\nTransaction Rollback:"
    begin
      manager.transaction do
        puts "  Transaction started"
        connection.execute("INSERT INTO users (name) VALUES ('Charlie')")
        puts "  Simulating error..."
        raise "Simulated error"
      end
    rescue => e
      puts "  Error caught: #{e.message}"
      puts "  Transaction rolled back"
    end
    
    puts "\nTransaction Status:"
    status = manager.transaction_status
    status.each { |key, value| puts "  #{key}: #{value}" }
    
    puts "\nTransaction Features:"
    puts "- Automatic transaction management"
    puts "- Nested transaction support"
    puts "- Savepoint handling"
    puts "- Automatic rollback on error"
    puts "- Transaction status tracking"
    puts "- Error handling"
  end
end

class MockConnection
  def initialize
    @transactions = []
    @current_transaction = nil
  end
  
  def begin_transaction
    @current_transaction = "transaction_#{@transactions.length + 1}"
    @transactions << @current_transaction
    puts "    [Database] BEGIN TRANSACTION"
  end
  
  def commit_transaction
    puts "    [Database] COMMIT TRANSACTION"
    @current_transaction = nil
  end
  
  def rollback_transaction
    puts "    [Database] ROLLBACK TRANSACTION"
    @current_transaction = nil
  end
  
  def execute(sql)
    puts "    [Database] Executing: #{sql}"
    true
  end
end
```

## 🔌 Database Adapters

### 5. Database Adapter Framework

Multiple database support:

```ruby
class DatabaseAdapter
  def initialize(config)
    @config = config
    @connection = nil
    @connected = false
  end
  
  attr_reader :connected, :config
  
  def connect
    raise NotImplementedError, "Subclass must implement connect method"
  end
  
  def disconnect
    raise NotImplementedError, "Subclass must implement disconnect method"
  end
  
  def execute_query(sql, params = [])
    raise NotImplementedError, "Subclass must implement execute_query method"
  end
  
  def execute_update(sql, params = [])
    raise NotImplementedError, "Subclass must implement execute_update method"
  end
  
  def begin_transaction
    raise NotImplementedError, "Subclass must implement begin_transaction method"
  end
  
  def commit_transaction
    raise NotImplementedError, "Subclass must implement commit_transaction method"
  end
  
  def rollback_transaction
    raise NotImplementedError, "Subclass must implement rollback_transaction method"
  end
  
  def table_exists?(table_name)
    raise NotImplementedError, "Subclass must implement table_exists? method"
  end
  
  def get_table_schema(table_name)
    raise NotImplementedError, "Subclass must implement get_table_schema method"
  end
  
  def quote_identifier(identifier)
    raise NotImplementedError, "Subclass must implement quote_identifier method"
  end
  
  def quote_string(string)
    raise NotImplementedError, "Subclass must implement quote_string method"
  end
  
  def self.create_adapter(config)
    adapter_type = config[:adapter] || config['adapter']
    
    case adapter_type
    when 'postgresql'
      PostgreSQLAdapter.new(config)
    when 'mysql', 'mysql2'
      MySQLAdapter.new(config)
    when 'sqlite', 'sqlite3'
      SQLiteAdapter.new(config)
    when 'sqlserver', 'tiny_tds'
      SQLServerAdapter.new(config)
    else
      raise "Unsupported adapter: #{adapter_type}"
    end
  end
end

class PostgreSQLAdapter < DatabaseAdapter
  def connect
    puts "  [PostgreSQL] Connecting to #{@config[:host]}:#{@config[:port]}/#{@config[:database]}"
    @connected = true
  end
  
  def disconnect
    puts "  [PostgreSQL] Disconnecting"
    @connected = false
  end
  
  def execute_query(sql, params = [])
    puts "  [PostgreSQL] Query: #{sql}"
    puts "  [PostgreSQL] Params: #{params}" if params.any?
    
    # Simulate PostgreSQL query execution
    [{ 'id' => 1, 'name' => 'Test Record'}]
  end
  
  def execute_update(sql, params = [])
    puts "  [PostgreSQL] Update: #{sql}"
    puts "  [PostgreSQL] Params: #{params}" if params.any?
    1
  end
  
  def begin_transaction
    puts "  [PostgreSQL] BEGIN TRANSACTION"
  end
  
  def commit_transaction
    puts "  [PostgreSQL] COMMIT TRANSACTION"
  end
  
  def rollback_transaction
    puts "  [PostgreSQL] ROLLBACK TRANSACTION"
  end
  
  def table_exists?(table_name)
    puts "  [PostgreSQL] Checking if table #{table_name} exists"
    true
  end
  
  def get_table_schema(table_name)
    puts "  [PostgreSQL] Getting schema for #{table_name}"
    {
      columns: [
        { name: 'id', type: 'integer', null: false, default: nil },
        { name: 'name', type: 'varchar', null: false, default: nil },
        { name: 'created_at', type: 'timestamp', null: false, default: 'CURRENT_TIMESTAMP' }
      ],
      indexes: [
        { name: 'users_pkey', columns: ['id'], unique: true }
      ]
    }
  end
  
  def quote_identifier(identifier)
    "\"#{identifier}\""
  end
  
  def quote_string(string)
    "'#{string.gsub("'", "''")}'"
  end
end

class MySQLAdapter < DatabaseAdapter
  def connect
    puts "  [MySQL] Connecting to #{@config[:host]}:#{@config[:port]}/#{@config[:database]}"
    @connected = true
  end
  
  def disconnect
    puts "  [MySQL] Disconnecting"
    @connected = false
  end
  
  def execute_query(sql, params = [])
    puts "  [MySQL] Query: #{sql}"
    puts "  [MySQL] Params: #{params}" if params.any?
    [{ 'id' => 1, 'name' => 'Test Record'}]
  end
  
  def execute_update(sql, params = [])
    puts "  [MySQL] Update: #{sql}"
    puts "  [MySQL] Params: #{params}" if params.any?
    1
  end
  
  def begin_transaction
    puts "  [MySQL] START TRANSACTION"
  end
  
  def commit_transaction
    puts "  [MySQL] COMMIT"
  end
  
  def rollback_transaction
    puts "  [MySQL] ROLLBACK"
  end
  
  def table_exists?(table_name)
    puts "  [MySQL] Checking if table #{table_name} exists"
    true
  end
  
  def get_table_schema(table_name)
    puts "  [MySQL] Getting schema for #{table_name}"
    {
      columns: [
        { name: 'id', type: 'int', null: false, default: nil },
        { name: 'name', type: 'varchar', null: false, default: nil },
        { name: 'created_at', type: 'datetime', null: false, default: 'CURRENT_TIMESTAMP' }
      ],
      indexes: [
        { name: 'PRIMARY', columns: ['id'], unique: true }
      ]
    }
  end
  
  def quote_identifier(identifier)
    "`#{identifier}`"
  end
  
  def quote_string(string)
    "'#{string.gsub("'", "\\'")}'"
  end
end

class SQLiteAdapter < DatabaseAdapter
  def connect
    puts "  [SQLite] Connecting to #{@config[:database]}"
    @connected = true
  end
  
  def disconnect
    puts "  [SQLite] Disconnecting"
    @connected = false
  end
  
  def execute_query(sql, params = [])
    puts "  [SQLite] Query: #{sql}"
    puts "  [SQLite] Params: #{params}" if params.any?
    [{ 'id' => 1, 'name' => 'Test Record'}]
  end
  
  def execute_update(sql, params = [])
    puts "  [SQLite] Update: #{sql}"
    puts "  [SQLite] Params: #{params}" if params.any?
    1
  end
  
  def begin_transaction
    puts "  [SQLite] BEGIN TRANSACTION"
  end
  
  def commit_transaction
    puts "  [SQLite] COMMIT TRANSACTION"
  end
  
  def rollback_transaction
    puts "  [SQLite] ROLLBACK TRANSACTION"
  end
  
  def table_exists?(table_name)
    puts "  [SQLite] Checking if table #{table_name} exists"
    true
  end
  
  def get_table_schema(table_name)
    puts "  [SQLite] Getting schema for #{table_name}"
    {
      columns: [
        { name: 'id', type: 'INTEGER', null: false, default: nil },
        { name: 'name', type: 'TEXT', null: false, default: nil },
        { name: 'created_at', type: 'DATETIME', null: false, default: 'CURRENT_TIMESTAMP' }
      ],
      indexes: []
    }
  end
  
  def quote_identifier(identifier)
    "`#{identifier}`"
  end
  
  def quote_string(string)
    "'#{string.gsub("'", "''")}'"
  end
end

class DatabaseAdapterDemo
  def self.demonstrate_adapters
    puts "Database Adapter Framework Demonstration:"
    puts "=" * 50
    
    # Test different adapters
    adapters = [
      {
        name: 'PostgreSQL',
        config: { adapter: 'postgresql', host: 'localhost', port: 5432, database: 'myapp_development' }
      },
      {
        name: 'MySQL',
        config: { adapter: 'mysql2', host: 'localhost', port: 3306, database: 'myapp_development' }
      },
      {
        name: 'SQLite',
        config: { adapter: 'sqlite3', database: 'db/development.sqlite3' }
      }
    ]
    
    adapters.each do |adapter|
      puts "\nTesting #{adapter[:name]} Adapter:"
      
      begin
        db_adapter = DatabaseAdapter.create_adapter(adapter[:config])
        
        # Connect
        db_adapter.connect
        puts "  Connected successfully"
        
        # Execute query
        result = db_adapter.execute_query("SELECT 1 as test")
        puts "  Query result: #{result}"
        
        # Get table schema
        schema = db_adapter.get_table_schema('users')
        puts "  Table schema: #{schema[:columns].length} columns, #{schema[:indexes].length} indexes"
        
        # Test quoting
        identifier = db_adapter.quote_identifier('table_name')
        string = db_adapter.quote_string("test'string")
        puts "  Quoted identifier: #{identifier}"
        puts "  Quoted string: #{string}"
        
        # Disconnect
        db_adapter.disconnect
        puts "  Disconnected"
        
      rescue => e
        puts "  Error: #{e.message}"
      end
    end
    
    puts "\nAdapter Framework Features:"
    puts "- Multiple database support"
    puts "- Uniform interface"
    "- Database-specific optimizations"
    "- Connection management"
    "- SQL generation"
    "- Schema introspection"
    "- Identifier quoting"
    "- String escaping"
  end
end
```

## 🎯 Connection Management Best Practices

### 6. Best Practices and Guidelines

Connection management recommendations:

```ruby
class ConnectionBestPractices
  def self.connection_guidelines
    puts "Database Connection Best Practices:"
    puts "=" * 50
    
    guidelines = [
      {
        category: "Connection Pooling",
        practices: [
          "Use connection pools for web applications",
          "Set appropriate pool size based on concurrency",
          "Configure timeout values for connection acquisition",
          "Monitor pool utilization and performance",
          "Implement connection validation and health checks"
        ]
      },
      {
        category: "Transaction Management",
        practices: [
          "Keep transactions short and focused",
          "Always handle exceptions and rollback on failure",
          "Use nested transactions with savepoints when needed",
          "Avoid long-running transactions",
          "Implement proper isolation levels"
        ]
      },
      {
        category: "Error Handling",
        practices: [
          "Implement retry logic for transient failures",
          "Use circuit breakers for database dependencies",
          "Log connection errors appropriately",
          "Implement graceful degradation",
          "Monitor database health and performance"
        ]
      },
      {
        category: "Performance Optimization",
        practices: [
          "Use prepared statements for repeated queries",
          "Implement query result caching",
          "Optimize connection pool configuration",
          "Use connection multiplexing when appropriate",
          "Monitor and tune database performance"
        ]
      },
      {
        category: "Security",
        practices: [
          "Use SSL/TLS for database connections",
          "Store credentials securely",
          "Implement connection encryption",
          "Use least privilege database users",
          "Audit database access regularly"
        ]
      },
      {
        category: "Monitoring and Logging",
        practices: [
          "Log connection acquisition and release",
          "Monitor pool metrics and statistics",
          "Track transaction duration and success rate",
          "Implement alerting for connection issues",
          "Regular health checks and maintenance"
        ]
      }
    ]
    
    guidelines.each do |guideline|
      puts "#{guideline[:category]}:"
      guideline[:practices].each_with_index do |practice, i|
        puts "  #{i + 1}. #{practice}"
      end
      puts
    end
  end
  
  def self.performance_tips
    puts "\nPerformance Tips:"
    puts "=" * 50
    
    tips = [
      {
        tip: "Pool Size Optimization",
        description: "Set pool size based on application concurrency",
        formula: "Pool size = (CPU cores * 2) + effective spool size",
        example: "4 cores = 8-10 connections"
      },
      {
        tip: "Connection Reuse",
        description: "Reuse connections instead of creating new ones",
        benefit: "50-90% performance improvement",
        implementation: "Use connection pooling"
      },
      {
        tip: "Query Optimization",
        description: "Optimize queries to reduce connection time",
        techniques: ["Use indexes", "Avoid N+1 queries", "Batch operations"],
        impact: "Significant performance gains"
      },
      {
        tip: "Timeout Configuration",
        description: "Set appropriate timeouts for connection operations",
        recommendations: [
          "Connection timeout: 5-10 seconds",
          "Query timeout: 30-60 seconds",
          "Pool timeout: 5-30 seconds"
        ]
      },
      {
        tip: "Connection Validation",
        description: "Validate connections before returning from pool",
        methods: ["Ping query", "Connection health check", "Last used timestamp"],
        frequency: "Every checkout or periodic validation"
      }
    ]
    
    tips.each do |tip|
      puts "#{tip[:tip]}:"
      puts "  Description: #{tip[:description]}"
      puts "  Formula: #{tip[:formula]}" if tip[:formula]
      puts "  Example: #{tip[:example]}" if tip[:example]
      puts "  Benefit: #{tip[:benefit]}" if tip[:benefit]
      puts "  Implementation: #{tip[:implementation]}" if tip[:implementation]
      puts "  Techniques: #{tip[:techniques].join(', ')}" if tip[:techniques]
      puts "  Impact: #{tip[:impact]}" if tip[:impact]
      puts "  Recommendations: #{tip[:recommendations].join(', ')}" if tip[:recommendations]
      puts "  Methods: #{tip[:methods].join(', ')}" if tip[:methods]
      puts "  Frequency: #{tip[:frequency]}" if tip[:frequency]
      puts
    end
  end
  
  def self.common_mistakes
    puts "\nCommon Connection Management Mistakes:"
    puts "=" * 50
    
    mistakes = [
      {
        mistake: "Not Using Connection Pools",
        description: "Creating new connections for every query",
        impact: "Poor performance, resource exhaustion",
        solution: "Implement proper connection pooling"
      },
      {
        mistake: "Long-Running Transactions",
        description: "Keeping transactions open for extended periods",
        impact: "Resource contention, deadlocks",
        solution: "Keep transactions short and focused"
      },
      {
        mistake: "Ignoring Connection Leaks",
        description: "Not properly closing or returning connections",
        impact: "Pool exhaustion, memory leaks",
        solution: "Use ensure blocks or try/finally"
      },
      {
        mistake: "No Connection Validation",
        description: "Not checking if connections are still valid",
        impact: "Stale connections, unexpected errors",
        solution: "Implement connection health checks"
      },
      {
        mistake: "Poor Pool Configuration",
        description: "Using default or inappropriate pool settings",
        impact: "Suboptimal performance",
        solution: "Tune pool size and timeouts"
      },
      {
        mistake: "No Error Handling",
        description: "Not handling connection failures gracefully",
        impact: "Application crashes, poor user experience",
        solution: "Implement retry logic and fallbacks"
      }
    ]
    
    mistakes.each do |mistake|
      puts "#{mistake[:mistake]}:"
      puts "  Description: #{mistake[:description]}"
      puts "  Impact: #{mistake[:impact]}"
      puts "  Solution: #{mistake[:solution]}"
      puts
    end
  end
  
  def self.monitoring_metrics
    puts "\nConnection Monitoring Metrics:"
    puts "=" * 50
    
    metrics = [
      {
        metric: "Pool Utilization",
        description: "Percentage of connections currently in use",
        formula: "(Active Connections / Total Connections) * 100",
        threshold: "Warning at 80%, Critical at 95%",
        action: "Scale pool or optimize queries"
      },
      {
        metric: "Connection Wait Time",
        description: "Time spent waiting for available connection",
        formula: "Average wait time across all requests",
        threshold: "Warning at 100ms, Critical at 500ms",
        action: "Increase pool size or optimize queries"
      },
      {
        metric: "Connection Creation Rate",
        description: "Rate of new connection creation",
        formula: "New connections per minute",
        threshold: "High rate indicates pool exhaustion",
        action: "Increase pool size or check for leaks"
      },
      {
        metric: "Connection Lifetime",
        description: "Average time connections remain active",
        formula: "Average checkout duration",
        threshold: "Too long may indicate connection leaks",
        action: "Review connection usage patterns"
      },
      {
        metric: "Query Execution Time",
        description: "Time taken to execute database queries",
        formula: "Average query duration",
        threshold: "Warning at 100ms, Critical at 1000ms",
        action: "Optimize queries or database performance"
      }
    ]
    
    metrics.each do |metric|
      puts "#{metric[:metric]}:"
      puts "  Description: #{metric[:description]}"
      puts "  Formula: #{metric[:formula]}"
      puts "  Threshold: #{metric[:threshold]}"
      puts "  Action: #{metric[:action]}"
      puts
    end
  end
  
  def self.demonstrate_best_practices
    connection_guidelines
    performance_tips
    common_mistakes
    monitoring_metrics
    
    puts "\nBest Practices Summary:"
    puts "- Use connection pooling for all database access"
    puts "- Keep transactions short and focused"
    "- Implement proper error handling and retry logic"
    puts "- Monitor connection pool metrics and performance"
    puts "- Use SSL/TLS for secure connections"
    puts "- Validate connections and handle failures gracefully"
    puts "- Optimize pool size based on application needs"
    puts "- Implement proper logging and monitoring"
    puts "- Regular maintenance and health checks"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Connection**: Implement simple database connection
2. **Connection Pool**: Create basic connection pool
3. **Transaction Management**: Add transaction support
4. **Database Adapter**: Build adapter framework

### Intermediate Exercises

1. **Advanced Pooling**: Implement sophisticated pooling
2. **Multiple Databases**: Support different database types
3. **Connection Health**: Add health checking
4. **Performance Optimization**: Optimize connection usage

### Advanced Exercises

1. **Enterprise Pooling**: Production-ready connection pool
2. **Connection Multiplexing**: Advanced connection sharing
3. **Distributed Connections**: Multi-database management
4. **Monitoring System**: Comprehensive monitoring solution

---

## 🎯 Summary

Database Connections in Ruby provide:

- **Connection Fundamentals** - Core concepts and lifecycle
- **Basic Implementation** - Simple connection management
- **Connection Pooling** - Efficient connection reuse
- **Transaction Management** - Advanced transaction handling
- **Database Adapters** - Multiple database support
- **Best Practices** - Performance and reliability guidelines

Master these database connection techniques for robust Ruby applications!
