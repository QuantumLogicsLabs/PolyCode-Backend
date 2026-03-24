# Advanced Network Programming

This file contains comprehensive advanced network programming examples in C, including socket wrappers, event loops, thread pools, buffer management, HTTP/WebSocket protocols, server architecture, client implementation, and asynchronous I/O operations.

## 📚 Advanced Network Programming Fundamentals

### 🌐 Network Programming Concepts
- **Socket Programming**: TCP/UDP socket creation and management
- **Event-Driven Architecture**: Non-blocking I/O with epoll/select
- **Thread Pooling**: Parallel processing of network operations
- **Protocol Implementation**: HTTP, WebSocket, and custom protocols
- **Connection Management**: Multi-client server with connection pooling

### 🎯 Network Architecture
- **Scalable Design**: Support for thousands of concurrent connections
- **Asynchronous I/O**: Non-blocking operations for performance
- **Buffer Management**: Efficient memory management for network data
- **Error Handling**: Robust error handling and recovery
- **Security**: SSL/TLS support and secure communication

## 🔌 Socket Wrapper and Utilities

### Socket Wrapper Structure
```c
// Socket wrapper structure
typedef struct {
    int socket_fd;
    struct sockaddr_in address;
    int is_connected;
    int is_blocking;
    int timeout_seconds;
    char remote_address[INET_ADDRSTRLEN];
} SocketWrapper;
```

### Socket Wrapper Implementation
```c
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
```

**Socket Wrapper Benefits**:
- **Abstraction**: High-level interface for socket operations
- **Error Handling**: Comprehensive error checking and reporting
- **Configuration**: Flexible socket configuration options
- **Portability**: Cross-platform socket handling

## 🔄 Asynchronous I/O and Event Loop

### Event Loop Structure
```c
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
```

### Event Loop Implementation
```c
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
```

**Event Loop Benefits**:
- **Scalability**: Handle thousands of connections efficiently
- **Non-blocking**: Asynchronous I/O operations
- **Event-driven**: React to network events as they occur
- **Performance**: High-performance event handling with epoll

## 🧵 Thread Pool

### Thread Pool Structure
```c
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
```

### Thread Pool Implementation
```c
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
```

**Thread Pool Benefits**:
- **Parallel Processing**: Execute multiple tasks simultaneously
- **Resource Management**: Efficient thread reuse
- **Load Balancing**: Distribute tasks across available threads
- **Scalability**: Handle variable workloads efficiently

## 📊 Buffer Management

### Dynamic Buffer Structure
```c
// Dynamic buffer structure
typedef struct {
    char* data;
    int size;
    int capacity;
    int position;
    int auto_expand;
} DynamicBuffer;
```

### Circular Buffer Structure
```c
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
```

### Buffer Implementation
```c
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
```

**Buffer Management Benefits**:
- **Memory Efficiency**: Optimize memory usage for network data
- **Performance**: Fast read/write operations
- **Flexibility**: Support for different buffer types
- **Thread Safety**: Safe concurrent access with proper synchronization

## 🌐 HTTP Protocol Implementation

### HTTP Request Structure
```c
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
```

### HTTP Response Structure
```c
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
```

### HTTP Implementation
```c
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
```

**HTTP Benefits**:
- **Protocol Compliance**: Full HTTP/1.1 support
- **Flexible**: Support for various HTTP methods and headers
- **Efficient**: Optimized parsing and response generation
- **Extensible**: Easy to add new HTTP features

## 🔌 WebSocket Protocol Implementation

### WebSocket Frame Structure
```c
// WebSocket frame structure
typedef struct {
    int opcode;
    int fin;
    int masked;
    char mask[4];
    char payload[BUFFER_SIZE];
    int payload_length;
} WebSocketFrame;
```

### WebSocket Implementation
```c
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
```

**WebSocket Benefits**:
- **Real-time Communication**: Bidirectional messaging
- **Low Latency**: Persistent connections for fast data transfer
- **Protocol Support**: Full WebSocket protocol implementation
- **Efficient**: Minimal overhead for frequent messages

## 🏢 Connection Management

### Connection State
```c
// Connection state
typedef enum {
    STATE_DISCONNECTED = 0,
    STATE_CONNECTING = 1,
    STATE_CONNECTED = 2,
    STATE_AUTHENTICATED = 3,
    STATE_READY = 4
} ConnectionState;
```

### Connection Structure
```c
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
```

### Connection Manager
```c
// Connection manager
typedef struct {
    Connection connections[MAX_CONNECTIONS];
    int connection_count;
    int max_connections;
    pthread_mutex_t mutex;
    time_t last_cleanup;
} ConnectionManager;

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
```

