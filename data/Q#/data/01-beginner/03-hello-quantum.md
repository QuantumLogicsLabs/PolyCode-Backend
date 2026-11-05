# Lesson 03 — Your First Qubit Program

**Module 01 · Beginner · Lesson 03 of 06**

## Learning objectives

- Allocate a qubit, transform it, measure it, and restore it safely.
- Separate a single-shot outcome from a repeated-shot probability estimate.
- Interpret simulator output without treating one sample as a guarantee.

## First circuit

```qsharp
@EntryPoint()
operation Main() : Unit {
    use q = Qubit();
    H(q);
    let result = M(q);
    Message($"Measured: {result}");
    Reset(q);
}
```

Each execution prepares `(|0> + |1>)/sqrt(2)` and measures once. A single run may return either result. Repeating the operation estimates the distribution; it does not change the probability of an individual ideal shot.

Q# `use` scopes qubit ownership. Before the scope ends, the qubit must be returned to `|0>`; explicit `Reset` makes that postcondition clear even when the measurement returned `One`.

## Lab

Replace `H` with `X` and predict the result. Restore `H`, run many independent shots using the project’s supported simulator/runner, and compare the observed fraction of `One` with one half. Record the shot count and observed fraction; do not expect an exact 50/50 split.

## Checkpoint

You can explain why a measured qubit must be reset and why repeated shots provide statistical evidence rather than a deterministic sequence.

**Next:** [Operations, functions, and data](04-operations.md)
