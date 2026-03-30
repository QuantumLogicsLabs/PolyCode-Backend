<?php
/**
 * Cloud Computing Fundamentals
 * 
 * Understanding cloud concepts, providers, and basic cloud operations with PHP.
 */

// Cloud Provider Manager
class CloudProviderManager
{
    private array $providers = [];
    private array $services = [];
    private array $credentials = [];
    
    public function __construct()
    {
        $this->initializeProviders();
        $this->setupServices();
    }
    
    /**
     * Initialize cloud providers
     */
    private function initializeProviders(): void
    {
        $this->providers = [
            'aws' => [
                'name' => 'Amazon Web Services',
                'region' => 'us-west-2',
                'endpoint' => 'https://aws.amazon.com',
                'services' => ['ec2', 's3', 'rds', 'lambda', 'api-gateway'],
                'pricing_model' => 'pay-as-you-go',
                'compliance' => ['SOC2', 'ISO27001', 'HIPAA', 'GDPR']
            ],
            'azure' => [
                'name' => 'Microsoft Azure',
                'region' => 'East US',
                'endpoint' => 'https://azure.microsoft.com',
                'services' => ['virtual-machines', 'blob-storage', 'sql-database', 'functions', 'app-service'],
                'pricing_model' => 'pay-as-you-go',
                'compliance' => ['SOC2', 'ISO27001', 'HIPAA', 'GDPR']
            ],
            'gcp' => [
                'name' => 'Google Cloud Platform',
                'region' => 'us-central1',
                'endpoint' => 'https://cloud.google.com',
                'services' => ['compute-engine', 'cloud-storage', 'cloud-sql', 'cloud-functions', 'app-engine'],
                'pricing_model' => 'pay-as-you-go',
                'compliance' => ['SOC2', 'ISO27001', 'HIPAA', 'GDPR']
            ],
            'digitalocean' => [
                'name' => 'DigitalOcean',
                'region' => 'nyc1',
                'endpoint' => 'https://www.digitalocean.com',
                'services' => ['droplets', 'spaces', 'managed-databases', 'functions'],
                'pricing_model' => 'fixed_monthly',
                'compliance' => ['SOC2', 'PCI-DSS']
            ]
        ];
    }
    
    /**
     * Setup cloud services
     */
    private function setupServices(): void
    {
        $this->services = [
            'compute' => [
                'aws' => 'EC2',
                'azure' => 'Virtual Machines',
                'gcp' => 'Compute Engine',
                'digitalocean' => 'Droplets'
            ],
            'storage' => [
                'aws' => 'S3',
                'azure' => 'Blob Storage',
                'gcp' => 'Cloud Storage',
                'digitalocean' => 'Spaces'
            ],
            'database' => [
                'aws' => 'RDS',
                'azure' => 'SQL Database',
                'gcp' => 'Cloud SQL',
                'digitalocean' => 'Managed Databases'
            ],
            'serverless' => [
                'aws' => 'Lambda',
                'azure' => 'Functions',
                'gcp' => 'Cloud Functions',
                'digitalocean' => 'Functions'
            ],
            'api_gateway' => [
                'aws' => 'API Gateway',
                'azure' => 'API Management',
                'gcp' => 'API Gateway',
                'digitalocean' => 'Load Balancer'
            ]
        ];
    }
    
    /**
     * Get provider information
     */
    public function getProviderInfo(string $provider): ?array
    {
        return $this->providers[$provider] ?? null;
    }
    
    /**
     * Get service mapping
     */
    public function getServiceMapping(string $serviceType): array
    {
        return $this->services[$serviceType] ?? [];
    }
    
    /**
     * Compare providers
     */
    public function compareProviders(array $providers): array
    {
        $comparison = [];
        
        foreach ($providers as $provider) {
            if (isset($this->providers[$provider])) {
                $comparison[$provider] = $this->providers[$provider];
            }
        }
        
        return $comparison;
    }
    
