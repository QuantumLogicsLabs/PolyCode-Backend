package main

import (
	"fmt"
	"reflect"
)

func main() {
	fmt.Println("=== Go Variable Types ===")
	
	// Basic types
	var name string = "Go Programmer"
	var age int = 25
	var height float64 = 175.5
	var isStudent bool = true
	var grade rune = 'A'
	var pi float32 = 3.14
	
	fmt.Printf("Name: %s (Type: %s)\n", name, reflect.TypeOf(name))
	fmt.Printf("Age: %d (Type: %s)\n", age, reflect.TypeOf(age))
	fmt.Printf("Height: %.1f (Type: %s)\n", height, reflect.TypeOf(height))
	fmt.Printf("Is Student: %t (Type: %s)\n", isStudent, reflect.TypeOf(isStudent))
	fmt.Printf("Grade: %c (Type: %s)\n", grade, reflect.TypeOf(grade))
	fmt.Printf("Pi: %.2f (Type: %s)\n", pi, reflect.TypeOf(pi))
	
	// Zero values
	fmt.Println("\n--- Zero Values ---")
	var defaultInt int
	var defaultFloat float64
	var defaultBool bool
	var defaultString string
	
	fmt.Printf("Default int: %d\n", defaultInt)
	fmt.Printf("Default float: %.1f\n", defaultFloat)
	fmt.Printf("Default bool: %t\n", defaultBool)
	fmt.Printf("Default string: '%s' (length: %d)\n", defaultString, len(defaultString))
	
	// Type conversion
	fmt.Println("\n--- Type Conversion ---")
	var i int = 42
	var f float64 = float64(i)
	var s string = string(rune(i))
	
	fmt.Printf("int: %d -> float64: %.1f\n", i, f)
	fmt.Printf("int: %d -> string: %s\n", i, s)
}
