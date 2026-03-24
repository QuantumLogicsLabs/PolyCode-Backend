package main

import (
	"go-learning-guide/calculator"
	"testing"
)

// TestBasicOperations tests basic arithmetic operations
func TestBasicOperations(t *testing.T) {
	// Test Add
	if result := calculator.Add(2, 3); result != 5 {
		t.Errorf("Add(2, 3) = %d; want 5", result)
	}
	
	// Test Subtract
	if result := calculator.Subtract(10, 4); result != 6 {
		t.Errorf("Subtract(10, 4) = %d; want 6", result)
	}
	
	// Test Multiply
	if result := calculator.Multiply(3, 4); result != 12 {
		t.Errorf("Multiply(3, 4) = %d; want 12", result)
	}
	
	// Test Divide
	if result := calculator.Divide(20, 4); result != 5 {
		t.Errorf("Divide(20, 4) = %d; want 5", result)
	}
	
	// Test Divide by zero
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
