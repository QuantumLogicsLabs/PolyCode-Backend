# Go Programming Language — Packages, Modules & Tooling

## Packages

Every Go file belongs to a package. Packages are Go's way of organizing and reusing code.

```go
package main // executable package

package utils // library package
```

### Importing Packages
```go
import (
    "fmt"           // standard library
    "math/rand"     // nested package
    "net/http"      // HTTP server/client
    "os"
    "strings"
)
```

### Creating a Package
```go
// file: mathutils/mathutils.go
package mathutils

// Exported function (capital letter)
func Square(x float64) float64 {
    return x * x
}

// Unexported (private)
func helper() {}
```

```go
// main.go
import "myproject/mathutils"

result := mathutils.Square(5) // 25
```

> **Rule:** Names starting with a capital letter are **exported** (public). Lowercase names are **unexported** (private).

---

## Go Modules

Modules are Go's dependency management system (introduced in Go 1.11).

### Initialize a Module
```bash
go mod init github.com/username/myproject
```

This creates `go.mod`:
```
module github.com/username/myproject

go 1.21
```

### Adding Dependencies
```bash
go get github.com/gin-gonic/gin
```

### go.sum
Automatically generated file that locks dependency checksums for security.

### Useful Module Commands
```bash
go mod tidy      # Remove unused dependencies
go mod download  # Download all dependencies
go mod vendor    # Copy deps to vendor/
go list -m all   # List all modules
```

---

## Standard Library Highlights

| Package | Purpose |
|---|---|
| `fmt` | Formatted I/O |
| `os` | OS interaction (files, env) |
| `io` | I/O primitives |
| `net/http` | HTTP client and server |
| `encoding/json` | JSON encode/decode |
| `math` | Math functions |
| `strings` | String manipulation |
| `strconv` | String conversions |
| `time` | Time and duration |
| `sync` | Concurrency primitives |
| `context` | Cancellation, deadlines |
| `log` | Logging |
| `testing` | Unit testing |

---

## Go CLI Tooling

```bash
go run main.go         # Compile and run
go build               # Compile binary
go build -o myapp      # Named binary
go test ./...          # Run all tests
go fmt ./...           # Format code
go vet ./...           # Static analysis
go doc fmt.Println     # Show documentation
go install             # Install binary to GOPATH
```

---

## Writing Tests

```go
// file: math_test.go
package mathutils

import "testing"

func TestSquare(t *testing.T) {
    got := Square(4)
    want := 16.0
    if got != want {
        t.Errorf("Square(4) = %v; want %v", got, want)
    }
}
```

```bash
go test -v        # verbose output
go test -cover    # show code coverage
```

---

## Interfaces

Interfaces are implicit in Go — no `implements` keyword needed.

```go
type Animal interface {
    Speak() string
}

type Dog struct{}
func (d Dog) Speak() string { return "Woof!" }

type Cat struct{}
func (c Cat) Speak() string { return "Meow!" }

func makeNoise(a Animal) {
    fmt.Println(a.Speak())
}

makeNoise(Dog{}) // Woof!
makeNoise(Cat{}) // Meow!
```

---

## Error Handling

```go
import "errors"

var ErrNotFound = errors.New("item not found")

func findItem(id int) (string, error) {
    if id <= 0 {
        return "", ErrNotFound
    }
    return "item", nil
}

item, err := findItem(-1)
if err != nil {
    fmt.Println("Error:", err)
}
```

---

## Summary

Go's package system, module management, and rich standard library make it a self-sufficient ecosystem. The CLI tooling (`go fmt`, `go test`, `go vet`) enforces consistency across all Go codebases, making collaboration seamless.
