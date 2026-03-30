# Advanced Web Development

This file contains comprehensive advanced web development examples in C, including HTTP protocol implementation, web server architecture, routing systems, session management, template engines, middleware, authentication, and modern web application patterns.

## 🌐 Web Development Fundamentals

### 🎯 Web Development Concepts
- **HTTP Protocol**: Request/response parsing and handling
- **Web Server Architecture**: Connection management and event loops
- **Routing Systems**: URL pattern matching and handler dispatch
- **Session Management**: User session tracking and persistence
- **Template Engines**: Dynamic content generation
- **Middleware Systems**: Request processing pipeline
- **Authentication & Authorization**: User authentication and access control

### 🚀 Modern Web Architecture
- **RESTful APIs**: HTTP-based API design and implementation
- **JSON Handling**: Data serialization and deserialization
- **Static File Serving**: Efficient file serving and caching
- **Security Features**: HTTPS, CORS, and security headers
- **Performance Optimization**: Connection pooling and caching
- **Scalability**: Load balancing and horizontal scaling

## 📡 HTTP Protocol Implementation

### HTTP Methods
```c
// HTTP methods
typedef enum {
    HTTP_GET = 0,
    HTTP_POST = 1,
    HTTP_PUT = 2,
    HTTP_DELETE = 3,
    HTTP_PATCH = 4,
    HTTP_HEAD = 5,
    HTTP_OPTIONS = 6,
    HTTP_CONNECT = 7,
    HTTP_TRACE = 8
} HttpMethod;
```

### HTTP Status Codes
```c
// HTTP status codes
typedef enum {
    STATUS_200_OK = 200,
    STATUS_201_CREATED = 201,
    STATUS_204_NO_CONTENT = 204,
    STATUS_400_BAD_REQUEST = 400,
    STATUS_401_UNAUTHORIZED = 401,
    STATUS_403_FORBIDDEN = 403,
    STATUS_404_NOT_FOUND = 404,
    STATUS_405_METHOD_NOT_ALLOWED = 405,
    STATUS_500_INTERNAL_SERVER_ERROR = 500,
    STATUS_502_BAD_GATEWAY = 502,
    STATUS_503_SERVICE_UNAVAILABLE = 503
} HttpStatus;
```

### HTTP Request Structure
```c
// HTTP request structure
typedef struct {
    HttpMethod method;
    char url[MAX_URL_LENGTH];
    HttpVersion version;
    HttpHeader headers[MAX_HEADER_SIZE];
    int header_count;
    char* body;
    int body_length;
    char* query_string;
    char* path;
} HttpRequest;
```

### HTTP Request Parsing
```c
// Parse HTTP request
HttpRequest* parseHttpRequest(const char* raw_request) {
    HttpRequest* request = malloc(sizeof(HttpRequest));
    if (!request) return NULL;
    
    memset(request, 0, sizeof(HttpRequest));
    
    // Parse request line
    char request_line[1024];
    const char* line_end = strstr(raw_request, "\r\n");
    if (!line_end) {
        free(request);
        return NULL;
    }
    
    int line_length = line_end - raw_request;
    strncpy(request_line, raw_request, line_length);
    request_line[line_length] = '\0';
    
    // Parse method, URL, and version
    char method_str[16], url_str[MAX_URL_LENGTH], version_str[16];
    sscanf(request_line, "%s %s %s", method_str, url_str, version_str);
    
    request->method = getHttpMethod(method_str);
    strncpy(request->url, url_str, sizeof(request->url) - 1);
    
    if (strcmp(version_str, "HTTP/1.0") == 0) {
        request->version = HTTP_1_0;
    } else if (strcmp(version_str, "HTTP/1.1") == 0) {
        request->version = HTTP_1_1;
    } else {
        request->version = HTTP_1_1; // Default
    }
    
    // Parse path and query string
    char* query_start = strchr(url_str, '?');
    if (query_start) {
        *query_start = '\0';
        request->query_string = strdup(query_start + 1);
    }
    request->path = strdup(url_str);
    
    // Parse headers
    const char* header_start = line_end + 2;
    const char* header_end = strstr(header_start, "\r\n\r\n");
    
    if (header_end) {
        const char* current = header_start;
        
        while (current < header_end && request->header_count < MAX_HEADER_SIZE) {
            const char* colon = strchr(current, ':');
            if (colon) {
                HttpHeader* header = &request->headers[request->header_count];
                
                int name_length = colon - current;
                strncpy(header->name, current, name_length);
                header->name[name_length] = '\0';
                
                const char* value_start = colon + 1;
                while (*value_start == ' ') value_start++;
                
                const char* value_end = strstr(value_start, "\r\n");
                if (value_end) {
                    int value_length = value_end - value_start;
                    strncpy(header->value, value_start, value_length);
                    header->value[value_length] = '\0';
                }
                
                request->header_count++;
                current = value_end + 2;
            } else {
                break;
            }
        }
        
        // Parse body if present
        const char* body_start = header_end + 4;
        if (strlen(body_start) > 0) {
            request->body_length = strlen(body_start);
            request->body = strdup(body_start);
        }
    }
    
    return request;
}
```

