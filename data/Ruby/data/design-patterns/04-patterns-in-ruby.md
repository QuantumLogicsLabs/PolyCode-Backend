# Design Patterns in Ruby
# Ruby-specific implementations and idiomatic patterns

## 🎯 Overview

Ruby's dynamic nature and metaprogramming capabilities provide unique opportunities for implementing design patterns. This guide explores Ruby-specific pattern implementations and idiomatic alternatives to classic patterns.

## 🔧 Ruby-Specific Pattern Implementations

### 1. Singleton with Modules

Ruby provides elegant ways to implement the Singleton pattern using modules and class methods.

```ruby
# Classic Singleton implementation
class ClassicSingleton
  include Singleton
  
  def initialize
    @created_at = Time.now
    @instance_id = object_id
  end
  
  def info
    "Singleton created at #{@created_at}, ID: #{@instance_id}"
  end
end

# Ruby idiomatic singleton using class methods
class IdiomaticSingleton
  def self.instance
    @instance ||= new
  end
  
  private
  
  def initialize
    @created_at = Time.now
  end
  
  def info
    "Singleton created at #{@created_at}"
  end
end

# Module-based singleton
module SingletonModule
  def self.included(base)
    base.extend(ClassMethods)
  end
  
  module ClassMethods
    def instance
      @instance ||= new
    end
  end
end

class ModuleSingleton
  include SingletonModule
  
  def initialize
    @created_at = Time.now
  end
  
  def info
    "Module singleton created at #{@created_at}"
  end
end

# Usage
puts "Classic Singleton:"
instance1 = ClassicSingleton.instance
instance2 = ClassicSingleton.instance
puts "Same instance: #{instance1.object_id == instance2.object_id}"
puts instance1.info

puts "\nIdiomatic Singleton:"
instance3 = IdiomaticSingleton.instance
instance4 = IdiomaticSingleton.instance
puts "Same instance: #{instance3.object_id == instance4.object_id}"
puts instance3.info

puts "\nModule Singleton:"
instance5 = ModuleSingleton.instance
instance6 = ModuleSingleton.instance
puts "Same instance: #{instance5.object_id == instance6.object_id}"
puts instance5.info
```

### 2. Strategy Pattern with Duck Typing

Ruby's duck typing makes the Strategy pattern more flexible without explicit interfaces.

```ruby
# No explicit interface needed - duck typing
class TaxCalculator
  def initialize(strategy)
    @strategy = strategy
  end
  
  def calculate(amount)
    @strategy.calculate_tax(amount)
  end
  
  def change_strategy(strategy)
    @strategy = strategy
  end
end

# Different tax strategies - no inheritance needed
class USTaxCalculator
  def calculate_tax(amount)
    amount * 0.07  # 7% sales tax
  end
end

class EUTaxCalculator
  def calculate_tax(amount)
    amount * 0.20  # 20% VAT
  end
end

class CanadianTaxCalculator
  def calculate_tax(amount)
    amount * 0.05  # 5% GST
  end
end

# Lambda-based strategy
class LambdaTaxCalculator
  def initialize(tax_rate)
    @calculate_tax = ->(amount) { amount * tax_rate }
  end
  
  def calculate_tax(amount)
    @calculate_tax.call(amount)
  end
end

# Usage
calculator = TaxCalculator.new(USTaxCalculator.new)
puts "US Tax on $100: $#{calculator.calculate(100)}"

calculator.change_strategy(EUTaxCalculator.new)
puts "EU Tax on $100: $#{calculator.calculate(100)}"

calculator.change_strategy(CanadianTaxCalculator.new)
puts "Canadian Tax on $100: $#{calculator.calculate(100)}"

# Lambda strategy
lambda_calc = TaxCalculator.new(LambdaTaxCalculator.new(0.15))
puts "Custom Tax (15%) on $100: $#{lambda_calc.calculate(100)}"
```

### 3. Observer with Observable Module

Ruby's standard library provides the Observable module for implementing the Observer pattern.

