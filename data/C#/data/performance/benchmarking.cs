using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Threading.Tasks;

// Benchmarking Examples and Best Practices

public class BenchmarkingExamples
{
    // 1. Simple manual benchmarking
    public static void ManualBenchmarking()
    {
        Console.WriteLine("=== Manual Benchmarking ===");
        
        // Method to benchmark
        void MethodToBenchmark()
        {
            int sum = 0;
            for (int i = 0; i < 1000000; i++)
            {
                sum += i;
            }
        }
        
        // Warm up
        MethodToBenchmark();
        
        // Benchmark
        var stopwatch = Stopwatch.StartNew();
        MethodToBenchmark();
        stopwatch.Stop();
        
        Console.WriteLine($"Method execution time: {stopwatch.ElapsedMilliseconds} ms");
        Console.WriteLine($"Method execution time: {stopwatch.ElapsedTicks} ticks");
        Console.WriteLine();
    }
    
    // 2. Multiple iterations benchmarking
    public static void MultipleIterationsBenchmark()
    {
        Console.WriteLine("=== Multiple Iterations Benchmark ===");
        
        void MethodToBenchmark()
        {
            int sum = 0;
            for (int i = 0; i < 100000; i++)
            {
                sum += i * i;
            }
        }
        
        const int iterations = 10;
        var times = new List<long>();
        
        // Warm up
        MethodToBenchmark();
        
        // Run multiple iterations
        for (int i = 0; i < iterations; i++)
        {
            var stopwatch = Stopwatch.StartNew();
            MethodToBenchmark();
            stopwatch.Stop();
            times.Add(stopwatch.ElapsedMilliseconds);
        }
        
        // Calculate statistics
        var avgTime = times.Average();
        var minTime = times.Min();
        var maxTime = times.Max();
        var medianTime = times.OrderBy(t => t).Skip(times.Count / 2).First();
        
        Console.WriteLine($"Iterations: {iterations}");
        Console.WriteLine($"Average time: {avgTime:F2} ms");
        Console.WriteLine($"Min time: {minTime} ms");
        Console.WriteLine($"Max time: {maxTime} ms");
        Console.WriteLine($"Median time: {medianTime} ms");
        Console.WriteLine();
    }
    
    // 3. Memory benchmarking
    public static void MemoryBenchmarking()
    {
        Console.WriteLine("=== Memory Benchmarking ===");
        
        // Force garbage collection before measuring
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        long memoryBefore = GC.GetTotalMemory(false);
        
        // Allocate memory
        var list = new List<int>();
        for (int i = 0; i < 1000000; i++)
        {
            list.Add(i);
        }
        
        long memoryAfter = GC.GetTotalMemory(false);
        long memoryUsed = memoryAfter - memoryBefore;
        
        Console.WriteLine($"Memory before: {memoryBefore / 1024} KB");
        Console.WriteLine($"Memory after: {memoryAfter / 1024} KB");
        Console.WriteLine($"Memory used: {memoryUsed / 1024} KB");
        
        // Clean up
        list.Clear();
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        long memoryAfterCleanup = GC.GetTotalMemory(false);
        Console.WriteLine($"Memory after cleanup: {memoryAfterCleanup / 1024} KB");
        Console.WriteLine();
    }
    
