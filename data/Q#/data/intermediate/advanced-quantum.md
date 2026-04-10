# Q# Advanced Quantum Concepts

## Quantum Entanglement

### Bell States
```qsharp
// Create all four Bell states
operation CreateBellStates() : Unit {
    // Bell state |Φ+⟩ = (|00⟩ + |11⟩)/√2
    operation CreatePhiPlus(q1 : Qubit, q2 : Qubit) : Unit is Adj+Ctl {
        H(q1);
        CNOT(q1, q2);
    }
    
    // Bell state |Φ-⟩ = (|00⟩ - |11⟩)/√2
    operation CreatePhiMinus(q1 : Qubit, q2 : Qubit) : Unit is Adj+Ctl {
        H(q1);
        CNOT(q1, q2);
        Z(q1);
    }
    
    // Bell state |Ψ+⟩ = (|01⟩ + |10⟩)/√2
    operation CreatePsiPlus(q1 : Qubit, q2 : Qubit) : Unit is Adj+Ctl {
        H(q1);
        CNOT(q1, q2);
        X(q2);
    }
    
    // Bell state |Ψ-⟩ = (|01⟩ - |10⟩)/√2
    operation CreatePsiMinus(q1 : Qubit, q2 : Qubit) : Unit is Adj+Ctl {
        H(q1);
        CNOT(q1, q2);
        X(q2);
        Z(q1);
    }
    
    // Test all Bell states
    using (qubits = Qubit[2]) {
        // Test |Φ+⟩
        CreatePhiPlus(qubits[0], qubits[1]);
        let results = MultiM(qubits);
        Message($"|Φ+⟩ measurement: {results}");
        ResetAll(qubits);
        
        // Test |Φ-⟩
        CreatePhiMinus(qubits[0], qubits[1]);
        let results = MultiM(qubits);
        Message($"|Φ-⟩ measurement: {results}");
        ResetAll(qubits);
        
        // Test |Ψ+⟩
        CreatePsiPlus(qubits[0], qubits[1]);
        let results = MultiM(qubits);
        Message($"|Ψ+⟩ measurement: {results}");
        ResetAll(qubits);
        
        // Test |Ψ-⟩
        CreatePsiMinus(qubits[0], qubits[1]);
        let results = MultiM(qubits);
        Message($"|Ψ-⟩ measurement: {results}");
        ResetAll(qubits);
    }
}

// Bell state measurement
operation MeasureBellState(q1 : Qubit, q2 : Qubit) : String {
    // Apply inverse Bell state creation circuit
    CNOT(q1, q2);
    H(q1);
    
    // Measure in computational basis
    let results = MultiM([q1, q2]);
    
    // Determine which Bell state
    if (results == [Zero, Zero]) {
        return "|Φ+⟩";
    } elif (results == [Zero, One]) {
        return "|Ψ+⟩";
    } elif (results == [One, Zero]) {
        return "|Ψ-⟩";
    } else {
        return "|Φ-⟩";
    }
}

// Test Bell state measurement
operation TestBellMeasurement() : Unit {
    using (qubits = Qubit[2]) {
        // Create |Φ+⟩ and measure
        CreatePhiPlus(qubits[0], qubits[1]);
        let measured = MeasureBellState(qubits[0], qubits[1]);
        Message($"Created |Φ+⟩, measured: {measured}");
        ResetAll(qubits);
        
        // Create |Ψ+⟩ and measure
        CreatePsiPlus(qubits[0], qubits[1]);
        let measured = MeasureBellState(qubits[0], qubits[1]);
        Message($"Created |Ψ+⟩, measured: {measured}");
        ResetAll(qubits);
    }
}
```

