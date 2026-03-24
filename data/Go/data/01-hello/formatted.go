package main

import "fmt"

func main() {
	fmt.Println("=== Formatted Hello Examples ===")
	
	// Example 1: Using Printf with different format verbs
	name := "Alex"
	age := 30
	score := 87.5
	
	fmt.Printf("Name: %s\n", name)
	fmt.Printf("Age: %d\n", age)
	fmt.Printf("Score: %.1f\n", score)
	fmt.Printf("Pass: %t\n", score >= 60)
	
	// Example 2: Padding and alignment
	fmt.Println("\n--- Aligned Output ---")
	fmt.Printf("%-10s | %5s | %8s\n", "Name", "Age", "Score")
	fmt.Println("-----------|-------|----------")
	fmt.Printf("%-10s | %5d | %8.1f\n", "Alice", 25, 92.3)
	fmt.Printf("%-10s | %5d | %8.1f\n", "Bob", 30, 87.5)
	fmt.Printf("%-10s | %5d | %8.1f\n", "Charlie", 28, 95.0)
	
	// Example 3: Complex formatting
	fmt.Println("\n--- Complex Formatting ---")
	student := "Maria"
	grade := 'A'
	percentage := 95.67
	
	fmt.Printf("Student: %-15s Grade: %c Percentage: %6.2f%%\n", 
		student, grade, percentage)
	
	// Example 4: Using Sprint and Sprintf
	message := fmt.Sprintf("Hello %s, you are %d years old!", name, age)
	fmt.Println("\n" + message)
}
