# Behavioral Design Patterns in Ruby
# Comprehensive guide to object interaction patterns

## 🎯 Overview

Behavioral design patterns are concerned with algorithms and the assignment of responsibilities between objects. They describe not just patterns of objects or classes but also patterns of communication between them.

## 📝 Strategy Pattern

The Strategy pattern defines a family of algorithms, encapsulates each one, and makes them interchangeable.

```ruby
# Strategy interface
class PaymentStrategy
  def pay(amount)
    raise NotImplementedError, "Subclasses must implement pay"
  end
end

# Concrete strategies
class CreditCardPayment < PaymentStrategy
  def initialize(card_number, expiry_date, cvv)
    @card_number = card_number
    @expiry_date = expiry_date
    @cvv = cvv
  end
  
  def pay(amount)
    puts "Processing credit card payment of $#{amount}"
    puts "Card: ****-****-****-#{@card_number[-4..-1]}"
    puts "Expiry: #{@expiry_date}"
    "Credit card payment processed"
  end
end

class PayPalPayment < PaymentStrategy
  def initialize(email, password)
    @email = email
    @password = password
  end
  
  def pay(amount)
    puts "Processing PayPal payment of $#{amount}"
    puts "Account: #{@email}"
    "PayPal payment processed"
  end
end

class BitcoinPayment < PaymentStrategy
  def initialize(wallet_address)
    @wallet_address = wallet_address
  end
  
  def pay(amount)
    puts "Processing Bitcoin payment of $#{amount} BTC"
    puts "Wallet: #{@wallet_address[0..10]}..."
    "Bitcoin payment processed"
  end
end

class BankTransferPayment < PaymentStrategy
  def initialize(account_number, routing_number)
    @account_number = account_number
    @routing_number = routing_number
  end
  
  def pay(amount)
    puts "Processing bank transfer of $#{amount}"
    puts "Account: ****#{@account_number[-4..-1]}"
    "Bank transfer processed"
  end
end

# Context
class PaymentProcessor
  def initialize(strategy)
    @strategy = strategy
  end
  
  def set_strategy(strategy)
    @strategy = strategy
  end
  
  def process_payment(amount)
    @strategy.pay(amount)
  end
end

# Usage
processor = PaymentProcessor.new(CreditCardPayment.new("1234567890123456", "12/25", "123"))
processor.process_payment(100.50)

# Change strategy at runtime
processor.set_strategy(PayPalPayment.new("user@example.com", "password123"))
processor.process_payment(75.25)

processor.set_strategy(BitcoinPayment.new("1A2B3C4D5E6F7G8H9I0J"))
processor.process_payment(50.00)

processor.set_strategy(BankTransferPayment.new("123456789", "987654321"))
processor.process_payment(200.00)
```

## 👀 Observer Pattern

The Observer pattern defines a one-to-many dependency between objects so that when one object changes state, all its dependents are notified and updated automatically.

