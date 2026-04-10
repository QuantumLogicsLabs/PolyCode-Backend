namespace IsPrime {

    open Microsoft.Quantum.Canon;
    open Microsoft.Quantum.Intrinsic;

    // Recursive helper to check divisibility
    function IsPrimeRecursive(n : Int, i : Int) : Bool {
        if (i * i > n) {
            return true;
        }
        if (n % i == 0) {
            return false;
        }
        return IsPrimeRecursive(n, i + 2);
    }

    @EntryPoint()
    operation IsPrime(n : Int) : Bool {
        if (n < 2) {
            return false;
        }
        if (n == 2) {
            return true;
        }
        if (n % 2 == 0) {
            return false;
        }

        // Start recursion from 3
        return IsPrimeRecursive(n, 3);
    }
}