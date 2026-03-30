<?php
/**
 * Service Communication Patterns
 * 
 * Implementation of various communication patterns for microservices.
 */

// REST API Communication
class RestApiCommunication
{
    private array $services;
    private array $httpClient;
    
    public function __construct()
    {
        $this->services = [
            'user-service' => 'http://localhost:8001',
            'order-service' => 'http://localhost:8002',
            'product-service' => 'http://localhost:8003',
            'notification-service' => 'http://localhost:8004',
            'payment-service' => 'http://localhost:8005'
        ];
        
        $this->httpClient = [
            'timeout' => 30,
            'retries' => 3,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];
    }
    
    /**
     * Make HTTP request to service
     */
    public function request(string $service, string $method, string $endpoint, array $data = []): array
    {
        $url = $this->services[$service] . $endpoint;
        
        $request = [
            'url' => $url,
            'method' => $method,
            'headers' => $this->httpClient['headers'],
            'data' => $data,
            'timeout' => $this->httpClient['timeout']
        ];
        
        // Simulate HTTP request
        $response = $this->makeHttpRequest($request);
        
        return $response;
    }
    
    /**
     * GET request
     */
    public function get(string $service, string $endpoint): array
    {
        return $this->request($service, 'GET', $endpoint);
    }
    
    /**
     * POST request
     */
    public function post(string $service, string $endpoint, array $data): array
    {
        return $this->request($service, 'POST', $endpoint, $data);
    }
    
    /**
     * PUT request
     */
    public function put(string $service, string $endpoint, array $data): array
    {
        return $this->request($service, 'PUT', $endpoint, $data);
    }
    
    /**
     * DELETE request
     */
    public function delete(string $service, string $endpoint): array
    {
        return $this->request($service, 'DELETE', $endpoint);
    }
    
    /**
     * Simulate HTTP request
     */
    private function makeHttpRequest(array $request): array
    {
        // Simulate request processing
        $response = [
            'status' => 200,
            'data' => null,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'request_id' => uniqid('req_'),
            'timestamp' => time()
        ];
        
        // Simulate different responses based on URL
        if (strpos($request['url'], 'users') !== false) {
            if ($request['method'] === 'GET') {
                $response['data'] = [
                    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
                    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com']
                ];
            } elseif ($request['method'] === 'POST') {
                $response['data'] = [
                    'id' => rand(100, 999),
                    'message' => 'User created successfully'
                ];
                $response['status'] = 201;
            }
        } elseif (strpos($request['url'], 'orders') !== false) {
            if ($request['method'] === 'GET') {
                $response['data'] = [
                    ['id' => 1, 'user_id' => 1, 'total' => 99.99, 'status' => 'completed'],
                    ['id' => 2, 'user_id' => 2, 'total' => 149.99, 'status' => 'pending']
                ];
            }
        }
        
        return $response;
    }
    
    /**
     * Add authentication header
     */
    public function withAuth(string $token): self
    {
        $this->httpClient['headers']['Authorization'] = "Bearer $token";
        return $this;
    }
    
    /**
     * Add custom header
     */
    public function withHeader(string $name, string $value): self
    {
        $this->httpClient['headers'][$name] = $value;
        return $this;
    }
}

// Message Queue Communication
class MessageQueueCommunication
{
    private array $queues;
    private array $exchanges;
    private array $bindings;
    
    public function __construct()
    {
        $this->initializeQueues();
        $this->initializeExchanges();
        $this->initializeBindings();
    }
    
    /**
     * Initialize queues
     */
    private function initializeQueues(): void
    {
        $this->queues = [
            'user_events' => [
                'name' => 'user_events',
                'durable' => true,
                'auto_delete' => false,
                'messages' => []
            ],
            'order_events' => [
                'name' => 'order_events',
                'durable' => true,
                'auto_delete' => false,
                'messages' => []
            ],
            'payment_events' => [
                'name' => 'payment_events',
                'durable' => true,
                'auto_delete' => false,
                'messages' => []
            ],
            'notification_queue' => [
                'name' => 'notification_queue',
                'durable' => true,
                'auto_delete' => false,
                'messages' => []
            ]
        ];
    }
    
