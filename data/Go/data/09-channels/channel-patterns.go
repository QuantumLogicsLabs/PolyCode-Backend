package main

import (
	"fmt"
	"math/rand"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Advanced Channel Patterns ===")
	
	// Fan-in pattern
	fmt.Println("\n--- Fan-in Pattern ---")
	fanInPattern()
	
	// Fan-out pattern
	fmt.Println("\n--- Fan-out Pattern ---")
	fanOutPattern()
	
	// Pipeline pattern
	fmt.Println("\n--- Pipeline Pattern ---")
	pipelinePattern()
	
	// Worker pool with channels
	fmt.Println("\n--- Worker Pool with Channels ---")
	workerPoolChannels()
	
	// Timeout pattern
	fmt.Println("\n--- Timeout Pattern ---")
	timeoutPattern()
	
	// Context cancellation with channels
	fmt.Println("\n--- Context Cancellation ---")
	contextCancellation()
	
	// Pub/Sub pattern
	fmt.Println("\n--- Publish/Subscribe Pattern ---")
	pubSubPattern()
	
	// Rate limiting with channels
	fmt.Println("\n--- Rate Limiting ---")
	rateLimitingPattern()
	
	// Generator pattern
	fmt.Println("\n--- Generator Pattern ---")
	generatorPattern()
}

// Fan-in pattern: multiple channels to one
func fanInPattern() {
	// Create multiple input channels
	ch1 := make(chan string)
	ch2 := make(chan string)
	ch3 := make(chan string)
	
	// Start generators
	go generator("Generator 1", ch1, 3)
	go generator("Generator 2", ch2, 3)
	go generator("Generator 3", ch3, 3)
	
	// Fan-in function
	output := fanIn(ch1, ch2, ch3)
	
	// Consume from fan-in channel
	for i := 0; i < 9; i++ {
		msg := <-output
		fmt.Printf("Received: %s\n", msg)
	}
}

func generator(name string, ch chan<- string, count int) {
	for i := 0; i < count; i++ {
		ch <- fmt.Sprintf("%s - Message %d", name, i+1)
	}
}

func fanIn(channels ...<-chan string) <-chan string {
	output := make(chan string)
	var wg sync.WaitGroup
	
	for _, ch := range channels {
		wg.Add(1)
		go func(c <-chan string) {
			defer wg.Done()
			for msg := range c {
				output <- msg
			}
		}(ch)
	}
	
	go func() {
		wg.Wait()
		close(output)
	}()
	
	return output
}

// Fan-out pattern: one channel to multiple
func fanOutPattern() {
	input := make(chan int, 10)
	
	// Fill input channel
	go func() {
		for i := 1; i <= 10; i++ {
			input <- i
		}
		close(input)
	}()
	
	// Start multiple workers
	var wg sync.WaitGroup
	for i := 1; i <= 3; i++ {
		wg.Add(1)
		go worker(i, input, &wg)
	}
	
	wg.Wait()
}

func worker(id int, input <-chan int, wg *sync.WaitGroup) {
	defer wg.Done()
	
	for num := range input {
		result := num * num
		fmt.Printf("Worker %d: %d squared = %d\n", id, num, result)
		time.Sleep(time.Millisecond * time.Duration(rand.Intn(100)))
	}
}

// Pipeline pattern
func pipelinePattern() {
	// Stage 1: Generate numbers
	numbers := generateNumbers(1, 10)
	
	// Stage 2: Square numbers
	squares := squareNumbers(numbers)
	
	// Stage 3: Filter even squares
	evenSquares := filterEven(squares)
	
	// Consume final results
	for result := range evenSquares {
		fmt.Printf("Pipeline result: %d\n", result)
	}
}

func generateNumbers(start, end int) <-chan int {
	out := make(chan int)
	
	go func() {
		for i := start; i <= end; i++ {
			out <- i
		}
		close(out)
	}()
	
	return out
}

func squareNumbers(input <-chan int) <-chan int {
	out := make(chan int)
	
	go func() {
		for num := range input {
			out <- num * num
		}
		close(out)
	}()
	
	return out
}

func filterEven(input <-chan int) <-chan int {
	out := make(chan int)
	
	go func() {
		for num := range input {
			if num%2 == 0 {
				out <- num
			}
		}
		close(out)
	}()
	
	return out
}

