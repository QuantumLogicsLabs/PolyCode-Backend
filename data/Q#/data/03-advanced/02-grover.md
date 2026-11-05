# Lesson 02 — Grover Search Under Realistic Constraints

**Module 03 · Advanced · Lesson 02 of 06**

## Learning objectives

- Construct the Grover iterate from an oracle and a reflection.
- Select iteration counts for known solution counts and recognize overshoot.
- Account for oracle synthesis, sampling, and verification cost.

## Cost and stopping

For `N` equally likely candidates and `M` marked items, the good-state angle satisfies `sin^2(theta) = M/N`. After `r` iterations, ideal success probability is `sin^2((2r+1)theta)`. This explains both the approximate quadratic improvement and the periodic drop after the optimum. When `M` is unknown, fixed iteration counts do not retain the same guarantee.

The total cost includes synthesizing and calling the oracle, state preparation, circuit depth, and enough repetitions to verify success. A black-box query advantage can disappear when the oracle is expensive.

## Lab

Implement a small reversible predicate, test it on every basis input, and sweep `r` across the first success peak and beyond. Compare empirical success with the formula. Include `M=0`, `M=1`, and multiple marked states; define behavior for no solution rather than assuming a match exists.

## Checkpoint

You can calculate an iteration estimate from `N` and `M`, and explain when the estimate is invalid or operationally misleading.

**Next:** [Quantum Fourier transform and phase](03-quantum-fft.md)
