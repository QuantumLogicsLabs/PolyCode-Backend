/*
 * File: validation_utils.c
 * Description: Comprehensive input validation utilities for C programming
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <stdbool.h>
#include <regex.h>

// ============================================================================
// BASIC VALIDATION FUNCTIONS
// ============================================================================

/**
 * @brief Check if a string is empty or contains only whitespace
 * @param str String to check
 * @return true if empty or whitespace only, false otherwise
 */
bool isEmpty(const char* str) {
    if (str == NULL) return true;
    
    while (*str) {
        if (!isspace((unsigned char)*str)) {
            return false;
        }
        str++;
    }
    return true;
}

/**
 * @brief Check if a string contains only digits
 * @param str String to check
 * @return true if contains only digits, false otherwise
 */
bool isNumeric(const char* str) {
    if (isEmpty(str)) return false;
    
    while (*str) {
        if (!isdigit((unsigned char)*str)) {
            return false;
        }
        str++;
    }
    return true;
}

/**
 * @brief Check if a string represents a valid integer (including optional + or -)
 * @param str String to check
 * @return true if valid integer, false otherwise
 */
bool isInteger(const char* str) {
    if (isEmpty(str)) return false;
    
    // Skip optional sign
    if (*str == '+' || *str == '-') {
        str++;
    }
    
    // Must have at least one digit
    if (*str == '\0') return false;
    
    // Check remaining characters
    while (*str) {
        if (!isdigit((unsigned char)*str)) {
            return false;
        }
        str++;
    }
    return true;
}

/**
 * @brief Check if a string represents a valid floating point number
 * @param str String to check
 * @return true if valid float, false otherwise
 */
bool isFloat(const char* str) {
    if (isEmpty(str)) return false;
    
    bool has_digit = false;
    bool has_dot = false;
    
    // Skip optional sign
    if (*str == '+' || *str == '-') {
        str++;
    }
    
    while (*str) {
        if (isdigit((unsigned char)*str)) {
            has_digit = true;
        } else if (*str == '.' && !has_dot) {
            has_dot = true;
        } else {
            return false;
        }
        str++;
    }
    
    return has_digit;
}

/**
 * @brief Check if a string contains only alphabetic characters
 * @param str String to check
 * @return true if contains only letters, false otherwise
 */
bool isAlpha(const char* str) {
    if (isEmpty(str)) return false;
    
    while (*str) {
        if (!isalpha((unsigned char)*str)) {
            return false;
        }
        str++;
    }
    return true;
}

/**
 * @brief Check if a string contains only alphanumeric characters
 * @param str String to check
 * @return true if contains only letters and digits, false otherwise
 */
bool isAlphaNumeric(const char* str) {
    if (isEmpty(str)) return false;
    
    while (*str) {
        if (!isalnum((unsigned char)*str)) {
            return false;
        }
        str++;
    }
    return true;
}

// ============================================================================
// RANGE VALIDATION FUNCTIONS
// ============================================================================

/**
 * @brief Check if a string's length is within specified range
 * @param str String to check
 * @param min_len Minimum length (inclusive)
 * @param max_len Maximum length (inclusive)
 * @return true if length is within range, false otherwise
 */
bool isLengthInRange(const char* str, size_t min_len, size_t max_len) {
    if (str == NULL) return false;
    
    size_t len = strlen(str);
    return len >= min_len && len <= max_len;
}

/**
 * @brief Check if an integer string represents a value within range
 * @param str String to check
 * @param min_val Minimum value (inclusive)
 * @param max_val Maximum value (inclusive)
 * @return true if value is within range, false otherwise
 */
bool isIntegerInRange(const char* str, int min_val, int max_val) {
    if (!isInteger(str)) return false;
    
    int value = atoi(str);
    return value >= min_val && value <= max_val;
}

/**
 * @brief Check if a float string represents a value within range
 * @param str String to check
 * @param min_val Minimum value (inclusive)
 * @param max_val Maximum value (inclusive)
 * @return true if value is within range, false otherwise
 */
bool isFloatInRange(const char* str, double min_val, double max_val) {
    if (!isFloat(str)) return false;
    
    double value = atof(str);
    return value >= min_val && value <= max_val;
}

// ============================================================================
// PATTERN VALIDATION FUNCTIONS
// ============================================================================

