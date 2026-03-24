package main

import (
	"context"
	"fmt"
	"runtime"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Goroutine Lifecycle Management ===")
	
	// Basic goroutine lifecycle
	fmt.Println("\n--- Basic Goroutine Lifecycle ---")
	basicLifecycle()
	
	// Goroutine with context
	fmt.Println("\n--- Goroutine with Context ---")
	goroutineWithContext()
	
	// Goroutine cancellation
	fmt.Println("\n--- Goroutine Cancellation ---")
	goroutineCancellation()
	
	// Goroutine timeouts
	fmt.Println("\n--- Goroutine Timeouts ---")
	goroutineTimeouts()
	
	// Goroutine cleanup
	fmt.Println("\n--- Goroutine Cleanup ---")
	goroutineCleanup()
	
	// Goroutine pools
	fmt.Println("\n--- Goroutine Pools ---")
	goroutinePools()
	
	// Goroutine monitoring
	fmt.Println("\n--- Goroutine Monitoring ---")
	goroutineMonitoring()
	
	// Graceful shutdown
	fmt.Println("\n--- Graceful Shutdown ---")
	gracefulShutdown()
}

// Basic goroutine lifecycle
func basicLifecycle() {
	fmt.Println("Starting goroutine...")
	
	done := make(chan bool)
	
	go func() {
		fmt.Println("Goroutine: Starting work")
		time.Sleep(time.Second * 2)
		fmt.Println("Goroutine: Work completed")
		done <- true
	}()
	
	// Wait for goroutine to complete
	<-done
	fmt.Println("Main: Goroutine finished")
}

// Goroutine with context
func goroutineWithContext() {
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()
	
	done := make(chan bool)
	
	go func(ctx context.Context) {
		fmt.Println("Context goroutine: Starting")
		select {
		case <-time.After(time.Second * 3):
			fmt.Println("Context goroutine: Work completed")
		case <-ctx.Done():
			fmt.Println("Context goroutine: Cancelled")
			return
		}
		done <- true
	}(ctx)
	
	// Wait for completion
	select {
	case <-done:
		fmt.Println("Main: Context goroutine completed")
	case <-time.After(time.Second * 4):
		fmt.Println("Main: Timeout waiting for goroutine")
	}
}

// Goroutine cancellation
func goroutineCancellation() {
	ctx, cancel := context.WithTimeout(context.Background(), time.Second*2)
	defer cancel()
	
	var wg sync.WaitGroup
	
	// Start multiple goroutines
	for i := 0; i < 3; i++ {
		wg.Add(1)
		go func(id int, ctx context.Context) {
			defer wg.Done()
			
			fmt.Printf("Worker %d: Starting\n", id)
			
			for {
				select {
				case <-ctx.Done():
					fmt.Printf("Worker %d: Stopping (%v)\n", id, ctx.Err())
					return
				default:
					// Do some work
					time.Sleep(time.Millisecond * 500)
					fmt.Printf("Worker %d: Working...\n", id)
				}
			}
		}(i, ctx)
	}
	
	// Let them run for a bit
	time.Sleep(time.Second * 3)
	
	wg.Wait()
	fmt.Println("All workers stopped")
}

// Goroutine timeouts
func goroutineTimeouts() {
	// Timeout with context
	ctx, cancel := context.WithTimeout(context.Background(), time.Second*1)
	defer cancel()
	
	result := make(chan string, 1)
	
	go func() {
		fmt.Println("Timeout goroutine: Starting long operation")
		time.Sleep(time.Second * 2) // This will exceed timeout
		result <- "operation completed"
	}()
	
	select {
	case res := <-result:
		fmt.Printf("Operation succeeded: %s\n", res)
	case <-ctx.Done():
		fmt.Printf("Operation timed out: %v\n", ctx.Err())
	}
	
	// Timeout with time.After
	fmt.Println("\n--- Timeout with time.After ---")
	
	result2 := make(chan string, 1)
	
	go func() {
		time.Sleep(time.Millisecond * 500) // This will complete in time
		result2 <- "quick operation"
	}()
	
	select {
	case res := <-result2:
		fmt.Printf("Quick operation succeeded: %s\n", res)
	case <-time.After(time.Second):
		fmt.Println("Quick operation timed out")
	}
}

// Goroutine cleanup with defer
func goroutineCleanup() {
	var wg sync.WaitGroup
	
	for i := 0; i < 3; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			
			// Setup cleanup
			resource := acquireResource(id)
			defer releaseResource(resource)
			
			fmt.Printf("Goroutine %d: Working with resource\n", id)
			time.Sleep(time.Millisecond * 500)
			
			// Simulate potential error
			if id == 1 {
				fmt.Printf("Goroutine %d: Simulating error\n", id)
				return // defer will still execute
			}
			
			fmt.Printf("Goroutine %d: Completed successfully\n", id)
		}(i)
	}
	
	wg.Wait()
	fmt.Println("All goroutines cleaned up")
}

type Resource struct {
	id int
}

func acquireResource(id int) *Resource {
	fmt.Printf("Acquiring resource for goroutine %d\n", id)
	return &Resource{id: id}
}

