/*
 * File: documentation.c
 * Description: Examples of proper documentation practices in C
 * 
 * This file demonstrates various documentation styles and practices
 * that should be followed when writing C code.
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>

/**
 * @file documentation.c
 * @brief Documentation examples for C programming
 * @author PolyCode Team
 * @date 2026-03-23
 * @version 1.0
 * 
 * This file contains examples of various documentation styles including:
 * - Function documentation with Doxygen-style comments
 * - Code comments explaining complex algorithms
 * - Inline comments for clarification
 * - Module-level documentation
 */

/**
 * @brief Calculates the factorial of a number
 * 
 * This function computes the factorial of a non-negative integer n.
 * The factorial is defined as n! = n * (n-1) * ... * 1 for n > 0,
 * and 0! = 1.
 * 
 * @param n The non-negative integer for which to calculate factorial
 * @return The factorial of n, or -1 if n is negative (error condition)
 * 
 * @note This function uses recursion, which may not be efficient for large n
 * @warning For n > 20, the result may overflow a 64-bit integer
 * 
 * Example usage:
 * @code
 * int result = factorial(5); // result = 120
 * @endcode
 */
long long factorial(int n) {
    // Input validation: factorial is not defined for negative numbers
    if (n < 0) {
        return -1; // Error code for invalid input
    }
    
    // Base case: 0! and 1! are both equal to 1
    if (n == 0 || n == 1) {
        return 1;
    }
    
    // Recursive case: n! = n * (n-1)!
    return n * factorial(n - 1);
}

/**
 * @brief Determines if a number is prime
 * 
 * A prime number is a natural number greater than 1 that has no positive
 * divisors other than 1 and itself.
 * 
 * This function implements an optimized primality test by checking
 * divisibility only up to the square root of the number and skipping
 * even numbers after checking for divisibility by 2.
 * 
 * @param num The number to test for primality
 * @return 1 if the number is prime, 0 otherwise
 * 
 * @complexity Time: O(√n), Space: O(1)
 * 
 * @see isPrimeOptimized() for an even more efficient version
 */
int isPrime(int num) {
    // Numbers less than 2 are not prime by definition
    if (num < 2) {
        return 0;
    }
    
    // 2 is the only even prime number
    if (num == 2) {
        return 1;
    }
    
    // All other even numbers are not prime
    if (num % 2 == 0) {
        return 0;
    }
    
    // Check odd divisors up to the square root of num
    // If num has a divisor larger than √num, it must also have
    // a corresponding divisor smaller than √num
    for (int i = 3; i * i <= num; i += 2) {
        if (num % i == 0) {
            return 0; // Found a divisor, so num is not prime
        }
    }
    
    return 1; // No divisors found, num is prime
}

/**
 * @brief Structure to represent a 2D point
 * 
 * This structure provides a simple way to represent points in 2D space
 * with floating-point coordinates.
 */
typedef struct {
    double x; /**< X coordinate of the point */
    double y; /**< Y coordinate of the point */
} Point;

/**
 * @brief Creates a new point with specified coordinates
 * 
 * @param x The x-coordinate
 * @param y The y-coordinate
 * @return A new Point structure with the specified coordinates
 */
Point createPoint(double x, double y) {
    Point p;
    p.x = x;
    p.y = y;
    return p;
}

/**
 * @brief Calculates the distance between two points
 * 
 * Uses the Euclidean distance formula:
 * distance = √((x₂-x₁)² + (y₂-y₁)²)
 * 
 * @param p1 The first point
 * @param p2 The second point
 * @return The Euclidean distance between p1 and p2
 * 
 * @pre Both points must be valid (no specific validation performed)
 * @post The return value is always non-negative
 */
double distanceBetweenPoints(Point p1, Point p2) {
    // Calculate differences in coordinates
    double dx = p2.x - p1.x;
    double dy = p2.y - p1.y;
    
    // Apply Pythagorean theorem
    return sqrt(dx * dx + dy * dy);
}

/**
 * @brief Structure to represent a circle
 * 
 * A circle is defined by its center point and radius.
 */
typedef struct {
    Point center; /**< Center point of the circle */
    double radius; /**< Radius of the circle (must be positive) */
} Circle;

/**
 * @brief Creates a new circle
 * 
 * @param center The center point of the circle
 * @param radius The radius of the circle (must be positive)
 * @return A new Circle structure
 * 
 * @warning No validation is performed on the radius value
 */
Circle createCircle(Point center, double radius) {
    Circle c;
    c.center = center;
    c.radius = radius;
    return c;
}

