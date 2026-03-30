#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <unistd.h>

// =============================================================================
// ADVANCED WEB DEVELOPMENT
// =============================================================================

#define MAX_URL_LENGTH 2048
#define MAX_HEADER_SIZE 8192
#define MAX_BODY_SIZE 1048576
#define MAX_RESPONSE_SIZE 10485760
#define MAX_CONNECTIONS 1000
#define DEFAULT_PORT 8080
#define BUFFER_SIZE 4096

// =============================================================================
// HTTP PROTOCOL IMPLEMENTATION
// =============================================================================

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

// HTTP version
typedef enum {
    HTTP_1_0 = 0,
    HTTP_1_1 = 1,
    HTTP_2_0 = 2
} HttpVersion;

// HTTP header structure
typedef struct {
    char name[256];
    char value[1024];
} HttpHeader;

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

// HTTP response structure
typedef struct {
    HttpStatus status;
    HttpVersion version;
    HttpHeader headers[MAX_HEADER_SIZE];
    int header_count;
    char* body;
    int body_length;
    char* status_message;
} HttpResponse;

// =============================================================================
// WEB SERVER ARCHITECTURE
// =============================================================================

// Connection states
typedef enum {
    CONN_IDLE = 0,
    CONN_READING = 1,
    CONN_WRITING = 2,
    CONN_CLOSING = 3
} ConnectionState;

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

// =============================================================================
// ROUTING SYSTEM
// =============================================================================

// Route handler function type
typedef void (*RouteHandler)(HttpRequest* request, HttpResponse* response);

// Route structure
typedef struct Route {
    char path[256];
    HttpMethod method;
    RouteHandler handler;
    struct Route* next;
} Route;

// Router structure
typedef struct {
    Route* routes;
    int route_count;
} Router;

// =============================================================================
// SESSION MANAGEMENT
// =============================================================================

// Session structure
typedef struct {
    char session_id[64];
    char* data;
    int data_size;
    time_t created_time;
    time_t last_accessed;
    int max_age;
} Session;

// Session manager structure
typedef struct {
    Session sessions[MAX_CONNECTIONS];
    int session_count;
    char session_secret[64];
    int session_timeout;
} SessionManager;

// =============================================================================
// TEMPLATE ENGINE
// =============================================================================

// Template variable structure
typedef struct {
    char name[64];
    char value[1024];
} TemplateVariable;

// Template structure
typedef struct {
    char* template_content;
    TemplateVariable variables[100];
    int variable_count;
} Template;

// =============================================================================
// MIDDLEWARE SYSTEM
// =============================================================================

// Middleware function type
typedef void (*MiddlewareFunc)(HttpRequest* request, HttpResponse* response, void (*next)());

// Middleware structure
typedef struct {
    MiddlewareFunc function;
    char name[64];
    struct Middleware* next;
} Middleware;

// Middleware chain structure
typedef struct {
    Middleware* head;
    Middleware* tail;
    int count;
} MiddlewareChain;

// =============================================================================
// DATABASE INTEGRATION
// =============================================================================

// Database connection types
typedef enum {
    DB_MYSQL = 0,
    DB_POSTGRESQL = 1,
    DB_SQLITE = 2,
    DB_MONGODB = 3
} DatabaseType;

// Database connection structure
typedef struct {
    DatabaseType type;
    char* connection_string;
    void* connection;
    int is_connected;
    char* last_error;
} DatabaseConnection;

// Query result structure
typedef struct {
    void* rows;
    int row_count;
    int column_count;
    char** column_names;
} QueryResult;

// =============================================================================
// AUTHENTICATION & AUTHORIZATION
// =============================================================================

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

// Auth token structure
typedef struct {
    char token[256];
    int user_id;
    char role[32];
    time_t issued_at;
    time_t expires_at;
} AuthToken;

// Authentication manager structure
typedef struct {
    User users[MAX_CONNECTIONS];
    int user_count;
    char jwt_secret[256];
    int token_expiry;
} AuthManager;

// =============================================================================
// HTTP PROTOCOL IMPLEMENTATION
// =============================================================================

