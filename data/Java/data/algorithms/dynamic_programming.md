# Dynamic Programming - Complete Guide

This guide covers dynamic programming from basic concepts to advanced applications, with implementations and optimization techniques.

## 📚 Table of Contents

1. [Introduction to Dynamic Programming](#introduction-to-dynamic-programming)
2. [DP Problem Characteristics](#dp-problem-characteristics)
3. [Basic DP Patterns](#basic-dp-patterns)
4. [Advanced DP Techniques](#advanced-dp-techniques)
5. [Optimization Strategies](#optimization-strategies)
6. [Common DP Problems](#common-dp-problems)
7. [Performance Analysis](#performance-analysis)

---

## Introduction to Dynamic Programming

### What is Dynamic Programming?
Dynamic Programming (DP) is a method for solving complex problems by breaking them down into simpler overlapping subproblems.

### Core Principles
- **Optimal Substructure**: Optimal solution can be constructed from optimal solutions of subproblems
- **Overlapping Subproblems**: Subproblems are reused multiple times
- **Memoization**: Store results of subproblems to avoid recomputation
- **Bottom-up**: Build solution from smallest subproblems upward

### When to Use DP
- Problem has optimal substructure
- Subproblems overlap significantly
- Problem can be broken down recursively
- Solution depends on solutions to smaller instances

---

## DP Problem Characteristics

### Optimal Substructure
A problem exhibits optimal substructure if an optimal solution can be constructed from optimal solutions to its subproblems.

#### Example: Shortest Path
The shortest path from A to C through B consists of:
- Shortest path from A to B
- Shortest path from B to C

### Overlapping Subproblems
Same subproblems are solved multiple times in the recursion tree.

#### Example: Fibonacci
fib(5) requires fib(4), fib(3), fib(2), fib(1)
fib(4) requires fib(3), fib(2), fib(1)
fib(3) requires fib(2), fib(1)

Notice fib(2), fib(1) are computed multiple times.

---

## Basic DP Patterns

### 1. Memoization (Top-Down)

#### Concept
Store results of expensive function calls and reuse them.

#### Implementation
```java
def fibonacci_memo(n, memo=None):
    """Fibonacci with memoization"""
    if memo is None:
        memo = {}
    
    if n in memo:
        return memo[n]
    
    if n <= 1:
        return n
    
    result = fibonacci_memo(n - 1, memo) + fibonacci_memo(n - 2, memo)
    memo[n] = result
    return result

# Compare with naive recursive Fibonacci
def fibonacci_naive(n):
    """Naive recursive Fibonacci"""
    if n <= 1:
        return n
    return fibonacci_naive(n - 1) + fibonacci_naive(n - 2)

# Performance comparison
import time

# Test memoized version
start = time.time()
result_memo = fibonacci_memo(35)
time_memo = time.time() - start

# Test naive version
start = time.time()
result_naive = fibonacci_naive(35)
time_naive = time.time() - start

print(f"Fibonacci(35) = {result_memo}")
print(f"Memoized time: {time_memo:.6f} seconds")
print(f"Naive time: {time_naive:.6f} seconds")
print(f"Speedup: {time_naive/time_memo:.1f}x faster")
```

### 2. Tabulation (Bottom-Up)

#### Concept
Fill DP table iteratively from smallest subproblems to largest.

#### Implementation
```java
def fibonacci_tabulation(n):
    """Fibonacci with tabulation"""
    if n <= 1:
        return n
    
    # Create table to store Fibonacci numbers
    dp = [0] * (n + 1)
    dp[0] = 0
    dp[1] = 1
    
    # Fill table iteratively
    for i in range(2, n + 1):
        dp[i] = dp[i - 1] + dp[i - 2]
    
    return dp[n]

# Test tabulation
for n in range(1, 11):
    result = fibonacci_tabulation(n)
    print(f"Fibonacci({n}) = {result}")

# Space optimization - only keep last two values
def fibonacci_optimized(n):
    """Space-optimized Fibonacci"""
    if n <= 1:
        return n
    
    prev2, prev1 = 0, 1
    
    for i in range(2, n + 1):
        current = prev1 + prev2
        prev2, prev1 = prev1, current
    
    return prev1

print(f"\nOptimized Fibonacci(10) = {fibonacci_optimized(10)}")
```

---

## Advanced DP Techniques

### 1. Knapsack Problem

#### Problem Statement
Given items with weights and values, and a knapsack with capacity W, maximize total value.

#### Implementation
```java
def knapsack_01(weights, values, capacity):
    """0/1 Knapsack problem using DP"""
    n = len(weights)
    
    # dp[i][w] = maximum value using first i items with capacity w
    dp = [[0] * (capacity + 1) for _ in range(n + 1)]
    
    # Build DP table
    for i in range(n + 1):
        for w in range(capacity + 1):
            if i == 0 or w == 0:
                dp[i][w] = 0
            elif weights[i - 1] <= w:
                dp[i][w] = max(
                    dp[i - 1][w],  # Don't include item i-1
                    values[i - 1] + dp[i - 1][w - weights[i - 1]]  # Include item i-1
                )
            else:
                dp[i][w] = dp[i - 1][w]  # Can't include item i-1
    
    return dp[n][capacity]

def knapsack_items(weights, values, capacity):
    """Return actual items in knapsack"""
    n = len(weights)
    dp = [[0] * (capacity + 1) for _ in range(n + 1)]
    
    # Build DP table
    for i in range(n + 1):
        for w in range(capacity + 1):
            if i == 0 or w == 0:
                dp[i][w] = 0
            elif weights[i - 1] <= w:
                dp[i][w] = max(
                    dp[i - 1][w],
                    values[i - 1] + dp[i - 1][w - weights[i - 1]]
                )
            else:
                dp[i][w] = dp[i - 1][w]
    
    # Backtrack to find items
    items = []
    w = capacity
    for i in range(n, 0, -1):
        if dp[i][w] != dp[i - 1][w]:
            items.append(i - 1)
            w -= weights[i - 1]
    
    return items

# Example usage
weights = [2, 3, 4, 5]
values = [3, 4, 5, 6]
capacity = 5

max_value = knapsack_01(weights, values, capacity)
items = knapsack_items(weights, values, capacity)

print(f"Maximum value: {max_value}")
print(f"Items selected: {[values[i] for i in items]}")
```

### 2. Longest Common Subsequence (LCS)

#### Problem Statement
Find longest subsequence common to two sequences.

#### Implementation
```java
def longest_common_subsequence(seq1, seq2):
    """Longest Common Subsequence using DP"""
    m, n = len(seq1), len(seq2)
    
    # dp[i][j] = LCS length for seq1[:i] and seq2[:j]
    dp = [[0] * (n + 1) for _ in range(m + 1)]
    
    # Fill DP table
    for i in range(1, m + 1):
        for j in range(1, n + 1):
            if seq1[i - 1] == seq2[j - 1]:
                dp[i][j] = dp[i - 1][j - 1] + 1
            else:
                dp[i][j] = max(dp[i - 1][j], dp[i][j - 1])
    
    return dp[m][n]

def reconstruct_lcs(seq1, seq2):
    """Reconstruct actual LCS string"""
    m, n = len(seq1), len(seq2)
    dp = [[0] * (n + 1) for _ in range(m + 1)]
    
    # Fill DP table
    for i in range(1, m + 1):
        for j in range(1, n + 1):
            if seq1[i - 1] == seq2[j - 1]:
                dp[i][j] = dp[i - 1][j - 1] + 1
            else:
                dp[i][j] = max(dp[i - 1][j], dp[i][j - 1])
    
    # Reconstruct LCS
    lcs = []
    i, j = m, n
    
    while i > 0 and j > 0:
        if seq1[i - 1] == seq2[j - 1]:
            lcs.append(seq1[i - 1])
            i -= 1
            j -= 1
        elif dp[i - 1][j] >= dp[i][j - 1]:
            i -= 1
        else:
            j -= 1
    
    return ''.join(reversed(lcs))

# Example usage
seq1 = "AGGTAB"
seq2 = "GXTXAYB"

lcs_length = longest_common_subsequence(seq1, seq2)
lcs_string = reconstruct_lcs(seq1, seq2)

print(f"LCS length: {lcs_length}")
print(f"LCS string: {lcs_string}")
```

### 3. Coin Change Problem

#### Problem Statement
Find minimum number of coins needed to make a given amount.

#### Implementation
```java
def coin_change(coins, amount):
    """Coin change problem - minimum coins needed"""
    # dp[i] = minimum coins needed for amount i
    dp = [float('infinity')] * (amount + 1)
    dp[0] = 0
    
    # Fill DP table
    for i in range(1, amount + 1):
        for coin in coins:
            if coin <= i:
                dp[i] = min(dp[i], dp[i - coin] + 1)
    
    return dp[amount] if dp[amount] != float('infinity') else -1

def coin_change_ways(coins, amount):
    """Coin change problem - number of ways to make amount"""
    # dp[i] = number of ways to make amount i
    dp = [0] * (amount + 1)
    dp[0] = 1  # One way to make amount 0 (use no coins)
    
    # Fill DP table
    for i in range(1, amount + 1):
        for coin in coins:
            if coin <= i:
                dp[i] += dp[i - coin]
    
    return dp[amount]

# Example usage
coins = [1, 3, 4]
amount = 6

min_coins = coin_change(coins, amount)
ways = coin_change_ways(coins, amount)

print(f"Minimum coins to make {amount}: {min_coins}")
print(f"Number of ways to make {amount}: {ways}")
```

### 4. Edit Distance

#### Problem Statement
Find minimum number of operations (insert, delete, replace) to convert one string to another.

#### Implementation
```java
def edit_distance(s1, s2):
    """Edit distance (Levenshtein distance) using DP"""
    m, n = len(s1), len(s2)
    
    # dp[i][j] = edit distance between s1[:i] and s2[:j]
    dp = [[0] * (n + 1) for _ in range(m + 1)]
    
    # Initialize base cases
    for i in range(m + 1):
        dp[i][0] = i  # Delete all characters from s1[:i]
    for j in range(n + 1):
        dp[0][j] = j  # Insert all characters from s2[:j]
    
    # Fill DP table
    for i in range(1, m + 1):
        for j in range(1, n + 1):
            if s1[i - 1] == s2[j - 1]:
                dp[i][j] = dp[i - 1][j - 1]  # No operation needed
            else:
                dp[i][j] = 1 + min(
                    dp[i - 1][j],      # Delete
                    dp[i][j - 1],      # Insert
                    dp[i - 1][j - 1]   # Replace
                )
    
    return dp[m][n]

def reconstruct_edit_sequence(s1, s2):
    """Reconstruct sequence of edit operations"""
    m, n = len(s1), len(s2)
    dp = [[0] * (n + 1) for _ in range(m + 1)]
    
    # Fill DP table (simplified version)
    for i in range(m + 1):
        for j in range(n + 1):
            if i == 0:
                dp[i][j] = j
            elif j == 0:
                dp[i][j] = i
            elif s1[i - 1] == s2[j - 1]:
                dp[i][j] = dp[i - 1][j - 1]
            else:
                dp[i][j] = 1 + min(dp[i - 1][j], dp[i][j - 1], dp[i - 1][j - 1])
    
    # Reconstruct operations (simplified)
    operations = []
    i, j = m, n
    
    while i > 0 and j > 0:
        if s1[i - 1] == s2[j - 1]:
            operations.append(f"Keep '{s1[i - 1]}'")
            i -= 1
            j -= 1
        elif dp[i - 1][j] <= dp[i][j - 1]:
            operations.append(f"Delete '{s1[i - 1]}'")
            i -= 1
        else:
            operations.append(f"Insert '{s2[j - 1]}'")
            j -= 1
    
    # Handle remaining characters
    while i > 0:
        operations.append(f"Delete '{s1[i - 1]}'")
        i -= 1
    while j > 0:
        operations.append(f"Insert '{s2[j - 1]}'")
        j -= 1
    
    return operations

# Example usage
s1 = "kitten"
s2 = "sitting"

distance = edit_distance(s1, s2)
operations = reconstruct_edit_sequence(s1, s2)

print(f"Edit distance between '{s1}' and '{s2}': {distance}")
print("Edit operations:")
for op in operations:
    print(f"  {op}")
```

---

## Optimization Strategies

### Space Optimization

#### Rolling Arrays
Use only necessary previous states instead of full DP table.

```java
def knapsack_space_optimized(weights, values, capacity):
    """Space-optimized knapsack"""
    n = len(weights)
    
    # Only keep two rows instead of full table
    prev_row = [0] * (capacity + 1)
    curr_row = [0] * (capacity + 1)
    
    for i in range(1, n + 1):
        for w in range(capacity + 1):
            if weights[i - 1] <= w:
                curr_row[w] = max(
                    prev_row[w],
                    values[i - 1] + prev_row[w - weights[i - 1]]
                )
            else:
                curr_row[w] = prev_row[w]
        
        # Swap rows
        prev_row, curr_row = curr_row, prev_row
    
    return prev_row[capacity]
```

### Time Optimization

#### Early Termination
Stop DP computation when optimal solution is found.

```java
def subset_sum_early(arr, target):
    """Subset sum with early termination"""
    n = len(arr)
    total = sum(arr)
    
    # Early termination checks
    if total < target:
        return []  # Impossible
    if total == target:
        return arr  # All elements needed
    
    # DP with bitset optimization
    dp = [False] * (target + 1)
    dp[0] = True
    
    for num in arr:
        for j in range(target, num - 1, -1):
            if dp[j - num]:
                dp[j] = True
        
        if dp[target]:
            return [target]  # Early exit if target reached
    
    # Reconstruct solution (simplified)
    for i in range(target, -1, -1):
        if dp[i]:
            return i
    
    return -1
```

---

## Common DP Problems

### 1. Matrix Chain Multiplication

#### Problem Statement
Find optimal parenthesization for matrix chain multiplication.

#### Implementation
```java
def matrix_chain_order(dimensions):
    """Matrix Chain Multiplication DP"""
    n = len(dimensions) - 1  # Number of matrices
    
    # dp[i][j] = minimum operations for matrices i..j
    dp = [[0] * n for _ in range(n)]
    split = [[0] * n for _ in range(n)]
    
    # L is chain length
    for L in range(2, n + 1):
        for i in range(n - L + 1):
            j = i + L - 1
            dp[i][j] = float('infinity')
            
            for k in range(i, j):
                operations = (dp[i][k] + dp[k + 1][j] + 
                           dimensions[i] * dimensions[k + 1] * dimensions[j + 1])
                if operations < dp[i][j]:
                    dp[i][j] = operations
                    split[i][j] = k
    
    return dp[0][n - 1]

def optimal_parenthesization(split, i, j):
    """Reconstruct optimal parenthesization"""
    if i == j:
        return f"M{i+1}"
    
    k = split[i][j]
    left = optimal_parenthesization(split, i, k)
    right = optimal_parenthesization(split, k + 1, j)
    
    return f"({left} x {right})"

# Example usage
# Matrix dimensions: A(10x30), B(30x5), C(5x60)
dimensions = [10, 30, 5, 60]

min_operations = matrix_chain_order(dimensions)
print(f"Minimum operations: {min_operations}")
```

### 2. Palindrome Partitioning

#### Problem Statement
Partition string into minimum number of palindromic substrings.

#### Implementation
```java
def palindrome_partitioning(s):
    """Palindrome partitioning DP"""
    n = len(s)
    
    # is_palindrome[i][j] = True if s[i:j+1] is palindrome
    is_palindrome = [[False] * n for _ in range(n)]
    
    # Precompute palindromes
    for i in range(n):
        is_palindrome[i][i] = True
        for j in range(i + 1, n):
            if s[i] == s[j] and (j - i <= 1 or is_palindrome[i + 1][j - 1]):
                is_palindrome[i][j] = True
    
    # dp[i] = minimum cuts for s[i:]
    dp = [0] * n
    
    for i in range(1, n):
        dp[i] = float('infinity')
        for j in range(i):
            if is_palindrome[j][i - 1]:
                dp[i] = min(dp[i], dp[j] + 1)
    
    return dp[n - 1]

# Example usage
s = "ababbbabbababa"
min_cuts = palindrome_partitioning(s)
print(f"Minimum palindrome cuts for '{s}': {min_cuts}")
```

### 3. Maximum Sum Subarray

#### Problem Statement
Find subarray with maximum sum (Kadane's algorithm).

#### Implementation
```java
def max_subarray_sum(arr):
    """Maximum subarray sum using DP (Kadane's algorithm)"""
    if not arr:
        return 0
    
    max_ending_here = max_so_far = arr[0]
    
    for i in range(1, len(arr)):
        max_ending_here = max(arr[i], max_ending_here + arr[i])
        max_so_far = max(max_so_far, max_ending_here)
    
    return max_so_far

def max_subarray_with_indices(arr):
    """Maximum subarray sum with start/end indices"""
    if not arr:
        return 0, -1, -1
    
    max_sum = current_sum = arr[0]
    start = end = s = 0
    
    for i in range(1, len(arr)):
        if current_sum < 0:
            current_sum = arr[i]
            s = i
        else:
            current_sum += arr[i]
        
        if current_sum > max_sum:
            max_sum = current_sum
            start = s
            end = i
    
    return max_sum, start, end

# Example usage
arr = [-2, -3, 4, -1, -2, 1, 5, -3]
max_sum = max_subarray_sum(arr)
max_sum_with_indices, start, end = max_subarray_with_indices(arr)

print(f"Maximum subarray sum: {max_sum}")
print(f"Subarray: {arr[start:end+1]} (indices {start}-{end})")
```

---

## Performance Analysis

### Time Complexity Patterns

| Problem Type | Time Complexity | Space Complexity | Optimization |
|--------------|------------------|------------------|------------|
| Fibonacci | O(n) with DP, O(2^n) naive | O(n) | Space O(1) |
| Knapsack | O(n × W) | O(n × W) | Space O(W) |
| LCS | O(m × n) | O(m × n) | Space O(min(m,n)) |
| Edit Distance | O(m × n) | O(m × n) | Space O(min(m,n)) |
| Matrix Chain | O(n³) | O(n²) | Space O(n²) |

### Memory Optimization Techniques

#### Bitset DP
Use bitsets for boolean DP states.

```java
def subset_sum_bitset(arr, target):
    """Subset sum using bitset optimization"""
    # Bitset where bit i is set if sum i is achievable
    bitset = 1  # Only sum 0 is initially achievable
    
    for num in arr:
        bitset |= (bitset << num)
    
    # Check if target is achievable
    return (bitset >> target) & 1 == 1
```

#### Rolling Hash
Use rolling hash for string DP problems.

```java
def rolling_hash(s, base=256, mod=10**9+7):
    """Rolling hash for string"""
    n = len(s)
    hash_value = 0
    
    for i in range(n):
        hash_value = (hash_value * base + ord(s[i])) % mod
    
    return hash_value
```

---

## Practical Applications

### Resource Allocation
```java
def resource_allocation(tasks, resources):
    """Allocate resources to tasks maximizing total value"""
    n = len(tasks)
    m = len(resources)
    
    # dp[i][j] = max value using first i tasks with j resources
    dp = [[0] * (m + 1) for _ in range(n + 1)]
    
    for i in range(1, n + 1):
        for j in range(1, m + 1):
            # Option 1: Don't use task i-1
            dp[i][j] = dp[i - 1][j]
            
            # Option 2: Use task i-1 if resources allow
            if resources[i - 1] <= j:
                dp[i][j] = max(dp[i][j], 
                              dp[i - 1][j - resources[i - 1]] + tasks[i - 1])
    
    return dp[n][m]

# Example usage
tasks = [60, 100, 120]  # Task values
resources = [10, 20, 30]   # Resource requirements

max_value = resource_allocation(tasks, resources)
print(f"Maximum value with resource allocation: {max_value}")
```

### Text Processing
```java
def word_break(s, word_dict):
    """Word break problem - can string be segmented"""
    n = len(s)
    
    # dp[i] = True if s[:i] can be segmented
    dp = [False] * (n + 1)
    dp[0] = True
    
    for i in range(1, n + 1):
        for j in range(i):
            if dp[j] and s[j:i] in word_dict:
                dp[i] = True
                break
    
    return dp[n]

# Example usage
s = "leetcode"
word_dict = {"leet", "code"}

can_break = word_break(s, word_dict)
print(f"Can '{s}' be broken into dictionary words: {can_break}")
```

---

## DP Problem Solving Framework

### Step-by-Step Approach

#### 1. Identify DP Characteristics
```java
def analyze_problem_for_dp(problem):
    """Check if problem is suitable for DP"""
    characteristics = {
        "optimal_substructure": False,
        "overlapping_subproblems": False,
        "recursive_structure": False
    }
    
    # Analysis questions to consider:
    analysis = [
        "Can the optimal solution be built from optimal solutions to subproblems?",
        "Do subproblems repeat in the recursion tree?",
        "Is there a clear recursive formulation?",
        "What are the parameters that define subproblems?",
        "What is the base case?"
    ]
    
    return characteristics, analysis
```

#### 2. Define State
```java
def define_dp_state(parameters):
    """Define DP state based on problem parameters"""
    state = {
        "dimensions": len(parameters),
        "parameter_ranges": [],
        "state_size": 1
    }
    
    # Calculate state space size
    for param in parameters:
        if isinstance(param, range):
            state["parameter_ranges"].append(len(param))
            state["state_size"] *= len(param)
        else:
            state["parameter_ranges"].append("N/A")
    
    return state
```

#### 3. Design Transition
```java
def design_dp_transition(current_state, next_states):
    """Design DP transition function"""
    transition = {
        "from_state": current_state,
        "to_states": next_states,
        "transition_function": None,
        "cost_function": None
    }
    
    # Template for transition function
    def transition_func(state, decision):
        """Template for DP transition"""
        new_state = list(state)
        # Apply decision to create new state
        # Update new_state based on decision
        return tuple(new_state), cost  # Return new state and transition cost
    
    transition["transition_function"] = transition_func
    return transition
```

---

## Exercises and Practice

### Exercise 1: Implement Classic Problems
1. **Egg Dropping**: Find minimum drops needed
2. **Rod Cutting**: Maximize revenue from rod cuts
3. **Coin Collection**: Maximum coins collectable in grid
4. **Boolean Parenthesization**: Count valid parenthesis expressions

### Exercise 2: Optimize DP Solutions
1. Add space optimization to knapsack
2. Implement bitset DP for subset problems
3. Add rolling hash for string DP
4. Optimize LCS with Hirschberg's algorithm

### Exercise 3: Real-world Applications
1. **Investment Portfolio**: Optimize investment allocation
2. **Production Planning**: Minimize production costs
3. **Route Planning**: Find optimal route with constraints
4. **Game Strategy**: Optimal game move sequences

---

## Summary

Dynamic Programming is a powerful technique for solving optimization problems with overlapping subproblems.

### Key Takeaways
1. **Identify DP characteristics**: Optimal substructure and overlapping subproblems
2. **Choose appropriate approach**: Memoization vs tabulation
3. **Optimize space and time**: Use rolling arrays, bitsets, early termination
4. **Practice pattern recognition**: Learn to identify common DP patterns

### Common DP Patterns
1. **Linear DP**: One-dimensional DP tables
2. **Grid DP**: Two-dimensional DP on grids
3. **Tree DP**: DP on tree structures
4. **Bitmask DP**: DP with bitmask states
5. **Digit DP**: DP on digit positions

### Next Steps
- Practice more complex DP problems
- Learn advanced optimization techniques
- Study DP on trees and graphs
- Explore DP with probability and expectations

---

*Last Updated: March 2026*  
*Algorithms Covered: 15+ DP techniques*  
*Difficulty: Intermediate to Advanced*
