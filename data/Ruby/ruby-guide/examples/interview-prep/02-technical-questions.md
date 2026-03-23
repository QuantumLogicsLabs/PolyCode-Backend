# Technical Interview Questions in Ruby

## Overview

Technical interviews test your programming skills, problem-solving abilities, and understanding of computer science concepts. This guide covers common technical interview questions with Ruby implementations and detailed explanations.

## Ruby Language Questions

### Ruby Basics
```ruby
# Question: What's the difference between nil, false, and falsy values in Ruby?
class RubyBasics
  def self.nil_vs_false
    # nil represents absence of value
    nil_value = nil
    false_value = false
    
    # Both are falsy in conditional statements
    result = {
      nil_to_s: nil_value.to_s,          # "nil"
      false_to_s: false_value.to_s,      # "false"
      nil_class: nil_value.class,        # NilClass
      false_class: false_value.class,    # FalseClass
      nil_nil?: nil_value.nil?,          # true
      false_nil?: false_value.nil?,      # false
      nil_false?: nil_value == false,    # false
      false_false?: false_value == false # true
    }
    
    result
  end

  def self.symbol_vs_string
    # Symbols are immutable, strings are mutable
    symbol = :hello
    string = "hello"
    
    # Symbols are singletons, strings create new objects
    symbol1 = :hello
    symbol2 = :hello
    string1 = "hello"
    string2 = "hello"
    
    {
      symbol_object_id: symbol1.object_id == symbol2.object_id,  # true
      string_object_id: string1.object_id == string2.object_id, # false
      symbol_immutable: symbol.frozen?,                         # true
      string_mutable: string.frozen?                            # false
      symbol_to_s: symbol.to_s,                                # "hello"
      string_to_sym: string.to_sym                              # :hello
    }
  end

  def self.method_types
    # Instance methods vs class methods
    class Example
      def instance_method
        "I'm an instance method"
      end
      
      def self.class_method
        "I'm a class method"
      end
      
      class << self
        def another_class_method
          "Another class method"
        end
      end
    end
    
    example = Example.new
    
    {
      instance_method: example.instance_method,
      class_method: Example.class_method,
      another_class_method: Example.another_class_method
    }
  end

  def self.block_vs_proc_vs_lambda
    # Different ways to handle blocks of code
    
    # Block (passed to method)
    def with_block
      yield if block_given?
    end
    
    # Proc object
    my_proc = Proc.new { |x| x * 2 }
    
    # Lambda
    my_lambda = lambda { |x| x * 2 }
    my_lambda2 = ->(x) { x * 2 }
    
    # Differences in behavior
    {
      block_result: with_block { "Block executed" },
      proc_result: my_proc.call(5),
      lambda_result: my_lambda.call(5),
      lambda2_result: my_lambda2.call(5),
      
      # Proc doesn't check arity, Lambda does
      proc_extra_args: my_proc.call(1, 2, 3),    # Works
      lambda_extra_args: begin
        my_lambda.call(1, 2, 3)               # Raises error
      rescue ArgumentError => e
        e.message
      end,
      
      # Return behavior
      proc_return: begin
        def test_proc
          my_proc = Proc.new { return "From proc" }
          my_proc.call
          "After proc"
        end
        test_proc
      end,
      
      lambda_return: begin
        def test_lambda
          my_lambda = lambda { return "From lambda" }
          my_lambda.call
          "After lambda"
        end
        test_lambda
      end
    }
  end
end

# Usage examples
puts "Nil vs False:"
RubyBasics.nil_vs_false.each { |k, v| puts "  #{k}: #{v}" }

puts "\nSymbol vs String:"
RubyBasics.symbol_vs_string.each { |k, v| puts "  #{k}: #{v}" }

puts "\nMethod Types:"
RubyBasics.method_types.each { |k, v| puts "  #{k}: #{v}" }

puts "\nBlock vs Proc vs Lambda:"
RubyBasics.block_vs_proc_vs_lambda.each { |k, v| puts "  #{k}: #{v}" }
```

