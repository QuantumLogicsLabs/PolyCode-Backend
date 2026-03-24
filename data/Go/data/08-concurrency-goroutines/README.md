# Goroutines and Concurrency in Go

This directory contains comprehensive examples of goroutines, concurrency patterns, and synchronization in Go.

## Files

- **main.go** - Basic goroutine examples
- **worker-pools.go** - Worker pool patterns and implementations
- **sync-patterns.go** - Synchronization primitives and patterns
- **goroutine-lifecycle.go** - Goroutine lifecycle management
- **README.md** - This file

## Concurrency Concepts

### Goroutines
- Basic goroutine creation and execution
- Goroutine lifecycle management
- Goroutine pools and reuse
- Graceful shutdown patterns

### Synchronization
- Mutex and RWMutex usage
- WaitGroup for coordination
- Atomic operations
- Condition variables
- Once initialization

### Patterns
- Worker pools
- Pipeline processing
- Fan-in/Fan-out
- Rate limiting
- Circuit breaker
- Supervisor pattern

## Key Features Demonstrated

### Basic Goroutines
```go
go func() {
    fmt.Println("Hello from goroutine")
}()
```

### Worker Pool
```go
jobs := make(chan int, 100)
results := make(chan int, 100)

for w := 1; w <= numWorkers; w++ {
    go worker(w, jobs, results)
}
```

### Synchronization
```go
var mu sync.Mutex
mu.Lock()
// critical section
mu.Unlock()
```

### WaitGroup
```go
var wg sync.WaitGroup
wg.Add(1)
go func() {
    defer wg.Done()
    // do work
}()
wg.Wait()
```

## Worker Pool Patterns

### Basic Worker Pool
```go
func worker(id int, jobs <-chan int, results chan<- int) {
    for j := range jobs {
        fmt.Printf("Worker %d processing job %d\n", id, j)
        results <- j * j
    }
}
```

### Dynamic Worker Pool
```go
func startWorker() {
    workerWg.Add(1)
    go func() {
        defer workerWg.Done()
        dynamicWorker(id, jobs, results)
    }()
}
```

### Worker Pool with Results
```go
type JobResult struct {
    JobID    int
    WorkerID int
    Result   int
    Error    error
}
```

## Synchronization Patterns

### Mutex Usage
```go
type Counter struct {
    mu    sync.Mutex
    value int
}

func (c *Counter) Increment() {
    c.mu.Lock()
    defer c.mu.Unlock()
    c.value++
}
```

### RWMutex for Read-Heavy Workloads
```go
type DataStore struct {
    mu   sync.RWMutex
    data map[string]string
}

func (ds *DataStore) Get(key string) (string, bool) {
    ds.mu.RLock()
    defer ds.mu.RUnlock()
    value, exists := ds.data[key]
    return value, exists
}
```

### Atomic Operations
```go
type AtomicCounter struct {
    value int64
}

func (ac *AtomicCounter) Increment() {
    atomic.AddInt64(&ac.value, 1)
}
```

### WaitGroup for Coordination
```go
var wg sync.WaitGroup
for i := 0; i < 5; i++ {
    wg.Add(1)
    go func(id int) {
        defer wg.Done()
        process(id)
    }(i)
}
wg.Wait()
```

## Goroutine Lifecycle Management

### Context-based Cancellation
```go
ctx, cancel := context.WithCancel(context.Background())
go func(ctx context.Context) {
    select {
    case <-ctx.Done():
        return
    default:
        // do work
    }
}(ctx)
```

### Timeout Handling
```go
ctx, cancel := context.WithTimeout(context.Background(), time.Second*5)
defer cancel()

select {
case result := <-ch:
    return result
case <-ctx.Done():
    return ctx.Err()
}
```

### Graceful Shutdown
```go
type Service struct {
    quit chan struct{}
    wg   sync.WaitGroup
}

func (s *Service) Shutdown() {
    close(s.quit)
    s.wg.Wait()
}
```

## Advanced Patterns

### Pipeline Pattern
```go
func pipelineStage(input <-chan int, output chan<- int) {
    for value := range input {
        result := process(value)
        output <- result
    }
}
```

