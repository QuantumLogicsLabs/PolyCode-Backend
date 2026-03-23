# Database Programming Examples
# Demonstrating database operations, ORM usage, and data management

puts "=== ACTIVE RECORD BASICS ==="

# Simulate ActiveRecord-like functionality
class ActiveRecord
  @@database = {}
  @@next_id = 1
  
  def self.table_name
    name.downcase + 's'
  end
  
  def self.create(attributes)
    id = @@next_id
    @@next_id += 1
    
    record = attributes.merge(id: id, created_at: Time.now, updated_at: Time.now)
    @@database[table_name] ||= []
    @@database[table_name] << record
    
    new(record)
  end
  
  def self.all
    records = @@database[table_name] || []
    records.map { |record| new(record) }
  end
  
  def self.find(id)
    records = @@database[table_name] || []
    record = records.find { |r| r[:id] == id }
    record ? new(record) : nil
  end
  
  def self.where(conditions)
    records = @@database[table_name] || []
    filtered = records.select do |record|
      conditions.all? { |key, value| record[key] == value }
    end
    filtered.map { |record| new(record) }
  end
  
  def self.find_by(conditions)
    where(conditions).first
  end
  
  def self.count
    records = @@database[table_name] || []
    records.length
  end
  
  def self.destroy_all
    @@database[table_name] = []
  end
  
  def initialize(attributes = {})
    @attributes = attributes
    @changed_attributes = {}
  end
  
  def id
    @attributes[:id]
  end
  
  def method_missing(method_name, *args)
    if method_name.to_s.end_with?('=')
      attribute = method_name.to_s.chomp('=').to_sym
      @changed_attributes[attribute] = @attributes[attribute]
      @attributes[attribute] = args.first
    elsif @attributes.key?(method_name)
      @attributes[method_name]
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    @attributes.key?(method_name) || method_name.to_s.end_with?('=') || super
  end
  
  def save
    if id
      # Update existing record
      records = self.class.send(:class_variable_get, :@@database)[self.class.table_name] || []
      record = records.find { |r| r[:id] == id }
      
      if record
        @changed_attributes.each { |key, value| record[key] = @attributes[key] }
        record[:updated_at] = Time.now
        @changed_attributes.clear
        true
      else
        false
      end
    else
      # Create new record
      self.class.create(@attributes)
      true
    end
  end
  
  def destroy
    records = self.class.send(:class_variable_get, :@@database)[self.class.table_name] || []
    records.delete_if { |record| record[:id] == id }
  end
  
  def attributes
    @attributes.dup
  end
  
  def changed?
    @changed_attributes.any?
  end
  
  def changed
    @changed_attributes.keys
  end
end

class User < ActiveRecord
  def self.active
    where(active: true)
  end
  
  def self.by_name(name)
    where(name: name)
  end
  
  def posts
    Post.where(user_id: id)
  end
  
  def full_name
    "#{first_name} #{last_name}"
  end
end

class Post < ActiveRecord
  def self.published
    where(published: true)
  end
  
  def self.recent
    all.sort_by(&:created_at).reverse
  end
  
  def user
    User.find(user_id)
  end
  
  def publish!
    self.published = true
    save
  end
  
  def word_count
    content.split(/\s+/).length
  end
end

puts "ActiveRecord-like functionality:"

# Create users
user1 = User.create(name: "John Doe", email: "john@example.com", active: true)
user2 = User.create(name: "Jane Smith", email: "jane@example.com", active: false)
user3 = User.create(name: "Bob Wilson", email: "bob@example.com", active: true)

puts "Created users: #{User.count}"

# Find users
all_users = User.all
puts "All users: #{all_users.map(&:name).join(', ')}"

active_users = User.active
puts "Active users: #{active_users.map(&:name).join(', ')}"

john = User.find_by(name: "John Doe")
puts "Found John: #{john.name} (#{john.email})"

