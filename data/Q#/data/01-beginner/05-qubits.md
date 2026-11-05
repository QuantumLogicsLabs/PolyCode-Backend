# Lesson 05 — Qubit Registers and Safe Lifetime

**Module 01 · Beginner · Lesson 05 of 06**

## Learning objectives

- Allocate a register and address its elements deliberately.
- Distinguish classical control flow from coherent quantum control.
- Preserve the zero-state lifetime invariant for locally allocated qubits.

## Registers and ownership

An array of qubits is a collection of distinct quantum resources, not an array of readable bits. Gates transform the joint state; measuring one element can affect correlated outcomes. Q# lexical allocation (`use`) makes ownership explicit, and every allocated qubit must be returned to `|0>` before its scope exits.

```qsharp
operation PreparePair() : Result[] {
    use qs = Qubit[2];
    H(qs[0]);
    CNOT(qs[0], qs[1]);
    let results = [M(qs[0]), M(qs[1])];
    ResetAll(qs);
    return results;
}
```

The two outcomes are correlated because the circuit prepares a Bell state. `ResetAll` restores both resources; the returned array contains classical measurement results, not qubits.

## Lab

Change the circuit to prepare `|00>` and then apply `X` only to `qs[1]`. Predict both results. Next, restore the entangling circuit and tabulate at least 100 shots; check whether mixed outcomes occur and explain why finite sampling may obscure the ideal correlation.

## Checkpoint

You can trace each qubit from allocation through gates and measurement to reset, and distinguish a correlated register from two independent bits.

**Next:** [Measurement, reset, and classical feedback](06-measurements.md)
