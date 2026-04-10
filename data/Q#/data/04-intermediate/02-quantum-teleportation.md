# 02 — Quantum Teleportation

## What Is Quantum Teleportation?

Quantum teleportation transfers the **quantum state** of one qubit to another, using:
- A shared Bell pair (entanglement)
- 2 classical bits of communication
- No physical movement of the qubit

> ⚠️ This does **not** violate the speed of light — classical communication is still needed!

---

## The Protocol

```
Alice                                    Bob
  │                                       │
  │  message qubit: |ψ⟩                   │
  │                                       │
  ├── Prepare Bell pair ──────────────────┤
  │   Alice keeps q1, Bob keeps q2        │
  │                                       │
  ├── CNOT(message, q1) ──────────────────│
  ├── H(message) ─────────────────────────│
  │                                       │
  ├── Measure message → m1 ───────────────│
  ├── Measure q1 → m2 ────────────────────│
  │                                       │
  ├──────── Send m1, m2 (classical) ──────▶
  │                                       │
  │                               if m2==1: X(q2)
  │                               if m1==1: Z(q2)
  │                                       │
  │                               Bob now has |ψ⟩!
```

---

## Q# Implementation

```qsharp
namespace Teleportation {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Diagnostics;

    /// Prepare a Bell pair between two qubits
    operation CreateBellPair(q1 : Qubit, q2 : Qubit) : Unit {
        H(q1);
        CNOT(q1, q2);
    }

    /// Alice's part: entangle message with her half of the Bell pair
    operation AliceOperation(message : Qubit, aliceQubit : Qubit) : (Result, Result) {
        CNOT(message, aliceQubit);
        H(message);
        
        let m1 = M(message);    // Measure message qubit
        let m2 = M(aliceQubit); // Measure Alice's Bell qubit
        
        return (m1, m2);
    }

    /// Bob's part: apply corrections based on classical bits
    operation BobOperation(m1 : Result, m2 : Result, bobQubit : Qubit) : Unit {
        // Apply X correction if m2 == One
        if m2 == One { X(bobQubit); }
        
        // Apply Z correction if m1 == One
        if m1 == One { Z(bobQubit); }
    }

    /// Full teleportation protocol
    operation Teleport(message : Qubit, bobQubit : Qubit) : Unit {
        use aliceQubit = Qubit();
        
        // Step 1: Create entangled pair shared between Alice and Bob
        CreateBellPair(aliceQubit, bobQubit);
        
        // Step 2: Alice performs Bell measurement
        let (m1, m2) = AliceOperation(message, aliceQubit);
        
        // Step 3: Bob applies corrections (simulating classical communication)
        BobOperation(m1, m2, bobQubit);
        
        // Alice's qubits are now collapsed/garbage
        Reset(aliceQubit);
    }

    @EntryPoint()
    operation Main() : Unit {
        // Prepare a message state |+⟩ = H|0⟩
        use (message, bobQubit) = (Qubit(), Qubit());
        
        H(message);  // Create |+⟩ to teleport
        
        Message("State to teleport (message qubit):");
        DumpRegister([message]);
        
        // Teleport
        Teleport(message, bobQubit);
        
        Message("\nBob's qubit after teleportation:");
        DumpRegister([bobQubit]);
        // Bob's qubit should now be |+⟩!
        
        Reset(message);
        Reset(bobQubit);
    }
}
```

---

## Why Does It Work?

Starting state: `|ψ⟩ = α|0⟩ + β|1⟩`

After creating Bell pair and Alice's operations, the full state is:

```
= ½[|00⟩(α|0⟩ + β|1⟩)
  + |01⟩(α|1⟩ + β|0⟩)
  + |10⟩(α|0⟩ - β|1⟩)
  + |11⟩(α|1⟩ - β|0⟩)]
```

After Alice measures `(m1, m2)`:
- `(0,0)` → Bob has `α|0⟩ + β|1⟩` (no correction needed)
- `(0,1)` → Bob has `α|1⟩ + β|0⟩` → apply X
- `(1,0)` → Bob has `α|0⟩ - β|1⟩` → apply Z
- `(1,1)` → Bob has `α|1⟩ - β|0⟩` → apply X then Z

Bob always ends up with `|ψ⟩ = α|0⟩ + β|1⟩`! ✅

---

## No-Cloning Theorem Connection

Notice that after teleportation, **Alice's original qubit is destroyed** (measured). This is consistent with the **no-cloning theorem**: you cannot copy an unknown quantum state.

Teleportation moves the state, it doesn't copy it.

---

## Exercises

### Exercise 1
Teleport the state `|1⟩` (apply X before teleporting). Verify Bob receives `|1⟩`.

### Exercise 2
Teleport the state `|-⟩ = (|0⟩ - |1⟩)/√2`. Verify Bob receives `|-⟩`.

### Exercise 3
Modify the protocol to use a different Bell state (|Ψ+⟩ instead of |Φ+⟩). How do the correction operations change?

### Exercise 4 (Challenge)
Implement **entanglement swapping**: teleport a qubit that is itself entangled with another qubit.

---

*Next: [03 — Superdense Coding](03-superdense-coding.md)*
