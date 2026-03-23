# Active Record Pattern in Ruby
# Comprehensive guide to the Active Record pattern and implementations

## 🗄️ Active Record Fundamentals

### 1. Active Record Pattern Concepts

Core Active Record pattern principles:

```ruby
class ActiveRecordPattern
  def self.explain_concepts
    puts "Active Record Pattern Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Active Record Pattern",
        description: "An object that wraps a row in a database table or view",
        characteristics: ["Encapsulates database access", "Adds domain logic", "Maps objects to tables"],
        responsibilities: ["CRUD operations", "Data validation", "Business logic"]
      },
      {
        concept: "Object-Relational Mapping (ORM)",
        description: "Technique for converting data between incompatible type systems",
        benefits: ["Reduced SQL writing", "Type safety", "Database independence"],
        challenges: ["Performance overhead", "Complex queries", "N+1 problem"]
      },
      {
        concept: "Convention over Configuration",
        description: "Sensible defaults reduce configuration needs",
        examples: ["Table naming", "Column mapping", "Primary key conventions"],
        benefits: ["Less boilerplate", "Faster development", "Consistent patterns"]
      },
      {
        concept: "Data Mapper vs Active Record",
        description: "Different approaches to object-relational mapping",
        data_mapper: ["Separate mapping layer", "Domain objects independent", "More complex"],
        active_record: ["Objects contain mapping", "Simpler implementation", "Tighter coupling"]
      },
      {
        concept: "Identity Map",
        description: "Ensures each object is loaded only once per session",
        purpose: ["Prevent duplicate objects", "Maintain identity", "Performance optimization"],
        implementation: ["Object cache", "Session-based", "Automatic management"]
      },
      {
        concept: "Unit of Work",
        description: "Manages business transactions and object persistence",
        responsibilities: ["Transaction management", "Change tracking", "Batch operations"],
        benefits: ["Atomic operations", "Consistency", "Performance"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Characteristics: #{concept[:characteristics].join(', ')}" if concept[:characteristics]
      puts "  Responsibilities: #{concept[:responsibilities].join(', ')}" if concept[:responsibilities]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Purpose: #{concept[:purpose].join(', ')}" if concept[:purpose]
      puts "  Implementation: #{concept[:implementation].join(', ')}" if concept[:implementation]
      puts "  Data Mapper: #{concept[:data_mapper].join(', ')}" if concept[:data_mapper]
      puts "  Active Record: #{concept[:active_record].join(', ')}" if concept[:active_record]
      puts
    end
  end
  
  def self.pattern_principles
    puts "\nActive Record Pattern Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Single Responsibility",
        description: "Each class has one responsibility",
        application: "Model classes focus on business logic and data access",
        benefits: ["Maintainability", "Testability", "Clarity"]
      },
      {
        principle: "Don't Repeat Yourself (DRY)",
        description: "Avoid duplication of code and logic",
        application: "Shared validations, scopes, and associations",
        benefits: ["Consistency", "Maintainability", "Reduced bugs"]
      },
      {
        principle: "Convention over Configuration",
        description: "Use sensible defaults to reduce configuration",
        application: "Automatic table/column naming, primary key conventions",
        benefits: ["Faster development", "Consistency", "Less boilerplate"]
      },
      {
        principle: "Fat Models, Skinny Controllers",
        description: "Place business logic in models, not controllers",
        application: "Validations, callbacks, and business rules in models",
        benefits: ["Reusability", "Testability", "Separation of concerns"]
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Application: #{principle[:application]}"
      puts "  Benefits: #{principle[:benefits].join(', ')}"
      puts
    end
  end
  
  # Run Active Record fundamentals
  explain_concepts
  pattern_principles
end
```

### 2. Basic Active Record Implementation

Core Active Record functionality:

```ruby
class BaseActiveRecord
  @@connection = nil
  @@table_name = nil
  @@primary_key = 'id'
  
  class << self
    attr_accessor :table_name, :primary_key
    
    def connection
      @@connection ||= DatabaseConnection.new
    end
    
    def table_name
      @table_name ||= name.downcase.pluralize
    end
    
    def primary_key
      @primary_key ||= 'id'
    end
    
    def find(id)
      result = connection.execute("SELECT * FROM #{table_name} WHERE #{primary_key} = ?", [id])
      return nil if result.empty?
      
      new_from_hash(result.first)
    end
    
    def all
      results = connection.execute("SELECT * FROM #{table_name}")
      results.map { |row| new_from_hash(row) }
    end
    
    def where(conditions)
      where_clause, *params = build_where_clause(conditions)
      results = connection.execute("SELECT * FROM #{table_name} WHERE #{where_clause}", params)
      results.map { |row| new_from_hash(row) }
    end
    
    def create(attributes)
      instance = new(attributes)
      instance.save
      instance
    end
    
    def new_from_hash(attributes)
      instance = new
      attributes.each do |key, value|
        instance.instance_variable_set("@#{key}", value)
      end
      instance.instance_variable_set(:@new_record, false)
      instance
    end
    
    private
    
    def build_where_clause(conditions)
      case conditions
      when String
        [conditions]
      when Hash
        clauses = []
        params = []
        
        conditions.each do |key, value|
          clauses << "#{key} = ?"
          params << value
        end
        
        [clauses.join(' AND ')] + params
      else
        raise ArgumentError, "Invalid conditions type"
      end
    end
  end
  
  def initialize(attributes = {})
    attributes.each do |key, value|
      instance_variable_set("@#{key}", value)
    end
    @new_record = true
    @destroyed = false
    @changed_attributes = Set.new
  end
  
  def save
    if @new_record
      create_record
    else
      update_record
    end
  end
  
  def update(attributes)
    attributes.each do |key, value|
      send("#{key}=", value) if respond_to?("#{key}=")
    end
    save
  end
  
  def destroy
    return false if @destroyed || @new_record
    
    self.class.connection.execute(
      "DELETE FROM #{self.class.table_name} WHERE #{self.class.primary_key} = ?",
      [id]
    )
    
    @destroyed = true
    freeze
  end
  
  def new_record?
    @new_record
  end
  
  def destroyed?
    @destroyed
  end
  
  def persisted?
    !@new_record && !@destroyed
  end
  
  def id
    instance_variable_get(:@id)
  end
  
  private
  
  def create_record
    columns = instance_variables.map { |var| var.to_s.delete('@') }
    values = columns.map { |col| instance_variable_get("@#{col}") }
    
    # Remove id for insert
    columns.delete(self.class.primary_key)
    values.shift
    
    placeholders = Array.new(values.length, '?').join(', ')
    
    sql = "INSERT INTO #{self.class.table_name} (#{columns.join(', ')}) VALUES (#{placeholders})"
    result = self.class.connection.execute(sql, values)
    
    @id = result.last_insert_id
    @new_record = false
    @changed_attributes.clear
    
    true
  end
  
  def update_record
    return false if @changed_attributes.empty?
    
    set_clauses = []
    params = []
    
    @changed_attributes.each do |attr|
      set_clauses << "#{attr} = ?"
      params << instance_variable_get("@#{attr}")
    end
    
    params << id
    
    sql = "UPDATE #{self.class.table_name} SET #{set_clauses.join(', ')} WHERE #{self.class.primary_key} = ?"
    self.class.connection.execute(sql, params)
    
    @changed_attributes.clear
    true
  end
end

class User < BaseActiveRecord
  attr_accessor :name, :email, :age, :created_at, :updated_at
  
  def initialize(attributes = {})
    super
    @created_at ||= Time.now
    @updated_at ||= Time.now
  end
  
  def validate
    errors = []
    
    errors << "Name cannot be blank" if name.nil? || name.strip.empty?
    errors << "Email cannot be blank" if email.nil? || email.strip.empty?
    errors << "Invalid email format" unless email =~ /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
    errors << "Age must be positive" if age && age <= 0
    
    errors
  end
  
  def valid?
    validate.empty?
  end
  
  def save
    return false unless valid?
    
    @updated_at = Time.now
    super
  end
  
  def self.find_by_email(email)
    where(email: email).first
  end
  
  def self.adults
    where("age >= ?", 18)
  end
  
  def self.recent(days = 7)
    where("created_at >= ?", Time.now - days * 24 * 60 * 60)
  end
  
  def to_s
    "User(#{id}): #{name} (#{email})"
  end
end

class DatabaseConnection
  def initialize
    @connected = true
    @tables = {}
    @sequences = Hash.new(1)
  end
  
  def execute(sql, params = [])
    puts "Executing SQL: #{sql}"
    puts "Parameters: #{params}" if params.any?
    
    # Simulate database operations
    case sql
    when /SELECT.*FROM (\w+)/
      table_name = $1
      simulate_select(table_name, sql, params)
    when /INSERT INTO (\w+)/
      table_name = $1
      simulate_insert(table_name, sql, params)
    when /UPDATE (\w+)/
      table_name = $1
      simulate_update(table_name, sql, params)
    when /DELETE FROM (\w+)/
      table_name = $1
      simulate_delete(table_name, sql, params)
    else
      []
    end
  end
  
  private
  
  def simulate_select(table_name, sql, params)
    @tables[table_name] ||= []
    
    # Simulate WHERE clause
    if sql.include?('WHERE')
      # Simplified filtering
      @tables[table_name].select do |row|
        # Simple ID matching simulation
        if sql.include?('id = ?')
          row['id'] == params[0].to_i
        else
          true
        end
      end
    else
      @tables[table_name]
    end
  end
  
  def simulate_insert(table_name, sql, params)
    @tables[table_name] ||= []
    
    new_id = @sequences[table_name]
    @sequences[table_name] += 1
    
    columns = sql.match(/\(([^)]+)\)/)[1].split(', ')
    new_row = { 'id' => new_id }
    
    columns.each_with_index do |col, i|
      new_row[col.strip] = params[i]
    end
    
    @tables[table_name] << new_row
    
    QueryResult.new(new_id)
  end
  
  def simulate_update(table_name, sql, params)
    @tables[table_name] ||= []
    
    # Simulate update
    true
  end
  
  def simulate_delete(table_name, sql, params)
    @tables[table_name] ||= []
    
    # Simulate delete
    true
  end
end

class QueryResult
  def initialize(last_insert_id = nil)
    @last_insert_id = last_insert_id
  end
  
  def last_insert_id
    @last_insert_id
  end
end

class ActiveRecordDemo
  def self.demonstrate_basic_active_record
    puts "Basic Active Record Demonstration:"
    puts "=" * 50
    
    # Create users
    puts "Creating users:"
    
    user1 = User.create(
      name: 'John Doe',
      email: 'john@example.com',
      age: 30
    )
    
    user2 = User.create(
      name: 'Jane Smith',
      email: 'jane@example.com',
      age: 25
    )
    
    user3 = User.create(
      name: 'Bob Johnson',
      email: 'bob@example.com',
      age: 35
    )
    
    puts "Created users:"
    puts "  #{user1}"
    puts "  #{user2}"
    puts "  #{user3}"
    
    # Find user
    puts "\nFinding user:"
    found_user = User.find(1)
    puts "Found: #{found_user}"
    
    # Query users
    puts "\nQuerying users:"
    all_users = User.all
    puts "All users (#{all_users.length}):"
    all_users.each { |user| puts "  #{user}" }
    
    # Filter users
    puts "\nFiltering users:"
    adults = User.adults
    puts "Adult users (#{adults.length}):"
    adults.each { |user| puts "  #{user}" }
    
    # Find by email
    puts "\nFinding by email:"
    email_user = User.find_by_email('jane@example.com')
    puts "Found by email: #{email_user}"
    
    # Update user
    puts "\nUpdating user:"
    user1.update(age: 31)
    puts "Updated: #{user1}"
    
    # Validation
    puts "\nValidation testing:"
    invalid_user = User.new(name: '', email: 'invalid', age: -5)
    puts "Invalid user errors: #{invalid_user.validate.join(', ')}"
    puts "Valid: #{invalid_user.valid?}"
    
    # Delete user
    puts "\nDeleting user:"
    user3.destroy
    puts "Deleted: #{user3.destroyed?}"
    
    remaining_users = User.all
    puts "Remaining users: #{remaining_users.length}"
    
    puts "\nActive Record Features:"
    puts "- CRUD operations (Create, Read, Update, Delete)"
    puts "- Query interface with conditions"
    puts "- Validation framework"
    puts "- Convention-based table/column naming"
    puts "- Automatic timestamp handling"
    puts "- Object-relational mapping"
  end
end
```

