# Q# Debugging and Testing

## Debugging in Q#

### Basic Debugging Techniques
```qsharp
// Using Message for debugging
operation BasicDebugging() : Unit {
    mutable counter = 0;
    
    // Debug variable values
    set counter = 5;
    Message($"Counter value: {counter}");
    
    // Debug loop iterations
    for i in 0..3 {
        set counter += i;
        Message($"Iteration {i}, counter = {counter}");
    }
    
    // Debug array contents
    let numbers = [1, 2, 3, 4, 5];
    for i in 0..Length(numbers) {
        Message($"numbers[{i}] = {numbers[i]}");
    }
}

// Debugging quantum states
operation DebugQuantumStates() : Unit {
    using (q = Qubit()) {
        // Debug initial state
        Message("Initial state: |0⟩");
        
        // Apply gate and debug
        H(q);
        Message("After H gate: superposition");
        
        // Debug measurement
        let result = M(q);
        Message($"Measurement result: {result}");
        
        Reset(q);
    }
}

// Debugging complex operations
operation DebugComplexOperation() : Unit {
    using (qubits = Qubit[2]) {
        // Debug initial state
        Message("Initial state: |00⟩");
        
        // Apply operations with debugging
        H(qubits[0]);
        Message("Applied H to qubit 0: |+0⟩");
        
        CNOT(qubits[0], qubits[1]);
        Message("Applied CNOT: Bell state |Φ+⟩");
        
        // Debug measurements
        let results = MultiM(qubits);
        Message($"Bell state measurement: {results}");
        
        ResetAll(qubits);
    }
}
```

### Advanced Debugging
```qsharp
// Debugging with conditional messages
operation ConditionalDebugging(verbose : Bool) : Unit {
    mutable value = 42;
    
    // Only show debug messages if verbose flag is true
    if (verbose) {
        Message($"Debug: Initial value = {value}");
    }
    
    set value = value * 2;
    
    if (verbose) {
        Message($"Debug: Final value = {value}");
    }
}

// Debugging with formatted output
operation FormattedDebugging() : Unit {
    let name = "Alice";
    let age = 25;
    let isActive = true;
    
    // Use formatted strings for debugging
    Message($"User Profile: Name={name}, Age={age}, Active={isActive}");
    
    // Debug arrays
    let scores = [85, 92, 78, 96, 88];
    Message($"Scores: [{scores[0]}, {scores[1]}, {scores[2]}, {scores[3]}, {scores[4]}]");
    
    // Debug tuples
    let point = (3.14, 2.71);
    Message($"Point: x={point::Item1}, y={point::Item2}");
}

// Debugging quantum operations step by step
operation StepByStepDebugging() : Unit {
    using (q = Qubit()) {
        Message("Step 1: Allocate qubit");
        
        Message("Step 2: Apply H gate");
        H(q);
        
        Message("Step 3: Apply phase gate");
        Rz(0.5, q);
        
        Message("Step 4: Apply H gate again");
        H(q);
        
        Message("Step 5: Measure qubit");
        let result = M(q);
        Message($"Step 6: Measurement result = {result}");
        
        Reset(q);
    }
}
```

### Error Handling and Validation
```qsharp
// Input validation with debugging
operation ValidatedOperation(input : Int) : Int {
    Message($"Input validation: input = {input}");
    
    if (input < 0) {
        Message("Error: Input must be non-negative");
        return 0;
    }
    
    if (input > 100) {
        Message("Error: Input must be <= 100");
        return 100;
    }
    
    Message($"Input validation passed: {input}");
    return input;
}

// Error handling in quantum operations
operation ErrorHandlingQuantum() : Unit {
    using (q = Qubit()) {
        // Validate qubit state before operations
        Message("Validating qubit state");
        
        // Apply operation with error checking
        try {
            H(q);
            let result = M(q);
            Message($"Operation successful: {result}");
            
            Reset(q);
        } catch {
            Message("Error in quantum operation");
            Reset(q);
        }
    }
}

// Debugging resource allocation
operation DebugResourceAllocation() : Unit {
    Message("Starting resource allocation debugging");
    
    // Track qubit allocation
    using (q1 = Qubit()) {
        Message("Allocated qubit 1");
        
        using (q2 = Qubit()) {
            Message("Allocated qubit 2");
            
            // Use qubits
            H(q1);
            CNOT(q1, q2);
            
            let results = MultiM([q1, q2]);
            Message($"Results: {results}");
            
            Message("Releasing qubit 2");
        }
        
        Message("Releasing qubit 1");
    }
    
    Message("Resource allocation debugging completed");
}
```