```ruby
require 'observer'

class WeatherStation
  include Observable
  
  def initialize
    @temperature = 0
    @humidity = 0
    @pressure = 0
  end
  
  def set_measurements(temperature, humidity, pressure)
    @temperature = temperature
    @humidity = humidity
    @pressure = pressure
    
    changed
    notify_observers(@temperature, @humidity, @pressure)
  end
end

class WeatherDisplay
  def update(temperature, humidity, pressure)
    puts "Display: #{temperature}°C, #{humidity}%, #{pressure} hPa"
  end
end

class WeatherLogger
  def update(temperature, humidity, pressure)
    timestamp = Time.now.strftime("%Y-%m-%d %H:%M:%S")
    puts "Log [#{timestamp}]: #{temperature}°C, #{humidity}%, #{pressure} hPa"
  end
end

# Usage
station = WeatherStation.new
display = WeatherDisplay.new
logger = WeatherLogger.new

station.add_observer(display)
station.add_observer(logger)

station.set_measurements(25.5, 65, 1013)
station.set_measurements(26.0, 70, 1015)

# Custom observer with callbacks
class CallbackObserver
  def initialize(&callback)
    @callback = callback
  end
  
  def update(*args)
    @callback.call(*args)
  end
end

custom_observer = CallbackObserver.new do |temp, humidity, pressure|
  puts "Callback: Alert - Temperature #{temp}°C"
end

station.add_observer(custom_observer)
station.set_measurements(30.0, 80, 1018)
```

### 4. Decorator with Method Missing

Ruby's `method_missing` allows for dynamic decoration without explicit wrapper classes.

```ruby
class TextDecorator
  def initialize(text)
    @text = text
    @decorations = []
  end
  
  def method_missing(method_name, *args, &block)
    if method_name.to_s.start_with?('decorate_')
      decoration_type = method_name.to_s.sub('decorate_', '')
      add_decoration(decoration_type, *args)
    else
      decorated_text
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.start_with?('decorate_') || super
  end
  
  def add_decoration(type, *args)
    @decorations << { type: type, args: args }
    self
  end
  
  def decorated_text
    result = @text.dup
    
    @decorations.each do |decoration|
      result = apply_decoration(result, decoration[:type], decoration[:args])
    end
    
    result
  end
  
  private
  
  def apply_decoration(text, type, args)
    case type
    when 'bold'
      "**#{text}**"
    when 'italic'
      "*#{text}*"
    when 'underline'
      "__#{text}__"
    when 'color'
      color = args.first
      "<span style='color: #{color}'>#{text}</span>"
    when 'size'
      size = args.first
      "<span style='font-size: #{size}px'>#{text}</span>"
    when 'prefix'
      prefix = args.first
      "#{prefix} #{text}"
    when 'suffix'
      suffix = args.first
      "#{text} #{suffix}"
    else
      text
    end
  end
end

# Usage
decorator = TextDecorator.new("Hello World")

decorator.decorate_bold
decorator.decorate_italic
decorator.decorate_color("red")
decorator.decorate_size(18)
decorator.decorate_prefix(">>")
decorator.decorate_suffix("<<")

puts decorator.decorated_text

# Fluent decorator with method chaining
class FluentTextDecorator
  def initialize(text)
    @text = text
  end
  
  def bold
    FluentTextDecorator.new("**#{@text}**")
  end
  
  def italic
    FluentTextDecorator.new("*#{@text}*")
  end
  
  def underline
    FluentTextDecorator.new("__#{@text}__")
  end
  
  def color(color)
    FluentTextDecorator.new("<span style='color: #{color}'>#{@text}</span>")
  end
  
  def size(size)
    FluentTextDecorator.new("<span style='font-size: #{size}px'>#{@text}</span>")
  end
  
  def to_s
    @text
  end
end

# Usage
fluent = FluentTextDecorator.new("Hello World")
result = fluent.bold.italic.color("blue").size(16).to_s
puts result
```

## 🎭 Metaprogramming Patterns

### 1. Template Method with Class Evaluation

Ruby's `class_eval` allows for dynamic template method implementations.

