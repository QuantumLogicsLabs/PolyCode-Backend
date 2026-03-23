/*
 * File: unit_testing.c
 * Description: Simple unit testing framework for C
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>

// Test result structure
typedef struct {
    int total_tests;
    int passed_tests;
    int failed_tests;
    char current_test_name[128];
} TestSuite;

// Assertion macros
#define ASSERT_TRUE(condition) \
    do { \
        current_suite->total_tests++; \
        if (condition) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected true, got false\n", current_suite->current_test_name); \
        } \
    } while(0)

#define ASSERT_FALSE(condition) \
    do { \
        current_suite->total_tests++; \
        if (!condition) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected false, got true\n", current_suite->current_test_name); \
        } \
    } while(0)

#define ASSERT_EQ(expected, actual) \
    do { \
        current_suite->total_tests++; \
        if ((expected) == (actual)) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected %d, got %d\n", current_suite->current_test_name, (int)(expected), (int)(actual)); \
        } \
    } while(0)

#define ASSERT_NE(expected, actual) \
    do { \
        current_suite->total_tests++; \
        if ((expected) != (actual)) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected not equal to %d, but got %d\n", current_suite->current_test_name, (int)(expected), (int)(actual)); \
        } \
    } while(0)

#define ASSERT_LT(expected, actual) \
    do { \
        current_suite->total_tests++; \
        if ((expected) < (actual)) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected %d < %d\n", current_suite->current_test_name, (int)(expected), (int)(actual)); \
        } \
    } while(0)

#define ASSERT_GT(expected, actual) \
    do { \
        current_suite->total_tests++; \
        if ((expected) > (actual)) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected %d > %d\n", current_suite->current_test_name, (int)(expected), (int)(actual)); \
        } \
    } while(0)

#define ASSERT_DOUBLE_EQ(expected, actual, tolerance) \
    do { \
        current_suite->total_tests++; \
        if (fabs((expected) - (actual)) < (tolerance)) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected %.6f, got %.6f (tolerance %.6f)\n", current_suite->current_test_name, (double)(expected), (double)(actual), (double)(tolerance)); \
        } \
    } while(0)

#define ASSERT_STR_EQ(expected, actual) \
    do { \
        current_suite->total_tests++; \
        if (strcmp((expected), (actual)) == 0) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected \"%s\", got \"%s\"\n", current_suite->current_test_name, (expected), (actual)); \
        } \
    } while(0)

#define ASSERT_STR_NE(expected, actual) \
    do { \
        current_suite->total_tests++; \
        if (strcmp((expected), (actual)) != 0) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected strings to be different, but both are \"%s\"\n", current_suite->current_test_name, (expected)); \
        } \
    } while(0)

#define ASSERT_NULL(ptr) \
    do { \
        current_suite->total_tests++; \
        if ((ptr) == NULL) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected NULL, got %p\n", current_suite->current_test_name, (void*)(ptr)); \
        } \
    } while(0)

#define ASSERT_NOT_NULL(ptr) \
    do { \
        current_suite->total_tests++; \
        if ((ptr) != NULL) { \
            current_suite->passed_tests++; \
            printf("  PASS: %s\n", current_suite->current_test_name); \
        } else { \
            current_suite->failed_tests++; \
            printf("  FAIL: %s - Expected non-NULL, got NULL\n", current_suite->current_test_name); \
        } \
    } while(0)

// Global test suite pointer
static TestSuite* current_suite = NULL;

// Test suite management
TestSuite* createTestSuite(const char* name) {
    TestSuite* suite = (TestSuite*)malloc(sizeof(TestSuite));
    if (suite != NULL) {
        suite->total_tests = 0;
        suite->passed_tests = 0;
        suite->failed_tests = 0;
        strncpy(suite->current_test_name, "", sizeof(suite->current_test_name));
    }
    return suite;
}

void destroyTestSuite(TestSuite* suite) {
    free(suite);
}

void startTest(TestSuite* suite, const char* test_name) {
    current_suite = suite;
    strncpy(suite->current_test_name, test_name, sizeof(suite->current_test_name) - 1);
    suite->current_test_name[sizeof(suite->current_test_name) - 1] = '\0';
}

void printTestResults(TestSuite* suite) {
    printf("\nTest Results:\n");
    printf("=============\n");
    printf("Total tests: %d\n", suite->total_tests);
    printf("Passed: %d\n", suite->passed_tests);
    printf("Failed: %d\n", suite->failed_tests);
    
    if (suite->failed_tests == 0) {
        printf("All tests PASSED!\n");
    } else {
        printf("Some tests FAILED!\n");
    }
    
    double success_rate = (double)suite->passed_tests / suite->total_tests * 100.0;
    printf("Success rate: %.1f%%\n", success_rate);
}

// Functions to test
int add(int a, int b) {
    return a + b;
}

int multiply(int a, int b) {
    return a * b;
}

int factorial(int n) {
    if (n < 0) return -1;
    if (n == 0 || n == 1) return 1;
    return n * factorial(n - 1);
}

int fibonacci(int n) {
    if (n <= 0) return 0;
    if (n == 1) return 1;
    return fibonacci(n - 1) + fibonacci(n - 2);
}

int isPrime(int n) {
    if (n <= 1) return 0;
    if (n == 2) return 1;
    if (n % 2 == 0) return 0;
    
    for (int i = 3; i * i <= n; i += 2) {
        if (n % i == 0) return 0;
    }
    return 1;
}

char* reverseString(const char* str) {
    if (str == NULL) return NULL;
    
    size_t len = strlen(str);
    char* reversed = (char*)malloc(len + 1);
    if (reversed == NULL) return NULL;
    
    for (size_t i = 0; i < len; i++) {
        reversed[i] = str[len - 1 - i];
    }
    reversed[len] = '\0';
    
    return reversed;
}

// Test functions
void testMathOperations(TestSuite* suite) {
    printf("\nTesting Math Operations:\n");
    
    startTest(suite, "add_positive_numbers");
    ASSERT_EQ(5, add(2, 3));
    
    startTest(suite, "add_negative_numbers");
    ASSERT_EQ(-1, add(-2, 1));
    
    startTest(suite, "multiply_positive_numbers");
    ASSERT_EQ(6, multiply(2, 3));
    
    startTest(suite, "multiply_with_zero");
    ASSERT_EQ(0, multiply(5, 0));
    
    startTest(suite, "factorial_zero");
    ASSERT_EQ(1, factorial(0));
    
    startTest(suite, "factorial_positive");
    ASSERT_EQ(120, factorial(5));
    
    startTest(suite, "factorial_negative");
    ASSERT_EQ(-1, factorial(-1));
}

void testRecursion(TestSuite* suite) {
    printf("\nTesting Recursion:\n");
    
    startTest(suite, "fibonacci_zero");
    ASSERT_EQ(0, fibonacci(0));
    
    startTest(suite, "fibonacci_one");
    ASSERT_EQ(1, fibonacci(1));
    
    startTest(suite, "fibonacci_five");
    ASSERT_EQ(5, fibonacci(5));
    
    startTest(suite, "fibonacci_ten");
    ASSERT_EQ(55, fibonacci(10));
}

void testPrimeNumbers(TestSuite* suite) {
    printf("\nTesting Prime Numbers:\n");
    
    startTest(suite, "isPrime_zero");
    ASSERT_FALSE(isPrime(0));
    
    startTest(suite, "isPrime_one");
    ASSERT_FALSE(isPrime(1));
    
    startTest(suite, "isPrime_two");
    ASSERT_TRUE(isPrime(2));
    
    startTest(suite, "isPrime_seven");
    ASSERT_TRUE(isPrime(7));
    
    startTest(suite, "isPrime_nine");
    ASSERT_FALSE(isPrime(9));
    
    startTest(suite, "isPrime_large_prime");
    ASSERT_TRUE(isPrime(97));
}

void testStringOperations(TestSuite* suite) {
    printf("\nTesting String Operations:\n");
    
    startTest(suite, "reverseString_normal");
    char* reversed = reverseString("hello");
    ASSERT_STR_EQ("olleh", reversed);
    free(reversed);
    
    startTest(suite, "reverseString_empty");
    char* empty_reversed = reverseString("");
    ASSERT_STR_EQ("", empty_reversed);
    free(empty_reversed);
    
    startTest(suite, "reverseString_null");
    char* null_reversed = reverseString(NULL);
    ASSERT_NULL(null_reversed);
    
    startTest(suite, "reverseString_palindrome");
    char* palindrome_reversed = reverseString("racecar");
    ASSERT_STR_EQ("racecar", palindrome_reversed);
    free(palindrome_reversed);
}

void testEdgeCases(TestSuite* suite) {
    printf("\nTesting Edge Cases:\n");
    
    startTest(suite, "add_overflow_test");
    // Note: This might not actually overflow on all systems
    int result = add(INT_MAX, 1);
    ASSERT_TRUE(result < 0); // Typically indicates overflow
    
    startTest(suite, "multiply_large_numbers");
    ASSERT_EQ(1000000, multiply(1000, 1000));
    
    startTest(suite, "double_comparison");
    ASSERT_DOUBLE_EQ(3.14159, 3.14158, 0.001);
    
    startTest(suite, "string_comparison_case_sensitive");
    ASSERT_STR_NE("Hello", "hello");
}

int main() {
    printf("=== Unit Testing Framework Demo ===\n");
    
    // Create test suite
    TestSuite* suite = createTestSuite("Demo Test Suite");
    
    // Run test categories
    testMathOperations(suite);
    testRecursion(suite);
    testPrimeNumbers(suite);
    testStringOperations(suite);
    testEdgeCases(suite);
    
    // Print final results
    printTestResults(suite);
    
    // Cleanup
    destroyTestSuite(suite);
    
    printf("\n=== Unit testing demo completed ===\n");
    
    return (suite->failed_tests == 0) ? 0 : 1;
}
