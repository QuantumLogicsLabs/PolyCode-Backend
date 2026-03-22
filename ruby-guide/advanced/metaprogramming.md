# Metaprogramming in Ruby

## Overview

Metaprogramming is writing code that writes or manipulates other code. Ruby's dynamic nature makes it particularly well-suited for metaprogramming techniques.

## Dynamic Method Definition

### define_method

```ruby
class Calculator
  # Define methods dynamically
  [:add, :subtract, :multiply, :divide].each do |operation|
    define_method(operation) do |a, b|
      case operation
      when :add
        a + b
      when :subtract
        a - b
      when :multiply
        a * b
      when :divide
        a / b.to_f
      end
    end
  end
end

calc = Calculator.new
puts calc.add(5, 3)        # => 8
puts calc.multiply(4, 6)   # => 24
```

### method_missing

```ruby
class DynamicAccessor
  def initialize(data)
    @data = data
  end
  
  def method_missing(method_name, *args, &block)
    if method_name.to_s.start_with?('get_')
      attribute = method_name.to_s.sub('get_', '')
      @data[attribute.to_sym]
    elsif method_name.to_s.start_with?('set_')
      attribute = method_name.to_s.sub('set_', '')
      @data[attribute.to_sym] = args.first
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.start_with?('get_') || 
    method_name.to_s.start_with?('set_') || 
    super
  end
end

accessor = DynamicAccessor.new({name: "John", age: 30})
puts accessor.get_name        # => "John"
accessor.set_age(31)
puts accessor.get_age        # => 31
```

## Class Evaluation and Binding

### class_eval and instance_eval

```ruby
class DynamicClass
end

# Add methods to class using class_eval
DynamicClass.class_eval do
  def self.class_method
    "This is a class method"
  end
  
  def instance_method
    "This is an instance method"
  end
end

puts DynamicClass.class_method  # => "This is a class method"
obj = DynamicClass.new
puts obj.instance_method       # => "This is an instance method"

# Add methods to individual instances
obj.instance_eval do
  def unique_method
    "This method exists only for this instance"
  end
end

puts obj.unique_method         # => "This method exists only for this instance"
```

### Binding Objects

```ruby
def create_binding_context
  x = 10
  y = 20
  binding
end

context = create_binding_context
puts context.eval("x + y")  # => 30

# Use binding for templates
template = "Hello, #{name}! You have #{count} messages."
name = "Alice"
count = 5
puts binding.eval('"Hello, #{name}! You have #{count} messages."')
```

## Reflection and Introspection

### Method Introspection

```ruby
class IntrospectionExample
  def public_method; end
  protected
  def protected_method; end
  private
  def private_method; end
  
  def self.class_method; end
end

obj = IntrospectionExample.new

# List all methods
puts obj.methods.length
puts obj.public_methods.length
puts obj.protected_methods.length
puts obj.private_methods.length

# Check method existence
puts obj.respond_to?(:public_method)        # => true
puts obj.respond_to?(:private_method)       # => false
puts obj.respond_to?(:private_method, true) # => true

# Method information
method_info = obj.method(:public_method)
puts method_info.name
puts method_info.owner
puts method_info.source_location
```

### Class and Module Introspection

```ruby
module TestModule
  def module_method
    "From module"
  end
end

class TestClass
  include TestModule
  
  def instance_method
    "From class"
  end
  
  def self.class_method
    "From class"
  end
end

obj = TestClass.new

# Check ancestry
puts TestClass.ancestors
puts TestClass.included_modules
puts TestClass.superclass

# Check method origins
puts obj.method(:module_method).owner  # => TestModule
puts obj.method(:instance_method).owner  # => TestClass
puts TestClass.method(:class_method).owner  # => #<Class:TestClass>
```

## Dynamic Classes and Modules

### Creating Classes Dynamically

