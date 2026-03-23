# Unit Testing in Ruby

## Overview

Unit testing is the practice of testing individual components or units of code in isolation. In Ruby, unit testing is fundamental to building reliable, maintainable software applications. This guide covers comprehensive unit testing strategies, best practices, and advanced techniques.

## Testing Fundamentals

### Basic Unit Test Structure
```ruby
require 'test/unit'

class CalculatorTest < Test::Unit::TestCase
  def setup
    @calculator = Calculator.new
  end

  def teardown
    @calculator = nil
  end

  def test_addition
    result = @calculator.add(2, 3)
    assert_equal(5, result, "2 + 3 should equal 5")
  end

  def test_subtraction
    result = @calculator.subtract(10, 4)
    assert_equal(6, result, "10 - 4 should equal 6")
  end

  def test_multiplication
    result = @calculator.multiply(4, 5)
    assert_equal(20, result, "4 * 5 should equal 20")
  end

  def test_division
    result = @calculator.divide(20, 4)
    assert_equal(5, result, "20 / 4 should equal 5")
  end

  def test_division_by_zero
    assert_raises(ZeroDivisionError) do
      @calculator.divide(10, 0)
    end
  end

  def test_addition_with_negative_numbers
    result = @calculator.add(-2, 3)
    assert_equal(1, result, "-2 + 3 should equal 1")
  end
end

# Simple Calculator class for testing
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
    raise ZeroDivisionError if b == 0
    a / b.to_f
  end
end
```

### RSpec Testing Framework
```ruby
require 'rspec'

RSpec.describe Calculator do
  let(:calculator) { Calculator.new }

  describe '#add' do
    it 'returns the sum of two positive numbers' do
      result = calculator.add(2, 3)
      expect(result).to eq(5)
    end

    it 'handles negative numbers' do
      result = calculator.add(-2, 3)
      expect(result).to eq(1)
    end

    it 'returns zero when adding opposites' do
      result = calculator.add(5, -5)
      expect(result).to eq(0)
    end

    it 'handles large numbers' do
      result = calculator.add(1_000_000, 2_000_000)
      expect(result).to eq(3_000_000)
    end
  end

  describe '#subtract' do
    it 'returns the difference of two numbers' do
      result = calculator.subtract(10, 4)
      expect(result).to eq(6)
    end

    it 'returns negative result when subtracting larger number' do
      result = calculator.subtract(4, 10)
      expect(result).to eq(-6)
    end
  end

  describe '#multiply' do
    it 'returns the product of two numbers' do
      result = calculator.multiply(4, 5)
      expect(result).to eq(20)
    end

    it 'returns zero when multiplying by zero' do
      result = calculator.multiply(10, 0)
      expect(result).to eq(0)
    end

    it 'handles negative multiplication' do
      result = calculator.multiply(-2, 3)
      expect(result).to eq(-6)
    end
  end

  describe '#divide' do
    it 'returns the quotient of two numbers' do
      result = calculator.divide(20, 4)
      expect(result).to eq(5.0)
    end

    it 'returns a float result' do
      result = calculator.divide(7, 2)
      expect(result).to eq(3.5)
    end

    it 'raises ZeroDivisionError when dividing by zero' do
      expect { calculator.divide(10, 0) }.to raise_error(ZeroDivisionError)
    end
  end
end
```

### Minitest Framework
```ruby
require 'minitest/autorun'

class TestCalculator < Minitest::Test
  def setup
    @calculator = Calculator.new
  end

  def test_add_two_positive_numbers
    assert_equal(5, @calculator.add(2, 3))
  end

  def test_add_negative_number
    assert_equal(1, @calculator.add(-2, 3))
  end

  def test_subtract_numbers
    assert_equal(6, @calculator.subtract(10, 4))
  end

  def test_multiply_numbers
    assert_equal(20, @calculator.multiply(4, 5))
  end

  def test_divide_numbers
    assert_equal(5.0, @calculator.divide(20, 4))
  end

  def test_divide_by_zero_raises_error
    assert_raises(ZeroDivisionError) do
      @calculator.divide(10, 0)
    end
  end

  def test_assertions_with_messages
    result = @calculator.add(2, 3)
    assert_equal(5, result, "Addition should work correctly")
  end
end
```