### Ruby Metaprogramming
```ruby
# Question: How does Ruby's metaprogramming work?
class RubyMetaprogramming
  def self.dynamic_methods
    # Define methods dynamically
    class DynamicClass
      # Define methods using define_method
      [:hello, :goodbye, :thank_you].each do |method_name|
        define_method(method_name) do |name = "World"
          "#{method_name.to_s.capitalize}, #{name}!"
        end
      end
      
      # Define methods using class_eval
      class_eval do
        [:add, :subtract, :multiply, :divide].each do |operation|
          define_method("math_#{operation}") do |a, b|
            case operation
            when :add then a + b
            when :subtract then a - b
            when :multiply then a * b
            when :divide then a / b.to_f
            end
          end
        end
      end
    end
    
    obj = DynamicClass.new
    
    {
      hello: obj.hello("Ruby"),
      goodbye: obj.goodbye,
      math_add: obj.math_add(5, 3),
      math_divide: obj.math_divide(10, 2)
    }
  end

  def self.method_missing_example
    # Implement method_missing for dynamic method calls
    class MethodMissingExample
      def method_missing(method_name, *args)
        if method_name.to_s.start_with?('say_')
          phrase = method_name.to_s.sub('say_', '').gsub('_', ' ')
          "You said: #{phrase} #{args.join(' ')}"
        else
          super
        end
      end
      
      def respond_to_missing?(method_name, include_private = false)
        method_name.to_s.start_with?('say_') || super
      end
    end
    
    obj = MethodMissingExample.new
    
    {
      say_hello_world: obj.say_hello_world,
      say_goodbye: obj.say_goodbye("everyone"),
      unknown_method: begin
        obj.unknown_method
      rescue NoMethodError => e
        e.message
      end
    }
  end

  def self.reflection_example
    # Ruby's reflection capabilities
    class ReflectionExample
      attr_reader :instance_var
      @@class_var = "class variable"
      
      def initialize
        @instance_var = "instance variable"
      end
      
      def instance_method
        "instance method"
      end
      
      def self.class_method
        "class method"
      end
      
      private
      
      def private_method
        "private method"
      end
    end
    
    obj = ReflectionExample.new
    
    {
      class_name: ReflectionExample.name,
      class_ancestors: ReflectionExample.ancestors.map(&:name),
      instance_methods: ReflectionExample.instance_methods(false).sort,
      class_methods: ReflectionExample.methods(false).sort,
      instance_variables: obj.instance_variables,
      instance_var_value: obj.instance_variable_get(:@instance_var),
      class_variables: ReflectionExample.class_variables,
      class_var_value: ReflectionExample.class_variable_get(:@@class_var),
      responds_to_instance: obj.respond_to?(:instance_method),
      responds_to_private: obj.respond_to?(:private_method),
      responds_to_missing: obj.respond_to?(:say_hello)
    }
  end

  def self.singleton_class_example
    # Singleton class and eigenclass
    class SingletonExample
      def self.class_method
        "Regular class method"
      end
    end
    
    obj = SingletonExample.new
    
    # Define method on object's singleton class
    def obj.object_method
      "Method only for this object"
    end
    
    # Define class method on singleton class
    class << obj
      def object_class_method
        "Class method for this object's class"
      end
    end
    
    {
      regular_class_method: SingletonExample.class_method,
      object_method: obj.object_method,
      object_class_method: obj.object_class_method,
      singleton_class: obj.singleton_class.name
    }
  end
end

# Usage examples
puts "\nDynamic Methods:"
RubyMetaprogramming.dynamic_methods.each { |k, v| puts "  #{k}: #{v}" }

puts "\nMethod Missing:"
RubyMetaprogramming.method_missing_example.each { |k, v| puts "  #{k}: #{v}" }

puts "\nReflection:"
reflection = RubyMetaprogramming.reflection_example
reflection.each { |k, v| puts "  #{k}: #{v}" }

puts "\nSingleton Class:"
RubyMetaprogramming.singleton_class_example.each { |k, v| puts "  #{k}: #{v}" }
```

## Algorithm Questions

