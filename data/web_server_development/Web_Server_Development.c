#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <sys/select.h>
#include <sys/time.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <dirent.h>

// =============================================================================
// WEB SERVER DEVELOPMENT
// =============================================================================

#define MAX_CLIENTS 100
#define BUFFER_SIZE 8192
#define MAX_REQUEST_SIZE 4096
#define MAX_RESPONSE_SIZE 8192
#define MAX_HEADERS 50
#define MAX_HEADER_SIZE 256
#define SERVER_PORT 8080
#define BACKLOG 10

// =============================================================================
// HTTP PROTOCOL IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// CLIENT CONNECTION MANAGEMENT
// =============================================================================

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

// =============================================================================
// FILE SERVING
// =============================================================================

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

// File serving configuration
typedef struct {
    char document_root[256];
    char index_file[64];
    int auto_index;
    int directory_listing;
    int file_cache_enabled;
    int max_file_size;
} FileServerConfig;

// =============================================================================
// ROUTING SYSTEM
// =============================================================================

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

// =============================================================================
// SESSION MANAGEMENT
// =============================================================================

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

// =============================================================================
// SECURITY FEATURES
// =============================================================================

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

// Rate limiter
typedef struct {
    char client_ip[INET_ADDRSTRLEN];
    int request_count;
    time_t window_start;
} RateLimitEntry;

// =============================================================================
// LOGGING SYSTEM
// =============================================================================

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

// =============================================================================
// HTTP PROTOCOL IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// CLIENT CONNECTION MANAGEMENT
// =============================================================================

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

// =============================================================================
// FILE SERVING IMPLEMENTATION
// =============================================================================

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

// Generate directory listing
int generateDirectoryListing(const char* dirpath, HTTPResponse* response) {
    DIR* dir = opendir(dirpath);
    if (!dir) {
        return -1;
    }
    
    char listing[MAX_RESPONSE_SIZE];
    int offset = 0;
    
    offset += snprintf(listing + offset, sizeof(listing) - offset,
        "<!DOCTYPE html><html><head><title>Directory Listing</title></head><body>");
    offset += snprintf(listing + offset, sizeof(listing) - offset,
        "<h1>Directory Listing</h1><ul>");
    
    struct dirent* entry;
    while ((entry = readdir(dir)) != NULL && offset < sizeof(listing) - 100) {
        if (strcmp(entry->d_name, ".") != 0) {
            offset += snprintf(listing + offset, sizeof(listing) - offset,
                "<li><a href=\"%s%s\">%s%s</a></li>",
                entry->d_name, entry->d_type == DT_DIR ? "/" : "",
                entry->d_name, entry->d_type == DT_DIR ? "/" : "");
        }
    }
    
    closedir(dir);
    
    offset += snprintf(listing + offset, sizeof(listing) - offset, "</ul></body></html>");
    
    strcpy(response->body, listing);
    response->body_length = offset;
    response->status = HTTP_200_OK;
    strcpy(response->content_type, "text/html");
    
    return 0;
}

// =============================================================================
// ROUTING SYSTEM IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// SESSION MANAGEMENT IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// SECURITY FEATURES IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// LOGGING SYSTEM IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// WEB SERVER IMPLEMENTATION
// =============================================================================

// Web server structure
typedef struct {
    ConnectionPool* connection_pool;
    Router* router;
    SessionManager* session_manager;
    SecurityConfig* security_config;
    Logger* logger;
    FileServerConfig* file_config;
    RateLimitEntry* rate_limits;
    int rate_limit_count;
    int running;
} WebServer;