## Testing in Q#

### Unit Testing
```qsharp
// Simple unit test operation
operation TestAddition() : Bool {
    let a = 5;
    let b = 3;
    let result = a + b;
    
    // Test assertion
    if (result == 8) {
        Message("TestAddition: PASSED");
        return true;
    } else {
        Message("TestAddition: FAILED - Expected 8, got {result}");
        return false;
    }
}

// Test with multiple assertions
operation TestMultipleOperations() : Bool {
    mutable passed = 0;
    mutable total = 0;
    
    // Test 1: Addition
    set total += 1;
    if (5 + 3 == 8) {
        set passed += 1;
        Message("Addition test: PASSED");
    } else {
        Message("Addition test: FAILED");
    }
    
    // Test 2: Multiplication
    set total += 1;
    if (4 * 5 == 20) {
        set passed += 1;
        Message("Multiplication test: PASSED");
    } else {
        Message("Multiplication test: FAILED");
    }
    
    // Test 3: String operations
    set total += 1;
    let str1 = "Hello";
    let str2 = "World";
    let combined = str1 + str2;
    if (combined == "HelloWorld") {
        set passed += 1;
        Message("String test: PASSED");
    } else {
        Message("String test: FAILED");
    }
    
    Message($"Tests passed: {passed}/{total}");
    return passed == total;
}
```

### Quantum Testing
```qsharp
// Test quantum gate properties
operation TestHadamardGate() : Bool {
    using (q = Qubit()) {
        // Test H|0⟩ = |+⟩
        H(q);
        
        // Measure multiple times to verify superposition
        mutable zeroCount = 0;
        mutable oneCount = 0;
        
        for i in 0..100 {
            let result = M(q);
            if (result == Zero) {
                set zeroCount += 1;
            } else {
                set oneCount += 1;
            }
            
            // Recreate superposition
            H(q);
        }
        
        Reset(q);
        
        // Check approximately equal distribution
        let tolerance = 10;
        let diff = AbsI(zeroCount - oneCount);
        
        Message($"Hadamard test: Zero={zeroCount}, One={oneCount}, Diff={diff}");
        
        if (diff <= tolerance) {
            Message("Hadamard test: PASSED");
            return true;
        } else {
            Message("Hadamard test: FAILED - Distribution not uniform");
            return false;
        }
    }
}

// Test CNOT gate properties
operation TestCNOTGate() : Bool {
    using (control = Qubit(), target = Qubit()) {
        // Test CNOT|00⟩ = |00⟩
        let result1 = MultiM([control, target]);
        
        // Test CNOT|10⟩ = |11⟩
        X(control);
        CNOT(control, target);
        let result2 = MultiM([control, target]);
        
        // Test CNOT|01⟩ = |01⟩
        ResetAll([control, target]);
        X(target);
        CNOT(control, target);
        let result3 = MultiM([control, target]);
        
        // Test CNOT|11⟩ = |10⟩
        ResetAll([control, target]);
        X(control);
        X(target);
        CNOT(control, target);
        let result4 = MultiM([control, target]);
        
        ResetAll([control, target]);
        
        // Verify results
        let expected1 = [Zero, Zero];
        let expected2 = [One, One];
        let expected3 = [Zero, One];
        let expected4 = [One, Zero];
        
        let test1 = result1 == expected1;
        let test2 = result2 == expected2;
        let test3 = result3 == expected3;
        let test4 = result4 == expected4;
        
        Message($"CNOT tests: {test1}, {test2}, {test3}, {test4}");
        
        if (test1 && test2 && test3 && test4) {
            Message("CNOT test: PASSED");
            return true;
        } else {
            Message("CNOT test: FAILED");
            return false;
        }
    }
}

// Test entanglement
operation TestEntanglement() : Bool {
    using (qubits = Qubit[2]) {
        // Create Bell state |Φ+⟩ = (|00⟩ + |11⟩)/√2
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        // Measure multiple times
        mutable zeroZero = 0;
        mutable oneOne = 0;
        mutable other = 0;
        
        for i in 0..100 {
            let results = MultiM(qubits);
            
            if (results == [Zero, Zero]) {
                set zeroZero += 1;
            } elif (results == [One, One]) {
                set oneOne += 1;
            } else {
                set other += 1;
            }
            
            // Recreate Bell state
            ResetAll(qubits);
            H(qubits[0]);
            CNOT(qubits[0], qubits[1]);
        }
        
        ResetAll(qubits);
        
        Message($"Entanglement test: |00⟩={zeroZero}, |11⟩={oneOne}, Other={other}");
        
        // Should only get |00⟩ and |11⟩ for Bell state
        if (other == 0) {
            Message("Entanglement test: PASSED");
            return true;
        } else {
            Message("Entanglement test: FAILED - Found non-entangled states");
            return false;
        }
    }
}
```