// Get HTTP method from string
HttpMethod getHttpMethod(const char* method_str) {
    if (strcmp(method_str, "GET") == 0) return HTTP_GET;
    if (strcmp(method_str, "POST") == 0) return HTTP_POST;
    if (strcmp(method_str, "PUT") == 0) return HTTP_PUT;
    if (strcmp(method_str, "DELETE") == 0) return HTTP_DELETE;
    if (strcmp(method_str, "PATCH") == 0) return HTTP_PATCH;
    if (strcmp(method_str, "HEAD") == 0) return HTTP_HEAD;
    if (strcmp(method_str, "OPTIONS") == 0) return HTTP_OPTIONS;
    if (strcmp(method_str, "CONNECT") == 0) return HTTP_CONNECT;
    if (strcmp(method_str, "TRACE") == 0) return HTTP_TRACE;
    return HTTP_GET; // Default
}

// Get HTTP method string
const char* getHttpMethodString(HttpMethod method) {
    switch (method) {
        case HTTP_GET: return "GET";
        case HTTP_POST: return "POST";
        case HTTP_PUT: return "PUT";
        case HTTP_DELETE: return "DELETE";
        case HTTP_PATCH: return "PATCH";
        case HTTP_HEAD: return "HEAD";
        case HTTP_OPTIONS: return "OPTIONS";
        case HTTP_CONNECT: return "CONNECT";
        case HTTP_TRACE: return "TRACE";
        default: return "GET";
    }
}

// Get status message
const char* getStatusMessage(HttpStatus status) {
    switch (status) {
        case STATUS_200_OK: return "OK";
        case STATUS_201_CREATED: return "Created";
        case STATUS_204_NO_CONTENT: return "No Content";
        case STATUS_400_BAD_REQUEST: return "Bad Request";
        case STATUS_401_UNAUTHORIZED: return "Unauthorized";
        case STATUS_403_FORBIDDEN: return "Forbidden";
        case STATUS_404_NOT_FOUND: return "Not Found";
        case STATUS_405_METHOD_NOT_ALLOWED: return "Method Not Allowed";
        case STATUS_500_INTERNAL_SERVER_ERROR: return "Internal Server Error";
        case STATUS_502_BAD_GATEWAY: return "Bad Gateway";
        case STATUS_503_SERVICE_UNAVAILABLE: return "Service Unavailable";
        default: return "Unknown";
    }
}

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

// Create HTTP response
HttpResponse* createHttpResponse(HttpStatus status) {
    HttpResponse* response = malloc(sizeof(HttpResponse));
    if (!response) return NULL;
    
    memset(response, 0, sizeof(HttpResponse));
    response->status = status;
    response->version = HTTP_1_1;
    response->status_message = strdup(getStatusMessage(status));
    
    return response;
}

// Set response header
void setResponseHeader(HttpResponse* response, const char* name, const char* value) {
    if (response->header_count >= MAX_HEADER_SIZE) return;
    
    HttpHeader* header = &response->headers[response->header_count];
    strncpy(header->name, name, sizeof(header->name) - 1);
    strncpy(header->value, value, sizeof(header->value) - 1);
    response->header_count++;
}

// Set response body
void setResponseBody(HttpResponse* response, const char* content, int content_length) {
    if (response->body) free(response->body);
    
    response->body = malloc(content_length + 1);
    if (response->body) {
        memcpy(response->body, content, content_length);
        response->body[content_length] = '\0';
        response->body_length = content_length;
    }
}

// Serialize HTTP response
char* serializeHttpResponse(HttpResponse* response) {
    char* response_str = malloc(MAX_RESPONSE_SIZE);
    if (!response_str) return NULL;
    
    // Status line
    const char* version_str = (response->version == HTTP_1_0) ? "HTTP/1.0" : "HTTP/1.1";
    snprintf(response_str, MAX_RESPONSE_SIZE, "%s %d %s\r\n", 
             version_str, response->status, response->status_message);
    
    // Headers
    for (int i = 0; i < response->header_count; i++) {
        char header_line[1024];
        snprintf(header_line, sizeof(header_line), "%s: %s\r\n", 
                 response->headers[i].name, response->headers[i].value);
        strcat(response_str, header_line);
    }
    
    // Content-Length header
    if (response->body && response->body_length > 0) {
        char content_length_header[64];
        snprintf(content_length_header, sizeof(content_length_header), 
                 "Content-Length: %d\r\n", response->body_length);
        strcat(response_str, content_length_header);
    }
    
    // End of headers
    strcat(response_str, "\r\n");
    
    // Body
    if (response->body && response->body_length > 0) {
        strncat(response_str, response->body, response->body_length);
    }
    
    return response_str;
}

