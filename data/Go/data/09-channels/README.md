# Channels in Go

This directory contains comprehensive examples of Go channels, from basic usage to advanced patterns and techniques.

## Files

- **main.go** - Basic channel examples
- **channel-patterns.go** - Common channel patterns (fan-in, fan-out, pipeline)
- **select-patterns.go** - Select statement patterns and techniques
- **advanced-channels.go** - Advanced channel implementations and use cases
- **README.md** - This file

## Channel Concepts

### Basic Channels
- Channel creation and operations
- Buffered vs unbuffered channels
- Channel closing and range
- Directional channels

### Channel Patterns
- Fan-in and fan-out patterns
- Pipeline processing
- Worker pools
- Rate limiting
- Pub/Sub systems

### Select Statements
- Basic select usage
- Timeout with select
- Non-blocking operations
- Multiplexing
- Context cancellation

### Advanced Techniques
- Channel as semaphore
- Channel for coordination
- Channel for streaming
- Channel for data structures
- Channel for resource management

## Key Features Demonstrated

### Basic Channel Operations
```go
// Create channels
ch := make(chan int)           // Unbuffered
ch := make(chan int, 10)      // Buffered

// Send and receive
ch <- value                    // Send
value := <-ch                  // Receive
value, ok := <-ch             // Receive with close check

// Close channel
close(ch)
```

### Fan-in Pattern
```go
func fanIn(inputs ...<-chan int) <-chan int {
    output := make(chan int)
    var wg sync.WaitGroup
    
    for _, input := range inputs {
        wg.Add(1)
        go func(ch <-chan int) {
            defer wg.Done()
            for val := range ch {
                output <- val
            }
        }(input)
    }
    
    go func() {
        wg.Wait()
        close(output)
    }()
    
    return output
}
```

### Fan-out Pattern
```go
func fanOut(input <-chan int, workers int) []<-chan int {
    outputs := make([]<-chan int, workers)
    
    for i := 0; i < workers; i++ {
        output := make(chan int)
        outputs[i] = output
        
        go func(ch chan<- int) {
            for val := range input {
                ch <- val
            }
            close(ch)
        }(output)
    }
    
    return outputs
}
```

### Pipeline Pattern
```go
func stage1(input <-chan int) <-chan int {
    output := make(chan int)
    go func() {
        defer close(output)
        for val := range input {
            output <- val * 2
        }
    }()
    return output
}

func stage2(input <-chan int) <-chan int {
    output := make(chan int)
    go func() {
        defer close(output)
        for val := range input {
            output <- val + 10
        }
    }()
    return output
}
```

### Select with Timeout
```go
select {
case result := <-ch:
    return result
case <-time.After(time.Second * 5):
    return errors.New("timeout")
case <-ctx.Done():
    return ctx.Err()
}
```

### Non-blocking Operations
```go
select {
case val := <-ch:
    fmt.Printf("Received: %v\n", val)
default:
    fmt.Println("No data available")
}
```

## Channel Patterns

### Worker Pool
```go
func worker(id int, jobs <-chan Job, results chan<- Result) {
    for job := range jobs {
        result := process(job)
        results <- Result{WorkerID: id, Value: result}
    }
}

func startWorkerPool(numWorkers int) (chan<- Job, <-chan Result) {
    jobs := make(chan Job, 100)
    results := make(chan Result, 100)
    
    for i := 0; i < numWorkers; i++ {
        go worker(i, jobs, results)
    }
    
    return jobs, results
}
```

### Rate Limiting
```go
func rateLimiter(rate time.Duration) <-chan time.Time {
    return time.Tick(rate)
}

func processWithRateLimit() {
    limiter := rateLimiter(time.Millisecond * 200)
    
    for i := 0; i < 10; i++ {
        <-limiter
        doWork(i)
    }
}
```

### Semaphore Pattern
```go
func semaphore(capacity int) chan struct{} {
    return make(chan struct{}, capacity)
}

func limitedAccess(sem chan struct{}) {
    sem <- struct{}{}
    defer func() { <-sem }()
    
    // Do limited work
}
```

### Pub/Sub Pattern
```go
type PubSub struct {
    subscribers map[chan<- string]bool
    mu          sync.RWMutex
}

func (ps *PubSub) Subscribe() <-chan string {
    ch := make(chan string, 10)
    ps.mu.Lock()
    ps.subscribers[ch] = true
    ps.mu.Unlock()
    return ch
}

func (ps *PubSub) Publish(message string) {
    ps.mu.RLock()
    defer ps.mu.RUnlock()
    
    for ch := range ps.subscribers {
        select {
        case ch <- message:
        default:
            // Slow subscriber
        }
    }
}
```

## Select Statement Patterns

### Basic Select
```go
select {
case msg1 := <-ch1:
    fmt.Printf("From ch1: %s\n", msg1)
case msg2 := <-ch2:
    fmt.Printf("From ch2: %s\n", msg2)
}
```

### Timeout with Select
```go
select {
case result := <-ch:
    return result
case <-time.After(time.Second * 5):
    return errors.New("operation timed out")
}
```

### Context Cancellation
```go
select {
case result := <-ch:
    return result
case <-ctx.Done():
    return ctx.Err()
}
```

### Non-blocking Operations
```go
select {
case val := <-ch:
    fmt.Printf("Received: %v\n", val)
default:
    fmt.Println("No data available")
}
```

### Priority Channels
```go
select {
case highPriority := <-highCh:
    handleHighPriority(highPriority)
case mediumPriority := <-mediumCh:
    handleMediumPriority(mediumPriority)
case lowPriority := <-lowCh:
    handleLowPriority(lowPriority)
}
```

## Advanced Channel Techniques

