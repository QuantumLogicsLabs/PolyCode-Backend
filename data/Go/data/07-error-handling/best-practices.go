package main

import (
	"errors"
	"fmt"
	"io"
	"os"
	"strings"
)

func main() {
	fmt.Println("=== Error Handling Best Practices ===")
	
	// Practice 1: Handle errors immediately
	fmt.Println("\n--- Practice 1: Handle Errors Immediately ---")
	
	// Bad: Ignoring errors
	fmt.Println("❌ Bad: Ignoring errors")
	ignoreErrors()
	
	// Good: Handling errors
	fmt.Println("✅ Good: Handling errors")
	handleErrors()
	
	// Practice 2: Use proper error wrapping
	fmt.Println("\n--- Practice 2: Proper Error Wrapping ---")
	
	// Bad: Losing context
	fmt.Println("❌ Bad: Losing context")
	err := badErrorWrapping()
	fmt.Printf("Error: %v\n", err)
	
	// Good: Preserving context
	fmt.Println("✅ Good: Preserving context")
	err = goodErrorWrapping()
	fmt.Printf("Error: %v\n", err)
	
	// Practice 3: Use sentinel errors appropriately
	fmt.Println("\n--- Practice 3: Sentinel Errors ---")
	
	err = processUser("admin")
	if errors.Is(err, ErrUserNotFound) {
		fmt.Println("User not found - check credentials")
	} else if errors.Is(err, ErrPermissionDenied) {
		fmt.Println("Permission denied - contact admin")
	}
	
	// Practice 4: Create meaningful error messages
	fmt.Println("\n--- Practice 4: Meaningful Error Messages ---")
	
	// Bad: Vague errors
	fmt.Println("❌ Bad: Vague errors")
	err = badValidationError("", "")
	fmt.Printf("Error: %v\n", err)
	
	// Good: Specific, actionable errors
	fmt.Println("✅ Good: Specific errors")
	err = goodValidationError("", "")
	fmt.Printf("Error: %v\n", err)
	
	// Practice 5: Handle resource cleanup properly
	fmt.Println("\n--- Practice 5: Resource Cleanup ---")
	
	// Bad: Resource leaks
	fmt.Println("❌ Bad: Resource leaks")
	badResourceHandling()
	
	// Good: Proper cleanup
	fmt.Println("✅ Good: Proper cleanup")
	goodResourceHandling()
	
	// Practice 6: Use appropriate error types
	fmt.Println("\n--- Practice 6: Appropriate Error Types ---")
	
	// Demonstrate different error types for different scenarios
	demonstrateErrorTypes()
	
	// Practice 7: Error handling in concurrent code
	fmt.Println("\n--- Practice 7: Concurrent Error Handling ---")
	
	err = concurrentProcessing()
	if err != nil {
		fmt.Printf("Concurrent processing error: %v\n", err)
	}
	
	// Practice 8: Logging and monitoring
	fmt.Println("\n--- Practice 8: Logging and Monitoring ---")
	
	err = processWithLogging("test@example.com")
	if err != nil {
		fmt.Printf("Processing completed with error: %v\n", err)
	}
	
	// Practice 9: Error handling in APIs
	fmt.Println("\n--- Practice 9: API Error Handling ---")
	
	response := apiRequest("/api/users/123")
	fmt.Printf("API Response: %+v\n", response)
	
	// Practice 10: Testing error paths
	fmt.Println("\n--- Practice 10: Testing Error Paths ---")
	
	testErrorPaths()
}

// Sentinel errors
var (
	ErrUserNotFound     = errors.New("user not found")
	ErrPermissionDenied = errors.New("permission denied")
	ErrInvalidInput     = errors.New("invalid input")
)

// Practice 1: Error handling comparison
func ignoreErrors() {
	// ❌ BAD: Ignoring errors
	file, _ := os.Open("config.txt")
	data := make([]byte, 100)
	file.Read(data) // Error ignored
	file.Close()    // Error ignored
	fmt.Println("Processed file (errors ignored)")
}

