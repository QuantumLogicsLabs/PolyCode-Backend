# Methods Example

## File Overview
This file demonstrates various types of methods in Java including parameters, return types, overloading, recursion, and variable arguments.

## Key Concepts Explained

### 1. Method Types

#### Instance Methods
- Belong to objects of the class
- Can access instance variables and methods
- Require object creation to call
- Example: `obj.simpleMethod()`

#### Static Methods
- Belong to the class itself
- Can only access static variables and methods
- Can be called using class name
- Example: `MethodsExample.staticMethod()`

### 2. Method Components

#### Return Type
- **void**: No return value
- **Primitive types**: int, double, boolean, etc.
- **Reference types**: String, arrays, objects
- Must match the type of returned value

#### Parameters
- Input values passed to methods
- Can be primitive or reference types
- Multiple parameters separated by commas
- Can have zero parameters

#### Method Signature
- Method name + parameter types
- Used for method overloading
- Return type is not part of signature

### 3. Method Features

#### Method Overloading
- Same method name, different parameters
- Different number or types of parameters
- Return type can be different
- Compile-time polymorphism

#### Variable Arguments (Varargs)
- Accept variable number of arguments
- Syntax: `type... parameterName`
- Treated as array inside method
- Must be last parameter

#### Recursion
- Method calling itself
- Must have base case to terminate
- Useful for problems like factorial, fibonacci
- Can lead to stack overflow if not careful

### 4. Access Modifiers
- **public**: Accessible from anywhere
- **private**: Accessible only within class
- **protected**: Accessible within package and subclasses
- **default**: Accessible within package only

### 5. Special Methods

#### Constructor
- Same name as class
- No return type
- Initializes objects
- Can be overloaded

#### Main Method
- `public static void main(String[] args)`
- Entry point of program
- Required for executable Java programs

## Best Practices
1. Use descriptive method names (verbs)
2. Keep methods small and focused
3. Use appropriate return types
4. Document method parameters and return values
5. Avoid too many parameters (consider objects)
6. Use overloading for related functionality
7. Be careful with recursion depth
8. Use varargs when number of arguments varies

## Method Naming Conventions
- Use camelCase (e.g., `calculateArea`)
- Start with verb (e.g., `get`, `set`, `is`, `calculate`)
- Boolean methods often start with `is` or `has`
- Accessor methods: `getPropertyName()`
- Mutator methods: `setPropertyName()`

## Common Pitfalls
1. Forgetting `return` statement in non-void methods
2. Wrong parameter types or order
3. Infinite recursion
4. Confusing method overloading with overriding
5. Not handling varargs properly
6. Static methods accessing instance members
