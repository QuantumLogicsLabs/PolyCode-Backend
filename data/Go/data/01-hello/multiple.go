package main

import "fmt"

func main() {
	fmt.Println("=== Multiple Hello Examples ===")
	
	// Example 1: Different greetings
	greetings := []string{
		"Hello, World!",
		"Hi there!",
		"Welcome to Go!",
		"Greetings!",
		"Good day!",
	}
	
	for i, greeting := range greetings {
		fmt.Printf("%d: %s\n", i+1, greeting)
	}
	
	// Example 2: Hello in different languages
	langGreetings := map[string]string{
		"English": "Hello",
		"Spanish": "Hola",
		"French":  "Bonjour",
		"German":  "Hallo",
		"Japanese": "こんにちは",
	}
	
	fmt.Println("\n--- Hello in Different Languages ---")
	for lang, greeting := range langGreetings {
		fmt.Printf("%s: %s\n", lang, greeting)
	}
	
	// Example 3: Personalized messages
	names := []string{"Alice", "Bob", "Charlie", "Diana"}
	fmt.Println("\n--- Personalized Greetings ---")
	for _, name := range names {
		fmt.Printf("Hello, %s! Nice to meet you.\n", name)
	}
}