```ruby
# Subject interface
class Subject
  def add_observer(observer)
    raise NotImplementedError, "Subclasses must implement add_observer"
  end
  
  def remove_observer(observer)
    raise NotImplementedError, "Subclasses must implement remove_observer"
  end
  
  def notify_observers
    raise NotImplementedError, "Subclasses must implement notify_observers"
  end
end

# Concrete subject
class WeatherStation < Subject
  def initialize
    @observers = []
    @temperature = 0
    @humidity = 0
    @pressure = 0
  end
  
  def add_observer(observer)
    @observers << observer
    puts "Added observer: #{observer.class}"
  end
  
  def remove_observer(observer)
    @observers.delete(observer)
    puts "Removed observer: #{observer.class}"
  end
  
  def notify_observers
    @observers.each { |observer| observer.update(@temperature, @humidity, @pressure) }
  end
  
  def set_measurements(temperature, humidity, pressure)
    @temperature = temperature
    @humidity = humidity
    @pressure = pressure
    puts "Weather updated: #{@temperature}°C, #{@humidity}%, #{@pressure} hPa"
    notify_observers
  end
end

# Observer interface
class Observer
  def update(temperature, humidity, pressure)
    raise NotImplementedError, "Subclasses must implement update"
  end
end

# Concrete observers
class PhoneDisplay < Observer
  def update(temperature, humidity, pressure)
    puts "Phone Display: #{temperature}°C, #{humidity}%, #{pressure} hPa"
  end
end

class WebDisplay < Observer
  def update(temperature, humidity, pressure)
    puts "Web Display: Current weather - #{temperature}°C, #{humidity}%, #{pressure} hPa"
  end
end

class TVDisplay < Observer
  def update(temperature, humidity, pressure)
    puts "TV Display: Weather Update - #{temperature}°C, #{humidity}%, #{pressure} hPa"
  end
end

# Usage
weather_station = WeatherStation.new

# Add observers
phone = PhoneDisplay.new
web = WebDisplay.new
tv = TVDisplay.new

weather_station.add_observer(phone)
weather_station.add_observer(web)
weather_station.add_observer(tv)

# Update weather data
weather_station.set_measurements(25.5, 65, 1013)
weather_station.set_measurements(26.0, 70, 1015)

# Remove an observer
weather_station.remove_observer(tv)
weather_station.set_measurements(24.5, 60, 1012)

# Event-driven observer example
class EventPublisher
  def initialize
    @subscribers = {}
  end
  
  def subscribe(event_type, subscriber)
    @subscribers[event_type] ||= []
    @subscribers[event_type] << subscriber
  end
  
  def unsubscribe(event_type, subscriber)
    @subscribers[event_type]&.delete(subscriber)
  end
  
  def publish(event_type, data = {})
    subscribers = @subscribers[event_type] || []
    subscribers.each { |subscriber| subscriber.handle_event(event_type, data) }
  end
end

class EventSubscriber
  def handle_event(event_type, data)
    puts "Received #{event_type}: #{data}"
  end
end

# Usage
publisher = EventPublisher.new
subscriber1 = EventSubscriber.new
subscriber2 = EventSubscriber.new

publisher.subscribe("user_registered", subscriber1)
publisher.subscribe("user_logged_in", subscriber2)

publisher.publish("user_registered", { user_id: 123, email: "user@example.com" })
publisher.publish("user_logged_in", { user_id: 123, timestamp: Time.now })
```

## 🔄 Command Pattern

The Command pattern encapsulates a request as an object, thereby letting you parameterize clients with different requests, queue or log requests, and support undoable operations.

