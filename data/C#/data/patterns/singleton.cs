using System;
using System.Threading;

// Singleton Pattern Examples

// 1. Basic Singleton (not thread-safe)
public class BasicSingleton
{
    private static BasicSingleton _instance;
    private static readonly object _lock = new object();
    
    // Private constructor to prevent instantiation
    private BasicSingleton()
    {
        Console.WriteLine("BasicSingleton instance created");
    }
    
    public static BasicSingleton Instance
    {
        get
        {
            if (_instance == null)
            {
                lock (_lock)
                {
                    if (_instance == null)
                    {
                        _instance = new BasicSingleton();
                    }
                }
            }
            return _instance;
        }
    }
    
    public void DoSomething()
    {
        Console.WriteLine("BasicSingleton is doing something");
    }
}

// 2. Thread-safe Singleton with Lazy<T>
public class LazySingleton
{
    private static readonly Lazy<LazySingleton> _lazyInstance = 
        new Lazy<LazySingleton>(() => new LazySingleton());
    
    public static LazySingleton Instance => _lazyInstance.Value;
    
    private LazySingleton()
    {
        Console.WriteLine("LazySingleton instance created");
    }
    
    public void DoSomething()
    {
        Console.WriteLine("LazySingleton is doing something");
    }
}

// 3. Singleton with properties
public class ConfigurationSingleton
{
    private static readonly Lazy<ConfigurationSingleton> _instance = 
        new Lazy<ConfigurationSingleton>(() => new ConfigurationSingleton());
    
    public static ConfigurationSingleton Instance => _instance.Value;
    
    public string DatabaseConnectionString { get; private set; }
    public string ApiKey { get; private set; }
    public int MaxRetries { get; private set; }
    
    private ConfigurationSingleton()
    {
        // Load configuration from file, environment variables, etc.
        DatabaseConnectionString = "Server=localhost;Database=MyApp;Trusted_Connection=true;";
        ApiKey = "your-api-key-here";
        MaxRetries = 3;
        
        Console.WriteLine("ConfigurationSingleton initialized");
    }
    
    public void UpdateConnectionString(string connectionString)
    {
        DatabaseConnectionString = connectionString;
        Console.WriteLine($"Database connection string updated: {connectionString}");
    }
}

// 4. Generic Singleton base class
public abstract class Singleton<T> where T : class, new()
{
    private static readonly Lazy<T> _instance = new Lazy<T>(() => new T());
    
    public static T Instance => _instance.Value;
}

// Usage of generic singleton
public class LoggerSingleton : Singleton<LoggerSingleton>
{
    private LoggerSingleton()
    {
        Console.WriteLine("LoggerSingleton instance created");
    }
    
    public void Log(string message)
    {
        Console.WriteLine($"[LOG] {DateTime.Now:yyyy-MM-dd HH:mm:ss} - {message}");
    }
}

// 5. Singleton with IDisposable
public class DatabaseConnectionSingleton : IDisposable
{
    private static readonly Lazy<DatabaseConnectionSingleton> _instance = 
        new Lazy<DatabaseConnectionSingleton>(() => new DatabaseConnectionSingleton());
    
    public static DatabaseConnectionSingleton Instance => _instance.Value;
    
    private bool _disposed = false;
    private string _connectionString;
    
    private DatabaseConnectionSingleton()
    {
        _connectionString = "Server=localhost;Database=MyApp;";
        Console.WriteLine("DatabaseConnectionSingleton connected");
    }
    
    public void ExecuteQuery(string query)
    {
        if (_disposed)
            throw new ObjectDisposedException(nameof(DatabaseConnectionSingleton));
        
        Console.WriteLine($"Executing query: {query}");
    }
    
    public void Dispose()
    {
        if (!_disposed)
        {
            Console.WriteLine("DatabaseConnectionSingleton disconnected");
            _disposed = true;
        }
    }
}

// 6. Singleton for caching
public class CacheSingleton
{
    private static readonly Lazy<CacheSingleton> _instance = 
        new Lazy<CacheSingleton>(() => new CacheSingleton());
    
    public static CacheSingleton Instance => _instance.Value;
    
    private readonly System.Collections.Generic.Dictionary<string, object> _cache = new();
    private readonly System.Collections.Generic.Dictionary<string, DateTime> _cacheTimes = new();
    
    private CacheSingleton()
    {
        // Start cleanup timer
        var timer = new Timer(CleanupExpiredItems, null, TimeSpan.FromMinutes(5), TimeSpan.FromMinutes(5));
        Console.WriteLine("CacheSingleton initialized with cleanup timer");
    }
    
    public void Set(string key, object value, TimeSpan? expiry = null)
    {
        _cache[key] = value;
        _cacheTimes[key] = DateTime.Now;
        
        if (expiry.HasValue)
        {
            // In a real implementation, you'd store expiry times
            Console.WriteLine($"Cached item '{key}' with expiry of {expiry.Value}");
        }
        else
        {
            Console.WriteLine($"Cached item '{key}' (no expiry)");
        }
    }
    
    public T Get<T>(string key)
    {
        if (_cache.TryGetValue(key, out var value))
        {
            Console.WriteLine($"Retrieved cached item '{key}'");
            return (T)value;
        }
        
        Console.WriteLine($"Cache miss for item '{key}'");
        return default(T);
    }
    
