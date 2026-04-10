# 04 — Arrays and Tuples

## Arrays

Arrays in Q# are **immutable by default** and **fixed-size**.

### Declaration and Initialization

```qsharp
// Literal
let nums   : Int[]    = [1, 2, 3, 4, 5];
let flags  : Bool[]   = [true, false, true];
let empty  : String[] = [];

// Repeat syntax: [value, size = n]
let zeros = [0, size = 10];       // [0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
let qubits = [PauliZ, size = 3];  // [PauliZ, PauliZ, PauliZ]

// Type inference works
let arr = [1, 2, 3];  // Inferred as Int[]
```

### Accessing Elements

```qsharp
let arr = [10, 20, 30, 40, 50];

let first = arr[0];           // 10
let last  = arr[4];           // 50
let slice = arr[1..3];        // [20, 30, 40]
let step  = arr[0..2..4];     // [10, 30, 50] (every 2nd)
let rev   = arr[4..-1..0];    // [50, 40, 30, 20, 10] (reverse)
```

### Array Properties

```qsharp
let n = Length(arr);   // Length of array
let h = Head(arr);     // First element
let t = Tail(arr);     // Last element
let rest = Rest(arr);  // All but first
let most = Most(arr);  // All but last
```

### Modifying Arrays (Copy-and-Update)

Arrays are immutable, but you can create modified copies:

```qsharp
let arr = [1, 2, 3, 4, 5];

// Update single element: w/= index <- value
let arr2 = arr w/ 2 <- 99;  // [1, 2, 99, 4, 5]

// Mutable array with set
mutable arr3 = [1, 2, 3];
set arr3 w/= 1 <- 42;       // arr3 is now [1, 42, 3]
```

### Array Operations

```qsharp
open Microsoft.Quantum.Arrays;

// Combine arrays
let combined = arr1 + arr2;        // Concatenation
let appended = arr + [newItem];   // Append single

// Searching
let idx = IndexOf(x -> x == 5, arr);  // Find index of 5

// Transformation
let doubled = Mapped(x -> x * 2, arr);  // [2, 4, 6, ...]
let evens   = Filtered(x -> x % 2 == 0, arr);  // Even elements

// Folding
let sum  = Fold((acc, x) -> acc + x, 0, arr);
let prod = Fold((acc, x) -> acc * x, 1, arr);

// Sorting
let sorted = Sorted((a, b) -> a <= b, arr);

// Zip two arrays
let zipped = Zip(arr1, arr2);  // Array of tuples
```

---

## Tuples

Tuples group fixed types together:

```qsharp
// Creating tuples
let point  = (3, 4);                  // (Int, Int)
let person = ("Alice", 30, true);     // (String, Int, Bool)

// Accessing via destructuring
let (x, y) = point;
let (name, age, active) = person;

// Nested tuples
let nested = ((1, 2), (3, 4));
let ((a, b), (c, d)) = nested;
```

### Tuple in Operations

```qsharp
// Return multiple values
operation MinMax(arr : Int[]) : (Int, Int) {
    mutable min = arr[0];
    mutable max = arr[0];
    for x in arr {
        if x < min { set min = x; }
        if x > max { set max = x; }
    }
    return (min, max);
}

// Call with destructuring
let (minimum, maximum) = MinMax([3, 1, 4, 1, 5, 9, 2, 6]);
```

### Unit as Empty Tuple

`Unit` is actually the empty tuple `()`:

```qsharp
operation DoNothing() : Unit { }
// Equivalent to:
operation DoNothing() : () { }
```

---

## 2D Arrays

```qsharp
// Array of arrays (jagged)
let matrix : Int[][] = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];

// Access element
let elem = matrix[1][2];  // 6

// Iterate
for row in matrix {
    for elem in row {
        Message($"{elem} ");
    }
}
```

---

## Exercises

### Exercise 1
Write a function `Reverse(arr : Int[]) : Int[]` that reverses an array without using `arr[n..-1..0]` (use a loop).

### Exercise 2
Write a function `Zip3(a : Int[], b : Int[], c : Int[]) : (Int, Int, Int)[]` that zips three arrays.

### Exercise 3
Write an operation that takes an array of qubits and returns a tuple of `(Int, Result[])` where the Int is the count of qubits measured as `One`.

---

*Next: [05 — User-Defined Types](05-user-defined-types.md)*
