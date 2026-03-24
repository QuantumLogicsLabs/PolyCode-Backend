package validator

import (
	"regexp"
	"strings"
)

// IsValidEmail checks if a string is a valid email address
func IsValidEmail(email string) bool {
	if len(email) == 0 {
		return false
	}
	
	// Basic email regex pattern
	pattern := `^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$`
	matched, err := regexp.MatchString(pattern, email)
	if err != nil {
		return false
	}
	
	return matched
}

// IsValidPhone checks if a string is a valid phone number (US format)
func IsValidPhone(phone string) bool {
	if len(phone) == 0 {
		return false
	}
	
	// Remove all non-digit characters
	digits := regexp.MustCompile(`\D`).ReplaceAllString(phone, "")
	
	// Check if it's 10 or 11 digits (with optional country code)
	return len(digits) == 10 || len(digits) == 11
}

// IsValidURL checks if a string is a valid URL
func IsValidURL(url string) bool {
	if len(url) == 0 {
		return false
	}
	
	pattern := `^https?://[^\s/$.?#].[^\s]*$`
	matched, err := regexp.MatchString(pattern, url)
	if err != nil {
		return false
	}
	
	return matched
}

// IsValidAge checks if an age is within a reasonable range
func IsValidAge(age int) bool {
	return age >= 0 && age <= 150
}

// IsEmpty checks if a string is empty or contains only whitespace
func IsEmpty(text string) bool {
	return len(strings.TrimSpace(text)) == 0
}

// HasMinLength checks if a string has at least the minimum length
func HasMinLength(text string, minLength int) bool {
	return len(text) >= minLength
}

// HasMaxLength checks if a string has at most the maximum length
func HasMaxLength(text string, maxLength int) bool {
	return len(text) <= maxLength
}

// ContainsOnlyLetters checks if a string contains only letters
func ContainsOnlyLetters(text string) bool {
	matched, err := regexp.MatchString(`^[a-zA-Z]+$`, text)
	if err != nil {
		return false
	}
	return matched
}

// ContainsOnlyNumbers checks if a string contains only numbers
func ContainsOnlyNumbers(text string) bool {
	matched, err := regexp.MatchString(`^[0-9]+$`, text)
	if err != nil {
		return false
	}
	return matched
}

// IsStrongPassword checks if a password meets basic security requirements
func IsStrongPassword(password string) bool {
	if len(password) < 8 {
		return false
	}
	
	hasUpper := regexp.MustCompile(`[A-Z]`).MatchString(password)
	hasLower := regexp.MustCompile(`[a-z]`).MatchString(password)
	hasNumber := regexp.MustCompile(`[0-9]`).MatchString(password)
	hasSpecial := regexp.MustCompile(`[!@#$%^&*(),.?":{}|<>]`).MatchString(password)
	
	return hasUpper && hasLower && hasNumber && hasSpecial
}