### Sorting Algorithms
```ruby
# Question: Implement different sorting algorithms in Ruby
class SortingAlgorithms
  def self.bubble_sort(arr)
    n = arr.length
    sorted = arr.dup
    
    n.times do |i|
      (n - i - 1).times do |j|
        if sorted[j] > sorted[j + 1]
          sorted[j], sorted[j + 1] = sorted[j + 1], sorted[j]
        end
      end
    end
    
    sorted
  end

  def self.selection_sort(arr)
    sorted = arr.dup
    n = sorted.length
    
    n.times do |i|
      min_index = i
      (i + 1...n).each do |j|
        min_index = j if sorted[j] < sorted[min_index]
      end
      sorted[i], sorted[min_index] = sorted[min_index], sorted[i]
    end
    
    sorted
  end

  def self.insertion_sort(arr)
    sorted = arr.dup
    
    (1...sorted.length).each do |i|
      key = sorted[i]
      j = i - 1
      
      while j >= 0 && sorted[j] > key
        sorted[j + 1] = sorted[j]
        j -= 1
      end
      
      sorted[j + 1] = key
    end
    
    sorted
  end

  def self.merge_sort(arr)
    return arr if arr.length <= 1
    
    mid = arr.length / 2
    left = merge_sort(arr[0...mid])
    right = merge_sort(arr[mid...arr.length])
    
    merge(left, right)
  end

  def self.quick_sort(arr)
    return arr if arr.length <= 1
    
    pivot = arr[arr.length / 2]
    left = arr.select { |x| x < pivot }
    middle = arr.select { |x| x == pivot }
    right = arr.select { |x| x > pivot }
    
    quick_sort(left) + middle + quick_sort(right)
  end

  def self.compare_algorithms(test_array)
    {
      original: test_array,
      bubble_sort: bubble_sort(test_array),
      selection_sort: selection_sort(test_array),
      insertion_sort: insertion_sort(test_array),
      merge_sort: merge_sort(test_array),
      quick_sort: quick_sort(test_array),
      ruby_sort: test_array.sort
    }
  end

  private

  def self.merge(left, right)
    result = []
    
    while left.any? && right.any?
      if left.first <= right.first
        result << left.shift
      else
        result << right.shift
      end
    end
    
    result + left + right
  end
end

# Usage example
test_array = [64, 34, 25, 12, 22, 11, 90]
puts "Original array: #{test_array}"

algorithms = SortingAlgorithms.compare_algorithms(test_array)
algorithms.each { |name, result| puts "#{name}: #{result}" }
```

### Search Algorithms
```ruby
# Question: Implement binary search and linear search
class SearchAlgorithms
  def self.linear_search(arr, target)
    arr.each_with_index do |element, index|
      return index if element == target
    end
    -1
  end

  def self.binary_search(arr, target)
    left = 0
    right = arr.length - 1
    
    while left <= right
      mid = (left + right) / 2
      
      if arr[mid] == target
        return mid
      elsif arr[mid] < target
        left = mid + 1
      else
        right = mid - 1
      end
    end
    
    -1
  end

  def self.binary_search_recursive(arr, target, left = 0, right = arr.length - 1)
    return -1 if left > right
    
    mid = (left + right) / 2
    
    if arr[mid] == target
      mid
    elsif arr[mid] < target
      binary_search_recursive(arr, target, mid + 1, right)
    else
      binary_search_recursive(arr, target, left, mid - 1)
    end
  end

  def self.compare_searches(arr, target)
    {
      array: arr,
      target: target,
      linear_search: linear_search(arr, target),
      binary_search: binary_search(arr, target),
      binary_search_recursive: binary_search_recursive(arr, target)
    }
  end

  def self.find_first_occurrence(arr, target)
    left = 0
    right = arr.length - 1
    result = -1
    
    while left <= right
      mid = (left + right) / 2
      
      if arr[mid] == target
        result = mid
        right = mid - 1  # Continue searching left side
      elsif arr[mid] < target
        left = mid + 1
      else
        right = mid - 1
      end
    end
    
    result
  end

  def self.find_last_occurrence(arr, target)
    left = 0
    right = arr.length - 1
    result = -1
    
    while left <= right
      mid = (left + right) / 2
      
      if arr[mid] == target
        result = mid
        left = mid + 1  # Continue searching right side
      elsif arr[mid] < target
        left = mid + 1
      else
        right = mid - 1
      end
    end
    
    result
  end
end

# Usage example
sorted_array = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19]
target = 13

puts "Binary Search Example:"
result = SearchAlgorithms.compare_searches(sorted_array, target)
result.each { |k, v| puts "#{k}: #{v}" }

puts "\nFirst and last occurrence:"
array_with_duplicates = [1, 2, 2, 2, 3, 4, 4, 5]
target2 = 2
puts "First occurrence of #{target2}: #{SearchAlgorithms.find_first_occurrence(array_with_duplicates, target2)}"
puts "Last occurrence of #{target2}: #{SearchAlgorithms.find_last_occurrence(array_with_duplicates, target2)}"
```

