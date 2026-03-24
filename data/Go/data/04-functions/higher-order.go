package main

import (
	"fmt"
	"strings"
)

func main() {
	fmt.Println("=== Higher-Order Functions ===")
	
	// Function as parameter
	fmt.Println("\n--- Function as Parameter ---")
	numbers := []int{1, 2, 3, 4, 5}
	
	doubled := mapNumbers(numbers, double)
	fmt.Printf("Doubled: %v\n", doubled)
	
	squared := mapNumbers(numbers, square)
	fmt.Printf("Squared: %v\n", squared)
	
	// Function as return value
	fmt.Println("\n--- Function as Return Value ---")
	add5 := getOperation("add", 5)
	multiply3 := getOperation("multiply", 3)
	
	fmt.Printf("Add 5 to 10: %d\n", add5(10))
	fmt.Printf("Multiply 10 by 3: %d\n", multiply3(10))
	
	// Filter function
	fmt.Println("\n--- Filter Function ---")
	even := filter(numbers, isEven)
	fmt.Printf("Even numbers: %v\n", even)
	
	odd := filter(numbers, isOdd)
	fmt.Printf("Odd numbers: %v\n", odd)
	
	// Reduce function
	fmt.Println("\n--- Reduce Function ---")
	sum := reduce(numbers, 0, add)
	fmt.Printf("Sum: %d\n", sum)
	
	product := reduce(numbers, 1, multiply)
	fmt.Printf("Product: %d\n", product)
	
	// String operations
	fmt.Println("\n--- String Operations ---")
	words := []string{"hello", "world", "go", "programming"}
	
	upper := mapStrings(words, strings.ToUpper)
	fmt.Printf("Uppercase: %v\n", upper)
	
	longWords := filterStrings(words, isLongWord)
	fmt.Printf("Long words: %v\n", longWords)
	
	// Composition
	fmt.Println("\n--- Function Composition ---")
	addThenDouble := compose(add5, double)
	fmt.Printf("Add 5 then double 10: %d\n", addThenDouble(10))
	
	// Currying
	fmt.Println("\n--- Currying ---")
	add := curryAdd(5)
	fmt.Printf("5 + 3 = %d\n", add(3))
	
	// Pipeline
	fmt.Println("\n--- Function Pipeline ---")
	result := pipeline(
		10,
		add5,
		multiply3,
		double,
	)
	fmt.Printf("Pipeline result: %d\n", result)
	
	// Predicate functions
	fmt.Println("\n--- Predicate Functions ---")
	positive := filter(numbers, isPositive)
	fmt.Printf("Positive numbers: %v\n", positive)
	
	greaterThan3 := filter(numbers, createGreaterThanPredicate(3))
	fmt.Printf("Numbers > 3: %v\n", greaterThan3)
	
	// Custom higher-order functions
	fmt.Println("\n--- Custom Higher-Order Functions ---")
	
	// Find function
	found := find(numbers, isEven)
	fmt.Printf("First even number: %d\n", found)
	
	// Every function
	allEven := every(numbers, isEven)
	fmt.Printf("All numbers are even: %t\n", allEven)
	
	// Some function
	someEven := some(numbers, isEven)
	fmt.Printf("Some numbers are even: %t\n", someEven)
}

// Basic functions to be used as parameters
func double(x int) int {
	return x * 2
}

func square(x int) int {
	return x * x
}

func add(a, b int) int {
	return a + b
}

func multiply(a, b int) int {
	return a * b
}

func isEven(x int) bool {
	return x%2 == 0
}

func isOdd(x int) bool {
	return x%2 != 0
}

func isPositive(x int) bool {
	return x > 0
}

// Higher-order function: map
func mapNumbers(nums []int, fn func(int) int) []int {
	result := make([]int, len(nums))
	for i, num := range nums {
		result[i] = fn(num)
	}
	return result
}

// Higher-order function: filter
func filter(nums []int, predicate func(int) bool) []int {
	var result []int
	for _, num := range nums {
		if predicate(num) {
			result = append(result, num)
		}
	}
	return result
}

// Higher-order function: reduce
func reduce(nums []int, initial int, fn func(int, int) int) int {
	result := initial
	for _, num := range nums {
		result = fn(result, num)
	}
	return result
}

// Higher-order function returning a function
func getOperation(op string, value int) func(int) int {
	switch op {
	case "add":
		return func(x int) int {
			return x + value
		}
	case "multiply":
		return func(x int) int {
			return x * value
		}
	case "subtract":
		return func(x int) int {
			return x - value
		}
	default:
		return func(x int) int {
			return x
		}
	}
}

// String operations
func mapStrings(strs []string, fn func(string) string) []string {
	result := make([]string, len(strs))
	for i, s := range strs {
		result[i] = fn(s)
	}
	return result
}