func releaseResource(resource *Resource) {
	fmt.Printf("Releasing resource for goroutine %d\n", resource.id)
}

// Goroutine pools for reuse
type GoroutinePool struct {
	work    chan func()
	workers int
	wg      sync.WaitGroup
	quit    chan struct{}
}

func NewGoroutinePool(workers int) *GoroutinePool {
	pool := &GoroutinePool{
		work:    make(chan func()),
		workers: workers,
		quit:    make(chan struct{}),
	}
	
	// Start worker goroutines
	for i := 0; i < workers; i++ {
		pool.wg.Add(1)
		go pool.worker(i)
	}
	
	return pool
}

func (p *GoroutinePool) worker(id int) {
	defer p.wg.Done()
	
	for {
		select {
		case work := <-p.work:
			if work != nil {
				fmt.Printf("Worker %d: Executing work\n", id)
				work()
			}
		case <-p.quit:
			fmt.Printf("Worker %d: Shutting down\n", id)
			return
		}
	}
}

func (p *GoroutinePool) Submit(work func()) {
	p.work <- work
}

func (p *GoroutinePool) Shutdown() {
	close(p.quit)
	p.wg.Wait()
}

func goroutinePools() {
	pool := NewGoroutinePool(3)
	defer pool.Shutdown()
	
	// Submit work
	for i := 0; i < 10; i++ {
		id := i
		pool.Submit(func() {
			fmt.Printf("Processing task %d\n", id)
			time.Sleep(time.Millisecond * 200)
		})
	}
	
	time.Sleep(time.Second * 2)
}

// Goroutine monitoring
type GoroutineMonitor struct {
	count    int64
	maxCount int64
	mu       sync.RWMutex
}

func NewGoroutineMonitor() *GoroutineMonitor {
	return &GoroutineMonitor{}
}

func (gm *GoroutineMonitor) Start() {
	gm.mu.Lock()
	gm.count++
	if gm.count > gm.maxCount {
		gm.maxCount = gm.count
	}
	gm.mu.Unlock()
}

func (gm *GoroutineMonitor) Stop() {
	gm.mu.Lock()
	gm.count--
	gm.mu.Unlock()
}

func (gm *GoroutineMonitor) GetCurrent() int64 {
	gm.mu.RLock()
	defer gm.mu.RUnlock()
	return gm.count
}

func (gm *GoroutineMonitor) GetMax() int64 {
	gm.mu.RLock()
	defer gm.mu.RUnlock()
	return gm.maxCount
}

func goroutineMonitoring() {
	monitor := NewGoroutineMonitor()
	
	var wg sync.WaitGroup
	
	// Start monitoring goroutine
	monitorDone := make(chan bool)
	go func() {
		ticker := time.NewTicker(time.Millisecond * 200)
		defer ticker.Stop()
		
		for {
			select {
			case <-ticker.C:
				current := monitor.GetCurrent()
				max := monitor.GetMax()
				fmt.Printf("Current goroutines: %d, Max: %d\n", current, max)
			case <-monitorDone:
				return
			}
		}
	}()
	
	// Start and stop goroutines
	for i := 0; i < 5; i++ {
		wg.Add(1)
		monitor.Start()
		
		go func(id int) {
			defer wg.Done()
			defer monitor.Stop()
			
			fmt.Printf("Monitored goroutine %d starting\n", id)
			time.Sleep(time.Millisecond * time.Duration(500+id*100))
			fmt.Printf("Monitored goroutine %d finished\n", id)
		}(i)
		
		time.Sleep(time.Millisecond * 100)
	}
	
	wg.Wait()
	close(monitorDone)
	
	fmt.Printf("Final - Current: %d, Max: %d\n", monitor.GetCurrent(), monitor.GetMax())
}

// Graceful shutdown pattern
type Service struct {
	name    string
	workers int
	quit    chan struct{}
	wg      sync.WaitGroup
}

func NewService(name string, workers int) *Service {
	return &Service{
		name:    name,
		workers: workers,
		quit:    make(chan struct{}),
	}
}

func (s *Service) Start() {
	fmt.Printf("Service %s: Starting %d workers\n", s.name, s.workers)
	
	for i := 0; i < s.workers; i++ {
		s.wg.Add(1)
		go s.worker(i)
	}
}

func (s *Service) worker(id int) {
	defer s.wg.Done()
	
	fmt.Printf("Service %s worker %d: Starting\n", s.name, id)
	
	for {
		select {
		case <-s.quit:
			fmt.Printf("Service %s worker %d: Shutting down\n", s.name, id)
			return
		default:
			// Do work
			time.Sleep(time.Millisecond * 200)
		}
	}
}

func (s *Service) Shutdown() {
	fmt.Printf("Service %s: Initiating shutdown\n", s.name)
	close(s.quit)
	s.wg.Wait()
	fmt.Printf("Service %s: Shutdown complete\n", s.name)
}

