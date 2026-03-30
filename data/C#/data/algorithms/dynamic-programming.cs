using System;
using System.Collections.Generic;

// Dynamic Programming Examples

public class DynamicProgramming
{
    // 1. Fibonacci Sequence - Classic DP Example
    public static long Fibonacci(int n)
    {
        if (n <= 1) return n;
        
        long[] dp = new long[n + 1];
        dp[0] = 0;
        dp[1] = 1;
        
        for (int i = 2; i <= n; i++)
        {
            dp[i] = dp[i - 1] + dp[i - 2];
        }
        
        return dp[n];
    }
    
    // Fibonacci with memoization (top-down approach)
    private static Dictionary<int, long> fibMemo = new Dictionary<int, long>();
    public static long FibonacciMemo(int n)
    {
        if (n <= 1) return n;
        
        if (fibMemo.ContainsKey(n))
            return fibMemo[n];
        
        fibMemo[n] = FibonacciMemo(n - 1) + FibonacciMemo(n - 2);
        return fibMemo[n];
    }
    
    // 2. Longest Common Subsequence (LCS)
    public static string LongestCommonSubsequence(string text1, string text2)
    {
        int m = text1.Length;
        int n = text2.Length;
        
        int[,] dp = new int[m + 1, n + 1];
        
        // Build DP table
        for (int i = 1; i <= m; i++)
        {
            for (int j = 1; j <= n; j++)
            {
                if (text1[i - 1] == text2[j - 1])
                {
                    dp[i, j] = dp[i - 1, j - 1] + 1;
                }
                else
                {
                    dp[i, j] = Math.Max(dp[i - 1, j], dp[i, j - 1]);
                }
            }
        }
        
        // Reconstruct LCS
        return ReconstructLCS(dp, text1, text2);
    }
    
    private static string ReconstructLCS(int[,] dp, string text1, string text2)
    {
        int i = text1.Length;
        int j = text2.Length;
        var lcs = new List<char>();
        
        while (i > 0 && j > 0)
        {
            if (text1[i - 1] == text2[j - 1])
            {
                lcs.Add(text1[i - 1]);
                i--;
                j--;
            }
            else if (dp[i - 1, j] > dp[i, j - 1])
            {
                i--;
            }
            else
            {
                j--;
            }
        }
        
        lcs.Reverse();
        return new string(lcs.ToArray());
    }
    
    // 3. Coin Change Problem
    public static int CoinChange(int[] coins, int amount)
    {
        if (amount == 0) return 0;
        
        int[] dp = new int[amount + 1];
        Array.Fill(dp, amount + 1); // Initialize with "infinity"
        dp[0] = 0;
        
        for (int i = 1; i <= amount; i++)
        {
            foreach (int coin in coins)
            {
                if (coin <= i)
                {
                    dp[i] = Math.Min(dp[i], dp[i - coin] + 1);
                }
            }
        }
        
        return dp[amount] > amount ? -1 : dp[amount];
    }
    
    // 4. Knapsack Problem (0/1)
    public static int Knapsack(int[] weights, int[] values, int capacity)
    {
        int n = weights.Length;
        int[,] dp = new int[n + 1, capacity + 1];
        
        for (int i = 1; i <= n; i++)
        {
            for (int w = 1; w <= capacity; w++)
            {
                if (weights[i - 1] <= w)
                {
                    dp[i, w] = Math.Max(
                        values[i - 1] + dp[i - 1, w - weights[i - 1]],
                        dp[i - 1, w]
                    );
                }
                else
                {
                    dp[i, w] = dp[i - 1, w];
                }
            }
        }
        
        return dp[n, capacity];
    }
    
    // 5. Longest Increasing Subsequence (LIS)
    public static int LongestIncreasingSubsequence(int[] nums)
    {
        if (nums.Length == 0) return 0;
        
        int[] dp = new int[nums.Length];
        Array.Fill(dp, 1);
        
        for (int i = 1; i < nums.Length; i++)
        {
            for (int j = 0; j < i; j++)
            {
                if (nums[i] > nums[j])
                {
                    dp[i] = Math.Max(dp[i], dp[j] + 1);
                }
            }
        }
        
        return dp.Max();
    }
    
