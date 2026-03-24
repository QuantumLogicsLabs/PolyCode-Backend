# Classes and Objects

## 🎯 Learning Objectives

By the end of this lesson, you will:
- Understand the concept of classes and objects
- Learn how to define classes with fields, properties, and methods
- Understand constructors and their role
- Learn about static vs instance members
- Understand object lifecycle and memory management

## 🏗️ What are Classes and Objects?

### Class
A class is a blueprint or template for creating objects. It defines the properties and behaviors that objects of that type will have.

### Object
An object is an instance of a class. It's a concrete entity that exists in memory and has its own state and behavior.

## 📝 Defining a Class

### Basic Class Structure

```csharp
using System;

namespace BasicClass
{
    public class Person
    {
        // Fields (variables)
        private string firstName;
        private string lastName;
        private int age;
        
        // Properties (controlled access to fields)
        public string FirstName 
        { 
            get { return firstName; } 
            set { firstName = value; } 
        }
        
        public string LastName 
        { 
            get { return lastName; } 
            set { lastName = value; } 
        }
        
        public int Age 
        { 
            get { return age; } 
            set 
            { 
                if (value >= 0)
                    age = value; 
            } 
        }
        
        // Constructor
        public Person(string firstName, string lastName, int age)
        {
            this.firstName = firstName;
            this.lastName = lastName;
            this.age = age;
        }
        
        // Methods
        public string GetFullName()
        {
            return $"{firstName} {lastName}";
        }
        
        public void CelebrateBirthday()
        {
            age++;
            Console.WriteLine($"Happy Birthday {firstName}! You are now {age} years old.");
        }
        
        public void DisplayInfo()
        {
            Console.WriteLine($"Name: {GetFullName()}");
            Console.WriteLine($"Age: {age}");
        }
    }
    
    class Program
    {
        static void Main(string[] args)
        {
            // Creating objects
            Person person1 = new Person("John", "Doe", 30);
            Person person2 = new Person("Jane", "Smith", 25);
            
            // Using objects
            person1.DisplayInfo();
            person2.DisplayInfo();
            
            person1.CelebrateBirthday();
            
            // Accessing properties
            Console.WriteLine($"Person 1 name: {person1.FirstName}");
            person2.Age = 26;
            Console.WriteLine($"Person 2 new age: {person2.Age}");
        }
    }
}
```

## 🔧 Properties

Properties provide controlled access to class fields while maintaining encapsulation.

### Auto-Implemented Properties

```csharp
public class Student
{
    // Auto-implemented properties
    public int StudentId { get; set; }
    public string FirstName { get; set; }
    public string LastName { get; set; }
    public double GPA { get; set; }
    
    // Read-only property
    public string FullName => $"{FirstName} {LastName}";
    
    // Computed property
    public string AcademicStanding 
    { 
        get 
        { 
            if (GPA >= 3.5) return "Excellent";
            if (GPA >= 3.0) return "Good";
            if (GPA >= 2.0) return "Satisfactory";
            return "Needs Improvement";
        } 
    }
}
```

### Properties with Validation

```csharp
public class BankAccount
{
    private decimal balance;
    
    public string AccountNumber { get; }
    public string OwnerName { get; set; }
    
    public decimal Balance
    {
        get { return balance; }
        private set { balance = value; } // Private setter
    }
    
    public BankAccount(string accountNumber, string ownerName, decimal initialBalance)
    {
        AccountNumber = accountNumber;
        OwnerName = ownerName;
        
        if (initialBalance >= 0)
            balance = initialBalance;
        else
            throw new ArgumentException("Initial balance cannot be negative");
    }
    
    public void Deposit(decimal amount)
    {
        if (amount <= 0)
            throw new ArgumentException("Deposit amount must be positive");
            
        balance += amount;
        Console.WriteLine($"Deposited: {amount:C}. New balance: {balance:C}");
    }
    
    public bool Withdraw(decimal amount)
    {
        if (amount <= 0)
            throw new ArgumentException("Withdrawal amount must be positive");
            
        if (amount > balance)
        {
            Console.WriteLine("Insufficient funds");
            return false;
        }
        
        balance -= amount;
        Console.WriteLine($"Withdrew: {amount:C}. New balance: {balance:C}");
        return true;
    }
}
```

