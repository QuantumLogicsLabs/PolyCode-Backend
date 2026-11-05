# Lesson 01 — Q# as a Quantum Programming Language

**Module 01 · Beginner · Lesson 01 of 06**

## Learning objectives

- Distinguish the host program, Q# operations, and a quantum target.
- Explain why quantum state is manipulated through operations rather than read directly.
- Create a minimal Q# entry point and identify what a simulator does.

## Core model

Q# is a strongly typed language for describing quantum and classical computation. An `operation` may act on qubits and can have quantum side effects; a `function` is classical and cannot access qubits. A simulator executes the circuit semantics on a classical machine—it does not make the simulated algorithm quantum-fast.

Qubits are resources allocated for a scope. Programs apply gates and measurements; they cannot inspect amplitudes or copy an unknown quantum state. Measurement is the interface from quantum state back to classical data.

## Minimal program

```qsharp
@EntryPoint()
operation Main() : Unit {
    Message("Q# program is running");
}
```

In the current QDK workflow, open a Q# project with the supported .NET/QDK tooling and run its entry point. Keep SDK and template versions pinned for reproducible work; do not assume every historical Azure Quantum command applies to a local project.

## Lab

Run the entry point, then change the message and run it again. Identify which part is ordinary classical execution. Create a second operation and call it from `Main`; state whether it needs to be a function or an operation and justify the choice.

## Checkpoint

You can describe the roles of Q#, its host/runtime, and a simulator without claiming that local simulation demonstrates quantum speedup.

**Next:** [Quantum states and probability](02-quantum-concepts.md)
