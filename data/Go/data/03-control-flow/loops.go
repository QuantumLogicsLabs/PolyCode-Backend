package main

import "fmt"

func main() {
	fmt.Println("=== Loop Examples ===")
	
	// Basic for loop
	fmt.Println("\n--- Basic For Loop ---")
	for i := 0; i < 5; i++ {
		fmt.Printf("Iteration %d\n", i)
	}
	
	// For loop as while loop
	fmt.Println("\n--- For Loop as While ---")
	count := 0
	for count < 3 {
		fmt.Printf("Count: %d\n", count)
		count++
	}
	
	// Infinite loop with break
	fmt.Println("\n--- Infinite Loop with Break ---")
	counter := 0
	for {
		if counter >= 3 {
			break
		}
		fmt.Printf("Counter: %d\n", counter)
		counter++
	}
	
	// For range with slice
	fmt.Println("\n--- For Range with Slice ---")
	fruits := []string{"Apple", "Banana", "Orange"}
	for index, fruit := range fruits {
		fmt.Printf("Index %d: %s\n", index, fruit)
	}
	
	// For range with map
	fmt.Println("\n--- For Range with Map ---")
	studentScores := map[string]int{
		"Alice":   95,
		"Bob":     87,
		"Charlie": 92,
	}
	
	for name, score := range studentScores {
		fmt.Printf("%s scored %d\n", name, score)
	}
	
	// For range with string
	fmt.Println("\n--- For Range with String ---")
	message := "Hello"
	for index, char := range message {
		fmt.Printf("Index %d: %c (Unicode: %U)\n", index, char, char)
	}
	
	// For range ignoring index
	fmt.Println("\n--- For Range Ignoring Index ---")
	for _, fruit := range fruits {
		fmt.Printf("Fruit: %s\n", fruit)
	}
	
	// Nested loops
	fmt.Println("\n--- Nested Loops ---")
	for i := 1; i <= 2; i++ {
		for j := 1; j <= 3; j++ {
			fmt.Printf("(%d,%d) ", i, j)
		}
		fmt.Println()
	}
	
	// Loop with continue
	fmt.Println("\n--- Loop with Continue ---")
	for i := 0; i < 5; i++ {
		if i == 2 {
			continue // Skip iteration 2
		}
		fmt.Printf("Iteration %d\n", i)
	}
	
	// Loop with labels
	fmt.Println("\n--- Loop with Labels ---")
outer:
	for i := 0; i < 3; i++ {
		for j := 0; j < 3; j++ {
			if i == 1 && j == 1 {
				fmt.Printf("Breaking from outer loop at (%d,%d)\n", i, j)
				break outer
			}
			fmt.Printf("(%d,%d) ", i, j)
		}
		fmt.Println()
	}
}