## 🏗️ Constructors

Constructors initialize objects when they are created.

### Multiple Constructors

```csharp
public class Product
{
    public int ProductId { get; }
    public string Name { get; set; }
    public decimal Price { get; set; }
    public string Category { get; set; }
    
    // Default constructor
    public Product()
    {
        ProductId = 0;
        Name = "Unknown";
        Price = 0.0m;
        Category = "General";
    }
    
    // Parameterized constructor
    public Product(int productId, string name, decimal price)
    {
        ProductId = productId;
        Name = name;
        Price = price;
        Category = "General";
    }
    
    // Full constructor
    public Product(int productId, string name, decimal price, string category)
    {
        ProductId = productId;
        Name = name;
        Price = price;
        Category = category;
    }
    
    // Copy constructor
    public Product(Product other)
    {
        ProductId = other.ProductId;
        Name = other.Name;
        Price = other.Price;
        Category = other.Category;
    }
    
    public void DisplayInfo()
    {
        Console.WriteLine($"Product ID: {ProductId}");
        Console.WriteLine($"Name: {Name}");
        Console.WriteLine($"Price: {Price:C}");
        Console.WriteLine($"Category: {Category}");
    }
}
```

### Constructor Chaining

```csharp
public class Employee
{
    public int EmployeeId { get; }
    public string FirstName { get; set; }
    public string LastName { get; set; }
    public string Department { get; set; }
    public decimal Salary { get; set; }
    
    // Main constructor
    public Employee(int employeeId, string firstName, string lastName, 
                   string department, decimal salary)
    {
        EmployeeId = employeeId;
        FirstName = firstName;
        LastName = lastName;
        Department = department;
        Salary = salary;
    }
    
    // Constructor with default department
    public Employee(int employeeId, string firstName, string lastName, decimal salary)
        : this(employeeId, firstName, lastName, "General", salary)
    {
    }
    
    // Constructor with default department and salary
    public Employee(int employeeId, string firstName, string lastName)
        : this(employeeId, firstName, lastName, "General", 30000m)
    {
    }
}
```

## 🔒 Static vs Instance Members

### Static Members
Static members belong to the class itself, not to any specific instance.

```csharp
public class MathHelper
{
    // Static field
    public static readonly double PI = 3.14159265359;
    
    // Static property
    public static string Version { get; } = "1.0.0";
    
    // Static method
    public static double Add(double a, double b)
    {
        return a + b;
    }
    
    public static double Multiply(double a, double b)
    {
        return a * b;
    }
    
    public static double CalculateCircleArea(double radius)
    {
        return PI * radius * radius;
    }
    
    // Static constructor
    static MathHelper()
    {
        Console.WriteLine("MathHelper class initialized");
    }
}

// Usage
double area = MathHelper.CalculateCircleArea(5.0);
Console.WriteLine($"Circle area: {area}");
Console.WriteLine($"MathHelper version: {MathHelper.Version}");
```

### Instance Members with Static Counters

```csharp
public class Counter
{
    // Static field to track all instances
    private static int instanceCount = 0;
    
    // Instance field
    private int value;
    
    public int Value 
    { 
        get { return value; } 
        set { this.value = value; } 
    }
    
    // Static property
    public static int InstanceCount 
    { 
        get { return instanceCount; } 
    }
    
    // Instance constructor
    public Counter()
    {
        instanceCount++;
        value = 0;
        Console.WriteLine($"Counter instance created. Total instances: {instanceCount}");
    }
    
    // Instance method
    public void Increment()
    {
        value++;
        Console.WriteLine($"Counter value: {value}");
    }
    
    // Static method
    public static void ResetInstanceCount()
    {
        instanceCount = 0;
        Console.WriteLine("Instance count reset to 0");
    }
}
```

## 🎯 Practical Examples

### Book Library System

