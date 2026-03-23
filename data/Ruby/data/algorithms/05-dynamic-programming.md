# Dynamic Programming in Ruby
# Comprehensive guide to dynamic programming algorithms and techniques

## 🎯 Overview

Dynamic Programming (DP) is a powerful optimization technique that solves complex problems by breaking them down into simpler subproblems. This guide covers DP concepts, patterns, and implementations in Ruby.

## 🧠 DP Fundamentals

### 1. Dynamic Programming Principles

Core concepts and approaches:

```ruby
class DynamicProgrammingBasics
  def self.explain_dp_concepts
    puts "Dynamic Programming Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Optimal Substructure",
        description: "Optimal solution contains optimal solutions to subproblems",
        example: "Shortest path problem - shortest path from A to C includes shortest path from A to B"
      },
      {
        concept: "Overlapping Subproblems",
        description: "Same subproblems are solved multiple times",
        example: "Fibonacci sequence - fib(n) depends on fib(n-1) and fib(n-2) repeatedly"
      },
      {
        concept: "Memoization",
        description: "Store results of expensive function calls and reuse",
        example: "Cache Fibonacci results to avoid recalculating"
      },
      {
        concept: "Tabulation",
        description: "Bottom-up approach filling a table",
        example: "Fill DP table iteratively from smallest subproblems"
      },
      {
        concept: "State Definition",
        description: "Define DP state to represent subproblem",
        example: "dp[i] = optimal solution for first i elements"
      },
      {
        concept: "Transition",
        description: "Relate current state to previous states",
        example: "dp[i] = dp[i-1] + dp[i-2] for Fibonacci"
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Example: #{concept[:example]}"
      puts
    end
  end
  
  def self.dp_approaches
    puts "\nDynamic Programming Approaches:"
    puts "=" * 50
    
    approaches = [
      {
        approach: "Top-Down (Memoization)",
        description: "Recursive with caching",
        pros: ["Natural implementation", "Only computes needed subproblems"],
        cons: ["Recursion overhead", "Stack depth limitations"]
      },
      {
        approach: "Bottom-Up (Tabulation)",
        description: "Iterative table filling",
        pros: ["No recursion overhead", "Better space optimization"],
        cons: ["May compute unnecessary subproblems", "Less intuitive"]
      },
      {
        approach: "Space Optimization",
        description: "Optimize space usage",
        pros: ["Reduced memory usage", "Better cache performance"],
        cons: ["More complex implementation", "Limited to certain problems"]
      }
    ]
    
    approaches.each do |approach|
      puts "#{approach[:approach]}:"
      puts "  Description: #{approach[:description]}"
      puts "  Pros: #{approach[:pros].join(', ')}"
      puts "  Cons: #{approach[:cons].join(', ')}"
      puts
    end
  end
  
  # Run DP basics examples
  explain_dp_concepts
  dp_approaches
end
```

### 2. Fibonacci Sequence

Classic DP example:

```ruby
class FibonacciDP
  def self.naive_fibonacci(n)
    return n if n <= 1
    
    naive_fibonacci(n - 1) + naive_fibonacci(n - 2)
  end
  
  def self.memoized_fibonacci(n, memo = {})
    return n if n <= 1
    return memo[n] if memo[n]
    
    memo[n] = memoized_fibonacci(n - 1, memo) + memoized_fibonacci(n - 2, memo)
    memo[n]
  end
  
  def self.tabulated_fibonacci(n)
    return n if n <= 1
    
    dp = Array.new(n + 1, 0)
    dp[0] = 0
    dp[1] = 1
    
    (2..n).each do |i|
      dp[i] = dp[i - 1] + dp[i - 2]
    end
    
    dp[n]
  end
  
  def self.space_optimized_fibonacci(n)
    return n if n <= 1
    
    prev2 = 0
    prev1 = 1
    
    (2..n).each do |i|
      current = prev1 + prev2
      prev2 = prev1
      prev1 = current
    end
    
    prev1
  end
  
  def self.demonstrate_fibonacci
    puts "Fibonacci Sequence Demonstration:"
    puts "=" * 50
    
    n = 10
    
    puts "Fibonacci(#{n}):"
    puts "Naive:       #{naive_fibonacci(n)}"
    puts "Memoized:    #{memoized_fibonacci(n)}"
    puts "Tabulated:   #{tabulated_fibonacci(n)}"
    puts "Optimized:   #{space_optimized_fibonacci(n)}"
    
    # Performance comparison
    puts "\nPerformance Comparison:"
    [20, 30, 35].each do |size|
      puts "\nFibonacci(#{size}):"
      
      # Memoized
      start_time = Time.now
      memoized_fibonacci(size)
      memoized_time = (Time.now - start_time) * 1000
      
      # Tabulated
      start_time = Time.now
      tabulated_fibonacci(size)
      tabulated_time = (Time.now - start_time) * 1000
      
      # Space optimized
      start_time = Time.now
      space_optimized_fibonacci(size)
      optimized_time = (Time.now - start_time) * 1000
      
      puts "  Memoized:    #{memoized_time.round(4)}ms"
      puts "  Tabulated:   #{tabulated_time.round(4)}ms"
      puts "  Optimized:   #{optimized_time.round(4)}ms"
    end
  end
end
```

## 🎒 Classic DP Problems

### 3. Knapsack Problem

0/1 Knapsack problem:

```ruby
class KnapsackDP
  def self.knapsack_recursive(weights, values, capacity, n, memo = {})
    return 0 if n == 0 || capacity == 0
    
    key = "#{n}-#{capacity}"
    return memo[key] if memo[key]
    
    # If current item weight exceeds capacity, skip it
    if weights[n - 1] > capacity
      result = knapsack_recursive(weights, values, capacity, n - 1, memo)
    else
      # Max of including or excluding current item
      include = values[n - 1] + knapsack_recursive(weights, values, capacity - weights[n - 1], n - 1, memo)
      exclude = knapsack_recursive(weights, values, capacity, n - 1, memo)
      result = [include, exclude].max
    end
    
    memo[key] = result
  end
  
  def self.knapsack_tabulated(weights, values, capacity)
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
  
  def self.knapsack_items(weights, values, capacity)
    n = weights.length
    dp = Array.new(n + 1) { Array.new(capacity + 1, 0) }
    
    # Fill DP table
    (1..n).each do |i|
      (0..capacity).each do |w|
        if weights[i - 1] <= w
          dp[i][w] = [values[i - 1] + dp[i - 1][w - weights[i - 1]], dp[i - 1][w]].max
        else
          dp[i][w] = dp[i - 1][w]
        end
      end
    end
    
    # Backtrack to find items
    items = []
    w = capacity
    
    n.downto(1) do |i|
      if dp[i][w] != dp[i - 1][w]
        items << i - 1
        w -= weights[i - 1]
      end
    end
    
    { value: dp[n][capacity], items: items.reverse }
  end
  
  def self.demonstrate_knapsack
    puts "Knapsack Problem Demonstration:"
    puts "=" * 50
    
    weights = [2, 3, 4, 5]
    values = [3, 4, 5, 6]
    capacity = 5
    
    puts "Weights: #{weights}"
    puts "Values:  #{values}"
    puts "Capacity: #{capacity}"
    
    max_value = knapsack_recursive(weights, values, capacity, weights.length)
    puts "\nMaximum value (recursive): #{max_value}"
    
    max_value = knapsack_tabulated(weights, values, capacity)
    puts "Maximum value (tabulated): #{max_value}"
    
    result = knapsack_items(weights, values, capacity)
    puts "Maximum value: #{result[:value]}"
    puts "Items selected: #{result[:items].map { |i| "Item#{i + 1}(W:#{weights[i]}, V:#{values[i]})" }.join(', ')}"
  end
end
```

