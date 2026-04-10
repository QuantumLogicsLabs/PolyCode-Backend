# Ruby Database Programming

## ActiveRecord Basics

### Database Configuration
```ruby
# config/database.yml
default: &default
  adapter: postgresql
  encoding: unicode
  pool: <%= ENV.fetch("RAILS_MAX_THREADS") { 5 } %>
  username: <%= ENV.fetch("DATABASE_USERNAME") %>
  password: <%= ENV.fetch("DATABASE_PASSWORD") %>
  host: <%= ENV.fetch("DATABASE_HOST") %>

development:
  <<: *default
  database: myapp_development

test:
  <<: *default
  database: myapp_test

production:
  <<: *default
  database: myapp_production
  pool: <%= ENV.fetch("RAILS_MAX_THREADS") { 5 } %>

# config/environments/development.rb
Rails.application.configure do
  config.log_level = :debug
  config.active_record.verbose_query_logs = true
  config.active_record.migration_error = :page_load
end

# config/environments/production.rb
Rails.application.configure do
  config.log_level = :info
  config.active_record.migration_error = :page_load
  config.active_record.dump_schema_after_migration = false
end
```

### Model Definition
```ruby
# app/models/user.rb
class User < ApplicationRecord
  # Validations
  validates :name, presence: true, length: { minimum: 2, maximum: 50 }
  validates :email, presence: true, uniqueness: true, 
                   format: { with: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i }
  validates :age, numericality: { greater_than_or_equal_to: 0, less_than: 150 }
  
  # Callbacks
  before_save :normalize_email
  after_create :send_welcome_email
  after_destroy :cleanup_user_data
  
  # Associations
  has_many :posts, dependent: :destroy
  has_many :comments, through: :posts
  has_one :profile, dependent: :destroy
  belongs_to :company, optional: true
  
  # Scopes
  scope :active, -> { where(active: true) }
  scope :by_age, ->(min_age, max_age) { where(age: min_age..max_age) }
  scope :recent, -> { order(created_at: :desc) }
  
  # Class methods
  def self.search(query)
    where("name ILIKE ? OR email ILIKE ?", "%#{query}%", "%#{query}%")
  end
  
  def self.by_company(company)
    where(company: company)
  end
  
  # Instance methods
  def full_name
    profile ? "#{name} #{profile.last_name}" : name
  end
  
  def admin?
    role == 'admin'
  end
  
  # Delegated methods
  delegate :bio, :avatar_url, to: :profile, prefix: true, allow_nil: true
  
  private
  
  def normalize_email
    self.email = email.downcase.strip
  end
  
  def send_welcome_email
    UserMailer.welcome_email(self).deliver_later
  end
  
  def cleanup_user_data
    # Cleanup related data
  end
end

# app/models/post.rb
class Post < ApplicationRecord
  belongs_to :user
  has_many :comments, dependent: :destroy
  has_many :likes, dependent: :destroy
  has_many :taggings, dependent: :destroy
  has_many :tags, through: :taggings
  
  validates :title, presence: true, length: { maximum: 200 }
  validates :content, presence: true, length: { minimum: 10 }
  validates :user_id, presence: true
  
  scope :published, -> { where(published: true) }
  scope :recent, -> { order(created_at: :desc) }
  scope :by_tag, ->(tag_name) { joins(:tags).where(tags: { name: tag_name }) }
  
  def self.popular
    joins(:likes).group('posts.id').having('COUNT(likes.id) > 5')
  end
  
  def excerpt(length: 100)
    content.truncate(length, separator: ' ')
  end
  
  def tag_list
    tags.pluck(:name).join(', ')
  end
  
  def tag_list=(tag_string)
    tag_names = tag_string.split(',').map(&:strip).reject(&:empty?)
    
    self.tags = tag_names.map do |name|
      Tag.find_or_create_by(name: name)
    end
  end
end

# app/models/comment.rb
class Comment < ApplicationRecord
  belongs_to :post
  belongs_to :user
  
  validates :content, presence: true, length: { minimum: 1, maximum: 1000 }
  validates :user_id, presence: true
  validates :post_id, presence: true
  
  default_scope -> { order(created_at: :asc) }
  
  def editable_by?(user)
    self.user == user || post.user == user
  end
end
```

### Advanced Associations
```ruby
# app/models/company.rb
class Company < ApplicationRecord
  has_many :users, dependent: :destroy
  has_many :projects, dependent: :destroy
  has_many :departments, dependent: :destroy
  
  # Nested associations
  has_many :active_users, -> { where(active: true) }, class_name: 'User'
  has_many :completed_projects, -> { where(status: 'completed') }, class_name: 'Project'
  
  # Polymorphic associations
  has_many :addresses, as: :addressable, dependent: :destroy
  
  # Through associations
  has_many :project_managers, through: :projects, source: :manager
  
  # Counter cache
  has_many :users, dependent: :destroy
  after_create :increment_user_count
  after_destroy :decrement_user_count
  
  def self.with_user_count
    left_joins(:users)
      .select('companies.*', 'COUNT(users.id) AS user_count')
      .group('companies.id')
  end
  
  private
  
  def increment_user_count
    update_column(:user_count, user_count + 1)
  end
  
  def decrement_user_count
    update_column(:user_count, [user_count - 1, 0].max)
  end
end

# app/models/address.rb
class Address < ApplicationRecord
  belongs_to :addressable, polymorphic: true
  
  validates :street, :city, :state, :zip_code, presence: true
  
  def full_address
    "#{street}, #{city}, #{state} #{zip_code}"
  end
end

# app/models/tagging.rb
class Tagging < ApplicationRecord
  belongs_to :tag
  belongs_to :taggable, polymorphic: true
  
  validates :tag_id, uniqueness: { scope: [:taggable_type, :taggable_id] }
end

# app/models/tag.rb
class Tag < ApplicationRecord
  has_many :taggings, dependent: :destroy
  has_many :posts, through: :taggings, source: :taggable, source_type: 'Post'
  
  validates :name, presence: true, uniqueness: true
  
  def self.popular
    joins(:taggings).group('tags.id').having('COUNT(taggings.id) > 5')
  end
end
```

