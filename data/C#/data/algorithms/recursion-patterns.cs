using System;
using System.Collections.Generic;

// Recursion Patterns and Examples

public class RecursionPatterns
{
    // 1. Basic Recursion - Factorial
    public static long Factorial(int n)
    {
        Console.WriteLine($"Calculating factorial of {n}");
        
        // Base case
        if (n <= 1)
        {
            Console.WriteLine($"Base case reached: factorial({n}) = 1");
            return 1;
        }
        
        // Recursive case
        long result = n * Factorial(n - 1);
        Console.WriteLine($"factorial({n}) = {n} * factorial({n-1}) = {result}");
        return result;
    }
    
    // 2. Tail Recursion - Factorial (optimized)
    public static long TailFactorial(int n)
    {
        return TailFactorialHelper(n, 1);
    }
    
    private static long TailFactorialHelper(int n, long accumulator)
    {
        Console.WriteLine($"Tail factorial: n={n}, accumulator={accumulator}");
        
        // Base case
        if (n <= 1)
        {
            Console.WriteLine($"Base case reached: result = {accumulator}");
            return accumulator;
        }
        
        // Tail recursive call
        return TailFactorialHelper(n - 1, n * accumulator);
    }
    
    // 3. Tree Recursion - Fibonacci
    public static int FibonacciRecursive(int n)
    {
        Console.WriteLine($"Calculating fibonacci({n})");
        
        // Base cases
        if (n <= 1)
        {
            Console.WriteLine($"Base case: fibonacci({n}) = {n}");
            return n;
        }
        
        // Recursive case (two recursive calls)
        int result = FibonacciRecursive(n - 1) + FibonacciRecursive(n - 2);
        Console.WriteLine($"fibonacci({n}) = fibonacci({n-1}) + fibonacci({n-2}) = {result}");
        return result;
    }
    
    // 4. Indirect Recursion
    public static void IndirectRecursionA(int n)
    {
        Console.WriteLine($"Function A called with n = {n}");
        
        if (n <= 0)
        {
            Console.WriteLine("Base case reached in A");
            return;
        }
        
        Console.WriteLine($"A calling B with n = {n - 1}");
        IndirectRecursionB(n - 1);
    }
    
    public static void IndirectRecursionB(int n)
    {
        Console.WriteLine($"Function B called with n = {n}");
        
        if (n <= 0)
        {
            Console.WriteLine("Base case reached in B");
            return;
        }
        
        Console.WriteLine($"B calling A with n = {n - 1}");
        IndirectRecursionA(n - 1);
    }
    
    // 5. Nested Recursion
    public static int NestedRecursion(int n)
    {
        Console.WriteLine($"Nested recursion called with n = {n}");
        
        if (n > 100)
        {
            Console.WriteLine($"n > 100, returning n - 10 = {n - 10}");
            return n - 10;
        }
        
        Console.WriteLine($"n <= 100, calling nested recursion with nested recursion of {n}");
        return NestedRecursion(NestedRecursion(n + 11));
    }
    
    // 6. Recursive Sum of Array
    public static int RecursiveSum(int[] arr, int index)
    {
        Console.WriteLine($"Calculating sum at index {index}");
        
        // Base case
        if (index >= arr.Length)
        {
            Console.WriteLine("Base case: reached end of array, sum = 0");
            return 0;
        }
        
        // Recursive case
        int sum = arr[index] + RecursiveSum(arr, index + 1);
        Console.WriteLine($"Sum at index {index}: {arr[index]} + remaining = {sum}");
        return sum;
    }
    
    // 7. Recursive Binary Search
    public static int RecursiveBinarySearch(int[] arr, int target, int left, int right)
    {
        Console.WriteLine($"Binary search: left={left}, right={right}");
        
        // Base case
        if (left > right)
        {
            Console.WriteLine("Base case: target not found");
            return -1;
        }
        
        int mid = left + (right - left) / 2;
        Console.WriteLine($"Checking middle element arr[{mid}] = {arr[mid]}");
        
        if (arr[mid] == target)
        {
            Console.WriteLine($"Target found at index {mid}");
            return mid;
        }
        
        if (arr[mid] < target)
        {
            Console.WriteLine($"Target > arr[{mid}], searching right half");
            return RecursiveBinarySearch(arr, target, mid + 1, right);
        }
        else
        {
            Console.WriteLine($"Target < arr[{mid}], searching left half");
            return RecursiveBinarySearch(arr, target, left, mid - 1);
        }
    }
    
