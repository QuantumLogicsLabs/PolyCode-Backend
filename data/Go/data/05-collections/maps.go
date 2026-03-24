package main

import (
	"fmt"
	"sort"
)

func main() {
	fmt.Println("=== Maps in Go ===")
	
	// Basic map operations
	fmt.Println("\n--- Basic Map Operations ---")
	studentAges := make(map[string]int)
	
	// Add elements
	studentAges["Alice"] = 21
	studentAges["Bob"] = 22
	studentAges["Charlie"] = 20
	
	fmt.Printf("Student ages: %v\n", studentAges)
	
	// Access elements
	aliceAge := studentAges["Alice"]
	fmt.Printf("Alice's age: %d\n", aliceAge)
	
	// Check if key exists
	davidAge, exists := studentAges["David"]
	fmt.Printf("David's age: %d, Exists: %t\n", davidAge, exists)
	
	// Delete element
	delete(studentAges, "Bob")
	fmt.Printf("After deleting Bob: %v\n", studentAges)
	
	// Map literal
	fmt.Println("\n--- Map Literal ---")
	grades := map[string]float64{
		"Math":     95.5,
		"Science":  87.3,
		"English":  92.1,
		"History":  88.9,
	}
	
	fmt.Printf("Grades: %v\n", grades)
	
	// Iterating over maps
	fmt.Println("\n--- Iterating Over Maps ---")
	fmt.Println("Student Grades:")
	for subject, grade := range grades {
		fmt.Printf("  %s: %.1f\n", subject, grade)
	}
	
	// Map with different value types
	fmt.Println("\n--- Maps with Different Value Types ---")
	
	// Map of slices
	classStudents := map[string][]string{
		"Class A": {"Alice", "Bob", "Charlie"},
		"Class B": {"David", "Eve", "Frank"},
		"Class C": {"Grace", "Henry"},
	}
	
	fmt.Printf("Class students: %v\n", classStudents)
	for className, students := range classStudents {
		fmt.Printf("%s: %v\n", className, students)
	}
	
	// Map of structs
	employeeInfo := map[string]struct {
		name     string
		age      int
		position string
		salary   float64
	}{
		"EMP001": {"Alice Johnson", 28, "Developer", 75000.0},
		"EMP002": {"Bob Smith", 32, "Designer", 65000.0},
		"EMP003": {"Charlie Brown", 35, "Manager", 85000.0},
	}
	
	fmt.Println("\n--- Employee Information ---")
	for empID, info := range employeeInfo {
		fmt.Printf("%s: %s, %d, %s, $%.2f\n", 
			empID, info.name, info.age, info.position, info.salary)
	}
	
	// Nested maps
	fmt.Println("\n--- Nested Maps ---")
	company := map[string]map[string]interface{}{
		"Engineering": {
			"budget":    1000000,
			"employees": 25,
			"manager":   "Alice Johnson",
		},
		"Marketing": {
			"budget":    500000,
			"employees": 15,
			"manager":   "Bob Smith",
		},
	}
	
	for dept, info := range company {
		fmt.Printf("%s Department:\n", dept)
		for key, value := range info {
			fmt.Printf("  %s: %v\n", key, value)
		}
	}
	
	// Map operations and utilities
	fmt.Println("\n--- Map Utilities ---")
	
	// Count occurrences
	words := []string{"apple", "banana", "apple", "orange", "banana", "apple"}
	wordCount := countWords(words)
	fmt.Printf("Word count: %v\n", wordCount)
	
	// Get keys and values
	keys := getKeys(grades)
	values := getValues(grades)
	
	fmt.Printf("Subjects: %v\n", keys)
	fmt.Printf("Grades: %v\n", values)
	
	// Sort keys
	sortedKeys := getSortedKeys(grades)
	fmt.Println("Sorted subjects with grades:")
	for _, key := range sortedKeys {
		fmt.Printf("  %s: %.1f\n", key, grades[key])
	}
	
	// Filter map
	highGrades := filterMap(grades, func(subject string, grade float64) bool {
		return grade > 90
	})
	fmt.Printf("High grades (>90): %v\n", highGrades)
	
	// Map as set
	fmt.Println("\n--- Map as Set ---")
	set := make(map[string]bool)
	items := []string{"apple", "banana", "orange", "apple", "banana"}
	
	for _, item := range items {
		set[item] = true
	}
	
	uniqueItems := getSetElements(set)
	fmt.Printf("Unique items: %v\n", uniqueItems)
	
	// Check membership
	fmt.Printf("Has 'apple': %t\n", set["apple"])
	fmt.Printf("Has 'grape': %t\n", set["grape"])
}

