package main

import (
	"crypto/md5"
	"crypto/sha1"
	"crypto/sha256"
	"encoding/hex"
	"fmt"
	"strings"
	"unicode"
)

// SecurityValidator provides security-focused validation utilities
type SecurityValidator struct {
	weakPasswords map[string]bool
}

// NewSecurityValidator creates a new security validator
func NewSecurityValidator() *SecurityValidator {
	return &SecurityValidator{
		weakPasswords: map[string]bool{
			"password":    true,
			"123456":      true,
			"123456789":   true,
			"qwerty":      true,
			"abc123":      true,
			"password123": true,
			"admin":       true,
			"letmein":     true,
			"welcome":     true,
			"monkey":      true,
			"12345678":    true,
			"iloveyou":    true,
		},
	}
}

// PasswordStrength represents password strength levels
type PasswordStrength int

const (
	PasswordWeak PasswordStrength = iota
	PasswordFair
	PasswordGood
	PasswordStrong
	PasswordVeryStrong
)

func (ps PasswordStrength) String() string {
	switch ps {
	case PasswordWeak:
		return "Weak"
	case PasswordFair:
		return "Fair"
	case PasswordGood:
		return "Good"
	case PasswordStrong:
		return "Strong"
	case PasswordVeryStrong:
		return "Very Strong"
	default:
		return "Unknown"
	}
}

// PasswordPolicy represents password policy requirements
type PasswordPolicy struct {
	MinLength        int
	MaxLength        int
	RequireUppercase bool
	RequireLowercase bool
	RequireNumbers   bool
	RequireSymbols   bool
	AllowCommon      bool
	AllowSpaces      bool
}

// DefaultPasswordPolicy returns a default password policy
func DefaultPasswordPolicy() PasswordPolicy {
	return PasswordPolicy{
		MinLength:        8,
		MaxLength:        128,
		RequireUppercase: true,
		RequireLowercase: true,
		RequireNumbers:   true,
		RequireSymbols:   true,
		AllowCommon:      false,
		AllowSpaces:      false,
	}
}

// ValidatePassword validates password against policy
func (sv *SecurityValidator) ValidatePassword(password string, policy PasswordPolicy) error {
	if password == "" {
		return fmt.Errorf("password is required")
	}

	if len(password) < policy.MinLength {
		return fmt.Errorf("password must be at least %d characters", policy.MinLength)
	}

	if policy.MaxLength > 0 && len(password) > policy.MaxLength {
		return fmt.Errorf("password must be at most %d characters", policy.MaxLength)
	}

	hasUpper := false
	hasLower := false
	hasNumber := false
	hasSymbol := false

	for _, char := range password {
		switch {
		case unicode.IsUpper(char):
			hasUpper = true
		case unicode.IsLower(char):
			hasLower = true
		case unicode.IsNumber(char):
			hasNumber = true
		case unicode.IsPunct(char) || unicode.IsSymbol(char):
			hasSymbol = true
		}
	}

	if policy.RequireUppercase && !hasUpper {
		return fmt.Errorf("password must contain at least one uppercase letter")
	}

	if policy.RequireLowercase && !hasLower {
		return fmt.Errorf("password must contain at least one lowercase letter")
	}

	if policy.RequireNumbers && !hasNumber {
		return fmt.Errorf("password must contain at least one number")
	}

	if policy.RequireSymbols && !hasSymbol {
		return fmt.Errorf("password must contain at least one symbol")
	}

	if !policy.AllowCommon {
		lowercasePassword := strings.ToLower(password)
		if sv.weakPasswords[lowercasePassword] {
			return fmt.Errorf("password is too common")
		}
	}

	if !policy.AllowSpaces && strings.Contains(password, " ") {
		return fmt.Errorf("password cannot contain spaces")
	}

	return nil
}

// GetPasswordStrength calculates password strength
func (sv *SecurityValidator) GetPasswordStrength(password string) PasswordStrength {
	if password == "" {
		return PasswordWeak
	}

	score := 0
	length := len(password)
	if length >= 8 {
		score++
	}
	if length >= 12 {
		score++
	}
	if length >= 16 {
		score++
	}

	hasUpper := false
	hasLower := false
	hasNumber := false
	hasSymbol := false

	for _, char := range password {
		switch {
		case unicode.IsUpper(char):
			hasUpper = true
		case unicode.IsLower(char):
			hasLower = true
		case unicode.IsNumber(char):
			hasNumber = true
		case unicode.IsPunct(char) || unicode.IsSymbol(char):
			hasSymbol = true
		}
	}

	if hasUpper {
		score++
	}
	if hasLower {
		score++
	}
	if hasNumber {
		score++
	}
	if hasSymbol {
		score++
	}

	if !sv.hasRepeatingChars(password, 2) {
		score++
	}

	if !sv.hasSequentialChars(password, 3) {
		score++
	}

	lowercasePassword := strings.ToLower(password)
	if sv.weakPasswords[lowercasePassword] {
		score -= 2
	}

	switch {
	case score >= 8:
		return PasswordVeryStrong
	case score >= 6:
		return PasswordStrong
	case score >= 4:
		return PasswordGood
	case score >= 2:
		return PasswordFair
	default:
		return PasswordWeak
	}
}

