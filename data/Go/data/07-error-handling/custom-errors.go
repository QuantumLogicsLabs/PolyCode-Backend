package main

import (
	"errors"
	"fmt"
	"net"
	"os"
	"time"
)

func main() {
	fmt.Println("=== Custom Error Types ===")
	
	// Basic custom error
	fmt.Println("\n--- Basic Custom Error ---")
	
	err := validateAge(15)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
		fmt.Printf("Error type: %T\n", err)
	}
	
	err = validateAge(25)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Println("Age is valid")
	}
	
	// Custom error with methods
	fmt.Println("\n--- Custom Error with Methods ---")
	
	err = processPayment(-100)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
		
		// Type assertion to access custom methods
		if paymentErr, ok := err.(PaymentError); ok {
			fmt.Printf("Payment ID: %s\n", paymentErr.PaymentID)
			fmt.Printf("Amount: $%.2f\n", paymentErr.Amount)
			fmt.Printf("Is retryable: %t\n", paymentErr.IsRetryable())
			fmt.Printf("Error code: %s\n", paymentErr.ErrorCode())
		}
	}
	
	// Network errors
	fmt.Println("\n--- Network Errors ---")
	
	err = connectToServer("invalid-server")
	if err != nil {
		fmt.Printf("Connection error: %v\n", err)
		
		if netErr, ok := err.(NetworkError); ok {
			fmt.Printf("Server: %s\n", netErr.Server)
			fmt.Printf("Port: %d\n", netErr.Port)
			fmt.Printf("Timeout: %v\n", netErr.Timeout)
			fmt.Printf("Temporary: %t\n", netErr.Temporary())
		}
	}
	
	// File operation errors
	fmt.Println("\n--- File Operation Errors ---")
	
	err = readFile("/nonexistent/file.txt")
	if err != nil {
		fmt.Printf("File error: %v\n", err)
		
		if fileErr, ok := err.(FileError); ok {
			fmt.Printf("File path: %s\n", fileErr.Path)
			fmt.Printf("Operation: %s\n", fileErr.Operation)
			fmt.Printf("Permission issue: %t\n", fileErr.IsPermissionError())
		}
	}
	
	// Validation errors with multiple fields
	fmt.Println("\n--- Validation Errors ---")
	
	user := User{
		Name:  "",
		Email: "invalid-email",
		Age:   15,
	}
	
	err = validateUser(user)
	if err != nil {
		fmt.Printf("Validation error: %v\n", err)
		
		if validationErr, ok := err.(ValidationError); ok {
			fmt.Printf("Field errors:\n")
			for field, errMsg := range validationErr.FieldErrors {
				fmt.Printf("  %s: %s\n", field, errMsg)
			}
		}
	}
	
	// Database errors
	fmt.Println("\n--- Database Errors ---")
	
	err = queryDatabase("invalid_table")
	if err != nil {
		fmt.Printf("Database error: %v\n", err)
		
		if dbErr, ok := err.(DatabaseError); ok {
			fmt.Printf("Query: %s\n", dbErr.Query)
			fmt.Printf("Table: %s\n", dbErr.Table)
			fmt.Printf("Error code: %s\n", dbErr.ErrorCode)
			fmt.Printf("Can retry: %t\n", dbErr.CanRetry())
		}
	}
	
	// Error wrapping with custom errors
	fmt.Println("\n--- Error Wrapping ---")
	
	err = complexOperation()
	if err != nil {
		fmt.Printf("Complex operation error: %v\n", err)
		
		// Unwrap errors
		unwrapped := errors.Unwrap(err)
		if unwrapped != nil {
			fmt.Printf("Unwrapped: %v\n", unwrapped)
		}
		
		// Check for specific error types
		if errors.Is(err, ErrInvalidInput) {
			fmt.Println("Root cause: Invalid input")
		}
	}
	
	// Error aggregation
	fmt.Println("\n--- Error Aggregation ---")
	
	errs := processMultipleItems()
	if len(errs) > 0 {
		fmt.Printf("Multiple errors occurred:\n")
		for i, err := range errs {
			fmt.Printf("  %d: %v\n", i+1, err)
		}
	}
}

// Basic custom error type
type ValidationError struct {
	Field   string
	Message string
}

