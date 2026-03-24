using System;

namespace CSharpExercises
{
    // Exercise 1: Type Converter
    // Write a program that demonstrates various type conversions
    
    class Exercise01
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exercise 1: Type Converter ===\n");
            
            // TODO: Add your code here
            // 1. Convert between different numeric types
            // 2. Convert strings to numbers (with validation)
            // 3. Convert numbers to strings
            // 4. Convert to/from boolean
            // 5. Handle conversion errors
            
            // Example solution (uncomment to see):
            /*
            // Numeric conversions
            int intValue = 123;
            double doubleValue = intValue;
            long longValue = (long)doubleValue;
            
            Console.WriteLine($"int to double: {intValue} -> {doubleValue}");
            Console.WriteLine($"double to long: {doubleValue} -> {longValue}");
            
            // String to number with validation
            Console.Write("Enter a number: ");
            string input = Console.ReadLine();
            
            if (int.TryParse(input, out int parsedInt))
            {
                Console.WriteLine($"Successfully parsed: {parsedInt}");
                Console.WriteLine($"Double of number: {parsedInt * 2}");
                Console.WriteLine($"Square of number: {parsedInt * parsedInt}");
            }
            else
            {
                Console.WriteLine("Invalid number format!");
            }
            
            // Boolean conversions
            Console.Write("Enter true/false: ");
            string boolInput = Console.ReadLine();
            if (bool.TryParse(boolInput, out bool boolValue))
            {
                Console.WriteLine($"Boolean value: {boolValue}");
                Console.WriteLine($"Boolean to string: {boolValue.ToString()}");
                Console.WriteLine($"Boolean to int: {(boolValue ? 1 : 0)}");
            }
            */
        }
    }
    
    // Exercise 2: BMI Calculator
    // Create a program that calculates BMI with proper data types
    
    class Exercise02
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exercise 2: BMI Calculator ===\n");
            
            // TODO: Add your code here
            // 1. Ask for weight in kg (use double for precision)
            // 2. Ask for height in meters (use double)
            // 3. Calculate BMI: weight / (height * height)
            // 4. Display BMI with 2 decimal places
            // 5. Show BMI category
            // 6. Handle invalid input
            
            // BMI Categories:
            // < 18.5: Underweight
            // 18.5-24.9: Normal weight
            // 25-29.9: Overweight
            // >= 30: Obesity
        }
    }
    
    // Exercise 3: Shopping Cart
    // Create a shopping cart with proper data types and calculations
    
    class Exercise03
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exercise 3: Shopping Cart ===\n");
            
            // TODO: Add your code here
            // 1. Create variables for cart items (name, price, quantity)
            // 2. Use appropriate data types (decimal for money)
            // 3. Calculate subtotal, tax, and total
            // 4. Apply discount if total exceeds certain amount
            // 5. Display formatted receipt
            
            // Tips:
            // - Use decimal for financial calculations
            // - Use constants for tax rate and discount threshold
            // - Format currency properly
        }
    }
}
