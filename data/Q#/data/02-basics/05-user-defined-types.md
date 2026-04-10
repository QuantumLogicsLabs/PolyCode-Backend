# 05 — User-Defined Types (UDTs)

## What Are UDTs?

User-defined types let you create **named, structured types** with semantic meaning. They improve readability and provide type safety.

```qsharp
// Without UDT: ambiguous
operation Rotate(q : Qubit, angle : Double, axis : Int) : Unit { ... }

// With UDT: clear and safe
newtype Angle = Double;
newtype Axis  = Int;

operation Rotate(q : Qubit, angle : Angle, axis : Axis) : Unit { ... }
```

---

## Defining UDTs

### Simple Wrapper
```qsharp
newtype Probability = Double;
newtype QubitIndex  = Int;
newtype Angle       = Double;
```

### Named Fields (Record-like)
```qsharp
newtype Complex = (Real : Double, Imag : Double);

newtype Rotation = (
    Angle : Double,
    Axis  : Pauli,
    Qubit : Int
);

newtype QuantumCircuit = (
    NumQubits   : Int,
    NumGates    : Int,
    Depth       : Int,
    Operations  : String[]
);
```

---

## Creating and Accessing UDTs

```qsharp
// Create
let c = Complex(3.0, 4.0);
let r = Rotation(1.57, PauliX, 0);

// Access named fields with ::
let real = c::Real;    // 3.0
let imag = c::Imag;    // 4.0
let angle = r::Angle;  // 1.57

// Destructure
let Complex(re, im) = c;

// Copy-and-update
let c2 = c w/ Real <- 5.0;   // (5.0, 4.0)
```

---

## UDTs Are Not Type Aliases

UDTs create **new types** — they don't implicitly convert:

```qsharp
newtype Meters = Double;
newtype Kilograms = Double;

let d : Meters    = Meters(5.0);
let m : Kilograms = Kilograms(5.0);

// let bad : Meters = m;  // ❌ Type error! Different types.

// Unwrap explicitly
let raw : Double = d!;   // Unwrap UDT with !
```

### Unwrapping with `!`

```qsharp
newtype Angle = Double;
let a = Angle(3.14);
let raw = a!;          // raw : Double = 3.14

// With named fields, unwrap the whole tuple
newtype Complex = (Real : Double, Imag : Double);
let c = Complex(1.0, 2.0);
let (re, im) = c!;     // Unwrap to (Double, Double)
```

---

## Practical Example: Quantum Circuit Metadata

```qsharp
newtype GateCount = (
    SingleQubitGates : Int,
    TwoQubitGates    : Int,
    Measurements     : Int
);

function TotalGates(gc : GateCount) : Int {
    return gc::SingleQubitGates + gc::TwoQubitGates;
}

function AddGateCounts(a : GateCount, b : GateCount) : GateCount {
    return GateCount(
        a::SingleQubitGates + b::SingleQubitGates,
        a::TwoQubitGates    + b::TwoQubitGates,
        a::Measurements     + b::Measurements
    );
}

// Usage
let myCircuit = GateCount(15, 8, 3);
Message($"Total gates: {TotalGates(myCircuit)}");
```

---

## UDTs for Quantum State Representation

```qsharp
newtype BlochVector = (X : Double, Y : Double, Z : Double);

function PureStateToBloch(alpha : Complex, beta : Complex) : BlochVector {
    // |ψ⟩ = α|0⟩ + β|1⟩
    let bx = 2.0 * (alpha::Real * beta::Real + alpha::Imag * beta::Imag);
    let by = 2.0 * (alpha::Real * beta::Imag - alpha::Imag * beta::Real);
    let bz = alpha::Real * alpha::Real + alpha::Imag * alpha::Imag
           - beta::Real  * beta::Real  - beta::Imag  * beta::Imag;
    return BlochVector(bx, by, bz);
}
```

---

## Exercises

### Exercise 1
Define a `Fraction` UDT with `Numerator : Int` and `Denominator : Int`. Write functions `Add`, `Multiply`, and `Simplify` for it.

### Exercise 2
Define a `QuantumState` UDT that holds the number of qubits and a description string. Write an operation that measures all qubits and updates the state.

### Exercise 3
Create a `CircuitStats` UDT and write a function that analyzes a list of operation names and returns the stats (count single-qubit, two-qubit, measurement operations).

---

*Next: [06 — Functors](06-functors.md)*
