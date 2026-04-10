# Java Exception Handling

## Exception Basics

### Try-Catch Block
```java
public class BasicException {
    public static void main(String[] args) {
        try {
            int result = 10 / 0; // This will throw ArithmeticException
            System.out.println("Result: " + result);
        } catch (ArithmeticException e) {
            System.out.println("Cannot divide by zero: " + e.getMessage());
        }
        
        try {
            String str = null;
            int length = str.length(); // This will throw NullPointerException
        } catch (NullPointerException e) {
            System.out.println("Null reference: " + e.getMessage());
        }
        
        try {
            int[] numbers = {1, 2, 3};
            int value = numbers[5]; // This will throw ArrayIndexOutOfBoundsException
        } catch (ArrayIndexOutOfBoundsException e) {
            System.out.println("Array index out of bounds: " + e.getMessage());
        }
    }
}
```

### Multiple Catch Blocks
```java
public class MultipleCatch {
    public static void main(String[] args) {
        try {
            String[] args2 = {"10", "abc", "0"};
            int a = Integer.parseInt(args2[0]);
            int b = Integer.parseInt(args2[1]);
            int result = a / b;
            System.out.println("Result: " + result);
        } catch (ArrayIndexOutOfBoundsException e) {
            System.out.println("Please provide 2 arguments");
        } catch (NumberFormatException e) {
            System.out.println("Invalid number format: " + e.getMessage());
        } catch (ArithmeticException e) {
            System.out.println("Cannot divide by zero");
        }
    }
}
```

### Finally Block
```java
public class FinallyBlock {
    public static void main(String[] args) {
        try {
            System.out.println("In try block");
            int result = 10 / 2;
            System.out.println("Result: " + result);
        } catch (Exception e) {
            System.out.println("In catch block: " + e.getMessage());
        } finally {
            System.out.println("In finally block - always executes");
        }
        
        // Example with exception
        try {
            System.out.println("\nIn try block");
            int result = 10 / 0;
            System.out.println("Result: " + result);
        } catch (Exception e) {
            System.out.println("In catch block: " + e.getMessage());
        } finally {
            System.out.println("In finally block - always executes");
        }
    }
}
```

## Custom Exceptions

### Creating Custom Exceptions
```java
// Custom exception class
public class InvalidAgeException extends Exception {
    public InvalidAgeException(String message) {
        super(message);
    }
}

// Another custom exception
public class InsufficientFundsException extends Exception {
    private double amount;
    private double balance;
    
    public InsufficientFundsException(String message, double amount, double balance) {
        super(message);
        this.amount = amount;
        this.balance = balance;
    }
    
    public double getAmount() { return amount; }
    public double getBalance() { return balance; }
}

// Using custom exceptions
public class BankAccount {
    private String accountNumber;
    private double balance;
    
    public BankAccount(String accountNumber, double balance) {
        this.accountNumber = accountNumber;
        this.balance = balance;
    }
    
    public void withdraw(double amount) throws InsufficientFundsException {
        if (amount > balance) {
            throw new InsufficientFundsException(
                "Insufficient funds for withdrawal", amount, balance);
        }
        balance -= amount;
        System.out.println("Withdrew: $" + amount);
    }
    
    public void deposit(double amount) {
        if (amount <= 0) {
            throw new IllegalArgumentException("Deposit amount must be positive");
        }
        balance += amount;
        System.out.println("Deposited: $" + amount);
    }
    
    public double getBalance() { return balance; }
}
```

### Using Custom Exceptions
```java
public class CustomExceptionDemo {
    public static void main(String[] args) {
        BankAccount account = new BankAccount("12345", 1000.0);
        
        try {
            account.withdraw(500.0);
            account.withdraw(600.0); // This will throw exception
        } catch (InsufficientFundsException e) {
            System.out.println("Error: " + e.getMessage());
            System.out.println("Attempted to withdraw: $" + e.getAmount());
            System.out.println("Available balance: $" + e.getBalance());
        }
        
        try {
            account.deposit(-100.0); // This will throw exception
        } catch (IllegalArgumentException e) {
            System.out.println("Error: " + e.getMessage());
        }
    }
}
```

## Try-With-Resources

