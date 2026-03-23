# Creational Design Patterns in Ruby
# Comprehensive guide to object creation patterns

## 🎯 Overview

Creational design patterns abstract the instantiation process and help make a system independent of how its objects are created, composed, and represented. This guide covers the essential creational patterns with Ruby implementations.

## 🏭 Factory Pattern

### 1. Simple Factory

The Simple Factory pattern provides a centralized way to create objects without exposing the creation logic to the client.

```ruby
class SimpleVehicleFactory
  def self.create_vehicle(type, options = {})
    case type
    when :car
      Car.new(options[:make], options[:model], options[:color])
    when :motorcycle
      Motorcycle.new(options[:make], options[:model], options[:type])
    when :truck
      Truck.new(options[:make], options[:model], options[:capacity])
    else
      raise ArgumentError, "Unknown vehicle type: #{type}"
    end
  end
end

class Car
  attr_reader :make, :model, :color
  
  def initialize(make, model, color)
    @make = make
    @model = model
    @color = color
  end
  
  def drive
    puts "Driving #{@color} #{@make} #{@model}"
  end
end

class Motorcycle
  attr_reader :make, :model, :type
  
  def initialize(make, model, type)
    @make = make
    @model = model
    @type = type
  end
  
  def ride
    puts "Riding #{@make} #{@model} #{@type} motorcycle"
  end
end

class Truck
  attr_reader :make, :model, :capacity
  
  def initialize(make, model, capacity)
    @make = make
    @model = model
    @capacity = capacity
  end
  
  def haul
    puts "Hauling with #{@make} #{@model} (capacity: #{@capacity} tons)"
  end
end

# Usage
car = SimpleVehicleFactory.create_vehicle(:car, 
  make: "Toyota", model: "Camry", color: "Blue")
car.drive

motorcycle = SimpleVehicleFactory.create_vehicle(:motorcycle,
  make: "Harley-Davidson", model: "Sportster", type: "Cruiser")
motorcycle.ride

truck = SimpleVehicleFactory.create_vehicle(:truck,
  make: "Ford", model: "F-150", capacity: 2.5)
truck.haul
```

### 2. Factory Method

The Factory Method pattern defines an interface for creating an object but lets subclasses decide which class to instantiate.

```ruby
class AbstractDocumentCreator
  def create_document
    raise NotImplementedError, "Subclasses must implement create_document"
  end
  
  def publish_document
    document = create_document
    document.create_content
    document.format_content
    document.save_document
    document
  end
end

class ReportCreator < AbstractDocumentCreator
  def create_document
    Report.new
  end
end

class InvoiceCreator < AbstractDocumentCreator
  def create_document
    Invoice.new
  end
end

class Document
  def create_content
    raise NotImplementedError, "Subclasses must implement create_content"
  end
  
  def format_content
    puts "Formatting document content"
  end
  
  def save_document
    puts "Saving document to file system"
  end
end

class Report < Document
  def create_content
    puts "Creating report content with charts and tables"
  end
end

class Invoice < Document
  def create_content
    puts "Creating invoice with items and totals"
  end
end

# Usage
report_creator = ReportCreator.new
report = report_creator.publish_document

invoice_creator = InvoiceCreator.new
invoice = invoice_creator.publish_document
```

### 3. Abstract Factory

The Abstract Factory pattern provides an interface for creating families of related objects without specifying their concrete classes.

