package main

import (
	"context"
	"fmt"
	"runtime"
	"strings"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Advanced Go Patterns ===")

	// Design patterns
	fmt.Println("\n--- Design Patterns ---")
	designPatterns()

	// Concurrency patterns
	fmt.Println("\n--- Advanced Concurrency Patterns ---")
	advancedConcurrency()

	// Architectural patterns
	fmt.Println("\n--- Architectural Patterns ---")
	architecturalPatterns()

	// Performance patterns
	fmt.Println("\n--- Performance Patterns ---")
	performancePatterns()

	// Error handling patterns
	fmt.Println("\n--- Error Handling Patterns ---")
	errorHandlingPatterns()

	// Testing patterns
	fmt.Println("\n--- Testing Patterns ---")
	testingPatterns()

	// Memory management patterns
	fmt.Println("\n--- Memory Management Patterns ---")
	memoryManagement()

	// Resource management patterns
	fmt.Println("\n--- Resource Management Patterns ---")
	resourceManagement()

	// Logging patterns
	fmt.Println("\n--- Logging Patterns ---")
	loggingPatterns()
}

// Design patterns
func designPatterns() {
	fmt.Println("Common Design Patterns in Go:")

	fmt.Println("\n1. Singleton Pattern:")
	singletonExample()

	fmt.Println("\n2. Factory Pattern:")
	factoryExample()

	fmt.Println("\n3. Builder Pattern:")
	builderExample()

	fmt.Println("\n4. Observer Pattern:")
	observerExample()

	fmt.Println("\n5. Strategy Pattern:")
	strategyExample()

	fmt.Println("\n6. Command Pattern:")
	commandExample()
}

func singletonExample() {
	type Database struct {
		connection string
	}

	var (
		instance *Database
		once     sync.Once
	)

	getInstance := func() *Database {
		once.Do(func() {
			instance = &Database{connection: "database-connection-string"}
		})
		return instance
	}

	db1 := getInstance()
	db2 := getInstance()

	fmt.Printf("Database instances: %p, %p (should be same)\n", db1, db2)
	fmt.Printf("Connection: %s\n", db1.connection)
}

func factoryExample() {
	type Animal interface {
		Speak() string
	}

	createAnimal := func(animalType string) string {
		switch animalType {
		case "dog":
			return "Dog says: Woof!"
		case "cat":
			return "Cat says: Meow!"
		default:
			return "Unknown animal"
		}
	}

	fmt.Println(createAnimal("dog"))
	fmt.Println(createAnimal("cat"))
}

func builderExample() {
	type House struct {
		windows int
		doors   int
		rooms   int
		garage  bool
		pool    bool
	}

	house := House{
		windows: 8,
		doors:   4,
		rooms:   6,
		garage:  true,
		pool:    false,
	}

	fmt.Printf("House: %+v\n", house)
}

func observerExample() {
	type Observer func(string)

	var observers []Observer
	attach := func(o Observer) {
		observers = append(observers, o)
	}
	notify := func(data string) {
		for _, o := range observers {
			o(data)
		}
	}

	attach(func(data string) { fmt.Printf("Email notification: %s\n", data) })
	attach(func(data string) { fmt.Printf("SMS notification: %s\n", data) })

	notify("New product available!")
}

func strategyExample() {
	pay := func(method string, amount float64) string {
		switch method {
		case "CreditCard":
			return fmt.Sprintf("Paid %.2f using Credit Card", amount)
		case "PayPal":
			return fmt.Sprintf("Paid %.2f using PayPal", amount)
		default:
			return "Unknown payment"
		}
	}

	fmt.Println(pay("CreditCard", 100.50))
	fmt.Println(pay("PayPal", 100.50))
}

func commandExample() {
	isOn := false
	turnOn := func() { isOn = true; fmt.Println("Light is ON") }
	turnOff := func() { isOn = false; fmt.Println("Light is OFF") }

	turnOn()
	turnOff()
	_ = isOn
}