### Auto Resource Management
```java
import java.io.*;

public class TryWithResources {
    public static void main(String[] args) {
        // Traditional way
        BufferedReader reader = null;
        try {
            reader = new BufferedReader(new FileReader("file.txt"));
            String line = reader.readLine();
            System.out.println(line);
        } catch (IOException e) {
            System.out.println("Error reading file: " + e.getMessage());
        } finally {
            if (reader != null) {
                try {
                    reader.close();
                } catch (IOException e) {
                    System.out.println("Error closing reader: " + e.getMessage());
                }
            }
        }
        
        // Try-with-resources (Java 7+)
        try (BufferedReader autoReader = new BufferedReader(new FileReader("file.txt"))) {
            String line = autoReader.readLine();
            System.out.println(line);
        } catch (IOException e) {
            System.out.println("Error reading file: " + e.getMessage());
        }
        // Reader is automatically closed
        
        // Multiple resources
        try (BufferedReader reader1 = new BufferedReader(new FileReader("input.txt"));
             BufferedWriter writer = new BufferedWriter(new FileWriter("output.txt"))) {
            
            String line;
            while ((line = reader1.readLine()) != null) {
                writer.write(line.toUpperCase());
                writer.newLine();
            }
        } catch (IOException e) {
            System.out.println("Error processing files: " + e.getMessage());
        }
    }
}
```

### Custom Auto-Closeable
```java
public class CustomResource implements AutoCloseable {
    private String name;
    private boolean isOpen;
    
    public CustomResource(String name) {
        this.name = name;
        this.isOpen = true;
        System.out.println(name + " opened");
    }
    
    public void doSomething() {
        if (!isOpen) {
            throw new IllegalStateException("Resource is closed");
        }
        System.out.println(name + " is doing something");
    }
    
    @Override
    public void close() {
        if (isOpen) {
            isOpen = false;
            System.out.println(name + " closed");
        }
    }
}

public class CustomResourceDemo {
    public static void main(String[] args) {
        try (CustomResource resource = new CustomResource("MyResource")) {
            resource.doSomething();
        } // Resource is automatically closed here
    }
}
```

## Exception Hierarchy

### Built-in Exception Types
```java
public class ExceptionHierarchy {
    public static void demonstrateExceptions() {
        // Checked exceptions (must be handled)
        try {
            Thread.sleep(1000); // InterruptedException
        } catch (InterruptedException e) {
            Thread.currentThread().interrupt();
        }
        
        try {
            FileInputStream fis = new FileInputStream("nonexistent.txt"); // FileNotFoundException
        } catch (FileNotFoundException e) {
            System.out.println("File not found: " + e.getMessage());
        }
        
        // Unchecked exceptions (runtime exceptions)
        try {
            String str = null;
            int length = str.length(); // NullPointerException
        } catch (NullPointerException e) {
            System.out.println("Null pointer: " + e.getMessage());
        }
        
        try {
            int[] arr = new int[5];
            int value = arr[10]; // ArrayIndexOutOfBoundsException
        } catch (ArrayIndexOutOfBoundsException e) {
            System.out.println("Array index out of bounds: " + e.getMessage());
        }
        
        try {
            Object obj = new String("Hello");
            Integer num = (Integer) obj; // ClassCastException
        } catch (ClassCastException e) {
            System.out.println("Class cast exception: " + e.getMessage());
        }
    }
}
```

## Exception Handling Best Practices

