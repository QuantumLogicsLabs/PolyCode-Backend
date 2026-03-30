<?php
/**
 * IoT Communication Protocols in PHP
 * 
 * MQTT, CoAP, HTTP, and other IoT communication protocols.
 */

// Communication Protocol Interface
interface CommunicationProtocol
{
    public function connect(): bool;
    public function disconnect(): bool;
    public function publish(string $topic, array $message): bool;
    public function subscribe(string $topic): bool;
    public function unsubscribe(string $topic): bool;
    public function isConnected(): bool;
    public function getStatus(): string;
    public function getProtocol(): string;
}

// MQTT Protocol Implementation
class MQTTProtocol implements CommunicationProtocol
{
    private string $host;
    private int $port;
    private string $clientId;
    private string $username;
    private string $password;
    private bool $connected = false;
    private array $subscriptions = [];
    private array $messageQueue = [];
    private int $keepAlive = 60;
    
    public function __construct(array $config)
    {
        $this->host = $config['host'] ?? 'localhost';
        $this->port = $config['port'] ?? 1883;
        $this->clientId = $config['client_id'] ?? 'php_mqtt_' . uniqid();
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->keepAlive = $config['keep_alive'] ?? 60;
    }
    
    public function connect(): bool
    {
        if ($this->connected) {
            return true;
        }
        
        echo "Connecting to MQTT broker at {$this->host}:{$this->port}\n";
        echo "Client ID: {$this->clientId}\n";
        
        // Simulate MQTT connection
        $this->connected = true;
        echo "Connected to MQTT broker\n";
        
        return true;
    }
    
    public function disconnect(): bool
    {
        if (!$this->connected) {
            return true;
        }
        
        echo "Disconnecting from MQTT broker\n";
        
        // Unsubscribe from all topics
        foreach ($this->subscriptions as $topic => $callback) {
            $this->unsubscribe($topic);
        }
        
        $this->connected = false;
        echo "Disconnected from MQTT broker\n";
        
        return true;
    }
    
    public function publish(string $topic, array $message): bool
    {
        if (!$this->connected) {
            echo "Not connected to MQTT broker\n";
            return false;
        }
        
        $payload = json_encode($message);
        $qos = 1; // Quality of Service level
        $retain = false;
        
        echo "Publishing to MQTT topic: $topic\n";
        echo "Payload: $payload\n";
        echo "QoS: $qos, Retain: " . ($retain ? 'true' : 'false') . "\n";
        
        // Add to message queue
        $this->messageQueue[] = [
            'topic' => $topic,
            'payload' => $payload,
            'qos' => $qos,
            'retain' => $retain,
            'timestamp' => time(),
            'direction' => 'outbound'
        ];
        
        return true;
    }
    
    public function subscribe(string $topic): bool
    {
        if (!$this->connected) {
            echo "Not connected to MQTT broker\n";
            return false;
        }
        
        echo "Subscribing to MQTT topic: $topic\n";
        
        $this->subscriptions[$topic] = true;
        
        // Simulate receiving a message
        $this->simulateIncomingMessage($topic);
        
        return true;
    }
    
    public function unsubscribe(string $topic): bool
    {
        if (!$this->connected) {
            echo "Not connected to MQTT broker\n";
            return false;
        }
        
        if (!isset($this->subscriptions[$topic])) {
            echo "Not subscribed to topic: $topic\n";
            return false;
        }
        
        echo "Unsubscribing from MQTT topic: $topic\n";
        unset($this->subscriptions[$topic]);
        
        return true;
    }
    
    public function isConnected(): bool
    {
        return $this->connected;
    }
    
    public function getStatus(): string
    {
        return $this->connected ? 'Connected' : 'Disconnected';
    }
    
    public function getProtocol(): string
    {
        return 'MQTT';
    }
    
    public function getSubscriptions(): array
    {
        return array_keys($this->subscriptions);
    }
    
    public function getMessageQueue(): array
    {
        return $this->messageQueue;
    }
    
    public function clearMessageQueue(): void
    {
        $this->messageQueue = [];
    }
    
    private function simulateIncomingMessage(string $topic): void
    {
        $message = [
            'device_id' => 'sensor_' . rand(1, 100),
            'timestamp' => time(),
            'value' => rand(0, 100),
            'status' => 'normal'
        ];
        
        $payload = json_encode($message);
        
        echo "Received message on topic: $topic\n";
        echo "Payload: $payload\n";
        
        $this->messageQueue[] = [
            'topic' => $topic,
            'payload' => $payload,
            'timestamp' => time(),
            'direction' => 'inbound'
        ];
    }
    
