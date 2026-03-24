package main

import "fmt"

func main() {
	fmt.Println("=== Pointers in Go ===")
	
	// Basic variable
	x := 42
	fmt.Printf("Value of x: %d\n", x)
	
	// Get memory address of x
	fmt.Printf("Address of x: %p\n", &x)
	
	// Create a pointer to x
	var ptr *int = &x
	fmt.Printf("Pointer value (address): %p\n", ptr)
	
	// Dereference pointer to get value
	fmt.Printf("Value at pointer: %d\n", *ptr)
	
	// Modify value through pointer
	*ptr = 100
	fmt.Printf("New value of x: %d\n", x)
	
	// Pointer with different types
	name := "Go"
	namePtr := &name
	fmt.Printf("Name: %s, Address: %p, Value at address: %s\n", 
		name, namePtr, *namePtr)
	
	// Nil pointer
	var nilPtr *int
	fmt.Printf("Nil pointer: %p\n", nilPtr)
	
	// Function with pointer
	fmt.Println("\n--- Function with Pointers ---")
	num := 10
	fmt.Printf("Before function: num = %d\n", num)
	doubleValue(&num)
	fmt.Printf("After function: num = %d\n", num)
	
	// Function returning pointer
	fmt.Println("\n--- Function Returning Pointer ---")
	resultPtr := createValue(50)
	fmt.Printf("Created value: %d\n", *resultPtr)
	
	// Pointer arithmetic is not allowed in Go
	// ptr++ // This would cause an error
	
	// Multiple pointers
	fmt.Println("\n--- Multiple Pointers ---")
	a := 5
	b := 10
	ptrA := &a
	ptrB := &b
	
	fmt.Printf("a = %d, b = %d\n", a, b)
	fmt.Printf("ptrA points to value: %d\n", *ptrA)
	fmt.Printf("ptrB points to value: %d\n", *ptrB)
	
	// Swap values using pointers
	fmt.Println("\n--- Swapping with Pointers ---")
	fmt.Printf("Before swap: a = %d, b = %d\n", a, b)
	swap(&a, &b)
	fmt.Printf("After swap: a = %d, b = %d\n", a, b)
}

func doubleValue(num *int) {
	*num = *num * 2
}

func createValue(val int) *int {
	// Return a pointer to a local variable
	// Go handles this correctly (escapes to heap)
	return &val
}

func swap(a, b *int) {
	temp := *a
	*a = *b
	*b = temp
}
