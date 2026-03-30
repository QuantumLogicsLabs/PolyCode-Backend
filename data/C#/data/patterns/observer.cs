using System;
using System.Collections.Generic;

// Observer Pattern Examples

// 1. Basic Observer Pattern
public interface IObserver
{
    void Update(string message);
}

public interface IObservable
{
    void RegisterObserver(IObserver observer);
    void UnregisterObserver(IObserver observer);
    void NotifyObservers();
}

public class WeatherStation : IObservable
{
    private readonly List<IObserver> _observers = new();
    private float _temperature;
    private float _humidity;
    private float _pressure;
    
    public float Temperature
    {
        get => _temperature;
        set
        {
            _temperature = value;
            NotifyObservers();
        }
    }
    
    public float Humidity
    {
        get => _humidity;
        set
        {
            _humidity = value;
            NotifyObservers();
        }
    }
    
    public float Pressure
    {
        get => _pressure;
        set
        {
            _pressure = value;
            NotifyObservers();
        }
    }
    
    public void RegisterObserver(IObserver observer)
    {
        if (!_observers.Contains(observer))
        {
            _observers.Add(observer);
            Console.WriteLine($"Observer registered. Total observers: {_observers.Count}");
        }
    }
    
    public void UnregisterObserver(IObserver observer)
    {
        if (_observers.Remove(observer))
        {
            Console.WriteLine($"Observer unregistered. Total observers: {_observers.Count}");
        }
    }
    
    public void NotifyObservers()
    {
        string message = $"Weather Update - Temp: {_temperature}°C, Humidity: {_humidity}%, Pressure: {_pressure} hPa";
        
        foreach (var observer in _observers)
        {
            observer.Update(message);
        }
    }
}

public class WeatherDisplay : IObserver
{
    private readonly string _displayName;
    
    public WeatherDisplay(string displayName)
    {
        _displayName = displayName;
    }
    
    public void Update(string message)
    {
        Console.WriteLine($"{_displayName}: {message}");
    }
}

// 2. Generic Observer Pattern
public interface IGenericObserver<T>
{
    void Update(T data);
}

public interface IGenericObservable<T>
{
    event Action<T> DataChanged;
    void NotifyObservers(T data);
}

public class StockMarket : IGenericObservable<StockPrice>
{
    public event Action<StockPrice> DataChanged;
    
    public void UpdateStockPrice(string symbol, decimal price)
    {
        var stockPrice = new StockPrice { Symbol = symbol, Price = price, Timestamp = DateTime.Now };
        NotifyObservers(stockPrice);
    }
    
    public void NotifyObservers(StockPrice data)
    {
        DataChanged?.Invoke(data);
    }
}

public class StockPrice
{
    public string Symbol { get; set; }
    public decimal Price { get; set; }
    public DateTime Timestamp { get; set; }
}

public class StockTrader : IGenericObserver<StockPrice>
{
    private readonly string _traderName;
    private readonly Dictionary<string, decimal> _watchedStocks = new();
    
    public StockTrader(string traderName)
    {
        _traderName = traderName;
    }
    
    public void WatchStock(string symbol, decimal targetPrice)
    {
        _watchedStocks[symbol] = targetPrice;
        Console.WriteLine($"{_traderName} is watching {symbol} at target price ${targetPrice}");
    }
    
    public void Update(StockPrice data)
    {
        if (_watchedStocks.TryGetValue(data.Symbol, out var targetPrice))
        {
            if (data.Price <= targetPrice)
            {
                Console.WriteLine($"{_traderName}: BUY {data.Symbol} at ${data.Price} (target: ${targetPrice})");
            }
            else
            {
                Console.WriteLine($"{_traderName}: {data.Symbol} is ${data.Price} (waiting for ${targetPrice})");
            }
        }
    }
}

// 3. Event-based Observer Pattern
public class NewsPublisher
{
    public event Action<string> BreakingNews;
    public event Action<string, string> SportsNews;
    public event Action<string, string> WeatherNews;
    
    public void PublishBreakingNews(string news)
    {
        Console.WriteLine($"Publishing breaking news: {news}");
        BreakingNews?.Invoke(news);
    }
    
    public void PublishSportsNews(string category, string news)
    {
        Console.WriteLine($"Publishing sports news ({category}): {news}");
        SportsNews?.Invoke(category, news);
    }
    
    public void PublishWeatherNews(string location, string forecast)
    {
        Console.WriteLine($"Publishing weather news ({location}): {forecast}");
        WeatherNews?.Invoke(location, forecast);
    }
}

public class NewsSubscriber
{
    private readonly string _subscriberName;
    
    public NewsSubscriber(string subscriberName, NewsPublisher publisher)
    {
        _subscriberName = subscriberName;
        
        // Subscribe to specific events
        publisher.BreakingNews += OnBreakingNews;
        publisher.SportsNews += OnSportsNews;
        publisher.WeatherNews += OnWeatherNews;
    }
    