## Advanced Testing Techniques

### Test Doubles and Mocks
```ruby
require 'rspec'

# Class to be tested
class OrderProcessor
  def initialize(payment_gateway, inventory_service, notifier)
    @payment_gateway = payment_gateway
    @inventory_service = inventory_service
    @notifier = notifier
  end

  def process_order(order)
    # Check inventory
    return false unless @inventory_service.check_availability(order[:product_id], order[:quantity])
    
    # Process payment
    payment_result = @payment_gateway.charge(order[:customer_id], order[:amount])
    return false unless payment_result[:success]
    
    # Update inventory
    @inventory_service.reserve(order[:product_id], order[:quantity])
    
    # Send notification
    @notifier.send_confirmation(order[:customer_id], order[:id])
    
    true
  end
end

# Test with mocks
RSpec.describe OrderProcessor do
  let(:payment_gateway) { double('PaymentGateway') }
  let(:inventory_service) { double('InventoryService') }
  let(:notifier) { double('Notifier') }
  let(:order_processor) { OrderProcessor.new(payment_gateway, inventory_service, notifier) }
  
  let(:order) do
    {
      id: 'ORD-001',
      customer_id: 'CUST-001',
      product_id: 'PROD-001',
      quantity: 2,
      amount: 100.0
    }
  end

  describe '#process_order' do
    context 'when order is successful' do
      before do
        allow(inventory_service).to receive(:check_availability)
          .with('PROD-001', 2)
          .and_return(true)
        
        allow(payment_gateway).to receive(:charge)
          .with('CUST-001', 100.0)
          .and_return({ success: true, transaction_id: 'TXN-001' })
        
        allow(inventory_service).to receive(:reserve)
          .with('PROD-001', 2)
        
        allow(notifier).to receive(:send_confirmation)
          .with('CUST-001', 'ORD-001')
      end

      it 'returns true for successful order' do
        result = order_processor.process_order(order)
        expect(result).to be true
      end

      it 'checks inventory availability' do
        order_processor.process_order(order)
        expect(inventory_service).to have_received(:check_availability)
          .with('PROD-001', 2)
      end

      it 'processes payment' do
        order_processor.process_order(order)
        expect(payment_gateway).to have_received(:charge)
          .with('CUST-001', 100.0)
      end

      it 'reserves inventory' do
        order_processor.process_order(order)
        expect(inventory_service).to have_received(:reserve)
          .with('PROD-001', 2)
      end

      it 'sends confirmation notification' do
        order_processor.process_order(order)
        expect(notifier).to have_received(:send_confirmation)
          .with('CUST-001', 'ORD-001')
      end
    end

    context 'when inventory is not available' do
      before do
        allow(inventory_service).to receive(:check_availability)
          .with('PROD-001', 2)
          .and_return(false)
      end

      it 'returns false' do
        result = order_processor.process_order(order)
        expect(result).to be false
      end

      it 'does not process payment' do
        order_processor.process_order(order)
        expect(payment_gateway).not_to have_received(:charge)
      end
    end

    context 'when payment fails' do
      before do
        allow(inventory_service).to receive(:check_availability)
          .with('PROD-001', 2)
          .and_return(true)
        
        allow(payment_gateway).to receive(:charge)
          .with('CUST-001', 100.0)
          .and_return({ success: false, error: 'Insufficient funds' })
      end

      it 'returns false' do
        result = order_processor.process_order(order)
        expect(result).to be false
      end

      it 'does not reserve inventory' do
        order_processor.process_order(order)
        expect(inventory_service).not_to have_received(:reserve)
      end
    end
  end
end
```

