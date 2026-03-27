package main

import (
	"fmt"
	"log"
	"time"
)

func main() {
	fmt.Println("=== Security Examples Demo ===")
	
	// Test authentication
	testAuthentication()
	
	// Test authorization
	testAuthorization()
	
	// Test encryption
	testEncryption()
	
	// Test hashing
	testHashing()
	
	// Test JWT
	testJWT()
	
	// Test rate limiting
	testRateLimiting()
	
	// Test input validation
	testInputValidation()
	
	// Test CORS
	testCORS()
	
	// Test security middleware
	testSecurityMiddleware()
}

func testAuthentication() {
	fmt.Println("\n=== Authentication Demo ===")
	
	auth := NewAuthService()
	
	// Register user
	user, err := auth.Register("john@example.com", "password123")
	if err != nil {
		log.Printf("Registration failed: %v", err)
		return
	}
	
	fmt.Printf("User registered: %+v\n", user)
	
	// Login
	token, err := auth.Login("john@example.com", "password123")
	if err != nil {
		log.Printf("Login failed: %v", err)
		return
	}
	
	fmt.Printf("Login successful, token: %s\n", token)
	
	// Validate token
	validatedUser, err := auth.ValidateToken(token)
	if err != nil {
		log.Printf("Token validation failed: %v", err)
		return
	}
	
	fmt.Printf("Token validated for user: %+v\n", validatedUser)
}

func testAuthorization() {
	fmt.Println("\n=== Authorization Demo ===")
	
	authz := NewAuthorizationService()
	
	// Define permissions
	authz.AddPermission("read:users")
	authz.AddPermission("write:users")
	authz.AddPermission("delete:users")
	
	// Create roles
	authz.CreateRole("admin")
	authz.CreateRole("user")
	
	// Assign permissions to roles
	authz.AssignPermissionToRole("admin", "read:users")
	authz.AssignPermissionToRole("admin", "write:users")
	authz.AssignPermissionToRole("admin", "delete:users")
	authz.AssignPermissionToRole("user", "read:users")
	
	// Create user and assign role
	user := &User{ID: "1", Email: "admin@example.com"}
	authz.AssignRoleToUser(user.ID, "admin")
	
	// Check permissions
	canRead := authz.UserHasPermission(user.ID, "read:users")
	canDelete := authz.UserHasPermission(user.ID, "delete:users")
	
	fmt.Printf("User can read users: %t\n", canRead)
	fmt.Printf("User can delete users: %t\n", canDelete)
}

func testEncryption() {
	fmt.Println("\n=== Encryption Demo ===")
	
	encryptor := NewAESEncryptor("my-secret-key-32-characters-long!!")
	
	original := "This is a secret message"
	
	// Encrypt
	encrypted, err := encryptor.Encrypt(original)
	if err != nil {
		log.Printf("Encryption failed: %v", err)
		return
	}
	
	fmt.Printf("Original: %s\n", original)
	fmt.Printf("Encrypted: %s\n", encrypted)
	
	// Decrypt
	decrypted, err := encryptor.Decrypt(encrypted)
	if err != nil {
		log.Printf("Decryption failed: %v", err)
		return
	}
	
	fmt.Printf("Decrypted: %s\n", decrypted)
	fmt.Printf("Match: %t\n", original == decrypted)
}

func testHashing() {
	fmt.Println("\n=== Hashing Demo ===")
	
	hasher := NewBcryptHasher()
	
	password := "mypassword123"
	
	// Hash password
	hashed, err := hasher.Hash(password)
	if err != nil {
		log.Printf("Hashing failed: %v", err)
		return
	}
	
	fmt.Printf("Original password: %s\n", password)
	fmt.Printf("Hashed password: %s\n", hashed)
	
	// Verify password
	valid := hasher.Verify(password, hashed)
	fmt.Printf("Password verification: %t\n", valid)
	
	// Test with wrong password
	invalid := hasher.Verify("wrongpassword", hashed)
	fmt.Printf("Wrong password verification: %t\n", invalid)
}

