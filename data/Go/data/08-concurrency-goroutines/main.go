package main

import (
	"fmt"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Goroutine Examples ===\n")

	// Example 1: Basic goroutine
	fmt.Println("1. Basic Goroutine:")
	go sayHello()
	fmt.Println("Main function continues...")
	time.Sleep(100 * time.Millisecond) // Wait for goroutine to finish

	// Example 2: Multiple goroutines
	fmt.Println("\n2. Multiple Goroutines:")
	for i := 0; i < 3; i++ {
		go printNumber(i)
	}
	time.Sleep(100 * time.Millisecond)

	// Example 3: Using WaitGroup
	fmt.Println("\n3. Using WaitGroup:")
	var wg sync.WaitGroup
	
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go worker(&wg, i)
	}
	
	fmt.Println("Waiting for workers to complete...")
	wg.Wait()
	fmt.Println("All workers completed!")

	// Example 4: Anonymous goroutines
	fmt.Println("\n4. Anonymous Goroutines:")
	for i := 0; i < 3; i++ {
		go func(id int) {
			fmt.Printf("Anonymous goroutine %d is running\n", id)
		}(i)
	}
	time.Sleep(100 * time.Millisecond)

	// Example 5: Goroutine with shared state (using mutex)
	fmt.Println("\n5. Goroutine with Shared State:")
	var counter int
	var mutex sync.Mutex
	
	for i := 0; i < 10; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			mutex.Lock()
			counter++
			fmt.Printf("Counter incremented to %d\n", counter)
			mutex.Unlock()
		}()
	}
	
	wg.Wait()
	fmt.Printf("Final counter value: %d\n", counter)

	// Example 6: Simulating work
	fmt.Println("\n6. Simulating Work:")
	tasks := []string{"task1", "task2", "task3", "task4", "task5"}
	
	for _, task := range tasks {
		wg.Add(1)
		go func(t string) {
			defer wg.Done()
			processTask(t)
		}(task)
	}
	
	wg.Wait()
	fmt.Println("All tasks processed!")
}

func sayHello() {
	fmt.Println("Hello from goroutine!")
}

func printNumber(num int) {
	fmt.Printf("Number: %d\n", num)
}

func worker(wg *sync.WaitGroup, id int) {
	defer wg.Done()
	fmt.Printf("Worker %d started\n", id)
	time.Sleep(50 * time.Millisecond) // Simulate work
	fmt.Printf("Worker %d finished\n", id)
}

func processTask(task string) {
	fmt.Printf("Processing %s...\n", task)
	time.Sleep(20 * time.Millisecond) // Simulate processing time
	fmt.Printf("Completed %s\n", task)
}
