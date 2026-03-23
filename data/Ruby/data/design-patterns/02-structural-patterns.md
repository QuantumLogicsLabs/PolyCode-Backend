# Structural Design Patterns in Ruby
# Comprehensive guide to object composition patterns

## 🎯 Overview

Structural design patterns deal with object composition and typically identify simple ways to realize relationships between different entities. This guide covers essential structural patterns with Ruby implementations.

## 🔌 Adapter Pattern

The Adapter pattern allows incompatible interfaces to work together by wrapping an object with a new interface.

```ruby
# Legacy system with incompatible interface
class LegacyPaymentSystem
  def process_payment(amount, currency, card_number, expiry_date, cvv)
    puts "Legacy payment: $#{amount} #{currency} ending in #{card_number[-4..-1]}"
    "PAYMENT_#{Time.now.to_i}"
  end
end

# Modern payment interface
class ModernPaymentGateway
  def charge(payment_request)
    puts "Modern gateway: Charging #{payment_request.amount} #{payment_request.currency}"
    "CHARGE_#{Time.now.to_i}"
  end
end

# Payment request object
class PaymentRequest
  attr_reader :amount, :currency, :payment_method
  
  def initialize(amount:, currency:, payment_method:)
    @amount = amount
    @currency = currency
    @payment_method = payment_method
  end
end

class CreditCard
  attr_reader :number, :expiry_date, :cvv
  
  def initialize(number:, expiry_date:, cvv:)
    @number = number
    @expiry_date = expiry_date
    @cvv = cvv
  end
end

# Adapter to make legacy system compatible with modern interface
class LegacyPaymentAdapter
  def initialize(legacy_system)
    @legacy_system = legacy_system
  end
  
  def charge(payment_request)
    card = payment_request.payment_method
    
    @legacy_system.process_payment(
      payment_request.amount,
      payment_request.currency,
      card.number,
      card.expiry_date,
      card.cvv
    )
  end
end

# Usage
legacy_system = LegacyPaymentSystem.new
adapter = LegacyPaymentAdapter.new(legacy_system)

card = CreditCard.new(
  number: "1234567890123456",
  expiry_date: "12/25",
  cvv: "123"
)

payment_request = PaymentRequest.new(
  amount: 100.00,
  currency: "USD",
  payment_method: card
)

# Use adapter with modern interface
result = adapter.charge(payment_request)
puts "Payment result: #{result}"

# Can also use modern gateway directly
modern_gateway = ModernPaymentGateway.new
result2 = modern_gateway.charge(payment_request)
puts "Modern payment result: #{result2}"
```

## 🌉 Bridge Pattern

The Bridge pattern decouples an abstraction from its implementation so that the two can vary independently.

```ruby
# Implementation interface
class MessageSender
  def send_message(message, recipient)
    raise NotImplementedError, "Subclasses must implement send_message"
  end
end

# Concrete implementations
class EmailSender < MessageSender
  def send_message(message, recipient)
    puts "Sending email to #{recipient}: #{message}"
    "Email sent successfully"
  end
end

class SMSSender < MessageSender
  def send_message(message, recipient)
    puts "Sending SMS to #{recipient}: #{message}"
    "SMS sent successfully"
  end
end

class PushNotificationSender < MessageSender
  def send_message(message, recipient)
    puts "Sending push notification to #{recipient}: #{message}"
    "Push notification sent successfully"
  end
end

# Abstraction
class Message
  def initialize(sender)
    @sender = sender
  end
  
  def send(content, recipient)
    formatted_content = format_content(content)
    @sender.send_message(formatted_content, recipient)
  end
  
  private
  
  def format_content(content)
    content
  end
end

# Refined abstractions
class UrgentMessage < Message
  private
  
  def format_content(content)
    "URGENT: #{content}"
  end
end

class FormalMessage < Message
  private
  
  def format_content(content)
    "Dear Sir/Madam,\n\n#{content}\n\nSincerely,\nSystem"
  end
end

class CasualMessage < Message
  private
  
  def format_content(content)
    "Hey! #{content} 😊"
  end
end

# Usage
email_sender = EmailSender.new
sms_sender = SMSSender.new
push_sender = PushNotificationSender.new

# Different message types with different senders
urgent_email = UrgentMessage.new(email_sender)
formal_sms = FormalMessage.new(sms_sender)
casual_push = CasualMessage.new(push_sender)

urgent_email.send("System maintenance scheduled", "admin@company.com")
formal_sms.send("Please review the attached document", "+1234567890")
casual_push.send("Your order has been shipped!", "user123")
```

