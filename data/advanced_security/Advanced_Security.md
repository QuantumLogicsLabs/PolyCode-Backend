# Advanced Security and Cryptography

This file contains comprehensive advanced security and cryptography examples in C, including secure memory management, random number generation, hashing functions, key derivation, symmetric and asymmetric encryption, digital signatures, certificates, password security, secure communication, and security auditing.

## 📚 Advanced Security Fundamentals

### 🔐 Security Concepts
- **Confidentiality**: Protecting data from unauthorized access
- **Integrity**: Ensuring data is not tampered with
- **Authentication**: Verifying identity of users and systems
- **Authorization**: Controlling access to resources
- **Non-repudiation**: Preventing denial of actions

### 🛡️ Cryptographic Principles
- **Strong Algorithms**: Use well-vetted cryptographic algorithms
- **Proper Implementation**: Avoid common implementation mistakes
- **Key Management**: Secure generation, storage, and rotation of keys
- **Random Number Generation**: Use cryptographically secure random numbers
- **Secure Memory**: Prevent sensitive data from leaking

## 🔒 Secure Memory Management

### Secure Allocation
```c
// Secure memory allocation
void* secureMalloc(size_t size) {
    void* ptr = malloc(size);
    if (ptr) {
        // Lock memory to prevent swapping (if available)
        if (mlock(ptr, size) != 0) {
            // Non-fatal, continue without memory lock
        }
    }
    return ptr;
}

// Secure memory deallocation
void secureFree(void* ptr, size_t size) {
    if (ptr) {
        // Clear memory before freeing
        memset(ptr, 0, size);
        
        // Unlock memory
        munlock(ptr, size);
        
        free(ptr);
    }
}

// Secure string copy
void secureStrcpy(char* dest, const char* src, size_t dest_size) {
    if (dest && src && dest_size > 0) {
        strncpy(dest, src, dest_size - 1);
        dest[dest_size - 1] = '\0';
        
        // Clear any overflow
        if (strlen(src) >= dest_size) {
            dest[dest_size - 1] = '\0';
        }
    }
}
```

**Secure Memory Benefits**:
- **Data Protection**: Prevents sensitive data from remaining in memory
- **Swap Protection**: Locks memory to prevent swapping to disk
- **Zeroization**: Clears memory before deallocation
- **Bounds Checking**: Prevents buffer overflows

## 🎲 Random Number Generation

### Cryptographically Secure Random Numbers
```c
// Generate cryptographically secure random bytes
int generateRandomBytes(unsigned char* buffer, size_t size) {
    if (!buffer || size == 0) {
        return -1;
    }
    
    int result = RAND_bytes(buffer, size);
    return result == 1 ? 0 : -1;
}

// Generate random integer
int generateRandomInt(int min, int max) {
    if (min > max) {
        return -1;
    }
    
    unsigned int random_value;
    if (generateRandomBytes((unsigned char*)&random_value, sizeof(random_value)) != 0) {
        return -1;
    }
    
    return min + (random_value % (max - min + 1));
}

// Generate random string
int generateRandomString(char* buffer, size_t length, const char* charset) {
    if (!buffer || length == 0 || !charset) {
        return -1;
    }
    
    size_t charset_length = strlen(charset);
    
    for (size_t i = 0; i < length - 1; i++) {
        int random_index = generateRandomInt(0, charset_length - 1);
        if (random_index < 0) {
            return -1;
        }
        buffer[i] = charset[random_index];
    }
    
    buffer[length - 1] = '\0';
    return 0;
}
```

**Random Number Benefits**:
- **Cryptographic Security**: Uses OpenSSL's secure random generator
- **Uniform Distribution**: Ensures proper random distribution
- **No Predictability**: Impossible to predict future values
- **Multiple Types**: Bytes, integers, strings, etc.

## 🔐 Hash Functions