// Initialize web server
WebServer* initWebServer(int port, const char* document_root) {
    WebServer* server = malloc(sizeof(WebServer));
    if (!server) return NULL;
    
    memset(server, 0, sizeof(WebServer));
    
    // Initialize components
    server->connection_pool = initConnectionPool(MAX_CLIENTS);
    server->router = initRouter();
    server->session_manager = initSessionManager(3600); // 1 hour timeout
    server->security_config = initSecurityConfig();
    server->logger = initLogger("server.log", LOG_INFO, 1, 1);
    server->rate_limits = malloc(MAX_CLIENTS * sizeof(RateLimitEntry));
    server->rate_limit_count = 0;
    server->running = 0;
    
    // Initialize file server config
    server->file_config = malloc(sizeof(FileServerConfig));
    strcpy(server->file_config->document_root, document_root);
    strcpy(server->file_config->index_file, "index.html");
    server->file_config->auto_index = 1;
    server->file_config->directory_listing = 1;
    server->file_config->file_cache_enabled = 0;
    server->file_config->max_file_size = 10 * 1024 * 1024; // 10MB
    
    // Create server socket
    server->connection_pool->server_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (server->connection_pool->server_socket < 0) {
        logMessage(server->logger, LOG_ERROR, "Failed to create server socket", NULL, NULL, 0);
        free(server);
        return NULL;
    }
    
    // Set socket options
    int opt = 1;
    setsockopt(server->connection_pool->server_socket, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));
    
    // Bind socket
    server->connection_pool->server_addr.sin_family = AF_INET;
    server->connection_pool->server_addr.sin_addr.s_addr = INADDR_ANY;
    server->connection_pool->server_addr.sin_port = htons(port);
    
    if (bind(server->connection_pool->server_socket, 
             (struct sockaddr*)&server->connection_pool->server_addr, 
             sizeof(server->connection_pool->server_addr)) < 0) {
        logMessage(server->logger, LOG_ERROR, "Failed to bind server socket", NULL, NULL, 0);
        close(server->connection_pool->server_socket);
        free(server);
        return NULL;
    }
    
    // Start listening
    if (listen(server->connection_pool->server_socket, BACKLOG) < 0) {
        logMessage(server->logger, LOG_ERROR, "Failed to listen on server socket", NULL, NULL, 0);
        close(server->connection_pool->server_socket);
        free(server);
        return NULL;
    }
    
    logMessage(server->logger, LOG_INFO, "Web server initialized", NULL, NULL, 0);
    return server;
}

// Handle HTTP request
int handleHTTPRequest(WebServer* server, int client_index) {
    Client* client = &server->connection_pool->clients[client_index];
    HTTPRequest* request = &client->current_request;
    HTTPResponse* response = &client->current_response;
    
    // Initialize response
    memset(response, 0, sizeof(HTTPResponse));
    response->status = HTTP_200_OK;
    strcpy(response->content_type, "text/html");
    
    // Check rate limit
    if (!checkRateLimit(server->security_config, client->remote_addr, 
                       server->rate_limits, &server->rate_limit_count)) {
        response->status = HTTP_429_TOO_MANY_REQUESTS;
        strcpy(response->body, "429 Too Many Requests");
        response->body_length = strlen(response->body);
        logMessage(server->logger, LOG_WARNING, "Rate limit exceeded", client->remote_addr, request->path, 429);
        return 0;
    }
    
    // Add security headers
    addCORSHeaders(response, server->security_config);
    
    // Handle OPTIONS request for CORS
    if (request->method == HTTP_OPTIONS) {
        response->status = HTTP_200_OK;
        response->body_length = 0;
        return 0;
    }
    
    // Find matching route
    Route* route = findRoute(server->router, request->path, request->method);
    
    if (route) {
        // Call route handler
        if (route->handler(request, response) == 0) {
            logMessage(server->logger, LOG_INFO, "Request handled by route", client->remote_addr, request->path, response->status);
        } else {
            response->status = HTTP_500_INTERNAL_SERVER_ERROR;
            strcpy(response->body, "500 Internal Server Error - Handler failed");
            response->body_length = strlen(response->body);
            logMessage(server->logger, LOG_ERROR, "Route handler failed", client->remote_addr, request->path, 500);
        }
    } else {
        // Try to serve static file
        if (serveStaticFile(request, response, server->file_config->document_root) == 0) {
            logMessage(server->logger, LOG_INFO, "Static file served", client->remote_addr, request->path, response->status);
        } else {
            response->status = HTTP_404_NOT_FOUND;
            strcpy(response->body, "404 Not Found - No route or file found");
            response->body_length = strlen(response->body);
            logMessage(server->logger, LOG_INFO, "404 Not Found", client->remote_addr, request->path, 404);
        }
    }
    
    return 0;
}

