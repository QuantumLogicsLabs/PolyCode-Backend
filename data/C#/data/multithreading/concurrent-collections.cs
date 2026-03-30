using System;
using System.Collections.Concurrent;
using System.Collections.Generic;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;

// Concurrent Collections Examples

public class ConcurrentCollections
{
    // 1. ConcurrentDictionary<TKey, TValue>
    public static async Task ConcurrentDictionaryExample()
    {
        Console.WriteLine("=== ConcurrentDictionary Example ===");
        
        var dictionary = new ConcurrentDictionary<string, int>();
        
        // Add items from multiple threads
        var tasks = new List<Task>();
        
        for (int i = 0; i < 5; i++)
        {
            int threadId = i;
            tasks.Add(Task.Run(() => {
                for (int j = 0; j < 10; j++)
                {
                    string key = $"Key_{threadId}_{j}";
                    int value = threadId * 10 + j;
                    
                    // TryAdd - returns false if key already exists
                    bool added = dictionary.TryAdd(key, value);
                    Console.WriteLine($"Thread {threadId}: TryAdd({key}, {value}) - {added}");
                    
                    Thread.Sleep(10);
                }
            }));
        }
        
        await Task.WhenAll(tasks);
        
        Console.WriteLine($"\nDictionary contains {dictionary.Count} items");
        
        // TryGetValue
        if (dictionary.TryGetValue("Key_2_5", out int value))
        {
            Console.WriteLine($"Found Key_2_5: {value}");
        }
        
        // GetOrAdd
        int newValue = dictionary.GetOrAdd("NewKey", 999);
        Console.WriteLine($"GetOrAdd result: {newValue}");
        
        // AddOrUpdate
        int updatedValue = dictionary.AddOrUpdate("Key_2_5", 100, (key, oldValue) => oldValue * 2);
        Console.WriteLine($"AddOrUpdate result: {updatedValue}");
        
        Console.WriteLine();
    }
    
    // 2. ConcurrentQueue<T>
    public static async Task ConcurrentQueueExample()
    {
        Console.WriteLine("=== ConcurrentQueue Example ===");
        
        var queue = new ConcurrentQueue<int>();
        
        // Producer tasks
        var producers = new List<Task>();
        
        for (int i = 0; i < 3; i++)
        {
            int producerId = i;
            producers.Add(Task.Run(() => {
                for (int j = 0; j < 5; j++)
                {
                    int item = producerId * 10 + j;
                    queue.Enqueue(item);
                    Console.WriteLine($"Producer {producerId}: Enqueued {item}");
                    Thread.Sleep(50);
                }
            }));
        }
        
        // Consumer tasks
        var consumers = new List<Task>();
        
        for (int i = 0; i < 2; i++)
        {
            int consumerId = i;
            consumers.Add(Task.Run(() => {
                while (true)
                {
                    if (queue.TryDequeue(out int item))
                    {
                        Console.WriteLine($"Consumer {consumerId}: Dequeued {item}");
                        Thread.Sleep(100);
                    }
                    else
                    {
                        // Check if producers are done
                        if (producers.All(t => t.IsCompleted))
                        {
                            // Try to dequeue any remaining items
                            if (queue.TryDequeue(out item))
                            {
                                Console.WriteLine($"Consumer {consumerId}: Dequeued {item}");
                                continue;
                            }
                            break;
                        }
                        Thread.Sleep(10);
                    }
                }
            }));
        }
        
        await Task.WhenAll(producers.Concat(consumers));
        
        Console.WriteLine($"Final queue count: {queue.Count}\n");
    }
    
    // 3. ConcurrentStack<T>
    public static async Task ConcurrentStackExample()
    {
        Console.WriteLine("=== ConcurrentStack Example ===");
        
        var stack = new ConcurrentStack<int>();
        
        // Push items from multiple threads
        var pushTasks = new List<Task>();
        
        for (int i = 0; i < 3; i++)
        {
            int threadId = i;
            pushTasks.Add(Task.Run(() => {
                for (int j = 0; j < 5; j++)
                {
                    int item = threadId * 10 + j;
                    stack.Push(item);
                    Console.WriteLine($"Thread {threadId}: Pushed {item}");
                    Thread.Sleep(50);
                }
            }));
        }
        
        await Task.WhenAll(pushTasks);
        
        // Pop items
        while (stack.TryPop(out int item))
        {
            Console.WriteLine($"Popped: {item}");
            Thread.Sleep(100);
        }
        
        Console.WriteLine();
    }
    
