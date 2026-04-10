# 04 — Hello, Quantum World!

## Your First Q# Program

Let's write the quintessential quantum program: **measuring a qubit in superposition**.

This is the quantum equivalent of "Hello, World!" — it demonstrates the fundamental randomness of quantum measurement.

---

## The Code

```qsharp
// Program.qs
namespace HelloQuantum {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Measurement;
    open Microsoft.Quantum.Diagnostics;

    @EntryPoint()
    operation Main() : Unit {
        // Allocate one qubit
        use q = Qubit();

        // The qubit starts in state |0⟩
        Message("Initial state:");
        DumpMachine();

        // Apply Hadamard gate → creates superposition
        H(q);
        Message("\nAfter Hadamard gate (superposition):");
        DumpMachine();

        // Measure the qubit
        let result = M(q);
        Message($"\nMeasurement result: {result}");

        // Reset qubit before releasing (required!)
        Reset(q);
    }
}
```

---

## Breaking It Down

### Namespace
```qsharp
namespace HelloQuantum { ... }
```
Every Q# file belongs to a namespace. Think of it like a package or module name.

### Open Statements
```qsharp
open Microsoft.Quantum.Intrinsic;
```
Import libraries — like `import` in Python or `using` in C#.

### @EntryPoint()
```qsharp
@EntryPoint()
operation Main() : Unit { ... }
```
Marks the entry point of the program. `Unit` means "returns nothing" (like `void`).

### Allocating Qubits
```qsharp
use q = Qubit();
```
The `use` statement allocates a qubit. It's automatically deallocated at the end of the block. **Qubits always start in state |0⟩.**

### The Hadamard Gate
```qsharp
H(q);
```
The `H` (Hadamard) gate puts the qubit into superposition:
- |0⟩ → (|0⟩ + |1⟩) / √2
- Now measuring gives 50% chance of 0, 50% chance of 1

### Measurement
```qsharp
let result = M(q);
```
`M` measures the qubit, returning `Zero` or `One`. This collapses the superposition.

### Reset
```qsharp
Reset(q);
```
Always reset qubits before releasing them — this is required in Q#.

---

## Running the Program

```bash
dotnet run
```

Sample output:
```
Initial state:
# wave function
∣0❭:     1.000000 +  0.000000 i  ==     ██████████ [ 1.000000 ]     --- [  0.00000 rad ]

After Hadamard gate (superposition):
∣0❭:     0.707107 +  0.000000 i  ==     █████      [ 0.500000 ]     --- [  0.00000 rad ]
∣1❭:     0.707107 +  0.000000 i  ==     █████      [ 0.500000 ]     --- [  0.00000 rad ]

Measurement result: One
```

Run it multiple times — you'll get `Zero` and `One` approximately 50% each.

---

## DumpMachine

`DumpMachine()` is your best debugging friend. It prints the current quantum state:

```
∣0❭:  0.707107 + 0.000000 i  ==  █████  [ 0.500000 ]
∣1❭:  0.707107 + 0.000000 i  ==  █████  [ 0.500000 ]
```

- `∣0❭` and `∣1❭` are the basis states
- The complex numbers are **amplitudes**
- The probability is `|amplitude|²` — so 0.707² ≈ 0.5 (50%)

---

## Exercises

### Exercise 1: Flip a Qubit
Write an operation that starts a qubit in |0⟩, flips it to |1⟩, and measures it. The result should always be `One`.

```qsharp
operation FlipQubit() : Result {
    use q = Qubit();
    // TODO: Apply X gate to flip the qubit
    let result = M(q);
    Reset(q);
    return result;
}
```

<details>
<summary>Solution</summary>

```qsharp
operation FlipQubit() : Result {
    use q = Qubit();
    X(q);  // X gate flips |0⟩ to |1⟩
    let result = M(q);
    Reset(q);
    return result;
}
```
</details>

### Exercise 2: Run Multiple Times
Modify `Main()` to allocate a qubit, apply H, measure it, and repeat 10 times. Count how many `Zero` and `One` results you get.

### Exercise 3: Use DumpMachine
Place `DumpMachine()` calls at different points in Exercise 1 to observe how the state changes.

---

*Next Module: [02 — Q# Basics](../02-basics/README.md)*
