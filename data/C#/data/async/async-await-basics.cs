using System;
using System.Threading.Tasks;

public class AsyncAwaitBasics
{
    public static async Task Main(string[] args)
    {
        Console.WriteLine("Starting async operation...");
        
        string result = await GetDataAsync();
        
        Console.WriteLine($"Result: {result}");
        Console.WriteLine("Async operation completed.");
    }
    
    public static async Task<string> GetDataAsync()
    {
        await Task.Delay(2000); // Simulate async work
        return "Data retrieved asynchronously";
    }
}