### GHZ States
```qsharp
// Create GHZ states for multiple qubits
operation CreateGHZState(nQubits : Int) : Unit {
    using (qubits = Qubit[nQubits]) {
        // Start with |0⟩ state
        Message($"Creating {nQubits}-qubit GHZ state");
        
        // Create superposition on first qubit
        H(qubits[0]);
        Message("Applied H to first qubit");
        
        // Create entanglement
        for i in 1..nQubits-1 {
            CNOT(qubits[0], qubits[i]);
        }
        Message("Created entanglement chain");
        
        // Measure GHZ state
        let results = MultiM(qubits);
        Message($"GHZ measurement: {results}");
        
        // Verify GHZ properties
        let zeroCount = CountZeros(results);
        let oneCount = nQubits - zeroCount;
        
        Message($"|0⟩ count: {zeroCount}, |1⟩ count: {oneCount}");
        
        // GHZ state should have all qubits the same
        if (zeroCount == 0 || oneCount == 0) {
            Message("GHZ state property verified: All qubits same");
        } else {
            Message("GHZ state property violated: Mixed states");
        }
        
        ResetAll(qubits);
    }
}

// Count zeros in measurement results
operation CountZeros(results : Int[]) : Int {
    mutable count = 0;
    for result in results {
        if (result == Zero) {
            set count += 1;
        }
    }
    return count;
}

// W state creation
operation CreateWState(nQubits : Int) : Unit {
    using (qubits = Qubit[nQubits]) {
        Message($"Creating {nQubits}-qubit W state");
        
        // W state = (|100...0⟩ + |010...0⟩ + ... + |000...1⟩)/√n
        
        // Start with |100...0⟩
        X(qubits[0]);
        
        // Create superposition
        for i in 0..nQubits-1 {
            H(qubits[i]);
            // Apply controlled rotations to distribute amplitude
            for j in (i+1)..nQubits-1 {
                Controlled Ry(2.0 * Asin(1.0 / IntAsDouble(nQubits - i)), qubits[i], qubits[j]);
            }
        }
        
        // Measure W state
        let results = MultiM(qubits);
        Message($"W state measurement: {results}");
        
        // W state should have exactly one qubit in |1⟩ state
        let oneCount = nQubits - CountZeros(results);
        
        if (oneCount == 1) {
            Message("W state property verified: Exactly one |1⟩");
        } else {
            Message($"W state property violated: {oneCount} qubits in |1⟩");
        }
        
        ResetAll(qubits);
    }
}
```

### Multi-Particle Entanglement
```qsharp
// Create multi-particle entangled states
operation MultiParticleEntanglement() : Unit {
    // Create 4-qubit cluster state
    operation CreateClusterState(qubits : Qubit[]) : Unit {
        // Apply H to all qubits
        ApplyToEach(H, qubits);
        
        // Apply CZ gates between neighbors
        for i in 0..Length(qubits)-1 {
            CZ(qubits[i], qubits[i+1]);
        }
    }
    
    // Test cluster state
    using (qubits = Qubit[4]) {
        CreateClusterState(qubits);
        
        let results = MultiM(qubits);
        Message($"Cluster state measurement: {results}");
        
        ResetAll(qubits);
    }
    
    // Create graph state (generalization of cluster state)
    operation CreateGraphState(qubits : Qubit[], edges : (Int, Int)[]) : Unit {
        // Apply H to all qubits
        ApplyToEach(H, qubits);
        
        // Apply CZ gates according to graph edges
        for (i, j) in edges {
            CZ(qubits[i], qubits[j]);
        }
    }
    
    // Test graph state (square graph)
    using (qubits = Qubit[4]) {
        let edges = [(0, 1), (1, 2), (2, 3), (3, 0)]; // Square
        CreateGraphState(qubits, edges);
        
        let results = MultiM(qubits);
        Message($"Graph state measurement: {results}");
        
        ResetAll(qubits);
    }
}

// Entanglement swapping
operation EntanglementSwapping() : Unit {
    using (qubits = Qubit[4]) {
        // Create two Bell pairs
        CreatePhiPlus(qubits[0], qubits[1]);
        CreatePhiPlus(qubits[2], qubits[3]);
        
        Message("Created two Bell pairs");
        
        // Perform Bell measurement on middle qubits
        let bellState = MeasureBellState(qubits[1], qubits[2]);
        Message($"Bell measurement on middle qubits: {bellState}");
        
        // Measure outer qubits
        let outerResults = MultiM([qubits[0], qubits[3]]);
        Message($"Outer qubits measurement: {outerResults}");
        
        // Outer qubits should now be entangled
        ResetAll(qubits);
    }
}
```

## Quantum Error Correction