### Integration Testing
```qsharp
// Integration test for quantum circuit
operation IntegrationTest() : Bool {
    using (qubits = Qubit[3]) {
        Message("Integration test: Starting 3-qubit circuit");
        
        // Step 1: Initialize
        ApplyToEach(H, qubits);
        Message("Step 1: Applied H to all qubits");
        
        // Step 2: Create entanglement
        CNOT(qubits[0], qubits[1]);
        CNOT(qubits[1], qubits[2]);
        Message("Step 2: Created entanglement");
        
        // Step 3: Apply rotations
        Rz(0.5, qubits[0]);
        Ry(0.3, qubits[1]);
        Rx(0.7, qubits[2]);
        Message("Step 3: Applied rotations");
        
        // Step 4: Final Hadamards
        ApplyToEach(H, qubits);
        Message("Step 4: Applied final Hadamards");
        
        // Step 5: Measure
        let results = MultiM(qubits);
        Message($"Step 5: Final measurement: {results}");
        
        ResetAll(qubits);
        
        // Validate results (simplified validation)
        Message("Integration test: COMPLETED");
        return true;
    }
}

// Performance testing
operation PerformanceTest() : Unit {
    let startTime = 1000000; // Mock timestamp
    
    // Test performance of repeated operations
    for i in 0..1000 {
        using (q = Qubit()) {
            H(q);
            let result = M(q);
            Reset(q);
        }
        
        if (i % 100 == 0) {
            Message($"Performance test: Completed {i} iterations");
        }
    }
    
    let endTime = 1001000; // Mock timestamp
    let duration = endTime - startTime;
    
    Message($"Performance test: Completed in {duration} time units");
}
```

## Test Framework

### Test Runner
```qsharp
// Simple test runner
operation TestRunner() : Unit {
    Message("Running Q# Test Suite");
    Message("===================");
    
    let tests = [
        ("Addition", TestAddition),
        ("Multiple Operations", TestMultipleOperations),
        ("Hadamard Gate", TestHadamardGate),
        ("CNOT Gate", TestCNOTGate),
        ("Entanglement", TestEntanglement),
        ("Integration", IntegrationTest),
    ];
    
    mutable passed = 0;
    mutable total = Length(tests);
    
    for (name, test) in tests {
        Message($"Running test: {name}");
        let result = test();
        
        if (result) {
            set passed += 1;
            Message($"✓ {name}: PASSED");
        } else {
            Message($"✗ {name}: FAILED");
        }
    }
    
    Message("===================");
    Message($"Test Results: {passed}/{total} tests passed");
    
    if (passed == total) {
        Message("All tests PASSED! 🎉");
    } else {
        Message("Some tests FAILED! ❌");
    }
}

// Benchmark runner
operation BenchmarkRunner() : Unit {
    Message("Running Q# Benchmarks");
    Message("===================");
    
    let benchmarks = [
        ("Single Qubit Operations", BenchmarkSingleQubit),
        ("Multi-Qubit Operations", BenchmarkMultiQubit),
        ("Gate Performance", BenchmarkGatePerformance),
    ];
    
    for (name, benchmark) in benchmarks {
        Message($"Running benchmark: {name}");
        benchmark();
        Message($"Benchmark {name}: COMPLETED");
    }
    
    Message("===================");
    Message("All benchmarks COMPLETED!");
}

// Individual benchmarks
operation BenchmarkSingleQubit() : Unit {
    using (q = Qubit()) {
        // Benchmark single qubit operations
        for i in 0..1000 {
            H(q);
            Rz(0.1, q);
            Ry(0.2, q);
            Rx(0.3, q);
            let result = M(q);
            Reset(q);
        }
    }
    
    Message("Single qubit benchmark: 1000 iterations");
}

operation BenchmarkMultiQubit() : Unit {
    using (qubits = Qubit[5]) {
        // Benchmark multi-qubit operations
        for i in 0..500 {
            ApplyToEach(H, qubits);
            
            for j in 0..(Length(qubits) - 1) {
                CNOT(qubits[j], qubits[j + 1]);
            }
            
            let results = MultiM(qubits);
            ResetAll(qubits);
        }
    }
    
    Message("Multi-qubit benchmark: 500 iterations");
}

operation BenchmarkGatePerformance() : Unit {
    using (q = Qubit()) {
        // Benchmark specific gate performance
        for i in 0..2000 {
            H(q);
            Y(q);
            Z(q);
            X(q);
            Reset(q);
        }
    }
    
    Message("Gate performance benchmark: 2000 iterations");
}
```

