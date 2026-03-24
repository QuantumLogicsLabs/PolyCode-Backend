# Variables and Data Types

## 🎯 Learning Objectives

By the end of this lesson, you will:
- Understand what variables are and how to use them
- Learn about C# data types
- Know when to use value types vs reference types
- Understand type conversion and casting
- Learn about constants and readonly fields

## 📦 What are Variables?

Variables are containers for storing data values. In C#, you must declare a variable with a specific type before using it.

### Basic Syntax

```csharp
dataType variableName = value;
```

### Example

```csharp
int age = 25;           // Integer variable
string name = "John";   // String variable
bool isStudent = true;  // Boolean variable
```

## 🔢 Value Types

Value types directly contain their data. Each variable has its own copy of the data.

### Integer Types

```csharp
using System;

namespace IntegerTypes
{
    class Program
    {
        static void Main(string[] args)
        {
            // Signed integers
            sbyte smallNumber = 127;        // -128 to 127
            short mediumNumber = 32767;     // -32,768 to 32,767
            int regularNumber = 2147483647; // -2,147,483,648 to 2,147,483,647
            long largeNumber = 9223372036854775807L; // -9,223,372,036,854,775,808 to 9,223,372,036,854,775,807
            
            // Unsigned integers
            byte tinyNumber = 255;          // 0 to 255
            ushort mediumUnsigned = 65535;  // 0 to 65,535
            uint regularUnsigned = 4294967295U; // 0 to 4,294,967,295
            ulong largeUnsigned = 18446744073709551615UL; // 0 to 18,446,744,073,709,551,615
            
            Console.WriteLine($"sbyte: {smallNumber}");
            Console.WriteLine($"short: {mediumNumber}");
            Console.WriteLine($"int: {regularNumber}");
            Console.WriteLine($"long: {largeNumber}");
        }
    }
}
```

### Floating-Point Types

```csharp
using System;

namespace FloatingPointTypes
{
    class Program
    {
        static void Main(string[] args)
        {
            float singlePrecision = 3.14f;      // 7 digits of precision
            double doublePrecision = 3.14159265359; // 15-16 digits of precision
            decimal highPrecision = 3.14159265358979323846m; // 28-29 digits of precision
            
            Console.WriteLine($"float: {singlePrecision}");
            Console.WriteLine($"double: {doublePrecision}");
            Console.WriteLine($"decimal: {highPrecision}");
            
            // When to use each:
            // float: When memory is critical and precision requirements are low
            // double: Default choice for most scientific calculations
            // decimal: Financial calculations where precision is critical
        }
    }
}
```

### Other Value Types

```csharp
using System;

namespace OtherValueTypes
{
    class Program
    {
        static void Main(string[] args)
        {
            // Boolean
            bool isActive = true;
            bool isCompleted = false;
            
            // Character
            char grade = 'A';
            char symbol = '$';
            
            // DateTime and TimeSpan
            DateTime today = DateTime.Now;
            DateTime birthday = new DateTime(1990, 5, 15);
            TimeSpan duration = TimeSpan.FromHours(2.5);
            
            Console.WriteLine($"Boolean: {isActive}");
            Console.WriteLine($"Character: {grade}");
            Console.WriteLine($"Date: {today.ToShortDateString()}");
            Console.WriteLine($"Duration: {duration.TotalHours} hours");
        }
    }
}
```

## 🏗️ Reference Types

Reference types store a reference to the actual data. Multiple variables can refer to the same object.

### String

```csharp
using System;

namespace StringType
{
    class Program
    {
        static void Main(string[] args)
        {
            // String creation
            string firstName = "John";
            string lastName = "Doe";
            string fullName = firstName + " " + lastName;
            
            // String interpolation
            string message = $"Hello, {fullName}!";
            
            // String properties and methods
            Console.WriteLine($"Length: {fullName.Length}");
            Console.WriteLine($"Upper: {fullName.ToUpper()}");
            Console.WriteLine($"Lower: {fullName.ToLower()}");
            Console.WriteLine($"Contains 'John': {fullName.Contains("John")}");
            
            // String immutability
            string original = "Hello";
            string modified = original + " World";
            Console.WriteLine($"Original: {original}"); // Still "Hello"
            Console.WriteLine($"Modified: {modified}"); // "Hello World"
        }
    }
}
```

