# Java Design Patterns

## Creational Patterns

### Singleton Pattern
```java
public class Singleton {
    // Private static instance
    private static volatile Singleton instance;
    
    // Private constructor
    private Singleton() {}
    
    // Double-checked locking
    public static Singleton getInstance() {
        if (instance == null) {
            synchronized (Singleton.class) {
                if (instance == null) {
                    instance = new Singleton();
                }
            }
        }
        return instance;
    }
    
    // Method
    public void doSomething() {
        System.out.println("Singleton is working");
    }
}

// Usage
public class SingletonDemo {
    public static void main(String[] args) {
        Singleton singleton1 = Singleton.getInstance();
        Singleton singleton2 = Singleton.getInstance();
        
        System.out.println("Same instance: " + (singleton1 == singleton2));
        singleton1.doSomething();
    }
}
```

### Factory Pattern
```java
// Interface
interface Shape {
    void draw();
}

// Concrete classes
class Circle implements Shape {
    @Override
    public void draw() {
        System.out.println("Drawing a circle");
    }
}

class Rectangle implements Shape {
    @Override
    public void draw() {
        System.out.println("Drawing a rectangle");
    }
}

class Triangle implements Shape {
    @Override
    public void draw() {
        System.out.println("Drawing a triangle");
    }
}

// Factory class
class ShapeFactory {
    public Shape createShape(String shapeType) {
        if (shapeType == null || shapeType.isEmpty()) {
            return null;
        }
        
        switch (shapeType.toUpperCase()) {
            case "CIRCLE":
                return new Circle();
            case "RECTANGLE":
                return new Rectangle();
            case "TRIANGLE":
                return new Triangle();
            default:
                throw new IllegalArgumentException("Unknown shape type: " + shapeType);
        }
    }
}

// Usage
public class FactoryDemo {
    public static void main(String[] args) {
        ShapeFactory factory = new ShapeFactory();
        
        Shape circle = factory.createShape("circle");
        circle.draw();
        
        Shape rectangle = factory.createShape("rectangle");
        rectangle.draw();
    }
}
```

### Builder Pattern
```java
public class Computer {
    private final String cpu;
    private final String ram;
    private final String storage;
    private final String gpu;
    private final boolean hasWifi;
    private final boolean hasBluetooth;
    
    // Private constructor
    private Computer(Builder builder) {
        this.cpu = builder.cpu;
        this.ram = builder.ram;
        this.storage = builder.storage;
        this.gpu = builder.gpu;
        this.hasWifi = builder.hasWifi;
        this.hasBluetooth = builder.hasBluetooth;
    }
    
    // Getters
    public String getCpu() { return cpu; }
    public String getRam() { return ram; }
    public String getStorage() { return storage; }
    public String getGpu() { return gpu; }
    public boolean hasWifi() { return hasWifi; }
    public boolean hasBluetooth() { return hasBluetooth; }
    
    @Override
    public String toString() {
        return "Computer{" +
                "cpu='" + cpu + '\'' +
                ", ram='" + ram + '\'' +
                ", storage='" + storage + '\'' +
                ", gpu='" + gpu + '\'' +
                ", hasWifi=" + hasWifi +
                ", hasBluetooth=" + hasBluetooth +
                '}';
    }
    
    // Builder class
    public static class Builder {
        private String cpu;
        private String ram;
        private String storage;
        private String gpu;
        private boolean hasWifi;
        private boolean hasBluetooth;
        
        public Builder setCpu(String cpu) {
            this.cpu = cpu;
            return this;
        }
        
        public Builder setRam(String ram) {
            this.ram = ram;
            return this;
        }
        
        public Builder setStorage(String storage) {
            this.storage = storage;
            return this;
        }
        
        public Builder setGpu(String gpu) {
            this.gpu = gpu;
            return this;
        }
        
        public Builder setHasWifi(boolean hasWifi) {
            this.hasWifi = hasWifi;
            return this;
        }
        
        public Builder setHasBluetooth(boolean hasBluetooth) {
            this.hasBluetooth = hasBluetooth;
            return this;
        }
        
        public Computer build() {
            return new Computer(this);
        }
    }
}

// Usage
public class BuilderDemo {
    public static void main(String[] args) {
        Computer gamingPC = new Computer.Builder()
            .setCpu("Intel i9")
            .setRam("32GB")
            .setStorage("1TB SSD")
            .setGpu("NVIDIA RTX 3080")
            .setHasWifi(true)
            .setHasBluetooth(true)
            .build();
        
        System.out.println(gamingPC);
        
        Computer officePC = new Computer.Builder()
            .setCpu("Intel i5")
            .setRam("16GB")
            .setStorage("512GB SSD")
            .setHasWifi(true)
            .build();
        
        System.out.println(officePC);
    }
}
```

