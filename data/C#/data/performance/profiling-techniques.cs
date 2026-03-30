using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;

// Profiling Techniques and Tools

public class ProfilingTechniques
{
    // 1. Manual profiling with Stopwatch
    public static void ManualProfiling()
    {
        Console.WriteLine("=== Manual Profiling with Stopwatch ===");
        
        // Method to profile
        void SlowMethod()
        {
            Thread.Sleep(100); // Simulate work
            int sum = 0;
            for (int i = 0; i < 10000; i++)
            {
                sum += i;
            }
        }
        
        // Profile the method
        var stopwatch = Stopwatch.StartNew();
        SlowMethod();
        stopwatch.Stop();
        
        Console.WriteLine($"SlowMethod took: {stopwatch.ElapsedMilliseconds} ms");
        Console.WriteLine($"High resolution: {stopwatch.ElapsedTicks} ticks");
        Console.WriteLine($"Frequency: {Stopwatch.Frequency} Hz");
        Console.WriteLine();
    }
    
    // 2. Method-level profiling
    public static void MethodLevelProfiling()
    {
        Console.WriteLine("=== Method-Level Profiling ===");
        
        // Profiler attribute
        void ProfileMethod(Action method, string methodName)
        {
            var stopwatch = Stopwatch.StartNew();
            method();
            stopwatch.Stop();
            Console.WriteLine($"{methodName}: {stopwatch.ElapsedMilliseconds} ms");
        }
        
        // Methods to profile
        void MethodA()
        {
            Thread.Sleep(50);
            for (int i = 0; i < 1000; i++)
            {
                Math.Sqrt(i);
            }
        }
        
        void MethodB()
        {
            Thread.Sleep(30);
            var list = new List<int>();
            for (int i = 0; i < 1000; i++)
            {
                list.Add(i * i);
            }
        }
        
        void MethodC()
        {
            Thread.Sleep(80);
            var dict = new Dictionary<int, string>();
            for (int i = 0; i < 1000; i++)
            {
                dict[i] = $"Value_{i}";
            }
        }
        
        // Profile each method
        ProfileMethod(MethodA, "MethodA");
        ProfileMethod(MethodB, "MethodB");
        ProfileMethod(MethodC, "MethodC");
        
        Console.WriteLine();
    }
    
    // 3. Memory profiling
    public static void MemoryProfiling()
    {
        Console.WriteLine("=== Memory Profiling ===");
        
        void ProfileMemory(Action action, string actionName)
        {
            // Force GC before measuring
            GC.Collect();
            GC.WaitForPendingFinalizers();
            GC.Collect();
            
            long memoryBefore = GC.GetTotalMemory(false);
            
            action();
            
            long memoryAfter = GC.GetTotalMemory(false);
            long memoryUsed = memoryAfter - memoryBefore;
            
            Console.WriteLine($"{actionName}: {memoryUsed / 1024} KB used");
        }
        
        // Actions to profile
        void CreateObjects()
        {
            var objects = new List<object>();
            for (int i = 0; i < 10000; i++)
            {
                objects.Add(new object());
            }
        }
        
        void CreateStrings()
        {
            var strings = new List<string>();
            for (int i = 0; i < 1000; i++)
            {
                strings.Add(new string('x', 100));
            }
        }
        
        void CreateArrays()
        {
            var arrays = new List<byte[]>();
            for (int i = 0; i < 100; i++)
            {
                arrays.Add(new byte[1024]);
            }
        }
        
        // Profile memory usage
        ProfileMemory(CreateObjects, "CreateObjects");
        ProfileMemory(CreateStrings, "CreateStrings");
        ProfileMemory(CreateArrays, "CreateArrays");
        
        Console.WriteLine();
    }
    
