package main

import (
	"context"
	"fmt"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Advanced Channel Techniques ===")
	
	// Buffered channels for flow control
	fmt.Println("\n--- Buffered Channels for Flow Control ---")
	bufferedFlowControl()
	
	// Channel closing patterns
	fmt.Println("\n--- Channel Closing Patterns ---")
	channelClosing()
	
	// Channel as semaphore
	fmt.Println("\n--- Channel as Semaphore ---")
	channelSemaphore()
	
	// Channel for rate limiting
	fmt.Println("\n--- Channel for Rate Limiting ---")
	channelRateLimiting()
	
	// Channel for work distribution
	fmt.Println("\n--- Work Distribution ---")
	workDistribution()
	
	// Channel for batching
	fmt.Println("\n--- Batching with Channels ---")
	batchingChannels()
	
	// Channel for timeouts and deadlines
	fmt.Println("\n--- Timeouts and Deadlines ---")
	timeoutChannels()
	
	// Channel for streaming
	fmt.Println("\n--- Streaming with Channels ---")
	streamingChannels()
	
	// Channel for coordination
	fmt.Println("\n--- Coordination Patterns ---")
	coordinationChannels()
}

// Buffered channels for flow control
func bufferedFlowControl() {
	// Producer-consumer with buffering
	fmt.Println("Producer-Consumer with Buffer:")
	
	// Small buffer to demonstrate backpressure
	ch := make(chan int, 2)
	
	// Producer
	go func() {
		for i := 1; i <= 10; i++ {
			fmt.Printf("Producing %d\n", i)
			ch <- i
			fmt.Printf("Produced %d, buffer size: %d\n", i, len(ch))
		}
		close(ch)
	}()
	
	// Consumer (slower)
	for val := range ch {
		fmt.Printf("Consuming %d\n", val)
		time.Sleep(time.Millisecond * 200) // Slower than producer
	}
	
	// Large buffer for burst handling
	fmt.Println("\nLarge Buffer for Burst Handling:")
	burstCh := make(chan int, 100)
	
	// Fast producer (burst)
	go func() {
		for i := 1; i <= 50; i++ {
			burstCh <- i
		}
		close(burstCh)
	}()
	
	// Slow consumer
	for val := range burstCh {
		fmt.Printf("Burst consumed: %d\n", val)
		time.Sleep(time.Millisecond * 10)
	}
}

// Channel closing patterns
func channelClosing() {
	// Pattern 1: Explicit close
	fmt.Println("Explicit Close Pattern:")
	ch1 := make(chan string, 3)
	
	go func() {
		ch1 <- "message 1"
		ch1 <- "message 2"
		ch1 <- "message 3"
		close(ch1) // Explicit close
	}()
	
	for msg := range ch1 {
		fmt.Printf("Received: %s\n", msg)
	}
	
	// Pattern 2: Close with defer
	fmt.Println("\nDefer Close Pattern:")
	ch2 := make(chan int)
	
	go func() {
		defer close(ch2) // Ensure close on exit
		for i := 1; i <= 5; i++ {
			ch2 <- i
			if i == 3 {
				return // Early exit, defer ensures close
			}
		}
	}()
	
	for val := range ch2 {
		fmt.Printf("Value: %d\n", val)
	}
	
	// Pattern 3: Close detection with comma ok
	fmt.Println("\nClose Detection Pattern:")
	ch3 := make(chan string)
	
	go func() {
		time.Sleep(time.Millisecond * 100)
		ch3 <- "first"
		time.Sleep(time.Millisecond * 100)
		ch3 <- "second"
		close(ch3)
	}()
	
	for {
		msg, ok := <-ch3
		if !ok {
			fmt.Println("Channel closed")
			break
		}
		fmt.Printf("Got: %s\n", msg)
	}
	
	// Pattern 4: Multiple channels, close all
	fmt.Println("\nMultiple Channels Close:")
	channels := make([]chan int, 3)
	for i := range channels {
		channels[i] = make(chan int, 2)
	}
	
	// Producers
	for i, ch := range channels {
		go func(id int, c chan<- int) {
			defer close(c)
			for j := 0; j < 2; j++ {
				c <- id*10 + j
			}
		}(i, ch)
	}
	
	// Consumer with close detection
	openChannels := 3
	for openChannels > 0 {
		for i, ch := range channels {
			select {
			case val, ok := <-ch:
				if ok {
					fmt.Printf("Channel %d: %d\n", i, val)
				} else {
					fmt.Printf("Channel %d closed\n", i)
					channels[i] = nil
					openChannels--
				}
			default:
				// No data on this channel
			}
		}
	}
}