## Mock and Stub Testing

### Mock Operations
```qsharp
// Mock quantum operation for testing
operation MockQuantumOperation(input : Int) : Int {
    // Mock implementation for testing
    Message($"MockQuantumOperation called with input: {input}");
    
    // Simulate quantum computation
    let result = input * 2;
    
    Message($"MockQuantumOperation returning: {result}");
    return result;
}

// Test using mock operation
operation TestWithMock() : Bool {
    let input = 5;
    let expected = 10;
    let actual = MockQuantumOperation(input);
    
    if (actual == expected) {
        Message("Mock test: PASSED");
        return true;
    } else {
        Message($"Mock test: FAILED - Expected {expected}, got {actual}");
        return false;
    }
}

// Stub for hardware-specific operations
operation StubHardwareOperation() : Unit {
    // Stub for hardware-specific quantum operations
    Message("StubHardwareOperation: Hardware operation not available in simulation");
    
    // Provide fallback behavior
    using (q = Qubit()) {
        H(q);
        let result = M(q);
        Message($"Fallback result: {result}");
        Reset(q);
    }
}
```

### Property-Based Testing
```qsharp
// Property-based test for commutativity
operation TestCommutativity() : Bool {
    // Test that addition is commutative
    let testCases = [(1, 2), (5, 10), (100, 200)];
    
    for (a, b) in testCases {
        let result1 = a + b;
        let result2 = b + a;
        
        if (result1 != result2) {
            Message($"Commutativity test FAILED: {a} + {b} ≠ {b} + {a}");
            return false;
        }
    }
    
    Message("Commutativity test: PASSED");
    return true;
}

// Property-based test for associativity
operation TestAssociativity() : Bool {
    // Test that addition is associative
    let testCases = [(1, 2, 3), (5, 10, 15), (100, 200, 300)];
    
    for (a, b, c) in testCases {
        let result1 = (a + b) + c;
        let result2 = a + (b + c);
        
        if (result1 != result2) {
            Message($"Associativity test FAILED: ({a} + {b}) + {c} ≠ {a} + ({b} + {c})");
            return false;
        }
    }
    
    Message("Associativity test: PASSED");
    return true;
}

// Property-based test for quantum gates
operation TestQuantumProperties() : Bool {
    // Test that H² = I (identity)
    using (q = Qubit()) {
        H(q);
        H(q);
        
        let result = M(q);
        Reset(q);
        
        if (result != Zero) {
            Message("H² = I test: FAILED");
            return false;
        }
    }
    
    // Test that X² = I
    using (q = Qubit()) {
        X(q);
        X(q);
        
        let result = M(q);
        Reset(q);
        
        if (result != Zero) {
            Message("X² = I test: FAILED");
            return false;
        }
    }
    
    Message("Quantum properties test: PASSED");
    return true;
}
```

