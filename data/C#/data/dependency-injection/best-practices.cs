using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;
using Microsoft.Extensions.Logging;
using System;
using System.Threading.Tasks;

// Best practices for Dependency Injection in C#

// 1. Prefer constructor injection over property or method injection
public class GoodService
{
    private readonly ILogger<GoodService> _logger;
    private readonly IRepository _repository;
    
    // Good: Constructor injection
    public GoodService(ILogger<GoodService> logger, IRepository repository)
    {
        _logger = logger ?? throw new ArgumentNullException(nameof(logger));
        _repository = repository ?? throw new ArgumentNullException(nameof(repository));
    }
    
    public void DoWork()
    {
        _logger.LogInformation("Doing work");
        var data = _repository.GetData();
        // Process data...
    }
}

// Bad: Property injection (avoid when possible)
public class BadService
{
    public ILogger<BadService> Logger { get; set; }
    public IRepository Repository { get; set; }
    
    public void DoWork()
    {
        Logger?.LogInformation("Doing work");
        var data = Repository?.GetData();
        // Process data...
    }
}

// 2. Use interfaces for services to enable loose coupling
public interface IRepository
{
    string GetData();
    void SaveData(string data);
}

public class SqlRepository : IRepository
{
    public string GetData()
    {
        return "Data from SQL Server";
    }
    
    public void SaveData(string data)
    {
        Console.WriteLine($"Saving to SQL Server: {data}");
    }
}

public class MongoRepository : IRepository
{
    public string GetData()
    {
        return "Data from MongoDB";
    }
    
    public void SaveData(string data)
    {
        Console.WriteLine($"Saving to MongoDB: {data}");
    }
}

// 3. Choose appropriate service lifetimes
public interface ITransientService { void DoWork(); }
public interface IScopedService { void DoWork(); }
public interface ISingletonService { void DoWork(); }

public class TransientService : ITransientService
{
    private readonly Guid _instanceId = Guid.NewGuid();
    public void DoWork() => Console.WriteLine($"Transient: {_instanceId}");
}

public class ScopedService : IScopedService
{
    private readonly Guid _instanceId = Guid.NewGuid();
    public void DoWork() => Console.WriteLine($"Scoped: {_instanceId}");
}

public class SingletonService : ISingletonService
{
    private readonly Guid _instanceId = Guid.NewGuid();
    public void DoWork() => Console.WriteLine($"Singleton: {_instanceId}");
}

// 4. Avoid service locator anti-pattern
public class AntiPatternService
{
    private readonly IServiceProvider _serviceProvider;
    
    public AntiPatternService(IServiceProvider serviceProvider)
    {
        _serviceProvider = serviceProvider;
    }
    
    public void DoWork()
    {
        // Bad: Service locator pattern
        var repository = _serviceProvider.GetRequiredService<IRepository>();
        var data = repository.GetData();
    }
}

// Good: Explicit dependencies
public class GoodPatternService
{
    private readonly IRepository _repository;
    
    public GoodPatternService(IRepository repository)
    {
        _repository = repository;
    }
    
    public void DoWork()
    {
        // Good: Explicit dependency
        var data = _repository.GetData();
    }
}

// 5. Use options pattern for configuration
public class EmailSettings
{
    public string SmtpServer { get; set; } = "localhost";
    public int Port { get; set; } = 587;
    public string Username { get; set; }
    public string Password { get; set; }
    public bool UseSsl { get; set; } = true;
}

public interface IEmailService
{
    void SendEmail(string to, string subject, string body);
}

public class EmailService : IEmailService
{
    private readonly EmailSettings _settings;
    private readonly ILogger<EmailService> _logger;
    
    public EmailService(IOptions<EmailSettings> settings, ILogger<EmailService> logger)
    {
        _settings = settings.Value;
        _logger = logger;
    }
    
    public void SendEmail(string to, string subject, string body)
    {
        _logger.LogInformation($"Sending email to {to} via {_settings.SmtpServer}:{_settings.Port}");
        // Implementation...
    }
}

// 6. Avoid circular dependencies
// Bad: Circular dependency
public class ServiceA
{
    private readonly ServiceB _serviceB;
    
    public ServiceA(ServiceB serviceB)
    {
        _serviceB = serviceB;
    }
}

