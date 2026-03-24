package main

import (
	"errors"
	"fmt"
	"os"
	"strings"
	"time"
)

func main() {
	fmt.Println("=== Error Handling Patterns ===")
	
	// Sentinel errors
	fmt.Println("\n--- Sentinel Errors ---")
	
	err := processFile("config.txt")
	if errors.Is(err, ErrNotFound) {
		fmt.Println("File not found error")
	} else if errors.Is(err, ErrPermission) {
		fmt.Println("Permission denied error")
	} else if err != nil {
		fmt.Printf("Other error: %v\n", err)
	}
	
	// Error wrapping patterns
	fmt.Println("\n--- Error Wrapping Patterns ---")
	
	err = complexDataProcessing()
	if err != nil {
		fmt.Printf("Processing error: %v\n", err)
		
		// Unwrap to find root cause
		rootCause := findRootCause(err)
		fmt.Printf("Root cause: %v\n", rootCause)
	}
	
	// Error aggregation pattern
	fmt.Println("\n--- Error Aggregation Pattern ---")
	
	errs := batchProcess([]string{"item1", "item2", "", "item4"})
	if len(errs) > 0 {
		fmt.Printf("Batch processing errors:\n")
		for i, err := range errs {
			fmt.Printf("  %d: %v\n", i+1, err)
		}
		
		// Combine all errors into one
		combined := combineErrors(errs...)
		fmt.Printf("Combined error: %v\n", combined)
	}
	
	// Retry pattern
	fmt.Println("\n--- Retry Pattern ---")
	
	err = retryOperationWithBackoff()
	if err != nil {
		fmt.Printf("Operation failed after retries: %v\n", err)
	} else {
		fmt.Println("Operation succeeded")
	}
	
	// Circuit breaker pattern
	fmt.Println("\n--- Circuit Breaker Pattern ---")
	
	cb := NewCircuitBreaker(3, time.Minute)
	
	for i := 0; i < 5; i++ {
		err := cb.Call(func() error {
			return simulateServiceCall(i < 2) // First 2 calls fail
		})
		
		fmt.Printf("Call %d: ", i+1)
		if err != nil {
			fmt.Printf("Failed (%v)\n", err)
		} else {
			fmt.Println("Success")
		}
	}
	
	// Error context pattern
	fmt.Println("\n--- Error Context Pattern ---")
	
	err = processUserInput("invalid")
	if err != nil {
		// Add context at each layer
		err = addContext(err, "user processing")
		err = addContext(err, "request handler")
		
		fmt.Printf("Contextual error: %v\n", err)
		
		// Extract context chain
		contexts := extractContexts(err)
		fmt.Printf("Context chain: %v\n", contexts)
	}
	
	// Validation error pattern
	fmt.Println("\n--- Validation Error Pattern ---")
	
	user := UserData{
		Name:  "",
		Email: "invalid-email",
		Age:   15,
	}
	
	err = validateUserData(user)
	if err != nil {
		fmt.Printf("Validation errors:\n")
		
		if validationErr, ok := err.(*ValidationError); ok {
			for field, errors := range validationErr.Errors {
				fmt.Printf("  %s:\n", field)
				for _, errMsg := range errors {
					fmt.Printf("    - %s\n", errMsg)
				}
			}
		}
	}
	
	// Resource cleanup pattern
	fmt.Println("\n--- Resource Cleanup Pattern ---")
	
	err = processWithCleanup()
	if err != nil {
		fmt.Printf("Processing error: %v\n", err)
	}
	
	// Timeout pattern
	fmt.Println("\n--- Timeout Pattern ---")
	
	err = processWithTimeout()
	if err != nil {
		if errors.Is(err, ErrTimeout) {
			fmt.Println("Operation timed out")
		} else {
			fmt.Printf("Other error: %v\n", err)
		}
	}
}

// Sentinel errors
var (
	ErrNotFound    = errors.New("not found")
	ErrPermission  = errors.New("permission denied")
	ErrInvalid     = errors.New("invalid input")
	ErrTimeout     = errors.New("operation timed out")
	ErrRateLimit   = errors.New("rate limit exceeded")
)

func processFile(filename string) error {
	if filename == "" {
		return ErrInvalid
	}
	
	if filename == "config.txt" {
		return ErrNotFound
	}
	
	if filename == "protected.txt" {
		return ErrPermission
	}
	
	return nil
}

// Error wrapping
func complexDataProcessing() error {
	// Simulate nested operation errors
	err := validateInput("")
	if err != nil {
		return fmt.Errorf("validation failed: %w", err)
	}
	
	err = processData()
	if err != nil {
		return fmt.Errorf("processing failed: %w", err)
	}
	
	err = saveResult()
	if err != nil {
		return fmt.Errorf("save failed: %w", err)
	}
	
	return nil
}

