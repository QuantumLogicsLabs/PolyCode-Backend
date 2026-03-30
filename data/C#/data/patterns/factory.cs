using System;
using System.Collections.Generic;

// Factory Pattern Examples

// 1. Simple Factory
public interface IVehicle
{
    string Drive();
}

public class Car : IVehicle
{
    public string Drive()
    {
        return "Driving a car";
    }
}

public class Motorcycle : IVehicle
{
    public string Drive()
    {
        return "Riding a motorcycle";
    }
}

public class Truck : IVehicle
{
    public string Drive()
    {
        return "Driving a truck";
    }
}

public class VehicleFactory
{
    public IVehicle CreateVehicle(string vehicleType)
    {
        return vehicleType.ToLower() switch
        {
            "car" => new Car(),
            "motorcycle" => new Motorcycle(),
            "truck" => new Truck(),
            _ => throw new ArgumentException($"Unknown vehicle type: {vehicleType}")
        };
    }
}

// 2. Factory Method Pattern
public abstract class Document
{
    public abstract void Open();
    public abstract void Close();
}

public class WordDocument : Document
{
    public override void Open()
    {
        Console.WriteLine("Opening Word document");
    }
    
    public override void Close()
    {
        Console.WriteLine("Closing Word document");
    }
}

public class PdfDocument : Document
{
    public override void Open()
    {
        Console.WriteLine("Opening PDF document");
    }
    
    public override void Close()
    {
        Console.WriteLine("Closing PDF document");
    }
}

public abstract class DocumentCreator
{
    public abstract Document CreateDocument();
    
    public void ManageDocument()
    {
        var document = CreateDocument();
        document.Open();
        Console.WriteLine("Managing document...");
        document.Close();
    }
}

public class WordDocumentCreator : DocumentCreator
{
    public override Document CreateDocument()
    {
        return new WordDocument();
    }
}

public class PdfDocumentCreator : DocumentCreator
{
    public override Document CreateDocument()
    {
        return new PdfDocument();
    }
}

// 3. Abstract Factory Pattern
public interface IButton
{
    void Render();
}

public interface ICheckbox
{
    void Render();
}

public interface IGUIFactory
{
    IButton CreateButton();
    ICheckbox CreateCheckbox();
}

// Windows UI components
public class WindowsButton : IButton
{
    public void Render()
    {
        Console.WriteLine("Rendering Windows button");
    }
}

public class WindowsCheckbox : ICheckbox
{
    public void Render()
    {
        Console.WriteLine("Rendering Windows checkbox");
    }
}

public class WindowsGUIFactory : IGUIFactory
{
    public IButton CreateButton()
    {
        return new WindowsButton();
    }
    
    public ICheckbox CreateCheckbox()
    {
        return new WindowsCheckbox();
    }
}

// macOS UI components
public class MacButton : IButton
{
    public void Render()
    {
        Console.WriteLine("Rendering macOS button");
    }
}

public class MacCheckbox : ICheckbox
{
    public void Render()
    {
        Console.WriteLine("Rendering macOS checkbox");
    }
}

public class MacGUIFactory : IGUIFactory
{
    public IButton CreateButton()
    {
        return new MacButton();
    }
    
    public ICheckbox CreateCheckbox()
    {
        return new MacCheckbox();
    }
}

// 4. Generic Factory
public interface IRepository<T>
{
    T GetById(int id);
    void Save(T item);
    List<T> GetAll();
}

public class SqlRepository<T> : IRepository<T> where T : class
{
    public T GetById(int id)
    {
        Console.WriteLine($"Getting {typeof(T).Name} with ID {id} from SQL database");
        return default(T);
    }
    
    public void Save(T item)
    {
        Console.WriteLine($"Saving {typeof(T).Name} to SQL database");
    }
    
    public List<T> GetAll()
    {
        Console.WriteLine($"Getting all {typeof(T).Name} from SQL database");
        return new List<T>();
    }
}

public class MongoRepository<T> : IRepository<T> where T : class
{
    public T GetById(int id)
    {
        Console.WriteLine($"Getting {typeof(T).Name} with ID {id} from MongoDB");
        return default(T);
    }
    
    public void Save(T item)
    {
        Console.WriteLine($"Saving {typeof(T).Name} to MongoDB");
    }
    
