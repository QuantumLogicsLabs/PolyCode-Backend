package main

import (
	"encoding/json"
	"fmt"
	"os"
	"sync"
	"time"
)

// AsyncLogger provides non-blocking logging
type AsyncLogger struct {
	inputChan chan LogEntry
	output    *os.File
	wg        sync.WaitGroup
	done      chan struct{}
	mu        sync.RWMutex
	closed    bool
}

func NewAsyncLogger(bufferSize int) *AsyncLogger {
	logger := &AsyncLogger{
		inputChan: make(chan LogEntry, bufferSize),
		output:    os.Stdout,
		done:      make(chan struct{}),
	}
	
	// Start background worker
	logger.wg.Add(1)
	go logger.worker()
	
	return logger
}

func (l *AsyncLogger) SetOutput(file *os.File) {
	l.mu.Lock()
	defer l.mu.Unlock()
	l.output = file
}

func (l *AsyncLogger) Debug(message string, fields map[string]interface{}) {
	l.log(LevelDebug, message, fields)
}

func (l *AsyncLogger) Info(message string, fields map[string]interface{}) {
	l.log(LevelInfo, message, fields)
}

func (l *AsyncLogger) Warn(message string, fields map[string]interface{}) {
	l.log(LevelWarn, message, fields)
}

func (l *AsyncLogger) Error(message string, fields map[string]interface{}) {
	l.log(LevelError, message, fields)
}

func (l *AsyncLogger) Fatal(message string, fields map[string]interface{}) {
	l.log(LevelFatal, message, fields)
	l.Close()
	os.Exit(1)
}

func (l *AsyncLogger) log(level LogLevel, message string, fields map[string]interface{}) {
	entry := LogEntry{
		Timestamp: time.Now(),
		Level:     level.String(),
		Message:   message,
		Fields:    fields,
	}
	
	select {
	case l.inputChan <- entry:
		// Log queued successfully
	default:
		// Buffer full, drop the log or handle overflow
		fmt.Fprintf(os.Stderr, "Async logger buffer full, dropping log: %s\n", message)
	}
}

func (l *AsyncLogger) worker() {
	defer l.wg.Done()
	
	for {
		select {
		case entry := <-l.inputChan:
			l.writeEntry(entry)
		case <-l.done:
			// Drain remaining entries
			for {
				select {
				case entry := <-l.inputChan:
					l.writeEntry(entry)
				default:
					return
				}
			}
		}
	}
}

func (l *AsyncLogger) writeEntry(entry LogEntry) {
	l.mu.RLock()
	output := l.output
	l.mu.RUnlock()
	
	jsonData, err := json.Marshal(entry)
	if err != nil {
		fmt.Fprintf(os.Stderr, "Failed to marshal log entry: %v\n", err)
		return
	}
	
	fmt.Fprintln(output, string(jsonData))
}

func (l *AsyncLogger) Flush() {
	// Wait for all entries to be processed
	for len(l.inputChan) > 0 {
		time.Sleep(10 * time.Millisecond)
	}
}

func (l *AsyncLogger) Close() {
	l.mu.Lock()
	if l.closed {
		l.mu.Unlock()
		return
	}
	l.closed = true
	l.mu.Unlock()
	
	close(l.done)
	l.wg.Wait()
}

// BufferedLogger with batch writing
type BufferedLogger struct {
	entries    []LogEntry
	bufferSize int
	output     *os.File
	mu         sync.Mutex
	flushTimer *time.Timer
}

func NewBufferedLogger(bufferSize int, flushInterval time.Duration) *BufferedLogger {
	logger := &BufferedLogger{
		entries:    make([]LogEntry, 0, bufferSize),
		bufferSize: bufferSize,
		output:     os.Stdout,
	}
	
	// Start auto-flush timer
	logger.startFlushTimer(flushInterval)
	
	return logger
}

func (l *BufferedLogger) SetOutput(file *os.File) {
	l.mu.Lock()
	defer l.mu.Unlock()
	l.output = file
}

func (l *BufferedLogger) Info(message string, fields map[string]interface{}) {
	l.log(LevelInfo, message, fields)
}