```ruby
class AbstractWidgetFactory
  def create_button
    raise NotImplementedError, "Subclasses must implement create_button"
  end
  
  def create_textbox
    raise NotImplementedError, "Subclasses must implement create_textbox"
  end
  
  def create_checkbox
    raise NotImplementedError, "Subclasses must implement create_checkbox"
  end
end

class WindowsWidgetFactory < AbstractWidgetFactory
  def create_button
    WindowsButton.new
  end
  
  def create_textbox
    WindowsTextbox.new
  end
  
  def create_checkbox
    WindowsCheckbox.new
  end
end

class MacOSWidgetFactory < AbstractWidgetFactory
  def create_button
    MacOSButton.new
  end
  
  def create_textbox
    MacOSTextbox.new
  end
  
  def create_checkbox
    MacOSCheckbox.new
  end
end

class Widget
  def render
    raise NotImplementedError, "Subclasses must implement render"
  end
end

class WindowsButton < Widget
  def render
    puts "Rendering Windows-style button"
  end
end

class WindowsTextbox < Widget
  def render
    puts "Rendering Windows-style textbox"
  end
end

class WindowsCheckbox < Widget
  def render
    puts "Rendering Windows-style checkbox"
  end
end

class MacOSButton < Widget
  def render
    puts "Rendering macOS-style button"
  end
end

class MacOSTextbox < Widget
  def render
    puts "Rendering macOS-style textbox"
  end
end

class MacOSCheckbox < Widget
  def render
    puts "Rendering macOS-style checkbox"
  end
end

# Usage
def create_ui(factory)
  button = factory.create_button
  textbox = factory.create_textbox
  checkbox = factory.create_checkbox
  
  button.render
  textbox.render
  checkbox.render
end

# Create Windows UI
windows_factory = WindowsWidgetFactory.new
create_ui(windows_factory)

# Create macOS UI
macos_factory = MacOSWidgetFactory.new
create_ui(macos_factory)
```

## 🏗️ Builder Pattern

The Builder pattern separates the construction of a complex object from its representation, allowing the same construction process to create different representations.

```ruby
class ComputerBuilder
  def initialize
    @computer = Computer.new
  end
  
  def set_cpu(cpu)
    @computer.cpu = cpu
    self
  end
  
  def set_ram(ram)
    @computer.ram = ram
    self
  end
  
  def set_storage(storage)
    @computer.storage = storage
    self
  end
  
  def set_graphics_card(graphics_card)
    @computer.graphics_card = graphics_card
    self
  end
  
  def set_power_supply(power_supply)
    @computer.power_supply = power_supply
    self
  end
  
  def build
    @computer.validate_configuration
    @computer
  end
end

class Computer
  attr_accessor :cpu, :ram, :storage, :graphics_card, :power_supply
  
  def initialize
    @cpu = nil
    @ram = nil
    @storage = nil
    @graphics_card = nil
    @power_supply = nil
  end
  
  def validate_configuration
    raise "CPU is required" unless @cpu
    raise "RAM is required" unless @ram
    raise "Storage is required" unless @storage
    raise "Power supply is required" unless @power_supply
  end
  
  def specs
    "Computer: #{@cpu}, #{@ram} RAM, #{@storage}, #{@graphics_card || 'Integrated'}, #{@power_supply}"
  end
end

class ComputerDirector
  def self.build_gaming_computer
    ComputerBuilder.new
      .set_cpu("Intel Core i9-12900K")
      .set_ram("32GB DDR4")
      .set_storage("1TB NVMe SSD")
      .set_graphics_card("NVIDIA RTX 3080")
      .set_power_supply("750W 80+ Gold")
      .build
  end
  
  def self.build_office_computer
    ComputerBuilder.new
      .set_cpu("Intel Core i5-12400")
      .set_ram("16GB DDR4")
      .set_storage("512GB SSD")
      .set_power_supply("500W 80+ Bronze")
      .build
  end
end

# Usage
gaming_pc = ComputerDirector.build_gaming_computer
puts gaming_pc.specs

office_pc = ComputerDirector.build_office_computer
puts office_pc.specs

# Custom build
custom_pc = ComputerBuilder.new
  .set_cpu("AMD Ryzen 7 5800X")
  .set_ram("64GB DDR4")
  .set_storage("2TB NVMe SSD")
  .set_power_supply("850W 80+ Platinum")
  .build
puts custom_pc.specs
```

