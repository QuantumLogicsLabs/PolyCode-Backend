using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.DependencyInjection.Extensions;
using Microsoft.Extensions.Hosting;
using System;
using System.Collections.Generic;
using System.Threading.Tasks;

// Strategy pattern with DI
public interface IPaymentStrategy
{
    string ProcessPayment(decimal amount);
}

public class CreditCardPayment : IPaymentStrategy
{
    public string ProcessPayment(decimal amount)
    {
        return $"Credit card payment of ${amount} processed";
    }
}

public class PayPalPayment : IPaymentStrategy
{
    public string ProcessPayment(decimal amount)
    {
        return $"PayPal payment of ${amount} processed";
    }
}

public class BankTransferPayment : IPaymentStrategy
{
    public string ProcessPayment(decimal amount)
    {
        return $"Bank transfer payment of ${amount} processed";
    }
}

// Context that uses strategy
public class PaymentProcessor
{
    private readonly IEnumerable<IPaymentStrategy> _paymentStrategies;
    
    public PaymentProcessor(IEnumerable<IPaymentStrategy> paymentStrategies)
    {
        _paymentStrategies = paymentStrategies;
    }
    
    public string ProcessPayment(string paymentType, decimal amount)
    {
        foreach (var strategy in _paymentStrategies)
        {
            if (strategy.GetType().Name.Contains(paymentType, StringComparison.OrdinalIgnoreCase))
            {
                return strategy.ProcessPayment(amount);
            }
        }
        
        return $"Payment type '{paymentType}' not supported";
    }
}

// Decorator pattern with DI
public interface IRepository<T>
{
    T GetById(int id);
    void Add(T item);
    IEnumerable<T> GetAll();
}

public class Repository<T> : IRepository<T> where T : class
{
    private readonly List<T> _items = new();
    
    public T GetById(int id)
    {
        Console.WriteLine($"Getting item {id} from repository");
        return _items.Count > id ? _items[id] : null;
    }
    
    public void Add(T item)
    {
        Console.WriteLine($"Adding item to repository");
        _items.Add(item);
    }
    
    public IEnumerable<T> GetAll()
    {
        Console.WriteLine("Getting all items from repository");
        return _items;
    }
}

// Caching decorator
public class CachingRepository<T> : IRepository<T> where T : class
{
    private readonly IRepository<T> _innerRepository;
    private readonly Dictionary<int, T> _cache = new();
    
    public CachingRepository(IRepository<T> innerRepository)
    {
        _innerRepository = innerRepository;
    }
    
    public T GetById(int id)
    {
        if (_cache.TryGetValue(id, out var cachedItem))
        {
            Console.WriteLine($"Getting item {id} from cache");
            return cachedItem;
        }
        
        var item = _innerRepository.GetById(id);
        if (item != null)
        {
            _cache[id] = item;
        }
        
        return item;
    }
    
    public void Add(T item)
    {
        _innerRepository.Add(item);
        // Invalidate cache or update as needed
    }
    
    public IEnumerable<T> GetAll()
    {
        return _innerRepository.GetAll();
    }
}

// Logging decorator
public class LoggingRepository<T> : IRepository<T> where T : class
{
    private readonly IRepository<T> _innerRepository;
    
    public LoggingRepository(IRepository<T> innerRepository)
    {
        _innerRepository = innerRepository;
    }
    
    public T GetById(int id)
    {
        Console.WriteLine($"[LOG] Repository.GetById called with id: {id}");
        var result = _innerRepository.GetById(id);
        Console.WriteLine($"[LOG] Repository.GetById returned: {result != null}");
        return result;
    }
    
    public void Add(T item)
    {
        Console.WriteLine($"[LOG] Repository.Add called with item: {item}");
        _innerRepository.Add(item);
        Console.WriteLine("[LOG] Repository.Add completed");
    }
    
    public IEnumerable<T> GetAll()
    {
        Console.WriteLine("[LOG] Repository.GetAll called");
        var result = _innerRepository.GetAll();
        Console.WriteLine($"[LOG] Repository.GetAll returned {result?.Count() ?? 0} items");
        return result;
    }
}

// Factory pattern with DI
public interface IDataService
{
    string GetData();
}

public class SqlDataService : IDataService
{
    public string GetData()
    {
        return "Data from SQL Server";
    }
}

public class NoSqlDataService : IDataService
{
    public string GetData()
    {
        return "Data from MongoDB";
    }
}

public class DataServiceFactory
{
    private readonly IServiceProvider _serviceProvider;
    private readonly string _connectionString;
    
