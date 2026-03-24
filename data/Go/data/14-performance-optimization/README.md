# Performance Optimization in Go

This directory contains comprehensive examples of performance optimization techniques, profiling tools, and best practices for Go applications.

## Files

- **main.go** - Overview of all performance optimization techniques
- **profiling-tools.go** - Detailed profiling tools and utilities
- **README.md** - This file

## Overview

This section covers advanced performance optimization in Go including:

- **Profiling and Benchmarking** - CPU, memory, and performance profiling
- **Memory Optimization** - Memory management and allocation strategies
- **CPU Optimization** - Algorithm and computation optimization
- **I/O Optimization** - Input/output and network optimization
- **Concurrency Optimization** - Goroutine and channel optimization
- **Algorithm Optimization** - Efficient algorithms and data structures
- **Data Structure Optimization** - Optimal data structure selection
- **Network Optimization** - Network and HTTP optimization
- **Database Optimization** - Database query and connection optimization

## Performance Optimization Techniques

### 1. Profiling and Benchmarking

#### CPU Profiling
```go
// Start CPU profiling
f, _ := os.Create("cpu.prof")
pprof.StartCPUProfile(f)
defer pprof.StopCPUProfile()

// Run code to profile
cpuIntensiveWork()

// Analyze with: go tool pprof cpu.prof
```

#### Memory Profiling
```go
// Create memory profile
f, _ := os.Create("mem.prof")

// Run code to profile
memoryIntensiveWork()

// Write heap profile
runtime.GC()
pprof.WriteHeapProfile(f)

// Analyze with: go tool pprof mem.prof
```

#### Block Profiling
```go
// Enable block profiling
runtime.SetBlockProfileRate(1)
defer runtime.SetBlockProfileRate(0)

// Run blocking operations
blockingWork()

// Write block profile
pprof.Lookup("block").WriteTo(f, 0)
```

#### Trace Profiling
```go
// Start trace
f, _ := os.Create("trace.out")
trace.Start(f)
defer trace.Stop()

// Run traced operations
tracedWork()

// Analyze with: go tool trace trace.out
```

### 2. Memory Optimization

#### Object Pooling
```go
type BufferPool struct {
    pool chan *Buffer
}

func (p *BufferPool) Get() *Buffer {
    select {
    case buf := <-p.pool:
        return buf
    default:
        return &Buffer{data: make([]byte, 1024)}
    }
}

func (p *BufferPool) Put(buf *Buffer) {
    select {
    case p.pool <- buf:
    default:
        // Pool is full
    }
}
```

#### Memory Reuse
```go
// Bad: allocate new slice each time
func badApproach() {
    for i := 0; i < 100; i++ {
        data := make([]byte, 1000)
        processData(data)
    }
}

// Good: reuse slice buffer
func goodApproach() {
    buffer := make([]byte, 1000)
    for i := 0; i < 100; i++ {
        // Reset buffer
        for j := range buffer {
            buffer[j] = 0
        }
        processData(buffer)
    }
}
```

#### Stack vs Heap Allocation
```go
// Stack allocation (fast)
func stackAllocation() {
    var x int = 42
    var y int = 84
    _ = x + y
}

// Heap allocation (slower)
func heapAllocation() {
    data := make([]int, 1000000)
    for i := range data {
        data[i] = i
    }
    _ = data[0]
}
```

### 3. CPU Optimization

#### Algorithm Optimization
```go
// Linear search O(n)
func linearSearch(data []int, target int) int {
    for i, val := range data {
        if val == target {
            return i
        }
    }
    return -1
}

// Binary search O(log n)
func binarySearch(data []int, target int) int {
    low, high := 0, len(data)-1
    for low <= high {
        mid := (low + high) / 2
        if data[mid] == target {
            return mid
        } else if data[mid] < target {
            low = mid + 1
        } else {
            high = mid - 1
        }
    }
    return -1
}
```