    // 6. Edit Distance (Levenshtein Distance)
    public static int EditDistance(string word1, string word2)
    {
        int m = word1.Length;
        int n = word2.Length;
        
        int[,] dp = new int[m + 1, n + 1];
        
        // Initialize base cases
        for (int i = 0; i <= m; i++) dp[i, 0] = i;
        for (int j = 0; j <= n; j++) dp[0, j] = j;
        
        for (int i = 1; i <= m; i++)
        {
            for (int j = 1; j <= n; j++)
            {
                if (word1[i - 1] == word2[j - 1])
                {
                    dp[i, j] = dp[i - 1, j - 1];
                }
                else
                {
                    dp[i, j] = 1 + Math.Min(
                        Math.Min(dp[i - 1, j],    // Delete
                        dp[i, j - 1]),           // Insert
                        dp[i - 1, j - 1]         // Replace
                    );
                }
            }
        }
        
        return dp[m, n];
    }
    
    // 7. Maximum Subarray Sum (Kadane's Algorithm)
    public static int MaxSubarraySum(int[] nums)
    {
        if (nums.Length == 0) return 0;
        
        int maxSoFar = nums[0];
        int maxEndingHere = nums[0];
        
        for (int i = 1; i < nums.Length; i++)
        {
            maxEndingHere = Math.Max(nums[i], maxEndingHere + nums[i]);
            maxSoFar = Math.Max(maxSoFar, maxEndingHere);
        }
        
        return maxSoFar;
    }
    
    // 8. Unique Paths in Grid
    public static int UniquePaths(int m, int n)
    {
        int[,] dp = new int[m, n];
        
        // Initialize first row and column
        for (int i = 0; i < m; i++) dp[i, 0] = 1;
        for (int j = 0; j < n; j++) dp[0, j] = 1;
        
        for (int i = 1; i < m; i++)
        {
            for (int j = 1; j < n; j++)
            {
                dp[i, j] = dp[i - 1, j] + dp[i, j - 1];
            }
        }
        
        return dp[m - 1, n - 1];
    }
    
    // 9. Palindrome Partitioning
    public static int PalindromePartitioning(string s)
    {
        int n = s.Length;
        bool[,] isPalindrome = new bool[n, n];
        int[] dp = new int[n];
        
        // Precompute palindrome substrings
        for (int i = 0; i < n; i++)
        {
            isPalindrome[i, i] = true;
        }
        
        for (int len = 2; len <= n; len++)
        {
            for (int i = 0; i <= n - len; i++)
            {
                int j = i + len - 1;
                if (s[i] == s[j])
                {
                    if (len == 2 || isPalindrome[i + 1, j - 1])
                    {
                        isPalindrome[i, j] = true;
                    }
                }
            }
        }
        
        // Calculate minimum cuts
        for (int i = 0; i < n; i++)
        {
            if (isPalindrome[0, i])
            {
                dp[i] = 0;
            }
            else
            {
                dp[i] = int.MaxValue;
                for (int j = 0; j < i; j++)
                {
                    if (isPalindrome[j + 1, i])
                    {
                        dp[i] = Math.Min(dp[i], dp[j] + 1);
                    }
                }
            }
        }
        
        return dp[n - 1];
    }
    
    // 10. House Robber Problem
    public static int HouseRobber(int[] nums)
    {
        if (nums.Length == 0) return 0;
        if (nums.Length == 1) return nums[0];
        
        int[] dp = new int[nums.Length];
        dp[0] = nums[0];
        dp[1] = Math.Max(nums[0], nums[1]);
        
        for (int i = 2; i < nums.Length; i++)
        {
            dp[i] = Math.Max(dp[i - 1], dp[i - 2] + nums[i]);
        }
        
        return dp[nums.Length - 1];
    }
}

// DP Problem Solver with Detailed Explanations
public class DPProblemSolver
{
    public static void SolveFibonacci()
    {
        Console.WriteLine("=== Fibonacci Sequence ===");
        Console.WriteLine("Problem: Find the nth Fibonacci number");
        Console.WriteLine("Approach: Bottom-up DP with O(n) time and O(n) space");
        
        int n = 10;
        long result = DynamicProgramming.Fibonacci(n);
        Console.WriteLine($"Fibonacci({n}) = {result}");
        
        // Compare with memoization
        long memoResult = DynamicProgramming.FibonacciMemo(n);
        Console.WriteLine($"Fibonacci with memoization({n}) = {memoResult}");
        
        Console.WriteLine();
    }
    
