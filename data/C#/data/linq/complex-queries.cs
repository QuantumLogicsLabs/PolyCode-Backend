using System;
using System.Collections.Generic;
using System.Linq;

public class ComplexQueries
{
    public static void Main(string[] args)
    {
        var students = new List<Student>
        {
            new Student { Name = "Alice", Age = 20, Grade = "A", Subjects = new[] { "Math", "Physics" } },
            new Student { Name = "Bob", Age = 22, Grade = "B", Subjects = new[] { "Chemistry", "Biology" } },
            new Student { Name = "Charlie", Age = 21, Grade = "A", Subjects = new[] { "Math", "Computer Science" } }
        };
        
        // Group by grade
        var groupedByGrade = students.GroupBy(s => s.Grade);
        
        foreach (var group in groupedByGrade)
        {
            Console.WriteLine($"Grade {group.Key}:");
            foreach (var student in group)
            {
                Console.WriteLine($"  - {student.Name}");
            }
        }
        
        // Select many for flattening
        var allSubjects = students.SelectMany(s => s.Subjects).Distinct();
        
        Console.WriteLine("\nAll subjects:");
        foreach (var subject in allSubjects)
        {
            Console.WriteLine($"  - {subject}");
        }
    }
}

public class Student
{
    public string Name { get; set; }
    public int Age { get; set; }
    public string Grade { get; set; }
    public string[] Subjects { get; set; }
}
