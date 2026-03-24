package main

import "fmt"

func main() {
	score := 72

	if score >= 80 {
		fmt.Println("Great job")
	} else if score >= 50 {
		fmt.Println("You passed")
	} else {
		fmt.Println("Keep practicing")
	}

	for i := 1; i <= 5; i++ {
		fmt.Printf("Loop step: %d\n", i)
	}

	day := "Friday"

	switch day {
	case "Monday":
		fmt.Println("Start of the week")
	case "Friday":
		fmt.Println("End of the work week")
	default:
		fmt.Println("A regular day")
	}
}
