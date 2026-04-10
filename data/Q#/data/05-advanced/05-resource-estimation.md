# 05 — Quantum Resource Estimation

## Why Resource Estimation Matters

Running algorithms on real quantum hardware requires knowing:
- **How many qubits** do I need?
- **How many T-gates** (the expensive fault-tolerant gate)?
- **What circuit depth** (time to execute)?
- **What's the logical error rate?**

The **Azure Quantum Resource Estimator** answers these questions without running the circuit.

---

## Setting Up Resource Estimation

```xml
<!-- In your .csproj file, add: -->
<PackageReference Include="Microsoft.Quantum.Numerics" Version="*" />
```

```qsharp
// Example operation to estimate
namespace ResourceDemo {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Canon;
    
    operation TargetOperation(n : Int) : Unit {
        use qs = Qubit[n];
        ApplyToEach(H, qs);
        for i in 0..n-2 {
            CNOT(qs[i], qs[i+1]);
        }
        ResetAll(qs);
    }
}
```

---

## Using the Azure Resource Estimator

Via VS Code:
1. Open the `.qs` file
2. Click "Estimate" in the Q# toolbar
3. Configure qubit model and error rate

Via CLI:
```bash
# Install resource estimator
dotnet add package Microsoft.Azure.Quantum.ResourceEstimator

# Run estimation
dotnet run --target microsoft.estimator
```

---

## Understanding the Output

```json
{
  "physicalQubits": 12514,
  "runtime": "3 seconds",
  "rqops": 1.06e9,
  "logicalQubit": {
    "codeDistance": 15,
    "physicalQubits": 450
  },
  "breakdown": {
    "algorithmicLogicalQubits": 27,
    "rotationCount": 3731,
    "tCount": 12012,
    "measurementCount": 17530
  }
}
```

Key fields:
- `physicalQubits` — total hardware qubits needed
- `codeDistance` — error correction strength
- `tCount` — number of T-gates (most expensive resource)
- `runtime` — wall-clock time on quantum hardware

---

## Gate Costs in Fault-Tolerant Computing

Not all gates are equal in fault-tolerant quantum computing:

| Gate | Cost | Notes |
|------|------|-------|
| Clifford (H, S, CNOT, CZ) | Cheap | Transversal in many codes |
| T gate | Expensive | Requires magic state distillation |
| Rotation Rz(θ) | Very expensive | Approximated by ~1.15 log₂(1/ε) T-gates |
| Measurement | Medium | Relatively cheap |

The **T-count** (number of T-gates) is the primary resource metric for fault-tolerant algorithms.

---

## Qubit Models

The Resource Estimator supports different hardware models:

```python
# QubitParams: different noise models
{
    "name": "qubit_gate_ns_e3",   # Superconducting, 1ns gates, 1e-3 error
    "name": "qubit_gate_us_e3",   # Superconducting, 1μs gates, 1e-3 error  
    "name": "qubit_ion_us_e4",    # Trapped ion, 1μs gates, 1e-4 error
    "name": "qubit_maj_ns_e4"     # Topological (Majorana), 1ns, 1e-4 error
}
```

---

## Optimizing T-Count

Since T-gates dominate cost, optimize by:

### 1. Use Clifford gates when possible
```qsharp
// ❌ Expensive: 2 T-gates
T(q);
T(q);

// ✅ Cheaper: S is Clifford
S(q);   // S = T²
```

### 2. T-gate cancellation
```qsharp
// ❌ Wasteful: T then T† cancel out
T(q);
Adjoint T(q);

// ✅ Remove both (they cancel)
// (empty)
```

### 3. Use library operations optimized for T-count
```qsharp
open Microsoft.Quantum.Arithmetic;

// The library's operations are T-count optimized
AddI(xs, ys);   // Quantum integer addition (optimized)
```

---

## Sample Estimation: Grover's Search

```qsharp
open Microsoft.Quantum.Diagnostics;

operation GroversSearch(n : Int, iterations : Int) : Unit {
    use qs = Qubit[n];
    
    // Hadamard all qubits
    ApplyToEach(H, qs);
    
    // Grover iterations
    for _ in 1..iterations {
        // Oracle (placeholder)
        Controlled Z(qs[0..n-2], qs[n-1]);
        
        // Diffusion operator
        ApplyToEach(H, qs);
        ApplyToEach(X, qs);
        Controlled Z(qs[0..n-2], qs[n-1]);
        ApplyToEach(X, qs);
        ApplyToEach(H, qs);
    }
    
    ResetAll(qs);
}
```

Running the estimator on this shows Grover's algorithm's resource requirements for different problem sizes.

---

## Exercises

### Exercise 1
Run the Resource Estimator on the `GroversSearch` operation for n=10, 20, 30. Plot how physical qubit count grows.

### Exercise 2
Compare resource estimates for:
- `CCNOT` using the default decomposition
- `CCNOT` using the Toffoli decomposition from `Microsoft.Quantum.Canon`

Which uses fewer T-gates?

### Exercise 3
For a meaningful real-world algorithm (Shor's algorithm for 2048-bit RSA), the resource estimate is approximately 20 million physical qubits. Use the estimator on a simplified version to explore the relationship between problem size and qubit requirements.

---

*Next: [06 — Advanced Patterns](06-advanced-patterns.md)*
