# Q# Quantum Concepts

## Qubits and Quantum States

### Qubit Basics
```qsharp
// Qubit allocation and basic operations
operation QubitBasics() : Unit {
    using (q = Qubit()) {
        // Qubit starts in |0⟩ state
        Message("Qubit allocated in |0⟩ state");
        
        // Apply Hadamard gate to create superposition
        H(q);
        Message("Applied Hadamard gate - now in superposition");
        
        // Measure the qubit
        let result = M(q);
        Message($"Measurement result: {result}");
        
        // Reset the qubit
        Reset(q);
        Message("Qubit reset to |0⟩ state");
    }
}

// Multiple qubits
operation MultipleQubits() : Unit {
    using (qubits = Qubit[3]) {
        Message("Allocated 3 qubits");
        
        // Put first qubit in superposition
        H(qubits[0]);
        
        // Create entanglement
        CNOT(qubits[0], qubits[1]);
        CNOT(qubits[1], qubits[2]);
        
        // Measure all qubits
        let results = MultiM(qubits);
        Message($"Measurement results: {results}");
        
        // Reset all qubits
        ResetAll(qubits);
    }
}
```

### Quantum State Representation
```qsharp
// State vector representation
operation StateVectorDemo() : Unit {
    // |ψ⟩ = α|0⟩ + β|1⟩ where |α|² + |β|² = 1
    
    using (q = Qubit()) {
        // Initial state: |0⟩ = 1|0⟩ + 0|1⟩
        Message("Initial state: |0⟩");
        
        // After H gate: (|0⟩ + |1⟩)/√2
        H(q);
        Message("After H gate: (|0⟩ + |1⟩)/√2");
        
        // After X gate: (|1⟩ + |0⟩)/√2
        X(q);
        Message("After X gate: (|1⟩ + |0⟩)/√2");
        
        Reset(q);
    }
}

// Basis states
operation BasisStates() : Unit {
    using (q1 = Qubit(), q2 = Qubit()) {
        // Create |00⟩ state
        Message("State |00⟩");
        
        // Create |01⟩ state
        X(q2);
        Message("State |01⟩");
        
        // Create |10⟩ state
        X(q1);
        Reset(q2);
        X(q1);
        Message("State |10⟩");
        
        // Create |11⟩ state
        X(q2);
        Message("State |11⟩");
        
        ResetAll([q1, q2]);
    }
}
```

## Quantum Gates

### Single-Qubit Gates
```qsharp
// Pauli gates
operation PauliGates() : Unit {
    using (q = Qubit()) {
        // Pauli-X gate (NOT gate)
        X(q);
        Message("Applied X gate (|0⟩ → |1⟩)");
        Reset(q);
        
        // Pauli-Y gate
        Y(q);
        Message("Applied Y gate");
        Reset(q);
        
        // Pauli-Z gate
        Z(q);
        Message("Applied Z gate");
        Reset(q);
    }
}

// Hadamard gate
operation HadamardGate() : Unit {
    using (q = Qubit()) {
        // H|0⟩ = (|0⟩ + |1⟩)/√2
        H(q);
        Message("Applied H gate - created superposition");
        
        let result = M(q);
        Message($"Measurement: {result}");
        
        Reset(q);
    }
}

// Phase gates
operation PhaseGates() : Unit {
    using (q = Qubit()) {
        // S gate (phase gate)
        S(q);
        Message("Applied S gate (π/2 phase)");
        
        // T gate (π/4 phase)
        T(q);
        Message("Applied T gate (π/4 phase)");
        
        Reset(q);
    }
}

// Rotation gates
operation RotationGates() : Unit {
    using (q = Qubit()) {
        // Rx rotation
        Rx(0.5, q);
        Message("Applied Rx(0.5) rotation");
        
        // Ry rotation
        Ry(0.5, q);
        Message("Applied Ry(0.5) rotation");
        
        // Rz rotation
        Rz(0.5, q);
        Message("Applied Rz(0.5) rotation");
        
        Reset(q);
    }
}
```