    // 8. Recursive String Reversal
    public static string RecursiveReverse(string s)
    {
        Console.WriteLine($"Reversing string: \"{s}\"");
        
        // Base case
        if (s.Length <= 1)
        {
            Console.WriteLine($"Base case: returning \"{s}\"");
            return s;
        }
        
        // Recursive case
        string reversed = RecursiveReverse(s.Substring(1)) + s[0];
        Console.WriteLine($"Reversed: \"{reversed}\"");
        return reversed;
    }
    
    // 9. Recursive Power Calculation
    public static double RecursivePower(double baseNum, int exponent)
    {
        Console.WriteLine($"Calculating {baseNum}^{exponent}");
        
        // Base case
        if (exponent == 0)
        {
            Console.WriteLine("Base case: any number^0 = 1");
            return 1;
        }
        
        if (exponent < 0)
        {
            Console.WriteLine($"Negative exponent: calculating 1/{baseNum}^{-exponent}");
            return 1 / RecursivePower(baseNum, -exponent);
        }
        
        // Recursive case with optimization (exponentiation by squaring)
        if (exponent % 2 == 0)
        {
            double halfPower = RecursivePower(baseNum, exponent / 2);
            Console.WriteLine($"Even exponent: {baseNum}^{exponent} = ({baseNum}^{exponent/2})^2 = {halfPower}^2");
            return halfPower * halfPower;
        }
        else
        {
            Console.WriteLine($"Odd exponent: {baseNum}^{exponent} = {baseNum} * {baseNum}^{exponent-1}");
            return baseNum * RecursivePower(baseNum, exponent - 1);
        }
    }
    
    // 10. Recursive GCD (Greatest Common Divisor)
    public static int RecursiveGCD(int a, int b)
    {
        Console.WriteLine($"Calculating GCD({a}, {b})");
        
        // Base case
        if (b == 0)
        {
            Console.WriteLine($"Base case: GCD({a}, 0) = {a}");
            return a;
        }
        
        // Recursive case (Euclidean algorithm)
        Console.WriteLine($"Recursive call: GCD({b}, {a % b})");
        return RecursiveGCD(b, a % b);
    }
    
    // 11. Recursive Permutations
    public static List<string> RecursivePermutations(string s)
    {
        Console.WriteLine($"Generating permutations of \"{s}\"");
        
        var permutations = new List<string>();
        
        // Base case
        if (s.Length == 1)
        {
            Console.WriteLine($"Base case: single character permutation \"{s}\"");
            permutations.Add(s);
            return permutations;
        }
        
        // Recursive case
        for (int i = 0; i < s.Length; i++)
        {
            char current = s[i];
            string remaining = s.Substring(0, i) + s.Substring(i + 1);
            
            Console.WriteLine($"Fixing '{current}' and permuting remaining \"{remaining}\"");
            
            var subPermutations = RecursivePermutations(remaining);
            
            foreach (var perm in subPermutations)
            {
                string fullPermutation = current + perm;
                Console.WriteLine($"Adding permutation: \"{fullPermutation}\"");
                permutations.Add(fullPermutation);
            }
        }
        
        return permutations;
    }
    
    // 12. Recursive Tree Traversal
    public static void RecursiveInOrderTraversal(TreeNode root)
    {
        if (root == null)
        {
            Console.WriteLine("Base case: null node");
            return;
        }
        
        Console.WriteLine($"Visiting left subtree of {root.Value}");
        RecursiveInOrderTraversal(root.Left);
        
        Console.WriteLine($"Processing node: {root.Value}");
        
        Console.WriteLine($"Visiting right subtree of {root.Value}");
        RecursiveInOrderTraversal(root.Right);
    }
    
    // 13. Recursive Backtracking - N-Queens Problem
    public static List<int[]> SolveNQueens(int n)
    {
        var solutions = new List<int[]>();
        var board = new int[n];
        Array.Fill(board, -1);
        
        Console.WriteLine($"Solving N-Queens for n = {n}");
        SolveNQueensHelper(board, 0, solutions);
        
        return solutions;
    }
    
    private static void SolveNQueensHelper(int[] board, int row, List<int[]> solutions)
    {
        Console.WriteLine($"Trying to place queen in row {row}");
        
        // Base case
        if (row == board.Length)
        {
            Console.WriteLine("Solution found!");
            solutions.Add((int[])board.Clone());
            return;
        }
        
        // Try placing queen in each column
        for (int col = 0; col < board.Length; col++)
        {
            Console.WriteLine($"Trying position ({row}, {col})");
            
            if (IsQueenSafe(board, row, col))
            {
                Console.WriteLine($"Position ({row}, {col}) is safe, placing queen");
                board[row] = col;
                
                SolveNQueensHelper(board, row + 1, solutions);
                
                Console.WriteLine($"Backtracking from row {row}");
                board[row] = -1; // Backtrack
            }
            else
            {
                Console.WriteLine($"Position ({row}, {col}) is not safe");
            }
        }
    }
    
