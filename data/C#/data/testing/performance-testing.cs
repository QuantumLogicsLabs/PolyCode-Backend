using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Threading.Tasks;

// Performance testing utilities
public class PerformanceTestResult
{
    public string TestName { get; set; }
    public long ElapsedMilliseconds { get; set; }
    public long MemoryUsedBytes { get; set; }
    public int Iterations { get; set; }
    public double AverageTimePerIteration { get; set; }
    public bool Passed { get; set; }
    public string FailureReason { get; set; }
    
    public override string ToString()
    {
        return $"{TestName}: {ElapsedMilliseconds}ms total, {AverageTimePerIteration:F2}ms avg, " +
               $"{MemoryUsedBytes / 1024}KB memory, {Iterations} iterations - {(Passed ? "PASSED" : "FAILED")}";
    }
}

public class PerformanceTestRunner
{
    private readonly List<PerformanceTestResult> _results = new();
    
    public async Task<PerformanceTestResult> RunTestAsync(string testName, Func<Task> testAction, 
        int iterations = 1000, long maxTimeMs = 1000, long maxMemoryKB = 1024)
    {
        var result = new PerformanceTestResult
        {
            TestName = testName,
            Iterations = iterations
        };
        
        // Warm up
        for (int i = 0; i < 10; i++)
        {
            await testAction();
        }
        
        // Force garbage collection before measuring
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        var memoryBefore = GC.GetTotalMemory(false);
        var stopwatch = Stopwatch.StartNew();
        
        // Run the test
        for (int i = 0; i < iterations; i++)
        {
            await testAction();
        }
        
        stopwatch.Stop();
        var memoryAfter = GC.GetTotalMemory(false);
        
        result.ElapsedMilliseconds = stopwatch.ElapsedMilliseconds;
        result.MemoryUsedBytes = memoryAfter - memoryBefore;
        result.AverageTimePerIteration = (double)result.ElapsedMilliseconds / iterations;
        
        // Check performance criteria
        result.Passed = result.ElapsedMilliseconds <= maxTimeMs && 
                       result.MemoryUsedBytes <= maxMemoryKB * 1024;
        
        if (!result.Passed)
        {
            var failures = new List<string>();
            if (result.ElapsedMilliseconds > maxTimeMs)
                failures.Add($"Time exceeded: {result.ElapsedMilliseconds}ms > {maxTimeMs}ms");
            if (result.MemoryUsedBytes > maxMemoryKB * 1024)
                failures.Add($"Memory exceeded: {result.MemoryUsedBytes / 1024}KB > {maxMemoryKB}KB");
            
            result.FailureReason = string.Join(", ", failures);
        }
        
        _results.Add(result);
        return result;
    }
    
    public PerformanceTestResult RunTest(string testName, Action testAction, 
        int iterations = 1000, long maxTimeMs = 1000, long maxMemoryKB = 1024)
    {
        return RunTestAsync(testName, async () => testAction(), iterations, maxTimeMs, maxMemoryKB).GetAwaiter().GetResult();
    }
    
    public void PrintResults()
    {
        Console.WriteLine("\n=== Performance Test Results ===");
        foreach (var result in _results)
        {
            Console.WriteLine(result);
        }
        
        var passedTests = _results.Count(r => r.Passed);
        var totalTests = _results.Count;
        Console.WriteLine($"\nSummary: {passedTests}/{totalTests} tests passed");
    }
}

// Test scenarios
public class PerformanceTestScenarios
{
    // String operations performance tests
    public static async Task<string> StringConcatenation(int iterations)
    {
        string result = "";
        for (int i = 0; i < iterations; i++)
        {
            result += i.ToString();
        }
        return result;
    }
    
    public static async Task<string> StringBuilderConcatenation(int iterations)
    {
        var sb = new System.Text.StringBuilder();
        for (int i = 0; i < iterations; i++)
        {
            sb.Append(i.ToString());
        }
        return sb.ToString();
    }
    
    public static async Task<string> StringJoinConcatenation(int iterations)
    {
        var strings = new string[iterations];
        for (int i = 0; i < iterations; i++)
        {
            strings[i] = i.ToString();
        }
        return string.Join("", strings);
    }
    
    // Collection operations performance tests
    public static async Task<List<int>> ListAddOperation(int iterations)
    {
        var list = new List<int>();
        for (int i = 0; i < iterations; i++)
        {
            list.Add(i);
        }
        return list;
    }
    
    public static async Task<HashSet<int>> HashSetAddOperation(int iterations)
    {
        var hashSet = new HashSet<int>();
        for (int i = 0; i < iterations; i++)
        {
            hashSet.Add(i);
        }
        return hashSet;
    }
    
