using System;
using System.Threading;
using System.Threading.Tasks;

// Thread Basics and Fundamentals

public class ThreadBasics
{
    // 1. Creating and starting a thread
    public static void CreateAndStartThread()
    {
        Console.WriteLine("=== Creating and Starting Thread ===");
        
        // Create a new thread
        Thread thread = new Thread(new ThreadStart(DoWork));
        
        Console.WriteLine($"Main thread ID: {Thread.CurrentThread.ManagedThreadId}");
        Console.WriteLine($"Worker thread ID: {thread.ManagedThreadId} (not started yet)");
        
        // Start the thread
        thread.Start();
        
        Console.WriteLine("Thread started, main thread continues...");
        
        // Wait for the thread to complete
        thread.Join();
        
        Console.WriteLine("Thread completed, main thread continues...\n");
    }
    
    // 2. Thread with parameters
    public static void ThreadWithParameters()
    {
        Console.WriteLine("=== Thread with Parameters ===");
        
        // Create thread with parameter using ParameterizedThreadStart
        Thread thread = new Thread(new ParameterizedThreadStart(DoWorkWithParameter));
        
        // Start thread with parameter
        thread.Start("Hello from parameterized thread!");
        
        thread.Join();
        Console.WriteLine();
    }
    
    // 3. Thread with lambda expression
    public static void ThreadWithLambda()
    {
        Console.WriteLine("=== Thread with Lambda Expression ===");
        
        string message = "Hello from lambda thread!";
        
        Thread thread = new Thread(() => {
            Console.WriteLine($"Lambda thread started. Thread ID: {Thread.CurrentThread.ManagedThreadId}");
            Console.WriteLine($"Message: {message}");
            Thread.Sleep(1000);
            Console.WriteLine("Lambda thread completed");
        });
        
        thread.Start();
        thread.Join();
        Console.WriteLine();
    }
    
    // 4. Thread properties and configuration
    public static void ThreadProperties()
    {
        Console.WriteLine("=== Thread Properties ===");
        
        Thread thread = new Thread(() => {
            Console.WriteLine($"Thread Name: {Thread.CurrentThread.Name}");
            Console.WriteLine($"Thread ID: {Thread.CurrentThread.ManagedThreadId}");
            Console.WriteLine($"Is Background: {Thread.CurrentThread.IsBackground}");
            Console.WriteLine($"Is Alive: {Thread.CurrentThread.IsAlive}");
            Console.WriteLine($"Priority: {Thread.CurrentThread.Priority}");
            Console.WriteLine($"Thread State: {Thread.CurrentThread.ThreadState}");
        });
        
        // Set thread properties before starting
        thread.Name = "WorkerThread";
        thread.IsBackground = false;
        thread.Priority = ThreadPriority.AboveNormal;
        
        thread.Start();
        thread.Join();
        Console.WriteLine();
    }
    
    // 5. Thread synchronization with Join
    public static void ThreadSynchronizationWithJoin()
    {
        Console.WriteLine("=== Thread Synchronization with Join ===");
        
        Thread thread1 = new Thread(() => {
            Console.WriteLine("Thread 1 started");
            Thread.Sleep(2000);
            Console.WriteLine("Thread 1 completed");
        });
        
        Thread thread2 = new Thread(() => {
            Console.WriteLine("Thread 2 started");
            Thread.Sleep(1000);
            Console.WriteLine("Thread 2 completed");
        });
        
        Console.WriteLine("Starting both threads...");
        thread1.Start();
        thread2.Start();
        
        Console.WriteLine("Waiting for both threads to complete...");
        thread1.Join(); // Wait for thread1 to complete
        thread2.Join(); // Wait for thread2 to complete
        
        Console.WriteLine("Both threads completed\n");
    }
    