```ruby
class ReportGenerator
  def self.define_report_template(name, sections)
    define_method("generate_#{name}_report") do |data|
      report = "#{name.to_s.upcase} REPORT\n"
      report << "=" * 40 + "\n\n"
      
      sections.each do |section|
        report += "#{section.to_s.upcase}\n"
        report += "-" * section.length + "\n"
        report += format_section(section, data) + "\n\n"
      end
      
      report += "Generated at #{Time.now}\n"
    end
  end
  
  private
  
  def self.format_section(section, data)
    case section
    when :summary
      "Summary: #{data[:summary]}"
    when :details
      data[:details].map { |k, v| "#{k}: #{v}" }.join("\n")
    when :conclusion
      "Conclusion: #{data[:conclusion]}"
    else
      "#{section}: #{data[section]}"
    end
  end
end

# Define different report templates
ReportGenerator.define_report_template(:sales, [:summary, :details, :conclusion])
ReportGenerator.define_report_template(:inventory, [:summary, :items, :total_value])
ReportGenerator.define_report_template(:performance, [:summary, :metrics, :recommendations])

# Usage
generator = ReportGenerator.new

sales_data = {
  summary: "Sales increased by 15%",
  details: { "Q1": "$100K", "Q2": "$115K", "Q3": "$130K", "Q4": "$150K" },
  conclusion: "Strong growth trend continues"
}

puts generator.generate_sales_report(sales_data)

inventory_data = {
  summary: "Total inventory value: $500K",
  items: { "Laptops": 100, "Phones": 200, "Tablets": 50 },
  total_value: 500000
}

puts generator.generate_inventory_report(inventory_data)
```

### 2. Builder with Dynamic Methods

Ruby's metaprogramming allows for dynamic builder method creation.

```ruby
class DynamicQueryBuilder
  def initialize(table_name)
    @table_name = table_name
    @conditions = []
    @order_by = nil
    @limit = nil
    @joins = []
  end
  
  def self.define_query_methods(*methods)
    methods.each do |method|
      define_method(method) do |value|
        @conditions << "#{method} = '#{value}'"
        self
      end
    end
  end
  
  def where(condition)
    @conditions << condition
    self
  end
  
  def order(field, direction = 'ASC')
    @order_by = "#{field} #{direction}"
    self
  end
  
  def limit(count)
    @limit = count
    self
  end
  
  def join(table, on_condition)
    @joins << "INNER JOIN #{table} ON #{on_condition}"
    self
  end
  
  def to_sql
    sql = "SELECT * FROM #{@table_name}"
    
    sql += " WHERE #{@conditions.join(' AND ')}" unless @conditions.empty?
    sql += " #{@joins.join(' ')}" unless @joins.empty?
    sql += " ORDER BY #{@order_by}" if @order_by
    sql += " LIMIT #{@limit}" if @limit
    
    sql
  end
end

# Define dynamic query methods for a specific table
class UserQueryBuilder < DynamicQueryBuilder
  define_query_methods :name, :email, :age, :status
  
  def initialize
    super('users')
  end
end

class ProductQueryBuilder < DynamicQueryBuilder
  define_query_methods :name, :category, :price, :in_stock
  
  def initialize
    super('products')
  end
end

# Usage
user_query = UserQueryBuilder.new
  .name('John')
  .email('john@example.com')
  .age(25)
  .status('active')
  .order('created_at', 'DESC')
  .limit(10)

puts user_query.to_sql

product_query = ProductQueryBuilder.new
  .name('Laptop')
  .category('Electronics')
  .price(999.99)
  .in_stock(true)
  .join('categories', 'products.category_id = categories.id')

puts product_query.to_sql
```

### 3. Chain of Responsibility with Method Missing

```ruby
class HandlerChain
  def initialize
    @handlers = []
  end
  
  def add_handler(handler)
    @handlers << handler
    self
  end
  
  def handle(request)
    @handlers.each do |handler|
      result = handler.process(request)
      return result if result
    end
    nil
  end
  
  def method_missing(method_name, *args, &block)
    if method_name.to_s.start_with?('handle_')
      request_type = method_name.to_s.sub('handle_', '')
      handle({ type: request_type, args: args })
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.start_with?('handle_') || super
  end
end

class AuthenticationHandler
  def process(request)
    return nil unless request[:type] == 'authentication'
    
    username, password = request[:args]
    
    if authenticate(username, password)
      { success: true, user: find_user(username) }
    else
      { success: false, error: 'Invalid credentials' }
    end
  end
  
  private
  
  def authenticate(username, password)
    # Simplified authentication
    username == 'admin' && password == 'secret'
  end
  
  def find_user(username)
    { id: 1, name: 'Admin User', role: 'administrator' }
  end
end

class AuthorizationHandler
  def process(request)
    return nil unless request[:type] == 'authorization'
    
    user, action = request[:args]
    
    if authorize(user, action)
      { success: true, authorized: true }
    else
      { success: false, error: 'Unauthorized' }
    end
  end
  
  private
  
  def authorize(user, action)
    # Simplified authorization
    return true if user[:role] == 'administrator'
    return true if %w[read view].include?(action)
    false
  end
end

class LoggingHandler
  def process(request)
    puts "Logging request: #{request[:type]} with args: #{request[:args]}"
    nil  # Continue to next handler
  end
end

# Usage
chain = HandlerChain.new
  .add_handler(LoggingHandler.new)
  .add_handler(AuthenticationHandler.new)
  .add_handler(AuthorizationHandler.new)

# Direct method calls using method_missing
result1 = chain.handle_authentication('admin', 'secret')
puts "Authentication result: #{result1}"

result2 = chain.handle_authorization({ id: 1, name: 'Admin', role: 'administrator' }, 'read')
puts "Authorization result: #{result2}"

# Generic handling
result3 = chain.handle({ type: 'authentication', args: ['user', 'pass'] })
puts "Generic result: #{result3}"
```

