# 03 — Shor's Factoring Algorithm

## Why Shor's Algorithm Matters

Shor's algorithm factors large integers in **polynomial time** — an exponential speedup over classical methods.

- **Classical best**: Sub-exponential, O(exp(n^(1/3))) for n-bit numbers
- **Shor's**: Polynomial O(n³) quantum gates

**Impact**: RSA encryption relies on factoring being hard. A sufficiently large quantum computer running Shor's would break RSA.

---

## The Mathematical Idea

Factoring N = p × q reduces to **period finding**:

1. Choose random a < N
2. Find the period r of f(x) = aˣ mod N (the smallest r where aʳ ≡ 1 mod N)
3. If r is even and aʳ/² ≢ -1 (mod N), then:
   - p = gcd(aʳ/² - 1, N)
   - q = gcd(aʳ/² + 1, N)

Period finding is where the quantum speedup happens — classical period finding is hard, quantum Fourier transform makes it efficient.

---

## Algorithm Overview

```
Input: N (number to factor)

1. Pick random a (2 ≤ a < N)
2. Check gcd(a, N) — if ≠ 1, we got lucky (found a factor)
3. QUANTUM PART: Find period r of f(x) = aˣ mod N
   a. Prepare: |0⟩|0⟩
   b. Superpose: H^⊗n|0⟩|0⟩
   c. Apply oracle: Σ|x⟩|aˣ mod N⟩  
   d. Measure second register
   e. Apply inverse QFT to first register
   f. Measure → get k/r approximation
   g. Use continued fractions to extract r
4. Compute gcd(aʳ/²±1, N)
5. Repeat if factors not found (probabilistic)
```

---

## The Quantum Fourier Transform (QFT)

The QFT is the heart of Shor's algorithm. It's the quantum analogue of the discrete Fourier transform:

```
QFT|j⟩ = (1/√N) Σₖ e^(2πijk/N)|k⟩
```

### Q# Implementation

```qsharp
operation QFT(register : Qubit[]) : Unit is Adj + Ctl {
    let n = Length(register);
    
    for i in 0..n-1 {
        H(register[i]);
        
        for j in i+1..n-1 {
            let angle = 2.0 * PI() / IntAsDouble(1 <<< (j - i + 1));
            Controlled R1([register[j]], (angle, register[i]));
        }
    }
    
    // Reverse qubit order
    for i in 0..n/2-1 {
        SWAP(register[i], register[n-1-i]);
    }
}
```

---

## Modular Exponentiation Oracle

The quantum oracle computes f(x) = aˣ mod N:

```qsharp
/// Compute a^x mod N into target register
/// This is the most resource-intensive part of Shor's
operation ModularExponentiationOracle(
    a : Int,
    N : Int,
    xRegister : Qubit[],
    targetRegister : Qubit[]
) : Unit is Adj + Ctl {
    let n = Length(xRegister);
    
    // Initialize target to |1⟩
    X(targetRegister[0]);
    
    // Controlled modular multiplications
    for i in 0..n-1 {
        let power = ModPow(a, 1 <<< i, N);
        Controlled MultiplyMod([xRegister[i]], (power, N, targetRegister));
    }
}

// Helper: modular exponentiation classically
function ModPow(base : Int, exp : Int, modulus : Int) : Int {
    mutable result = 1;
    mutable b = base % modulus;
    mutable e = exp;
    
    while e > 0 {
        if (e &&& 1) == 1 {
            set result = (result * b) % modulus;
        }
        set b = (b * b) % modulus;
        set e >>>= 1;
    }
    return result;
}
```

---

## Simplified Shor's for Small Numbers

```qsharp
namespace Shors {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Canon;
    open Microsoft.Quantum.Math;
    open Microsoft.Quantum.Convert;

    /// Factor N = 15 = 3 × 5 using a=2
    /// Hardcoded for pedagogical clarity
    @EntryPoint()
    operation FactorFifteen() : Unit {
        let N = 15;
        let a = 2;
        let n = 4;  // 4 qubits for the period register
        
        use (periodReg, targetReg) = (Qubit[n], Qubit[n]);
        
        // Initialize
        ApplyToEach(H, periodReg);
        X(targetReg[0]);  // targetReg = |1⟩
        
        // Modular exponentiation: targetReg *= a^x mod N
        // (For N=15, a=2: period is 4, so we expect QFT peaks at 0,4,8,12)
        
        // Apply QFT to period register
        QFT(periodReg);
        
        // Measure
        let measurement = ResultArrayAsInt(ForEach(M, periodReg));
        
        Message($"QFT measurement: {measurement}");
        Message($"Estimated period: approx {n}/{measurement} relates...");
        
        // Classical post-processing
        // r = 4 for a=2, N=15
        // gcd(2^2 - 1, 15) = gcd(3, 15) = 3 ✅
        // gcd(2^2 + 1, 15) = gcd(5, 15) = 5 ✅
        
        ResetAll(periodReg + targetReg);
        
        let r = 4;  // Known period
        let p = GCD(ModPow(a, r/2, N) - 1, N);
        let q = GCD(ModPow(a, r/2, N) + 1, N);
        Message($"Factors of {N}: {p} × {q}");
    }
    
    function GCD(a : Int, b : Int) : Int {
        mutable x = a;
        mutable y = b;
        while y != 0 {
            let temp = y;
            set y = x % y;
            set x = temp;
        }
        return x;
    }
}
```

---

## Resource Requirements for Real Factoring

| Key Size | Logical Qubits | Physical Qubits | Time |
|----------|---------------|-----------------|------|
| RSA-1024 | ~2,048 | ~4 million | Hours |
| RSA-2048 | ~4,096 | ~20 million | ~10 hours |
| RSA-4096 | ~8,192 | ~80+ million | Days |

Current largest quantum computers: ~1,000 physical qubits (NISQ era).
We are still **decades away** from breaking real RSA.

---

## Exercises

### Exercise 1
Implement the QFT for n=3 qubits and verify it produces the expected output for `|0⟩`, `|1⟩`, `|2⟩`.

### Exercise 2
Factor N=21 (= 3 × 7) classically using the period-finding approach. Choose a=2 and find the period manually.

### Exercise 3
Look up the current world record for the largest number factored on a quantum computer. How does it compare to RSA key sizes?

---

*Next: [04 — VQE](04-vqe.md)*
