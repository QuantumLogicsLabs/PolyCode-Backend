using System;

namespace HelloWorld
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Hello, World!");
            Console.WriteLine("Welcome to C# Programming!");
            
            // Get user input
            Console.Write("What's your name? ");
            string name = Console.ReadLine();
            
            Console.WriteLine($"Nice to meet you, {name}!");
            
            // Simple math demonstration
            Console.Write("Enter a number: ");
            if (int.TryParse(Console.ReadLine(), out int number))
            {
                Console.WriteLine($"Double of your number: {number * 2}");
                Console.WriteLine($"Square of your number: {number * number}");
            }
            else
            {
                Console.WriteLine("That's not a valid number!");
            }
        }
    }
}