    // 4. ConcurrentBag<T>
    public static async Task ConcurrentBagExample()
    {
        Console.WriteLine("=== ConcurrentBag Example ===");
        
        var bag = new ConcurrentBag<string>();
        
        // Add items from multiple threads
        var tasks = new List<Task>();
        
        for (int i = 0; i < 5; i++)
        {
            int threadId = i;
            tasks.Add(Task.Run(() => {
                for (int j = 0; j < 3; j++)
                {
                    string item = $"Item_{threadId}_{j}";
                    bag.Add(item);
                    Console.WriteLine($"Thread {threadId}: Added {item}");
                    Thread.Sleep(50);
                }
            }));
        }
        
        await Task.WhenAll(tasks);
        
        // TryTake items
        int takenCount = 0;
        while (bag.TryTake(out string item))
        {
            Console.WriteLine($"Taken: {item}");
            takenCount++;
        }
        
        Console.WriteLine($"Total items taken: {takenCount}\n");
    }
    
    // 5. BlockingCollection<T>
    public static async Task BlockingCollectionExample()
    {
        Console.WriteLine("=== BlockingCollection Example ===");
        
        var collection = new BlockingCollection<int>(capacity: 5);
        
        // Producer
        var producer = Task.Run(async () => {
            for (int i = 1; i <= 10; i++)
            {
                Console.WriteLine($"Producing: {i}");
                collection.Add(i); // Blocks if capacity reached
                await Task.Delay(200);
            }
            collection.CompleteAdding();
        });
        
        // Consumer
        var consumer = Task.Run(() => {
            foreach (int item in collection.GetConsumingEnumerable())
            {
                Console.WriteLine($"Consuming: {item}");
                Thread.Sleep(300);
            }
        });
        
        await Task.WhenAll(producer, consumer);
        
        Console.WriteLine();
    }
    
    // 6. Producer-Consumer with multiple consumers
    public static async Task MultipleConsumersExample()
    {
        Console.WriteLine("=== Multiple Consumers Example ===");
        
        var collection = new BlockingCollection<string>();
        
        // Producer
        var producer = Task.Run(async () => {
            for (int i = 1; i <= 15; i++)
            {
                string item = $"Item_{i}";
                Console.WriteLine($"Producing: {item}");
                collection.Add(item);
                await Task.Delay(100);
            }
            collection.CompleteAdding();
        });
        
        // Multiple consumers
        var consumers = new List<Task>();
        
        for (int i = 0; i < 3; i++)
        {
            int consumerId = i;
            consumers.Add(Task.Run(() => {
                foreach (string item in collection.GetConsumingEnumerable())
                {
                    Console.WriteLine($"Consumer {consumerId}: {item}");
                    Thread.Sleep(200);
                }
            }));
        }
        
        await Task.WhenAll(producer, consumers.Concat());
        
        Console.WriteLine();
    }
    
    // 7. ConcurrentDictionary with complex operations
    public static async Task ConcurrentDictionaryAdvanced()
    {
        Console.WriteLine("=== ConcurrentDictionary Advanced Operations ===");
        
        var wordCounts = new ConcurrentDictionary<string, int>();
        
        var sentences = new[]
        {
            "hello world this is a test",
            "world of programming is fun",
            "test your code before deployment",
            "hello again from the test suite"
        };
        
        // Count words from multiple threads
        var tasks = sentences.Select((sentence, index) => Task.Run(() => {
            var words = sentence.Split(' ');
            foreach (string word in words)
            {
                // Atomic increment
                wordCounts.AddOrUpdate(word, 1, (key, oldValue) => oldValue + 1);
                
                Console.WriteLine($"Thread {index}: Processed '{word}'");
                Thread.Sleep(50);
            }
        })).ToArray();
        
        await Task.WhenAll(tasks);
        
        Console.WriteLine("\nWord counts:");
        foreach (var kvp in wordCounts.OrderBy(x => x.Key))
        {
            Console.WriteLine($"{kvp.Key}: {kvp.Value}");
        }
        
        Console.WriteLine();
    }
    
