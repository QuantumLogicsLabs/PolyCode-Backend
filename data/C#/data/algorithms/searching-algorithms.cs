using System;
using System.Collections.Generic;

// Searching Algorithms Implementation

public class SearchingAlgorithms
{
    // Linear Search - O(n) time complexity
    public static int LinearSearch(int[] arr, int target)
    {
        for (int i = 0; i < arr.Length; i++)
        {
            if (arr[i] == target)
                return i;
        }
        return -1; // Not found
    }
    
    // Binary Search - O(log n) time complexity (requires sorted array)
    public static int BinarySearch(int[] arr, int target)
    {
        int left = 0;
        int right = arr.Length - 1;
        
        while (left <= right)
        {
            int mid = left + (right - left) / 2;
            
            if (arr[mid] == target)
                return mid;
            
            if (arr[mid] < target)
                left = mid + 1;
            else
                right = mid - 1;
        }
        
        return -1; // Not found
    }
    
    // Recursive Binary Search
    public static int BinarySearchRecursive(int[] arr, int target, int left, int right)
    {
        if (left > right)
            return -1;
        
        int mid = left + (right - left) / 2;
        
        if (arr[mid] == target)
            return mid;
        
        if (arr[mid] < target)
            return BinarySearchRecursive(arr, target, mid + 1, right);
        else
            return BinarySearchRecursive(arr, target, left, mid - 1);
    }
    
    // Jump Search - O(√n) time complexity (for sorted arrays)
    public static int JumpSearch(int[] arr, int target)
    {
        int n = arr.Length;
        int step = (int)Math.Sqrt(n);
        int prev = 0;
        
        // Find the block where element could be present
        while (arr[Math.Min(step, n) - 1] < target)
        {
            prev = step;
            step += (int)Math.Sqrt(n);
            if (prev >= n)
                return -1;
        }
        
        // Linear search within the block
        while (arr[prev] < target)
        {
            prev++;
            if (prev == Math.Min(step, n))
                return -1;
        }
        
        // If element found
        if (arr[prev] == target)
            return prev;
        
        return -1;
    }
    
    // Interpolation Search - O(log log n) average case (for uniformly distributed sorted arrays)
    public static int InterpolationSearch(int[] arr, int target)
    {
        int left = 0;
        int right = arr.Length - 1;
        
        while (left <= right && target >= arr[left] && target <= arr[right])
        {
            if (left == right)
            {
                if (arr[left] == target)
                    return left;
                return -1;
            }
            
            // Calculate the position
            int pos = left + ((target - arr[left]) * (right - left)) / (arr[right] - arr[left]);
            
            if (arr[pos] == target)
                return pos;
            
            if (arr[pos] < target)
                left = pos + 1;
            else
                right = pos - 1;
        }
        
        return -1;
    }
    
    // Exponential Search - O(log n) time complexity
    public static int ExponentialSearch(int[] arr, int target)
    {
        if (arr[0] == target)
            return 0;
        
        // Find range for binary search
        int i = 1;
        while (i < arr.Length && arr[i] <= target)
            i = i * 2;
        
        // Binary search in found range
        return BinarySearchRecursive(arr, target, i / 2, Math.Min(i, arr.Length - 1));
    }
    
