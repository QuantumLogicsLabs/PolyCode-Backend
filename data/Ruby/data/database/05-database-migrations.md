# Database Migrations in Ruby
# Comprehensive guide to database schema management and migrations

## 🔄 Migration Fundamentals

### 1. Migration Concepts

Core database migration principles:

```ruby
class MigrationFundamentals
  def self.explain_migration_concepts
    puts "Database Migration Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Database Migration",
        description: "Version-controlled database schema changes",
        purpose: ["Manage schema evolution", "Team collaboration", "Deployment automation"],
        benefits: ["Reproducible changes", "Rollback capability", "Version control"],
        components: ["Migration files", "Version tracking", "Execution engine"]
      },
      {
        concept: "Schema Versioning",
        description: "Track database schema versions",
        implementation: ["Version numbers", "Migration files", "Schema table"],
        benefits: ["Version control", "Rollback support", "Change tracking"],
        challenges: ["Version conflicts", "Data migration", "Schema evolution"]
      },
      {
        concept: "Migration Files",
        description: "Files containing database schema changes",
        structure: ["Up method", "Down method", "Version number", "Description"],
        naming: ["Timestamp", "Sequential numbering", "Descriptive names"],
        content: ["DDL statements", "Data migrations", "Indexes", "Constraints"]
      },
      {
        concept: "Migration Execution",
        description: "Process of applying database changes",
        steps: ["Check current version", "Apply pending migrations", "Update version tracking"],
        safety: ["Transaction wrapping", "Error handling", "Rollback support"],
        validation: ["Schema validation", "Data integrity", "Performance impact"]
      },
      {
        concept: "Rollback Strategy",
        description: "Ability to undo database changes",
        approaches: ["Down methods", "Backup and restore", "Version rollback"],
        considerations: ["Data loss", "Foreign key constraints", "Index dependencies"],
        best_practices: ["Test rollbacks", "Backup before rollback", "Rollback planning"]
      },
      {
        concept: "Data Migration",
        description: "Migrating data during schema changes",
        challenges: ["Data transformation", "Performance", "Data integrity"],
        strategies: ["Batch processing", "Temporary tables", "Parallel processing"],
        considerations: ["Downtime", "Data loss", "Rollback complexity"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Purpose: #{concept[:purpose].join(', ')}" if concept[:purpose]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Implementation: #{concept[:implementation].join(', ')}" if concept[:implementation]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Structure: #{concept[:structure].join(', ')}" if concept[:structure]
      puts "  Naming: #{concept[:naming].join(', ')}" if concept[:naming]
      puts "  Content: #{concept[:content].join(', ')}" if concept[:content]
      puts "  Steps: #{concept[:steps].join(', ')}" if concept[:steps]
      puts "  Safety: #{concept[:safety].join(', ')}" if concept[:safety]
      puts "  Validation: #{concept[:validation].join(', ')}" if concept[:validation]
      puts "  Approaches: #{concept[:approaches].join(', ')}" if concept[:approaches]
      puts "  Considerations: #{concept[:considerations].join(', ')}" if concept[:considerations]
      puts "  Best Practices: #{concept[:best_practices].join(', ')}" if concept[:best_practices]
      puts
    end
  end
  
  def self.migration_lifecycle
    puts "\nMigration Lifecycle:"
    puts "=" * 50
    
    lifecycle = [
      {
        phase: "1. Creation",
        description: "Create new migration file",
        steps: [
          "Generate migration file",
          "Define up method",
          "Define down method",
          "Add version number",
          "Write description"
        ],
        tools: ["Migration generator", "Editor", "Version tracking"]
      },
      {
        phase: "2. Development",
        description: "Develop and test migration",
        steps: [
          "Write SQL changes",
          "Add data transformations",
          "Include error handling",
          "Test on development database",
          "Review with team"
        ],
        considerations: ["Data safety", "Performance", "Rollback", "Compatibility"]
      },
      {
        phase: "3. Testing",
        description: "Test migration thoroughly",
        steps: [
          "Test up migration",
          "Test down migration",
          "Test data integrity",
          "Test performance",
          "Test rollback"
        ],
        environments: ["Development", "Staging", "Test"]
      },
      {
        phase: "4. Deployment",
        description: "Deploy migration to production",
        steps: [
          "Backup database",
          "Deploy migration",
          "Monitor execution",
          "Verify results",
          "Update documentation"
        ],
        safety: ["Backup strategy", "Rollback plan", "Monitoring", "Validation"]
      },
      {
        phase: "5. Verification",
        description: "Verify migration success",
        steps: [
          "Check schema version",
          "Validate data integrity",
          "Test application functionality",
          "Monitor performance",
          "Document changes"
        ],
        validation: ["Schema validation", "Data validation", "Application testing"]
      }
    ]
    
    lifecycle.each do |phase|
      puts "#{phase[:phase]}: #{phase[:description]}"
      puts "  Steps: #{phase[:steps].join(', ')}"
      puts "  Tools: #{phase[:tools].join(', ')}" if phase[:tools]
      puts "  Considerations: #{phase[:considerations].join(', ')}" if phase[:considerations]
      puts "  Environments: #{phase[:environments].join(', ')}" if phase[:environments]
      puts "  Safety: #{phase[:safety].join(', ')}" if phase[:safety]
      puts "  Validation: #{phase[:validation].join(', ')}" if phase[:validation]
      puts
    end
  end
  
  def self.migration_best_practices
    puts "\nMigration Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Version Control",
        description: "Keep migrations in version control",
        guidelines: [
          "Commit migration files",
          "Use descriptive names",
          "Document changes",
          "Review migrations",
          "Test before commit"
        ],
        benefits: ["Team collaboration", "Change tracking", "Reproducibility"]
      },
      {
        practice: "Atomic Changes",
        description: "Make migrations atomic and reversible",
        guidelines: [
          "Use transactions",
          "Write down methods",
          "Test rollbacks",
          "Handle errors gracefully",
          "Validate before commit"
        ],
        benefits: ["Data safety", "Rollback capability", "Consistency"]
      },
      {
        practice: "Data Safety",
        description: "Protect data during migrations",
        guidelines: [
          "Backup before migration",
          "Test on staging",
          "Use transactions",
          "Monitor execution",
          "Have rollback plan"
        ],
        benefits: ["Data protection", "Recovery capability", "Risk mitigation"]
      },
      {
        practice: "Performance",
        description: "Optimize migration performance",
        guidelines: [
          "Batch large operations",
          "Use indexes wisely",
          "Avoid locking",
          "Monitor performance",
          "Test with realistic data"
        ],
        benefits: ["Faster execution", "Less downtime", "Better user experience"]
      },
      {
        practice: "Documentation",
        description: "Document migration changes",
        guidelines: [
          "Write clear descriptions",
          "Document data changes",
          "Include rollback notes",
          "Update schema docs",
          "Communicate changes"
        ],
        benefits: ["Team understanding", "Change tracking", "Maintenance"]
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
  
  # Run migration fundamentals
  explain_migration_concepts
  migration_lifecycle
  migration_best_practices
end
```