```ruby
# Command interface
class Command
  def execute
    raise NotImplementedError, "Subclasses must implement execute"
  end
  
  def undo
    raise NotImplementedError, "Subclasses must implement undo"
  end
end

# Concrete commands
class LightOnCommand < Command
  def initialize(light)
    @light = light
    @previous_state = nil
  end
  
  def execute
    @previous_state = @light.is_on?
    @light.turn_on
  end
  
  def undo
    @light.turn_off if @previous_state == false
  end
end

class LightOffCommand < Command
  def initialize(light)
    @light = light
    @previous_state = nil
  end
  
  def execute
    @previous_state = @light.is_on?
    @light.turn_off
  end
  
  def undo
    @light.turn_on if @previous_state == true
  end
end

class ThermostatSetCommand < Command
  def initialize(thermostat, temperature)
    @thermostat = thermostat
    @temperature = temperature
    @previous_temperature = nil
  end
  
  def execute
    @previous_temperature = @thermostat.current_temperature
    @thermostat.set_temperature(@temperature)
  end
  
  def undo
    @thermostat.set_temperature(@previous_temperature) if @previous_temperature
  end
end

class SecuritySystemCommand < Command
  def initialize(security_system, action)
    @security_system = security_system
    @action = action
    @previous_state = nil
  end
  
  def execute
    @previous_state = @security_system.is_armed?
    case @action
    when :arm
      @security_system.arm
    when :disarm
      @security_system.disarm
    end
  end
  
  def undo
    case @previous_state
    when true
      @security_system.arm if @action == :disarm
    when false
      @security_system.disarm if @action == :arm
    end
  end
end

# Receiver classes
class Light
  def turn_on
    puts "Light is ON"
    @on = true
  end
  
  def turn_off
    puts "Light is OFF"
    @on = false
  end
  
  def is_on?
    @on || false
  end
end

class Thermostat
  attr_reader :current_temperature
  
  def initialize
    @current_temperature = 20
  end
  
  def set_temperature(temperature)
    puts "Setting thermostat to #{temperature}°C"
    @current_temperature = temperature
  end
end

class SecuritySystem
  def initialize
    @armed = false
  end
  
  def arm
    puts "Security system ARMED"
    @armed = true
  end
  
  def disarm
    puts "Security system DISARMED"
    @armed = false
  end
  
  def is_armed?
    @armed
  end
end

# Invoker
class RemoteControl
  def initialize
    @commands = []
    @undo_stack = []
  end
  
  def set_command(command)
    @commands << command
  end
  
  def execute_command(index)
    return unless @commands[index]
    
    command = @commands[index]
    command.execute
    @undo_stack << command
  end
  
  def undo
    return if @undo_stack.empty?
    
    last_command = @undo_stack.pop
    last_command.undo
  end
  
  def list_commands
    @commands.each_with_index do |command, index|
      puts "#{index}: #{command.class}"
    end
  end
end

# Macro command (command that contains multiple commands)
class MacroCommand < Command
  def initialize(commands)
    @commands = commands
  end
  
  def execute
    @commands.each(&:execute)
  end
  
  def undo
    @commands.reverse.each(&:undo)
  end
end

# Usage
living_room_light = Light.new
thermostat = Thermostat.new
security_system = SecuritySystem.new

remote = RemoteControl.new

# Set up commands
remote.set_command(LightOnCommand.new(living_room_light))
remote.set_command(LightOffCommand.new(living_room_light))
remote.set_command(ThermostatSetCommand.new(thermostat, 22))
remote.set_command(SecuritySystemCommand.new(security_system, :arm))
remote.set_command(SecuritySystemCommand.new(security_system, :disarm))

# Execute commands
puts "Available commands:"
remote.list_commands

puts "\nExecuting commands:"
remote.execute_command(0)  # Light on
remote.execute_command(2)  # Set thermostat
remote.execute_command(3)  # Arm security system

puts "\nUndoing last command:"
remote.undo

# Macro command example
puts "\nCreating macro command for 'Good Night' routine:"
good_night_commands = [
  LightOffCommand.new(living_room_light),
  ThermostatSetCommand.new(thermostat, 18),
  SecuritySystemCommand.new(security_system, :arm)
]

good_night_macro = MacroCommand.new(good_night_commands)
good_night_macro.execute

puts "\nUndoing macro command:"
good_night_macro.undo
```

## 🏪 Iterator Pattern

The Iterator pattern provides a way to access the elements of an aggregate object sequentially without exposing its underlying representation.

```ruby
# Iterator interface
class Iterator
  def has_next?
    raise NotImplementedError, "Subclasses must implement has_next?"
  end
  
  def next
    raise NotImplementedError, "Subclasses must implement next"
  end
  
  def reset
    raise NotImplementedError, "Subclasses must implement reset"
  end
end

# Concrete iterator
class ArrayIterator < Iterator
  def initialize(array)
    @array = array
    @index = 0
  end
  
  def has_next?
    @index < @array.length
  end
  
  def next
    raise StopIteration unless has_next?
    
    item = @array[@index]
    @index += 1
    item
  end
  
  def reset
    @index = 0
  end
end

# Aggregate interface
class Aggregate
  def create_iterator
    raise NotImplementedError, "Subclasses must implement create_iterator"
  end
end

# Concrete aggregate
class NumberCollection < Aggregate
  def initialize
    @numbers = []
  end
  
  def add(number)
    @numbers << number
  end
  
  def create_iterator
    ArrayIterator.new(@numbers)
  end
end

# Usage
collection = NumberCollection.new
collection.add(1)
collection.add(2)
collection.add(3)
collection.add(4)
collection.add(5)

iterator = collection.create_iterator

puts "Iterating through collection:"
while iterator.has_next?
  puts "Next number: #{iterator.next}"
end

iterator.reset
puts "\nReset and iterate again:"
iterator.each { |number| puts "Number: #{number}" }

# Tree iterator example
class TreeNode
  attr_accessor :value, :left, :right
  
  def initialize(value)
    @value = value
    @left = nil
    @right = nil
  end
end

class TreeIterator < Iterator
  def initialize(root)
    @root = root
    @stack = []
    @current = nil
    reset
  end
  
  def has_next?
    !@stack.empty? || @current
  end
  
  def next
    raise StopIteration unless has_next?
    
    if @current
      value = @current.value
      move_to_next
      value
    else
      value = @stack.pop.value
      @current = value.right
      @stack.push(value.left) if value.left
      value
    end
  end
  
  def reset
    @stack = [@root] if @root
    @current = @root
  end
  
  private
  
  def move_to_next
    return unless @current
    
    # Move to leftmost node of right subtree
    while @current
      @stack.push(@current)
      @current = @current.left
    end
  end
end

# Usage
# Build a binary tree
root = TreeNode.new(5)
root.left = TreeNode.new(3)
root.right = TreeNode.new(7)
root.left.left = TreeNode.new(2)
root.left.right = TreeNode.new(4)
root.right.left = TreeNode.new(6)
root.right.right = TreeNode.new(8)

tree_iterator = TreeIterator.new(root)
puts "\nIterating through tree (in-order):"
while tree_iterator.has_next?
  puts "Tree value: #{tree_iterator.next}"
end
```

