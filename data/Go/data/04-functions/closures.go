package main

import "fmt"

func main() {
	fmt.Println("=== Closures in Go ===")
	
	// Basic closure
	fmt.Println("\n--- Basic Closure ---")
	adder := createAdder(10)
	fmt.Printf("10 + 5 = %d\n", adder(5))
	fmt.Printf("10 + 3 = %d\n", adder(3))
	
	adder2 := createAdder(100)
	fmt.Printf("100 + 5 = %d\n", adder2(5))
	
	// Closure with multiple variables
	fmt.Println("\n--- Closure with Multiple Variables ---")
	multiplier := createMultiplier(2, 3)
	fmt.Printf("2 * 3 * 4 = %d\n", multiplier(4))
	fmt.Printf("2 * 3 * 5 = %d\n", multiplier(5))
	
	// Closure capturing loop variables
	fmt.Println("\n--- Closure with Loop Variables ---")
	functions := createFunctions()
	for i, fn := range functions {
		fmt.Printf("Function %d: %d\n", i, fn())
	}
	
	// Closure for incrementer
	fmt.Println("\n--- Incrementer Closure ---")
	counter := newCounter()
	fmt.Printf("Count: %d\n", counter())
	fmt.Printf("Count: %d\n", counter())
	fmt.Printf("Count: %d\n", counter())
	
	// Closure for accumulator
	fmt.Println("\n--- Accumulator Closure ---")
	acc := accumulator()
	fmt.Printf("Accumulate 5: %d\n", acc(5))
	fmt.Printf("Accumulate 3: %d\n", acc(3))
	fmt.Printf("Accumulate 2: %d\n", acc(2))
	
	// Closure with map
	fmt.Println("\n--- Closure with Map ---")
	calculator := makeCalculator()
	fmt.Printf("Add: %d\n", calculator("add", 10, 5))
	fmt.Printf("Subtract: %d\n", calculator("subtract", 10, 5))
	fmt.Printf("Multiply: %d\n", calculator("multiply", 10, 5))
	
	// Closure for filtering
	fmt.Println("\n--- Filter Closure ---")
	numbers := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
	
	evenFilter := createEvenFilter()
	even := filter(numbers, evenFilter)
	fmt.Printf("Even numbers: %v\n", even)
	
	greaterThan5Filter := createGreaterThanFilter(5)
	greaterThan5 := filter(numbers, greaterThan5Filter)
	fmt.Printf("Numbers > 5: %v\n", greaterThan5)
	
	// Closure for string processing
	fmt.Println("\n--- String Processing Closure ---")
	prefixer := createPrefixer("Hello, ")
	fmt.Printf("%s\n", prefixer("World"))
	fmt.Printf("%s\n", prefixer("Go"))
	
	// Closure maintaining state
	fmt.Println("\n--- Stateful Closure ---")
	bankAccount := createBankAccount(100)
	fmt.Printf("Balance: %d\n", bankAccount("balance"))
	fmt.Printf("Deposit 50: %d\n", bankAccount("deposit", 50))
	fmt.Printf("Withdraw 30: %d\n", bankAccount("withdraw", 30))
	fmt.Printf("Balance: %d\n", bankAccount("balance"))
}

// Function that returns a closure
func createAdder(base int) func(int) int {
	return func(x int) int {
		return base + x
	}
}

// Closure with multiple captured variables
func createMultiplier(a, b int) func(int) int {
	return func(c int) int {
		return a * b * c
	}
}

// Function returning multiple closures
func createFunctions() []func() int {
	var functions []func() int
	
	for i := 0; i < 3; i++ {
		// Important: create a new variable to capture the current value
		num := i
		functions = append(functions, func() int {
			return num
		})
	}
	
	return functions
}

// Closure that maintains state
func newCounter() func() int {
	count := 0
	return func() int {
		count++
		return count
	}
}

// Closure accumulator
func accumulator() func(int) int {
	sum := 0
	return func(x int) int {
		sum += x
		return sum
	}
}

// Closure with map lookup
func makeCalculator() func(string, int, int) int {
	operations := map[string]func(int, int) int{
		"add":      func(a, b int) int { return a + b },
		"subtract": func(a, b int) int { return a - b },
		"multiply": func(a, b int) int { return a * b },
		"divide":   func(a, b int) int { return a / b },
	}
	
	return func(op string, a, b int) int {
		if fn, exists := operations[op]; exists {
			return fn(a, b)
		}
		return 0
	}
}

// Filter function using closure
func filter(numbers []int, predicate func(int) bool) []int {
	var result []int
	for _, num := range numbers {
		if predicate(num) {
			result = append(result, num)
		}
	}
	return result
}

// Create filter closures
func createEvenFilter() func(int) bool {
	return func(x int) bool {
		return x%2 == 0
	}
}

func createGreaterThanFilter(threshold int) func(int) bool {
	return func(x int) bool {
		return x > threshold
	}
}

// String processing closure
func createPrefixer(prefix string) func(string) string {
	return func(s string) string {
		return prefix + s
	}
}

// Bank account closure with state
func createBankAccount(initialBalance int) func(...int) int {
	balance := initialBalance
	
	return func(operations ...int) int {
		if len(operations) == 0 {
			return balance
		}
		
		if len(operations) == 2 {
			operation := operations[0]
			amount := operations[1]
			
			switch operation {
			case 1: // deposit
				balance += amount
			case 2: // withdraw
				balance -= amount
			}
		}
		
		return balance
	}
}

// Advanced closure examples
func advancedClosures() {
	fmt.Println("\n--- Advanced Closures ---")
	
	// Closure factory
	addFactory := func(step int) func(int) int {
		return func(x int) int {
			return x + step
		}
	}
	
	add5 := addFactory(5)
	add10 := addFactory(10)
	
	fmt.Printf("Add 5 to 3: %d\n", add5(3))
	fmt.Printf("Add 10 to 3: %d\n", add10(3))
	
	// Closure with interface
	type Validator func(string) bool
	
	nameValidator := func(minLength int) Validator {
		return func(name string) bool {
			return len(name) >= minLength
		}
	}
	
	validateName := nameValidator(3)
	fmt.Printf("Name 'Bob' valid: %t\n", validateName("Bob"))
	fmt.Printf("Name 'Al' valid: %t\n", validateName("Al"))
}

// Memoization using closure
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

func fibonacci(n int) int {
	if n <= 1 {
		return n
	}
	return fibonacci(n-1) + fibonacci(n-2)
}

func demonstrateMemoization() {
	fmt.Println("\n--- Memoization Example ---")
	
	// Regular fibonacci
	fmt.Printf("Fibonacci(10): %d\n", fibonacci(10))
	
	// Memoized fibonacci
	memFib := memoize(fibonacci)
	fmt.Printf("Memoized Fibonacci(10): %d\n", memFib(10))
	fmt.Printf("Memoized Fibonacci(10): %d (cached)\n", memFib(10))
}
