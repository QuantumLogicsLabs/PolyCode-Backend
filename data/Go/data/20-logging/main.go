package main

import (
	"context"
	"log"
	"os"
	"os/signal"
	"syscall"
	"time"
)

func main() {
	// Test different logging approaches
	testBasicLogging()
	testStructuredLogging()
	testContextualLogging()
	testLoggingLevels()
	testAsyncLogging()
	testRotatingLogs()
	testMultiLogger()
	testErrorHandling()
	testPerformanceLogging()
}

func testBasicLogging() {
	log.Println("=== Basic Logging Demo ===")
	
	// Standard library logger
	log.SetFlags(log.LstdFlags | log.Lshortfile)
	log.Println("This is a basic log message")
	log.Printf("This is a formatted log message: %s", "Hello, World!")
	log.Fatal("This would terminate the program")
}

func testStructuredLogging() {
	log.Println("=== Structured Logging Demo ===")
	
	logger := NewStructuredLogger()
	logger.Info("User login", map[string]interface{}{
		"user_id": "12345",
		"ip":      "192.168.1.1",
		"method":  "POST",
	})
	
	logger.Error("Database connection failed", map[string]interface{}{
		"error":     "connection timeout",
		"database":  "users",
		"attempt":   3,
		"duration":  "5s",
	})
}

func testContextualLogging() {
	log.Println("=== Contextual Logging Demo ===")
	
	logger := NewContextualLogger()
	
	ctx := context.Background()
	ctx = WithUserID(ctx, "user123")
	ctx = WithRequestID(ctx, "req-456")
	ctx = WithSessionID(ctx, "sess-789")
	
	logger.InfoContext(ctx, "Processing request", map[string]interface{}{
		"action": "create_order",
		"amount": 99.99,
	})
}

func testLoggingLevels() {
	log.Println("=== Logging Levels Demo ===")
	
	logger := NewLevelLogger(LevelDebug)
	
	logger.Debug("Debug message - detailed info")
	logger.Info("Info message - general info")
	logger.Warn("Warning message - potential issue")
	logger.Error("Error message - error occurred")
	logger.Fatal("Fatal message - program terminating")
}

func testAsyncLogging() {
	log.Println("=== Async Logging Demo ===")
	
	logger := NewAsyncLogger(1000) // 1000 buffer size
	defer logger.Close()
	
	for i := 0; i < 100; i++ {
		logger.Info("Async log message", map[string]interface{}{
			"iteration": i,
			"timestamp": time.Now(),
		})
	}
	
	logger.Flush()
}

func testRotatingLogs() {
	log.Println("=== Rotating Logs Demo ===")
	
	logger := NewRotatingLogger("app.log", 1024, 3) // 1KB max, 3 backups
	
	for i := 0; i < 50; i++ {
		logger.Info("Log message for rotation test", map[string]interface{}{
			"message_id": i,
			"data":       "This is some sample data to fill up the log file and trigger rotation.",
		})
	}
}

func testMultiLogger() {
	log.Println("=== Multi Logger Demo ===")
	
	fileLogger := NewFileLogger("app.log")
	consoleLogger := NewConsoleLogger()
	
	multiLogger := NewMultiLogger(fileLogger, consoleLogger)
	
	multiLogger.Info("This goes to both file and console", map[string]interface{}{
		"type": "multi_output",
	})
}

func testErrorHandling() {
	log.Println("=== Error Handling Demo ===")
	
	logger := NewErrorAwareLogger()
	
	err := &ValidationError{
		Field:   "email",
		Message: "Invalid email format",
		Code:    "INVALID_EMAIL",
	}
	
	logger.Error("Validation failed", map[string]interface{}{
		"error": err,
		"user":  "john@example.com",
	})
}

func testPerformanceLogging() {
	log.Println("=== Performance Logging Demo ===")
	
	perfLogger := NewPerformanceLogger()
	
	start := time.Now()
	
	// Simulate some work
	time.Sleep(100 * time.Millisecond)
	
	perfLogger.Performance("Database query", start, map[string]interface{}{
		"query": "SELECT * FROM users WHERE id = ?",
		"rows":  42,
	})
}

// Graceful shutdown example
func runWithGracefulShutdown() {
	logger := NewStructuredLogger()
	
	// Setup graceful shutdown
	quit := make(chan os.Signal, 1)
	signal.Notify(quit, syscall.SIGINT, syscall.SIGTERM)
	
	// Start background worker
	go func() {
		for {
			select {
			case <-quit:
				logger.Info("Shutting down gracefully", nil)
				return
			default:
				logger.Info("Working...", nil)
				time.Sleep(1 * time.Second)
			}
		}
	}()
	
	<-quit
	logger.Info("Application stopped", nil)
}
