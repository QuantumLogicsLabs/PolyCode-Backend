using System;
using System.Collections.Generic;

// Strategy Pattern Examples

// 1. Basic Strategy Pattern - Payment Processing
public interface IPaymentStrategy
{
    string ProcessPayment(decimal amount);
    bool ValidatePaymentDetails();
}

public class CreditCardPayment : IPaymentStrategy
{
    public string CardNumber { get; set; }
    public string ExpiryDate { get; set; }
    public string CVV { get; set; }
    
    public string ProcessPayment(decimal amount)
    {
        if (!ValidatePaymentDetails())
            return "Invalid credit card details";
        
        // Simulate credit card processing
        Console.WriteLine($"Processing credit card payment of ${amount}");
        Console.WriteLine($"Card ending in {CardNumber.Substring(CardNumber.Length - 4)}");
        return $"Credit card payment of ${amount} processed successfully";
    }
    
    public bool ValidatePaymentDetails()
    {
        return !string.IsNullOrEmpty(CardNumber) && 
               !string.IsNullOrEmpty(ExpiryDate) && 
               !string.IsNullOrEmpty(CVV);
    }
}

public class PayPalPayment : IPaymentStrategy
{
    public string Email { get; set; }
    public string Password { get; set; }
    
    public string ProcessPayment(decimal amount)
    {
        if (!ValidatePaymentDetails())
            return "Invalid PayPal credentials";
        
        // Simulate PayPal processing
        Console.WriteLine($"Processing PayPal payment of ${amount}");
        Console.WriteLine($"PayPal account: {Email}");
        return $"PayPal payment of ${amount} processed successfully";
    }
    
    public bool ValidatePaymentDetails()
    {
        return !string.IsNullOrEmpty(Email) && 
               !string.IsNullOrEmpty(Password);
    }
}

public class BankTransferPayment : IPaymentStrategy
{
    public string AccountNumber { get; set; }
    public string RoutingNumber { get; set; }
    public string BankName { get; set; }
    
    public string ProcessPayment(decimal amount)
    {
        if (!ValidatePaymentDetails())
            return "Invalid bank transfer details";
        
        // Simulate bank transfer processing
        Console.WriteLine($"Processing bank transfer of ${amount}");
        Console.WriteLine($"From account {AccountNumber} at {BankName}");
        return $"Bank transfer of ${amount} processed successfully";
    }
    
    public bool ValidatePaymentDetails()
    {
        return !string.IsNullOrEmpty(AccountNumber) && 
               !string.IsNullOrEmpty(RoutingNumber) && 
               !string.IsNullOrEmpty(BankName);
    }
}

public class PaymentContext
{
    private IPaymentStrategy _paymentStrategy;
    
    public void SetPaymentStrategy(IPaymentStrategy paymentStrategy)
    {
        _paymentStrategy = paymentStrategy;
    }
    
    public string ExecutePayment(decimal amount)
    {
        if (_paymentStrategy == null)
            throw new InvalidOperationException("Payment strategy not set");
        
        return _paymentStrategy.ProcessPayment(amount);
    }
}

// 2. Strategy Pattern - Data Compression
public interface ICompressionStrategy
{
    byte[] Compress(byte[] data);
    byte[] Decompress(byte[] compressedData);
    string GetAlgorithmName();
}

public class ZipCompression : ICompressionStrategy
{
    public byte[] Compress(byte[] data)
    {
        Console.WriteLine("Compressing data using ZIP algorithm");
        // Simulate compression
        var compressed = new byte[data.Length / 2];
        Array.Copy(data, compressed, Math.Min(data.Length, compressed.Length));
        return compressed;
    }
    
    public byte[] Decompress(byte[] compressedData)
    {
        Console.WriteLine("Decompressing data using ZIP algorithm");
        // Simulate decompression
        var decompressed = new byte[compressedData.Length * 2];
        Array.Copy(compressedData, decompressed, compressedData.Length);
        return decompressed;
    }
    
    public string GetAlgorithmName()
    {
        return "ZIP";
    }
}

public class GzipCompression : ICompressionStrategy
{
    public byte[] Compress(byte[] data)
    {
        Console.WriteLine("Compressing data using GZIP algorithm");
        // Simulate compression
        var compressed = new byte[data.Length / 3];
        Array.Copy(data, compressed, Math.Min(data.Length, compressed.Length));
        return compressed;
    }
    
