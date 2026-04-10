# Ruby Metaprogramming

## Dynamic Method Definition

### Defining Methods at Runtime
```ruby
class DynamicMethods
  # Define method dynamically
  def create_method(method_name, operation)
    define_method(method_name) do |arg|
      arg.send(operation, arg)
    end
  end
  
  # Define multiple methods
  def create_math_methods
    operations = [:add, :subtract, :multiply, :divide]
    
    operations.each do |op|
      define_method("math_#{op}") do |a, b|
        a.send(op, b)
      end
    end
  end
  
  # Define method with variable arguments
  def create_variadic_method(method_name)
    define_method(method_name) do |*args|
      args.reduce(:+)
    end
  end
  
  # Define method with block
  def create_block_method(method_name, &block)
    define_method(method_name, &block)
  end
end

# Usage
dm = DynamicMethods.new
dm.create_method(:square, :**)  # Creates square method
dm.create_math_methods  # Creates math_add, math_subtract, etc.
dm.create_variadic_method(:sum)  # Creates sum method
dm.create_block_method(:greet) { puts "Hello!" }

puts dm.square(5)  # 25
puts dm.math_add(3, 4)  # 7
puts dm.sum(1, 2, 3, 4)  # 10
dm.greet  # "Hello!"
```

### Method Missing
```ruby
class MethodMissingExample
  def initialize
    @attributes = {}
  end
  
  # Handle missing methods
  def method_missing(method_name, *args, &block)
    method_name = method_name.to_s
    
    if method_name.start_with?('get_')
      attribute = method_name[4..-1]
      @attributes[attribute.to_sym]
    elsif method_name.start_with?('set_')
      attribute = method_name[4..-1]
      @attributes[attribute.to_sym] = args.first
    elsif method_name.start_with?('query_')
      attribute = method_name[6..-1]
      @attributes.key?(attribute.to_sym)
    else
      super
    end
  end
  
  # Respond to missing method
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.match?(/^(get_|set_|query_)/) || super
  end
end

# Usage
obj = MethodMissingExample.new
obj.set_name("John")
obj.set_age(30)
puts obj.get_name  # "John"
puts obj.get_age   # 30
puts obj.query_name?  # true
puts obj.query_email?  # false
```

### Class Methods and Eigenclasses
```ruby
class ClassMethods
  def self.create_factory_method(product_type)
    define_method("create_#{product_type}") do |name|
      case product_type
      when :car
        Car.new(name)
      when :bike
        Bike.new(name)
      when :boat
        Boat.new(name)
      else
        raise "Unknown product type: #{product_type}"
      end
    end
  end
  
  # Define class method on eigenclass
  def self.add_logging
    class << self
      def method_added(name)
        puts "Method #{name} added to #{self}"
        super
      end
    end
  end
end

# Usage
ClassMethods.create_factory_method(:car)
ClassMethods.create_factory_method(:bike)

car = ClassMethods.create_car("Toyota")
bike = ClassMethods.create_bike("Mountain Bike")

ClassMethods.add_logging
class Product
  def initialize(name)
    @name = name
  end
end
```

## Dynamic Classes and Modules

### Creating Classes Dynamically
```ruby
# Create class dynamically
def create_class(class_name, super_class = Object, &block)
  klass = Class.new(super_class, &block)
  Object.const_set(class_name, klass)
  klass
end

# Usage
create_class('DynamicPerson') do
  attr_accessor :name, :age
  
  def initialize(name, age)
    @name = name
    @age = age
  end
  
  def greet
    "Hello, I'm #{@name}"
  end
end

person = DynamicPerson.new("Alice", 25)
puts person.greet  # "Hello, I'm Alice"

# Create class with inheritance
create_class('Employee', 'DynamicPerson') do
  attr_accessor :salary, :department
  
  def initialize(name, age, salary, department)
    super(name, age)
    @salary = salary
    @department = department
  end
  
  def work
    "#{@name} is working in #{@department}"
  end
end

employee = Employee.new("Bob", 30, 50000, "Engineering")
puts employee.greet  # "Hello, I'm Bob"
puts employee.work   # "Bob is working in Engineering"
```

