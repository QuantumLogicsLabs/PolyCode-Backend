using System;
using System.Collections.Generic;
using System.Linq;

namespace LinqDemo
{
    public class Student
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public int Age { get; set; }
        public double GPA { get; set; }
        public string Major { get; set; }
    }
    
    public class Product
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public decimal Price { get; set; }
        public string Category { get; set; }
        public int Stock { get; set; }
    }
    
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== LINQ Demonstration ===\n");
            
            // Sample data
            List<Student> students = new List<Student>
            {
                new Student { Id = 1, Name = "Alice", Age = 20, GPA = 3.8, Major = "Computer Science" },
                new Student { Id = 2, Name = "Bob", Age = 22, GPA = 3.2, Major = "Mathematics" },
                new Student { Id = 3, Name = "Charlie", Age = 21, GPA = 3.9, Major = "Computer Science" },
                new Student { Id = 4, Name = "Diana", Age = 19, GPA = 3.5, Major = "Physics" },
                new Student { Id = 5, Name = "Eve", Age = 23, GPA = 3.7, Major = "Mathematics" },
                new Student { Id = 6, Name = "Frank", Age = 20, GPA = 3.6, Major = "Computer Science" }
            };
            
            List<Product> products = new List<Product>
            {
                new Product { Id = 1, Name = "Laptop", Price = 1200m, Category = "Electronics", Stock = 15 },
                new Product { Id = 2, Name = "Mouse", Price = 25m, Category = "Electronics", Stock = 50 },
                new Product { Id = 3, Name = "Desk", Price = 300m, Category = "Furniture", Stock = 8 },
                new Product { Id = 4, Name = "Chair", Price = 150m, Category = "Furniture", Stock = 20 },
                new Product { Id = 5, Name = "Keyboard", Price = 75m, Category = "Electronics", Stock = 30 },
                new Product { Id = 6, Name = "Monitor", Price = 400m, Category = "Electronics", Stock = 12 }
            };
            
            // 1. Filtering with Where
            Console.WriteLine("1. Students with GPA >= 3.7:");
            var highGpaStudents = students.Where(s => s.GPA >= 3.7);
            foreach (var student in highGpaStudents)
            {
                Console.WriteLine($"   {student.Name} - GPA: {student.GPA}");
            }
            Console.WriteLine();
            
            // 2. Ordering with OrderBy and ThenBy
            Console.WriteLine("2. Students sorted by major, then by GPA:");
            var sortedStudents = students
                .OrderBy(s => s.Major)
                .ThenByDescending(s => s.GPA);
            foreach (var student in sortedStudents)
            {
                Console.WriteLine($"   {student.Major} - {student.Name} - GPA: {student.GPA}");
            }
            Console.WriteLine();
            
            // 3. Projection with Select
            Console.WriteLine("3. Student names and status:");
            var studentInfo = students.Select(s => new 
            { 
                Name = s.Name, 
                Status = s.GPA >= 3.5 ? "Excellent" : "Good",
                Category = s.Major
            });
            foreach (var info in studentInfo)
            {
                Console.WriteLine($"   {info.Name} - {info.Status} ({info.Category})");
            }
            Console.WriteLine();
            
            // 4. Grouping with GroupBy
            Console.WriteLine("4. Students grouped by major:");
            var studentsByMajor = students.GroupBy(s => s.Major);
            foreach (var group in studentsByMajor)
            {
                Console.WriteLine($"   {group.Key} ({group.Count()} students):");
                foreach (var student in group)
                {
                    Console.WriteLine($"     {student.Name} - GPA: {student.GPA}");
                }
            }
            Console.WriteLine();
            
            // 5. Aggregation operations
            Console.WriteLine("5. Aggregation results:");
            double averageGPA = students.Average(s => s.GPA);
            double maxGPA = students.Max(s => s.GPA);
            int totalStudents = students.Count();
            int csStudents = students.Count(s => s.Major == "Computer Science");
            
            Console.WriteLine($"   Average GPA: {averageGPA:F2}");
            Console.WriteLine($"   Highest GPA: {maxGPA:F2}");
            Console.WriteLine($"   Total students: {totalStudents}");
            Console.WriteLine($"   Computer Science students: {csStudents}");
            Console.WriteLine();
            
            // 6. Complex product analysis
            Console.WriteLine("6. Product analysis:");
            var productAnalysis = products
                .Where(p => p.Stock > 10)
                .GroupBy(p => p.Category)
                .Select(g => new 
                { 
                    Category = g.Key,
                    Products = g.Count(),
                    TotalValue = g.Sum(p => p.Price * p.Stock),
                    AveragePrice = g.Average(p => p.Price)
                });
            
            foreach (var analysis in productAnalysis)
            {
                Console.WriteLine($"   {analysis.Category}:");
                Console.WriteLine($"     Products: {analysis.Products}");
                Console.WriteLine($"     Total inventory value: ${analysis.TotalValue:N2}");
                Console.WriteLine($"     Average price: ${analysis.AveragePrice:N2}");
            }
            Console.WriteLine();
            
            // 7. Query syntax example
            Console.WriteLine("7. Query syntax - Students age 20-22 with high GPA:");
            var queryResult = from student in students
                             where student.Age >= 20 && student.Age <= 22 && student.GPA >= 3.5
                             orderby student.GPA descending
                             select new { student.Name, student.Major, student.GPA };
            
            foreach (var result in queryResult)
            {
                Console.WriteLine($"   {result.Name} - {result.Major} - GPA: {result.GPA}");
            }
            Console.WriteLine();
            
            // 8. First/Single/FirstOrDefault
            Console.WriteLine("8. Finding specific elements:");
            var firstStudent = students.First(s => s.Major == "Computer Science");
            var firstOrDefaultStudent = students.FirstOrDefault(s => s.Major == "Engineering");
            var singleStudent = students.Single(s => s.Id == 3);
            
            Console.WriteLine($"   First CS student: {firstStudent.Name}");
            Console.WriteLine($"   First Engineering student: {(firstOrDefaultStudent?.Name ?? "None")}");
            Console.WriteLine($"   Student with ID 3: {singleStudent.Name}");
            Console.WriteLine();
            
            // 9. Any and All
            Console.WriteLine("9. Any and All operations:");
            bool hasHighGPA = students.Any(s => s.GPA >= 4.0);
            bool allAdults = students.All(s => s.Age >= 18);
            bool hasPhysicsStudents = students.Any(s => s.Major == "Physics");
            
            Console.WriteLine($"   Any student with GPA >= 4.0: {hasHighGPA}");
            Console.WriteLine($"   All students are adults: {allAdults}");
            Console.WriteLine($"   Has Physics students: {hasPhysicsStudents}");
            
            Console.WriteLine("\n=== LINQ Demo Complete ===");
        }
    }
}
