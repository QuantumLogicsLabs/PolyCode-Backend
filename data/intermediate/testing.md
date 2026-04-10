# Ruby Testing

## Introduction to Testing

### Why Test?
```ruby
# Testing ensures code quality, prevents regressions, and provides documentation

# Benefits of testing:
# 1. Catch bugs early
# 2. Ensure code works as expected
# 3. Provide living documentation
# 4. Enable safe refactoring
# 5. Improve code design

# Types of testing:
# - Unit tests: Test individual components
# - Integration tests: Test component interactions
# - System tests: Test entire application
# - Acceptance tests: Test user behavior
```

### Testing Philosophy
```ruby
# Test-Driven Development (TDD) Cycle:
# 1. Red: Write a failing test
# 2. Green: Write code to make test pass
# 3. Refactor: Improve code while keeping tests green

# Behavior-Driven Development (BDD):
# Focus on behavior from user perspective
# Use descriptive language for tests
# Test scenarios and examples

# Testing Pyramid:
# - Many unit tests (fast, isolated)
# - Fewer integration tests (slower, more complex)
# - Even fewer system tests (slowest, most complex)
```

## RSpec Framework

### RSpec Setup
```ruby
# Gemfile
group :development, :test do
  gem 'rspec', '~> 3.0'
  gem 'rspec-rails', '~> 4.0'  # For Rails
  gem 'factory_bot', '~> 6.0'
  gem 'faker', '~> 2.0'
  gem 'database_cleaner', '~> 1.0'
  gem 'simplecov', '~> 0.16'
end

# Install RSpec
# Command: bundle install
# Command: rails generate rspec:install  # For Rails
# Command: rspec --init  # For plain Ruby

# spec/spec_helper.rb
require 'simplecov'
SimpleCov.start

RSpec.configure do |config|
  config.color = true
  config.formatter = :documentation
  config.order = :random
  
  # Use expect syntax
  config.expect_with :rspec do |expectations|
    expectations.include_chain_clauses_in_custom_matcher_descriptions = true
  end
  
  # Use should syntax (optional)
  config.mock_with :rspec do |mocks|
    mocks.verify_partial_double_expectations = true
  end
end
```

### Basic RSpec Syntax
```ruby
# spec/calculator_spec.rb
require 'calculator'

RSpec.describe Calculator do
  # Describe the class/module being tested
  describe "#add" do
    # Describe the method being tested
    it "returns the sum of two numbers" do
      # Test case
      calculator = Calculator.new
      result = calculator.add(2, 3)
      
      # Expectation
      expect(result).to eq(5)
    end
    
    it "handles negative numbers" do
      calculator = Calculator.new
      result = calculator.add(-2, -3)
      
      expect(result).to eq(-5)
    end
  end
  
  describe "#divide" do
    context "when dividing by zero" do
      it "raises an error" do
        calculator = Calculator.new
        
        expect { calculator.divide(10, 0) }.to raise_error(ZeroDivisionError)
      end
    end
    
    context "when division is valid" do
      it "returns the quotient" do
        calculator = Calculator.new
        result = calculator.divide(10, 2)
        
        expect(result).to eq(5)
      end
    end
  end
end
```

### RSpec Matchers
```ruby
# Equality matchers
expect(value).to eq(5)        # == (object equality)
expect(value).to eql(5)       # === (object identity)
expect(value).to equal(obj)    # .equal? (same object)

# Comparison matchers
expect(value).to be > 5
expect(value).to be >= 5
expect(value).to be < 10
expect(value).to be <= 10

# Truthiness matchers
expect(value).to be true
expect(value).to be false
expect(value).to be_truthy
expect(value).to be_falsey
expect(value).to be_nil

# Collection matchers
expect(array).to include(item)
expect(array).to contain_exactly(item1, item2)
expect(array).to start_with(item1)
expect(array).to end_with(item3)

# String matchers
expect(string).to include("substring")
expect(string).to start_with("prefix")
expect(string).to end_with("suffix")
expect(string).to match(/regex/)

# Type matchers
expect(value).to be_a(String)
expect(value).to be_an_instance_of(String)
expect(value).to be_kind_of(Numeric)

# Error matchers
expect { block }.to raise_error(ErrorType)
expect { block }.to raise_error("error message")
expect { block }.to raise_error(ErrorType, "error message")

# Change matchers
expect { block }.to change(object, :attribute)
expect { block }.to change(object, :attribute).from(old_value).to(new_value)
expect { block }.to change(object, :attribute).by(delta)

# Yield matchers
expect { |b| object.method(&b) }.to yield_with_args(arg1, arg2)
```

