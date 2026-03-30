using System;
using System.Collections.Concurrent;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

// Parallel LINQ (PLINQ) Examples

public class ParallelLINQ
{
    // 1. Basic PLINQ query
    public static void BasicPLINQ()
    {
        Console.WriteLine("=== Basic PLINQ Query ===");
        
        var numbers = Enumerable.Range(1, 100).ToList();
        
        // Sequential LINQ
        var sequentialResult = numbers
            .Where(n => n % 2 == 0)
            .Select(n => n * n)
            .Take(5);
        
        Console.WriteLine("Sequential result:");
        foreach (var item in sequentialResult)
        {
            Console.WriteLine(item);
        }
        
        // Parallel LINQ
        var parallelResult = numbers
            .AsParallel()
            .Where(n => n % 2 == 0)
            .Select(n => n * n)
            .Take(5);
        
        Console.WriteLine("\nParallel result:");
        foreach (var item in parallelResult)
        {
            Console.WriteLine(item);
        }
        
        Console.WriteLine();
    }
    
    // 2. Performance comparison
    public static void PerformanceComparison()
    {
        Console.WriteLine("=== Performance Comparison ===");
        
        var numbers = Enumerable.Range(1, 1000000).ToList();
        
        // Sequential processing
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        var sequentialSum = numbers
            .Where(n => n % 2 == 0)
            .Select(n => n * n)
            .Sum();
        stopwatch.Stop();
        
        Console.WriteLine($"Sequential: Sum = {sequentialSum}, Time = {stopwatch.ElapsedMilliseconds}ms");
        
        // Parallel processing
        stopwatch.Restart();
        var parallelSum = numbers
            .AsParallel()
            .Where(n => n % 2 == 0)
            .Select(n => n * n)
            .Sum();
        stopwatch.Stop();
        
        Console.WriteLine($"Parallel: Sum = {parallelSum}, Time = {stopwatch.ElapsedMilliseconds}ms");
        
        Console.WriteLine();
    }
    
    // 3. PLINQ with degree of parallelism
    public static void DegreeOfParallelism()
    {
        Console.WriteLine("=== Degree of Parallelism ===");
        
        var numbers = Enumerable.Range(1, 100).ToList();
        
        // Use specific degree of parallelism
        var result = numbers
            .AsParallel()
            .WithDegreeOfParallelism(2)
            .Select(n => {
                Console.WriteLine($"Processing {n} on thread {System.Threading.Thread.CurrentThread.ManagedThreadId}");
                return n * n;
            })
            .ToList();
        
        Console.WriteLine($"Processed {result.Count} numbers\n");
    }
    
    // 4. Ordering in PLINQ
    public static void OrderingInPLINQ()
    {
        Console.WriteLine("=== Ordering in PLINQ ===");
        
        var numbers = Enumerable.Range(1, 20).ToList();
        
        // Without preserving order
        Console.WriteLine("Without preserving order:");
        var unorderedResult = numbers
            .AsParallel()
            .Select(n => {
                Console.WriteLine($"Processing {n}");
                return n * n;
            })
            .ToList();
        
        // With preserving order
        Console.WriteLine("\nWith preserving order:");
        var orderedResult = numbers
            .AsParallel()
            .AsOrdered()
            .Select(n => {
                Console.WriteLine($"Processing {n}");
                return n * n;
            })
            .ToList();
        
        Console.WriteLine();
    }
    
    // 5. PLINQ merge options
    public static void MergeOptions()
    {
        Console.WriteLine("=== PLINQ Merge Options ===");
        
        var numbers = Enumerable.Range(1, 100).ToList();
        
        // Different merge options
        var options = new[]
        {
            System.Linq.ParallelMergeOptions.NotBuffered,
            System.Linq.ParallelMergeOptions.AutoBuffered,
            System.Linq.ParallelMergeOptions.FullyBuffered
        };
        
        foreach (var option in options)
        {
            Console.WriteLine($"Merge option: {option}");
            var result = numbers
                .AsParallel()
                .WithMergeOptions(option)
                .Select(n => {
                    Console.WriteLine($"  Processing {n}");
                    return n;
                })
                .ToList();
            
            Console.WriteLine();
        }
    }
    
