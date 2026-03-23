# Query Optimization in Ruby
# Comprehensive guide to database query optimization techniques

## 🚀 Query Optimization Fundamentals

### 1. Optimization Concepts

Core query optimization principles:

```ruby
class QueryOptimizationFundamentals
  def self.explain_optimization_concepts
    puts "Query Optimization Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Query Performance",
        description: "Efficiency and speed of database queries",
        metrics: ["Execution time", "Resource usage", "Throughput", "Latency"],
        goals: ["Minimize response time", "Maximize throughput", "Reduce resource consumption"],
        importance: "Critical for application performance"
      },
      {
        concept: "Query Optimization",
        description: "Process of improving query performance",
        techniques: ["Indexing", "Query rewriting", "Caching", "Database tuning"],
        benefits: ["Faster execution", "Lower resource usage", "Better scalability"],
        challenges: ["Complex queries", "Data volume", "Query complexity"]
      },
      {
        concept: "Indexing Strategy",
        description: "Creating effective database indexes",
        types: ["Primary key", "Unique index", "Composite index", "Partial index"],
        considerations: ["Query patterns", "Data distribution", "Write performance"],
        tradeoffs: ["Read vs write performance", "Storage overhead", "Maintenance cost"]
      },
      {
        concept: "Query Analysis",
        description: "Analyzing query execution plans",
        tools: ["EXPLAIN", "Query profiler", "Performance monitoring"],
        metrics: ["Execution plan", "Index usage", "Row scans", "Sort operations"],
        optimization: "Identify bottlenecks and improvement opportunities"
      },
      {
        concept: "N+1 Query Problem",
        description: "Performance issue with nested queries",
        problem: "Multiple queries for each parent record",
        solutions: ["Eager loading", "Batch loading", "Preloading", "Join optimization"],
        impact: "Significant performance degradation"
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Metrics: #{concept[:metrics].join(', ')}" if concept[:metrics]
      puts "  Goals: #{concept[:goals].join(', ')}" if concept[:goals]
      puts "  Importance: #{concept[:importance]}" if concept[:importance]
      puts "  Techniques: #{concept[:techniques].join(', ')}" if concept[:techniques]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Considerations: #{concept[:considerations].join(', ')}" if concept[:considerations]
      puts "  Tradeoffs: #{concept[:tradeoffs].join(', ')}" if concept[:tradeoffs]
      puts "  Tools: #{concept[:tools].join(', ')}" if concept[:tools]
      puts "  Metrics: #{concept[:metrics].join(', ')}" if concept[:metrics]
      puts "  Optimization: #{concept[:optimization]}" if concept[:optimization]
      puts "  Problem: #{concept[:problem]}" if concept[:problem]
      puts "  Solutions: #{concept[:solutions].join(', ')}" if concept[:solutions]
      puts "  Impact: #{concept[:impact]}" if concept[:impact]
      puts
    end
  end
  
  def self.optimization_principles
    puts "\nQuery Optimization Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Measure First",
        description: "Profile queries before optimizing",
        steps: ["Identify slow queries", "Measure performance", "Set benchmarks"],
        tools: ["Query profiler", "Execution plans", "Performance metrics"]
      },
      {
        principle: "Index Appropriately",
        description: "Create indexes based on query patterns",
        guidelines: ["Index frequently queried columns", "Avoid over-indexing", "Use composite indexes"],
        considerations: ["Read/write ratio", "Selectivity", "Cardinality"]
      },
      {
        principle: "Avoid N+1 Queries",
        description: "Prevent multiple queries for related data",
        solutions: ["Eager loading", "Batch loading", "Join optimization"],
        techniques: ["Includes", "Joins", "Preloading"]
      },
      {
        principle: "Use Proper Data Types",
        description: "Choose appropriate data types for columns",
        benefits: ["Storage efficiency", "Performance", "Data integrity"],
        examples: ["INT vs BIGINT", "VARCHAR vs TEXT", "DATE vs DATETIME"]
      },
      {
        principle: "Limit Result Sets",
        description: "Only fetch necessary data",
        techniques: ["Pagination", "Column selection", "Filtering"],
        benefits: ["Reduced memory usage", "Faster queries", "Lower network traffic"]
      },
      {
        principle: "Cache Results",
        description: "Cache frequently accessed data",
        strategies: ["Query caching", "Object caching", "Fragment caching"],
        benefits: ["Reduced database load", "Faster response times"]
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Steps: #{principle[:steps].join(', ')}" if principle[:steps]
      puts "  Tools: #{principle[:tools].join(', ')}" if principle[:tools]
      puts "  Guidelines: #{principle[:guidelines].join(', ')}" if principle[:guidelines]
      puts "  Considerations: #{principle[:considerations].join(', ')}" if principle[:considerations]
      puts "  Solutions: #{principle[:solutions].join(', ')}" if principle[:solutions]
      puts "  Techniques: #{principle[:techniques].join(', ')}" if principle[:techniques]
      puts "  Benefits: #{principle[:benefits].join(', ')}" if principle[:benefits]
      puts "  Examples: #{principle[:examples].join(', ')}" if principle[:examples]
      puts "  Strategies: #{principle[:strategies].join(', ')}" if principle[:strategies]
      puts
    end
  end
  
  def self.performance_bottlenecks
    puts "\nCommon Performance Bottlenecks:"
    puts "=" * 50
    
    bottlenecks = [
      {
        bottleneck: "Missing Indexes",
        description: "Queries not using available indexes",
        symptoms: ["Full table scans", "Slow queries", "High CPU usage"],
        detection: "EXPLAIN plans, query profiler",
        solution: "Create appropriate indexes"
      },
      {
        bottleneck: "N+1 Queries",
        description: "Multiple queries for related data",
        symptoms: ["Linear performance degradation", "High query count", "Slow page loads"],
        detection: "Query analysis, logging",
        solution: "Eager loading, batch loading"
      },
      {
        bottleneck: "Large Result Sets",
        description: "Queries returning too much data",
        symptoms: ["High memory usage", "Slow network transfer", "Long response times"],
        detection: "Query logging, monitoring",
        solution: "Pagination, column selection"
      },
      {
        bottleneck: "Complex Joins",
        description: "Inefficient join operations",
        symptoms: ["Slow execution", "High CPU usage", "Memory pressure"],
        detection: "EXPLAIN plans, query analysis",
        solution: "Optimize joins, add indexes"
      },
      {
        bottleneck: "Inefficient Subqueries",
        description: "Poorly optimized subqueries",
        symptoms: ["Slow execution", "High resource usage"],
        detection: "Query analysis, execution plans",
        solution: "Rewrite as joins, optimize subqueries"
      },
      {
        bottleneck: "Missing Constraints",
        description: "Lack of proper database constraints",
        symptoms: ["Data inconsistency", "Slow queries", "Lock contention"],
        detection: "Schema analysis, query analysis",
        solution: "Add appropriate constraints"
      }
    ]
    
    bottlenecks.each do |bottleneck|
      puts "#{bottleneck[:bottleneck]}:"
      puts "  Description: #{bottleneck[:description]}"
      puts "  Symptoms: #{bottleneck[:symptoms].join(', ')}"
      puts "  Detection: #{bottleneck[:detection]}"
      puts "  Solution: #{bottleneck[:solution]}"
      puts
    end
  end
  
  # Run optimization fundamentals
  explain_optimization_concepts
  optimization_principles
  performance_bottlenecks
end
```

