# Advanced Ruby Features

## Advanced Method Techniques

### Method Aliasing and Chaining
```ruby
class MethodAliasing
  def original_method
    "Original implementation"
  end
  
  # Alias the method
  alias_method :aliased_method, :original_method
  
  # Override the original method
  def original_method
    "New implementation, but can call: #{aliased_method}"
  end
  
  # Method chaining with alias
  def chained_method
    "First part"
  end
  
  alias_method :old_chained_method, :chained_method
  
  def chained_method
    old_chained_method + " + Second part"
  end
end

obj = MethodAliasing.new
puts obj.original_method  # "New implementation, but can call: Original implementation"
puts obj.chained_method   # "First part + Second part"
```

### Method Removal and Undefinition
```ruby
class MethodManipulation
  def method1
    "Method 1"
  end
  
  def method2
    "Method 2"
  end
  
  def remove_methods
    # Undefine instance methods
    undef_method :method1
    
    # Remove methods from singleton class
    class << self
      undef_method :method2
    end
  end
  
  # Define method that can be removed
  def temporary_method
    "This will be removed"
  end
end

obj = MethodManipulation.new
puts obj.temporary_method  # "This will be removed"

# Remove the method
class MethodManipulation
  undef_method :temporary_method
end

# obj.temporary_method  # NoMethodError
```

### Method Visibility Manipulation
```ruby
class VisibilityManipulation
  def public_method
    "Public method"
  end
  
  private
  
  def private_method
    "Private method"
  end
  
  protected
  
  def protected_method
    "Protected method"
  end
  
  # Change visibility at runtime
  def make_private_public
    self.class.send(:public, :private_method)
  end
  
  def make_protected_private
    self.class.send(:private, :protected_method)
  end
end

obj = VisibilityManipulation.new
obj.make_private_public
puts obj.private_method  # "Private method" (now public)
```

## Advanced Blocks and Procs

### Proc and Lambda Comparison
```ruby
class ProcVsLambda
  def demonstrate_proc
    # Proc returns from current method
    proc = Proc.new { return "From proc" }
    result = proc.call
    "This won't be reached: #{result}"
  end
  
  def demonstrate_lambda
    # Lambda returns from lambda itself
    lambda = -> { return "From lambda" }
    result = lambda.call
    "This will be reached: #{result}"
  end
  
  def argument_handling
    # Proc is lenient with arguments
    proc = Proc.new { |x, y| "x: #{x}, y: #{y}" }
    puts proc.call(1)  # "x: 1, y: "
    
    # Lambda is strict with arguments
    lambda = ->(x, y) { "x: #{x}, y: #{y}" }
    # lambda.call(1)  # ArgumentError: wrong number of arguments
    
    lambda.call(1, 2)  # "x: 1, y: 2"
  end
  
  def conversion_examples
    # Convert method to proc
    def add(a, b)
      a + b
    end
    
    add_proc = method(:add).to_proc
    puts add_proc.call(5, 3)  # 8
    
    # Convert proc to lambda
    proc = Proc.new { |x| x * 2 }
    lambda = proc.to_lambda
    puts lambda.call(5)  # 10
  end
end

proc_demo = ProcVsLambda.new
puts proc_demo.demonstrate_proc  # "From proc"
puts proc_demo.demonstrate_lambda  # "This will be reached: From lambda"
```

### Closures and Binding
```ruby
class ClosureExample
  def create_counter
    count = 0
    
    # Closure captures the count variable
    lambda do
      count += 1
      count
    end
  end
  
  def create_multiplier(factor)
    # Closure captures the factor parameter
    lambda do |number|
      number * factor
    end
  end
  
  def demonstrate_binding
    # Create binding with local variables
    local_var = "local"
    instance_var = "instance"
    
    binding = binding
    
    # Use binding in eval
    eval("puts local_var", binding)  # "local"
    eval("puts instance_var", binding)  # "instance"
    
    # Create proc with binding
    proc = binding.eval { Proc.new { puts "#{local_var} and #{instance_var}" } }
    proc.call  # "local and instance"
  end
end

closure_demo = ClosureExample.new

counter = closure_demo.create_counter
puts counter.call  # 1
puts counter.call  # 2
puts counter.call  # 3

doubler = closure_demo.create_multiplier(2)
tripler = closure_demo.create_multiplier(3)
puts doubler.call(5)  # 10
puts tripler.call(5)  # 15
```

