package main

import (
	"context"
	"encoding/json"
	"fmt"
	"os"
	"path/filepath"
	"sync"
	"time"
)

// LogLevel definitions
type LogLevel int

const (
	LevelDebug LogLevel = iota
	LevelInfo
	LevelWarn
	LevelError
	LevelFatal
)

func (l LogLevel) String() string {
	switch l {
	case LevelDebug:
		return "DEBUG"
	case LevelInfo:
		return "INFO"
	case LevelWarn:
		return "WARN"
	case LevelError:
		return "ERROR"
	case LevelFatal:
		return "FATAL"
	default:
		return "UNKNOWN"
	}
}

// LogEntry represents a structured log entry
type LogEntry struct {
	Timestamp time.Time              `json:"timestamp"`
	Level     string                 `json:"level"`
	Message   string                 `json:"message"`
	Fields    map[string]interface{} `json:"fields,omitempty"`
}

// StructuredLogger implements basic structured logging
type StructuredLogger struct {
	level  LogLevel
	output *os.File
}

func NewStructuredLogger() *StructuredLogger {
	return &StructuredLogger{
		level:  LevelInfo,
		output: os.Stdout,
	}
}

func (l *StructuredLogger) SetLevel(level LogLevel) {
	l.level = level
}

func (l *StructuredLogger) SetOutput(file *os.File) {
	l.output = file
}

func (l *StructuredLogger) Debug(message string, fields map[string]interface{}) {
	l.log(LevelDebug, message, fields)
}

func (l *StructuredLogger) Info(message string, fields map[string]interface{}) {
	l.log(LevelInfo, message, fields)
}

func (l *StructuredLogger) Warn(message string, fields map[string]interface{}) {
	l.log(LevelWarn, message, fields)
}

func (l *StructuredLogger) Error(message string, fields map[string]interface{}) {
	l.log(LevelError, message, fields)
}

func (l *StructuredLogger) Fatal(message string, fields map[string]interface{}) {
	l.log(LevelFatal, message, fields)
	os.Exit(1)
}

func (l *StructuredLogger) log(level LogLevel, message string, fields map[string]interface{}) {
	if level < l.level {
		return
	}

	entry := LogEntry{
		Timestamp: time.Now(),
		Level:     level.String(),
		Message:   message,
		Fields:    fields,
	}

	jsonData, err := json.Marshal(entry)
	if err != nil {
		fmt.Fprintf(os.Stderr, "Failed to marshal log entry: %v\n", err)
		return
	}

	fmt.Fprintln(l.output, string(jsonData))
}

// Helper to combine field maps
func MergeFields(fields ...map[string]interface{}) map[string]interface{} {
	merged := make(map[string]interface{})
	for _, f := range fields {
		for k, v := range f {
			merged[k] = v
		}
	}
	return merged
}

// Context Logging keys and helpers
type contextKey string

const (
	UserIDKey        contextKey = "user_id"
	RequestIDKey     contextKey = "request_id"
	SessionIDKey     contextKey = "session_id"
	TraceIDKey       contextKey = "trace_id"
	CorrelationIDKey contextKey = "correlation_id"
)

func WithUserID(ctx context.Context, userID string) context.Context {
	return context.WithValue(ctx, UserIDKey, userID)
}

func WithRequestID(ctx context.Context, requestID string) context.Context {
	return context.WithValue(ctx, RequestIDKey, requestID)
}

func WithSessionID(ctx context.Context, sessionID string) context.Context {
	return context.WithValue(ctx, SessionIDKey, sessionID)
}

type ContextualLogger struct {
	*StructuredLogger
}

func NewContextualLogger() *ContextualLogger {
	return &ContextualLogger{
		StructuredLogger: NewStructuredLogger(),
	}
}