### 2. Query Profiler

Query performance analysis tool:

```ruby
class QueryProfiler
  def initialize
    @queries = []
    @slow_queries = []
    @threshold = 100 # milliseconds
    @enabled = false
  end
  
  def enable
    @enabled = true
    puts "Query profiler enabled (threshold: #{@threshold}ms)"
  end
  
  def disable
    @enabled = false
    puts "Query profiler disabled"
  end
  
  def profile_query(sql, params = [], &block)
    return yield unless @enabled
    
    start_time = Time.now
    result = nil
    exception = nil
    
    begin
      result = yield
    rescue => e
      exception = e
      raise e
    ensure
      end_time = Time.now - start_time
      
      query_info = {
        sql: sql,
        params: params,
        execution_time: end_time * 1000, # Convert to milliseconds
        timestamp: Time.now,
        result: result,
        exception: exception,
        stack_trace: caller_locations
      }
      
      @queries << query_info
      
      if end_time * 1000 > @threshold
        @slow_queries << query_info
        puts "Slow query detected: #{(end_time * 1000).round(2)}ms"
        puts "  SQL: #{sql}"
        puts "  Params: #{params}" if params.any?
      end
      
      raise exception if exception
    end
    
    result
  end
  
  def analyze_queries
    puts "Query Analysis Report:"
    puts "=" * 50
    
    return puts "No queries profiled" if @queries.empty?
    
    # Overall statistics
    total_queries = @queries.length
    total_time = @queries.sum { |q| q[:execution_time] }
    avg_time = total_time / total_queries
    slow_queries = @slow_queries.length
    slow_percentage = (slow_queries.to_f / total_queries * 100).round(2)
    
    puts "Total Queries: #{total_queries}"
    puts "Total Time: #{total_time.round(2)}ms"
    puts "Average Time: #{avg_time.round(2)}ms"
    puts "Slow Queries: #{slow_queries} (#{slow_percentage}%)"
    puts "Threshold: #{@threshold}ms"
    
    # Slow queries analysis
    puts "\nSlow Queries Analysis:"
    @slow_queries.each_with_index do |query, i|
      puts "#{i + 1}. #{(query[:execution_time]).round(2)}ms - #{query[:sql][0...50]}..."
      puts "   Params: #{query[:params]}" if query[:params].any?
      puts "   Timestamp: #{query[:timestamp]}"
      puts "   Stack trace: #{query[:stack_trace].first}"
    end
    
    # Query patterns
    puts "\nQuery Patterns:"
    patterns = analyze_query_patterns
    patterns.each do |pattern, count|
      puts "#{pattern}: #{count} queries"
    end
    
    # Recommendations
    puts "\nOptimization Recommendations:"
    recommendations = generate_recommendations
    recommendations.each { |rec| puts "- #{rec}" }
    
    {
      total_queries: total_queries,
      total_time: total_time,
      avg_time: avg_time,
      slow_queries: slow_queries,
      slow_percentage: slow_percentage,
      patterns: patterns,
      recommendations: recommendations
    }
  end
  
  def slow_queries
    @slow_queries
  end
  
  def all_queries
    @queries
  end
  
  def reset
    @queries.clear
    @slow_queries.clear
    puts "Query profiler reset"
  end
  
  def set_threshold(threshold)
    @threshold = threshold
    puts "Threshold set to #{threshold}ms"
  end
  
  def self.demonstrate_profiler
    puts "Query Profiler Demonstration:"
    puts "=" * 50
    
    profiler = QueryProfiler.new
    profiler.enable
    
    # Simulate different query types
    queries = [
      {
        name: "Fast Query",
        sql: "SELECT * FROM users WHERE id = ?",
        params: [1],
        delay: 0.01
      },
      {
        name: "Medium Query",
        sql: "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC",
        params: [1],
        delay: 0.05
      },
      {
        name: "Slow Query",
        sql: "SELECT * FROM comments WHERE post_id IN (SELECT id FROM posts WHERE user_id = ?)",
        params: [1],
        delay: 0.15
      },
      {
        name: "Very Slow Query",
        sql: "SELECT * FROM orders o JOIN users u ON o.user_id = u.id JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id WHERE u.created_at > ?",
        params: [Time.now - 30.days],
        delay: 0.25
      }
    ]
    
    puts "Executing queries with profiling:"
    
    queries.each do |query|
      puts "\n#{query[:name]}:"
      begin
        profiler.profile_query(query[:sql], query[:params]) do
          sleep(query[:delay]) # Simulate query execution time
          "Query result"
        end
      rescue => e
        puts "  Error: #{e.message}"
      end
    end
    
    # Analyze results
    puts "\nAnalyzing results:"
    analysis = profiler.analyze_queries
    
    puts "\nProfiler Features:"
    puts "- Query execution time tracking"
    puts "- Slow query detection"
    "- Query pattern analysis"
    "- Optimization recommendations"
    puts "- Stack trace capture"
    "- Parameter logging"
    "- Exception handling"
  end
  
  private
  
  def analyze_query_patterns
    patterns = Hash.new(0)
    
    @queries.each do |query|
      # Extract query pattern (simplified)
      pattern = extract_pattern(query[:sql])
      patterns[pattern] += 1
    end
    
    patterns
  end
  
  def extract_pattern(sql)
    # Simplified pattern extraction
    case sql
    when /SELECT \* FROM users/
      'users_select'
    when /SELECT \* FROM posts/
      'posts_select'
    when /SELECT \* FROM comments/
      'comments_select'
    when /JOIN/
      'join_query'
    when /IN \(SELECT/
      'subquery'
    else
      'other'
    end
  end
  
  def generate_recommendations
    recommendations = []
    
    slow_count = @slow_queries.length
    total_count = @queries.length
    
    if slow_count > 0
      recommendations << "Found #{slow_count} slow queries out of #{total_count} total"
    end
    
    if slow_count > total_count * 0.2
      recommendations << "High percentage of slow queries - investigate database performance"
    end
    
    patterns = analyze_query_patterns
    if patterns['subquery'] > 0
      recommendations << "Consider optimizing subqueries or using joins"
    end
    
    if patterns['join_query'] > 0
      recommendations << "Review join performance and indexing"
    end
    
    avg_time = @queries.sum { |q| q[:execution_time] } / @queries.length
    if avg_time > 100
      recommendations << "High average query time (#{avg_time.round(2)}ms) - consider optimization"
    end
    
    recommendations << "Consider adding indexes for frequently queried columns"
    recommendations << "Use query caching for frequently executed queries"
    recommendations << "Review N+1 query patterns and implement eager loading"
    
    recommendations
  end
end
```

