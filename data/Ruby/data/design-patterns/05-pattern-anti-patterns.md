# Design Pattern Anti-Patterns in Ruby
# Common pitfalls and how to avoid them

## 🎯 Overview

Design patterns are powerful tools, but they can be misapplied or overused. This guide covers common anti-patterns in Ruby, their consequences, and better alternatives.

## 🚫 Common Anti-Patterns

### 1. Singleton Overuse

**Problem**: Using Singleton everywhere when it's not needed.

```ruby
# ANTI-PATTERN: Singleton abuse
class GlobalConfig
  include Singleton
  
  def initialize
    @database_url = "localhost"
    @api_key = "secret"
    @timeout = 30
  end
  
  attr_accessor :database_url, :api_key, :timeout
end

# Usage problems:
class UserService
  def create_user(name)
    config = GlobalConfig.instance
    # Tight coupling to GlobalConfig
    Database.connect(config.database_url)
    # ... create user
  end
end

class OrderService
  def create_order(items)
    config = GlobalConfig.instance
    # Another tight coupling
    PaymentGateway.connect(config.api_key)
    # ... create order
  end
end

# BETTER: Dependency Injection
class Config
  attr_reader :database_url, :api_key, :timeout
  
  def initialize(database_url:, api_key:, timeout:)
    @database_url = database_url
    @api_key = api_key
    @timeout = timeout
  end
end

class UserService
  def initialize(config)
    @config = config
  end
  
  def create_user(name)
    Database.connect(@config.database_url)
    # ... create user
  end
end

# Usage with dependency injection
config = Config.new(
  database_url: "production-db.example.com",
  api_key: "prod-api-key",
  timeout: 60
)

user_service = UserService.new(config)
order_service = OrderService.new(config)
```

### 2. Factory Method Over-Engineering

**Problem**: Creating factories for simple object creation.

```ruby
# ANTI-PATTERN: Over-engineered factory
class UserFactory
  def self.create_user(name, email, age)
    User.new(name: name, email: email, age: age)
  end
  
  def self.create_admin_user(name, email, age)
    User.new(name: name, email: email, age: age, role: :admin)
  end
  
  def self.create_guest_user(name, email, age)
    User.new(name: name, email: email, age: age, role: :guest)
  end
end

# Usage problems:
user1 = UserFactory.create_user("John", "john@example.com", 30)
user2 = UserFactory.create_admin_user("Admin", "admin@example.com", 40)

# BETTER: Simple constructors or builders
class User
  attr_reader :name, :email, :age, :role
  
  def initialize(name:, email:, age:, role: :user)
    @name = name
    @email = email
    @age = age
    @role = role
  end
  
  def self.admin(name:, email:, age:)
    new(name: name, email: email, age: age, role: :admin)
  end
  
  def self.guest(name:, email:, age:)
    new(name: name, email: email, age: age, role: :guest)
  end
end

# Usage:
user1 = User.new(name: "John", email: "john@example.com", age: 30, role: :user)
user2 = User.admin(name: "Admin", email: "admin@example.com", age: 40)
user3 = User.guest(name: "Guest", email: "guest@example.com", age: 25)
```

### 3. Observer Over-Subscription

**Problem**: Creating too many observers for simple notifications.

```ruby
# ANTI-PATTERN: Observer overuse
class User
  include Observable
  
  def initialize(name)
    @name = name
    @email = nil
    @status = :inactive
  end
  
  def email=(email)
    @email = email
    changed
    notify_observers(self, :email_changed)
  end
  
  def status=(status)
    @status = status
    changed
    notify_observers(self, :status_changed)
  end
end

# Too many observers for simple changes
class EmailObserver
  def update(user, change_type)
    case change_type
    when :email_changed
      puts "Email changed for #{user.name}"
    when :status_changed
      puts "Status changed for #{user.name}"
    end
  end
end

class LoggingObserver
  def update(user, change_type)
    puts "Logging #{change_type} for #{user.name}"
  end
end

class AuditObserver
  def update(user, change_type)
    puts "Auditing #{change_type} for #{user.name}"
  end
end

class NotificationObserver
  def update(user, change_type)
    puts "Notifying #{change_type} for #{user.name}"
  end
end

# BETTER: Simple callbacks or events
class User
  attr_reader :name
  
  def initialize(name)
    @name = name
    @callbacks = {}
  end
  
  def on_email_changed(&block)
    @callbacks[:email_changed] = block
  end
  
  def on_status_changed(&block)
    @callbacks[:status_changed] = block
  end
  
  def email=(email)
    @email = email
    @callbacks[:email_changed]&.call(self)
  end
  
  def status=(status)
    @status = status
    @callbacks[:status_changed]&.call(self)
  end
end

# Usage:
user = User.new("John")
user.on_email_changed { |u| puts "Email changed to #{u.email}" }
user.on_status_changed { |u| puts "Status changed to #{u.status}" }

user.email = "john@example.com"
user.status = :active
```