    public static void SolveLCS()
    {
        Console.WriteLine("=== Longest Common Subsequence ===");
        Console.WriteLine("Problem: Find the longest subsequence common to two strings");
        Console.WriteLine("Approach: DP table with O(m*n) time and space");
        
        string text1 = "AGGTAB";
        string text2 = "GXTXAYB";
        
        string lcs = DynamicProgramming.LongestCommonSubsequence(text1, text2);
        Console.WriteLine($"LCS of '{text1}' and '{text2}' is: '{lcs}'");
        Console.WriteLine($"Length: {lcs.Length}");
        
        Console.WriteLine();
    }
    
    public static void SolveCoinChange()
    {
        Console.WriteLine("=== Coin Change Problem ===");
        Console.WriteLine("Problem: Find minimum number of coins to make up a given amount");
        Console.WriteLine("Approach: DP with O(amount * coins) time and O(amount) space");
        
        int[] coins = { 1, 3, 4 };
        int amount = 6;
        
        int minCoins = DynamicProgramming.CoinChange(coins, amount);
        Console.WriteLine($"Minimum coins to make {amount}: {minCoins}");
        
        if (minCoins != -1)
        {
            Console.WriteLine($"One possible solution uses {minCoins} coins");
        }
        
        Console.WriteLine();
    }
    
    public static void SolveKnapsack()
    {
        Console.WriteLine("=== 0/1 Knapsack Problem ===");
        Console.WriteLine("Problem: Maximize value within weight capacity");
        Console.WriteLine("Approach: DP with O(n*capacity) time and space");
        
        int[] weights = { 2, 3, 4, 5 };
        int[] values = { 3, 4, 5, 6 };
        int capacity = 5;
        
        int maxValue = DynamicProgramming.Knapsack(weights, values, capacity);
        Console.WriteLine($"Maximum value for capacity {capacity}: {maxValue}");
        
        Console.WriteLine();
    }
    
    public static void SolveLIS()
    {
        Console.WriteLine("=== Longest Increasing Subsequence ===");
        Console.WriteLine("Problem: Find the length of longest strictly increasing subsequence");
        Console.WriteLine("Approach: DP with O(n²) time and O(n) space");
        
        int[] nums = { 10, 9, 2, 5, 3, 7, 101, 18 };
        
        int lisLength = DynamicProgramming.LongestIncreasingSubsequence(nums);
        Console.WriteLine($"LIS length for [{string.Join(", ", nums)}]: {lisLength}");
        
        Console.WriteLine();
    }
    
    public static void SolveEditDistance()
    {
        Console.WriteLine("=== Edit Distance ===");
        Console.WriteLine("Problem: Minimum operations to convert one string to another");
        Console.WriteLine("Approach: DP with O(m*n) time and space");
        
        string word1 = "horse";
        string word2 = "ros";
        
        int distance = DynamicProgramming.EditDistance(word1, word2);
        Console.WriteLine($"Edit distance between '{word1}' and '{word2}': {distance}");
        
        Console.WriteLine();
    }
    
    public static void SolveMaxSubarray()
    {
        Console.WriteLine("=== Maximum Subarray Sum ===");
        Console.WriteLine("Problem: Find the maximum sum of a contiguous subarray");
        Console.WriteLine("Approach: Kadane's algorithm with O(n) time and O(1) space");
        
        int[] nums = { -2, 1, -3, 4, -1, 2, 1, -5, 4 };
        
        int maxSum = DynamicProgramming.MaxSubarraySum(nums);
        Console.WriteLine($"Maximum subarray sum for [{string.Join(", ", nums)}]: {maxSum}");
        
        Console.WriteLine();
    }
    
    public static void SolveUniquePaths()
    {
        Console.WriteLine("=== Unique Paths in Grid ===");
        Console.WriteLine("Problem: Number of unique paths from top-left to bottom-right");
        Console.WriteLine("Approach: DP with O(m*n) time and space");
        
        int m = 3, n = 7;
        
        int paths = DynamicProgramming.UniquePaths(m, n);
        Console.WriteLine($"Unique paths in {m}x{n} grid: {paths}");
        
        Console.WriteLine();
    }
    
