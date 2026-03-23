# Advanced Testing Strategies in Ruby

## Overview

This guide covers advanced testing strategies including test automation, performance testing, security testing, contract testing, and comprehensive test architectures in Ruby applications.

## Test Architecture

### Test Pyramid Implementation

```ruby
# test/test_helper.rb
require 'minitest/autorun'
require 'minitest/pride'
require 'database_cleaner'
require 'factory_bot'
require 'webmock'
require 'vcr'

class Minitest::Test
  include FactoryBot::Syntax::Methods
  
  def setup
    DatabaseCleaner.start
    WebMock.disable_net_connect!(allow_localhost: true)
  end
  
  def teardown
    DatabaseCleaner.clean
  end
end

# spec/spec_helper.rb
RSpec.configure do |config|
  config.include FactoryBot::Syntax::Methods
  
  config.before(:suite) do
    DatabaseCleaner.clean_with(:truncation)
  end
  
  config.before(:each) do
    DatabaseCleaner.strategy = :transaction
    DatabaseCleaner.start
  end
  
  config.after(:each) do
    DatabaseCleaner.clean
  end
end
```

### Test Categories

```ruby
# test/unit/user_test.rb
class UserTest < Minitest::Test
  def test_user_creation
    user = User.new(name: "John", email: "john@example.com")
    assert user.valid?
    assert_equal "John", user.name
  end
  
  def test_user_validation
    user = User.new(email: "invalid-email")
    assert_not user.valid?
    assert_includes user.errors[:email], "is invalid"
  end
end

# test/integration/user_flow_test.rb
class UserFlowTest < ActionDispatch::IntegrationTest
  def test_user_registration_flow
    visit new_user_registration_path
    
    fill_in 'Name', with: 'John Doe'
    fill_in 'Email', with: 'john@example.com'
    fill_in 'Password', with: 'password123'
    fill_in 'Password confirmation', with: 'password123'
    
    click_button 'Sign up'
    
    assert_text 'Welcome! You have signed up successfully.'
    assert_current_path root_path
  end
end

# test/system/user_system_test.rb
class UserSystemTest < ApplicationSystemTestCase
  def test_user_can_create_post
    user = create(:user)
    login_as(user)
    
    visit new_post_path
    
    fill_in 'Title', with: 'My First Post'
    fill_in 'Content', with: 'This is my first post content.'
    
    click_button 'Create Post'
    
    assert_text 'Post was successfully created.'
    assert_text 'My First Post'
  end
end
```

## Test Data Management

### Factory Bot

```ruby
# spec/factories/users.rb
FactoryBot.define do
  factory :user do
    name { "John Doe" }
    sequence(:email) { |n| "john#{n}@example.com" }
    password { "password123" }
    confirmed_at { Time.current }
    
    trait :admin do
      role { 'admin' }
    end
    
    trait :unconfirmed do
      confirmed_at { nil }
    end
    
    trait :with_posts do
      after(:create) do |user|
        create_list(:post, 3, user: user)
      end
    end
  end
  
  factory :post do
    title { "Sample Post" }
    content { "This is sample content." }
    published { true }
    association :user
    
    trait :draft do
      published { false }
    end
    
    trait :with_comments do
      after(:create) do |post|
        create_list(:comment, 2, post: post)
      end
    end
  end
end

# Usage in tests
user = create(:user, :admin, :with_posts)
posts = create_list(:post, 5, :draft, user: user)
```

### Test Fixtures

```ruby
# test/fixtures/users.yml
john:
  name: John Doe
  email: john@example.com
  encrypted_password: <%= User.new.send(:password_digest, 'password') %>
  created_at: <%= Time.current %>

jane:
  name: Jane Smith
  email: jane@example.com
  encrypted_password: <%= User.new.send(:password_digest, 'password') %>
  created_at: <%= Time.current %>

# test/fixtures/posts.yml
post_one:
  title: First Post
  content: This is the first post content.
  user: john
  published: true
  created_at: <%= Time.current %>

post_two:
  title: Second Post
  content: This is the second post content.
  user: jane
  published: false
  created_at: <%= Time.current %>
```

### Test Builders

