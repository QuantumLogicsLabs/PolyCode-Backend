using System;
using System.Collections.Generic;
using System.Linq;

// Test-Driven Development (TDD) Example
// We'll build a simple shopping cart system using TDD approach

// Step 1: Write failing tests first
public class ShoppingCartTests
{
    public void CreateEmptyCart_ShouldHaveZeroItems()
    {
        // Arrange & Act
        var cart = new ShoppingCart();
        
        // Assert
        if (cart.ItemCount != 0)
            throw new Exception($"Expected 0 items, got {cart.ItemCount}");
        
        if (cart.TotalAmount != 0)
            throw new Exception($"Expected total 0, got {cart.TotalAmount}");
    }
    
    public void AddItem_ShouldIncreaseItemCountAndTotal()
    {
        // Arrange
        var cart = new ShoppingCart();
        var product = new Product("Laptop", 999.99m);
        
        // Act
        cart.AddItem(product, 1);
        
        // Assert
        if (cart.ItemCount != 1)
            throw new Exception($"Expected 1 item, got {cart.ItemCount}");
        
        if (Math.Abs(cart.TotalAmount - 999.99m) > 0.01m)
            throw new Exception($"Expected total 999.99, got {cart.TotalAmount}");
    }
    
    public void AddMultipleItems_ShouldCalculateCorrectTotal()
    {
        // Arrange
        var cart = new ShoppingCart();
        var laptop = new Product("Laptop", 999.99m);
        var mouse = new Product("Mouse", 25.50m);
        
        // Act
        cart.AddItem(laptop, 1);
        cart.AddItem(mouse, 2);
        
        // Assert
        if (cart.ItemCount != 3)
            throw new Exception($"Expected 3 items, got {cart.ItemCount}");
        
        var expectedTotal = 999.99m + (25.50m * 2);
        if (Math.Abs(cart.TotalAmount - expectedTotal) > 0.01m)
            throw new Exception($"Expected total {expectedTotal}, got {cart.TotalAmount}");
    }
    
    public void RemoveItem_ShouldDecreaseItemCountAndTotal()
    {
        // Arrange
        var cart = new ShoppingCart();
        var product = new Product("Laptop", 999.99m);
        cart.AddItem(product, 2);
        
        // Act
        cart.RemoveItem(product, 1);
        
        // Assert
        if (cart.ItemCount != 1)
            throw new Exception($"Expected 1 item, got {cart.ItemCount}");
        
        if (Math.Abs(cart.TotalAmount - 999.99m) > 0.01m)
            throw new Exception($"Expected total 999.99, got {cart.TotalAmount}");
    }
    
    public void RemoveAllItems_ShouldEmptyCart()
    {
        // Arrange
        var cart = new ShoppingCart();
        var product = new Product("Laptop", 999.99m);
        cart.AddItem(product, 2);
        
        // Act
        cart.RemoveItem(product, 2);
        
        // Assert
        if (cart.ItemCount != 0)
            throw new Exception($"Expected 0 items, got {cart.ItemCount}");
        
        if (cart.TotalAmount != 0)
            throw new Exception($"Expected total 0, got {cart.TotalAmount}");
    }
    
    public void AddSameItemMultipleTimes_ShouldAccumulateQuantity()
    {
        // Arrange
        var cart = new ShoppingCart();
        var product = new Product("Laptop", 999.99m);
        
        // Act
        cart.AddItem(product, 1);
        cart.AddItem(product, 2);
        
        // Assert
        if (cart.ItemCount != 3)
            throw new Exception($"Expected 3 items, got {cart.ItemCount}");
        
        if (Math.Abs(cart.TotalAmount - (999.99m * 3)) > 0.01m)
            throw new Exception($"Expected total {(999.99m * 3)}, got {cart.TotalAmount}");
    }
    
    public void GetItems_ShouldReturnAllCartItems()
    {
        // Arrange
        var cart = new ShoppingCart();
        var laptop = new Product("Laptop", 999.99m);
        var mouse = new Product("Mouse", 25.50m);
        
        cart.AddItem(laptop, 1);
        cart.AddItem(mouse, 2);
        
        // Act
        var items = cart.GetItems();
        
        // Assert
        if (items.Count != 2)
            throw new Exception($"Expected 2 unique items, got {items.Count}");
        
        var laptopItem = items.FirstOrDefault(i => i.Product.Name == "Laptop");
        if (laptopItem == null || laptopItem.Quantity != 1)
            throw new Exception("Laptop item not found with correct quantity");
        
        var mouseItem = items.FirstOrDefault(i => i.Product.Name == "Mouse");
        if (mouseItem == null || mouseItem.Quantity != 2)
            throw new Exception("Mouse item not found with correct quantity");
    }
    