func testJWT() {
	fmt.Println("\n=== JWT Demo ===")
	
	jwtService := NewJWTService("my-secret-key")
	
	// Create claims
	claims := &JWTClaims{
		UserID: "123",
		Email:  "user@example.com",
		Roles:  []string{"user", "admin"},
	}
	
	// Generate token
	token, err := jwtService.GenerateToken(claims)
	if err != nil {
		log.Printf("Token generation failed: %v", err)
		return
	}
	
	fmt.Printf("Generated token: %s\n", token)
	
	// Validate token
	validatedClaims, err := jwtService.ValidateToken(token)
	if err != nil {
		log.Printf("Token validation failed: %v", err)
		return
	}
	
	fmt.Printf("Validated claims: %+v\n", validatedClaims)
	
	// Test expired token
	expiredClaims := &JWTClaims{
		UserID: "123",
		Email:  "user@example.com",
		ExpiresAt: time.Now().Add(-time.Hour).Unix(),
	}
	
	expiredToken, err := jwtService.GenerateToken(expiredClaims)
	if err != nil {
		log.Printf("Expired token generation failed: %v", err)
		return
	}
	
	_, err = jwtService.ValidateToken(expiredToken)
	fmt.Printf("Expired token validation failed (expected): %v\n", err)
}

func testRateLimiting() {
	fmt.Println("\n=== Rate Limiting Demo ===")
	
	limiter := NewTokenBucketLimiter(10, 1) // 10 tokens, refill 1 per second
	
	clientID := "client123"
	
	// Test rate limiting
	for i := 0; i < 15; i++ {
		allowed := limiter.Allow(clientID)
		fmt.Printf("Request %d: %t\n", i+1, allowed)
		
		if !allowed {
			fmt.Printf("Rate limit exceeded at request %d\n", i+1)
			break
		}
	}
	
	// Wait for refill
	fmt.Println("Waiting 2 seconds for token refill...")
	time.Sleep(2 * time.Second)
	
	// Should allow more requests
	allowed := limiter.Allow(clientID)
	fmt.Printf("Request after wait: %t\n", allowed)
}

func testInputValidation() {
	fmt.Println("\n=== Input Validation Demo ===")
	
	validator := NewInputValidator()
	
	// Valid email
	email := "user@example.com"
	valid := validator.IsValidEmail(email)
	fmt.Printf("Email %s is valid: %t\n", email, valid)
	
	// Invalid email
	invalidEmail := "invalid-email"
	valid = validator.IsValidEmail(invalidEmail)
	fmt.Printf("Email %s is valid: %t\n", invalidEmail, valid)
	
	// Valid phone
	phone := "+1234567890"
	valid = validator.IsValidPhone(phone)
	fmt.Printf("Phone %s is valid: %t\n", phone, valid)
	
	// SQL injection test
	input := "'; DROP TABLE users; --"
	sanitized := validator.SanitizeSQL(input)
	fmt.Printf("Original: %s\n", input)
	fmt.Printf("Sanitized: %s\n", sanitized)
	
	// XSS test
	xssInput := "<script>alert('xss')</script>"
	sanitizedHTML := validator.SanitizeHTML(xssInput)
	fmt.Printf("Original HTML: %s\n", xssInput)
	fmt.Printf("Sanitized HTML: %s\n", sanitizedHTML)
}

func testCORS() {
	fmt.Println("\n=== CORS Demo ===")
	
	cors := NewCORSHandler()
	
	// Test preflight request
	headers := map[string][]string{
		"Origin":                         {"https://example.com"},
		"Access-Control-Request-Method":  {"POST"},
		"Access-Control-Request-Headers": {"Content-Type"},
	}
	
	allowed, responseHeaders := cors.HandlePreflight(headers)
	fmt.Printf("Preflight allowed: %t\n", allowed)
	fmt.Printf("Response headers: %+v\n", responseHeaders)
	
	// Test actual request
	requestHeaders := map[string][]string{
		"Origin": {"https://example.com"},
	}
	
	responseHeaders = cors.HandleRequest(requestHeaders)
	fmt.Printf("CORS headers for actual request: %+v\n", responseHeaders)
}

func testSecurityMiddleware() {
	fmt.Println("\n=== Security Middleware Demo ===")
	
	security := NewSecurityMiddleware()
	
	// Test request validation
	request := &SecurityRequest{
		Method: "POST",
		Path:   "/api/users",
		Headers: map[string][]string{
			"Content-Type": {"application/json"},
			"X-API-Key":    {"valid-api-key"},
		},
		Body: `{"name": "John", "email": "john@example.com"}`,
	}
	
	valid, violations := security.ValidateRequest(request)
	fmt.Printf("Request valid: %t\n", valid)
	if !valid {
		fmt.Printf("Violations: %+v\n", violations)
	}
	
	// Test security headers
	headers := security.GetSecurityHeaders()
	fmt.Printf("Security headers: %+v\n", headers)
}