### SHA-256 and SHA-512
```c
// Compute SHA-256 hash
int computeSHA256(const unsigned char* data, size_t data_length, 
                  unsigned char* hash) {
    if (!data || !hash || data_length == 0) {
        return -1;
    }
    
    SHA256_CTX sha256;
    if (!SHA256_Init(&sha256)) {
        return -1;
    }
    
    if (!SHA256_Update(&sha256, data, data_length)) {
        return -1;
    }
    
    if (!SHA256_Final(hash, &sha256)) {
        return -1;
    }
    
    return 0;
}

// Compute SHA-512 hash
int computeSHA512(const unsigned char* data, size_t data_length, 
                  unsigned char* hash) {
    if (!data || !hash || data_length == 0) {
        return -1;
    }
    
    SHA512_CTX sha512;
    if (!SHA512_Init(&sha512)) {
        return -1;
    }
    
    if (!SHA512_Update(&sha512, data, data_length)) {
        return -1;
    }
    
    if (!SHA512_Final(hash, &sha512)) {
        return -1;
    }
    
    return 0;
}
```

### HMAC (Hash-based Message Authentication Code)
```c
// HMAC-SHA256
int computeHMACSHA256(const unsigned char* data, size_t data_length,
                      const unsigned char* key, size_t key_length,
                      unsigned char* hmac) {
    if (!data || !key || !hmac || data_length == 0 || key_length == 0) {
        return -1;
    }
    
    unsigned int hmac_length;
    HMAC(EVP_sha256(), key, key_length, data, data_length, hmac, &hmac_length);
    
    return 0;
}
```

**Hash Function Benefits**:
- **Data Integrity**: Detects any changes to data
- **Fixed Output**: Consistent size output for any input
- **One-Way Function**: Computationally infeasible to reverse
- **Collision Resistance**: Hard to find two inputs with same hash

## 🔑 Key Derivation

### PBKDF2 (Password-Based Key Derivation Function 2)
```c
// PBKDF2 key derivation
int deriveKeyPBKDF2(const char* password, const unsigned char* salt, 
                    size_t salt_length, int iterations, int key_length,
                    unsigned char* derived_key) {
    if (!password || !salt || !derived_key || salt_length == 0 || key_length == 0) {
        return -1;
    }
    
    int result = PKCS5_PBKDF2_HMAC(password, strlen(password), 
                                    salt, salt_length, iterations,
                                    EVP_sha256(), derived_key, key_length);
    
    return result == 1 ? 0 : -1;
}

// Create derived key with random salt
DerivedKey* createDerivedKey(const char* password, int key_length, int iterations) {
    if (!password || key_length <= 0 || iterations <= 0) {
        return NULL;
    }
    
    DerivedKey* dk = malloc(sizeof(DerivedKey));
    if (!dk) {
        return NULL;
    }
    
    // Generate random salt
    if (generateRandomBytes(dk->salt, MAX_SALT_SIZE) != 0) {
        free(dk);
        return NULL;
    }
    
    // Derive key
    if (deriveKeyPBKDF2(password, dk->salt, MAX_SALT_SIZE, iterations, 
                       key_length, dk->key) != 0) {
        free(dk);
        return NULL;
    }
    
    dk->iterations = iterations;
    dk->key_length = key_length;
    
    return dk;
}
```

**Key Derivation Benefits**:
- **Password Security**: Converts passwords into cryptographic keys
- **Salt Usage**: Prevents rainbow table attacks
- **Iteration Count**: Slows down brute force attacks
- **Deterministic**: Same password always produces same key

## 🔐 Symmetric Encryption (AES)

### AES Encryption Context
```c
// AES encryption context
typedef struct {
    EVP_CIPHER_CTX* ctx;
    unsigned char key[MAX_KEY_SIZE];
    unsigned char iv[MAX_IV_SIZE];
    int key_length;
    int mode; // ECB, CBC, GCM
} AESContext;

// Initialize AES context for encryption
AESContext* initAESEncryption(const unsigned char* key, int key_length, 
                             const unsigned char* iv, int mode) {
    if (!key || key_length != 16 && key_length != 24 && key_length != 32) {
        return NULL;
    }
    
    AESContext* ctx = malloc(sizeof(AESContext));
    if (!ctx) {
        return NULL;
    }
    
    ctx->ctx = EVP_CIPHER_CTX_new();
    if (!ctx->ctx) {
        free(ctx);
        return NULL;
    }
    
    memcpy(ctx->key, key, key_length);
    ctx->key_length = key_length;
    ctx->mode = mode;
    
    if (iv) {
        memcpy(ctx->iv, iv, MAX_IV_SIZE);
    } else {
        // Generate random IV for modes that need it
        if (mode != 0) { // Not ECB
            generateRandomBytes(ctx->iv, MAX_IV_SIZE);
        }
    }
    
    return ctx;
}
```

