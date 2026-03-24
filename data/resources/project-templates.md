# C# Project Templates

This document provides templates for common C# project types that you can use as starting points.

## 🖥️ Console Application Template

### Basic Console App
```csharp
// Program.cs
using System;

namespace MyConsoleApp
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Welcome to My Console Application!");
            Console.WriteLine("=====================================");
            
            // Your application logic here
            
            Console.WriteLine("\nPress any key to exit...");
            Console.ReadKey();
        }
    }
}
```

### Console App with Menu System
```csharp
// Program.cs
using System;

namespace ConsoleMenuApp
{
    class Program
    {
        static void Main(string[] args)
        {
            bool running = true;
            
            while (running)
            {
                DisplayMenu();
                string choice = Console.ReadLine();
                
                switch (choice)
                {
                    case "1":
                        Option1();
                        break;
                    case "2":
                        Option2();
                        break;
                    case "3":
                        Option3();
                        break;
                    case "4":
                        running = false;
                        break;
                    default:
                        Console.WriteLine("Invalid choice. Please try again.");
                        break;
                }
                
                if (running)
                {
                    Console.WriteLine("\nPress Enter to continue...");
                    Console.ReadLine();
                }
            }
        }
        
        static void DisplayMenu()
        {
            Console.Clear();
            Console.WriteLine("=== Main Menu ===");
            Console.WriteLine("1. Option 1");
            Console.WriteLine("2. Option 2");
            Console.WriteLine("3. Option 3");
            Console.WriteLine("4. Exit");
            Console.Write("Enter your choice: ");
        }
        
        static void Option1()
        {
            Console.WriteLine("Option 1 selected");
            // Implement option 1 logic
        }
        
        static void Option2()
        {
            Console.WriteLine("Option 2 selected");
            // Implement option 2 logic
        }
        
        static void Option3()
        {
            Console.WriteLine("Option 3 selected");
            // Implement option 3 logic
        }
    }
}
```

## 📚 Class Library Template

### Basic Class Library
```csharp
// MyLibrary/Calculator.cs
namespace MyLibrary
{
    public class Calculator
    {
        public double Add(double a, double b) => a + b;
        public double Subtract(double a, double b) => a - b;
        public double Multiply(double a, double b) => a * b;
        public double Divide(double a, double b)
        {
            if (b == 0)
                throw new System.DivideByZeroException("Cannot divide by zero");
            return a / b;
        }
        
        public double Power(double baseNum, double exponent)
        {
            return System.Math.Pow(baseNum, exponent);
        }
    }
}
```

### Data Processing Library
```csharp
// MyLibrary/DataProcessor.cs
using System;
using System.Collections.Generic;
using System.Linq;

namespace MyLibrary
{
    public class DataProcessor
    {
        public List<T> Filter<T>(IEnumerable<T> data, Func<T, bool> predicate)
        {
            return data.Where(predicate).ToList();
        }
        
        public Dictionary<TKey, List<T>> GroupBy<T, TKey>(
            IEnumerable<T> data, Func<T, TKey> keySelector)
        {
            return data.GroupBy(keySelector).ToDictionary(g => g.Key, g => g.ToList());
        }
        
        public double CalculateAverage(IEnumerable<double> numbers)
        {
            return numbers.Average();
        }
        
        public T FindMax<T>(IEnumerable<T> data, Func<T, IComparable> selector)
        {
            return data.OrderByDescending(selector).FirstOrDefault();
        }
    }
}
```

## 🌐 Web API Template

### Basic Web API Controller
```csharp
// Controllers/ProductsController.cs
using Microsoft.AspNetCore.Mvc;
using System.Collections.Generic;
using System.Linq;

namespace MyWebApi.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class ProductsController : ControllerBase
    {
        private static List<Product> _products = new List<Product>
        {
            new Product { Id = 1, Name = "Laptop", Price = 999.99m },
            new Product { Id = 2, Name = "Mouse", Price = 19.99m },
            new Product { Id = 3, Name = "Keyboard", Price = 49.99m }
        };
        
        [HttpGet]
        public ActionResult<IEnumerable<Product>> GetProducts()
        {
            return Ok(_products);
        }
        
        [HttpGet("{id}")]
        public ActionResult<Product> GetProduct(int id)
        {
            var product = _products.FirstOrDefault(p => p.Id == id);
            if (product == null)
                return NotFound();
            
            return Ok(product);
        }
        
        [HttpPost]
        public ActionResult<Product> CreateProduct(Product product)
        {
            product.Id = _products.Max(p => p.Id) + 1;
            _products.Add(product);
            
            return CreatedAtAction(nameof(GetProduct), new { id = product.Id }, product);
        }
        
        [HttpPut("{id}")]
        public IActionResult UpdateProduct(int id, Product product)
        {
            var existingProduct = _products.FirstOrDefault(p => p.Id == id);
            if (existingProduct == null)
                return NotFound();
            
            existingProduct.Name = product.Name;
            existingProduct.Price = product.Price;
            
            return NoContent();
        }
        
        [HttpDelete("{id}")]
        public IActionResult DeleteProduct(int id)
        {
            var product = _products.FirstOrDefault(p => p.Id == id);
            if (product == null)
                return NotFound();
            
            _products.Remove(product);
            return NoContent();
        }
    }
    
    public class Product
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public decimal Price { get; set; }
    }
}
```

