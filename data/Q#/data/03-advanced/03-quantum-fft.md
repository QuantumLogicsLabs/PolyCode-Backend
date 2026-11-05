# Lesson 03 — Quantum Fourier Transform and Phase Structure

**Module 03 · Advanced · Lesson 03 of 06**

## Learning objectives

- Relate the QFT to the discrete Fourier transform on amplitudes.
- Explain how controlled phases expose periodic structure.
- Track bit-order conventions and approximation error.

## Transform

For dimension `N = 2^n`, the QFT maps `|x>` to `1/sqrt(N) * sum_y exp(2*pi*i*x*y/N)|y>`. It is a unitary basis change, not a way to read out all Fourier coefficients at once. Its value appears when later interference and measurements extract a property such as a period or phase.

Circuit decompositions use Hadamards and controlled phase rotations, often followed by bit reversal. Reversing register order is a common source of apparently incorrect results. Approximate QFT implementations omit small rotations; quantify the induced error against the algorithm’s success requirement.

## Lab

For two or three qubits, implement a QFT and its inverse using the Q# gate set supported by your pinned SDK. Verify that inverse-after-forward returns every computational basis input to itself. Document register endianness and compare exact versus truncated rotations if your simulator supports them.

## Checkpoint

You can explain why a QFT alone does not provide an exponential readout advantage and identify where phase information is converted into measurable evidence.

**Next:** [Syndromes and quantum error correction](04-error-correction.md)
