# Lesson 06 — Measurement, Reset, and Feedback

**Module 01 · Beginner · Lesson 06 of 06**

## Learning objectives

- Use `M` to convert a quantum state into a classical `Result`.
- Apply classical branching on a measurement result without confusing it with coherent control.
- State the reset obligations for measured and unmeasured qubits.

## Measurement semantics

Measurement is probabilistic in general and changes the state. Its `Result` is classical data that can select a later operation. This measurement-conditioned branch is not the same as a controlled gate, which acts coherently without first observing its control.

```qsharp
operation MeasureAndCorrect() : Result {
    use q = Qubit();
    H(q);
    let r = M(q);
    if (r == One) {
        X(q);
    }
    let final = M(q);
    Reset(q);
    return final;
}
```

After the first measurement, `q` is in the corresponding basis state; the branch maps either measured outcome to `Zero` before the final measurement. This is a simple feedback pattern, not an error-correction code.

## Lab

Replace the conditional correction with a second unconditional `M` and compare the distributions. Explain which result controls the next gate, and why the final measurement is deterministic in the original circuit. Ensure all allocated qubits are reset on every control-flow path.

## Checkpoint

You have completed the foundations. Before continuing, be able to trace allocation, gates, measurement, classical feedback, and reset in a small Q# operation.

**Next:** [Oracle functions](../02-intermediate/01-oracle-functions.md)