#### Loop Optimization
```go
// Bad: calculate len() each iteration
func badLoop(data []int) {
    for i := 0; i < len(data); i++ {
        data[i] = i * 2
    }
}

// Good: calculate len() once
func goodLoop(data []int) {
    length := len(data)
    for i := 0; i < length; i++ {
        data[i] = i * 2
    }
}
```

#### Parallel Processing
```go
// Sequential processing
func sequential(data []int) {
    for i := range data {
        data[i] = i * i
    }
}

// Parallel processing
func parallel(data []int) {
    var wg sync.WaitGroup
    chunkSize := 1000
    
    for i := 0; i < len(data); i += chunkSize {
        end := i + chunkSize
        if end > len(data) {
            end = len(data)
        }
        
        wg.Add(1)
        go func(start, end int) {
            defer wg.Done()
            for j := start; j < end; j++ {
                data[j] = j * j
            }
        }(i, end)
    }
    
    wg.Wait()
}
```

### 4. I/O Optimization

#### Buffer Optimization
```go
// Use appropriate buffer sizes
smallBuffer := make([]byte, 64)
mediumBuffer := make([]byte, 1024)
largeBuffer := make([]byte, 8192)

// Read with buffer
func readWithBuffer(buffer []byte) {
    // Simulate reading
    for i := range buffer {
        buffer[i] = byte(i)
    }
}
```

#### Batch Operations
```go
// Individual operations
func individual(db *sql.DB, records []Record) error {
    for _, record := range records {
        if err := db.Exec("INSERT INTO table VALUES (?, ?)", record.A, record.B); err != nil {
            return err
        }
    }
    return nil
}

// Batch operations
func batch(db *sql.DB, records []Record) error {
    stmt, err := db.Prepare("INSERT INTO table VALUES (?, ?)")
    if err != nil {
        return err
    }
    defer stmt.Close()
    
    for _, record := range records {
        if _, err := stmt.Exec(record.A, record.B); err != nil {
            return err
        }
    }
    return nil
}
```

#### Connection Pooling
```go
type ConnectionPool struct {
    connections chan net.Conn
    maxSize     int
    factory     func() (net.Conn, error)
}

func (p *ConnectionPool) Get() (net.Conn, error) {
    select {
    case conn := <-p.connections:
        return conn, nil
    default:
        return p.factory()
    }
}

func (p *ConnectionPool) Put(conn net.Conn) {
    select {
    case p.connections <- conn:
    default:
        conn.Close()
    }
}
```

### 5. Concurrency Optimization

#### Worker Pool Pattern
```go
type WorkerPool struct {
    workers   int
    jobQueue  chan Job
    jobPool   chan Job
    wg        sync.WaitGroup
}

func NewWorkerPool(workers int) *WorkerPool {
    pool := &WorkerPool{
        workers:   workers,
        jobQueue:  make(chan Job, workers*2),
        jobPool:   make(chan Job, workers*2),
    }
    
    for i := 0; i < workers; i++ {
        pool.wg.Add(1)
        go pool.worker()
    }
    
    return pool
}

func (p *WorkerPool) worker() {
    defer p.wg.Done()
    for job := range p.jobQueue {
        job.Execute()
    }
}
```

#### Channel Optimization
```go
// Unbuffered channel (synchronization)
var unbuffered = make(chan int)

// Buffered channel (asynchronous)
var buffered = make(chan int, 100)

// Select for non-blocking operations
func nonBlockingSelect() {
    select {
    case value := <-ch:
        fmt.Println("Received:", value)
    default:
        fmt.Println("No value available")
    }
}
```

#### Atomic Operations
```go
// Use atomic operations instead of mutex for simple cases
type Counter struct {
    value int64
}

func (c *Counter) Increment() {
    atomic.AddInt64(&c.value, 1)
}

func (c *Counter) Get() int64 {
    return atomic.LoadInt64(&c.value)
}
```