func handleErrors() {
	// ✅ GOOD: Handling errors
	file, err := os.Open("config.txt")
	if err != nil {
		fmt.Printf("Failed to open file: %v\n", err)
		return
	}
	defer file.Close()
	
	data := make([]byte, 100)
	_, err = file.Read(data)
	if err != nil && err != io.EOF {
		fmt.Printf("Failed to read file: %v\n", err)
		return
	}
	
	fmt.Println("File processed successfully")
}

// Practice 2: Error wrapping comparison
func badErrorWrapping() error {
	err := validateEmail("invalid-email")
	if err != nil {
		return errors.New("validation failed") // Lost context
	}
	return nil
}

func goodErrorWrapping() error {
	err := validateEmail("invalid-email")
	if err != nil {
		return fmt.Errorf("email validation failed: %w", err) // Preserves context
	}
	return nil
}

func validateEmail(email string) error {
	if !strings.Contains(email, "@") {
		return errors.New("email must contain @ symbol")
	}
	return nil
}

// Practice 3: Sentinel error usage
func processUser(username string) error {
	user, err := findUser(username)
	if err != nil {
		return err
	}
	
	if !user.IsActive {
		return ErrPermissionDenied
	}
	
	return nil
}

type User struct {
	Username string
	IsActive bool
}

func findUser(username string) (*User, error) {
	if username == "nonexistent" {
		return nil, ErrUserNotFound
	}
	return &User{Username: username, IsActive: true}, nil
}

// Practice 4: Error message quality
func badValidationError(name, email string) error {
	// ❌ BAD: Vague error message
	if name == "" || email == "" {
		return errors.New("invalid input")
	}
	return nil
}

func goodValidationError(name, email string) error {
	// ✅ GOOD: Specific, actionable error messages
	var errors []string
	
	if name == "" {
		errors = append(errors, "name is required")
	} else if len(name) < 2 {
		errors = append(errors, "name must be at least 2 characters")
	}
	
	if email == "" {
		errors = append(errors, "email is required")
	} else if !strings.Contains(email, "@") {
		errors = append(errors, "email must be valid")
	}
	
	if len(errors) > 0 {
		return fmt.Errorf("validation failed: %s", strings.Join(errors, ", "))
	}
	
	return nil
}

// Practice 5: Resource management
func badResourceHandling() {
	// ❌ BAD: Potential resource leaks
	file1, _ := os.Open("file1.txt")
	file2, _ := os.Open("file2.txt")
	
	// If an error occurs here, files might not be closed
	_, err := file1.Read(make([]byte, 100))
	if err != nil {
		return // Files not closed!
	}
	
	file1.Close()
	file2.Close()
}

func goodResourceHandling() {
	// ✅ GOOD: Proper resource cleanup
	file1, err := os.Open("file1.txt")
	if err != nil {
		fmt.Printf("Failed to open file1: %v\n", err)
		return
	}
	defer file1.Close()
	
	file2, err := os.Open("file2.txt")
	if err != nil {
		fmt.Printf("Failed to open file2: %v\n", err)
		return
	}
	defer file2.Close()
	
	// Process files
	_, err = file1.Read(make([]byte, 100))
	if err != nil && err != io.EOF {
		fmt.Printf("Failed to read file1: %v\n", err)
		return
	}
	
	fmt.Println("Files processed successfully")
}

// Practice 6: Different error types
type BusinessError struct {
	Code    string
	Message string
}

func (be BusinessError) Error() string {
	return fmt.Sprintf("business error [%s]: %s", be.Code, be.Message)
}

type SystemError struct {
	Component string
	Err       error
}

func (se SystemError) Error() string {
	return fmt.Sprintf("system error in %s: %v", se.Component, se.Err)
}

func (se SystemError) Unwrap() error {
	return se.Err
}

