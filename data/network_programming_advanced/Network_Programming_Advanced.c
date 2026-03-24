#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <sys/select.h>
#include <sys/time.h>
#include <sys/types.h>
#include <netdb.h>
#include <fcntl.h>
#include <signal.h>
#include <errno.h>
#include <pthread.h>

// =============================================================================
// ADVANCED NETWORK PROGRAMMING
// =============================================================================

#define MAX_CLIENTS 1000
#define BUFFER_SIZE 8192
#define MAX_EVENTS 1000
#define MAX_CONNECTIONS 10000
#define DEFAULT_PORT 8080
#define BACKLOG 128

// =============================================================================
// SOCKET WRAPPER AND UTILITIES
// =============================================================================

// Socket wrapper structure
typedef struct {
    int socket_fd;
    struct sockaddr_in address;
    int is_connected;
    int is_blocking;
    int timeout_seconds;
    char remote_address[INET_ADDRSTRLEN];
} SocketWrapper;

// Network event types
typedef enum {
    EVENT_CONNECT = 0,
    EVENT_DISCONNECT = 1,
    EVENT_READ = 2,
    EVENT_WRITE = 3,
    EVENT_ERROR = 4,
    EVENT_TIMEOUT = 5
} NetworkEventType;

// Network event structure
typedef struct {
    NetworkEventType type;
    int socket_fd;
    char data[BUFFER_SIZE];
    int data_length;
    time_t timestamp;
    void* user_data;
} NetworkEvent;

// =============================================================================
// ASYNCHRONOUS I/O AND EVENT LOOP
// =============================================================================

// Event loop structure
typedef struct {
    int epoll_fd;
    struct epoll_event events[MAX_EVENTS];
    int event_count;
    int running;
    pthread_mutex_t mutex;
} EventLoop;

// Async operation structure
typedef struct {
    int operation_id;
    int socket_fd;
    int operation_type; // 0=read, 1=write, 2=connect
    char* buffer;
    int buffer_size;
    int bytes_processed;
    int completed;
    void (*callback)(int operation_id, int result, void* user_data);
    void* user_data;
} AsyncOperation;

// Async operation manager
typedef struct {
    AsyncOperation operations[MAX_CONNECTIONS];
    int operation_count;
    int next_operation_id;
    pthread_mutex_t mutex;
} AsyncManager;

// =============================================================================
// THREAD POOL
// =============================================================================

// Task structure
typedef struct {
    void (*function)(void* arg);
    void* arg;
    int task_id;
    int priority;
    int completed;
    pthread_t thread_id;
} Task;

// Thread pool structure
typedef struct {
    pthread_t threads[16];
    int thread_count;
    Task* task_queue[MAX_CONNECTIONS];
    int queue_size;
    int queue_front;
    int queue_rear;
    int shutdown;
    pthread_mutex_t mutex;
    pthread_cond_t condition;
} ThreadPool;

// =============================================================================
// BUFFER MANAGEMENT
// =============================================================================

// Dynamic buffer structure
typedef struct {
    char* data;
    int size;
    int capacity;
    int position;
    int auto_expand;
} DynamicBuffer;

// Circular buffer structure
typedef struct {
    char* data;
    int size;
    int capacity;
    int read_pos;
    int write_pos;
    int is_full;
    int is_empty;
} CircularBuffer;

// =============================================================================
// PROTOCOL IMPLEMENTATIONS
// =============================================================================

// HTTP request structure
typedef struct {
    char method[16];
    char path[256];
    char version[16];
    char headers[50][256];
    int header_count;
    char body[BUFFER_SIZE];
    int body_length;
    char query_string[256];
} HTTPRequest;

// HTTP response structure
typedef struct {
    int status_code;
    char status_message[64];
    char headers[50][256];
    int header_count;
    char body[BUFFER_SIZE];
    int body_length;
    char content_type[64];
} HTTPResponse;

// WebSocket frame structure
typedef struct {
    int opcode;
    int fin;
    int masked;
    char mask[4];
    char payload[BUFFER_SIZE];
    int payload_length;
} WebSocketFrame;

// =============================================================================
// CONNECTION MANAGEMENT
// =============================================================================

// Connection state
typedef enum {
    STATE_DISCONNECTED = 0,
    STATE_CONNECTING = 1,
    STATE_CONNECTED = 2,
    STATE_AUTHENTICATED = 3,
    STATE_READY = 4
} ConnectionState;

// Connection structure
typedef struct {
    int socket_fd;
    ConnectionState state;
    char remote_address[INET_ADDRSTRLEN];
    time_t connect_time;
    time_t last_activity;
    int bytes_sent;
    int bytes_received;
    DynamicBuffer* read_buffer;
    DynamicBuffer* write_buffer;
    CircularBuffer* send_queue;
    void* protocol_data; // Protocol-specific data
    int user_id;
    char username[64];
} Connection;

// Connection manager
typedef struct {
    Connection connections[MAX_CONNECTIONS];
    int connection_count;
    int max_connections;
    pthread_mutex_t mutex;
    time_t last_cleanup;
} ConnectionManager;

// =============================================================================
// SERVER ARCHITECTURE
// =============================================================================

// Server configuration
typedef struct {
    int port;
    int max_connections;
    int thread_pool_size;
    int buffer_size;
    int timeout_seconds;
    int enable_ssl;
    char ssl_cert_file[256];
    char ssl_key_file[256];
    char bind_address[64];
    int backlog;
} ServerConfig;

// Server statistics
typedef struct {
    int total_connections;
    int active_connections;
    int bytes_sent;
    int bytes_received;
    int requests_processed;
    int errors_count;
    time_t start_time;
    float uptime;
} ServerStats;

// Server structure
typedef struct {
    int server_socket;
    ServerConfig config;
    ServerStats stats;
    EventLoop* event_loop;
    AsyncManager* async_manager;
    ConnectionManager* connection_manager;
    ThreadPool* thread_pool;
    int running;
    pthread_mutex_t mutex;
} Server;

// =============================================================================
// CLIENT ARCHITECTURE
// =============================================================================

// Client configuration
typedef struct {
    char server_address[64];
    int server_port;
    int timeout_seconds;
    int auto_reconnect;
    int reconnect_interval;
    int max_reconnect_attempts;
    int buffer_size;
} ClientConfig;

// Client statistics
typedef struct {
    int bytes_sent;
    int bytes_received;
    int requests_sent;
    int responses_received;
    int reconnect_count;
    time_t last_activity;
    float uptime;
} ClientStats;

// Client structure
typedef struct {
    SocketWrapper socket;
    ClientConfig config;
    ClientStats stats;
    DynamicBuffer* read_buffer;
    DynamicBuffer* write_buffer;
    CircularBuffer* send_queue;
    int connected;
    int authenticated;
    pthread_t receive_thread;
    pthread_t send_thread;
    int running;
    pthread_mutex_t mutex;
} Client;

// =============================================================================
// SOCKET WRAPPER IMPLEMENTATION
// =============================================================================