### Fan-In Pattern
```go
func fanIn(inputs ...<-chan int) <-chan int {
    output := make(chan int)
    var wg sync.WaitGroup
    
    for _, input := range inputs {
        wg.Add(1)
        go func(ch <-chan int) {
            defer wg.Done()
            for v := range ch {
                output <- v
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

### Rate Limiting
```go
func rateLimiter(rate int) <-chan time.Time {
    return time.Tick(time.Second / time.Duration(rate))
}

func worker(rate <-chan time.Time) {
    for range rate {
        // do work at controlled rate
    }
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
1. **Use channels for communication** between goroutines
2. **Prefer synchronization primitives** over channels when appropriate
3. **Handle goroutine cleanup** properly with defer
4. **Use context for cancellation** and timeouts
5. **Avoid goroutine leaks** by ensuring they can exit
6. **Use worker pools** for managing many goroutines
7. **Implement graceful shutdown** for services

### ❌ Don'ts
1. **Don't share memory** without proper synchronization
2. **Don't create goroutine leaks** that never exit
3. **Don't block goroutines** indefinitely
4. **Don't ignore cancellation signals**
5. **Don't use mutexes** when channels would be clearer
6. **Don't create too many goroutines** without management

## Performance Considerations

### Goroutine Overhead
- Goroutines are lightweight (~2KB stack)
- Can run millions concurrently
- Scheduler manages execution efficiently

### Channel Performance
- Buffered channels reduce contention
- Channel operations have some overhead
- Consider sync primitives for simple coordination

### Memory Usage
- Goroutine stacks grow as needed
- Channel buffers consume memory
- Monitor for goroutine leaks

## Common Pitfalls

### 1. Goroutine Leaks
```go
// ❌ Bad: Goroutine never exits
go func() {
    for {
        doWork()
    }
}()

// ✅ Good: Goroutine can exit
go func(ctx context.Context) {
    for {
        select {
        case <-ctx.Done():
            return
        default:
            doWork()
        }
    }
}(ctx)
```

### 2. Race Conditions
```go
// ❌ Bad: Shared state without synchronization
var counter int
go func() {
    counter++
}()

// ✅ Good: Proper synchronization
var counter int64
go func() {
    atomic.AddInt64(&counter, 1)
}()
```

### 3. Blocking Operations
```go
// ❌ Bad: Blocking without cancellation
result := <-ch

// ✅ Good: Select with cancellation
select {
case result := <-ch:
    return result
case <-ctx.Done():
    return ctx.Err()
}
```

## Running the Examples

```bash
go run main.go
go run worker-pools.go
go run sync-patterns.go
go run goroutine-lifecycle.go
```

## Testing Concurrent Code

### Race Condition Detection
```bash
go run -race program.go
go test -race ./...
```

### Testing Goroutines
```go
func TestWorkerPool(t *testing.T) {
    jobs := make(chan int, 10)
    results := make(chan int, 10)
    
    // Start workers
    for i := 0; i < 3; i++ {
        go worker(jobs, results)
    }
    
    // Send jobs
    for i := 0; i < 10; i++ {
        jobs <- i
    }
    close(jobs)
    
    // Collect results
    for i := 0; i < 10; i++ {
        result := <-results
        assert.NotNil(t, result)
    }
}
```

## Debugging Concurrency

### Tools
- `go run -race` - Race condition detection
- `go test -race` - Test with race detection
- `pprof` - Performance profiling
- `trace` - Execution tracing

### Common Issues
- Race conditions
- Deadlocks
- Goroutine leaks
- Channel blocking
- Performance bottlenecks

## Real-World Applications

### Web Servers
- Handle multiple requests concurrently
- Connection pooling
- Graceful shutdown

### Data Processing
- Parallel processing pipelines
- Batch processing
- Stream processing

### Microservices
- Concurrent service calls
- Circuit breakers
- Rate limiting

### Background Jobs
- Task queues
- Scheduled jobs
- Cleanup tasks

## Exercises

1. Implement a worker pool with priority queues
2. Build a pipeline for data processing
3. Create a rate-limited API client
4. Implement a circuit breaker pattern
5. Build a concurrent web crawler
6. Create a background job scheduler
7. Implement fan-in/fan-out patterns
8. Build a concurrent cache with expiration
9. Create a concurrent file processor
10. Implement a distributed task queue