## 📊 Index Optimization

### 3. Index Management

Database index optimization:

```ruby
class IndexOptimizer
  def initialize(connection)
    @connection = connection
    @indexes = {}
    @recommendations = []
  end
  
  def analyze_table_indexes(table_name)
    puts "Analyzing indexes for table: #{table_name}"
    
    # Get table schema
    schema = get_table_schema(table_name)
    
    # Get existing indexes
    existing_indexes = get_table_indexes(table_name)
    
    # Analyze query patterns
    query_patterns = analyze_query_patterns(table_name)
    
    # Generate recommendations
    recommendations = generate_index_recommendations(schema, existing_indexes, query_patterns)
    
    {
      table: table_name,
      schema: schema,
      existing_indexes: existing_indexes,
      query_patterns: query_patterns,
      recommendations: recommendations
    }
  end
  
  def suggest_indexes(table_name, query_patterns)
    puts "Suggesting indexes for #{table_name}:"
    
    schema = get_table_schema(table_name)
    existing_indexes = get_table_indexes(table_name)
    
    suggestions = []
    
    # Analyze WHERE clauses
    where_columns = query_patterns[:where_columns] || []
    where_columns.each do |column|
      next if column_exists_in_indexes?(column, existing_indexes)
      next unless schema[:columns].any? { |col| col[:name] == column }
      
      suggestions << {
        type: :single_column,
        columns: [column],
        reason: "Frequently used in WHERE clause"
      }
    end
    
    # Analyze ORDER BY clauses
    order_columns = query_patterns[:order_columns] || []
    order_columns.each do |column|
      next if column_exists_in_indexes?(column, existing_indexes)
      next unless schema[:columns].any? { |col| col[:name] == column }
      
      suggestions << {
        type: :single_column,
        columns: [column],
        reason: "Frequently used in ORDER BY clause"
      }
    end
    
    # Analyze JOIN columns
    join_columns = query_patterns[:join_columns] || []
    join_columns.each do |column|
      next if column_exists_in_indexes?(column, existing_indexes)
      next unless schema[:columns].any? { |col| col[:name] == column }
      
      suggestions << {
        type: :single_column,
        columns: [column],
        reason: "Frequently used in JOIN conditions"
      }
    end
    
    # Analyze multi-column patterns
    multi_column_patterns = query_patterns[:multi_column_patterns] || []
    multi_column_patterns.each do |columns|
      next if all_columns_indexed?(columns, existing_indexes)
      
      suggestions << {
        type: :composite,
        columns: columns,
        reason: "Frequently used together in queries"
      }
    end
    
    puts "Found #{suggestions.length} index suggestions:"
    suggestions.each_with_index do |suggestion, i|
      puts "  #{i + 1}. #{suggestion[:type].to_s.humanize}: #{suggestion[:columns].join(', ')}"
      puts "     Reason: #{suggestion[:reason]}"
    end
    
    suggestions
  end
  
  def create_index(table_name, columns, options = {})
    index_name = generate_index_name(table_name, columns)
    
    sql = "CREATE INDEX #{index_name} ON #{table_name} (#{columns.join(', ')})"
    sql += " WHERE #{options[:where]}" if options[:where]
    sql += " USING #{options[:using]}" if options[:using]
    
    puts "Creating index: #{sql}"
    
    begin
      @connection.execute_update(sql)
      puts "Index created successfully"
      index_name
    rescue => e
      puts "Failed to create index: #{e.message}"
      nil
    end
  end
  
  def drop_index(index_name)
    sql = "DROP INDEX IF EXISTS #{index_name}"
    
    puts "Dropping index: #{sql}"
    
    begin
      @connection.execute_update(sql)
      puts "Index dropped successfully"
      true
    rescue => e
      puts "Failed to drop index: #{e.message}"
      false
    end
  end
  
  def analyze_index_usage
    puts "Analyzing index usage..."
    
    # Simulate index usage analysis
    usage_stats = {
      'idx_users_email' => { usage_count: 1000, last_used: Time.now, efficiency: 95 },
      'idx_posts_user_id' => { usage_count: 500, last_used: Time.now - 3600, efficiency: 85 },
      'idx_comments_post_id' => { usage_count: 200, last_used: Time.now - 86400, efficiency: 60 },
      'idx_orders_user_id_created_at' => { usage_count: 50, last_used: Time.now - 604800, efficiency: 30 }
    }
    
    puts "Index Usage Statistics:"
    usage_stats.each do |index, stats|
      puts "  #{index}:"
      puts "    Usage count: #{stats[:usage_count]}"
      puts "    Last used: #{stats[:last_used]}"
      puts "    Efficiency: #{stats[:efficiency]}%"
      
      if stats[:efficiency] < 50
        puts "    ⚠️  Low efficiency - consider dropping"
      elsif stats[:last_used] < Time.now - 7 * 24 * 3600
        puts "    ⚠️  Not used recently - consider dropping"
      end
    end
    
    usage_stats
  end
  
  def self.demonstrate_index_optimization
    puts "Index Optimization Demonstration:"
    puts "=" * 50
    
    # Create mock connection
    connection = MockConnection.new
    optimizer = IndexOptimizer.new(connection)
    
    # Analyze a table
    puts "\nAnalyzing indexes for 'posts' table:"
    analysis = optimizer.analyze_table_indexes('posts')
    
    puts "Table Schema:"
    analysis[:schema][:columns].each_with_index do |column, i|
      puts "  #{i + 1}. #{column[:name]} (#{column[:type]})"
    end
    
    puts "\nExisting Indexes:"
    analysis[:existing_indexes].each_with_index do |index, i|
      puts "  #{i + 1}. #{index[:name]} on #{index[:columns].join(', ')}"
      puts "     Unique: #{index[:unique]}" if index[:unique]
    end
    
    puts "\nQuery Patterns:"
    analysis[:query_patterns].each do |type, patterns|
      puts "  #{type}: #{patterns.join(', ')}"
    end
    
    puts "\nRecommendations:"
    analysis[:recommendations].each_with_index do |rec, i|
      puts "  #{i + 1}. #{rec}"
    end
    
    # Suggest new indexes
    puts "\nSuggesting new indexes:"
    suggestions = optimizer.suggest_indexes('posts', {
      where_columns: ['user_id', 'created_at'],
      order_columns: ['created_at', 'updated_at'],
      join_columns: ['user_id'],
      multi_column_patterns: [['user_id', 'created_at'], ['user_id', 'status', 'created_at']]
    })
    
    # Create some indexes
    puts "\nCreating indexes:"
    created_indexes = []
    
    suggestions.first(2).each do |suggestion|
      index_name = optimizer.create_index('posts', suggestion[:columns])
      created_indexes << index_name if index_name
    end
    
    puts "Created indexes: #{created_indexes.join(', ')}"
    
    # Analyze index usage
    puts "\nAnalyzing index usage:"
    usage_stats = optimizer.analyze_index_usage
    
    # Drop unused indexes
    puts "\nDropping unused indexes:"
    optimizer.drop_index('idx_comments_post_id')
    
    puts "\nIndex Optimization Features:"
    puts "- Index usage analysis"
    puts "- Query pattern detection"
    "- Index recommendation engine"
    "- Automatic index creation"
    "- Index usage monitoring"
    "- Unused index detection"
    "- Performance optimization"
  end
  
  private
  
  def get_table_schema(table_name)
    # Simulate table schema
    {
      columns: [
        { name: 'id', type: 'integer', null: false, default: nil },
        { name: 'user_id', type: 'integer', null: false, default: nil },
        { name: 'title', type: 'varchar', null: false, default: nil },
        name: 'content', type: 'text', null: true, default: nil },
        { name: 'status', type: 'varchar', null: false, default: 'draft' },
        { name: 'created_at', type: 'timestamp', null: false, default: 'CURRENT_TIMESTAMP' },
        { name: 'updated_at', type: 'timestamp', null: false, default: 'CURRENT_TIMESTAMP' }
      ]
    }
  end
  
  def get_table_indexes(table_name)
    # Simulate existing indexes
    [
      {
        name: 'idx_posts_user_id',
        columns: ['user_id'],
        unique: false
      },
      {
        name: 'idx_posts_status',
        columns: ['status'],
        unique: false
      }
    ]
  end
  
  def analyze_query_patterns(table_name)
    # Simulate query pattern analysis
    {
      where_columns: ['user_id', 'status', 'created_at'],
      order_columns: ['created_at', 'updated_at', 'id'],
      join_columns: ['user_id'],
      multi_column_patterns: [['user_id', 'created_at'], ['user_id', 'status']]
    }
  end
  
  def generate_index_name(table_name, columns)
    columns_str = columns.join('_')
    "idx_#{table_name}_#{columns_str}"
  end
  
  def column_exists_in_indexes?(column, indexes)
    indexes.any? { |index| index[:columns].include?(column) }
  end
  
  def all_columns_indexed?(columns, indexes)
    columns.all? { |column| column_exists_in_indexes?(column, indexes) }
  end
  
  def generate_index_recommendations(schema, existing_indexes, query_patterns)
    recommendations = []
    
    # Check for missing indexes on frequently queried columns
    (query_patterns[:where_columns] || []).each do |column|
      unless column_exists_in_indexes?(column, existing_indexes)
        recommendations << "Consider adding index on #{column} for WHERE clause optimization"
      end
    end
    
    # Check for missing indexes on ORDER BY columns
    (query_patterns[:order_columns] || []).each do |column|
      unless column_exists_in_indexes?(column, existing_indexes)
        recommendations << "Consider adding index on #{column} for ORDER BY optimization"
      end
    end
    
    # Check for missing indexes on JOIN columns
    (query_patterns[:join_columns] || []).each do |column|
      unless column_exists_in_indexes?(column, existing_indexes)
        recommendations << "Consider adding index on #{column} for JOIN optimization"
      end
    end
    
    # Check for composite indexes
    (query_patterns[:multi_column_patterns] || []).each do |columns|
      unless all_columns_indexed?(columns, existing_indexes)
        recommendations << "Consider adding composite index on #{columns.join(', ')} for multi-column queries"
      end
    end
    
    # Check for unused indexes
    existing_indexes.each do |index|
      if index[:columns].all? { |col| !query_patterns[:where_columns].include?(col) && !query_patterns[:order_columns].include?(col) }
        recommendations << "Consider dropping index #{index[:name]} (#{index[:columns].join(', ')}) - may be unused"
      end
    end
    
    recommendations
  end
end

class MockConnection
  def execute_update(sql)
    puts "  [Database] Executing: #{sql}"
    true
  end
end
```

