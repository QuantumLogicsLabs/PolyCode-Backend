# Lesson 01 — Reversible Oracles and Phase Kickback

**Module 02 · Intermediate · Lesson 01 of 06**

## Learning objectives

- Specify an oracle as a reversible unitary, not an irreversible classical lookup.
- Distinguish a bit-flip oracle from a phase oracle.
- Explain how an ancilla prepared in `|->` transfers a predicate value into phase.

## Oracle contract

For a Boolean predicate `f`, the standard bit oracle is `Uf|x,y> = |x, y xor f(x)>`. XOR makes the transformation reversible. Preparing the target in `|-> = (|0>-|1>)/sqrt(2)` yields phase kickback: the target returns unchanged while the input receives phase `(-1)^f(x)`.

An oracle implementation must preserve superposition and must not measure its input. A classical function that returns `f(x)` is useful for constructing or testing a small example, but it is not itself a coherent oracle.

## Circuit sketch

```qsharp
operation ApplyBitOracle(x : Qubit, target : Qubit) : Unit {
    // Example predicate: f(x) = x
    CNOT(x, target);
}
```

For phase kickback, prepare `target` with `X` then `H` before this operation. Track global versus relative phase carefully: a phase shared by every basis state is unobservable, while a phase on only marked inputs changes interference.

## Lab

Implement a two-input toy predicate reversibly using gates, verify that applying its bit oracle twice restores both registers, then add the phase-kickback preparation. Do not measure inside the oracle. Explain the behavior on each computational-basis input.

## Checkpoint

You can state the oracle’s input/output invariant and explain why reversibility is mandatory.

**Next:** [Entanglement and multi-qubit structure](02-entanglement-qsharp.md)