    // 6. Exception handling in PLINQ
    public static void ExceptionHandling()
    {
        Console.WriteLine("=== Exception Handling in PLINQ ===");
        
        var numbers = Enumerable.Range(1, 10).ToList();
        
        try
        {
            var result = numbers
                .AsParallel()
                .Select(n => {
                    if (n == 5)
                        throw new InvalidOperationException($"Error processing {n}");
                    return n * n;
                })
                .ToList();
        }
        catch (AggregateException ex)
        {
            Console.WriteLine($"Caught {ex.InnerExceptions.Count} exceptions:");
            foreach (var innerEx in ex.InnerExceptions)
            {
                Console.WriteLine($"  {innerEx.Message}");
            }
        }
        
        Console.WriteLine();
    }
    
    // 7. Cancellation in PLINQ
    public static async Task CancellationInPLINQ()
    {
        Console.WriteLine("=== Cancellation in PLINQ ===");
        
        var numbers = Enumerable.Range(1, 1000000).ToList();
        var cts = new System.Threading.CancellationTokenSource();
        
        // Cancel after 100ms
        _ = Task.Delay(100).ContinueWith(_ => cts.Cancel());
        
        try
        {
            var result = numbers
                .AsParallel()
                .WithCancellation(cts.Token)
                .Select(n => {
                    System.Threading.Thread.Sleep(1);
                    return n * n;
                })
                .ToList();
            
            Console.WriteLine($"Completed processing {result.Count} numbers");
        }
        catch (OperationCanceledException)
        {
            Console.WriteLine("Operation was cancelled");
        }
        
        Console.WriteLine();
    }
    
    // 8. PLINQ with custom partitioner
    public static void CustomPartitioner()
    {
        Console.WriteLine("=== Custom Partitioner ===");
        
        var numbers = Enumerable.Range(1, 100).ToList();
        
        // Create custom partitioner
        var partitioner = System.Collections.Concurrent.Partitioner.Create(numbers, loadBalance: true);
        
        var result = partitioner
            .AsParallel()
            .SelectMany(partition => partition)
            .Select(n => {
                Console.WriteLine($"Processing {n} on thread {System.Threading.Thread.CurrentThread.ManagedThreadId}");
                return n * n;
            })
            .ToList();
        
        Console.WriteLine($"Processed {result.Count} numbers\n");
    }
    
    // 9. PLINQ for-each style operations
    public static void ForAllOperations()
    {
        Console.WriteLine("=== ForAll Operations ===");
        
        var numbers = Enumerable.Range(1, 20).ToList();
        
        Console.WriteLine("Using ForAll:");
        numbers
            .AsParallel()
            .ForAll(n => {
                Console.WriteLine($"Processing {n} on thread {System.Threading.Thread.CurrentThread.ManagedThreadId}");
            });
        
        Console.WriteLine();
    }
    
    // 10. PLINQ aggregation
    public static void PLINQAggregation()
    {
        Console.WriteLine("=== PLINQ Aggregation ===");
        
        var numbers = Enumerable.Range(1, 100).ToList();
        
        // Custom aggregation
        var sum = numbers
            .AsParallel()
            .Aggregate(
                seed: 0,
                (partialSum, n) => partialSum + n,
                (totalSum, partialSum) => totalSum + partialSum,
                finalResult => finalResult
            );
        
        Console.WriteLine($"Sum of 1 to 100: {sum}");
        
        // Count with condition
        var evenCount = numbers
            .AsParallel()
            .Where(n => n % 2 == 0)
            .Count();
        
        Console.WriteLine($"Count of even numbers: {evenCount}\n");
    }
}

