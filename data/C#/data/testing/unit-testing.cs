using System;
using System.Collections.Generic;

// Classes to be tested
public class Calculator
{
    public int Add(int a, int b) => a + b;
    
    public int Subtract(int a, int b) => a - b;
    
    public int Multiply(int a, int b) => a * b;
    
    public double Divide(int a, int b)
    {
        if (b == 0)
            throw new DivideByZeroException("Cannot divide by zero");
        return (double)a / b;
    }
    
    public bool IsEven(int number) => number % 2 == 0;
}

public class StringHelper
{
    public string Reverse(string input)
    {
        if (string.IsNullOrEmpty(input))
            return input;
        
        char[] charArray = input.ToCharArray();
        Array.Reverse(charArray);
        return new string(charArray);
    }
    
    public bool IsPalindrome(string input)
    {
        if (string.IsNullOrEmpty(input))
            return true;
        
        var normalized = input.ToLower().Replace(" ", "");
        return normalized == Reverse(normalized);
    }
    
    public int CountWords(string text)
    {
        if (string.IsNullOrWhiteSpace(text))
            return 0;
        
        return text.Split(new[] { ' ', '\t', '\n' }, StringSplitOptions.RemoveEmptyEntries).Length;
    }
}

public class BankAccount
{
    public string AccountNumber { get; }
    public decimal Balance { get; private set; }
    
    public BankAccount(string accountNumber, decimal initialBalance = 0)
    {
        AccountNumber = accountNumber ?? throw new ArgumentNullException(nameof(accountNumber));
        Balance = initialBalance;
    }
    
    public void Deposit(decimal amount)
    {
        if (amount <= 0)
            throw new ArgumentException("Deposit amount must be positive", nameof(amount));
        
        Balance += amount;
    }
    
    public void Withdraw(decimal amount)
    {
        if (amount <= 0)
            throw new ArgumentException("Withdrawal amount must be positive", nameof(amount));
        
        if (amount > Balance)
            throw new InvalidOperationException("Insufficient funds");
        
        Balance -= amount;
    }
    
    public void Transfer(decimal amount, BankAccount targetAccount)
    {
        if (targetAccount == null)
            throw new ArgumentNullException(nameof(targetAccount));
        
        Withdraw(amount);
        targetAccount.Deposit(amount);
    }
}

// Mock classes for testing
public interface IDataRepository
{
    string GetData(int id);
    void SaveData(int id, string data);
}

public class MockDataRepository : IDataRepository
{
    private readonly Dictionary<int, string> _data = new();
    
    public string GetData(int id)
    {
        return _data.TryGetValue(id, out var value) ? value : null;
    }
    
    public void SaveData(int id, string data)
    {
        _data[id] = data;
    }
}

public class DataService
{
    private readonly IDataRepository _repository;
    
    public DataService(IDataRepository repository)
    {
        _repository = repository ?? throw new ArgumentNullException(nameof(repository));
    }
    
    public string ProcessData(int id)
    {
        var data = _repository.GetData(id);
        
        if (string.IsNullOrEmpty(data))
        {
            return "No data found";
        }
        
        return data.ToUpper();
    }
    
    public void UpdateData(int id, string data)
    {
        if (string.IsNullOrWhiteSpace(data))
        {
            throw new ArgumentException("Data cannot be null or empty", nameof(data));
        }
        
        _repository.SaveData(id, data);
    }
}

// Example test classes (would typically use a testing framework like xUnit, NUnit, or MSTest)
public class CalculatorTests
{
    public void Add_TwoPositiveNumbers_ReturnsCorrectSum()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Add(3, 5);
        
        // Assert
        if (result != 8)
            throw new Exception($"Expected 8, but got {result}");
    }
    
    public void Add_PositiveAndNegativeNumber_ReturnsCorrectResult()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Add(10, -3);
        
        // Assert
        if (result != 7)
            throw new Exception($"Expected 7, but got {result}");
    }
    
    public void Divide_ValidDivision_ReturnsCorrectQuotient()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.Divide(10, 2);
        
        // Assert
        if (result != 5.0)
            throw new Exception($"Expected 5.0, but got {result}");
    }
    
    public void Divide_ByZero_ThrowsDivideByZeroException()
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
    
    public void IsEven_EvenNumber_ReturnsTrue()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.IsEven(4);
        
        // Assert
        if (!result)
            throw new Exception("Expected true for even number");
    }
    
    public void IsEven_OddNumber_ReturnsFalse()
    {
        // Arrange
        var calculator = new Calculator();
        
        // Act
        var result = calculator.IsEven(3);
        
        // Assert
        if (result)
            throw new Exception("Expected false for odd number");
    }
}

