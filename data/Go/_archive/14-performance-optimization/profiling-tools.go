package main

import (
	"fmt"
	"os"
	"runtime"
	"runtime/pprof"
	"runtime/trace"
	"strings"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Go Profiling Tools ===")

	// CPU profiling
	fmt.Println("\n--- CPU Profiling ---")
	cpuProfilingDemo()

	// Memory profiling
	fmt.Println("\n--- Memory Profiling ---")
	memoryProfilingDemo()

	// Block profiling
	fmt.Println("\n--- Block Profiling ---")
	blockProfilingDemo()

	// Trace profiling
	fmt.Println("\n--- Trace Profiling ---")
	traceProfilingDemo()

	// Goroutine profiling
	fmt.Println("\n--- Goroutine Profiling ---")
	goroutineProfilingDemo()

	// Heap profiling
	fmt.Println("\n--- Heap Profiling ---")
	heapProfilingDemo()

	// Mutex profiling
	fmt.Println("\n--- Mutex Profiling ---")
	mutexProfilingDemo()

	// Custom profiling
	fmt.Println("\n--- Custom Profiling ---")
	customProfilingDemo()

	// Profiling best practices
	fmt.Println("\n--- Profiling Best Practices ---")
	profilingBestPractices()
}

// CPU profiling demo
func cpuProfilingDemo() {
	fmt.Println("CPU Profiling Setup:")

	cpuProfile, err := os.Create("cpu.prof")
	if err != nil {
		fmt.Printf("Error creating CPU profile: %v\n", err)
		return
	}
	defer cpuProfile.Close()
	defer os.Remove("cpu.prof")

	if err := pprof.StartCPUProfile(cpuProfile); err != nil {
		fmt.Printf("Error starting CPU profile: %v\n", err)
		return
	}
	defer pprof.StopCPUProfile()

	fmt.Println("CPU profiling started...")
	fmt.Println("Running CPU-intensive operations...")

	cpuIntensiveWork()

	fmt.Println("CPU profiling completed")
}

// Memory profiling demo
func memoryProfilingDemo() {
	fmt.Println("Memory Profiling Setup:")

	memProfile, err := os.Create("mem.prof")
	if err != nil {
		fmt.Printf("Error creating memory profile: %v\n", err)
		return
	}
	defer memProfile.Close()
	defer os.Remove("mem.prof")

	fmt.Println("Memory profiling setup...")
	fmt.Println("Running memory-intensive operations...")

	memoryIntensiveWork()

	runtime.GC()
	if err := pprof.WriteHeapProfile(memProfile); err != nil {
		fmt.Printf("Error writing memory profile: %v\n", err)
		return
	}

	fmt.Println("Memory profiling completed")
}

// Block profiling demo
func blockProfilingDemo() {
	fmt.Println("Block Profiling Setup:")

	runtime.SetBlockProfileRate(1)
	defer runtime.SetBlockProfileRate(0)

	blockProfile, err := os.Create("block.prof")
	if err != nil {
		fmt.Printf("Error creating block profile: %v\n", err)
		return
	}
	defer blockProfile.Close()
	defer os.Remove("block.prof")

	fmt.Println("Block profiling setup...")
	fmt.Println("Running blocking operations...")

	blockingWork()

	if p := pprof.Lookup("block"); p != nil {
		_ = p.WriteTo(blockProfile, 0)
	}

	fmt.Println("Block profiling completed")
}

// Trace profiling demo
func traceProfilingDemo() {
	fmt.Println("Trace Profiling Setup:")

	traceFile, err := os.Create("trace.out")
	if err != nil {
		fmt.Printf("Error creating trace file: %v\n", err)
		return
	}
	defer traceFile.Close()
	defer os.Remove("trace.out")

	fmt.Println("Trace profiling started...")
	fmt.Println("Running traced operations...")

	if err := trace.Start(traceFile); err != nil {
		fmt.Printf("Error starting trace: %v\n", err)
		return
	}
	defer trace.Stop()

	tracedWork()

	fmt.Println("Trace profiling completed")
}

