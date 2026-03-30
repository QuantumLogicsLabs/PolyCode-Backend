using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;
using System;
using System.Threading.Tasks;

// Services for DI demonstration
public interface IMessageService
{
    string SendMessage(string message);
}

public class EmailService : IMessageService
{
    public string SendMessage(string message)
    {
        return $"Email sent: {message}";
    }
}

public class SmsService : IMessageService
{
    public string SendMessage(string message)
    {
        return $"SMS sent: {message}";
    }
}

public class NotificationService
{
    private readonly IMessageService _messageService;
    
    // Constructor injection
    public NotificationService(IMessageService messageService)
    {
        _messageService = messageService;
    }
    
    public string SendNotification(string message)
    {
        return _messageService.SendMessage(message);
    }
}

// Service lifetime examples
public interface ITransientService
{
    Guid GetOperationId();
}

public interface ISingletonService
{
    Guid GetOperationId();
}

public interface IScopedService
{
    Guid GetOperationId();
}

public class TransientService : ITransientService
{
    private readonly Guid _operationId;
    
    public TransientService()
    {
        _operationId = Guid.NewGuid();
    }
    
    public Guid GetOperationId() => _operationId;
}

public class ScopedService : IScopedService
{
    private readonly Guid _operationId;
    
    public ScopedService()
    {
        _operationId = Guid.NewGuid();
    }
    
    public Guid GetOperationId() => _operationId;
}

public class SingletonService : ISingletonService
{
    private readonly Guid _operationId;
    
    public SingletonService()
    {
        _operationId = Guid.NewGuid();
    }
    
    public Guid GetOperationId() => _operationId;
}

// Service that demonstrates different lifetimes
public class LifetimeDemoService
{
    private readonly ITransientService _transientService;
    private readonly IScopedService _scopedService;
    private readonly ISingletonService _singletonService;
    
    public LifetimeDemoService(ITransientService transientService, 
                              IScopedService scopedService, 
                              ISingletonService singletonService)
    {
        _transientService = transientService;
        _scopedService = scopedService;
        _singletonService = singletonService;
    }
    
    public void ShowLifetimes()
    {
        Console.WriteLine($"Transient: {_transientService.GetOperationId()}");
        Console.WriteLine($"Scoped: {_scopedService.GetOperationId()}");
        Console.WriteLine($"Singleton: {_singletonService.GetOperationId()}");
    }
}

// Program.cs configuration
public class Program
{
    public static async Task Main(string[] args)
    {
        var host = CreateHostBuilder(args).Build();
        
        // Demonstrate DI
        await DemonstrateDependencyInjection(host);
        
        // Demonstrate service lifetimes
        await DemonstrateServiceLifetimes(host);
    }
    
    public static IHostBuilder CreateHostBuilder(string[] args) =>
        Host.CreateDefaultBuilder(args)
            .ConfigureServices((context, services) =>
            {
                // Register services with different lifetimes
                services.AddTransient<ITransientService, TransientService>();
                services.AddScoped<IScopedService, ScopedService>();
                services.AddSingleton<ISingletonService, SingletonService>();
                
                // Register message service (can be easily swapped)
                services.AddTransient<IMessageService, EmailService>();
                services.AddTransient<NotificationService>();
                services.AddTransient<LifetimeDemoService>();
            });
    
    private static async Task DemonstrateDependencyInjection(IHost host)
    {
        using var scope = host.Services.CreateScope();
        var services = scope.ServiceProvider;
        
        var notificationService = services.GetRequiredService<NotificationService>();
        var result = notificationService.SendNotification("Hello World!");
        
        Console.WriteLine(result);
    }
    
    private static async Task DemonstrateServiceLifetimes(IHost host)
    {
        using var scope = host.Services.CreateScope();
        var services = scope.ServiceProvider;
        
        Console.WriteLine("First request:");
        var demo1 = services.GetRequiredService<LifetimeDemoService>();
        demo1.ShowLifetimes();
        
        Console.WriteLine("\nSecond request (same scope):");
        var demo2 = services.GetRequiredService<LifetimeDemoService>();
        demo2.ShowLifetimes();
        
        Console.WriteLine("\nNew scope:");
        using var scope2 = host.Services.CreateScope();
        var services2 = scope2.ServiceProvider;
        var demo3 = services2.GetRequiredService<LifetimeDemoService>();
        demo3.ShowLifetimes();
    }
}
