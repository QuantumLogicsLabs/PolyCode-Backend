<?php
/**
 * Microservices Basics
 * 
 * Introduction to microservices architecture concepts and implementation.
 */

// Microservices Architecture Overview
class MicroservicesArchitecture
{
    private array $services = [];
    private array $serviceRegistry = [];
    private array $loadBalancers = [];
    
    public function __construct()
    {
        $this->initializeServices();
        $this->setupServiceRegistry();
        $this->configureLoadBalancers();
    }
    
    /**
     * Initialize microservices
     */
    private function initializeServices(): void
    {
        $this->services = [
            'user-service' => [
                'name' => 'User Service',
                'port' => 8001,
                'endpoints' => [
                    'GET /users',
                    'POST /users',
                    'GET /users/{id}',
                    'PUT /users/{id}',
                    'DELETE /users/{id}'
                ],
                'database' => 'user_db',
                'dependencies' => ['notification-service']
            ],
            'order-service' => [
                'name' => 'Order Service',
                'port' => 8002,
                'endpoints' => [
                    'GET /orders',
                    'POST /orders',
                    'GET /orders/{id}',
                    'PUT /orders/{id}'
                ],
                'database' => 'order_db',
                'dependencies' => ['user-service', 'product-service', 'payment-service']
            ],
            'product-service' => [
                'name' => 'Product Service',
                'port' => 8003,
                'endpoints' => [
                    'GET /products',
                    'POST /products',
                    'GET /products/{id}',
                    'PUT /products/{id}',
                    'DELETE /products/{id}'
                ],
                'database' => 'product_db',
                'dependencies' => []
            ],
            'notification-service' => [
                'name' => 'Notification Service',
                'port' => 8004,
                'endpoints' => [
                    'POST /notifications/email',
                    'POST /notifications/sms',
                    'POST /notifications/push'
                ],
                'database' => 'notification_db',
                'dependencies' => []
            ],
            'payment-service' => [
                'name' => 'Payment Service',
                'port' => 8005,
                'endpoints' => [
                    'POST /payments/process',
                    'GET /payments/{id}',
                    'POST /payments/refund'
                ],
                'database' => 'payment_db',
                'dependencies' => []
            ]
        ];
    }
    
    /**
     * Setup service registry
     */
    private function setupServiceRegistry(): void
    {
        foreach ($this->services as $serviceId => $service) {
            $this->serviceRegistry[$serviceId] = [
                'host' => 'localhost',
                'port' => $service['port'],
                'health_check' => "http://localhost:{$service['port']}/health",
                'registered_at' => time(),
                'last_heartbeat' => time(),
                'status' => 'active'
            ];
        }
    }
    
    /**
     * Configure load balancers
     */
    private function configureLoadBalancers(): void
    {
        $this->loadBalancers = [
            'user-service' => [
                'strategy' => 'round_robin',
                'instances' => ['localhost:8001', 'localhost:8011', 'localhost:8021'],
                'health_check_interval' => 30
            ],
            'order-service' => [
                'strategy' => 'least_connections',
                'instances' => ['localhost:8002', 'localhost:8012'],
                'health_check_interval' => 30
            ]
        ];
    }
    
    /**
     * Get service information
     */
    public function getService(string $serviceId): ?array
    {
        return $this->services[$serviceId] ?? null;
    }
    
    /**
     * Get all services
     */
    public function getAllServices(): array
    {
        return $this->services;
    }
    
    /**
     * Get service registry
     */
    public function getServiceRegistry(): array
    {
        return $this->serviceRegistry;
    }
    
    /**
     * Register service
     */
    public function registerService(string $serviceId, array $serviceInfo): bool
    {
        $this->serviceRegistry[$serviceId] = array_merge($serviceInfo, [
            'registered_at' => time(),
            'last_heartbeat' => time(),
            'status' => 'active'
        ]);
        
        return true;
    }
    
    /**
     * Unregister service
     */
    public function unregisterService(string $serviceId): bool
    {
        if (isset($this->serviceRegistry[$serviceId])) {
            $this->serviceRegistry[$serviceId]['status'] = 'inactive';
            return true;
        }
        
        return false;
    }
    
    /**
     * Get load balancer for service
     */
    public function getLoadBalancer(string $serviceId): ?array
    {
        return $this->loadBalancers[$serviceId] ?? null;
    }
}

// Service Communication Patterns
class ServiceCommunication
{
    private array $messageQueue;
    private array $eventBus;
    private array $apiGateway;
    
