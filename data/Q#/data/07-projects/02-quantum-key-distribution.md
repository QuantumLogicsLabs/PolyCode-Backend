# Project 02 — BB84 Quantum Key Distribution

## Overview

Implement the **BB84 quantum key distribution protocol** — the first quantum cryptography protocol, invented by Bennett and Brassard in 1984.

BB84 allows two parties (Alice and Bob) to establish a **provably secure shared key** over an insecure channel. Any eavesdropping attempt is detectable.

**Difficulty:** 🟡 Intermediate  
**Estimated time:** 3–4 hours

---

## Protocol Summary

```
Alice                              Bob
  │                                 │
  │ 1. Generate random bits         │
  │    + random bases                │
  │                                 │
  │──── Send qubits ───────────────▶│ 2. Measure in random bases
  │                                 │
  │◀─── Announce bases (classical)──┤ 3. Bob announces his bases
  │                                 │
  │──── Announce bases (classical)─▶│ 4. Keep only matching bases
  │                                 │
  │    Sift key (same basis)         │
  │                                 │
  │ 5. Sample subset for error check │
  │◀─────────────── Compare ────────▶│
  │                                 │
  │ 6. Error ≤ threshold?            │
  │    → Key is secure!             │
  │    Error > threshold?           │
  │    → Eavesdropper detected!     │
```

---

## The Two Bases

Alice encodes bits in one of two bases:
- **Rectilinear (+) basis**: `|0⟩` → bit 0, `|1⟩` → bit 1
- **Diagonal (×) basis**: `|+⟩` → bit 0, `|-⟩` → bit 1

```qsharp
operation PrepareQubit(bit : Int, basis : Int) : Qubit {
    use q = Qubit();
    
    // Encode bit
    if bit == 1 { X(q); }
    
    // Apply basis transformation
    if basis == 1 { H(q); }  // Diagonal basis
    
    return q;
}

operation MeasureQubit(q : Qubit, basis : Int) : Int {
    if basis == 1 { H(q); }  // Transform back from diagonal
    return M(q) == Zero ? 0 | 1;
}
```

---

## Full Implementation

```qsharp
namespace BB84 {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Math;
    open Microsoft.Quantum.Convert;

    /// Simulate BB84 with optional eavesdropper
    operation RunBB84(
        keyLength : Int,
        withEavesdropper : Bool
    ) : (Int[], Int[]) {
        
        // Step 1: Alice generates random bits and bases
        let aliceBits  = GenerateRandomBits(keyLength);
        let aliceBases = GenerateRandomBits(keyLength);
        
        // Step 2: Prepare qubits
        use qubits = Qubit[keyLength];
        for i in 0..keyLength-1 {
            PrepareQubitInPlace(aliceBits[i], aliceBases[i], qubits[i]);
        }
        
        // Optional: Eavesdropper (Eve) intercepts and remeasures
        if withEavesdropper {
            EveIntercept(qubits);
        }
        
        // Step 3: Bob measures in random bases
        let bobBases = GenerateRandomBits(keyLength);
        mutable bobBits = [0, size = keyLength];
        for i in 0..keyLength-1 {
            set bobBits w/= i <- MeasureQubitInPlace(bobBases[i], qubits[i]);
        }
        
        ResetAll(qubits);
        
        // Step 4: Sift — keep only bits where bases match
        mutable aliceKey = [];
        mutable bobKey   = [];
        for i in 0..keyLength-1 {
            if aliceBases[i] == bobBases[i] {
                set aliceKey += [aliceBits[i]];
                set bobKey   += [bobBits[i]];
            }
        }
        
        return (aliceKey, bobKey);
    }

    operation EveIntercept(qubits : Qubit[]) : Unit {
        // Eve measures in random bases (disturbs the state!)
        for q in qubits {
            let eveBasis = RandomBit();
            if eveBasis == 1 { H(q); }
            let measurement = M(q);
            // Eve re-prepares based on measurement
            if measurement == Zero { Reset(q); }
            else { Reset(q); X(q); }
            if eveBasis == 1 { H(q); }
        }
    }

    function EstimateErrorRate(aliceKey : Int[], bobKey : Int[]) : Double {
        mutable errors = 0;
        let n = Length(aliceKey);
        for i in 0..n-1 {
            if aliceKey[i] != bobKey[i] { set errors += 1; }
        }
        return IntAsDouble(errors) / IntAsDouble(n);
    }

    @EntryPoint()
    operation Main() : Unit {
        let keyLength = 100;
        
        // Without eavesdropper
        let (aliceKey1, bobKey1) = RunBB84(keyLength, false);
        let errorRate1 = EstimateErrorRate(aliceKey1, bobKey1);
        Message($"WITHOUT eavesdropper: error rate = {errorRate1 * 100.0:F1}%");
        Message($"  Key length after sifting: {Length(aliceKey1)} bits");
        
        // With eavesdropper
        let (aliceKey2, bobKey2) = RunBB84(keyLength, true);
        let errorRate2 = EstimateErrorRate(aliceKey2, bobKey2);
        Message($"\nWITH eavesdropper: error rate = {errorRate2 * 100.0:F1}%");
        Message($"  Key length after sifting: {Length(aliceKey2)} bits");
        
        // Detection threshold is typically 11% (QBER threshold)
        Message($"\nEavesdropper detected: {errorRate2 > 0.11}");
    }
}
```

---

## Project Tasks

### Task 1: Basic Protocol
Implement the basic BB84 protocol above and verify that without an eavesdropper, Alice and Bob share identical keys (0% error rate).

### Task 2: Eve Detection
Add the eavesdropper and verify that the error rate jumps to ~25% (Eve disturbs roughly 1/4 of bits on average).

### Task 3: Privacy Amplification
After sifting, implement **privacy amplification** — hashing the sifted key to reduce Eve's information to negligible amounts.

### Task 4: Information Reconciliation
Implement a simple **parity check protocol** to reconcile minor differences between Alice and Bob's keys.

### Task 5: Analysis
Run the protocol 100 times both with and without an eavesdropper. Plot the distribution of error rates and find the optimal detection threshold.

---

## Security Analysis

The security of BB84 relies on the **no-cloning theorem**: Eve cannot copy qubits without detection.

- No eavesdropper: ~0% error (only shot noise)
- Eve intercept-and-resend: ~25% error
- Undetectable Eve: quantum mechanics proves impossible

---

*Next Project: [03 — Quantum Tic-Tac-Toe](03-quantum-game.md)*