```ruby
class UserBuilder
  def initialize
    @attributes = {}
  end
  
  def with_name(name)
    @attributes[:name] = name
    self
  end
  
  def with_email(email)
    @attributes[:email] = email
    self
  end
  
  def as_admin
    @attributes[:role] = 'admin'
    self
  end
  
  def with_posts(count = 3)
    @posts_count = count
    self
  end
  
  def build
    user = User.new(@attributes)
    
    if @posts_count
      @posts_count.times { user.posts.build(title: "Post #{user.posts.length + 1}") }
    end
    
    user
  end
  
  def create!
    user = build
    user.save!
    user
  end
end

# Usage
user = UserBuilder.new
  .with_name("John Doe")
  .with_email("john@example.com")
  .as_admin
  .with_posts(5)
  .create!
```

## Mocking and Stubbing

### WebMock for HTTP Requests

```ruby
require 'webmock/rspec'

RSpec.configure do |config|
  config.before(:each, :webmock) do
    WebMock.enable!
  end
  
  config.after(:each, :webmock) do
    WebMock.reset!
  end
end

# Example usage
RSpec.describe ExternalAPIService do
  let(:service) { ExternalAPIService.new }
  
  before do
    stub_request(:get, "https://api.example.com/users/1")
      .to_return(
        status: 200,
        body: { id: 1, name: "John Doe" }.to_json,
        headers: { 'Content-Type' => 'application/json' }
      )
  end
  
  it "fetches user data" do
    user = service.get_user(1)
    
    expect(user['name']).to eq('John Doe')
    expect(WebMock).to have_requested(:get, "https://api.example.com/users/1")
  end
  
  it "handles API errors" do
    stub_request(:get, "https://api.example.com/users/999")
      .to_return(status: 404, body: { error: "User not found" }.to_json)
    
    expect { service.get_user(999) }.to raise_error(ExternalAPI::NotFoundError)
  end
end
```

### VCR for Recording HTTP Interactions

```ruby
require 'vcr'

VCR.configure do |config|
  config.cassette_library_dir = 'spec/cassettes'
  config.hook_into :webmock
  config.configure_rspec_metadata!
  config.default_cassette_options = {
    record: :once,
    match_requests_on: [:method, :uri, :body]
  }
end

RSpec.configure do |config|
  config.around(:each, :vcr) do |example|
    name = example.metadata[:full_description].gsub(/\W/, '_')
    VCR.use_cassette(name, &example)
  end
end

# Usage
RSpec.describe ExternalAPIService, :vcr do
  it "fetches user data from real API" do
    user = service.get_user(1)
    expect(user['name']).to be_present
  end
end
```

### Custom Mock Objects

```ruby
class PaymentServiceMock
  def initialize
    @transactions = []
    @should_fail = false
    @failure_reason = nil
  end
  
  def process_payment(amount, token)
    if @should_fail
      raise PaymentError, @failure_reason
    end
    
    transaction = {
      id: SecureRandom.uuid,
      amount: amount,
      status: 'success',
      created_at: Time.current
    }
    
    @transactions << transaction
    transaction
  end
  
  def should_fail_with(reason)
    @should_fail = true
    @failure_reason = reason
  end
  
  def reset
    @transactions = []
    @should_fail = false
    @failure_reason = nil
  end
  
  def transactions
    @transactions
  end
end

# Usage in tests
RSpec.describe OrderProcessor do
  let(:payment_service) { PaymentServiceMock.new }
  let(:processor) { OrderProcessor.new(payment_service: payment_service) }
  
  it "processes successful payment" do
    order = create(:order, total: 100.00)
    
    result = processor.process(order)
    
    expect(result[:success]).to be true
    expect(payment_service.transactions.length).to eq(1)
  end
  
  it "handles payment failures" do
    payment_service.should_fail_with("Insufficient funds")
    order = create(:order, total: 100.00)
    
    result = processor.process(order)
    
    expect(result[:success]).to be false
    expect(result[:error]).to eq("Insufficient funds")
  end
end
```

## Performance Testing

### Load Testing with Concurrent Requests

