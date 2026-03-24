package main

import (
	"fmt"
	"sync"
	"sync/atomic"
	"time"
)

func main() {
	fmt.Println("=== Synchronization Patterns ===")
	
	// Mutex patterns
	fmt.Println("\n--- Mutex Patterns ---")
	demonstrateMutex()
	
	// RWMutex patterns
	fmt.Println("\n--- RWMutex Patterns ---")
	demonstrateRWMutex()
	
	// WaitGroup patterns
	fmt.Println("\n--- WaitGroup Patterns ---")
	demonstrateWaitGroup()
	
	// Once patterns
	fmt.Println("\n--- Once Patterns ---")
	demonstrateOnce()
	
	// Atomic operations
	fmt.Println("\n--- Atomic Operations ---")
	demonstrateAtomic()
	
	// Condition variables
	fmt.Println("\n--- Condition Variables ---")
	demonstrateCond()
	
	// Pool patterns
	fmt.Println("\n--- Pool Patterns ---")
	demonstratePool()
	
	// Singleton pattern
	fmt.Println("\n--- Singleton Pattern ---")
	demonstrateSingleton()
}

// Mutex patterns
type Counter struct {
	mu    sync.Mutex
	value int
}

func (c *Counter) Increment() {
	c.mu.Lock()
	defer c.mu.Unlock()
	c.value++
}

func (c *Counter) GetValue() int {
	c.mu.Lock()
	defer c.mu.Unlock()
	return c.value
}

func demonstrateMutex() {
	counter := &Counter{}
	
	// Increment from multiple goroutines
	var wg sync.WaitGroup
	for i := 0; i < 100; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			counter.Increment()
		}()
	}
	
	wg.Wait()
	fmt.Printf("Final counter value: %d\n", counter.GetValue())
	
	// Mutex with defer pattern
	fmt.Println("\n--- Mutex with Defer ---")
	safeOperation()
}

func safeOperation() {
	var mu sync.Mutex
	data := make(map[string]int)
	
	// Function that uses mutex with defer
	updateData := func(key string, value int) {
		mu.Lock()
		defer mu.Unlock()
		data[key] = value
	}
	
	readData := func(key string) (int, bool) {
		mu.Lock()
		defer mu.Unlock()
		value, exists := data[key]
		return value, exists
	}
	
	// Use the functions
	updateData("a", 1)
	updateData("b", 2)
	
	if value, exists := readData("a"); exists {
		fmt.Printf("Data for 'a': %d\n", value)
	}
}

// RWMutex patterns
type DataStore struct {
	mu   sync.RWMutex
	data map[string]string
}

func NewDataStore() *DataStore {
	return &DataStore{
		data: make(map[string]string),
	}
}

func (ds *DataStore) Set(key, value string) {
	ds.mu.Lock()
	defer ds.mu.Unlock()
	ds.data[key] = value
}

func (ds *DataStore) Get(key string) (string, bool) {
	ds.mu.RLock()
	defer ds.mu.RUnlock()
	value, exists := ds.data[key]
	return value, exists
}

func (ds *DataStore) GetAll() map[string]string {
	ds.mu.RLock()
	defer ds.mu.RUnlock()
	
	// Create a copy to avoid race conditions
	copy := make(map[string]string)
	for k, v := range ds.data {
		copy[k] = v
	}
	return copy
}

func demonstrateRWMutex() {
	ds := NewDataStore()
	
	// Writers
	var wg sync.WaitGroup
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			ds.Set(fmt.Sprintf("key%d", id), fmt.Sprintf("value%d", id))
		}(i)
	}
	
	// Readers
	for i := 0; i < 10; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			if value, exists := ds.Get("key1"); exists {
				fmt.Printf("Read: key1 = %s\n", value)
			}
		}()
	}
	
	wg.Wait()
	
	allData := ds.GetAll()
	fmt.Printf("All data: %v\n", allData)
}

// WaitGroup patterns
func demonstrateWaitGroup() {
	// Basic WaitGroup
	fmt.Println("--- Basic WaitGroup ---")
	basicWaitGroup()
	
	// Nested WaitGroup
	fmt.Println("\n--- Nested WaitGroup ---")
	nestedWaitGroup()
	
	// WaitGroup with error handling
	fmt.Println("\n--- WaitGroup with Error Handling ---")
	waitGroupWithError()
}

func basicWaitGroup() {
	var wg sync.WaitGroup
	results := make(chan int, 5)
	
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			time.Sleep(time.Millisecond * 100)
			results <- id * id
		}(i)
	}
	
	// Wait in separate goroutine
	go func() {
		wg.Wait()
		close(results)
	}()
	
	for result := range results {
		fmt.Printf("Result: %d\n", result)
	}
}

func nestedWaitGroup() {
	var outerWg sync.WaitGroup
	
	for i := 0; i < 3; i++ {
		outerWg.Add(1)
		go func(groupID int) {
			defer outerWg.Done()
			
			var innerWg sync.WaitGroup
			for j := 0; j < 3; j++ {
				innerWg.Add(1)
				go func(id int) {
					defer innerWg.Done()
					fmt.Printf("Group %d, Task %d\n", groupID, id)
					time.Sleep(time.Millisecond * 50)
				}(j)
			}
			innerWg.Wait()
			fmt.Printf("Group %d completed\n", groupID)
		}(i)
	}
	
	outerWg.Wait()
	fmt.Println("All groups completed")
}