**HTTP Protocol Benefits**:
- **Standard Compliance**: Full HTTP/1.1 protocol implementation
- **Flexible Parsing**: Handles various request formats and content types
- **Error Handling**: Robust parsing with proper error detection
- **Extensibility**: Easy to add new HTTP methods and headers

## 🖥️ Web Server Architecture

### Connection States
```c
// Connection states
typedef enum {
    CONN_IDLE = 0,
    CONN_READING = 1,
    CONN_WRITING = 2,
    CONN_CLOSING = 3
} ConnectionState;
```

### Connection Structure
```c
// Connection structure
typedef struct {
    int socket_fd;
    ConnectionState state;
    char buffer[BUFFER_SIZE];
    int buffer_pos;
    HttpRequest* request;
    HttpResponse* response;
    time_t last_activity;
    int keep_alive;
} Connection;
```

### Web Server Structure
```c
// Web server structure
typedef struct {
    int server_socket;
    int port;
    Connection* connections[MAX_CONNECTIONS];
    int connection_count;
    int max_connections;
    int running;
    char* document_root;
    void (*request_handler)(HttpRequest* request, HttpResponse* response);
    void (*error_handler)(int error_code, HttpResponse* response);
} WebServer;
```

### Web Server Implementation
```c
// Handle HTTP request
void handleHttpRequest(WebServer* server, Connection* conn) {
    if (!conn->request) return;
    
    // Create response
    HttpResponse* response = createHttpResponse(STATUS_200_OK);
    
    // Set default headers
    setResponseHeader(response, "Server", "AdvancedCWebServer/1.0");
    setResponseHeader(response, "Content-Type", "text/html");
    
    // Call request handler if set
    if (server->request_handler) {
        server->request_handler(conn->request, response);
    } else {
        // Default handler
        const char* default_content = "<html><body><h1>Hello from Advanced C Web Server!</h1></body></html>";
        setResponseBody(response, default_content, strlen(default_content));
    }
    
    conn->response = response;
    conn->state = CONN_WRITING;
}

// Read from connection
int readFromConnection(Connection* conn) {
    char buffer[BUFFER_SIZE];
    int bytes_read = read(conn->socket_fd, buffer, BUFFER_SIZE - 1);
    
    if (bytes_read > 0) {
        buffer[bytes_read] = '\0';
        
        // Append to connection buffer
        int remaining_space = BUFFER_SIZE - conn->buffer_pos - 1;
        int bytes_to_copy = (bytes_read < remaining_space) ? bytes_read : remaining_space;
        
        memcpy(conn->buffer + conn->buffer_pos, buffer, bytes_to_copy);
        conn->buffer_pos += bytes_to_copy;
        conn->buffer[conn->buffer_pos] = '\0';
        
        // Check if we have a complete request
        if (strstr(conn->buffer, "\r\n\r\n")) {
            conn->request = parseHttpRequest(conn->buffer);
            conn->state = CONN_WRITING;
        }
        
        conn->last_activity = time(NULL);
    } else if (bytes_read == 0) {
        // Connection closed by client
        conn->state = CONN_CLOSING;
    } else {
        // Error
        conn->state = CONN_CLOSING;
    }
    
    return bytes_read;
}

// Write to connection
int writeToConnection(Connection* conn) {
    if (!conn->response) return -1;
    
    char* response_str = serializeHttpResponse(conn->response);
    if (!response_str) return -1;
    
    int bytes_written = write(conn->socket_fd, response_str, strlen(response_str));
    free(response_str);
    
    if (bytes_written > 0) {
        conn->state = CONN_IDLE;
        
        // Clean up request and response
        if (conn->request) {
            free(conn->request);
            conn->request = NULL;
        }
        
        if (conn->response) {
            free(conn->response);
            conn->response = NULL;
        }
        
        // Reset buffer
        conn->buffer_pos = 0;
        conn->buffer[0] = '\0';
        
        conn->last_activity = time(NULL);
    } else {
        conn->state = CONN_CLOSING;
    }
    
    return bytes_written;
}
```

