package main

import (
	"fmt"
	"os"
	"runtime"
	"runtime/pprof"
	"runtime/trace"
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
	
	// Create CPU profile file
	cpuProfile, err := os.Create("cpu.prof")
	if err != nil {
		fmt.Printf("Error creating CPU profile: %v\n", err)
		return
	}
	defer cpuProfile.Close()
	
	// Start CPU profiling
	if err := pprof.StartCPUProfile(cpuProfile); err != nil {
		fmt.Printf("Error starting CPU profile: %v\n", err)
		return
	}
	defer pprof.StopCPUProfile()
	
	fmt.Println("CPU profiling started...")
	fmt.Println("Running CPU-intensive operations...")
	
	// CPU-intensive operations
	cpuIntensiveWork()
	
	fmt.Println("CPU profiling completed")
	fmt.Println("Profile saved to: cpu.prof")
	fmt.Println("Analyze with: go tool pprof cpu.prof")
}

// Memory profiling demo
func memoryProfilingDemo() {
	fmt.Println("Memory Profiling Setup:")
	
	// Create memory profile file
	memProfile, err := os.Create("mem.prof")
	if err != nil {
		fmt.Printf("Error creating memory profile: %v\n", err)
		return
	}
	defer memProfile.Close()
	
	fmt.Println("Memory profiling setup...")
	fmt.Println("Running memory-intensive operations...")
	
	// Memory-intensive operations
	memoryIntensiveWork()
	
	// Write memory profile
	runtime.GC() // Force garbage collection
	if err := pprof.WriteHeapProfile(memProfile); err != nil {
		fmt.Printf("Error writing memory profile: %v\n", err)
		return
	}
	
	fmt.Println("Memory profiling completed")
	fmt.Println("Profile saved to: mem.prof")
	fmt.Println("Analyze with: go tool pprof mem.prof")
}

// Block profiling demo
func blockProfilingDemo() {
	fmt.Println("Block Profiling Setup:")
	
	// Enable block profiling
	runtime.SetBlockProfileRate(1)
	defer runtime.SetBlockProfileRate(0)
	
	// Create block profile file
	blockProfile, err := os.Create("block.prof")
	if err != nil {
		fmt.Printf("Error creating block profile: %v\n", err)
		return
	}
	defer blockProfile.Close()
	
	fmt.Println("Block profiling setup...")
	fmt.Println("Running blocking operations...")
	
	// Blocking operations
	blockingWork()
	
	// Write block profile
	if err := pprof.Lookup("block").WriteTo(blockProfile, 0); err != nil {
		fmt.Printf("Error writing block profile: %v\n", err)
		return
	}
	
	fmt.Println("Block profiling completed")
	fmt.Println("Profile saved to: block.prof")
	fmt.Println("Analyze with: go tool pprof block.prof")
}

// Trace profiling demo
func traceProfilingDemo() {
	fmt.Println("Trace Profiling Setup:")
	
	// Create trace file
	traceFile, err := os.Create("trace.out")
	if err != nil {
		fmt.Printf("Error creating trace file: %v\n", err)
		return
	}
	defer traceFile.Close()
	
	fmt.Println("Trace profiling started...")
	fmt.Println("Running traced operations...")
	
	// Start trace
	if err := trace.Start(traceFile); err != nil {
		fmt.Printf("Error starting trace: %v\n", err)
		return
	}
	defer trace.Stop()
	
	// Traced operations
	tracedWork()
	
	fmt.Println("Trace profiling completed")
	fmt.Println("Trace saved to: trace.out")
	fmt.Println("Analyze with: go tool trace trace.out")
}

// Goroutine profiling demo
func goroutineProfilingDemo() {
	fmt.Println("Goroutine Profiling Setup:")
	
	fmt.Println("Goroutine profiling setup...")
	fmt.Println("Running goroutine-intensive operations...")
	
	// Goroutine-intensive operations
	goroutineIntensiveWork()
	
	// Get goroutine stack traces
	stackBuf := make([]byte, 1024*1024)
	stackSize := runtime.Stack(stackBuf, true)
	
	fmt.Printf("Goroutine stack traces captured (%d bytes)\n", stackSize)
	fmt.Println("Goroutine profiling completed")
}