## 🔗 Associations and Relationships

### 3. Association Implementation

Model relationships and associations:

```ruby
class AssociationManager
  def self.demonstrate_associations
    puts "Active Record Associations Demonstration:"
    puts "=" * 50
    
    # Create models with associations
    create_models_with_associations
    
    # Demonstrate different association types
    demonstrate_has_many
    demonstrate_belongs_to
    demonstrate_has_one
    demonstrate_has_many_through
    demonstrate_many_to_many
    
    puts "\nAssociation Features:"
    puts "- One-to-many relationships"
    puts "- Many-to-one relationships"
    puts "- One-to-one relationships"
    puts "- Many-to-many relationships"
    puts "- Eager loading"
    puts "- Cascade operations"
  end
  
  private
  
  def self.create_models_with_associations
    # Define Post model
    class Post < BaseActiveRecord
      attr_accessor :title, :content, :user_id, :created_at, :updated_at
      
      def initialize(attributes = {})
        super
        @created_at ||= Time.now
        @updated_at ||= Time.now
      end
      
      def user
        User.find(user_id) if user_id
      end
      
      def comments
        Comment.where(post_id: id)
      end
      
      def tags
        post_tags = PostTag.where(post_id: id)
        tag_ids = post_tags.map(&:tag_id)
        Tag.where(id: tag_ids)
      end
      
      def add_tag(tag_name)
        tag = Tag.find_or_create_by_name(tag_name)
        PostTag.create(post_id: id, tag_id: tag.id)
      end
    end
    
    # Define Comment model
    class Comment < BaseActiveRecord
      attr_accessor :content, :post_id, :user_id, :created_at, :updated_at
      
      def initialize(attributes = {})
        super
        @created_at ||= Time.now
        @updated_at ||= Time.now
      end
      
      def post
        Post.find(post_id) if post_id
      end
      
      def user
        User.find(user_id) if user_id
      end
    end
    
    # Define Tag model
    class Tag < BaseActiveRecord
      attr_accessor :name, :created_at, :updated_at
      
      def initialize(attributes = {})
        super
        @created_at ||= Time.now
        @updated_at ||= Time.now
      end
      
      def posts
        post_tags = PostTag.where(tag_id: id)
        post_ids = post_tags.map(&:post_id)
        Post.where(id: post_ids)
      end
      
      def self.find_or_create_by_name(name)
        existing = where(name: name).first
        existing || create(name: name)
      end
    end
    
    # Define PostTag model (join table)
    class PostTag < BaseActiveRecord
      attr_accessor :post_id, :tag_id, :created_at
      
      def initialize(attributes = {})
        super
        @created_at ||= Time.now
      end
      
      def post
        Post.find(post_id) if post_id
      end
      
      def tag
        Tag.find(tag_id) if tag_id
      end
    end
  end
  
  def self.demonstrate_has_many
    puts "\nHas Many Association:"
    
    # Create user with posts
    user = User.create(name: 'Alice', email: 'alice@example.com', age: 28)
    
    post1 = Post.create(title: 'First Post', content: 'Hello World!', user_id: user.id)
    post2 = Post.create(title: 'Second Post', content: 'Another post', user_id: user.id)
    post3 = Post.create(title: 'Third Post', content: 'More content', user_id: user.id)
    
    puts "User: #{user}"
    puts "Posts:"
    user.posts = [post1, post2, post3]
    
    # Simulate has_many relationship
    class User
      def posts
        Post.where(user_id: id)
      end
    end
    
    user_posts = user.posts
    puts "User's posts (#{user_posts.length}):"
    user_posts.each { |post| puts "  #{post}" }
  end
  
  def self.demonstrate_belongs_to
    puts "\nBelongs To Association:"
    
    post = Post.create(title: 'Test Post', content: 'Test content', user_id: 1)
    
    puts "Post: #{post}"
    puts "Author: #{post.user}"
  end
  
  def self.demonstrate_has_one
    puts "\nHas One Association:"
    
    # Define Profile model
    class Profile < BaseActiveRecord
      attr_accessor :bio, :user_id, :created_at, :updated_at
      
      def initialize(attributes = {})
        super
        @created_at ||= Time.now
        @updated_at ||= Time.now
      end
      
      def user
        User.find(user_id) if user_id
      end
    end
    
    # Add has_one to User
    class User
      def profile
        Profile.where(user_id: id).first
      end
    end
    
    user = User.create(name: 'Bob', email: 'bob@example.com', age: 32)
    profile = Profile.create(bio: 'Software Developer', user_id: user.id)
    
    puts "User: #{user}"
    puts "Profile: #{user.profile}"
    puts "Profile User: #{user.profile.user}"
  end
  
  def self.demonstrate_has_many_through
    puts "\nHas Many Through Association:"
    
    # Define Category model
    class Category < BaseActiveRecord
      attr_accessor :name, :created_at, :updated_at
      
      def initialize(attributes = {})
        super
        @created_at ||= Time.now
        @updated_at ||= Time.now
      end
      
      def posts
        post_categories = PostCategory.where(category_id: id)
        post_ids = post_categories.map(&:post_id)
        Post.where(id: post_ids)
      end
    end
    
    # Define PostCategory model (join table)
    class PostCategory < BaseActiveRecord
      attr_accessor :post_id, :category_id, :created_at
      
      def initialize(attributes = {})
        super
        @created_at ||= Time.now
      end
      
      def post
        Post.find(post_id) if post_id
      end
      
      def category
        Category.find(category_id) if category_id
      end
    end
    
    # Add has_many_through to Post
    class Post
      def categories
        post_categories = PostCategory.where(post_id: id)
        category_ids = post_categories.map(&:category_id)
        Category.where(id: category_ids)
      end
    end
    
    # Create data
    category1 = Category.create(name: 'Technology')
    category2 = Category.create(name: 'Programming')
    
    post = Post.create(title: 'Ruby Guide', content: 'Learning Ruby', user_id: 1)
    
    PostCategory.create(post_id: post.id, category_id: category1.id)
    PostCategory.create(post_id: post.id, category_id: category2.id)
    
    puts "Post: #{post}"
    puts "Categories:"
    post.categories.each { |category| puts "  #{category}" }
    
    puts "Category Posts:"
    category1.posts.each { |post| puts "  #{post}" }
  end
  
  def self.demonstrate_many_to_many
    puts "\nMany to Many Association:"
    
    post = Post.create(title: 'Tutorial Post', content: 'Tutorial content', user_id: 1)
    
    # Add tags
    post.add_tag('ruby')
    post.add_tag('programming')
    post.add_tag('tutorial')
    
    puts "Post: #{post}"
    puts "Tags:"
    post.tags.each { |tag| puts "  #{tag.name}" }
    
    # Get posts for a tag
    ruby_tag = Tag.where(name: 'ruby').first
    if ruby_tag
      puts "Ruby Tag Posts:"
      ruby_tag.posts.each { |post| puts "  #{post.title}" }
    end
  end
end
```