    /**
     * Calculate estimated costs
     */
    public function estimateCosts(string $provider, array $resources): array
    {
        $pricing = $this->getPricingData($provider);
        $totalCost = 0;
        $breakdown = [];
        
        foreach ($resources as $resource => $quantity) {
            if (isset($pricing[$resource])) {
                $cost = $pricing[$resource] * $quantity;
                $totalCost += $cost;
                $breakdown[$resource] = [
                    'unit_cost' => $pricing[$resource],
                    'quantity' => $quantity,
                    'total' => $cost
                ];
            }
        }
        
        return [
            'provider' => $provider,
            'total_cost' => $totalCost,
            'breakdown' => $breakdown,
            'currency' => 'USD',
            'period' => 'monthly'
        ];
    }
    
    /**
     * Get pricing data (simulated)
     */
    private function getPricingData(string $provider): array
    {
        $pricingData = [
            'aws' => [
                'ec2_t3_micro' => 0.0104,
                's3_storage' => 0.023,
                'rds_t3_micro' => 0.013,
                'lambda' => 0.00001667,
                'api_gateway' => 3.50
            ],
            'azure' => [
                'vm_b1s' => 0.0077,
                'blob_storage' => 0.018,
                'sql_database' => 0.012,
                'functions' => 0.000016,
                'api_management' => 2.50
            ],
            'gcp' => [
                'compute_e2_micro' => 0.0049,
                'cloud_storage' => 0.020,
                'cloud_sql' => 0.010,
                'cloud_functions' => 0.000018,
                'api_gateway' => 3.00
            ],
            'digitalocean' => [
                'droplet_1gb' => 5.00,
                'spaces_storage' => 0.02,
                'managed_db_1gb' => 15.00,
                'functions' => 0.000020,
                'load_balancer' => 10.00
            ]
        ];
        
        return $pricingData[$provider] ?? [];
    }
}

// Cloud Service Manager
class CloudServiceManager
{
    private CloudProviderManager $providerManager;
    private array $deployedServices = [];
    private array $configurations = [];
    
    public function __construct()
    {
        $this->providerManager = new CloudProviderManager();
        $this->initializeConfigurations();
    }
    
    /**
     * Initialize service configurations
     */
    private function initializeConfigurations(): void
    {
        $this->configurations = [
            'web_server' => [
                'type' => 'compute',
                'instance_type' => 't3.micro',
                'os' => 'ubuntu-20.04',
                'storage' => 20,
                'bandwidth' => 100,
                'auto_scale' => false
            ],
            'database' => [
                'type' => 'database',
                'engine' => 'mysql',
                'version' => '8.0',
                'instance_class' => 'db.t3.micro',
                'storage' => 20,
                'backup_retention' => 7
            ],
            'file_storage' => [
                'type' => 'storage',
                'storage_class' => 'standard',
                'versioning' => true,
                'encryption' => true,
                'lifecycle_rules' => true
            ],
            'api_endpoint' => [
                'type' => 'api_gateway',
                'protocol' => 'REST',
                'authentication' => 'jwt',
                'rate_limiting' => true,
                'logging' => true
            ],
            'serverless_function' => [
                'type' => 'serverless',
                'runtime' => 'php',
                'memory' => 128,
                'timeout' => 30,
                'triggers' => ['http', 'database']
            ]
        ];
    }
    
    /**
     * Deploy service to cloud
     */
    public function deployService(string $provider, string $serviceType, array $config = []): array
    {
        $serviceId = uniqid('svc_');
        $deployment = [
            'service_id' => $serviceId,
            'provider' => $provider,
            'service_type' => $serviceType,
            'config' => array_merge($this->configurations[$serviceType] ?? [], $config),
            'status' => 'deploying',
            'created_at' => time(),
            'endpoints' => []
        ];
        
        // Simulate deployment process
        $deployment = $this->simulateDeployment($deployment);
        
        $this->deployedServices[$serviceId] = $deployment;
        
        return $deployment;
    }
    
