import java.io.*;
import java.net.*;
import java.util.concurrent.*;
import java.util.concurrent.atomic.AtomicInteger;

public class NetworkingExample {
    public static void main(String[] args) {
        // TCP Socket Programming
        System.out.println("=== TCP Socket Programming ===");
        demonstrateTCPSockets();
        
        // UDP Socket Programming
        System.out.println("\n=== UDP Socket Programming ===");
        demonstrateUDPSockets();
        
        // HTTP Client
        System.out.println("\n=== HTTP Client ===");
        demonstrateHTTPClient();
        
        // HTTP Server
        System.out.println("\n=== HTTP Server ===");
        demonstrateHTTPServer();
        
        // URL Connection
        System.out.println("\n=== URL Connection ===");
        demonstrateURLConnection();
        
        // Multithreaded Server
        System.out.println("\n=== Multithreaded Server ===");
        demonstrateMultithreadedServer();
        
        // Performance Considerations
        System.out.println("\n=== Performance Considerations ===");
        performanceComparison();
    }
    
    public static void demonstrateTCPSockets() {
        // Start TCP server in background
        Thread serverThread = new Thread(() -> startTCPServer(8080));
        serverThread.start();
        
        // Give server time to start
        try {
            Thread.sleep(1000);
        } catch (InterruptedException e) {
            System.err.println("Sleep interrupted: " + e.getMessage());
        }
        
        // TCP Client
        try (Socket socket = new Socket("localhost", 8080);
             PrintWriter out = new PrintWriter(socket.getOutputStream(), true);
             BufferedReader in = new BufferedReader(new InputStreamReader(socket.getInputStream()))) {
            
            System.out.println("TCP Client connected to server");
            
            // Send messages
            out.println("Hello TCP Server!");
            out.println("This is a test message");
            out.println("bye");
            
            // Receive responses
            String response;
            while ((response = in.readLine()) != null) {
                System.out.println("Server response: " + response);
                if (response.contains("Goodbye")) {
                    break;
                }
            }
            
        } catch (IOException e) {
            System.err.println("TCP Client error: " + e.getMessage());
        }
    }
    
    public static void startTCPServer(int port) {
        try (ServerSocket serverSocket = new ServerSocket(port)) {
            System.out.println("TCP Server started on port " + port);
            
            while (true) {
                try (Socket clientSocket = serverSocket.accept();
                     PrintWriter out = new PrintWriter(clientSocket.getOutputStream(), true);
                     BufferedReader in = new BufferedReader(new InputStreamReader(clientSocket.getInputStream()))) {
                    
                    System.out.println("TCP Client connected: " + clientSocket.getInetAddress());
                    
                    String inputLine;
                    while ((inputLine = in.readLine()) != null) {
                        System.out.println("Received: " + inputLine);
                        
                        if (inputLine.contains("bye")) {
                            out.println("Goodbye! Connection closing.");
                            break;
                        } else {
                            out.println("Echo: " + inputLine);
                        }
                    }
                    
                } catch (IOException e) {
                    System.err.println("Client handling error: " + e.getMessage());
                }
            }
            
        } catch (IOException e) {
            System.err.println("TCP Server error: " + e.getMessage());
        }
    }
    
    public static void demonstrateUDPSockets() {
        // Start UDP server in background
        Thread serverThread = new Thread(() -> startUDPServer(8081));
        serverThread.start();
        
        // Give server time to start
        try {
            Thread.sleep(1000);
        } catch (InterruptedException e) {
            System.err.println("Sleep interrupted: " + e.getMessage());
        }
        
        // UDP Client
        try (DatagramSocket socket = new DatagramSocket()) {
            byte[] sendData = "Hello UDP Server!".getBytes();
            InetAddress serverAddress = InetAddress.getByName("localhost");
            
            // Send packet
            DatagramPacket sendPacket = new DatagramPacket(
                sendData, sendData.length, serverAddress, 8081);
            socket.send(sendPacket);
            System.out.println("UDP Client sent message to server");
            
            // Receive response
            byte[] receiveData = new byte[1024];
            DatagramPacket receivePacket = new DatagramPacket(receiveData, receiveData.length);
            socket.setSoTimeout(5000); // 5 second timeout
            
            try {
                socket.receive(receivePacket);
                String response = new String(receivePacket.getData(), 0, receivePacket.getLength());
                System.out.println("UDP Server response: " + response);
            } catch (SocketTimeoutException e) {
                System.out.println("UDP Server response timeout");
            }
            
        } catch (IOException e) {
            System.err.println("UDP Client error: " + e.getMessage());
        }
    }
    