    // 8. ConcurrentQueue as work queue
    public static async Task WorkQueueExample()
    {
        Console.WriteLine("=== Work Queue Example ===");
        
        var workQueue = new ConcurrentQueue<Action>();
        var workers = new List<Task>();
        var completedWork = 0;
        
        // Enqueue work items
        for (int i = 1; i <= 10; i++)
        {
            int workId = i;
            workQueue.Enqueue(() => {
                Console.WriteLine($"Processing work item {workId}");
                Thread.Sleep(100);
                Interlocked.Increment(ref completedWork);
            });
        }
        
        // Create worker threads
        for (int i = 0; i < 3; i++)
        {
            int workerId = i;
            workers.Add(Task.Run(() => {
                while (true)
                {
                    if (workQueue.TryDequeue(out Action work))
                    {
                        Console.WriteLine($"Worker {workerId}: Got work item");
                        work();
                    }
                    else
                    {
                        // No more work
                        break;
                    }
                }
            }));
        }
        
        await Task.WhenAll(workers);
        
        Console.WriteLine($"Completed work items: {completedWork}\n");
    }
    
    // 9. Thread-safe cache using ConcurrentDictionary
    public static async Task ConcurrentCacheExample()
    {
        Console.WriteLine("=== Thread-Safe Cache Example ===");
        
        var cache = new ConcurrentDictionary<string, string>();
        
        // Simulate expensive operations
        async Task<string> GetExpensiveValue(string key)
        {
            Console.WriteLine($"Computing value for key: {key}");
            await Task.Delay(500); // Simulate expensive operation
            return $"Value_for_{key}_{DateTime.Now.Millisecond}";
        }
        
        // Get or add with factory method
        var tasks = new List<Task<string>>();
        
        for (int i = 0; i < 5; i++)
        {
            int taskId = i;
            tasks.Add(Task.Run(async () => {
                string key = $"Key_{taskId % 3}"; // Some keys will repeat
                
                // GetOrAdd ensures factory is called only once per key
                string value = cache.GetOrAdd(key, async (k) => {
                    Console.WriteLine($"Factory called for key: {k}");
                    return await GetExpensiveValue(k);
                }).Result;
                
                Console.WriteLine($"Task {taskId}: Got value for {key}: {value}");
                return value;
            }));
        }
        
        await Task.WhenAll(tasks);
        
        Console.WriteLine($"\nCache contains {cache.Count} items:");
        foreach (var kvp in cache)
        {
            Console.WriteLine($"{kvp.Key}: {kvp.Value}");
        }
        
        Console.WriteLine();
    }
    
    // 10. Performance comparison
    public static void PerformanceComparison()
    {
        Console.WriteLine("=== Performance Comparison ===");
        
        const int iterations = 100000;
        const int threadCount = 4;
        
        // Test ConcurrentDictionary
        var concurrentDict = new ConcurrentDictionary<int, int>();
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        
        var tasks = new List<Task>();
        for (int t = 0; t < threadCount; t++)
        {
            int threadId = t;
            tasks.Add(Task.Run(() => {
                for (int i = 0; i < iterations / threadCount; i++)
                {
                    int key = threadId * (iterations / threadCount) + i;
                    concurrentDict[key] = i;
                }
            }));
        }
        
        Task.WaitAll(tasks.ToArray());
        stopwatch.Stop();
        
        Console.WriteLine($"ConcurrentDictionary: {stopwatch.ElapsedMilliseconds}ms for {iterations} operations");
        
        // Test regular Dictionary with lock
        var regularDict = new Dictionary<int, int>();
        var lockObj = new object();
        
        stopwatch.Restart();
        tasks.Clear();
        
        for (int t = 0; t < threadCount; t++)
        {
            int threadId = t;
            tasks.Add(Task.Run(() => {
                for (int i = 0; i < iterations / threadCount; i++)
                {
                    int key = threadId * (iterations / threadCount) + i;
                    lock (lockObj)
                    {
                        regularDict[key] = i;
                    }
                }
            }));
        }
        
        Task.WaitAll(tasks.ToArray());
        stopwatch.Stop();
        
        Console.WriteLine($"Dictionary with lock: {stopwatch.ElapsedMilliseconds}ms for {iterations} operations");
        Console.WriteLine();
    }
}

