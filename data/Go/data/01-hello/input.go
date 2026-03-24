package main

import (
	"bufio"
	"fmt"
	"os"
)

func main() {
	fmt.Println("=== Interactive Hello Program ===")
	
	// Create a reader to get user input
	reader := bufio.NewReader(os.Stdin)
	
	// Get user's name
	fmt.Print("Enter your name: ")
	name, _ := reader.ReadString('\n')
	name = name[:len(name)-1] // Remove newline
	
	// Get user's location
	fmt.Print("Where are you from? ")
	location, _ := reader.ReadString('\n')
	location = location[:len(location)-1]
	
	// Personalized greeting
	fmt.Printf("\nHello, %s from %s!\n", name, location)
	fmt.Println("Welcome to the world of Go programming!")
}
