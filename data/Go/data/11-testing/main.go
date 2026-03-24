package main

import (
	"fmt"
	"go-learning-guide/calculator"
	"go-learning-guide/formatter"
	"go-learning-guide/validator"
)

func main() {
	fmt.Println("=== Testing Examples ===")
	fmt.Println("This directory contains test files.")
	fmt.Println("\nTo run the tests, use these commands:")
	fmt.Println("  go test                    # Run all tests")
	fmt.Println("  go test -v                 # Verbose output")
	fmt.Println("  go test -cover             # With coverage")
	fmt.Println("  go test -bench=.           # Run benchmarks")
	
	// Demonstrate the functions that will be tested
	fmt.Println("\n=== Functions Being Tested ===")
	
	// Calculator functions
	fmt.Println("\nCalculator Functions:")
	fmt.Printf("Add(10, 5) = %d\n", calculator.Add(10, 5))
	fmt.Printf("Multiply(4, 7) = %d\n", calculator.Multiply(4, 7))
	fmt.Printf("IsEven(6) = %t\n", calculator.IsEven(6))
	fmt.Printf("IsOdd(6) = %t\n", calculator.IsOdd(6))
	
	// Formatter functions
	fmt.Println("\nFormatter Functions:")
	fmt.Printf("FormatGreeting('Alice') = %s\n", formatter.FormatGreeting("Alice"))
	fmt.Printf("ToUpperCase('hello') = %s\n", formatter.ToUpperCase("hello"))
	fmt.Printf("FormatPrice(19.99) = %s\n", formatter.FormatPrice(19.99))
	
	// Validator functions
	fmt.Println("\nValidator Functions:")
	email := "test@example.com"
	fmt.Printf("IsValidEmail('%s') = %t\n", email, validator.IsValidEmail(email))
	
	age := 25
	fmt.Printf("IsValidAge(%d) = %t\n", age, validator.IsValidAge(age))
	
	password := "StrongPass123!"
	fmt.Printf("IsStrongPassword('%s') = %t\n", "StrongPass123!", validator.IsStrongPassword(password))
	
	fmt.Println("\n=== Test Files Available ===")
	fmt.Println("1. calculator_test.go - Tests for calculator package")
	fmt.Println("2. formatter_test.go - Tests for formatter package")
	fmt.Println("3. validator_test.go - Tests for validator package")
	fmt.Println("4. example_test.go - Example tests and benchmarks")
	fmt.Println("5. integration_test.go - Integration tests")
	
	fmt.Println("\n=== Testing Concepts Demonstrated ===")
	fmt.Println("• Basic unit tests")
	fmt.Println("• Table-driven tests")
	fmt.Println("• Benchmark tests")
	fmt.Println("• Example tests")
	fmt.Println("• Integration tests")
	fmt.Println("• Test coverage")
	fmt.Println("• Setup and teardown")
}