### Bit Flip Code
```qsharp
// Three-qubit bit flip code
operation BitFlipCode() : Unit {
    // Encode logical qubit
    operation EncodeBitFlip(logical : Qubit, physical : Qubit[]) : Unit is Adj+Ctl {
        // Create |000⟩ state
        // Copy logical state to physical qubits
        CNOT(logical, physical[0]);
        CNOT(logical, physical[1]);
        CNOT(logical, physical[2]);
    }
    
    // Decode logical qubit
    operation DecodeBitFlip(logical : Qubit, physical : Qubit[]) : Unit is Adj+Ctl {
        // Reverse encoding
        CNOT(logical, physical[2]);
        CNOT(logical, physical[1]);
        CNOT(logical, physical[0]);
    }
    
    // Error detection and correction
    operation CorrectBitFlip(physical : Qubit[]) : Unit {
        using (syndrome = Qubit[2]) {
            // Prepare syndrome qubits
            ApplyToEach(H, syndrome);
            
            // Syndrome measurement circuit
            CNOT(physical[0], syndrome[0]);
            CNOT(physical[1], syndrome[0]);
            CNOT(physical[1], syndrome[1]);
            CNOT(physical[2], syndrome[1]);
            
            ApplyToEach(H, syndrome);
            
            // Measure syndrome
            let syndromeResults = MultiM(syndrome);
            
            // Correct error based on syndrome
            if (syndromeResults == [One, Zero]) {
                // Error on qubit 0
                X(physical[0]);
                Message("Corrected error on qubit 0");
            } elif (syndromeResults == [One, One]) {
                // Error on qubit 1
                X(physical[1]);
                Message("Corrected error on qubit 1");
            } elif (syndromeResults == [Zero, One]) {
                // Error on qubit 2
                X(physical[2]);
                Message("Corrected error on qubit 2");
            } else {
                Message("No error detected");
            }
            
            ResetAll(syndrome);
        }
    }
    
    // Test bit flip code
    using ((logical, physical) = (Qubit(), Qubit[3])) {
        // Prepare logical state
        H(logical);
        Message("Prepared logical |+⟩ state");
        
        // Encode
        EncodeBitFlip(logical, physical);
        Message("Encoded logical qubit");
        
        // Introduce error (50% chance)
        let errorQubit = 1; // Error on middle qubit
        X(physical[errorQubit]);
        Message($"Introduced bit flip error on qubit {errorQubit}");
        
        // Correct error
        CorrectBitFlip(physical);
        
        // Decode
        DecodeBitFlip(logical, physical);
        
        // Measure
        let result = M(logical);
        Message($"Decoded result: {result}");
        
        ResetAll([logical] + physical);
    }
}
```

### Phase Flip Code
```qsharp
// Three-qubit phase flip code
operation PhaseFlipCode() : Unit {
    // Encode logical qubit
    operation EncodePhaseFlip(logical : Qubit, physical : Qubit[]) : Unit is Adj+Ctl {
        // Encode using Hadamard basis
        H(logical);
        CNOT(logical, physical[0]);
        CNOT(logical, physical[1]);
        CNOT(logical, physical[2]);
        ApplyToEach(H, physical);
    }
    
    // Decode logical qubit
    operation DecodePhaseFlip(logical : Qubit, physical : Qubit[]) : Unit is Adj+Ctl {
        // Reverse encoding
        ApplyToEach(H, physical);
        CNOT(logical, physical[2]);
        CNOT(logical, physical[1]);
        CNOT(logical, physical[0]);
        H(logical);
    }
    
    // Error detection and correction
    operation CorrectPhaseFlip(physical : Qubit[]) : Unit {
        using (syndrome = Qubit[2]) {
            // Prepare syndrome qubits
            ApplyToEach(H, syndrome);
            
            // Syndrome measurement for phase errors
            for i in 0..3 {
                CZ(physical[i], syndrome[0]);
            }
            CZ(physical[1], syndrome[1]);
            CZ(physical[2], syndrome[1]);
            
            ApplyToEach(H, syndrome);
            
            // Measure syndrome
            let syndromeResults = MultiM(syndrome);
            
            // Correct phase error based on syndrome
            if (syndromeResults == [One, Zero]) {
                // Phase error on qubit 0
                Z(physical[0]);
                Message("Corrected phase error on qubit 0");
            } elif (syndromeResults == [One, One]) {
                // Phase error on qubit 1
                Z(physical[1]);
                Message("Corrected phase error on qubit 1");
            } elif (syndromeResults == [Zero, One]) {
                // Phase error on qubit 2
                Z(physical[2]);
                Message("Corrected phase error on qubit 2");
            } else {
                Message("No phase error detected");
            }
            
            ResetAll(syndrome);
        }
    }
    
    // Test phase flip code
    using ((logical, physical) = (Qubit(), Qubit[3])) {
        // Prepare logical state
        H(logical);
        Message("Prepared logical |+⟩ state");
        
        // Encode
        EncodePhaseFlip(logical, physical);
        Message("Encoded logical qubit in phase flip code");
        
        // Introduce phase error
        Z(physical[1]);
        Message("Introduced phase flip error on qubit 1");
        
        // Correct error
        CorrectPhaseFlip(physical);
        
        // Decode
        DecodePhaseFlip(logical, physical);
        
        // Measure
        let result = M(logical);
        Message($"Decoded result: {result}");
        
        ResetAll([logical] + physical);
    }
}
```