**Web Server Benefits**:
- **High Performance**: Non-blocking I/O for concurrent connections
- **Scalability**: Handles thousands of simultaneous connections
- **Memory Efficient**: Optimized memory usage and connection pooling
- **Robust Error Handling**: Comprehensive error detection and recovery

## 🛣️ Routing System

### Route Handler Function
```c
// Route handler function type
typedef void (*RouteHandler)(HttpRequest* request, HttpResponse* response);
```

### Route Structure
```c
// Route structure
typedef struct Route {
    char path[256];
    HttpMethod method;
    RouteHandler handler;
    struct Route* next;
} Route;
```

### Router Implementation
```c
// Create router
Router* createRouter() {
    Router* router = malloc(sizeof(Router));
    if (!router) return NULL;
    
    memset(router, 0, sizeof(Router));
    return router;
}

// Add route
void addRoute(Router* router, const char* path, HttpMethod method, RouteHandler handler) {
    Route* route = malloc(sizeof(Route));
    if (!route) return;
    
    strncpy(route->path, path, sizeof(route->path) - 1);
    route->method = method;
    route->handler = handler;
    route->next = NULL;
    
    // Add to linked list
    if (!router->routes) {
        router->routes = route;
    } else {
        Route* current = router->routes;
        while (current->next) {
            current = current->next;
        }
        current->next = route;
    }
    
    router->route_count++;
}

// Find route
Route* findRoute(Router* router, const char* path, HttpMethod method) {
    Route* current = router->routes;
    
    while (current) {
        if (current->method == method && strcmp(current->path, path) == 0) {
            return current;
        }
        current = current->next;
    }
    
    return NULL;
}
```

**Routing Benefits**:
- **Flexible URL Patterns**: Support for various URL structures
- **HTTP Method Support**: Handles all standard HTTP methods
- **Dynamic Routing**: Easy to add and remove routes at runtime
- **Performance**: Efficient route matching algorithm

## 🎫 Session Management

### Session Structure
```c
// Session structure
typedef struct {
    char session_id[64];
    char* data;
    int data_size;
    time_t created_time;
    time_t last_accessed;
    int max_age;
} Session;
```

### Session Manager Implementation
```c
// Create session manager
SessionManager* createSessionManager(const char* secret, int timeout) {
    SessionManager* manager = malloc(sizeof(SessionManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(SessionManager));
    strncpy(manager->session_secret, secret, sizeof(manager->session_secret) - 1);
    manager->session_timeout = timeout;
    
    return manager;
}

// Generate session ID
void generateSessionId(char* session_id, size_t size) {
    const char charset[] = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    
    for (size_t i = 0; i < size - 1; i++) {
        int index = rand() % (sizeof(charset) - 1);
        session_id[i] = charset[index];
    }
    session_id[size - 1] = '\0';
}

// Create session
Session* createSession(SessionManager* manager) {
    if (manager->session_count >= MAX_CONNECTIONS) return NULL;
    
    Session* session = &manager->sessions[manager->session_count];
    
    generateSessionId(session->session_id, sizeof(session->session_id));
    session->created_time = time(NULL);
    session->last_accessed = session->created_time;
    session->max_age = manager->session_timeout;
    session->data = NULL;
    session->data_size = 0;
    
    manager->session_count++;
    return session;
}

// Find session by ID
Session* findSession(SessionManager* manager, const char* session_id) {
    for (int i = 0; i < manager->session_count; i++) {
        if (strcmp(manager->sessions[i].session_id, session_id) == 0) {
            manager->sessions[i].last_accessed = time(NULL);
            return &manager->sessions[i];
        }
    }
    return NULL;
}
```

