# Module 14: Network Programming and Sockets

## Learning Objectives
- Understand networking fundamentals and TCP/IP
- Master socket programming concepts
- Learn about TCP and UDP protocols
- Understand client-server architecture
- Master blocking and non-blocking I/O
- Learn about HTTP and web protocols
- Understand secure networking with SSL/TLS

## Introduction to Network Programming

Network programming enables communication between different computers over a network. C++ provides low-level socket APIs for network communication.

### Basic Socket Concepts
```cpp
#include <iostream>
#include <string>
#include <vector>
#include <cstring>

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

void demonstrateBasicNetworking() {
    std::cout << "=== Basic Networking Concepts ===" << std::endl;
    
    NetworkInitializer init;
    
    // Create a socket
    SOCKET sock = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
    if (sock == INVALID_SOCKET) {
        std::cerr << "Failed to create socket" << std::endl;
        return;
    }
    
    std::cout << "Socket created successfully" << std::endl;
    
    // Socket address structure
    sockaddr_in serverAddr;
    serverAddr.sin_family = AF_INET;
    serverAddr.sin_port = htons(8080);  // Port 8080
    serverAddr.sin_addr.s_addr = inet_addr("127.0.0.1");  // Localhost
    
    // Convert IP address to string
    char ipStr[INET_ADDRSTRLEN];
    inet_ntop(AF_INET, &(serverAddr.sin_addr), ipStr, INET_ADDRSTRLEN);
    std::cout << "IP address: " << ipStr << std::endl;
    std::cout << "Port: " << ntohs(serverAddr.sin_port) << std::endl;
    
    closesocket(sock);
}
```

## TCP Server Implementation

### Basic TCP Server
```cpp
#include <iostream>
#include <thread>
#include <vector>
#include <mutex>
#include <algorithm>

class TCPServer {
private:
    SOCKET serverSocket;
    int port;
    bool running;
    std::vector<std::thread> clientThreads;
    std::mutex clientsMutex;
    
public:
    TCPServer(int port) : port(port), running(false) {}
    
    ~TCPServer() {
        stop();
    }
    
    void start() {
        NetworkInitializer init;
        
        // Create socket
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
            throw std::runtime_error("Failed to start listening");
        }
        
        running = true;
        std::cout << "Server started on port " << port << std::endl;
        
        // Accept connections
        acceptConnections();
    }
    
    void stop() {
        running = false;
        
        // Close server socket
        if (serverSocket != INVALID_SOCKET) {
            closesocket(serverSocket);
            serverSocket = INVALID_SOCKET;
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
            clientThreads.emplace_back([this, clientSocket, clientIP]() {
                handleClient(clientSocket, clientIP);
            });
        }
    }
    
    void handleClient(SOCKET clientSocket, const std::string& clientIP) {
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
            std::cout << "Received from " << clientIP << ": " << buffer << std::endl;
            
            // Process the message and send response
            std::string response = processMessage(buffer);
            
            if (send(clientSocket, response.c_str(), response.length(), 0) == SOCKET_ERROR) {
                std::cerr << "Error sending response to client " << clientIP << std::endl;
                break;
            }
        }
        
        closesocket(clientSocket);
    }
    
    std::string processMessage(const std::string& message) {
        // Simple echo server with timestamp
        auto now = std::chrono::system_clock::now();
        auto timeT = std::chrono::system_clock::to_time_t(now);
        
        std::string response = "Echo: " + message;
        response += " (Time: " + std::string(std::ctime(&timeT));
        response.pop_back();  // Remove newline
        response += ")";
        
        return response;
    }
};

void demonstrateTCPServer() {
    std::cout << "=== TCP Server Demo ===" << std::endl;
    
    try {
        TCPServer server(8080);
        server.start();
        
        // Run for 30 seconds
        std::this_thread::sleep_for(std::chrono::seconds(30));
        
        server.stop();
    }
    catch (const std::exception& e) {
        std::cerr << "Server error: " << e.what() << std::endl;
    }
}
```

## TCP Client Implementation