    public List<T> GetAll()
    {
        Console.WriteLine($"Getting all {typeof(T).Name} from MongoDB");
        return new List<T>();
    }
}

public class RepositoryFactory
{
    public IRepository<T> CreateRepository<T>(string databaseType) where T : class
    {
        return databaseType.ToLower() switch
        {
            "sql" => new SqlRepository<T>(),
            "mongodb" => new MongoRepository<T>(),
            _ => throw new ArgumentException($"Unknown database type: {databaseType}")
        };
    }
}

// 5. Factory with Configuration
public class DatabaseConfiguration
{
    public string DatabaseType { get; set; }
    public string ConnectionString { get; set; }
    public int TimeoutSeconds { get; set; }
}

public interface IDatabaseConnection
{
    void Connect();
    void Disconnect();
    void ExecuteQuery(string query);
}

public class SqlConnection : IDatabaseConnection
{
    private readonly DatabaseConfiguration _config;
    
    public SqlConnection(DatabaseConfiguration config)
    {
        _config = config;
    }
    
    public void Connect()
    {
        Console.WriteLine($"Connecting to SQL Server with connection string: {_config.ConnectionString}");
    }
    
    public void Disconnect()
    {
        Console.WriteLine("Disconnecting from SQL Server");
    }
    
    public void ExecuteQuery(string query)
    {
        Console.WriteLine($"Executing SQL query: {query}");
    }
}

public class MongoConnection : IDatabaseConnection
{
    private readonly DatabaseConfiguration _config;
    
    public MongoConnection(DatabaseConfiguration config)
    {
        _config = config;
    }
    
    public void Connect()
    {
        Console.WriteLine($"Connecting to MongoDB with connection string: {_config.ConnectionString}");
    }
    
    public void Disconnect()
    {
        Console.WriteLine("Disconnecting from MongoDB");
    }
    
    public void ExecuteQuery(string query)
    {
        Console.WriteLine($"Executing MongoDB query: {query}");
    }
}

public class DatabaseConnectionFactory
{
    public IDatabaseConnection CreateConnection(DatabaseConfiguration config)
    {
        return config.DatabaseType.ToLower() switch
        {
            "sql" => new SqlConnection(config),
            "mongodb" => new MongoConnection(config),
            _ => throw new ArgumentException($"Unsupported database type: {config.DatabaseType}")
        };
    }
}

// 6. Factory with Dependency Injection
public interface ILogger
{
    void Log(string message);
}

public class ConsoleLogger : ILogger
{
    public void Log(string message)
    {
        Console.WriteLine($"[LOG] {message}");
    }
}

public class FileLogger : ILogger
{
    public void Log(string message)
    {
        Console.WriteLine($"[FILE LOG] {message}");
    }
}

public class LoggerFactory
{
    private readonly Dictionary<string, Func<ILogger>> _factories;
    
    public LoggerFactory()
    {
        _factories = new Dictionary<string, Func<ILogger>>
        {
            ["console"] = () => new ConsoleLogger(),
            ["file"] = () => new FileLogger()
        };
    }
    
    public ILogger CreateLogger(string loggerType)
    {
        if (_factories.TryGetValue(loggerType.ToLower(), out var factory))
        {
            return factory();
        }
        
        throw new ArgumentException($"Unknown logger type: {loggerType}");
    }
    
    public void RegisterLogger(string loggerType, Func<ILogger> factory)
    {
        _factories[loggerType.ToLower()] = factory;
    }
}

