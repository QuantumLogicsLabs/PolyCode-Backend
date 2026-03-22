# Metaprogramming Examples
# Demonstrating dynamic method creation, reflection, and DSL techniques

puts "=== DYNAMIC METHOD DEFINITION ==="

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
        b != 0 ? a / b.to_f : "Cannot divide by zero"
      end
    end
  end
end

calc = Calculator.new
puts "Addition: #{calc.add(5, 3)}"
puts "Multiplication: #{calc.multiply(4, 6)}"
puts "Division: #{calc.divide(10, 3)}"

puts "\n=== METHOD MISSING ==="

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

accessor = DynamicAccessor.new({name: "John", age: 30, city: "New York"})
puts "Name: #{accessor.get_name}"
puts "Age: #{accessor.get_age}"

accessor.set_age(31)
puts "Updated age: #{accessor.get_age}"

puts "\n=== CLASS EVAL AND INSTANCE EVAL ==="

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

puts DynamicClass.class_method

obj = DynamicClass.new
puts obj.instance_method

# Add methods to individual instances
obj.instance_eval do
  def unique_method
    "This method exists only for this instance"
  end
end

puts obj.unique_method

puts "\n=== BINDING OBJECTS ==="

def create_binding_context
  x = 10
  y = 20
  z = x + y
  binding
end

context = create_binding_context
puts "Sum from binding: #{context.eval('x + y')}"
puts "Z value: #{context.eval('z')}"

# Use binding for templates
name = "Alice"
count = 5
template_binding = binding
puts template_binding.eval('"Hello, #{name}! You have #{count} messages."')

puts "\n=== REFLECTION AND INTROSPECTION ==="

class IntrospectionExample
  def public_method; end
  protected
  def protected_method; end
  private
  def private_method; end
  
  def self.class_method; end
end

obj = IntrospectionExample.new

puts "Total methods: #{obj.methods.length}"
puts "Public methods: #{obj.public_methods.length}"
puts "Protected methods: #{obj.protected_methods.length}"
puts "Private methods: #{obj.private_methods.length}"

puts "Responds to public_method? #{obj.respond_to?(:public_method)}"
puts "Responds to private_method? #{obj.respond_to?(:private_method)}"
puts "Responds to private_method (private)? #{obj.respond_to?(:private_method, true)}"

# Method information
method_info = obj.method(:public_method)
puts "Method owner: #{method_info.owner}"

puts "\n=== DYNAMIC CLASS CREATION ==="

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
user = DynamicUser.new("Alice", "alice@example.com")
puts user.info

puts "\n=== DOMAIN-SPECIFIC LANGUAGE (DSL) ==="

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

puts "Configuration:"
puts config.to_hash.inspect

puts "\n=== QUERY BUILDER DSL ==="

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

puts "Generated SQL:"
puts query.to_sql

puts "\n=== VALIDATED ATTRIBUTES MODULE ==="

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

puts "User created successfully:"
puts "Name: #{user.name}"
puts "Email: #{user.email}"
puts "Status: #{user.status}"

puts "\n=== METHOD CACHING ==="

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
puts "First call (slow): #{cached.slow_operation("test")}"
puts "Second call (cached): #{cached.slow_operation("test")}"
puts "Fast operation: #{cached.fast_operation("test")}"

puts "\n=== DYNAMIC ORM EXAMPLE ==="

class Model
  @@models = {}
  
  def self.inherited(subclass)
    @@models[subclass.name] = subclass
  end
  
  def self.create_finder_methods
    define_method(:find_by_name) do |name|
      all.find { |record| record.name == name }
    end
    
    define_method(:find_by_email) do |email|
      all.find { |record| record.email == email }
    end
  end
end

class User < Model
  attr_accessor :name, :email, :id
  
  def initialize(id, name, email)
    @id = id
    @name = name
    @email = email
  end
  
  def self.all
    [
      new(1, "John", "john@example.com"),
      new(2, "Jane", "jane@example.com"),
      new(3, "Bob", "bob@example.com")
    ]
  end
  
  create_finder_methods
end

# Test dynamic finder methods
user = User.find_by_name("John")
puts "Found user: #{user.name} (#{user.email})"

user = User.find_by_email("jane@example.com")
puts "Found user: #{user.name} (#{user.email})"

puts "\n=== EVENT SYSTEM ==="

class EventSystem
  def initialize
    @listeners = Hash.new { |h, k| h[k] = [] }
  end
  
  def on(event_name, &block)
    @listeners[event_name] << block
  end
  
  def emit(event_name, *args)
    @listeners[event_name].each { |listener| listener.call(*args) }
  end
  
  def off(event_name, listener)
    @listeners[event_name].delete(listener)
  end
end

# Usage
event_system = EventSystem.new

# Register listeners
event_system.on(:user_created) do |user|
  puts "User created: #{user.name}"
end

event_system.on(:user_created) do |user|
  puts "Sending welcome email to: #{user.email}"
end

event_system.on(:user_deleted) do |user|
  puts "User deleted: #{user.name}"
end

# Emit events
class User
  attr_reader :name, :email
  
  def initialize(name, email)
    @name = name
    @email = email
  end
end

user = User.new("Alice", "alice@example.com")
event_system.emit(:user_created, user)

event_system.emit(:user_deleted, user)

puts "\n=== METAPROGRAMMING SUMMARY ==="
puts "- Dynamic method creation with define_method"
puts "- Method missing for flexible APIs"
puts "- Class and instance evaluation"
puts "- Binding objects for templates"
puts "- Reflection and introspection"
puts "- Dynamic class creation"
puts "- Domain-specific languages"
puts "- Method caching for performance"
puts "- Event systems with dynamic registration"
puts "\nAll examples demonstrate Ruby's powerful metaprogramming capabilities!"
