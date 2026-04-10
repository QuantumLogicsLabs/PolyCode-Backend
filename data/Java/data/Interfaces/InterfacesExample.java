public class InterfacesExample {
    public static void main(String[] args) {
        // Basic Interface Implementation
        System.out.println("=== Basic Interface Implementation ===");
        
        Drawable circle = new Circle(5);
        Drawable rectangle = new Rectangle(4, 6);
        
        circle.draw();
        rectangle.draw();
        
        // Multiple Interface Implementation
        System.out.println("\n=== Multiple Interface Implementation ===");
        
        SmartDevice phone = new SmartDevice("iPhone");
        
        // As a Phone
        ((Phone) phone).makeCall("123-456-7890");
        
        // As a Camera
        ((Camera) phone).takePhoto();
        
        // As a Computer
        ((Computer) phone).browseInternet();
        
        // Interface Default Methods
        System.out.println("\n=== Interface Default Methods ===");
        
        ElectronicDevice laptop = new Laptop("Dell");
        laptop.powerOn();
        laptop.getDeviceInfo();
        laptop.checkBattery();
        
        // Interface Static Methods
        System.out.println("\n=== Interface Static Methods ===");
        Vehicle.displayInfo();
        System.out.println("Max vehicles allowed: " + Vehicle.getMaxVehicles());
        
        // Functional Interfaces
        System.out.println("\n=== Functional Interfaces ===");
        
        // Using lambda expressions
        Calculator add = (a, b) -> a + b;
        Calculator multiply = (a, b) -> a * b;
        
        System.out.println("Add: " + add.calculate(5, 3));
        System.out.println("Multiply: " + multiply.calculate(5, 3));
        
        // Marker Interfaces
        System.out.println("\n=== Marker Interfaces ===");
        
        SerializableObject obj = new SerializableObject("Test Data");
        System.out.println("Is serializable: " + (obj instanceof Serializable));
        
        // Interface Inheritance
        System.out.println("\n=== Interface Inheritance ===");
        
        AdvancedCar car = new AdvancedCar("Tesla");
        car.start();           // From Vehicle
        car.fly();             // From FlyingVehicle
        selfDrive();           // From AutonomousVehicle (static method)
        
        // Interface as Types
        System.out.println("\n=== Interface as Types ===");
        
        Vehicle[] vehicles = {new Car("Toyota"), new Motorcycle("Harley")};
        for (Vehicle v : vehicles) {
            v.start();
            v.accelerate();
        }
        
        // Interface Constants
        System.out.println("\n=== Interface Constants ===");
        System.out.println("PI value: " + MathConstants.PI);
        System.out.println("E value: " + MathConstants.E);
        System.out.println("Golden ratio: " + MathConstants.GOLDEN_RATIO);
    }
    
    // Static method from AutonomousVehicle interface
    public static void selfDrive() {
        System.out.println("Engaging autonomous driving mode");
    }
}

// Basic Interface
interface Drawable {
    void draw();
    
    // Constant
    int DEFAULT_SIZE = 100;
    
    // Default method (Java 8+)
    default void resize(int newSize) {
        System.out.println("Resizing to " + newSize);
    }
    
    // Static method (Java 8+)
    static void showInfo() {
        System.out.println("Drawable interface for shapes");
    }
}

// Implementing classes
class Circle implements Drawable {
    private double radius;
    
    public Circle(double radius) {
        this.radius = radius;
    }
    
    @Override
    public void draw() {
        System.out.println("Drawing circle with radius " + radius);
    }
}

class Rectangle implements Drawable {
    private double width, height;
    
    public Rectangle(double width, double height) {
        this.width = width;
        this.height = height;
    }
    
    @Override
    public void draw() {
        System.out.println("Drawing rectangle " + width + "x" + height);
    }
}

// Multiple interfaces
interface Phone {
    void makeCall(String number);
    void receiveCall(String number);
}

interface Camera {
    void takePhoto();
    void recordVideo();
}

interface Computer {
    void browseInternet();
    void runApplication(String name);
}

class SmartDevice implements Phone, Camera, Computer {
    private String model;
    
    public SmartDevice(String model) {
        this.model = model;
    }
    
    @Override
    public void makeCall(String number) {
        System.out.println(model + " calling " + number);
    }
    
    @Override
    public void receiveCall(String number) {
        System.out.println(model + " receiving call from " + number);
    }
    
    @Override
    public void takePhoto() {
        System.out.println(model + " taking photo");
    }
    
    @Override
    public void recordVideo() {
        System.out.println(model + " recording video");
    }
    
    @Override
    public void browseInternet() {
        System.out.println(model + " browsing internet");
    }
    
    @Override
    public void runApplication(String name) {
        System.out.println(model + " running " + name);
    }
}

