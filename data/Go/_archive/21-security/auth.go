package main

import (
	"crypto/rand"
	"crypto/sha256"
	"encoding/base64"
	"encoding/hex"
	"errors"
	"fmt"
	"strconv"
	"strings"
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

// Session model
type Session struct {
	ID        string    `json:"id"`
	UserID    string    `json:"user_id"`
	Token     string    `json:"token"`
	ExpiresAt time.Time `json:"expires_at"`
	CreatedAt time.Time `json:"created_at"`
}

// PasswordHasher interface
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

// AuthService handles authentication
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

func (a *AuthService) Logout(token string) error {
	delete(a.sessions, token)
	return nil
}

func (a *AuthService) RefreshToken(token string) (string, error) {
	user, err := a.ValidateToken(token)
	if err != nil {
		return "", err
	}

	claims := &JWTClaims{
		UserID: user.ID,
		Email:  user.Email,
	}

	newToken, err := a.jwt.GenerateToken(claims)
	if err != nil {
		return "", fmt.Errorf("failed to generate new token: %w", err)
	}

	delete(a.sessions, token)

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

// Helpers
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

func main() {
	fmt.Println("=== Authentication Service Demo ===")
	auth := NewAuthService()

	// 1. Register
	user, err := auth.Register("alice@example.com", "securePass123")
	if err != nil {
		fmt.Printf("Registration error: %v\n", err)
		return
	}
	fmt.Printf("Registered user: ID=%s, Email=%s\n", user.ID, user.Email)

	// 2. Login
	token, err := auth.Login("alice@example.com", "securePass123")
	if err != nil {
		fmt.Printf("Login error: %v\n", err)
		return
	}
	fmt.Printf("Login success, JWT Token: %s\n", token)

	// 3. Validate Token
	valUser, err := auth.ValidateToken(token)
	if err != nil {
		fmt.Printf("Token validation failed: %v\n", err)
		return
	}
	fmt.Printf("Token valid for: %s (ID: %s)\n", valUser.Email, valUser.ID)

	// 4. Refresh Token
	newToken, err := auth.RefreshToken(token)
	if err != nil {
		fmt.Printf("Token refresh failed: %v\n", err)
		return
	}
	fmt.Printf("Refreshed JWT Token: %s\n", newToken)
}