// Worker pool with channels
func workerPoolChannels() {
	const numWorkers = 3
	const numJobs = 10
	
	jobs := make(chan int, numJobs)
	results := make(chan int, numJobs)
	
	// Start workers
	var wg sync.WaitGroup
	for i := 1; i <= numWorkers; i++ {
		wg.Add(1)
		go poolWorker(i, jobs, results, &wg)
	}
	
	// Send jobs
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
		fmt.Printf("Job result: %d\n", result)
	}
}

func poolWorker(id int, jobs <-chan int, results chan<- int, wg *sync.WaitGroup) {
	defer wg.Done()
	
	for job := range jobs {
		fmt.Printf("Worker %d processing job %d\n", id, job)
		time.Sleep(time.Millisecond * 100)
		results <- job * 2
	}
}

// Timeout pattern
func timeoutPattern() {
	ch := make(chan string)
	
	// Start a slow operation
	go func() {
		time.Sleep(time.Second * 2)
		ch <- "operation completed"
	}()
	
	select {
	case msg := <-ch:
		fmt.Printf("Received: %s\n", msg)
	case <-time.After(time.Second * 1):
		fmt.Println("Operation timed out")
	}
	
	// Timeout with context
	fmt.Println("\n--- Timeout with Context ---")
	ctx, cancel := context.WithTimeout(context.Background(), time.Second*1)
	defer cancel()
	
	ch2 := make(chan string)
	go func() {
		time.Sleep(time.Second * 2)
		select {
		case ch2 <- "too late":
		case <-ctx.Done():
			return
		}
	}()
	
	select {
	case msg := <-ch2:
		fmt.Printf("Received: %s\n", msg)
	case <-ctx.Done():
		fmt.Printf("Context timeout: %v\n", ctx.Err())
	}
}

// Context cancellation with channels
func contextCancellation() {
	ctx, cancel := context.WithCancel(context.Background())
	
	// Start a goroutine that respects cancellation
	ch := make(chan int)
	
	go func(ctx context.Context) {
		for i := 0; i < 10; i++ {
			select {
			case <-ctx.Done():
				fmt.Printf("Goroutine cancelled: %v\n", ctx.Err())
				return
			default:
				ch <- i
				time.Sleep(time.Millisecond * 200)
			}
		}
		close(ch)
	}(ctx)
	
	// Read some values then cancel
	for i := 0; i < 3; i++ {
		val := <-ch
		fmt.Printf("Received: %d\n", val)
	}
	
	fmt.Println("Cancelling goroutine...")
	cancel()
	
	// Try to read more (channel should be closed)
	for val := range ch {
		fmt.Printf("After cancel: %d\n", val)
	}
}

// Publish/Subscribe pattern
type PubSub struct {
	subscribers map[chan<- string]bool
	mu          sync.RWMutex
}

func NewPubSub() *PubSub {
	return &PubSub{
		subscribers: make(map[chan<- string]bool),
	}
}

func (ps *PubSub) Subscribe() <-chan string {
	ch := make(chan string, 10)
	
	ps.mu.Lock()
	ps.subscribers[ch] = true
	ps.mu.Unlock()
	
	return ch
}

func (ps *PubSub) Unsubscribe(ch <-chan string) {
	ps.mu.Lock()
	delete(ps.subscribers, ch)
	ps.mu.Unlock()
	close(ch)
}

func (ps *PubSub) Publish(message string) {
	ps.mu.RLock()
	defer ps.mu.RUnlock()
	
	for ch := range ps.subscribers {
		select {
		case ch <- message:
		default:
			// Subscriber is slow, skip
		}
	}
}