### Basic TCP Client
```cpp
#include <iostream>
#include <string>

class TCPClient {
private:
    SOCKET clientSocket;
    std::string serverIP;
    int serverPort;
    bool connected;
    
public:
    TCPClient(const std::string& ip, int port) 
        : serverIP(ip), serverPort(port), connected(false) {}
    
    ~TCPClient() {
        disconnect();
    }
    
    bool connect() {
        NetworkInitializer init;
        
        // Create socket
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

void demonstrateTCPClient() {
    std::cout << "=== TCP Client Demo ===" << std::endl;
    
    TCPClient client("127.0.0.1", 8080);
    
    if (client.connect()) {
        // Send some messages
        client.sendMessage("Hello, Server!");
        std::string response = client.receiveMessage();
        
        client.sendMessage("How are you?");
        response = client.receiveMessage();
        
        client.sendMessage("Goodbye!");
        response = client.receiveMessage();
        
        client.disconnect();
    }
}

// Interactive client
void interactiveClient() {
    std::cout << "=== Interactive TCP Client ===" << std::endl;
    std::cout << "Connect to server (localhost:8080)" << std::endl;
    
    TCPClient client("127.0.0.1", 8080);
    
    if (!client.connect()) {
        std::cout << "Failed to connect to server. Make sure server is running." << std::endl;
        return;
    }
    
    std::string message;
    std::cout << "Enter messages to send (type 'quit' to exit):" << std::endl;
    
    while (true) {
        std::cout << "> ";
        std::getline(std::cin, message);
        
        if (message == "quit") {
            break;
        }
        
        if (client.sendMessage(message)) {
            std::string response = client.receiveMessage();
            if (response.empty()) {
                break;  // Server disconnected
            }
        } else {
            break;  // Send failed
        }
    }
    
    client.disconnect();
}
```

## UDP Programming

### UDP Server and Client
```cpp
#include <iostream>

class UDPServer {
private:
    SOCKET serverSocket;
    int port;
    bool running;
    
public:
    UDPServer(int port) : port(port), running(false) {}
    
    ~UDPServer() {
        stop();
    }
    
    void start() {
        NetworkInitializer init;
        
        // Create UDP socket
        serverSocket = socket(AF_INET, SOCK_DGRAM, IPPROTO_UDP);
        if (serverSocket == INVALID_SOCKET) {
            throw std::runtime_error("Failed to create UDP socket");
        }
        
        // Bind socket
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
        std::cout << "UDP Server started on port " << port << std::endl;
        
        // Receive messages
        receiveMessages();
    }
    
    void stop() {
        running = false;
        if (serverSocket != INVALID_SOCKET) {
            closesocket(serverSocket);
            serverSocket = INVALID_SOCKET;
        }
    }
    
private:
    void receiveMessages() {
        char buffer[1024];
        sockaddr_in clientAddr;
        socklen_t clientAddrLen = sizeof(clientAddr);
        
        while (running) {
            // Receive message
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
            
            // Get client IP
            char clientIP[INET_ADDRSTRLEN];
            inet_ntop(AF_INET, &(clientAddr.sin_addr), clientIP, INET_ADDRSTRLEN);
            
            std::cout << "UDP message from " << clientIP << ":" 
                      << ntohs(clientAddr.sin_port) << ": " << buffer << std::endl;
            
            // Send response
            std::string response = "UDP Echo: " + std::string(buffer);
            sendto(serverSocket, response.c_str(), response.length(), 0,
                   reinterpret_cast<sockaddr*>(&clientAddr), clientAddrLen);
        }
    }
};

class UDPClient {
private:
    SOCKET clientSocket;
    std::string serverIP;
    int serverPort;
    
public:
    UDPClient(const std::string& ip, int port) : serverIP(ip), serverPort(port) {}
    
    bool connect() {
        NetworkInitializer init;
        
        // Create UDP socket
        clientSocket = socket(AF_INET, SOCK_DGRAM, IPPROTO_UDP);
        if (clientSocket == INVALID_SOCKET) {
            std::cerr << "Failed to create UDP client socket" << std::endl;
            return false;
        }
        
        std::cout << "UDP client created" << std::endl;
        return true;
    }
    
    void sendMessage(const std::string& message) {
        sockaddr_in serverAddr;
        serverAddr.sin_family = AF_INET;
        serverAddr.sin_port = htons(serverPort);
        inet_pton(AF_INET, serverIP.c_str(), &(serverAddr.sin_addr));
        
        if (sendto(clientSocket, message.c_str(), message.length(), 0,
                  reinterpret_cast<sockaddr*>(&serverAddr), sizeof(serverAddr)) == SOCKET_ERROR) {
            std::cerr << "Failed to send UDP message" << std::endl;
            return;
        }
        
        std::cout << "Sent UDP: " << message << std::endl;
    }
    
    std::string receiveMessage() {
        char buffer[1024];
        sockaddr_in fromAddr;
        socklen_t fromAddrLen = sizeof(fromAddr);
        
        int bytesReceived = recvfrom(clientSocket, buffer, sizeof(buffer) - 1, 0,
                                    reinterpret_cast<sockaddr*>(&fromAddr), 
                                    &fromAddrLen);
        
        if (bytesReceived <= 0) {
            std::cerr << "Error receiving UDP message" << std::endl;
            return "";
        }
        
        buffer[bytesReceived] = '\0';
        std::string message(buffer);
        std::cout << "Received UDP: " << message << std::endl;
        
        return message;
    }
    
    ~UDPClient() {
        if (clientSocket != INVALID_SOCKET) {
            closesocket(clientSocket);
        }
    }
};

void demonstrateUDP() {
    std::cout << "=== UDP Demo ===" << std::endl;
    
    // Start UDP server in a separate thread
    std::thread serverThread([]() {
        try {
            UDPServer server(9090);
            server.start();
        } catch (const std::exception& e) {
            std::cerr << "UDP Server error: " << e.what() << std::endl;
        }
    });
    
    // Give server time to start
    std::this_thread::sleep_for(std::chrono::milliseconds(100));
    
    // Create UDP client and send messages
    UDPClient client("127.0.0.1", 9090);
    if (client.connect()) {
        client.sendMessage("Hello UDP Server!");
        std::string response = client.receiveMessage();
        
        client.sendMessage("This is a test");
        response = client.receiveMessage();
    }
    
    // Let server run for a bit
    std::this_thread::sleep_for(std::chrono::seconds(5));
    
    // Note: In a real application, you'd need proper shutdown mechanism
    serverThread.detach();
}
```