### AES Encryption and Decryption
```c
// AES encryption
int aesEncrypt(AESContext* ctx, const unsigned char* plaintext, size_t plaintext_length,
                unsigned char* ciphertext, size_t* ciphertext_length) {
    if (!ctx || !plaintext || !ciphertext || !ciphertext_length) {
        return -1;
    }
    
    const EVP_CIPHER* cipher;
    
    switch (ctx->key_length) {
        case 16:
            cipher = (ctx->mode == 0) ? EVP_aes_128_ecb() : 
                    (ctx->mode == 1) ? EVP_aes_128_cbc() : EVP_aes_128_gcm();
            break;
        case 24:
            cipher = (ctx->mode == 0) ? EVP_aes_192_ecb() : 
                    (ctx->mode == 1) ? EVP_aes_192_cbc() : EVP_aes_192_gcm();
            break;
        case 32:
            cipher = (ctx->mode == 0) ? EVP_aes_256_ecb() : 
                    (ctx->mode == 1) ? EVP_aes_256_cbc() : EVP_aes_256_gcm();
            break;
        default:
            return -1;
    }
    
    if (EVP_EncryptInit_ex(ctx->ctx, cipher, NULL, ctx->key, 
                          ctx->mode == 0 ? NULL : ctx->iv) != 1) {
        return -1;
    }
    
    int len;
    int ciphertext_len = 0;
    
    if (EVP_EncryptUpdate(ctx->ctx, ciphertext, &len, plaintext, plaintext_length) != 1) {
        return -1;
    }
    
    ciphertext_len += len;
    
    if (EVP_EncryptFinal_ex(ctx->ctx, ciphertext + len, &len) != 1) {
        return -1;
    }
    
    ciphertext_len += len;
    *ciphertext_length = ciphertext_len;
    
    return 0;
}

// AES decryption
int aesDecrypt(AESContext* ctx, const unsigned char* ciphertext, size_t ciphertext_length,
                unsigned char* plaintext, size_t* plaintext_length) {
    if (!ctx || !ciphertext || !plaintext || !plaintext_length) {
        return -1;
    }
    
    const EVP_CIPHER* cipher;
    
    switch (ctx->key_length) {
        case 16:
            cipher = (ctx->mode == 0) ? EVP_aes_128_ecb() : 
                    (ctx->mode == 1) ? EVP_aes_128_cbc() : EVP_aes_128_gcm();
            break;
        case 24:
            cipher = (ctx->mode == 0) ? EVP_aes_192_ecb() : 
                    (ctx->mode == 1) ? EVP_aes_192_cbc() : EVP_aes_192_gcm();
            break;
        case 32:
            cipher = (ctx->mode == 0) ? EVP_aes_256_ecb() : 
                    (ctx->mode == 1) ? EVP_aes_256_cbc() : EVP_aes_256_gcm();
            break;
        default:
            return -1;
    }
    
    if (EVP_DecryptInit_ex(ctx->ctx, cipher, NULL, ctx->key, 
                          ctx->mode == 0 ? NULL : ctx->iv) != 1) {
        return -1;
    }
    
    int len;
    int plaintext_len = 0;
    
    if (EVP_DecryptUpdate(ctx->ctx, plaintext, &len, ciphertext, ciphertext_length) != 1) {
        return -1;
    }
    
    plaintext_len += len;
    
    if (EVP_DecryptFinal_ex(ctx->ctx, plaintext + len, &len) != 1) {
        return -1;
    }
    
    plaintext_len += len;
    *plaintext_length = plaintext_len;
    
    return 0;
}
```

