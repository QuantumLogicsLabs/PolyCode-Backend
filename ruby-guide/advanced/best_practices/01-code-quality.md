# Code Quality in Ruby
# Comprehensive guide to writing clean, maintainable Ruby code

## 🎯 Overview

Code quality is essential for maintainable, readable, and robust Ruby applications. This guide covers coding standards, best practices, and techniques for writing high-quality Ruby code.

## 📏 Ruby Coding Standards

### 1. Naming Conventions

Consistent naming conventions for better readability:

```ruby
class NamingConventions
  def self.demonstrate_naming_rules
    puts "Ruby Naming Conventions:"
    puts "=" * 50
    
    # Class names - PascalCase
    class UserAccountManager
      def initialize(user_service, payment_service)
        @user_service = user_service
        @payment_service = payment_service
      end
      
      def create_user_account(user_data)
        # Method names - snake_case
        validated_data = validate_user_input(user_data)
        
        # Variable names - snake_case
        user_account = @user_service.create_user(validated_data)
        
        # Constant names - SCREAMING_SNAKE_CASE
        DEFAULT_ACCOUNT_TYPE = "standard"
        
        account_type = user_data[:account_type] || DEFAULT_ACCOUNT_TYPE
        
        setup_payment_account(user_account, account_type)
      end
      
      private
      
      def validate_user_input(user_data)
        # Local variables - snake_case
        required_fields = %w[name email password]
        
        # Check for missing required fields
        missing_fields = required_fields.select { |field| user_data[field].nil? || user_data[field].empty? }
        
        unless missing_fields.empty?
          raise ArgumentError, "Missing required fields: #{missing_fields.join(', ')}"
        end
        
        # Email validation
        unless user_data[:email].match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
          raise ArgumentError, "Invalid email format"
        end
        
        # Password validation
        password = user_data[:password]
        unless password.length >= 8
          raise ArgumentError, "Password must be at least 8 characters"
        end
        
        user_data
      end
      
      def setup_payment_account(user_account, account_type)
        payment_account_id = @payment_service.create_account(
          user_id: user_account.id,
          account_type: account_type
        )
        
        user_account.payment_account_id = payment_account_id
        user_account
      end
    end
    
    # Module names - PascalCase
    module PaymentProcessing
      # Constant names - SCREAMING_SNAKE_CASE
      MAX_RETRY_ATTEMPTS = 3
      DEFAULT_CURRENCY = "USD"
      
      class TransactionProcessor
        def initialize(gateway_service)
          @gateway_service = gateway_service
          @retry_count = 0
        end
        
        def process_transaction(transaction_data)
          # Method parameter names - snake_case
          amount = transaction_data[:amount]
          currency = transaction_data[:currency] || DEFAULT_CURRENCY
          recipient_account = transaction_data[:recipient_account]
          
          validate_transaction_data(amount, currency, recipient_account)
          
          begin
            result = @gateway_service.process_payment(
              amount: amount,
              currency: currency,
              recipient: recipient_account
            )
            
            log_successful_transaction(result)
            result
            
          rescue => e
            handle_transaction_error(e, transaction_data)
          end
        end
        
        private
        
        def validate_transaction_data(amount, currency, recipient_account)
          # Instance variables - snake_case with @ prefix
          raise ArgumentError, "Amount must be positive" if amount <= 0
          raise ArgumentError, "Currency is required" if currency.nil? || currency.empty?
          
          # Use meaningful variable names
          valid_currencies = %w[USD EUR GBP JPY]
          unless valid_currencies.include?(currency)
            raise ArgumentError, "Invalid currency: #{currency}"
          end
          
          raise ArgumentError, "Recipient account is required" if recipient_account.nil? || recipient_account.empty?
        end
        
        def log_successful_transaction(result)
          # Use descriptive method names
          transaction_id = result[:transaction_id]
          status = result[:status]
          
          puts "Transaction #{transaction_id} completed with status: #{status}"
        end
        
        def handle_transaction_error(error, transaction_data)
          @retry_count += 1
          
          if @retry_count < MAX_RETRY_ATTEMPTS
            puts "Retrying transaction (attempt #{@retry_count}/#{MAX_RETRY_ATTEMPTS})"
            sleep(1)  # Wait before retry
            process_transaction(transaction_data)
          else
            puts "Transaction failed after #{MAX_RETRY_ATTEMPTS} attempts: #{error.message}"
            raise error
          end
        end
      end
    end
    
    # Demonstrate naming conventions
    puts "Class names (PascalCase): UserAccountManager, PaymentProcessing"
    puts "Method names (snake_case): create_user_account, process_transaction"
    puts "Variable names (snake_case): user_data, transaction_data, amount"
    puts "Constant names (SCREAMING_SNAKE_CASE): MAX_RETRY_ATTEMPTS, DEFAULT_CURRENCY"
    puts "Module names (PascalCase): PaymentProcessing"
  end
  
  def self.file_and_directory_naming
    puts "\nFile and Directory Naming:"
    puts "=" * 50
    
    puts "File naming conventions:"
    file_naming = {
      "Ruby files" => "snake_case.rb (user_service.rb, payment_processor.rb)",
      "Test files" => "test_name_test.rb (user_service_test.rb)",
      "Spec files" => "name_spec.rb (user_service_spec.rb)",
      "Configuration" => "snake_case.yml (database.yml, production.yml)"
    }
    
    file_naming.each { |type, convention| puts "  #{type}: #{convention}" }
    
    puts "\nDirectory naming conventions:"
    directory_naming = {
      "App directories" => "snake_case (app/models/, app/controllers/)",
      "Lib directories" => "snake_case (lib/utils/, lib/services/)",
      "Test directories" => "snake_case (test/unit/, test/integration/)",
      "Config directories" => "snake_case (config/environments/)"
    }
    
    directory_naming.each { |type, convention| puts "  #{type}: #{convention}" }
    
    puts "\nExample file structure:"
    file_structure = [
      "app/",
      "  models/",
      "    user.rb",
      "    user_account.rb",
      "    transaction.rb",
      "  services/",
      "    user_service.rb",
      "    payment_service.rb",
      "    transaction_service.rb",
      "  controllers/",
      "    users_controller.rb",
      "    transactions_controller.rb",
      "  utils/",
      "    date_helper.rb",
      "    string_helper.rb",
      "config/",
      "  database.yml",
      "  production.yml",
      "  test.yml",
      "test/",
      "  unit/",
      "    user_test.rb",
      "    transaction_test.rb",
      "  integration/",
      "    user_service_test.rb",
      "    payment_service_test.rb"
    ]
    
    file_structure.each { |file| puts "  #{file}" }
  end
  
  # Run naming conventions examples
  demonstrate_naming_rules
  file_and_directory_naming
end
```

### 2. Code Formatting and Style

Consistent code formatting for readability:

```ruby
class CodeFormatting
  def self.demonstrate_formatting_rules
    puts "Ruby Code Formatting Rules:"
    puts "=" * 50
    
    # Indentation and spacing
    def well_formatted_method(param1, param2, options = {})
      # Use 2 spaces for indentation
      if param1 && param2
        result = param1 + param2
      elsif param1
        result = param1
      else
        result = 0
      end
      
      # Use spaces around operators
      result = result * options[:multiplier] if options[:multiplier]
      
      # Align similar assignments
      user_name = options[:user_name] || "Unknown"
      user_email = options[:user_email] || "unknown@example.com"
      user_age = options[:user_age] || 0
      
      # Use consistent spacing in method calls
      formatted_result = format_result(result, user_name, user_email, user_age)
      
      formatted_result
    end
    
    def format_result(result, name, email, age)
      {
        calculation_result: result,
        user_info: {
          name: name,
          email: email,
          age: age
        }
      }
    end
    
    # Method chaining with proper indentation
    def method_chaining_example
      users = User.where(active: true)
              .joins(:orders)
              .where('orders.created_at > ?', 1.week.ago)
              .includes(:profile)
              .order('users.created_at DESC')
              .limit(10)
      
      users
    end
    
    # Long method parameters with proper alignment
    def complex_method(
      parameter_one,
      parameter_two,
      parameter_three,
      options = {}
    )
      # Method body with proper indentation
      result = parameter_one + parameter_two
      result = result * parameter_three if parameter_three
      
      # Handle options with proper spacing
      if options[:transform]
        result = result.send(options[:transform])
      end
      
      if options[:format]
        result = format_result(result, options[:format])
      end
      
      result
    end
    
    # Conditional statements with proper formatting
    def conditional_example(user)
      # Use consistent spacing around operators
      if user.age >= 18 && user.active?
        "Adult active user"
      elsif user.age >= 18 && !user.active?
        "Adult inactive user"
      elsif user.age < 18 && user.active?
        "Minor active user"
      else
        "Minor inactive user"
      end
    end
    
    # Array and hash formatting
    def data_structure_example
      # Arrays: one item per line for multi-line arrays
      users = [
        { id: 1, name: "John", age: 25 },
        { id: 2, name: "Jane", age: 30 },
        { id: 3, name: "Bob", age: 35 }
      ]
      
      # Hashes: align values for readability
      config = {
        database: {
          adapter: "postgresql",
          host: "localhost",
          port: 5432,
          pool: 5
        },
        redis: {
          host: "localhost",
          port: 6379,
          db: 0
        }
      }
      
      { users: users, config: config }
    end
    
    # Demonstrate formatting
    puts "1. Indentation: 2 spaces (no tabs)"
    puts "2. Method calls: Space after commas, no spaces before parentheses"
    puts "3. Operators: Space around operators (=, +, -, *, /, etc.)"
    puts "4. Conditionals: Space around comparison operators"
    puts "5. Chaining: Indent method chains properly"
    puts "6. Long parameters: Align parameters vertically"
    puts "7. Arrays/Hashes: One item per line for multi-line structures"
    
    # Show examples
    result = well_formatted_method(10, 20, multiplier: 3)
    puts "\nMethod result: #{result}"
    
    formatted_user = conditional_example({ age: 25, active: true })
    puts "Conditional result: #{formatted_user}"
    
    data = data_structure_example
    puts "\nData structure example:"
    puts "Users: #{data[:users].length} users"
    puts "Config keys: #{data[:config].keys.join(', ')}"
  end
  
  def self.commenting_standards
    puts "\nCommenting Standards:"
    puts "=" * 50
    
    puts "Commenting guidelines:"
    guidelines = [
      {
        type: "Class comments",
        format: "# Class description goes here",
        example: "class User\n  # User model for managing user data\n  class User\n  end"
      },
      {
        type: "Method comments",
        format: "# Method description goes here",
        example: "def create_user(params)\n  # Creates a new user with the given parameters\n  def create_user(params)\n  end"
      },
      {
        type: "Inline comments",
        format: "# Explain complex logic",
        example: "result = x * y # Calculate the product"
      },
      {
        type: "TODO comments",
        format: "# TODO: Description of what needs to be done",
        example: "# TODO: Add input validation"
      },
      {
        type: "FIXME comments",
        format: "# FIXME: Description of what needs to be fixed",
        example: "# FIXME: Handle edge case for empty array"
      },
      {
        type: "NOTE comments",
        format: "# NOTE: Additional information or explanation",
        example: "# NOTE: This method is deprecated, use new_method instead"
      }
    ]
    
    guidelines.each { |guideline| puts "#{guideline[:type]}:"; puts "  #{guideline[:format]}"; puts "  Example: #{guideline[:example]}"; puts }
    
    puts "\nExample of well-commented code:"
    example_code = <<~RUBY
      # PaymentProcessor class handles payment transactions
      # It integrates with external payment gateways and provides
      # retry logic for failed transactions
      class PaymentProcessor
        # Maximum number of retry attempts for failed transactions
        MAX_RETRY_ATTEMPTS = 3
        
        # Initialize the payment processor with a gateway service
        # @param gateway_service [GatewayService] The payment gateway service
        def initialize(gateway_service)
          @gateway_service = gateway_service
          @retry_count = 0
        end
        
        # Process a payment transaction
        # @param amount [Numeric] The payment amount
        # @param currency [String] The currency code (USD, EUR, etc.)
        # @param recipient [String] The recipient account identifier
        # @return [Hash] The transaction result
        def process_payment(amount, currency, recipient)
          # Validate input parameters
          raise ArgumentError, "Amount must be positive" if amount <= 0
          
          # Process the payment through the gateway
          begin
            result = @gateway_service.charge(
              amount: amount,
              currency: currency,
              recipient: recipient
            )
            
            # Reset retry count on success
            @retry_count = 0
            result
            
          rescue => e
            # Handle payment errors with retry logic
            handle_payment_error(e, amount, currency, recipient)
          end
        end
        
        private
        
        # Handle payment errors and implement retry logic
        # @param error [Exception] The error that occurred
        # @param amount [Numeric] The payment amount
        # @param currency [String] The currency code
        # @param recipient [String] The recipient account identifier
        def handle_payment_error(error, amount, currency, recipient)
          @retry_count += 1
          
          # Retry if we haven't reached the maximum attempts
          if @retry_count < MAX_RETRY_ATTEMPTS
            puts "Retrying payment (attempt #{@retry_count}/#{MAX_RETRY_ATTEMPTS})"
            sleep(1)
            process_payment(amount, currency, recipient)
          else
            # Re-raise the error if we've exhausted retries
            raise error
          end
        end
      end
    RUBY
    
    puts example_code
  end
  
  # Run formatting examples
  demonstrate_formatting_rules
  commenting_standards
end
```

## 🔍 Code Quality Metrics

### 1. Code Complexity Analysis

Measuring and managing code complexity:

```ruby
class CodeComplexity
  def self.method_complexity_analysis
    puts "Method Complexity Analysis:"
    puts "=" * 50
    
    class ComplexityAnalyzer
      def initialize
        @method_scores = {}
      end
      
      def analyze_method(method_code)
        # Calculate cyclomatic complexity
        complexity = calculate_cyclomatic_complexity(method_code)
        
        # Calculate other metrics
        lines_of_code = count_lines(method_code)
        parameter_count = count_parameters(method_code)
        nesting_depth = calculate_nesting_depth(method_code)
        
        # Calculate overall complexity score
        score = calculate_complexity_score(complexity, lines_of_code, parameter_count, nesting_depth)
        
        {
          cyclomatic_complexity: complexity,
          lines_of_code: lines_of_code,
          parameter_count: parameter_count,
          nesting_depth: nesting_depth,
          score: score,
          rating: get_complexity_rating(score)
        }
      end
      
      def analyze_class(class_code)
        methods = extract_methods(class_code)
        
        method_scores = methods.map { |method| analyze_method(method[:code]) }
        
        class_score = calculate_class_score(method_scores)
        
        {
          method_count: methods.length,
          method_scores: method_scores,
          class_score: class_score,
          rating: get_complexity_rating(class_score)
        }
      end
      
      private
      
      def calculate_cyclomatic_complexity(code)
        # Simplified cyclomatic complexity calculation
        complexity = 1  # Base complexity
        
        # Add complexity for each decision point
        complexity += code.scan(/\b(if\b|\bunless\b|\bcase\b|\bwhile\b|\buntil\b|\bfor\b|\bwhen\b|\belif\b|\belsif\b|\breturn\b|\bnext\b|\bbreak\b|\bretry\b|\braise\b|\bthrow\b).length
        
        # Add complexity for logical operators
        complexity += code.scan(/\&\&|\|\|/).length
        
        complexity
      end
      
      def count_lines(code)
        code.lines.count
      end
      
      def count_parameters(code)
        # Extract method signature
        method_signature = code.match(/def\s+\w+\s*\(([^)]*)\)/)
        return 0 unless method_signature
        
        parameters = method_signature[1]
        return 0 unless parameters
        
        # Count parameters (simplified)
        parameters.split(',').length
      end
      
      def calculate_nesting_depth(code)
        max_depth = 0
        current_depth = 0
        
        code.lines.each do |line|
          # Count opening and closing braces/keywords
          current_depth += line.scan(/\bdo\b|\bbegin\b|\bif\b|\bunless\b|\bcase\b|\bwhile\b|\buntil\b|\bfor\b/).length
          current_depth -= line.scan(/\bend\b|\bwhen\b/).length
          
          max_depth = [max_depth, current_depth].max
        end
        
        max_depth
      end
      
      def calculate_complexity_score(complexity, lines, params, nesting)
        # Weighted score calculation
        score = (complexity * 0.4) + (lines * 0.1) + (params * 0.2) + (nesting * 0.3)
        score.round(2)
      end
      
      def calculate_class_score(method_scores)
        return 0 if method_scores.empty?
        
        # Average method score
        total_score = method_scores.sum { |method| method[:score] }
        total_score / method_scores.length
      end
      
      def get_complexity_rating(score)
        case score
        when 0..10
          "Excellent"
        when 11..20
          "Good"
        when 21..30
          "Fair"
        when 31..50
          "Poor"
        else
          "Very Poor"
        end
      end
      
      def extract_methods(class_code)
        methods = []
        current_method = nil
        current_code = []
        
        class_code.lines.each do |line|
          if line.match(/^\s*def\s+\w+/)
            # Save previous method if exists
            if current_method
              methods << current_method
            end
            
            # Start new method
            method_name = line.match(/def\s+(\w+)/)[1]
            current_method = { name: method_name, code: line }
            current_code = [line]
          elsif current_method
            current_code << line
          end
        end
        
        # Add last method
        methods << current_method if current_method
        
        methods
      end
    end
    
    # Example methods with different complexity levels
    simple_method_code = <<~RUBY
      def simple_method(x, y)
        return x + y if x && y
        x || y
      end
    RUBY
    
    moderate_method_code = <<~RUBY
      def moderate_method(data, options = {})
        return nil unless data
        
        if options[:transform]
          case options[:transform]
          when :upcase
            data.map(&:upcase)
          when :downcase
            data.map(&:downcase)
          when :reverse
            data.map(&:reverse)
          else
            data
          end
        else
          data
        end
      end
    RUBY
    
    complex_method_code = <<~RUBY
      def complex_method(data, filters = {}, options = {})
        return nil unless data
        
        result = data.dup
        
        if filters[:type]
          result = result.select { |item| item[:type] == filters[:type] }
        end
        
        if filters[:status]
          result = result.select { |item| item[:status] == filters[:status] }
        end
        
        if filters[:date_range]
          start_date = filters[:date_range][:start]
          end_date = filters[:date_range][:end]
          
          result = result.select do |item|
            item_date = Date.parse(item[:created_at])
            item_date >= start_date && item_date <= end_date
          end
        end
        
        if options[:sort_by]
          case options[:sort_by]
          when :name
            result.sort_by { |item| item[:name] }
          when :date
            result.sort_by { |item| Date.parse(item[:created_at]) }
          when :priority
            result.sort_by { |item| item[:priority] }
          end
        end
        
        if options[:limit]
          result = result.first(options[:limit])
        end
        
        result
      end
    RUBY
    
    analyzer = ComplexityAnalyzer.new
    
    # Analyze methods
    simple_analysis = analyzer.analyze_method(simple_method_code)
    moderate_analysis = analyzer.analyze_method(moderate_method_code)
    complex_analysis = analyzer.analyze_method(complex_method_code)
    
    puts "Simple method analysis:"
    puts "  Cyclomatic complexity: #{simple_analysis[:cyclomatic_complexity]}"
    puts "  Lines of code: #{simple_analysis[:lines_of_code]}"
    puts "  Parameter count: #{simple_analysis[:parameter_count]}"
    puts "  Nesting depth: #{simple_analysis[:nesting_depth]}"
    puts "  Score: #{simple_analysis[:score]}"
    puts "  Rating: #{simple_analysis[:rating]}"
    
    puts "\nModerate method analysis:"
    puts "  Cyclomatic complexity: #{moderate_analysis[:cyclomatic_complexity]}"
    puts "  Lines of code: #{moderate_analysis[:lines_of_code]}"
    puts "  Parameter count: #{moderate_analysis[:parameter_count]}"
    puts "  Nesting depth: #{moderate_analysis[:nesting_depth]}"
    puts "  Score: #{moderate_analysis[:score]}"
    puts "  Rating: #{moderate_analysis[:rating]}"
    
    puts "\nComplex method analysis:"
    puts "  Cyclomatic complexity: #{complex_analysis[:cyclomatic_complexity]}"
    puts "  Lines of code: #{complex_analysis[:lines_of_code]}"
    puts "  Parameter count: #{complex_analysis[:parameter_count]}"
    puts "  Nesting depth: #{complex_analysis[:nesting_depth]}"
    puts "  Score: #{complex_analysis[:score]}"
    puts "  Rating: #{complex_analysis[:rating]}"
  end
  
  def self.code_duplication_analysis
    puts "\nCode Duplication Analysis:"
    puts "=" * 50
    
    class DuplicationDetector
      def initialize
        @code_blocks = {}
        @duplications = []
      end
      
      def analyze_file(file_path)
        code = File.read(file_path)
        blocks = extract_code_blocks(code)
        
        blocks.each_with_index do |block, index|
          block_hash = calculate_hash(block)
          
          if @code_blocks[block_hash]
            @duplications << {
              block_hash: block_hash,
              file1: @code_blocks[block_hash][:file],
              line1: @code_blocks[block_hash][:line],
              file2: file_path,
              line2: index + 1
            }
          else
            @code_blocks[block_hash] = {
              file: file_path,
              line: index + 1,
              code: block
            }
          end
        end
        
        @duplications
      end
      
      def get_duplication_percentage
        total_blocks = @code_blocks.length
        duplicate_blocks = @duplications.length
        
        return 0 if total_blocks == 0
        
        (duplicate_blocks.to_f / total_blocks * 100).round(2)
      end
      
      private
      
      def extract_code_blocks(code)
        # Simple block extraction (simplified)
        blocks = []
        current_block = []
        
        code.lines.each do |line|
          if line.strip.empty?
            blocks << current_block.join("\n") unless current_block.empty?
            current_block = []
          else
            current_block << line
          end
        end
        
        blocks << current_block.join("\n") unless current_block.empty?
        blocks
      end
      
      def calculate_hash(code)
        # Simplified hash calculation
        normalized_code = code.gsub(/\s+/, ' ').strip.downcase
        Digest::MD5.hexdigest(normalized_code)
      end
    end
    
    # Example files with duplication
    file1_content = <<~RUBY
      def process_data(data)
        return nil unless data
        
        if data.is_a?(Array)
          data.map(&:upcase)
        else
          data.to_s.upcase
        end
      end
    RUBY
    
    file2_content = <<~RUBY
      def transform_data(data)
        return nil unless data
        
        if data.is_a?(Array)
          data.map(&:upcase)
        else
          data.to_s.upcase
        end
      end
    RUBY
    
    file3_content = <<~RUBY
      def handle_data(data)
        return nil unless data
        
        if data.is_a?(Array)
          data.map(&:downcase)
        else
          data.to_s.downcase
        end
      end
    RUBY
    
    # Write temporary files for analysis
    File.write('temp_file1.rb', file1_content)
    File.write('temp_file2.rb', file2_content)
    File.write('temp_file3.rb', file3_content)
    
    detector = DuplicationDetector.new
    
    # Analyze files
    detector.analyze_file('temp_file1.rb')
    detector.analyze_file('temp_file2.rb')
    detector.analyze_file('temp_file3.rb')
    
    duplication_percentage = detector.get_duplication_percentage
    puts "Duplication percentage: #{duplication_percentage}%"
    
    # Show duplications
    if detector.instance_variable_get(:@duplications).any?
      puts "Found duplications:"
      detector.instance_variable_get(:@duplications).each do |duplication|
        puts "  #{duplication[:file1]}:#{duplication[:line1]} <-> #{duplication[:file2]}:#{duplication[:line2]}"
      end
    else
      puts "No duplications found"
    end
    
    # Cleanup
    File.delete('temp_file1.rb')
    File.delete('temp_file2.rb')
    File.delete('temp_file3.rb')
  end
  
  def self.method_length_analysis
    puts "\nMethod Length Analysis:"
    puts "=" * 50
    
    class MethodLengthAnalyzer
      def initialize
        @method_lengths = {}
      end
      
      def analyze_method(method_code)
        lines = method_code.lines.count
        @method_lengths[lines] = (@method_lengths[lines] || 0) + 1
      end
      
      def get_statistics
        return {} if @method_lengths.empty?
        
        total_methods = @method_lengths.values.sum
        avg_length = @method_lengths.keys.sum.to_f / total_methods
        max_length = @method_lengths.keys.max
        min_length = @method_lengths.keys.min
        
        {
          total_methods: total_methods,
          average_length: avg_length.round(2),
          max_length: max_length,
          min_length: min_length,
          distribution: @method_lengths
        }
      end
      
      def get_length_rating(length)
        case length
        when 1..10
          "Excellent"
        when 11..20
          "Good"
        when 21..30
          "Fair"
        when 31..50
          "Poor"
        else
          "Very Poor"
        end
      end
    end
    
    # Example methods with different lengths
    short_method_code = <<~RUBY
      def short_method
        "short"
      end
    RUBY
    
    medium_method_code = <<~RUBY
      def medium_method(data)
        return nil unless data
        
        result = data.dup
        
        if data.is_a?(Array)
          result = data.map(&:upcase)
        end
        
        result
      end
    RUBY
    
    long_method_code = <<~RUBY
      def long_method(data, options = {})
        return nil unless data
        
        result = data.dup
        
        if data.is_a?(Array)
          result = data.map(&:upcase)
        else
          result = data.to_s.upcase
        end
        
        if options[:filter]
          result = result.select { |item| item.include?(options[:filter]) }
        end
        
        if options[:sort]
          result = result.sort
        end
        
        if options[:limit]
          result = result.first(options[:limit])
        end
        
        result
      end
    RUBY
    
    analyzer = MethodLengthAnalyzer.new
    
    # Analyze methods
    analyzer.analyze_method(short_method_code)
    analyzer.analyze_method(medium_method_code)
    analyzer.analyze_method(long_method_code)
    
    stats = analyzer.get_statistics
    puts "Method length statistics:"
    puts "  Total methods: #{stats[:total_methods]}"
    puts "  Average length: #{stats[:average_length]} lines"
    puts "  Max length: #{stats[:max_length]} lines"
    puts "  Min length: #{stats[:min_length]} lines"
    
    puts "\nLength distribution:"
    stats[:distribution].sort_by { |length, count| length }.each do |length, count|
      rating = analyzer.get_length_rating(length)
      puts "  #{length} lines: #{count} methods (#{rating})"
    end
  end
  
  # Run complexity analysis examples
  method_complexity_analysis
  code_duplication_analysis
  method_length_analysis
end
```