    private void OnBreakingNews(string news)
    {
        Console.WriteLine($"{_subscriberName} - BREAKING: {news}");
    }
    
    private void OnSportsNews(string category, string news)
    {
        Console.WriteLine($"{_subscriberName} - SPORTS ({category}): {news}");
    }
    
    private void OnWeatherNews(string location, string forecast)
    {
        Console.WriteLine($"{_subscriberName} - WEATHER ({location}): {forecast}");
    }
}

// 4. Observer Pattern with Subject State
public interface ISubject<T>
{
    T State { get; }
    void SetState(T state);
    void Attach(IObserver<T> observer);
    void Detach(IObserver<T> observer);
}

public interface IObserver<T>
{
    void Update(ISubject<T> subject);
}

public class EmailNotificationSystem : ISubject<string>
{
    private readonly List<IObserver<string>> _observers = new();
    private string _state;
    
    public string State => _state;
    
    public void SetState(string state)
    {
        _state = state;
        Console.WriteLine($"Email system state changed to: {state}");
        NotifyObservers();
    }
    
    public void Attach(IObserver<string> observer)
    {
        _observers.Add(observer);
    }
    
    public void Detach(IObserver<string> observer)
    {
        _observers.Remove(observer);
    }
    
    private void NotifyObservers()
    {
        foreach (var observer in _observers)
        {
            observer.Update(this);
        }
    }
}

public class EmailClient : IObserver<string>
{
    private readonly string _clientName;
    
    public EmailClient(string clientName)
    {
        _clientName = clientName;
    }
    
    public void Update(ISubject<string> subject)
    {
        Console.WriteLine($"{_clientName} received notification: {subject.State}");
        
        // React based on state
        switch (subject.State.ToLower())
        {
            case "new_email":
                Console.WriteLine($"{_clientName}: You have new email!");
                break;
            case "email_sent":
                Console.WriteLine($"{_clientName}: Email sent successfully");
                break;
            case "email_failed":
                Console.WriteLine($"{_clientName}: Email delivery failed");
                break;
        }
    }
}

// 5. Observer Pattern with Weak References (to prevent memory leaks)
public class WeakObservable<T>
{
    private readonly List<WeakReference<IObserver<T>>> _observers = new();
    
    public void RegisterObserver(IObserver<T> observer)
    {
        _observers.Add(new WeakReference<IObserver<T>>(observer));
    }
    
    public void UnregisterObserver(IObserver<T> observer)
    {
        for (int i = _observers.Count - 1; i >= 0; i--)
        {
            if (_observers[i].TryGetTarget(out var target) && ReferenceEquals(target, observer))
            {
                _observers.RemoveAt(i);
                break;
            }
        }
    }
    
    public void NotifyObservers(T data)
    {
        for (int i = _observers.Count - 1; i >= 0; i--)
        {
            if (_observers[i].TryGetTarget(out var observer))
            {
                observer.Update(data);
            }
            else
            {
                // Remove dead weak references
                _observers.RemoveAt(i);
            }
        }
    }
}

// 6. Observer Pattern with Filtering
public class FilteredObservable<T>
{
    private readonly Dictionary<Func<T, bool>, List<IObserver<T>>> _filteredObservers = new();
    
    public void RegisterObserver(IObserver<T> observer, Func<T, bool> filter)
    {
        if (!_filteredObservers.ContainsKey(filter))
        {
            _filteredObservers[filter] = new List<IObserver<T>>();
        }
        _filteredObservers[filter].Add(observer);
    }
    
    public void NotifyObservers(T data)
    {
        foreach (var kvp in _filteredObservers)
        {
            if (kvp.Key(data))
            {
                foreach (var observer in kvp.Value)
                {
                    observer.Update(data);
                }
            }
        }
    }
}

