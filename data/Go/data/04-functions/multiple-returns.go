package main

import (
	"fmt"
	"math"
)

func main() {
	fmt.Println("=== Multiple Return Values ===")
	
	// Function with two return values
	sum, diff := calculate(10, 3)
	fmt.Printf("Sum: %d, Difference: %d\n", sum, diff)
	
	// Function with named return values
	area, perimeter := rectangle(5, 3)
	fmt.Printf("Area: %.1f, Perimeter: %.1f\n", area, perimeter)
	
	// Function returning error
	result, err := safeDivide(10, 2)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Printf("Result: %.2f\n", result)
	}
	
	result, err = safeDivide(10, 0)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Printf("Result: %.2f\n", result)
	}
	
	// Function returning three values
	quotient, remainder, err := divideWithRemainder(17, 5)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Printf("Quotient: %d, Remainder: %d\n", quotient, remainder)
	}
	
	// Ignoring return values
	onlySum, _ := calculate(20, 8)
	fmt.Printf("Only sum: %d\n", onlySum)
	
	// Function returning a function
	adder := createAdder(10)
	result2 := adder(5)
	fmt.Printf("Adder result: %d\n", result2)
	
	// Function returning multiple types
	name, age, isActive := getUserInfo()
	fmt.Printf("User: %s, Age: %d, Active: %t\n", name, age, isActive)
}

// Function with two return values
func calculate(a, b int) (int, int) {
	sum := a + b
	diff := a - b
	return sum, diff
}

// Function with named return values
func rectangle(width, height float64) (area, perimeter float64) {
	area = width * height
	perimeter = 2 * (width + height)
	return // naked return - returns named variables
}

// Function returning error
func safeDivide(a, b float64) (float64, error) {
	if b == 0 {
		return 0, fmt.Errorf("cannot divide by zero")
	}
	return a / b, nil
}

// Function returning three values
func divideWithRemainder(dividend, divisor int) (int, int, error) {
	if divisor == 0 {
		return 0, 0, fmt.Errorf("cannot divide by zero")
	}
	quotient := dividend / divisor
	remainder := dividend % divisor
	return quotient, remainder, nil
}

// Function returning a function (closure)
func createAdder(base int) func(int) int {
	return func(x int) int {
		return base + x
	}
}

// Function returning multiple different types
func getUserInfo() (string, int, bool) {
	return "John Doe", 30, true
}

// Advanced example: function that returns validation results
func validateInput(username, password string) (bool, string, []string) {
	var errors []string
	
	if len(username) < 3 {
		errors = append(errors, "username too short")
	}
	
	if len(password) < 8 {
		errors = append(errors, "password too short")
	}
	
	isValid := len(errors) == 0
	message := "validation completed"
	
	return isValid, message, errors
}

// Function returning coordinates
func getCoordinates() (float64, float64, float64) {
	// x, y, z coordinates
	return 10.5, 20.3, 15.7
}

// Function returning min, max, and average
func stats(numbers []float64) (min, max, avg float64) {
	if len(numbers) == 0 {
		return 0, 0, 0
	}
	
	min = numbers[0]
	max = numbers[0]
	sum := 0.0
	
	for _, num := range numbers {
		if num < min {
			min = num
		}
		if num > max {
			max = num
		}
		sum += num
	}
	
	avg = sum / float64(len(numbers))
	return
}

// Function demonstrating multiple returns with different scenarios
func analyzeString(s string) (length int, wordCount int, hasDigits bool, hasSpecial bool) {
	length = len(s)
	
	// Count words
	if len(s) > 0 {
		wordCount = 1
		for _, char := range s {
			if char == ' ' {
				wordCount++
			}
		}
	}
	
	// Check for digits and special characters
	for _, char := range s {
		if char >= '0' && char <= '9' {
			hasDigits = true
		}
		if !(char >= 'a' && char <= 'z' || char >= 'A' && char <= 'Z' || char >= '0' && char <= '9' || char == ' ') {
			hasSpecial = true
		}
	}
	
	return
}

// Additional demonstration
func demonstrateAdvancedReturns() {
	fmt.Println("\n--- Advanced Multiple Returns ---")
	
	// Validation example
	valid, msg, errs := validateInput("jo", "123")
	fmt.Printf("Valid: %t, Message: %s, Errors: %v\n", valid, msg, errs)
	
	// Coordinates example
	x, y, z := getCoordinates()
	fmt.Printf("Coordinates: (%.1f, %.1f, %.1f)\n", x, y, z)
	
	// Statistics example
	numbers := []float64{1.5, 2.7, 3.1, 4.9, 5.2}
	min, max, avg := stats(numbers)
	fmt.Printf("Stats - Min: %.1f, Max: %.1f, Avg: %.1f\n", min, max, avg)
	
	// String analysis example
	text := "Hello World 2023!"
	length, words, digits, special := analyzeString(text)
	fmt.Printf("Analysis - Length: %d, Words: %d, HasDigits: %t, HasSpecial: %t\n", 
		length, words, digits, special)
}