### Creating Modules Dynamically
```ruby
# Create module dynamically
def create_module(module_name, &block)
  mod = Module.new(&block)
  Object.const_set(module_name, mod)
  mod
end

# Usage
create_module('Validation') do
  def validate_presence(*fields)
    fields.each do |field|
      value = send(field)
      raise "#{field} cannot be nil" if value.nil?
    end
  end
  
  def validate_numeric(*fields)
    fields.each do |field|
      value = send(field)
      raise "#{field} must be numeric" unless value.is_a?(Numeric)
    end
  end
end

class Product
  include Validation
  
  attr_accessor :name, :price, :quantity
  
  def initialize(name, price, quantity)
    @name = name
    @price = price
    @quantity = quantity
    
    validate_presence(:name, :price, :quantity)
    validate_numeric(:price, :quantity)
  end
end

product = Product.new("Laptop", 999.99, 10)
puts product.name  # "Laptop"
```

### Eigenclasses
```ruby
class Person
  attr_accessor :name, :age
  
  def initialize(name, age)
    @name = name
    @age = age
  end
end

# Add methods to individual object's eigenclass
person = Person.new("John", 30)

def person.introduce
  "Hi, I'm #{@name} and I'm #{@age} years old"
end

def person.birthday
  @age += 1
  "Happy birthday! Now I'm #{@age}"
end

puts person.introduce  # "Hi, I'm John and I'm 30 years old"
puts person.birthday   # "Happy birthday! Now I'm 31"

# Add class methods to eigenclass
class << Person
  def create_adult(name)
    new(name, 18)
  end
  
  def create_child(name)
    new(name, 0)
  end
end

adult = Person.create_adult("Alice")
child = Person.create_child("Baby")

puts adult.name  # "Alice"
puts child.name  # "Baby"
```

## Reflection and Introspection

### Object Inspection
```ruby
class ReflectionExample
  attr_accessor :name, :age
  
  def initialize(name, age)
    @name = name
    @age = age
  end
  
  def greet
    "Hello, #{@name}"
  end
  
  private
  
  def secret_method
    "This is secret"
  end
end

obj = ReflectionExample.new("John", 30)

# Class information
puts obj.class  # ReflectionExample
puts obj.class.name  # "ReflectionExample"
puts obj.class.superclass  # Object

# Method information
puts obj.methods  # [:name, :name=, :age, :age=, :greet]
puts obj.public_methods  # [:name, :name=, :age, :age=, :greet]
puts obj.private_methods  # [:secret_method]
puts obj.protected_methods  # []

# Instance variables
puts obj.instance_variables  # [:@name, :@age]
puts obj.instance_variable_get(:@name)  # "John"

# Method objects
greet_method = obj.method(:greet)
puts greet_method.call  # "Hello, John"
puts greet_method.arity  # 0
puts greet_method.name  # :greet

# Class methods
puts ReflectionExample.methods(false)  # [:new, :allocate, :superclass, :<=>]
```

### Method and Constant Lookup
```ruby
class LookupExample
  def initialize
    @value = 42
  end
  
  def value
    @value
  end
  
  def self.constant
    "CLASS_CONSTANT"
  end
end

# Method lookup chain
obj = LookupExample.new

# Find where method is defined
puts obj.method(:value).owner  # LookupExample

# Constant lookup
puts LookupExample::constant  # "CLASS_CONSTANT"

# Module method lookup
module TestModule
  def module_method
    "From module"
  end
end

class TestClass
  include TestModule
  
  def class_method
    "From class"
  end
end

obj = TestClass.new
puts obj.module_method  # "From module"
puts obj.class_method    # "From class"
```

### Respond To Methods
```ruby
class RespondToExample
  def initialize
    @data = {}
  end
  
  def method_missing(method_name, *args)
    if method_name.to_s.start_with?('dynamic_')
      attribute = method_name.to_s[8..-1]
      @data[attribute.to_sym] = args.first
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.start_with?('dynamic_') || super
  end
  
  def respond_to?(method_name, include_private = false)
    respond_to_missing?(method_name, include_private) || super
  end
end

obj = RespondToExample.new
puts obj.respond_to?(:dynamic_name)  # true
puts obj.respond_to?(:missing_method)  # false
```

## Code Generation and Evaluation

