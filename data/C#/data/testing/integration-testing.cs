using System;
using System.Collections.Generic;
using System.Threading.Tasks;

// Integration testing examples
public interface IDatabaseConnection
{
    Task<bool> ConnectAsync();
    Task DisconnectAsync();
    Task<bool> IsConnectedAsync();
    Task<int> ExecuteQueryAsync(string query);
}

public interface ICacheService
{
    Task SetAsync(string key, string value, TimeSpan? expiry = null);
    Task<string> GetAsync(string key);
    Task<bool> RemoveAsync(string key);
    Task<bool> ExistsAsync(string key);
}

public interface IExternalApiService
{
    Task<string> GetDataAsync(string endpoint);
    Task<bool> PostDataAsync(string endpoint, string data);
}

// Real implementations for integration testing
public class SqlDatabaseConnection : IDatabaseConnection
{
    private bool _isConnected;
    
    public async Task<bool> ConnectAsync()
    {
        // Simulate database connection
        await Task.Delay(100);
        _isConnected = true;
        return true;
    }
    
    public async Task DisconnectAsync()
    {
        await Task.Delay(50);
        _isConnected = false;
    }
    
    public async Task<bool> IsConnectedAsync()
    {
        await Task.Delay(10);
        return _isConnected;
    }
    
    public async Task<int> ExecuteQueryAsync(string query)
    {
        if (!_isConnected)
            throw new InvalidOperationException("Not connected to database");
        
        await Task.Delay(200); // Simulate query execution
        
        // Simulate different query results
        if (query.Contains("SELECT COUNT"))
            return 42;
        if (query.Contains("INSERT"))
            return 1;
        if (query.Contains("UPDATE"))
            return 1;
        if (query.Contains("DELETE"))
            return 1;
        
        return 0;
    }
}

public class RedisCacheService : ICacheService
{
    private readonly Dictionary<string, (string value, DateTime expiry)> _cache = new();
    
    public async Task SetAsync(string key, string value, TimeSpan? expiry = null)
    {
        await Task.Delay(10);
        
        var expiryTime = expiry.HasValue ? DateTime.Now.Add(expiry.Value) : DateTime.MaxValue;
        _cache[key] = (value, expiryTime);
    }
    
    public async Task<string> GetAsync(string key)
    {
        await Task.Delay(10);
        
        if (_cache.TryGetValue(key, out var item))
        {
            if (item.expiry > DateTime.Now)
                return item.value;
            else
                _cache.Remove(key);
        }
        
        return null;
    }
    
    public async Task<bool> RemoveAsync(string key)
    {
        await Task.Delay(10);
        return _cache.Remove(key);
    }
    
    public async Task<bool> ExistsAsync(string key)
    {
        await Task.Delay(10);
        
        if (_cache.TryGetValue(key, out var item))
        {
            if (item.expiry > DateTime.Now)
                return true;
            else
                _cache.Remove(key);
        }
        
        return false;
    }
}

public class ExternalApiService : IExternalApiService
{
    public async Task<string> GetDataAsync(string endpoint)
    {
        await Task.Delay(500); // Simulate network latency
        
        return endpoint switch
        {
            "/users" => "[{\"id\":1,\"name\":\"John\"},{\"id\":2,\"name\":\"Jane\"}]",
            "/products" => "[{\"id\":1,\"name\":\"Laptop\"},{\"id\":2,\"name\":\"Mouse\"}]",
            _ => throw new ArgumentException($"Unknown endpoint: {endpoint}")
        };
    }
    
    public async Task<bool> PostDataAsync(string endpoint, string data)
    {
        await Task.Delay(300);
        
        return endpoint switch
        {
            "/users" => true,
            "/products" => true,
            _ => false
        };
    }
}

// Service that integrates multiple components
public class UserServiceIntegration
{
    private readonly IDatabaseConnection _database;
    private readonly ICacheService _cache;
    private readonly IExternalApiService _externalApi;
    
    public UserServiceIntegration(IDatabaseConnection database, ICacheService cache, IExternalApiService externalApi)
    {
        _database = database ?? throw new ArgumentNullException(nameof(database));
        _cache = cache ?? throw new ArgumentNullException(nameof(cache));
        _externalApi = externalApi ?? throw new ArgumentNullException(nameof(externalApi));
    }
    
    public async Task<bool> InitializeAsync()
    {
        try
        {
            var dbConnected = await _database.ConnectAsync();
            if (!dbConnected)
                return false;
            
            // Warm up cache
            await _cache.SetAsync("system:initialized", "true", TimeSpan.FromHours(1));
            
            return true;
        }
        catch
        {
            return false;
        }
    }
    
    public async Task<string> GetUserFromCacheAsync(int userId)
    {
        var cacheKey = $"user:{userId}";
        var cachedUser = await _cache.GetAsync(cacheKey);
        
        if (!string.IsNullOrEmpty(cachedUser))
            return cachedUser;
        
        // Get from external API
        var userData = await _externalApi.GetDataAsync("/users");
        
        // Cache the result
        await _cache.SetAsync(cacheKey, userData, TimeSpan.FromMinutes(5));
        
        return userData;
    }
    
