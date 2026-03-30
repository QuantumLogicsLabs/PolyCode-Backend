<?php
/**
 * Service Discovery and Load Balancing
 * 
 * Implementation of service discovery patterns and load balancing strategies.
 */

// Service Registry
class ServiceRegistry
{
    private array $services = [];
    private array $healthChecks = [];
    private array $loadBalancers = [];
    
    public function __construct()
    {
        $this->initializeServices();
        $this->setupHealthChecks();
        $this->configureLoadBalancers();
    }
    
    /**
     * Initialize services
     */
    private function initializeServices(): void
    {
        $this->services = [
            'user-service' => [
                'instances' => [
                    [
                        'id' => 'user-service-1',
                        'host' => 'localhost',
                        'port' => 8001,
                        'status' => 'healthy',
                        'registered_at' => time(),
                        'last_heartbeat' => time(),
                        'metadata' => [
                            'version' => '1.0.0',
                            'region' => 'us-west-1',
                            'zone' => 'us-west-1a'
                        ]
                    ],
                    [
                        'id' => 'user-service-2',
                        'host' => 'localhost',
                        'port' => 8011,
                        'status' => 'healthy',
                        'registered_at' => time(),
                        'last_heartbeat' => time(),
                        'metadata' => [
                            'version' => '1.0.0',
                            'region' => 'us-west-1',
                            'zone' => 'us-west-1b'
                        ]
                    ]
                ],
                'load_balancing' => 'round_robin',
                'health_check' => [
                    'path' => '/health',
                    'interval' => 30,
                    'timeout' => 5,
                    'unhealthy_threshold' => 3
                ]
            ],
            'order-service' => [
                'instances' => [
                    [
                        'id' => 'order-service-1',
                        'host' => 'localhost',
                        'port' => 8002,
                        'status' => 'healthy',
                        'registered_at' => time(),
                        'last_heartbeat' => time(),
                        'metadata' => [
                            'version' => '1.1.0',
                            'region' => 'us-west-1',
                            'zone' => 'us-west-1a'
                        ]
                    ],
                    [
                        'id' => 'order-service-2',
                        'host' => 'localhost',
                        'port' => 8012,
                        'status' => 'healthy',
                        'registered_at' => time(),
                        'last_heartbeat' => time(),
                        'metadata' => [
                            'version' => '1.1.0',
                            'region' => 'us-west-1',
                            'zone' => 'us-west-1b'
                        ]
                    ]
                ],
                'load_balancing' => 'least_connections',
                'health_check' => [
                    'path' => '/health',
                    'interval' => 30,
                    'timeout' => 5,
                    'unhealthy_threshold' => 3
                ]
            ],
            'product-service' => [
                'instances' => [
                    [
                        'id' => 'product-service-1',
                        'host' => 'localhost',
                        'port' => 8003,
                        'status' => 'healthy',
                        'registered_at' => time(),
                        'last_heartbeat' => time(),
                        'metadata' => [
                            'version' => '2.0.0',
                            'region' => 'us-west-1',
                            'zone' => 'us-west-1a'
                        ]
                    ]
                ],
                'load_balancing' => 'weighted_round_robin',
                'health_check' => [
                    'path' => '/health',
                    'interval' => 30,
                    'timeout' => 5,
                    'unhealthy_threshold' => 3
                ]
            ]
        ];
    }
    
    /**
     * Setup health checks
     */
    private function setupHealthChecks(): void
    {
        $this->healthChecks = [
            'http' => [
                'method' => 'GET',
                'expected_status' => 200,
                'timeout' => 5
            ],
            'tcp' => [
                'timeout' => 3
            ],
            'custom' => [
                'script_path' => '/health/check.php',
                'timeout' => 10
            ]
        ];
    }
    
