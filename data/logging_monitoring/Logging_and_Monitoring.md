# Logging and Monitoring

This file contains comprehensive logging and monitoring examples in C, including multi-level logging, custom handlers, performance monitoring, metrics collection, memory tracking, and health monitoring systems.

## 📚 Logging and Monitoring Fundamentals

### 🎯 Logging Concepts
- **Log Levels**: Hierarchical severity levels (TRACE, DEBUG, INFO, WARN, ERROR, FATAL)
- **Log Handlers**: Different output destinations (console, file, JSON, syslog)
- **Structured Logging**: Consistent log format with metadata
- **Thread Safety**: Concurrent logging from multiple threads

### 🔍 Monitoring Concepts
- **Metrics Collection**: Performance and application metrics
- **Health Checks**: System health monitoring
- **Performance Monitoring**: Execution time and resource usage
- **Memory Monitoring**: Memory allocation and usage tracking

## 📝 Log Levels and Management

### Log Level Definitions
```c
typedef enum {
    LOG_LEVEL_TRACE = 0,    // Detailed debugging information
    LOG_LEVEL_DEBUG = 1,    // Debugging information
    LOG_LEVEL_INFO = 2,     // General information
    LOG_LEVEL_WARN = 3,     // Warning conditions
    LOG_LEVEL_ERROR = 4,    // Error conditions
    LOG_LEVEL_FATAL = 5,    // Fatal errors
    LOG_LEVEL_OFF = 6       // No logging
} LogLevel;
```

### Log Level Management
```c
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

// Check if log level is enabled
int isLogLevelEnabled(LogLevel level) {
    return level >= g_logger.min_level;
}

// Set log level
void setLogLevel(LogLevel level) {
    pthread_mutex_lock(&g_logger.mutex);
    g_logger.min_level = level;
    pthread_mutex_unlock(&g_logger.mutex);
}
```

**Log Level Benefits**:
- **Filtering**: Control log verbosity
- **Performance**: Skip unnecessary log processing
- **Debugging**: Focus on relevant log levels
- **Production**: Reduce log noise in production

## 📅 Timestamp Formatting

### Timestamp Functions
```c
// Format timestamp
void formatTimestamp(time_t timestamp, char* buffer, size_t buffer_size) {
    struct tm* tm_info = localtime(&timestamp);
    strftime(buffer, buffer_size, "%Y-%m-%d %H:%M:%S", tm_info);
}

// Format timestamp with milliseconds
void formatTimestampWithMs(time_t timestamp, char* buffer, size_t buffer_size) {
    struct tm* tm_info = localtime(&timestamp);
    
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
```

**Timestamp Benefits**:
- **Precision**: Millisecond precision for debugging
- **Standardization**: Consistent timestamp format
- **Timezone**: Local time for readability
- **Sorting**: Chronological log ordering

## 📋 Log Entry Structure

### Log Entry Definition
```c
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
```

### Log Entry Creation
```c
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
    }
    
    if (function) {
        strncpy(entry->function, function, sizeof(entry->function) - 1);
        entry->function[sizeof(entry->function) - 1] = '\0';
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
```

**Log Entry Benefits**:
- **Rich Context**: Function, file, line information
- **Thread Safety**: Thread ID for multi-threaded debugging
- **Categorization**: Log categories for filtering
- **Metadata**: Timestamp and level information

## 🔧 Log Handlers

### Console Handler
```c
void consoleLogHandler(const LogEntry* entry) {
    char timestamp[64];
    formatTimestampWithMs(entry->timestamp, timestamp, sizeof(timestamp));
    
    // Color coding for different levels
    const char* color_code = "";
    const char* reset_code = "";
    
    #ifndef _WIN32
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
```

### File Handler
```c
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
```

### JSON Handler
```c
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
```

### Syslog Handler
```c
void syslogHandler(const LogEntry* entry) {
    // In a real implementation, this would use syslog()
    printf("[SYSLOG] %s [%s] %s\n", 
           getLogLevelName(entry->level), entry->category, entry->message);
}
```

**Handler Benefits**:
- **Flexibility**: Multiple output destinations
- **Formatting**: Different output formats
- **Integration**: Easy integration with external systems
- **Extensibility**: Custom handler implementation

## 🏗️ Logger Configuration

### Logger Structure
```c
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
```

### Logger Initialization
```c
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
```

### Handler Management
```c
void addLogHandler(LogHandler handler) {
    pthread_mutex_lock(&g_logger.mutex);
    
    if (g_logger.handler_count < MAX_LOG_HANDLERS) {
        g_logger.handlers[g_logger.handler_count++] = handler;
    }
    
    pthread_mutex_unlock(&g_logger.mutex);
}
```

**Configuration Benefits**:
- **Centralized Control**: Single configuration point
- **Thread Safety**: Safe concurrent access
- **Flexibility**: Multiple output options
- **Performance**: Configurable buffering and async options

## 📝 Core Logging Function

### Main Logging Function
```c
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
```

### Convenience Macros
```c
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
```

