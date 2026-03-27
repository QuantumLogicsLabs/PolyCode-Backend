package main

import (
	"crypto/rand"
	"encoding/hex"
	"errors"
	"fmt"
	"time"
)

// User represents a user in the system
type User struct {
	ID        string    `json:"id"`
	Email     string    `json:"email"`
	Password  string    `json:"-"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

// AuthService handles authentication
type AuthService struct {
	users    map[string]*User
	sessions map[string]*Session
	hasher   PasswordHasher
	jwt      JWTService
}

type Session struct {
	ID        string    `json:"id"`
	UserID    string    `json:"user_id"`
	Token     string    `json:"token"`
	ExpiresAt time.Time `json:"expires_at"`
	CreatedAt time.Time `json:"created_at"`
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
	
	if !isValidEmail(email) {
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
	
	// Create JWT token
	claims := &JWTClaims{
		UserID: user.ID,
		Email:  user.Email,
	}
	
	token, err := a.jwt.GenerateToken(claims)
	if err != nil {
		return "", fmt.Errorf("failed to generate token: %w", err)
	}
	
	// Create session
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
		return "", errors.New("session not found")
	}
	
	if time.Now().After(session.ExpiresAt) {
		delete(a.sessions, token)
		return "", errors.New("session expired")
	}
	
	// Validate JWT
	claims, err := a.jwt.ValidateToken(token)
	if err != nil {
		delete(a.sessions, token)
		return "", fmt.Errorf("invalid token: %w", err)
	}
	
	user, exists := a.users[claims.Email]
	if !exists {
		return "", errors.New("user not found")
	}
	
	return user, nil
}

func (a *AuthService) Logout(token string) error {
	delete(a.sessions, token)
	return nil
}

func (a *AuthService) RefreshToken(token string) (string, error) {
	user, err := a.ValidateToken(token)
	if err != nil {
		return "", err
	}
	
	// Create new token
	claims := &JWTClaims{
		UserID: user.ID,
		Email:  user.Email,
	}
	
	newToken, err := a.jwt.GenerateToken(claims)
	if err != nil {
		return "", fmt.Errorf("failed to generate new token: %w", err)
	}
	
	// Remove old session
	delete(a.sessions, token)
	
	// Create new session
	session := &Session{
		ID:        generateID(),
		UserID:    user.ID,
		Token:     newToken,
		ExpiresAt: time.Now().Add(24 * time.Hour),
		CreatedAt: time.Now(),
	}
	
	a.sessions[newToken] = session
	return newToken, nil
}

// PasswordHasher interface
type PasswordHasher interface {
	Hash(password string) (string, error)
	Verify(password, hash string) bool
}

// BcryptHasher implements PasswordHasher
type BcryptHasher struct {
	cost int
}

func NewBcryptHasher() *BcryptHasher {
	return &BcryptHasher{cost: 12}
}

func (b *BcryptHasher) Hash(password string) (string, error) {
	// Simplified bcrypt implementation
	// In production, use golang.org/x/crypto/bcrypt
	salt := generateSalt()
	hashed := simpleHash(password + salt)
	return fmt.Sprintf("%s$%s", salt, hashed), nil
}

func (b *BcryptHasher) Verify(password, hash string) bool {
	parts := splitString(hash, "$")
	if len(parts) != 2 {
		return false
	}
	
	salt := parts[0]
	expectedHash := parts[1]
	
	actualHash := simpleHash(password + salt)
	return actualHash == expectedHash
}

// JWT Service
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
	
	// Simplified JWT implementation
	// In production, use github.com/golang-jwt/jwt
	tokenData := fmt.Sprintf("%s.%s", j.secretKey, claimsToString(claims))
	signature := simpleHash(tokenData)
	
	return fmt.Sprintf("%s.%s", tokenData, signature), nil
}

func (j *JWTService) ValidateToken(token string) (*JWTClaims, error) {
	parts := splitString(token, ".")
	if len(parts) != 3 {
		return nil, errors.New("invalid token format")
	}
	
	tokenData := fmt.Sprintf("%s.%s", parts[0], parts[1])
	expectedSignature := simpleHash(tokenData)
	
	if parts[2] != expectedSignature {
		return nil, errors.New("invalid token signature")
	}
	
	claims, err := stringToClaims(parts[1])
	if err != nil {
		return nil, fmt.Errorf("invalid claims: %w", err)
	}
	
	if claims.ExpiresAt > 0 && time.Now().Unix() > claims.ExpiresAt {
		return nil, errors.New("token expired")
	}
	
	return claims, nil
}

// Utility functions
func generateID() string {
	bytes := make([]byte, 16)
	rand.Read(bytes)
	return hex.EncodeToString(bytes)
}

func generateSalt() string {
	bytes := make([]byte, 16)
	rand.Read(bytes)
	return hex.EncodeToString(bytes)
}

func simpleHash(input string) string {
	// Simplified hash function
	// In production, use proper cryptographic hash
	hash := 0
	for _, c := range input {
		hash = hash*31 + int(c)
	}
	return fmt.Sprintf("%x", hash)
}

func isValidEmail(email string) bool {
	return contains(email, "@") && contains(email, ".")
}

func contains(s, substr string) bool {
	for i := 0; i <= len(s)-len(substr); i++ {
		if s[i:i+len(substr)] == substr {
			return true
		}
	}
	return false
}

func splitString(s, sep string) []string {
	var parts []string
	start := 0
	
	for i := 0; i < len(s); i++ {
		if i+len(sep) <= len(s) && s[i:i+len(sep)] == sep {
			parts = append(parts, s[start:i])
			start = i + len(sep)
			i += len(sep) - 1
		}
	}
	
	parts = append(parts, s[start:])
	return parts
}

func claimsToString(claims *JWTClaims) string {
	return fmt.Sprintf("user_id:%s,email:%s,exp:%d,iat:%d", 
		claims.UserID, claims.Email, claims.ExpiresAt, claims.IssuedAt)
}

func stringToClaims(s string) (*JWTClaims, error) {
	// Simplified parsing
	// In production, use JSON parsing
	parts := splitString(s, ",")
	claims := &JWTClaims{}
	
	for _, part := range parts {
		keyValue := splitString(part, ":")
		if len(keyValue) == 2 {
			switch keyValue[0] {
			case "user_id":
				claims.UserID = keyValue[1]
			case "email":
				claims.Email = keyValue[1]
			case "exp":
				claims.ExpiresAt = parseInt(keyValue[1])
			case "iat":
				claims.IssuedAt = parseInt(keyValue[1])
			}
		}
	}
	
	return claims, nil
}

func parseInt(s string) int64 {
	result := int64(0)
	for _, c := range s {
		if c >= '0' && c <= '9' {
			result = result*10 + int64(c-'0')
		}
	}
	return result
}