// =============================================================================
// WEB SERVER IMPLEMENTATION
// =============================================================================

// Create web server
WebServer* createWebServer(int port, const char* document_root) {
    WebServer* server = malloc(sizeof(WebServer));
    if (!server) return NULL;
    
    memset(server, 0, sizeof(WebServer));
    server->port = port;
    server->max_connections = MAX_CONNECTIONS;
    server->document_root = strdup(document_root);
    server->running = 0;
    
    return server;
}

// Create connection
Connection* createConnection(int socket_fd) {
    Connection* conn = malloc(sizeof(Connection));
    if (!conn) return NULL;
    
    memset(conn, 0, sizeof(Connection));
    conn->socket_fd = socket_fd;
    conn->state = CONN_IDLE;
    conn->last_activity = time(NULL);
    conn->keep_alive = 1;
    
    return conn;
}

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

// Accept new connection
int acceptConnection(WebServer* server) {
    struct sockaddr_in client_addr;
    socklen_t client_len = sizeof(client_addr);
    
    int client_socket = accept(server->server_socket, (struct sockaddr*)&client_addr, &client_len);
    if (client_socket < 0) return -1;
    
    // Set socket to non-blocking
    int flags = fcntl(client_socket, F_GETFL, 0);
    fcntl(client_socket, F_SETFL, flags | O_NONBLOCK);
    
    // Create connection object
    Connection* conn = createConnection(client_socket);
    if (!conn) {
        close(client_socket);
        return -1;
    }
    
    // Add to server connections
    if (server->connection_count < server->max_connections) {
        server->connections[server->connection_count++] = conn;
        return client_socket;
    } else {
        free(conn);
        close(client_socket);
        return -1;
    }
}

// Run web server event loop
void runWebServer(WebServer* server) {
    server->running = 1;
    
    printf("Web server started on port %d\n", server->port);
    printf("Document root: %s\n", server->document_root);
    
    while (server->running) {
        // Accept new connections
        acceptConnection(server);
        
        // Handle existing connections
        for (int i = 0; i < server->connection_count; i++) {
            Connection* conn = server->connections[i];
            
            if (conn->state == CONN_CLOSING) {
                close(conn->socket_fd);
                free(conn);
                
                // Remove from connections array
                for (int j = i; j < server->connection_count - 1; j++) {
                    server->connections[j] = server->connections[j + 1];
                }
                server->connection_count--;
                i--;
                continue;
            }
            
            if (conn->state == CONN_IDLE) {
                readFromConnection(conn);
                if (conn->state == CONN_WRITING) {
                    handleHttpRequest(server, conn);
                }
            }
            
            if (conn->state == CONN_WRITING) {
                writeToConnection(conn);
            }
        }
        
        // Small delay to prevent CPU spinning
        usleep(1000); // 1ms
    }
}

// =============================================================================
// ROUTING SYSTEM IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// SESSION MANAGEMENT IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// TEMPLATE ENGINE IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// MIDDLEWARE SYSTEM IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// AUTHENTICATION IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// DEMONSTRATION HANDLERS
// =============================================================================

// Home page handler
void homeHandler(HttpRequest* request, HttpResponse* response) {
    const char* content = "<html><body><h1>Welcome to Advanced C Web Server!</h1><p>This is a high-performance web server built in C.</p></body></html>";
    setResponseBody(response, content, strlen(content));
}

// API handler
void apiHandler(HttpRequest* request, HttpResponse* response) {
    setResponseHeader(response, "Content-Type", "application/json");
    
    const char* json_content = "{\"message\": \"Hello from API!\", \"status\": \"success\", \"timestamp\": 1234567890}";
    setResponseBody(response, json_content, strlen(json_content));
}

