# Java Data Types

## Primitive Data Types

### Numeric Types
```java
// Integer types
byte smallNumber = 127;        // 8-bit, -128 to 127
short mediumNumber = 32767;     // 16-bit, -32768 to 32767
int largeNumber = 2147483647;    // 32-bit, -2^31 to 2^31-1
long hugeNumber = 9223372036854775807L; // 64-bit, -2^63 to 2^63-1

// Floating point types
float pi = 3.14f;              // 32-bit, ~6-7 decimal digits
double precise = 3.141592653589793; // 64-bit, ~15-16 decimal digits
```

### Other Primitive Types
```java
// Character type
char letter = 'A';              // 16-bit Unicode character

// Boolean type
boolean isActive = true;          // true or false
boolean isComplete = false;       // true or false
```

## Reference Data Types

### String
```java
// String creation
String name = "John Doe";
String greeting = "Hello, World!";
String empty = "";

// String methods
String upper = name.toUpperCase();
String lower = name.toLowerCase();
int length = name.length();
boolean contains = name.contains("John");
```

### Arrays
```java
// Array declaration and initialization
int[] numbers = new int[5];
numbers[0] = 1;
numbers[1] = 2;
numbers[2] = 3;
numbers[3] = 4;
numbers[4] = 5;

// Array literal
int[] primes = {2, 3, 5, 7, 11};

// Array operations
int length = numbers.length;
int first = numbers[0];
for (int num : numbers) {
    System.out.println(num);
}
```

### Wrapper Classes
```java
// Object wrappers for primitive types
Integer num = 100;
Double price = 99.99;
Boolean flag = true;
Character letter = 'A';

// Autoboxing and unboxing
int primitive = num.intValue();        // Integer to int
Integer wrapper = Integer.valueOf(100); // int to Integer
```

## Type Conversion

### Implicit Conversion
```java
// Widening conversion (automatic)
int i = 100;
long l = i;        // int to long
double d = i;       // int to double
float f = i;        // int to float
```

### Explicit Conversion
```java
// Narrowing conversion (requires casting)
double d = 99.99;
int i = (int) d;    // double to int
float f = (float) d;  // double to float
long l = (long) i;    // int to long
```

### String Conversion
```java
// String to primitive types
String str = "123";
int num = Integer.parseInt(str);
double d = Double.parseDouble(str);

// Primitive to String
int num = 100;
String str = Integer.toString(num);
String str2 = String.valueOf(num);
```

## Type Comparison

### Primitive Comparison
```java
// Numeric comparison
int a = 10;
int b = 20;
boolean result = a < b; // true

// Character comparison
char c1 = 'A';
char c2 = 'B';
boolean same = c1 == c2; // false

// Boolean comparison
bool flag1 = true;
bool flag2 = false;
boolean result = flag1 && flag2; // false
```

### Reference Comparison
```java
// String comparison
String s1 = "hello";
String s2 = "hello";
String s3 = new String("hello");

boolean same1 = s1 == s2;        // true (same object)
boolean same2 = s1 == s3;        // false (different objects)
boolean equals = s1.equals(s3); // true (same content)
```

## Type Size and Range

### Primitive Type Sizes
```java
// Size and range information
System.out.println("Byte: " + Byte.SIZE + " bytes, " + Byte.MIN_VALUE + " to " + Byte.MAX_VALUE);
System.out.println("Short: " + Short.SIZE + " bytes, " + Short.MIN_VALUE + " to " + Short.MAX_VALUE);
System.out.println("Integer: " + Integer.SIZE + " bytes, " + Integer.MIN_VALUE + " to " + Integer.MAX_VALUE);
System.out.println("Long: " + Long.SIZE + " bytes, " + Long.MIN_VALUE + " to " + Long.MAX_VALUE);
System.out.println("Float: " + Float.SIZE + " bytes, " + Float.MIN_VALUE + " to " + Float.MAX_VALUE);
System.out.println("Double: " + Double.SIZE + " bytes, " + Double.MIN_VALUE + " to " + Double.MAX_VALUE);
```

### Overflow and Underflow
```java
// Integer overflow
int max = Integer.MAX_VALUE;
int overflow = max + 1; // Wraps to Integer.MIN_VALUE

// Floating point special values
double posInf = Double.POSITIVE_INFINITY;
double negInf = Double.NEGATIVE_INFINITY;
double nan = Double.NaN;

// Checking for special values
if (Double.isNaN(value)) {
    System.out.println("Value is NaN");
}
if (Double.isInfinite(value)) {
    System.out.println("Value is finite");
}
```

## Examples

### Data Type Examples
```java
public class DataTypeExamples {
    public static void main(String[] args) {
        // Primitive types
        byte age = 25;
        short score = 850;
        int population = 7500000;
        long worldPopulation = 7800000000L;
        float pi = 3.14f;
        double precisePi = 3.141592653589793;
        char grade = 'A';
        boolean isStudent = true;
        
        // Reference types
        String name = "John Doe";
        int[] scores = {85, 90, 78, 92, 88};
        Integer count = scores.length;
        
        // Display information
        System.out.println("Age: " + age);
        System.out.println("Score: " + score);
        System.out.println("Population: " + population);
        System.out.println("World Population: " + worldPopulation);
        System.out.println("PI (float): " + pi);
        System.out.println("PI (double): " + precisePi);
        System.out.println("Grade: " + grade);
        System.out.println("Is Student: " + isStudent);
        System.out.println("Name: " + name);
        System.out.println("Count: " + count);
        
        // Array operations
        System.out.println("Scores:");
        for (int score : scores) {
            System.out.println(score);
        }
    }
}
```

## Best Practices
- Use appropriate data types for the data
- Use int for integers unless you need larger range
- Use double for decimal numbers unless memory is critical
- Use String for text, not char arrays
- Use wrapper classes when null values are needed
- Be aware of overflow/underflow in numeric operations
- Use constants (final) for fixed values
- Choose descriptive names for variables
