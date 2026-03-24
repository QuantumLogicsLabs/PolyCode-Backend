package main

import "fmt"

func main() {
	numbers := [3]int{10, 20, 30}
	fmt.Println("array:", numbers)

	scores := []int{90, 85, 100}
	scores = append(scores, 95)
	fmt.Println("slice:", scores)

	subjectMarks := map[string]int{
		"math":    95,
		"science": 88,
		"english": 91,
	}

	fmt.Println("map:", subjectMarks)
	fmt.Println("math marks:", subjectMarks["math"])
}
