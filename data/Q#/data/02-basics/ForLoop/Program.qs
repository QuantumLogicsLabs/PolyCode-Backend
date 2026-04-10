namespace ForLoop {

    open Microsoft.Quantum.Canon;
    open Microsoft.Quantum.Intrinsic;
    @EntryPoint()
    operation Main() : Unit {

        // Create a range from 1 to 20 stepping by 3
        let r = 1..3..20;

        // Iterate over the range
        for i in r {
            Message($"{i}");
        }
    }
}