### Higher-Order Functions
```ruby
class HigherOrderFunctions
  def compose(f, g)
    lambda { |x| f.call(g.call(x)) }
  end
  
  def curry_method(method)
    lambda { |*args| method.call(*args) }
  end
  
  def partial_apply(method, *fixed_args)
    lambda { |*args| method.call(*fixed_args, *args) }
  end
  
  def memoize(method)
    cache = {}
    
    lambda do |*args|
      cache[args] ||= method.call(*args)
    end
  end
  
  def demonstrate_composition
    add_one = ->(x) { x + 1 }
    double = ->(x) { x * 2 }
    
    add_then_double = compose(double, add_one)
    double_then_add = compose(add_one, double)
    
    puts add_then_double.call(5)  # 12 (5 + 1) * 2
    puts double_then_add.call(5)  # 11 (5 * 2) + 1
  end
  
  def demonstrate_memoization
    expensive_operation = lambda do |n|
      puts "Calculating #{n}..."
      n * n
    end
    
    memoized = memoize(expensive_operation)
    
    puts memoized.call(5)  # "Calculating 5..." then 25
    puts memoized.call(5)  # 25 (cached)
    puts memoized.call(10) # "Calculating 10..." then 100
  end
end

hof_demo = HigherOrderFunctions.new
hof_demo.demonstrate_composition
hof_demo.demonstrate_memoization
```

## Advanced Metaprogramming

### Class and Module Evaluation
```ruby
class DynamicClassCreation
  def self.create_class(name, super_class = Object, &block)
    klass = Class.new(super_class, &block)
    Object.const_set(name, klass)
    klass
  end
  
  def self.create_module(name, &block)
    mod = Module.new(&block)
    Object.const_set(name, mod)
    mod
  end
  
  def self.create_class_from_string(class_definition)
    eval(class_definition)
  end
end

# Create class dynamically
DynamicClassCreation.create_class('DynamicPerson') do
  attr_accessor :name, :age
  
  def initialize(name, age)
    @name = name
    @age = age
  end
  
  def greet
    "Hello, I'm #{@name} and I'm #{@age} years old"
  end
end

person = DynamicPerson.new("Alice", 25)
puts person.greet  # "Hello, I'm Alice and I'm 25 years old"

# Create module dynamically
DynamicClassCreation.create_module('Validation') do
  def validate_presence(*fields)
    fields.each do |field|
      value = send(field)
      raise "#{field} cannot be nil" if value.nil?
    end
  end
end

class Product
  include Validation
  
  attr_accessor :name, :price
  
  def initialize(name, price)
    @name = name
    @price = price
    validate_presence(:name, :price)
  end
end

# Create class from string
class_definition = <<~RUBY
  class StringClass
    def initialize(str)
      @string = str
    end
    
    def reverse
      @string.reverse
    end
  end
RUBY

DynamicClassCreation.create_class_from_string(class_definition)
str_obj = StringClass.new("hello")
puts str_obj.reverse  # "olleh"
```

### Advanced Method Missing
```ruby
class AdvancedMethodMissing
  def initialize
    @data = {}
    @methods = {}
  end
  
  def method_missing(method_name, *args, &block)
    method_str = method_name.to_s
    
    if method_str.start_with?('get_')
      attribute = method_str[4..-1]
      @data[attribute.to_sym]
    elsif method_str.start_with?('set_')
      attribute = method_str[4..-1]
      @data[attribute.to_sym] = args.first
    elsif method_str.start_with?('query_')
      attribute = method_str[6..-1]
      @data.key?(attribute.to_sym)
    elsif method_str.start_with?('define_')
      method_name = method_str[7..-1].to_sym
      @methods[method_name] = args.first
      define_singleton_method(method_name, &args.first)
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_str = method_name.to_s
    method_str.match?(/^(get_|set_|query_|define_)/) || super
  end
  
  def dynamic_method(name, &block)
    define_singleton_method(name, &block)
    @methods[name] = block
  end
  
  def list_dynamic_methods
    @methods.keys
  end
end

obj = AdvancedMethodMissing.new
obj.set_name("John")
obj.set_age(30)
puts obj.get_name  # "John"
puts obj.get_age   # 30
puts obj.query_name?  # true
puts obj.query_email?  # false

obj.define_greet { puts "Hello!" }
obj.greet  # "Hello!"
puts obj.list_dynamic_methods  # [:greet]
```

