# Error Handling in Go

This directory contains comprehensive examples of error handling patterns and best practices in Go.

## Files

- **main.go** - Basic error handling examples
- **custom-errors.go** - Custom error types and implementations
- **error-patterns.go** - Common error handling patterns
- **best-practices.go** - Error handling best practices and anti-patterns
- **README.md** - This file

## Error Handling Concepts

### Basic Error Handling
- Error checking with `if err != nil`
- Error propagation with `return`
- Error creation with `errors.New()` and `fmt.Errorf()`

### Custom Error Types
- Struct-based errors with methods
- Error interfaces and implementation
- Type assertions for error handling

### Error Wrapping
- Error wrapping with `fmt.Errorf()` and `%w` verb
- Error unwrapping with `errors.Unwrap()`
- Error checking with `errors.Is()` and `errors.As()`

### Error Patterns
- Sentinel errors for known conditions
- Error aggregation for multiple errors
- Retry patterns with exponential backoff
- Circuit breaker pattern
- Context-aware error handling

## Key Features Demonstrated

### Basic Error Handling
```go
result, err := someFunction()
if err != nil {
    return fmt.Errorf("operation failed: %w", err)
}
```

### Custom Error Types
```go
type ValidationError struct {
    Field   string
    Message string
}

func (ve ValidationError) Error() string {
    return fmt.Sprintf("validation failed for '%s': %s", ve.Field, ve.Message)
}
```

### Error Wrapping
```go
err := processFile()
if err != nil {
    return fmt.Errorf("file processing failed: %w", err)
}
```

### Error Checking
```go
if errors.Is(err, ErrNotFound) {
    // Handle not found error
}

if validationErr, ok := err.(ValidationError); ok {
    // Handle validation error
}
```

## Error Handling Patterns

### Sentinel Errors
```go
var (
    ErrNotFound    = errors.New("not found")
    ErrPermission  = errors.New("permission denied")
    ErrInvalid     = errors.New("invalid input")
)
```

### Error Aggregation
```go
type ErrorCollector struct {
    errors []error
}

func (ec *ErrorCollector) Add(err error) {
    if err != nil {
        ec.errors = append(ec.errors, err)
    }
}
```

### Retry Pattern
```go
func retryOperation(operation func() error, maxRetries int) error {
    for attempt := 0; attempt < maxRetries; attempt++ {
        err := operation()
        if err == nil {
            return nil
        }
        time.Sleep(time.Duration(1<<attempt) * time.Second)
    }
    return fmt.Errorf("operation failed after %d attempts", maxRetries)
}
```

### Circuit Breaker
```go
type CircuitBreaker struct {
    maxFailures  int
    resetTimeout time.Duration
    failures     int
    state        string
}

func (cb *CircuitBreaker) Call(operation func() error) error {
    if cb.state == "open" {
        return errors.New("circuit breaker is open")
    }
    
    err := operation()
    if err != nil {
        cb.failures++
        if cb.failures >= cb.maxFailures {
            cb.state = "open"
        }
        return err
    }
    
    cb.reset()
    return nil
}
```

## Best Practices

### ✅ Do's
1. **Handle errors immediately** - Don't ignore errors
2. **Add context to errors** - Use error wrapping
3. **Create meaningful error messages** - Be specific and actionable
4. **Use appropriate error types** - Match error type to the situation
5. **Clean up resources properly** - Use defer for cleanup
6. **Handle errors in concurrent code** - Collect and handle goroutine errors
7. **Log errors appropriately** - Include context and severity
8. **Test error paths** - Verify error handling works correctly

### ❌ Don'ts
1. **Don't ignore errors** - Always check returned errors
2. **Don't use panic for normal errors** - Reserve panic for unrecoverable errors
3. **Don't lose error context** - Avoid creating generic errors
4. **Don't create resource leaks** - Always clean up on error
5. **Don't use vague error messages** - Be specific about what went wrong

## Error Handling in Different Contexts

### File Operations
```go
file, err := os.Open(filename)
if err != nil {
    return fmt.Errorf("failed to open file %s: %w", filename, err)
}
defer file.Close()
```

### Network Operations
```go
resp, err := http.Get(url)
if err != nil {
    return fmt.Errorf("HTTP request failed: %w", err)
}
defer resp.Body.Close()
```