    public function setLastWill(string $topic, array $message, int $qos = 1, bool $retain = false): void
    {
        echo "Setting last will message for topic: $topic\n";
        echo "Last will: " . json_encode($message) . "\n";
    }
    
    public function publishRetained(string $topic, array $message): bool
    {
        if (!$this->connected) {
            return false;
        }
        
        echo "Publishing retained message to topic: $topic\n";
        
        $this->messageQueue[] = [
            'topic' => $topic,
            'payload' => json_encode($message),
            'qos' => 1,
            'retain' => true,
            'timestamp' => time(),
            'direction' => 'outbound'
        ];
        
        return true;
    }
}

// CoAP Protocol Implementation
class CoAPProtocol implements CommunicationProtocol
{
    private string $host;
    private int $port;
    private bool $connected = false;
    private array $messageQueue = [];
    private array $resources = [];
    
    public function __construct(array $config)
    {
        $this->host = $config['host'] ?? 'localhost';
        $this->port = $config['port'] ?? 5683;
    }
    
    public function connect(): bool
    {
        if ($this->connected) {
            return true;
        }
        
        echo "Connecting to CoAP server at {$this->host}:{$this->port}\n";
        
        // Simulate CoAP connection
        $this->connected = true;
        echo "Connected to CoAP server\n";
        
        return true;
    }
    
    public function disconnect(): bool
    {
        if (!$this->connected) {
            return true;
        }
        
        echo "Disconnecting from CoAP server\n";
        $this->connected = false;
        echo "Disconnected from CoAP server\n";
        
        return true;
    }
    
    public function publish(string $topic, array $message): bool
    {
        if (!$this->connected) {
            echo "Not connected to CoAP server\n";
            return false;
        }
        
        $payload = json_encode($message);
        $method = 'POST';
        
        echo "CoAP $method request to resource: $topic\n";
        echo "Payload: $payload\n";
        
        $this->messageQueue[] = [
            'resource' => $topic,
            'method' => $method,
            'payload' => $payload,
            'timestamp' => time(),
            'direction' => 'outbound'
        ];
        
        return true;
    }
    
    public function subscribe(string $topic): bool
    {
        if (!$this->connected) {
            echo "Not connected to CoAP server\n";
            return false;
        }
        
        echo "Observing CoAP resource: $topic\n";
        
        $this->resources[$topic] = 'observing';
        
        // Simulate receiving observation
        $this->simulateObservation($topic);
        
        return true;
    }
    
    public function unsubscribe(string $topic): bool
    {
        if (!$this->connected) {
            echo "Not connected to CoAP server\n";
            return false;
        }
        
        if (!isset($this->resources[$topic])) {
            echo "Not observing resource: $topic\n";
            return false;
        }
        
        echo "Stopping observation of CoAP resource: $topic\n";
        unset($this->resources[$topic]);
        
        return true;
    }
    
    public function isConnected(): bool
    {
        return $this->connected;
    }
    
    public function getStatus(): string
    {
        return $this->connected ? 'Connected' : 'Disconnected';
    }
    
    public function getProtocol(): string
    {
        return 'CoAP';
    }
    
    public function get(string $resource): array
    {
        if (!$this->connected) {
            echo "Not connected to CoAP server\n";
            return [];
        }
        
        echo "CoAP GET request to resource: $resource\n";
        
        // Simulate response
        $response = [
            'resource' => $resource,
            'method' => 'GET',
            'payload' => json_encode([
                'value' => rand(0, 100),
                'timestamp' => time(),
                'status' => 'ok'
            ]),
            'timestamp' => time(),
            'direction' => 'inbound'
        ];
        
        $this->messageQueue[] = $response;
        
        return json_decode($response['payload'], true);
    }
    
    public function put(string $resource, array $data): bool
    {
        if (!$this->connected) {
            echo "Not connected to CoAP server\n";
            return false;
        }
        
        $payload = json_encode($data);
        
        echo "CoAP PUT request to resource: $resource\n";
        echo "Payload: $payload\n";
        
        $this->messageQueue[] = [
            'resource' => $resource,
            'method' => 'PUT',
            'payload' => $payload,
            'timestamp' => time(),
            'direction' => 'outbound'
        ];
        
        return true;
    }
    
