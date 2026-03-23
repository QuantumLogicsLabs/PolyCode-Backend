# Testing Strategies in Ruby
# Comprehensive guide to effective testing practices and methodologies

## 🎯 Overview

Testing is crucial for building reliable Ruby applications. This guide covers testing strategies, frameworks, and best practices for comprehensive test coverage.

## 🧪 Testing Fundamentals

### 1. Testing Pyramid

Understanding the testing pyramid and test types:

```ruby
class TestingPyramid
  def self.demonstrate_pyramid
    puts "Testing Pyramid:"
    puts "=" * 50
    
    pyramid = [
      {
        level: "Unit Tests",
        percentage: 70,
        description: "Test individual components in isolation",
        examples: ["Model validation", "Service methods", "Utility functions"],
        characteristics: ["Fast", "Isolated", "Many", "Specific"]
      },
      {
        level: "Integration Tests",
        percentage: 20,
        description: "Test interaction between components",
        examples: ["Database operations", "API endpoints", "Service integration"],
        characteristics: ["Medium speed", "Real dependencies", "Fewer", "Broader scope"]
      },
      {
        level: "End-to-End Tests",
        percentage: 10,
        description: "Test complete user workflows",
        examples: ["User registration", "Payment processing", "File upload"],
        characteristics: ["Slow", "Full system", "Very few", "User-focused"]
      }
    ]
    
    pyramid.each do |level|
      puts "#{level[:level]} (#{level[:percentage]}%):"
      puts "  Description: #{level[:description]}"
      puts "  Examples: #{level[:examples].join(', ')}"
      puts "  Characteristics: #{level[:characteristics].join(', ')}"
      puts
    end
    
    puts "Testing Principles:"
    principles = [
      "Test first, write code second",
      "Write tests that fail, then make them pass",
      "Test one thing at a time",
      "Use descriptive test names",
      "Keep tests independent",
      "Use test doubles for external dependencies",
      "Test edge cases and error conditions",
      "Maintain test code quality"
    ]
    
    principles.each { |principle| puts "• #{principle}" }
  end
  
  def self.test_types
    puts "\nTest Types:"
    puts "=" * 50
    
    test_types = [
      {
        type: "Unit Tests",
        purpose: "Test individual methods/functions",
        scope: "Single class or method",
        dependencies: "Mocked/stubbed",
        speed: "Fast (< 100ms)",
        examples: ["user.calculate_age", "email.send_welcome", "validator.valid?"]
      },
      {
        type: "Integration Tests",
        purpose: "Test component interactions",
        scope: "Multiple components",
        dependencies: "Real but limited",
        speed: "Medium (100ms - 1s)",
        examples: ["user.save with database", "API endpoint with service", "email with real SMTP"]
      },
      {
        type: "System Tests",
        purpose: "Test complete workflows",
        scope: "Entire application",
        dependencies: "Full system",
        speed: "Slow (1s - 10s)",
        examples: ["User registration flow", "Order processing", "File upload workflow"]
      },
      {
        type: "Acceptance Tests",
        purpose: "Test business requirements",
        scope: "Business scenarios",
        dependencies: "Full system",
        speed: "Slow (5s - 30s)",
        examples: ["Customer can purchase product", "Admin can manage users", "Reports generate correctly"]
      },
      {
        type: "Performance Tests",
        purpose: "Test performance characteristics",
        scope: "Critical paths",
        dependencies: "Full system",
        speed: "Variable",
        examples: ["Load testing", "Stress testing", "Memory usage"]
      },
      {
        type: "Security Tests",
        purpose: "Test security vulnerabilities",
        scope: "Security-critical areas",
        dependencies: "Full system",
        speed: "Variable",
        examples: ["Authentication", "Authorization", "Data validation"]
      }
    ]
    
    test_types.each do |type|
      puts "#{type[:type]}:"
      puts "  Purpose: #{type[:purpose]}"
      puts "  Scope: #{type[:scope]}"
      doubles "  Dependencies: #{type[:dependencies]}"
      puts "  Speed: #{type[:speed]}"
      puts "  Examples: #{type[:examples].join(', ')}"
      puts
    end
  end
  
  def self.test_doubles
    puts "\nTest Doubles:"
    puts "=" * 50
    
    doubles = [
      {
        type: "Mock",
        description: "Pre-programmed with expectations",
        usage: "Verify method calls and parameters",
        example: "expect(email_service).to receive(:send).with(user.email)"
      },
      {
        type: "Stub",
        description: "Provides predefined responses",
        usage: "Return specific values for testing",
        example: "allow(user_service).to receive(:find).with(1).and_return(user)"
      },
      {
        type: "Fake",
        description: "Working implementation but simplified",
        usage: "In-memory database, fake email service",
        example: "FakeDatabase.new # In-memory implementation"
      },
      {
        type: "Spy",
        description: "Records method calls",
        usage: "Verify interactions after test",
        example: "spy = spy(email_service); spy.send(email); expect(spy).to have_received(:send)"
      },
      {
        type: "Dummy",
        description: "Passed around but never used",
        usage: "Parameter requirements",
        example: "dummy_user = double('user')"
      }
    ]
    
    doubles.each do |double|
      puts "#{double[:type]}:"
      puts "  Description: #{double[:description]}"
      puts "  Usage: #{double[:usage]}"
      puts "  Example: #{double[:example]}"
      puts
    end
  end
  
  # Run testing fundamentals examples
  demonstrate_pyramid
  test_types
  test_doubles
end
```

### 2. Test Structure and Organization

Organizing tests for maintainability:

```ruby
class TestStructure
  def self.test_organization
    puts "Test Organization:"
    puts "=" * 50
    
    organization = [
      {
        level: "Unit Tests",
        structure: "spec/models/user_spec.rb",
        naming: "describe User do ... end",
        examples: [
          "spec/models/user_spec.rb",
          "spec/services/email_service_spec.rb",
          "spec/lib/calculator_spec.rb"
        ]
      },
      {
        level: "Integration Tests",
        structure: "spec/integration/user_registration_spec.rb",
        naming: "describe 'User Registration' do ... end",
        examples: [
          "spec/integration/user_registration_spec.rb",
          "spec/integration/payment_processing_spec.rb",
          "spec/integration/api_endpoints_spec.rb"
        ]
      },
      {
        level: "System Tests",
        structure: "spec/system/user_workflow_spec.rb",
        naming: "describe 'User Workflow' do ... end",
        examples: [
          "spec/system/user_workflow_spec.rb",
          "spec/system/admin_panel_spec.rb",
          "spec/system/report_generation_spec.rb"
        ]
      },
      {
        level: "Feature Tests",
        structure: "spec/features/user_management.feature",
        naming: "Feature: User Management",
        examples: [
          "spec/features/user_management.feature",
          "spec/features/order_processing.feature",
          "spec/features/reporting.feature"
        ]
      }
    ]
    
    organization.each do |level|
      puts "#{level[:level]}:"
      puts "  Structure: #{level[:structure]}"
      puts "  Naming: #{level[:naming]}"
      puts "  Examples: #{level[:examples].join(', ')}"
      puts
    end
    
    puts "File Structure:"
    structure = [
      "spec/",
      "  models/",
      "    user_spec.rb",
      "    order_spec.rb",
      "    product_spec.rb",
      "  services/",
      "    email_service_spec.rb",
      "    payment_service_spec.rb",
      "    notification_service_spec.rb",
      "  lib/",
      "    calculator_spec.rb",
      "    string_helper_spec.rb",
      "    date_helper_spec.rb",
      "  integration/",
      "    user_registration_spec.rb",
      "    payment_processing_spec.rb",
      "    api_endpoints_spec.rb",
      "  system/",
      "    user_workflow_spec.rb",
      "    admin_panel_spec.rb",
      "    report_generation_spec.rb",
      "  support/",
      "    factory_bot.rb",
      "    database_cleaner.rb",
      "    test_helpers.rb",
      "  fixtures/",
      "    users.yml",
      "    orders.yml",
      "    products.yml"
    ]
    
    structure.each { |file| puts "  #{file}" }
  end
  
  def self.test_naming_conventions
    puts "\nTest Naming Conventions:"
    puts "=" * 50
    
    conventions = [
      {
        type: "RSpec",
        format: "describe ClassName do ... end",
        context: "context 'when condition' do ... end",
        example: "it 'does something' do ... end",
        examples: [
          "describe User do",
          "  context 'when user is active' do",
          "    it 'returns true for active?' do",
          "    end",
          "end"
        ]
      },
      {
        type: "Minitest",
        format: "class ClassNameTest < Minitest::Test",
        method: "def test_method_name",
        examples: [
          "class UserTest < Minitest::Test",
          "  def test_active_returns_true_for_active_user",
          "  end",
          "end"
        ]
      },
      {
        type: "Cucumber",
        format: "Feature: Feature Name",
        scenario: "Scenario: Scenario Description",
        examples: [
          "Feature: User Registration",
          "  Scenario: User registers successfully",
          "    Given I am on the registration page",
          "    When I fill in valid user details",
          "    Then I should see a success message"
        ]
      }
    ]
    
    conventions.each do |convention|
      puts "#{convention[:type]}:"
      puts "  Format: #{convention[:format]}"
      puts "  Context: #{convention[:context]}" if convention[:context]
      puts "  Example: #{convention[:example]}" if convention[:example]
      puts "  Examples: #{convention[:examples].join(', ')}" if convention[:examples]
      puts
    end
    
    puts "Naming Guidelines:"
    guidelines = [
      "Use descriptive names that explain what is being tested",
      "Focus on behavior, not implementation",
      "Use 'should' or present tense for expectations",
      "Include context in test names when helpful",
      "Keep names concise but informative",
      "Use consistent naming across the test suite"
    ]
    
    guidelines.each { |guideline| puts "• #{guideline}" }
  end
  
  def self.test_setup_teardown
    puts "\nTest Setup and Teardown:"
    puts "=" * 50
    
    setup_teardown = [
      {
        framework: "RSpec",
        setup: "before(:each) { # setup code }",
        teardown: "after(:each) { # cleanup code }",
        all_setup: "before(:all) { # one-time setup }",
        all_teardown: "after(:all) { # one-time cleanup }",
        example: <<~RUBY
          describe User do
            before(:each) do
              @user = User.new(name: "John", email: "john@example.com")
            end
            
            after(:each) do
              @user = nil
            end
            
            it "has a name" do
              expect(@user.name).to eq("John")
            end
          end
        RUBY
      },
      {
        framework: "Minitest",
        setup: "def setup; # setup code; end",
        teardown: "def teardown; # cleanup code; end",
        all_setup: "N/A (use class methods)",
        all_teardown: "N/A (use class methods)",
        example: <<~RUBY
          class UserTest < Minitest::Test
            def setup
              @user = User.new(name: "John", email: "john@example.com")
            end
            
            def teardown
              @user = nil
            end
            
            def test_user_has_name
              assert_equal "John", @user.name
            end
          end
        RUBY
      }
    ]
    
    setup_teardown.each do |framework|
      puts "#{framework[:framework]}:"
      puts "  Setup: #{framework[:setup]}"
      puts "  Teardown: #{framework[:teardown]}"
      puts "  All Setup: #{framework[:all_setup]}"
      puts "  All Teardown: #{framework[:all_teardown]}"
      puts "  Example: #{framework[:example]}"
      puts
    end
    
    puts "Setup/Teardown Best Practices:"
    practices = [
      "Use before(:each) for test isolation",
      "Use before(:all) for expensive setup",
      "Clean up after each test",
      "Use shared examples for common setup",
      "Avoid global state in tests",
      "Use test factories for object creation",
      "Keep setup code minimal",
      "Document complex setup logic"
    ]
    
    practices.each { |practice| puts "• #{practice}" }
  end
  
  # Run test structure examples
  test_organization
  test_naming_conventions
  test_setup_teardown
end
```

## 🛠️ Testing Frameworks

### 1. RSpec

Comprehensive RSpec testing examples:

```ruby
class RSpecExamples
  def self.basic_rspec_examples
    puts "Basic RSpec Examples:"
    puts "=" * 50
    
    # Example RSpec test structure
    rspec_example = <<~RUBY
      # spec/models/user_spec.rb
      require 'rails_helper'
      
      RSpec.describe User, type: :model do
        # Subject
        subject(:user) { User.new(name: "John", email: "john@example.com") }
        
        # Factories
        let(:admin_user) { create(:user, :admin) }
        let(:regular_user) { create(:user) }
        
        # Context blocks
        context "when user is new" do
          it "is not active" do
            expect(user).not_to be_active
          end
          
          it "has default role" do
            expect(user.role).to eq("user")
          end
        end
        
        context "when user is active" do
          before { user.activate! }
          
          it "is active" do
            expect(user).to be_active
          end
        end
        
        # Expectations
        it "has a name" do
          expect(user.name).to eq("John")
        end
        
        it "has an email" do
          expect(user.email).to eq("john@example.com")
        end
        
        # Matchers
        it "validates name presence" do
          user.name = nil
          expect(user).not_to be_valid
          expect(user.errors[:name]).to include("can't be blank")
        end
        
        it "validates email format" do
          user.email = "invalid-email"
          expect(user).not_to be_valid
          expect(user.errors[:email]).to include("is invalid")
        end
        
        # Change matchers
        it "changes user count when creating user" do
          expect {
            User.create(name: "Jane", email: "jane@example.com")
          }.to change(User, :count).by(1)
        end
        
        # Predicate matchers
        it "is valid with valid attributes" do
          expect(user).to be_valid
        end
        
        # Collection matchers
        it "includes user in active users" do
          user.activate!
          expect(User.active).to include(user)
        end
      end
    RUBY
    
    puts "RSpec Test Structure:"
    puts rspec_example
  end
  
  def self.rspec_matchers
    puts "\nRSpec Matchers:"
    puts "=" * 50
    
    matchers = [
      {
        category: "Equality",
        examples: [
          "expect(value).to eq(expected)",
          "expect(value).to eql(expected)",
          "expect(value).to equal(expected)",
          "expect(array).to match_array(expected)"
        ]
      },
      {
        category: "Comparison",
        examples: [
          "expect(value).to be > 5",
          "expect(value).to be >= 5",
          "expect(value).to be < 10",
          "expect(value).to be <= 10",
          "expect(value).to be_between(1, 10)"
        ]
      },
      {
        category: "Truthiness",
        examples: [
          "expect(value).to be_truthy",
          "expect(value).to be_falsey",
          "expect(value).to be_nil",
          "expect(value).to be_present"
        ]
      },
      {
        category: "Predicates",
        examples: [
          "expect(user).to be_valid",
          "expect(user).to be_active",
          "expect(array).to be_empty",
          "expect(hash).to have_key(:name)"
        ]
      },
      {
        category: "Collection",
        examples: [
          "expect(array).to include(item)",
          "expect(array).to contain_exactly(item1, item2)",
          "expect(array).to start_with(item)",
          "expect(array).to end_with(item)"
        ]
      },
      {
        category: "Error",
        examples: [
          "expect { raise_error }.to raise_error(ErrorType)",
          "expect { raise_error }.to raise_error(ErrorType, message)",
          "expect { raise_error }.to raise_error(/pattern/)"
        ]
      },
      {
        category: "Change",
        examples: [
          "expect { action }.to change(object, :attribute)",
          "expect { action }.to change(object, :attribute).by(1)",
          "expect { action }.to change(object, :attribute).from(old).to(new)"
        ]
      }
    ]
    
    matchers.each do |matcher|
      puts "#{matcher[:category]}:"
      matcher[:examples].each { |example| puts "  #{example}" }
      puts
    end
  end
  
  def self.rspec_doubles
    puts "\nRSpec Doubles:"
    puts "=" * 50
    
    doubles_example = <<~RUBY
      RSpec.describe EmailService do
        let(:user) { double('User', email: 'user@example.com') }
        let(:email_service) { EmailService.new }
        
        # Method stubbing
        it "sends welcome email" do
          allow(email_service).to receive(:send_welcome)
          email_service.send_welcome(user)
          
          expect(email_service).to have_received(:send_welcome).with(user)
        end
        
        # Return value stubbing
        it "returns true when email sent successfully" do
          allow(email_service).to receive(:send_welcome).and_return(true)
          
          result = email_service.send_welcome(user)
          
          expect(result).to be true
        end
        
        # Method expectations
        it "calls email service" do
          expect(email_service).to receive(:send_welcome).with(user)
          
          email_service.send_welcome(user)
        end
        
        # Argument matching
        it "sends email with correct template" do
          expect(email_service).to receive(:send_welcome)
            .with(user, template: 'welcome')
          
          email_service.send_welcome(user, template: 'welcome')
        end
        
        # Double objects
        it "uses double for external service" do
          payment_service = double('PaymentService')
          allow(payment_service).to receive(:process_payment)
            .with(100, 'USD').and_return(success: true)
          
          result = payment_service.process_payment(100, 'USD')
          
          expect(result[:success]).to be true
        end
        
        # Instance doubles
        it "uses instance double for existing class" do
          user_instance = instance_double('User')
          allow(user_instance).to receive(:email).and_return('user@example.com')
          
          expect(user_instance.email).to eq('user@example.com')
        end
        
        # Class doubles
        it "uses class double for external class" do
          payment_gateway = class_double('PaymentGateway')
          allow(payment_gateway).to receive(:process)
            .with(100, 'USD').and_return(success: true)
          
          result = PaymentGateway.process(100, 'USD')
          
          expect(result[:success]).to be true
        end
      end
    RUBY
    
    puts "RSpec Doubles Examples:"
    puts doubles_example
  end
  
  def self.rspec_shared_examples
    puts "\nRSpec Shared Examples:"
    puts "=" * 50
    
    shared_examples = <<~RUBY
      # spec/support/shared_examples/authenticated_user.rb
      RSpec.shared_examples "authenticated user" do
        let(:user) { create(:user, :authenticated) }
        
        before { sign_in user }
        
        it "has access to protected resources" do
          get :protected_action
          expect(response).to be_successful
        end
        
        it "can update own profile" do
          patch :update, params: { id: user.id, user: { name: "New Name" } }
          expect(user.reload.name).to eq("New Name")
        end
      end
      
      # spec/controllers/admin_controller_spec.rb
      RSpec.describe AdminController, type: :controller do
        context "when user is admin" do
          let(:admin_user) { create(:user, :admin) }
          
          before { sign_in admin_user }
          
          it_behaves_like "authenticated user"
          
          it "can access admin panel" do
            get :admin_panel
            expect(response).to be_successful
          end
        end
        
        context "when user is regular user" do
          let(:regular_user) { create(:user) }
          
          before { sign_in regular_user }
          
          it "cannot access admin panel" do
            get :admin_panel
            expect(response).to have_http_status(:forbidden)
          end
        end
      end
      
      # spec/support/shared_examples/paginated_response.rb
      RSpec.shared_examples "paginated response" do |action|
        it "returns paginated results" do
          create_list(:item, 25)
          
          get action, params: { page: 1, per_page: 10 }
          
          expect(response).to be_successful
          expect(json['data'].length).to eq(10)
          expect(json['pagination']['page']).to eq(1)
          expect(json['pagination']['total_pages']).to eq(3)
        end
      end
      
      # spec/controllers/items_controller_spec.rb
      RSpec.describe ItemsController, type: :controller do
        it_behaves_like "paginated response", :index
      end
    RUBY
    
    puts "RSpec Shared Examples:"
    puts shared_examples
  end
  
  # Run RSpec examples
  basic_rspec_examples
  rspec_matchers
  rspec_doubles
  rspec_shared_examples
end
```

