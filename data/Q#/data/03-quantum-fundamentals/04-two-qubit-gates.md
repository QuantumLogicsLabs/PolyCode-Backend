# 04 — Two-Qubit Gates

## CNOT Gate (Controlled-NOT / CX)

The CNOT is the most important two-qubit gate. It flips the **target** qubit if and only if the **control** qubit is |1⟩.

```
CNOT:
|00⟩ → |00⟩
|01⟩ → |01⟩
|10⟩ → |11⟩   ← control=1, so target flips
|11⟩ → |10⟩   ← control=1, so target flips
```

Matrix form:
```
CNOT = [1 0 0 0]
       [0 1 0 0]
       [0 0 0 1]
       [0 0 1 0]
```

```qsharp
CNOT(control, target);
// Or equivalently:
Controlled X([control], target);
```

---

## Creating Entanglement with CNOT

The CNOT gate is essential for creating Bell states (maximally entangled states):

```qsharp
operation CreateBellState(q1 : Qubit, q2 : Qubit) : Unit {
    H(q1);              // q1 → |+⟩
    CNOT(q1, q2);       // Entangle!
    // State is now: (|00⟩ + |11⟩)/√2
}
```

```qsharp
open Microsoft.Quantum.Diagnostics;

@EntryPoint()
operation BellStateDemo() : Unit {
    use (q1, q2) = (Qubit(), Qubit());
    
    CreateBellState(q1, q2);
    
    Message("Bell state |Φ+⟩:");
    DumpMachine();
    // Shows: |00⟩ with amp 0.707, |11⟩ with amp 0.707
    
    let (r1, r2) = (M(q1), M(q2));
    Message($"Measurement: {r1}, {r2}");
    // Always correlated: both Zero or both One!
    
    ResetAll([q1, q2]);
}
```

---

## The Four Bell States

```qsharp
operation PrepareBellState(index : Int, q1 : Qubit, q2 : Qubit) : Unit {
    H(q1);
    CNOT(q1, q2);
    
    if index == 1 { Z(q1); }  // |Φ-⟩ = (|00⟩ - |11⟩)/√2
    if index == 2 { X(q2); }  // |Ψ+⟩ = (|01⟩ + |10⟩)/√2
    if index == 3 { Z(q1); X(q2); }  // |Ψ-⟩ = (|01⟩ - |10⟩)/√2
}
// index=0: |Φ+⟩, 1: |Φ-⟩, 2: |Ψ+⟩, 3: |Ψ-⟩
```

---

## CZ Gate (Controlled-Z)

Flips the phase of |11⟩:
```
|00⟩ → |00⟩
|01⟩ → |01⟩
|10⟩ → |10⟩
|11⟩ → -|11⟩
```

```qsharp
CZ(control, target);
// Equivalently:
Controlled Z([control], target);
```

Note: `CZ` is **symmetric** — control and target can be swapped!

---

## SWAP Gate

Exchanges two qubits:
```
|ab⟩ → |ba⟩
```

```qsharp
SWAP(q1, q2);

// Decomposition into CNOTs:
CNOT(q1, q2);
CNOT(q2, q1);
CNOT(q1, q2);
```

---

## Toffoli Gate (CCNOT / Controlled-Controlled-NOT)

Three-qubit gate: flips target if **both** controls are |1⟩.

```qsharp
CCNOT(control1, control2, target);
// Equivalently:
Controlled X([control1, control2], target);
```

The Toffoli gate is **classically universal** — it can implement AND, OR, NOT:
```qsharp
// AND gate:
// target starts |0⟩, result in target after CCNOT
CCNOT(a, b, target);  // target = a AND b

// NOT gate:
// Set controls to |1⟩ and flip target
X(target);

// NAND gate:
CCNOT(a, b, target);
X(target);
```

---

## Two-Qubit Gate Summary

| Gate | Q# | Controls | Effect |
|------|----|----------|--------|
| CNOT/CX | `CNOT(c,t)` | 1 | Flip target if control=1 |
| CZ | `CZ(c,t)` | 1 | Phase flip if both=1 |
| SWAP | `SWAP(a,b)` | — | Exchange qubits |
| Toffoli/CCNOT | `CCNOT(c1,c2,t)` | 2 | Flip target if both controls=1 |
| Fredkin/CSWAP | `Controlled SWAP([c],[a,b])` | 1 | Swap if control=1 |

---

## Exercises

### Exercise 1: Bell States
Prepare all four Bell states and verify each with `DumpMachine`.

### Exercise 2: Entanglement Verification
Create a Bell state, then measure both qubits 1000 times (in a loop). Verify that results are always correlated (both 0 or both 1, never mixed).

### Exercise 3: SWAP Decomposition  
Implement SWAP using only three CNOT gates. Verify it works correctly.

### Exercise 4: Quantum Addition
Using ancilla qubits, implement a quantum half-adder:
- Inputs: a, b (qubits)
- Outputs: sum = a XOR b, carry = a AND b

---

*Next: [05 — Measurement](05-measurement.md)*