## 🤝 Mediator Pattern

The Mediator pattern defines an object that centralizes communications between a set of objects.

```ruby
# Mediator interface
class ChatMediator
  def register_user(user)
    raise NotImplementedError, "Subclasses must implement register_user"
  end
  
  def send_message(message, sender)
    raise NotImplementedError, "Subclasses must implement send_message"
  end
end

# Concrete mediator
class ChatRoom < ChatMediator
  def initialize
    @users = []
  end
  
  def register_user(user)
    @users << user
    user.set_mediator(self)
    puts "#{user.name} joined the chat room"
  end
  
  def send_message(message, sender)
    puts "#{sender.name}: #{message}"
    
    @users.each do |user|
      user.receive(message, sender) unless user == sender
    end
  end
end

# Colleague interface
class User
  attr_reader :name
  
  def initialize(name)
    @name = name
    @mediator = nil
  end
  
  def set_mediator(mediator)
    @mediator = mediator
  end
  
  def send(message)
    @mediator.send_message(message, self)
  end
  
  def receive(message, sender)
    puts "#{@name} received message from #{sender.name}: #{message}"
  end
end

# Usage
chat_room = ChatRoom.new

alice = User.new("Alice")
bob = User.new("Bob")
charlie = User.new("Charlie")

chat_room.register_user(alice)
chat_room.register_user(bob)
chat_room.register_user(charlie)

alice.send("Hello everyone!")
bob.send("Hi Alice!")
charlie.send("Good to see you all!")

# Control tower mediator example
class AirTrafficControlMediator
  def initialize
    @aircraft = []
    @runways = {}
    @flight_plans = {}
  end
  
  def register_aircraft(aircraft)
    @aircraft << aircraft
    aircraft.set_mediator(self)
    puts "Aircraft #{aircraft.flight_number} registered"
  end
  
  def request_takeoff(aircraft)
    runway = assign_runway(aircraft)
    if runway
      puts "Tower: #{aircraft.flight_number} cleared for takeoff on runway #{runway}"
      aircraft.takeoff_granted(runway)
    else
      puts "Tower: #{aircraft.flight_number} hold position, no runway available"
      aircraft.takeoff_denied
    end
  end
  
  def request_landing(aircraft)
    runway = assign_runway(aircraft)
    if runway
      puts "Tower: #{aircraft.flight_number} cleared for landing on runway #{runway}"
      aircraft.landing_granted(runway)
    else
      puts "Tower: #{aircraft.flight_number} circle, no runway available"
      aircraft.landing_denied
    end
  end
  
  def report_position(aircraft, altitude, position)
    puts "Tower: #{aircraft.flight_number} at #{altitude}ft, position #{position}"
    notify_nearby_aircraft(aircraft, altitude, position)
  end
  
  private
  
  def assign_runway(aircraft)
    available_runways = ["RW1", "RW2", "RW3"].reject { |rw| @runways[rw] }
    return nil if available_runways.empty?
    
    runway = available_runways.first
    @runways[runway] = aircraft
    runway
  end
  
  def notify_nearby_aircraft(aircraft, altitude, position)
    @aircraft.each do |other|
      next if other == aircraft
      other.nearby_aircraft_position(aircraft, altitude, position)
    end
  end
end

class Aircraft
  attr_reader :flight_number
  
  def initialize(flight_number)
    @flight_number = flight_number
    @mediator = nil
  end
  
  def set_mediator(mediator)
    @mediator = mediator
  end
  
  def request_takeoff
    @mediator.request_takeoff(self)
  end
  
  def request_landing
    @mediator.request_landing(self)
  end
  
  def report_position(altitude, position)
    @mediator.report_position(self, altitude, position)
  end
  
  def takeoff_granted(runway)
    puts "#{@flight_number}: Taking off from runway #{runway}"
  end
  
  def takeoff_denied
    puts "#{@flight_number}: Takeoff denied"
  end
  
  def landing_granted(runway)
    puts "#{@flight_number}: Landing on runway #{runway}"
  end
  
  def landing_denied
    puts "#{@flight_number}: Landing denied"
  end
  
  def nearby_aircraft_position(aircraft, altitude, position)
    puts "#{@flight_number}: Nearby #{aircraft.flight_number} at #{altitude}ft, position #{position}"
  end
end

# Usage
tower = AirTrafficControlMediator.new

flight1 = Aircraft.new("UA123")
flight2 = Aircraft.new("AA456")
flight3 = Aircraft.new("DL789")

tower.register_aircraft(flight1)
tower.register_aircraft(flight2)
tower.register_aircraft(flight3)

flight1.request_takeoff
flight2.request_takeoff
flight3.request_landing

flight1.report_position(5000, "10 miles north")
flight2.report_position(3000, "5 miles east")
```