    // 4. CPU profiling simulation
    public static void CPUProfiling()
    {
        Console.WriteLine("=== CPU Profiling Simulation ===");
        
        // Simulate CPU-intensive work
        void CPUIntensiveWork()
        {
            var random = new Random();
            for (int i = 0; i < 1000000; i++)
            {
                // Random mathematical operations
                double x = random.NextDouble();
                double y = Math.Sin(x) * Math.Cos(x);
                double z = Math.Sqrt(Math.Abs(y));
            }
        }
        
        // Profile CPU usage
        var stopwatch = Stopwatch.StartNew();
        
        // Get initial process time
        var startTime = Process.GetCurrentProcess().UserProcessorTime;
        
        CPUIntensiveWork();
        
        var endTime = Process.GetCurrentProcess().UserProcessorTime;
        stopwatch.Stop();
        
        var cpuTime = (endTime - startTime).TotalMilliseconds;
        var wallTime = stopwatch.ElapsedMilliseconds;
        
        Console.WriteLine($"Wall time: {wallTime} ms");
        Console.WriteLine($"CPU time: {cpuTime:F2} ms");
        Console.WriteLine($"CPU utilization: {(cpuTime / wallTime) * 100:F1}%");
        Console.WriteLine();
    }
    
    // 5. Thread profiling
    public static void ThreadProfiling()
    {
        Console.WriteLine("=== Thread Profiling ===");
        
        var threads = new List<Thread>();
        var threadResults = new Dictionary<int, long>();
        
        // Create and profile threads
        for (int i = 0; i < 4; i++)
        {
            int threadId = i;
            var thread = new Thread(() => {
                var stopwatch = Stopwatch.StartNew();
                
                // Simulate work
                Thread.Sleep(100 + threadId * 50);
                
                for (int j = 0; j < 100000; j++)
                {
                    Math.Sqrt(j);
                }
                
                stopwatch.Stop();
                lock (threadResults)
                {
                    threadResults[threadId] = stopwatch.ElapsedMilliseconds;
                }
            });
            
            threads.Add(thread);
            thread.Start();
        }
        
        // Wait for all threads
        foreach (var thread in threads)
        {
            thread.Join();
        }
        
        // Display results
        Console.WriteLine("Thread execution times:");
        foreach (var kvp in threadResults.OrderBy(x => x.Key))
        {
            Console.WriteLine($"Thread {kvp.Key}: {kvp.Value} ms");
        }
        
        Console.WriteLine();
    }
    
    // 6. Call stack profiling
    public static void CallStackProfiling()
    {
        Console.WriteLine("=== Call Stack Profiling ===");
        
        // Simulate call stack with timing
        void MethodD()
        {
            var stopwatch = Stopwatch.StartNew();
            Thread.Sleep(50);
            stopwatch.Stop();
            Console.WriteLine($"  MethodD: {stopwatch.ElapsedMilliseconds} ms");
        }
        
        void MethodC()
        {
            var stopwatch = Stopwatch.StartNew();
            MethodD();
            stopwatch.Stop();
            Console.WriteLine($" MethodC: {stopwatch.ElapsedMilliseconds} ms");
        }
        
        void MethodB()
        {
            var stopwatch = Stopwatch.StartNew();
            MethodC();
            MethodD();
            stopwatch.Stop();
            Console.WriteLine($"MethodB: {stopwatch.ElapsedMilliseconds} ms");
        }
        
        void MethodA()
        {
            var stopwatch = Stopwatch.StartNew();
            MethodB();
            MethodC();
            stopwatch.Stop();
            Console.WriteLine($"MethodA: {stopwatch.ElapsedMilliseconds} ms");
        }
        
        Console.WriteLine("Call stack timing:");
        MethodA();
        
        Console.WriteLine();
    }
    
    // 7. Performance counters simulation
    public static void PerformanceCounters()
    {
        Console.WriteLine("=== Performance Counters Simulation ===");
        
        // Simulate performance monitoring
        var counters = new Dictionary<string, long>
        {
            ["MethodCalls"] = 0,
            ["Exceptions"] = 0,
            ["CacheHits"] = 0,
            ["CacheMisses"] = 0
        };
        
        // Simulate application work
        void SimulateWork()
        {
            counters["MethodCalls"]++;
            
            try
            {
                // Simulate cache
                var random = new Random();
                if (random.Next(10) < 7) // 70% hit rate
                {
                    counters["CacheHits"]++;
                }
                else
                {
                    counters["CacheMisses"]++;
                }
                
                // Simulate occasional exception
                if (random.Next(100) < 5) // 5% exception rate
                {
                    counters["Exceptions"]++;
                    throw new InvalidOperationException("Simulated exception");
                }
            }
            catch
            {
                // Handle exception
            }
        }
        
        // Run simulation
        for (int i = 0; i < 1000; i++)
        {
            SimulateWork();
        }
        
        // Display counters
        Console.WriteLine("Performance Counters:");
        foreach (var kvp in counters)
        {
            Console.WriteLine($"{kvp.Key,-15}: {kvp.Value}");
        }
        
        if (counters["CacheHits"] + counters["CacheMisses"] > 0)
        {
            var hitRate = (double)counters["CacheHits"] / (counters["CacheHits"] + counters["CacheMisses"]) * 100;
            Console.WriteLine($"Cache Hit Rate: {hitRate:F1}%");
        }
        
        Console.WriteLine();
    }
    
