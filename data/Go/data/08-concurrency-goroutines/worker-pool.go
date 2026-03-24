package main

import (
	"fmt"
	"sync"
	"time"
)

type Job struct {
	ID  int
	Num int
}

type Result struct {
	JobID int
	Sum   int
}

func worker(id int, jobs <-chan Job, results chan<- Result, wg *sync.WaitGroup) {
	defer wg.Done()

	for job := range jobs {
		fmt.Printf("Worker %d processing job %d\n", id, job.ID)
		
		// Simulate work
		sum := 0
		for i := 1; i <= job.Num; i++ {
			sum += i
			time.Sleep(time.Millisecond * 10)
		}

		results <- Result{JobID: job.ID, Sum: sum}
		fmt.Printf("Worker %d completed job %d\n", id, job.ID)
	}
}

func main() {
	fmt.Println("=== Worker Pool Pattern ===")

	const numJobs = 8
	const numWorkers = 3

	jobs := make(chan Job, numJobs)
	results := make(chan Result, numJobs)
	var wg sync.WaitGroup

	// Start workers
	for w := 1; w <= numWorkers; w++ {
		wg.Add(1)
		go worker(w, jobs, results, &wg)
	}

	// Send jobs
	for j := 1; j <= numJobs; j++ {
		jobs <- Job{ID: j, Num: j * 10}
	}
	close(jobs)

	// Wait for workers to finish
	wg.Wait()
	close(results)

	// Collect results
	fmt.Println("\nResults:")
	for result := range results {
		fmt.Printf("Job %d: Sum = %d\n", result.JobID, result.Sum)
	}
}
