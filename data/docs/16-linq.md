# LINQ (Language Integrated Query)

## 🎯 Learning Objectives

By the end of this lesson, you will:
- Understand what LINQ is and why it's useful
- Learn LINQ query syntax and method syntax
- Master common LINQ operators
- Understand deferred vs immediate execution
- Learn to query different data sources

## 🔍 What is LINQ?

LINQ (Language Integrated Query) is a unified query syntax that allows you to query various data sources using the same syntax. It provides a consistent way to work with data from different sources like collections, databases, XML, and JSON.

### Key Benefits
- **Type Safety**: Compile-time checking of queries
- **IntelliSense**: Auto-completion and error checking
- **Unified Syntax**: Same syntax for different data sources
- **Composability**: Chain multiple operations
- **Readability**: Declarative, expressive code

## 📝 Two Syntax Styles

### Query Syntax (SQL-like)
```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace LinqQuerySyntax
{
    class Program
    {
        static void Main(string[] args)
        {
            List<Student> students = new List<Student>
            {
                new Student { Id = 1, Name = "Alice", Age = 20, GPA = 3.8, Major = "Computer Science" },
                new Student { Id = 2, Name = "Bob", Age = 22, GPA = 3.2, Major = "Mathematics" },
                new Student { Id = 3, Name = "Charlie", Age = 21, GPA = 3.9, Major = "Computer Science" },
                new Student { Id = 4, Name = "Diana", Age = 19, GPA = 3.5, Major = "Physics" },
                new Student { Id = 5, Name = "Eve", Age = 23, GPA = 3.7, Major = "Mathematics" }
            };
            
            // Query syntax example
            var highGpaStudents = from student in students
                                 where student.GPA >= 3.7
                                 orderby student.Name
                                 select student;
            
            Console.WriteLine("Students with GPA >= 3.7:");
            foreach (var student in highGpaStudents)
            {
                Console.WriteLine($"{student.Name} - GPA: {student.GPA}");
            }
        }
    }
    
    public class Student
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public int Age { get; set; }
        public double GPA { get; set; }
        public string Major { get; set; }
    }
}
```

### Method Syntax (Lambda expressions)
```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace LinqMethodSyntax
{
    class Program
    {
        static void Main(string[] args)
        {
            List<Student> students = new List<Student>
            {
                new Student { Id = 1, Name = "Alice", Age = 20, GPA = 3.8, Major = "Computer Science" },
                new Student { Id = 2, Name = "Bob", Age = 22, GPA = 3.2, Major = "Mathematics" },
                new Student { Id = 3, Name = "Charlie", Age = 21, GPA = 3.9, Major = "Computer Science" },
                new Student { Id = 4, Name = "Diana", Age = 19, GPA = 3.5, Major = "Physics" },
                new Student { Id = 5, Name = "Eve", Age = 23, GPA = 3.7, Major = "Mathematics" }
            };
            
            // Method syntax example
            var highGpaStudents = students
                .Where(student => student.GPA >= 3.7)
                .OrderBy(student => student.Name);
            
            Console.WriteLine("Students with GPA >= 3.7:");
            foreach (var student in highGpaStudents)
            {
                Console.WriteLine($"{student.Name} - GPA: {student.GPA}");
            }
        }
    }
    
    public class Student
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public int Age { get; set; }
        public double GPA { get; set; }
        public string Major { get; set; }
    }
}
```

## 🔧 Common LINQ Operators

### Filtering Operations

```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace LinqFiltering
{
    class Program
    {
        static void Main(string[] args)
        {
            List<int> numbers = new List<int> { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };
            
            // Where - Filter based on condition
            var evenNumbers = numbers.Where(n => n % 2 == 0);
            Console.WriteLine($"Even numbers: {string.Join(", ", evenNumbers)}");
            
            // OfType - Filter by type
            List<object> mixedList = new List<object> { 1, "hello", 3.14, "world", 5 };
            var stringsOnly = mixedList.OfType<string>();
            Console.WriteLine($"Strings only: {string.Join(", ", stringsOnly)}");
            
            // Take - Take first N elements
            var firstThree = numbers.Take(3);
            Console.WriteLine($"First three: {string.Join(", ", firstThree)}");
            
            // Skip - Skip first N elements
            var skipFirstThree = numbers.Skip(3);
            Console.WriteLine($"Skip first three: {string.Join(", ", skipFirstThree)}");
            
            // TakeWhile - Take while condition is true
            var takeWhileLessThan5 = numbers.TakeWhile(n => n < 5);
            Console.WriteLine($"Take while < 5: {string.Join(", ", takeWhileLessThan5)}");
            
            // SkipWhile - Skip while condition is true
            var skipWhileLessThan5 = numbers.SkipWhile(n => n < 5);
            Console.WriteLine($"Skip while < 5: {string.Join(", ", skipWhileLessThan5)}");
        }
    }
}
```

