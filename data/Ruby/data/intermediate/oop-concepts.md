# Ruby Object-Oriented Programming Concepts

## Classes and Objects

### Class Definition
```ruby
# Basic class definition
class Person
  # Constructor
  def initialize(name, age)
    @name = name
    @age = age
  end
  
  # Instance methods
  def greet
    "Hello, I'm #{@name} and I'm #{@age} years old"
  end
  
  def birthday
    @age += 1
    "Happy birthday! Now I'm #{@age}"
  end
  
  # Getter methods
  def name
    @name
  end
  
  def age
    @age
  end
  
  # Setter methods
  def name=(new_name)
    @name = new_name
  end
  
  def age=(new_age)
    @age = new_age
  end
end

# Creating objects
person1 = Person.new("John", 30)
person2 = Person.new("Jane", 25)

puts person1.greet  # "Hello, I'm John and I'm 30 years old"
puts person2.birthday  # "Happy birthday! Now I'm 26"
```

### Class Methods and Variables
```ruby
class Counter
  # Class variable
  @@count = 0
  
  # Class method
  def self.count
    @@count
  end
  
  def self.reset
    @@count = 0
  end
  
  # Instance method
  def initialize
    @@count += 1
  end
end

# Using class methods
puts Counter.count  # 0
c1 = Counter.new
c2 = Counter.new
puts Counter.count  # 2

Counter.reset
puts Counter.count  # 0
```

### Access Control
```ruby
class BankAccount
  # Public method
  def initialize(balance)
    @balance = balance
    @account_number = generate_account_number
  end
  
  # Public method
  def deposit(amount)
    @balance += amount if amount > 0
    "Deposited #{amount}. New balance: #{@balance}"
  end
  
  # Public method
  def withdraw(amount)
    if amount > 0 && @balance >= amount
      @balance -= amount
      "Withdrew #{amount}. New balance: #{@balance}"
    else
      "Insufficient funds"
    end
  end
  
  # Public method
  def balance
    @balance
  end
  
  private
  
  # Private method
  def generate_account_number
    rand(10000..99999)
  end
  
  protected
  
  # Protected method
  def internal_balance
    @balance
  end
end

account = BankAccount.new(1000)
puts account.deposit(500)  # "Deposited 500. New balance: 1500"
puts account.withdraw(200)  # "Withdrew 200. New balance: 1300"
# account.generate_account_number  # NoMethodError: private method
```

### Class Variables and Constants
```ruby
class MathConstants
  # Class constants
  PI = 3.141592653589793
  E = 2.718281828459045
  GOLDEN_RATIO = 1.618033988749895
  
  # Class variable
  @@calculations_performed = 0
  
  def self.calculations_performed
    @@calculations_performed
  end
  
  def self.circle_area(radius)
    @@calculations_performed += 1
    PI * radius ** 2
  end
  
  def self.reset_counter
    @@calculations_performed = 0
  end
end

puts MathConstants::PI  # 3.141592653589793
puts MathConstants.circle_area(5)  # 78.53981633974483
puts MathConstants.calculations_performed  # 1
```

## Inheritance

### Basic Inheritance
```ruby
# Base class
class Animal
  def initialize(name)
    @name = name
  end
  
  def speak
    "#{@name} makes a sound"
  end
  
  def eat
    "#{@name} is eating"
  end
  
  def sleep
    "#{@name} is sleeping"
  end
  
  protected
  
  def name
    @name
  end
end

# Derived class
class Dog < Animal
  def initialize(name, breed)
    super(name)  # Call parent constructor
    @breed = breed
  end
  
  # Override parent method
  def speak
    "#{name} barks: Woof! Woof!"
  end
  
  # New method specific to Dog
  def wag_tail
    "#{name} is wagging tail"
  end
  
  # Call parent method
  def eat
    super + " enthusiastically"
  end
  
  private
  
  def name
    super  # Call protected method from parent
  end
end

# Another derived class
class Cat < Animal
  def initialize(name, color)
    super(name)
    @color = color
  end
  
  def speak
    "#{name} meows: Meow!"
  end
  
  def purr
    "#{name} is purring"
  end
end

# Usage
dog = Dog.new("Buddy", "Golden Retriever")
cat = Cat.new("Whiskers", "orange")

puts dog.speak  # "Buddy barks: Woof! Woof!"
puts cat.speak  # "Whiskers meows: Meow!"
puts dog.wag_tail  # "Buddy is wagging tail"
puts cat.purr  # "Whiskers is purring"
```