```ruby
class LoadTester
  def initialize(base_url, concurrency = 10, duration = 60)
    @base_url = base_url
    @concurrency = concurrency
    @duration = duration
    @results = []
    @mutex = Mutex.new
  end
  
  def run_test(endpoint)
    puts "Starting load test: #{@concurrency} concurrent users for #{@duration}s"
    
    threads = []
    start_time = Time.current
    
    @concurrency.times do |i|
      threads << Thread.new do
        run_user_simulation(endpoint, start_time)
      end
    end
    
    threads.each(&:join)
    
    analyze_results
  end
  
  private
  
  def run_user_simulation(endpoint, start_time)
    user_id = Thread.current.object_id
    
    while Time.current - start_time < @duration
      request_start = Time.current
      
      begin
        response = make_request(endpoint)
        request_time = Time.current - request_start
        
        @mutex.synchronize do
          @results << {
            user_id: user_id,
            status: response.code,
            response_time: request_time,
            timestamp: request_start
          }
        end
      rescue => e
        @mutex.synchronize do
          @results << {
            user_id: user_id,
            status: 'error',
            response_time: Time.current - request_start,
            timestamp: request_start,
            error: e.message
          }
        end
      end
      
      sleep(rand * 2)  # Random delay between requests
    end
  end
  
  def make_request(endpoint)
    uri = URI("#{@base_url}#{endpoint}")
    
    Net::HTTP.start(uri.host, uri.port, use_ssl: uri.scheme == 'https') do |http|
      request = Net::HTTP::Get.new(uri)
      http.request(request)
    end
  end
  
  def analyze_results
    return if @results.empty?
    
    total_requests = @results.length
    successful_requests = @results.count { |r| r[:status] != 'error' }
    error_requests = total_requests - successful_requests
    
    response_times = @results.map { |r| r[:response_time] }
    avg_response_time = response_times.sum / response_times.length
    min_response_time = response_times.min
    max_response_time = response_times.max
    
    p95_response_time = percentile(response_times.sort, 95)
    p99_response_time = percentile(response_times.sort, 99)
    
    requests_per_second = total_requests / @duration
    
    puts "\nLoad Test Results:"
    puts "=================="
    puts "Total Requests: #{total_requests}"
    puts "Successful: #{successful_requests} (#{(successful_requests.to_f / total_requests * 100).round(2)}%)"
    puts "Errors: #{error_requests} (#{(error_requests.to_f / total_requests * 100).round(2)}%)"
    puts "Requests/sec: #{requests_per_second.round(2)}"
    puts "Avg Response Time: #{(avg_response_time * 1000).round(2)}ms"
    puts "Min Response Time: #{(min_response_time * 1000).round(2)}ms"
    puts "Max Response Time: #{(max_response_time * 1000).round(2)}ms"
    puts "95th Percentile: #{(p95_response_time * 1000).round(2)}ms"
    puts "99th Percentile: #{(p99_response_time * 1000).round(2)}ms"
  end
  
  def percentile(sorted_array, percentile)
    index = (percentile / 100.0 * (sorted_array.length - 1)).round
    sorted_array[index]
  end
end

# Usage
load_tester = LoadTester.new('http://localhost:3000', 20, 30)
load_tester.run_test('/api/posts')
```

### Memory Profiling

```ruby
require 'memory_profiler'

class MemoryProfiler
  def self.profile_method(method_name, &block)
    puts "Profiling memory usage for #{method_name}..."
    
    report = MemoryProfiler.report do
      block.call
    end
    
    puts "\nMemory Profile for #{method_name}:"
    puts "==============================="
    puts "Total allocated: #{report.total_allocated_memsize} bytes"
    puts "Total retained: #{report.total_retained_memsize} bytes"
    puts "Allocated objects: #{report.total_allocated}"
    puts "Retained objects: #{report.total_retained}"
    
    puts "\nTop 10 memory allocations:"
    report.allocated_memory_top10.each do |allocation|
      puts "  #{allocation[:file]}:#{allocation[:line]} - #{allocation[:class]} (#{allocation[:memsize]} bytes)"
    end
    
    report
  end
end

# Usage
RSpec.describe MemoryIntensiveOperation do
  it "doesn't leak memory" do
    report = MemoryProfiler.profile_method('process_large_dataset') do
      processor.process_large_dataset
    end
    
    # Assert memory usage is reasonable
    expect(report.total_allocated_memsize).to be < 100_000_000  # 100MB
    expect(report.total_retained_memsize).to be < 10_000_000   # 10MB
  end
end
```