    // 4. Comparing different implementations
    public static void ImplementationComparison()
    {
        Console.WriteLine("=== Implementation Comparison ===");
        
        const int iterations = 1000000;
        var data = Enumerable.Range(1, iterations).ToList();
        
        // Method 1: List with Add
        long ListWithAdd()
        {
            var list = new List<int>();
            foreach (var item in data)
            {
                list.Add(item * 2);
            }
            return list.Count;
        }
        
        // Method 2: LINQ Select
        long LinqSelect()
        {
            return data.Select(x => x * 2).Count();
        }
        
        // Method 3: Array with pre-allocation
        long ArrayWithPreAllocation()
        {
            var array = new int[iterations];
            for (int i = 0; i < iterations; i++)
            {
                array[i] = data[i] * 2;
            }
            return array.Length;
        }
        
        // Benchmark each method
        var methods = new[]
        {
            ("List with Add", new Func<long>(ListWithAdd)),
            ("LINQ Select", new Func<long>(LinqSelect)),
            ("Array with Pre-allocation", new Func<long>(ArrayWithPreAllocation))
        };
        
        foreach (var (name, method) in methods)
        {
            // Warm up
            method();
            
            // Benchmark
            var stopwatch = Stopwatch.StartNew();
            method();
            stopwatch.Stop();
            
            Console.WriteLine($"{name,-25}: {stopwatch.ElapsedMilliseconds,6} ms");
        }
        
        Console.WriteLine();
    }
    
    // 5. Async method benchmarking
    public static async Task AsyncBenchmarking()
    {
        Console.WriteLine("=== Async Method Benchmarking ===");
        
        // Synchronous version
        int SyncMethod()
        {
            int sum = 0;
            for (int i = 0; i < 1000; i++)
            {
                sum += i;
                Thread.Sleep(1); // Simulate work
            }
            return sum;
        }
        
        // Asynchronous version
        async Task<int> AsyncMethod()
        {
            int sum = 0;
            for (int i = 0; i < 1000; i++)
            {
                sum += i;
                await Task.Delay(1); // Simulate async work
            }
            return sum;
        }
        
        // Benchmark sync method
        var stopwatch = Stopwatch.StartNew();
        SyncMethod();
        stopwatch.Stop();
        Console.WriteLine($"Sync method: {stopwatch.ElapsedMilliseconds} ms");
        
        // Benchmark async method
        stopwatch.Restart();
        await AsyncMethod();
        stopwatch.Stop();
        Console.WriteLine($"Async method: {stopwatch.ElapsedMilliseconds} ms");
        
        Console.WriteLine();
    }
    
    // 6. String operations benchmarking
    public static void StringOperationsBenchmark()
    {
        Console.WriteLine("=== String Operations Benchmark ===");
        
        const int iterations = 10000;
        var strings = Enumerable.Range(1, iterations).Select(i => $"Item_{i}").ToList();
        
        // Method 1: String concatenation with +
        string ConcatWithPlus()
        {
            string result = "";
            foreach (var str in strings)
            {
                result += str + ",";
            }
            return result;
        }
        
        // Method 2: StringBuilder
        string ConcatWithStringBuilder()
        {
            var sb = new System.Text.StringBuilder();
            foreach (var str in strings)
            {
                sb.Append(str).Append(",");
            }
            return sb.ToString();
        }
        
        // Method 3: string.Join
        string ConcatWithJoin()
        {
            return string.Join(",", strings);
        }
        
        var methods = new[]
        {
            ("String +", new Func<string>(ConcatWithPlus)),
            ("StringBuilder", new Func<string>(ConcatWithStringBuilder)),
            ("string.Join", new Func<string>(ConcatWithJoin))
        };
        
        foreach (var (name, method) in methods)
        {
            // Warm up
            method();
            
            // Benchmark
            var stopwatch = Stopwatch.StartNew();
            method();
            stopwatch.Stop();
            
            Console.WriteLine($"{name,-15}: {stopwatch.ElapsedMilliseconds,6} ms");
        }
        
        Console.WriteLine();
    }
    