## 🎯 Best Practices

### 1. Pattern Selection Guide

```ruby
class PatternSelector
  PATTERNS = {
    strategy: {
      use_case: "Multiple algorithms that can be swapped",
      ruby_alternative: "Blocks and procs"
    },
    observer: {
      use_case: "One-to-many notifications",
      ruby_alternative: "Observable module, callbacks"
    },
    command: {
      use_case: "Encapsulating requests as objects",
      ruby_alternative: "Method objects, lambdas"
    },
    iterator: {
      use_case: "Sequential access to collections",
      ruby_alternative: "Enumerable module"
    },
    mediator: {
      use_case: "Centralizing complex communications",
      ruby_alternative: "Event systems"
    }
  }
  
  def self.recommend_for_use_case(use_case)
    PATTERNS.select { |_, details| details[:use_case].include?(use_case) }
  end
end
```

### 2. Performance Considerations

```ruby
# Efficient observer implementation with weak references
require 'set'

class OptimizedObservable
  def initialize
    @observers = Set.new
  end
  
  def add_observer(observer)
    @observers << observer
  end
  
  def remove_observer(observer)
    @observers.delete(observer)
  end
  
  def notify_observers(*args)
    @observers.each { |observer| observer.update(*args) }
  end
end

# Lazy iterator for large datasets
class LazyCollectionIterator
  include Enumerable
  
  def initialize(collection)
    @collection = collection
    @position = 0
  end
  
  def each(&block)
    while @position < @collection.size
      yield @collection[@position]
      @position += 1
    end
  end
end
```

### 3. Testing Behavioral Patterns

```ruby
# Testing strategy pattern
RSpec.describe PaymentProcessor do
  let(:processor) { PaymentProcessor.new(strategy) }
  
  context "with credit card strategy" do
    let(:strategy) { CreditCardPayment.new("1234", "12/25", "123") }
    
    it "processes credit card payment" do
      expect(strategy).to receive(:pay).with(100.50)
      processor.process_payment(100.50)
    end
  end
  
  context "with PayPal strategy" do
    let(:strategy) { PayPalPayment.new("user@example.com", "pass") }
    
    it "processes PayPal payment" do
      expect(strategy).to receive(:pay).with(75.25)
      processor.process_payment(75.25)
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Strategy**: Create different sorting algorithms
2. **Observer**: Implement a weather notification system
3. **Command**: Build a text editor with undo/redo

### Intermediate Exercises

1. **Iterator**: Create iterators for different data structures
2. **Mediator**: Implement a chat system with a mediator
3. **State Pattern**: Add state management to objects

### Advanced Exercises

1. **Memento**: Implement undo/redo with memento pattern
2. **Visitor**: Add operations to object structures
3. **Chain of Responsibility**: Create a request handling chain

---

## 🎯 Summary

Behavioral design patterns provide:

- **Strategy** - Interchangeable algorithms
- **Observer** - Event notification system
- **Command** - Encapsulated requests
- **Iterator** - Sequential access patterns
- **Mediator** - Centralized communication
- **State** - Object state management

Master these patterns to create flexible, maintainable object interactions!
