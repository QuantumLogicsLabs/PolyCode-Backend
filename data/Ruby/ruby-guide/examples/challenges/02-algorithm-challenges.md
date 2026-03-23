# Algorithm Challenges in Ruby

## Overview

Algorithm challenges are excellent for improving problem-solving skills, understanding data structures, and mastering Ruby's capabilities. This guide covers various algorithmic problems with Ruby solutions, from basic to advanced levels.

## Sorting Algorithms

### Quick Sort Implementation
```ruby
class QuickSort
  def self.sort(array)
    return array if array.length <= 1
    
    pivot = array[array.length / 2]
    left = array.select { |x| x < pivot }
    middle = array.select { |x| x == pivot }
    right = array.select { |x| x > pivot }
    
    sort(left) + middle + sort(right)
  end

  def self.sort_inplace!(array, low = 0, high = array.length - 1)
    return if low >= high
    
    partition_index = partition(array, low, high)
    sort_inplace!(array, low, partition_index - 1)
    sort_inplace!(array, partition_index + 1, high)
  end

  private

  def self.partition(array, low, high)
    pivot = array[high]
    i = low - 1
    
    (low...high).each do |j|
      if array[j] <= pivot
        i += 1
        array[i], array[j] = array[j], array[i]
      end
    end
    
    array[i + 1], array[high] = array[high], array[i + 1]
    i + 1
  end
end

# Usage example
array = [64, 34, 25, 12, 22, 11, 90]
puts "Original: #{array}"
puts "Sorted: #{QuickSort.sort(array)}"

# In-place version
array2 = [64, 34, 25, 12, 22, 11, 90]
QuickSort.sort_inplace!(array2)
puts "In-place sorted: #{array2}"
```

### Merge Sort Implementation
```ruby
class MergeSort
  def self.sort(array)
    return array if array.length <= 1
    
    mid = array.length / 2
    left = sort(array[0...mid])
    right = sort(array[mid...array.length])
    
    merge(left, right)
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
array = [38, 27, 43, 3, 9, 82, 10]
puts "Original: #{array}"
puts "Sorted: #{MergeSort.sort(array)}"
```

## Search Algorithms

### Binary Search
```ruby
class BinarySearch
  def self.search(array, target)
    low = 0
    high = array.length - 1
    
    while low <= high
      mid = (low + high) / 2
      
      if array[mid] == target
        return mid
      elsif array[mid] < target
        low = mid + 1
      else
        high = mid - 1
      end
    end
    
    -1  # Not found
  end

  def self.search_recursive(array, target, low = 0, high = array.length - 1)
    return -1 if low > high
    
    mid = (low + high) / 2
    
    if array[mid] == target
      mid
    elsif array[mid] < target
      search_recursive(array, target, mid + 1, high)
    else
      search_recursive(array, target, low, mid - 1)
    end
  end
end

# Usage example
sorted_array = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19]
target = 13
index = BinarySearch.search(sorted_array, target)
puts "Found #{target} at index #{index}"

# Recursive version
index2 = BinarySearch.search_recursive(sorted_array, target)
puts "Found #{target} at index #{index2} (recursive)"
```

### Depth-First Search (DFS)
```ruby
class Graph
  def initialize
    @adjacency_list = {}
  end

  def add_edge(vertex, neighbor)
    @adjacency_list[vertex] ||= []
    @adjacency_list[vertex] << neighbor
  end

  def dfs(start_vertex)
    visited = Set.new
    result = []
    
    dfs_recursive(start_vertex, visited, result)
    result
  end

  def dfs_iterative(start_vertex)
    visited = Set.new
    stack = [start_vertex]
    result = []
    
    while stack.any?
      vertex = stack.pop
      
      unless visited.include?(vertex)
        visited.add(vertex)
        result << vertex
        
        # Add neighbors to stack (reverse for correct order)
        neighbors = @adjacency_list[vertex] || []
        stack.concat(neighbors.reverse)
      end
    end
    
    result
  end

  private

  def dfs_recursive(vertex, visited, result)
    return if visited.include?(vertex)
    
    visited.add(vertex)
    result << vertex
    
    (@adjacency_list[vertex] || []).each do |neighbor|
      dfs_recursive(neighbor, visited, result)
    end
  end
end

# Usage example
graph = Graph.new
graph.add_edge('A', 'B')
graph.add_edge('A', 'C')
graph.add_edge('B', 'D')
graph.add_edge('B', 'E')
graph.add_edge('C', 'F')
graph.add_edge('E', 'F')

puts "DFS (recursive): #{graph.dfs('A')}"
puts "DFS (iterative): #{graph.dfs_iterative('A')}"
```