## Structural Patterns

### Adapter Pattern
```java
// Target interface
interface MediaPlayer {
    void play(String audioType, String fileName);
}

// Adaptee classes
class Mp3Player implements MediaPlayer {
    @Override
    public void play(String audioType, String fileName) {
        if (audioType.equalsIgnoreCase("mp3")) {
            System.out.println("Playing mp3 file: " + fileName);
        } else {
            System.out.println("Invalid media. " + audioType + " format not supported");
        }
    }
}

class Mp4Player {
    public void playMp4(String fileName) {
        System.out.println("Playing mp4 file: " + fileName);
    }
}

class VlcPlayer {
    public void playVlc(String fileName) {
        System.out.println("Playing vlc file: " + fileName);
    }
}

// Adapter classes
class Mp4Adapter implements MediaPlayer {
    private Mp4Player mp4Player;
    
    public Mp4Adapter(Mp4Player mp4Player) {
        this.mp4Player = mp4Player;
    }
    
    @Override
    public void play(String audioType, String fileName) {
        if (audioType.equalsIgnoreCase("mp4")) {
            mp4Player.playMp4(fileName);
        } else {
            System.out.println("Invalid media. " + audioType + " format not supported");
        }
    }
}

class VlcAdapter implements MediaPlayer {
    private VlcPlayer vlcPlayer;
    
    public VlcAdapter(VlcPlayer vlcPlayer) {
        this.vlcPlayer = vlcPlayer;
    }
    
    @Override
    public void play(String audioType, String fileName) {
        if (audioType.equalsIgnoreCase("vlc")) {
            vlcPlayer.playVlc(fileName);
        } else {
            System.out.println("Invalid media. " + audioType + " format not supported");
        }
    }
}

// Usage
public class AdapterDemo {
    public static void main(String[] args) {
        MediaPlayer mp3Player = new Mp3Player();
        MediaPlayer mp4Player = new Mp4Adapter(new Mp4Player());
        MediaPlayer vlcPlayer = new VlcAdapter(new VlcPlayer());
        
        mp3Player.play("mp3", "song.mp3");
        mp4Player.play("mp4", "video.mp4");
        vlcPlayer.play("vlc", "movie.vlc");
    }
}
```

### Decorator Pattern
```java
// Component interface
interface Coffee {
    String getDescription();
    double getCost();
}

// Concrete component
class SimpleCoffee implements Coffee {
    @Override
    public String getDescription() {
        return "Simple Coffee";
    }
    
    @Override
    public double getCost() {
        return 2.0;
    }
}

// Decorator abstract class
abstract class CoffeeDecorator implements Coffee {
    protected Coffee coffee;
    
    public CoffeeDecorator(Coffee coffee) {
        this.coffee = coffee;
    }
    
    @Override
    public String getDescription() {
        return coffee.getDescription();
    }
    
    @Override
    public double getCost() {
        return coffee.getCost();
    }
}

// Concrete decorators
class MilkDecorator extends CoffeeDecorator {
    public MilkDecorator(Coffee coffee) {
        super(coffee);
    }
    
    @Override
    public String getDescription() {
        return coffee.getDescription() + ", Milk";
    }
    
    @Override
    public double getCost() {
        return coffee.getCost() + 0.5;
    }
}

class SugarDecorator extends CoffeeDecorator {
    public SugarDecorator(Coffee coffee) {
        super(coffee);
    }
    
    @Override
    public String getDescription() {
        return coffee.getDescription() + ", Sugar";
    }
    
    @Override
    public double getCost() {
        return coffee.getCost() + 0.2;
    }
}

class WhippedCreamDecorator extends CoffeeDecorator {
    public WhippedCreamDecorator(Coffee coffee) {
        super(coffee);
    }
    
    @Override
    public String getDescription() {
        return coffee.getDescription() + ", Whipped Cream";
    }
    
    @Override
    public double getCost() {
        return coffee.getCost() + 0.7;
    }
}

// Usage
public class DecoratorDemo {
    public static void main(String[] args) {
        Coffee coffee = new SimpleCoffee();
        System.out.println(coffee.getDescription() + " $" + coffee.getCost());
        
        coffee = new MilkDecorator(coffee);
        System.out.println(coffee.getDescription() + " $" + coffee.getCost());
        
        coffee = new SugarDecorator(coffee);
        System.out.println(coffee.getDescription() + " $" + coffee.getCost());
        
        coffee = new WhippedCreamDecorator(coffee);
        System.out.println(coffee.getDescription() + " $" + coffee.getCost());
    }
}
```