**AES Benefits**:
- **Strong Encryption**: Industry-standard symmetric encryption
- **Multiple Modes**: ECB, CBC, GCM for different use cases
- **Key Sizes**: 128, 192, 256-bit keys for different security levels
- **Performance**: Fast encryption and decryption

## 🔑 Asymmetric Encryption (RSA)

### RSA Key Generation
```c
// Generate RSA key pair
RSA* generateRSAKeyPair(int bits) {
    RSA* rsa = RSA_new();
    if (!rsa) {
        return NULL;
    }
    
    BIGNUM* e = BN_new();
    if (!e) {
        RSA_free(rsa);
        return NULL;
    }
    
    BN_set_word(e, RSA_F4); // 65537
    
    if (RSA_generate_key_ex(rsa, bits, e, NULL) != 1) {
        BN_free(e);
        RSA_free(rsa);
        return NULL;
    }
    
    BN_free(e);
    return rsa;
}
```

### RSA Encryption and Decryption
```c
// RSA encryption
int rsaEncrypt(RSA* rsa, const unsigned char* plaintext, size_t plaintext_length,
               unsigned char* ciphertext, size_t* ciphertext_length) {
    if (!rsa || !plaintext || !ciphertext || !ciphertext_length) {
        return -1;
    }
    
    int result = RSA_public_encrypt(plaintext_length, plaintext, ciphertext, 
                                  rsa, RSA_PKCS1_OAEP_PADDING);
    
    if (result < 0) {
        return -1;
    }
    
    *ciphertext_length = result;
    return 0;
}

// RSA decryption
int rsaDecrypt(RSA* rsa, const unsigned char* ciphertext, size_t ciphertext_length,
               unsigned char* plaintext, size_t* plaintext_length) {
    if (!rsa || !ciphertext || !plaintext || !plaintext_length) {
        return -1;
    }
    
    int result = RSA_private_decrypt(ciphertext_length, ciphertext, plaintext, 
                                    rsa, RSA_PKCS1_OAEP_PADDING);
    
    if (result < 0) {
        return -1;
    }
    
    *plaintext_length = result;
    return 0;
}
```

**RSA Benefits**:
- **Asymmetric Encryption**: Different keys for encryption and decryption
- **Key Exchange**: Securely exchange symmetric keys
- **Digital Signatures**: Sign and verify messages
- **Key Sizes**: 1024, 2048, 4096-bit keys for different security levels

## 📝 Digital Signatures

### Digital Signature Creation
```c
// Digital signature structure
typedef struct {
    unsigned char signature[MAX_SIGNATURE_SIZE];
    size_t signature_length;
    int algorithm;
} DigitalSignature;

// Create digital signature
DigitalSignature* createDigitalSignature(const unsigned char* data, size_t data_length,
                                        EVP_PKEY* private_key) {
    if (!data || !private_key || data_length == 0) {
        return NULL;
    }
    
    DigitalSignature* sig = malloc(sizeof(DigitalSignature));
    if (!sig) {
        return NULL;
    }
    
    EVP_MD_CTX* md_ctx = EVP_MD_CTX_new();
    if (!md_ctx) {
        free(sig);
        return NULL;
    }
    
    if (EVP_DigestSignInit(md_ctx, NULL, EVP_sha256(), NULL, private_key) != 1) {
        EVP_MD_CTX_free(md_ctx);
        free(sig);
        return NULL;
    }
    
    if (EVP_DigestSignUpdate(md_ctx, data, data_length) != 1) {
        EVP_MD_CTX_free(md_ctx);
        free(sig);
        return NULL;
    }
    
    if (EVP_DigestSignFinal(md_ctx, NULL, &sig->signature_length) != 1) {
        EVP_MD_CTX_free(md_ctx);
        free(sig);
        return NULL;
    }
    
    sig->signature = malloc(sig->signature_length);
    if (!sig->signature) {
        EVP_MD_CTX_free(md_ctx);
        free(sig);
        return NULL;
    }
    
    if (EVP_DigestSignFinal(md_ctx, sig->signature, &sig->signature_length) != 1) {
        free(sig->signature);
        EVP_MD_CTX_free(md_ctx);
        free(sig);
        return NULL;
    }
    
    sig->algorithm = EVP_sha256();
    EVP_MD_CTX_free(md_ctx);
    
    return sig;
}
```

