# Q# Certificate — Final Assessment

## Format

- **40 questions** across conceptual reasoning, code/circuit analysis, and short design responses
- **80%** required to pass
- Open-book; answers must justify assumptions and distinguish guarantees from observations

## Assessment blueprint

| Module | Focus |
|--------|-------|
| 01 Beginner | Q# operations/functions, state amplitudes, gates, measurement, qubit lifetime |
| 02 Intermediate | Reversible oracles, phase kickback, entanglement, amplitude amplification, tests, resources, host boundaries |
| 03 Advanced | Promise problems, Grover iteration behavior, QFT phase/readout, syndromes, QAOA, reversible patterns |
| 04 Professional | Reproducible workflows, hybrid budgets, resource-aware performance, CI, reporting, deployment |
| 05 Mastery | Algorithm cost models, research evidence, noise assumptions, fault-tolerant estimates, capstone defense |

## Representative prompts

1. A measured algorithm has a high success rate on one run. What additional evidence is needed to establish a reproducible probability estimate?
2. Derive why a phase oracle is reversible and explain how phase kickback changes interference.
3. Given `N` search candidates and `M` marked items, estimate Grover’s first success peak and identify costs not included in query complexity.
4. Explain how syndrome extraction can identify an error without measuring an encoded logical state, and state a limitation of the three-qubit bit-flip code.
5. Review a proposed quantum-advantage claim. Identify data-loading, oracle, sampling, classical-baseline, and hardware assumptions that must be checked.

## Practical component

Submit the mastery capstone with source, deterministic tests, probabilistic evaluation, run manifest, classical baseline, resource analysis, and limitations. The reviewer must be able to reproduce the reported small-instance result.

## Retakes

After **48 hours**, review the weak module and submit corrections or additional evidence before retaking.

When ready, use the PolyCode assessment portal.