## Advanced Queries

### Complex Scopes
```ruby
# app/models/user.rb (continued)
class User < ApplicationRecord
  # Complex scope with joins
  scope :with_posts, -> { includes(:posts) }
  scope :with_recent_posts, -> { includes(:posts).where(posts: { created_at: 1.month.ago.. }) }
  
  # Scope with subquery
  scope :with_comment_count, -> {
    select('users.*, COUNT(comments.id) AS comment_count')
      .left_joins(:posts => :comments)
      .group('users.id')
  }
  
  # Scope with window function (PostgreSQL)
  scope :ranked_by_posts, -> {
    select('users.*, ROW_NUMBER() OVER (ORDER BY COUNT(posts.id) DESC) as rank')
      .joins(:posts)
      .group('users.id')
  }
  
  # Dynamic scope
  def self.by_filters(filters = {})
    users = all
    
    users = users.where('age >= ?', filters[:min_age]) if filters[:min_age].present?
    users = users.where('age <= ?', filters[:max_age]) if filters[:max_age].present?
    users = users.where('active = ?', filters[:active]) if filters[:active].present?
    users = users.where('company_id = ?', filters[:company_id]) if filters[:company_id].present?
    
    users
  end
  
  # Custom query methods
  def self.find_with_stats(id)
    find_by_sql(<<-SQL
      SELECT u.*, COUNT(p.id) as post_count, COUNT(c.id) as comment_count
      FROM users u
      LEFT JOIN posts p ON p.user_id = u.id
      LEFT JOIN comments c ON c.user_id = u.id
      WHERE u.id = ?
      GROUP BY u.id
    SQL, id)
  end
  
  def self.active_users_with_stats
    find_by_sql(<<-SQL
      SELECT u.*, COUNT(p.id) as post_count, COUNT(c.id) as comment_count
      FROM users u
      INNER JOIN posts p ON p.user_id = u.id
      LEFT JOIN comments c ON c.user_id = u.id
      WHERE u.active = true
      GROUP BY u.id
      ORDER BY post_count DESC
    SQL)
  end
end
```

### Raw SQL Queries
```ruby
# app/models/concerns/queryable.rb
module Queryable
  extend ActiveSupport::Concern
  
  def self.execute_query(sql, params = [])
    connection.execute(sanitize_sql([sql, *params]))
  end
  
  def self.execute_query_with_result(sql, params = [])
    connection.select_all(sanitize_sql([sql, *params]))
  end
  
  def self.execute_pluck(sql, params = [])
    connection.select_rows(sanitize_sql([sql, *params]))
  end
  
  def self.execute_scalar(sql, params = [])
    connection.select_value(sanitize_sql([sql, *params]))
  end
end

# app/models/user.rb
class User < ApplicationRecord
  extend Queryable
  
  # Custom statistics query
  def self.user_statistics
    execute_query_with_result(<<-SQL
      SELECT 
        COUNT(*) as total_users,
        COUNT(CASE WHEN active THEN 1 END) as active_users,
        AVG(age) as average_age,
        MAX(age) as max_age,
        MIN(age) as min_age
      FROM users
    SQL
  ).first
  end
  
  # User activity report
  def self.activity_report(start_date, end_date)
    execute_query_with_result(<<-SQL
      SELECT 
        u.id,
        u.name,
        u.email,
        COUNT(p.id) as posts_created,
        COUNT(c.id) as comments_made,
        MAX(p.created_at) as last_post_date,
        MAX(c.created_at) as last_comment_date
      FROM users u
      LEFT JOIN posts p ON p.user_id = u.id AND p.created_at BETWEEN ? AND ?
      LEFT JOIN comments c ON c.user_id = u.id AND c.created_at BETWEEN ? AND ?
      GROUP BY u.id, u.name, u.email
      ORDER BY posts_created DESC, comments_made DESC
    SQL, start_date, end_date, start_date, end_date)
  end
  
  # Complex aggregation
  def self.department_performance
    execute_query_with_result(<<-SQL
      SELECT 
        d.name as department_name,
        COUNT(u.id) as employee_count,
        COUNT(p.id) as total_posts,
        AVG(CASE WHEN p.published THEN 1 ELSE 0 END) as avg_published_ratio,
        COUNT(DISTINCT DATE_TRUNC('month', p.created_at)) as active_months
      FROM departments d
      LEFT JOIN users u ON u.department_id = d.id
      LEFT JOIN posts p ON p.user_id = u.id
      GROUP BY d.id, d.name
      ORDER BY avg_published_ratio DESC
    SQL
  end
  
  # Time series data
  def self.registration_trends(days: 30)
    execute_query_with_result(<<-SQL
      SELECT 
        DATE(created_at) as date,
        COUNT(*) as registrations
      FROM users
      WHERE created_at >= ?
      GROUP BY DATE(created_at)
      ORDER BY date DESC
    SQL, days.days.ago.to_date)
  end
  
  # Hierarchical data (if using PostgreSQL)
  def self.user_hierarchy
    execute_query_with_result(<<-SQL
      WITH RECURSIVE user_tree AS (
        SELECT id, name, email, manager_id, 0 as level
        FROM users
        WHERE manager_id IS NULL
        
        UNION ALL
        
        SELECT u.id, u.name, u.email, u.manager_id, ut.level + 1
        FROM users u
        INNER JOIN user_tree ut ON u.manager_id = ut.id
      )
      SELECT * FROM user_tree ORDER BY level, name
    SQL
  end
end
```