    public byte[] Decompress(byte[] compressedData)
    {
        Console.WriteLine("Decompressing data using GZIP algorithm");
        // Simulate decompression
        var decompressed = new byte[compressedData.Length * 3];
        Array.Copy(compressedData, decompressed, compressedData.Length);
        return decompressed;
    }
    
    public string GetAlgorithmName()
    {
        return "GZIP";
    }
}

public class RarCompression : ICompressionStrategy
{
    public byte[] Compress(byte[] data)
    {
        Console.WriteLine("Compressing data using RAR algorithm");
        // Simulate compression
        var compressed = new byte[data.Length / 4];
        Array.Copy(data, compressed, Math.Min(data.Length, compressed.Length));
        return compressed;
    }
    
    public byte[] Decompress(byte[] compressedData)
    {
        Console.WriteLine("Decompressing data using RAR algorithm");
        // Simulate decompression
        var decompressed = new byte[compressedData.Length * 4];
        Array.Copy(compressedData, decompressed, compressedData.Length);
        return decompressed;
    }
    
    public string GetAlgorithmName()
    {
        return "RAR";
    }
}

public class DataCompressor
{
    private ICompressionStrategy _compressionStrategy;
    
    public DataCompressor(ICompressionStrategy compressionStrategy)
    {
        _compressionStrategy = compressionStrategy;
    }
    
    public void SetCompressionStrategy(ICompressionStrategy compressionStrategy)
    {
        _compressionStrategy = compressionStrategy;
    }
    
    public byte[] CompressData(byte[] data)
    {
        Console.WriteLine($"Using {_compressionStrategy.GetAlgorithmName()} compression");
        return _compressionStrategy.Compress(data);
    }
    
    public byte[] DecompressData(byte[] compressedData)
    {
        Console.WriteLine($"Using {_compressionStrategy.GetAlgorithmName()} decompression");
        return _compressionStrategy.Decompress(compressedData);
    }
}

// 3. Strategy Pattern - Sorting Algorithms
public interface ISortStrategy<T>
{
    void Sort(T[] items);
    string GetAlgorithmName();
}

public class BubbleSort<T> : ISortStrategy<T> where T : IComparable<T>
{
    public void Sort(T[] items)
    {
        Console.WriteLine($"Sorting {items.Length} items using Bubble Sort");
        
        int n = items.Length;
        for (int i = 0; i < n - 1; i++)
        {
            for (int j = 0; j < n - i - 1; j++)
            {
                if (items[j].CompareTo(items[j + 1]) > 0)
                {
                    // Swap
                    T temp = items[j];
                    items[j] = items[j + 1];
                    items[j + 1] = temp;
                }
            }
        }
        
        Console.WriteLine("Bubble Sort completed");
    }
    
    public string GetAlgorithmName()
    {
        return "Bubble Sort";
    }
}

public class QuickSort<T> : ISortStrategy<T> where T : IComparable<T>
{
    public void Sort(T[] items)
    {
        Console.WriteLine($"Sorting {items.Length} items using Quick Sort");
        QuickSortRecursive(items, 0, items.Length - 1);
        Console.WriteLine("Quick Sort completed");
    }
    
    private void QuickSortRecursive(T[] items, int low, int high)
    {
        if (low < high)
        {
            int partitionIndex = Partition(items, low, high);
            QuickSortRecursive(items, low, partitionIndex - 1);
            QuickSortRecursive(items, partitionIndex + 1, high);
        }
    }
    
    private int Partition(T[] items, int low, int high)
    {
        T pivot = items[high];
        int i = low - 1;
        
        for (int j = low; j < high; j++)
        {
            if (items[j].CompareTo(pivot) <= 0)
            {
                i++;
                T temp = items[i];
                items[i] = items[j];
                items[j] = temp;
            }
        }
        
        T temp2 = items[i + 1];
        items[i + 1] = items[high];
        items[high] = temp2;
        
        return i + 1;
    }
    
    public string GetAlgorithmName()
    {
        return "Quick Sort";
    }
}

public class MergeSort<T> : ISortStrategy<T> where T : IComparable<T>
{
    public void Sort(T[] items)
    {
        Console.WriteLine($"Sorting {items.Length} items using Merge Sort");
        MergeSortRecursive(items, 0, items.Length - 1);
        Console.WriteLine("Merge Sort completed");
    }
    
