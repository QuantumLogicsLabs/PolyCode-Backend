using System;
using System.Collections.Generic;
using System.Threading;
using System.Threading.Tasks;

// Task Parallel Library (TPL) Examples

public class TaskParallelLibrary
{
    // 1. Basic Task creation and execution
    public static async Task BasicTaskExample()
    {
        Console.WriteLine("=== Basic Task Example ===");
        
        // Create and start a task
        Task task1 = Task.Run(() => {
            Console.WriteLine($"Task 1 running on thread {Thread.CurrentThread.ManagedThreadId}");
            Thread.Sleep(1000);
            Console.WriteLine("Task 1 completed");
        });
        
        // Create a task with factory
        Task task2 = Task.Factory.StartNew(() => {
            Console.WriteLine($"Task 2 running on thread {Thread.CurrentThread.ManagedThreadId}");
            Thread.Sleep(800);
            Console.WriteLine("Task 2 completed");
        });
        
        // Wait for both tasks to complete
        await Task.WhenAll(task1, task2);
        
        Console.WriteLine("Both tasks completed\n");
    }
    
    // 2. Task with return value
    public static async Task TaskWithReturnValue()
    {
        Console.WriteLine("=== Task with Return Value ===");
        
        // Task that returns a value
        Task<int> calculationTask = Task.Run(() => {
            Console.WriteLine($"Calculation running on thread {Thread.CurrentThread.ManagedThreadId}");
            int result = 0;
            for (int i = 1; i <= 100; i++)
            {
                result += i;
            }
            Console.WriteLine($"Calculation completed: {result}");
            return result;
        });
        
        // Wait for and get the result
        int result = await calculationTask;
        Console.WriteLine($"Final result: {result}\n");
    }
    
    // 3. Task continuation
    public static async Task TaskContinuation()
    {
        Console.WriteLine("=== Task Continuation ===");
        
        Task<int> initialTask = Task.Run(() => {
            Console.WriteLine("Initial task running...");
            Thread.Sleep(1000);
            return 42;
        });
        
        // Continue with the result
        Task<string> continuationTask = initialTask.ContinueWith(antecedent => {
            Console.WriteLine($"Continuation task running with result: {antecedent.Result}");
            return $"The answer is {antecedent.Result}";
        });
        
        string finalResult = await continuationTask;
        Console.WriteLine($"Final result: {finalResult}\n");
    }
    
    // 4. Multiple task continuations
    public static async Task MultipleTaskContinuations()
    {
        Console.WriteLine("=== Multiple Task Continuations ===");
        
        Task<string> task = Task.Run(() => {
            Console.WriteLine("Initial task running...");
            Thread.Sleep(500);
            return "Hello";
        })
        .ContinueWith(antecedent => {
            Console.WriteLine($"First continuation: {antecedent.Result}");
            return antecedent.Result + ", World";
        })
        .ContinueWith(antecedent => {
            Console.WriteLine($"Second continuation: {antecedent.Result}");
            return antecedent.Result + "!";
        });
        
        string result = await task;
        Console.WriteLine($"Final result: {result}\n");
    }
    
    // 5. Task.WhenAll - Wait for all tasks
    public static async Task TaskWhenAllExample()
    {
        Console.WriteLine("=== Task.WhenAll Example ===");
        
        var tasks = new List<Task<int>>();
        
        for (int i = 1; i <= 5; i++)
        {
            int taskNum = i;
            tasks.Add(Task.Run(() => {
                Console.WriteLine($"Task {taskNum} starting");
                Thread.Sleep(taskNum * 200);
                Console.WriteLine($"Task {taskNum} completed");
                return taskNum * 10;
            }));
        }
        
        Console.WriteLine("Waiting for all tasks to complete...");
        int[] results = await Task.WhenAll(tasks);
        
        Console.WriteLine($"All tasks completed. Results: [{string.Join(", ", results)}]\n");
    }
    
    // 6. Task.WhenAny - Wait for any task
    public static async Task TaskWhenAnyExample()
    {
        Console.WriteLine("=== Task.WhenAny Example ===");
        
        var tasks = new List<Task<string>>();
        
        tasks.Add(Task.Run(() => {
            Thread.Sleep(2000);
            return "Slow task completed";
        }));
        
        tasks.Add(Task.Run(() => {
            Thread.Sleep(1000);
            return "Fast task completed";
        }));
        
        tasks.Add(Task.Run(() => {
            Thread.Sleep(1500);
            return "Medium task completed";
        }));
        
        Console.WriteLine("Waiting for any task to complete...");
        Task<string> completedTask = await Task.WhenAny(tasks);
        
        Console.WriteLine($"First completed task: {completedTask.Result}");
        
        // Wait for remaining tasks
        await Task.WhenAll(tasks);
        Console.WriteLine("All tasks completed\n");
    }
    