// Heap profiling demo
func heapProfilingDemo() {
	fmt.Println("Heap Profiling Setup:")
	
	// Create heap profile file
	heapProfile, err := os.Create("heap.prof")
	if err != nil {
		fmt.Printf("Error creating heap profile: %v\n", err)
		return
	}
	defer heapProfile.Close()
	
	fmt.Println("Heap profiling setup...")
	fmt.Println("Running heap-intensive operations...")
	
	// Heap-intensive operations
	heapIntensiveWork()
	
	// Write heap profile
	runtime.GC()
	if err := pprof.WriteHeapProfile(heapProfile); err != nil {
		fmt.Printf("Error writing heap profile: %v\n", err)
		return
	}
	
	fmt.Println("Heap profiling completed")
	fmt.Println("Profile saved to: heap.prof")
	fmt.Println("Analyze with: go tool pprof heap.prof")
}

// Mutex profiling demo
func mutexProfilingDemo() {
	fmt.Println("Mutex Profiling Setup:")
	
	// Enable mutex profiling
	runtime.SetMutexProfileFraction(1)
	defer runtime.SetMutexProfileFraction(0)
	
	// Create mutex profile file
	mutexProfile, err := os.Create("mutex.prof")
	if err != nil {
		fmt.Printf("Error creating mutex profile: %v\n", err)
		return
	}
	defer mutexProfile.Close()
	
	fmt.Println("Mutex profiling setup...")
	fmt.Println("Running mutex-intensive operations...")
	
	// Mutex-intensive operations
	mutexIntensiveWork()
	
	// Write mutex profile
	if err := pprof.Lookup("mutex").WriteTo(mutexProfile, 0); err != nil {
		fmt.Printf("Error writing mutex profile: %v\n", err)
		return
	}
	
	fmt.Println("Mutex profiling completed")
	fmt.Println("Profile saved to: mutex.prof")
	fmt.Println("Analyze with: go tool pprof mutex.prof")
}

// Custom profiling demo
func customProfilingDemo() {
	fmt.Println("Custom Profiling Setup:")
	
	// Custom profiler
	type CustomProfiler struct {
		startTime time.Time
		counters  map[string]int64
	}
	
	NewCustomProfiler := func() *CustomProfiler {
		return &CustomProfiler{
			counters: make(map[string]int64),
		}
	}
	
	(prof *CustomProfiler) Start() {
		prof.startTime = time.Now()
	}
	
	(prof *CustomProfiler) Increment(name string) {
		prof.counters[name]++
	}
	
	(prof *CustomProfiler) Report() {
		duration := time.Since(prof.startTime)
		fmt.Printf("Custom profiling report (duration: %v)\n", duration)
		for name, count := range prof.counters {
			fmt.Printf("  %s: %d\n", name, count)
		}
	}
	
	// Use custom profiler
	profiler := NewCustomProfiler()
	profiler.Start()
	
	// Custom operations
	customWork(profiler)
	
	profiler.Report()
}

// CPU-intensive work
func cpuIntensiveWork() {
	// Mathematical calculations
	calculatePrimes(1000)
	
	// String operations
	stringOperations(1000)
	
	// Array operations
	arrayOperations(1000)
}

// Memory-intensive work
func memoryIntensiveWork() {
	// Allocate large slices
	allocateSlices(1000)
	
	// Create maps
	createMaps(1000)
	
	// String allocation
	allocateStrings(1000)
}

// Blocking work
func blockingWork() {
	// Simulate blocking operations
	for i := 0; i < 10; i++ {
		time.Sleep(10 * time.Millisecond)
	}
	
	// Channel operations
	channelOperations()
	
	// Mutex operations
	mutexOperations()
}