# Create posts
post1 = Post.create(title: "First Post", content: "This is my first post content", user_id: user1.id, published: true)
post2 = Post.create(title: "Second Post", content: "This is my second post content", user_id: user1.id, published: false)
post3 = Post.create(title: "Jane's Post", content: "This is Jane's post content", user_id: user2.id, published: true)

puts "Created posts: #{Post.count}"

# Query posts
published_posts = Post.published
puts "Published posts: #{published_posts.map(&:title).join(', ')}"

recent_posts = Post.recent
puts "Recent posts: #{recent_posts.first(2).map(&:title).join(', ')}"

# Associations
john_posts = john.posts
puts "John's posts: #{john_posts.map(&:title).join(', ')}"

post_user = post1.user
puts "Post 1 author: #{post_user.name}"

puts "\n=== SQL OPERATIONS ==="

class SQLSimulator
  def initialize
    @tables = {}
  end
  
  def create_table(name, columns)
    @tables[name] = {
      columns: columns,
      data: []
    }
    puts "Created table: #{name} with columns: #{columns.join(', ')}"
  end
  
  def insert(table_name, values)
    table = @tables[table_name]
    return "Table #{table_name} does not exist" unless table
    
    if values.length != table[:columns].length
      return "Column count mismatch"
    end
    
    record = Hash[table[:columns].zip(values)]
    record[:id] = table[:data].length + 1
    table[:data] << record
    
    "Inserted record with ID: #{record[:id]}"
  end
  
  def select(table_name, columns = '*', conditions = {})
    table = @tables[table_name]
    return "Table #{table_name} does not exist" unless table
    
    results = table[:data].dup
    
    # Apply conditions
    conditions.each do |column, value|
      results.select! { |record| record[column] == value }
    end
    
    # Select columns
    if columns == '*'
      results
    else
      results.map { |record| record.slice(*columns) }
    end
  end
  
  def update(table_name, values, conditions = {})
    table = @tables[table_name]
    return "Table #{table_name} does not exist" unless table
    
    updated_count = 0
    
    table[:data].each do |record|
      match = conditions.all? { |column, value| record[column] == value }
      
      if match
        values.each { |column, value| record[column] = value }
        updated_count += 1
      end
    end
    
    "Updated #{updated_count} records"
  end
  
  def delete(table_name, conditions = {})
    table = @tables[table_name]
    return "Table #{table_name} does not exist" unless table
    
    original_size = table[:data].length
    
    table[:data].reject! do |record|
      conditions.all? { |column, value| record[column] == value }
    end
    
    deleted_count = original_size - table[:data].length
    "Deleted #{deleted_count} records"
  end
  
  def join(table1, table2, on_condition)
    table1_data = @tables[table1][:data]
    table2_data = @tables[table2][:data]
    
    # Simple join simulation
    column1, column2 = on_condition.split('=').map(&:strip)
    column1 = column1.split('.').last.to_sym
    column2 = column2.split('.').last.to_sym
    
    results = []
    
    table1_data.each do |record1|
      table2_data.each do |record2|
        if record1[column1] == record2[column2]
          results << record1.merge(record2)
        end
      end
    end
    
    results
  end
end

puts "SQL Operations Example:"

db = SQLSimulator.new

# Create tables
db.create_table(:users, [:id, :name, :email, :created_at])
db.create_table(:posts, [:id, :title, :content, :user_id, :created_at])

# Insert data
puts db.insert(:users, ["John Doe", "john@example.com", Time.now])
puts db.insert(:users, ["Jane Smith", "jane@example.com", Time.now])
puts db.insert(:posts, ["First Post", "Content here", 1, Time.now])
puts db.insert(:posts, ["Second Post", "More content", 1, Time.now])
puts db.insert(:posts, ["Jane's Post", "Jane's content", 2, Time.now])

# Select operations
users = db.select(:users)
puts "All users: #{users.map { |u| u[:name] }.join(', ')}"

