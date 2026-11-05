# Lesson 02 — Hybrid Applications and Variational Workloads

**Module 04 · Professional · Lesson 02 of 06**

## Learning objectives

- Design a bounded classical–quantum optimization loop.
- Account for shot noise, queue latency, and optimizer query budget.
- Define cancellation, retry, and convergence behavior explicitly.

## Loop architecture

A hybrid workload has a classical parameter update, a quantum circuit evaluation, and classical aggregation. Make the circuit interface deterministic for a given parameter/input configuration even though its measured samples are stochastic. Batch compatible evaluations when supported; cache only results whose full configuration and sampling policy match.

Choose stopping criteria before running: iteration limit, objective tolerance, uncertainty threshold, and wall-clock budget. Detect invalid probabilities, backend failures, and optimizer non-convergence instead of returning a success-shaped default.

## Lab

Extend the one-layer QAOA experiment with a classical optimizer. Record objective estimates and confidence intervals per iteration, bound the total circuit evaluations, and compare at least two stopping rules. Test a simulated backend failure and verify the host reports it distinctly from a poor objective value.

## Checkpoint

You can estimate the workload’s total shot and evaluation budget before submission and identify failure modes at both the host and quantum-runner boundary.

**Next:** [Performance and resource-aware optimization](03-performance.md)