## 🔧 Refactoring Techniques

### 1. Code Smells and Refactoring

Identifying and fixing code smells:

```ruby
class RefactoringTechniques
  def self.identify_code_smells
    puts "Code Smells Identification:"
    puts "=" * 50
    
    code_smells = [
      {
        smell: "Long Method",
        description: "Methods that are too long and do too many things",
        indicators: ["More than 20 lines", "Multiple responsibilities", "Deep nesting"],
        example: "def process_user_data(user, options = {})\n  # 50 lines of code\nend",
        solution: "Extract smaller methods with single responsibilities"
      },
      {
        smell: "Large Class",
        description: "Classes that have too many responsibilities",
        indicators: ["Too many methods", "Too many instance variables", "Low cohesion"],
        example: "class User\n  def create_user\n  def update_user\n  def delete_user\n  def send_email\n  def generate_report\nend",
        solution: "Split into smaller, focused classes"
      },
      {
        smell: "Duplicate Code",
        description: "Similar or identical code in multiple places",
        indicators: ["Copy-paste programming", "Similar method implementations"],
        example: "# Method 1\ndef process_data(data)\n  data.map(&:upcase)\nend\n\n# Method 2\ndef handle_data(data)\n  data.map(&:upcase)\nend",
        solution: "Extract common method or use inheritance"
      },
      {
        smell: "Long Parameter List",
        description: "Methods with too many parameters",
        indicators: ["More than 5 parameters", "Parameter objects with many attributes"],
        example: "def create_user(name, email, age, address, phone, website, company, role, status)\n  # Method body\nend",
        solution: "Use parameter objects or options hash"
      },
      {
        smell: "Feature Envy",
        description: "Class that does more than it should",
        indicators: ["Unrelated methods", "Too many dependencies"],
        example: "class User\n  def save\n  def validate\n  def send_welcome_email\n  def generate_report\n  def backup_data\nend",
        solution: "Split into separate classes"
      },
      {
        smell: "Data Clumps",
        description: "Groups of data that appear together",
        indicators: ["Same parameters in multiple methods", "Related instance variables"],
        example: "def create_user(name, email, address, phone)\ndef update_user(name, email, address, phone)\ndef delete_user(name, email, address, phone)",
        solution: "Extract into User class"
      },
      {
        smell: "Primitive Obsession",
        description: "Overuse of primitive types instead of objects",
        indicators: ["Many methods on primitive types", "Repeated type checking"],
        example: "def process_string(str)\n  if str.is_a?(String)\n    str.upcase\n  else\n    str.to_s.upcase\n  end\nend",
        solution: "Use polymorphism or value objects"
      },
      {
        smell: "Inappropriate Intimacy",
        description: "Classes that are too tightly coupled",
        indicators: ["Accessing private data", "Too many dependencies"],
        example: "class Order\n  def initialize(user)\n    @user = user\n    user.private_data\n  end\nend",
        solution: "Use proper encapsulation"
      },
      {
        smell: "Refused Bequest",
        description: "Messages that pass through a chain of objects",
        indicators: ["Delegation methods", "No real work"],
        example: "class A\n  def process(data)\n    @b.process(data)\n  end\nend",
        solution: "Use inheritance or composition"
      },
      {
        smell: "Comments",
        description: "Excessive or unnecessary comments",
        indicators: ["Comments explaining obvious code", "Outdated comments"],
        example: "# Add two numbers\nresult = x + y  # Calculate sum",
        solution: "Write self-documenting code"
      }
    ]
    
    code_smells.each do |smell|
      puts "#{smell[:smell]}:"
      puts "  Description: #{smell[:description]}"
      puts "  Indicators: #{smell[:indicators].join(', ')}"
      puts "  Example: #{smell[:example]}"
      puts "  Solution: #{smell[:solution]}"
      puts
    end
  end
  
  def self.refactoring_examples
    puts "\nRefactoring Examples:"
    puts "=" * 50
    
    # Example: Extract Method
    class ExtractMethodExample
      def before_refactoring(order)
        # This method does too many things
        total = 0
        
        order.items.each do |item|
          total += item.quantity * item.price
        end
        
        if order.customer.vip?
          discount = total * 0.1
          total -= discount
        end
        
        if order.coupon
          coupon_discount = total * (order.coupon.discount / 100.0)
          total -= coupon_discount
        end
        
        shipping = calculate_shipping(order)
        total += shipping
        
        tax = total * 0.08
        total += tax
        
        total
      end
      
      def after_refactoring(order)
        total = calculate_subtotal(order)
        total = apply_discounts(total, order)
        total += calculate_shipping(order)
        total += calculate_tax(total)
        
        total
      end
      
      private
      
      def calculate_subtotal(order)
        order.items.sum { |item| item.quantity * item.price }
      end
      
      def apply_discounts(total, order)
        total = apply_vip_discount(total, order) if order.customer.vip?
        total = apply_coupon_discount(total, order) if order.coupon
        total
      end
      
      def apply_vip_discount(total, order)
        discount = total * 0.1
        total - discount
      end
      
      def apply_coupon_discount(total, order)
        coupon_discount = total * (order.coupon.discount / 100.0)
        total - coupon_discount
      end
      
      def calculate_shipping(order)
        # Calculate shipping based on order total and location
        base_shipping = 5.0
        distance_factor = calculate_distance_factor(order.address)
        base_shipping * distance_factor
      end
      
      def calculate_tax(total)
        total * 0.08
      end
      
      def calculate_distance_factor(address)
        # Simplified distance calculation
        case address.country
        when "US"
          1.0
        when "CA"
          1.2
        when "UK"
          1.5
        else
          2.0
        end
      end
    end
    
    # Example: Extract Class
    class ExtractClassExample
      def before_refactoring
        @name = "John"
        @email = "john@example.com"
        @address = "123 Main St"
        @city = "New York"
        @state = "NY"
        @zip = "10001"
        @country = "US"
        @phone = "555-123-4567"
        @website = "johnswebsite.com"
        
        validate_user_data
        save_user_to_database
        send_welcome_email
      end
      
      def after_refactoring
        user_info = UserInfo.new(@name, @email, @address, @city, @state, @zip, @country, @phone, @website)
        
        validate_user_data(user_info)
        save_user_to_database(user_info)
        send_welcome_email(user_info)
      end
      
      private
      
      def validate_user_data(user_info)
        # Validation logic
      end
      
      def save_user_to_database(user_info)
        # Database save logic
      end
      
      def send_welcome_email(user_info)
        # Email sending logic
      end
    end
    
    # UserInfo class
    class UserInfo
      attr_reader :name, :email, :address, :city, :state, :zip, :country, :phone, :website
      
      def initialize(name, email, address, city, state, zip, country, phone, website)
        @name = name
        @email = email
        @address = address
        @city = city
        @state = state
        @zip = zip
        @country = country
        @phone = phone
        @website = website
      end
    end
    
    # Example: Replace Conditional with Polymorphism
    class ReplaceConditionalExample
      def before_refactoring(order)
        case order.type
        when "physical"
          process_physical_order(order)
        when "digital"
          process_digital_order(order)
        when "service"
          process_service_order(order)
        else
          raise "Unknown order type: #{order.type}"
        end
      end
      
      def after_refactoring(order)
        order_processor = OrderProcessorFactory.create(order.type)
        order_processor.process(order)
      end
      
      private
      
      def process_physical_order(order)
        # Physical order processing
      end
      
      def process_digital_order(order)
        # Digital order processing
      end
      
      def process_service_order(order)
        # Service order processing
      end
    end
    
    # OrderProcessorFactory
    class OrderProcessorFactory
      def self.create(order_type)
        case order_type
        when "physical"
          PhysicalOrderProcessor.new
        when "digital"
          DigitalOrderProcessor.new
        when "service"
          ServiceOrderProcessor.new
        else
          raise "Unknown order type: #{order_type}"
        end
      end
    end
    
    class PhysicalOrderProcessor
      def process(order)
        # Physical order processing
      end
    end
    
    class DigitalOrderProcessor
      def process(order)
        # Digital order processing
      end
    end
    
    class ServiceOrderProcessor
      def process(order)
        # Service order processing
      end
    end
    
    puts "Refactoring examples:"
    puts "1. Extract Method - Break down long methods into smaller ones"
    puts "2. Extract Class - Group related data and behavior"
    puts "3. Replace Conditional with Polymorphism - Use polymorphism instead of conditionals"
    puts "4. Introduce Parameter Object - Replace long parameter lists"
    puts "5. Extract Method - Move method to appropriate class"
  end
  
  def self.automated_refactoring_tools
    puts "\nAutomated Refactoring Tools:"
    puts "=" * 50
    
    tools = [
      {
        name: "RuboCop",
        description: "Ruby static code analyzer and formatter",
        features: [
          "Code style checking",
          "Code formatting",
          "Complexity analysis",
          "Security analysis",
          "Performance analysis"
        ],
        usage: "rubocop --format --auto file.rb"
      },
      {
        name: "Reek",
        description: "Code smell detector",
        features: [
          "Duplicate code detection",
          "Long method detection",
          "Large class detection",
          "Feature envy detection"
        ],
        usage: "reek file.rb"
      },
      {
        name: "Ruby Lint",
        description: "Ruby code quality checker",
        features: [
          "Syntax checking",
          "Style checking",
          "Complexity analysis"
        ],
        usage: "ruby-lint file.rb"
      },
      {
        name: "IDE Refactoring",
        description: "IDE refactoring tools",
        features: [
          "Extract method",
          "Rename variable",
          "Introduce variable",
          "Inline method"
        ],
        usage: "IDE-specific shortcuts"
      }
    ]
    
    tools.each do |tool|
      puts "#{tool[:name]}:"
      puts "  Description: #{tool[:description]}"
      puts "  Features: #{tool[:features].join(', ')}"
      puts "  Usage: #{tool[:usage]}"
      puts
    end
    
    puts "Refactoring workflow:"
    workflow = [
      "1. Identify code smells",
      "2. Write tests for existing behavior",
      "3. Apply refactoring techniques",
      "4. Run tests to ensure no regression",
      "5. Review and improve code",
      "6. Commit changes"
    ]
    
    workflow.each { |step| puts "  #{step}" }
  end
  
  # Run refactoring examples
  identify_code_smells
  refactoring_examples
  automated_refactoring_tools
end
```

