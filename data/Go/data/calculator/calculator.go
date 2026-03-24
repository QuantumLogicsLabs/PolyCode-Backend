package calculator

import "math"

// Package constants
const (
	PI      = math.Pi
	Version = "1.0.0"
	MaxSize = 1000
)

// Add returns the sum of two integers
func Add(a, b int) int {
	return a + b
}

// Subtract returns the difference of two integers
func Subtract(a, b int) int {
	return a - b
}

// Multiply returns the product of two integers
func Multiply(a, b int) int {
	return a * b
}

// Divide returns the quotient of two integers
// Returns 0 if dividing by zero
func Divide(a, b int) int {
	if b == 0 {
		return 0
	}
	return a / b
}

// Power returns a raised to the power of b
func Power(a, b int) int {
	result := 1
	for i := 0; i < b; i++ {
		result *= a
	}
	return result
}

// IsEven checks if a number is even
func IsEven(n int) bool {
	return n%2 == 0
}

// IsOdd checks if a number is odd
func IsOdd(n int) bool {
	return n%2 != 0
}

// Max returns the larger of two numbers
func Max(a, b int) int {
	if a > b {
		return a
	}
	return b
}

// Min returns the smaller of two numbers
func Min(a, b int) int {
	if a < b {
		return a
	}
	return b
}

// Abs returns the absolute value of a number
func Abs(n int) int {
	if n < 0 {
		return -n
	}
	return n
}
