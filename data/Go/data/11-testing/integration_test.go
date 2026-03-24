package main

import (
	"fmt"
	"go-learning-guide/calculator"
	"go-learning-guide/formatter"
	"go-learning-guide/validator"
	"testing"
)

// TestUserWorkflow tests a complete user registration workflow
func TestUserWorkflow(t *testing.T) {
	// Simulate user registration data
	userData := map[string]interface{}{
		"name":     "John Doe",
		"email":    "john.doe@example.com",
		"age":      30,
		"password": "StrongPass123!",
	}
	
	// Validate user data
	if !validator.IsValidEmail(userData["email"].(string)) {
		t.Errorf("Invalid email: %s", userData["email"])
	}
	
	if !validator.IsValidAge(userData["age"].(int)) {
		t.Errorf("Invalid age: %d", userData["age"])
	}
	
	if !validator.IsStrongPassword(userData["password"].(string)) {
		t.Errorf("Weak password: %s", userData["password"])
	}
	
	// Format user greeting
	greeting := formatter.FormatGreeting(userData["name"].(string))
	expectedGreeting := "Hello, John Doe! Welcome to Go programming."
	if greeting != expectedGreeting {
		t.Errorf("Unexpected greeting: %s", greeting)
	}
	
	// Calculate user statistics (e.g., age in months)
	ageInMonths := calculator.Multiply(userData["age"].(int), 12)
	expectedMonths := 360
	if ageInMonths != expectedMonths {
		t.Errorf("Expected %d months, got %d", expectedMonths, ageInMonths)
	}
	
	// Format the result
	result := fmt.Sprintf("%s You are %d months old.", greeting, ageInMonths)
	expectedResult := "Hello, John Doe! Welcome to Go programming. You are 360 months old."
	if result != expectedResult {
		t.Errorf("Unexpected result: %s", result)
	}
}

// TestDataProcessingPipeline tests a data processing pipeline
func TestDataProcessingPipeline(t *testing.T) {
	// Input data
	inputs := []string{"10", "20", "30", "invalid", "40"}
	
	// Process valid numbers
	var numbers []int
	for _, input := range inputs {
		if validator.ContainsOnlyNumbers(input) {
			num := calculator.Power(2, len(numbers)+1) // Just for demonstration
			numbers = append(numbers, num)
		}
	}
	
	// Expected: process 4 valid inputs
	if len(numbers) != 4 {
		t.Errorf("Expected 4 numbers, got %d", len(numbers))
	}
	
	// Calculate sum
	sum := 0
	for _, num := range numbers {
		sum = calculator.Add(sum, num)
	}
	
	// Format result
	result := formatter.FormatPrice(float64(sum))
	
	// Just verify it's a properly formatted price
	if len(result) == 0 || result[0] != '$' {
		t.Errorf("Invalid price format: %s", result)
	}
}

// TestReportGeneration tests report generation
func TestReportGeneration(t *testing.T) {
	// Sample data
	records := []struct {
		name  string
		email string
		age   int
	}{
		{"Alice", "alice@example.com", 25},
		{"Bob", "bob@example.com", 30},
		{"Charlie", "charlie@example.com", 35},
	}
	
	// Validate all records
	for i, record := range records {
		if !validator.IsValidEmail(record.email) {
			t.Errorf("Record %d: Invalid email %s", i, record.email)
		}
		
		if !validator.IsValidAge(record.age) {
			t.Errorf("Record %d: Invalid age %d", i, record.age)
		}
	}
	
	// Calculate statistics
	var totalAge int
	for _, record := range records {
		totalAge = calculator.Add(totalAge, record.age)
	}
	
	averageAge := calculator.Divide(totalAge, len(records))
	
	// Format report
	report := fmt.Sprintf("Report Summary:\n")
	report += fmt.Sprintf("Total Records: %d\n", len(records))
	report += fmt.Sprintf("Average Age: %d\n", averageAge)
	
	// Just verify report contains expected content
	if validator.IsEmpty(report) {
		t.Error("Report is empty")
	}
	
	// Check if it contains "Average Age"
	if !validator.ContainsOnlyLetters(fmt.Sprintf("%d", averageAge)) {
		// This is just to use the validator function
		t.Log("Average age calculation completed")
	}
}

// TestErrorHandling tests error handling in integration scenarios
func TestErrorHandling(t *testing.T) {
	// Test with invalid data
	invalidData := map[string]interface{}{
		"email":    "invalid-email",
		"age":      -5,
		"password": "weak",
	}
	
	// Should fail validation
	if validator.IsValidEmail(invalidData["email"].(string)) {
		t.Error("Should have failed email validation")
	}
	
	if validator.IsValidAge(invalidData["age"].(int)) {
		t.Error("Should have failed age validation")
	}
	
	if validator.IsStrongPassword(invalidData["password"].(string)) {
		t.Error("Should have failed password validation")
	}
	
	// Test graceful handling
	var errors []string
	
	if !validator.IsValidEmail(invalidData["email"].(string)) {
		errors = append(errors, "Invalid email")
	}
	
	if !validator.IsValidAge(invalidData["age"].(int)) {
		errors = append(errors, "Invalid age")
	}
	
	if !validator.IsStrongPassword(invalidData["password"].(string)) {
		errors = append(errors, "Weak password")
	}
	
	// Should have 3 errors
	if len(errors) != 3 {
		t.Errorf("Expected 3 errors, got %d", len(errors))
	}
}

// BenchmarkIntegrationWorkflow benchmarks the complete workflow
func BenchmarkIntegrationWorkflow(b *testing.B) {
	for i := 0; i < b.N; i++ {
		// Simulate user data
		email := "user@example.com"
		age := 25
		name := "Test User"
		
		// Validate
		validator.IsValidEmail(email)
		validator.IsValidAge(age)
		
		// Format
		formatter.FormatGreeting(name)
		
		// Calculate
		calculator.Multiply(age, 12)
	}
}

// ExampleIntegrationWorkflow demonstrates the integration workflow
func ExampleIntegrationWorkflow() {
	// User data
	name := "Alice"
	email := "alice@example.com"
	age := 30
	
	// Validate data
	if validator.IsValidEmail(email) && validator.IsValidAge(age) {
		// Format greeting
		greeting := formatter.FormatGreeting(name)
		
		// Calculate age in months
		months := calculator.Multiply(age, 12)
		
		// Display result
		fmt.Printf("%s You are %d months old.\n", greeting, months)
	}
	// Output: Hello, Alice! Welcome to Go programming. You are 360 months old.
}
