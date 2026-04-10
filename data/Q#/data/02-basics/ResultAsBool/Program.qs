namespace ResultAsBool {

    open Microsoft.Quantum.Canon;
    open Microsoft.Quantum.Intrinsic;
    open Microsoft.Quantum.Convert;
    open Microsoft.Quantum.Diagnostics;

    @EntryPoint()
    operation Main() : Unit {

        let r1 = Zero;
        let r2 = One;

        let b1 = ResultToBool(r1);
        let b2 = ResultToBool(r2);

        Message($"Zero -> {BoolAsString(b1)}");
        Message($"One -> {BoolAsString(b2)}");
    }

    operation ResultToBool(r : Result) : Bool {
        return ResultAsBool(r);
    }
}