    // 7. Task exception handling
    public static async Task TaskExceptionHandling()
    {
        Console.WriteLine("=== Task Exception Handling ===");
        
        try
        {
            await Task.Run(() => {
                Console.WriteLine("Task running...");
                Thread.Sleep(500);
                throw new InvalidOperationException("Task failed!");
            });
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Caught exception: {ex.Message}");
        }
        
        // Multiple tasks with exceptions
        var tasks = new List<Task>();
        
        tasks.Add(Task.Run(() => {
            throw new ArgumentException("Task 1 failed");
        }));
        
        tasks.Add(Task.Run(() => {
            Thread.Sleep(1000);
            throw new InvalidOperationException("Task 2 failed");
        }));
        
        tasks.Add(Task.Run(() => {
            Thread.Sleep(500);
            Console.WriteLine("Task 3 completed successfully");
        }));
        
        try
        {
            await Task.WhenAll(tasks);
        }
        catch (Exception ex)
        {
            Console.WriteLine($"WhenAll exception: {ex.Message}");
            
            // Check all task exceptions
            foreach (var task in tasks)
            {
                if (task.IsFaulted)
                {
                    Console.WriteLine($"Task exception: {task.Exception?.InnerException?.Message}");
                }
            }
        }
        
        Console.WriteLine();
    }
    
    // 8. Task cancellation
    public static async Task TaskCancellation()
    {
        Console.WriteLine("=== Task Cancellation ===");
        
        var cts = new CancellationTokenSource();
        CancellationToken token = cts.Token;
        
        Task longRunningTask = Task.Run(async () => {
            Console.WriteLine("Long-running task started");
            
            for (int i = 1; i <= 10; i++)
            {
                token.ThrowIfCancellationRequested();
                Console.WriteLine($"Working... step {i}/10");
                await Task.Delay(500, token);
            }
            
            Console.WriteLine("Long-running task completed");
        }, token);
        
        // Cancel after 2 seconds
        _ = Task.Delay(2000).ContinueWith(_ => {
            Console.WriteLine("Cancelling task...");
            cts.Cancel();
        });
        
        try
        {
            await longRunningTask;
            Console.WriteLine("Task completed successfully");
        }
        catch (OperationCanceledException)
        {
            Console.WriteLine("Task was cancelled");
        }
        
        Console.WriteLine();
    }
    
    // 9. Parallel.For
    public static void ParallelForExample()
    {
        Console.WriteLine("=== Parallel.For Example ===");
        
        Parallel.For(0, 10, i => {
            Console.WriteLine($"Iteration {i} on thread {Thread.CurrentThread.ManagedThreadId}");
            Thread.Sleep(100);
        });
        
        Console.WriteLine("Parallel.For completed\n");
    }
    
    // 10. Parallel.ForEach
    public static void ParallelForEachExample()
    {
        Console.WriteLine("=== Parallel.ForEach Example ===");
        
        var numbers = new List<int> { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };
        
        Parallel.ForEach(numbers, number => {
            Console.WriteLine($"Processing {number} on thread {Thread.CurrentThread.ManagedThreadId}");
            Thread.Sleep(100);
        });
        
        Console.WriteLine("Parallel.ForEach completed\n");
    }
    
    // 11. Parallel.Invoke
    public static void ParallelInvokeExample()
    {
        Console.WriteLine("=== Parallel.Invoke Example ===");
        
        Parallel.Invoke(
            () => {
                Console.WriteLine($"Action 1 on thread {Thread.CurrentThread.ManagedThreadId}");
                Thread.Sleep(1000);
                Console.WriteLine("Action 1 completed");
            },
            () => {
                Console.WriteLine($"Action 2 on thread {Thread.CurrentThread.ManagedThreadId}");
                Thread.Sleep(800);
                Console.WriteLine("Action 2 completed");
            },
            () => {
                Console.WriteLine($"Action 3 on thread {Thread.CurrentThread.ManagedThreadId}");
                Thread.Sleep(600);
                Console.WriteLine("Action 3 completed");
            }
        );
        
        Console.WriteLine("Parallel.Invoke completed\n");
    }
    