### Method Overriding and Super
```ruby
class Vehicle
  def initialize(make, model, year)
    @make = make
    @model = model
    @year = year
    @speed = 0
  end
  
  def accelerate(amount)
    @speed += amount
    "#{@make} #{@model} accelerated to #{@speed} mph"
  end
  
  def brake(amount)
    @speed = [@speed - amount, 0].max
    "#{@make} #{@model} slowed to #{@speed} mph"
  end
  
  def info
    "#{@year} #{@make} #{@model} - Speed: #{@speed} mph"
  end
end

class Car < Vehicle
  def initialize(make, model, year, doors)
    super(make, model, year)
    @doors = doors
  end
  
  # Override parent method
  def info
    "#{super} - #{@doors} doors"
  end
  
  # New method
  def honk
    "Beep beep!"
  end
  
  # Override with super call
  def accelerate(amount)
    result = super
    result += " (Car acceleration)"
    result
  end
end

class Motorcycle < Vehicle
  def initialize(make, model, year, type)
    super(make, model, year)
    @type = type
  end
  
  def info
    "#{super} - #{@type} motorcycle"
  end
  
  def wheelie
    "#{@make} #{@model} is doing a wheelie!"
  end
end

car = Car.new("Toyota", "Camry", 2022, 4)
motorcycle = Motorcycle.new("Harley", "Street", 2021, "cruiser")

puts car.info  # "2022 Toyota Camry - Speed: 0 mph - 4 doors"
puts motorcycle.info  # "2021 Harley Street - Speed: 0 mph - cruiser motorcycle"
puts car.accelerate(60)  # "Toyota Camry accelerated to 60 mph (Car acceleration)"
```

### Multiple Inheritance with Modules
```ruby
# Mixin module
module Flyable
  def fly
    "#{self.class.name} is flying!"
  end
  
  def land
    "#{self.class.name} has landed"
  end
  
  private
  
  def check_wings
    "Wings are ready"
  end
end

# Another mixin
module Swimmable
  def swim
    "#{self.class.name} is swimming!"
  end
  
  def dive(depth)
    "#{self.class.name} is diving to #{depth} feet"
  end
end

# Class using multiple mixins
class Duck
  include Flyable
  include Swimmable
  
  def initialize(name)
    @name = name
  end
  
  def quack
    "#{@name} says: Quack!"
  end
end

# Another class using mixins
class Airplane
  include Flyable
  
  def initialize(model)
    @model = model
  end
  
  def take_off
    "#{@model} is taking off"
  end
  
  def land
    "#{@model} has landed safely"
  end
end

duck = Duck.new("Donald")
plane = Airplane.new("Boeing 747")

puts duck.fly      # "Duck is flying!"
puts duck.swim     # "Duck is swimming!"
puts duck.quack     # "Donald says: Quack!"

puts plane.fly     # "Airplane is flying!"
puts plane.land    # "Airplane has landed safely"
```

## Modules and Namespaces

### Module Definition
```ruby
# Module as namespace
module MathUtils
  PI = 3.141592653589793
  
  def self.circle_area(radius)
    PI * radius ** 2
  end
  
  def self.circle_circumference(radius)
    2 * PI * radius
  end
  
  def self.pythagorean_theorem(a, b)
    Math.sqrt(a ** 2 + b ** 2)
  end
end

# Module as mixin
module Validatable
  def validate_presence(*fields)
    fields.each do |field|
      value = send(field)
      raise "#{field} cannot be nil" if value.nil?
      raise "#{field} cannot be empty" if value.respond_to?(:empty?) && value.empty?
    end
  end
  
  def validate_numeric(*fields)
    fields.each do |field|
      value = send(field)
      raise "#{field} must be numeric" unless value.is_a?(Numeric)
      raise "#{field} cannot be negative" if value < 0
    end
  end
end

# Class using mixin
class Product
  include Validatable
  
  attr_accessor :name, :price, :quantity
  
  def initialize(name, price, quantity)
    @name = name
    @price = price
    @quantity = quantity
    
    validate_presence(:name, :price, :quantity)
    validate_numeric(:price, :quantity)
  end
  
  def total_value
    @price * @quantity
  end
end

# Usage
puts MathUtils.circle_area(5)  # 78.53981633974483
puts MathUtils.pythagorean_theorem(3, 4)  # 5.0

product = Product.new("Laptop", 999.99, 10)
puts product.total_value  # 9999.9
```

