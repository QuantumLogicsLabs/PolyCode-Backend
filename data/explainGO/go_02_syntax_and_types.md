# Go Programming Language — Syntax & Data Types

## Variables and Declarations

Go offers multiple ways to declare variables:

```go
// Explicit type declaration
var name string = "Alice"
var age int = 30

// Type inference
var city = "Karachi"

// Short variable declaration (most common inside functions)
language := "Go"
```

---

## Basic Data Types

| Type | Description | Example |
|---|---|---|
| `int` | Integer (platform-size) | `42` |
| `int8/16/32/64` | Fixed-size integers | `int64(100)` |
| `float32/64` | Floating point | `3.14` |
| `complex64/128` | Complex numbers | `1+2i` |
| `bool` | Boolean | `true`, `false` |
| `string` | UTF-8 string | `"hello"` |
| `byte` | Alias for uint8 | `'A'` |
| `rune` | Alias for int32 (Unicode) | `'€'` |

---

## Constants

```go
const Pi = 3.14159
const MaxRetries = 5
const AppName string = "MyApp"

// Iota — auto-incrementing constants
const (
    Sunday = iota  // 0
    Monday         // 1
    Tuesday        // 2
    Wednesday      // 3
)
```

---

## Composite Types

### Arrays (fixed length)
```go
var scores [5]int = [5]int{90, 85, 78, 92, 88}
fmt.Println(scores[0]) // 90
```

### Slices (dynamic arrays)
```go
fruits := []string{"apple", "banana", "mango"}
fruits = append(fruits, "orange")
fmt.Println(len(fruits)) // 4
```

### Maps (key-value pairs)
```go
person := map[string]int{
    "Alice": 30,
    "Bob":   25,
}
fmt.Println(person["Alice"]) // 30
```

### Structs
```go
type Person struct {
    Name string
    Age  int
    City string
}

p := Person{Name: "Zara", Age: 28, City: "Karachi"}
fmt.Println(p.Name) // Zara
```

---

## Pointers

Go supports pointers but not pointer arithmetic:

```go
x := 42
ptr := &x         // ptr holds the address of x
fmt.Println(*ptr) // dereference: prints 42

*ptr = 100
fmt.Println(x)    // x is now 100
```

---

## Type Conversions

Go requires **explicit** type conversions:

```go
var i int = 42
var f float64 = float64(i)
var u uint = uint(f)
```

---

## Zero Values

Uninitialized variables have zero values:

| Type | Zero Value |
|---|---|
| `int` | `0` |
| `float64` | `0.0` |
| `bool` | `false` |
| `string` | `""` |
| pointer/slice/map | `nil` |

---

## Summary

Go's type system is simple yet powerful. The combination of basic types, composite types, and explicit conversion rules makes Go programs predictable, safe, and easy to reason about.