// Send HTTP response
int sendHTTPResponse(WebServer* server, int client_index) {
    Client* client = &server->connection_pool->clients[client_index];
    HTTPResponse* response = &client->current_response;
    
    char response_buffer[MAX_RESPONSE_SIZE];
    int response_length = buildHTTPResponse(response, response_buffer, sizeof(response_buffer));
    
    if (response_length < 0) {
        logMessage(server->logger, LOG_ERROR, "Failed to build HTTP response", client->remote_addr, NULL, 0);
        return -1;
    }
    
    int bytes_sent = send(client->socket_fd, response_buffer, response_length, 0);
    if (bytes_sent < 0) {
        logMessage(server->logger, LOG_ERROR, "Failed to send HTTP response", client->remote_addr, NULL, 0);
        return -1;
    }
    
    return 0;
}

// Accept new connection
int acceptConnection(WebServer* server) {
    struct sockaddr_in client_addr;
    socklen_t client_len = sizeof(client_addr);
    
    int client_socket = accept(server->connection_pool->server_socket, 
                               (struct sockaddr*)&client_addr, &client_len);
    
    if (client_socket < 0) {
        logMessage(server->logger, LOG_ERROR, "Failed to accept connection", NULL, NULL, 0);
        return -1;
    }
    
    // Set socket to non-blocking
    int flags = fcntl(client_socket, F_GETFL, 0);
    fcntl(client_socket, F_SETFL, flags | O_NONBLOCK);
    
    // Add client to pool
    int client_index = addClient(server->connection_pool, client_socket, &client_addr);
    if (client_index < 0) {
        close(client_socket);
        logMessage(server->logger, LOG_WARNING, "Connection pool full, rejecting client", NULL, NULL, 0);
        return -1;
    }
    
    logMessage(server->logger, LOG_INFO, "New client connected", 
               server->connection_pool->clients[client_index].remote_addr, NULL, 0);
    
    return client_index;
}

// Process client request
int processClientRequest(WebServer* server, int client_index) {
    Client* client = &server->connection_pool->clients[client_index];
    
    if (client->request_complete) {
        return 0; // Already processed
    }
    
    char buffer[BUFFER_SIZE];
    int bytes_received = recv(client->socket_fd, buffer, sizeof(buffer) - 1, 0);
    
    if (bytes_received <= 0) {
        return -1; // Connection closed or error
    }
    
    buffer[bytes_received] = '\0';
    
    // Parse HTTP request
    if (parseHTTPRequest(buffer, bytes_received, &client->current_request) == 0) {
        client->request_complete = 1;
        client->last_activity = time(NULL);
        
        // Copy remote address to request
        strcpy(client->current_request.remote_addr, client->remote_addr);
        
        // Handle request
        handleHTTPRequest(server, client_index);
        
        return 0;
    } else {
        logMessage(server->logger, LOG_ERROR, "Failed to parse HTTP request", client->remote_addr, NULL, 0);
        return -1;
    }
}