    // 7. Collection operations benchmarking
    public static void CollectionOperationsBenchmark()
    {
        Console.WriteLine("=== Collection Operations Benchmark ===");
        
        const int itemCount = 100000;
        var items = Enumerable.Range(1, itemCount).ToList();
        
        // Test different collection types for add operations
        void TestAdd<T>(ICollection<T> collection, string name) where T : new()
        {
            // Warm up
            var warmUpCollection = (ICollection<T>)Activator.CreateInstance(collection.GetType());
            for (int i = 0; i < 100; i++)
            {
                warmUpCollection.Add(new T());
            }
            
            // Benchmark
            var testCollection = (ICollection<T>)Activator.CreateInstance(collection.GetType());
            var stopwatch = Stopwatch.StartNew();
            
            for (int i = 0; i < itemCount; i++)
            {
                testCollection.Add(new T());
            }
            
            stopwatch.Stop();
            Console.WriteLine($"{name,-15}: Add {itemCount} items in {stopwatch.ElapsedMilliseconds,6} ms");
        }
        
        // Test different collection types for lookup operations
        void TestLookup<T>(IEnumerable<T> collection, string name)
        {
            var lookupCollection = collection.ToList();
            var random = new Random();
            
            // Warm up
            for (int i = 0; i < 100; i++)
            {
                lookupCollection.Contains(lookupCollection.ElementAt(random.Next(lookupCollection.Count())));
            }
            
            // Benchmark
            var stopwatch = Stopwatch.StartNew();
            
            for (int i = 0; i < 10000; i++)
            {
                lookupCollection.Contains(lookupCollection.ElementAt(random.Next(lookupCollection.Count())));
            }
            
            stopwatch.Stop();
            Console.WriteLine($"{name,-15}: 10000 lookups in {stopwatch.ElapsedMilliseconds,6} ms");
        }
        
        // Test add operations
        Console.WriteLine("Add Operations:");
        TestAdd(new List<int>(), "List<int>");
        TestAdd(new LinkedList<int>(), "LinkedList<int>");
        TestAdd(new HashSet<int>(), "HashSet<int>");
        
        Console.WriteLine();
        
        // Test lookup operations
        Console.WriteLine("Lookup Operations:");
        TestLookup(items.ToList(), "List<int>");
        TestLookup(new HashSet<int>(items), "HashSet<int>");
        TestLookup(items.ToDictionary(x => x), "Dictionary<int,int>");
        
        Console.WriteLine();
    }
    
    // 8. Algorithm complexity benchmarking
    public static void AlgorithmComplexityBenchmark()
    {
        Console.WriteLine("=== Algorithm Complexity Benchmark ===");
        
        // Linear search
        int LinearSearch(int[] array, int target)
        {
            for (int i = 0; i < array.Length; i++)
            {
                if (array[i] == target)
                    return i;
            }
            return -1;
        }
        
        // Binary search (requires sorted array)
        int BinarySearch(int[] array, int target)
        {
            int left = 0, right = array.Length - 1;
            while (left <= right)
            {
                int mid = left + (right - left) / 2;
                if (array[mid] == target)
                    return mid;
                if (array[mid] < target)
                    left = mid + 1;
                else
                    right = mid - 1;
            }
            return -1;
        }
        
        var sizes = new[] { 1000, 10000, 100000 };
        
        foreach (var size in sizes)
        {
            var array = Enumerable.Range(1, size).ToArray();
            var target = size / 2;
            
            // Benchmark linear search
            var stopwatch = Stopwatch.StartNew();
            LinearSearch(array, target);
            stopwatch.Stop();
            long linearTime = stopwatch.ElapsedMilliseconds;
            
            // Benchmark binary search
            stopwatch.Restart();
            BinarySearch(array, target);
            stopwatch.Stop();
            long binaryTime = stopwatch.ElapsedMilliseconds;
            
            Console.WriteLine($"Size: {size,-8} Linear: {linearTime,6} ms Binary: {binaryTime,6} ms Ratio: {(double)linearTime / binaryTime:F2}x");
        }
        
        Console.WriteLine();
    }
}