### Shor Code
```qsharp
// Nine-qubit Shor code (simplified)
operation ShorCode() : Unit {
    // Encode logical qubit using Shor code
    operation EncodeShor(logical : Qubit, block1 : Qubit[], block2 : Qubit[], block3 : Qubit[]) : Unit is Adj+Ctl {
        // First encode using three-qubit repetition code
        CNOT(logical, block1[0]);
        CNOT(logical, block1[1]);
        CNOT(logical, block1[2]);
        
        // Then encode each qubit using phase flip code
        ApplyToEach(H, block1);
        CNOT(block1[0], block2[0]);
        CNOT(block1[0], block2[1]);
        CNOT(block1[0], block2[2]);
        
        CNOT(block1[1], block3[0]);
        CNOT(block1[1], block3[1]);
        CNOT(block1[1], block3[2]);
        
        ApplyToEach(H, block2);
        ApplyToEach(H, block3);
    }
    
    // Simplified error correction for Shor code
    operation CorrectShor(block1 : Qubit[], block2 : Qubit[], block3 : Qubit[]) : Unit {
        // Simplified: just demonstrate concept
        Message("Shor code error correction (simplified)");
        
        // In practice, would need complex syndrome extraction
        // and correction operations
    }
    
    // Test Shor code (simplified)
    using ((logical, blocks) = (Qubit(), Qubit[9])) {
        let block1 = blocks[0..2];
        let block2 = blocks[3..5];
        let block3 = blocks[6..8];
        
        // Prepare logical state
        H(logical);
        Message("Prepared logical |+⟩ state");
        
        // Encode
        EncodeShor(logical, block1, block2, block3);
        Message("Encoded using Shor code");
        
        // Introduce error (simplified)
        X(block1[0]);
        Message("Introduced bit flip error");
        
        // Correct (simplified)
        CorrectShor(block1, block2, block3);
        
        Message("Shor code test completed (simplified)");
        ResetAll([logical] + blocks);
    }
}
```

## Quantum Teleportation

### Basic Teleportation
```qsharp
// Quantum teleportation protocol
operation QuantumTeleportation() : Unit {
    // Teleport a quantum state
    operation TeleportState(message : Qubit, alice : Qubit, bob : Qubit) : Unit {
        // Create entangled pair between Alice and Bob
        H(alice);
        CNOT(alice, bob);
        
        // Alice's operations
        CNOT(message, alice);
        H(message);
        
        // Alice's measurements
        let msgResult = M(message);
        let aliceResult = M(alice);
        
        Message($"Alice's measurements: msg={msgResult}, alice={aliceResult}");
        
        // Bob's corrections
        if (aliceResult == One) {
            Z(bob);
            Message("Applied Z correction");
        }
        
        if (msgResult == One) {
            X(bob);
            Message("Applied X correction");
        }
    }
    
    // Test teleportation with known state
    using ((message, alice, bob) = (Qubit(), Qubit(), Qubit())) {
        // Prepare known state
        H(message);
        Rz(0.5, message);
        Message("Prepared message state");
        
        // Teleport
        TeleportState(message, alice, bob);
        
        // Verify teleportation
        let result = M(bob);
        Message($"Bob's measurement: {result}");
        
        ResetAll([message, alice, bob]);
    }
    
    // Test teleportation with random state
    using ((message, alice, bob) = (Qubit(), Qubit(), Qubit())) {
        // Prepare random state
        Rx(0.7, message);
        Ry(0.3, message);
        Message("Prepared random message state");
        
        // Teleport
        TeleportState(message, alice, bob);
        
        // Verify
        let result = M(bob);
        Message($"Random state teleportation result: {result}");
        
        ResetAll([message, alice, bob]);
    }
}
```