    public static async Task<Dictionary<int, string>> DictionaryAddOperation(int iterations)
    {
        var dictionary = new Dictionary<int, string>();
        for (int i = 0; i < iterations; i++)
        {
            dictionary[i] = $"value_{i}";
        }
        return dictionary;
    }
    
    // LINQ operations performance tests
    public static async Task<int> LinqWhereOperation(List<int> data)
    {
        return data.Where(x => x % 2 == 0).Count();
    }
    
    public static async Task<int> ForeachWhereOperation(List<int> data)
    {
        int count = 0;
        foreach (var item in data)
        {
            if (item % 2 == 0)
                count++;
        }
        return count;
    }
    
    // Async operations performance tests
    public static async Task<List<string>> SequentialAsyncOperations(int count)
    {
        var results = new List<string>();
        for (int i = 0; i < count; i++)
        {
            var result = await SimulateAsyncOperation(i);
            results.Add(result);
        }
        return results;
    }
    
    public static async Task<List<string>> ParallelAsyncOperations(int count)
    {
        var tasks = new List<Task<string>>();
        for (int i = 0; i < count; i++)
        {
            tasks.Add(SimulateAsyncOperation(i));
        }
        
        var results = await Task.WhenAll(tasks);
        return results.ToList();
    }
    
    private static async Task<string> SimulateAsyncOperation(int index)
    {
        await Task.Delay(1); // Simulate async work
        return $"result_{index}";
    }
    
    // Algorithm performance tests
    public static async Task<int> BubbleSort(int[] array)
    {
        int operations = 0;
        int n = array.Length;
        
        for (int i = 0; i < n - 1; i++)
        {
            for (int j = 0; j < n - i - 1; j++)
            {
                operations++;
                if (array[j] > array[j + 1])
                {
                    // Swap
                    int temp = array[j];
                    array[j] = array[j + 1];
                    array[j + 1] = temp;
                }
            }
        }
        
        return operations;
    }
    
    public static async Task<int> QuickSort(int[] array)
    {
        int operations = 0;
        QuickSortRecursive(array, 0, array.Length - 1, ref operations);
        return operations;
    }
    
    private static void QuickSortRecursive(int[] array, int low, int high, ref int operations)
    {
        if (low < high)
        {
            int partitionIndex = Partition(array, low, high, ref operations);
            QuickSortRecursive(array, low, partitionIndex - 1, ref operations);
            QuickSortRecursive(array, partitionIndex + 1, high, ref operations);
        }
    }
    
