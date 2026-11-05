# Lesson 01 — Reproducible Quantum Workflows

**Module 04 · Professional · Lesson 01 of 06**

## Learning objectives

- Make a Q# experiment reproducible from source, configuration, and dependencies.
- Separate circuit logic from backend and execution configuration.
- Preserve enough run metadata to interpret results later.

## Workflow contract

Pin the QDK/.NET toolchain and record compiler options, target profile, simulator or provider, shot count, random seed when supported, input instance, and source revision. Keep credentials outside source control and make backend selection explicit. A simulator result, resource estimate, and hardware job are different artifacts and must not be reported interchangeably.

Use a small deterministic circuit as a smoke test before expensive runs. Validate inputs before submission, capture job identifiers and failures, and make retries idempotent where the provider allows it.

## Lab

Create a run manifest for the QAOA experiment from Module 03. Include instance hash, parameters, shots, backend, toolchain version, code revision, start/end timestamps, and result location. Demonstrate that changing one manifest field makes the run distinguishable.

## Checkpoint

Another engineer can reproduce or audit the result without relying on undocumented local state or embedded credentials.

**Next:** [Hybrid applications and variational loops](02-hybrid-apps.md)