john_posts = db.select(:posts, '*', { user_id: 1 })
puts "John's posts: #{john_posts.map { |p| p[:title] }.join(', ')}"

# Update operation
puts db.update(:users, { name: "John Updated" }, { id: 1 })

# Delete operation
puts db.delete(:posts, { id: 2 })

# Join operation
joined_data = db.join(:users, :posts, "users.id = posts.user_id")
puts "Joined data: #{joined_data.length} records"

puts "\n=== DATABASE CONNECTIONS ==="

class ConnectionPool
  def initialize(size = 5)
    @pool = Queue.new
    @connections = []
    
    size.times do |i|
      connection = "Connection#{i + 1}"
      @connections << connection
      @pool.push(connection)
    end
    
    @created_at = Time.now
  end
  
  def with_connection
    connection = @pool.pop
    begin
      yield connection
    ensure
      @pool.push(connection)
    end
  end
  
  def size
    @connections.length
  end
  
  def available
    @pool.length
  end
  
  def in_use
    size - available
  end
  
  def stats
    {
      total: size,
      available: available,
      in_use: in_use,
      created_at: @created_at
    }
  end
end

class DatabaseManager
  def initialize
    @pools = {}
  end
  
  def add_pool(name, size = 5)
    @pools[name] = ConnectionPool.new(size)
    puts "Added connection pool '#{name}' with #{size} connections"
  end
  
  def execute_query(pool_name, query)
    pool = @pools[pool_name]
    return "Pool '#{pool_name}' not found" unless pool
    
    pool.with_connection do |connection|
      # Simulate query execution
      puts "Executing '#{query}' on #{connection}"
      "Results from #{connection}"
    end
  end
  
  def pool_stats(pool_name)
    pool = @pools[pool_name]
    return "Pool '#{pool_name}' not found" unless pool
    pool.stats
  end
end

puts "Connection Pool Example:"

db_manager = DatabaseManager.new
db_manager.add_pool(:primary, 3)
db_manager.add_pool(:analytics, 2)

# Execute queries
result1 = db_manager.execute_query(:primary, "SELECT * FROM users")
result2 = db_manager.execute_query(:analytics, "SELECT COUNT(*) FROM events")

# Show pool stats
puts "Primary pool stats: #{db_manager.pool_stats(:primary)}"
puts "Analytics pool stats: #{db_manager.pool_stats(:analytics)}"

puts "\n=== TRANSACTIONS ==="

class TransactionManager
  def initialize
    @transactions = {}
    @transaction_counter = 0
  end
  
  def begin_transaction
    transaction_id = "tx_#{@transaction_counter += 1}"
    @transactions[transaction_id] = {
      operations: [],
      status: :active,
      started_at: Time.now
    }
    
    puts "Started transaction: #{transaction_id}"
    transaction_id
  end
  
  def execute(transaction_id, operation)
    transaction = @transactions[transaction_id]
    return "Transaction #{transaction_id} not found" unless transaction
    return "Transaction #{transaction_id} is not active" unless transaction[:status] == :active
    
    # Record operation
    transaction[:operations] << {
      operation: operation,
      timestamp: Time.now
    }
    
    puts "Executed in #{transaction_id}: #{operation}"
    true
  end
  
  def commit(transaction_id)
    transaction = @transactions[transaction_id]
    return "Transaction #{transaction_id} not found" unless transaction
    return "Transaction #{transaction_id} already #{transaction[:status]}" unless transaction[:status] == :active
    
    transaction[:status] = :committed
    transaction[:committed_at] = Time.now
    
    puts "Committed transaction: #{transaction_id} (#{transaction[:operations].length} operations)"
    true
  end
  
  def rollback(transaction_id)
    transaction = @transactions[transaction_id]
    return "Transaction #{transaction_id} not found" unless transaction
    return "Transaction #{transaction_id} already #{transaction[:status]}" unless transaction[:status] == :active
    
    transaction[:status] = :rolled_back
    transaction[:rolled_back_at] = Time.now
    
    puts "Rolled back transaction: #{transaction_id} (#{transaction[:operations].length} operations)"
    true
  end
  
  def transaction_stats
    {
      total: @transactions.length,
      active: @transactions.count { |_, t| t[:status] == :active },
      committed: @transactions.count { |_, t| t[:status] == :committed },
      rolled_back: @transactions.count { |_, t| t[:status] == :rolled_back }
    }
  end