func (l *ContextualLogger) extractContextFields(ctx context.Context) map[string]interface{} {
	fields := make(map[string]interface{})
	if u := ctx.Value(UserIDKey); u != nil {
		fields["user_id"] = u
	}
	if r := ctx.Value(RequestIDKey); r != nil {
		fields["request_id"] = r
	}
	if s := ctx.Value(SessionIDKey); s != nil {
		fields["session_id"] = s
	}
	return fields
}

func (l *ContextualLogger) InfoContext(ctx context.Context, message string, fields map[string]interface{}) {
	merged := MergeFields(l.extractContextFields(ctx), fields)
	l.StructuredLogger.Info(message, merged)
}

func (l *ContextualLogger) ErrorContext(ctx context.Context, message string, fields map[string]interface{}) {
	merged := MergeFields(l.extractContextFields(ctx), fields)
	l.StructuredLogger.Error(message, merged)
}

// Level-filtering Logger
type LevelLogger struct {
	*StructuredLogger
}

func NewLevelLogger(level LogLevel) *LevelLogger {
	l := &LevelLogger{StructuredLogger: NewStructuredLogger()}
	l.level = level
	return l
}

// Asynchronous Logger
type AsyncLogger struct {
	inputChan chan LogEntry
	output    *os.File
	wg        sync.WaitGroup
	done      chan struct{}
}

func NewAsyncLogger(bufferSize int) *AsyncLogger {
	al := &AsyncLogger{
		inputChan: make(chan LogEntry, bufferSize),
		output:    os.Stdout,
		done:      make(chan struct{}),
	}
	al.wg.Add(1)
	go func() {
		defer al.wg.Done()
		for {
			select {
			case entry := <-al.inputChan:
				data, _ := json.Marshal(entry)
				fmt.Fprintln(al.output, string(data))
			case <-al.done:
				for len(al.inputChan) > 0 {
					entry := <-al.inputChan
					data, _ := json.Marshal(entry)
					fmt.Fprintln(al.output, string(data))
				}
				return
			}
		}
	}()
	return al
}

func (l *AsyncLogger) Info(message string, fields map[string]interface{}) {
	l.inputChan <- LogEntry{Timestamp: time.Now(), Level: "INFO", Message: message, Fields: fields}
}

func (l *AsyncLogger) Flush() {
	time.Sleep(50 * time.Millisecond)
}

func (l *AsyncLogger) Close() {
	close(l.done)
	l.wg.Wait()
}

// Rotating Logger
type RotatingLogger struct {
	filename string
	file     *os.File
}

func NewRotatingLogger(filename string, maxSize int64, maxBackups int) *RotatingLogger {
	dir := filepath.Dir(filename)
	if dir != "." {
		_ = os.MkdirAll(dir, 0755)
	}
	f, _ := os.OpenFile(filename, os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0644)
	return &RotatingLogger{filename: filename, file: f}
}

func (l *RotatingLogger) Info(message string, fields map[string]interface{}) {
	entry := LogEntry{Timestamp: time.Now(), Level: "INFO", Message: message, Fields: fields}
	data, _ := json.Marshal(entry)
	if l.file != nil {
		l.file.Write(append(data, '\n'))
	}
}

// Multi-Output Logger
type Logger interface {
	Info(message string, fields map[string]interface{})
}

type FileLogger struct {
	*StructuredLogger
}

func NewFileLogger(filename string) *FileLogger {
	f, _ := os.OpenFile(filename, os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0644)
	sl := NewStructuredLogger()
	sl.SetOutput(f)
	return &FileLogger{StructuredLogger: sl}
}

type ConsoleLogger struct {
	*StructuredLogger
}

func NewConsoleLogger() *ConsoleLogger {
	return &ConsoleLogger{StructuredLogger: NewStructuredLogger()}
}

type MultiLogger struct {
	loggers []Logger
}

func NewMultiLogger(loggers ...Logger) *MultiLogger {
	return &MultiLogger{loggers: loggers}
}

func (ml *MultiLogger) Info(message string, fields map[string]interface{}) {
	for _, l := range ml.loggers {
		l.Info(message, fields)
	}
}

