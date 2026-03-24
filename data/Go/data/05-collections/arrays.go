package main

import "fmt"

func main() {
	fmt.Println("=== Arrays in Go ===")
	
	// Basic array declaration
	fmt.Println("\n--- Basic Array Declaration ---")
	
	// Array with specified size and values
	var numbers [5]int
	fmt.Printf("Zero-initialized array: %v\n", numbers)
	
	// Array with initialization
	fixedNumbers := [5]int{1, 2, 3, 4, 5}
	fmt.Printf("Initialized array: %v\n", fixedNumbers)
	
	// Partial initialization
	partialNumbers := [5]int{1, 2, 3} // Rest will be 0
	fmt.Printf("Partial initialization: %v\n", partialNumbers)
	
	// Array with ellipsis (compiler determines size)
	ellipsisArray := [...]string{"apple", "banana", "cherry"}
	fmt.Printf("Ellipsis array: %v\n", ellipsisArray)
	fmt.Printf("Length: %d\n", len(ellipsisArray))
	
	// Accessing array elements
	fmt.Println("\n--- Accessing Elements ---")
	fmt.Printf("First element: %d\n", fixedNumbers[0])
	fmt.Printf("Last element: %d\n", fixedNumbers[4])
	
	// Modifying elements
	fixedNumbers[0] = 10
	fixedNumbers[4] = 50
	fmt.Printf("After modification: %v\n", fixedNumbers)
	
	// Iterating over arrays
	fmt.Println("\n--- Iterating Over Arrays ---")
	
	// Using index
	fmt.Println("Using index:")
	for i, value := range fixedNumbers {
		fmt.Printf("Index %d: %d\n", i, value)
	}
	
	// Using only values
	fmt.Println("Using only values:")
	for _, value := range fixedNumbers {
		fmt.Printf("Value: %d\n", value)
	}
	
	// Traditional for loop
	fmt.Println("Traditional for loop:")
	for i := 0; i < len(fixedNumbers); i++ {
		fmt.Printf("Index %d: %d\n", i, fixedNumbers[i])
	}
	
	// Different array types
	fmt.Println("\n--- Different Array Types ---")
	
	// Integer array
	intArray := [4]int{10, 20, 30, 40}
	fmt.Printf("Integer array: %v\n", intArray)
	
	// Float array
	floatArray := [3]float64{1.1, 2.2, 3.3}
	fmt.Printf("Float array: %v\n", floatArray)
	
	// String array
	stringArray := [4]string{"Go", "Java", "Python", "C++"}
	fmt.Printf("String array: %v\n", stringArray)
	
	// Boolean array
	boolArray := [5]bool{true, false, true, false, true}
	fmt.Printf("Boolean array: %v\n", boolArray)
	
	// Character array (rune)
	charArray := [5]rune{'H', 'e', 'l', 'l', 'o'}
	fmt.Printf("Character array: %v\n", charArray)
	
	// Multi-dimensional arrays
	fmt.Println("\n--- Multi-dimensional Arrays ---")
	
	// 2D array
	matrix := [3][4]int{
		{1, 2, 3, 4},
		{5, 6, 7, 8},
		{9, 10, 11, 12},
	}
	fmt.Printf("2D matrix: %v\n", matrix)
	
	// Accessing 2D array elements
	fmt.Printf("Element at [1][2]: %d\n", matrix[1][2])
	
	// Iterating over 2D array
	fmt.Println("2D array iteration:")
	for i, row := range matrix {
		fmt.Printf("Row %d: %v\n", i, row)
		for j, value := range row {
			fmt.Printf("  [%d][%d] = %d\n", i, j, value)
		}
	}
	
	// 3D array
	fmt.Println("\n--- 3D Array ---")
	cube := [2][2][3]int{
		{
			{1, 2, 3},
			{4, 5, 6},
		},
		{
			{7, 8, 9},
			{10, 11, 12},
		},
	}
	
	fmt.Printf("3D cube: %v\n", cube)
	fmt.Printf("Element at [1][1][2]: %d\n", cube[1][1][2])
	
	// Array operations
	fmt.Println("\n--- Array Operations ---")
	
	// Sum of array elements
	sum := 0
	for _, value := range fixedNumbers {
		sum += value
	}
	fmt.Printf("Sum of elements: %d\n", sum)
	
	// Find maximum
	max := fixedNumbers[0]
	for _, value := range fixedNumbers {
		if value > max {
			max = value
		}
	}
	fmt.Printf("Maximum element: %d\n", max)
	
	// Find minimum
	min := fixedNumbers[0]
	for _, value := range fixedNumbers {
		if value < min {
			min = value
		}
	}
	fmt.Printf("Minimum element: %d\n", min)
	
	// Reverse array (in-place)
	fmt.Println("\n--- Reversing Array ---")
	fmt.Printf("Original: %v\n", fixedNumbers)
	
	// Reverse in-place
	for i, j := 0, len(fixedNumbers)-1; i < j; i, j = i+1, j-1 {
		fixedNumbers[i], fixedNumbers[j] = fixedNumbers[j], fixedNumbers[i]
	}
	
	fmt.Printf("Reversed: %v\n", fixedNumbers)
	
	// Array comparison
	fmt.Println("\n--- Array Comparison ---")
	array1 := [3]int{1, 2, 3}
	array2 := [3]int{1, 2, 3}
	array3 := [3]int{1, 2, 4}
	
	fmt.Printf("Array1: %v\n", array1)
	fmt.Printf("Array2: %v\n", array2)
	fmt.Printf("Array3: %v\n", array3)
	
	fmt.Printf("Array1 == Array2: %t\n", array1 == array2)
	fmt.Printf("Array1 == Array3: %t\n", array1 == array3)
	
	// Arrays as function parameters
	fmt.Println("\n--- Arrays as Function Parameters ---")
	
	result := sumArray(fixedNumbers)
	fmt.Printf("Sum using function: %d\n", result)
	
	modifiedArray := modifyArray(fixedNumbers)
	fmt.Printf("Original array after function call: %v\n", fixedNumbers)
	fmt.Printf("Returned array: %v\n", modifiedArray)
	
	// Array of structs
	fmt.Println("\n--- Array of Structs ---")
	
	type Person struct {
		name string
		age  int
	}
	
	people := [3]Person{
		{"Alice", 25},
		{"Bob", 30},
		{"Charlie", 35},
	}
	
	fmt.Printf("People array: %v\n", people)
	
	for i, person := range people {
		fmt.Printf("Person %d: %s, %d years old\n", i, person.name, person.age)
	}
	
	// Performance considerations
	fmt.Println("\n--- Performance Considerations ---")
	fmt.Println("Arrays have fixed size determined at compile time")
	fmt.Println("Arrays are value types (copied when passed to functions)")
	fmt.Println("Arrays provide better performance than slices for fixed-size data")
	fmt.Println("Use slices when you need dynamic size")
	
	// When to use arrays vs slices
	fmt.Println("\n--- Arrays vs Slices ---")
	fmt.Println("Use arrays when:")
	fmt.Println("  - Size is known and fixed")
	fmt.Println("  - Performance is critical")
	fmt.Println("  - You want value semantics")
	fmt.Println("")
	fmt.Println("Use slices when:")
	fmt.Println("  - Size may change")
	fmt.Println("  - You need dynamic behavior")
	fmt.Println("  - You want reference semantics")
}

