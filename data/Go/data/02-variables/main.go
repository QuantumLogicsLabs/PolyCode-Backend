package main

import "fmt"

func main() {
	var name string = "Saad"
	var age int = 22
	height := 5.9
	isLearning := true
	const language = "Go"

	fmt.Printf("name: %s\n", name)
	fmt.Printf("age: %d\n", age)
	fmt.Printf("height: %.1f\n", height)
	fmt.Printf("isLearning: %t\n", isLearning)
	fmt.Printf("language: %s\n", language)
}