    // 6. Thread with timeout
    public static void ThreadWithTimeout()
    {
        Console.WriteLine("=== Thread with Timeout ===");
        
        Thread thread = new Thread(() => {
            Console.WriteLine("Long-running thread started");
            Thread.Sleep(3000); // Simulate 3 seconds of work
            Console.WriteLine("Long-running thread completed");
        });
        
        thread.Start();
        
        Console.WriteLine("Waiting for thread with timeout (2 seconds)...");
        
        bool completed = thread.Join(2000); // Wait for 2 seconds
        
        if (completed)
        {
            Console.WriteLine("Thread completed within timeout");
        }
        else
        {
            Console.WriteLine("Thread did not complete within timeout");
            
            // You could choose to abort the thread (not recommended)
            // thread.Abort();
            
            // Or wait for it to complete
            Console.WriteLine("Waiting for thread to complete...");
            thread.Join();
        }
        
        Console.WriteLine();
    }
    
    // 7. Background vs Foreground threads
    public static void BackgroundVsForeground()
    {
        Console.WriteLine("=== Background vs Foreground Threads ===");
        
        // Foreground thread (default)
        Thread foregroundThread = new Thread(() => {
            Console.WriteLine("Foreground thread started");
            Thread.Sleep(2000);
            Console.WriteLine("Foreground thread completed");
        });
        foregroundThread.IsBackground = false;
        
        // Background thread
        Thread backgroundThread = new Thread(() => {
            Console.WriteLine("Background thread started");
            Thread.Sleep(2000);
            Console.WriteLine("Background thread completed");
        });
        backgroundThread.IsBackground = true;
        
        Console.WriteLine("Starting foreground thread...");
        foregroundThread.Start();
        
        Console.WriteLine("Starting background thread...");
        backgroundThread.Start();
        
        Console.WriteLine("Main thread sleeping for 1 second...");
        Thread.Sleep(1000);
        
        Console.WriteLine("Main thread exiting...");
        Console.WriteLine("(Foreground thread will keep application alive)");
        Console.WriteLine("(Background thread will be terminated when main thread exits)");
        
        // Wait for foreground thread to see the difference
        foregroundThread.Join();
        
        Console.WriteLine("Main thread actually exiting now\n");
    }
    
    // 8. Thread priority
    public static void ThreadPriorityDemo()
    {
        Console.WriteLine("=== Thread Priority Demo ===");
        
        Thread highPriorityThread = new Thread(() => {
            Console.WriteLine("High priority thread started");
            for (int i = 0; i < 5; i++)
            {
                Console.WriteLine($"High priority: {i}");
                Thread.Sleep(100);
            }
            Console.WriteLine("High priority thread completed");
        });
        highPriorityThread.Priority = ThreadPriority.Highest;
        
        Thread lowPriorityThread = new Thread(() => {
            Console.WriteLine("Low priority thread started");
            for (int i = 0; i < 5; i++)
            {
                Console.WriteLine($"Low priority: {i}");
                Thread.Sleep(100);
            }
            Console.WriteLine("Low priority thread completed");
        });
        lowPriorityThread.Priority = ThreadPriority.Lowest;
        
        highPriorityThread.Start();
        lowPriorityThread.Start();
        
        highPriorityThread.Join();
        lowPriorityThread.Join();
        
        Console.WriteLine();
    }
    
    // 9. Thread local storage
    public static void ThreadLocalStorage()
    {
        Console.WriteLine("=== Thread Local Storage ===");
        
        // ThreadStatic field
        ThreadStaticDemo();
        
        // ThreadLocal<T>
        ThreadLocalDemo();
        
        Console.WriteLine();
    }
    
    // 10. Thread exception handling
    public static void ThreadExceptionHandling()
    {
        Console.WriteLine("=== Thread Exception Handling ===");
        
        Thread thread = new Thread(() => {
            try
            {
                Console.WriteLine("Thread starting work...");
                Thread.Sleep(500);
                
                // Simulate an exception
                throw new InvalidOperationException("Something went wrong in thread!");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Exception caught in thread: {ex.Message}");
            }
        });
        
        thread.Start();
        thread.Join();
        
        Console.WriteLine("Thread exception handling demo completed\n");
    }
    