// Utility functions for maps

func countWords(words []string) map[string]int {
	count := make(map[string]int)
	for _, word := range words {
		count[word]++
	}
	return count
}

func getKeys(m map[string]float64) []string {
	keys := make([]string, 0, len(m))
	for k := range m {
		keys = append(keys, k)
	}
	return keys
}

func getValues(m map[string]float64) []float64 {
	values := make([]float64, 0, len(m))
	for _, v := range m {
		values = append(values, v)
	}
	return values
}

func getSortedKeys(m map[string]float64) []string {
	keys := getKeys(m)
	sort.Strings(keys)
	return keys
}

func filterMap(m map[string]float64, predicate func(string, float64) bool) map[string]float64 {
	result := make(map[string]float64)
	for k, v := range m {
		if predicate(k, v) {
			result[k] = v
		}
	}
	return result
}

func getSetElements(set map[string]bool) []string {
	elements := make([]string, 0, len(set))
	for element := range set {
		elements = append(elements, element)
	}
	return elements
}

// Advanced map operations

func mergeMaps(m1, m2 map[string]int) map[string]int {
	result := make(map[string]int)
	
	// Copy first map
	for k, v := range m1 {
		result[k] = v
	}
	
	// Add/overwrite with second map
	for k, v := range m2 {
		result[k] = v
	}
	
	return result
}

func invertMap(m map[string]string) map[string]string {
	inverted := make(map[string]string)
	for k, v := range m {
		inverted[v] = k
	}
	return inverted
}

func deepCopyMap(original map[string]interface{}) map[string]interface{} {
	copy := make(map[string]interface{})
	for key, value := range original {
		copy[key] = value
	}
	return copy
}

// Demonstrate advanced operations
func demonstrateAdvancedMaps() {
	fmt.Println("\n--- Advanced Map Operations ---")
	
	// Merge maps
	map1 := map[string]int{"a": 1, "b": 2}
	map2 := map[string]int{"b": 3, "c": 4}
	
	merged := mergeMaps(map1, map2)
	fmt.Printf("Merged maps: %v\n", merged)
	
	// Invert map
	countries := map[string]string{
		"US": "United States",
		"UK": "United Kingdom",
		"FR": "France",
	}
	
	inverted := invertMap(countries)
	fmt.Printf("Inverted map: %v\n", inverted)
	
	// Map with function values
	operations := map[string]func(int, int) int{
		"add":      func(a, b int) int { return a + b },
		"subtract": func(a, b int) int { return a - b },
		"multiply": func(a, b int) int { return a * b },
	}
	
	fmt.Println("\n--- Map with Function Values ---")
	for op, fn := range operations {
		result := fn(10, 5)
		fmt.Printf("10 %s 5 = %d\n", op, result)
	}
	
	// Map comparison
	fmt.Println("\n--- Map Comparison ---")
	mapA := map[string]int{"x": 1, "y": 2}
	mapB := map[string]int{"x": 1, "y": 2}
	mapC := map[string]int{"x": 1, "y": 3}
	
	fmt.Printf("Map A == Map B: %t\n", mapsEqual(mapA, mapB))
	fmt.Printf("Map A == Map C: %t\n", mapsEqual(mapA, mapC))
}

func mapsEqual(a, b map[string]int) bool {
	if len(a) != len(b) {
		return false
	}
	
	for k, v := range a {
		if bVal, exists := b[k]; !exists || bVal != v {
			return false
		}
	}
	
	return true
}
