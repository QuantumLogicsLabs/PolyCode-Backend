# 04 — Variational Quantum Eigensolver (VQE)

## What Is VQE?

VQE is a **hybrid quantum-classical** algorithm designed for NISQ (current-era noisy) devices. It finds the ground state energy of a quantum system — crucial for:

- Drug discovery (molecular simulation)
- Materials science (finding stable materials)
- Quantum chemistry (reaction rates, properties)

---

## The Variational Principle

The foundation: for any trial state |ψ(θ)⟩, the expectation value of the Hamiltonian H is an **upper bound** on the ground state energy E₀:

```
⟨ψ(θ)|H|ψ(θ)⟩ ≥ E₀
```

VQE minimizes this expectation value by varying parameters θ.

---

## VQE Loop

```
                    ┌─────────────────────┐
                    │   Classical         │
                    │   Optimizer         │
                    │   (COBYLA, BFGS...) │
                    └────────┬────────────┘
                             │ Update θ
                             ▼
┌──────────────────────────────────────────┐
│   Quantum Computer                        │
│                                          │
│   1. Prepare ansatz: |ψ(θ)⟩              │
│   2. Measure: ⟨H⟩ = ⟨ψ(θ)|H|ψ(θ)⟩      │
│   3. Return energy estimate              │
└──────────────────────────────────────────┘
             │
             ▼
     Converged? → Output ground state energy
```

---

## The Ansatz

The **ansatz** (|ψ(θ)⟩) is a parameterized quantum circuit. A good ansatz should:
- Be expressive enough to represent the ground state
- Have few parameters (to avoid barren plateaus)
- Be efficient to implement on hardware

### Hardware-Efficient Ansatz
```qsharp
operation HardwareEfficientAnsatz(params : Double[], qubits : Qubit[]) : Unit is Adj + Ctl {
    let n = Length(qubits);
    let layers = Length(params) / (3 * n);
    
    mutable paramIdx = 0;
    
    for layer in 0..layers-1 {
        // Single-qubit rotations
        for i in 0..n-1 {
            Rx(params[paramIdx], qubits[i]);
            set paramIdx += 1;
            Ry(params[paramIdx], qubits[i]);
            set paramIdx += 1;
            Rz(params[paramIdx], qubits[i]);
            set paramIdx += 1;
        }
        
        // Entangling layer
        for i in 0..n-2 {
            CNOT(qubits[i], qubits[i+1]);
        }
    }
}
```

### UCCSD Ansatz (for Chemistry)
The Unitary Coupled Cluster Singles and Doubles (UCCSD) ansatz is chemically motivated:

```qsharp
// Conceptual structure of UCCSD
operation UCCSDLayer(
    singlesAmplitudes : Double[],
    doublesAmplitudes : Double[],
    qubits : Qubit[]
) : Unit is Adj + Ctl {
    // Single excitations: e^(t_ia * a†_a a_i - h.c.)
    for (i, amp) in Enumerated(singlesAmplitudes) {
        Ry(amp, qubits[i]);
    }
    
    // Double excitations: e^(t_ijab * a†_a a†_b a_i a_j - h.c.)
    // More complex, involves 4 qubits each
    // ...
}
```

---

## Energy Measurement (Hamiltonian)

The Hamiltonian is expressed as a sum of Pauli strings:

```
H = c₀·I + c₁·Z₀ + c₂·Z₁ + c₃·Z₀Z₁ + c₄·X₀X₁ + c₅·Y₀Y₁ + ...
```

Measure each term separately:

```qsharp
operation MeasurePauliString(
    paulis : Pauli[],
    coefficients : Double[],
    qubits : Qubit[]
) : Double {
    mutable energy = 0.0;
    
    for (pauli, coeff) in Zip(paulis, coefficients) {
        // Measure expectation value of this Pauli term
        let expectation = EstimateFrequency(
            () => MeasurePauli(pauli, qubits[0]),
            Zero,
            1000  // shots
        );
        set energy += coeff * (1.0 - 2.0 * expectation);
    }
    
    return energy;
}
```

---

## Hydrogen Molecule (H₂) Example

The simplest chemically relevant VQE problem:

```qsharp
namespace H2VQE {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Math;
    
    // H₂ Hamiltonian at equilibrium (simplified, 2-qubit)
    // H = g₀·I + g₁·Z₀ + g₂·Z₁ + g₃·Z₀Z₁ + g₄·X₀X₁ + g₅·Y₀Y₁
    
    operation VQEAnsatz(theta : Double, qubits : Qubit[]) : Unit is Adj {
        // Prepare Hartree-Fock reference state |01⟩
        X(qubits[1]);
        
        // Single GIVENS rotation (2-parameter UCCSD for H₂)
        Ry(theta, qubits[0]);
        CNOT(qubits[0], qubits[1]);
    }
    
    operation MeasureH2Energy(theta : Double) : Double {
        // Coefficients for H₂ at 0.74 Å bond length
        let g0 =  -0.4804;
        let g1 =  +0.3435;
        let g2 =  -0.4347;
        let g3 =  +0.5716;
        let g4 =  +0.0910;
        let g5 =  +0.0910;
        
        // Measure each Pauli term (in real VQE this is done in parallel shots)
        use qubits = Qubit[2];
        VQEAnsatz(theta, qubits);
        
        // Measure ZZ term: ⟨Z₀Z₁⟩
        let zzResult = Measure([PauliZ, PauliZ], qubits);
        let zzExp = zzResult == Zero ? 1.0 | -1.0;
        
        // Measure XX term: ⟨X₀X₁⟩
        let xxResult = Measure([PauliX, PauliX], qubits);
        let xxExp = xxResult == Zero ? 1.0 | -1.0;
        
        // Measure YY term: ⟨Y₀Y₁⟩
        let yyResult = Measure([PauliY, PauliY], qubits);
        let yyExp = yyResult == Zero ? 1.0 | -1.0;
        
        ResetAll(qubits);
        
        return g0 + g1 * 1.0 + g2 * 1.0 + g3 * zzExp + g4 * xxExp + g5 * yyExp;
    }
}
```

---

## Classical Optimization

The classical optimizer adjusts parameters to minimize energy:

```python
# Python side (using scipy)
from scipy.optimize import minimize
import qsharp

def energy_function(params):
    theta = params[0]
    return qsharp.eval(f"H2VQE.MeasureH2Energy({theta})")

result = minimize(energy_function, x0=[0.1], method='COBYLA')
print(f"Ground state energy: {result.fun} Hartree")
print(f"Optimal theta: {result.x[0]}")
```

---

## Challenges with VQE

| Challenge | Description |
|-----------|-------------|
| **Barren plateaus** | Gradient vanishes exponentially for deep circuits |
| **Shot noise** | Many measurements needed per evaluation |
| **Hardware noise** | Gate errors corrupt results |
| **Classical optimizer** | Can get stuck in local minima |

---

## Exercises

### Exercise 1
Implement a 1-qubit VQE for the Hamiltonian H = Z₀. The ground state should be |1⟩ with energy -1.

### Exercise 2
Implement the full H₂ VQE above. Plot energy vs theta to find the minimum visually.

### Exercise 3
Research: what is the "quantum advantage threshold" for VQE? When does it outperform classical chemistry methods?

---

*Next: [05 — QAOA](05-qaoa.md)*