// Advanced Concurrent Collections Patterns
public class AdvancedConcurrentPatterns
{
    // 1. Thread-safe observer pattern using ConcurrentDictionary
    public class ConcurrentObservable<T>
    {
        private readonly ConcurrentDictionary<Guid, Action<T>> _observers = new();
        
        public Guid Subscribe(Action<T> observer)
        {
            var id = Guid.NewGuid();
            _observers.TryAdd(id, observer);
            return id;
        }
        
        public void Unsubscribe(Guid id)
        {
            _observers.TryRemove(id, out _);
        }
        
        public void Notify(T value)
        {
            foreach (var observer in _observers.Values)
            {
                observer(value);
            }
        }
    }
    
    public static async Task ObserverPatternExample()
    {
        Console.WriteLine("=== Concurrent Observer Pattern ===");
        
        var observable = new ConcurrentObservable<string>();
        var subscriptionIds = new List<Guid>();
        
        // Subscribe observers
        for (int i = 0; i < 3; i++)
        {
            int observerId = i;
            var id = observable.Subscribe(message => {
                Console.WriteLine($"Observer {observerId}: Received '{message}'");
            });
            subscriptionIds.Add(id);
        }
        
        // Notify from multiple threads
        var tasks = new List<Task>();
        
        for (int i = 0; i < 5; i++)
        {
            int messageId = i;
            tasks.Add(Task.Run(() => {
                observable.Notify($"Message {messageId}");
                Thread.Sleep(100);
            }));
        }
        
        await Task.WhenAll(tasks);
        
        // Unsubscribe all observers
        foreach (var id in subscriptionIds)
        {
            observable.Unsubscribe(id);
        }
        
        Console.WriteLine("All observers unsubscribed\n");
    }
    
    // 2. Concurrent pipeline
    public static async Task ConcurrentPipelineExample()
    {
        Console.WriteLine("=== Concurrent Pipeline ===");
        
        var stage1 = new BlockingCollection<int>(capacity: 10);
        var stage2 = new BlockingCollection<string>(capacity: 10);
        var stage3 = new BlockingCollection<string>(capacity: 10);
        
        // Stage 1: Producer
        var producer = Task.Run(() => {
            for (int i = 1; i <= 10; i++)
            {
                Console.WriteLine($"Producing: {i}");
                stage1.Add(i);
                Thread.Sleep(100);
            }
            stage1.CompleteAdding();
        });
        
        // Stage 2: Transform
        var transformer = Task.Run(() => {
            foreach (int number in stage1.GetConsumingEnumerable())
            {
                string result = $"Processed_{number}";
                Console.WriteLine($"Transforming: {number} -> {result}");
                stage2.Add(result);
                Thread.Sleep(150);
            }
            stage2.CompleteAdding();
        });
        
        // Stage 3: Final processing
        var finalProcessor = Task.Run(() => {
            foreach (string item in stage2.GetConsumingEnumerable())
            {
                string result = $"Final_{item}";
                Console.WriteLine($"Final processing: {item} -> {result}");
                stage3.Add(result);
                Thread.Sleep(200);
            }
            stage3.CompleteAdding();
        });
        
        // Consumer
        var consumer = Task.Run(() => {
            foreach (string result in stage3.GetConsumingEnumerable())
            {
                Console.WriteLine($"Consumed: {result}");
            }
        });
        
        await Task.WhenAll(producer, transformer, finalProcessor, consumer);
        
        Console.WriteLine();
    }
    
    // 3. Rate limiter using BlockingCollection
    public class RateLimiter
    {
        private readonly BlockingCollection<DateTime> _tokens;
        private readonly TimeSpan _interval;
        private readonly int _maxTokens;
        
