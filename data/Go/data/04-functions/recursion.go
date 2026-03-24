package main

import "fmt"

func main() {
	fmt.Println("=== Recursion in Go ===")
	
	// Basic recursion - factorial
	fmt.Println("\n--- Factorial ---")
	for i := 0; i <= 5; i++ {
		fmt.Printf("%d! = %d\n", i, factorial(i))
	}
	
	// Fibonacci sequence
	fmt.Println("\n--- Fibonacci Sequence ---")
	for i := 0; i <= 10; i++ {
		fmt.Printf("Fib(%d) = %d\n", i, fibonacci(i))
	}
	
	// Power function
	fmt.Println("\n--- Power Function ---")
	fmt.Printf("2^3 = %d\n", power(2, 3))
	fmt.Printf("3^4 = %d\n", power(3, 4))
	fmt.Printf("5^0 = %d\n", power(5, 0))
	
	// Sum of array
	fmt.Println("\n--- Sum of Array ---")
	numbers := []int{1, 2, 3, 4, 5}
	fmt.Printf("Sum of %v = %d\n", numbers, sumArray(numbers, 0))
	
	// Reverse string
	fmt.Println("\n--- Reverse String ---")
	text := "Hello, Go!"
	fmt.Printf("Original: %s\n", text)
	fmt.Printf("Reversed: %s\n", reverseString(text))
	
	// Greatest Common Divisor
	fmt.Println("\n--- Greatest Common Divisor ---")
	fmt.Printf("GCD(48, 18) = %d\n", gcd(48, 18))
	fmt.Printf("GCD(54, 24) = %d\n", gcd(54, 24))
	
	// Binary search
	fmt.Println("\n--- Binary Search ---")
	sorted := []int{1, 3, 5, 7, 9, 11, 13, 15}
	target := 7
	index := binarySearch(sorted, target, 0, len(sorted)-1)
	fmt.Printf("Search for %d in %v: index %d\n", target, sorted, index)
	
	// Tree traversal simulation
	fmt.Println("\n--- Tree Traversal ---")
	root := &TreeNode{Value: 1}
	root.Left = &TreeNode{Value: 2}
	root.Right = &TreeNode{Value: 3}
	root.Left.Left = &TreeNode{Value: 4}
	root.Left.Right = &TreeNode{Value: 5}
	root.Right.Left = &TreeNode{Value: 6}
	
	fmt.Print("In-order traversal: ")
	inOrderTraversal(root)
	fmt.Println()
	
	// Tower of Hanoi
	fmt.Println("\n--- Tower of Hanoi ---")
	fmt.Println("Moving 3 disks from A to C:")
	towerOfHanoi(3, 'A', 'C', 'B')
	
	// Permutations
	fmt.Println("\n--- Permutations ---")
	items := []string{"A", "B", "C"}
	fmt.Printf("Permutations of %v:\n", items)
	generatePermutations(items, 0)
}

// Factorial function
func factorial(n int) int {
	if n <= 1 {
		return 1
	}
	return n * factorial(n-1)
}

// Fibonacci sequence
func fibonacci(n int) int {
	if n <= 1 {
		return n
	}
	return fibonacci(n-1) + fibonacci(n-2)
}

// Power function
func power(base, exponent int) int {
	if exponent == 0 {
		return 1
	}
	return base * power(base, exponent-1)
}

// Sum of array using recursion
func sumArray(arr []int, index int) int {
	if index >= len(arr) {
		return 0
	}
	return arr[index] + sumArray(arr, index+1)
}

// Reverse string recursively
func reverseString(s string) string {
	if len(s) <= 1 {
		return s
	}
	return reverseString(s[1:]) + string(s[0])
}

// Greatest Common Divisor (Euclidean algorithm)
func gcd(a, b int) int {
	if b == 0 {
		return a
	}
	return gcd(b, a%b)
}

