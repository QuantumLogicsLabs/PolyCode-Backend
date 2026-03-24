package main

import (
	"fmt"
	"runtime"
	"sync"
	"sync/atomic"
	"time"
)

// Fan-out/Fan-in pattern
func fanOutFanIn() {
	fmt.Println("=== Fan-out/Fan-in Pattern ===")

	// Input data
	input := make(chan int, 100)
	for i := 1; i <= 50; i++ {
		input <- i
	}
	close(input)

	// Worker function
	worker := func(id int, input <-chan int, output chan<- int, wg *sync.WaitGroup) {
		defer wg.Done()
		for num := range input {
			// Simulate work
			time.Sleep(time.Millisecond * 10)
			result := num * num
			fmt.Printf("Worker %d: %d^2 = %d\n", id, num, result)
			output <- result
		}
	}

	// Fan-out: distribute work to multiple workers
	const numWorkers = 5
	workerOutputs := make([]chan int, numWorkers)
	var wg sync.WaitGroup

	for i := 0; i < numWorkers; i++ {
		workerOutputs[i] = make(chan int, 10)
		wg.Add(1)
		go worker(i+1, input, workerOutputs[i], &wg)
	}

	// Fan-in: collect results from all workers
	results := make(chan int, 50)
	var fanInWG sync.WaitGroup

	for i, workerOutput := range workerOutputs {
		fanInWG.Add(1)
		go func(id int, input <-chan int) {
			defer fanInWG.Done()
			for result := range input {
				results <- result
			}
			fmt.Printf("Worker %d output channel closed\n", id+1)
		}(i, workerOutput)
	}

	// Wait for all workers to finish
	go func() {
		wg.Wait()
		for _, ch := range workerOutputs {
			close(ch)
		}
	}()

	// Wait for fan-in to finish
	go func() {
		fanInWG.Wait()
		close(results)
	}()

	// Collect results
	var allResults []int
	for result := range results {
		allResults = append(allResults, result)
	}

	fmt.Printf("Collected %d results\n", len(allResults))
}

// Worker pool with bounded concurrency
func boundedWorkerPool() {
	fmt.Println("\n=== Bounded Worker Pool ===")

	type Task struct {
		ID   int
		Data int
	}

	type Result struct {
		TaskID int
		Output int
	}

	const maxWorkers = runtime.NumCPU()
	const maxTasks = 100

	tasks := make(chan Task, maxTasks)
	results := make(chan Result, maxTasks)

	// Create worker pool
	var wg sync.WaitGroup
	for i := 0; i < maxWorkers; i++ {
		wg.Add(1)
		go func(workerID int) {
			defer wg.Done()
			for task := range tasks {
				// Simulate CPU-bound work
				sum := 0
				for i := 1; i <= task.Data; i++ {
					sum += i
				}
				results <- Result{TaskID: task.ID, Output: sum}
				fmt.Printf("Worker %d completed task %d\n", workerID, task.ID)
			}
		}(i)
	}

	// Send tasks
	go func() {
		for i := 1; i <= maxTasks; i++ {
			tasks <- Task{ID: i, Data: i * 10}
		}
		close(tasks)
	}()

	// Wait for all workers to finish
	go func() {
		wg.Wait()
		close(results)
	}()

	// Collect results
	var allResults []Result
	for result := range results {
		allResults = append(allResults, result)
	}

	fmt.Printf("Processed %d tasks with %d workers\n", len(allResults), maxWorkers)
}

// Pipeline pattern
func pipelinePattern() {
	fmt.Println("\n=== Pipeline Pattern ===")

	// Stage 1: Generate numbers
	generator := func(done <-chan struct{}, nums ...int) <-chan int {
		out := make(chan int)
		go func() {
			defer close(out)
			for _, n := range nums {
				select {
				case out <- n:
				case <-done:
					return
				}
			}
		}()
		return out
	}

	// Stage 2: Square numbers
	squarer := func(done <-chan struct{}, in <-chan int) <-chan int {
		out := make(chan int)
		go func() {
			defer close(out)
			for n := range in {
				select {
				case out <- n * n:
				case <-done:
					return
				}
			}
		}()
		return out
	}

	// Stage 3: Filter even numbers
	filter := func(done <-chan struct{}, in <-chan int, predicate func(int) bool) <-chan int {
		out := make(chan int)
		go func() {
			defer close(out)
			for n := range in {
				if predicate(n) {
					select {
					case out <- n:
					case <-done:
						return
					}
				}
			}
		}()
		return out
	}

	// Stage 4: Convert to string
	converter := func(done <-chan struct{}, in <-chan int) <-chan string {
		out := make(chan string)
		go func() {
			defer close(out)
			for n := range in {
				select {
				case out <- fmt.Sprintf("Result: %d", n):
				case <-done:
					return
				}
			}
		}()
		return out
	}

	// Build pipeline
	done := make(chan struct{})
	defer close(done)

	// Input data
	numbers := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}

	// Pipeline stages
	stage1 := generator(done, numbers...)
	stage2 := squarer(done, stage1)
	stage3 := filter(done, stage2, func(n int) bool { return n%2 == 0 })
	stage4 := converter(done, stage3)

	// Collect results
	for result := range stage4 {
		fmt.Println(result)
	}
}