### Test Data Builders
```ruby
class OrderBuilder
  attr_accessor :id, :customer_id, :product_id, :quantity, :amount, :status

  def initialize
    @id = "ORD-#{SecureRandom.hex(4).upcase}"
    @customer_id = "CUST-001"
    @product_id = "PROD-001"
    @quantity = 1
    @amount = 100.0
    @status = :pending
  end

  def with_id(id)
    @id = id
    self
  end

  def with_customer(customer_id)
    @customer_id = customer_id
    self
  end

  def with_product(product_id)
    @product_id = product_id
    self
  end

  def with_quantity(quantity)
    @quantity = quantity
    self
  end

  def with_amount(amount)
    @amount = amount
    self
  end

  def with_status(status)
    @status = status
    self
  end

  def build
    {
      id: @id,
      customer_id: @customer_id,
      product_id: @product_id,
      quantity: @quantity,
      amount: @amount,
      status: @status,
      created_at: Time.now
    }
  end
end

# Usage in tests
RSpec.describe OrderBuilder do
  it 'creates a basic order' do
    order = OrderBuilder.new.build
    
    expect(order[:id]).to match(/^ORD-\w{8}$/)
    expect(order[:customer_id]).to eq('CUST-001')
    expect(order[:quantity]).to eq(1)
    expect(order[:amount]).to eq(100.0)
    expect(order[:status]).to eq(:pending)
  end

  it 'allows customization of order attributes' do
    order = OrderBuilder.new
      .with_id('CUSTOM-001')
      .with_customer('CUST-999')
      .with_quantity(5)
      .with_amount(500.0)
      .with_status(:confirmed)
      .build
    
    expect(order[:id]).to eq('CUSTOM-001')
    expect(order[:customer_id]).to eq('CUST-999')
    expect(order[:quantity]).to eq(5)
    expect(order[:amount]).to eq(500.0)
    expect(order[:status]).to eq(:confirmed)
  end
end
```

### Parameterized Tests
```ruby
require 'rspec'

class MathOperations
  def self.add(a, b)
    a + b
  end

  def self.multiply(a, b)
    a * b
  end

  def self.factorial(n)
    return 1 if n <= 1
    n * factorial(n - 1)
  end

  def self.is_prime?(n)
    return false if n <= 1
    return true if n == 2
    
    (2..Math.sqrt(n).to_i).none? { |i| n % i == 0 }
  end
end

RSpec.describe MathOperations do
  describe '.add' do
    test_cases = [
      [1, 2, 3],
      [0, 0, 0],
      [-1, 1, 0],
      [100, 200, 300],
      [-5, -10, -15],
      [1.5, 2.5, 4.0]
    ]

    test_cases.each do |a, b, expected|
      it "returns #{expected} when adding #{a} and #{b}" do
        result = MathOperations.add(a, b)
        expect(result).to eq(expected)
      end
    end
  end

  describe '.multiply' do
    context 'with various inputs' do
      [
        [2, 3, 6],
        [0, 5, 0],
        [-2, 3, -6],
        [1.5, 2, 3.0],
        [-1, -1, 1]
      ].each do |a, b, expected|
        it "returns #{expected} when multiplying #{a} and #{b}" do
          result = MathOperations.multiply(a, b)
          expect(result).to eq(expected)
        end
      end
    end
  end

  describe '.factorial' do
    test_cases = [
      [0, 1],
      [1, 1],
      [2, 2],
      [3, 6],
      [4, 24],
      [5, 120],
      [10, 3628800]
    ]

    test_cases.each do |input, expected|
      it "returns #{expected} for factorial(#{input})" do
        result = MathOperations.factorial(input)
        expect(result).to eq(expected)
      end
    end

    it 'raises error for negative input' do
      expect { MathOperations.factorial(-1) }.to raise_error(ArgumentError)
    end
  end

  describe '.is_prime?' do
    primes = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47]
    non_primes = [1, 4, 6, 8, 9, 10, 12, 14, 15, 16, 18, 20, 21, 22, 24, 25, 26, 27, 28, 30]

    primes.each do |prime|
      it "identifies #{prime} as prime" do
        expect(MathOperations.is_prime?(prime)).to be true
      end
    end

    non_primes.each do |non_prime|
      it "identifies #{non_prime} as non-prime" do
        expect(MathOperations.is_prime?(non_prime)).to be false
      end
    end
  end
end
```

