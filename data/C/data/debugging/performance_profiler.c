/*
 * File: performance_profiler.c
 * Description: Simple performance profiling tools for C programs
 */

#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <string.h>
#include <sys/time.h>

// High-resolution timer
typedef struct {
    struct timeval start;
    struct timeval end;
} Timer;

// Performance statistics
typedef struct {
    double total_time;
    double min_time;
    double max_time;
    long call_count;
    char function_name[64];
} PerformanceStats;

// Global performance tracking
#define MAX_TRACKED_FUNCTIONS 50
static PerformanceStats performance_data[MAX_TRACKED_FUNCTIONS];
static int tracked_function_count = 0;

// Timer functions
void startTimer(Timer* timer) {
    gettimeofday(&timer->start, NULL);
}

void stopTimer(Timer* timer) {
    gettimeofday(&timer->end, NULL);
}

double getElapsedTime(Timer* timer) {
    double start_sec = timer->start.tv_sec + timer->start.tv_usec / 1000000.0;
    double end_sec = timer->end.tv_sec + timer->end.tv_usec / 1000000.0;
    return end_sec - start_sec;
}

// Find or create performance stats entry
PerformanceStats* getStatsEntry(const char* function_name) {
    // Look for existing entry
    for (int i = 0; i < tracked_function_count; i++) {
        if (strcmp(performance_data[i].function_name, function_name) == 0) {
            return &performance_data[i];
        }
    }
    
    // Create new entry if space available
    if (tracked_function_count < MAX_TRACKED_FUNCTIONS) {
        PerformanceStats* stats = &performance_data[tracked_function_count];
        strncpy(stats->function_name, function_name, sizeof(stats->function_name) - 1);
        stats->function_name[sizeof(stats->function_name) - 1] = '\0';
        stats->total_time = 0.0;
        stats->min_time = 1e9; // Very large initial value
        stats->max_time = 0.0;
        stats->call_count = 0;
        tracked_function_count++;
        return stats;
    }
    
    return NULL; // No space available
}

