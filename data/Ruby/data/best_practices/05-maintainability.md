# Maintainability in Ruby
# Comprehensive guide to writing maintainable Ruby code

## 🎯 Overview

Maintainability is crucial for long-term Ruby application success. This guide covers techniques for writing clean, maintainable code that's easy to understand, modify, and extend.

## 📝 Code Organization

### 1. Modular Architecture

Building maintainable modular systems:

```ruby
class ModularArchitecture
  def self.domain_driven_design
    puts "Domain-Driven Design Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Bounded Context",
        description: "Define clear boundaries for different domains",
        implementation: "Separate modules for different business domains"
      },
      {
        principle: "Ubiquitous Language",
        description: "Use consistent terminology across the codebase",
        implementation: "Domain-specific terms in class and method names"
      },
      {
        principle: "Domain Entities",
        description: "Model core business concepts as entities",
        implementation: "Rich domain models with behavior"
      },
      {
        principle: "Value Objects",
        description: "Use immutable value objects for concepts without identity",
        implementation: "Immutable objects with equality based on values"
      },
      {
        principle: "Aggregates",
        description: "Group related entities into aggregates",
        implementation: "Aggregate roots manage invariants"
      },
      {
        principle: "Repositories",
        description: "Abstract data access with repositories",
        implementation: "Repository pattern for data access"
      },
      {
        principle: "Domain Services",
        description: "Service objects for domain logic",
        implementation: "Services for business logic that doesn't fit entities"
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Implementation: #{principle[:implementation]}"
      puts
    end
  end
  
  def self.modular_example
    puts "\nModular Architecture Example:"
    puts "=" * 50
    
    modular_example = <<~RUBY
      # Domain: User Management
      module UserManagement
        # Domain Entity
        class User
          attr_reader :id, :name, :email, :profile
          
          def initialize(id:, name:, email:)
            @id = id
            @name = name
            @email = email
            @profile = nil
          end
          
          def update_profile(profile)
            @profile = profile
            validate_profile
          end
          
          def active?
            profile&.active? || false
          end
          
          private
          
          def validate_profile
            raise "Invalid profile" unless profile.valid?
          end
        end
        
        # Value Object
        class UserProfile
          attr_reader :first_name, :last_name, :preferences, :active
          
          def initialize(first_name:, last_name:, preferences: {}, active: true)
            @first_name = first_name
            @last_name = last_name
            @preferences = preferences.freeze
            @active = active
            freeze
          end
          
          def full_name
            "#{first_name} #{last_name}"
          end
          
          def valid?
            first_name && !first_name.empty? && 
            last_name && !last_name.empty?
          end
          
          def with_preferences(new_preferences)
            UserProfile.new(
              first_name: first_name,
              last_name: last_name,
              preferences: new_preferences,
              active: active
            )
          end
          
          def activate
            UserProfile.new(
              first_name: first_name,
              last_name: last_name,
              preferences: preferences,
              active: true
            )
          end
          
          def deactivate
            UserProfile.new(
              first_name: first_name,
              last_name: last_name,
              preferences: preferences,
              active: false
            )
          end
        end
        
        # Repository
        class UserRepository
          def initialize(database)
            @database = database
          end
          
          def find(id)
            data = @database.get("users:#{id}")
            return nil unless data
            
            build_user_from_data(data)
          end
          
          def save(user)
            data = serialize_user(user)
            @database.set("users:#{user.id}", data)
            user
          end
          
          def find_by_email(email)
            users = @database.keys("users:*")
            users.each do |key|
              user_data = @database.get(key)
              user = build_user_from_data(user_data)
              return user if user.email == email
            end
            nil
          end
          
          private
          
          def build_user_from_data(data)
            profile_data = data['profile']
            profile = profile_data ? UserProfile.new(
              first_name: profile_data['first_name'],
              last_name: profile_data['last_name'],
              preferences: profile_data['preferences'],
              active: profile_data['active']
            ) : nil
            
            User.new(
              id: data['id'],
              name: data['name'],
              email: data['email']
            ).tap { |user| user.instance_variable_set(:@profile, profile) }
          end
          
          def serialize_user(user)
            {
              'id' => user.id,
              'name' => user.name,
              'email' => user.email,
              'profile' => user.profile ? {
                'first_name' => user.profile.first_name,
                'last_name' => user.profile.last_name,
                'preferences' => user.profile.preferences,
                'active' => user.profile.active
              } : nil
            }
          end
        end
        
        # Domain Service
        class UserRegistrationService
          def initialize(user_repository, email_service)
            @user_repository = user_repository
            @email_service = email_service
          end
          
          def register_user(name:, email:, profile_data:)
            # Validate input
            validate_registration_data(name, email, profile_data)
            
            # Check if user already exists
            existing_user = @user_repository.find_by_email(email)
            raise "User already exists" if existing_user
            
            # Create user
            user = User.new(
              id: generate_id,
              name: name,
              email: email
            )
            
            # Create profile
            profile = UserProfile.new(
              first_name: profile_data[:first_name],
              last_name: profile_data[:last_name],
              preferences: profile_data[:preferences] || {},
              active: true
            )
            
            user.update_profile(profile)
            
            # Save user
            @user_repository.save(user)
            
            # Send welcome email
            @email_service.send_welcome_email(user)
            
            user
          end
          
          private
          
          def validate_registration_data(name, email, profile_data)
            raise "Name is required" if name.nil? || name.empty?
            raise "Email is required" if email.nil? || email.empty?
            raise "Invalid email format" unless email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
            raise "Profile data is incomplete" unless profile_data[:first_name] && profile_data[:last_name]
          end
          
          def generate_id
            SecureRandom.uuid
          end
        end
        
        # Application Service
        class UserApplicationService
          def initialize(user_repository, email_service)
            @registration_service = UserRegistrationService.new(user_repository, email_service)
            @user_repository = user_repository
          end
          
          def create_user(params)
            @registration_service.register_user(
              name: params[:name],
              email: params[:email],
              profile_data: {
                first_name: params[:first_name],
                last_name: params[:last_name],
                preferences: params[:preferences] || {}
              }
            )
          end
          
          def get_user(id)
            @user_repository.find(id)
          end
          
          def update_user_preferences(id, preferences)
            user = @user_repository.find(id)
            raise "User not found" unless user
            
            new_profile = user.profile.with_preferences(preferences)
            user.update_profile(new_profile)
            @user_repository.save(user)
            
            user
          end
        end
      end
      
      # Usage example
      def self.demonstrate_modular_architecture
        puts "Demonstrating Modular Architecture:"
        puts "=" * 40
        
        # Mock dependencies
        database = {}
        email_service = double('EmailService')
        allow(email_service).to receive(:send_welcome_email)
        
        # Create repository
        user_repository = UserManagement::UserRepository.new(database)
        
        # Create application service
        user_service = UserManagement::UserApplicationService.new(user_repository, email_service)
        
        # Create user
        user = user_service.create_user(
          name: "John Doe",
          email: "john@example.com",
          first_name: "John",
          last_name: "Doe",
          preferences: { theme: "dark", language: "en" }
        )
        
        puts "Created user: #{user.name} (#{user.email})"
        puts "User active: #{user.active?}"
        puts "User full name: #{user.profile.full_name}"
        
        # Update preferences
        updated_user = user_service.update_user_preferences(user.id, { theme: "light" })
        puts "Updated preferences: #{updated_user.profile.preferences}"
      end
    RUBY
    
    puts "Modular Architecture Example:"
    puts modular_example
  end
  
  # Run modular architecture examples
  domain_driven_design
  modular_example
end
```

