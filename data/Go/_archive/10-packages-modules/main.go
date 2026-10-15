package main

import (
	"fmt"
	"strings"
	"time"
)

// Embedded package mocks for standalone execution
type calculatorPkg struct {
	PI      float64
	Version string
	MaxSize int
}

func (c calculatorPkg) Add(a, b int) int      { return a + b }
func (c calculatorPkg) Multiply(a, b int) int { return a * b }
func (c calculatorPkg) Subtract(a, b int) int { return a - b }

var calculator = calculatorPkg{
	PI:      3.141592,
	Version: "1.0.0",
	MaxSize: 1000,
}

type formatterPkg struct{}

func (f formatterPkg) FormatGreeting(name string) string {
	return fmt.Sprintf("Hello, %s!", name)
}
func (f formatterPkg) ToUpperCase(text string) string {
	return strings.ToUpper(text)
}

var formatter = formatterPkg{}

type validatorPkg struct{}

func (v validatorPkg) IsValidEmail(email string) bool {
	return strings.Contains(email, "@") && strings.Contains(email, ".")
}
func (v validatorPkg) IsValidAge(age int) bool {
	return age >= 18 && age <= 120
}

var validator = validatorPkg{}

func main() {
	fmt.Println("=== Packages and Modules Examples ===\n")

	// Example 1: Using standard library packages
	fmt.Println("1. Standard Library Packages:")

	// fmt package
	fmt.Println("Using fmt package:")
	name := "Go Developer"
	fmt.Printf("Hello, %s!\n", name)

	// strings package
	fmt.Println("\nUsing strings package:")
	text := "Hello, World! Go is awesome."
	words := strings.Fields(text)
	fmt.Printf("Words: %v\n", words)
	fmt.Printf("Upper case: %s\n", strings.ToUpper(text))
	fmt.Printf("Contains 'Go': %t\n", strings.Contains(text, "Go"))

	// time package
	fmt.Println("\nUsing time package:")
	now := time.Now()
	fmt.Printf("Current time: %s\n", now.Format("2006-01-02 15:04:05"))
	fmt.Printf("Unix timestamp: %d\n", now.Unix())

	// Example 2: Using custom packages
	fmt.Println("\n2. Custom Packages:")

	// Using calculator package
	fmt.Println("\nCalculator package:")
	result := calculator.Add(10, 5)
	fmt.Printf("10 + 5 = %d\n", result)

	result = calculator.Multiply(4, 7)
	fmt.Printf("4 * 7 = %d\n", result)

	result = calculator.Subtract(20, 8)
	fmt.Printf("20 - 8 = %d\n", result)

	// Using formatter package
	fmt.Println("\nFormatter package:")
	message := formatter.FormatGreeting("Alice")
	fmt.Printf("Formatted greeting: %s\n", message)

	uppercase := formatter.ToUpperCase("hello world")
	fmt.Printf("Uppercase: %s\n", uppercase)

	// Using validator package
	fmt.Println("\nValidator package:")
	email := "user@example.com"
	if validator.IsValidEmail(email) {
		fmt.Printf("%s is a valid email\n", email)
	} else {
		fmt.Printf("%s is not a valid email\n", email)
	}

	invalidEmail := "invalid-email"
	if validator.IsValidEmail(invalidEmail) {
		fmt.Printf("%s is a valid email\n", invalidEmail)
	} else {
		fmt.Printf("%s is not a valid email\n", invalidEmail)
	}

	age := 25
	if validator.IsValidAge(age) {
		fmt.Printf("%d is a valid age\n", age)
	} else {
		fmt.Printf("%d is not a valid age\n", age)
	}

	// Example 3: Package composition
	fmt.Println("\n3. Package Composition:")
	userData := map[string]interface{}{
		"name":  "Bob",
		"email": "bob@example.com",
		"age":   30,
	}

	if validator.IsValidEmail(userData["email"].(string)) &&
		validator.IsValidAge(userData["age"].(int)) {
		greeting := formatter.FormatGreeting(userData["name"].(string))
		fmt.Printf("Valid user: %s\n", greeting)

		ageInMonths := calculator.Multiply(userData["age"].(int), 12)
		fmt.Printf("Age in months: %d\n", ageInMonths)
	}

	// Example 4: Package constants and variables
	fmt.Println("\n4. Package Constants and Variables:")
	fmt.Printf("PI value: %.6f\n", calculator.PI)
	fmt.Printf("Calculator version: %s\n", calculator.Version)
	fmt.Printf("Max calculation size: %d\n", calculator.MaxSize)
}