## 🏢 Singleton Pattern

The Singleton pattern ensures a class has only one instance and provides a global point of access to it.

```ruby
class DatabaseConnection
  include Singleton
  
  def initialize
    @connection_string = "postgresql://localhost/myapp"
    @connected = false
  end
  
  def connect
    return if @connected
    
    puts "Connecting to database: #{@connection_string}"
    @connected = true
    puts "Database connected successfully"
  end
  
  def disconnect
    return unless @connected
    
    puts "Disconnecting from database"
    @connected = false
  end
  
  def execute_query(sql)
    connect unless @connected
    puts "Executing query: #{sql}"
    "Query result"
  end
  
  def connection_status
    @connected ? "Connected" : "Disconnected"
  end
end

# Usage
db1 = DatabaseConnection.instance
db2 = DatabaseConnection.instance

puts "Same instance: #{db1.object_id == db2.object_id}"

db1.execute_query("SELECT * FROM users")
db2.execute_query("SELECT * FROM products")

db1.disconnect
puts db2.connection_status

# Thread-safe singleton implementation
class ThreadSafeLogger
  include Singleton
  
  def initialize
    @log_file = "application.log"
    @mutex = Mutex.new
  end
  
  def log(message)
    @mutex.synchronize do
      timestamp = Time.now.strftime("%Y-%m-%d %H:%M:%S")
      log_entry = "[#{timestamp}] #{message}"
      
      File.open(@log_file, "a") do |file|
        file.puts(log_entry)
      end
      
      puts log_entry
    end
  end
end

# Usage in multi-threaded environment
threads = 5.times.map do |i|
  Thread.new do
    logger = ThreadSafeLogger.instance
    logger.log("Thread #{i} message")
  end
end

threads.each(&:join)
```

## 🔄 Prototype Pattern

The Prototype pattern creates new objects by copying an existing object, known as the prototype.

```ruby
class Shape
  attr_accessor :x, :y, :color
  
  def initialize(x, y, color)
    @x = x
    @y = y
    @color = color
  end
  
  def clone
    raise NotImplementedError, "Subclasses must implement clone"
  end
  
  def draw
    puts "Drawing #{@color} shape at (#{@x}, #{@y})"
  end
end

class Circle < Shape
  attr_accessor :radius
  
  def initialize(x, y, color, radius)
    super(x, y, color)
    @radius = radius
  end
  
  def clone
    Circle.new(@x, @y, @color, @radius)
  end
  
  def draw
    puts "Drawing #{@color} circle with radius #{@radius} at (#{@x}, #{@y})"
  end
end

class Rectangle < Shape
  attr_accessor :width, :height
  
  def initialize(x, y, color, width, height)
    super(x, y, color)
    @width = width
    @height = height
  end
  
  def clone
    Rectangle.new(@x, @y, @color, @width, @height)
  end
  
  def draw
    puts "Drawing #{@color} rectangle #{@width}x#{@height} at (#{@x}, #{@y})"
  end
end

class ShapeRegistry
  def initialize
    @shapes = {}
  end
  
  def add_shape(name, shape)
    @shapes[name] = shape
  end
  
  def get_shape(name)
    @shapes[name].clone
  end
  
  def list_shapes
    @shapes.keys
  end
end

# Usage
registry = ShapeRegistry.new

# Add prototype shapes
registry.add_shape("blue_circle", Circle.new(0, 0, "blue", 10))
registry.add_shape("red_rectangle", Rectangle.new(0, 0, "red", 20, 15))

# Create new shapes from prototypes
circle1 = registry.get_shape("blue_circle")
circle1.x = 50
circle1.y = 50
circle1.draw

circle2 = registry.get_shape("blue_circle")
circle2.x = 100
circle2.y = 100
circle2.draw

rectangle1 = registry.get_shape("red_rectangle")
rectangle1.x = 200
rectangle1.y = 150
rectangle1.draw

# Deep cloning example
class ComplexObject
  attr_accessor :name, :data, :nested_object
  
  def initialize(name)
    @name = name
    @data = []
    @nested_object = nil
  end
  
  def add_data(item)
    @data << item
  end
  
  def set_nested(object)
    @nested_object = object
  end
  
  def deep_clone
    cloned = self.class.new(@name + " (clone)")
    cloned.instance_variable_set(:@data, @data.dup)
    cloned.instance_variable_set(:@nested_object, @nested_object.clone) if @nested_object
    cloned
  end
end

# Usage
original = ComplexObject.new("Original")
original.add_data("item1")
original.add_data("item2")
original.set_nested(ComplexObject.new("Nested"))

clone = original.deep_clone
clone.add_data("item3")
clone.name = "Modified Clone"

puts "Original data: #{original.data}"
puts "Clone data: #{clone.data}"
```

