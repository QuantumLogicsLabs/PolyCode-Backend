# Lesson 04 — Team Engineering for Quantum Projects

**Module 04 · Professional · Lesson 04 of 06**

## Learning objectives

- Split a quantum project into contracts that can be reviewed independently.
- Review oracle, register, measurement, and reset invariants.
- Use CI to catch correctness and resource regressions early.

## Collaboration practices

Agree on operation signatures, register ordering, bit/phase conventions, and ownership/reset requirements before parallel implementation. Review circuit changes at the state-transition level, not only by reading source. Keep classical problem encoding and quantum transformations separately testable.

CI should build with a pinned toolchain, run deterministic small-instance tests, verify probabilistic properties with declared tolerances, and optionally track resource baselines. Do not make a noisy device job a required unit test unless its variability and cost are controlled.

## Lab

Create a code-review checklist for a two-person algorithm project. Include oracle reversibility, endianness, qubit cleanup, invalid input handling, statistical test thresholds, and resource changes. Review a deliberate bug in each category and identify the test that catches it.

## Checkpoint

Your team can merge a change only when its mathematical contract, tests, and resource impact are reviewable.

**Next:** [Technical documentation and experiment records](05-documentation.md)