public class ServiceB
{
    private readonly ServiceA _serviceA;
    
    public ServiceB(ServiceA serviceA)
    {
        _serviceA = serviceA;
    }
}

// Good: Break circular dependency with interface
public interface IServiceB
{
    void DoWork();
}

public class GoodServiceA
{
    private readonly IServiceB _serviceB;
    
    public GoodServiceA(IServiceB serviceB)
    {
        _serviceB = serviceB;
    }
}

public class GoodServiceB : IServiceB
{
    private readonly GoodServiceA _serviceA;
    
    public GoodServiceB(GoodServiceA serviceA)
    {
        _serviceA = serviceA;
    }
    
    public void DoWork()
    {
        // Implementation...
    }
}

// 7. Use validation in constructors
public class ValidatedService
{
    private readonly string _connectionString;
    private readonly ILogger<ValidatedService> _logger;
    
    public ValidatedService(string connectionString, ILogger<ValidatedService> logger)
    {
        if (string.IsNullOrWhiteSpace(connectionString))
            throw new ArgumentException("Connection string cannot be null or empty", nameof(connectionString));
        
        _connectionString = connectionString;
        _logger = logger ?? throw new ArgumentNullException(nameof(logger));
    }
}

// 8. Consider using factory for complex object creation
public interface IComplexObjectFactory
{
    ComplexObject Create(string parameter1, int parameter2);
}

public class ComplexObjectFactory : IComplexObjectFactory
{
    private readonly IServiceProvider _serviceProvider;
    
    public ComplexObjectFactory(IServiceProvider serviceProvider)
    {
        _serviceProvider = serviceProvider;
    }
    
    public ComplexObject Create(string parameter1, int parameter2)
    {
        var logger = _serviceProvider.GetRequiredService<ILogger<ComplexObject>>();
        var repository = _serviceProvider.GetRequiredService<IRepository>();
        
        return new ComplexObject(parameter1, parameter2, logger, repository);
    }
}

public class ComplexObject
{
    public ComplexObject(string param1, int param2, ILogger<ComplexObject> logger, IRepository repository)
    {
        // Complex initialization...
    }
}

// Program demonstrating DI best practices
public class BestPracticesProgram
{
    public static async Task Main(string[] args)
    {
        var host = CreateHostBuilder(args).Build();
        
        using var scope = host.Services.CreateScope();
        var services = scope.ServiceProvider;
        
        // Demonstrate good practices
        var goodService = services.GetRequiredService<GoodService>();
        goodService.DoWork();
        
        // Demonstrate options pattern
        var emailService = services.GetRequiredService<IEmailService>();
        emailService.SendEmail("test@example.com", "Test", "Hello World");
        
        // Demonstrate factory pattern
        var factory = services.GetRequiredService<IComplexObjectFactory>();
        var complexObject = factory.Create("test", 42);
        Console.WriteLine($"Created complex object: {complexObject != null}");
    }
    
    public static IHostBuilder CreateHostBuilder(string[] args) =>
        Host.CreateDefaultBuilder(args)
            .ConfigureServices((context, services) =>
            {
                // Configure options
                services.Configure<EmailSettings>(context.Configuration.GetSection("EmailSettings"));
                
                // Register repositories
                services.AddTransient<IRepository, SqlRepository>();
                
                // Register services with appropriate lifetimes
                services.AddTransient<ITransientService, TransientService>();
                services.AddScoped<IScopedService, ScopedService>();
                services.AddSingleton<ISingletonService, SingletonService>();
                
                // Register good services
                services.AddTransient<GoodService>();
                services.AddTransient<GoodPatternService>();
                services.AddTransient<ValidatedService>();
                
                // Register email service
                services.AddTransient<IEmailService, EmailService>();
                
                // Register factory
                services.AddTransient<IComplexObjectFactory, ComplexObjectFactory>();
                
                // Register logging
                services.AddLogging();
            });
}

// Example appsettings.json structure
/*
{
  "EmailSettings": {
    "SmtpServer": "smtp.gmail.com",
    "Port": 587,
    "Username": "your-email@gmail.com",
    "Password": "your-app-password",
    "UseSsl": true
  }
}
*/