// Main server loop
void runServer(WebServer* server) {
    server->running = 1;
    
    logMessage(server->logger, LOG_INFO, "Web server started", NULL, NULL, 0);
    printf("Web server started on port %d\n", ntohs(server->connection_pool->server_addr.sin_port));
    printf("Document root: %s\n", server->file_config->document_root);
    printf("Press Ctrl+C to stop the server\n");
    
    fd_set read_fds;
    int max_fd;
    
    while (server->running) {
        FD_ZERO(&read_fds);
        
        // Add server socket to read set
        FD_SET(server->connection_pool->server_socket, &read_fds);
        max_fd = server->connection_pool->server_socket;
        
        // Add client sockets to read set
        for (int i = 0; i < server->connection_pool->max_clients; i++) {
            Client* client = &server->connection_pool->clients[i];
            if (client->is_active && client->socket_fd > 0) {
                FD_SET(client->socket_fd, &read_fds);
                if (client->socket_fd > max_fd) {
                    max_fd = client->socket_fd;
                }
            }
        }
        
        // Set timeout for select
        struct timeval timeout;
        timeout.tv_sec = 1;
        timeout.tv_usec = 0;
        
        int activity = select(max_fd + 1, &read_fds, NULL, NULL, &timeout);
        
        if (activity < 0) {
            logMessage(server->logger, LOG_ERROR, "Select error", NULL, NULL, 0);
            continue;
        }
        
        if (activity == 0) {
            // Timeout - perform cleanup tasks
            cleanupExpiredSessions(server->session_manager);
            continue;
        }
        
        // Check for new connections
        if (FD_ISSET(server->connection_pool->server_socket, &read_fds)) {
            acceptConnection(server);
        }
        
        // Check client activity
        for (int i = 0; i < server->connection_pool->max_clients; i++) {
            Client* client = &server->connection_pool->clients[i];
            
            if (!client->is_active || client->socket_fd < 0) {
                continue;
            }
            
            if (FD_ISSET(client->socket_fd, &read_fds)) {
                if (processClientRequest(server, i) < 0) {
                    // Client disconnected or error
                    logMessage(server->logger, LOG_INFO, "Client disconnected", client->remote_addr, NULL, 0);
                    removeClient(server->connection_pool, i);
                    continue;
                }
                
                // Send response if ready
                if (client->request_complete && !client->response_sent) {
                    if (sendHTTPResponse(server, i) == 0) {
                        client->response_sent = 1;
                        
                        // Close connection if not keep-alive
                        if (!client->current_response.keep_alive) {
                            removeClient(server->connection_pool, i);
                        }
                    } else {
                        logMessage(server->logger, LOG_ERROR, "Failed to send response", client->remote_addr, NULL, 0);
                        removeClient(server->connection_pool, i);
                    }
                }
            }
        }
    }
}

// =============================================================================
// ROUTE HANDLERS
// =============================================================================

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

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateHTTPProtocol() {
    printf("=== HTTP PROTOCOL DEMO ===\n");
    
    // Test HTTP request parsing
    const char* sample_request = 
        "GET /index.html?page=1 HTTP/1.1\r\n"
        "Host: localhost:8080\r\n"
        "User-Agent: Mozilla/5.0\r\n"
        "Accept: text/html\r\n"
        "\r\n"
        "Request body here";
    
    HTTPRequest request;
    if (parseHTTPRequest(sample_request, strlen(sample_request), &request) == 0) {
        printf("Parsed HTTP Request:\n");
        printf("Method: %d\n", request.method);
        printf("Path: %s\n", request.path);
        printf("Query String: %s\n", request.query_string);
        printf("Headers: %d\n", request.header_count);
        
        for (int i = 0; i < request.header_count; i++) {
            printf("  %s\n", request.headers[i]);
        }
        
        printf("Body Length: %d\n", request.body_length);
    } else {
        printf("Failed to parse HTTP request\n");
    }
    
    // Test HTTP response building
    HTTPResponse response;
    response.status = HTTP_200_OK;
    strcpy(response.body, "<html><body>Hello World!</body></html>");
    response.body_length = strlen(response.body);
    strcpy(response.content_type, "text/html");
    response.header_count = 0;
    
    char response_buffer[MAX_RESPONSE_SIZE];
    int response_length = buildHTTPResponse(&response, response_buffer, sizeof(response_buffer));
    
    printf("\nBuilt HTTP Response:\n");
    printf("Length: %d\n", response_length);
    printf("Response:\n%s\n", response_buffer);
}

void demonstrateFileServing() {
    printf("\n=== FILE SERVING DEMO ===\n");
    
    // Create a test HTML file
    FILE* test_file = fopen("test.html", "w");
    if (test_file) {
        fprintf(test_file, 
            "<!DOCTYPE html><html><head><title>Test File</title></head>"
            "<body><h1>This is a test file</h1><p>Served by C web server</p></body></html>");
        fclose(test_file);
        
        printf("Created test.html file\n");
        
        // Test MIME type detection
        printf("MIME type for .html: %s\n", getMimeType("test.html"));
        printf("MIME type for .css: %s\n", getMimeType("style.css"));
        printf("MIME type for .js: %s\n", getMimeType("script.js"));
        printf("MIME type for .jpg: %s\n", getMimeType("image.jpg"));
        
        // Test file serving
        HTTPRequest request;
        strcpy(request.path, "/test.html");
        request.method = HTTP_GET;
        
        HTTPResponse response;
        if (serveStaticFile(&request, &response, ".") == 0) {
            printf("Served file successfully\n");
            printf("Status: %d\n", response.status);
            printf("Content-Type: %s\n", response.content_type);
            printf("Body Length: %d\n", response.body_length);
            printf("Body: %s\n", response.body);
        } else {
            printf("Failed to serve file\n");
        }
        
        // Clean up
        remove("test.html");
    } else {
        printf("Failed to create test file\n");
    }
}