    /**
     * Simulate deployment process
     */
    private function simulateDeployment(array $deployment): array
    {
        // Simulate deployment time
        $deploymentTime = rand(30, 180);
        
        // Generate endpoints based on service type
        switch ($deployment['service_type']) {
            case 'compute':
                $deployment['endpoints'] = [
                    'public_ip' => $this->generatePublicIP(),
                    'ssh_endpoint' => 'ssh://user@' . $deployment['endpoints']['public_ip'],
                    'web_endpoint' => 'http://' . $deployment['endpoints']['public_ip']
                ];
                break;
            case 'database':
                $deployment['endpoints'] = [
                    'connection_string' => 'mysql://user:pass@' . $this->generatePrivateIP() . ':3306/db',
                    'admin_endpoint' => 'https://console.' . $deployment['provider'] . '.com/databases'
                ];
                break;
            case 'storage':
                $deployment['endpoints'] = [
                    'api_endpoint' => 'https://' . $deployment['provider'] . '.amazonaws.com',
                    'web_console' => 'https://console.' . $deployment['provider'] . '.com/storage'
                ];
                break;
            case 'api_gateway':
                $deployment['endpoints'] = [
                    'api_url' => 'https://api-' . $deployment['service_id'] . '.' . $deployment['provider'] . '.com',
                    'documentation' => 'https://api-' . $deployment['service_id'] . '.' . $deployment['provider'] . '.com/docs'
                ];
                break;
            case 'serverless':
                $deployment['endpoints'] = [
                    'invoke_url' => 'https://' . $deployment['service_id'] . '.execute-api.' . $deployment['provider'] . '.com',
                    'logs' => 'https://console.' . $deployment['provider'] . '.com/functions/logs'
                ];
                break;
        }
        
        $deployment['status'] = 'running';
        $deployment['deployed_at'] = time();
        $deployment['deployment_time'] = $deploymentTime;
        
        return $deployment;
    }
    
    /**
     * Generate public IP
     */
    private function generatePublicIP(): string
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }
    
    /**
     * Generate private IP
     */
    private function generatePrivateIP(): string
    {
        return '10.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }
    
    /**
     * List deployed services
     */
    public function listServices(?string $provider = null): array
    {
        $services = $this->deployedServices;
        
        if ($provider) {
            $services = array_filter($services, function($service) use ($provider) {
                return $service['provider'] === $provider;
            });
        }
        
        return array_values($services);
    }
    
    /**
     * Get service details
     */
    public function getService(string $serviceId): ?array
    {
        return $this->deployedServices[$serviceId] ?? null;
    }
    
    /**
     * Update service configuration
     */
    public function updateService(string $serviceId, array $config): bool
    {
        if (!isset($this->deployedServices[$serviceId])) {
            return false;
        }
        
        $this->deployedServices[$serviceId]['config'] = array_merge(
            $this->deployedServices[$serviceId]['config'],
            $config
        );
        $this->deployedServices[$serviceId]['updated_at'] = time();
        
        return true;
    }
    
    /**
     * Delete service
     */
    public function deleteService(string $serviceId): bool
    {
        if (!isset($this->deployedServices[$serviceId])) {
            return false;
        }
        
        // Simulate deletion
        $this->deployedServices[$serviceId]['status'] = 'terminating';
        $this->deployedServices[$serviceId]['terminated_at'] = time();
        
        unset($this->deployedServices[$serviceId]);
        
        return true;
    }
    
    /**
     * Get service metrics
     */
    public function getServiceMetrics(string $serviceId): array
    {
        if (!isset($this->deployedServices[$serviceId])) {
            return [];
        }
        
        $service = $this->deployedServices[$serviceId];
        
        // Simulate metrics based on service type
        switch ($service['service_type']) {
            case 'compute':
                return [
                    'cpu_usage' => rand(10, 80) . '%',
                    'memory_usage' => rand(20, 70) . '%',
                    'disk_usage' => rand(5, 60) . '%',
                    'network_in' => rand(100, 1000) . ' MB/s',
                    'network_out' => rand(50, 500) . ' MB/s',
                    'uptime' => rand(1, 30) . ' days'
                ];
            case 'database':
                return [
                    'connections' => rand(1, 50),
                    'cpu_usage' => rand(5, 60) . '%',
                    'memory_usage' => rand(30, 80) . '%',
                    'storage_usage' => rand(10, 90) . '%',
                    'query_count' => rand(1000, 10000),
                    'avg_query_time' => rand(1, 100) . ' ms'
                ];
            case 'storage':
                return [
                    'objects_count' => rand(1000, 100000),
                    'total_size' => rand(1, 1000) . ' GB',
                    'read_requests' => rand(1000, 10000),
                    'write_requests' => rand(100, 1000),
                    'bandwidth_usage' => rand(1, 100) . ' MB/s'
                ];
            case 'api_gateway':
                return [
                    'request_count' => rand(1000, 10000),
                    'error_rate' => rand(0, 5) . '%',
                    'avg_response_time' => rand(50, 500) . ' ms',
                    'active_endpoints' => rand(1, 10),
                    'rate_limit_hits' => rand(0, 100)
                ];
            case 'serverless':
                return [
                    'invocations' => rand(1000, 100000),
                    'errors' => rand(0, 100),
                    'avg_duration' => rand(50, 1000) . ' ms',
                    'memory_usage' => rand(20, 80) . '%',
                    'cold_starts' => rand(0, 50)
                ];
            default:
                return [];
        }
    }
}

