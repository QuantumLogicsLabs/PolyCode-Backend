package main

import (
	"fmt"
	"go-learning-guide/formatter"
	"testing"
)

// TestFormatGreeting tests the greeting formatter
func TestFormatGreeting(t *testing.T) {
	name := "Alice"
	expected := "Hello, Alice! Welcome to Go programming."
	result := formatter.FormatGreeting(name)
	
	if result != expected {
		t.Errorf("FormatGreeting(%s) = %s; want %s", name, result, expected)
	}
}

// TestCaseConversion tests case conversion functions
func TestCaseConversion(t *testing.T) {
	text := "Hello World"
	
	// Test ToUpperCase
	upper := formatter.ToUpperCase(text)
	expectedUpper := "HELLO WORLD"
	if upper != expectedUpper {
		t.Errorf("ToUpperCase(%s) = %s; want %s", text, upper, expectedUpper)
	}
	
	// Test ToLowerCase
	lower := formatter.ToLowerCase(text)
	expectedLower := "hello world"
	if lower != expectedLower {
		t.Errorf("ToLowerCase(%s) = %s; want %s", text, lower, expectedLower)
	}
}

// TestPaddingFunctions tests padding functions
func TestPaddingFunctions(t *testing.T) {
	text := "test"
	
	// Test PadLeft
	leftPadded := formatter.PadLeft(text, 8)
	expectedLeft := "    test"
	if leftPadded != expectedLeft {
		t.Errorf("PadLeft(%s, 8) = %s; want %s", text, leftPadded, expectedLeft)
	}
	
	// Test PadRight
	rightPadded := formatter.PadRight(text, 8)
	expectedRight := "test    "
	if rightPadded != expectedRight {
		t.Errorf("PadRight(%s, 8) = %s; want %s", text, rightPadded, expectedRight)
	}
	
	// Test Center
	centered := formatter.Center(text, 10)
	expectedCenter := "   test   "
	if centered != expectedCenter {
		t.Errorf("Center(%s, 10) = %s; want %s", text, centered, expectedCenter)
	}
}

// TestTruncate tests the truncate function
func TestTruncate(t *testing.T) {
	longText := "This is a very long text that should be truncated"
	
	// Test normal truncation
	result := formatter.Truncate(longText, 20)
	expected := "This is a very lo..."
	if result != expected {
		t.Errorf("Truncate(longText, 20) = %s; want %s", result, expected)
	}
	
	// Test short text (should not be truncated)
	shortText := "short"
	result = formatter.Truncate(shortText, 10)
	expected = "short"
	if result != expected {
		t.Errorf("Truncate(shortText, 10) = %s; want %s", result, expected)
	}
	
	// Test very small length
	result = formatter.Truncate(longText, 3)
	expected = "..."
	if result != expected {
		t.Errorf("Truncate(longText, 3) = %s; want %s", result, expected)
	}
}

// TestFormatPrice tests price formatting
func TestFormatPrice(t *testing.T) {
	tests := []struct {
		input    float64
		expected string
	}{
		{19.99, "$19.99"},
		{5.0, "$5.00"},
		{0.5, "$0.50"},
		{123.456, "$123.46"}, // Should round
	}
	
	for _, tt := range tests {
		result := formatter.FormatPrice(tt.input)
		if result != tt.expected {
			t.Errorf("FormatPrice(%.3f) = %s; want %s", tt.input, result, tt.expected)
		}
	}
}

// TestPaddingEdgeCases tests edge cases for padding functions
func TestPaddingEdgeCases(t *testing.T) {
	text := "longer text"
	
	// Test padding with shorter length than text
	result := formatter.PadLeft(text, 5)
	if result != text {
		t.Errorf("PadLeft with shorter length should return original text")
	}
	
	result = formatter.PadRight(text, 5)
	if result != text {
		t.Errorf("PadRight with shorter length should return original text")
	}
	
	result = formatter.Center(text, 5)
	if result != text {
		t.Errorf("Center with shorter length should return original text")
	}
}

// BenchmarkFormatGreeting benchmarks the greeting formatter
func BenchmarkFormatGreeting(b *testing.B) {
	name := "Test User"
	for i := 0; i < b.N; i++ {
		formatter.FormatGreeting(name)
	}
}

// BenchmarkToUpperCase benchmarks case conversion
func BenchmarkToUpperCase(b *testing.B) {
	text := "This is a test string for benchmarking"
	for i := 0; i < b.N; i++ {
		formatter.ToUpperCase(text)
	}
}

// ExampleFormatGreeting demonstrates the FormatGreeting function
func ExampleFormatGreeting() {
	greeting := formatter.FormatGreeting("Bob")
	fmt.Println(greeting)
	// Output: Hello, Bob! Welcome to Go programming.
}

// ExampleFormatPrice demonstrates the FormatPrice function
func ExampleFormatPrice() {
	price := formatter.FormatPrice(29.99)
	fmt.Println(price)
	// Output: $29.99
}
