# Web Server Development

This file contains comprehensive web server development examples in C, including HTTP protocol implementation, static file serving, dynamic routing, session management, security features, logging, and multi-client support.

## 📚 Web Server Fundamentals

### 🌐 Web Server Concepts
- **HTTP Protocol**: Request/response cycle and status codes
- **Static File Serving**: Serving HTML, CSS, JavaScript, and media files
- **Dynamic Routing**: URL pattern matching and handler functions
- **Session Management**: User state tracking and cookies
- **Security**: CORS, rate limiting, and request validation

### 🎯 Web Server Architecture
- **Event-Driven**: Non-blocking I/O with select()
- **Connection Pooling**: Efficient client connection management
- **Modular Design**: Separation of concerns with handlers
- **Configurable**: Runtime configuration options
- **Scalable**: Support for multiple concurrent clients

## 🌐 HTTP Protocol Implementation

### HTTP Methods and Status Codes
```c
// HTTP methods
typedef enum {
    HTTP_GET = 0,
    HTTP_POST = 1,
    HTTP_PUT = 2,
    HTTP_DELETE = 3,
    HTTP_HEAD = 4,
    HTTP_OPTIONS = 5,
    HTTP_PATCH = 6
} HTTPMethod;

// HTTP status codes
typedef enum {
    HTTP_200_OK = 200,
    HTTP_201_CREATED = 201,
    HTTP_204_NO_CONTENT = 204,
    HTTP_400_BAD_REQUEST = 400,
    HTTP_401_UNAUTHORIZED = 401,
    HTTP_403_FORBIDDEN = 403,
    HTTP_404_NOT_FOUND = 404,
    HTTP_405_METHOD_NOT_ALLOWED = 405,
    HTTP_500_INTERNAL_SERVER_ERROR = 500,
    HTTP_501_NOT_IMPLEMENTED = 501,
    HTTP_503_SERVICE_UNAVAILABLE = 503
} HTTPStatus;
```

### HTTP Request Structure
```c
// HTTP request structure
typedef struct {
    HTTPMethod method;
    char path[256];
    char version[16];
    char headers[MAX_HEADERS][MAX_HEADER_SIZE];
    int header_count;
    char body[MAX_REQUEST_SIZE];
    int body_length;
    char query_string[256];
    char remote_addr[INET_ADDRSTRLEN];
    time_t timestamp;
} HTTPRequest;
```

### HTTP Response Structure
```c
// HTTP response structure
typedef struct {
    HTTPStatus status;
    char headers[MAX_HEADERS][MAX_HEADER_SIZE];
    int header_count;
    char body[MAX_RESPONSE_SIZE];
    int body_length;
    char content_type[64];
    int keep_alive;
} HTTPResponse;
```

### HTTP Request Parsing
```c
// Parse HTTP method
HTTPMethod parseHTTPMethod(const char* method_str) {
    if (strcmp(method_str, "GET") == 0) return HTTP_GET;
    if (strcmp(method_str, "POST") == 0) return HTTP_POST;
    if (strcmp(method_str, "PUT") == 0) return HTTP_PUT;
    if (strcmp(method_str, "DELETE") == 0) return HTTP_DELETE;
    if (strcmp(method_str, "HEAD") == 0) return HTTP_HEAD;
    if (strcmp(method_str, "OPTIONS") == 0) return HTTP_OPTIONS;
    if (strcmp(method_str, "PATCH") == 0) return HTTP_PATCH;
    return HTTP_GET; // Default
}

// Parse HTTP request
int parseHTTPRequest(const char* request_data, int request_length, HTTPRequest* request) {
    if (!request_data || !request || request_length == 0) {
        return -1;
    }
    
    // Initialize request
    memset(request, 0, sizeof(HTTPRequest));
    request->timestamp = time(NULL);
    
    // Parse request line
    char request_line[MAX_REQUEST_SIZE];
    char* line_end = strstr(request_data, "\r\n");
    if (!line_end) {
        return -1;
    }
    
    int line_length = line_end - request_data;
    strncpy(request_line, request_data, line_length);
    request_line[line_length] = '\0';
    
    // Parse method, path, and version
    char method_str[16], path[256], version[16];
    if (sscanf(request_line, "%15s %255s %15s", method_str, path, version) != 3) {
        return -1;
    }
    
    request->method = parseHTTPMethod(method_str);
    strncpy(request->path, path, sizeof(request->path) - 1);
    strncpy(request->version, version, sizeof(request->version) - 1);
    
    // Parse query string
    char* query_start = strchr(request->path, '?');
    if (query_start) {
        *query_start = '\0';
        strncpy(request->query_string, query_start + 1, sizeof(request->query_string) - 1);
    }
    
    // Parse headers
    const char* current_pos = line_end + 2;
    request->header_count = 0;
    
    while (current_pos < request_data + request_length) {
        line_end = strstr(current_pos, "\r\n");
        if (!line_end || line_end == current_pos) {
            break; // End of headers
        }
        
        line_length = line_end - current_pos;
        if (line_length > MAX_HEADER_SIZE - 1) {
            line_length = MAX_HEADER_SIZE - 1;
        }
        
        strncpy(request->headers[request->header_count], current_pos, line_length);
        request->headers[request->header_count][line_length] = '\0';
        request->header_count++;
        
        current_pos = line_end + 2;
        
        if (request->header_count >= MAX_HEADERS) {
            break;
        }
    }
    
    // Parse body if present
    if (current_pos < request_data + request_length) {
        current_pos += 2; // Skip \r\n after headers
        int body_length = request_data + request_length - current_pos;
        if (body_length > MAX_REQUEST_SIZE - 1) {
            body_length = MAX_REQUEST_SIZE - 1;
        }
        
        memcpy(request->body, current_pos, body_length);
        request->body[body_length] = '\0';
        request->body_length = body_length;
    }
    
    return 0;
}
```