    private static bool IsQueenSafe(int[] board, int row, int col)
    {
        for (int i = 0; i < row; i++)
        {
            // Check same column
            if (board[i] == col)
                return false;
            
            // Check diagonals
            if (Math.Abs(board[i] - col) == Math.Abs(i - row))
                return false;
        }
        
        return true;
    }
    
    // 14. Recursive Maze Solving
    public static bool SolveMaze(int[,] maze, int startX, int startY, int endX, int endY)
    {
        int rows = maze.GetLength(0);
        int cols = maze.GetLength(1);
        
        Console.WriteLine($"Solving maze from ({startX}, {startY}) to ({endX}, {endY})");
        
        return SolveMazeHelper(maze, startX, startY, endX, endY, new bool[rows, cols]);
    }
    
    private static bool SolveMazeHelper(int[,] maze, int x, int y, int endX, int endY, bool[,] visited)
    {
        Console.WriteLine($"Visiting position ({x}, {y})");
        
        // Base cases
        if (x < 0 || x >= maze.GetLength(0) || y < 0 || y >= maze.GetLength(1))
        {
            Console.WriteLine("Out of bounds");
            return false;
        }
        
        if (maze[x, y] == 1) // 1 represents wall
        {
            Console.WriteLine("Hit a wall");
            return false;
        }
        
        if (visited[x, y])
        {
            Console.WriteLine("Already visited");
            return false;
        }
        
        if (x == endX && y == endY)
        {
            Console.WriteLine("Reached destination!");
            return true;
        }
        
        // Mark as visited
        visited[x, y] = true;
        
        // Try all four directions
        Console.WriteLine("Trying right");
        if (SolveMazeHelper(maze, x + 1, y, endX, endY, visited))
            return true;
        
        Console.WriteLine("Trying down");
        if (SolveMazeHelper(maze, x, y + 1, endX, endY, visited))
            return true;
        
        Console.WriteLine("Trying left");
        if (SolveMazeHelper(maze, x - 1, y, endX, endY, visited))
            return true;
        
        Console.WriteLine("Trying up");
        if (SolveMazeHelper(maze, x, y - 1, endX, endY, visited))
            return true;
        
        Console.WriteLine("No path from this position, backtracking");
        return false;
    }
}

// Tree node class for tree traversal
public class TreeNode
{
    public int Value { get; set; }
    public TreeNode Left { get; set; }
    public TreeNode Right { get; set; }
    
    public TreeNode(int value)
    {
        Value = value;
        Left = Right = null;
    }
}