## 🎨 Idiomatic Ruby Patterns

### 1. Null Object Pattern

Ruby's flexibility makes the Null Object pattern very elegant.

```ruby
# Instead of checking for nil everywhere
class User
  attr_reader :name, :email
  
  def initialize(name, email)
    @name = name
    @email = email
  end
  
  def display_name
    "#{name} (#{email})"
  end
  
  def send_welcome_email
    puts "Sending welcome email to #{email}"
  end
end

# Null Object implementation
class NullUser
  def name
    "Guest"
  end
  
  def email
    "guest@example.com"
  end
  
  def display_name
    "Guest (guest@example.com)"
  end
  
  def send_welcome_email
    # No operation
  end
end

# Usage without Null Object
def display_user_info(user)
  if user
    puts user.display_name
    user.send_welcome_email
  else
    puts "No user provided"
  end
end

# Usage with Null Object
def display_user_info_idiomatic(user)
  user = NullUser.new unless user
  puts user.display_name
  user.send_welcome_email
end

# Test both approaches
display_user_info(nil)
display_user_info_idiomatic(nil)
```

### 2. Dependency Injection with Blocks

Ruby blocks make dependency injection very natural.

```ruby
class DatabaseConnection
  def initialize(config = {})
    @config = default_config.merge(config)
    @connected = false
  end
  
  def connect
    puts "Connecting to database with config: #{@config}"
    @connected = true
  end
  
  def disconnect
    puts "Disconnecting from database"
    @connected = false
  end
  
  def connected?
    @connected
  end
  
  def execute_query(sql)
    raise "Not connected" unless @connected
    puts "Executing: #{sql}"
    "Query result"
  end
  
  private
  
  def default_config
    {
      host: 'localhost',
      port: 5432,
      database: 'myapp'
    }
  end
end

class UserService
  def initialize(db: nil, &block)
    @db = db || DatabaseConnection.new
    @db.instance_eval(&block) if block_given?
    @db.connect
  end
  
  def find_user(id)
    @db.execute_query("SELECT * FROM users WHERE id = #{id}")
  end
  
  def create_user(user_data)
    @db.execute_query("INSERT INTO users (name, email) VALUES ('#{user_data[:name]}', '#{user_data[:email]}')")
  end
end

# Usage with block-based dependency injection
service1 = UserService.new do |db|
  db.config = { host: 'prod-server', database: 'production' }
end

service2 = UserService.new(db: DatabaseConnection.new)

# Method chaining with dependency injection
class ReportService
  def initialize(db: nil, formatter: nil, &block)
    @db = db || DatabaseConnection.new
    @formatter = formatter || DefaultFormatter.new
    
    @db.instance_eval(&block) if block_given?
    @db.connect
  end
  
  def generate_user_report
    data = @db.execute_query("SELECT * FROM users")
    @formatter.format(data)
  end
end

class DefaultFormatter
  def format(data)
    "Report: #{data}"
  end
end

class JSONFormatter
  def format(data)
    "{ \"users\": #{data} }"
  end
end

# Usage
report1 = ReportService.new(formatter: JSONFormatter.new) do |db|
  db.config = { database: 'reporting_db' }
end

report2 = ReportService.new do |db|
  db.config = { host: 'analytics-server' }
end
```

### 3. Proxy with Forwardable

Ruby's Forwardable module makes proxy implementation clean.

