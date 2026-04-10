# 03 — Control Flow

## Classical Control Flow

### if / elif / else
```qsharp
let x = 7;

if x > 10 {
    Message("Large");
} elif x > 5 {
    Message("Medium");
} else {
    Message("Small");
}

// Single-line (no braces needed for one statement)
if x > 0 { Message("Positive"); }
```

### for Loop
```qsharp
// Iterate over a range
for i in 0..4 {
    Message($"i = {i}");
}

// Iterate over an array
let arr = [10, 20, 30];
for x in arr {
    Message($"value = {x}");
}

// Iterate over a range with step
for i in 0..2..10 {
    Message($"even: {i}");
}
```

### while Loop
```qsharp
mutable n = 1;
while n < 100 {
    set n *= 2;
}
Message($"First power of 2 >= 100: {n}");
```

---

## Quantum-Specific Control Flow

### repeat-until-success (RUS)

The `repeat-until` loop is uniquely quantum. It retries an operation until a condition is met — essential for probabilistic quantum protocols:

```qsharp
operation PrepareRandomBit() : Result {
    use q = Qubit();
    
    mutable result = Zero;
    repeat {
        H(q);
        set result = M(q);
    } until (result == One)
    fixup {
        Reset(q);
    }
    
    Reset(q);
    return result;
}
```

Structure:
```
repeat {
    <quantum operations>
} until (<success condition>)
fixup {
    <cleanup if failed — reset qubits for next attempt>
}
```

> ⚠️ The `fixup` block runs only when the condition is **false**. It should reset qubits to a known state for the next iteration.

### Practical RUS Example: Preparing a Specific State
```qsharp
operation PrepareStateWithPostSelection(q : Qubit) : Unit {
    repeat {
        H(q);
        T(q);
        H(q);
    } until (M(q) == Zero)
    fixup {
        Reset(q);
    }
}
```

---

## Conditional on Measurement Results

A common pattern: measure and branch classically:

```qsharp
operation TeleportationCorrection(q : Qubit, m1 : Result, m2 : Result) : Unit {
    if m1 == One { Z(q); }
    if m2 == One { X(q); }
}
```

---

## Early Return

```qsharp
function FindFirst(arr : Int[], target : Int) : Int {
    for i in 0..Length(arr) - 1 {
        if arr[i] == target {
            return i;  // Early return
        }
    }
    return -1;  // Not found
}
```

---

## Fail (Throwing Errors)

```qsharp
operation SafeDivide(a : Int, b : Int) : Int {
    if b == 0 {
        fail "Division by zero is not allowed";
    }
    return a / b;
}
```

---

## Pattern Matching with if-let (Q# 1.0+)

```qsharp
// Q# doesn't have match/switch like Rust/C#
// Use chained if-elif instead:

function DescribeResult(r : Result) : String {
    if r == Zero { return "measured zero"; }
    else         { return "measured one"; }
}
```

---

## Control Flow Summary

| Statement | Use Case |
|-----------|----------|
| `if/elif/else` | Classical branching |
| `for` | Iterate over ranges or arrays |
| `while` | Repeat while condition holds |
| `repeat-until` | Probabilistic quantum protocols |
| `return` | Early exit from operation/function |
| `fail` | Throw an error |
| `within/apply` | Apply + auto-undo pattern |

---

## Exercises

### Exercise 1
Write a function `Fibonacci(n : Int) : Int` using a `for` loop (not recursion).

### Exercise 2
Write a `repeat-until` operation that keeps flipping a qubit and measuring until it gets `Zero` three times in a row.

### Exercise 3
Write an operation that takes an array of qubits, measures each one, and returns an array of `Result`s. Use `for` and a mutable array.

---

*Next: [04 — Arrays and Tuples](04-arrays-and-tuples.md)*
