using System;

namespace VariablesDemo
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== C# Variables and Data Types Demo ===\n");
            
            // Integer types
            Console.WriteLine("INTEGER TYPES:");
            sbyte smallNumber = 127;
            short mediumNumber = 32767;
            int regularNumber = 2147483647;
            long largeNumber = 9223372036854775807L;
            
            Console.WriteLine($"sbyte: {smallNumber}");
            Console.WriteLine($"short: {mediumNumber}");
            Console.WriteLine($"int: {regularNumber}");
            Console.WriteLine($"long: {largeNumber}\n");
            
            // Floating-point types
            Console.WriteLine("FLOATING-POINT TYPES:");
            float singlePrecision = 3.14159f;
            double doublePrecision = 3.141592653589793;
            decimal highPrecision = 3.14159265358979323846m;
            
            Console.WriteLine($"float: {singlePrecision}");
            Console.WriteLine($"double: {doublePrecision}");
            Console.WriteLine($"decimal: {highPrecision}\n");
            
            // Other value types
            Console.WriteLine("OTHER VALUE TYPES:");
            bool isLoggedIn = true;
            char grade = 'A';
            DateTime today = DateTime.Now;
            
            Console.WriteLine($"Boolean: {isLoggedIn}");
            Console.WriteLine($"Character: {grade}");
            Console.WriteLine($"DateTime: {today.ToShortDateString()}\n");
            
            // Reference types
            Console.WriteLine("REFERENCE TYPES:");
            string message = "Hello, C#!";
            int[] numbers = { 1, 2, 3, 4, 5 };
            
            Console.WriteLine($"String: {message}");
            Console.WriteLine($"Array: [{string.Join(", ", numbers)}]\n");
            
            // Type conversion demo
            Console.WriteLine("TYPE CONVERSION:");
            double doubleValue = 123.456;
            int intValue = (int)doubleValue;
            
            Console.WriteLine($"Double to int: {doubleValue} -> {intValue}");
            
            string numberString = "456";
            if (int.TryParse(numberString, out int parsedInt))
            {
                Console.WriteLine($"String to int: '{numberString}' -> {parsedInt}");
            }
            
            Console.WriteLine("\n=== Demo Complete ===");
        }
    }
}