    // 8. Hot path identification
    public static void HotPathIdentification()
    {
        Console.WriteLine("=== Hot Path Identification ===");
        
        var methodTimes = new Dictionary<string, List<long>>();
        
        // Methods to track
        void TrackMethod(string methodName, Action method)
        {
            var stopwatch = Stopwatch.StartNew();
            method();
            stopwatch.Stop();
            
            if (!methodTimes.ContainsKey(methodName))
            {
                methodTimes[methodName] = new List<long>();
            }
            methodTimes[methodName].Add(stopwatch.ElapsedMilliseconds);
        }
        
        // Simulate application flow
        void DatabaseOperation()
        {
            Thread.Sleep(50); // Simulate DB call
        }
        
        void BusinessLogic()
        {
            TrackMethod("DatabaseOperation", DatabaseOperation);
            Thread.Sleep(20); // Business logic
        }
        
        void WebRequest()
        {
            TrackMethod("BusinessLogic", BusinessLogic);
            Thread.Sleep(10); // Request handling
        }
        
        // Run multiple requests
        for (int i = 0; i < 10; i++)
        {
            TrackMethod("WebRequest", WebRequest);
        }
        
        // Analyze hot paths
        Console.WriteLine("Hot Path Analysis:");
        foreach (var kvp in methodTimes)
        {
            var times = kvp.Value;
            var avg = times.Average();
            var total = times.Sum();
            var max = times.Max();
            var min = times.Min();
            
            Console.WriteLine($"{kvp.Key,-20}: Avg={avg:F2}ms, Total={total}ms, Max={max}ms, Min={min}ms, Calls={times.Count}");
        }
        
        // Identify hottest method
        var hottestMethod = methodTimes.OrderByDescending(x => x.Value.Sum()).First();
        Console.WriteLine($"\nHottest method: {hottestMethod.Key} (total: {hottestMethod.Value.Sum()}ms)");
        
        Console.WriteLine();
    }
}

// Advanced Profiling Techniques
public class AdvancedProfiling
{
    // 1. Sampling profiler simulation
    public static void SamplingProfiler()
    {
        Console.WriteLine("=== Sampling Profiler Simulation ===");
        
        // Simulate sampling profiler
        var samples = new Dictionary<string, int>();
        var random = new Random();
        
        void SimulateWork()
        {
            // Simulate different methods being called
            var methods = new[]
            {
                ("Main", 100),
                ("DataAccess", 200),
                ("BusinessLogic", 150),
                ("Utility", 50),
                ("Logging", 30)
            };
            
            foreach (var (method, duration) in methods)
            {
                // Simulate sampling during method execution
                var sampleCount = duration / 10; // Sample every 10ms
                
                for (int i = 0; i < sampleCount; i++)
                {
                    // Random sampling (in real profiler, this would be timer-based)
                    if (random.Next(100) < 80) // 80% sample rate
                    {
                        if (!samples.ContainsKey(method))
                        {
                            samples[method] = 0;
                        }
                        samples[method]++;
                    }
                    
                    Thread.Sleep(10);
                }
            }
        }
        
        Console.WriteLine("Running sampling profiler...");
        SimulateWork();
        
        // Display sampling results
        Console.WriteLine("\nSampling Results:");
        var totalSamples = samples.Values.Sum();
        foreach (var kvp in samples.OrderByDescending(x => x.Value))
        {
            var percentage = (double)kvp.Value / totalSamples * 100;
            Console.WriteLine($"{kvp.Key,-15}: {kvp.Value,4} samples ({percentage:F1}%)");
        }
        
        Console.WriteLine();
    }
    
