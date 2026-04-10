namespace DumpMachine {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Measurement;
    open Microsoft.Quantum.Diagnostics;

    @EntryPoint()
    operation Main() : Unit {
        // Allocate one qubit
        use q = Qubit();

        // The qubit starts in state |0⟩
        Message("Initial state:");
        DumpMachine();

        // Apply Hadamard gate → creates superposition
        H(q);
        Message("\nAfter Hadamard gate (superposition):");
        DumpMachine();

        // Measure the qubit
        let result = M(q);
        Message($"\nMeasurement result: {result}");

        // Reset qubit before releasing (required!)
        Reset(q);
    }
}

