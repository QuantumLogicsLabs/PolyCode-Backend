# Lesson 7: Error Handling

## Understanding Errors in Go

Go handles errors differently than many other languages. Instead of using exceptions, Go uses explicit error values returned from functions.

## The Error Type

In Go, `error` is a built-in interface type:
```go
type error interface {
    Error() string
}
```

## Basic Error Handling Pattern

Most functions that can fail return both a result and an error:
```go
result, err := someFunction()
if err != nil {
    // handle the error
    return err
}
// use result
```

## Creating Errors

Use the `errors` package to create new errors:
```go
import "errors"

err := errors.New("something went wrong")
```

Or use `fmt.Errorf` for formatted errors:
```go
err := fmt.Errorf("invalid input: %s", input)
```

## Error Wrapping (Go 1.13+)

Use `%w` verb to wrap errors while preserving the original:
```go
err := fmt.Errorf("failed to process file: %w", originalErr)
```

Use `errors.Unwrap` to get the original error:
```go
originalErr := errors.Unwrap(wrappedErr)
```

Use `errors.Is` to check for specific errors:
```go
if errors.Is(err, os.ErrNotExist) {
    // handle file not found
}
```

## Best Practices

- Always check for errors immediately after a function call
- Don't ignore errors (use `_` only when you're sure it's safe)
- Return errors from your functions instead of panicking
- Add context to errors when returning them up the call stack

## Practice

- Run `go run ./examples/07-error-handling`
- Try different error scenarios
- Practice wrapping errors with additional context