### Database Functions and Stored Procedures
```ruby
# db/migrate/20231201000001_create_database_functions.rb
class CreateDatabaseFunctions < ActiveRecord::Migration[6.0]
  def up
    execute <<~SQL
      -- PostgreSQL function for full text search
      CREATE OR REPLACE FUNCTION search_posts(search_term TEXT)
      RETURNS TABLE(id INTEGER, title TEXT, content TEXT, rank REAL) AS $$
      BEGIN
        RETURN QUERY
        SELECT 
          p.id,
          p.title,
          p.content,
          ts_rank_cd(to_tsvector('english', p.title || ' ' || p.content), plainto_tsquery('english', search_term)) as rank
        FROM posts p
        WHERE 
          to_tsvector('english', p.title || ' ' || p.content) @@ plainto_tsquery('english', search_term)
        ORDER BY rank DESC;
      END;
      $$ LANGUAGE plpgsql;
      
      -- Function to calculate user statistics
      CREATE OR REPLACE FUNCTION calculate_user_stats(user_id INTEGER)
      RETURNS JSON AS $$
      DECLARE
        user_stats JSON;
        user_name TEXT;
        post_count INTEGER;
        comment_count INTEGER;
        last_post_date TIMESTAMP;
      BEGIN
        SELECT u.name INTO user_name FROM users u WHERE u.id = user_id;
        
        SELECT COUNT(*) INTO post_count 
        FROM posts p WHERE p.user_id = user_id;
        
        SELECT COUNT(*) INTO comment_count 
        FROM comments c WHERE c.user_id = user_id;
        
        SELECT MAX(created_at) INTO last_post_date 
        FROM posts p WHERE p.user_id = user_id;
        
        user_stats := json_build_object(
          'name', user_name,
          'post_count', post_count,
          'comment_count', comment_count,
          'last_post_date', COALESCE(last_post_date, '1970-01-01'::timestamp)
        );
        
        RETURN user_stats;
      END;
      $$ LANGUAGE plpgsql;
      
      -- Trigger function for audit logging
      CREATE OR REPLACE FUNCTION audit_trigger()
      RETURNS TRIGGER AS $$
      BEGIN
        INSERT INTO audit_logs (table_name, operation, record_id, old_values, new_values, created_at)
        VALUES (
          TG_TABLE_NAME,
          TG_OP,
          COALESCE(NEW.id, OLD.id),
          CASE 
            WHEN TG_OP = 'DELETE' THEN row_to_json(OLD)
            ELSE row_to_json(NEW)
          END,
          CASE 
            WHEN TG_OP = 'INSERT' THEN row_to_json(NEW)
            WHEN TG_OP = 'UPDATE' THEN row_to_json(NEW)
            ELSE NULL
          END,
          NOW()
        );
        
        RETURN COALESCE(NEW, OLD);
      END;
      $$ LANGUAGE plpgsql;
    SQL
  end
  
  def down
    execute <<~SQL
      DROP FUNCTION IF EXISTS search_posts(TEXT);
      DROP FUNCTION IF EXISTS calculate_user_stats(INTEGER);
      DROP FUNCTION IF EXISTS audit_trigger();
    SQL
  end
end

# app/models/concerns/database_functions.rb
module DatabaseFunctions
  extend ActiveSupport::Concern
  
  # Call PostgreSQL function
  def self.search_posts(search_term)
    connection.select_rows("SELECT * FROM search_posts(#{connection.quote(search_term)})")
  end
  
  # Call PostgreSQL function returning JSON
  def self.user_stats(user_id)
    result = connection.select_one("SELECT calculate_user_stats(#{user_id})")
    JSON.parse(result['calculate_user_stats'])
  end
  
  # Materialized view for performance
  def self.create_materialized_views
    connection.execute(<<~SQL
      CREATE MATERIALIZED VIEW IF NOT EXISTS user_stats_view AS
        SELECT 
          u.id,
          u.name,
          u.email,
          COUNT(p.id) as post_count,
          COUNT(c.id) as comment_count,
          MAX(p.created_at) as last_post_date
        FROM users u
        LEFT JOIN posts p ON p.user_id = u.id
        LEFT JOIN comments c ON c.user_id = u.id
        GROUP BY u.id, u.name, u.email;
      
      CREATE INDEX IF NOT EXISTS idx_user_stats_post_count ON user_stats_view(post_count);
      CREATE INDEX IF NOT EXISTS idx_user_stats_last_post ON user_stats_view(last_post_date);
    SQL
  end
  
  def self.refresh_materialized_views
    connection.execute("REFRESH MATERIALIZED VIEW CONCURRENTLY user_stats_view")
  end
end

# app/models/user.rb
class User < ApplicationRecord
  extend DatabaseFunctions
  
  def self.search_with_function(search_term)
    search_posts(search_term).map do |row|
      new(
        id: row[0],
        title: row[1],
        content: row[2],
        rank: row[3]
      )
    end
  end
  
  def self.stats_with_function(user_id)
    user_stats(user_id)
  end
end
```

