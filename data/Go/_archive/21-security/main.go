package main

import (
	"crypto/aes"
	"crypto/cipher"
	"crypto/rand"
	"crypto/sha256"
	"encoding/base64"
	"encoding/hex"
	"errors"
	"fmt"
	"io"
	"log"
	"regexp"
	"strconv"
	"strings"
	"sync"
	"time"
)

// --- Auth & Passwords ---

type User struct {
	ID        string    `json:"id"`
	Email     string    `json:"email"`
	Password  string    `json:"-"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

type Session struct {
	ID        string    `json:"id"`
	UserID    string    `json:"user_id"`
	Token     string    `json:"token"`
	ExpiresAt time.Time `json:"expires_at"`
	CreatedAt time.Time `json:"created_at"`
}

type PasswordHasher interface {
	Hash(password string) (string, error)
	Verify(password, hash string) bool
}

type BcryptHasher struct {
	cost int
}

func NewBcryptHasher() *BcryptHasher {
	return &BcryptHasher{cost: 12}
}

func (b *BcryptHasher) Hash(password string) (string, error) {
	salt := generateSalt()
	hashed := sha256Hex(password + salt)
	return fmt.Sprintf("%s$%s", salt, hashed), nil
}

func (b *BcryptHasher) Verify(password, hash string) bool {
	parts := strings.Split(hash, "$")
	if len(parts) != 2 {
		return false
	}
	return sha256Hex(password+parts[0]) == parts[1]
}

type JWTClaims struct {
	UserID    string   `json:"user_id"`
	Email     string   `json:"email"`
	Roles     []string `json:"roles,omitempty"`
	ExpiresAt int64    `json:"exp,omitempty"`
	IssuedAt  int64    `json:"iat,omitempty"`
}

type JWTService struct {
	secretKey string
}

func NewJWTService(secretKey string) *JWTService {
	return &JWTService{secretKey: secretKey}
}

func (j *JWTService) GenerateToken(claims *JWTClaims) (string, error) {
	if claims.ExpiresAt == 0 {
		claims.ExpiresAt = time.Now().Add(24 * time.Hour).Unix()
	}
	claims.IssuedAt = time.Now().Unix()

	headerEnc := base64.RawURLEncoding.EncodeToString([]byte("HS256"))
	rawPayload := fmt.Sprintf("user_id=%s;email=%s;exp=%d;iat=%d",
		claims.UserID, claims.Email, claims.ExpiresAt, claims.IssuedAt)
	payloadEnc := base64.RawURLEncoding.EncodeToString([]byte(rawPayload))

	signature := sha256Hex(fmt.Sprintf("%s.%s.%s", headerEnc, payloadEnc, j.secretKey))
	return fmt.Sprintf("%s.%s.%s", headerEnc, payloadEnc, signature), nil
}

func (j *JWTService) ValidateToken(token string) (*JWTClaims, error) {
	parts := strings.Split(token, ".")
	if len(parts) != 3 {
		return nil, errors.New("invalid token format")
	}

	headerEnc, payloadEnc, signature := parts[0], parts[1], parts[2]
	expectedSignature := sha256Hex(fmt.Sprintf("%s.%s.%s", headerEnc, payloadEnc, j.secretKey))

	if signature != expectedSignature {
		return nil, errors.New("invalid token signature")
	}

	payloadBytes, err := base64.RawURLEncoding.DecodeString(payloadEnc)
	if err != nil {
		return nil, errors.New("invalid payload encoding")
	}

	claims := &JWTClaims{}
	for _, kvPair := range strings.Split(string(payloadBytes), ";") {
		kv := strings.Split(kvPair, "=")
		if len(kv) == 2 {
			switch kv[0] {
			case "user_id":
				claims.UserID = kv[1]
			case "email":
				claims.Email = kv[1]
			case "exp":
				claims.ExpiresAt, _ = strconv.ParseInt(kv[1], 10, 64)
			case "iat":
				claims.IssuedAt, _ = strconv.ParseInt(kv[1], 10, 64)
			}
		}
	}

	if claims.ExpiresAt > 0 && time.Now().Unix() > claims.ExpiresAt {
		return nil, errors.New("token expired")
	}

	return claims, nil
}

type AuthService struct {
	users    map[string]*User
	sessions map[string]*Session
	hasher   PasswordHasher
	jwt      *JWTService
}

func NewAuthService() *AuthService {
	return &AuthService{
		users:    make(map[string]*User),
		sessions: make(map[string]*Session),
		hasher:   NewBcryptHasher(),
		jwt:      NewJWTService("my-secret-key"),
	}
}

func (a *AuthService) Register(email, password string) (*User, error) {
	if _, exists := a.users[email]; exists {
		return nil, errors.New("user already exists")
	}

	if !strings.Contains(email, "@") || !strings.Contains(email, ".") {
		return nil, errors.New("invalid email format")
	}

	if len(password) < 8 {
		return nil, errors.New("password must be at least 8 characters")
	}

	hashedPassword, err := a.hasher.Hash(password)
	if err != nil {
		return nil, fmt.Errorf("failed to hash password: %w", err)
	}

	user := &User{
		ID:        generateID(),
		Email:     email,
		Password:  hashedPassword,
		CreatedAt: time.Now(),
		UpdatedAt: time.Now(),
	}

	a.users[email] = user
	return user, nil
}

func (a *AuthService) Login(email, password string) (string, error) {
	user, exists := a.users[email]
	if !exists {
		return "", errors.New("user not found")
	}

	if !a.hasher.Verify(password, user.Password) {
		return "", errors.New("invalid password")
	}

	claims := &JWTClaims{
		UserID: user.ID,
		Email:  user.Email,
	}

	token, err := a.jwt.GenerateToken(claims)
	if err != nil {
		return "", fmt.Errorf("failed to generate token: %w", err)
	}

	session := &Session{
		ID:        generateID(),
		UserID:    user.ID,
		Token:     token,
		ExpiresAt: time.Now().Add(24 * time.Hour),
		CreatedAt: time.Now(),
	}

	a.sessions[token] = session
	return token, nil
}

func (a *AuthService) ValidateToken(token string) (*User, error) {
	session, exists := a.sessions[token]
	if !exists {
		return nil, errors.New("session not found")
	}

	if time.Now().After(session.ExpiresAt) {
		delete(a.sessions, token)
		return nil, errors.New("session expired")
	}

	claims, err := a.jwt.ValidateToken(token)
	if err != nil {
		delete(a.sessions, token)
		return nil, fmt.Errorf("invalid token: %w", err)
	}

	user, exists := a.users[claims.Email]
	if !exists {
		return nil, errors.New("user not found")
	}

	return user, nil
}

// --- Authorization (RBAC) ---

type Role struct {
	ID          string
	Name        string
	Description string
	CreatedAt   int64
}

type Permission struct {
	ID          string
	Name        string
	Resource    string
	Action      string
	Description string
}

type AuthorizationService struct {
	roles       map[string]*Role
	permissions map[string]*Permission
	userRoles   map[string][]string
	rolePerms   map[string][]string
	mu          sync.RWMutex
}

func NewAuthorizationService() *AuthorizationService {
	return &AuthorizationService{
		roles:       make(map[string]*Role),
		permissions: make(map[string]*Permission),
		userRoles:   make(map[string][]string),
		rolePerms:   make(map[string][]string),
	}
}

func (a *AuthorizationService) CreateRole(name, description string) error {
	a.mu.Lock()
	defer a.mu.Unlock()
	role := &Role{ID: name, Name: name, Description: description, CreatedAt: time.Now().Unix()}
	a.roles[name] = role
	return nil
}

func (a *AuthorizationService) AddPermission(name, resource, action, description string) error {
	a.mu.Lock()
	defer a.mu.Unlock()
	perm := &Permission{ID: name, Name: name, Resource: resource, Action: action, Description: description}
	a.permissions[name] = perm
	return nil
}

func (a *AuthorizationService) AssignPermissionToRole(roleID, permissionID string) error {
	a.mu.Lock()
	defer a.mu.Unlock()
	a.rolePerms[roleID] = append(a.rolePerms[roleID], permissionID)
	return nil
}

func (a *AuthorizationService) AssignRoleToUser(userID, roleID string) error {
	a.mu.Lock()
	defer a.mu.Unlock()
	a.userRoles[userID] = append(a.userRoles[userID], roleID)
	return nil
}

func (a *AuthorizationService) UserHasPermission(userID, permissionName string) bool {
	a.mu.RLock()
	defer a.mu.RUnlock()
	for _, rID := range a.userRoles[userID] {
		for _, pID := range a.rolePerms[rID] {
			if pID == permissionName {
				return true
			}
		}
	}
	return false
}

// --- Encryption ---

type AESEncryptor struct {
	key []byte
}

func NewAESEncryptor(keyString string) *AESEncryptor {
	hash := sha256.Sum256([]byte(keyString))
	return &AESEncryptor{key: hash[:]}
}

func (a *AESEncryptor) Encrypt(plaintext string) (string, error) {
	block, err := aes.NewCipher(a.key)
	if err != nil {
		return "", err
	}
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return "", err
	}
	nonce := make([]byte, gcm.NonceSize())
	if _, err = io.ReadFull(rand.Reader, nonce); err != nil {
		return "", err
	}
	ciphertext := gcm.Seal(nonce, nonce, []byte(plaintext), nil)
	return base64.StdEncoding.EncodeToString(ciphertext), nil
}

func (a *AESEncryptor) Decrypt(ciphertext string) (string, error) {
	data, err := base64.StdEncoding.DecodeString(ciphertext)
	if err != nil {
		return "", err
	}
	block, err := aes.NewCipher(a.key)
	if err != nil {
		return "", err
	}
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return "", err
	}
	nonceSize := gcm.NonceSize()
	if len(data) < nonceSize {
		return "", errors.New("ciphertext too short")
	}
	plaintext, err := gcm.Open(nil, data[:nonceSize], data[nonceSize:], nil)
	if err != nil {
		return "", err
	}
	return string(plaintext), nil
}

// --- Rate Limiting ---

type TokenBucketLimiter struct {
	capacity   int
	refillRate int
	tokens     int
	lastRefill time.Time
	mu         sync.Mutex
}

func NewTokenBucketLimiter(capacity, refillRate int) *TokenBucketLimiter {
	return &TokenBucketLimiter{
		capacity:   capacity,
		refillRate: refillRate,
		tokens:     capacity,
		lastRefill: time.Now(),
	}
}

func (t *TokenBucketLimiter) Allow(identifier string) bool {
	t.mu.Lock()
	defer t.mu.Unlock()

	now := time.Now()
	elapsed := now.Sub(t.lastRefill).Seconds()
	t.tokens += int(elapsed * float64(t.refillRate))
	if t.tokens > t.capacity {
		t.tokens = t.capacity
	}
	t.lastRefill = now

	if t.tokens > 0 {
		t.tokens--
		return true
	}
	return false
}

// --- Input Validation ---

type InputValidator struct{}

func NewInputValidator() *InputValidator {
	return &InputValidator{}
}

func (v *InputValidator) IsValidEmail(email string) bool {
	re := regexp.MustCompile(`^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$`)
	return re.MatchString(email)
}

func (v *InputValidator) IsValidPhone(phone string) bool {
	clean := regexp.MustCompile(`[\s\-\(\)]`).ReplaceAllString(phone, "")
	return regexp.MustCompile(`^\+?[1-9]\d{6,14}$`).MatchString(clean)
}

func (v *InputValidator) SanitizeSQL(input string) string {
	patterns := []string{"'", "\"", ";", "--", "/*", "*/", "DROP", "DELETE", "INSERT", "UPDATE", "SELECT"}
	sanitized := input
	for _, p := range patterns {
		sanitized = strings.ReplaceAll(sanitized, p, "")
	}
	return sanitized
}

func (v *InputValidator) SanitizeHTML(input string) string {
	re := regexp.MustCompile(`<[^>]*>`)
	return re.ReplaceAllString(input, "")
}

// --- Middleware & CORS ---

type CORSHandler struct{}

func NewCORSHandler() *CORSHandler {
	return &CORSHandler{}
}

func (c *CORSHandler) HandlePreflight(headers map[string][]string) (bool, map[string]string) {
	return true, map[string]string{
		"Access-Control-Allow-Origin":  "*",
		"Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
		"Access-Control-Allow-Headers": "Content-Type, Authorization",
	}
}

func (c *CORSHandler) HandleRequest(headers map[string][]string) map[string]string {
	return map[string]string{
		"Access-Control-Allow-Origin": "*",
	}
}

type SecurityRequest struct {
	Method  string
	Path    string
	Headers map[string][]string
	Body    string
}

type SecurityMiddleware struct{}

func NewSecurityMiddleware() *SecurityMiddleware {
	return &SecurityMiddleware{}
}

func (s *SecurityMiddleware) ValidateRequest(req *SecurityRequest) (bool, []string) {
	return true, nil
}

func (s *SecurityMiddleware) GetSecurityHeaders() map[string]string {
	return map[string]string{
		"X-Content-Type-Options": "nosniff",
		"X-Frame-Options":        "DENY",
		"X-XSS-Protection":       "1; mode=block",
	}
}

// --- Utilities ---

func generateID() string {
	b := make([]byte, 16)
	rand.Read(b)
	return hex.EncodeToString(b)
}

func generateSalt() string {
	b := make([]byte, 8)
	rand.Read(b)
	return hex.EncodeToString(b)
}

func sha256Hex(s string) string {
	h := sha256.Sum256([]byte(s))
	return hex.EncodeToString(h[:])
}

// --- Main Runner ---

func main() {
	fmt.Println("=== Security Examples Demo ===")

	testAuthentication()
	testAuthorization()
	testEncryption()
	testHashing()
	testJWT()
	testRateLimiting()
	testInputValidation()
	testCORS()
	testSecurityMiddleware()
}

func testAuthentication() {
	fmt.Println("\n=== Authentication Demo ===")
	auth := NewAuthService()

	user, err := auth.Register("john@example.com", "password123")
	if err != nil {
		log.Printf("Registration failed: %v", err)
		return
	}
	fmt.Printf("User registered: %+v\n", user)

	token, err := auth.Login("john@example.com", "password123")
	if err != nil {
		log.Printf("Login failed: %v", err)
		return
	}
	fmt.Printf("Login successful, token: %s\n", token)

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

	authz.AddPermission("read:users", "users", "read", "Read users list")
	authz.AddPermission("write:users", "users", "write", "Create/Update users")
	authz.AddPermission("delete:users", "users", "delete", "Delete users")

	authz.CreateRole("admin", "System Administrator")
	authz.CreateRole("user", "Standard User")

	authz.AssignPermissionToRole("admin", "read:users")
	authz.AssignPermissionToRole("admin", "write:users")
	authz.AssignPermissionToRole("admin", "delete:users")
	authz.AssignPermissionToRole("user", "read:users")

	userID := "usr_101"
	authz.AssignRoleToUser(userID, "admin")

	canRead := authz.UserHasPermission(userID, "read:users")
	canDelete := authz.UserHasPermission(userID, "delete:users")

	fmt.Printf("User can read users: %t\n", canRead)
	fmt.Printf("User can delete users: %t\n", canDelete)
}

func testEncryption() {
	fmt.Println("\n=== Encryption Demo ===")
	encryptor := NewAESEncryptor("my-secret-key-32-characters-long!!")
	original := "This is a secret message"

	encrypted, err := encryptor.Encrypt(original)
	if err != nil {
		log.Printf("Encryption failed: %v", err)
		return
	}

	fmt.Printf("Original: %s\n", original)
	fmt.Printf("Encrypted: %s\n", encrypted)

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

	hashed, err := hasher.Hash(password)
	if err != nil {
		log.Printf("Hashing failed: %v", err)
		return
	}

	fmt.Printf("Original password: %s\n", password)
	fmt.Printf("Hashed password: %s\n", hashed)

	valid := hasher.Verify(password, hashed)
	fmt.Printf("Password verification: %t\n", valid)

	invalid := hasher.Verify("wrongpassword", hashed)
	fmt.Printf("Wrong password verification: %t\n", invalid)
}

func testJWT() {
	fmt.Println("\n=== JWT Demo ===")
	jwtService := NewJWTService("my-secret-key")
	claims := &JWTClaims{
		UserID: "123",
		Email:  "user@example.com",
		Roles:  []string{"user", "admin"},
	}

	token, err := jwtService.GenerateToken(claims)
	if err != nil {
		log.Printf("Token generation failed: %v", err)
		return
	}
	fmt.Printf("Generated token: %s\n", token)

	validatedClaims, err := jwtService.ValidateToken(token)
	if err != nil {
		log.Printf("Token validation failed: %v", err)
		return
	}
	fmt.Printf("Validated claims: %+v\n", validatedClaims)

	expiredClaims := &JWTClaims{
		UserID:    "123",
		Email:     "user@example.com",
		ExpiresAt: time.Now().Add(-time.Hour).Unix(),
	}

	expiredToken, err := jwtService.GenerateToken(expiredClaims)
	if err != nil {
		log.Printf("Expired token generation failed: %v", err)
		return
	}

	_, err = jwtService.ValidateToken(expiredToken)
	fmt.Printf("Expired token validation failed (expected): %v\n", err != nil)
}

func testRateLimiting() {
	fmt.Println("\n=== Rate Limiting Demo ===")
	limiter := NewTokenBucketLimiter(10, 1)
	clientID := "client123"

	for i := 0; i < 15; i++ {
		allowed := limiter.Allow(clientID)
		fmt.Printf("Request %d: %t\n", i+1, allowed)
		if !allowed {
			fmt.Printf("Rate limit exceeded at request %d\n", i+1)
			break
		}
	}

	fmt.Println("Waiting 2 seconds for token refill...")
	time.Sleep(2 * time.Second)

	allowed := limiter.Allow(clientID)
	fmt.Printf("Request after wait: %t\n", allowed)
}

func testInputValidation() {
	fmt.Println("\n=== Input Validation Demo ===")
	validator := NewInputValidator()

	email := "user@example.com"
	valid := validator.IsValidEmail(email)
	fmt.Printf("Email %s is valid: %t\n", email, valid)

	invalidEmail := "invalid-email"
	valid = validator.IsValidEmail(invalidEmail)
	fmt.Printf("Email %s is valid: %t\n", invalidEmail, valid)

	phone := "+1234567890"
	valid = validator.IsValidPhone(phone)
	fmt.Printf("Phone %s is valid: %t\n", phone, valid)

	input := "'; DROP TABLE users; --"
	sanitized := validator.SanitizeSQL(input)
	fmt.Printf("Original: %s\n", input)
	fmt.Printf("Sanitized: %s\n", sanitized)

	xssInput := "<script>alert('xss')</script>"
	sanitizedHTML := validator.SanitizeHTML(xssInput)
	fmt.Printf("Original HTML: %s\n", xssInput)
	fmt.Printf("Sanitized HTML: %s\n", sanitizedHTML)
}

func testCORS() {
	fmt.Println("\n=== CORS Demo ===")
	cors := NewCORSHandler()

	headers := map[string][]string{
		"Origin":                         {"https://example.com"},
		"Access-Control-Request-Method":  {"POST"},
		"Access-Control-Request-Headers": {"Content-Type"},
	}

	allowed, responseHeaders := cors.HandlePreflight(headers)
	fmt.Printf("Preflight allowed: %t\n", allowed)
	fmt.Printf("Response headers: %+v\n", responseHeaders)

	requestHeaders := map[string][]string{
		"Origin": {"https://example.com"},
	}

	responseHeaders = cors.HandleRequest(requestHeaders)
	fmt.Printf("CORS headers for actual request: %+v\n", responseHeaders)
}

func testSecurityMiddleware() {
	fmt.Println("\n=== Security Middleware Demo ===")
	security := NewSecurityMiddleware()
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

	headers := security.GetSecurityHeaders()
	fmt.Printf("Security headers: %+v\n", headers)
}