    /**
     * Initialize exchanges
     */
    private function initializeExchanges(): void
    {
        $this->exchanges = [
            'events' => [
                'name' => 'events',
                'type' => 'topic',
                'durable' => true,
                'auto_delete' => false
            ],
            'direct' => [
                'name' => 'direct',
                'type' => 'direct',
                'durable' => true,
                'auto_delete' => false
            ]
        ];
    }
    
    /**
     * Initialize bindings
     */
    private function initializeBindings(): void
    {
        $this->bindings = [
            'user_events' => [
                'exchange' => 'events',
                'routing_key' => 'user.*'
            ],
            'order_events' => [
                'exchange' => 'events',
                'routing_key' => 'order.*'
            ],
            'payment_events' => [
                'exchange' => 'events',
                'routing_key' => 'payment.*'
            ],
            'notification_queue' => [
                'exchange' => 'direct',
                'routing_key' => 'notifications'
            ]
        ];
    }
    
    /**
     * Publish message
     */
    public function publish(string $exchange, string $routingKey, array $message): bool
    {
        if (!isset($this->exchanges[$exchange])) {
            return false;
        }
        
        $messageData = [
            'id' => uniqid('msg_'),
            'exchange' => $exchange,
            'routing_key' => $routingKey,
            'payload' => $message,
            'timestamp' => time(),
            'headers' => [
                'content_type' => 'application/json',
                'message_id' => uniqid('mid_')
            ]
        ];
        
        // Route to bound queues
        $this->routeMessage($messageData);
        
        return true;
    }
    
    /**
     * Send message to queue
     */
    public function sendToQueue(string $queue, array $message): bool
    {
        if (!isset($this->queues[$queue])) {
            return false;
        }
        
        $messageData = [
            'id' => uniqid('msg_'),
            'queue' => $queue,
            'payload' => $message,
            'timestamp' => time(),
            'attempts' => 0,
            'status' => 'pending'
        ];
        
        $this->queues[$queue]['messages'][] = $messageData;
        
        return true;
    }
    
    /**
     * Consume message from queue
     */
    public function consume(string $queue, callable $callback): void
    {
        if (!isset($this->queues[$queue])) {
            return;
        }
        
        $messages = $this->queues[$queue]['messages'];
        
        foreach ($messages as $message) {
            if ($message['status'] === 'pending') {
                try {
                    $callback($message);
                    $message['status'] = 'processed';
                } catch (Exception $e) {
                    $message['status'] = 'failed';
                    $message['error'] = $e->getMessage();
                }
            }
        }
    }
    
    /**
     * Route message to queues
     */
    private function routeMessage(array $message): void
    {
        foreach ($this->bindings as $queue => $binding) {
            if ($this->matchesRoutingKey($binding['routing_key'], $message['routing_key'])) {
                $this->queues[$queue]['messages'][] = $message;
            }
        }
    }
    
    /**
     * Match routing key pattern
     */
    private function matchesRoutingKey(string $pattern, string $key): bool
    {
        $pattern = str_replace('*', '.*', $pattern);
        return preg_match("/^$pattern$/", $key);
    }
    
    /**
     * Get queue messages
     */
    public function getQueueMessages(string $queue): array
    {
        return $this->queues[$queue]['messages'] ?? [];
    }
    
    /**
     * Get all queues
     */
    public function getQueues(): array
    {
        return $this->queues;
    }
}

// Event-Driven Communication
class EventDrivenCommunication
{
    private array $eventBus;
    private array $eventHandlers;
    private array $eventStore;
    
    public function __construct()
    {
        $this->eventBus = [];
        $this->eventHandlers = [];
        $this->eventStore = [];
    }
    