## 🎮 Game Development Template

### Simple Game Class
```csharp
// Game/Game.cs
using System;
using System.Collections.Generic;

namespace SimpleGame
{
    public class Game
    {
        private bool _isRunning;
        private Player _player;
        private List<Enemy> _enemies;
        
        public Game()
        {
            _player = new Player { Health = 100, Score = 0 };
            _enemies = new List<Enemy>();
            _isRunning = true;
        }
        
        public void Start()
        {
            Console.WriteLine("Game Started!");
            InitializeEnemies();
            
            while (_isRunning)
            {
                Update();
                Render();
                HandleInput();
                
                if (_player.Health <= 0)
                {
                    GameOver();
                    _isRunning = false;
                }
            }
        }
        
        private void InitializeEnemies()
        {
            Random random = new Random();
            for (int i = 0; i < 5; i++)
            {
                _enemies.Add(new Enemy 
                { 
                    Health = random.Next(20, 50),
                    Damage = random.Next(5, 15)
                });
            }
        }
        
        private void Update()
        {
            // Update game logic
            foreach (var enemy in _enemies)
            {
                enemy.Update();
            }
        }
        
        private void Render()
        {
            Console.Clear();
            Console.WriteLine($"=== Game State ===");
            Console.WriteLine($"Player Health: {_player.Health}");
            Console.WriteLine($"Player Score: {_player.Score}");
            Console.WriteLine($"Enemies: {_enemies.Count}");
            Console.WriteLine();
            Console.WriteLine("Commands: [A]ttack, [H]eal, [Q]uit");
        }
        
        private void HandleInput()
        {
            string input = Console.ReadLine()?.ToUpper();
            
            switch (input)
            {
                case "A":
                    Attack();
                    break;
                case "H":
                    Heal();
                    break;
                case "Q":
                    _isRunning = false;
                    break;
            }
        }
        
        private void Attack()
        {
            if (_enemies.Count > 0)
            {
                Enemy target = _enemies[0];
                target.Health -= 10;
                Console.WriteLine($"Attacked enemy for 10 damage!");
                
                if (target.Health <= 0)
                {
                    _enemies.Remove(target);
                    _player.Score += 10;
                    Console.WriteLine("Enemy defeated! +10 points");
                }
            }
            else
            {
                Console.WriteLine("No enemies to attack!");
            }
        }
        
        private void Heal()
        {
            _player.Health = Math.Min(_player.Health + 20, 100);
            Console.WriteLine("Healed for 20 HP!");
        }
        
        private void GameOver()
        {
            Console.Clear();
            Console.WriteLine("=== GAME OVER ===");
            Console.WriteLine($"Final Score: {_player.Score}");
            Console.WriteLine("Thanks for playing!");
        }
    }
    
    public class Player
    {
        public int Health { get; set; }
        public int Score { get; set; }
    }
    
    public class Enemy
    {
        public int Health { get; set; }
        public int Damage { get; set; }
        
        public void Update()
        {
            // Enemy AI logic here
        }
    }
}
```

## 📊 Data Management Template

### Repository Pattern
```csharp
// Repositories/IRepository.cs
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;

namespace DataManagement
{
    public interface IRepository<T> where T : class
    {
        IEnumerable<T> GetAll();
        T GetById(int id);
        IEnumerable<T> Find(Expression<Func<T, bool>> predicate);
        void Add(T entity);
        void Update(T entity);
        void Delete(T entity);
        void Save();
    }
    
    public class Repository<T> : IRepository<T> where T : class
    {
        private readonly List<T> _data;
        
        public Repository()
        {
            _data = new List<T>();
        }
        
        public IEnumerable<T> GetAll() => _data;
        
        public T GetById(int id)
        {
            return _data.FirstOrDefault();
        }
        
        public IEnumerable<T> Find(Expression<Func<T, bool>> predicate)
        {
            return _data.AsQueryable().Where(predicate);
        }
        
        public void Add(T entity)
        {
            _data.Add(entity);
        }
        
        public void Update(T entity)
        {
            // Update logic here
        }
        
        public void Delete(T entity)
        {
            _data.Remove(entity);
        }
        
        public void Save()
        {
            // Save logic here
        }
    }
}
```