/**
 * @brief Check if a string matches a regular expression pattern
 * @param str String to check
 * @param pattern Regular expression pattern
 * @return true if matches pattern, false otherwise
 */
bool matchesPattern(const char* str, const char* pattern) {
    if (str == NULL || pattern == NULL) return false;
    
    regex_t regex;
    int ret = regcomp(&regex, pattern, REG_EXTENDED);
    if (ret != 0) return false;
    
    ret = regexec(&regex, str, 0, NULL, 0);
    regfree(&regex);
    
    return ret == 0;
}

/**
 * @brief Check if a string is a valid email address
 * @param str String to check
 * @return true if valid email, false otherwise
 */
bool isValidEmail(const char* str) {
    if (isEmpty(str)) return false;
    
    // Basic email validation pattern
    const char* pattern = "^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$";
    return matchesPattern(str, pattern);
}

/**
 * @brief Check if a string is a valid phone number (basic validation)
 * @param str String to check
 * @return true if valid phone number, false otherwise
 */
bool isValidPhone(const char* str) {
    if (isEmpty(str)) return false;
    
    // Allow digits, spaces, hyphens, parentheses, and plus sign
    while (*str) {
        char c = *str;
        if (!isdigit((unsigned char)c) && c != ' ' && c != '-' && 
            c != '(' && c != ')' && c != '+') {
            return false;
        }
        str++;
    }
    
    return true;
}

/**
 * @brief Check if a string is a valid URL (basic validation)
 * @param str String to check
 * @return true if valid URL, false otherwise
 */
bool isValidURL(const char* str) {
    if (isEmpty(str)) return false;
    
    // Basic URL validation pattern
    const char* pattern = "^(https?://)?([A-Za-z0-9.-]+\\.[A-Za-z]{2,})(/.*)?$";
    return matchesPattern(str, pattern);
}

/**
 * @brief Check if a string is a valid IPv4 address
 * @param str String to check
 * @return true if valid IPv4 address, false otherwise
 */
bool isValidIPv4(const char* str) {
    if (isEmpty(str)) return false;
    
    int octets = 0;
    int current_value = 0;
    
    while (*str) {
        if (*str == '.') {
            if (current_value > 255) return false;
            octets++;
            current_value = 0;
            if (octets > 3) return false;
        } else if (isdigit((unsigned char)*str)) {
            current_value = current_value * 10 + (*str - '0');
            if (current_value > 255) return false;
        } else {
            return false;
        }
        str++;
    }
    
    // Check last octet
    if (current_value > 255) return false;
    octets++;
    
    return octets == 4;
}

/**
 * @brief Check if a string is a valid credit card number (Luhn algorithm)
 * @param str String to check
 * @return true if valid credit card number, false otherwise
 */
bool isValidCreditCard(const char* str) {
    if (!isNumeric(str)) return false;
    
    int len = strlen(str);
    if (len < 13 || len > 19) return false;
    
    int sum = 0;
    bool double_digit = false;
    
    // Process from right to left
    for (int i = len - 1; i >= 0; i--) {
        int digit = str[i] - '0';
        
        if (double_digit) {
            digit *= 2;
            if (digit > 9) {
                digit = (digit / 10) + (digit % 10);
            }
        }
        
        sum += digit;
        double_digit = !double_digit;
    }
    
    return sum % 10 == 0;
}

// ============================================================================
// BUSINESS VALIDATION FUNCTIONS
// ============================================================================

/**
 * @brief Check if a string is a valid username
 * @param str String to check
 * @param min_len Minimum length
 * @param max_len Maximum length
 * @return true if valid username, false otherwise
 */
bool isValidUsername(const char* str, size_t min_len, size_t max_len) {
    if (!isLengthInRange(str, min_len, max_len)) return false;
    
    // Username: alphanumeric, underscore, hyphen
    while (*str) {
        char c = *str;
        if (!isalnum((unsigned char)c) && c != '_' && c != '-') {
            return false;
        }
        str++;
    }
    
    return true;
}

/**
 * @brief Check if a string is a valid password
 * @param str String to check
 * @param min_len Minimum length
 * @return true if valid password, false otherwise
 */
