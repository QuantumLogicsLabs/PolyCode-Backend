package validator

import (
	"crypto/md5"
	"crypto/sha1"
	"crypto/sha256"
	"encoding/hex"
	"fmt"
	"regexp"
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
			"password":     true,
			"123456":       true,
			"123456789":    true,
			"qwerty":       true,
			"abc123":       true,
			"password123":  true,
			"admin":        true,
			"letmein":      true,
			"welcome":      true,
			"monkey":       true,
			"1234":         true,
			"12345":        true,
			"1234567890":   true,
			"1234567":      true,
			"12345678":     true,
			"iloveyou":     true,
			"adobe123":     true,
			"123123":       true,
			"sunshine":     true,
			"password1":    true,
			"princess":     true,
			"azerty":       true,
			"trustno1":     true,
			"000000":       true,
			"111111":       true,
			"222222":       true,
			"333333":       true,
			"444444":       true,
			"555555":       true,
			"666666":       true,
			"777777":       true,
			"888888":       true,
			"999999":       true,
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
	AllowRepeating   bool
	MaxRepeating     int
	AllowSequential  bool
	MaxSequential    int
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
		AllowRepeating:   true,
		MaxRepeating:     2,
		AllowSequential:  true,
		MaxSequential:    3,
	}
}

// ValidatePassword validates password against policy
func (sv *SecurityValidator) ValidatePassword(password string, policy PasswordPolicy) error {
	if password == "" {
		return fmt.Errorf("password is required")
	}
	
	// Length validation
	if len(password) < policy.MinLength {
		return fmt.Errorf("password must be at least %d characters", policy.MinLength)
	}
	
	if policy.MaxLength > 0 && len(password) > policy.MaxLength {
		return fmt.Errorf("password must be at most %d characters", policy.MaxLength)
	}
	
	// Character type requirements
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
	
	// Common password check
	if !policy.AllowCommon {
		lowercasePassword := strings.ToLower(password)
		if sv.weakPasswords[lowercasePassword] {
			return fmt.Errorf("password is too common")
		}
	}
	
	// Space check
	if !policy.AllowSpaces && strings.Contains(password, " ") {
		return fmt.Errorf("password cannot contain spaces")
	}
	
	// Repeating characters check
	if !policy.AllowRepeating {
		if sv.hasRepeatingChars(password, policy.MaxRepeating) {
			return fmt.Errorf("password cannot contain more than %d repeating characters", policy.MaxRepeating)
		}
	}
	
	// Sequential characters check
	if !policy.AllowSequential {
		if sv.hasSequentialChars(password, policy.MaxSequential) {
			return fmt.Errorf("password cannot contain more than %d sequential characters", policy.MaxSequential)
		}
	}
	
	return nil
}

// GetPasswordStrength calculates password strength
func (sv *SecurityValidator) GetPasswordStrength(password string) PasswordStrength {
	if password == "" {
		return PasswordWeak
	}
	
	score := 0
	
	// Length score
	length := len(password)
	if length >= 8 {
		score += 1
	}
	if length >= 12 {
		score += 1
	}
	if length >= 16 {
		score += 1
	}
	
	// Character variety score
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
		score += 1
	}
	if hasLower {
		score += 1
	}
	if hasNumber {
		score += 1
	}
	if hasSymbol {
		score += 1
	}
	
	// Complexity score
	if sv.hasMixedChars(password) {
		score += 1
	}
	
	if !sv.hasRepeatingChars(password, 2) {
		score += 1
	}
	
	if !sv.hasSequentialChars(password, 3) {
		score += 1
	}
	
	// Common password penalty
	lowercasePassword := strings.ToLower(password)
	if sv.weakPasswords[lowercasePassword] {
		score -= 2
	}
	
	// Determine strength
	switch {
	case score >= 10:
		return PasswordVeryStrong
	case score >= 7:
		return PasswordStrong
	case score >= 4:
		return PasswordGood
	case score >= 2:
		return PasswordFair
	default:
		return PasswordWeak
	}
}