**Logging Function Benefits**:
- **Consistency**: Uniform logging interface
- **Performance**: Early level filtering
- **Flexibility**: Variable arguments support
- **Context**: Automatic function/line/file information

## 📊 Metrics Collection

### Metric Structure
```c
typedef struct {
    char name[64];
    double value;
    time_t timestamp;
    char unit[16];
} Metric;

typedef struct {
    Metric metrics[1000];
    int metric_count;
    pthread_mutex_t mutex;
} MetricsCollector;
```

### Metrics Management
```c
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
```

**Metrics Benefits**:
- **Performance Tracking**: Monitor application performance
- **Resource Monitoring**: Track resource usage
- **Trend Analysis**: Identify performance trends
- **Alerting**: Trigger alerts based on thresholds

## ⏱️ Performance Monitoring

### Performance Timer
```c
typedef struct {
    char name[64];
    clock_t start_time;
    int active;
} PerformanceTimer;

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
```

**Performance Monitoring Benefits**:
- **Execution Time**: Track function execution times
- **Bottleneck Identification**: Find performance bottlenecks
- **Optimization**: Guide performance optimization efforts
- **SLA Monitoring**: Monitor service level agreements

## 💾 Memory Monitoring

### Memory Tracking Structure
```c
typedef struct {
    size_t allocated;
    size_t freed;
    size_t peak_usage;
    int allocation_count;
    int free_count;
} MemoryUsage;
```

### Memory Monitoring Functions
```c
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

void monitoredFree(void* ptr) {
    if (ptr) {
        pthread_mutex_lock(&g_memory_mutex);
        
        g_memory_usage.free_count++;
        // Note: We can't easily track freed size without additional bookkeeping
        
        pthread_mutex_unlock(&g_memory_mutex);
        
        free(ptr);
    }
}

void getMemoryUsage(size_t* allocated, size_t* peak, int* alloc_count, int* free_count) {
    pthread_mutex_lock(&g_memory_mutex);
    
    if (allocated) *allocated = g_memory_usage.allocated;
    if (peak) *peak = g_memory_usage.peak_usage;
    if (alloc_count) *alloc_count = g_memory_usage.allocation_count;
    if (free_count) *free_count = g_memory_usage.free_count;
    
    pthread_mutex_unlock(&g_memory_mutex);
}
```

**Memory Monitoring Benefits**:
- **Leak Detection**: Identify memory leaks
- **Usage Tracking**: Monitor memory consumption
- **Optimization**: Guide memory optimization
- **Alerting**: Memory usage alerts

## 🏥 Health Monitoring

### Health Status
```c
typedef enum {
    HEALTH_STATUS_HEALTHY,
    HEALTH_STATUS_WARNING,
    HEALTH_STATUS_CRITICAL,
    HEALTH_STATUS_UNKNOWN
} HealthStatus;

typedef HealthStatus (*HealthCheck)(void);

typedef struct {
    char name[64];
    HealthCheck check_function;
    HealthStatus last_status;
    time_t last_check;
    int check_interval;
} HealthMonitor;
```

### Health Check Management
```c
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
```

### Sample Health Checks
```c
HealthStatus memoryHealthCheck() {
    size_t allocated, peak;
    getMemoryUsage(&allocated, &peak, NULL, NULL);
    
    // Consider unhealthy if using more than 80% of peak usage
    if (peak > 0 && (double)allocated / peak > 0.8) {
        return HEALTH_STATUS_WARNING;
    }
    
    return HEALTH_STATUS_HEALTHY;
}

HealthStatus performanceHealthCheck() {
    double avg_response_time = getMetric("avg_response_time");
    
    if (avg_response_time > 5.0) { // 5 seconds threshold
        return HEALTH_STATUS_CRITICAL;
    } else if (avg_response_time > 2.0) { // 2 seconds threshold
        return HEALTH_STATUS_WARNING;
    }
    
    return HEALTH_STATUS_HEALTHY;
}
```

**Health Monitoring Benefits**:
- **Proactive Monitoring**: Detect issues before they become critical
- **System Health**: Overall system health assessment
- **Automated Response**: Trigger automated responses
- **SLA Compliance**: Monitor service level compliance

## 🔧 Best Practices

### 1. Structured Logging
```c
// Good: Structured logging with context
LOG_INFO("USER_ACTION", "User login: user_id=%d, ip=%s, success=true", 
         user_id, client_ip);

// Bad: Unstructured logging
LOG_INFO("USER_ACTION", "User logged in");
```

### 2. Appropriate Log Levels
```c
// Good: Use appropriate log levels
LOG_DEBUG("DATABASE", "Executing query: SELECT * FROM users");
LOG_INFO("DATABASE", "Query executed successfully");
LOG_ERROR("DATABASE", "Query failed: connection timeout");

// Bad: Always use INFO level
LOG_INFO("DATABASE", "Executing query: SELECT * FROM users");
LOG_INFO("DATABASE", "Query executed successfully");
LOG_INFO("DATABASE", "Query failed: connection timeout");
```