// Traced work
func tracedWork() {
	// Function calls
	functionCalls()
	
	// Goroutine creation
	goroutineCreation()
	
	// Channel operations
	channelCreation()
}

// Goroutine-intensive work
func goroutineIntensiveWork() {
	var wg sync.WaitGroup
	
	// Create many goroutines
	for i := 0; i < 100; i++ {
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
	// Allocate many objects
	allocateObjects(1000)
	
	// Create large strings
	createLargeStrings(100)
	
	// Allocate structs
	allocateStructs(1000)
}

// Mutex-intensive work
func mutexIntensiveWork() {
	var mu sync.Mutex
	var wg sync.WaitGroup
	
	// Create many goroutines with mutex contention
	for i := 0; i < 100; i++ {
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
		
		// Simulate work
		time.Sleep(1 * time.Microsecond)
	}
}

// Helper functions for profiling work

func calculatePrimes(n int) {
	primes := []int{}
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
	
	// Producer
	go func() {
		for i := 0; i < 10; i++ {
			ch <- i
		}
		close(ch)
	}()
	
	// Consumer
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
	// Recursive function calls
	fibonacci(30)
	
	// Nested function calls
	nestedCalls(10)
}

func goroutineCreation() {
	var wg sync.WaitGroup
	
	for i := 0; i < 10; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			time.Sleep(10 * time.Millisecond)
		}(i)
	}
	
	wg.Wait()
}

func channelCreation() {
	channels := make([]chan int, 10)
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
	// Simulate work
	time.Sleep(10 * time.Millisecond)
	
	// Create more goroutines
	var wg sync.WaitGroup
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			time.Sleep(5 * time.Millisecond)
		}()
	}
	wg.Wait()
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
	
	// Simulate work while holding lock
	time.Sleep(1 * time.Millisecond)
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

// Profiling best practices
func profilingBestPractices() {
	fmt.Println("Profiling Best Practices:")
	
	practices := []string{
		"1. Profile realistic workloads",
		"2. Use representative data sizes",
		"3. Profile in production-like environment",
		"4. Collect multiple profile types",
		"5. Use sampling for long-running applications",
		"6. Profile with and without optimizations",
		"7. Document profiling conditions",
		"8. Use baseline profiles for comparison",
		"9. Profile hot paths specifically",
		"10. Consider profiling overhead",
	}
	
	for _, practice := range practices {
		fmt.Printf("  %s\n", practice)
	}
	
	fmt.Println("\nCommon Profiling Commands:")
	commands := []string{
		"go tool pprof cpu.prof",
		"go tool pprof mem.prof",
		"go tool pprof block.prof",
		"go tool pprof mutex.prof",
		"go tool trace trace.out",
		"go test -cpuprofile cpu.prof -memprofile mem.prof",
		"go test -run BenchmarkXxx -benchmem",
		"go build -gcflags=\"-m\"",
		"go build -gcflags=\"-m=2\"",
		"go build -ldflags=\"-w -s\"",
	}
	
	for _, cmd := range commands {
		fmt.Printf("  %s\n", cmd)
	}
	
	fmt.Println("\nProfiling Tips:")
	tips := []string{
		"Use -http flag for web interface",
		"Focus on top 10 functions",
		"Look for unexpected allocations",
		"Check for unnecessary function calls",
		"Analyze call stacks",
		"Profile with different inputs",
		"Consider memory fragmentation",
		"Check for goroutine leaks",
		"Profile hot paths repeatedly",
		"Use flame graphs for visualization",
	}
	
	for _, tip := range tips {
		fmt.Printf("  %s\n", tip)
	}
}

// Additional profiling utilities

