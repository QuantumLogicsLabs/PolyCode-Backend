using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

// Performance Optimization Strategies

public class OptimizationStrategies
{
    // 1. Algorithm optimization
    public static void AlgorithmOptimization()
    {
        Console.WriteLine("=== Algorithm Optimization ===");
        
        // O(n²) vs O(n log n) sorting comparison
        var data = Enumerable.Range(1, 10000).Reverse().ToArray();
        
        // Bubble sort (O(n²))
        long BubbleSort(int[] arr)
        {
            var stopwatch = Stopwatch.StartNew();
            
            int n = arr.Length;
            for (int i = 0; i < n - 1; i++)
            {
                for (int j = 0; j < n - i - 1; j++)
                {
                    if (arr[j] > arr[j + 1])
                    {
                        int temp = arr[j];
                        arr[j] = arr[j + 1];
                        arr[j + 1] = temp;
                    }
                }
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Array.Sort (O(n log n))
        long ArraySort(int[] arr)
        {
            var stopwatch = Stopwatch.StartNew();
            Array.Sort(arr);
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare performance
        var bubbleData = (int[])data.Clone();
        var arrayData = (int[])data.Clone();
        
        long bubbleTime = BubbleSort(bubbleData);
        long arrayTime = ArraySort(arrayData);
        
        Console.WriteLine($"Bubble sort (O(n²)): {bubbleTime} ms");
        Console.WriteLine($"Array.Sort (O(n log n)): {arrayTime} ms");
        Console.WriteLine($"Speedup: {(double)bubbleTime / arrayTime:F2}x");
        
        Console.WriteLine();
    }
    
    // 2. String optimization
    public static void StringOptimization()
    {
        Console.WriteLine("=== String Optimization ===");
        
        const int iterations = 10000;
        var items = Enumerable.Range(1, iterations).Select(i => $"Item_{i}").ToList();
        
        // String concatenation with +
        long ConcatWithPlus()
        {
            var stopwatch = Stopwatch.StartNew();
            string result = "";
            foreach (var item in items)
            {
                result += item + ",";
            }
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // StringBuilder
        long ConcatWithStringBuilder()
        {
            var stopwatch = Stopwatch.StartNew();
            var sb = new StringBuilder();
            foreach (var item in items)
            {
                sb.Append(item).Append(",");
            }
            var result = sb.ToString();
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // string.Join
        long ConcatWithJoin()
        {
            var stopwatch = Stopwatch.StartNew();
            var result = string.Join(",", items);
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare methods
        long plusTime = ConcatWithPlus();
        long builderTime = ConcatWithStringBuilder();
        long joinTime = ConcatWithJoin();
        
        Console.WriteLine($"String +: {plusTime} ms");
        Console.WriteLine($"StringBuilder: {builderTime} ms");
        Console.WriteLine($"string.Join: {joinTime} ms");
        Console.WriteLine($"StringBuilder vs +: {(double)plusTime / builderTime:F2}x faster");
        Console.WriteLine($"string.Join vs +: {(double)plusTime / joinTime:F2}x faster");
        
        Console.WriteLine();
    }
    
    // 3. Collection optimization
    public static void CollectionOptimization()
    {
        Console.WriteLine("=== Collection Optimization ===");
        
        const int itemCount = 100000;
        var data = Enumerable.Range(1, itemCount).ToList();
        
        // List<T> vs LinkedList<T> for sequential access
        long ListPerformance()
        {
            var stopwatch = Stopwatch.StartNew();
            var list = new List<int>(data);
            foreach (var item in list)
            {
                // Sequential access
                var temp = item;
            }
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        long LinkedListPerformance()
        {
            var stopwatch = Stopwatch.StartNew();
            var linkedList = new LinkedList<int>(data);
            foreach (var item in linkedList)
            {
                // Sequential access
                var temp = item;
            }
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // HashSet<T> vs List<T> for lookups
        long ListLookup()
        {
            var stopwatch = Stopwatch.StartNew();
            var list = new List<int>(data);
            for (int i = 0; i < 1000; i++)
            {
                var contains = list.Contains(itemCount / 2);
            }
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        long HashSetLookup()
        {
            var stopwatch = Stopwatch.StartNew();
            var hashSet = new HashSet<int>(data);
            for (int i = 0; i < 1000; i++)
            {
                var contains = hashSet.Contains(itemCount / 2);
            }
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare performance
        long listTime = ListPerformance();
        long linkedListTime = LinkedListPerformance();
        long listLookupTime = ListLookup();
        long hashSetLookupTime = HashSetLookup();
        
        Console.WriteLine($"List<T> sequential: {listTime} ms");
        Console.WriteLine($"LinkedList<T> sequential: {linkedListTime} ms");
        Console.WriteLine($"List<T> lookup: {listLookupTime} ms");
        Console.WriteLine($"HashSet<T> lookup: {hashSetLookupTime} ms");
        Console.WriteLine($"HashSet vs List lookup: {(double)listLookupTime / hashSetLookupTime:F2}x faster");
        
        Console.WriteLine();
    }
    
    // 4. LINQ optimization
    public static void LINQOptimization()
    {
        Console.WriteLine("=== LINQ Optimization ===");
        
        var data = Enumerable.Range(1, 100000).ToList();
        
        // Deferred execution vs immediate execution
        long DeferredExecution()
        {
            var stopwatch = Stopwatch.StartNew();
            
            // Query is not executed yet
            var query = data.Where(x => x % 2 == 0)
                           .Select(x => x * x)
                           .Take(100);
            
            // Execute query multiple times
            for (int i = 0; i < 10; i++)
            {
                var result = query.Count();
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        long ImmediateExecution()
        {
            var stopwatch = Stopwatch.StartNew();
            
            // Execute query once and reuse results
            var result = data.Where(x => x % 2 == 0)
                           .Select(x => x * x)
                           .Take(100)
                           .ToList();
            
            // Reuse results
            for (int i = 0; i < 10; i++)
            {
                var count = result.Count;
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare performance
        long deferredTime = DeferredExecution();
        long immediateTime = ImmediateExecution();
        
        Console.WriteLine($"Deferred execution: {deferredTime} ms");
        Console.WriteLine($"Immediate execution: {immediateTime} ms");
        Console.WriteLine($"Speedup: {(double)deferredTime / immediateTime:F2}x");
        
        Console.WriteLine();
    }
    
    // 5. Memory optimization
    public static void MemoryOptimization()
    {
        Console.WriteLine("=== Memory Optimization ===");
        
        // Object pooling for frequently created objects
        class ObjectPool<T> where T : class, new()
        {
            private readonly Concurrent.ConcurrentBag<T> _objects = new();
            private readonly Func<T> _objectGenerator;
            private readonly int _maxObjects;
            
            public ObjectPool(int maxObjects = 100)
            {
                _objectGenerator = () => new T();
                _maxObjects = maxObjects;
            }
            
            public T Get()
            {
                if (_objects.TryTake(out T item))
                {
                    return item;
                }
                return _objectGenerator();
            }
            
            public void Return(T item)
            {
                if (_objects.Count < _maxObjects)
                {
                    _objects.Add(item);
                }
            }
        }
        
        // Test object pooling
        var pool = new ObjectPool<object>();
        
        long WithoutPooling()
        {
            var stopwatch = Stopwatch.StartNew();
            
            for (int i = 0; i < 10000; i++)
            {
                var obj = new object();
                // Use object
                var hash = obj.GetHashCode();
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        long WithPooling()
        {
            var stopwatch = Stopwatch.StartNew();
            
            for (int i = 0; i < 10000; i++)
            {
                var obj = pool.Get();
                // Use object
                var hash = obj.GetHashCode();
                pool.Return(obj);
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare memory usage
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        long memoryBefore = GC.GetTotalMemory(false);
        long withoutPoolTime = WithoutPooling();
        long memoryAfterWithout = GC.GetTotalMemory(false);
        
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        memoryBefore = GC.GetTotalMemory(false);
        long withPoolTime = WithPooling();
        long memoryAfterWith = GC.GetTotalMemory(false);
        
        Console.WriteLine($"Without pooling: {withoutPoolTime} ms, Memory: {(memoryAfterWithout - memoryBefore) / 1024} KB");
        Console.WriteLine($"With pooling: {withPoolTime} ms, Memory: {(memoryAfterWith - memoryBefore) / 1024} KB");
        
        Console.WriteLine();
    }
    
    // 6. Parallelization optimization
    public static void ParallelizationOptimization()
    {
        Console.WriteLine("=== Parallelization Optimization ===");
        
        var data = Enumerable.Range(1, 100000).ToList();
        
        // Sequential processing
        long SequentialProcessing()
        {
            var stopwatch = Stopwatch.StartNew();
            
            var results = new List<int>();
            foreach (var item in data)
            {
                results.Add(item * item);
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Parallel processing
        long ParallelProcessing()
        {
            var stopwatch = Stopwatch.StartNew();
            
            var results = new Concurrent.ConcurrentBag<int>();
            Parallel.ForEach(data, item => {
                results.Add(item * item);
            });
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare performance
        long sequentialTime = SequentialProcessing();
        long parallelTime = ParallelProcessing();
        
        Console.WriteLine($"Sequential: {sequentialTime} ms");
        Console.WriteLine($"Parallel: {parallelTime} ms");
        Console.WriteLine($"Speedup: {(double)sequentialTime / parallelTime:F2}x");
        Console.WriteLine($"CPU cores: {Environment.ProcessorCount}");
        
        Console.WriteLine();
    }
    
    // 7. Caching optimization
    public static void CachingOptimization()
    {
        Console.WriteLine("=== Caching Optimization ===");
        
        // Expensive computation
        int ExpensiveComputation(int n)
        {
            Thread.Sleep(10); // Simulate expensive work
            return n * n;
        }
        
        // Without caching
        long WithoutCaching()
        {
            var stopwatch = Stopwatch.StartNew();
            
            for (int i = 0; i < 1000; i++)
            {
                var result = ExpensiveComputation(i % 100); // Repeat computations
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // With caching
        var cache = new Dictionary<int, int>();
        
        long WithCaching()
        {
            var stopwatch = Stopwatch.StartNew();
            
            for (int i = 0; i < 1000; i++)
            {
                int key = i % 100;
                if (!cache.ContainsKey(key))
                {
                    cache[key] = ExpensiveComputation(key);
                }
                var result = cache[key];
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare performance
        long withoutCacheTime = WithoutCaching();
        long withCacheTime = WithCaching();
        
        Console.WriteLine($"Without caching: {withoutCacheTime} ms");
        Console.WriteLine($"With caching: {withCacheTime} ms");
        Console.WriteLine($"Speedup: {(double)withoutCacheTime / withCacheTime:F2}x");
        Console.WriteLine($"Cache hits: {cache.Count}");
        
        Console.WriteLine();
    }
    
    // 8. I/O optimization
    public static void IOOptimization()
    {
        Console.WriteLine("=== I/O Optimization ===");
        
        const int writeCount = 10000;
        var data = Enumerable.Range(1, writeCount).Select(i => $"Line {i}\n").ToArray();
        
        // Write line by line
        long WriteLineByLine()
        {
            var stopwatch = Stopwatch.StartNew();
            
            using (var writer = new System.IO.StreamWriter("output1.txt"))
            {
                foreach (var line in data)
                {
                    writer.WriteLine(line);
                }
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Write all at once
        long WriteAllAtOnce()
        {
            var stopwatch = Stopwatch.StartNew();
            
            var allText = string.Concat(data);
            System.IO.File.WriteAllText("output2.txt", allText);
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Write with buffer
        long WriteWithBuffer()
        {
            var stopwatch = Stopwatch.StartNew();
            
            using (var writer = new System.IO.StreamWriter("output3.txt"))
            {
                writer.Write(string.Concat(data));
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare performance
        long lineByLineTime = WriteLineByLine();
        long allAtOnceTime = WriteAllAtOnce();
        long bufferedTime = WriteWithBuffer();
        
        Console.WriteLine($"Line by line: {lineByLineTime} ms");
        Console.WriteLine($"All at once: {allAtOnceTime} ms");
        Console.WriteLine($"Buffered: {bufferedTime} ms");
        Console.WriteLine($"Line by line vs buffered: {(double)lineByLineTime / bufferedTime:F2}x faster");
        
        // Clean up files
        System.IO.File.Delete("output1.txt");
        System.IO.File.Delete("output2.txt");
        System.IO.File.Delete("output3.txt");
        
        Console.WriteLine();
    }
}

// Advanced Optimization Strategies
public class AdvancedOptimization
{
    // 1. JIT optimization awareness
    public static void JITOptimization()
    {
        Console.WriteLine("=== JIT Optimization Awareness ===");
        
        // Method that will be optimized by JIT after warm-up
        void OptimizedMethod()
        {
            int sum = 0;
            for (int i = 0; i < 100000; i++)
            {
                sum += i * i;
            }
        }
        
        // Warm up (JIT compilation)
        var stopwatch = Stopwatch.StartNew();
        OptimizedMethod();
        stopwatch.Stop();
        long warmUpTime = stopwatch.ElapsedMilliseconds;
        
        // Optimized execution
        stopwatch.Restart();
        OptimizedMethod();
        stopwatch.Stop();
        long optimizedTime = stopwatch.ElapsedMilliseconds;
        
        Console.WriteLine($"Warm up (JIT compilation): {warmUpTime} ms");
        Console.WriteLine($"Optimized execution: {optimizedTime} ms");
        Console.WriteLine($"JIT overhead: {(double)warmUpTime / optimizedTime:F2}x");
        
        Console.WriteLine();
    }
    
    // 2. Value type optimization
    public static void ValueTypeOptimization()
    {
        Console.WriteLine("=== Value Type Optimization ===");
        
        // Struct vs Class performance
        struct PointStruct
        {
            public double X, Y;
            public PointStruct(double x, double y) { X = x; Y = y; }
        }
        
        class PointClass
        {
            public double X, Y;
            public PointClass(double x, double y) { X = x; Y = y; }
        }
        
        const int count = 1000000;
        
        // Struct performance
        long StructPerformance()
        {
            var stopwatch = Stopwatch.StartNew();
            
            var points = new PointStruct[count];
            for (int i = 0; i < count; i++)
            {
                points[i] = new PointStruct(i, i * 2);
            }
            
            double sum = 0;
            foreach (var point in points)
            {
                sum += point.X + point.Y;
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Class performance
        long ClassPerformance()
        {
            var stopwatch = Stopwatch.StartNew();
            
            var points = new PointClass[count];
            for (int i = 0; i < count; i++)
            {
                points[i] = new PointClass(i, i * 2);
            }
            
            double sum = 0;
            foreach (var point in points)
            {
                sum += point.X + point.Y;
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare performance
        long structTime = StructPerformance();
        long classTime = ClassPerformance();
        
        Console.WriteLine($"Struct: {structTime} ms");
        Console.WriteLine($"Class: {classTime} ms");
        Console.WriteLine($"Struct vs Class: {(double)classTime / structTime:F2}x");
        
        Console.WriteLine();
    }
    
    // 3. Boxing/unboxing optimization
    public static void BoxingOptimization()
    {
        Console.WriteLine("=== Boxing/Unboxing Optimization ===");
        
        const int iterations = 1000000;
        
        // With boxing
        long WithBoxing()
        {
            var stopwatch = Stopwatch.StartNew();
            
            var list = new ArrayList();
            for (int i = 0; i < iterations; i++)
            {
                list.Add(i); // Boxing occurs here
            }
            
            int sum = 0;
            foreach (int item in list)
            {
                sum += item; // Unboxing occurs here
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Without boxing
        long WithoutBoxing()
        {
            var stopwatch = Stopwatch.StartNew();
            
            var list = new List<int>();
            for (int i = 0; i < iterations; i++)
            {
                list.Add(i); // No boxing
            }
            
            int sum = 0;
            foreach (int item in list)
            {
                sum += item; // No unboxing
            }
            
            stopwatch.Stop();
            return stopwatch.ElapsedMilliseconds;
        }
        
        // Compare performance
        long boxingTime = WithBoxing();
        long noBoxingTime = WithoutBoxing();
        
        Console.WriteLine($"With boxing: {boxingTime} ms");
        Console.WriteLine($"Without boxing: {noBoxingTime} ms");
        Console.WriteLine($"No boxing vs boxing: {(double)boxingTime / noBoxingTime:F2}x faster");
        
        Console.WriteLine();
    }
    
    // 4. Async/await optimization
    public static async Task AsyncOptimization()
    {
        Console.WriteLine("=== Async/Await Optimization ===");
        
        // ConfigureAwait(false) optimization
        async Task<int> AsyncMethodConfigureAwait()
        {
            await Task.Delay(100).ConfigureAwait(false);
            return 42;
        }
        
        async Task<int> AsyncMethodNoConfigureAwait()
        {
            await Task.Delay(100);
            return 42;
        }
        
        // Measure ConfigureAwait(false)
        var stopwatch = Stopwatch.StartNew();
        for (int i = 0; i < 100; i++)
        {
            await AsyncMethodConfigureAwait();
        }
        stopwatch.Stop();
        long configureAwaitTime = stopwatch.ElapsedMilliseconds;
        
        // Measure without ConfigureAwait
        stopwatch.Restart();
        for (int i = 0; i < 100; i++)
        {
            await AsyncMethodNoConfigureAwait();
        }
        stopwatch.Stop();
        long noConfigureAwaitTime = stopwatch.ElapsedMilliseconds;
        
        Console.WriteLine($"ConfigureAwait(false): {configureAwaitTime} ms");
        Console.WriteLine($"No ConfigureAwait: {noConfigureAwaitTime} ms");
        
        Console.WriteLine();
    }
    
    // 5. Reflection optimization
    public static void ReflectionOptimization()
    {
        Console.WriteLine("=== Reflection Optimization ===");
        
        // Direct method call
        void DirectMethod()
        {
            // Simple method
            var result = Math.Sqrt(100);
        }
        
        // Reflection method call
        void ReflectionMethod()
        {
            var method = typeof(Math).GetMethod("Sqrt");
            var result = method.Invoke(null, new object[] { 100 });
        }
        
        // Cached delegate
        static readonly Func<double, double> CachedSqrt = 
            (Func<double, double>)Delegate.CreateDelegate(
                typeof(Func<double, double>),
                typeof(Math).GetMethod("Sqrt"));
        
        void CachedDelegateMethod()
        {
            var result = CachedSqrt(100);
        }
        
        const int iterations = 100000;
        
        // Measure direct call
        var stopwatch = Stopwatch.StartNew();
        for (int i = 0; i < iterations; i++)
        {
            DirectMethod();
        }
        stopwatch.Stop();
        long directTime = stopwatch.ElapsedMilliseconds;
        
        // Measure reflection call
        stopwatch.Restart();
        for (int i = 0; i < iterations; i++)
        {
            ReflectionMethod();
        }
        stopwatch.Stop();
        long reflectionTime = stopwatch.ElapsedMilliseconds;
        
        // Measure cached delegate call
        stopwatch.Restart();
        for (int i = 0; i < iterations; i++)
        {
            CachedDelegateMethod();
        }
        stopwatch.Stop();
        long cachedDelegateTime = stopwatch.ElapsedMilliseconds;
        
        Console.WriteLine($"Direct call: {directTime} ms");
        Console.WriteLine($"Reflection call: {reflectionTime} ms");
        Console.WriteLine($"Cached delegate: {cachedDelegateTime} ms");
        Console.WriteLine($"Reflection vs Direct: {(double)reflectionTime / directTime:F2}x slower");
        Console.WriteLine($"Reflection vs Cached: {(double)reflectionTime / cachedDelegateTime:F2}x slower");
        
        Console.WriteLine();
    }
}

// Optimization Best Practices
public class OptimizationBestPractices
{
    public static void DemonstrateBestPractices()
    {
        Console.WriteLine("=== Optimization Best Practices ===");
        
        // 1. Profile before optimizing
        Console.WriteLine("1. Profile Before Optimizing:");
        Console.WriteLine("   Don't optimize code that isn't a bottleneck");
        Console.WriteLine("   Use profiling tools to identify hot spots");
        
        // 2. Measure after optimizing
        Console.WriteLine("\n2. Measure After Optimizing:");
        Console.WriteLine("   Verify that optimizations actually improve performance");
        Console.WriteLine("   Consider the trade-offs (readability, maintainability)");
        
        // 3. Choose the right algorithm
        Console.WriteLine("\n3. Choose the Right Algorithm:");
        Console.WriteLine("   Algorithm choice has biggest impact on performance");
        Console.WriteLine("   Consider time and space complexity");
        
        // 4. Use appropriate data structures
        Console.WriteLine("\n4. Use Appropriate Data Structures:");
        Console.WriteLine("   List<T> for sequential access");
        Console.WriteLine("   HashSet<T> for fast lookups");
        Console.WriteLine("   Dictionary<TKey,TValue> for key-value pairs");
        
        // 5. Avoid premature optimization
        Console.WriteLine("\n5. Avoid Premature Optimization:");
        Console.WriteLine("   "Make it work, then make it fast" - Donald Knuth");
        Console.WriteLine("   Optimize based on actual usage patterns");
        
        // 6. Consider memory vs CPU trade-offs
        Console.WriteLine("\n6. Consider Memory vs CPU Trade-offs:");
        Console.WriteLine("   Sometimes using more memory saves CPU cycles");
        Console.WriteLine("   Caching trades memory for speed");
        
        // 7. Use built-in optimizations
        Console.WriteLine("\n7. Use Built-in Optimizations:");
        Console.WriteLine("   JIT compiler optimizations");
        Console.WriteLine("   Framework-optimized collections");
        Console.WriteLine("   Hardware acceleration when available");
        
        Console.WriteLine();
    }
}

// Main demonstration
public class OptimizationStrategiesDemo
{
    public static async Task Main(string[] args)
    {
        Console.WriteLine("=== Performance Optimization Strategies ===");
        Console.WriteLine("Learn how to optimize C# code for better performance.\n");
        
        // Basic optimizations
        OptimizationStrategies.AlgorithmOptimization();
        OptimizationStrategies.StringOptimization();
        OptimizationStrategies.CollectionOptimization();
        OptimizationStrategies.LINQOptimization();
        OptimizationStrategies.MemoryOptimization();
        OptimizationStrategies.ParallelizationOptimization();
        OptimizationStrategies.CachingOptimization();
        OptimizationStrategies.IOOptimization();
        
        // Advanced optimizations
        AdvancedOptimization.JITOptimization();
        AdvancedOptimization.ValueTypeOptimization();
        AdvancedOptimization.BoxingOptimization();
        await AdvancedOptimization.AsyncOptimization();
        AdvancedOptimization.ReflectionOptimization();
        
        // Best practices
        OptimizationBestPractices.DemonstrateBestPractices();
        
        Console.WriteLine("=== Optimization Strategies Summary ===");
        Console.WriteLine("1. Profile before optimizing");
        Console.WriteLine("2. Choose the right algorithm and data structures");
        Console.WriteLine("3. Optimize hot paths, not everything");
        Console.WriteLine("4. Use StringBuilder for string concatenation");
        Console.WriteLine("5. Use appropriate collection types");
        Console.WriteLine("6. Consider value types for small data");
        Console.WriteLine("7. Avoid unnecessary boxing/unboxing");
        Console.WriteLine("8. Use caching for expensive operations");
        Console.WriteLine("9. Parallelize CPU-bound operations");
        Console.WriteLine("10. Use async/await for I/O-bound operations");
        Console.WriteLine("11. Minimize allocations in tight loops");
        Console.WriteLine("12. Use ConfigureAwait(false) in library code");
        Console.WriteLine("13. Cache reflection results");
        Console.WriteLine("14. Optimize I/O operations");
        Console.WriteLine("15. Consider JIT compilation effects");
        Console.WriteLine("16. Balance memory usage and performance");
        Console.WriteLine("17. Test optimizations with realistic data");
        Console.WriteLine("18. Monitor for performance regressions");
    }
}