### Advanced Reflection
```ruby
class AdvancedReflection
  def analyze_object(obj)
    puts "Class: #{obj.class}"
    puts "Class name: #{obj.class.name}"
    puts "Superclass: #{obj.class.superclass}"
    puts "Modules: #{obj.class.included_modules}"
    puts "Instance methods: #{obj.methods(false).sort}"
    puts "Public methods: #{obj.public_methods(false).sort}"
    puts "Private methods: #{obj.private_methods(false).sort}"
    puts "Protected methods: #{obj.protected_methods(false).sort}"
    puts "Instance variables: #{obj.instance_variables}"
    puts "Singleton methods: #{obj.singleton_methods(false).sort}"
  end
  
  def get_method_source(obj, method_name)
    method = obj.method(method_name)
    puts "Method: #{method.name}"
    puts "Owner: #{method.owner}"
    puts "Arity: #{method.arity}"
    puts "Parameters: #{method.parameters}"
    
    # Try to get source location
    if method.source_location
      file, line = method.source_location
      puts "Source: #{file}:#{line}"
    end
  end
  
  def trace_method_calls(obj, method_name)
    original_method = obj.method(method_name)
    
    obj.define_singleton_method(method_name) do |*args, &block|
      puts "Calling #{method_name} with args: #{args.inspect}"
      result = original_method.call(*args, &block)
      puts "#{method_name} returned: #{result.inspect}"
      result
    end
  end
  
  def monkey_patch_class(klass, method_name, &block)
    original_method = klass.instance_method(method_name)
    
    klass.define_method(method_name) do |*args, &block|
      # Call original method
      result = original_method.bind(self).call(*args, &block)
      
      # Apply patch
      patch_result = block.call(result, *args)
      
      patch_result || result
    end
  end
end

reflection_demo = AdvancedReflection.new
reflection_demo.analyze_object("hello")
reflection_demo.get_method_source("hello", :upcase)

# Trace method calls
str = "hello world"
reflection_demo.trace_method_calls(str, :upcase)
str.upcase  # Shows tracing output
```

## Advanced Exception Handling

### Custom Exception Classes
```ruby
# Custom exception hierarchy
class CustomError < StandardError
  attr_reader :context, :timestamp
  
  def initialize(message, context = nil)
    super(message)
    @context = context
    @timestamp = Time.now
  end
  
  def to_s
    "#{super} (Context: #{context}, Time: #{timestamp})"
  end
end

class ValidationError < CustomError
  attr_reader :field, :value
  
  def initialize(message, field: nil, value: nil, context: nil)
    super(message, context)
    @field = field
    @value = value
  end
end

class BusinessError < CustomError
  attr_reader :business_rule
  
  def initialize(message, business_rule: nil, context: nil)
    super(message, context)
    @business_rule = business_rule
  end
end

# Exception handling with custom exceptions
class UserValidator
  def validate(user)
    raise ValidationError.new("Name cannot be empty", field: :name, value: user.name) if user.name.nil? || user.name.empty?
    raise ValidationError.new("Email cannot be empty", field: :email, value: user.email) if user.email.nil? || user.email.empty?
    raise ValidationError.new("Age must be positive", field: :age, value: user.age) if user.age && user.age < 0
    
    true
  end
end

class BusinessLogic
  def process_payment(user, amount)
    raise BusinessError.new("User must be active", business_rule: :user_active, context: { user_id: user.id }) unless user.active?
    raise BusinessError.new("Amount must be positive", business_rule: :positive_amount, context: { amount: amount }) if amount <= 0
    
    # Process payment logic
    true
  end
end

# Usage
begin
  validator = UserValidator.new
  user = OpenStruct.new(name: "", email: "test@example.com", age: 25)
  validator.validate(user)
rescue ValidationError => e
  puts "Validation failed: #{e}"
  puts "Field: #{e.field}, Value: #{e.value}"
end

begin
  business = BusinessLogic.new
  user = OpenStruct.new(id: 1, active: false)
  business.process_payment(user, 100)
rescue BusinessError => e
  puts "Business error: #{e}"
  puts "Rule: #{e.business_rule}, Context: #{e.context}"
end
```

