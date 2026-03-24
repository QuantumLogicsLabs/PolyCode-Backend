package main

import (
	"fmt"
	"os"
	"strconv"
)

var globalVar string

func init() {
	fmt.Println("Init function called")
	globalVar = "initialized"
}

func main() {
	fmt.Println("=== Module Initialization ===")
	fmt.Printf("Global variable: %s\n", globalVar)

	// Demonstrate package-level variables
	fmt.Printf("Process ID: %d\n", os.Getpid())

	// Convert string to int with error handling
	str := "42"
	if num, err := strconv.Atoi(str); err == nil {
		fmt.Printf("Converted string '%s' to number: %d\n", str, num)
	}

	// Show package constants
	fmt.Printf("Integer size: %d bits\n", strconv.IntSize)
}
