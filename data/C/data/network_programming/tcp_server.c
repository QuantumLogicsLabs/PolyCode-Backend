/*
 * File: tcp_server.c
 * Description: Simple TCP server implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#define PORT 8080
#define BUFFER_SIZE 1024

void handle_client(int client_socket) {
    char buffer[BUFFER_SIZE];
    int read_bytes;
    
    printf("Client connected\n");
    
    // Read data from client
    while ((read_bytes = read(client_socket, buffer, BUFFER_SIZE - 1)) > 0) {
        buffer[read_bytes] = '\0';
        printf("Received: %s", buffer);
        
        // Echo back to client
        write(client_socket, buffer, read_bytes);
        
        // Exit if client sends "quit"
        if (strncmp(buffer, "quit", 4) == 0) {
            break;
        }
    }
    
    printf("Client disconnected\n");
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
    
    printf("TCP Server listening on port %d\n", PORT);
    
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