    public function __construct()
    {
        $this->initializeMessageQueue();
        $this->initializeEventBus();
        $this->initializeApiGateway();
    }
    
    /**
     * Initialize message queue
     */
    private function initializeMessageQueue(): void
    {
        $this->messageQueue = [
            'queues' => [
                'user_events' => [
                    'name' => 'user_events',
                    'type' => 'topic',
                    'subscribers' => ['notification-service', 'order-service']
                ],
                'order_events' => [
                    'name' => 'order_events',
                    'type' => 'topic',
                    'subscribers' => ['notification-service', 'payment-service']
                ],
                'product_events' => [
                    'name' => 'product_events',
                    'type' => 'topic',
                    'subscribers' => ['order-service']
                ]
            ]
        ];
    }
    
    /**
     * Initialize event bus
     */
    private function initializeEventBus(): void
    {
        $this->eventBus = [
            'handlers' => [],
            'middleware' => [],
            'events' => []
        ];
    }
    
    /**
     * Initialize API Gateway
     */
    private function initializeApiGateway(): void
    {
        $this->apiGateway = [
            'routes' => [
                '/api/v1/users/*' => 'user-service',
                '/api/v1/orders/*' => 'order-service',
                '/api/v1/products/*' => 'product-service',
                '/api/v1/notifications/*' => 'notification-service',
                '/api/v1/payments/*' => 'payment-service'
            ],
            'middleware' => [
                'authentication',
                'rate_limiting',
                'logging',
                'cors'
            ],
            'rate_limits' => [
                'user-service' => 1000,
                'order-service' => 500,
                'product-service' => 2000,
                'notification-service' => 100,
                'payment-service' => 100
            ]
        ];
    }
    
    /**
     * Send message to queue
     */
    public function sendMessage(string $queue, array $message): bool
    {
        if (!isset($this->messageQueue['queues'][$queue])) {
            return false;
        }
        
        $messageData = [
            'id' => uniqid('msg_'),
            'queue' => $queue,
            'payload' => $message,
            'timestamp' => time(),
            'status' => 'pending'
        ];
        
        // Simulate message queuing
        $this->messageQueue['messages'][] = $messageData;
        
        return true;
    }
    
    /**
     * Publish event
     */
    public function publishEvent(string $event, array $data): bool
    {
        $eventData = [
            'id' => uniqid('event_'),
            'type' => $event,
            'data' => $data,
            'timestamp' => time(),
            'version' => '1.0'
        ];
        
        $this->eventBus['events'][] = $eventData;
        
        // Notify subscribers
        foreach ($this->eventBus['handlers'] as $handler) {
            if ($handler['event'] === $event) {
                call_user_func($handler['callback'], $eventData);
            }
        }
        
        return true;
    }
    
    /**
     * Subscribe to event
     */
    public function subscribe(string $event, callable $handler): string
    {
        $subscriptionId = uniqid('sub_');
        
        $this->eventBus['handlers'][] = [
            'id' => $subscriptionId,
            'event' => $event,
            'callback' => $handler
        ];
        
        return $subscriptionId;
    }
    
    /**
     * Route request through API Gateway
     */
    public function routeRequest(string $method, string $path, array $data): array
    {
        // Find matching service
        $service = null;
        foreach ($this->apiGateway['routes'] as $route => $serviceName) {
            if ($this->matchRoute($route, $path)) {
                $service = $serviceName;
                break;
            }
        }
        
        if (!$service) {
            return [
                'status' => 404,
                'message' => 'Service not found'
            ];
        }
        
        // Apply middleware
        foreach ($this->apiGateway['middleware'] as $middleware) {
            $result = $this->applyMiddleware($middleware, $method, $path, $data);
            if (!$result['allowed']) {
                return $result;
            }
        }
        
        // Forward to service
        return $this->forwardToService($service, $method, $path, $data);
    }
    
    /**
     * Match route pattern
     */
    private function matchRoute(string $pattern, string $path): bool
    {
        $pattern = str_replace('*', '.*', $pattern);
        return preg_match("/^$pattern$/", $path);
    }
    
    /**
     * Apply middleware
     */
    private function applyMiddleware(string $middleware, string $method, string $path, array $data): array
    {
        switch ($middleware) {
            case 'authentication':
                return $this->authenticate($data);
            case 'rate_limiting':
                return $this->checkRateLimit($data);
            case 'logging':
                return $this->logRequest($method, $path, $data);
            case 'cors':
                return $this->handleCors($method, $data);
            default:
                return ['allowed' => true];
        }
    }
    
