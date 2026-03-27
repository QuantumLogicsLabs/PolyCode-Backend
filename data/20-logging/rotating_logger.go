package main

import (
	"encoding/json"
	"fmt"
	"io"
	"os"
	"path/filepath"
	"sync"
	"time"
)

// RotatingLogger implements log rotation
type RotatingLogger struct {
	filename    string
	maxSize     int64  // Max size in bytes
	maxBackups  int    // Max number of backup files
	currentSize int64
	file        *os.File
	mu          sync.Mutex
}

func NewRotatingLogger(filename string, maxSize int64, maxBackups int) *RotatingLogger {
	logger := &RotatingLogger{
		filename:   filename,
		maxSize:    maxSize,
		maxBackups: maxBackups,
	}
	
	// Open current log file
	if err := logger.openFile(); err != nil {
		fmt.Fprintf(os.Stderr, "Failed to open log file: %v\n", err)
	}
	
	return logger
}

func (l *RotatingLogger) openFile() error {
	file, err := os.OpenFile(l.filename, os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0644)
	if err != nil {
		return err
	}
	
	// Get current file size
	stat, err := file.Stat()
	if err != nil {
		file.Close()
		return err
	}
	
	l.file = file
	l.currentSize = stat.Size()
	
	return nil
}

func (l *RotatingLogger) Info(message string, fields map[string]interface{}) {
	l.log(LevelInfo, message, fields)
}

func (l *RotatingLogger) Error(message string, fields map[string]interface{}) {
	l.log(LevelError, message, fields)
}

func (l *RotatingLogger) log(level LogLevel, message string, fields map[string]interface{}) {
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
	
	jsonData = append(jsonData, '\n')
	
	l.mu.Lock()
	defer l.mu.Unlock()
	
	// Check if rotation is needed
	if l.currentSize+int64(len(jsonData)) > l.maxSize {
		if err := l.rotate(); err != nil {
			fmt.Fprintf(os.Stderr, "Failed to rotate log: %v\n", err)
			return
		}
	}
	
	// Write to file
	n, err := l.file.Write(jsonData)
	if err != nil {
		fmt.Fprintf(os.Stderr, "Failed to write log entry: %v\n", err)
		return
	}
	
	l.currentSize += int64(n)
}

func (l *RotatingLogger) rotate() error {
	// Close current file
	if err := l.file.Close(); err != nil {
		return err
	}
	
	// Move current file to backup
	backupName := fmt.Sprintf("%s.%s", l.filename, time.Now().Format("20060102-150405"))
	if err := os.Rename(l.filename, backupName); err != nil {
		return err
	}
	
	// Clean up old backups
	if err := l.cleanupOldBackups(); err != nil {
		fmt.Fprintf(os.Stderr, "Failed to cleanup old backups: %v\n", err)
	}
	
	// Open new file
	return l.openFile()
}

func (l *RotatingLogger) cleanupOldBackups() error {
	dir := filepath.Dir(l.filename)
	base := filepath.Base(l.filename)
	
	files, err := filepath.Glob(filepath.Join(dir, base+".*"))
	if err != nil {
		return err
	}
	
	if len(files) <= l.maxBackups {
		return nil
	}
	
	// Sort files by modification time (oldest first)
	type fileInfo struct {
		name    string
		modTime time.Time
	}
	
	var fileInfos []fileInfo
	for _, file := range files {
		stat, err := os.Stat(file)
		if err != nil {
			continue
		}
		fileInfos = append(fileInfos, fileInfo{
			name:    file,
			modTime: stat.ModTime(),
		})
	}
	
	// Simple bubble sort by modification time
	for i := 0; i < len(fileInfos); i++ {
		for j := 0; j < len(fileInfos)-1-i; j++ {
			if fileInfos[j].modTime.After(fileInfos[j+1].modTime) {
				fileInfos[j], fileInfos[j+1] = fileInfos[j+1], fileInfos[j]
			}
		}
	}
	
	// Remove excess files
	toRemove := len(fileInfos) - l.maxBackups
	for i := 0; i < toRemove; i++ {
		if err := os.Remove(fileInfos[i].name); err != nil {
			fmt.Fprintf(os.Stderr, "Failed to remove old backup %s: %v\n", fileInfos[i].name, err)
		}
	}
	
	return nil
}

func (l *RotatingLogger) Close() error {
	l.mu.Lock()
	defer l.mu.Unlock()
	
	return l.file.Close()
}

// TimeBasedRotatingLogger rotates logs based on time
type TimeBasedRotatingLogger struct {
	filename    string
	interval    time.Duration
	currentFile *os.File
	startTime   time.Time
	mu          sync.Mutex
}

func NewTimeBasedRotatingLogger(filename string, interval time.Duration) *TimeBasedRotatingLogger {
	logger := &TimeBasedRotatingLogger{
		filename:  filename,
		interval:  interval,
		startTime: time.Now(),
	}
	
	if err := logger.openCurrentFile(); err != nil {
		fmt.Fprintf(os.Stderr, "Failed to open log file: %v\n", err)
	}
	
	// Start rotation checker
	go logger.rotationChecker()
	
	return logger
}