### 4. Decorator Explosion

**Problem**: Creating too many decorator classes for simple features.

```ruby
# ANTI-PATTERN: Too many decorators
class Text
  def initialize(content)
    @content = content
  end
  
  def render
    @content
  end
end

class BoldDecorator
  def initialize(text)
    @text = text
  end
  
  def render
    "**#{@text.render}**"
  end
end

class ItalicDecorator
  def initialize(text)
    @text = text
  end
  
  def render
    "*#{@text.render}*"
  end
end

class UnderlineDecorator
  def initialize(text)
    @text = text
  end
  
  def render
    "__#{@text.render}__"
  end
end

class ColorDecorator
  def initialize(text, color)
    @text = text
    @color = color
  end
  
  def render
    "<span style='color: #{@color}'>#{@text.render}</span>"
  end
end

# Usage becomes complex:
text = Text.new("Hello")
decorated = BoldDecorator.new(
  ItalicDecorator.new(
    UnderlineDecorator.new(
      ColorDecorator.new(text, "red")
    )
  )
)
puts decorated.render

# BETTER: Method chaining or format strings
class TextFormatter
  def initialize(content)
    @content = content
    @format_options = {}
  end
  
  def bold
    @format_options[:bold] = true
    self
  end
  
  def italic
    @format_options[:italic] = true
    self
  end
  
  def underline
    @format_options[:underline] = true
    self
  end
  
  def color(color)
    @format_options[:color] = color
    self
  end
  
  def render
    result = @content
    
    result = "**#{result}**" if @format_options[:bold]
    result = "*#{result}*" if @format_options[:italic]
    result = "__#{result}__" if @format_options[:underline]
    
    if @format_options[:color]
      result = "<span style='color: #{@format_options[:color]}'>#{result}</span>"
    end
    
    result
  end
end

# Usage:
formatter = TextFormatter.new("Hello")
result = formatter.bold.italic.underline.color("red").render
puts result
```

### 5. Adapter Over-Abstraction

**Problem**: Creating adapters when simple inheritance would work.

```ruby
# ANTI-PATTERN: Unnecessary adapter
class LegacyLogger
  def log_message(message)
    puts "LEGACY: #{message}"
  end
end

class ModernLogger
  def info(message)
    puts "INFO: #{message}"
  end
  
  def warn(message)
    puts "WARN: #{message}"
  end
  
  def error(message)
    puts "ERROR: #{message}"
  end
end

class LoggerAdapter
  def initialize(legacy_logger)
    @logger = legacy_logger
  end
  
  def info(message)
    @logger.log_message(message)
  end
  
  def warn(message)
    @logger.log_message(message)
  end
  
  def error(message)
    @logger.log_message(message)
  end
end

# Usage problems:
legacy = LegacyLogger.new
adapter = LoggerAdapter.new(legacy)
adapter.info("This is an info message")

# BETTER: Inherit or use composition directly
class ImprovedLegacyLogger < LegacyLogger
  def info(message)
    log_message("INFO: #{message}")
  end
  
  def warn(message)
    log_message("WARN: #{message}")
  end
  
  def error(message)
    log_message("ERROR: #{message}")
  end
end

# Or use composition with delegation
class DelegatingLogger
  def initialize(logger)
    @logger = logger
  end
  
  def info(message)
    @logger.log_message("INFO: #{message}")
  end
  
  def warn(message)
    @logger.log_message("WARN: #{message}")
  end
  
  def error(message)
    @logger.log_message("ERROR: #{message}")
  end
end
```

