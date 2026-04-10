# 04 — Quantum Oracles

## What Is a Quantum Oracle?

A **quantum oracle** is a "black box" operation that encodes a classical function into a quantum operation. Oracles are fundamental to quantum algorithms like Grover's search and Deutsch-Jozsa.

Two main types:
- **Bit oracle** (standard form): `|x⟩|y⟩ → |x⟩|y ⊕ f(x)⟩`
- **Phase oracle**: `|x⟩ → (-1)^f(x)|x⟩`

---

## Bit Oracle (Standard Form)

The bit oracle encodes f(x) into an ancilla qubit:

```
Uf: |x⟩|y⟩ → |x⟩|y ⊕ f(x)⟩
```

If the ancilla starts in |0⟩, the result is `|x⟩|f(x)⟩`.

### Q# Example: Oracle for f(x) = x₀ AND x₁

```qsharp
operation BitOracle(input : Qubit[], output : Qubit) : Unit is Adj + Ctl {
    // f(x) = x[0] AND x[1]
    CCNOT(input[0], input[1], output);
}

// Use:
use (x, y) = (Qubit[2], Qubit());
// Prepare input state
X(x[0]); X(x[1]);   // x = |11⟩
BitOracle(x, y);     // y = f(11) = 1 → y becomes |1⟩
```

---

## Phase Oracle

The phase oracle marks solutions with a phase flip:

```
Of: |x⟩ → (-1)^f(x)|x⟩
```

Solutions (where f(x)=1) get a -1 phase. Non-solutions are unchanged.

### Converting Bit Oracle to Phase Oracle (Phase Kickback!)

The elegant trick: set the output qubit to |−⟩ = (|0⟩ - |1⟩)/√2:

```
Uf|x⟩|−⟩ = |x⟩ ⊗ (|0⊕f(x)⟩ - |1⊕f(x)⟩)/√2
           = (-1)^f(x) |x⟩|−⟩
```

The phase "kicks back" to the input register!

```qsharp
operation BitOracleToPhaseOracle(
    bitOracle : (Qubit[], Qubit) => Unit is Adj,
    input : Qubit[]
) : Unit is Adj {
    use ancilla = Qubit();
    
    // Prepare |−⟩
    X(ancilla);
    H(ancilla);
    
    // Apply bit oracle — phase kickback happens!
    bitOracle(input, ancilla);
    
    // Return ancilla to |0⟩
    H(ancilla);
    X(ancilla);
}
```

---

## Common Oracles

### Constant Oracle (f(x) = 0 or f(x) = 1)
```qsharp
operation ConstantZeroOracle(input : Qubit[], output : Qubit) : Unit is Adj + Ctl {
    // f(x) = 0 for all x — do nothing
}

operation ConstantOneOracle(input : Qubit[], output : Qubit) : Unit is Adj + Ctl {
    // f(x) = 1 for all x — always flip output
    X(output);
}
```

### Balanced Oracle (f(x) = parity)
```qsharp
operation ParityOracle(input : Qubit[], output : Qubit) : Unit is Adj + Ctl {
    // f(x) = x[0] XOR x[1] XOR ... XOR x[n-1]
    for q in input {
        CNOT(q, output);
    }
}
```

### Marked Element Oracle (for Grover)
```qsharp
operation MarkElement(target : Int, register : Qubit[]) : Unit is Adj + Ctl {
    // Flips phase of the specific basis state |target⟩
    // Technique: flip bits to make target look like |111...1⟩, then multi-controlled Z
    
    let n = Length(register);
    
    within {
        for i in 0..n-1 {
            // Flip qubit i if bit i of target is 0
            if ((target &&& (1 <<< i)) == 0) { X(register[i]); }
        }
    } apply {
        Controlled Z(register[0..n-2], register[n-1]);
    }
}
```

---

## The Deutsch-Jozsa Oracle

Classic quantum algorithm using oracles. Given f: {0,1}ⁿ → {0,1}, determine if f is:
- **Constant**: f(x) = 0 for all x, or f(x) = 1 for all x
- **Balanced**: f(x) = 0 for exactly half of inputs

Classical: need 2^(n-1)+1 queries. Quantum: need **1 query**.

```qsharp
operation DeutschJozsa(oracle : (Qubit[], Qubit) => Unit, n : Int) : Bool {
    use (input, output) = (Qubit[n], Qubit());
    
    // Prepare: input in superposition, output in |−⟩
    ApplyToEach(H, input);
    X(output);
    H(output);
    
    // Query oracle once
    oracle(input, output);
    
    // Interference
    ApplyToEach(H, input);
    
    // Measure — all zeros → constant, any one → balanced
    let results = ForEach(M, input);
    
    ResetAll(input + [output]);
    
    // If all zeros → constant function
    return All(r -> r == Zero, results);
}
```

---

## Exercises

### Exercise 1
Implement an oracle for f(x) = x₀ XOR x₂ (XOR of first and third bits).

### Exercise 2
Convert your bit oracle to a phase oracle using the phase kickback technique. Verify the phase using `DumpMachine`.

### Exercise 3
Run the Deutsch-Jozsa algorithm on both a constant oracle and a balanced oracle. Verify you can distinguish them in 1 query.

---

*Next: [05 — Amplitude Amplification](05-amplitude-amplification.md)*
