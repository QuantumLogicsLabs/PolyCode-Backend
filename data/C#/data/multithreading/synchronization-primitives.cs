using System;
using System.Collections.Concurrent;
using System.Collections.Generic;
using System.Threading;
using System.Threading.Tasks;

// Synchronization Primitives Examples

public class SynchronizationPrimitives
{
    // 1. Monitor (lock keyword)
    private static readonly object _lockObject = new object();
    private static int _sharedCounter = 0;
    
    public static void MonitorExample()
    {
        Console.WriteLine("=== Monitor (lock) Example ===");
        
        _sharedCounter = 0;
        var tasks = new List<Task>();
        
        for (int i = 0; i < 10; i++)
        {
            int taskId = i;
            tasks.Add(Task.Run(() => {
                for (int j = 0; j < 1000; j++)
                {
                    lock (_lockObject)
                    {
                        int temp = _sharedCounter;
                        Thread.Sleep(1); // Simulate work
                        _sharedCounter = temp + 1;
                        
                        if (j % 100 == 0)
                        {
                            Console.WriteLine($"Task {taskId}: Counter = {_sharedCounter}");
                        }
                    }
                }
            }));
        }
        
        Task.WaitAll(tasks.ToArray());
        Console.WriteLine($"Final counter: {_sharedCounter}\n");
    }
    
    // 2. Mutex (cross-process synchronization)
    private static Mutex _mutex = new Mutex(false, "MyApplicationMutex");
    
    public static void MutexExample()
    {
        Console.WriteLine("=== Mutex Example ===");
        
        Console.WriteLine("Trying to acquire mutex...");
        
        if (_mutex.WaitOne(1000)) // Wait for 1 second
        {
            try
            {
                Console.WriteLine("Mutex acquired. Doing work...");
                Thread.Sleep(2000);
                Console.WriteLine("Work completed.");
            }
            finally
            {
                _mutex.ReleaseMutex();
                Console.WriteLine("Mutex released.");
            }
        }
        else
        {
            Console.WriteLine("Could not acquire mutex within timeout.");
        }
        
        Console.WriteLine();
    }
    
    // 3. Semaphore (limiting concurrent access)
    private static SemaphoreSlim _semaphore = new SemaphoreSlim(3, 3); // Allow 3 concurrent threads
    
    public static async Task SemaphoreExample()
    {
        Console.WriteLine("=== Semaphore Example ===");
        
        var tasks = new List<Task>();
        
        for (int i = 0; i < 8; i++)
        {
            int taskId = i;
            tasks.Add(Task.Run(async () => {
                Console.WriteLine($"Task {taskId}: Waiting for semaphore...");
                
                await _semaphore.WaitAsync();
                try
                {
                    Console.WriteLine($"Task {taskId}: Acquired semaphore. Working...");
                    await Task.Delay(2000);
                    Console.WriteLine($"Task {taskId}: Work completed.");
                }
                finally
                {
                    _semaphore.Release();
                    Console.WriteLine($"Task {taskId}: Released semaphore.");
                }
            }));
        }
        
        await Task.WhenAll(tasks);
        Console.WriteLine();
    }
    
    // 4. AutoResetEvent
    private static AutoResetEvent _autoResetEvent = new AutoResetEvent(false);
    
    public static void AutoResetEventExample()
    {
        Console.WriteLine("=== AutoResetEvent Example ===");
        
        var worker = Task.Run(() => {
            Console.WriteLine("Worker waiting for signal...");
            
            while (true)
            {
                _autoResetEvent.WaitOne();
                Console.WriteLine("Worker received signal and is working...");
                Thread.Sleep(1000);
                Console.WriteLine("Worker work completed.");
            }
        });
        
        // Signal the worker multiple times
        for (int i = 0; i < 3; i++)
        {
            Console.WriteLine($"Main thread signaling worker (signal {i + 1})");
            _autoResetEvent.Set();
            Thread.Sleep(2000);
        }
        
        Console.WriteLine("Main thread done.\n");
    }
    
    // 5. ManualResetEvent
    private static ManualResetEvent _manualResetEvent = new ManualResetEvent(false);
    