// Real-world PLINQ Examples
public class RealWorldPLINQ
{
    // 1. Text processing with PLINQ
    public static void TextProcessing()
    {
        Console.WriteLine("=== Text Processing with PLINQ ===");
        
        var words = new List<string>
        {
            "apple", "banana", "cherry", "date", "elderberry",
            "fig", "grape", "honeydew", "kiwi", "lemon",
            "mango", "nectarine", "orange", "pear", "quince"
        };
        
        // Process words in parallel
        var processedWords = words
            .AsParallel()
            .Select(word => new {
                Original = word,
                UpperCase = word.ToUpper(),
                Length = word.Length,
                Reversed = new string(word.Reverse().ToArray()),
                StartsWithVowel = "AEIOUaeiou".Contains(word[0])
            })
            .ToList();
        
        Console.WriteLine("Processed words:");
        foreach (var word in processedWords)
        {
            Console.WriteLine($"{word.Original} -> {word.UpperCase} (Length: {word.Length}, Reversed: {word.Reversed}, Starts with vowel: {word.StartsWithVowel})");
        }
        
        Console.WriteLine();
    }
    
    // 2. Image processing simulation
    public static void ImageProcessing()
    {
        Console.WriteLine("=== Image Processing Simulation ===");
        
        var images = Enumerable.Range(1, 20)
            .Select(i => new { Id = i, Name = $"Image_{i}.jpg", Size = i * 1024 })
            .ToList();
        
        // Process images in parallel
        var processedImages = images
            .AsParallel()
            .Select(image => {
                // Simulate image processing
                System.Threading.Thread.Sleep(50);
                return new {
                    image.Id,
                    image.Name,
                    ProcessedSize = image.Size * 2,
                    ProcessedName = $"Processed_{image.Name}",
                    ProcessingTime = DateTime.Now
                };
            })
            .ToList();
        
        Console.WriteLine($"Processed {processedImages.Count} images:");
        foreach (var img in processedImages.Take(5))
        {
            Console.WriteLine($"{img.Name} -> {img.ProcessedName} (Size: {img.Size} -> {img.ProcessedSize})");
        }
        
        Console.WriteLine();
    }
    
    // 3. Data analysis
    public static void DataAnalysis()
    {
        Console.WriteLine("=== Data Analysis with PLINQ ===");
        
        var random = new Random();
        var data = Enumerable.Range(1, 10000)
            .Select(i => new {
                Id = i,
                Category = $"Category_{i % 10}",
                Value = random.Next(1, 1000),
                IsActive = i % 3 != 0
            })
            .ToList();
        
        // Analyze data in parallel
        var analysis = new
        {
            TotalCount = data.AsParallel().Count(),
            ActiveCount = data.AsParallel().Count(d => d.IsActive),
            AverageValue = data.AsParallel().Where(d => d.IsActive).Average(d => d.Value),
            MaxValue = data.AsParallel().Max(d => d.Value),
            MinValue = data.AsParallel().Min(d => d.Value),
            CategoryStats = data.AsParallel()
                .GroupBy(d => d.Category)
                .Select(g => new {
                    Category = g.Key,
                    Count = g.Count(),
                    Average = g.Average(x => x.Value)
                })
                .ToList()
        };
        
        Console.WriteLine($"Total records: {analysis.TotalCount}");
        Console.WriteLine($"Active records: {analysis.ActiveCount}");
        Console.WriteLine($"Average value (active): {analysis.AverageValue:F2}");
        Console.WriteLine($"Max value: {analysis.MaxValue}");
        Console.WriteLine($"Min value: {analysis.MinValue}");
        
        Console.WriteLine("\nCategory statistics:");
        foreach (var stat in analysis.CategoryStats.Take(5))
        {
            Console.WriteLine($"{stat.Category}: Count={stat.Count}, Avg={stat.Average:F2}");
        }
        
        Console.WriteLine();
    }
    
