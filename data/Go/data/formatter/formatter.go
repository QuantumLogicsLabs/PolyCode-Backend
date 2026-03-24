package formatter

import (
	"fmt"
	"strings"
	"time"
)

// FormatGreeting creates a formatted greeting message
func FormatGreeting(name string) string {
	return fmt.Sprintf("Hello, %s! Welcome to Go programming.", name)
}

// ToUpperCase converts a string to uppercase
func ToUpperCase(text string) string {
	return strings.ToUpper(text)
}

// ToLowerCase converts a string to lowercase
func ToLowerCase(text string) string {
	return strings.ToLower(text)
}

// FormatTime formats the current time in a readable way
func FormatTime() string {
	return time.Now().Format("Monday, January 2, 2006 at 3:04:05 PM")
}

// FormatPrice formats a number as a price with 2 decimal places
func FormatPrice(price float64) string {
	return fmt.Sprintf("$%.2f", price)
}

// PadLeft pads a string on the left with spaces to reach the specified length
func PadLeft(text string, length int) string {
	if len(text) >= length {
		return text
	}
	padding := strings.Repeat(" ", length-len(text))
	return padding + text
}

// PadRight pads a string on the right with spaces to reach the specified length
func PadRight(text string, length int) string {
	if len(text) >= length {
		return text
	}
	padding := strings.Repeat(" ", length-len(text))
	return text + padding
}

// Truncate truncates a string to the specified length, adding "..." if truncated
func Truncate(text string, length int) string {
	if len(text) <= length {
		return text
	}
	if length <= 3 {
		return strings.Repeat(".", length)
	}
	return text[:length-3] + "..."
}

// Center centers a string within a field of the specified length
func Center(text string, length int) string {
	if len(text) >= length {
		return text
	}
	totalPadding := length - len(text)
	leftPadding := totalPadding / 2
	rightPadding := totalPadding - leftPadding
	return strings.Repeat(" ", leftPadding) + text + strings.Repeat(" ", rightPadding)
}