    public static void ManualResetEventExample()
    {
        Console.WriteLine("=== ManualResetEvent Example ===");
        
        var workers = new List<Task>();
        
        // Create multiple workers
        for (int i = 0; i < 3; i++)
        {
            int workerId = i;
            workers.Add(Task.Run(() => {
                Console.WriteLine($"Worker {workerId}: Waiting for signal...");
                _manualResetEvent.WaitOne();
                Console.WriteLine($"Worker {workerId}: Received signal, working...");
                Thread.Sleep(1000);
                Console.WriteLine($"Worker {workerId}: Work completed.");
            }));
        }
        
        Thread.Sleep(1000);
        Console.WriteLine("Main thread: Setting event (all workers should start)");
        _manualResetEvent.Set();
        
        Thread.Sleep(2000);
        Console.WriteLine("Main thread: Resetting event");
        _manualResetEvent.Reset();
        
        Thread.Sleep(1000);
        Console.WriteLine("Main thread: Setting event again");
        _manualResetEvent.Set();
        
        Thread.Sleep(2000);
        
        Console.WriteLine("Main thread done.\n");
    }
    
    // 6. CountdownEvent
    public static async Task CountdownEventExample()
    {
        Console.WriteLine("=== CountdownEvent Example ===");
        
        var countdown = new CountdownEvent(5);
        var tasks = new List<Task>();
        
        for (int i = 0; i < 5; i++)
        {
            int taskId = i;
            tasks.Add(Task.Run(async () => {
                Console.WriteLine($"Task {taskId}: Starting work...");
                await Task.Delay(taskId * 500);
                Console.WriteLine($"Task {taskId}: Work completed, signaling.");
                countdown.Signal();
            }));
        }
        
        // Wait for all tasks to signal
        await Task.Run(() => countdown.Wait());
        Console.WriteLine("All tasks have completed.\n");
    }
    
    // 7. Barrier
    public static async Task BarrierExample()
    {
        Console.WriteLine("=== Barrier Example ===");
        
        var barrier = new Barrier(3, b => {
            Console.WriteLine($"Phase {b.CurrentPhaseNumber} completed. All participants reached barrier.");
        });
        
        var tasks = new List<Task>();
        
        for (int i = 0; i < 3; i++)
        {
            int participantId = i;
            tasks.Add(Task.Run(async () => {
                for (int phase = 0; phase < 3; phase++)
                {
                    Console.WriteLine($"Participant {participantId}: Phase {phase} work starting...");
                    await Task.Delay(participantId * 200);
                    Console.WriteLine($"Participant {participantId}: Phase {phase} work completed, waiting at barrier.");
                    barrier.SignalAndWait();
                }
            }));
        }
        
        await Task.WhenAll(tasks);
        Console.WriteLine();
    }
    
    // 8. ReaderWriterLockSlim
    private static ReaderWriterLockSlim _rwLock = new ReaderWriterLockSlim();
    private static List<string> _sharedList = new List<string>();
    
    public static async Task ReaderWriterLockExample()
    {
        Console.WriteLine("=== ReaderWriterLockSlim Example ===");
        
        var tasks = new List<Task>();
        
        // Writer tasks
        for (int i = 0; i < 2; i++)
        {
            int writerId = i;
            tasks.Add(Task.Run(async () => {
                for (int j = 0; j < 3; j++)
                {
                    _rwLock.EnterWriteLock();
                    try
                    {
                        string item = $"Item_{writerId}_{j}";
                        _sharedList.Add(item);
                        Console.WriteLine($"Writer {writerId}: Added {item}. List count: {_sharedList.Count}");
                        await Task.Delay(500);
                    }
                    finally
                    {
                        _rwLock.ExitWriteLock();
                    }
                    
                    await Task.Delay(1000);
                }
            }));
        }
        
        // Reader tasks
        for (int i = 0; i < 5; i++)
        {
            int readerId = i;
            tasks.Add(Task.Run(async () => {
                for (int j = 0; j < 5; j++)
                {
                    _rwLock.EnterReadLock();
                    try
                    {
                        Console.WriteLine($"Reader {readerId}: Reading list. Count: {_sharedList.Count}");
                        await Task.Delay(200);
                    }
                    finally
                    {
                        _rwLock.ExitReadLock();
                    }
                    
                    await Task.Delay(300);
                }
            }));
        }
        
        await Task.WhenAll(tasks);
        Console.WriteLine();
    }
    