## Test Organization and Structure

### Test Hierarchy
```ruby
# test/test_helper.rb
require 'test/unit'
require_relative '../lib/calculator'
require_relative '../lib/order_processor'
require_relative '../lib/math_operations'

class TestHelper
  def self.setup_test_data
    @test_data ||= {}
  end

  def self.get_test_data(key)
    @test_data[key]
  end

  def self.set_test_data(key, value)
    @test_data[key] = value
  end
end

# test/unit/calculator_test.rb
require_relative '../test_helper'

class CalculatorTest < Test::Unit::TestCase
  def setup
    @calculator = Calculator.new
    TestHelper.set_test_data(:calculator, @calculator)
  end

  def teardown
    TestHelper.set_test_data(:calculator, nil)
  end

  # Test methods...
end

# test/integration/order_processing_test.rb
require_relative '../test_helper'

class OrderProcessingTest < Test::Unit::TestCase
  def setup
    @mock_payment_gateway = MockPaymentGateway.new
    @mock_inventory_service = MockInventoryService.new
    @mock_notifier = MockNotifier.new
    
    @order_processor = OrderProcessor.new(
      @mock_payment_gateway,
      @mock_inventory_service,
      @mock_notifier
    )
  end

  def test_successful_order_processing
    order = create_test_order
    result = @order_processor.process_order(order)
    
    assert(result, "Order should be processed successfully")
    assert(@mock_payment_gateway.charged?, "Payment should be charged")
    assert(@mock_inventory_service.reserved?, "Inventory should be reserved")
    assert(@mock_notifier.notified?, "Notification should be sent")
  end

  private

  def create_test_order
    {
      id: 'TEST-001',
      customer_id: 'CUST-001',
      product_id: 'PROD-001',
      quantity: 2,
      amount: 100.0
    }
  end
end
```

### Shared Test Examples
```ruby
# test/support/shared_examples.rb
RSpec.shared_examples 'a numeric operation' do
  it 'returns a numeric result' do
    result = operation.call(2, 3)
    expect(result).to be_a(Numeric)
  end

  it 'handles zero correctly' do
    result = operation.call(5, 0)
    expect(result).to be_a(Numeric)
  end

  it 'handles negative numbers' do
    result = operation.call(-2, 3)
    expect(result).to be_a(Numeric)
  end
end

RSpec.shared_examples 'a commutative operation' do
  it 'produces the same result regardless of operand order' do
    result1 = operation.call(2, 3)
    result2 = operation.call(3, 2)
    expect(result1).to eq(result2)
  end
end

# Usage in test files
RSpec.describe MathOperations do
  describe '.add' do
    let(:operation) { ->(a, b) { MathOperations.add(a, b) } }

    it_behaves_like 'a numeric operation'
    it_behaves_like 'a commutative operation'

    it 'returns the sum of two numbers' do
      expect(operation.call(2, 3)).to eq(5)
    end
  end

  describe '.multiply' do
    let(:operation) { ->(a, b) { MathOperations.multiply(a, b) } }

    it_behaves_like 'a numeric operation'
    it_behaves_like 'a commutative operation'

    it 'returns the product of two numbers' do
      expect(operation.call(4, 5)).to eq(20)
    end
  end
end
```

## Performance Testing

