# Modules and Mixins Examples
# Demonstrating module usage, mixins, and namespacing in Ruby

puts "=== BASIC MODULES ==="

# Simple module with constants and methods
module MathUtilities
  PI = 3.14159265359
  
  def self.circle_area(radius)
    PI * radius ** 2
  end
  
  def self.pythagorean(a, b)
    Math.sqrt(a ** 2 + b ** 2)
  end
end

puts "PI: #{MathUtilities::PI}"
puts "Circle area (radius 5): #{MathUtilities.circle_area(5)}"
puts "Pythagorean (3, 4): #{MathUtilities.pythagorean(3, 4)}"

puts "\n=== MODULE CONSTANTS ==="

module Config
  DATABASE_URL = "postgresql://localhost:5432/myapp"
  API_VERSION = "v1"
  MAX_RETRIES = 3
  TIMEOUT = 30
end

puts "Database URL: #{Config::DATABASE_URL}"
puts "API Version: #{Config::API_VERSION}"

puts "\n=== INCLUDE - INSTANCE METHODS ==="

module Greetings
  def hello
    "Hello, #{name}!"
  end
  
  def goodbye
    "Goodbye, #{name}!"
  end
  
  private
  
  def name
    "Friend"
  end
end

class Person
  include Greetings
  
  def initialize(name)
    @name = name
  end
  
  private
  
  def name
    @name
  end
end

person = Person.new("Alice")
puts person.hello    # => "Hello, Alice!"
puts person.goodbye  # => "Goodbye, Alice!"

puts "\n=== EXTEND - CLASS METHODS ==="

module Validation
  def validate_email(email)
    email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
  end
  
  def validate_phone(phone)
    phone.match?(/\A\d{10}\z/)
  end
end

class User
  extend Validation
  
  attr_accessor :email, :phone
  
  def initialize(email, phone)
    @email = email
    @phone = phone
  end
  
  def valid?
    self.class.validate_email(@email) && self.class.validate_phone(@phone)
  end
end

user = User.new("user@example.com", "1234567890")
puts "User valid? #{user.valid?}"
puts "Email valid? #{User.validate_email("test@example.com")}"
puts "Phone valid? #{User.validate_phone("1234567890")}"

puts "\n=== PREPEND - METHOD LOOKUP MODIFICATION ==="

module Logger
  def process(data)
    puts "Processing started at #{Time.now}"
    result = super
    puts "Processing completed at #{Time.now}"
    result
  end
end

class DataProcessor
  prepend Logger
  
  def process(data)
    puts "Actually processing: #{data}"
    "Processed: #{data}"
  end
end

processor = DataProcessor.new
puts processor.process("test data")

puts "\n=== MULTIPLE INHERITANCE SIMULATION ==="

module Drawable
  def draw
    puts "Drawing #{self.class.name}"
  end
  
  def erase
    puts "Erasing #{self.class.name}"
  end
end

module Movable
  def move(x, y)
    @x = x
    @y = y
    puts "Moved to (#{x}, #{y})"
  end
  
  def position
    "(#{@x}, #{@y})"
  end
end

module Resizable
  def resize(width, height)
    @width = width
    @height = height
    puts "Resized to #{width}x#{height}"
  end
  
  def size
    "#{@width}x#{@height}"
  end
end

class Shape
  include Drawable
  include Movable
  include Resizable
  
  def initialize(x = 0, y = 0, width = 100, height = 100)
    @x = x
    @y = y
    @width = width
    @height = height
  end
end

class Circle < Shape
  def draw
    puts "Drawing circle at #{position} with radius #{@width/2}"
  end
end

class Rectangle < Shape
  def draw
    puts "Drawing rectangle at #{position} with size #{size}"
  end
end

circle = Circle.new(10, 20, 50, 50)
rectangle = Rectangle.new(30, 40, 100, 60)

circle.draw
circle.move(15, 25)
circle.resize(60, 60)

rectangle.draw
rectangle.move(35, 45)
rectangle.resize(120, 80)

puts "\n=== NAMESPACING ==="