### 2. Migration Framework

Core migration system implementation:

```ruby
class Migration
  attr_reader :version, :name, :description
  
  def initialize(version, name, description = nil)
    @version = version
    @name = name
    @description = description || name.humanize
    @executed_at = nil
  end
  
  def up
    raise NotImplementedError, "Subclass must implement up method"
  end
  
  def down
    raise NotImplementedError, "Subclass must implement down method"
  end
  
  def execute_up(connection)
    puts "Executing migration #{@version}: #{@name} (up)"
    start_time = Time.now
    
    begin
      connection.transaction do
        up
        record_execution(connection, 'up')
      end
      
      @executed_at = Time.now
      duration = Time.now - start_time
      puts "Migration #{@version} completed in #{duration.round(2)}s"
      
      true
    rescue => e
      puts "Migration #{@version} failed: #{e.message}"
      raise
    end
  end
  
  def execute_down(connection)
    puts "Executing migration #{@version}: #{@name} (down)"
    start_time = Time.now
    
    begin
      connection.transaction do
        down
        record_execution(connection, 'down')
      end
      
      duration = Time.now - start_time
      puts "Migration #{@version} rollback completed in #{duration.round(2)}s"
      
      true
    rescue => e
      puts "Migration #{@version} rollback failed: #{e.message}"
      raise
    end
  end
  
  def self.generate_name(description)
    timestamp = Time.now.strftime('%Y%m%d_%H%M%S')
    "#{timestamp}_#{description.gsub(/[^a-zA-Z0-9_]/, '_').downcase}"
  end
  
  private
  
  def record_execution(connection, direction)
    sql = "INSERT INTO schema_migrations (version, name, description, direction, executed_at) VALUES (?, ?, ?, ?, ?)"
    params = [@version, @name, @description, direction, Time.now]
    
    if direction == 'down'
      # Remove previous up record
      connection.execute("DELETE FROM schema_migrations WHERE version = ? AND direction = 'up'", [@version])
    end
    
    connection.execute(sql, params)
  end
end

class CreateUsersTable < Migration
  def initialize
    super(1, 'create_users_table', 'Create users table with basic fields')
  end
  
  def up
    sql = <<~SQL
      CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        age INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
      );
    SQL
    
    execute_sql(sql)
    
    # Add indexes
    execute_sql("CREATE INDEX idx_users_email ON users(email);")
    execute_sql("CREATE INDEX idx_users_created_at ON users(created_at);")
    
    puts "  Created users table with indexes"
  end
  
  def down
    execute_sql("DROP TABLE users;")
    puts "  Dropped users table"
  end
  
  private
  
  def execute_sql(sql)
    puts "    [Migration] #{sql.strip}"
    # In real implementation, this would execute the SQL
  end
end

class AddUserProfileFields < Migration
  def initialize
    super(2, 'add_user_profile_fields', 'Add profile fields to users table')
  end
  
  def up
    # Add new columns
    execute_sql("ALTER TABLE users ADD COLUMN bio TEXT;")
    execute_sql("ALTER TABLE users ADD COLUMN avatar_url VARCHAR(255);")
    execute_sql("ALTER TABLE users ADD COLUMN phone VARCHAR(20);")
    execute_sql("ALTER TABLE users ADD COLUMN website VARCHAR(255);")
    
    # Add index for new columns
    execute_sql("CREATE INDEX idx_users_phone ON users(phone);")
    
    puts "  Added profile fields to users table"
  end
  
  def down
    # Remove indexes first
    execute_sql("DROP INDEX IF EXISTS idx_users_phone;")
    
    # Remove columns
    execute_sql("ALTER TABLE users DROP COLUMN bio;")
    execute_sql("ALTER TABLE users DROP COLUMN avatar_url;")
    execute_sql("ALTER TABLE users DROP COLUMN phone;")
    execute_sql("ALTER TABLE users DROP COLUMN website;")
    
    puts "  Removed profile fields from users table"
  end
  
  private
  
  def execute_sql(sql)
    puts "    [Migration] #{sql.strip}"
  end
end

class CreatePostsTable < Migration
  def initialize
    super(3, 'create_posts_table', 'Create posts table for user posts')
  end
  
  def up
    sql = <<~SQL
      CREATE TABLE posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        user_id INTEGER NOT NULL,
        status VARCHAR(50) DEFAULT 'draft',
        published_at DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
      );
    SQL
    
    execute_sql(sql)
    
    # Add indexes
    execute_sql("CREATE INDEX idx_posts_user_id ON posts(user_id);")
    execute_sql("CREATE INDEX idx_posts_status ON posts(status);")
    execute_sql("CREATE INDEX idx_posts_published_at ON posts(published_at);")
    
    puts "  Created posts table with indexes and foreign key"
  end
  
  def down
    execute_sql("DROP TABLE posts;")
    puts "  Dropped posts table"
  end
  
  private
  
  def execute_sql(sql)
    puts "    [Migration] #{sql.strip}"
  end
end

class MigrationFramework
  def initialize(connection)
    @connection = connection
    @migrations = {}
    @current_version = 0
    @executed_migrations = []
    load_migrations
    ensure_schema_migrations_table
    load_executed_migrations
  end
  
  def migrate(target_version = nil)
    target_version ||= latest_version
    
    puts "Migrating to version #{target_version}"
    puts "Current version: #{@current_version}"
    
    while @current_version < target_version
      next_version = @current_version + 1
      migration = @migrations[next_version]
      
      unless migration
        puts "No migration found for version #{next_version}"
        break
      end
      
      migration.execute_up(@connection)
      @current_version = next_version
      @executed_migrations << migration
    end
    
    puts "Migration completed. Current version: #{@current_version}"
    @current_version
  end
  
  def rollback(target_version = nil)
    target_version ||= @current_version - 1
    
    puts "Rolling back to version #{target_version}"
    puts "Current version: #{@current_version}"
    
    while @current_version > target_version
      migration = @migrations[@current_version]
      
      unless migration
        puts "No migration found for version #{@current_version}"
        break
      end
      
      migration.execute_down(@connection)
      @current_version -= 1
      @executed_migrations.pop
    end
    
    puts "Rollback completed. Current version: #{@current_version}"
    @current_version
  end
  
  def status
    puts "Migration Status:"
    puts "Current version: #{@current_version}"
    puts "Latest version: #{latest_version}"
    puts "Pending migrations: #{latest_version - @current_version}"
    puts "Executed migrations: #{@executed_migrations.length}"
    
    (@current_version + 1).upto(latest_version) do |version|
      migration = @migrations[version]
      puts "  #{version}: #{migration.name} (#{migration.description})"
    end
  end
  
  def create_migration(name, description = nil)
    version = latest_version + 1
    migration_name = Migration.generate_name(name)
    
    puts "Creating migration #{version}: #{migration_name}"
    
    # Generate migration file content
    content = generate_migration_file_content(version, migration_name, description)
    
    # In real implementation, this would write to a file
    puts "Migration file content:"
    puts content
    
    migration_name
  end
  
  def self.demonstrate_framework
    puts "Migration Framework Demonstration:"
    puts "=" * 50
    
    # Create mock connection
    connection = MockMigrationConnection.new
    framework = MigrationFramework.new(connection)
    
    # Load migrations
    puts "Loading migrations:"
    framework.load_migrations
    
    # Show initial status
    puts "\nInitial status:"
    framework.status
    
    # Run migrations
    puts "\nRunning migrations:"
    framework.migrate
    
    # Show status after migrations
    puts "\nStatus after migrations:"
    framework.status
    
    # Create new migration
    puts "\nCreating new migration:"
    framework.create_migration('add_comments_table', 'Create comments table for posts')
    
    # Run new migration
    puts "\nRunning new migration:"
    framework.migrate
    
    # Rollback
    puts "\nRolling back:"
    framework.rollback(1)
    
    # Show final status
    puts "\nFinal status:"
    framework.status
    
    puts "\nMigration Framework Features:"
    puts "- Version-controlled migrations"
    puts "- Up and down migration support"
    puts "- Transaction wrapping"
    puts "- Migration tracking"
    puts "- Rollback capability"
    puts "- Migration generation"
    puts "- Status reporting"
  end
  
  private
  
  def load_migrations
    @migrations[1] = CreateUsersTable.new
    @migrations[2] = AddUserProfileFields.new
    @migrations[3] = CreatePostsTable.new
  end
  
  def ensure_schema_migrations_table
    sql = <<~SQL
      CREATE TABLE IF NOT EXISTS schema_migrations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        version INTEGER NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        direction VARCHAR(10) NOT NULL,
        executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
      );
    SQL
    
    @connection.execute(sql)
  end
  
  def load_executed_migrations
    results = @connection.execute("SELECT version, name, direction, executed_at FROM schema_migrations ORDER BY version, executed_at")
    
    # Get latest up migration for each version
    latest_up_migrations = {}
    results.each do |row|
      if row['direction'] == 'up'
        latest_up_migrations[row['version']] = row
      end
    end
    
    @current_version = latest_up_migrations.keys.max || 0
    @executed_migrations = latest_up_migrations.values.map do |row|
      @migrations[row['version']]
    end.compact
  end
  
  def latest_version
    @migrations.keys.max || 0
  end
  
  def generate_migration_file_content(version, name, description)
    <<~RUBY
      class #{name.camelize} < Migration
        def initialize
          super(#{version}, '#{name}', '#{description}')
        end
        
        def up
          # Add your migration code here
          # Example:
          # execute_sql("CREATE TABLE example_table (id INTEGER PRIMARY KEY);")
        end
        
        def down
          # Add your rollback code here
          # Example:
          # execute_sql("DROP TABLE example_table;")
        end
        
        private
        
        def execute_sql(sql)
          # Execute SQL statement
          puts "    [Migration] \#{sql.strip}"
        end
      end
    RUBY
  end
end

class MockMigrationConnection
  def initialize
    @executed_statements = []
  end
  
  def execute(sql, params = [])
    puts "  [Database] Executing: #{sql}"
    puts "  [Database] Parameters: #{params}" if params.any?
    
    # Simulate execution
    @executed_statements << { sql: sql, params: params, timestamp: Time.now }
    
    # Return mock results
    case sql
    when /SELECT.*FROM schema_migrations/
      []
    when /INSERT INTO schema_migrations/
      true
    else
      true
    end
  end
  
  def transaction
    puts "  [Database] Starting transaction"
    yield
    puts "  [Database] Committing transaction"
  end
end
```

