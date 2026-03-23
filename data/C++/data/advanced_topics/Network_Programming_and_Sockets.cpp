// Module 14: Network Programming and Sockets - Real-Life Examples
// This file demonstrates practical applications of network programming

#include <iostream>
#include <string>
#include <thread>
#include <vector>
#include <memory>
#include <map>
#include <atomic>
#include <mutex>
#include <condition_variable>
#include <chrono>

// Platform-specific headers
#ifdef _WIN32
    #include <winsock2.h>
    #include <ws2tcpip.h>
    #pragma comment(lib, "ws2_32.lib")
#else
    #include <sys/socket.h>
    #include <netinet/in.h>
    #include <arpa/inet.h>
    #include <unistd.h>
    #include <fcntl.h>
    #define SOCKET int
    #define INVALID_SOCKET -1
    #define SOCKET_ERROR -1
    #define closesocket close
#endif

// Network initialization class
class NetworkInitializer {
public:
    NetworkInitializer() {
#ifdef _WIN32
        WSADATA wsaData;
        if (WSAStartup(MAKEWORD(2, 2), &wsaData) != 0) {
            throw std::runtime_error("Failed to initialize Winsock");
        }
#endif
    }
    
    ~NetworkInitializer() {
#ifdef _WIN32
        WSACleanup();
#endif
    }
};

// Example 1: Simple TCP Chat Server
class ChatServer {
private:
    SOCKET serverSocket;
    int port;
    std::atomic<bool> running;
    std::vector<std::thread> clientThreads;
    std::mutex clientsMutex;
    std::vector<SOCKET> clientSockets;
    
public:
    ChatServer(int port) : port(port), running(false) {}
    
    ~ChatServer() {
        stop();
    }
    
    void start() {
        NetworkInitializer init;
        
        serverSocket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
        if (serverSocket == INVALID_SOCKET) {
            throw std::runtime_error("Failed to create server socket");
        }
        
        // Set socket options
        int opt = 1;
        if (setsockopt(serverSocket, SOL_SOCKET, SO_REUSEADDR, 
                       reinterpret_cast<const char*>(&opt), sizeof(opt)) < 0) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to set socket options");
        }
        
        // Bind socket
        sockaddr_in serverAddr;
        serverAddr.sin_family = AF_INET;
        serverAddr.sin_addr.s_addr = INADDR_ANY;
        serverAddr.sin_port = htons(port);
        
        if (bind(serverSocket, reinterpret_cast<sockaddr*>(&serverAddr), 
                 sizeof(serverAddr)) == SOCKET_ERROR) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to bind socket");
        }
        
        // Start listening
        if (listen(serverSocket, SOMAXCONN) == SOCKET_ERROR) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to listen");
        }
        
        running = true;
        std::cout << "Chat server started on port " << port << std::endl;
        
        acceptConnections();
    }
    
    void stop() {
        running = false;
        
        // Close server socket
        if (serverSocket != INVALID_SOCKET) {
            closesocket(serverSocket);
        }
        
        // Close all client sockets
        {
            std::lock_guard<std::mutex> lock(clientsMutex);
            for (SOCKET clientSocket : clientSockets) {
                closesocket(clientSocket);
            }
            clientSockets.clear();
        }
        
        // Wait for all client threads to finish
        for (auto& thread : clientThreads) {
            if (thread.joinable()) {
                thread.join();
            }
        }
        clientThreads.clear();
    }
    