func demonstrateErrorTypes() {
	// Business logic error
	err := processBusinessLogic()
	if err != nil {
		fmt.Printf("Business error: %v\n", err)
	}
	
	// System error
	err = processSystemOperation()
	if err != nil {
		fmt.Printf("System error: %v\n", err)
		
		// Can unwrap to get underlying error
		unwrapped := errors.Unwrap(err)
		fmt.Printf("Underlying error: %v\n", unwrapped)
	}
}

func processBusinessLogic() error {
	return BusinessError{
		Code:    "INSUFFICIENT_BALANCE",
		Message: "Account balance is too low for this transaction",
	}
}

func processSystemOperation() error {
	return SystemError{
		Component: "database",
		Err:       errors.New("connection timeout"),
	}
}

// Practice 7: Concurrent error handling
func concurrentProcessing() error {
	type result struct {
		data string
		err  error
	}
	
	results := make(chan result, 3)
	
	// Process multiple items concurrently
	items := []string{"item1", "item2", "item3"}
	for _, item := range items {
		go func(i string) {
			data, err := processItem(i)
			results <- result{data: data, err: err}
		}(item)
	}
	
	var errors []error
	var successful []string
	
	// Collect results
	for i := 0; i < len(items); i++ {
		res := <-results
		if res.err != nil {
			errors = append(errors, res.err)
		} else {
			successful = append(successful, res.data)
		}
	}
	
	if len(errors) > 0 {
		return fmt.Errorf("processing completed with %d errors: %v", len(errors), errors)
	}
	
	fmt.Printf("Successfully processed: %v\n", successful)
	return nil
}

func processItem(item string) (string, error) {
	if item == "item2" {
		return "", fmt.Errorf("failed to process %s", item)
	}
	return fmt.Sprintf("processed_%s", item), nil
}

// Practice 8: Logging and monitoring
type Logger struct {
	level string
}

func (l *Logger) Info(message string) {
	fmt.Printf("[INFO] %s\n", message)
}

func (l *Logger) Error(message string, err error) {
	fmt.Printf("[ERROR] %s: %v\n", message, err)
}

func processWithLogging(email string) error {
	logger := &Logger{level: "INFO"}
	
	logger.Info("Starting user processing")
	
	user, err := findUserByEmail(email)
	if err != nil {
		logger.Error("Failed to find user", err)
		return fmt.Errorf("user lookup failed: %w", err)
	}
	
	logger.Info(fmt.Sprintf("Found user: %s", user.Username))
	
	err = updateUserLastLogin(user)
	if err != nil {
		logger.Error("Failed to update login time", err)
		return fmt.Errorf("login update failed: %w", err)
	}
	
	logger.Info("User processing completed successfully")
	return nil
}

func findUserByEmail(email string) (*User, error) {
	if email == "notfound@example.com" {
		return nil, ErrUserNotFound
	}
	return &User{Username: "testuser", IsActive: true}, nil
}

func updateUserLastLogin(user *User) error {
	// Simulate database update
	if user.Username == "erroruser" {
		return errors.New("database connection failed")
	}
	return nil
}

// Practice 9: API error handling
type APIResponse struct {
	Success bool        `json:"success"`
	Data    interface{} `json:"data,omitempty"`
	Error   *APIError   `json:"error,omitempty"`
}

type APIError struct {
	Code    string `json:"code"`
	Message string `json:"message"`
	Details string `json:"details,omitempty"`
}

func apiRequest(endpoint string) APIResponse {
	switch endpoint {
	case "/api/users/123":
		return APIResponse{
			Success: true,
			Data:    map[string]interface{}{"id": 123, "name": "John Doe"},
		}
	case "/api/users/999":
		return APIResponse{
			Success: false,
			Error: &APIError{
				Code:    "USER_NOT_FOUND",
				Message: "User not found",
				Details: "No user exists with ID 999",
			},
		}
	case "/api/users/invalid":
		return APIResponse{
			Success: false,
			Error: &APIError{
				Code:    "INVALID_FORMAT",
				Message: "Invalid user ID format",
				Details: "User ID must be a number",
			},
		}
	default:
		return APIResponse{
			Success: false,
			Error: &APIError{
				Code:    "INTERNAL_ERROR",
				Message: "Internal server error",
			},
		}
	}
}

