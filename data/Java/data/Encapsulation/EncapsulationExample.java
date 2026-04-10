public class EncapsulationExample {
    public static void main(String[] args) {
        // Basic Encapsulation Example
        System.out.println("=== Basic Encapsulation ===");
        BankAccount account = new BankAccount("123456789", "John Doe", 1000.0);
        
        // Access through public methods (not directly to private fields)
        System.out.println("Account Number: " + account.getAccountNumber());
        System.out.println("Account Holder: " + account.getAccountHolder());
        System.out.println("Balance: $" + account.getBalance());
        
        // Modifying data through controlled methods
        account.deposit(500.0);
        System.out.println("After deposit: $" + account.getBalance());
        
        boolean withdrawalSuccess = account.withdraw(200.0);
        System.out.println("Withdrawal successful: " + withdrawalSuccess);
        System.out.println("After withdrawal: $" + account.getBalance());
        
        // Attempting invalid withdrawal
        boolean invalidWithdrawal = account.withdraw(2000.0);
        System.out.println("Invalid withdrawal successful: " + invalidWithdrawal);
        System.out.println("Balance remains: $" + account.getBalance());
        
        // Encapsulation with Validation
        System.out.println("\n=== Encapsulation with Validation ===");
        Employee employee = new Employee();
        
        // Valid data
        employee.setName("Alice Smith");
        employee.setAge(25);
        employee.setSalary(75000.0);
        employee.setEmail("alice@company.com");
        
        System.out.println("Employee: " + employee.getName());
        System.out.println("Age: " + employee.getAge());
        System.out.println("Salary: $" + employee.getSalary());
        System.out.println("Email: " + employee.getEmail());
        System.out.println("Employee ID: " + employee.getEmployeeId());
        
        // Attempting invalid data
        System.out.println("\n=== Testing Invalid Data ===");
        employee.setAge(-5); // Invalid age
        employee.setSalary(-1000.0); // Invalid salary
        employee.setEmail("invalid-email"); // Invalid email
        
        System.out.println("Age after invalid input: " + employee.getAge());
        System.out.println("Salary after invalid input: " + employee.getSalary());
        System.out.println("Email after invalid input: " + employee.getEmail());
        
        // Immutable Object Example
        System.out.println("\n=== Immutable Object ===");
        ImmutablePerson person = new ImmutablePerson("Bob", 30, "bob@example.com");
        System.out.println("Person: " + person);
        
        // Cannot modify immutable object
        // person.name = "Alice"; // Compilation error
        // person.setAge(25); // No setter methods
        
        // Creating new instance for modification
        ImmutablePerson updatedPerson = person.withAge(31);
        System.out.println("Updated person: " + updatedPerson);
        System.out.println("Same object? " + (person == updatedPerson));
        
        // Encapsulation with Collections
        System.out.println("\n=== Encapsulation with Collections ===");
        StudentRoster roster = new StudentRoster();
        
        roster.addStudent("Alice");
        roster.addStudent("Bob");
        roster.addStudent("Charlie");
        
        System.out.println("Student count: " + roster.getStudentCount());
        System.out.println("Students: " + roster.getStudents());
        
        // Defensive copy prevents external modification
        java.util.List<String> students = roster.getStudents();
        students.add("Eve"); // This won't affect the original roster
        
        System.out.println("After external modification attempt:");
        System.out.println("Original roster: " + roster.getStudents());
        System.out.println("External list: " + students);
        
        // Encapsulation Benefits
        System.out.println("\n=== Encapsulation Benefits ===");
        demonstrateEncapsulationBenefits();
    }
    
    public static void demonstrateEncapsulationBenefits() {
        // Data Hiding
        System.out.println("1. Data Hiding:");
        BankAccount account = new BankAccount("987654321", "Jane Doe", 500.0);
        // Cannot directly access private fields:
        // account.balance = 1000000.0; // Compilation error
        // Must use public methods with validation
        
        // Controlled Access
        System.out.println("2. Controlled Access:");
        account.deposit(-100.0); // Will be rejected
        System.out.println("Balance after invalid deposit: $" + account.getBalance());
        
        // Flexibility in Implementation
        System.out.println("3. Implementation Flexibility:");
        System.out.println("Account details: " + account.getAccountInfo());
        // Internal implementation can change without affecting client code
        
        // Security
        System.out.println("4. Security:");
        // Sensitive data is protected from unauthorized access
        // Only validated operations are allowed
    }
}

// Basic encapsulation example
class BankAccount {
    // Private fields - hidden from outside access
    private String accountNumber;
    private String accountHolder;
    private double balance;
    
    // Public constructor
    public BankAccount(String accountNumber, String accountHolder, double initialBalance) {
        this.accountNumber = accountNumber;
        this.accountHolder = accountHolder;
        this.balance = Math.max(0, initialBalance); // Validation
    }
    
    // Public getters - controlled read access
    public String getAccountNumber() {
        return accountNumber;
    }
    
    public String getAccountHolder() {
        return accountHolder;
    }
    
    public double getBalance() {
        return balance;
    }
    
