# Lesson 11: Testing

## Why Test?

Testing ensures your code works correctly, prevents regressions, and documents expected behavior.

## Go's Testing Package

Go includes a built-in testing package: `testing`

## Test Files

Test files follow the naming convention: `*_test.go`
```go
// calculator_test.go
package calculator

import "testing"
```

## Test Functions

Test functions must:
- Start with `Test`
- Take a single argument: `*testing.T`
- Return nothing

```go
func TestAdd(t *testing.T) {
    result := Add(2, 3)
    expected := 5
    if result != expected {
        t.Errorf("Add(2, 3) = %d; want %d", result, expected)
    }
}
```

## Running Tests

```bash
go test                    # Run all tests in current directory
go test ./...              # Run all tests in all subdirectories
go test -v                 # Verbose output
go test -run TestAdd       # Run specific test
go test -cover             # Run with coverage
```

## Test Assertions

### Basic Assertions
```go
if result != expected {
    t.Errorf("got %v, want %v", result, expected)
}

if err != nil {
    t.Fatalf("unexpected error: %v", err)
}
```

### Helper Functions
```go
func assertEqual(t *testing.T, got, want interface{}) {
    t.Helper()
    if got != want {
        t.Errorf("got %v, want %v", got, want)
    }
}
```

## Table-Driven Tests

Run the same test with multiple inputs:

```go
func TestMultiply(t *testing.T) {
    tests := []struct {
        name     string
        a, b     int
        expected int
    }{
        {"positive", 2, 3, 6},
        {"negative", -2, 3, -6},
        {"zero", 0, 5, 0},
    }

    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            result := Multiply(tt.a, tt.b)
            if result != tt.expected {
                t.Errorf("Multiply(%d, %d) = %d; want %d", 
                    tt.a, tt.b, result, tt.expected)
            }
        })
    }
}
```

## Benchmark Tests

Measure performance:

```go
func BenchmarkAdd(b *testing.B) {
    for i := 0; i < b.N; i++ {
        Add(100, 200)
    }
}
```

Run benchmarks:
```bash
go test -bench=.
go test -bench=. -benchmem
```

## Example Tests

Examples that are both tests and documentation:

```go
func ExampleAdd() {
    result := Add(2, 3)
    fmt.Println(result)
    // Output: 5
}
```

## Testing with Setup/Teardown

### TestMain
```go
func TestMain(m *testing.M) {
    // Setup
    setup()
    
    // Run tests
    code := m.Run()
    
    // Teardown
    teardown()
    
    os.Exit(code)
}
```

### Subtests with Setup
```go
func TestDatabase(t *testing.T) {
    db := setupDatabase(t)
    defer cleanupDatabase(db)
    
    t.Run("UserCreation", func(t *testing.T) {
        // test with db
    })
    
    t.Run("UserQuery", func(t *testing.T) {
        // test with db
    })
}
```

## Mocking and Fakes

### Interface-based Testing
```go
type Database interface {
    GetUser(id int) (*User, error)
    SaveUser(user *User) error
}

type MockDatabase struct {
    users map[int]*User
}

func (m *MockDatabase) GetUser(id int) (*User, error) {
    return m.users[id], nil
}
```

## Test Coverage

```bash
go test -cover
go test -coverprofile=coverage.out
go tool cover -html=coverage.out
```

## Integration Tests

Test multiple components together:

```go
func TestUserWorkflow(t *testing.T) {
    // Test complete user registration flow
    // involving database, email service, etc.
}
```

## Best Practices

- Write tests as you write code
- Test both happy path and error cases
- Use descriptive test names
- Keep tests simple and focused
- Use table-driven tests for multiple scenarios
- Aim for high coverage but focus on important code paths

## Testing Tools

### Popular Testing Libraries
- `testify/assert` - Rich assertion functions
- `gomock` - Mock generation
- `ginkgo` - BDD-style testing framework

### Continuous Integration
Run tests automatically on code changes using:
- GitHub Actions
- GitLab CI
- Jenkins

## Practice

- Run `go test ./examples/11-testing`
- Write tests for the calculator package
- Try benchmarking different functions
- Practice table-driven tests
