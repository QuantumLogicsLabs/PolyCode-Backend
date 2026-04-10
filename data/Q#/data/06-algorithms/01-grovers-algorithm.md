# 01 — Grover's Search Algorithm

## The Problem

Given an unsorted list of N items, find the one that satisfies a condition.

- **Classical**: O(N) — check each item one by one
- **Grover's**: O(√N) — **quadratic speedup**

For N = 1,000,000 items: classical needs ~500,000 queries, Grover needs ~1,000.

---

## The Algorithm

### 1. Initialize: Uniform Superposition
```
|s⟩ = H^⊗n |0⟩^⊗n = (1/√N) Σ|x⟩
```
Equal probability for all N = 2ⁿ states.

### 2. Grover Iteration (repeat √N times)
Each iteration has two steps:

**Step A — Oracle (Phase Flip):**
Mark the solution |ω⟩ with a -1 phase:
```
|x⟩ → (-1)^f(x)|x⟩
```

**Step B — Diffusion (Inversion About Average):**
Amplify marked states, suppress others:
```
D = 2|s⟩⟨s| - I
```

After √N iterations, the solution has probability ~1 of being measured.

---

## Visual Intuition

Grover's works like amplitude amplification:

```
Iteration 0:  All states equal ~1/√N amplitude
              ████████████████████ (flat)

Iteration 1:  Oracle flips target, diffusion amplifies
              ████████████████████████ ← target grows

Iteration √N: Target amplitude ≈ 1
              ░░░░░░░░░░░░░░░░░░░░░█ ← almost certain
```

---

## Full Q# Implementation

```qsharp
namespace Grover {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Canon;
    open Microsoft.Quantum.Math;
    open Microsoft.Quantum.Convert;

    /// Oracle that marks the target element
    operation Oracle(target : Int, register : Qubit[]) : Unit is Adj + Ctl {
        let n = Length(register);
        
        within {
            // Flip bits where target has 0
            for i in 0..n-1 {
                if ((target &&& (1 <<< i)) == 0) {
                    X(register[i]);
                }
            }
        } apply {
            // Multi-controlled Z: only fires when all qubits are |1⟩
            Controlled Z(register[0..n-2], register[n-1]);
        }
    }

    /// Grover diffusion operator
    operation GroverDiffusion(register : Qubit[]) : Unit is Adj + Ctl {
        within {
            ApplyToEach(H, register);
            ApplyToEach(X, register);
        } apply {
            Controlled Z(register[0..Length(register)-2], register[Length(register)-1]);
        }
    }

    /// Single Grover iteration
    operation GroverIteration(target : Int, register : Qubit[]) : Unit {
        Oracle(target, register);
        GroverDiffusion(register);
    }

    /// Full Grover's algorithm
    operation GroversSearch(target : Int, n : Int) : Int {
        use register = Qubit[n];
        
        // Step 1: Initialize in uniform superposition
        ApplyToEach(H, register);
        
        // Step 2: Run √(2^n) iterations
        let numIterations = Round(PI() / 4.0 * Sqrt(IntAsDouble(1 <<< n)));
        Message($"Running {numIterations} Grover iterations for {1 <<< n} elements...");
        
        for i in 1..numIterations {
            GroverIteration(target, register);
        }
        
        // Step 3: Measure
        let resultBits = ForEach(M, register);
        let result = ResultArrayAsInt(resultBits);
        
        ResetAll(register);
        return result;
    }

    @EntryPoint()
    operation Main() : Unit {
        let n = 4;         // 4 qubits = 16 elements
        let target = 11;   // Search for element 11

        let found = GroversSearch(target, n);
        Message($"Searched for: {target}, Found: {found}");
        Message($"Correct: {found == target}");
    }
}
```

---

## Number of Iterations

Optimal number of iterations:
```
k = Round(π/4 · √(N/M))
```

Where:
- N = 2ⁿ (search space size)
- M = number of solutions (usually 1)

**Important:** Too many iterations REDUCES success probability! Grover oscillates.

```qsharp
function OptimalIterations(n : Int, numSolutions : Int) : Int {
    let N = 1 <<< n;  // 2^n
    return Round(PI() / 4.0 * Sqrt(IntAsDouble(N) / IntAsDouble(numSolutions)));
}
```

---

## Multiple Solutions

Grover works for M > 1 solutions too. The angle changes:
```
sin²(θ) = M/N, use k = Round(π/(4θ)) iterations
```

---

## Practical Considerations

| Aspect | Notes |
|--------|-------|
| Speedup | Only quadratic — not exponential |
| Requires | Quantum oracle for the problem |
| Best for | Unstructured search, NP-hard optimization |
| Limitation | Oracle must be reversible |

---

## Applications

- **Cryptography**: Grover halves the effective key length (AES-128 → 64-bit security)
- **SAT solving**: Quadratic speedup over brute force
- **Optimization**: Finding minimum/maximum in a dataset
- **Machine learning**: Quantum database search

---

## Exercises

### Exercise 1
Implement Grover's search for n=3 qubits (8 elements), searching for element 5. Run it and verify you find 5 most of the time.

### Exercise 2
Modify the oracle to search for **two** elements simultaneously (e.g., both 3 and 7). Adjust the number of iterations accordingly.

### Exercise 3
Plot success probability vs number of iterations for n=4, target=11. Observe the oscillation pattern.

### Exercise 4 (Challenge)
Implement Grover's algorithm for the satisfiability problem: given a 3-variable boolean formula, find a satisfying assignment.

---

*Next: [02 — Quantum Phase Estimation](02-phase-estimation.md)*
