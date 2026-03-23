# Test Doubles and Mocks in Ruby
# This file demonstrates comprehensive testing with test doubles, mocks,
# stubs, and other testing patterns for Ruby applications.

require 'rspec/mocks/verifying_doubles'
require 'rspec/mocks/double'

module TestingExamples
  module TestDoublesAndMocks
    # 1. Test Doubles - Objects that stand in for real objects during testing
    
    class PaymentProcessor
      def initialize(gateway)
        @gateway = gateway
      end
      
      def process_payment(amount, card_details)
        result = @gateway.charge(amount, card_details)
        
        if result.success?
          PaymentResult.new(success: true, transaction_id: result.transaction_id)
        else
          PaymentResult.new(success: false, error: result.error_message)
        end
      end
    end
    
    class PaymentGateway
      def charge(amount, card_details)
        # Real implementation would call external payment service
        if card_details[:number].start_with?('4')
          PaymentResult.new(success: true, transaction_id: generate_transaction_id)
        else
          PaymentResult.new(success: false, error: 'Invalid card number')
        end
      end
      
      private
      
      def generate_transaction_id
        "txn_#{Time.current.to_i}_#{rand(1000)}"
      end
    end
    
    class PaymentResult
      attr_reader :success, :transaction_id, :error
      
      def initialize(success:, transaction_id: nil, error: nil)
        @success = success
        @transaction_id = transaction_id
        @error = error
      end
    end
    
    # 2. Test Double Implementation
    # Simple test double for PaymentGateway
    
    class PaymentGatewayDouble
      def initialize
        @transactions = {}
        @success_rate = 1.0
      end
      
      def charge(amount, card_details)
        transaction_id = generate_transaction_id
        @transactions[transaction_id] = { amount: amount, card_details: card_details }
        
        if rand <= @success_rate
          PaymentResult.new(success: true, transaction_id: transaction_id)
        else
          PaymentResult.new(success: false, error: 'Payment declined')
        end
      end
      
      def transaction_exists?(transaction_id)
        @transactions.key?(transaction_id)
      end
      
      def get_transaction(transaction_id)
        @transactions[transaction_id]
      end
      
      def set_success_rate(rate)
        @success_rate = rate
      end
      
      private
      
      def generate_transaction_id
        "test_txn_#{Time.current.to_i}_#{rand(1000)}"
      end
    end
    
    # 3. Mock Object Implementation
    # Mock object that verifies interactions
    
    class NotificationServiceMock
      def initialize
        @notifications_sent = []
        @email_sent = []
        @sms_sent = []
      end
      
      def send_notification(user, message, type = :email)
        @notifications_sent << { user: user, message: message, type: type }
        
        case type
        when :email
          @email_sent << { user: user, message: message }
        when :sms
          @sms_sent << { user: user, message: message }
        end
      end
      
      def notifications_sent
        @notifications_sent
      end
      
      def email_sent
        @email_sent
      end
      
      def sms_sent
        @sms_sent
      end
      
      def notification_sent_to?(user, message, type = nil)
        notification = @notifications_sent.find { |n| n[:user] == user && n[:message] == message }
        return false unless notification
        
        type.nil? || notification[:type] == type
      end
      
      def reset
        @notifications_sent.clear
        @email_sent.clear
        @sms_sent.clear
      end
    end
    
    # 4. Stub Implementation
    # Stub object that returns predefined responses
    
    class UserRepositoryStub
      def initialize
        @users = {}
        @find_by_email_responses = {}
        @save_responses = {}
      end
      
      def find(id)
        @users[id]
      end
      
      def find_by_email(email)
        @find_by_email_responses[email] || @users.values.find { |u| u.email == email }
      end
      
      def save(user)
        @users[user.id] = user
        @save_responses[user.id] || true
      end
      
      def delete(user)
        @users.delete(user.id)
      end
      
      def all
        @users.values
      end
      
      # Stub configuration methods
      def stub_find_by_email(email, user)
        @find_by_email_responses[email] = user
      end
      
      def stub_save(user_id, success)
        @save_responses[user_id] = success
      end
      
      def add_user(user)
        @users[user.id] = user
      end
    end
    
    # 5. Fake Object Implementation
    # Fake object with working implementation for testing
    
    class FakeEmailService
      def initialize
        @sent_emails = []
        @delivery_failures = []
      end
      
      def send_email(to, subject, body)
        email = {
          to: to,
          subject: subject,
          body: body,
          sent_at: Time.current
        }
        
        if should_fail_delivery?(to)
          @delivery_failures << email
          false
        else
          @sent_emails << email
          true
        end
      end
      
      def emails_sent
        @sent_emails
      end
      
      def delivery_failures
        @delivery_failures
      end
      
      def email_sent_to?(to, subject = nil)
        email = @sent_emails.find { |e| e[:to] == to }
        return false unless email
        
        subject.nil? || email[:subject] == subject
      end
      
      def simulate_failure_for(email_address)
        @failure_emails ||= []
        @failure_emails << email_address
      end
      
      def clear_failures
        @failure_emails = []
        @delivery_failures.clear
      end
      
      private
      
      def should_fail_delivery?(to)
        @failure_emails&.include?(to)
      end
    end
    
    # 6. Spy Object Implementation
    # Spy object that records all interactions
    
    class UserServiceSpy
      def initialize(real_service = nil)
        @real_service = real_service
        @interactions = []
        @method_calls = Hash.new { |h, k| h[k] = [] }
      end
      
      def create_user(params)
        record_interaction(:create_user, params)
        
        if @real_service
          @real_service.create_user(params)
        else
          User.new(params)
        end
      end
      
      def update_user(user, params)
        record_interaction(:update_user, user, params)
        
        if @real_service
          @real_service.update_user(user, params)
        else
          user.update(params)
        end
      end
      
      def delete_user(user)
        record_interaction(:delete_user, user)
        
        if @real_service
          @real_service.delete_user(user)
        else
          user.destroy
        end
      end
      
      def interactions
        @interactions
      end
      
      def method_calls(method_name)
        @method_calls[method_name]
      end
      
      def was_called_with?(method_name, *args)
        @method_calls[method_name].any? do |call|
          call == args
        end
      end
      
      def was_called?(method_name)
        @method_calls[method_name].any?
      end
      
      def call_count(method_name)
        @method_calls[method_name].length
      end
      
      private
      
      def record_interaction(method, *args)
        interaction = {
          method: method,
          args: args,
          timestamp: Time.current
        }
        
        @interactions << interaction
        @method_calls[method] << args
      end
    end
    
    # 7. RSpec Mock Examples
    # Using RSpec's built-in mocking capabilities
    
    RSpec.describe 'Payment Processing with Mocks' do
      let(:gateway_double) { instance_double(PaymentGateway) }
      let(:processor) { PaymentProcessor.new(gateway_double) }
      
      describe 'processing successful payment' do
        let(:amount) { 100 }
        let(:card_details) { { number: '4111111111111111', cvv: '123', expiry: '12/25' } }
        let(:payment_result) { PaymentResult.new(success: true, transaction_id: 'txn_123') }
        
        before do
          allow(gateway_double).to receive(:charge).with(amount, card_details).and_return(payment_result)
        end
        
        it 'charges the payment gateway' do
          processor.process_payment(amount, card_details)
          
          expect(gateway_double).to have_received(:charge).with(amount, card_details)
        end
        
        it 'returns a successful payment result' do
          result = processor.process_payment(amount, card_details)
          
          expect(result.success).to be true
          expect(result.transaction_id).to eq('txn_123')
        end
      end
      
      describe 'processing failed payment' do
        let(:amount) { 100 }
        let(:card_details) { { number: '5111111111111111', cvv: '123', expiry: '12/25' } }
        let(:payment_result) { PaymentResult.new(success: false, error: 'Invalid card number') }
        
        before do
          allow(gateway_double).to receive(:charge).with(amount, card_details).and_return(payment_result)
        end
        
        it 'returns a failed payment result' do
          result = processor.process_payment(amount, card_details)
          
          expect(result.success).to be false
          expect(result.error).to eq('Invalid card number')
        end
      end
    end
    
    RSpec.describe 'User Service with Spies' do
      let(:real_service) { UserService.new }
      let(:spy_service) { UserServiceSpy.new(real_service) }
      
      describe 'creating a user' do
        let(:user_params) { { name: 'John Doe', email: 'john@example.com', age: 25 } }
        
        it 'records the method call' do
          spy_service.create_user(user_params)
          
          expect(spy_service).to have_received(:create_user).with(user_params)
        end
        
        it 'records the interaction details' do
          spy_service.create_user(user_params)
          
          interactions = spy_service.interactions
          expect(interactions.length).to eq(1)
          expect(interactions.first[:method]).to eq(:create_user)
          expect(interactions.first[:args]).to eq([user_params])
        end
        
        it 'returns a user object' do
          user = spy_service.create_user(user_params)
          
          expect(user).to be_a(User)
          expect(user.name).to eq('John Doe')
        end
      end
    end
    
    RSpec.describe 'Email Service with Fakes' do
      let(:email_service) { FakeEmailService.new }
      
      describe 'sending emails' do
        let(:to) { 'user@example.com' }
        let(:subject) { 'Welcome!' }
        let(:body) { 'Welcome to our service!' }
        
        it 'sends the email successfully' do
          result = email_service.send_email(to, subject, body)
          
          expect(result).to be true
        end
        
        it 'records the sent email' do
          email_service.send_email(to, subject, body)
          
          expect(email_service.email_sent_to?(to, subject)).to be true
        end
        
        it 'handles delivery failures' do
          email_service.simulate_failure_for(to)
          
          result = email_service.send_email(to, subject, body)
          
          expect(result).to be false
          expect(email_service.delivery_failures.length).to eq(1)
        end
      end
    end
    
    RSpec.describe 'User Repository with Stubs' do
      let(:repository) { UserRepositoryStub.new }
      let(:user) { User.new(id: 1, name: 'John Doe', email: 'john@example.com', age: 25) }
      
      before do
        repository.add_user(user)
      end
      
      describe 'finding users' do
        it 'finds user by id' do
          found_user = repository.find(1)
          
          expect(found_user).to eq(user)
        end
        
        it 'finds user by email' do
          found_user = repository.find_by_email('john@example.com')
          
          expect(found_user).to eq(user)
        end
        
        it 'returns nil for non-existent user' do
          found_user = repository.find(999)
          
          expect(found_user).to be_nil
        end
      end
      
      describe 'stubbing responses' do
        it 'returns stubbed response for find_by_email' do
          stub_user = User.new(id: 2, name: 'Jane Doe', email: 'jane@example.com', age: 30)
          repository.stub_find_by_email('jane@example.com', stub_user)
          
          found_user = repository.find_by_email('jane@example.com')
          
          expect(found_user).to eq(stub_user)
        end
        
        it 'returns stubbed response for save' do
          repository.stub_save(1, false)
          
          result = repository.save(user)
          
          expect(result).to be false
        end
      end
    end
    
    # 8. Test Double Factory
    # Factory for creating different types of test doubles
    
    class TestDoubleFactory
      def self.create_payment_gateway(type = :double)
        case type
        when :double
          instance_double(PaymentGateway)
        when :fake
          PaymentGatewayDouble.new
        when :stub
          instance_double(PaymentGateway)
        when :spy
          PaymentGatewaySpy.new
        else
          raise ArgumentError, "Unknown test double type: #{type}"
        end
      end
      
      def self.create_notification_service(type = :mock)
        case type
        when :mock
          NotificationServiceMock.new
        when :fake
          FakeEmailService.new
        when :spy
          NotificationServiceSpy.new
        else
          raise ArgumentError, "Unknown test double type: #{type}"
        end
      end
      
      def self.create_user_repository(type = :stub)
        case type
        when :stub
          UserRepositoryStub.new
        when :fake
          FakeUserRepository.new
        when :spy
          UserRepositorySpy.new
        else
          raise ArgumentError, "Unknown test double type: #{type}"
        end
      end
    end
    
    # 9. Verification Techniques
    # Advanced verification patterns for test doubles
    
    RSpec.describe 'Advanced Verification Techniques' do
      let(:notification_service) { NotificationServiceMock.new }
      
      describe 'verifying multiple interactions' do
        let(:user1) { User.new(id: 1, name: 'John Doe', email: 'john@example.com') }
        let(:user2) { User.new(id: 2, name: 'Jane Doe', email: 'jane@example.com') }
        
        it 'verifies multiple notifications were sent' do
          notification_service.send_notification(user1, 'Welcome!', :email)
          notification_service.send_notification(user2, 'Welcome!', :email)
          
          expect(notification_service.notifications_sent.length).to eq(2)
          expect(notification_service.notification_sent_to?(user1, 'Welcome!')).to be true
          expect(notification_service.notification_sent_to?(user2, 'Welcome!')).to be true
        end
      end
      
      describe 'verifying interaction order' do
        let(:user) { User.new(id: 1, name: 'John Doe', email: 'john@example.com') }
        
        it 'verifies notifications were sent in specific order' do
          notification_service.send_notification(user, 'First message', :email)
          notification_service.send_notification(user, 'Second message', :sms)
          
          notifications = notification_service.notifications_sent
          
          expect(notifications.first[:message]).to eq('First message')
          expect(notifications.first[:type]).to eq(:email)
          expect(notifications.second[:message]).to eq('Second message')
          expect(notifications.second[:type]).to eq(:sms)
        end
      end
      
      describe 'verifying interaction counts' do
        let(:user) { User.new(id: 1, name: 'John Doe', email: 'john@example.com') }
        
        it 'verifies exact number of interactions' do
          3.times { notification_service.send_notification(user, 'Test message', :email) }
          
          expect(notification_service.notifications_sent.length).to eq(3)
          expect(notification_service.email_sent.length).to eq(3)
        end
      end
    end
    
    # 10. Test Double Best Practices
    # Guidelines and patterns for effective test double usage
    
    RSpec.describe 'Test Double Best Practices' do
      describe 'when to use test doubles' do
        it 'uses doubles for external dependencies' do
          # Use doubles when testing code that depends on external services
          gateway = TestDoubleFactory.create_payment_gateway(:double)
          allow(gateway).to receive(:charge).and_return(PaymentResult.new(success: true))
          
          processor = PaymentProcessor.new(gateway)
          result = processor.process_payment(100, { number: '4111111111111111' })
          
          expect(result.success).to be true
        end
        
        it 'uses fakes for complex behavior' do
          # Use fakes when you need working implementation
          email_service = FakeEmailService.new
          
          result = email_service.send_email('test@example.com', 'Test', 'Test message')
          
          expect(result).to be true
          expect(email_service.email_sent_to?('test@example.com', 'Test')).to be true
        end
        
        it 'uses spies for verification' do
          # Use spies when you need to verify interactions
          service = UserServiceSpy.new
          service.create_user(name: 'John', email: 'john@example.com')
          
          expect(service).to have_received(:create_user)
        end
      end
      
      describe 'test double limitations' do
        it 'avoids over-specification' do
          # Don't over-specify mock expectations
          gateway = instance_double(PaymentGateway)
          
          # Bad: Over-specified
          # allow(gateway).to receive(:charge).with(100, { number: '4111111111111111', cvv: '123', expiry: '12/25' })
          
          # Good: Focus on essential behavior
          allow(gateway).to receive(:charge).and_return(PaymentResult.new(success: true))
          
          processor = PaymentProcessor.new(gateway)
          result = processor.process_payment(100, { number: '4111111111111111' })
          
          expect(result.success).to be true
        end
        
        it 'uses real objects when appropriate' do
          # Use real objects when the dependency is simple and fast
          validator = EmailValidator.new
          
          expect(validator.valid?('user@example.com')).to be true
          expect(validator.valid?('invalid-email')).to be false
        end
      end
    end
    
    # 11. Test Double Maintenance
    # Keeping test doubles maintainable and up-to-date
    
    class TestDoubleMaintenance
      def self.sync_double_with_real_implementation(double_class, real_class)
        real_methods = real_class.instance_methods(false)
        double_methods = double_class.instance_methods(false)
        
        missing_methods = real_methods - double_methods
        
        missing_methods.each do |method|
          puts "Warning: #{double_class} missing method: #{method}"
          
          # Add missing method to double
          double_class.define_method(method) do |*args|
            # Default implementation or raise NotImplementedError
            raise NotImplementedError, "Method #{method} not implemented in test double"
          end
        end
      end
      
      def self.validate_double_behavior(double_class, real_class)
        # Create instances and compare behavior
        double_instance = double_class.new
        real_instance = real_class.new
        
        # Test common methods
        common_methods = real_class.instance_methods(false) & double_class.instance_methods(false)
        
        common_methods.each do |method|
          begin
            # Test if signatures match
            real_method = real_instance.method(method)
            double_method = double_instance.method(method)
            
            if real_method.arity != double_method.arity
              puts "Warning: Method #{method} arity mismatch:"
              puts "  Real: #{real_method.arity}"
              puts "  Double: #{double_method.arity}"
            end
          rescue NameError
            puts "Warning: Method #{method} not found in one of the classes"
          end
        end
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "Test Doubles and Mocks Demonstration"
  puts "=" * 60
  
  # Demonstrate different types of test doubles
  puts "\n1. Test Double Types:"
  puts "✅ Test Doubles - Simple stand-ins"
  puts "✅ Mocks - Verification objects"
  puts "✅ Stubs - Predefined responses"
  puts "✅ Fakes - Working implementations"
  puts "✅ Spies - Recording interactions"
  
  # Demonstrate RSpec mocking
  puts "\n2. RSpec Mocking:"
  puts "✅ instance_double"
  puts "✅ allow/receive expectations"
  puts "✅ have_received verification"
  
  # Demonstrate verification techniques
  puts "\n3. Verification Techniques:"
  puts "✅ Interaction verification"
  puts "✅ Order verification"
  puts "✅ Count verification"
  
  # Demonstrate best practices
  puts "\n4. Best Practices:"
  puts "✅ Use doubles for external dependencies"
  puts "✅ Use fakes for complex behavior"
  puts "✅ Use spies for verification"
  puts "✅ Avoid over-specification"
  
  # Demonstrate factory pattern
  puts "\n5. Test Double Factory:"
  puts "✅ Centralized double creation"
  puts "✅ Type-specific configuration"
  puts "✅ Consistent interface"
  
  puts "\nTest doubles help isolate units and make tests more reliable!"
end
