package main

import (
	"fmt"
	"runtime"
	"sort"
	"sync"
	"time"
)

// Different sorting algorithms for benchmarking
func bubbleSort(arr []int) []int {
	n := len(arr)
	result := make([]int, len(arr))
	copy(result, arr)

	for i := 0; i < n-1; i++ {
		for j := 0; j < n-i-1; j++ {
			if result[j] > result[j+1] {
				result[j], result[j+1] = result[j+1], result[j]
			}
		}
	}
	return result
}

func selectionSort(arr []int) []int {
	n := len(arr)
	result := make([]int, len(arr))
	copy(result, arr)

	for i := 0; i < n-1; i++ {
		minIdx := i
		for j := i + 1; j < n; j++ {
			if result[j] < result[minIdx] {
				minIdx = j
			}
		}
		result[i], result[minIdx] = result[minIdx], result[i]
	}
	return result
}

func insertionSort(arr []int) []int {
	n := len(arr)
	result := make([]int, len(arr))
	copy(result, arr)

	for i := 1; i < n; i++ {
		key := result[i]
		j := i - 1
		for j >= 0 && result[j] > key {
			result[j+1] = result[j]
			j--
		}
		result[j+1] = key
	}
	return result
}

func mergeSort(arr []int) []int {
	if len(arr) <= 1 {
		return arr
	}

	mid := len(arr) / 2
	left := mergeSort(arr[:mid])
	right := mergeSort(arr[mid:])

	return merge(left, right)
}

func merge(left, right []int) []int {
	result := make([]int, 0, len(left)+len(right))
	i, j := 0, 0

	for i < len(left) && j < len(right) {
		if left[i] <= right[j] {
			result = append(result, left[i])
			i++
		} else {
			result = append(result, right[j])
			j++
		}
	}

	result = append(result, left[i:]...)
	result = append(result, right[j:]...)
	return result
}

// Benchmark function
func benchmark(name string, fn func([]int) []int, data []int) time.Duration {
	start := time.Now()
	result := fn(data)
	duration := time.Since(start)
	
	// Verify result is sorted
	if !sort.IntsAreSorted(result) {
		fmt.Printf("ERROR: %s produced unsorted result!\n", name)
	}
	
	return duration
}

// Memory profiling helper
func measureMemory() (uint64, uint64) {
	var m1, m2 runtime.MemStats
	runtime.GC()
	runtime.ReadMemStats(&m1)
	
	// Do some work here to measure
	data := make([]int, 100000)
	for i := range data {
		data[i] = i
	}
	_ = bubbleSort(data)
	
	runtime.ReadMemStats(&m2)
	return m1.Alloc, m2.Alloc
}

// Concurrent processing benchmark
func processSequential(data []int) int {
	sum := 0
	for _, v := range data {
		sum += v * v // Square each number
	}
	return sum
}

func processConcurrent(data []int, workers int) int {
	chunkSize := len(data) / workers
	results := make(chan int, workers)
	var wg sync.WaitGroup

	for i := 0; i < workers; i++ {
		wg.Add(1)
		go func(start int) {
			defer wg.Done()
			end := start + chunkSize
			if end > len(data) {
				end = len(data)
			}
			
			sum := 0
			for j := start; j < end; j++ {
				sum += data[j] * data[j]
			}
			results <- sum
		}(i * chunkSize)
	}

	wg.Wait()
	close(results)

	totalSum := 0
	for result := range results {
		totalSum += result
	}
	return totalSum
}