func pubSubPattern() {
	pubsub := NewPubSub()
	
	// Subscribe multiple readers
	sub1 := pubsub.Subscribe()
	sub2 := pubsub.Subscribe()
	sub3 := pubsub.Subscribe()
	
	// Start readers
	var wg sync.WaitGroup
	wg.Add(3)
	
	go func() {
		defer wg.Done()
		for msg := range sub1 {
			fmt.Printf("Subscriber 1: %s\n", msg)
		}
	}()
	
	go func() {
		defer wg.Done()
		for msg := range sub2 {
			fmt.Printf("Subscriber 2: %s\n", msg)
		}
	}()
	
	go func() {
		defer wg.Done()
		for msg := range sub3 {
			fmt.Printf("Subscriber 3: %s\n", msg)
		}
	}()
	
	// Publish messages
	messages := []string{"Hello", "World", "Go", "Channels", "Patterns"}
	for _, msg := range messages {
		pubsub.Publish(msg)
		time.Sleep(time.Millisecond * 100)
	}
	
	// Unsubscribe and close
	pubsub.Unsubscribe(sub1)
	pubsub.Unsubscribe(sub2)
	pubsub.Unsubscribe(sub3)
	
	wg.Wait()
}

// Rate limiting with channels
func rateLimitingPattern() {
	// Simple rate limiter
	limiter := time.Tick(time.Millisecond * 200)
	
	requests := []string{"req1", "req2", "req3", "req4", "req5"}
	
	for _, req := range requests {
		<-limiter // Wait for rate limit token
		fmt.Printf("Processing: %s\n", req)
	}
	
	// Burst rate limiter
	fmt.Println("\n--- Burst Rate Limiter ---")
	burstLimiter := make(chan time.Time, 3)
	
	// Fill burst limiter
	for i := 0; i < 3; i++ {
		burstLimiter <- time.Now()
	}
	
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
	for i := 0; i < 5; i++ {
		<-burstLimiter
		fmt.Printf("Burst request %d\n", i+1)
	}
}

// Generator pattern
func generatorPattern() {
	// Fibonacci generator
	fmt.Println("--- Fibonacci Generator ---")
	fib := fibonacciGenerator(10)
	
	for i := 0; i < 10; i++ {
		fmt.Printf("Fibonacci %d: %d\n", i+1, <-fib)
	}
	
	// Prime number generator
	fmt.Println("\n--- Prime Number Generator ---")
	primes := primeGenerator()
	
	for i := 0; i < 10; i++ {
		fmt.Printf("Prime %d: %d\n", i+1, <-primes)
	}
}

func fibonacciGenerator(count int) <-chan int {
	out := make(chan int)
	
	go func() {
		defer close(out)
		
		a, b := 0, 1
		for i := 0; i < count; i++ {
			out <- a
			a, b = b, a+b
		}
	}()
	
	return out
}

func primeGenerator() <-chan int {
	out := make(chan int)
	
	go func() {
		defer close(out)
		
		num := 2
		for {
			if isPrime(num) {
				out <- num
			}
			num++
		}
	}()
	
	return out
}

func isPrime(n int) bool {
	if n <= 1 {
		return false
	}
	if n <= 3 {
		return true
	}
	if n%2 == 0 || n%3 == 0 {
		return false
	}
	
	i := 5
	w := 2
	for i*i <= n {
		if n%i == 0 {
			return false
		}
		i += w
		w = 6 - w
	}
	
	return true
}

// Advanced channel patterns

// Tee pattern: split channel into multiple
func tee(input <-chan int) (<-chan int, <-chan int) {
	out1 := make(chan int)
	out2 := make(chan int)
	
	go func() {
		defer close(out1)
		defer close(out2)
		
		for val := range input {
			out1 <- val
			out2 <- val
		}
	}()
	
	return out1, out2
}

// Merge pattern: combine multiple channels
func merge(inputs ...<-chan int) <-chan int {
	output := make(chan int)
	var wg sync.WaitGroup
	
	for _, input := range inputs {
		wg.Add(1)
		go func(ch <-chan int) {
			defer wg.Done()
			for val := range ch {
				output <- val
			}
		}(input)
	}
	
	go func() {
		wg.Wait()
		close(output)
	}()
	
	return output
}

// Buffer pattern: control flow rate
func buffer(input <-chan int, size int) <-chan int {
	output := make(chan int, size)
	
	go func() {
		defer close(output)
		
		for val := range input {
			output <- val
		}
	}()
	
	return output
}

// Throttle pattern: limit processing rate
func throttle(input <-chan int, rate time.Duration) <-chan int {
	output := make(chan int)
	ticker := time.NewTicker(rate)
	defer ticker.Stop()
	
	go func() {
		defer close(output)
		
		for {
			select {
			case val, ok := <-input:
				if !ok {
					return
				}
				<-ticker
				output <- val
			case <-ticker.C:
				// Tick without value, continue
			}
		}
	}()
	
	return output
}

