using System;

// Custom exception classes
public class BusinessException : Exception
{
    public BusinessException() : base() { }
    
    public BusinessException(string message) : base(message) { }
    
    public BusinessException(string message, Exception innerException) 
        : base(message, innerException) { }
    
    public int ErrorCode { get; set; }
    
    public BusinessException(string message, int errorCode) : base(message)
    {
        ErrorCode = errorCode;
    }
}

public class InsufficientFundsException : BusinessException
{
    public decimal CurrentBalance { get; }
    public decimal RequestedAmount { get; }
    
    public InsufficientFundsException(decimal currentBalance, decimal requestedAmount)
        : base($"Insufficient funds. Current balance: {currentBalance:C}, Requested: {requestedAmount:C}")
    {
        CurrentBalance = currentBalance;
        RequestedAmount = requestedAmount;
        ErrorCode = 1001;
    }
}

public class UserNotFoundException : BusinessException
{
    public string UserId { get; }
    
    public UserNotFoundException(string userId) 
        : base($"User with ID '{userId}' not found")
    {
        UserId = userId;
        ErrorCode = 2001;
    }
}

public class ValidationException : BusinessException
{
    public Dictionary<string, string> ValidationErrors { get; }
    
    public ValidationException(Dictionary<string, string> validationErrors)
        : base($"Validation failed with {validationErrors.Count} errors")
    {
        ValidationErrors = validationErrors;
        ErrorCode = 3001;
    }
}

// Bank account class that uses custom exceptions
public class BankAccount
{
    public string AccountNumber { get; }
    public decimal Balance { get; private set; }
    
    public BankAccount(string accountNumber, decimal initialBalance)
    {
        AccountNumber = accountNumber;
        Balance = initialBalance;
    }
    
    public void Withdraw(decimal amount)
    {
        if (amount <= 0)
        {
            throw new ArgumentException("Withdrawal amount must be positive", nameof(amount));
        }
        
        if (amount > Balance)
        {
            throw new InsufficientFundsException(Balance, amount);
        }
        
        Balance -= amount;
        Console.WriteLine($"Withdrawn {amount:C}. New balance: {Balance:C}");
    }
    
    public void Deposit(decimal amount)
    {
        if (amount <= 0)
        {
            throw new ArgumentException("Deposit amount must be positive", nameof(amount));
        }
        
        Balance += amount;
        Console.WriteLine($"Deposited {amount:C}. New balance: {Balance:C}");
    }
}

// User management class
public class UserManager
{
    private readonly Dictionary<string, string> _users = new();
    
    public UserManager()
    {
        // Initialize with some users
        _users["user1"] = "John Doe";
        _users["user2"] = "Jane Smith";
    }
    
    public string GetUser(string userId)
    {
        if (!_users.ContainsKey(userId))
        {
            throw new UserNotFoundException(userId);
        }
        
        return _users[userId];
    }
    
    public void AddUser(string userId, string name)
    {
        ValidateUserInput(userId, name);
        
        if (_users.ContainsKey(userId))
        {
            throw new BusinessException($"User with ID '{userId}' already exists", 2002);
        }
        
        _users[userId] = name;
        Console.WriteLine($"User '{name}' added with ID '{userId}'");
    }
    
    private void ValidateUserInput(string userId, string name)
    {
        var errors = new Dictionary<string, string>();
        
        if (string.IsNullOrWhiteSpace(userId))
        {
            errors["UserId"] = "User ID is required";
        }
        
        if (string.IsNullOrWhiteSpace(name))
        {
            errors["Name"] = "Name is required";
        }
        
        if (name?.Length < 2)
        {
            errors["Name"] = "Name must be at least 2 characters long";
        }
        
        if (errors.Count > 0)
        {
            throw new ValidationException(errors);
        }
    }
}

// Program demonstrating custom exceptions
public class CustomExceptionsProgram
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Custom Exceptions Demo ===");
        
        // Demonstrate bank account operations
        DemonstrateBankAccount();
        
        // Demonstrate user management
        DemonstrateUserManagement();
        
        // Demonstrate exception handling with custom exceptions
        DemonstrateExceptionHandling();
    }
    
    public static void DemonstrateBankAccount()
    {
        Console.WriteLine("\n--- Bank Account Operations ---");
        
        var account = new BankAccount("ACC123", 1000);
        
        try
        {
            account.Deposit(500);
            account.Withdraw(200);
            account.Withdraw(2000); // This will throw InsufficientFundsException
        }
        catch (InsufficientFundsException ex)
        {
            Console.WriteLine($"Error: {ex.Message}");
            Console.WriteLine($"Error Code: {ex.ErrorCode}");
            Console.WriteLine($"Current Balance: {ex.CurrentBalance:C}");
            Console.WriteLine($"Requested Amount: {ex.RequestedAmount:C}");
        }
        catch (BusinessException ex)
        {
            Console.WriteLine($"Business Error: {ex.Message} (Code: {ex.ErrorCode})");
        }
    }
    
    public static void DemonstrateUserManagement()
    {
        Console.WriteLine("\n--- User Management ---");
        
        var userManager = new UserManager();
        
        try
        {
            // Try to get existing user
            var user = userManager.GetUser("user1");
            Console.WriteLine($"Found user: {user}");
            
            // Try to get non-existent user
            var nonExistentUser = userManager.GetUser("user999");
        }
        catch (UserNotFoundException ex)
        {
            Console.WriteLine($"Error: {ex.Message}");
            Console.WriteLine($"User ID: {ex.UserId}");
            Console.WriteLine($"Error Code: {ex.ErrorCode}");
        }
        
        try
        {
            // Try to add user with invalid data
            userManager.AddUser("", "A");
        }
        catch (ValidationException ex)
        {
            Console.WriteLine($"Validation Error: {ex.Message}");
            Console.WriteLine("Validation errors:");
            foreach (var error in ex.ValidationErrors)
            {
                Console.WriteLine($"  {error.Key}: {error.Value}");
            }
        }
    }
    
    public static void DemonstrateExceptionHandling()
    {
        Console.WriteLine("\n--- Exception Handling Patterns ---");
        
        try
        {
            // Simulate a business operation that might fail
            ProcessOrder("order123", -100);
        }
        catch (BusinessException ex)
        {
            Console.WriteLine($"Business logic failed: {ex.Message}");
            
            // Log the error (in real app)
            Console.WriteLine($"Logged error: Code {ex.ErrorCode}, Message: {ex.Message}");
            
            // Handle based on error code
            switch (ex.ErrorCode)
            {
                case 1001:
                    Console.WriteLine("Action: Redirect to payment page");
                    break;
                case 2001:
                    Console.WriteLine("Action: Show user not found message");
                    break;
                case 3001:
                    Console.WriteLine("Action: Show validation errors to user");
                    break;
                default:
                    Console.WriteLine("Action: Show generic error message");
                    break;
            }
        }
    }
    
    public static void ProcessOrder(string orderId, decimal amount)
    {
        // Simulate order processing with business rules
        if (amount <= 0)
        {
            throw new BusinessException("Order amount must be positive", 4001);
        }
        
        if (amount > 10000)
        {
            throw new BusinessException("Order amount exceeds daily limit", 4002);
        }
        
        Console.WriteLine($"Order {orderId} processed successfully for amount {amount:C}");
    }
}
