using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;
using System;
using System.Threading.Tasks;

// Simple factory interface
public interface IDataServiceFactory
{
    IDataService CreateService(string serviceType);
}

public interface IDataService
{
    string GetData();
}

// Concrete implementations
public class SqlDataService : IDataService
{
    public string GetData()
    {
        return "Data retrieved from SQL Server";
    }
}

public class NoSqlDataService : IDataService
{
    public string GetData()
    {
        return "Data retrieved from MongoDB";
    }
}

public class CacheDataService : IDataService
{
    public string GetData()
    {
        return "Data retrieved from Redis Cache";
    }
}

// Factory implementation
public class DataServiceFactory : IDataServiceFactory
{
    private readonly IServiceProvider _serviceProvider;
    
    public DataServiceFactory(IServiceProvider serviceProvider)
    {
        _serviceProvider = serviceProvider;
    }
    
    public IDataService CreateService(string serviceType)
    {
        return serviceType.ToLower() switch
        {
            "sql" => _serviceProvider.GetRequiredService<SqlDataService>(),
            "nosql" => _serviceProvider.GetRequiredService<NoSqlDataService>(),
            "cache" => _serviceProvider.GetRequiredService<CacheDataService>(),
            _ => throw new ArgumentException($"Unknown service type: {serviceType}")
        };
    }
}

// Generic factory interface
public interface IServiceFactory<T>
{
    T CreateService();
}

public class ServiceFactory<T> : IServiceFactory<T> where T : class
{
    private readonly IServiceProvider _serviceProvider;
    
    public ServiceFactory(IServiceProvider serviceProvider)
    {
        _serviceProvider = serviceProvider;
    }
    
    public T CreateService()
    {
        return _serviceProvider.GetRequiredService<T>();
    }
}

// Abstract factory pattern
public interface IDataSourceFactory
{
    IConnection CreateConnection();
    ICommand CreateCommand();
}

public interface IConnection
{
    string Connect();
    void Disconnect();
}

public interface ICommand
{
    string Execute(string query);
}

// SQL Server factory
public class SqlServerFactory : IDataSourceFactory
{
    public IConnection CreateConnection()
    {
        return new SqlServerConnection();
    }
    
    public ICommand CreateCommand()
    {
        return new SqlServerCommand();
    }
}

// MongoDB factory
public class MongoDbFactory : IDataSourceFactory
{
    public IConnection CreateConnection()
    {
        return new MongoDbConnection();
    }
    
    public ICommand CreateCommand()
    {
        return new MongoDbCommand();
    }
}

// SQL Server implementations
public class SqlServerConnection : IConnection
{
    public string Connect()
    {
        return "Connected to SQL Server";
    }
    
    public void Disconnect()
    {
        Console.WriteLine("Disconnected from SQL Server");
    }
}

public class SqlServerCommand : ICommand
{
    public string Execute(string query)
    {
        return $"SQL Server executed: {query}";
    }
}

// MongoDB implementations
public class MongoDbConnection : IConnection
{
    public string Connect()
    {
        return "Connected to MongoDB";
    }
    
    public void Disconnect()
    {
        Console.WriteLine("Disconnected from MongoDB");
    }
}

public class MongoDbCommand : ICommand
{
    public string Execute(string query)
    {
        return $"MongoDB executed: {query}";
    }
}

// Factory provider that selects appropriate factory
public class DataSourceFactoryProvider
{
    private readonly IServiceProvider _serviceProvider;
    
    public DataSourceFactoryProvider(IServiceProvider serviceProvider)
    {
        _serviceProvider = serviceProvider;
    }
    
    public IDataSourceFactory GetFactory(string databaseType)
    {
        return databaseType.ToLower() switch
        {
            "sqlserver" => _serviceProvider.GetRequiredService<SqlServerFactory>(),
            "mongodb" => _serviceProvider.GetRequiredService<MongoDbFactory>(),
            _ => throw new ArgumentException($"Unsupported database type: {databaseType}")
        };
    }
}

// Service that uses factories
public class DataProcessor
{
    private readonly IDataServiceFactory _dataServiceFactory;
    private readonly DataSourceFactoryProvider _factoryProvider;
    
    public DataProcessor(IDataServiceFactory dataServiceFactory, DataSourceFactoryProvider factoryProvider)
    {
        _dataServiceFactory = dataServiceFactory;
        _factoryProvider = factoryProvider;
    }
    
    public void ProcessData()
    {
        // Using simple factory
        var sqlService = _dataServiceFactory.CreateService("sql");
        Console.WriteLine(sqlService.GetData());
        
        var nosqlService = _dataServiceFactory.CreateService("nosql");
        Console.WriteLine(nosqlService.GetData());
        
        // Using abstract factory
        var sqlFactory = _factoryProvider.GetFactory("sqlserver");
        var sqlConnection = sqlFactory.CreateConnection();
        var sqlCommand = sqlFactory.CreateCommand();
        
        Console.WriteLine(sqlConnection.Connect());
        Console.WriteLine(sqlCommand.Execute("SELECT * FROM Users"));
        sqlConnection.Disconnect();
        
        var mongoFactory = _factoryProvider.GetFactory("mongodb");
        var mongoConnection = mongoFactory.CreateConnection();
        var mongoCommand = mongoFactory.CreateCommand();
        
        Console.WriteLine(mongoConnection.Connect());
        Console.WriteLine(mongoCommand.Execute("db.users.find()"));
        mongoConnection.Disconnect();
    }
}

// Program demonstrating factory patterns
public class FactoryPatternProgram
{
    public static async Task Main(string[] args)
    {
        var host = CreateHostBuilder(args).Build();
        
        using var scope = host.Services.CreateScope();
        var services = scope.ServiceProvider;
        
        // Demonstrate factory patterns
        var dataProcessor = services.GetRequiredService<DataProcessor>();
        dataProcessor.ProcessData();
        
        // Demonstrate generic factory
        Console.WriteLine("\n=== Generic Factory ===");
        var userServiceFactory = services.GetRequiredService<IServiceFactory<UserService>>();
        var userService1 = userServiceFactory.CreateService();
        var userService2 = userServiceFactory.CreateService();
        
        Console.WriteLine($"Same instance? {ReferenceEquals(userService1, userService2)}");
    }
    
    public static IHostBuilder CreateHostBuilder(string[] args) =>
        Host.CreateDefaultBuilder(args)
            .ConfigureServices((context, services) =>
            {
                // Register data services
                services.AddTransient<SqlDataService>();
                services.AddTransient<NoSqlDataService>();
                services.AddTransient<CacheDataService>();
                
                // Register factories
                services.AddTransient<IDataServiceFactory, DataServiceFactory>();
                services.AddTransient(typeof(IServiceFactory<>), typeof(ServiceFactory<>));
                
                // Register abstract factories
                services.AddTransient<SqlServerFactory>();
                services.AddTransient<MongoDbFactory>();
                services.AddTransient<DataSourceFactoryProvider>();
                
                // Register main service
                services.AddTransient<DataProcessor>();
                
                // Register a sample service for generic factory demo
                services.AddTransient<UserService>();
            });
}

// Sample service for generic factory demo
public class UserService
{
    private readonly Guid _instanceId = Guid.NewGuid();
    
    public override string ToString()
    {
        return $"UserService instance: {_instanceId}";
    }
}
