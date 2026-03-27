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
)

// Encryptor interface
type Encryptor interface {
	Encrypt(plaintext string) (string, error)
	Decrypt(ciphertext string) (string, error)
}

// AESEncryptor implements AES encryption
type AESEncryptor struct {
	key []byte
}

func NewAESEncryptor(keyString string) *AESEncryptor {
	// Convert key string to 32-byte key using SHA256
	hash := sha256.Sum256([]byte(keyString))
	return &AESEncryptor{key: hash[:]}
}

func (a *AESEncryptor) Encrypt(plaintext string) (string, error) {
	block, err := aes.NewCipher(a.key)
	if err != nil {
		return "", fmt.Errorf("failed to create cipher: %w", err)
	}
	
	// Create byte array from plaintext
	plaintextBytes := []byte(plaintext)
	
	// Create GCM cipher
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return "", fmt.Errorf("failed to create GCM: %w", err)
	}
	
	// Create nonce
	nonce := make([]byte, gcm.NonceSize())
	if _, err = io.ReadFull(rand.Reader, nonce); err != nil {
		return "", fmt.Errorf("failed to generate nonce: %w", err)
	}
	
	// Encrypt
	ciphertext := gcm.Seal(nonce, nonce, plaintextBytes, nil)
	
	// Return base64 encoded ciphertext
	return base64.StdEncoding.EncodeToString(ciphertext), nil
}

func (a *AESEncryptor) Decrypt(ciphertext string) (string, error) {
	// Decode base64
	ciphertextBytes, err := base64.StdEncoding.DecodeString(ciphertext)
	if err != nil {
		return "", fmt.Errorf("failed to decode ciphertext: %w", err)
	}
	
	block, err := aes.NewCipher(a.key)
	if err != nil {
		return "", fmt.Errorf("failed to create cipher: %w", err)
	}
	
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return "", fmt.Errorf("failed to create GCM: %w", err)
	}
	
	nonceSize := gcm.NonceSize()
	if len(ciphertextBytes) < nonceSize {
		return "", errors.New("ciphertext too short")
	}
	
	nonce, ciphertextBytes := ciphertextBytes[:nonceSize], ciphertextBytes[nonceSize:]
	plaintext, err := gcm.Open(nil, nonce, ciphertextBytes, nil)
	if err != nil {
		return "", fmt.Errorf("failed to decrypt: %w", err)
	}
	
	return string(plaintext), nil
}

// RSAPublicKey represents RSA public key
type RSAPublicKey struct {
	N string `json:"n"` // Modulus
	E string `json:"e"` // Exponent
}

// RSAPrivateKey represents RSA private key
type RSAPrivateKey struct {
	N string `json:"n"`
	D string `json:"d"` // Private exponent
	P string `json:"p"` // Prime factor
	Q string `json:"q"` // Prime factor
}

// RSAEncryptor implements RSA encryption (simplified)
type RSAEncryptor struct {
	publicKey  *RSAPublicKey
	privateKey *RSAPrivateKey
}

func NewRSAEncryptor() *RSAEncryptor {
	// In production, generate proper RSA keys
	return &RSAEncryptor{
		publicKey: &RSAPublicKey{
			N: "1234567890123456789012345678901234567890",
			E: "65537",
		},
		privateKey: &RSAPrivateKey{
			N: "1234567890123456789012345678901234567890",
			D: "1234567890123456789012345678901234567890",
			P: "1234567891",
			Q: "1234567891",
		},
	}
}

func (r *RSAEncryptor) Encrypt(plaintext string) (string, error) {
	// Simplified RSA encryption
	// In production, use crypto/rsa package
	
	// Convert plaintext to bytes
	plaintextBytes := []byte(plaintext)
	
	// Simple XOR encryption for demonstration
	key := []byte(r.publicKey.N)
	if len(key) > len(plaintextBytes) {
		key = key[:len(plaintextBytes)]
	}
	
	encrypted := make([]byte, len(plaintextBytes))
	for i := 0; i < len(plaintextBytes); i++ {
		encrypted[i] = plaintextBytes[i] ^ key[i%len(key)]
	}
	
	return hex.EncodeToString(encrypted), nil
}

func (r *RSAEncryptor) Decrypt(ciphertext string) (string, error) {
	// Decode hex
	ciphertextBytes, err := hex.DecodeString(ciphertext)
	if err != nil {
		return "", fmt.Errorf("failed to decode ciphertext: %w", err)
	}
	
	// Simple XOR decryption
	key := []byte(r.privateKey.N)
	if len(key) > len(ciphertextBytes) {
		key = key[:len(ciphertextBytes)]
	}
	
	decrypted := make([]byte, len(ciphertextBytes))
	for i := 0; i < len(ciphertextBytes); i++ {
		decrypted[i] = ciphertextBytes[i] ^ key[i%len(key)]
	}
	
	return string(decrypted), nil
}

// Hashing utilities
type Hasher interface {
	Hash(data string) string
	Verify(data, hash string) bool
}

type SHA256Hasher struct{}

func NewSHA256Hasher() *SHA256Hasher {
	return &SHA256Hasher{}
}