private:
    void acceptConnections() {
        while (running) {
            sockaddr_in clientAddr;
            socklen_t clientAddrLen = sizeof(clientAddr);
            
            SOCKET clientSocket = accept(serverSocket, 
                                       reinterpret_cast<sockaddr*>(&clientAddr), 
                                       &clientAddrLen);
            
            if (clientSocket == INVALID_SOCKET) {
                if (running) {
                    std::cerr << "Failed to accept client connection" << std::endl;
                }
                continue;
            }
            
            // Get client IP address
            char clientIP[INET_ADDRSTRLEN];
            inet_ntop(AF_INET, &(clientAddr.sin_addr), clientIP, INET_ADDRSTRLEN);
            std::cout << "Client connected from " << clientIP << ":" 
                      << ntohs(clientAddr.sin_port) << std::endl;
            
            // Handle client in separate thread
            std::lock_guard<std::mutex> lock(clientsMutex);
            clientSockets.push_back(clientSocket);
            clientThreads.emplace_back([this, clientSocket, std::string(clientIP)]() {
                handleClient(clientSocket, std::string(clientIP));
            });
        }
    }
    
    void handleClient(SOCKET clientSocket, std::string clientIP) {
        char buffer[1024];
        
        while (running) {
            // Receive data
            int bytesReceived = recv(clientSocket, buffer, sizeof(buffer) - 1, 0);
            
            if (bytesReceived <= 0) {
                if (bytesReceived == 0) {
                    std::cout << "Client " << clientIP << " disconnected" << std::endl;
                } else {
                    std::cerr << "Error receiving data from client " << clientIP << std::endl;
                }
                break;
            }
            
            buffer[bytesReceived] = '\0';
            std::string message(buffer);
            std::cout << "Received from " << clientIP << ": " << message << std::endl;
            
            // Broadcast message to all clients
            broadcastMessage(message, clientSocket);
        }
        
        // Remove client from list
        {
            std::lock_guard<std::mutex> lock(clientsMutex);
            clientSockets.erase(
                std::remove(clientSockets.begin(), clientSockets.end(), clientSocket),
                clientSockets.end()
            );
        }
        
        closesocket(clientSocket);
    }
    
    void broadcastMessage(const std::string& message, SOCKET excludeSocket = INVALID_SOCKET) {
        std::lock_guard<std::mutex> lock(clientsMutex);
        
        for (SOCKET clientSocket : clientSockets) {
            if (clientSocket != excludeSocket) {
                send(clientSocket, message.c_str(), message.length(), 0);
            }
        }
    }
};

// Example 2: Simple TCP Chat Client
class ChatClient {
private:
    SOCKET clientSocket;
    std::string serverIP;
    int serverPort;
    std::atomic<bool> connected;
    
public:
    ChatClient(const std::string& ip, int port) 
        : serverIP(ip), serverPort(port), connected(false) {}
    
    ~ChatClient() {
        disconnect();
    }
    
    bool connect() {
        NetworkInitializer init;
        
        clientSocket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
        if (clientSocket == INVALID_SOCKET) {
            std::cerr << "Failed to create client socket" << std::endl;
            return false;
        }
        
        // Set server address
        sockaddr_in serverAddr;
        serverAddr.sin_family = AF_INET;
        serverAddr.sin_port = htons(serverPort);
        
        if (inet_pton(AF_INET, serverIP.c_str(), &(serverAddr.sin_addr)) <= 0) {
            std::cerr << "Invalid server IP address" << std::endl;
            closesocket(clientSocket);
            return false;
        }
        
        // Connect to server
        if (::connect(clientSocket, reinterpret_cast<sockaddr*>(&serverAddr), 
                     sizeof(serverAddr)) == SOCKET_ERROR) {
            std::cerr << "Failed to connect to server" << std::endl;
            closesocket(clientSocket);
            return false;
        }
        
        connected = true;
        std::cout << "Connected to server " << serverIP << ":" << serverPort << std::endl;
        return true;
    }
    
    void disconnect() {
        if (connected && clientSocket != INVALID_SOCKET) {
            closesocket(clientSocket);
            clientSocket = INVALID_SOCKET;
            connected = false;
            std::cout << "Disconnected from server" << std::endl;
        }
    }
    
    bool sendMessage(const std::string& message) {
        if (!connected) {
            std::cerr << "Not connected to server" << std::endl;
            return false;
        }
        
        if (send(clientSocket, message.c_str(), message.length(), 0) == SOCKET_ERROR) {
            std::cerr << "Failed to send message" << std::endl;
            return false;
        }
        
        std::cout << "Sent: " << message << std::endl;
        return true;
    }
    