    public static void startUDPServer(int port) {
        try (DatagramSocket socket = new DatagramSocket(port)) {
            System.out.println("UDP Server started on port " + port);
            byte[] receiveData = new byte[1024];
            
            while (true) {
                DatagramPacket receivePacket = new DatagramPacket(receiveData, receiveData.length);
                socket.receive(receivePacket);
                
                String message = new String(receivePacket.getData(), 0, receivePacket.getLength());
                System.out.println("UDP Server received: " + message);
                
                // Send response
                String response = "Echo: " + message;
                byte[] sendData = response.getBytes();
                DatagramPacket sendPacket = new DatagramPacket(
                    sendData, sendData.length, 
                    receivePacket.getAddress(), receivePacket.getPort());
                socket.send(sendPacket);
            }
            
        } catch (IOException e) {
            System.err.println("UDP Server error: " + e.getMessage());
        }
    }
    
    public static void demonstrateHTTPClient() {
        try {
            URL url = new URL("https://httpbin.org/get");
            HttpURLConnection connection = (HttpURLConnection) url.openConnection();
            
            // Set request properties
            connection.setRequestMethod("GET");
            connection.setRequestProperty("User-Agent", "Java HTTP Client");
            connection.setConnectTimeout(5000);
            connection.setReadTimeout(5000);
            
            // Get response
            int responseCode = connection.getResponseCode();
            System.out.println("HTTP Response Code: " + responseCode);
            
            if (responseCode == HttpURLConnection.HTTP_OK) {
                try (BufferedReader in = new BufferedReader(
                        new InputStreamReader(connection.getInputStream()))) {
                    
                    String inputLine;
                    StringBuilder response = new StringBuilder();
                    
                    while ((inputLine = in.readLine()) != null) {
                        response.append(inputLine);
                    }
                    
                    System.out.println("HTTP Response: " + response.toString());
                }
            }
            
        } catch (IOException e) {
            System.err.println("HTTP Client error: " + e.getMessage());
        }
    }
    
    public static void demonstrateHTTPServer() {
        // Start simple HTTP server in background
        Thread serverThread = new Thread(() -> startHTTPServer(8082));
        serverThread.start();
        
        // Give server time to start
        try {
            Thread.sleep(1000);
        } catch (InterruptedException e) {
            System.err.println("Sleep interrupted: " + e.getMessage());
        }
        
        // Test the HTTP server
        try {
            URL url = new URL("http://localhost:8082");
            HttpURLConnection connection = (HttpURLConnection) url.openConnection();
            connection.setRequestMethod("GET");
            
            int responseCode = connection.getResponseCode();
            System.out.println("Test HTTP Server Response Code: " + responseCode);
            
            if (responseCode == HttpURLConnection.HTTP_OK) {
                try (BufferedReader in = new BufferedReader(
                        new InputStreamReader(connection.getInputStream()))) {
                    
                    String response = in.readLine();
                    System.out.println("Test HTTP Server Response: " + response);
                }
            }
            
        } catch (IOException e) {
            System.err.println("HTTP Server test error: " + e.getMessage());
        }
    }
    
    public static void startHTTPServer(int port) {
        try (ServerSocket serverSocket = new ServerSocket(port)) {
            System.out.println("HTTP Server started on port " + port);
            
            while (true) {
                try (Socket clientSocket = serverSocket.accept();
                     BufferedReader in = new BufferedReader(new InputStreamReader(clientSocket.getInputStream()));
                     PrintWriter out = new PrintWriter(clientSocket.getOutputStream())) {
                    
                    String requestLine = in.readLine();
                    System.out.println("HTTP Request: " + requestLine);
                    
                    // Simple HTTP response
                    String httpResponse = "HTTP/1.1 200 OK\r\n" +
                            "Content-Type: text/plain\r\n" +
                            "Content-Length: 13\r\n" +
                            "\r\n" +
                            "Hello, World!";
                    
                    out.print(httpResponse);
                    out.flush();
                    
                } catch (IOException e) {
                    System.err.println("HTTP request handling error: " + e.getMessage());
                }
            }
            
        } catch (IOException e) {
            System.err.println("HTTP Server error: " + e.getMessage());
        }
    }
    