// Binary search
func binarySearch(arr []int, target, low, high int) int {
	if low > high {
		return -1 // not found
	}
	
	mid := (low + high) / 2
	
	if arr[mid] == target {
		return mid
	} else if arr[mid] > target {
		return binarySearch(arr, target, low, mid-1)
	} else {
		return binarySearch(arr, target, mid+1, high)
	}
}

// Tree node structure
type TreeNode struct {
	Value int
	Left  *TreeNode
	Right *TreeNode
}

// In-order tree traversal
func inOrderTraversal(node *TreeNode) {
	if node == nil {
		return
	}
	inOrderTraversal(node.Left)
	fmt.Printf("%d ", node.Value)
	inOrderTraversal(node.Right)
}

// Tower of Hanoi
func towerOfHanoi(n int, from, to, aux rune) {
	if n == 1 {
		fmt.Printf("Move disk 1 from %c to %c\n", from, to)
		return
	}
	
	towerOfHanoi(n-1, from, aux, to)
	fmt.Printf("Move disk %d from %c to %c\n", n, from, to)
	towerOfHanoi(n-1, aux, to, from)
}

// Generate permutations
func generatePermutations(arr []string, index int) {
	if index == len(arr)-1 {
		fmt.Println(arr)
		return
	}
	
	for i := index; i < len(arr); i++ {
		// Swap
		arr[index], arr[i] = arr[i], arr[index]
		
		// Recurse
		generatePermutations(arr, index+1)
		
		// Backtrack
		arr[index], arr[i] = arr[i], arr[index]
	}
}

// Advanced recursive examples

// Check if string is palindrome
func isPalindrome(s string) bool {
	if len(s) <= 1 {
		return true
	}
	
	if s[0] != s[len(s)-1] {
		return false
	}
	
	return isPalindrome(s[1 : len(s)-1])
}

// Count digits in a number
func countDigits(n int) int {
	if n < 10 {
		return 1
	}
	return 1 + countDigits(n/10)
}

// Sum of digits
func sumOfDigits(n int) int {
	if n < 10 {
		return n
	}
	return n%10 + sumOfDigits(n/10)
}

// Find maximum in array
func findMax(arr []int, index int) int {
	if index == len(arr)-1 {
		return arr[index]
	}
	
	maxInRest := findMax(arr, index+1)
	
	if arr[index] > maxInRest {
		return arr[index]
	}
	return maxInRest
}

// Demonstrate advanced recursion
func demonstrateAdvancedRecursion() {
	fmt.Println("\n--- Advanced Recursion ---")
	
	// Palindrome check
	text := "racecar"
	fmt.Printf("Is '%s' a palindrome? %t\n", text, isPalindrome(text))
	
	// Count digits
	number := 12345
	fmt.Printf("Number of digits in %d: %d\n", number, countDigits(number))
	
	// Sum of digits
	fmt.Printf("Sum of digits in %d: %d\n", number, sumOfDigits(number))
	
	// Find maximum
	numbers := []int{3, 7, 2, 9, 1, 5}
	fmt.Printf("Maximum in %v: %d\n", numbers, findMax(numbers, 0))
}

// Tail recursion example (Go doesn't optimize tail calls, but it's good to know)
func tailFactorial(n, accumulator int) int {
	if n <= 1 {
		return accumulator
	}
	return tailFactorial(n-1, n*accumulator)
}

// Memoization for Fibonacci to avoid exponential complexity
func memoizedFibonacci(n int, memo map[int]int) int {
	if n <= 1 {
		return n
	}
	
	if result, exists := memo[n]; exists {
		return result
	}
	
	result := memoizedFibonacci(n-1, memo) + memoizedFibonacci(n-2, memo)
	memo[n] = result
	return result
}

// Demonstrate memoization
func demonstrateMemoization() {
	fmt.Println("\n--- Memoized Fibonacci ---")
	
	memo := make(map[int]int)
	for i := 0; i <= 10; i++ {
		fmt.Printf("Fib(%d) = %d\n", i, memoizedFibonacci(i, memo))
	}
}