    public void ApplyDiscount_ShouldReduceTotal()
    {
        // Arrange
        var cart = new ShoppingCart();
        var product = new Product("Laptop", 1000m);
        cart.AddItem(product, 1);
        
        // Act
        cart.ApplyDiscount(0.1m); // 10% discount
        
        // Assert
        var expectedTotal = 1000m * 0.9m;
        if (Math.Abs(cart.TotalAmount - expectedTotal) > 0.01m)
            throw new Exception($"Expected total {expectedTotal}, got {cart.TotalAmount}");
    }
    
    public void ClearCart_ShouldRemoveAllItems()
    {
        // Arrange
        var cart = new ShoppingCart();
        var product = new Product("Laptop", 999.99m);
        cart.AddItem(product, 1);
        
        // Act
        cart.Clear();
        
        // Assert
        if (cart.ItemCount != 0)
            throw new Exception($"Expected 0 items, got {cart.ItemCount}");
        
        if (cart.TotalAmount != 0)
            throw new Exception($"Expected total 0, got {cart.TotalAmount}");
    }
}

// Step 2: Implement the minimal code to make tests pass
public class Product
{
    public string Name { get; }
    public decimal Price { get; }
    
    public Product(string name, decimal price)
    {
        if (string.IsNullOrWhiteSpace(name))
            throw new ArgumentException("Product name cannot be empty", nameof(name));
        
        if (price < 0)
            throw new ArgumentException("Product price cannot be negative", nameof(price));
        
        Name = name;
        Price = price;
    }
}

public class CartItem
{
    public Product Product { get; }
    public int Quantity { get; set; }
    public decimal TotalPrice => Product.Price * Quantity;
    
    public CartItem(Product product, int quantity)
    {
        Product = product ?? throw new ArgumentNullException(nameof(product));
        Quantity = quantity > 0 ? quantity : throw new ArgumentException("Quantity must be positive", nameof(quantity));
    }
}

public class ShoppingCart
{
    private readonly Dictionary<string, CartItem> _items = new();
    private decimal _discountPercentage = 0m;
    
    public int ItemCount => _items.Values.Sum(item => item.Quantity);
    public decimal TotalAmount => CalculateTotal();
    
    public void AddItem(Product product, int quantity)
    {
        if (product == null)
            throw new ArgumentNullException(nameof(product));
        
        if (quantity <= 0)
            throw new ArgumentException("Quantity must be positive", nameof(quantity));
        
        var key = product.Name;
        if (_items.ContainsKey(key))
        {
            _items[key].Quantity += quantity;
        }
        else
        {
            _items[key] = new CartItem(product, quantity);
        }
    }
    
    public void RemoveItem(Product product, int quantity)
    {
        if (product == null)
            throw new ArgumentNullException(nameof(product));
        
        if (quantity <= 0)
            throw new ArgumentException("Quantity must be positive", nameof(quantity));
        
        var key = product.Name;
        if (_items.ContainsKey(key))
        {
            var item = _items[key];
            if (quantity >= item.Quantity)
            {
                _items.Remove(key);
            }
            else
            {
                item.Quantity -= quantity;
            }
        }
    }
    
    public void ApplyDiscount(decimal discountPercentage)
    {
        if (discountPercentage < 0 || discountPercentage > 1)
            throw new ArgumentException("Discount must be between 0 and 1", nameof(discountPercentage));
        
        _discountPercentage = discountPercentage;
    }
    
    public void Clear()
    {
        _items.Clear();
        _discountPercentage = 0m;
    }
    
    public List<CartItem> GetItems()
    {
        return _items.Values.ToList();
    }
    
    private decimal CalculateTotal()
    {
        var subtotal = _items.Values.Sum(item => item.TotalPrice);
        return subtotal * (1 - _discountPercentage);
    }
}

// TDD Example: Building a string validator
public class StringValidatorTests
{
    public void ValidateEmptyString_ShouldReturnFalse()
    {
        // Arrange
        var validator = new StringValidator();
        
        // Act
        var result = validator.IsValid("");
        
        // Assert
        if (result)
            throw new Exception("Empty string should be invalid");
    }
    
    public void ValidateNullString_ShouldReturnFalse()
    {
        // Arrange
        var validator = new StringValidator();
        
        // Act
        var result = validator.IsValid(null);
        
        // Assert
        if (result)
            throw new Exception("Null string should be invalid");
    }
    
    public void ValidateValidString_ShouldReturnTrue()
    {
        // Arrange
        var validator = new StringValidator();
        
        // Act
        var result = validator.IsValid("ValidString123");
        
        // Assert
        if (!result)
            throw new Exception("Valid string should be valid");
    }
    