// Cloud Cost Optimizer
class CloudCostOptimizer
{
    private array $costData = [];
    private array $recommendations = [];
    
    public function __construct()
    {
        $this->initializeCostData();
    }
    
    /**
     * Initialize cost data
     */
    private function initializeCostData(): void
    {
        $this->costData = [
            'compute' => [
                'aws' => [
                    't3.micro' => ['cost' => 0.0104, 'vcpu' => 2, 'memory' => 1],
                    't3.small' => ['cost' => 0.0416, 'vcpu' => 2, 'memory' => 2],
                    't3.medium' => ['cost' => 0.0832, 'vcpu' => 2, 'memory' => 4],
                    't3.large' => ['cost' => 0.1664, 'vcpu' => 2, 'memory' => 8]
                ],
                'azure' => [
                    'b1s' => ['cost' => 0.0077, 'vcpu' => 1, 'memory' => 1],
                    'b2s' => ['cost' => 0.0376, 'vcpu' => 2, 'memory' => 4],
                    'b4ms' => ['cost' => 0.1056, 'vcpu' => 2, 'memory' => 8]
                ],
                'gcp' => [
                    'e2-micro' => ['cost' => 0.0049, 'vcpu' => 2, 'memory' => 1],
                    'e2-small' => ['cost' => 0.0196, 'vcpu' => 2, 'memory' => 2],
                    'e2-medium' => ['cost' => 0.0391, 'vcpu' => 2, 'memory' => 4]
                ]
            ],
            'storage' => [
                'aws' => [
                    'standard' => ['cost' => 0.023, 'performance' => 'standard'],
                    'infrequent' => ['cost' => 0.0125, 'performance' => 'infrequent'],
                    'glacier' => ['cost' => 0.004, 'performance' => 'archive']
                ],
                'azure' => [
                    'hot' => ['cost' => 0.018, 'performance' => 'high'],
                    'cool' => ['cost' => 0.01, 'performance' => 'cool'],
                    'archive' => ['cost' => 0.002, 'performance' => 'archive']
                ],
                'gcp' => [
                    'standard' => ['cost' => 0.020, 'performance' => 'standard'],
                    'nearline' => ['cost' => 0.010, 'performance' => 'nearline'],
                    'coldline' => ['cost' => 0.004, 'performance' => 'cold']
                ]
            ]
        ];
    }
    
    /**
     * Analyze costs and generate recommendations
     */
    public function analyzeCosts(array $usage): array
    {
        $analysis = [
            'current_cost' => 0,
            'potential_savings' => 0,
            'recommendations' => []
        ];
        
        // Analyze compute costs
        if (isset($usage['compute'])) {
            $computeAnalysis = $this->analyzeComputeCosts($usage['compute']);
            $analysis['current_cost'] += $computeAnalysis['current_cost'];
            $analysis['potential_savings'] += $computeAnalysis['potential_savings'];
            $analysis['recommendations'] = array_merge(
                $analysis['recommendations'],
                $computeAnalysis['recommendations']
            );
        }
        
        // Analyze storage costs
        if (isset($usage['storage'])) {
            $storageAnalysis = $this->analyzeStorageCosts($usage['storage']);
            $analysis['current_cost'] += $storageAnalysis['current_cost'];
            $analysis['potential_savings'] += $storageAnalysis['potential_savings'];
            $analysis['recommendations'] = array_merge(
                $analysis['recommendations'],
                $storageAnalysis['recommendations']
            );
        }
        
        return $analysis;
    }
    
