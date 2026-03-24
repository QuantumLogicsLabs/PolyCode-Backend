# Your First C# Program

## 🎯 Learning Objectives

By the end of this lesson, you will:
- Understand the basic structure of a C# program
- Write and run your first C# application
- Learn about the Main method
- Understand basic console output

## 📝 The Classic "Hello, World!" Program

### Complete Program

```csharp
using System;

namespace HelloWorld
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Hello, World!");
        }
    }
}
```

### Breaking It Down

Let's examine each part of this program:

#### 1. `using System;`
- Imports the `System` namespace
- Gives access to core classes like `Console`
- Similar to `import` in other languages

#### 2. `namespace HelloWorld`
- Organizes code into logical groups
- Prevents naming conflicts
- Optional but recommended

#### 3. `class Program`
- C# is object-oriented, so all code lives in classes
- `Program` is a conventional name for the main class

#### 4. `static void Main(string[] args)`
- **Entry point** of the application
- `static`: Can be called without creating an object
- `void`: Returns no value
- `string[] args`: Command line arguments

#### 5. `Console.WriteLine("Hello, World!");`
- Writes text to the console
- Adds a newline after the text
- `Console` is in the `System` namespace

## 🚀 Running Your Program

### Method 1: Visual Studio

1. Create a new Console App project
2. Replace the generated code with the example above
3. Press **F5** or click the "Start" button
4. You'll see the output in a console window

### Method 2: .NET CLI

1. Create a new project:
   ```bash
   dotnet new console -n HelloWorld
   cd HelloWorld
   ```

2. Replace the code in `Program.cs`

3. Run the program:
   ```bash
   dotnet run
   ```

### Method 3: Online Compiler

Visit [dotnetfiddle.net](https://dotnetfiddle.net/) and paste the code to run it in your browser.

## 🔤 Working with Text

### Basic Output Methods

```csharp
using System;

namespace TextExamples
{
    class Program
    {
        static void Main(string[] args)
        {
            // WriteLine adds a newline
            Console.WriteLine("This is on line 1");
            Console.WriteLine("This is on line 2");
            
            // Write does NOT add a newline
            Console.Write("This stays ");
            Console.Write("on the same line.");
            
            // Empty line for spacing
            Console.WriteLine();
        }
    }
}
```

### String Concatenation

```csharp
using System;

namespace StringDemo
{
    class Program
    {
        static void Main(string[] args)
        {
            string firstName = "John";
            string lastName = "Doe";
            int age = 25;
            
            // Method 1: String concatenation
            Console.WriteLine("Hello, " + firstName + " " + lastName);
            
            // Method 2: String formatting
            Console.WriteLine("Hello, {0} {1}", firstName, lastName);
            
            // Method 3: String interpolation (recommended)
            Console.WriteLine($"Hello, {firstName} {lastName}");
            Console.WriteLine($"You are {age} years old.");
        }
    }
}
```

## 🔢 Working with Numbers

### Basic Arithmetic

```csharp
using System;

namespace MathDemo
{
    class Program
    {
        static void Main(string[] args)
        {
            int x = 10;
            int y = 5;
            
            // Basic operations
            Console.WriteLine($"Addition: {x} + {y} = {x + y}");
            Console.WriteLine($"Subtraction: {x} - {y} = {x - y}");
            Console.WriteLine($"Multiplication: {x} * {y} = {x * y}");
            Console.WriteLine($"Division: {x} / {y} = {x / y}");
            Console.WriteLine($"Modulus: {x} % {y} = {x % y}");
            
            // Floating point
            double a = 10.5;
            double b = 3.2;
            Console.WriteLine($"Float division: {a} / {b} = {a / b}");
        }
    }
}
```

## 🎮 Interactive Program

### Getting User Input

```csharp
using System;

namespace InteractiveDemo
{
    class Program
    {
        static void Main(string[] args)
        {
            // Get user's name
            Console.Write("Enter your name: ");
            string name = Console.ReadLine();
            
            // Get user's age
            Console.Write("Enter your age: ");
            string ageInput = Console.ReadLine();
            int age = int.Parse(ageInput);
            
            // Display personalized message
            Console.WriteLine($"Hello, {name}!");
            Console.WriteLine($"You are {age} years old.");
            
            // Calculate birth year (simplified)
            int currentYear = 2024;
            int birthYear = currentYear - age;
            Console.WriteLine($"You were born around {birthYear}.");
        }
    }
}
```

### Safe Input Parsing

```csharp
using System;

namespace SafeInput
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.Write("Enter a number: ");
            string input = Console.ReadLine();
            
            // Safe parsing
            if (int.TryParse(input, out int number))
            {
                Console.WriteLine($"You entered: {number}");
                Console.WriteLine($"Double of your number: {number * 2}");
            }
            else
            {
                Console.WriteLine("That's not a valid number!");
            }
        }
    }
}
```

## 🔤 Comments in C#

### Types of Comments

```csharp
using System;

namespace CommentsDemo
{
    class Program
    {
        static void Main(string[] args)
        {
            // This is a single-line comment
            
            /* This is a multi-line comment
               that can span multiple lines
               and is useful for longer explanations */
            
            /// <summary>
            /// This is an XML documentation comment
            /// It's used to generate documentation
            /// </summary>
            /// <param name="args">Command line arguments</param>
            static void DocumentationExample(string[] args)
            {
                Console.WriteLine("Documentation comments are useful!");
            }
        }
    }
}
```

## 🎯 Practice Exercises

### Exercise 1: Personal Information
Write a program that asks for:
- First name
- Last name  
- Age
- Favorite color

Then display a formatted message with all the information.

### Exercise 2: Simple Calculator
Create a program that:
- Asks for two numbers
- Performs addition, subtraction, multiplication, and division
- Displays all results

### Exercise 3: Temperature Converter
Write a program that:
- Asks for a temperature in Celsius
- Converts it to Fahrenheit
- Displays both temperatures

**Formula:** F = (C × 9/5) + 32

## 💡 Best Practices

1. **Use meaningful variable names**
2. **Add comments to explain complex logic**
3. **Use string interpolation (`$"{}"`) for formatting**
4. **Always validate user input**
5. **Keep methods small and focused**

## 🚀 Next Steps

Now that you can write basic programs, let's learn about:

[Variables and Data Types →](04-variables-data-types.md)

---

**Congratulations! You've written your first C# program! 🎉**