    std::string receiveMessage() {
        if (!connected) {
            std::cerr << "Not connected to server" << std::endl;
            return "";
        }
        
        char buffer[1024];
        int bytesReceived = recv(clientSocket, buffer, sizeof(buffer) - 1, 0);
        
        if (bytesReceived <= 0) {
            if (bytesReceived == 0) {
                std::cout << "Server closed the connection" << std::endl;
            } else {
                std::cerr << "Error receiving data" << std::endl;
            }
            connected = false;
            return "";
        }
        
        buffer[bytesReceived] = '\0';
        std::string message(buffer);
        std::cout << "Received: " << message << std::endl;
        
        return message;
    }
    
    bool isConnected() const {
        return connected;
    }
};

// Example 3: HTTP Server
class HTTPServer {
private:
    SOCKET serverSocket;
    int port;
    std::atomic<bool> running;
    
    std::string generateResponse(const std::string& contentType, const std::string& content) {
        std::string response = "HTTP/1.1 200 OK\r\n";
        response += "Content-Type: " + contentType + "\r\n";
        response += "Content-Length: " + std::to_string(content.length()) + "\r\n";
        response += "Connection: close\r\n";
        response += "\r\n" + content;
        return response;
    }
    
    std::string generateHTMLResponse(const std::string& title, const std::string& body) {
        std::string html = "<!DOCTYPE html><html><head><title>" + title + "</title></head><body>" + body + "</body></html>";
        return generateResponse("text/html", html);
    }
    
    std::string generateJSONResponse(const std::string& json) {
        return generateResponse("application/json", json);
    }
    
public:
    HTTPServer(int port) : port(port), running(false) {}
    
    ~HTTPServer() {
        stop();
    }
    
    void start() {
        NetworkInitializer init;
        
        serverSocket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
        if (serverSocket == INVALID_SOCKET) {
            throw std::runtime_error("Failed to create HTTP server socket");
        }
        
        sockaddr_in serverAddr;
        serverAddr.sin_family = AF_INET;
        serverAddr.sin_addr.s_addr = INADDR_ANY;
        serverAddr.sin_port = htons(port);
        
        if (bind(serverSocket, reinterpret_cast<sockaddr*>(&serverAddr), 
                 sizeof(serverAddr)) == SOCKET_ERROR) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to bind HTTP server socket");
        }
        
        if (listen(serverSocket, SOMAXCONN) == SOCKET_ERROR) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to listen for HTTP connections");
        }
        
        running = true;
        std::cout << "HTTP server started on port " << port << std::endl;
        
        handleRequests();
    }
    
    void stop() {
        running = false;
        if (serverSocket != INVALID_SOCKET) {
            closesocket(serverSocket);
        }
    }
    