### 6. Algorithm Optimization

#### Sorting Algorithms
```go
// Bubble sort O(n²)
func bubbleSort(data []int) {
    n := len(data)
    for i := 0; i < n-1; i++ {
        for j := 0; j < n-i-1; j++ {
            if data[j] > data[j+1] {
                data[j], data[j+1] = data[j+1], data[j]
            }
        }
    }
}

// Quick sort O(n log n)
func quickSort(data []int) {
    if len(data) <= 1 {
        return
    }
    
    pivot := data[len(data)/2]
    var left, right []int
    
    for _, val := range data {
        if val < pivot {
            left = append(left, val)
        } else if val > pivot {
            right = append(right, val)
        }
    }
    
    quickSort(left)
    quickSort(right)
    
    copy(data, append(left, pivot, right...))
}
```

#### Memoization
```go
func memoize(fn func(int) int) func(int) int {
    cache := make(map[int]int)
    
    return func(n int) int {
        if val, exists := cache[n]; exists {
            return val
        }
        
        val := fn(n)
        cache[n] = val
        return val
    }
}

// Memoized Fibonacci
var memoizedFibonacci = memoize(func(n int) int {
    if n <= 1 {
        return n
    }
    return memoizedFibonacci(n-1) + memoizedFibonacci(n-2)
})
```

### 7. Data Structure Optimization

#### Slice Pre-allocation
```go
// Bad: no pre-allocation
func badPreallocation() {
    var data []int
    for i := 0; i < 10000; i++ {
        data = append(data, i)
    }
}

// Good: pre-allocate capacity
func goodPreallocation() {
    data := make([]int, 0, 10000)
    for i := 0; i < 10000; i++ {
        data = append(data, i)
    }
}
```

#### Map Pre-allocation
```go
// Bad: no pre-allocation
func badMapPreallocation() {
    data := make(map[int]string)
    for i := 0; i < 10000; i++ {
        data[i] = fmt.Sprintf("value-%d", i)
    }
}

// Good: pre-allocate size
func goodMapPreallocation() {
    data := make(map[int]string, 10000)
    for i := 0; i < 10000; i++ {
        data[i] = fmt.Sprintf("value-%d", i)
    }
}
```

#### String Builder
```go
// Bad: string concatenation
func badStringBuilder() {
    var result string
    for i := 0; i < 1000; i++ {
        result += fmt.Sprintf("item-%d", i)
    }
}

// Good: strings.Builder
func goodStringBuilder() {
    var builder strings.Builder
    for i := 0; i < 1000; i++ {
        builder.WriteString(fmt.Sprintf("item-%d", i))
    }
    _ = builder.String()
}
```

### 8. Network Optimization

#### Connection Reuse
```go
// HTTP client with connection pooling
client := &http.Client{
    Transport: &http.Transport{
        MaxIdleConns:        100,
        MaxIdleConnsPerHost: 10,
        IdleConnTimeout:     90 * time.Second,
    },
}
```

#### Batch Requests
```go
// Individual requests
func individualRequests(urls []string) error {
    for _, url := range urls {
        resp, err := http.Get(url)
        if err != nil {
            return err
        }
        resp.Body.Close()
    }
    return nil
}

// Batch requests using goroutines
func batchRequests(urls []string) error {
    var wg sync.WaitGroup
    errors := make(chan error, len(urls))
    
    for _, url := range urls {
        wg.Add(1)
        go func(u string) {
            defer wg.Done()
            resp, err := http.Get(u)
            if err != nil {
                errors <- err
                return
            }
            resp.Body.Close()
        }(url)
    }
    
    wg.Wait()
    close(errors)
    
    for err := range errors {
        if err != nil {
            return err
        }
    }
    return nil
}
```

### 9. Database Optimization