func validateInput(input string) error {
	if input == "" {
		return ErrInvalid
	}
	return nil
}

func processData() error {
	return errors.New("processing error")
}

func saveResult() error {
	return errors.New("save error")
}

func findRootCause(err error) error {
	for {
		unwrapped := errors.Unwrap(err)
		if unwrapped == nil {
			return err
		}
		err = unwrapped
	}
}

// Error aggregation
func batchProcess(items []string) []error {
	var errors []error
	
	for i, item := range items {
		if err := processItem(item); err != nil {
			errors = append(errors, fmt.Errorf("item %d: %w", i, err))
		}
	}
	
	return errors
}

func processItem(item string) error {
	if item == "" {
		return ErrInvalid
	}
	return nil
}

func combineErrors(errs ...error) error {
	if len(errs) == 0 {
		return nil
	}
	
	var messages []string
	for _, err := range errs {
		messages = append(messages, err.Error())
	}
	
	return fmt.Errorf("multiple errors: %s", strings.Join(messages, "; "))
}

// Retry with exponential backoff
func retryOperationWithBackoff() error {
	var lastErr error
	
	maxRetries := 3
	baseDelay := 100 * time.Millisecond
	
	for attempt := 0; attempt < maxRetries; attempt++ {
		err := flakyOperation()
		if err == nil {
			return nil
		}
		
		lastErr = err
		
		if attempt < maxRetries-1 {
			delay := time.Duration(1<<uint(attempt)) * baseDelay
			fmt.Printf("Attempt %d failed, retrying in %v...\n", attempt+1, delay)
			time.Sleep(delay)
		}
	}
	
	return fmt.Errorf("operation failed after %d attempts: %w", maxRetries, lastErr)
}

func flakyOperation() error {
	// Simulate a flaky operation that might fail
	if time.Now().Unix()%2 == 0 {
		return errors.New("temporary failure")
	}
	return nil
}

// Circuit breaker pattern
type CircuitBreaker struct {
	maxFailures  int
	resetTimeout time.Duration
	failures     int
	lastFailTime time.Time
	state        string // "closed", "open", "half-open"
}

func NewCircuitBreaker(maxFailures int, resetTimeout time.Duration) *CircuitBreaker {
	return &CircuitBreaker{
		maxFailures:  maxFailures,
		resetTimeout: resetTimeout,
		state:        "closed",
	}
}

func (cb *CircuitBreaker) Call(operation func() error) error {
	if cb.state == "open" {
		if time.Since(cb.lastFailTime) > cb.resetTimeout {
			cb.state = "half-open"
		} else {
			return errors.New("circuit breaker is open")
		}
	}
	
	err := operation()
	
	if err != nil {
		cb.failures++
		cb.lastFailTime = time.Now()
		
		if cb.failures >= cb.maxFailures {
			cb.state = "open"
		}
		
		return err
	}
	
	// Success
	if cb.state == "half-open" {
		cb.reset()
	}
	
	return nil
}

func (cb *CircuitBreaker) reset() {
	cb.failures = 0
	cb.state = "closed"
}

func simulateServiceCall(shouldFail bool) error {
	if shouldFail {
		return errors.New("service unavailable")
	}
	return nil
}

// Error context pattern
type ContextualError struct {
	Message string
	Context []string
	Err     error
}

func (ce *ContextualError) Error() string {
	if len(ce.Context) == 0 {
		return fmt.Sprintf("%s: %v", ce.Message, ce.Err)
	}
	
	contextStr := strings.Join(ce.Context, " -> ")
	return fmt.Sprintf("%s: %s: %v", contextStr, ce.Message, ce.Err)
}

func (ce *ContextualError) Unwrap() error {
	return ce.Err
}

func addContext(err error, context string) error {
	if ce, ok := err.(*ContextualError); ok {
		newContext := append([]string{context}, ce.Context...)
		return &ContextualError{
			Message: ce.Message,
			Context: newContext,
			Err:     ce.Err,
		}
	}
	
	return &ContextualError{
		Message: "operation failed",
		Context: []string{context},
		Err:     err,
	}
}

func extractContexts(err error) []string {
	if ce, ok := err.(*ContextualError); ok {
		return ce.Context
	}
	return nil
}

func processUserInput(input string) error {
	if input == "invalid" {
		return ErrInvalid
	}
	return nil
}

// Validation error pattern
type ValidationError struct {
	Errors map[string][]string
}

