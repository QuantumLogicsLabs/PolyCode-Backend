package main

import (
	"fmt"
	"strings"
)

func main() {
	fmt.Println("=== Conditional Statements ===")
	
	// Basic if statement
	age := 18
	if age >= 18 {
		fmt.Println("You are an adult")
	}
	
	// If-else statement
	score := 75
	if score >= 60 {
		fmt.Println("You passed!")
	} else {
		fmt.Println("You failed!")
	}
	
	// If-else if-else chain
	grade := 'B'
	if grade == 'A' {
		fmt.Println("Excellent!")
	} else if grade == 'B' {
		fmt.Println("Good job!")
	} else if grade == 'C' {
		fmt.Println("Fair")
	} else {
		fmt.Println("Need improvement")
	}
	
	// If with initialization
	if num := 42; num > 40 {
		fmt.Printf("%d is greater than 40\n", num)
	}
	
	// Multiple conditions
	temperature := 25
	isSunny := true
	
	if temperature > 20 && isSunny {
		fmt.Println("Perfect weather for a walk!")
	}
	
	if temperature < 0 || temperature > 35 {
		fmt.Println("Extreme weather!")
	}
	
	// Complex conditions
	username := "admin"
	password := "secret123"
	isLoggedIn := true
	
	if (username == "admin" || username == "root") && 
	   len(password) >= 8 && isLoggedIn {
		fmt.Println("Access granted to admin panel")
	}
	
	// Negation
	isEmpty := false
	if !isEmpty {
		fmt.Println("Container is not empty")
	}
	
	// String conditions
	name := "Alice"
	if strings.HasPrefix(name, "A") {
		fmt.Printf("%s starts with 'A'\n", name)
	}
	
	if strings.Contains(name, "lic") {
		fmt.Printf("%s contains 'lic'\n", name)
	}
	
	// Nested conditions
	fmt.Println("\n--- Nested Conditions ---")
	score = 85
	attendance := 90
	
	if score >= 60 {
		fmt.Println("Passed the exam")
		if attendance >= 80 {
			fmt.Println("Good attendance - eligible for certificate")
		} else {
			fmt.Println("Poor attendance - certificate withheld")
		}
	} else {
		fmt.Println("Failed the exam")
		if attendance >= 80 {
			fmt.Println("Good attendance - can retake exam")
		} else {
			fmt.Println("Poor attendance - must repeat course")
		}
	}
	
	// Using functions in conditions
	fmt.Println("\n--- Function in Conditions ---")
	userInput := "hello@example.com"
	if isValidEmail(userInput) {
		fmt.Printf("'%s' is a valid email\n", userInput)
	} else {
		fmt.Printf("'%s' is not a valid email\n", userInput)
	}
}

func isValidEmail(email string) bool {
	return strings.Contains(email, "@") && strings.Contains(email, ".")
}
