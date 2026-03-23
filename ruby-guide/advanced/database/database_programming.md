# Database Programming in Ruby

## Overview

Ruby provides excellent support for database programming through various gems and libraries. This guide covers database concepts, ORM usage, SQL operations, and best practices for database-driven applications.

## Database Fundamentals

### Relational Databases

```ruby
# Basic SQL concepts
# Tables, rows, columns, primary keys, foreign keys
# Normalization, indexing, transactions
# ACID properties

# Example database schema for a blog application
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title VARCHAR(255) NOT NULL,
  content TEXT,
  user_id INTEGER,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### NoSQL Databases

```ruby
# Document databases (MongoDB)
# Key-value stores (Redis)
# Graph databases (Neo4j)
# Column-family stores (Cassandra)
```

## ActiveRecord ORM

### Database Setup and Configuration

```ruby
# Gemfile
gem 'activerecord'
gem 'sqlite3'  # or 'pg' for PostgreSQL, 'mysql2' for MySQL

# config/database.yml
development:
  adapter: sqlite3
  database: db/development.sqlite3

test:
  adapter: sqlite3
  database: db/test.sqlite3

production:
  adapter: postgresql
  database: myapp_production
  username: <%= ENV['DATABASE_USERNAME'] %>
  password: <%= ENV['DATABASE_PASSWORD'] %>
  host: <%= ENV['DATABASE_HOST'] %>

# config/application.rb
require 'active_record'

ActiveRecord::Base.establish_connection(
  adapter: 'sqlite3',
  database: 'db/development.sqlite3'
)
```

### Model Definitions

```ruby
class User < ApplicationRecord
  # Validations
  validates :name, presence: true, length: { minimum: 2, maximum: 100 }
  validates :email, presence: true, uniqueness: true, format: { with: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i }
  
  # Associations
  has_many :posts, dependent: :destroy
  has_many :comments, through: :posts
  has_many :likes, dependent: :destroy
  
  # Callbacks
  before_save :normalize_email
  after_create :send_welcome_email
  
  # Scopes
  scope :active, -> { where(active: true) }
  scope :recent, -> { order(created_at: :desc) }
  
  # Class methods
  def self.search(query)
    where("name ILIKE ? OR email ILIKE ?", "%#{query}%", "%#{query}%")
  end
  
  # Instance methods
  def full_name
    name
  end
  
  def post_count
    posts.count
  end
  
  private
  
  def normalize_email
    self.email = email.downcase.strip
  end
  
  def send_welcome_email
    UserMailer.welcome(self).deliver_later
  end
end

class Post < ApplicationRecord
  belongs_to :user
  has_many :comments, dependent: :destroy
  has_many :likes, dependent: :destroy
  has_many :tags, through: :post_tags
  
  validates :title, presence: true, length: { minimum: 5, maximum: 255 }
  validates :content, presence: true
  
  scope :published, -> { where(published: true) }
  scope :recent, -> { order(created_at: :desc) }
  scope :by_user, ->(user) { where(user: user) }
  
  def word_count
    content.split(/\s+/).length
  end
  
  def summary(length: 100)
    content.length > length ? "#{content[0...length]}..." : content
  end
  
  def like_count
    likes.count
  end
end

class Comment < ApplicationRecord
  belongs_to :post
  belongs_to :user
  
  validates :content, presence: true, length: { minimum: 10 }
  
  def author_name
    user.full_name
  end
end
```

### Migrations

```ruby
# Create users table
class CreateUsers < ActiveRecord::Migration[7.0]
  def change
    create_table :users do |t|
      t.string :name, null: false
      t.string :email, null: false
      t.boolean :active, default: true
      t.timestamps
    end
    
    add_index :users, :email, unique: true
  end
end

# Add columns to existing table
class AddProfileToUsers < ActiveRecord::Migration[7.0]
  def change
    add_column :users, :bio, :text
    add_column :users, :avatar_url, :string
    add_column :users, :birth_date, :date
  end
end

# Create join table for many-to-many relationship
class CreatePostTags < ActiveRecord::Migration[7.0]
  def change
    create_table :post_tags do |t|
      t.references :post, null: false, foreign_key: true
      t.references :tag, null: false, foreign_key: true
      t.timestamps
    end
    
    add_index :post_tags, [:post_id, :tag_id], unique: true
  end
