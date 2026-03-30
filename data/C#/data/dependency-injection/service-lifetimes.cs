using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;
using System;
using System.Threading.Tasks;

// Detailed service lifetime examples
public interface ILogger
{
    void Log(string message);
}

public class ConsoleLogger : ILogger
{
    private readonly Guid _instanceId = Guid.NewGuid();
    
    public void Log(string message)
    {
        Console.WriteLine($"[{_instanceId}] {message}");
    }
}

public class FileLogger : ILogger
{
    private readonly Guid _instanceId = Guid.NewGuid();
    
    public void Log(string message)
    {
        Console.WriteLine($"[FileLogger {_instanceId}] Writing to file: {message}");
    }
}

// Service with dependency on logger
public class UserService
{
    private readonly ILogger _logger;
    private readonly Guid _instanceId = Guid.NewGuid();
    
    public UserService(ILogger logger)
    {
        _logger = logger;
    }
    
    public void CreateUser(string userName)
    {
        _logger.Log($"Creating user: {userName} (Service instance: {_instanceId})");
    }
}

// Service that manages other services
public class ServiceManager
{
    private readonly IServiceProvider _serviceProvider;
    
    public ServiceManager(IServiceProvider serviceProvider)
    {
        _serviceProvider = serviceProvider;
    }
    
    public void DemonstrateLifetimes()
    {
        Console.WriteLine("=== Transient Services ===");
        var transient1 = _serviceProvider.GetRequiredService<UserService>();
        var transient2 = _serviceProvider.GetRequiredService<UserService>();
        transient1.CreateUser("User1");
        transient2.CreateUser("User2");
        
        Console.WriteLine("\n=== Scoped Services ===");
        using var scope1 = _serviceProvider.CreateScope();
        var scoped1 = scope1.ServiceProvider.GetRequiredService<UserService>();
        scoped1.CreateUser("ScopedUser1");
        
        using var scope2 = _serviceProvider.CreateScope();
        var scoped2 = scope2.ServiceProvider.GetRequiredService<UserService>();
        scoped2.CreateUser("ScopedUser2");
        
        Console.WriteLine("\n=== Singleton Services ===");
        var singleton1 = _serviceProvider.GetRequiredService<IServiceProvider>(); // IServiceProvider is singleton
        var singleton2 = _serviceProvider.GetRequiredService<IServiceProvider>();
        Console.WriteLine($"Same instance? {ReferenceEquals(singleton1, singleton2)}");
    }
}

// Configuration service example
public interface IConfigurationService
{
    string GetSetting(string key);
    void SetSetting(string key, string value);
}

public class ConfigurationService : IConfigurationService
{
    private readonly Dictionary<string, string> _settings = new();
    
    public string GetSetting(string key)
    {
        return _settings.TryGetValue(key, out var value) ? value : null;
    }
    
    public void SetSetting(string key, string value)
    {
        _settings[key] = value;
    }
}

// Service that uses configuration
public class AppService
{
    private readonly IConfigurationService _config;
    private readonly ILogger _logger;
    
    public AppService(IConfigurationService config, ILogger logger)
    {
        _config = config;
        _logger = logger;
    }
    
    public void Run()
    {
        var appName = _config.GetSetting("AppName") ?? "Default App";
        _logger.Log($"Running application: {appName}");
    }
}

// Factory pattern with DI
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

// Program demonstrating advanced DI concepts
public class AdvancedProgram
{
    public static async Task Main(string[] args)
    {
        var host = CreateHostBuilder(args).Build();
        
        using var scope = host.Services.CreateScope();
        var services = scope.ServiceProvider;
        
        // Demonstrate service lifetimes
        var serviceManager = services.GetRequiredService<ServiceManager>();
        serviceManager.DemonstrateLifetimes();
        
        // Demonstrate factory pattern
        var userServiceFactory = services.GetRequiredService<IServiceFactory<UserService>>();
        var userService1 = userServiceFactory.CreateService();
        var userService2 = userServiceFactory.CreateService();
        
        userService1.CreateUser("FactoryUser1");
        userService2.CreateUser("FactoryUser2");
        
        // Demonstrate configuration
        var configService = services.GetRequiredService<IConfigurationService>();
        configService.SetSetting("AppName", "My Advanced App");
        
        var appService = services.GetRequiredService<AppService>();
        appService.Run();
    }
    
    public static IHostBuilder CreateHostBuilder(string[] args) =>
        Host.CreateDefaultBuilder(args)
            .ConfigureServices((context, services) =>
            {
                // Register logger as singleton (shared across all services)
                services.AddSingleton<ILogger, ConsoleLogger>();
                
                // Register user service as scoped (new instance per scope)
                services.AddScoped<UserService>();
                
                // Register configuration as singleton
                services.AddSingleton<IConfigurationService, ConfigurationService>();
                
                // Register app service
                services.AddTransient<AppService>();
                
                // Register service manager
                services.AddTransient<ServiceManager>();
                
                // Register factory
                services.AddTransient(typeof(IServiceFactory<>), typeof(ServiceFactory<>));
            });
}
