# Object-Oriented Programming in Ruby

## Overview

Ruby is a pure object-oriented programming language where everything is an object. Understanding OOP concepts is fundamental to writing effective Ruby code.

## Classes and Objects

### Defining a Class

```ruby
class Person
  # Class body
end

# Creating an instance
person = Person.new
puts person.class  # => Person
puts person.class.superclass  # => Object
```

### Instance Variables

Instance variables store object-specific data and start with `@`.

```ruby
class Person
  def initialize(name, age)
    @name = name  # Instance variable
    @age = age    # Instance variable
  end
  
  def name
    @name
  end
  
  def age
    @age
  end
end

person = Person.new("Alice", 25)
puts person.name  # => "Alice"
puts person.age   # => 25
```

### Constructor (initialize)

The `initialize` method is called when a new object is created.

```ruby
class Car
  def initialize(make, model, year)
    @make = make
    @model = model
    @year = year
    @started = false
  end
  
  def start
    @started = true
    puts "#{@make} #{@@model} started!"
  end
  
  def stop
    @started = false
    puts "#{@make} #{@model} stopped!"
  end
end

car = Car.new("Toyota", "Camry", 2020)
car.start  # => "Toyota Camry started!"
```

## Accessor Methods

### Getters and Setters

```ruby
class Person
  def initialize(name)
    @name = name
  end
  
  # Getter method
  def name
    @name
  end
  
  # Setter method
  def name=(new_name)
    @name = new_name
  end
end

person = Person.new("John")
puts person.name    # => "John"
person.name = "Jane"
puts person.name    # => "Jane"
```

### Attribute Accessors

Ruby provides shortcuts for creating accessor methods.

```ruby
class Person
  # Creates getter and setter methods
  attr_accessor :name, :age
  
  # Creates only getter methods
  attr_reader :id
  
  # Creates only setter methods
  attr_writer :password
  
  def initialize(id, name, age)
    @id = id
    @name = name
    @age = age
  end
end

person = Person.new(1, "Alice", 25)
puts person.id      # => 1 (read-only)
puts person.name    # => "Alice"
person.name = "Bob"  # => "Bob" (read-write)
person.age = 30      # => 30 (read-write)
person.password = "secret"  # => "secret" (write-only)
```

## Class Variables and Class Methods

### Class Variables

Class variables are shared across all instances of a class.

```ruby
class Student
  @@school_name = "Ruby High School"
  @@total_students = 0
  
  def initialize(name)
    @name = name
    @@total_students += 1
  end
  
  def self.total_students
    @@total_students
  end
  
  def self.school_name
    @@school_name
  end
end

student1 = Student.new("Alice")
student2 = Student.new("Bob")
student3 = Student.new("Charlie")

puts Student.total_students  # => 3
puts Student.school_name     # => "Ruby High School"
```

### Class Methods

Class methods operate on the class itself, not on instances.

```ruby
class MathOperations
  def self.add(a, b)
    a + b
  end
  
  def self.multiply(a, b)
    a * b
  end
  
  def self.factorial(n)
    return 1 if n <= 1
    n * factorial(n - 1)
  end
end

puts MathOperations.add(5, 3)        # => 8
puts MathOperations.multiply(4, 6)  # => 24
puts MathOperations.factorial(5)    # => 120
```

## Inheritance

### Basic Inheritance

```ruby
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
end

class Dog < Animal
  def speak
    "#{@name} barks: Woof!"
  end
  
  def fetch
    "#{@name} is fetching the ball"
  end
end

class Cat < Animal
  def speak
    "#{@name} meows: Meow!"
  end
  
  def purr
    "#{@name} is purring"
  end
end

dog = Dog.new("Buddy")
cat = Cat.new("Whiskers")

puts dog.speak  # => "Buddy barks: Woof!"
puts dog.eat    # => "Buddy is eating"
puts dog.fetch  # => "Buddy is fetching the ball"

puts cat.speak  # => "Whiskers meows: Meow!"
puts cat.eat    # => "Whiskers is eating"
puts cat.purr   # => "Whiskers is purring"
```

### Using Super

The `super` keyword calls the parent class method.

