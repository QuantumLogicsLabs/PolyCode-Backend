package main

import (
	"fmt"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Channel Examples ===\n")

	// Example 1: Basic channel communication
	fmt.Println("1. Basic Channel Communication:")
	ch := make(chan string)
	
	go func() {
		ch <- "Hello from goroutine!"
	}()
	
	message := <-ch
	fmt.Printf("Received: %s\n", message)

	// Example 2: Buffered channel
	fmt.Println("\n2. Buffered Channel:")
	bufferedCh := make(chan int, 3)
	
	// Send without blocking
	bufferedCh <- 1
	bufferedCh <- 2
	bufferedCh <- 3
	
	// Receive values
	for i := 0; i < 3; i++ {
		fmt.Printf("Received: %d\n", <-bufferedCh)
	}

	// Example 3: Channel with range
	fmt.Println("\n3. Channel with Range:")
	numbers := make(chan int)
	
	go func() {
		for i := 1; i <= 5; i++ {
			numbers <- i
		}
		close(numbers)
	}()
	
	for num := range numbers {
		fmt.Printf("Number: %d\n", num)
	}

	// Example 4: Select statement
	fmt.Println("\n4. Select Statement:")
	ch1 := make(chan string)
	ch2 := make(chan string)
	
	go func() {
		time.Sleep(100 * time.Millisecond)
		ch1 <- "From channel 1"
	}()
	
	go func() {
		time.Sleep(200 * time.Millisecond)
		ch2 <- "From channel 2"
	}()
	
	for i := 0; i < 2; i++ {
		select {
		case msg1 := <-ch1:
			fmt.Printf("Received: %s\n", msg1)
		case msg2 := <-ch2:
			fmt.Printf("Received: %s\n", msg2)
		case <-time.After(50 * time.Millisecond):
			fmt.Println("Timeout: No message received")
		}
	}

	// Example 5: Worker pool pattern
	fmt.Println("\n5. Worker Pool Pattern:")
	jobs := make(chan int, 10)
	results := make(chan int, 10)
	
	// Start workers
	for w := 1; w <= 3; w++ {
		go worker(w, jobs, results)
	}
	
	// Send jobs
	for j := 1; j <= 5; j++ {
		jobs <- j
	}
	close(jobs)
	
	// Collect results
	for r := 1; r <= 5; r++ {
		result := <-results
		fmt.Printf("Result: %d\n", result)
	}

	// Example 6: Pipeline pattern
	fmt.Println("\n6. Pipeline Pattern:")
	input := make(chan int)
	squared := make(chan int)
	
	// Stage 1: Generate numbers
	go func() {
		for i := 1; i <= 3; i++ {
			input <- i
		}
		close(input)
	}()
	
	// Stage 2: Square numbers
	go squareNumbers(input, squared)
	
	// Stage 3: Print results
	for result := range squared {
		fmt.Printf("Squared: %d\n", result)
	}

	// Example 7: Fan-in pattern
	fmt.Println("\n7. Fan-in Pattern:")
	chA := make(chan string)
	chB := make(chan string)
	merged := make(chan string)
	
	go func() {
		for i := 0; i < 3; i++ {
			chA <- fmt.Sprintf("A-%d", i)
		}
		close(chA)
	}()
	
	go func() {
		for i := 0; i < 3; i++ {
			chB <- fmt.Sprintf("B-%d", i)
		}
		close(chB)
	}()
	
	go fanIn(chA, chB, merged)
	
	for msg := range merged {
		fmt.Printf("Merged: %s\n", msg)
	}
}

func worker(id int, jobs <-chan int, results chan<- int) {
	for job := range jobs {
		fmt.Printf("Worker %d started job %d\n", id, job)
		time.Sleep(50 * time.Millisecond) // Simulate work
		results <- job * 2
		fmt.Printf("Worker %d finished job %d\n", id, job)
	}
}

func squareNumbers(input <-chan int, output chan<- int) {
	for num := range input {
		output <- num * num
	}
	close(output)
}

func fanIn(ch1, ch2 <-chan string, output chan<- string) {
	for ch1 != nil || ch2 != nil {
		select {
		case msg, ok := <-ch1:
			if ok {
				output <- msg
			} else {
				ch1 = nil
			}
		case msg, ok := <-ch2:
			if ok {
				output <- msg
			} else {
				ch2 = nil
			}
		}
	}
	close(output)
}
