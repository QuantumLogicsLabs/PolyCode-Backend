/*
 * File: http_server.c
 * Description: Simple HTTP server implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <time.h>

#define PORT 8080
#define BUFFER_SIZE 4096
#define RESPONSE_SIZE 2048

// HTTP response structure
typedef struct {
    char* content;
    char* content_type;
    int status_code;
} HTTPResponse;

// Create HTTP response
void create_http_response(HTTPResponse* response, int status_code, 
                         const char* content_type, const char* content) {
    response->status_code = status_code;
    response->content_type = strdup(content_type);
    response->content = strdup(content);
}

// Send HTTP response
void send_http_response(int client_socket, HTTPResponse* response) {
    char response_buffer[RESPONSE_SIZE];
    char status_text[50];
    
    // Set status text
    switch (response->status_code) {
        case 200: strcpy(status_text, "OK"); break;
        case 404: strcpy(status_text, "Not Found"); break;
        case 500: strcpy(status_text, "Internal Server Error"); break;
        default: strcpy(status_text, "Unknown"); break;
    }
    
    // Build HTTP response
    snprintf(response_buffer, RESPONSE_SIZE,
             "HTTP/1.1 %d %s\r\n"
             "Content-Type: %s\r\n"
             "Content-Length: %ld\r\n"
             "Connection: close\r\n"
             "Server: SimpleHTTPServer/1.0\r\n"
             "Date: %s\r\n"
             "\r\n"
             "%s",
             response->status_code, status_text,
             response->content_type,
             strlen(response->content),
             get_current_time(),
             response->content);
    
    write(client_socket, response_buffer, strlen(response_buffer));
}

// Get current time string
char* get_current_time() {
    static char time_buffer[100];
    time_t rawtime;
    struct tm* timeinfo;
    
    time(&rawtime);
    timeinfo = gmtime(&rawtime);
    
    strftime(time_buffer, sizeof(time_buffer), "%a, %d %b %Y %H:%M:%S GMT", timeinfo);
    return time_buffer;
}

// Parse HTTP request
void parse_http_request(const char* request, char* method, char* path, char* version) {
    sscanf(request, "%s %s %s", method, path, version);
}

// Handle GET request
void handle_get_request(int client_socket, const char* path) {
    HTTPResponse response;
    
    // Default home page
    if (strcmp(path, "/") == 0) {
        char* html_content = 
            "<!DOCTYPE html>"
            "<html><head><title>Simple HTTP Server</title></head>"
            "<body>"
            "<h1>Welcome to Simple HTTP Server!</h1>"
            "<p>This is a basic HTTP server written in C.</p>"
            "<ul>"
            "<li><a href='/about'>About</a></li>"
            "<li><a href='/time'>Current Time</a></li>"
            "<li><a href='/info'>Server Info</a></li>"
            "</ul>"
            "</body></html>";
        
        create_http_response(&response, 200, "text/html", html_content);
    }
    // About page
    else if (strcmp(path, "/about") == 0) {
        char* html_content = 
            "<!DOCTYPE html>"
            "<html><head><title>About</title></head>"
            "<body>"
            "<h1>About This Server</h1>"
            "<p>A simple HTTP server implementation in C demonstrating:</p>"
            "<ul>"
            "<li>Socket programming</li>"
            "<li>HTTP protocol basics</li>"
            "<li>Request/response handling</li>"
            "<li>Content type management</li>"
            "</ul>"
            "<p><a href='/'>Back to Home</a></p>"
            "</body></html>";
        
        create_http_response(&response, 200, "text/html", html_content);
    }
    // Time page
    else if (strcmp(path, "/time") == 0) {
        char html_content[512];
        snprintf(html_content, sizeof(html_content),
            "<!DOCTYPE html>"
            "<html><head><title>Current Time</title></head>"
            "<body>"
            "<h1>Current Time</h1>"
            "<p>Server time: %s</p>"
            "<p><a href='/'>Back to Home</a></p>"
            "</body></html>",
            get_current_time());
        
        create_http_response(&response, 200, "text/html", html_content);
    }
    // Server info page
    else if (strcmp(path, "/info") == 0) {
        char html_content[1024];
        snprintf(html_content, sizeof(html_content),
            "<!DOCTYPE html>"
            "<html><head><title>Server Info</title></head>"
            "<body>"
            "<h1>Server Information</h1>"
            "<ul>"
            "<li>Server: SimpleHTTPServer/1.0</li>"
            "<li>Port: %d</li>"
            "<li>Protocol: HTTP/1.1</li>"
            "<li>Language: C</li>"
            "<li>Current Time: %s</li>"
            "</ul>"
            "<p><a href='/'>Back to Home</a></p>"
            "</body></html>",
            PORT, get_current_time());
        
        create_http_response(&response, 200, "text/html", html_content);
    }
    // 404 Not Found
    else {
        char* html_content = 
            "<!DOCTYPE html>"
            "<html><head><title>404 Not Found</title></head>"
            "<body>"
            "<h1>404 - Page Not Found</h1>"
            "<p>The requested page was not found on this server.</p>"
            "<p><a href='/'>Back to Home</a></p>"
            "</body></html>";
        
        create_http_response(&response, 404, "text/html", html_content);
    }
    
    send_http_response(client_socket, &response);
    
    // Clean up
    free(response.content);
    free(response.content_type);
}

// Handle POST request
void handle_post_request(int client_socket, const char* path, const char* body) {
    HTTPResponse response;
    
    if (strcmp(path, "/echo") == 0) {
        char html_content[1024];
        snprintf(html_content, sizeof(html_content),
            "<!DOCTYPE html>"
            "<html><head><title>Echo</title></head>"
            "<body>"
            "<h1>Echo Service</h1>"
            "<p>You sent:</p>"
            "<pre>%s</pre>"
            "<p><a href='/'>Back to Home</a></p>"
            "</body></html>",
            body);
        
        create_http_response(&response, 200, "text/html", html_content);
    } else {
        char* html_content = 
            "<!DOCTYPE html>"
            "<html><head><title>Method Not Allowed</title></head>"
            "<body>"
            "<h1>405 - Method Not Allowed</h1>"
            "<p>POST method is not allowed on this endpoint.</p>"
            "<p><a href='/'>Back to Home</a></p>"
            "</body></html>";
        
        create_http_response(&response, 405, "text/html", html_content);
    }
    
    send_http_response(client_socket, &response);
    
    // Clean up
    free(response.content);
    free(response.content_type);
}

// Handle client request
void handle_client(int client_socket) {
    char buffer[BUFFER_SIZE];
    int bytes_received = recv(client_socket, buffer, BUFFER_SIZE - 1, 0);
    
    if (bytes_received <= 0) {
        close(client_socket);
        return;
    }
    
    buffer[bytes_received] = '\0';
    
    printf("Received request:\n%s\n", buffer);
    
    // Parse request
    char method[16], path[256], version[16];
    parse_http_request(buffer, method, path, version);
    
    printf("Method: %s, Path: %s, Version: %s\n", method, path, version);
    
    // Find body for POST requests
    char* body = strstr(buffer, "\r\n\r\n");
    if (body) {
        body += 4; // Skip the \r\n\r\n
    }
    
    // Handle different methods
    if (strcmp(method, "GET") == 0) {
        handle_get_request(client_socket, path);
    } else if (strcmp(method, "POST") == 0) {
        handle_post_request(client_socket, path, body ? body : "");
    } else {
        // Method not supported
        HTTPResponse response;
        char* html_content = 
            "<!DOCTYPE html>"
            "<html><head><title>Method Not Allowed</title></head>"
            "<body>"
            "<h1>405 - Method Not Allowed</h1>"
            "<p>Only GET and POST methods are supported.</p>"
            "<p><a href='/'>Back to Home</a></p>"
            "</body></html>";
        
        create_http_response(&response, 405, "text/html", html_content);
        send_http_response(client_socket, &response);
        
        free(response.content);
        free(response.content_type);
    }
    
    close(client_socket);
}

int main() {
    int server_socket, client_socket;
    struct sockaddr_in server_addr, client_addr;
    socklen_t client_len = sizeof(client_addr);
    
    // Create socket
    server_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (server_socket < 0) {
        perror("Socket creation failed");
        exit(EXIT_FAILURE);
    }
    
    // Set socket options
    int opt = 1;
    setsockopt(server_socket, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));
    
    // Configure server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_addr.s_addr = INADDR_ANY;
    server_addr.sin_port = htons(PORT);
    
    // Bind socket to address
    if (bind(server_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Bind failed");
        close(server_socket);
        exit(EXIT_FAILURE);
    }
    
    // Listen for connections
    if (listen(server_socket, 5) < 0) {
        perror("Listen failed");
        close(server_socket);
        exit(EXIT_FAILURE);
    }
    
    printf("HTTP Server started on port %d\n", PORT);
    printf("Open your web browser and navigate to: http://localhost:%d\n", PORT);
    printf("Press Ctrl+C to stop the server\n");
    
    // Accept connections
    while (1) {
        client_socket = accept(server_socket, (struct sockaddr*)&client_addr, &client_len);
        if (client_socket < 0) {
            perror("Accept failed");
            continue;
        }
        
        printf("Connection from %s:%d\n", 
               inet_ntoa(client_addr.sin_addr), ntohs(client_addr.sin_port));
        
        // Handle client (in real application, you'd use threads or processes)
        handle_client(client_socket);
    }
    
    close(server_socket);
    return 0;
}
