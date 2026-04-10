# Java Object-Oriented Programming

## Classes and Objects

### Class Definition
```java
public class Person {
    // Instance variables (fields)
    private String name;
    private int age;
    private String email;
    
    // Constructor
    public Person(String name, int age, String email) {
        this.name = name;
        this.age = age;
        this.email = email;
    }
    
    // Default constructor
    public Person() {
        this.name = "Unknown";
        this.age = 0;
        this.email = "unknown@example.com";
    }
    
    // Methods
    public void displayInfo() {
        System.out.println("Name: " + name);
        System.out.println("Age: " + age);
        System.out.println("Email: " + email);
    }
    
    // Getters and setters
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }
    
    public int getAge() { return age; }
    public void setAge(int age) { this.age = age; }
    
    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }
}
```

### Object Creation
```java
// Creating objects
Person person1 = new Person("John Doe", 30, "john@example.com");
Person person2 = new Person();

// Using methods
person1.displayInfo();
person2.setName("Jane Smith");
person2.setAge(25);
person2.displayInfo();

// Accessing properties
String name = person1.getName();
int age = person1.getAge();
```

## Encapsulation

### Access Modifiers
```java
public class BankAccount {
    // Private fields - encapsulation
    private String accountNumber;
    private double balance;
    private String ownerName;
    
    // Public methods - controlled access
    public BankAccount(String accountNumber, String ownerName, double initialBalance) {
        this.accountNumber = accountNumber;
        this.ownerName = ownerName;
        this.balance = initialBalance;
    }
    
    // Public getter for balance (read-only access)
    public double getBalance() {
        return balance;
    }
    
    // Public method for deposit (with validation)
    public void deposit(double amount) {
        if (amount > 0) {
            balance += amount;
            System.out.println("Deposited: $" + amount);
        } else {
            System.out.println("Invalid deposit amount");
        }
    }
    
    // Public method for withdrawal (with validation)
    public void withdraw(double amount) {
        if (amount > 0 && amount <= balance) {
            balance -= amount;
            System.out.println("Withdrew: $" + amount);
        } else {
            System.out.println("Invalid withdrawal amount");
        }
    }
    
    // Private helper method
    private boolean isValidAmount(double amount) {
        return amount > 0;
    }
}
```

## Inheritance

### Superclass and Subclass
```java
// Superclass
public class Animal {
    protected String name;
    protected int age;
    
    public Animal(String name, int age) {
        this.name = name;
        this.age = age;
    }
    
    public void eat() {
        System.out.println(name + " is eating");
    }
    
    public void sleep() {
        System.out.println(name + " is sleeping");
    }
    
    public void makeSound() {
        System.out.println(name + " makes a sound");
    }
    
    // Getters
    public String getName() { return name; }
    public int getAge() { return age; }
}

// Subclass
public class Dog extends Animal {
    private String breed;
    
    public Dog(String name, int age, String breed) {
        super(name, age); // Call superclass constructor
        this.breed = breed;
    }
    
    // Override superclass method
    @Override
    public void makeSound() {
        System.out.println(name + " barks: Woof!");
    }
    
    // New method specific to Dog
    public void wagTail() {
        System.out.println(name + " wags tail");
    }
    
    // Getter
    public String getBreed() { return breed; }
}

// Another subclass
public class Cat extends Animal {
    private boolean isIndoor;
    
    public Cat(String name, int age, boolean isIndoor) {
        super(name, age);
        this.isIndoor = isIndoor;
    }
    
    @Override
    public void makeSound() {
        System.out.println(name + " meows: Meow!");
    }
    
    public void purr() {
        System.out.println(name + " purrs");
    }
    
    public boolean isIndoor() { return isIndoor; }
}
```

