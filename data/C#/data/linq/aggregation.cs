using System;
using System.Collections.Generic;
using System.Linq;

public class AggregationExample
{
    public static void Main(string[] args)
    {
        var products = new List<Product>
        {
            new Product { Name = "Laptop", Price = 1200, Category = "Electronics" },
            new Product { Name = "Mouse", Price = 25, Category = "Electronics" },
            new Product { Name = "Book", Price = 15, Category = "Education" },
            new Product { Name = "Pen", Price = 2, Category = "Education" },
            new Product { Name = "Desk", Price = 200, Category = "Furniture" }
        };
        
        // Basic aggregations
        var totalProducts = products.Count();
        var totalPrice = products.Sum(p => p.Price);
        var averagePrice = products.Average(p => p.Price);
        var maxPrice = products.Max(p => p.Price);
        var minPrice = products.Min(p => p.Price);
        
        Console.WriteLine($"Total Products: {totalProducts}");
        Console.WriteLine($"Total Price: ${totalPrice}");
        Console.WriteLine($"Average Price: ${averagePrice:F2}");
        Console.WriteLine($"Max Price: ${maxPrice}");
        Console.WriteLine($"Min Price: ${minPrice}");
        
        // Group by and aggregate
        var categoryStats = products.GroupBy(p => p.Category)
            .Select(g => new
            {
                Category = g.Key,
                Count = g.Count(),
                TotalPrice = g.Sum(p => p.Price),
                AveragePrice = g.Average(p => p.Price)
            });
        
        Console.WriteLine("\nCategory Statistics:");
        foreach (var stat in categoryStats)
        {
            Console.WriteLine($"{stat.Category}: {stat.Count} products, " +
                            $"Total: ${stat.TotalPrice}, Avg: ${stat.AveragePrice:F2}");
        }
    }
}

public class Product
{
    public string Name { get; set; }
    public decimal Price { get; set; }
    public string Category { get; set; }
}