### 2. Clean Architecture

Implementing clean architecture principles:

```ruby
class CleanArchitecture
  def self.clean_architecture_principles
    puts "Clean Architecture Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Dependency Inversion",
        description: "Dependencies point inward",
        implementation: "Use interfaces and dependency injection"
      },
      {
        principle: "Single Responsibility",
        description: "Each module has one reason to change",
        implementation: "Separate concerns into different modules"
      },
      {
        principle: "Open/Closed",
        description: "Open for extension, closed for modification",
        implementation: "Use polymorphism and interfaces"
      },
      {
        principle: "Interface Segregation",
        description: "Many small, focused interfaces",
        implementation: "Avoid fat interfaces"
      },
      {
        principle: "Dependency Inversion",
        description: "Depend on abstractions, not concretions",
        implementation: "Use dependency injection"
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Implementation: #{principle[:implementation]}"
      puts
    end
  end
  
  def self.clean_architecture_example
    puts "\nClean Architecture Example:"
    puts "=" * 50
    
    clean_architecture_example = <<~RUBY
      # Entity Layer (Core Business Logic)
      module Entities
        class Order
          attr_reader :id, :customer_id, :items, :status, :total
          
          def initialize(id:, customer_id:, items: [])
            @id = id
            @customer_id = customer_id
            @items = items.dup
            @status = :pending
            @total = calculate_total
          end
          
          def add_item(item)
            raise "Cannot add items to confirmed order" if confirmed?
            
            @items << item
            @total = calculate_total
          end
          
          def confirm
            raise "Cannot confirm empty order" if @items.empty?
            raise "Order already confirmed" if confirmed?
            
            @status = :confirmed
          end
          
          def cancel
            raise "Cannot cancel confirmed order" if confirmed?
            
            @status = :cancelled
          end
          
          def confirmed?
            @status == :confirmed
          end
          
          def cancelled?
            @status == :cancelled
          end
          
          def pending?
            @status == :pending
          end
          
          private
          
          def calculate_total
            @items.sum(&:total)
          end
        end
        
        class OrderItem
          attr_reader :product_id, :quantity, :price, :total
          
          def initialize(product_id:, quantity:, price:)
            @product_id = product_id
            @quantity = quantity
            @price = price
            @total = quantity * price
          end
        end
      end
      
      # Use Case Layer (Application Logic)
      module UseCases
        class CreateOrder
          def initialize(order_repository, product_repository, notification_service)
            @order_repository = order_repository
            @product_repository = product_repository
            @notification_service = notification_service
          end
          
          def execute(customer_id, items_data)
            # Validate customer
            raise "Invalid customer ID" unless customer_id
            
            # Create order items
            items = items_data.map do |item_data|
              product = @product_repository.find(item_data[:product_id])
              raise "Product not found: #{item_data[:product_id]}" unless product
              
              Entities::OrderItem.new(
                product_id: item_data[:product_id],
                quantity: item_data[:quantity],
                price: product.price
              )
            end
            
            # Create order
            order = Entities::Order.new(
              id: generate_id,
              customer_id: customer_id,
              items: items
            )
            
            # Save order
            @order_repository.save(order)
            
            # Send notification
            @notification_service.order_created(order)
            
            order
          end
          
          private
          
          def generate_id
            SecureRandom.uuid
          end
        end
        
        class ConfirmOrder
          def initialize(order_repository, payment_service, notification_service)
            @order_repository = order_repository
            @payment_service = payment_service
            @notification_service = notification_service
          end
          
          def execute(order_id, payment_details)
            # Find order
            order = @order_repository.find(order_id)
            raise "Order not found: #{order_id}" unless order
            
            # Process payment
            payment_result = @payment_service.process_payment(
              amount: order.total,
              payment_details: payment_details
            )
            
            raise "Payment failed" unless payment_result.success?
            
            # Confirm order
            order.confirm
            @order_repository.save(order)
            
            # Send notification
            @notification_service.order_confirmed(order)
            
            order
          end
        end
      end
      
      # Interface Adapters Layer (Infrastructure)
      module Interfaces
        # Repository Interface
        class OrderRepository
          def find(id)
            raise NotImplementedError
          end
          
          def save(order)
            raise NotImplementedError
          end
          
          def find_by_customer(customer_id)
            raise NotImplementedError
          end
        end
        
        # Notification Service Interface
        class NotificationService
          def order_created(order)
            raise NotImplementedError
          end
          
          def order_confirmed(order)
            raise NotImplementedError
          end
          
          def order_cancelled(order)
            raise NotImplementedError
          end
        end
        
        # Payment Service Interface
        class PaymentService
          def process_payment(amount:, payment_details:)
            raise NotImplementedError
          end
        end
      end
      
      # Infrastructure Layer (Implementation)
      module Infrastructure
        class InMemoryOrderRepository < Interfaces::OrderRepository
          def initialize
            @orders = {}
          end
          
          def find(id)
            @orders[id]
          end
          
          def save(order)
            @orders[order.id] = order
          end
          
          def find_by_customer(customer_id)
            @orders.values.select { |order| order.customer_id == customer_id }
          end
        end
        
        class EmailNotificationService < Interfaces::NotificationService
          def order_created(order)
            puts "Order created notification sent for order #{order.id}"
          end
          
          def order_confirmed(order)
            puts "Order confirmed notification sent for order #{order.id}"
          end
          
          def order_cancelled(order)
            puts "Order cancelled notification sent for order #{order.id}"
          end
        end
        
        class MockPaymentService < Interfaces::PaymentService
          def process_payment(amount:, payment_details:)
            # Mock payment processing
            success = amount > 0 && payment_details[:card_number]
            PaymentResult.new(success: success, transaction_id: SecureRandom.hex(8))
          end
        end
        
        class PaymentResult
          attr_reader :success, :transaction_id
          
          def initialize(success:, transaction_id:)
            @success = success
            @transaction_id = transaction_id
          end
        end
      end
      
      # Application Layer (Orchestration)
      module Application
        class OrderService
          def initialize(order_repository, product_repository, payment_service, notification_service)
            @create_order_use_case = UseCases::CreateOrder.new(
              order_repository, product_repository, notification_service
            )
            @confirm_order_use_case = UseCases::ConfirmOrder.new(
              order_repository, payment_service, notification_service
            )
          end
          
          def create_order(customer_id, items_data)
            @create_order_use_case.execute(customer_id, items_data)
          end
          
          def confirm_order(order_id, payment_details)
            @confirm_order_use_case.execute(order_id, payment_details)
          end
        end
      end
      
      # Usage example
      def self.demonstrate_clean_architecture
        puts "Demonstrating Clean Architecture:"
        puts "=" * 40
        
        # Setup infrastructure
        order_repository = Infrastructure::InMemoryOrderRepository.new
        notification_service = Infrastructure::EmailNotificationService.new
        payment_service = Infrastructure::MockPaymentService.new
        
        # Mock product repository
        product_repository = double('ProductRepository')
        allow(product_repository).to receive(:find).with('prod_1').and_return(
          double('Product', id: 'prod_1', price: 10.0)
        )
        allow(product_repository).to receive(:find).with('prod_2').and_return(
          double('Product', id: 'prod_2', price: 20.0)
        )
        
        # Create application service
        order_service = Application::OrderService.new(
          order_repository, product_repository, payment_service, notification_service
        )
        
        # Create order
        order = order_service.create_order(
          'customer_1',
          [
            { product_id: 'prod_1', quantity: 2 },
            { product_id: 'prod_2', quantity: 1 }
          ]
        )
        
        puts "Created order: #{order.id}"
        puts "Order total: #{order.total}"
        puts "Order status: #{order.status}"
        
        # Confirm order
        confirmed_order = order_service.confirm_order(
          order.id,
          { card_number: '4111111111111111', cvv: '123' }
        )
        
        puts "Confirmed order: #{confirmed_order.id}"
        puts "Order status: #{confirmed_order.status}"
      end
    RUBY
    
    puts "Clean Architecture Example:"
    puts clean_architecture_example
  end
  
  # Run clean architecture examples
  clean_architecture_principles
  clean_architecture_example
end
```