### Using Inheritance
```java
public class AnimalDemo {
    public static void main(String[] args) {
        // Create objects
        Dog dog = new Dog("Buddy", 3, "Golden Retriever");
        Cat cat = new Cat("Whiskers", 2, true);
        
        // Use inherited methods
        dog.eat();    // From Animal class
        dog.sleep();  // From Animal class
        dog.makeSound(); // Overridden in Dog
        dog.wagTail(); // Dog-specific
        
        cat.eat();    // From Animal class
        cat.sleep();  // From Animal class
        cat.makeSound(); // Overridden in Cat
        cat.purr();   // Cat-specific
        
        // Polymorphism
        Animal animal1 = new Dog("Max", 4, "Labrador");
        Animal animal2 = new Cat("Luna", 1, false);
        
        animal1.makeSound(); // Calls Dog's makeSound
        animal2.makeSound(); // Calls Cat's makeSound
    }
}
```

## Polymorphism

### Method Overriding
```java
public class Shape {
    protected String color;
    
    public Shape(String color) {
        this.color = color;
    }
    
    public double getArea() {
        return 0.0; // Default implementation
    }
    
    public void draw() {
        System.out.println("Drawing a " + color + " shape");
    }
}

public class Circle extends Shape {
    private double radius;
    
    public Circle(String color, double radius) {
        super(color);
        this.radius = radius;
    }
    
    @Override
    public double getArea() {
        return Math.PI * radius * radius;
    }
    
    @Override
    public void draw() {
        System.out.println("Drawing a " + color + " circle with radius " + radius);
    }
}

public class Rectangle extends Shape {
    private double width;
    private double height;
    
    public Rectangle(String color, double width, double height) {
        super(color);
        this.width = width;
        this.height = height;
    }
    
    @Override
    public double getArea() {
        return width * height;
    }
    
    @Override
    public void draw() {
        System.out.println("Drawing a " + color + " rectangle " + width + "x" + height);
    }
}
```

### Polymorphic Behavior
```java
public class ShapeDemo {
    public static void main(String[] args) {
        // Create shapes array
        Shape[] shapes = {
            new Circle("red", 5.0),
            new Rectangle("blue", 4.0, 6.0),
            new Circle("green", 3.0)
        };
        
        // Polymorphic behavior
        for (Shape shape : shapes) {
            shape.draw(); // Calls appropriate draw method
            System.out.println("Area: " + shape.getArea());
            System.out.println();
        }
        
        // Method with polymorphic parameter
        printShapeInfo(new Circle("yellow", 2.5));
        printShapeInfo(new Rectangle("purple", 3.0, 4.0));
    }
    
    public static void printShapeInfo(Shape shape) {
        System.out.println("Shape info:");
        shape.draw();
        System.out.println("Area: " + shape.getArea());
    }
}
```

## Abstraction

### Abstract Classes
```java
public abstract class Vehicle {
    protected String brand;
    protected String model;
    protected int year;
    
    public Vehicle(String brand, String model, int year) {
        this.brand = brand;
        this.model = model;
        this.year = year;
    }
    
    // Concrete method
    public void displayInfo() {
        System.out.println(year + " " + brand + " " + model);
    }
    
    // Abstract methods (must be implemented by subclasses)
    public abstract void start();
    public abstract void stop();
    public abstract double getFuelEfficiency();
}

public class Car extends Vehicle {
    private int numberOfDoors;
    
    public Car(String brand, String model, int year, int numberOfDoors) {
        super(brand, model, year);
        this.numberOfDoors = numberOfDoors;
    }
    
    @Override
    public void start() {
        System.out.println("Car engine starting...");
    }
    
    @Override
    public void stop() {
        System.out.println("Car engine stopping...");
    }
    
    @Override
    public double getFuelEfficiency() {
        return 25.5; // miles per gallon
    }
}

public class Motorcycle extends Vehicle {
    private boolean hasSidecar;
    
    public Motorcycle(String brand, String model, int year, boolean hasSidecar) {
        super(brand, model, year);
        this.hasSidecar = hasSidecar;
    }
    
    @Override
    public void start() {
        System.out.println("Motorcycle engine starting...");
    }
    
    @Override
    public void stop() {
        System.out.println("Motorcycle engine stopping...");
    }
    
    @Override
    public double getFuelEfficiency() {
        return 45.0; // miles per gallon
    }
}
```