## 🔄 Query Builder Optimization

### 4. Optimized Query Builder

Efficient query construction:

```ruby
class OptimizedQueryBuilder
  def initialize(model_class)
    @model_class = model_class
    @where_conditions = []
    @where_params = []
    @order_clauses = []
    @limit_clause = nil
    @offset_clause = nil
    @select_fields = nil
    @joins = []
    @group_clause = nil
    @having_clause = nil
    @includes = []
    @eager_load = false
    @cache_enabled = false
    @cache_key = nil
  end
  
  def where(conditions, *params)
    case conditions
    when String
      @where_conditions << conditions
      @where_params.concat(params) if params.any?
    when Hash
      conditions.each do |key, value|
        @where_conditions << "#{key} = ?"
        @where_params << value
      end
    when Array
      @where_conditions << conditions[0]
      @where_params.concat(conditions[1..-1])
    end
    
    self
  end
  
  def order(field, direction = 'ASC')
    @order_clauses << "#{field} #{direction}"
    self
  end
  
  def limit(count)
    @limit_clause = count
    self
  end
  
  def offset(count)
    @offset_clause = count
    self
  end
  
  def select(*fields)
    @select_fields = fields if fields.any?
    self
  end
  
  def joins(associations)
    @joins.concat(Array(associations))
    self
  end
  
  def includes(associations)
    @includes.concat(Array(associations))
    @eager_load = true
    self
  end
  
  def group(field)
    @group_clause = field
    self
  end
  
  def having(conditions)
    @having_clause = conditions
    self
  end
  
  def cache(key = nil)
    @cache_enabled = true
    @cache_key = key
    self
  end
  
  def count
    if @cache_enabled && cached_result = get_cached_result('count')
      return cached_result
    end
    
    sql = build_count_sql
    result = execute_query(sql, @where_params)
    
    set_cached_result('count', result) if @cache_enabled
    result
  end
  
  def first
    limit(1).to_a.first
  end
  
  def pluck(field)
    select(field).to_a.map { |record| record[field] }
  end
  
  def exists?
    limit(1).any?
  end
  
  def to_a
    if @cache_enabled && cached_result = get_cached_result('to_a')
      return cached_result
    end
    
    sql = build_sql
    results = execute_query(sql, @where_params)
    
    # Handle eager loading
    results = eager_load_associations(results) if @eager_load
    
    set_cached_result('to_a', results) if @cache_enabled
    results
  end
  
  def find_each(batch_size: 1000, &block)
    if @cache_enabled
      # For cached queries, load all at once
      to_a.each(&block)
    else
      offset = 0
      
      loop do
        batch = limit(batch_size).offset(offset).to_a
        break if batch.empty?
        
        batch.each { |record| yield record }
        offset += batch_size
      end
    end
  end
  
  def find_in_batches(batch_size: 1000, &block)
    if @cache_enabled
      # For cached queries, load all at once
      all_records = to_a
      all_records.each_slice(batch_size) { |batch| yield batch }
    else
      offset = 0
      
      loop do
        batch = limit(batch_size).offset(offset).to_a
        break if batch.empty?
        
        yield batch
        offset += batch_size
      end
    end
  end
  
  def explain
    sql = build_sql
    puts "Query Plan:"
    puts "SQL: #{sql}"
    puts "Parameters: #{@where_params}" if @where_params.any?
    
    # Simulate EXPLAIN output
    puts "Execution Plan:"
    puts "  -> Index Scan using idx_users_email (cost: 1.00)"
    puts "  -> Filter (cost: 0.50)"
    puts "  -> Sort (cost: 2.00)"
    puts "  -> Limit (cost: 0.10)"
    puts "  -> Total Cost: 3.60"
    
    self
  end
  
  def self.demonstrate_optimized_builder
    puts "Optimized Query Builder Demonstration:"
    puts "=" * 50
    
    # Extend model class
    class User
      def self.where(conditions = {})
        OptimizedQueryBuilder.new(self).where(conditions)
      end
      
      def self.order(field, direction = 'ASC')
        OptimizedQueryBuilder.new(self).order(field, direction)
      end
      
      def self.limit(count)
        OptimizedQueryBuilder.new(self).limit(count)
      end
      
      def self.select(*fields)
        OptimizedQueryBuilder.new(self).select(*fields)
      end
      
      def self.joins(associations)
        OptimizedQueryBuilder.new(self).joins(associations)
      end
      
      def self.includes(associations)
        OptimizedQueryBuilder.new(self).includes(associations)
      end
      
      def self.cache(key = nil)
        OptimizedQueryBuilder.new(self).cache(key)
      end
    end
    
    # Create sample data
    users = [
      { id: 1, name: 'Alice', email: 'alice@example.com', age: 25, created_at: Time.now },
      { id: 2, name: 'Bob', email: 'bob@example.com', age: 30, created_at: Time.now - 86400 },
      { id: 3, name: 'Charlie', email: 'charlie@example.com', age: 35, created_at: Time.now - 172800 }
    ]
    
    puts "Created #{users.length} sample users"
    
    # Demonstrate different query optimizations
    puts "\nQuery Optimizations:"
    
    # Basic query
    puts "\nBasic Query:"
    basic_query = User.where(age: 25..35)
    puts "  Query: #{basic_query.to_sql}"
    puts "  Results: #{basic_query.count} users"
    
    # Optimized query with caching
    puts "\nCached Query:"
    cached_query = User.where(age: 25..35).cache('age_range')
    puts "  Query: #{cached_query.to_sql}"
    puts "  Results: #{cached_query.count} users"
    
    # Eager loading
    puts "\nEager Loading:"
    eager_query = User.joins(:posts).includes(:comments)
    puts "  Query: #{eager_query.to_sql}"
    puts "  Results: #{eager_query.count} users"
    
    # Optimized select
    puts "\nOptimized Select:"
    select_query = User.select(:id, :name, :email)
    puts "  Query: #{select_query.to_sql}"
    puts "  Results: #{select_query.pluck(:name).join(', ')}"
    
    # Complex query with optimization
    puts "\nComplex Query with Optimization:"
    complex_query = User
      .where(age: 25..35)
      .order(:created_at, :desc)
      .limit(2)
      .select(:id, :name, :email)
      .cache('users_25_35')
    
    puts "  Query: #{complex_query.to_sql}"
    puts "  Results: #{complex_query.count} users"
    
    # Query explanation
    puts "\nQuery Explanation:"
    complex_query.explain
    
    puts "\nOptimized Builder Features:"
    puts "- Cached query results"
    puts "- Eager loading support"
    "- Optimized SELECT statements"
    "- Query plan analysis"
    "- Batch processing methods"
    "- Connection reuse"
    "- Parameter binding"
  end
  
  def to_sql
    build_sql
  end
  
  private
  
  def build_sql
    sql = "SELECT "
    sql += @select_fields || "*"
    sql += " FROM #{@model_class.table_name}"
    
    # Add joins
    @joins.each do |join|
      sql += " LEFT JOIN #{join} ON #{@model_class.table_name}.#{join}_id = #{join}.id"
    end
    
    # Add where conditions
    if @where_conditions.any?
      sql += " WHERE #{@where_conditions.join(' AND ')}"
    end
    
    # Add group by
    sql += " GROUP BY #{@group_clause}" if @group_clause
    
    # Add having
    sql += " HAVING #{@having_clause}" if @having_clause
    
    # Add order
    sql += " ORDER BY #{@order_clauses.join(', ')}" if @order_clauses.any?
    
    # Add limit and offset
    sql += " LIMIT #{@limit_clause}" if @limit_clause
    sql += " OFFSET #{@offset_clause}" if @offset_clause
    
    sql
  end
  
  def build_count_sql
    sql = "SELECT COUNT(*) as count FROM #{@model_class.table_name}"
    
    # Add joins
    @joins.each do |join|
      sql += " LEFT JOIN #{join} ON #{@model_class.table_name}.#{join}_id = #{join}.id"
    end
    
    # Add where conditions
    if @where_conditions.any?
      sql += " WHERE #{@where_conditions.join(' AND ')}"
    end
    
    sql
  end
  
  def execute_query(sql, params)
    # Simulate query execution
    puts "  [Database] Executing: #{sql}"
    puts "  [Database] Parameters: #{params}" if params.any?
    
    # Simulate different query types
    case sql
    when /WHERE age BETWEEN \? AND \?/
      [{ 'id' => 1, 'name' => 'Alice', 'age' => 25 }]
    when /ORDER BY created_at DESC/
      users.sort_by { |u| u[:created_at] }.reverse
    when /LIMIT \d+/
      users.first(3)
    when /SELECT id, name, email/
      users.map { |u| { 'id' => u[:id], 'name' => u[:name], 'email' => u[:email] } }
    when /SELECT COUNT\(\*\)/
      [{ 'count' => users.length }]
    else
      users
    end
  end
  
  def eager_load_associations(results)
    # Simulate eager loading
    results.map do |record|
      if @includes.include?(:posts)
        record[:posts] = [{ 'id' => 1, 'title' => 'Post 1', 'user_id' => record[:id] }]
      end
      
      if @includes.include?(:comments)
        record[:comments] = [{ 'id' => 1, 'content' => 'Comment 1', 'post_id' => 1, 'user_id' => record[:id] }]
      end
      
      record
    end
  end
  
  def get_cached_result(key)
    # Simulate cache lookup
    cache[key]
  end
  
  def set_cached_result(key, result)
    # Simulate cache storage
    cache[key] = result
  end
  
  def cache
    @cache ||= {}
  end
end
```

