# 01 — Types and Variables

## The Q# Type System

Q# is **statically typed** — every variable has a known type at compile time. Types are divided into classical and quantum.

---

## Primitive Types

### Boolean
```qsharp
let isReady : Bool = true;
let isDone  : Bool = false;
```

### Integers
```qsharp
let count  : Int  = 42;          // 64-bit signed integer
let bigNum : BigInt = 1000000L;  // Arbitrary precision (note the L suffix)
```

### Floating Point
```qsharp
let pi    : Double = 3.14159265358979;
let angle : Double = 0.5 * Microsoft.Quantum.Math.PI();
```

### Complex Numbers (via library)
```qsharp
open Microsoft.Quantum.Math;
let c = Complex(1.0, 2.0);  // 1 + 2i
```

### Strings
```qsharp
let name   : String = "Alice";
let greet  : String = $"Hello, {name}!";  // String interpolation
```

### Range
```qsharp
let r1 = 0..4;        // 0, 1, 2, 3, 4
let r2 = 0..2..10;    // 0, 2, 4, 6, 8, 10 (step of 2)
let r3 = 5..-1..0;    // 5, 4, 3, 2, 1, 0 (count down)
```

### Pauli
```qsharp
let axis : Pauli = PauliX;  // PauliX, PauliY, PauliZ, PauliI
```

### Result
```qsharp
let outcome : Result = Zero;  // Zero or One (from measurement)
```

---

## Quantum Types

### Qubit
```qsharp
use q = Qubit();          // Single qubit
use qs = Qubit[5];        // Array of 5 qubits
```

> ⚠️ You **cannot** directly create a `Qubit` value or store it in a variable separately from allocation. Qubits are always managed via `use`.

---

## Variable Declaration

### Immutable (`let`)
```qsharp
let x = 5;          // Type inferred as Int
let y : Double = 3.14;
// x = 6;           // ❌ ERROR: cannot reassign let
```

### Mutable (`mutable` + `set`)
```qsharp
mutable count = 0;
set count = count + 1;   // Reassign
set count += 1;          // Shorthand (Q# 0.15+)
```

---

## Type Inference

Q# infers types when they can be determined:

```qsharp
let n = 42;          // Int
let f = 3.14;        // Double
let b = true;        // Bool
let s = "hello";     // String
let r = Zero;        // Result
```

When ambiguous, annotate explicitly:
```qsharp
let arr : Int[] = [];  // Empty array — must specify type
```

---

## Type Conversions

Q# does **not** do implicit type conversion:

```qsharp
let n : Int    = 5;
let d : Double = IntAsDouble(n);   // Explicit conversion required
let i : Int    = Round(d);         // Double back to Int

// Useful conversions:
// IntAsDouble(n)      Int → Double
// RoundD(d) / Floor() Double → Int  
// ResultAsBool(r)     Result → Bool
// BoolAsResult(b)     Bool → Result
```

---

## The Unit Type

`Unit` is Q#'s "nothing" type (like `void`):

```qsharp
operation DoSomething() : Unit {
    // No return value
    Message("Done!");
}
```

---

## Exercises

### Exercise 1
Declare variables of each primitive type and print them using `Message($"...")`.

### Exercise 2
Create a range from 1 to 20 stepping by 3, then iterate over it with a `for` loop and print each value.

### Exercise 3
Write an operation that takes a `Result` and returns `true` if it's `One`, `false` if it's `Zero`. Use `ResultAsBool`.

---

*Next: [02 — Operations and Functions](02-operations-and-functions.md)*