## Non-Blocking I/O

### Non-Blocking Socket Operations
```cpp
#include <iostream>
#include <vector>
#include <algorithm>

class NonBlockingServer {
private:
    SOCKET serverSocket;
    std::vector<SOCKET> clientSockets;
    int port;
    bool running;
    
public:
    NonBlockingServer(int port) : port(port), running(false) {}
    
    void start() {
        NetworkInitializer init;
        
        // Create server socket
        serverSocket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
        if (serverSocket == INVALID_SOCKET) {
            throw std::runtime_error("Failed to create server socket");
        }
        
        // Set non-blocking mode
        setNonBlocking(serverSocket);
        
        // Bind and listen
        sockaddr_in serverAddr;
        serverAddr.sin_family = AF_INET;
        serverAddr.sin_addr.s_addr = INADDR_ANY;
        serverAddr.sin_port = htons(port);
        
        if (bind(serverSocket, reinterpret_cast<sockaddr*>(&serverAddr), 
                 sizeof(serverAddr)) == SOCKET_ERROR) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to bind socket");
        }
        
        if (listen(serverSocket, SOMAXCONN) == SOCKET_ERROR) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to listen");
        }
        
        running = true;
        std::cout << "Non-blocking server started on port " << port << std::endl;
        
        runEventLoop();
    }
    
    void stop() {
        running = false;
        
        // Close all client sockets
        for (SOCKET clientSocket : clientSockets) {
            closesocket(clientSocket);
        }
        clientSockets.clear();
        
        if (serverSocket != INVALID_SOCKET) {
            closesocket(serverSocket);
        }
    }
    
private:
    void setNonBlocking(SOCKET socket) {
#ifdef _WIN32
        u_long mode = 1;  // 1 = non-blocking
        ioctlsocket(socket, FIONBIO, &mode);
#else
        int flags = fcntl(socket, F_GETFL, 0);
        fcntl(socket, F_SETFL, flags | O_NONBLOCK);
#endif
    }
    
    void runEventLoop() {
        char buffer[1024];
        
        while (running) {
            // Accept new connections
            sockaddr_in clientAddr;
            socklen_t clientAddrLen = sizeof(clientAddr);
            
            SOCKET newClient = accept(serverSocket, 
                                     reinterpret_cast<sockaddr*>(&clientAddr), 
                                     &clientAddrLen);
            
            if (newClient != INVALID_SOCKET) {
                setNonBlocking(newClient);
                clientSockets.push_back(newClient);
                
                char clientIP[INET_ADDRSTRLEN];
                inet_ntop(AF_INET, &(clientAddr.sin_addr), clientIP, INET_ADDRSTRLEN);
                std::cout << "New client connected: " << clientIP << std::endl;
            }
            
            // Handle existing clients
            auto it = clientSockets.begin();
            while (it != clientSockets.end()) {
                SOCKET clientSocket = *it;
                
                // Receive data
                int bytesReceived = recv(clientSocket, buffer, sizeof(buffer) - 1, 0);
                
                if (bytesReceived > 0) {
                    buffer[bytesReceived] = '\0';
                    std::cout << "Received: " << buffer << std::endl;
                    
                    // Echo back
                    send(clientSocket, buffer, bytesReceived, 0);
                    ++it;
                } else if (bytesReceived == 0) {
                    // Client disconnected
                    std::cout << "Client disconnected" << std::endl;
                    closesocket(clientSocket);
                    it = clientSockets.erase(it);
                } else {
#ifdef _WIN32
                    if (WSAGetLastError() == WSAEWOULDBLOCK) {
                        // No data available
                        ++it;
                    } else {
                        // Error occurred
                        std::cerr << "Error receiving from client" << std::endl;
                        closesocket(clientSocket);
                        it = clientSockets.erase(it);
                    }
#else
                    if (errno == EAGAIN || errno == EWOULDBLOCK) {
                        // No data available
                        ++it;
                    } else {
                        // Error occurred
                        std::cerr << "Error receiving from client" << std::endl;
                        closesocket(clientSocket);
                        it = clientSockets.erase(it);
                    }
#endif
                }
            }
            
            // Small delay to prevent busy waiting
            std::this_thread::sleep_for(std::chrono::milliseconds(10));
        }
    }
};

void demonstrateNonBlocking() {
    std::cout << "=== Non-Blocking I/O Demo ===" << std::endl;
    
    try {
        NonBlockingServer server(8081);
        server.start();
    } catch (const std::exception& e) {
        std::cerr << "Non-blocking server error: " << e.what() << std::endl;
    }
}
```