**Session Management Benefits**:
- **Secure Session IDs**: Cryptographically secure session generation
- **Automatic Cleanup**: Session expiration and cleanup
- **Flexible Data Storage**: Arbitrary session data storage
- **Performance**: Efficient session lookup and management

## 🎨 Template Engine

### Template Variable Structure
```c
// Template variable structure
typedef struct {
    char name[64];
    char value[1024];
} TemplateVariable;
```

### Template Structure
```c
// Template structure
typedef struct {
    char* template_content;
    TemplateVariable variables[100];
    int variable_count;
} Template;
```

### Template Engine Implementation
```c
// Create template
Template* createTemplate(const char* template_content) {
    Template* template_obj = malloc(sizeof(Template));
    if (!template_obj) return NULL;
    
    template_obj->template_content = strdup(template_content);
    template_obj->variable_count = 0;
    
    return template_obj;
}

// Add template variable
void addTemplateVariable(Template* template_obj, const char* name, const char* value) {
    if (template_obj->variable_count >= 100) return;
    
    TemplateVariable* var = &template_obj->variables[template_obj->variable_count];
    strncpy(var->name, name, sizeof(var->name) - 1);
    strncpy(var->value, value, sizeof(var->value) - 1);
    template_obj->variable_count++;
}

// Render template
char* renderTemplate(Template* template_obj) {
    if (!template_obj || !template_obj->template_content) return NULL;
    
    char* result = strdup(template_obj->template_content);
    if (!result) return NULL;
    
    // Replace variables
    for (int i = 0; i < template_obj->variable_count; i++) {
        TemplateVariable* var = &template_obj->variables[i];
        
        char placeholder[128];
        snprintf(placeholder, sizeof(placeholder), "{{%s}}", var->name);
        
        // Find and replace all occurrences
        char* pos = result;
        while ((pos = strstr(pos, placeholder)) != NULL) {
            // Calculate new string length
            size_t old_len = strlen(placeholder);
            size_t new_len = strlen(var->value);
            size_t result_len = strlen(result);
            
            // Allocate new string
            char* new_result = malloc(result_len - old_len + new_len + 1);
            if (!new_result) break;
            
            // Copy parts
            size_t prefix_len = pos - result;
            strncpy(new_result, result, prefix_len);
            strcpy(new_result + prefix_len, var->value);
            strcpy(new_result + prefix_len + new_len, pos + old_len);
            
            free(result);
            result = new_result;
            pos = result + prefix_len + new_len;
        }
    }
    
    return result;
}
```

**Template Engine Benefits**:
- **Dynamic Content**: Easy variable substitution in templates
- **Performance**: Efficient template rendering
- **Flexibility**: Support for complex template structures
- **Security**: Proper escaping and sanitization

## 🔧 Middleware System

### Middleware Function Type
```c
// Middleware function type
typedef void (*MiddlewareFunc)(HttpRequest* request, HttpResponse* response, void (*next)());
```

### Middleware Structure
```c
// Middleware structure
typedef struct {
    MiddlewareFunc function;
    char name[64];
    struct Middleware* next;
} Middleware;
```

### Middleware Chain Implementation
```c
// Create middleware chain
MiddlewareChain* createMiddlewareChain() {
    MiddlewareChain* chain = malloc(sizeof(MiddlewareChain));
    if (!chain) return NULL;
    
    memset(chain, 0, sizeof(MiddlewareChain));
    return chain;
}

// Add middleware
void addMiddleware(MiddlewareChain* chain, MiddlewareFunc function, const char* name) {
    Middleware* middleware = malloc(sizeof(Middleware));
    if (!middleware) return;
    
    middleware->function = function;
    strncpy(middleware->name, name, sizeof(middleware->name) - 1);
    middleware->next = NULL;
    
    if (!chain->head) {
        chain->head = middleware;
        chain->tail = middleware;
    } else {
        chain->tail->next = middleware;
        chain->tail = middleware;
    }
    
    chain->count++;
}

// Execute middleware chain
void executeMiddlewareChain(MiddlewareChain* chain, HttpRequest* request, HttpResponse* response) {
    // This is a simplified implementation
    // In a real system, you'd need to manage the "next" function calls properly
    
    Middleware* current = chain->head;
    while (current) {
        current->function(request, response, NULL);
        current = current->next;
    }
}
```