### 2. Minitest

Minitest testing framework examples:

```ruby
class MinitestExamples
  def self.basic_minitest_examples
    puts "Basic Minitest Examples:"
    puts "=" * 50
    
    # Example Minitest test structure
    minitest_example = <<~RUBY
      # test/models/user_test.rb
      require 'test_helper'
      
      class UserTest < ActiveSupport::TestCase
        # Setup method runs before each test
        def setup
          @user = User.new(name: "John", email: "john@example.com")
        end
        
        # Teardown method runs after each test
        def teardown
          @user = nil
        end
        
        # Basic assertions
        def test_user_has_name
          assert_equal "John", @user.name
        end
        
        def test_user_has_email
          assert_equal "john@example.com", @user.email
        end
        
        # Boolean assertions
        def test_user_is_not_active_by_default
          assert_not @user.active?
        end
        
        # Nil assertions
        def test_user_id_is_nil_before_save
          assert_nil @user.id
        end
        
        # Inclusion assertions
        def test_user_role_is_valid
          valid_roles = %w[user admin moderator]
          assert_includes valid_roles, @user.role
        end
        
        # Validation assertions
        def test_user_requires_name
          @user.name = nil
          assert_not @user.valid?
          assert_includes @user.errors[:name], "can't be blank"
        end
        
        def test_user_requires_email
          @user.email = nil
          assert_not @user.valid?
          assert_includes @user.errors[:email], "can't be blank"
        end
        
        # Change assertions
        def test_create_user_increases_count
          assert_difference 'User.count', 1 do
            User.create(name: "Jane", email: "jane@example.com")
          end
        end
        
        # Exception assertions
        def test_raises_error_for_invalid_email
          assert_raises ArgumentError do
            @user.email = "invalid-email"
            @user.save!
          end
        end
        
        # Performance assertions
        def test_user_creation_performance
          assert_performance_constant do
            User.create(name: "Test", email: "test@example.com")
          end
        end
      end
    RUBY
    
    puts "Minitest Test Structure:"
    puts minitest_example
  end
  
  def self.minitest_assertions
    puts "\nMinitest Assertions:"
    puts "=" * 50
    
    assertions = [
      {
        category: "Equality",
        methods: [
          "assert_equal(expected, actual)",
          "assert_not_equal(expected, actual)",
          "assert_same(expected, actual)",
          "assert_not_same(expected, actual)"
        ]
      },
      {
        category: "Truthiness",
        methods: [
          "assert(boolean)",
          "assert_not(boolean)",
          "assert_nil(object)",
          "assert_not_nil(object)"
        ]
      },
      {
        category: "Inclusion",
        methods: [
          "assert_includes(collection, object)",
          "assert_not_includes(collection, object)",
          "assert_match(pattern, string)",
          "assert_no_match(pattern, string)"
        ]
      },
      {
        category: "Numeric",
        methods: [
          "assert_operator(object1, :>, object2)",
          "assert_operator(object1, :<, object2)",
          "assert_operator(object1, :>=, object2)",
          "assert_operator(object1, :<=, object2)",
          "assert_in_delta(expected, actual, delta)"
        ]
      },
      {
        category: "Exception",
        methods: [
          "assert_raises(ExceptionClass) { raise ExceptionClass }",
          "assert_nothing_raised { safe_operation }",
          "assert_throw(:symbol) { throw :symbol }"
        ]
      },
      {
        category: "Change",
        methods: [
          "assert_difference('Model.count', 1) { Model.create }",
          "assert_no_difference('Model.count') { safe_operation }",
          "assert_difference(['Model.count', 'OtherModel.count'], 1) { operation }"
        ]
      }
    ]
    
    assertions.each do |assertion|
      puts "#{assertion[:category]}:"
      assertion[:methods].each { |method| puts "  #{method}" }
      puts
    end
  end
  
  def self.minitest_fixtures
    puts "\nMinitest Fixtures:"
    puts "=" * 50
    
    fixtures_example = <<~RUBY
      # test/fixtures/users.yml
      john:
        name: John Doe
        email: john@example.com
        active: true
        role: user
        created_at: <%= 1.day.ago %>
        updated_at: <%= 1.day.ago %>
      
      jane:
        name: Jane Smith
        email: jane@example.com
        active: false
        role: admin
        created_at: <%= 2.days.ago %>
        updated_at: <%= 2.days.ago %>
      
      # test/models/user_test.rb
      require 'test_helper'
      
      class UserTest < ActiveSupport::TestCase
        fixtures :users
        
        def test_fixture_user_john_exists
          user = users(:john)
          assert_equal "John Doe", user.name
          assert_equal "john@example.com", user.email
          assert user.active?
          assert_equal "user", user.role
        end
        
        def test_fixture_user_jane_exists
          user = users(:jane)
          assert_equal "Jane Smith", user.name
          assert_equal "jane@example.com", user.email
          assert_not user.active?
          assert_equal "admin", user.role
        end
        
        def test_fixture_users_are_persisted
          assert users(:john).persisted?
          assert users(:jane).persisted?
        end
        
        def test_fixture_relationships
          # If users have relationships
          assert_not_nil users(:john).profile
        end
      end
    RUBY
    
    puts "Minitest Fixtures Example:"
    puts fixtures_example
  end
  
  def self.minitest_helpers
    puts "\nMinitest Helpers:"
    puts "=" * 50
    
    helpers_example = <<~RUBY
      # test/test_helper.rb
      require 'simplecov'
      SimpleCov.start 'rails'
      
      ENV['RAILS_ENV'] ||= 'test'
      require_relative '../config/environment'
      require 'rails/test_help'
      
      class ActiveSupport::TestCase
        # Setup all fixtures in test/fixtures/*.yml
        fixtures :all
        
        # Add more helper methods to be used by all tests here...
        
        # Helper method to create user with default attributes
        def create_user(attributes = {})
          default_attributes = {
            name: "Test User",
            email: "test@example.com",
            active: true,
            role: "user"
          }
          
          User.create!(default_attributes.merge(attributes))
        end
        
        # Helper method to create admin user
        def create_admin(attributes = {})
          create_user(attributes.merge(role: "admin"))
        end
        
        # Helper method to sign in user
        def sign_in(user)
          session[:user_id] = user.id
        end
        
        # Helper method to sign out user
        def sign_out
          session[:user_id] = nil
        end
        
        # Helper method to assert response success
        def assert_response_success
          assert_response :success
        end
        
        # Helper method to assert response redirect
        def assert_response_redirect_to(path)
          assert_response :redirect
          assert_redirected_to path
        end
        
        # Helper method to assert JSON response
        def assert_json_response(expected)
          assert_equal expected, JSON.parse(response.body)
        end
      end
      
      # test/controllers/users_controller_test.rb
      require 'test_helper'
      
      class UsersControllerTest < ActionDispatch::IntegrationTest
        def test_user_registration
          user_count = User.count
          
          post users_path, params: {
            user: {
              name: "New User",
              email: "newuser@example.com",
              password: "password123"
            }
          }
          
          assert_response :success
          assert_equal user_count + 1, User.count
          assert_equal "New User", User.last.name
        end
        
        def test_user_login
          user = create_user(email: "login@example.com", password: "password123")
          
          post session_path, params: {
            email: "login@example.com",
            password: "password123"
          }
          
          assert_response :success
          assert_equal user.id, session[:user_id]
        end
        
        def test_protected_action_requires_login
          get protected_path
          
          assert_response :redirect
          assert_redirected_to login_path
        end
      end
    RUBY
    
    puts "Minitest Helpers Example:"
    puts helpers_example
  end
  
  # Run Minitest examples
  basic_minitest_examples
  minitest_assertions
  minitest_fixtures
  minitest_helpers
end
```

