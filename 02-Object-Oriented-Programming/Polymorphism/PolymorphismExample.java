public class PolymorphismExample {
    public static void main(String[] args) {
        // Compile-time Polymorphism (Method Overloading)
        System.out.println("=== Compile-time Polymorphism (Method Overloading) ===");
        Calculator calc = new Calculator();
        System.out.println("add(2, 3): " + calc.add(2, 3));
        System.out.println("add(2.5, 3.5): " + calc.add(2.5, 3.5));
        System.out.println("add(2, 3, 4): " + calc.add(2, 3, 4));
        System.out.println("add(\"Hello\", \" World\"): " + calc.add("Hello", " World"));
        
        // Runtime Polymorphism (Method Overriding)
        System.out.println("\n=== Runtime Polymorphism (Method Overriding) ===");
        Animal[] animals = {
            new Dog("Buddy"),
            new Cat("Whiskers"),
            new Bird("Tweety")
        };
        
        for (Animal animal : animals) {
            animal.makeSound(); // Polymorphic method call
            animal.move();
            System.out.println();
        }
        
        // Dynamic Method Dispatch
        System.out.println("=== Dynamic Method Dispatch ===");
        Animal myAnimal = new Dog("Max"); // Superclass reference to subclass object
        myAnimal.makeSound(); // Calls Dog's makeSound() at runtime
        
        myAnimal = new Cat("Mittens");
        myAnimal.makeSound(); // Calls Cat's makeSound() at runtime
        
        // Polymorphic Parameters
        System.out.println("\n=== Polymorphic Parameters ===");
        AnimalTrainer trainer = new AnimalTrainer();
        trainer.train(new Dog("Rex"));
        trainer.train(new Cat("Fluffy"));
        trainer.train(new Bird("Sunny"));
        
        // Polymorphic Return Types
        System.out.println("\n=== Polymorphic Return Types ===");
        AnimalFactory factory = new AnimalFactory();
        Animal animal1 = factory.createAnimal("dog");
        Animal animal2 = factory.createAnimal("cat");
        Animal animal3 = factory.createAnimal("bird");
        
        animal1.makeSound();
        animal2.makeSound();
        animal3.makeSound();
        
        // Covariant Return Types
        System.out.println("\n=== Covariant Return Types ===");
        AnimalShelter shelter = new AnimalShelter();
        Dog adoptedDog = shelter.adoptDog();
        Cat adoptedCat = shelter.adoptCat();
        
        adoptedDog.makeSound();
        adoptedCat.makeSound();
        
        // Polymorphism with Interfaces
        System.out.println("\n=== Polymorphism with Interfaces ===");
        Drawable[] drawables = {
            new Circle(5),
            new Rectangle(4, 6),
            new Triangle(3, 4, 5)
        };
        
        for (Drawable drawable : drawables) {
            drawable.draw();
            System.out.println("Area: " + drawable.calculateArea());
        }
        
        // Downcasting with instanceof
        System.out.println("\n=== Safe Downcasting ===");
        for (Animal animal : animals) {
            if (animal instanceof Dog) {
                Dog dog = (Dog) animal;
                dog.fetch();
            } else if (animal instanceof Cat) {
                Cat cat = (Cat) animal;
                cat.climb();
            } else if (animal instanceof Bird) {
                Bird bird = (Bird) animal;
                bird.fly();
            }
        }
        
        // Abstract Class Polymorphism
        System.out.println("\n=== Abstract Class Polymorphism ===");
        Vehicle[] vehicles = {
            new Car("Toyota"),
            new Motorcycle("Harley"),
            new Truck("Ford")
        };
        
        for (Vehicle vehicle : vehicles) {
            vehicle.start();
            vehicle.accelerate();
            vehicle.brake();
            System.out.println("Max speed: " + vehicle.getMaxSpeed());
            System.out.println();
        }
    }
}

// Compile-time Polymorphism Example
class Calculator {
    // Method overloading - same name, different parameters
    public int add(int a, int b) {
        System.out.println("Adding two integers");
        return a + b;
    }
    
    public double add(double a, double b) {
        System.out.println("Adding two doubles");
        return a + b;
    }
    
    public int add(int a, int b, int c) {
        System.out.println("Adding three integers");
        return a + b + c;
    }
    
    public String add(String a, String b) {
        System.out.println("Concatenating strings");
        return a + b;
    }
}

// Runtime Polymorphism Example
abstract class Animal {
    protected String name;
    
