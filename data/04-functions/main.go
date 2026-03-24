package main

import "fmt"

func add(a, b int) int {
	return a + b
}

func divide(a, b float64) (float64, bool) {
	if b == 0 {
		return 0, false
	}

	return a / b, true
}

func main() {
	fmt.Printf("add(4, 6) = %d\n", add(4, 6))

	result, ok := divide(10, 2)
	if !ok {
		fmt.Println("division failed")
		return
	}

	fmt.Printf("divide(10, 2) = %.2f\n", result)
}
