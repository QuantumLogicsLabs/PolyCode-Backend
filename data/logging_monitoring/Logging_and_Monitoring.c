#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <stdarg.h>
#include <pthread.h>

// =============================================================================
// LOGGING AND MONITORING FUNDAMENTALS
// =============================================================================

#define MAX_LOG_MESSAGE_SIZE 1024
#define MAX_LOG_FILE_NAME_LENGTH 256
#define MAX_LOG_CATEGORY_LENGTH 64
#define MAX_LOG_BUFFER_SIZE 10000
#define MAX_LOG_HANDLERS 10

// Log levels
typedef enum {
    LOG_LEVEL_TRACE = 0,
    LOG_LEVEL_DEBUG = 1,
    LOG_LEVEL_INFO = 2,
    LOG_LEVEL_WARN = 3,
    LOG_LEVEL_ERROR = 4,
    LOG_LEVEL_FATAL = 5,
    LOG_LEVEL_OFF = 6
} LogLevel;

// Log entry structure
typedef struct {
    time_t timestamp;
    LogLevel level;
    char category[MAX_LOG_CATEGORY_LENGTH];
    char message[MAX_LOG_MESSAGE_SIZE];
    char function[64];
    int line;
    char file[128];
    pthread_t thread_id;
} LogEntry;

// Log handler function type
typedef void (*LogHandler)(const LogEntry* entry);

// Logger configuration
typedef struct {
    LogLevel min_level;
    char log_file[MAX_LOG_FILE_NAME_LENGTH];
    int console_output;
    int file_output;
    int buffer_size;
    int async_logging;
    pthread_mutex_t mutex;
    LogHandler handlers[MAX_LOG_HANDLERS];
    int handler_count;
} LoggerConfig;

// Global logger instance
static LoggerConfig g_logger = {
    .min_level = LOG_LEVEL_INFO,
    .console_output = 1,
    .file_output = 0,
    .buffer_size = MAX_LOG_BUFFER_SIZE,
    .async_logging = 0,
    .handler_count = 0
};

// =============================================================================
// LOG LEVEL MANAGEMENT
// =============================================================================

// Get log level name
const char* getLogLevelName(LogLevel level) {
    switch (level) {
        case LOG_LEVEL_TRACE: return "TRACE";
        case LOG_LEVEL_DEBUG: return "DEBUG";
        case LOG_LEVEL_INFO:  return "INFO";
        case LOG_LEVEL_WARN:  return "WARN";
        case LOG_LEVEL_ERROR: return "ERROR";
        case LOG_LEVEL_FATAL: return "FATAL";
        case LOG_LEVEL_OFF:  return "OFF";
        default: return "UNKNOWN";
    }
}

// Get log level from string
LogLevel getLogLevelFromString(const char* level_str) {
    if (strcmp(level_str, "TRACE") == 0) return LOG_LEVEL_TRACE;
    if (strcmp(level_str, "DEBUG") == 0) return LOG_LEVEL_DEBUG;
    if (strcmp(level_str, "INFO") == 0) return LOG_LEVEL_INFO;
    if (strcmp(level_str, "WARN") == 0) return LOG_LEVEL_WARN;
    if (strcmp(level_str, "ERROR") == 0) return LOG_LEVEL_ERROR;
    if (strcmp(level_str, "FATAL") == 0) return LOG_LEVEL_FATAL;
    if (strcmp(level_str, "OFF") == 0) return LOG_LEVEL_OFF;
    return LOG_LEVEL_INFO; // Default
}

// Check if log level is enabled
int isLogLevelEnabled(LogLevel level) {
    return level >= g_logger.min_level;
}

// =============================================================================
// TIMESTAMP FORMATTING
// =============================================================================

// Format timestamp
void formatTimestamp(time_t timestamp, char* buffer, size_t buffer_size) {
    struct tm* tm_info = localtime(&timestamp);
    strftime(buffer, buffer_size, "%Y-%m-%d %H:%M:%S", tm_info);
}