// Memory leak detection
func memoryLeakDetection() {
	fmt.Println("Memory Leak Detection:")
	
	// Track allocations over time
	type AllocationTracker struct {
		allocations int64
		deallocations int64
	}
	
	tracker := &AllocationTracker{}
	
	// Simulate memory operations
	for i := 0; i < 1000; i++ {
		// Allocate
		data := make([]byte, 1024)
		tracker.allocations++
		
		// Sometimes forget to deallocate (leak)
		if i%10 != 0 {
			tracker.deallocations++
			_ = data // Use data
		}
	}
	
	leakCount := tracker.allocations - tracker.deallocations
	fmt.Printf("Potential leaks: %d\n", leakCount)
}

// Performance regression testing
func performanceRegressionTest() {
	fmt.Println("Performance Regression Testing:")
	
	// Benchmark function
	benchmark := func(name string, fn func()) time.Duration {
		start := time.Now()
		fn()
		return time.Since(start)
	}
	
	// Test function
	testFunction := func() {
		// Simulate work
		for i := 0; i < 100000; i++ {
			_ = i * i
		}
	}
	
	// Run multiple iterations
	iterations := 10
	var totalDuration time.Duration
	
	for i := 0; i < iterations; i++ {
		duration := benchmark("test", testFunction)
		totalDuration += duration
	}
	
	avgDuration := totalDuration / time.Duration(iterations)
	fmt.Printf("Average duration: %v\n", avgDuration)
	
	// Check for regression (simplified)
	if avgDuration > 10*time.Millisecond {
		fmt.Println("Potential performance regression detected!")
	}
}

// Hot path identification
func hotPathIdentification() {
	fmt.Println("Hot Path Identification:")
	
	// Simulate hot path
	hotPath := func() {
		for i := 0; i < 1000000; i++ {
			// Hot operation
			_ = i * i
		}
	}
	
	// Cold path
	coldPath := func() {
		for i := 0; i < 100; i++ {
			// Cold operation
			_ = i * i
		}
	}
	
	// Measure hot path
	start := time.Now()
	hotPath()
	hotDuration := time.Since(start)
	
	// Measure cold path
	start = time.Now()
	coldPath()
	coldDuration := time.Since(start)
	
	fmt.Printf("Hot path: %v\n", hotDuration)
	fmt.Printf("Cold path: %v\n", coldDuration)
	fmt.Printf("Hot path is %.2fx slower\n", float64(hotDuration)/float64(coldDuration))
}

// Resource usage monitoring
func resourceUsageMonitoring() {
	fmt.Println("Resource Usage Monitoring:")
	
	// Get initial stats
	var initialMemStats runtime.MemStats
	runtime.ReadMemStats(&initialMemStats)
	
	// Run some work
	work := func() {
		data := make([]byte, 1024*1024) // 1MB
		for i := range data {
			data[i] = byte(i)
		}
		_ = data
	}
	
	work()
	
	// Get final stats
	var finalMemStats runtime.MemStats
	runtime.ReadMemStats(&finalMemStats)
	
	// Calculate differences
	allocDiff := finalMemStats.Alloc - initialMemStats.Alloc
	totalAllocDiff := finalMemStats.TotalAlloc - initialMemStats.TotalAlloc
	
	fmt.Printf("Memory allocated: %d bytes\n", allocDiff)
	fmt.Printf("Total allocations: %d bytes\n", totalAllocDiff)
	fmt.Printf("GC cycles: %d\n", finalMemStats.NumGC-initialMemStats.NumGC)
}

// Demonstrate all profiling utilities
func demonstrateAllProfilingUtilities() {
	fmt.Println("\n--- Additional Profiling Utilities ---")
	
	memoryLeakDetection()
	performanceRegressionTest()
	hotPathIdentification()
	resourceUsageMonitoring()
	
	fmt.Println("\n--- Advanced Profiling Techniques ---")
	advancedTechniques := []string{
		"1. Continuous profiling",
		"2. Differential profiling",
		"3. Flame graph generation",
		"4. Call graph analysis",
		"5. Memory allocation tracing",
		"6. Goroutine leak detection",
		"7. Lock contention analysis",
		"8. I/O profiling",
		"9. Network latency profiling",
		"10. Database query profiling",
	}
	
	for _, technique := range advancedTechniques {
		fmt.Printf("  %s\n", technique)
	}
}

