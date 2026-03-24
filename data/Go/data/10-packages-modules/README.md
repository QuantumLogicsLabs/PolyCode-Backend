# Packages and Modules in Go

This directory contains comprehensive examples of Go packages, modules, and dependency management.

## Files

- **main.go** - Basic package and module examples
- **dependency-management.go** - Go module dependency management
- **package-design.go** - Package design principles and patterns
- **module-workspace.go** - Go module workspaces
- **README.md** - This file

## Package and Module Concepts

### Go Modules
- Module initialization and management
- Dependency resolution
- Version management
- Private modules
- Module replacement

### Package Design
- Package organization principles
- Naming conventions
- API design
- Documentation
- Testing strategies

### Workspaces
- Multi-module development
- Workspace management
- Cross-module dependencies
- CI/CD integration

## Key Features Demonstrated

### Module Management
```bash
go mod init github.com/user/project
go mod tidy
go mod download
go mod verify
```

### Package Structure
```go
package calculator

// Add returns the sum of two integers
func Add(a, b int) int {
    return a + b
}
```

### Workspace Setup
```bash
go work init
go work use ./module1
go work use ./module2
go work sync
```

## Module Management

### Basic Module Commands
```bash
# Initialize a new module
go mod init github.com/user/project

# Add dependencies
go get github.com/pkg/errors

# Update dependencies
go get -u ./...

# Remove unused dependencies
go mod tidy

# Download dependencies
go mod download

# Verify dependencies
go mod verify
```

### Version Management
```bash
# List all dependencies
go list -m all

# Check for updates
go list -u -m all

# Update specific package
go get github.com/pkg/errors@v1.5.0

# Update all dependencies
go get -u ./...
```

### Private Modules
```bash
# Set GOPRIVATE for private modules
export GOPRIVATE=github.com/company/*

# Use .netrc for authentication
machine github.com
login username
password token
```

## Package Design Principles

### Package Organization
1. **Single Responsibility** - One clear purpose per package
2. **Cohesion** - Related functionality grouped together
3. **Low Coupling** - Minimal dependencies between packages
4. **Clear API** - Well-defined public interface

### Naming Conventions
```go
// Good: Short, lowercase, single word
package fmt
package strings
package http

// Bad: Long, camelCase, generic
package stringUtilities
package httpClient
package common
```

### Package Structure
```
calculator/
├── calculator.go     // Main API
├── advanced.go      // Advanced operations
├── constants.go     // Package constants
├── doc.go           // Package documentation
└── calculator_test.go // Tests
```

### API Design
```go
// Public API - exported names
func Add(a, b int) int
func Subtract(a, b int) int

// Internal implementation - unexported names
type calculator struct{}
func (c *calculator) calculate(a, b int) int
```

## Workspace Management

### Creating a Workspace
```bash
# Initialize workspace
go work init

# Add modules to workspace
go work use ./app
go work use ./lib
go work use ./utils

# Sync workspace
go work sync
```

### Workspace Structure
```
my-project/
├── go.work
├── go.work.sum
├── app/
│   ├── go.mod
│   ├── go.sum
│   └── main.go
├── lib/
│   ├── go.mod
│   ├── go.sum
│   └── lib.go
└── utils/
    ├── go.mod
    ├── go.sum
    └── utils.go
```

### go.work File
```go
go 1.21

use (
    ./app
    ./lib
    ./utils
)

replace github.com/external/repo => ./local/repo
```

## Dependency Management

### Dependency Resolution
- **Minimal Version Selection (MVS)** - Selects highest compatible version
- **Module Graph** - Builds dependency graph
- **Version Constraints** - Semantic versioning support

### Dependency Patterns
```go
// Direct dependency
require github.com/pkg/errors v1.4.0

// Indirect dependency (added automatically)
// require github.com/some/dependency v1.2.3 // indirect

// Replace with local version
replace github.com/external/repo => ../local/repo

// Replace with different version
replace golang.org/x/text => v0.3.8
```

### Environment Variables
```bash
# Module proxy
GOPROXY=https://proxy.golang.org,direct

# Private modules
GOPRIVATE=github.com/company/*

# Module cache
GOMODCACHE=/path/to/cache

# Checksum database
GOSUMDB=sum.golang.org
```

## Package Patterns

### Utility Package
```go
package strings

// Join concatenates strings
func Join(elems []string, sep string) string

// Split splits strings
func Split(s, sep string) []string
```

### Service Package
```go
package database

type DB struct {
    conn *sql.DB
}

func NewDB(dsn string) (*DB, error) {
    // Initialize database
}

func (db *DB) Query(query string, args ...interface{}) (*sql.Rows, error) {
    // Execute query
}
```