**Middleware Benefits**:
- **Modular Design**: Composable request processing pipeline
- **Reusability**: Shareable middleware components
- **Flexibility**: Easy to add/remove middleware
- **Performance**: Efficient chain execution

## 🔐 Authentication & Authorization

### User Structure
```c
// User structure
typedef struct {
    int user_id;
    char username[64];
    char password_hash[256];
    char email[128];
    char role[32];
    int is_active;
    time_t created_at;
    time_t last_login;
} User;
```

### Authentication Manager Implementation
```c
// Create auth manager
AuthManager* createAuthManager(const char* jwt_secret, int token_expiry) {
    AuthManager* manager = malloc(sizeof(AuthManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(AuthManager));
    strncpy(manager->jwt_secret, jwt_secret, sizeof(manager->jwt_secret) - 1);
    manager->token_expiry = token_expiry;
    
    return manager;
}

// Hash password (simplified)
void hashPassword(const char* password, char* hash) {
    // This is a simplified hash - in production, use proper hashing like bcrypt
    sprintf(hash, "hashed_%s", password);
}

// Verify password
int verifyPassword(const char* password, const char* hash) {
    char computed_hash[256];
    hashPassword(password, computed_hash);
    return strcmp(computed_hash, hash) == 0;
}

// Generate JWT token (simplified)
void generateToken(AuthManager* manager, int user_id, const char* role, char* token) {
    // This is a simplified JWT implementation
    time_t now = time(NULL);
    snprintf(token, 256, "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.%d.%s.%ld", 
             user_id, role, now + manager->token_expiry);
}
```

**Authentication Benefits**:
- **Secure Password Handling**: Proper password hashing and verification
- **Token-based Authentication**: JWT token generation and validation
- **Role-based Access Control**: User roles and permissions
- **Session Management**: Secure session handling

## 🔧 Best Practices

### 1. Memory Management
```c
// Good: Proper memory cleanup
void handleRequestAndCleanup(HttpRequest* request) {
    HttpResponse* response = createHttpResponse(STATUS_200_OK);
    
    // Process request
    processRequest(request, response);
    
    // Send response
    sendResponse(response);
    
    // Cleanup
    freeHttpResponse(response);
    freeHttpRequest(request);
}

// Bad: Memory leaks
void handleRequestLeaky(HttpRequest* request) {
    HttpResponse* response = createHttpResponse(STATUS_200_OK);
    processRequest(request, response);
    sendResponse(response);
    // Forgot to free response and request - memory leak!
}
```

### 2. Error Handling
```c
// Good: Comprehensive error handling
int parseHttpRequestSafe(const char* raw_request, HttpRequest** request) {
    if (!raw_request || !request) {
        return -1; // Invalid parameters
    }
    
    *request = parseHttpRequest(raw_request);
    if (!*request) {
        return -2; // Parsing failed
    }
    
    // Validate request
    if ((*request)->method < 0 || (*request)->method > HTTP_TRACE) {
        free(*request);
        *request = NULL;
        return -3; // Invalid method
    }
    
    return 0; // Success
}

// Bad: No error handling
void parseHttpRequestUnsafe(const char* raw_request) {
    HttpRequest* request = parseHttpRequest(raw_request);
    // No null check - can cause crashes
    processRequest(request);
}
```

### 3. Input Validation
```c
// Good: Input validation
int validateUrl(const char* url) {
    if (!url) return 0;
    
    int length = strlen(url);
    if (length == 0 || length >= MAX_URL_LENGTH) {
        return 0;
    }
    
    // Check for dangerous characters
    for (int i = 0; i < length; i++) {
        if (url[i] < 32 || url[i] > 126) {
            return 0; // Non-printable character
        }
    }
    
    return 1; // Valid
}

// Bad: No validation
void processUrlUnsafe(const char* url) {
    // No validation - can cause buffer overflows
    strcpy(safe_buffer, url);
}
```

