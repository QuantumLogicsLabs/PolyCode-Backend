# Data Types Example

## File Overview
This file demonstrates all primitive and reference data types available in Java.

## Key Concepts Explained

### 1. Primitive Data Types (8 types)

#### Numeric Types
- **byte**: 8-bit signed integer (-128 to 127)
- **short**: 16-bit signed integer (-32,768 to 32,767)
- **int**: 32-bit signed integer (-2,147,483,648 to 2,147,483,647)
- **long**: 64-bit signed integer (requires 'L' suffix)

#### Floating Point Types
- **float**: 32-bit floating point (requires 'f' suffix)
- **double**: 64-bit floating point (default for decimal numbers)

#### Other Types
- **char**: 16-bit Unicode character (0 to 65,535)
- **boolean**: Logical values (true or false)

### 2. Reference Data Types
- **String**: Sequence of characters
- **Arrays**: Collection of similar type elements
- **Objects**: Instances of classes
- **Interfaces**: Reference types for polymorphism

### 3. Type Casting

#### Automatic Casting (Widening)
- byte → short → int → long → float → double
- No data loss, automatic conversion

#### Explicit Casting (Narrowing)
- double → float → long → int → short → char → byte
- May cause data loss, requires explicit cast

## Memory Usage
- byte: 1 byte
- short: 2 bytes
- int: 4 bytes
- long: 8 bytes
- float: 4 bytes
- double: 8 bytes
- char: 2 bytes
- boolean: 1 bit (implementation dependent)

## Best Practices
1. Use appropriate data types to save memory
2. Use 'L' for long literals to avoid confusion
3. Use 'f' for float literals
4. Be careful with type casting to avoid data loss
5. Use wrapper classes when null values are needed
