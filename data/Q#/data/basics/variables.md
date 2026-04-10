# Q# Variables and Data Types

## Variables in Q#

### Variable Declaration
```qsharp
// Mutable variables
mutable x = 5;
mutable y = 10.0;
mutable message = "Hello Q#";

// Immutable variables (using let)
let constant = 42;
let pi = 3.14159;
let greeting = "Welcome to Q#";

// Variable assignment
set x = x + 1;
set y = y * 2.0;
set message = message + " World";

// Type annotations
mutable intVar : Int = 100;
mutable doubleVar : Double = 3.14;
mutable boolVar : Bool = true;
mutable stringVar : String = "Q# Programming";
```

### Naming Conventions
```qsharp
// Camel case for variables
mutable userName = "Alice";
mutable userAge = 25;
mutable isActive = true;

// Pascal case for constants
let MaxUsers = 1000;
let DefaultTimeout = 30000;
let AppName = "QuantumApp";

// Use descriptive names
mutable userAuthenticationToken = "abc123"; // Good
mutable token = "abc123"; // Too short

// Use verbs for operations
mutable sum = 5 + 10;
mutable product = 3 * 4;
mutable result = sum + product;
```

## Q# Data Types

### Primitive Types
```qsharp
// Integer types
mutable smallInt : Int = -128;           // 32-bit signed integer
mutable bigInt : BigInt = 1000000000L;    // 64-bit signed integer
mutable unsignedInt : UInt = 255u;       // 32-bit unsigned integer

// Floating point types
mutable singlePrecision : Single = 3.14f; // 32-bit float
mutable doublePrecision : Double = 2.718; // 64-bit float

// Boolean type
mutable isReady : Bool = true;
mutable isComplete : Bool = false;

// String type
mutable text : String = "Hello, Quantum!";
mutable emptyString : String = "";

// Pauli types (unique to Q#)
mutable pauliX : PauliX = PauliX;
mutable pauliY : PauliY = PauliY;
mutable pauliZ : PauliZ = PauliZ;
mutable pauliI : PauliI = PauliI;

// Result type (measurement outcome)
mutable measurementResult : Result = Zero;
mutable anotherResult : Result = One;
```

### Complex Types
```qsharp
// Arrays
mutable numberArray : Int[] = [1, 2, 3, 4, 5];
mutable stringArray : String[] = ["Hello", "World", "Q#"];
mutable boolArray : Bool[] = [true, false, true];

// Array operations
mutable firstElement = numberArray[0];
mutable lastElement = numberArray[numberArray.Length - 1];
mutable arrayLength = numberArray.Length;

// Tuples
mutable point : (Double, Double) = (1.0, 2.0);
mutable person : (String, Int) = ("Alice", 25);
mutable complexTuple : (Int, Double, Bool) = (42, 3.14, true);

// Tuple access
mutable xCoord = point::Item1;
mutable yCoord = point::Item2;
mutable name = person::Item1;
mutable age = person::Item2;

// Unit type
mutable unitValue : Unit = ();
```

### Quantum-Specific Types
```qsharp
// Qubit (quantum bit)
// Note: Qubits are allocated using the 'using' statement
using (qubits = Qubit[2]) {
    // Quantum operations here
}

// Result type for measurements
operation MeasureQubit() : Result {
    using (q = Qubit()) {
        // Apply operations
        let result = M(q);
        Reset(q);
        return result;
    }
}

// Complex numbers
mutable complexNum : Complex = Complex(1.0, 2.0);
mutable realPart = complexNum::Real;
mutable imagPart = complexNum::Imag;

// Range types
mutable intRange : Range = 1..10;
mutable stepRange = Range(0, 2, 10);
```

## Type Conversion

### Implicit Conversion
```qsharp
// Q# supports limited implicit conversion
mutable intToDouble : Double = 5; // Int to Double
mutable doubleToInt : Int = 3.14; // Double to Int (truncates)
```

### Explicit Conversion
```qsharp
// Using type conversion functions
mutable strToInt : Int = Int("123");
mutable intToStr : String = IntAsString(42);
mutable doubleToStr : String = DoubleAsString(3.14);

// Complex number operations
mutable realToComplex : Complex = Complex(5.0, 0.0);
mutable fromPolar : Complex = ComplexAsPolar(1.0, 3.14159);

// Result type operations
mutable resultToInt : Int = ResultAsInt(Zero);
mutable intToResult : Result = IntAsResult(1);
```

## Constants and Immutable Values

### Constants
```qsharp
// Constants are defined at operation level
operation ConstantsDemo() : Unit {
    let PI = 3.14159;
    let E = 2.71828;
    let GOLDEN_RATIO = 1.61803;
    
    Message($"PI = {PI}");
    Message($"E = {E}");
    Message($"Golden Ratio = {GOLDEN_RATIO}");
}
```