### Arrays

```csharp
using System;

namespace ArrayTypes
{
    class Program
    {
        static void Main(string[] args)
        {
            // Single-dimensional arrays
            int[] numbers = new int[5];
            numbers[0] = 10;
            numbers[1] = 20;
            numbers[2] = 30;
            numbers[3] = 40;
            numbers[4] = 50;
            
            // Array initialization
            string[] names = { "Alice", "Bob", "Charlie" };
            
            // Multi-dimensional arrays
            int[,] matrix = new int[3, 3];
            matrix[0, 0] = 1;
            matrix[0, 1] = 2;
            matrix[0, 2] = 3;
            
            // Jagged arrays
            int[][] jagged = new int[3][];
            jagged[0] = new int[] { 1, 2 };
            jagged[1] = new int[] { 3, 4, 5 };
            jagged[2] = new int[] { 6 };
            
            // Accessing arrays
            Console.WriteLine($"First number: {numbers[0]}");
            Console.WriteLine($"Array length: {numbers.Length}");
            
            // Iterating through arrays
            foreach (string name in names)
            {
                Console.WriteLine($"Name: {name}");
            }
        }
    }
}
```

## 🔄 Type Conversion

### Implicit Conversion

```csharp
using System;

namespace ImplicitConversion
{
    class Program
    {
        static void Main(string[] args)
        {
            // Safe conversions (no data loss)
            int intValue = 100;
            long longValue = intValue;        // int to long
            float floatValue = intValue;       // int to float
            double doubleValue = intValue;     // int to double
            decimal decimalValue = intValue;   // int to decimal
            
            Console.WriteLine($"int to long: {intValue} -> {longValue}");
            Console.WriteLine($"int to float: {intValue} -> {floatValue}");
            
            // Unsafe conversions (compile-time error)
            // long longValue2 = intValue;     // This works
            // int intValue2 = longValue;      // This would cause an error
        }
    }
}
```

### Explicit Conversion (Casting)

```csharp
using System;

namespace ExplicitConversion
{
    class Program
    {
        static void Main(string[] args)
        {
            // Casting between numeric types
            double doubleValue = 123.456;
            int intValue = (int)doubleValue; // Truncates decimal part
            Console.WriteLine($"Double to int: {doubleValue} -> {intValue}");
            
            // Casting with potential data loss
            long longValue = 300;
            byte byteValue = (byte)longValue; // 300 % 256 = 44
            Console.WriteLine($"Long to byte: {longValue} -> {byteValue}");
            
            // Safe casting with checked
            try
            {
                checked
                {
                    int bigNumber = 2000000000;
                    int result = bigNumber * 2; // Overflow
                }
            }
            catch (OverflowException)
            {
                Console.WriteLine("Overflow detected!");
            }
        }
    }
}
```

### Conversion Methods

```csharp
using System;

namespace ConversionMethods
{
    class Program
    {
        static void Main(string[] args)
        {
            // Parse methods
            string numberString = "123";
            int parsedInt = int.Parse(numberString);
            double parsedDouble = double.Parse("45.67");
            
            Console.WriteLine($"Parsed int: {parsedInt}");
            Console.WriteLine($"Parsed double: {parsedDouble}");
            
            // TryParse (safer)
            string invalidNumber = "abc";
            if (int.TryParse(invalidNumber, out int result))
            {
                Console.WriteLine($"Successfully parsed: {result}");
            }
            else
            {
                Console.WriteLine("Failed to parse invalid number");
            }
            
            // Convert class
            string stringValue = "456";
            int convertedInt = Convert.ToInt32(stringValue);
            bool convertedBool = Convert.ToBoolean("true");
            
            Console.WriteLine($"Converted int: {convertedInt}");
            Console.WriteLine($"Converted bool: {convertedBool}");
            
            // ToString method
            int number = 789;
            string stringResult = number.ToString();
            Console.WriteLine($"ToString result: {stringResult}");
        }
    }
}
```