    public function delete(string $resource): bool
    {
        if (!$this->connected) {
            echo "Not connected to CoAP server\n";
            return false;
        }
        
        echo "CoAP DELETE request to resource: $resource\n";
        
        $this->messageQueue[] = [
            'resource' => $resource,
            'method' => 'DELETE',
            'payload' => '',
            'timestamp' => time(),
            'direction' => 'outbound'
        ];
        
        return true;
    }
    
    public function getMessageQueue(): array
    {
        return $this->messageQueue;
    }
    
    public function getObservedResources(): array
    {
        return array_keys($this->resources);
    }
    
    private function simulateObservation(string $resource): void
    {
        $message = [
            'resource' => $resource,
            'method' => 'OBSERVE',
            'payload' => json_encode([
                'value' => rand(0, 100),
                'timestamp' => time(),
                'notification' => true
            ]),
            'timestamp' => time(),
            'direction' => 'inbound'
        ];
        
        echo "Received CoAP notification for resource: $resource\n";
        $this->messageQueue[] = $message;
    }
}

// HTTP Protocol Implementation
class HTTPProtocol implements CommunicationProtocol
{
    private string $baseUrl;
    private string $apiKey;
    private bool $connected = false;
    private array $messageQueue = [];
    private array $headers;
    
    public function __construct(array $config)
    {
        $this->baseUrl = $config['base_url'] ?? 'http://localhost/api';
        $this->apiKey = $config['api_key'] ?? '';
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        
        if ($this->apiKey) {
            $this->headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }
    }
    
    public function connect(): bool
    {
        if ($this->connected) {
            return true;
        }
        
        echo "Connecting to HTTP API at {$this->baseUrl}\n";
        
        // Simulate HTTP connection
        $this->connected = true;
        echo "Connected to HTTP API\n";
        
        return true;
    }
    
    public function disconnect(): bool
    {
        if (!$this->connected) {
            return true;
        }
        
        echo "Disconnecting from HTTP API\n";
        $this->connected = false;
        echo "Disconnected from HTTP API\n";
        
        return true;
    }
    
    public function publish(string $topic, array $message): bool
    {
        if (!$this->connected) {
            echo "Not connected to HTTP API\n";
            return false;
        }
        
        $payload = json_encode($message);
        $endpoint = $this->baseUrl . '/' . $topic;
        
        echo "HTTP POST to endpoint: $endpoint\n";
        echo "Payload: $payload\n";
        
        $this->messageQueue[] = [
            'endpoint' => $endpoint,
            'method' => 'POST',
            'payload' => $payload,
            'headers' => $this->headers,
            'timestamp' => time(),
            'direction' => 'outbound'
        ];
        
        return true;
    }
    
    public function subscribe(string $topic): bool
    {
        if (!$this->connected) {
            echo "Not connected to HTTP API\n";
            return false;
        }
        
        echo "HTTP doesn't support push subscriptions. Use polling or webhooks for: $topic\n";
        
        // Simulate webhook registration
        $webhookUrl = $this->baseUrl . '/webhooks/' . $topic;
        echo "Registered webhook: $webhookUrl\n";
        
        return true;
    }
    
    public function unsubscribe(string $topic): bool
    {
        if (!$this->connected) {
            echo "Not connected to HTTP API\n";
            return false;
        }
        
        echo "Unregistering webhook for: $topic\n";
        return true;
    }
    
    public function isConnected(): bool
    {
        return $this->connected;
    }
    
    public function getStatus(): string
    {
        return $this->connected ? 'Connected' : 'Disconnected';
    }
    
    public function getProtocol(): string
    {
        return 'HTTP';
    }
    
    public function get(string $endpoint): array
    {
        if (!$this->connected) {
            echo "Not connected to HTTP API\n";
            return [];
        }
        
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        echo "HTTP GET request to: $url\n";
        
        // Simulate response
        $response = [
            'endpoint' => $url,
            'method' => 'GET',
            'payload' => json_encode([
                'data' => [
                    'id' => rand(1, 100),
                    'value' => rand(0, 100),
                    'timestamp' => time()
                ]
            ]),
            'headers' => $this->headers,
            'timestamp' => time(),
            'direction' => 'inbound'
        ];
        
        $this->messageQueue[] = $response;
        
        return json_decode($response['payload'], true);
    }
    
