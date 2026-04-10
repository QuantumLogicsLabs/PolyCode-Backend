public class ExceptionHandlingExample {
    public static void main(String[] args) {
        // Basic Exception Handling
        System.out.println("=== Basic Exception Handling ===");
        demonstrateBasicExceptions();
        
        // Custom Exceptions
        System.out.println("\n=== Custom Exceptions ===");
        demonstrateCustomExceptions();
        
        // Try-with-Resources
        System.out.println("\n=== Try-with-Resources ===");
        demonstrateTryWithResources();
        
        // Exception Chaining
        System.out.println("\n=== Exception Chaining ===");
        demonstrateExceptionChaining();
        
        // Multiple Exceptions
        System.out.println("\n=== Multiple Exceptions ===");
        demonstrateMultipleExceptions();
        
        // Finally Block
        System.out.println("\n=== Finally Block ===");
        demonstrateFinallyBlock();
        
        // Exception Propagation
        System.out.println("\n=== Exception Propagation ===");
        demonstrateExceptionPropagation();
        
        // Performance Considerations
        System.out.println("\n=== Performance Considerations ===");
        demonstratePerformance();
    }
    
    public static void demonstrateBasicExceptions() {
        // ArithmeticException
        try {
            int result = 10 / 0;
            System.out.println("This won't print due to exception");
        } catch (ArithmeticException e) {
            System.out.println("ArithmeticException: " + e.getMessage());
        }
        
        // NullPointerException
        try {
            String str = null;
            if (str != null) {
                int length = str.length();
                System.out.println("Length: " + length);
            } else {
                System.out.println("String is null");
            }
        } catch (NullPointerException e) {
            System.out.println("NullPointerException: " + e.getMessage());
        }
        
        // ArrayIndexOutOfBoundsException
        try {
            int[] array = {1, 2, 3};
            int value = array[5];
            System.out.println("This won't print due to exception");
        } catch (ArrayIndexOutOfBoundsException e) {
            System.out.println("ArrayIndexOutOfBoundsException: " + e.getMessage());
        }
        
        // StringIndexOutOfBoundsException
        try {
            String str = "Hello";
            char ch = str.charAt(10);
            System.out.println("Character: " + ch);
        } catch (StringIndexOutOfBoundsException e) {
            System.out.println("StringIndexOutOfBoundsException: " + e.getMessage());
        }
        
        // NumberFormatException
        try {
            int number = Integer.parseInt("abc");
            System.out.println("Number: " + number);
        } catch (NumberFormatException e) {
            System.out.println("NumberFormatException: " + e.getMessage());
        }
        
        // ClassCastException
        try {
            Object obj = "Hello";
            Integer num = (Integer) obj;
            System.out.println("Number: " + num);
        } catch (ClassCastException e) {
            System.out.println("ClassCastException: " + e.getMessage());
        }
    }
    
    public static void demonstrateCustomExceptions() {
        try {
            BankAccount account = new BankAccount(1000);
            account.withdraw(1500);
        } catch (InsufficientFundsException e) {
            System.out.println("Custom Exception: " + e.getMessage());
            System.out.println("Available balance: " + e.getAvailableBalance());
            System.out.println("Requested amount: " + e.getRequestedAmount());
        }
        
        try {
            validateAge(15);
        } catch (InvalidAgeException e) {
            System.out.println("Age Validation Error: " + e.getMessage());
            System.out.println("Invalid age: " + e.getInvalidAge());
            System.out.println("Minimum age: " + e.getMinimumAge());
        }
    }
    
    public static void demonstrateTryWithResources() {
        // Using try-with-resources with custom resource
        try (CustomResource resource = new CustomResource()) {
            resource.doSomething();
            System.out.println("Resource used successfully");
        } catch (Exception e) {
            System.out.println("Exception with resources: " + e.getMessage());
        }
        
        // Using try-with-resources with file (simulated)
        try (FileResource fileResource = new FileResource("test.txt")) {
            fileResource.writeData("Hello, World!");
            fileResource.readData();
        } catch (Exception e) {
            System.out.println("File operation failed: " + e.getMessage());
        }
    }
    
    public static void demonstrateExceptionChaining() {
        try {
            try {
                // Simulate a low-level exception
                throw new LowLevelException("Database connection failed");
            } catch (LowLevelException e) {
                // Wrap it in a higher-level exception
                throw new HighLevelException("Unable to process user request", e);
            }
        } catch (HighLevelException e) {
            System.out.println("High-level exception: " + e.getMessage());
            System.out.println("Caused by: " + e.getCause().getMessage());
            
            // Print full stack trace
            e.printStackTrace();
        }
    }
    
    public static void demonstrateMultipleExceptions() {
        try {
            processRequest("invalid_number", -1);
        } catch (InvalidInputException | InvalidRangeException e) {
            System.out.println("Input/Range Exception: " + e.getMessage());
        } catch (Exception e) {
            System.out.println("General Exception: " + e.getMessage());
        }
    }
    
    public static void demonstrateFinallyBlock() {
        System.out.println("Entering try-finally block");
        try {
            System.out.println("Inside try block");
            // Simulate an exception
            throw new RuntimeException("Something went wrong");
        } catch (RuntimeException e) {
            System.out.println("Caught exception: " + e.getMessage());
        } finally {
            System.out.println("Finally block always executes");
        }
        System.out.println("Exited try-finally block");
    }
    
    public static void demonstrateExceptionPropagation() {
        try {
            level1();
        } catch (Exception e) {
            System.out.println("Exception caught at main: " + e.getMessage());
        }
    }
    
    public static void level1() throws Exception {
        try {
            level2();
        } catch (Exception e) {
            System.out.println("Exception caught at level1: " + e.getMessage());
            throw e; // Re-throw to propagate
        }
    }
    
    public static void level2() throws Exception {
        throw new Exception("Original exception from level2");
    }
    
    public static void validateAge(int age) throws InvalidAgeException {
        if (age < 18) {
            throw new InvalidAgeException("Age must be at least 18", age, 18);
        }
    }
    
    public static void processRequest(String input, int value) throws InvalidInputException, InvalidRangeException {
        if (input == null || input.trim().isEmpty()) {
            throw new InvalidInputException("Input cannot be null or empty");
        }
        
        if (value < 0 || value > 100) {
            throw new InvalidRangeException("Value must be between 0 and 100", value);
        }
        
        System.out.println("Processing: " + input + " with value " + value);
    }
    
    public static void demonstratePerformance() {
        final int ITERATIONS = 1000000;
        
        // Performance with exceptions
        long startTime = System.nanoTime();
        int exceptionCount = 0;
        
        for (int i = 0; i < ITERATIONS; i++) {
            try {
                if (i % 100 == 0) {
                    throw new RuntimeException("Test exception");
                }
            } catch (RuntimeException e) {
                exceptionCount++;
            }
        }
        
        long endTime = System.nanoTime();
        
        System.out.println("Exception handling performance:");
        System.out.println("Iterations: " + ITERATIONS);
        System.out.println("Exceptions thrown: " + exceptionCount);
        System.out.println("Time: " + (endTime - startTime) / 1000000.0 + " ms");
        System.out.println("Note: Exception handling is expensive!");
    }
}