    /**
     * Analyze compute costs
     */
    private function analyzeComputeCosts(array $computeUsage): array
    {
        $analysis = [
            'current_cost' => 0,
            'potential_savings' => 0,
            'recommendations' => []
        ];
        
        foreach ($computeUsage as $instance) {
            $provider = $instance['provider'];
            $type = $instance['type'];
            $utilization = $instance['utilization'] ?? 50;
            
            if (isset($this->costData['compute'][$provider][$type])) {
                $instanceInfo = $this->costData['compute'][$provider][$type];
                $monthlyCost = $instanceInfo['cost'] * 24 * 30;
                $analysis['current_cost'] += $monthlyCost;
                
                // Check for underutilization
                if ($utilization < 30) {
                    $savings = $monthlyCost * 0.7; // Could save 70% by downsizing
                    $analysis['potential_savings'] += $savings;
                    
                    $analysis['recommendations'][] = [
                        'type' => 'downsize',
                        'resource' => $instance['instance_id'],
                        'current_type' => $type,
                        'suggested_type' => $this->suggestSmallerInstance($provider, $type),
                        'potential_savings' => $savings,
                        'reason' => 'Instance is underutilized (' . $utilization . '%)'
                    ];
                }
                
                // Check for overprovisioning
                if ($utilization > 90) {
                    $analysis['recommendations'][] = [
                        'type' => 'scale_up',
                        'resource' => $instance['instance_id'],
                        'current_type' => $type,
                        'suggested_type' => $this->suggestLargerInstance($provider, $type),
                        'reason' => 'Instance is overutilized (' . $utilization . '%)'
                    ];
                }
            }
        }
        
        return $analysis;
    }
    
    /**
     * Analyze storage costs
     */
    private function analyzeStorageCosts(array $storageUsage): array
    {
        $analysis = [
            'current_cost' => 0,
            'potential_savings' => 0,
            'recommendations' => []
        ];
        
        foreach ($storageUsage as $storage) {
            $provider = $storage['provider'];
            $class = $storage['class'];
            $size = $storage['size_gb'];
            $access_pattern = $storage['access_pattern'] ?? 'frequent';
            
            if (isset($this->costData['storage'][$provider][$class])) {
                $storageInfo = $this->costData['storage'][$provider][$class];
                $monthlyCost = $storageInfo['cost'] * $size;
                $analysis['current_cost'] += $monthlyCost;
                
                // Check for storage tier optimization
                if ($access_pattern === 'infrequent' && $class === 'standard') {
                    $suggestedClass = $this->suggestStorageClass($provider, 'infrequent');
                    if ($suggestedClass) {
                        $newCost = $this->costData['storage'][$provider][$suggestedClass]['cost'] * $size;
                        $savings = $monthlyCost - $newCost;
                        $analysis['potential_savings'] += $savings;
                        
                        $analysis['recommendations'][] = [
                            'type' => 'storage_tier',
                            'resource' => $storage['storage_id'],
                            'current_class' => $class,
                            'suggested_class' => $suggestedClass,
                            'potential_savings' => $savings,
                            'reason' => 'Storage access pattern suggests lower tier'
                        ];
                    }
                }
            }
        }
        
        return $analysis;
    }
    
    /**
     * Suggest smaller instance
     */
    private function suggestSmallerInstance(string $provider, string $currentType): string
    {
        $instances = array_keys($this->costData['compute'][$provider]);
        $currentIndex = array_search($currentType, $instances);
        
        if ($currentIndex > 0) {
            return $instances[$currentIndex - 1];
        }
        
        return $currentType;
    }
    
    /**
     * Suggest larger instance
     */
    private function suggestLargerInstance(string $provider, string $currentType): string
    {
        $instances = array_keys($this->costData['compute'][$provider]);
        $currentIndex = array_search($currentType, $instances);
        
        if ($currentIndex < count($instances) - 1) {
            return $instances[$currentIndex + 1];
        }
        
        return $currentType;
    }
    
    /**
     * Suggest storage class
     */
    private function suggestStorageClass(string $provider, string $accessPattern): ?string
    {
        $storageClasses = $this->costData['storage'][$provider];
        
        foreach ($storageClasses as $class => $info) {
            if ($accessPattern === 'infrequent' && strpos($class, 'infrequent') !== false) {
                return $class;
            }
            if ($accessPattern === 'archive' && strpos($class, 'cold') !== false) {
                return $class;
            }
        }
        
        return null;
    }
}