private:
    void handleRequests() {
        while (running) {
            sockaddr_in clientAddr;
            socklen_t clientAddrLen = sizeof(clientAddr);
            
            SOCKET clientSocket = accept(serverSocket, 
                                       reinterpret_cast<sockaddr*>(&clientAddr), 
                                       &clientAddrLen);
            
            if (clientSocket == INVALID_SOCKET) {
                if (running) {
                    std::cerr << "Failed to accept HTTP connection" << std::endl;
                }
                continue;
            }
            
            // Handle HTTP request in separate thread
            std::thread([this, clientSocket]() {
                handleHTTPRequest(clientSocket);
            }).detach();
        }
    }
    
    void handleHTTPRequest(SOCKET clientSocket) {
        char buffer[4096];
        int bytesReceived = recv(clientSocket, buffer, sizeof(buffer) - 1, 0);
        
        if (bytesReceived <= 0) {
            closesocket(clientSocket);
            return;
        }
        
        buffer[bytesReceived] = '\0';
        std::string request(buffer);
        
        // Parse HTTP request
        std::istringstream iss(request);
        std::string method, path, version;
        iss >> method >> path >> version;
        
        std::cout << "HTTP " << method << " request for " << path << std::endl;
        
        std::string response;
        
        if (method == "GET") {
            if (path == "/") {
                response = generateHTMLResponse("C++ HTTP Server", 
                    "<h1>Welcome to C++ HTTP Server</h1>"
                    "<p>This is a simple HTTP server implemented in C++.</p>"
                    "<p><a href=\"/api/status\">Check Status</a></p>"
                    "<p><a href=\"/api/time\">Get Current Time</a></p>");
            } else if (path == "/api/status") {
                response = generateJSONResponse(
                    "{\"status\": \"running\", \"server\": \"C++ HTTP Server\", \"version\": \"1.0\"}"
                );
            } else if (path == "/api/time") {
                auto now = std::chrono::system_clock::now();
                auto timeT = std::chrono::system_clock::to_time_t(now);
                response = generateJSONResponse(
                    "{\"timestamp\": " + std::to_string(timeT) + ", \"time\": \"" + std::string(std::ctime(&timeT)) + "\"}"
                );
            } else {
                response = generateHTMLResponse("404 Not Found", "<h1>404 - Not Found</h1><p>The requested page was not found.</p>");
            }
        } else {
            response = generateHTMLResponse("405 Method Not Allowed", "<h1>405 - Method Not Allowed</h1><p>Only GET method is supported.</p>");
        }
        
        send(clientSocket, response.c_str(), response.length(), 0);
        closesocket(clientSocket);
    }
};

// Example 4: UDP Echo Server
class UDPEchoServer {
private:
    SOCKET serverSocket;
    int port;
    std::atomic<bool> running;
    
public:
    UDPEchoServer(int port) : port(port), running(false) {}
    
    ~UDPEchoServer() {
        stop();
    }
    
    void start() {
        NetworkInitializer init;
        
        serverSocket = socket(AF_INET, SOCK_DGRAM, IPPROTO_UDP);
        if (serverSocket == INVALID_SOCKET) {
            throw std::runtime_error("Failed to create UDP socket");
        }
        
        sockaddr_in serverAddr;
        serverAddr.sin_family = AF_INET;
        serverAddr.sin_addr.s_addr = INADDR_ANY;
        serverAddr.sin_port = htons(port);
        
        if (bind(serverSocket, reinterpret_cast<sockaddr*>(&serverAddr), 
                 sizeof(serverAddr)) == SOCKET_ERROR) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to bind UDP socket");
        }
        
        running = true;
        std::cout << "UDP echo server started on port " << port << std::endl;
        
        handleRequests();
    }
    
    void stop() {
        running = false;
        if (serverSocket != INVALID_SOCKET) {
            closesocket(serverSocket);
        }
    }
    
private:
    void handleRequests() {
        char buffer[1024];
        sockaddr_in clientAddr;
        socklen_t clientAddrLen = sizeof(clientAddr);
        
        while (running) {
            int bytesReceived = recvfrom(serverSocket, buffer, sizeof(buffer) - 1, 0,
                                       reinterpret_cast<sockaddr*>(&clientAddr), 
                                       &clientAddrLen);
            
            if (bytesReceived <= 0) {
                if (running) {
                    std::cerr << "Error receiving UDP message" << std::endl;
                }
                continue;
            }
            
            buffer[bytesReceived] = '\0';
            
            char clientIP[INET_ADDRSTRLEN];
            inet_ntop(AF_INET, &(clientAddr.sin_addr), clientIP, INET_ADDRSTRLEN);
            
            std::cout << "UDP message from " << clientIP << ":" 
                      << ntohs(clientAddr.sin_port) << ": " << buffer << std::endl;
            
            // Echo back the message
            sendto(serverSocket, buffer, bytesReceived, 0,
                   reinterpret_cast<sockaddr*>(&clientAddr), clientAddrLen);
        }
    }
};