func filterStrings(strs []string, predicate func(string) bool) []string {
	var result []string
	for _, s := range strs {
		if predicate(s) {
			result = append(result, s)
		}
	}
	return result
}

func isLongWord(s string) bool {
	return len(s) > 4
}

// Function composition
func compose(f, g func(int) int) func(int) int {
	return func(x int) int {
		return f(g(x))
	}
}

// Currying
func curryAdd(a int) func(int) int {
	return func(b int) int {
		return a + b
	}
}

// Function pipeline
func pipeline(value int, operations ...func(int) int) int {
	result := value
	for _, op := range operations {
		result = op(result)
	}
	return result
}

// Predicate factory
func createGreaterThanPredicate(threshold int) func(int) bool {
	return func(x int) bool {
		return x > threshold
	}
}

// Find function
func find(nums []int, predicate func(int) bool) int {
	for _, num := range nums {
		if predicate(num) {
			return num
		}
	}
	return -1 // not found
}

// Every function
func every(nums []int, predicate func(int) bool) bool {
	for _, num := range nums {
		if !predicate(num) {
			return false
		}
	}
	return true
}

// Some function
func some(nums []int, predicate func(int) bool) bool {
	for _, num := range nums {
		if predicate(num) {
			return true
		}
	}
	return false
}

// Advanced higher-order functions

// Memoization factory
func memoize(fn func(int) int) func(int) int {
	cache := make(map[int]int)
	
	return func(x int) int {
		if result, exists := cache[x]; exists {
			return result
		}
		
		result := fn(x)
		cache[x] = result
		return result
	}
}

// Retry function
func retry(attempts int, fn func() error) error {
	var err error
	
	for i := 0; i < attempts; i++ {
		err = fn()
		if err == nil {
			return nil
		}
		fmt.Printf("Attempt %d failed, retrying...\n", i+1)
	}
	
	return fmt.Errorf("failed after %d attempts: %w", attempts, err)
}

// Timeout simulation
func withTimeout(fn func() string, timeout int) func() string {
	return func() string {
		// In a real scenario, you'd use goroutines and channels
		// This is a simplified simulation
		fmt.Printf("Function will timeout after %d seconds\n", timeout)
		return fn()
	}
}

// Decorator pattern
func withLogging(fn func(int) int) func(int) int {
	return func(x int) int {
		fmt.Printf("Calling function with %d\n", x)
		result := fn(x)
		fmt.Printf("Function returned %d\n", result)
		return result
	}
}

// Demonstrate advanced patterns
func demonstrateAdvancedPatterns() {
	fmt.Println("\n--- Advanced Higher-Order Patterns ---")
	
	// Memoization
	slowFunc := func(n int) int {
		fmt.Printf("Computing fibonacci(%d)...\n", n)
		if n <= 1 {
			return n
		}
		return slowFunc(n-1) + slowFunc(n-2)
	}
	
	memFunc := memoize(slowFunc)
	fmt.Printf("Memoized result: %d\n", memFunc(5))
	fmt.Printf("Memoized result (cached): %d\n", memFunc(5))
	
	// Decorator
	loggedDouble := withLogging(double)
	fmt.Printf("Logged result: %d\n", loggedDouble(10))
	
	// Retry
	attempt := 0
	failingFunc := func() error {
		attempt++
		if attempt < 3 {
			return fmt.Errorf("attempt %d failed", attempt)
		}
		return nil
	}
	
	err := retry(5, failingFunc)
	if err != nil {
		fmt.Printf("Final error: %v\n", err)
	} else {
		fmt.Println("Success after retries!")
	}
}

// Generic higher-order functions (Go 1.18+)
type Number interface {
	int | int64 | float64 | float32
}

func genericMap[T any, R any](items []T, fn func(T) R) []R {
	result := make([]R, len(items))
	for i, item := range items {
		result[i] = fn(item)
	}
	return result
}

func genericFilter[T any](items []T, predicate func(T) bool) []T {
	var result []T
	for _, item := range items {
		if predicate(item) {
			result = append(result, item)
		}
	}
	return result
}

// Demonstrate generic functions
func demonstrateGenericFunctions() {
	fmt.Println("\n--- Generic Higher-Order Functions ---")
	
	// Generic map
	ints := []int{1, 2, 3, 4, 5}
	strings := []string{"a", "bb", "ccc"}
	
	intLengths := genericMap(ints, func(x int) string {
		return fmt.Sprintf("num-%d", x)
	})
	fmt.Printf("Int to strings: %v\n", intLengths)
	
	strLengths := genericMap(strings, len)
	fmt.Printf("String lengths: %v\n", strLengths)
	
	// Generic filter
	longStrings := genericFilter(strings, func(s string) bool {
		return len(s) > 1
	})
	fmt.Printf("Long strings: %v\n", longStrings)
}