### Teleportation Gates
```qsharp
// Teleportation as quantum gate
operation TeleportationGate() : Unit {
    // Teleportation gate that can be used in circuits
    operation TeleportGate(control : Qubit, target : Qubit, ancilla : Qubit[]) : Unit {
        // Create entanglement
        H(ancilla[0]);
        CNOT(ancilla[0], ancilla[1]);
        
        // Teleport control to ancilla[1]
        CNOT(control, ancilla[0]);
        H(control);
        
        let ctrlResult = M(control);
        let ancResult = M(ancilla[0]);
        
        // Corrections
        if (ancResult == One) {
            Z(ancilla[1]);
        }
        
        if (ctrlResult == One) {
            X(ancilla[1]);
        }
        
        // Now apply controlled operation
        CNOT(ancilla[1], target);
        
        // Teleport back
        CNOT(ancilla[1], ancilla[0]);
        H(ancilla[1]);
        
        let backResult = M(ancilla[1]);
        let backAncResult = M(ancilla[0]);
        
        // Corrections
        if (backAncResult == One) {
            Z(control);
        }
        
        if (backResult == One) {
            X(control);
        }
    }
    
    // Test teleportation gate
    using ((control, target, ancilla) = (Qubit(), Qubit(), Qubit[2])) {
        // Prepare control state
        H(control);
        Message("Prepared control state");
        
        // Apply teleportation gate
        TeleportGate(control, target, ancilla);
        
        // Measure results
        let results = MultiM([control, target]);
        Message($"Teleportation gate results: {results}");
        
        ResetAll([control, target] + ancilla);
    }
}
```

### Entanglement Swapping
```qsharp
// Entanglement swapping for quantum networks
operation EntanglementSwapping() : Unit {
    // Create entanglement between distant nodes
    operation CreateEntanglement(node1 : Qubit, node2 : Qubit) : Unit {
        H(node1);
        CNOT(node1, node2);
    }
    
    // Bell measurement for entanglement swapping
    operation BellMeasurement(q1 : Qubit, q2 : Qubit) : String {
        CNOT(q1, q2);
        H(q1);
        
        let results = MultiM([q1, q2]);
        
        if (results == [Zero, Zero]) {
            return "|Φ+⟩";
        } elif (results == [Zero, One]) {
            return "|Ψ+⟩";
        } elif (results == [One, Zero]) {
            return "|Ψ-⟩";
        } else {
            return "|Φ-⟩";
        }
    }
    
    // Test entanglement swapping
    using (nodes = Qubit[4]) {
        // Create two entangled pairs
        CreateEntanglement(nodes[0], nodes[1]);
        CreateEntanglement(nodes[2], nodes[3]);
        
        Message("Created two entangled pairs");
        
        // Perform Bell measurement on middle nodes
        let bellState = BellMeasurement(nodes[1], nodes[2]);
        Message($"Bell measurement: {bellState}");
        
        // Measure outer nodes
        let outerResults = MultiM([nodes[0], nodes[3]]);
        Message($"Outer nodes measurement: {outerResults}");
        
        // Outer nodes should now be entangled
        ResetAll(nodes);
    }
}
```

## Superdense Coding

