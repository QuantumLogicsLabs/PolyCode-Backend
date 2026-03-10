namespace HelloQuantum {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Measurement;
    open Microsoft.Quantum.Diagnostics;

    @EntryPoint()
    operation FlipQubit() : Result {
        use q = Qubit();
        X(q);
        let result = M(q);
        DumpMachine();
        Reset(q);
        return result;
    }
}