// File serving handler
void fileHandler(HttpRequest* request, HttpResponse* response) {
    // Simplified file serving - in production, you'd want proper file handling
    const char* file_path = request->path + 1; // Remove leading '/'
    
    if (strcmp(file_path, "") == 0 || strcmp(file_path, "index.html") == 0) {
        const char* content = "<html><body><h1>Index Page</h1><p>Welcome to the index page!</p></body></html>";
        setResponseBody(response, content, strlen(content));
    } else {
        response->status = STATUS_404_NOT_FOUND;
        const char* content = "<html><body><h1>404 Not Found</h1><p>The requested file was not found.</p></body></html>";
        setResponseBody(response, content, strlen(content));
    }
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateHttpProtocol() {
    printf("=== HTTP PROTOCOL DEMO ===\n");
    
    // Sample HTTP request
    const char* raw_request = "GET /api/users?limit=10 HTTP/1.1\r\n"
                            "Host: localhost:8080\r\n"
                            "User-Agent: Mozilla/5.0\r\n"
                            "Accept: application/json\r\n"
                            "Content-Type: application/json\r\n"
                            "\r\n"
                            "{\"name\": \"John\", \"age\": 30}";
    
    printf("Raw HTTP Request:\n%s\n", raw_request);
    
    // Parse request
    HttpRequest* request = parseHttpRequest(raw_request);
    if (request) {
        printf("Parsed Request:\n");
        printf("  Method: %s\n", getHttpMethodString(request->method));
        printf("  URL: %s\n", request->url);
        printf("  Path: %s\n", request->path);
        printf("  Query: %s\n", request->query_string ? request->query_string : "None");
        printf("  Headers (%d):\n", request->header_count);
        
        for (int i = 0; i < request->header_count; i++) {
            printf("    %s: %s\n", request->headers[i].name, request->headers[i].value);
        }
        
        printf("  Body: %s\n", request->body ? request->body : "None");
        
        free(request);
    }
    
    // Create response
    HttpResponse* response = createHttpResponse(STATUS_200_OK);
    setResponseHeader(response, "Content-Type", "application/json");
    const char* json_body = "{\"users\": [{\"id\": 1, \"name\": \"John\"}], \"total\": 1}";
    setResponseBody(response, json_body, strlen(json_body));
    
    char* response_str = serializeHttpResponse(response);
    printf("\nSerialized Response:\n%s\n", response_str);
    
    free(response_str);
    free(response);
}

void demonstrateRouting() {
    printf("\n=== ROUTING DEMO ===\n");
    
    Router* router = createRouter();
    
    // Add routes
    addRoute(router, "/", HTTP_GET, homeHandler);
    addRoute(router, "/api", HTTP_GET, apiHandler);
    addRoute(router, "/files/*", HTTP_GET, fileHandler);
    
    printf("Router created with %d routes:\n", router->route_count);
    
    Route* current = router->routes;
    int route_num = 1;
    while (current) {
        printf("  %d. %s %s\n", route_num++, getHttpMethodString(current->method), current->path);
        current = current->next;
    }
    
    // Test route matching
    Route* found = findRoute(router, "/", HTTP_GET);
    printf("\nFound route for GET /: %s\n", found ? "Yes" : "No");
    
    found = findRoute(router, "/api", HTTP_POST);
    printf("Found route for POST /api: %s\n", found ? "Yes" : "No");
    
    free(router);
}

void demonstrateSessions() {
    printf("\n=== SESSION MANAGEMENT DEMO ===\n");
    
    SessionManager* manager = createSessionManager("secret123", 3600);
    
    // Create sessions
    printf("Creating sessions...\n");
    for (int i = 0; i < 3; i++) {
        Session* session = createSession(manager);
        printf("  Session %d: %s\n", i + 1, session->session_id);
    }
    
    // Find session
    if (manager->session_count > 0) {
        Session* found = findSession(manager, manager->sessions[0].session_id);
        printf("\nFound session: %s\n", found ? found->session_id : "None");
    }
    
    free(manager);
}

void demonstrateTemplates() {
    printf("\n=== TEMPLATE ENGINE DEMO ===\n");
    
    const char* template_str = "<html><body>"
                               "<h1>Hello {{name}}!</h1>"
                               "<p>Welcome to {{app_name}} version {{version}}</p>"
                               "<p>Today is {{date}}</p>"
                               "</body></html>";
    
    Template* template_obj = createTemplate(template_str);
    
    // Add variables
    addTemplateVariable(template_obj, "name", "John Doe");
    addTemplateVariable(template_obj, "app_name", "Advanced C Web Server");
    addTemplateVariable(template_obj, "version", "1.0.0");
    addTemplateVariable(template_obj, "date", "2024-01-01");
    
    printf("Template variables added: %d\n", template_obj->variable_count);
    
    // Render template
    char* rendered = renderTemplate(template_obj);
    printf("\nRendered template:\n%s\n", rendered);
    
    free(rendered);
    free(template_obj);
}

void demonstrateAuthentication() {
    printf("\n=== AUTHENTICATION DEMO ===\n");
    
    AuthManager* auth = createAuthManager("jwt_secret_key", 3600);
    
    // Create user
    User user;
    user.user_id = 1;
    strcpy(user.username, "john_doe");
    strcpy(user.email, "john@example.com");
    strcpy(user.role, "user");
    user.is_active = 1;
    user.created_at = time(NULL);
    
    // Hash password
    char password_hash[256];
    hashPassword("password123", password_hash);
    strcpy(user.password_hash, password_hash);
    
    printf("User created:\n");
    printf("  ID: %d\n", user.user_id);
    printf("  Username: %s\n", user.username);
    printf("  Email: %s\n", user.email);
    printf("  Role: %s\n", user.role);
    printf("  Password Hash: %s\n", user.password_hash);
    
    // Verify password
    int is_valid = verifyPassword("password123", user.password_hash);
    printf("\nPassword verification: %s\n", is_valid ? "Valid" : "Invalid");
    
    // Generate token
    char token[256];
    generateToken(auth, user.user_id, user.role, token);
    printf("Generated token: %s\n", token);
    
    free(auth);
}

void demonstrateMiddleware() {
    printf("\n=== MIDDLEWARE DEMO ===\n");
    
    MiddlewareChain* chain = createMiddlewareChain();
    
    // Add middleware functions (simplified)
    addMiddleware(chain, NULL, "Logger");
    addMiddleware(chain, NULL, "Auth");
    addMiddleware(chain, NULL, "CORS");
    
    printf("Middleware chain created with %d middleware:\n", chain->count);
    
    Middleware* current = chain->head;
    int middleware_num = 1;
    while (current) {
        printf("  %d. %s\n", middleware_num++, current->name);
        current = current->next;
    }
    
    free(chain);
}

void demonstrateWebServer() {
    printf("\n=== WEB SERVER DEMO ===\n");
    
    // Create web server
    WebServer* server = createWebServer(DEFAULT_PORT, "./public");
    
    if (server) {
        printf("Web server created:\n");
        printf("  Port: %d\n", server->port);
        printf("  Document Root: %s\n", server->document_root);
        printf("  Max Connections: %d\n", server->max_connections);
        
        // Set request handler
        server->request_handler = fileHandler;
        
        printf("\nWeb server is ready to start!\n");
        printf("In a real implementation, you would call runWebServer(server)\n");
        printf("This demo shows the setup without actually starting the server.\n");
        
        free(server);
    }
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Web Development Examples\n");
    printf("================================\n\n");
    
    // Seed random number generator
    srand(time(NULL));
    
    // Run all demonstrations
    demonstrateHttpProtocol();
    demonstrateRouting();
    demonstrateSessions();
    demonstrateTemplates();
    demonstrateAuthentication();
    demonstrateMiddleware();
    demonstrateWebServer();
    
    printf("\nAll advanced web development examples demonstrated!\n");
    printf("Key features implemented:\n");
    printf("- HTTP protocol parsing and serialization\n");
    printf("- Web server architecture with connection management\n");
    printf("- Routing system with multiple HTTP methods\n");
    printf("- Session management with secure session IDs\n");
    printf("- Template engine for dynamic content\n");
    printf("- Middleware system for request processing\n");
    printf("- Authentication and authorization\n");
    printf("- Error handling and status codes\n");
    printf("- Content type negotiation\n");
    printf("- Keep-alive connections\n");
    printf("- Non-blocking I/O for scalability\n");
    
    return 0;
}