## Data Structure Questions

### Linked List Implementation
```ruby
# Question: Implement a linked list in Ruby
class Node
  attr_accessor :value, :next_node

  def initialize(value)
    @value = value
    @next_node = nil
  end
end

class LinkedList
  def initialize
    @head = nil
    @size = 0
  end

  def append(value)
    new_node = Node.new(value)
    
    if @head.nil?
      @head = new_node
    else
      current = @head
      current = current.next_node while current.next_node
      current.next_node = new_node
    end
    
    @size += 1
  end

  def prepend(value)
    new_node = Node.new(value)
    new_node.next_node = @head
    @head = new_node
    @size += 1
  end

  def delete(value)
    return false if @head.nil?
    
    if @head.value == value
      @head = @head.next_node
      @size -= 1
      return true
    end
    
    current = @head
    while current.next_node
      if current.next_node.value == value
        current.next_node = current.next_node.next_node
        @size -= 1
        return true
      end
      current = current.next_node
    end
    
    false
  end

  def find(value)
    current = @head
    index = 0
    
    while current
      return index if current.value == value
      current = current.next_node
      index += 1
    end
    
    -1
  end

  def reverse
    prev = nil
    current = @head
    
    while current
      next_node = current.next_node
      current.next_node = prev
      prev = current
      current = next_node
    end
    
    @head = prev
  end

  def to_array
    result = []
    current = @head
    
    while current
      result << current.value
      current = current.next_node
    end
    
    result
  end

  def size
    @size
  end

  def empty?
    @size == 0
  end
end

# Usage example
list = LinkedList.new
[1, 2, 3, 4, 5].each { |val| list.append(val) }

puts "Linked List Operations:"
puts "Original list: #{list.to_array}"
puts "Size: #{list.size}"
puts "Find 3: #{list.find(3)}"
puts "Delete 3: #{list.delete(3)}"
puts "After delete: #{list.to_array}"

list.reverse
puts "Reversed: #{list.to_array}"
```

### Stack Implementation
```ruby
# Question: Implement a stack using arrays and linked lists
class ArrayStack
  def initialize
    @elements = []
  end

  def push(element)
    @elements.push(element)
  end

  def pop
    @elements.pop
  end

  def peek
    @elements.last
  end

  def empty?
    @elements.empty?
  end

  def size
    @elements.length
  end
end

class LinkedListStack
  def initialize
    @top = nil
    @size = 0
  end

  def push(element)
    new_node = Node.new(element)
    new_node.next_node = @top
    @top = new_node
    @size += 1
  end

  def pop
    return nil if @top.nil?
    
    value = @top.value
    @top = @top.next_node
    @size -= 1
    value
  end

  def peek
    @top ? @top.value : nil
  end

  def empty?
    @size == 0
  end

  def size
    @size
  end
end

# Stack-based problem: Check if parentheses are balanced
class ParenthesesChecker
  def self.balanced?(string)
    stack = ArrayStack.new
    pairs = { ')' => '(', '}' => '{', ']' => '[' }
    
    string.each_char do |char|
      if char == '(' || char == '{' || char == '['
        stack.push(char)
      elsif char == ')' || char == '}' || char == ']'
        return false if stack.empty? || stack.pop != pairs[char]
      end
    end
    
    stack.empty?
  end
end

# Usage example
puts "\nStack Implementations:"
array_stack = ArrayStack.new
[1, 2, 3].each { |val| array_stack.push(val) }
puts "Array stack: #{[array_stack.pop, array_stack.pop, array_stack.pop]}"

linked_stack = LinkedListStack.new
[4, 5, 6].each { |val| linked_stack.push(val) }
puts "Linked stack: #{[linked_stack.pop, linked_stack.pop, linked_stack.pop]}"

puts "\nParentheses checking:"
test_strings = ["()", "()[]{}", "(]", "([{}])", "([)]"]
test_strings.each { |str| puts "#{str}: #{ParenthesesChecker.balanced?(str)}" }
```

## Problem-Solving Questions

