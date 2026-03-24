package main

import (
	"fmt"
	"time"
)

func main() {
	fmt.Println("=== Switch Statements ===")
	
	// Basic switch
	day := 3
	switch day {
	case 1:
		fmt.Println("Monday")
	case 2:
		fmt.Println("Tuesday")
	case 3:
		fmt.Println("Wednesday")
	case 4:
		fmt.Println("Thursday")
	case 5:
		fmt.Println("Friday")
	case 6, 7:
		fmt.Println("Weekend")
	default:
		fmt.Println("Invalid day")
	}
	
	// Switch with expression
	score := 85
	grade := ""
	
	switch {
	case score >= 90:
		grade = "A"
	case score >= 80:
		grade = "B"
	case score >= 70:
		grade = "C"
	case score >= 60:
		grade = "D"
	default:
		grade = "F"
	}
	
	fmt.Printf("Score %d gets grade %s\n", score, grade)
	
	// Switch with fallthrough
	fmt.Println("\n--- Fallthrough Example ---")
	for i := 1; i <= 3; i++ {
		switch i {
		case 1:
			fmt.Print("One")
			fallthrough
		case 2:
			fmt.Print(" Two")
			fallthrough
		case 3:
			fmt.Print(" Three")
			fallthrough
		default:
			fmt.Println(" - Done!")
		}
	}
	
	// Switch with type assertion
	fmt.Println("\n--- Type Switch ---")
	var x interface{} = "hello"
	
	switch v := x.(type) {
	case string:
		fmt.Printf("String: %s (length: %d)\n", v, len(v))
	case int:
		fmt.Printf("Integer: %d\n", v)
	case float64:
		fmt.Printf("Float: %.2f\n", v)
	case bool:
		fmt.Printf("Boolean: %t\n", v)
	default:
		fmt.Printf("Unknown type: %T\n", v)
	}
	
	// Switch with time
	fmt.Println("\n--- Time-based Switch ---")
	now := time.Now()
	switch now.Weekday() {
	case time.Monday:
		fmt.Println("Start of the work week")
	case time.Tuesday, time.Wednesday, time.Thursday:
		fmt.Println("Mid week")
	case time.Friday:
		fmt.Println("TGIF!")
	case time.Saturday, time.Sunday:
		fmt.Println("Weekend!")
	}
	
	// Switch without expression (like if-else chain)
	fmt.Println("\n--- Switch Without Expression ---")
	temperature := 25
	
	switch {
	case temperature < 0:
		fmt.Println("Freezing!")
	case temperature < 10:
		fmt.Println("Cold")
	case temperature < 20:
		fmt.Println("Cool")
	case temperature < 30:
		fmt.Println("Warm")
	default:
		fmt.Println("Hot!")
	}
}