bool isValidPassword(const char* str, size_t min_len) {
    if (!isLengthInRange(str, min_len, 128)) return false;
    
    bool has_upper = false;
    bool has_lower = false;
    bool has_digit = false;
    bool has_special = false;
    
    while (*str) {
        char c = *str;
        if (isupper((unsigned char)c)) has_upper = true;
        else if (islower((unsigned char)c)) has_lower = true;
        else if (isdigit((unsigned char)c)) has_digit = true;
        else if (!isalnum((unsigned char)c)) has_special = true;
        str++;
    }
    
    return has_upper && has_lower && has_digit && has_special;
}

/**
 * @brief Check if a string is a valid date in YYYY-MM-DD format
 * @param str String to check
 * @return true if valid date, false otherwise
 */
bool isValidDate(const char* str) {
    if (isEmpty(str)) return false;
    
    int year, month, day;
    if (sscanf(str, "%d-%d-%d", &year, &month, &day) != 3) {
        return false;
    }
    
    // Basic range checks
    if (year < 1900 || year > 2100) return false;
    if (month < 1 || month > 12) return false;
    if (day < 1 || day > 31) return false;
    
    // Month-specific day checks
    if (month == 4 || month == 6 || month == 9 || month == 11) {
        return day <= 30;
    } else if (month == 2) {
        // Leap year check
        bool is_leap = (year % 4 == 0 && year % 100 != 0) || (year % 400 == 0);
        return day <= (is_leap ? 29 : 28);
    }
    
    return true;
}

/**
 * @brief Check if a string is a valid time in HH:MM format
 * @param str String to check
 * @return true if valid time, false otherwise
 */
bool isValidTime(const char* str) {
    if (isEmpty(str)) return false;
    
    int hour, minute;
    if (sscanf(str, "%d:%d", &hour, &minute) != 2) {
        return false;
    }
    
    return (hour >= 0 && hour < 24) && (minute >= 0 && minute < 60);
}

// ============================================================================
// SANITIZATION FUNCTIONS
// ============================================================================

/**
 * @brief Remove all non-digit characters from a string
 * @param str String to sanitize (modified in-place)
 * @return Pointer to the sanitized string
 */
char* sanitizeDigits(char* str) {
    if (str == NULL) return NULL;
    
    char* write = str;
    char* read = str;
    
    while (*read) {
        if (isdigit((unsigned char)*read)) {
            *write++ = *read;
        }
        read++;
    }
    *write = '\0';
    
    return str;
}

/**
 * @brief Remove all non-alphanumeric characters from a string
 * @param str String to sanitize (modified in-place)
 * @return Pointer to the sanitized string
 */
char* sanitizeAlphaNumeric(char* str) {
    if (str == NULL) return NULL;
    
    char* write = str;
    char* read = str;
    
    while (*read) {
        if (isalnum((unsigned char)*read)) {
            *write++ = *read;
        }
        read++;
    }
    *write = '\0';
    
    return str;
}

/**
 * @brief Trim whitespace from both ends of a string
 * @param str String to trim (modified in-place)
 * @return Pointer to the trimmed string
 */
char* trimWhitespace(char* str) {
    if (str == NULL) return NULL;
    
    // Trim leading whitespace
    char* start = str;
    while (isspace((unsigned char)*start)) {
        start++;
    }
    
    // Trim trailing whitespace
    char* end = str + strlen(str) - 1;
    while (end >= start && isspace((unsigned char)*end)) {
        end--;
    }
    
    // Move trimmed string to beginning
    size_t len = end - start + 1;
    memmove(str, start, len);
    str[len] = '\0';
    
    return str;
}

// ============================================================================
// VALIDATION RESULT STRUCTURE
// ============================================================================

typedef struct {
    bool is_valid;
    char error_message[256];
    char sanitized_value[256];
} ValidationResult;

/**
 * @brief Validate and sanitize input with detailed result
 * @param input Input string to validate
 * @param validation_func Function to validate the input
 * @param sanitize_func Function to sanitize the input (optional)
 * @param error_message Error message to use if validation fails
 * @return ValidationResult structure with validation details
 */