func (l *BufferedLogger) Error(message string, fields map[string]interface{}) {
	l.log(LevelError, message, fields)
}

func (l *BufferedLogger) log(level LogLevel, message string, fields map[string]interface{}) {
	entry := LogEntry{
		Timestamp: time.Now(),
		Level:     level.String(),
		Message:   message,
		Fields:    fields,
	}
	
	l.mu.Lock()
	defer l.mu.Unlock()
	
	l.entries = append(l.entries, entry)
	
	if len(l.entries) >= l.bufferSize {
		l.flush()
	}
}

func (l *BufferedLogger) Flush() {
	l.mu.Lock()
	defer l.mu.Unlock()
	l.flush()
}

func (l *BufferedLogger) flush() {
	if len(l.entries) == 0 {
		return
	}
	
	for _, entry := range l.entries {
		jsonData, err := json.Marshal(entry)
		if err != nil {
			fmt.Fprintf(os.Stderr, "Failed to marshal log entry: %v\n", err)
			continue
		}
		fmt.Fprintln(l.output, string(jsonData))
	}
	
	l.entries = l.entries[:0] // Clear buffer
}

func (l *BufferedLogger) startFlushTimer(interval time.Duration) {
	l.flushTimer = time.AfterFunc(interval, func() {
		l.Flush()
		l.startFlushTimer(interval) // Restart timer
	})
}

func (l *BufferedLogger) Close() {
	l.flushTimer.Stop()
	l.Flush()
}

// PerformanceLogger for measuring operation performance
type PerformanceLogger struct {
	logger Logger
}

func NewPerformanceLogger() *PerformanceLogger {
	return &PerformanceLogger{
		logger: NewStructuredLogger(),
	}
}

func (l *PerformanceLogger) Performance(operation string, startTime time.Time, fields map[string]interface{}) {
	duration := time.Since(startTime)
	
	allFields := MergeFields(fields, map[string]interface{}{
		"operation": operation,
		"duration":  duration.String(),
		"duration_ms": duration.Milliseconds(),
	})
	
	if duration > time.Second {
		l.logger.Warn("Slow operation detected", allFields)
	} else {
		l.logger.Info("Operation completed", allFields)
	}
}

// Logger interface
type Logger interface {
	Debug(message string, fields map[string]interface{})
	Info(message string, fields map[string]interface{})
	Warn(message string, fields map[string]interface{})
	Error(message string, fields map[string]interface{})
	Fatal(message string, fields map[string]interface{})
}

// RateLimitedLogger prevents log spam
type RateLimitedLogger struct {
	logger     Logger
	limits     map[string]*rateLimitEntry
	mu         sync.Mutex
}

type rateLimitEntry struct {
	count    int
	lastTime time.Time
}

func NewRateLimitedLogger(logger Logger, maxMessages int, window time.Duration) *RateLimitedLogger {
	return &RateLimitedLogger{
		logger: logger,
		limits: make(map[string]*rateLimitEntry),
	}
}

func (l *RateLimitedLogger) Info(message string, fields map[string]interface{}) {
	l.logWithRateLimit("info", message, fields)
}

func (l *RateLimitedLogger) Error(message string, fields map[string]interface{}) {
	l.logWithRateLimit("error", message, fields)
}

func (l *RateLimitedLogger) logWithRateLimit(level, message string, fields map[string]interface{}) {
	key := fmt.Sprintf("%s:%s", level, message)
	
	l.mu.Lock()
	defer l.mu.Unlock()
	
	entry, exists := l.limits[key]
	now := time.Now()
	
	if !exists || now.Sub(entry.lastTime) > time.Minute {
		// Reset counter
		l.limits[key] = &rateLimitEntry{
			count:    1,
			lastTime: now,
		}
		l.logger.Info(message, fields)
		return
	}
	
	if entry.count < 10 { // Allow 10 messages per minute
		entry.count++
		l.logger.Info(message, fields)
	} else if entry.count == 10 {
		// First time hitting limit, log a warning
		entry.count++
		l.logger.Warn("Rate limit exceeded, suppressing further messages", map[string]interface{}{
			"message": message,
			"level":   level,
		})
	}
}