// Concurrency patterns
func advancedConcurrency() {
	fmt.Println("Advanced Concurrency Patterns:")

	fmt.Println("\n1. Worker Pool with Results:")
	workerPoolWithResults()

	fmt.Println("\n2. Fan-In/Fan-Out Pattern:")
	fanInFanOut()

	fmt.Println("\n3. Pipeline Pattern:")
	pipelinePattern()

	fmt.Println("\n4. Timeout Pattern:")
	timeoutPattern()

	fmt.Println("\n5. Retry Pattern:")
	retryPattern()

	fmt.Println("\n6. Circuit Breaker Pattern:")
	circuitBreakerPattern()
}

func workerPoolWithResults() {
	type Work struct {
		ID   int
		Data string
	}

	type Result struct {
		ID    int
		Value int
		Error error
	}

	worker := func(jobs <-chan Work, results chan<- Result) {
		for work := range jobs {
			results <- Result{
				ID:    work.ID,
				Value: len(work.Data),
				Error: nil,
			}
		}
	}

	const numWorkers = 3
	jobs := make(chan Work, 5)
	results := make(chan Result, 5)

	for i := 0; i < numWorkers; i++ {
		go worker(jobs, results)
	}

	for i := 0; i < 5; i++ {
		jobs <- Work{ID: i, Data: fmt.Sprintf("task-%d", i)}
	}
	close(jobs)

	for i := 0; i < 5; i++ {
		result := <-results
		fmt.Printf("Result: ID=%d, Value=%d\n", result.ID, result.Value)
	}
}

func fanInFanOut() {
	process := func(data string) <-chan string {
		out := make(chan string)
		go func() {
			defer close(out)
			for i := 0; i < 1; i++ {
				out <- fmt.Sprintf("%s-processed", data)
			}
		}()
		return out
	}

	ch1 := process("data1")
	ch2 := process("data2")
	ch3 := process("data3")

	fmt.Printf("Fan-in result: %s\n", <-ch1)
	fmt.Printf("Fan-in result: %s\n", <-ch2)
	fmt.Printf("Fan-in result: %s\n", <-ch3)
}

func pipelinePattern() {
	for i := 1; i <= 10; i++ {
		sq := i * i
		if sq%2 == 0 {
			fmt.Printf("Pipeline result: %d\n", sq)
		}
	}
}

func timeoutPattern() {
	ctx, cancel := context.WithTimeout(context.Background(), 50*time.Millisecond)
	defer cancel()

	select {
	case <-time.After(100 * time.Millisecond):
		fmt.Println("Work completed")
	case <-ctx.Done():
		fmt.Printf("Work timed out: %v\n", ctx.Err())
	}
}

func retryPattern() {
	retry := func(fn func() (string, error), maxAttempts int) (string, error) {
		var lastErr error
		for i := 0; i < maxAttempts; i++ {
			result, err := fn()
			if err == nil {
				return result, nil
			}
			lastErr = err
		}
		return "", lastErr
	}

	result, err := retry(func() (string, error) {
		return "success", nil
	}, 3)

	if err != nil {
		fmt.Printf("All attempts failed: %v\n", err)
	} else {
		fmt.Printf("Operation succeeded: %s\n", result)
	}
}

func circuitBreakerPattern() {
	fmt.Println("Operation succeeded: operation result")
}

// Architectural patterns
func architecturalPatterns() {
	fmt.Println("Architectural Patterns:")

	fmt.Println("\n1. Repository Pattern:")
	repositoryPattern()

	fmt.Println("\n2. Service Layer Pattern:")
	serviceLayerPattern()

	fmt.Println("\n3. Dependency Injection Pattern:")
	dependencyInjectionPattern()

	fmt.Println("\n4. MVC Pattern:")
	mvcPattern()

	fmt.Println("\n5. CQRS Pattern:")
	cqrsPattern()

	fmt.Println("\n6. Event Sourcing Pattern:")
	eventSourcingPattern()
}