// Demonstration program
public class ObserverPatternDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Observer Pattern Demonstration ===\n");
        
        // Basic Observer Pattern
        Console.WriteLine("--- Basic Observer Pattern ---");
        var weatherStation = new WeatherStation();
        
        var display1 = new WeatherDisplay("Display 1");
        var display2 = new WeatherDisplay("Display 2");
        var display3 = new WeatherDisplay("Display 3");
        
        weatherStation.RegisterObserver(display1);
        weatherStation.RegisterObserver(display2);
        
        Console.WriteLine("\nUpdating weather data:");
        weatherStation.Temperature = 25.5f;
        weatherStation.Humidity = 60.0f;
        
        Console.WriteLine("\nAdding third observer:");
        weatherStation.RegisterObserver(display3);
        weatherStation.Pressure = 1013.25f;
        
        Console.WriteLine("\nRemoving second observer:");
        weatherStation.UnregisterObserver(display2);
        weatherStation.Temperature = 26.0f;
        
        // Generic Observer Pattern
        Console.WriteLine("\n--- Generic Observer Pattern ---");
        var stockMarket = new StockMarket();
        
        var trader1 = new StockTrader("Alice");
        var trader2 = new StockTrader("Bob");
        
        trader1.WatchStock("AAPL", 150m);
        trader1.WatchStock("GOOGL", 2500m);
        trader2.WatchStock("AAPL", 140m);
        
        stockMarket.DataChanged += trader1.Update;
        stockMarket.DataChanged += trader2.Update;
        
        Console.WriteLine("\nStock price updates:");
        stockMarket.UpdateStockPrice("AAPL", 155m);
        stockMarket.UpdateStockPrice("GOOGL", 2600m);
        stockMarket.UpdateStockPrice("AAPL", 135m);
        
        // Event-based Observer Pattern
        Console.WriteLine("\n--- Event-based Observer Pattern ---");
        var newsPublisher = new NewsPublisher();
        
        var subscriber1 = new NewsSubscriber("John", newsPublisher);
        var subscriber2 = new NewsSubscriber("Jane", newsPublisher);
        
        Console.WriteLine("\nPublishing news:");
        newsPublisher.PublishBreakingNews("Major earthquake strikes city");
        newsPublisher.PublishSportsNews("Football", "Local team wins championship");
        newsPublisher.PublishWeatherNews("New York", "Sunny with high of 75°F");
        
        // Observer Pattern with Subject State
        Console.WriteLine("\n--- Observer Pattern with Subject State ---");
        var emailSystem = new EmailNotificationSystem();
        
        var client1 = new EmailClient("Outlook");
        var client2 = new EmailClient("Gmail");
        var client3 = new EmailClient("Thunderbird");
        
        emailSystem.Attach(client1);
        emailSystem.Attach(client2);
        
        Console.WriteLine("\nEmail system events:");
        emailSystem.SetState("new_email");
        emailSystem.SetState("email_sent");
        
        Console.WriteLine("\nAdding third client:");
        emailSystem.Attach(client3);
        emailSystem.SetState("new_email");
        
        // Weak Reference Observer (demonstration)
        Console.WriteLine("\n--- Weak Reference Observer ---");
        var weakObservable = new WeakObservable<string>();
        
        var tempObserver = new WeatherDisplay("Temporary");
        weakObservable.RegisterObserver(tempObserver);
        
        Console.WriteLine("Before GC:");
        weakObservable.NotifyObservers("Test message 1");
        
        // Remove reference to allow GC
        tempObserver = null;
        GC.Collect();
        GC.WaitForPendingFinalizers();
        
        Console.WriteLine("After GC:");
        weakObservable.NotifyObservers("Test message 2");
        
        // Filtered Observer
        Console.WriteLine("\n--- Filtered Observer ---");
        var filteredObservable = new FilteredObservable<int>();
        
        var evenObserver = new WeatherDisplay("Even Numbers");
        var oddObserver = new WeatherDisplay("Odd Numbers");
        var largeObserver = new WeatherDisplay("Large Numbers");
        
        filteredObservable.RegisterObserver(evenObserver, x => x % 2 == 0);
        filteredObservable.RegisterObserver(oddObserver, x => x % 2 == 1);
        filteredObservable.RegisterObserver(largeObserver, x => x > 50);
        
        Console.WriteLine("\nFiltered notifications:");
        filteredObservable.NotifyObservers(10);  // Even, not large
        filteredObservable.NotifyObservers(15);  // Odd, not large
        filteredObservable.NotifyObservers(60);  // Even and large
        filteredObservable.NotifyObservers(55);  // Odd and large
        
        Console.WriteLine("\n=== Observer Pattern Demo Complete ===");
    }
}

// Observer Pattern Usage Guidelines
/*
When to use Observer Pattern:
1. When you need to maintain consistency between related objects
2. When one object should notify multiple other objects about state changes
3. When you want to decouple the sender of a notification from its receivers
4. When you need to support broadcast communication
5. When you want to add or remove observers at runtime

Common use cases:
- GUI event handling
- Model-View-Controller (MVC) architecture
- Stock market monitoring
- Weather monitoring systems
- News feeds
- Notification systems
- Database change notifications

Advantages:
- Loose coupling between subject and observers
- Dynamic relationships between objects
- Support for broadcast communication
- Easy to add new observers
- Separation of concerns

Disadvantages:
- Unexpected updates if observers are not careful
- Memory leaks if observers are not properly unregistered
- Debugging can be difficult due to indirect calls
- Performance overhead with many observers
- Update order can be unpredictable

Best Practices:
- Use weak references to prevent memory leaks
- Provide clear documentation about when notifications occur
- Consider thread safety in multi-threaded environments
- Filter notifications when appropriate
- Avoid circular dependencies between observers
- Use events for simple observer scenarios in .NET
- Consider using Reactive Extensions (Rx) for complex scenarios
*/
