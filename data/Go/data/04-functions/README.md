# Functions in Go

This directory contains comprehensive examples of Go functions, from basic to advanced concepts.

## Files

- **main.go** - Basic function examples
- **multiple-returns.go** - Functions returning multiple values
- **closures.go** - Closures and anonymous functions
- **recursion.go** - Recursive functions and algorithms
- **higher-order.go** - Higher-order functions and functional programming
- **README.md** - This file

## Concepts Covered

### Basic Functions
- Function declaration and calling
- Parameters and return values
- Named return values
- Naked returns

### Advanced Functions
- **Multiple Return Values**: Functions returning multiple results
- **Closures**: Anonymous functions capturing variables
- **Recursion**: Functions calling themselves
- **Higher-Order Functions**: Functions as parameters and return values

### Key Features Demonstrated

#### Multiple Returns
```go
func calculate(a, b int) (int, int) {
    return a + b, a - b
}
```

#### Closures
```go
adder := func(base int) func(int) int {
    return func(x int) int {
        return base + x
    }
}
```

#### Recursion
```go
func factorial(n int) int {
    if n <= 1 {
        return 1
    }
    return n * factorial(n-1)
}
```

#### Higher-Order Functions
```go
func mapNumbers(nums []int, fn func(int) int) []int {
    result := make([]int, len(nums))
    for i, num := range nums {
        result[i] = fn(num)
    }
    return result
}
```

## Running the Examples

```bash
go run main.go
go run multiple-returns.go
go run closures.go
go run recursion.go
go run higher-order.go
```

## Best Practices

1. **Keep functions small and focused**
2. **Use descriptive names**
3. **Handle errors properly**
4. **Use closures judiciously**
5. **Be careful with recursion depth**
6. **Leverage higher-order functions for reusable code**

## Advanced Topics

- Function composition
- Currying
- Memoization
- Generic functions (Go 1.18+)
- Function decorators
- Pipeline patterns

## Exercises

1. Create a function that returns both a result and an error
2. Write a closure that maintains state
3. Implement a recursive algorithm for tree traversal
4. Create a higher-order function for filtering data
5. Build a function pipeline for data processing