    // Fibonacci Search - O(log n) time complexity
    public static int FibonacciSearch(int[] arr, int target)
    {
        int n = arr.Length;
        
        // Initialize fibonacci numbers
        int fibM2 = 0; // (m-2)'th Fibonacci number
        int fibM1 = 1; // (m-1)'th Fibonacci number
        int fibM = fibM2 + fibM1; // m'th Fibonacci number
        
        // Find the smallest Fibonacci number greater than or equal to n
        while (fibM < n)
        {
            fibM2 = fibM1;
            fibM1 = fibM;
            fibM = fibM2 + fibM1;
        }
        
        // Marks the eliminated range from front
        int offset = -1;
        
        while (fibM > 1)
        {
            // Check if fibM2 is a valid location
            int i = Math.Min(offset + fibM2, n - 1);
            
            if (arr[i] < target)
            {
                fibM = fibM1;
                fibM1 = fibM2;
                fibM2 = fibM - fibM1;
                offset = i;
            }
            else if (arr[i] > target)
            {
                fibM = fibM2;
                fibM1 = fibM1 - fibM2;
                fibM2 = fibM - fibM1;
            }
            else
            {
                return i;
            }
        }
        
        // Compare the last element with target
        if (fibM1 > 0 && offset + 1 < n && arr[offset + 1] == target)
            return offset + 1;
        
        return -1;
    }
    
    // Ternary Search - O(log n) time complexity (for sorted arrays)
    public static int TernarySearch(int[] arr, int target, int left, int right)
    {
        if (left > right)
            return -1;
        
        // Find the two mid points
        int mid1 = left + (right - left) / 3;
        int mid2 = right - (right - left) / 3;
        
        if (arr[mid1] == target)
            return mid1;
        
        if (arr[mid2] == target)
            return mid2;
        
        if (target < arr[mid1])
            return TernarySearch(arr, target, left, mid1 - 1);
        else if (target > arr[mid2])
            return TernarySearch(arr, target, mid2 + 1, right);
        else
            return TernarySearch(arr, target, mid1 + 1, mid2 - 1);
    }
}

// Generic Search Algorithms
public class GenericSearchAlgorithms
{
    // Generic Linear Search
    public static int LinearSearch<T>(T[] arr, T target) where T : IComparable<T>
    {
        for (int i = 0; i < arr.Length; i++)
        {
            if (arr[i].CompareTo(target) == 0)
                return i;
        }
        return -1;
    }
    
    // Generic Binary Search
    public static int BinarySearch<T>(T[] arr, T target) where T : IComparable<T>
    {
        int left = 0;
        int right = arr.Length - 1;
        
        while (left <= right)
        {
            int mid = left + (right - left) / 2;
            
            int comparison = arr[mid].CompareTo(target);
            
            if (comparison == 0)
                return mid;
            
            if (comparison < 0)
                left = mid + 1;
            else
                right = mid - 1;
        }
        
        return -1;
    }
    
    // Find first occurrence in sorted array with duplicates
    public static int FindFirstOccurrence<T>(T[] arr, T target) where T : IComparable<T>
    {
        int left = 0;
        int right = arr.Length - 1;
        int result = -1;
        
        while (left <= right)
        {
            int mid = left + (right - left) / 2;
            
            int comparison = arr[mid].CompareTo(target);
            
            if (comparison == 0)
            {
                result = mid;
                right = mid - 1; // Continue searching in left half
            }
            else if (comparison < 0)
            {
                left = mid + 1;
            }
            else
            {
                right = mid - 1;
            }
        }
        
        return result;
    }
    
    // Find last occurrence in sorted array with duplicates
    public static int FindLastOccurrence<T>(T[] arr, T target) where T : IComparable<T>
    {
        int left = 0;
        int right = arr.Length - 1;
        int result = -1;
        
        while (left <= right)
        {
            int mid = left + (right - left) / 2;
            
            int comparison = arr[mid].CompareTo(target);
            
            if (comparison == 0)
            {
                result = mid;
                left = mid + 1; // Continue searching in right half
            }
            else if (comparison < 0)
            {
                left = mid + 1;
            }
            else
            {
                right = mid - 1;
            }
        }
        
        return result;
    }
    
