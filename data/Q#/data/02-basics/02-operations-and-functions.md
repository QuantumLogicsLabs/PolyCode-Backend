# 02 — Operations and Functions

## Operations vs Functions

This is one of the most important distinctions in Q#:

| | Operation | Function |
|--|-----------|----------|
| **Keyword** | `operation` | `function` |
| **Can use qubits?** | ✅ Yes | ❌ No |
| **Side effects?** | ✅ Yes | ❌ No (pure) |
| **Supports Adjoint?** | ✅ Yes | ❌ No |
| **Supports Controlled?** | ✅ Yes | ❌ No |

**Rule of thumb:** If it touches quantum state or has side effects → `operation`. If it's a pure computation → `function`.

---

## Defining Operations

```qsharp
operation OperationName(param1 : Type1, param2 : Type2) : ReturnType {
    // body
    return value;
}
```

### Example: Prepare a Bell State
```qsharp
operation PrepareBellState(q1 : Qubit, q2 : Qubit) : Unit {
    H(q1);
    CNOT(q1, q2);
}
```

### Example: Returns a value
```qsharp
operation MeasureInBasis(q : Qubit, basis : Pauli) : Result {
    return Measure([basis], [q]);
}
```

---

## Defining Functions

```qsharp
function FunctionName(param : Type) : ReturnType {
    return expression;
}
```

### Example: Pure math
```qsharp
function Factorial(n : Int) : Int {
    if n <= 1 { return 1; }
    return n * Factorial(n - 1);
}
```

### Example: Classical data processing
```qsharp
function MaxOfArray(arr : Int[]) : Int {
    mutable max = arr[0];
    for x in arr {
        if x > max { set max = x; }
    }
    return max;
}
```

---

## Parameters and Return Types

### Multiple Parameters
```qsharp
operation RotateAndMeasure(q : Qubit, angle : Double, axis : Pauli) : Result {
    R(axis, angle, q);
    return Measure([axis], [q]);
}
```

### Tuple Return
```qsharp
operation MeasureTwo(q1 : Qubit, q2 : Qubit) : (Result, Result) {
    return (M(q1), M(q2));
}

// Calling:
let (r1, r2) = MeasureTwo(qubit1, qubit2);
```

### Unit (no return)
```qsharp
operation ApplyX(q : Qubit) : Unit {
    X(q);
    // No return statement needed
}
```

---

## Calling Operations and Functions

```qsharp
@EntryPoint()
operation Main() : Unit {
    // Call a function
    let f = Factorial(5);
    Message($"5! = {f}");

    // Call an operation
    use (q1, q2) = (Qubit(), Qubit());
    PrepareBellState(q1, q2);

    // Call with tuple destructuring
    let (r1, r2) = MeasureTwo(q1, q2);
    Message($"Results: {r1}, {r2}");

    ResetAll([q1, q2]);
}
```

---

## The `within/apply` Pattern

A common Q# pattern for applying an operation, doing something, then undoing it:

```qsharp
operation PhaseKickback(control : Qubit, target : Qubit) : Unit {
    within {
        H(target);       // Transform basis
    } apply {
        CNOT(control, target);   // Do work in transformed basis
    }
    // H(target) is automatically applied again (undone) here
}
```

This is cleaner than manually calling the adjoint.

---

## Partial Application

Q# supports partial application — fixing some arguments to create a new callable:

```qsharp
operation ApplyToEach<'T>(op : ('T => Unit), arr : 'T[]) : Unit {
    for x in arr { op(x); }
}

// Partial application:
let applyH = H;            // H is already an (Qubit => Unit)
ApplyToEach(H, qubits);   // Apply H to every qubit in array
```

---

## Recursive Operations

```qsharp
operation ApplyNTimes(n : Int, op : (Qubit => Unit), q : Qubit) : Unit {
    if n > 0 {
        op(q);
        ApplyNTimes(n - 1, op, q);
    }
}
```

---

## Exercises

### Exercise 1
Write a function `IsPrime(n : Int) : Bool` that returns whether a number is prime.

### Exercise 2
Write an operation `PrepareUniformSuperposition(qs : Qubit[]) : Unit` that applies H to every qubit in the array.

### Exercise 3
Write an operation that takes another operation as a parameter and applies it twice to a qubit.

---

*Next: [03 — Control Flow](03-control-flow.md)*