// Interface with default methods
interface ElectronicDevice {
    void powerOn();
    void powerOff();
    
    // Default methods
    default void getDeviceInfo() {
        System.out.println("Device: " + this.getClass().getSimpleName());
    }
    
    default void checkBattery() {
        System.out.println("Checking battery level...");
    }
    
    // Static method
    static void showAllDevices() {
        System.out.println("All electronic devices support power management");
    }
}

class Laptop implements ElectronicDevice {
    private String brand;
    
    public Laptop(String brand) {
        this.brand = brand;
    }
    
    @Override
    public void powerOn() {
        System.out.println(brand + " laptop is booting up");
    }
    
    @Override
    public void powerOff() {
        System.out.println(brand + " laptop is shutting down");
    }
}

// Interface with static methods
interface Vehicle {
    void start();
    void accelerate();
    void brake();
    void stop();
    
    // Constants
    int MAX_VEHICLES = 1000;
    String DEFAULT_FUEL = "Gasoline";
    
    // Static methods
    static void displayInfo() {
        System.out.println("Vehicles are means of transportation");
    }
    
    static int getMaxVehicles() {
        return MAX_VEHICLES;
    }
    
    // Default method
    default void maintenance() {
        System.out.println("Performing regular maintenance");
    }
}

class Car implements Vehicle {
    private String model;
    
    public Car(String model) {
        this.model = model;
    }
    
    @Override
    public void start() {
        System.out.println(model + " car starting");
    }
    
    @Override
    public void accelerate() {
        System.out.println(model + " car accelerating");
    }
    
    @Override
    public void brake() {
        System.out.println(model + " car braking");
    }
    
    @Override
    public void stop() {
        System.out.println(model + " car stopping");
    }
}

class Motorcycle implements Vehicle {
    private String brand;
    
    public Motorcycle(String brand) {
        this.brand = brand;
    }
    
    @Override
    public void start() {
        System.out.println(brand + " motorcycle starting");
    }
    
    @Override
    public void accelerate() {
        System.out.println(brand + " motorcycle accelerating");
    }
    
    @Override
    public void brake() {
        System.out.println(brand + " motorcycle braking");
    }
    
    @Override
    public void stop() {
        System.out.println(brand + " motorcycle stopping");
    }
}

// Functional Interface
@FunctionalInterface
interface Calculator {
    double calculate(double a, double b);
    
    // Can have default methods
    default void showResult(double a, double b) {
        System.out.println("Result: " + calculate(a, b));
    }
    
    // Can have static methods
    static void showInfo() {
        System.out.println("Calculator functional interface");
    }
}

// Marker Interface (no methods)
interface Serializable {
    // Marker interface - no methods
}

class SerializableObject implements Serializable {
    private String data;
    
    public SerializableObject(String data) {
        this.data = data;
    }
    
    public String getData() {
        return data;
    }
}

// Interface Inheritance
interface FlyingVehicle extends Vehicle {
    void fly();
    void land();
    
    // Can add new default methods
    default void preFlightCheck() {
        System.out.println("Performing pre-flight check");
    }
}

interface AutonomousVehicle extends Vehicle {
    void enableAutopilot();
    void disableAutopilot();
    
    // Static method
    static void selfDrive() {
        System.out.println("Autonomous driving engaged");
    }
}

class AdvancedCar implements FlyingVehicle, AutonomousVehicle {
    private String model;
    
    public AdvancedCar(String model) {
        this.model = model;
    }
    
    // From Vehicle interface
    @Override
    public void start() {
        System.out.println(model + " advanced car starting");
    }
    
    @Override
    public void accelerate() {
        System.out.println(model + " advanced car accelerating");
    }
    
    @Override
    public void brake() {
        System.out.println(model + " advanced car braking");
    }
    
    @Override
    public void stop() {
        System.out.println(model + " advanced car stopping");
    }
    
    // From FlyingVehicle interface
    @Override
    public void fly() {
        System.out.println(model + " taking off");
    }
    
    @Override
    public void land() {
        System.out.println(model + " landing");
    }
    
    // From AutonomousVehicle interface
    @Override
    public void enableAutopilot() {
        System.out.println(model + " autopilot enabled");
    }
    
    @Override
    public void disableAutopilot() {
        System.out.println(model + " autopilot disabled");
    }
}

// Interface for constants
interface MathConstants {
    double PI = 3.14159265359;
    double E = 2.71828182846;
    double GOLDEN_RATIO = 1.61803398875;
    
    // Static methods for calculations
    static double circleArea(double radius) {
        return PI * radius * radius;
    }
    
    static double fibonacciRatio() {
        return GOLDEN_RATIO;
    }
}