func (l *TimeBasedRotatingLogger) openCurrentFile() error {
	timestamp := l.startTime.Format("20060102-150405")
	filename := fmt.Sprintf("%s.%s", l.filename, timestamp)
	
	file, err := os.OpenFile(filename, os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0644)
	if err != nil {
		return err
	}
	
	l.currentFile = file
	return nil
}

func (l *TimeBasedRotatingLogger) rotationChecker() {
	ticker := time.NewTicker(time.Minute)
	defer ticker.Stop()
	
	for range ticker.C {
		l.mu.Lock()
		if time.Since(l.startTime) >= l.interval {
			l.rotate()
			l.startTime = time.Now()
		}
		l.mu.Unlock()
	}
}

func (l *TimeBasedRotatingLogger) Info(message string, fields map[string]interface{}) {
	l.log(LevelInfo, message, fields)
}

func (l *TimeBasedRotatingLogger) Error(message string, fields map[string]interface{}) {
	l.log(LevelError, message, fields)
}

func (l *TimeBasedRotatingLogger) log(level LogLevel, message string, fields map[string]interface{}) {
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
	
	jsonData = append(jsonData, '\n')
	
	l.mu.Lock()
	defer l.mu.Unlock()
	
	if l.currentFile == nil {
		return
	}
	
	_, err = l.currentFile.Write(jsonData)
	if err != nil {
		fmt.Fprintf(os.Stderr, "Failed to write log entry: %v\n", err)
	}
}

func (l *TimeBasedRotatingLogger) rotate() error {
	if l.currentFile != nil {
		l.currentFile.Close()
	}
	
	return l.openCurrentFile()
}

func (l *TimeBasedRotatingLogger) Close() error {
	l.mu.Lock()
	defer l.mu.Unlock()
	
	if l.currentFile != nil {
		return l.currentFile.Close()
	}
	
	return nil
}

// MultiWriter writes to multiple outputs
type MultiWriter struct {
	writers []io.Writer
	mu      sync.Mutex
}

func NewMultiWriter(writers ...io.Writer) *MultiWriter {
	return &MultiWriter{
		writers: writers,
	}
}

func (mw *MultiWriter) Write(p []byte) (n int, err error) {
	mw.mu.Lock()
	defer mw.mu.Unlock()
	
	for _, writer := range mw.writers {
		n, err = writer.Write(p)
		if err != nil {
			return n, err
		}
	}
	
	return len(p), nil
}

func (mw *MultiWriter) AddWriter(writer io.Writer) {
	mw.mu.Lock()
	defer mw.mu.Unlock()
	
	mw.writers = append(mw.writers, writer)
}

func (mw *MultiWriter) RemoveWriter(writer io.Writer) {
	mw.mu.Lock()
	defer mw.mu.Unlock()
	
	for i, w := range mw.writers {
		if w == writer {
			mw.writers = append(mw.writers[:i], mw.writers[i+1:]...)
			break
		}
	}
}

// MultiLogger writes to multiple loggers
type MultiLogger struct {
	loggers []Logger
}

func NewMultiLogger(loggers ...Logger) *MultiLogger {
	return &MultiLogger{
		loggers: loggers,
	}
}

func (ml *MultiLogger) Debug(message string, fields map[string]interface{}) {
	for _, logger := range ml.loggers {
		logger.Debug(message, fields)
	}
}

func (ml *MultiLogger) Info(message string, fields map[string]interface{}) {
	for _, logger := range ml.loggers {
		logger.Info(message, fields)
	}
}

func (ml *MultiLogger) Warn(message string, fields map[string]interface{}) {
	for _, logger := range ml.loggers {
		logger.Warn(message, fields)
	}
}

func (ml *MultiLogger) Error(message string, fields map[string]interface{}) {
	for _, logger := range ml.loggers {
		logger.Error(message, fields)
	}
}

func (ml *MultiLogger) Fatal(message string, fields map[string]interface{}) {
	for _, logger := range ml.loggers {
		logger.Fatal(message, fields)
	}
}

// FileLogger writes to a file
type FileLogger struct {
	file *os.File
}

func NewFileLogger(filename string) *FileLogger {
	file, err := os.OpenFile(filename, os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0644)
	if err != nil {
		fmt.Fprintf(os.Stderr, "Failed to open file logger: %v\n", err)
		return nil
	}
	
	return &FileLogger{file: file}
}

func (fl *FileLogger) Info(message string, fields map[string]interface{}) {
	fl.log(LevelInfo, message, fields)
}

func (fl *FileLogger) Error(message string, fields map[string]interface{}) {
	fl.log(LevelError, message, fields)
}

func (fl *FileLogger) log(level LogLevel, message string, fields map[string]interface{}) {
	entry := LogEntry{
		Timestamp: time.Now(),
		Level:     level.String(),
		Message:   message,
		Fields:    fields,
	}
	
	jsonData, err := json.Marshal(entry)
	if err != nil {
		return
	}
	
	jsonData = append(jsonData, '\n')
	fl.file.Write(jsonData)
}

func (fl *FileLogger) Close() error {
	if fl.file != nil {
		return fl.file.Close()
	}
	return nil
}

// ConsoleLogger writes to console
type ConsoleLogger struct {
	*StructuredLogger
}

func NewConsoleLogger() *ConsoleLogger {
	return &ConsoleLogger{
		StructuredLogger: NewStructuredLogger(),
	}
}
