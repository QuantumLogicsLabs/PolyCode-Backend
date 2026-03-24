# C# Quick Reference Cheat Sheet

## 🎯 Basic Syntax

### Hello World
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

### Comments
```csharp
// Single line comment

/* Multi-line
   comment */

/// <summary>
/// XML documentation comment
/// </summary>
```

## 📦 Data Types

### Value Types
```csharp
// Integers
int number = 42;
long bigNumber = 9000000000L;
short smallNumber = 32767;
byte tinyNumber = 255;

// Floating point
float singlePrecision = 3.14f;
double doublePrecision = 3.14159265359;
decimal financial = 1234.56m;

// Other
bool isActive = true;
char grade = 'A';
DateTime now = DateTime.Now;
```

### Reference Types
```csharp
string text = "Hello";
int[] numbers = {1, 2, 3};
object obj = new object();
dynamic dynamicVar = "Can change type";
```

## 🔤 Variables and Constants

### Variables
```csharp
int age = 25;
string name = "John";
var implicitType = "Type inferred"; // Compiler determines type
```

### Constants
```csharp
const double PI = 3.14159;
readonly DateTime CreatedDate = DateTime.Now;
```

## 🎮 Control Flow

### If Statements
```csharp
if (condition)
{
    // code
}
else if (anotherCondition)
{
    // code
}
else
{
    // code
}
```

### Switch Statement
```csharp
switch (variable)
{
    case value1:
        // code
        break;
    case value2:
        // code
        break;
    default:
        // code
        break;
}
```

### Loops
```csharp
// For loop
for (int i = 0; i < 10; i++)
{
    Console.WriteLine(i);
}

// While loop
while (condition)
{
    // code
}

// Do-while loop
do
{
    // code
} while (condition);

// Foreach loop
foreach (var item in collection)
{
    Console.WriteLine(item);
}
```

## 🏗️ Classes and Objects

### Class Definition
```csharp
public class Person
{
    // Fields
    private string firstName;
    
    // Properties
    public string LastName { get; set; }
    public int Age { get; private set; }
    
    // Constructor
    public Person(string firstName, string lastName)
    {
        this.firstName = firstName;
        LastName = lastName;
    }
    
    // Methods
    public string GetFullName()
    {
        return $"{firstName} {LastName}";
    }
    
    // Static method
    public static Person CreateDefault()
    {
        return new Person("John", "Doe");
    }
}
```

### Object Creation
```csharp
Person person = new Person("Alice", "Smith");
person.Age = 30;
string fullName = person.GetFullName();
```

## 🔧 Properties

### Auto-Implemented Properties
```csharp
public string Name { get; set; }
public int Id { get; private set; }
public double Price { get; } // Read-only
```

### Full Properties
```csharp
private double _balance;
public double Balance
{
    get { return _balance; }
    set
    {
        if (value >= 0)
            _balance = value;
    }
}
```

## 📦 Arrays and Collections

### Arrays
```csharp
int[] numbers = new int[5];
int[] initialized = {1, 2, 3, 4, 5};
string[,] matrix = new string[3, 3];
```

### Lists
```csharp
List<string> names = new List<string>();
names.Add("Alice");
names.Add("Bob");
string firstName = names[0];
```

### Dictionaries
```csharp
Dictionary<string, int> ages = new Dictionary<string, int>();
ages["Alice"] = 25;
ages["Bob"] = 30;
int aliceAge = ages["Alice"];
```

## 🎯 Methods

### Method Definition
```csharp
public int Add(int a, int b)
{
    return a + b;
}

public void PrintMessage(string message)
{
    Console.WriteLine(message);
}

public double CalculateAverage(params double[] numbers)
{
    return numbers.Average();
}
```

### Method Overloading
```csharp
public int Add(int a, int b) => a + b;
public double Add(double a, double b) => a + b;
public string Add(string a, string b) => a + b;
```

## 🔄 Type Conversion

### Implicit Conversion
```csharp
int intValue = 100;
long longValue = intValue; // Automatic
double doubleValue = intValue; // Automatic
```

### Explicit Conversion
```csharp
double doubleValue = 123.456;
int intValue = (int)doubleValue; // Truncates decimal
```

### Safe Conversion
```csharp
string text = "123";
if (int.TryParse(text, out int number))
{
    Console.WriteLine($"Parsed: {number}");
}
```

## 🔍 LINQ

### Query Syntax
```csharp
var result = from item in collection
             where item.Property > 10
             orderby item.Name
             select item;
```

### Method Syntax
```csharp
var result = collection
    .Where(item => item.Property > 10)
    .OrderBy(item => item.Name)
    .Select(item => item);
```