## Database Migrations

### Advanced Migration Patterns
```ruby
# db/migrate/20231201000002_create_complex_tables.rb
class CreateComplexTables < ActiveRecord::Migration[6.0]
  def change
    # Users table with constraints
    create_table :users do |t|
      t.string :name, null: false, limit: 100
      t.string :email, null: false, index: { unique: true }
      t.integer :age, null: false
      t.boolean :active, default: true, null: false
      t.decimal :salary, precision: 10, scale: 2
      t.jsonb :preferences, default: {}
      t.jsonb :metadata, default: {}
      t.timestamps null: false
      
      t.index :active
      t.index :age
      t.index :created_at
      t.index :updated_at
    end
    
    # Companies table
    create_table :companies do |t|
      t.string :name, null: false
      t.string :industry
      t.text :description
      t.string :website
      t.string :phone
      t.jsonb :settings, default: {}
      t.timestamps null: false
      
      t.index :industry
    end
    
    # Departments table (many-to-many with users)
    create_table :departments do |t|
      t.string :name, null: false
      t.text :description
      t.references :company, null: false, foreign_key: true
      t.timestamps null: false
      
      t.index :company_id
    end
    
    # User-department join table
    create_table :user_departments do |t|
      t.references :user, null: false, foreign_key: true
      t.references :department, null: false, foreign_key: true
      t.string :role, default: 'member'
      t.timestamps null: false
      
      t.index [:user_id, :department_id], unique: true
    end
    
    # Posts table with full-text search
    enable_extension 'pg_trgm' if extension_enabled?('pg_trgm')
    
    create_table :posts do |t|
      t.string :title, null: false
      t.text :content, null: false
      t.boolean :published, default: false, null: false
      t.integer :user_id, null: false
      t.tsvector :search_vector
      t.jsonb :metadata, default: {}
      t.timestamps null: false
      
      t.index :user_id
      t.index :published
      t.index :created_at
      t.index :search_vector, using: :gin
      
      t.foreign_key :user_id, references: :users
    end
    
    # Comments table with hierarchical structure
    create_table :comments do |t|
      t.text :content, null: false
      t.references :user, null: false, foreign_key: true
      t.references :post, null: false, foreign_key: true
      t.references :parent, foreign_key: :comments
      t.integer :lft
      t.integer :rgt
      t.integer :depth, default: 0
      t.timestamps null: false
      
      t.index :user_id
      t.index :post_id
      t.index [:lft, :rgt]
      t.index :parent_id
    end
    
    # Audit log table
    create_table :audit_logs do |t|
      t.string :table_name, null: false
      t.string :operation, null: false
      t.integer :record_id
      t.jsonb :old_values
      t.jsonb :new_values
      t.datetime :created_at, null: false
      
      t.index [:table_name, :record_id]
      t.index :created_at
    end
    
    # Add foreign key constraints
    add_foreign_key :user_departments, :users, on_delete: :cascade
    add_foreign_key :user_departments, :departments, on_delete: :cascade
    add_foreign_key :comments, :comments, column: :parent_id
    
    # Add triggers for audit logging
    execute <<~SQL
      CREATE TRIGGER users_audit_trigger
        AFTER INSERT OR UPDATE OR DELETE ON users
        FOR EACH ROW EXECUTE FUNCTION audit_trigger();
      
      CREATE TRIGGER posts_audit_trigger
        AFTER INSERT OR UPDATE OR DELETE ON posts
        FOR EACH ROW EXECUTE FUNCTION audit_trigger();
      
      CREATE TRIGGER comments_audit_trigger
        AFTER INSERT OR UPDATE OR DELETE ON comments
        FOR EACH ROW EXECUTE FUNCTION audit_trigger();
    SQL
    
    # Add check constraints
    execute <<~SQL
      ALTER TABLE users ADD CONSTRAINT check_age 
        CHECK (age >= 0 AND age <= 150);
      
      ALTER TABLE posts ADD CONSTRAINT check_title_length 
        CHECK (char_length(title) >= 3 AND char_length(title) <= 200);
    SQL
  end
  
  def down
    drop_table :audit_logs
    drop_table :comments
    drop_table :posts
    drop_table :user_departments
    drop_table :departments
    drop_table :companies
    drop_table :users
    
    execute <<~SQL
      DROP TRIGGER IF EXISTS users_audit_trigger;
      DROP TRIGGER IF EXISTS posts_audit_trigger;
      DROP TRIGGER IF EXISTS comments_audit_trigger;
    SQL
  end
end
```