### Service Layer Template
```csharp
// Services/ProductService.cs
using System;
using System.Collections.Generic;

namespace DataManagement
{
    public class ProductService
    {
        private readonly IRepository<Product> _repository;
        
        public ProductService(IRepository<Product> repository)
        {
            _repository = repository;
        }
        
        public IEnumerable<Product> GetAllProducts()
        {
            return _repository.GetAll();
        }
        
        public Product GetProductById(int id)
        {
            return _repository.GetById(id);
        }
        
        public IEnumerable<Product> GetProductsByCategory(string category)
        {
            return _repository.Find(p => p.Category == category);
        }
        
        public void AddProduct(Product product)
        {
            ValidateProduct(product);
            _repository.Add(product);
            _repository.Save();
        }
        
        public void UpdateProduct(Product product)
        {
            ValidateProduct(product);
            _repository.Update(product);
            _repository.Save();
        }
        
        public void DeleteProduct(int id)
        {
            var product = _repository.GetById(id);
            if (product != null)
            {
                _repository.Delete(product);
                _repository.Save();
            }
        }
        
        private void ValidateProduct(Product product)
        {
            if (string.IsNullOrWhiteSpace(product.Name))
                throw new ArgumentException("Product name is required");
            
            if (product.Price <= 0)
                throw new ArgumentException("Price must be positive");
        }
    }
    
    public class Product
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public decimal Price { get; set; }
        public string Category { get; set; }
    }
}
```

## 🔧 Utility Templates

### Configuration Manager
```csharp
// Utils/ConfigManager.cs
using System;
using System.IO;
using System.Collections.Generic;

namespace Utils
{
    public class ConfigManager
    {
        private readonly Dictionary<string, string> _settings;
        
        public ConfigManager(string configPath = "config.txt")
        {
            _settings = new Dictionary<string, string>();
            LoadConfig(configPath);
        }
        
        private void LoadConfig(string configPath)
        {
            if (File.Exists(configPath))
            {
                foreach (string line in File.ReadAllLines(configPath))
                {
                    if (!string.IsNullOrWhiteSpace(line) && !line.StartsWith("#"))
                    {
                        string[] parts = line.Split('=', 2);
                        if (parts.Length == 2)
                        {
                            _settings[parts[0].Trim()] = parts[1].Trim();
                        }
                    }
                }
            }
        }
        
        public string GetSetting(string key, string defaultValue = "")
        {
            return _settings.TryGetValue(key, out string value) ? value : defaultValue;
        }
        
        public T GetSetting<T>(string key, T defaultValue)
        {
            string value = GetSetting(key);
            if (string.IsNullOrEmpty(value))
                return defaultValue;
            
            try
            {
                return (T)Convert.ChangeType(value, typeof(T));
            }
            catch
            {
                return defaultValue;
            }
        }
        
        public void SetSetting(string key, string value)
        {
            _settings[key] = value;
        }
    }
}
```

### Logger Template
```csharp
// Utils/Logger.cs
using System;
using System.IO;

namespace Utils
{
    public enum LogLevel
    {
        Info,
        Warning,
        Error,
        Debug
    }
    
    public class Logger
    {
        private readonly string _logPath;
        private readonly LogLevel _minLevel;
        
        public Logger(string logPath = "app.log", LogLevel minLevel = LogLevel.Info)
        {
            _logPath = logPath;
            _minLevel = minLevel;
        }
        
        public void Log(string message, LogLevel level = LogLevel.Info)
        {
            if (level >= _minLevel)
            {
                string logEntry = $"[{DateTime.Now:yyyy-MM-dd HH:mm:ss}] [{level}] {message}";
                
                Console.WriteLine(logEntry);
                
                try
                {
                    File.AppendAllText(_logPath, logEntry + Environment.NewLine);
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"Failed to write to log file: {ex.Message}");
                }
            }
        }
        
        public void Info(string message) => Log(message, LogLevel.Info);
        public void Warning(string message) => Log(message, LogLevel.Warning);
        public void Error(string message) => Log(message, LogLevel.Error);
        public void Debug(string message) => Log(message, LogLevel.Debug);
    }
}
```

## 🚀 How to Use These Templates

1. **Copy the relevant template code** to your project
2. **Customize namespaces** to match your project structure
3. **Add necessary NuGet packages** (for web APIs, etc.)
4. **Implement business logic** specific to your needs
5. **Follow the established patterns** for consistency

---

**These templates provide a solid foundation for your C# projects! 🏗️**
