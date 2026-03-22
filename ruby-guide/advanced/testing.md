# Testing Strategies in Ruby

## Overview

Testing is crucial for building reliable Ruby applications. This guide covers testing frameworks, strategies, and best practices for comprehensive testing in Ruby.

## Testing Frameworks

### Minitest

Minitest is Ruby's built-in testing framework.

```ruby
require 'minitest/autorun'

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

class Calculator
  def add(a, b)
    a + b
  end
  
  def divide(a, b)
    a / b
  end
  
  def multiply(a, b)
    a * b
  end
end
```

### RSpec

RSpec is a popular BDD (Behavior-Driven Development) framework.

```ruby
require 'rspec'

class Calculator
  def add(a, b)
    a + b
  end
  
  def divide(a, b)
    raise ZeroDivisionError if b == 0
    a / b
  end
end

RSpec.describe Calculator do
  let(:calculator) { Calculator.new }
  
  describe '#add' do
    it 'returns the sum of two numbers' do
      result = calculator.add(2, 3)
      expect(result).to eq(5)
    end
    
    it 'handles negative numbers' do
      result = calculator.add(-2, 3)
      expect(result).to eq(1)
    end
  end
  
  describe '#divide' do
    it 'returns the quotient of two numbers' do
      result = calculator.divide(10, 2)
      expect(result).to eq(5)
    end
    
    it 'raises ZeroDivisionError when dividing by zero' do
      expect { calculator.divide(10, 0) }.to raise_error(ZeroDivisionError)
    end
  end
  
  context 'when dealing with edge cases' do
    it 'handles large numbers' do
      result = calculator.add(1_000_000, 1_000_000)
      expect(result).to eq(2_000_000)
    end
  end
end
```

## Test Types and Strategies

### Unit Testing

```ruby
# Test individual components in isolation
require 'minitest/autorun'

class UserTest < Minitest::Test
  def setup
    @user = User.new("John", "john@example.com")
  end
  
  def test_user_creation
    assert_equal "John", @user.name
    assert_equal "john@example.com", @user.email
  end
  
  def test_email_validation
    assert @user.valid?
    
    @user.email = "invalid-email"
    refute @user.valid?
  end
  
  def test_full_name
    assert_equal "John Doe", @user.full_name
  end
end

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
```

### Integration Testing

```ruby
# Test how components work together
require 'minitest/autorun'

class OrderProcessingTest < Minitest::Test
  def setup
    @user = User.new("John", "john@example.com")
    @product = Product.new("Laptop", 999.99)
    @payment_gateway = MockPaymentGateway.new
    @order_processor = OrderProcessor.new(@payment_gateway)
  end
  
  def test_successful_order
    order = @order_processor.process_order(@user, [@product])
    
    assert order.completed?
    assert_equal 999.99, order.total
    assert @payment_gateway.payment_processed?
  end
  
  def test_failed_payment
    @payment_gateway.force_failure = true
    
    assert_raises(PaymentError) do
      @order_processor.process_order(@user, [@product])
    end
  end
end

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

class Product
  attr_reader :name, :price
  
  def initialize(name, price)
    @name = name
    @price = price
  end
end

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
```

### Functional Testing

```ruby
# Test complete user workflows
require 'minitest/autorun'
require 'net/http'
require 'uri'

class WebApplicationTest < Minitest::Test
  def setup
    @base_url = 'http://localhost:3000'
  end
  
  def test_user_registration_flow
    # Register user
    response = post('/users', {
      name: 'John Doe',
      email: 'john@example.com',
      password: 'password123'
    })
    
    assert_equal 201, response.code.to_i
    
    # Login
    login_response = post('/login', {
      email: 'john@example.com',
      password: 'password123'
    })
    
    assert_equal 200, login_response.code.to_i
    
    # Access protected resource
    dashboard_response = get('/dashboard', auth_token: extract_token(login_response))
    
    assert_equal 200, dashboard_response.code.to_i
    assert dashboard_response.body.include?('Welcome, John')
  end
  
  private
  
  def post(path, data)
    uri = URI("#{@base_url}#{path}")
    http = Net::HTTP.new(uri.host, uri.port)
    request = Net::HTTP::Post.new(uri)
    request.set_form_data(data)
    http.request(request)
  end
  
  def get(path, auth_token: nil)
    uri = URI("#{@base_url}#{path}")
    http = Net::HTTP.new(uri.host, uri.port)
    request = Net::HTTP::Get.new(uri)
    request['Authorization'] = "Bearer #{auth_token}" if auth_token
    http.request(request)
  end
  
  def extract_token(response)
    JSON.parse(response.body)['token']
  end
end
```

