# Testing Examples
# Demonstrating testing frameworks, strategies, and best practices

puts "=== MINITEST BASICS ==="

require 'minitest/autorun'

# Simple test class
class Calculator
  def add(a, b)
    a + b
  end
  
  def divide(a, b)
    raise ZeroDivisionError if b == 0
    a / b
  end
  
  def multiply(a, b)
    a * b
  end
end

# Test class (would normally be in separate file)
class CalculatorTest < Minitest::Test
  def setup
    @calculator = Calculator.new
  end
  
  def test_addition
    result = @calculator.add(2, 3)
    assert_equal 5, result
  end
  
  def test_division_by_zero
    assert_raises(ZeroDivisionError) do
      @calculator.divide(10, 0)
    end
  end
  
  def test_multiplication
    result = @calculator.multiply(4, 5)
    assert_equal 20, result
    assert_kind_of Integer, result
  end
end

puts "\n=== RSPEC BASICS ==="

# RSpec-style testing (would normally use RSpec framework)
class RSpecStyleTest
  def initialize
    @calculator = Calculator.new
  end
  
  def test_addition
    result = @calculator.add(2, 3)
    raise "Expected 5, got #{result}" unless result == 5
    puts "✓ Addition test passed"
  end
  
  def test_division_error
    begin
      @calculator.divide(10, 0)
      raise "Expected ZeroDivisionError"
    rescue ZeroDivisionError
      puts "✓ Division error test passed"
    end
  end
  
  def run_tests
    test_addition
    test_division_error
    puts "All RSpec-style tests passed!"
  end
end

rspec_test = RSpecStyleTest.new
rspec_test.run_tests

puts "\n=== UNIT TESTING ==="

class User
  attr_accessor :name, :email
  
  def initialize(name, email)
    @name = name
    @email = email
  end
  
  def valid?
    email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
  end
  
  def full_name
    "#{name} Doe"
  end
end

class UserTest
  def initialize
    @user = User.new("John", "john@example.com")
  end
  
  def test_user_creation
    assert_equal "John", @user.name
    assert_equal "john@example.com", @user.email
    puts "✓ User creation test passed"
  end
  
  def test_email_validation
    assert @user.valid?, "Valid email should pass validation"
    
    @user.email = "invalid-email"
    assert !@user.valid?, "Invalid email should fail validation"
    puts "✓ Email validation test passed"
  end
  
  def test_full_name
    assert_equal "John Doe", @user.full_name
    puts "✓ Full name test passed"
  end
  
  def run_tests
    test_user_creation
    test_email_validation
    test_full_name
    puts "All unit tests passed!"
  end
  
  private
  
  def assert(condition, message = "Assertion failed")
    raise message unless condition
  end
  
  def assert_equal(expected, actual, message = "Values not equal")
    raise "#{message}: expected #{expected}, got #{actual}" unless expected == actual
  end
end

user_test = UserTest.new
user_test.run_tests

puts "\n=== MOCK OBJECTS ==="

class MockPaymentGateway
  attr_accessor :force_failure, :payment_processed
  
  def initialize
    @payment_processed = false
    @force_failure = false
  end
  
  def process_payment(amount)
    raise PaymentError if @force_failure
    @payment_processed = true
    true
  end
end

class PaymentError < StandardError; end

class Order
  attr_reader :total, :completed
  
  def initialize(user, items)
    @user = user
    @items = items
    @total = items.sum(&:price)
    @completed = false
  end
  
  def complete!
    @completed = true
  end
end

class OrderProcessor
  def initialize(payment_gateway)
    @payment_gateway = payment_gateway
  end
  
  def process_order(user, items)
    order = Order.new(user, items)
    
    if @payment_gateway.process_payment(order.total)
      order.complete!
      order
    else
      raise PaymentError
    end
  end
end

class Product
  attr_reader :name, :price
  
  def initialize(name, price)
    @name = name
    @price = price
  end
end