// Advanced Benchmarking Techniques
public class AdvancedBenchmarking
{
    // 1. High-resolution timing
    public static void HighResolutionTiming()
    {
        Console.WriteLine("=== High-Resolution Timing ===");
        
        void MethodToTime()
        {
            // Some work
            int sum = 0;
            for (int i = 0; i < 100000; i++)
            {
                sum += i;
            }
        }
        
        // Use Stopwatch with high resolution
        var stopwatch = new Stopwatch();
        stopwatch.Start();
        
        MethodToTime();
        
        stopwatch.Stop();
        
        Console.WriteLine($"High-resolution timing:");
        Console.WriteLine($"  Elapsed: {stopwatch.Elapsed}");
        Console.WriteLine($"  Milliseconds: {stopwatch.ElapsedMilliseconds}");
        Console.WriteLine($"  Ticks: {stopwatch.ElapsedTicks}");
        Console.WriteLine($"  Frequency: {Stopwatch.Frequency} Hz");
        Console.WriteLine($"  Nanoseconds per tick: {1000000000.0 / Stopwatch.Frequency:F2}");
        Console.WriteLine();
    }
    
    // 2. Statistical benchmarking
    public static void StatisticalBenchmarking()
    {
        Console.WriteLine("=== Statistical Benchmarking ===");
        
        void MethodToBenchmark()
        {
            int sum = 0;
            for (int i = 0; i < 10000; i++)
            {
                sum += i * i;
            }
        }
        
        const int iterations = 100;
        var times = new List<long>();
        
        // Warm up
        MethodToBenchmark();
        
        // Collect samples
        for (int i = 0; i < iterations; i++)
        {
            var stopwatch = Stopwatch.StartNew();
            MethodToBenchmark();
            stopwatch.Stop();
            times.Add(stopwatch.ElapsedTicks);
        }
        
        // Calculate statistics
        var avg = times.Average();
        var min = times.Min();
        var max = times.Max();
        var median = times.OrderBy(t => t).Skip(times.Count / 2).First();
        
        // Calculate standard deviation
        var variance = times.Select(t => Math.Pow(t - avg, 2)).Average();
        var stdDev = Math.Sqrt(variance);
        
        // Calculate percentiles
        var sortedTimes = times.OrderBy(t => t).ToList();
        double p95 = sortedTimes[(int)(sortedTimes.Count * 0.95)];
        double p99 = sortedTimes[(int)(sortedTimes.Count * 0.99)];
        
        Console.WriteLine($"Samples: {iterations}");
        Console.WriteLine($"Average: {avg:F2} ticks");
        Console.WriteLine($"Median: {median:F2} ticks");
        Console.WriteLine($"Min: {min} ticks");
        Console.WriteLine($"Max: {max} ticks");
        Console.WriteLine($"Std Dev: {stdDev:F2} ticks");
        Console.WriteLine($"95th percentile: {p95:F2} ticks");
        Console.WriteLine($"99th percentile: {p99:F2} ticks");
        Console.WriteLine();
    }
    
    // 3. Memory allocation profiling
    public static void MemoryAllocationProfiling()
    {
        Console.WriteLine("=== Memory Allocation Profiling ===");
        
        void ProfileAllocation<T>(Func<T> factory, string name, int iterations)
        {
            // Force GC before measuring
            GC.Collect();
            GC.WaitForPendingFinalizers();
            GC.Collect();
            
            long memoryBefore = GC.GetTotalMemory(false);
            
            // Create objects
            var objects = new List<T>();
            for (int i = 0; i < iterations; i++)
            {
                objects.Add(factory());
            }
            
            long memoryAfter = GC.GetTotalMemory(false);
            long memoryUsed = memoryAfter - memoryBefore;
            
            Console.WriteLine($"{name,-20}: {iterations,6} objects, {memoryUsed / 1024,6} KB total, {memoryUsed / iterations,3} bytes per object");
            
            // Clean up
            objects.Clear();
            GC.Collect();
        }
        
        // Profile different object types
        ProfileAllocation(() => new object(), "object", 10000);
        ProfileAllocation(() => new int(), "int", 10000);
        ProfileAllocation(() => new string('x', 100), "string(100)", 10000);
        ProfileAllocation(() => new List<int>(), "List<int>", 1000);
        ProfileAllocation(() => new int[10], "int[10]", 1000);
        
        Console.WriteLine();
    }
    