func (ve *ValidationError) Error() string {
	var messages []string
	for field, fieldErrors := range ve.Errors {
		messages = append(messages, fmt.Sprintf("%s: %s", field, strings.Join(fieldErrors, ", ")))
	}
	return fmt.Sprintf("validation failed: %s", strings.Join(messages, "; "))
}

type UserData struct {
	Name  string
	Email string
	Age   int
}

func validateUserData(user UserData) error {
	validationErrors := make(map[string][]string)
	
	if user.Name == "" {
		validationErrors["name"] = append(validationErrors["name"], "name is required")
	}
	
	if len(user.Name) < 2 {
		validationErrors["name"] = append(validationErrors["name"], "name must be at least 2 characters")
	}
	
	if !strings.Contains(user.Email, "@") {
		validationErrors["email"] = append(validationErrors["email"], "invalid email format")
	}
	
	if user.Age < 18 {
		validationErrors["age"] = append(validationErrors["age"], "must be at least 18 years old")
	}
	
	if user.Age > 120 {
		validationErrors["age"] = append(validationErrors["age"], "age seems unrealistic")
	}
	
	if len(validationErrors) > 0 {
		return &ValidationError{Errors: validationErrors}
	}
	
	return nil
}

// Resource cleanup pattern
func processWithCleanup() error {
	// Simulate opening resources
	file, err := os.Open("nonexistent.txt")
	if err != nil {
		return fmt.Errorf("failed to open file: %w", err)
	}
	defer file.Close()
	
	// Simulate database connection
	db := &MockDB{connected: true}
	defer db.Close()
	
	// Simulate network connection
	conn := &MockConnection{connected: true}
	defer conn.Close()
	
	// Process with potential error
	if err := processDataWithResources(); err != nil {
		return fmt.Errorf("processing failed: %w", err)
	}
	
	return nil
}

func processDataWithResources() error {
	return errors.New("processing error")
}

type MockDB struct {
	connected bool
}

func (db *MockDB) Close() {
	if db.connected {
		fmt.Println("Closing database connection")
		db.connected = false
	}
}

type MockConnection struct {
	connected bool
}

func (conn *MockConnection) Close() {
	if conn.connected {
		fmt.Println("Closing network connection")
		conn.connected = false
	}
}

// Timeout pattern
func processWithTimeout() error {
	done := make(chan error, 1)
	
	go func() {
		time.Sleep(2 * time.Second) // Simulate long operation
		done <- nil
	}()
	
	select {
	case err := <-done:
		return err
	case <-time.After(1 * time.Second):
		return ErrTimeout
	}
}

// Advanced error handling patterns

// Error handler middleware
type ErrorHandler struct {
	handlers map[string]func(error) error
}

func NewErrorHandler() *ErrorHandler {
	return &ErrorHandler{
		handlers: make(map[string]func(error) error),
	}
}

func (eh *ErrorHandler) Handle(errorType string, handler func(error) error) {
	eh.handlers[errorType] = handler
}

func (eh *ErrorHandler) Process(err error) error {
	errorType := getErrorType(err)
	
	if handler, exists := eh.handlers[errorType]; exists {
		return handler(err)
	}
	
	return err
}

func getErrorType(err error) string {
	if errors.Is(err, ErrNotFound) {
		return "not_found"
	}
	if errors.Is(err, ErrPermission) {
		return "permission"
	}
	if errors.Is(err, ErrTimeout) {
		return "timeout"
	}
	return "unknown"
}

// Error recovery pattern
func recoverFromPanic() (err error) {
	defer func() {
		if r := recover(); r != nil {
			switch v := r.(type) {
			case error:
				err = fmt.Errorf("panic recovered: %w", v)
			case string:
				err = fmt.Errorf("panic recovered: %s", v)
			default:
				err = fmt.Errorf("panic recovered: %v", v)
			}
		}
	}()
	
	// Simulate a panic
	panic("something went wrong")
}

// Demonstrate advanced patterns
func demonstrateAdvancedPatterns() {
	fmt.Println("\n--- Advanced Error Handling Patterns ---")
	
	// Error handler middleware
	errorHandler := NewErrorHandler()
	errorHandler.Handle("not_found", func(err error) error {
		return fmt.Errorf("resource not found, please check the path")
	})
	
	err := processFile("config.txt")
	if err != nil {
		handledErr := errorHandler.Process(err)
		fmt.Printf("Handled error: %v\n", handledErr)
	}
	
	// Panic recovery
	fmt.Println("\n--- Panic Recovery ---")
	err = recoverFromPanic()
	if err != nil {
		fmt.Printf("Recovered error: %v\n", err)
	}
}