### Immutable Variables
```qsharp
// Using 'let' for immutable variables
operation ImmutableDemo() : Unit {
    let immutableInt = 42;
    let immutableString = "Cannot change";
    let immutableArray = [1, 2, 3];
    
    // These would cause compilation errors:
    // set immutableInt = 100;
    // set immutableString = "Changed";
    
    Message($"Immutable int: {immutableInt}");
    Message($"Immutable string: {immutableString}");
}
```

## Variable Scope

### Local Scope
```qsharp
operation LocalScopeDemo() : Unit {
    let outerVar = 10;
    
    {
        let innerVar = 20;
        Message($"Inner: {innerVar}");
        Message($"Outer from inner: {outerVar}");
    }
    
    // innerVar is not accessible here
    Message($"Outer: {outerVar}");
}
```

### Operation Parameters
```qsharp
operation ParameterScope(x : Int, y : Double) : Double {
    let result = x + y;
    return result;
}

operation CallParameterScope() : Unit {
    let result = ParameterScope(5, 3.14);
    Message($"Result: {result}");
}
```

## Best Practices

### Variable Naming
```qsharp
operation VariableNaming() : Unit {
    // Use descriptive names
    mutable userAuthenticationToken = "token123";
    mutable quantumStateVector = [0.0, 1.0, 0.0, 0.0];
    mutable measurementOutcome = Zero;
    
    // Use consistent naming conventions
    mutable isActive = true;
    mutable hasCompleted = false;
    mutable canProceed = true;
    
    // Avoid single-letter names except for loop counters
    for i in 0..5 {
        Message($"Iteration {i}");
    }
}
```

### Type Safety
```qsharp
operation TypeSafety() : Unit {
    // Use appropriate types
    mutable userId : Int = 12345;
    mutable userName : String = "Alice";
    mutable isActive : Bool = true;
    mutable balance : Double = 1000.50;
    
    // Convert types explicitly when needed
    mutable stringId = IntAsString(userId);
    mutable idFromString = Int(stringId);
    
    Message($"User ID: {userId}, Name: {userName}");
}
```

### Initialization
```qsharp
operation Initialization() : Unit {
    // Initialize variables immediately
    mutable counter = 0;
    mutable maxAttempts = 3;
    mutable isSuccess = false;
    
    // Use default values where appropriate
    mutable emptyArray = new Int[0];
    mutable defaultResult = Zero;
    
    // Initialize complex types properly
    mutable complexState = Complex(1.0, 0.0);
    mutable pauliMatrix = PauliX;
}
```

## Common Pitfalls

### Type Mismatches
```qsharp
// Common errors and how to avoid them
operation TypeMismatches() : Unit {
    // Error: Type mismatch
    // mutable wrongType : Int = "Hello"; // String to Int
    
    // Correct: Use proper type conversion
    mutable correctType : String = "Hello";
    mutable convertedInt : Int = Int(correctType);
    
    // Error: Pauli type confusion
    // mutable wrongPauli : PauliX = PauliY; // Different Pauli types
    
    // Correct: Use proper Pauli type
    mutable correctPauli : PauliX = PauliX;
}
```

### Scope Issues
```qsharp
operation ScopeIssues() : Unit {
    // Error: Accessing out-of-scope variable
    {
        let localVar = 42;
    }
    // Message($"Value: {localVar}"); // Error: localVar not accessible
    
    // Correct: Access within scope
    {
        let localVar = 42;
        Message($"Value: {localVar}");
    }
}
```

### Mutable vs Immutable
```qsharp
operation MutableImmutable() : Unit {
    // Error: Trying to modify immutable variable
    let immutableVar = 10;
    // set immutableVar = 20; // Error: Cannot modify let variable
    
    // Correct: Use mutable for variables that need to change
    mutable mutableVar = 10;
    set mutableVar = 20; // This works
}
```

## Summary

Q# variables and data types provide:

**Variable Types:**
- `mutable` for variables that can change
- `let` for immutable constants
- Type annotations for clarity
- Descriptive naming conventions

**Data Types:**
- Primitive types: `Int`, `Double`, `Bool`, `String`
- Quantum types: `Qubit`, `Result`, `Pauli` types
- Complex types: Arrays, tuples, `Complex`
- Collection types: Arrays with type parameters

**Type Conversion:**
- Limited implicit conversion
- Explicit conversion functions
- String to/from numeric conversions
- Complex number operations

**Best Practices:**
- Use descriptive variable names
- Choose appropriate types
- Initialize variables properly
- Understand variable scope
- Use immutable variables when possible

**Q# Specific Features:**
- Quantum-specific types (Qubit, Result, Pauli)
- Complex number support
- Measurement result types
- Type-safe quantum operations

Q# provides a strongly-typed, functional programming environment with special support for quantum computing concepts. Understanding variables and data types is fundamental for writing effective quantum algorithms.
