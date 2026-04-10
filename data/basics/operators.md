<<<<<<< HEAD
# Operators in Java

- Arithmetic: +, -, *, /  
- Comparison: ==, !=  
- Logical: &&, ||  

## Example
```java
int a = 5;
int b = 3;
System.out.println(a + b);
Practice

Check if a number is even or odd.
=======
# Java Operators

## Arithmetic Operators

### Basic Arithmetic
```java
int a = 10, b = 3;

int sum = a + b;        // Addition: 13
int diff = a - b;       // Subtraction: 7
int product = a * b;    // Multiplication: 30
int quotient = a / b;    // Division: 3
int remainder = a % b;   // Modulus: 1
```

### Arithmetic with Different Types
```java
// Integer division
int result1 = 10 / 3;    // 3 (integer division)

// Floating point division
double result2 = 10.0 / 3.0; // 3.3333333333333335

// Mixed type arithmetic
double result3 = 10 / 3.0; // 3.3333333333333335
```

## Comparison Operators

### Equality Operators
```java
int x = 10, y = 10;
boolean isEqual = (x == y);  // true
boolean notEqual = (x != y); // false

// Object comparison
String s1 = "hello";
String s2 = "hello";
boolean sameContent = s1.equals(s2); // true
boolean sameObject = (s1 == s2); // true
```

### Relational Operators
```java
int a = 5, b = 10;

boolean lessThan = (a < b);      // true
boolean lessEqual = (a <= b);    // true
boolean greaterThan = (a > b);   // false
boolean greaterEqual = (a >= b); // false
```

## Logical Operators

### Boolean Logic
```java
boolean x = true, y = false;

boolean and = x && y;        // false (logical AND)
boolean or = x || y;         // true (logical OR)
boolean not = !x;            // false (logical NOT)

// Short-circuit evaluation
boolean result = (a > 0) && (b < 20); // Second part not evaluated if first is false
```

## Assignment Operators

### Simple Assignment
```java
int x = 10;
String name = "John";
boolean flag = true;
```

### Compound Assignment
```java
int a = 10;
a += 5;    // a = a + 5; // 15
a -= 3;    // a = a - 3; // 12
a *= 2;    // a = a * 2; // 24
a /= 3;    // a = a / 3; // 8
a %= 3;    // a = a % 3; // 2
```

## Increment/Decrement Operators

### Prefix and Postfix
```java
int a = 5;

int prefix = ++a;    // Increment then use: a = 6, prefix = 6
int postfix = a++;    // Use then increment: a = 6, postfix = 5

int b = 5;
int preDec = --b;      // Decrement then use: b = 4, preDec = 4
int postDec = b--;      // Use then decrement: b = 4, postDec = 5
```

## Bitwise Operators

### Bitwise Operations
```java
int a = 5;  // 0101 in binary
int b = 3;  // 0011 in binary

int and = a & b;     // 1 (bitwise AND)
int or = a | b;      // 7 (bitwise OR)
int xor = a ^ b;     // 6 (bitwise XOR)
int not = ~a;        // -6 (bitwise NOT)

int leftShift = a << 1;   // 10 (shift left)
int rightShift = a >> 1;  // 2 (shift right)
int unsignedShift = a >>> 1; // 2 (unsigned right shift)
```

## Conditional (Ternary) Operator

### Ternary Expression
```java
int age = 25;
String status = (age >= 18) ? "Adult" : "Minor";

// Nested ternary
String grade = (score >= 90) ? "A" : 
                (score >= 80) ? "B" : 
                (score >= 70) ? "C" : "F";
```

## Instanceof Operator

### Type Checking
```java
String text = "Hello";
Object obj = text;

boolean isString = obj instanceof String; // true
boolean isNumber = obj instanceof Number; // false

// With casting
if (obj instanceof String) {
    String str = (String) obj;
    System.out.println(str.toUpperCase());
}
```

## Operator Precedence

### Precedence Order
```java
// Highest precedence: postfix ++, --
// Next: prefix ++, --, ~, !
// Next: multiplication, division, modulus
// Next: addition, subtraction
// Next: shift <<, >>, >>>
// Next: relational <, >, <=, >=
// Next: equality ==, !=
// Next: bitwise AND &
// Next: bitwise XOR ^
// Next: bitwise OR |
// Next: logical AND &&
// Next: logical OR ||
// Lowest: assignment =, +=, -=, *=, /=, %=, etc.

// Use parentheses to override precedence
int result = (a + b) * c; // (a + b) * c
int result2 = a + (b * c); // a + (b * c)
```

## Examples

### Operator Examples
```java
public class OperatorExamples {
    public static void main(String[] args) {
        // Arithmetic operations
        int a = 10, b = 3;
        System.out.println("Arithmetic:");
        System.out.println(a + " + b + " = " + (a + b));
        System.out.println(a + " - " + b + " = " + (a - b));
        System.out.println(a + " * " + b + " = " + (a * b));
        System.out.println(a + " / " + b + " = " + (a / b));
        System.out.println(a + " % " + b + " = " + (a % b));
        
        // Comparison operations
        System.out.println("\nComparison:");
        System.out.println(a + " == " + b + " = " + (a == b));
        System.out.println(a + " != " + b + " = " + (a != b));
        System.out.println(a + " > " + b + " = " + (a > b));
        System.out.println(a + " < " + b + " = " + (a < b));
        
        // Logical operations
        boolean x = true, y = false;
        System.out.println("\nLogical:");
        System.out.println(x + " && " + y + " = " + (x && y));
        System.out.println(x + " || " + y + " = " + (x || y));
        System.out.println("!" + x + " = " + (!x));
        
        // Bitwise operations
        System.out.println("\nBitwise:");
        System.out.println("5 & 3 = " + (5 & 3));
        System.out.println("5 | 3 = " + (5 | 3));
        System.out.println("5 ^ 3 = " + (5 ^ 3));
        System.out.println("~5 = " + (~5));
        System.out.println("5 << 1 = " + (5 << 1));
        System.out.println("5 >> 1 = " + (5 >> 1));
        
        // Ternary operator
        int age = 25;
        String status = (age >= 18) ? "Adult" : "Minor";
        System.out.println("\nTernary:");
        System.out.println("Age " + age + " is " + status);
    }
}
```

## Best Practices
- Use parentheses to clarify complex expressions
- Be aware of integer division vs floating point division
- Use short-circuit logical operators for efficiency
- Use instanceof operator with casting
- Use ternary operator for simple conditional assignments
- Be careful with operator precedence in complex expressions
- Use meaningful variable names to improve readability
- Avoid complex nested operations without parentheses
>>>>>>> 8f5b7d4c3fd7b8f91af9afa9065e83663bf4a476
