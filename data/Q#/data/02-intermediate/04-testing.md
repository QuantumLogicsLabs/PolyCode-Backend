# Lesson 04 — Testing Quantum Programs

**Module 02 · Intermediate · Lesson 04 of 06**

## Learning objectives

- Test deterministic circuit invariants separately from probabilistic outcomes.
- Validate a circuit on basis states before measuring superpositions.
- Use repeated trials and explicit tolerances for statistical checks.

## Test strategy

Start with properties that must hold for every run: an oracle is an involution when designed that way; a prepared basis state yields its expected result; and temporary qubits are reset on every path. These exact invariants make stronger tests than checking one random sample.

For a probabilistic circuit, test a distribution over many independent shots. Predeclare shot count, confidence/tolerance, and failure criteria. A test that demands an exact sample frequency is flaky; a test that accepts any output can hide a broken circuit.

## Lab

Build a test matrix for the Bell sampler and amplitude-amplification example: include deterministic basis inputs, expected invariants, randomized shots, and negative cases such as a deliberately inverted phase oracle. Log the seed when the runner supports deterministic seeding. Keep simulator-specific test harness APIs in the project’s pinned QDK version.

## Checkpoint

Your tests distinguish compiler/circuit correctness from sampling noise and can reliably catch an intentionally introduced defect.

**Next:** [Resource estimation and algorithm feasibility](05-resource-estimation.md)