### Digital Signature Verification
```c
// Verify digital signature
int verifyDigitalSignature(const unsigned char* data, size_t data_length,
                          const DigitalSignature* signature, EVP_PKEY* public_key) {
    if (!data || !signature || !public_key || data_length == 0) {
        return -1;
    }
    
    EVP_MD_CTX* md_ctx = EVP_MD_CTX_new();
    if (!md_ctx) {
        return -1;
    }
    
    if (EVP_DigestVerifyInit(md_ctx, NULL, EVP_sha256(), NULL, public_key) != 1) {
        EVP_MD_CTX_free(md_ctx);
        return -1;
    }
    
    if (EVP_DigestVerifyUpdate(md_ctx, data, data_length) != 1) {
        EVP_MD_CTX_free(md_ctx);
        return -1;
    }
    
    int result = EVP_DigestVerifyFinal(md_ctx, signature->signature, 
                                       signature->signature_length);
    
    EVP_MD_CTX_free(md_ctx);
    
    return result == 1 ? 0 : -1;
}
```

**Digital Signature Benefits**:
- **Authentication**: Verifies the identity of the signer
- **Integrity**: Ensures the message hasn't been tampered with
- **Non-repudiation**: Prevents the signer from denying the signature
- **Algorithm Support**: SHA-256, SHA-384, SHA-512 algorithms

## 📜 Certificate Management

### Certificate Structure
```c
// Certificate structure
typedef struct {
    X509* cert;
    EVP_PKEY* private_key;
    EVP_PKEY* public_key;
    char subject[256];
    char issuer[256];
    time_t not_before;
    time_t not_after;
} Certificate;
```

### Self-Signed Certificate Generation
```c
// Generate self-signed certificate
Certificate* generateSelfSignedCertificate(const char* subject, int key_size) {
    if (!subject || key_size < 1024) {
        return NULL;
    }
    
    Certificate* cert = malloc(sizeof(Certificate));
    if (!cert) {
        return NULL;
    }
    
    // Generate key pair
    cert->private_key = EVP_PKEY_new();
    if (!cert->private_key) {
        free(cert);
        return NULL;
    }
    
    RSA* rsa = generateRSAKeyPair(key_size);
    if (!rsa) {
        EVP_PKEY_free(cert->private_key);
        free(cert);
        return NULL;
    }
    
    if (EVP_PKEY_assign_RSA(cert->private_key, rsa) != 1) {
        RSA_free(rsa);
        EVP_PKEY_free(cert->private_key);
        free(cert);
        return NULL;
    }
    
    // Create certificate
    cert->cert = X509_new();
    if (!cert->cert) {
        EVP_PKEY_free(cert->private_key);
        free(cert);
        return NULL;
    }
    
    // Set version, serial, subject, issuer, validity, etc.
    // ... (implementation details in full code)
    
    return cert;
}
```

**Certificate Benefits**:
- **Trust Management**: Establish trust relationships
- **Identity Verification**: Verify entity identities
- **Key Binding**: Bind public keys to identities
- **X.509 Standard**: Industry-standard certificate format

## 🔐 Password Security