// Goroutine profiling demo
func goroutineProfilingDemo() {
	fmt.Println("Goroutine Profiling Setup:")
	fmt.Println("Running goroutine-intensive operations...")

	goroutineIntensiveWork()

	stackBuf := make([]byte, 1024*1024)
	stackSize := runtime.Stack(stackBuf, true)

	fmt.Printf("Goroutine stack traces captured (%d bytes)\n", stackSize)
	fmt.Println("Goroutine profiling completed")
}

// Heap profiling demo
func heapProfilingDemo() {
	fmt.Println("Heap Profiling Setup:")

	heapProfile, err := os.Create("heap.prof")
	if err != nil {
		fmt.Printf("Error creating heap profile: %v\n", err)
		return
	}
	defer heapProfile.Close()
	defer os.Remove("heap.prof")

	fmt.Println("Running heap-intensive operations...")
	heapIntensiveWork()

	runtime.GC()
	if err := pprof.WriteHeapProfile(heapProfile); err != nil {
		fmt.Printf("Error writing heap profile: %v\n", err)
		return
	}

	fmt.Println("Heap profiling completed")
}

// Mutex profiling demo
func mutexProfilingDemo() {
	fmt.Println("Mutex Profiling Setup:")

	runtime.SetMutexProfileFraction(1)
	defer runtime.SetMutexProfileFraction(0)

	mutexProfile, err := os.Create("mutex.prof")
	if err != nil {
		fmt.Printf("Error creating mutex profile: %v\n", err)
		return
	}
	defer mutexProfile.Close()
	defer os.Remove("mutex.prof")

	fmt.Println("Running mutex-intensive operations...")
	mutexIntensiveWork()

	if p := pprof.Lookup("mutex"); p != nil {
		_ = p.WriteTo(mutexProfile, 0)
	}

	fmt.Println("Mutex profiling completed")
}

// Custom profiling demo
type CustomProfiler struct {
	startTime time.Time
	counters  map[string]int64
	mu        sync.Mutex
}

func NewCustomProfiler() *CustomProfiler {
	return &CustomProfiler{
		counters: make(map[string]int64),
	}
}

func (prof *CustomProfiler) Start() {
	prof.startTime = time.Now()
}

func (prof *CustomProfiler) Increment(name string) {
	prof.mu.Lock()
	defer prof.mu.Unlock()
	prof.counters[name]++
}

func (prof *CustomProfiler) Report() {
	duration := time.Since(prof.startTime)
	fmt.Printf("Custom profiling report (duration: %v)\n", duration)
	for name, count := range prof.counters {
		fmt.Printf("  %s: %d\n", name, count)
	}
}

func customProfilingDemo() {
	fmt.Println("Custom Profiling Setup:")

	profiler := NewCustomProfiler()
	profiler.Start()

	customWork(profiler)
	profiler.Report()
}

// CPU-intensive work
func cpuIntensiveWork() {
	calculatePrimes(500)
	stringOperations(500)
	arrayOperations(500)
}

// Memory-intensive work
func memoryIntensiveWork() {
	allocateSlices(500)
	createMaps(500)
	allocateStrings(500)
}

// Blocking work
func blockingWork() {
	for i := 0; i < 5; i++ {
		time.Sleep(1 * time.Millisecond)
	}
	channelOperations()
	mutexOperations()
}

// Traced work
func tracedWork() {
	functionCalls()
	goroutineCreation()
	channelCreation()
}

// Goroutine-intensive work
func goroutineIntensiveWork() {
	var wg sync.WaitGroup
	for i := 0; i < 20; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			goroutineWork(id)
		}(i)
	}
	wg.Wait()
}

// Heap-intensive work
func heapIntensiveWork() {
	allocateObjects(500)
	createLargeStrings(50)
	allocateStructs(500)
}

// Mutex-intensive work
func mutexIntensiveWork() {
	var mu sync.Mutex
	var wg sync.WaitGroup
	for i := 0; i < 20; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			mutexWork(&mu, id)
		}(i)
	}
	wg.Wait()
}

