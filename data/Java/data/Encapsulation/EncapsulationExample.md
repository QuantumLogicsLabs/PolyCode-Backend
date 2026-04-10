# Encapsulation Example

## File Overview
This file demonstrates encapsulation principles in Java including data hiding, controlled access, validation, immutable objects, and defensive copying.

## Key Concepts Explained

### 1. Encapsulation Fundamentals

#### Definition
- Bundling data (fields) and methods that operate on the data
- Hiding internal state and requiring all interaction through object's methods
- One of the four fundamental OOP principles

#### Core Components
- **Private fields**: Data hidden from external access
- **Public methods**: Controlled interface for data access
- **Validation**: Rules enforced at the interface level

### 2. Access Modifiers in Encapsulation

#### Private Access
```java
private String accountNumber;
private double balance;
```
- Most restrictive access level
- Accessible only within the same class
- Essential for data hiding

#### Public Access
```java
public double getBalance() { return balance; }
public boolean deposit(double amount) { }
```
- Accessible from anywhere
- Defines the public interface
- Controlled access to private data

#### Protected Access
- Accessible within package and subclasses
- Used in inheritance scenarios
- Limited external access

### 3. Data Hiding

#### Private Fields
- Internal state not exposed directly
- Prevents unauthorized modification
- Enables validation and business rules

#### Benefits
- **Security**: Sensitive data protected
- **Flexibility**: Implementation can change
- **Maintainability**: Clear separation of concerns

### 4. Controlled Access

#### Getters (Accessors)
```java
public double getBalance() {
    return balance;
}
```
- Provide read access to private fields
- Can include additional logic
- Maintain data integrity

#### Setters (Mutators)
```java
public void setAge(int age) {
    if (age >= 18 && age <= 65) {
        this.age = age;
    }
}
```
- Provide write access with validation
- Enforce business rules
- Prevent invalid state

### 5. Validation in Encapsulation

#### Input Validation
```java
public boolean deposit(double amount) {
    if (amount > 0) {
        balance += amount;
        return true;
    }
    return false;
}
```
- Validate input before modification
- Reject invalid operations
- Maintain object invariants

#### Business Logic Enforcement
- Age restrictions
- Salary limits
- Email format validation
- Account balance rules

### 6. Immutable Objects

#### Characteristics
```java
final class ImmutablePerson {
    private final String name;
    private final int age;
    
    public ImmutablePerson withAge(int newAge) {
        return new ImmutablePerson(this.name, newAge, this.email);
    }
}
```
- All fields are final
- No setter methods
- Thread-safe by design
- New instances for modifications

#### Benefits
- **Thread Safety**: No synchronization needed
- **Predictability**: State never changes
- **Security**: Cannot be modified
- **Caching**: Safe to share references

### 7. Defensive Copying

#### Collections Protection
```java
public List<String> getStudents() {
    return new ArrayList<>(students); // Defensive copy
}
```
- Returns copy instead of reference
- Prevents external modification
- Maintains encapsulation

#### Immutable Wrappers
- `Collections.unmodifiableList()`
- `Collections.unmodifiableMap()`
- Read-only views of collections

### 8. Encapsulation Patterns

#### Bean Pattern
- Private fields with public getters/setters
- Standard Java convention
- Used by frameworks (Spring, JPA)

#### Builder Pattern
- Complex object construction
- Immutable objects
- Fluent interface

#### Value Object Pattern
- Immutable equality objects
- No identity, only values
- Replaceable with equal instances

### 9. Advanced Encapsulation

#### Package-Private Classes
- Hidden implementation details
- Exposed only through public interfaces
- Better modularity

#### Factory Methods
- Controlled object creation
- Encapsulated construction logic
- Validation during creation

#### Static Factory Methods
- Alternative to constructors
- More descriptive names
- Can return existing instances

### 10. Encapsulation Benefits

#### Data Protection
- Prevents corruption
- Enforces invariants
- Controls modification

#### Implementation Flexibility
- Internal changes don't affect clients
- Performance optimizations possible
- Refactoring without breaking code

#### Maintainability
- Clear interfaces
- Localized changes
- Easier debugging

#### Security
- Access control
- Input validation
- Audit points

### 11. Best Practices

#### Field Access
- Make fields private
- Use final for immutable fields
- Group related fields

#### Method Design
- Minimal public interface
- Clear method names
- Consistent validation

#### Validation
- Validate all inputs
- Provide meaningful error messages
- Fail fast on invalid data

#### Documentation
- Document invariants
- Explain validation rules
- Usage examples

### 12. Common Pitfalls

#### Over-Exposure
- Too many public methods
- Direct field access
- Weak validation

#### Under-Exposure
- Insufficient interface
- Missing convenience methods
- Poor usability

#### Validation Issues
- Incomplete validation
- Wrong validation order
- Poor error handling

#### Performance Issues
- Excessive copying
- Unnecessary validation
- Poor caching

### 13. Encapsulation vs Information Hiding

#### Encapsulation
- Bundling data and methods
- Language feature
- Implementation technique

#### Information Hiding
- Design principle
- Abstraction concept
- Interface design

#### Relationship
- Encapsulation enables information hiding
- Information hiding guides encapsulation
- Both support modularity

### 14. Testing Encapsulated Code

#### Unit Testing
- Test public interface only
- Verify validation logic
- Test edge cases

#### Integration Testing
- Test interaction patterns
- Verify invariants
- Test error scenarios

#### Mock Objects
- Isolate dependencies
- Test validation separately
- Verify behavior

## Real-World Examples

#### Banking Systems
- Account balance protection
- Transaction validation
- Audit trails

#### User Management
- Password hashing
- Profile validation
- Access control

#### Configuration
- Immutable settings
- Validation at load time
- Type safety

## When to Use Encapsulation

#### Always Use
- Public classes
- Domain objects
- Configuration classes

#### Consider Alternatives
- Simple data transfer objects
- Performance-critical code
- Temporary objects

#### Avoid Over-Engineering
- Simple utility classes
- Private nested classes
- Single-use objects
