using System;
using System.Threading;
using System.Threading.Tasks;

public class ResiliencePatterns
{
    public static async Task Main(string[] args)
    {
        Console.WriteLine("=== Resilience Patterns Demo ===");
        
        // Retry pattern
        await RetryPatternExample();
        
        // Circuit breaker pattern
        await CircuitBreakerExample();
        
        // Timeout pattern
        await TimeoutPatternExample();
        
        // Fallback pattern
        await FallbackPatternExample();
        
        // Bulkhead isolation pattern
        await BulkheadPatternExample();
        
        // Combined resilience patterns
        await CombinedResilienceExample();
    }
    
    public static async Task RetryPatternExample()
    {
        Console.WriteLine("\n--- Retry Pattern ---");
        
        try
        {
            var result = await RetryAsync(async () =>
            {
                return await FlakyOperation();
            }, maxRetries: 3, delay: TimeSpan.FromSeconds(1));
            
            Console.WriteLine($"Retry succeeded: {result}");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Retry failed after all attempts: {ex.Message}");
        }
    }
    
    public static async Task CircuitBreakerExample()
    {
        Console.WriteLine("\n--- Circuit Breaker Pattern ---");
        
        var circuitBreaker = new CircuitBreaker(threshold: 3, timeout: TimeSpan.FromSeconds(5));
        
        for (int i = 1; i <= 6; i++)
        {
            try
            {
                var result = await circuitBreaker.ExecuteAsync(async () =>
                {
                    return await UnreliableOperation();
                });
                
                Console.WriteLine($"Attempt {i}: Success - {result}");
            }
            catch (CircuitBreakerOpenException)
            {
                Console.WriteLine($"Attempt {i}: Circuit breaker is open");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Attempt {i}: Failed - {ex.Message}");
            }
            
            await Task.Delay(500);
        }
    }
    
    public static async Task TimeoutPatternExample()
    {
        Console.WriteLine("\n--- Timeout Pattern ---");
        
        try
        {
            var result = await WithTimeout(async () =>
            {
                return await LongRunningOperation();
            }, TimeSpan.FromSeconds(2));
            
            Console.WriteLine($"Operation completed: {result}");
        }
        catch (TimeoutException ex)
        {
            Console.WriteLine($"Operation timed out: {ex.Message}");
        }
    }
    
