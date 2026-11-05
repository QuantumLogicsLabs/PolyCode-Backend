# Lesson 05 — Documentation for Quantum Results

**Module 04 · Professional · Lesson 05 of 06**

## Learning objectives

- Document mathematical assumptions and implementation conventions together.
- Report uncertainty and baselines alongside measured outcomes.
- Make claims traceable to code, run configuration, and evidence.

## Minimum technical record

For each algorithm, document the problem encoding, oracle contract, register ordering, gate conventions, measurement basis, and any approximation. For each experiment, record instance, source revision, toolchain, backend, shots, seed if applicable, resource estimate assumptions, and failures/retries.

Separate proven properties, simulator observations, hardware observations, and hypotheses. State the classical baseline and include uncertainty; a best sample alone is not a performance result.

## Lab

Write a one-page report for the QAOA experiment. Include an exact tiny-instance baseline, sampling uncertainty, resource estimates, limitations, and a reproducibility manifest. Ask a reader to reproduce one table entry using only the report.

## Checkpoint

A reader can distinguish what the algorithm guarantees from what one experimental run happened to observe.

**Next:** [Deployment and execution readiness](06-deployment.md)
