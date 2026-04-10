# 01 — What Is Quantum Computing?

## Classical vs Quantum Computing

Classical computers store information as **bits** — either `0` or `1`. Every operation you've ever done on a computer — loading a webpage, playing a video, running code — boils down to manipulating billions of these 0s and 1s.

Quantum computers use **qubits** (quantum bits). A qubit can be 0, 1, or — here's the magic — **both at the same time** through a phenomenon called *superposition*.

### The Key Quantum Phenomena

#### 1. Superposition
A qubit in superposition is in a combination of 0 and 1 simultaneously. When you *measure* it, it collapses to either 0 or 1.

Think of it like a spinning coin: while spinning, it's neither heads nor tails. The moment it lands, it becomes one or the other.

#### 2. Entanglement
Two qubits can be **entangled** — meaning the state of one instantly determines the state of the other, no matter the distance between them.

Einstein called this "spooky action at a distance." It's real, and it's incredibly useful for quantum algorithms.

#### 3. Interference
Quantum algorithms use interference to amplify the probability of correct answers and cancel out wrong ones — like noise-canceling headphones, but for computation.

---

## What Can Quantum Computers Do Better?

Quantum computers are **not** universally faster. They excel at specific problem classes:

| Problem Type | Example | Quantum Advantage |
|-------------|---------|-------------------|
| Factoring large numbers | RSA cryptography | Exponential (Shor's algorithm) |
| Searching unsorted data | Database search | Quadratic (Grover's algorithm) |
| Simulating quantum systems | Drug discovery | Exponential |
| Optimization problems | Logistics, finance | Polynomial–Exponential |

> ⚠️ **Common misconception:** Quantum computers won't make your browser faster. They solve specific mathematical problems that classical computers struggle with.

---

## The Quantum Computing Stack

```
┌─────────────────────────────────────┐
│         Applications / Algorithms   │  ← You write this in Q#
├─────────────────────────────────────┤
│         Quantum Software (QDK/Q#)   │  ← Microsoft's tools
├─────────────────────────────────────┤
│         Quantum Simulators          │  ← Run on classical hardware
├─────────────────────────────────────┤
│         Quantum Hardware            │  ← Real quantum chips
└─────────────────────────────────────┘
```

---

## Why Q#?

Q# (Q-sharp) is Microsoft's domain-specific language for quantum computing. It's designed to:

- Express quantum algorithms naturally and clearly
- Scale from small simulations to real quantum hardware
- Integrate with classical .NET code (C#, Python)
- Run on simulators and Azure Quantum hardware

Unlike other quantum frameworks (Qiskit, Cirq) that are Python libraries, Q# is a **first-class language** built specifically for quantum programming.

---

## Key Concepts to Remember

- **Qubit**: The basic unit of quantum information
- **Superposition**: A qubit can be 0, 1, or both simultaneously
- **Entanglement**: Correlated qubits whose states are linked
- **Interference**: Amplifying correct answers, canceling wrong ones
- **Measurement**: Collapsing a quantum state to a classical 0 or 1

---

## 📝 Self-Check Questions

1. What is the difference between a bit and a qubit?
2. Name the three core quantum phenomena.
3. What type of problems do quantum computers excel at?
4. Why is Q# different from Python quantum libraries like Qiskit?

---

*Next: [02 — The Q# Ecosystem](02-qsharp-ecosystem.md)*