    /**
     * Configure load balancers
     */
    private function configureLoadBalancers(): void
    {
        $this->loadBalancers = [
            'round_robin' => [
                'name' => 'Round Robin',
                'description' => 'Distribute requests evenly across instances',
                'algorithm' => 'sequential'
            ],
            'least_connections' => [
                'name' => 'Least Connections',
                'description' => 'Route to instance with fewest active connections',
                'algorithm' => 'connection_based'
            ],
            'weighted_round_robin' => [
                'name' => 'Weighted Round Robin',
                'description' => 'Distribute based on instance weights',
                'algorithm' => 'weighted_sequential'
            ],
            'random' => [
                'name' => 'Random',
                'description' => 'Select random healthy instance',
                'algorithm' => 'random'
            ],
            'ip_hash' => [
                'name' => 'IP Hash',
                'description' => 'Route based on client IP hash',
                'algorithm' => 'hash_based'
            ]
        ];
    }
    
    /**
     * Register service instance
     */
    public function registerInstance(string $serviceName, array $instance): bool
    {
        if (!isset($this->services[$serviceName])) {
            $this->services[$serviceName] = [
                'instances' => [],
                'load_balancing' => 'round_robin',
                'health_check' => $this->getDefaultHealthCheck()
            ];
        }
        
        $instance['registered_at'] = time();
        $instance['last_heartbeat'] = time();
        $instance['status'] = 'healthy';
        
        $this->services[$serviceName]['instances'][] = $instance;
        
        return true;
    }
    