func (ve ValidationError) Error() string {
	return fmt.Sprintf("validation failed for field '%s': %s", ve.Field, ve.Message)
}

func validateAge(age int) error {
	if age < 18 {
		return ValidationError{
			Field:   "age",
			Message: "must be at least 18 years old",
		}
	}
	return nil
}

// Payment error with methods
type PaymentError struct {
	PaymentID string
	Amount    float64
	Message   string
	Retryable bool
}

func (pe PaymentError) Error() string {
	return fmt.Sprintf("payment %s failed: %s (amount: $%.2f)", pe.PaymentID, pe.Message, pe.Amount)
}

func (pe PaymentError) IsRetryable() bool {
	return pe.Retryable
}

func (pe PaymentError) ErrorCode() string {
	return "PAYMENT_FAILED"
}

func processPayment(amount float64) error {
	if amount <= 0 {
		return PaymentError{
			PaymentID: "PAY123",
			Amount:    amount,
			Message:   "invalid amount",
			Retryable: false,
		}
	}
	return nil
}

// Network error
type NetworkError struct {
	Server  string
	Port    int
	Message string
	Timeout time.Duration
	Temp    bool
}

func (ne NetworkError) Error() string {
	return fmt.Sprintf("network error connecting to %s:%d - %s", ne.Server, ne.Port, ne.Message)
}

func (ne NetworkError) Temporary() bool {
	return ne.Temp
}

func (ne NetworkError) Timeout() bool {
	return ne.Timeout > 0
}

func connectToServer(server string) error {
	if server == "invalid-server" {
		return NetworkError{
			Server:  server,
			Port:    8080,
			Message: "server not found",
			Timeout: 30 * time.Second,
			Temp:    true,
		}
	}
	return nil
}

// File operation error
type FileError struct {
	Path      string
	Operation string
	Message   string
	Err       error
}

func (fe FileError) Error() string {
	return fmt.Sprintf("file error during %s on %s: %s", fe.Operation, fe.Path, fe.Message)
}

func (fe FileError) Unwrap() error {
	return fe.Err
}

func (fe FileError) IsPermissionError() bool {
	return fe.Err != nil && os.IsPermission(fe.Err)
}

func readFile(path string) error {
	_, err := os.ReadFile(path)
	if err != nil {
		return FileError{
			Path:      path,
			Operation: "read",
			Message:   "failed to read file",
			Err:       err,
		}
	}
	return nil
}

// Multi-field validation error
type User struct {
	Name  string
	Email string
	Age   int
}

type ValidationError struct {
	FieldErrors map[string]string
}

func (ve ValidationError) Error() string {
	var messages []string
	for field, msg := range ve.FieldErrors {
		messages = append(messages, fmt.Sprintf("%s: %s", field, msg))
	}
	return fmt.Sprintf("validation failed: %s", strings.Join(messages, ", "))
}

func validateUser(user User) error {
	fieldErrors := make(map[string]string)
	
	if user.Name == "" {
		fieldErrors["name"] = "name cannot be empty"
	}
	
	if !strings.Contains(user.Email, "@") {
		fieldErrors["email"] = "invalid email format"
	}
	
	if user.Age < 18 {
		fieldErrors["age"] = "must be at least 18 years old"
	}
	
	if len(fieldErrors) > 0 {
		return ValidationError{FieldErrors: fieldErrors}
	}
	
	return nil
}

// Database error
type DatabaseError struct {
	Query     string
	Table     string
	Message   string
	ErrorCode string
	Retryable bool
}

func (de DatabaseError) Error() string {
	return fmt.Sprintf("database error on table '%s': %s", de.Table, de.Message)
}

func (de DatabaseError) CanRetry() bool {
	return de.Retryable
}

func queryDatabase(table string) error {
	if table == "invalid_table" {
		return DatabaseError{
			Query:     fmt.Sprintf("SELECT * FROM %s", table),
			Table:     table,
			Message:   "table does not exist",
			ErrorCode: "TABLE_NOT_FOUND",
			Retryable: false,
		}
	}
	return nil
}

// Error wrapping examples
var ErrInvalidInput = errors.New("invalid input")

func complexOperation() error {
	err := validateInput("")
	if err != nil {
		return fmt.Errorf("complex operation failed: %w", err)
	}
	return nil
}