## 🔧 Code Quality

### 1. SOLID Principles

Applying SOLID principles in Ruby:

```ruby
class SOLIDPrinciples
  def self.solid_principles
    puts "SOLID Principles in Ruby:"
    puts "=" * 50
    
    principles = [
      {
        principle: "S - Single Responsibility",
        description: "A class should have only one reason to change",
        ruby_example: "Separate validation, persistence, and business logic"
      },
      {
        principle: "O - Open/Closed",
        description: "Open for extension, closed for modification",
        ruby_example: "Use modules and inheritance for extension"
      },
      {
        principle: "L - Liskov Substitution",
        description: "Subtypes must be substitutable for base types",
        ruby_example: "Ensure child classes can replace parent classes"
      },
      {
        principle: "I - Interface Segregation",
        description: "Many small, focused interfaces",
        ruby_example: "Use modules for focused behavior"
      },
      {
        principle: "D - Dependency Inversion",
        description: "Depend on abstractions, not concretions",
        ruby_example: "Use dependency injection"
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Ruby Example: #{principle[:ruby_example]}"
      puts
    end
  end
  
  def self.solid_example
    puts "\nSOLID Principles Example:"
    puts "=" * 50
    
    solid_example = <<~RUBY
      # S - Single Responsibility Principle
      class UserValidator
        def self.validate(user_data)
          errors = []
          
          errors << "Name is required" if user_data[:name].nil? || user_data[:name].empty?
          errors << "Email is required" if user_data[:email].nil? || user_data[:email].empty?
          errors << "Invalid email format" unless user_data[:email]&.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
          
          errors
        end
      end
      
      class UserRepository
        def initialize(database)
          @database = database
        end
        
        def save(user)
          @database.save('users', user.to_h)
        end
        
        def find(id)
          data = @database.find('users', id)
          data ? User.new(data) : nil
        end
      end
      
      class UserNotifier
        def self.send_welcome_email(user)
          puts "Sending welcome email to #{user.email}"
        end
      end
      
      # O - Open/Closed Principle
      class ReportGenerator
        def generate_report(data, format)
          formatter = create_formatter(format)
          formatter.format(data)
        end
        
        private
        
        def create_formatter(format)
          case format
          when :json
            JSONFormatter.new
          when :xml
            XMLFormatter.new
          when :csv
            CSVFormatter.new
          else
            raise "Unsupported format: #{format}"
          end
        end
      end
      
      class JSONFormatter
        def format(data)
          data.to_json
        end
      end
      
      class XMLFormatter
        def format(data)
          # XML formatting logic
          "<data>#{data}</data>"
        end
      end
      
      class CSVFormatter
        def format(data)
          # CSV formatting logic
          data.keys.join(',') + "\n" + data.values.join(',')
        end
      end
      
      # L - Liskov Substitution Principle
      class Bird
        def fly
          raise NotImplementedError
        end
        
        def make_sound
          raise NotImplementedError
        end
      end
      
      class Sparrow < Bird
        def fly
          "Sparrow flying"
        end
        
        def make_sound
          "Chirp chirp"
        end
      end
      
      class Penguin < Bird
        def fly
          raise "Penguins can't fly"
        end
        
        def make_sound
          "Squawk squawk"
        end
      end
      
      # I - Interface Segregation Principle
      module Readable
        def read
          raise NotImplementedError
        end
      end
      
      module Writable
        def write(data)
          raise NotImplementedError
        end
      end
      
      module Deletable
        def delete
          raise NotImplementedError
        end
      end
      
      class ReadOnlyFile
        include Readable
        
        def initialize(path)
          @path = path
        end
        
        def read
          File.read(@path)
        end
      end
      
      class ReadWriteFile
        include Readable
        include Writable
        
        def initialize(path)
          @path = path
        end
        
        def read
          File.read(@path)
        end
        
        def write(data)
          File.write(@path, data)
        end
      end
      
      # D - Dependency Inversion Principle
      class EmailService
        def initialize(email_provider)
          @email_provider = email_provider
        end
        
        def send_email(to, subject, body)
          @email_provider.send(to, subject, body)
        end
      end
      
      class SMTPProvider
        def send(to, subject, body)
          puts "Sending email via SMTP to #{to}: #{subject}"
        end
      end
      
      class SendGridProvider
        def send(to, subject, body)
          puts "Sending email via SendGrid to #{to}: #{subject}"
        end
      end
      
      # Usage example
      class UserService
        def initialize(validator, repository, notifier)
          @validator = validator
          @repository = repository
          @notifier = notifier
        end
        
        def create_user(user_data)
          # Validate user data
          errors = @validator.validate(user_data)
          raise "Validation failed: #{errors.join(', ')}" if errors.any?
          
          # Create user
          user = User.new(user_data)
          
          # Save user
          @repository.save(user)
          
          # Send welcome email
          @notifier.send_welcome_email(user)
          
          user
        end
      end
      
      class User
        attr_reader :id, :name, :email
        
        def initialize(data)
          @id = data[:id] || SecureRandom.uuid
          @name = data[:name]
          @email = data[:email]
        end
        
        def to_h
          { id: @id, name: @name, email: @email }
        end
      end
      
      # Demonstration
      def self.demonstrate_solid_principles
        puts "Demonstrating SOLID Principles:"
        puts "=" * 40
        
        # Single Responsibility
        database = double('Database')
        allow(database).to receive(:save).with('users', anything)
        allow(database).to receive(:find).with('users', anything).and_return({ id: '1', name: 'John', email: 'john@example.com' })
        
        repository = UserRepository.new(database)
        validator = UserValidator
        notifier = UserNotifier
        
        user_service = UserService.new(validator, repository, notifier)
        
        user = user_service.create_user(
          name: "John Doe",
          email: "john@example.com"
        )
        
        puts "Created user: #{user.name} (#{user.email})"
        
        # Open/Closed
        report_generator = ReportGenerator.new
        data = { name: "John", age: 30 }
        
        json_report = report_generator.generate_report(data, :json)
        xml_report = report_generator.generate_report(data, :xml)
        
        puts "JSON report: #{json_report}"
        puts "XML report: #{xml_report}"
        
        # Liskov Substitution
        birds = [Sparrow.new, Penguin.new]
        
        birds.each do |bird|
          begin
            puts "#{bird.class.name} says: #{bird.make_sound}"
            puts "#{bird.class.name} action: #{bird.fly}"
          rescue => e
            puts "#{bird.class.name} error: #{e.message}"
          end
        end
        
        # Interface Segregation
        read_only_file = ReadOnlyFile.new('test.txt')
        read_write_file = ReadWriteFile.new('test.txt')
        
        read_only_file.read
        read_write_file.write("test data")
        read_write_file.read
        
        # Dependency Inversion
        smtp_provider = SMTPProvider.new
        sendgrid_provider = SendGridProvider.new
        
        smtp_service = EmailService.new(smtp_provider)
        sendgrid_service = EmailService.new(sendgrid_provider)
        
        smtp_service.send_email('test@example.com', 'Test', 'Test body')
        sendgrid_service.send_email('test@example.com', 'Test', 'Test body')
      end
    RUBY
    
    puts "SOLID Principles Example:"
    puts solid_example
  end
  
  # Run SOLID principles examples
  solid_principles
  solid_example
end
```

