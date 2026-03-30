using System;
using System.Net.Http;
using System.Threading.Tasks;

public class AsyncErrorHandling
{
    private static readonly HttpClient _httpClient = new HttpClient();
    
    public static async Task Main(string[] args)
    {
        Console.WriteLine("=== Async Error Handling ===");
        
        // Basic async exception handling
        await BasicAsyncExceptionHandling();
        
        // Multiple async operations with error handling
        await MultipleAsyncOperations();
        
        // Async streams with error handling
        await AsyncStreamsWithErrorHandling();
        
        // Cancellation with error handling
        await CancellationWithErrorHandling();
        
        // Task.WhenAll error handling
        await TaskWhenAllErrorHandling();
    }
    
    public static async Task BasicAsyncExceptionHandling()
    {
        Console.WriteLine("\n--- Basic Async Exception Handling ---");
        
        try
        {
            await SimulateAsyncOperation(true); // This will throw
        }
        catch (InvalidOperationException ex)
        {
            Console.WriteLine($"Caught async exception: {ex.Message}");
        }
        
        try
        {
            var result = await GetValueAsync();
            Console.WriteLine($"Got value: {result}");
        }
        catch (ArgumentException ex)
        {
            Console.WriteLine($"Caught argument exception: {ex.Message}");
        }
    }
    
    public static async Task MultipleAsyncOperations()
    {
        Console.WriteLine("\n--- Multiple Async Operations ---");
        
        try
        {
            await FirstOperation();
            await SecondOperation();
            await ThirdOperation();
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Error in async chain: {ex.Message}");
        }
    }
    
    public static async Task AsyncStreamsWithErrorHandling()
    {
        Console.WriteLine("\n--- Async Streams with Error Handling ---");
        
        try
        {
            await foreach (var item in GenerateDataStream())
            {
                Console.WriteLine($"Received: {item}");
            }
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Error in async stream: {ex.Message}");
        }
    }
    
    public static async Task CancellationWithErrorHandling()
    {
        Console.WriteLine("\n--- Cancellation with Error Handling ---");
        
        var cts = new System.Threading.CancellationTokenSource();
        cts.CancelAfter(2000); // Cancel after 2 seconds
        
        try
        {
            await LongRunningOperation(cts.Token);
        }
        catch (OperationCanceledException)
        {
            Console.WriteLine("Operation was cancelled as expected");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Unexpected error: {ex.Message}");
        }
    }
    
