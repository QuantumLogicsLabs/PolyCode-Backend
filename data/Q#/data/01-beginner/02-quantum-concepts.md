# Lesson 02 — Quantum States, Phase, and Measurement

**Module 01 · Beginner · Lesson 02 of 06**

## Learning objectives

- Represent a pure qubit state as amplitudes over computational basis states.
- Relate amplitude magnitude to measurement probability.
- Explain why relative phase matters even when it is not directly observable.

## State model

A normalized qubit can be written `alpha |0> + beta |1>`, where `|alpha|^2 + |beta|^2 = 1`. A computational-basis measurement returns `Zero` with probability `|alpha|^2` and `One` with probability `|beta|^2`. The result is a sample, not a readout of both amplitudes.

The Hadamard gate maps `|0>` to an equal superposition. Applying a phase gate can leave basis-state probabilities unchanged yet change later interference. For example, `H`, then `Z`, then `H` maps `|0>` to `|1>`; omitting `Z` maps it back to `|0>`.

## Circuit in Q#

```qsharp
operation InterferenceExample() : Result {
    use q = Qubit();
    H(q);
    Z(q);
    H(q);
    let result = M(q);
    Reset(q);
    return result;
}
```

The reset is required before the allocated qubit leaves scope. Repeatedly run the operation: this circuit is deterministic despite its intermediate superposition, because the final interference concentrates probability on `One`.

## Lab

Compare `H; H` with `H; Z; H`. Predict the final result before running each. Explain why measuring between the gates changes the computation.

## Checkpoint

You can distinguish a basis measurement from a state description, and explain interference using relative phase rather than “trying every answer at once.”

**Next:** [A first Q# program with a qubit](03-hello-quantum.md)