### 4. Longest Common Subsequence

Find LCS between two strings:

```ruby
class LongestCommonSubsequence
  def self.lcs_recursive(str1, str2, m = nil, n = nil, memo = {})
    m ||= str1.length
    n ||= str2.length
    
    return 0 if m == 0 || n == 0
    
    key = "#{m}-#{n}"
    return memo[key] if memo[key]
    
    if str1[m - 1] == str2[n - 1]
      result = 1 + lcs_recursive(str1, str2, m - 1, n - 1, memo)
    else
      result = [lcs_recursive(str1, str2, m - 1, n, memo),
                lcs_recursive(str1, str2, m, n - 1, memo)].max
    end
    
    memo[key] = result
  end
  
  def self.lcs_tabulated(str1, str2)
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
  
  def self.lcs_string(str1, str2)
    m = str1.length
    n = str2.length
    
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
    
    # Backtrack to find LCS string
    lcs = ""
    i, j = m, n
    
    while i > 0 && j > 0
      if str1[i - 1] == str2[j - 1]
        lcs = str1[i - 1] + lcs
        i -= 1
        j -= 1
      elsif dp[i - 1][j] > dp[i][j - 1]
        i -= 1
      else
        j -= 1
      end
    end
    
    lcs
  end
  
  def self.demonstrate_lcs
    puts "Longest Common Subsequence Demonstration:"
    puts "=" * 50
    
    test_cases = [
      ['AGGTAB', 'GXTXAYB'],
      ['ABCDGH', 'AEDFHR'],
      ['ABC', 'DEF'],
      ['XMJYAUZ', 'MZJAWXU']
    ]
    
    test_cases.each do |str1, str2|
      puts "\nString 1: #{str1}"
      puts "String 2: #{str2}"
      
      length = lcs_recursive(str1, str2)
      puts "LCS length (recursive): #{length}"
      
      length = lcs_tabulated(str1, str2)
      puts "LCS length (tabulated): #{length}"
      
      lcs_str = lcs_string(str1, str2)
      puts "LCS string: #{lcs_str}"
    end
  end
end
```

### 5. Coin Change Problem

Minimum coins to make amount:

```ruby
class CoinChangeDP
  def self.coin_change_recursive(coins, amount, memo = {})
    return 0 if amount == 0
    return Float::INFINITY if amount < 0 || coins.empty?
    
    return memo[amount] if memo[amount]
    
    min_coins = Float::INFINITY
    coins.each do |coin|
      if coin <= amount
        result = coin_change_recursive(coins, amount - coin, memo)
        min_coins = [min_coins, result + 1].min if result != Float::INFINITY
      end
    end
    
    memo[amount] = min_coins
  end
  
  def self.coin_change_tabulated(coins, amount)
    dp = Array.new(amount + 1, Float::INFINITY)
    dp[0] = 0
    
    (1..amount).each do |i|
      coins.each do |coin|
        if coin <= i
          dp[i] = [dp[i], dp[i - coin] + 1].min
        end
      end
    end
    
    dp[amount] == Float::INFINITY ? -1 : dp[amount]
  end
  
  def self.coin_change_ways(coins, amount)
    dp = Array.new(amount + 1, 0)
    dp[0] = 1
    
    coins.each do |coin|
      (coin..amount).each do |i|
        dp[i] += dp[i - coin]
      end
    end
    
    dp[amount]
  end
  
  def self.coin_change_combination(coins, amount)
    dp = Array.new(amount + 1, Float::INFINITY)
    dp[0] = 0
    
    (1..amount).each do |i|
      coins.each do |coin|
        if coin <= i
          dp[i] = [dp[i], dp[i - coin] + 1].min
        end
      end
    end
    
    # Backtrack to find coins used
    return -1 if dp[amount] == Float::INFINITY
    
    coins_used = []
    remaining = amount
    
    while remaining > 0
      coins.each do |coin|
        if coin <= remaining && dp[remaining] == dp[remaining - coin] + 1
          coins_used << coin
          remaining -= coin
          break
        end
      end
    end
    
    { count: dp[amount], coins: coins_used }
  end
  
  def self.demonstrate_coin_change
    puts "Coin Change Problem Demonstration:"
    puts "=" * 50
    
    test_cases = [
      { coins: [1, 2, 5], amount: 11 },
      { coins: [2], amount: 3 },
      { coins: [1, 3, 4, 5], amount: 7 },
      { coins: [1, 5, 10, 25], amount: 30 }
    ]
    
    test_cases.each do |test_case|
      coins = test_case[:coins]
      amount = test_case[:amount]
      
      puts "\nCoins: #{coins}"
      puts "Amount: #{amount}"
      
      min_coins = coin_change_recursive(coins, amount)
      puts "Minimum coins (recursive): #{min_coins == Float::INFINITY ? 'Not possible' : min_coins}"
      
      min_coins = coin_change_tabulated(coins, amount)
      puts "Minimum coins (tabulated): #{min_coins}"
      
      ways = coin_change_ways(coins, amount)
      puts "Number of ways: #{ways}"
      
      if min_coins != -1
        combination = coin_change_combination(coins, amount)
        puts "One optimal combination: #{combination[:coins].join(' + ')}"
      end
    end
  end
end
```