## 🔍 Query Interface

### 4. Advanced Query Building

Flexible and powerful query interface:

```ruby
class QueryBuilder
  def initialize(model_class)
    @model_class = model_class
    @where_conditions = []
    @where_params = []
    @order_clause = nil
    @limit_clause = nil
    @offset_clause = nil
    @joins = []
    @select_fields = nil
    @group_clause = nil
    @having_clause = nil
  end
  
  def where(conditions)
    case conditions
    when String
      @where_conditions << conditions
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
    @order_clause = "#{field} #{direction}"
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
  
  def joins(table)
    @joins << table
    self
  end
  
  def select(*fields)
    @select_fields = fields.join(', ')
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
  
  def count
    sql = build_count_sql
    @model_class.connection.execute(sql, @where_params).first['count']
  end
  
  def first
    limit(1)
    results = to_a
    results.first
  end
  
  def last
    order("#{@model_class.primary_key} DESC").limit(1).to_a.first
  end
  
  def pluck(field)
    select(field).to_a.map { |record| record[field] }
  end
  
  def exists?
    limit(1).any?
  end
  
  def any?
    !to_a.empty?
  end
  
  def empty?
    to_a.empty?
  end
  
  def to_a
    sql = build_sql
    results = @model_class.connection.execute(sql, @where_params)
    results.map { |row| @model_class.new_from_hash(row) }
  end
  
  def find_each(batch_size: 1000)
    offset = 0
    
    loop do
      records = limit(batch_size).offset(offset).to_a
      break if records.empty?
      
      records.each { |record| yield record }
      offset += batch_size
    end
  end
  
  def find_in_batches(batch_size: 1000)
    offset = 0
    
    loop do
      batch = limit(batch_size).offset(offset).to_a
      break if batch.empty?
      
      yield batch
      offset += batch_size
    end
  end
  
  private
  
  def build_sql
    sql = "SELECT "
    sql += @select_fields || "*"
    sql += " FROM #{@model_class.table_name}"
    
    # Add joins
    @joins.each do |join_table|
      sql += " JOIN #{join_table} ON #{@model_class.table_name}.#{join_table}_id = #{join_table}.id"
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
    sql += " ORDER BY #{@order_clause}" if @order_clause
    
    # Add limit
    sql += " LIMIT #{@limit_clause}" if @limit_clause
    
    # Add offset
    sql += " OFFSET #{@offset_clause}" if @offset_clause
    
    sql
  end
  
  def build_count_sql
    sql = "SELECT COUNT(*) as count FROM #{@model_class.table_name}"
    
    # Add joins
    @joins.each do |join_table|
      sql += " JOIN #{join_table} ON #{@model_class.table_name}.#{join_table}_id = #{join_table}.id"
    end
    
    # Add where conditions
    if @where_conditions.any?
      sql += " WHERE #{@where_conditions.join(' AND ')}"
    end
    
    sql
  end
end

class QueryInterfaceDemo
  def self.demonstrate_query_builder
    puts "Query Interface Demonstration:"
    puts "=" * 50
    
    # Extend User class with query builder
    class User
      def self.where(conditions = {})
        QueryBuilder.new(self).where(conditions)
      end
      
      def self.order(field, direction = 'ASC')
        QueryBuilder.new(self).order(field, direction)
      end
      
      def self.limit(count)
        QueryBuilder.new(self).limit(count)
      end
      
      def self.offset(count)
        QueryBuilder.new(self).offset(count)
      end
      
      def self.select(*fields)
        QueryBuilder.new(self).select(*fields)
      end
      
      def self.joins(table)
        QueryBuilder.new(self).joins(table)
      end
    end
    
    # Create sample data
    users = [
      User.create(name: 'Alice', email: 'alice@example.com', age: 25),
      User.create(name: 'Bob', email: 'bob@example.com', age: 30),
      User.create(name: 'Charlie', email: 'charlie@example.com', age: 35),
      User.create(name: 'Diana', email: 'diana@example.com', age: 28),
      User.create(name: 'Eve', email: 'eve@example.com', age: 32)
    ]
    
    puts "Created #{users.length} users"
    
    # Demonstrate different query methods
    puts "\nBasic Queries:"
    
    # Where clause
    adults = User.where(age: 30..40)
    puts "Adults (30-40): #{adults.count} users"
    
    # Order by
    ordered_users = User.order('age', 'DESC')
    puts "Ordered by age (DESC):"
    ordered_users.each { |user| puts "  #{user.name}: #{user.age}" }
    
    # Limit and offset
    limited_users = User.limit(2).offset(1)
    puts "Limited (2) with offset (1):"
    limited_users.each { |user| puts "  #{user.name}" }
    
    # Select specific fields
    names = User.select('name').pluck('name')
    puts "Names: #{names.join(', ')}"
    
    # Count
    total_count = User.count
    puts "Total count: #{total_count}"
    
    # First and last
    first_user = User.order('id').first
    last_user = User.order('id').last
    puts "First user: #{first_user.name}"
    puts "Last user: #{last_user.name}"
    
    # Exists?
    alice_exists = User.where(name: 'Alice').exists?
    puts "Alice exists: #{alice_exists}"
    
    # Complex query
    puts "\nComplex Queries:"
    
    complex_query = User
      .where('age > ?', 25)
      .order('name', 'ASC')
      .limit(3)
    
    puts "Users over 25, ordered by name, limited to 3:"
    complex_query.each { |user| puts "  #{user.name} (#{user.age})" }
    
    puts "\nQuery Interface Features:"
    puts "- WHERE conditions with multiple operators"
    puts "- ORDER BY with direction"
    puts "- LIMIT and OFFSET for pagination"
    puts "- SELECT specific fields"
    puts "- COUNT aggregation"
    puts "- EXISTS check"
    puts "- FIRST and LAST methods"
    puts "- PLUCK for single field extraction"
  end
end
```

