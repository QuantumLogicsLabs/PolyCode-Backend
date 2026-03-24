package main

import (
	"fmt"
	"log"
)

func riskyOperation(x int) {
	defer func() {
		if r := recover(); r != nil {
			log.Printf("Recovered from panic: %v", r)
		}
	}()

	if x < 0 {
		panic("negative value not allowed")
	}

	fmt.Printf("Operation completed with value: %d\n", x)
}

func main() {
	fmt.Println("=== Panic and Recovery ===")

	fmt.Println("Testing valid operation:")
	riskyOperation(10)

	fmt.Println("\nTesting panic recovery:")
	riskyOperation(-5)

	fmt.Println("Program continues after recovery")
}
