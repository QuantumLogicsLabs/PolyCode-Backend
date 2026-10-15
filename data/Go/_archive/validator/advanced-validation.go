package main

import (
	"fmt"
	"net"
	"net/url"
	"reflect"
	"regexp"
	"strconv"
	"strings"
)

// AdvancedValidator provides advanced validation utilities
type AdvancedValidator struct {
	regexCache map[string]*regexp.Regexp
}

// NewAdvancedValidator creates a new advanced validator
func NewAdvancedValidator() *AdvancedValidator {
	return &AdvancedValidator{
		regexCache: make(map[string]*regexp.Regexp),
	}
}

// ValidationRule represents a validation rule
type ValidationRule struct {
	Name        string
	Validator   func(interface{}) error
	Message     string
	Required    bool
	StopOnError bool
}

// ValidationResult represents validation results
type ValidationResult struct {
	IsValid bool
	Errors  []ValidationError
	Data    map[string]interface{}
}

// ValidationError represents a validation error
type ValidationError struct {
	Field   string
	Value   interface{}
	Rule    string
	Message string
}

func (ve ValidationError) Error() string {
	return fmt.Sprintf("validation error on field '%s': %s", ve.Field, ve.Message)
}

// ValidateStruct validates a struct with rules
func (av *AdvancedValidator) ValidateStruct(data interface{}, rules map[string][]ValidationRule) *ValidationResult {
	result := &ValidationResult{
		IsValid: true,
		Errors:  []ValidationError{},
		Data:    make(map[string]interface{}),
	}

	val := reflect.ValueOf(data)
	if val.Kind() == reflect.Ptr {
		val = val.Elem()
	}

	if val.Kind() != reflect.Struct {
		result.Errors = append(result.Errors, ValidationError{
			Field:   "root",
			Value:   data,
			Rule:    "struct",
			Message: "input must be a struct",
		})
		result.IsValid = false
		return result
	}

	typ := val.Type()

	for i := 0; i < val.NumField(); i++ {
		field := typ.Field(i)
		fieldValue := val.Field(i)
		fieldName := field.Name

		if jsonTag := field.Tag.Get("json"); jsonTag != "" {
			if parts := strings.Split(jsonTag, ","); parts[0] != "" {
				fieldName = parts[0]
			}
		}

		var value interface{}
		if fieldValue.IsValid() && fieldValue.CanInterface() {
			value = fieldValue.Interface()
		}

		result.Data[fieldName] = value

		if fieldRules, exists := rules[fieldName]; exists {
			for _, rule := range fieldRules {
				if err := rule.Validator(value); err != nil {
					result.Errors = append(result.Errors, ValidationError{
						Field:   fieldName,
						Value:   value,
						Rule:    rule.Name,
						Message: rule.Message,
					})
					result.IsValid = false

					if rule.StopOnError {
						break
					}
				}
			}
		}
	}

	return result
}

// ValidateMap validates a map with rules
func (av *AdvancedValidator) ValidateMap(data map[string]interface{}, rules map[string][]ValidationRule) *ValidationResult {
	result := &ValidationResult{
		IsValid: true,
		Errors:  []ValidationError{},
		Data:    make(map[string]interface{}),
	}

	for key, value := range data {
		result.Data[key] = value

		if fieldRules, exists := rules[key]; exists {
			for _, rule := range fieldRules {
				if err := rule.Validator(value); err != nil {
					result.Errors = append(result.Errors, ValidationError{
						Field:   key,
						Value:   value,
						Rule:    rule.Name,
						Message: rule.Message,
					})
					result.IsValid = false

					if rule.StopOnError {
						break
					}
				}
			}
		}
	}

	return result
}