    /**
     * Unregister service instance
     */
    public function unregisterInstance(string $serviceName, string $instanceId): bool
    {
        if (!isset($this->services[$serviceName])) {
            return false;
        }
        
        $instances = &$this->services[$serviceName]['instances'];
        
        foreach ($instances as $key => $instance) {
            if ($instance['id'] === $instanceId) {
                unset($instances[$key]);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get healthy instances for service
     */
    public function getHealthyInstances(string $serviceName): array
    {
        if (!isset($this->services[$serviceName])) {
            return [];
        }
        
        return array_filter(
            $this->services[$serviceName]['instances'],
            function($instance) {
                return $instance['status'] === 'healthy';
            }
        );
    }
    
    /**
     * Discover service
     */
    public function discoverService(string $serviceName): ?array
    {
        $healthyInstances = $this->getHealthyInstances($serviceName);
        
        if (empty($healthyInstances)) {
            return null;
        }
        
        $loadBalancing = $this->services[$serviceName]['load_balancing'];
        $selectedInstance = $this->selectInstance($healthyInstances, $loadBalancing);
        
        return $selectedInstance;
    }
    
    /**
     * Select instance using load balancing algorithm
     */
    private function selectInstance(array $instances, string $algorithm): array
    {
        switch ($algorithm) {
            case 'round_robin':
                return $this->roundRobinSelection($instances);
            case 'least_connections':
                return $this->leastConnectionsSelection($instances);
            case 'weighted_round_robin':
                return $this->weightedRoundRobinSelection($instances);
            case 'random':
                return $this->randomSelection($instances);
            case 'ip_hash':
                return $this->ipHashSelection($instances);
            default:
                return $instances[0];
        }
    }
    
    /**
     * Round robin selection
     */
    private function roundRobinSelection(array $instances): array
    {
        static $currentIndex = [];
        
        $serviceKey = array_keys($this->services)[0]; // Simplified
        
        if (!isset($currentIndex[$serviceKey])) {
            $currentIndex[$serviceKey] = 0;
        }
        
        $selected = $instances[$currentIndex[$serviceKey] % count($instances)];
        $currentIndex[$serviceKey]++;
        
        return $selected;
    }
    
    /**
     * Least connections selection
     */
    private function leastConnectionsSelection(array $instances): array
    {
        $selected = $instances[0];
        $minConnections = $selected['active_connections'] ?? 0;
        
        foreach ($instances as $instance) {
            $connections = $instance['active_connections'] ?? 0;
            if ($connections < $minConnections) {
                $selected = $instance;
                $minConnections = $connections;
            }
        }
        
        return $selected;
    }
    
    /**
     * Weighted round robin selection
     */
    private function weightedRoundRobinSelection(array $instances): array
    {
        $weights = [];
        
        foreach ($instances as $instance) {
            $weight = $instance['metadata']['weight'] ?? 1;
            $weights = array_merge($weights, array_fill(0, $weight, $instance));
        }
        
        $index = rand(0, count($weights) - 1);
        return $weights[$index];
    }
    
    /**
     * Random selection
     */
    private function randomSelection(array $instances): array
    {
        $index = rand(0, count($instances) - 1);
        return $instances[$index];
    }
    
    /**
     * IP hash selection
     */
    private function ipHashSelection(array $instances): array
    {
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $hash = crc32($clientIp);
        $index = abs($hash) % count($instances);
        
        return $instances[$index];
    }
    
    /**
     * Update instance heartbeat
     */
    public function updateHeartbeat(string $serviceName, string $instanceId): bool
    {
        if (!isset($this->services[$serviceName])) {
            return false;
        }
        
        $instances = &$this->services[$serviceName]['instances'];
        
        foreach ($instances as &$instance) {
            if ($instance['id'] === $instanceId) {
                $instance['last_heartbeat'] = time();
                $instance['status'] = 'healthy';
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Perform health checks
     */
    public function performHealthChecks(): void
    {
        foreach ($this->services as $serviceName => $service) {
            $healthCheckConfig = $service['health_check'];
            
            foreach ($service['instances'] as &$instance) {
                $isHealthy = $this->checkInstanceHealth($instance, $healthCheckConfig);
                
                if ($isHealthy) {
                    $instance['status'] = 'healthy';
                    $instance['consecutive_failures'] = 0;
                } else {
                    $instance['consecutive_failures'] = ($instance['consecutive_failures'] ?? 0) + 1;
                    
                    if ($instance['consecutive_failures'] >= $healthCheckConfig['unhealthy_threshold']) {
                        $instance['status'] = 'unhealthy';
                    }
                }
            }
        }
    }
    
    /**
     * Check individual instance health
     */
    private function checkInstanceHealth(array $instance, array $config): bool
    {
        // Simulate health check
        $url = "http://{$instance['host']}:{$instance['port']}{$config['path']}";
        
        // In real implementation, this would make HTTP request
        // For demo, simulate with random success
        return rand(1, 100) > 10; // 90% success rate
    }
    
    /**
     * Get default health check configuration
     */
    private function getDefaultHealthCheck(): array
    {
        return [
            'path' => '/health',
            'interval' => 30,
            'timeout' => 5,
            'unhealthy_threshold' => 3
        ];
    }
    
    /**
     * Get all services
     */
    public function getAllServices(): array
    {
        return $this->services;
    }
    
    /**
     * Get service information
     */
    public function getServiceInfo(string $serviceName): ?array
    {
        return $this->services[$serviceName] ?? null;
    }
}

// Configuration Management
class ConfigurationManagement
{
    private array $configurations;
    private array $environments;
    private array $versionHistory;
    
    public function __construct()
    {
        $this->initializeConfigurations();
        $this->setupEnvironments();
        $this->initializeVersionHistory();
    }
    
    /**
     * Initialize configurations
     */
    private function initializeConfigurations(): void
    {
        $this->configurations = [
            'database' => [
                'host' => 'localhost',
                'port' => 5432,
                'name' => 'microservices_db',
                'username' => 'app_user',
                'password' => 'secure_password',
                'ssl_mode' => 'require',
                'max_connections' => 100
            ],
            'redis' => [
                'host' => 'localhost',
                'port' => 6379,
                'database' => 0,
                'password' => null,
                'timeout' => 5,
                'max_connections' => 50
            ],
            'message_queue' => [
                'host' => 'localhost',
                'port' => 5672,
                'username' => 'guest',
                'password' => 'guest',
                'vhost' => '/',
                'heartbeat' => 60
            ],
            'logging' => [
                'level' => 'INFO',
                'format' => 'json',
                'file' => '/var/log/microservices.log',
                'max_size' => '100MB',
                'backup_count' => 5
            ],
            'monitoring' => [
                'metrics_enabled' => true,
                'metrics_port' => 9090,
                'health_check_interval' => 30,
                'alert_thresholds' => [
                    'cpu_usage' => 80,
                    'memory_usage' => 85,
                    'disk_usage' => 90
                ]
            ]
        ];
    }
    
    /**
     * Setup environments
     */
    private function setupEnvironments(): void
    {
        $this->environments = [
            'development' => [
                'database' => [
                    'host' => 'localhost',
                    'port' => 5432,
                    'name' => 'microservices_dev'
                ],
                'logging' => [
                    'level' => 'DEBUG',
                    'file' => '/var/log/microservices-dev.log'
                ],
                'debug' => true,
                'profiling' => true
            ],
            'staging' => [
                'database' => [
                    'host' => 'staging-db.example.com',
                    'port' => 5432,
                    'name' => 'microservices_staging'
                ],
                'logging' => [
                    'level' => 'INFO',
                    'file' => '/var/log/microservices-staging.log'
                ],
                'debug' => false,
                'profiling' => false
            ],
            'production' => [
                'database' => [
                    'host' => 'prod-db.example.com',
                    'port' => 5432,
                    'name' => 'microservices_prod'
                ],
                'logging' => [
                    'level' => 'WARN',
                    'file' => '/var/log/microservices-prod.log'
                ],
                'debug' => false,
                'profiling' => false
            ]
        ];
    }
    
    /**
     * Initialize version history
     */
    private function initializeVersionHistory(): void
    {
        $this->versionHistory = [
            'current_version' => '1.2.0',
            'versions' => [
                '1.2.0' => [
                    'released_at' => '2024-01-15',
                    'changes' => [
                        'Added circuit breaker pattern',
                        'Improved load balancing algorithms',
                        'Fixed memory leak in service discovery'
                    ],
                    'config_changes' => [
                        'Added new monitoring thresholds',
                        'Updated database connection pool settings'
                    ]
                ],
                '1.1.0' => [
                    'released_at' => '2023-12-01',
                    'changes' => [
                        'Initial service discovery implementation',
                        'Added health check endpoints',
                        'Implemented configuration management'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Get configuration for environment
     */
    public function getConfiguration(string $environment): array
    {
        $baseConfig = $this->configurations;
        $envConfig = $this->environments[$environment] ?? [];
        
        return array_merge_recursive($baseConfig, $envConfig);
    }
    
    /**
     * Get specific configuration value
     */
    public function getConfigValue(string $key, string $environment = 'production'): mixed
    {
        $config = $this->getConfiguration($environment);
        $keys = explode('.', $key);
        
        $value = $config;
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return null;
            }
        }
        
        return $value;
    }
    
    /**
     * Update configuration
     */
    public function updateConfiguration(string $key, mixed $value, string $environment = 'production'): bool
    {
        if (!isset($this->environments[$environment])) {
            return false;
        }
        
        $keys = explode('.', $key);
        $config = &$this->environments[$environment];
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
        
        return true;
    }
    
    /**
     * Get version history
     */
    public function getVersionHistory(): array
    {
        return $this->versionHistory;
    }
    
    /**
     * Get current version
     */
    public function getCurrentVersion(): string
    {
        return $this->versionHistory['current_version'];
    }
}

// Service Discovery Examples
class ServiceDiscoveryExamples
{
    private ServiceRegistry $registry;
    private ConfigurationManagement $config;
    
    public function __construct()
    {
        $this->registry = new ServiceRegistry();
        $this->config = new ConfigurationManagement();
    }
    
    public function demonstrateServiceRegistration(): void
    {
        echo "Service Registration Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Register new instances
        echo "Registering Service Instances:\n";
        
        $newInstance = [
            'id' => 'user-service-3',
            'host' => 'localhost',
            'port' => 8021,
            'metadata' => [
                'version' => '1.0.1',
                'region' => 'us-west-1',
                'zone' => 'us-west-1c',
                'weight' => 2
            ]
        ];
        
        $this->registry->registerInstance('user-service', $newInstance);
        echo "Registered: {$newInstance['id']} at {$newInstance['host']}:{$newInstance['port']}\n";
        
        $orderInstance = [
            'id' => 'order-service-3',
            'host' => 'localhost',
            'port' => 8022,
            'metadata' => [
                'version' => '1.1.1',
                'region' => 'us-west-1',
                'zone' => 'us-west-1c'
            ]
        ];
        
        $this->registry->registerInstance('order-service', $orderInstance);
        echo "Registered: {$orderInstance['id']} at {$orderInstance['host']}:{$orderInstance['port']}\n";
        
        // Show all services
        echo "\nAll Registered Services:\n";
        $services = $this->registry->getAllServices();
        
        foreach ($services as $serviceName => $service) {
            echo "$serviceName:\n";
            echo "  Load Balancing: {$service['load_balancing']}\n";
            echo "  Instances: " . count($service['instances']) . "\n";
            
            foreach ($service['instances'] as $instance) {
                echo "    {$instance['id']}: {$instance['host']}:{$instance['port']} ({$instance['status']})\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateServiceDiscovery(): void
    {
        echo "\nService Discovery Demo\n";
        echo str_repeat("-", 28) . "\n";
        
        // Discover services
        echo "Discovering Services:\n";
        
        $userService = $this->registry->discoverService('user-service');
        if ($userService) {
            echo "User Service Found:\n";
            echo "  Instance: {$userService['id']}\n";
            echo "  Address: {$userService['host']}:{$userService['port']}\n";
            echo "  Version: {$userService['metadata']['version']}\n";
            echo "  Region: {$userService['metadata']['region']}\n\n";
        }
        
        $orderService = $this->registry->discoverService('order-service');
        if ($orderService) {
            echo "Order Service Found:\n";
            echo "  Instance: {$orderService['id']}\n";
            echo "  Address: {$orderService['host']}:{$orderService['port']}\n";
            echo "  Version: {$orderService['metadata']['version']}\n\n";
        }
        
        $productService = $this->registry->discoverService('product-service');
        if ($productService) {
            echo "Product Service Found:\n";
            echo "  Instance: {$productService['id']}\n";
            echo "  Address: {$productService['host']}:{$productService['port']}\n";
            echo "  Version: {$productService['metadata']['version']}\n\n";
        }
    }
    
    public function demonstrateLoadBalancing(): void
    {
        echo "Load Balancing Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Test different load balancing algorithms
        $algorithms = ['round_robin', 'least_connections', 'weighted_round_robin', 'random'];
        
        foreach ($algorithms as $algorithm) {
            echo "\nTesting $algorithm algorithm:\n";
            
            for ($i = 0; $i < 5; $i++) {
                $service = $this->registry->discoverService('user-service');
                if ($service) {
                    echo "  Request $i: {$service['id']} ({$service['host']}:{$service['port']})\n";
                }
            }
        }
    }
    
    public function demonstrateHealthChecks(): void
    {
        echo "\nHealth Check Demo\n";
        echo str_repeat("-", 22) . "\n";
        
        // Perform health checks
        echo "Performing Health Checks:\n";
        
        $this->registry->performHealthChecks();
        
        $services = $this->registry->getAllServices();
        
        foreach ($services as $serviceName => $service) {
            echo "\n$serviceName Health Status:\n";
            
            foreach ($service['instances'] as $instance) {
                $status = $instance['status'];
                $failures = $instance['consecutive_failures'] ?? 0;
                echo "  {$instance['id']}: $status";
                
                if ($failures > 0) {
                    echo " ($failures failures)";
                }
                echo "\n";
            }
        }
    }
    
    public function demonstrateConfiguration(): void
    {
        echo "\nConfiguration Management Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Show configurations for different environments
        $environments = ['development', 'staging', 'production'];
        
        foreach ($environments as $env) {
            echo "\n$env Environment:\n";
            
            $config = $this->config->getConfiguration($env);
            
            echo "  Database Host: {$config['database']['host']}\n";
            echo "  Database Name: {$config['database']['name']}\n";
            echo "  Log Level: {$config['logging']['level']}\n";
            echo "  Debug Mode: " . ($config['debug'] ? 'true' : 'false') . "\n";
        }
        
        // Get specific config values
        echo "\nSpecific Configuration Values:\n";
        echo "  Production Database Host: " . $this->config->getConfigValue('database.host', 'production') . "\n";
        echo "  Development Log Level: " . $this->config->getConfigValue('logging.level', 'development') . "\n";
        echo "  Monitoring Enabled: " . ($this->config->getConfigValue('monitoring.metrics_enabled') ? 'true' : 'false') . "\n";
        
        // Update configuration
        echo "\nUpdating Configuration:\n";
        $this->config->updateConfiguration('logging.level', 'ERROR', 'production');
        echo "Updated production log level to ERROR\n";
        
        $newLevel = $this->config->getConfigValue('logging.level', 'production');
        echo "New production log level: $newLevel\n";
        
        // Show version history
        echo "\nVersion History:\n";
        $versionHistory = $this->config->getVersionHistory();
        echo "Current Version: {$versionHistory['current_version']}\n";
        
        foreach ($versionHistory['versions'] as $version => $info) {
            echo "\nVersion $version ({$info['released_at']}):\n";
            foreach ($info['changes'] as $change) {
                echo "  • $change\n";
            }
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nService Discovery Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Service Registration:\n";
        echo "   • Register with metadata (version, region, capabilities)\n";
        echo "   • Implement proper health checks\n";
        echo "   • Send regular heartbeats\n";
        echo "   • Handle graceful shutdown\n";
        echo "   • Use unique instance IDs\n\n";
        
        echo "2. Health Checks:\n";
        echo "   • Implement lightweight health endpoints\n";
        echo "   • Check dependencies health\n";
        echo "   • Use appropriate intervals\n";
        echo "   • Implement failure thresholds\n";
        echo "   • Provide detailed health status\n\n";
        
        echo "3. Load Balancing:\n";
        echo "   • Choose appropriate algorithm for your use case\n";
        echo "   • Consider instance capabilities\n";
        echo "   • Implement connection tracking\n";
        echo "   • Handle instance failures gracefully\n";
        echo "   • Monitor load distribution\n\n";
        
        echo "4. Configuration Management:\n";
        echo "   • Environment-specific configurations\n";
        echo "   • Version control configuration changes\n";
        echo "   • Implement configuration validation\n";
        echo "   • Use secure credential management\n";
        echo "   • Provide configuration rollback\n\n";
        
        echo "5. Monitoring:\n";
        echo "   • Monitor service discovery metrics\n";
        echo "   • Track registration/deregistration\n";
        echo "   • Monitor health check failures\n";
        echo "   • Alert on service unavailability\n";
        echo "   • Track load balancing effectiveness";
    }
    
    public function runAllExamples(): void
    {
        echo "Service Discovery and Load Balancing Examples\n";
        echo str_repeat("=", 50) . "\n";
        
        $this->demonstrateServiceRegistration();
        $this->demonstrateServiceDiscovery();
        $this->demonstrateLoadBalancing();
        $this->demonstrateHealthChecks();
        $this->demonstrateConfiguration();
        $this->demonstrateBestPractices();
    }
}

// Helper function for recursive array merge
function array_merge_recursive(array $array1, array $array2): array
{
    $merged = $array1;
    
    foreach ($array2 as $key => $value) {
        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
            $merged[$key] = array_merge_recursive($merged[$key], $value);
        } else {
            $merged[$key] = $value;
        }
    }
    
    return $merged;
}

// Main execution
function runServiceDiscoveryDemo(): void
{
    $examples = new ServiceDiscoveryExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runServiceDiscoveryDemo();
}
?>
