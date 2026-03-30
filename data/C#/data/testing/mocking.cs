using System;
using System.Collections.Generic;

// Interfaces for mocking examples
public interface ILogger
{
    void Log(string message);
    void LogError(string message, Exception ex);
    List<string> GetLogs();
}

public interface IEmailService
{
    void SendEmail(string to, string subject, string body);
    bool WasEmailSent { get; }
}

public interface IPaymentGateway
{
    bool ProcessPayment(decimal amount, string cardNumber);
    string GetLastTransactionId();
}

public interface IUserRepository
{
    User GetUserById(int id);
    void SaveUser(User user);
    List<User> GetAllUsers();
}

public interface INotificationService
{
    void SendNotification(string userId, string message);
    bool CanSendNotification(string userId);
}

// Domain classes
public class User
{
    public int Id { get; set; }
    public string Name { get; set; }
    public string Email { get; set; }
    public bool IsActive { get; set; }
}

public class Order
{
    public int Id { get; set; }
    public decimal Amount { get; set; }
    public string Status { get; set; }
    public int UserId { get; set; }
}

// Services to be tested
public class UserService
{
    private readonly IUserRepository _userRepository;
    private readonly ILogger _logger;
    private readonly INotificationService _notificationService;
    
    public UserService(IUserRepository userRepository, ILogger logger, INotificationService notificationService)
    {
        _userRepository = userRepository ?? throw new ArgumentNullException(nameof(userRepository));
        _logger = logger ?? throw new ArgumentNullException(nameof(logger));
        _notificationService = notificationService ?? throw new ArgumentNullException(nameof(notificationService));
    }
    
    public User CreateUser(string name, string email)
    {
        _logger.Log($"Creating user: {name}");
        
        var user = new User
        {
            Id = new Random().Next(1, 1000),
            Name = name,
            Email = email,
            IsActive = true
        };
        
        _userRepository.SaveUser(user);
        
        if (_notificationService.CanSendNotification(user.Id.ToString()))
        {
            _notificationService.SendNotification(user.Id.ToString(), "Welcome to our service!");
        }
        
        _logger.Log($"User created successfully: {user.Id}");
        return user;
    }
    
    public User GetUser(int id)
    {
        _logger.Log($"Getting user: {id}");
        var user = _userRepository.GetUserById(id);
        
        if (user == null)
        {
            _logger.LogError($"User not found: {id}", new Exception("User not found"));
        }
        
        return user;
    }
}

public class OrderService
{
    private readonly IPaymentGateway _paymentGateway;
    private readonly IUserRepository _userRepository;
    private readonly ILogger _logger;
    private readonly IEmailService _emailService;
    
    public OrderService(IPaymentGateway paymentGateway, IUserRepository userRepository, 
        ILogger logger, IEmailService emailService)
    {
        _paymentGateway = paymentGateway ?? throw new ArgumentNullException(nameof(paymentGateway));
        _userRepository = userRepository ?? throw new ArgumentNullException(nameof(userRepository));
        _logger = logger ?? throw new ArgumentNullException(nameof(logger));
        _emailService = emailService ?? throw new ArgumentNullException(nameof(emailService));
    }
    
    public Order ProcessOrder(int userId, decimal amount, string cardNumber)
    {
        _logger.Log($"Processing order for user {userId}, amount: {amount}");
        
        var user = _userRepository.GetUserById(userId);
        if (user == null)
        {
            throw new ArgumentException($"User {userId} not found");
        }
        
        var order = new Order
        {
            Id = new Random().Next(1, 1000),
            Amount = amount,
            UserId = userId,
            Status = "Pending"
        };
        
        bool paymentSuccess = _paymentGateway.ProcessPayment(amount, cardNumber);
        
        if (paymentSuccess)
        {
            order.Status = "Completed";
            _logger.Log($"Payment successful. Transaction ID: {_paymentGateway.GetLastTransactionId()}");
            
            _emailService.SendEmail(user.Email, "Order Confirmation", 
                $"Your order #{order.Id} has been processed successfully.");
        }
        else
        {
            order.Status = "Failed";
            _logger.LogError("Payment failed", new Exception("Payment gateway declined transaction"));
        }
        
        return order;
    }
}