    public bool Remove(string key)
    {
        var removed = _cache.Remove(key);
        _cacheTimes.Remove(key);
        
        if (removed)
        {
            Console.WriteLine($"Removed cached item '{key}'");
        }
        
        return removed;
    }
    
    private void CleanupExpiredItems(object state)
    {
        Console.WriteLine("Running cache cleanup...");
        // In a real implementation, you'd check expiry times and remove expired items
    }
}

// Demonstration program
public class SingletonPatternDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Singleton Pattern Demonstration ===\n");
        
        // Demonstrate Basic Singleton
        Console.WriteLine("--- Basic Singleton ---");
        var singleton1 = BasicSingleton.Instance;
        var singleton2 = BasicSingleton.Instance;
        
        Console.WriteLine($"Same instance? {ReferenceEquals(singleton1, singleton2)}");
        singleton1.DoSomething();
        
        // Demonstrate Lazy Singleton
        Console.WriteLine("\n--- Lazy Singleton ---");
        var lazySingleton1 = LazySingleton.Instance;
        var lazySingleton2 = LazySingleton.Instance;
        
        Console.WriteLine($"Same instance? {ReferenceEquals(lazySingleton1, lazySingleton2)}");
        lazySingleton2.DoSomething();
        
        // Demonstrate Configuration Singleton
        Console.WriteLine("\n--- Configuration Singleton ---");
        var config1 = ConfigurationSingleton.Instance;
        var config2 = ConfigurationSingleton.Instance;
        
        Console.WriteLine($"Same instance? {ReferenceEquals(config1, config2)}");
        Console.WriteLine($"Connection string: {config1.DatabaseConnectionString}");
        Console.WriteLine($"API Key: {config1.ApiKey}");
        Console.WriteLine($"Max Retries: {config1.MaxRetries}");
        
        config1.UpdateConnectionString("Server=newserver;Database=NewApp;");
        Console.WriteLine($"Updated connection string: {config2.DatabaseConnectionString}");
        
        // Demonstrate Generic Singleton
        Console.WriteLine("\n--- Generic Singleton ---");
        var logger1 = LoggerSingleton.Instance;
        var logger2 = LoggerSingleton.Instance;
        
        Console.WriteLine($"Same instance? {ReferenceEquals(logger1, logger2)}");
        logger1.Log("Application started");
        logger2.Log("User logged in");
        
        // Demonstrate IDisposable Singleton
        Console.WriteLine("\n--- IDisposable Singleton ---");
        using (var dbConnection = DatabaseConnectionSingleton.Instance)
        {
            dbConnection.ExecuteQuery("SELECT * FROM Users");
        }
        
        // Demonstrate Cache Singleton
        Console.WriteLine("\n--- Cache Singleton ---");
        var cache1 = CacheSingleton.Instance;
        var cache2 = CacheSingleton.Instance;
        
        Console.WriteLine($"Same instance? {ReferenceEquals(cache1, cache2)}");
        
        cache1.Set("user:123", new { Name = "John Doe", Age = 30 });
        var user = cache2.Get<dynamic>("user:123");
        
        if (user != null)
        {
            Console.WriteLine($"Cached user: {user.Name}, Age: {user.Age}");
        }
        
        // Thread safety demonstration
        Console.WriteLine("\n--- Thread Safety Demonstration ---");
        TestThreadSafety();
        
        Console.WriteLine("\n=== Singleton Pattern Demo Complete ===");
    }
    
    private static void TestThreadSafety()
    {
        var tasks = new Task[10];
        var instances = new BasicSingleton[10];
        
        for (int i = 0; i < 10; i++)
        {
            int index = i;
            tasks[i] = Task.Run(() =>
            {
                instances[index] = BasicSingleton.Instance;
                Console.WriteLine($"Task {index} got instance");
            });
        }
        
        Task.WaitAll(tasks);
        
        // Verify all instances are the same
        bool allSame = true;
        for (int i = 1; i < instances.Length; i++)
        {
            if (!ReferenceEquals(instances[0], instances[i]))
            {
                allSame = false;
                break;
            }
        }
        
        Console.WriteLine($"All threads got the same instance: {allSame}");
    }
}

// Singleton Pattern Usage Guidelines
/*
When to use Singleton:
1. When you need exactly one instance of a class
2. When the single instance should be accessible globally
3. When the instance manages shared resources (database connections, caches, etc.)
4. When you want to control access to shared resources
5. When you need lazy initialization

Common use cases:
- Configuration managers
- Logging services
- Database connection pools
- Cache managers
- Thread pools
- Service locators

Advantages:
- Controlled access to sole instance
- Reduced memory footprint
- Global access point
- Lazy initialization

Disadvantages:
- Violates Single Responsibility Principle
- Difficult to test (tight coupling)
- Can hide dependencies
- Global state can cause issues
- Not suitable for multi-threaded environments without proper synchronization

Implementation considerations:
- Thread safety is crucial
- Consider using Lazy<T> for thread-safe lazy initialization
- Implement IDisposable if the singleton holds unmanaged resources
- Be careful with serialization (can create multiple instances)
- Consider dependency injection as an alternative
*/
