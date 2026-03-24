using System;
using System.Collections.Generic;

namespace CSharpExercises
{
    // Exercise 1: Car Class
    // Create a Car class with properties and methods
    
    public class Car
    {
        // TODO: Add properties here
        // - Make (string)
        // - Model (string)
        // - Year (int)
        // - Speed (double)
        // - Fuel (double)
        
        // TODO: Add constructor that initializes all properties
        
        // TODO: Add methods
        // - Accelerate(double amount) - increases speed, decreases fuel
        // - Brake(double amount) - decreases speed
        // - Refuel(double amount) - increases fuel
        // - DisplayInfo() - shows all car information
        
        // TODO: Add validation
        // - Speed cannot be negative
        // - Fuel cannot exceed 100 or be less than 0
        // - Year should be reasonable
    }
    
    class Exercise01
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exercise 1: Car Class ===\n");
            
            // TODO: Create Car objects and test methods
            // Example solution (uncomment to see):
            /*
            Car myCar = new Car("Toyota", "Camry", 2020);
            myCar.DisplayInfo();
            
            myCar.Accelerate(50);
            myCar.DisplayInfo();
            
            myCar.Brake(20);
            myCar.DisplayInfo();
            
            myCar.Refuel(30);
            myCar.DisplayInfo();
            */
        }
    }
    
    // Exercise 2: Rectangle Class
    // Create a Rectangle class with validation and computed properties
    
    public class Rectangle
    {
        // TODO: Add private fields for width and height
        
        // TODO: Add public properties with validation
        // - Width (must be positive)
        // - Height (must be positive)
        
        // TODO: Add read-only computed properties
        // - Area (width * height)
        // - Perimeter (2 * (width + height))
        // - IsSquare (width == height)
        
        // TODO: Add constructor
        // TODO: Add method Scale(double factor)
        // TODO: Add static method CreateSquare(double size)
    }
    
    class Exercise02
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exercise 2: Rectangle Class ===\n");
            
            // TODO: Create Rectangle objects and test properties
            // Test validation, computed properties, and methods
        }
    }
    
    // Exercise 3: Bank Account System
    // Create a banking system with multiple account types
    
    public class BankAccount
    {
        // TODO: Add properties and fields
        // - AccountNumber (string, read-only)
        // - OwnerName (string)
        // - Balance (decimal, private with public getter)
        // - InterestRate (decimal, virtual)
        
        // TODO: Add static counter for account numbers
        
        // TODO: Add constructor
        // TODO: Add methods
        // - Deposit(decimal amount)
        // - Withdraw(decimal amount)
        // - CalculateInterest() - virtual
        // - Transfer(BankAccount target, decimal amount)
        
        // TODO: Add validation for all operations
    }
    
    public class SavingsAccount : BankAccount
    {
        // TODO: Override interest rate
        // TODO: Add minimum balance requirement
        // TODO: Override CalculateInterest() with bonus for high balances
    }
    
    public class CheckingAccount : BankAccount
    {
        // TODO: Add overdraft limit
        // TODO: Add monthly fee
        // TODO: Override Withdraw() to handle overdrafts
    }
    
    class Exercise03
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Exercise 3: Bank Account System ===\n");
            
            // TODO: Create different account types
            // Test deposits, withdrawals, transfers, and interest calculations
            // Demonstrate inheritance and polymorphism
        }
    }
}