// Cloud Fundamentals Examples
class CloudFundamentalsExamples
{
    private CloudProviderManager $providerManager;
    private CloudServiceManager $serviceManager;
    private CloudCostOptimizer $costOptimizer;
    
    public function __construct()
    {
        $this->providerManager = new CloudProviderManager();
        $this->serviceManager = new CloudServiceManager();
        $this->costOptimizer = new CloudCostOptimizer();
    }
    
    public function demonstrateProviders(): void
    {
        echo "Cloud Providers Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Show provider information
        $providers = ['aws', 'azure', 'gcp', 'digitalocean'];
        
        foreach ($providers as $provider) {
            $info = $this->providerManager->getProviderInfo($provider);
            echo "{$info['name']}:\n";
            echo "  Region: {$info['region']}\n";
            echo "  Pricing Model: {$info['pricing_model']}\n";
            echo "  Services: " . implode(', ', $info['services']) . "\n";
            echo "  Compliance: " . implode(', ', $info['compliance']) . "\n\n";
        }
        
        // Compare providers
        echo "Provider Comparison:\n";
        $comparison = $this->providerManager->compareProviders(['aws', 'azure', 'gcp']);
        
        foreach ($comparison as $provider => $info) {
            echo "$provider:\n";
            echo "  Services: " . count($info['services']) . "\n";
            echo "  Compliance Standards: " . count($info['compliance']) . "\n\n";
        }
    }
    