### RSpec Hooks
```ruby
RSpec.describe User do
  # Run once before all examples in this group
  before(:all) do
    @shared_resource = create_shared_resource
  end
  
  # Run before each example
  before(:each) do
    @user = User.new(name: "John", email: "john@example.com")
  end
  
  # Run after each example
  after(:each) do
    @user = nil
  end
  
  # Run once after all examples in this group
  after(:all) do
    @shared_resource = nil
  end
  
  # Run around each example
  around(:each) do |example|
    # Setup
    database.transaction do
      example.run
      # Cleanup (rolled back automatically)
    end
  end
  
  it "has a name" do
    expect(@user.name).to eq("John")
  end
  
  it "has an email" do
    expect(@user.email).to eq("john@example.com")
  end
end
```

## Minitest Framework

### Minitest Setup
```ruby
# Gemfile
group :test do
  gem 'minitest', '~> 5.0'
  gem 'minitest-reporters', '~> 1.0'
  gem 'factory_bot', '~> 6.0'
end

# test/test_helper.rb
require 'minitest/autorun'
require 'minitest/reporters'
require 'factory_bot'

Minitest::Reporters.use! [Minitest::Reporters::SpecReporter.new]

class Minitest::Test
  include FactoryBot::Syntax::Methods
end

FactoryBot.find_definitions
```

### Basic Minitest Syntax
```ruby
# test/calculator_test.rb
require 'test_helper'

class CalculatorTest < Minitest::Test
  def setup
    @calculator = Calculator.new
  end
  
  def teardown
    @calculator = nil
  end
  
  def test_add_returns_sum
    result = @calculator.add(2, 3)
    assert_equal 5, result
  end
  
  def test_add_handles_negative_numbers
    result = @calculator.add(-2, -3)
    assert_equal(-5, result)
  end
  
  def test_divide_raises_error_when_dividing_by_zero
    assert_raises(ZeroDivisionError) do
      @calculator.divide(10, 0)
    end
  end
  
  def test_user_has_name
    user = User.new(name: "John")
    assert_equal "John", user.name
  end
  
  def test_user_is_valid_with_name
    user = User.new(name: "John")
    assert user.valid?
  end
  
  def test_user_is_invalid_without_name
    user = User.new
    refute user.valid?
  end
end
```

### Minitest Assertions
```ruby
# Basic assertions
assert_equal(expected, actual)
assert_not_equal(expected, actual)

# Truthiness assertions
assert(actual)
refute(actual)
assert_nil(actual)
refute_nil(actual)

# Inclusion assertions
assert_includes(collection, item)
refute_includes(collection, item)

# Type assertions
assert_instance_of(expected_class, actual)
assert_kind_of(expected_class, actual)
assert_respond_to(actual, method)

# Exception assertions
assert_raises(ErrorType) do
  # Code that should raise error
end

# Numeric assertions
assert_operator(5, :>, 3)
assert_in_delta(3.14159, 3.14, 0.01)

# File assertions
assert_path_exists(path)
refute_path_exists(path)

# Custom assertions
def assert_valid_user(user)
  assert user.valid?, "User should be valid"
end
```

## Test Doubles

### Mocks and Stubs
```ruby
# Using RSpec mocks
RSpec.describe PaymentProcessor do
  it "charges the payment gateway" do
    # Create a mock
    gateway = double("payment_gateway")
    
    # Set expectations
    expect(gateway).to receive(:charge).with(100, "USD").and_return(true)
    
    # Use the mock
    processor = PaymentProcessor.new(gateway)
    result = processor.process_payment(100, "USD")
    
    expect(result).to be true
  end
  
  it "handles failed payments" do
    gateway = double("payment_gateway")
    
    expect(gateway).to receive(:charge).and_raise(PaymentError)
    
    processor = PaymentProcessor.new(gateway)
    
    expect { processor.process_payment(100, "USD") }.to raise_error(PaymentError)
  end
end

# Using stubs
RSpec.describe UserService do
  it "creates a user with valid data" do
    # Stub the User class
    allow(User).to receive(:new).and_return(double("user", save: true))
    
    service = UserService.new
    result = service.create_user(name: "John", email: "john@example.com")
    
    expect(result).to be true
  end
  
  it "handles validation errors" do
    user = double("user")
    allow(user).to receive(:save).and_return(false)
    allow(User).to receive(:new).and_return(user)
    
    service = UserService.new
    result = service.create_user(name: "", email: "")
    
    expect(result).to be false
  end
end
```