    public function post(string $endpoint, array $data): array
    {
        if (!$this->connected) {
            echo "Not connected to HTTP API\n";
            return [];
        }
        
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $payload = json_encode($data);
        
        echo "HTTP POST request to: $url\n";
        echo "Payload: $payload\n";
        
        $response = [
            'endpoint' => $url,
            'method' => 'POST',
            'payload' => json_encode([
                'success' => true,
                'data' => $data,
                'id' => rand(1, 100)
            ]),
            'headers' => $this->headers,
            'timestamp' => time(),
            'direction' => 'inbound'
        ];
        
        $this->messageQueue[] = $response;
        
        return json_decode($response['payload'], true);
    }
    
    public function put(string $endpoint, array $data): array
    {
        if (!$this->connected) {
            echo "Not connected to HTTP API\n";
            return [];
        }
        
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $payload = json_encode($data);
        
        echo "HTTP PUT request to: $url\n";
        echo "Payload: $payload\n";
        
        $response = [
            'endpoint' => $url,
            'method' => 'PUT',
            'payload' => json_encode([
                'success' => true,
                'data' => $data,
                'updated' => true
            ]),
            'headers' => $this->headers,
            'timestamp' => time(),
            'direction' => 'inbound'
        ];
        
        $this->messageQueue[] = $response;
        
        return json_decode($response['payload'], true);
    }
    
    public function delete(string $endpoint): array
    {
        if (!$this->connected) {
            echo "Not connected to HTTP API\n";
            return [];
        }
        
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        echo "HTTP DELETE request to: $url\n";
        
        $response = [
            'endpoint' => $url,
            'method' => 'DELETE',
            'payload' => json_encode([
                'success' => true,
                'deleted' => true
            ]),
            'headers' => $this->headers,
            'timestamp' => time(),
            'direction' => 'inbound'
        ];
        
        $this->messageQueue[] = $response;
        
        return json_decode($response['payload'], true);
    }
    
    public function getMessageQueue(): array
    {
        return $this->messageQueue;
    }
    
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }
}

// WebSocket Protocol Implementation
class WebSocketProtocol implements CommunicationProtocol
{
    private string $url;
    private bool $connected = false;
    private array $subscriptions = [];
    private array $messageQueue = [];
    
    public function __construct(array $config)
    {
        $this->url = $config['url'] ?? 'ws://localhost:8080';
    }
    
    public function connect(): bool
    {
        if ($this->connected) {
            return true;
        }
        
        echo "Connecting to WebSocket at {$this->url}\n";
        
        // Simulate WebSocket connection
        $this->connected = true;
        echo "Connected to WebSocket\n";
        
        return true;
    }
    
    public function disconnect(): bool
    {
        if (!$this->connected) {
            return true;
        }
        
        echo "Disconnecting from WebSocket\n";
        $this->connected = false;
        echo "Disconnected from WebSocket\n";
        
        return true;
    }
    
    public function publish(string $topic, array $message): bool
    {
        if (!$this->connected) {
            echo "Not connected to WebSocket\n";
            return false;
        }
        
        $payload = json_encode([
            'topic' => $topic,
            'message' => $message,
            'timestamp' => time()
        ]);
        
        echo "WebSocket message to topic: $topic\n";
        echo "Payload: $payload\n";
        
        $this->messageQueue[] = [
            'topic' => $topic,
            'payload' => $payload,
            'timestamp' => time(),
            'direction' => 'outbound'
        ];
        
        return true;
    }
    
    public function subscribe(string $topic): bool
    {
        if (!$this->connected) {
            echo "Not connected to WebSocket\n";
            return false;
        }
        
        echo "Subscribing to WebSocket topic: $topic\n";
        
        $this->subscriptions[$topic] = true;
        
        // Simulate receiving a message
        $this->simulateIncomingMessage($topic);
        
        return true;
    }
    
    public function unsubscribe(string $topic): bool
    {
        if (!$this->connected) {
            echo "Not connected to WebSocket\n";
            return false;
        }
        
        if (!isset($this->subscriptions[$topic])) {
            echo "Not subscribed to topic: $topic\n";
            return false;
        }
        
        echo "Unsubscribing from WebSocket topic: $topic\n";
        unset($this->subscriptions[$topic]);
        
        return true;
    }
    
    public function isConnected(): bool
    {
        return $this->connected;
    }
    
    public function getStatus(): string
    {
        return $this->connected ? 'Connected' : 'Disconnected';
    }
    
    public function getProtocol(): string
    {
        return 'WebSocket';
    }
    
    public function sendMessage(array $message): bool
    {
        if (!$this->connected) {
            echo "Not connected to WebSocket\n";
            return false;
        }
        
        $payload = json_encode($message);
        
        echo "WebSocket message: $payload\n";
        
        $this->messageQueue[] = [
            'payload' => $payload,
            'timestamp' => time(),
            'direction' => 'outbound'
        ];
        
        return true;
    }
    
