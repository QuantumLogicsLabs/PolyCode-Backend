/*
 * File: tcp_client.c
 * Description: Simple TCP client implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#define SERVER_IP "127.0.0.1"
#define PORT 8080
#define BUFFER_SIZE 1024

int main() {
    int client_socket;
    struct sockaddr_in server_addr;
    char buffer[BUFFER_SIZE];
    
    // Create socket
    client_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (client_socket < 0) {
        perror("Socket creation failed");
        exit(EXIT_FAILURE);
    }
    
    // Configure server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(PORT);
    
    // Convert IP address
    if (inet_pton(AF_INET, SERVER_IP, &server_addr.sin_addr) <= 0) {
        perror("Invalid address");
        close(client_socket);
        exit(EXIT_FAILURE);
    }
    
    // Connect to server
    if (connect(client_socket, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Connection failed");
        close(client_socket);
        exit(EXIT_FAILURE);
    }
    
    printf("Connected to TCP server at %s:%d\n", SERVER_IP, PORT);
    printf("Type messages (type 'quit' to exit):\n");
    
    // Communication loop
    while (1) {
        printf("> ");
        fgets(buffer, BUFFER_SIZE, stdin);
        
        // Send message to server
        if (write(client_socket, buffer, strlen(buffer)) < 0) {
            perror("Send failed");
            break;
        }
        
        // Exit if user typed "quit"
        if (strncmp(buffer, "quit", 4) == 0) {
            break;
        }
        
        // Read response from server
        int bytes_read = read(client_socket, buffer, BUFFER_SIZE - 1);
        if (bytes_read > 0) {
            buffer[bytes_read] = '\0';
            printf("Server: %s", buffer);
        } else if (bytes_read == 0) {
            printf("Server disconnected\n");
            break;
        } else {
            perror("Read failed");
            break;
        }
    }
    
    close(client_socket);
    printf("Disconnected from server\n");
    
    return 0;
}