```ruby
class Vehicle
  def initialize(make, model)
    @make = make
    @model = model
  end
  
  def info
    "#{@make} #{@model}"
  end
end

class Car < Vehicle
  def initialize(make, model, year)
    super(make, model)  # Call parent initialize
    @year = year
  end
  
  def info
    "#{super} (#{@year})"  # Call parent info and add year
  end
end

car = Car.new("Toyota", "Camry", 2020)
puts car.info  # => "Toyota Camry (2020)"
```

### Method Overriding

```ruby
class Shape
  def area
    "Base shape area calculation"
  end
  
  def perimeter
    "Base shape perimeter calculation"
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
end

rectangle = Rectangle.new(5, 3)
circle = Circle.new(4)

puts "Rectangle area: #{rectangle.area}"        # => 15
puts "Rectangle perimeter: #{rectangle.perimeter}"  # => 16
puts "Circle area: #{circle.area}"              # => 50.265...
puts "Circle perimeter: #{circle.perimeter}"     # => 25.132...
```

## Polymorphism

### Duck Typing

Ruby uses duck typing - "If it walks like a duck and quacks like a duck, it's a duck."

```ruby
class Duck
  def speak
    "Quack!"
  end
  
  def swim
    "Swimming like a duck"
  end
end

class Person
  def speak
    "Hello!"
  end
  
  def swim
    "Swimming like a person"
  end
end

def make_speak(object)
  object.speak
end

def go_swimming(object)
  object.swim
end

duck = Duck.new
person = Person.new

puts make_speak(duck)    # => "Quack!"
puts make_speak(person)  # => "Hello!"
puts go_swimming(duck)   # => "Swimming like a duck"
puts go_swimming(person) # => "Swimming like a person"
```

### Interface-like Behavior

```ruby
class PaymentProcessor
  def process_payment(amount)
    raise NotImplementedError, "Subclasses must implement process_payment"
  end
end

class CreditCardProcessor < PaymentProcessor
  def process_payment(amount)
    "Processing credit card payment of $#{amount}"
  end
end

class PayPalProcessor < PaymentProcessor
  def process_payment(amount)
    "Processing PayPal payment of $#{amount}"
  end
end

class BankTransferProcessor < PaymentProcessor
  def process_payment(amount)
    "Processing bank transfer of $#{amount}"
  end
end

def handle_payment(processor, amount)
  processor.process_payment(amount)
end

processors = [
  CreditCardProcessor.new,
  PayPalProcessor.new,
  BankTransferProcessor.new
]

processors.each { |processor| puts handle_payment(processor, 100) }
```

## Encapsulation

### Public, Private, and Protected Methods

```ruby
class BankAccount
  def initialize(account_number, initial_balance = 0)
    @account_number = account_number
    @balance = initial_balance
  end
  
  # Public methods
  def deposit(amount)
    validate_amount(amount)
    @balance += amount
    log_transaction("Deposit", amount)
  end
  
  def withdraw(amount)
    validate_amount(amount)
    return "Insufficient funds" if amount > @balance
    
    @balance -= amount
    log_transaction("Withdrawal", amount)
  end
  
  def balance
    @balance
  end
  
  def account_info
    "Account #{@account_number}: Balance $#{@balance}"
  end
  
  private
  
  def validate_amount(amount)
    raise ArgumentError, "Amount must be positive" if amount <= 0
  end
  
  def log_transaction(type, amount)
    puts "#{type}: $#{amount} - New balance: $#{@balance}"
  end
  
  protected
  
  def internal_balance
    @balance
  end
end

account = BankAccount.new("12345", 1000)
puts account.deposit(500)      # => "Deposit: $500 - New balance: $1500"
puts account.withdraw(200)     # => "Withdrawal: $200 - New balance: $1300"
puts account.balance           # => 1300
puts account.account_info      # => "Account 12345: Balance $1300"

# account.validate_amount(100)  # => NoMethodError: private method
# account.internal_balance      # => NoMethodError: protected method
```

## Composition over Inheritance

### Using Composition