## Dynamic Programming

### Fibonacci Sequence
```ruby
class Fibonacci
  def self.naive(n)
    return n if n <= 1
    naive(n - 1) + naive(n - 2)
  end

  def self.memoized(n, memo = {})
    return n if n <= 1
    return memo[n] if memo.key?(n)
    
    memo[n] = memoized(n - 1, memo) + memoized(n - 2, memo)
    memo[n]
  end

  def self.bottom_up(n)
    return n if n <= 1
    
    fib = [0, 1]
    
    (2..n).each do |i|
      fib[i] = fib[i - 1] + fib[i - 2]
    end
    
    fib[n]
  end

  def self.space_optimized(n)
    return n if n <= 1
    
    a, b = 0, 1
    
    (2..n).each do
      a, b = b, a + b
    end
    
    b
  end
end

# Usage example
n = 10
puts "Fibonacci #{n}:"
puts "  Naive: #{Fibonacci.naive(n)}"
puts "  Memoized: #{Fibonacci.memoized(n)}"
puts "  Bottom-up: #{Fibonacci.bottom_up(n)}"
puts "  Space optimized: #{Fibonacci.space_optimized(n)}"
```

### Longest Common Subsequence
```ruby
class LongestCommonSubsequence
  def self.lcs_recursive(str1, str2, m = nil, n = nil)
    m ||= str1.length
    n ||= str2.length
    
    return 0 if m == 0 || n == 0
    
    if str1[m - 1] == str2[n - 1]
      1 + lcs_recursive(str1, str2, m - 1, n - 1)
    else
      [lcs_recursive(str1, str2, m - 1, n),
       lcs_recursive(str1, str2, m, n - 1)].max
    end
  end

  def self.lcs_memoized(str1, str2)
    m, n = str1.length, str2.length
    memo = Array.new(m + 1) { Array.new(n + 1) }
    
    lcs_recursive_helper(str1, str2, m, n, memo)
  end

  def self.lcs_bottom_up(str1, str2)
    m, n = str1.length, str2.length
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

  def self.lcs_sequence(str1, str2)
    m, n = str1.length, str2.length
    dp = Array.new(m + 1) { Array.new(n + 1, 0) }
    
    # Fill DP table
    (1..m).each do |i|
      (1..n).each do |j|
        if str1[i - 1] == str2[j - 1]
          dp[i][j] = dp[i - 1][j - 1] + 1
        else
          dp[i][j] = [dp[i - 1][j], dp[i][j - 1]].max
        end
      end
    end
    
    # Reconstruct sequence
    sequence = []
    i, j = m, n
    
    while i > 0 && j > 0
      if str1[i - 1] == str2[j - 1]
        sequence.unshift(str1[i - 1])
        i -= 1
        j -= 1
      elsif dp[i - 1][j] >= dp[i][j - 1]
        i -= 1
      else
        j -= 1
      end
    end
    
    sequence.join
  end

  private

  def self.lcs_recursive_helper(str1, str2, m, n, memo)
    return 0 if m == 0 || n == 0
    return memo[m][n] if memo[m][n]
    
    if str1[m - 1] == str2[n - 1]
      memo[m][n] = 1 + lcs_recursive_helper(str1, str2, m - 1, n - 1, memo)
    else
      memo[m][n] = [
        lcs_recursive_helper(str1, str2, m - 1, n, memo),
        lcs_recursive_helper(str1, str2, m, n - 1, memo)
      ].max
    end
    
    memo[m][n]
  end
end

# Usage example
str1 = "AGGTAB"
str2 = "GXTXAYB"

puts "LCS length: #{LongestCommonSubsequence.lcs_bottom_up(str1, str2)}"
puts "LCS sequence: #{LongestCommonSubsequence.lcs_sequence(str1, str2)}"
```