    public static async Task FallbackPatternExample()
    {
        Console.WriteLine("\n--- Fallback Pattern ---");
        
        try
        {
            var result = await WithFallback(async () =>
            {
                return await PrimaryOperation();
            }, async () =>
            {
                return await FallbackOperation();
            });
            
            Console.WriteLine($"Result: {result}");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"All operations failed: {ex.Message}");
        }
    }
    
    public static async Task BulkheadPatternExample()
    {
        Console.WriteLine("\n--- Bulkhead Pattern ---");
        
        var bulkhead = new Bulkhead(maxConcurrentOperations: 2);
        
        var tasks = new Task[5];
        for (int i = 0; i < 5; i++)
        {
            int operationId = i;
            tasks[i] = Task.Run(async () =>
            {
                try
                {
                    var result = await bulkhead.ExecuteAsync(async () =>
                    {
                        return await SimulateWork(operationId);
                    });
                    
                    Console.WriteLine($"Operation {operationId} completed: {result}");
                }
                catch (BulkheadRejectedException)
                {
                    Console.WriteLine($"Operation {operationId} rejected - bulkhead full");
                }
            });
        }
        
        await Task.WhenAll(tasks);
    }
    
    public static async Task CombinedResilienceExample()
    {
        Console.WriteLine("\n--- Combined Resilience Patterns ---");
        
        var circuitBreaker = new CircuitBreaker(threshold: 2, timeout: TimeSpan.FromSeconds(3));
        
        try
        {
            var result = await WithTimeout(async () =>
            {
                return await RetryAsync(async () =>
                {
                    return await circuitBreaker.ExecuteAsync(async () =>
                    {
                        return await FlakyOperation();
                    });
                }, maxRetries: 2, delay: TimeSpan.FromMilliseconds(500));
            }, TimeSpan.FromSeconds(5));
            
            Console.WriteLine($"Combined patterns succeeded: {result}");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Combined patterns failed: {ex.Message}");
        }
    }
    
    // Helper operations
    private static int _attemptCount = 0;
    public static async Task<string> FlakyOperation()
    {
        _attemptCount++;
        await Task.Delay(100);
        
        if (_attemptCount % 3 != 0) // Fail 2 out of 3 times
        {
            throw new InvalidOperationException($"Flaky operation failed (attempt {_attemptCount})");
        }
        
        return $"Success on attempt {_attemptCount}";
    }
    
    private static int _unreliableCount = 0;
    public static async Task<string> UnreliableOperation()
    {
        _unreliableCount++;
        await Task.Delay(200);
        
        if (_unreliableCount <= 4) // First 4 attempts fail
        {
            throw new InvalidOperationException($"Unreliable operation failed (attempt {_unreliableCount})");
        }
        
        return $"Success on attempt {_unreliableCount}";
    }
    
    public static async Task<string> LongRunningOperation()
    {
        await Task.Delay(3000);
        return "Long operation completed";
    }
    
    public static async Task<string> PrimaryOperation()
    {
        await Task.Delay(100);
        throw new InvalidOperationException("Primary operation failed");
    }
    
    public static async Task<string> FallbackOperation()
    {
        await Task.Delay(200);
        return "Fallback operation succeeded";
    }
    
    public static async Task<string> SimulateWork(int operationId)
    {
        Console.WriteLine($"Operation {operationId} started");
        await Task.Delay(2000); // Simulate work
        return $"Operation {operationId} result";
    }
    
    // Resilience pattern implementations
    
    public static async Task<T> RetryAsync<T>(Func<Task<T>> operation, int maxRetries, TimeSpan delay)
    {
        Exception lastException = null;
        
        for (int attempt = 1; attempt <= maxRetries; attempt++)
        {
            try
            {
                return await operation();
            }
            catch (Exception ex)
            {
                lastException = ex;
                
                if (attempt == maxRetries)
                {
                    break;
                }
                
                Console.WriteLine($"Retry attempt {attempt} failed. Retrying in {delay.TotalMilliseconds}ms...");
                await Task.Delay(delay);
            }
        }
        
        throw new Exception($"Operation failed after {maxRetries} retries", lastException);
    }
    
    public static async Task<T> WithTimeout<T>(Func<Task<T>> operation, TimeSpan timeout)
    {
        using var cts = new CancellationTokenSource(timeout);
        
        try
        {
            return await operation().ConfigureAwait(false);
        }
        catch (OperationCanceledException) when (cts.Token.IsCancellationRequested)
        {
            throw new TimeoutException($"Operation timed out after {timeout.TotalSeconds} seconds");
        }
    }
    
    public static async Task<T> WithFallback<T>(Func<Task<T>> primaryOperation, Func<Task<T>> fallbackOperation)
    {
        try
        {
            return await primaryOperation();
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Primary operation failed: {ex.Message}. Using fallback...");
            return await fallbackOperation();
        }
    }
}

// Circuit Breaker implementation
public class CircuitBreaker
{
    private readonly int _threshold;
    private readonly TimeSpan _timeout;
    private int _failureCount;
    private DateTime _lastFailureTime;
    private CircuitBreakerState _state = CircuitBreakerState.Closed;
    
    public CircuitBreaker(int threshold, TimeSpan timeout)
    {
        _threshold = threshold;
        _timeout = timeout;
    }
    
    public async Task<T> ExecuteAsync<T>(Func<Task<T>> operation)
    {
        if (_state == CircuitBreakerState.Open)
        {
            if (DateTime.Now - _lastFailureTime > _timeout)
            {
                _state = CircuitBreakerState.HalfOpen;
            }
            else
            {
                throw new CircuitBreakerOpenException("Circuit breaker is open");
            }
        }
        
        try
        {
            var result = await operation();
            
            if (_state == CircuitBreakerState.HalfOpen)
            {
                _state = CircuitBreakerState.Closed;
                _failureCount = 0;
            }
            
            return result;
        }
        catch (Exception ex)
        {
            _failureCount++;
            _lastFailureTime = DateTime.Now;
            
            if (_failureCount >= _threshold)
            {
                _state = CircuitBreakerState.Open;
            }
            
            throw;
        }
    }
    
    private enum CircuitBreakerState
    {
        Closed,
        Open,
        HalfOpen
    }
}

public class CircuitBreakerOpenException : Exception
{
    public CircuitBreakerOpenException(string message) : base(message) { }
}

// Bulkhead implementation
public class Bulkhead
{
    private readonly SemaphoreSlim _semaphore;
    
    public Bulkhead(int maxConcurrentOperations)
    {
        _semaphore = new SemaphoreSlim(maxConcurrentOperations, maxConcurrentOperations);
    }
    
    public async Task<T> ExecuteAsync<T>(Func<Task<T>> operation)
    {
        if (!await _semaphore.WaitAsync(TimeSpan.Zero))
        {
            throw new BulkheadRejectedException("Bulkhead is full - operation rejected");
        }
        
        try
        {
            return await operation();
        }
        finally
        {
            _semaphore.Release();
        }
    }
}

public class BulkheadRejectedException : Exception
{
    public BulkheadRejectedException(string message) : base(message) { }
}