```ruby
# Create class with dynamic name
class_name = "DynamicUser"
user_class = Class.new do
  attr_accessor :name, :email
  
  def initialize(name, email)
    @name = name
    @email = email
  end
  
  def info
    "#{name} (#{email})"
  end
end

# Assign to constant
Object.const_set(class_name, user_class)

# Use the dynamically created class
user = DynamicUser.new("John", "john@example.com")
puts user.info  # => "John (john@example.com)"
```

### Creating Modules Dynamically

```ruby
# Create module with dynamic methods
validator_module = Module.new do
  define_method(:validate_presence) do |field|
    value = send(field)
    raise ArgumentError, "#{field} cannot be blank" if value.nil? || value.to_s.strip.empty?
  end
  
  define_method(:validate_email) do |field|
    value = send(field)
    email_pattern = /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
    raise ArgumentError, "Invalid email format" unless value.match?(email_pattern)
  end
end

class User
  extend validator_module
  
  attr_accessor :name, :email
  
  def initialize(name, email)
    @name = name
    @email = email
  end
  
  def validate
    validate_presence(:name)
    validate_presence(:email)
    validate_email(:email)
  end
end

user = User.new("John", "john@example.com")
user.validate  # No error
```

## Domain-Specific Languages (DSLs)

### Simple DSL Example

```ruby
class ConfigDSL
  def initialize(&block)
    instance_eval(&block)
  end
  
  def database(&block)
    @database = DatabaseConfig.new(&block)
  end
  
  def server(&block)
    @server = ServerConfig.new(&block)
  end
  
  def to_hash
    {
      database: @database&.to_hash,
      server: @server&.to_hash
    }
  end
end

class DatabaseConfig
  def initialize(&block)
    instance_eval(&block)
  end
  
  def host(value)
    @host = value
  end
  
  def port(value)
    @port = value
  end
  
  def name(value)
    @name = value
  end
  
  def to_hash
    {
      host: @host,
      port: @port,
      name: @name
    }
  end
end

class ServerConfig
  def initialize(&block)
    instance_eval(&block)
  end
  
  def host(value)
    @host = value
  end
  
  def port(value)
    @port = value
  end
  
  def ssl(enabled: true)
    @ssl = enabled
  end
  
  def to_hash
    {
      host: @host,
      port: @port,
      ssl: @ssl
    }
  end
end

# Usage
config = ConfigDSL.new do
  database do
    host "localhost"
    port 5432
    name "myapp_development"
  end
  
  server do
    host "0.0.0.0"
    port 3000
    ssl enabled: false
  end
end

puts config.to_hash.inspect
```

## Advanced Metaprogramming Techniques

### Method Chaining Builder

```ruby
class QueryBuilder
  def initialize(table)
    @table = table
    @conditions = []
    @order = nil
    @limit = nil
  end
  
  def where(condition)
    @conditions << condition
    self
  end
  
  def order(field, direction = :asc)
    @order = "#{field} #{direction}"
    self
  end
  
  def limit(count)
    @limit = count
    self
  end
  
  def to_sql
    sql = "SELECT * FROM #{@table}"
    sql += " WHERE #{@conditions.join(' AND ')}" unless @conditions.empty?
    sql += " ORDER BY #{@order}" if @order
    sql += " LIMIT #{@limit}" if @limit
    sql
  end
end

# Dynamic method creation for different conditions
class QueryBuilder
  [:eq, :ne, :gt, :lt, :gte, :lte].each do |operator|
    define_method(operator) do |field, value|
      case operator
      when :eq
        where("#{field} = '#{value}'")
      when :ne
        where("#{field} != '#{value}'")
      when :gt
        where("#{field} > '#{value}'")
      when :lt
        where("#{field} < '#{value}'")
      when :gte
        where("#{field} >= '#{value}'")
      when :lte
        where("#{field} <= '#{value}'")
      end
    end
  end
end

query = QueryBuilder.new("users")
  .eq("status", "active")
  .gte("age", 18)
  .order("created_at", :desc)
  .limit(10)

puts query.to_sql
# => "SELECT * FROM users WHERE status = 'active' AND age >= '18' ORDER BY created_at desc LIMIT 10"
```

### Attribute Accessors with Validation

