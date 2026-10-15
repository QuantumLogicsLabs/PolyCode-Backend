package main

import (
	"context"
	"fmt"
	"math/rand"
	"strings"
	"sync"
	"sync/atomic"
	"time"
)

func main() {
	fmt.Println("=== Performance Optimization in Go ===")

	// Profiling and benchmarking
	fmt.Println("\n--- Profiling and Benchmarking ---")
	profilingAndBenchmarking()

	// Memory optimization
	fmt.Println("\n--- Memory Optimization ---")
	memoryOptimization()

	// CPU optimization
	fmt.Println("\n--- CPU Optimization ---")
	cpuOptimization()

	// I/O optimization
	fmt.Println("\n--- I/O Optimization ---")
	ioOptimization()

	// Concurrency optimization
	fmt.Println("\n--- Concurrency Optimization ---")
	concurrencyOptimization()

	// Algorithm optimization
	fmt.Println("\n--- Algorithm Optimization ---")
	algorithmOptimization()

	// Data structure optimization
	fmt.Println("\n--- Data Structure Optimization ---")
	dataStructureOptimization()

	// Network optimization
	fmt.Println("\n--- Network Optimization ---")
	networkOptimization()

	// Database optimization
	fmt.Println("\n--- Database Optimization ---")
	databaseOptimization()
}

// Profiling and benchmarking
func profilingAndBenchmarking() {
	fmt.Println("Profiling and Benchmarking Examples:")

	fmt.Println("\n1. CPU Profiling:")
	cpuProfiling()

	fmt.Println("\n2. Memory Profiling:")
	memoryProfiling()

	fmt.Println("\n3. Benchmarking:")
	benchmarking()

	fmt.Println("\n4. Trace Profiling:")
	traceProfiling()
}

func cpuProfiling() {
	fmt.Println("CPU Profiling Commands:")
	fmt.Println("  go tool pprof http://localhost:6060/debug/pprof/profile")
	fmt.Println("  go tool pprof -text http://localhost:6060/debug/pprof/profile")
	fmt.Println("  go tool pprof -pdf http://localhost:6060/debug/pprof/profile")

	cpuIntensiveFunction := func() {
		for i := 0; i < 1000000; i++ {
			_ = i * i * i
		}
	}

	fmt.Println("Running CPU-intensive function...")
	start := time.Now()
	cpuIntensiveFunction()
	duration := time.Since(start)
	fmt.Printf("CPU-intensive function took: %v\n", duration)
}

func memoryProfiling() {
	fmt.Println("Memory Profiling Commands:")
	fmt.Println("  go tool pprof http://localhost:6060/debug/pprof/heap")
	fmt.Println("  go tool pprof -text http://localhost:6060/debug/pprof/heap")
	fmt.Println("  go tool pprof -alloc_objects http://localhost:6060/debug/pprof/heap")

	memoryIntensiveFunction := func() {
		data := make([][]byte, 1000)
		for i := range data {
			data[i] = make([]byte, 1000)
			for j := range data[i] {
				data[i][j] = byte(i + j)
			}
		}
	}

	fmt.Println("Running memory-intensive function...")
	start := time.Now()
	memoryIntensiveFunction()
	duration := time.Since(start)
	fmt.Printf("Memory-intensive function took: %v\n", duration)
}

func benchmarking() {
	fmt.Println("Benchmarking Examples:")

	benchmarkFunction := func(name string, fn func()) {
		start := time.Now()
		fn()
		duration := time.Since(start)
		fmt.Printf("%s took: %v\n", name, duration)
	}

	benchmarkFunction("String concatenation", func() {
		var result string
		for i := 0; i < 10000; i++ {
			result += "test"
		}
	})

	benchmarkFunction("String builder", func() {
		var builder strings.Builder
		for i := 0; i < 10000; i++ {
			builder.WriteString("test")
		}
		_ = builder.String()
	})

	benchmarkFunction("Map operations", func() {
		m := make(map[int]string)
		for i := 0; i < 10000; i++ {
			m[i] = fmt.Sprintf("value-%d", i)
		}
		for i := 0; i < 10000; i++ {
			_ = m[i]
		}
	})
}

