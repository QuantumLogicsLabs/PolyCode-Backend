using System;
using System.Collections.Generic;
using System.Linq;
using System.Runtime;
using System.Threading.Tasks;

// Memory Management Examples

public class MemoryManagement
{
    // 1. Understanding garbage collection
    public static void UnderstandingGarbageCollection()
    {
        Console.WriteLine("=== Understanding Garbage Collection ===");
        
        // Create objects
        var objects = new List<object>();
        for (int i = 0; i < 1000; i++)
        {
            objects.Add(new object());
        }
        
        Console.WriteLine($"Created {objects.Count} objects");
        Console.WriteLine($"Memory before GC: {GC.GetTotalMemory(false) / 1024} KB");
        
        // Make objects eligible for GC
        objects.Clear();
        
        // Force garbage collection
        Console.WriteLine("Forcing garbage collection...");
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        Console.WriteLine($"Memory after GC: {GC.GetTotalMemory(false) / 1024} KB");
        Console.WriteLine($"Gen 0 collections: {GC.CollectionCount(0)}");
        Console.WriteLine($"Gen 1 collections: {GC.CollectionCount(1)}");
        Console.WriteLine($"Gen 2 collections: {GC.CollectionCount(2)}");
        Console.WriteLine();
    }
    
    // 2. Finalizers and IDisposable
    public static void FinalizersAndIDisposable()
    {
        Console.WriteLine("=== Finalizers and IDisposable ===");
        
        // Class with finalizer
        class WithFinalizer
        {
            private readonly string _name;
            
            public WithFinalizer(string name)
            {
                _name = name;
                Console.WriteLine($"{_name}: Created");
            }
            
            ~WithFinalizer()
            {
                Console.WriteLine($"{_name}: Finalized");
            }
        }
        
        // Class implementing IDisposable
        class WithDisposable : IDisposable
        {
            private readonly string _name;
            private bool _disposed = false;
            
            public WithDisposable(string name)
            {
                _name = name;
                Console.WriteLine($"{_name}: Created");
            }
            
            public void Dispose()
            {
                Dispose(true);
                GC.SuppressFinalize(this);
                Console.WriteLine($"{_name}: Disposed");
            }
            
            protected virtual void Dispose(bool disposing)
            {
                if (!_disposed)
                {
                    if (disposing)
                    {
                        // Dispose managed resources
                    }
                    _disposed = true;
                }
            }
            
            ~WithDisposable()
            {
                Console.WriteLine($"{_name}: Finalized (not disposed properly)");
                Dispose(false);
            }
        }
        
        // Test finalizer
        Console.WriteLine("Testing finalizer:");
        var obj1 = new WithFinalizer("FinalizerObj");
        obj1 = null; // Make eligible for GC
        
        // Test IDisposable
        Console.WriteLine("\nTesting IDisposable (proper disposal):");
        using (var obj2 = new WithDisposable("DisposableObj1"))
        {
            // obj2 will be automatically disposed
        }
        
        Console.WriteLine("\nTesting IDisposable (improper disposal):");
        var obj3 = new WithDisposable("DisposableObj2");
        obj3 = null; // Not disposed, will be finalized
        
        // Force GC to see finalizers
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        Console.WriteLine();
    }
    
    // 3. Memory leaks demonstration
    public static void MemoryLeaksDemo()
    {
        Console.WriteLine("=== Memory Leaks Demonstration ===");
        
        // Event handler leak
        class EventPublisher
        {
            public event EventHandler SomethingHappened;
            
            public void DoSomething()
            {
                SomethingHappened?.Invoke(this, EventArgs.Empty);
            }
        }
        
        class EventSubscriber
        {
            private readonly string _name;
            
            public EventSubscriber(string name)
            {
                _name = name;
            }
            
            public void Subscribe(EventPublisher publisher)
            {
                publisher.SomethingHappened += HandleSomething;
                Console.WriteLine($"{_name}: Subscribed to event");
            }
            
            private void HandleSomething(object sender, EventArgs e)
            {
                Console.WriteLine($"{_name}: Event handled");
            }
        }
        
        // Static collection leak
        class StaticCollectionLeak
        {
            private static readonly List<string> _items = new List<string>();
            
            public static void AddItem(string item)
            {
                _items.Add(item);
                Console.WriteLine($"Added item to static collection. Total: {_items.Count}");
            }
        }
        
        // Create publisher and subscribers
        var publisher = new EventPublisher();
        
        Console.WriteLine("Creating subscribers...");
        for (int i = 0; i < 5; i++)
        {
            var subscriber = new EventSubscriber($"Subscriber{i}");
            subscriber.Subscribe(publisher);
        }
        
        // Trigger events
        publisher.DoSomething();
        
        // Clear references (but event handlers still hold references)
        Console.WriteLine("\nClearing references...");
        
        // Static collection leak
        Console.WriteLine("\nStatic collection leak:");
        for (int i = 0; i < 10; i++)
        {
            StaticCollectionLeak.AddItem($"Item{i}");
        }
        
        Console.WriteLine("\nNote: These objects won't be garbage collected due to:");
        Console.WriteLine("1. Event handlers still holding references");
        Console.WriteLine("2. Static collection holding references");
        Console.WriteLine();
    }
    