### Channel as Data Structure
```go
// Queue implementation
type Queue struct {
    items chan interface{}
}

func (q *Queue) Enqueue(item interface{}) {
    q.items <- item
}

func (q *Queue) Dequeue() interface{} {
    return <-q.items
}

// Stack implementation
type Stack struct {
    items chan interface{}
}

func (s *Stack) Push(item interface{}) {
    s.items <- item
}

func (s *Stack) Pop() interface{} {
    // Read all and put back all except last
    var items []interface{}
    for item := range s.items {
        items = append(items, item)
    }
    
    for i := 0; i < len(items)-1; i++ {
        s.items <- items[i]
    }
    
    return items[len(items)-1]
}
```

### Ring Buffer
```go
type RingBuffer struct {
    data chan interface{}
}

func (rb *RingBuffer) Write(item interface{}) {
    select {
    case rb.data <- item:
    default:
        <-rb.data // Remove oldest
        rb.data <- item
    }
}
```

### Generator Pattern
```go
func fibonacci(n int) <-chan int {
    ch := make(chan int)
    
    go func() {
        defer close(ch)
        a, b := 0, 1
        for i := 0; i < n; i++ {
            ch <- a
            a, b = b, a+b
        }
    }()
    
    return ch
}
```

### Stream Processing
```go
func streamProcessor(input <-chan int) <-chan int {
    output := make(chan int)
    
    go func() {
        defer close(output)
        
        for val := range input {
            // Process and forward
            processed := val * 2
            output <- processed
        }
    }()
    
    return output
}
```

## Best Practices

### ✅ Do's
1. **Use channels for communication** between goroutines
2. **Close channels** when done sending
3. **Use range over channels** for consuming all values
4. **Handle channel closing** with comma ok
5. **Use buffered channels** for flow control
6. **Prefer select** for multiple channel operations
7. **Use context** for cancellation and timeouts

### ❌ Don'ts
1. **Don't close channels** from the receiver side
2. **Don't send to closed channels**
3. **Don't close channels multiple times**
4. **Don't use nil channels** (they block forever)
5. **Don't ignore channel closing**
6. **Don't create deadlocks** with circular dependencies
7. **Don't forget to handle** all select cases

## Performance Considerations

### Buffer Size
- **Unbuffered channels**: Synchronous, handoff
- **Buffered channels**: Asynchronous, flow control
- **Large buffers**: More throughput, more memory

### Channel Operations
- **Send**: Blocks if buffer full (unbuffered: blocks until receiver)
- **Receive**: Blocks if buffer empty (unbuffered: blocks until sender)
- **Close**: Immediate, receivers get zero value

### Memory Usage
- Each channel has overhead
- Buffer size affects memory usage
- Consider channel pooling for high-frequency operations

## Common Pitfalls

### 1. Deadlocks
```go
// ❌ Bad: Deadlock
ch1 := make(chan int)
ch2 := make(chan int)

go func() {
    ch1 <- <-ch2
}()
ch2 <- <-ch1 // Deadlock!

// ✅ Good: Use select or proper ordering
select {
case ch1 <- <-ch2:
case ch2 <- <-ch1:
}
```

### 2. Forgetting to Close
```go
// ❌ Bad: Goroutine leak
func producer() {
    ch := make(chan int)
    go func() {
        ch <- 1
        // Forget to close - consumer waits forever
    }()
    
    for val := range ch { // Will block forever
        fmt.Println(val)
    }
}

// ✅ Good: Always close
func producer() {
    ch := make(chan int)
    go func() {
        defer close(ch)
        ch <- 1
    }()
    
    for val := range ch {
        fmt.Println(val)
    }
}
```

### 3. Panic on Closed Channel
```go
// ❌ Bad: Panic on send to closed channel
ch := make(chan int)
close(ch)
ch <- 1 // Panic!

// ✅ Good: Check before sending
ch := make(chan int)
close(ch)

select {
case ch <- 1:
    // Won't execute
default:
    fmt.Println("Channel is closed")
}
```

## Running the Examples

```bash
go run main.go
go run channel-patterns.go
go run select-patterns.go
go run advanced-channels.go
```

## Testing Channel Code

### Race Detection
```bash
go run -race program.go
go test -race ./...
```

### Channel Testing
```go
func TestFanIn(t *testing.T) {
    ch1 := make(chan int, 2)
    ch2 := make(chan int, 2)
    
    ch1 <- 1
    ch1 <- 2
    ch2 <- 3
    ch2 <- 4
    
    close(ch1)
    close(ch2)
    
    result := fanIn(ch1, ch2)
    
    expected := []int{1, 2, 3, 4}
    actual := []int{}
    
    for val := range result {
        actual = append(actual, val)
    }
    
    assert.ElementsMatch(t, expected, actual)
}
```

## Real-World Applications

### Web Servers
- Request routing
- Response streaming
- Connection pooling

### Data Processing
- Pipeline processing
- Stream processing
- Batch processing

### Microservices
- Service communication
- Event streaming
- Message queuing

### Background Jobs
- Task distribution
- Result collection
- Progress tracking

## Debugging Channels

### Tools
- `go run -race` - Race condition detection
- Channel debugging with prints
- Deadlock detection with runtime

### Common Issues
- Deadlocks
- Goroutine leaks
- Race conditions
- Buffer overflow/underflow
- Channel blocking

## Exercises

1. Implement a worker pool with priority jobs
2. Build a pipeline for image processing
3. Create a rate-limited API client
4. Implement a pub/sub system with topics
5. Build a stream processor with backpressure
6. Create a concurrent cache with channels
7. Implement a distributed task queue
8. Build a real-time data processor
9. Create a concurrent file processor
10. Implement a channel-based state machine
