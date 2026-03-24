package main

import (
	"fmt"
	"sort"
	"strings"
)

func main() {
	fmt.Println("=== Advanced Slice Operations ===")
	
	// Slice operations
	fmt.Println("\n--- Slice Operations ---")
	numbers := []int{1, 2, 3, 4, 5}
	
	// Append
	numbers = append(numbers, 6, 7, 8)
	fmt.Printf("After append: %v\n", numbers)
	
	// Copy
	original := []int{10, 20, 30}
	copied := make([]int, len(original))
	copy(copied, original)
	fmt.Printf("Original: %v, Copied: %v\n", original, copied)
	
	// Slicing
	fmt.Printf("First 3: %v\n", numbers[:3])
	fmt.Printf("Last 3: %v\n", numbers[5:])
	fmt.Printf("Middle: %v\n", numbers[2:6])
	
	// Slice manipulation
	fmt.Println("\n--- Slice Manipulation ---")
	
	// Insert at beginning
	numbers = insertAtBeginning(numbers, 0)
	fmt.Printf("Insert 0 at beginning: %v\n", numbers)
	
	// Insert at end
	numbers = insertAtEnd(numbers, 9)
	fmt.Printf("Insert 9 at end: %v\n", numbers)
	
	// Insert in middle
	numbers = insertAtIndex(numbers, 4, 99)
	fmt.Printf("Insert 99 at index 4: %v\n", numbers)
	
	// Remove element
	numbers = removeAtIndex(numbers, 4)
	fmt.Printf("Remove element at index 4: %v\n", numbers)
	
	// Filter slice
	even := filter(numbers, func(x int) bool {
		return x%2 == 0
	})
	fmt.Printf("Even numbers: %v\n", even)
	
	// Map slice
 doubled := mapSlice(numbers, func(x int) int {
		return x * 2
	})
	fmt.Printf("Doubled: %v\n", doubled)
	
	// Reduce slice
	sum := reduce(numbers, 0, func(acc, x int) int {
		return acc + x
	})
	fmt.Printf("Sum: %d\n", sum)
	
	// String slices
	fmt.Println("\n--- String Slices ---")
	words := []string{"hello", "world", "go", "programming"}
	
	// Join
	joined := strings.Join(words, " ")
	fmt.Printf("Joined: %s\n", joined)
	
	// Split
	sentence := "go is awesome programming language"
	split := strings.Split(sentence, " ")
	fmt.Printf("Split: %v\n", split)
	
	// Filter strings
	longWords := filterStringSlice(words, func(s string) bool {
		return len(s) > 4
	})
	fmt.Printf("Long words: %v\n", longWords)
	
	// Sort slices
	fmt.Println("\n--- Sorting Slices ---")
	
	// Sort integers
	unsorted := []int{3, 1, 4, 1, 5, 9, 2, 6}
	sort.Ints(unsorted)
	fmt.Printf("Sorted integers: %v\n", unsorted)
	
	// Sort strings
	unsortedStrings := []string{"banana", "apple", "cherry", "date"}
	sort.Strings(unsortedStrings)
	fmt.Printf("Sorted strings: %v\n", unsortedStrings)
	
	// Custom sort
	people := []struct {
		name string
		age  int
	}{
		{"Alice", 30},
		{"Bob", 25},
		{"Charlie", 35},
		{"Diana", 28},
	}
	
	// Sort by age
	sort.Slice(people, func(i, j int) bool {
		return people[i].age < people[j].age
	})
	fmt.Printf("People sorted by age: %v\n", people)
	
	// Sort by name
	sort.Slice(people, func(i, j int) bool {
		return people[i].name < people[j].name
	})
	fmt.Printf("People sorted by name: %v\n", people)
	
	// Search in slices
	fmt.Println("\n--- Searching Slices ---")
	
	// Binary search (requires sorted slice)
	index := sort.SearchInts(unsorted, 5)
	fmt.Printf("Index of 5: %d\n", index)
	
	// Linear search
	found := contains(unsorted, 4)
	fmt.Printf("Contains 4: %t\n", found)
	
	foundIndex := indexOf(unsorted, 4)
	fmt.Printf("Index of 4: %d\n", foundIndex)
	
	// Advanced operations
	fmt.Println("\n--- Advanced Operations ---")
	
	// Unique elements
	withDuplicates := []int{1, 2, 2, 3, 3, 3, 4, 5, 5}
	unique := uniqueElements(withDuplicates)
	fmt.Printf("Unique elements: %v\n", unique)
	
	// Intersection
	slice1 := []int{1, 2, 3, 4, 5}
	slice2 := []int{4, 5, 6, 7, 8}
	intersection := intersect(slice1, slice2)
	fmt.Printf("Intersection: %v\n", intersection)
	
	// Union
	union := unite(slice1, slice2)
	fmt.Printf("Union: %v\n", union)
	
	// Difference
	difference := difference(slice1, slice2)
	fmt.Printf("Difference (slice1 - slice2): %v\n", difference)
	
	// Partition
	evens, odds := partition(numbers, func(x int) bool {
		return x%2 == 0
	})
	fmt.Printf("Evens: %v, Odds: %v\n", evens, odds)
	
	// Group by
	grouped := groupBy(numbers, func(x int) int {
		return x % 3
	})
	fmt.Printf("Grouped by mod 3: %v\n", grouped)
}