### Multi-Qubit Gates
```qsharp
// CNOT gate
operation CNOTGate() : Unit {
    using (control = Qubit(), target = Qubit()) {
        // Create |+⟩ state on control
        H(control);
        
        // Apply CNOT
        CNOT(control, target);
        
        // Measure both qubits
        let controlResult = M(control);
        let targetResult = M(target);
        
        Message($"CNOT results: Control={controlResult}, Target={targetResult}");
        
        ResetAll([control, target]);
    }
}

// CZ gate
operation CZGate() : Unit {
    using (control = Qubit(), target = Qubit()) {
        // Create superposition on control
        H(control);
        
        // Apply CZ
        CZ(control, target);
        
        // Measure
        let results = MultiM([control, target]);
        Message($"CZ results: {results}");
        
        ResetAll([control, target]);
    }
}

// SWAP gate
operation SWAPGate() : Unit {
    using (q1 = Qubit(), q2 = Qubit()) {
        // Set initial states
        X(q1); // |1⟩
        Message("Initial: q1=|1⟩, q2=|0⟩");
        
        // Apply SWAP
        SWAP(q1, q2);
        
        // Measure
        let results = MultiM([q1, q2]);
        Message($"After SWAP: {results}");
        
        ResetAll([q1, q2]);
    }
}

// Toffoli gate (CCNOT)
operation ToffoliGate() : Unit {
    using (control1 = Qubit(), control2 = Qubit(), target = Qubit()) {
        // Set control states
        X(control1);
        X(control2);
        
        // Apply Toffoli
        CCNOT(control1, control2, target);
        
        // Measure
        let results = MultiM([control1, control2, target]);
        Message($"Toffoli results: {results}");
        
        ResetAll([control1, control2, target]);
    }
}
```

## Superposition and Entanglement

### Creating Superposition
```qsharp
// Creating superposition states
operation CreateSuperposition() : Unit {
    using (q = Qubit()) {
        // Equal superposition
        H(q);
        Message("Created |+⟩ = (|0⟩ + |1⟩)/√2");
        
        // Measure multiple times to see probability
        for i in 0..10 {
            let result = M(q);
            Message($"Measurement {i}: {result}");
            
            // Recreate superposition
            H(q);
        }
        
        Reset(q);
    }
}

// Unequal superposition
operation UnequalSuperposition() : Unit {
    using (q = Qubit()) {
        // Create unequal superposition using rotation
        Ry(0.7853981633974483, q); // π/4 rotation
        Message("Created unequal superposition");
        
        let result = M(q);
        Message($"Measurement: {result}");
        
        Reset(q);
    }
}

// Multiple qubit superposition
operation MultipleSuperposition() : Unit {
    using (qubits = Qubit[2]) {
        // Create |++⟩ state
        ApplyToEach(H, qubits);
        Message("Created |++⟩ = (|00⟩ + |01⟩ + |10⟩ + |11⟩)/2");
        
        let results = MultiM(qubits);
        Message($"Measurement: {results}");
        
        ResetAll(qubits);
    }
}
```

### Creating Entanglement
```qsharp
// Bell states
operation CreateBellState() : Unit {
    using (qubits = Qubit[2]) {
        // Create |00⟩ state
        Message("Starting with |00⟩");
        
        // Create Bell state |Φ+⟩ = (|00⟩ + |11⟩)/√2
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        Message("Created Bell state |Φ+⟩ = (|00⟩ + |11⟩)/√2");
        
        // Measure multiple times
        for i in 0..5 {
            let results = MultiM(qubits);
            Message($"Measurement {i}: {results}");
            
            // Recreate Bell state
            ResetAll(qubits);
            using (newQubits = Qubit[2]) {
                H(newQubits[0]);
                CNOT(newQubits[0], newQubits[1]);
                // Copy state back (conceptually)
                for j in 0..2 {
                    if (results[j] == One) {
                        X(newQubits[j]);
                    }
                }
                set qubits = newQubits;
            }
        }
        
        ResetAll(qubits);
    }
}

// GHZ state
operation CreateGHZState() : Unit {
    using (qubits = Qubit[3]) {
        // Create |000⟩ state
        Message("Starting with |000⟩");
        
        // Create GHZ state |GHZ⟩ = (|000⟩ + |111⟩)/√2
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        CNOT(qubits[1], qubits[2]);
        
        Message("Created GHZ state |GHZ⟩ = (|000⟩ + |111⟩)/√2");
        
        let results = MultiM(qubits);
        Message($"GHZ measurement: {results}");
        
        ResetAll(qubits);
    }
}

// W state
operation CreateWState() : Unit {
    using (qubits = Qubit[3]) {
        // Create W state |W⟩ = (|100⟩ + |010⟩ + |001⟩)/√3
        Message("Creating W state");
        
        // Simplified W state preparation
        H(qubits[0]);
        H(qubits[1]);
        H(qubits[2]);
        
        // Apply phase to create W state
        Rz(2.0943951023931953, qubits[0]); // 2π/3
        Rz(4.1887902047863905, qubits[1]); // 4π/3
        
        let results = MultiM(qubits);
        Message($"W state measurement: {results}");
        
        ResetAll(qubits);
    }
}
```

## Measurement