## Data Structure Challenges

### Stack Implementation
```ruby
class Stack
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

  def to_s
    @elements.to_s
  end
end

# Stack-based palindrome checker
class PalindromeChecker
  def self.palindrome?(string)
    stack = Stack.new
    
    # Push first half of characters
    (string.length / 2).times do |i|
      stack.push(string[i])
    end
    
    # Skip middle character for odd-length strings
    start_index = (string.length / 2.0).ceil
    
    # Compare second half with stack
    (start_index...string.length).each do |i|
      return false if stack.pop != string[i]
    end
    
    true
  end
end

# Usage examples
stack = Stack.new
stack.push(1)
stack.push(2)
stack.push(3)
puts "Stack: #{stack}"
puts "Pop: #{stack.pop}"
puts "Peek: #{stack.peek}"

puts "Is 'racecar' a palindrome? #{PalindromeChecker.palindrome?('racecar')}"
puts "Is 'hello' a palindrome? #{PalindromeChecker.palindrome?('hello')}"
```

### Queue Implementation
```ruby
class Queue
  def initialize
    @elements = []
  end

  def enqueue(element)
    @elements.push(element)
  end

  def dequeue
    @elements.shift
  end

  def front
    @elements.first
  end

  def empty?
    @elements.empty?
  end

  def size
    @elements.length
  end
end

# Queue-based task scheduler
class TaskScheduler
  def initialize
    @queue = Queue.new
  end

  def add_task(task, priority = 1)
    @queue.enqueue({ task: task, priority: priority, added_at: Time.now })
  end

  def process_tasks
    while !@queue.empty?
      task_info = @queue.dequeue
      puts "Processing task: #{task_info[:task]} (Priority: #{task_info[:priority]})"
      
      # Simulate task processing
      sleep(0.1)
    end
  end

  def priority_process_tasks
    # Sort by priority (higher priority first)
    tasks = []
    tasks << @queue.dequeue until @queue.empty?
    
    tasks.sort_by { |task| -task[:priority] }.each do |task|
      puts "Processing priority task: #{task[:task]} (Priority: #{task[:priority]})"
      sleep(0.1)
    end
  end
end

# Usage examples
queue = Queue.new
queue.enqueue(1)
queue.enqueue(2)
queue.enqueue(3)
puts "Queue front: #{queue.front}"
puts "Dequeue: #{queue.dequeue}"

scheduler = TaskScheduler.new
scheduler.add_task("Task 1", 2)
scheduler.add_task("Task 2", 1)
scheduler.add_task("Task 3", 3)
scheduler.priority_process_tasks
```

## Mathematical Algorithms