```csharp
using System;
using System.Collections.Generic;

public class Book
{
    public string ISBN { get; }
    public string Title { get; set; }
    public string Author { get; set; }
    public int PublicationYear { get; set; }
    public bool IsAvailable { get; private set; }
    
    public Book(string isbn, string title, string author, int publicationYear)
    {
        ISBN = isbn;
        Title = title;
        Author = author;
        PublicationYear = publicationYear;
        IsAvailable = true;
    }
    
    public void Borrow()
    {
        if (IsAvailable)
        {
            IsAvailable = false;
            Console.WriteLine($"Book '{Title}' has been borrowed.");
        }
        else
        {
            Console.WriteLine($"Book '{Title}' is already borrowed.");
        }
    }
    
    public void Return()
    {
        if (!IsAvailable)
        {
            IsAvailable = true;
            Console.WriteLine($"Book '{Title}' has been returned.");
        }
        else
        {
            Console.WriteLine($"Book '{Title}' was not borrowed.");
        }
    }
    
    public void DisplayInfo()
    {
        string status = IsAvailable ? "Available" : "Borrowed";
        Console.WriteLine($"ISBN: {ISBN}");
        Console.WriteLine($"Title: {Title}");
        Console.WriteLine($"Author: {Author}");
        Console.WriteLine($"Year: {PublicationYear}");
        Console.WriteLine($"Status: {status}");
        Console.WriteLine();
    }
}

public class Library
{
    private List<Book> books;
    private string name;
    
    public string Name 
    { 
        get { return name; } 
        set { name = value; } 
    }
    
    public int BookCount => books.Count;
    
    public Library(string name)
    {
        this.name = name;
        books = new List<Book>();
    }
    
    public void AddBook(Book book)
    {
        books.Add(book);
        Console.WriteLine($"Book '{book.Title}' added to {name} library.");
    }
    
    public Book FindBookByISBN(string isbn)
    {
        return books.Find(b => b.ISBN == isbn);
    }
    
    public void DisplayAllBooks()
    {
        Console.WriteLine($"=== {name} Library Catalog ===");
        Console.WriteLine($"Total books: {BookCount}");
        Console.WriteLine();
        
        foreach (Book book in books)
        {
            book.DisplayInfo();
        }
    }
}

class Program
{
    static void Main(string[] args)
    {
        // Create library
        Library library = new Library("City Central");
        
        // Create books
        Book book1 = new Book("978-0-13-468599-1", "C# Programming", "John Smith", 2020);
        Book book2 = new Book("978-0-13-235088-4", "Design Patterns", "Erich Gamma", 1994);
        Book book3 = new Book("978-0-13-595705-9", "Clean Code", "Robert Martin", 2008);
        
        // Add books to library
        library.AddBook(book1);
        library.AddBook(book2);
        library.AddBook(book3);
        
        // Display catalog
        library.DisplayAllBooks();
        
        // Borrow and return operations
        Book foundBook = library.FindBookByISBN("978-0-13-595705-9");
        if (foundBook != null)
        {
            foundBook.Borrow();
            foundBook.Borrow(); // Try to borrow again
            foundBook.Return();
        }
    }
}
```

## 🎯 Practice Exercises

### Exercise 1: Car Class
Create a `Car` class with:
- Properties: Make, Model, Year, Speed, Fuel
- Methods: Accelerate(), Brake(), Refuel()
- Constructor that initializes all properties
- Method to display car information

### Exercise 2: Rectangle Class
Create a `Rectangle` class with:
- Properties: Width, Height (with validation)
- Read-only properties: Area, Perimeter
- Methods: Scale(double factor), IsSquare()
- Static method to create a square

### Exercise 3: Bank Account System
Create a banking system with:
- `Account` class with balance, account number, owner
- Methods for deposit, withdrawal, transfer
- Static method to generate account numbers
- Validation for all operations

## 💡 Best Practices

1. **Use properties instead of public fields** for better encapsulation
2. **Initialize objects in a valid state** using constructors
3. **Use static members** for data shared across all instances
4. **Implement validation** in property setters and methods
5. **Follow naming conventions** (PascalCase for public members, camelCase for private)
6. **Keep classes focused** on a single responsibility

## 🚀 Next Steps

Now that you understand classes and objects, let's learn about:

[Inheritance →](09-inheritance.md)

---

**You're building object-oriented thinking! Great progress! 🎯**