## 🎯 Code Review Guidelines

### 1. Review Process

Effective code review practices:

```ruby
class CodeReviewGuidelines
  def self.review_checklist
    puts "Code Review Checklist:"
    puts "=" * 50
    
    checklist = [
      {
        category: "Functionality",
        items: [
          "Code implements requirements correctly",
          "Edge cases are handled appropriately",
          "Error handling is comprehensive",
          "Performance considerations are addressed"
        ]
      },
      {
        category: "Code Quality",
        items: [
          "Code follows naming conventions",
          "Code is properly formatted",
          "Comments are appropriate and helpful",
          "No dead code or commented-out code"
        ]
      },
      {
        category: "Design",
        items: [
          "Code follows SOLID principles",
          "Classes have single responsibility",
          "Dependencies are minimal",
          "Code is extensible and maintainable"
        ]
      },
      {
        category: "Testing",
        items: [
          "Tests cover critical functionality",
          "Tests are well-written and maintainable",
          "Edge cases are tested",
          "Test coverage is adequate"
        ]
      },
      {
        category: "Security",
        items: [
          "No hardcoded secrets or credentials",
          "Input validation is implemented",
          "SQL injection prevention",
          "XSS prevention where applicable"
        ]
      },
      {
        category: "Performance",
        items: [
          "No obvious performance issues",
          "Database queries are optimized",
          "Memory usage is reasonable",
          "No unnecessary computations"
        ]
      }
    ]
    
    checklist.each do |category|
      puts "#{category[:category]}:"
      category[:items].each { |item| puts "  ✓ #{item}" }
      puts
    end
  end
  
  def self.review_process
    puts "\nCode Review Process:"
    puts "=" * 50
    
    process = [
      {
        step: "1. Self-Review",
        description: "Author reviews their own code before submission",
        checklist: [
          "Code follows style guidelines",
          "Tests pass",
          "Code is well-documented",
          "No obvious bugs"
        ]
      },
      {
        step: "2. Peer Review",
        description: "Team member reviews the code",
        checklist: [
          "Functional correctness",
          "Code quality",
          "Design patterns",
          "Security considerations"
        ]
      },
      {
        step: "3. Discussion",
        description: "Reviewer and author discuss feedback",
        checklist: [
          "Clarify unclear points",
          "Discuss alternative approaches",
          "Agree on changes needed",
          "Document decisions"
        ]
      },
      {
        step: "4. Implementation",
        description: "Author implements changes",
        checklist: [
          "Address all feedback",
          "Update tests if needed",
          "Re-run tests",
          "Update documentation"
        ]
      },
      {
        step: "5. Final Review",
        description: "Reviewer checks implemented changes",
        checklist: [
          "All feedback addressed",
          "No new issues introduced",
          "Tests still pass",
          "Code is ready to merge"
        ]
      },
      {
        step: "6. Merge",
        description: "Code is merged to main branch",
        checklist: [
          "Pull request is approved",
          "All checks pass",
          "Documentation is updated",
          "Code is integrated"
        ]
      }
    ]
    
    process.each do |step|
      puts "#{step[:step]}: #{step[:description]}"
      step[:checklist].each { |item| puts "  • #{item}" }
      puts
    end
  end
  
  def self.review_feedback_guidelines
    puts "\nReview Feedback Guidelines:"
    puts "=" * 50
    
    guidelines = [
      {
        principle: "Be Constructive",
        description: "Focus on improvement, not criticism",
        example: "Instead of 'This code is terrible', say 'This could be improved by...'"
      },
      {
        principle: "Be Specific",
        description: "Provide specific, actionable feedback",
        example: "Instead of 'This method is too long', say 'This method could be split into smaller methods'"
      },
      {
        principle: "Explain Why",
        description: "Explain the reasoning behind suggestions",
        example: "Instead of 'Use a different approach', say 'Use a different approach because it's more readable'"
      },
      {
        principle: "Provide Examples",
        description: "Show code examples when helpful",
        example: "Instead of 'Use a different pattern', show the pattern in code"
      },
      {
        principle: "Be Respectful",
        description: "Be respectful of the author's work",
        example: "Acknowledge good parts of the code before suggesting improvements"
      },
      {
        principle: "Focus on Code, Not Person",
        description: "Comment on code, not the person who wrote it",
        example: "Instead of 'You should have known better', say 'This code could be improved by...'"
      }
    ]
    
    guidelines.each { |guideline| puts "#{guideline[:principle]}:"; puts "  #{guideline[:description]}"; puts "  Example: #{guideline[:example]}"; puts }
  end
  
  def self.review_best_practices
    puts "\nCode Review Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Review Small Changes",
        description: "Review small, focused changes frequently",
        reason: "Easier to review and provides faster feedback"
      },
      {
        practice: "Use Checklists",
        description: "Use standard checklists for consistency",
        reason: "Ensures all important aspects are covered"
      },
      {
        practice: "Automate Where Possible",
        description: "Use automated tools for style and basic checks",
        reason: "Frees up time for more important aspects"
      },
      {
        practice: "Focus on Learning",
        description: "Use reviews as learning opportunities",
        reason: "Everyone learns from the code review process"
      },
      {
        practice: "Document Decisions",
        description: "Document important design decisions",
        reason: "Provides context for future changes"
      },
      {
        practice: "Be Consistent",
        description: "Apply consistent standards across reviews",
        reason: "Ensures fair and predictable reviews"
      },
      {
        practice: "Follow Up",
        description: "Follow up on implemented changes",
        reason: "Ensures feedback is acted upon"
      },
      {
        practice: "Balance Thoroughness and Speed",
        description: "Focus on most important issues first",
        reason: "Avoid getting stuck on minor issues"
      }
    ]
    
    practices.each { |practice| puts "#{practice[:practice]}:"; puts "  #{practice[:description]}"; puts "  Reason: #{practice[:reason]}"; puts }
  end
  
  # Run review guidelines examples
  review_checklist
  review_process
  review_feedback_guidelines
  review_best_practices
end
```