    // 4. Financial calculations
    public static void FinancialCalculations()
    {
        Console.WriteLine("=== Financial Calculations ===");
        
        var random = new Random();
        var transactions = Enumerable.Range(1, 1000)
            .Select(i => new {
                Date = DateTime.Now.AddDays(-random.Next(0, 365)),
                Amount = random.Next(-1000, 5000),
                Type = random.Next(0, 2) == 0 ? "Credit" : "Debit",
                Account = $"Account_{random.Next(1, 10)}"
            })
            .ToList();
        
        // Calculate financial metrics in parallel
        var metrics = new
        {
            TotalTransactions = transactions.AsParallel().Count(),
            TotalCredits = transactions.AsParallel().Where(t => t.Type == "Credit").Sum(t => t.Amount),
            TotalDebits = transactions.AsParallel().Where(t => t.Type == "Debit").Sum(t => Math.Abs(t.Amount)),
            NetAmount = transactions.AsParallel().Sum(t => t.Type == "Credit" ? t.Amount : -Math.Abs(t.Amount)),
            AverageTransaction = transactions.AsParallel().Average(t => Math.Abs(t.Amount)),
            AccountBalances = transactions.AsParallel()
                .GroupBy(t => t.Account)
                .Select(g => new {
                    Account = g.Key,
                    Balance = g.Sum(t => t.Type == "Credit" ? t.Amount : -Math.Abs(t.Amount))
                })
                .ToList()
        };
        
        Console.WriteLine($"Total transactions: {metrics.TotalTransactions}");
        Console.WriteLine($"Total credits: ${metrics.TotalCredits:N2}");
        Console.WriteLine($"Total debits: ${metrics.TotalDebits:N2}");
        Console.WriteLine($"Net amount: ${metrics.NetAmount:N2}");
        Console.WriteLine($"Average transaction: ${metrics.AverageTransaction:N2}");
        
        Console.WriteLine("\nAccount balances:");
        foreach (var balance in metrics.AccountBalances.Take(5))
        {
            Console.WriteLine($"{balance.Account}: ${balance.Balance:N2}");
        }
        
        Console.WriteLine();
    }
    
    // 5. Search and filtering
    public static void SearchAndFiltering()
    {
        Console.WriteLine("=== Search and Filtering ===");
        
        var products = Enumerable.Range(1, 10000)
            .Select(i => new {
                Id = i,
                Name = $"Product_{i}",
                Category = $"Category_{i % 20}",
                Price = i * 1.5m,
                InStock = i % 4 != 0,
                Rating = (i % 5) + 1
            })
            .ToList();
        
        // Search in parallel
        var searchResults = products
            .AsParallel()
            .Where(p => p.InStock && p.Price > 100 && p.Rating >= 4)
            .OrderByDescending(p => p.Rating)
            .ThenBy(p => p.Price)
            .Take(10)
            .ToList();
        
        Console.WriteLine($"Found {searchResults.Count} products matching criteria:");
        foreach (var product in searchResults)
        {
            Console.WriteLine($"{product.Name} - {product.Category} - ${product.Price:F2} - Rating: {product.Rating}");
        }
        
        Console.WriteLine();
    }
}

// PLINQ Performance Analysis
public class PLINQPerformanceAnalysis
{
    // 1. When to use PLINQ
    public static void WhenToUsePLINQ()
    {
        Console.WriteLine("=== When to Use PLINQ ===");
        
        var sizes = new[] { 1000, 10000, 100000, 1000000 };
        
        foreach (var size in sizes)
        {
            var data = Enumerable.Range(1, size).ToList();
            
            // Sequential
            var sequentialTime = MeasureTime(() => {
                var result = data
                    .Where(n => n % 2 == 0)
                    .Select(n => n * n)
                    .Sum();
            });
            
            // Parallel
            var parallelTime = MeasureTime(() => {
                var result = data
                    .AsParallel()
                    .Where(n => n % 2 == 0)
                    .Select(n => n * n)
                    .Sum();
            });
            
            double speedup = (double)sequentialTime / parallelTime;
            
            Console.WriteLine($"Size: {size,-10} Sequential: {sequentialTime,6}ms Parallel: {parallelTime,6}ms Speedup: {speedup:F2}x");
        }
        
        Console.WriteLine();
    }
    
