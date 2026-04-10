# Q# Operations and Functions

## Operations in Q#

### Basic Operation Structure
```qsharp
// Basic operation definition
operation HelloWorld() : Unit {
    Message("Hello, Quantum World!");
}

// Operation with parameters
operation GreetUser(name : String) : Unit {
    Message($"Hello, {name}!");
}

// Operation with return value
operation AddNumbers(a : Int, b : Int) : Int {
    return a + b;
}

// Operation with multiple return values (tuple)
operation DivideAndRemainder(a : Int, b : Int) : (Int, Int) {
    let quotient = a / b;
    let remainder = a % b;
    return (quotient, remainder);
}
```

### Operation Types
```qsharp
// Unit operation (no return value)
operation LogMessage(message : String) : Unit {
    Message($"Log: {message}");
}

// Operation returning a value
operation SquareNumber(x : Int) : Int {
    return x * x;
}

// Operation returning a tuple
operation GetCoordinates() : (Double, Double) {
    return (1.0, 2.0);
}

// Operation with generic type parameter
operation Identity<T>(value : T) : T {
    return value;
}
```

### Operation Attributes
```qsharp
// Operation with attributes
@EntryPoint()
operation Main() : Unit {
    Message("This is the entry point");
}

// Operation with adjoint attribute
@Adjoint
operation AdjointOperation() : Unit is Adj+Ctl {
    // This operation can be adjointed
}

// Controlled operation
operation ControlledOperation() : Unit is Ctl {
    // This operation can be controlled
}

// Operation with both adjoint and controlled
@Adjoint
@Controlled
operation QuantumGate() : Unit is Adj+Ctl {
    // This operation is both adjoint and controlled
}
```

## Quantum Operations

### Basic Quantum Operations
```qsharp
// Single-qubit operations
operation ApplyX(q : Qubit) : Unit is Adj+Ctl {
    X(q);
}

operation ApplyY(q : Qubit) : Unit is Adj+Ctl {
    Y(q);
}

operation ApplyZ(q : Qubit) : Unit is Adj+Ctl {
    Z(q);
}

operation ApplyH(q : Qubit) : Unit is Adj+Ctl {
    H(q);
}

// Rotation operations
operation ApplyRx(angle : Double, q : Qubit) : Unit is Adj+Ctl {
    Rx(angle, q);
}

operation ApplyRy(angle : Double, q : Qubit) : Unit is Adj+Ctl {
    Ry(angle, q);
}

operation ApplyRz(angle : Double, q : Qubit) : Unit is Adj+Ctl {
    Rz(angle, q);
}
```

### Multi-Qubit Operations
```qsharp
// Two-qubit gates
operation ApplyCNOT(control : Qubit, target : Qubit) : Unit is Adj+Ctl {
    CNOT(control, target);
}

operation ApplyCZ(control : Qubit, target : Qubit) : Unit is Adj+Ctl {
    CZ(control, target);
}

operation ApplySWAP(q1 : Qubit, q2 : Qubit) : Unit is Adj+Ctl {
    SWAP(q1, q2);
}

// Multi-qubit operations
operation ApplyToffoli(control1 : Qubit, control2 : Qubit, target : Qubit) : Unit is Adj+Ctl {
    CCNOT(control1, control2, target);
}

operation ApplyFredkin(control : Qubit, target1 : Qubit, target2 : Qubit) : Unit is Adj+Ctl {
    CSWAP(control, target1, target2);
}
```

### Measurement Operations
```qsharp
// Single-qubit measurement
operation MeasureQubit(q : Qubit) : Result {
    return M(q);
}

// Measurement in different bases
operation MeasureInX(q : Qubit) : Result {
    H(q);
    let result = M(q);
    H(q);
    return result;
}

operation MeasureInY(q : Qubit) : Result {
    Adjoint S(q);
    H(q);
    let result = M(q);
    H(q);
    S(q);
    return result;
}

// Multi-qubit measurement
operation MeasureBellState(q1 : Qubit, q2 : Qubit) : (Result, Result) {
    let r1 = M(q1);
    let r2 = M(q2);
    return (r1, r2);
}
```

