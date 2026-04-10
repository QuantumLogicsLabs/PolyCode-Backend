# 01 — Qubit Mathematics

## Dirac Notation (Bra-Ket Notation)

Quantum states are written using **bra-ket notation**, invented by Paul Dirac:

- **Ket** `|ψ⟩` — a quantum state (column vector)
- **Bra** `⟨ψ|` — the conjugate transpose of a ket (row vector)
- **Braket** `⟨φ|ψ⟩` — inner product (overlap/probability amplitude)

---

## The Computational Basis

The two basis states for a single qubit:

```
|0⟩ = [1]    |1⟩ = [0]
      [0]          [1]
```

Every qubit state is a **superposition** of these:

```
|ψ⟩ = α|0⟩ + β|1⟩ = [α]
                      [β]
```

Where α and β are **complex amplitudes** satisfying:

```
|α|² + |β|² = 1    (normalization constraint)
```

The probability of measuring `0` is `|α|²`, and measuring `1` is `|β|²`.

---

## Common States

| State | Dirac Notation | Vector | Description |
|-------|---------------|--------|-------------|
| Zero | `|0⟩` | `[1, 0]ᵀ` | Classical 0 |
| One | `|1⟩` | `[0, 1]ᵀ` | Classical 1 |
| Plus | `|+⟩` | `[1/√2, 1/√2]ᵀ` | Superposition, H|0⟩ |
| Minus | `|-⟩` | `[1/√2, -1/√2]ᵀ` | Superposition, H|1⟩ |
| Plus i | `|i⟩` | `[1/√2, i/√2]ᵀ` | Y-basis |
| Minus i | `|-i⟩` | `[1/√2, -i/√2]ᵀ` | Y-basis |

---

## The Bloch Sphere

Any pure qubit state can be visualized as a point on the **Bloch sphere**:

```
                    |0⟩ (North Pole)
                     │
          |+i⟩ ─────┼───── |-i⟩
         /           │           \
       |+⟩           │            |-⟩
         \           │           /
          └──────────┼──────────┘
                     │
                    |1⟩ (South Pole)
```

Parameterization:
```
|ψ⟩ = cos(θ/2)|0⟩ + e^(iφ) sin(θ/2)|1⟩
```

- `θ` (theta) — polar angle (0 = north pole = |0⟩, π = south pole = |1⟩)
- `φ` (phi) — azimuthal angle (phase)

---

## DumpMachine Output Explained

When you call `DumpMachine()` in Q#:

```
∣0❭:  0.707107 + 0.000000 i  ==  █████  [ 0.500000 ]  ---  [  0.00000 rad ]
∣1❭:  0.707107 + 0.000000 i  ==  █████  [ 0.500000 ]  ---  [  0.00000 rad ]
```

Columns:
- `∣0❭` — basis state label
- `0.707107 + 0.000000 i` — complex amplitude α
- `[ 0.500000 ]` — probability = |α|² = 0.707² ≈ 0.5
- `[ 0.00000 rad ]` — phase angle

---

## Global vs Relative Phase

**Global phase** (e^(iφ) applied to whole state) is unobservable:
```
|ψ⟩ = e^(iφ)(α|0⟩ + β|1⟩)   ← same physical state as α|0⟩ + β|1⟩
```

**Relative phase** (between |0⟩ and |1⟩ components) IS observable:
```
|+⟩ = (|0⟩ + |1⟩)/√2   ← different from
|-⟩ = (|0⟩ - |1⟩)/√2   ← this (different relative phase)
```

Interference effects rely on relative phase!

---

## Mixed vs Pure States

- **Pure state**: complete quantum information, described by `|ψ⟩`
- **Mixed state**: statistical mixture, described by density matrix ρ

Q# simulators work with pure states. Mixed states arise from:
- Entanglement with unmeasured qubits
- Environmental noise (decoherence)

---

## Q# Connection

```qsharp
open Microsoft.Quantum.Diagnostics;
open Microsoft.Quantum.Math;

operation ExploreStates() : Unit {
    use q = Qubit();

    // |0⟩ state
    Message("=== |0⟩ state ===");
    DumpMachine();

    // |+⟩ state (after H)
    H(q);
    Message("\n=== |+⟩ state (after H) ===");
    DumpMachine();

    // Rotate on Bloch sphere
    Rx(PI() / 4.0, q);
    Message("\n=== After Rx(π/4) ===");
    DumpMachine();

    Reset(q);
}
```

---

## Exercises

### Exercise 1
What is the probability of measuring `Zero` for each of these states?
- `|0⟩`
- `|1⟩`
- `|+⟩ = (|0⟩ + |1⟩)/√2`
- `(√3/2)|0⟩ + (1/2)|1⟩`

### Exercise 2
Use `DumpMachine()` to verify the state after each of these gate sequences:
- Start |0⟩ → apply H → should show |+⟩
- Start |0⟩ → apply X → should show |1⟩
- Start |0⟩ → apply H → apply Z → should show |-⟩

### Exercise 3
Calculate: what is |α|² + |β|² for `α = 1/√3, β = √(2/3)`? Is this a valid quantum state?

---

*Next: [02 — Single-Qubit Gates](02-single-qubit-gates.md)*