    public static void demonstrateURLConnection() {
        try {
            URL url = new URL("https://jsonplaceholder.typicode.com/posts/1");
            URLConnection connection = url.openConnection();
            
            // Read data
            try (BufferedReader in = new BufferedReader(
                    new InputStreamReader(connection.getInputStream()))) {
                
                String inputLine;
                StringBuilder content = new StringBuilder();
                
                while ((inputLine = in.readLine()) != null) {
                    content.append(inputLine);
                }
                
                System.out.println("URL Content: " + content.toString());
            }
            
            // Get connection info
            System.out.println("Content Type: " + connection.getContentType());
            System.out.println("Content Length: " + connection.getContentLength());
            System.out.println("Last Modified: " + connection.getLastModified());
            
        } catch (IOException e) {
            System.err.println("URL Connection error: " + e.getMessage());
        }
    }
    
    public static void demonstrateMultithreadedServer() {
        // Start multithreaded server
        Thread serverThread = new Thread(() -> startMultithreadedServer(8083));
        serverThread.start();
        
        // Give server time to start
        try {
            Thread.sleep(1000);
        } catch (InterruptedException e) {
            System.err.println("Sleep interrupted: " + e.getMessage());
        }
        
        // Create multiple clients to test
        ExecutorService executor = Executors.newFixedThreadPool(5);
        for (int i = 0; i < 5; i++) {
            final int clientId = i;
            executor.submit(() -> {
                try (Socket socket = new Socket("localhost", 8083);
                     PrintWriter out = new PrintWriter(socket.getOutputStream(), true);
                     BufferedReader in = new BufferedReader(new InputStreamReader(socket.getInputStream()))) {
                    
                    out.println("Client " + clientId + " message");
                    String response = in.readLine();
                    System.out.println("Client " + clientId + " received: " + response);
                    
                } catch (IOException e) {
                    System.err.println("Client " + clientId + " error: " + e.getMessage());
                }
            });
        }
        
        executor.shutdown();
        try {
            executor.awaitTermination(5, TimeUnit.SECONDS);
        } catch (InterruptedException e) {
            System.err.println("Executor shutdown interrupted: " + e.getMessage());
        }
    }
    
    public static void startMultithreadedServer(int port) {
        AtomicInteger clientCount = new AtomicInteger(0);
        ExecutorService threadPool = Executors.newFixedThreadPool(10);
        
        try (ServerSocket serverSocket = new ServerSocket(port)) {
            System.out.println("Multithreaded Server started on port " + port);
            
            while (true) {
                try {
                    Socket clientSocket = serverSocket.accept();
                    int clientId = clientCount.incrementAndGet();
                    
                    threadPool.submit(() -> handleClient(clientSocket, clientId));
                    
                } catch (IOException e) {
                    System.err.println("Client acceptance error: " + e.getMessage());
                }
            }
            
        } catch (IOException e) {
            System.err.println("Multithreaded Server error: " + e.getMessage());
        }
    }
    
    private static void handleClient(Socket clientSocket, int clientId) {
        try (PrintWriter out = new PrintWriter(clientSocket.getOutputStream(), true);
             BufferedReader in = new BufferedReader(new InputStreamReader(clientSocket.getInputStream()))) {
            
            System.out.println("Handling client " + clientId + " from " + clientSocket.getInetAddress());
            
            String inputLine;
            while ((inputLine = in.readLine()) != null) {
                System.out.println("Client " + clientId + " says: " + inputLine);
                out.println("Server to client " + clientId + ": " + inputLine);
                
                if (inputLine.contains("bye")) {
                    break;
                }
            }
            
        } catch (IOException e) {
            System.err.println("Client " + clientId + " handling error: " + e.getMessage());
        }
    }
    
    public static void performanceComparison() {
        final int REQUESTS = 100;
        
        // Test TCP performance
        long startTime = System.nanoTime();
        
        for (int i = 0; i < REQUESTS; i++) {
            try {
                Socket socket = new Socket("httpbin.org", 80);
                socket.close();
            } catch (IOException e) {
                // Connection failed, but we're measuring overhead
            }
        }
        
        long endTime = System.nanoTime();
        System.out.println("TCP socket creation (" + REQUESTS + " requests): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // Test HTTP performance
        startTime = System.nanoTime();
        
        for (int i = 0; i < REQUESTS; i++) {
            try {
                URL url = new URL("https://httpbin.org/get");
                HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                connection.setRequestMethod("GET");
                connection.setConnectTimeout(1000);
                connection.getResponseCode();
                connection.disconnect();
            } catch (IOException e) {
                // Request failed, but we're measuring overhead
            }
        }
        
        endTime = System.nanoTime();
        System.out.println("HTTP requests (" + REQUESTS + " requests): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        System.out.println("\nPerformance tip: Reuse connections and use connection pooling for better performance");
    }
}
