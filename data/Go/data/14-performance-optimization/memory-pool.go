package main

import (
	"fmt"
	"sync"
	"time"
)

// Object that will be pooled
type Buffer struct {
	data []byte
	size int
}

func NewBuffer(size int) *Buffer {
	return &Buffer{
		data: make([]byte, size),
		size: size,
	}
}

func (b *Buffer) Reset() {
	// Clear the data but keep the underlying array
	for i := range b.data {
		b.data[i] = 0
	}
}

func (b *Buffer) Write(data []byte) {
	copy(b.data, data)
}

func (b *Buffer) String() string {
	return string(b.data)
}

// Simple object pool
type BufferPool struct {
	pool sync.Pool
}

func NewBufferPool(size int) *BufferPool {
	return &BufferPool{
		pool: sync.Pool{
			New: func() interface{} {
				return NewBuffer(size)
			},
		},
	}
}

func (p *BufferPool) Get() *Buffer {
	buf := p.pool.Get().(*Buffer)
	buf.Reset()
	return buf
}

func (p *BufferPool) Put(buf *Buffer) {
	p.pool.Put(buf)
}

// Performance comparison
func withoutPool() {
	start := time.Now()
	
	for i := 0; i < 1000000; i++ {
		buf := NewBuffer(1024)
		buf.Write([]byte(fmt.Sprintf("data-%d", i)))
		// Simulate work
		_ = buf.String()
		// Buffer gets garbage collected
	}
	
	duration := time.Since(start)
	fmt.Printf("Without pool: %v\n", duration)
}

func withPool() {
	pool := NewBufferPool(1024)
	start := time.Now()
	
	for i := 0; i < 1000000; i++ {
		buf := pool.Get()
		buf.Write([]byte(fmt.Sprintf("data-%d", i)))
		// Simulate work
		_ = buf.String()
		pool.Put(buf)
	}
	
	duration := time.Since(start)
	fmt.Printf("With pool: %v\n", duration)
}

// Worker pool example
type Task struct {
	ID   int
	Data []byte
}

type Result struct {
	TaskID int
	Output string
}

type Worker struct {
	id     int
	taskCh <-chan Task
	resultCh chan<- Result
	bufferPool *BufferPool
}

func NewWorker(id int, taskCh <-chan Task, resultCh chan<- Result, bufferPool *BufferPool) *Worker {
	return &Worker{
		id:         id,
		taskCh:     taskCh,
		resultCh:   resultCh,
		bufferPool: bufferPool,
	}
}

func (w *Worker) Start() {
	go func() {
		for task := range w.taskCh {
			buf := w.bufferPool.Get()
			buf.Write(task.Data)
			
			// Simulate processing
			time.Sleep(time.Microsecond * 10)
			
			result := Result{
				TaskID: task.ID,
				Output: fmt.Sprintf("Processed: %s", buf.String()),
			}
			
			w.resultCh <- result
			w.bufferPool.Put(buf)
		}
	}()
}

func main() {
	fmt.Println("=== Memory Pool Optimization ===")

	// Basic pool performance comparison
	fmt.Println("\n--- Performance Comparison ---")
	fmt.Println("Creating and processing 1,000,000 buffers:")
	
	// Run without pool
	go withoutPool()
	time.Sleep(time.Millisecond * 100)
	
	// Run with pool
	go withPool()
	time.Sleep(time.Millisecond * 100)

	// Wait for completion
	time.Sleep(time.Second * 2)

	// Worker pool example
	fmt.Println("\n--- Worker Pool with Buffer Pool ---")
	
	const numTasks = 1000
	const numWorkers = 10
	
	taskCh := make(chan Task, numTasks)
	resultCh := make(chan Result, numTasks)
	bufferPool := NewBufferPool(256)
	
	// Start workers
	for i := 0; i < numWorkers; i++ {
		worker := NewWorker(i+1, taskCh, resultCh, bufferPool)
		worker.Start()
	}
	
	// Send tasks
	start := time.Now()
	for i := 0; i < numTasks; i++ {
		task := Task{
			ID:   i + 1,
			Data: []byte(fmt.Sprintf("task-data-%d", i+1)),
		}
		taskCh <- task
	}
	close(taskCh)
	
	// Collect results
	for i := 0; i < numTasks; i++ {
		result := <-resultCh
		if i%100 == 0 {
			fmt.Printf("Completed task %d: %s\n", result.TaskID, result.Output)
		}
	}
	close(resultCh)
	
	duration := time.Since(start)
	fmt.Printf("Processed %d tasks in %v\n", numTasks, duration)
	
	// Memory usage demonstration
	fmt.Println("\n--- Memory Usage Demonstration ---")
	
	// Simulate memory pressure
	var buffers []*Buffer
	fmt.Println("Allocating buffers without pool...")
	for i := 0; i < 10000; i++ {
		buffers = append(buffers, NewBuffer(1024))
	}
	fmt.Printf("Allocated %d buffers\n", len(buffers))
	
	// Clear references to allow garbage collection
	buffers = nil
	
	// Using pool reduces memory pressure
	fmt.Println("Using buffer pool...")
	for i := 0; i < 10000; i++ {
		buf := bufferPool.Get()
		buf.Write([]byte(fmt.Sprintf("test-%d", i)))
		bufferPool.Put(buf)
	}
	fmt.Printf("Processed 10000 buffers using pool\n")
}