### Two Sum Problem
```ruby
# Question: Find two numbers that add up to a target
class TwoSum
  def self.brute_force(nums, target)
    (0...nums.length).each do |i|
      (i + 1...nums.length).each do |j|
        return [i, j] if nums[i] + nums[j] == target
      end
    end
    nil
  end

  def self.hash_table(nums, target)
    hash_map = {}
    
    nums.each_with_index do |num, i|
      complement = target - num
      
      return [hash_map[complement], i] if hash_map.key?(complement)
      
      hash_map[num] = i
    end
    
    nil
  end

  def self.two_pointer(nums, target)
    nums_with_index = nums.each_with_index.to_a.sort_by(&:first)
    
    left = 0
    right = nums_with_index.length - 1
    
    while left < right
      sum = nums_with_index[left][0] + nums_with_index[right][0]
      
      if sum == target
        return [nums_with_index[left][1], nums_with_index[right][1]].sort
      elsif sum < target
        left += 1
      else
        right -= 1
      end
    end
    
    nil
  end

  def self.compare_solutions(nums, target)
    {
      array: nums,
      target: target,
      brute_force: brute_force(nums, target),
      hash_table: hash_table(nums, target),
      two_pointer: two_pointer(nums, target)
    }
  end
end

# Usage example
nums = [2, 7, 11, 15]
target = 9

result = TwoSum.compare_solutions(nums, target)
result.each { |method, indices| puts "#{method}: #{indices}" }
```

### Valid Parentheses
```ruby
# Question: Check if parentheses are balanced
class ValidParentheses
  def self.is_valid?(s)
    stack = []
    pairs = { ')' => '(', '}' => '{', ']' => '[' }
    
    s.each_char do |char|
      if char == '(' || char == '{' || char == '['
        stack.push(char)
      elsif char == ')' || char == '}' || char == ']'
        return false if stack.empty? || stack.pop != pairs[char]
      end
    end
    
    stack.empty?
  end

  def self.minimum_add_to_make_valid(s)
    stack = []
    additions = 0
    
    s.each_char do |char|
      if char == '(' || char == '{' || char == '['
        stack.push(char)
      elsif char == ')' || char == '}' || char == ']'
        if stack.empty?
          additions += 1
        else
          stack.pop
        end
      end
    end
    
    additions + stack.length
  end

  def self.longest_valid_parentheses(s)
    max_length = 0
    stack = [-1]  # Base index
    
    s.each_char.with_index do |char, i|
      if char == '('
        stack.push(i)
      else
        stack.pop
        if stack.empty?
          stack.push(i)
        else
          length = i - stack.last
          max_length = [max_length, length].max
        end
      end
    end
    
    max_length
  end
end

# Usage example
test_cases = ["()", "()[]{}", "(]", "([{}])", "([)]"]
puts "Valid Parentheses:"
test_cases.each { |test| puts "#{test}: #{ValidParentheses.is_valid?(test)}" }

puts "\nMinimum additions:"
puts "()) needs #{ValidParentheses.minimum_add_to_make_valid('())')} additions"
puts "([)] needs #{ValidParentheses.minimum_add_to_make_valid('([)]')} additions"

puts "\nLongest valid parentheses:"
puts "(()()) has #{ValidParentheses.longest_valid_parentheses('(()())')} valid chars"
puts "())() has #{ValidParentheses.longest_valid_parentheses('())()')} valid chars"
```

## System Design Questions

### LRU Cache Design
```ruby
# Question: Design an LRU (Least Recently Used) cache
class LRUCache
  def initialize(capacity)
    @capacity = capacity
    @cache = {}
    @order = []
  end

  def get(key)
    return -1 unless @cache.key?(key)
    
    # Move to end (most recently used)
    @order.delete(key)
    @order << key
    
    @cache[key]
  end

  def put(key, value)
    if @cache.key?(key)
      # Update existing key
      @cache[key] = value
      @order.delete(key)
      @order << key
    else
      # Add new key
      if @order.length >= @capacity
        # Remove least recently used
        lru_key = @order.shift
        @cache.delete(lru_key)
      end
      
      @cache[key] = value
      @order << key
    end
  end

  def size
    @cache.length
  end

  def to_array
    @order.map { |key| [key, @cache[key]] }
  end
end

# Usage example
cache = LRUCache.new(2)
cache.put(1, 1)
cache.put(2, 2)
puts "Get 1: #{cache.get(1)}"      # Returns 1
cache.put(3, 3)  # Evicts key 2
puts "Get 2: #{cache.get(2)}"      # Returns -1 (not found)
cache.put(4, 4)  # Evicts key 1
puts "Get 1: #{cache.get(1)}"      # Returns -1 (not found)
puts "Get 3: #{cache.get(3)}"      # Returns 3
puts "Get 4: #{cache.get(4)}"      # Returns 4
puts "Cache state: #{cache.to_array}"
```