void demonstrateRouting() {
    printf("\n=== ROUTING DEMO ===\n");
    
    Router* router = initRouter();
    if (!router) {
        printf("Failed to initialize router\n");
        return;
    }
    
    // Add routes
    addRoute(router, "/", HTTP_GET, defaultHandler);
    addRoute(router, "/test", HTTP_GET, testHandler);
    addRoute(router, "/api/data", HTTP_GET, apiHandler);
    addRoute(router, "/api/*", HTTP_GET, apiHandler); // Wildcard route
    
    printf("Added %d routes\n", router->route_count);
    
    // Test route matching
    Route* route;
    
    route = findRoute(router, "/", HTTP_GET);
    printf("Route for '/': %s\n", route ? "Found" : "Not found");
    
    route = findRoute(router, "/test", HTTP_GET);
    printf("Route for '/test': %s\n", route ? "Found" : "Not found");
    
    route = findRoute(router, "/api/data", HTTP_GET);
    printf("Route for '/api/data': %s\n", route ? "Found" : "Not found");
    
    route = findRoute(router, "/api/users", HTTP_GET); // Should match wildcard
    printf("Route for '/api/users': %s\n", route ? "Found" : "Not found");
    
    route = findRoute(router, "/nonexistent", HTTP_GET);
    printf("Route for '/nonexistent': %s\n", route ? "Found" : "Not found");
    
    free(router);
}

void demonstrateSessionManagement() {
    printf("\n=== SESSION MANAGEMENT DEMO ===\n");
    
    SessionManager* manager = initSessionManager(3600);
    if (!manager) {
        printf("Failed to initialize session manager\n");
        return;
    }
    
    // Create sessions
    Session* session1 = createSession(manager);
    Session* session2 = createSession(manager);
    
    if (session1 && session2) {
        printf("Created sessions:\n");
        printf("Session 1 ID: %s\n", session1->session_id);
        printf("Session 2 ID: %s\n", session2->session_id);
        
        // Store some data in session 1
        strcpy(session1->data, "user_id=123;username=test");
        
        // Retrieve session
        Session* retrieved = getSession(manager, session1->session_id);
        if (retrieved) {
            printf("Retrieved session data: %s\n", retrieved->data);
        }
        
        printf("Active sessions: %d\n", manager->session_count);
        
        // Clean up
        free(manager);
    } else {
        printf("Failed to create sessions\n");
    }
}

void demonstrateSecurity() {
    printf("\n=== SECURITY DEMO ===\n");
    
    SecurityConfig* config = initSecurityConfig();
    if (!config) {
        printf("Failed to initialize security config\n");
        return;
    }
    
    printf("Security Configuration:\n");
    printf("CORS enabled: %s\n", config->enable_cors ? "Yes" : "No");
    printf("Allowed origins: %s\n", config->allowed_origins);
    printf("Allowed methods: %s\n", config->allowed_methods);
    printf("Rate limiting enabled: %s\n", config->enable_rate_limiting ? "Yes" : "No");
    printf("Max requests per minute: %d\n", config->max_requests_per_minute);
    
    // Test rate limiting
    RateLimitEntry rate_limits[MAX_CLIENTS];
    int rate_limit_count = 0;
    
    const char* test_ip = "192.168.1.100";
    
    for (int i = 0; i < 65; i++) {
        int allowed = checkRateLimit(config, test_ip, rate_limits, &rate_limit_count);
        printf("Request %d: %s\n", i + 1, allowed ? "Allowed" : "Denied");
    }
    
    free(config);
}

