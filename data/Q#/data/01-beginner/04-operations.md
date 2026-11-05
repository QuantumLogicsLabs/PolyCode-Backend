# Lesson 04 — Q# Operations, Functions, and Data

**Module 01 · Beginner · Lesson 04 of 06**

## Learning objectives

- Choose between a classical `function` and a quantum `operation`.
- Use immutable bindings and typed arguments to make circuit intent explicit.
- Package related classical data in a user-defined type.

## Language patterns

Functions compute classical values and cannot allocate or manipulate qubits. Operations may perform quantum actions and can call other operations. Prefer a function for preprocessing and postprocessing; keep state-changing gates in operations. Q# types such as `Int`, `Double`, `Bool`, `Result`, tuples, and arrays make interfaces explicit.

```qsharp
function Square(x : Int) : Int {
    x * x
}

operation PrepareBasis(bit : Bool, target : Qubit) : Unit {
    if (bit) {
        X(target);
    }
}
```

`Square` is classical. `PrepareBasis` acts on a qubit; its `Bool` input selects a preparation, not a way to inspect the qubit.

## Lab

Define a small UDT for a problem configuration, then write one function that validates/transforms its classical fields and one operation that consumes the result to prepare a circuit. Keep quantum resources out of functions. Add boundary cases to the classical helper (including an empty or invalid configuration) and document the intended behavior.

## Checkpoint

You can explain why a helper that applies `H` must be an operation, while a helper that computes a loop bound can be a function.

**Next:** [Qubit registers and safe lifetime](05-qubits.md)
