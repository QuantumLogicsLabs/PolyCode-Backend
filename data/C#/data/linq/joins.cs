using System;
using System.Collections.Generic;
using System.Linq;

public class JoinsExample
{
    public static void Main(string[] args)
    {
        var employees = new List<Employee>
        {
            new Employee { Id = 1, Name = "John", DepartmentId = 1 },
            new Employee { Id = 2, Name = "Jane", DepartmentId = 2 },
            new Employee { Id = 3, Name = "Bob", DepartmentId = 1 },
            new Employee { Id = 4, Name = "Alice", DepartmentId = 3 }
        };
        
        var departments = new List<Department>
        {
            new Department { Id = 1, Name = "IT" },
            new Department { Id = 2, Name = "HR" },
            new Department { Id = 3, Name = "Finance" }
        };
        
        // Inner join
        var innerJoin = from emp in employees
                       join dept in departments on emp.DepartmentId equals dept.Id
                       select new { emp.Name, Department = dept.Name };
        
        Console.WriteLine("Inner Join:");
        foreach (var item in innerJoin)
        {
            Console.WriteLine($"{item.Name} - {item.Department}");
        }
        
        // Left join
        var leftJoin = from emp in employees
                      join dept in departments on emp.DepartmentId equals dept.Id into empDept
                      from dept in empDept.DefaultIfEmpty()
                      select new { emp.Name, Department = dept?.Name ?? "No Department" };
        
        Console.WriteLine("\nLeft Join:");
        foreach (var item in leftJoin)
        {
            Console.WriteLine($"{item.Name} - {item.Department}");
        }
    }
}

public class Employee
{
    public int Id { get; set; }
    public string Name { get; set; }
    public int DepartmentId { get; set; }
}

public class Department
{
    public int Id { get; set; }
    public string Name { get; set; }
}
