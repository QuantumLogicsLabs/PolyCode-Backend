package validator

import (
	"fmt"
	"net"
	"regexp"
	"strings"
	"unicode"
)

// EmailValidator validates email addresses
type EmailValidator struct {
	domainValidator *DomainValidator
	rfcPatterns     map[string]*regexp.Regexp
}

// DomainValidator validates domain names
type DomainValidator struct {
	disposableDomains map[string]bool
	temporaryDomains  map[string]bool
}

// NewEmailValidator creates a new email validator
func NewEmailValidator() *EmailValidator {
	return &EmailValidator{
		domainValidator: NewDomainValidator(),
		rfcPatterns: map[string]*regexp.Regexp{
			"basic":    regexp.MustCompile(`^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$`),
			"strict":   regexp.MustCompile(`^[a-zA-Z0-9.!#$%&'*+/=?^_` + "`" + `{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$`),
			"username": regexp.MustCompile(`^[a-zA-Z0-9._%+-]{1,64}$`),
			"domain":   regexp.MustCompile(`^[a-zA-Z0-9.-]{1,253}$`),
		},
	}
}

// NewDomainValidator creates a new domain validator
func NewDomainValidator() *DomainValidator {
	// Common disposable email domains
	disposable := map[string]bool{
		"10minutemail.com":     true,
		"tempmail.org":         true,
		"guerrillamail.com":    true,
		"mailinator.com":       true,
		"throwaway.email":     true,
		"yopmail.com":          true,
		"temp-mail.org":        true,
		"maildrop.cc":          true,
		"temp-mail.io":         true,
		"20minutemail.com":     true,
	}

	// Common temporary email domains
	temporary := map[string]bool{
		"example.com":          true,
		"test.com":             true,
		"localhost":            true,
		"example.org":         true,
	}

	return &DomainValidator{
		disposableDomains: disposable,
		temporaryDomains:  temporary,
	}
}

// EmailValidationResult contains detailed validation results
type EmailValidationResult struct {
	Email          string
	LocalPart      string
	Domain         string
	IsValid        bool
	IsDisposable   bool
	IsTemporary    bool
	HasMXRecord    bool
	Errors         []string
	Warnings       []string
	Suggestions    []string
}

// Validate performs comprehensive email validation
func (v *EmailValidator) Validate(email string) *EmailValidationResult {
	result := &EmailValidationResult{
		Email: email,
	}

	// Basic format validation
	if !v.validateBasicFormat(email, result) {
		return result
	}

	// Split email into local part and domain
	localPart, domain, err := v.splitEmail(email)
	if err != nil {
		result.Errors = append(result.Errors, err.Error())
		return result
	}

	result.LocalPart = localPart
	result.Domain = domain

	// Validate local part
	v.validateLocalPart(localPart, result)

	// Validate domain
	v.validateDomain(domain, result)

	// Check for disposable/temporary domains
	v.checkDomainReputation(domain, result)

	// DNS validation (optional, can be slow)
	v.validateDNS(domain, result)

	// Set overall validity
	result.IsValid = len(result.Errors) == 0

	return result
}

// validateBasicFormat checks basic email format
func (v *EmailValidator) validateBasicFormat(email string, result *EmailValidationResult) bool {
	if email == "" {
		result.Errors = append(result.Errors, "email address is empty")
		return false
	}

	// Check for multiple @ symbols
	if strings.Count(email, "@") != 1 {
		result.Errors = append(result.Errors, "email must contain exactly one @ symbol")
		return false
	}

	// Check basic pattern
	if !v.rfcPatterns["basic"].MatchString(email) {
		result.Errors = append(result.Errors, "email format is invalid")
		return false
	}

	return true
}

// splitEmail splits email into local part and domain
func (v *EmailValidator) splitEmail(email string) (string, string, error) {
	parts := strings.Split(email, "@")
	if len(parts) != 2 {
		return "", "", fmt.Errorf("invalid email format")
	}

	localPart := strings.TrimSpace(parts[0])
	domain := strings.ToLower(strings.TrimSpace(parts[1]))

	if localPart == "" || domain == "" {
		return "", "", fmt.Errorf("local part and domain cannot be empty")
	}

	return localPart, domain, nil
}