        public RateLimiter(int maxRequests, TimeSpan interval)
        {
            _maxTokens = maxRequests;
            _interval = interval;
            _tokens = new BlockingCollection<DateTime>(maxRequests);
            
            // Initialize tokens
            for (int i = 0; i < maxRequests; i++)
            {
                _tokens.Add(DateTime.Now);
            }
            
            // Start token refresh task
            Task.Run(RefreshTokens);
        }
        
        public async Task<bool> WaitForTokenAsync(TimeSpan timeout)
        {
            try
            {
                await Task.Run(() => _tokens.Take(timeout));
                return true;
            }
            catch (InvalidOperationException)
            {
                return false; // Timeout
            }
        }
        
        private async Task RefreshTokens()
        {
            while (true)
            {
                await Task.Delay(_interval);
                
                // Add new tokens if needed
                while (_tokens.Count < _maxTokens)
                {
                    _tokens.Add(DateTime.Now);
                }
            }
        }
    }
    
    public static async Task RateLimiterExample()
    {
        Console.WriteLine("=== Rate Limiter Example ===");
        
        var rateLimiter = new RateLimiter(maxRequests: 3, interval: TimeSpan.FromSeconds(1));
        
        var tasks = new List<Task>();
        
        for (int i = 0; i < 10; i++)
        {
            int requestId = i;
            tasks.Add(Task.Run(async () => {
                Console.WriteLine($"Request {requestId}: Waiting for token...");
                
                bool gotToken = await rateLimiter.WaitForTokenAsync(TimeSpan.FromSeconds(2));
                
                if (gotToken)
                {
                    Console.WriteLine($"Request {requestId}: Got token, processing...");
                    await Task.Delay(500);
                    Console.WriteLine($"Request {requestId}: Completed");
                }
                else
                {
                    Console.WriteLine($"Request {requestId}: Timed out waiting for token");
                }
            }));
        }
        
        await Task.WhenAll(tasks);
        
        Console.WriteLine();
    }
}

// Main demonstration
public class ConcurrentCollectionsDemo
{
    public static async Task Main(string[] args)
    {
        Console.WriteLine("=== Concurrent Collections Demonstration ===");
        Console.WriteLine("Thread-safe collections for concurrent programming scenarios.\n");
        
        // Basic concurrent collections
        await ConcurrentCollections.ConcurrentDictionaryExample();
        await ConcurrentCollections.ConcurrentQueueExample();
        await ConcurrentCollections.ConcurrentStackExample();
        await ConcurrentCollections.ConcurrentBagExample();
        await ConcurrentCollections.BlockingCollectionExample();
        await ConcurrentCollections.MultipleConsumersExample();
        await ConcurrentCollections.ConcurrentDictionaryAdvanced();
        await ConcurrentCollections.WorkQueueExample();
        await ConcurrentCollections.ConcurrentCacheExample();
        ConcurrentCollections.PerformanceComparison();
        
        // Advanced patterns
        await AdvancedConcurrentPatterns.ObserverPatternExample();
        await AdvancedConcurrentPatterns.ConcurrentPipelineExample();
        await AdvancedConcurrentPatterns.RateLimiterExample();
        
        Console.WriteLine("=== Key Concurrent Collections Concepts ===");
        Console.WriteLine("1. ConcurrentDictionary: Thread-safe dictionary operations");
        Console.WriteLine("2. ConcurrentQueue: Thread-safe FIFO queue");
        Console.WriteLine("3. ConcurrentStack: Thread-safe LIFO stack");
        Console.WriteLine("4. ConcurrentBag: Thread-safe unordered collection");
        Console.WriteLine("5. BlockingCollection: Producer-consumer pattern with blocking");
        Console.WriteLine("6. GetOrAdd ensures atomic initialization");
        Console.WriteLine("7. AddOrUpdate provides atomic update logic");
        Console.WriteLine("8. TryTake/TryDequeue for non-blocking operations");
        Console.WriteLine("9. GetConsumingEnumerable for consumer loops");
        Console.WriteLine("10. Performance optimized for concurrent scenarios");
        Console.WriteLine("11. No need for external locking");
        Console.WriteLine("12. Lock-free algorithms where possible");
    }
}