    // 2. Instrumentation profiling
    public static void InstrumentationProfiler()
    {
        Console.WriteLine("=== Instrumentation Profiler ===");
        
        // Simple instrumentation profiler
        class InstrumentationProfiler
        {
            private readonly Dictionary<string, List<long>> _methodTimes = new();
            
            public IDisposable Profile(string methodName)
            {
                return new MethodProfiler(this, methodName);
            }
            
            private void RecordTime(string methodName, long elapsedMs)
            {
                if (!_methodTimes.ContainsKey(methodName))
                {
                    _methodTimes[methodName] = new List<long>();
                }
                _methodTimes[methodName].Add(elapsedMs);
            }
            
            public void Report()
            {
                Console.WriteLine("Instrumentation Results:");
                foreach (var kvp in _methodTimes)
                {
                    var times = kvp.Value;
                    var avg = times.Average();
                    var total = times.Sum();
                    var calls = times.Count;
                    
                    Console.WriteLine($"{kvp.Key,-20}: Avg={avg:F2}ms, Total={total}ms, Calls={calls}");
                }
            }
            
            private class MethodProfiler : IDisposable
            {
                private readonly InstrumentationProfiler _profiler;
                private readonly string _methodName;
                private readonly Stopwatch _stopwatch;
                
                public MethodProfiler(InstrumentationProfiler profiler, string methodName)
                {
                    _profiler = profiler;
                    _methodName = methodName;
                    _stopwatch = Stopwatch.StartNew();
                }
                
                public void Dispose()
                {
                    _stopwatch.Stop();
                    _profiler.RecordTime(_methodName, _stopwatch.ElapsedMilliseconds);
                }
            }
        }
        
        var profiler = new InstrumentationProfiler();
        
        // Profile methods
        void ProfiledMethod()
        {
            Thread.Sleep(50);
        }
        
        void AnotherProfiledMethod()
        {
            Thread.Sleep(30);
        }
        
        // Use profiler
        using (profiler.Profile("ProfiledMethod"))
        {
            ProfiledMethod();
        }
        
        using (profiler.Profile("AnotherProfiledMethod"))
        {
            AnotherProfiledMethod();
        }
        
        // Profile nested calls
        using (profiler.Profile("OuterMethod"))
        {
            Thread.Sleep(20);
            using (profiler.Profile("InnerMethod"))
            {
                Thread.Sleep(40);
            }
            Thread.Sleep(10);
        }
        
        profiler.Report();
        Console.WriteLine();
    }
    
    // 3. Memory leak detection
    public static void MemoryLeakDetection()
    {
        Console.WriteLine("=== Memory Leak Detection ===");
        
        // Simulate memory leak detection
        var initialMemory = GC.GetTotalMemory(false);
        var memorySnapshots = new List<(string Description, long Memory)>();
        
        // Take baseline
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        memorySnapshots.Add(("Baseline", GC.GetTotalMemory(false)));
        
        // Create potential leak
        var staticList = new List<string>();
        
        // Add items to static list (potential leak)
        for (int i = 0; i < 1000; i++)
        {
            staticList.Add($"Item_{i}");
        }
        
        memorySnapshots.Add(("After static allocation", GC.GetTotalMemory(false)));
        
        // Create and discard objects (should be collected)
        for (int i = 0; i < 1000; i++)
        {
            var obj = new object();
        }
        
        memorySnapshots.Add(("After object allocation", GC.GetTotalMemory(false)));
        
        // Force GC
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        memorySnapshots.Add(("After GC", GC.GetTotalMemory(false)));
        
        // Analyze memory usage
        Console.WriteLine("Memory Usage Analysis:");
        var baseline = memorySnapshots[0].Memory;
        
        for (int i = 1; i < memorySnapshots.Count; i++)
        {
            var (description, memory) = memorySnapshots[i];
            var increase = (memory - baseline) / 1024;
            Console.WriteLine($"{description,-25}: +{increase,6} KB");
        }
        
        // Potential leak detection
        var finalMemory = memorySnapshots.Last().Memory;
        var expectedMemory = baseline; // Should be close to baseline after GC
        
        if (finalMemory - expectedMemory > 1024 * 100) // 100 KB threshold
        {
            Console.WriteLine("\n⚠️  Potential memory leak detected!");
            Console.WriteLine($"Expected memory: {expectedMemory / 1024} KB");
            Console.WriteLine($"Actual memory: {finalMemory / 1024} KB");
            Console.WriteLine($"Leak size: {(finalMemory - expectedMemory) / 1024} KB");
        }
        else
        {
            Console.WriteLine("\n✅ No significant memory leak detected");
        }
        
        Console.WriteLine();
    }
    
