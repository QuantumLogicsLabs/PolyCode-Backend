package main

import (
	"fmt"
	"os"
)

func main() {
	fmt.Println("=== Defer Statements ===")
	
	// Basic defer
	fmt.Println("\n--- Basic Defer ---")
	fmt.Println("Start")
	defer fmt.Println("End")
	fmt.Println("Middle")
	
	// Multiple defers (LIFO order)
	fmt.Println("\n--- Multiple Defers ---")
	fmt.Println("Function start")
	defer fmt.Println("Third defer")
	defer fmt.Println("Second defer")
	defer fmt.Println("First defer")
	fmt.Println("Function end")
	
	// Defer with function arguments
	fmt.Println("\n--- Defer with Arguments ---")
	for i := 1; i <= 3; i++ {
		defer fmt.Printf("Deferred call %d\n", i)
		fmt.Printf("Immediate call %d\n", i)
	}
	
	// Defer for cleanup
	fmt.Println("\n--- Defer for Cleanup ---")
	fileCleanup()
	
	// Defer for resource management
	fmt.Println("\n--- Defer for Resource Management ---")
	if err := processFile(); err != nil {
		fmt.Printf("Error: %v\n", err)
	}
	
	// Defer with panic recovery
	fmt.Println("\n--- Defer with Panic Recovery ---")
	safeFunction()
	
	// Defer in loops
	fmt.Println("\n--- Defer in Loops ---")
	deferInLoops()
	
	// Defer with return values
	fmt.Println("\n--- Defer with Return Values ---")
	result := deferredReturn()
	fmt.Printf("Result: %d\n", result)
}

func fileCleanup() {
	fmt.Println("Opening file simulation")
	defer fmt.Println("Closing file simulation")
	
	// Simulate file operations
	fmt.Println("Reading file...")
	fmt.Println("Processing file...")
	fmt.Println("File operations complete")
}

func processFile() error {
	// Simulate opening a file
	file, err := os.Open("nonexistent.txt")
	if err != nil {
		return fmt.Errorf("failed to open file: %w", err)
	}
	defer file.Close() // Always close the file
	
	fmt.Println("File opened successfully")
	fmt.Println("Processing file content...")
	
	return nil
}

func safeFunction() {
	defer func() {
		if r := recover(); r != nil {
			fmt.Printf("Recovered from panic: %v\n", r)
		}
	}()
	
	fmt.Println("About to panic")
	panic("Something went wrong!")
}

func deferInLoops() {
	fmt.Println("Processing items...")
	for i := 0; i < 3; i++ {
		// Each defer will execute when the function exits, not when loop iteration ends
		defer fmt.Printf("Cleanup for item %d\n", i)
		fmt.Printf("Processing item %d\n", i)
	}
	fmt.Println("All items processed")
}

func deferredReturn() (result int) {
	// Defer can modify named return values
	defer func() {
		result *= 2 // Double the result before returning
	}()
	
	result = 5
	fmt.Printf("Set result to %d\n", result)
	return // result will be 10 due to defer
}