### Password Hashing with Salt
```c
// Password hashing with salt
int hashPassword(const char* password, char* hash, size_t hash_size) {
    if (!password || !hash || hash_size < 65) { // 64 chars + null terminator
        return -1;
    }
    
    unsigned char salt[MAX_SALT_SIZE];
    if (generateRandomBytes(salt, 16) != 0) { // 16 bytes salt
        return -1;
    }
    
    unsigned char derived_key[32]; // 256 bits
    if (deriveKeyPBKDF2(password, salt, 16, 10000, 32, derived_key) != 0) {
        return -1;
    }
    
    // Format: $pbkdf2$iterations$salt$hash
    snprintf(hash, hash_size, "$pbkdf2$10000$%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x%02x",
             salt[0], salt[1], salt[2], salt[3], salt[4], salt[5], salt[6], salt[7],
             salt[8], salt[9], salt[10], salt[11], salt[12], salt[13], salt[14], salt[15]);
    
    // Add hash
    char* ptr = hash + strlen(hash);
    for (int i = 0; i < 32; i++) {
        ptr += snprintf(ptr, hash_size - (ptr - hash), "%02x", derived_key[i]);
    }
    
    return 0;
}

// Verify password
int verifyPassword(const char* password, const char* hash) {
    if (!password || !hash) {
        return -1;
    }
    
    // Parse hash format: $pbkdf2$iterations$salt$hash
    // Extract salt and hash, then recompute and compare
    // ... (implementation details in full code)
    
    return memcmp(derived_key, stored_hash, 32) == 0 ? 0 : -1;
}
```

**Password Security Benefits**:
- **Salted Hashing**: Prevents rainbow table attacks
- **Key Stretching**: Slows down brute force attacks
- **Strong Algorithms**: Uses PBKDF2 with SHA-256
- **Secure Storage**: Never store plaintext passwords

## 🔐 Secure Communication

### Secure Session Management
```c
// Secure session structure
typedef struct {
    unsigned char session_key[MAX_KEY_SIZE];
    unsigned char iv[MAX_IV_SIZE];
    time_t created;
    int expires_in;
} SecureSession;

// Create secure session
SecureSession* createSecureSession() {
    SecureSession* session = malloc(sizeof(SecureSession));
    if (!session) {
        return NULL;
    }
    
    // Generate random session key
    if (generateRandomBytes(session->session_key, 32) != 0) {
        free(session);
        return NULL;
    }
    
    // Generate random IV
    if (generateRandomBytes(session->iv, MAX_IV_SIZE) != 0) {
        free(session);
        return NULL;
    }
    
    session->created = time(NULL);
    session->expires_in = 3600; // 1 hour
    
    return session;
}

// Encrypt message in session
int encryptInSession(SecureSession* session, const char* message, 
                    unsigned char* ciphertext, size_t* ciphertext_length) {
    if (!session || !message || !ciphertext || !ciphertext_length) {
        return -1;
    }
    
    AESContext* ctx = initAESEncryption(session->session_key, 32, session->iv, 1); // CBC mode
    if (!ctx) {
        return -1;
    }
    
    int result = aesEncrypt(ctx, (unsigned char*)message, strlen(message), 
                          ciphertext, ciphertext_length);
    
    freeAESContext(ctx);
    return result;
}
```

**Secure Communication Benefits**:
- **Session Keys**: Temporary keys for each session
- **Perfect Forward Secrecy**: Compromise of long-term keys doesn't affect past sessions
- **Message Encryption**: Protects data in transit
- **Key Management**: Secure key generation and rotation

## 📊 Security Auditing

### Security Event Logging
```c
// Security event structure
typedef struct {
    time_t timestamp;
    char event_type[64];
    char source[64];
    char description[256];
    int severity; // 1=Low, 2=Medium, 3=High, 4=Critical
} SecurityEvent;

// Security audit log
typedef struct {
    SecurityEvent events[1000];
    int event_count;
    pthread_mutex_t mutex;
} SecurityAuditLog;

// Log security event
void logSecurityEvent(SecurityAuditLog* log, const char* event_type, const char* source,
                      const char* description, int severity) {
    pthread_mutex_lock(&log->mutex);
    
    if (log->event_count < 1000) {
        SecurityEvent* event = &log->events[log->event_count];
        
        event->timestamp = time(NULL);
        strncpy(event->event_type, event_type, sizeof(event->event_type) - 1);
        strncpy(event->source, source, sizeof(event->source) - 1);
        strncpy(event->description, description, sizeof(event->description) - 1);
        event->severity = severity;
        
        log->event_count++;
    }
    
    pthread_mutex_unlock(&log->mutex);
}
```