// ValidateHash validates hash format
func (sv *SecurityValidator) ValidateHash(hash, algorithm string) error {
	if hash == "" {
		return fmt.Errorf("hash is required")
	}
	
	switch strings.ToLower(algorithm) {
	case "md5":
		return sv.validateMD5(hash)
	case "sha1":
		return sv.validateSHA1(hash)
	case "sha256":
		return sv.validateSHA256(hash)
	default:
		return fmt.Errorf("unsupported hash algorithm: %s", algorithm)
	}
}

// ValidateAPIKey validates API key format
func (sv *SecurityValidator) ValidateAPIKey(apiKey string) error {
	if apiKey == "" {
		return fmt.Errorf("API key is required")
	}
	
	// Basic API key validation
	if len(apiKey) < 16 {
		return fmt.Errorf("API key must be at least 16 characters")
	}
	
	if len(apiKey) > 256 {
		return fmt.Errorf("API key must be at most 256 characters")
	}
	
	// Check for valid characters (alphanumeric and some symbols)
	for _, char := range apiKey {
		if !unicode.IsLetter(char) && !unicode.IsNumber(char) && char != '_' && char != '-' {
			return fmt.Errorf("API key contains invalid character: %c", char)
		}
	}
	
	return nil
}

// ValidateSecretKey validates secret key format
func (sv *SecurityValidator) ValidateSecretKey(secretKey string) error {
	if secretKey == "" {
		return fmt.Errorf("secret key is required")
	}
	
	// Secret keys should be at least 32 characters
	if len(secretKey) < 32 {
		return fmt.Errorf("secret key must be at least 32 characters")
	}
	
	// Check for sufficient entropy
	if !sv.hasSufficientEntropy(secretKey) {
		return fmt.Errorf("secret key has insufficient entropy")
	}
	
	return nil
}

// ValidateToken validates token format
func (sv *SecurityValidator) ValidateToken(token string) error {
	if token == "" {
		return fmt.Errorf("token is required")
	}
	
	// Basic token validation
	if len(token) < 10 {
		return fmt.Errorf("token must be at least 10 characters")
	}
	
	// Check for valid characters (alphanumeric and some symbols)
	for _, char := range token {
		if !unicode.IsLetter(char) && !unicode.IsNumber(char) && char != '_' && char != '-' {
			return fmt.Errorf("token contains invalid character: %c", char)
		}
	}
	
	return nil
}

// ValidateJWT validates JWT format
func (sv *SecurityValidator) ValidateJWT(jwt string) error {
	if jwt == "" {
		return fmt.Errorf("JWT is required")
	}
	
	// JWT should have 3 parts separated by dots
	parts := strings.Split(jwt, ".")
	if len(parts) != 3 {
		return fmt.Errorf("JWT must have 3 parts separated by dots")
	}
	
	// Each part should be base64 encoded
	for i, part := range parts {
		if len(part) == 0 {
			return fmt.Errorf("JWT part %d is empty", i+1)
		}
		
		// Basic base64 validation (simplified)
		if !sv.isBase64(part) {
			return fmt.Errorf("JWT part %d is not valid base64", i+1)
		}
	}
	
	return nil
}

// ValidateSessionID validates session ID format
func (sv *SecurityValidator) ValidateSessionID(sessionID string) error {
	if sessionID == "" {
		return fmt.Errorf("session ID is required")
	}
	
	// Session ID should be at least 16 characters
	if len(sessionID) < 16 {
		return fmt.Errorf("session ID must be at least 16 characters")
	}
	
	// Check for valid characters
	for _, char := range sessionID {
		if !unicode.IsLetter(char) && !unicode.IsNumber(char) {
			return fmt.Errorf("session ID contains invalid character: %c", char)
		}
	}
	
	return nil
}

// ValidateCSRFToken validates CSRF token format
func (sv *SecurityValidator) ValidateCSRFToken(token string) error {
	if token == "" {
		return fmt.Errorf("CSRF token is required")
	}
	
	// CSRF token should be at least 20 characters
	if len(token) < 20 {
		return fmt.Errorf("CSRF token must be at least 20 characters")
	}
	
	// Check for valid characters
	for _, char := range token {
		if !unicode.IsLetter(char) && !unicode.IsNumber(char) {
			return fmt.Errorf("CSRF token contains invalid character: %c", char)
		}
	}
	
	return nil
}