// Integration with CI/CD
func ciCDIntegration() {
	fmt.Println("CI/CD Integration for Profiling:")
	
	integrationSteps := []string{
		"1. Add profiling to test suite",
		"2. Set performance benchmarks",
		"3. Profile on every PR",
		"4. Compare with baseline",
		"5. Alert on regressions",
		"6. Store profiles for analysis",
		"7. Generate performance reports",
		"8. Automate flame graph generation",
		"9. Integrate with monitoring",
		"10. Profile in staging environment",
	}
	
	for _, step := range integrationSteps {
		fmt.Printf("  %s\n", step)
	}
	
	fmt.Println("\nExample CI/CD Pipeline:")
	pipeline := `
# Example GitHub Actions workflow
name: Performance Profiling
on: [push, pull_request]

jobs:
  profile:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
    - uses: actions/setup-go@v2
      with:
        go-version: 1.21
    
    - name: Run benchmarks
      run: go test -bench=. -benchmem -cpuprofile=cpu.prof -memprofile=mem.prof ./...
    
    - name: Upload profiles
      uses: actions/upload-artifact@v2
      with:
        name: profiles
        path: "*.prof"
    
    - name: Generate flame graph
      run: |
        go tool pprof -raw cpu.prof | go-flamegraph > flamegraph.svg
        go tool pprof -raw mem.prof | go-flamegraph > mem-flamegraph.svg
    
    - name: Upload flame graphs
      uses: actions/upload-artifact@v2
      with:
        name: flamegraphs
        path: "*.svg"
`
	
	fmt.Println(pipeline)
}

// Performance monitoring in production
func productionMonitoring() {
	fmt.Println("Production Performance Monitoring:")
	
	monitoringStrategies := []string{
		"1. Continuous profiling",
		"2. Metrics collection",
		"3. Alerting on anomalies",
		"4. Distributed tracing",
		"5. Real user monitoring",
		"6. Synthetic monitoring",
		"7. Error rate tracking",
		"8. Latency monitoring",
		"9. Throughput monitoring",
		"10. Resource utilization tracking",
	}
	
	for _, strategy := range monitoringStrategies {
		fmt.Printf("  %s\n", strategy)
	}
	
	fmt.Println("\nKey Performance Indicators:")
	kpis := []string{
		"Response time (P50, P95, P99)",
		"Throughput (requests/second)",
		"Error rate",
		"Memory usage",
		"CPU usage",
		"Database query time",
		"Cache hit ratio",
		"Connection pool usage",
		"Queue depth",
		"Garbage collection pause time",
	}
	
	for _, kpi := range kpis {
		fmt.Printf("  - %s\n", kpi)
	}
}

// Demonstrate all advanced profiling concepts
func demonstrateAllAdvancedProfiling() {
	fmt.Println("\n--- Advanced Profiling Concepts ---")
	
	ciCDIntegration()
	productionMonitoring()
	
	fmt.Println("\n--- Profiling Tools Comparison ---")
	tools := []struct {
		name        string
		useCase     string
		overhead    string
		complexity  string
	}{
		{"pprof", "CPU, memory, block, mutex", "Low", "Medium"},
		{"trace", "Execution tracing", "Medium", "Low"},
		{"perf", "System-wide profiling", "Low", "High"},
		{"eBPF", "Kernel-level tracing", "Very Low", "Very High"},
		{"Delve", "Debugging and profiling", "Medium", "Medium"},
		{"go-torch", "Flame graphs", "Low", "Low"},
		{"go-tool-trace", "Execution traces", "Medium", "Medium"},
	}
	
	for _, tool := range tools {
		fmt.Printf("  %s: %s (Overhead: %s, Complexity: %s)\n", 
			tool.name, tool.useCase, tool.overhead, tool.complexity)
	}
}
