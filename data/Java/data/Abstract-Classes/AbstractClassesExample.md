# Abstract Classes Example

## File Overview
This file demonstrates abstract class concepts in Java including abstract methods, concrete methods, constructors, template methods, and inheritance patterns.

## Key Concepts Explained

### 1. Abstract Classes Fundamentals

#### Definition
- Classes that cannot be instantiated directly
- Designed to be subclassed
- Can contain both abstract and concrete methods
- Provide common implementation for subclasses

#### Key Characteristics
- **Cannot be instantiated**: `new Animal()` is illegal
- **Can have constructors**: Called by subclass constructors
- **Can have instance variables**: Shared state among subclasses
- **Can have abstract methods**: Must be implemented by subclasses
- **Can have concrete methods**: Inherited by subclasses

### 2. Abstract Methods

#### Declaration
```java
public abstract void makeSound();
public abstract String getHabitat();
```
- No method body (no curly braces)
- Must be implemented by concrete subclasses
- Cannot be private, static, or final
- Define contract for subclasses

#### Implementation
```java
@Override
public void makeSound() {
    System.out.println(name + " barks: Woof! Woof!");
}
```
- Must provide implementation
- Access modifier cannot be more restrictive
- Can throw fewer or same exceptions

### 3. Concrete Methods in Abstract Classes

#### Purpose
- Provide common functionality
- Reduce code duplication
- Define shared behavior
- Maintain consistency

#### Example
```java
public void eat() {
    System.out.println(name + " is eating");
}

public void sleep() {
    System.out.println(name + " is sleeping");
}
```

### 4. Constructors in Abstract Classes

#### Purpose
- Initialize inherited fields
- Enforce initialization requirements
- Provide common setup logic
- Cannot be called directly

#### Implementation
```java
public Animal(String name, String species) {
    this.name = name;
    this.species = species;
    animalCount++;
}
```

#### Constructor Chaining
```java
public Dog(String name, String breed) {
    super(name, "Dog");  // Calls abstract class constructor
    this.breed = breed;
}
```

### 5. Template Method Pattern

#### Concept
- Defines algorithm skeleton in abstract class
- Subclasses implement specific steps
- Controls algorithm flow
- Ensures consistent structure

#### Implementation
```java
public final void dailyRoutine() {
    wakeUp();          // Concrete method
    eat();            // Concrete method
    move();           // Abstract method
    makeSound();      // Abstract method
    sleep();          // Concrete method
}
```

#### Benefits
- Code reuse
- Consistent algorithm structure
- Controlled extension points
- Easy maintenance

### 6. Final Methods in Abstract Classes

#### Purpose
- Prevent overriding
- Maintain critical behavior
- Ensure consistent implementation
- Define invariants

#### Example
```java
public final void wakeUp() {
    System.out.println(name + " is waking up");
}
```

### 7. Static Members in Abstract Classes

#### Static Variables
- Shared among all instances
- Used for counting or configuration
- Accessed via class name

#### Static Methods
- Utility methods
- Factory methods
- Class-level operations

#### Example
```java
private static int animalCount = 0;

public static int getAnimalCount() {
    return animalCount;
}
```

### 8. Abstract Class vs Concrete Class

#### Abstract Class
- Cannot be instantiated
- Can have abstract methods
- Designed for inheritance
- Provides partial implementation

#### Concrete Class
- Can be instantiated
- All methods implemented
- Can be instantiated directly
- Complete implementation

### 9. Abstract Class vs Interface

#### Abstract Class
- Can have instance variables
- Can have constructors
- Can have concrete methods
- Single inheritance
- Used for "is-a" relationship

#### Interface
- Only constants (before Java 8)
- No constructors
- All methods abstract (before Java 8)
- Multiple inheritance
- Used for "can-do" relationship

### 10. Inheritance Patterns

#### Single Inheritance
- One abstract class extended by multiple concrete classes
- Common base functionality
- Polymorphic behavior

#### Multilevel Inheritance
- Abstract class extends another abstract class
- Layered abstraction
- Progressive specialization

#### Hierarchical Inheritance
- Multiple concrete classes extend same abstract class
- Shared interface
- Different implementations

### 11. Best Practices

#### Design Guidelines
- Use abstract classes for shared implementation
- Define clear contracts with abstract methods
- Provide useful concrete methods
- Use template method pattern for algorithms

#### Implementation Tips
- Use @Override annotation
- Implement all abstract methods
- Follow naming conventions
- Document abstract method contracts

#### Constructor Design
- Initialize all fields
- Call super constructor appropriately
- Don't call overridable methods
- Keep constructors simple

### 12. Common Pitfalls

#### Design Issues
- Too many abstract methods
- Unclear contracts
- Missing concrete implementations
- Overly complex hierarchies

#### Implementation Problems
- Forgetting to implement abstract methods
- Wrong method signatures
- Inappropriate access modifiers
- Constructor issues

#### Concept Confusion
- Confusing with interfaces
- Using concrete classes where abstraction needed
- Not leveraging template methods
- Ignoring inheritance benefits

### 13. Real-World Examples

#### Framework Base Classes
- HttpServlet in Servlet API
- AppCompatActivity in Android
- Component in UI frameworks

#### Domain Models
- Account in banking systems
- Product in e-commerce
- User in authentication systems

#### Utility Classes
- Abstract collections
- Abstract I/O classes
- Abstract network classes

### 14. Advanced Concepts

#### Generic Abstract Classes
```java
abstract class Repository<T> {
    public abstract T findById(int id);
    public abstract List<T> findAll();
    public abstract void save(T entity);
}
```

#### Abstract Collections
- AbstractList
- AbstractSet
- AbstractMap

#### Abstract Factories
- Create families of related objects
- Encapsulate object creation
- Provide common interface

### 15. Testing Abstract Classes

#### Unit Testing
- Test concrete methods directly
- Test abstract method contracts
- Use test subclasses
- Mock dependencies

#### Integration Testing
- Test inheritance hierarchies
- Verify polymorphic behavior
- Test template methods
- Validate constructor chains

#### Test Subclasses
```java
class TestAnimal extends Animal {
    public TestAnimal() {
        super("Test", "Test");
    }
    
    @Override
    public void makeSound() { }
    
    @Override
    public void move() { }
    
    @Override
    public String getHabitat() { return "Test"; }
}
```

## Benefits of Abstract Classes

#### Code Reuse
- Common implementation shared
- Reduced duplication
- Consistent behavior
- Maintenance efficiency

#### Design Flexibility
- Extensible architecture
- Pluggable components
- Runtime polymorphism
- Easy to add new types

#### Contract Enforcement
- Required method implementation
- Consistent interface
- Type safety
- Compiler guarantees

#### Template Patterns
- Algorithm reuse
- Controlled extension
- Consistent structure
- Easy maintenance

## When to Use Abstract Classes

#### Appropriate Cases
- Classes share common code
- Need to define common state
- Want to enforce method contracts
- Need template method pattern

#### Inappropriate Cases
- No shared implementation
- Need multiple inheritance
- Simple marker interfaces
- Performance-critical code

#### Alternative Approaches
- Interfaces for multiple inheritance
- Concrete classes for simple cases
- Composition over inheritance
- Strategy pattern for behavior

## Summary

Abstract classes provide:
- Partial implementation for reuse
- Contract definition through abstract methods
- Template method patterns
- Common state management
- Inheritance hierarchies

They bridge the gap between interfaces and concrete classes, providing both structure and flexibility in object-oriented design.