### Design a Simple Hash Map
```ruby
# Question: Implement a basic hash map
class SimpleHashMap
  def initialize(capacity = 16)
    @capacity = capacity
    @buckets = Array.new(capacity) { [] }
    @size = 0
  end

  def put(key, value)
    index = hash(key)
    bucket = @buckets[index]
    
    # Check if key already exists
    bucket.each_with_index do |(existing_key, _), i|
      if existing_key == key
        bucket[i] = [key, value]
        return
      end
    end
    
    bucket << [key, value]
    @size += 1
  end

  def get(key)
    index = hash(key)
    bucket = @buckets[index]
    
    bucket.each do |existing_key, value|
      return value if existing_key == key
    end
    
    nil
  end

  def delete(key)
    index = hash(key)
    bucket = @buckets[index]
    
    bucket.each_with_index do |(existing_key, _), i|
      if existing_key == key
        bucket.delete_at(i)
        @size -= 1
        return true
      end
    end
    
    false
  end

  def contains_key?(key)
    !get(key).nil?
  end

  def size
    @size
  end

  def empty?
    @size == 0
  end

  def keys
    @buckets.flatten.select { |item| item.is_a?(String) || item.is_a?(Symbol) }
  end

  def to_array
    @buckets.flatten.each_slice(2).to_a
  end

  private

  def hash(key)
    key.hash % @capacity
  end
end

# Usage example
hash_map = SimpleHashMap.new
hash_map.put("name", "Ruby")
hash_map.put("version", "3.2")
hash_map.put("type", "Language")

puts "Hash Map Operations:"
puts "Get name: #{hash_map.get('name')}"
puts "Get version: #{hash_map.get('version')}"
puts "Contains 'type': #{hash_map.contains_key?('type')}"
puts "Contains 'unknown': #{hash_map.contains_key?('unknown')}"
puts "Size: #{hash_map.size}"

hash_map.delete("version")
puts "After delete version: #{hash_map.to_array}"
```

## Performance and Optimization

### Time Complexity Analysis
```ruby
# Question: Analyze and optimize time complexity
class ComplexityAnalysis
  def self.naive_fibonacci(n)
    return n if n <= 1
    naive_fibonacci(n - 1) + naive_fibonacci(n - 2)
  end

  def self.memoized_fibonacci(n, memo = {})
    return n if n <= 1
    return memo[n] if memo[n]
    
    memo[n] = memoized_fibonacci(n - 1, memo) + memoized_fibonacci(n - 2, memo)
  end

  def self.iterative_fibonacci(n)
    return n if n <= 1
    
    a, b = 0, 1
    (2..n).each do
      a, b = b, a + b
    end
    b
  end

  def self.compare_fibonacci_performance(n)
    require 'benchmark'
    
    results = {}
    
    # Test different implementations
    if n <= 35  # Naive is very slow for larger n
      results[:naive] = Benchmark.realtime { naive_fibonacci(n) }
    end
    
    results[:memoized] = Benchmark.realtime { memoized_fibonacci(n) }
    results[:iterative] = Benchmark.realtime { iterative_fibonacci(n) }
    
    results
  end

  def self.find_duplicates_naive(arr)
    duplicates = []
    
    (0...arr.length).each do |i|
      (i + 1...arr.length).each do |j|
        duplicates << arr[i] if arr[i] == arr[j] && !duplicates.include?(arr[i])
      end
    end
    
    duplicates
  end

  def self.find_duplicates_hash(arr)
    seen = Set.new
    duplicates = Set.new
    
    arr.each do |item|
      if seen.include?(item)
        duplicates.add(item)
      else
        seen.add(item)
      end
    end
    
    duplicates.to_a
  end

  def self.find_duplicates_sort(arr)
    return [] if arr.empty?
    
    sorted = arr.sort
    duplicates = []
    
    (1...sorted.length).each do |i|
      if sorted[i] == sorted[i - 1] && (i == sorted.length - 1 || sorted[i] != sorted[i + 1])
        duplicates << sorted[i]
      end
    end
    
    duplicates
  end

  def self.compare_duplicate_methods(arr)
    require 'benchmark'
    
    {
      array: arr,
      naive_time: Benchmark.realtime { find_duplicates_naive(arr) },
      hash_time: Benchmark.realtime { find_duplicates_hash(arr) },
      sort_time: Benchmark.realtime { find_duplicates_sort(arr) }
    }
  end
end

# Usage example
puts "\nFibonacci Performance:"
fib_results = ComplexityAnalysis.compare_fibonacci_performance(30)
fib_results.each { |method, time| puts "#{method}: #{time.round(4)}s" }

puts "\nDuplicate Finding Performance:"
test_array = (1..1000).to_a + [500, 600, 500, 700, 600] + [800, 900, 800]
dup_results = ComplexityAnalysis.compare_duplicate_methods(test_array)
dup_results.each { |method, result| puts "#{method}: #{result}" }
```