## Regression Testing
```qsharp
// Regression test for known issues
operation RegressionTest() : Unit {
    Message("Running regression tests");
    
    // Test for specific bug fixes
    TestBugFix1();
    TestBugFix2();
    TestBugFix3();
    
    Message("Regression tests completed");
}

// Test for specific bug fix 1
operation TestBugFix1() : Bool {
    // Test that array indexing works correctly
    let array = [1, 2, 3, 4, 5];
    
    // Test boundary conditions
    let first = array[0];
    let last = array[Length(array) - 1];
    
    if (first == 1 && last == 5) {
        Message("Bug fix 1 test: PASSED");
        return true;
    } else {
        Message("Bug fix 1 test: FAILED");
        return false;
    }
}

// Test for specific bug fix 2
operation TestBugFix2() : Bool {
    // Test that string concatenation works correctly
    let str1 = "Hello";
    let str2 = "World";
    let combined = str1 + str2;
    
    if (combined == "HelloWorld") {
        Message("Bug fix 2 test: PASSED");
        return true;
    } else {
        Message("Bug fix 2 test: FAILED");
        return false;
    }
}

// Test for specific bug fix 3
operation TestBugFix3() : Bool {
    // Test that complex number operations work correctly
    let z1 = Complex(1.0, 2.0);
    let z2 = Complex(3.0, 4.0);
    let sum = z1 + z2;
    
    let expectedReal = 4.0;
    let expectedImag = 6.0;
    
    if (sum::Real == expectedReal && sum::Imag == expectedImag) {
        Message("Bug fix 3 test: PASSED");
        return true;
    } else {
        Message("Bug fix 3 test: FAILED");
        return false;
    }
}
```

## Best Practices

### Debugging Best Practices
```qsharp
// Structured debugging approach
operation StructuredDebugging() : Unit {
    // 1. Log entry point
    Message("=== StructuredDebugging Starting ===");
    
    // 2. Log configuration
    let debugLevel = 2;
    Message($"Debug level: {debugLevel}");
    
    // 3. Log operations with context
    mutable data = [1, 2, 3];
    Message($"Initial data: {data}");
    
    // 4. Log modifications with before/after
    Message("Before modification: {data}");
    set data = [4, 5, 6];
    Message("After modification: {data}");
    
    // 5. Log exit point
    Message("=== StructuredDebugging Completed ===");
}

// Performance-aware debugging
operation PerformanceAwareDebugging() : Unit {
    // Only debug in debug mode
    if (true) { // Replace with actual debug flag
        using (qubits = Qubit[10]) {
            Message("Performance debug: Allocating 10 qubits");
            
            // Debug expensive operations sparingly
            for i in 0..10 {
                if (i % 3 == 0) {
                    Message($"Performance debug: Iteration {i}");
                }
                
                H(qubits[i]);
            }
            
            Message("Performance debug: Operations completed");
        }
    }
}

// Error-aware debugging
operation ErrorAwareDebugging() : Unit {
    try {
        // Operation that might fail
        using (q = Qubit()) {
            H(q);
            let result = M(q);
            
            if (result == Zero) {
                Message("Debug: Measured Zero (expected)");
            } else {
                Message($"Debug: Unexpected result: {result}");
            }
            
            Reset(q);
        }
    } catch {
        Message("Debug: Caught exception in quantum operation");
    }
}
```

### Testing Best Practices
```qsharp
// Test organization
operation OrganizedTesting() : Unit {
    // Group related tests
    let mathTests = [
        ("Addition", TestAddition),
        ("Multiplication", TestMultiplication),
        ("Division", TestDivision),
    ];
    
    let quantumTests = [
        ("Hadamard", TestHadamardGate),
        ("CNOT", TestCNOTGate),
        ("Entanglement", TestEntanglement),
    ];
    
    // Run test suites
    RunTestSuite("Math Tests", mathTests);
    RunTestSuite("Quantum Tests", quantumTests);
}

// Test suite runner
operation RunTestSuite(suiteName : String, tests : (String, () -> Bool)[]) : Unit {
    Message($"Running {suiteName}");
    Message("------------------");
    
    mutable passed = 0;
    mutable total = Length(tests);
    
    for (name, test) in tests {
        Message($"Running: {name}");
        let result = test();
        
        if (result) {
            set passed += 1;
            Message($"✓ {name}");
        } else {
            Message($"✗ {name}");
        }
    }
    
    Message("------------------");
    Message($"{suiteName}: {passed}/{total} tests passed");
}

// Test multiplication
operation TestMultiplication() : Bool {
    let testCases = [(2, 3, 6), (5, 4, 20), (10, 10, 100)];
    
    for (a, b, expected) in testCases {
        let result = a * b;
        if (result != expected) {
            Message($"Multiplication test FAILED: {a} * {b} = {result}, expected {expected}");
            return false;
        }
    }
    
    return true;
}

// Test division
operation TestDivision() : Bool {
    let testCases = [(6, 2, 3), (20, 4, 5), (100, 10, 10)];
    
    for (a, b, expected) in testCases {
        let result = a / b;
        if (result != expected) {
            Message($"Division test FAILED: {a} / {b} = {result}, expected {expected}");
            return false;
        }
    }
    
    return true;
}
```