    // Find closest element to target in sorted array
    public static int FindClosest<T>(T[] arr, T target) where T : IComparable<T>
    {
        if (arr.Length == 0)
            return -1;
        
        int left = 0;
        int right = arr.Length - 1;
        
        // Check if target is outside array bounds
        if (target.CompareTo(arr[0]) <= 0)
            return 0;
        
        if (target.CompareTo(arr[right]) >= 0)
            return right;
        
        // Binary search for closest
        while (left <= right)
        {
            int mid = left + (right - left) / 2;
            
            int comparison = arr[mid].CompareTo(target);
            
            if (comparison == 0)
                return mid;
            
            if (comparison < 0)
                left = mid + 1;
            else
                right = mid - 1;
        }
        
        // At this point, right < left, and target is between arr[right] and arr[left]
        // Return the closer one
        if (Math.Abs(arr[left].CompareTo(target)) < Math.Abs(arr[right].CompareTo(target)))
            return left;
        else
            return right;
    }
}

// Search Demonstration
public class SearchDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Searching Algorithms Demonstration ===");
        
        int[] sortedArray = { 2, 5, 8, 12, 16, 23, 38, 56, 72, 91 };
        int[] unsortedArray = { 23, 5, 72, 12, 91, 8, 56, 2, 16, 38 };
        
        int target = 23;
        
        Console.WriteLine($"Target value: {target}");
        Console.WriteLine($"Sorted array: [{string.Join(", ", sortedArray)}]");
        Console.WriteLine($"Unsorted array: [{string.Join(", ", unsortedArray)}]");
        
        // Test linear search
        Console.WriteLine("\n--- Linear Search ---");
        int linearIndex = SearchingAlgorithms.LinearSearch(unsortedArray, target);
        Console.WriteLine($"Linear Search result: {linearIndex}");
        
        // Test binary search
        Console.WriteLine("\n--- Binary Search ---");
        int binaryIndex = SearchingAlgorithms.BinarySearch(sortedArray, target);
        Console.WriteLine($"Binary Search result: {binaryIndex}");
        
        // Test recursive binary search
        int recursiveBinaryIndex = SearchingAlgorithms.BinarySearchRecursive(sortedArray, target, 0, sortedArray.Length - 1);
        Console.WriteLine($"Recursive Binary Search result: {recursiveBinaryIndex}");
        
        // Test jump search
        Console.WriteLine("\n--- Jump Search ---");
        int jumpIndex = SearchingAlgorithms.JumpSearch(sortedArray, target);
        Console.WriteLine($"Jump Search result: {jumpIndex}");
        
        // Test interpolation search
        Console.WriteLine("\n--- Interpolation Search ---");
        int interpolationIndex = SearchingAlgorithms.InterpolationSearch(sortedArray, target);
        Console.WriteLine($"Interpolation Search result: {interpolationIndex}");
        
        // Test exponential search
        Console.WriteLine("\n--- Exponential Search ---");
        int exponentialIndex = SearchingAlgorithms.ExponentialSearch(sortedArray, target);
        Console.WriteLine($"Exponential Search result: {exponentialIndex}");
        
        // Test fibonacci search
        Console.WriteLine("\n--- Fibonacci Search ---");
        int fibonacciIndex = SearchingAlgorithms.FibonacciSearch(sortedArray, target);
        Console.WriteLine($"Fibonacci Search result: {fibonacciIndex}");
        
        // Test ternary search
        Console.WriteLine("\n--- Ternary Search ---");
        int ternaryIndex = SearchingAlgorithms.TernarySearch(sortedArray, target, 0, sortedArray.Length - 1);
        Console.WriteLine($"Ternary Search result: {ternaryIndex}");
        
        // Test generic search algorithms
        Console.WriteLine("\n--- Generic Search Algorithms ---");
        string[] stringArray = { "apple", "banana", "cherry", "date", "fig", "grape" };
        string stringTarget = "cherry";
        
        int genericLinear = GenericSearchAlgorithms.LinearSearch(stringArray, stringTarget);
        int genericBinary = GenericSearchAlgorithms.BinarySearch(stringArray, stringTarget);
        
        Console.WriteLine($"Generic Linear Search ('{stringTarget}'): {genericLinear}");
        Console.WriteLine($"Generic Binary Search ('{stringTarget}'): {genericBinary}");
        
        // Test with duplicates
        Console.WriteLine("\n--- Search with Duplicates ---");
        int[] arrayWithDuplicates = { 2, 5, 5, 5, 8, 12, 12, 16, 23, 23, 23, 23, 38 };
        int duplicateTarget = 23;
        
        int firstOccurrence = GenericSearchAlgorithms.FindFirstOccurrence(arrayWithDuplicates, duplicateTarget);
        int lastOccurrence = GenericSearchAlgorithms.FindLastOccurrence(arrayWithDuplicates, duplicateTarget);
        
        Console.WriteLine($"Array with duplicates: [{string.Join(", ", arrayWithDuplicates)}]");
        Console.WriteLine($"First occurrence of {duplicateTarget}: {firstOccurrence}");
        Console.WriteLine($"Last occurrence of {duplicateTarget}: {lastOccurrence}");
        
        // Test closest element search
        Console.WriteLine("\n--- Closest Element Search ---");
        int closestTarget = 20;
        int closestIndex = GenericSearchAlgorithms.FindClosest(sortedArray, closestTarget);
        
        Console.WriteLine($"Closest to {closestTarget} in [{string.Join(", ", sortedArray)}]: index {closestIndex}, value {sortedArray[closestIndex]}");
    }
}