### Exception Handling Patterns
```ruby
class ExceptionPatterns
  # Retry pattern
  def self.retry(max_attempts = 3, delay: 1)
    attempts = 0
    
    begin
      yield
    rescue => e
      attempts += 1
      if attempts < max_attempts
        sleep(delay * attempts)
        retry
      else
        raise e
      end
    end
  end
  
  # Timeout pattern
  def self.timeout(seconds)
    thread = Thread.new { yield }
    
    unless thread.join(seconds)
      thread.kill
      raise TimeoutError, "Operation timed out after #{seconds} seconds"
    end
    
    thread.value
  end
  
  # Circuit breaker pattern
  class CircuitBreaker
    def initialize(failure_threshold: 5, timeout: 60)
      @failure_threshold = failure_threshold
      @timeout = timeout
      @failures = 0
      @last_failure_time = nil
      @state = :closed
    end
    
    def call(&block)
      case @state
      when :open
        if Time.now - @last_failure_time > @timeout
          @state = :half_open
        else
          raise CircuitBreakerError, "Circuit breaker is open"
        end
      end
      
      begin
        result = yield
        reset if @state == :half_open
        result
      rescue => e
        record_failure
        raise e
      end
    end
    
    private
    
    def record_failure
      @failures += 1
      @last_failure_time = Time.now
      
      if @failures >= @failure_threshold
        @state = :open
      end
    end
    
    def reset
      @failures = 0
      @state = :closed
    end
  end
  
  class CircuitBreakerError < StandardError; end
end

# Usage examples
begin
  ExceptionPatterns.retry(3, delay: 1) do
    # Simulate failing operation
    raise "Operation failed" if rand < 0.7
    "Success!"
  end
rescue => e
  puts "Operation failed after retries: #{e}"
end

begin
  ExceptionPatterns.timeout(2) do
    sleep 3
    "This won't be reached"
  end
rescue TimeoutError => e
  puts e.message
end

# Circuit breaker usage
breaker = ExceptionPatterns::CircuitBreaker.new

begin
  breaker.call do
    # Simulate operation that might fail
    raise "Service unavailable" if rand < 0.3
    "Service response"
  end
rescue ExceptionPatterns::CircuitBreakerError => e
  puts e.message
rescue => e
  puts "Service error: #{e}"
end
```

## Advanced Concurrency

### Thread Synchronization
```ruby
require 'thread'

class ThreadSynchronization
  def initialize
    @mutex = Mutex.new
    @condition_variable = ConditionVariable.new
    @queue = []
    @shutdown = false
  end
  
  # Producer-consumer pattern
  def producer_consumer_example
    producer = Thread.new do
      5.times do |i|
        @mutex.synchronize do
          @queue << "Item #{i}"
          puts "Produced: Item #{i}"
          @condition_variable.signal
        end
        sleep 0.1
      end
    end
    
    consumer = Thread.new do
      loop do
        @mutex.synchronize do
          @condition_variable.wait(@mutex) while @queue.empty? && !@shutdown
          break if @shutdown && @queue.empty?
          
          item = @queue.shift
          puts "Consumed: #{item}"
        end
      end
    end
    
    producer.join
    @mutex.synchronize { @shutdown = true; @condition_variable.broadcast }
    consumer.join
  end
  
  # Reader-writer lock pattern
  def reader_writer_lock
    @readers = 0
    @writers_waiting = 0
    @writer_active = false
    @rw_mutex = Mutex.new
    @can_read = ConditionVariable.new
    @can_write = ConditionVariable.new
    
    # Readers
    readers = Array.new(3) do |i|
      Thread.new do
        3.times do
          @rw_mutex.synchronize do
            @can_read.wait(@rw_mutex) while @writer_active || @writers_waiting > 0
            @readers += 1
          end
          
          puts "Reader #{i} reading"
          sleep 0.1
          
          @rw_mutex.synchronize do
            @readers -= 1
            @can_write.signal if @readers == 0
          end
        end
      end
    end
    
    # Writers
    writers = Array.new(2) do |i|
      Thread.new do
        2.times do
          @rw_mutex.synchronize do
            @writers_waiting += 1
            @can_write.wait(@rw_mutex) while @writer_active || @readers > 0
            @writers_waiting -= 1
            @writer_active = true
          end
          
          puts "Writer #{i} writing"
          sleep 0.2
          
          @rw_mutex.synchronize do
            @writer_active = false
            @can_read.broadcast if @writers_waiting == 0
            @can_write.signal if @writers_waiting == 0
          end
        end
      end
    end
    
    (readers + writers).each(&:join)
  end
  
  # Thread pool pattern
  class ThreadPool
    def initialize(size)
      @size = size
      @queue = Queue.new
      @workers = Array.new(size) { |i| create_worker(i) }
      @shutdown = false
    end
    
    def execute(&block)
      @queue << block
    end
    
    def shutdown
      @shutdown = true
      @size.times { @queue << :shutdown }
      @workers.each(&:join)
    end
    
    private
    
    def create_worker(id)
      Thread.new do
        loop do
          task = @queue.pop
          break if task == :shutdown && @shutdown
          
          begin
            task.call
          rescue => e
            puts "Worker #{id} error: #{e}"
          end
        end
      end
    end
  end
  
  def thread_pool_example
    pool = ThreadPool.new(4)
    
    10.times do |i|
      pool.execute do
        puts "Task #{i} processed by #{Thread.current.object_id}"
        sleep 0.1
      end
    end
    
    sleep 2
    pool.shutdown
  end
end

sync_demo = ThreadSynchronization.new
sync_demo.producer_consumer_example
sync_demo.reader_writer_lock
sync_demo.thread_pool_example
```