// Mock implementations
public class MockLogger : ILogger
{
    private readonly List<string> _logs = new();
    
    public void Log(string message)
    {
        _logs.Add($"INFO: {message}");
    }
    
    public void LogError(string message, Exception ex)
    {
        _logs.Add($"ERROR: {message} - {ex.Message}");
    }
    
    public List<string> GetLogs() => new(_logs);
    
    public void ClearLogs() => _logs.Clear();
    
    public bool ContainsLog(string message) => _logs.Exists(log => log.Contains(message));
}

public class MockEmailService : IEmailService
{
    public bool WasEmailSent { get; private set; }
    public string LastTo { get; private set; }
    public string LastSubject { get; private set; }
    public string LastBody { get; private set; }
    
    public void SendEmail(string to, string subject, string body)
    {
        WasEmailSent = true;
        LastTo = to;
        LastSubject = subject;
        LastBody = body;
    }
    
    public void Reset() => WasEmailSent = false;
}

public class MockPaymentGateway : IPaymentGateway
{
    public bool ShouldSucceed { get; set; } = true;
    public string LastTransactionId { get; private set; }
    public decimal LastAmount { get; private set; }
    public string LastCardNumber { get; private set; }
    
    public bool ProcessPayment(decimal amount, string cardNumber)
    {
        LastAmount = amount;
        LastCardNumber = cardNumber;
        LastTransactionId = Guid.NewGuid().ToString();
        
        return ShouldSucceed;
    }
    
    public string GetLastTransactionId() => LastTransactionId;
}

public class MockUserRepository : IUserRepository
{
    private readonly Dictionary<int, User> _users = new();
    
    public User GetUserById(int id)
    {
        return _users.TryGetValue(id, out var user) ? user : null;
    }
    
    public void SaveUser(User user)
    {
        _users[user.Id] = user;
    }
    
    public List<User> GetAllUsers()
    {
        return new List<User>(_users.Values);
    }
    
    public void SetupUser(User user)
    {
        _users[user.Id] = user;
    }
    
    public void ClearUsers() => _users.Clear();
}

public class MockNotificationService : INotificationService
{
    public bool CanSendNotificationResult { get; set; } = true;
    public string LastUserId { get; private set; }
    public string LastMessage { get; private set; }
    
    public void SendNotification(string userId, string message)
    {
        LastUserId = userId;
        LastMessage = message;
    }
    
    public bool CanSendNotification(string userId)
    {
        return CanSendNotificationResult;
    }
}

// Test classes using mocks
public class UserServiceTests
{
    public void CreateUser_ValidData_CreatesUserSuccessfully()
    {
        // Arrange
        var mockRepository = new MockUserRepository();
        var mockLogger = new MockLogger();
        var mockNotificationService = new MockNotificationService();
        
        var userService = new UserService(mockRepository, mockLogger, mockNotificationService);
        
        // Act
        var user = userService.CreateUser("John Doe", "john@example.com");
        
        // Assert
        if (user == null)
            throw new Exception("User was not created");
        
        if (user.Name != "John Doe")
            throw new Exception($"Expected name 'John Doe', got '{user.Name}'");
        
        if (user.Email != "john@example.com")
            throw new Exception($"Expected email 'john@example.com', got '{user.Email}'");
        
        if (!user.IsActive)
            throw new Exception("User should be active");
        
        if (!mockLogger.ContainsLog("Creating user: John Doe"))
            throw new Exception("User creation was not logged");
        
        if (!mockLogger.ContainsLog("User created successfully"))
            throw new Exception("User creation success was not logged");
        
        if (mockNotificationService.LastUserId != user.Id.ToString())
            throw new Exception("Notification was not sent");
        
        if (mockNotificationService.LastMessage != "Welcome to our service!")
            throw new Exception("Wrong notification message");
    }
    
