using System;
using System.Threading.Tasks;

public class AsyncBestPractices
{
    // Good: Use async all the way down
    public async Task<string> GetDataAsync()
    {
        var data = await FetchDataFromDatabaseAsync();
        return ProcessData(data);
    }
    
    // Bad: Blocking async code
    public string GetDataBad()
    {
        var data = FetchDataFromDatabaseAsync().Result; // Don't do this!
        return ProcessData(data);
    }
    
    // Good: ConfigureAwait for library code
    public async Task<string> LibraryMethodAsync()
    {
        var result = await SomeOperationAsync().ConfigureAwait(false);
        return result;
    }
    
    // Good: Return Task instead of void (except for event handlers)
    public async Task ProcessDataAsync()
    {
        await Task.Delay(1000);
        Console.WriteLine("Data processed");
    }
    
    private async Task<string> FetchDataFromDatabaseAsync()
    {
        await Task.Delay(500);
        return "Database data";
    }
    
    private string ProcessData(string data)
    {
        return $"Processed: {data}";
    }
    
    private async Task<string> SomeOperationAsync()
    {
        await Task.Delay(200);
        return "Operation result";
    }
}
