# Go Programming Language — Introduction

## What is Go?

Go (also known as Golang) is a statically typed, compiled programming language designed at **Google** by Robert Griesemer, Rob Pike, and Ken Thompson. It was publicly announced in 2009 and reached version 1.0 in 2012.

Go was created to address shortcomings in other languages used at Google — particularly to combine the efficiency of C/C++ with the ease of use of Python, while adding first-class support for concurrency.

---

## Key Characteristics

| Feature | Description |
|---|---|
| Typing | Statically typed |
| Compilation | Compiled to native machine code |
| Paradigm | Procedural, concurrent |
| Garbage Collection | Yes |
| Memory Safety | Yes |

---

## Why Go Was Created

Google engineers were frustrated with:
- **C++**: Long compile times and complexity
- **Java**: Verbose syntax and heavy runtime
- **Python**: Slow execution speed

Go was designed to be **fast to compile**, **fast to run**, and **easy to write**.

---

## Hello, World in Go

```go
package main

import "fmt"

func main() {
    fmt.Println("Hello, World!")
}
```

**Explanation:**
- `package main` — Every Go program starts with a package declaration
- `import "fmt"` — Imports the format package for I/O
- `func main()` — Entry point of the program
- `fmt.Println()` — Prints a line to standard output

---

## Go's Design Philosophy

1. **Simplicity** — The language has very few keywords (25 total)
2. **Readability** — Code should be easy to read by others
3. **Productivity** — Fast compilation and great tooling
4. **Safety** — Memory-safe and type-safe by default
5. **Concurrency** — Built-in primitives for concurrent programming

---

## Where Go is Used

- **Web servers and APIs** (e.g., backend services)
- **Cloud infrastructure** (Docker, Kubernetes are written in Go)
- **CLI tools**
- **Microservices**
- **DevOps tooling**

---

## Notable Projects Written in Go

- **Docker** — Container platform
- **Kubernetes** — Container orchestration
- **Terraform** — Infrastructure as code
- **Hugo** — Static site generator
- **CockroachDB** — Distributed SQL database

---

## Summary

Go is a modern, pragmatic language built for the real world. It strikes a balance between performance and developer productivity, making it one of the most popular languages for backend and cloud-native development today.