### Common Operations
```csharp
// Filtering
var filtered = items.Where(x => x.IsActive);

// Projection
var names = items.Select(x => x.Name);

// Grouping
var grouped = items.GroupBy(x => x.Category);

// Aggregation
int count = items.Count();
double average = items.Average(x => x.Price);
decimal sum = items.Sum(x => x.Amount);
```

## ⚡ Exception Handling

### Try-Catch
```csharp
try
{
    // Risky code
    int result = 10 / int.Parse("0");
}
catch (DivideByZeroException ex)
{
    Console.WriteLine("Division by zero");
}
catch (FormatException ex)
{
    Console.WriteLine("Invalid number format");
}
catch (Exception ex)
{
    Console.WriteLine($"General error: {ex.Message}");
}
finally
{
    // Always executed
}
```

### Throwing Exceptions
```csharp
if (age < 0)
{
    throw new ArgumentException("Age cannot be negative");
}
```

## 📅 DateTime Operations

### Creating DateTime
```csharp
DateTime now = DateTime.Now;
DateTime today = DateTime.Today;
DateTime specific = new DateTime(2023, 12, 25, 14, 30, 0);
```

### DateTime Manipulation
```csharp
DateTime tomorrow = today.AddDays(1);
DateTime lastMonth = today.AddMonths(-1);
TimeSpan difference = now - specific;
```

## 📝 String Operations

### String Methods
```csharp
string text = "Hello World";
bool contains = text.Contains("World");
string upper = text.ToUpper();
string lower = text.ToLower();
string replaced = text.Replace("World", "C#");
string[] parts = text.Split(' ');
bool startsWith = text.StartsWith("Hello");
bool endsWith = text.EndsWith("World");
```

### String Formatting
```csharp
string formatted = $"Name: {name}, Age: {age}";
string currency = price.ToString("C");
string date = DateTime.Now.ToString("yyyy-MM-dd");
string number = 1234.567.ToString("F2");
```

## 🎮 Console I/O

### Input
```csharp
string input = Console.ReadLine();
int number = int.Parse(Console.ReadLine());
if (int.TryParse(Console.ReadLine(), out int safeNumber))
{
    // Use safeNumber
}
```

### Output
```csharp
Console.WriteLine("Hello");
Console.Write("No newline");
Console.WriteLine($"Value: {value}");
Console.WriteLine("Formatted: {0:C}", amount);
```

## 🏛️ Access Modifiers

| Modifier | Accessibility |
|----------|---------------|
| `public` | Accessible from anywhere |
| `private` | Accessible only within the class |
| `protected` | Accessible within class and derived classes |
| `internal` | Accessible within the same assembly |
| `protected internal` | Protected OR internal |

## 🔄 Interfaces

### Interface Definition
```csharp
public interface IDrawable
{
    void Draw();
    int Color { get; set; }
}
```

### Interface Implementation
```csharp
public class Circle : IDrawable
{
    public int Color { get; set; }
    
    public void Draw()
    {
        Console.WriteLine("Drawing circle");
    }
}
```

## 🧬 Inheritance

### Base Class
```csharp
public class Animal
{
    public string Name { get; set; }
    
    public virtual void MakeSound()
    {
        Console.WriteLine("Some sound");
    }
}
```

### Derived Class
```csharp
public class Dog : Animal
{
    public override void MakeSound()
    {
        Console.WriteLine("Woof!");
    }
}
```

## 📊 Useful Extensions

### String Extensions
```csharp
public static class StringExtensions
{
    public static bool IsNullOrEmpty(this string value)
    {
        return string.IsNullOrEmpty(value);
    }
}
```

### Generic Extensions
```csharp
public static class ListExtensions
{
    public static T FindOrDefault<T>(this List<T> list, Func<T, bool> predicate)
    {
        return list.FirstOrDefault(predicate);
    }
}
```

## 🎯 Common Patterns

### Singleton Pattern
```csharp
public class Singleton
{
    private static Singleton _instance;
    private static readonly object _lock = new object();
    
    public static Singleton Instance
    {
        get
        {
            if (_instance == null)
            {
                lock (_lock)
                {
                    if (_instance == null)
                        _instance = new Singleton();
                }
            }
            return _instance;
        }
    }
}
```

### Factory Pattern
```csharp
public static class VehicleFactory
{
    public static IVehicle CreateVehicle(string type)
    {
        switch (type.ToLower())
        {
            case "car":
                return new Car();
            case "truck":
                return new Truck();
            default:
                throw new ArgumentException("Unknown vehicle type");
        }
    }
}
```

---

**Keep this cheat sheet handy for quick C# reference! 🚀**