**Connection Management Benefits**:
- **Scalability**: Support for thousands of concurrent connections
- **State Tracking**: Complete connection lifecycle management
- **Resource Management**: Efficient memory and socket management
- **Thread Safety**: Safe concurrent access to connection data

## 🖥️ Server Architecture

### Server Configuration
```c
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
```

### Server Structure
```c
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
```

### Server Implementation
```c
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
```

**Server Benefits**:
- **High Performance**: Event-driven architecture for scalability
- **Modular Design**: Separate subsystems for maintainability
- **Configurable**: Flexible server configuration options
- **Robust**: Comprehensive error handling and recovery

## 🖥️ Client Architecture

### Client Configuration
```c
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
```

### Client Structure
```c
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
```

### Client Implementation
```c
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
```

**Client Benefits**:
- **Reliable**: Auto-reconnect capability
- **Thread-safe**: Safe concurrent operations
- **Flexible**: Configurable connection parameters
- **Robust**: Comprehensive error handling

## 🔧 Best Practices

### 1. Error Handling
```c
// Good: Comprehensive error handling
int sendData(SocketWrapper* wrapper, const char* data, int length) {
    if (!wrapper || !data || length <= 0 || !wrapper->is_connected) {
        return -1;
    }
    
    int total_sent = 0;
    while (total_sent < length) {
        int sent = send(wrapper->socket_fd, data + total_sent, length - total_sent, MSG_NOSIGNAL);
        if (sent < 0) {
            if (errno == EAGAIN || errno == EWOULDBLOCK) {
                return total_sent; // Socket would block
            }
            if (errno == ECONNRESET || errno == EPIPE) {
                wrapper->is_connected = 0; // Connection lost
            }
            return -1;
        }
        total_sent += sent;
    }
    
    return total_sent;
}

// Bad: No error handling
int sendData(SocketWrapper* wrapper, const char* data, int length) {
    return send(wrapper->socket_fd, data, length, 0); // No error checking
}
```

### 2. Resource Management
```c
// Good: Proper resource cleanup
void destroyEventLoop(EventLoop* loop) {
    if (!loop) return;
    
    if (loop->epoll_fd >= 0) {
        close(loop->epoll_fd);
    }
    
    pthread_mutex_destroy(&loop->mutex);
    free(loop);
}

// Bad: Resource leaks
void destroyEventLoop(EventLoop* loop) {
    free(loop); // File descriptor and mutex not cleaned up
}
```

### 3. Thread Safety
```c
// Good: Thread-safe operations
int addConnection(ConnectionManager* manager, int socket_fd, struct sockaddr_in* address) {
    if (!manager) return -1;
    
    pthread_mutex_lock(&manager->mutex);
    
    // Connection management logic here
    
    manager->connection_count++;
    
    pthread_mutex_unlock(&manager->mutex);
    return 0;
}

// Bad: Not thread-safe
int addConnection(ConnectionManager* manager, int socket_fd, struct sockaddr_in* address) {
    // No mutex protection - race conditions possible
    manager->connection_count++;
    return 0;
}
```

### 4. Memory Management
```c
// Good: Memory allocation with error checking
DynamicBuffer* createDynamicBuffer(int initial_capacity, int auto_expand) {
    DynamicBuffer* buffer = malloc(sizeof(DynamicBuffer));
    if (!buffer) return NULL;
    
    buffer->data = malloc(initial_capacity);
    if (!buffer->data) {
        free(buffer);
        return NULL;
    }
    
    // Initialize buffer
    return buffer;
}

// Bad: No error checking
DynamicBuffer* createDynamicBuffer(int initial_capacity, int auto_expand) {
    DynamicBuffer* buffer = malloc(sizeof(DynamicBuffer));
    buffer->data = malloc(initial_capacity); // No error checking
    return buffer;
}
```

### 5. Protocol Validation
```c
// Good: Input validation
int parseHTTPRequest(const char* request_data, int request_length, HTTPRequest* request) {
    if (!request_data || !request || request_length <= 0) {
        return -1;
    }
    
    // Validate request format
    if (request_length > MAX_REQUEST_SIZE) {
        return -1;
    }
    
    // Check for required fields
    char* line_end = strstr(request_data, "\r\n");
    if (!line_end) {
        return -1;
    }
    
    // Parse request
    return 0;
}

// Bad: No validation
int parseHTTPRequest(const char* request_data, int request_length, HTTPRequest* request) {
    sscanf(request_data, "%s %s %s", request->method, request->path, request->version);
    // No validation - can cause buffer overflows
    return 0;
}
```

## ⚠️ Common Pitfalls

