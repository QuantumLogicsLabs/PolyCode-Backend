/*
 * File: hash_functions.c
 * Description: Various hash function implementations
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdint.h>

// Simple hash function (djb2 algorithm)
uint32_t djb2_hash(const char* str) {
    uint32_t hash = 5381;
    int c;
    
    while ((c = *str++)) {
        hash = ((hash << 5) + hash) + c; // hash * 33 + c
    }
    
    return hash;
}

// Simple hash function (sdbm algorithm)
uint32_t sdbm_hash(const char* str) {
    uint32_t hash = 0;
    int c;
    
    while ((c = *str++)) {
        hash = c + (hash << 6) + (hash << 16) - hash;
    }
    
    return hash;
}

// Simple hash function (FNV-1a)
uint32_t fnv1a_hash(const char* str) {
    uint32_t hash = 2166136261u; // FNV offset basis
    
    while (*str) {
        hash ^= (uint8_t)*str++;
        hash *= 16777619u; // FNV prime
    }
    
    return hash;
}

// CRC32 hash (simplified version)
uint32_t crc32_hash(const char* str) {
    uint32_t crc = 0xFFFFFFFF;
    uint32_t table[256];
    
    // Generate CRC table
    for (int i = 0; i < 256; i++) {
        uint32_t rem = i;
        for (int j = 0; j < 8; j++) {
            if (rem & 1) {
                rem = (rem >> 1) ^ 0xEDB88320;
            } else {
                rem >>= 1;
            }
        }
        table[i] = rem;
    }
    
    // Calculate CRC
    while (*str) {
        uint8_t byte = *str++;
        crc = (crc >> 8) ^ table[(crc ^ byte) & 0xFF];
    }
    
    return crc ^ 0xFFFFFFFF;
}

// Simple checksum
uint16_t simple_checksum(const char* str) {
    uint16_t checksum = 0;
    
    while (*str) {
        checksum += (uint8_t)*str++;
    }
    
    return checksum;
}

// XOR-based hash
uint8_t xor_hash(const char* str) {
    uint8_t hash = 0;
    
    while (*str) {
        hash ^= (uint8_t)*str++;
    }
    
    return hash;
}

// Rolling hash (for string matching)
typedef struct {
    uint32_t hash;
    uint32_t base;
    uint32_t mod;
    int window_size;
} RollingHash;

RollingHash* rolling_hash_init(int window_size) {
    RollingHash* rh = (RollingHash*)malloc(sizeof(RollingHash));
    rh->base = 256;
    rh->mod = 1000000007;
    rh->window_size = window_size;
    rh->hash = 0;
    return rh;
}

void rolling_hash_add_char(RollingHash* rh, char c) {
    rh->hash = (rh->hash * rh->base + c) % rh->mod;
}

void rolling_hash_remove_char(RollingHash* rh, char old_char) {
    uint32_t power = 1;
    for (int i = 0; i < rh->window_size - 1; i++) {
        power = (power * rh->base) % rh->mod;
    }
    rh->hash = (rh->hash - old_char * power + rh->mod) % rh->mod;
}

void rolling_hash_slide(RollingHash* rh, char old_char, char new_char) {
    rolling_hash_remove_char(rh, old_char);
    rolling_hash_add_char(rh, new_char);
}

// Bloom filter implementation
#define BLOOM_SIZE 1000

typedef struct {
    uint8_t bits[BLOOM_SIZE / 8];
    int num_hashes;
} BloomFilter;

BloomFilter* bloom_filter_create(int num_hashes) {
    BloomFilter* bf = (BloomFilter*)malloc(sizeof(BloomFilter));
    bf->num_hashes = num_hashes;
    memset(bf->bits, 0, sizeof(bf->bits));
    return bf;
}

void bloom_filter_add(BloomFilter* bf, const char* str) {
    for (int i = 0; i < bf->num_hashes; i++) {
        uint32_t hash = djb2_hash(str) + i;
        int bit = hash % BLOOM_SIZE;
        bf->bits[bit / 8] |= (1 << (bit % 8));
    }
}

int bloom_filter_contains(BloomFilter* bf, const char* str) {
    for (int i = 0; i < bf->num_hashes; i++) {
        uint32_t hash = djb2_hash(str) + i;
        int bit = hash % BLOOM_SIZE;
        if (!(bf->bits[bit / 8] & (1 << (bit % 8)))) {
            return 0; // Definitely not present
        }
    }
    return 1; // Might be present
}

// Hash table implementation
#define TABLE_SIZE 100

typedef struct HashNode {
    char* key;
    char* value;
    struct HashNode* next;
} HashNode;

typedef struct {
    HashNode* buckets[TABLE_SIZE];
} HashTable;

HashTable* hash_table_create() {
    HashTable* ht = (HashTable*)malloc(sizeof(HashTable));
    memset(ht->buckets, 0, sizeof(ht->buckets));
    return ht;
}

uint32_t hash_table_index(const char* key) {
    return djb2_hash(key) % TABLE_SIZE;
}

void hash_table_insert(HashTable* ht, const char* key, const char* value) {
    uint32_t index = hash_table_index(key);
    HashNode* node = (HashNode*)malloc(sizeof(HashNode));
    
    node->key = strdup(key);
    node->value = strdup(value);
    node->next = ht->buckets[index];
    ht->buckets[index] = node;
}

char* hash_table_get(HashTable* ht, const char* key) {
    uint32_t index = hash_table_index(key);
    HashNode* node = ht->buckets[index];
    
    while (node != NULL) {
        if (strcmp(node->key, key) == 0) {
            return node->value;
        }
        node = node->next;
    }
    
    return NULL;
}

void hash_table_remove(HashTable* ht, const char* key) {
    uint32_t index = hash_table_index(key);
    HashNode* node = ht->buckets[index];
    HashNode* prev = NULL;
    
    while (node != NULL) {
        if (strcmp(node->key, key) == 0) {
            if (prev == NULL) {
                ht->buckets[index] = node->next;
            } else {
                prev->next = node->next;
            }
            free(node->key);
            free(node->value);
            free(node);
            return;
        }
        prev = node;
        node = node->next;
    }
}

// Test function
void test_hash_functions() {
    const char* test_strings[] = {
        "hello",
        "world",
        "hash",
        "functions",
        "testing",
        "c programming",
        "data structures",
        "algorithms"
    };
    
    int num_strings = sizeof(test_strings) / sizeof(test_strings[0]);
    
    printf("=== Hash Function Testing ===\n\n");
    
    // Test different hash functions
    printf("1. Hash Function Comparisons:\n");
    printf("%-20s %-10s %-10s %-10s %-10s\n", 
           "String", "DJB2", "SDBM", "FNV-1a", "CRC32");
    printf("--------------------------------------------------------\n");
    
    for (int i = 0; i < num_strings; i++) {
        uint32_t djb2 = djb2_hash(test_strings[i]);
        uint32_t sdbm = sdbm_hash(test_strings[i]);
        uint32_t fnv1a = fnv1a_hash(test_strings[i]);
        uint32_t crc32 = crc32_hash(test_strings[i]);
        
        printf("%-20s %-10u %-10u %-10u %-10u\n", 
               test_strings[i], djb2, sdbm, fnv1a, crc32);
    }
    
    // Test checksum
    printf("\n2. Simple Checksum:\n");
    for (int i = 0; i < num_strings; i++) {
        uint16_t checksum = simple_checksum(test_strings[i]);
        printf("%-20s: %u\n", test_strings[i], checksum);
    }
    
    // Test XOR hash
    printf("\n3. XOR Hash:\n");
    for (int i = 0; i < num_strings; i++) {
        uint8_t xor = xor_hash(test_strings[i]);
        printf("%-20s: %u\n", test_strings[i], xor);
    }
    
    // Test rolling hash
    printf("\n4. Rolling Hash Test:\n");
    RollingHash* rh = rolling_hash_init(5);
    
    const char* test_string = "HELLO WORLD";
    printf("String: %s\n", test_string);
    
    // Initialize with first 5 characters
    for (int i = 0; i < 5; i++) {
        rolling_hash_add_char(rh, test_string[i]);
    }
    printf("Initial hash (first 5 chars): %u\n", rh->hash);
    
    // Slide window
    for (int i = 5; i < strlen(test_string); i++) {
        rolling_hash_slide(rh, test_string[i - 5], test_string[i]);
        printf("Hash after adding '%c', removing '%c': %u\n", 
               test_string[i], test_string[i - 5], rh->hash);
    }
    
    free(rh);
    
    // Test Bloom filter
    printf("\n5. Bloom Filter Test:\n");
    BloomFilter* bf = bloom_filter_create(3);
    
    // Add some items
    bloom_filter_add(bf, "apple");
    bloom_filter_add(bf, "banana");
    bloom_filter_add(bf, "orange");
    
    // Test membership
    const char* test_items[] = {"apple", "banana", "orange", "grape", "pear"};
    for (int i = 0; i < 5; i++) {
        int contains = bloom_filter_contains(bf, test_items[i]);
        printf("%-10s: %s\n", test_items[i], contains ? "Probably present" : "Definitely absent");
    }
    
    free(bf);
    
    // Test hash table
    printf("\n6. Hash Table Test:\n");
    HashTable* ht = hash_table_create();
    
    // Insert key-value pairs
    hash_table_insert(ht, "name", "John Doe");
    hash_table_insert(ht, "age", "30");
    hash_table_insert(ht, "city", "New York");
    hash_table_insert(ht, "country", "USA");
    
    // Retrieve values
    const char* keys[] = {"name", "age", "city", "country", "email"};
    for (int i = 0; i < 5; i++) {
        char* value = hash_table_get(ht, keys[i]);
        printf("%-10s: %s\n", keys[i], value ? value : "NULL");
    }
    
    // Remove a key
    hash_table_remove(ht, "city");
    printf("\nAfter removing 'city':\n");
    for (int i = 0; i < 5; i++) {
        char* value = hash_table_get(ht, keys[i]);
        printf("%-10s: %s\n", keys[i], value ? value : "NULL");
    }
    
    free(ht);
}

int main() {
    test_hash_functions();
    
    printf("\n=== Hash function testing completed ===\n");
    
    return 0;
}