```ruby
class Engine
  def start
    "Engine started"
  end
  
  def stop
    "Engine stopped"
  end
end

class Wheels
  def rotate
    "Wheels rotating"
  end
  
  def stop_rotation
    "Wheels stopped"
  end
end

class Car
  def initialize
    @engine = Engine.new
    @wheels = Wheels.new
  end
  
  def start
    @engine.start
    @wheels.rotate
    "Car is moving"
  end
  
  def stop
    @engine.stop
    @wheels.stop_rotation
    "Car stopped"
  end
end

car = Car.new
puts car.start  # => "Car is moving"
puts car.stop   # => "Car stopped"
```

## Singleton Classes

### Singleton Pattern

```ruby
require 'singleton'

class Logger
  include Singleton
  
  def initialize
    @logs = []
  end
  
  def log(message)
    @logs << "#{Time.now}: #{message}"
    puts "Logged: #{message}"
  end
  
  def show_logs
    @logs
  end
end

logger1 = Logger.instance
logger2 = Logger.instance

puts logger1.object_id == logger2.object_id  # => true

logger1.log("First message")
logger2.log("Second message")

puts logger1.show_logs
```

## Method Missing and Dynamic Methods

### Dynamic Method Handling

```ruby
class DynamicMethods
  def self.create_method(name)
    define_method(name) do |arg|
      "#{name} called with #{arg}"
    end
  end
  
  # Create methods dynamically
  create_method :hello
  create_method :goodbye
end

obj = DynamicMethods.new
puts obj.hello("World")     # => "hello called with World"
puts obj.goodbye("Friend")  # => "goodbye called with Friend"
```

### Method Missing

```ruby
class FlexibleCalculator
  def method_missing(method_name, *args)
    if method_name.to_s.start_with?("add_")
      number = method_name.to_s.sub("add_", "").to_i
      args.reduce(number, :+)
    elsif method_name.to_s.start_with?("multiply_")
      number = method_name.to_s.sub("multiply_", "").to_i
      args.reduce(number, :*)
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.start_with?("add_") || 
    method_name.to_s.start_with?("multiply_") || 
    super
  end
end

calc = FlexibleCalculator.new
puts calc.add_10(5, 3)        # => 18
puts calc.multiply_5(2, 3, 4)  # => 120
```

## Practical Examples

### Example 1: Banking System

```ruby
class Account
  attr_reader :account_number, :balance, :owner
  
  def initialize(account_number, owner, initial_balance = 0)
    @account_number = account_number
    @owner = owner
    @balance = initial_balance
    @transactions = []
  end
  
  def deposit(amount)
    raise ArgumentError, "Amount must be positive" if amount <= 0
    
    @balance += amount
    @transactions << { type: :deposit, amount: amount, timestamp: Time.now }
    "Deposited $#{amount}. New balance: $#{@balance}"
  end
  
  def withdraw(amount)
    raise ArgumentError, "Amount must be positive" if amount <= 0
    raise InsufficientFundsError if amount > @balance
    
    @balance -= amount
    @transactions << { type: :withdrawal, amount: amount, timestamp: Time.now }
    "Withdrew $#{amount}. New balance: $#{@balance}"
  end
  
  def transfer(target_account, amount)
    withdraw(amount)
    target_account.deposit(amount)
    "Transferred $#{amount} to account #{target_account.account_number}"
  end
  
  def transaction_history
    @transactions
  end
  
  def account_summary
    {
      account_number: @account_number,
      owner: @owner,
      balance: @balance,
      transaction_count: @transactions.length
    }
  end
end

class InsufficientFundsError < StandardError; end

# Usage
account1 = Account.new("001", "Alice", 1000)
account2 = Account.new("002", "Bob", 500)

puts account1.deposit(500)
puts account1.withdraw(200)
puts account1.transfer(account2, 300)

puts account1.account_summary
puts account2.account_summary
```

### Example 2: E-commerce System