### Benchmark Testing
```ruby
require 'benchmark'

class PerformanceTest < Test::Unit::TestCase
  def test_sorting_performance
    data = Array.new(10000) { rand(10000) }
    
    time = Benchmark.realtime do
      data.sort
    end
    
    assert(time < 1.0, "Sorting should complete in less than 1 second, took #{time}s")
  end

  def test_string_concatenation_performance
    strings = Array.new(1000) { "test_string_#{rand(1000)}" }
    
    time = Benchmark.realtime do
      result = ""
      strings.each { |s| result += s }
      result
    end
    
    assert(time < 0.1, "String concatenation should complete in less than 0.1s, took #{time}s")
  end

  def test_array_vs_set_lookup_performance
    data = Array.new(10000) { rand(10000) }
    array = data.dup
    set = data.to_set
    
    # Array lookup
    array_time = Benchmark.realtime do
      1000.times { array.include?(5000) }
    end
    
    # Set lookup
    set_time = Benchmark.realtime do
      1000.times { set.include?(5000) }
    end
    
    assert(set_time < array_time, "Set lookup should be faster than array lookup")
  end
end
```

### Memory Testing
```ruby
class MemoryTest < Test::Unit::TestCase
  def test_memory_usage
    initial_memory = get_memory_usage
    
    # Create large objects
    large_arrays = Array.new(100) { Array.new(10000) { rand(10000) } }
    
    current_memory = get_memory_usage
    memory_increase = current_memory - initial_memory
    
    # Clean up
    large_arrays = nil
    GC.start
    
    final_memory = get_memory_usage
    
    assert(final_memory < initial_memory + 1000000, "Memory should be freed after garbage collection")
  end

  private

  def get_memory_usage
    # Simple memory usage check
    GC.stat[:heap_allocated_pages] * GC.stat[:heap_page_size]
  end
end
```

## Test Coverage and Quality

### Coverage Analysis
```ruby
# lib/code_coverage.rb
require 'coverage'

class CoverageAnalyzer
  def self.start_coverage
    Coverage.start
  end

  def self.stop_coverage_and_report
    result = Coverage.result
    generate_coverage_report(result)
  end

  def self.generate_coverage_report(coverage_data)
    report = []
    report << "Code Coverage Report"
    report << "=" * 25
    report << ""
    
    total_files = 0
    covered_files = 0
    total_lines = 0
    covered_lines = 0
    
    coverage_data.each do |file, lines|
      next if file.include?('test/') || file.include?('/gems/')
      
      total_files += 1
      file_lines = lines.length
      file_covered = lines.count { |line| line }
      
      total_lines += file_lines
      covered_lines += file_covered
      
      if file_covered == file_lines
        covered_files += 1
      end
      
      coverage_percentage = (file_covered.to_f / file_lines * 100).round(2)
      report << "#{file}: #{coverage_percentage}% (#{file_covered}/#{file_lines})"
    end
    
    overall_coverage = (covered_lines.to_f / total_lines * 100).round(2)
    
    report << ""
    report << "Summary:"
    report << "Files: #{covered_files}/#{total_files}"
    report << "Lines: #{covered_lines}/#{total_lines}"
    report << "Coverage: #{overall_coverage}%"
    
    report.join("\n")
  end
end

# Usage in test runner
if __FILE__ == $0
  CoverageAnalyzer.start_coverage
  
  # Run all tests
  Dir['test/**/*_test.rb'].each { |file| require file }
  
  # Generate coverage report
  puts CoverageAnalyzer.stop_coverage_and_report
end
```