// Channel as semaphore
func channelSemaphore() {
	const maxConcurrency = 3
	semaphore := make(chan struct{}, maxConcurrency)
	
	var wg sync.WaitGroup
	tasks := []string{"Task A", "Task B", "Task C", "Task D", "Task E", "Task F", "Task G"}
	
	for _, task := range tasks {
		wg.Add(1)
		go func(t string) {
			defer wg.Done()
			
			// Acquire semaphore
			semaphore <- struct{}{}
			defer func() { <-semaphore }()
			
			fmt.Printf("Starting %s\n", t)
			time.Sleep(time.Millisecond * time.Duration(200+rand.Intn(300)))
			fmt.Printf("Completed %s\n", t)
		}(task)
	}
	
	wg.Wait()
}

// Channel for rate limiting
func channelRateLimiting() {
	// Simple rate limiter
	rateLimiter := time.Tick(time.Millisecond * 200)
	
	fmt.Println("Rate Limited Requests:")
	requests := []string{"req1", "req2", "req3", "req4", "req5"}
	
	for _, req := range requests {
		<-rateLimiter // Wait for rate limit token
		fmt.Printf("Processing: %s\n", req)
	}
	
	// Burst rate limiter
	fmt.Println("\nBurst Rate Limiter:")
	burstLimiter := make(chan time.Time, 5)
	
	// Fill burst limiter
	for i := 0; i < 5; i++ {
		burstLimiter <- time.Now()
	}
	
	// Refill burst limiter
	go func() {
		ticker := time.NewTicker(time.Millisecond * 200)
		defer ticker.Stop()
		
		for range ticker.C {
			select {
			case burstLimiter <- time.Now():
			default:
				// Channel full, skip
			}
		}
	}()
	
	// Process burst requests
	for i := 0; i < 8; i++ {
		<-burstLimiter
		fmt.Printf("Burst request %d\n", i+1)
	}
}

// Work distribution with channels
func workDistribution() {
	const numWorkers = 3
	const numJobs = 12
	
	jobs := make(chan int, numJobs)
	results := make(chan string, numJobs)
	
	// Start workers
	var wg sync.WaitGroup
	for i := 1; i <= numWorkers; i++ {
		wg.Add(1)
		go worker(i, jobs, results, &wg)
	}
	
	// Distribute work
	go func() {
		for j := 1; j <= numJobs; j++ {
			jobs <- j
		}
		close(jobs)
	}()
	
	// Wait for workers and close results
	go func() {
		wg.Wait()
		close(results)
	}()
	
	// Collect results
	for result := range results {
		fmt.Printf("Result: %s\n", result)
	}
}

func worker(id int, jobs <-chan int, results chan<- string, wg *sync.WaitGroup) {
	defer wg.Done()
	
	for job := range jobs {
		start := time.Now()
		
		// Simulate work
		time.Sleep(time.Millisecond * time.Duration(100+rand.Intn(200)))
		
		duration := time.Since(start)
		results <- fmt.Sprintf("Worker %d completed job %d in %v", id, job, duration)
	}
}

// Batching with channels
func batchingChannels() {
	const batchSize = 5
	const numItems = 23
	
	items := make(chan int, numItems)
	batches := make(chan []int, (numItems+batchSize-1)/batchSize)
	
	// Producer
	go func() {
		defer close(items)
		for i := 1; i <= numItems; i++ {
			items <- i
		}
	}()
	
	// Batcher
	go func() {
		defer close(batches)
		
		batch := make([]int, 0, batchSize)
		
		for item := range items {
			batch = append(batch, item)
			
			if len(batch) == batchSize {
				batches <- batch
				batch = make([]int, 0, batchSize)
			}
		}
		
		// Send remaining items
		if len(batch) > 0 {
			batches <- batch
		}
	}()
	
	// Consumer
	for batch := range batches {
		fmt.Printf("Processing batch: %v\n", batch)
		time.Sleep(time.Millisecond * 100)
	}
}