### Prime Number Generation
```ruby
class PrimeNumbers
  def self.is_prime?(n)
    return false if n <= 1
    return true if n <= 3
    return false if n % 2 == 0 || n % 3 == 0
    
    i = 5
    while i * i <= n
      return false if n % i == 0 || n % (i + 2) == 0
      i += 6
    end
    
    true
  end

  def self.sieve_of_eratosthenes(n)
    return [] if n < 2
    
    sieve = Array.new(n + 1, true)
    sieve[0] = sieve[1] = false
    
    (2..Math.sqrt(n).to_i).each do |i|
      if sieve[i]
        (i * i).step(n, i) { |j| sieve[j] = false }
      end
    end
    
    (2..n).select { |i| sieve[i] }
  end

  def self.primes_in_range(start, finish)
    return [] if start > finish
    
    # Sieve of Eratosthenes for range
    limit = Math.sqrt(finish).to_i
    base_primes = sieve_of_eratosthenes(limit)
    
    # Initialize range sieve
    range_size = finish - start + 1
    sieve = Array.new(range_size, true)
    
    base_primes.each do |prime|
      # Find first multiple of prime in range
      first_multiple = [(start / prime) * prime, prime * 2].max
      first_multiple = prime * 2 if first_multiple < prime * 2
      
      (first_multiple..finish).step(prime) do |multiple|
        sieve[multiple - start] = false if multiple >= start
      end
    end
    
    (start..finish).select { |i| sieve[i - start] && i >= 2 }
  end

  def self.nth_prime(n)
    return nil if n <= 0
    
    count = 0
    candidate = 2
    
    loop do
      if is_prime?(candidate)
        count += 1
        return candidate if count == n
      end
      candidate += 1
    end
  end
end

# Usage examples
puts "Is 17 prime? #{PrimeNumbers.is_prime?(17)}"
puts "Is 18 prime? #{PrimeNumbers.is_prime?(18)}"
puts "Primes up to 30: #{PrimeNumbers.sieve_of_eratosthenes(30)}"
puts "Primes between 10 and 50: #{PrimeNumbers.primes_in_range(10, 50)}"
puts "10th prime: #{PrimeNumbers.nth_prime(10)}"
```

### GCD and LCM
```ruby
class NumberTheory
  def self.gcd(a, b)
    while b != 0
      a, b = b, a % b
    end
    a
  end

  def self.gcd_recursive(a, b)
    return a if b == 0
    gcd_recursive(b, a % b)
  end

  def self.lcm(a, b)
    (a * b) / gcd(a, b)
  end

  def self.extended_gcd(a, b)
    return [1, 0, a] if b == 0
    
    x1, y1, gcd = extended_gcd(b, a % b)
    x, y = y1, x1 - (a / b) * y1
    
    [x, y, gcd]
  end

  def self.modular_inverse(a, m)
    x, y, gcd = extended_gcd(a, m)
    
    return nil unless gcd == 1
    
    x % m
  end
end

# Usage examples
puts "GCD of 48 and 18: #{NumberTheory.gcd(48, 18)}"
puts "LCM of 48 and 18: #{NumberTheory.lcm(48, 18)}"
puts "Extended GCD of 48 and 18: #{NumberTheory.extended_gcd(48, 18)}"
puts "Modular inverse of 3 mod 11: #{NumberTheory.modular_inverse(3, 11)}"
```

## String Algorithms

### String Matching
```ruby
class StringMatching
  def self.naive_search(text, pattern)
    n = text.length
    m = pattern.length
    matches = []
    
    (0..n - m).each do |i|
      matches << i if text[i...i + m] == pattern
    end
    
    matches
  end

  def self.kmp_search(text, pattern)
    n = text.length
    m = pattern.length
    return [] if m == 0
    
    # Build LPS (Longest Prefix Suffix) array
    lps = build_lps_array(pattern)
    
    i = j = 0  # i for text, j for pattern
    matches = []
    
    while i < n
      if text[i] == pattern[j]
        i += 1
        j += 1
        
        if j == m
          matches << i - j
          j = lps[j - 1]
        end
      else
        if j != 0
          j = lps[j - 1]
        else
          i += 1
        end
      end
    end
    
    matches
  end

  def self.rabin_karp(text, pattern)
    n = text.length
    m = pattern.length
    return [] if m == 0 || m > n
    
    d = 256  # Number of characters in alphabet
    q = 101  # A prime number
    
    h = (d ** (m - 1)) % q
    p = 0    # Hash for pattern
    t = 0    # Hash for text
    
    # Calculate initial hash values
    (0...m).each do |i|
      p = (d * p + pattern[i].ord) % q
      t = (d * t + text[i].ord) % q
    end
    
    matches = []
    
    (0..n - m).each do |i|
      if p == t && text[i...i + m] == pattern
        matches << i
      end
      
      # Calculate hash for next window
      if i < n - m
        t = (d * (t - text[i].ord * h) + text[i + m].ord) % q
        t = (t + q) % q if t < 0
      end
    end
    
    matches
  end

  private

  def self.build_lps_array(pattern)
    m = pattern.length
    lps = Array.new(m, 0)
    length = 0  # Length of previous longest prefix suffix
    
    i = 1
    while i < m
      if pattern[i] == pattern[length]
        length += 1
        lps[i] = length
        i += 1
      else
        if length != 0
          length = lps[length - 1]
        else
          lps[i] = 0
          i += 1
        end
      end
    end
    
    lps
  end
end

# Usage examples
text = "ABABDABACDABABCABAB"
pattern = "ABABCABAB"

puts "Naive search: #{StringMatching.naive_search(text, pattern)}"
puts "KMP search: #{StringMatching.kmp_search(text, pattern)}"
puts "Rabin-Karp search: #{StringMatching.rabin_karp(text, pattern)}"
```