    public async Task<int> GetUserCountAsync()
    {
        // Try cache first
        var cachedCount = await _cache.GetAsync("user:count");
        if (int.TryParse(cachedCount, out var count))
            return count;
        
        // Query database
        count = await _database.ExecuteQueryAsync("SELECT COUNT(*) FROM Users");
        
        // Cache the result
        await _cache.SetAsync("user:count", count.ToString(), TimeSpan.FromMinutes(10));
        
        return count;
    }
    
    public async Task<bool> CreateUserAsync(string userData)
    {
        try
        {
            // Post to external API
            var success = await _externalApi.PostDataAsync("/users", userData);
            if (!success)
                return false;
            
            // Insert into database
            var rowsAffected = await _database.ExecuteQueryAsync($"INSERT INTO Users (Data) VALUES ('{userData}')");
            if (rowsAffected == 0)
                return false;
            
            // Invalidate cache
            await _cache.RemoveAsync("user:count");
            
            return true;
        }
        catch
        {
            return false;
        }
    }
    
    public async Task CleanupAsync()
    {
        await _database.DisconnectAsync();
    }
}

// Integration test class
public class UserServiceIntegrationTests
{
    public async Task InitializeService_ConnectsToAllComponents_ReturnsTrue()
    {
        // Arrange
        var database = new SqlDatabaseConnection();
        var cache = new RedisCacheService();
        var externalApi = new ExternalApiService();
        
        var userService = new UserServiceIntegration(database, cache, externalApi);
        
        try
        {
            // Act
            var result = await userService.InitializeAsync();
            
            // Assert
            if (!result)
                throw new Exception("Service initialization failed");
            
            var dbConnected = await database.IsConnectedAsync();
            if (!dbConnected)
                throw new Exception("Database is not connected");
            
            var systemInitialized = await cache.ExistsAsync("system:initialized");
            if (!systemInitialized)
                throw new Exception("System initialization flag not set in cache");
        }
        finally
        {
            await userService.CleanupAsync();
        }
    }
    
    public async Task GetUserFromCache_UserNotCached_FetchesFromApiAndCaches()
    {
        // Arrange
        var database = new SqlDatabaseConnection();
        var cache = new RedisCacheService();
        var externalApi = new ExternalApiService();
        
        var userService = new UserServiceIntegration(database, cache, externalApi);
        
        try
        {
            await userService.InitializeAsync();
            
            // Act
            var userData = await userService.GetUserFromCacheAsync(1);
            
            // Assert
            if (string.IsNullOrEmpty(userData))
                throw new Exception("User data should not be null or empty");
            
            if (!userData.Contains("John") && !userData.Contains("Jane"))
                throw new Exception("Unexpected user data format");
            
            // Check if data was cached
            var cachedData = await cache.GetAsync("user:1");
            if (cachedData != userData)
                throw new Exception("Data was not properly cached");
        }
        finally
        {
            await userService.CleanupAsync();
        }
    }
    
    public async Task GetUserFromCache_UserAlreadyCached_ReturnsFromCache()
    {
        // Arrange
        var database = new SqlDatabaseConnection();
        var cache = new RedisCacheService();
        var externalApi = new ExternalApiService();
        
        var userService = new UserServiceIntegration(database, cache, externalApi);
        
        try
        {
            await userService.InitializeAsync();
            
            // Pre-populate cache
            var expectedData = "[{\"id\":1,\"name\":\"CachedUser\"}]";
            await cache.SetAsync("user:2", expectedData, TimeSpan.FromMinutes(5));
            
            // Act
            var userData = await userService.GetUserFromCacheAsync(2);
            
            // Assert
            if (userData != expectedData)
                throw new Exception("Should return cached data, not fetch from API");
        }
        finally
        {
            await userService.CleanupAsync();
        }
    }
    
    public async Task GetUserCount_DatabaseQuery_CachesResult()
    {
        // Arrange
        var database = new SqlDatabaseConnection();
        var cache = new RedisCacheService();
        var externalApi = new ExternalApiService();
        
        var userService = new UserServiceIntegration(database, cache, externalApi);
        
        try
        {
            await userService.InitializeAsync();
            
            // Act
            var count = await userService.GetUserCountAsync();
            
            // Assert
            if (count != 42)
                throw new Exception($"Expected count 42, got {count}");
            
            // Check if result was cached
            var cachedCount = await cache.GetAsync("user:count");
            if (cachedCount != "42")
                throw new Exception("Count was not cached properly");
        }
        finally
        {
            await userService.CleanupAsync();
        }
    }
    