## 🏗️ Schema Management

### 3. Schema Builder

Database schema construction:

```ruby
class SchemaBuilder
  def initialize(connection)
    @connection = connection
    @statements = []
  end
  
  def create_table(table_name, options = {})
    builder = TableBuilder.new(table_name, options, @connection)
    yield builder if block_given?
    
    sql = builder.to_sql
    @connection.execute(sql)
    @statements << sql
    
    puts "Created table: #{table_name}"
    table_name
  end
  
  def drop_table(table_name, options = {})
    sql = "DROP TABLE"
    sql += " IF EXISTS" if options[:if_exists]
    sql += " #{table_name}"
    
    if options[:cascade]
      sql += " CASCADE"
    end
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Dropped table: #{table_name}"
  end
  
  def rename_table(old_name, new_name)
    sql = "ALTER TABLE #{old_name} RENAME TO #{new_name}"
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Renamed table: #{old_name} -> #{new_name}"
  end
  
  def add_column(table_name, column_name, type, options = {})
    sql = "ALTER TABLE #{table_name} ADD COLUMN #{column_name} #{type}"
    
    # Add options
    sql += " NOT NULL" if options[:null] == false
    sql += " DEFAULT #{quote_value(options[:default])}" if options.key?(:default)
    sql += " UNIQUE" if options[:unique]
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Added column: #{table_name}.#{column_name} (#{type})"
  end
  
  def remove_column(table_name, column_name)
    sql = "ALTER TABLE #{table_name} DROP COLUMN #{column_name}"
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Removed column: #{table_name}.#{column_name}"
  end
  
  def change_column(table_name, column_name, type, options = {})
    sql = "ALTER TABLE #{table_name} ALTER COLUMN #{column_name} TYPE #{type}"
    
    # Add options (database-specific)
    if options[:null] == false
      sql += " SET NOT NULL"
    elsif options[:null] == true
      sql += " DROP NOT NULL"
    end
    
    if options.key?(:default)
      sql += " SET DEFAULT #{quote_value(options[:default])}"
    end
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Changed column: #{table_name}.#{column_name} (#{type})"
  end
  
  def rename_column(table_name, old_name, new_name)
    sql = "ALTER TABLE #{table_name} RENAME COLUMN #{old_name} TO #{new_name}"
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Renamed column: #{table_name}.#{old_name} -> #{new_name}"
  end
  
  def add_index(table_name, column_names, options = {})
    index_name = options[:name] || generate_index_name(table_name, column_names)
    unique = options[:unique] ? "UNIQUE" : ""
    where = options[:where] ? " WHERE #{options[:where]}" : ""
    
    sql = "CREATE #{unique} INDEX #{index_name} ON #{table_name} (#{Array(column_names).join(', ')})#{where}"
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Added index: #{index_name} on #{table_name} (#{Array(column_names).join(', ')})"
  end
  
  def remove_index(table_name, options = {})
    if options[:name]
      index_name = options[:name]
    elsif options[:column]
      index_name = generate_index_name(table_name, options[:column])
    else
      raise "Must specify either :name or :column"
    end
    
    sql = "DROP INDEX IF EXISTS #{index_name}"
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Removed index: #{index_name}"
  end
  
  def add_foreign_key(from_table, to_table, options = {})
    column = options[:column] || "#{to_table.singularize}_id"
    constraint_name = options[:name] || "fk_#{from_table}_#{column}"
    on_delete = options[:on_delete] || "RESTRICT"
    on_update = options[:on_update] || "RESTRICT"
    
    sql = <<~SQL
      ALTER TABLE #{from_table} 
      ADD CONSTRAINT #{constraint_name} 
      FOREIGN KEY (#{column}) 
      REFERENCES #{to_table}(id) 
      ON DELETE #{on_delete} 
      ON UPDATE #{on_update}
    SQL
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Added foreign key: #{constraint_name}"
  end
  
  def remove_foreign_key(from_table, options = {})
    if options[:name]
      constraint_name = options[:name]
    elsif options[:column]
      constraint_name = "fk_#{from_table}_#{options[:column]}"
    else
      raise "Must specify either :name or :column"
    end
    
    sql = "ALTER TABLE #{from_table} DROP CONSTRAINT IF EXISTS #{constraint_name}"
    
    @connection.execute(sql)
    @statements << sql
    
    puts "Removed foreign key: #{constraint_name}"
  end
  
  def execute(sql)
    @connection.execute(sql)
    @statements << sql
    
    puts "Executed: #{sql}"
  end
  
  def statements
    @statements
  end
  
  def self.demonstrate_schema_builder
    puts "Schema Builder Demonstration:"
    puts "=" * 50
    
    # Create mock connection
    connection = MockSchemaConnection.new
    builder = SchemaBuilder.new(connection)
    
    puts "Building database schema:"
    
    # Create users table
    puts "\nCreating users table:"
    builder.create_table(:users) do |t|
      t.string :name, null: false
      t.string :email, null: false, unique: true
      t.string :password_hash, null: false
      t.integer :age
      t.timestamp :created_at, default: 'CURRENT_TIMESTAMP'
      t.timestamp :updated_at, default: 'CURRENT_TIMESTAMP'
    end
    
    # Create posts table
    puts "\nCreating posts table:"
    builder.create_table(:posts) do |t|
      t.string :title, null: false
      t.text :content
      t.references :user, foreign_key: true, on_delete: :cascade
      t.string :status, default: 'draft'
      t.timestamp :published_at
      t.timestamps
    end
    
    # Add indexes
    puts "\nAdding indexes:"
    builder.add_index(:users, :email, unique: true)
    builder.add_index(:users, :created_at)
    builder.add_index(:posts, :user_id)
    builder.add_index(:posts, :status)
    builder.add_index(:posts, [:user_id, :status])
    
    # Add foreign key
    puts "\nAdding foreign key:"
    builder.add_foreign_key(:posts, :users, on_delete: :cascade)
    
    # Modify table
    puts "\nModifying table:"
    builder.add_column(:users, :bio, :text)
    builder.add_column(:users, :avatar_url, :string)
    builder.change_column(:users, :age, :integer, null: false, default: 0)
    
    # Show all statements
    puts "\nAll executed statements:"
    builder.statements.each_with_index do |statement, i|
      puts "#{i + 1}. #{statement}"
    end
    
    puts "\nSchema Builder Features:"
    puts "- Table creation and dropping"
    puts "- Column management"
    puts "- Index creation and removal"
    puts "- Foreign key management"
    puts "- Constraint handling"
    puts "- Schema modification"
    puts "- SQL generation"
  end
  
  private
  
  def generate_index_name(table_name, column_names)
    columns = Array(column_names).join('_')
    "idx_#{table_name}_#{columns}"
  end
  
  def quote_value(value)
    case value
    when String
      "'#{value.gsub("'", "''")}'"
    when NilClass
      'NULL'
    when TrueClass, FalseClass
      value ? 'TRUE' : 'FALSE'
    else
      value.to_s
    end
  end
end

class TableBuilder
  def initialize(table_name, options, connection)
    @table_name = table_name
    @options = options
    @connection = connection
    @columns = []
    @indexes = []
    @foreign_keys = []
    @primary_key = options[:primary_key] || 'id'
    @timestamps = options[:timestamps] != false
  end
  
  def string(name, options = {})
    @columns << { name: name, type: 'VARCHAR(255)', options: options }
  end
  
  def text(name, options = {})
    @columns << { name: name, type: 'TEXT', options: options }
  end
  
  def integer(name, options = {})
    @columns << { name: name, type: 'INTEGER', options: options }
  end
  
  def decimal(name, options = {})
    precision = options[:precision] || 10
    scale = options[:scale] || 2
    @columns << { name: name, type: "DECIMAL(#{precision},#{scale})", options: options }
  end
  
  def timestamp(name, options = {})
    @columns << { name: name, type: 'DATETIME', options: options }
  end
  
  def boolean(name, options = {})
    @columns << { name: name, type: 'BOOLEAN', options: options }
  end
  
  def references(name, options = {})
    column_name = options[:column] || "#{name}_id"
    @columns << { name: column_name, type: 'INTEGER', options: { null: false } }
    
    if options[:foreign_key] != false
      @foreign_keys << {
        column: column_name,
        references: name,
        on_delete: options[:on_delete] || 'RESTRICT',
        on_update: options[:on_update] || 'RESTRICT'
      }
    end
  end
  
  def timestamps
    @timestamps = true
  end
  
  def to_sql
    sql = "CREATE TABLE #{@table_name} (\n"
    
    # Add columns
    column_definitions = []
    
    # Primary key
    column_definitions << "#{@primary_key} INTEGER PRIMARY KEY AUTOINCREMENT"
    
    # Regular columns
    @columns.each do |column|
      column_def = "#{column[:name]} #{column[:type]}"
      
      # Add options
      column_def += " NOT NULL" if column[:options][:null] == false
      column_def += " DEFAULT #{quote_value(column[:options][:default])}" if column[:options].key?(:default)
      column_def += " UNIQUE" if column[:options][:unique]
      
      column_definitions << column_def
    end
    
    # Timestamps
    if @timestamps
      column_definitions << "created_at DATETIME DEFAULT CURRENT_TIMESTAMP"
      column_definitions << "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP"
    end
    
    sql += column_definitions.map { |def| "  #{def}" }.join(",\n")
    sql += "\n);"
    
    sql
  end
  
  private
  
  def quote_value(value)
    case value
    when String
      "'#{value.gsub("'", "''")}'"
    when NilClass
      'NULL'
    when TrueClass, FalseClass
      value ? 'TRUE' : 'FALSE'
    else
      value.to_s
    end
  end
end

class MockSchemaConnection
  def initialize
    @executed_statements = []
  end
  
  def execute(sql)
    puts "  [Database] Executing: #{sql}"
    @executed_statements << { sql: sql, timestamp: Time.now }
    true
  end
end
```

