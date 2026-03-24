# Lesson 8: Concurrency with Goroutines

## What is Concurrency?

Concurrency is about dealing with multiple things at once. Go makes concurrency easy with goroutines.

## Goroutines

A goroutine is a lightweight thread managed by the Go runtime. They're cheap to create and manage.

## Starting a Goroutine

Use the `go` keyword to start a goroutine:
```go
go functionName()
```

## Anonymous Goroutines

You can also start goroutines with anonymous functions:
```go
go func() {
    // code to run concurrently
}()
```

## Goroutine Characteristics

- Goroutines are extremely lightweight (starts with 2KB stack)
- They're scheduled by the Go runtime, not the OS
- They communicate through channels, not shared memory
- Multiple goroutines can run on a single OS thread

## Main Function

The main function is itself a goroutine. When main exits, all other goroutines are terminated.

## Waiting for Goroutines

Use `sync.WaitGroup` to wait for goroutines to complete:
```go
var wg sync.WaitGroup

wg.Add(1) // increment counter
go func() {
    defer wg.Done() // decrement counter
    // do work
}()
wg.Wait() // wait for counter to reach 0
```

## Common Patterns

### Worker Pool Pattern
Multiple goroutines processing work from a queue.

### Pipeline Pattern
Data flows through a series of goroutines, each performing a transformation.

### Fan-in/Fan-out Pattern
Distribute work to multiple goroutines (fan-out) and collect results (fan-in).

## Best Practices

- Don't create too many goroutines
- Use channels for communication between goroutines
- Always handle goroutine cleanup
- Be careful with shared data - use mutexes or channels

## Practice

- Run `go run ./examples/08-concurrency-goroutines`
- Experiment with different numbers of goroutines
- Try implementing a simple worker pool
