# Lesson 01 — Research-Grade Quantum Algorithm Design

**Module 05 · Mastery · Lesson 01 of 06**

## Learning objectives

- Start from a precise computational problem and derive a quantum resource model.
- Identify the oracle, data-loading, and readout costs hidden by asymptotic claims.
- Compare the proposed quantum method with the strongest relevant classical baseline.

## Design sequence

Specify input size, output, promise (if any), error tolerance, and success criterion. Identify which part of the task is accelerated and whether its data can be loaded efficiently. Derive query complexity, then account for state preparation, oracle synthesis, repetitions, error correction, and classical postprocessing.

A quantum advantage claim needs a fair end-to-end comparison at matched accuracy and resource constraints. Simulator feasibility is not evidence of hardware feasibility, and asymptotic advantage does not establish a useful finite-size crossover.

## Lab

Choose Grover search, QFT-based period finding, or QAOA. Write a design memo with problem definition, assumptions, algorithm, full cost model, classical baseline, expected failure modes, and the smallest experiment that could falsify the central claim.

## Checkpoint

Your proposal states what is accelerated, what is not, and which unmeasured cost could invalidate the conclusion.

**Next:** [Research methods and evidence](02-research.md)
