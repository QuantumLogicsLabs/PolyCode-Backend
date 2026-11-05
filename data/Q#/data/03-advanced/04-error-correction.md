# Lesson 04 — Error Correction, Syndromes, and Logical State

**Module 03 · Advanced · Lesson 04 of 06**

## Learning objectives

- Explain why unknown quantum data cannot be protected by copying it.
- Encode a logical state and extract an error syndrome without measuring that state.
- Distinguish a pedagogical code from fault-tolerant computation.

## Three-qubit bit-flip code

The repetition code maps `alpha|0> + beta|1>` to `alpha|000> + beta|111>`. Measuring parity checks such as `Z0 Z1` and `Z1 Z2` reveals which qubit flipped while preserving the encoded amplitudes. The syndrome selects a recovery; it does not reveal `alpha` or `beta`.

This code corrects one bit-flip but not arbitrary single-qubit errors: phase errors require complementary protection. Real fault tolerance also needs repeated syndrome extraction, fault-tolerant gates, decoding, and a noise model below threshold.

## Lab

Construct encode–inject-error–syndrome–recover circuits for each single-qubit bit flip. Confirm logical basis recovery and test a phase-flip as a counterexample. Compare syndrome distributions with and without injected noise. Keep physical data qubits unmeasured until after recovery.

## Checkpoint

You can explain how syndrome measurements preserve encoded information and name the limits of the three-qubit repetition code.

**Next:** [Quantum optimization and QAOA](05-optimization.md)
