package main

import (
	"fmt"
	"math/rand"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Worker Pool Pattern ===")
	
	// Basic worker pool
	fmt.Println("\n--- Basic Worker Pool ---")
	basicWorkerPool()
	
	// Dynamic worker pool
	fmt.Println("\n--- Dynamic Worker Pool ---")
	dynamicWorkerPool()
	
	// Worker pool with results
	fmt.Println("\n--- Worker Pool with Results ---")
	workerPoolWithResults()
	
	// Worker pool with error handling
	fmt.Println("\n--- Worker Pool with Error Handling ---")
	workerPoolWithErrorHandling()
	
	// Worker pool with shutdown
	fmt.Println("\n--- Worker Pool with Graceful Shutdown ---")
	workerPoolWithShutdown()
	
	// Pipeline worker pool
	fmt.Println("\n--- Pipeline Worker Pool ---")
	pipelineWorkerPool()
	
	// Rate-limited worker pool
	fmt.Println("\n--- Rate-Limited Worker Pool ---")
	rateLimitedWorkerPool()
}

// Basic worker pool implementation
func basicWorkerPool() {
	const numWorkers = 3
	const numJobs = 10
	
	jobs := make(chan int, numJobs)
	results := make(chan int, numJobs)
	
	// Start workers
	for w := 1; w <= numWorkers; w++ {
		go worker(w, jobs, results)
	}
	
	// Send jobs
	for j := 1; j <= numJobs; j++ {
		jobs <- j
	}
	close(jobs)
	
	// Collect results
	for a := 1; a <= numJobs; a++ {
		result := <-results
		fmt.Printf("Result: %d\n", result)
	}
}

func worker(id int, jobs <-chan int, results chan<- int) {
	for j := range jobs {
		fmt.Printf("Worker %d processing job %d\n", id, j)
		time.Sleep(time.Millisecond * time.Duration(rand.Intn(100)))
		results <- j * j // Square the number
	}
}

// Dynamic worker pool
func dynamicWorkerPool() {
	const maxWorkers = 5
	const numJobs = 15
	
	var workerCount int
	var workerWg sync.WaitGroup
	
	jobs := make(chan int, numJobs)
	results := make(chan int, numJobs)
	
	// Function to start a new worker
	startWorker := func() {
		workerWg.Add(1)
		workerCount++
		fmt.Printf("Starting worker %d (total: %d)\n", workerCount, workerCount)
		
		go func(id int) {
			defer workerWg.Done()
			dynamicWorker(id, jobs, results)
		}(workerCount)
	}
	
	// Start initial workers
	for i := 0; i < 3; i++ {
		startWorker()
	}
	
	// Send jobs and dynamically scale workers
	go func() {
		for j := 1; j <= numJobs; j++ {
			// Add more workers if needed
			if len(jobs) > 5 && workerCount < maxWorkers {
				startWorker()
			}
			jobs <- j
			time.Sleep(time.Millisecond * 10)
		}
		close(jobs)
	}()
	
	// Collect results
	for i := 0; i < numJobs; i++ {
		result := <-results
		fmt.Printf("Received result: %d\n", result)
	}
	
	workerWg.Wait()
	close(results)
}

func dynamicWorker(id int, jobs <-chan int, results chan<- int) {
	for j := range jobs {
		fmt.Printf("Dynamic worker %d processing job %d\n", id, j)
		time.Sleep(time.Millisecond * time.Duration(rand.Intn(50)))
		results <- j * 2
	}
}

// Worker pool with structured results
type JobResult struct {
	JobID   int
	WorkerID int
	Result  int
	Error   error
}