    private static int Partition(int[] array, int low, int high, ref int operations)
    {
        int pivot = array[high];
        int i = low - 1;
        
        for (int j = low; j < high; j++)
        {
            operations++;
            if (array[j] < pivot)
            {
                i++;
                // Swap
                int temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
        }
        
        // Swap pivot
        int temp2 = array[i + 1];
        array[i + 1] = array[high];
        array[high] = temp2;
        
        return i + 1;
    }
}

// Performance test suite
public class PerformanceTestSuite
{
    public static async Task RunAllPerformanceTests()
    {
        var runner = new PerformanceTestRunner();
        
        // String concatenation tests
        Console.WriteLine("Running string concatenation performance tests...");
        
        await runner.RunTestAsync("String Concatenation (+=)", 
            () => PerformanceTestScenarios.StringConcatenation(1000), 
            iterations: 100, maxTimeMs: 5000, maxMemoryKB: 10240);
        
        await runner.RunTestAsync("StringBuilder Concatenation", 
            () => PerformanceTestScenarios.StringBuilderConcatenation(1000), 
            iterations: 100, maxTimeMs: 1000, maxMemoryKB: 1024);
        
        await runner.RunTestAsync("String.Join Concatenation", 
            () => PerformanceTestScenarios.StringJoinConcatenation(1000), 
            iterations: 100, maxTimeMs: 1000, maxMemoryKB: 1024);
        
        // Collection operations tests
        Console.WriteLine("Running collection operations performance tests...");
        
        await runner.RunTestAsync("List Add Operation", 
            () => PerformanceTestScenarios.ListAddOperation(10000), 
            iterations: 100, maxTimeMs: 2000, maxMemoryKB: 1024);
        
        await runner.RunTestAsync("HashSet Add Operation", 
            () => PerformanceTestScenarios.HashSetAddOperation(10000), 
            iterations: 100, maxTimeMs: 2000, maxMemoryKB: 1024);
        
        await runner.RunTestAsync("Dictionary Add Operation", 
            () => PerformanceTestScenarios.DictionaryAddOperation(10000), 
            iterations: 100, maxTimeMs: 2000, maxMemoryKB: 1024);
        
        // LINQ vs traditional loops
        Console.WriteLine("Running LINQ vs loops performance tests...");
        
        var testData = Enumerable.Range(1, 10000).ToList();
        
        await runner.RunTestAsync("LINQ Where Operation", 
            () => PerformanceTestScenarios.LinqWhereOperation(testData), 
            iterations: 1000, maxTimeMs: 1000, maxMemoryKB: 512);
        
        await runner.RunTestAsync("Foreach Where Operation", 
            () => PerformanceTestScenarios.ForeachWhereOperation(testData), 
            iterations: 1000, maxTimeMs: 1000, maxMemoryKB: 512);
        
        // Async operations tests
        Console.WriteLine("Running async operations performance tests...");
        
        await runner.RunTestAsync("Sequential Async Operations", 
            () => PerformanceTestScenarios.SequentialAsyncOperations(100), 
            iterations: 10, maxTimeMs: 5000, maxMemoryKB: 1024);
        
        await runner.RunTestAsync("Parallel Async Operations", 
            () => PerformanceTestScenarios.ParallelAsyncOperations(100), 
            iterations: 10, maxTimeMs: 1000, maxMemoryKB: 1024);
        
        // Algorithm performance tests
        Console.WriteLine("Running algorithm performance tests...");
        
        var unsortedData = Enumerable.Range(1, 1000).Reverse().ToArray();
        
        await runner.RunTestAsync("Bubble Sort", 
            () => PerformanceTestScenarios.BubbleSort(unsortedData.ToArray()), 
            iterations: 10, maxTimeMs: 10000, maxMemoryKB: 512);
        
        await runner.RunTestAsync("Quick Sort", 
            () => PerformanceTestScenarios.QuickSort(unsortedData.ToArray()), 
            iterations: 10, maxTimeMs: 1000, maxMemoryKB: 512);
        
        // Print all results
        runner.PrintResults();
    }
}

// Memory profiling utilities
public class MemoryProfiler
{
    public static void ProfileMemoryUsage(string testName, Action action)
    {
        // Force garbage collection before measuring
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        var memoryBefore = GC.GetTotalMemory(false);
        var stopwatch = Stopwatch.StartNew();
        
        action();
        
        stopwatch.Stop();
        var memoryAfter = GC.GetTotalMemory(false);
        
        Console.WriteLine($"{testName}:");
        Console.WriteLine($"  Time: {stopwatch.ElapsedMilliseconds}ms");
        Console.WriteLine($"  Memory before: {memoryBefore / 1024}KB");
        Console.WriteLine($"  Memory after: {memoryAfter / 1024}KB");
        Console.WriteLine($"  Memory used: {(memoryAfter - memoryBefore) / 1024}KB");
        Console.WriteLine();
    }
    
    public static void ProfileObjectCreation<T>(string objectName, Func<T> factory, int count = 1000)
    {
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        var memoryBefore = GC.GetTotalMemory(false);
        var objects = new List<T>();
        
        for (int i = 0; i < count; i++)
        {
            objects.Add(factory());
        }
        
        var memoryAfter = GC.GetTotalMemory(false);
        
        Console.WriteLine($"{objectName} creation ({count} objects):");
        Console.WriteLine($"  Total memory: {(memoryAfter - memoryBefore) / 1024}KB");
        Console.WriteLine($"  Average per object: {(memoryAfter - memoryBefore) / count} bytes");
        Console.WriteLine();
    }
}

// Program to run performance tests
public class PerformanceTestingProgram
{
    public static async Task Main(string[] args)
    {
        Console.WriteLine("=== Performance Testing Suite ===");
        
        // Run comprehensive performance tests
        await PerformanceTestSuite.RunAllPerformanceTests();
        
        // Run memory profiling
        Console.WriteLine("\n=== Memory Profiling ===");
        
        MemoryProfiler.ProfileMemoryUsage("String concatenation", () =>
        {
            string result = "";
            for (int i = 0; i < 10000; i++)
            {
                result += i.ToString();
            }
        });
        
        MemoryProfiler.ProfileMemoryUsage("StringBuilder concatenation", () =>
        {
            var sb = new System.Text.StringBuilder();
            for (int i = 0; i < 10000; i++)
            {
                sb.Append(i.ToString());
            }
        });
        
        MemoryProfiler.ProfileObjectCreation("List<int>", () => new List<int>(), 1000);
        MemoryProfiler.ProfileObjectCreation("Dictionary<int,string>", () => 
            new Dictionary<int, string>(), 1000);
        MemoryProfiler.ProfileObjectCreation("HashSet<int>", () => new HashSet<int>(), 1000);
        
        Console.WriteLine("Performance testing completed!");
    }
}