```ruby
require 'forwardable'

class RealService
  def expensive_operation(param)
    puts "Performing expensive operation with #{param}"
    "Result for #{param}"
  end
  
  def quick_operation(param)
    puts "Performing quick operation with #{param}"
    "Quick result for #{param}"
  end
  
  def status
    "Service is running"
  end
end

class ServiceProxy
  extend Forwardable
  
  def initialize(service)
    @service = service
    @cache = {}
    @access_count = {}
  end
  
  def_delegators :@service, :status
  
  def expensive_operation(param)
    cache_key = "expensive_#{param}"
    
    if @cache.key?(cache_key)
      @access_count[cache_key] = (@access_count[cache_key] || 0) + 1
      puts "Returning cached result for #{param} (accessed #{@access_count[cache_key]} times)"
      return @cache[cache_key]
    end
    
    puts "Cache miss for #{param}, calling real service"
    result = @service.expensive_operation(param)
    @cache[cache_key] = result
    @access_count[cache_key] = 1
    result
  end
  
  def quick_operation(param)
    # Quick operations bypass cache
    @service.quick_operation(param)
  end
  
  def cache_stats
    {
      cache_size: @cache.size,
      access_counts: @access_count
    }
  end
  
  def clear_cache
    @cache.clear
    @access_count.clear
  end
end

# Usage
real_service = RealService.new
proxy = ServiceProxy.new(real_service)

# First call - cache miss
puts proxy.expensive_operation("test1")

# Second call - cache hit
puts proxy.expensive_operation("test1")

# Quick operation bypasses cache
puts proxy.quick_operation("test2")

# Check cache status
puts "Cache stats: #{proxy.cache_stats}"

# Status is delegated directly
puts proxy.status
```

## 🎯 Performance Considerations

### 1. Lazy Loading with Method Missing

```ruby
class LazyLoader
  def initialize
    @loaded_objects = {}
    @loaders = {}
  end
  
  def define_lazy_loader(name, &loader)
    @loaders[name] = loader
    define_method(name) do
      load_object(name)
    end
  end
  
  private
  
  def load_object(name)
    return @loaded_objects[name] if @loaded_objects.key?(name)
    
    loader = @loaders[name]
    raise "No loader defined for #{name}" unless loader
    
    @loaded_objects[name] = loader.call
    @loaded_objects[name]
  end
end

# Usage
loader = LazyLoader.new

loader.define_lazy_loader(:heavy_data) do
  puts "Loading heavy data..."
  sleep(1)  # Simulate expensive loading
  "Heavy data loaded"
end

loader.define_lazy_loader(:config_data) do
  puts "Loading configuration..."
  { database: "myapp", host: "localhost" }
end

puts "Loader created, data not loaded yet"
puts loader.heavy_data  # Data is loaded now
puts loader.heavy_data  # Returns cached data
puts loader.config_data
```

### 2. Memoization for Performance

```ruby
class MemoizedCalculator
  def initialize
    @cache = {}
  end
  
  def expensive_calculation(x, y)
    cache_key = "#{x}_#{y}"
    return @cache[cache_key] if @cache.key?(cache_key)
    
    puts "Performing expensive calculation for #{x}, #{y}"
    result = x * y + Math.sqrt(x + y)
    @cache[cache_key] = result
    result
  end
  
  def clear_cache
    @cache.clear
  end
end

# Ruby's built-in memoization
class RubyMemoizedCalculator
  def initialize
    @memoized_cache = {}
  end
  
  def expensive_calculation(x, y)
    @memoized_cache[[x, y]] ||= begin
      puts "Performing expensive calculation for #{x}, #{y}"
      x * y + Math.sqrt(x + y)
    end
  end
  
  def clear_cache
    @memoized_cache.clear
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Ruby Singleton**: Implement different singleton approaches
2. **Duck Typing Strategy**: Create strategies without inheritance
3. **Observable Module**: Use Ruby's Observable module

### Intermediate Exercises

1. **Method Missing Decorator**: Implement decorator with method_missing
2. **Dynamic Builder**: Create builder with dynamic methods
3. **Forwardable Proxy**: Use Forwardable for clean proxy

### Advanced Exercises

1. **Metaprogramming Template**: Create dynamic template methods
2. **Chain of Responsibility**: Implement with method_missing
3. **Performance Optimization**: Add caching and lazy loading

---

## 🎯 Summary

Ruby-specific pattern implementations provide:

- **Dynamic flexibility** - Metaprogramming capabilities
- **Idiomatic alternatives** - Ruby-native solutions
- **Performance optimization** - Lazy loading and memoization
- **Clean syntax** - Blocks and method_missing
- **Type flexibility** - Duck typing over interfaces

Master Ruby's unique features to write elegant, efficient pattern implementations!
