# Lesson 02 — Quantum Research Methods and Evidence

**Module 05 · Mastery · Lesson 02 of 06**

## Learning objectives

- Form a falsifiable hypothesis about an algorithm or implementation.
- Design controls that separate algorithmic effects from sampling and backend effects.
- Report reproducible evidence with uncertainty and known limitations.

## Experimental discipline

Predefine metrics, baselines, sample sizes, and exclusion rules. Use exact classical enumeration for tiny instances as a correctness oracle, not as a scalable comparator. Compare distributions where the algorithm is probabilistic; report confidence intervals and account for parameter-search or multiple-comparison bias.

Preserve failed runs and configuration changes. Record the full toolchain and backend assumptions, and separate a hypothesis from a result. Independent reruns should reproduce the conclusion within stated uncertainty.

## Lab

Design an experiment to evaluate whether a QAOA implementation improves solution quality on a chosen family. Specify instance generation, classical baseline, shot budget, optimizer budget, randomization, statistical analysis, and stopping rules before collecting data.

## Checkpoint

Another researcher can reproduce the protocol and determine whether the evidence supports, weakens, or fails to test the hypothesis.

**Next:** [Noise-aware algorithm design](03-noise-aware-design.md)