    // 12. Parallel options and breaking
    public static void ParallelWithOptions()
    {
        Console.WriteLine("=== Parallel with Options ===");
        
        var options = new ParallelOptions
        {
            MaxDegreeOfParallelism = 2 // Limit to 2 threads
        };
        
        Parallel.For(0, 10, options, (i, state) => {
            Console.WriteLine($"Iteration {i} on thread {Thread.CurrentThread.ManagedThreadId}");
            
            if (i == 5)
            {
                Console.WriteLine("Breaking at iteration 5");
                state.Break();
            }
            
            Thread.Sleep(100);
        });
        
        Console.WriteLine("Parallel.For with options completed\n");
    }
    
    // 13. Task scheduler and synchronization context
    public static async Task TaskSchedulerDemo()
    {
        Console.WriteLine("=== Task Scheduler Demo ===");
        
        Console.WriteLine($"Main thread ID: {Thread.CurrentThread.ManagedThreadId}");
        Console.WriteLine($"Main thread has sync context: {SynchronizationContext.Current != null}");
        
        await Task.Run(() => {
            Console.WriteLine($"Task thread ID: {Thread.CurrentThread.ManagedThreadId}");
            Console.WriteLine($"Task thread has sync context: {SynchronizationContext.Current != null}");
        });
        
        Console.WriteLine($"After await, thread ID: {Thread.CurrentThread.ManagedThreadId}");
        Console.WriteLine();
    }
    
    // 14. ConfigureAwait
    public static async Task ConfigureAwaitDemo()
    {
        Console.WriteLine("=== ConfigureAwait Demo ===");
        
        Console.WriteLine($"Main thread ID: {Thread.CurrentThread.ManagedThreadId}");
        
        await Task.Run(() => {
            Console.WriteLine($"Task thread ID: {Thread.CurrentThread.ManagedThreadId}");
        }).ConfigureAwait(false);
        
        Console.WriteLine($"After ConfigureAwait(false), thread ID: {Thread.CurrentThread.ManagedThreadId}");
        Console.WriteLine("Note: May not return to original thread");
        Console.WriteLine();
    }
    
    // 15. ValueTask
    public static async Task ValueTaskDemo()
    {
        Console.WriteLine("=== ValueTask Demo ===");
        
        // Use ValueTask for potentially synchronous operations
        ValueTask<int> valueTask = GetValueTaskAsync();
        
        if (valueTask.IsCompletedSuccessfully)
        {
            Console.WriteLine($"ValueTask completed synchronously: {valueTask.Result}");
        }
        else
        {
            int result = await valueTask;
            Console.WriteLine($"ValueTask completed asynchronously: {result}");
        }
        
        Console.WriteLine();
    }
    
    // Helper method for ValueTask demo
    private static ValueTask<int> GetValueTaskAsync()
    {
        // Simulate sometimes synchronous, sometimes asynchronous
        if (DateTime.Now.Second % 2 == 0)
        {
            return new ValueTask<int>(42); // Synchronous
        }
        else
        {
            return new ValueTask<int>(Task.Run(() => 42)); // Asynchronous
        }
    }
}

// Advanced TPL Examples
public class AdvancedTPLExamples
{
    // 1. Producer-Consumer pattern with TPL
    public static async Task ProducerConsumerPattern()
    {
        Console.WriteLine("=== Producer-Consumer Pattern ===");
        
        var buffer = new System.Collections.Concurrent.BlockingCollection<int>(capacity: 5);
        
        // Producer task
        var producer = Task.Run(async () => {
            for (int i = 1; i <= 10; i++)
            {
                Console.WriteLine($"Producing: {i}");
                buffer.Add(i);
                await Task.Delay(200); // Simulate production time
            }
            buffer.CompleteAdding();
        });
        
        // Consumer task
        var consumer = Task.Run(async () => {
            foreach (int item in buffer.GetConsumingEnumerable())
            {
                Console.WriteLine($"Consuming: {item}");
                await Task.Delay(300); // Simulate consumption time
            }
        });
        
        await Task.WhenAll(producer, consumer);
        Console.WriteLine("Producer-Consumer completed\n");
    }
    
