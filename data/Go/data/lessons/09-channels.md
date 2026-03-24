# Lesson 9: Channels

## What are Channels?

Channels are the pipes that connect concurrent goroutines. They allow goroutines to communicate with each other and synchronize their execution.

## Creating Channels

```go
// Unbuffered channel (synchronous)
ch := make(chan int)

// Buffered channel (asynchronous)
ch := make(chan int, 10)
```

## Channel Operations

### Sending
```go
ch <- value  // Send value to channel
```

### Receiving
```go
value := <-ch  // Receive value from channel
```

### Closing
```go
close(ch)  // Close channel
```

## Unbuffered vs Buffered Channels

### Unbuffered Channels
- Synchronous: sender blocks until receiver is ready
- Provides synchronization between goroutines

### Buffered Channels
- Asynchronous: sender can send up to buffer capacity without receiver
- Useful for producer-consumer patterns

## Channel Directions

You can specify channel direction in function signatures:

```go
// Send-only channel
func send(ch chan<- int) {
    ch <- 42
}

// Receive-only channel
func receive(ch <-chan int) {
    value := <-ch
}
```

## Multiple Values

Use `for...range` to receive all values from a channel until it's closed:

```go
for value := range ch {
    fmt.Println(value)
}
```

## Select Statement

The `select` statement lets a goroutine wait on multiple communication operations:

```go
select {
case value := <-ch1:
    fmt.Println("Received from ch1:", value)
case value := <-ch2:
    fmt.Println("Received from ch2:", value)
case ch3 <- value:
    fmt.Println("Sent to ch3")
default:
    fmt.Println("No channel operation ready")
}
```

## Common Patterns

### Pipeline Pattern
```go
func square(numbers <-chan int, result chan<- int) {
    for n := range numbers {
        result <- n * n
    }
    close(result)
}
```

### Fan-in Pattern
```go
func fanIn(input1, input2 <-chan int, output chan<- int) {
    for {
        select {
        case value := <-input1:
            output <- value
        case value := <-input2:
            output <- value
        }
    }
}
```

### Worker Pool Pattern
```go
func worker(id int, jobs <-chan int, results chan<- int) {
    for job := range jobs {
        fmt.Printf("Worker %d processing job %d\n", id, job)
        results <- job * 2
    }
}
```

## Best Practices

- Close channels when you're done sending
- Don't close channels from the receiver side
- Use buffered channels when you know the capacity
- Always check if a channel is closed when receiving (using `value, ok := <-ch`)

## Practice

- Run `go run ./examples/09-channels`
- Try implementing different channel patterns
- Experiment with buffered vs unbuffered channels