// Custom Exception Classes

class InsufficientFundsException extends Exception {
    private double availableBalance;
    private double requestedAmount;
    
    public InsufficientFundsException(String message, double availableBalance, double requestedAmount) {
        super(message);
        this.availableBalance = availableBalance;
        this.requestedAmount = requestedAmount;
    }
    
    public double getAvailableBalance() { return availableBalance; }
    public double getRequestedAmount() { return requestedAmount; }
}

class InvalidAgeException extends Exception {
    private int invalidAge;
    private int minimumAge;
    
    public InvalidAgeException(String message, int invalidAge, int minimumAge) {
        super(message);
        this.invalidAge = invalidAge;
        this.minimumAge = minimumAge;
    }
    
    public int getInvalidAge() { return invalidAge; }
    public int getMinimumAge() { return minimumAge; }
}

class LowLevelException extends Exception {
    public LowLevelException(String message) {
        super(message);
    }
}

class HighLevelException extends Exception {
    public HighLevelException(String message, Throwable cause) {
        super(message, cause);
    }
}

class InvalidInputException extends Exception {
    public InvalidInputException(String message) {
        super(message);
    }
}

class InvalidRangeException extends Exception {
    private int invalidValue;
    
    public InvalidRangeException(String message, int invalidValue) {
        super(message);
        this.invalidValue = invalidValue;
    }
    
    public int getInvalidValue() { return invalidValue; }
}

// Resource Classes for Try-with-Resources

class CustomResource implements AutoCloseable {
    private boolean isOpen = false;
    
    public CustomResource() {
        System.out.println("CustomResource: Opening resource");
        isOpen = true;
    }
    
    public void doSomething() throws Exception {
        System.out.println("CustomResource: Doing something with resource");
        if (Math.random() > 0.7) {
            throw new Exception("Resource operation failed");
        }
    }
    
    @Override
    public void close() {
        if (isOpen) {
            System.out.println("CustomResource: Closing resource");
            isOpen = false;
        }
    }
}

class FileResource implements AutoCloseable {
    private String filename;
    private boolean isOpen = false;
    
    public FileResource(String filename) {
        this.filename = filename;
        System.out.println("FileResource: Opening file " + filename);
        isOpen = true;
    }
    
    public void writeData(String data) throws Exception {
        if (!isOpen) {
            throw new Exception("File is not open");
        }
        System.out.println("FileResource: Writing to " + filename + ": " + data);
    }
    
    public void readData() throws Exception {
        if (!isOpen) {
            throw new Exception("File is not open");
        }
        System.out.println("FileResource: Reading from " + filename);
    }
    
    @Override
    public void close() {
        if (isOpen) {
            System.out.println("FileResource: Closing file " + filename);
            isOpen = false;
        }
    }
}

// Bank Account Class for Custom Exception Demo
class BankAccount {
    private double balance;
    
    public BankAccount(double initialBalance) {
        this.balance = initialBalance;
    }
    
    public void withdraw(double amount) throws InsufficientFundsException {
        if (amount > balance) {
            throw new InsufficientFundsException(
                "Insufficient funds for withdrawal", 
                balance, 
                amount
            );
        }
        balance -= amount;
        System.out.println("Withdrawal successful. New balance: " + balance);
    }
    
    public double getBalance() {
        return balance;
    }
}