func workerPoolWithResults() {
	const numWorkers = 4
	const numJobs = 12
	
	jobs := make(chan int, numJobs)
	results := make(chan JobResult, numJobs)
	
	// Start workers
	var wg sync.WaitGroup
	for i := 1; i <= numWorkers; i++ {
		wg.Add(1)
		go func(workerID int) {
			defer wg.Done()
			resultWorker(workerID, jobs, results)
		}(i)
	}
	
	// Send jobs
	go func() {
		for j := 1; j <= numJobs; j++ {
			jobs <- j
		}
		close(jobs)
	}()
	
	// Wait for workers to finish
	go func() {
		wg.Wait()
		close(results)
	}()
	
	// Collect results
	for result := range results {
		if result.Error != nil {
			fmt.Printf("Job %d failed on worker %d: %v\n", 
				result.JobID, result.WorkerID, result.Error)
		} else {
			fmt.Printf("Job %d completed by worker %d: result %d\n", 
				result.JobID, result.WorkerID, result.Result)
		}
	}
}

func resultWorker(workerID int, jobs <-chan int, results chan<- JobResult) {
	for jobID := range jobs {
		// Simulate work that might fail
		time.Sleep(time.Millisecond * time.Duration(rand.Intn(100)))
		
		if rand.Intn(10) == 0 { // 10% chance of failure
			results <- JobResult{
				JobID:    jobID,
				WorkerID: workerID,
				Error:    fmt.Errorf("random failure"),
			}
		} else {
			results <- JobResult{
				JobID:    jobID,
				WorkerID: workerID,
				Result:   jobID * 3,
			}
		}
	}
}

// Worker pool with error handling and retry
func workerPoolWithErrorHandling() {
	const numWorkers = 3
	const numJobs = 10
	const maxRetries = 2
	
	jobs := make(chan Job, numJobs)
	results := make(chan JobResult, numJobs)
	
	// Start workers
	var wg sync.WaitGroup
	for i := 1; i <= numWorkers; i++ {
		wg.Add(1)
		go func(workerID int) {
			defer wg.Done()
			retryWorker(workerID, jobs, results, maxRetries)
		}(i)
	}
	
	// Send jobs
	go func() {
		for j := 1; j <= numJobs; j++ {
			jobs <- Job{ID: j, Data: fmt.Sprintf("data-%d", j)}
		}
		close(jobs)
	}()
	
	// Wait for workers
	go func() {
		wg.Wait()
		close(results)
	}()
	
	// Collect results
	for result := range results {
		if result.Error != nil {
			fmt.Printf("Job %d ultimately failed: %v\n", result.JobID, result.Error)
		} else {
			fmt.Printf("Job %d succeeded: %s\n", result.JobID, result.Data)
		}
	}
}

type Job struct {
	ID   int
	Data string
}

func retryWorker(workerID int, jobs <-chan Job, results chan<- JobResult, maxRetries int) {
	for job := range jobs {
		var lastErr error
		
		for attempt := 0; attempt <= maxRetries; attempt++ {
			err := processJob(job)
			if err == nil {
				results <- JobResult{
					JobID:    job.ID,
					WorkerID: workerID,
					Result:   len(job.Data),
				}
				break
			}
			
			lastErr = err
			if attempt < maxRetries {
				fmt.Printf("Worker %d retrying job %d (attempt %d)\n", 
					workerID, job.ID, attempt+1)
				time.Sleep(time.Millisecond * 50)
			}
		}
		
		if lastErr != nil {
			results <- JobResult{
				JobID:    job.ID,
				WorkerID: workerID,
				Error:    fmt.Errorf("after %d retries: %w", maxRetries, lastErr),
			}
		}
	}
}

func processJob(job Job) error {
	// Simulate processing that might fail
	if rand.Intn(5) == 0 { // 20% chance of failure
		return fmt.Errorf("processing failed for job %d", job.ID)
	}
	
	// Simulate work
	time.Sleep(time.Millisecond * time.Duration(rand.Intn(50)))
	return nil
}

