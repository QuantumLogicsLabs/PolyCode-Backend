# Lesson 2: Variables And Types

## Declaring Variables

Go supports a few common ways to declare variables.

```go
var name string = "Saad"
var age int = 20
city := "Karachi"
```

## Common Types

- `string`
- `int`
- `float64`
- `bool`

## Constants

Use `const` for values that should not change.

```go
const language = "Go"
```

## Zero Values

When a variable is declared but not assigned, Go gives it a default value:

- `0` for numbers
- `""` for strings
- `false` for booleans

## Practice

- Run `go run ./examples/02-variables`
- Add more variables about yourself
- Print both values and types