    public static async Task TaskWhenAllErrorHandling()
    {
        Console.WriteLine("\n--- Task.WhenAll Error Handling ---");
        
        var tasks = new[]
        {
            SimulateAsyncOperation(false), // Success
            SimulateAsyncOperation(true),  // Failure
            SimulateAsyncOperation(false)  // Success
        };
        
        try
        {
            await Task.WhenAll(tasks);
            Console.WriteLine("All tasks completed successfully");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"One or more tasks failed: {ex.Message}");
            
            // Check individual task statuses
            for (int i = 0; i < tasks.Length; i++)
            {
                if (tasks[i].IsFaulted)
                {
                    Console.WriteLine($"Task {i} failed: {tasks[i].Exception?.InnerException?.Message}");
                }
                else if (tasks[i].IsCanceled)
                {
                    Console.WriteLine($"Task {i} was cancelled");
                }
                else
                {
                    Console.WriteLine($"Task {i} completed successfully");
                }
            }
        }
    }
    
    // Helper methods for demonstration
    public static async Task SimulateAsyncOperation(bool shouldFail)
    {
        await Task.Delay(500);
        
        if (shouldFail)
        {
            throw new InvalidOperationException("Simulated async operation failure");
        }
        
        Console.WriteLine("Async operation completed successfully");
    }
    
    public static async Task<string> GetValueAsync()
    {
        await Task.Delay(300);
        
        if (DateTime.Now.Second % 3 == 0) // Random failure
        {
            throw new ArgumentException("Invalid value generated");
        }
        
        return "Valid value";
    }
    
    public static async Task FirstOperation()
    {
        await Task.Delay(100);
        Console.WriteLine("First operation completed");
    }
    
    public static async Task SecondOperation()
    {
        await Task.Delay(100);
        throw new InvalidOperationException("Second operation failed");
    }
    
    public static async Task ThirdOperation()
    {
        await Task.Delay(100);
        Console.WriteLine("Third operation completed");
    }
    
    public static async IAsyncEnumerable<int> GenerateDataStream()
    {
        for (int i = 1; i <= 5; i++)
        {
            await Task.Delay(500);
            
            if (i == 3)
            {
                throw new InvalidOperationException("Error generating data stream");
            }
            
            yield return i;
        }
    }
    
    public static async Task LongRunningOperation(System.Threading.CancellationToken cancellationToken)
    {
        for (int i = 1; i <= 10; i++)
        {
            cancellationToken.ThrowIfCancellationRequested();
            
            Console.WriteLine($"Working... step {i}/10");
            await Task.Delay(500, cancellationToken);
        }
        
        Console.WriteLine("Long running operation completed");
    }
    
    // Advanced async error handling patterns
    public static async Task<Result<T>> SafeExecuteAsync<T>(Func<Task<T>> operation)
    {
        try
        {
            var result = await operation();
            return Result<T>.Success(result);
        }
        catch (Exception ex)
        {
            return Result<T>.Failure(ex.Message);
        }
    }
    
    public static async Task DemonstrateSafeExecute()
    {
        Console.WriteLine("\n--- Safe Execute Pattern ---");
        
        var result1 = await SafeExecuteAsync(() => GetValueAsync());
        if (result1.IsSuccess)
        {
            Console.WriteLine($"Success: {result1.Value}");
        }
        else
        {
            Console.WriteLine($"Failure: {result1.ErrorMessage}");
        }
        
        var result2 = await SafeExecuteAsync(() => SimulateAsyncOperation(true).ContinueWith(_ => "Done"));
        if (result2.IsSuccess)
        {
            Console.WriteLine($"Success: {result2.Value}");
        }
        else
        {
            Console.WriteLine($"Failure: {result2.ErrorMessage}");
        }
    }
}

// Result pattern for better error handling
public class Result<T>
{
    public bool IsSuccess { get; private set; }
    public T Value { get; private set; }
    public string ErrorMessage { get; private set; }
    
    private Result(bool isSuccess, T value, string errorMessage)
    {
        IsSuccess = isSuccess;
        Value = value;
        ErrorMessage = errorMessage;
    }
    
    public static Result<T> Success(T value)
    {
        return new Result<T>(true, value, null);
    }
    
    public static Result<T> Failure(string errorMessage)
    {
        return new Result<T>(false, default(T), errorMessage);
    }
}

// Extension methods for better async error handling
public static class AsyncErrorHandlingExtensions
{
    public static async Task<T> WithTimeout<T>(this Task<T> task, TimeSpan timeout)
    {
        using var cts = new System.Threading.CancellationTokenSource(timeout);
        
        var completedTask = await Task.WhenAny(task, Task.Delay(timeout, cts.Token));
        
        if (completedTask == task)
        {
            return await task;
        }
        else
        {
            throw new TimeoutException($"Operation timed out after {timeout.TotalSeconds} seconds");
        }
    }
    
    public static async Task<T> WithRetry<T>(this Func<Task<T>> operation, int maxAttempts = 3)
    {
        Exception lastException = null;
        
        for (int attempt = 1; attempt <= maxAttempts; attempt++)
        {
            try
            {
                return await operation();
            }
            catch (Exception ex)
            {
                lastException = ex;
                
                if (attempt == maxAttempts)
                {
                    break;
                }
                
                Console.WriteLine($"Attempt {attempt} failed. Retrying... {ex.Message}");
                await Task.Delay(1000 * attempt); // Exponential backoff
            }
        }
        
        throw lastException;
    }
}
