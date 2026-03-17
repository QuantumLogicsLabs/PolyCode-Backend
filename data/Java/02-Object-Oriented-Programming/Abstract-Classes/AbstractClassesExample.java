public class AbstractClassesExample {
    public static void main(String[] args) {
        // Cannot instantiate abstract class directly
        // Animal animal = new Animal(); // Compilation error
        
        // Create concrete subclasses
        Dog dog = new Dog("Buddy", "Golden Retriever");
        Cat cat = new Cat("Whiskers", "Persian");
        Bird bird = new Bird("Tweety", "Canary");
        
        // Use abstraction - treat different animals uniformly
        Animal[] animals = {dog, cat, bird};
        
        System.out.println("=== Abstract Class Demonstration ===");
        for (Animal animal : animals) {
            System.out.println("\n--- " + animal.getName() + " ---");
            animal.makeSound();      // Abstract method
            animal.eat();           // Concrete method
            animal.sleep();         // Concrete method
            animal.move();          // Abstract method
            animal.dailyRoutine();  // Template method
            System.out.println("Species: " + animal.getSpecies());
            System.out.println("Habitat: " + animal.getHabitat());
        }
        
        // Abstract class with constructors
        System.out.println("\n=== Abstract Class with Constructors ===");
        
        Vehicle car = new Car("Toyota", "Camry", 2023);
        Vehicle motorcycle = new Motorcycle("Harley", "Sportster", 2022);
        
        car.startEngine();
        car.accelerate();
        car.brake();
        System.out.println("Vehicle info: " + car.getVehicleInfo());
        
        motorcycle.startEngine();
        motorcycle.accelerate();
        motorcycle.brake();
        System.out.println("Vehicle info: " + motorcycle.getVehicleInfo());
        
        // Abstract class with final methods
        System.out.println("\n=== Abstract Class with Final Methods ===");
        
        Employee[] employees = {
            new FullTimeEmployee("Alice", 50000),
            new PartTimeEmployee("Bob", 15, 20),
            new ContractEmployee("Charlie", 100000)
        };
        
        for (Employee emp : employees) {
            System.out.println("\n--- " + emp.getName() + " ---");
            emp.calculateSalary(); // Template method
            System.out.println("Type: " + emp.getEmployeeType());
            System.out.println("Benefits: " + emp.getBenefits());
        }
        
        // Abstract class with static members
        System.out.println("\n=== Abstract Class with Static Members ===");
        System.out.println("Total animals created: " + Animal.getAnimalCount());
        System.out.println("Total vehicles created: " + Vehicle.getVehicleCount());
        
        // Abstract class vs concrete class comparison
        System.out.println("\n=== Abstract vs Concrete Class ===");
        demonstrateComparison();
    }
    
    public static void demonstrateComparison() {
        System.out.println("Abstract Class:");
        System.out.println("- Cannot be instantiated");
        System.out.println("- Can have abstract methods");
        System.out.println("- Can have concrete methods");
        System.out.println("- Can have constructors");
        System.out.println("- Can have instance variables");
        System.out.println("- Supports inheritance");
        
        System.out.println("\nConcrete Class:");
        System.out.println("- Can be instantiated");
        System.out.println("- All methods must be implemented");
        System.out.println("- Can have constructors");
        System.out.println("- Can have instance variables");
        System.out.println("- Supports inheritance");
    }
}

// Basic abstract class
abstract class Animal {
    protected String name;
    protected String species;
    private static int animalCount = 0;
    
    // Abstract constructor (called by subclasses)
    public Animal(String name, String species) {
        this.name = name;
        this.species = species;
        animalCount++;
        System.out.println("Animal constructor called for " + name);
    }
    
    // Abstract methods - must be implemented by subclasses
    public abstract void makeSound();
    public abstract void move();
    public abstract String getHabitat();
    
    // Concrete methods - inherited by subclasses
    public void eat() {
        System.out.println(name + " is eating");
    }
    
    public void sleep() {
        System.out.println(name + " is sleeping");
    }
    
    // Template method - defines algorithm structure
    public void dailyRoutine() {
        wakeUp();
        eat();
        move();
        makeSound();
        sleep();
    }
    
    // Final method - cannot be overridden
    public final void wakeUp() {
        System.out.println(name + " is waking up");
    }
    
    // Static method
    public static int getAnimalCount() {
        return animalCount;
    }
    
    // Getters
    public String getName() {
        return name;
    }
    
    public String getSpecies() {
        return species;
    }
    
    // Concrete method with abstract behavior
    public void displayInfo() {
        System.out.println("Animal: " + name);
        System.out.println("Species: " + species);
        System.out.println("Habitat: " + getHabitat());
    }
}

// Concrete subclasses
class Dog extends Animal {
    private String breed;
    
    public Dog(String name, String breed) {
        super(name, "Dog");
        this.breed = breed;
    }
    
    @Override
    public void makeSound() {
        System.out.println(name + " barks: Woof! Woof!");
    }
    
    @Override
    public void move() {
        System.out.println(name + " runs on four legs");
    }
    
    @Override
    public String getHabitat() {
        return "Domestic - Lives with humans";
    }
    
    // Additional method specific to Dog
    public void wagTail() {
        System.out.println(name + " wags tail happily");
    }
    
    @Override
    public void dailyRoutine() {
        super.dailyRoutine();
        wagTail(); // Add dog-specific behavior
    }
}