// Custom work
func customWork(profiler *CustomProfiler) {
	for i := 0; i < 1000; i++ {
		profiler.Increment("iterations")
		if i%2 == 0 {
			profiler.Increment("even_iterations")
		} else {
			profiler.Increment("odd_iterations")
		}
	}
}

func calculatePrimes(n int) {
	var primes []int
	for num := 2; num <= n; num++ {
		isPrime := true
		for i := 2; i*i <= num; i++ {
			if num%i == 0 {
				isPrime = false
				break
			}
		}
		if isPrime {
			primes = append(primes, num)
		}
	}
	_ = primes
}

func stringOperations(n int) {
	var result string
	for i := 0; i < n; i++ {
		result += fmt.Sprintf("item-%d", i)
	}
	_ = result
}

func arrayOperations(n int) {
	data := make([]int, n)
	for i := range data {
		data[i] = i * i
	}
	for i := range data {
		data[i] = data[i] + 1
	}
	_ = data
}

func allocateSlices(n int) {
	for i := 0; i < n; i++ {
		_ = make([]byte, 1024)
	}
}

func createMaps(n int) {
	for i := 0; i < n; i++ {
		m := make(map[int]string)
		m[i] = fmt.Sprintf("value-%d", i)
		_ = m
	}
}

func allocateStrings(n int) {
	for i := 0; i < n; i++ {
		_ = fmt.Sprintf("string-%d", i)
	}
}

func channelOperations() {
	ch := make(chan int, 10)
	go func() {
		for i := 0; i < 10; i++ {
			ch <- i
		}
		close(ch)
	}()
	for val := range ch {
		_ = val
	}
}

func mutexOperations() {
	var mu sync.Mutex
	var counter int
	for i := 0; i < 10; i++ {
		mu.Lock()
		counter++
		mu.Unlock()
	}
	_ = counter
}

func functionCalls() {
	fibonacci(20)
	nestedCalls(5)
}

func goroutineCreation() {
	var wg sync.WaitGroup
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			time.Sleep(1 * time.Millisecond)
		}(i)
	}
	wg.Wait()
}

func channelCreation() {
	channels := make([]chan int, 5)
	for i := range channels {
		channels[i] = make(chan int, 1)
	}
	for _, ch := range channels {
		ch <- 1
	}
	for _, ch := range channels {
		<-ch
	}
}

func goroutineWork(id int) {
	time.Sleep(1 * time.Millisecond)
}

func allocateObjects(n int) {
	for i := 0; i < n; i++ {
		_ = &struct {
			id   int
			data []byte
		}{
			id:   i,
			data: make([]byte, 100),
		}
	}
}

func createLargeStrings(n int) {
	for i := 0; i < n; i++ {
		_ = strings.Repeat("x", 1000)
	}
}

func allocateStructs(n int) {
	for i := 0; i < n; i++ {
		_ = struct {
			field1 int
			field2 string
			field3 []byte
			field4 float64
		}{
			field1: i,
			field2: fmt.Sprintf("field-%d", i),
			field3: make([]byte, 100),
			field4: float64(i),
		}
	}
}

func mutexWork(mu *sync.Mutex, id int) {
	mu.Lock()
	defer mu.Unlock()
	time.Sleep(100 * time.Microsecond)
}

func fibonacci(n int) int {
	if n <= 1 {
		return n
	}
	return fibonacci(n-1) + fibonacci(n-2)
}

func nestedCalls(depth int) int {
	if depth <= 0 {
		return 0
	}
	return 1 + nestedCalls(depth-1)
}

func profilingBestPractices() {
	fmt.Println("Profiling Best Practices:")
	practices := []string{
		"1. Profile realistic workloads",
		"2. Use representative data sizes",
		"3. Profile in production-like environment",
		"4. Collect multiple profile types",
		"5. Focus on top 10 functions",
	}
	for _, practice := range practices {
		fmt.Printf("  %s\n", practice)
	}
}