## 🎯 Advanced DP Problems

### 6. Edit Distance

Levenshtein distance between strings:

```ruby
class EditDistanceDP
  def self.edit_distance_recursive(str1, str2, m = nil, n = nil, memo = {})
    m ||= str1.length
    n ||= str2.length
    
    return m if n == 0
    return n if m == 0
    
    key = "#{m}-#{n}"
    return memo[key] if memo[key]
    
    if str1[m - 1] == str2[n - 1]
      result = edit_distance_recursive(str1, str2, m - 1, n - 1, memo)
    else
      insert = edit_distance_recursive(str1, str2, m, n - 1, memo)
      delete = edit_distance_recursive(str1, str2, m - 1, n, memo)
      replace = edit_distance_recursive(str1, str2, m - 1, n - 1, memo)
      result = 1 + [insert, delete, replace].min
    end
    
    memo[key] = result
  end
  
  def self.edit_distance_tabulated(str1, str2)
    m = str1.length
    n = str2.length
    
    dp = Array.new(m + 1) { Array.new(n + 1, 0) }
    
    # Initialize base cases
    (0..m).each { |i| dp[i][0] = i }
    (0..n).each { |j| dp[0][j] = j }
    
    (1..m).each do |i|
      (1..n).each do |j|
        if str1[i - 1] == str2[j - 1]
          dp[i][j] = dp[i - 1][j - 1]
        else
          dp[i][j] = 1 + [dp[i - 1][j],    # Delete
                          dp[i][j - 1],    # Insert
                          dp[i - 1][j - 1]].min # Replace
        end
      end
    end
    
    dp[m][n]
  end
  
  def self.edit_operations(str1, str2)
    m = str1.length
    n = str2.length
    
    dp = Array.new(m + 1) { Array.new(n + 1, 0) }
    
    # Initialize base cases
    (0..m).each { |i| dp[i][0] = i }
    (0..n).each { |j| dp[0][j] = j }
    
    # Fill DP table
    (1..m).each do |i|
      (1..n).each do |j|
        if str1[i - 1] == str2[j - 1]
          dp[i][j] = dp[i - 1][j - 1]
        else
          dp[i][j] = 1 + [dp[i - 1][j], dp[i][j - 1], dp[i - 1][j - 1]].min
        end
      end
    end
    
    # Backtrack to find operations
    operations = []
    i, j = m, n
    
    while i > 0 || j > 0
      if i > 0 && j > 0 && str1[i - 1] == str2[j - 1]
        i -= 1
        j -= 1
      elsif j > 0 && (i == 0 || dp[i][j - 1] + 1 == dp[i][j])
        operations << { type: :insert, char: str2[j - 1], position: i }
        j -= 1
      elsif i > 0 && (j == 0 || dp[i - 1][j] + 1 == dp[i][j])
        operations << { type: :delete, char: str1[i - 1], position: i - 1 }
        i -= 1
      else
        operations << { type: :replace, char: str2[j - 1], position: i - 1, old_char: str1[i - 1] }
        i -= 1
        j -= 1
      end
    end
    
    { distance: dp[m][n], operations: operations.reverse }
  end
  
  def self.demonstrate_edit_distance
    puts "Edit Distance Demonstration:"
    puts "=" * 50
    
    test_cases = [
      ['kitten', 'sitting'],
      ['flaw', 'lawn'],
      ['intention', 'execution'],
      ['abc', 'xyz'],
      ['', 'empty']
    ]
    
    test_cases.each do |str1, str2|
      puts "\nString 1: #{str1}"
      puts "String 2: #{str2}"
      
      distance = edit_distance_recursive(str1, str2)
      puts "Edit distance (recursive): #{distance}"
      
      distance = edit_distance_tabulated(str1, str2)
      puts "Edit distance (tabulated): #{distance}"
      
      result = edit_operations(str1, str2)
      puts "Operations:"
      result[:operations].each do |op|
        case op[:type]
        when :insert
          puts "  Insert '#{op[:char]}' at position #{op[:position]}"
        when :delete
          puts "  Delete '#{op[:char]}' at position #{op[:position]}"
        when :replace
          puts "  Replace '#{op[:old_char]}' with '#{op[:char]}' at position #{op[:position]}"
        end
      end
    end
  end
end
```

