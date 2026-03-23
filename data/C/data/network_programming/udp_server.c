/*
 * File: udp_server.c
 * Description: Simple UDP server implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#define PORT 9090
#define BUFFER_SIZE 1024

int main() {
    int server_socket;
    struct sockaddr_in server_addr, client_addr;
    char buffer[BUFFER_SIZE];
    socklen_t client_len = sizeof(client_addr);
    
    // Create UDP socket
    server_socket = socket(AF_INET, SOCK_DGRAM, 0);
    if (server_socket < 0) {
        perror("Socket creation failed");
        exit(EXIT_FAILURE);
    }
    
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
    
    printf("UDP Server listening on port %d\n", PORT);
    
    // Receive and send messages
    while (1) {
        // Receive message from client
        int bytes_received = recvfrom(server_socket, buffer, BUFFER_SIZE - 1, 0,
                                     (struct sockaddr*)&client_addr, &client_len);
        
        if (bytes_received < 0) {
            perror("Receive failed");
            continue;
        }
        
        buffer[bytes_received] = '\0';
        
        printf("Received from %s:%d: %s", 
               inet_ntoa(client_addr.sin_addr), ntohs(client_addr.sin_port), buffer);
        
        // Echo back to client
        if (sendto(server_socket, buffer, bytes_received, 0,
                   (struct sockaddr*)&client_addr, client_len) < 0) {
            perror("Send failed");
        }
        
        // Exit if client sends "quit"
        if (strncmp(buffer, "quit", 4) == 0) {
            printf("Client requested to quit\n");
            break;
        }
    }
    
    close(server_socket);
    return 0;
}