    public async Task CreateUser_ValidData_CreatesUserAndInvalidatesCache()
    {
        // Arrange
        var database = new SqlDatabaseConnection();
        var cache = new RedisCacheService();
        var externalApi = new ExternalApiService();
        
        var userService = new UserServiceIntegration(database, cache, externalApi);
        
        try
        {
            await userService.InitializeAsync();
            
            // Pre-populate cache
            await cache.SetAsync("user:count", "50", TimeSpan.FromMinutes(10));
            
            // Act
            var result = await userService.CreateUserAsync("{\"name\":\"New User\"}");
            
            // Assert
            if (!result)
                throw new Exception("User creation failed");
            
            // Check if cache was invalidated
            var cachedCount = await cache.ExistsAsync("user:count");
            if (cachedCount)
                throw new Exception("User count cache should have been invalidated");
        }
        finally
        {
            await userService.CleanupAsync();
        }
    }
    
    public async Task FullWorkflow_EndToEndTest_WorksCorrectly()
    {
        // Arrange
        var database = new SqlDatabaseConnection();
        var cache = new RedisCacheService();
        var externalApi = new ExternalApiService();
        
        var userService = new UserServiceIntegration(database, cache, externalApi);
        
        try
        {
            // Act - Full workflow
            var initialized = await userService.InitializeAsync();
            if (!initialized)
                throw new Exception("Initialization failed");
            
            var initialCount = await userService.GetUserCountAsync();
            
            var userData = await userService.GetUserFromCacheAsync(1);
            if (string.IsNullOrEmpty(userData))
                throw new Exception("Failed to get user data");
            
            var created = await userService.CreateUserAsync("{\"name\":\"Integration Test User\"}");
            if (!created)
                throw new Exception("Failed to create user");
            
            var newCount = await userService.GetUserCountAsync();
            
            // Assert
            if (initialCount != 42)
                throw new Exception($"Expected initial count 42, got {initialCount}");
            
            if (newCount == initialCount)
                throw new Exception("Count should have changed after creating user");
        }
        finally
        {
            await userService.CleanupAsync();
        }
    }
}

// Integration test runner
public class IntegrationTestRunner
{
    public static async Task RunAllTests()
    {
        Console.WriteLine("=== Running Integration Tests ===");
        
        var integrationTests = new UserServiceIntegrationTests();
        
        var testMethods = new[]
        {
            nameof(UserServiceIntegrationTests.InitializeService_ConnectsToAllComponents_ReturnsTrue),
            nameof(UserServiceIntegrationTests.GetUserFromCache_UserNotCached_FetchesFromApiAndCaches),
            nameof(UserServiceIntegrationTests.GetUserFromCache_UserAlreadyCached_ReturnsFromCache),
            nameof(UserServiceIntegrationTests.GetUserCount_DatabaseQuery_CachesResult),
            nameof(UserServiceIntegrationTests.CreateUser_ValidData_CreatesUserAndInvalidatesCache),
            nameof(UserServiceIntegrationTests.FullWorkflow_EndToEndTest_WorksCorrectly)
        };
        
        int passedTests = 0;
        int totalTests = testMethods.Length;
        
        foreach (var testMethod in testMethods)
        {
            try
            {
                await RunTest(testMethod, integrationTests);
                Console.WriteLine($"✓ {testMethod} - PASSED");
                passedTests++;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"✗ {testMethod} - FAILED: {ex.Message}");
            }
        }
        
        Console.WriteLine($"\nTest Results: {passedTests}/{totalTests} tests passed");
    }
    
    private static async Task RunTest(string testName, UserServiceIntegrationTests integrationTests)
    {
        switch (testName)
        {
            case nameof(UserServiceIntegrationTests.InitializeService_ConnectsToAllComponents_ReturnsTrue):
                await integrationTests.InitializeService_ConnectsToAllComponents_ReturnsTrue();
                break;
            case nameof(UserServiceIntegrationTests.GetUserFromCache_UserNotCached_FetchesFromApiAndCaches):
                await integrationTests.GetUserFromCache_UserNotCached_FetchesFromApiAndCaches();
                break;
            case nameof(UserServiceIntegrationTests.GetUserFromCache_UserAlreadyCached_ReturnsFromCache):
                await integrationTests.GetUserFromCache_UserAlreadyCached_ReturnsFromCache();
                break;
            case nameof(UserServiceIntegrationTests.GetUserCount_DatabaseQuery_CachesResult):
                await integrationTests.GetUserCount_DatabaseQuery_CachesResult();
                break;
            case nameof(UserServiceIntegrationTests.CreateUser_ValidData_CreatesUserAndInvalidatesCache):
                await integrationTests.CreateUser_ValidData_CreatesUserAndInvalidatesCache();
                break;
            case nameof(UserServiceIntegrationTests.FullWorkflow_EndToEndTest_WorksCorrectly):
                await integrationTests.FullWorkflow_EndToEndTest_WorksCorrectly();
                break;
        }
    }
}

// Program to run integration tests
public class IntegrationTestingProgram
{
    public static async Task Main(string[] args)
    {
        await IntegrationTestRunner.RunAllTests();
    }
}
