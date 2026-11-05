# Lesson 03 — Performance and Resource-Aware Optimization

**Module 04 · Professional · Lesson 03 of 06**

## Learning objectives

- Optimize the circuit’s dominant cost rather than source-code appearance.
- Compare gate count, depth, width, and measurement overhead.
- Preserve semantics while applying compiler-aware transformations.

## Measure before changing

Profile classical preprocessing separately from circuit execution and provider queue time. For a circuit, inspect depth as well as gate count: parallel gates can reduce depth, while connectivity constraints or decomposition can erase an apparent source-level improvement. Include ancilla allocation, reset, and measurement costs in the comparison.

Treat compiler/resource-estimator output as tied to its exact target profile and assumptions. Validate every optimization against basis-state behavior or the appropriate probability distribution, and keep a baseline for regression comparison.

## Lab

Choose a circuit from the advanced module and compare two equivalent implementations. Record source-level operations, compiled resources, execution shots, and wall-clock components separately. Explain one tradeoff, such as lower depth at higher qubit count.

## Checkpoint

You can justify an optimization with measured or estimated evidence and demonstrate that it preserves the intended behavior.

**Next:** [Team engineering and review](04-team-projects.md)
