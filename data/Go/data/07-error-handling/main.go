package main

import (
	"errors"
	"fmt"
	"os"
	"strconv"
)

func main() {
	fmt.Println("=== Error Handling Examples ===\n")

	// Example 1: Basic error handling
	fmt.Println("1. Basic Error Handling:")
	result, err := divide(10, 2)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Printf("Result: %.2f\n", result)
	}

	result, err = divide(10, 0)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Printf("Result: %.2f\n", result)
	}

	// Example 2: File operations
	fmt.Println("\n2. File Operations:")
	err = readFile("nonexistent.txt")
	if err != nil {
		fmt.Printf("File error: %v\n", err)
	}

	// Example 3: Error wrapping
	fmt.Println("\n3. Error Wrapping:")
	err = processData("invalid")
	if err != nil {
		fmt.Printf("Wrapped error: %v\n", err)
		
		// Check if it's a specific type of error
		if errors.Is(err, ErrInvalidInput) {
			fmt.Println("This is an invalid input error")
		}
	}

	// Example 4: Multiple error returns
	fmt.Println("\n4. Multiple Error Handling:")
	values := []string{"10", "20", "abc", "30"}
	for i, val := range values {
		num, err := strconv.Atoi(val)
		if err != nil {
			fmt.Printf("Value %d (%s) is not a valid number: %v\n", i, val, err)
			continue
		}
		fmt.Printf("Value %d: %d\n", i, num)
	}
}

// divide returns an error when dividing by zero
func divide(a, b float64) (float64, error) {
	if b == 0 {
		return 0, errors.New("cannot divide by zero")
	}
	return a / b, nil
}

// readFile demonstrates file error handling
func readFile(filename string) error {
	content, err := os.ReadFile(filename)
	if err != nil {
		return fmt.Errorf("failed to read file %s: %w", filename, err)
	}
	fmt.Printf("File content: %s\n", content)
	return nil
}

// Custom error type
var ErrInvalidInput = errors.New("invalid input provided")

// processData demonstrates error wrapping
func processData(input string) error {
	if input == "invalid" {
		return fmt.Errorf("processing failed for input '%s': %w", input, ErrInvalidInput)
	}
	fmt.Printf("Successfully processed: %s\n", input)
	return nil
}