### Mutation Testing
```ruby
class MutationTester
  def self.mutate_and_test(original_file, test_files)
    mutations = generate_mutations(original_file)
    results = []
    
    mutations.each do |mutation|
      mutated_code = apply_mutation(original_file, mutation)
      
      # Write mutated code
      write_mutated_file(original_file, mutated_code)
      
      # Run tests
      test_result = run_tests(test_files)
      
      results << {
        mutation: mutation,
        survived: test_result[:passed],
        test_result: test_result
      }
      
      # Restore original code
      restore_original_file(original_file)
    end
    
    generate_mutation_report(results)
  end

  private

  def self.generate_mutations(file_path)
    content = File.read(file_path)
    mutations = []
    
    # Simple mutation patterns
    mutations << { type: :operator, original: '+', mutated: '-', line: find_line_with_operator(content, '+') }
    mutations << { type: :operator, original: '-', mutated: '+', line: find_line_with_operator(content, '-') }
    mutations << { type: :operator, original: '*', mutated: '/', line: find_line_with_operator(content, '*') }
    mutations << { type: :operator, original: '>', mutated: '<', line: find_line_with_operator(content, '>') }
    mutations << { type: :operator, original: '<', mutated: '>', line: find_line_with_operator(content, '<') }
    
    mutations << { type: :condition, original: 'true', mutated: 'false', line: find_line_with_condition(content, 'true') }
    mutations << { type: :condition, original: 'false', mutated: 'true', line: find_line_with_condition(content, 'false') }
    
    mutations.select { |m| m[:line] }
  end

  def self.apply_mutation(file_path, mutation)
    content = File.read(file_path)
    lines = content.split("\n")
    
    if mutation[:line] && lines[mutation[:line]]
      lines[mutation[:line]] = lines[mutation[:line]].gsub(mutation[:original], mutation[:mutated])
    end
    
    lines.join("\n")
  end

  def self.find_line_with_operator(content, operator)
    lines = content.split("\n")
    lines.each_with_index.find { |line, i| line.include?(operator) && !line.strip.start_with?('#') }&.last
  end

  def self.find_line_with_condition(content, condition)
    lines = content.split("\n")
    lines.each_with_index.find { |line, i| line.include?(condition) && !line.strip.start_with?('#') }&.last
  end

  def self.write_mutated_file(file_path, content)
    File.write(file_path, content)
  end

  def self.restore_original_file(file_path)
    # Implementation would depend on your version control system
    # This is a simplified version
  end

  def self.run_tests(test_files)
    # Simplified test runner
    passed = 0
    failed = 0
    
    test_files.each do |file|
      begin
        require file
        passed += 1
      rescue => e
        failed += 1
      end
    end
    
    { passed: passed, failed: failed, total: passed + failed }
  end

  def self.generate_mutation_report(results)
    survived = results.count { |r| r[:survived] }
    total = results.length
    
    report = []
    report << "Mutation Testing Report"
    report << "=" * 25
    report << ""
    report << "Total mutations: #{total}"
    report << "Survived mutations: #{survived}"
    report << "Mutation score: #{((total - survived).to_f / total * 100).round(2)}%"
    report << ""
    
    if survived > 0
      report << "Survived mutations:"
      results.select { |r| r[:survived] }.each do |result|
        report << "  #{result[:mutation][:type]} on line #{result[:mutation][:line]}"
      end
    end
    
    report.join("\n")
  end
end
```

## Best Practices

1. **Test Organization**: Structure tests logically with clear naming conventions
2. **Descriptive Tests**: Write tests that clearly describe expected behavior
3. **Independent Tests**: Ensure tests don't depend on each other
4. **Test Data**: Use factories and builders for consistent test data
5. **Mocking**: Use mocks judiciously to test interfaces, not implementations
6. **Coverage**: Aim for high test coverage but focus on critical paths
7. **Maintenance**: Keep tests updated with code changes
8. **Performance**: Include performance tests for critical operations

## Conclusion

Unit testing is essential for building robust Ruby applications. By using appropriate testing frameworks, organizing tests effectively, and following best practices, you can ensure code quality, catch bugs early, and maintain confidence in your codebase throughout the development lifecycle.

## Further Reading

- [RSpec Documentation](https://rspec.info/)
- [Minitest Documentation](https://github.com/seattlerb/minitest)
- [Test::Unit Documentation](https://ruby-doc.org/stdlib-3.0.0/libdoc/test/unit/rdoc/Test/Unit.html)
- [Testing Anti-Patterns](https://testinganti-patterns.com/)
- [Mutation Testing](https://mutation-testing.org/)