### Module Methods and Constants
```ruby
module Configuration
  # Module constants
  APP_NAME = "MyApp"
  VERSION = "1.0.0"
  DEBUG_MODE = false
  
  # Module methods
  def self.app_info
    "#{APP_NAME} v#{VERSION}"
  end
  
  def self.debug_enabled?
    DEBUG_MODE
  end
  
  def self.configure(options = {})
    options.each do |key, value|
      const_set(key.upcase, value)
    end
  end
  
  # Private module method
  def self.internal_setting
    "Internal configuration"
  end
  
  private_class_method :internal_setting
end

# Usage
puts Configuration.app_info  # "MyApp v1.0.0"
puts Configuration.debug_enabled?  # false

Configuration.configure(timeout: 30, retries: 3)
puts Configuration::TIMEOUT  # 30
puts Configuration::RETRIES  # 3
```

## Polymorphism

### Duck Typing
```ruby
# Duck typing - if it walks like a duck and quacks like a duck, it's a duck
class Duck
  def speak
    "Quack!"
  end
  
  def walk
    "Waddle waddle"
  end
end

class Person
  def speak
    "Hello!"
  end
  
  def walk
    "Strolling along"
  end
end

class Robot
  def speak
    "BEEP BOOP"
  end
  
  def walk
    "Rolling forward"
  end
end

# Polymorphic method
def make_speak(object)
  puts object.speak
end

def make_walk(object)
  puts object.walk
end

# Usage
duck = Duck.new
person = Person.new
robot = Robot.new

make_speak(duck)    # "Quack!"
make_speak(person)  # "Hello!"
make_speak(robot)   # "BEEP BOOP"

make_walk(duck)    # "Waddle waddle"
make_walk(person)  # "Strolling along"
make_walk(robot)   # "Rolling forward"
```

### Method Overriding Polymorphism
```ruby
class Shape
  def area
    raise NotImplementedError, "Subclasses must implement area method"
  end
  
  def perimeter
    raise NotImplementedError, "Subclasses must implement perimeter method"
  end
  
  def description
    "A shape with area #{area} and perimeter #{perimeter}"
  end
end

class Rectangle < Shape
  def initialize(width, height)
    @width = width
    @height = height
  end
  
  def area
    @width * @height
  end
  
  def perimeter
    2 * (@width + @height)
  end
  
  def description
    "Rectangle #{@width}x#{@height} - #{super}"
  end
end

class Circle < Shape
  def initialize(radius)
    @radius = radius
  end
  
  def area
    Math::PI * @radius ** 2
  end
  
  def perimeter
    2 * Math::PI * @radius
  end
  
  def description
    "Circle with radius #{@radius} - #{super}"
  end
end

class Triangle < Shape
  def initialize(base, height)
    @base = base
    @height = height
  end
  
  def area
    0.5 * @base * @height
  end
  
  def perimeter
    # Equilateral triangle approximation
    3 * @base
  end
  
  def description
    "Triangle with base #{@base} and height #{@height} - #{super}"
  end
end

# Polymorphic usage
shapes = [
  Rectangle.new(5, 3),
  Circle.new(4),
  Triangle.new(6, 4)
]

shapes.each do |shape|
  puts shape.description
end
```