    public void ValidateStringWithSpaces_ShouldReturnFalse()
    {
        // Arrange
        var validator = new StringValidator();
        
        // Act
        var result = validator.IsValid("Invalid String");
        
        // Assert
        if (result)
            throw new Exception("String with spaces should be invalid");
    }
    
    public void ValidateStringWithSpecialChars_ShouldReturnFalse()
    {
        // Arrange
        var validator = new StringValidator();
        
        // Act
        var result = validator.IsValid("Invalid@String");
        
        // Assert
        if (result)
            throw new Exception("String with special characters should be invalid");
    }
    
    public void ValidateStringTooLong_ShouldReturnFalse()
    {
        // Arrange
        var validator = new StringValidator();
        var longString = new string('a', 51); // 51 characters
        
        // Act
        var result = validator.IsValid(longString);
        
        // Assert
        if (result)
            throw new Exception("String too long should be invalid");
    }
    
    public void ValidateStringExactlyMaxLength_ShouldReturnTrue()
    {
        // Arrange
        var validator = new StringValidator();
        var maxLengthString = new string('a', 50); // 50 characters
        
        // Act
        var result = validator.IsValid(maxLengthString);
        
        // Assert
        if (!result)
            throw new Exception("String at max length should be valid");
    }
}

// Implementation of StringValidator
public class StringValidator
{
    private const int MaxLength = 50;
    
    public bool IsValid(string input)
    {
        if (string.IsNullOrEmpty(input))
            return false;
        
        if (input.Length > MaxLength)
            return false;
        
        if (input.Contains(" "))
            return false;
        
        // Check for alphanumeric only
        return input.All(char.IsLetterOrDigit);
    }
}

// TDD Example: Building a simple calculator
public class CalculatorTests
{
    public void AddTwoPositiveNumbers_ShouldReturnCorrectSum()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Add(5, 3);
        
        // Assert
        if (result != 8)
            throw new Exception($"Expected 8, got {result}");
    }
    
    public void AddPositiveAndNegativeNumber_ShouldReturnCorrectResult()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Add(10, -3);
        
        // Assert
        if (result != 7)
            throw new Exception($"Expected 7, got {result}");
    }
    
    public void AddTwoNegativeNumbers_ShouldReturnCorrectSum()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Add(-5, -3);
        
        // Assert
        if (result != -8)
            throw new Exception($"Expected -8, got {result}");
    }
    
    public void SubtractSmallerFromLarger_ShouldReturnPositiveResult()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Subtract(10, 3);
        
        // Assert
        if (result != 7)
            throw new Exception($"Expected 7, got {result}");
    }
    
    public void SubtractLargerFromSmaller_ShouldReturnNegativeResult()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Subtract(3, 10);
        
        // Assert
        if (result != -7)
            throw new Exception($"Expected -7, got {result}");
    }
    
    public void MultiplyTwoPositiveNumbers_ShouldReturnCorrectProduct()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Multiply(4, 5);
        
        // Assert
        if (result != 20)
            throw new Exception($"Expected 20, got {result}");
    }
    
    public void MultiplyByZero_ShouldReturnZero()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Multiply(10, 0);
        
        // Assert
        if (result != 0)
            throw new Exception($"Expected 0, got {result}");
    }
    
    public void MultiplyPositiveAndNegative_ShouldReturnNegativeResult()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Multiply(5, -3);
        
        // Assert
        if (result != -15)
            throw new Exception($"Expected -15, got {result}");
    }
    
    public void DivideTwoPositiveNumbers_ShouldReturnCorrectQuotient()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Divide(15, 3);
        
        // Assert
        if (result != 5)
            throw new Exception($"Expected 5, got {result}");
    }
    
    public void DivideByZero_ShouldThrowException()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act & Assert
        try
        {
            calculator.Divide(10, 0);
            throw new Exception("Expected DivideByZeroException was not thrown");
        }
        catch (DivideByZeroException)
        {
            // Expected exception - test passes
        }
    }
}

// Implementation of Calculator
public class Calculator
{
    public int Add(int a, int b) => a + b;
    
    public int Subtract(int a, int b) => a - b;
    
    public int Multiply(int a, int b) => a * b;
    
    public int Divide(int a, int b)
    {
        if (b == 0)
            throw new DivideByZeroException("Cannot divide by zero");
        
        return a / b;
    }
}

