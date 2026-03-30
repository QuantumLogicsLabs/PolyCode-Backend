using System;
using System.Collections.Generic;
using System.Linq;

public class DeferredExecution
{
    public static void Main(string[] args)
    {
        var numbers = new List<int> { 1, 2, 3, 4, 5 };
        
        // Query is defined but not executed yet
        var query = numbers.Where(n => n > 3);
        
        Console.WriteLine("Query defined, data before modification:");
        Console.WriteLine(string.Join(", ", numbers));
        
        // Modify the source data
        numbers.Add(6);
        numbers.Add(7);
        
        // Now execute the query - it will see the new data
        Console.WriteLine("\nExecuting query after adding 6 and 7:");
        foreach (var num in query)
        {
            Console.WriteLine(num);
        }
        
        // Force immediate execution with ToList()
        var immediateQuery = numbers.Where(n => n % 2 == 0).ToList();
        
        Console.WriteLine("\nImmediate execution result:");
        Console.WriteLine(string.Join(", ", immediateQuery));
        
        // Modify source again
        numbers.Add(8);
        
        Console.WriteLine("\nImmediate query result after adding 8 (unchanged):");
        Console.WriteLine(string.Join(", ", immediateQuery));
        
        Console.WriteLine("\nDeferred query result after adding 8 (updated):");
        foreach (var num in query)
        {
            Console.WriteLine(num);
        }
    }
}
