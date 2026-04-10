# 06 — Functors: Adjoint and Controlled

## What Are Functors?

Functors are Q#'s way of transforming operations. They're one of Q#'s most powerful features.

Two built-in functors:
- **`Adjoint`** — the quantum inverse (conjugate transpose) of an operation
- **`Controlled`** — apply an operation conditionally based on a control qubit

---

## The Adjoint Functor

Every quantum gate has an inverse. The `Adjoint` functor computes it automatically.

```qsharp
operation PrepareState(q : Qubit) : Unit is Adj {
    H(q);
    T(q);
    S(q);
}

// Use:
PrepareState(q);           // Apply forward
Adjoint PrepareState(q);   // Apply inverse (undoes PrepareState)
```

### Declaring Adjoint Support

```qsharp
// Auto-generate adjoint from body:
operation MyOp(q : Qubit) : Unit is Adj {
    H(q);
    T(q);
}

// Manual adjoint:
operation MyOp2(q : Qubit) : Unit is Adj {
    body (...) {
        H(q);
        T(q);
    }
    adjoint (...) {
        Adjoint T(q);   // Manual: reverse order + adjoint each gate
        H(q);           // H is self-adjoint, so Adjoint H = H
    }
}
```

### `is Adj` vs `is Adj + Ctl`

```qsharp
operation A(q : Qubit) : Unit is Adj { ... }       // Adjoint only
operation B(q : Qubit) : Unit is Ctl { ... }       // Controlled only
operation C(q : Qubit) : Unit is Adj + Ctl { ... } // Both
```

---

## The Controlled Functor

`Controlled` makes any operation conditional on a control qubit (or register):

```qsharp
use (control, target) = (Qubit(), Qubit());

H(control);                          // Put control in superposition
Controlled X([control], target);     // CNOT: flip target if control = |1⟩
```

Note: `Controlled` takes an **array** of control qubits:
```qsharp
// Single control
Controlled H([ctrl], q);

// Multi-control (Toffoli-like)
Controlled X([ctrl1, ctrl2], target);
```

### Shorthand for Common Controlled Gates

The standard library provides shorthand:
```qsharp
CNOT(control, target);           // Controlled X
CCNOT(ctrl1, ctrl2, target);     // Doubly-controlled X (Toffoli)
CZ(control, target);             // Controlled Z
```

---

## Combining Functors

```qsharp
operation Apply(q : Qubit) : Unit is Adj + Ctl {
    H(q);
    T(q);
}

// You can combine them:
Controlled Adjoint Apply([ctrl], q);    // Controlled inverse
Adjoint Controlled Apply([ctrl], q);    // Same thing (commutative)
```

---

## Practical Example: Quantum Phase Kickback

```qsharp
operation PhaseKickback(control : Qubit, target : Qubit) : Unit is Adj + Ctl {
    within {
        H(target);
    } apply {
        Controlled Z([control], target);
    }
}
```

---

## Why Functors Matter

| Scenario | Functor Used |
|----------|-------------|
| Uncomputing ancilla qubits | `Adjoint` |
| Conditional quantum operations | `Controlled` |
| `within/apply` blocks | Auto-uses `Adjoint` |
| Quantum error correction | Both |
| Phase estimation | `Controlled` |

---

## Auto-Generated vs Manual Adjoint

Q# can **auto-generate** the adjoint for operations whose body only uses adjointable operations:

```qsharp
// Q# can auto-generate adjoint here ✅
operation AutoAdj(q : Qubit) : Unit is Adj {
    H(q);   // H is Adj
    T(q);   // T is Adj
    CNOT(q, q);  // CNOT is Adj
}

// Must be manual here ❌ (measurement is not reversible)
operation WithMeasure(q : Qubit) : Unit {
    H(q);
    let r = M(q);   // M is NOT adjointable
    if r == One { X(q); }
}
```

---

## Exercises

### Exercise 1
Write an operation `PrepareAndUnprepare(q : Qubit) : Unit` that applies `H` then `T`, then uses `Adjoint` to undo both. Verify with `DumpMachine` that the qubit returns to |0⟩.

### Exercise 2
Implement a controlled-H gate using `Controlled H([ctrl], target)`. Put the control qubit in superposition and observe the entanglement.

### Exercise 3
Write an operation marked `is Adj + Ctl` and demonstrate all four usages: normal, Adjoint, Controlled, and Controlled Adjoint.

---

*Next Module: [03 — Quantum Fundamentals](../03-quantum-fundamentals/README.md)*