// Performance tracking macros
#define PROFILE_START(name) \
    static Timer timer_##name; \
    startTimer(&timer_##name);

#define PROFILE_END(name) \
    stopTimer(&timer_##name); \
    do { \
        PerformanceStats* stats = getStatsEntry(#name); \
        if (stats != NULL) { \
            double elapsed = getElapsedTime(&timer_##name); \
            stats->total_time += elapsed; \
            stats->call_count++; \
            if (elapsed < stats->min_time) stats->min_time = elapsed; \
            if (elapsed > stats->max_time) stats->max_time = elapsed; \
        } \
    } while(0)

// Function to print performance report
void printPerformanceReport() {
    printf("\n=== Performance Report ===\n");
    printf("%-20s %-10s %-12s %-12s %-12s %-12s\n", 
           "Function", "Calls", "Total (s)", "Avg (ms)", "Min (ms)", "Max (ms)");
    printf("------------------------------------------------------------------------\n");
    
    for (int i = 0; i < tracked_function_count; i++) {
        PerformanceStats* stats = &performance_data[i];
        double avg_time_ms = (stats->total_time / stats->call_count) * 1000.0;
        double min_time_ms = stats->min_time * 1000.0;
        double max_time_ms = stats->max_time * 1000.0;
        
        printf("%-20s %-10ld %-12.6f %-12.3f %-12.3f %-12.3f\n",
               stats->function_name,
               stats->call_count,
               stats->total_time,
               avg_time_ms,
               min_time_ms,
               max_time_ms);
    }
    printf("========================================================================\n");
}

// Function to reset performance data
void resetPerformanceData() {
    tracked_function_count = 0;
    memset(performance_data, 0, sizeof(performance_data));
}

// Test functions with different performance characteristics
void quickFunction() {
    PROFILE_START(quickFunction);
    // Simulate quick operation
    volatile int sum = 0;
    for (int i = 0; i < 100; i++) {
        sum += i;
    }
    PROFILE_END(quickFunction);
}

void mediumFunction() {
    PROFILE_START(mediumFunction);
    // Simulate medium operation
    volatile int sum = 0;
    for (int i = 0; i < 10000; i++) {
        sum += i * i;
    }
    PROFILE_END(mediumFunction);
}

void slowFunction() {
    PROFILE_START(slowFunction);
    // Simulate slow operation
    volatile int sum = 0;
    for (int i = 0; i < 1000000; i++) {
        sum += i * i * i;
    }
    PROFILE_END(slowFunction);
}

void variableFunction(int iterations) {
    PROFILE_START(variableFunction);
    // Simulate variable operation
    volatile int sum = 0;
    for (int i = 0; i < iterations; i++) {
        sum += i;
    }
    PROFILE_END(variableFunction);
}

// Memory allocation tracking
typedef struct {
    void* ptr;
    size_t size;
    const char* file;
    int line;
    struct timeval alloc_time;
} AllocationRecord;

#define MAX_ALLOCATIONS 1000
static AllocationRecord allocations[MAX_ALLOCATIONS];
static int allocation_count = 0;
static size_t total_allocated = 0;

void* tracked_malloc(size_t size, const char* file, int line) {
    void* ptr = malloc(size);
    if (ptr != NULL && allocation_count < MAX_ALLOCATIONS) {
        allocations[allocation_count].ptr = ptr;
        allocations[allocation_count].size = size;
        allocations[allocation_count].file = file;
        allocations[allocation_count].line = line;
        gettimeofday(&allocations[allocation_count].alloc_time, NULL);
        total_allocated += size;
        allocation_count++;
    }
    return ptr;
}

void tracked_free(void* ptr) {
    if (ptr == NULL) return;
    
    for (int i = 0; i < allocation_count; i++) {
        if (allocations[i].ptr == ptr) {
            total_allocated -= allocations[i].size;
            
            // Remove from tracking list
            for (int j = i; j < allocation_count - 1; j++) {
                allocations[j] = allocations[j + 1];
            }
            allocation_count--;
            break;
        }
    }
    
    free(ptr);
}

#define TRACKED_MALLOC(size) tracked_malloc(size, __FILE__, __LINE__)
#define TRACKED_FREE(ptr) tracked_free(ptr)

// Memory usage report
void printMemoryReport() {
    printf("\n=== Memory Usage Report ===\n");
    printf("Total allocated: %zu bytes\n", total_allocated);
    printf("Active allocations: %d\n", allocation_count);
    
    if (allocation_count > 0) {
        printf("Outstanding allocations:\n");
        for (int i = 0; i < allocation_count; i++) {
            printf("  %p (%zu bytes) at %s:%d\n", 
                   allocations[i].ptr, allocations[i].size,
                   allocations[i].file, allocations[i].line);
        }
    }
    printf("========================\n");
}

// Function to demonstrate memory tracking
void memoryTestFunction() {
    PROFILE_START(memoryTestFunction);
    
    // Allocate some memory
    char* buffer1 = (char*)TRACKED_MALLOC(1024);
    char* buffer2 = (char*)TRACKED_MALLOC(2048);
    char* buffer3 = (char*)TRACKED_MALLOC(512);
    
    // Use the memory
    strcpy(buffer1, "Test data 1");
    strcpy(buffer2, "Test data 2");
    strcpy(buffer3, "Test data 3");
    
    // Free some memory
    TRACKED_FREE(buffer2);
    
    PROFILE_END(memoryTestFunction);
}

// Benchmark function
void benchmarkFunction(void (*func)(void), const char* name, int iterations) {
    printf("\nBenchmarking %s (%d iterations):\n", name, iterations);
    
    Timer total_timer;
    startTimer(&total_timer);
    
    for (int i = 0; i < iterations; i++) {
        func();
    }
    
    stopTimer(&total_timer);
    
    double total_time = getElapsedTime(&total_timer);
    double avg_time = total_time / iterations;
    
    printf("  Total time: %.6f seconds\n", total_time);
    printf("  Average time: %.6f seconds\n", avg_time);
    printf("  Operations per second: %.2f\n", 1.0 / avg_time);
}

int main() {
    printf("=== Performance Profiler Demo ===\n");
    
    // Reset performance data
    resetPerformanceData();
    
    // Run test functions multiple times
    printf("Running test functions...\n");
    
    for (int i = 0; i < 100; i++) {
        quickFunction();
    }
    
    for (int i = 0; i < 50; i++) {
        mediumFunction();
    }
    
    for (int i = 0; i < 10; i++) {
        slowFunction();
    }
    
    // Variable function with different parameters
    for (int i = 0; i < 20; i++) {
        variableFunction(1000 + i * 100);
    }
    
    // Memory tracking test
    memoryTestFunction();
    
    // Print performance report
    printPerformanceReport();
    
    // Print memory report
    printMemoryReport();
    
    // Benchmark individual functions
    benchmarkFunction(quickFunction, "quickFunction", 1000);
    benchmarkFunction(mediumFunction, "mediumFunction", 100);
    benchmarkFunction(slowFunction, "slowFunction", 10);
    
    // Clean up remaining memory
    for (int i = 0; i < allocation_count; i++) {
        TRACKED_FREE(allocations[i].ptr);
    }
    
    printf("\n=== Performance profiler demo completed ===\n");
    
    return 0;
}