// Timeouts and deadlines with channels
func timeoutChannels() {
	// Operation with timeout
	fmt.Println("Operation with Timeout:")
	
	result := make(chan string, 1)
	
	go func() {
		time.Sleep(time.Second * 2) // Simulate slow operation
		result <- "operation completed"
	}()
	
	select {
	case res := <-result:
		fmt.Printf("Success: %s\n", res)
	case <-time.After(time.Second * 1):
		fmt.Println("Operation timed out")
	}
	
	// Operation with deadline
	fmt.Println("\nOperation with Deadline:")
	
	deadline := time.Now().Add(time.Second * 3)
	deadlineResult := make(chan string, 1)
	
	go func() {
		time.Sleep(time.Second * 4) // Will exceed deadline
		deadlineResult <- "deadline operation completed"
	}()
	
	select {
	case res := <-deadlineResult:
		fmt.Printf("Success: %s\n", res)
	case <-time.After(time.Until(deadline)):
		fmt.Println("Operation exceeded deadline")
	}
	
	// Context with timeout
	fmt.Println("\nContext with Timeout:")
	
	ctx, cancel := context.WithTimeout(context.Background(), time.Second*2)
	defer cancel()
	
	contextResult := make(chan string, 1)
	
	go func() {
		time.Sleep(time.Second * 3) // Will exceed context timeout
		contextResult <- "context operation completed"
	}()
	
	select {
	case res := <-contextResult:
		fmt.Printf("Success: %s\n", res)
	case <-ctx.Done():
		fmt.Printf("Context timeout: %v\n", ctx.Err())
	}
}

// Streaming with channels
func streamingChannels() {
	// Data stream generator
	fmt.Println("Data Stream:")
	
	stream := make(chan int)
	
	go func() {
		defer close(stream)
		
		for i := 1; i <= 20; i++ {
			stream <- i
			time.Sleep(time.Millisecond * 50)
		}
	}()
	
	// Stream processor
	processed := make(chan int)
	
	go func() {
		defer close(processed)
		
		for value := range stream {
			processed <- value * value
		}
	}()
	
	// Stream consumer
	count := 0
	for value := range processed {
		fmt.Printf("Processed value: %d\n", value)
		count++
		if count >= 10 { // Stop after 10 values
			break
		}
	}
	
	// Infinite stream with cancellation
	fmt.Println("\nInfinite Stream with Cancellation:")
	
	ctx, cancel := context.WithCancel(context.Background())
	infinite := make(chan int)
	
	go func() {
		defer close(infinite)
		
		i := 0
		for {
			select {
			case <-ctx.Done():
				return
			default:
				infinite <- i
				i++
				time.Sleep(time.Millisecond * 100)
			}
		}
	}()
	
	// Consume some values then cancel
	for i := 0; i < 5; i++ {
		val := <-infinite
		fmt.Printf("Infinite stream value: %d\n", val)
	}
	
	cancel()
	
	// Try to read more (should get closed channel)
	for val := range infinite {
		fmt.Printf("After cancel: %d\n", val)
	}
}

