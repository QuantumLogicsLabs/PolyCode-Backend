package main

import "fmt"

// Package-level variable
var globalCount int = 0

func main() {
	fmt.Println("=== Variable Scope in Go ===")
	
	// Function-level variable
	localCount := 10
	
	fmt.Printf("Global count: %d\n", globalCount)
	fmt.Printf("Local count: %d\n", localCount)
	
	// Modify global variable
	globalCount = 100
	fmt.Printf("Updated global count: %d\n", globalCount)
	
	// Demonstrate block scope
	if true {
		blockVar := "I'm in a block"
		fmt.Printf("Block variable: %s\n", blockVar)
		
		// Shadowing local variable
		localCount := 20
		fmt.Printf("Shadowed local count: %d\n", localCount)
	}
	
	// Original localCount is still 10
	fmt.Printf("Original local count after block: %d\n", localCount)
	
	// Demonstrate loop scope
	fmt.Println("\n--- Loop Scope ---")
	for i := 0; i < 3; i++ {
		loopVar := i * 10
		fmt.Printf("Iteration %d: loopVar = %d\n", i, loopVar)
	}
	
	// loopVar is not accessible here
	
	// Call functions to demonstrate scope
	outerFunction()
	
	// Show that globalCount was modified
	fmt.Printf("\nFinal global count: %d\n", globalCount)
}

func outerFunction() {
	// This function cannot access main's local variables
	// but can access global variables
	
	fmt.Printf("Global count in outerFunction: %d\n", globalCount)
	
	// Function-level variable
	funcVar := "Function scope"
	fmt.Printf("Function variable: %s\n", funcVar)
	
	innerFunction()
}

func innerFunction() {
	// This function can also access global variables
	globalCount++
	fmt.Printf("Global count incremented in innerFunction: %d\n", globalCount)
}