func validateInput(input string) error {
	if input == "" {
		return ErrInvalidInput
	}
	return nil
}

// Error aggregation
func processMultipleItems() []error {
	var errors []error
	
	items := []string{"item1", "", "item3", "item4"}
	
	for i, item := range items {
		if item == "" {
			errors = append(errors, fmt.Errorf("item %d is empty", i+1))
		}
	}
	
	return errors
}

// Advanced error patterns

type ErrorCollector struct {
	errors []error
}

func (ec *ErrorCollector) Add(err error) {
	if err != nil {
		ec.errors = append(ec.errors, err)
	}
}

func (ec *ErrorCollector) HasErrors() bool {
	return len(ec.errors) > 0
}

func (ec *ErrorCollector) Error() string {
	if len(ec.errors) == 0 {
		return ""
	}
	
	var messages []string
	for _, err := range ec.errors {
		messages = append(messages, err.Error())
	}
	
	return fmt.Sprintf("multiple errors occurred: %s", strings.Join(messages, "; "))
}

func (ec *ErrorCollector) Errors() []error {
	return ec.errors
}

// Retry mechanism with custom errors
type RetryableError struct {
	Message    string
	Attempts   int
	MaxRetries int
	Err        error
}

func (re RetryableError) Error() string {
	return fmt.Sprintf("retryable error: %s (attempt %d/%d)", re.Message, re.Attempts, re.MaxRetries)
}

func (re RetryableError) Unwrap() error {
	return re.Err
}

func (re RetryableError) ShouldRetry() bool {
	return re.Attempts < re.MaxRetries
}

func retryOperation(operation func() error, maxRetries int) error {
	var lastErr error
	
	for attempt := 1; attempt <= maxRetries; attempt++ {
		err := operation()
		if err == nil {
			return nil
		}
		
		lastErr = err
		
		// Check if error is retryable
		if retryableErr, ok := err.(RetryableError); ok {
			if !retryableErr.ShouldRetry() {
				return retryableErr
			}
		} else {
			// Non-retryable error
			return err
		}
		
		fmt.Printf("Attempt %d failed, retrying...\n", attempt)
		time.Sleep(time.Duration(attempt) * time.Second)
	}
	
	return fmt.Errorf("operation failed after %d attempts: %w", maxRetries, lastErr)
}

// Context-aware errors
type ContextError struct {
	Context string
	Err     error
	Time    time.Time
}

func (ce ContextError) Error() string {
	return fmt.Sprintf("context error in %s at %s: %v", ce.Context, ce.Time.Format("15:04:05"), ce.Err)
}

func (ce ContextError) Unwrap() error {
	return ce.Err
}

func withContext(context string, operation func() error) error {
	err := operation()
	if err != nil {
		return ContextError{
			Context: context,
			Err:     err,
			Time:    time.Now(),
		}
	}
	return nil
}

// Demonstrate advanced error patterns
func demonstrateAdvancedErrors() {
	fmt.Println("\n--- Advanced Error Patterns ---")
	
	// Error collector
	collector := &ErrorCollector{}
	collector.Add(validateAge(15))
	collector.Add(validateAge(-5))
	collector.Add(validateAge(25)) // This won't add an error
	
	if collector.HasErrors() {
		fmt.Printf("Collected errors: %v\n", collector.Error())
	}
	
	// Retry mechanism
	fmt.Println("\n--- Retry Mechanism ---")
	
	attempt := 0
	failingOperation := func() error {
		attempt++
		if attempt < 3 {
			return RetryableError{
				Message:    "temporary failure",
				Attempts:   attempt,
				MaxRetries: 3,
				Err:        fmt.Errorf("operation failed"),
			}
		}
		return nil
	}
	
	err := retryOperation(failingOperation, 3)
	if err != nil {
		fmt.Printf("Final error: %v\n", err)
	} else {
		fmt.Println("Operation succeeded after retries")
	}
	
	// Context-aware errors
	fmt.Println("\n--- Context-Aware Errors ---")
	
	err = withContext("user validation", func() error {
		return validateAge(15)
	})
	
	if err != nil {
		fmt.Printf("Context error: %v\n", err)
	}
}