## Classical Operations

### Mathematical Operations
```qsharp
// Basic arithmetic
operation ArithmeticOperations() : Unit {
    let a = 10;
    let b = 3;
    
    let sum = a + b;
    let difference = a - b;
    let product = a * b;
    let quotient = a / b;
    let remainder = a % b;
    
    Message($"Sum: {sum}");
    Message($"Difference: {difference}");
    Message($"Product: {product}");
    Message($"Quotient: {quotient}");
    Message($"Remainder: {remainder}");
}

// Floating-point operations
operation FloatingPointOperations() : Unit {
    let x = 3.14;
    let y = 2.71;
    
    let sum = x + y;
    let difference = x - y;
    let product = x * y;
    let quotient = x / y;
    
    Message($"Float sum: {sum}");
    Message($"Float difference: {difference}");
    Message($"Float product: {product}");
    Message($"Float quotient: {quotient}");
}

// Complex number operations
operation ComplexOperations() : Unit {
    let z1 = Complex(1.0, 2.0);
    let z2 = Complex(3.0, 4.0);
    
    let sum = z1 + z2;
    let difference = z1 - z2;
    let product = z1 * z2;
    
    Message($"Complex sum: {sum}");
    Message($"Complex difference: {difference}");
    Message($"Complex product: {product}");
}
```

### String Operations
```qsharp
// String manipulation
operation StringOperations() : Unit {
    let greeting = "Hello, ";
    let name = "Q#";
    
    // String concatenation
    let message = greeting + name;
    Message(message);
    
    // String length
    let length = Length(message);
    Message($"Length: {length}");
    
    // String to numeric conversion
    let numberString = "42";
    let number = Int(numberString);
    Message($"Number from string: {number}");
    
    // Numeric to string conversion
    let value = 123;
    let valueString = IntAsString(value);
    Message($"String from number: {valueString}");
}
```

### Array Operations
```qsharp
// Array manipulation
operation ArrayOperations() : Unit {
    let numbers = [1, 2, 3, 4, 5];
    let strings = ["Hello", "World", "Q#"];
    
    // Array length
    Message($"Numbers length: {numbers.Length}");
    Message($"Strings length: {strings.Length}");
    
    // Array access
    let firstNumber = numbers[0];
    let lastNumber = numbers[numbers.Length - 1];
    
    Message($"First number: {firstNumber}");
    Message($"Last number: {lastNumber}");
    
    // Array slicing (if supported)
    // let slice = numbers[1..3];
    
    // Array iteration
    for number in numbers {
        Message($"Number: {number}");
    }
}
```

## Operation Composition

### Calling Operations
```qsharp
// Basic operation calls
operation OperationCalling() : Unit {
    // Call unit operation
    LogMessage("Starting operation");
    
    // Call operation with return value
    let result = SquareNumber(5);
    Message($"Square: {result}");
    
    // Call operation with parameters
    GreetUser("Alice");
    
    // Call operation returning tuple
    let (quotient, remainder) = DivideAndRemainder(10, 3);
    Message($"10 / 3 = {quotient} remainder {remainder}");
}

// Nested operation calls
operation NestedCalls() : Unit {
    let result = SquareNumber(AddNumbers(3, 4));
    Message($"(3 + 4)² = {result}");
}
```

### Partial Application
```qsharp
// Partial application (functor)
operation PartialApplication() : Unit {
    // Create a functor by partially applying an operation
    let addFive = AddNumbers(5, _);
    
    let result1 = addFive(10);
    let result2 = addFive(20);
    
    Message($"5 + 10 = {result1}");
    Message($"5 + 20 = {result2}");
}

// Operation as parameter
operation HigherOrderOperation(op : (Int -> Int), value : Int) : Int {
    return op(value);
}

operation UseHigherOrder() : Unit {
    let result = HigherOrderOperation(SquareNumber, 5);
    Message($"Square of 5: {result}");
}
```