end

puts "Transaction Example:"

tx_manager = TransactionManager.new

# Begin transaction
tx1 = tx_manager.begin_transaction
tx2 = tx_manager.begin_transaction

# Execute operations
tx_manager.execute(tx1, "INSERT INTO users (name) VALUES ('Alice')")
tx_manager.execute(tx1, "UPDATE posts SET title = 'Updated' WHERE id = 1")
tx_manager.execute(tx2, "DELETE FROM comments WHERE post_id = 1")

# Commit one, rollback one
tx_manager.commit(tx1)
tx_manager.rollback(tx2)

# Show stats
puts "Transaction stats: #{tx_manager.transaction_stats}"

puts "\n=== MIGRATIONS ==="

class MigrationManager
  def initialize
    @migrations = []
    @applied_migrations = []
  end
  
  def create_migration(name, up_sql, down_sql)
    migration = {
      id: @migrations.length + 1,
      name: name,
      up_sql: up_sql,
      down_sql: down_sql,
      created_at: Time.now
    }
    
    @migrations << migration
    puts "Created migration: #{migration[:id]} - #{name}"
    migration
  end
  
  def migrate_up(target_version = nil)
    target_version ||= @migrations.length
    
    (@applied_migrations.length + 1).upto(target_version) do |version|
      migration = @migrations.find { |m| m[:id] == version }
      next unless migration
      
      puts "Applying migration #{version}: #{migration[:name]}"
      puts "SQL: #{migration[:up_sql]}"
      
      @applied_migrations << version
    end
    
    puts "Migrated up to version #{target_version}"
  end
  
  def migrate_down(target_version)
    return "No migrations applied" if @applied_migrations.empty?
    
    target_version = [target_version, 0].max
    
    @applied_migrations.reverse.each do |version|
      break if version <= target_version
      
      migration = @migrations.find { |m| m[:id] == version }
      next unless migration
      
      puts "Rolling back migration #{version}: #{migration[:name]}"
      puts "SQL: #{migration[:down_sql]}"
      
      @applied_migrations.delete(version)
    end
    
    puts "Migrated down to version #{target_version}"
  end
  
  def current_version
    @applied_migrations.empty? ? 0 : @applied_migrations.max
  end
  
  def pending_migrations
    @migrations.reject { |m| @applied_migrations.include?(m[:id]) }
  end
  
  def migration_status
    {
      current: current_version,
      total: @migrations.length,
      applied: @applied_migrations.length,
      pending: pending_migrations.length
    }
  end
end

puts "Migration Example:"

migration_manager = MigrationManager.new

# Create migrations
migration_manager.create_migration(
  "CreateUsersTable",
  "CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT)",
  "DROP TABLE users"
)

migration_manager.create_migration(
  "CreatePostsTable", 
  "CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT, user_id INTEGER)",
  "DROP TABLE posts"
)

migration_manager.create_migration(
  "AddIndexToUsersEmail",
  "CREATE INDEX idx_users_email ON users(email)",
  "DROP INDEX idx_users_email"
)

# Migrate up
migration_manager.migrate_up

# Show status
puts "Migration status: #{migration_manager.migration_status}"

# Migrate down
migration_manager.migrate_down(1)

puts "Final migration status: #{migration_manager.migration_status}"

puts "\n=== DATABASE OPTIMIZATION ==="