module ECommerce
  module Models
    class Product
      attr_accessor :id, :name, :price
      
      def initialize(id, name, price)
        @id = id
        @name = name
        @price = price
      end
      
      def to_s
        "#{@name} - $#{@'%.2f' % @price}"
      end
    end
    
    class Order
      attr_accessor :id, :products, :total
      
      def initialize(id)
        @id = id
        @products = []
        @total = 0
      end
      
      def add_product(product)
        @products << product
        @total += product.price
      end
      
      def to_s
        "Order #{@id}: #{@products.length} items, Total: $#{'%.2f' % @total}"
      end
    end
  end
  
  module Services
    class PaymentProcessor
      def process_payment(order)
        puts "Processing payment for order #{order.id}: $#{'%.2f' % order.total}"
        "Payment successful"
      end
    end
    
    class InventoryManager
      def check_stock(product, quantity)
        puts "Checking stock for #{product.name}: #{quantity} units"
        true
      end
    end
  end
  
  module Utils
    class PriceCalculator
      def self.calculate_total(products)
        products.sum(&:price)
      end
      
      def self.apply_discount(total, discount_percentage)
        total * (1 - discount_percentage / 100.0)
      end
    end
  end
end

# Using namespaced classes
product = ECommerce::Models::Product.new(1, "Laptop", 999.99)
order = ECommerce::Models::Order.new(1001)
order.add_product(product)

processor = ECommerce::Services::PaymentProcessor.new
puts processor.process_payment(order)

total = ECommerce::Utils::PriceCalculator.calculate_total(order.products)
discounted_total = ECommerce::Utils::PriceCalculator.apply_discount(total, 10)
puts "Total: $#{'%.2f' % total}, Discounted: $#{'%.2f' % discounted_total}"

puts "\n=== METHOD LOOKUP CHAIN ==="

module A
  def test_method
    "Module A"
  end
end

module B
  def test_method
    "Module B"
  end
end

module C
  def test_method
    "Module C"
  end
end

class TestClass
  include A
  include B
  include C
  
  def test_method
    "TestClass"
  end
end

obj = TestClass.new
puts "Method result: #{obj.test_method}"
puts "Ancestors: #{TestClass.ancestors}"

puts "\n=== AUTHENTICATION SYSTEM EXAMPLE ==="

module Authenticable
  def authenticate(password)
    password == @password
  end
  
  def login(password)
    if authenticate(password)
      @logged_in = true
      @login_time = Time.now
      "Login successful"
    else
      "Login failed"
    end
  end
  
  def logout
    @logged_in = false
    @login_time = nil
    "Logged out"
  end
  
  def logged_in?
    @logged_in || false
  end
  
  def session_duration
    return 0 unless @login_time
    Time.now - @login_time
  end
end

module Authorizable
  def can_read?(resource)
    @permissions.include?(:read) || @permissions.include?(:all)
  end
  
  def can_write?(resource)
    @permissions.include?(:write) || @permissions.include?(:all)
  end
  
  def can_delete?(resource)
    @permissions.include?(:delete) || @permissions.include?(:all)
  end
  
  def grant_permission(permission)
    @permissions << permission unless @permissions.include?(permission)
  end
  
  def revoke_permission(permission)
    @permissions.delete(permission)
  end
end

class User
  include Authenticable
  include Authorizable
  
  attr_reader :username, :permissions
  
  def initialize(username, password, permissions = [:read])
    @username = username
    @password = password
    @permissions = permissions
  end
end

class Admin
  include Authenticable
  include Authorizable
  
  attr_reader :username, :permissions
  
  def initialize(username, password)
    @username = username
    @password = password
    @permissions = [:read, :write, :delete, :all]
  end
end

# Usage
user = User.new("john_doe", "password123")
admin = Admin.new("admin", "admin123")

puts user.login("password123")      # => "Login successful"
puts "User can read? #{user.can_read?("document")}"
puts "User can write? #{user.can_write?("document")}"

puts admin.login("admin123")        # => "Login successful"
puts "Admin can delete? #{admin.can_delete?("user")}"

puts "\n=== VALIDATION FRAMEWORK EXAMPLE ==="