### Conditional Operations
```qsharp
// Conditional execution
operation ConditionalOperations(condition : Bool) : Unit {
    if (condition) {
        Message("Condition is true");
    } else {
        Message("Condition is false");
    }
}

// Conditional quantum operations
operation ConditionalQuantum(condition : Bool, q : Qubit) : Unit {
    if (condition) {
        X(q);
    }
}

// Pattern matching with results
operation PatternMatching(result : Result) : Unit {
    if (result == Zero) {
        Message("Measured Zero");
    } elif (result == One) {
        Message("Measured One");
    }
}
```

## Advanced Operation Features

### Adjoint Operations
```qsharp
// Operation with explicit adjoint
operation AdjointDemo() : Unit is Adj {
    // Operation body
}

// Adjoint operation implementation
operation AdjointAdjointDemo() : Unit is Adj {
    // Operation body
}

// Using adjoint operations
operation UseAdjoint() : Unit {
    using (q = Qubit()) {
        // Apply operation
        AdjointDemo(q);
        
        // Apply adjoint
        Adjoint AdjointDemo(q);
        
        // Should be back to original state
        Reset(q);
    }
}
```

### Controlled Operations
```qsharp
// Controlled operation
operation ControlledDemo() : Unit is Ctl {
    // Operation body
}

// Using controlled operations
operation UseControlled() : Unit {
    using (control = Qubit(), target = Qubit()) {
        // Put control in superposition
        H(control);
        
        // Apply controlled operation
        Controlled ControlledDemo([control], target);
        
        // Measure
        let controlResult = M(control);
        let targetResult = M(target);
        
        Message($"Control: {controlResult}, Target: {targetResult}");
        
        ResetAll([control, target]);
    }
}
```

### Controlled-Adjoint Operations
```qsharp
// Operation that is both controlled and adjoint
operation ControlledAdjointDemo() : Unit is Adj+Ctl {
    // Operation body
}

// Using controlled-adjoint operations
operation UseControlledAdjoint() : Unit {
    using (controls = Qubit[2], target = Qubit()) {
        // Put controls in superposition
        ApplyToEach(H, controls);
        
        // Apply controlled-adjoint operation
        Controlled ControlledAdjointDemo(controls, target);
        
        // Apply adjoint of controlled operation
        Adjoint Controlled ControlledAdjointDemo(controls, target);
        
        ResetAll(controls + [target]);
    }
}
```

## Error Handling

### Try-Catch Pattern
```qsharp
// Error handling with Result type
operation SafeDivision(a : Int, b : Int) : Result {
    if (b == 0) {
        return ResultAsInt(0); // Error case
    }
    
    return ResultAsInt(a / b); // Success case
}

operation ErrorHandling() : Unit {
    let result = SafeDivision(10, 2);
    
    if (result == ResultAsInt(0)) {
        Message("Division failed (division by zero)");
    } else {
        Message($"Division succeeded: {result}");
    }
}
```

### Validation Operations
```qsharp
// Input validation
operation ValidateInput(value : Int) : Bool {
    return value >= 0 && value <= 100;
}

operation ValidationExample() : Unit {
    let value = 50;
    
    if (ValidateInput(value)) {
        Message($"Value {value} is valid");
    } else {
        Message($"Value {value} is invalid");
    }
}
```

## Best Practices

### Operation Design
```qsharp
// Good operation design
operation WellDesignedOperation(input : String, iterations : Int) : Unit {
    // Input validation
    if (Length(input) == 0) {
        Message("Input cannot be empty");
        return;
    }
    
    if (iterations <= 0) {
        Message("Iterations must be positive");
        return;
    }
    
    // Main logic
    for i in 0..iterations {
        Message($"{input} - Iteration {i}");
    }
}

// Use appropriate operation attributes
@EntryPoint()
operation EntryPointOperation() : Unit {
    Message("This is the entry point");
}

@Adjoint
@Controlled
operation QuantumOperation(q : Qubit) : Unit is Adj+Ctl {
    // Quantum operation that can be adjointed and controlled
    H(q);
}
```