## Behavioral Patterns

### Observer Pattern
```java
import java.util.*;

// Subject interface
interface Subject {
    void registerObserver(Observer observer);
    void removeObserver(Observer observer);
    void notifyObservers();
}

// Observer interface
interface Observer {
    void update(String message);
}

// Concrete subject
class WeatherStation implements Subject {
    private List<Observer> observers = new ArrayList<>();
    private String weather;
    
    public void setWeather(String weather) {
        this.weather = weather;
        System.out.println("Weather updated to: " + weather);
        notifyObservers();
    }
    
    @Override
    public void registerObserver(Observer observer) {
        observers.add(observer);
    }
    
    @Override
    public void removeObserver(Observer observer) {
        observers.remove(observer);
    }
    
    @Override
    public void notifyObservers() {
        for (Observer observer : observers) {
            observer.update(weather);
        }
    }
}

// Concrete observers
class PhoneDisplay implements Observer {
    @Override
    public void update(String message) {
        System.out.println("Phone Display: Weather updated - " + message);
    }
}

class TvDisplay implements Observer {
    @Override
    public void update(String message) {
        System.out.println("TV Display: Weather updated - " + message);
    }
}

class WebDisplay implements Observer {
    @Override
    public void update(String message) {
        System.out.println("Web Display: Weather updated - " + message);
    }
}

// Usage
public class ObserverDemo {
    public static void main(String[] args) {
        WeatherStation weatherStation = new WeatherStation();
        
        Observer phoneDisplay = new PhoneDisplay();
        Observer tvDisplay = new TvDisplay();
        Observer webDisplay = new WebDisplay();
        
        weatherStation.registerObserver(phoneDisplay);
        weatherStation.registerObserver(tvDisplay);
        weatherStation.registerObserver(webDisplay);
        
        weatherStation.setWeather("Sunny");
        weatherStation.setWeather("Rainy");
        
        weatherStation.removeObserver(tvDisplay);
        weatherStation.setWeather("Cloudy");
    }
}
```

### Strategy Pattern
```java
// Strategy interface
interface PaymentStrategy {
    void pay(double amount);
}

// Concrete strategies
class CreditCardPayment implements PaymentStrategy {
    private String cardNumber;
    private String name;
    
    public CreditCardPayment(String cardNumber, String name) {
        this.cardNumber = cardNumber;
        this.name = name;
    }
    
    @Override
    public void pay(double amount) {
        System.out.println("Paid $" + amount + " using credit card ending in " + 
            cardNumber.substring(cardNumber.length() - 4) + " (Holder: " + name + ")");
    }
}

class PayPalPayment implements PaymentStrategy {
    private String email;
    
    public PayPalPayment(String email) {
        this.email = email;
    }
    
    @Override
    public void pay(double amount) {
        System.out.println("Paid $" + amount + " using PayPal (Email: " + email + ")");
    }
}

class BitcoinPayment implements PaymentStrategy {
    private String walletAddress;
    
    public BitcoinPayment(String walletAddress) {
        this.walletAddress = walletAddress;
    }
    
    @Override
    public void pay(double amount) {
        System.out.println("Paid $" + amount + " using Bitcoin (Wallet: " + walletAddress + ")");
    }
}

// Context class
class ShoppingCart {
    private PaymentStrategy paymentStrategy;
    
    public void setPaymentStrategy(PaymentStrategy paymentStrategy) {
        this.paymentStrategy = paymentStrategy;
    }
    
    public void checkout(double amount) {
        if (paymentStrategy == null) {
            System.out.println("Please select a payment method");
            return;
        }
        
        System.out.println("Checking out...");
        paymentStrategy.pay(amount);
    }
}

// Usage
public class StrategyDemo {
    public static void main(String[] args) {
        ShoppingCart cart = new ShoppingCart();
        
        cart.setPaymentStrategy(new CreditCardPayment("1234567890123456", "John Doe"));
        cart.checkout(100.50);
        
        cart.setPaymentStrategy(new PayPalPayment("john@example.com"));
        cart.checkout(75.25);
        
        cart.setPaymentStrategy(new BitcoinPayment("1A2B3C4D5E6F7G8H9I0J"));
        cart.checkout(50.75);
    }
}
```