### Interface-like Behavior
```ruby
# Ruby doesn't have interfaces, but we can simulate them with modules
module Drawable
  def draw
    raise NotImplementedError, "Classes including Drawable must implement draw method"
  end
  
  def resize(width, height)
    raise NotImplementedError, "Classes including Drawable must implement resize method"
  end
end

class Circle
  include Drawable
  
  def initialize(radius)
    @radius = radius
  end
  
  def draw
    "Drawing a circle with radius #{@radius}"
  end
  
  def resize(width, height)
    # Circle doesn't use width/height, but we implement the interface
    new_radius = [width, height].min / 2
    @radius = new_radius
    "Resized circle to radius #{@radius}"
  end
end

class Rectangle
  include Drawable
  
  def initialize(width, height)
    @width = width
    @height = height
  end
  
  def draw
    "Drawing a rectangle #{@width}x#{@height}"
  end
  
  def resize(width, height)
    @width = width
    @height = height
    "Resized rectangle to #{@width}x#{@height}"
  end
end

# Polymorphic usage
drawable_objects = [Circle.new(5), Rectangle.new(10, 5)]

drawable_objects.each do |obj|
  puts obj.draw
  puts obj.resize(8, 8)
end
```

## Encapsulation

### Public, Private, Protected
```ruby
class BankAccount
  def initialize(account_number, initial_balance)
    @account_number = account_number
    @balance = initial_balance
    @transaction_history = []
    @is_frozen = false
  end
  
  # Public interface
  def deposit(amount)
    raise "Account is frozen" if @is_frozen
    raise "Amount must be positive" if amount <= 0
    
    @balance += amount
    add_transaction("deposit", amount)
    "Deposited #{amount}. New balance: #{@balance}"
  end
  
  def withdraw(amount)
    raise "Account is frozen" if @is_frozen
    raise "Amount must be positive" if amount <= 0
    raise "Insufficient funds" if @balance < amount
    
    @balance -= amount
    add_transaction("withdrawal", amount)
    "Withdrew #{amount}. New balance: #{@balance}"
  end
  
  def balance
    @balance
  end
  
  def freeze_account
    @is_frozen = true
    "Account #{@account_number} is now frozen"
  end
  
  def unfreeze_account
    @is_frozen = false
    "Account #{@account_number} is now unfrozen"
  end
  
  def transaction_history
    @transaction_history.dup  # Return copy to prevent modification
  end
  
  private
  
  # Private method - only accessible within this class
  def add_transaction(type, amount)
    transaction = {
      type: type,
      amount: amount,
      balance: @balance,
      timestamp: Time.now
    }
    @transaction_history << transaction
  end
  
  protected
  
  # Protected method - accessible by subclasses
  def account_number
    @account_number
  end
  
  def is_frozen?
    @is_frozen
  end
end

# Subclass accessing protected method
class SavingsAccount < BankAccount
  def initialize(account_number, initial_balance, interest_rate)
    super(account_number, initial_balance)
    @interest_rate = interest_rate
  end
  
  def apply_interest
    return "Account is frozen" if is_frozen?
    
    interest = balance * @interest_rate / 100
    deposit(interest)
    "Applied interest: #{interest}"
  end
  
  def account_info
    "Account #{account_number} - Balance: #{balance} - Rate: #{@interest_rate}%"
  end
end

# Usage
account = BankAccount.new(12345, 1000)
puts account.deposit(500)  # "Deposited 500. New balance: 1500"
puts account.withdraw(200)  # "Withdrew 200. New balance: 1300"
# account.add_transaction("test", 100)  # NoMethodError: private method

savings = SavingsAccount.new(67890, 2000, 2.5)
puts savings.apply_interest  # "Applied interest: 50.0"
puts savings.account_info  # "Account 67890 - Balance: 2050.0 - Rate: 2.5%"
```

