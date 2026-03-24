#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <unistd.h>
#include <openssl/evp.h>
#include <openssl/rand.h>
#include <openssl/sha.h>
#include <openssl/aes.h>
#include <openssl/rsa.h>
#include <openssl/pem.h>
#include <openssl/x509.h>
#include <openssl/err.h>

// =============================================================================
// ADVANCED SECURITY AND CRYPTOGRAPHY
// =============================================================================

#define MAX_KEY_SIZE 256
#define MAX_IV_SIZE 16
#define MAX_SALT_SIZE 32
#define MAX_HASH_SIZE 64
#define MAX_SIGNATURE_SIZE 512
#define BUFFER_SIZE 4096

// =============================================================================
// CRYPTOGRAPHIC PRIMITIVES
// =============================================================================

// Key derivation structure
typedef struct {
    unsigned char key[MAX_KEY_SIZE];
    unsigned char salt[MAX_SALT_SIZE];
    int iterations;
    int key_length;
} DerivedKey;

// Digital signature structure
typedef struct {
    unsigned char signature[MAX_SIGNATURE_SIZE];
    size_t signature_length;
    int algorithm;
} DigitalSignature;

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

// =============================================================================
// SECURE MEMORY MANAGEMENT
// =============================================================================

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

// =============================================================================
// RANDOM NUMBER GENERATION
// =============================================================================

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

// =============================================================================
// HASH FUNCTIONS
// =============================================================================

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

// =============================================================================
// KEY DERIVATION
// =============================================================================

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

// Free derived key
void freeDerivedKey(DerivedKey* dk) {
    if (dk) {
        secureFree(dk->key, dk->key_length);
        secureFree(dk->salt, MAX_SALT_SIZE);
        free(dk);
    }
}

// =============================================================================
// SYMMETRIC ENCRYPTION (AES)
// =============================================================================

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

// Free AES context
void freeAESContext(AESContext* ctx) {
    if (ctx) {
        EVP_CIPHER_CTX_free(ctx->ctx);
        secureFree(ctx->key, ctx->key_length);
        secureFree(ctx->iv, MAX_IV_SIZE);
        free(ctx);
    }
}

// =============================================================================
// ASYMMETRIC ENCRYPTION (RSA)
// =============================================================================

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

// =============================================================================
// DIGITAL SIGNATURES
// =============================================================================

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

// Free digital signature
void freeDigitalSignature(DigitalSignature* signature) {
    if (signature) {
        secureFree(signature->signature, signature->signature_length);
        free(signature);
    }
}

// =============================================================================
// CERTIFICATE MANAGEMENT
// =============================================================================

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
    
    // Set version
    if (X509_set_version(cert->cert, 2) != 1) {
        X509_free(cert->cert);
        EVP_PKEY_free(cert->private_key);
        free(cert);
        return NULL;
    }
    
    // Set serial number
    ASN1_INTEGER* serial = ASN1_INTEGER_new();
    if (!serial) {
        X509_free(cert->cert);
        EVP_PKEY_free(cert->private_key);
        free(cert);
        return NULL;
    }
    
    ASN1_INTEGER_set(serial, 1);
    X509_set_serialNumber(cert->cert, serial);
    ASN1_INTEGER_free(serial);
    
    // Set subject and issuer
    X509_NAME* name = X509_NAME_new();
    if (!name) {
        X509_free(cert->cert);
        EVP_PKEY_free(cert->private_key);
        free(cert);
        return NULL;
    }
    
    X509_NAME_add_entry_by_txt(name, "CN", MBSTRING_ASC, 
                               (unsigned char*)subject, -1, -1, 0);
    
    X509_set_subject_name(cert->cert, name);
    X509_set_issuer_name(cert->cert, name); // Self-signed
    X509_NAME_free(name);
    
    // Set validity period
    X509_gmtime_adj(X509_get_notBefore(cert->cert), 0); // Now
    X509_gmtime_adj(X509_get_notAfter(cert->cert), 365 * 24 * 3600); // 1 year
    
    // Set public key
    X509_set_pubkey(cert->cert, cert->private_key);
    
    // Sign certificate
    if (X509_sign(cert->cert, cert->private_key, EVP_sha256()) != 1) {
        X509_free(cert->cert);
        EVP_PKEY_free(cert->private_key);
        free(cert);
        return NULL;
    }
    
    // Copy public key
    cert->public_key = EVP_PKEY_new();
    EVP_PKEY_copy(cert->public_key, cert->private_key);
    
    // Store subject and issuer
    strncpy(cert->subject, subject, sizeof(cert->subject) - 1);
    strncpy(cert->issuer, subject, sizeof(cert->issuer) - 1);
    
    cert->not_before = time(NULL);
    cert->not_after = cert->not_before + (365 * 24 * 3600);
    
    return cert;
}

