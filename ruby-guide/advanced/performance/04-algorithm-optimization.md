# Algorithm Optimization in Ruby
# Comprehensive guide to efficient algorithms and data structures

## 🎯 Overview

Algorithm optimization is crucial for building performant Ruby applications. This guide covers algorithmic complexity analysis, data structure selection, and optimization techniques for common problems.

## 📊 Algorithm Complexity Analysis

### 1. Big O Notation and Performance

Understanding algorithm complexity:

```ruby
class ComplexityAnalyzer
  def self.analyze_algorithms
    puts "Algorithm Complexity Analysis:"
    puts "=" * 50
    
    # Test different algorithms with varying input sizes
    sizes = [100, 1000, 5000, 10000]
    
    sizes.each do |size|
      puts "\nInput size: #{size}"
      puts "-" * 30
      
      data = (1..size).to_a
      
      # O(1) - Constant time
      o1_time = Benchmark.measure do
        1000.times { data[0] }
      end
      
      # O(n) - Linear time
      on_time = Benchmark.measure do
        data.each { |item| item * 2 }
      end
      
      # O(n log n) - Linearithmic time
      onlogn_time = Benchmark.measure do
        data.sort
      end
      
      # O(n²) - Quadratic time
      on2_time = Benchmark.measure do
        data.each do |i|
          data.each do |j|
            i + j
          end
        end
      end
      
      puts "O(1): #{o1_time.real.round(6)}s"
      puts "O(n): #{on_time.real.round(6)}s"
      puts "O(n log n): #{onlogn_time.real.round(6)}s"
      puts "O(n²): #{on2_time.real.round(6)}s"
    end
  end
  
  def self.compare_sorting_algorithms
    puts "\nSorting Algorithm Comparison:"
    puts "=" * 50
    
    # Generate test data
    sizes = [100, 1000, 5000]
    
    sizes.each do |size|
      data = (1..size).to_a.shuffle
      
      puts "\nArray size: #{size}"
      puts "-" * 30
      
      # Ruby's built-in sort (usually quicksort)
      ruby_sort_time = Benchmark.measure do
        data.sort
      end
      
      # Bubble sort (O(n²))
      bubble_sort_time = Benchmark.measure do
        bubble_sort(data.dup)
      end
      
      # Insertion sort (O(n²) average)
      insertion_sort_time = Benchmark.measure do
        insertion_sort(data.dup)
      end
      
      # Merge sort (O(n log n))
      merge_sort_time = Benchmark.measure do
        merge_sort(data.dup)
      end
      
      puts "Ruby sort: #{ruby_sort_time.real.round(6)}s"
      puts "Bubble sort: #{bubble_sort_time.real.round(6)}s"
      puts "Insertion sort: #{insertion_sort_time.real.round(6)}s"
      puts "Merge sort: #{merge_sort_time.real.round(6)}s"
    end
  end
  
  private
  
  def self.bubble_sort(array)
    n = array.length
    (n-1).times do |i|
      (n-i-1).times do |j|
        array[j], array[j+1] = array[j+1], array[j] if array[j] > array[j+1]
      end
    end
    array
  end
  
  def self.insertion_sort(array)
    (1...array.length).each do |i|
      key = array[i]
      j = i - 1
      
      while j >= 0 && array[j] > key
        array[j + 1] = array[j]
        j -= 1
      end
      
      array[j + 1] = key
    end
    array
  end
  
  def self.merge_sort(array)
    return array if array.length <= 1
    
    mid = array.length / 2
    left = merge_sort(array[0...mid])
    right = merge_sort(array[mid...array.length])
    
    merge(left, right)
  end
  
  def self.merge(left, right)
    result = []
    i = j = 0
    
    while i < left.length && j < right.length
      if left[i] <= right[j]
        result << left[i]
        i += 1
      else
        result << right[j]
        j += 1
      end
    end
    
    result.concat(left[i..-1]).concat(right[j..-1])
  end
end

# Run analysis
ComplexityAnalyzer.analyze_algorithms
ComplexityAnalyzer.compare_sorting_algorithms
```

### 2. Performance Profiling

Profile algorithm performance:

```ruby
class AlgorithmProfiler
  def self.profile_search_algorithms
    puts "Search Algorithm Profiling:"
    puts "=" * 50
    
    # Create sorted data
    data = (1..10000).to_a
    targets = [1, 5000, 10000, 15000]  # Include non-existent target
    
    targets.each do |target|
      puts "\nSearching for: #{target}"
      puts "-" * 30
      
      # Linear search (O(n))
      linear_time = Benchmark.measure do
        linear_search(data, target)
      end
      
      # Binary search (O(log n))
      binary_time = Benchmark.measure do
        binary_search(data, target)
      end
      
      # Hash lookup (O(1))
      hash_data = data.each_with_index.to_h
      hash_time = Benchmark.measure do
        hash_data[target]
      end
      
      puts "Linear search: #{linear_time.real.round(6)}s"
      puts "Binary search: #{binary_time.real.round(6)}s"
      puts "Hash lookup: #{hash_time.real.round(6)}s"
    end
  end
  
  def self.profile_data_structures
    puts "\nData Structure Performance:"
    puts "=" * 50
    
    operations = [:insert, :lookup, :delete]
    sizes = [1000, 5000, 10000]
    
    sizes.each do |size|
      puts "\nData size: #{size}"
      puts "-" * 30
      
      # Array performance
      array = []
      array_insert_time = Benchmark.measure do
        size.times { |i| array << i }
      end
      
      array_lookup_time = Benchmark.measure do
        size.times { |i| array.include?(size / 2) }
      end
      
      # Hash performance
      hash = {}
      hash_insert_time = Benchmark.measure do
        size.times { |i| hash[i] = "value_#{i}" }
      end
      
      hash_lookup_time = Benchmark.measure do
        size.times { |i| hash[size / 2] }
      end
      
      # Set performance
      set = Set.new
      set_insert_time = Benchmark.measure do
        size.times { |i| set << i }
      end
      
      set_lookup_time = Benchmark.measure do
        size.times { |i| set.include?(size / 2) }
      end
      
      puts "Array insert: #{array_insert_time.real.round(6)}s"
      puts "Array lookup: #{array_lookup_time.real.round(6)}s"
      puts "Hash insert: #{hash_insert_time.real.round(6)}s"
      puts "Hash lookup: #{hash_lookup_time.real.round(6)}s"
      puts "Set insert: #{set_insert_time.real.round(6)}s"
      puts "Set lookup: #{set_lookup_time.real.round(6)}s"
    end
  end
  
  private
  
  def self.linear_search(array, target)
    array.each { |item| return item if item == target }
    nil
  end
  
  def self.binary_search(array, target)
    low = 0
    high = array.length - 1
    
    while low <= high
      mid = (low + high) / 2
      return array[mid] if array[mid] == target
      
      if array[mid] < target
        low = mid + 1
      else
        high = mid - 1
      end
    end
    
    nil
  end
end

# Run profiling
AlgorithmProfiler.profile_search_algorithms
AlgorithmProfiler.profile_data_structures
```

## 🚀 Algorithm Optimization Techniques

### 1. Memoization

Cache results of expensive computations:

```ruby
class MemoizationOptimizer
  def self.fibonacci_memoization
    puts "Fibonacci Memoization:"
    puts "=" * 40
    
    # Without memoization
    def fibonacci_naive(n)
      return n if n <= 1
      fibonacci_naive(n - 1) + fibonacci_naive(n - 2)
    end
    
    # With memoization
    @fib_cache = {}
    def fibonacci_memoized(n)
      return n if n <= 1
      return @fib_cache[n] if @fib_cache[n]
      
      @fib_cache[n] = fibonacci_memoized(n - 1) + fibonacci_memoized(n - 2)
    end
    
    # Test performance
    n = 35
    
    naive_time = Benchmark.measure do
      fibonacci_naive(n)
    end
    
    memoized_time = Benchmark.measure do
      fibonacci_memoized(n)
    end
    
    puts "Naive Fibonacci (n=#{n}): #{naive_time.real.round(6)}s"
    puts "Memoized Fibonacci (n=#{n}): #{memoized_time.real.round(6)}s"
    puts "Speedup: #{(naive_time.real / memoized_time.real).round(2)}x"
  end
  
  def self.general_memoization
    puts "\nGeneral Memoization:"
    puts "=" * 40
    
    # Memoization module
    module Memoizable
      def memoize(method_name)
        original_method = instance_method(method_name)
        cache = {}
        
        define_method(method_name) do |*args|
          cache[args] ||= original_method.bind(self).call(*args)
        end
      end
    end
    
    class ExpensiveCalculator
      extend Memoizable
      
      def expensive_calculation(x, y)
        # Simulate expensive computation
        sleep(0.01)
        x ** y
      end
      
      memoize :expensive_calculation
    end
    
    calculator = ExpensiveCalculator.new
    
    # Test memoization
    time_without_memo = Benchmark.measure do
      5.times { calculator.expensive_calculation(2, 10) }
    end
    
    # Clear cache to simulate without memoization
    calculator.instance_variable_set(:@expensive_calculation_cache, {})
    
    time_with_memo = Benchmark.measure do
      5.times { calculator.expensive_calculation(2, 10) }
    end
    
    puts "Without memoization: #{time_without_memo.real.round(6)}s"
    puts "With memoization: #{time_with_memo.real.round(6)}s"
    puts "Speedup: #{(time_without_memo.real / time_with_memo.real).round(2)}x"
  end
  
  def self.lru_cache
    puts "\nLRU Cache Implementation:"
    puts "=" * 40
    
    class LRUCache
      def initialize(capacity)
        @capacity = capacity
        @cache = {}
        @order = []
      end
      
      def get(key)
        if @cache.key?(key)
          # Move to end (most recently used)
          @order.delete(key)
          @order << key
          @cache[key]
        else
          nil
        end
      end
      
      def put(key, value)
        if @cache.key?(key)
          # Update existing
          @cache[key] = value
          @order.delete(key)
          @order << key
        else
          # Add new
          if @cache.size >= @capacity
            # Remove least recently used
            lru = @order.shift
            @cache.delete(lru)
          end
          
          @cache[key] = value
          @order << key
        end
      end
      
      def size
        @cache.size
      end
    end
    
    # Test LRU cache
    cache = LRUCache.new(3)
    
    cache.put(1, "one")
    cache.put(2, "two")
    cache.put(3, "three")
    
    puts "Cache size: #{cache.size}"
    puts "Get 1: #{cache.get(1)}"
    puts "Get 2: #{cache.get(2)}"
    
    cache.put(4, "four")  # Should evict 3 (least recently used)
    
    puts "Cache size after adding 4: #{cache.size}"
    puts "Get 3: #{cache.get(3)}"  # Should be nil (evicted)
    puts "Get 4: #{cache.get(4)}"  # Should be "four"
  end
end

# Run memoization examples
MemoizationOptimizer.fibonacci_memoization
MemoizationOptimizer.general_memoization
MemoizationOptimizer.lru_cache
```

### 2. Dynamic Programming

Optimize recursive problems with dynamic programming:

```ruby
class DynamicProgrammingOptimizer
  def self.knapsack_problem
    puts "Knapsack Problem - Dynamic Programming:"
    puts "=" * 50
    
    # 0/1 Knapsack problem
    def knapsack_recursive(weights, values, capacity, n)
      return 0 if n == 0 || capacity == 0
      return 0 if weights[n - 1] > capacity
      
      # Include item
      include = values[n - 1] + knapsack_recursive(weights, values, capacity - weights[n - 1], n - 1)
      
      # Exclude item
      exclude = knapsack_recursive(weights, values, capacity, n - 1)
      
      [include, exclude].max
    end
    
    def knapsack_dp(weights, values, capacity)
      n = weights.length
      dp = Array.new(n + 1) { Array.new(capacity + 1, 0) }
      
      (1..n).each do |i|
        (0..capacity).each do |w|
          if weights[i - 1] <= w
            dp[i][w] = [values[i - 1] + dp[i - 1][w - weights[i - 1]], dp[i - 1][w]].max
          else
            dp[i][w] = dp[i - 1][w]
          end
        end
      end
      
      dp[n][capacity]
    end
    
    # Test data
    weights = [10, 20, 30]
    values = [60, 100, 120]
    capacity = 50
    
    # Compare performance
    recursive_time = Benchmark.measure do
      knapsack_recursive(weights, values, capacity, weights.length)
    end
    
    dp_time = Benchmark.measure do
      knapsack_dp(weights, values, capacity)
    end
    
    puts "Recursive solution: #{recursive_time.real.round(6)}s"
    puts "DP solution: #{dp_time.real.round(6)}s"
    puts "Speedup: #{(recursive_time.real / dp_time.real).round(2)}x"
    
    # Results
    recursive_result = knapsack_recursive(weights, values, capacity, weights.length)
    dp_result = knapsack_dp(weights, values, capacity)
    
    puts "Recursive result: #{recursive_result}"
    puts "DP result: #{dp_result}"
  end
  
  def self.longest_common_subsequence
    puts "\nLongest Common Subsequence:"
    puts "=" * 50
    
    def lcs_recursive(str1, str2, m, n)
      return 0 if m == 0 || n == 0
      
      if str1[m - 1] == str2[n - 1]
        1 + lcs_recursive(str1, str2, m - 1, n - 1)
      else
        [lcs_recursive(str1, str2, m - 1, n),
         lcs_recursive(str1, str2, m, n - 1)].max
      end
    end
    
    def lcs_dp(str1, str2)
      m = str1.length
      n = str2.length
      
      dp = Array.new(m + 1) { Array.new(n + 1, 0) }
      
      (1..m).each do |i|
        (1..n).each do |j|
          if str1[i - 1] == str2[j - 1]
            dp[i][j] = dp[i - 1][j - 1] + 1
          else
            dp[i][j] = [dp[i - 1][j], dp[i][j - 1]].max
          end
        end
      end
      
      dp[m][n]
    end
    
    # Test strings
    str1 = "AGGTAB"
    str2 = "GXTXAYB"
    
    # Compare performance
    recursive_time = Benchmark.measure do
      lcs_recursive(str1, str2, str1.length, str2.length)
    end
    
    dp_time = Benchmark.measure do
      lcs_dp(str1, str2)
    end
    
    puts "Recursive LCS: #{recursive_time.real.round(6)}s"
    puts "DP LCS: #{dp_time.real.round(6)}s"
    
    # Results
    recursive_result = lcs_recursive(str1, str2, str1.length, str2.length)
    dp_result = lcs_dp(str1, str2)
    
    puts "Recursive result: #{recursive_result}"
    puts "DP result: #{dp_result}"
  end
  
  def self.coin_change
    puts "\nCoin Change Problem:"
    puts "=" * 50
    
    def coin_change_recursive(coins, amount, index)
      return 1 if amount == 0
      return 0 if amount < 0 || index >= coins.length
      
      # Include current coin
      include = coin_change_recursive(coins, amount - coins[index], index)
      
      # Exclude current coin
      exclude = coin_change_recursive(coins, amount, index + 1)
      
      include + exclude
    end
    
    def coin_change_dp(coins, amount)
      dp = Array.new(amount + 1, 0)
      dp[0] = 1
      
      (1..amount).each do |i|
        coins.each do |coin|
          dp[i] += dp[i - coin] if i >= coin
        end
      end
      
      dp[amount]
    end
    
    # Test data
    coins = [1, 2, 5]
    amount = 5
    
    # Compare performance
    recursive_time = Benchmark.measure do
      coin_change_recursive(coins, amount, 0)
    end
    
    dp_time = Benchmark.measure do
      coin_change_dp(coins, amount)
    end
    
    puts "Recursive coin change: #{recursive_time.real.round(6)}s"
    puts "DP coin change: #{dp_time.real.round(6)}s"
    
    # Results
    recursive_result = coin_change_recursive(coins, amount, 0)
    dp_result = coin_change_dp(coins, amount)
    
    puts "Recursive result: #{recursive_result}"
    puts "DP result: #{dp_result}"
  end
end

# Run dynamic programming examples
DynamicProgrammingOptimizer.knapsack_problem
DynamicProgrammingOptimizer.longest_common_subsequence
DynamicProgrammingOptimizer.coin_change
```

### 3. Greedy Algorithms

Optimize with greedy approaches:

```ruby
class GreedyAlgorithmOptimizer
  def self.activity_selection
    puts "Activity Selection Problem:"
    puts "=" * 40
    
    # Activity selection problem
    def activity_selection(start_times, finish_times)
      n = start_times.length
      
      # Create activities array
      activities = (0...n).map do |i|
        { start: start_times[i], finish: finish_times[i], index: i }
      end
      
      # Sort by finish time
      activities.sort_by! { |activity| activity[:finish] }
      
      # Select activities
      selected = []
      last_finish = -1
      
      activities.each do |activity|
        if activity[:start] >= last_finish
          selected << activity
          last_finish = activity[:finish]
        end
      end
      
      selected
    end
    
    # Test data
    start_times = [1, 3, 0, 5, 8, 5]
    finish_times = [2, 4, 6, 7, 9, 9]
    
    selected_activities = activity_selection(start_times, finish_times)
    
    puts "Selected activities:"
    selected_activities.each do |activity|
      puts "  Activity #{activity[:index]}: #{activity[:start]} - #{activity[:finish]}"
    end
    
    puts "Maximum activities: #{selected_activities.length}"
  end
  
  def self.huffman_coding
    puts "\nHuffman Coding:"
    puts "=" * 40
    
    # Huffman tree node
    class HuffmanNode
      attr_accessor :char, :frequency, :left, :right
      
      def initialize(char, frequency)
        @char = char
        @frequency = frequency
        @left = nil
        @right = nil
      end
      
      def leaf?
        @char && @left.nil? && @right.nil?
      end
    end
    
    def build_huffman_tree(characters, frequencies)
      # Create leaf nodes
      nodes = characters.zip(frequencies).map { |char, freq| HuffmanNode.new(char, freq) }
      
      # Build tree
      while nodes.length > 1
        # Sort by frequency
        nodes.sort_by!(&:frequency)
        
        # Take two nodes with lowest frequency
        left = nodes.shift
        right = nodes.shift
        
        # Create internal node
        combined = HuffmanNode.new(nil, left.frequency + right.frequency)
        combined.left = left
        combined.right = right
        
        nodes.unshift(combined)
      end
      
      nodes.first
    end
    
    def generate_codes(root, current_code = "", codes = {})
      return codes unless root
      
      if root.leaf?
        codes[root.char] = current_code
      else
        generate_codes(root.left, current_code + "0", codes)
        generate_codes(root.right, current_code + "1", codes)
      end
      
      codes
    end
    
    # Test data
    characters = ['a', 'b', 'c', 'd', 'e', 'f']
    frequencies = [5, 9, 12, 13, 16, 45]
    
    # Build Huffman tree
    root = build_huffman_tree(characters, frequencies)
    
    # Generate codes
    codes = generate_codes(root)
    
    puts "Huffman codes:"
    codes.each do |char, code|
      freq = frequencies[characters.index(char)]
      puts "  #{char}: #{code} (frequency: #{freq})"
    end
    
    # Calculate compression ratio
    original_bits = characters.length * 8  # 8 bits per character
    compressed_bits = frequencies.zip(codes.values).sum { |freq, code| freq * code.length }
    
    puts "\nOriginal bits: #{original_bits}"
    puts "Compressed bits: #{compressed_bits}"
    puts "Compression ratio: #{(compressed_bits.to_f / original_bits * 100).round(2)}%"
  end
  
  def self.kruskal_mst
    puts "\nKruskal's Minimum Spanning Tree:"
    puts "=" * 40
    
    # Disjoint set for cycle detection
    class DisjointSet
      def initialize(n)
        @parent = (0...n).to_a
        @rank = Array.new(n, 0)
      end
      
      def find(x)
        return x if @parent[x] == x
        @parent[x] = find(@parent[x])
      end
      
      def union(x, y)
        x_root = find(x)
        y_root = find(y)
        
        return if x_root == y_root
        
        if @rank[x_root] < @rank[y_root]
          @parent[x_root] = y_root
        elsif @rank[x_root] > @rank[y_root]
          @parent[y_root] = x_root
        else
          @parent[y_root] = x_root
          @rank[x_root] += 1
        end
      end
    end
    
    def kruskal_mst(edges, vertices)
      # Sort edges by weight
      edges.sort_by! { |edge| edge[:weight] }
      
      # Initialize disjoint set
      ds = DisjointSet.new(vertices)
      
      mst = []
      total_weight = 0
      
      edges.each do |edge|
        u, v = edge[:u], edge[:v]
        
        # Check if adding this edge creates a cycle
        if ds.find(u) != ds.find(v)
          mst << edge
          total_weight += edge[:weight]
          ds.union(u, v)
          
          break if mst.length == vertices - 1
        end
      end
      
      [mst, total_weight]
    end
    
    # Test data
    edges = [
      { u: 0, v: 1, weight: 4 },
      { u: 0, v: 2, weight: 4 },
      { u: 1, v: 2, weight: 2 },
      { u: 1, v: 3, weight: 6 },
      { u: 2, v: 3, weight: 8 },
      { u: 2, v: 4, weight: 9 },
      { u: 3, v: 4, weight: 7 },
      { u: 3, v: 5, weight: 9 },
      { u: 4, v: 5, weight: 10 },
      { u: 2, v: 5, weight: 3 }
    ]
    
    vertices = 6
    
    mst, total_weight = kruskal_mst(edges, vertices)
    
    puts "Minimum Spanning Tree edges:"
    mst.each do |edge|
      puts "  #{edge[:u]} - #{edge[:v]} (weight: #{edge[:weight]})"
    end
    
    puts "Total weight: #{total_weight}"
  end
end

# Run greedy algorithm examples
GreedyAlgorithmOptimizer.activity_selection
GreedyAlgorithmOptimizer.huffman_coding
GreedyAlgorithmOptimizer.kruskal_mst
```