    public void CreateUser_NotificationDisabled_DoesNotSendNotification()
    {
        // Arrange
        var mockRepository = new MockUserRepository();
        var mockLogger = new MockLogger();
        var mockNotificationService = new MockNotificationService();
        mockNotificationService.CanSendNotificationResult = false;
        
        var userService = new UserService(mockRepository, mockLogger, mockNotificationService);
        
        // Act
        var user = userService.CreateUser("Jane Doe", "jane@example.com");
        
        // Assert
        if (user == null)
            throw new Exception("User was not created");
        
        if (mockNotificationService.LastUserId != null)
            throw new Exception("Notification should not have been sent");
    }
    
    public void GetUser_ExistingUser_ReturnsUser()
    {
        // Arrange
        var mockRepository = new MockUserRepository();
        var mockLogger = new MockLogger();
        var mockNotificationService = new MockNotificationService();
        
        var testUser = new User { Id = 1, Name = "Test User", Email = "test@example.com" };
        mockRepository.SetupUser(testUser);
        
        var userService = new UserService(mockRepository, mockLogger, mockNotificationService);
        
        // Act
        var user = userService.GetUser(1);
        
        // Assert
        if (user == null)
            throw new Exception("User was not found");
        
        if (user.Id != 1)
            throw new Exception($"Expected user ID 1, got {user.Id}");
        
        if (user.Name != "Test User")
            throw new Exception($"Expected name 'Test User', got '{user.Name}'");
    }
    
    public void GetUser_NonExistingUser_LogsError()
    {
        // Arrange
        var mockRepository = new MockUserRepository();
        var mockLogger = new MockLogger();
        var mockNotificationService = new MockNotificationService();
        
        var userService = new UserService(mockRepository, mockLogger, mockNotificationService);
        
        // Act
        var user = userService.GetUser(999);
        
        // Assert
        if (user != null)
            throw new Exception("Expected null for non-existing user");
        
        if (!mockLogger.ContainsLog("User not found: 999"))
            throw new Exception("Error was not logged for non-existing user");
    }
}

public class OrderServiceTests
{
    public void ProcessOrder_SuccessfulPayment_CompletesOrder()
    {
        // Arrange
        var mockPaymentGateway = new MockPaymentGateway { ShouldSucceed = true };
        var mockUserRepository = new MockUserRepository();
        var mockLogger = new MockLogger();
        var mockEmailService = new MockEmailService();
        
        var testUser = new User { Id = 1, Name = "Test User", Email = "test@example.com" };
        mockUserRepository.SetupUser(testUser);
        
        var orderService = new OrderService(mockPaymentGateway, mockUserRepository, mockLogger, mockEmailService);
        
        // Act
        var order = orderService.ProcessOrder(1, 100.00m, "4111111111111111");
        
        // Assert
        if (order == null)
            throw new Exception("Order was not created");
        
        if (order.Status != "Completed")
            throw new Exception($"Expected status 'Completed', got '{order.Status}'");
        
        if (order.Amount != 100.00m)
            throw new Exception($"Expected amount 100.00, got {order.Amount}");
        
        if (!mockPaymentGateway.ShouldSucceed)
            throw new Exception("Payment should have succeeded");
        
        if (!mockEmailService.WasEmailSent)
            throw new Exception("Confirmation email should have been sent");
        
        if (mockEmailService.LastTo != "test@example.com")
            throw new Exception($"Expected email to 'test@example.com', got '{mockEmailService.LastTo}'");
        
        if (!mockLogger.ContainsLog("Payment successful"))
            throw new Exception("Payment success was not logged");
    }
    
    public void ProcessOrder_FailedPayment_FailsOrder()
    {
        // Arrange
        var mockPaymentGateway = new MockPaymentGateway { ShouldSucceed = false };
        var mockUserRepository = new MockUserRepository();
        var mockLogger = new MockLogger();
        var mockEmailService = new MockEmailService();
        
        var testUser = new User { Id = 1, Name = "Test User", Email = "test@example.com" };
        mockUserRepository.SetupUser(testUser);
        
        var orderService = new OrderService(mockPaymentGateway, mockUserRepository, mockLogger, mockEmailService);
        
        // Act
        var order = orderService.ProcessOrder(1, 100.00m, "4111111111111111");
        
        // Assert
        if (order == null)
            throw new Exception("Order was not created");
        
        if (order.Status != "Failed")
            throw new Exception($"Expected status 'Failed', got '{order.Status}'");
        
        if (mockEmailService.WasEmailSent)
            throw new Exception("Email should not have been sent for failed payment");
        
        if (!mockLogger.ContainsLog("Payment failed"))
            throw new Exception("Payment failure was not logged");
    }
    