    private void MergeSortRecursive(T[] items, int left, int right)
    {
        if (left < right)
        {
            int middle = left + (right - left) / 2;
            MergeSortRecursive(items, left, middle);
            MergeSortRecursive(items, middle + 1, right);
            Merge(items, left, middle, right);
        }
    }
    
    private void Merge(T[] items, int left, int middle, int right)
    {
        int n1 = middle - left + 1;
        int n2 = right - middle;
        
        T[] leftArray = new T[n1];
        T[] rightArray = new T[n2];
        
        Array.Copy(items, left, leftArray, 0, n1);
        Array.Copy(items, middle + 1, rightArray, 0, n2);
        
        int i = 0, j = 0, k = left;
        
        while (i < n1 && j < n2)
        {
            if (leftArray[i].CompareTo(rightArray[j]) <= 0)
            {
                items[k] = leftArray[i];
                i++;
            }
            else
            {
                items[k] = rightArray[j];
                j++;
            }
            k++;
        }
        
        while (i < n1)
        {
            items[k] = leftArray[i];
            i++;
            k++;
        }
        
        while (j < n2)
        {
            items[k] = rightArray[j];
            j++;
            k++;
        }
    }
    
    public string GetAlgorithmName()
    {
        return "Merge Sort";
    }
}

public class Sorter<T> where T : IComparable<T>
{
    private ISortStrategy<T> _sortStrategy;
    
    public Sorter(ISortStrategy<T> sortStrategy)
    {
        _sortStrategy = sortStrategy;
    }
    
    public void SetSortStrategy(ISortStrategy<T> sortStrategy)
    {
        _sortStrategy = sortStrategy;
    }
    
    public void Sort(T[] items)
    {
        if (_sortStrategy == null)
            throw new InvalidOperationException("Sort strategy not set");
        
        Console.WriteLine($"Original array: [{string.Join(", ", items)}]");
        _sortStrategy.Sort(items);
        Console.WriteLine($"Sorted array: [{string.Join(", ", items)}]");
    }
}

// 4. Strategy Pattern - Navigation Routes
public interface INavigationStrategy
{
    string CalculateRoute(string from, string to);
    int GetEstimatedTime(string from, string to);
    decimal GetDistance(string from, string to);
}

public class CarNavigation : INavigationStrategy
{
    public string CalculateRoute(string from, string to)
    {
        Console.WriteLine("Calculating car route...");
        return $"Car route from {from} to {to}: Take Highway 101, then Exit 25";
    }
    
    public int GetEstimatedTime(string from, string to)
    {
        return 45; // minutes
    }
    
    public decimal GetDistance(string from, string to)
    {
        return 35.5m; // miles
    }
}

public class WalkingNavigation : INavigationStrategy
{
    public string CalculateRoute(string from, string to)
    {
        Console.WriteLine("Calculating walking route...");
        return $"Walking route from {from} to {to}: Take Main Street, then Oak Avenue";
    }
    
    public int GetEstimatedTime(string from, string to)
    {
        return 120; // minutes
    }
    
    public decimal GetDistance(string from, string to)
    {
        return 2.8m; // miles
    }
}

public class PublicTransportNavigation : INavigationStrategy
{
    public string CalculateRoute(string from, string to)
    {
        Console.WriteLine("Calculating public transport route...");
        return $"Public transport route from {from} to {to}: Take Bus 42 to Central Station, then Train Line 3";
    }
    
    public int GetEstimatedTime(string from, string to)
    {
        return 60; // minutes
    }
    
    public decimal GetDistance(string from, string to)
    {
        return 28.0m; // miles
    }
}

public class NavigationSystem
{
    private INavigationStrategy _navigationStrategy;
    
    public void SetNavigationStrategy(INavigationStrategy navigationStrategy)
    {
        _navigationStrategy = navigationStrategy;
    }
    
    public void ShowDirections(string from, string to)
    {
        if (_navigationStrategy == null)
            throw new InvalidOperationException("Navigation strategy not set");
        
        var route = _navigationStrategy.CalculateRoute(from, to);
        var time = _navigationStrategy.GetEstimatedTime(from, to);
        var distance = _navigationStrategy.GetDistance(from, to);
        
        Console.WriteLine($"\nNavigation Instructions:");
        Console.WriteLine($"Route: {route}");
        Console.WriteLine($"Estimated time: {time} minutes");
        Console.WriteLine($"Distance: {distance} miles");
    }
}