    // 9. SpinLock
    private static SpinLock _spinLock = new SpinLock();
    
    public static void SpinLockExample()
    {
        Console.WriteLine("=== SpinLock Example ===");
        
        int sharedValue = 0;
        var tasks = new List<Task>();
        
        for (int i = 0; i < 10; i++)
        {
            int taskId = i;
            tasks.Add(Task.Run(() => {
                bool lockTaken = false;
                try
                {
                    _spinLock.Enter(ref lockTaken);
                    
                    int temp = sharedValue;
                    Thread.Sleep(10); // Simulate work
                    sharedValue = temp + 1;
                    
                    Console.WriteLine($"Task {taskId}: Incremented value to {sharedValue}");
                }
                finally
                {
                    if (lockTaken)
                        _spinLock.Exit();
                }
            }));
        }
        
        Task.WaitAll(tasks.ToArray());
        Console.WriteLine($"Final value: {sharedValue}\n");
    }
    
    // 10. Interlocked operations
    private static long _interlockedCounter = 0;
    
    public static void InterlockedExample()
    {
        Console.WriteLine("=== Interlocked Example ===");
        
        _interlockedCounter = 0;
        var tasks = new List<Task>();
        
        for (int i = 0; i < 1000; i++)
        {
            tasks.Add(Task.Run(() => {
                // Atomic increment
                Interlocked.Increment(ref _interlockedCounter);
                
                // Atomic compare and exchange
                long currentValue = Interlocked.Read(ref _interlockedCounter);
                
                // Atomic exchange
                long oldValue = Interlocked.Exchange(ref _interlockedCounter, currentValue + 10);
                
                // Atomic add
                Interlocked.Add(ref _interlockedCounter, -9); // Net effect: +1
            }));
        }
        
        Task.WaitAll(tasks.ToArray());
        Console.WriteLine($"Final counter: {_interlockedCounter}\n");
    }
}

// Advanced Synchronization Patterns
public class AdvancedSynchronization
{
    // 1. Producer-Consumer with SemaphoreSlim
    public static async Task ProducerConsumerWithSemaphore()
    {
        Console.WriteLine("=== Producer-Consumer with SemaphoreSlim ===");
        
        var buffer = new ConcurrentQueue<int>();
        var emptySemaphore = new SemaphoreSlim(0, 10); // Initially empty, max 10 items
        var fullSemaphore = new SemaphoreSlim(10, 10);  // Initially can add 10 items
        
        var producer = Task.Run(async () => {
            for (int i = 1; i <= 20; i++)
            {
                await fullSemaphore.WaitAsync(); // Wait for space
                buffer.Enqueue(i);
                Console.WriteLine($"Produced: {i}");
                emptySemaphore.Release(); // Signal that item is available
                await Task.Delay(100);
            }
        });
        
        var consumer = Task.Run(async () => {
            for (int i = 1; i <= 20; i++)
            {
                await emptySemaphore.WaitAsync(); // Wait for item
                if (buffer.TryDequeue(out int item))
                {
                    Console.WriteLine($"Consumed: {item}");
                }
                fullSemaphore.Release(); // Signal that space is available
                await Task.Delay(150);
            }
        });
        
        await Task.WhenAll(producer, consumer);
        Console.WriteLine();
    }
    
    // 2. Async coordination with AsyncLock
    public class AsyncLock
    {
        private readonly SemaphoreSlim _semaphore = new SemaphoreSlim(1, 1);
        
        public async Task<IDisposable> LockAsync()
        {
            await _semaphore.WaitAsync();
            return new LockReleaser(_semaphore);
        }
        
        private class LockReleaser : IDisposable
        {
            private readonly SemaphoreSlim _semaphore;
            
            public LockReleaser(SemaphoreSlim semaphore)
            {
                _semaphore = semaphore;
            }
            
            public void Dispose()
            {
                _semaphore.Release();
            }
        }
    }
    
    public static async Task AsyncLockExample()
    {
        Console.WriteLine("=== Async Lock Example ===");
        
        var asyncLock = new AsyncLock();
        var sharedResource = 0;
        
        var tasks = new List<Task>();
        
        for (int i = 0; i < 5; i++)
        {
            int taskId = i;
            tasks.Add(Task.Run(async () => {
                using (await asyncLock.LockAsync())
                {
                    int temp = sharedResource;
                    Console.WriteLine($"Task {taskId}: Read value {temp}");
                    await Task.Delay(500);
                    sharedResource = temp + 1;
                    Console.WriteLine($"Task {taskId}: Wrote value {sharedResource}");
                }
            }));
        }
        
        await Task.WhenAll(tasks);
        Console.WriteLine($"Final value: {sharedResource}\n");
    }
    