### 7. Longest Increasing Subsequence

Find LIS in an array:

```ruby
class LongestIncreasingSubsequence
  def self.lis_recursive(arr, n = nil, prev = nil, memo = {})
    n ||= arr.length
    return 0 if n == 0
    
    key = "#{n}-#{prev}"
    return memo[key] if memo[key]
    
    # Exclude current element
    exclude = lis_recursive(arr, n - 1, prev, memo)
    
    # Include current element if it's larger than previous
    include = 0
    if prev.nil? || arr[n - 1] > prev
      include = 1 + lis_recursive(arr, n - 1, arr[n - 1], memo)
    end
    
    memo[key] = [exclude, include].max
  end
  
  def self.lis_tabulated(arr)
    return 0 if arr.empty?
    
    n = arr.length
    dp = Array.new(n, 1)
    
    (1...n).each do |i|
      (0...i).each do |j|
        if arr[j] < arr[i]
          dp[i] = [dp[i], dp[j] + 1].max
        end
      end
    end
    
    dp.max
  end
  
  def self.lis_sequence(arr)
    return [] if arr.empty?
    
    n = arr.length
    dp = Array.new(n, 1)
    parent = Array.new(n, -1)
    
    (1...n).each do |i|
      (0...i).each do |j|
        if arr[j] < arr[i] && dp[j] + 1 > dp[i]
          dp[i] = dp[j] + 1
          parent[i] = j
        end
      end
    end
    
    # Find maximum length index
    max_index = (0...n).max_by { |i| dp[i] }
    
    # Reconstruct LIS
    lis = []
    while max_index >= 0
      lis.unshift(arr[max_index])
      max_index = parent[max_index]
    end
    
    lis
  end
  
  def self.lis_optimized(arr)
    return [] if arr.empty?
    
    tails = [] # tails[i] = smallest tail of LIS of length i+1
    
    arr.each do |num|
      # Find position to insert/replace
      left, right = 0, tails.length
      
      while left < right
        mid = (left + right) / 2
        if tails[mid] < num
          left = mid + 1
        else
          right = mid
        end
      end
      
      if left == tails.length
        tails << num
      else
        tails[left] = num
      end
    end
    
    tails.length
  end
  
  def self.demonstrate_lis
    puts "Longest Increasing Subsequence Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [10, 9, 2, 5, 3, 7, 101, 18],
      [0, 1, 0, 3, 2, 3],
      [7, 7, 7, 7, 7, 7, 7],
      [1, 2, 3, 4, 5],
      [5, 4, 3, 2, 1],
      [3, 10, 2, 1, 20]
    ]
    
    test_arrays.each do |arr|
      puts "\nArray: #{arr}"
      
      length = lis_recursive(arr)
      puts "LIS length (recursive): #{length}"
      
      length = lis_tabulated(arr)
      puts "LIS length (tabulated): #{length}"
      
      length = lis_optimized(arr)
      puts "LIS length (optimized): #{length}"
      
      sequence = lis_sequence(arr)
      puts "LIS sequence: #{sequence.join(', ')}"
    end
  end
end
```