### Eval and Instance Eval
```ruby
class CodeGeneration
  def initialize
    @variables = {}
  end
  
  # Generate and execute code
  def generate_and_execute
    code = "
      @variables[:x] = 10
      @variables[:y] = 20
      result = @variables[:x] + @variables[:y]
      puts 'Result: ' + result.to_s
    "
    
    instance_eval(code)
  end
  
  # Generate method dynamically
  def create_calculator(operation)
    code = "
      def calculate_#{operation}(a, b)
        a #{operation} b
      end
    "
    
    instance_eval(code)
  end
  
  # Generate class dynamically
  def create_class_with_methods(class_name, methods)
    class_code = "
      class #{class_name}
        #{methods.map { |name, body| \"def #{name}#{body}\" }.join(\"\\n\")}
      end
    "
    
    eval(class_code)
  end
end

# Usage
cg = CodeGeneration.new
cg.generate_and_execute  # "Result: 30"

cg.create_calculator(:multiply)
puts cg.calculate_multiply(5, 3)  # 15

methods = {
  greet: "() { puts 'Hello!' }",
  farewell: "() { puts 'Goodbye!' }"
}
cg.create_class_with_methods("Greeter", methods)

greeter = Greeter.new
greeter.greet  # "Hello!"
greeter.farewell  # "Goodbye!"
```

### Binding and Unbinding
```ruby
class BindingExample
  def show_context(binding)
    puts "Local variables: #{binding.local_variables}"
    puts "Self: #{binding.receiver}"
  end
  
  def create_binding
    local_var = "I'm local"
    binding
  end
  
  def execute_in_binding(binding, code)
    binding.eval(code)
  end
end

# Usage
be = BindingExample.new
binding = be.create_binding

be.execute_in_binding(binding, "puts local_var")  # "I'm local"

# Create binding with specific context
context = binding
context.eval("self.class")  # BindingExample
```

### Define Method with Binding
```ruby
class DefineMethodExample
  def create_context_aware_method(method_name)
    define_method(method_name) do |arg|
      "#{self.class.name}: #{arg} (from #{caller_locations[0].label})"
    end
  end
  
  def create_method_with_binding(method_name)
    define_method(method_name) do |arg|
      binding.eval("local_var = 'I am local'")
      "#{arg} - #{binding.local_variable_get(:local_var)}"
    end
  end
end

# Usage
dme = DefineMethodExample.new
dme.create_context_aware_method(:context_method)
dme.create_method_with_binding(:binding_method)

puts dme.context_method("test")  # "DefineMethodExample: test (from create_context_aware_method)"
puts dme.binding_method("test")    # "test - I am local"
```

## Macros

### Simple Macros
```ruby
class MacroExample
  # Simple macro for property definition
  def self.property(name, type = String)
    define_method(name) do
      instance_variable_get("@#{name}")
    end
    
    define_method("#{name}=") do |value|
      case type
      when String
        instance_variable_set("@#{name}", value.to_s)
      when Integer
        instance_variable_set("@#{name}", value.to_i)
      when Float
        instance_variable_set("@#{name}", value.to_f)
      else
        instance_variable_set("@#{name}", value)
      end
    end
  end
  
  # Macro for validation
  def self.validate_presence(*fields)
    fields.each do |field|
      define_method("validate_#{field}") do
        value = instance_variable_get("@#{field}")
        raise "#{field} cannot be nil" if value.nil?
        raise "#{field} cannot be empty" if value.respond_to?(:empty?) && value.empty?
        true
      end
    end
  end
  
  # Macro for class methods
  def self.class_method(name, &block)
    define_singleton_method(name, &block)
  end
end

# Usage
class User
  extend MacroExample
  
  property :name, String
  property :age, Integer
  property :email, String
  
  validate_presence :name, :age, :email
  
  class_method :create do |name, age, email|
    new(name, age, email)
  end
end

user = User.new
user.name = "John"
user.age = 30
user.email = "john@example.com"

user.validate_name  # No error
user.validate_age   # No error
user.validate_email  # No error

user = User.create("Jane", 25, "jane@example.com")
puts user.name  # "Jane"
```