    // 4. Performance regression testing
    public static void PerformanceRegressionTesting()
    {
        Console.WriteLine("=== Performance Regression Testing ===");
        
        // Baseline performance data (simulated)
        var baseline = new Dictionary<string, double>
        {
            ["MethodA"] = 50.0, // ms
            ["MethodB"] = 30.0,
            ["MethodC"] = 80.0
        };
        
        // Current performance measurements
        var current = new Dictionary<string, double>();
        
        // Measure current performance
        void MeasureMethod(string methodName, Action method)
        {
            var stopwatch = Stopwatch.StartNew();
            method();
            stopwatch.Stop();
            current[methodName] = stopwatch.ElapsedMilliseconds;
        }
        
        // Methods to test
        void MethodA()
        {
            Thread.Sleep(45); // Slightly faster than baseline
        }
        
        void MethodB()
        {
            Thread.Sleep(40); // Slower than baseline
        }
        
        void MethodC()
        {
            Thread.Sleep(75); // Slightly faster than baseline
        }
        
        // Measure current performance
        MeasureMethod("MethodA", MethodA);
        MeasureMethod("MethodB", MethodB);
        MeasureMethod("MethodC", MethodC);
        
        // Check for regressions
        Console.WriteLine("Performance Regression Analysis:");
        bool hasRegressions = false;
        
        foreach (var methodName in baseline.Keys)
        {
            var baselineTime = baseline[methodName];
            var currentTime = current[methodName];
            var change = ((currentTime - baselineTime) / baselineTime) * 100;
            
            var status = change switch
            {
                > 10 => "🔴 REGRESSION",
                < -10 => "🟢 IMPROVEMENT",
                _ => "🟡 STABLE"
            };
            
            Console.WriteLine($"{methodName,-10}: {currentTime,6:F1}ms (vs {baselineTime,6:F1}ms) [{change:+F1;-F1}%] {status}");
            
            if (change > 10)
            {
                hasRegressions = true;
            }
        }
        
        Console.WriteLine($"\nRegression Status: {(hasRegressions ? "FAILED" : "PASSED")}");
        Console.WriteLine();
    }
    
    // 5. Resource usage profiling
    public static void ResourceUsageProfiling()
    {
        Console.WriteLine("=== Resource Usage Profiling ===");
        
        // Get initial resource usage
        var process = Process.GetCurrentProcess();
        var initialMemory = process.WorkingSet64;
        var initialThreads = process.Threads.Count;
        
        Console.WriteLine("Initial Resource Usage:");
        Console.WriteLine($"Memory: {initialMemory / 1024 / 1024} MB");
        Console.WriteLine($"Threads: {initialThreads}");
        
        // Simulate resource-intensive operations
        var tasks = new List<Task>();
        
        // Create threads
        for (int i = 0; i < 5; i++)
        {
            int threadId = i;
            tasks.Add(Task.Run(() => {
                // Allocate memory
                var arrays = new List<byte[]>();
                for (int j = 0; j < 10; j++)
                {
                    arrays.Add(new byte[1024 * 1024]); // 1 MB each
                }
                
                // Do some work
                Thread.Sleep(1000);
                
                // Memory will be freed when arrays go out of scope
            }));
        }
        
        // Monitor resource usage during execution
        for (int i = 0; i < 5; i++)
        {
            Thread.Sleep(200);
            process.Refresh();
            Console.WriteLine($"During execution - Memory: {process.WorkingSet64 / 1024 / 1024} MB, Threads: {process.Threads.Count}");
        }
        
        // Wait for completion
        Task.WaitAll(tasks.ToArray());
        
        // Final resource usage
        process.Refresh();
        var finalMemory = process.WorkingSet64;
        var finalThreads = process.Threads.Count;
        
        Console.WriteLine("\nFinal Resource Usage:");
        Console.WriteLine($"Memory: {finalMemory / 1024 / 1024} MB");
        Console.WriteLine($"Threads: {finalThreads}");
        Console.WriteLine($"Memory change: {(finalMemory - initialMemory) / 1024 / 1024:+F1} MB");
        Console.WriteLine($"Thread change: {finalThreads - initialThreads:+D}");
        
        Console.WriteLine();
    }
}