// Format timestamp with milliseconds
void formatTimestampWithMs(time_t timestamp, char* buffer, size_t buffer_size) {
    struct tm* tm_info = localtime(&timestamp);
    
    // Get milliseconds (simplified - in real implementation would use platform-specific APIs)
    #ifdef _WIN32
        SYSTEMTIME st;
        GetLocalTime(&st);
        snprintf(buffer, buffer_size, "%04d-%02d-%02d %02d:%02d:%02d.%03d",
                tm_info->tm_year + 1900, tm_info->tm_mon + 1, tm_info->tm_mday,
                tm_info->tm_hour, tm_info->tm_min, tm_info->tm_sec, st.wMilliseconds);
    #else
        struct timespec ts;
        clock_gettime(CLOCK_REALTIME, &ts);
        int ms = ts.tv_nsec / 1000000;
        snprintf(buffer, buffer_size, "%04d-%02d-%02d %02d:%02d:%02d.%03d",
                tm_info->tm_year + 1900, tm_info->tm_mon + 1, tm_info->tm_mday,
                tm_info->tm_hour, tm_info->tm_min, tm_info->tm_sec, ms);
    #endif
}

// =============================================================================
// LOG ENTRY CREATION
// =============================================================================

// Create log entry
void createLogEntry(LogEntry* entry, LogLevel level, const char* category,
                    const char* message, const char* function, int line, const char* file) {
    entry->timestamp = time(NULL);
    entry->level = level;
    entry->thread_id = pthread_self();
    
    if (category) {
        strncpy(entry->category, category, sizeof(entry->category) - 1);
        entry->category[sizeof(entry->category) - 1] = '\0';
    } else {
        strcpy(entry->category, "DEFAULT");
    }
    
    if (message) {
        strncpy(entry->message, message, sizeof(entry->message) - 1);
        entry->message[sizeof(entry->message) - 1] = '\0';
    } else {
        strcpy(entry->message, "");
    }
    
    if (function) {
        strncpy(entry->function, function, sizeof(entry->function) - 1);
        entry->function[sizeof(entry->function) - 1] = '\0';
    } else {
        strcpy(entry->function, "");
    }
    
    entry->line = line;
    
    if (file) {
        // Extract just the filename from path
        const char* filename = strrchr(file, '/');
        if (!filename) filename = strrchr(file, '\\');
        if (!filename) filename = file;
        else filename++;
        
        strncpy(entry->file, filename, sizeof(entry->file) - 1);
        entry->file[sizeof(entry->file) - 1] = '\0';
    } else {
        strcpy(entry->file, "");
    }
}

// =============================================================================
// LOG HANDLERS
// =============================================================================

// Console log handler
void consoleLogHandler(const LogEntry* entry) {
    char timestamp[64];
    formatTimestampWithMs(entry->timestamp, timestamp, sizeof(timestamp));
    
    // Color coding for different levels
    const char* color_code = "";
    const char* reset_code = "";
    
    #ifdef _WIN32
        // Windows console color codes would go here
    #else
        switch (entry->level) {
            case LOG_LEVEL_TRACE: color_code = "\033[37m"; break;  // White
            case LOG_LEVEL_DEBUG: color_code = "\033[36m"; break;  // Cyan
            case LOG_LEVEL_INFO:  color_code = "\033[32m"; break;  // Green
            case LOG_LEVEL_WARN:  color_code = "\033[33m"; break;  // Yellow
            case LOG_LEVEL_ERROR: color_code = "\033[31m"; break;  // Red
            case LOG_LEVEL_FATAL: color_code = "\033[35m"; break;  // Magenta
            default: break;
        }
        reset_code = "\033[0m";
    #endif
    
    printf("%s [%s] %s%s [%s] %s (%s:%d in %s)%s\n",
           timestamp, getLogLevelName(entry->level),
           color_code, entry->category, reset_code,
           entry->message, entry->file, entry->line, entry->function);
}