// Functions working with arrays

func sumArray(arr [5]int) int {
	sum := 0
	for _, value := range arr {
		sum += value
	}
	return sum
}

func modifyArray(arr [5]int) [5]int {
	// This creates a copy, so original is not modified
	for i := range arr {
		arr[i] *= 2
	}
	return arr
}

func print2DArray(matrix [3][4]int) {
	for i, row := range matrix {
		fmt.Printf("Row %d: ", i)
		for _, value := range row {
			fmt.Printf("%3d ", value)
		}
		fmt.Println()
	}
}

func findInArray(arr [5]int, target int) int {
	for i, value := range arr {
		if value == target {
			return i
		}
	}
	return -1
}

func countOccurrences(arr [5]int, target int) int {
	count := 0
	for _, value := range arr {
		if value == target {
			count++
		}
	}
	return count
}

// Array algorithms

func bubbleSort(arr [5]int) [5]int {
	result := arr // Copy to avoid modifying original
	n := len(result)
	
	for i := 0; i < n-1; i++ {
		for j := 0; j < n-i-1; j++ {
			if result[j] > result[j+1] {
				result[j], result[j+1] = result[j+1], result[j]
			}
		}
	}
	
	return result
}

func binarySearchArray(arr [5]int, target int) int {
	// Note: This assumes the array is sorted
	low, high := 0, len(arr)-1
	
	for low <= high {
		mid := (low + high) / 2
		if arr[mid] == target {
			return mid
		} else if arr[mid] < target {
			low = mid + 1
		} else {
			high = mid - 1
		}
	}
	
	return -1
}

func demonstrateArrayAlgorithms() {
	fmt.Println("\n--- Array Algorithms ---")
	
	numbers := [5]int{64, 34, 25, 12, 22}
	fmt.Printf("Original array: %v\n", numbers)
	
	// Bubble sort
	sorted := bubbleSort(numbers)
	fmt.Printf("Sorted array: %v\n", sorted)
	
	// Binary search
	index := binarySearchArray(sorted, 25)
	fmt.Printf("Index of 25: %d\n", index)
	
	// Find and count
	target := 25
	foundIndex := findInArray(numbers, target)
	occurrences := countOccurrences(numbers, target)
	
	fmt.Printf("Found %d at index %d\n", target, foundIndex)
	fmt.Printf("Occurrences of %d: %d\n", target, occurrences)
}