// Demonstration program
public class FactoryPatternDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Factory Pattern Demonstration ===\n");
        
        // Simple Factory
        Console.WriteLine("--- Simple Factory ---");
        var vehicleFactory = new VehicleFactory();
        
        var car = vehicleFactory.CreateVehicle("car");
        var motorcycle = vehicleFactory.CreateVehicle("motorcycle");
        var truck = vehicleFactory.CreateVehicle("truck");
        
        Console.WriteLine(car.Drive());
        Console.WriteLine(motorcycle.Drive());
        Console.WriteLine(truck.Drive());
        
        // Factory Method
        Console.WriteLine("\n--- Factory Method ---");
        var wordCreator = new WordDocumentCreator();
        var pdfCreator = new PdfDocumentCreator();
        
        Console.WriteLine("Word Document:");
        wordCreator.ManageDocument();
        
        Console.WriteLine("\nPDF Document:");
        pdfCreator.ManageDocument();
        
        // Abstract Factory
        Console.WriteLine("\n--- Abstract Factory ---");
        Console.WriteLine("Windows UI:");
        var windowsFactory = new WindowsGUIFactory();
        var windowsButton = windowsFactory.CreateButton();
        var windowsCheckbox = windowsFactory.CreateCheckbox();
        windowsButton.Render();
        windowsCheckbox.Render();
        
        Console.WriteLine("\nmacOS UI:");
        var macFactory = new MacGUIFactory();
        var macButton = macFactory.CreateButton();
        var macCheckbox = macFactory.CreateCheckbox();
        macButton.Render();
        macCheckbox.Render();
        
        // Generic Factory
        Console.WriteLine("\n--- Generic Factory ---");
        var repositoryFactory = new RepositoryFactory();
        
        var sqlRepo = repositoryFactory.CreateRepository<User>("sql");
        var mongoRepo = repositoryFactory.CreateRepository<User>("mongodb");
        
        sqlRepo.GetById(1);
        sqlRepo.Save(new User { Id = 1, Name = "John" });
        
        mongoRepo.GetById(1);
        mongoRepo.Save(new User { Id = 1, Name = "John" });
        
        // Factory with Configuration
        Console.WriteLine("\n--- Factory with Configuration ---");
        var dbConnectionFactory = new DatabaseConnectionFactory();
        
        var sqlConfig = new DatabaseConfiguration
        {
            DatabaseType = "SQL",
            ConnectionString = "Server=localhost;Database=MyApp;",
            TimeoutSeconds = 30
        };
        
        var mongoConfig = new DatabaseConfiguration
        {
            DatabaseType = "MongoDB",
            ConnectionString = "mongodb://localhost:27017",
            TimeoutSeconds = 10
        };
        
        var sqlConnection = dbConnectionFactory.CreateConnection(sqlConfig);
        var mongoConnection = dbConnectionFactory.CreateConnection(mongoConfig);
        
        sqlConnection.Connect();
        sqlConnection.ExecuteQuery("SELECT * FROM Users");
        sqlConnection.Disconnect();
        
        mongoConnection.Connect();
        mongoConnection.ExecuteQuery("db.users.find()");
        mongoConnection.Disconnect();
        
        // Factory with Dependency Injection
        Console.WriteLine("\n--- Factory with Dependency Injection ---");
        var loggerFactory = new LoggerFactory();
        
        var consoleLogger = loggerFactory.CreateLogger("console");
        var fileLogger = loggerFactory.CreateLogger("file");
        
        consoleLogger.Log("This message goes to console");
        fileLogger.Log("This message goes to file");
        
        // Register a new logger type
        loggerFactory.RegisterLogger("debug", () => new ConsoleLogger());
        var debugLogger = loggerFactory.CreateLogger("debug");
        debugLogger.Log("This is a debug message");
        
        Console.WriteLine("\n=== Factory Pattern Demo Complete ===");
    }
}

// Supporting classes
public class User
{
    public int Id { get; set; }
    public string Name { get; set; }
}

// Factory Pattern Usage Guidelines
/*
When to use Factory Pattern:
1. When you don't know the exact types of objects you need to create
2. When you want to provide users with a way to create objects without exposing creation logic
3. When you want to centralize object creation logic
4. When you need to create objects based on configuration or runtime parameters
5. When you want to decouple object creation from object usage

Simple Factory:
- Use when you have a small number of related objects
- Not a true design pattern, but a common programming idiom
- Easy to implement and understand

Factory Method:
- Use when you want to let subclasses decide which class to instantiate
- Provides extension points for derived classes
- Follows Open/Closed Principle

Abstract Factory:
- Use when you need to create families of related objects
- Ensures that created objects are compatible
- Useful for platform-specific implementations

Advantages:
- Decouples object creation from object usage
- Makes code more maintainable and extensible
- Provides better abstraction over object creation
- Enables easy testing with mock objects

Disadvantages:
- Can increase code complexity
- May lead to many small classes
- Can make debugging more difficult
- May violate Single Responsibility Principle

Best Practices:
- Keep factories focused on object creation
- Use dependency injection when possible
- Consider using generic factories for type safety
- Validate input parameters in factory methods
- Use meaningful names for factory methods
*/