## 🌿 Composite Pattern

The Composite pattern composes objects into tree structures to represent part-whole hierarchies, allowing clients to treat individual objects and compositions uniformly.

```ruby
# Component interface
class FileSystemComponent
  attr_reader :name
  
  def initialize(name)
    @name = name
  end
  
  def add(component)
    raise NotImplementedError, "Leaf nodes cannot add components"
  end
  
  def remove(component)
    raise NotImplementedError, "Leaf nodes cannot remove components"
  end
  
  def get_child(index)
    raise NotImplementedError, "Leaf nodes have no children"
  end
  
  def list(indent = 0)
    raise NotImplementedError, "Subclasses must implement list"
  end
  
  def size
    raise NotImplementedError, "Subclasses must implement size"
  end
end

# Leaf node
class File < FileSystemComponent
  def initialize(name, content = "")
    super(name)
    @content = content
  end
  
  def list(indent = 0)
    puts "#{'  ' * indent}📄 #{name} (#{content.length} bytes)"
  end
  
  def size
    @content.length
  end
end

# Composite node
class Directory < FileSystemComponent
  def initialize(name)
    super(name)
    @children = []
  end
  
  def add(component)
    @children << component
  end
  
  def remove(component)
    @children.delete(component)
  end
  
  def get_child(index)
    @children[index]
  end
  
  def list(indent = 0)
    puts "#{'  ' * indent}📁 #{name}/"
    @children.each { |child| child.list(indent + 1) }
  end
  
  def size
    @children.sum(&:size)
  end
  
  def find(name)
    return self if @name == name
    
    @children.each do |child|
      result = child.find(name)
      return result if result
    end
    
    nil
  end
end

# Usage
# Create file system structure
root = Directory.new("root")

documents = Directory.new("documents")
documents.add(File.new("resume.pdf", 102400))
documents.add(File.new("cover_letter.doc", 20480))

pictures = Directory.new("pictures")
pictures.add(File.new("vacation.jpg", 2048000))
pictures.add(File.new("family.png", 1024000))

projects = Directory.new("projects")
ruby_project = Directory.new("ruby_app")
ruby_project.add(File.new("main.rb", 5120))
ruby_project.add(File.new("Gemfile", 256))
projects.add(ruby_project)

# Build directory tree
root.add(documents)
root.add(pictures)
root.add(projects)

# List entire structure
puts "File System Structure:"
root.list

# Calculate total size
puts "\nTotal size: #{root.size} bytes"

# Find specific item
found = root.find("ruby_app")
puts "\nFound 'ruby_app': #{found ? found.name : 'Not found'}"
```

## 🎭 Decorator Pattern

The Decorator pattern dynamically adds new responsibilities to an object without altering its structure.

```ruby
# Component interface
class Coffee
  def cost
    raise NotImplementedError, "Subclasses must implement cost"
  end
  
  def description
    raise NotImplementedError, "Subclasses must implement description"
  end
end

# Concrete component
class SimpleCoffee < Coffee
  def cost
    2.00
  end
  
  def description
    "Simple Coffee"
  end
end

# Base decorator
class CoffeeDecorator < Coffee
  def initialize(coffee)
    @coffee = coffee
  end
  
  def cost
    @coffee.cost
  end
  
  def description
    @coffee.description
  end
end

# Concrete decorators
class MilkDecorator < CoffeeDecorator
  def cost
    @coffee.cost + 0.50
  end
  
  def description
    "#{@coffee.description}, Milk"
  end
end

class SugarDecorator < CoffeeDecorator
  def cost
    @coffee.cost + 0.25
  end
  
  def description
    "#{@coffee.description}, Sugar"
  end
end

class WhippedCreamDecorator < CoffeeDecorator
  def cost
    @coffee.cost + 1.00
  end
  
  def description
    "#{@coffee.description}, Whipped Cream"
  end
end

class VanillaDecorator < CoffeeDecorator
  def cost
    @coffee.cost + 0.75
  end
  
  def description
    "#{@coffee.description}, Vanilla"
  end
end

# Usage
# Start with simple coffee
coffee = SimpleCoffee.new
puts "#{coffee.description}: $#{coffee.cost}"

# Add decorators dynamically
coffee = MilkDecorator.new(coffee)
puts "#{coffee.description}: $#{coffee.cost}"

coffee = SugarDecorator.new(coffee)
puts "#{coffee.description}: $#{coffee.cost}"

coffee = WhippedCreamDecorator.new(coffee)
puts "#{coffee.description}: $#{coffee.cost}"

coffee = VanillaDecorator.new(coffee)
puts "#{coffee.description}: $#{coffee.cost}"

# Another coffee with different decorators
coffee2 = SimpleCoffee.new
coffee2 = WhippedCreamDecorator.new(coffee2)
coffee2 = VanillaDecorator.new(coffee2)
coffee2 = MilkDecorator.new(coffee2)
puts "#{coffee2.description}: $#{coffee2.cost}"

# Dynamic decorator builder
class CoffeeBuilder
  def initialize
    @coffee = SimpleCoffee.new
  end
  
  def with_milk
    @coffee = MilkDecorator.new(@coffee)
    self
  end
  
  def with_sugar
    @coffee = SugarDecorator.new(@coffee)
    self
  end
  
  def with_whipped_cream
    @coffee = WhippedCreamDecorator.new(@coffee)
    self
  end
  
  def with_vanilla
    @coffee = VanillaDecorator.new(@coffee)
    self
  end
  
  def build
    @coffee
  end
end

# Usage with builder
coffee3 = CoffeeBuilder.new
  .with_milk
  .with_sugar
  .with_vanilla
  .build

puts "#{coffee3.description}: $#{coffee3.cost}"
```