### 3. Cucumber

Behavior-driven development with Cucumber:

```ruby
class CucumberExamples
  def self.basic_cucumber_examples
    puts "Basic Cucumber Examples:"
    puts "=" * 50
    
    # Example Cucumber feature file
    cucumber_feature = <<~CUCUMBER
      # features/user_registration.feature
      Feature: User Registration
        
        As a new user
        I want to register an account
        So that I can access the application
        
        Background:
          Given I am on the registration page
          And the registration form is displayed
        
        Scenario: Successful registration
          When I fill in "Name" with "John Doe"
          And I fill in "Email" with "john@example.com"
          And I fill in "Password" with "password123"
          And I fill in "Password confirmation" with "password123"
          And I press "Register"
          Then I should see "Welcome, John Doe!"
          And I should be logged in
          And I should receive a welcome email
          And my account should be created
        
        Scenario: Registration with invalid email
          When I fill in "Name" with "John Doe"
          And I fill in "Email" with "invalid-email"
          And I fill in "Password" with "password123"
          And I fill in "Password confirmation" with "password123"
          And I press "Register"
          Then I should see "Email is invalid"
          And I should not be logged in
          And no account should be created
        
        Scenario: Registration with mismatched passwords
          When I fill in "Name" with "John Doe"
          And I fill in "Email" with "john@example.com"
          And I fill in "Password" with "password123"
          And I fill in "Password confirmation" with "different"
          And I press "Register"
          Then I should see "Password confirmation doesn't match Password"
          And I should not be logged in
          And no account should be created
        
        Scenario Outline: Registration with different roles
          Given I fill in "Name" with "<name>"
          And I fill in "Email" with "<email>"
          And I fill in "Password" with "<password>"
          And I fill in "Password confirmation" with "<password>"
          And I select "<role>" from "Role"
          And I press "Register"
          Then I should see "Welcome, <name>!"
          And my account should have role "<role>"
        
          Examples:
            | name      | email                | password    | role    |
            | John Doe | john@example.com     | password123 | user    |
            | Jane Smith| jane@example.com    | password123 | admin   |
            | Bob Johnson| bob@example.com   | password123 | moderator|
    CUCUMBER
    
    puts "Cucumber Feature Example:"
    puts cucumber_feature
  end
  
  def self.cucumber_step_definitions
    puts "\nCucumber Step Definitions:"
    puts "=" * 50
    
    step_definitions = <<~RUBY
      # features/step_definitions/user_steps.rb
      Given('I am on the registration page') do
        visit new_user_registration_path
      end
      
      Given('the registration form is displayed') do
        expect(page).to have_content('Name')
        expect(page).to have_content('Email')
        expect(page).to have_content('Password')
        expect(page).to have_button('Register')
      end
      
      When('I fill in {string} with {string}') do |field, value|
        fill_in field, with: value
      end
      
      When('I select {string} from {string}') do |option, dropdown|
        select option, from: dropdown
      end
      
      When('I press {string}') do |button|
        click_button button
      end
      
      Then('I should see {string}') do |content|
        expect(page).to have_content(content)
      end
      
      Then('I should be logged in') do
        expect(page).to have_content('Logout')
        expect(page).not_to have_content('Login')
      end
      
      Then('I should receive a welcome email') do
        expect(ActionMailer::Base.deliveries).not_to be_empty
        email = ActionMailer::Base.deliveries.last
        expect(email.subject).to include('Welcome')
        expect(email.to).to include(@user.email)
      end
      
      Then('my account should be created') do
        expect(User.find_by(email: @user.email)).not_to be_nil
      end
      
      Then('I should not be logged in') do
        expect(page).to have_content('Login')
        expect(page).not_to have_content('Logout')
      end
      
      Then('no account should be created') do
        expect(User.find_by(email: @user.email)).to be_nil
      end
      
      Then('my account should have role {string}') do |role|
        user = User.find_by(email: @user.email)
        expect(user.role).to eq(role)
      end
      
      # Background steps
      Before do
        ActionMailer::Base.deliveries.clear
      end
      
      # Helper methods
      def current_user
        @user ||= User.find_by(email: find_field('Email', type: 'email').value)
      end
    RUBY
    
    puts "Cucumber Step Definitions Example:"
    puts step_definitions
  end
  
  def self.cucumber_support_code
    puts "\nCucumber Support Code:"
    puts "=" * 50
    
    support_code = <<~RUBY
      # features/support/env.rb
      require 'cucumber/rails'
      require 'capybara/poltergeist'
      
      Capybara.javascript_driver = :poltergeist
      Capybara.default_max_wait_time = 5
      
      # Database cleaner
      Before do
        DatabaseCleaner.strategy = :truncation
        DatabaseCleaner.start
      end
      
      After do
        DatabaseCleaner.clean
      end
      
      # features/support/factory_bot.rb
      require 'factory_bot_rails'
      
      World(FactoryBot::Syntax::Methods)
      
      # features/support/helpers.rb
      module CustomHelpers
        def create_user_with_role(role)
          create(:user, role: role)
        end
        
        def sign_in_as(user)
          visit login_path
          fill_in 'Email', with: user.email
          fill_in 'Password', with: user.password
          click_button 'Login'
        end
        
        def sign_out
          click_link 'Logout'
        end
        
        def wait_for_ajax
          wait_until { page.evaluate_script('jQuery.active == 0') }
        end
        
        def wait_until(timeout = Capybara.default_max_wait_time)
          start_time = Time.now
          while Time.now - start_time < timeout
            break if yield
            sleep 0.1
          end
          raise "Timeout waiting for condition" unless yield
        end
      end
      
      World(CustomHelpers)
      
      # features/support/hooks.rb
      Before('@javascript') do
        Capybara.current_driver = :poltergeist
      end
      
      Before('@no-js') do
        Capybara.current_driver = :rack_test
      end
      
      After('@javascript') do
        Capybara.use_default_driver
      end
      
      After('@no-js') do
        Capybara.use_default_driver
      end
      
      Before('@slow') do
        Capybara.default_max_wait_time = 30
      end
      
      After('@slow') do
        Capybara.default_max_wait_time = 5
      end
      
      # features/support/transforms.rb
      Transform /^(\d+)$/ do |number|
        number.to_i
      end
      
      Transform /^(\d+\.\d+)$/ do |number|
        number.to_f
      end
      
      Transform /^(true|false)$/ do |boolean|
        boolean == 'true'
      end
    RUBY
    
    puts "Cucumber Support Code Example:"
    puts support_code
  end
  
  # Run Cucumber examples
  basic_cucumber_examples
  cucumber_step_definitions
  cucumber_support_code
end
```

