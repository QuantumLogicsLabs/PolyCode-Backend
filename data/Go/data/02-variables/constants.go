package main

import "fmt"

func main() {
	fmt.Println("=== Constants in Go ===")
	
	// Constant declarations
	const PI = 3.14159
	const GREETING = "Hello, Go!"
	const MAX_USERS = 1000
	const DEBUG_MODE = true
	
	fmt.Printf("PI: %.5f\n", PI)
	fmt.Printf("Greeting: %s\n", GREETING)
	fmt.Printf("Max Users: %d\n", MAX_USERS)
	fmt.Printf("Debug Mode: %t\n", DEBUG_MODE)
	
	// Grouped constants
	const (
		WINTER = 1
		SPRING = 2
		SUMMER = 3
		AUTUMN = 4
	)
	
	fmt.Printf("Seasons: Winter=%d, Spring=%d, Summer=%d, Autumn=%d\n", 
		WINTER, SPRING, SUMMER, AUTUMN)
	
	// Constants with increment (iota)
	const (
		JANUARY = iota + 1
		FEBRUARY
		MARCH
		APRIL
		MAY
		JUNE
	)
	
	fmt.Printf("Month numbers: Jan=%d, Feb=%d, Mar=%d, Apr=%d, May=%d, Jun=%d\n",
		JANUARY, FEBRUARY, MARCH, APRIL, MAY, JUNE)
	
	// Using iota with bit operations
	const (
		READ = 1 << iota
		WRITE
		EXECUTE
	)
	
	fmt.Printf("Permissions: READ=%d, WRITE=%d, EXECUTE=%d\n", READ, WRITE, EXECUTE)
	
	// Typed constants
	const TIMEOUT_SECONDS int = 30
	const BUFFER_SIZE int = 1024
	const VERSION string = "1.0.0"
	
	fmt.Printf("Timeout: %d seconds\n", TIMEOUT_SECONDS)
	fmt.Printf("Buffer size: %d bytes\n", BUFFER_SIZE)
	fmt.Printf("Version: %s\n", VERSION)
}