// ValidateNonce validates nonce format
func (sv *SecurityValidator) ValidateNonce(nonce string) error {
	if nonce == "" {
		return fmt.Errorf("nonce is required")
	}
	
	// Nonce should be at least 8 characters
	if len(nonce) < 8 {
		return fmt.Errorf("nonce must be at least 8 characters")
	}
	
	// Check for valid characters
	for _, char := range nonce {
		if !unicode.IsLetter(char) && !unicode.IsNumber(char) {
			return fmt.Errorf("nonce contains invalid character: %c", char)
		}
	}
	
	return nil
}

// ValidateSalt validates salt format
func (sv *SecurityValidator) ValidateSalt(salt string) error {
	if salt == "" {
		return fmt.Errorf("salt is required")
	}
	
	// Salt should be at least 16 characters
	if len(salt) < 16 {
		return fmt.Errorf("salt must be at least 16 characters")
	}
	
	// Check for valid characters
	for _, char := range salt {
		if !unicode.IsLetter(char) && !unicode.IsNumber(char) {
			return fmt.Errorf("salt contains invalid character: %c", char)
		}
	}
	
	return nil
}

// ValidateIV validates initialization vector format
func (sv *SecurityValidator) ValidateIV(iv string) error {
	if iv == "" {
		return fmt.Errorf("IV is required")
	}
	
	// IV should be at least 16 characters (128 bits)
	if len(iv) < 16 {
		return fmt.Errorf("IV must be at least 16 characters")
	}
	
	// Check for valid characters (hex)
	for _, char := range iv {
		if !((char >= '0' && char <= '9') || (char >= 'A' && char <= 'F') || (char >= 'a' && char <= 'f')) {
			return fmt.Errorf("IV contains invalid character: %c", char)
		}
	}
	
	return nil
}

// ValidateCertificate validates certificate format
func (sv *SecurityValidator) ValidateCertificate(cert string) error {
	if cert == "" {
		return fmt.Errorf("certificate is required")
	}
	
	// Basic certificate validation
	if !strings.Contains(cert, "BEGIN CERTIFICATE") || !strings.Contains(cert, "END CERTIFICATE") {
		return fmt.Errorf("certificate must be in PEM format")
	}
	
	return nil
}

// ValidatePrivateKey validates private key format
func (sv *SecurityValidator) ValidatePrivateKey(key string) error {
	if key == "" {
		return fmt.Errorf("private key is required")
	}
	
	// Basic private key validation
	if !strings.Contains(key, "BEGIN PRIVATE KEY") && !strings.Contains(key, "BEGIN RSA PRIVATE KEY") {
		return fmt.Errorf("private key must be in PEM format")
	}
	
	return nil
}

// ValidatePublicKey validates public key format
func (sv *SecurityValidator) ValidatePublicKey(key string) error {
	if key == "" {
		return fmt.Errorf("public key is required")
	}
	
	// Basic public key validation
	if !strings.Contains(key, "BEGIN PUBLIC KEY") && !strings.Contains(key, "BEGIN RSA PUBLIC KEY") {
		return fmt.Errorf("public key must be in PEM format")
	}
	
	return nil
}

// ValidateFingerprint validates fingerprint format
func (sv *SecurityValidator) ValidateFingerprint(fingerprint string) error {
	if fingerprint == "" {
		return fmt.Errorf("fingerprint is required")
	}
	
	// Remove colons and spaces
	cleaned := strings.ReplaceAll(strings.ReplaceAll(fingerprint, ":", ""), " ", "")
	
	// Check if it's valid hexadecimal
	if !sv.isHexadecimal(cleaned) {
		return fmt.Errorf("fingerprint must contain only hexadecimal characters")
	}
	
	// Check length (should be 32 or 40 characters for SHA256 or SHA1)
	if len(cleaned) != 32 && len(cleaned) != 40 {
		return fmt.Errorf("fingerprint must be 32 or 40 characters")
	}
	
	return nil
}