## Ruby-Specific Questions

### Ruby Garbage Collection
```ruby
# Question: How does Ruby's garbage collection work?
class GarbageCollectionExample
  def self.gc_demo
    puts "GC Demo:"
    puts "Before GC: #{GC.stat}"
    
    # Create some objects
    objects = Array.new(1000) { Object.new }
    
    puts "After creating objects: #{GC.stat}"
    
    # Force garbage collection
    GC.start
    
    puts "After GC: #{GC.stat}"
    
    # Clear reference
    objects = nil
    GC.start
    
    puts "After clearing and GC: #{GC.stat}"
  end

  def self.weak_ref_demo
    require 'weakref'
    
    obj = Object.new
    weak_ref = WeakRef.new(obj)
    
    puts "Weak ref exists: #{weak_ref.weakref_alive?}"
    
    obj = nil  # Remove strong reference
    GC.start
    
    puts "Weak ref after GC: #{weak_ref.weakref_alive?}"
    puts "Weak ref value: #{weak_ref.object rescue 'Reference lost'}"
  end

  def self.memory_leak_demo
    puts "Memory Leak Demo:"
    
    # Potential memory leak
    @cache = {}
    
    1000.times do |i|
      @cache["key_#{i}"] = "value_#{i}" * 1000
    end
    
    puts "Cache size: #{@cache.length}"
    puts "Memory usage: #{GC.stat[:heap_allocated_pages]}"
    
    # Clear cache
    @cache.clear
    GC.start
    
    puts "After clearing: #{GC.stat[:heap_allocated_pages]}"
  end

  def self.object_finalization
    class FinalizerDemo
      def initialize(name)
        @name = name
        ObjectSpace.define_finalizer(self, proc { |id| puts "#{@name} finalized" })
      end
    end
    
    obj1 = FinalizerDemo.new("Object 1")
    obj2 = FinalizerDemo.new("Object 2")
    
    obj1 = nil
    GC.start
    
    puts "After first GC:"
    
    obj2 = nil
    GC.start
    
    puts "After second GC:"
  end
end

# Usage examples
puts "\nGarbage Collection Examples:"
GarbageCollectionExample.gc_demo
GarbageCollectionExample.weak_ref_demo
GarbageCollectionExample.memory_leak_demo
GarbageCollectionExample.object_finalization
```