### Database Operations
```go
result, err := db.Exec(query, args...)
if err != nil {
    return fmt.Errorf("database query failed: %w", err)
}
```

### Concurrent Operations
```go
type Result struct {
    Data interface{}
    Err  error
}

results := make(chan Result, numWorkers)

for i := 0; i < numWorkers; i++ {
    go func(id int) {
        data, err := processWork(id)
        results <- Result{Data: data, Err: err}
    }(i)
}

// Collect results and handle errors
for i := 0; i < numWorkers; i++ {
    res := <-results
    if res.Err != nil {
        log.Printf("Worker %d failed: %v", i, res.Err)
    }
}
```

## Advanced Error Handling

### Error Context
```go
type ContextualError struct {
    Context []string
    Err     error
}

func (ce ContextualError) Error() string {
    return fmt.Sprintf("%s: %v", strings.Join(ce.Context, " -> "), ce.Err)
}
```

### Error Metrics
```go
type ErrorMetrics struct {
    TotalErrors  int
    ErrorsByType map[string]int
}

func (em *ErrorMetrics) Record(err error) {
    em.TotalErrors++
    errorType := reflect.TypeOf(err).Name()
    em.ErrorsByType[errorType]++
}
```

### Graceful Degradation
```go
func processWithFallback() (result string, err error) {
    result, err = primaryMethod()
    if err == nil {
        return result, nil
    }
    
    result, err = fallbackMethod()
    if err == nil {
        return result, nil
    }
    
    return minimalResult(), nil
}
```

## Running the Examples

```bash
go run main.go
go run custom-errors.go
go run error-patterns.go
go run best-practices.go
```

## Testing Error Handling

### Unit Tests
```go
func TestValidateInput(t *testing.T) {
    tests := []struct {
        name    string
        input   string
        wantErr bool
        errType error
    }{
        {"valid input", "valid", false, nil},
        {"empty input", "", true, ErrInvalidInput},
        {"invalid format", "invalid", true, ValidationError{}},
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            err := validateInput(tt.input)
            if (err != nil) != tt.wantErr {
                t.Errorf("validateInput() error = %v, wantErr %v", err, tt.wantErr)
            }
        })
    }
}
```

### Error Assertion Tests
```go
func TestCustomError(t *testing.T) {
    err := process()
    
    var customErr CustomError
    if !errors.As(err, &customErr) {
        t.Errorf("expected CustomError, got %T", err)
    }
    
    if customErr.Code != "EXPECTED_CODE" {
        t.Errorf("expected code EXPECTED_CODE, got %s", customErr.Code)
    }
}
```

## Performance Considerations

### Error Creation
- Pre-allocate common errors as package variables
- Use error wrapping sparingly in hot paths
- Consider error pools for high-frequency operations

### Memory Usage
- Custom errors with methods have overhead
- Error wrapping creates additional allocations
- Use sentinel errors for common conditions

### Error Handling Overhead
- Error checking has minimal overhead
- Type assertions are relatively cheap
- Error wrapping adds some overhead

## Common Pitfalls

### 1. Ignoring Errors
```go
// ❌ Bad
file, _ := os.Open("config.txt")

// ✅ Good
file, err := os.Open("config.txt")
if err != nil {
    return fmt.Errorf("failed to open config: %w", err)
}
```

### 2. Losing Context
```go
// ❌ Bad
if err != nil {
    return errors.New("processing failed")
}

// ✅ Good
if err != nil {
    return fmt.Errorf("processing failed: %w", err)
}
```

### 3. Resource Leaks
```go
// ❌ Bad
file, err := os.Open("data.txt")
if err != nil {
    return err
}
// If error occurs later, file isn't closed

// ✅ Good
file, err := os.Open("data.txt")
if err != nil {
    return err
}
defer file.Close()
```

## Exercises

1. Create a custom error type for a payment system
2. Implement error wrapping with context preservation
3. Build a retry mechanism with exponential backoff
4. Create an error collector for batch operations
5. Implement graceful degradation with fallback methods
6. Build error metrics and monitoring
7. Create error handling middleware for web APIs
8. Implement circuit breaker pattern
9. Test error handling paths comprehensively
10. Design error handling for concurrent operations
