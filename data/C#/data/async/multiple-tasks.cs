using System;
using System.Threading.Tasks;

public class MultipleTasks
{
    public static async Task Main(string[] args)
    {
        var task1 = DoWorkAsync("Task 1", 1000);
        var task2 = DoWorkAsync("Task 2", 1500);
        var task3 = DoWorkAsync("Task 3", 800);
        
        await Task.WhenAll(task1, task2, task3);
        
        Console.WriteLine("All tasks completed!");
    }
    
    public static async Task DoWorkAsync(string taskName, int delay)
    {
        Console.WriteLine($"{taskName} started");
        await Task.Delay(delay);
        Console.WriteLine($"{taskName} completed");
    }
}