// TDD Test Runner
public class TDDTestRunner
{
    public static void RunAllTests()
    {
        Console.WriteLine("=== TDD Test Results ===");
        
        int passedTests = 0;
        int totalTests = 0;
        
        // Shopping Cart Tests
        Console.WriteLine("\n--- Shopping Cart Tests ---");
        var cartTests = new ShoppingCartTests();
        passedTests += RunTestGroup(cartTests, new[]
        {
            nameof(ShoppingCartTests.CreateEmptyCart_ShouldHaveZeroItems),
            nameof(ShoppingCartTests.AddItem_ShouldIncreaseItemCountAndTotal),
            nameof(ShoppingCartTests.AddMultipleItems_ShouldCalculateCorrectTotal),
            nameof(ShoppingCartTests.RemoveItem_ShouldDecreaseItemCountAndTotal),
            nameof(ShoppingCartTests.RemoveAllItems_ShouldEmptyCart),
            nameof(ShoppingCartTests.AddSameItemMultipleTimes_ShouldAccumulateQuantity),
            nameof(ShoppingCartTests.GetItems_ShouldReturnAllCartItems),
            nameof(ShoppingCartTests.ApplyDiscount_ShouldReduceTotal),
            nameof(ShoppingCartTests.ClearCart_ShouldRemoveAllItems)
        });
        totalTests += 9;
        
        // String Validator Tests
        Console.WriteLine("\n--- String Validator Tests ---");
        var validatorTests = new StringValidatorTests();
        passedTests += RunTestGroup(validatorTests, new[]
        {
            nameof(StringValidatorTests.ValidateEmptyString_ShouldReturnFalse),
            nameof(StringValidatorTests.ValidateNullString_ShouldReturnFalse),
            nameof(StringValidatorTests.ValidateValidString_ShouldReturnTrue),
            nameof(StringValidatorTests.ValidateStringWithSpaces_ShouldReturnFalse),
            nameof(StringValidatorTests.ValidateStringWithSpecialChars_ShouldReturnFalse),
            nameof(StringValidatorTests.ValidateStringTooLong_ShouldReturnFalse),
            nameof(StringValidatorTests.ValidateStringExactlyMaxLength_ShouldReturnTrue)
        });
        totalTests += 7;
        
        // Calculator Tests
        Console.WriteLine("\n--- Calculator Tests ---");
        var calculatorTests = new CalculatorTests();
        passedTests += RunTestGroup(calculatorTests, new[]
        {
            nameof(CalculatorTests.AddTwoPositiveNumbers_ShouldReturnCorrectSum),
            nameof(CalculatorTests.AddPositiveAndNegativeNumber_ShouldReturnCorrectResult),
            nameof(CalculatorTests.AddTwoNegativeNumbers_ShouldReturnCorrectSum),
            nameof(CalculatorTests.SubtractSmallerFromLarger_ShouldReturnPositiveResult),
            nameof(CalculatorTests.SubtractLargerFromSmaller_ShouldReturnNegativeResult),
            nameof(CalculatorTests.MultiplyTwoPositiveNumbers_ShouldReturnCorrectProduct),
            nameof(CalculatorTests.MultiplyByZero_ShouldReturnZero),
            nameof(CalculatorTests.MultiplyPositiveAndNegative_ShouldReturnNegativeResult),
            nameof(CalculatorTests.DivideTwoPositiveNumbers_ShouldReturnCorrectQuotient),
            nameof(CalculatorTests.DivideByZero_ShouldThrowException)
        });
        totalTests += 10;
        
        Console.WriteLine($"\n=== Overall Results ===");
        Console.WriteLine($"Tests Passed: {passedTests}/{totalTests}");
        Console.WriteLine($"Success Rate: {(double)passedTests / totalTests * 100:F1}%");
    }
    
    private static int RunTestGroup(object testInstance, string[] testMethods)
    {
        int passedTests = 0;
        
        foreach (var testMethod in testMethods)
        {
            try
            {
                var method = testInstance.GetType().GetMethod(testMethod);
                method?.Invoke(testInstance, null);
                Console.WriteLine($"✓ {testMethod} - PASSED");
                passedTests++;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"✗ {testMethod} - FAILED: {ex.Message}");
            }
        }
        
        return passedTests;
    }
}

// Program to demonstrate TDD
public class TestDrivenDevelopmentProgram
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Test-Driven Development Demo ===");
        Console.WriteLine("This demo shows the TDD process where tests are written first,");
        Console.WriteLine("then minimal code is implemented to make them pass.");
        
        TDDTestRunner.RunAllTests();
        
        Console.WriteLine("\n=== TDD Process Summary ===");
        Console.WriteLine("1. RED: Write a failing test");
        Console.WriteLine("2. GREEN: Write minimal code to make test pass");
        Console.WriteLine("3. REFACTOR: Improve code while keeping tests green");
        Console.WriteLine("4. Repeat for each feature/requirement");
    }
}