## 📊 Performance Analysis

### DP Algorithm Performance

```ruby
class DPPerformance
  def self.compare_fibonacci_approaches
    puts "Fibonacci Performance Comparison:"
    puts "=" * 60
    
    [20, 30, 35, 40].each do |n|
      puts "\nFibonacci(#{n}):"
      
      # Memoized
      start_time = Time.now
      FibonacciDP.memoized_fibonacci(n)
      memoized_time = (Time.now - start_time) * 1000
      
      # Tabulated
      start_time = Time.now
      FibonacciDP.tabulated_fibonacci(n)
      tabulated_time = (Time.now - start_time) * 1000
      
      # Space optimized
      start_time = Time.now
      FibonacciDP.space_optimized_fibonacci(n)
      optimized_time = (Time.now - start_time) * 1000
      
      puts "  Memoized:    #{memoized_time.round(4)}ms"
      puts "  Tabulated:   #{tabulated_time.round(4)}ms"
      puts "  Optimized:   #{optimized_time.round(4)}ms"
    end
  end
  
  def self.compare_knapsack_approaches
    puts "\nKnapsack Performance Comparison:"
    puts "=" * 60
    
    weights = (1..20).to_a
    values = weights.map { |w| w * 2 }
    capacity = 50
    
    [5, 10, 15, 20].each do |n|
      puts "\nKnapsack with #{n} items:"
      
      # Recursive
      start_time = Time.now
      KnapsackDP.knapsack_recursive(weights[0...n], values[0...n], capacity, n)
      recursive_time = (Time.now - start_time) * 1000
      
      # Tabulated
      start_time = Time.now
      KnapsackDP.knapsack_tabulated(weights[0...n], values[0...n], capacity)
      tabulated_time = (Time.now - start_time) * 1000
      
      puts "  Recursive:  #{recursive_time.round(4)}ms"
      puts "  Tabulated:  #{tabulated_time.round(4)}ms"
    end
  end
  
  def self.time_complexity_analysis
    puts "\nTime Complexity Analysis:"
    puts "=" * 50
    
    complexities = {
      'Fibonacci (Naive)' => 'O(2^n)',
      'Fibonacci (DP)' => 'O(n)',
      'Knapsack (Recursive)' => 'O(2^n)',
      'Knapsack (DP)' => 'O(nW)',
      'LCS (DP)' => 'O(mn)',
      'Edit Distance (DP)' => 'O(mn)',
      'LIS (DP)' => 'O(n²)',
      'LIS (Optimized)' => 'O(n log n)',
      'Coin Change (DP)' => 'O(nW)'
    }
    
    complexities.each do |algorithm, complexity|
      puts "#{algorithm.ljust(25)}: #{complexity}"
    end
  end
end
```

## 🎯 Practical Applications

### Real-World DP Problems