public class StringHelperTests
{
    public void Reverse_ValidString_ReturnsReversedString()
    {
        // Arrange
        var stringHelper = new StringHelper();
        string input = "hello";
        
        // Act
        var result = stringHelper.Reverse(input);
        
        // Assert
        if (result != "olleh")
            throw new Exception($"Expected 'olleh', but got '{result}'");
    }
    
    public void Reverse_EmptyString_ReturnsEmptyString()
    {
        // Arrange
        var stringHelper = new StringHelper();
        
        // Act
        var result = stringHelper.Reverse("");
        
        // Assert
        if (result != "")
            throw new Exception($"Expected empty string, but got '{result}'");
    }
    
    public void IsPalindrome_ValidPalindrome_ReturnsTrue()
    {
        // Arrange
        var stringHelper = new StringHelper();
        
        // Act
        var result = stringHelper.IsPalindrome("A man a plan a canal Panama");
        
        // Assert
        if (!result)
            throw new Exception("Expected true for palindrome");
    }
    
    public void IsPalindrome_NonPalindrome_ReturnsFalse()
    {
        // Arrange
        var stringHelper = new StringHelper();
        
        // Act
        var result = stringHelper.IsPalindrome("hello world");
        
        // Assert
        if (result)
            throw new Exception("Expected false for non-palindrome");
    }
    
    public void CountWords_ValidText_ReturnsCorrectCount()
    {
        // Arrange
        var stringHelper = new StringHelper();
        string text = "Hello world, this is a test";
        
        // Act
        var result = stringHelper.CountWords(text);
        
        // Assert
        if (result != 6)
            throw new Exception($"Expected 6 words, but got {result}");
    }
}

public class BankAccountTests
{
    public void Constructor_ValidParameters_CreatesAccount()
    {
        // Arrange & Act
        var account = new BankAccount("ACC123", 100);
        
        // Assert
        if (account.AccountNumber != "ACC123")
            throw new Exception("Account number not set correctly");
        
        if (account.Balance != 100)
            throw new Exception("Initial balance not set correctly");
    }
    
    public void Deposit_ValidAmount_IncreasesBalance()
    {
        // Arrange
        var account = new BankAccount("ACC123", 100);
        
        // Act
        account.Deposit(50);
        
        // Assert
        if (account.Balance != 150)
            throw new Exception($"Expected balance 150, but got {account.Balance}");
    }
    
    public void Deposit_NegativeAmount_ThrowsArgumentException()
    {
        // Arrange
        var account = new BankAccount("ACC123", 100);
        
        // Act & Assert
        try
        {
            account.Deposit(-10);
            throw new Exception("Expected ArgumentException was not thrown");
        }
        catch (ArgumentException)
        {
            // Expected exception - test passes
        }
    }
    
    public void Withdraw_ValidAmount_DecreasesBalance()
    {
        // Arrange
        var account = new BankAccount("ACC123", 100);
        
        // Act
        account.Withdraw(30);
        
        // Assert
        if (account.Balance != 70)
            throw new Exception($"Expected balance 70, but got {account.Balance}");
    }
    
    public void Withdraw_InsufficientFunds_ThrowsInvalidOperationException()
    {
        // Arrange
        var account = new BankAccount("ACC123", 100);
        
        // Act & Assert
        try
        {
            account.Withdraw(150);
            throw new Exception("Expected InvalidOperationException was not thrown");
        }
        catch (InvalidOperationException)
        {
            // Expected exception - test passes
        }
    }
    
    public void Transfer_ValidAmount_TransfersFunds()
    {
        // Arrange
        var sourceAccount = new BankAccount("ACC001", 100);
        var targetAccount = new BankAccount("ACC002", 50);
        
        // Act
        sourceAccount.Transfer(30, targetAccount);
        
        // Assert
        if (sourceAccount.Balance != 70)
            throw new Exception($"Expected source balance 70, but got {sourceAccount.Balance}");
        
        if (targetAccount.Balance != 80)
            throw new Exception($"Expected target balance 80, but got {targetAccount.Balance}");
    }
}