class QueryOptimizer
  def initialize
    @query_stats = {}
    @indexes = {}
  end
  
  def analyze_query(query, execution_time)
    query_type = extract_query_type(query)
    
    @query_stats[query_type] ||= {
      count: 0,
      total_time: 0,
      avg_time: 0,
      queries: []
    }
    
    stats = @query_stats[query_type]
    stats[:count] += 1
    stats[:total_time] += execution_time
    stats[:avg_time] = stats[:total_time] / stats[:count]
    stats[:queries] << { query: query, time: execution_time }
    
    # Keep only last 10 queries for memory
    stats[:queries] = stats[:queries].last(10)
  end
  
  def suggest_indexes
    suggestions = []
    
    @query_stats.each do |query_type, stats|
      if stats[:count] > 10 && stats[:avg_time] > 0.1
        suggestions << "Consider adding index for #{query_type} queries (avg: #{stats[:avg_time].round(3)}s)"
      end
    end
    
    suggestions
  end
  
  def slow_queries(threshold = 0.5)
    slow_queries = []
    
    @query_stats.each do |query_type, stats|
      stats[:queries].each do |query_info|
        if query_info[:time] > threshold
          slow_queries << {
            query: query_info[:query],
            time: query_info[:time],
            type: query_type
          }
        end
      end
    end
    
    slow_queries.sort_by { |q| -q[:time] }
  end
  
  def performance_report
    {
      total_queries: @query_stats.values.sum { |s| s[:count] },
      query_types: @query_stats.keys,
      avg_execution_time: @query_stats.values.map { |s| s[:avg_time] }.sum / @query_stats.length,
      slow_queries_count: slow_queries(0.1).length,
      index_suggestions: suggest_indexes.length
    }
  end
  
  private
  
  def extract_query_type(query)
    case query.upcase.strip
    when /^SELECT/
      :select
    when /^INSERT/
      :insert
    when /^UPDATE/
      :update
    when /^DELETE/
      :delete
    else
      :other
    end
  end
end

puts "Query Optimization Example:"

optimizer = QueryOptimizer.new

# Simulate query execution
queries = [
  ["SELECT * FROM users WHERE email = 'test@example.com'", 0.05],
  ["SELECT * FROM posts WHERE user_id = 1 ORDER BY created_at", 0.15],
  ["INSERT INTO users (name, email) VALUES ('John', 'john@example.com')", 0.02],
  ["UPDATE posts SET title = 'Updated' WHERE id = 1", 0.03],
  ["SELECT * FROM users WHERE name LIKE '%John%'", 0.25],
  ["SELECT * FROM posts WHERE user_id = 1 AND published = true", 0.08],
  ["DELETE FROM comments WHERE post_id = 1", 0.04]
]

# Analyze queries
queries.each do |query, time|
  optimizer.analyze_query(query, time)
end

# Get performance report
report = optimizer.performance_report
puts "Performance Report:"
puts "  Total queries: #{report[:total_queries]}"
puts "  Query types: #{report[:query_types].join(', ')}"
puts "  Avg execution time: #{report[:avg_execution_time].round(3)}s"
puts "  Slow queries: #{report[:slow_queries_count]}"
puts "  Index suggestions: #{report[:index_suggestions]}"

# Show slow queries
slow_queries = optimizer.slow_queries(0.1)
puts "\nSlow Queries (>0.1s):"
slow_queries.each do |query|
  puts "  #{query[:query]} (#{query[:time].round(3)}s)"
end

# Show index suggestions
suggestions = optimizer.suggest_indexes
puts "\nIndex Suggestions:"
suggestions.each { |suggestion| puts "  #{suggestion}" }

puts "\n=== DATABASE PROGRAMMING SUMMARY ==="
puts "- ActiveRecord-like ORM: Models, associations, queries"
puts "- SQL Operations: CRUD, joins, conditions"
puts "- Connection Pooling: Resource management, scaling"
puts "- Transactions: ACID properties, rollback/commit"
puts "- Migrations: Schema management, versioning"
puts "- Query Optimization: Performance analysis, indexing"
puts "\nAll examples demonstrate comprehensive database programming concepts!"