/**
 * @brief Calculates the area of a circle
 * 
 * Uses the formula: area = π × r²
 * 
 * @param circle The circle whose area to calculate
 * @return The area of the circle
 * 
 * @note Uses M_PI constant from math.h for π
 */
double circleArea(Circle circle) {
    return M_PI * circle.radius * circle.radius;
}

/**
 * @brief Calculates the circumference of a circle
 * 
 * Uses the formula: circumference = 2 × π × r
 * 
 * @param circle The circle whose circumference to calculate
 * @return The circumference of the circle
 */
double circleCircumference(Circle circle) {
    return 2 * M_PI * circle.radius;
}

/**
 * @brief Determines if a point lies inside a circle
 * 
 * A point is inside a circle if the distance from the point to the
 * circle's center is less than the circle's radius.
 * 
 * @param circle The circle to test against
 * @param point The point to test
 * @return 1 if the point is inside the circle, 0 otherwise
 * 
 * @see distanceBetweenPoints() for the distance calculation
 */
int isPointInsideCircle(Circle circle, Point point) {
    double distance = distanceBetweenPoints(circle.center, point);
    return distance < circle.radius;
}

/**
 * @brief String utility functions
 * 
 * This section contains utility functions for string manipulation
 * with proper documentation.
 */

/**
 * @brief Safely copies a string with length checking
 * 
 * This function copies up to dest_size-1 characters from src to dest
 * and ensures the destination string is null-terminated.
 * 
 * @param dest Destination buffer
 * @param src Source string
 * @param dest_size Size of destination buffer
 * @return Pointer to destination buffer
 * 
 * @warning dest must have enough space for src plus null terminator
 * @note This is safer than strcpy() as it prevents buffer overflow
 */
char* safeStringCopy(char* dest, const char* src, size_t dest_size) {
    if (dest == NULL || src == NULL || dest_size == 0) {
        return dest; // Invalid parameters, return as-is
    }
    
    // Copy up to dest_size-1 characters
    strncpy(dest, src, dest_size - 1);
    
    // Ensure null termination
    dest[dest_size - 1] = '\0';
    
    return dest;
}

/**
 * @brief Demonstrates the usage of documented functions
 * 
 * This function serves as a comprehensive example of how to use
 * the various functions and structures defined in this file.
 * 
 * @return 0 on successful execution
 */
int main() {
    printf("=== Documentation Examples ===\n\n");
    
    // Example 1: Using factorial function
    printf("1. Factorial Examples:\n");
    int n = 5;
    long long fact = factorial(n);
    if (fact != -1) {
        printf("   %d! = %lld\n", n, fact);
    } else {
        printf("   Error: Invalid input for factorial\n");
    }
    
    // Example 2: Prime number testing
    printf("\n2. Prime Number Testing:\n");
    int test_numbers[] = {2, 3, 4, 17, 20, 97};
    int num_tests = sizeof(test_numbers) / sizeof(test_numbers[0]);
    
    for (int i = 0; i < num_tests; i++) {
        int num = test_numbers[i];
        printf("   %d is %s\n", num, isPrime(num) ? "prime" : "not prime");
    }
    
    // Example 3: Point and Circle operations
    printf("\n3. Point and Circle Operations:\n");
    
    // Create points
    Point p1 = createPoint(0.0, 0.0);
    Point p2 = createPoint(3.0, 4.0);
    
    // Calculate distance
    double dist = distanceBetweenPoints(p1, p2);
    printf("   Distance between (0,0) and (3,4): %.2f\n", dist);
    
    // Create circle
    Circle circle = createCircle(p1, 5.0);
    
    // Calculate circle properties
    printf("   Circle with center (0,0) and radius 5.0:\n");
    printf("   Area: %.2f\n", circleArea(circle));
    printf("   Circumference: %.2f\n", circleCircumference(circle));
    
    // Test if point is inside circle
    Point test_point = createPoint(3.0, 3.0);
    printf("   Point (3,3) is %s the circle\n", 
           isPointInsideCircle(circle, test_point) ? "inside" : "outside");
    
    // Example 4: String operations
    printf("\n4. String Operations:\n");
    char buffer[50];
    const char* source = "Hello, Documentation!";
    
    safeStringCopy(buffer, source, sizeof(buffer));
    printf("   Copied string: \"%s\"\n", buffer);
    
    // Demonstrate buffer overflow protection
    char small_buffer[10];
    safeStringCopy(small_buffer, source, sizeof(small_buffer));
    printf("   Truncated string: \"%s\"\n", small_buffer);
    
    printf("\n=== Documentation examples completed ===\n");
    
    return 0;
}