### Actor Model Implementation
```ruby
class Actor
  def initialize(name)
    @name = name
    @mailbox = Queue.new
    @running = true
    @thread = Thread.new { run }
  end
  
  def send_message(message)
    @mailbox << message
  end
  
  def stop
    @running = false
    @mailbox << :stop
    @thread.join
  end
  
  private
  
  def run
    while @running
      message = @mailbox.pop
      break if message == :stop
      
      begin
        handle_message(message)
      rescue => e
        puts "Actor #{@name} error: #{e}"
      end
    end
  end
  
  def handle_message(message)
    # Override in subclasses
    puts "Actor #{@name} received: #{message}"
  end
end

class CalculatorActor < Actor
  def initialize
    super("Calculator")
    @result = 0
  end
  
  private
  
  def handle_message(message)
    case message
    when Hash
      case message[:operation]
      when :add
        @result += message[:value]
        send_message({ operation: :result, value: @result })
      when :multiply
        @result *= message[:value]
        send_message({ operation: :result, value: @result })
      end
    when :get_result
      puts "Current result: #{@result}"
    end
  end
end

# Actor usage
calculator = CalculatorActor.new
calculator.send_message({ operation: :add, value: 10 })
calculator.send_message({ operation: :multiply, value: 2 })
calculator.send_message(:get_result)

sleep 1
calculator.stop
```

## Advanced Performance Optimization

### Memory Management
```ruby
class MemoryOptimization
  # Object pooling
  class ObjectPool
    def initialize(create_proc, reset_proc = nil, max_size: 10)
      @create_proc = create_proc
      @reset_proc = reset_proc || proc { |obj| obj }
      @pool = Queue.new
      @max_size = max_size
      @created = 0
    end
    
    def with_object
      obj = checkout
      begin
        yield obj
      ensure
        checkin(obj)
      end
    end
    
    private
    
    def checkout
      if @pool.empty? && @created < @max_size
        @created += 1
        @create_proc.call
      else
        @pool.pop
      end
    end
    
    def checkin(obj)
      @reset_proc.call(obj)
      @pool.push(obj)
    end
  end
  
  # Weak references
  def weak_reference_example
    weak_refs = []
    
    10.times do |i|
      obj = "Object #{i}"
      weak_refs << WeakRef.new(obj)
    end
    
    # Force garbage collection
    GC.start
    
    weak_refs.each_with_index do |ref, i|
      if ref.weakref_alive?
        puts "Object #{i} is still alive"
      else
        puts "Object #{i} was garbage collected"
      end
    end
  end
  
  # Memory profiling
  def memory_profile
    require 'objspace'
    
    objects_before = ObjectSpace.count_objects
    
    # Create some objects
    1000.times { |i| "String #{i}" }
    
    objects_after = ObjectSpace.count_objects
    
    puts "Objects created: #{objects_after[:T_STRING] - objects_before[:T_STRING]}"
    
    # Get memory usage
    memory_usage = `ps -o rss= -p #{Process.pid}`.to_i
    puts "Memory usage: #{memory_usage} KB"
  end
  
  # Lazy evaluation
  def lazy_evaluation
    # Lazy enumerator for large datasets
    lazy_numbers = (1..1_000_000).lazy
      .select { |n| n.even? }
      .map { |n| n * 2 }
      .take(10)
    
    lazy_numbers.each { |n| puts n }
  end