// Worker pool with graceful shutdown
func workerPoolWithShutdown() {
	const numWorkers = 3
	
	jobs := make(chan int)
	results := make(chan int)
	
	// Context for shutdown
	shutdown := make(chan struct{})
	var wg sync.WaitGroup
	
	// Start workers
	for i := 1; i <= numWorkers; i++ {
		wg.Add(1)
		go func(workerID int) {
			defer wg.Done()
			shutdownWorker(workerID, jobs, results, shutdown)
		}(i)
	}
	
	// Send some jobs
	go func() {
		for i := 1; i <= 10; i++ {
			select {
			case jobs <- i:
				fmt.Printf("Sent job %d\n", i)
			case <-shutdown:
				return
			}
			time.Sleep(time.Millisecond * 50)
		}
		fmt.Println("Finished sending jobs")
	}()
	
	// Collect results for a while
	go func() {
		for i := 0; i < 5; i++ {
			result := <-results
			fmt.Printf("Received result: %d\n", result)
		}
	}()
	
	// Let it run for a bit
	time.Sleep(time.Second)
	
	// Initiate shutdown
	fmt.Println("Initiating shutdown...")
	close(shutdown)
	
	// Wait for workers to finish
	wg.Wait()
	close(jobs)
	close(results)
	
	fmt.Println("Shutdown complete")
}

func shutdownWorker(id int, jobs <-chan int, results chan<- int, shutdown <-chan struct{}) {
	for {
		select {
		case job, ok := <-jobs:
			if !ok {
				fmt.Printf("Worker %d: jobs channel closed\n", id)
				return
			}
			fmt.Printf("Worker %d processing job %d\n", id, job)
			time.Sleep(time.Millisecond * 100)
			
			select {
			case results <- job * 2:
			case <-shutdown:
				fmt.Printf("Worker %d shutting down\n", id)
				return
			}
			
		case <-shutdown:
			fmt.Printf("Worker %d received shutdown signal\n", id)
			return
		}
	}
}

// Pipeline worker pool
func pipelineWorkerPool() {
	const numWorkers = 3
	
	// Pipeline stages
	input := make(chan int, 10)
	stage1 := make(chan int, 10)
	stage2 := make(chan int, 10)
	output := make(chan int, 10)
	
	// Stage 1: Double the numbers
	var wg1 sync.WaitGroup
	for i := 1; i <= numWorkers; i++ {
		wg1.Add(1)
		go func(id int) {
			defer wg1.Done()
			pipelineStage(id, input, stage1, func(x int) int { return x * 2 })
		}(i)
	}
	
	// Stage 2: Add 10
	var wg2 sync.WaitGroup
	for i := 1; i <= numWorkers; i++ {
		wg2.Add(1)
		go func(id int) {
			defer wg2.Done()
			pipelineStage(id, stage1, stage2, func(x int) int { return x + 10 })
		}(i)
	}
	
	// Stage 3: Square
	var wg3 sync.WaitGroup
	for i := 1; i <= numWorkers; i++ {
		wg3.Add(1)
		go func(id int) {
			defer wg3.Done()
			pipelineStage(id, stage2, output, func(x int) int { return x * x })
		}(i)
	}
	
	// Send input
	go func() {
		for i := 1; i <= 9; i++ {
			input <- i
		}
		close(input)
	}()
	
	// Wait for all stages
	go func() {
		wg1.Wait()
		close(stage1)
	}()
	
	go func() {
		wg2.Wait()
		close(stage2)
	}()
	
	go func() {
		wg3.Wait()
		close(output)
	}()
	
	// Collect final results
	for result := range output {
		fmt.Printf("Pipeline result: %d\n", result)
	}
}

func pipelineStage(workerID int, input <-chan int, output chan<- int, process func(int) int) {
	for value := range input {
		result := process(value)
		fmt.Printf("Stage worker %d: %d -> %d\n", workerID, value, result)
		time.Sleep(time.Millisecond * 50)
		output <- result
	}
}