## Common Pitfalls

### Common Debugging Mistakes
```qsharp
// Mistake: Too many debug messages
operation TooMuchDebugging() : Unit {
    // Bad: Debug every single step
    for i in 0..1000 {
        Message($"Iteration {i}"); // Too much output!
        // ... actual work ...
    }
    
    // Good: Debug at key points
    Message("Starting large operation");
    for i in 0..1000 {
        if (i % 100 == 0) {
            Message($"Progress: {i}/1000");
        }
        // ... actual work ...
    }
    Message("Large operation completed");
}

// Mistake: Not resetting qubits
operation NotResettingQubits() : Unit {
    // Bad: Forgetting to reset qubits
    let q = Qubit();
    X(q);
    let result = M(q);
    // Forgot: Reset(q);
    
    // Good: Always reset qubits
    using (goodQubit = Qubit()) {
        X(goodQubit);
        let result = M(goodQubit);
        // Reset is automatic
    }
}

// Mistake: Testing in production code
operation TestingInProduction() : Unit {
    // Bad: Running tests in production
    let testResult = TestAddition();
    Message($"Test result: {testResult}");
    
    // Good: Use assertions or debug flags
    #if DEBUG
    let testResult = TestAddition();
    if (!testResult) {
        Message("WARNING: Test failed in debug mode");
    }
    #endif
}
```

### Common Testing Mistakes
```qsharp
// Mistake: Not testing edge cases
operation NotTestingEdgeCases() : Unit {
    // Bad: Only testing normal cases
    let result = AddNumbers(5, 3);
    Message($"5 + 3 = {result}");
    
    // Good: Test edge cases
    let edgeCases = [
        (0, 0),    // Zero values
        (1, 1),    // Identity
        (-1, 1),   // Negative
        (Int.MaxValue(), 1), // Maximum value
    ];
    
    for (a, b) in edgeCases {
        let result = AddNumbers(a, b);
        Message($"Edge case: {a} + {b} = {result}");
    }
}

// Mistake: Not testing quantum error cases
operation NotTestingQuantumErrors() : Unit {
    // Bad: Only testing ideal cases
    using (q = Qubit()) {
        H(q);
        let result = M(q);
        Message($"Ideal case: {result}");
        Reset(q);
    }
    
    // Good: Test error cases and edge cases
    using (q = Qubit()) {
        // Test with different initial states
        H(q);
        let result1 = M(q);
        Message($"Superposition case: {result1}");
        
        Reset(q);
        X(q);
        let result2 = M(q);
        Message(|"Excited state case: {result2}");
        
        Reset(q);
    }
}

// Mistake: Hard-coded test values
operation HardcodedTestValues() : Unit {
    // Bad: Hard-coded expected values
    let result = SomeOperation();
    if (result == 42) {
        Message("Test passed");
    }
    
    // Good: Calculate expected values
    let input = 5;
    let expected = input * 8 + 2;
    let result = SomeOperation();
    
    if (result == expected) {
        Message($"Test passed: {input} * 8 + 2 = {result}");
    }
}

// Mock operation for testing
operation SomeOperation() : Int {
    return 42;
}
```

## Summary

Q# debugging and testing provide:

**Debugging Techniques:**
- Message-based debugging
- Step-by-step debugging
- Conditional debugging
- Error handling and validation
- Resource allocation debugging

**Testing Framework:**
- Unit testing for classical operations
- Quantum operation testing
- Integration testing
- Performance benchmarking
- Test runners and suites

**Advanced Testing:**
- Mock and stub testing
- Property-based testing
- Regression testing
- Error case testing
- Edge case validation

**Best Practices:**
- Structured debugging approach
- Performance-aware debugging
- Error-aware debugging
- Test organization
- Comprehensive test coverage

**Common Pitfalls:**
- Too many debug messages
- Not resetting qubits
- Testing in production code
- Not testing edge cases
- Hard-coded test values
- Incomplete test coverage

**Testing Tools:**
- Message statements for debugging
- Assert operations
- Test runners
- Benchmark frameworks
- Mock operations

**Debugging Tools:**
- Formatted output
- Conditional debugging
- Resource tracking
- Error handling
- Performance monitoring

Q# provides built-in debugging and testing capabilities that allow developers to verify quantum algorithms and classical operations effectively. Proper debugging and testing practices ensure reliable quantum program development.