// ValidateSignature validates signature format
func (sv *SecurityValidator) ValidateSignature(signature string) error {
	if signature == "" {
		return fmt.Errorf("signature is required")
	}
	
	// Remove whitespace
	cleaned := strings.ReplaceAll(signature, " ", "")
	
	// Check if it's valid hexadecimal
	if !sv.isHexadecimal(cleaned) {
		return fmt.Errorf("signature must contain only hexadecimal characters")
	}
	
	// Check minimum length
	if len(cleaned) < 128 { // At least 512 bits
		return fmt.Errorf("signature must be at least 128 characters")
	}
	
	return nil
}

// ValidateEncryptionKey validates encryption key format
func (sv *SecurityValidator) ValidateEncryptionKey(key string, algorithm string) error {
	if key == "" {
		return fmt.Errorf("encryption key is required")
	}
	
	switch strings.ToLower(algorithm) {
	case "aes128":
		if len(key) != 16 {
			return fmt.Errorf("AES-128 key must be 16 characters")
		}
	case "aes192":
		if len(key) != 24 {
			return fmt.Errorf("AES-192 key must be 24 characters")
		}
	case "aes256":
		if len(key) != 32 {
			return fmt.Errorf("AES-256 key must be 32 characters")
		}
	default:
		return fmt.Errorf("unsupported encryption algorithm: %s", algorithm)
	}
	
	return nil
}

// ValidateHMAC validates HMAC format
func (sv *SecurityValidator) ValidateHMAC(hmac string) error {
	if hmac == "" {
		return fmt.Errorf("HMAC is required")
	}
	
	// Remove whitespace
	cleaned := strings.ReplaceAll(hmac, " ", "")
	
	// Check if it's valid hexadecimal
	if !sv.isHexadecimal(cleaned) {
		return fmt.Errorf("HMAC must contain only hexadecimal characters")
	}
	
	// Check minimum length
	if len(cleaned) < 32 { // At least 128 bits
		return fmt.Errorf("HMAC must be at least 32 characters")
	}
	
	return nil
}

// ValidateDigest validates digest format
func (sv *SecurityValidator) ValidateDigest(digest string) error {
	if digest == "" {
		return fmt.Errorf("digest is required")
	}
	
	// Remove whitespace
	cleaned := strings.ReplaceAll(digest, " ", "")
	
	// Check if it's valid hexadecimal
	if !sv.isHexadecimal(cleaned) {
		return fmt.Errorf("digest must contain only hexadecimal characters")
	}
	
	// Check length (should be 32, 40, or 64 characters for SHA256, SHA1, or SHA512)
	if len(cleaned) != 32 && len(cleaned) != 40 && len(cleaned) != 64 {
		return fmt.Errorf("digest must be 32, 40, or 64 characters")
	}
	
	return nil
}

// ValidatePermission validates permission format
func (sv *SecurityValidator) ValidatePermission(permission string) error {
	if permission == "" {
		return fmt.Errorf("permission is required")
	}
	
	// Basic permission validation (e.g., "read", "write", "admin")
	validPermissions := []string{
		"read", "write", "execute", "delete", "create", "update", "admin", "owner",
		"user", "guest", "moderator", "editor", "viewer", "contributor",
	}
	
	for _, validPerm := range validPermissions {
		if strings.EqualFold(permission, validPerm) {
			return nil
		}
	}
	
	return fmt.Errorf("invalid permission: %s", permission)
}

// ValidateRole validates role format
func (sv *SecurityValidator) ValidateRole(role string) error {
	if role == "" {
		return fmt.Errorf("role is required")
	}
	
	// Basic role validation
	validRoles := []string{
		"admin", "administrator", "user", "guest", "moderator", "editor",
		"viewer", "contributor", "developer", "manager", "operator",
		"superadmin", "root", "system", "service", "bot",
	}
	
	for _, validRole := range validRoles {
		if strings.EqualFold(role, validRole) {
			return nil
		}
	}
	
	return fmt.Errorf("invalid role: %s", role)
}