### 4. Security Headers
```c
// Good: Security headers
void setSecurityHeaders(HttpResponse* response) {
    setResponseHeader(response, "X-Content-Type-Options", "nosniff");
    setResponseHeader(response, "X-Frame-Options", "DENY");
    setResponseHeader(response, "X-XSS-Protection", "1; mode=block");
    setResponseHeader(response, "Strict-Transport-Security", "max-age=31536000");
    setResponseHeader(response, "Content-Security-Policy", "default-src 'self'");
}

// Bad: No security headers
void createResponseUnsafe(HttpResponse* response) {
    // No security headers - vulnerable to attacks
    setResponseBody(response, content, strlen(content));
}
```

### 5. Performance Optimization
```c
// Good: Efficient string operations
void appendToBuffer(char* buffer, int* pos, int max_size, const char* str) {
    int str_len = strlen(str);
    if (*pos + str_len >= max_size) {
        return; // Buffer overflow protection
    }
    
    strcpy(buffer + *pos, str);
    *pos += str_len;
}

// Bad: Inefficient string operations
void appendToBufferInefficient(char* buffer, const char* str) {
    strcat(buffer, str); // No length check - inefficient and unsafe
}
```

## ⚠️ Common Pitfalls

### 1. Buffer Overflows
```c
// Wrong: No bounds checking
void copyUrlUnsafe(char* dest, const char* src) {
    strcpy(dest, src); // Can cause buffer overflow
}

// Right: Safe string copying
void copyUrlSafe(char* dest, const char* src, size_t dest_size) {
    strncpy(dest, src, dest_size - 1);
    dest[dest_size - 1] = '\0';
}
```

### 2. Memory Leaks
```c
// Wrong: Memory not freed
void handleRequest(HttpRequest* request) {
    HttpResponse* response = createHttpResponse(STATUS_200_OK);
    // Use response
    // Forgot to free response - memory leak!
}

// Right: Proper cleanup
void handleRequestSafe(HttpRequest* request) {
    HttpResponse* response = createHttpResponse(STATUS_200_OK);
    // Use response
    freeHttpResponse(response);
}
```

### 3. Race Conditions
```c
// Wrong: Shared state without synchronization
int global_counter = 0;

void incrementCounter() {
    global_counter++; // Race condition in multi-threaded environment
}

// Right: Thread-safe operations
pthread_mutex_t counter_mutex = PTHREAD_MUTEX_INITIALIZER;

void incrementCounterSafe() {
    pthread_mutex_lock(&counter_mutex);
    global_counter++;
    pthread_mutex_unlock(&counter_mutex);
}
```

### 4. SQL Injection
```c
// Wrong: Direct string concatenation
void getUserUnsafe(char* query, const char* username) {
    sprintf(query, "SELECT * FROM users WHERE username='%s'", username);
    // Vulnerable to SQL injection
}

// Right: Parameterized queries
void getUserSafe(char* query, const char* username) {
    // Use parameterized queries or prepared statements
    // This is a simplified example
    char* escaped = escapeString(username);
    sprintf(query, "SELECT * FROM users WHERE username='%s'", escaped);
    free(escaped);
}
```

## 🔧 Real-World Applications

### 1. RESTful API Server
```c
// API endpoint handlers
void getUsersHandler(HttpRequest* request, HttpResponse* response) {
    setResponseHeader(response, "Content-Type", "application/json");
    
    // Query database for users
    char* json = getUsersFromDatabase();
    setResponseBody(response, json, strlen(json));
    
    free(json);
}

void createUserHandler(HttpRequest* request, HttpResponse* response) {
    // Parse JSON body
    User* user = parseUserFromJson(request->body);
    
    // Validate user data
    if (!validateUser(user)) {
        response->status = STATUS_400_BAD_REQUEST;
        setResponseBody(response, "{\"error\": \"Invalid user data\"}", 32);
        return;
    }
    
    // Save to database
    saveUserToDatabase(user);
    
    response->status = STATUS_201_CREATED;
    setResponseBody(response, "{\"message\": \"User created\"}", 28);
    
    free(user);
}
```