    // 2. Dataflow pipeline
    public static async Task DataflowPipeline()
    {
        Console.WriteLine("=== Dataflow Pipeline ===");
        
        var bufferBlock = new System.Threading.Tasks.Dataflow.BufferBlock<int>();
        var transformBlock = new System.Threading.Tasks.Dataflow.TransformBlock<int, string>(x => {
            Console.WriteLine($"Transforming: {x} -> {x * 2}");
            Thread.Sleep(100);
            return $"Processed: {x * 2}";
        });
        var actionBlock = new System.Threading.Tasks.Dataflow.ActionBlock<string>(x => {
            Console.WriteLine($"Final result: {x}");
        });
        
        // Link blocks together
        bufferBlock.LinkTo(transformBlock);
        transformBlock.LinkTo(actionBlock);
        
        // Post data to pipeline
        for (int i = 1; i <= 5; i++)
        {
            await bufferBlock.SendAsync(i);
            Console.WriteLine($"Posted: {i}");
        }
        
        bufferBlock.Complete();
        await actionBlock.Completion;
        
        Console.WriteLine("Dataflow pipeline completed\n");
    }
    
    // 3. Task completion source
    public static async Task TaskCompletionSourceDemo()
    {
        Console.WriteLine("=== TaskCompletionSource Demo ===");
        
        var tcs = new TaskCompletionSource<string>();
        
        // Simulate external event
        _ = Task.Run(async () => {
            await Task.Delay(2000);
            tcs.SetResult("Event completed!");
        });
        
        Console.WriteLine("Waiting for external event...");
        string result = await tcs.Task;
        Console.WriteLine($"Event result: {result}\n");
    }
    
    // 4. Timeout with TaskCompletionSource
    public static async Task TimeoutWithTaskCompletionSource()
    {
        Console.WriteLine("=== Timeout with TaskCompletionSource ===");
        
        var tcs = new TaskCompletionSource<bool>();
        
        // Long-running operation
        var operation = Task.Run(async () => {
            await Task.Delay(3000);
            tcs.SetResult(true);
        });
        
        // Timeout
        var timeout = Task.Delay(2000);
        
        var completedTask = await Task.WhenAny(tcs.Task, timeout);
        
        if (completedTask == timeout)
        {
            Console.WriteLine("Operation timed out!");
        }
        else
        {
            Console.WriteLine("Operation completed successfully!");
        }
        
        Console.WriteLine();
    }
}

// Main demonstration
public class TPLDemo
{
    public static async Task Main(string[] args)
    {
        Console.WriteLine("=== Task Parallel Library Demonstration ===");
        Console.WriteLine("TPL provides high-level abstractions for parallel and asynchronous programming.\n");
        
        // Basic TPL operations
        await TaskParallelLibrary.BasicTaskExample();
        await TaskParallelLibrary.TaskWithReturnValue();
        await TaskParallelLibrary.TaskContinuation();
        await TaskParallelLibrary.MultipleTaskContinuations();
        await TaskParallelLibrary.TaskWhenAllExample();
        await TaskParallelLibrary.TaskWhenAnyExample();
        await TaskParallelLibrary.TaskExceptionHandling();
        await TaskParallelLibrary.TaskCancellation();
        
        // Parallel loops
        TaskParallelLibrary.ParallelForExample();
        TaskParallelLibrary.ParallelForEachExample();
        TaskParallelLibrary.ParallelInvokeExample();
        TaskParallelLibrary.ParallelWithOptions();
        await TaskParallelLibrary.TaskSchedulerDemo();
        await TaskParallelLibrary.ConfigureAwaitDemo();
        await TaskParallelLibrary.ValueTaskDemo();
        
        // Advanced TPL
        await AdvancedTPLExamples.ProducerConsumerPattern();
        await AdvancedTPLExamples.DataflowPipeline();
        await AdvancedTPLExamples.TaskCompletionSourceDemo();
        await AdvancedTPLExamples.TimeoutWithTaskCompletionSource();
        
        Console.WriteLine("=== Key TPL Concepts ===");
        Console.WriteLine("1. Task represents an asynchronous operation");
        Console.WriteLine("2. Task.Run() queues work to thread pool");
        Console.WriteLine("3. await suspends execution until task completes");
        Console.WriteLine("4. Task.WhenAll() waits for multiple tasks");
        Console.WriteLine("5. Task.WhenAny() waits for first completed task");
        Console.WriteLine("6. Continuations execute after task completion");
        Console.WriteLine("7. CancellationToken allows cooperative cancellation");
        Console.WriteLine("8. Parallel loops use multiple threads automatically");
        Console.WriteLine("9. ConfigureAwait(false) avoids context switching");
        Console.WriteLine("10. ValueTask optimizes for synchronous completion");
        Console.WriteLine("11. TaskCompletionSource creates manual tasks");
        Console.WriteLine("12. Dataflow blocks create processing pipelines");
    }
}
