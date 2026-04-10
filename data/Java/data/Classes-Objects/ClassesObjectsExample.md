# Classes and Objects Example

## File Overview
This file demonstrates fundamental Object-Oriented Programming concepts including classes, objects, constructors, methods, and object lifecycle in Java.

## Key Concepts Explained

### 1. Classes and Objects

#### Class Definition
- **Class**: Blueprint or template for creating objects
- Contains attributes (fields) and behaviors (methods)
- Defines the structure and behavior of objects

#### Object Creation
- **Object**: Instance of a class
- Created using `new` keyword
- Each object has its own state (instance variables)
- Shares behavior (methods) with other objects of same class

### 2. Constructors

#### Default Constructor
```java
public Student() {
    this.name = "Unknown";
    this.age = 0;
    this.major = "Undeclared";
}
```
- No parameters
- Initializes object with default values
- Automatically provided if no constructors defined

#### Parameterized Constructor
```java
public Student(String name, int age, String major) {
    this.name = name;
    this.age = age;
    this.major = major;
}
```
- Accepts parameters for initialization
- Allows object creation with specific values
- Overloaded based on parameters

#### Copy Constructor
```java
public Student(Student other) {
    this.name = other.name;
    this.age = other.age;
    this.major = other.major;
}
```
- Creates object from another object
- Useful for object duplication

### 3. Members of a Class

#### Instance Variables
- Belong to individual objects
- Each object has its own copy
- Represent object state

#### Static Variables (Class Variables)
- Shared among all objects of the class
- Belong to the class, not individual objects
- Used for common data

#### Instance Methods
- Operate on object state
- Can access instance and static members
- Called on object instances

#### Static Methods (Class Methods)
- Belong to the class
- Can only access static members
- Called using class name

### 4. Access Modifiers

#### Public
- Accessible from anywhere
- Used for methods that need to be called externally

#### Private
- Accessible only within the class
- Used for encapsulation and data hiding

#### Protected
- Accessible within package and subclasses
- Used for inheritance scenarios

#### Default (Package-private)
- Accessible within same package
- Used when no explicit modifier specified

### 5. Object Methods (from Object class)

#### toString()
- Returns string representation of object
- Automatically called by println()
- Should be overridden for meaningful output

#### equals()
- Compares objects for content equality
- Default implementation compares references
- Should be overridden for logical equality

#### hashCode()
- Returns integer hash code for object
- Used in hash-based collections
- Must be consistent with equals()

#### clone()
- Creates and returns copy of object
- Must implement Cloneable interface
- Creates shallow copy by default

#### finalize()
- Called by garbage collector before object destruction
- Used for cleanup operations
- Deprecated in favor of try-with-resources

### 6. Object Lifecycle

#### Creation
1. Memory allocation
2. Field initialization to default values
3. Constructor execution
4. Object reference assignment

#### Usage
- Methods called on object
- State modified through methods
- Object referenced by variables

#### Destruction
- Object becomes unreachable
- Garbage collector identifies eligible objects
- Memory reclaimed
- finalize() may be called

### 7. Object Comparison

#### Reference Equality (==)
- Compares memory addresses
- True only for same object instance
- Default behavior

#### Content Equality (equals())
- Compares object contents
- Must be overridden for custom logic
- Used for logical comparison

### 8. Static vs Instance Context

#### Static Context
- No access to instance members
- Can access only static members
- Used for utility methods

#### Instance Context
- Access to both static and instance members
- Operates on object state
- Most common method type

## Best Practices
1. Use private fields with public getters/setters (encapsulation)
2. Override toString(), equals(), hashCode() for meaningful behavior
3. Use static methods for utility functions
4. Implement Cloneable for object copying
5. Use meaningful constructor parameters
6. Follow naming conventions
7. Initialize all fields in constructors

## Common Pitfalls
1. Forgetting to initialize fields
2. Using == instead of equals() for content comparison
3. Not overriding equals() and hashCode() together
4. Accessing instance members from static context
5. Memory leaks from object references
6. Incorrect constructor chaining

## Design Considerations
- Keep classes focused and cohesive
- Minimize public exposure of internal state
- Use constructors for object initialization
- Consider immutable objects for thread safety
- Design for inheritance or declare final