void demonstrateLogging() {
    printf("\n=== LOGGING DEMO ===\n");
    
    Logger* logger = initLogger("demo.log", LOG_DEBUG, 1, 1);
    if (!logger) {
        printf("Failed to initialize logger\n");
        return;
    }
    
    printf("Logger initialized\n");
    printf("Log file: %s\n", logger->log_file_path);
    printf("Console output: %s\n", logger->console_output ? "Yes" : "No");
    printf("File output: %s\n", logger->file_output ? "Yes" : "No");
    
    // Log messages at different levels
    logMessage(logger, LOG_DEBUG, "Debug message", "127.0.0.1", "/debug", 200);
    logMessage(logger, LOG_INFO, "Info message", "127.0.0.1", "/info", 200);
    logMessage(logger, LOG_WARNING, "Warning message", "127.0.0.1", "/warning", 400);
    logMessage(logger, LOG_ERROR, "Error message", "127.0.0.1", "/error", 500);
    logMessage(logger, LOG_CRITICAL, "Critical message", "127.0.0.1", "/critical", 500);
    
    printf("Logged %d messages\n");
    
    if (logger->log_file) {
        fclose(logger->log_file);
    }
    free(logger);
    
    // Clean up log file
    remove("demo.log");
}

void demonstrateWebServer() {
    printf("\n=== WEB SERVER DEMO ===\n");
    
    // Create web server
    WebServer* server = initWebServer(8080, ".");
    if (!server) {
        printf("Failed to initialize web server\n");
        return;
    }
    
    // Add routes
    addRoute(server->router, "/", HTTP_GET, defaultHandler);
    addRoute(server->router, "/test", HTTP_GET, testHandler);
    addRoute(server->router, "/api/data", HTTP_GET, apiHandler);
    
    printf("Web server initialized successfully\n");
    printf("Routes added: %d\n", server->router->route_count);
    printf("Document root: %s\n", server->file_config->document_root);
    printf("Max clients: %d\n", server->connection_pool->max_clients);
    
    // Create a simple test HTML file
    FILE* index_file = fopen("index.html", "w");
    if (index_file) {
        fprintf(index_file, 
            "<!DOCTYPE html><html><head><title>C Web Server</title></head>"
            "<body><h1>C Web Server</h1><p>This is the default page.</p>"
            "<p><a href='/test'>Test Page</a> | <a href='/api/data'>API</a></p>"
            "</body></html>");
        fclose(index_file);
        printf("Created index.html\n");
    }
    
    printf("\nWeb server is ready to run!\n");
    printf("To test the server:\n");
    printf("1. Open a web browser and navigate to http://localhost:8080\n");
    printf("2. Try http://localhost:8080/test\n");
    printf("3. Try http://localhost:8080/api/data\n");
    printf("4. Try accessing static files like http://localhost:8080/index.html\n");
    
    // Note: In a real implementation, you would call runServer(server) here
    // For demonstration purposes, we'll just show the server configuration
    printf("\nServer configuration completed successfully!\n");
    
    // Clean up (normally you'd keep the server running)
    remove("index.html");
    
    // Free server resources
    free(server->connection_pool);
    free(server->router);
    free(server->session_manager);
    free(server->security_config);
    free(server->logger);
    free(server->file_config);
    free(server->rate_limits);
    free(server);
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Web Server Development Examples\n");
    printf("==============================\n\n");
    
    // Seed random number generator
    srand(time(NULL));
    
    // Run all demonstrations
    demonstrateHTTPProtocol();
    demonstrateFileServing();
    demonstrateRouting();
    demonstrateSessionManagement();
    demonstrateSecurity();
    demonstrateLogging();
    demonstrateWebServer();
    
    printf("\nAll web server examples demonstrated!\n");
    printf("Key features implemented:\n");
    printf("- HTTP protocol parsing and response generation\n");
    printf("- Static file serving with MIME type detection\n");
    printf("- Dynamic routing system with handlers\n");
    printf("- Session management for user state\n");
    printf("- Security features (CORS, rate limiting)\n");
    printf("- Comprehensive logging system\n");
    printf("- Connection pooling and client management\n");
    printf("- Configurable file serving\n");
    printf("- Multi-client support with select()\n");
    printf("- Non-blocking I/O for performance\n");
    
    return 0;
}
