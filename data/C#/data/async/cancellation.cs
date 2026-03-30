using System;
using System.Threading;
using System.Threading.Tasks;

public class CancellationExample
{
    public static async Task Main(string[] args)
    {
        var cts = new CancellationTokenSource();
        cts.CancelAfter(3000); // Cancel after 3 seconds
        
        try
        {
            await LongRunningOperationAsync(cts.Token);
        }
        catch (OperationCanceledException)
        {
            Console.WriteLine("Operation was cancelled!");
        }
    }
    
    public static async Task LongRunningOperationAsync(CancellationToken token)
    {
        for (int i = 0; i < 10; i++)
        {
            token.ThrowIfCancellationRequested();
            Console.WriteLine($"Working... {i + 1}/10");
            await Task.Delay(1000, token);
        }
    }
}
