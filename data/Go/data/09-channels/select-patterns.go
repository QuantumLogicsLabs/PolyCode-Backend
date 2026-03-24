package main

import (
	"context"
	"fmt"
	"math/rand"
	"time"
)

func main() {
	fmt.Println("=== Select Statement Patterns ===")
	
	// Basic select
	fmt.Println("\n--- Basic Select ---")
	basicSelect()
	
	// Select with timeout
	fmt.Println("\n--- Select with Timeout ---")
	selectWithTimeout()
	
	// Select with default
	fmt.Println("\n--- Select with Default ---")
	selectWithDefault()
	
	// Select for non-blocking operations
	fmt.Println("\n--- Non-blocking Operations ---")
	nonBlockingOperations()
	
	// Select for multiplexing
	fmt.Println("\n--- Multiplexing ---")
	multiplexing()
	
	// Select with context cancellation
	fmt.Println("\n--- Context Cancellation ---")
	selectWithContext()
	
	// Select for fan-in
	fmt.Println("\n--- Fan-in with Select ---")
	fanInWithSelect()
	
	// Select for priority channels
	fmt.Println("\n--- Priority Channels ---")
	priorityChannels()
	
	// Select for command processing
	fmt.Println("\n--- Command Processing ---")
	commandProcessing()
}

// Basic select example
func basicSelect() {
	ch1 := make(chan string)
	ch2 := make(chan string)
	
	// Send data in separate goroutines
	go func() {
		time.Sleep(time.Second * 1)
		ch1 <- "from channel 1"
	}()
	
	go func() {
		time.Sleep(time.Second * 2)
		ch2 <- "from channel 2"
	}()
	
	// Select waits for any channel to have data
	for i := 0; i < 2; i++ {
		select {
		case msg1 := <-ch1:
			fmt.Printf("Received: %s\n", msg1)
		case msg2 := <-ch2:
			fmt.Printf("Received: %s\n", msg2)
		}
	}
}

// Select with timeout
func selectWithTimeout() {
	ch := make(chan string)
	
	go func() {
		time.Sleep(time.Second * 3) // This will exceed timeout
		ch <- "delayed message"
	}()
	
	select {
	case msg := <-ch:
		fmt.Printf("Received: %s\n", msg)
	case <-time.After(time.Second * 2):
		fmt.Println("Timeout occurred")
	}
	
	// Another timeout example with context
	fmt.Println("\n--- Timeout with Context ---")
	ctx, cancel := context.WithTimeout(context.Background(), time.Second*1)
	defer cancel()
	
	ch2 := make(chan string)
	go func() {
		time.Sleep(time.Second * 2)
		ch2 <- "too late"
	}()
	
	select {
	case msg := <-ch2:
		fmt.Printf("Received: %s\n", msg)
	case <-ctx.Done():
		fmt.Printf("Context timeout: %v\n", ctx.Err())
	}
}

// Select with default clause
func selectWithDefault() {
	ch := make(chan string)
	
	// Non-blocking receive
	select {
	case msg := <-ch:
		fmt.Printf("Received: %s\n", msg)
	default:
		fmt.Println("No data available")
	}
	
	// Non-blocking send
	select {
	case ch <- "test message":
		fmt.Println("Message sent")
	default:
		fmt.Println("Cannot send, channel full or no receiver")
	}
	
	// Start a receiver and try again
	go func() {
		time.Sleep(time.Millisecond * 100)
		msg := <-ch
		fmt.Printf("Goroutine received: %s\n", msg)
	}()
	
	select {
	case ch <- "test message 2":
		fmt.Println("Message sent successfully")
	default:
		fmt.Println("Still cannot send")
	}
	
	time.Sleep(time.Millisecond * 200)
}