public class DataServiceTests
{
    public void ProcessData_ExistingData_ReturnsUppercaseData()
    {
        // Arrange
        var mockRepository = new MockDataRepository();
        mockRepository.SaveData(1, "test data");
        var dataService = new DataService(mockRepository);
        
        // Act
        var result = dataService.ProcessData(1);
        
        // Assert
        if (result != "TEST DATA")
            throw new Exception($"Expected 'TEST DATA', but got '{result}'");
    }
    
    public void ProcessData_NonExistingData_ReturnsNoDataMessage()
    {
        // Arrange
        var mockRepository = new MockDataRepository();
        var dataService = new DataService(mockRepository);
        
        // Act
        var result = dataService.ProcessData(999);
        
        // Assert
        if (result != "No data found")
            throw new Exception($"Expected 'No data found', but got '{result}'");
    }
    
    public void UpdateData_ValidData_SavesData()
    {
        // Arrange
        var mockRepository = new MockDataRepository();
        var dataService = new DataService(mockRepository);
        
        // Act
        dataService.UpdateData(1, "new data");
        
        // Assert
        var savedData = mockRepository.GetData(1);
        if (savedData != "new data")
            throw new Exception($"Expected 'new data', but got '{savedData}'");
    }
    
    public void UpdateData_EmptyData_ThrowsArgumentException()
    {
        // Arrange
        var mockRepository = new MockDataRepository();
        var dataService = new DataService(mockRepository);
        
        // Act & Assert
        try
        {
            dataService.UpdateData(1, "");
            throw new Exception("Expected ArgumentException was not thrown");
        }
        catch (ArgumentException)
        {
            // Expected exception - test passes
        }
    }
}

// Test runner to demonstrate the tests
public class TestRunner
{
    public static void RunAllTests()
    {
        Console.WriteLine("=== Running Unit Tests ===");
        
        var testMethods = new[]
        {
            // Calculator tests
            nameof(CalculatorTests.Add_TwoPositiveNumbers_ReturnsCorrectSum),
            nameof(CalculatorTests.Add_PositiveAndNegativeNumber_ReturnsCorrectResult),
            nameof(CalculatorTests.Divide_ValidDivision_ReturnsCorrectQuotient),
            nameof(CalculatorTests.Divide_ByZero_ThrowsDivideByZeroException),
            nameof(CalculatorTests.IsEven_EvenNumber_ReturnsTrue),
            nameof(CalculatorTests.IsEven_OddNumber_ReturnsFalse),
            
            // StringHelper tests
            nameof(StringHelperTests.Reverse_ValidString_ReturnsReversedString),
            nameof(StringHelperTests.Reverse_EmptyString_ReturnsEmptyString),
            nameof(StringHelperTests.IsPalindrome_ValidPalindrome_ReturnsTrue),
            nameof(StringHelperTests.IsPalindrome_NonPalindrome_ReturnsFalse),
            nameof(StringHelperTests.CountWords_ValidText_ReturnsCorrectCount),
            
            // BankAccount tests
            nameof(BankAccountTests.Constructor_ValidParameters_CreatesAccount),
            nameof(BankAccountTests.Deposit_ValidAmount_IncreasesBalance),
            nameof(BankAccountTests.Deposit_NegativeAmount_ThrowsArgumentException),
            nameof(BankAccountTests.Withdraw_ValidAmount_DecreasesBalance),
            nameof(BankAccountTests.Withdraw_InsufficientFunds_ThrowsInvalidOperationException),
            nameof(BankAccountTests.Transfer_ValidAmount_TransfersFunds),
            
            // DataService tests
            nameof(DataServiceTests.ProcessData_ExistingData_ReturnsUppercaseData),
            nameof(DataServiceTests.ProcessData_NonExistingData_ReturnsNoDataMessage),
            nameof(DataServiceTests.UpdateData_ValidData_SavesData),
            nameof(DataServiceTests.UpdateData_EmptyData_ThrowsArgumentException)
        };
        
        int passedTests = 0;
        int totalTests = testMethods.Length;
        
        foreach (var testMethod in testMethods)
        {
            try
            {
                RunTest(testMethod);
                Console.WriteLine($"✓ {testMethod} - PASSED");
                passedTests++;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"✗ {testMethod} - FAILED: {ex.Message}");
            }
        }
        