// ValidateScope validates scope format
func (sv *SecurityValidator) ValidateScope(scope string) error {
	if scope == "" {
		return fmt.Errorf("scope is required")
	}
	
	// Basic scope validation (e.g., "read:users", "write:posts", "admin:all")
	parts := strings.Split(scope, ":")
	if len(parts) != 2 {
		return fmt.Errorf("scope must be in format 'action:resource'")
	}
	
	action := parts[0]
	resource := parts[1]
	
	validActions := []string{"read", "write", "create", "update", "delete", "admin", "all"}
	validResources := []string{"users", "posts", "comments", "files", "settings", "all"}
	
	actionValid := false
	for _, validAction := range validActions {
		if strings.EqualFold(action, validAction) {
			actionValid = true
			break
		}
	}
	
	if !actionValid {
		return fmt.Errorf("invalid scope action: %s", action)
	}
	
	resourceValid := false
	for _, validResource := range validResources {
		if strings.EqualFold(resource, validResource) {
			resourceValid = true
			break
		}
	}
	
	if !resourceValid {
		return fmt.Errorf("invalid scope resource: %s", resource)
	}
	
	return nil
}

// ValidatePrivilege validates privilege format
func (sv *SecurityValidator) ValidatePrivilege(privilege string) error {
	if privilege == "" {
		return fmt.Errorf("privilege is required")
	}
	
	// Basic privilege validation
	validPrivileges := []string{
		"create", "read", "update", "delete", "execute", "admin", "owner", "grant", "revoke",
	}
	
	for _, validPriv := range validPrivileges {
		if strings.EqualFold(privilege, validPriv) {
			return nil
		}
	}
	
	return fmt.Errorf("invalid privilege: %s", privilege)
}

// ValidateAccessLevel validates access level format
func (sv *SecurityValidator) ValidateAccessLevel(level string) error {
	if level == "" {
		return fmt.Errorf("access level is required")
	}
	
	// Basic access level validation
	validLevels := []string{
		"public", "private", "confidential", "secret", "top-secret", "restricted",
		"internal", "external", "guest", "user", "admin", "system",
	}
	
	for _, validLevel := range validLevels {
		if strings.EqualFold(level, validLevel) {
			return nil
		}
	}
	
	return fmt.Errorf("invalid access level: %s", level)
}

// ValidateSecurityLevel validates security level format
func (sv *SecurityValidator) ValidateSecurityLevel(level string) error {
	if level == "" {
		return fmt.Errorf("security level is required")
	}
	
	// Basic security level validation
	validLevels := []string{
		"low", "medium", "high", "critical", "urgent", "normal", "elevated", "maximum",
	}
	
	for _, validLevel := range validLevels {
		if strings.EqualFold(level, validLevel) {
			return nil
		}
	}
	
	return fmt.Errorf("invalid security level: %s", level)
}

// Helper functions