end
```

## Database Operations

### CRUD Operations

```ruby
# Create
user = User.create(name: "John Doe", email: "john@example.com")
user = User.new(name: "Jane Smith", email: "jane@example.com")
user.save

# Read
users = User.all
user = User.find(1)
user = User.find_by(email: "john@example.com")
users = User.where(active: true)
users = User.where("created_at > ?", 1.week.ago)

# Update
user = User.find(1)
user.update(name: "John Updated")
user.update_columns(active: true, updated_at: Time.now)

# Delete
user = User.find(1)
user.destroy
User.where(active: false).delete_all
```

### Advanced Queries

```ruby
# Complex where conditions
User.where("name LIKE ? AND email LIKE ?", "%John%", "%example%")
User.where(created_at: 1.month.ago..Time.now)
User.where(age: 18..65)

# Joins
Post.joins(:user).where(users: { active: true })
Post.joins("LEFT JOIN users ON posts.user_id = users.id")

# Subqueries
Post.where(user_id: User.where(active: true))
Post.where("id IN (SELECT post_id FROM likes WHERE created_at > ?)", 1.day.ago)

# Aggregations
User.count
User.maximum(:age)
User.minimum(:age)
User.average(:age)
User.group(:active).count
Post.group(:user_id).having("COUNT(*) > 5")

# Order and limit
User.order(:name)
User.order(created_at: :desc)
User.limit(10).offset(20)
```

### Transactions

```ruby
# Basic transaction
ActiveRecord::Base.transaction do
  user = User.create(name: "John", email: "john@example.com")
  Post.create(title: "First Post", content: "Content", user: user)
end

# Transaction with rollback
ActiveRecord::Base.transaction do
  user = User.create(name: "Jane", email: "jane@example.com")
  
  if user.valid?
    # Something that might fail
    Post.create(title: "Second Post", content: "Content", user: user)
  else
    raise ActiveRecord::Rollback
  end
end

# Nested transactions
ActiveRecord::Base.transaction(requires_new: true) do
  # This creates a savepoint
  User.create(name: "Nested User", email: "nested@example.com")
end
```

## Database Connections

### Connection Management

```ruby
# Multiple database connections
class PrimaryDatabase < ActiveRecord::Base
  establish_connection(
    adapter: 'postgresql',
    database: 'primary_db',
    username: 'user',
    password: 'password'
  )
end

class AnalyticsDatabase < ActiveRecord::Base
  establish_connection(
    adapter: 'postgresql',
    database: 'analytics_db',
    username: 'user',
    password: 'password'
  )
end

# Connection pooling
class ApplicationRecord < ActiveRecord::Base
  self.abstract_class = true
  
  # Configure connection pool
  establish_connection(
    adapter: 'postgresql',
    database: 'myapp',
    pool: 5,
    timeout: 5000
  )
end
```

### Database Sharding

```ruby
# Simple sharding implementation
class ShardedModel < ActiveRecord::Base
  self.abstract_class = true
  
  def self.shard_by_user_id(user_id)
    shard_id = user_id % 2
    connection_name = "shard_#{shard_id}"
    
    establish_connection(connection_name)
  end
  
  def self.find_by_user_id(user_id)
    shard_by_user_id(user_id)
    where(user_id: user_id).first
  end
end

class User < ShardedModel
  def self.find(id)
    shard_by_user_id(id)
    super(id)
  end
end
```

## Database Performance

### Query Optimization

```ruby
# Eager loading to avoid N+1 queries
users = User.includes(:posts, :comments).all
users.each { |user| puts user.posts.count }

# Select specific columns
users = User.select(:id, :name, :email).all

# Pluck for single values
emails = User.pluck(:email)
names_and_emails = User.pluck(:name, :email)

# Find in batches for large datasets
User.find_each(batch_size: 1000) do |user|
  process_user(user)
end

# Find in batches with processing
User.find_in_batches(batch_size: 1000) do |users|
  process_users_batch(users)
end

