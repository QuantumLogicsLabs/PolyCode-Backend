using Microsoft.Extensions.Logging;
using System;
using System.Threading.Tasks;

public class LoggingPatterns
{
    private readonly ILogger<LoggingPatterns> _logger;
    
    public LoggingPatterns(ILogger<LoggingPatterns> logger)
    {
        _logger = logger;
    }
    
    // Basic logging with error handling
    public void BasicLoggingExample()
    {
        _logger.LogInformation("Starting basic logging example");
        
        try
        {
            PerformRiskyOperation();
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error occurred while performing risky operation");
            // Re-throw or handle the exception
            throw;
        }
        
        _logger.LogInformation("Basic logging example completed");
    }
    
    // Structured logging with additional context
    public void StructuredLoggingExample(string userId, string operation)
    {
        _logger.LogInformation("Starting operation {Operation} for user {UserId}", operation, userId);
        
        try
        {
            var result = ProcessUserOperation(userId, operation);
            _logger.LogInformation("Operation {Operation} completed successfully for user {UserId}. Result: {Result}", 
                operation, userId, result);
        }
        catch (ArgumentException ex)
        {
            _logger.LogWarning(ex, "Invalid arguments for operation {Operation} for user {UserId}", operation, userId);
            throw;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Unexpected error in operation {Operation} for user {UserId}", operation, userId);
            throw;
        }
    }
    
    // Performance logging
    public async Task<T> PerformanceLoggingAsync<T>(string operationName, Func<Task<T>> operation)
    {
        var startTime = DateTime.UtcNow;
        _logger.LogInformation("Starting operation {OperationName}", operationName);
        
        try
        {
            var result = await operation();
            var duration = DateTime.UtcNow - startTime;
            
            _logger.LogInformation("Operation {OperationName} completed successfully in {Duration}ms", 
                operationName, duration.TotalMilliseconds);
            
            return result;
        }
        catch (Exception ex)
        {
            var duration = DateTime.UtcNow - startTime;
            _logger.LogError(ex, "Operation {OperationName} failed after {Duration}ms", 
                operationName, duration.TotalMilliseconds);
            throw;
        }
    }
    
    // Exception logging with custom properties
    public void ExceptionLoggingWithCustomProperties()
    {
        try
        {
            ValidateUserInput(null, "test@example.com");
        }
        catch (ValidationException ex)
        {
            // Log with custom properties
            _logger.LogError(ex, "Validation failed for user {@ValidationErrors}", ex.ValidationErrors);
        }
    }
    
    // Scoped logging for request tracking
    public void ScopedLoggingExample()
    {
        using var loggerScope = _logger.BeginScope("RequestId: {RequestId}", Guid.NewGuid());
        
        _logger.LogInformation("Starting request processing");
        
        try
        {
            ProcessRequest();
            _logger.LogInformation("Request processed successfully");
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Request processing failed");
            throw;
        }
    }
    
    // Logging levels demonstration
    public void LoggingLevelsExample()
    {
        _logger.LogTrace("This is a trace message - very detailed");
        _logger.LogDebug("This is a debug message - for development");
        _logger.LogInformation("This is an information message - general info");
        _logger.LogWarning("This is a warning message - something unusual happened");
        _logger.LogError("This is an error message - something went wrong");
        _logger.LogCritical("This is a critical message - system failure");
    }
    
    // Async logging with error handling
    public async Task AsyncLoggingExample()
    {
        _logger.LogInformation("Starting async operation");
        
        try
        {
            await ProcessAsyncOperation();
            _logger.LogInformation("Async operation completed successfully");
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Async operation failed");
            throw;
        }
    }
    
    // Helper methods
    private void PerformRiskyOperation()
    {
        // Simulate an operation that might fail
        if (DateTime.Now.Second % 3 == 0)
        {
            throw new InvalidOperationException("Simulated operation failure");
        }
        
        Console.WriteLine("Risky operation completed successfully");
    }
    
    private string ProcessUserOperation(string userId, string operation)
    {
        if (string.IsNullOrEmpty(userId))
        {
            throw new ArgumentException("User ID cannot be null or empty", nameof(userId));
        }
        
        return $"Processed {operation} for user {userId}";
    }
    