## Security Testing

### SQL Injection Testing

```ruby
class SecurityTestHelper
  def self.test_sql_injection(model, field, payloads)
    vulnerabilities = []
    
    payloads.each do |payload|
      begin
        # Try to inject SQL
        test_instance = model.new(field => payload)
        
        if test_instance.save
          # Check if SQL was executed by looking for unexpected behavior
          if sql_injection_successful?(test_instance, payload)
            vulnerabilities << {
              field: field,
              payload: payload,
              severity: 'high'
            }
          end
        end
      rescue => e
        # SQL errors might indicate successful injection
        if e.message.match?(/SQL|syntax|error/i)
          vulnerabilities << {
            field: field,
            payload: payload,
            severity: 'critical',
            error: e.message
          }
        end
      end
    end
    
    vulnerabilities
  end
  
  def self.test_xss(model, field, payloads)
    vulnerabilities = []
    
    payloads.each do |payload|
      test_instance = model.new(field => payload)
      
      if test_instance.save
        # Check if XSS payload is stored without sanitization
        if xss_successful?(test_instance, field, payload)
          vulnerabilities << {
            field: field,
            payload: payload,
            severity: 'high'
          }
        end
      end
    end
    
    vulnerabilities
  end
  
  private
  
  def self.sql_injection_successful?(instance, payload)
    # Check for unexpected data or behavior
    # This would need to be customized based on your application
    false
  end
  
  def self.xss_successful?(instance, field, payload)
    # Check if script tags are preserved
    value = instance.send(field)
    value.include?('<script>') || value.include?('javascript:')
  end
end

# Usage
RSpec.describe User do
  it "is protected against SQL injection" do
    sql_payloads = [
      "'; DROP TABLE users; --",
      "' OR '1'='1",
      "'; SELECT * FROM users --"
    ]
    
    vulnerabilities = SecurityTestHelper.test_sql_injection(User, :email, sql_payloads)
    
    expect(vulnerabilities).to be_empty, "SQL injection vulnerabilities found: #{vulnerabilities}"
  end
  
  it "is protected against XSS" do
    xss_payloads = [
      "<script>alert('xss')</script>",
      "<img src=x onerror=alert('xss')>",
      "javascript:alert('xss')"
    ]
    
    vulnerabilities = SecurityTestHelper.test_xss(User, :name, xss_payloads)
    
    expect(vulnerabilities).to be_empty, "XSS vulnerabilities found: #{vulnerabilities}"
  end
end
```

### Authentication Security Testing

```ruby
class AuthenticationSecurityTest
  def initialize(auth_service)
    @auth_service = auth_service
  end
  
  def test_weak_passwords
    weak_passwords = [
      'password',
      '123456',
      'qwerty',
      'admin',
      'letmein',
      'welcome'
    ]
    
    vulnerabilities = []
    
    weak_passwords.each do |password|
      if @auth_service.authenticate('test@example.com', password)
        vulnerabilities << {
          type: 'weak_password',
          password: password,
          severity: 'medium'
        }
      end
    end
    
    vulnerabilities
  end
  
  def test_brute_force_protection(email, max_attempts = 10)
    attempts = 0
    
    begin
      max_attempts.times do |i|
        attempts = i + 1
        @auth_service.authenticate(email, "wrong_password_#{i}")
      end
    rescue => e
      # Account should be locked after too many attempts
      return {
        protected: true,
        attempts: attempts,
        error: e.message
      }
    end
    
    {
      protected: false,
      attempts: attempts
    }
  end
  
  def test_session_hijacking
    # Test session fixation
    session_id = @auth_service.generate_session_id
    
    # Simulate session hijacking
    hijacked_session = @auth_service.validate_session(session_id)
    
    {
      session_fixation_vulnerable: hijacked_session[:user_id] != nil,
      session_id: session_id
    }
  end
end

# Usage
RSpec.describe AuthenticationService do
  let(:auth_service) { AuthenticationService.new }
  let(:security_test) { AuthenticationSecurityTest.new(auth_service) }
  
  it "rejects weak passwords" do
    vulnerabilities = security_test.test_weak_passwords
    
    expect(vulnerabilities).to be_empty, "Weak password vulnerabilities found: #{vulnerabilities}"
  end
  
  it "protects against brute force attacks" do
    result = security_test.test_brute_force_protection('test@example.com', 5)
    
    expect(result[:protected]).to be true, "Brute force protection not working"
  end
  
  it "prevents session hijacking" do
    result = security_test.test_session_hijacking
    
    expect(result[:session_fixation_vulnerable]).to be false, "Session fixation vulnerability found"
  end
end
```