### Basis Measurement
```qsharp
// Computational basis measurement
operation ComputationalBasisMeasurement() : Unit {
    using (q = Qubit()) {
        // Prepare superposition
        H(q);
        
        // Measure in computational basis
        let result = M(q);
        Message($"Computational basis measurement: {result}");
        
        Reset(q);
    }
}

// Alternative basis measurement
operation AlternativeBasisMeasurement() : Unit {
    using (q = Qubit()) {
        // Prepare |0⟩ state
        Message("Starting in |0⟩");
        
        // Measure in X basis
        H(q);
        let result = M(q);
        H(q);
        
        Message($"X basis measurement: {result}");
        
        Reset(q);
    }
}

// Y basis measurement
operation YBasisMeasurement() : Unit {
    using (q = Qubit()) {
        // Prepare |0⟩ state
        Message("Starting in |0⟩");
        
        // Measure in Y basis
        Adjoint S(q);
        H(q);
        let result = M(q);
        H(q);
        S(q);
        
        Message($"Y basis measurement: {result}");
        
        Reset(q);
    }
}
```

### Multiple Qubit Measurement
```qsharp
// Joint measurement
operation JointMeasurement() : Unit {
    using (qubits = Qubit[2]) {
        // Create Bell state
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        // Joint measurement
        let results = MultiM(qubits);
        Message($"Joint measurement: {results}");
        
        ResetAll(qubits);
    }
}

// Sequential measurement
operation SequentialMeasurement() : Unit {
    using (qubits = Qubit[2]) {
        // Create entangled state
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        // Measure sequentially
        let result1 = M(qubits[0]);
        let result2 = M(qubits[1]);
        
        Message($"Sequential measurement: {result1}, {result2}");
        
        ResetAll(qubits);
    }
}
```

## Quantum Algorithms Basics

### Quantum Random Number Generation
```qsharp
// Quantum random number generator
operation QuantumRandomNumber() : Unit {
    using (q = Qubit()) {
        // Create superposition
        H(q);
        
        // Measure to get random bit
        let bit = M(q);
        
        Message($"Random bit: {bit}");
        
        Reset(q);
    }
}

// Multi-bit random number
operation QuantumRandomNumberBits(bits : Int) : Unit {
    using (qubits = Qubit[bits]) {
        // Put all qubits in superposition
        ApplyToEach(H, qubits);
        
        // Measure all qubits
        let results = MultiM(qubits);
        
        // Convert to integer
        mutable randomNumber = 0;
        for i in 0..bits {
            if (results[i] == One) {
                set randomNumber += 1 <<< i;
            }
        }
        
        Message($"Random number ({bits} bits): {randomNumber}");
        
        ResetAll(qubits);
    }
}
```

### Quantum Teleportation
```qsharp
// Quantum teleportation protocol
operation TeleportationProtocol() : Unit {
    // Alice's qubit to teleport, entangled pair, Bob's qubit
    using (msg = Qubit(), entangled = Qubit[2]) {
        // Create entangled pair
        H(entangled[0]);
        CNOT(entangled[0], entangled[1]);
        
        // Alice's operations
        CNOT(msg, entangled[0]);
        H(msg);
        
        // Alice's measurements
        let msgResult = M(msg);
        let aliceResult = M(entangled[0]);
        
        Message($"Alice's measurements: msg={msgResult}, alice={aliceResult}");
        
        // Bob's corrections
        if (aliceResult == One) {
            Z(entangled[1]);
        }
        
        if (msgResult == One) {
            X(entangled[1]);
        }
        
        // Verify teleportation
        let bobResult = M(entangled[1]);
        Message($"Bob's measurement: {bobResult}");
        
        ResetAll([msg] + entangled);
    }
}
```

### Superdense Coding
```qsharp
// Superdense coding (sending 2 classical bits with 1 qubit)
operation SuperdenseCoding() : Unit {
    using (aliceQubit = Qubit(), bobQubit = Qubit()) {
        // Create entangled pair
        H(aliceQubit);
        CNOT(aliceQubit, bobQubit);
        
        // Alice wants to send bits "10"
        // Apply X gate (bit pattern 01)
        X(aliceQubit);
        
        // Alice sends her qubit to Bob
        // Bob applies CNOT and H
        CNOT(aliceQubit, bobQubit);
        H(aliceQubit);
        
        // Bob measures both qubits
        let results = MultiM([aliceQubit, bobQubit]);
        Message($"Superdense coding results: {results}");
        
        ResetAll([aliceQubit, bobQubit]);
    }
}
```

## Quantum Phenomena

### Quantum Interference
```qsharp
// Quantum interference demonstration
operation InterferenceDemo() : Unit {
    using (q = Qubit()) {
        // Create superposition
        H(q);
        Message("Created superposition");
        
        // Apply H again - constructive interference
        H(q);
        let result1 = M(q);
        Message($"After H-H: {result1} (should be 0)");
        
        Reset(q);
        
        // Create superposition
        H(q);
        
        // Apply Z then H - destructive interference
        Z(q);
        H(q);
        let result2 = M(q);
        Message($"After H-Z-H: {result2} (should be 1)");
        
        Reset(q);
    }
}

// Phase interference
operation PhaseInterference() : Unit {
    using (q = Qubit()) {
        // Create superposition
        H(q);
        
        // Apply phase
        Rz(3.141592653589793, q); // π phase
        
        // Apply H again
        H(q);
        
        let result = M(q);
        Message($"Phase interference result: {result}");
        
        Reset(q);
    }
}
```