    public DataServiceFactory(IServiceProvider serviceProvider, string connectionString)
    {
        _serviceProvider = serviceProvider;
        _connectionString = connectionString;
    }
    
    public IDataService CreateDataService()
    {
        if (_connectionString.Contains("mongodb"))
        {
            return _serviceProvider.GetRequiredService<NoSqlDataService>();
        }
        
        return _serviceProvider.GetRequiredService<SqlDataService>();
    }
}

// Observer pattern with DI
public interface IEventPublisher
{
    void Publish<T>(T eventData);
}

public interface IEventHandler<T>
{
    void Handle(T eventData);
}

public class EventPublisher : IEventPublisher
{
    private readonly IServiceProvider _serviceProvider;
    
    public EventPublisher(IServiceProvider serviceProvider)
    {
        _serviceProvider = serviceProvider;
    }
    
    public void Publish<T>(T eventData)
    {
        var handlers = _serviceProvider.GetServices<IEventHandler<T>>();
        
        foreach (var handler in handlers)
        {
            handler.Handle(eventData);
        }
    }
}

public class UserCreatedEvent
{
    public string UserName { get; set; }
    public DateTime CreatedAt { get; set; }
}

public class EmailNotificationHandler : IEventHandler<UserCreatedEvent>
{
    public void Handle(UserCreatedEvent eventData)
    {
        Console.WriteLine($"Sending welcome email to {eventData.UserName}");
    }
}

public class AuditLogHandler : IEventHandler<UserCreatedEvent>
{
    public void Handle(UserCreatedEvent eventData)
    {
        Console.WriteLine($"Audit log: User '{eventData.UserName}' created at {eventData.CreatedAt}");
    }
}

// Program demonstrating advanced DI patterns
public class AdvancedPatternsProgram
{
    public static async Task Main(string[] args)
    {
        var host = CreateHostBuilder(args).Build();
        
        using var scope = host.Services.CreateScope();
        var services = scope.ServiceProvider;
        
        // Demonstrate strategy pattern
        Console.WriteLine("=== Strategy Pattern ===");
        var paymentProcessor = services.GetRequiredService<PaymentProcessor>();
        Console.WriteLine(paymentProcessor.ProcessPayment("CreditCard", 100));
        Console.WriteLine(paymentProcessor.ProcessPayment("PayPal", 50));
        
        // Demonstrate decorator pattern
        Console.WriteLine("\n=== Decorator Pattern ===");
        var repository = services.GetRequiredService<IRepository<string>>();
        repository.Add("Test Item");
        var item = repository.GetById(0);
        Console.WriteLine($"Retrieved item: {item}");
        
        // Demonstrate factory pattern
        Console.WriteLine("\n=== Factory Pattern ===");
        var sqlFactory = services.GetRequiredService<DataServiceFactory>();
        var sqlService = sqlFactory.CreateDataService();
        Console.WriteLine(sqlService.GetData());
        
        // Demonstrate observer pattern
        Console.WriteLine("\n=== Observer Pattern ===");
        var eventPublisher = services.GetRequiredService<IEventPublisher>();
        var userEvent = new UserCreatedEvent { UserName = "JohnDoe", CreatedAt = DateTime.Now };
        eventPublisher.Publish(userEvent);
    }
    
    public static IHostBuilder CreateHostBuilder(string[] args) =>
        Host.CreateDefaultBuilder(args)
            .ConfigureServices((context, services) =>
            {
                // Strategy pattern registration
                services.AddTransient<IPaymentStrategy, CreditCardPayment>();
                services.AddTransient<IPaymentStrategy, PayPalPayment>();
                services.AddTransient<IPaymentStrategy, BankTransferPayment>();
                services.AddTransient<PaymentProcessor>();
                
                // Decorator pattern registration
                services.AddTransient<IRepository<string>, Repository<string>>();
                services.Decorate<IRepository<string>, LoggingRepository<string>>();
                services.Decorate<IRepository<string>, CachingRepository<string>>();
                
                // Factory pattern registration
                services.AddTransient<SqlDataService>();
                services.AddTransient<NoSqlDataService>();
                services.AddTransient<DataServiceFactory>();
                
                // Observer pattern registration
                services.AddTransient<IEventPublisher, EventPublisher>();
                services.AddTransient<IEventHandler<UserCreatedEvent>, EmailNotificationHandler>();
                services.AddTransient<IEventHandler<UserCreatedEvent>, AuditLogHandler>();
            });
}