## 🎓 Code Quality Metrics

### 1. Quality Metrics Dashboard

Comprehensive code quality measurement:

```ruby
class CodeQualityMetrics
  def self.quality_dashboard
    puts "Code Quality Metrics Dashboard:"
    puts "=" * 50
    
    class QualityMetricsCollector
      def initialize
        @metrics = {}
      end
      
      def collect_metrics(codebase_path)
        @metrics = {
          codebase_path: codebase_path,
          timestamp: Time.now,
          files_analyzed: 0,
          total_lines: 0,
          total_complexity: 0,
          test_coverage: 0,
          duplication_rate: 0,
          style_violations: 0,
          security_issues: 0,
          performance_issues: 0,
          maintainability_index: 0
        }
        
        # Analyze all Ruby files
        Dir.glob("#{codebase_path}/**/*.rb").each do |file|
          analyze_file(file)
        end
        
        # Calculate overall metrics
        calculate_overall_metrics
        
        @metrics
      end
      
      def get_quality_grade
        score = calculate_quality_score
        case score
        when 90..100
          "A"
        when 80..89
          "B"
        when 70..79
          "C"
        when 60..69
          "D"
        else
          "F"
        end
      end
      
      def get_recommendations
        recommendations = []
        
        if @metrics[:complexity_average] > 20
          recommendations << "Reduce method complexity (current: #{@metrics[:complexity_average]})"
        end
        
        if @metrics[:duplication_rate] > 10
          recommendations << "Reduce code duplication (current: #{@metrics[:duplication_rate]}%)"
        end
        
        if @metrics[:test_coverage] < 80
          recommendations << "Increase test coverage (current: #{@metrics[:test_coverage]}%)"
        end
        
        if @metrics[:style_violations] > 5
          recommendations << "Fix style violations (current: #{@metrics[:style_violations]})"
        end
        
        if @metrics[:maintainability_index] < 70
          recommendations << "Improve maintainability (current: #{@metrics[:maintainability_index]})"
        end
        
        recommendations
      end
      
      private
      
      def analyze_file(file_path)
        code = File.read(file_path)
        lines = code.lines.count
        
        @metrics[:files_analyzed] += 1
        @metrics[:total_lines] += lines
        
        # Analyze complexity (simplified)
        complexity = analyze_complexity(code)
        @metrics[:total_complexity] += complexity
        
        # Analyze style violations (simplified)
        style_violations = analyze_style_violations(code)
        @metrics[:style_violations] += style_violations
        
        # Analyze security issues (simplified)
        security_issues = analyze_security_issues(code)
        @metrics[:security_issues] += security_issues
        
        # Analyze performance issues (simplified)
        performance_issues = analyze_performance_issues(code)
        @metrics[:performance_issues] += performance_issues
      end
      
      def analyze_complexity(code)
        # Simplified complexity analysis
        complexity = code.scan(/\bif\b|\bunless\b|\bcase\b|\bwhile\b|\buntil\b|\bfor\b|\bwhen\b|\belif\b|\belsif\b|\breturn\b|\bnext\b|\bbreak\b|\bretry\b|\braise\b).length
        complexity += code.scan(/\&\&|\|\|/).length
        complexity
      end
      
      def analyze_style_violations(code)
        # Simplified style analysis
        violations = 0
        
        violations += code.scan(/\s+$/).length  # Trailing whitespace
        violations += code.scan(/[\t]/).length  # Tab characters
        violations += code.scan(/.{80,}/).length  # Long lines
        
        violations
      end
      
      def analyze_security_issues(code)
        # Simplified security analysis
        issues = 0
        
        issues += code.scan(/eval\s*\(/).length  # eval usage
        issues += code.scan(/system\s*\(/).length  # system usage
        issues += code.scan(/sql.*\+.*\s*['"]/i).length  # SQL injection risk
        issues += code.scan(/exec\s*\(/).length  # exec usage
        
        issues
      end
      
      def analyze_performance_issues(code)
        # Simplified performance analysis
        issues = 0
        
        issues += code.scan(/\.each\s*\{\s*\|.*\|/).length  # Inefficient iteration
        issues += code.scan(/\.map\s*\{.*\}/).length  # Multiple map operations
        issues += code.scan(/\.select\s*\{.*\}/).length  # Multiple select operations
        
        issues
      end
      
      def calculate_overall_metrics
        return if @metrics[:files_analyzed] == 0
        
        @metrics[:average_complexity] = @metrics[:total_complexity].to_f / @metrics[:files_analyzed]
        @metrics[:average_lines_per_file] = @metrics[:total_lines].to_f / @metrics[:files_analyzed]
        
        # Mock test coverage (would be calculated from actual test results)
        @metrics[:test_coverage] = 75  # Placeholder
        
        # Mock duplication rate (would be calculated from actual duplication analysis)
        @metrics[:duplication_rate] = 8  # Placeholder
        
        # Calculate maintainability index
        @metrics[:maintainability_index] = calculate_maintainability_index
      end
      
      def calculate_maintainability_index
        # Simplified maintainability index calculation
        complexity_factor = [100 - (@metrics[:average_complexity] * 2), 0].max
        style_factor = [100 - (@metrics[:style_violations] * 5), 0].max
        coverage_factor = @metrics[:test_coverage]
        duplication_factor = [100 - (@metrics[:duplication_rate] * 2), 0].max
        
        (complexity_factor + style_factor + coverage_factor + duplication_factor) / 4
      end
      
      def calculate_quality_score
        # Quality score based on multiple factors
        complexity_score = [@metrics[:average_complexity], 50].min
        style_score = [100 - (@metrics[:style_violations] * 2), 0].max
        test_score = @metrics[:test_coverage]
        security_score = [100 - (@metrics[:security_issues] * 10), 0].max
        performance_score = [100 - (@metrics[:performance_issues] * 5), 0].max
        
        (complexity_score + style_score + test_score + security_score + performance_score) / 5
      end
    end
    
    # Simulate metrics collection
    puts "Simulating code quality metrics..."
    
    # Create temporary directory with sample files
    Dir.mkdir('temp_code_quality') unless Dir.exist?('temp_code_quality')
    
    sample_files = [
      {
        name: 'good_code.rb',
        content: <<~RUBY
          class User
            def initialize(name, email)
              @name = name
              @email = email
            end
            
            def display_name
              @name.upcase
            end
            
            def display_email
              @email.downcase
            end
          end
        RUBY
      },
      {
        name: 'complex_code.rb',
        content: <<~RUBY
          class ComplexClass
            def initialize(data, options = {})
              @data = data
              @options = options
              @processed_data = []
              
              if @data.is_a?(Array)
                if @options[:transform]
                  case @options[:transform]
                  when :upcase
                    @processed_data = @data.map(&:upcase)
                  when :downcase
                    @processed_data = @data.map(&:downcase)
                  else
                    @processed_data = @data
                  end
                else
                  @processed_data = @data
                end
              else
                @processed_data = [@data.to_s]
              end
              
              if @options[:filter]
                @processed_data = @processed_data.select { |item| item.include?(@options[:filter]) }
              end
              
              if @options[:sort]
                @processed_data = @processed_data.sort_by { |item| item[:name] }
              end
              
              @processed_data
            end
          end
        RUBY
      },
      {
        name: 'style_issues.rb',
        content: <<~RUBY
          class BadStyle
            def method_with_issues(  param1 , param2 )
              result = param1 + param2
              if result > 100
                result = result / 2
              end
              return result
            end
          end
        RUBY
      }
    ]
    
    sample_files.each do |file|
      File.write("temp_code_quality/#{file[:name]}", file[:content])
    end
    
    # Collect metrics
    collector = QualityMetricsCollector.new
    metrics = collector.collect_metrics('temp_code_quality')
    
    # Display results
    puts "\nCode Quality Metrics:"
    puts "Files analyzed: #{metrics[:files_analyzed]}"
    puts "Total lines of code: #{metrics[:total_lines]}"
    puts "Average complexity: #{metrics[:average_complexity].round(2)}"
    puts "Test coverage: #{metrics[:test_coverage]}%"
    puts "Duplication rate: #{metrics[:duplication_rate]}%"
    puts "Style violations: #{metrics[:style_violations]}"
    puts "Security issues: #{metrics[:security_issues]}"
    puts "Performance issues: #{metrics[:performance_issues]}"
    puts "Maintainability index: #{metrics[:maintainability_index].round(2)}"
    
    quality_grade = collector.get_quality_grade
    puts "Quality grade: #{quality_grade}"
    
    recommendations = collector.get_recommendations
    if recommendations.any?
      puts "\nRecommendations:"
      recommendations.each { |rec| puts "• #{rec}" }
    end
    
    # Cleanup
    sample_files.each { |file| File.delete("temp_code_quality/#{file[:name]}") }
    Dir.rmdir('temp_code_quality')
  end
  
  def self.quality_trends
    puts "\nQuality Trends Analysis:"
    puts "=" * 50
    
    class QualityTrendAnalyzer
      def initialize
        @historical_metrics = []
      end
      
      def record_metrics(metrics)
        @historical_metrics << metrics
      end
      
      def analyze_trends
        return if @historical_metrics.length < 2
        
        latest = @historical_metrics.last
        previous = @historical_metrics[@historical_metrics.length - 2]
        
        trends = {}
        
        # Calculate trends
        trends[:complexity_trend] = calculate_trend(previous[:average_complexity], latest[:average_complexity])
        trends[:coverage_trend] = calculate_trend(previous[:test_coverage], latest[:test_coverage])
        trends[:maintainability_trend] = calculate_trend(previous[:maintainability_index], latest[:maintainability_index])
        
        trends
      end
      
      def get_trend_indicators
        return if @historical_metrics.length < 2
        
        trends = analyze_trends
        indicators = {}
        
        trends.each do |metric, trend|
          if trend > 5
            indicators[metric] = "Improving"
          elsif trend < -5
            indicators[metric] = "Declining"
          else
            indicators[metric] = "Stable"
          end
        end
        
        indicators
      end
      
      private
      
      def calculate_trend(previous, current)
        return 0 if previous.zero?
        
        ((current - previous) / previous * 100).round(2)
      end
    end
    
    # Simulate historical data
    analyzer = QualityTrendAnalyzer.new
    
    # Add historical metrics
    (1..5).each do |i|
      base_metrics = {
        average_complexity: 15 + rand(10),
        test_coverage: 60 + rand(30),
        maintainability_index: 70 + rand(20)
      }
      
      # Add some variation
      metrics = base_metrics.merge(
        average_complexity: base_metrics[:average_complexity] + (i - 3),
        test_coverage: base_metrics[:test_coverage] + (i - 2) * 2,
        maintainability_index: base_metrics[:maintainability_index] + (i - 3) * 3
      )
      
      analyzer.record_metrics(metrics)
    end
    
    # Analyze trends
    trends = analyzer.analyze_trends
    puts "Quality trends:"
    trends.each { |metric, trend| puts "  #{metric}: #{trend > 0 ? '+' : ''}#{trend}%" }
    
    indicators = analyzer.get_trend_indicators
    puts "\nTrend indicators:"
    indicators.each { |metric, indicator| puts "  #{metric}: #{indicator}" }
  end
  
  # Run quality metrics examples
  quality_dashboard
  quality_trends
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Naming Conventions**: Apply proper naming to existing code
2. **Code Formatting**: Format code according to Ruby standards
3. **Basic Refactoring**: Extract methods and classes

### Intermediate Exercises

1. **Code Smells**: Identify and fix common code smells
2. **Complexity Analysis**: Analyze and reduce method complexity
3. **Code Review**: Conduct effective code reviews

### Advanced Exercises

1. **Quality Metrics**: Implement comprehensive quality metrics
2. **Automated Refactoring**: Set up automated refactoring tools
3. **Quality Dashboard**: Create quality monitoring dashboard

---

## 🎯 Summary

Code quality in Ruby provides:

- **Coding Standards** - Naming conventions and formatting
- **Code Metrics** - Complexity analysis and measurement
- **Refactoring** - Identify and fix code smells
- **Code Review** - Effective review processes
- **Quality Monitoring** - Continuous quality tracking

Master these techniques to write clean, maintainable Ruby code!