# Integration test with mock
class OrderProcessingTest
  def test_successful_order
    mock_gateway = MockPaymentGateway.new
    processor = OrderProcessor.new(mock_gateway)
    
    user = User.new("John", "john@example.com")
    product = Product.new("Laptop", 999.99)
    
    order = processor.process_order(user, [product])
    
    assert order.completed?, "Order should be completed"
    assert_equal 999.99, order.total
    assert mock_gateway.payment_processed?, "Payment should be processed"
    puts "✓ Successful order test passed"
  end
  
  def test_failed_payment
    mock_gateway = MockPaymentGateway.new
    mock_gateway.force_failure = true
    processor = OrderProcessor.new(mock_gateway)
    
    user = User.new("John", "john@example.com")
    product = Product.new("Laptop", 999.99)
    
    begin
      processor.process_order(user, [product])
      raise "Expected PaymentError"
    rescue PaymentError
      puts "✓ Failed payment test passed"
    end
  end
  
  def run_tests
    test_successful_order
    test_failed_payment
    puts "All integration tests passed!"
  end
  
  private
  
  def assert(condition, message = "Assertion failed")
    raise message unless condition
  end
  
  def assert_equal(expected, actual, message = "Values not equal")
    raise "#{message}: expected #{expected}, got #{actual}" unless expected == actual
  end
end

order_test = OrderProcessingTest.new
order_test.run_tests

puts "\n=== TEST HELPERS ==="

class TestHelper
  def self.create_user(name: "Test User", email: "test@example.com")
    User.new(name, email)
  end
  
  def self.create_product(name: "Test Product", price: 9.99)
    Product.new(name, price)
  end
  
  def self.assert_valid_email(email)
    email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
  end
  
  def self.assert_invalid_email(email)
    !email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
  end
end

class HelperTest
  def test_helper_creates_valid_user
    user = TestHelper.create_user
    assert user.valid?
    puts "✓ Helper creates valid user"
  end
  
  def test_helper_with_attributes
    user = TestHelper.create_user(name: "Alice", email: "alice@example.com")
    assert_equal "Alice", user.name
    assert_equal "alice@example.com", user.email
    puts "✓ Helper with attributes works"
  end
  
  def test_email_validations
    assert TestHelper.assert_valid_email("test@example.com")
    assert TestHelper.assert_invalid_email("invalid-email")
    puts "✓ Email validations work"
  end
  
  def run_tests
    test_helper_creates_valid_user
    test_helper_with_attributes
    test_email_validations
    puts "All helper tests passed!"
  end
  
  private
  
  def assert(condition, message = "Assertion failed")
    raise message unless condition
  end
  
  def assert_equal(expected, actual, message = "Values not equal")
    raise "#{message}: expected #{expected}, got #{actual}" unless expected == actual
  end
end

helper_test = HelperTest.new
helper_test.run_tests

puts "\n=== FACTORY PATTERN ==="

class UserFactory
  def self.create(attributes = {})
    default_attributes = {
      name: "John Doe",
      email: "john@example.com",
      age: 30
    }
    
    User.new(default_attributes[:name], default_attributes[:email])
  end
  
  def self.create_admin(attributes = {})
    user = create(attributes)
    user.instance_variable_set(:@role, "admin") if user.respond_to?(:instance_variable_set)
    user
  end
  
  def self.create_multiple(count, attributes = {})
    count.times.map { |i| create(attributes.merge(email: "user#{i}@example.com")) }
  end
end

class FactoryTest
  def test_factory_creates_valid_user
    user = UserFactory.create
    assert user.valid?
    puts "✓ Factory creates valid user"
  end
  
  def test_factory_with_attributes
    user = UserFactory.create(name: "Alice", email: "alice@example.com")
    assert_equal "Alice", user.name
    assert_equal "alice@example.com", user.email
    puts "✓ Factory with attributes works"
  end
  
  def test_factory_creates_multiple
    users = UserFactory.create_multiple(3)
    assert_equal 3, users.length
    assert users.all?(&:valid?)
    puts "✓ Factory creates multiple users"
  end
  
  def run_tests
    test_factory_creates_valid_user
    test_factory_with_attributes
    test_factory_creates_multiple
    puts "All factory tests passed!"
  end
  
  private
  
  def assert(condition, message = "Assertion failed")
    raise message unless condition
  end
  
  def assert_equal(expected, actual, message = "Values not equal")
    raise "#{message}: expected #{expected}, got #{actual}" unless expected == actual
  end
end

factory_test = FactoryTest.new
factory_test.run_tests