// File log handler
void fileLogHandler(const LogEntry* entry) {
    if (strlen(g_logger.log_file) == 0) {
        return;
    }
    
    FILE* file = fopen(g_logger.log_file, "a");
    if (!file) {
        return;
    }
    
    char timestamp[64];
    formatTimestamp(entry->timestamp, timestamp, sizeof(timestamp));
    
    fprintf(file, "%s [%s] [%s] %s (%s:%d in %s)\n",
            timestamp, getLogLevelName(entry->level),
            entry->category, entry->message,
            entry->file, entry->line, entry->function);
    
    fclose(file);
}

// JSON log handler
void jsonLogHandler(const LogEntry* entry) {
    char timestamp[64];
    formatTimestamp(entry->timestamp, timestamp, sizeof(timestamp));
    
    printf("{\n");
    printf("  \"timestamp\": \"%s\",\n", timestamp);
    printf("  \"level\": \"%s\",\n", getLogLevelName(entry->level));
    printf("  \"category\": \"%s\",\n", entry->category);
    printf("  \"message\": \"%s\",\n", entry->message);
    printf("  \"function\": \"%s\",\n", entry->function);
    printf("  \"line\": %d,\n", entry->line);
    printf("  \"file\": \"%s\",\n", entry->file);
    printf("  \"thread_id\": %lu\n", (unsigned long)entry->thread_id);
    printf("}\n");
}

// Syslog handler (simplified)
void syslogHandler(const LogEntry* entry) {
    // In a real implementation, this would use syslog()
    printf("[SYSLOG] %s [%s] %s\n", 
           getLogLevelName(entry->level), entry->category, entry->message);
}

// =============================================================================
// LOGGER CONFIGURATION
// =============================================================================

// Initialize logger
void initLogger(const char* log_file, LogLevel min_level, int console_output, int file_output) {
    pthread_mutex_init(&g_logger.mutex, NULL);
    
    g_logger.min_level = min_level;
    g_logger.console_output = console_output;
    g_logger.file_output = file_output;
    
    if (log_file) {
        strncpy(g_logger.log_file, log_file, sizeof(g_logger.log_file) - 1);
        g_logger.log_file[sizeof(g_logger.log_file) - 1] = '\0';
    } else {
        strcpy(g_logger.log_file, "");
    }
    
    // Register default handlers
    if (console_output) {
        g_logger.handlers[g_logger.handler_count++] = consoleLogHandler;
    }
    
    if (file_output) {
        g_logger.handlers[g_logger.handler_count++] = fileLogHandler;
    }
}

// Cleanup logger
void cleanupLogger() {
    pthread_mutex_destroy(&g_logger.mutex);
    memset(&g_logger, 0, sizeof(g_logger));
}

// Set log level
void setLogLevel(LogLevel level) {
    pthread_mutex_lock(&g_logger.mutex);
    g_logger.min_level = level;
    pthread_mutex_unlock(&g_logger.mutex);
}

// Add custom handler
void addLogHandler(LogHandler handler) {
    pthread_mutex_lock(&g_logger.mutex);
    
    if (g_logger.handler_count < MAX_LOG_HANDLERS) {
        g_logger.handlers[g_logger.handler_count++] = handler;
    }
    
    pthread_mutex_unlock(&g_logger.mutex);
}

// =============================================================================
// CORE LOGGING FUNCTION
// =============================================================================

