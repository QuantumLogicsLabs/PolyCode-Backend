package main

import (
	"fmt"
	"time"
)

func main() {
	fmt.Println("=== Select with Timeout ===")

	ch1 := make(chan string)
	ch2 := make(chan string)

	// Goroutine that sends to ch1 after 2 seconds
	go func() {
		time.Sleep(2 * time.Second)
		ch1 <- "from channel 1"
	}()

	// Goroutine that sends to ch2 after 3 seconds
	go func() {
		time.Sleep(3 * time.Second)
		ch2 <- "from channel 2"
	}()

	for i := 0; i < 2; i++ {
		select {
		case msg1 := <-ch1:
			fmt.Printf("Received: %s\n", msg1)
		case msg2 := <-ch2:
			fmt.Printf("Received: %s\n", msg2)
		case <-time.After(1 * time.Second):
			fmt.Println("Timeout: No message received within 1 second")
		}
	}

	fmt.Println("=== Non-blocking Select ===")

	ch3 := make(chan string)
	
	select {
	case msg := <-ch3:
		fmt.Printf("Received: %s\n", msg)
	default:
		fmt.Println("No message available, default case executed")
	}

	select {
	case ch3 <- "hello":
		fmt.Println("Message sent successfully")
	default:
		fmt.Println("Channel is full, message not sent")
	}
}
