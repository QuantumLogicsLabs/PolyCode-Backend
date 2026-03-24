package main

import (
	"go-learning-guide/validator"
	"testing"
)

// TestIsValidEmail tests email validation
func TestIsValidEmail(t *testing.T) {
	validEmails := []string{
		"user@example.com",
		"test.email+tag@domain.co.uk",
		"user123@test-domain.com",
		"a@b.c",
	}
	
	invalidEmails := []string{
		"invalid-email",
		"@domain.com",
		"user@",
		"user..name@domain.com",
		"user@domain",
		"",
		"user name@domain.com",
	}
	
	for _, email := range validEmails {
		if !validator.IsValidEmail(email) {
			t.Errorf("IsValidEmail(%s) = false; want true", email)
		}
	}
	
	for _, email := range invalidEmails {
		if validator.IsValidEmail(email) {
			t.Errorf("IsValidEmail(%s) = true; want false", email)
		}
	}
}

// TestIsValidPhone tests phone validation
func TestIsValidPhone(t *testing.T) {
	validPhones := []string{
		"1234567890",
		"(123) 456-7890",
		"123-456-7890",
		"123.456.7890",
		"+1 123 456 7890",
		"11234567890", // 11 digits with country code
	}
	
	invalidPhones := []string{
		"123456789",  // too short
		"123456789012345", // too long
		"abcdefghij",
		"",
		"123-456",
	}
	
	for _, phone := range validPhones {
		if !validator.IsValidPhone(phone) {
			t.Errorf("IsValidPhone(%s) = false; want true", phone)
		}
	}
	
	for _, phone := range invalidPhones {
		if validator.IsValidPhone(phone) {
			t.Errorf("IsValidPhone(%s) = true; want false", phone)
		}
	}
}

// TestIsValidAge tests age validation
func TestIsValidAge(t *testing.T) {
	validAges := []int{0, 1, 25, 50, 100, 150}
	invalidAges := []int{-1, -10, 151, 200}
	
	for _, age := range validAges {
		if !validator.IsValidAge(age) {
			t.Errorf("IsValidAge(%d) = false; want true", age)
		}
	}
	
	for _, age := range invalidAges {
		if validator.IsValidAge(age) {
			t.Errorf("IsValidAge(%d) = true; want false", age)
		}
	}
}

// TestStringValidation tests string validation functions
func TestStringValidation(t *testing.T) {
	// Test IsEmpty
	if !validator.IsEmpty("") {
		t.Error("IsEmpty(\"\") = false; want true")
	}
	
	if !validator.IsEmpty("   ") {
		t.Error("IsEmpty(\"   \") = false; want true")
	}
	
	if validator.IsEmpty("test") {
		t.Error("IsEmpty(\"test\") = true; want false")
	}
	
	// Test HasMinLength
	if !validator.HasMinLength("hello", 3) {
		t.Error("HasMinLength(\"hello\", 3) = false; want true")
	}
	
	if validator.HasMinLength("hi", 3) {
		t.Error("HasMinLength(\"hi\", 3) = true; want false")
	}
	
	// Test HasMaxLength
	if !validator.HasMaxLength("hello", 10) {
		t.Error("HasMaxLength(\"hello\", 10) = false; want true")
	}
	
	if validator.HasMaxLength("hello world", 5) {
		t.Error("HasMaxLength(\"hello world\", 5) = true; want false")
	}
}

// TestContentValidation tests content validation functions
func TestContentValidation(t *testing.T) {
	// Test ContainsOnlyLetters
	if !validator.ContainsOnlyLetters("Hello") {
		t.Error("ContainsOnlyLetters(\"Hello\") = false; want true")
	}
	
	if validator.ContainsOnlyLetters("Hello123") {
		t.Error("ContainsOnlyLetters(\"Hello123\") = true; want false")
	}
	
	if validator.ContainsOnlyLetters("Hello World") {
		t.Error("ContainsOnlyLetters(\"Hello World\") = true; want false")
	}
	
	// Test ContainsOnlyNumbers
	if !validator.ContainsOnlyNumbers("12345") {
		t.Error("ContainsOnlyNumbers(\"12345\") = false; want true")
	}
	
	if validator.ContainsOnlyNumbers("123a5") {
		t.Error("ContainsOnlyNumbers(\"123a5\") = true; want false")
	}
	
	if validator.ContainsOnlyNumbers("12 345") {
		t.Error("ContainsOnlyNumbers(\"12 345\") = true; want false")
	}
}

// TestIsStrongPassword tests password strength validation
func TestIsStrongPassword(t *testing.T) {
	strongPasswords := []string{
		"StrongPass123!",
		"MyP@ssw0rd",
		"Complex1!Pass",
		"Abcdefgh1!",
	}
	
	weakPasswords := []string{
		"weak",           // too short
		"weakpassword",   // no numbers or special chars
		"WeakPassword123", // no special chars
		"WeakPass!",      // no numbers
		"12345678!",      // no letters
		"weak1!",         // too short
	}
	
	for _, password := range strongPasswords {
		if !validator.IsStrongPassword(password) {
			t.Errorf("IsStrongPassword(%s) = false; want true", password)
		}
	}
	
	for _, password := range weakPasswords {
		if validator.IsStrongPassword(password) {
			t.Errorf("IsStrongPassword(%s) = true; want false", password)
		}
	}
}

// TestTableDrivenEmailValidation tests email validation with table-driven approach
func TestTableDrivenEmailValidation(t *testing.T) {
	tests := []struct {
		email    string
		expected bool
	}{
		{"user@example.com", true},
		{"test.email+tag@domain.co.uk", true},
		{"invalid-email", false},
		{"@domain.com", false},
		{"user@", false},
		{"", false},
	}
	
	for _, tt := range tests {
		t.Run(tt.email, func(t *testing.T) {
			result := validator.IsValidEmail(tt.email)
			if result != tt.expected {
				t.Errorf("IsValidEmail(%s) = %t; want %t", tt.email, result, tt.expected)
			}
		})
	}
}

// BenchmarkIsValidEmail benchmarks email validation
func BenchmarkIsValidEmail(b *testing.B) {
	email := "test.user@example.com"
	for i := 0; i < b.N; i++ {
		validator.IsValidEmail(email)
	}
}

// BenchmarkIsStrongPassword benchmarks password validation
func BenchmarkIsStrongPassword(b *testing.B) {
	password := "StrongPassword123!"
	for i := 0; i < b.N; i++ {
		validator.IsStrongPassword(password)
	}
}

// ExampleIsValidEmail demonstrates email validation
func ExampleIsValidEmail() {
	fmt.Println(validator.IsValidEmail("user@example.com"))
	fmt.Println(validator.IsValidEmail("invalid-email"))
	// Output:
	// true
	// false
}

// ExampleIsStrongPassword demonstrates password validation
func ExampleIsStrongPassword() {
	fmt.Println(validator.IsStrongPassword("StrongPass123!"))
	fmt.Println(validator.IsStrongPassword("weak"))
	// Output:
	// true
	// false
}