```ruby
class PracticalDP
  def self.stock_trading
    puts "Stock Trading Problem:"
    puts "=" * 40
    
    # Maximum profit with at most k transactions
    def self.max_profit_k_transactions(prices, k)
      return 0 if prices.empty? || k == 0
      
      n = prices.length
      dp = Array.new(k + 1) { Array.new(n, 0) }
      
      (1..k).each do |transaction|
        (1...n).each do |day|
          max_profit = 0
          
          (0...day).each do |prev_day|
            profit = prices[day] - prices[prev_day] + dp[transaction - 1][prev_day]
            max_profit = [max_profit, profit].max
          end
          
          dp[transaction][day] = [dp[transaction][day - 1], max_profit].max
        end
      end
      
      dp[k][n - 1]
    end
    
    # Maximum profit with unlimited transactions
    def self.max_profit_unlimited(prices)
      return 0 if prices.empty?
      
      profit = 0
      
      (1...prices.length).each do |i|
        profit += prices[i] - prices[i - 1] if prices[i] > prices[i - 1]
      end
      
      profit
    end
    
    prices = [3, 3, 5, 0, 0, 3, 1, 4]
    
    puts "Prices: #{prices}"
    puts "Max profit (2 transactions): #{max_profit_k_transactions(prices, 2)}"
    puts "Max profit (unlimited): #{max_profit_unlimited(prices)}"
  end
  
  def self.matrix_chain_multiplication
    puts "\nMatrix Chain Multiplication:"
    puts "=" * 40
    
    def self.matrix_chain_order(dimensions)
      n = dimensions.length - 1
      dp = Array.new(n) { Array.new(n, 0) }
      
      # Chain length from 2 to n
      (2..n).each do |length|
        (0...n - length + 1).each do |i|
          j = i + length - 1
          dp[i][j] = Float::INFINITY
          
          (i...j).each do |k|
            cost = dp[i][k] + dp[k + 1][j] + dimensions[i] * dimensions[k + 1] * dimensions[j + 1]
            dp[i][j] = [dp[i][j], cost].min
          end
        end
      end
      
      dp[0][n - 1]
    end
    
    dimensions = [10, 30, 5, 60]
    
    puts "Matrix dimensions: #{dimensions}"
    puts "Minimum multiplications: #{matrix_chain_order(dimensions)}"
  end
  
  def self.palindrome_partitioning
    puts "\nPalindrome Partitioning:"
    puts "=" * 40
    
    def self.min_palindrome_partitions(s)
      n = s.length
      dp = Array.new(n + 1, Float::INFINITY)
      dp[0] = 0
      
      (1..n).each do |i|
        (0...i).each do |j|
          if s[j...i] == s[j...i].reverse
            dp[i] = [dp[i], dp[j] + 1].min
          end
        end
      end
      
      dp[n]
    end
    
    test_strings = ['ababbbabbababa', 'noonabbad', 'racecar', 'abc']
    
    test_strings.each do |str|
      puts "String: #{str}"
      puts "Min partitions: #{min_palindrome_partitions(str)}"
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Fibonacci**: Implement all Fibonacci approaches
2. **Coin Change**: Solve coin change problem
3. **LCS**: Find longest common subsequence

### Intermediate Exercises

1. **Knapsack**: Implement 0/1 knapsack
2. **Edit Distance**: Calculate edit distance
3. **LIS**: Find longest increasing subsequence

### Advanced Exercises

1. **Complex DP**: Solve matrix chain multiplication
2. **Space Optimization**: Optimize DP solutions
3. **Real Applications**: Build practical DP solutions

---

## 🎯 Summary

Dynamic Programming in Ruby provides:

- **DP Fundamentals** - Core concepts and approaches
- **Classic Problems** - Fibonacci, Knapsack, LCS, Coin Change
- **Advanced Problems** - Edit Distance, LIS, Matrix Chain
- **Performance Analysis** - Time and space complexity
- **Practical Applications** - Real-world DP problems
- **Optimization Techniques** - Memoization and tabulation

Master DP to solve complex optimization problems efficiently!
