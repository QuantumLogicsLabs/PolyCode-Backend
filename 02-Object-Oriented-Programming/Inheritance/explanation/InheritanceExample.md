# Inheritance Example

## File Overview
This file demonstrates inheritance concepts in Java including single, multilevel, and hierarchical inheritance, method overriding, constructor chaining, and related OOP principles.

## Key Concepts Explained

### 1. Inheritance Types

#### Single Inheritance
- One class extends another single class
- Java supports single inheritance for classes
- Example: `Dog extends Animal`

#### Multilevel Inheritance
- Chain of inheritance: A → B → C
- Grandparent → Parent → Child relationship
- Example: `Puppy extends Dog extends Animal`

#### Hierarchical Inheritance
- One parent class, multiple child classes
- Multiple classes extend the same base class
- Example: `Dog extends Animal`, `Cat extends Animal`

#### Multiple Inheritance (Not supported for classes)
- Java doesn't support multiple inheritance for classes
- Avoids "diamond problem"
- Achieved through interfaces

### 2. Inheritance Terminology

#### Superclass (Parent/Base Class)
- Class being inherited from
- Provides common attributes and behaviors
- More general in nature

#### Subclass (Child/Derived Class)
- Class that inherits from another
- Extends functionality of superclass
- More specific in nature

### 3. Method Overriding

#### Rules for Overriding
- Method signature must be identical
- Return type must be same or covariant
- Access modifier cannot be more restrictive
- Cannot override final methods

#### @Override Annotation
- Indicates method overrides superclass method
- Compiler verifies override correctness
- Best practice for clarity

#### Super Keyword Usage
```java
@Override
public void displayInfo() {
    super.displayInfo(); // Calls parent method
    // Add subclass-specific behavior
}
```

### 4. Constructor Inheritance

#### Constructor Chaining
- Subclass constructors call superclass constructors
- `super()` calls default constructor
- `super(params)` calls parameterized constructor

#### Default Constructor Behavior
- If no constructor defined, default provided
- Subclass automatically calls `super()`
- Must match superclass constructor signature

#### Explicit Constructor Calls
- Must be first statement in constructor
- Can only call one superclass constructor
- Cannot be called after other statements

### 5. Access Control in Inheritance

#### Protected Members
- Accessible within subclass
- Accessible within same package
- Not accessible outside package

#### Private Members
- Not inherited by subclasses
- Accessed through public/protected methods
- Maintains encapsulation

#### Public Members
- Fully inherited and accessible
- Can be accessed from anywhere
- Part of subclass public interface

### 6. Object Class Methods

#### equals()
- Override for content comparison
- Must be consistent with hashCode()
- Important for collections

#### hashCode()
- Override for hash-based collections
- Same objects must have same hash code
- Consistent with equals()

#### toString()
- Override for meaningful string representation
- Automatically called by println()
- Useful for debugging

### 7. Polymorphism in Inheritance

#### Upcasting
```java
Animal animal = new Dog(); // Implicit
```
- Subclass reference assigned to superclass type
- Can access only superclass methods
- Enables polymorphic behavior

#### Downcasting
```java
Dog dog = (Dog) animal; // Explicit
```
- Superclass reference cast to subclass type
- Requires runtime type checking
- Access to subclass methods

#### instanceof Operator
- Checks object type before casting
- Prevents ClassCastException
- Used for safe type operations

### 8. Final Keyword

#### Final Class
```java
final class FinalClass { }
```
- Cannot be extended
- Prevents inheritance
- Used for security and design

#### Final Method
```java
public final void finalMethod() { }
```
- Cannot be overridden
- Maintains behavior in inheritance
- Used for critical methods

#### Final Variable
- Cannot be reassigned
- Constants in inheritance hierarchy
- Shared behavior across classes

### 9. Inheritance Best Practices

#### Design Principles
- Use "is-a" relationship for inheritance
- Favor composition over inheritance
- Keep inheritance hierarchies shallow
- Use abstract classes for common behavior

#### Method Design
- Override toString(), equals(), hashCode()
- Use @Override annotation
- Document inheritance behavior
- Consider final for critical methods

#### Constructor Design
- Always call super constructor
- Initialize superclass state first
- Don't call overridable methods from constructors
- Keep constructors simple

## Common Pitfalls

#### Constructor Issues
- Forgetting to call super constructor
- Calling super after other statements
- Calling overridable methods in constructors

#### Method Overriding
- Wrong method signature
- More restrictive access modifier
- Not using @Override annotation
- Forgetting to call super.method()

#### Type Casting
- Incorrect downcasting without instanceof
- Assuming upcasting changes object type
- Not handling ClassCastException

#### Design Issues
- Deep inheritance hierarchies
- Inappropriate inheritance relationships
- Breaking encapsulation
- Overriding final methods

## When to Use Inheritance

#### Appropriate Cases
- Clear "is-a" relationship
- Code reuse with specialization
- Polymorphic behavior needed
- Shared interface with different implementations

#### Inappropriate Cases
- "has-a" relationship (use composition)
- Just for code reuse
- Deep inheritance chains
- Unrelated functionality

## Alternative: Composition
- "Has-a" relationship
- More flexible than inheritance
- Avoids inheritance limitations
- Easier to understand and maintain