end

# Usage
memory_demo = MemoryOptimization.new

# Object pooling
pool = MemoryOptimization::ObjectPool.new(
  -> { Array.new },
  ->(arr) { arr.clear },
  max_size: 5
)

pool.with_object do |array|
  array << 1
  array << 2
  puts "Array: #{array.inspect}"
end

memory_demo.weak_reference_example
memory_demo.memory_profile
memory_demo.lazy_evaluation
```

### Performance Profiling
```ruby
require 'benchmark'
require 'ruby-prof'

class PerformanceProfiling
  # Benchmarking
  def benchmark_comparison
    puts "Performance comparison:"
    
    Benchmark.bm(20) do |x|
      x.report("Array#each") do
        100_000.times { |i| i }
      end
      
      x.report("Array#map") do
        (1..100_000).map { |i| i * 2 }
      end
      
      x.report("Array#select") do
        (1..100_000).select { |i| i.even? }
      end
    end
  end
  
  # RubyProf profiling
  def profile_method
    # Profile CPU time
    result = RubyProf.profile do
      expensive_operation
    end
    
    # Print results
    printer = RubyProf::FlatPrinter.new(result)
    printer.print(STDOUT)
    
    # Save call graph
    File.open('call_graph.html', 'w') do |f|
      printer = RubyProf::CallStackPrinter.new(result)
      printer.print(f)
    end
  end
  
  # Memory profiling
  def memory_profile
    require 'memory_profiler'
    
    report = MemoryProfiler.report do
      create_objects
    end
    
    puts "Memory allocated: #{report.total_allocated_memsize} bytes"
    puts "Objects allocated: #{report.total_allocated}"
    puts "Memory retained: #{report.total_retained_memsize} bytes"
    puts "Objects retained: #{report.total_retained}"
    
    # Show biggest allocators
    report.allocated_memory_top_n(5).each do |data|
      puts "#{data[:file]}:#{data[:line]} #{data[:total_allocated_memsize]} bytes"
    end
  end
  
  # Method timing
  def time_methods
    methods = [
      :method1,
      :method2,
      :method3
    ]
    
    timings = {}
    
    methods.each do |method|
      timings[method] = Benchmark.realtime do
        1000.times { send(method) }
      end
    end
    
    puts "Method timings:"
    timings.each { |method, time| puts "#{method}: #{time.round(4)}s" }
  end
  
  private
  
  def expensive_operation
    sleep 0.1
    (1..1000).map { |i| i * i }.sum
  end
  
  def create_objects
    1000.times do |i|
      {
        id: i,
        name: "Object #{i}",
        data: Array.new(100) { rand(1000) }
      }
    end
  end
  
  def method1
    (1..100).map { |i| i * 2 }
  end
  
  def method2
    (1..100).select { |i| i.even? }
  end
  
  def method3
    (1..100).reduce(:+)
  end
end

# Usage
profiling_demo = PerformanceProfiling.new
profiling_demo.benchmark_comparison
profiling_demo.profile_method
profiling_demo.memory_profile
profiling_demo.time_methods
```

## Best Practices

### Advanced Ruby Best Practices
```ruby
# Use memoization for expensive computations
class ExpensiveCalculator
  def initialize
    @cache = {}
  end
  
  def fibonacci(n)
    @cache[n] ||= begin
      return n if n <= 1
      fibonacci(n - 1) + fibonacci(n - 2)
    end
  end
end

# Use constants for configuration
class ApplicationConfig
  TIMEOUT = 30
  MAX_RETRIES = 3
  API_ENDPOINT = "https://api.example.com"
  
  def self.timeout
    TIMEOUT
  end
end

# Use modules for namespacing
module MyApp
  module Models
    class User
      # User model code
    end
  end
  
  module Services
    class UserService
      # Service code
    end
  end
end

# Use delegation to avoid inheritance
class ReportGenerator
  attr_reader :formatter, :data_source
  
  def initialize(formatter:, data_source:)
    @formatter = formatter
    @data_source = data_source
  end
  
  def generate
    data = @data_source.fetch
    @formatter.format(data)
  end
  
  # Delegate to formatter
  def method_missing(method, *args, &block)
    if @formatter.respond_to?(method)
      @formatter.send(method, *args, &block)
    else
      super
    end
  end
  
  def respond_to_missing?(method, include_private = false)
    @formatter.respond_to?(method) || super
  end
end