        Console.WriteLine($"\nTest Results: {passedTests}/{totalTests} tests passed");
    }
    
    private static void RunTest(string testName)
    {
        var calculatorTests = new CalculatorTests();
        var stringHelperTests = new StringHelperTests();
        var bankAccountTests = new BankAccountTests();
        var dataServiceTests = new DataServiceTests();
        
        switch (testName)
        {
            case nameof(CalculatorTests.Add_TwoPositiveNumbers_ReturnsCorrectSum):
                calculatorTests.Add_TwoPositiveNumbers_ReturnsCorrectSum();
                break;
            case nameof(CalculatorTests.Add_PositiveAndNegativeNumber_ReturnsCorrectResult):
                calculatorTests.Add_PositiveAndNegativeNumber_ReturnsCorrectResult();
                break;
            case nameof(CalculatorTests.Divide_ValidDivision_ReturnsCorrectQuotient):
                calculatorTests.Divide_ValidDivision_ReturnsCorrectQuotient();
                break;
            case nameof(CalculatorTests.Divide_ByZero_ThrowsDivideByZeroException):
                calculatorTests.Divide_ByZero_ThrowsDivideByZeroException();
                break;
            case nameof(CalculatorTests.IsEven_EvenNumber_ReturnsTrue):
                calculatorTests.IsEven_EvenNumber_ReturnsTrue();
                break;
            case nameof(CalculatorTests.IsEven_OddNumber_ReturnsFalse):
                calculatorTests.IsEven_OddNumber_ReturnsFalse();
                break;
            case nameof(StringHelperTests.Reverse_ValidString_ReturnsReversedString):
                stringHelperTests.Reverse_ValidString_ReturnsReversedString();
                break;
            case nameof(StringHelperTests.Reverse_EmptyString_ReturnsEmptyString):
                stringHelperTests.Reverse_EmptyString_ReturnsEmptyString();
                break;
            case nameof(StringHelperTests.IsPalindrome_ValidPalindrome_ReturnsTrue):
                stringHelperTests.IsPalindrome_ValidPalindrome_ReturnsTrue();
                break;
            case nameof(StringHelperTests.IsPalindrome_NonPalindrome_ReturnsFalse):
                stringHelperTests.IsPalindrome_NonPalindrome_ReturnsFalse();
                break;
            case nameof(StringHelperTests.CountWords_ValidText_ReturnsCorrectCount):
                stringHelperTests.CountWords_ValidText_ReturnsCorrectCount();
                break;
            case nameof(BankAccountTests.Constructor_ValidParameters_CreatesAccount):
                bankAccountTests.Constructor_ValidParameters_CreatesAccount();
                break;
            case nameof(BankAccountTests.Deposit_ValidAmount_IncreasesBalance):
                bankAccountTests.Deposit_ValidAmount_IncreasesBalance();
                break;
            case nameof(BankAccountTests.Deposit_NegativeAmount_ThrowsArgumentException):
                bankAccountTests.Deposit_NegativeAmount_ThrowsArgumentException();
                break;
            case nameof(BankAccountTests.Withdraw_ValidAmount_DecreasesBalance):
                bankAccountTests.Withdraw_ValidAmount_DecreasesBalance();
                break;
            case nameof(BankAccountTests.Withdraw_InsufficientFunds_ThrowsInvalidOperationException):
                bankAccountTests.Withdraw_InsufficientFunds_ThrowsInvalidOperationException();
                break;
            case nameof(BankAccountTests.Transfer_ValidAmount_TransfersFunds):
                bankAccountTests.Transfer_ValidAmount_TransfersFunds();
                break;
            case nameof(DataServiceTests.ProcessData_ExistingData_ReturnsUppercaseData):
                dataServiceTests.ProcessData_ExistingData_ReturnsUppercaseData();
                break;
            case nameof(DataServiceTests.ProcessData_NonExistingData_ReturnsNoDataMessage):
                dataServiceTests.ProcessData_NonExistingData_ReturnsNoDataMessage();
                break;
            case nameof(DataServiceTests.UpdateData_ValidData_SavesData):
                dataServiceTests.UpdateData_ValidData_SavesData();
                break;
            case nameof(DataServiceTests.UpdateData_EmptyData_ThrowsArgumentException):
                dataServiceTests.UpdateData_EmptyData_ThrowsArgumentException();
                break;
        }
    }
}

// Program to run the tests
public class UnitTestingProgram
{
    public static void Main(string[] args)
    {
        TestRunner.RunAllTests();
    }
}
