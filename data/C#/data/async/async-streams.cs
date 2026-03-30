using System;
using System.Collections.Generic;
using System.Runtime.CompilerServices;
using System.Threading.Tasks;

public class AsyncStreams
{
    public static async Task Main(string[] args)
    {
        await foreach (var number in GenerateNumbersAsync())
        {
            Console.WriteLine($"Received: {number}");
        }
    }
    
    public static async IAsyncEnumerable<int> GenerateNumbersAsync()
    {
        for (int i = 1; i <= 5; i++)
        {
            await Task.Delay(500);
            yield return i;
        }
    }
}