func waitGroupWithError() {
	var wg sync.WaitGroup
	errors := make(chan error, 10)
	
	for i := 0; i < 10; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			
			if id%3 == 0 {
				errors <- fmt.Errorf("task %d failed", id)
				return
			}
			
			fmt.Printf("Task %d succeeded\n", id)
		}(i)
	}
	
	// Wait for completion
	go func() {
		wg.Wait()
		close(errors)
	}()
	
	// Collect errors
	var errorList []error
	for err := range errors {
		errorList = append(errorList, err)
	}
	
	if len(errorList) > 0 {
		fmt.Printf("Completed with %d errors:\n", len(errorList))
		for _, err := range errorList {
			fmt.Printf("  %v\n", err)
		}
	}
}

// Once patterns
type Config struct {
	loaded bool
	data   map[string]string
	mu     sync.Once
}

func (c *Config) Load() {
	c.mu.Do(func() {
		fmt.Println("Loading configuration...")
		time.Sleep(time.Millisecond * 100)
		c.data = map[string]string{
			"host": "localhost",
			"port": "8080",
		}
		c.loaded = true
		fmt.Println("Configuration loaded")
	})
}

func (c *Config) Get(key string) (string, bool) {
	if !c.loaded {
		c.Load()
	}
	value, exists := c.data[key]
	return value, exists
}

func demonstrateOnce() {
	config := &Config{}
	
	// Multiple goroutines trying to load config
	var wg sync.WaitGroup
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			config.Load()
		}()
	}
	wg.Wait()
	
	// Config should be loaded only once
	if host, exists := config.Get("host"); exists {
		fmt.Printf("Host: %s\n", host)
	}
}

// Atomic operations
type AtomicCounter struct {
	value int64
}

func (ac *AtomicCounter) Increment() {
	atomic.AddInt64(&ac.value, 1)
}

func (ac *AtomicCounter) Get() int64 {
	return atomic.LoadInt64(&ac.value)
}

func (ac *AtomicCounter) CompareAndSwap(old, new int64) bool {
	return atomic.CompareAndSwapInt64(&ac.value, old, new)
}

func demonstrateAtomic() {
	counter := &AtomicCounter{}
	
	// Increment from multiple goroutines
	var wg sync.WaitGroup
	for i := 0; i < 1000; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			counter.Increment()
		}()
	}
	wg.Wait()
	
	fmt.Printf("Atomic counter value: %d\n", counter.Get())
	
	// Compare and swap
	fmt.Println("\n--- Compare and Swap ---")
	old := counter.Get()
	new := old + 100
	
	if counter.CompareAndSwap(old, new) {
		fmt.Printf("CAS succeeded, new value: %d\n", counter.Get())
	}
	
	// Atomic flags
	var flag int64
	setFlag := func() {
		atomic.StoreInt64(&flag, 1)
	}
	
	isFlagSet := func() bool {
		return atomic.LoadInt64(&flag) == 1
	}
	
	go setFlag()
	time.Sleep(time.Millisecond * 10)
	fmt.Printf("Flag set: %t\n", isFlagSet())
}

// Condition variables
type BoundedBuffer struct {
	mu     sync.Mutex
	cond   *sync.Cond
	buffer []int
	size   int
}

func NewBoundedBuffer(size int) *BoundedBuffer {
	b := &BoundedBuffer{
		buffer: make([]int, 0, size),
		size:   size,
	}
	b.cond = sync.NewCond(&b.mu)
	return b
}

func (b *BoundedBuffer) Put(item int) {
	b.mu.Lock()
	defer b.mu.Unlock()
	
	for len(b.buffer) == b.size {
		fmt.Printf("Buffer full, waiting to put %d\n", item)
		b.cond.Wait()
	}
	
	b.buffer = append(b.buffer, item)
	fmt.Printf("Put %d, buffer size: %d\n", item, len(b.buffer))
	b.cond.Signal()
}

func (b *BoundedBuffer) Get() int {
	b.mu.Lock()
	defer b.mu.Unlock()
	
	for len(b.buffer) == 0 {
		fmt.Println("Buffer empty, waiting to get")
		b.cond.Wait()
	}
	
	item := b.buffer[0]
	b.buffer = b.buffer[1:]
	fmt.Printf("Got %d, buffer size: %d\n", item, len(b.buffer))
	b.cond.Signal()
	return item
}

func demonstrateCond() {
	buffer := NewBoundedBuffer(3)
	
	// Producers
	var wg sync.WaitGroup
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			buffer.Put(id)
		}(i)
	}
	
	// Consumers
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			time.Sleep(time.Millisecond * 50) // Let producers fill first
			item := buffer.Get()
			fmt.Printf("Consumed: %d\n", item)
		}()
	}
	
	wg.Wait()
}