### Data Hiding
```ruby
class SecureData
  def initialize(secret_key, public_data)
    @secret_key = secret_key
    @public_data = public_data
    @access_log = []
  end
  
  # Public method - controlled access to data
  def get_public_data
    log_access("read_public")
    @public_data.dup  # Return copy to prevent modification
  end
  
  def update_public_data(new_data)
    log_access("update_public")
    @public_data = new_data.dup
    "Public data updated"
  end
  
  def get_secret_data(password)
    log_access("read_secret_attempt")
    
    if authenticate(password)
      log_access("read_secret_success")
      @secret_key.dup
    else
      log_access("read_secret_failed")
      "Authentication failed"
    end
  end
  
  def access_log
    @access_log.dup  # Return copy of access log
  end
  
  private
  
  # Private method - not accessible outside
  def authenticate(password)
    password == "secret123"
  end
  
  def log_access(action)
    @access_log << {
      action: action,
      timestamp: Time.now,
      thread_id: Thread.current.object_id
    }
  end
end

# Usage
secure = SecureData.new("my_secret_key", { name: "Public Data", value: 42 })
puts secure.get_public_data
puts secure.update_public_data({ name: "Updated Data", value: 100 })
puts secure.get_secret_data("wrong")  # "Authentication failed"
puts secure.get_secret_data("secret123")  # "my_secret_key"

log = secure.access_log
puts log.length  # 4 entries
```

## Best Practices

### Single Responsibility Principle
```ruby
# Good: Each class has one responsibility
class User
  attr_reader :name, :email, :age
  
  def initialize(name, email, age)
    @name = name
    @email = email
    @age = age
  end
  
  def adult?
    @age >= 18
  end
end

class UserRepository
  def initialize
    @users = []
  end
  
  def save(user)
    @users << user
    "User saved"
  end
  
  def find_by_email(email)
    @users.find { |user| user.email == email }
  end
  
  def all_adults
    @users.select(&:adult?)
  end
end

class UserNotifier
  def notify(user, message)
    puts "Notifying #{user.name}: #{message}"
  end
end

# Bad: Class doing too many things
class UserManager
  def initialize
    @users = []
  end
  
  def create_user(name, email, age)
    user = User.new(name, email, age)
    @users << user
    send_welcome_email(user)
    log_user_creation(user)
    user
  end
  
  def send_welcome_email(user)
    # Email sending logic
  end
  
  def log_user_creation(user)
    # Logging logic
  end
  
  def find_user(email)
    # Search logic
  end
  
  def notify_users(message)
    # Notification logic
  end
end
```

### Composition over Inheritance
```ruby
# Good: Use composition when appropriate
class Engine
  def initialize(type, horsepower)
    @type = type
    @horsepower = horsepower
  end
  
  def start
    "#{@type} engine starting with #{@horsepower} HP"
  end
  
  def stop
    "#{@type} engine stopped"
  end
end

class Transmission
  def initialize(gears)
    @gears = gears
    @current_gear = 1
  end
  
  def shift_up
    @current_gear = [@current_gear + 1, @gears].min
    "Shifted to gear #{@current_gear}"
  end
  
  def shift_down
    @current_gear = [@current_gear - 1, 1].max
    "Shifted to gear #{@current_gear}"
  end
end

class Car
  def initialize(make, model, engine, transmission)
    @make = make
    @model = model
    @engine = engine
    @transmission = transmission
  end
  
  def start
    "#{@engine.start} - Car ready to drive"
  end
  
  def drive
    "#{@make} #{@model} is driving"
  end
  
  def shift_up
    @transmission.shift_up
  end
  
  def stop
    @engine.stop
    "#{@make} #{@model} stopped"
  end
end

# Usage
engine = Engine.new("V6", 300)
transmission = Transmission.new(6)
car = Car.new("Toyota", "Camry", engine, transmission)

puts car.start
puts car.shift_up
puts car.drive
puts car.stop
```

### Dependency Injection
```ruby
# Good: Use dependency injection for flexibility
class ReportGenerator
  def initialize(data_source, formatter)
    @data_source = data_source
    @formatter = formatter
  end
  
  def generate_report
    data = @data_source.fetch_data
    @formatter.format(data)
  end
end

# Data source interface
class DatabaseSource
  def fetch_data
    # Fetch from database
    { users: 1000, orders: 500, revenue: 50000 }
  end
end

class ApiSource
  def fetch_data
    # Fetch from API
    { users: 1500, orders: 750, revenue: 75000 }
  end
end

# Formatter interface
class JsonFormatter
  def format(data)
    data.to_json
  end
end

class XmlFormatter
  def format(data)
    # Convert to XML
    "<data><users>#{data[:users]}</users><orders>#{data[:orders]}</orders><revenue>#{data[:revenue]}</revenue></data>"
  end
end

# Usage
db_source = DatabaseSource.new
api_source = ApiSource.new
json_formatter = JsonFormatter.new
xml_formatter = XmlFormatter.new

db_report = ReportGenerator.new(db_source, json_formatter)
api_report = ReportGenerator.new(api_source, xml_formatter)

puts db_report.generate_report
puts api_report.generate_report
```