// Core logging function
void logMessage(LogLevel level, const char* category, const char* function,
               int line, const char* file, const char* format, ...) {
    if (!isLogLevelEnabled(level)) {
        return;
    }
    
    // Format message
    char message[MAX_LOG_MESSAGE_SIZE];
    va_list args;
    va_start(args, format);
    vsnprintf(message, sizeof(message), format, args);
    va_end(args);
    
    // Create log entry
    LogEntry entry;
    createLogEntry(&entry, level, category, message, function, line, file);
    
    // Process handlers
    pthread_mutex_lock(&g_logger.mutex);
    
    for (int i = 0; i < g_logger.handler_count; i++) {
        if (g_logger.handlers[i]) {
            g_logger.handlers[i](&entry);
        }
    }
    
    pthread_mutex_unlock(&g_logger.mutex);
}

// =============================================================================
// CONVENIENCE MACROS
// =============================================================================

#define LOG_TRACE(category, ...) \
    logMessage(LOG_LEVEL_TRACE, category, __FUNCTION__, __LINE__, __FILE__, __VA_ARGS__)

#define LOG_DEBUG(category, ...) \
    logMessage(LOG_LEVEL_DEBUG, category, __FUNCTION__, __LINE__, __FILE__, __VA_ARGS__)

#define LOG_INFO(category, ...) \
    logMessage(LOG_LEVEL_INFO, category, __FUNCTION__, __LINE__, __FILE__, __VA_ARGS__)

#define LOG_WARN(category, ...) \
    logMessage(LOG_LEVEL_WARN, category, __FUNCTION__, __LINE__, __FILE__, __VA_ARGS__)

#define LOG_ERROR(category, ...) \
    logMessage(LOG_LEVEL_ERROR, category, __FUNCTION__, __LINE__, __FILE__, __VA_ARGS__)

#define LOG_FATAL(category, ...) \
    logMessage(LOG_LEVEL_FATAL, category, __FUNCTION__, __LINE__, __FILE__, __VA_ARGS__)

// =============================================================================
// MONITORING AND METRICS
// =============================================================================

// Performance metrics structure
typedef struct {
    char name[64];
    double value;
    time_t timestamp;
    char unit[16];
} Metric;

// Metrics collector
typedef struct {
    Metric metrics[1000];
    int metric_count;
    pthread_mutex_t mutex;
} MetricsCollector;

static MetricsCollector g_metrics = { .metric_count = 0 };

// Initialize metrics collector
void initMetricsCollector() {
    pthread_mutex_init(&g_metrics.mutex, NULL);
    g_metrics.metric_count = 0;
}

// Cleanup metrics collector
void cleanupMetricsCollector() {
    pthread_mutex_destroy(&g_metrics.mutex);
    memset(&g_metrics, 0, sizeof(g_metrics));
}

// Record metric
void recordMetric(const char* name, double value, const char* unit) {
    pthread_mutex_lock(&g_metrics.mutex);
    
    if (g_metrics.metric_count < 1000) {
        Metric* metric = &g_metrics.metrics[g_metrics.metric_count];
        
        strncpy(metric->name, name, sizeof(metric->name) - 1);
        metric->name[sizeof(metric->name) - 1] = '\0';
        
        metric->value = value;
        metric->timestamp = time(NULL);
        
        if (unit) {
            strncpy(metric->unit, unit, sizeof(metric->unit) - 1);
            metric->unit[sizeof(metric->unit) - 1] = '\0';
        } else {
            strcpy(metric->unit, "");
        }
        
        g_metrics.metric_count++;
    }
    
    pthread_mutex_unlock(&g_metrics.mutex);
}

// Get metric value
double getMetric(const char* name) {
    pthread_mutex_lock(&g_metrics.mutex);
    
    double value = 0.0;
    for (int i = 0; i < g_metrics.metric_count; i++) {
        if (strcmp(g_metrics.metrics[i].name, name) == 0) {
            value = g_metrics.metrics[i].value;
            break;
        }
    }
    
    pthread_mutex_unlock(&g_metrics.mutex);
    return value;
}