## 🔄 Callbacks and Validations

### 5. Callbacks and Validations Framework

Model lifecycle and validation system:

```ruby
class CallbackManager
  def self.demonstrate_callbacks
    puts "Callbacks Demonstration:"
    puts "=" * 50
    
    # Define model with callbacks
    class Article < BaseActiveRecord
      attr_accessor :title, :content, :published, :view_count, :created_at, :updated_at
      
      def initialize(attributes = {})
        super
        @published = false
        @view_count = 0
        @created_at ||= Time.now
        @updated_at ||= Time.now
      end
      
      # Callbacks
      before_create :set_default_values
      after_create :log_creation
      before_update :update_timestamp
      after_update :log_update
      before_destroy :check_dependencies
      after_destroy :log_destruction
      
      def save
        return false unless valid?
        
        run_callbacks(:before_save) do
          if new_record?
            run_callbacks(:before_create) do
              result = create_record
              run_callbacks(:after_create) if result
              result
            end
          else
            run_callbacks(:before_update) do
              result = update_record
              run_callbacks(:after_update) if result
              result
            end
          end
        end
      end
      
      def destroy
        run_callbacks(:before_destroy) do
          result = super
          run_callbacks(:after_destroy) if result
          result
        end
      end
      
      private
      
      def set_default_values
        self.content ||= "Default content"
        puts "  [before_create] Setting default values"
      end
      
      def log_creation
        puts "  [after_create] Article created: #{title}"
      end
      
      def update_timestamp
        self.updated_at = Time.now
        puts "  [before_update] Updating timestamp"
      end
      
      def log_update
        puts "  [after_update] Article updated: #{title}"
      end
      
      def check_dependencies
        if published && view_count > 100
          puts "  [before_destroy] Cannot delete popular published article"
          throw :abort
        end
        puts "  [before_destroy] Checking dependencies"
      end
      
      def log_destruction
        puts "  [after_destroy] Article destroyed: #{title}"
      end
    end
    
    # Callback execution framework
    module CallbackExecution
      def run_callbacks(callback_type, &block)
        callbacks = self.class.callbacks[callback_type] || []
        
        callbacks.each do |callback|
          result = instance_exec(&callback)
          throw :abort if result == false
        end
        
        if block_given?
          result = yield
          run_after_callbacks(callback_type) if result
          result
        else
          true
        end
      end
      
      def run_after_callbacks(callback_type)
        callbacks = self.class.callbacks["after_#{callback_type.to_s.gsub('before_', '')}".to_sym] || []
        callbacks.each { |callback| instance_exec(&callback) }
      end
    end
    
    class Article
      extend CallbackExecution
      include CallbackExecution
      
      class << self
        def callbacks
          @callbacks ||= {}
        end
        
        def before_create(method_name = nil, &block)
          add_callback(:before_create, method_name, &block)
        end
        
        def after_create(method_name = nil, &block)
          add_callback(:after_create, method_name, &block)
        end
        
        def before_update(method_name = nil, &block)
          add_callback(:before_update, method_name, &block)
        end
        
        def after_update(method_name = nil, &block)
          add_callback(:after_update, method_name, &block)
        end
        
        def before_destroy(method_name = nil, &block)
          add_callback(:before_destroy, method_name, &block)
        end
        
        def after_destroy(method_name = nil, &block)
          add_callback(:after_destroy, method_name, &block)
        end
        
        private
        
        def add_callback(type, method_name = nil, &block)
          callbacks[type] ||= []
          callback = method_name ? method(method_name) : block
          callbacks[type] << callback
        end
      end
    end
    
    # Create and save article
    article = Article.new(title: 'Ruby Programming', content: 'Learn Ruby programming')
    
    puts "Creating article:"
    article.save
    
    puts "\nUpdating article:"
    article.content = 'Updated content about Ruby programming'
    article.save
    
    puts "\nDestroying article:"
    article.destroy
    
    puts "\nCallback Features:"
    puts "- Before/after create callbacks"
    puts "- Before/after update callbacks"
    puts "- Before/after destroy callbacks"
    puts "- Callback chaining"
    puts "- Callback interruption"
  end
end

class ValidationFramework
  def self.demonstrate_validations
    puts "\nValidation Framework Demonstration:"
    puts "=" * 50
    
    # Define model with validations
    class Product < BaseActiveRecord
      attr_accessor :name, :price, :description, :stock_quantity, :category, :created_at, :updated_at
      
      def initialize(attributes = {})
        super
        @created_at ||= Time.now
        @updated_at ||= Time.now
      end
      
      # Validations
      validates :name, presence: true, length: { minimum: 3, maximum: 100 }
      validates :price, presence: true, numericality: { greater_than: 0 }
      validates :description, length: { maximum: 1000 }
      validates :stock_quantity, presence: true, numericality: { greater_than_or_equal_to: 0 }
      validates :category, inclusion: { in: %w[electronics clothing books food] }
      
      # Custom validation
      validate :price_must_be_reasonable
      
      def save
        return false unless valid?
        
        @updated_at = Time.now
        super
      end
      
      def valid?
        errors.clear
        run_validations
        errors.empty?
      end
      
      def errors
        @errors ||= {}
      end
      
      private
      
      def price_must_be_reasonable
        if price && price > 10000
          errors[:price] << "Price cannot exceed $10,000"
        end
      end
      
      def run_validations
        self.class.validations.each do |validation|
          run_validation(validation)
        end
        
        # Run custom validations
        custom_validations.each { |validation| instance_exec(&validation) }
      end
      
      def run_validation(validation)
        field = validation[:field]
        value = instance_variable_get("@#{field}")
        
        validation[:rules].each do |rule, options|
          case rule
          when :presence
            errors[field] << "#{field.to_s.humanize} can't be blank" if value.nil? || value.to_s.strip.empty?
          when :length
            if value && value.respond_to?(:length)
              min_length = options[:minimum]
              max_length = options[:maximum]
              
              errors[field] << "#{field.to_s.humanize} is too short (minimum is #{min_length} characters)" if min_length && value.length < min_length
              errors[field] << "#{field.to_s.humanize} is too long (maximum is #{max_length} characters)" if max_length && value.length > max_length
            end
          when :numericality
            if value && !value.is_a?(Numeric)
              errors[field] << "#{field.to_s.humanize} is not a number"
            elsif value
              errors[field] << "#{field.to_s.humanize} must be greater than 0" if options[:greater_than] && value <= options[:greater_than]
              errors[field] << "#{field.to_s.humanize} must be greater than or equal to 0" if options[:greater_than_or_equal_to] && value < options[:greater_than_or_equal_to]
            end
          when :inclusion
            if value && !options[:in].include?(value)
              errors[field] << "#{field.to_s.humanize} must be one of: #{options[:in].join(', ')}"
            end
          end
        end
      end
      
      def custom_validations
        [method(:price_must_be_reasonable)]
      end
      
      class << self
        def validations
          @validations ||= []
        end
        
        def validates(field, options = {})
          validations << { field: field, rules: extract_rules(options) }
        end
        
        def validate(method_name)
          custom_validations << method(method_name)
        end
        
        def custom_validations
          @custom_validations ||= []
        end
        
        private
        
        def extract_rules(options)
          rules = {}
          
          options.each do |key, value|
            case key
            when :presence
              rules[:presence] = value
            when :length
              rules[:length] = value
            when :numericality
              rules[:numericality] = value
            when :inclusion
              rules[:inclusion] = value
            end
          end
          
          rules
        end
      end
    end
    
    # Test validations
    puts "Testing Validations:"
    
    # Valid product
    valid_product = Product.new(
      name: 'Laptop',
      price: 999.99,
      description: 'High-performance laptop',
      stock_quantity: 50,
      category: 'electronics'
    )
    
    puts "Valid product:"
    puts "  Name: #{valid_product.name}"
    puts "  Price: #{valid_product.price}"
    puts "  Valid: #{valid_product.valid?}"
    puts "  Errors: #{valid_product.errors}" unless valid_product.valid?
    
    # Invalid products
    puts "\nInvalid Products:"
    
    invalid_products = [
      { name: '', price: 999.99, description: 'Test', stock_quantity: 10, category: 'electronics' },
      { name: 'Test', price: -100, description: 'Test', stock_quantity: 10, category: 'electronics' },
      { name: 'Test', price: 999.99, description: 'A' * 1001, stock_quantity: 10, category: 'electronics' },
      { name: 'Test', price: 999.99, description: 'Test', stock_quantity: -5, category: 'electronics' },
      { name: 'Test', price: 999.99, description: 'Test', stock_quantity: 10, category: 'invalid' },
      { name: 'Test', price: 15000, description: 'Test', stock_quantity: 10, category: 'electronics' }
    ]
    
    invalid_products.each_with_index do |product_data, i|
      product = Product.new(product_data)
      puts "  Product #{i + 1}: #{product.valid? ? 'Valid' : 'Invalid'}"
      product.errors.each do |field, errors|
        errors.each { |error| puts "    #{field}: #{error}" }
      end
    end
    
    puts "\nValidation Features:"
    puts "- Presence validations"
    puts "- Length validations"
    puts "- Numericality validations"
    puts "- Inclusion validations"
    puts "- Custom validations"
    puts "- Error message formatting"
    puts "- Validation chaining"
  end
end
```