## Test Doubles and Mocking

### Test Stubs

```ruby
require 'minitest/autorun'

class WeatherServiceTest < Minitest::Test
  def test_weather_report_with_stub
    # Stub the external API call
    weather_data = {
      temperature: 25,
      humidity: 60,
      description: "Sunny"
    }
    
    weather_service = WeatherService.new
    weather_service.stub(:fetch_weather, weather_data) do
      report = weather_service.get_report("New York")
      
      assert_equal 25, report.temperature
      assert_equal 60, report.humidity
      assert_equal "Sunny", report.description
    end
  end
end

class WeatherService
  def get_report(city)
    weather_data = fetch_weather(city)
    WeatherReport.new(weather_data)
  end
  
  def fetch_weather(city)
    # Would make API call to weather service
    # For testing, this gets stubbed
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
```

### Mock Objects

```ruby
require 'minitest/autorun'

class NotificationServiceTest < Minitest::Test
  def test_sends_notification_on_user_action
    # Create mock
    mock_notifier = Minitest::Mock.new
    mock_notifier.expect(:send_notification, true, ["user@example.com", "Welcome!"])
    
    user_service = UserService.new(mock_notifier)
    result = user_service.register_user("John", "user@example.com")
    
    assert result
    mock_notifier.verify
  end
end

class UserService
  def initialize(notifier)
    @notifier = notifier
  end
  
  def register_user(name, email)
    # Simulate user registration
    user = User.new(name, email)
    @notifier.send_notification(email, "Welcome!")
    true
  end
end

class User
  def initialize(name, email)
    @name = name
    @email = email
  end
end
```

### Test Spies

```ruby
require 'minitest/autorun'

class EmailSpy
  attr_reader :sent_emails
  
  def initialize
    @sent_emails = []
  end
  
  def send_email(to, subject, body)
    @sent_emails << { to: to, subject: subject, body: body }
  end
end

class UserServiceTest < Minitest::Test
  def test_sends_welcome_email
    email_spy = EmailSpy.new
    user_service = UserService.new(email_spy)
    
    user_service.register_user("John", "john@example.com")
    
    assert_equal 1, email_spy.sent_emails.length
    email = email_spy.sent_emails.first
    assert_equal "john@example.com", email[:to]
    assert_equal "Welcome!", email[:subject]
  end
end

class UserService
  def initialize(email_service)
    @email_service = email_service
  end
  
  def register_user(name, email)
    # Simulate user registration
    @email_service.send_email(email, "Welcome!", "Welcome to our service!")
  end
end
```

## Test Organization

### Test Helpers

```ruby
# test/test_helper.rb
require 'minitest/autorun'
require 'minitest/pride'
require_relative '../lib/my_app'

class MiniTest::Test
  def setup
    super
    # Common setup for all tests
  end
  
  def teardown
    super
    # Common cleanup for all tests
  end
  
  protected
  
  def create_user(name: "Test User", email: "test@example.com")
    User.new(name, email)
  end
  
  def create_product(name: "Test Product", price: 9.99)
    Product.new(name, price)
  end
  
  def assert_valid_email(email)
    assert email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i), 
           "#{email} should be a valid email"
  end
  
  def assert_invalid_email(email)
    refute email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i), 
           "#{email} should be an invalid email"
  end
end

# test/user_test.rb
require_relative 'test_helper'

class UserTest < MiniTest::Test
  def test_user_creation
    user = create_user(name: "John", email: "john@example.com")
    
    assert_equal "John", user.name
    assert_equal "john@example.com", user.email
    assert_valid_email(user.email)
  end
end
```

### Shared Examples