    // Public setters - controlled write access with validation
    public void setAccountHolder(String accountHolder) {
        if (accountHolder != null && !accountHolder.trim().isEmpty()) {
            this.accountHolder = accountHolder.trim();
        }
    }
    
    // Business methods with encapsulated logic
    public boolean deposit(double amount) {
        if (amount > 0) {
            balance += amount;
            System.out.println("Deposited: $" + amount);
            return true;
        }
        System.out.println("Invalid deposit amount: $" + amount);
        return false;
    }
    
    public boolean withdraw(double amount) {
        if (amount > 0 && amount <= balance) {
            balance -= amount;
            System.out.println("Withdrew: $" + amount);
            return true;
        }
        System.out.println("Invalid withdrawal amount: $" + amount);
        return false;
    }
    
    // Additional method showing encapsulation benefits
    public String getAccountInfo() {
        return String.format("Account %s (Holder: %s, Balance: $%.2f)", 
                           accountNumber, accountHolder, balance);
    }
}

// Encapsulation with comprehensive validation
class Employee {
    // Private fields with different access levels
    private static int nextId = 1;
    private final int employeeId; // Immutable after construction
    private String name;
    private int age;
    private double salary;
    private String email;
    
    // Public constructor
    public Employee() {
        this.employeeId = nextId++;
        this.name = "Unknown";
        this.age = 0;
        this.salary = 0.0;
        this.email = "unknown@company.com";
    }
    
    // Public constructor with parameters
    public Employee(String name, int age, double salary, String email) {
        this();
        setName(name);
        setAge(age);
        setSalary(salary);
        setEmail(email);
    }
    
    // Getters with appropriate access control
    public int getEmployeeId() {
        return employeeId; // Read-only (no setter)
    }
    
    public String getName() {
        return name;
    }
    
    public int getAge() {
        return age;
    }
    
    public double getSalary() {
        return salary;
    }
    
    public String getEmail() {
        return email;
    }
    
    // Setters with validation
    public void setName(String name) {
        if (name != null && !name.trim().isEmpty()) {
            this.name = name.trim();
        }
    }
    
    public void setAge(int age) {
        if (age >= 18 && age <= 65) {
            this.age = age;
        } else {
            System.out.println("Invalid age: " + age + ". Age must be between 18 and 65.");
        }
    }
    
    public void setSalary(double salary) {
        if (salary >= 0) {
            this.salary = salary;
        } else {
            System.out.println("Invalid salary: " + salary + ". Salary must be non-negative.");
        }
    }
    
    public void setEmail(String email) {
        if (isValidEmail(email)) {
            this.email = email;
        } else {
            System.out.println("Invalid email format: " + email);
        }
    }
    
    // Private helper method
    private boolean isValidEmail(String email) {
        return email != null && email.contains("@") && email.contains(".");
    }
    
    // Business method
    public void giveRaise(double percentage) {
        if (percentage > 0 && percentage <= 20) {
            salary *= (1 + percentage / 100);
            System.out.println("Raise given: " + percentage + "%. New salary: $" + salary);
        } else {
            System.out.println("Invalid raise percentage: " + percentage);
        }
    }
    
    @Override
    public String toString() {
        return String.format("Employee[ID: %d, Name: %s, Age: %d, Salary: $%.2f, Email: %s]",
                           employeeId, name, age, salary, email);
    }
}

// Immutable class example
final class ImmutablePerson {
    private final String name;
    private final int age;
    private final String email;
    
    public ImmutablePerson(String name, int age, String email) {
        this.name = name;
        this.age = age;
        this.email = email;
    }
    
    // Only getters - no setters
    public String getName() {
        return name;
    }
    
    public int getAge() {
        return age;
    }
    
    public String getEmail() {
        return email;
    }
    
    // Method to create new instance with modified value
    public ImmutablePerson withName(String newName) {
        return new ImmutablePerson(newName, this.age, this.email);
    }
    
    public ImmutablePerson withAge(int newAge) {
        return new ImmutablePerson(this.name, newAge, this.email);
    }
    
    public ImmutablePerson withEmail(String newEmail) {
        return new ImmutablePerson(this.name, this.age, newEmail);
    }
    
    @Override
    public String toString() {
        return String.format("Person[name=%s, age=%d, email=%s]", name, age, email);
    }
}

// Encapsulation with collections (defensive copying)
class StudentRoster {
    private final java.util.List<String> students = new java.util.ArrayList<>();
    
    public void addStudent(String studentName) {
        if (studentName != null && !studentName.trim().isEmpty()) {
            students.add(studentName.trim());
        }
    }
    
    public boolean removeStudent(String studentName) {
        return students.remove(studentName);
    }
    
    public int getStudentCount() {
        return students.size();
    }
    
    // Defensive copy - returns copy, not original
    public java.util.List<String> getStudents() {
        return new java.util.ArrayList<>(students);
    }
    
    // Read-only access
    public String getStudent(int index) {
        if (index >= 0 && index < students.size()) {
            return students.get(index);
        }
        return null;
    }
    
    @Override
    public String toString() {
        return "StudentRoster{students=" + students + "}";
    }
}