### 2. Code Documentation

Effective documentation practices:

```ruby
class CodeDocumentation
  def self.documentation_guidelines
    puts "Documentation Guidelines:"
    puts "=" * 50
    
    guidelines = [
      {
        type: "Class Documentation",
        description: "Document class purpose and usage",
        elements: [
          "Class purpose",
          "Usage examples",
          "Important methods",
          "Dependencies",
          "Thread safety"
        ]
      },
      {
        type: "Method Documentation",
        description: "Document method behavior and parameters",
        elements: [
          "Method purpose",
          "Parameters and types",
          "Return value",
          "Exceptions",
          "Examples"
        ]
      },
      {
        type: "Module Documentation",
        description: "Document module functionality",
        elements: [
          "Module purpose",
          "Included modules",
          "Public methods",
          "Usage examples"
        ]
      },
      {
        type: "Inline Comments",
        description: "Explain complex logic",
        elements: [
          "Why, not what",
          "Complex algorithms",
          "Business rules",
          "Temporary workarounds"
        ]
      }
    ]
    
    guidelines.each do |guideline|
      puts "#{guideline[:type]}:"
      puts "  Description: #{guideline[:description]}"
      puts "  Elements: #{guideline[:elements].join(', ')}"
      puts
    end
  end
  
  def self.documentation_example
    puts "\nDocumentation Example:"
    puts "=" * 50
    
    documentation_example = <<~RUBY
      # User Management Module
      #
      # This module provides functionality for managing users in the system.
      # It includes user creation, validation, and authentication.
      #
      # @example Basic Usage
      #   user = UserManagement.create_user(
      #     name: "John Doe",
      #     email: "john@example.com",
      #     password: "secure_password"
      #   )
      #
      #   if UserManagement.authenticate?(email, password)
      #     puts "User authenticated successfully"
      #   end
      #
      # @author Ruby Development Team
      # @since 1.0.0
      module UserManagement
        # Creates a new user with the given parameters
        #
        # @param name [String] The user's full name
        # @param email [String] The user's email address
        # @param password [String] The user's password
        # @param options [Hash] Additional options
        # @option options [String] :role The user's role (default: 'user')
        # @option options [Boolean] :active Whether the user is active (default: true)
        #
        # @return [User] The created user
        #
        # @raise [ArgumentError] If required parameters are missing
        # @raise [ValidationError] If validation fails
        #
        # @example Create a user with default options
        #   user = create_user("John Doe", "john@example.com", "password123")
        #
        # @example Create a user with custom role
        #   user = create_user("Admin", "admin@example.com", "admin123", role: 'admin')
        #
        # @note Password will be automatically hashed
        # @see User#authenticate?
        def self.create_user(name, email, password, options = {})
          # Validate required parameters
          raise ArgumentError, "Name is required" if name.nil? || name.empty?
          raise ArgumentError, "Email is required" if email.nil? || email.empty?
          raise ArgumentError, "Password is required" if password.nil? || password.empty?
          
          # Validate email format
          unless email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
            raise ValidationError, "Invalid email format"
          end
          
          # Validate password strength
          validate_password_strength(password)
          
          # Create user
          user = User.new(
            name: name,
            email: email,
            password_hash: hash_password(password),
            role: options[:role] || 'user',
            active: options.fetch(:active, true)
          )
          
          # Save user to database
          user.save!
          
          # Send welcome email
          UserNotifier.send_welcome_email(user)
          
          user
        end
        
        # Authenticates a user with email and password
        #
        # @param email [String] The user's email address
        # @param password [String] The user's password
        #
        # @return [User, nil] The authenticated user or nil if authentication fails
        #
        # @example Authenticate a user
        #   user = authenticate?("john@example.com", "password123")
        #   if user
        #     puts "User authenticated: #{user.name}"
        #   else
        #     puts "Authentication failed"
        #   end
        #
        # @note Uses bcrypt for password verification
        def self.authenticate?(email, password)
          # Find user by email
          user = find_by_email(email)
          return nil unless user
          
          # Check if user is active
          return nil unless user.active?
          
          # Verify password
          user.authenticate?(password) ? user : nil
        end
        
        private
        
        # Validates password strength requirements
        #
        # @param password [String] The password to validate
        #
        # @raise [ValidationError] If password doesn't meet requirements
        def self.validate_password_strength(password)
          errors = []
          
          # Minimum length requirement
          if password.length < 8
            errors << "Password must be at least 8 characters long"
          end
          
          # Uppercase letter requirement
          unless password.match?(/[A-Z]/)
            errors << "Password must contain at least one uppercase letter"
          end
          
          # Lowercase letter requirement
          unless password.match?(/[a-z]/)
            errors << "Password must contain at least one lowercase letter"
          end
          
          # Number requirement
          unless password.match?(/\d/)
            errors << "Password must contain at least one number"
          end
          
          # Special character requirement
          unless password.match?(/[!@#$%^&*]/)
            errors << "Password must contain at least one special character"
          end
          
          raise ValidationError, errors.join(', ') if errors.any?
        end
        
        # Hashes password using bcrypt
        #
        # @param password [String] The password to hash
        # @return [String] The hashed password
        def self.hash_password(password)
          BCrypt::Password.create(password)
        end
      end
      
      # User model
      #
      # Represents a user in the system with authentication and profile information.
      #
      # @attr_reader [String] id The user's unique identifier
      # @attr_reader [String] name The user's full name
      # @attr_reader [String] email The user's email address
      # @attr_reader [String] role The user's role
      # @attr_reader [Boolean] active Whether the user is active
      #
      # @example Create a new user
      #   user = User.new(
      #     id: "12345",
      #     name: "John Doe",
      #     email: "john@example.com",
      #     password_hash: "hashed_password",
      #     role: "user",
      #     active: true
      #   )
      class User
        attr_reader :id, :name, :email, :role, :active
        
        # Initializes a new user
        #
        # @param id [String] The user's unique identifier
        # @param name [String] The user's full name
        # @param email [String] The user's email address
        # @param password_hash [String] The hashed password
        # @param role [String] The user's role
        # @param active [Boolean] Whether the user is active
        def initialize(id:, name:, email:, password_hash:, role:, active:)
          @id = id
          @name = name
          @email = email
          @password_hash = password_hash
          @role = role
          @active = active
        end
        
        # Authenticates the user with a password
        #
        # @param password [String] The password to verify
        # @return [Boolean] True if the password is correct
        #
        # @example Authenticate user
        #   if user.authenticate?("password123")
        #     puts "Authentication successful"
        #   else
        #     puts "Authentication failed"
        #   end
        def authenticate?(password)
          BCrypt::Password.new(@password_hash) == password
        end
        
        # Updates the user's profile information
        #
        # @param attributes [Hash] The attributes to update
        # @option attributes [String] :name The new name
        # @option attributes [String] :email The new email
        # @option attributes [String] :role The new role
        #
        # @return [User] The updated user
        #
        # @example Update user name
        #   user.update_profile(name: "Jane Doe")
        def update_profile(attributes)
          # This would update the user in the database
          # For example purposes, we'll just return the updated user
          self.class.new(
            id: @id,
            name: attributes[:name] || @name,
            email: attributes[:email] || @email,
            password_hash: @password_hash,
            role: attributes[:role] || @role,
            active: @active
          )
        end
        
        # Deactivates the user account
        #
        # @return [User] The deactivated user
        #
        # @example Deactivate user
        #   user.deactivate!
        #   puts "User is now inactive" unless user.active?
        def deactivate!
          # This would update the user in the database
          @active = false
          self
        end
        
        # Returns whether the user is an admin
        #
        # @return [Boolean] True if the user is an admin
        #
        # @example Check if user is admin
        #   if user.admin?
        #     puts "User has admin privileges"
        #   end
        def admin?
          @role == 'admin'
        end
        
        # Returns the user's display name
        #
        # @return [String] The user's display name
        #
        # @example Get display name
        #   puts "Hello, #{user.display_name}!"
        def display_name
          @name
        end
      end
    RUBY
    
    puts "Documentation Example:"
    puts documentation_example
  end
  
  # Run documentation examples
  documentation_guidelines
  documentation_example
end
```