// Print all metrics
void printMetrics() {
    pthread_mutex_lock(&g_metrics.mutex);
    
    printf("=== METRICS ===\n");
    for (int i = 0; i < g_metrics.metric_count; i++) {
        Metric* metric = &g_metrics.metrics[i];
        char timestamp[64];
        formatTimestamp(metric->timestamp, timestamp, sizeof(timestamp));
        
        printf("%s: %.2f %s (%s)\n", 
               metric->name, metric->value, metric->unit, timestamp);
    }
    
    pthread_mutex_unlock(&g_metrics.mutex);
}

// =============================================================================
// PERFORMANCE MONITORING
// =============================================================================

// Performance timer
typedef struct {
    char name[64];
    clock_t start_time;
    int active;
} PerformanceTimer;

static PerformanceTimer g_timers[100];
static int g_timer_count = 0;
static pthread_mutex_t g_timer_mutex = PTHREAD_MUTEX_INITIALIZER;

// Start performance timer
void startTimer(const char* name) {
    pthread_mutex_lock(&g_timer_mutex);
    
    if (g_timer_count < 100) {
        PerformanceTimer* timer = &g_timers[g_timer_count];
        
        strncpy(timer->name, name, sizeof(timer->name) - 1);
        timer->name[sizeof(timer->name) - 1] = '\0';
        
        timer->start_time = clock();
        timer->active = 1;
        
        g_timer_count++;
    }
    
    pthread_mutex_unlock(&g_timer_mutex);
}

// Stop performance timer and record metric
void stopTimer(const char* name) {
    pthread_mutex_lock(&g_timer_mutex);
    
    clock_t end_time = clock();
    
    for (int i = 0; i < g_timer_count; i++) {
        if (strcmp(g_timers[i].name, name) == 0 && g_timers[i].active) {
            double elapsed = ((double)(end_time - g_timers[i].start_time)) / CLOCKS_PER_SEC;
            
            char metric_name[128];
            snprintf(metric_name, sizeof(metric_name), "timer_%s", name);
            recordMetric(metric_name, elapsed, "seconds");
            
            g_timers[i].active = 0;
            break;
        }
    }
    
    pthread_mutex_unlock(&g_timer_mutex);
}

// Function execution time macro
#define TIME_FUNCTION(category) \
    PerformanceTimer __timer; \
    startTimer(__FUNCTION__); \
    LOG_DEBUG(category, "Starting function: %s", __FUNCTION__); \
    // Function body here
    stopTimer(__FUNCTION__); \
    LOG_DEBUG(category, "Completed function: %s", __FUNCTION__)

// =============================================================================
// MEMORY MONITORING
// =============================================================================

// Memory usage structure
typedef struct {
    size_t allocated;
    size_t freed;
    size_t peak_usage;
    int allocation_count;
    int free_count;
} MemoryUsage;

static MemoryUsage g_memory_usage = {0};
static pthread_mutex_t g_memory_mutex = PTHREAD_MUTEX_INITIALIZER;

// Custom malloc wrapper
void* monitoredMalloc(size_t size) {
    void* ptr = malloc(size);
    
    if (ptr) {
        pthread_mutex_lock(&g_memory_mutex);
        
        g_memory_usage.allocated += size;
        g_memory_usage.allocation_count++;
        
        if (g_memory_usage.allocated - g_memory_usage.freed > g_memory_usage.peak_usage) {
            g_memory_usage.peak_usage = g_memory_usage.allocated - g_memory_usage.freed;
        }
        
        pthread_mutex_unlock(&g_memory_mutex);
    }
    
    return ptr;
}

// Custom free wrapper
void monitoredFree(void* ptr) {
    if (ptr) {
        pthread_mutex_lock(&g_memory_mutex);
        
        g_memory_usage.free_count++;
        // Note: We can't easily track freed size without additional bookkeeping
        
        pthread_mutex_unlock(&g_memory_mutex);
        
        free(ptr);
    }
}