### Anagram Checker
```ruby
class AnagramChecker
  def self.anagram?(str1, str2)
    return false if str1.length != str2.length
    
    # Remove spaces and convert to lowercase
    clean_str1 = str1.gsub(/\s+/, '').downcase
    clean_str2 = str2.gsub(/\s+/, '').downcase
    
    # Sort and compare
    clean_str1.chars.sort.join == clean_str2.chars.sort.join
  end

  def self.anagram_hash?(str1, str2)
    return false if str1.length != str2.length
    
    clean_str1 = str1.gsub(/\s+/, '').downcase
    clean_str2 = str2.gsub(/\s+/, '').downcase
    
    # Use hash count
    char_count = Hash.new(0)
    
    clean_str1.each_char { |char| char_count[char] += 1 }
    clean_str2.each_char { |char| char_count[char] -= 1 }
    
    char_count.values.all?(&:zero?)
  end

  def self.find_anagrams(word, dictionary)
    sorted_word = word.downcase.chars.sort.join
    
    dictionary.select do |dict_word|
      dict_word.length == word.length &&
      dict_word.downcase.chars.sort.join == sorted_word
    end
  end
end

# Usage examples
puts "Are 'listen' and 'silent' anagrams? #{AnagramChecker.anagram?('listen', 'silent')}"
puts "Are 'hello' and 'world' anagrams? #{AnagramChecker.anagram?('hello', 'world')}"

dictionary = ['listen', 'silent', 'enlist', 'tinsel', 'inlets', 'netsil']
word = 'listen'
puts "Anagrams of '#{word}': #{AnagramChecker.find_anagrams(word, dictionary)}"
```

## Challenge Problems

### Two Sum Problem
```ruby
class TwoSum
  def self.find_brute_force(nums, target)
    (0...nums.length).each do |i|
      (i + 1...nums.length).each do |j|
        return [i, j] if nums[i] + nums[j] == target
      end
    end
    nil
  end

  def self.find_hash(nums, target)
    hash_map = {}
    
    nums.each_with_index do |num, i|
      complement = target - num
      
      if hash_map.key?(complement)
        return [hash_map[complement], i]
      end
      
      hash_map[num] = i
    end
    
    nil
  end

  def self.find_two_pointer(nums, target)
    nums_sorted = nums.each_with_index.to_a.sort_by(&:first)
    
    left = 0
    right = nums_sorted.length - 1
    
    while left < right
      sum = nums_sorted[left][0] + nums_sorted[right][0]
      
      if sum == target
        return [nums_sorted[left][1], nums_sorted[right][1]].sort
      elsif sum < target
        left += 1
      else
        right -= 1
      end
    end
    
    nil
  end
end

# Usage example
nums = [2, 7, 11, 15]
target = 9

puts "Brute force: #{TwoSum.find_brute_force(nums, target)}"
puts "Hash method: #{TwoSum.find_hash(nums, target)}"
puts "Two pointer: #{TwoSum.find_two_pointer(nums, target)}"
```