// Practice 10: Testing error paths
func testErrorPaths() {
	fmt.Println("Testing error paths...")
	
	// Test user not found
	err := processUser("nonexistent")
	if err != nil && errors.Is(err, ErrUserNotFound) {
		fmt.Println("✅ User not found error handled correctly")
	}
	
	// Test permission denied
	err = processUser("inactive")
	if err != nil && errors.Is(err, ErrPermissionDenied) {
		fmt.Println("✅ Permission denied error handled correctly")
	}
	
	// Test validation
	err = goodValidationError("", "")
	if err != nil {
		fmt.Println("✅ Validation error handled correctly")
	}
	
	// Test error wrapping
	err = goodErrorWrapping()
	if err != nil {
		fmt.Println("✅ Error wrapping works correctly")
	}
}

// Additional best practices

// Error grouping and categorization
type ErrorCategory int

const (
	CategoryUser ErrorCategory = iota
	CategorySystem
	CategoryNetwork
	CategoryBusiness
)

type CategorizedError struct {
	Category ErrorCategory
	Err      error
}

func (ce CategorizedError) Error() string {
	return fmt.Sprintf("[%s] %v", ce.Category, ce.Err)
}

func (ce CategorizedError) Unwrap() error {
	return ce.Err
}

// Error metrics and monitoring
type ErrorMetrics struct {
	TotalErrors    int
	ErrorsByType   map[string]int
	ErrorsByHour   map[int]int
	LastError      error
	LastErrorTime  string
}

func (em *ErrorMetrics) Record(err error) {
	em.TotalErrors++
	em.LastError = err
	em.LastErrorTime = fmt.Sprintf("%v", err)
	
	errorType := getErrorType(err)
	em.ErrorsByType[errorType]++
	
	hour := 1 // Simulate current hour
	em.ErrorsByHour[hour]++
}

// Graceful degradation
func processWithGracefulDegradation() (result string, err error) {
	// Try primary method
	result, err = primaryProcessing()
	if err == nil {
		return result, nil
	}
	
	// Fall back to secondary method
	fmt.Println("Primary method failed, trying fallback...")
	result, err = fallbackProcessing()
	if err == nil {
		return result, nil
	}
	
	// Last resort
	fmt.Println("Fallback failed, using minimal functionality...")
	return minimalProcessing(), nil
}

func primaryProcessing() (string, error) {
	return "", errors.New("primary service unavailable")
}

func fallbackProcessing() (string, error) {
	return "", errors.New("fallback service unavailable")
}

func minimalProcessing() string {
	return "minimal result"
}

// Demonstrate additional practices
func demonstrateAdditionalPractices() {
	fmt.Println("\n--- Additional Best Practices ---")
	
	// Error categorization
	err := BusinessError{Code: "VALIDATION", Message: "Invalid data"}
	categorizedErr := CategorizedError{Category: CategoryBusiness, Err: err}
	fmt.Printf("Categorized error: %v\n", categorizedErr)
	
	// Error metrics
	metrics := &ErrorMetrics{
		ErrorsByType: make(map[string]int),
		ErrorsByHour: make(map[int]int),
	}
	
	metrics.Record(err)
	metrics.Record(SystemError{Component: "database", Err: errors.New("timeout")})
	
	fmt.Printf("Error metrics - Total: %d, Types: %v\n", 
		metrics.TotalErrors, metrics.ErrorsByType)
	
	// Graceful degradation
	result, err := processWithGracefulDegradation()
	if err != nil {
		fmt.Printf("Processing failed: %v\n", err)
	} else {
		fmt.Printf("Processing result: %s\n", result)
	}
}