### Ruby Concurrency
```ruby
# Question: How does Ruby handle concurrency?
class ConcurrencyExample
  def self.thread_demo
    puts "Thread Demo:"
    
    threads = []
    results = []
    
    5.times do |i|
      threads << Thread.new do
        sleep(0.1)
        results << "Thread #{i} completed"
      end
    end
    
    threads.each(&:join)
    puts "Results: #{results}"
  end

  def self.mutex_demo
    puts "Mutex Demo:"
    
    counter = 0
    mutex = Mutex.new
    threads = []
    
    10.times do |i|
      threads << Thread.new do
        1000.times do
          mutex.synchronize do
            counter += 1
          end
        end
      end
    end
    
    threads.each(&:join)
    puts "Final counter: #{counter}"
  end

  def self.queue_demo
    require 'thread'
    
    puts "Queue Demo:"
    
    queue = Queue.new
    threads = []
    
    # Producer threads
    3.times do |i|
      threads << Thread.new do
        5.times do |j|
          queue << "Producer #{i}, item #{j}"
          sleep(0.01)
        end
      end
    end
    
    # Consumer thread
    consumer = Thread.new do
      while !queue.empty? || threads.any?(&:alive?)
        item = queue.pop(true) rescue nil
        puts "Consumed: #{item}" if item
        sleep(0.02)
      end
    end
    
    threads.each(&:join)
    queue << nil  # Signal consumer to stop
    consumer.join
  end

  def self.fiber_demo
    puts "Fiber Demo:"
    
    fiber1 = Fiber.new do
      5.times do |i|
        puts "Fiber 1: #{i}"
        Fiber.yield
      end
    end
    
    fiber2 = Fiber.new do
      5.times do |i|
        puts "Fiber 2: #{i}"
        Fiber.yield
      end
    end
    
    10.times do
      fiber1.resume if fiber1.alive?
      fiber2.resume if fiber2.alive?
    end
  end
end

# Usage examples
puts "\nConcurrency Examples:"
ConcurrencyExample.thread_demo
ConcurrencyExample.mutex_demo
ConcurrencyExample.queue_demo
ConcurrencyExample.fiber_demo
```

## Interview Tips

### Problem-Solving Framework
```ruby
class InterviewFramework
  def self.solve_problem(problem_description)
    puts "=== Interview Problem Solving Framework ==="
    puts "Problem: #{problem_description}"
    
    # Step 1: Understand the problem
    puts "\n1. Understanding:"
    puts "   - What are the inputs?"
    puts "   - What are the expected outputs?"
    puts "   - What are the constraints?"
    puts "   - What are the edge cases?"
    
    # Step 2: Plan the approach
    puts "\n2. Planning:"
    puts "   - Start with a brute force solution"
    puts "   - Identify bottlenecks"
    puts "   - Consider optimizations"
    puts "   - Choose data structures"
    
    # Step 3: Implement
    puts "\n3. Implementation:"
    puts "   - Write clean, readable code"
    puts "   - Add comments for complex logic"
    puts "   - Handle edge cases"
    puts "   - Test with examples"
    
    # Step 4: Analyze
    puts "\n4. Analysis:"
    puts "   - Time complexity: O(n) typical"
    puts "   - Space complexity: O(1) or O(n)"
    puts "   - Trade-offs considered"
    puts "   - Alternative approaches"
    
    puts "\nFramework complete!"
  end

  def self.communication_tips
    [
      "Think out loud during the interview",
      "Ask clarifying questions before coding",
      "Explain your approach before implementing",
      "Discuss time and space complexity",
      "Test your solution with examples",
      "Be open to feedback and suggestions",
      "Consider edge cases and error handling",
      "Discuss trade-offs and alternatives"
    ]
  end

  def self.common_mistakes
    [
      "Not asking clarifying questions",
      "Jumping into coding without planning",
      "Not considering edge cases",
      "Ignoring time/space complexity",
      "Not testing the solution",
      "Poor code readability",
      "Not communicating thought process",
      "Getting stuck on one approach"
    ]
  end
end

# Usage example
InterviewFramework.solve_problem("Find the kth largest element in an array")

puts "\nCommunication Tips:"
InterviewFramework.communication_tips.each_with_index { |tip, i| puts "#{i + 1}. #{tip}" }

puts "\nCommon Mistakes to Avoid:"
InterviewFramework.common_mistakes.each_with_index { |mistake, i| puts "#{i + 1}. #{mistake}" }
```

## Conclusion

Technical interviews test your understanding of fundamental concepts, problem-solving abilities, and programming skills. By practicing these questions and understanding the underlying concepts, you'll be well-prepared for Ruby technical interviews.

## Further Reading

- [Cracking the Coding Interview](https://www.careercup.com/book/)
- [Ruby Interview Questions](https://www.edureka.co/blog/ruby-interview-questions/)
- [LeetCode Ruby Solutions](https://leetcode.com/tag/ruby/)
- [System Design Interview](https://www.systemdesigninterview.com/)
- [Ruby Documentation](https://ruby-doc.org/)