```ruby
module ValidatedAttributes
  def self.included(base)
    base.extend(ClassMethods)
  end
  
  module ClassMethods
    def attr_validated(name, options = {})
      attr_accessor name
      
      define_method("#{name}=") do |value|
        validate_attribute(name, value, options)
        instance_variable_set("@#{name}", value)
      end
    end
    
    private
    
    def validate_attribute(name, value, options)
      if options[:presence] && (value.nil? || value.to_s.strip.empty?)
        raise ArgumentError, "#{name} cannot be blank"
      end
      
      if options[:length] && value.to_s.length > options[:length]
        raise ArgumentError, "#{name} is too long (max #{options[:length]} characters)"
      end
      
      if options[:format] && !value.match?(options[:format])
        raise ArgumentError, "#{name} format is invalid"
      end
      
      if options[:inclusion] && !options[:inclusion].include?(value)
        raise ArgumentError, "#{name} must be one of: #{options[:inclusion].join(', ')}"
      end
    end
  end
end

class User
  include ValidatedAttributes
  
  attr_validated :name, presence: true, length: 50
  attr_validated :email, presence: true, format: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
  attr_validated :status, inclusion: ["active", "inactive", "pending"]
end

user = User.new
user.name = "John"
user.email = "john@example.com"
user.status = "active"

# user.name = ""  # => ArgumentError: name cannot be blank
# user.status = "invalid"  # => ArgumentError: status must be one of: active, inactive, pending
```

## Performance Considerations

### Method Caching

```ruby
class CachedMethods
  def initialize
    @method_cache = {}
  end
  
  def method_missing(name, *args, &block)
    if @method_cache.key?(name)
      return @method_cache[name].call(*args, &block)
    end
    
    # Create and cache the method
    method_proc = create_method(name)
    @method_cache[name] = method_proc
    method_proc.call(*args, &block)
  end
  
  private
  
  def create_method(name)
    case name.to_s
    when /^slow_/
      ->(*args) { sleep(0.1); "Slow operation: #{args}" }
    when /^fast_/
      ->(*args) { "Fast operation: #{args}" }
    else
      ->(*args) { "Default operation: #{args}" }
    end
  end
end

cached = CachedMethods.new
puts cached.slow_operation("test")  # First call: slow
puts cached.slow_operation("test")  # Second call: fast (cached)
```

## Best Practices

### 1. Use Metaprogramming Sparingly

```ruby
# Good - Clear and explicit
class User
  def name
    @name
  end
  
  def name=(value)
    @name = value
  end
end

# Consider metaprogramming for repetitive patterns
class User
  attr_accessor :name, :email, :age
end
```

### 2. Document Metaprogramming

```ruby
# Always document dynamic methods
class DynamicClass
  # Dynamically creates getter methods for all attributes
  # @example
  #   create_getters(:name, :email)
  def self.create_getters(*attributes)
    attributes.each do |attr|
      define_method(attr) { instance_variable_get("@#{attr}") }
    end
  end
end
```

### 3. Handle Errors Gracefully

```ruby
class SafeMetaprogramming
  def method_missing(name, *args)
    super unless name.to_s.start_with?('dynamic_')
    
    begin
      # Dynamic method logic
    rescue => e
      puts "Error in dynamic method #{name}: #{e.message}"
      nil
    end
  end
end
```

## Practice Exercises

### Exercise 1: Dynamic ORM
Create a simple ORM that:
- Dynamically creates finders based on column names
- Supports method chaining
- Handles different query types

### Exercise 2: Validation Framework
Build a validation system that:
- Creates validators dynamically
- Supports custom validation rules
- Provides detailed error messages

### Exercise 3: Configuration Builder
Implement a configuration builder that:
- Uses DSL syntax
- Supports nested configurations
- Validates configuration values

### Exercise 4: Event System
Create an event system that:
- Dynamically registers event handlers
- Supports event inheritance
- Provides event filtering

---

**Ready to explore concurrency and threading in Ruby? Let's continue! 🚀**