**Security Auditing Benefits**:
- **Event Tracking**: Log all security-relevant events
- **Compliance**: Meet regulatory requirements
- **Forensics**: Investigate security incidents
- **Monitoring**: Real-time security monitoring

## 🔧 Best Practices

### 1. Use Established Libraries
```c
// Good: Use OpenSSL for cryptography
#include <openssl/evp.h>
#include <openssl/rand.h>
#include <openssl/sha.h>

// Bad: Implement your own cryptography
void myOwnHashFunction() {
    // Don't do this! Use established libraries
}
```

### 2. Proper Key Management
```c
// Good: Secure key storage
void storeKeySecurely(const unsigned char* key, size_t key_length) {
    // Use hardware security module if available
    // Or encrypt the key with a master key
    // Never store keys in plaintext
}

// Bad: Insecure key storage
void storeKeyInsecurely(const unsigned char* key, size_t key_length) {
    FILE* file = fopen("keys.txt", "w");
    fprintf(file, "%s", key); // Never do this!
    fclose(file);
}
```

### 3. Error Handling
```c
// Good: Comprehensive error checking
int secureOperation() {
    if (initializeCrypto() != 0) {
        return -1;
    }
    
    if (performOperation() != 0) {
        cleanupCrypto();
        return -1;
    }
    
    cleanupCrypto();
    return 0;
}

// Bad: No error checking
int insecureOperation() {
    initializeCrypto(); // Assume success
    performOperation(); // Assume success
    cleanupCrypto();
    return 0;
}
```

### 4. Memory Management
```c
// Good: Secure memory handling
void handleSensitiveData() {
    char* sensitive_data = secureMalloc(256);
    if (sensitive_data) {
        // Use sensitive data
        secureFree(sensitive_data, 256);
    }
}

// Bad: Insecure memory handling
void handleSensitiveDataInsecurely() {
    char sensitive_data[256];
    // Use sensitive data
    // Data remains in memory after function returns
}
```

### 5. Random Number Generation
```c
// Good: Use cryptographically secure random numbers
unsigned char key[32];
if (generateRandomBytes(key, 32) != 0) {
    // Handle error
}

// Bad: Use predictable random numbers
srand(time(NULL));
unsigned char key[32];
for (int i = 0; i < 32; i++) {
    key[i] = rand() % 256; // Predictable!
}
```

## ⚠️ Common Pitfalls

### 1. Rolling Your Own Cryptography
```c
// Wrong: Custom hash function
unsigned int myHash(const char* str) {
    unsigned int hash = 5381;
    int c;
    while ((c = *str++)) {
        hash = ((hash << 5) + hash) + c; // Not cryptographically secure
    }
    return hash;
}

// Right: Use established hash functions
unsigned char hash[32];
SHA256((unsigned char*)str, strlen(str), hash);
```

### 2. Weak Password Storage
```c
// Wrong: Plain text passwords
void storePassword(const char* username, const char* password) {
    FILE* file = fopen("passwords.txt", "a");
    fprintf(file, "%s:%s\n", username, password); // Security disaster!
    fclose(file);
}

// Right: Salted and hashed passwords
void storePasswordSecurely(const char* username, const char* password) {
    char hash[256];
    hashPassword(password, hash, sizeof(hash));
    // Store only the hash, not the password
}
```

### 3. Key Hardcoding
```c
// Wrong: Hardcoded keys
unsigned char encryption_key[32] = {0x01, 0x23, 0x45, 0x67, ...}; // Never do this!

// Right: Load keys from secure storage
unsigned char encryption_key[32];
loadKeyFromSecureStorage(encryption_key, 32);
```