## 🎯 Data Structure Optimization

### 1. Custom Data Structures

Optimized data structures for specific use cases:

```ruby
class DataStructureOptimizer
  def self.trie_implementation
    puts "Trie (Prefix Tree) Implementation:"
    puts "=" * 40
    
    class TrieNode
      def initialize
        @children = {}
        @is_end_of_word = false
      end
      
      attr_accessor :children, :is_end_of_word
    end
    
    class Trie
      def initialize
        @root = TrieNode.new
      end
      
      def insert(word)
        node = @root
        
        word.each_char do |char|
          node.children[char] = TrieNode.new unless node.children[char]
          node = node.children[char]
        end
        
        node.is_end_of_word = true
      end
      
      def search(word)
        node = @root
        
        word.each_char do |char|
          return false unless node.children[char]
          node = node.children[char]
        end
        
        node.is_end_of_word
      end
      
      def starts_with(prefix)
        node = @root
        
        prefix.each_char do |char|
          return false unless node.children[char]
          node = node.children[char]
        end
        
        true
      end
      
      def get_all_words(prefix = "")
        words = []
        node = @root
        
        # Navigate to prefix
        prefix.each_char do |char|
          return words unless node.children[char]
          node = node.children[char]
        end
        
        # Collect all words from this node
        collect_words(node, prefix, words)
        words
      end
      
      private
      
      def collect_words(node, prefix, words)
        if node.is_end_of_word
          words << prefix
        end
        
        node.children.each do |char, child|
          collect_words(child, prefix + char, words)
        end
      end
    end
    
    # Test trie
    trie = Trie.new
    
    words = ["apple", "app", "application", "apt", "bat", "ball"]
    words.each { |word| trie.insert(word) }
    
    puts "Search results:"
    puts "apple: #{trie.search("apple")}"
    puts "app: #{trie.search("app")}"
    puts "appl: #{trie.search("appl")}"
    puts "bat: #{trie.search("bat")}"
    puts "ball: #{trie.search("ball")}"
    
    puts "\nPrefix searches:"
    puts "Words starting with 'app': #{trie.get_all_words("app")}"
    puts "Words starting with 'b': #{trie.get_all_words("b")}"
  end
  
  def self.priority_queue_implementation
    puts "\nPriority Queue Implementation:"
    puts "=" * 40
    
    class PriorityQueue
      def initialize
        @heap = []
      end
      
      def push(element, priority)
        @heap << { element: element, priority: priority }
        bubble_up(@heap.length - 1)
      end
      
      def pop
        return nil if @heap.empty?
        
        min = @heap[0]
        last = @heap.pop
        
        unless @heap.empty?
          @heap[0] = last
          bubble_down(0)
        end
        
        min[:element]
      end
      
      def peek
        return nil if @heap.empty?
        @heap[0][:element]
      end
      
      def empty?
        @heap.empty?
      end
      
      def size
        @heap.length
      end
      
      private
      
      def bubble_up(index)
        return if index == 0
        
        parent = (index - 1) / 2
        
        if @heap[parent][:priority] > @heap[index][:priority]
          @heap[parent], @heap[index] = @heap[index], @heap[parent]
          bubble_up(parent)
        end
      end
      
      def bubble_down(index)
        left_child = 2 * index + 1
        right_child = 2 * index + 2
        smallest = index
        
        if left_child < @heap.length && @heap[left_child][:priority] < @heap[smallest][:priority]
          smallest = left_child
        end
        
        if right_child < @heap.length && @heap[right_child][:priority] < @heap[smallest][:priority]
          smallest = right_child
        end
        
        if smallest != index
          @heap[smallest], @heap[index] = @heap[index], @heap[smallest]
          bubble_down(smallest)
        end
      end
    end
    
    # Test priority queue
    pq = PriorityQueue.new
    
    # Insert elements with priorities
    pq.push("Task 1", 3)
    pq.push("Task 2", 1)
    pq.push("Task 3", 2)
    pq.push("Task 4", 5)
    pq.push("Task 5", 4)
    
    puts "Priority queue operations:"
    puts "Size: #{pq.size}"
    puts "Peek: #{pq.peek}"
    
    puts "\nPop elements (in priority order):"
    until pq.empty?
      element = pq.pop
      puts "Popped: #{element}"
    end
  end
  
  def self.lru_cache_implementation
    puts "\nLRU Cache Implementation:"
    puts "=" * 40
    
    class LRUCache
      def initialize(capacity)
        @capacity = capacity
        @cache = {}
        @order = []
      end
      
      def get(key)
        if @cache.key?(key)
          # Move to end (most recently used)
          @order.delete(key)
          @order << key
          @cache[key]
        else
          nil
        end
      end
      
      def put(key, value)
        if @cache.key?(key)
          # Update existing
          @cache[key] = value
          @order.delete(key)
          @order << key
        else
          # Add new
          if @cache.size >= @capacity
            # Remove least recently used
            lru = @order.shift
            @cache.delete(lru)
          end
          
          @cache[key] = value
          @order << key
        end
      end
      
      def size
        @cache.size
      end
      
      def to_s
        "Cache: #{@cache}, Order: #{@order}"
      end
    end
    
    # Test LRU cache
    cache = LRUCache.new(3)
    
    cache.put(1, "one")
    cache.put(2, "two")
    cache.put(3, "three")
    
    puts "Cache after 3 insertions: #{cache}"
    puts "Get 1: #{cache.get(1)}"
    puts "Cache after get 1: #{cache}"
    
    cache.put(4, "four")  # Should evict 2
    puts "Cache after adding 4: #{cache}"
    
    puts "Get 2: #{cache.get(2)}"  # Should be nil
    puts "Get 3: #{cache.get(3)}"  # Should be "three"
    puts "Get 4: #{cache.get(4)}"  # Should be "four"
  end
end

# Run data structure examples
DataStructureOptimizer.trie_implementation
DataStructureOptimizer.priority_queue_implementation
DataStructureOptimizer.lru_cache_implementation
```

## 🎓 Exercises

### Beginner Exercises

1. **Complexity Analysis**: Analyze algorithm complexity
2. **Memoization**: Implement memoization for expensive functions
3. **Sorting Algorithms**: Compare different sorting approaches

### Intermediate Exercises

1. **Dynamic Programming**: Solve DP problems
2. **Greedy Algorithms**: Implement greedy solutions
3. **Data Structures**: Build custom data structures

### Advanced Exercises

1. **Algorithm Optimization**: Optimize existing algorithms
2. **Performance Profiling**: Profile and optimize algorithms
3. **Real-world Problems**: Solve complex algorithmic problems

---

## 🎯 Summary

Algorithm optimization in Ruby provides:

- **Complexity Analysis** - Understanding algorithm performance
- **Memoization** - Caching expensive computations
- **Dynamic Programming** - Optimizing recursive problems
- **Greedy Algorithms** - Efficient problem-solving approaches
- **Data Structure Optimization** - Custom structures for specific needs

Master these techniques to build efficient, high-performance Ruby applications!