### Test Doubles with Mocha
```ruby
# Gemfile
group :test do
  gem 'mocha', '~> 1.0'
end

# test/test_helper.rb
require 'mocha/minitest'

class PaymentProcessorTest < Minitest::Test
  def test_charges_payment_gateway
    # Create a mock object
    gateway = mock('payment_gateway')
    
    # Set expectations
    gateway.expects(:charge).with(100, "USD").returns(true)
    
    # Use the mock
    processor = PaymentProcessor.new(gateway)
    result = processor.process_payment(100, "USD")
    
    assert result
  end
  
  def test_handles_failed_payments
    gateway = mock('payment_gateway')
    gateway.expects(:charge).raises(PaymentError)
    
    processor = PaymentProcessor.new(gateway)
    
    assert_raises(PaymentError) do
      processor.process_payment(100, "USD")
    end
  end
end
```

## Factory Bot

### Factory Definition
```ruby
# spec/factories/users.rb
FactoryBot.define do
  factory :user do
    name { "John Doe" }
    email { "john@example.com" }
    age { 30 }
    
    trait :admin do
      role { "admin" }
    end
    
    trait :underage do
      age { 17 }
    end
    
    factory :admin_user, parent: :user, traits: [:admin]
  end
  
  factory :product do
    name { "Product Name" }
    price { 99.99 }
    description { "Product description" }
    
    trait :expensive do
      price { 999.99 }
    end
    
    trait :cheap do
      price { 9.99 }
    end
  end
  
  factory :order do
    user
    status { "pending" }
    
    after(:build) do |order|
      order.items << build(:item) if order.items.empty?
    end
    
    after(:create) do |order|
      order.items.each(&:save!)
    end
  end
end
```

### Factory Usage
```ruby
# RSpec usage
RSpec.describe User do
  let(:user) { build(:user) }
  let(:admin_user) { build(:user, :admin) }
  let(:created_user) { create(:user) }
  
  it "has a name" do
    expect(user.name).to eq("John Doe")
  end
  
  it "can be an admin" do
    expect(admin_user.role).to eq("admin")
  end
  
  it "is saved to database" do
    created_user
    expect(User.count).to eq(1)
  end
end

# Minitest usage
class UserTest < Minitest::Test
  def setup
    @user = build(:user)
    @admin_user = build(:user, :admin)
    @created_user = create(:user)
  end
  
  def test_user_has_name
    assert_equal "John Doe", @user.name
  end
  
  def test_admin_user_has_admin_role
    assert_equal "admin", @admin_user.role
  end
  
  def test_user_is_saved_to_database
    assert_equal 1, User.count
  end
end

# Custom attributes
user = build(:user, name: "Jane Doe", age: 25)
user = build(:user, :admin, name: "Admin User")

# Associations
order = build(:order, user: user)
order = create(:order, user: user)
```

## Database Testing

### Database Cleaner
```ruby
# spec/spec_helper.rb
require 'database_cleaner'

RSpec.configure do |config|
  config.use_transactional_fixtures = false
  
  config.before(:suite) do
    DatabaseCleaner.clean_with(:truncation)
  end
  
  config.before(:each) do
    DatabaseCleaner.strategy = :transaction
  end
  
  config.before(:each, js: true) do
    DatabaseCleaner.strategy = :truncation
  end
  
  config.before(:each) do
    DatabaseCleaner.start
  end
  
  config.after(:each) do
    DatabaseCleaner.clean
  end
end
```