// validateLocalPart validates the local part of email
func (v *EmailValidator) validateLocalPart(localPart string, result *EmailValidationResult) {
	// Length check
	if len(localPart) > 64 {
		result.Errors = append(result.Errors, "local part is too long (max 64 characters)")
	}

	// Check for consecutive dots
	if strings.Contains(localPart, "..") {
		result.Errors = append(result.Errors, "local part cannot contain consecutive dots")
	}

	// Check for leading/trailing dots
	if strings.HasPrefix(localPart, ".") || strings.HasSuffix(localPart, ".") {
		result.Errors = append(result.Errors, "local part cannot start or end with a dot")
	}

	// Check for invalid characters
	if !v.isValidLocalPartChars(localPart) {
		result.Errors = append(result.Errors, "local part contains invalid characters")
	}

	// Check for quoted local part
	if strings.HasPrefix(localPart, `"`) && strings.HasSuffix(localPart, `"`) {
		if len(localPart) < 3 {
			result.Errors = append(result.Errors, "quoted local part is too short")
		}
		// Additional validation for quoted local parts could be added here
	}

	// Warnings for common issues
	if strings.Contains(localPart, "+") {
		result.Warnings = append(result.Warnings, "email uses plus addressing")
	}
}

// validateDomain validates the domain part of email
func (v *EmailValidator) validateDomain(domain string, result *EmailValidationResult) {
	// Length check
	if len(domain) > 253 {
		result.Errors = append(result.Errors, "domain is too long (max 253 characters)")
	}

	// Check domain pattern
	if !v.rfcPatterns["domain"].MatchString(domain) {
		result.Errors = append(result.Errors, "domain format is invalid")
		return
	}

	// Check for consecutive dots
	if strings.Contains(domain, "..") {
		result.Errors = append(result.Errors, "domain cannot contain consecutive dots")
	}

	// Check for leading/trailing dots or hyphens
	if strings.HasPrefix(domain, ".") || strings.HasSuffix(domain, ".") ||
		strings.HasPrefix(domain, "-") || strings.HasSuffix(domain, "-") {
		result.Errors = append(result.Errors, "domain cannot start or end with dot or hyphen")
	}

	// Check TLD
	if !v.hasValidTLD(domain) {
		result.Errors = append(result.Errors, "domain has invalid TLD")
	}

	// Generate suggestions for common typos
	v.generateDomainSuggestions(domain, result)
}

// isValidLocalPartChars checks for valid characters in local part
func (v *EmailValidator) isValidLocalPartChars(localPart string) bool {
	// RFC 5322 allows more characters, but we'll use a practical subset
	validChars := "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789._%+-"
	
	for _, char := range localPart {
		if !strings.ContainsRune(validChars, char) && char != '"' {
			return false
		}
	}
	
	return true
}

// hasValidTLD checks if domain has a valid top-level domain
func (v *EmailValidator) hasValidTLD(domain string) bool {
	parts := strings.Split(domain, ".")
	if len(parts) < 2 {
		return false
	}

	tld := parts[len(parts)-1]
	
	// Basic TLD validation
	if len(tld) < 2 || len(tld) > 63 {
		return false
	}

	// Check if TLD contains only letters
	for _, char := range tld {
		if !unicode.IsLetter(char) {
			return false
		}
	}

	return true
}

// generateDomainSuggestions generates suggestions for common domain typos
func (v *EmailValidator) generateDomainSuggestions(domain string, result *EmailValidationResult) {
	commonDomains := []string{
		"gmail.com", "yahoo.com", "outlook.com", "hotmail.com",
		"icloud.com", "aol.com", "mail.com", "protonmail.com",
	}

	// Check for common typos
	suggestions := []string{
		v.fixCommonTypos(domain),
		v.suggestSimilarDomain(domain, commonDomains),
	}

	for _, suggestion := range suggestions {
		if suggestion != "" && suggestion != domain {
			result.Suggestions = append(result.Suggestions, suggestion)
		}
	}
}