## 🏢 Facade Pattern

The Facade pattern provides a unified interface to a set of interfaces in a subsystem, making the subsystem easier to use.

```ruby
# Complex subsystem components
class CPU
  def freeze
    puts "CPU: Freezing"
  end
  
  def jump(position)
    puts "CPU: Jumping to position #{position}"
  end
  
  def execute
    puts "CPU: Executing"
  end
end

class Memory
  def load(position, data)
    puts "Memory: Loading #{data} at position #{position}"
  end
end

class HardDrive
  def read(lba, size)
    puts "HardDrive: Reading #{size} bytes from LBA #{lba}"
    "data_from_lba_#{lba}"
  end
end

class GPU
  def render
    puts "GPU: Rendering graphics"
  end
end

class SoundCard
  def play_sound(sound)
    puts "SoundCard: Playing #{sound}"
  end
end

# Facade
class ComputerFacade
  def initialize
    @cpu = CPU.new
    @memory = Memory.new
    @hard_drive = HardDrive.new
    @gpu = GPU.new
    @sound_card = SoundCard.new
  end
  
  def start_computer
    puts "Starting computer..."
    
    @cpu.freeze
    @memory.load(0, @hard_drive.read(0, 1024))
    @cpu.jump(0)
    @cpu.execute
    
    @gpu.render
    @sound_card.play_sound("startup_sound.wav")
    
    puts "Computer started successfully!"
  end
  
  def shutdown_computer
    puts "Shutting down computer..."
    @sound_card.play_sound("shutdown_sound.wav")
    puts "Computer shut down successfully!"
  end
  
  def run_program(program_name)
    puts "Running program: #{program_name}"
    
    @memory.load(0, @hard_drive.read(1024, 2048))
    @cpu.jump(0)
    @cpu.execute
    @gpu.render
    
    puts "Program #{program_name} executed successfully!"
  end
  
  def play_media(media_file)
    puts "Playing media: #{media_file}"
    
    @memory.load(0, @hard_drive.read(2048, 4096))
    @cpu.execute
    @gpu.render
    @sound_card.play_sound(media_file)
    
    puts "Media #{media_file} played successfully!"
  end
end

# Usage
computer = ComputerFacade.new
computer.start_computer
computer.run_program("web_browser")
computer.play_media("music.mp3")
computer.shutdown_computer

# Another facade example for e-commerce system
class OrderProcessingFacade
  def initialize
    @inventory = InventorySystem.new
    @payment = PaymentProcessor.new
    @shipping = ShippingService.new
    @notifications = NotificationService.new
  end
  
  def process_order(customer, items, payment_info, shipping_address)
    puts "Processing order for #{customer.name}..."
    
    # Check inventory
    available_items = []
    items.each do |item|
      if @inventory.check_availability(item[:id], item[:quantity])
        available_items << item
        @inventory.reserve_item(item[:id], item[:quantity])
      else
        puts "Item #{item[:id]} not available"
        return false
      end
    end
    
    # Process payment
    total = calculate_total(available_items)
    payment_result = @payment.process_payment(payment_info, total)
    
    unless payment_result.success?
      puts "Payment failed"
      # Release reserved items
      available_items.each { |item| @inventory.release_item(item[:id], item[:quantity]) }
      return false
    end
    
    # Arrange shipping
    shipping_label = @shipping.create_shipment(available_items, shipping_address)
    
    # Send notifications
    @notifications.send_order_confirmation(customer, available_items, total)
    @notifications.send_shipping_notification(customer, shipping_label)
    
    puts "Order processed successfully!"
    {
      success: true,
      order_id: "ORDER_#{Time.now.to_i}",
      shipping_label: shipping_label
    }
  end
  
  private
  
  def calculate_total(items)
    items.sum { |item| item[:price] * item[:quantity] }
  end
end

# Supporting subsystem classes
class InventorySystem
  def check_availability(item_id, quantity)
    puts "Checking availability for item #{item_id}, quantity #{quantity}"
    true  # Simplified
  end
  
  def reserve_item(item_id, quantity)
    puts "Reserving #{quantity} of item #{item_id}"
  end
  
  def release_item(item_id, quantity)
    puts "Releasing #{quantity} of item #{item_id}"
  end
end

class PaymentProcessor
  def process_payment(payment_info, amount)
    puts "Processing payment of $#{amount}"
    OpenStruct.new(success: true, transaction_id: "TXN_#{Time.now.to_i}")
  end
end

class ShippingService
  def create_shipment(items, address)
    puts "Creating shipment to #{address}"
    "SHIPPING_#{Time.now.to_i}"
  end
end

class NotificationService
  def send_order_confirmation(customer, items, total)
    puts "Sending order confirmation to #{customer.email}"
  end
  
  def send_shipping_notification(customer, shipping_label)
    puts "Sending shipping notification to #{customer.email}"
  end
end

# Usage
customer = OpenStruct.new(name: "John Doe", email: "john@example.com")
items = [
  { id: "ITEM1", quantity: 2, price: 29.99 },
  { id: "ITEM2", quantity: 1, price: 49.99 }
]
payment_info = { type: "credit_card", number: "****-****-****-1234" }
shipping_address = "123 Main St, City, State"

order_facade = OrderProcessingFacade.new
result = order_facade.process_order(customer, items, payment_info, shipping_address)
puts "Order result: #{result[:success]}"
```