    private void ValidateUserInput(string name, string email)
    {
        var errors = new System.Collections.Generic.Dictionary<string, string>();
        
        if (string.IsNullOrWhiteSpace(name))
        {
            errors["Name"] = "Name is required";
        }
        
        if (string.IsNullOrWhiteSpace(email))
        {
            errors["Email"] = "Email is required";
        }
        
        if (errors.Count > 0)
        {
            throw new ValidationException(errors);
        }
    }
    
    private void ProcessRequest()
    {
        // Simulate request processing
        Console.WriteLine("Processing request...");
        
        if (DateTime.Now.Second % 5 == 0)
        {
            throw new InvalidOperationException("Request processing failed");
        }
    }
    
    private async Task ProcessAsyncOperation()
    {
        await Task.Delay(1000);
        
        if (DateTime.Now.Second % 4 == 0)
        {
            throw new InvalidOperationException("Async operation failed");
        }
        
        Console.WriteLine("Async operation completed");
    }
}

// Custom exception for validation
public class ValidationException : Exception
{
    public System.Collections.Generic.Dictionary<string, string> ValidationErrors { get; }
    
    public ValidationException(System.Collections.Generic.Dictionary<string, string> validationErrors)
        : base($"Validation failed with {validationErrors.Count} errors")
    {
        ValidationErrors = validationErrors;
    }
}

// Logger wrapper for consistent logging patterns
public class AppLogger<T>
{
    private readonly ILogger<T> _logger;
    
    public AppLogger(ILogger<T> logger)
    {
        _logger = logger;
    }
    
    public void LogOperationStart(string operation, object parameters = null)
    {
        if (parameters != null)
        {
            _logger.LogInformation("Starting {Operation} with parameters {@Parameters}", operation, parameters);
        }
        else
        {
            _logger.LogInformation("Starting {Operation}", operation);
        }
    }
    
    public void LogOperationSuccess(string operation, object result = null)
    {
        if (result != null)
        {
            _logger.LogInformation("{Operation} completed successfully. Result: {@Result}", operation, result);
        }
        else
        {
            _logger.LogInformation("{Operation} completed successfully", operation);
        }
    }
    
    public void LogOperationFailure(string operation, Exception exception, object parameters = null)
    {
        if (parameters != null)
        {
            _logger.LogError(exception, "{Operation} failed with parameters {@Parameters}", operation, parameters);
        }
        else
        {
            _logger.LogError(exception, "{Operation} failed", operation);
        }
    }
    
    public async Task<T> LogOperationAsync<T>(string operation, Func<Task<T>> func, object parameters = null)
    {
        LogOperationStart(operation, parameters);
        
        try
        {
            var result = await func();
            LogOperationSuccess(operation, result);
            return result;
        }
        catch (Exception ex)
        {
            LogOperationFailure(operation, ex, parameters);
            throw;
        }
    }
}

// Program demonstrating logging patterns
public class LoggingPatternsProgram
{
    public static async Task Main(string[] args)
    {
        // Set up logging
        using var loggerFactory = LoggerFactory.Create(builder =>
        {
            builder
                .AddConsole()
                .SetMinimumLevel(LogLevel.Information);
        });
        
        var logger = loggerFactory.CreateLogger<LoggingPatterns>();
        var loggingPatterns = new LoggingPatterns(logger);
        
        Console.WriteLine("=== Logging Patterns Demo ===");
        
        // Demonstrate different logging patterns
        loggingPatterns.BasicLoggingExample();
        
        loggingPatterns.StructuredLoggingExample("user123", "update_profile");
        
        await loggingPatterns.PerformanceLoggingAsync("data_fetch", async () =>
        {
            await Task.Delay(1000);
            return "Data fetched successfully";
        });
        
        loggingPatterns.ExceptionLoggingWithCustomProperties();
        
        loggingPatterns.ScopedLoggingExample();
        
        loggingPatterns.LoggingLevelsExample();
        
        await loggingPatterns.AsyncLoggingExample();
        
        // Demonstrate app logger wrapper
        var appLogger = new AppLogger<LoggingPatternsProgram>(loggerFactory.CreateLogger<LoggingPatternsProgram>());
        
        await appLogger.LogOperationAsync("user_creation", async () =>
        {
            await Task.Delay(500);
            return "User created successfully";
        }, new { Username = "john_doe", Email = "john@example.com" });
    }
}