## 📝 Data Migrations

### 4. Data Migration System

Data transformation and migration:

```ruby
class DataMigration
  def initialize(connection)
    @connection = connection
    @batch_size = 1000
  end
  
  def transform_data(table_name, transformations = {})
    puts "Transforming data in table: #{table_name}"
    
    # Get table schema
    schema = get_table_schema(table_name)
    
    # Get total rows
    total_rows = count_rows(table_name)
    puts "Total rows to transform: #{total_rows}"
    
    # Process in batches
    offset = 0
    processed = 0
    
    while offset < total_rows
      batch = get_batch(table_name, offset, @batch_size)
      
      batch.each do |row|
        transformed_row = transform_row(row, transformations)
        update_row(table_name, row['id'], transformed_row)
        processed += 1
      end
      
      puts "Processed #{processed}/#{total_rows} rows"
      offset += @batch_size
    end
    
    puts "Data transformation completed"
    processed
  end
  
  def migrate_data(from_table, to_table, transformations = {})
    puts "Migrating data from #{from_table} to #{to_table}"
    
    # Get source data
    total_rows = count_rows(from_table)
    puts "Total rows to migrate: #{total_rows}"
    
    # Process in batches
    offset = 0
    migrated = 0
    
    while offset < total_rows
      batch = get_batch(from_table, offset, @batch_size)
      
      batch.each do |row|
        transformed_row = transform_row(row, transformations)
        insert_row(to_table, transformed_row)
        migrated += 1
      end
      
      puts "Migrated #{migrated}/#{total_rows} rows"
      offset += @batch_size
    end
    
    puts "Data migration completed"
    migrated
  end
  
  def validate_data(table_name, validations = {})
    puts "Validating data in table: #{table_name}"
    
    total_rows = count_rows(table_name)
    errors = []
    
    # Process in batches
    offset = 0
    validated = 0
    
    while offset < total_rows
      batch = get_batch(table_name, offset, @batch_size)
      
      batch.each do |row|
        row_errors = validate_row(row, validations)
        if row_errors.any?
          errors << {
            id: row['id'],
            errors: row_errors,
            data: row
          }
        end
        validated += 1
      end
      
      puts "Validated #{validated}/#{total_rows} rows"
      offset += @batch_size
    end
    
    puts "Data validation completed"
    puts "Found #{errors.length} validation errors"
    
    if errors.any?
      puts "Validation errors:"
      errors.first(5).each do |error|
        puts "  Row #{error[:id]}: #{error[:errors].join(', ')}"
      end
    end
    
    errors
  end
  
  def backup_table(table_name, backup_name = nil)
    backup_name ||= "#{table_name}_backup_#{Time.now.strftime('%Y%m%d_%H%M%S')}"
    
    puts "Creating backup of #{table_name} as #{backup_name}"
    
    # Create backup table
    create_table_like(table_name, backup_name)
    
    # Copy data
    total_rows = count_rows(table_name)
    copied = 0
    
    offset = 0
    while offset < total_rows
      batch = get_batch(table_name, offset, @batch_size)
      
      batch.each do |row|
        insert_row(backup_name, row)
        copied += 1
      end
      
      puts "Copied #{copied}/#{total_rows} rows"
      offset += @batch_size
    end
    
    puts "Backup completed: #{backup_name}"
    backup_name
  end
  
  def self.demonstrate_data_migration
    puts "Data Migration Demonstration:"
    puts "=" * 50
    
    # Create mock connection
    connection = MockDataConnection.new
    migrator = DataMigration.new(connection)
    
    # Create sample data
    puts "Creating sample data:"
    connection.create_sample_data
    
    # Data transformation
    puts "\nData Transformation:"
    transformations = {
      'name' => ->(value) { value&.upcase },
      'email' => ->(value) { value&.downcase },
      'age' => ->(value) { value ? value + 1 : nil }
    }
    
    migrator.transform_data('users', transformations)
    
    # Data migration
    puts "\nData Migration:"
    migration_transformations = {
      'name' => ->(value) { value },
      'email' => ->(value) { value },
      'user_id' => ->(value) { value },
      'created_at' => ->(value) { Time.now }
    }
    
    migrator.migrate_data('users', 'user_profiles', migration_transformations)
    
    # Data validation
    puts "\nData Validation:"
    validations = {
      'name' => ->(value) { value && !value.empty? },
      'email' => ->(value) { value && value.include?('@') },
      'age' => ->(value) { value && value > 0 }
    }
    
    migrator.validate_data('users', validations)
    
    # Backup table
    puts "\nTable Backup:"
    backup_name = migrator.backup_table('users')
    
    puts "\nData Migration Features:"
    puts "- Data transformation"
    puts "- Data migration between tables"
    puts "- Data validation"
    puts "- Table backup and restore"
    puts "- Batch processing"
    puts "- Progress tracking"
    puts "- Error handling"
  end
  
  private
  
  def get_table_schema(table_name)
    # Simulate schema retrieval
    {
      columns: [
        { name: 'id', type: 'integer' },
        { name: 'name', type: 'string' },
        { name: 'email', type: 'string' },
        { name: 'age', type: 'integer' }
      ]
    }
  end
  
  def count_rows(table_name)
    # Simulate row count
    1000
  end
  
  def get_batch(table_name, offset, batch_size)
    # Simulate batch retrieval
    batch = []
    (offset...[offset + batch_size, 1000].min).each do |i|
      batch << {
        'id' => i + 1,
        'name' => "User #{i + 1}",
        'email' => "user#{i + 1}@example.com",
        'age' => rand(18..65)
      }
    end
    batch
  end
  
  def transform_row(row, transformations)
    transformed = row.dup
    
    transformations.each do |column, transformer|
      if transformed.key?(column)
        begin
          transformed[column] = transformer.call(transformed[column])
        rescue => e
          puts "Error transforming #{column}: #{e.message}"
        end
      end
    end
    
    transformed
  end
  
  def update_row(table_name, id, row)
    # Simulate row update
    puts "    [Database] UPDATE #{table_name} SET ... WHERE id = #{id}"
  end
  
  def insert_row(table_name, row)
    # Simulate row insertion
    puts "    [Database] INSERT INTO #{table_name} ..."
  end
  
  def validate_row(row, validations)
    errors = []
    
    validations.each do |column, validator|
      if row.key?(column)
        begin
          result = validator.call(row[column])
          errors << "#{column} validation failed" unless result
        rescue => e
          errors << "#{column} validation error: #{e.message}"
        end
      end
    end
    
    errors
  end
  
  def create_table_like(source_table, target_table)
    # Simulate table creation
    puts "    [Database] CREATE TABLE #{target_table} LIKE #{source_table}"
  end
end

class MockDataConnection
  def initialize
    @tables = {}
    @sequences = Hash.new(1)
  end
  
  def create_sample_data
    # Create sample users table
    @tables['users'] = []
    
    10.times do |i|
      @tables['users'] << {
        'id' => i + 1,
        'name' => "User #{i + 1}",
        'email' => "user#{i + 1}@example.com",
        'age' => rand(18..65)
      }
    end
    
    puts "  [Database] Created sample data: #{@tables['users'].length} users"
  end
  
  def execute(sql, params = [])
    puts "  [Database] Executing: #{sql}"
    puts "  [Database] Parameters: #{params}" if params.any?
    true
  end
end
```