// Recursion Demonstration
public class RecursionDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Recursion Patterns Demonstration ===");
        Console.WriteLine("Recursion is a technique where a function calls itself to solve smaller instances of the same problem.\n");
        
        // Test basic recursion
        Console.WriteLine("1. Basic Recursion - Factorial");
        Console.WriteLine("--------------------------------");
        int factorialInput = 5;
        long factorialResult = RecursionPatterns.Factorial(factorialInput);
        Console.WriteLine($"Result: {factorialInput}! = {factorialResult}\n");
        
        // Test tail recursion
        Console.WriteLine("2. Tail Recursion - Factorial");
        Console.WriteLine("-------------------------------");
        long tailFactorialResult = RecursionPatterns.TailFactorial(factorialInput);
        Console.WriteLine($"Result: {factorialInput}! = {tailFactorialResult}\n");
        
        // Test tree recursion
        Console.WriteLine("3. Tree Recursion - Fibonacci");
        Console.WriteLine("------------------------------");
        int fibInput = 6;
        int fibResult = RecursionPatterns.FibonacciRecursive(fibInput);
        Console.WriteLine($"Result: fibonacci({fibInput}) = {fibResult}\n");
        
        // Test indirect recursion
        Console.WriteLine("4. Indirect Recursion");
        Console.WriteLine("--------------------");
        Console.WriteLine("Calling indirect recursion with n = 3:");
        RecursionPatterns.IndirectRecursionA(3);
        Console.WriteLine();
        
        // Test nested recursion
        Console.WriteLine("5. Nested Recursion");
        Console.WriteLine("------------------");
        int nestedResult = RecursionPatterns.NestedRecursion(95);
        Console.WriteLine($"Result: nested recursion(95) = {nestedResult}\n");
        
        // Test recursive sum
        Console.WriteLine("6. Recursive Sum of Array");
        Console.WriteLine("-------------------------");
        int[] array = { 1, 2, 3, 4, 5 };
        int sumResult = RecursionPatterns.RecursiveSum(array, 0);
        Console.WriteLine($"Result: sum of [{string.Join(", ", array)}] = {sumResult}\n");
        
        // Test recursive binary search
        Console.WriteLine("7. Recursive Binary Search");
        Console.WriteLine("--------------------------");
        int[] sortedArray = { 1, 3, 5, 7, 9, 11, 13, 15 };
        int target = 7;
        int searchResult = RecursionPatterns.RecursiveBinarySearch(sortedArray, target, 0, sortedArray.Length - 1);
        Console.WriteLine($"Result: {target} found at index {searchResult}\n");
        
        // Test string reversal
        Console.WriteLine("8. Recursive String Reversal");
        Console.WriteLine("----------------------------");
        string original = "hello";
        string reversed = RecursionPatterns.RecursiveReverse(original);
        Console.WriteLine($"Result: reverse(\"{original}\") = \"{reversed}\"\n");
        
        // Test power calculation
        Console.WriteLine("9. Recursive Power Calculation");
        Console.WriteLine("------------------------------");
        double powerResult = RecursionPatterns.RecursivePower(2, 8);
        Console.WriteLine($"Result: 2^8 = {powerResult}\n");
        
        // Test GCD
        Console.WriteLine("10. Recursive GCD");
        Console.WriteLine("----------------");
        int gcdResult = RecursionPatterns.RecursiveGCD(48, 18);
        Console.WriteLine($"Result: GCD(48, 18) = {gcdResult}\n");
        
        // Test permutations
        Console.WriteLine("11. Recursive Permutations");
        Console.WriteLine("---------------------------");
        string permString = "ABC";
        var permutations = RecursionPatterns.RecursivePermutations(permString);
        Console.WriteLine($"Result: permutations of \"{permString}\": [{string.Join(", ", permutations)}]\n");
        
        // Test tree traversal
        Console.WriteLine("12. Recursive Tree Traversal");
        Console.WriteLine("-----------------------------");
        var root = new TreeNode(1);
        root.Left = new TreeNode(2);
        root.Right = new TreeNode(3);
        root.Left.Left = new TreeNode(4);
        root.Left.Right = new TreeNode(5);
        root.Right.Left = new TreeNode(6);
        
        Console.WriteLine("In-order traversal:");
        RecursionPatterns.RecursiveInOrderTraversal(root);
        Console.WriteLine();
        
        // Test N-Queens
        Console.WriteLine("13. N-Queens Problem");
        Console.WriteLine("--------------------");
        int n = 4;
        var solutions = RecursionPatterns.SolveNQueens(n);
        Console.WriteLine($"Found {solutions.Count} solutions for {n}-Queens problem");
        for (int i = 0; i < solutions.Count; i++)
        {
            Console.WriteLine($"Solution {i + 1}: [{string.Join(", ", solutions[i])}]");
        }
        Console.WriteLine();
        
        // Test maze solving
        Console.WriteLine("14. Recursive Maze Solving");
        Console.WriteLine("-------------------------");
        int[,] maze = {
            { 0, 0, 0, 0, 0 },
            { 1, 1, 0, 1, 0 },
            { 0, 0, 0, 1, 0 },
            { 0, 1, 1, 1, 0 },
            { 0, 0, 0, 0, 0 }
        };
        
        bool mazeSolved = RecursionPatterns.SolveMaze(maze, 0, 0, 4, 4);
        Console.WriteLine($"Maze solved: {mazeSolved}\n");
        
        Console.WriteLine("=== Key Recursion Concepts ===");
        Console.WriteLine("1. Base Case: Condition that stops recursion");
        Console.WriteLine("2. Recursive Case: Function calls itself with smaller problem");
        Console.WriteLine("3. Progress Toward Base Case: Each call must move closer to base case");
        Console.WriteLine("4. Stack Overflow: Too many recursive calls can cause stack overflow");
        Console.WriteLine("5. Tail Recursion: Recursive call is the last operation");
        Console.WriteLine("6. Memoization: Cache results to avoid redundant calculations");
        Console.WriteLine("7. Backtracking: Explore possibilities and backtrack when needed");
    }
}