### Valid Parentheses
```ruby
class ValidParentheses
  def self.valid?(s)
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

  def self.valid_with_counting?(s)
    counts = { '(' => 0, '{' => 0, '[' => 0 }
    
    s.each_char do |char|
      case char
      when '(', '{', '['
        counts[char] += 1
      when ')'
        counts['('] -= 1
        return false if counts['('] < 0
      when '}'
        counts['{'] -= 1
        return false if counts['{'] < 0
      when ']'
        counts['['] -= 1
        return false if counts['['] < 0
      end
    end
    
    counts.values.all?(&:zero?)
  end
end

# Usage examples
test_cases = ["()", "()[]{}", "(]", "([{}])", "([)]"]

test_cases.each do |test|
  puts "#{test}: #{ValidParentheses.valid?(test)}"
end
```

## Performance Comparison

### Algorithm Performance Tester
```ruby
require 'benchmark'

class AlgorithmPerformanceTester
  def self.compare_sorting_algorithms
    data_sizes = [100, 1000, 5000, 10000]
    
    data_sizes.each do |size|
      data = Array.new(size) { rand(10000) }
      
      puts "\n=== Sorting #{size} elements ==="
      
      Benchmark.bm(15) do |x|
        x.report("Quick Sort:") do
          QuickSort.sort(data.dup)
        end
        
        x.report("Merge Sort:") do
          MergeSort.sort(data.dup)
        end
        
        x.report("Ruby Sort:") do
          data.dup.sort
        end
        
        x.report("Bubble Sort:") do
          bubble_sort(data.dup)
        end
      end
    end
  end

  def self.compare_search_algorithms
    data_sizes = [100, 1000, 10000]
    
    data_sizes.each do |size|
      data = (1..size).to_a
      target = size / 2
      
      puts "\n=== Searching in #{size} elements ==="
      
      Benchmark.bm(15) do |x|
        x.report("Linear Search:") do
          data.index(target)
        end
        
        x.report("Binary Search:") do
          BinarySearch.search(data, target)
        end
        
        x.report("Ruby find_index:") do
          data.find_index(target)
        end
      end
    end
  end

  def self.compare_fibonacci_implementations
    n_values = [10, 20, 30, 35]
    
    n_values.each do |n|
      puts "\n=== Fibonacci #{n} ==="
      
      Benchmark.bm(15) do |x|
        x.report("Naive:") do
          Fibonacci.naive(n)
        end if n <= 35  # Naive is very slow
        
        x.report("Memoized:") do
          Fibonacci.memoized(n)
        end
        
        x.report("Bottom-up:") do
          Fibonacci.bottom_up(n)
        end
        
        x.report("Space optimized:") do
          Fibonacci.space_optimized(n)
        end
      end
    end
  end

  private

  def self.bubble_sort(array)
    n = array.length
    
    n.times do |i|
      (n - i - 1).times do |j|
        array[j], array[j + 1] = array[j + 1], array[j] if array[j] > array[j + 1]
      end
    end
    
    array
  end
end

# Run performance tests
if __FILE__ == $0
  AlgorithmPerformanceTester.compare_sorting_algorithms
  AlgorithmPerformanceTester.compare_search_algorithms
  AlgorithmPerformanceTester.compare_fibonacci_implementations
end
```

## Best Practices

1. **Choose the Right Algorithm**: Select algorithms based on input size and requirements
2. **Time Complexity**: Understand Big O notation and analyze algorithm efficiency
3. **Space Complexity**: Consider memory usage and space-time tradeoffs
4. **Edge Cases**: Handle edge cases and invalid inputs
5. **Testing**: Write comprehensive tests for algorithm implementations
6. **Optimization**: Profile and optimize critical code paths
7. **Documentation**: Document algorithm complexity and use cases

## Conclusion

Algorithm challenges are excellent for developing problem-solving skills and understanding computational thinking. By practicing these challenges in Ruby, you'll improve your understanding of data structures, algorithms, and Ruby's capabilities as a programming language.

## Further Reading

- [Introduction to Algorithms](https://mitpress.mit.edu/books/introduction-algorithms-third-edition)
- [Algorithm Design Manual](https://www.algorist.com/)
- [Cracking the Coding Interview](https://www.careercup.com/book)
- [LeetCode](https://leetcode.com/)
- [HackerRank](https://www.hackerrank.com/)