## 🔍 Test-Driven Development

### 1. TDD Workflow

Test-driven development methodology:

```ruby
class TestDrivenDevelopment
  def self.tdd_workflow
    puts "Test-Driven Development Workflow:"
    puts "=" * 50
    
    workflow = [
      {
        step: "1. Red",
        description: "Write a failing test",
        details: [
          "Write test for new functionality",
          "Ensure test fails initially",
          "Test should be as simple as possible",
          "Test should fail for the right reason"
        ]
      },
      {
        step: "2. Green",
        description: "Make the test pass",
        details: [
          "Write minimal code to make test pass",
          "Don't worry about code quality yet",
          "Focus on making test green",
          "Use simplest possible implementation"
        ]
      },
      {
        step: "3. Refactor",
        description: "Improve the code",
        details: [
          "Refactor code while keeping tests green",
          "Remove duplication",
          "Improve design",
          "Ensure all tests still pass"
        ]
      }
    ]
    
    workflow.each do |step|
      puts "#{step[:step]}: #{step[:description]}"
      step[:details].each { |detail| puts "  • #{detail}" }
      puts
    end
    
    puts "TDD Benefits:"
    benefits = [
      "Better code design",
      "Comprehensive test coverage",
      "Reduced debugging time",
      "Confidence in refactoring",
      "Living documentation",
      "Faster development cycles"
    ]
    
    benefits.each { |benefit| puts "• #{benefit}" }
  end
  
  def self.tdd_example
    puts "\nTDD Example - String Calculator:"
    puts "=" * 50
    
    # Step 1: Red - Write failing test
    red_phase = <<~RUBY
      # spec/calculator_spec.rb
      RSpec.describe Calculator do
        describe '#add' do
          it 'adds two positive numbers' do
            calculator = Calculator.new
            result = calculator.add(2, 3)
            expect(result).to eq(5)
          end
          
          it 'adds negative numbers' do
            calculator = Calculator.new
            result = calculator.add(-2, -3)
            expect(result).to eq(-5)
          end
        end
      end
    RUBY
    
    # Step 2: Green - Make test pass
    green_phase = <<~RUBY
      # lib/calculator.rb
      class Calculator
        def add(a, b)
          a + b
        end
      end
    RUBY
    
    # Step 3: Refactor - Improve code
    refactor_phase = <<~RUBY
      # lib/calculator.rb (refactored)
      class Calculator
        def add(a, b)
          # Add input validation
          raise ArgumentError, "First argument must be numeric" unless a.is_a?(Numeric)
          raise ArgumentError, "Second argument must be numeric" unless b.is_a?(Numeric)
          
          # Perform addition
          result = a + b
          
          # Return result
          result
        end
      end
      
      # spec/calculator_spec.rb (additional tests)
      RSpec.describe Calculator do
        describe '#add' do
          it 'adds two positive numbers' do
            calculator = Calculator.new
            result = calculator.add(2, 3)
            expect(result).to eq(5)
          end
          
          it 'adds negative numbers' do
            calculator = Calculator.new
            result = calculator.add(-2, -3)
            expect(result).to eq(-5)
          end
          
          it 'raises error for non-numeric first argument' do
            calculator = Calculator.new
            expect { calculator.add('2', 3) }.to raise_error(ArgumentError)
          end
          
          it 'raises error for non-numeric second argument' do
            calculator = Calculator.new
            expect { calculator.add(2, '3') }.to raise_error(ArgumentError)
          end
        end
      end
    RUBY
    
    puts "Step 1 - Red Phase:"
    puts red_phase
    puts "\nStep 2 - Green Phase:"
    puts green_phase
    puts "\nStep 3 - Refactor Phase:"
    puts refactor_phase
  end
  
  def self.tdd_best_practices
    puts "\nTDD Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Write small, focused tests",
        description: "Each test should test one specific behavior",
        example: "Test addition separately from subtraction"
      },
      {
        practice: "Use descriptive test names",
        description: "Test names should describe what is being tested",
        example: "it 'adds two positive numbers' do ... end"
      },
      {
        practice: "Write the simplest test first",
        description: "Start with the simplest possible test case",
        example: "Test with positive numbers before negative numbers"
      },
      {
        practice: "Write minimal code to pass",
        description: "Only write code needed to make the current test pass",
        example: "Hardcode return value if test doesn't require calculation"
      },
      {
        practice: "Refactor after each test passes",
        description: "Improve code design while keeping tests green",
        example: "Extract methods, improve naming, remove duplication"
      },
      {
        practice: "Keep tests independent",
        description: "Tests should not depend on each other",
        example: "Use setup/teardown instead of shared state"
      },
      {
        practice: "Test edge cases",
        description: "Test boundary conditions and error cases",
        example: "Test with zero, negative numbers, nil values"
      },
      {
        practice: "Use test doubles for external dependencies",
        description: "Isolate code from external systems",
        example: "Mock database calls, external APIs"
      }
    ]
    
    practices.each do |practice|
      puts "#{practice[:practice]}:"
      puts "  Description: #{practice[:description]}"
      puts "  Example: #{practice[:example]}"
      puts
    end
  end
  
  # Run TDD examples
  tdd_workflow
  tdd_example
  tdd_best_practices
end
```

