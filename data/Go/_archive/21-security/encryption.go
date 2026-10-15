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

// AESEncryptor implements AES-GCM encryption
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
		return "", fmt.Errorf("failed to create cipher: %w", err)
	}

	plaintextBytes := []byte(plaintext)
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return "", fmt.Errorf("failed to create GCM: %w", err)
	}

	nonce := make([]byte, gcm.NonceSize())
	if _, err = io.ReadFull(rand.Reader, nonce); err != nil {
		return "", fmt.Errorf("failed to generate nonce: %w", err)
	}

	ciphertext := gcm.Seal(nonce, nonce, plaintextBytes, nil)
	return base64.StdEncoding.EncodeToString(ciphertext), nil
}

func (a *AESEncryptor) Decrypt(ciphertext string) (string, error) {
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

// RSA Keys and simplified RSA Encryptor
type RSAPublicKey struct {
	N string `json:"n"`
	E string `json:"e"`
}

type RSAPrivateKey struct {
	N string `json:"n"`
	D string `json:"d"`
	P string `json:"p"`
	Q string `json:"q"`
}

type RSAEncryptor struct {
	publicKey  *RSAPublicKey
	privateKey *RSAPrivateKey
}

func NewRSAEncryptor() *RSAEncryptor {
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
	plaintextBytes := []byte(plaintext)
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
	ciphertextBytes, err := hex.DecodeString(ciphertext)
	if err != nil {
		return "", fmt.Errorf("failed to decode ciphertext: %w", err)
	}

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

// Hasher interface and SHA256 implementation
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

// KeyDerivation
type KeyDerivation struct {
	salt []byte
}

func NewKeyDerivation(salt string) *KeyDerivation {
	return &KeyDerivation{
		salt: []byte(salt),
	}
}

func (k *KeyDerivation) DeriveKey(password string, length int) []byte {
	hasher := sha256.New()
	hasher.Write([]byte(password))
	hasher.Write(k.salt)

	hash := hasher.Sum(nil)
	for len(hash) < length {
		hasher.Reset()
		hasher.Write(hash)
		hasher.Write(k.salt)
		hash = hasher.Sum(hash)
	}

	return hash[:length]
}

// Digital Signature
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

// Password Strength
type PasswordStrength struct{}

func NewPasswordStrength() *PasswordStrength {
	return &PasswordStrength{}
}

func (p *PasswordStrength) CheckStrength(password string) (int, string) {
	score := 0
	var feedbacks []string

	if len(password) >= 8 {
		score++
	} else {
		feedbacks = append(feedbacks, "Password should be at least 8 characters")
	}

	if len(password) >= 12 {
		score++
	}

	if hasUpperCase(password) {
		score++
	} else {
		feedbacks = append(feedbacks, "Password should contain uppercase letters")
	}

	if hasLowerCase(password) {
		score++
	} else {
		feedbacks = append(feedbacks, "Password should contain lowercase letters")
	}

	if hasDigit(password) {
		score++
	} else {
		feedbacks = append(feedbacks, "Password should contain digits")
	}

	if hasSpecialChar(password) {
		score++
	} else {
		feedbacks = append(feedbacks, "Password should contain special characters")
	}

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

func main() {
	fmt.Println("=== AES Encryption Demo ===")
	aesEnc := NewAESEncryptor("super-secret-passphrase-key-32b")
	plain := "Sensitive User Data"
	encrypted, _ := aesEnc.Encrypt(plain)
	decrypted, _ := aesEnc.Decrypt(encrypted)
	fmt.Printf("Plaintext: %s\nEncrypted (Base64): %s\nDecrypted: %s\nMatch: %t\n", plain, encrypted, decrypted, plain == decrypted)

	fmt.Println("\n=== SHA-256 Hashing Demo ===")
	hasher := NewSHA256Hasher()
	hash := hasher.Hash("PolyCodeBackend2026")
	fmt.Printf("SHA-256 Hash: %s\nHash Verified: %t\n", hash, hasher.Verify("PolyCodeBackend2026", hash))

	fmt.Println("\n=== Digital Signature Demo ===")
	signer := NewDigitalSignature()
	sig, _ := signer.Sign("Critical Financial Order #1001")
	fmt.Printf("Signature: %s\nSignature Verified: %t\n", sig, signer.Verify("Critical Financial Order #1001", sig))

	fmt.Println("\n=== Password Strength Demo ===")
	checker := NewPasswordStrength()
	score, feedback := checker.CheckStrength("P@ssw0rd2026!Sec")
	fmt.Printf("Score: %d/6\nFeedback: %s\n", score, feedback)
}