// Create socket wrapper
SocketWrapper* createSocketWrapper() {
    SocketWrapper* wrapper = malloc(sizeof(SocketWrapper));
    if (!wrapper) return NULL;
    
    memset(wrapper, 0, sizeof(SocketWrapper));
    wrapper->socket_fd = -1;
    wrapper->is_blocking = 1;
    wrapper->timeout_seconds = 30;
    
    return wrapper;
}

// Set socket to non-blocking
int setNonBlocking(SocketWrapper* wrapper) {
    if (!wrapper) return -1;
    
    int flags = fcntl(wrapper->socket_fd, F_GETFL, 0);
    if (flags == -1) return -1;
    
    return fcntl(wrapper->socket_fd, F_SETFL, flags | O_NONBLOCK);
}

// Set socket timeout
int setSocketTimeout(SocketWrapper* wrapper, int seconds) {
    if (!wrapper) return -1;
    
    struct timeval timeout;
    timeout.tv_sec = seconds;
    timeout.tv_usec = 0;
    
    if (setsockopt(wrapper->socket_fd, SOL_SOCKET, SO_RCVTIMEO, &timeout, sizeof(timeout)) < 0) {
        return -1;
    }
    
    if (setsockopt(wrapper->socket_fd, SOL_SOCKET, SO_SNDTIMEO, &timeout, sizeof(timeout)) < 0) {
        return -1;
    }
    
    wrapper->timeout_seconds = seconds;
    return 0;
}

// Connect to server
int connectToServer(SocketWrapper* wrapper, const char* host, int port) {
    if (!wrapper || !host) return -1;
    
    // Create socket
    wrapper->socket_fd = socket(AF_INET, SOCK_STREAM, 0);
    if (wrapper->socket_fd < 0) {
        return -1;
    }
    
    // Set server address
    struct hostent* server = gethostbyname(host);
    if (!server) {
        close(wrapper->socket_fd);
        wrapper->socket_fd = -1;
        return -1;
    }
    
    wrapper->address.sin_family = AF_INET;
    wrapper->address.sin_port = htons(port);
    memcpy(&wrapper->address.sin_addr.s_addr, server->h_addr_list[0], server->h_length);
    
    // Connect
    int result = connect(wrapper->socket_fd, (struct sockaddr*)&wrapper->address, sizeof(wrapper->address));
    if (result < 0) {
        close(wrapper->socket_fd);
        wrapper->socket_fd = -1;
        return -1;
    }
    
    // Get remote address string
    inet_ntop(AF_INET, &wrapper->address.sin_addr, wrapper->remote_address, INET_ADDRSTRLEN);
    
    wrapper->is_connected = 1;
    return 0;
}

// Send data
int sendData(SocketWrapper* wrapper, const char* data, int length) {
    if (!wrapper || !data || length <= 0 || !wrapper->is_connected) {
        return -1;
    }
    
    int total_sent = 0;
    while (total_sent < length) {
        int sent = send(wrapper->socket_fd, data + total_sent, length - total_sent, MSG_NOSIGNAL);
        if (sent < 0) {
            if (errno == EAGAIN || errno == EWOULDBLOCK) {
                // Socket would block, try again later
                return total_sent;
            }
            return -1; // Error
        }
        total_sent += sent;
    }
    
    return total_sent;
}

// Receive data
int receiveData(SocketWrapper* wrapper, char* buffer, int buffer_size) {
    if (!wrapper || !buffer || buffer_size <= 0 || !wrapper->is_connected) {
        return -1;
    }
    
    int received = recv(wrapper->socket_fd, buffer, buffer_size, 0);
    if (received < 0) {
        if (errno == EAGAIN || errno == EWOULDBLOCK) {
            return 0; // No data available
        }
        return -1; // Error
    }
    
    if (received == 0) {
        // Connection closed
        wrapper->is_connected = 0;
        return 0;
    }
    
    return received;
}

// Close socket
void closeSocket(SocketWrapper* wrapper) {
    if (!wrapper) return;
    
    if (wrapper->socket_fd >= 0) {
        close(wrapper->socket_fd);
        wrapper->socket_fd = -1;
    }
    
    wrapper->is_connected = 0;
}

// =============================================================================
// EVENT LOOP IMPLEMENTATION
// =============================================================================

// Create event loop
EventLoop* createEventLoop() {
    EventLoop* loop = malloc(sizeof(EventLoop));
    if (!loop) return NULL;
    
    memset(loop, 0, sizeof(EventLoop));
    
    loop->epoll_fd = epoll_create1(0);
    if (loop->epoll_fd < 0) {
        free(loop);
        return NULL;
    }
    
    pthread_mutex_init(&loop->mutex, NULL);
    
    return loop;
}

// Add socket to event loop
int addToEventLoop(EventLoop* loop, int socket_fd, int events) {
    if (!loop || socket_fd < 0) return -1;
    
    struct epoll_event event;
    event.events = events;
    event.data.fd = socket_fd;
    
    if (epoll_ctl(loop->epoll_fd, EPOLL_CTL_ADD, socket_fd, &event) < 0) {
        return -1;
    }
    
    return 0;
}

// Remove socket from event loop
int removeFromEventLoop(EventLoop* loop, int socket_fd) {
    if (!loop || socket_fd < 0) return -1;
    
    if (epoll_ctl(loop->epoll_fd, EPOLL_CTL_DEL, socket_fd, NULL, 0) < 0) {
        return -1;
    }
    
    return 0;
}

// Wait for events
int waitForEvents(EventLoop* loop, int timeout_ms) {
    if (!loop) return -1;
    
    int event_count = epoll_wait(loop->epoll_fd, loop->events, MAX_EVENTS, timeout_ms);
    
    pthread_mutex_lock(&loop->mutex);
    loop->event_count = event_count;
    pthread_mutex_unlock(&loop->mutex);
    
    return event_count;
}

// Get event
struct epoll_event getEvent(EventLoop* loop, int index) {
    if (!loop || index < 0 || index >= loop->event_count) {
        struct epoll_event empty_event = {0};
        return empty_event;
    }
    
    return loop->events[index];
}

// Destroy event loop
void destroyEventLoop(EventLoop* loop) {
    if (!loop) return;
    
    if (loop->epoll_fd >= 0) {
        close(loop->epoll_fd);
    }
    
    pthread_mutex_destroy(&loop->mutex);
    free(loop);
}

// =============================================================================
// THREAD POOL IMPLEMENTATION
// =============================================================================

// Thread function
void* threadFunction(void* arg) {
    ThreadPool* pool = (ThreadPool*)arg;
    
    while (!pool->shutdown) {
        pthread_mutex_lock(&pool->mutex);
        
        // Wait for task
        while (pool->queue_size == 0 && !pool->shutdown) {
            pthread_cond_wait(&pool->condition, &pool->mutex);
        }
        
        if (pool->shutdown) {
            pthread_mutex_unlock(&pool->mutex);
            break;
        }
        
        // Get task
        Task* task = &pool->task_queue[pool->queue_front];
        pool->queue_front = (pool->queue_front + 1) % MAX_CONNECTIONS;
        pool->queue_size--;
        
        pthread_mutex_unlock(&pool->mutex);
        
        // Execute task
        if (task->function) {
            task->function(task->arg);
            task->completed = 1;
        }
    }
    
    return NULL;
}