### Data Migration Scripts
```ruby
# db/migrate/20231201000003_migrate_legacy_data.rb
class MigrateLegacyData < ActiveRecord::Migration[6.0]
  def up
    # Create temporary table for legacy data
    create_table :legacy_users, temporary: true do |t|
      t.string :full_name
      t.string :email_address
      t.integer :years_old
      t.string :status
      t.text :notes
    end
    
    # Import legacy data from CSV or external source
    legacy_data = [
      ['John Doe', 'john@example.com', 30, 'active', 'Regular user'],
      ['Jane Smith', 'jane@example.com', 25, 'active', 'Regular user'],
      ['Bob Johnson', 'bob@example.com', 35, 'inactive', 'Former user']
    ]
    
    legacy_data.each do |name, email, age, status, notes|
      execute <<~SQL
        INSERT INTO legacy_users (full_name, email_address, years_old, status, notes)
        VALUES ('#{name}', '#{email}', #{age}, '#{status}', '#{notes}');
      SQL
    end
    
    # Migrate data to new structure
    execute <<~SQL
      INSERT INTO users (name, email, age, active, metadata, created_at, updated_at)
      SELECT 
        full_name,
        email_address,
        years_old,
        CASE WHEN status = 'active' THEN true ELSE false END,
        jsonb_build_object('legacy_status', status, 'legacy_notes', notes),
        NOW(),
        NOW()
      FROM legacy_users;
    SQL
    
    # Create user profiles for migrated users
    execute <<~SQL
      INSERT INTO profiles (user_id, bio, created_at, updated_at)
      SELECT 
        u.id,
        'Migrated from legacy system',
        NOW(),
        NOW()
      FROM users u
      WHERE NOT EXISTS (
        SELECT 1 FROM profiles p WHERE p.user_id = u.id
      );
    SQL
    
    # Drop temporary table
    drop_table :legacy_users
  end
  
  def down
    # Reverse migration if needed
    # This would typically involve moving data back to legacy format
  end
end

# db/migrate/20231201000004_optimize_database.rb
class OptimizeDatabase < ActiveRecord::Migration[6.0]
  def up
    # Create indexes for performance
    add_index :users, [:active, :created_at]
    add_index :posts, [:published, :created_at]
    add_index :comments, [:post_id, :created_at]
    
    # Create composite indexes
    add_index :posts, [:user_id, :published], name: 'index_posts_on_user_and_published'
    add_index :comments, [:user_id, :post_id], name: 'index_comments_on_user_and_post'
    
    # Create partial indexes
    execute <<~SQL
      CREATE INDEX CONCURRENTLY index_users_active 
        ON users (id) WHERE active = true;
      
      CREATE INDEX CONCURRENTLY index_posts_published 
        ON posts (id) WHERE published = true;
    SQL
    
    # Create covering indexes
    execute <<~SQL
      CREATE INDEX CONCURRENTLY index_posts_covering 
        ON posts (user_id, published, created_at, title, id);
    SQL
    
    # Create hash indexes for JSONB columns
    execute <<~SQL
      CREATE INDEX CONCURRENTLY index_users_preferences_gin 
        ON users USING gin (preferences);
      
      CREATE INDEX CONCURRENTLY index_posts_metadata_gin 
        ON posts USING gin (metadata);
    SQL
    
    # Create expression indexes
    execute <<~SQL
      CREATE INDEX CONCURRENTLY index_users_email_lower 
        ON users (lower(email));
      
      CREATE INDEX CONCURRENTLY index_posts_title_lower 
        ON posts (lower(title));
    SQL
    
    # Update table statistics
    execute <<~SQL
      ANALYZE users;
      ANALYZE posts;
      ANALYZE comments;
      ANALYZE companies;
      ANALYZE departments;
    SQL
  end
  
  def down
    # Drop indexes
    remove_index :users, [:active, :created_at]
    remove_index :posts, [:published, :created_at]
    remove_index :comments, [:post_id, :created_at]
    remove_index :posts, name: 'index_posts_on_user_and_published'
    remove_index :comments, name: 'index_comments_on_user_and_post'
    
    execute <<~SQL
      DROP INDEX CONCURRENTLY index_users_active;
      DROP INDEX CONCURRENTLY index_posts_published;
      DROP INDEX CONCURRENTLY index_posts_covering;
      DROP INDEX CONCURRENTLY index_users_preferences_gin;
      DROP INDEX CONCURRENTLY index_posts_metadata_gin;
      DROP INDEX CONCURRENTLY index_users_email_lower;
      DROP INDEX CONCURRENTLY index_posts_title_lower;
    SQL
  end
end
```

## Database Performance

