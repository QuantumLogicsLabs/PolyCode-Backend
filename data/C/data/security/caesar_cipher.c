/*
 * File: caesar_cipher.c
 * Description: Caesar cipher implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>

// Caesar cipher encryption
void caesar_encrypt(const char* plaintext, char* ciphertext, int shift) {
    int i;
    int len = strlen(plaintext);
    
    for (i = 0; i < len; i++) {
        char c = plaintext[i];
        
        if (isupper(c)) {
            // Handle uppercase letters
            ciphertext[i] = ((c - 'A' + shift) % 26) + 'A';
        } else if (islower(c)) {
            // Handle lowercase letters
            ciphertext[i] = ((c - 'a' + shift) % 26) + 'a';
        } else {
            // Leave non-alphabetic characters unchanged
            ciphertext[i] = c;
        }
    }
    
    ciphertext[i] = '\0';
}

// Caesar cipher decryption
void caesar_decrypt(const char* ciphertext, char* plaintext, int shift) {
    // Decryption is just encryption with negative shift
    caesar_encrypt(ciphertext, plaintext, (26 - shift) % 26);
}

// Brute force Caesar cipher decryption
void caesar_brute_force(const char* ciphertext) {
    char plaintext[1024];
    
    printf("Brute force decryption results:\n");
    printf("================================\n");
    
    for (int shift = 0; shift < 26; shift++) {
        caesar_decrypt(ciphertext, plaintext, shift);
        printf("Shift %2d: %s\n", shift, plaintext);
    }
}

// Frequency analysis for Caesar cipher
void frequency_analysis(const char* text) {
    int frequency[26] = {0};
    int total_letters = 0;
    
    // Count letter frequencies
    for (int i = 0; text[i] != '\0'; i++) {
        if (isalpha(text[i])) {
            char c = toupper(text[i]);
            frequency[c - 'A']++;
            total_letters++;
        }
    }
    
    printf("Letter Frequency Analysis:\n");
    printf("==========================\n");
    
    // Print frequencies
    for (int i = 0; i < 26; i++) {
        double percentage = (double)frequency[i] / total_letters * 100;
        printf("%c: %2d (%.1f%%)\n", 'A' + i, frequency[i], percentage);
    }
    
    // Find most frequent letter
    int max_freq = 0;
    int max_index = 0;
    
    for (int i = 0; i < 26; i++) {
        if (frequency[i] > max_freq) {
            max_freq = frequency[i];
            max_index = i;
        }
    }
    
    printf("\nMost frequent letter: %c (%d occurrences)\n", 
           'A' + max_index, max_freq);
    
    // Suggest possible shift (assuming 'E' is most common in English)
    int suggested_shift = (max_index - ('E' - 'A') + 26) % 26;
    printf("Suggested Caesar shift: %d\n", suggested_shift);
}

// Vigenère cipher (extended Caesar cipher)
void vigenere_encrypt(const char* plaintext, char* ciphertext, const char* key) {
    int key_len = strlen(key);
    int key_index = 0;
    
    for (int i = 0; plaintext[i] != '\0'; i++) {
        char c = plaintext[i];
        char k = toupper(key[key_index % key_len]) - 'A';
        
        if (isupper(c)) {
            ciphertext[i] = ((c - 'A' + k) % 26) + 'A';
            key_index++;
        } else if (islower(c)) {
            ciphertext[i] = ((c - 'a' + k) % 26) + 'a';
            key_index++;
        } else {
            ciphertext[i] = c;
        }
    }
    
    ciphertext[strlen(plaintext)] = '\0';
}

void vigenere_decrypt(const char* ciphertext, char* plaintext, const char* key) {
    int key_len = strlen(key);
    int key_index = 0;
    
    for (int i = 0; ciphertext[i] != '\0'; i++) {
        char c = ciphertext[i];
        char k = toupper(key[key_index % key_len]) - 'A';
        
        if (isupper(c)) {
            plaintext[i] = ((c - 'A' - k + 26) % 26) + 'A';
            key_index++;
        } else if (islower(c)) {
            plaintext[i] = ((c - 'a' - k + 26) % 26) + 'a';
            key_index++;
        } else {
            plaintext[i] = c;
        }
    }
    
    plaintext[strlen(ciphertext)] = '\0';
}

// ROT13 (special case of Caesar cipher)
void rot13(const char* input, char* output) {
    caesar_encrypt(input, output, 13);
}

// Test function
void test_ciphers() {
    char plaintext[1024];
    char ciphertext[1024];
    char decrypted[1024];
    
    printf("=== Cipher Testing ===\n\n");
    
    // Test Caesar cipher
    printf("1. Caesar Cipher Test:\n");
    strcpy(plaintext, "Hello, World! This is a test message.");
    int shift = 3;
    
    caesar_encrypt(plaintext, ciphertext, shift);
    printf("Original:  %s\n", plaintext);
    printf("Encrypted: %s (shift %d)\n", ciphertext, shift);
    
    caesar_decrypt(ciphertext, decrypted, shift);
    printf("Decrypted: %s\n\n", decrypted);
    
    // Test brute force
    printf("2. Brute Force Decryption:\n");
    caesar_encrypt(plaintext, ciphertext, 7);
    printf("Encrypted: %s\n", ciphertext);
    caesar_brute_force(ciphertext);
    
    // Test frequency analysis
    printf("\n3. Frequency Analysis:\n");
    frequency_analysis(ciphertext);
    
    // Test Vigenère cipher
    printf("\n4. Vigenère Cipher Test:\n");
    const char* key = "SECRET";
    strcpy(plaintext, "This is a secret message using Vigenere cipher.");
    
    vigenere_encrypt(plaintext, ciphertext, key);
    printf("Original:  %s\n", plaintext);
    printf("Key:       %s\n", key);
    printf("Encrypted: %s\n", ciphertext);
    
    vigenere_decrypt(ciphertext, decrypted, key);
    printf("Decrypted: %s\n\n", decrypted);
    
    // Test ROT13
    printf("5. ROT13 Test:\n");
    strcpy(plaintext, "ROT13 is its own inverse!");
    rot13(plaintext, ciphertext);
    printf("Original:  %s\n", plaintext);
    printf("ROT13:     %s\n", ciphertext);
    
    rot13(ciphertext, decrypted);
    printf("ROT13 again:%s\n", decrypted);
}

// Interactive cipher tool
void interactive_cipher() {
    char input[1024];
    char output[1024];
    int choice, shift;
    char key[100];
    
    printf("\n=== Interactive Cipher Tool ===\n");
    
    while (1) {
        printf("\nOptions:\n");
        printf("1. Caesar encrypt\n");
        printf("2. Caesar decrypt\n");
        printf("3. Caesar brute force\n");
        printf("4. Vigenère encrypt\n");
        printf("5. Vigenère decrypt\n");
        printf("6. ROT13\n");
        printf("7. Exit\n");
        printf("Choice: ");
        
        scanf("%d", &choice);
        getchar(); // Consume newline
        
        switch (choice) {
            case 1:
                printf("Enter text: ");
                fgets(input, sizeof(input), stdin);
                input[strcspn(input, "\n")] = '\0';
                
                printf("Enter shift (0-25): ");
                scanf("%d", &shift);
                getchar();
                
                caesar_encrypt(input, output, shift);
                printf("Encrypted: %s\n", output);
                break;
                
            case 2:
                printf("Enter text: ");
                fgets(input, sizeof(input), stdin);
                input[strcspn(input, "\n")] = '\0';
                
                printf("Enter shift (0-25): ");
                scanf("%d", &shift);
                getchar();
                
                caesar_decrypt(input, output, shift);
                printf("Decrypted: %s\n", output);
                break;
                
            case 3:
                printf("Enter text: ");
                fgets(input, sizeof(input), stdin);
                input[strcspn(input, "\n")] = '\0';
                
                caesar_brute_force(input);
                break;
                
            case 4:
                printf("Enter text: ");
                fgets(input, sizeof(input), stdin);
                input[strcspn(input, "\n")] = '\0';
                
                printf("Enter key: ");
                fgets(key, sizeof(key), stdin);
                key[strcspn(key, "\n")] = '\0';
                
                vigenere_encrypt(input, output, key);
                printf("Encrypted: %s\n", output);
                break;
                
            case 5:
                printf("Enter text: ");
                fgets(input, sizeof(input), stdin);
                input[strcspn(input, "\n")] = '\0';
                
                printf("Enter key: ");
                fgets(key, sizeof(key), stdin);
                key[strcspn(key, "\n")] = '\0';
                
                vigenere_decrypt(input, output, key);
                printf("Decrypted: %s\n", output);
                break;
                
            case 6:
                printf("Enter text: ");
                fgets(input, sizeof(input), stdin);
                input[strcspn(input, "\n")] = '\0';
                
                rot13(input, output);
                printf("ROT13: %s\n", output);
                break;
                
            case 7:
                return;
                
            default:
                printf("Invalid choice!\n");
        }
    }
}

int main() {
    test_ciphers();
    interactive_cipher();
    
    return 0;
}