// Create thread pool
ThreadPool* createThreadPool(int thread_count) {
    ThreadPool* pool = malloc(sizeof(ThreadPool));
    if (!pool) return NULL;
    
    memset(pool, 0, sizeof(ThreadPool));
    pool->thread_count = thread_count;
    
    pthread_mutex_init(&pool->mutex, NULL);
    pthread_cond_init(&pool->condition, NULL);
    
    // Create threads
    for (int i = 0; i < thread_count; i++) {
        if (pthread_create(&pool->threads[i], NULL, threadFunction, pool) != 0) {
            // Handle thread creation error
            pool->thread_count = i;
            break;
        }
    }
    
    return pool;
}

// Add task to thread pool
int addTask(ThreadPool* pool, void (*function)(void*), void* arg, int priority) {
    if (!pool || !function) return -1;
    
    pthread_mutex_lock(&pool->mutex);
    
    if (pool->queue_size >= MAX_CONNECTIONS) {
        pthread_mutex_unlock(&pool->mutex);
        return -1; // Queue full
    }
    
    // Add task to queue
    Task* task = &pool->task_queue[pool->queue_rear];
    task->function = function;
    task->arg = arg;
    task->task_id = pool->queue_rear;
    task->priority = priority;
    task->completed = 0;
    
    pool->queue_rear = (pool->queue_rear + 1) % MAX_CONNECTIONS;
    pool->queue_size++;
    
    pthread_cond_signal(&pool->condition);
    pthread_mutex_unlock(&pool->mutex);
    
    return task->task_id;
}

// Shutdown thread pool
void shutdownThreadPool(ThreadPool* pool) {
    if (!pool) return;
    
    pthread_mutex_lock(&pool->mutex);
    pool->shutdown = 1;
    pthread_cond_broadcast(&pool->condition);
    pthread_mutex_unlock(&pool->mutex);
    
    // Wait for threads to finish
    for (int i = 0; i < pool->thread_count; i++) {
        pthread_join(pool->threads[i], NULL);
    }
    
    pthread_mutex_destroy(&pool->mutex);
    pthread_cond_destroy(&pool->condition);
    free(pool);
}

// =============================================================================
// BUFFER MANAGEMENT IMPLEMENTATION
// =============================================================================

// Create dynamic buffer
DynamicBuffer* createDynamicBuffer(int initial_capacity, int auto_expand) {
    DynamicBuffer* buffer = malloc(sizeof(DynamicBuffer));
    if (!buffer) return NULL;
    
    buffer->data = malloc(initial_capacity);
    if (!buffer->data) {
        free(buffer);
        return NULL;
    }
    
    buffer->size = 0;
    buffer->capacity = initial_capacity;
    buffer->position = 0;
    buffer->auto_expand = auto_expand;
    
    return buffer;
}

// Write to dynamic buffer
int writeToDynamicBuffer(DynamicBuffer* buffer, const char* data, int length) {
    if (!buffer || !data || length <= 0) return -1;
    
    // Check if buffer needs to expand
    if (buffer->position + length > buffer->capacity) {
        if (!buffer->auto_expand) {
            return -1; // Buffer full and auto-expand disabled
        }
        
        int new_capacity = buffer->capacity * 2;
        char* new_data = realloc(buffer->data, new_capacity);
        if (!new_data) {
            return -1; // Allocation failed
        }
        
        buffer->data = new_data;
        buffer->capacity = new_capacity;
    }
    
    // Copy data
    memcpy(buffer->data + buffer->position, data, length);
    buffer->position += length;
    buffer->size = buffer->position;
    
    return length;
}

// Read from dynamic buffer
int readFromDynamicBuffer(DynamicBuffer* buffer, char* data, int length) {
    if (!buffer || !data || length <= 0) return -1;
    
    if (buffer->position - length < 0) {
        return -1; // Not enough data
    }
    
    // Copy data
    memcpy(data, buffer->data + buffer->position - length, length);
    buffer->position -= length;
    
    return length;
}

// Create circular buffer
CircularBuffer* createCircularBuffer(int capacity) {
    CircularBuffer* buffer = malloc(sizeof(CircularBuffer));
    if (!buffer) return NULL;
    
    buffer->data = malloc(capacity);
    if (!buffer->data) {
        free(buffer);
        return NULL;
    }
    
    buffer->size = 0;
    buffer->capacity = capacity;
    buffer->read_pos = 0;
    buffer->write_pos = 0;
    buffer->is_full = 0;
    buffer->is_empty = 1;
    
    return buffer;
}

// Write to circular buffer
int writeToCircularBuffer(CircularBuffer* buffer, const char* data, int length) {
    if (!buffer || !data || length <= 0) return -1;
    
    if (buffer->is_full) {
        return -1; // Buffer full
    }
    
    // Check if write would wrap around
    int available_space = buffer->capacity - buffer->size;
    if (length > available_space) {
        return -1; // Not enough space
    }
    
    // Copy data
    for (int i = 0; i < length; i++) {
        buffer->data[buffer->write_pos] = data[i];
        buffer->write_pos = (buffer->write_pos + 1) % buffer->capacity;
    }
    
    buffer->size += length;
    buffer->is_empty = 0;
    buffer->is_full = (buffer->size == buffer->capacity);
    
    return length;
}

// Read from circular buffer
int readFromCircularBuffer(CircularBuffer* buffer, char* data, int length) {
    if (!buffer || !data || length <= 0) return -1;
    
    if (buffer->is_empty) {
        return -1; // Buffer empty
    }
    
    if (length > buffer->size) {
        length = buffer->size; // Return available data
    }
    
    // Copy data
    for (int i = 0; i < length; i++) {
        data[i] = buffer->data[buffer->read_pos];
        buffer->read_pos = (buffer->read_pos + 1) % buffer->capacity;
    }
    
    buffer->size -= length;
    buffer->is_empty = (buffer->size == 0);
    buffer->is_full = 0;
    
    return length;
}

// =============================================================================
// HTTP PROTOCOL IMPLEMENTATION
// =============================================================================