// Example 5: File Transfer Client
class FileTransferClient {
private:
    SOCKET clientSocket;
    std::string serverIP;
    int serverPort;
    std::atomic<bool> connected;
    
public:
    FileTransferClient(const std::string& ip, int port) 
        : serverIP(ip), serverPort(port), connected(false) {}
    
    ~FileTransferClient() {
        disconnect();
    }
    
    bool connect() {
        NetworkInitializer init;
        
        clientSocket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
        if (clientSocket == INVALID_SOCKET) {
            std::cerr << "Failed to create client socket" << std::endl;
            return false;
        }
        
        sockaddr_in serverAddr;
        serverAddr.sin_family = AF_INET;
        serverAddr.sin_port = htons(serverPort);
        
        if (inet_pton(AF_INET, serverIP.c_str(), &(serverAddr.sin_addr)) <= 0) {
            std::cerr << "Invalid server IP address" << std::endl;
            closesocket(clientSocket);
            return false;
        }
        
        if (::connect(clientSocket, reinterpret_cast<sockaddr*>(&serverAddr), 
                     sizeof(serverAddr)) == SOCKET_ERROR) {
            std::cerr << "Failed to connect to server" << std::endl;
            closesocket(clientSocket);
            return false;
        }
        
        connected = true;
        std::cout << "Connected to file transfer server " << serverIP << ":" << serverPort << std::endl;
        return true;
    }
    
    void disconnect() {
        if (connected && clientSocket != INVALID_SOCKET) {
            closesocket(clientSocket);
            clientSocket = INVALID_SOCKET;
            connected = false;
        }
    }
    
    bool uploadFile(const std::string& filename) {
        if (!connected) {
            std::cerr << "Not connected to server" << std::endl;
            return false;
        }
        
        std::ifstream file(filename, std::ios::binary);
        if (!file.is_open()) {
            std::cerr << "Cannot open file: " << filename << std::endl;
            return false;
        }
        
        // Send file command
        std::string command = "UPLOAD " + filename;
        if (send(clientSocket, command.c_str(), command.length(), 0) == SOCKET_ERROR) {
            std::cerr << "Failed to send upload command" << std::endl;
            return false;
        }
        
        // Read and send file content
        char buffer[1024];
        size_t totalBytes = 0;
        
        while (file.read(buffer, sizeof(buffer))) {
            size_t bytesRead = file.gcount();
            totalBytes += bytesRead;
            
            if (send(clientSocket, buffer, bytesRead, 0) == SOCKET_ERROR) {
                std::cerr << "Failed to send file data" << std::endl;
                return false;
            }
        }
        
        file.close();
        std::cout << "File uploaded successfully: " << filename 
                  << " (" << totalBytes << " bytes)" << std::endl;
        return true;
    }
    
    bool downloadFile(const std::string& filename) {
        if (!connected) {
            std::cerr << "Not connected to server" << std::endl;
            return false;
        }
        
        // Send download command
        std::string command = "DOWNLOAD " + filename;
        if (send(clientSocket, command.c_str(), command.length(), 0) == SOCKET_ERROR) {
            std::cerr << "Failed to send download command" << std::endl;
            return false;
        }
        
        // Receive file content
        std::ofstream file(filename, std::ios::binary);
        if (!file.is_open()) {
            std::cerr << "Cannot create file: " << filename << std::endl;
            return false;
        }
        
        char buffer[1024];
        size_t totalBytes = 0;
        
        while (true) {
            int bytesReceived = recv(clientSocket, buffer, sizeof(buffer), 0);
            
            if (bytesReceived <= 0) {
                break;
            }
            
            file.write(buffer, bytesReceived);
            totalBytes += bytesReceived;
        }
        
        file.close();
        std::cout << "File downloaded successfully: " << filename 
                  << " (" << totalBytes << " bytes)" << std::endl;
        return true;
    }
    
    bool isConnected() const {
        return connected;
    }
};

