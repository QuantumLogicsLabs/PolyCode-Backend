package main

import (
	"encoding/json"
	"fmt"
	"os"
	"time"
)

// LogLevel represents different logging levels
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

// StructuredLogger implements structured logging
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

// Field helpers
func StringField(key, value string) map[string]interface{} {
	return map[string]interface{}{key: value}
}

func IntField(key string, value int) map[string]interface{} {
	return map[string]interface{}{key: value}
}

func FloatField(key string, value float64) map[string]interface{} {
	return map[string]interface{}{key: value}
}

func BoolField(key string, value bool) map[string]interface{} {
	return map[string]interface{}{key: value}
}

func DurationField(key string, value time.Duration) map[string]interface{} {
	return map[string]interface{}{key: value.String()}
}

func ErrorField(err error) map[string]interface{} {
	if err == nil {
		return map[string]interface{}{"error": nil}
	}
	return map[string]interface{}{
		"error": err.Error(),
		"type":  fmt.Sprintf("%T", err),
	}
}

// Merge fields helper
func MergeFields(fields ...map[string]interface{}) map[string]interface{} {
	merged := make(map[string]interface{})
	for _, f := range fields {
		for k, v := range f {
			merged[k] = v
		}
	}
	return merged
}

// Logger with context support
type ContextualLogger struct {
	*StructuredLogger
	context map[string]interface{}
}

func NewContextualLogger() *ContextualLogger {
	return &ContextualLogger{
		StructuredLogger: NewStructuredLogger(),
		context:         make(map[string]interface{}),
	}
}

func (l *ContextualLogger) WithContext(fields map[string]interface{}) *ContextualLogger {
	newContext := make(map[string]interface{})
	for k, v := range l.context {
		newContext[k] = v
	}
	for k, v := range fields {
		newContext[k] = v
	}
	
	return &ContextualLogger{
		StructuredLogger: l.StructuredLogger,
		context:         newContext,
	}
}

func (l *ContextualLogger) Debug(message string, fields map[string]interface{}) {
	merged := MergeFields(l.context, fields)
	l.StructuredLogger.Debug(message, merged)
}

func (l *ContextualLogger) Info(message string, fields map[string]interface{}) {
	merged := MergeFields(l.context, fields)
	l.StructuredLogger.Info(message, merged)
}

func (l *ContextualLogger) Warn(message string, fields map[string]interface{}) {
	merged := MergeFields(l.context, fields)
	l.StructuredLogger.Warn(message, merged)
}

func (l *ContextualLogger) Error(message string, fields map[string]interface{}) {
	merged := MergeFields(l.context, fields)
	l.StructuredLogger.Error(message, merged)
}

func (l *ContextualLogger) Fatal(message string, fields map[string]interface{}) {
	merged := MergeFields(l.context, fields)
	l.StructuredLogger.Fatal(message, merged)
}

// Logger with levels and filtering
type LevelLogger struct {
	*StructuredLogger
	filters []func(LogEntry) bool
}

func NewLevelLogger(level LogLevel) *LevelLogger {
	return &LevelLogger{
		StructuredLogger: NewStructuredLogger(),
		filters:         []func(LogEntry) bool{},
	}
}

func (l *LevelLogger) AddFilter(filter func(LogEntry) bool) {
	l.filters = append(l.filters, filter)
}

func (l *LevelLogger) shouldLog(entry LogEntry) bool {
	for _, filter := range l.filters {
		if !filter(entry) {
			return false
		}
	}
	return true
}

func (l *LevelLogger) log(level LogLevel, message string, fields map[string]interface{}) {
	if level < l.level {
		return
	}
	
	entry := LogEntry{
		Timestamp: time.Now(),
		Level:     level.String(),
		Message:   message,
		Fields:    fields,
	}
	
	if !l.shouldLog(entry) {
		return
	}
	
	jsonData, err := json.Marshal(entry)
	if err != nil {
		fmt.Fprintf(os.Stderr, "Failed to marshal log entry: %v\n", err)
		return
	}
	
	fmt.Fprintln(l.output, string(jsonData))
}

// Example filters
func FilterByLevel(minLevel LogLevel) func(LogEntry) bool {
	return func(entry LogEntry) bool {
		switch entry.Level {
		case "DEBUG":
			return minLevel <= LevelDebug
		case "INFO":
			return minLevel <= LevelInfo
		case "WARN":
			return minLevel <= LevelWarn
		case "ERROR":
			return minLevel <= LevelError
		case "FATAL":
			return minLevel <= LevelFatal
		default:
			return false
		}
	}
}

func FilterByField(key string, value interface{}) func(LogEntry) bool {
	return func(entry LogEntry) bool {
		if entry.Fields == nil {
			return false
		}
		fieldValue, exists := entry.Fields[key]
		return exists && fieldValue == value
	}
}

func FilterByMessageContains(substring string) func(LogEntry) bool {
	return func(entry LogEntry) bool {
		return contains(entry.Message, substring)
	}
}

func contains(s, substr string) bool {
	return len(s) >= len(substr) && (s == substr || len(s) > len(substr) && 
		(s[:len(substr)] == substr || s[len(s)-len(substr):] == substr || 
		 containsSubstring(s, substr)))
}

func containsSubstring(s, substr string) bool {
	for i := 0; i <= len(s)-len(substr); i++ {
		if s[i:i+len(substr)] == substr {
			return true
		}
	}
	return false
}