// Coordination patterns with channels
func coordinationChannels() {
	// Barrier pattern
	fmt.Println("Barrier Pattern:")
	
	const numGoroutines = 3
	barrier := make(chan struct{}, numGoroutines)
	
	var wg sync.WaitGroup
	
	for i := 0; i < numGoroutines; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			
			fmt.Printf("Goroutine %d: Phase 1\n", id)
			time.Sleep(time.Millisecond * time.Duration(100+id*50))
			
			// Signal arrival at barrier
			barrier <- struct{}{}
			
			fmt.Printf("Goroutine %d: Waiting at barrier\n", id)
			
			// Wait for all goroutines
			<-barrier
			
			fmt.Printf("Goroutine %d: Phase 2\n", id)
		}(i)
	}
	
	wg.Wait()
	
	// Done channel pattern
	fmt.Println("\nDone Channel Pattern:")
	
	done := make(chan struct{})
	
	go func() {
		defer close(done)
		
		fmt.Println("Worker starting...")
		time.Sleep(time.Second * 2)
		fmt.Println("Worker finished")
	}()
	
	// Wait for completion
	<-done
	fmt.Println("Main goroutine continuing")
	
	// Quit channel pattern
	fmt.Println("\nQuit Channel Pattern:")
	
	quit := make(chan struct{})
	data := make(chan int)
	
	// Producer
	go func() {
		for i := 1; i <= 10; i++ {
			select {
			case data <- i:
				fmt.Printf("Produced: %d\n", i)
			case <-quit:
				fmt.Println("Producer quitting")
				return
			}
			time.Sleep(time.Millisecond * 100)
		}
	}()
	
	// Consumer
	go func() {
		for i := 0; i < 5; i++ {
			val := <-data
			fmt.Printf("Consumed: %d\n", val)
			time.Sleep(time.Millisecond * 150)
		}
		fmt.Println("Consumer sending quit signal")
		quit <- struct{}{}
	}()
	
	// Let them run
	time.Sleep(time.Second * 2)
}

// Advanced channel techniques

// Channel for implementing a queue
type ChannelQueue struct {
	items chan interface{}
	mu    sync.Mutex
}

func NewChannelQueue(size int) *ChannelQueue {
	return &ChannelQueue{
		items: make(chan interface{}, size),
	}
}

func (q *ChannelQueue) Enqueue(item interface{}) {
	q.items <- item
}

func (q *ChannelQueue) Dequeue() interface{} {
	return <-q.items
}

func (q *ChannelQueue) TryDequeue() (interface{}, bool) {
	select {
	case item := <-q.items:
		return item, true
	default:
		return nil, false
	}
}

// Channel for implementing a stack
type ChannelStack struct {
	items chan interface{}
}

func NewChannelStack(size int) *ChannelStack {
	return &ChannelStack{
		items: make(chan interface{}, size),
	}
}

func (s *ChannelStack) Push(item interface{}) {
	s.items <- item
}

func (s *ChannelStack) Pop() interface{} {
	// Read all items and put back all except the last
	var items []interface{}
	
	for item := range s.items {
		items = append(items, item)
	}
	
	// Put back all except the last one
	for i := 0; i < len(items)-1; i++ {
		s.items <- items[i]
	}
	
	if len(items) > 0 {
		return items[len(items)-1]
	}
	
	return nil
}

// Channel for implementing a priority queue
type PriorityQueueItem struct {
	Value    interface{}
	Priority int
}

type ChannelPriorityQueue struct {
	items chan PriorityQueueItem
}

func NewChannelPriorityQueue(size int) *ChannelPriorityQueue {
	return &ChannelPriorityQueue{
		items: make(chan PriorityQueueItem, size),
	}
}

func (pq *ChannelPriorityQueue) Enqueue(item interface{}, priority int) {
	pq.items <- PriorityQueueItem{Value: item, Priority: priority}
}

func (pq *ChannelPriorityQueue) Dequeue() interface{} {
	// Find highest priority item
	var highest PriorityQueueItem
	var hasItem bool
	
	// Read all items
	var items []PriorityQueueItem
	for item := range pq.items {
		items = append(items, item)
		if !hasItem || item.Priority > highest.Priority {
			highest = item
			hasItem = true
		}
	}
	
	// Put back all except the highest priority
	for _, item := range items {
		if item != highest {
			pq.items <- item
		}
	}
	
	if hasItem {
		return highest.Value
	}
	
	return nil
}