## 🔄 Refactoring Techniques

### 1. Code Refactoring

Systematic refactoring approaches:

```ruby
class CodeRefactoring
  def self.refactoring_techniques
    puts "Refactoring Techniques:"
    puts "=" * 50
    
    techniques = [
      {
        technique: "Extract Method",
        description: "Extract a method from existing code",
        when: "Method is too long or does multiple things",
        benefit: "Improves readability and reusability"
      },
      {
        technique: "Extract Class",
        description: "Extract a class from existing code",
        when: "Class has too many responsibilities",
        benefit: "Improves cohesion and reduces complexity"
      },
      {
        technique: "Replace Conditional with Polymorphism",
        description: "Replace conditionals with polymorphism",
        when: "Complex conditional logic",
        benefit: "Reduces complexity and improves extensibility"
      },
      {
        technique: "Introduce Parameter Object",
        description: "Replace long parameter lists with objects",
        when: "Method has too many parameters",
        benefit: "Improves readability and maintainability"
      },
      {
        technique: "Replace Magic Number with Symbolic Constant",
        description: "Replace magic numbers with named constants",
        when: "Hardcoded numbers in code",
        benefit: "Improves readability and maintainability"
      }
    ]
    
    techniques.each do |technique|
      puts "#{technique[:technique]}:"
      puts "  Description: #{technique[:description]}"
      puts "  When: #{technique[:when]}"
      puts "  Benefit: #{technique[:benefit]}"
      puts
    end
  end
  
  def self.refactoring_example
    puts "\nRefactoring Example:"
    puts "=" * 50
    
    refactoring_example = <<~RUBY
      # Before Refactoring
      class OrderProcessor
        def process_order(order_data)
          # Validate order data
          errors = []
          errors << "Customer ID is required" if order_data[:customer_id].nil? || order_data[:customer_id].empty?
          errors << "Items are required" if order_data[:items].nil? || order_data[:items].empty?
          errors << "Shipping address is required" if order_data[:shipping_address].nil?
          
          raise "Validation failed: #{errors.join(', ')}" if errors.any?
          
          # Calculate total
          total = 0
          order_data[:items].each do |item|
            price = item[:price]
            quantity = item[:quantity]
            subtotal = price * quantity
            total += subtotal
          end
          
          # Apply discount
          discount = 0
          if order_data[:customer_id] == 'premium_customer'
            discount = total * 0.1
          end
          
          # Calculate shipping
          shipping = 0
          if order_data[:shipping_address][:country] == 'US'
            shipping = 5.0
          elsif order_data[:shipping_address][:country] == 'CA'
            shipping = 10.0
          else
            shipping = 15.0
          end
          
          # Calculate tax
          tax_rate = 0.08
          if order_data[:shipping_address][:country] == 'CA'
            tax_rate = 0.13
          end
          
          tax = (total - discount) * tax_rate
          
          # Final total
          final_total = total - discount + shipping + tax
          
          # Create order
          order = {
            id: SecureRandom.uuid,
            customer_id: order_data[:customer_id],
            items: order_data[:items],
            total: final_total,
            discount: discount,
            shipping: shipping,
            tax: tax,
            status: 'pending',
            created_at: Time.now
          }
          
          # Save order
          Database.save('orders', order)
          
          # Send notification
          EmailService.send_order_confirmation(order)
          
          order
        end
      end
      
      # After Refactoring
      class OrderProcessor
        def process_order(order_data)
          # Validate order data
          validate_order_data(order_data)
          
          # Calculate order total
          total = calculate_order_total(order_data)
          
          # Apply discounts
          discount = calculate_discount(order_data[:customer_id], total)
          
          # Calculate shipping
          shipping = calculate_shipping(order_data[:shipping_address])
          
          # Calculate tax
          tax = calculate_tax(order_data[:shipping_address], total - discount)
          
          # Calculate final total
          final_total = total - discount + shipping + tax
          
          # Create order
          order = create_order(order_data, final_total, discount, shipping, tax)
          
          # Save order
          save_order(order)
          
          # Send notification
          send_notification(order)
          
          order
        end
        
        private
        
        def validate_order_data(order_data)
          validator = OrderValidator.new
          validator.validate(order_data)
        end
        
        def calculate_order_total(order_data)
          calculator = OrderCalculator.new
          calculator.calculate_total(order_data[:items])
        end
        
        def calculate_discount(customer_id, total)
          discount_calculator = DiscountCalculator.new
          discount_calculator.calculate_discount(customer_id, total)
        end
        
        def calculate_shipping(shipping_address)
          shipping_calculator = ShippingCalculator.new
          shipping_calculator.calculate_shipping(shipping_address)
        end
        
        def calculate_tax(shipping_address, amount)
          tax_calculator = TaxCalculator.new
          tax_calculator.calculate_tax(shipping_address, amount)
        end
        
        def create_order(order_data, total, discount, shipping, tax)
          Order.new(
            id: SecureRandom.uuid,
            customer_id: order_data[:customer_id],
            items: order_data[:items],
            total: total,
            discount: discount,
            shipping: shipping,
            tax: tax,
            status: 'pending',
            created_at: Time.now
          )
        end
        
        def save_order(order)
          OrderRepository.new.save(order)
        end
        
        def send_notification(order)
          NotificationService.send_order_confirmation(order)
        end
      end
      
      # Supporting classes
      class OrderValidator
        def validate(order_data)
          errors = []
          
          errors << "Customer ID is required" if order_data[:customer_id].nil? || order_data[:customer_id].empty?
          errors << "Items are required" if order_data[:items].nil? || order_data[:items].empty?
          errors << "Shipping address is required" if order_data[:shipping_address].nil?
          
          raise "Validation failed: #{errors.join(', ')}" if errors.any?
        end
      end
      
      class OrderCalculator
        def calculate_total(items)
          items.sum { |item| item[:price] * item[:quantity] }
        end
      end
      
      class DiscountCalculator
        PREMIUM_DISCOUNT = 0.1
        
        def calculate_discount(customer_id, total)
          return 0 unless customer_id == 'premium_customer'
          
          total * PREMIUM_DISCOUNT
        end
      end
      
      class ShippingCalculator
        SHIPPING_RATES = {
          'US' => 5.0,
          'CA' => 10.0,
          'DEFAULT' => 15.0
        }.freeze
        
        def calculate_shipping(shipping_address)
          country = shipping_address[:country]
          SHIPPING_RATES[country] || SHIPPING_RATES['DEFAULT']
        end
      end
      
      class TaxCalculator
        TAX_RATES = {
          'US' => 0.08,
          'CA' => 0.13,
          'DEFAULT' => 0.08
        }.freeze
        
        def calculate_tax(shipping_address, amount)
          country = shipping_address[:country]
          rate = TAX_RATES[country] || TAX_RATES['DEFAULT']
          
          amount * rate
        end
      end
      
      class Order
        attr_reader :id, :customer_id, :items, :total, :discount, :shipping, :tax, :status, :created_at
        
        def initialize(id:, customer_id:, items:, total:, discount:, shipping:, tax:, status:, created_at:)
          @id = id
          @customer_id = customer_id
          @items = items
          @total = total
          @discount = discount
          @shipping = shipping
          @tax = tax
          @status = status
          @created_at = created_at
        end
      end
      
      class OrderRepository
        def save(order)
          Database.save('orders', order.to_h)
        end
      end
      
      class NotificationService
        def self.send_order_confirmation(order)
          EmailService.send_order_confirmation(order)
        end
      end
      
      # Demonstration
      def self.demonstrate_refactoring
        puts "Demonstrating Refactoring:"
        puts "=" * 40
        
        # Mock dependencies
        Database = double('Database')
        EmailService = double('EmailService')
        allow(Database).to receive(:save)
        allow(EmailService).to receive(:send_order_confirmation)
        
        # Create order processor
        processor = OrderProcessor.new
        
        # Process order
        order_data = {
          customer_id: 'premium_customer',
          items: [
            { price: 10.0, quantity: 2 },
            { price: 20.0, quantity: 1 }
          ],
          shipping_address: {
            country: 'US'
          }
        }
        
        order = processor.process_order(order_data)
        
        puts "Processed order: #{order.id}"
        puts "Order total: #{order.total}"
        puts "Order discount: #{order.discount}"
        puts "Order shipping: #{order.shipping}"
        puts "Order tax: #{order.tax}"
      end
    RUBY
    
    puts "Refactoring Example:"
    puts refactoring_example
  end
  
  # Run refactoring examples
  refactoring_techniques
  refactoring_example
end
```