### Query Optimization
```ruby
# app/models/concerns/query_optimizer.rb
module QueryOptimizer
  extend ActiveSupport::Concern
  
  # Batch processing for large datasets
  def self.process_in_batches(batch_size: 1000, &block)
    find_in_batches(batch_size: batch_size) do |batch|
      batch.each(&block)
    end
  end
  
  # Efficient pagination with cursor-based pagination
  def self.cursor_paginate(cursor: nil, limit: 20)
    query = all.order(:id)
    query = query.where('id > ?', cursor) if cursor
    query = query.limit(limit + 1)
    
    records = query.to_a
    next_cursor = records.length > limit ? records.last.id : nil
    
    {
      records: records.first(limit),
      next_cursor: next_cursor
    }
  end
  
  # Efficient counting for large tables
  def self.fast_count
    connection.select_value("SELECT COUNT(*) FROM #{table_name}")
  end
  
  # Fast existence check
  def self.exists_fast?(id)
    connection.select_value("SELECT 1 FROM #{table_name} WHERE id = ? LIMIT 1", id) == 1
  end
  
  # Bulk insert with UPSERT
  def self.bulk_upsert(records)
    return if records.empty?
    
    columns = records.first.attributes.keys
    values = records.map { |record| "('#{record.attributes.values.join("', '")}')" }
    
    execute <<~SQL
      INSERT INTO #{table_name} (#{columns.join(', ')})
      VALUES #{values.join(', ')}
      ON CONFLICT (id) DO UPDATE SET
        #{columns.map { |col| "#{col} = EXCLUDED.#{col}" }.join(', ')}
    SQL
  end
  
  # Bulk update with single query
  def self.bulk_update(updates)
    return if updates.empty?
    
    when_clause = updates.map { |id, attrs| "WHEN id = #{id} THEN #{attrs.map { |k, v| "#{k} = #{connection.quote(v)}" }.join(', ')}" }.join(' ')
    
    execute <<~SQL
      UPDATE #{table_name}
      SET #{updates.values.first.keys.map { |key| "#{key} = CASE #{when_clause} END" }.join(', ')}
      WHERE id IN (#{updates.keys.join(', ')})
    SQL
  end
  
  # Efficient delete with single query
  def self.bulk_delete(ids)
    return if ids.empty?
    
    where(id: ids).delete_all
  end
  
  # Materialized view for complex queries
  def self.create_materialized_view(view_name, query)
    execute <<~SQL
      DROP MATERIALIZED VIEW IF EXISTS #{view_name};
      
      CREATE MATERIALIZED VIEW #{view_name} AS
        #{query}
    SQL
  end
  
  # Refresh materialized view
  def self.refresh_materialized_view(view_name)
    execute "REFRESH MATERIALIZED VIEW #{view_name}"
  end
end

# app/models/user.rb
class User < ApplicationRecord
  extend QueryOptimizer
  
  # Batch processing example
  def self.send_welcome_emails_to_inactive_users
    where(active: false).process_in_batches do |batch|
      batch.each do |user|
        UserMailer.reactivation_email(user).deliver_later
      end
    end
  end
  
  # Cursor pagination example
  def self.cursor_paginated_users(cursor: nil, limit: 20)
    cursor_paginate(cursor: cursor, limit: limit)
  end
  
  # Bulk operations
  def self.bulk_update_last_login_time
    updates = Hash[
      1 => { last_login_at: Time.current },
      2 => { last_login_at: Time.current - 1.day },
      3 => { last_login_at: Time.current - 2.days }
    ]
    
    bulk_update(updates)
  end
  
  # Materialized view for user statistics
  def self.create_user_stats_view
    create_materialized_view(:user_stats_view, <<~SQL
      SELECT 
        u.id,
        u.name,
        u.email,
        u.active,
        COUNT(p.id) as post_count,
        COUNT(c.id) as comment_count,
        MAX(p.created_at) as last_post_date
      FROM users u
      LEFT JOIN posts p ON p.user_id = u.id
      LEFT JOIN comments c ON c.user_id = u.id
      GROUP BY u.id, u.name, u.email, u.active
    SQL
  end
end
```

### Connection Pooling
```ruby
# config/database.yml
production:
  adapter: postgresql
  encoding: unicode
  pool: 25
  host: db.example.com
  database: myapp_production
  username: myapp_user
  password: <%= ENV['DATABASE_PASSWORD'] %>
  
  # Connection pool settings
  checkout_timeout: 5
  reaping_frequency: 10
  max_threads: 25
  min_threads: 5
  idle_timeout: 300

# config/environments/production.rb
Rails.application.configure do
  config.active_record.database_selector = 51 # 50% of connections
  config.active_record.lock_timeout = 5
  config.active_record.log_level = :warn
end

# config/initializers/database_connection_pool.rb
if Rails.env.production?
  # Configure connection pool for production
  Rails.application.configure do
    config.after_initialize do
      # Warm up connection pool
      ActiveRecord::Base.connection_pool.with_connection do |connection|
        connection.execute("SELECT 1")
      end
    end
  end
end
```

## Database Testing