    public Animal(String name) {
        this.name = name;
    }
    
    // Abstract method - must be overridden
    public abstract void makeSound();
    
    // Virtual method - can be overridden
    public void move() {
        System.out.println(name + " is moving");
    }
    
    public String getName() {
        return name;
    }
}

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
        System.out.println(name + " is running");
    }
    
    public void fetch() {
        System.out.println(name + " is fetching the ball");
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
        System.out.println(name + " is sneaking");
    }
    
    public void climb() {
        System.out.println(name + " is climbing");
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
        System.out.println(name + " is flying");
    }
    
    public void fly() {
        System.out.println(name + " is soaring in the sky");
    }
}

// Polymorphic Parameters
class AnimalTrainer {
    public void train(Animal animal) {
        System.out.println("Training " + animal.getName());
        animal.makeSound();
        animal.move();
        System.out.println(animal.getName() + " is trained!");
    }
}

// Polymorphic Return Types
class AnimalFactory {
    public Animal createAnimal(String type) {
        switch (type.toLowerCase()) {
            case "dog":
                return new Dog("Factory Dog");
            case "cat":
                return new Cat("Factory Cat");
            case "bird":
                return new Bird("Factory Bird");
            default:
                return new Dog("Default Dog");
        }
    }
}

// Covariant Return Types
class AnimalShelter {
    public Dog adoptDog() {
        return new Dog("Shelter Dog");
    }
    
    public Cat adoptCat() {
        return new Cat("Shelter Cat");
    }
}

// Interface Polymorphism
interface Drawable {
    void draw();
    double calculateArea();
}

class Circle implements Drawable {
    private double radius;
    
    public Circle(double radius) {
        this.radius = radius;
    }
    
    @Override
    public void draw() {
        System.out.println("Drawing a circle with radius " + radius);
    }
    
    @Override
    public double calculateArea() {
        return Math.PI * radius * radius;
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
        System.out.println("Drawing a rectangle " + width + "x" + height);
    }
    
    @Override
    public double calculateArea() {
        return width * height;
    }
}

class Triangle implements Drawable {
    private double a, b, c;
    
    public Triangle(double a, double b, double c) {
        this.a = a;
        this.b = b;
        this.c = c;
    }
    
    @Override
    public void draw() {
        System.out.println("Drawing a triangle with sides " + a + ", " + b + ", " + c);
    }
    
    @Override
    public double calculateArea() {
        double s = (a + b + c) / 2;
        return Math.sqrt(s * (s - a) * (s - b) * (s - c));
    }
}

// Abstract Class Polymorphism
abstract class Vehicle {
    protected String brand;
    
    public Vehicle(String brand) {
        this.brand = brand;
    }
    
    public abstract void start();
    public abstract void accelerate();
    public abstract void brake();
    public abstract int getMaxSpeed();
    
    public void displayBrand() {
        System.out.println("Brand: " + brand);
    }
}

class Car extends Vehicle {
    public Car(String brand) {
        super(brand);
    }
    
    @Override
    public void start() {
        System.out.println(brand + " car is starting with key ignition");
    }
    
    @Override
    public void accelerate() {
        System.out.println(brand + " car is accelerating smoothly");
    }
    
    @Override
    public void brake() {
        System.out.println(brand + " car is braking with ABS");
    }
    
    @Override
    public int getMaxSpeed() {
        return 200;
    }
}

class Motorcycle extends Vehicle {
    public Motorcycle(String brand) {
        super(brand);
    }
    
    @Override
    public void start() {
        System.out.println(brand + " motorcycle is starting with kick start");
    }
    
    @Override
    public void accelerate() {
        System.out.println(brand + " motorcycle is accelerating quickly");
    }
    
    @Override
    public void brake() {
        System.out.println(brand + " motorcycle is braking with disc brakes");
    }
    
    @Override
    public int getMaxSpeed() {
        return 180;
    }
}

class Truck extends Vehicle {
    public Truck(String brand) {
        super(brand);
    }
    
    @Override
    public void start() {
        System.out.println(brand + " truck is starting with diesel engine");
    }
    
    @Override
    public void accelerate() {
        System.out.println(brand + " truck is accelerating slowly");
    }
    
    @Override
    public void brake() {
        System.out.println(brand + " truck is braking with air brakes");
    }
    
    @Override
    public int getMaxSpeed() {
        return 120;
    }
}