### Advanced Macros
```ruby
class AdvancedMacro
  # Macro for attr_accessor with validation
  def self.validated_attr_accessor(name, validator = nil)
    define_method(name) do
      instance_variable_get("@#{name}")
    end
    
    define_method("#{name}=") do |value|
      if validator && !validator.call(value)
        raise "Invalid value for #{name}"
      end
      instance_variable_set("@#{name}", value)
    end
    
    define_method("#{name}?") do
      !instance_variable_get("@#{name}").nil?
    end
  end
  
  # Macro for delegation
  def self.delegate_to(target, *methods)
    methods.each do |method|
      define_method(method) do |*args, &block|
        instance_variable_get("@#{target}").send(method, *args, &block)
      end
    end
  end
  
  # Macro for class attributes
  def self.class_attr_accessor(*names)
    names.each do |name|
      define_singleton_method(name) { instance_variable_get("@#{name}") }
      define_singleton_method("#{name}=") { |value| instance_variable_set("@#{name}", value) }
      define_singleton_method("#{name}?") { !instance_variable_get("@#{name}").nil? }
      
      instance_variable_set("@#{name}", nil)
    end
  end
end

# Usage
class Product
  extend AdvancedMacro
  
  validated_attr_accessor :price, ->(value) { value.is_a?(Numeric) && value > 0 }
  validated_attr_accessor :quantity, ->(value) { value.is_a?(Integer) && value > 0 }
  
  delegate_to :details, :description, :category
  
  class_attr_accessor :default_currency
  
  def initialize
    @details = {}
    @default_currency = "USD"
  end
end

product = Product.new
product.price = 99.99
product.quantity = 10
product.details[:description] = "Great product"

puts product.price  # 99.99
puts product.quantity  # 10
puts product.details[:description]  # "Great product"
puts Product.default_currency  # "USD"

product.price = -5  # Raises "Invalid value for price"
```

## Best Practices

### When to Use Metaprogramming
```ruby
# Good: Use for DSL creation
class FormBuilder
  def self.form(&block)
    builder = new
    builder.instance_eval(&block)
    builder.build
  end
  
  def text_field(name)
    @fields ||= {}
    @fields[name] = { type: :text }
  end
  
  def number_field(name)
    @fields ||= {}
    @fields[name] = { type: :number }
  end
  
  def build
    @fields || {}
  end
end

# Usage
form = FormBuilder.form do
  text_field :name
  text_field :email
  number_field :age
end

# Good: Use for reducing duplication
class ValidationHelper
  def self.create_validator(field_name, options = {})
    define_method("validate_#{field_name}") do
      value = instance_variable_get("@#{field_name}")
      
      if options[:required] && (value.nil? || value.empty?)
        raise "#{field_name} is required"
      end
      
      if options[:min] && value && value < options[:min]
        raise "#{field_name} must be at least #{options[:min]}"
      end
      
      if options[:max] && value && value > options[:max]
        raise "#{field_name} must be at most #{options[:max]}"
      end
      
      if options[:format] && value && !value.match?(options[:format])
        raise "#{field_name} format is invalid"
      end
      
      true
    end
  end
end

class User
  extend ValidationHelper
  
  attr_accessor :name, :age, :email
  
  def initialize(name, age, email)
    @name = name
    @age = age
    @email = email
    
    validate_name
    validate_age
    validate_email
  end
  
  private
  
  create_validator :name, required: true, min: 2, max: 50
  create_validator :age, required: true, min: 0, max: 150
  create_validator :email, required: true, format: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
end
```

### Avoiding Common Pitfalls
```ruby
# Bad: Using eval with user input
class BadEval
  def process_user_input(input)
    eval(input)  # Dangerous!
  end
end

# Good: Use safer alternatives
class GoodEval
  def process_user_input(input)
    # Parse and validate input first
    case input
    when /^\d+$/
      input.to_i
    when /^\w+$/
      input.to_sym
    else
      raise "Invalid input format"
    end
  end
end

# Bad: Overusing metaprogramming
class OverMeta
  def initialize
    create_dynamic_methods
    create_dynamic_classes
    modify_existing_classes
  end
  
  private
  
  def create_dynamic_methods
    # Too much dynamic code
  end
  
  def create_dynamic_classes
    # Too much dynamic code
  end
  
  def modify_existing_classes
    # Modifying existing classes can be dangerous
  end
end

# Good: Use metaprogramming sparingly and purposefully
class GoodMeta
  def initialize
    create_validation_helpers
  end
  
  private
  
  def create_validation_helpers
    # Focused, purposeful dynamic code
  end
end
```