func traceProfiling() {
	fmt.Println("Trace Profiling Commands:")
	fmt.Println("  go tool trace http://localhost:6060/debug/pprof/trace")
	fmt.Println("  go tool trace -text http://localhost:6060/debug/pprof/trace")
	fmt.Println("  go tool trace -pprof http://localhost:6060/debug/pprof/trace")

	traceFunction := func() {
		for i := 0; i < 5; i++ {
			time.Sleep(10 * time.Millisecond)
			fmt.Printf("Step %d completed\n", i)
		}
	}

	fmt.Println("Running trace function...")
	traceFunction()
}

// Memory optimization
func memoryOptimization() {
	fmt.Println("Memory Optimization Examples:")

	fmt.Println("\n1. Object Pooling:")
	objectPooling()

	fmt.Println("\n2. Memory Reuse:")
	memoryReuse()

	fmt.Println("\n3. Stack vs Heap Allocation:")
	stackVsHeap()

	fmt.Println("\n4. Slice Optimization:")
	sliceOptimization()

	fmt.Println("\n5. Map Optimization:")
	mapOptimization()

	fmt.Println("\n6. String Optimization:")
	stringOptimization()
}

func objectPooling() {
	type Buffer struct {
		data []byte
	}

	pool := make(chan *Buffer, 10)
	for i := 0; i < 10; i++ {
		pool <- &Buffer{data: make([]byte, 1024)}
	}

	getBuf := func() *Buffer {
		select {
		case buf := <-pool:
			return buf
		default:
			return &Buffer{data: make([]byte, 1024)}
		}
	}

	putBuf := func(buf *Buffer) {
		for i := range buf.data {
			buf.data[i] = 0
		}
		select {
		case pool <- buf:
		default:
		}
	}

	buf1 := getBuf()
	buf2 := getBuf()

	copy(buf1.data, "data1")
	copy(buf2.data, "data2")

	fmt.Printf("Buffer 1: %s\n", string(buf1.data[:5]))
	fmt.Printf("Buffer 2: %s\n", string(buf2.data[:5]))

	putBuf(buf1)
	putBuf(buf2)
}

func memoryReuse() {
	processData := func(data []byte) {
		_ = len(data)
	}

	badApproach := func() {
		for i := 0; i < 100; i++ {
			data := make([]byte, 1000)
			processData(data)
		}
	}

	goodApproach := func() {
		buffer := make([]byte, 1000)
		for i := 0; i < 100; i++ {
			for j := range buffer {
				buffer[j] = 0
			}
			processData(buffer)
		}
	}

	start := time.Now()
	badApproach()
	badDuration := time.Since(start)

	start = time.Now()
	goodApproach()
	goodDuration := time.Since(start)

	fmt.Printf("Bad approach: %v\n", badDuration)
	fmt.Printf("Good approach: %v\n", goodDuration)
}

func stackVsHeap() {
	stackAllocation := func() {
		var x int = 42
		var y int = 84
		_ = x + y
	}

	heapAllocation := func() {
		data := make([]int, 1000)
		for i := range data {
			data[i] = i
		}
		_ = data[0]
	}

	start := time.Now()
	for i := 0; i < 1000; i++ {
		stackAllocation()
	}
	stackDuration := time.Since(start)

	start = time.Now()
	for i := 0; i < 1000; i++ {
		heapAllocation()
	}
	heapDuration := time.Since(start)

	fmt.Printf("Stack allocation: %v\n", stackDuration)
	fmt.Printf("Heap allocation: %v\n", heapDuration)
}

func sliceOptimization() {
	preallocate := func() {
		good := make([]int, 0, 10000)
		for i := 0; i < 10000; i++ {
			good = append(good, i)
		}
		_ = len(good)
	}

	copySlice := func() {
		src := make([]int, 1000)
		for i := range src {
			src[i] = i
		}
		good := make([]int, len(src))
		copy(good, src)
		_ = good
	}

	start := time.Now()
	preallocate()
	preallocateDuration := time.Since(start)

	start = time.Now()
	copySlice()
	copyDuration := time.Since(start)

	fmt.Printf("Pre-allocation: %v\n", preallocateDuration)
	fmt.Printf("Slice copying: %v\n", copyDuration)
}