ValidationResult validateInput(const char* input, 
                              bool (*validation_func)(const char*),
                              char* (*sanitize_func)(char*),
                              const char* error_message) {
    ValidationResult result = {false, "", ""};
    
    if (input == NULL) {
        strcpy(result.error_message, "Input is NULL");
        return result;
    }
    
    // Copy input for potential sanitization
    strncpy(result.sanitized_value, input, sizeof(result.sanitized_value) - 1);
    result.sanitized_value[sizeof(result.sanitized_value) - 1] = '\0';
    
    // Sanitize if function provided
    if (sanitize_func != NULL) {
        sanitize_func(result.sanitized_value);
    }
    
    // Validate
    result.is_valid = validation_func(result.sanitized_value);
    
    if (!result.is_valid && error_message != NULL) {
        strncpy(result.error_message, error_message, sizeof(result.error_message) - 1);
        result.error_message[sizeof(result.error_message) - 1] = '\0';
    }
    
    return result;
}

// ============================================================================
// TEST FUNCTION
// ============================================================================

void testValidationUtils() {
    printf("=== Validation Utilities Test ===\n\n");
    
    // Test basic validation
    printf("1. Basic Validation:\n");
    printf("   \"\" is empty: %s\n", isEmpty("") ? "Yes" : "No");
    printf("   \"   \" is empty: %s\n", isEmpty("   ") ? "Yes" : "No");
    printf("   \"123\" is numeric: %s\n", isNumeric("123") ? "Yes" : "No");
    printf("   \"-456\" is integer: %s\n", isInteger("-456") ? "Yes" : "No");
    printf("   \"3.14\" is float: %s\n", isFloat("3.14") ? "Yes" : "No");
    printf("   \"abc\" is alpha: %s\n", isAlpha("abc") ? "Yes" : "No");
    printf("   \"abc123\" is alphanumeric: %s\n", isAlphaNumeric("abc123") ? "Yes" : "No");
    
    // Test range validation
    printf("\n2. Range Validation:\n");
    printf("   \"hello\" length 3-10: %s\n", isLengthInRange("hello", 3, 10) ? "Yes" : "No");
    printf("   \"123\" in range 100-200: %s\n", isIntegerInRange("123", 100, 200) ? "Yes" : "No");
    printf("   \"3.14\" in range 3.0-4.0: %s\n", isFloatInRange("3.14", 3.0, 4.0) ? "Yes" : "No");
    
    // Test pattern validation
    printf("\n3. Pattern Validation:\n");
    printf("   \"user@example.com\" is email: %s\n", isValidEmail("user@example.com") ? "Yes" : "No");
    printf("   \"(123) 456-7890\" is phone: %s\n", isValidPhone("(123) 456-7890") ? "Yes" : "No");
    printf("   \"https://example.com\" is URL: %s\n", isValidURL("https://example.com") ? "Yes" : "No");
    printf("   \"192.168.1.1\" is IPv4: %s\n", isValidIPv4("192.168.1.1") ? "Yes" : "No");
    printf("   \"4532015112830366\" is credit card: %s\n", isValidCreditCard("4532015112830366") ? "Yes" : "No");
    
    // Test business validation
    printf("\n4. Business Validation:\n");
    printf("   \"john_doe\" is username (3-20): %s\n", isValidUsername("john_doe", 3, 20) ? "Yes" : "No");
    printf("   \"Password123!\" is password (8+): %s\n", isValidPassword("Password123!", 8) ? "Yes" : "No");
    printf("   \"2026-03-23\" is date: %s\n", isValidDate("2026-03-23") ? "Yes" : "No");
    printf("   \"14:30\" is time: %s\n", isValidTime("14:30") ? "Yes" : "No");
    
    // Test sanitization
    printf("\n5. Sanitization:\n");
    char phone[] = "(123) 456-7890";
    sanitizeDigits(phone);
    printf("   Sanitized phone: \"%s\"\n", phone);
    
    char mixed[] = "abc123!@#";
    sanitizeAlphaNumeric(mixed);
    printf("   Sanitized alphanumeric: \"%s\"\n", mixed);
    
    char spaced[] = "  hello world  ";
    trimWhitespace(spaced);
    printf("   Trimmed whitespace: \"%s\"\n", spaced);
    
    // Test validation result
    printf("\n6. Validation Result:\n");
    ValidationResult result = validateInput("  user@example.com  ", isValidEmail, trimWhitespace, "Invalid email address");
    printf("   Input validation: %s\n", result.is_valid ? "Valid" : "Invalid");
    printf("   Sanitized value: \"%s\"\n", result.sanitized_value);
    printf("   Error message: \"%s\"\n", result.error_message);
    
    printf("\n=== Validation utilities test completed ===\n");
}

int main() {
    testValidationUtils();
    return 0;
}