## 📊 Test Coverage and Quality

### 1. Coverage Analysis

Measuring and improving test coverage:

```ruby
class TestCoverage
  def self.coverage_tools
    puts "Test Coverage Tools:"
    puts "=" * 50
    
    tools = [
      {
        name: "SimpleCov",
        description: "Ruby code coverage tool",
        features: [
          "Line coverage",
          "Branch coverage",
          "HTML reports",
          "Threshold enforcement",
          "Integration with CI"
        ],
        setup: <<~RUBY
          # Gemfile
          gem 'simplecov', require: false, group: :test
          
          # spec/spec_helper.rb
          require 'simplecov'
          SimpleCov.start 'rails' do
            add_group 'Models', 'app/models'
            add_group 'Controllers', 'app/controllers'
            add_group 'Services', 'app/services'
            add_group 'Lib', 'app/lib'
            
            add_filter '/spec/'
            add_filter '/config/'
            add_filter '/vendor/'
          end
          
          SimpleCov.minimum_coverage 90
          SimpleCov.refuse_error_drop
        RUBY
      },
      {
        name: "Coverage",
        description: "Ruby's built-in coverage tool",
        features: [
          "Line coverage",
          "Branch coverage",
          "Method coverage",
          "Built-in to Ruby",
          "No external dependencies"
        ],
        setup: <<~RUBY
          # Run with coverage
          ruby --coverage --coverage-dir=coverage test/test.rb
          
          # Generate report
          ruby -r coverage coverage.rb
        RUBY
      },
      {
        name: "RCov",
        description: "Legacy Ruby coverage tool",
        features: [
          "Line coverage",
          "HTML reports",
          "Cross-platform",
          "Deprecated in favor of SimpleCov"
        ],
        setup: <<~RUBY
          # Gemfile
          gem 'rcov', group: :test
          
          # Run with rcov
          rcov test/test.rb
        RUBY
      }
    ]
    
    tools.each do |tool|
      puts "#{tool[:name]}:"
      puts "  Description: #{tool[:description]}"
      puts "  Features: #{tool[:features].join(', ')}"
      puts "  Setup: #{tool[:setup]}"
      puts
    end
  end
  
  def self.coverage_analysis
    puts "\nCoverage Analysis:"
    puts "=" * 50
    
    analysis = [
      {
        metric: "Line Coverage",
        description: "Percentage of lines executed during tests",
        target: "90%+",
        importance: "High - Basic coverage metric"
      },
      {
        metric: "Branch Coverage",
        description: "Percentage of conditional branches executed",
        target: "80%+",
        importance: "High - Tests all code paths"
      },
      {
        metric: "Method Coverage",
        description: "Percentage of methods called during tests",
        target: "95%+",
        importance: "Medium - Ensures methods are tested"
      },
      {
        metric: "Statement Coverage",
        description: "Percentage of statements executed",
        target: "90%+",
        importance: "High - Similar to line coverage"
      }
    ]
    
    analysis.each do |metric|
      puts "#{metric[:metric]}:"
      puts "  Description: #{metric[:description]}"
      puts "  Target: #{metric[:target]}"
      puts "  Importance: #{metric[:importance]}"
      puts
    end
    
    puts "Coverage Goals:"
    goals = [
      "90%+ line coverage for critical code",
      "80%+ branch coverage for complex logic",
      "95%+ method coverage for public APIs",
      "100% coverage for security-critical code",
      "70%+ coverage for configuration code"
    ]
    
    goals.each { |goal| puts "• #{goal}" }
  end
  
  def self.coverage_improvement
    puts "\nCoverage Improvement Strategies:"
    puts "=" * 50
    
    strategies = [
      {
        strategy: "Identify uncovered code",
        description: "Find code not covered by tests",
        steps: [
          "Generate coverage report",
          "Review uncovered files",
          "Prioritize critical code",
          "Write tests for uncovered code"
        ]
      },
      {
        strategy: "Test edge cases",
        description: "Test boundary conditions and error cases",
        steps: [
          "Test with nil values",
          "Test with empty collections",
          "Test with maximum/minimum values",
          "Test error conditions"
        ]
      },
      {
        strategy: "Use test doubles",
        description: "Test code that depends on external systems",
        steps: [
          "Identify external dependencies",
          "Create test doubles",
          "Test with mocked dependencies",
          "Verify interactions"
        ]
      },
      {
        strategy: "Refactor for testability",
        description: "Make code easier to test",
        steps: [
          "Extract dependencies",
          "Create smaller methods",
          "Reduce complexity",
          "Add injection points"
        ]
      }
    ]
    
    strategies.each do |strategy|
      puts "#{strategy[:strategy]}:"
      puts "  Description: #{strategy[:description]}"
      puts "  Steps: #{strategy[:steps].join(', ')}"
      puts
    end
  end
  
  def self.coverage_automation
    puts "\nCoverage Automation:"
    puts "=" * 50
    
    automation = <<~RUBY
      # .github/workflows/coverage.yml
      name: Coverage Check
      on: [push, pull_request]
      
      jobs:
        test:
          runs-on: ubuntu-latest
          
          steps:
            - uses: actions/checkout@v2
            
            - name: Set up Ruby
              uses: ruby/setup-ruby@v1
              with:
                ruby-version: 3.0.0
                bundler-cache: true
            
            - name: Install dependencies
              run: bundle install
            
            - name: Run tests with coverage
              run: bundle exec rspec --format documentation --format RspecJunitFormatter --out coverage/rspec.xml
            
            - name: Upload coverage to Codecov
              uses: codecov/codecov-action@v1
              with:
                file: ./coverage/.resultset.json
                flags: unittests
                name: codecov-umbrella
                fail_ci_if_error: false
    RUBY
    
    puts "Coverage Automation Example:"
    puts automation
    
    puts "\nCoverage Monitoring:"
    monitoring = [
      "Set up coverage thresholds in CI",
      "Fail builds if coverage drops",
      "Monitor coverage trends over time",
      "Alert on significant coverage changes",
      "Integrate with pull request reviews",
      "Generate coverage badges"
    ]
    
    monitoring.each { |item| puts "• #{item}" }
  end
  
  # Run coverage examples
  coverage_tools
  coverage_analysis
  coverage_improvement
  coverage_automation
end
```

## 🎯 Testing Best Practices

### 1. Testing Guidelines