    // 4. GC pressure benchmarking
    public static void GCPressureBenchmark()
    {
        Console.WriteLine("=== GC Pressure Benchmark ===");
        
        void LowGCPressure()
        {
            // Reuse objects
            var list = new List<int>(1000);
            for (int i = 0; i < 1000; i++)
            {
                list.Clear();
                for (int j = 0; j < 100; j++)
                {
                    list.Add(j);
                }
            }
        }
        
        void HighGCPressure()
        {
            // Create many short-lived objects
            for (int i = 0; i < 1000; i++)
            {
                var list = new List<int>();
                for (int j = 0; j < 100; j++)
                {
                    list.Add(j);
                }
            }
        }
        
        // Benchmark low GC pressure
        var gen0Before = GC.CollectionCount(0);
        var gen1Before = GC.CollectionCount(1);
        var gen2Before = GC.CollectionCount(2);
        
        var stopwatch = Stopwatch.StartNew();
        LowGCPressure();
        stopwatch.Stop();
        
        var gen0After = GC.CollectionCount(0);
        var gen1After = GC.CollectionCount(1);
        var gen2After = GC.CollectionCount(2);
        
        Console.WriteLine("Low GC Pressure:");
        Console.WriteLine($"  Time: {stopwatch.ElapsedMilliseconds} ms");
        Console.WriteLine($"  Gen 0 collections: {gen0After - gen0Before}");
        Console.WriteLine($"  Gen 1 collections: {gen1After - gen1Before}");
        Console.WriteLine($"  Gen 2 collections: {gen2After - gen2Before}");
        
        // Benchmark high GC pressure
        gen0Before = GC.CollectionCount(0);
        gen1Before = GC.CollectionCount(1);
        gen2Before = GC.CollectionCount(2);
        
        stopwatch.Restart();
        HighGCPressure();
        stopwatch.Stop();
        
        gen0After = GC.CollectionCount(0);
        gen1After = GC.CollectionCount(1);
        gen2After = GC.CollectionCount(2);
        
        Console.WriteLine("\nHigh GC Pressure:");
        Console.WriteLine($"  Time: {stopwatch.ElapsedMilliseconds} ms");
        Console.WriteLine($"  Gen 0 collections: {gen0After - gen0Before}");
        Console.WriteLine($"  Gen 1 collections: {gen1After - gen1Before}");
        Console.WriteLine($"  Gen 2 collections: {gen2After - gen2Before}");
        
        Console.WriteLine();
    }
    
    // 5. Multithreaded benchmarking
    public static void MultithreadedBenchmarking()
    {
        Console.WriteLine("=== Multithreaded Benchmarking ===");
        
        void Workload()
        {
            int sum = 0;
            for (int i = 0; i < 100000; i++)
            {
                sum += i * i;
            }
        }
        
        // Sequential execution
        var stopwatch = Stopwatch.StartNew();
        Workload();
        Workload();
        Workload();
        Workload();
        stopwatch.Stop();
        long sequentialTime = stopwatch.ElapsedMilliseconds;
        
        // Parallel execution
        stopwatch.Restart();
        Parallel.Invoke(Workload, Workload, Workload, Workload);
        stopwatch.Stop();
        long parallelTime = stopwatch.ElapsedMilliseconds;
        
        Console.WriteLine($"Sequential execution: {sequentialTime} ms");
        Console.WriteLine($"Parallel execution: {parallelTime} ms");
        Console.WriteLine($"Speedup: {(double)sequentialTime / parallelTime:F2}x");
        Console.WriteLine($"Processor count: {Environment.ProcessorCount}");
        
        Console.WriteLine();
    }
}

