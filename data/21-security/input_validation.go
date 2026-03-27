package main

import (
	"fmt"
	"regexp"
	"strconv"
	"strings"
	"unicode"
)

// Validator interface
type Validator interface {
	Validate(input string) (bool, []string)
}

// InputValidator provides comprehensive input validation
type InputValidator struct {
	rules map[string]ValidationRule
}

type ValidationRule struct {
	Pattern   *regexp.Regexp
	Required  bool
	MinLength int
	MaxLength int
	Custom    func(string) (bool, string)
}

func NewInputValidator() *InputValidator {
	return &InputValidator{
		rules: make(map[string]ValidationRule),
	}
}

func (v *InputValidator) AddRule(field string, rule ValidationRule) {
	v.rules[field] = rule
}

func (v *InputValidator) ValidateField(field, value string) (bool, []string) {
	rule, exists := v.rules[field]
	if !exists {
		return true, []string{} // No rules means valid
	}
	
	var errors []string
	
	// Check required
	if rule.Required && (value == "" || strings.TrimSpace(value) == "") {
		errors = append(errors, fmt.Sprintf("%s is required", field))
		return false, errors
	}
	
	if value == "" {
		return true, errors // Empty but not required
	}
	
	// Check length
	if rule.MinLength > 0 && len(value) < rule.MinLength {
		errors = append(errors, fmt.Sprintf("%s must be at least %d characters", field, rule.MinLength))
	}
	
	if rule.MaxLength > 0 && len(value) > rule.MaxLength {
		errors = append(errors, fmt.Sprintf("%s must be at most %d characters", field, rule.MaxLength))
	}
	
	// Check pattern
	if rule.Pattern != nil && !rule.Pattern.MatchString(value) {
		errors = append(errors, fmt.Sprintf("%s format is invalid", field))
	}
	
	// Custom validation
	if rule.Custom != nil {
		if valid, message := rule.Custom(value); !valid {
			errors = append(errors, message)
		}
	}
	
	return len(errors) == 0, errors
}

// Email validation
func (v *InputValidator) IsValidEmail(email string) bool {
	emailRegex := regexp.MustCompile(`^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$`)
	return emailRegex.MatchString(email)
}

// Phone validation
func (v *InputValidator) IsValidPhone(phone string) bool {
	// Remove common phone number formatting
	phone = regexp.MustCompile(`[\s\-\(\)]`).ReplaceAllString(phone, "")
	
	// Check if it's all digits and reasonable length
	phoneRegex := regexp.MustCompile(`^\+?[1-9]\d{6,14}$`)
	return phoneRegex.MatchString(phone)
}

// URL validation
func (v *InputValidator) IsValidURL(url string) bool {
	urlRegex := regexp.MustCompile(`^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$`)
	return urlRegex.MatchString(url)
}

// Credit card validation
func (v *InputValidator) IsValidCreditCard(card string) bool {
	// Remove spaces and dashes
	card = regexp.MustCompile(`[\s\-]`).ReplaceAllString(card, "")
	
	// Check if it's all digits and valid length
	if !regexp.MustCompile(`^\d{13,19}$`).MatchString(card) {
		return false
	}
	
	// Luhn algorithm
	return v.luhnCheck(card)
}

func (v *InputValidator) luhnCheck(card string) bool {
	sum := 0
	doubleDigit := false
	
	for i := len(card) - 1; i >= 0; i-- {
		digit := int(card[i] - '0')
		
		if doubleDigit {
			digit *= 2
			if digit > 9 {
				digit = digit/10 + digit%10
			}
		}
		
		sum += digit
		doubleDigit = !doubleDigit
	}
	
	return sum%10 == 0
}