// Helper functions for slice operations

func insertAtBeginning(slice []int, value int) []int {
	return append([]int{value}, slice...)
}

func insertAtEnd(slice []int, value int) []int {
	return append(slice, value)
}

func insertAtIndex(slice []int, index int, value int) []int {
	if index < 0 || index > len(slice) {
		return slice
	}
	
	slice = append(slice, 0)
	copy(slice[index+1:], slice[index:])
	slice[index] = value
	return slice
}

func removeAtIndex(slice []int, index int) []int {
	if index < 0 || index >= len(slice) {
		return slice
	}
	
	return append(slice[:index], slice[index+1:]...)
}

func filter(slice []int, predicate func(int) bool) []int {
	var result []int
	for _, item := range slice {
		if predicate(item) {
			result = append(result, item)
		}
	}
	return result
}

func filterStringSlice(slice []string, predicate func(string) bool) []string {
	var result []string
	for _, item := range slice {
		if predicate(item) {
			result = append(result, item)
		}
	}
	return result
}

func mapSlice(slice []int, transform func(int) int) []int {
	result := make([]int, len(slice))
	for i, item := range slice {
		result[i] = transform(item)
	}
	return result
}

func reduce(slice []int, initial int, accumulator func(int, int) int) int {
	result := initial
	for _, item := range slice {
		result = accumulator(result, item)
	}
	return result
}

func contains(slice []int, value int) bool {
	for _, item := range slice {
		if item == value {
			return true
		}
	}
	return false
}

func indexOf(slice []int, value int) int {
	for i, item := range slice {
		if item == value {
			return i
		}
	}
	return -1
}

func uniqueElements(slice []int) []int {
	seen := make(map[int]bool)
	var result []int
	
	for _, item := range slice {
		if !seen[item] {
			seen[item] = true
			result = append(result, item)
		}
	}
	
	return result
}

func intersect(slice1, slice2 []int) []int {
	set := make(map[int]bool)
	for _, item := range slice2 {
		set[item] = true
	}
	
	var result []int
	for _, item := range slice1 {
		if set[item] {
			result = append(result, item)
		}
	}
	
	return uniqueElements(result)
}

func unite(slice1, slice2 []int) []int {
	combined := append(slice1, slice2...)
	return uniqueElements(combined)
}

func difference(slice1, slice2 []int) []int {
	set := make(map[int]bool)
	for _, item := range slice2 {
		set[item] = true
	}
	
	var result []int
	for _, item := range slice1 {
		if !set[item] {
			result = append(result, item)
		}
	}
	
	return result
}

func partition(slice []int, predicate func(int) bool) ([]int, []int) {
	var trueSlice, falseSlice []int
	for _, item := range slice {
		if predicate(item) {
			trueSlice = append(trueSlice, item)
		} else {
			falseSlice = append(falseSlice, item)
		}
	}
	return trueSlice, falseSlice
}

func groupBy(slice []int, keyFunc func(int) int) map[int][]int {
	result := make(map[int][]int)
	for _, item := range slice {
		key := keyFunc(item)
		result[key] = append(result[key], item)
	}
	return result
}

// Advanced slice algorithms

func chunk(slice []int, size int) [][]int {
	if size <= 0 {
		return nil
	}
	
	var chunks [][]int
	for i := 0; i < len(slice); i += size {
		end := i + size
		if end > len(slice) {
			end = len(slice)
		}
		chunks = append(chunks, slice[i:end])
	}
	return chunks
}

func flatten(slices [][]int) []int {
	var result []int
	for _, slice := range slices {
		result = append(result, slice...)
	}
	return result
}

func reverse(slice []int) []int {
	length := len(slice)
	reversed := make([]int, length)
	for i, item := range slice {
		reversed[length-1-i] = item
	}
	return reversed
}

func rotate(slice []int, positions int) []int {
	length := len(slice)
	if length == 0 {
		return slice
	}
	
	positions %= length
	if positions < 0 {
		positions += length
	}
	
	return append(slice[positions:], slice[:positions]...)
}

func shuffle(slice []int) []int {
	shuffled := make([]int, len(slice))
	copy(shuffled, slice)
	
	// Simple Fisher-Yates shuffle
	for i := len(shuffled) - 1; i > 0; i-- {
		j := i % (len(shuffled) / 2 + 1) // Simple pseudo-random
		shuffled[i], shuffled[j] = shuffled[j], shuffled[i]
	}
	
	return shuffled
}

func demonstrateAdvancedAlgorithms() {
	fmt.Println("\n--- Advanced Slice Algorithms ---")
	
	numbers := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
	
	// Chunk
	chunks := chunk(numbers, 3)
	fmt.Printf("Chunks of size 3: %v\n", chunks)
	
	// Flatten
	nested := [][]int{{1, 2}, {3, 4, 5}, {6}}
	flat := flatten(nested)
	fmt.Printf("Flattened: %v\n", flat)
	
	// Reverse
	reversed := reverse(numbers)
	fmt.Printf("Reversed: %v\n", reversed)
	
	// Rotate
	rotated := rotate(numbers, 3)
	fmt.Printf("Rotated by 3: %v\n", rotated)
	
	// Shuffle
	shuffled := shuffle(numbers)
	fmt.Printf("Shuffled: %v\n", shuffled)
}