```ruby
# test/shared_examples/validation_examples.rb
module ValidationExamples
  def test_requires_name
    object = subject_class.new(valid_attributes.merge(name: ""))
    refute object.valid?, "should require name"
  end
  
  def test_requires_email
    object = subject_class.new(valid_attributes.merge(email: ""))
    refute object.valid?, "should require email"
  end
  
  def test_accepts_valid_email
    object = subject_class.new(valid_attributes.merge(email: "test@example.com"))
    assert object.valid?, "should accept valid email"
  end
  
  def test_rejects_invalid_email
    object = subject_class.new(valid_attributes.merge(email: "invalid-email"))
    refute object.valid?, "should reject invalid email"
  end
  
  private
  
  def subject_class
    raise NotImplementedError, "Test must define subject_class"
  end
  
  def valid_attributes
    raise NotImplementedError, "Test must define valid_attributes"
  end
end

# test/user_test.rb
require_relative 'shared_examples/validation_examples'

class UserTest < MiniTest::Test
  include ValidationExamples
  
  def subject_class
    User
  end
  
  def valid_attributes
    { name: "John", email: "john@example.com" }
  end
end
```

## Test Data Management

### Factories

```ruby
# test/factories/user_factory.rb
class UserFactory
  def self.create(attributes = {})
    default_attributes = {
      name: "John Doe",
      email: "john@example.com",
      age: 30
    }
    
    User.new(default_attributes.merge(attributes))
  end
  
  def self.create_admin(attributes = {})
    create(attributes.merge(role: "admin"))
  end
  
  def self.create_multiple(count, attributes = {})
    count.times.map { |i| create(attributes.merge(email: "user#{i}@example.com")) }
  end
end

# test/user_test.rb
class UserTest < MiniTest::Test
  def test_factory_creates_valid_user
    user = UserFactory.create
    assert user.valid?
  end
  
  def test_factory_with_attributes
    user = UserFactory.create(name: "Alice", age: 25)
    assert_equal "Alice", user.name
    assert_equal 25, user.age
  end
  
  def test_factory_creates_multiple_users
    users = UserFactory.create_multiple(3)
    assert_equal 3, users.length
    assert users.all?(&:valid?)
  end
end
```

### Fixtures

```ruby
# test/fixtures/users.yml
john:
  name: John Doe
  email: john@example.com
  age: 30

jane:
  name: Jane Smith
  email: jane@example.com
  age: 25

# test/user_test.rb
require 'yaml'

class UserTest < MiniTest::Test
  def setup
    @fixtures = YAML.load_file('test/fixtures/users.yml')
  end
  
  def test_loads_user_fixtures
    john_data = @fixtures['john']
    john = User.new(john_data)
    
    assert_equal "John Doe", john.name
    assert_equal "john@example.com", john.email
    assert_equal 30, john.age
  end
end
```

## Test Coverage

### Simple Coverage

```ruby
# Run with: ruby -rcoverage test/test_helper.rb
require 'coverage'

Coverage.start

# Run your tests
Dir.glob('test/**/*_test.rb').each { |f| require f }

# Generate coverage report
result = Coverage.result
result.each do |file, coverage|
  next unless file.start_with?('./lib/')
  
  lines = File.readlines(file)
  covered_lines = coverage.each_with_index.select { |cov, i| cov && i > 0 }.count
  total_lines = lines.count - 1  # Exclude first line (require statement)
  
  coverage_percentage = (covered_lines.to_f / total_lines * 100).round(2)
  puts "#{file}: #{coverage_percentage}% coverage"
end
```

### SimpleCov Integration

```ruby
# Gemfile
group :test do
  gem 'simplecov'
end

# test/test_helper.rb
require 'simplecov'
SimpleCov.start 'rails' do
  add_group 'Libraries', 'lib'
  add_group 'Models', 'app/models'
  add_group 'Controllers', 'app/controllers'
  
  add_filter '/test/'
  add_filter '/config/'
  add_filter '/vendor/'
  
  minimum_coverage 90
end
```

## Behavior-Driven Development

### Feature Testing with Cucumber