puts "\n=== TEST DOUBLES ==="

# Test Stub
class WeatherService
  def get_report(city)
    weather_data = fetch_weather(city)
    WeatherReport.new(weather_data)
  end
  
  def fetch_weather(city)
    # Would make API call to weather service
    { temperature: 20, humidity: 50, description: "Cloudy" }
  end
end

class WeatherReport
  attr_reader :temperature, :humidity, :description
  
  def initialize(data)
    @temperature = data[:temperature]
    @humidity = data[:humidity]
    @description = data[:description]
  end
end

class WeatherServiceTest
  def test_weather_report_with_stub
    weather_data = {
      temperature: 25,
      humidity: 60,
      description: "Sunny"
    }
    
    # Simulate stub by monkey-patching
    weather_service = WeatherService.new
    def weather_service.fetch_weather(city)
      {
        temperature: 25,
        humidity: 60,
        description: "Sunny"
      }
    end
    
    report = weather_service.get_report("New York")
    
    assert_equal 25, report.temperature
    assert_equal 60, report.humidity
    assert_equal "Sunny", report.description
    puts "✓ Weather stub test passed"
  end
  
  def run_tests
    test_weather_report_with_stub
    puts "All stub tests passed!"
  end
  
  private
  
  def assert(condition, message = "Assertion failed")
    raise message unless condition
  end
  
  def assert_equal(expected, actual, message = "Values not equal")
    raise "#{message}: expected #{expected}, got #{actual}" unless expected == actual
  end
end

weather_test = WeatherServiceTest.new
weather_test.run_tests

puts "\n=== PERFORMANCE TESTING ==="

require 'benchmark'

class PerformanceTest
  def test_array_performance
    sizes = [100, 1000, 10000]
    
    sizes.each do |size|
      array = (1..size).to_a
      
      time = Benchmark.measure do
        array.each { |n| n * 2 }
      end
      
      puts "Array size #{size}: #{time.real.round(4)}s"
      
      # Assert performance doesn't degrade too much
      assert_operator time.real, :<, 1.0, "Processing #{size} items should be fast"
    end
    puts "✓ Performance test passed"
  end
  
  def run_tests
    test_array_performance
    puts "All performance tests passed!"
  end
  
  private
  
  def assert_operator(value, operator, expected, message = "Comparison failed")
    case operator
    when :<
      raise message unless value < expected
    when :>
      raise message unless value > expected
    when :<=
      raise message unless value <= expected
    when :>=
      raise message unless value >= expected
    else
      raise "Unknown operator: #{operator}"
    end
  end
end

perf_test = PerformanceTest.new
perf_test.run_tests

puts "\n=== TEST ORGANIZATION ==="

# Test suite runner
class TestSuite
  def initialize
    @tests = []
  end
  
  def add_test(test_class)
    @tests << test_class
  end
  
  def run_all
    passed = 0
    failed = 0
    
    @tests.each do |test_class|
      begin
        test_instance = test_class.new
        test_instance.run_tests
        passed += 1
      rescue => e
        puts "❌ #{test_class.name} failed: #{e.message}"
        failed += 1
      end
    end
    
    puts "\n=== TEST RESULTS ==="
    puts "Passed: #{passed}"
    puts "Failed: #{failed}"
    puts "Total: #{passed + failed}"
    
    failed == 0
  end
end

# Run all tests
suite = TestSuite.new
suite.add_test(UserTest)
suite.add_test(OrderProcessingTest)
suite.add_test(HelperTest)
suite.add_test(FactoryTest)
suite.add_test(WeatherServiceTest)
suite.add_test(PerformanceTest)

success = suite.run_all
puts "\n#{success ? '🎉 All tests passed!' : '❌ Some tests failed!'}"

puts "\n=== TESTING SUMMARY ==="
puts "- Unit testing with Minitest and RSpec-style approaches"
puts "- Integration testing with mock objects"
puts "- Test helpers and utility methods"
puts "- Factory pattern for test data generation"
puts "- Test doubles (stubs, mocks, spies)"
puts "- Performance testing with Benchmark"
puts "- Test organization and suite management"
puts "- Assertion helpers and validation utilities"
puts "\nAll examples demonstrate comprehensive Ruby testing strategies!"