## 🪡 Proxy Pattern

The Proxy pattern provides a surrogate or placeholder for another object to control access to it.

```ruby
# Subject interface
class Image
  def display
    raise NotImplementedError, "Subclasses must implement display"
  end
end

# Real subject
class RealImage < Image
  def initialize(filename)
    @filename = filename
    load_image_from_disk
  end
  
  def display
    puts "Displaying #{@filename}"
  end
  
  private
  
  def load_image_from_disk
    puts "Loading #{@filename} from disk..."
    sleep(1)  # Simulate slow loading
  end
end

# Proxy
class ProxyImage < Image
  def initialize(filename)
    @filename = filename
    @real_image = nil
  end
  
  def display
    if @real_image.nil?
      @real_image = RealImage.new(@filename)
    end
    @real_image.display
  end
end

# Usage
puts "Creating proxy images..."
image1 = ProxyImage.new("image1.jpg")
image2 = ProxyImage.new("image2.jpg")

puts "Image 1 display (first time - loads from disk):"
image1.display

puts "Image 1 display (second time - uses cached):"
image1.display

puts "Image 2 display (first time - loads from disk):"
image2.display

# Protection proxy example
class BankAccount
  attr_reader :balance
  
  def initialize(owner, initial_balance)
    @owner = owner
    @balance = initial_balance
  end
  
  def withdraw(amount)
    @balance -= amount
    puts "Withdrew $#{amount}. New balance: $#{@balance}"
  end
  
  def deposit(amount)
    @balance += amount
    puts "Deposited $#{amount}. New balance: $#{@balance}"
  end
end

class BankAccountProxy
  def initialize(real_account, owner)
    @real_account = real_account
    @owner = owner
  end
  
  def withdraw(amount)
    check_access
    validate_amount(amount)
    @real_account.withdraw(amount)
  end
  
  def deposit(amount)
    check_access
    validate_amount(amount)
    @real_account.deposit(amount)
  end
  
  def balance
    check_access
    @real_account.balance
  end
  
  private
  
  def check_access
    raise SecurityError, "Access denied" unless authorized?
  end
  
  def authorized?
    # Simplified authorization check
    @owner == "account_holder"
  end
  
  def validate_amount(amount)
    raise ArgumentError, "Amount must be positive" if amount <= 0
    raise ArgumentError, "Amount too large" if amount > 10000
  end
end

# Usage
account = BankAccount.new("account_holder", 1000.00)
proxy = BankAccountProxy.new(account, "account_holder")

proxy.deposit(500.00)
proxy.withdraw(200.00)
puts "Balance: $#{proxy.balance}"

begin
  proxy.withdraw(-50)
rescue ArgumentError => e
  puts "Error: #{e.message}"
end

# Virtual proxy for expensive operations
class ExpensiveCalculator
  def initialize
    @results = {}
  end
  
  def calculate_complex_operation(input)
    puts "Performing complex calculation for input: #{input}"
    sleep(2)  # Simulate expensive computation
    result = input * input * Math.sqrt(input)
    @results[input] = result
    result
  end
end

class CalculatorProxy
  def initialize
    @calculator = ExpensiveCalculator.new
    @cache = {}
  end
  
  def calculate(input)
    if @cache.key?(input)
      puts "Returning cached result for #{input}"
      @cache[input]
    else
      result = @calculator.calculate_complex_operation(input)
      @cache[input] = result
      result
    end
  end
end

# Usage
proxy = CalculatorProxy.new

proxy.calculate(10)  # Calculates and caches
proxy.calculate(10)  # Returns from cache
proxy.calculate(20)  # Calculates and caches
proxy.calculate(20)  # Returns from cache
```