#### Connection Pooling
```go
// Database connection pool
db, err := sql.Open("postgres", connStr)
if err != nil {
    log.Fatal(err)
}

// Set connection pool parameters
db.SetMaxOpenConns(25)
db.SetMaxIdleConns(25)
db.SetConnMaxLifetime(5 * time.Minute)
db.SetConnMaxIdleTime(5 * time.Minute)
```

#### Prepared Statements
```go
// Prepare statement once
stmt, err := db.Prepare("SELECT name FROM users WHERE id = $1")
if err != nil {
    log.Fatal(err)
}
defer stmt.Close()

// Use multiple times
func getUser(id int) (string, error) {
    var name string
    err := stmt.QueryRow(id).Scan(&name)
    return name, err
}
```

#### Query Optimization
```go
// Use indexes
func createIndexes(db *sql.DB) error {
    _, err := db.Exec(`
        CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
        CREATE INDEX IF NOT EXISTS idx_users_created_at ON users(created_at);
    `)
    return err
}

// Use EXPLAIN to analyze queries
func explainQuery(db *sql.DB, query string) error {
    rows, err := db.Query("EXPLAIN ANALYZE " + query)
    if err != nil {
        return err
    }
    defer rows.Close()
    
    for rows.Next() {
        var plan string
        if err := rows.Scan(&plan); err != nil {
            return err
        }
        fmt.Println(plan)
    }
    return nil
}
```

## Profiling Tools and Commands

### Built-in Profiling Tools

#### CPU Profiling
```bash
# Start CPU profiling
go tool pprof http://localhost:6060/debug/pprof/profile

# Analyze CPU profile
go tool pprof cpu.prof

# Generate flame graph
go tool pprof -raw cpu.prof | go-flamegraph > flamegraph.svg
```

#### Memory Profiling
```bash
# Heap profiling
go tool pprof http://localhost:6060/debug/pprof/heap

# Analyze memory profile
go tool pprof mem.prof

# Show allocations
go tool pprof -alloc_objects mem.prof
```

#### Block Profiling
```bash
# Block profiling
go tool pprof http://localhost:6060/debug/pprof/block

# Analyze block profile
go tool pprof block.prof
```

#### Trace Profiling
```bash
# Execution tracing
go tool trace http://localhost:6060/debug/pprof/trace

# Analyze trace
go tool trace trace.out
```

### Benchmarking

#### Writing Benchmarks
```go
func BenchmarkFunction(b *testing.B) {
    for i := 0; i < b.N; i++ {
        functionToBenchmark()
    }
}

func BenchmarkParallel(b *testing.B) {
    b.RunParallel(func(pb *testing.PB) {
        for pb.Next() {
            functionToBenchmark()
        }
    })
}
```

#### Running Benchmarks
```bash
# Run benchmarks
go test -bench=.

# Run specific benchmark
go test -bench=BenchmarkFunction

# Run benchmarks with memory profiling
go test -bench=. -benchmem

# Run benchmarks with CPU profiling
go test -bench=. -cpuprofile=cpu.prof

# Generate benchmark report
go test -bench=. -benchmem -run=^$ > benchmark.txt
```

### Performance Analysis

#### Memory Analysis
```go
// Get memory stats
var m runtime.MemStats
runtime.ReadMemStats(&m)

fmt.Printf("Alloc = %d MiB", m.Alloc/1024/1024)
fmt.Printf("TotalAlloc = %d MiB", m.TotalAlloc/1024/1024)
fmt.Printf("Sys = %d MiB", m.Sys/1024/1024)
fmt.Printf("NumGC = %v", m.NumGC)
```

#### Goroutine Analysis
```go
// Get goroutine stack traces
stackBuf := make([]byte, 1024*1024)
stackSize := runtime.Stack(stackBuf, true)
fmt.Printf("Goroutine stack traces: %d bytes\n", stackSize)

// Get number of goroutines
numGoroutines := runtime.NumGoroutine()
fmt.Printf("Number of goroutines: %d\n", numGoroutines)
```