### Resource Management
```qsharp
// Proper qubit management
operation ResourceManagement() : Unit {
    using (qubits = Qubit[2]) {
        // Use qubits
        for q in qubits {
            H(q);
        }
        
        // Measure
        let results = MultiM(qubits);
        
        // Reset is handled automatically by 'using' block
    }
}

// Manual resource management
operation ManualResourceManagement() : Unit {
    // Allocate qubits manually when needed
    let qubits = Qubit[2];
    
    try {
        // Use qubits
        for q in qubits {
            H(q);
        }
        
        // Measure
        let results = MultiM(qubits);
    } finally {
        // Always reset qubits
        ResetAll(qubits);
    }
}
```

### Performance Considerations
```qsharp
// Efficient operations
operation EfficientOperation(data : Int[]) : Int {
    mutable sum = 0;
    
    // Use efficient iteration
    for item in data {
        set sum += item;
    }
    
    return sum;
}

// Avoid unnecessary allocations
operation EfficientQuantumOperation() : Unit {
    using (qubits = Qubit[1]) {
        // Reuse qubits when possible
        let q = qubits[0];
        
        // Chain operations
        H(q);
        Rz(0.5, q);
        H(q);
        
        // Single measurement
        let result = M(q);
        
        Message($"Result: {result}");
    }
}
```

## Common Pitfalls

### Common Operation Errors
```qsharp
// Error: Forgetting to reset qubits
operation BadResourceManagement() : Unit {
    let q = Qubit();
    X(q);
    // Forgot to reset q - resource leak!
}

// Correct: Always reset qubits
operation GoodResourceManagement() : Unit {
    using (q = Qubit()) {
        X(q);
        let result = M(q);
        Message($"Result: {result}");
    }
}

// Error: Type mismatch
operation TypeMismatchError() : Unit {
    let x : Int = 5;
    // let y : Double = x; // Type mismatch
    let y : Double = IntAsDouble(x); // Correct conversion
}

// Error: Invalid measurement
operation MeasurementError() : Unit {
    using (q = Qubit()) {
        X(q);
        let result = M(q);
        // Trying to use q after measurement without reset
        // X(q); // Error: q is in classical state
    }
}
```

### Quantum-Specific Pitfalls
```qsharp
// Error: Not considering measurement collapse
operation MeasurementPitfall() : Unit {
    using (q = Qubit()) {
        H(q);
        let result = M(q);
        
        // q is now in a classical state
        // Any further quantum operations will have no effect
        H(q); // This has no effect
    }
}

// Error: Ignoring entanglement
operation EntanglementPitfall() : Unit {
    using (qubits = Qubit[2]) {
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        // qubits are now entangled
        let result1 = M(qubits[0]);
        let result2 = M(qubits[1]);
        
        // result2 is correlated with result1
        Message($"Results: {result1}, {result2}");
    }
}
```

## Summary

Q# operations and functions provide:

**Operation Types:**
- Unit operations (no return value)
- Operations returning values
- Operations returning tuples
- Generic operations with type parameters

**Quantum Operations:**
- Single-qubit gates (X, Y, Z, H)
- Rotation gates (Rx, Ry, Rz)
- Multi-qubit gates (CNOT, CZ, SWAP)
- Measurement operations

**Classical Operations:**
- Mathematical operations
- String manipulation
- Array operations
- Type conversions

**Advanced Features:**
- Adjoint operations
- Controlled operations
- Controlled-adjoint operations
- Partial application
- Higher-order operations

**Best Practices:**
- Proper resource management
- Input validation
- Error handling
- Performance optimization
- Appropriate use of attributes

**Common Pitfalls:**
- Resource leaks (not resetting qubits)
- Type mismatches
- Measurement collapse issues
- Ignoring entanglement
- Invalid quantum operations

Q# operations provide a powerful framework for both classical and quantum computing, with special support for quantum-specific concepts like adjoint and controlled operations. Understanding these concepts is essential for writing effective quantum algorithms.
