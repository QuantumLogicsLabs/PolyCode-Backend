# Testing Examples in Ruby
# Demonstrating various testing frameworks and patterns

require 'minitest/autorun'
require 'test/unit'
require 'rspec'

class TestingExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "🧪 Testing Examples in Ruby"
    puts "=========================="
    puts "Explore testing frameworks and patterns!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Testing Examples Menu:"
      puts "1. Minitest Examples"
      puts "2. RSpec Examples"
      puts "3. Test::Unit Examples"
      puts "4. Mocking and Stubbing"
      puts "5. Integration Testing"
      puts "6. Performance Testing"
      puts "7. Test Data Management"
      puts "8. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-8): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        minitest_examples
      when 2
        rspec_examples
      when 3
        test_unit_examples
      when 4
        mocking_stubbing
      when 5
        integration_testing
      when 6
        performance_testing
      when 7
        test_data_management
      when 8
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def minitest_examples
    puts "\n🧪 Example 1: Minitest Examples"
    puts "=" * 50
    puts "Testing with Minitest framework."
    puts ""
    
    # Sample class to test
    class Calculator
      def add(a, b)
        a + b
      end
      
      def subtract(a, b)
        a - b
      end
      
      def multiply(a, b)
        a * b
      end
      
      def divide(a, b)
        a / b.to_f
      end
      
      def power(base, exponent)
        base ** exponent
      end
      
      def factorial(n)
        return 1 if n <= 1
        n * factorial(n - 1)
      end
    end
    
    # Minitest test class
    class CalculatorTest < Minitest::Test
      def setup
        @calculator = Calculator.new
      end
      
      def test_addition
        assert_equal 5, @calculator.add(2, 3)
        assert_equal 0, @calculator.add(-2, 2)
        assert_equal -1, @calculator.add(-2, 1)
      end
      
      def test_subtraction
        assert_equal 1, @calculator.subtract(5, 4)
        assert_equal -1, @calculator.subtract(2, 3)
      end
      
      def test_multiplication
        assert_equal 6, @calculator.multiply(2, 3)
        assert_equal 0, @calculator.multiply(5, 0)
      end
      
      def test_division
        assert_equal 2.5, @calculator.divide(5, 2)
        assert_equal 1.0, @calculator.divide(4, 4)
      end
      
      def test_division_by_zero
        assert_raises ZeroDivisionError do
          @calculator.divide(5, 0)
        end
      end
      
      def test_power
        assert_equal 8, @calculator.power(2, 3)
        assert_equal 1, @calculator.power(5, 0)
        assert_equal 0.25, @calculator.power(2, -2)
      end
      
      def test_factorial
        assert_equal 1, @calculator.factorial(0)
        assert_equal 1, @calculator.factorial(1)
        assert_equal 6, @calculator.factorial(3)
        assert_equal 120, @calculator.factorial(5)
      end
    end
    
    # String manipulation tests
    class StringManipulator
      def reverse_string(str)
        str.reverse
      end
      
      def capitalize_words(str)
        str.split.map(&:capitalize).join(' ')
      end
      
      def palindrome?(str)
        cleaned = str.downcase.gsub(/[^a-z0-9]/, '')
        cleaned == cleaned.reverse
      end
    end
    
    class StringManipulatorTest < Minitest::Test
      def setup
        @manipulator = StringManipulator.new
      end
      
      def test_reverse_string
        assert_equal "olleh", @manipulator.reverse_string("hello")
        assert_equal "", @manipulator.reverse_string("")
        assert_equal "A", @manipulator.reverse_string("A")
      end
      
      def test_capitalize_words
        assert_equal "Hello World", @manipulator.capitalize_words("hello world")
        assert_equal "Ruby Programming", @manipulator.capitalize_words("ruby programming")
        assert_equal "", @manipulator.capitalize_words("")
      end
      
      def test_palindrome
        assert @manipulator.palindrome?("racecar")
        assert @manipulator.palindrome?("A man, a plan, a canal: Panama")
        refute @manipulator.palindrome?("hello")
      end
    end
    
    puts "Minitest test classes created:"
    puts "- CalculatorTest: Basic arithmetic operations"
    puts "- StringManipulatorTest: String manipulation methods"
    
    # Run tests
    puts "\nRunning tests..."
    
    begin
      Minitest.run([])
      puts "All tests passed!"
    rescue => e
      puts "Test execution error: #{e.message}"
    end
    
    @examples << {
      title: "Minitest Examples",
      description: "Testing with Ruby's built-in Minitest framework",
      code: <<~RUBY
        class CalculatorTest < Minitest::Test
          def setup
            @calculator = Calculator.new
          end
          
          def test_addition
            assert_equal 5, @calculator.add(2, 3)
          end
        end
      RUBY
    }
    
    puts "\n✅ Minitest Examples completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def rspec_examples
    puts "\n🔍 Example 2: RSpec Examples"
    puts "=" * 50
    puts "Testing with RSpec framework."
    puts ""
    
    # Sample class for RSpec testing
    class User
      attr_reader :name, :email, :age
  
      def initialize(name, email, age)
        @name = name
        @email = email
        @age = age
      end
  
      def adult?
        @age >= 18
      end
  
      def valid_email?
        @email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
      end
  
      def display_name
        "#{@name} (#{@email})"
      end
    end
    
    # RSpec examples (would normally be in separate files)
    rspec_user_test = <<~RSPEC
      require 'rspec'
      
      describe User do
        let(:user) { User.new("Alice", "alice@test.com", 30) }
        let(:minor) { User.new("Bob", "bob@test.com", 16) }
        
        describe "#initialize" do
          it "sets the name" do
            expect(user.name).to eq("Alice")
          end
          
          it "sets the email" do
            expect(user.email).to eq("alice@test.com")
          end
          
          it "sets the age" do
            expect(user.age).to eq(30)
          end
        end
        
        describe "#adult?" do
          context "when age is 18 or older" do
            it "returns true" do
              expect(user.adult?).to be true
            end
          end
          
          context "when age is less than 18" do
            it "returns false" do
              expect(minor.adult?).to be false
            end
          end
        end
        
        describe "#valid_email?" do
          context "with valid email" do
            it "returns true" do
              expect(user.valid_email?).to be true
            end
          end
          
          context "with invalid email" do
            let(:invalid_user) { User.new("Charlie", "invalid-email", 25) }
            
            it "returns false" do
              expect(invalid_user.valid_email?).to be false
            end
          end
        end
        
        describe "#display_name" do
          it "returns formatted name with email" do
            expect(user.display_name).to eq("Alice (alice@test.com)")
          end
        end
      end
    RSPEC
    
    # Array utility class for testing
    class ArrayUtils
      def self.sum(arr)
        arr.reduce(0, :+)
      end
      
      def self.average(arr)
        return 0 if arr.empty?
        sum(arr).to_f / arr.length
      end
      
      def self.max(arr)
        return nil if arr.empty?
        arr.max
      end
      
      def self.min(arr)
        return nil if arr.empty?
        arr.min
      end
      
      def self.uniq(arr)
        arr.uniq
      end
    end
    
    rspec_array_test = <<~RSPEC
      describe ArrayUtils do
        describe ".sum" do
          it "returns the sum of array elements" do
            expect(ArrayUtils.sum([1, 2, 3, 4])).to eq(10)
          end
          
          it "returns 0 for empty array" do
            expect(ArrayUtils.sum([])).to eq(0)
          end
          
          it "handles negative numbers" do
            expect(ArrayUtils.sum([-1, -2, 3])).to eq(0)
          end
        end
        
        describe ".average" do
          it "returns the average of array elements" do
            expect(ArrayUtils.average([1, 2, 3, 4])).to eq(2.5)
          end
          
          it "returns 0 for empty array" do
            expect(ArrayUtils.average([])).to eq(0)
          end
          
          it "returns exact value for single element" do
            expect(ArrayUtils.average([5])).to eq(5.0)
          end
        end
        
        describe ".max" do
          it "returns the maximum value" do
            expect(ArrayUtils.max([1, 3, 2, 5, 4])).to eq(5)
          end
          
          it "returns nil for empty array" do
            expect(ArrayUtils.max([])).to be_nil
          end
        end
        
        describe ".min" do
          it "returns the minimum value" do
            expect(ArrayUtils.min([1, 3, 2, 5, 4])).to eq(1)
          end
          
          it "returns nil for empty array" do
            expect(ArrayUtils.min([])).to be_nil
          end
        end
      end
    RSPEC
    
    puts "RSpec test examples created:"
    puts "- User specs: Object behavior testing"
    puts "- ArrayUtils specs: Static method testing"
    
    puts "\nRSpec test structure:"
    puts rspec_user_test
    
    @examples << {
      title: "RSpec Examples",
      description: "Behavior-driven development with RSpec",
      code: <<~RUBY
        describe User do
          let(:user) { User.new("Alice", "alice@test.com", 30) }
          
          describe "#adult?" do
            it "returns true for adults" do
              expect(user.adult?).to be true
            end
          end
        end
      RUBY
    }
    
    puts "\n✅ RSpec Examples completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Testing Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate different testing patterns!"
  end
end

if __FILE__ == $0
  examples = TestingExamples.new
  examples.start_examples
end