#### GC Analysis
```go
// Force garbage collection
runtime.GC()

// Set GC percentage
debug.SetGCPercent(100)

// Get GC stats
var m runtime.MemStats
runtime.ReadMemStats(&m)
fmt.Printf("GC cycles: %d\n", m.NumGC)
fmt.Printf("GC pause total: %v\n", time.Duration(m.PauseTotalNs))
```

## Performance Best Practices

### General Guidelines

1. **Profile First**: Always profile before optimizing
2. **Measure Everything**: Use benchmarks to measure improvements
3. **Focus on Hot Paths**: Optimize code that runs frequently
4. **Consider Trade-offs**: Balance performance with readability
5. **Test Realistic Workloads**: Profile with realistic data and loads
6. **Monitor in Production**: Continuously monitor performance metrics

### Memory Management

1. **Pre-allocate**: Use make() with capacity for slices and maps
2. **Reuse Objects**: Use object pools for frequently allocated objects
3. **Avoid Allocations**: Minimize allocations in hot paths
4. **Use Value Types**: Use structs instead of pointers for small objects
5. **Profile Memory**: Use heap profiles to identify allocation hotspots

### Concurrency

1. **Use Worker Pools**: Limit number of concurrent goroutines
2. **Buffer Channels**: Use buffered channels to reduce blocking
3. **Avoid Mutex Contention**: Use atomic operations when possible
4. **Profile Goroutines**: Use goroutine profiles to detect leaks
5. **Use Context**: Use context for cancellation and timeouts

### I/O Operations

1. **Batch Operations**: Group multiple operations together
2. **Use Buffers**: Use appropriate buffer sizes for I/O
3. **Reuse Connections**: Use connection pooling
4. **Compress Data**: Use compression for large data transfers
5. **Async I/O**: Use goroutines for concurrent I/O operations

## Performance Monitoring

### Key Metrics

1. **Response Time**: P50, P95, P99 percentiles
2. **Throughput**: Requests per second
3. **Error Rate**: Percentage of failed requests
4. **Memory Usage**: Heap size, allocations
5. **CPU Usage**: Percentage of CPU utilized
6. **Goroutine Count**: Number of active goroutines
7. **GC Pause Time**: Garbage collection pause duration

### Monitoring Tools

#### Prometheus Metrics
```go
import "github.com/prometheus/client_golang/prometheus"

var (
    requestDuration = prometheus.NewHistogramVec(
        prometheus.HistogramOpts{
            Name: "http_request_duration_seconds",
            Help: "HTTP request duration in seconds",
        },
        []string{"method", "path"},
    )
    
    requestCount = prometheus.NewCounterVec(
        prometheus.CounterOpts{
            Name: "http_requests_total",
            Help: "Total number of HTTP requests",
        },
        []string{"method", "path", "status"},
    )
)

func init() {
    prometheus.MustRegister(requestDuration)
    prometheus.MustRegister(requestCount)
}
```

#### Custom Metrics
```go
type PerformanceMetrics struct {
    requestCount    int64
    errorCount      int64
    responseTime    time.Duration
    memoryUsage     int64
    goroutineCount  int
}

func (m *PerformanceMetrics) RecordRequest(duration time.Duration, err error) {
    atomic.AddInt64(&m.requestCount, 1)
    atomic.AddInt64((*int64)(&m.responseTime), int64(duration))
    
    if err != nil {
        atomic.AddInt64(&m.errorCount, 1)
    }
}
```

## Optimization Checklist

### Before Optimizing

- [ ] Profile the application
- [ ] Identify bottlenecks
- [ ] Set performance goals
- [ ] Establish baseline metrics
- [ ] Create benchmarks

### During Optimization

- [ ] Make one change at a time
- [ ] Measure impact of each change
- [ ] Keep code readable
- [ ] Add comments for optimizations
- [ ] Test thoroughly