// Non-blocking operations
func nonBlockingOperations() {
	ch1 := make(chan int)
	ch2 := make(chan int)
	
	// Producer goroutines
	go func() {
		for i := 0; i < 5; i++ {
			time.Sleep(time.Millisecond * 100)
			ch1 <- i
		}
	}()
	
	go func() {
		for i := 10; i < 15; i++ {
			time.Sleep(time.Millisecond * 150)
			ch2 <- i
		}
	}()
	
	// Non-blocking multiplexing
	for {
		select {
		case val := <-ch1:
			fmt.Printf("From ch1: %d\n", val)
		case val := <-ch2:
			fmt.Printf("From ch2: %d\n", val)
		default:
			fmt.Println("No data available, doing other work...")
			time.Sleep(time.Millisecond * 50)
			continue
		}
	}
}

// Multiplexing with select
func multiplexing() {
	channels := make([]chan int, 3)
	for i := range channels {
		channels[i] = make(chan int)
		
		// Start producer for each channel
		go func(ch chan<- int, id int) {
			for j := 0; j < 3; j++ {
				time.Sleep(time.Millisecond * time.Duration(100+id*50))
				ch <- id*10 + j
			}
			close(ch)
		}(channels[i], i)
	}
	
	// Use reflect to select from multiple channels
	openChannels := 3
	for openChannels > 0 {
		select {
		case val, ok := <-channels[0]:
			if ok {
				fmt.Printf("Channel 0: %d\n", val)
			} else {
				openChannels--
				channels[0] = nil
			}
		case val, ok := <-channels[1]:
			if ok {
				fmt.Printf("Channel 1: %d\n", val)
			} else {
				openChannels--
				channels[1] = nil
			}
		case val, ok := <-channels[2]:
			if ok {
				fmt.Printf("Channel 2: %d\n", val)
			} else {
				openChannels--
				channels[2] = nil
			}
		}
	}
}

// Select with context cancellation
func selectWithContext() {
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()
	
	ch1 := make(chan string)
	ch2 := make(chan string)
	
	// Start producers
	go func() {
		for i := 0; i < 5; i++ {
			select {
			case ch1 <- fmt.Sprintf("message %d", i):
			case <-ctx.Done():
				fmt.Println("Producer 1 cancelled")
				return
			}
			time.Sleep(time.Millisecond * 200)
		}
	}()
	
	go func() {
		for i := 0; i < 5; i++ {
			select {
			case ch2 <- fmt.Sprintf("data %d", i):
			case <-ctx.Done():
				fmt.Println("Producer 2 cancelled")
				return
			}
			time.Sleep(time.Millisecond * 300)
		}
	}()
	
	// Consumer with context
	for i := 0; i < 8; i++ {
		select {
		case msg := <-ch1:
			fmt.Printf("From ch1: %s\n", msg)
		case msg := <-ch2:
			fmt.Printf("From ch2: %s\n", msg)
		case <-ctx.Done():
			fmt.Printf("Consumer cancelled: %v\n", ctx.Err())
			return
		}
	}
	
	// Cancel after some time
	go func() {
		time.Sleep(time.Second * 1)
		cancel()
	}()
}

// Fan-in pattern using select
func fanInWithSelect() {
	ch1 := make(chan string)
	ch2 := make(chan string)
	
	// Start producers
	go func() {
		for i := 0; i < 3; i++ {
			ch1 <- fmt.Sprintf("Producer 1 - Message %d", i)
			time.Sleep(time.Millisecond * 200)
		}
		close(ch1)
	}()
	
	go func() {
		for i := 0; i < 3; i++ {
			ch2 <- fmt.Sprintf("Producer 2 - Message %d", i)
			time.Sleep(time.Millisecond * 300)
		}
		close(ch2)
	}()
	
	// Fan-in consumer
	for {
		select {
		case msg, ok := <-ch1:
			if !ok {
				ch1 = nil
			} else {
				fmt.Printf("Received: %s\n", msg)
			}
		case msg, ok := <-ch2:
			if !ok {
				ch2 = nil
			} else {
				fmt.Printf("Received: %s\n", msg)
			}
		}
		
		if ch1 == nil && ch2 == nil {
			break
		}
	}
}