int main() {
    std::cout << "=== Network Programming and Sockets - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of network programming\n" << std::endl;
    
    // Example 1: Chat Server
    std::cout << "=== CHAT SERVER ===" << std::endl;
    std::cout << "Starting chat server on port 8080..." << std::endl;
    std::cout << "Use telnet or netcat to connect: telnet localhost 8080" << std::endl;
    
    // Note: In a real application, you'd run the server in a separate thread
    // For this demo, we'll just show the structure
    
    // Example 2: HTTP Server
    std::cout << "\n=== HTTP SERVER ===" << std::endl;
    std::cout << "Starting HTTP server on port 8081..." << std::endl;
    std::cout << "Access http://localhost:8081 in your browser" << std::endl;
    
    // Note: In a real application, you'd run the server in a separate thread
    // For this demo, we'll just show the structure
    
    // Example 3: UDP Echo Server
    std::cout << "\n=== UDP ECHO SERVER ===" << std::endl;
    std::cout << "Starting UDP echo server on port 8082..." << std::endl;
    std::cout << "Use netcat to test: echo 'hello' | nc -u localhost 8082" << std::endl;
    
    // Note: In a real application, you'd run the server in a separate thread
    // For this demo, we'll just show the structure
    
    // Example 4: File Transfer Demonstration
    std::cout << "\n=== FILE TRANSFER DEMONSTRATION ===" << std::endl;
    std::cout << "This demonstrates the structure for file transfer operations" << std::endl;
    std::cout << "In a real application, you would:" << std::endl;
    std::cout << "1. Start the file transfer server" << std::endl;
    std::cout << "2. Connect clients to upload/download files" << std::endl;
    std::cout << "3. Handle multiple concurrent transfers" << std::endl;
    
    // Example 5: Network Programming Concepts
    std::cout << "\n=== NETWORK PROGRAMMING CONCEPTS ===" << std::endl;
    std::cout << "This example demonstrates various network programming concepts:" << std::endl;
    std::cout << "• TCP socket programming for reliable communication" << std::endl;
    std::cout << "• UDP socket programming for fast, connectionless communication" << std::endl;
    std::cout << "• HTTP server implementation for web services" << std::endl;
    std::cout << "• Multi-threaded server architecture" << std::endl;
    std::cout << "• Client-server communication patterns" << std::endl;
    std::cout << "• File transfer protocols" << std::endl;
    std::cout << "• Cross-platform socket programming" << std::endl;
    std::cout << "• Error handling and resource management" << std::endl;
    std::cout << "• Network security considerations" << std::endl;
    
    std::cout << "\n=== PRACTICAL APPLICATIONS ===" << std::endl;
    std::cout << "Real-world applications of network programming:" << std::endl;
    std::cout << "• Web servers and HTTP services" << std::endl;
    std::cout << "• Chat applications and messaging systems" << std::endl;
    std::cout << "• File sharing and transfer services" << std::endl;
    std::cout << "• Remote procedure calls (RPC)" << std::endl;
    std::cout << "• Database connectivity" << std::endl;
    std::cout << "• IoT device communication" << std::endl;
    std::cout << "• Game networking and multiplayer" << std::endl;
    std::cout << "• Cloud service integration" << std::endl;
    
    std::cout << "\n=== BEST PRACTICES ===" << std::endl;
    std::cout << "Key best practices for network programming:" << std::endl;
    std::cout << "• Always validate input data" << std::endl;
    std::cout << "• Implement proper error handling" << std::endl;
    std::cout << "• Use timeouts for network operations" << std::endl;
    std::cout << "• Handle connection failures gracefully" << std::endl;
    std::cout << "• Implement proper resource cleanup" << std::endl;
    std::cout << "• Use secure communication protocols" << std::endl;
    std::cout << "• Consider network latency and bandwidth" << std::endl;
    std::cout << "• Implement connection pooling for scalability" << std::endl;
    std::cout << "• Log network activities for debugging" << std::endl;
    
    std::cout << "\nNetwork programming is essential for building connected applications!" << std::endl;
    
    return 0;
}