### No-Cloning Theorem
```qsharp
// Demonstration of no-cloning theorem
operation NoCloningTheorem() : Unit {
    using (original = Qubit(), copy = Qubit()) {
        // Prepare original state
        H(original);
        
        Message("Original state prepared");
        
        // Attempt to clone (this is not possible in quantum mechanics)
        // We can only create entangled states
        
        // Create entangled state instead
        H(copy);
        CNOT(original, copy);
        
        // Measure both
        let results = MultiM([original, copy]);
        Message($"Entangled measurement: {results}");
        
        ResetAll([original, copy]);
    }
}
```

### Quantum Decoherence
```qsharp
// Conceptual demonstration of decoherence
operation DecoherenceConcept() : Unit {
    using (q = Qubit()) {
        // Create superposition
        H(q);
        Message("Created superposition state");
        
        // In real quantum systems, decoherence causes loss of quantum coherence
        // This is simulated by measurement or environmental interaction
        
        // Simulate decoherence by measuring (in real systems, this happens naturally)
        let result = M(q);
        Message($"After decoherence (measurement): {result}");
        
        // The qubit is now in a classical state
        Reset(q);
    }
}
```

## Best Practices

### Qubit Management
```qsharp
// Proper qubit resource management
operation ProperQubitManagement() : Unit {
    // Use 'using' blocks for automatic cleanup
    using (qubits = Qubit[2]) {
        // Use qubits
        ApplyToEach(H, qubits);
        
        // Measure
        let results = MultiM(qubits);
        Message($"Results: {results}");
        
        // Reset is automatic
    }
}

// Manual qubit management
operation ManualQubitManagement() : Unit {
    let qubits = Qubit[2];
    
    try {
        // Use qubits
        ApplyToEach(H, qubits);
        
        // Measure
        let results = MultiM(qubits);
        Message($"Results: {results}");
    } finally {
        // Always reset qubits
        ResetAll(qubits);
    }
}
```

### Quantum Algorithm Design
```qsharp
// Well-structured quantum operation
operation WellStructuredAlgorithm(input : Int) : Int {
    // Input validation
    if (input < 0) {
        Message("Input must be non-negative");
        return 0;
    }
    
    // Quantum computation
    using (q = Qubit()) {
        // Prepare state
        H(q);
        
        // Apply algorithm
        for i in 0..input {
            Rz(0.1, q);
        }
        
        // Measure
        let result = M(q);
        
        // Post-processing
        if (result == One) {
            return 1;
        } else {
            return 0;
        }
    }
}
```

## Common Pitfalls

### Common Quantum Programming Errors
```qsharp
// Error: Not resetting qubits
operation BadResourceManagement() : Unit {
    let q = Qubit();
    X(q);
    let result = M(q);
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

// Error: Using qubits after measurement
operation PostMeasurementError() : Unit {
    using (q = Qubit()) {
        H(q);
        let result = M(q);
        
        // Error: q is now classical
        H(q); // This has no effect
    }
}

// Error: Ignoring entanglement
operation EntanglementError() : Unit {
    using (qubits = Qubit[2]) {
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        // Error: Treating qubits as independent
        let result1 = M(qubits[0]);
        let result2 = M(qubits[1]);
        
        // These results are correlated!
        Message($"Results: {result1}, {result2}");
    }
}
```

## Summary

Q# quantum concepts provide:

**Fundamental Concepts:**
- Qubits and quantum states
- Superposition and entanglement
- Measurement and basis states
- Quantum interference

**Quantum Gates:**
- Single-qubit gates (Pauli, Hadamard, Phase, Rotation)
- Multi-qubit gates (CNOT, CZ, SWAP, Toffoli)
- Adjoint and controlled operations

**Quantum Algorithms:**
- Random number generation
- Teleportation protocol
- Superdense coding
- Basic interference patterns

**Quantum Phenomena:**
- Quantum interference
- No-cloning theorem
- Quantum decoherence
- Measurement collapse

**Best Practices:**
- Proper qubit resource management
- Input validation
- Error handling
- Algorithm structure

**Common Pitfalls:**
- Resource leaks (not resetting qubits)
- Post-measurement errors
- Ignoring entanglement
- Type mismatches

Q# provides a powerful framework for quantum programming with built-in support for quantum concepts like superposition, entanglement, and measurement. Understanding these concepts is essential for developing quantum algorithms and applications.
