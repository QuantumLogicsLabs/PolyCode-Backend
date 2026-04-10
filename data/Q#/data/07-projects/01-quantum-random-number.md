# Project 01 — Quantum Random Number Generator

## Overview

Build a true quantum random number generator using the inherent randomness of quantum measurement. Unlike pseudo-random number generators (PRNGs), quantum measurements are provably random.

**Difficulty:** 🟢 Beginner  
**Estimated time:** 2–3 hours

---

## Background

Classical computers are deterministic — given the same seed, they produce the same "random" numbers. Quantum measurement, however, is **fundamentally non-deterministic**. When you measure a qubit in superposition, nature itself chooses the outcome — no algorithm can predict it.

---

## Requirements

### Minimum Viable Product
1. Generate a single random bit (0 or 1)
2. Generate a random integer in range [0, max)
3. Generate a random array of bits
4. Demonstrate uniformity with statistics

### Stretch Goals
- Random floating point in [0, 1)
- Random permutation of an array
- Cryptographically secure string generation
- Test randomness with NIST statistical tests

---

## Implementation Guide

### Step 1: Random Bit

```qsharp
operation RandomBit() : Int {
    use q = Qubit();
    H(q);  // Superposition → truly random
    let result = M(q);
    Reset(q);
    return result == Zero ? 0 | 1;
}
```

### Step 2: Random Integer

```qsharp
operation RandomInt(max : Int) : Int {
    // Find how many bits we need
    mutable n = 0;
    mutable temp = max - 1;
    while temp > 0 {
        set n += 1;
        set temp >>>= 1;
    }
    
    // Generate random n-bit number, retry if out of range
    mutable result = max;
    repeat {
        set result = RandomNBits(n);
    } until (result < max);
    
    return result;
}

operation RandomNBits(n : Int) : Int {
    use qs = Qubit[n];
    ApplyToEach(H, qs);
    
    mutable value = 0;
    for i in 0..n-1 {
        let bit = M(qs[i]);
        if bit == One {
            set value += 1 <<< i;
        }
    }
    
    ResetAll(qs);
    return value;
}
```

### Step 3: Statistical Validation

```qsharp
@EntryPoint()
operation ValidateRandomness() : Unit {
    let samples = 1000;
    let max = 6;  // Simulate a dice
    
    mutable counts = [0, size = max];
    
    for _ in 1..samples {
        let roll = RandomInt(max);
        set counts w/= roll <- counts[roll] + 1;
    }
    
    Message($"Dice roll distribution over {samples} samples:");
    for i in 0..max-1 {
        let pct = IntAsDouble(counts[i]) / IntAsDouble(samples) * 100.0;
        Message($"  {i+1}: {counts[i]} ({pct:F1}%) — expected {100.0/IntAsDouble(max):F1}%");
    }
}
```

---

## Project Tasks

### Task 1: Core Implementation
Implement `RandomBit()`, `RandomNBits(n)`, and `RandomInt(max)`.

### Task 2: Statistics
Run 10,000 samples of `RandomBit()` and plot the distribution. Is it close to 50/50?

### Task 3: Random Float
Implement `RandomDouble() : Double` that returns a random float in [0, 1). 

Hint: generate 53 bits and scale to [0, 1).

### Task 4: Shuffle
Implement `Shuffle(arr : Int[]) : Int[]` using the Fisher-Yates algorithm with `RandomInt`.

### Task 5: Password Generator
Using your QRNG, generate a cryptographically strong random 256-bit key (as a hex string).

---

## Testing

```qsharp
@Test("QuantumSimulator")
operation TestRandomBitIsZeroOrOne() : Unit {
    for _ in 1..100 {
        let bit = RandomBit();
        Fact(bit == 0 or bit == 1, "RandomBit must return 0 or 1");
    }
}

@Test("QuantumSimulator")
operation TestRandomIntInRange() : Unit {
    for _ in 1..100 {
        let n = RandomInt(10);
        Fact(n >= 0 and n < 10, "RandomInt must be in [0, max)");
    }
}
```

---

## Extension: Quantum Entropy Source

Research question: How does quantum randomness compare to classical entropy sources (OS entropy, hardware RNG)? Write a short analysis.

---

*Next Project: [02 — BB84 Quantum Key Distribution](02-quantum-key-distribution.md)*
