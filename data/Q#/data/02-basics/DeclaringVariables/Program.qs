namespace DeclaringVariables {
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Measurement;
    open Microsoft.Quantum.Diagnostics;

    @EntryPoint()
    operation Main() : Unit {

        let isReady = true;
        let count = 42;
        let pi = 3.14159265358979;
        let name = "Alice";

        Message($"Ready: {isReady}");
        Message($"Count: {count}");
        Message($"Pi: {pi}");
        Message($"Name: {name}");
    }
}