### 4. Ignoring Certificate Validation
```c
// Wrong: Skip certificate validation
int connectToServer(const char* hostname) {
    SSL* ssl = SSL_new(ctx);
    SSL_set_fd(ssl, socket);
    SSL_connect(ssl); // Don't check certificate!
    return 0;
}

// Right: Validate certificates
int connectToServerSecurely(const char* hostname) {
    SSL* ssl = SSL_new(ctx);
    SSL_set_fd(ssl, socket);
    
    if (SSL_connect(ssl) == 1) {
        X509* cert = SSL_get_peer_certificate(ssl);
        if (cert && verifyCertificate(cert) == 0) {
            return 0; // Success
        }
    }
    
    return -1; // Failed
}
```

## 🔧 Real-World Applications

### 1. Secure File Storage
```c
// Encrypt file before storage
int encryptAndStoreFile(const char* filename, const char* data, size_t data_length) {
    // Generate random key and IV
    unsigned char key[32], iv[16];
    generateRandomBytes(key, 32);
    generateRandomBytes(iv, 16);
    
    // Encrypt data
    unsigned char* ciphertext = malloc(data_length + 256);
    size_t ciphertext_length;
    
    AESContext* ctx = initAESEncryption(key, 32, iv, 1);
    if (aesEncrypt(ctx, (unsigned char*)data, data_length, 
                  ciphertext, &ciphertext_length) == 0) {
        // Store encrypted data with key and IV
        FILE* file = fopen(filename, "wb");
        fwrite(key, 1, 32, file);
        fwrite(iv, 1, 16, file);
        fwrite(ciphertext, 1, ciphertext_length, file);
        fclose(file);
    }
    
    freeAESContext(ctx);
    free(ciphertext);
    return 0;
}
```

### 2. Secure Network Communication
```c
// Send encrypted message over network
int sendSecureMessage(int socket, const char* message, EVP_PKEY* peer_public_key) {
    // Generate session key
    unsigned char session_key[32];
    generateRandomBytes(session_key, 32);
    
    // Encrypt session key with peer's public key
    unsigned char encrypted_key[256];
    size_t encrypted_key_length;
    
    RSA* rsa = EVP_PKEY_get1_RSA(peer_public_key);
    if (rsaEncrypt(rsa, session_key, 32, encrypted_key, &encrypted_key_length) == 0) {
        // Send encrypted key
        send(socket, encrypted_key, encrypted_key_length, 0);
        
        // Encrypt message with session key
        unsigned char* ciphertext = malloc(strlen(message) + 256);
        size_t ciphertext_length;
        
        AESContext* ctx = initAESEncryption(session_key, 32, NULL, 1);
        if (aesEncrypt(ctx, (unsigned char*)message, strlen(message),
                      ciphertext, &ciphertext_length) == 0) {
            // Send encrypted message
            send(socket, ciphertext, ciphertext_length, 0);
        }
        
        freeAESContext(ctx);
        free(ciphertext);
    }
    
    RSA_free(rsa);
    return 0;
}
```

### 3. Authentication System
```c
// User authentication with password hashing
int authenticateUser(const char* username, const char* password) {
    // Get stored hash from database
    char stored_hash[256];
    if (getUserPasswordHash(username, stored_hash, sizeof(stored_hash)) != 0) {
        return -1; // User not found
    }
    
    // Verify password
    if (verifyPassword(password, stored_hash) == 0) {
        // Generate session token
        char session_token[64];
        generateRandomString(session_token, 64, 
                           "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
        
        // Store session token
        storeSessionToken(username, session_token);
        return 0; // Authentication successful
    }
    
    return -1; // Authentication failed
}
```

## 📚 Further Reading

### Books
- "Applied Cryptography" by Bruce Schneier
- "Cryptography Engineering" by Niels Ferguson, Bruce Schneier, and Tadayoshi Kohno
- "Practical Cryptography for Developers" by David Wong

### Topics
- Post-quantum cryptography
- Zero-knowledge proofs
- Homomorphic encryption
- Secure multi-party computation
- Blockchain and cryptocurrency

Advanced security and cryptography in C requires understanding of cryptographic principles, proper implementation techniques, and security best practices. Master these techniques to build secure, robust applications that protect sensitive data and maintain system integrity!