## 🔍 Pattern Selection Anti-Patterns

### 1. Pattern for Pattern's Sake

**Problem**: Using patterns because they exist, not because they solve a problem.

```ruby
# ANTI-PATTERN: Using patterns unnecessarily
class SingletonFactoryManagerObserver
  include Singleton
  
  def initialize
    @factories = []
    @observers = []
  end
  
  def add_factory(factory)
    @factories << factory
    notify_observers(:factory_added, factory)
  end
  
  def add_observer(observer)
    @observers << observer
  end
  
  def notify_observers(event, data)
    @observers.each { |observer| observer.update(event, data) }
  end
  
  def create_product(type, *args)
    factory = @factories.find { |f| f.can_create?(type) }
    factory&.create(*args)
  end
end

# BETTER: Simple class that actually solves a problem
class ProductManager
  def initialize
    @products = {}
  end
  
  def register_product(type, creator)
    @products[type] = creator
  end
  
  def create(type, *args)
    creator = @products[type]
    raise "Unknown product type: #{type}" unless creator
    
    creator.call(*args)
  end
end

# Usage:
manager = ProductManager.new
manager.register_product(:widget, ->(name) { Widget.new(name) })
manager.register_product(:gadget, ->(name) { Gadget.new(name) })

widget = manager.create(:widget, "MyWidget")
```

### 2. Pattern Inflation

**Problem**: Combining multiple patterns when a simple solution would work.

```ruby
# ANTI-PATTERN: Pattern inflation
class ComplexUserManager
  include Singleton
  
  def initialize
    @users = []
    @observers = []
    @command_history = []
  end
  
  # Observer pattern
  def add_observer(observer)
    @observers << observer
  end
  
  def notify_observers(event, data)
    @observers.each { |observer| observer.update(event, data) }
  end
  
  # Command pattern
  def execute_command(command)
    @command_history << command
    result = command.execute
    notify_observers(:command_executed, command)
    result
  end
  
  # Iterator pattern
  def create_iterator
    UserIterator.new(@users)
  end
  
  # Strategy pattern
  def set_search_strategy(strategy)
    @search_strategy = strategy
  end
  
  def search_users(criteria)
    @search_strategy.search(@users, criteria)
  end
  
  # ... more patterns
end

# BETTER: Simple class that does what's needed
class UserManager
  def initialize
    @users = []
    @observers = []
  end
  
  def add_user(user)
    @users << user
    notify_observers(:user_added, user)
  end
  
  def remove_user(user)
    @users.delete(user)
    notify_observers(:user_removed, user)
  end
  
  def find_user(id)
    @users.find { |user| user.id == id }
  end
  
  def all_users
    @users.dup
  end
  
  def add_observer(observer)
    @observers << observer
  end
  
  private
  
  def notify_observers(event, data)
    @observers.each { |observer| observer.update(event, data) }
  end
end
```

## 🚨 Performance Anti-Patterns

### 1. Singleton in Multi-Threaded Environments

**Problem**: Singleton can cause performance bottlenecks.

```ruby
# ANTI-PATTERN: Singleton in multi-threaded code
class DatabaseConnection
  include Singleton
  
  def initialize
    @connection = connect_to_database
    puts "Database connection created"
  end
  
  def execute_query(sql)
    @connection.execute(sql)
  end
  
  private
  
  def connect_to_database
    # Expensive connection setup
    sleep(1)
    "connection_object"
  end
end

# Usage problems in multi-threaded code
threads = 10.times.map do
  Thread.new do
    db = DatabaseConnection.instance  # All threads wait for instance creation
    100.times { |i| db.execute_query("SELECT * FROM table_#{i}") }
  end
end

threads.each(&:join)

# BETTER: Connection pool or dependency injection
class ConnectionPool
  def initialize(size: 5)
    @pool = Queue.new
    size.times { @pool << create_connection }
    @created = size
  end
  
  def with_connection
    connection = @pool.pop
    begin
      yield connection
    ensure
      @pool.push(connection)
    end
  end
  
  private
  
  def create_connection
    puts "Creating connection #{@created + 1}"
    "connection_#{@created + 1}"
  end
end

# Usage
pool = ConnectionPool.new(size: 5)

threads = 10.times.map do
  Thread.new do
    pool.with_connection do |connection|
      100.times { |i| connection.execute("SELECT * FROM table_#{i}") }
    end
  end
end

threads.each(&:join)
```