class Cat extends Animal {
    private String breed;
    
    public Cat(String name, String breed) {
        super(name, "Cat");
        this.breed = breed;
    }
    
    @Override
    public void makeSound() {
        System.out.println(name + " meows: Meow! Meow!");
    }
    
    @Override
    public void move() {
        System.out.println(name + " walks gracefully");
    }
    
    @Override
    public String getHabitat() {
        return "Domestic - Independent hunter";
    }
    
    public void purr() {
        System.out.println(name + " purrs contentedly");
    }
}

class Bird extends Animal {
    private String type;
    
    public Bird(String name, String type) {
        super(name, "Bird");
        this.type = type;
    }
    
    @Override
    public void makeSound() {
        System.out.println(name + " chirps: Tweet! Tweet!");
    }
    
    @Override
    public void move() {
        System.out.println(name + " flies in the sky");
    }
    
    @Override
    public String getHabitat() {
        return "Aerial - Nests in trees";
    }
    
    public void sing() {
        System.out.println(name + " sings beautifully");
    }
}

// Abstract class with constructors and protected members
abstract class Vehicle {
    protected String brand;
    protected String model;
    protected int year;
    private static int vehicleCount = 0;
    
    // Protected constructor
    protected Vehicle(String brand, String model, int year) {
        this.brand = brand;
        this.model = model;
        this.year = year;
        vehicleCount++;
        System.out.println("Vehicle constructor called");
    }
    
    // Abstract methods
    public abstract void startEngine();
    public abstract void accelerate();
    public abstract void brake();
    public abstract String getVehicleType();
    
    // Concrete methods
    public void stop() {
        System.out.println(brand + " " + model + " stopping");
    }
    
    public void honk() {
        System.out.println(brand + " " + model + " honking");
    }
    
    // Template method
    public final void startVehicle() {
        startEngine();
        System.out.println("Vehicle started successfully");
    }
    
    // Static method
    public static int getVehicleCount() {
        return vehicleCount;
    }
    
    // Getter
    public String getVehicleInfo() {
        return year + " " + brand + " " + model;
    }
}

class Car extends Vehicle {
    public Car(String brand, String model, int year) {
        super(brand, model, year);
    }
    
    @Override
    public void startEngine() {
        System.out.println(brand + " car engine starting with ignition");
    }
    
    @Override
    public void accelerate() {
        System.out.println(brand + " car accelerating");
    }
    
    @Override
    public void brake() {
        System.out.println(brand + " car braking with ABS");
    }
    
    @Override
    public String getVehicleType() {
        return "Car";
    }
}

class Motorcycle extends Vehicle {
    public Motorcycle(String brand, String model, int year) {
        super(brand, model, year);
    }
    
    @Override
    public void startEngine() {
        System.out.println(brand + " motorcycle engine starting with kick start");
    }
    
    @Override
    public void accelerate() {
        System.out.println(brand + " motorcycle accelerating quickly");
    }
    
    @Override
    public void brake() {
        System.out.println(brand + " motorcycle braking");
    }
    
    @Override
    public String getVehicleType() {
        return "Motorcycle";
    }
}

// Abstract class with final template methods
abstract class Employee {
    protected String name;
    protected double baseSalary;
    
    public Employee(String name, double baseSalary) {
        this.name = name;
        this.baseSalary = baseSalary;
    }
    
    // Abstract methods
    protected abstract double calculateNetSalary();
    public abstract String getEmployeeType();
    public abstract String getBenefits();
    
    // Final template method
    public final void calculateSalary() {
        double netSalary = calculateNetSalary();
        System.out.println(name + "'s net salary: $" + netSalary);
    }
    
    // Concrete method
    public void displayInfo() {
        System.out.println("Employee: " + name);
        System.out.println("Type: " + getEmployeeType());
        System.out.println("Benefits: " + getBenefits());
    }
    
    // Getter
    public String getName() {
        return name;
    }
}

class FullTimeEmployee extends Employee {
    public FullTimeEmployee(String name, double baseSalary) {
        super(name, baseSalary);
    }
    
    @Override
    protected double calculateNetSalary() {
        return baseSalary * 0.8; // 20% tax deduction
    }
    
    @Override
    public String getEmployeeType() {
        return "Full-time";
    }
    
    @Override
    public String getBenefits() {
        return "Health insurance, retirement plan, paid leave";
    }
}

class PartTimeEmployee extends Employee {
    private double hourlyRate;
    private int hoursWorked;
    
    public PartTimeEmployee(String name, double hourlyRate, int hoursWorked) {
        super(name, 0); // No base salary for part-time
        this.hourlyRate = hourlyRate;
        this.hoursWorked = hoursWorked;
    }
    
    @Override
    protected double calculateNetSalary() {
        return hourlyRate * hoursWorked * 0.85; // 15% tax deduction
    }
    
    @Override
    public String getEmployeeType() {
        return "Part-time";
    }
    
    @Override
    public String getBenefits() {
        return "Flexible hours, no health insurance";
    }
}

class ContractEmployee extends Employee {
    public ContractEmployee(String name, double contractAmount) {
        super(name, contractAmount);
    }
    
    @Override
    protected double calculateNetSalary() {
        return baseSalary * 0.9; // 10% tax deduction
    }
    
    @Override
    public String getEmployeeType() {
        return "Contract";
    }
    
    @Override
    public String getBenefits() {
        return "No benefits, higher pay rate";
    }
}