## 🎯 Best Practices

### 1. When to Use Each Pattern

```ruby
class PatternGuide
  PATTERNS = {
    adapter: {
      when: "When you need to make incompatible interfaces work together",
      example: "Legacy system integration"
    },
    bridge: {
      when: "When you need to decouple abstraction from implementation",
      example: "Multiple UI frameworks"
    },
    composite: {
      when: "When you need to treat individual objects and compositions uniformly",
      example: "File systems, UI component trees"
    },
    decorator: {
      when: "When you need to add responsibilities dynamically",
      example: "Adding features to objects without subclassing"
    },
    facade: {
      when: "When you need to simplify a complex subsystem",
      example: "API for complex library"
    },
    proxy: {
      when: "When you need to control access to an object",
      example: "Lazy loading, access control, caching"
    }
  }
  
  def self.recommend_for_scenario(scenario)
    PATTERNS.select { |_, details| details[:example].include?(scenario) }
  end
end
```

### 2. Testing Structural Patterns

```ruby
# Testing adapter pattern
RSpec.describe LegacyPaymentAdapter do
  let(:legacy_system) { double("LegacyPaymentSystem") }
  let(:adapter) { LegacyPaymentAdapter.new(legacy_system) }
  
  it "adapts legacy system to modern interface" do
    payment_request = build_payment_request
    
    expect(legacy_system).to receive(:process_payment).with(100.00, "USD", "1234", "12/25", "123")
    
    adapter.charge(payment_request)
  end
end

# Testing composite pattern
RSpec.describe Directory do
  let(:directory) { Directory.new("test") }
  
  it "calculates total size including children" do
    directory.add(File.new("file1.txt", 100))
    directory.add(File.new("file2.txt", 200))
    
    expect(directory.size).to eq(300)
  end
end
```

### 3. Performance Considerations

```ruby
# Lazy loading proxy for better performance
class LazyLoadingProxy
  def initialize(&loader)
    @loader = loader
    @loaded = false
    @object = nil
  end
  
  def method_missing(method_name, *args, &block)
    load_object unless @loaded
    @object.send(method_name, *args, &block)
  end
  
  private
  
  def load_object
    @object = @loader.call
    @loaded = true
  end
end

# Usage
proxy = LazyLoadingProxy.new do
  # Expensive object creation
  sleep(1)
  { data: "expensive data" }
end

# Object is only created when first used
puts proxy[:data]
```

## 🎓 Exercises

### Beginner Exercises

1. **Adapter**: Create an adapter for a third-party API
2. **Composite**: Implement a UI component tree structure
3. **Decorator**: Add features to a base object dynamically

### Intermediate Exercises

1. **Bridge**: Create a messaging system with different senders
2. **Facade**: Simplify a complex library interface
3. **Proxy**: Implement lazy loading for expensive objects

### Advanced Exercises

1. **Smart Proxy**: Create a proxy with caching and logging
2. **Dynamic Decorator**: Implement decorators that can be added at runtime
3. **Composite Iterator**: Create an iterator for composite structures

---

## 🎯 Summary

Structural design patterns provide:

- **Adapter** - Interface compatibility
- **Bridge** - Decoupling abstraction from implementation
- **Composite** - Tree structures for part-whole hierarchies
- **Decorator** - Dynamic responsibility addition
- **Facade** - Simplified subsystem interface
- **Proxy** - Controlled object access

Master these patterns to create flexible, maintainable object compositions!
