using System;
using System.IO;

public class BasicExceptions
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Basic Exception Handling ===");
        
        // Try-catch-finally example
        TryCatchFinallyExample();
        
        // Multiple catch blocks
        MultipleCatchExample();
        
        // Using statement for resource cleanup
        UsingStatementExample();
        
        // Throwing exceptions
        ThrowingExceptionsExample();
    }
    
    public static void TryCatchFinallyExample()
    {
        Console.WriteLine("\n--- Try-Catch-Finally ---");
        
        try
        {
            Console.WriteLine("Attempting risky operation...");
            int result = 10 / int.Parse("0"); // This will throw DivideByZeroException
        }
        catch (DivideByZeroException ex)
        {
            Console.WriteLine($"Caught DivideByZeroException: {ex.Message}");
        }
        catch (FormatException ex)
        {
            Console.WriteLine($"Caught FormatException: {ex.Message}");
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Caught general Exception: {ex.Message}");
        }
        finally
        {
            Console.WriteLine("Finally block always executes");
        }
    }
    
    public static void MultipleCatchExample()
    {
        Console.WriteLine("\n--- Multiple Catch Blocks ---");
        
        string[] inputs = { "10", "0", "abc", "5" };
        
        foreach (var input in inputs)
        {
            try
            {
                int divisor = int.Parse(input);
                int result = 100 / divisor;
                Console.WriteLine($"100 / {divisor} = {result}");
            }
            catch (DivideByZeroException)
            {
                Console.WriteLine($"Cannot divide by zero (input: {input})");
            }
            catch (FormatException)
            {
                Console.WriteLine($"Invalid number format (input: {input})");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Unexpected error: {ex.Message}");
            }
        }
    }
    
    public static void UsingStatementExample()
    {
        Console.WriteLine("\n--- Using Statement ---");
        
        try
        {
            string filePath = "test.txt";
            
            // Using statement ensures proper disposal
            using (var writer = new StreamWriter(filePath))
            {
                writer.WriteLine("Hello, World!");
                writer.WriteLine("This is a test file.");
            }
            
            Console.WriteLine("File written successfully");
            
            // Read the file back
            using (var reader = new StreamReader(filePath))
            {
                string content = reader.ReadToEnd();
                Console.WriteLine("File content:");
                Console.WriteLine(content);
            }
        }
        catch (IOException ex)
        {
            Console.WriteLine($"IO Error: {ex.Message}");
        }
        finally
        {
            // Clean up file if it exists
            if (File.Exists("test.txt"))
            {
                File.Delete("test.txt");
                Console.WriteLine("Test file cleaned up");
            }
        }
    }
    
    public static void ThrowingExceptionsExample()
    {
        Console.WriteLine("\n--- Throwing Exceptions ---");
        
        try
        {
            ValidateAge(-5);
        }
        catch (ArgumentException ex)
        {
            Console.WriteLine($"Validation failed: {ex.Message}");
        }
        
        try
        {
            ProcessUser(null);
        }
        catch (ArgumentNullException ex)
        {
            Console.WriteLine($"Null argument: {ex.Message}");
        }
    }
    
    public static void ValidateAge(int age)
    {
        if (age < 0)
        {
            throw new ArgumentException("Age cannot be negative", nameof(age));
        }
        
        if (age > 150)
        {
            throw new ArgumentOutOfRangeException(nameof(age), "Age seems unrealistic");
        }
        
        Console.WriteLine($"Age {age} is valid");
    }
    
    public static void ProcessUser(string userName)
    {
        if (string.IsNullOrEmpty(userName))
        {
            throw new ArgumentNullException(nameof(userName), "User name cannot be null or empty");
        }
        
        Console.WriteLine($"Processing user: {userName}");
    }
}