// fixCommonTypos fixes common domain typos
func (v *EmailValidator) fixCommonTypos(domain string) string {
	typos := map[string]string{
		"gnail.com":     "gmail.com",
		"yahoo.co":      "yahoo.com",
		"hotmal.com":    "hotmail.com",
		"outlok.com":    "outlook.com",
		"gmaill.com":    "gmail.com",
		"yahooo.com":    "yahoo.com",
	}

	if fixed, exists := typos[domain]; exists {
		return fixed
	}

	return ""
}

// suggestSimilarDomain suggests similar domains
func (v *EmailValidator) suggestSimilarDomain(domain string, commonDomains []string) string {
	for _, common := range commonDomains {
		if v.levenshteinDistance(domain, common) <= 2 {
			return common
		}
	}
	return ""
}

// levenshteinDistance calculates the distance between two strings
func (v *EmailValidator) levenshteinDistance(s1, s2 string) int {
	if len(s1) == 0 {
		return len(s2)
	}
	if len(s2) == 0 {
		return len(s1)
	}

	// Simple implementation for short strings
	if s1 == s2 {
		return 0
	}

	if len(s1) == 1 {
		if strings.ContainsRune(s2, rune(s1[0])) {
			return len(s2) - 1
		}
		return len(s2)
	}

	if len(s2) == 1 {
		if strings.ContainsRune(s1, rune(s2[0])) {
			return len(s1) - 1
		}
		return len(s1)
	}

	// For longer strings, use a simplified approach
	if s1[0] == s2[0] {
		return v.levenshteinDistance(s1[1:], s2[1:])
	}

	return 1 + min(
		v.levenshteinDistance(s1[1:], s2),
		v.levenshteinDistance(s1, s2[1:]),
		v.levenshteinDistance(s1[1:], s2[1:]),
	)
}

// checkDomainReputation checks if domain is disposable or temporary
func (v *EmailValidator) checkDomainReputation(domain string, result *EmailValidationResult) {
	if v.domainValidator.isDisposable(domain) {
		result.IsDisposable = true
		result.Warnings = append(result.Warnings, "domain is known for disposable emails")
	}

	if v.domainValidator.isTemporary(domain) {
		result.IsTemporary = true
		result.Warnings = append(result.Warnings, "domain appears to be temporary")
	}
}

// validateDNS performs DNS validation
func (v *EmailValidator) validateDNS(domain string, result *EmailValidationResult) {
	// Check for MX records
	mxRecords, err := net.LookupMX(domain)
	if err != nil {
		result.Warnings = append(result.Warnings, "no MX records found")
	} else {
		result.HasMXRecord = len(mxRecords) > 0
	}

	// Check for A records as fallback
	if !result.HasMXRecord {
		_, err := net.LookupHost(domain)
		if err != nil {
			result.Warnings = append(result.Warnings, "no A records found")
		}
	}
}

// isDisposable checks if domain is a disposable email provider
func (dv *DomainValidator) isDisposable(domain string) bool {
	return dv.disposableDomains[domain]
}

// isTemporary checks if domain is a temporary email provider
func (dv *DomainValidator) isTemporary(domain string) bool {
	return dv.temporaryDomains[domain]
}

// QuickValidate performs basic email format validation
func (v *EmailValidator) QuickValidate(email string) bool {
	return v.rfcPatterns["basic"].MatchString(email)
}

// ValidateBatch validates multiple emails
func (v *EmailValidator) ValidateBatch(emails []string) []*EmailValidationResult {
	results := make([]*EmailValidationResult, len(emails))
	
	for i, email := range emails {
		results[i] = v.Validate(email)
	}
	
	return results
}

// AddDisposableDomain adds a domain to the disposable list
func (v *EmailValidator) AddDisposableDomain(domain string) {
	v.domainValidator.disposableDomains[domain] = true
}

// AddTemporaryDomain adds a domain to the temporary list
func (v *EmailValidator) AddTemporaryDomain(domain string) {
	v.domainValidator.temporaryDomains[domain] = true
}

// min returns the minimum of three integers
func min(a, b, c int) int {
	if a < b && a < c {
		return a
	}
	if b < c {
		return b
	}
	return c
}