## HTTP Server Implementation

### Simple HTTP Server
```cpp
#include <iostream>
#include <sstream>
#include <map>
#include <fstream>

class HTTPServer {
private:
    SOCKET serverSocket;
    int port;
    bool running;
    std::map<std::string, std::string> mimeTypes;
    
public:
    HTTPServer(int port) : port(port), running(false) {
        initializeMimeTypes();
    }
    
    void start() {
        NetworkInitializer init;
        
        // Create socket
        serverSocket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
        if (serverSocket == INVALID_SOCKET) {
            throw std::runtime_error("Failed to create HTTP server socket");
        }
        
        // Bind and listen
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
        std::cout << "HTTP Server started on port " << port << std::endl;
        
        handleConnections();
    }
    
    void stop() {
        running = false;
        if (serverSocket != INVALID_SOCKET) {
            closesocket(serverSocket);
        }
    }
    
private:
    void initializeMimeTypes() {
        mimeTypes[".html"] = "text/html";
        mimeTypes[".css"] = "text/css";
        mimeTypes[".js"] = "application/javascript";
        mimeTypes[".json"] = "application/json";
        mimeTypes[".png"] = "image/png";
        mimeTypes[".jpg"] = "image/jpeg";
        mimeTypes[".gif"] = "image/gif";
        mimeTypes[".txt"] = "text/plain";
    }
    
    std::string getMimeType(const std::string& filename) {
        size_t dotPos = filename.find_last_of('.');
        if (dotPos == std::string::npos) {
            return "text/plain";
        }
        
        std::string extension = filename.substr(dotPos);
        auto it = mimeTypes.find(extension);
        return (it != mimeTypes.end()) ? it->second : "text/plain";
    }
    
    void handleConnections() {
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
        
        // Handle different methods
        if (method == "GET") {
            handleGETRequest(clientSocket, path);
        } else if (method == "POST") {
            handlePOSTRequest(clientSocket, path, request);
        } else {
            sendErrorResponse(clientSocket, 501, "Not Implemented");
        }
        
        closesocket(clientSocket);
    }
    
    void handleGETRequest(SOCKET clientSocket, const std::string& path) {
        if (path == "/" || path == "/index.html") {
            sendHTMLResponse(clientSocket, generateHomePage());
        } else if (path == "/api/time") {
            sendJSONResponse(clientSocket, getCurrentTimeJSON());
        } else if (path == "/api/status") {
            sendJSONResponse(clientSocket, getStatusJSON());
        } else {
            sendErrorResponse(clientSocket, 404, "Not Found");
        }
    }
    
    void handlePOSTRequest(SOCKET clientSocket, const std::string& path, const std::string& request) {
        // Parse POST data (simplified)
        size_t bodyStart = request.find("\r\n\r\n");
        if (bodyStart != std::string::npos) {
            std::string body = request.substr(bodyStart + 4);
            std::cout << "POST data: " << body << std::endl;
        }
        
        sendJSONResponse(clientSocket, "{\"status\": \"POST received\"}");
    }
    
    void sendHTMLResponse(SOCKET clientSocket, const std::string& html) {
        std::string response = "HTTP/1.1 200 OK\r\n";
        response += "Content-Type: text/html\r\n";
        response += "Content-Length: " + std::to_string(html.length()) + "\r\n";
        response += "Connection: close\r\n";
        response += "\r\n" + html;
        
        send(clientSocket, response.c_str(), response.length(), 0);
    }
    
    void sendJSONResponse(SOCKET clientSocket, const std::string& json) {
        std::string response = "HTTP/1.1 200 OK\r\n";
        response += "Content-Type: application/json\r\n";
        response += "Content-Length: " + std::to_string(json.length()) + "\r\n";
        response += "Connection: close\r\n";
        response += "Access-Control-Allow-Origin: *\r\n";
        response += "\r\n" + json;
        
        send(clientSocket, response.c_str(), response.length(), 0);
    }
    
    void sendErrorResponse(SOCKET clientSocket, int code, const std::string& message) {
        std::string html = "<html><body><h1>" + std::to_string(code) + " - " + message + "</h1></body></html>";
        
        std::string response = "HTTP/1.1 " + std::to_string(code) + " " + message + "\r\n";
        response += "Content-Type: text/html\r\n";
        response += "Content-Length: " + std::to_string(html.length()) + "\r\n";
        response += "Connection: close\r\n";
        response += "\r\n" + html;
        
        send(clientSocket, response.c_str(), response.length(), 0);
    }
    
    std::string generateHomePage() {
        return R"(
<!DOCTYPE html>
<html>
<head>
    <title>C++ HTTP Server</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .button { background: #007bff; color: white; padding: 10px 20px; 
                 border: none; cursor: pointer; margin: 5px; }
        .response { background: #f8f9fa; padding: 10px; margin: 10px 0; 
                   border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>C++ HTTP Server</h1>
        <p>Welcome to the C++ HTTP server!</p>
        
        <h2>API Endpoints</h2>
        <button class="button" onclick="getTime()">Get Current Time</button>
        <button class="button" onclick="getStatus()">Get Server Status</button>
        
        <div id="response" class="response" style="display: none;"></div>
    </div>
    
    <script>
        async function getTime() {
            const response = await fetch('/api/time');
            const data = await response.json();
            document.getElementById('response').style.display = 'block';
            document.getElementById('response').innerHTML = 
                '<h3>Current Time</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        }
        
        async function getStatus() {
            const response = await fetch('/api/status');
            const data = await response.json();
            document.getElementById('response').style.display = 'block';
            document.getElementById('response').innerHTML = 
                '<h3>Server Status</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        }
    </script>
</body>
</html>
        )";
    }
    
    std::string getCurrentTimeJSON() {
        auto now = std::chrono::system_clock::now();
        auto timeT = std::chrono::system_clock::to_time_t(now);
        
        std::stringstream ss;
        ss << "{\"timestamp\": " << timeT 
           << ", \"time\": \"" << std::ctime(&timeT);
        std::string timeStr = ss.str();
        timeStr.pop_back();  // Remove newline
        timeStr += "\"}";
        
        return timeStr;
    }
    
    std::string getStatusJSON() {
        return R"({
            "status": "running",
            "server": "C++ HTTP Server",
            "version": "1.0.0",
            "endpoints": ["/", "/api/time", "/api/status"]
        })";
    }
};

void demonstrateHTTPServer() {
    std::cout << "=== HTTP Server Demo ===" << std::endl;
    
    try {
        HTTPServer server(8082);
        server.start();
    } catch (const std::exception& e) {
        std::cerr << "HTTP Server error: " << e.what() << std::endl;
    }
}
```

