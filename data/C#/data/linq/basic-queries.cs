using System;
using System.Collections.Generic;
using System.Linq;

public class BasicQueries
{
    public static void Main(string[] args)
    {
        var numbers = new List<int> { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };
        
        // Query syntax
        var evenNumbersQuery = from num in numbers
                               where num % 2 == 0
                               select num;
        
        // Method syntax
        var evenNumbersMethod = numbers.Where(n => n % 2 == 0);
        
        Console.WriteLine("Even numbers (Query syntax):");
        foreach (var num in evenNumbersQuery)
        {
            Console.WriteLine(num);
        }
        
        Console.WriteLine("\nEven numbers (Method syntax):");
        foreach (var num in evenNumbersMethod)
        {
            Console.WriteLine(num);
        }
    }
}