// SQL injection prevention
func (v *InputValidator) SanitizeSQL(input string) string {
	// Remove common SQL injection patterns
	dangerousPatterns := []string{
		`'`,
		`"`,
		`;`,
		`--`,
		`/*`,
		`*/`,
		`xp_`,
		`sp_`,
		`DROP`,
		`DELETE`,
		`INSERT`,
		`UPDATE`,
		`SELECT`,
		`UNION`,
		`EXEC`,
		`SCRIPT`,
	}
	
	sanitized := input
	for _, pattern := range dangerousPatterns {
		sanitized = strings.ReplaceAll(sanitized, pattern, "")
	}
	
	return sanitized
}

// XSS prevention
func (v *InputValidator) SanitizeHTML(input string) string {
	// Remove HTML tags
	htmlRegex := regexp.MustCompile(`<[^>]*>`)
	sanitized := htmlRegex.ReplaceAllString(input, "")
	
	// Remove JavaScript event handlers
	jsEventRegex := regexp.MustCompile(`on\w+\s*=`)
	sanitized = jsEventRegex.ReplaceAllString(sanitized, "")
	
	// Remove javascript: protocol
	jsProtocolRegex := regexp.MustCompile(`javascript:`, regexp.IgnoreCase)
	sanitized = jsProtocolRegex.ReplaceAllString(sanitized, "")
	
	return sanitized
}

// Password validation
func (v *InputValidator) ValidatePassword(password string) (bool, []string) {
	var errors []string
	
	if len(password) < 8 {
		errors = append(errors, "Password must be at least 8 characters long")
	}
	
	if len(password) > 128 {
		errors = append(errors, "Password must be less than 128 characters long")
	}
	
	if !v.hasUpperCase(password) {
		errors = append(errors, "Password must contain at least one uppercase letter")
	}
	
	if !v.hasLowerCase(password) {
		errors = append(errors, "Password must contain at least one lowercase letter")
	}
	
	if !v.hasDigit(password) {
		errors = append(errors, "Password must contain at least one digit")
	}
	
	if !v.hasSpecialChar(password) {
		errors = append(errors, "Password must contain at least one special character")
	}
	
	// Check for common passwords
	if v.isCommonPassword(password) {
		errors = append(errors, "Password is too common, please choose a stronger one")
	}
	
	return len(errors) == 0, errors
}

func (v *InputValidator) hasUpperCase(s string) bool {
	for _, r := range s {
		if unicode.IsUpper(r) {
			return true
		}
	}
	return false
}

func (v *InputValidator) hasLowerCase(s string) bool {
	for _, r := range s {
		if unicode.IsLower(r) {
			return true
		}
	}
	return false
}

func (v *InputValidator) hasDigit(s string) bool {
	for _, r := range s {
		if unicode.IsDigit(r) {
			return true
		}
	}
	return false
}

func (v *InputValidator) hasSpecialChar(s string) bool {
	specialChars := "!@#$%^&*()_+-=[]{}|;:,.<>?"
	for _, r := range s {
		if strings.ContainsRune(specialChars, r) {
			return true
		}
	}
	return false
}

func (v *InputValidator) isCommonPassword(password string) bool {
	commonPasswords := []string{
		"password", "123456", "123456789", "qwerty", "abc123",
		"password123", "admin", "letmein", "welcome", "monkey",
		"password1", "qwerty123", "starwars", "football", "whatever",
	}
	
	lowerPassword := strings.ToLower(password)
	for _, common := range commonPasswords {
		if lowerPassword == common {
			return true
		}
	}
	return false
}

// File validation
type FileValidator struct {
	allowedExtensions map[string]bool
	maxFileSize        int64
}

func NewFileValidator() *FileValidator {
	return &FileValidator{
		allowedExtensions: map[string]bool{
			".jpg":  true,
			".jpeg": true,
			".png":  true,
			".gif":  true,
			".pdf":  true,
			".txt":  true,
			".doc":  true,
			".docx": true,
			".xls":  true,
			".xlsx": true,
		},
		maxFileSize: 10 * 1024 * 1024, // 10MB
	}
}

