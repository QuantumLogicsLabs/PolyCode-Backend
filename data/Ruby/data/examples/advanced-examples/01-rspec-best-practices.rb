# RSpec Best Practices in Ruby
# This file demonstrates comprehensive RSpec testing patterns, best practices,
# and advanced testing techniques for Ruby applications.

require 'rspec'
require 'rspec/expectations'
require 'rspec/mocks'
require 'factory_bot'
require 'faker'

module TestingExamples
  module RSpecBestPractices
    # 1. Test Structure and Organization
    # Proper RSpec file structure and organization
    
    class User
      attr_accessor :name, :email, :age, :active
      
      def initialize(name:, email:, age:)
        @name = name
        @email = email
        @age = age
        @active = true
      end
      
      def activate!
        @active = true
      end
      
      def deactivate!
        @active = false
      end
      
      def adult?
        @age >= 18
      end
      
      def send_welcome_email
        # Simulate sending email
        puts "Welcome email sent to #{@email}"
      end
      
      def self.find_by_email(email)
        # Simulate database lookup
        new(name: "John", email: email, age: 25)
      end
    end
    
    class Order
      attr_accessor :user, :items, :status, :total_amount
      
      def initialize(user:)
        @user = user
        @items = []
        @status = 'pending'
        @total_amount = 0
      end
      
      def add_item(item)
        @items << item
        calculate_total
      end
      
      def calculate_total
        @total_amount = @items.sum(&:price)
      end
      
      def process!
        return false unless @user.adult?
        return false if @items.empty?
        
        @status = 'processed'
        @user.send_welcome_email if @user.active?
        true
      end
      
      def cancel!
        @status = 'cancelled'
      end
    end
    
    class Item
      attr_accessor :name, :price
      
      def initialize(name:, price:)
        @name = name
        @price = price
      end
    end
    
    # 2. Model Testing Best Practices
    # Comprehensive model testing with proper setup and teardown
    
    RSpec.describe User, type: :model do
      # Use descriptive context blocks
      describe 'attributes' do
        it { is_expected.to respond_to(:name) }
        it { is_expected.to respond_to(:email) }
        it { is_expected.to respond_to(:age) }
        it { is_expected.to respond_to(:active) }
      end
      
      describe 'initialization' do
        context 'with valid attributes' do
          let(:user) { User.new(name: 'John Doe', email: 'john@example.com', age: 25) }
          
          it 'sets the name correctly' do
            expect(user.name).to eq('John Doe')
          end
          
          it 'sets the email correctly' do
            expect(user.email).to eq('john@example.com')
          end
          
          it 'sets the age correctly' do
            expect(user.age).to eq(25)
          end
          
          it 'sets active to true by default' do
            expect(user.active).to be true
          end
        end
      end
      
      describe 'instance methods' do
        let(:user) { User.new(name: 'John Doe', email: 'john@example.com', age: 25) }
        
        describe '#adult?' do
          context 'when user is 18 or older' do
            it 'returns true' do
              expect(user.adult?).to be true
            end
          end
          
          context 'when user is younger than 18' do
            let(:user) { User.new(name: 'Jane Doe', email: 'jane@example.com', age: 16) }
            
            it 'returns false' do
              expect(user.adult?).to be false
            end
          end
        end
        
        describe '#activate!' do
          context 'when user is inactive' do
            before { user.deactivate! }
            
            it 'activates the user' do
              expect { user.activate! }.to change(user, :active).from(false).to(true)
            end
          end
          
          context 'when user is already active' do
            it 'keeps the user active' do
              expect { user.activate! }.not_to change(user, :active)
            end
          end
        end
        
        describe '#deactivate!' do
          it 'deactivates the user' do
            expect { user.deactivate! }.to change(user, :active).from(true).to(false)
          end
        end
        
        describe '#send_welcome_email' do
          it 'sends a welcome email' do
            expect(user).to receive(:puts).with("Welcome email sent to #{user.email}")
            user.send_welcome_email
          end
        end
      end
      
      describe 'class methods' do
        describe '.find_by_email' do
          it 'returns a user with the given email' do
            user = User.find_by_email('test@example.com')
            
            expect(user).to be_a(User)
            expect(user.email).to eq('test@example.com')
          end
        end
      end
    end
    
    # 3. Controller Testing Best Practices
    # Testing controller actions with proper request/response handling
    
    class UsersController
      def initialize
        @users = []
      end
      
      def create(params)
        user = User.new(
          name: params[:name],
          email: params[:email],
          age: params[:age]
        )
        
        if user.save
          render json: { user: user }, status: :created
        else
          render json: { errors: user.errors }, status: :unprocessable_entity
        end
      end
      
      def show(id)
        user = User.find_by_id(id)
        
        if user
          render json: { user: user }
        else
          render json: { error: 'User not found' }, status: :not_found
        end
      end
      
      def update(id, params)
        user = User.find_by_id(id)
        
        if user
          if user.update(params)
            render json: { user: user }
          else
            render json: { errors: user.errors }, status: :unprocessable_entity
          end
        else
          render json: { error: 'User not found' }, status: :not_found
        end
      end
      
      def destroy(id)
        user = User.find_by_id(id)
        
        if user
          user.destroy
          render json: { message: 'User deleted successfully' }
        else
          render json: { error: 'User not found' }, status: :not_found
        end
      end
      
      private
      
      def render(options)
        @response = options
      end
      
      def params
        @params ||= {}
      end
    end
    
    RSpec.describe UsersController, type: :controller do
      let(:controller) { UsersController.new }
      
      describe '#create' do
        context 'with valid parameters' do
          let(:params) do
            {
              name: 'John Doe',
              email: 'john@example.com',
              age: 25
            }
          end
          
          before do
            allow(User).to receive(:new).and_return(user)
            allow(user).to receive(:save).and_return(true)
          end
          
          let(:user) { instance_double(User, id: 1, name: 'John Doe') }
          
          it 'creates a new user' do
            expect(User).to receive(:new).with(params).and_return(user)
            controller.create(params)
          end
          
          it 'returns a created status' do
            controller.create(params)
            expect(controller.instance_variable_get(:@response)[:status]).to eq(:created)
          end
          
          it 'returns the user in the response' do
            controller.create(params)
            response = controller.instance_variable_get(:@response)
            expect(response[:json][:user]).to eq(user)
          end
        end
        
        context 'with invalid parameters' do
          let(:params) do
            {
              name: '',
              email: 'invalid-email',
              age: -5
            }
          end
          
          before do
            allow(User).to receive(:new).and_return(user)
            allow(user).to receive(:save).and_return(false)
            allow(user).to receive(:errors).and_return(['Name cannot be blank'])
          end
          
          let(:user) { instance_double(User, errors: ['Name cannot be blank']) }
          
          it 'returns unprocessable entity status' do
            controller.create(params)
            expect(controller.instance_variable_get(:@response)[:status]).to eq(:unprocessable_entity)
          end
          
          it 'returns the errors in the response' do
            controller.create(params)
            response = controller.instance_variable_get(:@response)
            expect(response[:json][:errors]).to eq(['Name cannot be blank'])
          end
        end
      end
      
      describe '#show' do
        context 'when user exists' do
          let(:user) { instance_double(User, id: 1, name: 'John Doe') }
          
          before do
            allow(User).to receive(:find_by_id).with('1').and_return(user)
          end
          
          it 'finds the user' do
            expect(User).to receive(:find_by_id).with('1')
            controller.show('1')
          end
          
          it 'returns the user in the response' do
            controller.show('1')
            response = controller.instance_variable_get(:@response)
            expect(response[:json][:user]).to eq(user)
          end
        end
        
        context 'when user does not exist' do
          before do
            allow(User).to receive(:find_by_id).with('999').and_return(nil)
          end
          
          it 'returns not found status' do
            controller.show('999')
            expect(controller.instance_variable_get(:@response)[:status]).to eq(:not_found)
          end
          
          it 'returns an error message' do
            controller.show('999')
            response = controller.instance_variable_get(:@response)
            expect(response[:json][:error]).to eq('User not found')
          end
        end
      end
    end
    
    # 4. Feature Testing with Capybara
    # Integration testing with Capybara-style browser simulation
    
    class BrowserSimulator
      def initialize
        @current_page = nil
        @history = []
      end
      
      def visit(url)
        @history << url
        @current_page = url
        puts "Visiting: #{url}"
      end
      
      def fill_in(field, with:)
        puts "Filling in #{field} with: #{with}"
      end
      
      def click_button(text)
        puts "Clicking button: #{text}"
      end
      
      def click_link(text)
        puts "Clicking link: #{text}"
      end
      
      def has_content?(text)
        puts "Checking for content: #{text}"
        true
      end
      
      def has_css?(selector)
        puts "Checking for CSS: #{selector}"
        true
      end
      
      def current_path
        @current_page
      end
    end
    
    RSpec.describe 'User registration flow', type: :feature do
      let(:browser) { BrowserSimulator.new }
      
      it 'allows a user to register successfully' do
        # Visit registration page
        browser.visit('/register')
        
        # Fill in registration form
        browser.fill_in('Name', with: 'John Doe')
        browser.fill_in('Email', with: 'john@example.com')
        browser.fill_in('Password', with: 'password123')
        browser.fill_in('Age', with: '25')
        
        # Submit form
        browser.click_button('Register')
        
        # Verify successful registration
        expect(browser.current_path).to eq('/dashboard')
        expect(browser).to have_content('Welcome, John Doe!')
        expect(browser).to have_content('Your account has been created successfully')
      end
      
      it 'shows validation errors for invalid data' do
        # Visit registration page
        browser.visit('/register')
        
        # Submit empty form
        browser.click_button('Register')
        
        # Verify validation errors
        expect(browser).to have_content('Name cannot be blank')
        expect(browser).to have_content('Email cannot be blank')
        expect(browser).to have_content('Age must be greater than 0')
      end
      
      it 'prevents duplicate email registration' do
        # Create existing user
        User.create(name: 'Existing User', email: 'existing@example.com', age: 30)
        
        # Visit registration page
        browser.visit('/register')
        
        # Fill in form with existing email
        browser.fill_in('Name', with: 'New User')
        browser.fill_in('Email', with: 'existing@example.com')
        browser.fill_in('Password', with: 'password123')
        browser.fill_in('Age', with: '25')
        
        # Submit form
        browser.click_button('Register')
        
        # Verify error message
        expect(browser).to have_content('Email has already been taken')
      end
    end
    
    # 5. Mocking and Stubbing Best Practices
    # Proper use of mocks, stubs, and test doubles
    
    RSpec.describe 'External API Integration' do
      let(:api_client) { instance_double(ExternalAPIClient) }
      
      before do
        # Use allow to stub external dependencies
        allow(ExternalAPIClient).to receive(:new).and_return(api_client)
      end
      
      describe 'fetching user data' do
        context 'when API call succeeds' do
          let(:user_data) do
            {
              id: 1,
              name: 'John Doe',
              email: 'john@example.com'
            }
          end
          
          before do
            allow(api_client).to receive(:get_user).with(1).and_return(user_data)
          end
          
          it 'returns user data from API' do
            service = UserService.new
            result = service.fetch_user(1)
            
            expect(result[:name]).to eq('John Doe')
            expect(result[:email]).to eq('john@example.com')
          end
          
          it 'calls the API with correct parameters' do
            service = UserService.new
            service.fetch_user(1)
            
            expect(api_client).to have_received(:get_user).with(1)
          end
        end
        
        context 'when API call fails' do
          before do
            allow(api_client).to receive(:get_user).with(1).and_raise(StandardError, 'API Error')
          end
          
          it 'handles the error gracefully' do
            service = UserService.new
            
            expect { service.fetch_user(1) }.not_to raise_error
            expect(service.fetch_user(1)).to be_nil
          end
        end
      end
    end
    
    # 6. Test Data Management with Factory Bot
    # Using Factory Bot for test data generation
    
    FactoryBot.define do
      factory :user do
        name { Faker::Name.name }
        email { Faker::Internet.email }
        age { Faker::Number.between(18, 80) }
        active { true }
      end
      
      factory :order do
        association :user
        
        trait :pending do
          status { 'pending' }
        end
        
        trait :processed do
          status { 'processed' }
        end
        
        trait :with_items do
          after(:build) do |order|
            3.times { order.add_item(build(:item)) }
          end
        end
      end
      
      factory :item do
        name { Faker::Commerce.product_name }
        price { Faker::Commerce.price }
      end
    end
    
    RSpec.describe 'Order Processing' do
      let(:user) { create(:user, age: 25) }
      
      describe 'order processing' do
        context 'with valid order' do
          let(:order) { build(:order, user: user, :with_items) }
          
          it 'processes the order successfully' do
            expect(order.process!).to be true
            expect(order.status).to eq('processed')
          end
          
          it 'calculates total amount correctly' do
            expect(order.total_amount).to be > 0
            expect(order.total_amount).to eq(order.items.sum(&:price))
          end
        end
        
        context 'with underage user' do
          let(:underage_user) { create(:user, age: 16) }
          let(:order) { build(:order, user: underage_user, :with_items) }
          
          it 'fails to process the order' do
            expect(order.process!).to be false
            expect(order.status).to eq('pending')
          end
        end
        
        context 'with empty order' do
          let(:order) { build(:order, user: user) }
          
          it 'fails to process the order' do
            expect(order.process!).to be false
            expect(order.status).to eq('pending')
          end
        end
      end
    end
    
    # 7. Performance Testing
    # Testing performance characteristics and benchmarks
    
    RSpec.describe 'Performance Tests' do
      describe 'user creation performance' do
        it 'creates users within acceptable time limit' do
          start_time = Time.current
          
          100.times do
            User.create(
              name: Faker::Name.name,
              email: Faker::Internet.email,
              age: Faker::Number.between(18, 80)
            )
          end
          
          end_time = Time.current
          duration = end_time - start_time
          
          expect(duration).to be < 1.0 # Should complete within 1 second
        end
      end
      
      describe 'query performance' do
        let!(:users) do
          1000.times.map do
            User.create(
              name: Faker::Name.name,
              email: Faker::Internet.email,
              age: Faker::Number.between(18, 80)
            )
          end
        end
        
        it 'queries users efficiently' do
          start_time = Time.current
          
          adult_users = users.select(&:adult?)
          
          end_time = Time.current
          duration = end_time - start_time
          
          expect(duration).to be < 0.1 # Should complete within 100ms
          expect(adult_users.length).to be > 0
        end
      end
    end
    
    # 8. Shared Examples and Contexts
    # Reusable test components and shared contexts
    
    RSpec.shared_examples 'a valid user' do
      it 'has a valid name' do
        expect(user.name).not_to be_blank
      end
      
      it 'has a valid email' do
        expect(user.email).to match(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
      end
      
      it 'has a valid age' do
        expect(user.age).to be_between(0, 150)
      end
    end
    
    RSpec.shared_context 'with authentication' do
      let(:user) { create(:user) }
      let(:token) { 'auth_token_123' }
      
      before do
        allow(AuthenticationService).to receive(:authenticate).with(token).and_return(user)
      end
    end
    
    RSpec.describe 'User Authentication', type: :request do
      context 'with valid credentials' do
        let(:user) { create(:user) }
        let(:token) { 'valid_token' }
        
        before do
          allow(AuthenticationService).to receive(:authenticate).with(token).and_return(user)
        end
        
        it 'authenticates the user' do
          expect(AuthenticationService).to receive(:authenticate).with(token)
          
          get '/api/profile', headers: { 'Authorization' => "Bearer #{token}" }
          
          expect(response).to have_http_status(:ok)
          expect(JSON.parse(response.body)['user']['id']).to eq(user.id)
        end
      end
      
      context 'with invalid credentials' do
        let(:token) { 'invalid_token' }
        
        before do
          allow(AuthenticationService).to receive(:authenticate).with(token).and_return(nil)
        end
        
        it 'returns unauthorized status' do
          get '/api/profile', headers: { 'Authorization' => "Bearer #{token}" }
          
          expect(response).to have_http_status(:unauthorized)
        end
      end
    end
    
    # 9. Custom Matchers
    # Custom RSpec matchers for domain-specific assertions
    
    RSpec::Matchers.define :be_adult do
      match do |actual|
        actual.respond_to?(:age) && actual.age >= 18
      end
      
      failure_message do |actual|
        "expected #{actual} to be an adult (age >= 18)"
      end
      
      failure_message_when_negated do |actual|
        "expected #{actual} not to be an adult (age < 18)"
      end
    end
    
    RSpec::Matchers.define :have_valid_email do
      match do |actual|
        actual.respond_to?(:email) && actual.email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
      end
      
      failure_message do |actual|
        "expected #{actual} to have a valid email address"
      end
      
      failure_message_when_negated do |actual|
        "expected #{actual} not to have a valid email address"
      end
    end
    
    RSpec::Matchers.define :contain_sensitive_information do
      match do |actual|
        sensitive_patterns = [
          /password/i,
          /secret/i,
          /token/i,
          /api_key/i,
          /credit_card/i
        ]
        
        sensitive_patterns.any? { |pattern| actual.match?(pattern) }
      end
      
      failure_message do |actual|
        "expected #{actual} to contain sensitive information"
      end
      
      failure_message_when_negated do |actual|
        "expected #{actual} not to contain sensitive information"
      end
    end
    
    RSpec.describe 'Custom Matchers Usage' do
      let(:user) { User.new(name: 'John Doe', email: 'john@example.com', age: 25) }
      let(:log_message) { 'User password: secret123' }
      
      it 'uses custom matcher for age validation' do
        expect(user).to be_adult
      end
      
      it 'uses custom matcher for email validation' do
        expect(user).to have_valid_email
      end
      
      it 'uses custom matcher for sensitive information' do
        expect(log_message).to contain_sensitive_information
      end
    end
    
    # 10. Test Helpers and Utilities
    # Reusable test helper methods and utilities
    
    module TestHelpers
      def create_test_user(overrides = {})
        default_attrs = {
          name: 'Test User',
          email: 'test@example.com',
          age: 25,
          active: true
        }
        
        User.new(default_attrs.merge(overrides))
      end
      
      def create_test_order(user, overrides = {})
        default_attrs = {
          status: 'pending',
          items: []
        }
        
        Order.new(user: user).tap do |order|
          default_attrs.merge(overrides).each do |key, value|
            order.send("#{key}=", value)
          end
        end
      end
      
      def assert_json_response(response, expected_status = :ok)
        expect(response).to have_http_status(expected_status)
        expect(response.content_type).to eq('application/json')
        
        begin
          JSON.parse(response.body)
        rescue JSON::ParserError
          flunk 'Response body is not valid JSON'
        end
      end
      
      def assert_error_response(response, expected_error)
        assert_json_response(response, :unprocessable_entity)
        
        json_response = JSON.parse(response.body)
        expect(json_response['errors']).to include(expected_error)
      end
      
      def wait_for_ajax(timeout = 5)
        start_time = Time.current
        
        while Time.current - start_time < timeout
          break if page.evaluate_script('jQuery.active === 0')
          sleep 0.1
        end
        
        raise 'AJAX requests did not complete within timeout' if page.evaluate_script('jQuery.active > 0')
      end
      
      def login_as(user)
        visit '/login'
        fill_in 'Email', with: user.email
        fill_in 'Password', with: 'password'
        click_button 'Login'
        
        expect(page).to have_content('Welcome')
      end
      
      def logout
        click_link 'Logout'
        expect(page).to have_content('Goodbye')
      end
    end
    
    RSpec.configure do |config|
      config.include TestHelpers
    end
    
    # 11. Test Coverage and Quality Metrics
    # Ensuring comprehensive test coverage
    
    RSpec.describe 'Test Coverage' do
      it 'covers all public methods' do
        user = create_test_user
        
        # Test all public methods
        expect(user).to respond_to(:name)
        expect(user).to respond_to(:email)
        expect(user).to respond_to(:age)
        expect(user).to respond_to(:active)
        expect(user).to respond_to(:adult?)
        expect(user).to respond_to(:activate!)
        expect(user).to respond_to(:deactivate!)
        expect(user).to respond_to(:send_welcome_email)
      end
      
      it 'covers all edge cases' do
        # Test edge cases for age validation
        user_under_18 = create_test_user(age: 17)
        user_exactly_18 = create_test_user(age: 18)
        user_over_18 = create_test_user(age: 19)
        
        expect(user_under_18).not_to be_adult
        expect(user_exactly_18).to be_adult
        expect(user_over_18).to be_adult
      end
      
      it 'covers error conditions' do
        # Test invalid email format
        user = create_test_user(email: 'invalid-email')
        
        expect { user.validate! }.to raise_error(ValidationError)
      end
    end
    
    # 12. Parallel Testing and Optimization
    # Running tests in parallel for better performance
    
    RSpec.describe 'Parallel Testing' do
      it 'runs tests independently' do
        # Each test should be independent and not rely on shared state
        user = create_test_user
        
        expect(user.name).not_to be_blank
        expect(user.email).not_to be_blank
      end
      
      it 'cleans up after each test' do
        # Tests should clean up any created data
        user = create_test_user
        
        # Test logic here
        expect(user).to be_valid
        
        # Cleanup should happen automatically
        expect(User.count).to eq(0)
      end
      
      it 'uses database transactions for isolation' do
        # Use transactions to ensure test isolation
        ActiveRecord::Base.transaction do
          user = create_test_user
          
          expect(user).to be_valid
          
          # Rollback transaction
          raise ActiveRecord::Rollback
        end
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "RSpec Best Practices Demonstration"
  puts "=" * 60
  
  # Demonstrate test structure
  puts "\n1. Test Structure:"
  puts "✅ Descriptive context blocks"
  puts "✅ Proper setup and teardown"
  puts "✅ Clear test descriptions"
  
  # Demonstrate assertions
  puts "\n2. Assertions:"
  puts "✅ RSpec matchers"
  puts "✅ Custom matchers"
  puts "✅ Expectation syntax"
  
  # Demonstrate mocking
  puts "\n3. Mocking and Stubbing:"
  puts "✅ Test doubles"
  puts "✅ Method stubbing"
  puts "✅ External service mocking"
  
  # Demonstrate test data
  puts "\n4. Test Data Management:"
  puts "✅ Factory Bot usage"
  puts "✅ Test fixtures"
  puts "✅ Data cleanup"
  
  # Demonstrate coverage
  puts "\n5. Test Coverage:"
  puts "✅ Edge case testing"
  puts "✅ Error condition testing"
  puts "✅ Integration testing"
  
  puts "\nRSpec best practices help write comprehensive and maintainable tests!"
end