### Client Package
```go
package http

type Client struct {
    client *http.Client
}

func NewClient() *Client {
    return &Client{
        client: &http.Client{Timeout: 30 * time.Second},
    }
}

func (c *Client) Get(url string) (*http.Response, error) {
    // Make GET request
}
```

## Testing Packages

### Unit Testing
```go
func TestAdd(t *testing.T) {
    tests := []struct {
        name     string
        a, b     int
        expected int
    }{
        {"positive", 2, 3, 5},
        {"negative", -2, -3, -5},
        {"zero", 0, 5, 5},
    }

    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            result := Add(tt.a, tt.b)
            if result != tt.expected {
                t.Errorf("Add(%d, %d) = %d; want %d",
                    tt.a, tt.b, result, tt.expected)
            }
        })
    }
}
```

### Benchmark Testing
```go
func BenchmarkAdd(b *testing.B) {
    for i := 0; i < b.N; i++ {
        Add(100, 200)
    }
}
```

### Example Testing
```go
func ExampleAdd() {
    result := Add(2, 3)
    fmt.Println(result)
    // Output: 5
}
```

## Best Practices

### ✅ Do's
1. **Use semantic versioning** for module versions
2. **Keep packages focused** on single responsibility
3. **Document public API** clearly
4. **Write comprehensive tests** with good coverage
5. **Use consistent naming** conventions
6. **Minimize dependencies** between packages
7. **Use interfaces** for extensibility
8. **Handle errors** consistently

### ❌ Don'ts
1. **Don't create utils packages** - be specific
2. **Don't use circular dependencies**
3. **Don't export implementation details**
4. **Don't ignore version compatibility**
5. **Don't forget to test** edge cases
6. **Don't use generic names** like "common"
7. **Don't break API compatibility** in major versions
8. **Don't ignore security** updates

## Common Patterns

### Factory Pattern
```go
package calculator

type Calculator interface {
    Add(a, b int) int
    Subtract(a, b int) int
}

func NewCalculator() Calculator {
    return &basicCalculator{}
}
```

### Builder Pattern
```go
package config

type Config struct {
    host string
    port int
}

type Builder struct {
    config Config
}

func NewBuilder() *Builder {
    return &Builder{config: Config{}}
}

func (b *Builder) Host(host string) *Builder {
    b.config.host = host
    return b
}

func (b *Builder) Build() Config {
    return b.config
}
```

### Repository Pattern
```go
package repository

type UserRepository interface {
    Find(id string) (*User, error)
    Save(user *User) error
    Delete(id string) error
}

type userRepository struct {
    db *sql.DB
}

func NewUserRepository(db *sql.DB) UserRepository {
    return &userRepository{db: db}
}
```

## Running the Examples

```bash
go run main.go
go run dependency-management.go
go run package-design.go
go run module-workspace.go
```

## Testing the Examples

```bash
go test ./...
go test -v ./calculator
go test -bench ./...
go test -race ./...
```

## Module Commands Reference

### Basic Commands
- `go mod init` - Initialize module
- `go mod tidy` - Clean up dependencies
- `go mod download` - Download dependencies
- `go mod verify` - Verify dependencies

### Version Commands
- `go list -m all` - List all modules
- `go list -u -m all` - Check for updates
- `go get package@version` - Get specific version
- `go get -u ./...` - Update all dependencies

### Workspace Commands
- `go work init` - Initialize workspace
- `go work use` - Add module to workspace
- `go work sync` - Sync workspace
- `go work list` - List workspace modules

## Troubleshooting

### Common Issues
1. **Module not found** - Check go.mod and GOPATH
2. **Version conflicts** - Use replace directive
3. **Circular dependencies** - Restructure packages
4. **Build failures** - Run `go mod tidy`
5. **Test failures** - Check module dependencies

### Debug Commands
```bash
go mod why github.com/pkg/errors
go mod graph
go mod download -json
go list -deps ./...
```

## CI/CD Integration

### GitHub Actions
```yaml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - uses: actions/setup-go@v2
        with:
          go-version: 1.21
      - run: go mod download
      - run: go test ./...
      - run: go build ./...
```

### Docker Build
```dockerfile
FROM golang:1.21-alpine AS builder
WORKDIR /app
COPY go.mod go.sum ./
RUN go mod download
COPY . .
RUN go build ./...

FROM alpine:latest
RUN apk --no-cache add ca-certificates
WORKDIR /root/
COPY --from=builder /app/app .
CMD ["./app"]
```

## Exercises

1. Create a new Go module with multiple packages
2. Design a package with clear API boundaries
3. Implement a workspace with multiple modules
4. Add comprehensive tests to a package
5. Set up CI/CD for a multi-module project
6. Create a package with version compatibility
7. Implement dependency injection pattern
8. Design a package with interfaces
9. Create a workspace with private modules
10. Set up automated dependency updates