    // 3. Latch pattern (one-time signal)
    public class Latch
    {
        private readonly TaskCompletionSource<bool> _tcs = new TaskCompletionSource<bool>();
        
        public void Signal()
        {
            _tcs.SetResult(true);
        }
        
        public Task WaitAsync()
        {
            return _tcs.Task;
        }
    }
    
    public static async Task LatchExample()
    {
        Console.WriteLine("=== Latch Example ===");
        
        var latch = new Latch();
        var tasks = new List<Task>();
        
        // Create waiting tasks
        for (int i = 0; i < 5; i++)
        {
            int taskId = i;
            tasks.Add(Task.Run(async () => {
                Console.WriteLine($"Task {taskId}: Waiting for signal...");
                await latch.WaitAsync();
                Console.WriteLine($"Task {taskId}: Signal received!");
            }));
        }
        
        // Signal after delay
        _ = Task.Delay(2000).ContinueWith(_ => {
            Console.WriteLine("Main thread: Signaling latch");
            latch.Signal();
        });
        
        await Task.WhenAll(tasks);
        Console.WriteLine();
    }
    
    // 4. Bounded buffer using SemaphoreSlim
    public class BoundedBuffer<T>
    {
        private readonly Queue<T> _buffer = new Queue<T>();
        private readonly SemaphoreSlim _itemsAvailable;
        private readonly SemaphoreSlim _spaceAvailable;
        private readonly int _capacity;
        
        public BoundedBuffer(int capacity)
        {
            _capacity = capacity;
            _itemsAvailable = new SemaphoreSlim(0, capacity);
            _spaceAvailable = new SemaphoreSlim(capacity, capacity);
        }
        
        public async Task WriteAsync(T item)
        {
            await _spaceAvailable.WaitAsync();
            lock (_buffer)
            {
                _buffer.Enqueue(item);
            }
            _itemsAvailable.Release();
        }
        
        public async Task<T> ReadAsync()
        {
            await _itemsAvailable.WaitAsync();
            lock (_buffer)
            {
                return _buffer.Dequeue();
            }
        }
    }
    
    public static async Task BoundedBufferExample()
    {
        Console.WriteLine("=== Bounded Buffer Example ===");
        
        var buffer = new BoundedBuffer<int>(capacity: 5);
        
        var producer = Task.Run(async () => {
            for (int i = 1; i <= 10; i++)
            {
                await buffer.WriteAsync(i);
                Console.WriteLine($"Produced: {i}");
                await Task.Delay(200);
            }
        });
        
        var consumer = Task.Run(async () => {
            for (int i = 1; i <= 10; i++)
            {
                int item = await buffer.ReadAsync();
                Console.WriteLine($"Consumed: {item}");
                await Task.Delay(300);
            }
        });
        
        await Task.WhenAll(producer, consumer);
        Console.WriteLine();
    }
    
    // 5. Timeout with cancellation
    public static async Task TimeoutWithCancellation()
    {
        Console.WriteLine("=== Timeout with Cancellation ===");
        
        using var cts = new CancellationTokenSource(TimeSpan.FromSeconds(2));
        
        try
        {
            await Task.Run(async () => {
                for (int i = 0; i < 10; i++)
                {
                    cts.Token.ThrowIfCancellationRequested();
                    Console.WriteLine($"Working... step {i + 1}");
                    await Task.Delay(500, cts.Token);
                }
            }, cts.Token);
            
            Console.WriteLine("Task completed successfully");
        }
        catch (OperationCanceledException)
        {
            Console.WriteLine("Task was cancelled due to timeout");
        }
        
        Console.WriteLine();
    }
}

// Deadlock Detection and Prevention
public class DeadlockExamples
{
    // Deadlock example
    private static readonly object _lock1 = new object();
    private static readonly object _lock2 = new object();
    