// Verify certificate
int verifyCertificate(Certificate* cert) {
    if (!cert || !cert->cert || !cert->public_key) {
        return -1;
    }
    
    int result = X509_verify(cert->cert, cert->public_key);
    return result == 1 ? 0 : -1;
}

// Free certificate
void freeCertificate(Certificate* cert) {
    if (cert) {
        if (cert->cert) X509_free(cert->cert);
        if (cert->private_key) EVP_PKEY_free(cert->private_key);
        if (cert->public_key) EVP_PKEY_free(cert->public_key);
        free(cert);
    }
}

// =============================================================================
// PASSWORD SECURITY
// =============================================================================

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
    char hash_copy[256];
    strncpy(hash_copy, hash, sizeof(hash_copy) - 1);
    hash_copy[sizeof(hash_copy) - 1] = '\0';
    
    char* parts[4];
    parts[0] = strtok(hash_copy, "$");
    parts[1] = strtok(NULL, "$");
    parts[2] = strtok(NULL, "$");
    parts[3] = strtok(NULL, "$");
    
    if (!parts[0] || !parts[1] || !parts[2] || !parts[3]) {
        return -1;
    }
    
    // Convert salt from hex
    unsigned char salt[16];
    for (int i = 0; i < 16; i++) {
        char hex_byte[3] = {parts[2][i*2], parts[2][i*2+1], '\0'};
        salt[i] = (unsigned char)strtol(hex_byte, NULL, 16);
    }
    
    // Derive key
    unsigned char derived_key[32];
    int iterations = atoi(parts[1]);
    if (deriveKeyPBKDF2(password, salt, 16, iterations, 32, derived_key) != 0) {
        return -1;
    }
    
    // Convert stored hash to bytes
    unsigned char stored_hash[32];
    for (int i = 0; i < 32; i++) {
        char hex_byte[3] = {parts[3][i*2], parts[3][i*2+1], '\0'};
        stored_hash[i] = (unsigned char)strtol(hex_byte, NULL, 16);
    }
    
    // Compare
    return memcmp(derived_key, stored_hash, 32) == 0 ? 0 : -1;
}

// =============================================================================
// SECURE COMMUNICATION
// =============================================================================

// Secure session key exchange
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

// Decrypt message in session
int decryptInSession(SecureSession* session, const unsigned char* ciphertext, 
                    size_t ciphertext_length, char* plaintext, size_t* plaintext_length) {
    if (!session || !ciphertext || !plaintext || !plaintext_length) {
        return -1;
    }
    
    AESContext* ctx = initAESEncryption(session->session_key, 32, session->iv, 1); // CBC mode
    if (!ctx) {
        return -1;
    }
    
    int result = aesDecrypt(ctx, ciphertext, ciphertext_length, 
                          (unsigned char*)plaintext, plaintext_length);
    
    freeAESContext(ctx);
    return result;
}

// Free secure session
void freeSecureSession(SecureSession* session) {
    if (session) {
        secureFree(session->session_key, MAX_KEY_SIZE);
        secureFree(session->iv, MAX_IV_SIZE);
        free(session);
    }
}

// =============================================================================
// SECURITY AUDITING
// =============================================================================

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

// Initialize audit log
void initAuditLog(SecurityAuditLog* log) {
    memset(log, 0, sizeof(SecurityAuditLog));
    pthread_mutex_init(&log->mutex, NULL);
}

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

