package main

import (
	"fmt"
)

func main() {
	fmt.Println("=== Panic and Recover ===")
	
	// Basic panic
	fmt.Println("\n--- Basic Panic ---")
	basicPanic()
	
	// Panic with recovery
	fmt.Println("\n--- Panic with Recovery ---")
	safeOperation()
	
	// Panic in functions
	fmt.Println("\n--- Panic in Functions ---")
	if err := riskyOperation(); err != nil {
		fmt.Printf("Risky operation failed: %v\n", err)
	}
	
	// Panic with different types
	fmt.Println("\n--- Panic with Different Types ---")
	panicWithTypes()
	
	// Real-world example: validation
	fmt.Println("\n--- Real-world Validation Example ---")
	if err := processData(-5); err != nil {
		fmt.Printf("Validation error: %v\n", err)
	}
	
	// Panic vs error
	fmt.Println("\n--- Panic vs Error ---")
	fmt.Println("Use errors for expected problems")
	fmt.Println("Use panic for unrecoverable errors")
}

func basicPanic() {
	fmt.Println("About to panic...")
	panic("Something went terribly wrong!")
	// This line will never execute
	fmt.Println("This will not print")
}

func safeOperation() {
	defer func() {
		if r := recover(); r != nil {
			fmt.Printf("Recovered from panic: %v\n", r)
			fmt.Println("Operation can continue safely")
		}
	}()
	
	fmt.Println("Starting safe operation")
	
	// This will panic but be recovered
	panic("Simulated error")
	
	fmt.Println("This won't execute due to panic")
}

func riskyOperation() (err error) {
	defer func() {
		if r := recover(); r != nil {
			// Convert panic to error
			switch v := r.(type) {
			case string:
				err = fmt.Errorf("panic: %s", v)
			case error:
				err = v
			default:
				err = fmt.Errorf("unknown panic: %v", v)
			}
		}
	}()
	
	fmt.Println("Performing risky operation...")
	
	// Simulate a condition that should panic
	shouldPanic := true
	if shouldPanic {
		panic("critical system failure")
	}
	
	return nil
}

func panicWithTypes() {
	// Panic with string
	func() {
		defer func() {
			if r := recover(); r != nil {
				fmt.Printf("Recovered string panic: %s (type: %T)\n", r, r)
			}
		}()
		panic("string panic")
	}()
	
	// Panic with error
	func() {
		defer func() {
			if r := recover(); r != nil {
				fmt.Printf("Recovered error panic: %v (type: %T)\n", r, r)
			}
		}()
		panic(fmt.Errorf("error panic"))
	}()
	
	// Panic with integer
	func() {
		defer func() {
			if r := recover(); r != nil {
				fmt.Printf("Recovered int panic: %d (type: %T)\n", r, r)
			}
		}()
		panic(42)
	}()
}

func processData(value int) error {
	defer func() {
		if r := recover(); r != nil {
			fmt.Printf("Panic caught in processData: %v\n", r)
		}
	}()
	
	// Validate input
	if value < 0 {
		return fmt.Errorf("value cannot be negative: %d", value)
	}
	
	if value > 100 {
		panic(fmt.Sprintf("value too large: %d", value))
	}
	
	fmt.Printf("Processing value: %d\n", value)
	return nil
}

// Best practices function
func demonstrateBestPractices() {
	fmt.Println("\n=== Best Practices ===")
	fmt.Println("1. Use errors for expected problems")
	fmt.Println("2. Use panic for unrecoverable errors")
	fmt.Println("3. Always recover if you expect panics")
	fmt.Println("4. Convert panics to errors when appropriate")
	fmt.Println("5. Don't use panic for normal control flow")
}