## 🎯 Query Caching

### 5. Query Result Caching

Intelligent caching system:

```ruby
class QueryCache
  def initialize(options = {})
    @cache = {}
    @cache_size = options[:size] || 1000
    @ttl = options[:ttl] || 300 # 5 minutes
    @stats = {
      hits: 0,
      misses: 0,
      evictions: 0,
      size: 0
    }
    @mutex = Mutex.new
  end
  
  def get(key)
    @mutex.synchronize do
      entry = @cache[key]
      
      if entry && !expired?(entry)
        @stats[:hits] += 1
        puts "Cache HIT: #{key}"
        entry[:value]
      else
        @stats[:misses] += 1
        puts "Cache MISS: #{key}"
        nil
      end
    end
  end
  
  def set(key, value, ttl = nil)
    @mutex.synchronize do
      # Remove old entry if exists
      @cache.delete(key) if @cache.key?(key)
      
      # Evict if necessary
      evict_if_needed
      
      # Add new entry
      @cache[key] = {
        value: value,
        created_at: Time.now,
        expires_at: Time.now + (ttl || @ttl),
        access_count: 0,
        size: calculate_size(value)
      }
      
      @stats[:size] = @cache.size
      puts "Cache SET: #{key} (TTL: #{ttl || @ttl}s)"
      
      value
    end
  end
  
  def delete(key)
    @mutex.synchronize do
      if @cache.key?(key)
        @cache.delete(key)
        @stats[:size] = @cache.size
        puts "Cache DELETE: #{key}"
      end
    end
  end
  
  def clear
    @mutex.synchronize do
      @cache.clear
      @stats[:size] = 0
      puts "Cache CLEARED"
    end
  end
  
  def exists?(key)
    @mutex.synchronize do
      entry = @cache[key]
      entry && !expired?(entry)
    end
  end
  
  def size
    @mutex.synchronize { @cache.size }
  end
  
  def stats
    @stats.merge({
      hit_rate: @stats[:hits] + @stats[:misses] > 0 ? (@stats[:hits].to_f / (@stats[:hits] + @stats[:misses]) * 100).round(2) : 0,
      eviction_rate: @stats[:evictions] > 0 ? (@stats[:evictions].to_f / (@stats[:hits] + @stats[:misses] + @stats[:evictions]) * 100).round(2) : 0
    })
  end
  
  def self.demonstrate_cache
    puts "Query Cache Demonstration:"
    puts "=" * 50
    
    cache = QueryCache.new(size: 10, ttl: 60)
    
    # Test basic operations
    puts "Basic Operations:"
    
    # Set values
    cache.set('user:1', { name: 'Alice', email: 'alice@example.com' })
    cache.set('user:2', { name: 'Bob', email: 'bob@example.com' })
    cache.set('user:3', { name: 'Charlie', email: 'charlie@example.com' }, 30)
    
    # Get values
    user1 = cache.get('user:1')
    puts "Get user:1: #{user1 ? user1[:name] : 'nil'}"
    
    user2 = cache.get('user:2')
    puts "Get user:2: #{user2 ? user2[:name] : 'nil'}"
    
    user4 = cache.get('user:4')
    puts "Get user:4: #{user4 ? user4[:name] : 'nil'}"
    
    # Test cache expiration
    puts "\nCache Expiration:"
    sleep(2)
    
    user1_expired = cache.get('user:1')
    puts "Get user:1 after 2s: #{user1_expired ? user1_expired[:name] : 'nil'}"
    
    user3_expired = cache.get('user:3')
    puts "Get user:3 after 2s: #{user3_expired ? user3_expired[:name] : 'nil'}"
    
    # Test cache size limits
    puts "\nCache Size Limits:"
    
    # Fill cache beyond size limit
    15.times do |i|
      cache.set("user:#{i + 4}", { name: "User #{i + 4}" })
    end
    
    puts "Cache size: #{cache.size} (limit: 10)"
    puts "Evictions: #{cache.stats[:evictions]}"
    
    # Test cache statistics
    puts "\nCache Statistics:"
    stats = cache.stats
    stats.each { |key, value| puts "  #{key}: #{value}" }
    
    # Clear cache
    puts "\nClearing cache:"
    cache.clear
    puts "Cache size after clear: #{cache.size}"
    puts "Stats after clear: #{cache.stats}"
    
    puts "\nCache Features:"
    puts "- Key-value storage"
    puts "- TTL (time-to-live) support"
    "- Size limits with LRU eviction"
    "- Thread safety"
    "- Cache statistics"
    "- Expiration handling"
    "- Cache hits/misses tracking"
  end
  
  private
  
  def expired?(entry)
    Time.now > entry[:expires_at]
  end
  
  def calculate_size(value)
    # Simplified size calculation
    value.to_s.length
  end
  
  def evict_if_needed
    return if @cache.size <= @cache_size
    
    # Evict least recently used items
    sorted_entries = @cache.sort_by { |_, entry| entry[:access_count] }
    items_to_evict = sorted_entries.first(@cache.size - @cache_size)
    
    items_to_evict.each do |key, entry|
      @cache.delete(key)
      @stats[:evictions] += 1
      puts "Cache EVICTED: #{key}"
    end
  end
end

class QueryCacheManager
  def initialize
    @caches = {
      short_term: QueryCache.new(size: 100, ttl: 60),    # 1 minute
      medium_term: QueryCache.new(size: 500, ttl: 300),  # 5 minutes
      long_term: QueryCache.new(size: 1000, ttl: 1800) # 30 minutes
    }
    @stats = {
      total_hits: 0,
      total_misses: 0
    }
  end
  
  def get(key, cache_type = :short_term)
    cache = @caches[cache_type]
    return nil unless cache
    
    result = cache.get(key)
    update_stats(result != nil)
    
    result
  end
  
  def set(key, value, cache_type = :short_term, ttl = nil)
    cache = @caches[cache_type]
    return false unless cache
    
    cache.set(key, value, ttl)
    true
  end
  
  def invalidate(key)
    @caches.each_value { |cache| cache.delete(key) }
    puts "Invalidated cache entry: #{key}"
  end
  
  def clear(cache_type = nil)
    if cache_type
      @caches[cache_type]&.clear
      puts "Cleared #{cache_type} cache"
    else
      @caches.each_value(&:clear)
      puts "Cleared all caches"
    end
  end
  
  def stats
    total_hits = @caches.values.sum { |cache| cache.stats[:hits] }
    total_misses = @caches.values.sum { |cache| cache.stats[:misses] }
    
    @stats.merge({
      total_hits: total_hits,
      total_misses: total_misses,
      hit_rate: total_hits + total_misses > 0 ? (total_hits.to_f / (total_hits + total_misses) * 100).round(2) : 0,
      cache_sizes: @caches.transform_values { |type, cache| cache.size }
    })
  end
  
  def self.demonstrate_cache_manager
    puts "Query Cache Manager Demonstration:"
    puts "=" * 50
    
    manager = QueryCacheManager.new
    
    # Test different cache types
    puts "Testing Different Cache Types:"
    
    # Short-term cache
    manager.set('user:1', { name: 'Alice' }, :short_term)
    short_term_result = manager.get('user:1', :short_term)
    puts "Short-term cache: #{short_term_result ? short_term_result[:name] : 'nil'}"
    
    # Medium-term cache
    manager.set('user:2', { name: 'Bob' }, :medium_term)
    medium_term_result = manager.get('user:2', :medium_term)
    puts "Medium-term cache: #{medium_term_result ? medium_term_result[:name] : 'nil'}"
    
    # Long-term cache
    manager.set('user:3', { name: 'Charlie' }, :long_term)
    long_term_result = manager.get('user:3', :long_term)
    puts "Long-term cache: #{long_term_result ? long_term_result[:name] : 'nil'}"
    
    # Test cache invalidation
    puts "\nTesting Cache Invalidation:"
    manager.invalidate('user:1')
    invalidated_result = manager.get('user:1')
    puts "Invalidated cache: #{invalidated_result ? invalidated_result[:name] : 'nil'}"
    
    # Test statistics
    puts "\nCache Manager Statistics:"
    stats = manager.stats
    stats.each { |key, value| puts "  #{key}: #{value}" }
    
    # Test cache clearing
    puts "\nCache Clearing:"
    manager.clear(:short_term)
    puts "Short-term cache cleared"
    
    manager.clear
    puts "All caches cleared"
    
    puts "\nCache Manager Features:"
    puts "- Multiple cache levels"
    puts "- Different TTL for each level"
    "- Cache invalidation"
    "- Cache statistics"
    "- Automatic cache clearing"
    - "Thread-safe operations"
  end
end
```