## Contract Testing

### Pact Contract Testing

```ruby
# spec/contracts/user_service_consumer_spec.rb
require 'pact/consumer/rspec'

Pact.configure do |config|
  config.reports_dir = 'spec/pacts'
end

Pact.service_consumer "User API Consumer" do
  has_pact_with "User API Provider" do
    mock_service :user_api do
      port 1234
      
      upon_receiving "a request for a user" do
        with(method: :get, path: "/users/1", headers: { "Accept" => "application/json" })
        
        will_respond_with(
          status: 200,
          headers: { "Content-Type" => "application/json" },
          body: {
            id: 1,
            name: "John Doe",
            email: "john@example.com"
          }
        )
      end
      
      upon_receiving "a request to create a user" do
        with(method: :post, 
             path: "/users",
             headers: { "Content-Type" => "application/json" },
             body: {
               name: "Jane Doe",
               email: "jane@example.com"
             })
        
        will_respond_with(
          status: 201,
          headers: { "Content-Type" => "application/json" },
          body: {
            id: 2,
            name: "Jane Doe",
            email: "jane@example.com"
          }
        )
      end
    end
  end
end

RSpec.describe "User API Consumer", :pact => true do
  before do
    UserServiceClient.base_uri "http://localhost:1234"
  end
  
  it "can get a user" do
    user = UserServiceClient.get_user(1)
    
    expect(user['id']).to eq(1)
    expect(user['name']).to eq("John Doe")
    expect(user['email']).to eq("john@example.com")
  end
  
  it "can create a user" do
    user_data = { name: "Jane Doe", email: "jane@example.com" }
    user = UserServiceClient.create_user(user_data)
    
    expect(user['id']).to eq(2)
    expect(user['name']).to eq("Jane Doe")
    expect(user['email']).to eq("jane@example.com")
  end
end
```

### Provider Contract Verification

```ruby
# spec/contracts/user_service_provider_spec.rb
require 'pact/provider/rspec'

Pact.service_provider "User API Provider" do
  app_version "1.0.0"
  
  honours_pact_with "User API Consumer" do
    pact_uri "http://pact-broker/pacts/consumer-user-api-provider/latest"
    
    app do
      # Load your Rails application
      load Rails.root.join('config/environment.rb')
    end
  end
end

Pact.configure do |config|
  config.verifier_ignore_path "/health"
  config.verifier_ignore_path "/metrics"
end

RSpec.describe "User API Provider" do
  before do
    # Set up test data
    allow(User).to receive(:find).with(1).and_return(
      User.new(id: 1, name: "John Doe", email: "john@example.com")
    )
    
    allow(User).to receive(:create!).and_return(
      User.new(id: 2, name: "Jane Doe", email: "jane@example.com")
    )
  end
  
  it "verifies the GET user pact" do
    get "/users/1"
    
    expect(last_response.status).to eq(200)
    expect(last_response.headers["Content-Type"]).to eq("application/json")
    
    user_data = JSON.parse(last_response.body)
    expect(user_data["id"]).to eq(1)
    expect(user_data["name"]).to eq("John Doe")
    expect(user_data["email"]).to eq("john@example.com")
  end
  
  it "verifies the POST user pact" do
    post "/users", {
      name: "Jane Doe",
      email: "jane@example.com"
    }.to_json, { "CONTENT_TYPE" => "application/json" }
    
    expect(last_response.status).to eq(201)
    expect(last_response.headers["Content-Type"]).to eq("application/json")
    
    user_data = JSON.parse(last_response.body)
    expect(user_data["id"]).to eq(2)
    expect(user_data["name"]).to eq("Jane Doe")
    expect(user_data["email"]).to eq("jane@example.com")
  end
end
```