### Interfaces
```java
public interface Drawable {
    void draw();
    void setColor(String color);
    String getColor();
}

public interface Movable {
    void move(int x, int y);
    int getX();
    int getY();
}

public class Point implements Drawable, Movable {
    private int x, y;
    private String color;
    
    public Point(int x, int y, String color) {
        this.x = x;
        this.y = y;
        this.color = color;
    }
    
    @Override
    public void draw() {
        System.out.println("Drawing point at (" + x + ", " + y + ") with color " + color);
    }
    
    @Override
    public void setColor(String color) {
        this.color = color;
    }
    
    @Override
    public String getColor() {
        return color;
    }
    
    @Override
    public void move(int x, int y) {
        this.x = x;
        this.y = y;
    }
    
    @Override
    public int getX() { return x; }
    
    @Override
    public int getY() { return y; }
}
```

## Examples

### Complete OOP Example
```java
public class BankingSystem {
    public static void main(String[] args) {
        // Create accounts
        SavingsAccount savings = new SavingsAccount("12345", "John Doe", 1000.0, 0.05);
        CheckingAccount checking = new CheckingAccount("67890", "Jane Smith", 500.0, 1000.0);
        
        // Use polymorphism
        Account[] accounts = {savings, checking};
        
        for (Account account : accounts) {
            account.displayInfo();
            account.deposit(200.0);
            account.withdraw(100.0);
            System.out.println("Balance: $" + account.getBalance());
            System.out.println();
        }
        
        // Specific method calls
        savings.addInterest();
        checking.checkOverdraft();
    }
}

// Abstract base class
abstract class Account {
    protected String accountNumber;
    protected String ownerName;
    protected double balance;
    
    public Account(String accountNumber, String ownerName, double balance) {
        this.accountNumber = accountNumber;
        this.ownerName = ownerName;
        this.balance = balance;
    }
    
    public abstract void withdraw(double amount);
    
    public void deposit(double amount) {
        if (amount > 0) {
            balance += amount;
            System.out.println("Deposited: $" + amount);
        }
    }
    
    public void displayInfo() {
        System.out.println("Account: " + accountNumber);
        System.out.println("Owner: " + ownerName);
        System.out.println("Balance: $" + balance);
    }
    
    // Getters
    public String getAccountNumber() { return accountNumber; }
    public String getOwnerName() { return ownerName; }
    public double getBalance() { return balance; }
}

// Concrete subclass
class SavingsAccount extends Account {
    private double interestRate;
    
    public SavingsAccount(String accountNumber, String ownerName, double balance, double interestRate) {
        super(accountNumber, ownerName, balance);
        this.interestRate = interestRate;
    }
    
    @Override
    public void withdraw(double amount) {
        if (amount > 0 && amount <= balance) {
            balance -= amount;
            System.out.println("Withdrew: $" + amount);
        } else {
            System.out.println("Invalid withdrawal amount");
        }
    }
    
    public void addInterest() {
        double interest = balance * interestRate;
        balance += interest;
        System.out.println("Interest added: $" + interest);
    }
}

// Another concrete subclass
class CheckingAccount extends Account {
    private double overdraftLimit;
    
    public CheckingAccount(String accountNumber, String ownerName, double balance, double overdraftLimit) {
        super(accountNumber, ownerName, balance);
        this.overdraftLimit = overdraftLimit;
    }
    
    @Override
    public void withdraw(double amount) {
        if (amount > 0 && amount <= (balance + overdraftLimit)) {
            balance -= amount;
            System.out.println("Withdrew: $" + amount);
        } else {
            System.out.println("Exceeds overdraft limit");
        }
    }
    
    public void checkOverdraft() {
        if (balance < 0) {
            System.out.println("Account is overdrawn by: $" + Math.abs(balance));
        } else {
            System.out.println("Account is not overdrawn");
        }
    }
}
```

## Best Practices
- Use private fields and public methods for encapsulation
- Favor composition over inheritance when appropriate
- Use abstract classes for shared implementation
- Use interfaces for shared behavior
- Follow single responsibility principle
- Use meaningful names for classes, methods, and variables
- Override toString() for better debugging
- Implement equals() and hashCode() for object comparison
- Use polymorphism to write flexible code
- Keep inheritance hierarchies shallow and focused