// Print audit log
void printAuditLog(SecurityAuditLog* log) {
    pthread_mutex_lock(&log->mutex);
    
    printf("=== SECURITY AUDIT LOG ===\n");
    printf("Total events: %d\n\n", log->event_count);
    
    for (int i = 0; i < log->event_count; i++) {
        SecurityEvent* event = &log->events[i];
        char timestamp[64];
        strftime(timestamp, sizeof(timestamp), "%Y-%m-%d %H:%M:%S", localtime(&event->timestamp));
        
        printf("[%s] %s - %s (Severity: %d)\n", timestamp, event->event_type, 
               event->description, event->severity);
        printf("  Source: %s\n\n", event->source);
    }
    
    pthread_mutex_unlock(&log->mutex);
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateSecureMemory() {
    printf("=== SECURE MEMORY DEMO ===\n");
    
    char* sensitive_data = (char*)secureMalloc(256);
    if (sensitive_data) {
        secureStrcpy(sensitive_data, "This is sensitive data that should be securely handled", 256);
        printf("Sensitive data: %s\n", sensitive_data);
        
        // Clear and free
        secureFree(sensitive_data, 256);
        printf("Sensitive data securely cleared and freed\n");
    }
}

void demonstrateRandomGeneration() {
    printf("\n=== RANDOM GENERATION DEMO ===\n");
    
    // Generate random bytes
    unsigned char random_bytes[32];
    if (generateRandomBytes(random_bytes, 32) == 0) {
        printf("Random bytes: ");
        for (int i = 0; i < 32; i++) {
            printf("%02x", random_bytes[i]);
        }
        printf("\n");
    }
    
    // Generate random integer
    int random_int = generateRandomInt(1, 100);
    printf("Random integer (1-100): %d\n", random_int);
    
    // Generate random password
    char password[17];
    const char* charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    if (generateRandomString(password, 17, charset) == 0) {
        printf("Random password: %s\n", password);
    }
}

void demonstrateHashing() {
    printf("\n=== HASHING DEMO ===\n");
    
    const char* message = "Hello, World!";
    unsigned char hash_sha256[32];
    unsigned char hash_sha512[64];
    
    // SHA-256
    if (computeSHA256((unsigned char*)message, strlen(message), hash_sha256) == 0) {
        printf("SHA-256 of '%s': ", message);
        for (int i = 0; i < 32; i++) {
            printf("%02x", hash_sha256[i]);
        }
        printf("\n");
    }
    
    // SHA-512
    if (computeSHA512((unsigned char*)message, strlen(message), hash_sha512) == 0) {
        printf("SHA-512 of '%s': ", message);
        for (int i = 0; i < 64; i++) {
            printf("%02x", hash_sha512[i]);
        }
        printf("\n");
    }
    
    // HMAC
    const char* key = "secret_key";
    unsigned char hmac[32];
    if (computeHMACSHA256((unsigned char*)message, strlen(message),
                         (unsigned char*)key, strlen(key), hmac) == 0) {
        printf("HMAC-SHA256 of '%s': ", message);
        for (int i = 0; i < 32; i++) {
            printf("%02x", hmac[i]);
        }
        printf("\n");
    }
}

void demonstrateKeyDerivation() {
    printf("\n=== KEY DERIVATION DEMO ===\n");
    
    const char* password = "my_secure_password";
    
    DerivedKey* dk = createDerivedKey(password, 32, 10000);
    if (dk) {
        printf("Derived key (32 bytes): ");
        for (int i = 0; i < dk->key_length; i++) {
            printf("%02x", dk->key[i]);
        }
        printf("\n");
        
        printf("Salt (16 bytes): ");
        for (int i = 0; i < 16; i++) {
            printf("%02x", dk->salt[i]);
        }
        printf("\n");
        
        printf("Iterations: %d\n", dk->iterations);
        
        freeDerivedKey(dk);
    }
}

void demonstrateAESEncryption() {
    printf("\n=== AES ENCRYPTION DEMO ===\n");
    
    const char* plaintext = "This is a secret message that will be encrypted using AES.";
    unsigned char key[32] = "01234567890123456789012345678901"; // 256-bit key
    unsigned char iv[16] = "1234567890123456"; // 128-bit IV
    
    // Encrypt
    AESContext* encrypt_ctx = initAESEncryption(key, 32, iv, 1); // CBC mode
    if (encrypt_ctx) {
        unsigned char ciphertext[256];
        size_t ciphertext_length;
        
        if (aesEncrypt(encrypt_ctx, (unsigned char*)plaintext, strlen(plaintext),
                      ciphertext, &ciphertext_length) == 0) {
            printf("Original: %s\n", plaintext);
            printf("Encrypted (%zu bytes): ", ciphertext_length);
            for (size_t i = 0; i < ciphertext_length; i++) {
                printf("%02x", ciphertext[i]);
            }
            printf("\n");
            
            // Decrypt
            AESContext* decrypt_ctx = initAESEncryption(key, 32, iv, 1);
            if (decrypt_ctx) {
                unsigned char decrypted[256];
                size_t decrypted_length;
                
                if (aesDecrypt(decrypt_ctx, ciphertext, ciphertext_length,
                              decrypted, &decrypted_length) == 0) {
                    decrypted[decrypted_length] = '\0';
                    printf("Decrypted: %s\n", decrypted);
                }
                
                freeAESContext(decrypt_ctx);
            }
        }
        
        freeAESContext(encrypt_ctx);
    }
}

void demonstrateRSAEncryption() {
    printf("\n=== RSA ENCRYPTION DEMO ===\n");
    
    // Generate RSA key pair
    RSA* rsa = generateRSAKeyPair(2048);
    if (rsa) {
        const char* message = "RSA encryption test message";
        unsigned char ciphertext[256];
        size_t ciphertext_length;
        
        // Encrypt
        if (rsaEncrypt(rsa, (unsigned char*)message, strlen(message),
                      ciphertext, &ciphertext_length) == 0) {
            printf("Original: %s\n", message);
            printf("Encrypted (%zu bytes): ", ciphertext_length);
            for (size_t i = 0; i < ciphertext_length; i++) {
                printf("%02x", ciphertext[i]);
            }
            printf("\n");
            
            // Decrypt
            unsigned char decrypted[256];
            size_t decrypted_length;
            
            if (rsaDecrypt(rsa, ciphertext, ciphertext_length,
                          decrypted, &decrypted_length) == 0) {
                decrypted[decrypted_length] = '\0';
                printf("Decrypted: %s\n", decrypted);
            }
        }
        
        RSA_free(rsa);
    }
}

void demonstrateDigitalSignatures() {
    printf("\n=== DIGITAL SIGNATURES DEMO ===\n");
    
    // Generate RSA key pair
    RSA* rsa = generateRSAKeyPair(2048);
    if (rsa) {
        EVP_PKEY* private_key = EVP_PKEY_new();
        EVP_PKEY* public_key = EVP_PKEY_new();
        
        if (EVP_PKEY_assign_RSA(private_key, rsa) == 1) {
            EVP_PKEY_copy(public_key, private_key);
            
            const char* message = "This message will be digitally signed";
            
            // Create signature
            DigitalSignature* sig = createDigitalSignature((unsigned char*)message, 
                                                           strlen(message), private_key);
            if (sig) {
                printf("Original: %s\n", message);
                printf("Signature (%zu bytes): ", sig->signature_length);
                for (size_t i = 0; i < sig->signature_length; i++) {
                    printf("%02x", sig->signature[i]);
                }
                printf("\n");
                
                // Verify signature
                if (verifyDigitalSignature((unsigned char*)message, strlen(message),
                                           sig, public_key) == 0) {
                    printf("Signature verification: SUCCESS\n");
                } else {
                    printf("Signature verification: FAILED\n");
                }
                
                freeDigitalSignature(sig);
            }
        }
        
        EVP_PKEY_free(private_key);
        EVP_PKEY_free(public_key);
    }
}

void demonstrateCertificates() {
    printf("\n=== CERTIFICATES DEMO ===\n");
    
    Certificate* cert = generateSelfSignedCertificate("Test Certificate", 2048);
    if (cert) {
        printf("Certificate generated successfully!\n");
        printf("Subject: %s\n", cert->subject);
        printf("Issuer: %s\n", cert->issuer);
        printf("Valid from: %s", ctime(&cert->not_before));
        printf("Valid until: %s", ctime(&cert->not_after));
        
        // Verify certificate
        if (verifyCertificate(cert) == 0) {
            printf("Certificate verification: SUCCESS\n");
        } else {
            printf("Certificate verification: FAILED\n");
        }
        
        freeCertificate(cert);
    }
}

void demonstratePasswordSecurity() {
    printf("\n=== PASSWORD SECURITY DEMO ===\n");
    
    const char* password = "my_secure_password_123";
    char hash[256];
    
    // Hash password
    if (hashPassword(password, hash, sizeof(hash)) == 0) {
        printf("Password: %s\n", password);
        printf("Hash: %s\n", hash);
        
        // Verify password
        if (verifyPassword(password, hash) == 0) {
            printf("Password verification: SUCCESS\n");
        } else {
            printf("Password verification: FAILED\n");
        }
        
        // Try wrong password
        if (verifyPassword("wrong_password", hash) == -1) {
            printf("Wrong password verification: FAILED (as expected)\n");
        }
    }
}

void demonstrateSecureCommunication() {
    printf("\n=== SECURE COMMUNICATION DEMO ===\n");
    
    SecureSession* session = createSecureSession();
    if (session) {
        const char* message = "This is a secure message";
        unsigned char ciphertext[1024];
        size_t ciphertext_length;
        
        // Encrypt in session
        if (encryptInSession(session, message, ciphertext, &ciphertext_length) == 0) {
            printf("Original: %s\n", message);
            printf("Encrypted (%zu bytes): ", ciphertext_length);
            for (size_t i = 0; i < ciphertext_length; i++) {
                printf("%02x", ciphertext[i]);
            }
            printf("\n");
            
            // Decrypt in session
            char plaintext[1024];
            size_t plaintext_length;
            
            if (decryptInSession(session, ciphertext, ciphertext_length,
                                plaintext, &plaintext_length) == 0) {
                plaintext[plaintext_length] = '\0';
                printf("Decrypted: %s\n", plaintext);
            }
        }
        
        freeSecureSession(session);
    }
}

void demonstrateSecurityAuditing() {
    printf("\n=== SECURITY AUDITING DEMO ===\n");
    
    SecurityAuditLog audit_log;
    initAuditLog(&audit_log);
    
    // Log some security events
    logSecurityEvent(&audit_log, "LOGIN", "Web Interface", 
                    "User admin logged in successfully", 1);
    logSecurityEvent(&audit_log, "LOGIN_FAILED", "Web Interface", 
                    "Failed login attempt for user admin", 2);
    logSecurityEvent(&audit_log, "ENCRYPTION", "Database", 
                    "Sensitive data encrypted", 1);
    logSecurityEvent(&audit_log, "SUSPICIOUS_ACTIVITY", "API", 
                    "Multiple failed login attempts detected", 3);
    
    // Print audit log
    printAuditLog(&audit_log);
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Security and Cryptography Examples\n");
    printf("==========================================\n\n");
    
    // Initialize OpenSSL
    OpenSSL_add_all_algorithms();
    ERR_load_crypto_strings();
    
    // Run all demonstrations
    demonstrateSecureMemory();
    demonstrateRandomGeneration();
    demonstrateHashing();
    demonstrateKeyDerivation();
    demonstrateAESEncryption();
    demonstrateRSAEncryption();
    demonstrateDigitalSignatures();
    demonstrateCertificates();
    demonstratePasswordSecurity();
    demonstrateSecureCommunication();
    demonstrateSecurityAuditing();
    
    // Cleanup OpenSSL
    EVP_cleanup();
    ERR_free_strings();
    
    printf("\nAll advanced security examples demonstrated!\n");
    printf("Key takeaways:\n");
    printf("- Secure memory management prevents data leakage\n");
    printf("- Cryptographically secure random number generation\n");
    printf("- Strong hashing algorithms (SHA-256, SHA-512) for integrity\n");
    printf("- Key derivation functions for secure password storage\n");
    printf("- AES encryption for symmetric cryptography\n");
    printf("- RSA encryption for asymmetric cryptography\n");
    printf("- Digital signatures for authentication and integrity\n");
    printf("- X.509 certificates for trust management\n");
    printf("- Password hashing with salt for secure storage\n");
    printf("- Secure session management for communication\n");
    printf("- Security auditing for compliance and monitoring\n");
    
    return 0;
}