Comprehensive testing best practices:

```ruby
class TestingBestPractices
  def self.testing_principles
    puts "Testing Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "FIRST",
        description: "Fast, Independent, Repeatable, Self-validating, Timely",
        details: [
          "Fast - Tests should run quickly",
          "Independent - Tests should not depend on each other",
          "Repeatable - Tests should produce same results",
          "Self-validating - Tests should have clear pass/fail",
          "Timely - Tests should be written with code"
        ]
      },
      {
        principle: "Arrange-Act-Assert",
        description: "Structure tests with clear phases",
        details: [
          "Arrange - Set up test data and conditions",
          "Act - Execute the code being tested",
          "Assert - Verify the expected outcome"
        ]
      },
      {
        principle: "Single Responsibility",
        description: "Each test should test one thing",
        details: [
          "One assertion per test (ideally)",
          "Test one behavior at a time",
          "Clear, focused test names",
          "Descriptive failure messages"
        ]
      },
      {
        principle: "Test Isolation",
        description: "Tests should be independent",
        details: [
          "No shared state between tests",
          "Clean setup and teardown",
          "Deterministic results",
          "No external dependencies"
        ]
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}: #{principle[:description]}"
      principle[:details].each { |detail| puts "  • #{detail}" }
      puts
    end
  end
  
  def self.test_organization
    puts "\nTest Organization:"
    puts "=" * 50
    
    organization = [
      {
        area: "Unit Tests",
        location: "spec/models/, spec/services/, spec/lib/",
        focus: "Individual components",
        examples: ["Model validation", "Service methods", "Utility functions"]
      },
      {
        area: "Integration Tests",
        location: "spec/integration/, spec/requests/",
        focus: "Component interactions",
        examples: ["API endpoints", "Database operations", "Service integration"]
      },
      {
        area: "System Tests",
        location: "spec/system/, spec/features/",
        focus: "Complete workflows",
        examples: ["User registration", "Payment processing", "File upload"]
      },
      {
        area: "Performance Tests",
        location: "spec/performance/",
        focus: "Performance characteristics",
        examples: ["Load testing", "Stress testing", "Memory usage"]
      },
      {
        area: "Security Tests",
        location: "spec/security/",
        focus: "Security vulnerabilities",
        examples: ["Authentication", "Authorization", "Data validation"]
      }
    ]
    
    organization.each do |area|
      puts "#{area[:area]}:"
      puts "  Location: #{area[:location]}"
      puts "  Focus: #{area[:focus]}"
      puts "  Examples: #{area[:examples].join(', ')}"
      puts
    end
  end
  
  def self.maintainable_tests
    puts "\nMaintainable Tests:"
    puts "=" * 50
    
    guidelines = [
      {
        guideline: "Use descriptive test names",
        description: "Test names should explain what is being tested",
        example: "it 'returns user profile when user exists' do ... end"
      },
      {
        guideline: "Keep tests simple",
        description: "Tests should be easy to read and understand",
        example: "Use helper methods for complex setup"
      },
      {
        guideline: "Use test factories",
        description: "Use factories instead of fixtures for flexibility",
        example: "create(:user, :admin) instead of fixture data"
      },
      {
        guideline: "Use shared examples",
        description: "Reuse common test patterns",
        example: "it_behaves_like 'authenticated user' do ... end"
      },
      {
        guideline: "Test error conditions",
        description: "Test both success and failure cases",
        example: "test both valid and invalid input"
      },
      {
        guideline: "Use appropriate assertions",
        description: "Use specific assertions for clarity",
        example: "assert_equal instead of assert true for comparisons"
      },
      {
        guideline: "Document complex tests",
        description: "Add comments for complex test logic",
        example: "Explain why certain setup is needed"
      },
      {
        guideline: "Regular maintenance",
        description: "Keep tests updated with code changes",
        example: "Update tests when refactoring code"
      }
    ]
    
    guidelines.each do |guideline|
      puts "#{guideline[:guideline]}:"
      puts "  Description: #{guideline[:description]}"
      puts "  Example: #{guideline[:example]}"
      puts
    end
  end
  
  def self.test_antipatterns
    puts "\nTest Anti-patterns:"
    puts "=" * 50
    
    antipatterns = [
      {
        pattern: "Testing implementation details",
        problem: "Tests break when implementation changes",
        solution: "Test behavior, not implementation"
      },
      {
        pattern: "Shared state between tests",
        problem: "Tests depend on each other",
        solution: "Use proper setup/teardown"
      },
      {
        pattern: "Overly complex tests",
        problem: "Tests are hard to read and maintain",
        solution: "Break into smaller, focused tests"
      },
      {
        pattern: "Testing multiple things",
        problem: "Unclear what is being tested",
        solution: "One assertion per test"
      },
      {
        pattern: "Hard-coded test data",
        problem: "Tests are inflexible",
        solution: "Use factories and builders"
      },
      {
        pattern: "No error testing",
        problem: "Only happy path is tested",
        solution: "Test edge cases and error conditions"
      },
      {
        pattern: "Slow tests",
        problem: "Tests take too long to run",
        solution: "Use test doubles, optimize setup"
      },
      {
        pattern: "Flaky tests",
        problem: "Tests sometimes fail randomly",
        solution: "Fix timing issues, improve isolation"
      }
    ]
    
    antipatterns.each do |antipattern|
      puts "#{antipattern[:pattern]}:"
      puts "  Problem: #{antipattern[:problem]}"
      puts "  Solution: #{antipattern[:solution]}"
      puts
    end
  end
  
  # Run best practices examples
  testing_principles
  test_organization
  maintainable_tests
  test_antipatterns
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic RSpec**: Write simple RSpec tests
2. **Minitest**: Create Minitest test cases
3. **Test Structure**: Organize tests properly

### Intermediate Exercises

1. **TDD Workflow**: Practice test-driven development
2. **Test Coverage**: Measure and improve coverage
3. **Test Doubles**: Use mocks and stubs effectively

### Advanced Exercises

1. **Integration Testing**: Build integration test suite
2. **Performance Testing**: Create performance tests
3. **Testing Strategy**: Design comprehensive testing strategy

---

## 🎯 Summary

Testing strategies in Ruby provide:

- **Testing Fundamentals** - Understanding test types and pyramid
- **Testing Frameworks** - RSpec, Minitest, Cucumber
- **Test-Driven Development** - TDD workflow and practices
- **Test Coverage** - Measuring and improving coverage
- **Testing Best Practices** - Writing maintainable tests

Master these strategies to build reliable, well-tested Ruby applications!