// Parse HTTP request
int parseHTTPRequest(const char* request_data, int request_length, HTTPRequest* request) {
    if (!request_data || !request || request_length <= 0) {
        return -1;
    }
    
    memset(request, 0, sizeof(HTTPRequest));
    
    // Parse request line
    char request_line[BUFFER_SIZE];
    char* line_end = strstr(request_data, "\r\n");
    if (!line_end) {
        return -1;
    }
    
    int line_length = line_end - request_data;
    strncpy(request_line, request_data, line_length);
    request_line[line_length] = '\0';
    
    // Parse method, path, version
    sscanf(request_line, "%15s %255s %15s", request->method, request->path, request->version);
    
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
        if (line_length > 255) {
            line_length = 255;
        }
        
        strncpy(request->headers[request->header_count], current_pos, line_length);
        request->headers[request->header_count][line_length] = '\0';
        request->header_count++;
        
        current_pos = line_end + 2;
        
        if (request->header_count >= 50) {
            break;
        }
    }
    
    // Parse body if present
    if (current_pos < request_data + request_length) {
        current_pos += 2; // Skip \r\n after headers
        int body_length = request_data + request_length - current_pos;
        if (body_length > BUFFER_SIZE - 1) {
            body_length = BUFFER_SIZE - 1;
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
                      "HTTP/1.1 %d %s\r\n", response->status_code, response->status_message);
    
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

// Create simple HTTP response
HTTPResponse* createHTTPResponse(int status_code, const char* status_message, const char* content_type, const char* body) {
    HTTPResponse* response = malloc(sizeof(HTTPResponse));
    if (!response) return NULL;
    
    memset(response, 0, sizeof(HTTPResponse));
    
    response->status_code = status_code;
    strncpy(response->status_message, status_message, sizeof(response->status_message) - 1);
    
    if (content_type) {
        strncpy(response->content_type, content_type, sizeof(response->content_type) - 1);
    }
    
    if (body) {
        strncpy(response->body, body, sizeof(response->body) - 1);
        response->body_length = strlen(body);
    }
    
    return response;
}

// =============================================================================
// WEBSOCKET PROTOCOL IMPLEMENTATION
// =============================================================================

// Parse WebSocket frame
int parseWebSocketFrame(const char* frame_data, int frame_length, WebSocketFrame* frame) {
    if (!frame_data || !frame || frame_length < 2) {
        return -1;
    }
    
    memset(frame, 0, sizeof(WebSocketFrame));
    
    // Parse first byte
    frame->fin = (frame_data[0] & 0x80) ? 1 : 0;
    frame->opcode = frame_data[0] & 0x0F;
    
    // Parse payload length
    int payload_length = frame_data[1] & 0x7F;
    int mask_offset = 2;
    
    if (payload_length == 126) {
        payload_length = (frame_data[2] << 8) | frame_data[3];
        mask_offset = 4;
    } else if (payload_length == 127) {
        payload_length = (frame_data[2] << 24) | (frame_data[3] << 16) | (frame_data[4] << 8) | frame_data[5];
        mask_offset = 8;
    }
    
    frame->payload_length = payload_length;
    
    // Check if masked
    frame->masked = (frame_data[mask_offset] & 0x80) ? 1 : 0;
    
    if (frame->masked) {
        memcpy(frame->mask, frame_data + mask_offset + 1, 4);
        mask_offset += 5;
    }
    
    // Extract payload
    if (payload_length > 0 && payload_length <= BUFFER_SIZE) {
        memcpy(frame->payload, frame_data + mask_offset + 1, payload_length);
        
        // Unmask payload if needed
        if (frame->masked) {
            for (int i = 0; i < payload_length; i++) {
                frame->payload[i] ^= frame->mask[i % 4];
            }
        }
    }
    
    return 0;
}

// Build WebSocket frame
int buildWebSocketFrame(WebSocketFrame* frame, char* frame_buffer, int buffer_size) {
    if (!frame || !frame_buffer || buffer_size == 0) {
        return -1;
    }
    
    int offset = 0;
    
    // First byte
    frame_buffer[offset++] = (frame->fin ? 0x80 : 0x00) | (frame->opcode & 0x0F);
    
    // Payload length
    if (frame->payload_length < 126) {
        frame_buffer[offset++] = frame->payload_length;
    } else if (frame->payload_length < 65536) {
        frame_buffer[offset++] = 126;
        frame_buffer[offset++] = (frame->payload_length >> 8) & 0xFF;
        frame_buffer[offset++] = frame->payload_length & 0xFF;
    } else {
        frame_buffer[offset++] = 127;
        frame_buffer[offset++] = (frame->payload_length >> 24) & 0xFF;
        frame_buffer[offset++] = (frame->payload_length >> 16) & 0xFF;
        frame_buffer[offset++] = (frame->payload_length >> 8) & 0xFF;
        frame_buffer[offset++] = frame->payload_length & 0xFF;
    }
    
    // Mask bit (not masked for server to client)
    frame_buffer[offset++] = 0x00;
    
    // Payload
    if (frame->payload_length > 0 && frame->payload_length <= buffer_size - offset) {
        memcpy(frame_buffer + offset, frame->payload, frame->payload_length);
        offset += frame->payload_length;
    }
    
    return offset;
}

// =============================================================================
// CONNECTION MANAGEMENT IMPLEMENTATION
// =============================================================================

// Create connection manager
ConnectionManager* createConnectionManager(int max_connections) {
    ConnectionManager* manager = malloc(sizeof(ConnectionManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(ConnectionManager));
    manager->max_connections = max_connections;
    manager->last_cleanup = time(NULL);
    
    pthread_mutex_init(&manager->mutex, NULL);
    
    return manager;
}

// Add connection
int addConnection(ConnectionManager* manager, int socket_fd, struct sockaddr_in* address) {
    if (!manager || socket_fd < 0 || !address) {
        return -1;
    }
    
    pthread_mutex_lock(&manager->mutex);
    
    if (manager->connection_count >= manager->max_connections) {
        pthread_mutex_unlock(&manager->mutex);
        return -1; // Connection limit reached
    }
    
    // Find empty slot
    Connection* connection = NULL;
    for (int i = 0; i < manager->max_connections; i++) {
        if (manager->connections[i].socket_fd == -1) {
            connection = &manager->connections[i];
            break;
        }
    }
    
    if (!connection) {
        pthread_mutex_unlock(&manager->mutex);
        return -1; // No available slots
    }
    
    // Initialize connection
    connection->socket_fd = socket_fd;
    connection->state = STATE_CONNECTED;
    connection->connect_time = time(NULL);
    connection->last_activity = time(NULL);
    connection->bytes_sent = 0;
    connection->bytes_received = 0;
    
    // Copy address
    inet_ntop(AF_INET, &address->sin_addr, connection->remote_address, INET_ADDRSTRLEN);
    
    // Create buffers
    connection->read_buffer = createDynamicBuffer(BUFFER_SIZE, 1);
    connection->write_buffer = createDynamicBuffer(BUFFER_SIZE, 1);
    connection->send_queue = createCircularBuffer(BUFFER_SIZE);
    
    manager->connection_count++;
    
    pthread_mutex_unlock(&manager->mutex);
    return connection - manager->connections;
}

// Remove connection
int removeConnection(ConnectionManager* manager, int socket_fd) {
    if (!manager || socket_fd < 0) {
        return -1;
    }
    
    pthread_mutex_lock(&manager->mutex);
    
    // Find connection
    Connection* connection = NULL;
    for (int i = 0; i < manager->max_connections; i++) {
        if (manager->connections[i].socket_fd == socket_fd) {
            connection = &manager->connections[i];
            break;
        }
    }
    
    if (!connection) {
        pthread_mutex_unlock(&manager->mutex);
        return -1; // Connection not found
    }
    
    // Clean up connection
    if (connection->read_buffer) {
        free(connection->read_buffer->data);
        free(connection->read_buffer);
    }
    
    if (connection->write_buffer) {
        free(connection->write_buffer->data);
        free(connection->write_buffer);
    }
    
    if (connection->send_queue) {
        free(connection->send_queue->data);
        free(connection->send_queue);
    }
    
    // Reset connection
    connection->socket_fd = -1;
    connection->state = STATE_DISCONNECTED;
    connection->protocol_data = NULL;
    
    manager->connection_count--;
    
    pthread_mutex_unlock(&manager->mutex);
    return 0;
}

// Find connection by socket
Connection* findConnection(ConnectionManager* manager, int socket_fd) {
    if (!manager || socket_fd < 0) {
        return NULL;
    }
    
    for (int i = 0; i < manager->max_connections; i++) {
        if (manager->connections[i].socket_fd == socket_fd) {
            return &manager->connections[i];
        }
    }
    
    return NULL;
}

// Cleanup inactive connections
void cleanupInactiveConnections(ConnectionManager* manager, int timeout_seconds) {
    if (!manager) return;
    
    time_t current_time = time(NULL);
    
    pthread_mutex_lock(&manager->mutex);
    
    for (int i = 0; i < manager->max_connections; i++) {
        Connection* connection = &manager->connections[i];
        
        if (connection->socket_fd >= 0 && connection->state == STATE_CONNECTED) {
            if (current_time - connection->last_activity > timeout_seconds) {
                // Close socket
                close(connection->socket_fd);
                connection->socket_fd = -1;
                connection->state = STATE_DISCONNECTED;
            }
        }
    }
    
    manager->last_cleanup = current_time;
    
    pthread_mutex_unlock(&manager->mutex);
}

// =============================================================================
// SERVER IMPLEMENTATION
// =============================================================================

// Create server
Server* createServer(ServerConfig* config) {
    Server* server = malloc(sizeof(Server));
    if (!server) return NULL;
    
    memset(server, 0, sizeof(Server));
    
    if (config) {
        server->config = *config;
    } else {
        server->config.port = DEFAULT_PORT;
        server->config.max_connections = MAX_CLIENTS;
        server->config.thread_pool_size = 4;
        server->config.buffer_size = BUFFER_SIZE;
        server->config.timeout_seconds = 30;
        server->config.backlog = BACKLOG;
    }
    
    // Initialize subsystems
    server->event_loop = createEventLoop();
    server->async_manager = malloc(sizeof(AsyncManager));
    server->connection_manager = createConnectionManager(server->config.max_connections);
    server->thread_pool = createThreadPool(server->config.thread_pool_size);
    
    if (!server->event_loop || !server->async_manager || !server->connection_manager || !server->thread_pool) {
        free(server);
        return NULL;
    }
    
    memset(server->async_manager, 0, sizeof(AsyncManager));
    pthread_mutex_init(&server->async_manager->mutex, NULL);
    
    server->stats.start_time = time(NULL);
    
    pthread_mutex_init(&server->mutex, NULL);
    
    return server;
}

// Start server
int startServer(Server* server) {
    if (!server) return -1;
    
    // Create server socket
    server->server_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (server->server_socket < 0) {
        return -1;
    }
    
    // Set socket options
    int opt = 1;
    setsockopt(server->server_socket, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));
    
    // Bind socket
    struct sockaddr_in server_addr;
    server_addr.sin_family = AF_INET;
    server_addr.sin_addr.s_addr = INADDR_ANY;
    server_addr.sin_port = htons(server->config.port);
    
    if (bind(server->server_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        close(server->server_socket);
        return -1;
    }
    
    // Start listening
    if (listen(server->server_socket, server->config.backlog) < 0) {
        close(server->server_socket);
        return -1;
    }
    
    // Set server socket to non-blocking
    int flags = fcntl(server->server_socket, F_GETFL, 0);
    fcntl(server->server_socket, F_SETFL, flags | O_NONBLOCK);
    
    // Add server socket to event loop
    addToEventLoop(server->event_loop, server->server_socket, EPOLLIN);
    
    server->running = 1;
    
    printf("Server started on port %d\n", server->config.port);
    
    return 0;
}

// Handle new connection
void handleNewConnection(Server* server) {
    struct sockaddr_in client_addr;
    socklen_t client_len = sizeof(client_addr);
    
    int client_socket = accept(server->server_socket, (struct sockaddr*)&client_addr, &client_len);
    if (client_socket < 0) {
        if (errno != EAGAIN && errno != EWOULDBLOCK) {
            printf("Accept error: %s\n", strerror(errno));
        }
        return;
    }
    
    // Set client socket to non-blocking
    int flags = fcntl(client_socket, F_GETFL, 0);
    fcntl(client_socket, F_SETFL, flags | O_NONBLOCK);
    
    // Add connection to manager
    int connection_id = addConnection(server->connection_manager, client_socket, &client_addr);
    if (connection_id >= 0) {
        Connection* connection = &server->connection_manager->connections[connection_id];
        
        printf("New connection from %s (fd: %d)\n", connection->remote_address, client_socket);
        
        // Add to event loop
        addToEventLoop(server->event_loop, client_socket, EPOLLIN | EPOLLOUT);
        
        // Update statistics
        server->stats.total_connections++;
        server->stats.active_connections++;
    } else {
        printf("Connection limit reached, rejecting client\n");
        close(client_socket);
    }
}

// Handle client data
void handleClientData(Server* server, int socket_fd) {
    Connection* connection = findConnection(server->connection_manager, socket_fd);
    if (!connection) return;
    
    char buffer[BUFFER_SIZE];
    int bytes_received = receiveData((SocketWrapper*)&connection->socket_fd, buffer, sizeof(buffer));
    
    if (bytes_received < 0) {
        // Error or connection closed
        printf("Client %s disconnected\n", connection->remote_address);
        removeFromEventLoop(server->event_loop, socket_fd);
        removeConnection(server->connection_manager, socket_fd);
        server->stats.active_connections--;
        return;
    }
    
    if (bytes_received == 0) {
        // Connection closed
        printf("Client %s disconnected\n", connection->remote_address);
        removeFromEventLoop(server->event_loop, socket_fd);
        removeConnection(server->connection_manager, socket_fd);
        server->stats.active_connections--;
        return;
    }
    
    // Add data to read buffer
    writeToDynamicBuffer(connection->read_buffer, buffer, bytes_received);
    
    // Update statistics
    server->stats.bytes_received += bytes_received;
    connection->last_activity = time(NULL);
    
    // Process data (HTTP request)
    HTTPRequest request;
    if (parseHTTPRequest(connection->read_buffer->data, connection->read_buffer->size, &request) == 0) {
        printf("Received HTTP request: %s %s\n", request.method, request.path);
        
        // Create response
        HTTPResponse* response = createHTTPResponse(200, "OK", "text/html", 
            "<html><body><h1>Hello from C Server!</h1></body></html>");
        
        char response_buffer[BUFFER_SIZE];
        int response_length = buildHTTPResponse(response, response_buffer, sizeof(response_buffer));
        
        // Send response
        int sent = sendData((SocketWrapper*)&connection->socket_fd, response_buffer, response_length);
        if (sent > 0) {
            server->stats.bytes_sent += sent;
            server->stats.requests_processed++;
        }
        
        free(response);
    }
    
    // Clear read buffer
    connection->read_buffer->size = 0;
    connection->read_buffer->position = 0;
}

// Run server event loop
void runServerEventLoop(Server* server) {
    if (!server || !server->running) return;
    
    while (server->running) {
        // Wait for events
        int event_count = waitForEvents(server->event_loop, 1000); // 1 second timeout
        
        // Process events
        for (int i = 0; i < event_count; i++) {
            struct epoll_event event = getEvent(server->event_loop, i);
            
            if (event.data.fd == server->server_socket) {
                // New connection
                if (event.events & EPOLLIN) {
                    handleNewConnection(server);
                }
            } else {
                // Client event
                if (event.events & EPOLLIN) {
                    handleClientData(server, event.data.fd);
                }
                
                if (event.events & EPOLLERR || event.events & EPOLLHUP) {
                    // Error or hangup
                    Connection* connection = findConnection(server->connection_manager, event.data.fd);
                    if (connection) {
                        printf("Client %s error/hangup\n", connection->remote_address);
                        removeFromEventLoop(server->event_loop, event.data.fd);
                        removeConnection(server->connection_manager, event.data.fd);
                        server->stats.active_connections--;
                    }
                }
            }
        }
        
        // Update uptime
        server->stats.uptime = difftime(time(NULL), server->stats.start_time);
        
        // Cleanup inactive connections
        cleanupInactiveConnections(server->connection_manager, server->config.timeout_seconds);
    }
}

// Stop server
void stopServer(Server* server) {
    if (!server) return;
    
    server->running = 0;
    
    // Close server socket
    if (server->server_socket >= 0) {
        close(server->server_socket);
        server->server_socket = -1;
    }
    
    printf("Server stopped\n");
    printf("Total connections: %d\n", server->stats.total_connections);
    printf("Requests processed: %d\n", server->stats.requests_processed);
    printf("Bytes sent: %d\n", server->stats.bytes_sent);
    printf("Bytes received: %d\n", server->stats.bytes_received);
    printf("Uptime: %.2f seconds\n", server->stats.uptime);
}

// =============================================================================
// CLIENT IMPLEMENTATION
// =============================================================================

// Create client
Client* createClient(ClientConfig* config) {
    Client* client = malloc(sizeof(Client));
    if (!client) return NULL;
    
    memset(client, 0, sizeof(Client));
    
    if (config) {
        client->config = *config;
    } else {
        strcpy(client->config.server_address, "127.0.0.1");
        client->config.server_port = DEFAULT_PORT;
        client->config.timeout_seconds = 30;
        client->config.auto_reconnect = 1;
        client->config.reconnect_interval = 5;
        client->config.max_reconnect_attempts = 3;
        client->config.buffer_size = BUFFER_SIZE;
    }
    
    // Create socket wrapper
    client->socket = *createSocketWrapper();
    
    // Create buffers
    client->read_buffer = createDynamicBuffer(client->config.buffer_size, 1);
    client->write_buffer = createDynamicBuffer(client->config.buffer_size, 1);
    client->send_queue = createCircularBuffer(client->config.buffer_size);
    
    pthread_mutex_init(&client->mutex, NULL);
    
    return client;
}

// Connect to server
int connectToServer(Client* client) {
    if (!client) return -1;
    
    int result = connectToServer(&client->socket, client->config.server_address, client->config.server_port);
    if (result < 0) {
        printf("Failed to connect to server: %s:%d\n", client->config.server_address, client->config.server_port);
        return -1;
    }
    
    client->connected = 1;
    client->stats.uptime = time(NULL);
    
    printf("Connected to server %s:%d\n", client->config.server_address, client->config.server_port);
    
    return 0;
}

// Send data to server
int sendToServer(Client* client, const char* data, int length) {
    if (!client || !client->connected || !data || length <= 0) {
        return -1;
    }
    
    pthread_mutex_lock(&client->mutex);
    
    int result = sendData(&client->socket, data, length);
    
    if (result > 0) {
        client->stats.bytes_sent += result;
        client->stats.last_activity = time(NULL);
    }
    
    pthread_mutex_unlock(&client->mutex);
    
    return result;
}

// Receive data from server
int receiveFromServer(Client* client, char* buffer, int buffer_size) {
    if (!client || !client->connected || !buffer || buffer_size <= 0) {
        return -1;
    }
    
    pthread_mutex_lock(&client->mutex);
    
    int result = receiveData(&client->socket, buffer, buffer_size);
    
    if (result > 0) {
        client->stats.bytes_received += result;
        client->stats.last_activity = time(NULL);
    } else if (result == 0) {
        // Connection closed
        client->connected = 0;
        printf("Server disconnected\n");
    }
    
    pthread_mutex_unlock(&client->mutex);
    
    return result;
}

// Disconnect from server
void disconnectFromServer(Client* client) {
    if (!client) return;
    
    pthread_mutex_lock(&client->mutex);
    
    if (client->connected) {
        closeSocket(&client->socket);
        client->connected = 0;
        printf("Disconnected from server\n");
    }
    
    pthread_mutex_unlock(&client->mutex);
}

// Auto-reconnect
int autoReconnect(Client* client) {
    if (!client || !client->config.auto_reconnect) {
        return -1;
    }
    
    if (client->connected) {
        return 0; // Already connected
    }
    
    static int reconnect_attempts = 0;
    
    if (reconnect_attempts < client->config.max_reconnect_attempts) {
        printf("Attempting to reconnect... (attempt %d/%d)\n", 
               reconnect_attempts + 1, client->config.max_reconnect_attempts);
        
        if (connectToServer(client) == 0) {
            reconnect_attempts = 0;
            return 0;
        }
        
        reconnect_attempts++;
        
        // Wait before next attempt
        sleep(client->config.reconnect_interval);
    } else {
        printf("Max reconnect attempts reached\n");
        return -1;
    }
    
    return -1;
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateSocketWrapper() {
    printf("=== SOCKET WRAPPER DEMO ===\n");
    
    // Create socket wrapper
    SocketWrapper* wrapper = createSocketWrapper();
    if (!wrapper) {
        printf("Failed to create socket wrapper\n");
        return;
    }
    
    printf("Socket wrapper created\n");
    
    // Set non-blocking
    if (setNonBlocking(wrapper) == 0) {
        printf("Socket set to non-blocking\n");
    }
    
    // Set timeout
    if (setSocketTimeout(wrapper, 10) == 0) {
        printf("Socket timeout set to %d seconds\n", wrapper->timeout_seconds);
    }
    
    // Connect to a test server (this will likely fail)
    printf("Attempting to connect to test server...\n");
    int result = connectToServer(wrapper, "127.0.0.1", 8080);
    if (result == 0) {
        printf("Connected to test server\n");
        
        // Send test data
        const char* test_data = "Hello, Server!";
        int sent = sendData(wrapper, test_data, strlen(test_data));
        printf("Sent %d bytes: %s\n", sent, test_data);
        
        // Receive data
        char buffer[1024];
        int received = receiveData(wrapper, buffer, sizeof(buffer));
        printf("Received %d bytes\n", received);
        
        if (received > 0) {
            buffer[received] = '\0';
            printf("Received data: %s\n", buffer);
        }
        
        closeSocket(wrapper);
    } else {
        printf("Failed to connect to test server (expected)\n");
    }
    
    free(wrapper);
}

void demonstrateEventLoop() {
    printf("\n=== EVENT LOOP DEMO ===\n");
    
    // Create event loop
    EventLoop* loop = createEventLoop();
    if (!loop) {
        printf("Failed to create event loop\n");
        return;
    }
    
    printf("Event loop created with epoll fd: %d\n", loop->epoll_fd);
    
    // Create a test socket
    int test_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (test_socket >= 0) {
        // Set non-blocking
        int flags = fcntl(test_socket, F_GETFL, 0);
        fcntl(test_socket, F_SETFL, flags | O_NONBLOCK);
        
        // Add to event loop
        if (addToEventLoop(loop, test_socket, EPOLLIN) == 0) {
            printf("Test socket added to event loop\n");
            
            // Wait for events (timeout after 1 second)
            printf("Waiting for events...\n");
            int event_count = waitForEvents(loop, 1000);
            
            printf("Received %d events\n", event_count);
            
            // Process events
            for (int i = 0; i < event_count; i++) {
                struct epoll_event event = getEvent(loop, i);
                printf("Event %d: fd=%d, events=0x%x\n", i, event.data.fd, event.events);
            }
        }
        
        close(test_socket);
    }
    
    destroyEventLoop(loop);
    printf("Event loop destroyed\n");
}

void demonstrateThreadPool() {
    printf("\n=== THREAD POOL DEMO ===\n");
    
    // Create thread pool
    ThreadPool* pool = createThreadPool(4);
    if (!pool) {
        printf("Failed to create thread pool\n");
        return;
    }
    
    printf("Thread pool created with %d threads\n", pool->thread_count);
    
    // Add tasks
    for (int i = 0; i < 10; i++) {
        char task_data[64];
        snprintf(task_data, sizeof(task_data), "Task %d", i);
        
        int task_id = addTask(pool, (void(*)(void*))printf, strdup(task_data), 0);
        printf("Added task %d (id: %d)\n", i, task_id);
    }
    
    // Wait for tasks to complete
    printf("Waiting for tasks to complete...\n");
    sleep(2);
    
    // Shutdown thread pool
    shutdownThreadPool(pool);
    printf("Thread pool shut down\n");
}

void demonstrateBuffers() {
    printf("\n=== BUFFER MANAGEMENT DEMO ===\n");
    
    // Dynamic buffer
    printf("Dynamic Buffer:\n");
    DynamicBuffer* dynamic = createDynamicBuffer(256, 1);
    if (dynamic) {
        printf("Created dynamic buffer (capacity: %d)\n", dynamic->capacity);
        
        const char* test_data = "Hello, Dynamic Buffer!";
        int written = writeToDynamicBuffer(dynamic, test_data, strlen(test_data));
        printf("Written %d bytes: %s\n", written, test_data);
        
        printf("Buffer size: %d, position: %d\n", dynamic->size, dynamic->position);
        
        char read_data[256];
        int read = readFromDynamicBuffer(dynamic, read_data, strlen(test_data));
        read_data[read] = '\0';
        printf("Read %d bytes: %s\n", read, read_data);
        
        printf("Buffer size: %d, position: %d\n", dynamic->size, dynamic->position);
        
        free(dynamic->data);
        free(dynamic);
    }
    
    // Circular buffer
    printf("\nCircular Buffer:\n");
    CircularBuffer* circular = createCircularBuffer(256);
    if (circular) {
        printf("Created circular buffer (capacity: %d)\n", circular->capacity);
        
        const char* test_data = "Hello, Circular Buffer!";
        int written = writeToCircularBuffer(circular, test_data, strlen(test_data));
        printf("Written %d bytes: %s\n", written, test_data);
        
        printf("Buffer size: %d, read_pos: %d, write_pos: %d\n", 
               circular->size, circular->read_pos, circular->write_pos);
        
        char read_data[256];
        int read = readFromCircularBuffer(circular, read_data, strlen(test_data));
        read_data[read] = '\0';
        printf("Read %d bytes: %s\n", read, read_data);
        
        printf("Buffer size: %d, read_pos: %d, write_pos: %d\n", 
               circular->size, circular->read_pos, circular->write_pos);
        
        free(circular->data);
        free(circular);
    }
}

void demonstrateHTTPProtocol() {
    printf("\n=== HTTP PROTOCOL DEMO ===\n");
    
    // Sample HTTP request
    const char* sample_request = 
        "GET /index.html HTTP/1.1\r\n"
        "Host: localhost:8080\r\n"
        "User-Agent: C-Client/1.0\r\n"
        "Accept: text/html\r\n"
        "\r\n"
        "Request body here";
    
    printf("Sample HTTP Request:\n%s\n", sample_request);
    
    // Parse request
    HTTPRequest request;
    if (parseHTTPRequest(sample_request, strlen(sample_request), &request) == 0) {
        printf("Parsed HTTP Request:\n");
        printf("Method: %s\n", request.method);
        printf("Path: %s\n", request.path);
        printf("Version: %s\n", request.version);
        printf("Query String: %s\n", request.query_string);
        printf("Headers: %d\n", request.header_count);
        
        for (int i = 0; i < request.header_count; i++) {
            printf("  %s\n", request.headers[i]);
        }
        
        printf("Body Length: %d\n", request.body_length);
        if (request.body_length > 0) {
            printf("Body: %s\n", request.body);
        }
    } else {
        printf("Failed to parse HTTP request\n");
    }
    
    // Create HTTP response
    HTTPResponse* response = createHTTPResponse(200, "OK", "text/html", 
        "<html><body><h1>Hello from C Server!</h1><p>This is a test response.</p></body></html>");
    
    if (response) {
        printf("\nCreated HTTP Response:\n");
        printf("Status: %d %s\n", response->status_code, response->status_message);
        printf("Content-Type: %s\n", response->content_type);
        printf("Body Length: %d\n", response->body_length);
        printf("Body: %s\n", response->body);
        
        // Build response
        char response_buffer[BUFFER_SIZE];
        int response_length = buildHTTPResponse(response, response_buffer, sizeof(response_buffer));
        
        printf("Response Buffer Length: %d\n", response_length);
        printf("Response Buffer:\n%s\n", response_buffer);
        
        free(response);
    }
}

void demonstrateWebSocket() {
    printf("\n=== WEBSOCKET DEMO ===\n");
    
    // Sample WebSocket frame
    const char* sample_frame = "\x81\x85\x05\x00Hello, WebSocket!";
    
    printf("Sample WebSocket Frame:\n");
    
    // Parse frame
    WebSocketFrame frame;
    if (parseWebSocketFrame(sample_frame, strlen(sample_frame), &frame) == 0) {
        printf("Parsed WebSocket Frame:\n");
        printf("FIN: %s\n", frame.fin ? "Yes" : "No");
        printf("Opcode: %d\n", frame.opcode);
        printf("Masked: %s\n", frame.masked ? "Yes" : "No");
        printf("Payload Length: %d\n", frame.payload_length);
        printf("Payload: %s\n", frame.payload);
    } else {
        printf("Failed to parse WebSocket frame\n");
    }
    
    // Create WebSocket frame
    WebSocketFrame response_frame;
    response_frame.fin = 1;
    response_frame.opcode = 1; // Text frame
    response_frame.masked = 0;
    response_frame.payload_length = strlen("Hello, Client!");
    strcpy(response_frame.payload, "Hello, Client!");
    
    printf("\nCreated WebSocket Frame:\n");
    printf("FIN: %s\n", response_frame.fin ? "Yes" : "No");
    printf("Opcode: %d\n", response_frame.opcode);
    printf("Masked: %s\n", response_frame.masked ? "Yes" : "No");
    printf("Payload Length: %d\n", response_frame.payload_length);
    printf("Payload: %s\n", response_frame.payload);
    
    // Build frame
    char frame_buffer[BUFFER_SIZE];
    int frame_length = buildWebSocketFrame(&response_frame, frame_buffer, sizeof(frame_buffer));
    
    printf("Frame Buffer Length: %d\n", frame_length);
    printf("Frame Buffer:\n");
    
    // Print frame buffer in hex
    for (int i = 0; i < frame_length; i++) {
        printf("%02X ", (unsigned char)frame_buffer[i]);
        if ((i + 1) % 16 == 0) {
            printf("\n");
        }
    }
    printf("\n");
}

void demonstrateServer() {
    printf("\n=== SERVER DEMO ===\n");
    
    // Create server
    ServerConfig config = {
        .port = 8080,
        .max_connections = 100,
        .thread_pool_size = 4,
        .buffer_size = BUFFER_SIZE,
        .timeout_seconds = 30,
        .backlog = BACKLOG
    };
    
    Server* server = createServer(&config);
    if (!server) {
        printf("Failed to create server\n");
        return;
    }
    
    printf("Server created with configuration:\n");
    printf("Port: %d\n", server->config.port);
    printf("Max Connections: %d\n", server->config.max_connections);
    printf("Thread Pool Size: %d\n", server->config.thread_pool_size);
    printf("Buffer Size: %d\n", server->config.buffer_size);
    printf("Timeout: %d seconds\n", server->config.timeout_seconds);
    
    // Start server
    if (startServer(server) == 0) {
        printf("Server started successfully\n");
        
        // Run server for a few seconds
        printf("Running server for 5 seconds...\n");
        
        // In a real implementation, this would run indefinitely
        // For demonstration, we'll simulate a short run
        sleep(5);
        
        // Stop server
        stopServer(server);
    } else {
        printf("Failed to start server\n");
    }
    
    free(server);
}

void demonstrateClient() {
    printf("\n=== CLIENT DEMO ===\n");
    
    // Create client
    ClientConfig config = {
        .server_address = "127.0.0.1",
        .server_port = 8080,
        .timeout_seconds = 10,
        .auto_reconnect = 1,
        .reconnect_interval = 2,
        .max_reconnect_attempts = 3,
        .buffer_size = BUFFER_SIZE
    };
    
    Client* client = createClient(&config);
    if (!client) {
        printf("Failed to create client\n");
        return;
    }
    
    printf("Client created with configuration:\n");
    printf("Server: %s:%d\n", client->config.server_address, client->config.server_port);
    printf("Timeout: %d seconds\n", client->config.timeout_seconds);
    printf("Auto Reconnect: %s\n", client->config.auto_reconnect ? "Yes" : "No");
    
    // Connect to server
    printf("Attempting to connect to server...\n");
    if (connectToServer(client) == 0) {
        printf("Connected successfully\n");
        
        // Send HTTP request
        const char* http_request = "GET / HTTP/1.1\r\nHost: localhost:8080\r\n\r\n";
        int sent = sendToServer(client, http_request, strlen(http_request));
        printf("Sent HTTP request (%d bytes)\n", sent);
        
        // Receive response
        char response_buffer[BUFFER_SIZE];
        int received = receiveFromServer(client, response_buffer, sizeof(response_buffer));
        
        if (received > 0) {
            response_buffer[received] = '\0';
            printf("Received HTTP response (%d bytes):\n%s\n", received, response_buffer);
        }
        
        // Disconnect
        disconnectFromServer(client);
    } else {
        printf("Failed to connect to server\n");
        
        // Test auto-reconnect
        printf("Testing auto-reconnect...\n");
        if (autoReconnect(client) == 0) {
            printf("Reconnected successfully\n");
            
            // Disconnect again
            disconnectFromServer(client);
        } else {
            printf("Auto-reconnect failed\n");
        }
    }
    
    printf("Client Statistics:\n");
    printf("Bytes Sent: %d\n", client->stats.bytes_sent);
    printf("Bytes Received: %d\n", client->stats.bytes_received);
    printf("Requests Sent: %d\n", client->stats.requests_sent);
    printf("Responses Received: %d\n", client->stats.responses_received);
    printf("Reconnect Count: %d\n", client->stats.reconnect_count);
    printf("Uptime: %.2f seconds\n", difftime(time(NULL), client->stats.uptime));
    
    free(client);
}

void demonstrateAsyncManager() {
    printf("\n=== ASYNC MANAGER DEMO ===\n");
    
    // Create async manager
    AsyncManager* manager = server->async_manager;
    
    pthread_mutex_lock(&manager->mutex);
    printf("Async Manager initialized\n");
    printf("Next Operation ID: %d\n", manager->next_operation_id);
    pthread_mutex_unlock(&manager->mutex);
    
    // Simulate async operations
    printf("Simulating async operations...\n");
    
    for (int i = 0; i < 5; i++) {
        pthread_mutex_lock(&manager->mutex);
        
        int operation_id = manager->next_operation_id++;
        
        AsyncOperation* operation = &manager->operations[i];
        operation->operation_id = operation_id;
        operation->socket_fd = i + 1;
        operation->operation_type = i % 3; // 0=read, 1=write, 2=connect
        operation->buffer_size = 1024;
        operation->bytes_processed = 0;
        operation->completed = 0;
        
        printf("Created async operation %d (type: %d, socket: %d)\n", 
               operation_id, operation->operation_type, operation->socket_fd);
        
        // Simulate completion
        operation->bytes_processed = operation->buffer_size;
        operation->completed = 1;
        
        if (operation->callback) {
            operation->callback(operation_id, operation->bytes_processed, NULL);
        }
        
        pthread_mutex_unlock(&manager->mutex);
        
        usleep(100000); // 100ms
    }
    
    printf("Async operations completed\n");
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Network Programming Examples\n");
    printf("===============================\n\n");
    
    // Run all demonstrations
    demonstrateSocketWrapper();
    demonstrateEventLoop();
    demonstrateThreadPool();
    demonstrateBuffers();
    demonstrateHTTPProtocol();
    demonstrateWebSocket();
    demonstrateServer();
    demonstrateClient();
    demonstrateAsyncManager();
    
    printf("\nAll advanced network programming examples demonstrated!\n");
    printf("Key features implemented:\n");
    printf("- Socket wrapper with non-blocking I/O\n");
    printf("- Event loop with epoll for scalability\n");
    printf("- Thread pool for parallel processing\n");
    printf("- Dynamic and circular buffer management\n");
    printf("- HTTP protocol implementation\n");
    printf("- WebSocket protocol implementation\n");
    printf("- Multi-client server with connection management\n");
    printf("- Client with auto-reconnect capability\n");
    printf("- Asynchronous I/O operations\n");
    printf("- Thread-safe operations with mutexes\n");
    printf("- Configurable server and client parameters\n");
    printf("- Comprehensive error handling\n");
    
    return 0;
}