### Documentation and Testing
```ruby
# Good: Document metaprogramming code
class DocumentedMeta
  # Creates a property with validation
  # 
  # @param name [Symbol] the name of the property
  # @param type [Class] the expected type of the property
  # @param validator [Proc] optional validation proc
  def self.validated_property(name, type: String, validator: nil)
    define_method(name) do
      instance_variable_get("@#{name}")
    end
    
    define_method("#{name}=") do |value|
      unless value.is_a?(type)
        raise "#{name} must be a #{type}"
      end
      
      if validator && !validator.call(value)
        raise "Invalid value for #{name}"
      end
      
      instance_variable_set("@#{name}", value)
    end
  end
end

# Good: Test metaprogramming code
class TestedMeta
  def self.validated_property(name, type: String, validator: nil)
    # Implementation...
  end
end

# Test
RSpec.describe TestedMeta do
  describe '.validated_property' do
    it 'creates getter method' do
      klass = Class.new do
        TestedMeta.validated_property(:name, type: String)
      end
      
      obj = klass.new
      obj.name = "test"
      expect(obj.name).to eq("test")
    end
    
    it 'validates type' do
      klass = Class.new do
        TestedMeta.validated_property(:name, type: String)
      end
      
      obj = klass.new
      expect { obj.name = 123 }.to raise_error
    end
  end
end
```

## Common Pitfalls

### Security Issues
```ruby
# Pitfall: Using eval with untrusted input
class SecurityRisk
  def process_data(data)
    eval(data)  # Code injection risk!
  end
end

# Solution: Use safer alternatives
class SafeProcessing
  def process_data(data)
    # Parse and validate data
    case data
    when Hash
      process_hash(data)
    when Array
      process_array(data)
    when String
      process_string(data)
    else
      raise "Unsupported data type"
    end
  end
  
  private
  
  def process_hash(hash)
    # Safe hash processing
  end
  
  def process_array(array)
    # Safe array processing
  end
  
  def process_string(string)
    # Safe string processing
  end
end
```

### Performance Issues
```ruby
# Pitfall: Excessive method creation
class PerformanceRisk
  def initialize
    (1..1000).each do |i|
      define_method("method_#{i}") { i }
    end
  end
end

# Solution: Cache methods or use alternative approach
class PerformanceSolution
  def initialize
    @methods = {}
  end
  
  def method_missing(method_name, *args)
    if method_name.to_s.match(/^method_(\d+)$/)
      number = $1.to_i
      number
    else
      super
    end
  end
end
```

### Maintenance Issues
```ruby
# Pitfall: Obfuscated code
class ObfuscatedCode
  def self.create_complex_class(name)
    eval("class #{name}
      def initialize
        @x = 1
        @y = 2
      end
      
      def compute
        @x + @y
      end
    end")
  end
end

# Solution: Use clear, readable code
class ClearCode
  def self.create_simple_class(name)
    Class.new do
      define_method(:initialize) do
        @x = 1
        @y = 2
      end
      
      define_method(:compute) do
        @x + @y
      end
    end
  end
end
```

## Summary

Ruby metaprogramming provides:

**Dynamic Method Definition:**
- define_method for runtime method creation
- method_missing for handling unknown methods
- respond_to_missing for capability checking
- Class methods and eigenclasses

**Dynamic Classes and Modules:**
- Runtime class creation with Class.new
- Dynamic module creation with Module.new
- Eigenclasses for object-specific behavior
- Mixin functionality

**Reflection and Introspection:**
- Object inspection methods
- Method and constant lookup
- respond_to? method checking
- Method objects and binding

**Code Generation:**
- eval and instance_eval for code execution
- Binding and unbinding for context management
- define_method with binding
- Safe code generation practices

**Macros:**
- Custom method generation macros
- Validation and delegation macros
- Class attribute macros
- DSL creation patterns

**Best Practices:**
- Use metaprogramming for DSL creation
- Reduce code duplication purposefully
- Document metaprogramming code
- Test dynamic code thoroughly

**Common Pitfalls:**
- Security risks with eval
- Performance issues with excessive method creation
- Maintenance problems with obfuscated code
- Overuse of metaprogramming

Ruby's metaprogramming capabilities make it one of the most dynamic and flexible languages, enabling powerful DSLs, frameworks, and code generation when used appropriately and safely.