    public function getMessageQueue(): array
    {
        return $this->messageQueue;
    }
    
    public function getSubscriptions(): array
    {
        return array_keys($this->subscriptions);
    }
    
    private function simulateIncomingMessage(string $topic): void
    {
        $message = [
            'topic' => $topic,
            'message' => [
                'device_id' => 'sensor_' . rand(1, 100),
                'timestamp' => time(),
                'value' => rand(0, 100),
                'status' => 'normal'
            ],
            'timestamp' => time(),
            'direction' => 'inbound'
        ];
        
        echo "Received WebSocket message on topic: $topic\n";
        $this->messageQueue[] = $message;
    }
}

// IoT Communication Manager
class IoTCommunicationManager
{
    private array $protocols = [];
    private array $activeConnections = [];
    private array $messageHandlers = [];
    
    public function addProtocol(string $name, CommunicationProtocol $protocol): void
    {
        $this->protocols[$name] = $protocol;
        echo "Added protocol: $name\n";
    }
    
    public function connect(string $protocolName): bool
    {
        if (!isset($this->protocols[$protocolName])) {
            echo "Protocol not found: $protocolName\n";
            return false;
        }
        
        $protocol = $this->protocols[$protocolName];
        
        if ($protocol->connect()) {
            $this->activeConnections[$protocolName] = $protocol;
            return true;
        }
        
        return false;
    }
    
    public function disconnect(string $protocolName): bool
    {
        if (!isset($this->activeConnections[$protocolName])) {
            echo "No active connection for: $protocolName\n";
            return false;
        }
        
        $protocol = $this->activeConnections[$protocolName];
        
        if ($protocol->disconnect()) {
            unset($this->activeConnections[$protocolName]);
            return true;
        }
        
        return false;
    }
    
    public function disconnectAll(): void
    {
        foreach (array_keys($this->activeConnections) as $protocolName) {
            $this->disconnect($protocolName);
        }
    }
    
    public function publish(string $protocolName, string $topic, array $message): bool
    {
        if (!isset($this->activeConnections[$protocolName])) {
            echo "No active connection for: $protocolName\n";
            return false;
        }
        
        return $this->activeConnections[$protocolName]->publish($topic, $message);
    }
    
    public function broadcast(string $topic, array $message): array
    {
        $results = [];
        
        foreach ($this->activeConnections as $protocolName => $protocol) {
            $results[$protocolName] = $protocol->publish($topic, $message);
        }
        
        return $results;
    }
    
    public function subscribe(string $protocolName, string $topic): bool
    {
        if (!isset($this->activeConnections[$protocolName])) {
            echo "No active connection for: $protocolName\n";
            return false;
        }
        
        return $this->activeConnections[$protocolName]->subscribe($topic);
    }
    
    public function unsubscribe(string $protocolName, string $topic): bool
    {
        if (!isset($this->activeConnections[$protocolName])) {
            echo "No active connection for: $protocolName\n";
            return false;
        }
        
        return $this->activeConnections[$protocolName]->unsubscribe($topic);
    }
    
    public function addMessageHandler(string $protocolName, callable $handler): void
    {
        if (!isset($this->messageHandlers[$protocolName])) {
            $this->messageHandlers[$protocolName] = [];
        }
        
        $this->messageHandlers[$protocolName][] = $handler;
    }
    
    public function getStatus(): array
    {
        $status = [];
        
        foreach ($this->protocols as $name => $protocol) {
            $status[$name] = [
                'protocol' => $protocol->getProtocol(),
                'connected' => $protocol->isConnected(),
                'status' => $protocol->getStatus(),
                'active' => isset($this->activeConnections[$name])
            ];
        }
        
        return $status;
    }
    
    public function getProtocols(): array
    {
        return array_keys($this->protocols);
    }
    
    public function getActiveConnections(): array
    {
        return array_keys($this->activeConnections);
    }
    
    public function getMessageQueues(): array
    {
        $queues = [];
        
        foreach ($this->activeConnections as $name => $protocol) {
            $queues[$name] = $protocol->getMessageQueue();
        }
        
        return $queues;
    }
    
    public function clearMessageQueues(): void
    {
        foreach ($this->activeConnections as $protocol) {
            if (method_exists($protocol, 'clearMessageQueue')) {
                $protocol->clearMessageQueue();
            }
        }
    }
}