// Get memory usage statistics
void getMemoryUsage(size_t* allocated, size_t* peak, int* alloc_count, int* free_count) {
    pthread_mutex_lock(&g_memory_mutex);
    
    if (allocated) *allocated = g_memory_usage.allocated;
    if (peak) *peak = g_memory_usage.peak_usage;
    if (alloc_count) *alloc_count = g_memory_usage.allocation_count;
    if (free_count) *free_count = g_memory_usage.free_count;
    
    pthread_mutex_unlock(&g_memory_mutex);
}

// Print memory usage
void printMemoryUsage() {
    size_t allocated, peak;
    int alloc_count, free_count;
    
    getMemoryUsage(&allocated, &peak, &alloc_count, &free_count);
    
    printf("=== MEMORY USAGE ===\n");
    printf("Allocated: %zu bytes\n", allocated);
    printf("Peak usage: %zu bytes\n", peak);
    printf("Allocations: %d\n", alloc_count);
    printf("Frees: %d\n", free_count);
    printf("Current usage: %zu bytes\n", allocated - (peak - allocated)); // Approximation
}

// =============================================================================
// HEALTH MONITORING
// =============================================================================

// Health status
typedef enum {
    HEALTH_STATUS_HEALTHY,
    HEALTH_STATUS_WARNING,
    HEALTH_STATUS_CRITICAL,
    HEALTH_STATUS_UNKNOWN
} HealthStatus;

// Health check function type
typedef HealthStatus (*HealthCheck)(void);

// Health monitor
typedef struct {
    char name[64];
    HealthCheck check_function;
    HealthStatus last_status;
    time_t last_check;
    int check_interval;
} HealthMonitor;

static HealthMonitor g_health_monitors[50];
static int g_health_monitor_count = 0;
static pthread_mutex_t g_health_mutex = PTHREAD_MUTEX_INITIALIZER;

// Add health check
void addHealthCheck(const char* name, HealthCheck check_function, int check_interval) {
    pthread_mutex_lock(&g_health_mutex);
    
    if (g_health_monitor_count < 50) {
        HealthMonitor* monitor = &g_health_monitors[g_health_monitor_count];
        
        strncpy(monitor->name, name, sizeof(monitor->name) - 1);
        monitor->name[sizeof(monitor->name) - 1] = '\0';
        
        monitor->check_function = check_function;
        monitor->last_status = HEALTH_STATUS_UNKNOWN;
        monitor->last_check = 0;
        monitor->check_interval = check_interval;
        
        g_health_monitor_count++;
    }
    
    pthread_mutex_unlock(&g_health_mutex);
}

// Run health checks
void runHealthChecks() {
    pthread_mutex_lock(&g_health_mutex);
    
    time_t now = time(NULL);
    
    for (int i = 0; i < g_health_monitor_count; i++) {
        HealthMonitor* monitor = &g_health_monitors[i];
        
        if (now - monitor->last_check >= monitor->check_interval) {
            if (monitor->check_function) {
                monitor->last_status = monitor->check_function();
                monitor->last_check = now;
                
                LOG_INFO("HEALTH", "Health check '%s': %s", 
                        monitor->name, 
                        monitor->last_status == HEALTH_STATUS_HEALTHY ? "HEALTHY" :
                        monitor->last_status == HEALTH_STATUS_WARNING ? "WARNING" :
                        monitor->last_status == HEALTH_STATUS_CRITICAL ? "CRITICAL" : "UNKNOWN");
            }
        }
    }
    
    pthread_mutex_unlock(&g_health_mutex);
}

// Get overall health status
HealthStatus getOverallHealth() {
    pthread_mutex_lock(&g_health_mutex);
    
    HealthStatus overall = HEALTH_STATUS_HEALTHY;
    
    for (int i = 0; i < g_health_monitor_count; i++) {
        HealthMonitor* monitor = &g_health_monitors[i];
        
        if (monitor->last_status == HEALTH_STATUS_CRITICAL) {
            overall = HEALTH_STATUS_CRITICAL;
            break;
        } else if (monitor->last_status == HEALTH_STATUS_WARNING && overall == HEALTH_STATUS_HEALTHY) {
            overall = HEALTH_STATUS_WARNING;
        }
    }
    
    pthread_mutex_unlock(&g_health_mutex);
    return overall;
}