// Performance Logger
type PerformanceLogger struct {
	logger *StructuredLogger
}

func NewPerformanceLogger() *PerformanceLogger {
	return &PerformanceLogger{logger: NewStructuredLogger()}
}

func (l *PerformanceLogger) Performance(operation string, startTime time.Time, fields map[string]interface{}) {
	duration := time.Since(startTime)
	allFields := MergeFields(fields, map[string]interface{}{
		"operation":   operation,
		"duration":    duration.String(),
		"duration_ms": duration.Milliseconds(),
	})
	l.logger.Info("Operation completed", allFields)
}

// Fixed ValidationError struct
type ValidationError struct {
	Field        string
	ErrorMessage string
	ErrorCode    string
}

func (e *ValidationError) Error() string {
	return fmt.Sprintf("Validation error on %s: %s", e.Field, e.ErrorMessage)
}

func (e *ValidationError) Code() string {
	return e.ErrorCode
}

type ErrorAwareLogger struct {
	*ContextualLogger
}

func NewErrorAwareLogger() *ErrorAwareLogger {
	return &ErrorAwareLogger{ContextualLogger: NewContextualLogger()}
}

func (l *ErrorAwareLogger) Error(message string, fields map[string]interface{}) {
	l.ContextualLogger.StructuredLogger.Error(message, fields)
}

// Main Runner
func main() {
	fmt.Println("=== 1. Basic & Structured Logging Demo ===")
	structured := NewStructuredLogger()
	structured.Info("User login", map[string]interface{}{
		"user_id": "12345",
		"ip":      "192.168.1.1",
		"method":  "POST",
	})

	fmt.Println("\n=== 2. Contextual Logging Demo ===")
	contextual := NewContextualLogger()
	ctx := WithUserID(context.Background(), "user123")
	ctx = WithRequestID(ctx, "req-456")
	ctx = WithSessionID(ctx, "sess-789")
	contextual.InfoContext(ctx, "Processing request", map[string]interface{}{
		"action": "create_order",
		"amount": 99.99,
	})

	fmt.Println("\n=== 3. Level Logging Demo ===")
	levelLogger := NewLevelLogger(LevelWarn)
	levelLogger.Debug("Debug message - skipped", nil)
	levelLogger.Warn("Warning message - potential issue", nil)

	fmt.Println("\n=== 4. Async Logging Demo ===")
	asyncLogger := NewAsyncLogger(100)
	for i := 1; i <= 3; i++ {
		asyncLogger.Info("Async log entry", map[string]interface{}{"iteration": i})
	}
	asyncLogger.Flush()
	asyncLogger.Close()

	fmt.Println("\n=== 5. Rotating Logs Demo ===")
	rotLogger := NewRotatingLogger("app.log", 1024, 3)
	rotLogger.Info("Log message for rotation test", map[string]interface{}{"data": "sample log data"})

	fmt.Println("\n=== 6. Multi-Logger Demo ===")
	multi := NewMultiLogger(NewConsoleLogger(), NewFileLogger("app.log"))
	multi.Info("Logging simultaneously to console and file", map[string]interface{}{"status": "success"})

	fmt.Println("\n=== 7. Error Aware Logging Demo ===")
	errLogger := NewErrorAwareLogger()
	vErr := &ValidationError{
		Field:        "email",
		ErrorMessage: "Invalid email format",
		ErrorCode:    "INVALID_EMAIL",
	}
	errLogger.Error("Validation failed", map[string]interface{}{
		"error": vErr.Error(),
		"code":  vErr.Code(),
	})

	fmt.Println("\n=== 8. Performance Logging Demo ===")
	perf := NewPerformanceLogger()
	start := time.Now()
	time.Sleep(30 * time.Millisecond)
	perf.Performance("Database query", start, map[string]interface{}{
		"query": "SELECT * FROM users WHERE id = 1",
		"rows":  1,
	})
}