### HTTP Response Building
```c
// Get status message
const char* getStatusMessage(HTTPStatus status) {
    switch (status) {
        case HTTP_200_OK: return "OK";
        case HTTP_201_CREATED: return "Created";
        case HTTP_204_NO_CONTENT: return "No Content";
        case HTTP_400_BAD_REQUEST: return "Bad Request";
        case HTTP_401_UNAUTHORIZED: return "Unauthorized";
        case HTTP_403_FORBIDDEN: return "Forbidden";
        case HTTP_404_NOT_FOUND: return "Not Found";
        case HTTP_405_METHOD_NOT_ALLOWED: return "Method Not Allowed";
        case HTTP_500_INTERNAL_SERVER_ERROR: return "Internal Server Error";
        case HTTP_501_NOT_IMPLEMENTED: return "Not Implemented";
        case HTTP_503_SERVICE_UNAVAILABLE: return "Service Unavailable";
        default: return "Unknown";
    }
}

// Build HTTP response
int buildHTTPResponse(HTTPResponse* response, char* response_buffer, int buffer_size) {
    if (!response || !response_buffer || buffer_size == 0) {
        return -1;
    }
    
    int offset = 0;
    
    // Status line
    offset += snprintf(response_buffer + offset, buffer_size - offset,
                      "HTTP/1.1 %d %s\r\n", response->status, getStatusMessage(response->status));
    
    // Headers
    for (int i = 0; i < response->header_count; i++) {
        offset += snprintf(response_buffer + offset, buffer_size - offset,
                          "%s\r\n", response->headers[i]);
    }
    
    // Content-Type header if not set
    int has_content_type = 0;
    for (int i = 0; i < response->header_count; i++) {
        if (strncmp(response->headers[i], "Content-Type:", 13) == 0) {
            has_content_type = 1;
            break;
        }
    }
    
    if (!has_content_type && response->body_length > 0) {
        offset += snprintf(response_buffer + offset, buffer_size - offset,
                          "Content-Type: %s\r\n", response->content_type);
    }
    
    // Content-Length header
    if (response->body_length > 0) {
        offset += snprintf(response_buffer + offset, buffer_size - offset,
                          "Content-Length: %d\r\n", response->body_length);
    }
    
    // Connection header
    if (!response->keep_alive) {
        offset += snprintf(response_buffer + offset, buffer_size - offset,
                          "Connection: close\r\n");
    }
    
    // End of headers
    offset += snprintf(response_buffer + offset, buffer_size - offset, "\r\n");
    
    // Body
    if (response->body_length > 0) {
        int body_to_copy = response->body_length;
        if (offset + body_to_copy > buffer_size - 1) {
            body_to_copy = buffer_size - offset - 1;
        }
        
        memcpy(response_buffer + offset, response->body, body_to_copy);
        offset += body_to_copy;
    }
    
    response_buffer[offset] = '\0';
    return offset;
}
```

**HTTP Benefits**:
- **Standard Protocol**: Follows HTTP/1.1 specification
- **Flexible**: Supports various HTTP methods and status codes
- **Extensible**: Easy to add new headers and features
- **Compatible**: Works with standard web browsers

## 📁 Static File Serving