### Basic Superdense Coding
```qsharp
// Superdense coding protocol
operation SuperdenseCoding() : Unit {
    // Encode 2 classical bits into 1 qubit
    operation EncodeBits(bits : Int[], qubit : Qubit) : Unit {
        // bits[0] is the first bit, bits[1] is the second bit
        
        if (bits[1] == One) {
            X(qubit); // Apply X if second bit is 1
        }
        
        if (bits[0] == One) {
            Z(qubit); // Apply Z if first bit is 1
        }
    }
    
    // Decode 2 bits from 1 qubit
    operation DecodeBits(qubit1 : Qubit, qubit2 : Qubit) : Int[] {
        // Apply Bell measurement
        CNOT(qubit1, qubit2);
        H(qubit1);
        
        let results = MultiM([qubit1, qubit2]);
        
        // Convert measurement to bits
        if (results == [Zero, Zero]) {
            return [Zero, Zero]; // 00
        } elif (results == [Zero, One]) {
            return [Zero, One]; // 01
        } elif (results == [One, Zero]) {
            return [One, Zero]; // 10
        } else {
            return [One, One]; // 11
        }
    }
    
    // Test superdense coding
    using ((aliceQubit, bobQubit) = (Qubit(), Qubit())) {
        // Create entangled pair
        H(aliceQubit);
        CNOT(aliceQubit, bobQubit);
        Message("Created entangled pair");
        
        // Alice wants to send bits "10"
        let bits = [One, Zero];
        EncodeBits(bits, aliceQubit);
        Message($"Alice encoded bits: {bits}");
        
        // Alice sends her qubit to Bob
        // Bob performs Bell measurement
        let decodedBits = DecodeBits(aliceQubit, bobQubit);
        Message($"Bob decoded bits: {decodedBits}");
        
        ResetAll([aliceQubit, bobQubit]);
    }
}
```

### Advanced Superdense Coding
```qsharp
// Multi-bit superdense coding
operation MultiBitSuperdenseCoding() : Unit {
    // Send multiple bit pairs using multiple entangled pairs
    operation SendMultipleBits(bitPairs : Int[][]) : Unit {
        let nPairs = Length(bitPairs);
        using (aliceQubits = Qubit[nPairs], bobQubits = Qubit[nPairs]) {
            // Create entangled pairs
            for i in 0..nPairs {
                H(aliceQubits[i]);
                CNOT(aliceQubits[i], bobQubits[i]);
            }
            
            // Encode all bit pairs
            for i in 0..nPairs {
                EncodeBits(bitPairs[i], aliceQubits[i]);
            }
            
            // Decode all bit pairs
            for i in 0..nPairs {
                let decoded = DecodeBits(aliceQubits[i], bobQubits[i]);
                Message($"Pair {i}: Sent {bitPairs[i]}, Received {decoded}");
            }
            
            ResetAll(aliceQubits + bobQubits);
        }
    }
    
    // Test multi-bit superdense coding
    let bitPairs = [[One, Zero], [Zero, One], [One, One]];
    SendMultipleBits(bitPairs);
}
```

## Quantum Cryptography

