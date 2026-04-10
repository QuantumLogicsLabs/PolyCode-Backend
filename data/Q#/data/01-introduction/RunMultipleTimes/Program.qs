namespace FlipAQubit {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Canon;
    open Microsoft.Quantum.Measurement;

    operation FlipAndMeasure() : Result {
        use q = Qubit();
        H(q);
        let r = M(q);
        Reset(q);
        return r;
    }
}