    /**
     * Authentication middleware
     */
    private function authenticate(array $data): array
    {
        $token = $data['headers']['Authorization'] ?? '';
        
        if (empty($token)) {
            return [
                'allowed' => false,
                'status' => 401,
                'message' => 'Authentication required'
            ];
        }
        
        // Simulate token validation
        if (strlen($token) < 10) {
            return [
                'allowed' => false,
                'status' => 401,
                'message' => 'Invalid token'
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Rate limiting middleware
     */
    private function checkRateLimit(array $data): array
    {
        $clientId = $data['client_ip'] ?? 'unknown';
        
        // Simulate rate limiting
        $currentRequests = $this->getCurrentRequests($clientId);
        $limit = $this->apiGateway['rate_limits']['user-service'] ?? 1000;
        
        if ($currentRequests >= $limit) {
            return [
                'allowed' => false,
                'status' => 429,
                'message' => 'Rate limit exceeded'
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Logging middleware
     */
    private function logRequest(string $method, string $path, array $data): array
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $method,
            'path' => $path,
            'client_ip' => $data['client_ip'] ?? 'unknown',
            'user_agent' => $data['headers']['User-Agent'] ?? 'unknown'
        ];
        
        // Simulate logging
        error_log(json_encode($logEntry));
        
        return ['allowed' => true];
    }
    
    /**
     * CORS middleware
     */
    private function handleCors(string $method, array $data): array
    {
        $origin = $data['headers']['Origin'] ?? '';
        $allowedOrigins = ['http://localhost:3000', 'https://example.com'];
        
        if (!in_array($origin, $allowedOrigins) && !empty($origin)) {
            return [
                'allowed' => false,
                'status' => 403,
                'message' => 'CORS policy violation'
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Forward request to service
     */
    private function forwardToService(string $service, string $method, string $path, array $data): array
    {
        // Simulate service call
        return [
            'status' => 200,
            'service' => $service,
            'path' => $path,
            'data' => $data,
            'timestamp' => time()
        ];
    }
    
    /**
     * Get current requests for client
     */
    private function getCurrentRequests(string $clientId): int
    {
        // Simulate request counting
        return rand(0, 100);
    }
}

// Data Management in Microservices
class MicroservicesDataManagement
{
    private array $databases = [];
    private array $dataConsistency;
    private array $eventSourcing;
    
    public function __construct()
    {
        $this->initializeDatabases();
        $this->setupDataConsistency();
        $this->initializeEventSourcing();
    }
    
    /**
     * Initialize databases for each service
     */
    private function initializeDatabases(): void
    {
        $this->databases = [
            'user_db' => [
                'type' => 'PostgreSQL',
                'tables' => ['users', 'user_profiles', 'user_sessions'],
                'connection' => 'postgresql://user:pass@localhost:5432/user_db'
            ],
            'order_db' => [
                'type' => 'MySQL',
                'tables' => ['orders', 'order_items', 'order_status'],
                'connection' => 'mysql://user:pass@localhost:3306/order_db'
            ],
            'product_db' => [
                'type' => 'MongoDB',
                'collections' => ['products', 'categories', 'inventory'],
                'connection' => 'mongodb://localhost:27017/product_db'
            ],
            'notification_db' => [
                'type' => 'Redis',
                'data_types' => ['notifications', 'templates', 'queues'],
                'connection' => 'redis://localhost:6379/notification_db'
            ],
            'payment_db' => [
                'type' => 'PostgreSQL',
                'tables' => ['payments', 'transactions', 'refunds'],
                'connection' => 'postgresql://user:pass@localhost:5432/payment_db'
            ]
        ];
    }
    
    /**
     * Setup data consistency strategies
     */
    private function setupDataConsistency(): void
    {
        $this->dataConsistency = [
            'strategies' => [
                'eventual_consistency' => [
                    'description' => 'Data becomes consistent over time',
                    'use_case' => 'High availability requirements',
                    'implementation' => 'Event-driven updates'
                ],
                'strong_consistency' => [
                    'description' => 'Data is always consistent',
                    'use_case' => 'Critical operations',
                    'implementation' => 'Two-phase commit'
                ],
                'sagas' => [
                    'description' => 'Long-running transactions',
                    'use_case' => 'Distributed transactions',
                    'implementation' => 'Compensating transactions'
                ]
            ]
        ];
    }
    
    /**
     * Initialize event sourcing
     */
    private function initializeEventSourcing(): void
    {
        $this->eventSourcing = [
            'event_store' => [
                'table' => 'events',
                'fields' => ['id', 'aggregate_id', 'event_type', 'event_data', 'version', 'timestamp'],
                'indexes' => ['aggregate_id', 'timestamp', 'event_type']
            ],
            'snapshots' => [
                'table' => 'snapshots',
                'fields' => ['id', 'aggregate_id', 'snapshot_data', 'version', 'timestamp'],
                'indexes' => ['aggregate_id', 'version']
            ],
            'aggregates' => [
                'User' => ['created', 'updated', 'deleted'],
                'Order' => ['created', 'updated', 'cancelled', 'completed'],
                'Product' => ['created', 'updated', 'deleted', 'price_changed']
            ]
        ];
    }
    
    /**
     * Get database for service
     */
    public function getDatabase(string $serviceName): ?array
    {
        $dbKey = str_replace('-service', '_db', $serviceName);
        return $this->databases[$dbKey] ?? null;
    }
    
    /**
     * Get all databases
     */
    public function getAllDatabases(): array
    {
        return $this->databases;
    }
    
    /**
     * Get data consistency strategies
     */
    public function getDataConsistencyStrategies(): array
    {
        return $this->dataConsistency;
    }
    
    /**
     * Get event sourcing configuration
     */
    public function getEventSourcing(): array
    {
        return $this->eventSourcing;
    }
    
    /**
     * Create event
     */
    public function createEvent(string $aggregateId, string $eventType, array $data): string
    {
        $eventId = uniqid('event_');
        
        $event = [
            'id' => $eventId,
            'aggregate_id' => $aggregateId,
            'event_type' => $eventType,
            'event_data' => json_encode($data),
            'version' => $this->getNextVersion($aggregateId),
            'timestamp' => time()
        ];
        
        // Store event
        $this->storeEvent($event);
        
        return $eventId;
    }
    
    /**
     * Store event in event store
     */
    private function storeEvent(array $event): void
    {
        // Simulate event storage
        $this->eventSourcing['stored_events'][] = $event;
    }
    
    /**
     * Get next version for aggregate
     */
    private function getNextVersion(string $aggregateId): int
    {
        // Simulate version calculation
        return count($this->eventSourcing['stored_events'] ?? []) + 1;
    }
    
    /**
     * Get events for aggregate
     */
    public function getEvents(string $aggregateId): array
    {
        return array_filter(
            $this->eventSourcing['stored_events'] ?? [],
            function($event) use ($aggregateId) {
                return $event['aggregate_id'] === $aggregateId;
            }
        );
    }
}

// Microservices Examples
class MicroservicesExamples
{
    private MicroservicesArchitecture $architecture;
    private ServiceCommunication $communication;
    private MicroservicesDataManagement $dataManagement;
    
    public function __construct()
    {
        $this->architecture = new MicroservicesArchitecture();
        $this->communication = new ServiceCommunication();
        $this->dataManagement = new MicroservicesDataManagement();
    }
    
    public function demonstrateArchitecture(): void
    {
        echo "Microservices Architecture Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Show services
        $services = $this->architecture->getAllServices();
        
        echo "Available Services:\n";
        foreach ($services as $serviceId => $service) {
            echo "$serviceId ({$service['name']}):\n";
            echo "  Port: {$service['port']}\n";
            echo "  Database: {$service['database']}\n";
            echo "  Dependencies: " . implode(', ', $service['dependencies']) . "\n";
            echo "  Endpoints: " . implode(', ', array_slice($service['endpoints'], 0, 3)) . "...\n\n";
        }
        
        // Show service registry
        $registry = $this->architecture->getServiceRegistry();
        
        echo "Service Registry:\n";
        foreach ($registry as $serviceId => $info) {
            echo "$serviceId: {$info['host']}:{$info['port']} ({$info['status']})\n";
        }
        
        // Show load balancers
        echo "\nLoad Balancers:\n";
        foreach ($services as $serviceId => $service) {
            $loadBalancer = $this->architecture->getLoadBalancer($serviceId);
            if ($loadBalancer) {
                echo "$serviceId:\n";
                echo "  Strategy: {$loadBalancer['strategy']}\n";
                echo "  Instances: " . implode(', ', $loadBalancer['instances']) . "\n";
            }
        }
    }
    
    public function demonstrateCommunication(): void
    {
        echo "\nService Communication Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Event-driven communication
        echo "Event-Driven Communication:\n";
        
        // Subscribe to events
        $this->communication->subscribe('user_created', function($event) {
            echo "User created event received: {$event['data']['name']}\n";
        });
        
        $this->communication->subscribe('order_placed', function($event) {
            echo "Order placed event received: Order #{$event['data']['order_id']}\n";
        });
        
        // Publish events
        $this->communication->publishEvent('user_created', [
            'user_id' => 123,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $this->communication->publishEvent('order_placed', [
            'order_id' => 'ORD-001',
            'user_id' => 123,
            'total' => 99.99
        ]);
        
        // Message queue
        echo "\nMessage Queue Communication:\n";
        $this->communication->sendMessage('user_events', [
            'type' => 'email_verification',
            'user_id' => 123,
            'email' => 'john@example.com'
        ]);
        
        $this->communication->sendMessage('order_events', [
            'type' => 'order_confirmation',
            'order_id' => 'ORD-001',
            'user_id' => 123
        ]);
        
        // API Gateway routing
        echo "\nAPI Gateway Routing:\n";
        
        $request = [
            'method' => 'POST',
            'path' => '/api/v1/users',
            'data' => ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
            'headers' => ['Authorization' => 'Bearer valid-token-12345'],
            'client_ip' => '192.168.1.100'
        ];
        
        $response = $this->communication->routeRequest(
            $request['method'],
            $request['path'],
            $request
        );
        
        echo "Request: {$request['method']} {$request['path']}\n";
        echo "Response: {$response['status']} - Service: {$response['service']}\n";
    }
    
    public function demonstrateDataManagement(): void
    {
        echo "\nData Management Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Show databases
        $databases = $this->dataManagement->getAllDatabases();
        
        echo "Service Databases:\n";
        foreach ($databases as $dbName => $config) {
            echo "$dbName:\n";
            echo "  Type: {$config['type']}\n";
            echo "  Tables/Collections: " . implode(', ', array_slice($config['tables'] ?? $config['collections'] ?? [], 0, 3)) . "...\n";
            echo "  Connection: {$config['connection']}\n\n";
        }
        
        // Show consistency strategies
        $strategies = $this->dataManagement->getDataConsistencyStrategies();
        
        echo "Data Consistency Strategies:\n";
        foreach ($strategies['strategies'] as $strategy => $details) {
            echo "$strategy:\n";
            echo "  Description: {$details['description']}\n";
            echo "  Use Case: {$details['use_case']}\n";
            echo "  Implementation: {$details['implementation']}\n\n";
        }
        
        // Event sourcing
        echo "Event Sourcing:\n";
        
        // Create events
        $eventId1 = $this->dataManagement->createEvent('user-123', 'created', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $eventId2 = $this->dataManagement->createEvent('user-123', 'updated', [
            'name' => 'John Smith',
            'email' => 'john.smith@example.com'
        ]);
        
        echo "Created events: $eventId1, $eventId2\n";
        
        // Get events
        $events = $this->dataManagement->getEvents('user-123');
        
        echo "Events for user-123:\n";
        foreach ($events as $event) {
            echo "  {$event['event_type']}: {$event['timestamp']}\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nMicroservices Best Practices\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. Service Design:\n";
        echo "   • Single Responsibility Principle\n";
        echo "   • Bounded Contexts\n";
        echo "   • API-First Design\n";
        echo "   • Versioning Strategy\n";
        echo "   • Documentation\n\n";
        
        echo "2. Communication:\n";
        echo "   • Use appropriate patterns (REST, Events, Queues)\n";
        echo "   • Implement circuit breakers\n";
        echo "   • Use service mesh\n";
        echo "   • Handle failures gracefully\n";
        echo "   • Monitor communication\n\n";
        
        echo "3. Data Management:\n";
        echo "   • Database per service\n";
        echo "   • Choose consistency strategy wisely\n";
        echo "   • Implement event sourcing\n";
        echo "   • Handle data migration\n";
        echo "   • Plan for data synchronization\n\n";
        
        echo "4. Deployment:\n";
        echo "   • Containerize services\n";
        echo "   • Use CI/CD pipelines\n";
        echo "   • Implement blue-green deployment\n";
        echo "   • Use infrastructure as code\n";
        echo "   • Monitor deployments\n\n";
        
        echo "5. Monitoring:\n";
        echo "   • Implement distributed tracing\n";
        echo "   • Use structured logging\n";
        echo "   • Monitor key metrics\n";
        echo "   • Set up alerts\n";
        echo "   • Use dashboards";
    }
    
    public function runAllExamples(): void
    {
        echo "Microservices Architecture Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateArchitecture();
        $this->demonstrateCommunication();
        $this->demonstrateDataManagement();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runMicroservicesBasicsDemo(): void
{
    $examples = new MicroservicesExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runMicroservicesBasicsDemo();
}
?>