## Test Automation

### CI/CD Integration

```ruby
# lib/test_automation.rb
class TestAutomation
  def initialize
    @test_suites = []
    @results = {}
    @reports = []
  end
  
  def add_test_suite(name, &block)
    @test_suites << {
      name: name,
      block: block,
      status: :pending
    }
  end
  
  def run_all_tests
    puts "Starting automated test run..."
    
    @test_suites.each do |suite|
      run_test_suite(suite)
    end
    
    generate_report
    notify_results
  end
  
  def run_test_suite(suite)
    puts "Running test suite: #{suite[:name]}"
    
    start_time = Time.current
    
    begin
      result = suite[:block].call
      
      suite[:status] = :passed
      suite[:duration] = Time.current - start_time
      suite[:result] = result
      
      puts "✅ #{suite[:name]} passed (#{suite[:duration].round(2)}s)"
    rescue => e
      suite[:status] = :failed
      suite[:duration] = Time.current - start_time
      suite[:error] = e.message
      
      puts "❌ #{suite[:name]} failed: #{e.message}"
    end
    
    @results[suite[:name]] = suite
  end
  
  def generate_report
    report = {
      timestamp: Time.current,
      total_suites: @test_suites.length,
      passed: @test_suites.count { |s| s[:status] == :passed },
      failed: @test_suites.count { |s| s[:status] == :failed },
      total_duration: @test_suites.sum { |s| s[:duration] || 0 },
      suites: @results
    }
    
    @reports << report
    save_report(report)
    
    report
  end
  
  def notify_results
    report = @reports.last
    
    # Send to Slack
    send_slack_notification(report)
    
    # Send to email
    send_email_notification(report)
    
    # Update dashboard
    update_dashboard(report)
  end
  
  private
  
  def save_report(report)
    filename = "test_report_#{report[:timestamp].strftime('%Y%m%d_%H%M%S')}.json"
    File.write("reports/#{filename}", report.to_json)
  end
  
  def send_slack_notification(report)
    # Slack notification implementation
    puts "Slack notification sent: #{report[:passed]}/#{report[:total_suites]} tests passed"
  end
  
  def send_email_notification(report)
    # Email notification implementation
    puts "Email notification sent to team"
  end
  
  def update_dashboard(report)
    # Dashboard update implementation
    puts "Dashboard updated with latest results"
  end
end

# Usage
automation = TestAutomation.new

automation.add_test_suite("Unit Tests") do
  # Run unit tests
  system("bundle exec rake test:units")
  $?.success?
end

automation.add_test_suite("Integration Tests") do
  # Run integration tests
  system("bundle exec rake test:integration")
  $?.success?
end

automation.add_test_suite("System Tests") do
  # Run system tests
  system("bundle exec rake test:system")
  $?.success?
end

automation.add_test_suite("Security Tests") do
  # Run security tests
  system("bundle exec rake test:security")
  $?.success?
end

# Run all tests
automation.run_all_tests
```

### Test Data Generation

```ruby
class TestDataGenerator
  def initialize
    @generators = {}
  end
  
  def register_generator(name, &block)
    @generators[name] = block
  end
  
  def generate(type, options = {})
    generator = @generators[type]
    raise "No generator for type: #{type}" unless generator
    
    generator.call(options)
  end
  
  def generate_batch(type, count, options = {})
    (1..count).map { generate(type, options.merge(index: count)) }
  end
  
  # Built-in generators
  def self.setup_defaults(generator)
    generator.register_generator(:user) do |options|
      {
        name: Faker::Name.name,
        email: Faker::Internet.email,
        age: rand(18..80),
        city: Faker::Address.city,
        created_at: Time.current
      }.merge(options[:overrides] || {})
    end
    
    generator.register_generator(:post) do |options|
      {
        title: Faker::Lorem.sentence,
        content: Faker::Lorem.paragraph,
        published: [true, false].sample,
        created_at: Time.current - rand(1..365).days
      }.merge(options[:overrides] || {})
    end
    
    generator.register_generator(:order) do |options|
      {
        total: rand(10..1000) + rand.round(2),
        status: %w[pending processing shipped delivered].sample,
        created_at: Time.current - rand(1..30).days
      }.merge(options[:overrides] || {})
    end
  end
end

# Usage
generator = TestDataGenerator.new
TestDataGenerator.setup_defaults(generator)

# Generate single record
user = generator.generate(:user, overrides: { name: "John Doe" })

# Generate batch records
posts = generator.generate_batch(:post, 100, overrides: { published: true })
```

