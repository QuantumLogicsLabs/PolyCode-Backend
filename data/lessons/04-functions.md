# Lesson 4: Functions

## Why Functions Matter

Functions let you break a program into small reusable pieces.

## Example

```go
func add(a int, b int) int {
    return a + b
}
```

Go often shortens repeated types:

```go
func add(a, b int) int {
    return a + b
}
```

## Multiple Return Values

Go can return more than one value from a function.

```go
func divide(a, b float64) (float64, bool) {
    if b == 0 {
        return 0, false
    }

    return a / b, true
}
```

## Practice

- Run `go run ./examples/04-functions`
- Write a function to multiply two numbers
- Write a function that returns your name and favorite language
