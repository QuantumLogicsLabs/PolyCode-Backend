package main

import "fmt"

func main() {
	fmt.Println("=== Short Variable Declaration (:=) ===")
	
	// Short declaration with type inference
	name := "Alice"
	age := 30
	height := 165.5
	isActive := true
	
	fmt.Printf("Name: %s, Age: %d, Height: %.1f, Active: %t\n", 
		name, age, height, isActive)
	
	// Multiple variables in one declaration
	x, y, z := 10, 20, 30
	fmt.Printf("x=%d, y=%d, z=%d\n", x, y, z)
	
	// Mixed types
	firstName, lastName, score := "John", "Doe", 95.5
	fmt.Printf("%s %s scored %.1f\n", firstName, lastName, score)
	
	// Function returns with short declaration
	message, length := getMessage()
	fmt.Printf("Message: %s (Length: %d)\n", message, length)
	
	// Short declaration in for loops
	fmt.Println("\n--- Counting with Short Declaration ---")
	for i := 0; i < 5; i++ {
		fmt.Printf("Count: %d\n", i)
	}
	
	// Short declaration with if statements
	fmt.Println("\n--- Conditional with Short Declaration ---")
	if num := 42; num > 40 {
		fmt.Printf("%d is greater than 40\n", num)
	}
	
	// Reassignment (must use =, not :=)
	name = "Bob"
	fmt.Printf("Updated name: %s\n", name)
	
	// Error: cannot use := for existing variables
	// name := "Charlie" // This would cause an error
	
	fmt.Println("\nNote: := is for new variables, = is for existing variables")
}

func getMessage() (string, int) {
	msg := "Hello from function!"
	return msg, len(msg)
}
