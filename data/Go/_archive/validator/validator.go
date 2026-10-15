package main

import (
	"fmt"
	"regexp"
	"strconv"
	"strings"
	"time"
)

var (
	emailRegex   = regexp.MustCompile(`^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$`)
	urlRegex     = regexp.MustCompile(`^https?://[^\s/$.?#].[^\s]*$`)
	digitsOnly   = regexp.MustCompile(`\D`)
	lettersOnly  = regexp.MustCompile(`^[a-zA-Z]+$`)
	numbersOnly  = regexp.MustCompile(`^[0-9]+$`)
	upperRegex   = regexp.MustCompile(`[A-Z]`)
	lowerRegex   = regexp.MustCompile(`[a-z]`)
	numRegex     = regexp.MustCompile(`[0-9]`)
	specialRegex = regexp.MustCompile(`[!@#$%^&*(),.?":{}|<>]`)
)

// Validator provides basic validation utilities
type Validator struct{}

// NewValidator creates a new validator instance
func NewValidator() *Validator {
	return &Validator{}
}

// IsValidEmail checks if a string is a valid email address
func (v *Validator) IsValidEmail(email string) bool {
	if len(email) == 0 {
		return false
	}
	return emailRegex.MatchString(email)
}

func IsValidEmail(email string) bool {
	return NewValidator().IsValidEmail(email)
}

// IsValidPhone checks if a string is a valid phone number (US format)
func (v *Validator) IsValidPhone(phone string) bool {
	if len(phone) == 0 {
		return false
	}
	digits := digitsOnly.ReplaceAllString(phone, "")
	return len(digits) == 10 || len(digits) == 11
}

func IsValidPhone(phone string) bool {
	return NewValidator().IsValidPhone(phone)
}

// IsValidURL checks if a string is a valid URL
func (v *Validator) IsValidURL(url string) bool {
	if len(url) == 0 {
		return false
	}
	return urlRegex.MatchString(url)
}

func IsValidURL(url string) bool {
	return NewValidator().IsValidURL(url)
}

// IsValidCreditCard checks if a credit card number is valid using Luhn algorithm
func (v *Validator) IsValidCreditCard(cardNumber string) bool {
	if len(cardNumber) == 0 {
		return false
	}
	cleaned := digitsOnly.ReplaceAllString(cardNumber, "")
	if len(cleaned) < 13 || len(cleaned) > 19 {
		return false
	}

	sum := 0
	alternate := false

	for i := len(cleaned) - 1; i >= 0; i-- {
		digit := int(cleaned[i] - '0')
		if alternate {
			digit *= 2
			if digit > 9 {
				digit = (digit % 10) + 1
			}
		}
		sum += digit
		alternate = !alternate
	}

	return sum%10 == 0
}

func IsValidCreditCard(cardNumber string) bool {
	return NewValidator().IsValidCreditCard(cardNumber)
}

// IsValidNumber checks if a value is numeric
func (v *Validator) IsValidNumber(value interface{}) bool {
	switch val := value.(type) {
	case int, int8, int16, int32, int64, uint, uint8, uint16, uint32, uint64, float32, float64:
		return true
	case string:
		_, err := strconv.ParseFloat(val, 64)
		return err == nil
	default:
		return false
	}
}

// IsValidString checks if a value is a valid non-empty string
func (v *Validator) IsValidString(value interface{}) bool {
	str, ok := value.(string)
	if !ok {
		return false
	}
	return len(strings.TrimSpace(str)) > 0
}

// IsValidDate checks if a date string is valid (YYYY-MM-DD)
func (v *Validator) IsValidDate(date string) bool {
	_, err := time.Parse("2006-01-02", date)
	return err == nil
}

// IsValidTime checks if a time string is valid (HH:MM:SS)
func (v *Validator) IsValidTime(timeStr string) bool {
	_, err := time.Parse("15:04:05", timeStr)
	return err == nil
}

// IsValidAge checks if an age is within a reasonable range
func (v *Validator) IsValidAge(age int) bool {
	return age >= 0 && age <= 150
}

func IsValidAge(age int) bool {
	return NewValidator().IsValidAge(age)
}

// IsEmpty checks if a string is empty or contains only whitespace
func (v *Validator) IsEmpty(text string) bool {
	return len(strings.TrimSpace(text)) == 0
}

func IsEmpty(text string) bool {
	return NewValidator().IsEmpty(text)
}

// HasMinLength checks if a string has at least the minimum length
func (v *Validator) HasMinLength(text string, minLength int) bool {
	return len(text) >= minLength
}

func HasMinLength(text string, minLength int) bool {
	return NewValidator().HasMinLength(text, minLength)
}

// HasMaxLength checks if a string has at most the maximum length
func (v *Validator) HasMaxLength(text string, maxLength int) bool {
	return len(text) <= maxLength
}

func HasMaxLength(text string, maxLength int) bool {
	return NewValidator().HasMaxLength(text, maxLength)
}

// ContainsOnlyLetters checks if a string contains only letters
func (v *Validator) ContainsOnlyLetters(text string) bool {
	return lettersOnly.MatchString(text)
}

func ContainsOnlyLetters(text string) bool {
	return NewValidator().ContainsOnlyLetters(text)
}

// ContainsOnlyNumbers checks if a string contains only numbers
func (v *Validator) ContainsOnlyNumbers(text string) bool {
	return numbersOnly.MatchString(text)
}

func ContainsOnlyNumbers(text string) bool {
	return NewValidator().ContainsOnlyNumbers(text)
}

// IsStrongPassword checks if a password meets basic security requirements
func (v *Validator) IsStrongPassword(password string) bool {
	if len(password) < 8 {
		return false
	}
	return upperRegex.MatchString(password) &&
		lowerRegex.MatchString(password) &&
		numRegex.MatchString(password) &&
		specialRegex.MatchString(password)
}

func IsStrongPassword(password string) bool {
	return NewValidator().IsStrongPassword(password)
}

func main() {
	fmt.Println("=== Basic Validator Demo ===")
	v := NewValidator()

	// 1. Strings & Identity
	fmt.Printf("Valid Email (alice@example.com): %t\n", v.IsValidEmail("alice@example.com"))
	fmt.Printf("Valid Phone ((123) 456-7890): %t\n", v.IsValidPhone("(123) 456-7890"))
	fmt.Printf("Valid URL (https://github.com): %t\n", v.IsValidURL("https://github.com"))
	fmt.Printf("Valid Credit Card (4111111111111111): %t\n", v.IsValidCreditCard("4111111111111111"))

	// 2. Types & Ranges
	fmt.Printf("Valid Number (\"42.5\"): %t\n", v.IsValidNumber("42.5"))
	fmt.Printf("Valid Age (25): %t\n", v.IsValidAge(25))
	fmt.Printf("Valid Date (2026-08-29): %t\n", v.IsValidDate("2026-08-29"))
	fmt.Printf("Strong Password (Pass@1234): %t\n", v.IsStrongPassword("Pass@1234"))
}