### BB84 Protocol
```qsharp
// BB84 quantum key distribution protocol
operation BB84Protocol() : Unit {
    // Generate random bit
    operation RandomBit() : Bool {
        using (q = Qubit()) {
            H(q);
            let result = M(q);
            Reset(q);
            return result == One;
        }
    }
    
    // Generate random basis (0 = Z, 1 = X)
    operation RandomBasis() : Bool {
        return RandomBit();
    }
    
    // Prepare qubit in specified basis and state
    operation PrepareQubit(bit : Bool, basis : Bool, q : Qubit) : Unit {
        if (basis == Zero) {
            // Z basis
            if (bit == One) {
                X(q);
            }
        } else {
            // X basis
            H(q);
            if (bit == One) {
                X(q);
            }
            H(q);
        }
    }
    
    // Measure qubit in specified basis
    operation MeasureQubit(basis : Bool, q : Qubit) : Bool {
        if (basis == One) {
            H(q);
        }
        
        let result = M(q);
        
        if (basis == One) {
            H(q);
        }
        
        return result == One;
    }
    
    // Simulate BB84 protocol
    operation SimulateBB84(nBits : Int) : Unit {
        Message($"Simulating BB84 with {nBits} bits");
        
        // Alice generates random bits and bases
        mutable aliceBits = new Bool[nBits];
        mutable aliceBases = new Bool[nBits];
        
        for i in 0..nBits {
            set aliceBits w/= RandomBit();
            set aliceBases w/= RandomBasis();
        }
        
        Message("Alice generated bits and bases");
        
        // Bob generates random bases
        mutable bobBases = new Bool[nBits];
        for i in 0..nBits {
            set bobBases w/= RandomBasis();
        }
        
        // Alice sends qubits to Bob
        using (qubits = Qubit[nBits]) {
            // Alice prepares qubits
            for i in 0..nBits {
                PrepareQubit(aliceBits[i], aliceBases[i], qubits[i]);
            }
            
            // Bob measures qubits
            mutable bobBits = new Bool[nBits];
            for i in 0..nBits {
                set bobBits w/= MeasureQubit(bobBases[i], qubits[i]);
            }
            
            // Basis reconciliation
            mutable keyBits = new Bool[0];
            for i in 0..nBits {
                if (aliceBases[i] == bobBases[i]) {
                    // Same basis - keep bit
                    set keyBits += [aliceBits[i]];
                    Message($"Bit {i}: Kept (basis {aliceBases[i]})");
                } else {
                    // Different basis - discard bit
                    Message($"Bit {i}: Discarded (Alice basis {aliceBases[i]}, Bob basis {bobBases[i]})");
                }
            }
            
            Message($"Final key length: {Length(keyBits)} bits");
            Message($"Key: {keyBits}");
            
            ResetAll(qubits);
        }
    }
    
    // Run BB84 simulation
    SimulateBB84(10);
}
```

### E91 Protocol
```qsharp
// E91 protocol (Ekert 1991) using entanglement
operation E91Protocol() : Unit {
    // Create Bell state
    operation CreateBellState(q1 : Qubit, q2 : Qubit) : Unit {
        H(q1);
        CNOT(q1, q2);
    }
    
    // Measure in specified basis
    operation MeasureInBasis(basis : Int, q : Qubit) : Bool {
        // Basis 0: Z basis
        // Basis 1: X basis
        // Basis 2: Y basis
        
        if (basis == 1) {
            H(q);
        } elif (basis == 2) {
            Adjoint S(q);
            H(q);
        }
        
        let result = M(q);
        
        if (basis == 1) {
            H(q);
        } elif (basis == 2) {
            H(q);
            S(q);
        }
        
        return result == One;
    }
    
    // Simulate E91 protocol
    operation SimulateE91(nPairs : Int) : Unit {
        Message($"Simulating E91 with {nPairs} entangled pairs");
        
        using (aliceQubits = Qubit[nPairs], bobQubits = Qubit[nPairs]) {
            // Create entangled pairs
            for i in 0..nPairs {
                CreateBellState(aliceQubits[i], bobQubits[i]);
            }
            
            // Random basis choices
            mutable aliceBases = new Int[nPairs];
            mutable bobBases = new Int[nPairs];
            
            for i in 0..nPairs {
                set aliceBases w/= RandomInt() % 3;
                set bobBases w/= RandomInt() % 3;
            }
            
            // Measure in chosen bases
            mutable aliceResults = new Bool[nPairs];
            mutable bobResults = new Bool[nPairs];
            
            for i in 0..nPairs {
                set aliceResults w/= MeasureInBasis(aliceBases[i], aliceQubits[i]);
                set bobResults w/= MeasureInBasis(bobBases[i], bobQubits[i]);
            }
            
            // Basis reconciliation and key generation
            mutable keyBits = new Bool[0];
            mutable testBits = new Bool[0];
            
            for i in 0..nPairs {
                if (aliceBases[i] == bobBases[i]) {
                    // Same basis - use for key
                    set keyBits += [aliceResults[i]];
                    Message($"Pair {i}: Key bit {aliceResults[i]} (basis {aliceBases[i]})");
                } elif (aliceBases[i] == 0 && bobBases[i] == 1) {
                    // Different bases - use for testing
                    set testBits += [aliceResults[i]];
                    Message($"Pair {i}: Test bit {aliceResults[i]}");
                }
            }
            
            Message($"Key length: {Length(keyBits)} bits");
            Message($"Test bits: {Length(testBits)}");
            
            ResetAll(aliceQubits + bobQubits);
        }
    }
    
    // Run E91 simulation
    SimulateE91(10);
}
```