    public static void DeadlockDemo()
    {
        Console.WriteLine("=== Deadlock Demo ===");
        Console.WriteLine("This demonstrates a deadlock situation (will hang)");
        
        // Uncomment to see deadlock
        /*
        var task1 = Task.Run(() => {
            lock (_lock1)
            {
                Console.WriteLine("Task 1: Acquired lock1");
                Thread.Sleep(100);
                lock (_lock2)
                {
                    Console.WriteLine("Task 1: Acquired lock2");
                }
            }
        });
        
        var task2 = Task.Run(() => {
            lock (_lock2)
            {
                Console.WriteLine("Task 2: Acquired lock2");
                Thread.Sleep(100);
                lock (_lock1)
                {
                    Console.WriteLine("Task 2: Acquired lock1");
                }
            }
        });
        
        Task.WaitAll(task1, task2);
        */
        
        Console.WriteLine("Deadlock demo commented out to prevent hanging\n");
    }
    
    // Deadlock prevention - consistent ordering
    public static void DeadlockPreventionDemo()
    {
        Console.WriteLine("=== Deadlock Prevention Demo ===");
        
        var task1 = Task.Run(() => {
            // Always acquire locks in the same order
            lock (_lock1)
            {
                Console.WriteLine("Task 1: Acquired lock1");
                Thread.Sleep(100);
                lock (_lock2)
                {
                    Console.WriteLine("Task 1: Acquired lock2");
                }
            }
        });
        
        var task2 = Task.Run(() => {
            // Same order as task1
            lock (_lock1)
            {
                Console.WriteLine("Task 2: Acquired lock1");
                Thread.Sleep(100);
                lock (_lock2)
                {
                    Console.WriteLine("Task 2: Acquired lock2");
                }
            }
        });
        
        Task.WaitAll(task1, task2);
        Console.WriteLine("No deadlock occurred!\n");
    }
}

// Main demonstration
public class SynchronizationPrimitivesDemo
{
    public static async Task Main(string[] args)
    {
        Console.WriteLine("=== Synchronization Primitives Demonstration ===");
        Console.WriteLine("Thread synchronization primitives for coordinating access to shared resources.\n");
        
        // Basic synchronization
        SynchronizationPrimitives.MonitorExample();
        SynchronizationPrimitives.MutexExample();
        await SynchronizationPrimitives.SemaphoreExample();
        SynchronizationPrimitives.AutoResetEventExample();
        SynchronizationPrimitives.ManualResetEventExample();
        await SynchronizationPrimitives.CountdownEventExample();
        await SynchronizationPrimitives.BarrierExample();
        await SynchronizationPrimitives.ReaderWriterLockExample();
        SynchronizationPrimitives.SpinLockExample();
        SynchronizationPrimitives.InterlockedExample();
        
        // Advanced patterns
        await AdvancedSynchronization.ProducerConsumerWithSemaphore();
        await AdvancedSynchronization.AsyncLockExample();
        await AdvancedSynchronization.LatchExample();
        await AdvancedSynchronization.BoundedBufferExample();
        await AdvancedSynchronization.TimeoutWithCancellation();
        
        // Deadlock examples
        DeadlockExamples.DeadlockDemo();
        DeadlockExamples.DeadlockPreventionDemo();
        
        Console.WriteLine("=== Key Synchronization Concepts ===");
        Console.WriteLine("1. Monitor (lock): Mutual exclusion for critical sections");
        Console.WriteLine("2. Mutex: Cross-process synchronization");
        Console.WriteLine("3. Semaphore: Limit concurrent access to resources");
        Console.WriteLine("4. AutoResetEvent: Automatic reset after each wait");
        Console.WriteLine("5. ManualResetEvent: Manual control over signaling");
        Console.WriteLine("6. CountdownEvent: Wait for multiple signals");
        Console.WriteLine("7. Barrier: Synchronize multiple threads at a point");
        Console.WriteLine("8. ReaderWriterLock: Multiple readers, exclusive writers");
        Console.WriteLine("9. SpinLock: Low-contention locking");
        Console.WriteLine("10. Interlocked: Atomic operations on simple types");
        Console.WriteLine("11. Always release locks in finally blocks");
        Console.WriteLine("12. Avoid lock ordering to prevent deadlocks");
        Console.WriteLine("13. Use async-friendly primitives in async code");
        Console.WriteLine("14. Choose the right primitive for your scenario");
    }
}