## 🎯 Performance Monitoring

### 6. Performance Monitoring

Query performance tracking:

```ruby
class PerformanceMonitor
  def initialize
    @query_metrics = []
    @slow_queries = []
    @threshold = 1000 # milliseconds
    @enabled = false
    @alerts = []
  end
  
  def enable
    @enabled = true
    puts "Performance monitoring enabled (threshold: #{@threshold}ms)"
  end
  
  def disable
    @enabled = false
    puts "Performance monitoring disabled"
  end
  
  def track_query(sql, params = [], execution_time = nil, rows_returned = nil)
    return unless @enabled
    
    execution_time ||= 0
    rows_returned ||= 0
    
    metric = {
      sql: sql,
      params: params,
      execution_time: execution_time,
      rows_returned: rows_returned,
      timestamp: Time.now,
      slow: execution_time > @threshold
    }
    
    @query_metrics << metric
    
    if metric[:slow]
      @slow_queries << metric
      @alerts << create_alert(metric)
      puts "🐌 Slow Query Alert: #{(execution_time).round(2)}ms"
      puts "  SQL: #{sql[0..50]}..."
      puts "  Rows: #{rows_returned}"
      puts "  Timestamp: #{metric[:timestamp]}"
    end
  end
  
  def get_slow_queries(limit = 10)
    @slow_queries.last(limit)
  end
  
  def get_query_metrics(limit = 50)
    @query_metrics.last(limit)
  end
  
  def generate_report
    puts "Performance Report:"
    puts "=" * 50
    
    return puts "No queries tracked" if @query_metrics.empty?
    
    total_queries = @query_metrics.length
    total_time = @query_metrics.sum { |m| m[:execution_time] }
    avg_time = total_time / total_queries
    slow_queries = @slow_queries.length
    slow_percentage = (slow_queries.to_f / total_queries * 100).round(2)
    max_time = @query_metrics.max_by { |m| m[:execution_time] }
    min_time = @query_metrics.min_by { |m| m[:execution_time] }
    
    puts "Total Queries: #{total_queries}"
    puts "Total Time: #{total_time.round(2)}ms"
    puts "Average Time: #{avg_time.round(2)}ms"
    puts "Slow Queries: #{slow_queries} (#{slow_percentage}%)"
    puts "Max Time: #{max_time[:execution_time].round(2)}ms"
    puts "Min Time: #{min_time[:execution_time].round(2)}ms"
    
    # Slow queries analysis
    puts "\nSlow Queries Analysis:"
    @slow_queries.last(5).each_with_index do |query, i|
      puts "#{i + 1}. #{(query[:execution_time]).round(2)}ms"
      puts "   SQL: #{query[:sql][0..50]}..."
      puts "   Rows: #{query[:rows_returned]}"
      puts "   Timestamp: #{query[:timestamp]}"
    end
    
    # Performance trends
    puts "\nPerformance Trends:"
    trends = analyze_performance_trends
    trends.each do |trend|
      puts "#{t[:period]}: #{t[:avg_time].round(2)}ms avg, #{t[:slow_count]} slow queries"
    end
    
    # Alerts
    puts "\nPerformance Alerts:"
    @alerts.last(5).each_with_index do |alert, i|
      puts "#{i + 1}. #{alert[:message]}"
      puts "   Severity: #{alert[:severity]}"
      puts   Timestamp: #{alert[:timestamp]}"
    end
    
    # Recommendations
    puts "\nPerformance Recommendations:"
    recommendations = generate_recommendations
    recommendations.each { |rec| puts "- #{rec}" }
    
    {
      total_queries: total_queries,
      total_time: total_time,
      avg_time: avg_time,
      slow_queries: slow_queries,
      slow_percentage: slow_percentage,
      max_time: max_time[:execution_time],
      min_time: min_time[:execution_time],
      trends: trends,
      alerts: @alerts,
      recommendations: recommendations
    }
  end
  
  def set_threshold(threshold)
    @threshold = threshold
    puts "Performance monitoring threshold set to #{threshold}ms"
  end
  
  def self.demonstrate_monitoring
    puts "Performance Monitoring Demonstration:"
    puts "=" * 50
    
    monitor = PerformanceMonitor.new
    monitor.enable
    
    # Simulate different query performances
    queries = [
      {
        name: "Fast Query",
        sql: "SELECT * FROM users WHERE id = ?",
        params: [1],
        execution_time: 50,
        rows_returned: 1
      },
      {
        name: "Medium Query",
        sql: "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC",
        params: [1],
        execution_time: 200,
        rows_returned: 50
      },
      {
        name: "Slow Query",
        sql: "SELECT * FROM orders JOIN users ON orders.user_id = users.id JOIN order_items ON orders.id = order_items.order_id WHERE users.created_at > ?",
        params: [Time.now - 30.days],
        execution_time: 1500,
        rows_returned: 100
      },
      {
        name: "Very Slow Query",
        sql: "SELECT * FROM products p JOIN categories c ON p.category_id = c.id JOIN reviews r ON r.product_id = p.id WHERE p.price > ? AND c.name = ? AND r.rating >= ?",
        params: [100, 'Electronics', 4],
        execution_time: 3000,
        rows_returned: 500
      }
    ]
    
    puts "Tracking query performances:"
    
    queries.each do |query|
      puts "\n#{query[:name]}:"
      monitor.track_query(query[:sql], query[:params], query[:execution_time], query[:rows_returned])
    end
    
    # Generate report
    puts "\nGenerating performance report:"
    report = monitor.generate_report
    
    puts "\nPerformance Monitoring Features:"
    puts "- Query execution time tracking"
    slow_queries = monitor.get_slow_queries
    puts "- Slow query detection and alerting"
    puts "- Performance statistics and trends"
    puts "- Performance recommendations"
    puts "- Threshold-based alerting"
    puts "- Historical performance analysis"
  end
  
  private
  
  def create_alert(metric)
    {
      message: "Query execution time exceeded threshold",
      severity: metric[:execution_time] > 2000 ? 'critical' : 'warning',
      timestamp: metric[:timestamp],
      details: {
        sql: metric[:sql],
        execution_time: metric[:execution_time],
        rows_returned: metric[:rows_returned]
      }
    }
  end
  
  def analyze_performance_trends
    # Group queries by time periods
    now = Time.now
    trends = []
    
    # Last hour
    hour_ago = now - 3600
    hour_queries = @query_metrics.select { |m| m[:timestamp] >= hour_ago }
    if hour_queries.any?
      hour_avg = hour_queries.sum { |m| m[:execution_time] } / hour_queries.length
      hour_slow = hour_queries.count { |m| m[:slow] }
      trends << { period: 'Last Hour', avg_time: hour_avg, slow_count: hour_slow }
    end
    
    # Last day
    day_ago = now - 86400
    day_queries = @query_metrics.select { |m| m[:timestamp] >= day_ago }
    if day_queries.any?
      day_avg = day_queries.sum { |m| m[:execution_time] } / day_queries.length
      day_slow = day_queries.count { |m| m[:slow] }
      trends << { period: 'Last Day', avg_time: day_avg, slow_count: day_slow }
    end
    
    # Last week
    week_ago = now - 604800
    week_queries = @query_metrics.select { |m| m[:timestamp] >= week_ago }
    if week_queries.any?
      week_avg = week_queries.sum { |m| m[:execution_time] } / week_queries.length
      week_slow = week_queries.count { |m| m[:slow] }
      trends << { period: 'Last Week', avg_time: week_avg, slow_count: week_slow }
    end
    
    trends
  end
  
  def generate_recommendations
    recommendations = []
    
    slow_count = @slow_queries.length
    total_count = @query_metrics.length
    
    if slow_count > 0
      recommendations << "Found #{slow_count} slow queries out of #{total_count} total"
    end
    
    if slow_count > total_count * 0.1
      recommendations << "High percentage of slow queries - urgent optimization needed"
    end
    
    avg_time = @query_metrics.sum { |m| m[:execution_time] } / @query_metrics.length
    if avg_time > 500
      recommendations << "High average query time (#{avg_time.round(2)}ms) - optimization required"
    end
    
    max_time = @query_metrics.max_by { |m| m[:execution_time] }
    if max_time[:execution_time] > 5000
      recommendations << "Very slow query detected (#{max_time[:execution_time].round(2)}ms) - immediate optimization required"
    end
    
    recommendations << "Consider adding indexes for frequently queried columns"
    recommendations << "Optimize complex queries and subqueries"
    recommendations << "Implement query result caching"
    recommendations << "Review database configuration and hardware"
    
    recommendations
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Query Profiler**: Implement query profiling tool
2. **Index Optimization**: Create index optimization system
3. **Query Builder**: Build optimized query builder
4. **Query Caching**: Implement caching system

### Intermediate Exercises

1. **Advanced Indexing**: Complex index strategies
2. **Query Optimization**: Multi-query optimization
3. **Performance Monitoring**: Comprehensive monitoring
4. **Cache Management**: Multi-level caching

### Advanced Exercises

1. **Query Analyzer**: Complete query analysis tool
2. **Auto-Optimization**: Automatic query optimization
3. **Performance Tuning**: Database performance tuning
4. **Real-time Monitoring**: Live performance tracking

---

## 🎯 Summary

Query Optimization in Ruby provides:

- **Optimization Fundamentals** - Core concepts and principles
- **Query Profiler** - Performance analysis tool
- **Index Optimization** - Database indexing strategies
- **Query Builder** - Efficient query construction
- **Query Caching** - Intelligent caching system
- **Performance Monitoring** - Performance tracking and alerting

Master these query optimization techniques for high-performance Ruby applications!
