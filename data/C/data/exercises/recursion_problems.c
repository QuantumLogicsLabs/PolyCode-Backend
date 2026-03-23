/*
 * File: recursion_problems.c
 * Description: Collection of recursive programming exercises
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

// Exercise 1: Calculate factorial recursively
long long factorial(int n) {
    // Base case: 0! = 1
    if (n == 0) {
        return 1;
    }
    // Recursive case: n! = n * (n-1)!
    return n * factorial(n - 1);
}

// Exercise 2: Calculate nth Fibonacci number
int fibonacci(int n) {
    // Base cases
    if (n <= 0) return 0;
    if (n == 1) return 1;
    
    // Recursive case
    return fibonacci(n - 1) + fibonacci(n - 2);
}

// Exercise 3: Calculate GCD using Euclidean algorithm
int gcd(int a, int b) {
    // Base case
    if (b == 0) {
        return a;
    }
    // Recursive case
    return gcd(b, a % b);
}

// Exercise 4: Calculate power using recursion
double power(double base, int exponent) {
    // Base case
    if (exponent == 0) {
        return 1.0;
    }
    // Handle negative exponents
    if (exponent < 0) {
        return 1.0 / power(base, -exponent);
    }
    // Recursive case
    return base * power(base, exponent - 1);
}

// Exercise 5: Reverse a string recursively
void reverseString(char* str) {
    if (str == NULL || *str == '\0') {
        return;
    }
    
    // Find the length of the string
    int len = 0;
    while (str[len] != '\0') {
        len++;
    }
    
    // Helper recursive function
    void reverseHelper(char* s, int start, int end) {
        if (start >= end) {
            return;
        }
        
        // Swap characters
        char temp = s[start];
        s[start] = s[end];
        s[end] = temp;
        
        // Recurse on the substring
        reverseHelper(s, start + 1, end - 1);
    }
    
    reverseHelper(str, 0, len - 1);
}

// Exercise 6: Check if a string is a palindrome
int isPalindrome(const char* str) {
    // Helper function to check palindrome recursively
    int palindromeHelper(const char* s, int left, int right) {
        if (left >= right) {
            return 1; // Base case: all characters checked
        }
        
        if (s[left] != s[right]) {
            return 0; // Characters don't match
        }
        
        return palindromeHelper(s, left + 1, right - 1);
    }
    
    if (str == NULL) return 0;
    
    int len = strlen(str);
    return palindromeHelper(str, 0, len - 1);
}

// Exercise 7: Calculate sum of array elements recursively
int arraySum(int arr[], int size) {
    // Base case: empty array
    if (size == 0) {
        return 0;
    }
    // Recursive case: last element + sum of rest
    return arr[size - 1] + arraySum(arr, size - 1);
}

// Exercise 8: Find maximum element in array recursively
int findMax(int arr[], int size) {
    // Base case: single element
    if (size == 1) {
        return arr[0];
    }
    
    // Find maximum of rest of array
    int max_rest = findMax(arr, size - 1);
    
    // Return maximum of last element and max of rest
    return (arr[size - 1] > max_rest) ? arr[size - 1] : max_rest;
}

// Exercise 9: Binary search recursively
int binarySearch(int arr[], int left, int right, int target) {
    // Base case: not found
    if (left > right) {
        return -1;
    }
    
    // Calculate middle index
    int mid = left + (right - left) / 2;
    
    // Check if middle element is the target
    if (arr[mid] == target) {
        return mid;
    }
    
    // Search left or right half
    if (arr[mid] > target) {
        return binarySearch(arr, left, mid - 1, target);
    } else {
        return binarySearch(arr, mid + 1, right, target);
    }
}

// Exercise 10: Calculate sum of digits recursively
int sumOfDigits(int n) {
    // Handle negative numbers
    n = abs(n);
    
    // Base case: single digit
    if (n < 10) {
        return n;
    }
    
    // Recursive case: last digit + sum of rest
    return (n % 10) + sumOfDigits(n / 10);
}

// Exercise 11: Tower of Hanoi
void towerOfHanoi(int n, char from_rod, char to_rod, char aux_rod) {
    if (n == 1) {
        printf("Move disk 1 from %c to %c\n", from_rod, to_rod);
        return;
    }
    
    // Move n-1 disks from from_rod to aux_rod
    towerOfHanoi(n - 1, from_rod, aux_rod, to_rod);
    
    // Move the last disk from from_rod to to_rod
    printf("Move disk %d from %c to %c\n", n, from_rod, to_rod);
    
    // Move n-1 disks from aux_rod to to_rod
    towerOfHanoi(n - 1, aux_rod, to_rod, from_rod);
}

// Exercise 12: Generate all permutations of a string
void permutations(char* str, int start, int end) {
    if (start == end) {
        printf("%s\n", str);
        return;
    }
    
    for (int i = start; i <= end; i++) {
        // Swap characters
        char temp = str[start];
        str[start] = str[i];
        str[i] = temp;
        
        // Recurse
        permutations(str, start + 1, end);
        
        // Backtrack
        temp = str[start];
        str[start] = str[i];
        str[i] = temp;
    }
}

// Exercise 13: Check if a number is prime recursively
int isPrimeRecursive(int n, int divisor) {
    // Base cases
    if (n <= 1) return 0;
    if (divisor == 1) return 1;
    if (n % divisor == 0) return 0;
    
    // Recursive case
    return isPrimeRecursive(n, divisor - 1);
}

int isPrime(int n) {
    return isPrimeRecursive(n, n / 2);
}

// Exercise 14: Calculate nth triangular number
int triangularNumber(int n) {
    if (n == 1) return 1;
    return n + triangularNumber(n - 1);
}

// Exercise 15: Print numbers from n to 1 recursively
void printNumbers(int n) {
    if (n <= 0) return;
    
    printf("%d ", n);
    printNumbers(n - 1);
}

// Test function
void testRecursionExercises() {
    printf("=== Recursion Exercises ===\n\n");
    
    // Exercise 1: Factorial
    printf("1. Factorial:\n");
    printf("   5! = %lld\n", factorial(5));
    printf("   0! = %lld\n", factorial(0));
    
    // Exercise 2: Fibonacci
    printf("\n2. Fibonacci:\n");
    printf("   Fibonacci(10) = %d\n", fibonacci(10));
    printf("   Fibonacci(0) = %d\n", fibonacci(0));
    
    // Exercise 3: GCD
    printf("\n3. GCD:\n");
    printf("   GCD(48, 18) = %d\n", gcd(48, 18));
    printf("   GCD(17, 23) = %d\n", gcd(17, 23));
    
    // Exercise 4: Power
    printf("\n4. Power:\n");
    printf("   2^8 = %.0f\n", power(2.0, 8));
    printf("   3^-2 = %.3f\n", power(3.0, -2));
    
    // Exercise 5: Reverse string
    printf("\n5. Reverse string:\n");
    char str1[] = "Hello World";
    printf("   Original: %s\n", str1);
    reverseString(str1);
    printf("   Reversed: %s\n", str1);
    
    // Exercise 6: Palindrome
    printf("\n6. Palindrome check:\n");
    const char* pal1 = "racecar";
    const char* pal2 = "hello";
    printf("   \"%s\" is palindrome: %s\n", pal1, isPalindrome(pal1) ? "Yes" : "No");
    printf("   \"%s\" is palindrome: %s\n", pal2, isPalindrome(pal2) ? "Yes" : "No");
    
    // Exercise 7: Array sum
    printf("\n7. Array sum:\n");
    int arr[] = {1, 2, 3, 4, 5};
    int size = sizeof(arr) / sizeof(arr[0]);
    printf("   Sum of [1,2,3,4,5] = %d\n", arraySum(arr, size));
    
    // Exercise 8: Find maximum
    printf("\n8. Find maximum:\n");
    int arr2[] = {3, 7, 1, 9, 2, 5};
    int size2 = sizeof(arr2) / sizeof(arr2[0]);
    printf("   Maximum of [3,7,1,9,2,5] = %d\n", findMax(arr2, size2));
    
    // Exercise 9: Binary search
    printf("\n9. Binary search:\n");
    int sorted_arr[] = {1, 3, 5, 7, 9, 11, 13};
    int sorted_size = sizeof(sorted_arr) / sizeof(sorted_arr[0]);
    int index = binarySearch(sorted_arr, 0, sorted_size - 1, 7);
    printf("   Search for 7 in sorted array: index %d\n", index);
    
    // Exercise 10: Sum of digits
    printf("\n10. Sum of digits:\n");
    printf("    Sum of digits of 12345 = %d\n", sumOfDigits(12345));
    printf("    Sum of digits of -987 = %d\n", sumOfDigits(-987));
    
    // Exercise 11: Tower of Hanoi
    printf("\n11. Tower of Hanoi (3 disks):\n");
    towerOfHanoi(3, 'A', 'C', 'B');
    
    // Exercise 12: Permutations
    printf("\n12. Permutations of \"ABC\":\n");
    char perm_str[] = "ABC";
    permutations(perm_str, 0, strlen(perm_str) - 1);
    
    // Exercise 13: Prime check
    printf("\n13. Prime check:\n");
    printf("    17 is prime: %s\n", isPrime(17) ? "Yes" : "No");
    printf("    15 is prime: %s\n", isPrime(15) ? "Yes" : "No");
    
    // Exercise 14: Triangular numbers
    printf("\n14. Triangular numbers:\n");
    printf("    5th triangular number = %d\n", triangularNumber(5));
    printf("    7th triangular number = %d\n", triangularNumber(7));
    
    // Exercise 15: Print numbers
    printf("\n15. Print numbers from 5 to 1:\n");
    printf("    ");
    printNumbers(5);
    printf("\n");
    
    printf("\n=== All recursion exercises completed ===\n");
}

int main() {
    testRecursionExercises();
    return 0;
}