# Explain queries
User.where(active: true).explain
```

### Indexing Strategies

```ruby
# Add indexes in migrations
add_index :users, :email, unique: true
add_index :posts, [:user_id, :created_at]
add_index :posts, :title
add_index :users, :name

# Composite indexes for complex queries
add_index :posts, [:user_id, :published, :created_at]

# Partial indexes
add_index :users, :email, unique: true, where: "active = true"

# Expression indexes
add_index :users, "LOWER(email)", unique: true
```

### Connection Pooling

```ruby
# Configure connection pool
ActiveRecord::Base.establish_connection(
  adapter: 'postgresql',
  database: 'myapp',
  pool: 10,           # Number of connections
  timeout: 5000,      # Timeout in seconds
  reaping_frequency: 10
)

# Check connection pool status
ActiveRecord::Base.connection_pool.stat
```

## NoSQL Databases

### MongoDB with Mongoid

```ruby
# Gemfile
gem 'mongoid'

# config/mongoid.yml
development:
  clients:
    default:
      database: myapp_development
      hosts:
        - localhost:27017

class User
  include Mongoid::Document
  include Mongoid::Timestamps
  
  field :name, type: String
  field :email, type: String
  field :age, type: Integer
  field :active, type: Boolean, default: true
  
  validates :name, presence: true
  validates :email, presence: true, uniqueness: true
  
  embeds_many :addresses
  
  has_many :posts
  
  scope :active, -> { where(active: true) }
  scope :by_age, ->(range) { where(age: range) }
  
  index({ email: 1 }, { unique: true })
  
  def full_name
    name
  end
end

class Address
  include Mongoid::Document
  
  field :street, type: String
  field :city, type: String
  field :state, type: String
  field :zip, type: String
  
  embedded_in :user
end
```

### Redis for Caching

```ruby
# Gemfile
gem 'redis'

# Redis connection
require 'redis'
redis = Redis.new(host: 'localhost', port: 6379, db: 0)

# Basic operations
redis.set('key', 'value')
value = redis.get('key')
redis.del('key')

# Hash operations
redis.hset('user:1', 'name', 'John', 'email', 'john@example.com')
name = redis.hget('user:1', 'name')
redis.hgetall('user:1')

# List operations
redis.lpush('queue', 'task1', 'task2', 'task3')
task = redis.rpop('queue')

# Set operations
redis.sadd('tags', 'ruby', 'programming', 'web')
redis.smembers('tags')
redis.sismember('tags', 'ruby')

# Expiration
redis.setex('session:123', 3600, 'user_data')
redis.expire('key', 60)

# Caching with Redis
class CacheService
  def initialize(redis)
    @redis = redis
  end
  
  def get(key)
    @redis.get(key)
  end
  
  def set(key, value, ttl = 3600)
    @redis.setex(key, ttl, value)
  end
  
  def delete(key)
    @redis.del(key)
  end
  
  def fetch(key, ttl = 3600)
    value = get(key)
    return value if value
    
    value = yield
    set(key, value, ttl)
    value
  end
end

cache = CacheService.new(redis)
user_data = cache.fetch("user:1") do
  User.find(1).to_json
end
```

## Database Testing

### Testing with Fixtures

```ruby
# test/fixtures/users.yml
john:
  name: John Doe
  email: john@example.com
  active: true

jane:
  name: Jane Smith
  email: jane@example.com
  active: false

# test/models/user_test.rb
require 'test_helper'

class UserTest < ActiveSupport::TestCase
  fixtures :users
  
  test "user is valid with valid attributes" do
    user = users(:john)
    assert user.valid?
  end
  
  test "user requires name" do
    user = User.new(email: "test@example.com")
    assert_not user.valid?
    assert_includes user.errors[:name], "can't be blank"
  end
  
  test "user requires email" do
    user = User.new(name: "Test")
    assert_not user.valid?
    assert_includes user.errors[:email], "can't be blank"
  end
  
  test "user email must be unique" do
    user = User.new(name: "Test", email: users(:john).email)
    assert_not user.valid?
    assert_includes user.errors[:email], "has already been taken"
  end