### Projection Operations

```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace LinqProjection
{
    class Program
    {
        static void Main(string[] args)
        {
            List<Student> students = new List<Student>
            {
                new Student { Id = 1, Name = "Alice", Age = 20, GPA = 3.8, Major = "Computer Science" },
                new Student { Id = 2, Name = "Bob", Age = 22, GPA = 3.2, Major = "Mathematics" },
                new Student { Id = 3, Name = "Charlie", Age = 21, GPA = 3.9, Major = "Computer Science" }
            };
            
            // Select - Transform to new form
            var studentNames = students.Select(s => s.Name);
            Console.WriteLine($"Student names: {string.Join(", ", studentNames)}");
            
            // Select with anonymous type
            var studentInfo = students.Select(s => new 
            { 
                FullName = s.Name, 
                Status = s.GPA >= 3.5 ? "Excellent" : "Good" 
            });
            Console.WriteLine("Student info:");
            foreach (var info in studentInfo)
            {
                Console.WriteLine($"{info.FullName} - {info.Status}");
            }
            
            // SelectMany - Flatten collections
            List<Teacher> teachers = new List<Teacher>
            {
                new Teacher { Name = "Mr. Smith", Subjects = new List<string> { "Math", "Physics" } },
                new Teacher { Name = "Ms. Johnson", Subjects = new List<string> { "English", "History" } }
            };
            
            var allSubjects = teachers.SelectMany(t => t.Subjects);
            Console.WriteLine($"All subjects: {string.Join(", ", allSubjects)}");
        }
    }
    
    public class Student
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public int Age { get; set; }
        public double GPA { get; set; }
        public string Major { get; set; }
    }
    
    public class Teacher
    {
        public string Name { get; set; }
        public List<string> Subjects { get; set; }
    }
}
```

### Ordering Operations

```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace LinqOrdering
{
    class Program
    {
        static void Main(string[] args)
        {
            List<Student> students = new List<Student>
            {
                new Student { Id = 1, Name = "Alice", Age = 20, GPA = 3.8, Major = "Computer Science" },
                new Student { Id = 2, Name = "Bob", Age = 22, GPA = 3.2, Major = "Mathematics" },
                new Student { Id = 3, Name = "Charlie", Age = 21, GPA = 3.9, Major = "Computer Science" },
                new Student { Id = 4, Name = "Diana", Age = 19, GPA = 3.5, Major = "Physics" },
                new Student { Id = 5, Name = "Eve", Age = 23, GPA = 3.7, Major = "Mathematics" }
            };
            
            // OrderBy - Sort in ascending order
            var byName = students.OrderBy(s => s.Name);
            Console.WriteLine("Sorted by name:");
            foreach (var student in byName)
            {
                Console.WriteLine($"{student.Name}");
            }
            
            // OrderByDescending - Sort in descending order
            var byGPA = students.OrderByDescending(s => s.GPA);
            Console.WriteLine("\nSorted by GPA (descending):");
            foreach (var student in byGPA)
            {
                Console.WriteLine($"{student.Name} - GPA: {student.GPA}");
            }
            
            // ThenBy - Secondary sorting
            var byMajorThenName = students
                .OrderBy(s => s.Major)
                .ThenBy(s => s.Name);
            Console.WriteLine("\nSorted by major, then name:");
            foreach (var student in byMajorThenName)
            {
                Console.WriteLine($"{student.Major} - {student.Name}");
            }
            
            // Reverse - Reverse the order
            var reversed = students.Reverse();
            Console.WriteLine("\nReversed order:");
            foreach (var student in reversed)
            {
                Console.WriteLine($"{student.Name}");
            }
        }
    }
    
    public class Student
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public int Age { get; set; }
        public double GPA { get; set; }
        public string Major { get; set; }
    }
}
```

### Grouping Operations