## Best Practices

### Entanglement Best Practices
```qsharp
// Proper entanglement creation
operation ProperEntanglement() : Unit {
    using (qubits = Qubit[2]) {
        // Good: Clear entanglement creation
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        // Verify entanglement
        let results = MultiM(qubits);
        Message($"Entangled state: {results}");
        
        ResetAll(qubits);
    }
}

// Resource-efficient entanglement
operation EfficientEntanglement() : Unit {
    // Reuse qubits when possible
    using (qubits = Qubit[4]) {
        // Create first Bell pair
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        // Use remaining qubits for second pair
        H(qubits[2]);
        CNOT(qubits[2], qubits[3]);
        
        // Measure both pairs
        let results1 = MultiM([qubits[0], qubits[1]]);
        let results2 = MultiM([qubits[2], qubits[3]]);
        
        Message($"Bell pair 1: {results1}");
        Message($"Bell pair 2: {results2}");
        
        ResetAll(qubits);
    }
}
```

### Error Correction Best Practices
```qsharp
// Robust error correction
operation RobustErrorCorrection() : Unit {
    // Always validate syndrome measurements
    operation ValidateSyndrome(syndrome : Int[]) : Bool {
        // Check if syndrome is valid
        return Length(syndrome) == 2 && 
               (syndrome[0] == 0 || syndrome[0] == 1) &&
               (syndrome[1] == 0 || syndrome[1] == 1);
    }
    
    // Test validation
    let validSyndrome = [0, 1];
    let invalidSyndrome = [2, 3];
    
    Message($"Valid syndrome: {ValidateSyndrome(validSyndrome)}");
    Message($"Invalid syndrome: {ValidateSyndrome(invalidSyndrome)}");
}
```

## Common Pitfalls

### Common Entanglement Errors
```qsharp
// Error: Not measuring entangled qubits correctly
operation BadEntanglementMeasurement() : Unit {
    using (qubits = Qubit[2]) {
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        // Bad: Measuring qubits individually without considering correlation
        let result1 = M(qubits[0]);
        let result2 = M(qubits[1]);
        
        // Good: Measure and analyze correlation
        let results = MultiM(qubits);
        if (results[0] == results[1]) {
            Message("Entangled qubits correlated");
        }
        
        ResetAll(qubits);
    }
}

// Error: Not resetting qubits after entanglement
operation BadEntanglementCleanup() : Unit {
    using (qubits = Qubit[2]) {
        H(qubits[0]);
        CNOT(qubits[0], qubits[1]);
        
        let results = MultiM(qubits);
        
        // Bad: Forgetting to reset qubits
        // ResetAll(qubits); // This is needed!
    }
}
```

## Summary

Q# advanced quantum concepts provide:

**Quantum Entanglement:**
- Bell states (|Φ+⟩, |Φ-⟩, |Ψ+⟩, |Ψ-⟩)
- GHZ states for multi-qubit entanglement
- W states and cluster states
- Entanglement swapping

**Quantum Error Correction:**
- Bit flip code (3-qubit repetition)
- Phase flip code
- Shor code (9-qubit code)
- Syndrome measurement and correction

**Quantum Teleportation:**
- Basic teleportation protocol
- Teleportation gates
- Entanglement swapping
- Quantum networks

**Superdense Coding:**
- Basic superdense coding
- Multi-bit superdense coding
- Classical bit encoding/decoding
- Bandwidth efficiency

**Quantum Cryptography:**
- BB84 key distribution protocol
- E91 entanglement-based protocol
- Quantum key generation
- Basis reconciliation

**Best Practices:**
- Proper entanglement creation
- Resource-efficient operations
- Robust error correction
- Syndrome validation

**Common Pitfalls:**
- Incorrect entanglement measurement
- Improper qubit cleanup
- Syndrome measurement errors
- Basis choice mistakes

These advanced concepts form the foundation for practical quantum computing applications, including quantum communication, error correction, and quantum algorithms.
