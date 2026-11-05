# Lesson 01 — Deutsch–Jozsa: Promise, Oracle, and Evidence

**Module 03 · Advanced · Lesson 01 of 06**

## Learning objectives

- State the constant-or-balanced promise precisely.
- Derive how phase kickback and Hadamards produce the decision signal.
- Compare query complexity with the practical cost of implementing the oracle.

## Algorithm

For a Boolean function `f : {0,1}^n -> {0,1}`, the promise is that `f` is either constant or balanced. Prepare the input in a uniform superposition and the target in `|->`; one coherent oracle call adds phase `(-1)^f(x)`. Applying Hadamards to the input yields all-zero exactly for the constant case and never for the balanced case in the ideal model.

The one-query claim counts oracle calls, not the cost of building an arbitrary oracle. The promise problem is highly structured and does not imply a general exponential speedup on useful workloads.

## Q# implementation outline

Separate state preparation, reversible bit oracle, phase-kickback target preparation, and final measurement. Test constant and balanced truth tables for small `n`. Reset every allocated qubit and keep measurement outside the oracle.

## Lab

Implement both promise classes for `n = 2`. Exhaustively verify each truth table and simulate the circuit. Add a deliberately non-promised function and document that the algorithm’s guarantee no longer applies.

## Checkpoint

You can derive the all-zero amplitude as a sum over `(-1)^f(x)` and identify the promise as an essential precondition.

**Next:** [Grover search and iteration limits](02-grover.md)