// ValidateJWT validates JWT format
func (sv *SecurityValidator) ValidateJWT(jwt string) error {
	if jwt == "" {
		return fmt.Errorf("JWT is required")
	}

	parts := strings.Split(jwt, ".")
	if len(parts) != 3 {
		return fmt.Errorf("JWT must have 3 parts separated by dots")
	}

	for i, part := range parts {
		if len(part) == 0 {
			return fmt.Errorf("JWT part %d is empty", i+1)
		}
		if !sv.isBase64(part) {
			return fmt.Errorf("JWT part %d is not valid base64", i+1)
		}
	}

	return nil
}

// ValidateHash validates hash format
func (sv *SecurityValidator) ValidateHash(hash, algorithm string) error {
	if hash == "" {
		return fmt.Errorf("hash is required")
	}

	switch strings.ToLower(algorithm) {
	case "md5":
		if len(hash) != 32 || !sv.isHexadecimal(hash) {
			return fmt.Errorf("invalid MD5 hash")
		}
	case "sha1":
		if len(hash) != 40 || !sv.isHexadecimal(hash) {
			return fmt.Errorf("invalid SHA1 hash")
		}
	case "sha256":
		if len(hash) != 64 || !sv.isHexadecimal(hash) {
			return fmt.Errorf("invalid SHA256 hash")
		}
	default:
		return fmt.Errorf("unsupported hash algorithm: %s", algorithm)
	}

	return nil
}

// GenerateHash generates a hash for testing
func (sv *SecurityValidator) GenerateHash(data string, algorithm string) string {
	switch strings.ToLower(algorithm) {
	case "md5":
		hash := md5.Sum([]byte(data))
		return hex.EncodeToString(hash[:])
	case "sha1":
		hash := sha1.Sum([]byte(data))
		return hex.EncodeToString(hash[:])
	case "sha256":
		hash := sha256.Sum256([]byte(data))
		return hex.EncodeToString(hash[:])
	default:
		return ""
	}
}

func (sv *SecurityValidator) hasRepeatingChars(password string, maxRepeating int) bool {
	runes := []rune(password)
	if len(runes) < maxRepeating {
		return false
	}

	for i := 0; i <= len(runes)-maxRepeating; i++ {
		char := runes[i]
		repeating := true

		for j := 1; j < maxRepeating; j++ {
			if runes[i+j] != char {
				repeating = false
				break
			}
		}

		if repeating {
			return true
		}
	}

	return false
}

func (sv *SecurityValidator) hasSequentialChars(password string, maxSequential int) bool {
	runes := []rune(password)
	if len(runes) < maxSequential {
		return false
	}

	for i := 0; i <= len(runes)-maxSequential; i++ {
		sequential := true

		for j := 1; j < maxSequential; j++ {
			if runes[i+j] != runes[i]+rune(j) {
				sequential = false
				break
			}
		}

		if sequential {
			return true
		}
	}

	return false
}

func (sv *SecurityValidator) isBase64(s string) bool {
	for _, char := range s {
		if !((char >= 'A' && char <= 'Z') || (char >= 'a' && char <= 'z') || (char >= '0' && char <= '9') || char == '+' || char == '/' || char == '=' || char == '_' || char == '-') {
			return false
		}
	}
	return true
}

func (sv *SecurityValidator) isHexadecimal(s string) bool {
	for _, char := range s {
		if !((char >= '0' && char <= '9') || (char >= 'A' && char <= 'F') || (char >= 'a' && char <= 'f')) {
			return false
		}
	}
	return len(s) > 0
}

func main() {
	fmt.Println("=== Security Validator Demo ===")
	sv := NewSecurityValidator()

	// 1. Password Policy & Strength
	policy := DefaultPasswordPolicy()
	pass := "SecureP@ssw0rd2026!"
	fmt.Printf("Validating '%s': %v\n", pass, sv.ValidatePassword(pass, policy) == nil)
	fmt.Printf("Password Strength: %s\n", sv.GetPasswordStrength(pass))

	// 2. Cryptographic Hashes
	sha256Hash := sv.GenerateHash("test-secret", "sha256")
	fmt.Printf("Generated SHA-256: %s\n", sha256Hash)
	fmt.Printf("Valid SHA-256: %v\n", sv.ValidateHash(sha256Hash, "sha256") == nil)

	// 3. JWT Validation
	jwtSample := "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkFkYW4iLCJpYXQiOjE1MTYyMzkwMjJ9.4z9f0z"
	fmt.Printf("Valid JWT Structure: %v\n", sv.ValidateJWT(jwtSample) == nil)
}