// Benchmarking Best Practices
public class BenchmarkingBestPractices
{
    public static void DemonstrateBestPractices()
    {
        Console.WriteLine("=== Benchmarking Best Practices ===");
        
        // 1. Always warm up
        Console.WriteLine("1. Warm-up Example:");
        var stopwatch = Stopwatch.StartNew();
        SomeWork();
        stopwatch.Stop();
        Console.WriteLine($"   Cold run: {stopwatch.ElapsedMilliseconds} ms");
        
        stopwatch.Restart();
        SomeWork();
        stopwatch.Stop();
        Console.WriteLine($"   Warm run: {stopwatch.ElapsedMilliseconds} ms");
        
        // 2. Use multiple iterations
        Console.WriteLine("\n2. Multiple Iterations:");
        const int iterations = 5;
        var times = new List<long>();
        
        for (int i = 0; i < iterations; i++)
        {
            stopwatch.Restart();
            SomeWork();
            stopwatch.Stop();
            times.Add(stopwatch.ElapsedMilliseconds);
        }
        
        Console.WriteLine($"   Times: [{string.Join(", ", times)}] ms");
        Console.WriteLine($"   Average: {times.Average():F2} ms");
        
        // 3. Measure memory usage
        Console.WriteLine("\n3. Memory Measurement:");
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        long memoryBefore = GC.GetTotalMemory(false);
        var list = new List<int>();
        for (int i = 0; i < 100000; i++)
        {
            list.Add(i);
        }
        long memoryAfter = GC.GetTotalMemory(false);
        
        Console.WriteLine($"   Memory used: {(memoryAfter - memoryBefore) / 1024} KB");
        
        // 4. Consider environmental factors
        Console.WriteLine("\n4. Environmental Factors:");
        Console.WriteLine($"   Processor count: {Environment.ProcessorCount}");
        Console.WriteLine($"   OS version: {Environment.OSVersion}");
        Console.WriteLine($"   64-bit process: {Environment.Is64BitProcess}");
        Console.WriteLine($"   Working set: {Environment.WorkingSet / 1024 / 1024} MB");
        
        Console.WriteLine();
    }
    
    private static void SomeWork()
    {
        int sum = 0;
        for (int i = 0; i < 50000; i++)
        {
            sum += i * i;
        }
    }
}

// Main demonstration
public class BenchmarkingDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Benchmarking Demonstration ===");
        Console.WriteLine("Learn how to properly measure and compare code performance.\n");
        
        // Basic benchmarking
        BenchmarkingExamples.ManualBenchmarking();
        BenchmarkingExamples.MultipleIterationsBenchmark();
        BenchmarkingExamples.MemoryBenchmarking();
        BenchmarkingExamples.ImplementationComparison();
        BenchmarkingExamples.AsyncBenchmarking().GetAwaiter().GetResult();
        BenchmarkingExamples.StringOperationsBenchmark();
        BenchmarkingExamples.CollectionOperationsBenchmark();
        BenchmarkingExamples.AlgorithmComplexityBenchmark();
        
        // Advanced techniques
        AdvancedBenchmarking.HighResolutionTiming();
        AdvancedBenchmarking.StatisticalBenchmarking();
        AdvancedBenchmarking.MemoryAllocationProfiling();
        AdvancedBenchmarking.GCPressureBenchmark();
        AdvancedBenchmarking.MultithreadedBenchmarking();
        
        // Best practices
        BenchmarkingBestPractices.DemonstrateBestPractices();
        
        Console.WriteLine("=== Benchmarking Best Practices Summary ===");
        Console.WriteLine("1. Always warm up before measuring");
        Console.WriteLine("2. Run multiple iterations and use statistics");
        Console.WriteLine("3. Measure memory usage and GC pressure");
        Console.WriteLine("4. Consider environmental factors");
        Console.WriteLine("5. Use high-resolution timers (Stopwatch)");
        Console.WriteLine("6. Test with realistic data sizes");
        Console.WriteLine("7. Compare relative performance, not absolute");
        Console.WriteLine("8. Document test conditions and assumptions");
        Console.WriteLine("9. Use dedicated benchmarking frameworks for serious work");
        Console.WriteLine("10. Profile before optimizing");
        Console.WriteLine("11. Consider both time and space complexity");
        Console.WriteLine("12. Test on target hardware/environment");
    }
}