### 3. Performance Considerations
```c
// Good: Early level filtering
void logMessage(LogLevel level, ...) {
    if (!isLogLevelEnabled(level)) {
        return; // Skip expensive operations
    }
    // ... rest of logging code
}

// Bad: Always process log messages
void logMessage(LogLevel level, ...) {
    // Expensive string formatting happens even if level is disabled
    // ... rest of logging code
}
```

### 4. Thread Safety
```c
// Good: Thread-safe logging
pthread_mutex_lock(&g_logger.mutex);
// ... logging operations
pthread_mutex_unlock(&g_logger.mutex);

// Bad: Non-thread-safe logging
// ... logging operations without synchronization
```

### 5. Error Handling
```c
// Good: Handle logging errors gracefully
void fileLogHandler(const LogEntry* entry) {
    FILE* file = fopen(g_logger.log_file, "a");
    if (!file) {
        // Fallback to console logging
        consoleLogHandler(entry);
        return;
    }
    // ... write to file
    fclose(file);
}

// Bad: Ignore logging errors
void fileLogHandler(const LogEntry* entry) {
    FILE* file = fopen(g_logger.log_file, "a");
    // ... write to file without checking if file opened successfully
    fclose(file);
}
```

## ⚠️ Common Pitfalls

### 1. Over-logging
```c
// Wrong: Logging too much information
for (int i = 0; i < 1000000; i++) {
    LOG_DEBUG("LOOP", "Iteration %d", i); // Too much noise
}

// Right: Log meaningful information
LOG_DEBUG("LOOP", "Starting processing of 1,000,000 items");
for (int i = 0; i < 1000000; i++) {
    // Process items
}
LOG_DEBUG("LOOP", "Completed processing of 1,000,000 items");
```

### 2. Sensitive Information
```c
// Wrong: Logging sensitive data
LOG_INFO("AUTH", "User login: username=john, password=secret123");

// Right: Log without sensitive data
LOG_INFO("AUTH", "User login attempt: username=john");
```

### 3. Performance Impact
```c
// Wrong: Expensive operations in hot paths
void processRequest() {
    LOG_DEBUG("REQUEST", "Processing request with large payload: %s", large_json_string);
    // ... process request
}

// Right: Log summary information
void processRequest() {
    LOG_DEBUG("REQUEST", "Processing request: size=%d bytes", payload_size);
    // ... process request
}
```

### 4. Missing Context
```c
// Wrong: Insufficient context
LOG_ERROR("DATABASE", "Query failed");

// Right: Provide sufficient context
LOG_ERROR("DATABASE", "Query failed: query=%s, error=%s, connection_id=%d", 
           query, error_message, connection_id);
```

## 🔧 Real-World Applications

### 1. Web Application Logging
```c
void logHttpRequest(const char* method, const char* path, int status_code, 
                   double response_time, const char* client_ip) {
    LOG_INFO("HTTP", "%s %s %d %.3fms %s", 
             method, path, status_code, response_time * 1000, client_ip);
    
    // Record metrics
    recordMetric("http_requests", 1.0, "count");
    recordMetric("http_response_time", response_time, "seconds");
    
    if (status_code >= 400) {
        recordMetric("http_errors", 1.0, "count");
    }
}
```

### 2. Database Connection Monitoring
```c
void logDatabaseConnection(const char* database, int connection_count, 
                          double avg_query_time) {
    LOG_INFO("DATABASE", "Connection status: db=%s, connections=%d, avg_query_time=%.3fms",
             database, connection_count, avg_query_time * 1000);
    
    recordMetric("db_connections", connection_count, "count");
    recordMetric("db_query_time", avg_query_time, "seconds");
    
    // Health check
    if (avg_query_time > 1.0) {
        LOG_WARN("DATABASE", "Slow query performance detected");
    }
}
```

### 3. System Resource Monitoring
```c
void monitorSystemResources() {
    // CPU usage
    double cpu_usage = getCpuUsage();
    recordMetric("cpu_usage", cpu_usage, "percent");
    
    // Memory usage
    size_t memory_usage = getMemoryUsage();
    recordMetric("memory_usage", memory_usage, "bytes");
    
    // Disk usage
    size_t disk_usage = getDiskUsage();
    recordMetric("disk_usage", disk_usage, "bytes");
    
    LOG_INFO("SYSTEM", "Resource usage: cpu=%.1f%%, memory=%zuMB, disk=%zuMB",
             cpu_usage, memory_usage / (1024 * 1024), disk_usage / (1024 * 1024));
}
```

## 📚 Further Reading

### Books
- "The Log Structured Handbook" by Jamie Begin
- "Monitoring Distributed Systems" by Betsy Beyer
- "Site Reliability Engineering" by Google SRE Team

### Topics
- Log aggregation and analysis
- Distributed tracing
- Application performance monitoring (APM)
- Observability principles
- Log management best practices

Logging and monitoring in C provide essential visibility into application behavior, performance, and health. Master these techniques to build robust, observable, and maintainable C applications!
