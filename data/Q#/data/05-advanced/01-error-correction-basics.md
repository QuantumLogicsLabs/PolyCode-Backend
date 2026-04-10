# 01 — Quantum Error Correction Basics

## Why Errors Are Inevitable

Real quantum hardware is fragile. Qubits interact with their environment — a phenomenon called **decoherence**. This causes:

- **Bit flip errors**: |0⟩ → |1⟩ or |1⟩ → |0⟩ (X errors)
- **Phase flip errors**: |+⟩ → |-⟩ (Z errors)
- **Combined errors** (Y errors = X and Z together)

Classical error correction copies data (majority vote). But **quantum no-cloning theorem** forbids copying qubits!

Quantum error correction must:
1. Detect errors without measuring the qubit's value
2. Correct the error without destroying the superposition

---

## The 3-Qubit Bit Flip Code

The simplest QEC code. Encodes 1 logical qubit into 3 physical qubits:

```
|0⟩_L = |000⟩
|1⟩_L = |111⟩
α|0⟩ + β|1⟩ → α|000⟩ + β|111⟩
```

### Encoding
```qsharp
operation EncodeLogicalQubit(logicalQubit : Qubit, physicalQubits : Qubit[]) : Unit is Adj {
    // physicalQubits[0] is already the logical qubit content
    CNOT(physicalQubits[0], physicalQubits[1]);
    CNOT(physicalQubits[0], physicalQubits[2]);
}
```

### Error Detection (Syndrome Measurement)

Measure **parity** of qubit pairs without revealing individual values:

```qsharp
operation MeasureBitFlipSyndrome(physicalQubits : Qubit[]) : (Result, Result) {
    use ancilla = Qubit[2];
    
    // Syndrome 1: parity of qubits 0 and 1
    CNOT(physicalQubits[0], ancilla[0]);
    CNOT(physicalQubits[1], ancilla[0]);
    
    // Syndrome 2: parity of qubits 1 and 2
    CNOT(physicalQubits[1], ancilla[1]);
    CNOT(physicalQubits[2], ancilla[1]);
    
    let s1 = M(ancilla[0]);
    let s2 = M(ancilla[1]);
    
    ResetAll(ancilla);
    return (s1, s2);
}
```

### Syndrome Interpretation

| Syndrome (s1, s2) | Error on qubit |
|-------------------|----------------|
| (Zero, Zero) | No error |
| (One, Zero) | Qubit 0 flipped |
| (One, One) | Qubit 1 flipped |
| (Zero, One) | Qubit 2 flipped |

### Error Correction
```qsharp
operation CorrectBitFlip(physicalQubits : Qubit[]) : Unit {
    let (s1, s2) = MeasureBitFlipSyndrome(physicalQubits);
    
    if s1 == One and s2 == Zero { X(physicalQubits[0]); }
    elif s1 == One and s2 == One { X(physicalQubits[1]); }
    elif s1 == Zero and s2 == One { X(physicalQubits[2]); }
    // If (Zero, Zero): no correction needed
}
```

---

## The 3-Qubit Phase Flip Code

Similarly, we can protect against phase flips using the **Hadamard basis**:

```
|0⟩_L = |+++⟩ = H⊗H⊗H|000⟩
|1⟩_L = |---⟩ = H⊗H⊗H|111⟩
```

```qsharp
operation EncodePhaseFlip(logicalQubit : Qubit, physicalQubits : Qubit[]) : Unit is Adj {
    CNOT(physicalQubits[0], physicalQubits[1]);
    CNOT(physicalQubits[0], physicalQubits[2]);
    // Then apply H to all three
    ApplyToEach(H, physicalQubits);
}
```

---

## The Shor Code (9-qubit)

Combines both codes to protect against **any single-qubit error**:

```
1 logical qubit → 9 physical qubits

Encodes each logical qubit with phase flip code:
|0⟩_L → (|000⟩ + |111⟩)⊗3 / 2√2
|1⟩_L → (|000⟩ - |111⟩)⊗3 / 2√2
```

Structure:
1. Encode with phase flip code (3 blocks of 3)
2. Encode each block with bit flip code
3. Can correct any X, Z, or Y error on any single qubit

---

## Key QEC Concepts

| Concept | Description |
|---------|-------------|
| **Logical qubit** | The protected, encoded qubit |
| **Physical qubits** | The raw hardware qubits |
| **Syndrome** | The error signature (doesn't reveal data) |
| **Code distance d** | Corrects ⌊(d-1)/2⌋ errors |
| **Threshold** | Error rate below which more qubits = more reliable |

---

## The Quantum Error Correction Conditions

A code with code space C can correct errors {Eₐ} if:

```
⟨ψᵢ|Eₐ†Eᵦ|ψⱼ⟩ = Cₐᵦ δᵢⱼ
```

Intuitively: errors must be distinguishable from the syndrome alone, without knowing the logical state.

---

## Exercises

### Exercise 1
Implement the full 3-qubit bit flip code:
1. Encode `|+⟩` into 3 qubits
2. Deliberately inject an X error on qubit 1
3. Run syndrome measurement
4. Apply correction
5. Decode and verify `|+⟩` is recovered

### Exercise 2
Implement the 3-qubit phase flip code. Inject a Z error and correct it.

### Exercise 3 (Research)
Read about the [[7,1,3]] Steane code. How many errors can it correct? How many physical qubits per logical qubit?

---

*Next: [02 — Stabilizer Codes](02-stabilizer-codes.md)*