// Atomic operations for high-performance counters
func atomicCounters() {
	fmt.Println("\n=== Atomic Counters ===")

	const numGoroutines = 1000
	const incrementsPerGoroutine = 1000

	// Using atomic operations
	var atomicCounter int64
	var atomicWG sync.WaitGroup

	start := time.Now()
	for i := 0; i < numGoroutines; i++ {
		atomicWG.Add(1)
		go func() {
			defer atomicWG.Done()
			for j := 0; j < incrementsPerGoroutine; j++ {
				atomic.AddInt64(&atomicCounter, 1)
			}
		}()
	}
	atomicWG.Wait()
	atomicTime := time.Since(start)

	// Using mutex for comparison
	var mutexCounter int64
	var mutexWG sync.WaitGroup
	var mutex sync.Mutex

	start = time.Now()
	for i := 0; i < numGoroutines; i++ {
		mutexWG.Add(1)
		go func() {
			defer mutexWG.Done()
			for j := 0; j < incrementsPerGoroutine; j++ {
				mutex.Lock()
				mutexCounter++
				mutex.Unlock()
			}
		}()
	}
	mutexWG.Wait()
	mutexTime := time.Since(start)

	fmt.Printf("Atomic counter: %d (time: %v)\n", atomicCounter, atomicTime)
	fmt.Printf("Mutex counter: %d (time: %v)\n", mutexCounter, mutexTime)
	fmt.Printf("Performance improvement: %.2fx\n", float64(mutexTime)/float64(atomicTime))
}

// Rate limiting pattern
func rateLimiting() {
	fmt.Println("\n=== Rate Limiting Pattern ===")

	const requests = 100
	const rateLimit = 10 // requests per second

	requestCh := make(chan int, requests)
	resultCh := make(chan string, requests)

	// Generate requests
	go func() {
		for i := 1; i <= requests; i++ {
			requestCh <- i
		}
		close(requestCh)
	}()

	// Rate limiter using ticker
	ticker := time.NewTicker(time.Second / time.Duration(rateLimit))
	defer ticker.Stop()

	go func() {
		for req := range requestCh {
			<-ticker.C // Wait for ticker
			resultCh <- fmt.Sprintf("Processed request %d at %v", req, time.Now().Format("15:04:05.000"))
		}
		close(resultCh)
	}()

	// Collect results
	var results []string
	for result := range resultCh {
		results = append(results, result)
		if len(results)%10 == 0 {
			fmt.Printf("Processed %d requests\n", len(results))
		}
	}

	fmt.Printf("Completed %d requests at rate %d/sec\n", len(results), rateLimit)
}

// Circuit breaker pattern
func circuitBreaker() {
	fmt.Println("\n=== Circuit Breaker Pattern ===")

	type CircuitBreaker struct {
		maxFailures  int
		resetTimeout time.Duration
		failures     int
		lastFailure  time.Time
		state        string // "closed", "open", "half-open"
		mu           sync.Mutex
	}

	NewCircuitBreaker := func(maxFailures int, resetTimeout time.Duration) *CircuitBreaker {
		return &CircuitBreaker{
			maxFailures:  maxFailures,
			resetTimeout: resetTimeout,
			state:        "closed",
		}
	}

	call := func(cb *CircuitBreaker, operation func() error) error {
		cb.mu.Lock()
		defer cb.mu.Unlock()

		// Check if circuit should reset
		if cb.state == "open" && time.Since(cb.lastFailure) > cb.resetTimeout {
			cb.state = "half-open"
			cb.failures = 0
		}

		// Reject calls if circuit is open
		if cb.state == "open" {
			return fmt.Errorf("circuit breaker is open")
		}

		// Execute operation
		err := operation()
		if err != nil {
			cb.failures++
			cb.lastFailure = time.Now()
			
			if cb.failures >= cb.maxFailures {
				cb.state = "open"
				fmt.Printf("Circuit breaker opened after %d failures\n", cb.failures)
			}
			return err
		}

		// Reset on success
		if cb.state == "half-open" {
			cb.state = "closed"
			fmt.Println("Circuit breaker reset to closed state")
		}
		cb.failures = 0
		return nil
	}

	cb := NewCircuitBreaker(3, time.Second*2)

	// Simulate operations
	for i := 1; i <= 10; i++ {
		err := call(cb, func() error {
			if i <= 5 {
				return fmt.Errorf("simulated failure %d", i)
			}
			fmt.Printf("Operation %d succeeded\n", i)
			return nil
		})

		if err != nil {
			fmt.Printf("Operation %d failed: %v (circuit state: %s)\n", i, err, cb.state)
		}

		time.Sleep(time.Millisecond * 500)
	}
}

func main() {
	fmt.Println("=== Concurrency Patterns for Performance ===")

	fanOutFanIn()
	boundedWorkerPool()
	pipelinePattern()
	atomicCounters()
	rateLimiting()
	circuitBreaker()
}