// Debounce pattern: only pass through last value in time window
func debounce(input <-chan int, duration time.Duration) <-chan int {
	output := make(chan int)
	
	go func() {
		defer close(output)
		
		var timer *time.Timer
		var value int
		
		for {
			select {
			case val, ok := <-input:
				if !ok {
					return
				}
				
				value = val
				
				if timer != nil {
					timer.Stop()
				}
				
				timer = time.AfterFunc(duration, func() {
					output <- value
				})
			}
		}
	}()
	
	return output
}

// Demonstrate advanced patterns
func demonstrateAdvancedPatterns() {
	fmt.Println("\n--- Advanced Channel Patterns ---")
	
	// Tee pattern
	fmt.Println("\n--- Tee Pattern ---")
	source := make(chan int)
	go func() {
		defer close(source)
		for i := 1; i <= 5; i++ {
			source <- i
			time.Sleep(time.Millisecond * 100)
		}
	}()
	
	out1, out2 := tee(source)
	
	var wg sync.WaitGroup
	wg.Add(2)
	
	go func() {
		defer wg.Done()
		for val := range out1 {
			fmt.Printf("Output 1: %d\n", val)
		}
	}()
	
	go func() {
		defer wg.Done()
		for val := range out2 {
			fmt.Printf("Output 2: %d\n", val)
		}
	}()
	
	wg.Wait()
	
	// Throttle pattern
	fmt.Println("\n--- Throttle Pattern ---")
	inputs := make(chan int)
	go func() {
		defer close(inputs)
		for i := 1; i <= 10; i++ {
			inputs <- i
			time.Sleep(time.Millisecond * 50)
		}
	}()
	
	throttled := throttle(inputs, time.Millisecond*200)
	
	for val := range throttled {
		fmt.Printf("Throttled: %d\n", val)
	}
	
	// Debounce pattern
	fmt.Println("\n--- Debounce Pattern ---")
	debounceInput := make(chan int)
	go func() {
		defer close(debounceInput)
		for i := 1; i <= 10; i++ {
			debounceInput <- i
			time.Sleep(time.Millisecond * 50)
		}
	}()
	
	debounced := debounce(debounceInput, time.Millisecond*200)
	
	for val := range debounced {
		fmt.Printf("Debounced: %d\n", val)
	}
}

// Channel-based state machine
type State int

const (
	StateIdle State = iota
	StateActive
	StatePaused
	StateStopped
)

type StateMachine struct {
	current State
	states  chan State
	events  chan string
}

func NewStateMachine() *StateMachine {
	sm := &StateMachine{
		current: StateIdle,
		states:  make(chan State, 10),
		events:  make(chan string, 10),
	}
	
	go sm.run()
	return sm
}

func (sm *StateMachine) run() {
	for {
		select {
		case state := <-sm.states:
			sm.current = state
			fmt.Printf("State changed to: %v\n", sm.current)
		case event := <-sm.events:
			sm.handleEvent(event)
		}
	}
}

func (sm *StateMachine) handleEvent(event string) {
	switch sm.current {
	case StateIdle:
		if event == "start" {
			sm.states <- StateActive
		}
	case StateActive:
		if event == "pause" {
			sm.states <- StatePaused
		} else if event == "stop" {
			sm.states <- StateStopped
		}
	case StatePaused:
		if event == "resume" {
			sm.states <- StateActive
		} else if event == "stop" {
			sm.states <- StateStopped
		}
	case StateStopped:
		if event == "start" {
			sm.states <- StateActive
		}
	}
}

func (sm *StateMachine) SendEvent(event string) {
	sm.events <- event
}

func (sm *StateMachine) GetCurrent() State {
	return sm.current
}

func demonstrateStateMachine() {
	fmt.Println("\n--- State Machine Pattern ---")
	
	sm := NewStateMachine()
	
	events := []string{"start", "pause", "resume", "pause", "stop", "start"}
	for _, event := range events {
		sm.SendEvent(event)
		time.Sleep(time.Millisecond * 100)
	}
	
	time.Sleep(time.Millisecond * 500)
}