    /**
     * Emit event
     */
    public function emit(string $eventName, array $data): void
    {
        $event = [
            'id' => uniqid('event_'),
            'name' => $eventName,
            'data' => $data,
            'timestamp' => time(),
            'version' => '1.0',
            'source' => 'microservice'
        ];
        
        $this->eventBus[] = $event;
        $this->eventStore[] = $event;
        
        // Notify handlers
        $this->notifyHandlers($event);
    }
    
    /**
     * Subscribe to event
     */
    public function on(string $eventName, callable $handler): string
    {
        $subscriptionId = uniqid('sub_');
        
        $this->eventHandlers[] = [
            'id' => $subscriptionId,
            'event' => $eventName,
            'handler' => $handler,
            'created_at' => time()
        ];
        
        return $subscriptionId;
    }
    
    /**
     * Unsubscribe from event
     */
    public function off(string $subscriptionId): bool
    {
        foreach ($this->eventHandlers as $key => $handler) {
            if ($handler['id'] === $subscriptionId) {
                unset($this->eventHandlers[$key]);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Notify event handlers
     */
    private function notifyHandlers(array $event): void
    {
        foreach ($this->eventHandlers as $handler) {
            if ($handler['event'] === $event['name']) {
                try {
                    call_user_func($handler['handler'], $event);
                } catch (Exception $e) {
                    error_log("Event handler error: " . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Get event history
     */
    public function getEventHistory(string $eventName = null): array
    {
        if ($eventName) {
            return array_filter($this->eventStore, function($event) use ($eventName) {
                return $event['name'] === $eventName;
            });
        }
        
        return $this->eventStore;
    }
    
    /**
     * Get active subscriptions
     */
    public function getSubscriptions(): array
    {
        return $this->eventHandlers;
    }
}

// Circuit Breaker Pattern
class CircuitBreaker
{
    private string $service;
    private int $failureThreshold;
    private int $timeout;
    private int $failureCount = 0;
    private int $lastFailureTime = 0;
    private string $state = 'closed'; // closed, open, half-open
    
    public function __construct(string $service, int $failureThreshold = 5, int $timeout = 60)
    {
        $this->service = $service;
        $this->failureThreshold = $failureThreshold;
        $this->timeout = $timeout;
    }
    
    /**
     * Execute operation with circuit breaker
     */
    public function execute(callable $operation): mixed
    {
        if ($this->state === 'open') {
            if ($this->shouldAttemptReset()) {
                $this->state = 'half-open';
            } else {
                throw new Exception("Circuit breaker is OPEN for service: {$this->service}");
            }
        }
        
        try {
            $result = $operation();
            $this->onSuccess();
            return $result;
        } catch (Exception $e) {
            $this->onFailure();
            throw $e;
        }
    }
    
    /**
     * Handle successful operation
     */
    private function onSuccess(): void
    {
        $this->failureCount = 0;
        $this->state = 'closed';
    }
    
    /**
     * Handle failed operation
     */
    private function onFailure(): void
    {
        $this->failureCount++;
        $this->lastFailureTime = time();
        
        if ($this->failureCount >= $this->failureThreshold) {
            $this->state = 'open';
        }
    }
    
    /**
     * Check if should attempt reset
     */
    private function shouldAttemptReset(): bool
    {
        return (time() - $this->lastFailureTime) >= $this->timeout;
    }
    
    /**
     * Get current state
     */
    public function getState(): string
    {
        return $this->state;
    }
    
    /**
     * Get failure count
     */
    public function getFailureCount(): int
    {
        return $this->failureCount;
    }
    
    /**
     * Reset circuit breaker
     */
    public function reset(): void
    {
        $this->failureCount = 0;
        $this->state = 'closed';
    }
}

// Service Communication Examples
class ServiceCommunicationExamples
{
    private RestApiCommunication $restApi;
    private MessageQueueCommunication $messageQueue;
    private EventDrivenCommunication $eventBus;
    private array $circuitBreakers;
    
    public function __construct()
    {
        $this->restApi = new RestApiCommunication();
        $this->messageQueue = new MessageQueueCommunication();
        $this->eventBus = new EventDrivenCommunication();
        $this->circuitBreakers = [];
    }
    
    public function demonstrateRestApi(): void
    {
        echo "REST API Communication Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // GET request
        echo "GET Request to User Service:\n";
        $users = $this->restApi->get('user-service', '/users');
        echo "Status: {$users['status']}\n";
        echo "Data: " . json_encode($users['data']) . "\n\n";
        
        // POST request
        echo "POST Request to User Service:\n";
        $newUser = [
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com'
        ];
        $result = $this->restApi->post('user-service', '/users', $newUser);
        echo "Status: {$result['status']}\n";
        echo "Data: " . json_encode($result['data']) . "\n\n";
        
        // Authenticated request
        echo "Authenticated Request:\n";
        $authResult = $this->restApi
            ->withAuth('Bearer token-12345')
            ->withHeader('X-Request-ID', uniqid())
            ->get('order-service', '/orders');
        echo "Status: {$authResult['status']}\n";
        echo "Data: " . json_encode($authResult['data']) . "\n";
    }
    
    public function demonstrateMessageQueue(): void
    {
        echo "\nMessage Queue Communication Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        // Send messages to queues
        echo "Sending Messages to Queues:\n";
        
        $this->messageQueue->sendToQueue('user_events', [
            'type' => 'user_registered',
            'user_id' => 123,
            'email' => 'john@example.com'
        ]);
        
        $this->messageQueue->sendToQueue('order_events', [
            'type' => 'order_placed',
            'order_id' => 'ORD-001',
            'user_id' => 123,
            'total' => 99.99
        ]);
        
        $this->messageQueue->sendToQueue('notification_queue', [
            'type' => 'email',
            'to' => 'john@example.com',
            'subject' => 'Welcome!',
            'body' => 'Thank you for registering.'
        ]);
        
        echo "Messages sent to queues.\n\n";
        
        // Publish to exchange
        echo "Publishing to Exchange:\n";
        
        $this->messageQueue->publish('events', 'user.registered', [
            'user_id' => 124,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com'
        ]);
        
        $this->messageQueue->publish('events', 'order.placed', [
            'order_id' => 'ORD-002',
            'user_id' => 124,
            'total' => 149.99
        ]);
        
        echo "Events published to exchange.\n\n";
        
        // Consume messages
        echo "Consuming Messages:\n";
        
        $this->messageQueue->consume('user_events', function($message) {
            echo "User Event: {$message['payload']['type']} - User {$message['payload']['user_id']}\n";
        });
        
        $this->messageQueue->consume('notification_queue', function($message) {
            echo "Notification: {$message['payload']['type']} - To: {$message['payload']['to']}\n";
        });
        
        // Show queue status
        echo "\nQueue Status:\n";
        $queues = $this->messageQueue->getQueues();
        foreach ($queues as $queueName => $queue) {
            $messageCount = count($queue['messages']);
            echo "$queueName: $messageCount messages\n";
        }
    }
    
    public function demonstrateEventBus(): void
    {
        echo "\nEvent-Driven Communication Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        // Subscribe to events
        echo "Subscribing to Events:\n";
        
        $this->eventBus->on('user.created', function($event) {
            echo "User Created Event: {$event['data']['name']} ({$event['data']['email']})\n";
        });
        
        $this->eventBus->on('order.placed', function($event) {
            echo "Order Placed Event: Order {$event['data']['order_id']} - \${$event['data']['total']}\n";
        });
        
        $this->eventBus->on('payment.processed', function($event) {
            echo "Payment Processed Event: Payment {$event['data']['payment_id']} - \${$event['data']['amount']}\n";
        });
        
        // Emit events
        echo "\nEmitting Events:\n";
        
        $this->eventBus->emit('user.created', [
            'user_id' => 125,
            'name' => 'Bob Wilson',
            'email' => 'bob@example.com'
        ]);
        
        $this->eventBus->emit('order.placed', [
            'order_id' => 'ORD-003',
            'user_id' => 125,
            'total' => 79.99
        ]);
        
        $this->eventBus->emit('payment.processed', [
            'payment_id' => 'PAY-001',
            'order_id' => 'ORD-003',
            'amount' => 79.99,
            'status' => 'completed'
        ]);
        
        // Show event history
        echo "\nEvent History:\n";
        $events = $this->eventBus->getEventHistory();
        foreach ($events as $event) {
            echo "{$event['name']}: {$event['timestamp']}\n";
        }
        
        // Show subscriptions
        echo "\nActive Subscriptions:\n";
        $subscriptions = $this->eventBus->getSubscriptions();
        foreach ($subscriptions as $subscription) {
            echo "Event: {$subscription['event']} - ID: {$subscription['id']}\n";
        }
    }
    
    public function demonstrateCircuitBreaker(): void
    {
        echo "\nCircuit Breaker Pattern Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Create circuit breakers
        $userCircuitBreaker = new CircuitBreaker('user-service', 3, 10);
        $orderCircuitBreaker = new CircuitBreaker('order-service', 2, 15);
        
        $this->circuitBreakers['user-service'] = $userCircuitBreaker;
        $this->circuitBreakers['order-service'] = $orderCircuitBreaker;
        
        // Simulate operations
        echo "Simulating Service Operations:\n";
        
        $operations = [
            ['service' => 'user-service', 'success' => true],
            ['service' => 'user-service', 'success' => true],
            ['service' => 'user-service', 'success' => false],
            ['service' => 'user-service', 'success' => false],
            ['service' => 'user-service', 'success' => false],
            ['service' => 'order-service', 'success' => true],
            ['service' => 'order-service', 'success' => false],
            ['service' => 'order-service', 'success' => false]
        ];
        
        foreach ($operations as $op) {
            $service = $op['service'];
            $circuitBreaker = $this->circuitBreakers[$service];
            
            echo "Operation on $service: ";
            
            try {
                $result = $circuitBreaker->execute(function() use ($op) {
                    if ($op['success']) {
                        return ['status' => 'success', 'data' => 'operation completed'];
                    } else {
                        throw new Exception('Service unavailable');
                    }
                });
                
                echo "SUCCESS - " . json_encode($result) . "\n";
            } catch (Exception $e) {
                echo "FAILED - {$e->getMessage()}\n";
            }
            
            echo "  Circuit State: {$circuitBreaker->getState()}\n";
            echo "  Failure Count: {$circuitBreaker->getFailureCount()}\n\n";
        }
    }
    
    public function demonstratePatterns(): void
    {
        echo "\nCommunication Patterns Summary\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Synchronous Communication (REST API):\n";
        echo "   • Direct request-response\n";
        echo "   • Immediate feedback\n";
        echo "   • Tight coupling\n";
        echo "   • Good for CRUD operations\n\n";
        
        echo "2. Asynchronous Communication (Message Queue):\n";
        echo "   • Decoupled services\n";
        echo "   • Reliable delivery\n";
        echo "   • Load balancing\n";
        echo "   • Good for background processing\n\n";
        
        echo "3. Event-Driven Communication:\n";
        echo "   • Loose coupling\n";
        echo "   • Scalable\n";
        echo "   • Event sourcing\n";
        echo "   • Good for notifications\n\n";
        
        echo "4. Circuit Breaker Pattern:\n";
        echo "   • Fault tolerance\n";
        echo "   • Prevents cascading failures\n";
        echo "   • Automatic recovery\n";
        echo "   • Service resilience\n\n";
        
        echo "Choosing the Right Pattern:\n";
        echo "• Use REST for direct operations\n";
        echo "• Use Message Queue for reliability\n";
        echo "• Use Events for loose coupling\n";
        echo "• Use Circuit Breaker for resilience";
    }
    
    public function runAllExamples(): void
    {
        echo "Service Communication Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateRestApi();
        $this->demonstrateMessageQueue();
        $this->demonstrateEventBus();
        $this->demonstrateCircuitBreaker();
        $this->demonstratePatterns();
    }
}

// Main execution
function runServiceCommunicationDemo(): void
{
    $examples = new ServiceCommunicationExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runServiceCommunicationDemo();
}
?>
