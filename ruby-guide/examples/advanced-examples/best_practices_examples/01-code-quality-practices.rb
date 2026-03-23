# Ruby Code Quality Best Practices
# This file demonstrates essential Ruby code quality practices with examples
# and explanations for writing clean, maintainable, and efficient Ruby code.

module BestPracticesExamples
  module CodeQualityPractices
    # 1. Naming Conventions
    # Use descriptive and consistent naming throughout your code
    
    # Good naming examples
    class UserAccountManager
      def initialize(user_repository)
        @user_repository = user_repository
      end
      
      def find_active_users_with_recent_activity
        # Method name clearly describes what it does
        @user_repository.find_by(active: true, last_login: 1.week.ago..Time.now)
      end
      
      def calculate_total_revenue_for_period(start_date, end_date)
        # Parameters have descriptive names
        # Method name follows snake_case convention
        transactions = @user_repository.transactions_between(start_date, end_date)
        transactions.sum(&:amount)
      end
    end
    
    # Variable naming
    def process_user_data(users_data)
      # Use descriptive variable names
      active_users = users_data.select { |user| user[:status] == 'active' }
      premium_users = active_users.select { |user| user[:subscription_type] == 'premium' }
      
      premium_users.each do |user|
        send_welcome_email(user[:email]) if user[:new_user]
        apply_premium_features(user[:id])
      end
    end
    
    # 2. Method and Class Design
    # Keep methods small and focused on a single responsibility
    
    class OrderProcessor
      def initialize(order, payment_gateway, inventory_service)
        @order = order
        @payment_gateway = payment_gateway
        @inventory_service = inventory_service
      end
      
      def process_order
        validate_order
        reserve_inventory
        process_payment
        confirm_order
        send_confirmation_email
      end
      
      private
      
      def validate_order
        raise ArgumentError, "Order cannot be nil" if @order.nil?
        raise ArgumentError, "Order has no items" if @order.items.empty?
        raise ArgumentError, "Invalid shipping address" unless valid_shipping_address?
      end
      
      def reserve_inventory
        @order.items.each do |item|
          unless @inventory_service.reserve(item[:product_id], item[:quantity])
            raise InventoryError, "Insufficient stock for #{item[:product_id]}"
          end
        end
      end
      
      def process_payment
        payment_result = @payment_gateway.charge(@order.total_amount, @order.payment_method)
        raise PaymentError, "Payment failed" unless payment_result.success?
      end
      
      def confirm_order
        @order.status = 'confirmed'
        @order.save!
      end
      
      def send_confirmation_email
        OrderConfirmationMailer.deliver(@order)
      end
      
      def valid_shipping_address?
        @order.shipping_address &&
        @order.shipping_address.street.present? &&
        @order.shipping_address.city.present? &&
        @order.shipping_address.postal_code.present?
      end
    end
    
    # 3. Error Handling
    # Use proper exception handling and create custom exceptions
    
    # Custom exceptions
    class BusinessError < StandardError; end
    class ValidationError < BusinessError; end
    class AuthenticationError < BusinessError; end
    class AuthorizationError < BusinessError; end
    
    class UserService
      def authenticate_user(email, password)
        user = find_user_by_email(email)
        
        raise AuthenticationError, "Invalid credentials" unless user
        raise AuthenticationError, "Account locked" if user.locked?
        
        unless user.authenticate(password)
          increment_failed_attempts(user)
          raise AuthenticationError, "Invalid credentials"
        end
        
        reset_failed_attempts(user)
        user
      end
      
      def authorize_user(user, resource, action)
        return true if user.admin?
        
        permission = "#{action}_#{resource.class.name.downcase}"
        unless user.has_permission?(permission)
          raise AuthorizationError, "Access denied to #{action} #{resource.class.name}"
        end
        
        true
      end
      
      private
      
      def find_user_by_email(email)
        User.find_by(email: email)
      rescue => e
        raise AuthenticationError, "Authentication service unavailable"
      end
      
      def increment_failed_attempts(user)
        user.increment!(:failed_attempts)
        user.update!(locked: true) if user.failed_attempts >= 5
      end
      
      def reset_failed_attempts(user)
        user.update!(failed_attempts: 0, locked: false)
      end
    end
    
    # 4. Code Organization and Structure
    # Use modules to share functionality and keep code organized
    
    module Timestampable
      extend ActiveSupport::Concern
      
      included do
        before_create :set_created_at
        before_save :set_updated_at
      end
      
      def set_created_at
        self.created_at = Time.current if created_at.nil?
      end
      
      def set_updated_at
        self.updated_at = Time.current
      end
      
      def recently_created?
        created_at > 5.minutes.ago
      end
      
      def recently_updated?
        updated_at > 5.minutes.ago
      end
    end
    
    module Searchable
      extend ActiveSupport::Concern
      
      class_methods do
        def search(query, fields = [:name, :description])
          return all if query.blank?
          
          conditions = fields.map do |field|
            "#{field} ILIKE ?"
          end.join(" OR ")
          
          values = fields.map { |field| "%#{query}%" }
          
          where(conditions, *values)
        end
      end
    end
    
    class Product < ApplicationRecord
      include Timestampable
      include Searchable
      
      validates :name, presence: true, length: { maximum: 255 }
      validates :price, presence: true, numericality: { greater_than: 0 }
      validates :description, length: { maximum: 1000 }
      
      scope :active, -> { where(active: true) }
      scope :featured, -> { where(featured: true) }
      scope :by_category, ->(category) { where(category: category) }
      
      def available?
        active? && stock_quantity > 0
      end
      
      def out_of_stock?
        stock_quantity == 0
      end
      
      def low_stock?
        stock_quantity > 0 && stock_quantity < 10
      end
    end
    
    # 5. Performance Considerations
    # Write efficient code and avoid common performance pitfalls
    
    class ReportGenerator
      def generate_sales_report(start_date, end_date)
        # Use database queries efficiently
        orders = Order.includes(:customer, :items)
                     .where(created_at: start_date..end_date)
                     .where(status: 'completed')
        
        # Process data in memory efficiently
        report_data = {
          total_orders: orders.count,
          total_revenue: calculate_total_revenue(orders),
          top_products: find_top_products(orders),
          customer_stats: calculate_customer_statistics(orders)
        }
        
        format_report(report_data)
      end
      
      private
      
      def calculate_total_revenue(orders)
        # Use database calculations when possible
        orders.sum(:total_amount)
      end
      
      def find_top_products(orders)
        # Use efficient aggregation
        OrderItem.joins(:order)
                 .where(orders: { id: orders.pluck(:id) })
                 .group(:product_id)
                 .select('product_id, SUM(quantity) as total_quantity, SUM(price * quantity) as total_revenue')
                 .order('total_quantity DESC')
                 .limit(10)
      end
      
      def calculate_customer_statistics(orders)
        # Use efficient grouping
        orders.group(:customer_id)
             .select('customer_id, COUNT(*) as order_count, SUM(total_amount) as total_spent')
             .order('total_spent DESC')
      end
      
      def format_report(data)
        # Format data efficiently
        {
          summary: {
            total_orders: data[:total_orders],
            total_revenue: data[:total_revenue],
            average_order_value: data[:total_revenue] / data[:total_orders]
          },
          top_products: data[:top_products].map do |product|
            {
              product_id: product.product_id,
              quantity_sold: product.total_quantity,
              revenue: product.total_revenue
            }
          end,
          customer_insights: data[:customer_stats].take(5)
        }
      end
    end
    
    # 6. Testing Best Practices
    # Write comprehensive and maintainable tests
    
    class UserServiceTest < Minitest::Test
      def setup
        @user_service = UserService.new
        @valid_user = create_user(email: 'test@example.com', password: 'password123')
      end
      
      def test_authenticate_user_with_valid_credentials
        result = @user_service.authenticate_user('test@example.com', 'password123')
        
        assert_equal @valid_user, result
        assert_equal 0, result.failed_attempts
        refute result.locked?
      end
      
      def test_authenticate_user_with_invalid_credentials
        assert_raises(AuthenticationError) do
          @user_service.authenticate_user('test@example.com', 'wrongpassword')
        end
        
        @valid_user.reload
        assert_equal 1, @valid_user.failed_attempts
      end
      
      def test_authenticate_user_with_locked_account
        @valid_user.update!(locked: true)
        
        assert_raises(AuthenticationError, /locked/) do
          @user_service.authenticate_user('test@example.com', 'password123')
        end
      end
      
      private
      
      def create_user(attributes)
        User.create!(attributes)
      end
    end
    
    # 7. Documentation and Comments
    # Write clear documentation and meaningful comments
    
    # Good documentation example
    class PaymentProcessor
      # Processes a payment for the given order using the specified payment method.
      #
      # @param order [Order] The order to be paid for
      # @param payment_method [PaymentMethod] The payment method to use
      # @param options [Hash] Additional processing options
      # @option options [Boolean] :save_card (false) Whether to save the payment method
      # @option options [String] :description Custom payment description
      #
      # @return [PaymentResult] The result of the payment processing
      #
      # @raise [PaymentError] If the payment fails
      # @raise [ValidationError] If the order or payment method is invalid
      #
      # @example Process a payment with credit card
      #   order = Order.find(123)
      #   payment_method = PaymentMethod.find_by(token: 'tok_123')
      #   result = processor.process_payment(order, payment_method)
      #   puts "Payment #{result.success? ? 'successful' : 'failed'}"
      #
      def process_payment(order, payment_method, options = {})
        validate_payment_data(order, payment_method)
        
        # Create payment record before processing
        payment = create_payment_record(order, payment_method, options)
        
        begin
          # Process payment through payment gateway
          gateway_result = payment_gateway.charge(
            amount: order.total_amount,
            payment_method: payment_method,
            description: options[:description]
          )
          
          # Update payment status based on gateway result
          update_payment_status(payment, gateway_result)
          
          # Handle post-payment operations
          handle_successful_payment(payment) if gateway_result.success?
          
          PaymentResult.new(
            success: gateway_result.success?,
            payment: payment,
            gateway_response: gateway_result
          )
        rescue => e
          # Log error and update payment status
          handle_payment_error(payment, e)
          raise PaymentError, "Payment processing failed: #{e.message}"
        end
      end
      
      private
      
      # Validates the payment data before processing
      def validate_payment_data(order, payment_method)
        raise ValidationError, "Order cannot be nil" if order.nil?
        raise ValidationError, "Payment method cannot be nil" if payment_method.nil?
        raise ValidationError, "Order has no items" if order.items.empty?
        raise ValidationError, "Invalid payment method" unless payment_method.valid?
        raise ValidationError, "Order must have total amount" if order.total_amount <= 0
      end
      
      # Creates a payment record in the database
      def create_payment_record(order, payment_method, options)
        Payment.create!(
          order: order,
          payment_method: payment_method,
          amount: order.total_amount,
          currency: order.currency,
          status: 'pending',
          save_card: options[:save_card] || false
        )
      end
      
      # Updates payment status based on gateway response
      def update_payment_status(payment, gateway_result)
        if gateway_result.success?
          payment.update!(
            status: 'completed',
            gateway_transaction_id: gateway_result.transaction_id,
            processed_at: Time.current
          )
        else
          payment.update!(
            status: 'failed',
            failure_reason: gateway_result.error_message,
            processed_at: Time.current
          )
        end
      end
      
      # Handles operations after successful payment
      def handle_successful_payment(payment)
        order = payment.order
        order.update!(status: 'paid')
        order.items.each { |item| item.mark_as_paid }
        send_payment_confirmation_email(payment)
        update_inventory(order)
      end
      
      # Handles payment errors
      def handle_payment_error(payment, error)
        payment.update!(
          status: 'error',
          failure_reason: error.message,
          processed_at: Time.current
        )
        
        Rails.logger.error "Payment error: #{error.message}", error
      end
    end
    
    # 8. Security Best Practices
    # Write secure code and follow security guidelines
    
    class SecureDataHandler
      # Sensitive data should be encrypted at rest
      def encrypt_sensitive_data(data)
        # Use proper encryption with strong algorithms
        cipher = OpenSSL::Cipher.new('AES-256-GCM')
        cipher.encrypt
        
        # Generate random IV for each encryption
        iv = cipher.random_iv
        cipher.key = encryption_key
        
        # Encrypt the data
        encrypted = cipher.update(data.to_json) + cipher.final
        
        # Include authentication tag
        tag = cipher.auth_tag
        
        # Return encrypted data with IV and tag
        {
          encrypted_data: Base64.strict_encode64(encrypted),
          iv: Base64.strict_encode64(iv),
          tag: Base64.strict_encode64(tag)
        }
      end
      
      def decrypt_sensitive_data(encrypted_data)
        cipher = OpenSSL::Cipher.new('AES-256-GCM')
        cipher.decrypt
        
        cipher.key = encryption_key
        cipher.iv = Base64.strict_decode64(encrypted_data[:iv])
        cipher.auth_tag = Base64.strict_decode64(encrypted_data[:tag])
        
        decrypted = cipher.update(Base64.strict_decode64(encrypted_data[:encrypted_data])) + cipher.final
        
        JSON.parse(decrypted)
      end
      
      # Input validation and sanitization
      def sanitize_user_input(input)
        return nil if input.nil?
        
        # Remove potentially dangerous characters
        sanitized = input.gsub(/[<>'"&]/, '')
        
        # Limit length to prevent buffer overflow
        sanitized[0, 1000]
      end
      
      # SQL injection prevention
      def find_users_safely(search_params)
        # Use parameterized queries instead of string interpolation
        User.where(
          'name ILIKE ? AND email ILIKE ? AND created_at > ?',
          "%#{sanitize_user_input(search_params[:name])}%",
          "%#{sanitize_user_input(search_params[:email])}%",
          search_params[:created_after]
        )
      end
      
      # Secure file handling
      def process_uploaded_file(file)
        # Validate file type and size
        raise SecurityError, "Invalid file type" unless allowed_file_type?(file.content_type)
        raise SecurityError, "File too large" if file.size > max_file_size
        
        # Generate secure filename
        filename = SecureRandom.uuid + File.extname(file.original_filename)
        
        # Store file in secure location
        file_path = File.join(secure_storage_path, filename)
        File.write(file_path, file.read, mode: 0o600) # Secure file permissions
        
        file_path
      end
      
      private
      
      def encryption_key
        # Load encryption key from secure storage
        Rails.application.credentials.encryption_key
      end
      
      def allowed_file_type?(content_type)
        %w[image/jpeg image/png application/pdf text/plain].include?(content_type)
      end
      
      def max_file_size
        10.megabytes
      end
      
      def secure_storage_path
        Rails.root.join('storage', 'secure_files')
      end
    end
    
    # 9. Code Metrics and Quality Indicators
    # Monitor and maintain code quality metrics
    
    class CodeQualityAnalyzer
      def analyze_class(klass)
        {
          lines_of_code: count_lines_of_code(klass),
          method_count: count_methods(klass),
          cyclomatic_complexity: calculate_complexity(klass),
          class_length: count_class_length(klass),
          method_length: calculate_average_method_length(klass),
          test_coverage: calculate_test_coverage(klass)
        }
      end
      
      def quality_score(analysis)
        score = 100
        
        # Deduct points for issues
        score -= 10 if analysis[:lines_of_code] > 500
        score -= 5 if analysis[:method_count] > 20
        score -= 15 if analysis[:cyclomatic_complexity] > 10
        score -= 10 if analysis[:method_length] > 15
        score -= 20 if analysis[:test_coverage] < 80
        
        [score, 0].max
      end
      
      private
      
      def count_lines_of_code(klass)
        source = File.read(klass.source_location[0])
        source.lines.count { |line| !line.strip.empty? && !line.strip.start_with?('#') }
      end
      
      def count_methods(klass)
        klass.instance_methods(false).length
      end
      
      def calculate_complexity(klass)
        # Simplified cyclomatic complexity calculation
        source = File.read(klass.source_location[0])
        complexity = 1
        
        complexity += source.scan(/\bif\b/).length
        complexity += source.scan(/\bunless\b/).length
        complexity += source.scan(/\bcase\b/).length
        complexity += source.scan(/\bwhile\b/).length
        complexity += source.scan(/\bfor\b/).length
        complexity += source.scan(/\belsif\b/).length
        complexity += source.scan(/\bwhen\b/).length
        complexity += source.scan(/\b\|\|/).length
        complexity += source.scan(/\b&&/).length
        
        complexity
      end
      
      def count_class_length(klass)
        source = File.read(klass.source_location[0])
        source.lines.length
      end
      
      def calculate_average_method_length(klass)
        return 0 if klass.instance_methods(false).empty?
        
        total_length = klass.instance_methods(false).sum do |method|
          source = klass.instance_method(method).source
          source.lines.length
        end
        
        total_length / klass.instance_methods(false).length
      end
      
      def calculate_test_coverage(klass)
        # This would integrate with a coverage tool
        # For demonstration, return a placeholder
        85
      end
    end
    
    # 10. Refactoring Patterns
    # Common refactoring patterns for improving code quality
    
    class RefactoringExamples
      # Extract Method - Break down large method into smaller methods
      class BeforeExtractMethod
        def process_order(order)
          # Validate order
          if order.nil?
            raise ArgumentError, "Order cannot be nil"
          end
          
          if order.items.empty?
            raise ArgumentError, "Order must have items"
          end
          
          if order.customer.nil?
            raise ArgumentError, "Order must have customer"
          end
          
          # Calculate totals
          subtotal = order.items.sum(&:price)
          tax = subtotal * 0.1
          shipping = calculate_shipping(order)
          total = subtotal + tax + shipping
          
          # Apply discounts
          if order.customer.vip?
            total *= 0.9
          end
          
          if order.coupon_code
            discount = Coupon.find_by(code: order.coupon_code)
            if discount && discount.valid?
              total -= discount.amount
            end
          end
          
          # Process payment
          payment = Payment.create!(
            order: order,
            amount: total,
            status: 'pending'
          )
          
          gateway_result = PaymentGateway.charge(total, order.payment_method)
          
          if gateway_result.success?
            payment.update!(status: 'completed')
            order.update!(status: 'paid')
          else
            payment.update!(status: 'failed')
            raise PaymentError, "Payment failed"
          end
          
          # Send notifications
          OrderConfirmationMailer.deliver(order)
          NotificationService.notify_customer(order.customer, "Your order has been processed")
        end
      end
      
      # After Extract Method - Much cleaner and more maintainable
      class AfterExtractMethod
        def process_order(order)
          validate_order(order)
          total = calculate_order_total(order)
          process_payment(order, total)
          send_notifications(order)
        end
        
        private
        
        def validate_order(order)
          raise ArgumentError, "Order cannot be nil" if order.nil?
          raise ArgumentError, "Order must have items" if order.items.empty?
          raise ArgumentError, "Order must have customer" if order.customer.nil?
        end
        
        def calculate_order_total(order)
          subtotal = order.items.sum(&:price)
          tax = subtotal * 0.1
          shipping = calculate_shipping(order)
          total = subtotal + tax + shipping
          
          total = apply_customer_discount(total, order.customer)
          total = apply_coupon_discount(total, order.coupon_code)
          
          total
        end
        
        def apply_customer_discount(total, customer)
          return total unless customer&.vip?
          total * 0.9
        end
        
        def apply_coupon_discount(total, coupon_code)
          return total unless coupon_code
          
          discount = Coupon.find_by(code: coupon_code)
          return total unless discount&.valid?
          
          total - discount.amount
        end
        
        def process_payment(order, total)
          payment = Payment.create!(
            order: order,
            amount: total,
            status: 'pending'
          )
          
          gateway_result = PaymentGateway.charge(total, order.payment_method)
          
          if gateway_result.success?
            payment.update!(status: 'completed')
            order.update!(status: 'paid')
          else
            payment.update!(status: 'failed')
            raise PaymentError, "Payment failed"
          end
        end
        
        def send_notifications(order)
          OrderConfirmationMailer.deliver(order)
          NotificationService.notify_customer(order.customer, "Your order has been processed")
        end
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "Ruby Code Quality Best Practices Demonstration"
  puts "=" * 60
  
  # Demonstrate naming conventions
  puts "\n1. Naming Conventions:"
  puts "✅ UserAccountManager (class name)"
  puts "✅ find_active_users_with_recent_activity (method name)"
  puts "✅ @user_repository (instance variable)"
  puts "✅ MAX_RETRY_ATTEMPTS (constant)"
  
  # Demonstrate method design
  puts "\n2. Method Design:"
  puts "✅ Small, focused methods"
  puts "✅ Single responsibility principle"
  puts "✅ Clear method names"
  
  # Demonstrate error handling
  puts "\n3. Error Handling:"
  puts "✅ Custom exceptions for different error types"
  puts "✅ Proper exception handling with rescue blocks"
  puts "✅ Meaningful error messages"
  
  # Demonstrate code organization
  puts "\n4. Code Organization:"
  puts "✅ Modules for shared functionality"
  puts "✅ Concerns for cross-cutting concerns"
  puts "✅ Logical grouping of related methods"
  
  # Demonstrate performance considerations
  puts "\n5. Performance Considerations:"
  puts "✅ Database query optimization"
  puts "✅ Efficient data processing"
  puts "✅ Avoid N+1 queries"
  
  # Demonstrate security practices
  puts "\n6. Security Practices:"
  puts "✅ Input validation and sanitization"
  puts "✅ SQL injection prevention"
  puts "✅ Secure file handling"
  puts "✅ Data encryption"
  
  # Demonstrate refactoring
  puts "\n7. Refactoring:"
  puts "✅ Extract Method pattern"
  puts "✅ Single Responsibility Principle"
  puts "✅ Code readability and maintainability"
  
  puts "\nCode quality is an ongoing process!"
  puts "Always strive to write clean, maintainable, and efficient code."
end