    // Helper methods
    private static void DoWork()
    {
        Console.WriteLine($"Worker thread started. Thread ID: {Thread.CurrentThread.ManagedThreadId}");
        
        for (int i = 1; i <= 5; i++)
        {
            Console.WriteLine($"Working... step {i}");
            Thread.Sleep(500); // Simulate work
        }
        
        Console.WriteLine("Worker thread completed");
    }
    
    private static void DoWorkWithParameter(object data)
    {
        Console.WriteLine($"Parameterized thread started. Thread ID: {Thread.CurrentThread.ManagedThreadId}");
        Console.WriteLine($"Received parameter: {data}");
        
        Thread.Sleep(1000);
        
        Console.WriteLine("Parameterized thread completed");
    }
    
    private static void ThreadStaticDemo()
    {
        Console.WriteLine("ThreadStatic field demo:");
        
        for (int i = 0; i < 3; i++)
        {
            int threadNum = i;
            Thread thread = new Thread(() => {
                ThreadStaticField.Counter = threadNum;
                Console.WriteLine($"Thread {threadNum}: Counter = {ThreadStaticField.Counter}");
            });
            thread.Start();
            thread.Join();
        }
    }
    
    private static void ThreadLocalDemo()
    {
        Console.WriteLine("ThreadLocal<T> demo:");
        
        var threadLocalCounter = new ThreadLocal<int>(() => Thread.CurrentThread.ManagedThreadId);
        
        for (int i = 0; i < 3; i++)
        {
            Thread thread = new Thread(() => {
                Console.WriteLine($"Thread {threadLocalCounter.Value}: ThreadLocal value = {threadLocalCounter.Value}");
            });
            thread.Start();
            thread.Join();
        }
    }
}

// Class with ThreadStatic field
public static class ThreadStaticField
{
    [ThreadStatic]
    public static int Counter;
}

// Thread Pool Demo
public class ThreadPoolDemo
{
    public static void ThreadPoolBasics()
    {
        Console.WriteLine("=== Thread Pool Basics ===");
        
        Console.WriteLine($"Available worker threads before: {ThreadPool.GetAvailableThreads(out int workerThreads, out int completionPortThreads); workerThreads}");
        
        // Queue work item to thread pool
        ThreadPool.QueueUserWorkItem(state => {
            Console.WriteLine($"Thread pool work item executed on thread {Thread.CurrentThread.ManagedThreadId}");
            Console.WriteLine($"State: {state}");
            Thread.Sleep(1000);
            Console.WriteLine("Thread pool work item completed");
        }, "Hello from thread pool!");
        
        Console.WriteLine($"Available worker threads after: {ThreadPool.GetAvailableThreads(out workerThreads, out completionPortThreads); workerThreads}");
        
        // Wait a bit for the work item to complete
        Thread.Sleep(2000);
        Console.WriteLine();
    }
    
    public static void ThreadPoolWithMultipleItems()
    {
        Console.WriteLine("=== Multiple Thread Pool Items ===");
        
        ManualResetEvent[] doneEvents = new ManualResetEvent[5];
        
        for (int i = 0; i < 5; i++)
        {
            doneEvents[i] = new ManualResetEvent(false);
            int workItem = i;
            
            ThreadPool.QueueUserWorkItem(state => {
                Console.WriteLine($"Work item {workItem} started on thread {Thread.CurrentThread.ManagedThreadId}");
                Thread.Sleep(1000); // Simulate work
                Console.WriteLine($"Work item {workItem} completed");
                doneEvents[workItem].Set();
            });
        }
        
        Console.WriteLine("Waiting for all work items to complete...");
        WaitHandle.WaitAll(doneEvents);
        Console.WriteLine("All work items completed\n");
    }
}

// Thread Safety Demo
public class ThreadSafetyDemo
{
    private static int _counter = 0;
    private static readonly object _lock = new object();
    