## 🎯 Best Practices

### 1. Choose the Right Pattern

```ruby
# Factory Method vs Abstract Factory decision
class PatternSelector
  def self.recommend_pattern(scenario)
    case scenario
    when :single_product_family
      "Use Factory Method - simple product creation"
    when :multiple_product_families
      "Use Abstract Factory - related products"
    when :complex_object_construction
      "Use Builder - step-by-step construction"
    when :object_copying
      "Use Prototype - clone existing objects"
    when :single_instance
      "Use Singleton - one instance only"
    end
  end
end
```

### 2. Error Handling in Factories

```ruby
class RobustFactory
  def self.create_with_validation(product_type, **options)
    begin
      product = create_product(product_type, **options)
      validate_product(product)
      product
    rescue => e
      handle_creation_error(e, product_type, options)
    end
  end
  
  private
  
  def self.create_product(type, **options)
    # Factory logic here
  end
  
  def self.validate_product(product)
    # Validation logic here
  end
  
  def self.handle_creation_error(error, type, options)
    puts "Error creating #{type}: #{error.message}"
    nil
  end
end
```

### 3. Testing Creational Patterns

```ruby
# Testing factory patterns
RSpec.describe SimpleVehicleFactory do
  it "creates a car with correct attributes" do
    car = SimpleVehicleFactory.create_vehicle(:car, 
      make: "Toyota", model: "Camry", color: "Blue")
    
    expect(car).to be_a(Car)
    expect(car.make).to eq("Toyota")
    expect(car.model).to eq("Camry")
    expect(car.color).to eq("Blue")
  end
  
  it "raises error for unknown vehicle type" do
    expect {
      SimpleVehicleFactory.create_vehicle(:spaceship)
    }.to raise_error(ArgumentError, "Unknown vehicle type: spaceship")
  end
end

# Testing singleton pattern
RSpec.describe DatabaseConnection do
  it "returns the same instance" do
    instance1 = DatabaseConnection.instance
    instance2 = DatabaseConnection.instance
    
    expect(instance1).to be(instance2)
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Simple Factory**: Create a factory for different types of animals
2. **Builder Pattern**: Implement a house builder with different configurations
3. **Singleton**: Create a configuration manager singleton

### Intermediate Exercises

1. **Factory Method**: Implement a document factory with multiple document types
2. **Abstract Factory**: Create UI component factories for different platforms
3. **Prototype**: Implement a shape cloning system with deep copying

### Advanced Exercises

1. **Fluent Builder**: Create a fluent builder with method chaining
2. **Registry Pattern**: Implement a prototype registry with caching
3. **Multiton**: Create a pattern that manages multiple named instances

---

## 🎯 Summary

Creational design patterns provide:

- **Factory Patterns** - Centralized object creation
- **Builder Pattern** - Complex object construction
- **Singleton Pattern** - Single instance management
- **Prototype Pattern** - Object cloning and copying
- **Abstract Factory** - Product family creation

Master these patterns to write flexible, maintainable object creation code!