func (sv *SecurityValidator) hasRepeatingChars(password string, maxRepeating int) bool {
	if len(password) < maxRepeating {
		return false
	}
	
	for i := 0; i <= len(password)-maxRepeating; i++ {
		char := password[i]
		repeating := true
		
		for j := 1; j < maxRepeating; j++ {
			if password[i+j] != char {
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
	if len(password) < maxSequential {
		return false
	}
	
	for i := 0; i <= len(password)-maxSequential; i++ {
		sequential := true
		
		for j := 1; j < maxSequential; j++ {
			if password[i+j] != password[i]+rune(j) {
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

func (sv *SecurityValidator) hasMixedChars(password string) bool {
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
	
	return (hasUpper && hasLower) || (hasUpper && hasNumber) || (hasLower && hasNumber) || hasSymbol
}

func (sv *SecurityValidator) hasSufficientEntropy(key string) bool {
	if len(key) < 32 {
		return false
	}
	
	charTypes := 0
	hasUpper := false
	hasLower := false
	hasNumber := false
	hasSymbol := false
	
	for _, char := range key {
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
		charTypes++
	}
	if hasLower {
		charTypes++
	}
	if hasNumber {
		charTypes++
	}
	if hasSymbol {
		charTypes++
	}
	
	return charTypes >= 3
}

func (sv *SecurityValidator) isBase64(s string) bool {
	// Basic base64 validation
	for _, char := range s {
		if !((char >= 'A' && char <= 'Z') || (char >= 'a' && char <= 'z') || (char >= '0' && char <= '9') || char == '+' || char == '/' || char == '=') {
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
	return true
}

func (sv *SecurityValidator) validateMD5(hash string) error {
	if len(hash) != 32 {
		return fmt.Errorf("MD5 hash must be 32 characters")
	}
	
	if !sv.isHexadecimal(hash) {
		return fmt.Errorf("MD5 hash must contain only hexadecimal characters")
	}
	
	return nil
}

func (sv *SecurityValidator) validateSHA1(hash string) error {
	if len(hash) != 40 {
		return fmt.Errorf("SHA1 hash must be 40 characters")
	}
	
	if !sv.isHexadecimal(hash) {
		return fmt.Errorf("SHA1 hash must contain only hexadecimal characters")
	}
	
	return nil
}

func (sv *SecurityValidator) validateSHA256(hash string) error {
	if len(hash) != 64 {
		return fmt.Errorf("SHA256 hash must be 64 characters")
	}
	
	if !sv.isHexadecimal(hash) {
		return fmt.Errorf("SHA256 hash must contain only hexadecimal characters")
	}
	
	return nil
}

// GenerateHash generates a hash for testing purposes
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

// CheckPasswordStrength checks password strength with detailed feedback
func (sv *SecurityValidator) CheckPasswordStrength(password string) (PasswordStrength, []string) {
	var feedback []string
	
	if len(password) < 8 {
		feedback = append(feedback, "Password should be at least 8 characters")
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
	
	if !hasUpper {
		feedback = append(feedback, "Password should contain at least one uppercase letter")
	}
	
	if !hasLower {
		feedback = append(feedback, "Password should contain at least one lowercase letter")
	}
	
	if !hasNumber {
		feedback = append(feedback, "Password should contain at least one number")
	}
	
	if !hasSymbol {
		feedback = append(feedback, "Password should contain at least one symbol")
	}
	
	if sv.hasRepeatingChars(password, 3) {
		feedback = append(feedback, "Password should not contain repeating characters")
	}
	
	if sv.hasSequentialChars(password, 3) {
		feedback = append(feedback, "Password should not contain sequential characters")
	}
	
	lowercasePassword := strings.ToLower(password)
	if sv.weakPasswords[lowercasePassword] {
		feedback = append(feedback, "Password is too common")
	}
	
	strength := sv.GetPasswordStrength(password)
	
	return strength, feedback
}

// ValidateComplexPassword validates password with comprehensive checks
func (sv *SecurityValidator) ValidateComplexPassword(password string) error {
	if password == "" {
		return fmt.Errorf("password is required")
	}
	
	// Length check
	if len(password) < 12 {
		return fmt.Errorf("password must be at least 12 characters")
	}
	
	// Character variety check
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
	
	if !hasUpper || !hasLower || !hasNumber || !hasSymbol {
		return fmt.Errorf("password must contain uppercase, lowercase, numbers, and symbols")
	}
	
	// Common password check
	lowercasePassword := strings.ToLower(password)
	if sv.weakPasswords[lowercasePassword] {
		return fmt.Errorf("password is too common")
	}
	
	// Repeating characters check
	if sv.hasRepeatingChars(password, 2) {
		return fmt.Errorf("password cannot contain repeating characters")
	}
	
	// Sequential characters check
	if sv.hasSequentialChars(password, 3) {
		return fmt.Errorf("password cannot contain sequential characters")
	}
	
	// Entropy check
	if !sv.hasSufficientEntropy(password) {
		return fmt.Errorf("password has insufficient entropy")
	}
	
	return nil
}