    public static void UnsafeIncrement()
    {
        Console.WriteLine("=== Unsafe Increment (Race Condition) ===");
        
        _counter = 0;
        Thread[] threads = new Thread[10];
        
        for (int i = 0; i < 10; i++)
        {
            threads[i] = new Thread(() => {
                for (int j = 0; j < 1000; j++)
                {
                    _counter++; // Not thread-safe!
                }
            });
            threads[i].Start();
        }
        
        foreach (Thread thread in threads)
        {
            thread.Join();
        }
        
        Console.WriteLine($"Expected counter: 10000");
        Console.WriteLine($"Actual counter: {_counter}");
        Console.WriteLine($"Race condition occurred: {_counter != 10000}\n");
    }
    
    public static void SafeIncrement()
    {
        Console.WriteLine("=== Safe Increment (with Lock) ===");
        
        _counter = 0;
        Thread[] threads = new Thread[10];
        
        for (int i = 0; i < 10; i++)
        {
            threads[i] = new Thread(() => {
                for (int j = 0; j < 1000; j++)
                {
                    lock (_lock)
                    {
                        _counter++; // Thread-safe with lock
                    }
                }
            });
            threads[i].Start();
        }
        
        foreach (Thread thread in threads)
        {
            thread.Join();
        }
        
        Console.WriteLine($"Expected counter: 10000");
        Console.WriteLine($"Actual counter: {_counter}");
        Console.WriteLine($"Thread-safe: {_counter == 10000}\n");
    }
    
    public static void InterlockedIncrement()
    {
        Console.WriteLine("=== Safe Increment (with Interlocked) ===");
        
        _counter = 0;
        Thread[] threads = new Thread[10];
        
        for (int i = 0; i < 10; i++)
        {
            threads[i] = new Thread(() => {
                for (int j = 0; j < 1000; j++)
                {
                    Interlocked.Increment(ref _counter); // Thread-safe with Interlocked
                }
            });
            threads[i].Start();
        }
        
        foreach (Thread thread in threads)
        {
            thread.Join();
        }
        
        Console.WriteLine($"Expected counter: 10000");
        Console.WriteLine($"Actual counter: {_counter}");
        Console.WriteLine($"Thread-safe: {_counter == 10000}\n");
    }
}

// Main demonstration
public class ThreadBasicsDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Thread Basics Demonstration ===");
        Console.WriteLine("This demo covers fundamental threading concepts in C#.\n");
        
        // Basic thread operations
        ThreadBasics.CreateAndStartThread();
        ThreadBasics.ThreadWithParameters();
        ThreadBasics.ThreadWithLambda();
        ThreadBasics.ThreadProperties();
        ThreadBasics.ThreadSynchronizationWithJoin();
        ThreadBasics.ThreadWithTimeout();
        ThreadBasics.BackgroundVsForeground();
        ThreadBasics.ThreadPriorityDemo();
        ThreadBasics.ThreadLocalStorage();
        ThreadBasics.ThreadExceptionHandling();
        
        // Thread pool
        ThreadPoolDemo.ThreadPoolBasics();
        ThreadPoolDemo.ThreadPoolWithMultipleItems();
        
        // Thread safety
        ThreadSafetyDemo.UnsafeIncrement();
        ThreadSafetyDemo.SafeIncrement();
        ThreadSafetyDemo.InterlockedIncrement();
        
        Console.WriteLine("=== Key Thread Concepts ===");
        Console.WriteLine("1. Threads are the smallest unit of execution");
        Console.WriteLine("2. Use Thread.Start() to begin execution");
        Console.WriteLine("3. Use Thread.Join() to wait for completion");
        Console.WriteLine("4. Background threads don't keep application alive");
        Console.WriteLine("5. Thread priority affects scheduling order");
        Console.WriteLine("6. ThreadStatic and ThreadLocal provide thread-specific storage");
        Console.WriteLine("7. Thread pool reuses threads for efficiency");
        Console.WriteLine("8. Always use synchronization for shared data");
        Console.WriteLine("9. Interlocked provides atomic operations for simple types");
        Console.WriteLine("10. Handle exceptions within threads");
    }
}
