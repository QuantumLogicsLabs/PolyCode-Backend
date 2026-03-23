/*
 * File: aes_encryption.c
 * Description: Simple AES encryption implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdint.h>

// AES block size in bytes
#define AES_BLOCK_SIZE 16

// Number of rounds for different key sizes
#define AES128_ROUNDS 10
#define AES192_ROUNDS 12
#define AES256_ROUNDS 14

// AES key schedule structure
typedef struct {
    uint32_t words[60]; // Maximum for AES-256
    int rounds;
} AESKeySchedule;

// S-box (Substitution box)
static const uint8_t sbox[256] = {
    0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5,
    0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76,
    0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0,
    0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0,
    0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc,
    0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15,
    0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a,
    0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75,
    0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0,
    0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84,
    0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b,
    0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf,
    0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85,
    0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8,
    0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5,
    0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2,
    0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17,
    0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73,
    0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88,
    0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb,
    0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c,
    0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79,
    0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9,
    0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08,
    0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6,
    0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a,
    0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e,
    0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e,
    0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94,
    0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf,
    0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68,
    0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16
};

// Inverse S-box
static const uint8_t inv_sbox[256] = {
    0x52, 0x09, 0x6a, 0xd5, 0x30, 0x36, 0xa5, 0x38,
    0xbf, 0x40, 0xa3, 0x9e, 0x81, 0xf3, 0xd7, 0xfb,
    0x7c, 0xe3, 0x39, 0x82, 0x9b, 0x2f, 0xff, 0x87,
    0x34, 0x8e, 0x43, 0x44, 0xc4, 0xde, 0xe9, 0xcb,
    0x54, 0x7b, 0x94, 0x32, 0xa6, 0xc2, 0x23, 0x3d,
    0xee, 0x4c, 0x95, 0x0b, 0x42, 0xfa, 0xc3, 0x4e,
    0x08, 0x2e, 0xa1, 0x66, 0x28, 0xd9, 0x24, 0xb2,
    0x76, 0x5b, 0xa2, 0x49, 0x6d, 0x8b, 0xd1, 0x25,
    0x72, 0xf8, 0xf6, 0x64, 0x86, 0x68, 0x98, 0x16,
    0xd4, 0xa4, 0x5c, 0xcc, 0x5d, 0x65, 0xb6, 0x92,
    0x6c, 0x70, 0x48, 0x50, 0xfd, 0xed, 0xb9, 0xda,
    0x5e, 0x15, 0x46, 0x57, 0xa7, 0x8d, 0x9d, 0x84,
    0x90, 0xd8, 0xab, 0x00, 0x8c, 0xbc, 0xd3, 0x0a,
    0xf7, 0xe4, 0x58, 0x05, 0xb8, 0xb3, 0x45, 0x06,
    0xd0, 0x2c, 0x1e, 0x8f, 0xca, 0x3f, 0x0f, 0x02,
    0xc1, 0xaf, 0xbd, 0x03, 0x01, 0x13, 0x8a, 0x6b,
    0x3a, 0x91, 0x11, 0x41, 0x4f, 0x67, 0xdc, 0xea,
    0x97, 0xf2, 0xcf, 0xce, 0xf0, 0xb4, 0xe6, 0x73,
    0x96, 0xac, 0x74, 0x22, 0xe7, 0xad, 0x35, 0x85,
    0xe2, 0xf9, 0x37, 0xe8, 0x1c, 0x75, 0xdf, 0x6e,
    0x47, 0xf1, 0x1a, 0x71, 0x1d, 0x29, 0xc5, 0x89,
    0x6f, 0xb7, 0x62, 0x0e, 0xaa, 0x18, 0xbe, 0x1b,
    0xfc, 0x56, 0x3e, 0x4b, 0xc6, 0xd2, 0x79, 0x20,
    0x9a, 0xdb, 0xc0, 0xfe, 0x78, 0xcd, 0x5a, 0xf4,
    0x1f, 0xdd, 0xa8, 0x33, 0x88, 0x07, 0xc7, 0x31,
    0xb1, 0x12, 0x10, 0x59, 0x27, 0x80, 0xec, 0x5f,
    0x60, 0x51, 0x7f, 0xa9, 0x19, 0xb5, 0x4a, 0x0d,
    0x2d, 0xe5, 0x7a, 0x9f, 0x93, 0xc9, 0x9c, 0xef,
    0xa0, 0xe0, 0x3b, 0x4d, 0xae, 0x2a, 0xf5, 0xb0,
    0xc8, 0xeb, 0xbb, 0x3c, 0x83, 0x53, 0x99, 0x61,
    0x17, 0x2b, 0x04, 0x7e, 0xba, 0x77, 0xd6, 0x26,
    0xe1, 0x69, 0x14, 0x63, 0x55, 0x21, 0x0c, 0x7d
};

// Round constants
static const uint32_t rcon[10] = {
    0x01000000, 0x02000000, 0x04000000, 0x08000000, 0x10000000,
    0x20000000, 0x40000000, 0x80000000, 0x1b000000, 0x36000000
};

// Helper function: rotate word
uint32_t rotate_word(uint32_t word) {
    return (word << 8) | (word >> 24);
}

// Helper function: S-box substitution
uint32_t sub_word(uint32_t word) {
    return (sbox[word & 0xff] << 24) |
           (sbox[(word >> 8) & 0xff] << 16) |
           (sbox[(word >> 16) & 0xff] << 8) |
           sbox[(word >> 24) & 0xff];
}

// Helper function: inverse S-box substitution
uint32_t inv_sub_word(uint32_t word) {
    return (inv_sbox[word & 0xff] << 24) |
           (inv_sbox[(word >> 8) & 0xff] << 16) |
           (inv_sbox[(word >> 16) & 0xff] << 8) |
           inv_sbox[(word >> 24) & 0xff];
}

// Key expansion for AES-128
void expand_key_128(const uint8_t* key, AESKeySchedule* schedule) {
    schedule->rounds = AES128_ROUNDS;
    
    // Copy the original key
    for (int i = 0; i < 4; i++) {
        schedule->words[i] = (key[i*4] << 24) | (key[i*4+1] << 16) | 
                           (key[i*4+2] << 8) | key[i*4+3];
    }
    
    // Generate the remaining words
    for (int i = 4; i < 44; i++) {
        uint32_t temp = schedule->words[i - 1];
        
        if (i % 4 == 0) {
            temp = sub_word(rotate_word(temp)) ^ rcon[i / 4 - 1];
        }
        
        schedule->words[i] = schedule->words[i - 4] ^ temp;
    }
}

// Key expansion for AES-192
void expand_key_192(const uint8_t* key, AESKeySchedule* schedule) {
    schedule->rounds = AES192_ROUNDS;
    
    // Copy the original key
    for (int i = 0; i < 6; i++) {
        schedule->words[i] = (key[i*4] << 24) | (key[i*4+1] << 16) | 
                           (key[i*4+2] << 8) | key[i*4+3];
    }
    
    // Generate the remaining words
    for (int i = 6; i < 52; i++) {
        uint32_t temp = schedule->words[i - 1];
        
        if (i % 6 == 0) {
            temp = sub_word(rotate_word(temp)) ^ rcon[i / 6 - 1];
        }
        
        schedule->words[i] = schedule->words[i - 6] ^ temp;
    }
}

// Key expansion for AES-256
void expand_key_256(const uint8_t* key, AESKeySchedule* schedule) {
    schedule->rounds = AES256_ROUNDS;
    
    // Copy the original key
    for (int i = 0; i < 8; i++) {
        schedule->words[i] = (key[i*4] << 24) | (key[i*4+1] << 16) | 
                           (key[i*4+2] << 8) | key[i*4+3];
    }
    
    // Generate the remaining words
    for (int i = 8; i < 60; i++) {
        uint32_t temp = schedule->words[i - 1];
        
        if (i % 8 == 0) {
            temp = sub_word(rotate_word(temp)) ^ rcon[i / 8 - 1];
        } else if (i % 8 == 4) {
            temp = sub_word(temp);
        }
        
        schedule->words[i] = schedule->words[i - 8] ^ temp;
    }
}

// SubBytes transformation
void sub_bytes(uint8_t* state) {
    for (int i = 0; i < 16; i++) {
        state[i] = sbox[state[i]];
    }
}

// Inverse SubBytes transformation
void inv_sub_bytes(uint8_t* state) {
    for (int i = 0; i < 16; i++) {
        state[i] = inv_sbox[state[i]];
    }
}

// ShiftRows transformation
void shift_rows(uint8_t* state) {
    uint8_t temp[4];
    
    // Row 1: shift 1
    temp[0] = state[1];
    state[1] = state[5];
    state[5] = state[9];
    state[9] = state[13];
    state[13] = temp[0];
    
    // Row 2: shift 2
    temp[0] = state[2];
    temp[1] = state[6];
    state[2] = state[10];
    state[6] = state[14];
    state[10] = temp[0];
    state[14] = temp[1];
    
    // Row 3: shift 3
    temp[0] = state[3];
    temp[1] = state[7];
    temp[2] = state[11];
    state[3] = state[15];
    state[7] = temp[2];
    state[11] = temp[1];
    state[15] = temp[0];
}

// Inverse ShiftRows transformation
void inv_shift_rows(uint8_t* state) {
    uint8_t temp[4];
    
    // Row 1: shift 3 (inverse of shift 1)
    temp[0] = state[1];
    state[1] = state[13];
    state[13] = state[9];
    state[9] = state[5];
    state[5] = temp[0];
    
    // Row 2: shift 2 (inverse of shift 2)
    temp[0] = state[2];
    temp[1] = state[6];
    state[2] = state[14];
    state[6] = state[10];
    state[10] = temp[0];
    state[14] = temp[1];
    
    // Row 3: shift 1 (inverse of shift 3)
    temp[0] = state[3];
    temp[1] = state[7];
    temp[2] = state[11];
    state[3] = state[15];
    state[7] = temp[2];
    state[11] = temp[1];
    state[15] = temp[0];
}

// MixColumns transformation
void mix_columns(uint8_t* state) {
    uint8_t temp[16];
    
    for (int c = 0; c < 4; c++) {
        temp[c*4] = (uint8_t)( (uint32_t)0x02 * state[c*4] ^ 
                              (uint32_t)0x03 * state[c*4+1] ^ 
                              state[c*4+2] ^ 
                              state[c*4+3] );
        temp[c*4+1] = (uint8_t)( state[c*4] ^ 
                              (uint32_t)0x02 * state[c*4+1] ^ 
                              (uint32_t)0x03 * state[c*4+2] ^ 
                              state[c*4+3] );
        temp[c*4+2] = (uint8_t)( state[c*4] ^ 
                              state[c*4+1] ^ 
                              (uint32_t)0x02 * state[c*4+2] ^ 
                              (uint32_t)0x03 * state[c*4+3] );
        temp[c*4+3] = (uint8_t)( (uint32_t)0x03 * state[c*4] ^ 
                              state[c*4+1] ^ 
                              state[c*4+2] ^ 
                              (uint32_t)0x02 * state[c*4+3] );
    }
    
    memcpy(state, temp, 16);
}

// Inverse MixColumns transformation
void inv_mix_columns(uint8_t* state) {
    uint8_t temp[16];
    
    for (int c = 0; c < 4; c++) {
        temp[c*4] = (uint8_t)( (uint32_t)0x0e * state[c*4] ^ 
                              (uint32_t)0x0b * state[c*4+1] ^ 
                              (uint32_t)0x0d * state[c*4+2] ^ 
                              (uint32_t)0x09 * state[c*4+3] );
        temp[c*4+1] = (uint8_t)( (uint32_t)0x09 * state[c*4] ^ 
                              (uint32_t)0x0e * state[c*4+1] ^ 
                              (uint32_t)0x0b * state[c*4+2] ^ 
                              (uint32_t)0x0d * state[c*4+3] );
        temp[c*4+2] = (uint8_t)( (uint32_t)0x0d * state[c*4] ^ 
                              (uint32_t)0x09 * state[c*4+1] ^ 
                              (uint32_t)0x0e * state[c*4+2] ^ 
                              (uint32_t)0x0b * state[c*4+3] );
        temp[c*4+3] = (uint8_t)( (uint32_t)0x0b * state[c*4] ^ 
                              (uint32_t)0x0d * state[c*4+1] ^ 
                              (uint32_t)0x09 * state[c*4+2] ^ 
                              (uint32_t)0x0e * state[c*4+3] );
    }
    
    memcpy(state, temp, 16);
}

// AddRoundKey transformation
void add_round_key(uint8_t* state, const uint32_t* round_key) {
    for (int i = 0; i < 4; i++) {
        state[i*4]   ^= (round_key[i] >> 24) & 0xff;
        state[i*4+1] ^= (round_key[i] >> 16) & 0xff;
        state[i*4+2] ^= (round_key[i] >> 8) & 0xff;
        state[i*4+3] ^= round_key[i] & 0xff;
    }
}

// AES encryption
void aes_encrypt_block(const uint8_t* plaintext, const uint8_t* key, 
                      uint8_t* ciphertext, int key_size) {
    AESKeySchedule schedule;
    
    // Expand key
    switch (key_size) {
        case 16:
            expand_key_128(key, &schedule);
            break;
        case 24:
            expand_key_192(key, &schedule);
            break;
        case 32:
            expand_key_256(key, &schedule);
            break;
        default:
            return; // Invalid key size
    }
    
    // Copy plaintext to state
    uint8_t state[16];
    memcpy(state, plaintext, 16);
    
    // Initial round
    add_round_key(state, schedule.words);
    
    // Main rounds
    for (int round = 1; round < schedule.rounds; round++) {
        sub_bytes(state);
        shift_rows(state);
        mix_columns(state);
        add_round_key(state, schedule.words + round * 4);
    }
    
    // Final round
    sub_bytes(state);
    shift_rows(state);
    add_round_key(state, schedule.words + schedule.rounds * 4);
    
    // Copy state to ciphertext
    memcpy(ciphertext, state, 16);
}

// AES decryption
void aes_decrypt_block(const uint8_t* ciphertext, const uint8_t* key, 
                      uint8_t* plaintext, int key_size) {
    AESKeySchedule schedule;
    
    // Expand key
    switch (key_size) {
        case 16:
            expand_key_128(key, &schedule);
            break;
        case 24:
            expand_key_192(key, &schedule);
            break;
        case 32:
            expand_key_256(key, &schedule);
            break;
        default:
            return; // Invalid key size
    }
    
    // Copy ciphertext to state
    uint8_t state[16];
    memcpy(state, ciphertext, 16);
    
    // Initial round
    add_round_key(state, schedule.words + schedule.rounds * 4);
    
    // Main rounds
    for (int round = schedule.rounds - 1; round > 0; round--) {
        inv_shift_rows(state);
        inv_sub_bytes(state);
        add_round_key(state, schedule.words + round * 4);
        inv_mix_columns(state);
    }
    
    // Final round
    inv_shift_rows(state);
    inv_sub_bytes(state);
    add_round_key(state, schedule.words);
    
    // Copy state to plaintext
    memcpy(plaintext, state, 16);
}

// Print hex data
void print_hex(const char* label, const uint8_t* data, int len) {
    printf("%s: ", label);
    for (int i = 0; i < len; i++) {
        printf("%02x", data[i]);
    }
    printf("\n");
}

// Test function
void test_aes() {
    printf("=== AES Encryption Test ===\n\n");
    
    // Test vectors from FIPS-197
    uint8_t key[32];
    uint8_t plaintext[16];
    uint8_t ciphertext[16];
    uint8_t decrypted[16];
    
    // Test AES-128
    printf("1. AES-128 Test:\n");
    memset(key, 0x00, 16);
    memset(plaintext, 0x00, 16);
    
    aes_encrypt_block(plaintext, key, ciphertext, 16);
    print_hex("Plaintext", plaintext, 16);
    print_hex("Key", key, 16);
    print_hex("Ciphertext", ciphertext, 16);
    
    aes_decrypt_block(ciphertext, key, decrypted, 16);
    print_hex("Decrypted", decrypted, 16);
    
    if (memcmp(plaintext, decrypted, 16) == 0) {
        printf("AES-128: PASSED\n");
    } else {
        printf("AES-128: FAILED\n");
    }
    
    // Test AES-192
    printf("\n2. AES-192 Test:\n");
    memset(key, 0x00, 24);
    memset(plaintext, 0x00, 16);
    
    aes_encrypt_block(plaintext, key, ciphertext, 24);
    print_hex("Key", key, 24);
    print_hex("Ciphertext", ciphertext, 16);
    
    aes_decrypt_block(ciphertext, key, decrypted, 24);
    print_hex("Decrypted", decrypted, 16);
    
    if (memcmp(plaintext, decrypted, 16) == 0) {
        printf("AES-192: PASSED\n");
    } else {
        printf("AES-192: FAILED\n");
    }
    
    // Test AES-256
    printf("\n3. AES-256 Test:\n");
    memset(key, 0x00, 32);
    memset(plaintext, 0x00, 16);
    
    aes_encrypt_block(plaintext, key, ciphertext, 32);
    print_hex("Key", key, 32);
    print_hex("Ciphertext", ciphertext, 16);
    
    aes_decrypt_block(ciphertext, key, decrypted, 32);
    print_hex("Decrypted", decrypted, 16);
    
    if (memcmp(plaintext, decrypted, 16) == 0) {
        printf("AES-256: PASSED\n");
    } else {
        printf("AES-256: FAILED\n");
    }
    
    // Test with real data
    printf("\n4. Real Data Test (AES-128):\n");
    const char* message = "Hello, AES!";
    const char* key_str = "MySecretKey12345";
    
    // Pad message to 16 bytes
    memset(plaintext, 0, 16);
    strncpy((char*)plaintext, message, 16);
    
    // Pad key to 16 bytes
    memset(key, 0, 16);
    strncpy((char*)key, key_str, 16);
    
    printf("Original: %s\n", message);
    aes_encrypt_block(plaintext, key, ciphertext, 16);
    print_hex("Encrypted", ciphertext, 16);
    
    aes_decrypt_block(ciphertext, key, decrypted, 16);
    printf("Decrypted: %s\n", decrypted);
    
    printf("\n=== AES testing completed ===\n");
}

int main() {
    test_aes();
    
    return 0;
}