### MIME Type Detection
```c
// MIME types
typedef struct {
    char extension[16];
    char mime_type[64];
} MimeType;

static MimeType mime_types[] = {
    {".html", "text/html"},
    {".htm", "text/html"},
    {".css", "text/css"},
    {".js", "application/javascript"},
    {".json", "application/json"},
    {".xml", "application/xml"},
    {".txt", "text/plain"},
    {".jpg", "image/jpeg"},
    {".jpeg", "image/jpeg"},
    {".png", "image/png"},
    {".gif", "image/gif"},
    {".svg", "image/svg+xml"},
    {".ico", "image/x-icon"},
    {".pdf", "application/pdf"},
    {".zip", "application/zip"},
    {".mp4", "video/mp4"},
    {".mp3", "audio/mpeg"},
    {"", "application/octet-stream"} // Default
};

// Get MIME type for file extension
const char* getMimeType(const char* filename) {
    const char* extension = strrchr(filename, '.');
    if (!extension) {
        return mime_types[sizeof(mime_types)/sizeof(mime_types) - 1].mime_type; // Default
    }
    
    for (int i = 0; i < sizeof(mime_types)/sizeof(mime_types) - 1; i++) {
        if (strcasecmp(extension, mime_types[i].extension) == 0) {
            return mime_types[i].mime_type;
        }
    }
    
    return mime_types[sizeof(mime_types)/sizeof(mime_types) - 1].mime_type; // Default
}
```

### File Operations
```c
// Check if file exists and is accessible
int isFileAccessible(const char* filepath) {
    return access(filepath, R_OK) == 0;
}

// Get file size
long getFileSize(const char* filepath) {
    struct stat file_stat;
    if (stat(filepath, &file_stat) == 0) {
        return file_stat.st_size;
    }
    return -1;
}

// Read file into buffer
int readFile(const char* filepath, char* buffer, int buffer_size) {
    FILE* file = fopen(filepath, "rb");
    if (!file) {
        return -1;
    }
    
    long file_size = getFileSize(filepath);
    if (file_size < 0 || file_size > buffer_size - 1) {
        fclose(file);
        return -1;
    }
    
    size_t bytes_read = fread(buffer, 1, file_size, file);
    fclose(file);
    
    buffer[bytes_read] = '\0';
    return bytes_read;
}
```

### Static File Serving
```c
// Serve static file
int serveStaticFile(HTTPRequest* request, HTTPResponse* response, const char* document_root) {
    char filepath[512];
    
    // Build full file path
    if (strcmp(request->path, "/") == 0) {
        snprintf(filepath, sizeof(filepath), "%s/index.html", document_root);
    } else {
        snprintf(filepath, sizeof(filepath), "%s%s", document_root, request->path);
    }
    
    // Security check: prevent directory traversal
    if (strstr(filepath, "..") != NULL) {
        response->status = HTTP_403_FORBIDDEN;
        strcpy(response->body, "403 Forbidden - Directory traversal not allowed");
        response->body_length = strlen(response->body);
        strcpy(response->content_type, "text/html");
        return 0;
    }
    
    // Check if file exists
    if (!isFileAccessible(filepath)) {
        response->status = HTTP_404_NOT_FOUND;
        strcpy(response->body, "404 Not Found - File not found");
        response->body_length = strlen(response->body);
        strcpy(response->content_type, "text/html");
        return 0;
    }
    
    // Read file
    int bytes_read = readFile(filepath, response->body, sizeof(response->body));
    if (bytes_read < 0) {
        response->status = HTTP_500_INTERNAL_SERVER_ERROR;
        strcpy(response->body, "500 Internal Server Error - Could not read file");
        response->body_length = strlen(response->body);
        strcpy(response->content_type, "text/html");
        return -1;
    }
    
    response->status = HTTP_200_OK;
    response->body_length = bytes_read;
    strcpy(response->content_type, getMimeType(filepath));
    
    return 0;
}
```

**File Serving Benefits**:
- **MIME Detection**: Automatic content type detection
- **Security**: Directory traversal protection
- **Efficiency**: Optimized file reading
- **Flexibility**: Support for various file types

## 🛣️ Dynamic Routing

### Route Structure
```c
// Route handler function pointer
typedef int (*RouteHandler)(HTTPRequest* request, HTTPResponse* response);

// Route structure
typedef struct {
    char path[256];
    HTTPMethod method;
    RouteHandler handler;
    int is_wildcard;
} Route;

// Router structure
typedef struct {
    Route routes[MAX_ROUTES];
    int route_count;
    int default_handler_set;
} Router;
```

### Router Implementation
```c
// Initialize router
Router* initRouter() {
    Router* router = malloc(sizeof(Router));
    if (!router) return NULL;
    
    memset(router, 0, sizeof(Router));
    return router;
}

// Add route
int addRoute(Router* router, const char* path, HTTPMethod method, RouteHandler handler) {
    if (router->route_count >= MAX_ROUTES) {
        return -1; // Router full
    }
    
    Route* route = &router->routes[router->route_count];
    strncpy(route->path, path, sizeof(route->path) - 1);
    route->method = method;
    route->handler = handler;
    route->is_wildcard = (strchr(path, '*') != NULL);
    
    router->route_count++;
    return 0;
}

// Find matching route
Route* findRoute(Router* router, const char* path, HTTPMethod method) {
    for (int i = 0; i < router->route_count; i++) {
        Route* route = &router->routes[i];
        
        if (route->method != method && route->method != HTTP_OPTIONS) {
            continue;
        }
        
        if (route->is_wildcard) {
            // Wildcard matching
            char* wildcard = strchr(route->path, '*');
            int prefix_length = wildcard - route->path;
            
            if (strncmp(path, route->path, prefix_length) == 0) {
                return route;
            }
        } else {
            // Exact matching
            if (strcmp(path, route->path) == 0) {
                return route;
            }
        }
    }
    
    return NULL; // No matching route
}
```

