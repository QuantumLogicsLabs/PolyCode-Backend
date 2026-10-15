package main

import (
	"fmt"
	"testing"
)

// Top-level functions to satisfy Go Example identifiers
func Add(a, b int) int      { return a + b }
func Subtract(a, b int) int { return a - b }
func Multiply(a, b int) int { return a * b }
func Divide(a, b int) int {
	if b == 0 {
		return 0
	}
	return a / b
}
func IsEven(n int) bool { return n%2 == 0 }
func IsOdd(n int) bool  { return n%2 != 0 }
func Max(a, b int) int {
	if a > b {
		return a
	}
	return b
}
func Min(a, b int) int {
	if a < b {
		return a
	}
	return b
}
func Abs(n int) int {
	if n < 0 {
		return -n
	}
	return n
}

// Struct mock for calculator namespace calls
type calculatorPkg struct{}

func (c calculatorPkg) Add(a, b int) int      { return Add(a, b) }
func (c calculatorPkg) Subtract(a, b int) int { return Subtract(a, b) }
func (c calculatorPkg) Multiply(a, b int) int { return Multiply(a, b) }
func (c calculatorPkg) Divide(a, b int) int   { return Divide(a, b) }
func (c calculatorPkg) IsEven(n int) bool     { return IsEven(n) }
func (c calculatorPkg) IsOdd(n int) bool      { return IsOdd(n) }
func (c calculatorPkg) Max(a, b int) int      { return Max(a, b) }
func (c calculatorPkg) Min(a, b int) int      { return Min(a, b) }
func (c calculatorPkg) Abs(n int) int         { return Abs(n) }

var calculator = calculatorPkg{}

// TestBasicOperations tests basic arithmetic operations
func TestBasicOperations(t *testing.T) {
	if result := calculator.Add(2, 3); result != 5 {
		t.Errorf("Add(2, 3) = %d; want 5", result)
	}

	if result := calculator.Subtract(10, 4); result != 6 {
		t.Errorf("Subtract(10, 4) = %d; want 6", result)
	}

	if result := calculator.Multiply(3, 4); result != 12 {
		t.Errorf("Multiply(3, 4) = %d; want 12", result)
	}

	if result := calculator.Divide(20, 4); result != 5 {
		t.Errorf("Divide(20, 4) = %d; want 5", result)
	}

	if result := calculator.Divide(10, 0); result != 0 {
		t.Errorf("Divide(10, 0) = %d; want 0", result)
	}
}

// TestTableDriven tests multiple scenarios using table-driven approach
func TestTableDriven(t *testing.T) {
	tests := []struct {
		name     string
		a, b     int
		expected int
	}{
		{"AddPositive", 2, 3, 5},
		{"AddNegative", -2, -3, -5},
		{"AddMixed", -2, 3, 1},
		{"AddZero", 0, 5, 5},
	}

	for _, tt := range tests {
		t.Run(tt.name, func(t *testing.T) {
			result := calculator.Add(tt.a, tt.b)
			if result != tt.expected {
				t.Errorf("Add(%d, %d) = %d; want %d", tt.a, tt.b, result, tt.expected)
			}
		})
	}
}

// TestEvenOdd tests even and odd number detection
func TestEvenOdd(t *testing.T) {
	evenNumbers := []int{0, 2, 4, 6, 8, 10}
	for _, num := range evenNumbers {
		if !calculator.IsEven(num) {
			t.Errorf("IsEven(%d) = false; want true", num)
		}
		if calculator.IsOdd(num) {
			t.Errorf("IsOdd(%d) = true; want false", num)
		}
	}

	oddNumbers := []int{1, 3, 5, 7, 9}
	for _, num := range oddNumbers {
		if calculator.IsEven(num) {
			t.Errorf("IsEven(%d) = true; want false", num)
		}
		if !calculator.IsOdd(num) {
			t.Errorf("IsOdd(%d) = false; want true", num)
		}
	}
}

// TestMinMax tests min and max functions
func TestMinMax(t *testing.T) {
	if calculator.Max(5, 10) != 10 {
		t.Errorf("Max(5, 10) = %d; want 10", calculator.Max(5, 10))
	}

	if calculator.Min(5, 10) != 5 {
		t.Errorf("Min(5, 10) = %d; want 5", calculator.Min(5, 10))
	}

	if calculator.Max(10, 5) != 10 {
		t.Errorf("Max(10, 5) = %d; want 10", calculator.Max(10, 5))
	}

	if calculator.Min(10, 5) != 5 {
		t.Errorf("Min(10, 5) = %d; want 5", calculator.Min(10, 5))
	}
}

// TestAbs tests absolute value function
func TestAbs(t *testing.T) {
	tests := []struct {
		input    int
		expected int
	}{
		{5, 5},
		{-5, 5},
		{0, 0},
		{100, 100},
		{-100, 100},
	}

	for _, tt := range tests {
		if result := calculator.Abs(tt.input); result != tt.expected {
			t.Errorf("Abs(%d) = %d; want %d", tt.input, result, tt.expected)
		}
	}
}

// BenchmarkAdd benchmarks the Add function
func BenchmarkAdd(b *testing.B) {
	for i := 0; i < b.N; i++ {
		calculator.Add(100, 200)
	}
}

// BenchmarkMultiply benchmarks the Multiply function
func BenchmarkMultiply(b *testing.B) {
	for i := 0; i < b.N; i++ {
		calculator.Multiply(15, 20)
	}
}

// ExampleAdd demonstrates the Add function
func ExampleAdd() {
	result := calculator.Add(2, 3)
	fmt.Println(result)
	// Output: 5
}

// ExampleIsEven demonstrates the IsEven function
func ExampleIsEven() {
	fmt.Println(calculator.IsEven(4))
	fmt.Println(calculator.IsEven(3))
	// Output:
	// true
	// false
}