## Complete Example: Chat Application

### Multi-Client Chat Server
```cpp
#include <iostream>
#include <thread>
#include <vector>
#include <mutex>
#include <algorithm>
#include <sstream>

class ChatServer {
private:
    SOCKET serverSocket;
    int port;
    bool running;
    std::vector<SOCKET> clientSockets;
    std::mutex clientsMutex;
    std::vector<std::string> messageHistory;
    std::mutex historyMutex;
    
public:
    ChatServer(int port) : port(port), running(false) {}
    
    ~ChatServer() {
        stop();
    }
    
    void start() {
        NetworkInitializer init;
        
        serverSocket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
        if (serverSocket == INVALID_SOCKET) {
            throw std::runtime_error("Failed to create chat server socket");
        }
        
        sockaddr_in serverAddr;
        serverAddr.sin_family = AF_INET;
        serverAddr.sin_addr.s_addr = INADDR_ANY;
        serverAddr.sin_port = htons(port);
        
        if (bind(serverSocket, reinterpret_cast<sockaddr*>(&serverAddr), 
                 sizeof(serverAddr)) == SOCKET_ERROR) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to bind chat server socket");
        }
        
        if (listen(serverSocket, SOMAXCONN) == SOCKET_ERROR) {
            closesocket(serverSocket);
            throw std::runtime_error("Failed to listen for chat connections");
        }
        
        running = true;
        std::cout << "Chat server started on port " << port << std::endl;
        
        acceptConnections();
    }
    
    void stop() {
        running = false;
        
        // Close all client connections
        std::lock_guard<std::mutex> lock(clientsMutex);
        for (SOCKET clientSocket : clientSockets) {
            closesocket(clientSocket);
        }
        clientSockets.clear();
        
        if (serverSocket != INVALID_SOCKET) {
            closesocket(serverSocket);
        }
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
                    std::cerr << "Failed to accept chat client connection" << std::endl;
                }
                continue;
            }
            
            char clientIP[INET_ADDRSTRLEN];
            inet_ntop(AF_INET, &(clientAddr.sin_addr), clientIP, INET_ADDRSTRLEN);
            
            std::cout << "Chat client connected from " << clientIP << std::endl;
            
            // Send welcome message and history
            sendWelcomeMessage(clientSocket);
            sendHistory(clientSocket);
            
            // Handle client in separate thread
            std::thread([this, clientSocket, clientIP]() {
                handleChatClient(clientSocket, clientIP);
            }).detach();
        }
    }
    
    void sendWelcomeMessage(SOCKET clientSocket) {
        std::string welcome = "=== Welcome to C++ Chat Server ===\n";
        welcome += "Type your messages and press Enter to send.\n";
        welcome += "Type '/quit' to exit.\n";
        welcome += "Type '/history' to see message history.\n\n";
        
        send(clientSocket, welcome.c_str(), welcome.length(), 0);
    }
    
    void sendHistory(SOCKET clientSocket) {
        std::lock_guard<std::mutex> lock(historyMutex);
        
        if (messageHistory.empty()) {
            std::string noHistory = "No previous messages.\n\n";
            send(clientSocket, noHistory.c_str(), noHistory.length(), 0);
        } else {
            std::string historyHeader = "=== Message History ===\n";
            send(clientSocket, historyHeader.c_str(), historyHeader.length(), 0);
            
            for (const auto& message : messageHistory) {
                send(clientSocket, message.c_str(), message.length(), 0);
            }
            
            std::string historyFooter = "=== End of History ===\n\n";
            send(clientSocket, historyFooter.c_str(), historyFooter.length(), 0);
        }
    }
    
    void handleChatClient(SOCKET clientSocket, const std::string& clientIP) {
        char buffer[1024];
        
        // Add client to list
        {
            std::lock_guard<std::mutex> lock(clientsMutex);
            clientSockets.push_back(clientSocket);
        }
        
        // Broadcast join message
        broadcastMessage("User " + clientIP + " joined the chat.\n", clientSocket);
        
        while (running) {
            int bytesReceived = recv(clientSocket, buffer, sizeof(buffer) - 1, 0);
            
            if (bytesReceived <= 0) {
                break;
            }
            
            buffer[bytesReceived] = '\0';
            std::string message(buffer);
            
            // Remove newline
            if (!message.empty() && message.back() == '\n') {
                message.pop_back();
            }
            
            // Handle commands
            if (message == "/quit") {
                break;
            } else if (message == "/history") {
                sendHistory(clientSocket);
                continue;
            } else if (message.empty()) {
                continue;
            }
            
            // Format and broadcast message
            std::string formattedMessage = "[" + clientIP + "]: " + message + "\n";
            
            // Add to history
            {
                std::lock_guard<std::mutex> lock(historyMutex);
                messageHistory.push_back(formattedMessage);
                
                // Keep only last 100 messages
                if (messageHistory.size() > 100) {
                    messageHistory.erase(messageHistory.begin());
                }
            }
            
            // Broadcast to all clients
            broadcastMessage(formattedMessage);
        }
        
        // Remove client and broadcast leave message
        {
            std::lock_guard<std::mutex> lock(clientsMutex);
            clientSockets.erase(
                std::remove(clientSockets.begin(), clientSockets.end(), clientSocket),
                clientSockets.end()
            );
        }
        
        broadcastMessage("User " + clientIP + " left the chat.\n", clientSocket);
        
        std::cout << "Chat client " << clientIP << " disconnected" << std::endl;
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

void demonstrateChatServer() {
    std::cout << "=== Chat Server Demo ===" << std::endl;
    std::cout << "Starting chat server on port 8083..." << std::endl;
    std::cout << "Use telnet or netcat to connect: telnet localhost 8083" << std::endl;
    
    try {
        ChatServer server(8083);
        server.start();
    } catch (const std::exception& e) {
        std::cerr << "Chat server error: " << e.what() << std::endl;
    }
}

int main() {
    std::cout << "=== Network Programming Examples ===" << std::endl;
    std::cout << "Choose an example to run:" << std::endl;
    std::cout << "1. Basic Networking Concepts" << std::endl;
    std::cout << "2. TCP Server" << std::endl;
    std::cout << "3. TCP Client" << std::endl;
    std::cout << "4. UDP Programming" << std::endl;
    std::cout << "5. Non-Blocking I/O" << std::endl;
    std::cout << "6. HTTP Server" << std::endl;
    std::cout << "7. Chat Server" << std::endl;
    
    int choice;
    std::cout << "Enter choice (1-7): ";
    std::cin >> choice;
    
    switch (choice) {
        case 1:
            demonstrateBasicNetworking();
            break;
        case 2:
            demonstrateTCPServer();
            break;
        case 3:
            demonstrateTCPClient();
            break;
        case 4:
            demonstrateUDP();
            break;
        case 5:
            demonstrateNonBlocking();
            break;
        case 6:
            demonstrateHTTPServer();
            break;
        case 7:
            demonstrateChatServer();
            break;
        default:
            std::cout << "Invalid choice. Running basic networking demo..." << std::endl;
            demonstrateBasicNetworking();
    }
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Multi-Protocol Server
Create a server that handles multiple protocols:
- TCP and UDP support
- HTTP and WebSocket
- Protocol detection and routing
- Unified client management

### Exercise 2: File Transfer Application
Build a file transfer system:
- Client-server architecture
- Resumable transfers
- Progress tracking
- Error handling and recovery

### Exercise 3: Real-time Game Server
Implement a simple multiplayer game server:
- Real-time synchronization
- Client state management
- Lag compensation
- Room/lobby system

### Exercise 4: Network Monitoring Tool
Create a network monitoring application:
- Bandwidth usage tracking
- Connection monitoring
- Protocol analysis
- Performance metrics

## Key Takeaways
- Socket programming enables low-level network communication
- TCP provides reliable, connection-oriented communication
- UDP offers fast, connectionless messaging
- Non-blocking I/O enables concurrent operations
- HTTP servers handle web requests and responses
- Proper error handling is crucial for network applications
- Cross-platform compatibility requires platform-specific code
- Security considerations are important in network programming

## Next Module
In the final module, we'll explore advanced C++ features and modern C++ best practices.