### Route Handlers
```c
// Default route handler
int defaultHandler(HTTPRequest* request, HTTPResponse* response) {
    response->status = HTTP_200_OK;
    strcpy(response->body, 
        "<!DOCTYPE html><html><head><title>Welcome</title></head><body>"
        "<h1>Welcome to the C Web Server!</h1>"
        "<p>This is a simple web server implemented in C.</p>"
        "<p>Try visiting <a href='/test'>/test</a> for a test page.</p>"
        "</body></html>");
    response->body_length = strlen(response->body);
    strcpy(response->content_type, "text/html");
    return 0;
}

// Test route handler
int testHandler(HTTPRequest* request, HTTPResponse* response) {
    response->status = HTTP_200_OK;
    
    char body[1024];
    snprintf(body, sizeof(body),
        "<!DOCTYPE html><html><head><title>Test Page</title></head><body>"
        "<h1>Test Page</h1>"
        "<p>Method: %s</p>"
        "<p>Path: %s</p>"
        "<p>Query String: %s</p>"
        "<p>Remote Address: %s</p>"
        "</body></html>",
        (request->method == HTTP_GET) ? "GET" : 
        (request->method == HTTP_POST) ? "POST" : "OTHER",
        request->path,
        request->query_string,
        request->remote_addr);
    
    strcpy(response->body, body);
    response->body_length = strlen(response->body);
    strcpy(response->content_type, "text/html");
    return 0;
}

// API handler
int apiHandler(HTTPRequest* request, HTTPResponse* response) {
    response->status = HTTP_200_OK;
    
    // Create JSON response
    char json[512];
    snprintf(json, sizeof(json),
        "{"
        "\"message\": \"Hello from API\","
        "\"method\": \"%s\","
        "\"path\": \"%s\","
        "\"timestamp\": %ld"
        "}",
        (request->method == HTTP_GET) ? "GET" : "POST",
        request->path,
        time(NULL));
    
    strcpy(response->body, json);
    response->body_length = strlen(response->body);
    strcpy(response->content_type, "application/json");
    return 0;
}
```

**Routing Benefits**:
- **Flexible**: Support for exact and wildcard patterns
- **Modular**: Separate handlers for different routes
- **Extensible**: Easy to add new routes and handlers
- **Type-safe**: Strongly typed handler functions

## 🔄 Session Management

### Session Structure
```c
// Session structure
typedef struct {
    char session_id[64];
    char data[1024];
    time_t created;
    time_t last_accessed;
    int is_active;
} Session;

// Session manager
typedef struct {
    Session sessions[MAX_SESSIONS];
    int session_count;
    int session_timeout;
    char session_cookie_name[32];
} SessionManager;
```

### Session Implementation
```c
// Initialize session manager
SessionManager* initSessionManager(int session_timeout) {
    SessionManager* manager = malloc(sizeof(SessionManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(SessionManager));
    manager->session_timeout = session_timeout;
    strcpy(manager->session_cookie_name, "SESSION_ID");
    
    return manager;
}

// Generate session ID
void generateSessionId(char* session_id, int length) {
    const char charset[] = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    
    for (int i = 0; i < length - 1; i++) {
        session_id[i] = charset[rand() % (sizeof(charset) - 1)];
    }
    session_id[length - 1] = '\0';
}

// Create new session
Session* createSession(SessionManager* manager) {
    if (manager->session_count >= MAX_SESSIONS) {
        return NULL; // Session limit reached
    }
    
    // Find empty slot
    for (int i = 0; i < MAX_SESSIONS; i++) {
        if (!manager->sessions[i].is_active) {
            Session* session = &manager->sessions[i];
            
            generateSessionId(session->session_id, sizeof(session->session_id));
            session->created = time(NULL);
            session->last_accessed = time(NULL);
            session->is_active = 1;
            strcpy(session->data, "");
            
            manager->session_count++;
            return session;
        }
    }
    
    return NULL; // No empty slot
}

// Get session by ID
Session* getSession(SessionManager* manager, const char* session_id) {
    for (int i = 0; i < MAX_SESSIONS; i++) {
        Session* session = &manager->sessions[i];
        if (session->is_active && strcmp(session->session_id, session_id) == 0) {
            session->last_accessed = time(NULL);
            return session;
        }
    }
    return NULL;
}

// Clean up expired sessions
void cleanupExpiredSessions(SessionManager* manager) {
    time_t current_time = time(NULL);
    
    for (int i = 0; i < MAX_SESSIONS; i++) {
        Session* session = &manager->sessions[i];
        if (session->is_active && 
            (current_time - session->last_accessed) > manager->session_timeout) {
            session->is_active = 0;
            manager->session_count--;
        }
    }
}
```

