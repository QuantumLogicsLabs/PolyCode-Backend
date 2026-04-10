public class AbstractionExample {
    public static void main(String[] args) {
        // Abstract Class Example
        System.out.println("=== Abstract Class Example ===");
        
        // Cannot instantiate abstract class directly
        // Animal animal = new Animal(); // Compilation error
        
        // Create concrete subclasses
        Dog dog = new Dog("Buddy");
        Cat cat = new Cat("Whiskers");
        Bird bird = new Bird("Tweety");
        
        // Use abstraction - treat different animals uniformly
        Animal[] animals = {dog, cat, bird};
        
        for (Animal animal : animals) {
            System.out.println("\n--- " + animal.getName() + " ---");
            animal.makeSound(); // Abstract method
            animal.eat(); // Concrete method
            animal.sleep(); // Concrete method
            animal.move(); // Abstract method
            System.out.println("Habitat: " + animal.getHabitat());
        }
        
        // Interface Example
        System.out.println("\n=== Interface Example ===");
        
        // Create objects that implement interfaces
        Car car = new Car("Toyota");
        Bicycle bicycle = new Bicycle("Mountain Bike");
        Boat boat = new Boat("Sailboat");
        
        // Use interface abstraction
        Vehicle[] vehicles = {car, bicycle, boat};
        
        for (Vehicle vehicle : vehicles) {
            System.out.println("\n--- " + vehicle.getVehicleName() + " ---");
            vehicle.start();
            vehicle.accelerate();
            vehicle.brake();
            vehicle.stop();
            System.out.println("Max speed: " + vehicle.getMaxSpeed() + " km/h");
        }
        
        // Multiple Interface Implementation
        System.out.println("\n=== Multiple Interface Implementation ===");
        
        Smartphone phone = new Smartphone("iPhone");
        
        // As a Phone
        phone.makeCall("123-456-7890");
        phone.receiveCall("098-765-4321");
        
        // As a Camera
        phone.takePhoto();
        phone.recordVideo();
        
        // As a MusicPlayer
        phone.playMusic();
        phone.pauseMusic();
        phone.stopMusic();
        
        // As a Computer
        phone.runApplication("Calculator");
        phone.browseInternet();
        
        // Interface Default Methods
        System.out.println("\n=== Interface Default Methods ===");
        
        ElectronicDevice device = phone;
        device.powerOn();
        device.powerOff();
        device.getDeviceInfo();
        
        // Abstract Class vs Interface
        System.out.println("\n=== Abstract Class vs Interface ===");
        
        demonstrateAbstractionTypes();
        
        // Practical Example - Payment System
        System.out.println("\n=== Payment System Example ===");
        
        PaymentProcessor processor = new PaymentProcessor();
        
        // Process different payment methods
        processor.processPayment(new CreditCardPayment(100.0));
        processor.processPayment(new PayPalPayment(50.0));
        processor.processPayment(new BankTransferPayment(200.0));
        
        // Template Method Pattern
        System.out.println("\n=== Template Method Pattern ===");
        
        Game[] games = {new Chess(), new Soccer(), new VideoGame()};
        
        for (Game game : games) {
            game.playGame(); // Template method
        }
    }
    
    public static void demonstrateAbstractionTypes() {
        System.out.println("Abstract Class:");
        System.out.println("- Can have both abstract and concrete methods");
        System.out.println("- Can have instance variables");
        System.out.println("- Supports single inheritance");
        System.out.println("- Can have constructors");
        
        System.out.println("\nInterface:");
        System.out.println("- All methods are abstract (default methods since Java 8)");
        System.out.println("- Only constants (static final) allowed");
        System.out.println("- Supports multiple inheritance");
        System.out.println("- Cannot have constructors (before Java 8)");
    }
}

// Abstract Class Example
abstract class Animal {
    protected String name;
    
    // Constructor
    public Animal(String name) {
        this.name = name;
    }
    
    // Abstract methods - must be implemented by subclasses
    public abstract void makeSound();
    public abstract void move();
    
    // Concrete methods - inherited by subclasses
    public void eat() {
        System.out.println(name + " is eating");
    }
    
    public void sleep() {
        System.out.println(name + " is sleeping");
    }
    