// ValidateCreditCard validates credit card number using Luhn algorithm
func (av *AdvancedValidator) ValidateCreditCard(cardNumber string) error {
	if cardNumber == "" {
		return fmt.Errorf("credit card number is required")
	}

	cleaned := strings.ReplaceAll(strings.ReplaceAll(cardNumber, " ", ""), "-", "")
	if !av.IsNumeric(cleaned) {
		return fmt.Errorf("credit card number must contain only digits")
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

	if sum%10 != 0 {
		return fmt.Errorf("invalid credit card number")
	}

	return nil
}

// ValidateIBAN validates IBAN (International Bank Account Number)
func (av *AdvancedValidator) ValidateIBAN(iban string) error {
	if iban == "" {
		return fmt.Errorf("IBAN is required")
	}

	cleaned := strings.ToUpper(strings.ReplaceAll(iban, " ", ""))
	if !av.IsAlphanumeric(cleaned) {
		return fmt.Errorf("IBAN must contain only letters and digits")
	}

	if len(cleaned) < 15 || len(cleaned) > 34 {
		return fmt.Errorf("IBAN length is invalid")
	}

	rearranged := cleaned[4:] + cleaned[:4]
	var numeric strings.Builder
	for _, char := range rearranged {
		if char >= 'A' && char <= 'Z' {
			numeric.WriteString(strconv.Itoa(int(char-'A') + 10))
		} else {
			numeric.WriteRune(char)
		}
	}

	// Piecewise mod 97 to avoid int64 overflow
	remainder := 0
	for _, ch := range numeric.String() {
		remainder = (remainder*10 + int(ch-'0')) % 97
	}

	if remainder != 1 {
		return fmt.Errorf("invalid IBAN checksum")
	}

	return nil
}

// ValidateIPAddress validates IP address
func (av *AdvancedValidator) ValidateIPAddress(ip string) error {
	if ip == "" {
		return fmt.Errorf("IP address is required")
	}
	if net.ParseIP(ip) == nil {
		return fmt.Errorf("invalid IP address")
	}
	return nil
}

// ValidateURL validates URL
func (av *AdvancedValidator) ValidateURL(urlStr string) error {
	if urlStr == "" {
		return fmt.Errorf("URL is required")
	}
	parsedURL, err := url.Parse(urlStr)
	if err != nil || parsedURL.Scheme == "" || parsedURL.Host == "" {
		return fmt.Errorf("invalid URL")
	}
	return nil
}

// ValidateUUID validates UUID format
func (av *AdvancedValidator) ValidateUUID(uuid string) error {
	if uuid == "" {
		return fmt.Errorf("UUID is required")
	}
	pattern := `^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$`
	regex, err := av.getRegex(pattern)
	if err != nil || !regex.MatchString(uuid) {
		return fmt.Errorf("invalid UUID format")
	}
	return nil
}

func (av *AdvancedValidator) IsValidEmail(email string) error {
	if email == "" {
		return fmt.Errorf("email cannot be empty")
	}
	pattern := `^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$`
	regex, err := av.getRegex(pattern)
	if err != nil || !regex.MatchString(email) {
		return fmt.Errorf("invalid email format")
	}
	return nil
}

func (av *AdvancedValidator) getRegex(pattern string) (*regexp.Regexp, error) {
	if regex, exists := av.regexCache[pattern]; exists {
		return regex, nil
	}
	regex, err := regexp.Compile(pattern)
	if err != nil {
		return nil, err
	}
	av.regexCache[pattern] = regex
	return regex, nil
}

func (av *AdvancedValidator) IsNumeric(s string) bool {
	for _, char := range s {
		if char < '0' || char > '9' {
			return false
		}
	}
	return len(s) > 0
}

func (av *AdvancedValidator) IsAlphanumeric(s string) bool {
	for _, char := range s {
		if !((char >= '0' && char <= '9') || (char >= 'A' && char <= 'Z') || (char >= 'a' && char <= 'z')) {
			return false
		}
	}
	return len(s) > 0
}

// Rule builders
func (av *AdvancedValidator) Required(message string) ValidationRule {
	if message == "" {
		message = "field is required"
	}
	return ValidationRule{
		Name: "required",
		Validator: func(value interface{}) error {
			if value == nil || value == "" {
				return fmt.Errorf(message)
			}
			return nil
		},
		Message:     message,
		Required:    true,
		StopOnError: true,
	}
}

func (av *AdvancedValidator) MinLength(min int, message string) ValidationRule {
	if message == "" {
		message = fmt.Sprintf("minimum length is %d", min)
	}
	return ValidationRule{
		Name: "min_length",
		Validator: func(value interface{}) error {
			if str, ok := value.(string); ok && len(str) < min {
				return fmt.Errorf(message)
			}
			return nil
		},
		Message: message,
	}
}

func (av *AdvancedValidator) Range(min, max int, message string) ValidationRule {
	if message == "" {
		message = fmt.Sprintf("value must be between %d and %d", min, max)
	}
	return ValidationRule{
		Name: "range",
		Validator: func(value interface{}) error {
			if num, ok := value.(int); ok && (num < min || num > max) {
				return fmt.Errorf(message)
			}
			return nil
		},
		Message: message,
	}
}

func (av *AdvancedValidator) Email(message string) ValidationRule {
	if message == "" {
		message = "invalid email format"
	}
	return ValidationRule{
		Name: "email",
		Validator: func(value interface{}) error {
			if str, ok := value.(string); ok {
				return av.IsValidEmail(str)
			}
			return nil
		},
		Message: message,
	}
}

type User struct {
	Name  string `json:"name"`
	Email string `json:"email"`
	Age   int    `json:"age"`
}

func main() {
	fmt.Println("=== Advanced Validator Demo ===")
	av := NewAdvancedValidator()

	// 1. Struct Validation
	rules := map[string][]ValidationRule{
		"name":  {av.Required("Name is required"), av.MinLength(3, "Name must have at least 3 chars")},
		"email": {av.Required("Email is required"), av.Email("Invalid email address")},
		"age":   {av.Range(18, 100, "Age must be between 18 and 100")},
	}

	user := User{Name: "Adan", Email: "adan@example.com", Age: 20}
	res := av.ValidateStruct(user, rules)
	fmt.Printf("User Struct Valid: %t (Errors: %d)\n", res.IsValid, len(res.Errors))

	// 2. Network & Identifiers
	fmt.Printf("Valid IP (192.168.1.1): %v\n", av.ValidateIPAddress("192.168.1.1") == nil)
	fmt.Printf("Valid URL (https://golang.org): %v\n", av.ValidateURL("https://golang.org") == nil)
	fmt.Printf("Valid UUID (123e4567-e89b-12d3-a456-426614174000): %v\n", av.ValidateUUID("123e4567-e89b-12d3-a456-426614174000") == nil)
	fmt.Printf("Valid Card (4111111111111111): %v\n", av.ValidateCreditCard("4111111111111111") == nil)
}