func mapOptimization() {
	preallocateMap := func() {
		good := make(map[int]string, 10000)
		for i := 0; i < 10000; i++ {
			good[i] = fmt.Sprintf("value-%d", i)
		}
		_ = len(good)
	}

	start := time.Now()
	preallocateMap()
	fmt.Printf("Map pre-allocation: %v\n", time.Since(start))
}

func stringOptimization() {
	stringBuilder := func() {
		var good strings.Builder
		for i := 0; i < 10000; i++ {
			good.WriteString(fmt.Sprintf("item-%d", i))
		}
		_ = good.Len()
	}

	start := time.Now()
	stringBuilder()
	fmt.Printf("String builder: %v\n", time.Since(start))
}

// CPU optimization
func cpuOptimization() {
	fmt.Println("CPU Optimization Examples:")

	fmt.Println("\n1. Algorithm Optimization:")
	algorithmOptimizationDemo()

	fmt.Println("\n2. Loop Optimization:")
	loopOptimization()

	fmt.Println("\n3. Parallel Processing:")
	parallelProcessing()

	fmt.Println("\n4. CPU Cache Optimization:")
	cpuCacheOptimization()

	fmt.Println("\n5. Branch Prediction Optimization:")
	branchPredictionOptimization()
}

func algorithmOptimizationDemo() {
	linearSearch := func(data []int, target int) int {
		for i, val := range data {
			if val == target {
				return i
			}
		}
		return -1
	}

	binarySearch := func(data []int, target int) int {
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

	data := make([]int, 10000)
	for i := range data {
		data[i] = i
	}
	target := 9999

	start := time.Now()
	linearSearch(data, target)
	linearDuration := time.Since(start)

	start = time.Now()
	binarySearch(data, target)
	binaryDuration := time.Since(start)

	fmt.Printf("Linear search: %v\n", linearDuration)
	fmt.Printf("Binary search: %v\n", binaryDuration)
}

func loopOptimization() {
	useRange := func() {
		good := make([]int, 10000)
		for i := range good {
			good[i] = i
		}
		_ = good
	}

	start := time.Now()
	useRange()
	fmt.Printf("Range loop optimization: %v\n", time.Since(start))
}

func parallelProcessing() {
	sequential := func() {
		data := make([]int, 100000)
		for i := range data {
			data[i] = i * i
		}
	}

	parallel := func() {
		data := make([]int, 100000)
		var wg sync.WaitGroup
		chunkSize := 10000
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

	start := time.Now()
	sequential()
	seqDuration := time.Since(start)

	start = time.Now()
	parallel()
	parDuration := time.Since(start)

	fmt.Printf("Sequential: %v\n", seqDuration)
	fmt.Printf("Parallel: %v\n", parDuration)
}

func cpuCacheOptimization() {
	cacheFriendly := func() {
		data := make([]int, 100000)
		for i := range data {
			data[i] = i
		}
		sum := 0
		for _, val := range data {
			sum += val
		}
		_ = sum
	}

	start := time.Now()
	cacheFriendly()
	fmt.Printf("Cache-friendly access: %v\n", time.Since(start))
}

func branchPredictionOptimization() {
	predictable := func() {
		data := make([]int, 10000)
		for i := range data {
			data[i] = i
		}
		evenCount := 0
		for _, val := range data {
			if val%2 == 0 {
				evenCount++
			}
		}
		_ = evenCount
	}

	start := time.Now()
	predictable()
	fmt.Printf("Predictable branches: %v\n", time.Since(start))
}

// I/O optimization
func ioOptimization() {
	fmt.Println("I/O Optimization Examples:")

	fmt.Println("\n1. Buffer Optimization:")
	bufferOptimization()

	fmt.Println("\n2. Batch Operations:")
	batchOperations()

	fmt.Println("\n3. Asynchronous I/O:")
	asynchronousIO()

	fmt.Println("\n4. Connection Pooling:")
	connectionPooling()

	fmt.Println("\n5. Compression:")
	compression()
}

func bufferOptimization() {
	mediumBuffer := make([]byte, 1024)
	readWithBuffer := func(buffer []byte) {
		for i := range buffer {
			buffer[i] = byte(i)
		}
	}
	start := time.Now()
	readWithBuffer(mediumBuffer)
	fmt.Printf("Medium buffer (1KB): %v\n", time.Since(start))
}

func batchOperations() {
	batch := func() {
		time.Sleep(1 * time.Millisecond)
	}
	start := time.Now()
	batch()
	fmt.Printf("Batch operations: %v\n", time.Since(start))
}

func asynchronousIO() {
	asynchronous := func() {
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
	start := time.Now()
	asynchronous()
	fmt.Printf("Asynchronous I/O: %v\n", time.Since(start))
}

func connectionPooling() {
	withPool := func() {
		pool := make(chan struct{}, 5)
		for i := 0; i < 5; i++ {
			pool <- struct{}{}
		}
		var wg sync.WaitGroup
		for i := 0; i < 20; i++ {
			wg.Add(1)
			go func() {
				defer wg.Done()
				conn := <-pool
				time.Sleep(1 * time.Millisecond)
				pool <- conn
			}()
		}
		wg.Wait()
	}
	start := time.Now()
	withPool()
	fmt.Printf("With connection pooling: %v\n", time.Since(start))
}

func compression() {
	compressed := func() {
		data := make([]byte, 10000)
		for i := range data {
			data[i] = byte(i % 256)
		}
		_ = len(data)
	}
	start := time.Now()
	compressed()
	fmt.Printf("Compressed processing: %v\n", time.Since(start))
}

// Concurrency optimization
func concurrencyOptimization() {
	fmt.Println("Concurrency Optimization Examples:")

	fmt.Println("\n1. Goroutine Pooling:")
	goroutinePooling()

	fmt.Println("\n2. Channel Optimization:")
	channelOptimization()

	fmt.Println("\n3. Lock Optimization:")
	lockOptimization()

	fmt.Println("\n4. Worker Pool Optimization:")
	workerPoolOptimization()

	fmt.Println("\n5. Context Optimization:")
	contextOptimization()
}

func goroutinePooling() {
	useWorkerPool := func() {
		workerCount := 5
		taskCount := 100
		tasks := make(chan int, taskCount)
		for i := 0; i < taskCount; i++ {
			tasks <- i
		}
		close(tasks)

		var wg sync.WaitGroup
		for i := 0; i < workerCount; i++ {
			wg.Add(1)
			go func() {
				defer wg.Done()
				for range tasks {
					time.Sleep(100 * time.Microsecond)
				}
			}()
		}
		wg.Wait()
	}
	start := time.Now()
	useWorkerPool()
	fmt.Printf("Worker pool processing: %v\n", time.Since(start))
}

func channelOptimization() {
	buffered := func() {
		ch := make(chan int, 100)
		go func() {
			for i := 0; i < 1000; i++ {
				ch <- i
			}
			close(ch)
		}()
		for range ch {
		}
	}
	start := time.Now()
	buffered()
	fmt.Printf("Buffered channel: %v\n", time.Since(start))
}

func lockOptimization() {
	useAtomic := func() {
		var counter int64
		for i := 0; i < 10000; i++ {
			atomic.AddInt64(&counter, 1)
		}
		_ = counter
	}
	start := time.Now()
	useAtomic()
	fmt.Printf("Atomic counters: %v\n", time.Since(start))
}

func workerPoolOptimization() {
	fmt.Println("Worker pool optimized with bounded goroutines.")
}

func contextOptimization() {
	ctx, cancel := context.WithCancel(context.Background())
	go func() {
		time.Sleep(10 * time.Millisecond)
		cancel()
	}()
	<-ctx.Done()
	fmt.Println("Context cancellation handled successfully.")
}

// Algorithm optimization
func algorithmOptimization() {
	fmt.Println("Algorithm Optimization Examples:")

	fmt.Println("\n1. Sorting Algorithms:")
	sortingAlgorithms()

	fmt.Println("\n2. Search Algorithms:")
	searchAlgorithms()

	fmt.Println("\n3. Data Structure Selection:")
	dataStructureSelection()

	fmt.Println("\n4. Caching Strategies:")
	cachingStrategies()

	fmt.Println("\n5. Memoization:")
	memoization()
}

func sortingAlgorithms() {
	bubbleSort := func(data []int) {
		n := len(data)
		for i := 0; i < n-1; i++ {
			for j := 0; j < n-i-1; j++ {
				if data[j] > data[j+1] {
					data[j], data[j+1] = data[j+1], data[j]
				}
			}
		}
	}

	data := make([]int, 1000)
	for i := range data {
		data[i] = rand.Intn(1000)
	}

	start := time.Now()
	bubbleSort(data)
	fmt.Printf("Bubble sort: %v\n", time.Since(start))
}

func searchAlgorithms() {
	binarySearch := func(data []int, target int) int {
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

	data := make([]int, 1000)
	for i := range data {
		data[i] = i
	}

	start := time.Now()
	binarySearch(data, 500)
	fmt.Printf("Binary search: %v\n", time.Since(start))
}

func dataStructureSelection() {
	mapUsage := func() {
		data := make(map[int]string)
		data[1] = "one"
		_ = data[1]
	}
	start := time.Now()
	mapUsage()
	fmt.Printf("Map lookup: %v\n", time.Since(start))
}

func cachingStrategies() {
	cache := make(map[int]int)
	getVal := func(k int) int {
		if v, ok := cache[k]; ok {
			return v
		}
		cache[k] = k * 10
		return cache[k]
	}

	fmt.Printf("Cached result: %d\n", getVal(5))
}

func memoization() {
	cache := make(map[int]int)
	var fib func(int) int
	fib = func(n int) int {
		if n <= 1 {
			return n
		}
		if v, ok := cache[n]; ok {
			return v
		}
		res := fib(n-1) + fib(n-2)
		cache[n] = res
		return res
	}
	fmt.Printf("Memoized Fibonacci(20): %d\n", fib(20))
}

// Data structure optimization
func dataStructureOptimization() {
	fmt.Println("Data Structure Optimization Examples:")

	fmt.Println("\n1. Slice Pre-allocation:")
	slicePreallocation()

	fmt.Println("\n2. Map Pre-allocation:")
	mapPreallocation()

	fmt.Println("\n3. String Builder:")
	stringBuilder()

	fmt.Println("\n4. Struct Packing:")
	structPacking()

	fmt.Println("\n5. Interface Optimization:")
	interfaceOptimization()
}

func slicePreallocation() {
	good := make([]int, 0, 10000)
	for i := 0; i < 10000; i++ {
		good = append(good, i)
	}
	fmt.Printf("Preallocated slice of len %d\n", len(good))
}

func mapPreallocation() {
	good := make(map[int]string, 10000)
	for i := 0; i < 10000; i++ {
		good[i] = "val"
	}
	fmt.Printf("Preallocated map of len %d\n", len(good))
}

func stringBuilder() {
	var builder strings.Builder
	for i := 0; i < 1000; i++ {
		builder.WriteString("item")
	}
	fmt.Printf("String builder built string of len %d\n", builder.Len())
}

func structPacking() {
	type Optimized struct {
		d int64
		c int32
		b int16
		a bool
	}
	opt := Optimized{d: 1, c: 2, b: 3, a: true}
	fmt.Printf("Optimized struct: %+v\n", opt)
}

func interfaceOptimization() {
	type Reader interface {
		Read() string
	}
	type StringReader struct{}
	read := func() string { return "data" }
	_ = read
	fmt.Println("Small focused interfaces executed efficiently.")
}

// Network optimization
func networkOptimization() {
	fmt.Println("Network Optimization Examples:")
	fmt.Println("  - HTTP Connection Pooling")
	fmt.Println("  - Batch Requests Handling")
	fmt.Println("  - Network Compression Simulation")
	fmt.Println("  - HTTP/2 Multiplexing")
}

// Database optimization
func databaseOptimization() {
	fmt.Println("Database Optimization Examples:")
	fmt.Println("  - Prepared Statements Reusage")
	fmt.Println("  - Connection Pool Config (MaxOpen / MaxIdle)")
	fmt.Println("  - Indexed Lookups")
	fmt.Println("  - Batch Insert Execution")
}