end
```

### Testing with Factories

```ruby
# test/factories/users.rb
FactoryBot.define do
  factory :user do
    name { "John Doe" }
    sequence(:email) { |n| "user#{n}@example.com" }
    active { true }
    
    trait :inactive do
      active { false }
    end
    
    trait :with_posts do
      after(:create) do |user|
        create_list(:post, 3, user: user)
      end
    end
  end
  
  factory :post do
    title { "Sample Post" }
    content { "This is a sample post content." }
    association :user
    
    trait :published do
      published { true }
    end
  end
end

# test/models/user_test.rb
require 'test_helper'

class UserTest < ActiveSupport::TestCase
  test "factory creates valid user" do
    user = create(:user)
    assert user.valid?
  end
  
  test "inactive user factory" do
    user = create(:user, :inactive)
    assert_not user.active?
  end
  
  test "user with posts association" do
    user = create(:user, :with_posts)
    assert_equal 3, user.posts.count
  end
end
```

## Database Migrations

### Migration Best Practices

```ruby
# Use reversible migrations
class AddProfileToUsers < ActiveRecord::Migration[7.0]
  def change
    add_column :users, :bio, :text
    add_column :users, :avatar_url, :string
    add_column :users, :birth_date, :date
    
    # Add indexes
    add_index :users, :birth_date
  end
end

# Use up/down for complex migrations
class ComplexMigration < ActiveRecord::Migration[7.0]
  def up
    create_table :new_table do |t|
      t.string :name
      t.timestamps
    end
    
    # Migrate data from old table
    execute <<-SQL
      INSERT INTO new_table (name, created_at, updated_at)
      SELECT name, created_at, updated_at FROM old_table
    SQL
    
    drop_table :old_table
    rename_table :new_table, :old_table
  end
  
  def down
    # Revert the migration
    create_table :old_table do |t|
      t.string :name
      t.timestamps
    end
    
    execute <<-SQL
      INSERT INTO old_table (name, created_at, updated_at)
      SELECT name, created_at, updated_at FROM new_table
    SQL
    
    drop_table :new_table
  end
end
```

### Data Migrations

```ruby
# Migration for data transformation
class NormalizeEmails < ActiveRecord::Migration[7.0]
  def up
    User.find_each do |user|
      user.update_column(:email, user.email.downcase.strip)
    end
  end
  
  def down
    # Can't easily revert data migrations
    raise ActiveRecord::IrreversibleMigration
  end
end
```

## Best Practices

### 1. Use Proper Indexing

```ruby
# Index frequently queried columns
add_index :users, :email, unique: true
add_index :posts, :user_id
add_index :posts, :created_at

# Composite indexes for multi-column queries
add_index :posts, [:user_id, :published, :created_at]
```

### 2. Avoid N+1 Queries

```ruby
# Bad: N+1 queries
users = User.all
users.each { |user| puts user.posts.count }

# Good: Eager loading
users = User.includes(:posts).all
users.each { |user| puts user.posts.count }
```

### 3. Use Transactions

```ruby
# Always wrap related operations in transactions
ActiveRecord::Base.transaction do
  user = User.create!(name: "John", email: "john@example.com")
  Post.create!(title: "First Post", user: user)
end
```

### 4. Handle Database Errors

```ruby
begin
  User.create!(name: "John", email: "john@example.com")
rescue ActiveRecord::RecordInvalid => e
  puts "Validation failed: #{e.message}"
rescue ActiveRecord::RecordNotUnique => e
  puts "Duplicate record: #{e.message}"
rescue => e
  puts "Database error: #{e.message}"
end
```

## Practice Exercises

### Exercise 1: Blog Database
Create a complete blog database with:
- User management system
- Post and comment system
- Tag system
- Search functionality
- Pagination

### Exercise 2: E-commerce Database
Build an e-commerce database with:
- Product catalog
- Order management
- Inventory tracking
- Customer management
- Sales reporting

### Exercise 3: Analytics Database
Design an analytics database with:
- Event tracking
- Aggregation tables
- Time-series data
- Performance optimization
- Data archiving

### Exercise 4: Multi-tenant Application
Create a multi-tenant system with:
- Tenant isolation
- Shared and tenant-specific data
- Database sharding
- Performance monitoring
- Backup strategies

---

**Ready to explore more advanced Ruby topics? Let's continue! 🗄️**