### Database Testing Strategies
```ruby
# spec/models/user_spec.rb
RSpec.describe User, type: :model do
  # Basic model tests
  it { should validate_presence_of(:name) }
  it { should validate_length_of(:name, minimum: 2, maximum: 50) }
  it { should validate_presence_of(:email) }
  it { should validate_uniqueness_of(:email).case_insensitive }
  it { should validate_numericality_of(:age).is_greater_than_or_equal_to(0) }
  it { should validate_numericality_of(:age).is_less_than(150) }
  
  # Association tests
  it { should have_many(:posts) }
  it { should have_many(:comments) }
  it { should have_one(:profile) }
  it { should belong_to(:company) }
  
  # Custom validation tests
  it "should accept valid email format" do
    user = build(:user, email: 'test@example.com')
    expect(user).to be_valid
  end
  
  it "should reject invalid email format" do
    user = build(:user, email: 'invalid_email')
    expect(user).not_to be_valid
  end
  
  # Scope tests
  describe ".active" do
    it "returns only active users" do
      active_user = create(:user, active: true)
      inactive_user = create(:user, active: false)
      
      expect(User.active).to include(active_user)
      expect(User.active).not_to include(inactive_user)
    end
  end
  
  describe ".by_age" do
    it "returns users within age range" do
      young_user = create(:user, age: 25)
      middle_user = create(:user, age: 35)
      old_user = create(:user, age: 45)
      
      result = User.by_age(30, 40)
      expect(result).to include(middle_user)
      expect(result).not_to include(young_user, old_user)
    end
  end
  
  # Method tests
  describe "#full_name" do
    it "returns name when no profile exists" do
      user = build(:user, name: 'John')
      expect(user.full_name).to eq('John')
    end
    
    it "returns full name when profile exists" do
      user = create(:user, name: 'John')
      create(:profile, user: user, last_name: 'Doe')
      expect(user.full_name).to eq('John Doe')
    end
  end
  
  describe "#admin?" do
    it "returns true for admin role" do
      user = build(:user, role: 'admin')
      expect(user.admin?).to be true
    end
    
    it "returns false for regular role" do
      user = build(:user, role: 'user')
      expect(user.admin?).to be false
    end
  end
  
  # Callback tests
  describe "callbacks" do
    it "normalizes email before saving" do
      user = create(:user, email: 'JOHN@EXAMPLE.COM')
      expect(user.email).to eq('john@example.com')
    end
    
    it "sends welcome email after creation" do
      expect(UserMailer).to receive(:welcome_email).with(kind_of(User))
      
      create(:user)
    end
  end
  
  # Database interaction tests
  describe "database operations" do
    it "creates user successfully" do
      user = User.create!(name: 'John', email: 'john@example.com', age: 30)
      
      expect(user.persisted?).to be true
      expect(user.name).to eq('John')
      expect(user.email).to eq('john@example.com')
      expect(user.age).to eq(30)
    end
    
    it "updates user successfully" do
      user = create(:user, name: 'John')
      user.update!(name: 'Jane')
      
      expect(user.reload.name).to eq('Jane')
    end
    
    it "deletes user successfully" do
      user = create(:user, name: 'John')
      user.destroy!
      
      expect(User.exists?(user.id)).to be false
    end
  end
end

# spec/requests/api/users_spec.rb
RSpec.describe "Users API", type: :request do
  describe "GET /api/v1/users" do
    it "returns a list of users" do
      create_list(:user, 3)
      
      get '/api/v1/users'
      
      expect(response).to have_http_status(200)
      expect(response.content_type).to eq('application/json; charset=utf-8')
      
      json_response = JSON.parse(response.body)
      expect(json_response.length).to eq(3)
    end
    
    it "supports pagination" do
      create_list(:user, 25)
      
      get '/api/v1/users?page=1&per_page=10'
      
      expect(response).to have_http_status(200)
      
      json_response = JSON.parse(response.body)
      expect(json_response['data'].length).to eq(10)
      expect(json_response['pagination']['total_pages']).to eq(3)
    end
  end
  
  describe "POST /api/v1/users" do
    context "with valid parameters" do
      it "creates a new user" do
        user_params = {
          name: 'John Doe',
          email: 'john@example.com',
          age: 30
        }
        
        post '/api/v1/users', params: { user: user_params }
        
        expect(response).to have_http_status(201)
        expect(response.content_type).to eq('application/json; charset=utf-8')
        
        json_response = JSON.parse(response.body)
        expect(json_response['name']).to eq('John Doe')
        expect(json_response['email']).to eq('john@example.com')
        expect(json_response['age']).to eq(30)
      end
    end
    
    context "with invalid parameters" do
      it "returns validation errors" do
        user_params = {
          name: '',
          email: 'invalid_email',
          age: -1
        }
        
        post '/api/v1/users', params: { user: user_params }
        
        expect(response).to have_http_status(422)
        expect(response.content_type).to eq('application/json; charset=utf-8')
        
        json_response = JSON.parse(response.body)
        expect(json_response['errors']).to include("Name can't be blank")
        expect(json_response['errors']).to include("Email is invalid")
        expect(json_response['errors']).to include("Age must be greater than or equal to 0")
      end
    end
  end
end

# spec/support/database_cleaner.rb
RSpec.configure do |config|
  config.use_transactional_fixtures = false
  
  config.before(:suite) do
    DatabaseCleaner.clean_with(:truncation)
  end
  
  config.before(:each) do
    DatabaseCleaner.strategy = :transaction
  end
  
  config.after(:each) do
    DatabaseCleaner.clean
  end
end
```

## Best Practices