// =============================================================================
// SAMPLE HEALTH CHECKS
// =============================================================================

// Memory health check
HealthStatus memoryHealthCheck() {
    size_t allocated, peak;
    getMemoryUsage(&allocated, &peak, NULL, NULL);
    
    // Consider unhealthy if using more than 80% of peak usage
    if (peak > 0 && (double)allocated / peak > 0.8) {
        return HEALTH_STATUS_WARNING;
    }
    
    return HEALTH_STATUS_HEALTHY;
}

// Disk space health check (simplified)
HealthStatus diskSpaceHealthCheck() {
    // In a real implementation, this would check actual disk space
    // For demonstration, we'll just return healthy
    return HEALTH_STATUS_HEALTHY;
}

// Performance health check
HealthStatus performanceHealthCheck() {
    double avg_response_time = getMetric("avg_response_time");
    
    if (avg_response_time > 5.0) { // 5 seconds threshold
        return HEALTH_STATUS_CRITICAL;
    } else if (avg_response_time > 2.0) { // 2 seconds threshold
        return HEALTH_STATUS_WARNING;
    }
    
    return HEALTH_STATUS_HEALTHY;
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateBasicLogging() {
    printf("=== BASIC LOGGING DEMO ===\n");
    
    // Initialize logger
    initLogger("app.log", LOG_LEVEL_DEBUG, 1, 1);
    
    // Log at different levels
    LOG_TRACE("DEMO", "This is a trace message");
    LOG_DEBUG("DEMO", "This is a debug message");
    LOG_INFO("DEMO", "This is an info message");
    LOG_WARN("DEMO", "This is a warning message");
    LOG_ERROR("DEMO", "This is an error message");
    LOG_FATAL("DEMO", "This is a fatal message");
    
    printf("Check app.log for file output\n");
}

void demonstrateCustomHandlers() {
    printf("\n=== CUSTOM HANDLERS DEMO ===\n");
    
    // Add JSON handler
    addLogHandler(jsonLogHandler);
    
    // Add syslog handler
    addLogHandler(syslogHandler);
    
    LOG_INFO("DEMO", "This message will be logged to all handlers");
    
    // Remove handlers (in a real implementation, we'd have a remove function)
    // For now, just reinitialize
    cleanupLogger();
    initLogger("app.log", LOG_LEVEL_INFO, 1, 0);
}

void demonstrateMetrics() {
    printf("\n=== METRICS DEMO ===\n");
    
    initMetricsCollector();
    
    // Record some metrics
    recordMetric("cpu_usage", 75.5, "percent");
    recordMetric("memory_usage", 512.0, "MB");
    recordMetric("request_count", 1000.0, "count");
    recordMetric("response_time", 0.25, "seconds");
    
    // Print metrics
    printMetrics();
    
    // Get specific metric
    double cpu_usage = getMetric("cpu_usage");
    printf("CPU usage: %.1f%%\n", cpu_usage);
    
    cleanupMetricsCollector();
}

void demonstratePerformanceMonitoring() {
    printf("\n=== PERFORMANCE MONITORING DEMO ===\n");
    
    // Start some timers
    startTimer("database_query");
    
    // Simulate some work
    for (int i = 0; i < 1000000; i++) {
        volatile int x = i * i; // Some computation
    }
    
    stopTimer("database_query");
    
    startTimer("api_call");
    
    // Simulate API call
    for (int i = 0; i < 500000; i++) {
        volatile int y = i + i;
    }
    
    stopTimer("api_call");
    
    // Print timer metrics
    initMetricsCollector();
    printMetrics();
    cleanupMetricsCollector();
}

void demonstrateMemoryMonitoring() {
    printf("\n=== MEMORY MONITORING DEMO ===\n");
    
    // Allocate some memory
    char* buffer1 = (char*)monitoredMalloc(1024);
    char* buffer2 = (char*)monitoredMalloc(2048);
    char* buffer3 = (char*)monitoredMalloc(512);
    
    // Print memory usage
    printMemoryUsage();
    
    // Free some memory
    monitoredFree(buffer2);
    
    // Print memory usage again
    printMemoryUsage();
    
    // Clean up
    monitoredFree(buffer1);
    monitoredFree(buffer3);
}

void demonstrateHealthMonitoring() {
    printf("\n=== HEALTH MONITORING DEMO ===\n");
    
    // Add health checks
    addHealthCheck("memory", memoryHealthCheck, 30); // Check every 30 seconds
    addHealthCheck("disk", diskSpaceHealthCheck, 60); // Check every minute
    addHealthCheck("performance", performanceHealthCheck, 15); // Check every 15 seconds
    
    // Run health checks
    runHealthChecks();
    
    // Get overall health
    HealthStatus overall = getOverallHealth();
    printf("Overall health status: %s\n", 
           overall == HEALTH_STATUS_HEALTHY ? "HEALTHY" :
           overall == HEALTH_STATUS_WARNING ? "WARNING" :
           overall == HEALTH_STATUS_CRITICAL ? "CRITICAL" : "UNKNOWN");
}

void demonstrateAdvancedLogging() {
    printf("\n=== ADVANCED LOGGING DEMO ===\n");
    
    // Change log level
    setLogLevel(LOG_LEVEL_WARN);
    
    LOG_INFO("DEMO", "This won't be logged (below WARN level)");
    LOG_WARN("DEMO", "This will be logged");
    LOG_ERROR("DEMO", "This will also be logged");
    
    // Reset log level
    setLogLevel(LOG_LEVEL_INFO);
    
    // Log with different categories
    LOG_INFO("DATABASE", "Connected to database");
    LOG_INFO("NETWORK", "Network request sent");
    LOG_INFO("AUTH", "User authenticated successfully");
    
    // Log errors with context
    int error_code = 404;
    LOG_ERROR("HTTP", "Request failed with error code %d", error_code);
}

void demonstrateStructuredLogging() {
    printf("\n=== STRUCTURED LOGGING DEMO ===\n");
    
    // Add JSON handler for structured logging
    addLogHandler(jsonLogHandler);
    
    LOG_INFO("USER_ACTION", "User logged in");
    LOG_WARN("SECURITY", "Failed login attempt from IP 192.168.1.100");
    LOG_ERROR("PAYMENT", "Payment processing failed for order #12345");
    
    // Reinitialize to remove JSON handler
    cleanupLogger();
    initLogger("app.log", LOG_LEVEL_INFO, 1, 0);
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Logging and Monitoring Examples\n");
    printf("===============================\n\n");
    
    // Run all demonstrations
    demonstrateBasicLogging();
    demonstrateCustomHandlers();
    demonstrateMetrics();
    demonstratePerformanceMonitoring();
    demonstrateMemoryMonitoring();
    demonstrateHealthMonitoring();
    demonstrateAdvancedLogging();
    demonstrateStructuredLogging();
    
    // Cleanup
    cleanupLogger();
    cleanupMetricsCollector();
    
    printf("\nAll logging and monitoring examples demonstrated!\n");
    printf("Key takeaways:\n");
    printf("- Structured logging with multiple handlers and formatters\n");
    printf("- Performance monitoring with timers and metrics collection\n");
    printf("- Memory usage tracking and monitoring\n");
    printf("- Health check system for application monitoring\n");
    printf("- Thread-safe operations for concurrent logging\n");
    printf("- Configurable log levels and output destinations\n");
    
    return 0;
}