// Demonstration program
public class StrategyPatternDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Strategy Pattern Demonstration ===\n");
        
        // Payment Processing Strategy
        Console.WriteLine("--- Payment Processing Strategy ---");
        var paymentContext = new PaymentContext();
        
        // Credit Card Payment
        var creditCard = new CreditCardPayment 
        { 
            CardNumber = "4111111111111111", 
            ExpiryDate = "12/25", 
            CVV = "123" 
        };
        paymentContext.SetPaymentStrategy(creditCard);
        Console.WriteLine(paymentContext.ExecutePayment(100.00m));
        
        // PayPal Payment
        var payPal = new PayPalPayment 
        { 
            Email = "user@example.com", 
            Password = "password123" 
        };
        paymentContext.SetPaymentStrategy(payPal);
        Console.WriteLine(paymentContext.ExecutePayment(50.00m));
        
        // Bank Transfer
        var bankTransfer = new BankTransferPayment 
        { 
            AccountNumber = "123456789", 
            RoutingNumber = "987654321", 
            BankName = "First National Bank" 
        };
        paymentContext.SetPaymentStrategy(bankTransfer);
        Console.WriteLine(paymentContext.ExecutePayment(200.00m));
        
        // Data Compression Strategy
        Console.WriteLine("\n--- Data Compression Strategy ---");
        var data = new byte[] { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };
        
        var compressor = new DataCompressor(new ZipCompression());
        var compressed = compressor.CompressData(data);
        var decompressed = compressor.DecompressData(compressed);
        
        compressor.SetCompressionStrategy(new GzipCompression());
        compressed = compressor.CompressData(data);
        decompressed = compressor.DecompressData(compressed);
        
        // Sorting Strategy
        Console.WriteLine("\n--- Sorting Strategy ---");
        var numbers = new int[] { 64, 34, 25, 12, 22, 11, 90 };
        var sorter = new Sorter<int>(new BubbleSort<int>());
        sorter.Sort(numbers);
        
        numbers = new int[] { 64, 34, 25, 12, 22, 11, 90 };
        sorter.SetSortStrategy(new QuickSort<int>());
        sorter.Sort(numbers);
        
        numbers = new int[] { 64, 34, 25, 12, 22, 11, 90 };
        sorter.SetSortStrategy(new MergeSort<int>());
        sorter.Sort(numbers);
        
        // Navigation Strategy
        Console.WriteLine("\n--- Navigation Strategy ---");
        var navigation = new NavigationSystem();
        
        navigation.SetNavigationStrategy(new CarNavigation());
        navigation.ShowDirections("Home", "Work");
        
        navigation.SetNavigationStrategy(new WalkingNavigation());
        navigation.ShowDirections("Home", "Work");
        
        navigation.SetNavigationStrategy(new PublicTransportNavigation());
        navigation.ShowDirections("Home", "Work");
        
        Console.WriteLine("\n=== Strategy Pattern Demo Complete ===");
    }
}

// Strategy Pattern Usage Guidelines
/*
When to use Strategy Pattern:
1. When you have multiple ways to do something and want to choose between them at runtime
2. When you want to avoid multiple conditional statements in your code
3. When you want to make algorithms interchangeable
4. When you want to add new algorithms without modifying existing code
5. When you have complex algorithms that you want to separate from the context that uses them

Common use cases:
- Payment processing systems
- Data compression algorithms
- Sorting algorithms
- Navigation systems
- Authentication methods
- Validation strategies
- Rendering strategies
- Caching strategies

Advantages:
- Open/Closed Principle: Easy to add new strategies
- Single Responsibility: Each strategy has one responsibility
- Runtime flexibility: Can change strategies at runtime
- Testability: Each strategy can be tested independently
- Code reuse: Strategies can be reused in different contexts

Disadvantages:
- Increased number of objects
- Strategy selection logic can be complex
- Clients must be aware of different strategies
- Overhead for simple algorithms
- May lead to many small classes

Best Practices:
- Keep strategies focused on a single algorithm
- Use dependency injection to provide strategies
- Consider using enums or configuration for strategy selection
- Provide default strategies when appropriate
- Document the trade-offs of each strategy
- Use interfaces or abstract classes for strategy contracts
- Consider the Strategy pattern when you have conditional logic that switches between algorithms
*/