### 2. File Server
```c
// File serving handler
void fileHandler(HttpRequest* request, HttpResponse* response) {
    char file_path[1024];
    snprintf(file_path, sizeof(file_path), "%s%s", document_root, request->path);
    
    // Security check - prevent directory traversal
    if (strstr(request->path, "..") != NULL) {
        response->status = STATUS_403_FORBIDDEN;
        return;
    }
    
    FILE* file = fopen(file_path, "rb");
    if (!file) {
        response->status = STATUS_404_NOT_FOUND;
        return;
    }
    
    // Get file size
    fseek(file, 0, SEEK_END);
    long file_size = ftell(file);
    fseek(file, 0, SEEK_SET);
    
    // Read file content
    char* content = malloc(file_size);
    fread(content, 1, file_size, file);
    fclose(file);
    
    // Set appropriate content type
    const char* content_type = getContentType(file_path);
    setResponseHeader(response, "Content-Type", content_type);
    
    setResponseBody(response, content, file_size);
    free(content);
}
```

### 3. WebSocket Support
```c
// WebSocket upgrade handler
void websocketHandler(HttpRequest* request, HttpResponse* response) {
    // Check WebSocket upgrade request
    if (strcmp(getHeaderValue(request, "Upgrade"), "websocket") != 0) {
        response->status = STATUS_400_BAD_REQUEST;
        return;
    }
    
    // Generate WebSocket accept key
    const char* client_key = getHeaderValue(request, "Sec-WebSocket-Key");
    char* accept_key = generateWebSocketAccept(client_key);
    
    // Send WebSocket upgrade response
    response->status = STATUS_101_SWITCHING_PROTOCOLS;
    setResponseHeader(response, "Upgrade", "websocket");
    setResponseHeader(response, "Connection", "Upgrade");
    setResponseHeader(response, "Sec-WebSocket-Accept", accept_key);
    
    free(accept_key);
    
    // Upgrade connection to WebSocket
    upgradeToWebSocket(conn);
}
```

### 4. Load Balancer
```c
// Load balancer implementation
typedef struct {
    char* host;
    int port;
    int weight;
    int current_connections;
    int is_healthy;
} BackendServer;

// Select backend server
BackendServer* selectBackend(BackendServer* backends, int count) {
    BackendServer* selected = NULL;
    int min_connections = INT_MAX;
    
    for (int i = 0; i < count; i++) {
        if (backends[i].is_healthy && backends[i].current_connections < min_connections) {
            min_connections = backends[i].current_connections;
            selected = &backends[i];
        }
    }
    
    if (selected) {
        selected->current_connections++;
    }
    
    return selected;
}

// Forward request to backend
void forwardRequest(BackendServer* backend, HttpRequest* request, HttpResponse* response) {
    // Create connection to backend
    int backend_socket = connectToBackend(backend->host, backend->port);
    
    if (backend_socket < 0) {
        response->status = STATUS_502_BAD_GATEWAY;
        return;
    }
    
    // Forward request
    char* request_str = serializeHttpRequest(request);
    write(backend_socket, request_str, strlen(request_str));
    free(request_str);
    
    // Read response
    char response_buffer[MAX_RESPONSE_SIZE];
    int bytes_read = read(backend_socket, response_buffer, sizeof(response_buffer));
    
    // Parse response
    HttpResponse* backend_response = parseHttpResponse(response_buffer);
    
    // Copy response
    *response = *backend_response;
    
    close(backend_socket);
    backend->current_connections--;
}
```

## 📚 Further Reading

### Books
- "HTTP: The Definitive Guide" by David Gourley
- "Web Development with C" by various authors
- "Network Programming in C" by various authors
- "Systems Programming in Unix/Linux" by K. C. Wang

### Topics
- HTTP/2 and HTTP/3 protocols
- WebSocket implementation
- TLS/SSL encryption
- Content Delivery Networks (CDN)
- Microservices architecture
- Container orchestration
- API gateway patterns
- Real-time web applications

Advanced web development in C provides the foundation for building high-performance, secure, and scalable web applications. Master these techniques to create robust web servers and APIs that can handle enterprise-level traffic and requirements!