```ruby
# features/user_registration.feature
Feature: User Registration
  As a new user
  I want to register an account
  So that I can use the application

  Scenario: Successful registration
    Given I am on the registration page
    When I fill in "Name" with "John Doe"
    And I fill in "Email" with "john@example.com"
    And I fill in "Password" with "password123"
    And I press "Register"
    Then I should see "Welcome, John Doe"
    And I should be logged in

  Scenario: Registration with invalid email
    Given I am on the registration page
    When I fill in "Name" with "John Doe"
    And I fill in "Email" with "invalid-email"
    And I fill in "Password" with "password123"
    And I press "Register"
    Then I should see "Email is invalid"
    And I should not be logged in

# features/step_definitions/user_steps.rb
Given('I am on the registration page') do
  visit '/register'
end

When('I fill in {string} with {string}') do |field, value|
  fill_in field, with: value
end

When('I press {string}') do |button|
  click_button button
end

Then('I should see {string}') do |text|
  expect(page).to have_content(text)
end

Then('I should be logged in') do
  expect(page).to have_content('Logout')
end

Then('I should not be logged in') do
  expect(page).to have_content('Login')
end
```

## Performance Testing

### Benchmark Tests

```ruby
require 'minitest/autorun'
require 'benchmark'

class PerformanceTest < MiniTest::Test
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
  end
  
  def test_string_concatenation_performance
    iterations = 10000
    
    time = Benchmark.measure do
      result = ""
      iterations.times { |i| result += "item_#{i}" }
    end
    
    puts "String concatenation: #{time.real.round(4)}s"
    assert_operator time.real, :<, 0.5, "String concatenation should be fast"
  end
end
```

## Best Practices

### 1. Write Isolated Tests

```ruby
# Good: Tests don't depend on each other
class UserTest < MiniTest::Test
  def test_user_creation
    user = User.new("John", "john@example.com")
    assert user.valid?
  end
  
  def test_user_validation
    user = User.new("", "invalid-email")
    refute user.valid?
  end
end

# Bad: Tests depend on shared state
class BadUserTest < MiniTest::Test
  def setup
    @user = User.new("John", "john@example.com")  # Shared state
  end
  
  def test_modification
    @user.name = "Jane"  # Modifies shared state
    assert_equal "Jane", @user.name
  end
  
  def test_original_state  # This test will fail!
    assert_equal "John", @user.name
  end
end
```

### 2. Use Descriptive Test Names

```ruby
# Good: Clear what the test does
def test_user_registration_with_valid_data_creates_active_user
  user = User.register("John", "john@example.com", "password")
  assert user.active?
end

# Bad: Unclear test purpose
def test_user_registration
  # Test code here
end
```

### 3. Test One Thing at a Time

```ruby
# Good: Single assertion focus
def test_user_email_validation
  user = User.new("John", "invalid-email")
  refute user.valid?
  assert_includes user.errors[:email], "is invalid"
end

# Bad: Multiple unrelated assertions
def test_user_validation
  user = User.new("", "invalid-email")
  refute user.valid?
  assert_includes user.errors[:name], "can't be blank"
  assert_includes user.errors[:email], "is invalid"
  assert_nil user.id
end
```

### 4. Use Test Data Builders

```ruby
# Good: Use factories/builders
def test_user_creation
  user = UserFactory.create(name: "John", email: "john@example.com")
  assert user.valid?
end

# Bad: Hard-coded test data
def test_user_creation
  user = User.new("John", "john@example.com", "password", "user", Time.now, true)
  assert user.valid?
end
```

## Practice Exercises

### Exercise 1: Test Suite Builder
Create a test suite builder that:
- Generates test files automatically
- Supports multiple testing frameworks
- Includes test data generation
- Provides coverage reporting

### Exercise 2: Mock Framework
Build a simple mocking framework that:
- Creates mock objects
- Expects method calls
- Verifies interactions
- Provides failure messages

### Exercise 3: Test Data Generator
Implement a test data generator that:
- Creates realistic test data
- Supports different data types
- Handles relationships
- Generates edge cases

### Exercise 4: Continuous Integration Setup
Create a CI configuration that:
- Runs tests automatically
- Generates coverage reports
- Performs performance tests
- Notifies on failures

---

**Congratulations! You've completed the comprehensive Ruby guide! 🎉**