func (f *FileValidator) ValidateFile(filename string, size int64) (bool, []string) {
	var errors []string
	
	// Check file extension
	extension := strings.ToLower(filename[strings.LastIndex(filename, "."):])
	if !f.allowedExtensions[extension] {
		errors = append(errors, fmt.Sprintf("File type %s is not allowed", extension))
	}
	
	// Check file size
	if size > f.maxFileSize {
		errors = append(errors, fmt.Sprintf("File size %d exceeds maximum allowed size %d", size, f.maxFileSize))
	}
	
	return len(errors) == 0, errors
}

// Numeric validation
func (v *InputValidator) ValidateNumeric(input string, min, max float64) (bool, []string) {
	var errors []string
	
	value, err := strconv.ParseFloat(input, 64)
	if err != nil {
		errors = append(errors, "Input must be a valid number")
		return false, errors
	}
	
	if value < min {
		errors = append(errors, fmt.Sprintf("Value must be at least %f", min))
	}
	
	if value > max {
		errors = append(errors, fmt.Sprintf("Value must be at most %f", max))
	}
	
	return len(errors) == 0, errors
}

// Date validation
func (v *InputValidator) ValidateDate(input string, format string) (bool, []string) {
	var errors []string
	
	// Simple date format validation (YYYY-MM-DD)
	dateRegex := regexp.MustCompile(`^\d{4}-\d{2}-\d{2}$`)
	if format == "YYYY-MM-DD" && !dateRegex.MatchString(input) {
		errors = append(errors, "Date must be in YYYY-MM-DD format")
		return false, errors
	}
	
	// Additional validation could be added here
	// For now, just check format
	
	return len(errors) == 0, errors
}

// String validation utilities
func (v *InputValidator) ValidateAlpha(input string) bool {
	return regexp.MustCompile(`^[a-zA-Z]+$`).MatchString(input)
}

func (v *InputValidator) ValidateAlphaNumeric(input string) bool {
	return regexp.MustCompile(`^[a-zA-Z0-9]+$`).MatchString(input)
}

func (v *InputValidator) ValidateNoSpecialChars(input string) bool {
	return regexp.MustCompile(`^[a-zA-Z0-9\s]+$`).MatchString(input)
}

// Address validation
func (v *InputValidator) ValidateAddress(address string) (bool, []string) {
	var errors []string
	
	if len(strings.TrimSpace(address)) < 5 {
		errors = append(errors, "Address is too short")
	}
	
	if len(address) > 200 {
		errors = append(errors, "Address is too long")
	}
	
	// Check for basic address components
	if !v.containsAddressComponents(address) {
		errors = append(errors, "Address appears to be incomplete")
	}
	
	return len(errors) == 0, errors
}

func (v *InputValidator) containsAddressComponents(address string) bool {
	// Check for street number and name
	hasNumber := regexp.MustCompile(`\d+`).MatchString(address)
	hasStreet := regexp.MustCompile(`\b(street|st|avenue|ave|road|rd|boulevard|blvd|lane|ln|drive|dr|court|ct|way|place|pl)\b`, regexp.IgnoreCase).MatchString(address)
	
	return hasNumber && hasStreet
}

// IP address validation
func (v *InputValidator) ValidateIP(ip string) bool {
	ipv4Regex := regexp.MustCompile(`^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$`)
	ipv6Regex := regexp.MustCompile(`^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$`)
	
	return ipv4Regex.MatchString(ip) || ipv6Regex.MatchString(ip)
}

// Comprehensive validation function
func (v *InputValidator) ValidateAll(data map[string]string) (bool, map[string][]string) {
	allErrors := make(map[string][]string)
	isValid := true
	
	for field, value := range data {
		if valid, errors := v.ValidateField(field, value); !valid {
			allErrors[field] = errors
			isValid = false
		}
	}
	
	return isValid, allErrors
}