### Testing Models
```ruby
# spec/models/user_spec.rb
RSpec.describe User, type: :model do
  # Validation tests
  it { should validate_presence_of(:name) }
  it { should validate_presence_of(:email) }
  it { should validate_uniqueness_of(:email) }
  it { should validate_numericality_of(:age).is_greater_than_or_equal_to(0) }
  
  # Association tests
  it { should have_many(:orders) }
  it { should belong_to(:company) }
  
  # Custom validation tests
  it "validates email format" do
    user = build(:user, email: "invalid_email")
    expect(user).not_to be_valid
  end
  
  # Custom method tests
  describe "#admin?" do
    it "returns true for admin users" do
      user = build(:user, :admin)
      expect(user.admin?).to be true
    end
    
    it "returns false for regular users" do
      user = build(:user)
      expect(user.admin?).to be false
    end
  end
  
  # Scope tests
  describe ".adults" do
    it "returns users 18 and older" do
      adult = create(:user, age: 25)
      child = create(:user, age: 15)
      
      adults = User.adults
      
      expect(adults).to include(adult)
      expect(adults).not_to include(child)
    end
  end
  
  # Callback tests
  describe "#send_welcome_email" do
    it "sends email after creation" do
      user = build(:user)
      
      expect(user).to receive(:send_welcome_email)
      
      user.save!
    end
  end
end
```

### Testing Controllers
```ruby
# spec/controllers/users_controller_spec.rb
RSpec.describe UsersController, type: :controller do
  let(:user) { create(:user) }
  
  # Index action
  describe "GET #index" do
    it "returns a successful response" do
      get :index
      
      expect(response).to have_http_status(:ok)
      expect(response).to render_template(:index)
    end
    
    it "assigns all users" do
      users = create_list(:user, 3)
      
      get :index
      
      expect(assigns(:users)).to match_array(users)
    end
  end
  
  # Show action
  describe "GET #show" do
    it "returns a successful response" do
      get :show, params: { id: user.id }
      
      expect(response).to have_http_status(:ok)
      expect(response).to render_template(:show)
    end
    
    it "assigns the requested user" do
      get :show, params: { id: user.id }
      
      expect(assigns(:user)).to eq(user)
    end
    
    it "returns not found for non-existent user" do
      get :show, params: { id: 999 }
      
      expect(response).to have_http_status(:not_found)
    end
  end
  
  # Create action
  describe "POST #create" do
    context "with valid params" do
      it "creates a new user" do
        expect {
          post :create, params: { user: attributes_for(:user) }
        }.to change(User, :count).by(1)
      end
      
      it "redirects to the created user" do
        post :create, params: { user: attributes_for(:user) }
        
        expect(response).to redirect_to(user_path(User.last))
      end
    end
    
    context "with invalid params" do
      it "does not create a new user" do
        expect {
          post :create, params: { user: attributes_for(:user, name: nil) }
        }.not_to change(User, :count)
      end
      
      it "renders the new template" do
        post :create, params: { user: attributes_for(:user, name: nil) }
        
        expect(response).to render_template(:new)
      end
    end
  end
end
```

## Integration Testing

### Feature Testing
```ruby
# spec/features/user_registration_spec.rb
RSpec.feature "User Registration", type: :feature do
  scenario "User registers successfully" do
    visit new_user_registration_path
    
    fill_in "Name", with: "John Doe"
    fill_in "Email", with: "john@example.com"
    fill_in "Password", with: "password123"
    fill_in "Password confirmation", with: "password123"
    
    click_button "Sign up"
    
    expect(page).to have_content("Welcome! You have signed up successfully.")
    expect(page).to have_current_path(root_path)
  end
  
  scenario "User fails to register with invalid data" do
    visit new_user_registration_path
    
    fill_in "Name", with: ""
    fill_in "Email", with: "invalid_email"
    fill_in "Password", with: "123"
    fill_in "Password confirmation", with: "456"
    
    click_button "Sign up"
    
    expect(page).to have_content("Name can't be blank")
    expect(page).to have_content("Email is invalid")
    expect(page).to have_content("Password confirmation doesn't match Password")
  end
end
```