## Common Pitfalls

### Inheritance Issues
```ruby
# Pitfall: Deep inheritance hierarchy
class Animal < Object; end
class Mammal < Animal; end
class Dog < Mammal; end
class GoldenRetriever < Dog; end
class Buddy < GoldenRetriever; end

# Solution: Favor composition over deep inheritance
class Dog
  def initialize(breed)
    @breed = breed
  end
end

class GoldenRetriever
  def initialize(name)
    @dog = Dog.new("Golden Retriever")
    @name = name
  end
end

# Pitfall: Breaking Liskov Substitution Principle
class Bird
  def fly
    "I'm flying!"
  end
end

class Penguin < Bird
  def fly
    raise "Penguins can't fly!"  # Breaks LSP
  end
end

# Solution: Use separate classes or interfaces
class Bird
  def move
    "I'm moving!"
  end
end

class FlyingBird < Bird
  def fly
    "I'm flying!"
  end
end

class Penguin < Bird
  def swim
    "I'm swimming!"
  end
end
```

### Module Issues
```ruby
# Pitfall: Module name conflicts
module A
  def shared_method
    "From module A"
  end
end

module B
  def shared_method
    "From module B"
  end
end

class Confused
  include A
  include B  # shared_method from B overwrites A
end

# Solution: Use explicit method names or namespacing
module A
  def method_a
    "From module A"
  end
end

module B
  def method_b
    "From module B"
  end
end

# Pitfall: Module instance variables
module Counter
  @count = 0
  
  def self.count
    @count
  end
  
  def self.increment
    @count += 1
  end
end

# This works but can be confusing
# Solution: Use class variables or separate state management
```

### Encapsulation Issues
```ruby
# Pitfall: Exposing internal state
class BadEncapsulation
  attr_accessor :internal_state  # Exposes internal state
  
  def initialize
    @internal_state = "sensitive"
  end
end

# Solution: Use proper access control
class GoodEncapsulation
  def initialize
    @internal_state = "sensitive"
  end
  
  def public_method
    # Use internal state safely
    @internal_state.upcase
  end
  
  private
  
  def internal_state
    @internal_state
  end
end

# Pitfall: Breaking encapsulation with instance_variable_get
class BreakEncapsulation
  def initialize
    @private_var = "private"
  end
  
  def get_private_var
    @private_var
  end
end

obj = BreakEncapsulation.new
obj.instance_variable_get(:@private_var)  # Direct access breaks encapsulation
```

## Summary

Ruby OOP provides:

**Classes and Objects:**
- Class definition and instantiation
- Constructors and destructors
- Instance and class methods
- Access control (public, private, protected)
- Class variables and constants

**Inheritance:**
- Single inheritance with super
- Method overriding and super calls
- Multiple inheritance with modules (mixins)
- Interface-like behavior with modules

**Modules and Namespaces:**
- Module definition and usage
- Mixins for shared behavior
- Namespace organization
- Module methods and constants

**Polymorphism:**
- Duck typing and dynamic typing
- Method overriding polymorphism
- Interface-like behavior with modules
- Dynamic method dispatch

**Encapsulation:**
- Access control and data hiding
- Public interfaces
- Private and protected members
- Safe data access patterns

**Best Practices:**
- Single Responsibility Principle
- Composition over inheritance
- Dependency injection
- Proper encapsulation
- Interface segregation

**Common Pitfalls:**
- Deep inheritance hierarchies
- Breaking Liskov Substitution
- Module name conflicts
- Exposing internal state
- Breaking encapsulation

Ruby's OOP features provide a flexible and expressive way to structure code, with dynamic typing and mixins offering powerful alternatives to traditional inheritance patterns.