```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace LinqGrouping
{
    class Program
    {
        static void Main(string[] args)
        {
            List<Student> students = new List<Student>
            {
                new Student { Id = 1, Name = "Alice", Age = 20, GPA = 3.8, Major = "Computer Science" },
                new Student { Id = 2, Name = "Bob", Age = 22, GPA = 3.2, Major = "Mathematics" },
                new Student { Id = 3, Name = "Charlie", Age = 21, GPA = 3.9, Major = "Computer Science" },
                new Student { Id = 4, Name = "Diana", Age = 19, GPA = 3.5, Major = "Physics" },
                new Student { Id = 5, Name = "Eve", Age = 23, GPA = 3.7, Major = "Mathematics" },
                new Student { Id = 6, Name = "Frank", Age = 20, GPA = 3.6, Major = "Computer Science" }
            };
            
            // GroupBy - Group by key
            var byMajor = students.GroupBy(s => s.Major);
            Console.WriteLine("Students grouped by major:");
            foreach (var group in byMajor)
            {
                Console.WriteLine($"\n{group.Key} ({group.Count()} students):");
                foreach (var student in group)
                {
                    Console.WriteLine($"  {student.Name} - GPA: {student.GPA}");
                }
            }
            
            // GroupBy with multiple keys
            var byMajorAndGPA = students.GroupBy(s => new 
            { 
                Major = s.Major, 
                Category = s.GPA >= 3.5 ? "High" : "Regular" 
            });
            
            Console.WriteLine("\nStudents grouped by major and GPA category:");
            foreach (var group in byMajorAndGPA)
            {
                Console.WriteLine($"\n{group.Key.Major} - {group.Key.Category}:");
                foreach (var student in group)
                {
                    Console.WriteLine($"  {student.Name} - GPA: {student.GPA}");
                }
            }
        }
    }
    
    public class Student
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public int Age { get; set; }
        public double GPA { get; set; }
        public string Major { get; set; }
    }
}
```

### Aggregate Operations

```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace LinqAggregation
{
    class Program
    {
        static void Main(string[] args)
        {
            List<int> numbers = new List<int> { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };
            
            // Count - Count elements
            int count = numbers.Count();
            Console.WriteLine($"Count: {count}");
            
            // Count with condition
            int evenCount = numbers.Count(n => n % 2 == 0);
            Console.WriteLine($"Even count: {evenCount}");
            
            // Sum - Sum of elements
            int sum = numbers.Sum();
            Console.WriteLine($"Sum: {sum}");
            
            // Average - Average of elements
            double average = numbers.Average();
            Console.WriteLine($"Average: {average}");
            
            // Min/Max - Minimum and maximum
            int min = numbers.Min();
            int max = numbers.Max();
            Console.WriteLine($"Min: {min}, Max: {max}");
            
            // Aggregate - Custom aggregation
            string[] words = { "Hello", "World", "LINQ", "is", "awesome" };
            string sentence = words.Aggregate((current, next) => current + " " + next);
            Console.WriteLine($"Aggregated sentence: {sentence}");
            
            // Complex aggregation with students
            List<Student> students = new List<Student>
            {
                new Student { Name = "Alice", GPA = 3.8, Credits = 120 },
                new Student { Name = "Bob", GPA = 3.2, Credits = 90 },
                new Student { Name = "Charlie", GPA = 3.9, Credits = 110 }
            };
            
            double averageGPA = students.Average(s => s.GPA);
            int totalCredits = students.Sum(s => s.Credits);
            
            Console.WriteLine($"Average GPA: {averageGPA:F2}");
            Console.WriteLine($"Total credits: {totalCredits}");
        }
    }
    
    public class Student
    {
        public string Name { get; set; }
        public double GPA { get; set; }
        public int Credits { get; set; }
    }
}
```

## ⚡ Execution Models

### Deferred Execution

```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace DeferredExecution
{
    class Program
    {
        static void Main(string[] args)
        {
            List<int> numbers = new List<int> { 1, 2, 3, 4, 5 };
            
            // Query is defined but not executed yet
            var query = numbers.Where(n => n > 3);
            
            Console.WriteLine("Query defined, not executed");
            
            // Add more numbers to the source
            numbers.Add(6);
            numbers.Add(7);
            
            // Query is executed when enumerated
            Console.WriteLine("Executing query:");
            foreach (var num in query)
            {
                Console.WriteLine(num);
            }
        }
    }
}
```

### Immediate Execution

```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace ImmediateExecution
{
    class Program
    {
        static void Main(string[] args)
        {
            List<int> numbers = new List<int> { 1, 2, 3, 4, 5 };
            
            // ToList, ToArray, ToDictionary execute immediately
            var result = numbers.Where(n => n > 3).ToList();
            
            Console.WriteLine("Query executed immediately");
            
            // Adding to original list won't affect result
            numbers.Add(6);
            numbers.Add(7);
            
            Console.WriteLine("Result after adding to source:");
            foreach (var num in result)
            {
                Console.WriteLine(num);
            }
            
            // Count, First, Single also execute immediately
            int count = numbers.Count(n => n > 3);
            int first = numbers.First(n => n > 3);
            
            Console.WriteLine($"Count: {count}");
            Console.WriteLine($"First: {first}");
        }
    }
}
```

## 🎯 Practical Examples