## 🎯 Maintainability Best Practices

### 1. Maintainability Guidelines

Comprehensive maintainability guidelines:

```ruby
class MaintainabilityGuidelines
  def self.maintainability_principles
    puts "Maintainability Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Readability",
        description: "Code should be easy to read and understand",
        practices: [
          "Use meaningful names",
          "Write small methods",
          "Add comments when necessary",
          "Follow consistent style"
        ]
      },
      {
        principle: "Simplicity",
        description: "Keep code simple and straightforward",
        practices: [
          "Avoid over-engineering",
          "Use simple solutions",
          "Minimize complexity",
          "Prefer clarity over cleverness"
        ]
      },
      {
        principle: "Consistency",
        description: "Maintain consistent coding standards",
        practices: [
          "Follow naming conventions",
          "Use consistent patterns",
          "Maintain consistent structure",
          "Use consistent formatting"
        ]
      },
      {
        principle: "Modularity",
        description: "Break code into small, focused modules",
        practices: [
          "Single responsibility principle",
          "Loose coupling",
          "High cohesion",
          "Clear interfaces"
        ]
      },
      {
        principle: "Testability",
        description: "Code should be easy to test",
        practices: [
          "Dependency injection",
          "Small methods",
          "Pure functions",
          "Minimal side effects"
        ]
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Practices: #{principle[:practices].join(', ')}"
      puts
    end
  end
  
  def self.code_smells
    puts "\nCode Smells to Avoid:"
    puts "=" * 50
    
    code_smells = [
      {
        smell: "Long Method",
        description: "Method is too long and does too many things",
        solution: "Extract smaller methods with single responsibilities"
      },
      {
        smell: "Large Class",
        description: "Class has too many responsibilities",
        solution: "Split into smaller, focused classes"
      },
      {
        smell: "Duplicate Code",
        description: "Similar or identical code in multiple places",
        solution: "Extract common methods or use inheritance"
      },
      {
        smell: "Long Parameter List",
        description: "Method has too many parameters",
        solution: "Use parameter objects or options hash"
      },
      {
        smell: "Feature Envy",
        description: "Class uses another class more than its own data",
        solution: "Move methods to appropriate class"
      },
      {
        smell: "Data Clumps",
        description: "Group of data that appears together",
        solution: "Extract into a class or value object"
      },
      {
        smell: "Primitive Obsession",
        description: "Overuse of primitive types instead of objects",
        solution: "Create value objects for domain concepts"
      },
      {
        smell: "Inappropriate Intimacy",
        description: "Class knows too much about another class",
        solution: "Reduce coupling and improve encapsulation"
      }
    ]
    
    code_smells.each do |smell|
      puts "#{smell[:smell]}:"
      puts "  Description: #{smell[:description]}"
      puts "  Solution: #{smell[:solution]}"
      puts
    end
  end
  
  def self.maintainability_checklist
    puts "\nMaintainability Checklist:"
    puts "=" * 50
    
    checklist = [
      "□ Code is easy to read and understand",
      "□ Methods are small and focused",
      "□ Classes have single responsibilities",
      "□ Naming conventions are followed",
      "□ Code is well-documented",
      "□ Tests are comprehensive and maintainable",
      "□ Dependencies are minimal and explicit",
      "□ Error handling is consistent",
      "□ Code is modular and loosely coupled",
      "□ Technical debt is tracked and managed",
      "□ Code reviews are conducted regularly",
      "□ Refactoring is done continuously",
      "□ Performance is considered without sacrificing readability"
    ]
    
    checklist.each { |item| puts item }
  end
  
  # Run maintainability guidelines examples
  maintainability_principles
  code_smells
  maintainability_checklist
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Modular Design**: Create modular Ruby code
2. **SOLID Principles**: Apply SOLID principles
3. **Code Documentation**: Document Ruby code effectively

### Intermediate Exercises

1. **Clean Architecture**: Implement clean architecture
2. **Refactoring**: Refactor legacy Ruby code
3. **Code Quality**: Improve code quality metrics

### Advanced Exercises

1. **Domain-Driven Design**: Implement DDD concepts
2. **Architecture Patterns**: Apply architecture patterns
3. **Code Reviews**: Conduct effective code reviews

---

## 🎯 Summary

Maintainability in Ruby provides:

- **Code Organization** - Modular architecture and clean code
- **Code Quality** - SOLID principles and documentation
- **Refactoring** - Systematic code improvement
- **Maintainability Guidelines** - Best practices for maintainable code

Master these techniques to build maintainable Ruby applications that stand the test of time!
