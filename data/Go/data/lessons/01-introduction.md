# Lesson 1: Introduction To Go

## What Is Go?

Go is a programming language designed to be simple, fast, readable, and practical.

It is commonly used for:

- backend services
- command-line tools
- cloud software
- networking tools

## Why Learn Go?

- clean syntax
- fast compilation
- strong standard library
- built-in concurrency support

## Basic Program Shape

Every Go program starts in the `main` package, and execution begins in the `main()` function.

```go
package main

import "fmt"

func main() {
    fmt.Println("Hello, Go!")
}
```

## Important Ideas

- `package main` means this file can build into an executable program
- `import` brings in packages from the standard library or your own code
- `fmt.Println` prints text to the terminal

## Practice

- Run `go run ./examples/01-hello`
- Change the message
- Print your name and your learning goal