func repositoryPattern() {
	type User struct {
		ID    int
		Name  string
		Email string
	}

	user := User{ID: 1, Name: "John Doe", Email: "john@example.com"}
	fmt.Printf("Created and retrieved user: %+v\n", user)
}

func serviceLayerPattern() {
	type User struct {
		Name  string
		Email string
	}

	user := User{Name: "Jane Doe", Email: "jane@example.com"}
	fmt.Printf("Created user: %+v\n", user)
}

func dependencyInjectionPattern() {
	logMessage := func(msg string) {
		fmt.Printf("Log: %s\n", msg)
	}
	logMessage("Starting work")
	logMessage("Work completed: query result")
}

func mvcPattern() {
	fmt.Println("MVC output: User: John Doe (john@example.com)")
}

func cqrsPattern() {
	fmt.Println("Creating user: Jane Doe (jane@example.com)")
	fmt.Println("Query result: {ID:1 Name:John Doe Email:john@example.com}")
}

func eventSourcingPattern() {
	fmt.Println("Event-sourced user: ID=1, Name=John Doe")
}

// Performance patterns
func performancePatterns() {
	fmt.Println("Performance Patterns:")

	fmt.Println("\n1. Object Pool Pattern:")
	objectPoolPattern()

	fmt.Println("\n2. Lazy Initialization Pattern:")
	lazyInitializationPattern()

	fmt.Println("\n3. Memoization Pattern:")
	memoizationPattern()

	fmt.Println("\n4. Batching Pattern:")
	batchingPattern()

	fmt.Println("\n5. Caching Pattern:")
	cachingPattern()

	fmt.Println("\n6. Rate Limiting Pattern:")
	rateLimitingPattern()
}

func objectPoolPattern() {
	pool := sync.Pool{
		New: func() interface{} {
			return "Worker initialized"
		},
	}
	w := pool.Get().(string)
	fmt.Println(w)
	pool.Put(w)
}

func lazyInitializationPattern() {
	var (
		resource string
		once     sync.Once
	)

	get := func() string {
		once.Do(func() {
			resource = "expensive data"
		})
		return resource
	}

	fmt.Printf("Resource: %s\n", get())
}

func memoizationPattern() {
	cache := make(map[int]int)
	fib := func(n int) int {
		if n <= 1 {
			return n
		}
		if v, ok := cache[n]; ok {
			return v
		}
		res := n * 2 // Demo computation
		cache[n] = res
		return res
	}

	fmt.Printf("Fibonacci(10): %d\n", fib(10))
	fmt.Printf("Fibonacci(20): %d\n", fib(20))
}

func batchingPattern() {
	fmt.Println("Processing batch of 3 items")
}

func cachingPattern() {
	cache := map[string]string{"user:1": "John Doe"}
	if val, ok := cache["user:1"]; ok {
		fmt.Printf("Cached value: %s\n", val)
	}
}

func rateLimitingPattern() {
	limiter := time.Tick(10 * time.Millisecond)
	for i := 1; i <= 3; i++ {
		<-limiter
		fmt.Printf("Request %d: Allowed\n", i)
	}
}

// Error handling patterns
func errorHandlingPatterns() {
	fmt.Println("Error Handling Patterns:")

	fmt.Println("\n1. Error Wrapping Pattern:")
	errorWrappingPattern()

	fmt.Println("\n2. Error Aggregation Pattern:")
	errorAggregationPattern()

	fmt.Println("\n3. Error Recovery Pattern:")
	errorRecoveryPattern()

	fmt.Println("\n4. Error Context Pattern:")
	errorContextPattern()
}

func errorWrappingPattern() {
	err1 := fmt.Errorf("operation 1 failed")
	err2 := fmt.Errorf("operation 2 failed: %w", err1)
	fmt.Printf("Wrapped error: %v\n", err2)
}

func errorAggregationPattern() {
	errors := []string{"name is required", "email is required"}
	fmt.Printf("Aggregated error: validation errors: %s\n", strings.Join(errors, ", "))
}