    public function demonstrateServiceMapping(): void
    {
        echo "Service Mapping Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $serviceTypes = ['compute', 'storage', 'database', 'serverless', 'api_gateway'];
        
        foreach ($serviceTypes as $serviceType) {
            echo ucfirst($serviceType) . " Services:\n";
            $mapping = $this->providerManager->getServiceMapping($serviceType);
            
            foreach ($mapping as $provider => $serviceName) {
                echo "  $provider: $serviceName\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateCostEstimation(): void
    {
        echo "Cost Estimation Demo\n";
        echo str_repeat("-", 22) . "\n";
        
        // Estimate costs for different providers
        $resources = [
            'ec2_t3_micro' => 2,
            's3_storage' => 100,
            'rds_t3_micro' => 1
        ];
        
        foreach (['aws', 'azure', 'gcp'] as $provider) {
            echo "$provider Cost Estimation:\n";
            $cost = $this->providerManager->estimateCosts($provider, $resources);
            
            echo "  Total Cost: \${$cost['total_cost']}/{$cost['period']}\n";
            echo "  Breakdown:\n";
            
            foreach ($cost['breakdown'] as $resource => $details) {
                echo "    $resource: \${$details['total']} ({$details['quantity']} x \${$details['unit_cost']})\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateServiceDeployment(): void
    {
        echo "Service Deployment Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Deploy different services
        $deployments = [];
        
        // Deploy web server
        $webServer = $this->serviceManager->deployService('aws', 'web_server', [
            'instance_type' => 't3.micro',
            'os' => 'ubuntu-20.04'
        ]);
        $deployments[] = $webServer;
        
        // Deploy database
        $database = $this->serviceManager->deployService('aws', 'database', [
            'engine' => 'mysql',
            'instance_class' => 'db.t3.micro'
        ]);
        $deployments[] = $database;
        
        // Deploy storage
        $storage = $this->serviceManager->deployService('aws', 'file_storage', [
            'storage_class' => 'standard',
            'versioning' => true
        ]);
        $deployments[] = $storage;
        
        // Show deployment results
        foreach ($deployments as $deployment) {
            echo "{$deployment['service_type']}: {$deployment['status']}\n";
            echo "  Service ID: {$deployment['service_id']}\n";
            echo "  Provider: {$deployment['provider']}\n";
            echo "  Endpoints:\n";
            
            foreach ($deployment['endpoints'] as $type => $endpoint) {
                echo "    $type: $endpoint\n";
            }
            echo "\n";
        }
        
        // List all services
        echo "All Deployed Services:\n";
        $services = $this->serviceManager->listServices();
        
        foreach ($services as $service) {
            echo "{$service['service_type']} ({$service['provider']}): {$service['status']}\n";
        }
    }
    
    public function demonstrateServiceMetrics(): void
    {
        echo "\nService Metrics Demo\n";
        echo str_repeat("-", 22) . "\n";
        
        $services = $this->serviceManager->listServices();
        
        foreach ($services as $service) {
            echo "{$service['service_type']} Metrics:\n";
            $metrics = $this->serviceManager->getServiceMetrics($service['service_id']);
            
            foreach ($metrics as $metric => $value) {
                echo "  $metric: $value\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateCostOptimization(): void
    {
        echo "Cost Optimization Demo\n";
        echo str_repeat("-", 23) . "\n";
        
        // Simulate usage data
        $usage = [
            'compute' => [
                [
                    'provider' => 'aws',
                    'type' => 't3.large',
                    'instance_id' => 'i-12345',
                    'utilization' => 25 // Underutilized
                ],
                [
                    'provider' => 'aws',
                    'type' => 't3.micro',
                    'instance_id' => 'i-67890',
                    'utilization' => 95 // Overutilized
                ]
            ],
            'storage' => [
                [
                    'provider' => 'aws',
                    'class' => 'standard',
                    'storage_id' => 's3-bucket-1',
                    'size_gb' => 500,
                    'access_pattern' => 'infrequent'
                ]
            ]
        ];
        
        $analysis = $this->costOptimizer->analyzeCosts($usage);
        
        echo "Cost Analysis:\n";
        echo "  Current Monthly Cost: \${$analysis['current_cost']}\n";
        echo "  Potential Savings: \${$analysis['potential_savings']}\n";
        echo "  Savings Percentage: " . round(($analysis['potential_savings'] / $analysis['current_cost']) * 100, 2) . "%\n\n";
        
        echo "Optimization Recommendations:\n";
        foreach ($analysis['recommendations'] as $rec) {
            echo "[{$rec['type']}] {$rec['resource']}\n";
            echo "  Reason: {$rec['reason']}\n";
            
            if (isset($rec['potential_savings'])) {
                echo "  Potential Savings: \${$rec['potential_savings']}/month\n";
            }
            
            if (isset($rec['suggested_type'])) {
                echo "  Suggested: {$rec['suggested_type']}\n";
            }
            
            if (isset($rec['suggested_class'])) {
                echo "  Suggested: {$rec['suggested_class']}\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "Cloud Best Practices\n";
        echo str_repeat("-", 20) . "\n";
        
        echo "1. Provider Selection:\n";
        echo "   • Evaluate based on service requirements\n";
        echo "   • Consider compliance and security needs\n";
        echo "   • Compare pricing models\n";
        echo "   • Check regional availability\n";
        echo "   • Evaluate support options\n\n";
        
        echo "2. Cost Management:\n";
        echo "   • Monitor usage regularly\n";
        echo "   • Use auto-scaling\n";
        echo "   • Implement cost alerts\n";
        echo "   • Optimize instance sizes\n";
        echo "   • Use appropriate storage tiers\n\n";
        
        echo "3. Security:\n";
        echo "   • Use IAM roles and policies\n";
        echo "   • Implement network security\n";
        echo "   • Enable encryption at rest and in transit\n";
        echo "   • Regular security audits\n";
        echo "   • Monitor for suspicious activity\n\n";
        
        echo "4. Performance:\n";
        echo "   • Choose appropriate instance types\n";
        echo "   • Implement caching strategies\n";
        echo "   • Use CDN for static content\n";
        echo "   • Monitor performance metrics\n";
        echo "   • Optimize database queries\n\n";
        
        echo "5. Reliability:\n";
        echo "   • Implement multi-AZ deployments\n";
        echo "   • Use automated backups\n";
        echo "   • Implement disaster recovery\n";
        echo "   • Monitor service health\n";
        echo "   • Test failover procedures";
    }
    
    public function runAllExamples(): void
    {
        echo "Cloud Computing Fundamentals Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateProviders();
        $this->demonstrateServiceMapping();
        $this->demonstrateCostEstimation();
        $this->demonstrateServiceDeployment();
        $this->demonstrateServiceMetrics();
        $this->demonstrateCostOptimization();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runCloudFundamentalsDemo(): void
{
    $examples = new CloudFundamentalsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCloudFundamentalsDemo();
}
?>