### Request Testing
```ruby
# spec/requests/api/users_spec.rb
RSpec.describe "Users API", type: :request do
  let(:user) { create(:user) }
  
  describe "GET /api/users" do
    it "returns a list of users" do
      user
      get "/api/users"
      
      expect(response).to have_http_status(:ok)
      expect(JSON.parse(response.body)).to include("id" => user.id)
    end
  end
  
  describe "POST /api/users" do
    context "with valid params" do
      it "creates a new user" do
        params = { user: attributes_for(:user) }
        
        post "/api/users", params: params
        
        expect(response).to have_http_status(:created)
        expect(JSON.parse(response.body)).to include("name" => params[:user][:name])
      end
    end
    
    context "with invalid params" do
      it "returns errors" do
        params = { user: attributes_for(:user, name: nil) }
        
        post "/api/users", params: params
        
        expect(response).to have_http_status(:unprocessable_entity)
        expect(JSON.parse(response.body)).to include("errors")
      end
    end
  end
end
```

## Performance Testing

### Benchmark Testing
```ruby
# spec/performance/calculator_performance_spec.rb
RSpec.describe "Calculator Performance", type: :performance do
  let(:calculator) { Calculator.new }
  
  it "performs addition quickly" do
    time = Benchmark.realtime do
      1000.times { calculator.add(rand(100), rand(100)) }
    end
    
    expect(time).to be < 0.1  # Should complete in less than 100ms
  end
  
  it "handles large numbers efficiently" do
    time = Benchmark.realtime do
      100.times { calculator.add(10**10, 10**10) }
    end
    
    expect(time).to be < 0.05
  end
end
```

### Memory Testing
```ruby
# spec/performance/memory_spec.rb
RSpec.describe "Memory Usage", type: :performance do
  it "doesn't leak memory when creating objects" do
    require 'memory_profiler'
    
    report = MemoryProfiler.report do
      1000.times { User.new(name: "Test", email: "test@example.com") }
    end
    
    expect(report.total_allocated_memsize).to be < 10_000_000  # Less than 10MB
  end
  
  it "frees memory after garbage collection" do
    objects = []
    1000.times { objects << User.new(name: "Test") }
    
    objects = nil
    GC.start
    
    # Check that memory was freed
    expect(ObjectSpace.count_objects[:TOTAL]).to be < 100_000
  end
end
```

## Test Coverage

### SimpleCov Setup
```ruby
# spec/spec_helper.rb
require 'simplecov'

SimpleCov.start 'rails' do
  add_filter '/bin/'
  add_filter '/db/'
  add_filter '/spec/'
  add_filter '/config/'
  add_filter '/vendor/'
  
  add_group 'Models', 'app/models'
  add_group 'Controllers', 'app/controllers'
  add_group 'Helpers', 'app/helpers'
  add_group 'Libraries', 'app/lib'
  
  minimum_coverage 90
  minimum_coverage_by_file 80
end

SimpleCov.at_exit do
  SimpleCov.result.format!
  
  if SimpleCov.result.covered_percent < SimpleCov.minimum_coverage
    puts "Coverage below minimum threshold"
    exit 1
  end
end
```

### Coverage Analysis
```ruby
# Run coverage
# Command: rspec --format documentation

# Generate coverage report
# Command: open coverage/index.html

# Coverage thresholds
# Set minimum coverage requirements
# Fail build if coverage is too low

# Coverage by file
# Identify files with low coverage
# Focus testing efforts on critical areas
```

## Best Practices

### Test Organization
```ruby
# Good test structure
spec/
├── factories/
│   └── users.rb
├── models/
│   └── user_spec.rb
├── controllers/
│   └── users_controller_spec.rb
├── features/
│   └── user_registration_spec.rb
├── requests/
│   └── api/
│       └── users_spec.rb
├── support/
│   ├── factory_bot.rb
│   └── database_cleaner.rb
├── spec_helper.rb
└── rails_helper.rb

# Good test naming
RSpec.describe User do
  describe "#admin?" do
    context "when user is admin" do
      it "returns true" do
        # Test implementation
      end
    end
    
    context "when user is not admin" do
      it "returns false" do
        # Test implementation
      end
    end
  end
end
```

### Test Data Management
```ruby
# Use factories instead of fixtures
# Factories are more flexible and maintainable

# Use traits for variations
factory :user do
  name { "John Doe" }
  email { "john@example.com" }
  
  trait :admin do
    role { "admin" }
  end
  
  trait :underage do
    age { 17 }
  end
end

# Use let for test data
let(:user) { build(:user) }
let(:admin_user) { build(:user, :admin) }

# Use create when database persistence is needed
let(:persisted_user) { create(:user) }
```