// Pool patterns
type WorkerPool struct {
	workers chan func()
	wg      sync.WaitGroup
}

func NewWorkerPool(size int) *WorkerPool {
	wp := &WorkerPool{
		workers: make(chan func(), size),
	}
	
	// Start workers
	for i := 0; i < size; i++ {
		wp.wg.Add(1)
		go wp.worker()
	}
	
	return wp
}

func (wp *WorkerPool) worker() {
	defer wp.wg.Done()
	for work := range wp.workers {
		work()
	}
}

func (wp *WorkerPool) Submit(work func()) {
	wp.workers <- work
}

func (wp *WorkerPool) Close() {
	close(wp.workers)
	wp.wg.Wait()
}

func demonstratePool() {
	pool := NewWorkerPool(3)
	defer pool.Close()
	
	// Submit work
	for i := 0; i < 10; i++ {
		id := i
		pool.Submit(func() {
			fmt.Printf("Processing task %d\n", id)
			time.Sleep(time.Millisecond * 100)
		})
	}
	
	time.Sleep(time.Second) // Let work complete
}

// Singleton pattern with sync.Once
type Database struct {
	connected bool
	mu        sync.Once
}

func (db *Database) Connect() {
	db.mu.Do(func() {
		fmt.Println("Connecting to database...")
		time.Sleep(time.Millisecond * 200)
		db.connected = true
		fmt.Println("Database connected")
	})
}

func (db *Database) Query(query string) string {
	if !db.connected {
		db.Connect()
	}
	return fmt.Sprintf("Result for: %s", query)
}

func demonstrateSingleton() {
	db1 := &Database{}
	db2 := &Database{}
	
	// Both should connect only once
	var wg sync.WaitGroup
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go func(db *Database) {
			defer wg.Done()
			result := db.Query(fmt.Sprintf("SELECT * FROM table%d", i))
			fmt.Printf("Query result: %s\n", result)
		}(db1)
	}
	
	for i := 0; i < 5; i++ {
		wg.Add(1)
		go func(db *Database) {
			defer wg.Done()
			result := db.Query(fmt.Sprintf("SELECT * FROM table%d", i+5))
			fmt.Printf("Query result: %s\n", result)
		}(db2)
	}
	
	wg.Wait()
}

// Advanced synchronization patterns

type SafeMap struct {
	mu   sync.RWMutex
	data map[string]interface{}
}

func NewSafeMap() *SafeMap {
	return &SafeMap{
		data: make(map[string]interface{}),
	}
}

func (sm *SafeMap) Set(key string, value interface{}) {
	sm.mu.Lock()
	defer sm.mu.Unlock()
	sm.data[key] = value
}

func (sm *SafeMap) Get(key string) (interface{}, bool) {
	sm.mu.RLock()
	defer sm.mu.RUnlock()
	value, exists := sm.data[key]
	return value, exists
}

func (sm *SafeMap) Delete(key string) {
	sm.mu.Lock()
	defer sm.mu.Unlock()
	delete(sm.data, key)
}

// Rate limiter using atomic operations
type RateLimiter struct {
	tokens    int64
	maxTokens int64
	refillAt  int64
}

func NewRateLimiter(maxTokens int64) *RateLimiter {
	return &RateLimiter{
		tokens:    maxTokens,
		maxTokens: maxTokens,
		refillAt:  time.Now().Add(time.Second).Unix(),
	}
}

func (rl *RateLimiter) Allow() bool {
	now := time.Now().Unix()
	
	// Refill tokens if needed
	if now >= atomic.LoadInt64(&rl.refillAt) {
		if atomic.CompareAndSwapInt64(&rl.refillAt, now, now+1) {
			atomic.StoreInt64(&rl.tokens, rl.maxTokens)
		}
	}
	
	// Try to consume a token
	for {
		current := atomic.LoadInt64(&rl.tokens)
		if current <= 0 {
			return false
		}
		
		if atomic.CompareAndSwapInt64(&rl.tokens, current, current-1) {
			return true
		}
	}
}

func demonstrateAdvancedPatterns() {
	fmt.Println("\n--- Advanced Synchronization Patterns ---")
	
	// Safe map
	sm := NewSafeMap()
	
	var wg sync.WaitGroup
	for i := 0; i < 10; i++ {
		wg.Add(2)
		
		// Writer
		go func(id int) {
			defer wg.Done()
			sm.Set(fmt.Sprintf("key%d", id), id*10)
		}(i)
		
		// Reader
		go func(id int) {
			defer wg.Done()
			if value, exists := sm.Get(fmt.Sprintf("key%d", id)); exists {
				fmt.Printf("Read key%d: %v\n", id, value)
			}
		}(i)
	}
	
	wg.Wait()
	
	// Rate limiter
	fmt.Println("\n--- Rate Limiter ---")
	limiter := NewRateLimiter(5)
	
	for i := 0; i < 10; i++ {
		if limiter.Allow() {
			fmt.Printf("Request %d allowed\n", i)
		} else {
			fmt.Printf("Request %d rate limited\n", i)
		}
		time.Sleep(time.Millisecond * 100)
	}
}