### Sales Analysis System

```csharp
using System;
using System.Collections.Generic;
using System.Linq;

namespace SalesAnalysis
{
    class Program
    {
        static void Main(string[] args)
        {
            List<Sale> sales = new List<Sale>
            {
                new Sale { Id = 1, Product = "Laptop", Amount = 1200, Date = new DateTime(2023, 1, 15), Category = "Electronics", Salesperson = "Alice" },
                new Sale { Id = 2, Product = "Mouse", Amount = 25, Date = new DateTime(2023, 1, 16), Category = "Electronics", Salesperson = "Bob" },
                new Sale { Id = 3, Product = "Desk", Amount = 300, Date = new DateTime(2023, 1, 17), Category = "Furniture", Salesperson = "Alice" },
                new Sale { Id = 4, Product = "Chair", Amount = 150, Date = new DateTime(2023, 1, 18), Category = "Furniture", Salesperson = "Charlie" },
                new Sale { Id = 5, Product = "Keyboard", Amount = 75, Date = new DateTime(2023, 1, 19), Category = "Electronics", Salesperson = "Bob" },
                new Sale { Id = 6, Product = "Monitor", Amount = 400, Date = new DateTime(2023, 1, 20), Category = "Electronics", Salesperson = "Alice" }
            };
            
            Console.WriteLine("=== Sales Analysis ===\n");
            
            // Total sales
            decimal totalSales = sales.Sum(s => s.Amount);
            Console.WriteLine($"Total sales: ${totalSales:N2}");
            
            // Sales by category
            var salesByCategory = sales
                .GroupBy(s => s.Category)
                .Select(g => new 
                { 
                    Category = g.Key, 
                    Total = g.Sum(s => s.Amount),
                    Count = g.Count()
                });
            
            Console.WriteLine("\nSales by category:");
            foreach (var category in salesByCategory)
            {
                Console.WriteLine($"{category.Category}: ${category.Total:N2} ({category.Count} sales)");
            }
            
            // Top salesperson
            var salesByPerson = sales
                .GroupBy(s => s.Salesperson)
                .Select(g => new 
                { 
                    Salesperson = g.Key, 
                    Total = g.Sum(s => s.Amount),
                    Count = g.Count()
                })
                .OrderByDescending(p => p.Total)
                .First();
            
            Console.WriteLine($"\nTop salesperson: {salesByPerson.Salesperson}");
            Console.WriteLine($"Total sales: ${salesByPerson.Total:N2}");
            Console.WriteLine($"Number of sales: {salesByPerson.Count}");
            
            // Average sale amount
            double averageSale = sales.Average(s => s.Amount);
            Console.WriteLine($"\nAverage sale amount: ${averageSale:N2}");
            
            // Largest single sale
            var largestSale = sales.OrderByDescending(s => s.Amount).First();
            Console.WriteLine($"\nLargest sale: {largestSale.Product} - ${largestSale.Amount:N2}");
            
            // Sales over $100
            var bigSales = sales.Where(s => s.Amount > 100);
            Console.WriteLine($"\nSales over $100: {bigSales.Count()} sales");
            foreach (var sale in bigSales)
            {
                Console.WriteLine($"  {sale.Product}: ${sale.Amount:N2}");
            }
        }
    }
    
    public class Sale
    {
        public int Id { get; set; }
        public string Product { get; set; }
        public decimal Amount { get; set; }
        public DateTime Date { get; set; }
        public string Category { get; set; }
        public string Salesperson { get; set; }
    }
}
```

## 🎯 Practice Exercises

### Exercise 1: Product Filter
Create a product list and use LINQ to:
- Find products in a specific price range
- Group products by category
- Find the most expensive product in each category
- Calculate average price per category

### Exercise 2: Student Grades
Create a student grade system that:
- Calculates average grades per student
- Finds top 3 students by GPA
- Groups students by major
- Calculates grade distribution (A, B, C, etc.)

### Exercise 3: Order Processing
Create an order processing system that:
- Filters orders by date range
- Groups orders by customer
- Calculates total revenue per month
- Finds customers with highest order values

## 💡 Best Practices

1. **Use method syntax** for complex queries and chaining
2. **Use query syntax** for simple, SQL-like queries
3. **Understand deferred execution** to avoid unexpected behavior
4. **Use appropriate operators** for the task (Count vs Any, First vs Single)
5. **Avoid multiple enumerations** by caching results with ToList()
6. **Use meaningful variable names** for query results

## 🚀 Next Steps

Now that you understand LINQ, let's learn about:

[Delegates and Events →](17-delegates-events.md)

---

**You're mastering data querying in C#! LINQ is incredibly powerful! 🚀**