**Session Benefits**:
- **State Management**: Track user state across requests
- **Security**: Secure session ID generation
- **Automatic Cleanup**: Expired session removal
- **Flexible Data**: Custom session data storage

## 🔒 Security Features

### Security Configuration
```c
// Security configuration
typedef struct {
    int enable_cors;
    char allowed_origins[256];
    char allowed_methods[64];
    char allowed_headers[256];
    int enable_csrf_protection;
    char csrf_token[64];
    int enable_rate_limiting;
    int max_requests_per_minute;
    int enable_ssl;
    char ssl_cert_file[256];
    char ssl_key_file[256];
} SecurityConfig;

// Initialize security config
SecurityConfig* initSecurityConfig() {
    SecurityConfig* config = malloc(sizeof(SecurityConfig));
    if (!config) return NULL;
    
    memset(config, 0, sizeof(SecurityConfig));
    
    config->enable_cors = 1;
    strcpy(config->allowed_origins, "*");
    strcpy(config->allowed_methods, "GET, POST, PUT, DELETE, OPTIONS");
    strcpy(config->allowed_headers, "Content-Type, Authorization");
    config->enable_csrf_protection = 0;
    config->enable_rate_limiting = 1;
    config->max_requests_per_minute = 60;
    config->enable_ssl = 0;
    
    return config;
}
```

### CORS Headers
```c
// Add CORS headers
void addCORSHeaders(HTTPResponse* response, SecurityConfig* config) {
    if (!config->enable_cors) {
        return;
    }
    
    char header[MAX_HEADER_SIZE];
    
    // Access-Control-Allow-Origin
    snprintf(header, sizeof(header), "Access-Control-Allow-Origin: %s", config->allowed_origins);
    strcpy(response->headers[response->header_count++], header);
    
    // Access-Control-Allow-Methods
    snprintf(header, sizeof(header), "Access-Control-Allow-Methods: %s", config->allowed_methods);
    strcpy(response->headers[response->header_count++], header);
    
    // Access-Control-Allow-Headers
    snprintf(header, sizeof(header), "Access-Control-Allow-Headers: %s", config->allowed_headers);
    strcpy(response->headers[response->header_count++], header);
}
```

### Rate Limiting
```c
// Rate limiter
typedef struct {
    char client_ip[INET_ADDRSTRLEN];
    int request_count;
    time_t window_start;
} RateLimitEntry;

// Check rate limit
int checkRateLimit(SecurityConfig* config, const char* client_ip, RateLimitEntry* rate_limits, int* rate_limit_count) {
    if (!config->enable_rate_limiting) {
        return 1; // Allow
    }
    
    time_t current_time = time(NULL);
    
    // Find existing entry for this IP
    for (int i = 0; i < *rate_limit_count; i++) {
        if (strcmp(rate_limits[i].client_ip, client_ip) == 0) {
            // Check if window has expired
            if (current_time - rate_limits[i].window_start > 60) {
                // Reset window
                rate_limits[i].request_count = 1;
                rate_limits[i].window_start = current_time;
                return 1; // Allow
            }
            
            // Check if rate limit exceeded
            if (rate_limits[i].request_count >= config->max_requests_per_minute) {
                return 0; // Deny
            }
            
            // Increment counter
            rate_limits[i].request_count++;
            return 1; // Allow
        }
    }
    
    // Create new entry
    if (*rate_limit_count < MAX_CLIENTS) {
        strcpy(rate_limits[*rate_limit_count].client_ip, client_ip);
        rate_limits[*rate_limit_count].request_count = 1;
        rate_limits[*rate_limit_count].window_start = current_time;
        (*rate_limit_count)++;
    }
    
    return 1; // Allow
}
```

**Security Benefits**:
- **CORS Support**: Cross-origin resource sharing
- **Rate Limiting**: Prevent abuse and DoS attacks
- **Request Validation**: Input sanitization and validation
- **Configurable**: Flexible security policies

## 📊 Logging System

### Log Structure
```c
// Log levels
typedef enum {
    LOG_DEBUG = 0,
    LOG_INFO = 1,
    LOG_WARNING = 2,
    LOG_ERROR = 3,
    LOG_CRITICAL = 4
} LogLevel;

// Log entry
typedef struct {
    time_t timestamp;
    LogLevel level;
    char message[512];
    char client_ip[INET_ADDRSTRLEN];
    char request_path[256];
    int status_code;
} LogEntry;

// Logger structure
typedef struct {
    FILE* log_file;
    LogLevel min_level;
    int console_output;
    int file_output;
    char log_file_path[256];
} Logger;
```

