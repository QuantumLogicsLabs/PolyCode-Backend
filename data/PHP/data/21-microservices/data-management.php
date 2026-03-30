<?php
/**
 * Data Management in Microservices
 * 
 * Strategies for managing data across distributed services.
 */

// Database Per Service Pattern
class DatabasePerService
{
    private array $databases;
    private array $connections;
    
    public function __construct()
    {
        $this->initializeDatabases();
        $this->establishConnections();
    }
    
    /**
     * Initialize service databases
     */
    private function initializeDatabases(): void
    {
        $this->databases = [
            'user_service' => [
                'name' => 'user_db',
                'type' => 'PostgreSQL',
                'host' => 'localhost',
                'port' => 5432,
                'schema' => [
                    'users' => [
                        'id' => 'BIGINT PRIMARY KEY',
                        'username' => 'VARCHAR(50) UNIQUE NOT NULL',
                        'email' => 'VARCHAR(100) UNIQUE NOT NULL',
                        'password_hash' => 'VARCHAR(255) NOT NULL',
                        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                        'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
                    ],
                    'user_profiles' => [
                        'id' => 'BIGINT PRIMARY KEY',
                        'user_id' => 'BIGINT NOT NULL',
                        'first_name' => 'VARCHAR(50)',
                        'last_name' => 'VARCHAR(50)',
                        'avatar_url' => 'VARCHAR(255)',
                        'bio' => 'TEXT',
                        'FOREIGN KEY (user_id) REFERENCES users(id)'
                    ]
                ]
            ],
            'order_service' => [
                'name' => 'order_db',
                'type' => 'MySQL',
                'host' => 'localhost',
                'port' => 3306,
                'schema' => [
                    'orders' => [
                        'id' => 'BIGINT PRIMARY KEY AUTO_INCREMENT',
                        'user_id' => 'BIGINT NOT NULL',
                        'order_number' => 'VARCHAR(50) UNIQUE NOT NULL',
                        'status' => 'ENUM("pending", "confirmed", "shipped", "delivered", "cancelled")',
                        'total_amount' => 'DECIMAL(10,2) NOT NULL',
                        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                        'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                    ],
                    'order_items' => [
                        'id' => 'BIGINT PRIMARY KEY AUTO_INCREMENT',
                        'order_id' => 'BIGINT NOT NULL',
                        'product_id' => 'BIGINT NOT NULL',
                        'quantity' => 'INT NOT NULL',
                        'price' => 'DECIMAL(10,2) NOT NULL',
                        'FOREIGN KEY (order_id) REFERENCES orders(id)'
                    ]
                ]
            ],
            'product_service' => [
                'name' => 'product_db',
                'type' => 'MongoDB',
                'host' => 'localhost',
                'port' => 27017,
                'collections' => [
                    'products' => [
                        '_id' => 'ObjectId',
                        'name' => 'String',
                        'description' => 'String',
                        'price' => 'Number',
                        'category' => 'String',
                        'stock' => 'Number',
                        'created_at' => 'Date',
                        'updated_at' => 'Date'
                    ],
                    'categories' => [
                        '_id' => 'ObjectId',
                        'name' => 'String',
                        'description' => 'String',
                        'parent_id' => 'ObjectId'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Establish database connections
     */
    private function establishConnections(): void
    {
        foreach ($this->databases as $service => $config) {
            $this->connections[$service] = [
                'status' => 'connected',
                'established_at' => time(),
                'last_query' => null
            ];
        }
    }
    
    /**
     * Get database for service
     */
    public function getDatabase(string $service): ?array
    {
        return $this->databases[$service] ?? null;
    }
    
    /**
     * Get connection for service
     */
    public function getConnection(string $service): ?array
    {
        return $this->connections[$service] ?? null;
    }
    
    /**
     * Execute query on service database
     */
    public function query(string $service, string $sql, array $params = []): array
    {
        if (!isset($this->connections[$service])) {
            throw new Exception("Service $service not connected");
        }
        
        // Simulate query execution
        $this->connections[$service]['last_query'] = [
            'sql' => $sql,
            'params' => $params,
            'executed_at' => time()
        ];
        
        return [
            'success' => true,
            'data' => $this->simulateQueryResult($service, $sql),
            'affected_rows' => rand(0, 10)
        ];
    }
    
    /**
     * Simulate query result
     */
    private function simulateQueryResult(string $service, string $sql): array
    {
        if (strpos($sql, 'SELECT') !== false) {
            if (strpos($sql, 'users') !== false) {
                return [
                    ['id' => 1, 'username' => 'john_doe', 'email' => 'john@example.com'],
                    ['id' => 2, 'username' => 'jane_smith', 'email' => 'jane@example.com']
                ];
            }
        }
        
        return [];
    }
    
    /**
     * Get all databases
     */
    public function getAllDatabases(): array
    {
        return $this->databases;
    }
}

// Event Sourcing Pattern
class EventSourcing
{
    private array $eventStore;
    private array $snapshots;
    private array $aggregates;
    
    public function __construct()
    {
        $this->initializeEventStore();
        $this->initializeSnapshots();
        $this->initializeAggregates();
    }
    
    /**
     * Initialize event store
     */
    private function initializeEventStore(): void
    {
        $this->eventStore = [
            'events' => [],
            'streams' => []
        ];
    }
    
    /**
     * Initialize snapshots
     */
    private function initializeSnapshots(): void
    {
        $this->snapshots = [];
    }
    
    /**
     * Initialize aggregates
     */
    private function initializeAggregates(): void
    {
        $this->aggregates = [
            'User' => [
                'events' => ['UserCreated', 'UserUpdated', 'UserDeleted'],
                'snapshot_frequency' => 100
            ],
            'Order' => [
                'events' => ['OrderCreated', 'OrderUpdated', 'OrderCancelled', 'OrderShipped'],
                'snapshot_frequency' => 50
            ],
            'Product' => [
                'events' => ['ProductCreated', 'ProductUpdated', 'ProductDeleted', 'PriceChanged'],
                'snapshot_frequency' => 75
            ]
        ];
    }
    
    /**
     * Save event
     */
    public function saveEvent(string $aggregateId, string $eventType, array $data, int $expectedVersion = null): string
    {
        $eventId = uniqid('event_');
        
        $event = [
            'id' => $eventId,
            'aggregate_id' => $aggregateId,
            'event_type' => $eventType,
            'event_data' => $data,
            'version' => $this->getNextVersion($aggregateId),
            'timestamp' => time(),
            'metadata' => [
                'user_id' => 'system',
                'correlation_id' => uniqid('corr_')
            ]
        ];
        
        // Check version conflict
        if ($expectedVersion && $event['version'] !== $expectedVersion) {
            throw new Exception("Version conflict: expected $expectedVersion, got {$event['version']}");
        }
        
        $this->eventStore['events'][] = $event;
        
        // Create snapshot if needed
        if ($event['version'] % $this->getSnapshotFrequency($eventType) === 0) {
            $this->createSnapshot($aggregateId, $event['version']);
        }
        
        return $eventId;
    }
    
    /**
     * Get events for aggregate
     */
    public function getEvents(string $aggregateId, int $fromVersion = 0): array
    {
        return array_filter($this->eventStore['events'], function($event) use ($aggregateId, $fromVersion) {
            return $event['aggregate_id'] === $aggregateId && $event['version'] > $fromVersion;
        });
    }
    
    /**
     * Get latest snapshot for aggregate
     */
    public function getSnapshot(string $aggregateId): ?array
    {
        $snapshots = array_filter($this->snapshots, function($snapshot) use ($aggregateId) {
            return $snapshot['aggregate_id'] === $aggregateId;
        });
        
        if (empty($snapshots)) {
            return null;
        }
        
        // Return latest snapshot
        usort($snapshots, function($a, $b) {
            return $b['version'] - $a['version'];
        });
        
        return $snapshots[0];
    }
    
    /**
     * Rebuild aggregate from events
     */
    public function rebuildAggregate(string $aggregateId): array
    {
        // Check for snapshot first
        $snapshot = $this->getSnapshot($aggregateId);
        $state = $snapshot ? $snapshot['state'] : [];
        $fromVersion = $snapshot ? $snapshot['version'] : 0;
        
        // Apply events from version
        $events = $this->getEvents($aggregateId, $fromVersion);
        
        foreach ($events as $event) {
            $state = $this->applyEvent($state, $event);
        }
        
        return $state;
    }
    
    /**
     * Apply event to state
     */
    private function applyEvent(array $state, array $event): array
    {
        switch ($event['event_type']) {
            case 'UserCreated':
                $state = [
                    'id' => $event['aggregate_id'],
                    'username' => $event['event_data']['username'],
                    'email' => $event['event_data']['email'],
                    'created_at' => $event['timestamp']
                ];
                break;
            case 'UserUpdated':
                $state = array_merge($state, $event['event_data']);
                $state['updated_at'] = $event['timestamp'];
                break;
            case 'OrderCreated':
                $state = [
                    'id' => $event['aggregate_id'],
                    'user_id' => $event['event_data']['user_id'],
                    'total' => $event['event_data']['total'],
                    'status' => 'pending',
                    'created_at' => $event['timestamp']
                ];
                break;
            case 'OrderShipped':
                $state['status'] = 'shipped';
                $state['shipped_at'] = $event['timestamp'];
                break;
        }
        
        return $state;
    }
    
    /**
     * Get next version for aggregate
     */
    private function getNextVersion(string $aggregateId): int
    {
        $events = $this->getEvents($aggregateId);
        return count($events) + 1;
    }
    
    /**
     * Get snapshot frequency for event type
     */
    private function getSnapshotFrequency(string $eventType): int
    {
        foreach ($this->aggregates as $aggregate => $config) {
            if (in_array($eventType, $config['events'])) {
                return $config['snapshot_frequency'];
            }
        }
        
        return 100; // Default frequency
    }
    
    /**
     * Create snapshot
     */
    private function createSnapshot(string $aggregateId, int $version): void
    {
        $state = $this->rebuildAggregate($aggregateId);
        
        $snapshot = [
            'id' => uniqid('snapshot_'),
            'aggregate_id' => $aggregateId,
            'state' => $state,
            'version' => $version,
            'created_at' => time()
        ];
        
        $this->snapshots[] = $snapshot;
    }
    
    /**
     * Get all events
     */
    public function getAllEvents(): array
    {
        return $this->eventStore['events'];
    }
    
    /**
     * Get all snapshots
     */
    public function getAllSnapshots(): array
    {
        return $this->snapshots;
    }
}

// CQRS Pattern
class CQRS
{
    private array $writeDatabase;
    private array $readDatabase;
    private array $eventHandlers;
    
    public function __construct()
    {
        $this->initializeWriteDatabase();
        $this->initializeReadDatabase();
        $this->initializeEventHandlers();
    }
    
    /**
     * Initialize write database
     */
    private function initializeWriteDatabase(): void
    {
        $this->writeDatabase = [
            'users' => [],
            'orders' => [],
            'products' => []
        ];
    }
    
    /**
     * Initialize read database
     */
    private function initializeReadDatabase(): void
    {
        $this->readDatabase = [
            'user_views' => [],
            'order_views' => [],
            'product_views' => [],
            'user_order_views' => []
        ];
    }
    
    /**
     * Initialize event handlers
     */
    private function initializeEventHandlers(): void
    {
        $this->eventHandlers = [
            'UserCreated' => [$this, 'handleUserCreated'],
            'UserUpdated' => [$this, 'handleUserUpdated'],
            'OrderCreated' => [$this, 'handleOrderCreated'],
            'OrderUpdated' => [$this, 'handleOrderUpdated'],
            'ProductCreated' => [$this, 'handleProductCreated'],
            'ProductUpdated' => [$this, 'handleProductUpdated']
        ];
    }
    
    /**
     * Handle command (write operation)
     */
    public function handleCommand(string $commandType, array $data): array
    {
        switch ($commandType) {
            case 'CreateUser':
                return $this->createUser($data);
            case 'UpdateUser':
                return $this->updateUser($data);
            case 'CreateOrder':
                return $this->createOrder($data);
            case 'UpdateOrder':
                return $this->updateOrder($data);
            default:
                throw new Exception("Unknown command type: $commandType");
        }
    }
    
    /**
     * Handle query (read operation)
     */
    public function handleQuery(string $queryType, array $parameters = []): array
    {
        switch ($queryType) {
            case 'GetUser':
                return $this->getUser($parameters['id']);
            case 'GetUsers':
                return $this->getUsers($parameters);
            case 'GetOrders':
                return $this->getOrders($parameters);
            case 'GetUserOrders':
                return $this->getUserOrders($parameters);
            default:
                throw new Exception("Unknown query type: $queryType");
        }
    }
    
    /**
     * Create user command
     */
    public function createUser(array $data): array
    {
        $userId = uniqid('user_');
        
        $user = [
            'id' => $userId,
            'username' => $data['username'],
            'email' => $data['email'],
            'created_at' => time(),
            'version' => 1
        ];
        
        $this->writeDatabase['users'][$userId] = $user;
        
        // Emit event
        $this->emitEvent('UserCreated', $user);
        
        return ['success' => true, 'user_id' => $userId];
    }
    
    /**
     * Update user command
     */
    public function updateUser(array $data): array
    {
        $userId = $data['id'];
        
        if (!isset($this->writeDatabase['users'][$userId])) {
            throw new Exception("User not found: $userId");
        }
        
        $user = $this->writeDatabase['users'][$userId];
        $user = array_merge($user, $data);
        $user['updated_at'] = time();
        $user['version']++;
        
        $this->writeDatabase['users'][$userId] = $user;
        
        // Emit event
        $this->emitEvent('UserUpdated', $user);
        
        return ['success' => true];
    }
    
    /**
     * Create order command
     */
    public function createOrder(array $data): array
    {
        $orderId = uniqid('order_');
        
        $order = [
            'id' => $orderId,
            'user_id' => $data['user_id'],
            'total' => $data['total'],
            'status' => 'pending',
            'created_at' => time(),
            'version' => 1
        ];
        
        $this->writeDatabase['orders'][$orderId] = $order;
        
        // Emit event
        $this->emitEvent('OrderCreated', $order);
        
        return ['success' => true, 'order_id' => $orderId];
    }
    
    /**
     * Get user query
     */
    public function getUser(string $userId): ?array
    {
        return $this->readDatabase['user_views'][$userId] ?? null;
    }
    
    /**
     * Get users query
     */
    public function getUsers(array $filters = []): array
    {
        $users = array_values($this->readDatabase['user_views']);
        
        if (isset($filters['limit'])) {
            $users = array_slice($users, 0, $filters['limit']);
        }
        
        return $users;
    }
    
    /**
     * Get orders query
     */
    public function getOrders(array $filters = []): array
    {
        $orders = array_values($this->readDatabase['order_views']);
        
        if (isset($filters['user_id'])) {
            $orders = array_filter($orders, function($order) use ($filters) {
                return $order['user_id'] === $filters['user_id'];
            });
        }
        
        return array_values($orders);
    }
    
    /**
     * Get user orders query
     */
    public function getUserOrders(array $filters): array
    {
        return array_values($this->readDatabase['user_order_views'] ?? []);
    }
    
    /**
     * Emit event
     */
    public function emitEvent(string $eventType, array $data): void
    {
        $event = [
            'type' => $eventType,
            'data' => $data,
            'timestamp' => time()
        ];
        
        // Handle event
        if (isset($this->eventHandlers[$eventType])) {
            call_user_func($this->eventHandlers[$eventType], $event);
        }
    }
    
    /**
     * Handle UserCreated event
     */
    public function handleUserCreated(array $event): void
    {
        $user = $event['data'];
        
        // Update read model
        $this->readDatabase['user_views'][$user['id']] = $user;
    }
    
    /**
     * Handle UserUpdated event
     */
    public function handleUserUpdated(array $event): void
    {
        $user = $event['data'];
        
        // Update read model
        $this->readDatabase['user_views'][$user['id']] = $user;
    }
    
    /**
     * Handle OrderCreated event
     */
    public function handleOrderCreated(array $event): void
    {
        $order = $event['data'];
        
        // Update read models
        $this->readDatabase['order_views'][$order['id']] = $order;
        
        // Update denormalized view
        $user = $this->readDatabase['user_views'][$order['user_id']] ?? null;
        if ($user) {
            $this->readDatabase['user_order_views'][] = [
                'user' => $user,
                'order' => $order
            ];
        }
    }
    
    /**
     * Handle OrderUpdated event
     */
    public function handleOrderUpdated(array $event): void
    {
        $order = $event['data'];
        
        // Update read model
        $this->readDatabase['order_views'][$order['id']] = $order;
    }
    
    /**
     * Handle ProductCreated event
     */
    public function handleProductCreated(array $event): void
    {
        $product = $event['data'];
        
        // Update read model
        $this->readDatabase['product_views'][$product['id']] = $product;
    }
    
    /**
     * Handle ProductUpdated event
     */
    public function handleProductUpdated(array $event): void
    {
        $product = $event['data'];
        
        // Update read model
        $this->readDatabase['product_views'][$product['id']] = $product;
    }
    
    /**
     * Get write database
     */
    public function getWriteDatabase(): array
    {
        return $this->writeDatabase;
    }
    
    /**
     * Get read database
     */
    public function getReadDatabase(): array
    {
        return $this->readDatabase;
    }
}

// Data Management Examples
class DataManagementExamples
{
    private DatabasePerService $databasePerService;
    private EventSourcing $eventSourcing;
    private CQRS $cqrs;
    
    public function __construct()
    {
        $this->databasePerService = new DatabasePerService();
        $this->eventSourcing = new EventSourcing();
        $this->cqrs = new CQRS();
    }
    
    public function demonstrateDatabasePerService(): void
    {
        echo "Database Per Service Pattern Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        // Show service databases
        $databases = $this->databasePerService->getAllDatabases();
        
        echo "Service Databases:\n";
        foreach ($databases as $service => $config) {
            echo "$service:\n";
            echo "  Database: {$config['name']}\n";
            echo "  Type: {$config['type']}\n";
            echo "  Host: {$config['host']}:{$config['port']}\n";
            echo "  Tables/Collections:\n";
            
            if ($config['type'] === 'MongoDB') {
                foreach ($config['collections'] as $collection => $fields) {
                    echo "    $collection: " . implode(', ', array_keys($fields)) . "\n";
                }
            } else {
                foreach ($config['schema'] as $table => $fields) {
                    echo "    $table: " . implode(', ', array_keys($fields)) . "\n";
                }
            }
            echo "\n";
        }
        
        // Execute queries
        echo "Executing Queries:\n";
        
        $result1 = $this->databasePerService->query('user_service', 'SELECT * FROM users WHERE username = ?', ['john_doe']);
        echo "User Service Query: {$result1['affected_rows']} rows affected\n";
        
        $result2 = $this->databasePerService->query('order_service', 'SELECT * FROM orders WHERE status = ?', ['pending']);
        echo "Order Service Query: {$result2['affected_rows']} rows affected\n";
        
        // Show connections
        echo "\nDatabase Connections:\n";
        $connections = [
            $this->databasePerService->getConnection('user_service'),
            $this->databasePerService->getConnection('order_service'),
            $this->databasePerService->getConnection('product_service')
        ];
        
        foreach ($connections as $service => $connection) {
            echo "$service: {$connection['status']} (connected at {$connection['established_at']})\n";
        }
    }
    
    public function demonstrateEventSourcing(): void
    {
        echo "\nEvent Sourcing Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Save events
        echo "Saving Events:\n";
        
        $eventId1 = $this->eventSourcing->saveEvent('user-123', 'UserCreated', [
            'username' => 'john_doe',
            'email' => 'john@example.com'
        ]);
        
        $eventId2 = $this->eventSourcing->saveEvent('user-123', 'UserUpdated', [
            'username' => 'john_smith',
            'email' => 'john.smith@example.com'
        ]);
        
        $eventId3 = $this->eventSourcing->saveEvent('order-456', 'OrderCreated', [
            'user_id' => 'user-123',
            'total' => 99.99
        ]);
        
        $eventId4 = $this->eventSourcing->saveEvent('order-456', 'OrderShipped', [
            'tracking_number' => 'TRK123456'
        ]);
        
        echo "Events saved: $eventId1, $eventId2, $eventId3, $eventId4\n\n";
        
        // Rebuild aggregates
        echo "Rebuilding Aggregates:\n";
        
        $userState = $this->eventSourcing->rebuildAggregate('user-123');
        echo "User State:\n";
        echo "  ID: {$userState['id']}\n";
        echo "  Username: {$userState['username']}\n";
        echo "  Email: {$userState['email']}\n";
        echo "  Created: {$userState['created_at']}\n";
        echo "  Updated: {$userState['updated_at']}\n\n";
        
        $orderState = $this->eventSourcing->rebuildAggregate('order-456');
        echo "Order State:\n";
        echo "  ID: {$orderState['id']}\n";
        echo "  User ID: {$orderState['user_id']}\n";
        echo "  Total: {$orderState['total']}\n";
        echo "  Status: {$orderState['status']}\n";
        echo "  Created: {$orderState['created_at']}\n";
        echo "  Shipped: {$orderState['shipped_at']}\n\n";
        
        // Show events
        echo "Event History:\n";
        $events = $this->eventSourcing->getAllEvents();
        foreach ($events as $event) {
            echo "  {$event['event_type']}: {$event['aggregate_id']} (v{$event['version']})\n";
        }
        
        // Show snapshots
        echo "\nSnapshots:\n";
        $snapshots = $this->eventSourcing->getAllSnapshots();
        foreach ($snapshots as $snapshot) {
            echo "  {$snapshot['aggregate_id']}: v{$snapshot['version']} at {$snapshot['created_at']}\n";
        }
    }
    
    public function demonstrateCQRS(): void
    {
        echo "\nCQRS Pattern Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Handle commands (write operations)
        echo "Handling Commands (Write Operations):\n";
        
        $userResult = $this->cqrs->handleCommand('CreateUser', [
            'username' => 'alice_johnson',
            'email' => 'alice@example.com'
        ]);
        echo "User Created: {$userResult['user_id']}\n";
        
        $orderResult = $this->cqrs->handleCommand('CreateOrder', [
            'user_id' => $userResult['user_id'],
            'total' => 149.99
        ]);
        echo "Order Created: {$orderResult['order_id']}\n";
        
        // Handle queries (read operations)
        echo "\nHandling Queries (Read Operations):\n";
        
        $users = $this->cqrs->handleQuery('GetUsers', ['limit' => 10]);
        echo "Users: " . count($users) . " found\n";
        foreach ($users as $user) {
            echo "  {$user['username']} ({$user['email']})\n";
        }
        
        $orders = $this->cqrs->handleQuery('GetOrders', ['user_id' => $userResult['user_id']]);
        echo "\nOrders for user: " . count($orders) . " found\n";
        foreach ($orders as $order) {
            echo "  Order {$order['id']}: \${$order['total']} ({$order['status']})\n";
        }
        
        // Show write database
        echo "\nWrite Database (Command Side):\n";
        $writeDb = $this->cqrs->getWriteDatabase();
        foreach ($writeDb as $table => $data) {
            echo "$table: " . count($data) . " records\n";
        }
        
        // Show read database
        echo "\nRead Database (Query Side):\n";
        $readDb = $this->cqrs->getReadDatabase();
        foreach ($readDb as $view => $data) {
            if (is_array($data)) {
                $count = count($data);
                echo "$view: $count records\n";
            } else {
                echo "$view: optimized view\n";
            }
        }
    }
    
    public function demonstrateDataConsistency(): void
    {
        echo "\nData Consistency Strategies\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Eventual Consistency:\n";
        echo "   • Data becomes consistent over time\n";
        echo "   • High availability\n";
        echo "   • Good for read-heavy workloads\n";
        echo "   • Used in social media, e-commerce\n\n";
        
        echo "2. Strong Consistency:\n";
        echo "   • Data is always consistent\n";
        echo "   • Lower availability\n";
        echo "   • Good for critical operations\n";
        echo "   • Used in banking, inventory\n\n";
        
        echo "3. Saga Pattern:\n";
        echo "   • Long-running transactions\n";
        echo "   • Compensating transactions\n";
        echo "   • Distributed transaction management\n";
        echo "   • Good for complex workflows\n\n";
        
        echo "4. Two-Phase Commit:\n";
        echo "   • Atomic distributed transactions\n";
        echo "   • Coordinator manages commit\n";
        echo "   • High consistency guarantee\n";
        echo "   • Performance overhead\n\n";
        
        echo "Choosing Consistency Strategy:\n";
        echo "• Consider business requirements\n";
        echo "• Evaluate performance needs\n";
        echo "• Analyze failure scenarios\n";
        echo "• Plan for reconciliation\n";
        echo "• Monitor consistency";
    }
    
    public function runAllExamples(): void
    {
        echo "Data Management in Microservices Examples\n";
        echo str_repeat("=", 45) . "\n";
        
        $this->demonstrateDatabasePerService();
        $this->demonstrateEventSourcing();
        $this->demonstrateCQRS();
        $this->demonstrateDataConsistency();
    }
}

// Main execution
function runDataManagementDemo(): void
{
    $examples = new DataManagementExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runDataManagementDemo();
}
?>