### 2. Observer Memory Leaks

**Problem**: Observers not being removed properly.

```ruby
# ANTI-PATTERN: Memory leaks in observer pattern
class EventPublisher
  def initialize
    @observers = []
  end
  
  def subscribe(observer)
    @observers << observer
  end
  
  def publish(event)
    @observers.each { |observer| observer.handle(event) }
  end
end

# Usage problems:
publisher = EventPublisher.new

# Observers accumulate, never removed
1000.times do |i|
  observer = Object.new
  def observer.handle(event)
    puts "Handling #{event}"
  end
  
  publisher.subscribe(observer)
end

# BETTER: Weak references or explicit cleanup
require 'set'

class ImprovedEventPublisher
  def initialize
    @observers = Set.new
    @mutex = Mutex.new
  end
  
  def subscribe(observer)
    @mutex.synchronize do
      @observers << observer
    end
  end
  
  def unsubscribe(observer)
    @mutex.synchronize do
      @observers.delete(observer)
    end
  end
  
  def publish(event)
    @mutex.synchronize do
      @observers.each { |observer| observer.handle(event) }
    end
  end
  
  def clear_observers
    @mutex.synchronize { @observers.clear }
  end
end

# Usage with proper cleanup
publisher = ImprovedEventPublisher.new

observers = []
1000.times do |i|
  observer = Object.new
  def observer.handle(event)
    puts "Handling #{event}"
  end
  
  publisher.subscribe(observer)
  observers << observer
end

# Clean up when done
observers.each { |observer| publisher.unsubscribe(observer) }
```

## 🎯 When to Use vs. When to Avoid

### 1. Use Patterns When:

- **Clear problem exists** with a known solution
- **Complexity is managed** and reduced
- **Flexibility is needed** for future changes
- **Communication is improved** between team members
- **Code is reused** in multiple contexts

### 2. Avoid Patterns When:

- **Simple solution exists** that's more readable
- **Problem is not yet clear** (YAGNI principle)
- **Pattern adds complexity** without benefit
- **Ruby has built-in solutions** (blocks, modules, duck typing)
- **Over-engineering** for simple use cases

### 3. Ruby-Specific Considerations:

```ruby
# Use Ruby features instead of patterns when possible

# Instead of Strategy pattern:
class Calculator
  def initialize(operation = :add)
    @operation = operation
  end
  
  def calculate(a, b)
    case @operation
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

# Instead of Iterator pattern:
class DataCollection
  include Enumerable
  
  def initialize(data)
    @data = data
  end
  
  def each(&block)
    @data.each(&block)
  end
end

# Instead of Command pattern:
class TaskRunner
  def initialize
    @tasks = []
  end
  
  def add_task(&block)
    @tasks << block
  end
  
  def run_tasks
    @tasks.each(&:call)
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Identify Anti-Patterns**: Find and fix anti-patterns in existing code
2. **Refactor to Simpler**: Remove unnecessary patterns
3. **Ruby Native Solutions**: Replace patterns with Ruby features

### Intermediate Exercises

1. **Pattern Selection**: Choose appropriate patterns for problems
2. **Performance Analysis**: Identify performance issues with patterns
3. **Memory Management**: Fix memory leaks in observer patterns

### Advanced Exercises

1. **Pattern Combination**: Combine patterns effectively
2. **Anti-Pattern Detection**: Create tools to detect anti-patterns
3. **Refactoring Strategies**: Develop systematic refactoring approaches

---

## 🎯 Summary

Avoid design pattern anti-patterns by:

- **Problem-First Approach** - Solve real problems, not theoretical ones
- **Simplicity Principle** - Choose the simplest solution
- **Ruby Idioms** - Use Ruby's native features
- **Performance Awareness** - Consider performance implications
- **Regular Refactoring** - Remove unnecessary complexity

Remember: "The best code is no code at all. The second best is clean, simple code." - Yukihiro Matsumoto