    // 2. Memory usage analysis
    public static void MemoryUsageAnalysis()
    {
        Console.WriteLine("=== Memory Usage Analysis ===");
        
        var data = Enumerable.Range(1, 100000).ToList();
        
        // Measure memory usage
        long memoryBefore = GC.GetTotalMemory(true);
        
        var sequentialResult = data
            .Where(n => n % 2 == 0)
            .Select(n => n * n)
            .ToList();
        
        long memoryAfterSequential = GC.GetTotalMemory(true);
        
        memoryBefore = GC.GetTotalMemory(true);
        
        var parallelResult = data
            .AsParallel()
            .Where(n => n % 2 == 0)
            .Select(n => n * n)
            .ToList();
        
        long memoryAfterParallel = GC.GetTotalMemory(true);
        
        Console.WriteLine($"Sequential memory usage: {memoryAfterSequential - memoryBefore} bytes");
        Console.WriteLine($"Parallel memory usage: {memoryAfterParallel - memoryBefore} bytes");
        
        Console.WriteLine();
    }
    
    private static long MeasureTime(Action action)
    {
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        action();
        stopwatch.Stop();
        return stopwatch.ElapsedMilliseconds;
    }
}

// Main demonstration
public class PLINQDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Parallel LINQ (PLINQ) Demonstration ===");
        Console.WriteLine("PLINQ enables parallel processing of LINQ queries for improved performance.\n");
        
        // Basic PLINQ operations
        ParallelLINQ.BasicPLINQ();
        ParallelLINQ.PerformanceComparison();
        ParallelLINQ.DegreeOfParallelism();
        ParallelLINQ.OrderingInPLINQ();
        ParallelLINQ.MergeOptions();
        ParallelLINQ.ExceptionHandling();
        ParallelLINQ.CancellationInPLINQ().GetAwaiter().GetResult();
        ParallelLINQ.CustomPartitioner();
        ParallelLINQ.ForAllOperations();
        ParallelLINQ.PLINQAggregation();
        
        // Real-world examples
        RealWorldPLINQ.TextProcessing();
        RealWorldPLINQ.ImageProcessing();
        RealWorldPLINQ.DataAnalysis();
        RealWorldPLINQ.FinancialCalculations();
        RealWorldPLINQ.SearchAndFiltering();
        
        // Performance analysis
        PLINQPerformanceAnalysis.WhenToUsePLINQ();
        PLINQPerformanceAnalysis.MemoryUsageAnalysis();
        
        Console.WriteLine("=== Key PLINQ Concepts ===");
        Console.WriteLine("1. AsParallel() converts LINQ query to PLINQ");
        Console.WriteLine("2. WithDegreeOfParallelism() controls thread usage");
        Console.WriteLine("3. AsOrdered() preserves original ordering");
        Console.WriteLine("4. WithMergeOptions() controls result buffering");
        Console.WriteLine("5. WithCancellation() enables query cancellation");
        Console.WriteLine("6. ForAll() executes actions in parallel");
        Console.WriteLine("7. Aggregate() supports parallel aggregation");
        Console.WriteLine("8. Handle AggregateException for multiple errors");
        Console.WriteLine("9. Use PLINQ for CPU-bound operations on large datasets");
        Console.WriteLine("10. Avoid PLINQ for small datasets or I/O-bound operations");
        Console.WriteLine("11. Consider ordering requirements vs performance");
        Console.WriteLine("12. Test performance gains in your specific scenario");
    }
}