    // 4. Large object heap
    public static void LargeObjectHeap()
    {
        Console.WriteLine("=== Large Object Heap ===");
        
        // Small objects (stay in Gen 0/1/2)
        Console.WriteLine("Creating small objects...");
        var smallObjects = new List<byte[]>();
        for (int i = 0; i < 100; i++)
        {
            smallObjects.Add(new byte[1024]); // 1 KB
        }
        
        Console.WriteLine($"Small objects memory: {GC.GetTotalMemory(false) / 1024} KB");
        
        // Large objects (go to LOH)
        Console.WriteLine("\nCreating large objects...");
        var largeObjects = new List<byte[]>();
        for (int i = 0; i < 5; i++)
        {
            largeObjects.Add(new byte[85 * 1024]); // 85 KB (over LOH threshold)
        }
        
        Console.WriteLine($"With large objects memory: {GC.GetTotalMemory(false) / 1024} KB");
        
        // Clear and collect
        smallObjects.Clear();
        largeObjects.Clear();
        
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        Console.WriteLine($"After GC memory: {GC.GetTotalMemory(false) / 1024} KB");
        Console.WriteLine();
    }
    
    // 5. Array pooling
    public static void ArrayPooling()
    {
        Console.WriteLine("=== Array Pooling ===");
        
        // Without pooling
        Console.WriteLine("Without pooling:");
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        
        for (int i = 0; i < 1000; i++)
        {
            var array = new int[1000];
            for (int j = 0; j < array.Length; j++)
            {
                array[j] = j;
            }
            // Array goes out of scope and needs GC
        }
        
        stopwatch.Stop();
        Console.WriteLine($"Time without pooling: {stopwatch.ElapsedMilliseconds} ms");
        
        // With pooling (simplified version)
        Console.WriteLine("\nWith pooling:");
        var pool = new List<int[]>();
        stopwatch.Restart();
        
        for (int i = 0; i < 1000; i++)
        {
            int[] array;
            if (pool.Count > 0)
            {
                array = pool[pool.Count - 1];
                pool.RemoveAt(pool.Count - 1);
            }
            else
            {
                array = new int[1000];
            }
            
            for (int j = 0; j < array.Length; j++)
            {
                array[j] = j;
            }
            
            // Return to pool
            pool.Add(array);
        }
        
        stopwatch.Stop();
        Console.WriteLine($"Time with pooling: {stopwatch.ElapsedMilliseconds} ms");
        Console.WriteLine($"Pool size: {pool.Count}");
        Console.WriteLine();
    }
    
    // 6. Weak references
    public static void WeakReferences()
    {
        Console.WriteLine("=== Weak References ===");
        
        // Strong reference
        var strongRef = new LargeObject("Strong");
        Console.WriteLine($"Created strong reference: {strongRef.Name}");
        
        // Weak reference
        var weakRef = new WeakReference(new LargeObject("Weak"));
        Console.WriteLine("Created weak reference");
        
        // Check weak reference
        if (weakRef.IsAlive)
        {
            var obj = (LargeObject)weakRef.Target;
            Console.WriteLine($"Weak reference is alive: {obj.Name}");
        }
        
        // Remove strong reference and force GC
        strongRef = null;
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        // Check weak reference again
        if (weakRef.IsAlive)
        {
            var obj = (LargeObject)weakRef.Target;
            Console.WriteLine($"Weak reference is still alive: {obj.Name}");
        }
        else
        {
            Console.WriteLine("Weak reference is no longer alive (object was collected)");
        }
        
        Console.WriteLine();
    }
    