func main() {
	fmt.Println("=== Benchmark and Profiling Examples ===")

	// Generate test data
	sizes := []int{1000, 5000, 10000}
	
	for _, size := range sizes {
		fmt.Printf("\n--- Benchmarking with %d elements ---\n", size)
		
		// Generate random data
		data := make([]int, size)
		for i := range data {
			data[i] = size - i // Reverse sorted data (worst case for some algorithms)
		}
		
		// Benchmark different sorting algorithms
		fmt.Printf("Bubble Sort: %v\n", benchmark("Bubble Sort", bubbleSort, data))
		fmt.Printf("Selection Sort: %v\n", benchmark("Selection Sort", selectionSort, data))
		fmt.Printf("Insertion Sort: %v\n", benchmark("Insertion Sort", insertionSort, data))
		fmt.Printf("Merge Sort: %v\n", benchmark("Merge Sort", mergeSort, data))
		
		// Built-in sort for comparison
		start := time.Now()
		sorted := make([]int, len(data))
		copy(sorted, data)
		sort.Ints(sorted)
		fmt.Printf("Built-in Sort: %v\n", time.Since(start))
	}

	// Memory usage measurement
	fmt.Println("\n--- Memory Usage Measurement ---")
	before, after := measureMemory()
	fmt.Printf("Memory before: %d bytes\n", before)
	fmt.Printf("Memory after: %d bytes\n", after)
	fmt.Printf("Memory used: %d bytes\n", after-before)

	// Concurrent vs Sequential processing
	fmt.Println("\n--- Concurrent vs Sequential Processing ---")
	
	dataSizes := []int{10000, 100000, 1000000}
	
	for _, size := range dataSizes {
		data := make([]int, size)
		for i := range data {
			data[i] = i
		}
		
		fmt.Printf("\nDataset size: %d\n", size)
		
		// Sequential processing
		start := time.Now()
		seqResult := processSequential(data)
		seqDuration := time.Since(start)
		fmt.Printf("Sequential: %v (result: %d)\n", seqDuration, seqResult)
		
		// Concurrent processing with different worker counts
		for workers := 2; workers <= 8; workers *= 2 {
			start := time.Now()
			concResult := processConcurrent(data, workers)
			concDuration := time.Since(start)
			
			if seqResult == concResult {
				speedup := float64(seqDuration) / float64(concDuration)
				fmt.Printf("Concurrent (%d workers): %v (speedup: %.2fx)\n", workers, concDuration, speedup)
			} else {
				fmt.Printf("Concurrent (%d workers): %v (ERROR: results don't match!)\n", workers, concDuration)
			}
		}
	}

	// Goroutine overhead measurement
	fmt.Println("\n--- Goroutine Overhead Measurement ---")
	
	const iterations = 1000000
	
	// Sequential execution
	start := time.Now()
	for i := 0; i < iterations; i++ {
		_ = i * i
	}
	seqTime := time.Since(start)
	
	// Concurrent execution
	start = time.Now()
	var wg sync.WaitGroup
	for i := 0; i < iterations; i++ {
		wg.Add(1)
		go func(val int) {
			defer wg.Done()
			_ = val * val
		}(i)
	}
	wg.Wait()
	concTime := time.Since(start)
	
	fmt.Printf("Sequential (%d iterations): %v\n", iterations, seqTime)
	fmt.Printf("Concurrent (%d goroutines): %v\n", iterations, concTime)
	fmt.Printf("Overhead factor: %.2fx\n", float64(concTime)/float64(seqTime))

	// Cache performance demonstration
	fmt.Println("\n--- Cache Performance Demonstration ---")
	
	const matrixSize = 1000
	
	// Row-major access (cache-friendly)
	matrix := make([][]int, matrixSize)
	for i := range matrix {
		matrix[i] = make([]int, matrixSize)
		for j := range matrix[i] {
			matrix[i][j] = i + j
		}
	}
	
	start = time.Now()
	sum := 0
	for i := 0; i < matrixSize; i++ {
		for j := 0; j < matrixSize; j++ {
			sum += matrix[i][j] // Row-major access
		}
	}
	rowMajorTime := time.Since(start)
	
	// Column-major access (cache-unfriendly)
	start = time.Now()
	sum = 0
	for j := 0; j < matrixSize; j++ {
		for i := 0; i < matrixSize; i++ {
			sum += matrix[i][j] // Column-major access
		}
	}
	colMajorTime := time.Since(start)
	
	fmt.Printf("Row-major access: %v\n", rowMajorTime)
	fmt.Printf("Column-major access: %v\n", colMajorTime)
	fmt.Printf("Performance difference: %.2fx\n", float64(colMajorTime)/float64(rowMajorTime))
}
