"""
Dynamic Programming Examples
Demonstrates common dynamic programming patterns and solutions.
"""

def fibonacci_memo(n, memo={}):
    """
    Calculate nth Fibonacci number using memoization.
    
    Args:
        n (int): Position in Fibonacci sequence
        memo (dict): Memoization cache
        
    Returns:
        int: nth Fibonacci number
    """
    if n in memo:
        return memo[n]
    if n <= 1:
        return n
    
    memo[n] = fibonacci_memo(n-1, memo) + fibonacci_memo(n-2, memo)
    return memo[n]

def fibonacci_tab(n):
    """
    Calculate nth Fibonacci number using tabulation.
    
    Args:
        n (int): Position in Fibonacci sequence
        
    Returns:
        int: nth Fibonacci number
    """
    if n <= 1:
        return n
    
    dp = [0] * (n + 1)
    dp[0], dp[1] = 0, 1
    
    for i in range(2, n + 1):
        dp[i] = dp[i-1] + dp[i-2]
    
    return dp[n]

def knapsack_01(weights, values, capacity):
    """
    Solve 0/1 Knapsack problem using dynamic programming.
    
    Args:
        weights (list): Weights of items
        values (list): Values of items
        capacity (int): Maximum capacity of knapsack
        
    Returns:
        int: Maximum value achievable
    """
    n = len(weights)
    dp = [[0 for _ in range(capacity + 1)] for _ in range(n + 1)]
    
    for i in range(1, n + 1):
        for w in range(1, capacity + 1):
            if weights[i-1] <= w:
                dp[i][w] = max(values[i-1] + dp[i-1][w-weights[i-1]], dp[i-1][w])
            else:
                dp[i][w] = dp[i-1][w]
    
    return dp[n][capacity]

def longest_common_subsequence(s1, s2):
    """
    Find length of longest common subsequence between two strings.
    
    Args:
        s1 (str): First string
        s2 (str): Second string
        
    Returns:
        int: Length of LCS
    """
    m, n = len(s1), len(s2)
    dp = [[0] * (n + 1) for _ in range(m + 1)]
    
    for i in range(1, m + 1):
        for j in range(1, n + 1):
            if s1[i-1] == s2[j-1]:
                dp[i][j] = dp[i-1][j-1] + 1
            else:
                dp[i][j] = max(dp[i-1][j], dp[i][j-1])
    
    return dp[m][n]

def coin_change(coins, amount):
    """
    Find minimum number of coins needed to make amount.
    
    Args:
        coins (list): Available coin denominations
        amount (int): Target amount
        
    Returns:
        int: Minimum number of coins, or -1 if impossible
    """
    dp = [float('inf')] * (amount + 1)
    dp[0] = 0
    
    for coin in coins:
        for i in range(coin, amount + 1):
            dp[i] = min(dp[i], dp[i - coin] + 1)
    
    return dp[amount] if dp[amount] != float('inf') else -1

def main():
    """Demonstrate dynamic programming algorithms."""
    print("Dynamic Programming Algorithms Demonstration")
    print("=" * 50)
    
    # Fibonacci examples
    print("\n1. Fibonacci Sequence:")
    for n in range(10):
        fib_memo = fibonacci_memo(n)
        fib_tab = fibonacci_tab(n)
        print(f"F({n}) = {fib_memo} (memoization) = {fib_tab} (tabulation)")
    
    # Knapsack problem
    print("\n2. 0/1 Knapsack Problem:")
    weights = [1, 3, 4, 5]
    values = [1, 4, 5, 7]
    capacity = 7
    max_value = knapsack_01(weights, values, capacity)
    print(f"Weights: {weights}")
    print(f"Values: {values}")
    print(f"Capacity: {capacity}")
    print(f"Maximum value: {max_value}")
    
    # Longest Common Subsequence
    print("\n3. Longest Common Subsequence:")
    s1, s2 = "AGGTAB", "GXTXAYB"
    lcs_length = longest_common_subsequence(s1, s2)
    print(f"String 1: {s1}")
    print(f"String 2: {s2}")
    print(f"LCS Length: {lcs_length}")
    
    # Coin Change
    print("\n4. Coin Change Problem:")
    coins = [1, 3, 4]
    amount = 6
    min_coins = coin_change(coins, amount)
    print(f"Coins: {coins}")
    print(f"Amount: {amount}")
    print(f"Minimum coins needed: {min_coins}")

if __name__ == "__main__":
    main()