```ruby
class Product
  attr_reader :id, :name, :price, :stock
  
  def initialize(id, name, price, stock)
    @id = id
    @name = name
    @price = price
    @stock = stock
  end
  
  def in_stock?
    @stock > 0
  end
  
  def reduce_stock(quantity)
    raise OutOfStockError if quantity > @stock
    @stock -= quantity
  end
  
  def restock(quantity)
    @stock += quantity
  end
end

class ShoppingCart
  def initialize
    @items = {}
  end
  
  def add_item(product, quantity = 1)
    raise OutOfStockError unless product.in_stock?
    raise InsufficientStockError if quantity > product.stock
    
    @items[product] = (@items[product] || 0) + quantity
    product.reduce_stock(quantity)
  end
  
  def remove_item(product, quantity = 1)
    return unless @items[product]
    
    @items[product] -= quantity
    product.restock(quantity)
    @items.delete(product) if @items[product] <= 0
  end
  
  def total
    @items.sum { |product, quantity| product.price * quantity }
  end
  
  def items
    @items.dup
  end
  
  def clear
    @items.each { |product, quantity| product.restock(quantity) }
    @items.clear
  end
end

class Order
  attr_reader :id, :items, :total, :status
  
  def initialize(id, cart)
    @id = id
    @items = cart.items.dup
    @total = cart.total
    @status = :pending
    @created_at = Time.now
  end
  
  def confirm
    @status = :confirmed
    "Order ##{@id} confirmed. Total: $#{'%.2f' % @total}"
  end
  
  def ship
    raise OrderNotConfirmedError if @status != :confirmed
    @status = :shipped
    "Order ##{@id} shipped"
  end
  
  def deliver
    raise OrderNotShippedError if @status != :shipped
    @status = :delivered
    "Order ##{@id} delivered"
  end
end

class OutOfStockError < StandardError; end
class InsufficientStockError < StandardError; end
class OrderNotConfirmedError < StandardError; end
class OrderNotShippedError < StandardError; end

# Usage
product1 = Product.new(1, "Laptop", 999.99, 10)
product2 = Product.new(2, "Mouse", 29.99, 50)

cart = ShoppingCart.new
cart.add_item(product1, 1)
cart.add_item(product2, 2)

puts "Cart total: $#{'%.2f' % cart.total}"

order = Order.new(1001, cart)
puts order.confirm
puts order.ship
puts order.deliver
```

## Best Practices

### 1. Single Responsibility Principle

```ruby
# Good - Each class has one responsibility
class User
  attr_accessor :name, :email
  
  def initialize(name, email)
    @name = name
    @email = email
  end
end

class UserValidator
  def self.valid?(user)
    user.name && user.email && user.email.include?("@")
  end
end

class UserRepository
  def self.save(user)
    # Database logic
  end
end

# Bad - One class doing too much
class User
  def initialize(name, email)
    @name = name
    @email = email
  end
  
  def valid?
    # Validation logic
  end
  
  def save
    # Database logic
  end
  
  def send_email
    # Email logic
  end
end
```

### 2. Composition over Inheritance

```ruby
# Good - Composition
class Car
  def initialize
    @engine = Engine.new
    @wheels = Wheels.new
  end
end

# Avoid deep inheritance hierarchies
```

### 3. Use Interfaces through Duck Typing

```ruby
# Good - Duck typing
class PaymentProcessor
  def process(payment)
    payment.process if payment.respond_to?(:process)
  end
end

# Bad - Rigid inheritance
class PaymentProcessor
  def process(payment)
    if payment.is_a?(CreditCardPayment)
      # specific logic
    elsif payment.is_a?(PayPalPayment)
      # specific logic
    end
  end
end
```

## Practice Exercises

### Exercise 1: Shape Hierarchy
Create a shape hierarchy with:
- Base Shape class
- Circle, Rectangle, Triangle subclasses
- Area and perimeter calculations
- Polymorphic behavior

### Exercise 2: Vehicle System
Build a vehicle system with:
- Base Vehicle class
- Car, Motorcycle, Bicycle subclasses
- Shared and unique behaviors
- Composition for engines, wheels

### Exercise 3: Library System
Implement a library management system with:
- Book, Member, Library classes
- Borrowing and returning books
- Due dates and fines

### Exercise 4: Game Characters
Create a game character system with:
- Base Character class
- Warrior, Mage, Rogue subclasses
- Abilities and inventory
- Combat system

---

**Ready to learn about modules and mixins in Ruby? Let's continue! 🔧**