    public void ProcessOrder_NonExistingUser_ThrowsException()
    {
        // Arrange
        var mockPaymentGateway = new MockPaymentGateway();
        var mockUserRepository = new MockUserRepository();
        var mockLogger = new MockLogger();
        var mockEmailService = new MockEmailService();
        
        var orderService = new OrderService(mockPaymentGateway, mockUserRepository, mockLogger, mockEmailService);
        
        // Act & Assert
        try
        {
            orderService.ProcessOrder(999, 100.00m, "4111111111111111");
            throw new Exception("Expected ArgumentException was not thrown");
        }
        catch (ArgumentException ex)
        {
            if (!ex.Message.Contains("User 999 not found"))
                throw new Exception($"Wrong exception message: {ex.Message}");
        }
    }
}

// Test runner for mocking tests
public class MockingTestRunner
{
    public static void RunAllTests()
    {
        Console.WriteLine("=== Running Mocking Tests ===");
        
        var userServiceTests = new UserServiceTests();
        var orderServiceTests = new OrderServiceTests();
        
        var testMethods = new[]
        {
            // UserService tests
            nameof(UserServiceTests.CreateUser_ValidData_CreatesUserSuccessfully),
            nameof(UserServiceTests.CreateUser_NotificationDisabled_DoesNotSendNotification),
            nameof(UserServiceTests.GetUser_ExistingUser_ReturnsUser),
            nameof(UserServiceTests.GetUser_NonExistingUser_LogsError),
            
            // OrderService tests
            nameof(OrderServiceTests.ProcessOrder_SuccessfulPayment_CompletesOrder),
            nameof(OrderServiceTests.ProcessOrder_FailedPayment_FailsOrder),
            nameof(OrderServiceTests.ProcessOrder_NonExistingUser_ThrowsException)
        };
        
        int passedTests = 0;
        int totalTests = testMethods.Length;
        
        foreach (var testMethod in testMethods)
        {
            try
            {
                RunTest(testMethod, userServiceTests, orderServiceTests);
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
    
    private static void RunTest(string testName, UserServiceTests userServiceTests, OrderServiceTests orderServiceTests)
    {
        switch (testName)
        {
            case nameof(UserServiceTests.CreateUser_ValidData_CreatesUserSuccessfully):
                userServiceTests.CreateUser_ValidData_CreatesUserSuccessfully();
                break;
            case nameof(UserServiceTests.CreateUser_NotificationDisabled_DoesNotSendNotification):
                userServiceTests.CreateUser_NotificationDisabled_DoesNotSendNotification();
                break;
            case nameof(UserServiceTests.GetUser_ExistingUser_ReturnsUser):
                userServiceTests.GetUser_ExistingUser_ReturnsUser();
                break;
            case nameof(UserServiceTests.GetUser_NonExistingUser_LogsError):
                userServiceTests.GetUser_NonExistingUser_LogsError();
                break;
            case nameof(OrderServiceTests.ProcessOrder_SuccessfulPayment_CompletesOrder):
                orderServiceTests.ProcessOrder_SuccessfulPayment_CompletesOrder();
                break;
            case nameof(OrderServiceTests.ProcessOrder_FailedPayment_FailsOrder):
                orderServiceTests.ProcessOrder_FailedPayment_FailsOrder();
                break;
            case nameof(OrderServiceTests.ProcessOrder_NonExistingUser_ThrowsException):
                orderServiceTests.ProcessOrder_NonExistingUser_ThrowsException();
                break;
        }
    }
}

// Program to run mocking tests
public class MockingProgram
{
    public static void Main(string[] args)
    {
        MockingTestRunner.RunAllTests();
    }
}