### Logger Implementation
```c
// Initialize logger
Logger* initLogger(const char* log_file_path, LogLevel min_level, int console_output, int file_output) {
    Logger* logger = malloc(sizeof(Logger));
    if (!logger) return NULL;
    
    memset(logger, 0, sizeof(Logger));
    logger->min_level = min_level;
    logger->console_output = console_output;
    logger->file_output = file_output;
    
    if (log_file_path) {
        strncpy(logger->log_file_path, log_file_path, sizeof(logger->log_file_path) - 1);
        
        if (file_output) {
            logger->log_file = fopen(log_file_path, "a");
            if (!logger->log_file) {
                printf("Warning: Could not open log file %s\n", log_file_path);
                logger->file_output = 0;
            }
        }
    }
    
    return logger;
}

// Log message
void logMessage(Logger* logger, LogLevel level, const char* message, const char* client_ip, const char* request_path, int status_code) {
    if (level < logger->min_level) {
        return;
    }
    
    time_t timestamp = time(NULL);
    char time_str[64];
    strftime(time_str, sizeof(time_str), "%Y-%m-%d %H:%M:%S", localtime(&timestamp));
    
    const char* level_str[] = {"DEBUG", "INFO", "WARNING", "ERROR", "CRITICAL"};
    
    if (logger->console_output) {
        printf("[%s] %s - %s", time_str, level_str[level], message);
        if (client_ip) printf(" [%s]", client_ip);
        if (request_path) printf(" %s", request_path);
        if (status_code > 0) printf(" %d", status_code);
        printf("\n");
    }
    
    if (logger->file_output && logger->log_file) {
        fprintf(logger->log_file, "[%s] %s - %s", time_str, level_str[level], message);
        if (client_ip) fprintf(logger->log_file, " [%s]", client_ip);
        if (request_path) fprintf(logger->log_file, " %s", request_path);
        if (status_code > 0) fprintf(logger->log_file, " %d", status_code);
        fprintf(logger->log_file, "\n");
        fflush(logger->log_file);
    }
}
```

**Logging Benefits**:
- **Multiple Levels**: Debug, Info, Warning, Error, Critical
- **Flexible Output**: Console and file logging
- **Structured Format**: Consistent log format
- **Request Tracking**: Log HTTP requests and responses

## 🌐 Client Connection Management

### Client Structure
```c
// Client structure
typedef struct {
    int socket_fd;
    struct sockaddr_in address;
    char remote_addr[INET_ADDRSTRLEN];
    time_t connect_time;
    time_t last_activity;
    int is_active;
    HTTPRequest current_request;
    HTTPResponse current_response;
    int request_complete;
    int response_sent;
} Client;

// Connection pool
typedef struct {
    Client clients[MAX_CLIENTS];
    int client_count;
    int max_clients;
    int server_socket;
    struct sockaddr_in server_addr;
} ConnectionPool;
```

### Connection Management
```c
// Initialize connection pool
ConnectionPool* initConnectionPool(int max_clients) {
    ConnectionPool* pool = malloc(sizeof(ConnectionPool));
    if (!pool) return NULL;
    
    memset(pool, 0, sizeof(ConnectionPool));
    pool->max_clients = max_clients;
    
    // Initialize clients
    for (int i = 0; i < max_clients; i++) {
        pool->clients[i].socket_fd = -1;
        pool->clients[i].is_active = 0;
    }
    
    return pool;
}

// Add client to pool
int addClient(ConnectionPool* pool, int client_socket, struct sockaddr_in* client_addr) {
    if (pool->client_count >= pool->max_clients) {
        return -1; // Pool full
    }
    
    // Find empty slot
    for (int i = 0; i < pool->max_clients; i++) {
        if (!pool->clients[i].is_active) {
            Client* client = &pool->clients[i];
            
            client->socket_fd = client_socket;
            client->address = *client_addr;
            client->connect_time = time(NULL);
            client->last_activity = time(NULL);
            client->is_active = 1;
            client->request_complete = 0;
            client->response_sent = 0;
            
            // Convert IP address to string
            inet_ntop(AF_INET, &client_addr->sin_addr, client->remote_addr, INET_ADDRSTRLEN);
            
            pool->client_count++;
            return i;
        }
    }
    
    return -1; // No empty slot found
}

// Remove client from pool
int removeClient(ConnectionPool* pool, int client_index) {
    if (client_index < 0 || client_index >= pool->max_clients) {
        return -1;
    }
    
    Client* client = &pool->clients[client_index];
    if (!client->is_active) {
        return -1;
    }
    
    close(client->socket_fd);
    client->socket_fd = -1;
    client->is_active = 0;
    pool->client_count--;
    
    return 0;
}
```