    // 7. Memory optimization techniques
    public static void MemoryOptimization()
    {
        Console.WriteLine("=== Memory Optimization Techniques ===");
        
        // String interning
        Console.WriteLine("String interning:");
        string str1 = "Hello";
        string str2 = "Hello";
        string str3 = string.Intern("Hello");
        
        Console.WriteLine($"str1 == str2: {ReferenceEquals(str1, str2)}");
        Console.WriteLine($"str1 == str3: {ReferenceEquals(str1, str3)}");
        
        // Value types vs reference types
        Console.WriteLine("\nValue types vs reference types:");
        
        // Value type (struct)
        var structArray = new Point[1000];
        long structMemory = GC.GetTotalMemory(false);
        
        // Reference type (class)
        var classArray = new PointClass[1000];
        for (int i = 0; i < 1000; i++)
        {
            classArray[i] = new PointClass { X = i, Y = i };
        }
        long classMemory = GC.GetTotalMemory(false);
        
        Console.WriteLine($"Struct array memory: {(structMemory) / 1024} KB");
        Console.WriteLine($"Class array memory: {(classMemory - structMemory) / 1024} KB");
        
        Console.WriteLine();
    }
    
    // 8. GC tuning
    public static void GCTuning()
    {
        Console.WriteLine("=== GC Tuning ===");
        
        // Show current GC settings
        Console.WriteLine($"GC mode: {GCSettings.IsServerGC}");
        Console.WriteLine($"Latency mode: {GCSettings.LatencyMode}");
        
        // Set latency mode
        var originalMode = GCSettings.LatencyMode;
        GCSettings.LatencyMode = GCLatencyMode.LowLatency;
        
        Console.WriteLine($"Changed latency mode to: {GCSettings.LatencyMode}");
        
        // Do some work
        var objects = new List<object>();
        for (int i = 0; i < 10000; i++)
        {
            objects.Add(new object());
        }
        
        // Restore original mode
        GCSettings.LatencyMode = originalMode;
        Console.WriteLine($"Restored latency mode to: {GCSettings.LatencyMode}");
        
        Console.WriteLine();
    }
    
    // Helper classes
    private class LargeObject
    {
        public string Name { get; }
        private readonly byte[] _data = new byte[1024 * 1024]; // 1 MB
        
        public LargeObject(string name)
        {
            Name = name;
            Console.WriteLine($"LargeObject '{name}' created ({_data.Length} bytes)");
        }
        
        ~LargeObject()
        {
            Console.WriteLine($"LargeObject '{Name}' finalized");
        }
    }
    
    private struct Point
    {
        public int X, Y;
    }
    
    private class PointClass
    {
        public int X, Y;
    }
}

// Advanced Memory Management
public class AdvancedMemoryManagement
{
    // 1. Memory-mapped files simulation
    public static void MemoryMappedFiles()
    {
        Console.WriteLine("=== Memory-Mapped Files Simulation ===");
        
        // Simulate working with large data sets
        var largeDataSet = new List<byte[]>(100);
        
        Console.WriteLine("Creating large data set...");
        for (int i = 0; i < 100; i++)
        {
            largeDataSet.Add(new byte[1024 * 1024]); // 1 MB each
        }
        
        Console.WriteLine($"Total memory used: {largeDataSet.Count * 1024 / 1024} MB");
        Console.WriteLine($"Process memory: {Environment.WorkingSet / 1024 / 1024} MB");
        
        // Clear references
        largeDataSet.Clear();
        
        // Force cleanup
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        
        Console.WriteLine($"After cleanup: {Environment.WorkingSet / 1024 / 1024} MB");
        Console.WriteLine();
    }
    
