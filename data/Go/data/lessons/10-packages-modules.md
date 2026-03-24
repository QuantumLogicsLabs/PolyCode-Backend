# Lesson 10: Packages and Modules

## Packages in Go

Every Go program is made up of packages. Packages organize code and provide reusability.

## Package Declaration

Every Go file starts with a package declaration:
```go
package main  // executable programs
package math  // library code
```

## The `main` Package

- Special package that creates executable programs
- Must have a `main()` function
- Entry point of the program

## Importing Packages

```go
import "fmt"                    // single import
import (
    "os"                        // multiple imports
    "strings"
)
```

## Imported Package Aliases

```go
import (
    "fmt"
    myfmt "my/custom/fmt"       // alias
    _ "database/sql/driver"     // blank import (side effects only)
)
```

## Standard Library Packages

Common useful packages:
- `fmt` - formatted I/O
- `os` - operating system interface
- `strings` - string manipulation
- `math` - mathematical functions
- `time` - time and date
- `net/http` - HTTP client/server
- `encoding/json` - JSON encoding/decoding

## Creating Your Own Packages

1. Create a directory with the package name
2. Add Go files with the same package declaration
3. Export names by starting with capital letter

```go
// math/calculator.go
package math

// Add is exported (starts with capital)
func Add(a, b int) int {
    return a + b
}

// subtract is not exported (starts with lowercase)
func subtract(a, b int) int {
    return a - b
}
```

## Go Modules

Modules are Go's way of managing dependencies.

### Creating a Module

```bash
go mod init module-name
```

This creates a `go.mod` file:
```go
module module-name

go 1.22
```

### Adding Dependencies

```bash
go get package-name
```

### Tidying Dependencies

```bash
go mod tidy
```

## Package Visibility Rules

- **Exported**: Names starting with capital letter are public
- **Unexported**: Names starting with lowercase are private to the package

## Package Organization

### Common Package Structures

```
project/
├── go.mod
├── main.go
├── math/
│   ├── calculator.go
│   └── geometry.go
├── utils/
│   ├── strings.go
│   └── files.go
└── config/
    └── settings.go
```

### Import Paths

```go
import (
    "project/math"
    "project/utils"
    "project/config"
)
```

## Third-Party Packages

### Finding Packages

- Go Package Discovery: pkg.go.dev
- GitHub repositories
- Company internal packages

### Using Third-Party Packages

```bash
go get github.com/pkg/errors
```

```go
import "github.com/pkg/errors"
```

## Version Management

Go modules use semantic versioning:
- `v1.2.3` - specific version
- `v1` - latest v1.x.x
- `latest` - latest version

## Best Practices

- Keep packages focused and small
- Use descriptive package names
- Export only what's necessary
- Organize related functionality in packages
- Use modules for dependency management

## Practice

- Run `go run ./examples/10-packages-modules`
- Create your own package
- Practice importing and using packages
- Try adding a third-party dependency