### Test Isolation
```ruby
# Keep tests independent
# Don't rely on test order
# Clean up after each test

# Use database_cleaner for database cleanup
DatabaseCleaner.strategy = :transaction
DatabaseCleaner.cleaning do
  # Test code here
end

# Use mocks and stubs to isolate dependencies
allow(PaymentGateway).to receive(:charge).and_return(true)

# Use context for setup
context "when user is admin" do
  let(:user) { build(:user, :admin) }
  
  it "can access admin features" do
    # Test implementation
  end
end
```

## Common Pitfalls

### Test Smells
```ruby
# Bad: Tests too many things
it "creates user, sends email, and updates cache" do
  # Too many assertions
end

# Good: One test, one assertion
it "creates user" do
  # Test user creation
end

it "sends welcome email" do
  # Test email sending
end

it "updates cache" do
  # Test cache update
end

# Bad: Test depends on implementation details
it "calls the User model" do
  expect(User).to receive(:new)
end

# Good: Test behavior, not implementation
it "creates a new user" do
  expect { post :create, params: { user: attributes } }.to change(User, :count)
end

# Bad: Brittle tests that break with refactoring
it "uses the correct SQL query" do
  expect(User).to receive(:where).with("email = ?", email)
end

# Good: Test behavior
it "finds user by email" do
  user = create(:user, email: email)
  found_user = User.find_by_email(email)
  expect(found_user).to eq(user)
end
```

### Mocking Issues
```ruby
# Bad: Over-mocking
it "creates user" do
  allow(User).to receive(:new).and_return(user_double)
  allow(user_double).to receive(:save).and_return(true)
  allow(user_double).to receive(:name).and_return("John")
  allow(user_double).to receive(:email).and_return("john@example.com")
  
  result = service.create_user(name: "John", email: "john@example.com")
  expect(result).to be true
end

# Good: Mock only external dependencies
it "sends email when user is created" do
  allow(User).to receive(:new).and_return(user)
  allow(user).to receive(:save).and_return(true)
  expect(UserMailer).to receive(:welcome_email).with(user).and_return(double(deliver_now: true))
  
  service.create_user(name: "John", email: "john@example.com")
end

# Bad: Mocking the class under test
it "calculates total" do
  allow(calculator).to receive(:add).and_return(5)
  result = calculator.add(2, 3)
  expect(result).to eq(5)
end

# Good: Test the actual implementation
it "calculates total" do
  calculator = Calculator.new
  result = calculator.add(2, 3)
  expect(result).to eq(5)
end
```

### Performance Issues
```ruby
# Bad: Creating too many objects in tests
it "processes 1000 users" do
  users = 1000.times.map { create(:user) }
  result = service.process_users(users)
  expect(result).to be true
end

# Good: Use fewer objects or mocks
it "processes users" do
  users = create_list(:user, 3)
  result = service.process_users(users)
  expect(result).to be true
end

# Bad: Not cleaning up database
it "creates user" do
  create(:user)  # User remains in database
  expect(User.count).to eq(1)
end

# Good: Clean up after tests
it "creates user" do
  expect { create(:user) }.to change(User, :count).by(1)
end
```

## Summary

Ruby testing provides:

**Testing Frameworks:**
- RSpec with expressive syntax and rich matchers
- Minitest with simple, fast testing
- Both support test doubles and mocking

**Test Organization:**
- Unit tests for individual components
- Integration tests for component interaction
- Feature tests for user behavior
- Request tests for API endpoints

**Test Doubles:**
- Mocks for verifying interactions
- Stubs for controlling behavior
- Factories for test data generation
- Database cleaning for isolation

**Testing Patterns:**
- Arrange-Act-Assert pattern
- Given-When-Then for BDD
- Context blocks for setup
- Descriptive test naming

**Performance Testing:**
- Benchmark testing for speed
- Memory testing for leaks
- Coverage analysis for completeness
- Continuous integration for automation

**Best Practices:**
- Test isolation and independence
- One assertion per test
- Test behavior, not implementation
- Use factories for test data
- Clean up after tests

**Common Pitfalls:**
- Test smells and brittleness
- Over-mocking and under-testing
- Performance issues
- Database contamination
- Poor test organization

Ruby's testing ecosystem provides comprehensive tools for ensuring code quality, enabling developers to write reliable, maintainable applications with confidence.