// Profiling Best Practices
public class ProfilingBestPractices
{
    public static void DemonstrateBestPractices()
    {
        Console.WriteLine("=== Profiling Best Practices ===");
        
        // 1. Always profile in release builds
        Console.WriteLine("1. Profile in Release Build:");
        Console.WriteLine("   Release builds have optimizations enabled");
        Console.WriteLine("   Debug builds add overhead that skews results");
        
        // 2. Use representative data
        Console.WriteLine("\n2. Use Representative Data:");
        Console.WriteLine("   Test with realistic data sizes");
        Console.WriteLine("   Consider edge cases and worst-case scenarios");
        
        // 3. Warm up before measuring
        Console.WriteLine("\n3. Warm Up Before Measuring:");
        var stopwatch = Stopwatch.StartNew();
        SomeWork();
        stopwatch.Stop();
        Console.WriteLine($"   Cold run: {stopwatch.ElapsedMilliseconds} ms");
        
        stopwatch.Restart();
        SomeWork();
        stopwatch.Stop();
        Console.WriteLine($"   Warm run: {stopwatch.ElapsedMilliseconds} ms");
        
        // 4. Measure multiple iterations
        Console.WriteLine("\n4. Measure Multiple Iterations:");
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
        Console.WriteLine($"   Min/Max: {times.Min()}/{times.Max()} ms");
        
        // 5. Consider environmental factors
        Console.WriteLine("\n5. Environmental Factors:");
        Console.WriteLine($"   CPU cores: {Environment.ProcessorCount}");
        Console.WriteLine($"   64-bit: {Environment.Is64BitProcess}");
        Console.WriteLine($"   OS: {Environment.OSVersion}");
        
        Console.WriteLine();
    }
    
    private static void SomeWork()
    {
        Thread.Sleep(50);
        int sum = 0;
        for (int i = 0; i < 10000; i++)
        {
            sum += i;
        }
    }
}

// Main demonstration
public class ProfilingTechniquesDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Profiling Techniques Demonstration ===");
        Console.WriteLine("Learn how to profile and analyze application performance.\n");
        
        // Basic profiling techniques
        ProfilingTechniques.ManualProfiling();
        ProfilingTechniques.MethodLevelProfiling();
        ProfilingTechniques.MemoryProfiling();
        ProfilingTechniques.CPUProfiling();
        ProfilingTechniques.ThreadProfiling();
        ProfilingTechniques.CallStackProfiling();
        ProfilingTechniques.PerformanceCounters();
        ProfilingTechniques.HotPathIdentification();
        
        // Advanced profiling
        AdvancedProfiling.SamplingProfiler();
        AdvancedProfiling.InstrumentationProfiler();
        AdvancedProfiling.MemoryLeakDetection();
        AdvancedProfiling.PerformanceRegressionTesting();
        AdvancedProfiling.ResourceUsageProfiling();
        
        // Best practices
        ProfilingBestPractices.DemonstrateBestPractices();
        
        Console.WriteLine("=== Profiling Best Practices Summary ===");
        Console.WriteLine("1. Profile in release builds, not debug builds");
        Console.WriteLine("2. Use representative data and scenarios");
        Console.WriteLine("3. Always warm up before measuring");
        Console.WriteLine("4. Take multiple measurements and use statistics");
        Console.WriteLine("5. Consider environmental factors");
        Console.WriteLine("6. Use both manual and automated profiling");
        Console.WriteLine("7. Profile memory usage, not just CPU time");
        Console.WriteLine("8. Look for hot paths and bottlenecks");
        Console.WriteLine("9. Monitor for memory leaks");
        Console.WriteLine("10. Set up performance regression tests");
        Console.WriteLine("11. Use appropriate profiling tools");
        Console.WriteLine("12. Profile in production-like environments");
        Console.WriteLine("13. Consider both time and space complexity");
        Console.WriteLine("14. Document profiling conditions");
        Console.WriteLine("15. Profile before and after optimizations");
    }
}