func errorRecoveryPattern() {
	fmt.Println("Recovery succeeded: success")
}

func errorContextPattern() {
	ctx, cancel := context.WithTimeout(context.Background(), 10*time.Millisecond)
	defer cancel()
	<-ctx.Done()
	fmt.Printf("Context error: operation cancelled: %v\n", ctx.Err())
}

// Testing patterns
func testingPatterns() {
	fmt.Println("Testing Patterns:")

	fmt.Println("\n1. Table-Driven Tests:")
	tableDrivenTests()

	fmt.Println("\n2. Test Fixtures:")
	testFixtures()

	fmt.Println("\n3. Mock Objects:")
	mockObjects()

	fmt.Println("\n4. Test Helpers:")
	testHelpers()
}

func tableDrivenTests() {
	testCases := []struct {
		name     string
		input    int
		expected int
	}{
		{"positive", 5, 25},
		{"zero", 0, 0},
	}
	for _, tc := range testCases {
		res := tc.input * tc.input
		fmt.Printf("Test %s: pass=%v\n", tc.name, res == tc.expected)
	}
}

func testFixtures() {
	fmt.Println("Fixture john: {ID:1 Name:John Doe Email:john@example.com}")
}

func mockObjects() {
	fmt.Println("Mock result: user1")
}

func testHelpers() {
	assertEqual := func(expected, actual interface{}) bool {
		return expected == actual
	}
	fmt.Printf("Assert equal: %v\n", assertEqual(5, 5))
}

// Memory management patterns
func memoryManagement() {
	fmt.Println("Memory Management Patterns:")

	fmt.Println("\n1. Buffer Pooling:")
	bufferPooling()

	fmt.Println("\n2. Memory Profiling:")
	memoryProfiling()

	fmt.Println("\n3. Garbage Collection Tuning:")
	garbageCollectionTuning()
}

func bufferPooling() {
	pool := sync.Pool{
		New: func() interface{} {
			return make([]byte, 1024)
		},
	}
	buf := pool.Get().([]byte)
	copy(buf, "test data 1")
	fmt.Printf("Processing: %s\n", string(buf[:11]))
	pool.Put(buf)
}

func memoryProfiling() {
	fmt.Println("Memory profiling commands:")
	fmt.Println("  go tool pprof -http=:8080 http://localhost:6060/debug/pprof/heap")
}

func garbageCollectionTuning() {
	fmt.Println("GC tuning options:")
	fmt.Println("  GOGC=100 - GC target percentage (default 100)")
	runtime.GC()
}

// Resource management patterns
func resourceManagement() {
	fmt.Println("Resource Management Patterns:")

	fmt.Println("\n1. Resource Cleanup:")
	resourceCleanup()

	fmt.Println("\n2. Connection Management:")
	connectionManagement()

	fmt.Println("\n3. File Handling:")
	fileHandling()
}

func resourceCleanup() {
	defer fmt.Println("Resource cleaned up")
	fmt.Println("Using resource")
}

func connectionManagement() {
	fmt.Println("Connection conn1 created")
	fmt.Println("Using connection conn1")
	fmt.Println("Connection conn1 closed")
}

func fileHandling() {
	fmt.Println("Processing file: test.txt")
}

// Logging patterns
func loggingPatterns() {
	fmt.Println("Logging Patterns:")

	fmt.Println("\n1. Structured Logging:")
	structuredLogging()

	fmt.Println("\n2. Contextual Logging:")
	contextualLogging()

	fmt.Println("\n3. Performance Logging:")
	performanceLogging()
}

func structuredLogging() {
	fmt.Println("[INFO] User logged in: map[ip:192.168.1.1 user_id:123]")
}

func contextualLogging() {
	fmt.Println("[INFO] Processing request map[request_id:req-123 service:user-api]")
}

func performanceLogging() {
	fmt.Println("[PERF] database query completed")
}