### Proper Exception Handling
```java
public class ExceptionBestPractices {
    
    // Handle exceptions at appropriate level
    public static void readFile(String filename) {
        try (BufferedReader reader = new BufferedReader(new FileReader(filename))) {
            String line;
            while ((line = reader.readLine()) != null) {
                processLine(line);
            }
        } catch (FileNotFoundException e) {
            System.err.println("File not found: " + filename);
            // Log error and continue
        } catch (IOException e) {
            System.err.println("Error reading file: " + e.getMessage());
            // Log error and continue
        }
    }
    
    // Throw exceptions to caller when appropriate
    public static int calculateAge(int birthYear) throws InvalidAgeException {
        int currentYear = java.time.Year.now().getValue();
        int age = currentYear - birthYear;
        
        if (age < 0 || age > 150) {
            throw new InvalidAgeException("Invalid age calculated: " + age);
        }
        
        return age;
    }
    
    // Use specific exceptions
    public static void processData(String data) {
        try {
            if (data == null) {
                throw new IllegalArgumentException("Data cannot be null");
            }
            
            int number = Integer.parseInt(data);
            if (number < 0) {
                throw new IllegalArgumentException("Number cannot be negative");
            }
            
            // Process valid data
            System.out.println("Processing: " + number);
            
        } catch (NumberFormatException e) {
            System.err.println("Invalid number format: " + data);
        }
    }
    
    // Finally block for cleanup
    public static void databaseOperation() {
        Connection conn = null;
        try {
            conn = getDatabaseConnection();
            // Perform database operations
            executeQuery(conn);
        } catch (SQLException e) {
            System.err.println("Database error: " + e.getMessage());
        } finally {
            if (conn != null) {
                try {
                    conn.close();
                } catch (SQLException e) {
                    System.err.println("Error closing connection: " + e.getMessage());
                }
            }
        }
    }
    
    // Helper methods (simplified)
    private static Connection getDatabaseConnection() throws SQLException {
        // Simulated database connection
        return null;
    }
    
    private static void executeQuery(Connection conn) throws SQLException {
        // Simulated query execution
    }
    
    private static void processLine(String line) {
        // Process line
    }
}
```

## Examples

### Complete Exception Handling Example
```java
import java.io.*;
import java.util.*;

public class CompleteExceptionExample {
    public static void main(String[] args) {
        try {
            // Process file with numbers
            List<Integer> numbers = processNumberFile("numbers.txt");
            
            // Calculate statistics
            double average = calculateAverage(numbers);
            System.out.println("Average: " + average);
            
            // Find max and min
            int max = findMax(numbers);
            int min = findMin(numbers);
            System.out.println("Max: " + max + ", Min: " + min);
            
        } catch (FileNotFoundException e) {
            System.err.println("File not found: " + e.getMessage());
        } catch (InvalidDataException e) {
            System.err.println("Invalid data in file: " + e.getMessage());
        } catch (Exception e) {
            System.err.println("Unexpected error: " + e.getMessage());
        }
    }
    
    public static List<Integer> processNumberFile(String filename) 
            throws FileNotFoundException, InvalidDataException {
        
        List<Integer> numbers = new ArrayList<>();
        
        try (BufferedReader reader = new BufferedReader(new FileReader(filename))) {
            String line;
            int lineNumber = 0;
            
            while ((line = reader.readLine()) != null) {
                lineNumber++;
                line = line.trim();
                
                // Skip empty lines
                if (line.isEmpty()) continue;
                
                try {
                    int number = Integer.parseInt(line);
                    numbers.add(number);
                } catch (NumberFormatException e) {
                    throw new InvalidDataException(
                        "Invalid number at line " + lineNumber + ": " + line);
                }
            }
            
        } catch (IOException e) {
            throw new InvalidDataException("Error reading file: " + e.getMessage());
        }
        
        return numbers;
    }
    
    public static double calculateAverage(List<Integer> numbers) throws InvalidDataException {
        if (numbers.isEmpty()) {
            throw new InvalidDataException("No numbers to calculate average");
        }
        
        int sum = 0;
        for (int number : numbers) {
            if (number < 0) {
                throw new InvalidDataException("Negative number found: " + number);
            }
            sum += number;
        }
        
        return (double) sum / numbers.size();
    }
    
    public static int findMax(List<Integer> numbers) {
        return Collections.max(numbers);
    }
    
    public static int findMin(List<Integer> numbers) {
        return Collections.min(numbers);
    }
}

// Custom exception
class InvalidDataException extends Exception {
    public InvalidDataException(String message) {
        super(message);
    }
}
```

## Best Practices
- Handle exceptions at the appropriate level
- Use specific exceptions rather than general ones
- Use try-with-resources for automatic resource management
- Create custom exceptions for domain-specific errors
- Log exceptions with sufficient context
- Don't swallow exceptions (empty catch blocks)
- Use finally blocks for cleanup
- Document exceptions in method signatures
- Consider performance implications of exception handling
- Use exceptions for exceptional conditions, not normal flow control
