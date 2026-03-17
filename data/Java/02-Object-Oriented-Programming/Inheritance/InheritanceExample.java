public class InheritanceExample {
    public static void main(String[] args) {
        // Single Inheritance
        System.out.println("=== Single Inheritance ===");
        Dog dog = new Dog("Buddy", 3, "Golden Retriever");
        dog.makeSound();
        dog.eat();
        dog.displayInfo();
        dog.wagTail();
        
        // Multilevel Inheritance
        System.out.println("\n=== Multilevel Inheritance ===");
        Puppy puppy = new Puppy("Max", 1, "Labrador", "Chocolate");
        puppy.makeSound();
        puppy.eat();
        puppy.displayInfo();
        puppy.wagTail();
        puppy.play();
        
        // Hierarchical Inheritance
        System.out.println("\n=== Hierarchical Inheritance ===");
        Cat cat = new Cat("Whiskers", 2, "Persian");
        cat.makeSound();
        cat.eat();
        cat.displayInfo();
        cat.climb();
        
        // Upcasting and Downcasting
        System.out.println("\n=== Upcasting and Downcasting ===");
        Animal animal = dog; // Upcasting (implicit)
        animal.makeSound(); // Calls Dog's makeSound()
        // animal.wagTail(); // Error: Animal doesn't have wagTail()
        
        Dog dogRef = (Dog) animal; // Downcasting (explicit)
        dogRef.wagTail(); // Now we can access Dog methods
        
        // instanceof operator
        System.out.println("\n=== instanceof Operator ===");
        System.out.println("dog instanceof Dog: " + (dog instanceof Dog));
        System.out.println("dog instanceof Animal: " + (dog instanceof Animal));
        System.out.println("animal instanceof Dog: " + (animal instanceof Dog));
        System.out.println("cat instanceof Animal: " + (cat instanceof Animal));
        System.out.println("cat instanceof Cat: " + (cat instanceof Cat));
        
        // Method Overriding
        System.out.println("\n=== Method Overriding ===");
        Animal[] animals = {dog, cat, puppy};
        for (Animal a : animals) {
            a.makeSound(); // Polymorphic behavior
            a.sleep();
        }
        
        // Super keyword usage
        System.out.println("\n=== Super Keyword ===");
        puppy.displayInfo(); // Calls overridden method that uses super
        
        // Constructor chaining
        System.out.println("\n=== Constructor Chaining ===");
        Bird bird = new Bird("Tweety", 1);
        bird.displayInfo();
        
        // Final class and methods
        System.out.println("\n=== Final Class and Methods ===");
        FinalClass finalObj = new FinalClass();
        finalObj.finalMethod();
        // Cannot extend FinalClass or override finalMethod
        
        // Object class methods in inheritance
        System.out.println("\n=== Object Methods in Inheritance ===");
        Dog dog2 = new Dog("Buddy", 3, "Golden Retriever");
        System.out.println("dog.equals(dog2): " + dog.equals(dog2));
        System.out.println("dog.hashCode(): " + dog.hashCode());
        System.out.println("dog.toString(): " + dog.toString());
    }
}

// Base class (Superclass)
class Animal {
    protected String name;
    protected int age;
    
    public Animal() {
        System.out.println("Animal default constructor");
    }
    
    public Animal(String name, int age) {
        this.name = name;
        this.age = age;
        System.out.println("Animal parameterized constructor");
    }
    
    public void eat() {
        System.out.println(name + " is eating");
    }
    
    public void sleep() {
        System.out.println(name + " is sleeping");
    }
    
    public void makeSound() {
        System.out.println(name + " makes a generic animal sound");
    }
    
    public void displayInfo() {
        System.out.println("Animal: " + name + ", Age: " + age);
    }
    
    // Overridden Object methods
    @Override
    public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || getClass() != obj.getClass()) return false;
        Animal animal = (Animal) obj;
        return age == animal.age && name.equals(animal.name);
    }
    
    @Override
    public int hashCode() {
        return name.hashCode() + age;
    }
    
    @Override
    public String toString() {
        return "Animal{name='" + name + "', age=" + age + "}";
    }
}

// Derived class (Subclass) - Single Inheritance
class Dog extends Animal {
    private String breed;
    
    public Dog() {
        super(); // Calls Animal default constructor
        System.out.println("Dog default constructor");
    }
    
    public Dog(String name, int age, String breed) {
        super(name, age); // Calls Animal parameterized constructor
        this.breed = breed;
        System.out.println("Dog parameterized constructor");
    }
    
    // Method overriding
    @Override
    public void makeSound() {
        System.out.println(name + " barks: Woof! Woof!");
    }
    
    @Override
    public void displayInfo() {
        super.displayInfo(); // Calls parent's displayInfo()
        System.out.println("Breed: " + breed);
    }
    
    // New method specific to Dog
    public void wagTail() {
        System.out.println(name + " is wagging tail happily");
    }
    
    // Getter for breed
    public String getBreed() {
        return breed;
    }
    
    // Overriding equals for Dog-specific comparison
    @Override
    public boolean equals(Object obj) {
        if (!super.equals(obj)) return false;
        if (obj instanceof Dog) {
            Dog dog = (Dog) obj;
            return breed.equals(dog.breed);
        }
        return false;
    }
    
    @Override
    public int hashCode() {
        return super.hashCode() + breed.hashCode();
    }
}

// Multilevel Inheritance
class Puppy extends Dog {
    private String color;
    
    public Puppy(String name, int age, String breed, String color) {
        super(name, age, breed); // Calls Dog constructor
        this.color = color;
        System.out.println("Puppy constructor");
    }
    
    @Override
    public void makeSound() {
        System.out.println(name + " yips: Yip! Yip!");
    }
    
    public void play() {
        System.out.println(name + " is playing with a ball");
    }
    
    @Override
    public void displayInfo() {
        super.displayInfo();
        System.out.println("Color: " + color);
    }
}

// Hierarchical Inheritance
class Cat extends Animal {
    private String breed;
    
    public Cat(String name, int age, String breed) {
        super(name, age);
        this.breed = breed;
    }
    
    @Override
    public void makeSound() {
        System.out.println(name + " meows: Meow! Meow!");
    }
    
    public void climb() {
        System.out.println(name + " is climbing a tree");
    }
    
    @Override
    public void displayInfo() {
        super.displayInfo();
        System.out.println("Breed: " + breed);
    }
}

// Demonstrating constructor chaining
class Bird extends Animal {
    private boolean canFly;
    
    public Bird() {
        this("Unknown", 0, true);
    }
    
    public Bird(String name, int age) {
        this(name, age, true);
    }
    
    public Bird(String name, int age, boolean canFly) {
        super(name, age);
        this.canFly = canFly;
        System.out.println("Bird constructor");
    }
    
    @Override
    public void makeSound() {
        System.out.println(name + " chirps: Tweet! Tweet!");
    }
    
    @Override
    public void displayInfo() {
        super.displayInfo();
        System.out.println("Can fly: " + canFly);
    }
}

// Final class - cannot be extended
final class FinalClass {
    public final void finalMethod() {
        System.out.println("This method cannot be overridden");
    }
}

// This would cause compilation error:
// class ExtendedFinal extends FinalClass { } // Error: Cannot inherit from final class
