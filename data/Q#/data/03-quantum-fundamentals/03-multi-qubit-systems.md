# 03 — Multi-Qubit Systems

## Tensor Products

When you have multiple qubits, the combined state is their **tensor product** (⊗).

For two qubits:
```
|q1⟩ ⊗ |q2⟩ = |q1 q2⟩
```

Two basis states each → four combined basis states:

```
|00⟩ = [1,0,0,0]ᵀ
|01⟩ = [0,1,0,0]ᵀ
|10⟩ = [0,0,1,0]ᵀ
|11⟩ = [0,0,0,1]ᵀ
```

A general 2-qubit state:
```
|ψ⟩ = α|00⟩ + β|01⟩ + γ|10⟩ + δ|11⟩
```
with |α|² + |β|² + |γ|² + |δ|² = 1.

---

## Separable vs Entangled States

**Separable (product) state**: can be written as tensor product of individual qubits

```
|ψ⟩ = |q1⟩ ⊗ |q2⟩ = (α|0⟩ + β|1⟩) ⊗ (γ|0⟩ + δ|1⟩)
    = αγ|00⟩ + αδ|01⟩ + βγ|10⟩ + βδ|11⟩
```

**Entangled state**: CANNOT be written as a product

```
|Φ+⟩ = (|00⟩ + |11⟩)/√2
```
There are no single-qubit states α,β,γ,δ that multiply to give this!

---

## Q# Multi-Qubit Allocation

```qsharp
// Allocate array of qubits
use qs = Qubit[3];   // qs[0], qs[1], qs[2]

// Allocate tuple
use (q1, q2) = (Qubit(), Qubit());

// All start in |0⟩ state → combined state is |000⟩

// Apply H to first qubit: (|0⟩+|1⟩)/√2 ⊗ |0⟩ ⊗ |0⟩
H(qs[0]);
```

---

## DumpMachine for Multi-Qubit States

```qsharp
use qs = Qubit[2];

H(qs[0]);
// State: (|0⟩+|1⟩)/√2 ⊗ |0⟩ = (|00⟩+|10⟩)/√2

DumpMachine();
// Output:
// ∣00❭:  0.707107 + 0.000000 i  ==  █████  [ 0.500000 ]
// ∣10❭:  0.707107 + 0.000000 i  ==  █████  [ 0.500000 ]
```

---

## Qubit Ordering Convention

In Q#, qubits are ordered **little-endian** in `DumpMachine` output:

```
State label |q0 q1 q2⟩
```

So `|10⟩` in DumpMachine means q0=1, q1=0. This can be confusing — always check!

---

## Multi-Qubit Measurement

```qsharp
// Measure all qubits
let results = ForEach(M, qs);  // Returns Result[]

// Convert to integer
open Microsoft.Quantum.Convert;
let value = ResultArrayAsInt(results);

// Parity measurement (doesn't collapse individual qubits!)
let parity = Measure([PauliZ, PauliZ], [q1, q2]);
```

---

## Quantum Registers

In algorithms, groups of qubits form **registers**:

```qsharp
// Conceptual registers using arrays
use (inputReg, outputReg, ancillaReg) = (Qubit[4], Qubit[4], Qubit[2]);

// Apply operations to entire registers
ApplyToEach(H, inputReg);
ResetAll(ancillaReg);
```

---

## Example: GHZ State (3-qubit entanglement)

```qsharp
open Microsoft.Quantum.Diagnostics;

operation PrepareGHZ(qs : Qubit[]) : Unit is Adj {
    H(qs[0]);
    for i in 1..Length(qs)-1 {
        CNOT(qs[0], qs[i]);
    }
}

@EntryPoint()
operation GHZDemo() : Unit {
    use qs = Qubit[3];
    PrepareGHZ(qs);
    
    Message("GHZ state |000⟩ + |111⟩ (unnormalized):");
    DumpMachine();
    // Shows equal amplitude for |000⟩ and |111⟩ only
    
    // All qubits measure the same!
    let (r0, r1, r2) = (M(qs[0]), M(qs[1]), M(qs[2]));
    Message($"Results: {r0}, {r1}, {r2}");
    // Always: all Zero or all One
    
    ResetAll(qs);
}
```

---

## Exercises

### Exercise 1
Create a 3-qubit state |+++⟩ (H applied to all). Use DumpMachine to verify all 8 basis states appear with equal probability (1/8 each).

### Exercise 2
Create the GHZ state for n=4 qubits. Measure all qubits 100 times in a loop and verify they always agree.

### Exercise 3
Create the 2-qubit state `(|01⟩ + |10⟩)/√2` (the |Ψ+⟩ Bell state). Verify it's entangled by checking that measuring one qubit determines the other.

---

*Next: [04 — Two-Qubit Gates](04-two-qubit-gates.md)*