### After Optimization

- [ ] Verify performance improvements
- [ ] Check for regressions
- [ ] Update documentation
- [ ] Monitor in production
- [ ] Share results with team

## Common Performance Issues

### Memory Issues

1. **Memory Leaks**: Goroutines holding references
2. **Excessive Allocations**: Too many small allocations
3. **Large Objects**: Unnecessarily large data structures
4. **Garbage Collection**: Frequent GC cycles
5. **Memory Fragmentation**: Poor memory layout

### CPU Issues

1. **Inefficient Algorithms**: O(n²) instead of O(n log n)
2. **Excessive Function Calls**: Function call overhead
3. **Poor Cache Locality**: Random memory access patterns
4. **Branch Misprediction**: Unpredictable conditional branches
5. **Lock Contention**: Too much synchronization

### I/O Issues

1. **Blocking Operations**: Synchronous I/O in hot paths
2. **Small Buffer Sizes**: Inefficient buffer usage
3. **Excessive Network Calls**: Too many round trips
4. **Poor Connection Reuse**: Creating new connections
5. **Inefficient Serialization**: Slow data marshaling

## Advanced Optimization Techniques

### Compiler Optimizations

```go
// Use build tags for optimization
// +build !noopt

// Inline hints
//go:noinline
func noinlineFunction() {
    // This function won't be inlined
}

// Use escape analysis
func escapeAnalysis() {
    // This will escape to heap
    data := make([]byte, 1024*1024)
    _ = data
}
```

### Assembly Optimization

```go
// Use assembly for critical sections
func fastFunction() int

//go:noescape
func fastFunction() int {
    // Assembly implementation
    return 42
}
```

### CGO Optimization

```go
// Use C for performance-critical code
/*
#include <math.h>

int fastCalc(int x) {
    return (int)sqrt((double)x);
}
*/
import "C"

func fastCalc(x int) int {
    return int(C.fastCalc(C.int(x)))
}
```

## Resources

### Documentation

- [Go pprof documentation](https://pkg.go.dev/runtime/pprof)
- [Go trace documentation](https://pkg.go/cmd/trace)
- [Go testing/benchmark](https://pkg.go/go/testing#hdr-Benchmarking)
- [Go runtime documentation](https://pkg.go/go/runtime)

### Tools

- [pprof](https://github.com/google/pprof) - CPU and memory profiling
- [go-torch](https://github.com/uber/go-torch) - Flame graph generation
- [Delve](https://github.com/go-delve/delve) - Go debugger
- [perf](https://perf.wiki.kernel.org/) - Linux profiling tool

### Books and Articles

- "The Go Programming Language" - Alan Donovan & Brian Kernighan
- "High Performance Go" - Various authors
- "Go Performance Tuning" - Dave Cheney
- "Profiling Go Programs" - Julia Evans

### Online Resources

- [Go Performance Blog](https://go.dev/blog/performance)
- [Go Performance Patterns](https://github.com/dgryski/go-perfbook)
- [Go Profiling Guide](https://go.dev/doc/diagnostics/profiling)
- [Go Benchmarking Guide](https://go.dev/doc/testing/benchmarks)

## Troubleshooting

### Common Profiling Issues

1. **No Profile Data**: Ensure code runs long enough
2. **Missing Symbols**: Use -gcflags="-l" for line numbers
3. **Large Profile Files**: Use sampling or filtering
4. **Slow Profiling**: Reduce profiling frequency
5. **Memory Issues**: Check for memory leaks

### Performance Regression

1. **Compare Profiles**: Use differential profiling
2. **Check Dependencies**: Look for dependency updates
3. **Review Code Changes**: Identify performance-impacting changes
4. **Monitor Metrics**: Use performance monitoring
5. **Rollback Changes**: Revert problematic optimizations

This comprehensive guide provides everything needed to optimize Go applications for maximum performance while maintaining code quality and reliability.
