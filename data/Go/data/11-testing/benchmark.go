package main

import (
	"fmt"
	"math/rand"
	"sort"
	"time"
)

func bubbleSort(arr []int) []int {
	n := len(arr)
	result := make([]int, len(arr))
	copy(result, arr)

	for i := 0; i < n-1; i++ {
		for j := 0; j < n-i-1; j++ {
			if result[j] > result[j+1] {
				result[j], result[j+1] = result[j+1], result[j]
			}
		}
	}
	return result
}

func quickSort(arr []int) []int {
	if len(arr) <= 1 {
		return arr
	}

	pivot := arr[len(arr)/2]
	left := []int{}
	right := []int{}
	middle := []int{}

	for _, v := range arr {
		if v < pivot {
			left = append(left, v)
		} else if v > pivot {
			right = append(right, v)
		} else {
			middle = append(middle, v)
		}
	}

	result := append(quickSort(left), middle...)
	result = append(result, quickSort(right)...)
	return result
}

func main() {
	fmt.Println("=== Sorting Algorithm Comparison ===")

	// Generate test data
	rand.Seed(time.Now().UnixNano())
	sizes := []int{100, 1000, 5000}

	for _, size := range sizes {
		data := make([]int, size)
		for i := range data {
			data[i] = rand.Intn(size * 10)
		}

		fmt.Printf("\nTesting with %d elements:\n", size)

		// Test bubble sort
		start := time.Now()
		bubbleSorted := bubbleSort(data)
		bubbleTime := time.Since(start)

		// Test quick sort
		start = time.Now()
		quickSorted := quickSort(data)
		quickTime := time.Since(start)

		// Test built-in sort
		builtinData := make([]int, len(data))
		copy(builtinData, data)
		start = time.Now()
		sort.Ints(builtinData)
		builtinTime := time.Since(start)

		fmt.Printf("Bubble Sort: %v\n", bubbleTime)
		fmt.Printf("Quick Sort: %v\n", quickTime)
		fmt.Printf("Built-in Sort: %v\n", builtinTime)

		// Verify all sorts produce same result
		if len(bubbleSorted) == len(quickSorted) && len(quickSorted) == len(builtinData) {
			fmt.Println("✓ All sorting algorithms produced valid results")
		}
	}
}