## Best Practices

### 1. Test Organization

```ruby
# test/support/test_helpers.rb
module TestHelpers
  def login_as(user)
    session[:user_id] = user.id
  end
  
  def logout
    session.delete(:user_id)
  end
  
  def create_authenticated_user
    user = create(:user)
    login_as(user)
    user
  end
  
  def assert_page_title(title)
    assert_select 'title', title
  end
  
  def assert_flash_message(type, message)
    assert_select ".flash-#{type}", text: message
  end
end

# test/integration/base_integration_test.rb
class BaseIntegrationTest < ActionDispatch::IntegrationTest
  include TestHelpers
  
  def setup
    super
    # Common setup for all integration tests
  end
  
  def teardown
    super
    # Common teardown for all integration tests
  end
end
```

### 2. Test Utilities

```ruby
# lib/test_utils.rb
class TestUtils
  def self.create_test_database
    # Create isolated test database
    ActiveRecord::Base.establish_connection(
      adapter: 'sqlite3',
      database: 'db/test.sqlite3'
    )
    
    # Load schema
    load Rails.root.join('db/schema.rb')
  end
  
  def self.cleanup_test_data
    # Clean up test data
    ActiveRecord::Base.connection.execute("DELETE FROM users")
    ActiveRecord::Base.connection.execute("DELETE FROM posts")
  end
  
  def self.wait_for_ajax(timeout = 5)
    start_time = Time.current
    
    while Time.current - start_time < timeout
      return true if page.evaluate_script("jQuery.active == 0")
      sleep(0.1)
    end
    
    false
  end
  
  def self.screenshot_on_failure(test_name)
    return unless page.driver.respond_to?(:save_screenshot)
    
    filename = "tmp/screenshots/#{test_name}_#{Time.current.strftime('%Y%m%d_%H%M%S')}.png"
    page.driver.save_screenshot(filename)
    puts "Screenshot saved: #{filename}"
  end
end
```

### 3. Test Configuration

```ruby
# config/environments/test.rb
Rails.application.configure do
  config.cache_classes = true
  config.eager_load = false
  config.public_file_server.enabled = true
  config.consider_all_requests_local = true
  config.action_controller.perform_caching = false
  config.action_dispatch.show_exceptions = false
  config.action_controller.allow_forgery_protection = false
  config.action_mailer.perform_caching = false
  config.action_mailer.delivery_method = :test
  config.active_support.deprecation = :stderr
  config.log_level = :debug
  config.log_tags = [:request_id]
  
  # Test-specific settings
  config.action_mailer.default_url_options = { host: 'localhost', port: 3000 }
  config.action_mailer.delivery_method = :test
  config.action_mailer.perform_deliveries = true
end
```

## Practice Exercises

### Exercise 1: Test Framework
Create a custom test framework with:
- Assertion helpers
- Test discovery
- Result reporting
- Parallel execution

### Exercise 2: Mock Service
Build a comprehensive mock service with:
- Request/response recording
- Dynamic response generation
- Error simulation
- Performance monitoring

### Exercise 3: Test Data Factory
Implement an advanced test data factory with:
- Relationship management
- Data validation
- Performance optimization
- Cleanup strategies

### Exercise 4: Test Automation Pipeline
Create a complete test automation pipeline with:
- Multiple test types
- Parallel execution
- Result aggregation
- Notification system

---

**Ready to explore more advanced Ruby topics? Let's continue! 🧪**