    public static void SolvePalindromePartitioning()
    {
        Console.WriteLine("=== Palindrome Partitioning ===");
        Console.WriteLine("Problem: Minimum cuts to partition string into palindromes");
        Console.WriteLine("Approach: DP with O(n²) time and space");
        
        string s = "aab";
        
        int minCuts = DynamicProgramming.PalindromePartitioning(s);
        Console.WriteLine($"Minimum palindrome partitions for '{s}': {minCuts}");
        
        Console.WriteLine();
    }
    
    public static void SolveHouseRobber()
    {
        Console.WriteLine("=== House Robber Problem ===");
        Console.WriteLine("Problem: Maximum amount that can be robbed without alerting police");
        Console.WriteLine("Approach: DP with O(n) time and space");
        
        int[] houses = { 2, 7, 9, 3, 1 };
        
        int maxAmount = DynamicProgramming.HouseRobber(houses);
        Console.WriteLine($"Maximum amount that can be robbed: {maxAmount}");
        
        Console.WriteLine();
    }
}

// DP Performance Comparison
public class DPPerformanceComparison
{
    public static void CompareFibonacciApproaches()
    {
        Console.WriteLine("=== Fibonacci Performance Comparison ===");
        
        int[] testValues = { 10, 20, 30, 40 };
        
        Console.WriteLine("n\tBottom-up\tMemoization");
        
        foreach (int n in testValues)
        {
            var stopwatch1 = System.Diagnostics.Stopwatch.StartNew();
            long result1 = DynamicProgramming.Fibonacci(n);
            stopwatch1.Stop();
            
            var stopwatch2 = System.Diagnostics.Stopwatch.StartNew();
            long result2 = DynamicProgramming.FibonacciMemo(n);
            stopwatch2.Stop();
            
            Console.WriteLine($"{n}\t{stopwatch1.ElapsedTicks,8}\t{stopwatch2.ElapsedTicks,8}");
        }
        
        Console.WriteLine();
    }
    
    public static void CompareDPApproaches()
    {
        Console.WriteLine("=== DP Problem Performance ===");
        
        // Test LCS
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        string lcs = DynamicProgramming.LongestCommonSubsequence("AGGTAB", "GXTXAYB");
        stopwatch.Stop();
        Console.WriteLine($"LCS: {stopwatch.ElapsedTicks} ticks");
        
        // Test Edit Distance
        stopwatch.Restart();
        int editDist = DynamicProgramming.EditDistance("kitten", "sitting");
        stopwatch.Stop();
        Console.WriteLine($"Edit Distance: {stopwatch.ElapsedTicks} ticks");
        
        // Test Coin Change
        stopwatch.Restart();
        int coins = DynamicProgramming.CoinChange(new int[] { 1, 3, 4 }, 6);
        stopwatch.Stop();
        Console.WriteLine($"Coin Change: {stopwatch.ElapsedTicks} ticks");
        
        Console.WriteLine();
    }
}

// Main Demonstration
public class DynamicProgrammingDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Dynamic Programming Demonstration ===");
        Console.WriteLine("Dynamic programming solves problems by breaking them into subproblems");
        Console.WriteLine("and storing the results of subproblems to avoid recomputation.\n");
        
        // Solve individual DP problems
        DPProblemSolver.SolveFibonacci();
        DPProblemSolver.SolveLCS();
        DPProblemSolver.SolveCoinChange();
        DPProblemSolver.SolveKnapsack();
        DPProblemSolver.SolveLIS();
        DPProblemSolver.SolveEditDistance();
        DPProblemSolver.SolveMaxSubarray();
        DPProblemSolver.SolveUniquePaths();
        DPProblemSolver.SolvePalindromePartitioning();
        DPProblemSolver.SolveHouseRobber();
        
        // Performance comparisons
        DPPerformanceComparison.CompareFibonacciApproaches();
        DPPerformanceComparison.CompareDPApproaches();
        
        Console.WriteLine("=== Key DP Concepts ===");
        Console.WriteLine("1. Optimal Substructure: Optimal solution can be constructed from optimal solutions of subproblems");
        Console.WriteLine("2. Overlapping Subproblems: Same subproblems are solved multiple times");
        Console.WriteLine("3. Memoization: Top-down approach with caching");
        Console.WriteLine("4. Tabulation: Bottom-up approach with iterative filling");
        Console.WriteLine("5. State Definition: Define what each DP state represents");
        Console.WriteLine("6. Transition: How to move from one state to another");
        Console.WriteLine("7. Base Cases: Initial conditions for the DP");
    }
}