## 🎯 Migrations and Schema Management

### 6. Migration System

Database schema management:

```ruby
class MigrationSystem
  def self.demonstrate_migrations
    puts "Migration System Demonstration:"
    puts "=" * 50
    
    # Define migration framework
    class Migration
      attr_reader :version, :name, :up_sql, :down_sql
      
      def initialize(version, name, up_sql, down_sql)
        @version = version
        @name = name
        @up_sql = up_sql
        @down_sql = down_sql
      end
      
      def up
        puts "  [Migration] Up: #{name} (#{version})"
        DatabaseConnection.new.execute(@up_sql)
      end
      
      def down
        puts "  [Migration] Down: #{name} (#{version})"
        DatabaseConnection.new.execute(@down_sql)
      end
    end
    
    # Define specific migrations
    class CreateUsersTable < Migration
      def initialize
        super(
          1,
          'create_users_table',
          <<~SQL,
            CREATE TABLE users (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              name VARCHAR(255) NOT NULL,
              email VARCHAR(255) UNIQUE NOT NULL,
              age INTEGER,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
          SQL
          <<~SQL
            DROP TABLE users;
          SQL
        )
      end
    end
    
    class CreatePostsTable < Migration
      def initialize
        super(
          2,
          'create_posts_table',
          <<~SQL,
            CREATE TABLE posts (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              title VARCHAR(255) NOT NULL,
              content TEXT,
              user_id INTEGER,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (user_id) REFERENCES users(id)
            );
          SQL
          <<~SQL
            DROP TABLE posts;
          SQL
        )
      end
    end
    
    class AddEmailIndexToUsers < Migration
      def initialize
        super(
          3,
          'add_email_index_to_users',
          <<~SQL,
            CREATE INDEX idx_users_email ON users(email);
          SQL
          <<~SQL
            DROP INDEX idx_users_email;
          SQL
        )
      end
    end
    
    # Migration manager
    class MigrationManager
      def initialize
        @migrations = {}
        @current_version = 0
        load_migrations
      end
      
      def migrate(target_version = nil)
        target_version ||= latest_version
        
        puts "Migrating to version #{target_version}"
        
        while @current_version < target_version
          next_migration = @migrations[@current_version + 1]
          break unless next_migration
          
          next_migration.up
          @current_version += 1
        end
        
        puts "Migration completed. Current version: #{@current_version}"
      end
      
      def rollback(target_version = nil)
        target_version ||= @current_version - 1
        
        puts "Rolling back to version #{target_version}"
        
        while @current_version > target_version
          current_migration = @migrations[@current_version]
          break unless current_migration
          
          current_migration.down
          @current_version -= 1
        end
        
        puts "Rollback completed. Current version: #{@current_version}"
      end
      
      def status
        puts "Migration Status:"
        puts "Current version: #{@current_version}"
        puts "Latest version: #{latest_version}"
        puts "Pending migrations: #{latest_version - @current_version}"
        
        (@current_version + 1).upto(latest_version) do |version|
          migration = @migrations[version]
          puts "  #{version}: #{migration.name}"
        end
      end
      
      private
      
      def load_migrations
        @migrations[1] = CreateUsersTable.new
        @migrations[2] = CreatePostsTable.new
        @migrations[3] = AddEmailIndexToUsers.new
      end
      
      def latest_version
        @migrations.keys.max || 0
      end
    end
    
    # Run migration demonstration
    manager = MigrationManager.new
    
    puts "Initial status:"
    manager.status
    
    puts "\nRunning migrations:"
    manager.migrate
    
    puts "\nFinal status:"
    manager.status
    
    puts "\nRolling back:"
    manager.rollback(1)
    
    puts "\nStatus after rollback:"
    manager.status
    
    puts "\nMigration Features:"
    puts "- Version-controlled schema changes"
    puts "- Up and down migrations"
    puts "- Migration status tracking"
    puts "- Rollback capability"
    puts "- Database-agnostic SQL"
    puts "- Migration dependencies"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Active Record**: Implement simple ORM
2. **Query Interface**: Build query builder
3. **Associations**: Create model relationships
4. **Validations**: Add validation framework

### Intermediate Exercises

1. **Advanced Queries**: Complex query building
2. **Callbacks System**: Lifecycle management
3. **Migration System**: Schema management
4. **Performance**: Optimize database operations

### Advanced Exercises

1. **Complete ORM**: Full-featured Active Record
2. **Database Adapter**: Multiple database support
3. **Connection Pooling**: Performance optimization
4. **Caching Layer**: Query result caching

---

## 🎯 Summary

Active Record Pattern in Ruby provides:

- **Pattern Fundamentals** - Core concepts and principles
- **Basic Implementation** - CRUD operations and mapping
- **Associations** - Model relationships and queries
- **Query Interface** - Flexible query building
- **Callbacks and Validations** - Lifecycle and validation framework
- **Migration System** - Schema management and versioning

Master these Active Record patterns for effective Ruby database applications!