// Demonstrate advanced channel structures
func demonstrateAdvancedStructures() {
	fmt.Println("\n--- Advanced Channel Structures ---")
	
	// Queue
	fmt.Println("Channel Queue:")
	queue := NewChannelQueue(5)
	
	go func() {
		queue.Enqueue("A")
		queue.Enqueue("B")
		queue.Enqueue("C")
	}()
	
	for i := 0; i < 3; i++ {
		item := queue.Dequeue()
		fmt.Printf("Dequeued: %v\n", item)
	}
	
	// Stack
	fmt.Println("\nChannel Stack:")
	stack := NewChannelStack(5)
	
	go func() {
		stack.Push("X")
		stack.Push("Y")
		stack.Push("Z")
	}()
	
	for i := 0; i < 3; i++ {
		item := stack.Pop()
		fmt.Printf("Popped: %v\n", item)
	}
	
	// Priority Queue
	fmt.Println("\nChannel Priority Queue:")
	pq := NewChannelPriorityQueue(10)
	
	go func() {
		pq.Enqueue("Low Priority", 1)
		pq.Enqueue("High Priority", 10)
		pq.Enqueue("Medium Priority", 5)
		pq.Enqueue("Urgent Priority", 15)
	}()
	
	for i := 0; i < 4; i++ {
		item := pq.Dequeue()
		fmt.Printf("Priority dequeued: %v\n", item)
	}
}

// Channel for implementing a ring buffer
type RingBuffer struct {
	data     chan interface{}
	capacity int
}

func NewRingBuffer(capacity int) *RingBuffer {
	return &RingBuffer{
		data:     make(chan interface{}, capacity),
		capacity: capacity,
	}
}

func (rb *RingBuffer) Write(item interface{}) {
	select {
	case rb.data <- item:
		// Normal write
	default:
		// Buffer full, remove oldest item
		<-rb.data
		rb.data <- item
	}
}

func (rb *RingBuffer) Read() interface{} {
	return <-rb.data
}

// Channel for implementing a broadcast system
type Broadcast struct {
	listeners map[chan<- string]bool
	mu        sync.RWMutex
}

func NewBroadcast() *Broadcast {
	return &Broadcast{
		listeners: make(map[chan<- string]bool),
	}
}

func (b *Broadcast) Subscribe() <-chan string {
	ch := make(chan string, 10)
	
	b.mu.Lock()
	b.listeners[ch] = true
	b.mu.Unlock()
	
	return ch
}

func (b *Broadcast) Unsubscribe(ch <-chan string) {
	b.mu.Lock()
	delete(b.listeners, ch)
	b.mu.Unlock()
	close(ch)
}

func (b *Broadcast) Broadcast(message string) {
	b.mu.RLock()
	defer b.mu.RUnlock()
	
	for ch := range b.listeners {
		select {
		case ch <- message:
		default:
			// Slow listener, skip
		}
	}
}

// Demonstrate ring buffer and broadcast
func demonstrateRingBufferAndBroadcast() {
	fmt.Println("\n--- Ring Buffer ---")
	
	rb := NewRingBuffer(3)
	
	// Write more items than capacity
	for i := 0; i < 5; i++ {
		rb.Write(fmt.Sprintf("Item %d", i))
		fmt.Printf("Wrote: Item %d\n", i)
	}
	
	// Read all items
	for i := 0; i < 3; i++ {
		item := rb.Read()
		fmt.Printf("Read: %v\n", item)
	}
	
	fmt.Println("\n--- Broadcast System ---")
	
	broadcast := NewBroadcast()
	
	// Subscribe multiple listeners
	listener1 := broadcast.Subscribe()
	listener2 := broadcast.Subscribe()
	listener3 := broadcast.Subscribe()
	
	// Start listeners
	var wg sync.WaitGroup
	wg.Add(3)
	
	go func() {
		defer wg.Done()
		for msg := range listener1 {
			fmt.Printf("Listener 1: %s\n", msg)
		}
	}()
	
	go func() {
		defer wg.Done()
		for msg := range listener2 {
			fmt.Printf("Listener 2: %s\n", msg)
		}
	}()
	
	go func() {
		defer wg.Done()
		for msg := range listener3 {
			fmt.Printf("Listener 3: %s\n", msg)
		}
	}()
	
	// Broadcast messages
	messages := []string{"Hello", "World", "Go", "Channels"}
	for _, msg := range messages {
		broadcast.Broadcast(msg)
		time.Sleep(time.Millisecond * 100)
	}
	
	// Unsubscribe and close
	broadcast.Unsubscribe(listener1)
	broadcast.Unsubscribe(listener2)
	broadcast.Unsubscribe(listener3)
	
	wg.Wait()
}