# Use composition over inheritance
class Engine
  def initialize(type, horsepower)
    @type = type
    @horsepower = horsepower
  end
  
  def start
    "#{@type} engine starting"
  end
end

class Car
  def initialize(engine)
    @engine = engine
  end
  
  def start
    @engine.start
  end
end

# Use dependency injection
class UserService
  def initialize(repository:, notifier:)
    @repository = repository
    @notifier = notifier
  end
  
  def create_user(attributes)
    user = @repository.create(attributes)
    @notifier.notify("User created: #{user.id}")
    user
  end
end
```

## Common Pitfalls

### Advanced Ruby Pitfalls
```ruby
# Pitfall: Method missing abuse
class MethodMissingAbuse
  def method_missing(method, *args)
    # Too much logic in method_missing
    # Hard to debug and maintain
    if method.to_s.start_with?('get_')
      # Complex logic here
    elsif method.to_s.start_with?('set_')
      # More complex logic
    else
      super
    end
  end
end

# Solution: Use proper method definitions or delegation
class ProperMethodHandling
  def method_missing(method, *args)
    # Keep method_missing simple
    super
  end
  
  def respond_to_missing?(method, include_private = false)
    # Keep respond_to_missing simple
    super
  end
  
  # Define methods properly
  def self.define_accessors
    define_method(:name) { @name }
    define_method(:name=) { |value| @name = value }
  end
end

# Pitfall: Excessive metaprogramming
class ExcessiveMetaprogramming
  def self.create_class(name, &block)
    # Creating classes dynamically when not needed
    klass = Class.new(&block)
    Object.const_set(name, klass)
    klass
  end
end

# Solution: Use metaprogramming sparingly
class AppropriateMetaprogramming
  # Use metaprogramming for clear, specific purposes
  def self.create_validator(field, options = {})
    define_method("validate_#{field}") do
      value = instance_variable_get("@#{field}")
      
      options.each do |option, value|
        case option
        when :required
          raise "#{field} is required" if value.nil?
        when :min_length
          raise "#{field} is too short" if value.length < value
        end
      end
    end
  end
end

# Pitfall: Thread safety issues
class ThreadSafetyIssues
  def initialize
    @counter = 0
  end
  
  def increment
    # Not thread-safe
    @counter += 1
  end
end

# Solution: Use proper synchronization
class ThreadSafeCounter
  def initialize
    @counter = 0
    @mutex = Mutex.new
  end
  
  def increment
    @mutex.synchronize do
      @counter += 1
    end
  end
end

# Pitfall: Memory leaks
class MemoryLeak
  def initialize
    @cache = {}
  end
  
  def cache_result(key, value)
    @cache[key] = value  # Cache grows indefinitely
  end
end

# Solution: Use weak references or cleanup
class MemoryEfficientCache
  def initialize
    @cache = {}
    @max_size = 1000
  end
  
  def cache_result(key, value)
    cleanup if @cache.size >= @max_size
    @cache[key] = value
  end
  
  private
  
  def cleanup
    @cache.shift
  end
end
```

## Summary

Advanced Ruby features provide:

**Advanced Method Techniques:**
- Method aliasing and chaining
- Method removal and undefinition
- Runtime visibility manipulation
- Method introspection and reflection

**Advanced Blocks and Procs:**
- Proc vs lambda differences
- Closures and binding manipulation
- Higher-order functions
- Currying and partial application

**Advanced Metaprogramming:**
- Dynamic class and module creation
- Advanced method_missing patterns
- Sophisticated reflection techniques
- Runtime code generation

**Advanced Exception Handling:**
- Custom exception hierarchies
- Retry and timeout patterns
- Circuit breaker pattern
- Contextual error handling

**Advanced Concurrency:**
- Thread synchronization patterns
- Actor model implementation
- Thread pools and work queues
- Producer-consumer patterns

**Performance Optimization:**
- Object pooling and memory management
- Weak references and garbage collection
- Lazy evaluation techniques
- Comprehensive profiling tools

**Best Practices:**
- Memoization for expensive operations
- Proper dependency injection
- Composition over inheritance
- Appropriate metaprogramming usage

**Common Pitfalls:**
- Method missing abuse
- Excessive metaprogramming
- Thread safety issues
- Memory leaks and performance problems

Advanced Ruby features provide powerful tools for building sophisticated, high-performance applications when used appropriately and with careful consideration of maintainability and performance implications.