## Examples

### Complete Pattern Implementation
```java
import java.util.*;

// Command Pattern
interface Command {
    void execute();
    void undo();
}

class Light {
    public void turnOn() {
        System.out.println("Light is ON");
    }
    
    public void turnOff() {
        System.out.println("Light is OFF");
    }
}

class LightOnCommand implements Command {
    private Light light;
    
    public LightOnCommand(Light light) {
        this.light = light;
    }
    
    @Override
    public void execute() {
        light.turnOn();
    }
    
    @Override
    public void undo() {
        light.turnOff();
    }
}

class LightOffCommand implements Command {
    private Light light;
    
    public LightOffCommand(Light light) {
        this.light = light;
    }
    
    @Override
    public void execute() {
        light.turnOff();
    }
    
    @Override
    public void undo() {
        light.turnOn();
    }
}

class RemoteControl {
    private Command command;
    private Stack<Command> history = new Stack<>();
    
    public void setCommand(Command command) {
        this.command = command;
    }
    
    public void pressButton() {
        if (command != null) {
            command.execute();
            history.push(command);
        }
    }
    
    public void pressUndo() {
        if (!history.isEmpty()) {
            Command lastCommand = history.pop();
            lastCommand.undo();
        }
    }
}

// Template Method Pattern
abstract class DataProcessor {
    public final void processData() {
        loadData();
        validateData();
        transformData();
        saveData();
        cleanup();
    }
    
    protected abstract void loadData();
    protected abstract void validateData();
    protected abstract void transformData();
    protected abstract void saveData();
    
    protected void cleanup() {
        System.out.println("Cleaning up resources");
    }
}

class CSVDataProcessor extends DataProcessor {
    @Override
    protected void loadData() {
        System.out.println("Loading CSV data");
    }
    
    @Override
    protected void validateData() {
        System.out.println("Validating CSV data format");
    }
    
    @Override
    protected void transformData() {
        System.out.println("Transforming CSV data");
    }
    
    @Override
    protected void saveData() {
        System.out.println("Saving processed CSV data");
    }
}

class XMLDataProcessor extends DataProcessor {
    @Override
    protected void loadData() {
        System.out.println("Loading XML data");
    }
    
    @Override
    protected void validateData() {
        System.out.println("Validating XML schema");
    }
    
    @Override
    protected void transformData() {
        System.out.println("Transforming XML data");
    }
    
    @Override
    protected void saveData() {
        System.out.println("Saving processed XML data");
    }
}

// Main demonstration
public class CompletePatternDemo {
    public static void main(String[] args) {
        System.out.println("=== Command Pattern Demo ===");
        Light livingRoomLight = new Light();
        RemoteControl remote = new RemoteControl();
        
        remote.setCommand(new LightOnCommand(livingRoomLight));
        remote.pressButton();
        
        remote.setCommand(new LightOffCommand(livingRoomLight));
        remote.pressButton();
        
        remote.pressUndo(); // Undo last command
        
        System.out.println("\n=== Template Method Pattern Demo ===");
        DataProcessor csvProcessor = new CSVDataProcessor();
        DataProcessor xmlProcessor = new XMLDataProcessor();
        
        System.out.println("Processing CSV:");
        csvProcessor.processData();
        
        System.out.println("\nProcessing XML:");
        xmlProcessor.processData();
    }
}
```

## Best Practices
- Choose the right pattern for the right problem
- Don't over-engineer simple problems with patterns
- Understand when to use creational, structural, or behavioral patterns
- Consider performance implications of patterns
- Use patterns to improve code maintainability
- Document pattern usage in code comments
- Learn the GoF (Gang of Four) patterns thoroughly
- Consider modern alternatives to traditional patterns
- Use patterns consistently across the codebase
- Refactor to patterns when code becomes complex