// Priority channels with select
func priorityChannels() {
	highPriority := make(chan string, 5)
	mediumPriority := make(chan string, 5)
	lowPriority := make(chan string, 5)
	
	// Send messages to different priority channels
	go func() {
		time.Sleep(time.Millisecond * 100)
		highPriority <- "High priority task 1"
	}()
	
	go func() {
		time.Sleep(time.Millisecond * 200)
		mediumPriority <- "Medium priority task 1"
	}()
	
	go func() {
		time.Sleep(time.Millisecond * 150)
		lowPriority <- "Low priority task 1"
	}()
	
	go func() {
		time.Sleep(time.Millisecond * 300)
		highPriority <- "High priority task 2"
	}()
	
	// Process with priority (high first, then medium, then low)
	for {
		select {
		case task := <-highPriority:
			fmt.Printf("HIGH: %s\n", task)
		case task := <-mediumPriority:
			fmt.Printf("MEDIUM: %s\n", task)
		case task := <-lowPriority:
			fmt.Printf("LOW: %s\n", task)
		default:
			// No tasks available
			if len(highPriority) == 0 && len(mediumPriority) == 0 && len(lowPriority) == 0 {
				fmt.Println("All tasks completed")
				return
			}
			time.Sleep(time.Millisecond * 50)
		}
	}
}

// Command processing with select
type Command struct {
	Type string
	Data interface{}
}

func commandProcessing() {
	commands := make(chan Command, 10)
	responses := make(chan string, 10)
	stop := make(chan struct{})
	
	// Command processor
	go func() {
		for {
			select {
			case cmd := <-commands:
				response := processCommand(cmd)
				responses <- response
			case <-stop:
				fmt.Println("Command processor stopping...")
				return
			}
		}
	}()
	
	// Send commands
	commands <- Command{Type: "echo", Data: "Hello"}
	commands <- Command{Type: "add", Data: []int{5, 3}}
	commands <- Command{Type: "multiply", Data: []int{4, 6}}
	commands <- Command{Type: "unknown", Data: "test"}
	
	// Process responses
	for i := 0; i < 4; i++ {
		select {
		case response := <-responses:
			fmt.Printf("Response: %s\n", response)
		case <-time.After(time.Second * 2):
			fmt.Println("Timeout waiting for response")
		}
	}
	
	// Stop the processor
	close(stop)
}

func processCommand(cmd Command) string {
	switch cmd.Type {
	case "echo":
		if str, ok := cmd.Data.(string); ok {
			return fmt.Sprintf("Echo: %s", str)
		}
	case "add":
		if nums, ok := cmd.Data.([]int); ok && len(nums) == 2 {
			return fmt.Sprintf("Sum: %d", nums[0]+nums[1])
		}
	case "multiply":
		if nums, ok := cmd.Data.([]int); ok && len(nums) == 2 {
			return fmt.Sprintf("Product: %d", nums[0]*nums[1])
		}
	default:
		return fmt.Sprintf("Unknown command: %s", cmd.Type)
	}
	
	return "Invalid command data"
}

// Advanced select patterns

// Select with random channel selection
func randomSelect(channels []chan int) <-chan int {
	output := make(chan int)
	
	go func() {
		defer close(output)
		
		for {
			// Randomly select a channel to check
			channelIndex := rand.Intn(len(channels))
			selected := channels[channelIndex]
			
			select {
			case val := <-selected:
				output <- val
				return
			default:
				// Try next channel
				allEmpty := true
				for _, ch := range channels {
					select {
					case val := <-ch:
						output <- val
						allEmpty = false
					default:
						continue
					}
					return
				}
				if allEmpty {
					return
				}
			}
		}
	}()
	
	return output
}

// Select for load balancing
func loadBalancer(channels []chan int, jobs []int) {
	for _, job := range jobs {
		// Find least loaded channel (simplified)
		selected := channels[job%len(channels)]
		
		select {
		case selected <- job:
			fmt.Printf("Sent job %d to channel\n", job)
		case <-time.After(time.Millisecond * 100):
			fmt.Printf("Failed to send job %d, channel busy\n", job)
		}
	}
}

