using System;

namespace CSharpExercises
{
    // Exercise 1: Personal Information
    // Write a program that asks for first name, last name, age, and favorite color
    // Then displays a formatted message with all the information
    
    class Exercise01
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exercise 1: Personal Information ===\n");
            
            // TODO: Add your code here
            // 1. Ask for first name
            // 2. Ask for last name
            // 3. Ask for age
            // 4. Ask for favorite color
            // 5. Display formatted message
            
            // Example solution (uncomment to see):
            /*
            Console.Write("Enter your first name: ");
            string firstName = Console.ReadLine();
            
            Console.Write("Enter your last name: ");
            string lastName = Console.ReadLine();
            
            Console.Write("Enter your age: ");
            if (int.TryParse(Console.ReadLine(), out int age))
            {
                Console.Write("Enter your favorite color: ");
                string favoriteColor = Console.ReadLine();
                
                Console.WriteLine($"\nHello, {firstName} {lastName}!");
                Console.WriteLine($"You are {age} years old and your favorite color is {favoriteColor}.");
                Console.WriteLine($"Nice to meet you, {age}-year-old {firstName} who loves {favoriteColor}!");
            }
            else
            {
                Console.WriteLine("Invalid age entered!");
            }
            */
        }
    }
    
    // Exercise 2: Simple Calculator
    // Create a program that asks for two numbers and performs all basic operations
    
    class Exercise02
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exercise 2: Simple Calculator ===\n");
            
            // TODO: Add your code here
            // 1. Ask for first number
            // 2. Ask for second number
            // 3. Perform addition, subtraction, multiplication, division
            // 4. Display all results with proper formatting
            // 5. Handle division by zero
        }
    }
    
    // Exercise 3: Temperature Converter
    // Write a program that converts between Celsius, Fahrenheit, and Kelvin
    
    class Exercise03
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exercise 3: Temperature Converter ===\n");
            
            // TODO: Add your code here
            // 1. Ask for temperature value
            // 2. Ask for input scale (C, F, or K)
            // 3. Convert to other two scales
            // 4. Display all temperatures
            // 5. Handle invalid input
            
            // Formulas:
            // F = (C × 9/5) + 32
            // C = (F - 32) × 5/9
            // K = C + 273.15
        }
    }
}