## 🔒 Constants and Readonly

### Constants

```csharp
using System;

namespace Constants
{
    class Program
    {
        // Compile-time constants
        const double PI = 3.14159;
        const int DAYS_IN_WEEK = 7;
        const string GREETING = "Hello";
        
        static void Main(string[] args)
        {
            Console.WriteLine($"PI: {PI}");
            Console.WriteLine($"Days in week: {DAYS_IN_WEEK}");
            
            // Calculate circle area
            double radius = 5.0;
            double area = PI * radius * radius;
            Console.WriteLine($"Circle area: {area}");
            
            // Constants cannot be modified
            // PI = 3.14; // This would cause a compile error
        }
    }
}
```

### Readonly Fields

```csharp
using System;

namespace ReadonlyFields
{
    class Circle
    {
        public readonly double Radius;
        public readonly DateTime CreatedDate;
        
        public Circle(double radius)
        {
            Radius = radius;
            CreatedDate = DateTime.Now;
        }
        
        public double CalculateArea()
        {
            return Math.PI * Radius * Radius;
        }
    }
    
    class Program
    {
        static void Main(string[] args)
        {
            Circle circle = new Circle(5.0);
            Console.WriteLine($"Radius: {circle.Radius}");
            Console.WriteLine($"Area: {circle.CalculateArea()}");
            Console.WriteLine($"Created: {circle.CreatedDate}");
            
            // Readonly fields can only be assigned in constructor or declaration
            // circle.Radius = 10.0; // This would cause a compile error
        }
    }
}
```

## 🎯 Practical Examples

### Student Information System

```csharp
using System;

namespace StudentSystem
{
    class Student
    {
        public int StudentId;
        public string FirstName;
        public string LastName;
        public DateTime BirthDate;
        public double GPA;
        public bool IsActive;
        
        public int GetAge()
        {
            int age = DateTime.Now.Year - BirthDate.Year;
            if (BirthDate > DateTime.Now.AddYears(-age))
                age--;
            return age;
        }
        
        public string GetFullName()
        {
            return $"{FirstName} {LastName}";
        }
        
        public void DisplayInfo()
        {
            Console.WriteLine($"Student ID: {StudentId}");
            Console.WriteLine($"Name: {GetFullName()}");
            Console.WriteLine($"Age: {GetAge()}");
            Console.WriteLine($"GPA: {GPA:F2}");
            Console.WriteLine($"Status: {(IsActive ? "Active" : "Inactive")}");
        }
    }
    
    class Program
    {
        static void Main(string[] args)
        {
            Student student = new Student
            {
                StudentId = 1001,
                FirstName = "Alice",
                LastName = "Johnson",
                BirthDate = new DateTime(2000, 5, 15),
                GPA = 3.85,
                IsActive = true
            };
            
            student.DisplayInfo();
        }
    }
}
```

## 🎯 Practice Exercises

### Exercise 1: Type Converter
Write a program that:
- Takes various inputs (number, text, boolean)
- Converts them between different types
- Displays the results

### Exercise 2: BMI Calculator
Create a program that:
- Asks for weight (in kg) and height (in meters)
- Calculates BMI: weight / (height * height)
- Displays the result with appropriate formatting

### Exercise 3: Temperature Converter Enhanced
Extend the temperature converter to:
- Support multiple conversions (Celsius, Fahrenheit, Kelvin)
- Use appropriate data types for precision
- Handle invalid input gracefully

## 💡 Best Practices

1. **Choose the right data type** for your needs
2. **Use `var` for local variables** when the type is obvious
3. **Prefer `decimal` for financial calculations**
4. **Use `TryParse` instead of `Parse` for user input**
5. **Use meaningful variable names**
6. **Initialize variables before use**

## 🚀 Next Steps

Now that you understand variables and data types, let's learn about:

[Operators and Expressions →](05-operators.md)

---

**You're mastering the fundamentals! Keep going! 💪**