module Validatable
  def self.included(base)
    base.extend(ClassMethods)
  end
  
  module ClassMethods
    def validates(attribute, options = {})
      validations[attribute] << options
    end
    
    def validations
      @validations ||= Hash.new { |h, k| h[k] = [] }
    end
  end
  
  def valid?
    errors.clear
    
    self.class.validations.each do |attribute, validation_rules|
      value = send(attribute)
      
      validation_rules.each do |rule|
        validate_presence(attribute, value) if rule[:presence]
        validate_length(attribute, value, rule[:length]) if rule[:length]
        validate_format(attribute, value, rule[:format]) if rule[:format]
        validate_inclusion(attribute, value, rule[:in]) if rule[:in]
      end
    end
    
    errors.empty?
  end
  
  def errors
    @errors ||= []
  end
  
  private
  
  def validate_presence(attribute, value)
    errors << "#{attribute} can't be blank" if value.nil? || value.to_s.strip.empty?
  end
  
  def validate_length(attribute, value, length_options)
    return unless value.respond_to?(:length)
    
    if length_options[:minimum] && value.length < length_options[:minimum]
      errors << "#{attribute} is too short (minimum is #{length_options[:minimum]} characters)"
    end
    
    if length_options[:maximum] && value.length > length_options[:maximum]
      errors << "#{attribute} is too long (maximum is #{length_options[:maximum]} characters)"
    end
  end
  
  def validate_format(attribute, value, format)
    return unless value.respond_to?(:match?)
    errors << "#{attribute} is invalid" unless value.match?(format)
  end
  
  def validate_inclusion(attribute, value, inclusion_list)
    errors << "#{attribute} is not included in the list" unless inclusion_list.include?(value)
  end
end

class User
  include Validatable
  
  attr_accessor :name, :email, :age, :role
  
  validates :name, presence: true, length: { minimum: 2, maximum: 50 }
  validates :email, presence: true, format: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
  validates :age, presence: true, inclusion: 18..100
  validates :role, presence: true, inclusion: ["user", "admin", "moderator"]
  
  def initialize(name: nil, email: nil, age: nil, role: nil)
    @name = name
    @email = email
    @age = age
    @role = role
  end
end

# Usage
valid_user = User.new(
  name: "John Doe",
  email: "john@example.com",
  age: 25,
  role: "user"
)

invalid_user = User.new(
  name: "J",
  email: "invalid-email",
  age: 15,
  role: "superuser"
)

puts "Valid user valid? #{valid_user.valid?}"
puts "Valid user errors: #{valid_user.errors}"

puts "Invalid user valid? #{invalid_user.valid?}"
puts "Invalid user errors: #{invalid_user.errors.join(', ')}"

puts "\n=== PLUGIN SYSTEM EXAMPLE ==="

module Plugin
  def self.included(base)
    base.extend(ClassMethods)
    PluginRegistry.register(base)
  end
  
  module ClassMethods
    def plugin_name(name)
      @plugin_name = name
    end
    
    def get_plugin_name
      @plugin_name || self.name
    end
    
    def plugin_version(version)
      @plugin_version = version
    end
    
    def get_plugin_version
      @plugin_version || "1.0.0"
    end
  end
  
  def execute(context)
    raise NotImplementedError, "Plugin must implement execute method"
  end
end

module PluginRegistry
  @plugins = []
  
  def self.register(plugin_class)
    @plugins << plugin_class
  end
  
  def self.plugins
    @plugins
  end
  
  def self.find_by_name(name)
    @plugins.find { |plugin| plugin.get_plugin_name == name }
  end
  
  def self.execute_plugin(name, context)
    plugin = find_by_name(name)
    return "Plugin not found" unless plugin
    
    plugin.new.execute(context)
  end
end

class LoggerPlugin
  include Plugin
  
  plugin_name "Logger"
  plugin_version "1.2.0"
  
  def execute(context)
    puts "[LOGGER] #{context}"
    "Logged: #{context}"
  end
end

class UppercasePlugin
  include Plugin
  
  plugin_name "Uppercase"
  plugin_version "1.0.0"
  
  def execute(context)
    result = context.upcase
    puts "[UPPERCASE] #{result}"
    result
  end
end

class TimestampPlugin
  include Plugin
  
  plugin_name "Timestamp"
  plugin_version "1.1.0"
  
  def execute(context)
    result = "#{Time.now}: #{context}"
    puts "[TIMESTAMP] #{result}"
    result
  end
end

# Usage
puts "Available plugins:"
PluginRegistry.plugins.each do |plugin|
  puts "- #{plugin.get_plugin_name} v#{plugin.get_plugin_version}"
end

puts "\nExecuting plugins:"
PluginRegistry.execute_plugin("Logger", "System started")
PluginRegistry.execute_plugin("Uppercase", "hello world")
PluginRegistry.execute_plugin("Timestamp", "Processing complete")
