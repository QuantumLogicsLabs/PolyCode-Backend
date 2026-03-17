# Polymorphism Example

## File Overview
This file demonstrates both compile-time and runtime polymorphism in Java, including method overloading, method overriding, dynamic method dispatch, and polymorphic parameters.

## Key Concepts Explained

### 1. Types of Polymorphism

#### Compile-time Polymorphism (Static Binding)
- **Method Overloading**: Same method name, different parameters
- Resolved at compile time
- Based on method signature
- Example: `Calculator.add()` with different parameter types

#### Runtime Polymorphism (Dynamic Binding)
- **Method Overriding**: Subclass provides its own implementation
- Resolved at runtime
- Based on actual object type
- Example: `Animal.makeSound()` called through different subclass objects

### 2. Method Overloading

#### Rules for Overloading
- Same method name
- Different parameter list (type, number, or order)
- Return type can be different
- Access modifier can be different

#### Overloading Examples
```java
public int add(int a, int b) { }
public double add(double a, double b) { }
public int add(int a, int b, int c) { }
public String add(String a, String b) { }
```

#### Type Promotion
- Automatic type conversion for overloading resolution
- byte → short → int → long → float → double
- char → int → long → float → double

### 3. Method Overriding

#### Rules for Overriding
- Same method name and parameters
- Same return type or covariant return type
- Access modifier cannot be more restrictive
- Cannot override static methods
- Cannot override final methods

#### @Override Annotation
- Ensures correct method overriding
- Compile-time verification
- Best practice for clarity

#### Super Keyword
```java
@Override
public void makeSound() {
    super.makeSound(); // Call parent method
    // Add subclass behavior
}
```

### 4. Dynamic Method Dispatch

#### Mechanism
- Method call resolved at runtime
- Based on actual object type
- Enables runtime polymorphism
- Foundation for flexible code

#### Example
```java
Animal myAnimal = new Dog("Buddy");
myAnimal.makeSound(); // Calls Dog's makeSound() at runtime
```

#### Benefits
- Code flexibility
- Extensibility
- Reduced coupling
- Easier maintenance

### 5. Polymorphic Parameters

#### Concept
- Methods can accept superclass references
- Any subclass object can be passed
- Enables generic processing
- Reduces code duplication

#### Example
```java
public void train(Animal animal) {
    animal.makeSound(); // Works for any Animal subclass
}
```

#### Usage Patterns
- Strategy pattern
- Template method pattern
- Visitor pattern
- Generic algorithms

### 6. Polymorphic Return Types

#### Concept
- Methods can return superclass references
- Actual return type can be any subclass
- Factory pattern implementation
- Flexible object creation

#### Example
```java
public Animal createAnimal(String type) {
    if (type.equals("dog")) return new Dog();
    if (type.equals("cat")) return new Cat();
    return new Animal();
}
```

### 7. Covariant Return Types

#### Concept
- Overriding method can return subclass type
- Introduced in Java 5
- Maintains type safety
- Reduces casting requirements

#### Example
```java
class AnimalShelter {
    public Dog adoptDog() { return new Dog(); }
    public Cat adoptCat() { return new Cat(); }
}
```

### 8. Interface Polymorphism

#### Multiple Inheritance
- Interfaces provide multiple inheritance of type
- Classes can implement multiple interfaces
- Enables flexible design
- Decouples implementation from interface

#### Example
```java
Drawable[] shapes = {new Circle(), new Rectangle()};
for (Drawable shape : shapes) {
    shape.draw(); // Polymorphic behavior
}
```

### 9. Abstract Class Polymorphism

#### Abstract Methods
- Must be overridden by concrete subclasses
- Define contract for subclasses
- Enable polymorphic behavior
- Provide partial implementation

#### Example
```java
abstract class Vehicle {
    public abstract void start();
    public abstract void accelerate();
}
```

### 10. Safe Downcasting

#### instanceof Operator
- Checks object type before casting
- Prevents ClassCastException
- Enables type-specific operations
- Runtime type checking

#### Example
```java
if (animal instanceof Dog) {
    Dog dog = (Dog) animal;
    dog.fetch(); // Dog-specific method
}
```

### 11. Polymorphism Benefits

#### Code Reusability
- Single method works for multiple types
- Reduced code duplication
- Easier maintenance
- Consistent behavior

#### Extensibility
- New types can be added easily
- Existing code works unchanged
- Open/Closed Principle
- Flexible architecture

#### Maintainability
- Clear separation of concerns
- Reduced coupling
- Easier testing
- Better organization

### 12. Polymorphism Patterns

#### Strategy Pattern
- Encapsulates algorithms
- Interchangeable strategies
- Runtime algorithm selection
- Flexible behavior

#### Template Method Pattern
- Defines algorithm skeleton
- Subclasses implement steps
- Code reuse with variation
- Controlled extension

#### Factory Pattern
- Creates objects without specifying exact class
- Centralized object creation
- Type-independent client code
- Easy to add new types

## Best Practices

#### Design Guidelines
- Program to interfaces, not implementations
- Use abstract classes for shared code
- Favor composition over inheritance
- Keep inheritance hierarchies shallow

#### Implementation Tips
- Use @Override annotation
- Document polymorphic behavior
- Consider performance implications
- Use instanceof for safe casting

#### Testing Considerations
- Test each polymorphic path
- Verify dynamic dispatch
- Test with all concrete types
- Consider edge cases

## Common Pitfalls

#### Method Resolution
- Wrong method selected for overloading
- Unexpected behavior with type promotion
- Confusion between overloading and overriding
- Incorrect method signatures

#### Type Issues
- ClassCastException from unsafe casting
- instanceof not used before casting
- Confusion about compile-time vs runtime types
- Wrong assumptions about object types

#### Design Problems
- Deep inheritance hierarchies
- Inappropriate use of inheritance
- Breaking polymorphic contracts
- Inconsistent method behavior

## Performance Considerations

#### Runtime Overhead
- Dynamic dispatch has small performance cost
- Method lookup at runtime
- JIT compiler optimization
- Usually negligible impact

#### Optimization
- Final methods can be inlined
- JIT optimizes common paths
- Profile before optimizing
- Consider critical paths

## When to Use Polymorphism

#### Appropriate Cases
- Multiple types with common behavior
- Need for extensibility
- Code reuse across types
- Algorithm variations

#### Inappropriate Cases
- Simple, fixed behavior
- Performance-critical code
- Very different behaviors
- No common interface