// IoT Communication Examples
class IoTCommunicationExamples
{
    public function demonstrateMQTT(): void
    {
        echo "MQTT Protocol Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $mqtt = new MQTTProtocol([
            'host' => 'mqtt.example.com',
            'port' => 1883,
            'client_id' => 'php_iot_demo',
            'username' => 'iot_user',
            'password' => 'iot_pass'
        ]);
        
        $mqtt->connect();
        
        // Publish messages
        echo "\nPublishing messages:\n";
        $mqtt->publish('sensors/temperature', [
            'device_id' => 'temp_001',
            'temperature' => 23.5,
            'unit' => 'celsius',
            'timestamp' => time()
        ]);
        
        $mqtt->publish('sensors/motion', [
            'device_id' => 'motion_001',
            'motion_detected' => true,
            'timestamp' => time()
        ]);
        
        // Subscribe to topics
        echo "\nSubscribing to topics:\n";
        $mqtt->subscribe('sensors/temperature');
        $mqtt->subscribe('sensors/motion');
        $mqtt->subscribe('devices/status');
        
        // Publish retained message
        echo "\nPublishing retained message:\n";
        $mqtt->publishRetained('devices/config', [
            'update_interval' => 60,
            'retry_attempts' => 3
        ]);
        
        // Set last will
        echo "\nSetting last will message:\n";
        $mqtt->setLastWill('devices/offline', [
            'device_id' => 'temp_001',
            'status' => 'offline',
            'timestamp' => time()
        ]);
        
        // Show status
        echo "\nMQTT Status:\n";
        echo "Connected: " . ($mqtt->isConnected() ? 'Yes' : 'No') . "\n";
        echo "Subscriptions: " . implode(', ', $mqtt->getSubscriptions()) . "\n";
        echo "Message queue: " . count($mqtt->getMessageQueue()) . " messages\n";
        
        $mqtt->disconnect();
    }
    
    public function demonstrateCoAP(): void
    {
        echo "\nCoAP Protocol Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $coap = new CoAPProtocol([
            'host' => 'coap.example.com',
            'port' => 5683
        ]);
        
        $coap->connect();
        
        // GET request
        echo "\nCoAP GET requests:\n";
        $tempData = $coap->get('sensors/temperature');
        echo "Temperature data: " . json_encode($tempData) . "\n";
        
        $motionData = $coap->get('sensors/motion');
        echo "Motion data: " . json_encode($motionData) . "\n";
        
        // PUT request
        echo "\nCoAP PUT requests:\n";
        $coap->put('actuators/light', [
            'state' => 'on',
            'brightness' => 75
        ]);
        
        $coap->put('actuators/fan', [
            'speed' => 2,
            'mode' => 'auto'
        ]);
        
        // Observe resources
        echo "\nCoAP Observing resources:\n";
        $coap->subscribe('sensors/temperature');
        $coap->subscribe('sensors/humidity');
        
        // POST request
        echo "\nCoAP POST requests:\n";
        $coap->publish('commands/reset', [
            'device_id' => 'sensor_001',
            'command' => 'reset'
        ]);
        
        // DELETE request
        echo "\nCoAP DELETE requests:\n";
        $coap->delete('cache/old_data');
        
        // Show status
        echo "\nCoAP Status:\n";
        echo "Connected: " . ($coap->isConnected() ? 'Yes' : 'No') . "\n";
        echo "Observed resources: " . implode(', ', $coap->getObservedResources()) . "\n";
        echo "Message queue: " . count($coap->getMessageQueue()) . " messages\n";
        
        $coap->disconnect();
    }
    
    public function demonstrateHTTP(): void
    {
        echo "\nHTTP Protocol Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $http = new HTTPProtocol([
            'base_url' => 'https://api.iot.example.com/v1',
            'api_key' => 'your_api_key_here'
        ]);
        
        $http->connect();
        
        // GET requests
        echo "\nHTTP GET requests:\n";
        $devices = $http->get('devices');
        echo "Devices: " . json_encode($devices) . "\n";
        
        $sensors = $http->get('sensors');
        echo "Sensors: " . json_encode($sensors) . "\n";
        
        // POST requests
        echo "\nHTTP POST requests:\n";
        $http->publish('data/sensors', [
            'device_id' => 'temp_001',
            'temperature' => 25.3,
            'humidity' => 60.2
        ]);
        
        $http->post('devices', [
            'name' => 'New Sensor',
            'type' => 'temperature',
            'location' => 'Living Room'
        ]);
        
        // PUT requests
        echo "\nHTTP PUT requests:\n";
        $http->put('devices/temp_001', [
            'name' => 'Updated Sensor',
            'location' => 'Bedroom'
        ]);
        
        // DELETE requests
        echo "\nHTTP DELETE requests:\n";
        $http->delete('devices/old_sensor');
        
        // Subscribe (webhook)
        echo "\nHTTP Webhooks:\n";
        $http->subscribe('sensor/alerts');
        $http->subscribe('device/status');
        
        // Show status
        echo "\nHTTP Status:\n";
        echo "Connected: " . ($http->isConnected() ? 'Yes' : 'No') . "\n";
        echo "Base URL: " . $http->getBaseUrl() . "\n";
        echo "Message queue: " . count($http->getMessageQueue()) . " messages\n";
        
        $http->disconnect();
    }
    
    public function demonstrateWebSocket(): void
    {
        echo "\nWebSocket Protocol Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $ws = new WebSocketProtocol([
            'url' => 'ws://iot.example.com:8080/ws'
        ]);
        
        $ws->connect();
        
        // Send messages
        echo "\nWebSocket messages:\n";
        $ws->sendMessage([
            'type' => 'sensor_data',
            'device_id' => 'temp_001',
            'data' => [
                'temperature' => 24.5,
                'humidity' => 58.3
            ]
        ]);
        
        $ws->publish('devices/status', [
            'device_id' => 'light_001',
            'status' => 'online'
        ]);
        
        // Subscribe to topics
        echo "\nWebSocket subscriptions:\n";
        $ws->subscribe('sensor/updates');
        $ws->subscribe('device/alerts');
        $ws->subscribe('system/status');
        
        // Show status
        echo "\nWebSocket Status:\n";
        echo "Connected: " . ($ws->isConnected() ? 'Yes' : 'No') . "\n";
        echo "Subscriptions: " . implode(', ', $ws->getSubscriptions()) . "\n";
        echo "Message queue: " . count($ws->getMessageQueue()) . " messages\n";
        
        $ws->disconnect();
    }
    
    public function demonstrateCommunicationManager(): void
    {
        echo "\nIoT Communication Manager Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        $manager = new IoTCommunicationManager();
        
        // Add protocols
        echo "Adding protocols:\n";
        $manager->addProtocol('mqtt', new MQTTProtocol([
            'host' => 'mqtt.example.com',
            'port' => 1883
        ]));
        
        $manager->addProtocol('coap', new CoAPProtocol([
            'host' => 'coap.example.com',
            'port' => 5683
        ]));
        
        $manager->addProtocol('http', new HTTPProtocol([
            'base_url' => 'https://api.iot.example.com/v1'
        ]));
        
        // Connect protocols
        echo "\nConnecting protocols:\n";
        $manager->connect('mqtt');
        $manager->connect('coap');
        $manager->connect('http');
        
        // Publish messages
        echo "\nPublishing messages:\n";
        $manager->publish('mqtt', 'sensors/temperature', [
            'device_id' => 'temp_001',
            'temperature' => 23.5
        ]);
        
        $manager->publish('coap', 'actuators/light', [
            'state' => 'on',
            'brightness' => 80
        ]);
        
        $manager->publish('http', 'data/sensors', [
            'device_id' => 'motion_001',
            'motion_detected' => true
        ]);
        
        // Broadcast message
        echo "\nBroadcasting message:\n";
        $results = $manager->broadcast('system/status', [
            'timestamp' => time(),
            'status' => 'operational'
        ]);
        
        foreach ($results as $protocol => $result) {
            echo "$protocol: " . ($result ? 'Success' : 'Failed') . "\n";
        }
        
        // Subscribe to topics
        echo "\nSubscribing to topics:\n";
        $manager->subscribe('mqtt', 'sensors/temperature');
        $manager->subscribe('coap', 'sensors/humidity');
        $manager->subscribe('http', 'device/alerts');
        
        // Show status
        echo "\nCommunication Manager Status:\n";
        $status = $manager->getStatus();
        foreach ($status as $protocol => $info) {
            echo "$protocol:\n";
            echo "  Protocol: {$info['protocol']}\n";
            echo "  Connected: " . ($info['connected'] ? 'Yes' : 'No') . "\n";
            echo "  Status: {$info['status']}\n";
            echo "  Active: " . ($info['active'] ? 'Yes' : 'No') . "\n";
        }
        
        // Show message queues
        echo "\nMessage Queues:\n";
        $queues = $manager->getMessageQueues();
        foreach ($queues as $protocol => $messages) {
            echo "$protocol: " . count($messages) . " messages\n";
        }
        
        // Disconnect all
        echo "\nDisconnecting all protocols:\n";
        $manager->disconnectAll();
    }
    
    public function demonstrateProtocolComparison(): void
    {
        echo "\nProtocol Comparison Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $protocols = [
            'MQTT' => [
                'characteristics' => ['Lightweight', 'Publish/Subscribe', 'QoS levels', 'Last Will'],
                'use_cases' => ['Sensor data', 'Device control', 'Real-time monitoring'],
                'pros' => ['Low bandwidth', 'Reliable delivery', 'Scalable'],
                'cons' => ['No built-in security', 'Limited payload size']
            ],
            'CoAP' => [
                'characteristics' => ['UDP-based', 'RESTful', 'Observing', 'Multicast'],
                'use_cases' => ['Resource discovery', 'Device configuration', 'Constrained devices'],
                'pros' => ['Very lightweight', 'HTTP-like', 'Multicast support'],
                'cons' => ['Unreliable (UDP)', 'Limited error handling']
            ],
            'HTTP' => [
                'characteristics' => ['Request/Response', 'RESTful', 'Stateful', 'Secure'],
                'use_cases' => ['Device management', 'Data analytics', 'Web interfaces'],
                'pros' => ['Well-known', 'Secure', 'Rich features'],
                'cons' => ['Heavyweight', 'Not real-time', 'High overhead']
            ],
            'WebSocket' => [
                'characteristics' => ['Full-duplex', 'Real-time', 'Persistent', 'Bi-directional'],
                'use_cases' => ['Live monitoring', 'Interactive control', 'Dashboards'],
                'pros' => ['Low latency', 'Efficient', 'Browser support'],
                'cons' => ['Stateful', 'Scaling challenges', 'Firewall issues']
            ]
        ];
        
        foreach ($protocols as $protocol => $info) {
            echo "\n$protocol:\n";
            echo "  Characteristics: " . implode(', ', $info['characteristics']) . "\n";
            echo "  Use Cases: " . implode(', ', $info['use_cases']) . "\n";
            echo "  Pros: " . implode(', ', $info['pros']) . "\n";
            echo "  Cons: " . implode(', ', $info['cons']) . "\n";
        }
        
        echo "\nProtocol Selection Guide:\n";
        echo "• Use MQTT for sensor data and device control\n";
        echo "• Use CoAP for constrained devices and resource discovery\n";
        echo "• Use HTTP for device management and web interfaces\n";
        echo "• Use WebSocket for real-time dashboards and interactive control\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nIoT Communication Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Protocol Selection:\n";
        echo "   • Choose protocol based on device capabilities\n";
        echo "   • Consider network conditions and bandwidth\n";
        echo "   • Evaluate security requirements\n";
        echo "   • Plan for scalability and reliability\n";
        echo "   • Use multiple protocols when needed\n\n";
        
        echo "2. Security:\n";
        echo "   • Use TLS/SSL encryption for sensitive data\n";
        echo "   • Implement proper authentication\n";
        echo "   • Use secure MQTT (TLS)\n";
        echo "   • Validate all incoming data\n";
        echo "   • Implement rate limiting\n\n";
        
        echo "3. Reliability:\n";
        echo "   • Implement retry mechanisms\n";
        echo "   • Use appropriate QoS levels\n";
        echo "   • Handle connection failures gracefully\n";
        echo "   • Implement message acknowledgments\n";
        echo "   • Use offline queuing\n\n";
        
        echo "4. Performance:\n";
        echo "   • Minimize message size\n";
        echo "   • Use efficient data formats\n";
        echo "   • Implement message batching\n";
        echo "   • Use compression when appropriate\n";
        echo "   • Optimize polling intervals\n\n";
        
        echo "5. Monitoring:\n";
        echo "   • Track message delivery\n";
        echo "   • Monitor connection status\n";
        echo "   • Log communication errors\n";
        echo "   • Implement health checks\n";
        echo "   • Use metrics and alerts";
    }
    
    public function runAllExamples(): void
    {
        echo "IoT Communication Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateMQTT();
        $this->demonstrateCoAP();
        $this->demonstrateHTTP();
        $this->demonstrateWebSocket();
        $this->demonstrateCommunicationManager();
        $this->demonstrateProtocolComparison();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runIoTCommunicationDemo(): void
{
    $examples = new IoTCommunicationExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runIoTCommunicationDemo();
}
?>