### Database Design Best Practices
```ruby
# Good: Use proper data types
class User < ApplicationRecord
  # Use appropriate column types
  # - string for text
  # - integer for whole numbers
  # - decimal for financial data
  # - boolean for flags
  # - jsonb for structured data
  # - timestamp for dates
  # - tsvector for full-text search
end

# Good: Use proper indexes
class AddPerformanceIndexes < ActiveRecord::Migration[6.0]
  def change
    # Index foreign keys
    add_index :posts, :user_id
    
    # Index frequently queried columns
    add_index :users, :email
    add_index :posts, :published
    
    # Composite indexes for common query patterns
    add_index :posts, [:user_id, :published]
    add_index :comments, [:post_id, :created_at]
    
    # Partial indexes for filtered queries
    execute <<~SQL
      CREATE INDEX CONCURRENTLY index_posts_published 
        ON posts (id) WHERE published = true;
    SQL
  end
end

# Good: Use constraints
class AddConstraints < ActiveRecord::Migration[6.0]
  def change
    # Check constraints
    execute <<~SQL
      ALTER TABLE users ADD CONSTRAINT check_age 
        CHECK (age >= 0 AND age <= 150);
      
      ALTER TABLE posts ADD CONSTRAINT check_title_length 
        CHECK (char_length(title) >= 3 AND char_length(title) <= 200);
    SQL
    
    # Foreign key constraints
    add_foreign_key :posts, :users, on_delete: :cascade
    add_foreign_key :comments, :posts, on_delete: :cascade
    
    # Unique constraints
    add_index :users, :email, unique: true
  end
end

# Good: Use proper naming conventions
class CreateTables < ActiveRecord::Migration[6.0]
  def change
    # Use snake_case for table names
    create_table :user_profiles do |t|
      # Use snake_case for column names
      t.references :user, null: false, foreign_key: true
      t.string :first_name
      t.string :last_name
      t.date :birth_date
      t.text :bio
      t.timestamps null: false
    end
    
    # Use descriptive names for indexes
    add_index :user_profiles, :user_id
    add_index :user_profiles, :birth_date
  end
end
```

### Performance Best Practices
```ruby
# Good: Use includes to prevent N+1 queries
class UsersController < ApplicationController
  def index
    @users = User.includes(:profile, :posts).active.recent
  end
end

# Good: Use select to limit data transfer
class UsersController < ApplicationController
  def index
    @users = User.select(:id, :name, :email, :active)
                    .includes(:profile)
                    .active
  end
end

# Good: Use pluck for single columns
class UsersController < ApplicationController
  def index
    user_ids = User.active.pluck(:id)
    @users = User.where(id: user_ids).includes(:profile)
  end
end

# Good: Use find_each for large datasets
class DataProcessor
  def self.process_all_users
    User.find_each(batch_size: 1000) do |user|
      process_user(user)
    end
  end
  
  private
  
  def self.process_user(user)
    # Processing logic
  end
end

# Good: Use bulk operations
class BulkOperations
  def self.bulk_create_users(users_data)
    User.insert_all(users_data)
  end
  
  def self.bulk_update_users(updates)
    updates.each do |id, attributes|
      User.where(id: id).update_all(attributes)
    end
  end
end
```

## Common Pitfalls

### Database Pitfalls
```ruby
# Pitfall: N+1 queries
# Bad: Loading associations in loop
@posts.each do |post|
  puts post.user.name  # N+1 query
  puts post.comments.count  # N+1 query
end

# Solution: Eager loading
@posts.includes(:user, :comments).each do |post|
  puts post.user.name
  puts post.comments.count
end

# Pitfall: Not using transactions
# Bad: Multiple database operations without transaction
user = User.create!(name: 'John')
user.update!(name: 'Jane')
Post.create!(user: user, title: 'Test')

# Solution: Use transactions
User.transaction do
  user = User.create!(name: 'John')
  user.update!(name: 'Jane')
  Post.create!(user: user, title: 'Test')
end

# Pitfall: Not handling database errors
# Bad: No error handling
user = User.find(params[:id])
user.update!(name: params[:name])

# Solution: Proper error handling
begin
  user = User.find(params[:id])
  user.update!(name: params[:name])
rescue ActiveRecord::RecordNotFound
  render json: { error: 'User not found' }, status: :not_found
rescue ActiveRecord::RecordInvalid => e
  render json: { error: e.message }, status: :unprocessable_entity
end

# Pitfall: Not using proper indexes
# Bad: No indexes on frequently queried columns
class User < ApplicationRecord
  # No indexes defined
end

# Solution: Add appropriate indexes
class AddIndexes < ActiveRecord::Migration[6.0]
  def change
    add_index :users, :email
    add_index :users, :active
    add_index :users, [:active, :created_at]
  end
end

# Pitfall: Using too many database connections
# Bad: Creating too many connections
1000.times do
  User.find(rand(1..1000))  # Each query uses a new connection
end

# Solution: Use connection pooling or batch operations
User.where(id: 1..1000).find_in_batches(batch_size: 100) do |batch|
  batch.each { |user| process_user(user) }
end
```

## Summary

Ruby database programming provides:

**ActiveRecord Basics:**
- Model definition and validations
- Associations and relationships
- Callbacks and observers
- Scopes and queries
- Migration management

**Advanced Queries:**
- Complex scopes and custom queries
- Raw SQL and database functions
- Materialized views
- Window functions and CTEs
- Full-text search

**Database Migrations:**
- Table creation and modification
- Constraint management
- Index optimization
- Data migration scripts
- Schema management

**Database Performance:**
- Query optimization techniques
- Connection pooling
- Bulk operations
- Materialized views
- Index strategies

**Database Testing:**
- Model testing with RSpec
- API testing
- Database cleaner setup
- Transaction testing
- Performance testing

**Best Practices:**
- Proper data type selection
- Index optimization
- Constraint usage
- Naming conventions
- Transaction management

**Common Pitfalls:**
- N+1 query problems
- Missing transaction handling
- Inadequate indexing
- Connection pool issues
- Error handling gaps

Ruby's database programming ecosystem provides powerful tools for building data-driven applications with proper architecture, performance optimization, and maintainable code when following established best practices.