// Select for heartbeat
func heartbeat(stop <-chan struct{}) <-chan time.Time {
	heartbeat := make(chan time.Time)
	
	go func() {
		defer close(heartbeat)
		
		ticker := time.NewTicker(time.Millisecond * 500)
		defer ticker.Stop()
		
		for {
			select {
			case <-ticker.C:
				heartbeat <- time.Now()
			case <-stop:
				return
			}
		}
	}()
	
	return heartbeat
}

// Select for race condition detection
func raceDetection(ch1, ch2 <-chan int) {
	for {
		select {
		case val := <-ch1:
			fmt.Printf("Channel 1 won: %d\n", val)
		case val := <-ch2:
			fmt.Printf("Channel 2 won: %d\n", val)
		}
	}
}

// Demonstrate advanced patterns
func demonstrateAdvancedSelectPatterns() {
	fmt.Println("\n--- Advanced Select Patterns ---")
	
	// Heartbeat pattern
	fmt.Println("\n--- Heartbeat Pattern ---")
	stop := make(chan struct{})
	hb := heartbeat(stop)
	
	for i := 0; i < 3; i++ {
		select {
		case beat := <-hb:
			fmt.Printf("Heartbeat: %v\n", beat.Format("15:04:05.000"))
		case <-time.After(time.Second):
			fmt.Println("Heartbeat missed!")
		}
	}
	
	close(stop)
	
	// Load balancing
	fmt.Println("\n--- Load Balancing ---")
	
	channels := []chan int{make(chan int, 1), make(chan int, 1), make(chan int, 1)}
	
	// Start workers
	for i, ch := range channels {
		go func(id int, ch <-chan int) {
			for job := range ch {
				fmt.Printf("Worker %d processing job %d\n", id, job)
				time.Sleep(time.Millisecond * 100)
			}
		}(i, ch)
	}
	
	jobs := []int{1, 2, 3, 4, 5, 6, 7, 8, 9}
	loadBalancer(channels, jobs)
	
	// Close channels
	for _, ch := range channels {
		close(ch)
	}
	
	time.Sleep(time.Second)
	
	// Race detection
	fmt.Println("\n--- Race Detection ---")
	
	raceCh1 := make(chan int)
	raceCh2 := make(chan int)
	
	go func() {
		time.Sleep(time.Millisecond * 100)
		raceCh1 <- 1
	}()
	
	go func() {
		time.Sleep(time.Millisecond * 150)
		raceCh2 <- 2
	}()
	
	raceDetection(raceCh1, raceCh2)
}

// Select for resource management
type Resource struct {
	id     int
	active bool
}

func resourcePool(resources []*Resource, requests <-chan int, results chan<- int) {
	for req := range requests {
		// Find available resource
		var selected *Resource
		for _, res := range resources {
			if !res.active {
				selected = res
				break
			}
		}
		
		if selected == nil {
			// Wait for resource to become available
			select {
			case <-time.After(time.Millisecond * 100):
				results <- -1 // Timeout
				continue
			}
		}
		
		// Allocate resource
		selected.active = true
		
		// Process request
		go func(res *Resource, request int) {
			defer func() {
				res.active = false
			}()
			
			time.Sleep(time.Millisecond * 50)
			results <- request * 2
		}(selected, req)
	}
}

func demonstrateResourceManagement() {
	fmt.Println("\n--- Resource Management ---")
	
	resources := []*Resource{
		{id: 1, active: false},
		{id: 2, active: false},
		{id: 3, active: false},
	}
	
	requests := make(chan int, 10)
	results := make(chan int, 10)
	
	go resourcePool(resources, requests, results)
	
	// Send requests
	for i := 1; i <= 8; i++ {
		requests <- i
	}
	
	// Collect results
	for i := 0; i < 8; i++ {
		select {
		case result := <-results:
			if result == -1 {
				fmt.Printf("Request timed out\n")
			} else {
				fmt.Printf("Request result: %d\n", result)
			}
		case <-time.After(time.Millisecond * 200):
			fmt.Printf("Timeout waiting for result\n")
		}
	}
	
	close(requests)
}