### 1. Blocking Operations
```c
// Wrong: Blocking operations in event loop
void handleClientData(int socket_fd) {
    char buffer[BUFFER_SIZE];
    int received = recv(socket_fd, buffer, sizeof(buffer), 0); // Blocking
    // Process data
}

// Right: Non-blocking operations
void handleClientData(int socket_fd) {
    char buffer[BUFFER_SIZE];
    int received = recv(socket_fd, buffer, sizeof(buffer), MSG_DONTWAIT);
    if (received < 0 && (errno == EAGAIN || errno == EWOULDBLOCK)) {
        return; // No data available, try again later
    }
    // Process data
}
```

### 2. Memory Leaks
```c
// Wrong: Memory leak in connection handling
void handleNewConnection(int client_socket) {
    Connection* connection = malloc(sizeof(Connection));
    connection->socket_fd = client_socket;
    // Process connection
    // Forgot to free(connection)
}

// Right: Proper memory management
void handleNewConnection(int client_socket) {
    Connection* connection = malloc(sizeof(Connection));
    connection->socket_fd = client_socket;
    // Process connection
    free(connection);
}
```

### 3. Race Conditions
```c
// Wrong: Race condition in connection count
void addConnection() {
    connection_count++; // Race condition with multiple threads
}

// Right: Thread-safe counter
void addConnection() {
    pthread_mutex_lock(&connection_mutex);
    connection_count++;
    pthread_mutex_unlock(&connection_mutex);
}
```

### 4. Buffer Overflows
```c
// Wrong: No bounds checking
void copyData(char* dest, const char* src) {
    strcpy(dest, src); // Can overflow if src is too long
}

// Right: Safe string operations
void copyData(char* dest, const char* src, int dest_size) {
    strncpy(dest, src, dest_size - 1);
    dest[dest_size - 1] = '\0';
}
```

## 🔧 Real-World Applications

### 1. Web Server
```c
// HTTP web server implementation
void handleHTTPRequest(Server* server, Connection* connection) {
    HTTPRequest request;
    if (parseHTTPRequest(connection->read_buffer->data, connection->read_buffer->size, &request) == 0) {
        printf("Received %s request for %s\n", request.method, request.path);
        
        // Route request
        if (strcmp(request.path, "/") == 0) {
            HTTPResponse* response = createHTTPResponse(200, "OK", "text/html", 
                "<html><body><h1>Welcome!</h1></body></html>");
            
            char response_buffer[BUFFER_SIZE];
            int response_length = buildHTTPResponse(response, response_buffer, sizeof(response_buffer));
            
            sendData((SocketWrapper*)&connection->socket_fd, response_buffer, response_length);
            free(response);
        }
    }
}
```

### 2. Chat Server
```c
// WebSocket chat server
void handleWebSocketMessage(Server* server, Connection* connection, WebSocketFrame* frame) {
    if (frame->opcode == 1) { // Text frame
        // Broadcast message to all connected clients
        for (int i = 0; i < server->connection_manager->max_connections; i++) {
            Connection* client = &server->connection_manager->connections[i];
            if (client->socket_fd >= 0 && client->state == STATE_CONNECTED) {
                sendData((SocketWrapper*)&client->socket_fd, frame->payload, frame->payload_length);
            }
        }
    }
}
```

### 3. File Transfer Server
```c
// File transfer server
void handleFileTransfer(Server* server, Connection* connection) {
    char filename[256];
    int file_size;
    
    // Receive file metadata
    receiveData((SocketWrapper*)&connection->socket_fd, (char*)&file_size, sizeof(file_size));
    receiveData((SocketWrapper*)&connection->socket_fd, filename, sizeof(filename));
    
    // Receive file data
    FILE* file = fopen(filename, "wb");
    if (file) {
        char buffer[BUFFER_SIZE];
        int total_received = 0;
        
        while (total_received < file_size) {
            int received = receiveData((SocketWrapper*)&connection->socket_fd, buffer, sizeof(buffer));
            if (received <= 0) break;
            
            fwrite(buffer, 1, received, file);
            total_received += received;
        }
        
        fclose(file);
        printf("File %s received successfully (%d bytes)\n", filename, total_received);
    }
}
```

## 📚 Further Reading

### Books
- "UNIX Network Programming" by W. Richard Stevens
- "Beej's Guide to Network Programming" by Brian Hall
- "Network Programming with Sockets" by various authors

### Topics
- SSL/TLS implementation
- IPv6 support
- UDP programming
- Raw sockets
- Network security
- Load balancing

Advanced network programming in C provides the foundation for building high-performance, scalable, and robust network applications. Master these techniques to create professional-grade servers and clients that can handle thousands of concurrent connections efficiently!
