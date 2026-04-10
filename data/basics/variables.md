<<<<<<< HEAD
# Variables in Java

Variables store data values.

## Example
```java
int age = 20;
String name = "Ali";
Practice

Declare variables for your name and age.
=======
# Java Variables

## Variable Declaration

### Basic Variables
```java
// Primitive variables
int age = 25;
double salary = 75000.50;
boolean isActive = true;
char grade = 'A';

// Reference variables
String name = "John Doe";
Integer count = 100;
Double price = 99.99;
```

### Variable Types
```java
// Primitive types
byte    // 8-bit integer
short   // 16-bit integer
int     // 32-bit integer
long    // 64-bit integer
float   // 32-bit floating point
double  // 64-bit floating point
boolean // true or false
char    // 16-bit Unicode character

// Reference types
String  // Text
Integer // Object wrapper for int
Double  // Object wrapper for double
Boolean // Object wrapper for boolean
```

## Variable Initialization

### Default Values
```java
// Instance variables have default values
class Person {
    int age;         // Default: 0
    double salary;   // Default: 0.0
    boolean active;  // Default: false
    String name;     // Default: null
}

// Local variables must be initialized
void method() {
    int count = 0;        // Must initialize
    String text = "hello"; // Must initialize
}
```

### Final Variables
```java
// Constants
final int MAX_SIZE = 100;
final String APP_NAME = "MyApp";
final double PI = 3.14159;

// Final reference (cannot be reassigned)
final List<String> names = new ArrayList<>();
names.add("Alice"); // OK
// names = new ArrayList<>(); // Error: final variable
```

## Variable Scope

### Class Variables
```java
public class Counter {
    // Static variable - shared across all instances
    private static int instanceCount = 0;
    
    // Instance variable - each object has its own copy
    private int count = 0;
    
    public Counter() {
        instanceCount++; // Increment shared count
        count++;         // Increment instance count
    }
}
```

### Local Variables
```java
public void process() {
    int localCount = 0; // Only visible in this method
    
    if (true) {
        int blockCount = 0; // Only visible in this block
        localCount++;
    }
    
    // blockCount++; // Error: not visible
}
```

## Type Conversion

### Implicit Conversion
```java
// Widening conversion (automatic)
int num = 100;
double d = num; // int to double (implicit)

// Narrowing conversion (explicit)
double d = 100.5;
int num = (int) d; // double to int (explicit)
```

### Wrapper Classes
```java
// Autoboxing and unboxing
Integer num = 100;        // Autoboxing int to Integer
int value = num;          // Unboxing Integer to int

// Using wrapper methods
String str = "123";
int num = Integer.parseInt(str); // String to int

Integer obj = Integer.valueOf(100); // int to Integer
String result = obj.toString();     // Integer to String
```

## Examples

### Variable Examples
```java
public class VariableExamples {
    // Class variables
    private static final String COMPANY = "TechCorp";
    private static int employeeCount = 0;
    
    // Instance variables
    private String name;
    private int age;
    private double salary;
    
    public VariableExamples(String name, int age, double salary) {
        this.name = name;
        this.age = age;
        this.salary = salary;
        employeeCount++;
    }
    
    public void displayInfo() {
        // Local variables
        String info = String.format("Name: %s, Age: %d, Salary: $%.2f", name, age, salary);
        System.out.println(info);
        System.out.println("Company: " + COMPANY);
        System.out.println("Total Employees: " + employeeCount);
    }
    
    public static void main(String[] args) {
        VariableExamples emp = new VariableExamples("Alice", 30, 75000.00);
        emp.displayInfo();
    }
}
```

## Best Practices
- Use meaningful variable names
- Initialize variables before use
- Use final for constants
- Use appropriate data types
- Follow naming conventions (camelCase)
- Declare variables in smallest possible scope
- Use wrapper classes when null values are needed
>>>>>>> 8f5b7d4c3fd7b8f91af9afa9065e83663bf4a476
