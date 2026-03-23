# Database Connection Management in Ruby
# This file demonstrates various database connection patterns, connection pooling,
# and database management strategies in Ruby applications.

module DatabaseExamples
  module DatabaseConnections
    # 1. Basic Database Connection
    # Simple connection setup and management
    
    class BasicConnectionManager
      def initialize(config)
        @config = config
        @connection = nil
      end
      
      def connect
        @connection = PG.connect(@config)
        puts "Connected to database: #{@config[:dbname]}"
        @connection
      end
      
      def disconnect
        if @connection
          @connection.close
          @connection = nil
          puts "Disconnected from database"
        end
      end
      
      def execute_query(sql, params = [])
        ensure_connected
        
        begin
          result = @connection.exec_params(sql, params)
          puts "Query executed successfully: #{result.cmd_tuples} rows affected"
          result
        rescue PG::Error => e
          puts "Query failed: #{e.message}"
          raise
        end
      end
      
      def transaction
        ensure_connected
        
        begin
          @connection.transaction do
            yield @connection
          end
        rescue PG::Error => e
          puts "Transaction failed: #{e.message}"
          raise
        end
      end
      
      def connected?
        @connection && !@connection.finished?
      end
      
      private
      
      def ensure_connected
        connect unless connected?
      end
    end
    
    # 2. Connection Pool
    # Managing multiple database connections efficiently
    
    class ConnectionPool
      def initialize(config, pool_size = 5)
        @config = config
        @pool_size = pool_size
        @available_connections = Queue.new
        @all_connections = []
        @mutex = Mutex.new
        
        create_pool
      end
      
      def with_connection
        connection = checkout_connection
        begin
          yield connection
        ensure
          checkin_connection(connection)
        end
      end
      
      def execute_query(sql, params = [])
        with_connection do |connection|
          connection.exec_params(sql, params)
        end
      end
      
      def transaction
        with_connection do |connection|
          connection.transaction do
            yield connection
          end
        end
      end
      
      def pool_stats
        {
          total_connections: @all_connections.size,
          available_connections: @available_connections.size,
          busy_connections: @all_connections.size - @available_connections.size
        }
      end
      
      def shutdown
        @mutex.synchronize do
          while @available_connections.size > 0
            connection = @available_connections.pop
            connection.close
          end
          
          @all_connections.clear
        end
      end
      
      private
      
      def create_pool
        @pool_size.times do
          connection = PG.connect(@config)
          @all_connections << connection
          @available_connections << connection
        end
        
        puts "Created connection pool with #{@pool_size} connections"
      end
      
      def checkout_connection
        @mutex.synchronize do
          if @available_connections.empty?
            # Wait for an available connection
            connection = @available_connections.pop(true) # non-blocking
          else
            connection = @available_connections.pop
          end
          
          # Check if connection is still alive
          if connection.finished?
            puts "Dead connection found, creating new one"
            connection = PG.connect(@config)
            @all_connections << connection
          end
          
          connection
        end
      end
      
      def checkin_connection(connection)
        @available_connections.push(connection)
      end
    end
    
    # 3. Database Configuration Management
    # Environment-specific database configurations
    
    class DatabaseConfig
      def self.load_config(environment = :development)
        config_file = "config/database.yml"
        
        unless File.exist?(config_file)
          puts "Database config file not found: #{config_file}"
          return default_config(environment)
        end
        
        configs = YAML.load_file(config_file)
        configs[environment.to_s] || default_config(environment)
      end
      
      def self.default_config(environment)
        case environment
        when :development
          {
            host: 'localhost',
            port: 5432,
            dbname: 'ruby_app_development',
            user: 'postgres',
            password: 'password',
            pool: 5,
            timeout: 5000
          }
        when :test
          {
            host: 'localhost',
            port: 5432,
            dbname: 'ruby_app_test',
            user: 'postgres',
            password: 'password',
            pool: 5,
            timeout: 5000
          }
        when :production
          {
            host: ENV['DB_HOST'] || 'localhost',
            port: ENV['DB_PORT'] || 5432,
            dbname: ENV['DB_NAME'] || 'ruby_app_production',
            user: ENV['DB_USER'] || 'postgres',
            password: ENV['DB_PASSWORD'],
            pool: 20,
            timeout: 10000
          }
        else
          raise ArgumentError, "Unknown environment: #{environment}"
        end
      end
      
      def self.validate_config(config)
        required_fields = %w[host port dbname user]
        
        missing_fields = required_fields.select { |field| config[field.to_sym].nil? }
        
        unless missing_fields.empty?
          raise ArgumentError, "Missing required database config fields: #{missing_fields.join(', ')}"
        end
        
        # Validate port
        port = config[:port]
        unless port.is_a?(Integer) && port > 0 && port < 65536
          raise ArgumentError, "Invalid port number: #{port}"
        end
        
        # Validate pool size
        pool = config[:pool] || 5
        unless pool.is_a?(Integer) && pool > 0
          raise ArgumentError, "Invalid pool size: #{pool}"
        end
        
        true
      end
    end
    
    # 4. Database Health Monitoring
    # Monitoring database connectivity and performance
    
    class DatabaseHealthMonitor
      def initialize(connection_pool)
        @connection_pool = connection_pool
        @health_status = :unknown
        @last_check = nil
        @error_count = 0
        @mutex = Mutex.new
      end
      
      def check_health
        @mutex.synchronize do
          begin
            @connection_pool.with_connection do |connection|
              # Test basic connectivity
              result = connection.exec('SELECT 1 as test')
              
              # Test database responsiveness
              start_time = Time.current
              connection.exec('SELECT COUNT(*) FROM pg_stat_activity')
              response_time = (Time.current - start_time) * 1000
              
              # Check connection pool health
              pool_stats = @connection_pool.pool_stats
              
              @health_status = :healthy
              @last_check = Time.current
              @error_count = 0
              
              {
                status: @health_status,
                response_time_ms: response_time.round(2),
                pool_stats: pool_stats,
                timestamp: @last_check
              }
            end
          rescue => e
            @error_count += 1
            @health_status = :unhealthy if @error_count >= 3
            
            {
              status: @health_status,
              error: e.message,
              error_count: @error_count,
              timestamp: Time.current
            }
          end
        end
      end
      
      def healthy?
        @health_status == :healthy
      end
      
      def status
        @health_status
      end
      
      def last_check
        @last_check
      end
    end
    
    # 5. Database Migration Manager
    # Managing database schema migrations
    
    class MigrationManager
      def initialize(connection_pool)
        @connection_pool = connection_pool
        @migrations_path = 'db/migrations'
        @migrations_table = 'schema_migrations'
      end
      
      def create_migrations_table
        @connection_pool.execute_query(<<~SQL)
          CREATE TABLE IF NOT EXISTS #{@migrations_table} (
            version VARCHAR(255) PRIMARY KEY,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
          )
        SQL
      end
      
      def pending_migrations
        create_migrations_table
        
        applied_versions = get_applied_versions
        all_versions = get_all_migration_versions
        
        (all_versions - applied_versions).sort
      end
      
      def migrate_up(target_version = nil)
        pending = pending_migrations
        
        if target_version
          pending = pending.select { |version| version <= target_version }
        end
        
        pending.each do |version|
          run_migration_up(version)
        end
        
        puts "Migrated #{pending.length} pending migrations"
      end
      
      def migrate_down(target_version)
        applied = get_applied_versions.sort.reverse
        
        applied.each do |version|
          break if version <= target_version
          run_migration_down(version)
        end
        
        puts "Migrated down to version #{target_version}"
      end
      
      def current_version
        get_applied_versions.max
      end
      
      def migration_status
        {
          current_version: current_version,
          pending_count: pending_migrations.length,
          pending_migrations: pending_migrations
        }
      end
      
      private
      
      def get_applied_versions
        result = @connection_pool.execute_query("SELECT version FROM #{@migrations_table}")
        result.map { |row| row['version'] }
      end
      
      def get_all_migration_versions
        Dir.glob("#{@migrations_path}/*.rb").map do |file|
          File.basename(file, '.rb')
        end
      end
      
      def run_migration_up(version)
        migration_file = "#{@migrations_path}/#{version}.rb"
        
        unless File.exist?(migration_file)
          raise ArgumentError, "Migration file not found: #{migration_file}"
        end
        
        load migration_file
        
        migration_class = "Migration#{version.gsub(/[^\d]/, '')}"
        migration = Object.const_get(migration_class).new
        
        @connection_pool.transaction do |connection|
          migration.up(connection)
          connection.exec_params(
            "INSERT INTO #{@migrations_table} (version) VALUES ($1)",
            [version]
          )
        end
        
        puts "Applied migration: #{version}"
      end
      
      def run_migration_down(version)
        migration_file = "#{@migrations_path}/#{version}.rb"
        
        unless File.exist?(migration_file)
          raise ArgumentError, "Migration file not found: #{migration_file}"
        end
        
        load migration_file
        
        migration_class = "Migration#{version.gsub(/[^\d]/, '')}"
        migration = Object.const_get(migration_class).new
        
        @connection_pool.transaction do |connection|
          migration.down(connection)
          connection.exec_params(
            "DELETE FROM #{@migrations_table} WHERE version = $1",
            [version]
          )
        end
        
        puts "Reverted migration: #{version}"
      end
    end
    
    # 6. Database Backup and Restore
    # Automated backup and restore functionality
    
    class DatabaseBackupManager
      def initialize(config)
        @config = config
        @backup_dir = 'db/backups'
        Dir.mkdir(@backup_dir) unless Dir.exist?(@backup_dir)
      end
      
      def create_backup(type = :full)
        timestamp = Time.current.strftime('%Y%m%d_%H%M%S')
        backup_file = "#{@backup_dir}/backup_#{type}_#{timestamp}.sql"
        
        case type
        when :full
          create_full_backup(backup_file)
        when :incremental
          create_incremental_backup(backup_file)
        when :differential
          create_differential_backup(backup_file)
        else
          raise ArgumentError, "Unknown backup type: #{type}"
        end
        
        compress_backup(backup_file)
        cleanup_old_backups
        
        "#{backup_file}.gz"
      end
      
      def restore_from_backup(backup_file)
        if backup_file.end_with?('.gz')
          decompress_backup(backup_file)
          backup_file = backup_file.gsub('.gz', '')
        end
        
        unless File.exist?(backup_file)
          raise ArgumentError, "Backup file not found: #{backup_file}"
        end
        
        puts "Restoring from backup: #{backup_file}"
        
        # Use psql to restore
        system("psql -h #{@config[:host]} -p #{@config[:port]} -U #{@config[:user]} -d #{@config[:dbname]} < #{backup_file}")
        
        puts "Database restored successfully"
      end
      
      def list_backups
        Dir.glob("#{@backup_dir}/*.gz").sort_by { |file| File.mtime(file) }.reverse
      end
      
      def backup_info(backup_file)
        return nil unless File.exist?(backup_file)
        
        stat = File.stat(backup_file)
        
        {
          file: backup_file,
          size: stat.size,
          created_at: stat.mtime,
          compressed: backup_file.end_with?('.gz')
        }
      end
      
      private
      
      def create_full_backup(backup_file)
        puts "Creating full backup: #{backup_file}"
        
        command = "pg_dump -h #{@config[:host]} -p #{@config[:port]} -U #{@config[:user]} -d #{@config[:dbname]} > #{backup_file}"
        system(command)
      end
      
      def create_incremental_backup(backup_file)
        # For PostgreSQL, we'll use WAL (Write-Ahead Log) for incremental backups
        puts "Creating incremental backup: #{backup_file}"
        
        # This is a simplified implementation
        # In practice, you'd use WAL archiving
        command = "pg_dump -h #{@config[:host]} -p #{@config[:port]} -U #{@config[:user]} -d #{@config[:dbname]} --data-only > #{backup_file}"
        system(command)
      end
      
      def create_differential_backup(backup_file)
        puts "Creating differential backup: #{backup_file}"
        
        # Get the last full backup timestamp
        last_full_backup = list_backups.find { |file| file.include?('full_') }
        
        if last_full_backup
          # Create differential since last full backup
          command = "pg_dump -h #{@config[:host]} -p #{@config[:port]} -U #{@config[:user]} -d #{@config[:dbname]} --since '#{File.mtime(last_full_backup)}' > #{backup_file}"
        else
          # No full backup found, create full backup instead
          create_full_backup(backup_file)
        end
        
        system(command)
      end
      
      def compress_backup(backup_file)
        puts "Compressing backup: #{backup_file}"
        system("gzip #{backup_file}")
      end
      
      def decompress_backup(backup_file)
        puts "Decompressing backup: #{backup_file}"
        system("gunzip #{backup_file}")
      end
      
      def cleanup_old_backups
        # Keep only last 30 days of backups
        cutoff_date = 30.days.ago
        
        list_backups.each do |backup_file|
          if File.mtime(backup_file) < cutoff_date
            File.delete(backup_file)
            puts "Deleted old backup: #{backup_file}"
          end
        end
      end
    end
    
    # 7. Database Query Builder
    # Building complex database queries programmatically
    
    class QueryBuilder
      def initialize(table_name)
        @table_name = table_name
        @select_clauses = ['*']
        @where_clauses = []
        @join_clauses = []
        @order_clauses = []
        @group_clauses = []
        @having_clauses = []
        @limit_value = nil
        @offset_value = nil
        @params = []
      end
      
      def select(*fields)
        @select_clauses = fields.map { |field| field.to_s }
        self
      end
      
      def where(condition, *params)
        @where_clauses << condition
        @params.concat(params)
        self
      end
      
      def join(table, on_condition)
        @join_clauses << "INNER JOIN #{table} ON #{on_condition}"
        self
      end
      
      def left_join(table, on_condition)
        @join_clauses << "LEFT JOIN #{table} ON #{on_condition}"
        self
      end
      
      def order(field, direction = :asc)
        @order_clauses << "#{field} #{direction.to_s.upcase}"
        self
      end
      
      def group(*fields)
        @group_clauses = fields.map { |field| field.to_s }
        self
      end
      
      def having(condition, *params)
        @having_clauses << condition
        @params.concat(params)
        self
      end
      
      def limit(count)
        @limit_value = count
        self
      end
      
      def offset(count)
        @offset_value = count
        self
      end
      
      def build
        sql = "SELECT #{@select_clauses.join(', ')}"
        sql << " FROM #{@table_name}"
        
        sql << " #{@join_clauses.join(' ')}" unless @join_clauses.empty?
        sql << " WHERE #{@where_clauses.join(' AND ')}" unless @where_clauses.empty?
        sql << " GROUP BY #{@group_clauses.join(', ')}" unless @group_clauses.empty?
        sql << " HAVING #{@having_clauses.join(' AND ')}" unless @having_clauses.empty?
        sql << " ORDER BY #{@order_clauses.join(', ')}" unless @order_clauses.empty?
        sql << " LIMIT #{@limit_value}" if @limit_value
        sql << " OFFSET #{@offset_value}" if @offset_value
        
        [sql, @params]
      end
      
      def to_sql
        sql, _ = build
        sql
      end
    end
    
    # 8. Database Replication Support
    # Read replicas and write splitting
    
    class ReplicationManager
      def initialize(primary_config, replica_configs = [])
        @primary_pool = ConnectionPool.new(primary_config)
        @replica_pools = replica_configs.map { |config| ConnectionPool.new(config) }
        @current_replica_index = 0
      end
      
      def with_primary_connection(&block)
        @primary_pool.with_connection(&block)
      end
      
      def with_replica_connection(&block)
        return yield @primary_pool if @replica_pools.empty?
        
        replica_pool = get_next_replica_pool
        replica_pool.with_connection(&block)
      rescue => e
        puts "Replica connection failed, falling back to primary: #{e.message}"
        @primary_pool.with_connection(&block)
      end
      
      def execute_read_query(sql, params = [])
        with_replica_connection do |connection|
          connection.exec_params(sql, params)
        end
      end
      
      def execute_write_query(sql, params = [])
        with_primary_connection do |connection|
          connection.exec_params(sql, params)
        end
      end
      
      def replication_lag
        return 0 if @replica_pools.empty?
        
        max_lag = 0
        @replica_pools.each do |replica_pool|
          begin
            replica_pool.with_connection do |connection|
              result = connection.exec('SELECT pg_last_xact_replay_timestamp()')
              lag = Time.current - Time.parse(result[0]['pg_last_xact_replay_timestamp'])
              max_lag = [max_lag, lag].max
            end
          rescue => e
            puts "Failed to check replication lag: #{e.message}"
          end
        end
        
        max_lag
      end
      
      def switch_to_primary_if_needed
        lag = replication_lag
        
        if lag > 30.seconds
          puts "High replication lag detected (#{lag}s), switching to primary for reads"
          return true
        end
        
        false
      end
      
      private
      
      def get_next_replica_pool
        pool = @replica_pools[@current_replica_index]
        @current_replica_index = (@current_replica_index + 1) % @replica_pools.size
        pool
      end
    end
    
    # 9. Database Connection Factory
    # Factory pattern for creating different types of connections
    
    class DatabaseConnectionFactory
      def self.create_connection(type, config)
        case type
        when :basic
          BasicConnectionManager.new(config)
        when :pooled
          pool_size = config.delete(:pool) || 5
          ConnectionPool.new(config, pool_size)
        when :replicated
          primary_config = config[:primary]
          replica_configs = config[:replicas] || []
          ReplicationManager.new(primary_config, replica_configs)
        else
          raise ArgumentError, "Unknown connection type: #{type}"
        end
      end
      
      def self.create_from_environment(environment = :development)
        config = DatabaseConfig.load_config(environment)
        DatabaseConfig.validate_config(config)
        
        case environment
        when :production
          create_connection(:replicated, config)
        when :staging
          create_connection(:pooled, config)
        else
          create_connection(:basic, config)
        end
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "Database Connection Management Demonstration"
  puts "=" * 60
  
  # Demonstrate basic connection
  puts "\n1. Basic Connection:"
  config = {
    host: 'localhost',
    port: 5432,
    dbname: 'test_db',
    user: 'postgres',
    password: 'password'
  }
  
  basic_manager = DatabaseExamples::DatabaseConnections::BasicConnectionManager.new(config)
  puts "✅ Basic connection manager created"
  
  # Demonstrate connection pool
  puts "\n2. Connection Pool:"
  pool = DatabaseExamples::DatabaseConnections::ConnectionPool.new(config, 3)
  puts "✅ Connection pool created with 3 connections"
  
  # Demonstrate query builder
  puts "\n3. Query Builder:"
  query = DatabaseExamples::DatabaseConnections::QueryBuilder.new('users')
    .select('id', 'name', 'email')
    .where('active = ?', true)
    .order('created_at', :desc)
    .limit(10)
  
  sql, params = query.build
  puts "✅ Query built: #{sql}"
  puts "✅ Parameters: #{params}"
  
  # Demonstrate health monitoring
  puts "\n4. Health Monitoring:"
  health_monitor = DatabaseExamples::DatabaseConnections::DatabaseHealthMonitor.new(pool)
  puts "✅ Health monitor created"
  
  # Demonstrate backup manager
  puts "\n5. Backup Manager:"
  backup_manager = DatabaseExamples::DatabaseConnections::DatabaseBackupManager.new(config)
  puts "✅ Backup manager created"
  
  # Demonstrate replication
  puts "\n6. Replication Manager:"
  replica_configs = [
    config.merge(host: 'replica1'),
    config.merge(host: 'replica2')
  ]
  
  replication_manager = DatabaseExamples::DatabaseConnections::ReplicationManager.new(config, replica_configs)
  puts "✅ Replication manager created with 2 replicas"
  
  # Demonstrate factory pattern
  puts "\n7. Connection Factory:"
  connection = DatabaseExamples::DatabaseConnections::DatabaseConnectionFactory.create_from_environment(:development)
  puts "✅ Connection created for development environment"
  
  puts "\nDatabase connection patterns help build scalable and reliable applications!"
end