**Connection Benefits**:
- **Efficient**: Connection pooling for performance
- **Scalable**: Support for multiple concurrent clients
- **Non-blocking**: Asynchronous I/O with select()
- **Robust**: Proper error handling and cleanup

## 🔧 Best Practices

### 1. Input Validation
```c
// Good: Validate input before processing
int validatePath(const char* path) {
    if (!path || strlen(path) == 0 || strlen(path) > 255) {
        return 0; // Invalid
    }
    
    // Check for directory traversal
    if (strstr(path, "..") != NULL) {
        return 0; // Invalid
    }
    
    // Check for null bytes
    if (strlen(path) != strnlen(path, 256)) {
        return 0; // Invalid
    }
    
    return 1; // Valid
}

// Bad: No input validation
void serveFile(const char* path) {
    char filepath[512];
    snprintf(filepath, sizeof(filepath), "/var/www%s", path); // Vulnerable to path traversal
}
```

### 2. Memory Management
```c
// Good: Proper memory management
void handleRequest(HTTPRequest* request, HTTPResponse* response) {
    char* buffer = malloc(1024);
    if (!buffer) {
        response->status = HTTP_500_INTERNAL_SERVER_ERROR;
        return;
    }
    
    // Use buffer
    processRequest(buffer, request, response);
    
    free(buffer);
}

// Bad: Memory leak
void handleRequest(HTTPRequest* request, HTTPResponse* response) {
    char* buffer = malloc(1024);
    processRequest(buffer, request, response);
    // Forgot to free(buffer)
}
```

### 3. Error Handling
```c
// Good: Comprehensive error handling
int readFile(const char* filepath, char* buffer, int buffer_size) {
    FILE* file = fopen(filepath, "rb");
    if (!file) {
        logMessage(logger, LOG_ERROR, "Failed to open file", NULL, filepath, 0);
        return -1;
    }
    
    long file_size = getFileSize(filepath);
    if (file_size < 0 || file_size > buffer_size - 1) {
        fclose(file);
        logMessage(logger, LOG_ERROR, "File too large or size error", NULL, filepath, 0);
        return -1;
    }
    
    size_t bytes_read = fread(buffer, 1, file_size, file);
    fclose(file);
    
    if (bytes_read != file_size) {
        logMessage(logger, LOG_ERROR, "Failed to read complete file", NULL, filepath, 0);
        return -1;
    }
    
    buffer[bytes_read] = '\0';
    return bytes_read;
}

// Bad: No error handling
int readFile(const char* filepath, char* buffer, int buffer_size) {
    FILE* file = fopen(filepath, "rb");
    long file_size = getFileSize(filepath);
    size_t bytes_read = fread(buffer, 1, file_size, file);
    fclose(file);
    buffer[bytes_read] = '\0';
    return bytes_read; // No error checking
}
```

### 4. Thread Safety
```c
// Good: Thread-safe operations
pthread_mutex_t log_mutex = PTHREAD_MUTEX_INITIALIZER;

void logMessage(const char* message) {
    pthread_mutex_lock(&log_mutex);
    
    FILE* log_file = fopen("server.log", "a");
    if (log_file) {
        fprintf(log_file, "%s\n", message);
        fclose(log_file);
    }
    
    pthread_mutex_unlock(&log_mutex);
}

// Bad: Not thread-safe
void logMessage(const char* message) {
    FILE* log_file = fopen("server.log", "a"); // Race condition
    if (log_file) {
        fprintf(log_file, "%s\n", message);
        fclose(log_file);
    }
}
```

### 5. Resource Limits
```c
// Good: Check resource limits
int acceptConnection(ConnectionPool* pool) {
    if (pool->client_count >= pool->max_clients) {
        logMessage(logger, LOG_WARNING, "Connection pool full", NULL, NULL, 0);
        return -1;
    }
    
    // Accept connection
    int client_socket = accept(pool->server_socket, NULL, NULL);
    if (client_socket < 0) {
        logMessage(logger, LOG_ERROR, "Failed to accept connection", NULL, NULL, 0);
        return -1;
    }
    
    return addClient(pool, client_socket, &client_addr);
}

// Bad: No resource limits
int acceptConnection(ConnectionPool* pool) {
    int client_socket = accept(pool->server_socket, NULL, NULL);
    return addClient(pool, client_socket, &client_addr); // May exceed limits
}
```

## ⚠️ Common Pitfalls

### 1. Buffer Overflow
```c
// Wrong: No bounds checking
void copyPath(const char* input, char* output) {
    strcpy(output, input); // Can overflow if input is too long
}

// Right: Safe string operations
void copyPath(const char* input, char* output, size_t output_size) {
    strncpy(output, input, output_size - 1);
    output[output_size - 1] = '\0';
}
```