// Rate-limited worker pool
func rateLimitedWorkerPool() {
	const numWorkers = 3
	const rateLimit = 2 // jobs per second
	
	jobs := make(chan int, 20)
	results := make(chan int, 20)
	
	// Rate limiter
	limiter := time.NewTicker(time.Second / time.Duration(rateLimit))
	defer limiter.Stop()
	
	// Start workers
	var wg sync.WaitGroup
	for i := 1; i <= numWorkers; i++ {
		wg.Add(1)
		go func(workerID int) {
			defer wg.Done()
			rateLimitedWorker(workerID, jobs, results, limiter.C)
		}(i)
	}
	
	// Send jobs
	go func() {
		for i := 1; i <= 15; i++ {
			jobs <- i
		}
		close(jobs)
	}()
	
	// Wait for workers
	go func() {
		wg.Wait()
		close(results)
	}()
	
	// Collect results
	for result := range results {
		fmt.Printf("Rate-limited result: %d\n", result)
	}
}

func rateLimitedWorker(workerID int, jobs <-chan int, results chan<- int, rateLimit <-chan time.Time) {
	for job := range jobs {
		// Wait for rate limit token
		<-rateLimit
		
		fmt.Printf("Rate-limited worker %d processing job %d\n", workerID, job)
		time.Sleep(time.Millisecond * 100)
		results <- job + 1000
	}
}

// Advanced worker pool patterns

type WorkItem struct {
	ID       int
	Priority int
	Data     interface{}
}

type PriorityQueue struct {
	items []WorkItem
	mu    sync.Mutex
}

func (pq *PriorityQueue) Push(item WorkItem) {
	pq.mu.Lock()
	defer pq.mu.Unlock()
	
	pq.items = append(pq.items, item)
	
	// Simple bubble sort for priority (higher number = higher priority)
	for i := len(pq.items) - 1; i > 0; i-- {
		if pq.items[i].Priority > pq.items[i-1].Priority {
			pq.items[i], pq.items[i-1] = pq.items[i-1], pq.items[i]
		} else {
			break
		}
	}
}

func (pq *PriorityQueue) Pop() (WorkItem, bool) {
	pq.mu.Lock()
	defer pq.mu.Unlock()
	
	if len(pq.items) == 0 {
		return WorkItem{}, false
	}
	
	item := pq.items[0]
	pq.items = pq.items[1:]
	return item, true
}

func priorityWorkerPool() {
	const numWorkers = 2
	
	pq := &PriorityQueue{}
	results := make(chan WorkItem, 20)
	
	var wg sync.WaitGroup
	
	// Start workers
	for i := 1; i <= numWorkers; i++ {
		wg.Add(1)
		go func(workerID int) {
			defer wg.Done()
			priorityWorker(workerID, pq, results)
		}(i)
	}
	
	// Add work items with different priorities
	go func() {
		workItems := []WorkItem{
			{ID: 1, Priority: 1, Data: "low priority"},
			{ID: 2, Priority: 5, Data: "high priority"},
			{ID: 3, Priority: 3, Data: "medium priority"},
			{ID: 4, Priority: 5, Data: "another high priority"},
			{ID: 5, Priority: 1, Data: "another low priority"},
		}
		
		for _, item := range workItems {
			pq.Push(item)
			time.Sleep(time.Millisecond * 10)
		}
		
		// Add sentinel value to stop workers
		pq.Push(WorkItem{ID: -1, Priority: 0})
	}()
	
	// Wait for workers
	go func() {
		wg.Wait()
		close(results)
	}()
	
	// Collect results
	for result := range results {
		if result.ID == -1 {
			continue
		}
		fmt.Printf("Processed priority item %d (priority %d): %v\n", 
			result.ID, result.Priority, result.Data)
	}
}

func priorityWorker(workerID int, pq *PriorityQueue, results chan<- WorkItem) {
	for {
		item, ok := pq.Pop()
		if !ok || item.ID == -1 {
			return
		}
		
		fmt.Printf("Priority worker %d processing item %d (priority %d)\n", 
			workerID, item.ID, item.Priority)
		time.Sleep(time.Millisecond * 100)
		results <- item
	}
}