## 🔄 Migration Testing

### 5. Migration Testing Framework

Testing database migrations:

```ruby
class MigrationTester
  def initialize
    @test_databases = {}
    @test_results = []
    @current_test = nil
  end
  
  def test_migration(migration_class, test_name = nil)
    test_name ||= migration_class.name
    @current_test = {
      name: test_name,
      migration: migration_class,
      started_at: Time.now,
      status: :running
    }
    
    puts "Testing migration: #{test_name}"
    
    begin
      # Create test database
      test_db = create_test_database(test_name)
      @test_databases[test_name] = test_db
      
      # Test up migration
      test_up_migration(migration_class, test_db)
      
      # Test down migration
      test_down_migration(migration_class, test_db)
      
      # Test data integrity
      test_data_integrity(migration_class, test_db)
      
      @current_test[:status] = :passed
      @current_test[:completed_at] = Time.now
      @current_test[:duration] = @current_test[:completed_at] - @current_test[:started_at]
      
      puts "✅ Migration test passed: #{test_name}"
      
    rescue => e
      @current_test[:status] = :failed
      @current_test[:error] = e.message
      @current_test[:completed_at] = Time.now
      @current_test[:duration] = @current_test[:completed_at] - @current_test[:started_at]
      
      puts "❌ Migration test failed: #{test_name}"
      puts "   Error: #{e.message}"
      
    ensure
      @test_results << @current_test
      cleanup_test_database(test_name)
    end
    
    @current_test
  end
  
  def test_all_migrations(migration_classes)
    puts "Testing all migrations..."
    
    results = []
    migration_classes.each do |migration_class|
      result = test_migration(migration_class)
      results << result
    end
    
    puts "\nMigration Test Results:"
    results.each do |result|
      status_icon = result[:status] == :passed ? '✅' : '❌'
      puts "#{status_icon} #{result[:name]} (#{result[:duration].round(2)}s)"
      puts "   Error: #{result[:error]}" if result[:status] == :failed
    end
    
    passed = results.count { |r| r[:status] == :passed }
    total = results.length
    
    puts "\nSummary: #{passed}/#{total} tests passed"
    
    results
  end
  
  def generate_report
    puts "Migration Test Report:"
    puts "=" * 50
    
    return puts "No tests run" if @test_results.empty?
    
    total_tests = @test_results.length
    passed_tests = @test_results.count { |r| r[:status] == :passed }
    failed_tests = total_tests - passed_tests
    total_duration = @test_results.sum { |r| r[:duration] }
    avg_duration = total_duration / total_tests
    
    puts "Total Tests: #{total_tests}"
    puts "Passed: #{passed_tests}"
    puts "Failed: #{failed_tests}"
    puts "Success Rate: #{(passed_tests.to_f / total_tests * 100).round(2)}%"
    puts "Total Duration: #{total_duration.round(2)}s"
    puts "Average Duration: #{avg_duration.round(2)}s"
    
    puts "\nFailed Tests:" if failed_tests > 0
    @test_results.select { |r| r[:status] == :failed }.each do |result|
      puts "  #{result[:name]}: #{result[:error]}"
    end
    
    puts "\nTest Details:"
    @test_results.each do |result|
      status_icon = result[:status] == :passed ? '✅' : '❌'
      puts "#{status_icon} #{result[:name]} (#{result[:duration].round(2)}s)"
    end
    
    {
      total_tests: total_tests,
      passed_tests: passed_tests,
      failed_tests: failed_tests,
      success_rate: (passed_tests.to_f / total_tests * 100).round(2),
      total_duration: total_duration,
      avg_duration: avg_duration,
      results: @test_results
    }
  end
  
  def self.demonstrate_migration_testing
    puts "Migration Testing Demonstration:"
    puts "=" * 50
    
    tester = MigrationTester.new
    
    # Test individual migrations
    puts "Testing individual migrations:"
    
    tester.test_migration(CreateUsersTable, 'create_users_table')
    tester.test_migration(AddUserProfileFields, 'add_user_profile_fields')
    tester.test_migration(CreatePostsTable, 'create_posts_table')
    
    # Test all migrations
    puts "\nTesting all migrations:"
    all_migrations = [CreateUsersTable, AddUserProfileFields, CreatePostsTable]
    results = tester.test_all_migrations(all_migrations)
    
    # Generate report
    puts "\nGenerating test report:"
    report = tester.generate_report
    
    puts "\nMigration Testing Features:"
    puts "- Individual migration testing"
    puts "- Batch migration testing"
    puts "- Up/down migration testing"
    puts "- Data integrity testing"
    puts "- Test database isolation"
    puts "- Error handling and reporting"
    puts "- Performance measurement"
    puts "- Comprehensive reporting"
  end
  
  private
  
  def create_test_database(test_name)
    puts "  Creating test database: #{test_name}"
    TestDatabase.new("#{test_name}_test")
  end
  
  def cleanup_test_database(test_name)
    if @test_databases.key?(test_name)
      puts "  Cleaning up test database: #{test_name}"
      @test_databases.delete(test_name)
    end
  end
  
  def test_up_migration(migration_class, test_db)
    puts "  Testing up migration..."
    
    migration = migration_class.new
    migration.execute_up(test_db)
    
    # Verify schema changes
    verify_schema_changes(migration_class, test_db, :up)
  end
  
  def test_down_migration(migration_class, test_db)
    puts "  Testing down migration..."
    
    migration = migration_class.new
    migration.execute_down(test_db)
    
    # Verify schema changes
    verify_schema_changes(migration_class, test_db, :down)
  end
  
  def test_data_integrity(migration_class, test_db)
    puts "  Testing data integrity..."
    
    # Insert test data
    insert_test_data(test_db)
    
    # Verify data integrity
    verify_data_integrity(test_db)
  end
  
  def verify_schema_changes(migration_class, test_db, direction)
    # Simulate schema verification
    puts "    Schema verification passed for #{direction}"
  end
  
  def insert_test_data(test_db)
    # Simulate test data insertion
    puts "    Test data inserted"
  end
  
  def verify_data_integrity(test_db)
    # Simulate data integrity verification
    puts "    Data integrity verified"
  end
end

class TestDatabase
  def initialize(name)
    @name = name
    @tables = {}
    @sequences = Hash.new(1)
  end
  
  def execute(sql, params = [])
    puts "    [TestDB #{@name}] Executing: #{sql}"
    puts "    [TestDB #{@name}] Parameters: #{params}" if params.any?
    
    # Simulate execution
    case sql
    when /CREATE TABLE/
      table_name = sql.match(/CREATE TABLE (\w+)/)[1]
      @tables[table_name] = []
      true
    when /DROP TABLE/
      table_name = sql.match(/DROP TABLE (\w+)/)[1]
      @tables.delete(table_name)
      true
    when /INSERT INTO/
      table_name = sql.match(/INSERT INTO (\w+)/)[1]
      @tables[table_name] ||= []
      @tables[table_name] << { 'id' => @sequences[table_name] }
      @sequences[table_name] += 1
      true
    else
      true
    end
  end
  
  def transaction
    puts "    [TestDB #{@name}] Starting transaction"
    yield
    puts "    [TestDB #{@name}] Committing transaction"
  end
end
```