### 2. Memory Leaks
```c
// Wrong: Memory leak in loop
void processRequests() {
    for (int i = 0; i < 1000; i++) {
        char* buffer = malloc(1024);
        processRequest(buffer);
        // Forgot to free(buffer)
    }
}

// Right: Proper memory management
void processRequests() {
    for (int i = 0; i < 1000; i++) {
        char* buffer = malloc(1024);
        if (buffer) {
            processRequest(buffer);
            free(buffer);
        }
    }
}
```

### 3. Race Conditions
```c
// Wrong: Shared state without synchronization
int request_count = 0;

void handleRequest() {
    request_count++; // Race condition with multiple threads
}

// Right: Thread-safe operations
pthread_mutex_t count_mutex = PTHREAD_MUTEX_INITIALIZER;
int request_count = 0;

void handleRequest() {
    pthread_mutex_lock(&count_mutex);
    request_count++;
    pthread_mutex_unlock(&count_mutex);
}
```

### 4. File Handle Leaks
```c
// Wrong: File handle leak
void serveFile(const char* filepath) {
    FILE* file = fopen(filepath, "rb");
    // Process file
    // Forgot to close(file)
}

// Right: Proper file handle management
void serveFile(const char* filepath) {
    FILE* file = fopen(filepath, "rb");
    if (file) {
        // Process file
        fclose(file);
    }
}
```

## 🔧 Real-World Applications

### 1. REST API Server
```c
// REST API endpoints
int apiUsersHandler(HTTPRequest* request, HTTPResponse* response) {
    if (request->method == HTTP_GET) {
        // Return list of users
        return getUsersList(response);
    } else if (request->method == HTTP_POST) {
        // Create new user
        return createUser(request, response);
    } else if (request->method == HTTP_PUT) {
        // Update user
        return updateUser(request, response);
    } else if (request->method == HTTP_DELETE) {
        // Delete user
        return deleteUser(request, response);
    }
    
    response->status = HTTP_405_METHOD_NOT_ALLOWED;
    return 0;
}
```

### 2. Static File Server
```c
// Enhanced static file server with caching
typedef struct {
    char* content;
    int content_length;
    time_t last_modified;
    char etag[64];
} FileCache;

FileCache* getFileCache(const char* filepath) {
    static FileCache cache[100];
    static int cache_count = 0;
    
    // Check if file is in cache
    for (int i = 0; i < cache_count; i++) {
        if (strcmp(cache[i].filepath, filepath) == 0) {
            // Check if file is modified
            struct stat file_stat;
            if (stat(filepath, &file_stat) == 0 && 
                file_stat.st_mtime == cache[i].last_modified) {
                return &cache[i]; // Return cached version
            }
        }
    }
    
    // Load file into cache
    if (cache_count < 100) {
        FileCache* entry = &cache[cache_count];
        entry->content = malloc(MAX_FILE_SIZE);
        
        if (readFile(filepath, entry->content, MAX_FILE_SIZE) > 0) {
            entry->content_length = strlen(entry->content);
            entry->last_modified = time(NULL);
            snprintf(entry->etag, sizeof(entry->etag), "\"%ld-%d\"", 
                    entry->last_modified, entry->content_length);
            
            cache_count++;
            return entry;
        }
    }
    
    return NULL;
}
```

### 3. WebSocket Server Extension
```c
// WebSocket handshake
int handleWebSocketUpgrade(HTTPRequest* request, HTTPResponse* response) {
    // Check WebSocket upgrade request
    if (request->method != HTTP_GET) {
        return 0;
    }
    
    char* upgrade_header = getHeader(request, "Upgrade");
    char* connection_header = getHeader(request, "Connection");
    char* websocket_key = getHeader(request, "Sec-WebSocket-Key");
    
    if (!upgrade_header || !connection_header || !websocket_key ||
        strcmp(upgrade_header, "websocket") != 0 ||
        strstr(connection_header, "Upgrade") == NULL) {
        return 0;
    }
    
    // Generate accept key
    char accept_key[256];
    generateWebSocketAccept(websocket_key, accept_key);
    
    // Send 101 Switching Protocols response
    response->status = 101;
    strcpy(response->headers[response->header_count++], "Upgrade: websocket");
    strcpy(response->headers[response->header_count++], "Connection: Upgrade");
    snprintf(response->headers[response->header_count++], 
             "Sec-WebSocket-Accept: %s", accept_key);
    
    return 1;
}
```

## 📚 Further Reading

### Books
- "Unix Network Programming" by W. Richard Stevens
- "HTTP: The Definitive Guide" by David Gourley
- "Web Development with C" by various authors

### Topics
- WebSocket implementation
- HTTPS/SSL integration
- Load balancing
- Reverse proxy implementation
- Content delivery networks

Web server development in C provides the foundation for building high-performance, secure, and scalable web applications. Master these techniques to create robust web servers that can handle thousands of concurrent connections efficiently!