    // 2. Object pooling pattern
    public static void ObjectPooling()
    {
        Console.WriteLine("=== Object Pooling Pattern ===");
        
        // Simple object pool
        class ObjectPool<T> where T : class, new()
        {
            private readonly Concurrent.ConcurrentBag<T> _objects = new();
            private readonly Func<T> _objectGenerator;
            private readonly int _maxObjects;
            
            public ObjectPool(int maxObjects = 10)
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
        
        // Pooled object
        class PooledObject
        {
            public int Id { get; set; }
            public DateTime Created { get; set; } = DateTime.Now;
            
            public void Reset()
            {
                Id = 0;
                Created = DateTime.Now;
            }
        }
        
        // Test object pool
        var pool = new ObjectPool<PooledObject>();
        var objects = new List<PooledObject>();
        
        Console.WriteLine("Getting objects from pool...");
        for (int i = 0; i < 15; i++)
        {
            var obj = pool.Get();
            obj.Id = i;
            objects.Add(obj);
            Console.WriteLine($"Got object with ID: {obj.Id}");
        }
        
        Console.WriteLine("\nReturning objects to pool...");
        foreach (var obj in objects)
        {
            pool.Return(obj);
            Console.WriteLine($"Returned object with ID: {obj.Id}");
        }
        
        Console.WriteLine();
    }
    
    // 3. Memory profiling simulation
    public static void MemoryProfiling()
    {
        Console.WriteLine("=== Memory Profiling Simulation ===");
        
        // Simulate memory-intensive operations
        var memorySnapshots = new List<(string Description, long Memory)>();
        
        // Take initial snapshot
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        memorySnapshots.Add(("Initial", GC.GetTotalMemory(false)));
        
        // Create objects
        var objects = new List<object>();
        for (int i = 0; i < 10000; i++)
        {
            objects.Add(new object());
        }
        memorySnapshots.Add(("After object creation", GC.GetTotalMemory(false)));
        
        // Create strings
        var strings = new List<string>();
        for (int i = 0; i < 5000; i++)
        {
            strings.Add(new string('x', 100));
        }
        memorySnapshots.Add(("After string creation", GC.GetTotalMemory(false)));
        
        // Create arrays
        var arrays = new List<byte[]>();
        for (int i = 0; i < 100; i++)
        {
            arrays.Add(new byte[1024]);
        }
        memorySnapshots.Add(("After array creation", GC.GetTotalMemory(false)));
        
        // Clear and collect
        objects.Clear();
        strings.Clear();
        arrays.Clear();
        
        GC.Collect();
        GC.WaitForPendingFinalizers();
        GC.Collect();
        memorySnapshots.Add(("After cleanup", GC.GetTotalMemory(false)));
        
        // Display results
        Console.WriteLine("Memory usage snapshots:");
        foreach (var (description, memory) in memorySnapshots)
        {
            Console.WriteLine($"{description,-25}: {memory / 1024,6} KB");
        }
        
        Console.WriteLine();
    }
    
    // 4. Memory-efficient data structures
    public static void MemoryEfficientStructures()
    {
        Console.WriteLine("=== Memory-Efficient Data Structures ===");
        
        // Compare different data structures for the same data
        
        const int itemCount = 100000;
        var data = Enumerable.Range(1, itemCount).ToList();
        
        // List<T>
        var list = new List<int>(data);
        long listMemory = GC.GetTotalMemory(false);
        
        // Array
        var array = data.ToArray();
        long arrayMemory = GC.GetTotalMemory(false);
        
        // HashSet<T>
        var hashSet = new HashSet<int>(data);
        long hashSetMemory = GC.GetTotalMemory(false);
        
        // Dictionary<int, int>
        var dictionary = data.ToDictionary(x => x, x => x);
        long dictionaryMemory = GC.GetTotalMemory(false);
        
        Console.WriteLine($"List<int> memory: {(listMemory - arrayMemory) / 1024} KB");
        Console.WriteLine($"Array memory: {(arrayMemory - listMemory) / 1024} KB");
        Console.WriteLine($"HashSet<int> memory: {(hashSetMemory - arrayMemory) / 1024} KB");
        Console.WriteLine($"Dictionary<int,int> memory: {(dictionaryMemory - hashSetMemory) / 1024} KB");
        
        Console.WriteLine();
    }
}

// Main demonstration
public class MemoryManagementDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Memory Management Demonstration ===");
        Console.WriteLine("Understanding .NET memory management and optimization techniques.\n");
        
        // Basic memory management
        MemoryManagement.UnderstandingGarbageCollection();
        MemoryManagement.FinalizersAndIDisposable();
        MemoryManagement.MemoryLeaksDemo();
        MemoryManagement.LargeObjectHeap();
        MemoryManagement.ArrayPooling();
        MemoryManagement.WeakReferences();
        MemoryManagement.MemoryOptimization();
        MemoryManagement.GCTuning();
        
        // Advanced topics
        AdvancedMemoryManagement.MemoryMappedFiles();
        AdvancedMemoryManagement.ObjectPooling();
        AdvancedMemoryManagement.MemoryProfiling();
        AdvancedMemoryManagement.MemoryEfficientStructures();
        
        Console.WriteLine("=== Memory Management Best Practices ===");
        Console.WriteLine("1. Dispose objects implementing IDisposable promptly");
        Console.WriteLine("2. Avoid unnecessary object allocations");
        Console.WriteLine("3. Use value types for small, frequently created objects");
        Console.WriteLine("4. Be careful with event subscriptions (potential memory leaks)");
        Console.WriteLine("5. Use object pooling for frequently created/destroyed objects");
        Console.WriteLine("6. Consider weak references for caching scenarios");
        Console.WriteLine("7. Monitor Large Object Heap usage");
        Console.WriteLine("8. Use appropriate collection types");
        Console.WriteLine("9. Profile memory usage regularly");
        Console.WriteLine("10. Consider GC latency mode for real-time applications");
        Console.WriteLine("11. Use array pooling for large temporary arrays");
        Console.WriteLine("12. Understand the cost of boxing/unboxing");
        Console.WriteLine("13. Use structs for data that doesn't need identity");
        Console.WriteLine("14. Avoid static collections that grow indefinitely");
        Console.WriteLine("15. Use string interning carefully");
    }
}