## 🎯 Migration Tools

### 6. Migration Utilities

Helper utilities for migrations:

```ruby
class MigrationUtils
  def self.generate_migration_name(description)
    timestamp = Time.now.strftime('%Y%m%d_%H%M%S')
    "#{timestamp}_#{description.gsub(/[^a-zA-Z0-9_]/, '_').downcase}"
  end
  
  def self.parse_migration_name(filename)
    match = filename.match(/(\d+)_(.+)\.rb/)
    return nil unless match
    
    {
      timestamp: match[1],
      name: match[2],
      version: match[1].to_i
    }
  end
  
  def self.validate_migration_file(filename)
    errors = []
    
    # Check filename format
    unless filename.match(/^\d{14}_\w+\.rb$/)
      errors << "Invalid filename format: #{filename}"
    end
    
    # Check file existence
    unless File.exist?(filename)
      errors << "File does not exist: #{filename}"
    end
    
    # Check file content
    if File.exist?(filename)
      content = File.read(filename)
      
      unless content.include?('class') && content.include?('def up')
        errors << "Migration file must include class and up method"
      end
      
      unless content.include?('def down')
        errors << "Migration file must include down method"
      end
    end
    
    errors
  end
  
  def self.backup_database(connection, backup_name = nil)
    backup_name ||= "backup_#{Time.now.strftime('%Y%m%d_%H%M%S')}"
    
    puts "Creating database backup: #{backup_name}"
    
    # Get all tables
    tables = get_tables(connection)
    
    # Create backup SQL
    backup_sql = "-- Database backup created at #{Time.now}\n"
    backup_sql += "-- Backup name: #{backup_name}\n\n"
    
    tables.each do |table|
      backup_sql += "-- Table: #{table}\n"
      backup_sql += connection.get_table_schema_sql(table)
      backup_sql += "\n"
      
      # Get table data
      data = connection.get_table_data(table)
      data.each do |row|
        columns = row.keys.join(', ')
        values = row.values.map { |v| quote_sql_value(v) }.join(', ')
        backup_sql += "INSERT INTO #{table} (#{columns}) VALUES (#{values});\n"
      end
      
      backup_sql += "\n"
    end
    
    # Save backup file
    backup_file = "#{backup_name}.sql"
    File.write(backup_file, backup_sql)
    
    puts "Backup saved to: #{backup_file}"
    backup_file
  end
  
  def self.restore_database(connection, backup_file)
    puts "Restoring database from: #{backup_file}"
    
    unless File.exist?(backup_file)
      raise "Backup file not found: #{backup_file}"
    end
    
    # Read backup file
    backup_sql = File.read(backup_file)
    
    # Execute backup SQL
    connection.execute_batch(backup_sql)
    
    puts "Database restored successfully"
  end
  
  def self.compare_schemas(connection1, connection2, table_name)
    puts "Comparing schemas for table: #{table_name}"
    
    schema1 = connection1.get_table_schema(table_name)
    schema2 = connection2.get_table_schema(table_name)
    
    differences = []
    
    # Compare columns
    columns1 = schema1[:columns].map { |c| c[:name] }
    columns2 = schema2[:columns].map { |c| c[:name] }
    
    # Missing columns in schema2
    (columns1 - columns2).each do |column|
      differences << {
        type: :missing_column,
        table: table_name,
        column: column,
        in_schema: :second
      }
    end
    
    # Extra columns in schema2
    (columns2 - columns1).each do |column|
      differences << {
        type: :extra_column,
        table: table_name,
        column: column,
        in_schema: :first
      }
    end
    
    # Compare column types
    common_columns = columns1 & columns2
    common_columns.each do |column|
      col1 = schema1[:columns].find { |c| c[:name] == column }
      col2 = schema2[:columns].find { |c| c[:name] == column }
      
      if col1[:type] != col2[:type]
        differences << {
          type: :column_type_mismatch,
          table: table_name,
          column: column,
          type1: col1[:type],
          type2: col2[:type]
        }
      end
    end
    
    puts "Found #{differences.length} schema differences:"
    differences.each do |diff|
      case diff[:type]
      when :missing_column
        puts "  Missing column: #{diff[:table]}.#{diff[:column]} in #{diff[:in_schema]} schema"
      when :extra_column
        puts "  Extra column: #{diff[:table]}.#{diff[:column]} in #{diff[:in_schema]} schema"
      when :column_type_mismatch
        puts "  Type mismatch: #{diff[:table]}.#{diff[:column]} (#{diff[:type1]} vs #{diff[:type2]})"
      end
    end
    
    differences
  end
  
  def self.validate_schema(connection, schema_definition)
    puts "Validating database schema..."
    
    errors = []
    
    schema_definition[:tables].each do |table_def|
      table_name = table_def[:name]
      
      # Check if table exists
      unless connection.table_exists?(table_name)
        errors << "Table not found: #{table_name}"
        next
      end
      
      # Get actual schema
      actual_schema = connection.get_table_schema(table_name)
      
      # Check columns
      expected_columns = table_def[:columns] || []
      actual_columns = actual_schema[:columns].map { |c| c[:name] }
      
      expected_columns.each do |expected_col|
        unless actual_columns.include?(expected_col[:name])
          errors << "Column not found: #{table_name}.#{expected_col[:name]}"
        end
      end
      
      # Check indexes
      expected_indexes = table_def[:indexes] || []
      expected_indexes.each do |expected_idx|
        unless connection.index_exists?(table_name, expected_idx[:name])
          errors << "Index not found: #{expected_idx[:name]}"
        end
      end
    end
    
    puts "Schema validation completed"
    puts "Found #{errors.length} validation errors:"
    errors.each { |error| puts "  #{error}" }
    
    errors
  end
  
  def self.demonstrate_migration_utils
    puts "Migration Utilities Demonstration:"
    puts "=" * 50
    
    # Migration name generation
    puts "Migration Name Generation:"
    name = generate_migration_name('add_user_profiles')
    puts "Generated name: #{name}"
    
    # Migration name parsing
    puts "\nMigration Name Parsing:"
    parsed = parse_migration_name(name)
    puts "Parsed: #{parsed}"
    
    # Migration file validation
    puts "\nMigration File Validation:"
    errors = validate_migration_file('nonexistent.rb')
    puts "Validation errors: #{errors}"
    
    # Schema comparison
    puts "\nSchema Comparison:"
    connection1 = MockSchemaConnection.new
    connection2 = MockSchemaConnection.new
    differences = compare_schemas(connection1, connection2, 'users')
    puts "Schema differences: #{differences.length}"
    
    # Schema validation
    puts "\nSchema Validation:"
    schema_def = {
      tables: [
        {
          name: 'users',
          columns: [
            { name: 'id', type: 'integer' },
            { name: 'name', type: 'string' },
            { name: 'email', type: 'string' }
          ]
        }
      ]
    }
    validation_errors = validate_schema(connection1, schema_def)
    puts "Validation errors: #{validation_errors.length}"
    
    puts "\nMigration Utilities Features:"
    puts "- Migration name generation"
    puts "- Migration file validation"
    puts "- Database backup and restore"
    puts "- Schema comparison"
    puts "- Schema validation"
    puts "- SQL value quoting"
    puts "- Batch SQL execution"
  end
  
  private
  
  def self.get_tables(connection)
    # Simulate getting tables
    ['users', 'posts', 'comments']
  end
  
  def self.quote_sql_value(value)
    case value
    when String
      "'#{value.gsub("'", "''")}'"
    when NilClass
      'NULL'
    when TrueClass, FalseClass
      value ? 'TRUE' : 'FALSE'
    else
      value.to_s
    end
  end
end

class MockSchemaConnection
  def initialize
    @tables = {
      'users' => {
        columns: [
          { name: 'id', type: 'INTEGER' },
          { name: 'name', type: 'VARCHAR(255)' },
          { name: 'email', type: 'VARCHAR(255)' }
        ]
      }
    }
  end
  
  def table_exists?(table_name)
    @tables.key?(table_name)
  end
  
  def index_exists?(table_name, index_name)
    # Simulate index check
    true
  end
  
  def get_table_schema(table_name)
    @tables[table_name] || { columns: [] }
  end
  
  def get_table_schema_sql(table_name)
    "CREATE TABLE #{table_name} (id INTEGER, name VARCHAR(255));"
  end
  
  def get_table_data(table_name)
    # Simulate getting table data
    []
  end
  
  def execute_batch(sql)
    puts "  [Database] Executing batch SQL (#{sql.length} chars)"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Migration**: Create simple migration system
2. **Schema Builder**: Build schema construction tool
3. **Data Migration**: Implement data transformation
4. **Migration Testing**: Add testing framework

### Intermediate Exercises

1. **Advanced Migrations**: Complex schema changes
2. **Rollback System**: Robust rollback mechanism
3. **Batch Processing**: Large data migration
4. **Validation Framework**: Schema validation system

### Advanced Exercises

1. **Migration Platform**: Complete migration system
2. **Database Comparison**: Schema diff tool
3. **Migration Automation**: Automated deployment
4. **Multi-Database**: Cross-database migrations

---

## 🎯 Summary

Database Migrations in Ruby provide:

- **Migration Fundamentals** - Core concepts and lifecycle
- **Migration Framework** - Version-controlled schema changes
- **Schema Management** - Database schema construction
- **Data Migrations** - Data transformation and migration
- **Migration Testing** - Comprehensive testing framework
- **Migration Utilities** - Helper tools and utilities

Master these database migration techniques for robust Ruby applications!
