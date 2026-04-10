# 02 — Single-Qubit Gates

## What Is a Quantum Gate?

A quantum gate is a **unitary matrix** that transforms qubit states. Unitarity means:
- U†U = I (the gate is its own inverse when conjugate transposed)
- Preserves normalization: probabilities still sum to 1
- All quantum gates are **reversible**

---

## The Pauli Gates

### X Gate (Quantum NOT / Bit Flip)
```
X = [0 1]
    [1 0]

|0⟩ → |1⟩
|1⟩ → |0⟩
```

```qsharp
X(q);  // Flip the qubit
```

### Z Gate (Phase Flip)
```
Z = [1  0]
    [0 -1]

|0⟩ → |0⟩
|1⟩ → -|1⟩
```
Z leaves |0⟩ unchanged but flips the phase of |1⟩.

```qsharp
Z(q);
```

### Y Gate
```
Y = [0 -i]
    [i  0]

|0⟩ → i|1⟩
|1⟩ → -i|0⟩
```

```qsharp
Y(q);
```

---

## The Hadamard Gate

The most important single-qubit gate:

```
H = 1/√2 * [1  1]
            [1 -1]

|0⟩ → (|0⟩ + |1⟩)/√2 = |+⟩
|1⟩ → (|0⟩ - |1⟩)/√2 = |-⟩
```

H creates equal superposition and is **self-adjoint** (H† = H, so H applied twice = identity).

```qsharp
H(q);
H(q);  // Back to original state
```

---

## Phase Gates

### S Gate (Phase Gate, √Z)
```
S = [1 0]
    [0 i]
```
Adds a phase of i (π/2 rotation) to |1⟩.

### T Gate (π/8 gate, ⁴√Z)
```
T = [1 0        ]
    [0 e^(iπ/4) ]
```
Adds a phase of e^(iπ/4) to |1⟩. Critical for universal quantum computing.

```qsharp
S(q);
T(q);
Adjoint S(q);  // S† = S⁻¹
Adjoint T(q);  // T† = T⁻¹
```

---

## Rotation Gates

Continuous rotation around Bloch sphere axes:

### Rx — Rotation around X axis
```
Rx(θ, q)  →  cos(θ/2)·I - i·sin(θ/2)·X
```

### Ry — Rotation around Y axis
```
Ry(θ, q)  →  cos(θ/2)·I - i·sin(θ/2)·Y
```

### Rz — Rotation around Z axis
```
Rz(θ, q)  →  e^(-iθ/2)·|0⟩⟨0| + e^(iθ/2)·|1⟩⟨1|
```

```qsharp
open Microsoft.Quantum.Math;

Rx(PI() / 4.0, q);     // 45° rotation around X
Ry(PI() / 2.0, q);     // 90° rotation around Y
Rz(PI(), q);            // 180° rotation around Z (= e^(-iπ/2)·Z up to global phase)
```

### R1 — Phase Rotation
```
R1(θ, q)  →  [1 0      ]
              [0 e^(iθ) ]
```

```qsharp
R1(PI() / 4.0, q);   // Same as T gate
R1(PI() / 2.0, q);   // Same as S gate
```

---

## Gate Relationships

```
X = Rx(π) (up to global phase)
Z = Rz(π) (up to global phase)
H = Ry(π/2)·Z = (X+Z)/√2
S = T²
T = S^(1/2)
Z = S² = T⁴
```

---

## Gate Summary Table

| Gate | Q# Call | Effect on |0⟩ | Effect on |1⟩ |
|------|---------|------------|------------|
| X | `X(q)` | → \|1⟩ | → \|0⟩ |
| Y | `Y(q)` | → i\|1⟩ | → -i\|0⟩ |
| Z | `Z(q)` | → \|0⟩ | → -\|1⟩ |
| H | `H(q)` | → \|+⟩ | → \|-⟩ |
| S | `S(q)` | → \|0⟩ | → i\|1⟩ |
| T | `T(q)` | → \|0⟩ | → e^(iπ/4)\|1⟩ |

---

## Code Examples

```qsharp
open Microsoft.Quantum.Diagnostics;
open Microsoft.Quantum.Math;

operation ExploreGates() : Unit {
    use q = Qubit();

    // Demonstrate H gate
    H(q);
    Message("After H:");
    DumpMachine();  // Should show |+⟩

    // Apply Z in superposition
    Z(q);
    Message("After H then Z:");
    DumpMachine();  // Should show |-⟩

    // H again returns to |1⟩
    H(q);
    Message("After H, Z, H:");
    DumpMachine();  // Should show |1⟩

    Reset(q);
}
```

---

## Exercises

### Exercise 1: Gate Identities
Verify these identities using `DumpMachine`:
- `H·X·H = Z`
- `H·Z·H = X`
- `S·S = Z`
- `T·T = S`

### Exercise 2: State Preparation
Write operations that prepare these specific states from |0⟩:
- `|1⟩` (use X)
- `|+⟩` (use H)
- `|-⟩` (use X then H)
- `|i⟩ = (|0⟩ + i|1⟩)/√2` (use H then S)

### Exercise 3: Rotation
Apply `Rx(PI()/3.0, q)` and calculate by hand what probability of measuring `Zero` you expect. Verify with simulation.

---

*Next: [03 — Multi-Qubit Systems](03-multi-qubit-systems.md)*