    // Concrete method with abstract behavior
    public void dailyRoutine() {
        wakeUp();
        eat();
        move();
        sleep();
    }
    
    // Helper method
    private void wakeUp() {
        System.out.println(name + " is waking up");
    }
    
    // Getter
    public String getName() {
        return name;
    }
    
    // Abstract method for additional information
    public abstract String getHabitat();
}

// Concrete subclasses
class Dog extends Animal {
    public Dog(String name) {
        super(name);
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
}

class Cat extends Animal {
    public Cat(String name) {
        super(name);
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
    public Bird(String name) {
        super(name);
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

// Interface Example
interface Vehicle {
    // Abstract methods
    void start();
    void accelerate();
    void brake();
    void stop();
    
    // Constants
    int MAX_VEHICLES = 100;
    
    // Default methods (since Java 8)
    default void maintenance() {
        System.out.println("Performing regular maintenance");
    }
    
    // Static methods (since Java 8)
    static void displayInfo() {
        System.out.println("Vehicles are means of transportation");
    }
    
    // Abstract method for additional info
    String getVehicleName();
    int getMaxSpeed();
}

// Concrete implementations
class Car implements Vehicle {
    private String model;
    
    public Car(String model) {
        this.model = model;
    }
    
    @Override
    public void start() {
        System.out.println(model + " engine starts with ignition key");
    }
    
    @Override
    public void accelerate() {
        System.out.println(model + " accelerates by pressing gas pedal");
    }
    
    @Override
    public void brake() {
        System.out.println(model + " brakes with disc brakes");
    }
    
    @Override
    public void stop() {
        System.out.println(model + " stops and engine turns off");
    }
    
    @Override
    public String getVehicleName() {
        return model;
    }
    
    @Override
    public int getMaxSpeed() {
        return 200;
    }
}

class Bicycle implements Vehicle {
    private String type;
    
    public Bicycle(String type) {
        this.type = type;
    }
    
    @Override
    public void start() {
        System.out.println(type + " is ready to ride");
    }
    
    @Override
    public void accelerate() {
        System.out.println(type + " accelerates by pedaling faster");
    }
    
    @Override
    public void brake() {
        System.out.println(type + " brakes with hand brakes");
    }
    
    @Override
    public void stop() {
        System.out.println(type + " stops and dismount");
    }
    
    @Override
    public String getVehicleName() {
        return type;
    }
    
    @Override
    public int getMaxSpeed() {
        return 30;
    }
}

class Boat implements Vehicle {
    private String name;
    
    public Boat(String name) {
        this.name = name;
    }
    
    @Override
    public void start() {
        System.out.println(name + " engine starts with key");
    }
    
    @Override
    public void accelerate() {
        System.out.println(name + " accelerates by increasing throttle");
    }
    
    @Override
    public void brake() {
        System.out.println(name + " slows down by reducing throttle");
    }
    
    @Override
    public void stop() {
        System.out.println(name + " stops and anchors");
    }
    
    @Override
    public String getVehicleName() {
        return name;
    }
    
    @Override
    public int getMaxSpeed() {
        return 60;
    }
}

// Multiple Interface Implementation
interface Phone {
    void makeCall(String number);
    void receiveCall(String number);
}

interface Camera {
    void takePhoto();
    void recordVideo();
}

interface MusicPlayer {
    void playMusic();
    void pauseMusic();
    void stopMusic();
}

interface Computer {
    void runApplication(String appName);
    void browseInternet();
}

// Class implementing multiple interfaces
class Smartphone implements Phone, Camera, MusicPlayer, Computer, ElectronicDevice {
    private String model;
    
    public Smartphone(String model) {
        this.model = model;
    }
    
    // Phone interface methods
    @Override
    public void makeCall(String number) {
        System.out.println(model + " calling " + number);
    }
    
    @Override
    public void receiveCall(String number) {
        System.out.println(model + " receiving call from " + number);
    }
    
    // Camera interface methods
    @Override
    public void takePhoto() {
        System.out.println(model + " taking photo");
    }
    
    @Override
    public void recordVideo() {
        System.out.println(model + " recording video");
    }
    
    // MusicPlayer interface methods
    @Override
    public void playMusic() {
        System.out.println(model + " playing music");
    }
    
    @Override
    public void pauseMusic() {
        System.out.println(model + " pausing music");
    }
    
    @Override
    public void stopMusic() {
        System.out.println(model + " stopping music");
    }
    
    // Computer interface methods
    @Override
    public void runApplication(String appName) {
        System.out.println(model + " running " + appName);
    }
    
    @Override
    public void browseInternet() {
        System.out.println(model + " browsing internet");
    }
    
    // ElectronicDevice interface methods
    @Override
    public void powerOn() {
        System.out.println(model + " powering on");
    }
    
    @Override
    public void powerOff() {
        System.out.println(model + " powering off");
    }
}

// Interface with default methods
interface ElectronicDevice {
    void powerOn();
    void powerOff();
    
    // Default method
    default void getDeviceInfo() {
        System.out.println("Device information: " + this.getClass().getSimpleName());
    }
    
    // Another default method
    default void checkBattery() {
        System.out.println("Checking battery level...");
    }
}

// Payment System Example - Abstract Classes
abstract class PaymentMethod {
    protected double amount;
    
    public PaymentMethod(double amount) {
        this.amount = amount;
    }
    
    // Template method - defines payment process
    public final void processPayment() {
        validatePayment();
        authenticateUser();
        debitAmount();
        sendConfirmation();
    }
    
    // Abstract methods to be implemented by subclasses
    protected abstract void validatePayment();
    protected abstract void authenticateUser();
    protected abstract void debitAmount();
    
    // Concrete method
    protected void sendConfirmation() {
        System.out.println("Payment of $" + amount + " processed successfully");
    }
}

class CreditCardPayment extends PaymentMethod {
    public CreditCardPayment(double amount) {
        super(amount);
    }
    
    @Override
    protected void validatePayment() {
        System.out.println("Validating credit card...");
    }
    
    @Override
    protected void authenticateUser() {
        System.out.println("Authenticating with credit card PIN...");
    }
    
    @Override
    protected void debitAmount() {
        System.out.println("Debiting $" + amount + " from credit card");
    }
}

class PayPalPayment extends PaymentMethod {
    public PayPalPayment(double amount) {
        super(amount);
    }
    
    @Override
    protected void validatePayment() {
        System.out.println("Validating PayPal account...");
    }
    
    @Override
    protected void authenticateUser() {
        System.out.println("Authenticating with PayPal credentials...");
    }
    
    @Override
    protected void debitAmount() {
        System.out.println("Debiting $" + amount + " from PayPal account");
    }
}

class BankTransferPayment extends PaymentMethod {
    public BankTransferPayment(double amount) {
        super(amount);
    }
    
    @Override
    protected void validatePayment() {
        System.out.println("Validating bank account details...");
    }
    
    @Override
    protected void authenticateUser() {
        System.out.println("Authenticating with banking credentials...");
    }
    
    @Override
    protected void debitAmount() {
        System.out.println("Transferring $" + amount + " from bank account");
    }
}

class PaymentProcessor {
    public void processPayment(PaymentMethod paymentMethod) {
        paymentMethod.processPayment();
    }
}

// Template Method Pattern
abstract class Game {
    // Template method - final to prevent overriding
    public final void playGame() {
        initializeGame();
        startGame();
        play();
        endGame();
        cleanup();
    }
    
    // Concrete methods
    private void initializeGame() {
        System.out.println("Initializing " + getGameName());
    }
    
    private void startGame() {
        System.out.println("Starting " + getGameName());
    }
    
    private void endGame() {
        System.out.println("Ending " + getGameName());
    }
    
    private void cleanup() {
        System.out.println("Cleaning up " + getGameName());
    }
    
    // Abstract methods to be implemented
    protected abstract void play();
    protected abstract String getGameName();
}

class Chess extends Game {
    @Override
    protected void play() {
        System.out.println("Playing chess - moving pieces strategically");
    }
    
    @Override
    protected String getGameName() {
        return "Chess";
    }
}

class Soccer extends Game {
    @Override
    protected void play() {
        System.out.println("Playing soccer - kicking ball and scoring goals");
    }
    
    @Override
    protected String getGameName() {
        return "Soccer";
    }
}

class VideoGame extends Game {
    @Override
    protected void play() {
        System.out.println("Playing video game - using controller and following storyline");
    }
    
    @Override
    protected String getGameName() {
        return "Video Game";
    }
}
