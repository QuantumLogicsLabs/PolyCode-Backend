package validator

import (
	"fmt"
	"net"
	"net/url"
	"reflect"
	"regexp"
	"strconv"
	"strings"
	"time"
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
		
		// Check for json tag if available
		if jsonTag := field.Tag.Get("json"); jsonTag != "" {
			if parts := strings.Split(jsonTag, ","); parts[0] != "" {
				fieldName = parts[0]
			}
		}
		
		// Get field value
		var value interface{}
		if fieldValue.IsValid() {
			value = fieldValue.Interface()
		}
		
		// Store in data map
		result.Data[fieldName] = value
		
		// Apply validation rules
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

// Custom validation functions

// ValidateCreditCard validates credit card number
func (av *AdvancedValidator) ValidateCreditCard(cardNumber string) error {
	if cardNumber == "" {
		return fmt.Errorf("credit card number is required")
	}
	
	// Remove spaces and dashes
	cleaned := strings.ReplaceAll(strings.ReplaceAll(cardNumber, " ", ""), "-", "")
	
	// Check if it's all digits
	if !av.IsNumeric(cleaned) {
		return fmt.Errorf("credit card number must contain only digits")
	}
	
	// Luhn algorithm
	sum := 0
	alternate := false
	
	for i := len(cleaned) - 1; i >= 0; i-- {
		digit, err := strconv.Atoi(string(cleaned[i]))
		if err != nil {
			return fmt.Errorf("invalid credit card number")
		}
		
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

// ValidateCreditCardType validates credit card type
func (av *AdvancedValidator) ValidateCreditCardType(cardNumber, cardType string) error {
	if err := av.ValidateCreditCard(cardNumber); err != nil {
		return err
	}
	
	cleaned := strings.ReplaceAll(strings.ReplaceAll(cardNumber, " ", ""), "-", "")
	
	switch strings.ToLower(cardType) {
	case "visa":
		if !strings.HasPrefix(cleaned, "4") {
			return fmt.Errorf("invalid Visa card number")
		}
	case "mastercard":
		if !strings.HasPrefix(cleaned, "5") {
			return fmt.Errorf("invalid Mastercard number")
		}
	case "amex", "american express":
		if !strings.HasPrefix(cleaned, "3") || len(cleaned) != 15 {
			return fmt.Errorf("invalid American Express card number")
		}
	case "discover":
		if !strings.HasPrefix(cleaned, "6") {
			return fmt.Errorf("invalid Discover card number")
		}
	default:
		return fmt.Errorf("unsupported card type: %s", cardType)
	}
	
	return nil
}

// ValidateCVV validates CVV/CVC code
func (av *AdvancedValidator) ValidateCVV(cvv string, cardType string) error {
	if cvv == "" {
		return fmt.Errorf("CVV is required")
	}
	
	if !av.IsNumeric(cvv) {
		return fmt.Errorf("CVV must contain only digits")
	}
	
	expectedLength := 3
	if strings.ToLower(cardType) == "amex" || strings.ToLower(cardType) == "american express" {
		expectedLength = 4
	}
	
	if len(cvv) != expectedLength {
		return fmt.Errorf("CVV must be %d digits for %s", expectedLength, cardType)
	}
	
	return nil
}

// ValidateExpiryDate validates credit card expiry date
func (av *AdvancedValidator) ValidateExpiryDate(expiryDate string) error {
	if expiryDate == "" {
		return fmt.Errorf("expiry date is required")
	}
	
	// Expected format: MM/YY or MM/YYYY
	parts := strings.Split(expiryDate, "/")
	if len(parts) != 2 {
		return fmt.Errorf("expiry date must be in MM/YY or MM/YYYY format")
	}
	
	month, err := strconv.Atoi(parts[0])
	if err != nil {
		return fmt.Errorf("invalid month in expiry date")
	}
	
	if month < 1 || month > 12 {
		return fmt.Errorf("invalid month in expiry date")
	}
	
	year, err := strconv.Atoi(parts[1])
	if err != nil {
		return fmt.Errorf("invalid year in expiry date")
	}
	
	// Handle 2-digit year
	if year < 100 {
		currentYear := time.Now().Year()
		century := currentYear / 100 * 100
		year += century
		
		// If year is in the past, assume next century
		if year < currentYear {
			year += 100
		}
	}
	
	now := time.Now()
	expiry := time.Date(year, time.Month(month), 1, 23, 59, 59, 0, time.UTC)
	
	if expiry.Before(now) {
		return fmt.Errorf("card has expired")
	}
	
	return nil
}

// ValidateIBAN validates IBAN (International Bank Account Number)
func (av *AdvancedValidator) ValidateIBAN(iban string) error {
	if iban == "" {
		return fmt.Errorf("IBAN is required")
	}
	
	// Remove spaces
	cleaned := strings.ReplaceAll(iban, " ", "")
	
	// Check if it's alphanumeric
	if !av.IsAlphanumeric(cleaned) {
		return fmt.Errorf("IBAN must contain only letters and digits")
	}
	
	// Convert to uppercase
	cleaned = strings.ToUpper(cleaned)
	
	// Check length (varies by country, minimum 15, maximum 34)
	if len(cleaned) < 15 || len(cleaned) > 34 {
		return fmt.Errorf("IBAN length is invalid")
	}
	
	// Move first 4 characters to the end
	rearranged := cleaned[4:] + cleaned[:4]
	
	// Replace letters with numbers
	var numeric string
	for _, char := range rearranged {
		if char >= 'A' && char <= 'Z' {
			numeric += strconv.Itoa(int(char-'A') + 10)
		} else {
			numeric += string(char)
		}
	}
	
	// Mod 97 check
	num, err := strconv.ParseInt(numeric, 10, 64)
	if err != nil {
		return fmt.Errorf("invalid IBAN format")
	}
	
	if num%97 != 1 {
		return fmt.Errorf("invalid IBAN checksum")
	}
	
	return nil
}

// ValidateRoutingNumber validates US routing number
func (av *AdvancedValidator) ValidateRoutingNumber(routingNumber string) error {
	if routingNumber == "" {
		return fmt.Errorf("routing number is required")
	}
	
	if !av.IsNumeric(routingNumber) {
		return fmt.Errorf("routing number must contain only digits")
	}
	
	if len(routingNumber) != 9 {
		return fmt.Errorf("routing number must be 9 digits")
	}
	
	// Checksum calculation
	digits := make([]int, 9)
	for i, char := range routingNumber {
		digit, _ := strconv.Atoi(string(char))
		digits[i] = digit
	}
	
	checksum := 7*(digits[0]+digits[3]+digits[6]) +
		3*(digits[1]+digits[4]+digits[7]) +
		9*(digits[2]+digits[5]+digits[8])
	
	if checksum%10 != 0 {
		return fmt.Errorf("invalid routing number checksum")
	}
	
	return nil
}

// ValidateIPAddress validates IP address (IPv4 or IPv6)
func (av *AdvancedValidator) ValidateIPAddress(ip string) error {
	if ip == "" {
		return fmt.Errorf("IP address is required")
	}
	
	if net.ParseIP(ip) == nil {
		return fmt.Errorf("invalid IP address")
	}
	
	return nil
}

// ValidateIPv4 validates IPv4 address
func (av *AdvancedValidator) ValidateIPv4(ip string) error {
	if ip == "" {
		return fmt.Errorf("IPv4 address is required")
	}
	
	parsedIP := net.ParseIP(ip)
	if parsedIP == nil {
		return fmt.Errorf("invalid IPv4 address")
	}
	
	// Check if it's IPv4
	if parsedIP.To4() == nil {
		return fmt.Errorf("address is not IPv4")
	}
	
	return nil
}

// ValidateIPv6 validates IPv6 address
func (av *AdvancedValidator) ValidateIPv6(ip string) error {
	if ip == "" {
		return fmt.Errorf("IPv6 address is required")
	}
	
	parsedIP := net.ParseIP(ip)
	if parsedIP == nil {
		return fmt.Errorf("invalid IPv6 address")
	}
	
	// Check if it's IPv6
	if parsedIP.To4() != nil {
		return fmt.Errorf("address is not IPv6")
	}
	
	return nil
}

// ValidateMACAddress validates MAC address
func (av *AdvancedValidator) ValidateMACAddress(mac string) error {
	if mac == "" {
		return fmt.Errorf("MAC address is required")
	}
	
	// Try parsing as hardware address
	_, err := net.ParseMAC(mac)
	if err != nil {
		return fmt.Errorf("invalid MAC address")
	}
	
	return nil
}

// ValidateURL validates URL
func (av *AdvancedValidator) ValidateURL(urlStr string) error {
	if urlStr == "" {
		return fmt.Errorf("URL is required")
	}
	
	parsedURL, err := url.Parse(urlStr)
	if err != nil {
		return fmt.Errorf("invalid URL")
	}
	
	if parsedURL.Scheme == "" {
		return fmt.Errorf("URL must have a scheme (http, https, etc.)")
	}
	
	if parsedURL.Host == "" {
		return fmt.Errorf("URL must have a host")
	}
	
	return nil
}

// ValidateURLWithScheme validates URL with specific scheme
func (av *AdvancedValidator) ValidateURLWithScheme(urlStr, expectedScheme string) error {
	if err := av.ValidateURL(urlStr); err != nil {
		return err
	}
	
	parsedURL, _ := url.Parse(urlStr)
	if parsedURL.Scheme != expectedScheme {
		return fmt.Errorf("URL must use %s scheme", expectedScheme)
	}
	
	return nil
}

// ValidateDomain validates domain name
func (av *AdvancedValidator) ValidateDomain(domain string) error {
	if domain == "" {
		return fmt.Errorf("domain is required")
	}
	
	// Basic domain validation
	if len(domain) > 253 {
		return fmt.Errorf("domain name too long")
	}
	
	// Check each label
	labels := strings.Split(domain, ".")
	for _, label := range labels {
		if len(label) == 0 || len(label) > 63 {
			return fmt.Errorf("invalid domain label length")
		}
		
		// Check if label starts or ends with hyphen
		if strings.HasPrefix(label, "-") || strings.HasSuffix(label, "-") {
			return fmt.Errorf("domain label cannot start or end with hyphen")
		}
		
		// Check if label contains only valid characters
		if !av.IsDomainLabel(label) {
			return fmt.Errorf("invalid domain label: %s", label)
		}
	}
	
	return nil
}

// IsDomainLabel checks if string is a valid domain label
func (av *AdvancedValidator) IsDomainLabel(label string) bool {
	if label == "" {
		return false
	}
	
	// First character must be letter or digit
	if !av.IsAlphaNumeric(string(label[0])) {
		return false
	}
	
	// Last character must be letter or digit
	if !av.IsAlphaNumeric(string(label[len(label)-1])) {
		return false
	}
	
	// Middle characters can be letters, digits, or hyphens
	for i := 1; i < len(label)-1; i++ {
		char := string(label[i])
		if !av.IsAlphaNumeric(char) && char != "-" {
			return false
		}
	}
	
	return true
}

// ValidateUUID validates UUID
func (av *AdvancedValidator) ValidateUUID(uuid string) error {
	if uuid == "" {
		return fmt.Errorf("UUID is required")
	}
	
	// UUID regex pattern
	pattern := `^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$`
	
	regex, err := av.getRegex(pattern)
	if err != nil {
		return fmt.Errorf("failed to compile UUID regex")
	}
	
	if !regex.MatchString(uuid) {
		return fmt.Errorf("invalid UUID format")
	}
	
	return nil
}

// ValidateISBN validates ISBN (ISBN-10 or ISBN-13)
func (av *AdvancedValidator) ValidateISBN(isbn string) error {
	if isbn == "" {
		return fmt.Errorf("ISBN is required")
	}
	
	// Remove hyphens and spaces
	cleaned := strings.ReplaceAll(strings.ReplaceAll(isbn, "-", ""), " ", "")
	
	if len(cleaned) == 10 {
		return av.validateISBN10(cleaned)
	} else if len(cleaned) == 13 {
		return av.validateISBN13(cleaned)
	} else {
		return fmt.Errorf("ISBN must be 10 or 13 characters")
	}
}

func (av *AdvancedValidator) validateISBN10(isbn string) error {
	// Check if all characters are digits except possibly the last one
	for i := 0; i < 9; i++ {
		if !av.IsNumeric(string(isbn[i])) {
			return fmt.Errorf("invalid ISBN-10 format")
		}
	}
	
	// Last character can be digit or X
	lastChar := string(isbn[9])
	if !av.IsNumeric(lastChar) && lastChar != "X" {
		return fmt.Errorf("invalid ISBN-10 checksum")
	}
	
	// Calculate checksum
	sum := 0
	for i := 0; i < 9; i++ {
		digit, _ := strconv.Atoi(string(isbn[i]))
		sum += digit * (10 - i)
	}
	
	checksum := 11 - (sum % 11)
	if checksum == 10 {
		if lastChar != "X" {
			return fmt.Errorf("invalid ISBN-10 checksum")
		}
	} else if checksum == 11 {
		if lastChar != "0" {
			return fmt.Errorf("invalid ISBN-10 checksum")
		}
	} else {
		if lastChar != strconv.Itoa(checksum) {
			return fmt.Errorf("invalid ISBN-10 checksum")
		}
	}
	
	return nil
}

func (av *AdvancedValidator) validateISBN13(isbn string) error {
	// Check if all characters are digits
	if !av.IsNumeric(isbn) {
		return fmt.Errorf("invalid ISBN-13 format")
	}
	
	// Calculate checksum
	sum := 0
	for i := 0; i < 12; i++ {
		digit, _ := strconv.Atoi(string(isbn[i]))
		if i%2 == 0 {
			sum += digit
		} else {
			sum += digit * 3
		}
	}
	
	checksum := (10 - (sum % 10)) % 10
	lastDigit, _ := strconv.Atoi(string(isbn[12]))
	
	if lastDigit != checksum {
		return fmt.Errorf("invalid ISBN-13 checksum")
	}
	
	return nil
}

// ValidateISBN10 validates ISBN-10 specifically
func (av *AdvancedValidator) ValidateISBN10(isbn string) error {
	if isbn == "" {
		return fmt.Errorf("ISBN-10 is required")
	}
	
	cleaned := strings.ReplaceAll(strings.ReplaceAll(isbn, "-", ""), " ", "")
	
	if len(cleaned) != 10 {
		return fmt.Errorf("ISBN-10 must be 10 characters")
	}
	
	return av.validateISBN10(cleaned)
}

// ValidateISBN13 validates ISBN-13 specifically
func (av *AdvancedValidator) ValidateISBN13(isbn string) error {
	if isbn == "" {
		return fmt.Errorf("ISBN-13 is required")
	}
	
	cleaned := strings.ReplaceAll(strings.ReplaceAll(isbn, "-", ""), " ", "")
	
	if len(cleaned) != 13 {
		return fmt.Errorf("ISBN-13 must be 13 characters")
	}
	
	return av.validateISBN13(cleaned)
}

// ValidatePostalCode validates postal code for specific country
func (av *AdvancedValidator) ValidatePostalCode(postalCode, countryCode string) error {
	if postalCode == "" {
		return fmt.Errorf("postal code is required")
	}
	
	countryCode = strings.ToUpper(countryCode)
	
	switch countryCode {
	case "US":
		return av.validateUSPostalCode(postalCode)
	case "CA":
		return av.validateCAPostalCode(postalCode)
	case "UK":
		return av.validateUKPostalCode(postalCode)
	case "DE":
		return av.validateDEPostalCode(postalCode)
	case "FR":
		return av.validateFRPostalCode(postalCode)
	case "JP":
		return av.validateJPPostalCode(postalCode)
	default:
		return fmt.Errorf("postal code validation not supported for country: %s", countryCode)
	}
}

func (av *AdvancedValidator) validateUSPostalCode(postalCode string) error {
	// US ZIP code: 5 digits or 5 digits + 4 digits
	pattern := `^\d{5}(-\d{4})?$`
	
	regex, err := av.getRegex(pattern)
	if err != nil {
		return fmt.Errorf("failed to compile US postal code regex")
	}
	
	if !regex.MatchString(postalCode) {
		return fmt.Errorf("invalid US ZIP code format")
	}
	
	return nil
}

func (av *AdvancedValidator) validateCAPostalCode(postalCode string) error {
	// Canadian postal code: A1A 1A1
	pattern := `^[A-Z]\d[A-Z] \d[A-Z]\d$`
	
	regex, err := av.getRegex(pattern)
	if err != nil {
		return fmt.Errorf("failed to compile CA postal code regex")
	}
	
	if !regex.MatchString(strings.ToUpper(postalCode)) {
		return fmt.Errorf("invalid Canadian postal code format")
	}
	
	return nil
}

func (av *AdvancedValidator) validateUKPostalCode(postalCode string) error {
	// UK postal code: complex pattern
	pattern := `^[A-Z]{1,2}\d[A-Z\d]? \d[A-Z]{2}$`
	
	regex, err := av.getRegex(pattern)
	if err != nil {
		return fmt.Errorf("failed to compile UK postal code regex")
	}
	
	if !regex.MatchString(strings.ToUpper(postalCode)) {
		return fmt.Errorf("invalid UK postal code format")
	}
	
	return nil
}

func (av *AdvancedValidator) validateDEPostalCode(postalCode string) error {
	// German postal code: 5 digits
	pattern := `^\d{5}$`
	
	regex, err := av.getRegex(pattern)
	if err != nil {
		return fmt.Errorf("failed to compile DE postal code regex")
	}
	
	if !regex.MatchString(postalCode) {
		return fmt.Errorf("invalid German postal code format")
	}
	
	return nil
}

func (av *AdvancedValidator) validateFRPostalCode(postalCode string) error {
	// French postal code: 5 digits
	pattern := `^\d{5}$`
	
	regex, err := av.getRegex(pattern)
	if err != nil {
		return fmt.Errorf("failed to compile FR postal code regex")
	}
	
	if !regex.MatchString(postalCode) {
		return fmt.Errorf("invalid French postal code format")
	}
	
	return nil
}

func (av *AdvancedValidator) validateJPPostalCode(postalCode string) error {
	// Japanese postal code: 3 digits + 4 digits
	pattern := `^\d{3}-\d{4}$`
	
	regex, err := av.getRegex(pattern)
	if err != nil {
		return fmt.Errorf("failed to compile JP postal code regex")
	}
	
	if !regex.MatchString(postalCode) {
		return fmt.Errorf("invalid Japanese postal code format")
	}
	
	return nil
}

// ValidateTime validates time string
func (av *AdvancedValidator) ValidateTime(timeStr, format string) error {
	if timeStr == "" {
		return fmt.Errorf("time is required")
	}
	
	if format == "" {
		format = "15:04:05"
	}
	
	_, err := time.Parse(format, timeStr)
	if err != nil {
		return fmt.Errorf("invalid time format: %v", err)
	}
	
	return nil
}

// ValidateDate validates date string
func (av *AdvancedValidator) ValidateDate(dateStr, format string) error {
	if dateStr == "" {
		return fmt.Errorf("date is required")
	}
	
	if format == "" {
		format = "2006-01-02"
	}
	
	_, err := time.Parse(format, dateStr)
	if err != nil {
		return fmt.Errorf("invalid date format: %v", err)
	}
	
	return nil
}

// ValidateDateTime validates datetime string
func (av *AdvancedValidator) ValidateDateTime(dateTimeStr, format string) error {
	if dateTimeStr == "" {
		return fmt.Errorf("datetime is required")
	}
	
	if format == "" {
		format = "2006-01-02 15:04:05"
	}
	
	_, err := time.Parse(format, dateTimeStr)
	if err != nil {
		return fmt.Errorf("invalid datetime format: %v", err)
	}
	
	return nil
}

// ValidateTimezone validates timezone string
func (av *AdvancedValidator) ValidateTimezone(timezone string) error {
	if timezone == "" {
		return fmt.Errorf("timezone is required")
	}
	
	// Try to load timezone
	_, err := time.LoadLocation(timezone)
	if err != nil {
		return fmt.Errorf("invalid timezone: %v", err)
	}
	
	return nil
}

// ValidateDuration validates duration string
func (av *AdvancedValidator) ValidateDuration(durationStr string) error {
	if durationStr == "" {
		return fmt.Errorf("duration is required")
	}
	
	_, err := time.ParseDuration(durationStr)
	if err != nil {
		return fmt.Errorf("invalid duration format: %v", err)
	}
	
	return nil
}

// ValidateColor validates color code (hex, rgb, rgba, hsl, hsla)
func (av *AdvancedValidator) ValidateColor(color string) error {
	if color == "" {
		return fmt.Errorf("color is required")
	}
	
	// Hex color
	if strings.HasPrefix(color, "#") {
		hex := color[1:]
		if len(hex) == 3 || len(hex) == 6 {
			if !av.IsHexadecimal(hex) {
				return fmt.Errorf("invalid hex color format")
			}
			return nil
		}
		return fmt.Errorf("invalid hex color length")
	}
	
	// RGB/RGBA color
	if strings.HasPrefix(color, "rgb") {
		rgbPattern := `^rgba?\(\s*(\d{1,3}%?)\s*,\s*(\d{1,3}%?)\s*,\s*(\d{1,3}%?)\s*(,\s*[\d.]+\s*)?\)$`
		regex, err := av.getRegex(rgbPattern)
		if err != nil {
			return fmt.Errorf("failed to compile RGB regex")
		}
		
		if !regex.MatchString(color) {
			return fmt.Errorf("invalid RGB/RGBA color format")
		}
		return nil
	}
	
	// HSL/HSLA color
	if strings.HasPrefix(color, "hsl") {
		hslPattern := `^hsla?\(\s*(\d{1,3})\s*,\s*(\d{1,3}%?)\s*,\s*(\d{1,3}%?)\s*(,\s*[\d.]+\s*)?\)$`
		regex, err := av.getRegex(hslPattern)
		if err != nil {
			return fmt.Errorf("failed to compile HSL regex")
		}
		
		if !regex.MatchString(color) {
			return fmt.Errorf("invalid HSL/HSLA color format")
		}
		return nil
	}
	
	// Named colors (basic validation)
	namedColors := []string{
		"red", "green", "blue", "yellow", "orange", "purple", "pink", "brown",
		"black", "white", "gray", "grey", "cyan", "magenta", "lime", "navy",
		"teal", "olive", "maroon", "aqua", "fuchsia", "silver", "gold",
	}
	
	for _, namedColor := range namedColors {
		if strings.EqualFold(color, namedColor) {
			return nil
		}
	}
	
	return fmt.Errorf("invalid color format")
}

// ValidateHexColor validates hex color specifically
func (av *AdvancedValidator) ValidateHexColor(color string) error {
	if color == "" {
		return fmt.Errorf("hex color is required")
	}
	
	if !strings.HasPrefix(color, "#") {
		return fmt.Errorf("hex color must start with #")
	}
	
	hex := color[1:]
	
	if len(hex) != 3 && len(hex) != 6 {
		return fmt.Errorf("hex color must be 3 or 6 characters")
	}
	
	if !av.IsHexadecimal(hex) {
		return fmt.Errorf("hex color must contain only hexadecimal characters")
	}
	
	return nil
}

// ValidateRGBColor validates RGB color
func (av *AdvancedValidator) ValidateRGBColor(color string) error {
	if color == "" {
		return fmt.Errorf("RGB color is required")
	}
	
	pattern := `^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$`
	regex, err := av.getRegex(pattern)
	if err != nil {
		return fmt.Errorf("failed to compile RGB regex")
	}
	
	if !regex.MatchString(color) {
		return fmt.Errorf("invalid RGB color format")
	}
	
	return nil
}

// ValidateRGBAColor validates RGBA color
func (av *AdvancedValidator) ValidateRGBAColor(color string) error {
	if color == "" {
		return fmt.Errorf("RGBA color is required")
	}
	
	pattern := `^rgba\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*([\d.]+)\s*\)$`
	regex, err := av.getRegex(pattern)
	if err != nil {
		return fmt.Errorf("failed to compile RGBA regex")
	}
	
	if !regex.MatchString(color) {
		return fmt.Errorf("invalid RGBA color format")
	}
	
	return nil
}

// ValidateLanguageCode validates language code (ISO 639-1)
func (av *AdvancedValidator) ValidateLanguageCode(code string) error {
	if code == "" {
		return fmt.Errorf("language code is required")
	}
	
	// ISO 639-1: 2 letters
	if len(code) == 2 && av.IsAlpha(code) {
		return nil
	}
	
	// ISO 639-2: 3 letters
	if len(code) == 3 && av.IsAlpha(code) {
		return nil
	}
	
	// ISO 639-1 with country: en-US, fr-FR, etc.
	if len(code) == 5 && code[2] == '-' {
		lang := code[:2]
		country := code[3:]
		if av.IsAlpha(lang) && av.IsAlpha(country) {
			return nil
		}
	}
	
	return fmt.Errorf("invalid language code format")
}

// ValidateCountryCode validates country code (ISO 3166-1 alpha-2)
func (av *AdvancedValidator) ValidateCountryCode(code string) error {
	if code == "" {
		return fmt.Errorf("country code is required")
	}
	
	if len(code) != 2 || !av.IsAlpha(code) {
		return fmt.Errorf("country code must be 2 letters")
	}
	
	// Convert to uppercase and check if it's a valid country code
	// This is a simplified check - in practice, you'd want a complete list
	code = strings.ToUpper(code)
	
	// Basic validation for common country codes
	validCodes := []string{
		"US", "CA", "GB", "UK", "DE", "FR", "IT", "ES", "PT", "NL",
		"BE", "AT", "CH", "SE", "NO", "DK", "FI", "PL", "CZ", "HU",
		"GR", "TR", "RU", "UA", "JP", "CN", "KR", "IN", "AU", "NZ",
		"BR", "AR", "MX", "CL", "PE", "CO", "ZA", "EG", "IL", "SA",
		"AE", "TH", "VN", "PH", "MY", "SG", "ID", "HK", "TW", "MO",
	}
	
	for _, validCode := range validCodes {
		if code == validCode {
			return nil
		}
	}
	
	return fmt.Errorf("invalid country code")
}

// ValidateCurrencyCode validates currency code (ISO 4217)
func (av *AdvancedValidator) ValidateCurrencyCode(code string) error {
	if code == "" {
		return fmt.Errorf("currency code is required")
	}
	
	if len(code) != 3 || !av.IsAlpha(code) {
		return fmt.Errorf("currency code must be 3 letters")
	}
	
	// Convert to uppercase and check if it's a valid currency code
	code = strings.ToUpper(code)
	
	// Basic validation for common currency codes
	validCodes := []string{
		"USD", "EUR", "GBP", "JPY", "CNY", "CAD", "AUD", "CHF", "SEK",
		"NOK", "DKK", "PLN", "CZK", "HUF", "RUB", "UAH", "BRL", "MXN",
		"ARS", "CLP", "COP", "PEN", "ZAR", "EGP", "ILS", "SAR", "AED",
		"THB", "VND", "PHP", "MYR", "SGD", "IDR", "HKD", "TWD", "KRW",
		"INR", "NZD", "TRY", "PLN", "NOK", "DKK", "SEK", "CHF", "ISK",
	}
	
	for _, validCode := range validCodes {
		if code == validCode {
			return nil
		}
	}
	
	return fmt.Errorf("invalid currency code")
}

// Helper functions

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
	return true
}

func (av *AdvancedValidator) IsAlphanumeric(s string) bool {
	for _, char := range s {
		if !((char >= '0' && char <= '9') || (char >= 'A' && char <= 'Z') || (char >= 'a' && char <= 'z')) {
			return false
		}
	}
	return true
}

func (av *AdvancedValidator) IsAlpha(s string) bool {
	for _, char := range s {
		if !((char >= 'A' && char <= 'Z') || (char >= 'a' && char <= 'z')) {
			return false
		}
	}
	return true
}

func (av *AdvancedValidator) IsAlphaNumeric(s string) bool {
	for _, char := range s {
		if !((char >= '0' && char <= '9') || (char >= 'A' && char <= 'Z') || (char >= 'a' && char <= 'z')) {
			return false
		}
	}
	return true
}

func (av *AdvancedValidator) IsHexadecimal(s string) bool {
	for _, char := range s {
		if !((char >= '0' && char <= '9') || (char >= 'A' && char <= 'F') || (char >= 'a' && char <= 'f')) {
			return false
		}
	}
	return true
}

// Validation rule builders

// Required creates a required validation rule
func (av *AdvancedValidator) Required(message string) ValidationRule {
	if message == "" {
		message = "field is required"
	}
	
	return ValidationRule{
		Name:      "required",
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

// MinLength creates a minimum length validation rule
func (av *AdvancedValidator) MinLength(min int, message string) ValidationRule {
	if message == "" {
		message = fmt.Sprintf("minimum length is %d", min)
	}
	
	return ValidationRule{
		Name: "min_length",
		Validator: func(value interface{}) error {
			if str, ok := value.(string); ok {
				if len(str) < min {
					return fmt.Errorf(message)
				}
			}
			return nil
		},
		Message: message,
	}
}

// MaxLength creates a maximum length validation rule
func (av *AdvancedValidator) MaxLength(max int, message string) ValidationRule {
	if message == "" {
		message = fmt.Sprintf("maximum length is %d", max)
	}
	
	return ValidationRule{
		Name: "max_length",
		Validator: func(value interface{}) error {
			if str, ok := value.(string); ok {
				if len(str) > max {
					return fmt.Errorf(message)
				}
			}
			return nil
		},
		Message: message,
	}
}

// Range creates a range validation rule
func (av *AdvancedValidator) Range(min, max int, message string) ValidationRule {
	if message == "" {
		message = fmt.Sprintf("value must be between %d and %d", min, max)
	}
	
	return ValidationRule{
		Name: "range",
		Validator: func(value interface{}) error {
			if num, ok := value.(int); ok {
				if num < min || num > max {
					return fmt.Errorf(message)
				}
			}
			return nil
		},
		Message: message,
	}
}

// Email creates an email validation rule
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

// Custom creates a custom validation rule
func (av *AdvancedValidator) Custom(name string, validator func(interface{}) error, message string) ValidationRule {
	return ValidationRule{
		Name:      name,
		Validator: validator,
		Message:   message,
	}
}