func (s *SHA256Hasher) Hash(data string) string {
	hash := sha256.Sum256([]byte(data))
	return hex.EncodeToString(hash[:])
}

func (s *SHA256Hasher) Verify(data, hash string) bool {
	return s.Hash(data) == hash
}

// KeyDerivation for generating keys from passwords
type KeyDerivation struct {
	salt []byte
}

func NewKeyDerivation(salt string) *KeyDerivation {
	return &KeyDerivation{
		salt: []byte(salt),
	}
}

func (k *KeyDerivation) DeriveKey(password string, length int) []byte {
	// Simplified key derivation
	// In production, use crypto/scrypt or crypto/pbkdf2
	
	hasher := sha256.New()
	hasher.Write([]byte(password))
	hasher.Write(k.salt)
	
	hash := hasher.Sum(nil)
	
	// Repeat until we have enough bytes
	for len(hash) < length {
		hasher.Reset()
		hasher.Write(hash)
		hasher.Write(k.salt)
		hash = hasher.Sum(hash)
	}
	
	return hash[:length]
}

// Digital signature (simplified)
type DigitalSignature struct {
	privateKey string
	publicKey  string
}

func NewDigitalSignature() *DigitalSignature {
	return &DigitalSignature{
		privateKey: "private_key_1234567890",
		publicKey:  "public_key_1234567890",
	}
}

func (d *DigitalSignature) Sign(message string) (string, error) {
	// Simplified signing
	// In production, use proper digital signature algorithms
	
	hasher := sha256.New()
	hasher.Write([]byte(message))
	hasher.Write([]byte(d.privateKey))
	
	hash := hasher.Sum(nil)
	return hex.EncodeToString(hash), nil
}

func (d *DigitalSignature) Verify(message, signature string) bool {
	expectedSignature, err := d.Sign(message)
	if err != nil {
		return false
	}
	
	return signature == expectedSignature
}

// Secure random generator
type SecureRandom struct{}

func NewSecureRandom() *SecureRandom {
	return &SecureRandom{}
}

func (s *SecureRandom) GenerateBytes(length int) ([]byte, error) {
	bytes := make([]byte, length)
	_, err := rand.Read(bytes)
	if err != nil {
		return nil, fmt.Errorf("failed to generate random bytes: %w", err)
	}
	return bytes, nil
}

func (s *SecureRandom) GenerateString(length int) (string, error) {
	const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
	bytes, err := s.GenerateBytes(length)
	if err != nil {
		return "", err
	}
	
	for i, b := range bytes {
		bytes[i] = charset[b%byte(len(charset))]
	}
	
	return string(bytes), nil
}

func (s *SecureRandom) GenerateHex(length int) (string, error) {
	bytes, err := s.GenerateBytes(length)
	if err != nil {
		return "", err
	}
	
	return hex.EncodeToString(bytes), nil
}

// Password strength checker
type PasswordStrength struct{}

func NewPasswordStrength() *PasswordStrength {
	return &PasswordStrength{}
}

func (p *PasswordStrength) CheckStrength(password string) (int, string) {
	score := 0
	feedbacks := []string{}
	
	// Length check
	if len(password) >= 8 {
		score += 1
	} else {
		feedbacks = append(feedbacks, "Password should be at least 8 characters")
	}
	
	if len(password) >= 12 {
		score += 1
	}
	
	// Complexity checks
	if hasUpperCase(password) {
		score += 1
	} else {
		feedbacks = append(feedbacks, "Password should contain uppercase letters")
	}
	
	if hasLowerCase(password) {
		score += 1
	} else {
		feedbacks = append(feedbacks, "Password should contain lowercase letters")
	}
	
	if hasDigit(password) {
		score += 1
	} else {
		feedbacks = append(feedbacks, "Password should contain digits")
	}
	
	if hasSpecialChar(password) {
		score += 1
	} else {
		feedbacks = append(feedbacks, "Password should contain special characters")
	}
	
	// Return strength level
	strength := "Weak"
	if score >= 5 {
		strength = "Strong"
	} else if score >= 3 {
		strength = "Medium"
	}
	
	feedback := strength
	if len(feedbacks) > 0 {
		feedback += ": " + joinStrings(feedbacks, ", ")
	}
	
	return score, feedback
}

// Utility functions
func hasUpperCase(s string) bool {
	for _, c := range s {
		if c >= 'A' && c <= 'Z' {
			return true
		}
	}
	return false
}

func hasLowerCase(s string) bool {
	for _, c := range s {
		if c >= 'a' && c <= 'z' {
			return true
		}
	}
	return false
}

func hasDigit(s string) bool {
	for _, c := range s {
		if c >= '0' && c <= '9' {
			return true
		}
	}
	return false
}

func hasSpecialChar(s string) bool {
	specialChars := "!@#$%^&*()_+-=[]{}|;:,.<>?"
	for _, c := range s {
		for _, sc := range specialChars {
			if c == sc {
				return true
			}
		}
	}
	return false
}

func joinStrings(strs []string, sep string) string {
	if len(strs) == 0 {
		return ""
	}
	
	result := strs[0]
	for i := 1; i < len(strs); i++ {
		result += sep + strs[i]
	}
	return result
}
