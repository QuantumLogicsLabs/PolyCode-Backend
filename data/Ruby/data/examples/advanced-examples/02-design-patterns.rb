# Ruby Design Patterns Best Practices
# This file demonstrates common design patterns implemented in Ruby
# with practical examples and explanations for each pattern.

module BestPracticesExamples
  module DesignPatterns
    # 1. Singleton Pattern
    # Ensures a class has only one instance and provides global access to it
    
    class DatabaseConnection
      include Singleton
      
      def initialize
        @connection = establish_connection
        puts "Database connection established"
      end
      
      def execute_query(sql)
        @connection.execute(sql)
      end
      
      private
      
      def establish_connection
        # Simulate database connection
        OpenStruct.new(execute: ->(sql) { "Executing: #{sql}" })
      end
    end
    
    # 2. Factory Pattern
    # Creates objects without specifying the exact class
    
    class VehicleFactory
      def self.create_vehicle(type, options = {})
        case type.to_sym
        when :car
          Car.new(options)
        when :truck
          Truck.new(options)
        when :motorcycle
          Motorcycle.new(options)
        else
          raise ArgumentError, "Unknown vehicle type: #{type}"
        end
      end
    end
    
    class Vehicle
      attr_reader :make, :model, :year
      
      def initialize(make:, model:, year:)
        @make = make
        @model = model
        @year = year
      end
      
      def start
        "#{@make} #{@@model} is starting"
      end
    end
    
    class Car < Vehicle
      def initialize(options)
        super(make: options[:make], model: options[:model], year: options[:year])
        @doors = options[:doors] || 4
      end
      
      def honk
        "Beep beep!"
      end
    end
    
    class Truck < Vehicle
      def initialize(options)
        super(make: options[:make], model: options[:model], year: options[:year])
        @capacity = options[:capacity] || 1000
      end
      
      def load_cargo(weight)
        "Loading #{weight}kg of cargo"
      end
    end
    
    class Motorcycle < Vehicle
      def initialize(options)
        super(make: options[:make], model: options[:model], year: options[:year])
        @type = options[:type] || :sport
      end
      
      def wheelie
        "Doing a wheelie!"
      end
    end
    
    # 3. Observer Pattern
    # Defines a one-to-many dependency between objects
    
    module ObserverPattern
      class Subject
        def initialize
          @observers = []
          @state = nil
        end
        
        def attach(observer)
          @observers << observer
        end
        
        def detach(observer)
          @observers.delete(observer)
        end
        
        def notify_observers
          @observers.each { |observer| observer.update(@state) }
        end
        
        def set_state(state)
          @state = state
          notify_observers
        end
      end
      
      class ConcreteSubject < Subject
        attr_reader :state
        
        def initialize
          super
          @state = 0
        end
        
        def change_state(new_state)
          @state = new_state
          notify_observers
        end
      end
      
      module Observer
        def update(state)
          raise NotImplementedError, "Subclasses must implement update method"
        end
      end
      
      class ConcreteObserver
        include Observer
        
        def initialize(name)
          @name = name
        end
        
        def update(state)
          puts "#{@name} received update: state is now #{state}"
        end
      end
    end
    
    # 4. Strategy Pattern
    # Defines a family of algorithms, encapsulates each one, and makes them interchangeable
    
    class PaymentProcessor
      def initialize(payment_strategy)
        @payment_strategy = payment_strategy
      end
      
      def process_payment(amount)
        @payment_strategy.process(amount)
      end
      
      def change_strategy(new_strategy)
        @payment_strategy = new_strategy
      end
    end
    
    module PaymentStrategy
      def process(amount)
        raise NotImplementedError, "Subclasses must implement process method"
      end
    end
    
    class CreditCardPayment
      include PaymentStrategy
      
      def process(amount)
        puts "Processing credit card payment of $#{amount}"
        "Credit card payment of $#{amount} processed successfully"
      end
    end
    
    class PayPalPayment
      include PaymentStrategy
      
      def process(amount)
        puts "Processing PayPal payment of $#{amount}"
        "PayPal payment of $#{amount} processed successfully"
      end
    end
    
    class BitcoinPayment
      include PaymentStrategy
      
      def process(amount)
        puts "Processing Bitcoin payment of $#{amount}"
        "Bitcoin payment of $#{amount} processed successfully"
      end
    end
    
    # 5. Command Pattern
    # Encapsulates a request as an object
    
    module CommandPattern
      class Command
        def execute
          raise NotImplementedError, "Subclasses must implement execute method"
        end
        
        def undo
          raise NotImplementedError, "Subclasses must implement undo method"
        end
      end
      
      class Light
        attr_reader :is_on
        
        def initialize
          @is_on = false
        end
        
        def turn_on
          @is_on = true
          puts "Light is on"
        end
        
        def turn_off
          @is_on = false
          puts "Light is off"
        end
      end
      
      class LightOnCommand < Command
        def initialize(light)
          @light = light
        end
        
        def execute
          @light.turn_on
        end
        
        def undo
          @light.turn_off
        end
      end
      
      class LightOffCommand < Command
        def initialize(light)
          @light = light
        end
        
        def execute
          @light.turn_off
        end
        
        def undo
          @light.turn_on
        end
      end
      
      class RemoteControl
        def initialize
          @command = nil
          @history = []
        end
        
        def set_command(command)
          @command = command
        end
        
        def press_button
          return unless @command
          
          @command.execute
          @history << @command
        end
        
        def press_undo
          return if @history.empty?
          
          last_command = @history.pop
          last_command.undo
        end
      end
    end
    
    # 6. Decorator Pattern
    # Adds new functionality to an object dynamically
    
    class Coffee
      def cost
        2.50
      end
      
      def description
        "Simple coffee"
      end
    end
    
    class CoffeeDecorator
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
    
    class MilkDecorator < CoffeeDecorator
      def cost
        @coffee.cost + 0.50
      end
      
      def description
        "#{@coffee.description}, milk"
      end
    end
    
    class SugarDecorator < CoffeeDecorator
      def cost
        @coffee.cost + 0.20
      end
      
      def description
        "#{@coffee.description}, sugar"
      end
    end
    
    class WhippedCreamDecorator < CoffeeDecorator
      def cost
        @coffee.cost + 0.70
      end
      
      def description
        "#{@coffee.description}, whipped cream"
      end
    end
    
    # 7. Adapter Pattern
    # Allows incompatible interfaces to work together
    
    class LegacyPaymentSystem
      def process_payment(amount_in_cents)
        puts "Processing #{amount_in_cents} cents through legacy system"
        "Payment processed: #{amount_in_cents} cents"
      end
    end
    
    class ModernPaymentSystem
      def make_payment(amount_in_dollars)
        puts "Making payment of $#{amount_in_dollars} through modern system"
        "Payment made: $#{amount_in_dollars}"
      end
    end
    
    class PaymentAdapter
      def initialize(legacy_system)
        @legacy_system = legacy_system
      end
      
      def make_payment(amount_in_dollars)
        amount_in_cents = (amount_in_dollars * 100).to_i
        @legacy_system.process_payment(amount_in_cents)
      end
    end
    
    # 8. Template Method Pattern
    # Defines the skeleton of an algorithm in a method, deferring some steps to subclasses
    
    class DataProcessor
      def process_data(data)
        validate_data(data)
        transform_data(data)
        save_data(data)
        notify_completion(data)
      end
      
      protected
      
      def validate_data(data)
        puts "Validating data"
        raise ArgumentError, "Invalid data" unless data_valid?(data)
      end
      
      def transform_data(data)
        puts "Transforming data"
        perform_transformation(data)
      end
      
      def save_data(data)
        puts "Saving data"
        perform_save(data)
      end
      
      def notify_completion(data)
        puts "Notifying completion"
        send_notification(data)
      end
      
      private
      
      def data_valid?(data)
        true
      end
      
      def perform_transformation(data)
        # Default implementation
      end
      
      def perform_save(data)
        # Default implementation
      end
      
      def send_notification(data)
        # Default implementation
      end
    end
    
    class CSVDataProcessor < DataProcessor
      private
      
      def data_valid?(data)
        data.is_a?(Array) && data.all? { |row| row.is_a?(Hash) }
      end
      
      def perform_transformation(data)
        data.each { |row| row[:processed] = true }
      end
      
      def perform_save(data)
        File.write('output.csv', data.map(&:to_csv).join)
      end
      
      def send_notification(data)
        puts "CSV processing completed for #{data.length} records"
      end
    end
    
    class JSONDataProcessor < DataProcessor
      private
      
      def data_valid?(data)
        data.is_a?(Hash) || data.is_a?(Array)
      end
      
      def perform_transformation(data)
        data.transform_keys(&:to_sym)
      end
      
      def perform_save(data)
        File.write('output.json', JSON.pretty_generate(data))
      end
      
      def send_notification(data)
        puts "JSON processing completed"
      end
    end
    
    # 9. Iterator Pattern
    # Provides a way to access the elements of an aggregate object sequentially
    
    class BookCollection
      def initialize
        @books = []
      end
      
      def add_book(book)
        @books << book
      end
      
      def create_iterator
        BookIterator.new(@books)
      end
    end
    
    class Book
      attr_reader :title, :author, :year
      
      def initialize(title:, author:, year:)
        @title = title
        @author = author
        @year = year
      end
    end
    
    class BookIterator
      def initialize(books)
        @books = books
        @current_index = 0
      end
      
      def has_next?
        @current_index < @books.length
      end
      
      def next
        return nil unless has_next?
        
        book = @books[@current_index]
        @current_index += 1
        book
      end
      
      def reset
        @current_index = 0
      end
    end
    
    # 10. Builder Pattern
    # Separates the construction of a complex object from its representation
    
    class Computer
      attr_reader :cpu, :memory, :storage, :graphics_card, :operating_system
      
      def initialize
        @cpu = nil
        @memory = nil
        @storage = nil
        @graphics_card = nil
        @operating_system = nil
      end
      
      def to_s
        "Computer: #{@cpu || 'No CPU'}, #{@memory || 'No Memory'}, #{@storage || 'No Storage'}, #{@graphics_card || 'No Graphics'}, #{@operating_system || 'No OS'}"
      end
    end
    
    class ComputerBuilder
      def initialize
        @computer = Computer.new
      end
      
      def set_cpu(cpu)
        @computer.instance_variable_set(:@cpu, cpu)
        self
      end
      
      def set_memory(memory)
        @computer.instance_variable_set(:@memory, memory)
        self
      end
      
      def set_storage(storage)
        @computer.instance_variable_set(:@storage, storage)
        self
      end
      
      def set_graphics_card(card)
        @computer.instance_variable_set(:@graphics_card, card)
        self
      end
      
      def set_operating_system(os)
        @computer.instance_variable_set(:@operating_system, os)
        self
      end
      
      def build
        @computer
      end
    end
    
    class ComputerDirector
      def self.build_gaming_computer
        ComputerBuilder.new
          .set_cpu('Intel Core i9')
          .set_memory('32GB DDR4')
          .set_storage('1TB NVMe SSD')
          .set_graphics_card('NVIDIA RTX 3080')
          .set_operating_system('Windows 10')
          .build
      end
      
      def self.build_office_computer
        ComputerBuilder.new
          .set_cpu('Intel Core i5')
          .set_memory('8GB DDR4')
          .set_storage('256GB SSD')
          .set_graphics_card('Integrated Intel HD')
          .set_operating_system('Windows 10')
          .build
      end
    end
    
    # 11. Facade Pattern
    # Provides a simplified interface to a complex subsystem
    
    class ComputerSubsystem
      class CPU
        def start
          puts "CPU starting up..."
        end
        
        def shutdown
          puts "CPU shutting down..."
        end
      end
      
      class Memory
        def check
          puts "Checking memory..."
        end
        
        def load
          puts "Loading memory..."
        end
      end
      
      class HardDrive
        def read
          puts "Reading from hard drive..."
        end
        
        def write
          puts "Writing to hard drive..."
        end
      end
      
      class GraphicsCard
        def initialize
          @cpu = CPU.new
          @memory = Memory.new
          @hard_drive = HardDrive.new
        end
        
        def start
          @cpu.start
          @memory.check
          @memory.load
          @hard_drive.read
          puts "Computer started successfully"
        end
        
        def shutdown
          @hard_drive.write
          @cpu.shutdown
          puts "Computer shut down successfully"
        end
      end
    end
    
    class ComputerFacade
      def initialize
        @subsystem = ComputerSubsystem::GraphicsCard.new
      end
      
      def start_computer
        puts "Starting computer..."
        @subsystem.start
        puts "Computer is ready to use"
      end
      
      def shutdown_computer
        puts "Shutting down computer..."
        @subsystem.shutdown
        puts "Computer is off"
      end
    end
    
    # 12. Proxy Pattern
    # Provides a surrogate or placeholder for another object to control access to it
    
    class RealImage
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
        sleep(1) # Simulate loading time
      end
    end
    
    class ImageProxy
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
    
    # 13. Chain of Responsibility Pattern
    # Passes a request along a chain of handlers
    
    module ChainOfResponsibility
      class Handler
        def initialize(successor = nil)
          @successor = successor
        end
        
        def handle_request(request)
          if can_handle?(request)
            process_request(request)
          elsif @successor
            @successor.handle_request(request)
          else
            puts "No handler found for request: #{request}"
          end
        end
        
        protected
        
        def can_handle?(request)
          false
        end
        
        def process_request(request)
          puts "Handling request: #{request}"
        end
      end
      
      class ConcreteHandler1 < Handler
        protected
        
        def can_handle?(request)
          request == 'request1'
        end
        
        def process_request(request)
          puts "Handler1 processing: #{request}"
        end
      end
      
      class ConcreteHandler2 < Handler
        protected
        
        def can_handle?(request)
          request == 'request2'
        end
        
        def process_request(request)
          puts "Handler2 processing: #{request}"
        end
      end
      
      class ConcreteHandler3 < Handler
        protected
        
        def can_handle?(request)
          request == 'request3'
        end
        
        def process_request(request)
          puts "Handler3 processing: #{request}"
        end
      end
    end
    
    # 14. State Pattern
    # Allows an object to change its behavior when its internal state changes
    
    class TrafficLight
      def initialize
        @state = RedState.new(self)
      end
      
      def change_state(state)
        @state = state
      end
      
      def turn_on
        @state.turn_on
      end
      
      def turn_off
        @state.turn_off
      end
      
      def report
        @state.report
      end
    end
    
    class TrafficLightState
      def turn_on
        raise NotImplementedError, "Subclasses must implement turn_on"
      end
      
      def turn_off
        raise NotImplementedError, "Subclasses must implement turn_off"
      end
      
      def report
        raise NotImplementedError, "Subclasses must implement report"
      end
    end
    
    class RedState < TrafficLightState
      def initialize(traffic_light)
        @traffic_light = traffic_light
      end
      
      def turn_on
        puts "Red light is already on"
      end
      
      def turn_off
        puts "Switching red light off"
        @traffic_light.change_state(GreenState.new(@traffic_light))
      end
      
      def report
        "Red light is on - STOP"
      end
    end
    
    class GreenState < TrafficLightState
      def initialize(traffic_light)
        @traffic_light = traffic_light
      end
      
      def turn_on
        puts "Green light is already on"
      end
      
      def turn_off
        puts "Switching green light off"
        @traffic_light.change_state(YellowState.new(@traffic_light))
      end
      
      def report
        "Green light is on - GO"
      end
    end
    
    class YellowState < TrafficLightState
      def initialize(traffic_light)
        @traffic_light = traffic_light
      end
      
      def turn_on
        puts "Yellow light is already on"
      end
      
      def turn_off
        puts "Switching yellow light off"
        @traffic_light.change_state(RedState.new(@traffic_light))
      end
      
      def report
        "Yellow light is on - CAUTION"
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "Ruby Design Patterns Demonstration"
  puts "=" * 60
  
  # Singleton Pattern
  puts "\n1. Singleton Pattern:"
  db1 = BestPracticesExamples::DesignPatterns::DatabaseConnection.instance
  db2 = BestPracticesExamples::DesignPatterns::DatabaseConnection.instance
  puts "Same instance: #{db1.object_id == db2.object_id}"
  
  # Factory Pattern
  puts "\n2. Factory Pattern:"
  car = BestPracticesExamples::DesignPatterns::VehicleFactory.create_vehicle(:car, 
    make: 'Toyota', model: 'Camry', year: 2022)
  truck = BestPracticesExamples::DesignPatterns::VehicleFactory.create_vehicle(:truck,
    make: 'Ford', model: 'F-150', year: 2022)
  puts "Created: #{car.start}"
  puts "Created: #{truck.start}"
  
  # Observer Pattern
  puts "\n3. Observer Pattern:"
  subject = BestPracticesExamples::DesignPatterns::ObserverPattern::ConcreteSubject.new
  observer1 = BestPracticesExamples::DesignPatterns::ObserverPattern::ConcreteObserver.new('Observer 1')
  observer2 = BestPracticesExamples::DesignPatterns::ObserverPattern::ConcreteObserver.new('Observer 2')
  
  subject.attach(observer1)
  subject.attach(observer2)
  subject.change_state('New State')
  
  # Strategy Pattern
  puts "\n4. Strategy Pattern:"
  processor = BestPracticesExamples::DesignPatterns::PaymentProcessor.new(
    BestPracticesExamples::DesignPatterns::CreditCardPayment.new
  )
  puts processor.process_payment(100)
  
  processor.change_strategy(BestPracticesExamples::DesignPatterns::PayPalPayment.new)
  puts processor.process_payment(50)
  
  # Command Pattern
  puts "\n5. Command Pattern:"
  light = BestPracticesExamples::DesignPatterns::CommandPattern::Light.new
  light_on = BestPracticesExamples::DesignPatterns::CommandPattern::LightOnCommand.new(light)
  light_off = BestPracticesExamples::DesignPatterns::CommandPattern::LightOffCommand.new(light)
  
  remote = BestPracticesExamples::DesignPatterns::CommandPattern::RemoteControl.new
  remote.set_command(light_on)
  remote.press_button
  
  remote.set_command(light_off)
  remote.press_button
  remote.press_undo
  
  # Decorator Pattern
  puts "\n6. Decorator Pattern:"
  coffee = BestPracticesExamples::DesignPatterns::Coffee.new
  puts "#{coffee.description}: $#{coffee.cost}"
  
  coffee_with_milk = BestPracticesExamples::DesignPatterns::MilkDecorator.new(coffee)
  puts "#{coffee_with_milk.description}: $#{coffee_with_milk.cost}"
  
  coffee_with_milk_sugar = BestPracticesExamples::DesignPatterns::SugarDecorator.new(coffee_with_milk)
  puts "#{coffee_with_milk_sugar.description}: $#{coffee_with_milk_sugar.cost}"
  
  # Template Method Pattern
  puts "\n7. Template Method Pattern:"
  csv_processor = BestPracticesExamples::DesignPatterns::CSVDataProcessor.new
  csv_processor.process_data([{ name: 'John', age: 30 }])
  
  # Iterator Pattern
  puts "\n8. Iterator Pattern:"
  collection = BestPracticesExamples::DesignPatterns::BookCollection.new
  collection.add_book(BestPracticesExamples::DesignPatterns::Book.new(title: 'Ruby Programming', author: 'John Doe', year: 2020))
  collection.add_book(BestPracticesExamples::DesignPatterns::Book.new(title: 'Design Patterns', author: 'Jane Smith', year: 2019))
  
  iterator = collection.create_iterator
  while iterator.has_next?
    book = iterator.next
    puts "Book: #{book.title} by #{book.author}"
  end
  
  # Builder Pattern
  puts "\n9. Builder Pattern:"
  gaming_pc = BestPracticesExamples::DesignPatterns::ComputerDirector.build_gaming_computer
  puts "Gaming PC: #{gaming_pc}"
  
  office_pc = BestPracticesExamples::DesignPatterns::ComputerDirector.build_office_computer
  puts "Office PC: #{office_pc}"
  
  # Facade Pattern
  puts "\n10. Facade Pattern:"
  facade = BestPracticesExamples::DesignPatterns::ComputerFacade.new
  facade.start_computer
  facade.shutdown_computer
  
  # State Pattern
  puts "\n11. State Pattern:"
  traffic_light = BestPracticesExamples::DesignPatterns::TrafficLight.new
  puts traffic_light.report
  traffic_light.turn_off
  puts traffic_light.report
  traffic_light.turn_off
  puts traffic_light.report
  
  puts "\nDesign patterns help write maintainable and extensible code!"
end