func gracefulShutdown() {
	services := []*Service{
		NewService("Database", 2),
		NewService("Cache", 3),
		NewService("API", 4),
	}
	
	// Start services
	for _, service := range services {
		service.Start()
	}
	
	// Let them run
	time.Sleep(time.Second * 2)
	
	// Shutdown services in reverse order
	for i := len(services) - 1; i >= 0; i-- {
		services[i].Shutdown()
	}
	
	fmt.Println("All services shut down gracefully")
}

// Advanced goroutine lifecycle patterns

// Heartbeat pattern
func heartbeatPattern() {
	fmt.Println("\n--- Heartbeat Pattern ---")
	
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()
	
	heartbeat := make(chan time.Time, 1)
	done := make(chan bool)
	
	// Heartbeat sender
	go func() {
		defer close(heartbeat)
		ticker := time.NewTicker(time.Millisecond * 500)
		defer ticker.Stop()
		
		for {
			select {
			case <-ctx.Done():
				return
			case t := <-ticker.C:
				select {
				case heartbeat <- t:
				default:
					// Don't block if heartbeat channel is full
				}
			}
		}
	}()
	
	// Heartbeat monitor
	go func() {
		defer close(done)
		
		lastHeartbeat := time.Now()
		timeout := time.Second * 2
		
		for {
			select {
			case hb, ok := <-heartbeat:
				if !ok {
					return
				}
				fmt.Printf("Heartbeat received: %v\n", hb.Format("15:04:05"))
				lastHeartbeat = hb
			case <-time.After(timeout - time.Since(lastHeartbeat)):
				fmt.Printf("No heartbeat for %v, stopping\n", timeout)
				cancel()
				return
			}
		}
	}()
	
	// Main work
	go func() {
		time.Sleep(time.Second * 3)
		cancel()
	}()
	
	<-done
	fmt.Println("Heartbeat pattern completed")
}

// Supervisor pattern
type Supervisor struct {
	children map[string]*ChildProcess
	mu       sync.RWMutex
	quit     chan struct{}
	wg       sync.WaitGroup
}

type ChildProcess struct {
	name     string
	restarts int
	maxRestarts int
	quit     chan struct{}
}

func NewSupervisor() *Supervisor {
	return &Supervisor{
		children: make(map[string]*ChildProcess),
		quit:     make(chan struct{}),
	}
}

func (s *Supervisor) AddChild(name string, maxRestarts int, work func(context.Context) error) {
	s.mu.Lock()
	defer s.mu.Unlock()
	
	child := &ChildProcess{
		name:       name,
		maxRestarts: maxRestarts,
		quit:       make(chan struct{}),
	}
	
	s.children[name] = child
	
	s.wg.Add(1)
	go s.superviseChild(child, work)
}

func (s *Supervisor) superviseChild(child *ChildProcess, work func(context.Context) error) {
	defer s.wg.Done()
	
	for {
		ctx, cancel := context.WithCancel(context.Background())
		
		done := make(chan error, 1)
		go func() {
			done <- work(ctx)
		}()
		
		select {
		case err := <-done:
			cancel()
			if err != nil {
				fmt.Printf("Child %s failed: %v\n", child.name, err)
				
				child.restarts++
				if child.restarts >= child.maxRestarts {
					fmt.Printf("Child %s exceeded max restarts (%d)\n", 
						child.name, child.maxRestarts)
					return
				}
				
				fmt.Printf("Restarting child %s (restart %d/%d)\n", 
					child.name, child.restarts, child.maxRestarts)
				time.Sleep(time.Second) // Backoff
				continue
			}
			
		case <-child.quit:
			cancel()
			return
			
		case <-s.quit:
			cancel()
			return
		}
	}
}

func (s *Supervisor) StopChild(name string) {
	s.mu.RLock()
	child, exists := s.children[name]
	s.mu.RUnlock()
	
	if exists {
		close(child.quit)
	}
}

func (s *Supervisor) Shutdown() {
	close(s.quit)
	s.wg.Wait()
}

func supervisorPattern() {
	fmt.Println("\n--- Supervisor Pattern ---")
	
	supervisor := NewSupervisor()
	
	// Add children
	supervisor.AddChild("worker1", 3, func(ctx context.Context) error {
		for i := 0; i < 5; i++ {
			select {
			case <-ctx.Done():
				return ctx.Err()
			default:
				fmt.Printf("Worker1: Working %d\n", i)
				time.Sleep(time.Millisecond * 300)
			}
		}
		return nil
	})
	
	supervisor.AddChild("worker2", 2, func(ctx context.Context) error {
		for i := 0; i < 3; i++ {
			select {
			case <-ctx.Done():
				return ctx.Err()
			default:
				fmt.Printf("Worker2: Working %d\n", i)
				time.Sleep(time.Millisecond * 200)
			}
		}
		return fmt.Errorf("worker2 failed")
	})
	
	// Let them run
	time.Sleep(time.Second * 5)
	
	supervisor.Shutdown()
	fmt.Println("Supervisor shutdown complete")
}

// Demonstrate advanced patterns
func demonstrateAdvancedLifecycle() {
	heartbeatPattern()
	supervisorPattern()
}