// Search Algorithm Performance Comparison
public class SearchPerformanceComparison
{
    public static void CompareSearchAlgorithms()
    {
        Console.WriteLine("\n=== Search Algorithm Performance Comparison ===");
        
        int[] sizes = { 1000, 10000, 100000 };
        
        foreach (int size in sizes)
        {
            Console.WriteLine($"\nArray size: {size}");
            int[] sortedArray = GenerateSortedArray(size);
            
            // Test with different target positions
            int[] targets = { sortedArray[0], sortedArray[size / 2], sortedArray[size - 1], size * 2 };
            string[] targetNames = { "First", "Middle", "Last", "Not Found" };
            
            for (int i = 0; i < targets.Length; i++)
            {
                Console.WriteLine($"\nTarget position: {targetNames[i]} ({targets[i]})");
                CompareSearchAlgorithms(sortedArray, targets[i]);
            }
        }
    }
    
    private static void CompareSearchAlgorithms(int[] array, int target)
    {
        const int iterations = 1000;
        
        // Linear Search
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        for (int i = 0; i < iterations; i++)
        {
            SearchingAlgorithms.LinearSearch(array, target);
        }
        stopwatch.Stop();
        Console.WriteLine($"Linear Search:      {stopwatch.ElapsedMilliseconds,6} ms");
        
        // Binary Search
        stopwatch.Restart();
        for (int i = 0; i < iterations; i++)
        {
            SearchingAlgorithms.BinarySearch(array, target);
        }
        stopwatch.Stop();
        Console.WriteLine($"Binary Search:      {stopwatch.ElapsedMilliseconds,6} ms");
        
        // Jump Search
        stopwatch.Restart();
        for (int i = 0; i < iterations; i++)
        {
            SearchingAlgorithms.JumpSearch(array, target);
        }
        stopwatch.Stop();
        Console.WriteLine($"Jump Search:        {stopwatch.ElapsedMilliseconds,6} ms");
        
        // Interpolation Search
        stopwatch.Restart();
        for (int i = 0; i < iterations; i++)
        {
            SearchingAlgorithms.InterpolationSearch(array, target);
        }
        stopwatch.Stop();
        Console.WriteLine($"Interpolation:      {stopwatch.ElapsedMilliseconds,6} ms");
    }
    
    private static int[] GenerateSortedArray(int size)
    {
        int[] array = new int[size];
        for (int i = 0; i < size; i++)
        {
            array[i] = i * 2; // Even numbers for uniform distribution
        }
        return array;
    }
}
