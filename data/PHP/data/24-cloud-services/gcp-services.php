<?php
/**
 * Google Cloud Platform Services Integration
 * 
 * Working with GCP services using PHP SDK and REST APIs.
 */

// GCP Service Manager
class GCPServiceManager
{
    private array $credentials;
    private string $projectId;
    private array $services = [];
    
    public function __construct(array $credentials, string $projectId)
    {
        $this->credentials = $credentials;
        $this->projectId = $projectId;
        $this->initializeServices();
    }
    
    /**
     * Initialize GCP services
     */
    private function initializeServices(): void
    {
        $this->services = [
            'compute_engine' => new ComputeEngineService($this->credentials, $this->projectId),
            'cloud_storage' => new CloudStorageService($this->credentials, $this->projectId),
            'cloud_sql' => new CloudSQLService($this->credentials, $this->projectId),
            'cloud_functions' => new CloudFunctionsService($this->credentials, $this->projectId),
            'app_engine' => new AppEngineService($this->credentials, $this->projectId),
            'cloud_monitoring' => new CloudMonitoringService($this->credentials, $this->projectId)
        ];
    }
    
    /**
     * Get service instance
     */
    public function getService(string $serviceName): ?object
    {
        return $this->services[$serviceName] ?? null;
    }
    
    /**
     * Get all services
     */
    public function getServices(): array
    {
        return $this->services;
    }
}

// Compute Engine Service
class ComputeEngineService
{
    private array $credentials;
    private string $projectId;
    private array $instances = [];
    
    public function __construct(array $credentials, string $projectId)
    {
        $this->credentials = $credentials;
        $this->projectId = $projectId;
        $this->initializeInstances();
    }
    
    /**
     * Initialize sample instances
     */
    private function initializeInstances(): void
    {
        $this->instances = [
            'web-instance-1' => [
                'name' => 'web-instance-1',
                'zone' => 'us-central1-a',
                'machineType' => 'e2-medium',
                'status' => 'RUNNING',
                'internalIp' => '10.128.0.10',
                'externalIp' => '34.123.45.67',
                'creationTimestamp' => '2024-01-15T10:30:00.000-08:00',
                'tags' => [
                    'items' => [
                        ['key' => 'environment', 'value' => 'production'],
                        ['key' => 'application', 'value' => 'web']
                    ]
                ],
                'disks' => [
                    [
                        'source' => 'projects/' . $this->projectId . '/zones/us-central1-a/disks/web-instance-1-boot',
                        'boot' => true,
                        'initializeParams' => [
                            'diskSizeGb' => 30,
                            'diskType' => 'pd-balanced'
                        ]
                    ]
                ],
                'networkInterfaces' => [
                    [
                        'network' => 'projects/' . $this->projectId . '/global/networks/default',
                        'accessConfigs' => [
                            [
                                'type' => 'ONE_TO_ONE_NAT',
                                'natIP' => '34.123.45.67',
                                'name' => 'External NAT'
                            ]
                        ]
                    ]
                ]
            ],
            'db-instance-1' => [
                'name' => 'db-instance-1',
                'zone' => 'us-central1-b',
                'machineType' => 'e2-small',
                'status' => 'TERMINATED',
                'internalIp' => '10.128.0.11',
                'externalIp' => null,
                'creationTimestamp' => '2024-01-10T08:15:00.000-08:00',
                'tags' => [
                    'items' => [
                        ['key' => 'environment', 'value' => 'production'],
                        ['key' => 'application', 'value' => 'database']
                    ]
                ]
            ]
        ];
    }
    
    /**
     * List instances
     */
    public function listInstances(string $zone = null): array
    {
        $instances = $this->instances;
        
        if ($zone) {
            $instances = array_filter($instances, function($instance) use ($zone) {
                return $instance['zone'] === $zone;
            });
        }
        
        return array_values($instances);
    }
    
    /**
     * Get instance details
     */
    public function getInstance(string $instanceName): ?array
    {
        return $this->instances[$instanceName] ?? null;
    }
    
    /**
     * Create instance
     */
    public function createInstance(array $config): array
    {
        $instanceName = $config['name'] ?? 'instance-' . uniqid();
        
        if (isset($this->instances[$instanceName])) {
            return ['error' => 'Instance already exists'];
        }
        
        $instance = [
            'name' => $instanceName,
            'zone' => $config['zone'] ?? 'us-central1-a',
            'machineType' => $config['machineType'] ?? 'e2-medium',
            'status' => 'PROVISIONING',
            'internalIp' => '10.128.0.' . rand(10, 200),
            'externalIp' => null,
            'creationTimestamp' => date('c') . '.000-08:00',
            'tags' => [
                'items' => $config['tags'] ?? []
            ]
        ];
        
        $this->instances[$instanceName] = $instance;
        
        // Simulate instance creation
        sleep(2);
        $this->instances[$instanceName]['status'] = 'RUNNING';
        $this->instances[$instanceName]['externalIp'] = $this->generateExternalIP();
        
        return $instance;
    }
    
    /**
     * Start instance
     */
    public function startInstance(string $instanceName): array
    {
        if (!isset($this->instances[$instanceName])) {
            return ['error' => 'Instance not found'];
        }
        
        $instance = &$this->instances[$instanceName];
        
        if ($instance['status'] === 'RUNNING') {
            return ['error' => 'Instance is already running'];
        }
        
        $instance['status'] = 'STARTING';
        
        // Simulate starting
        sleep(1);
        $instance['status'] = 'RUNNING';
        
        return [
            'name' => $instanceName,
            'status' => 'STARTED',
            'currentStatus' => $instance['status']
        ];
    }
    
    /**
     * Stop instance
     */
    public function stopInstance(string $instanceName): array
    {
        if (!isset($this->instances[$instanceName])) {
            return ['error' => 'Instance not found'];
        }
        
        $instance = &$this->instances[$instanceName];
        
        if ($instance['status'] === 'TERMINATED') {
            return ['error' => 'Instance is already terminated'];
        }
        
        $instance['status'] = 'STOPPING';
        
        // Simulate stopping
        sleep(1);
        $instance['status'] = 'TERMINATED';
        $instance['externalIp'] = null;
        
        return [
            'name' => $instanceName,
            'status' => 'STOPPED',
            'currentStatus' => $instance['status']
        ];
    }
    
    /**
     * Delete instance
     */
    public function deleteInstance(string $instanceName): array
    {
        if (!isset($this->instances[$instanceName])) {
            return ['error' => 'Instance not found'];
        }
        
        unset($this->instances[$instanceName]);
        
        return [
            'name' => $instanceName,
            'status' => 'DELETED'
        ];
    }
    
    /**
     * Get instance metrics
     */
    public function getInstanceMetrics(string $instanceName, string $metric, int $hours = 1): array
    {
        if (!isset($this->instances[$instanceName])) {
            return ['error' => 'Instance not found'];
        }
        
        // Simulate Cloud Monitoring metrics
        $dataPoints = [];
        $endTime = time();
        $startTime = $endTime - ($hours * 3600);
        
        for ($i = 0; $i < $hours * 12; $i++) {
            $timestamp = $startTime + ($i * 300); // 5-minute intervals
            
            switch ($metric) {
                case 'compute.googleapis.com/instance/cpu/utilization':
                    $value = rand(10, 80);
                    break;
                case 'compute.googleapis.com/instance/network/received_bytes_count':
                    $value = rand(1000, 10000);
                    break;
                case 'compute.googleapis.com/instance/network/sent_bytes_count':
                    $value = rand(500, 5000);
                    break;
                case 'compute.googleapis.com/instance/disk/read_bytes_count':
                    $value = rand(100, 1000);
                    break;
                case 'compute.googleapis.com/instance/disk/write_bytes_count':
                    $value = rand(50, 500);
                    break;
                default:
                    $value = rand(1, 100);
            }
            
            $dataPoints[] = [
                'interval' => [
                    'startTime' => date('c', $timestamp),
                    'endTime' => date('c', $timestamp + 300)
                ],
                'value' => [
                    'doubleValue' => $value
                ]
            ];
        }
        
        return $dataPoints;
    }
    
    /**
     * Generate external IP
     */
    private function generateExternalIP(): string
    {
        return rand(34, 35) . '.' . rand(100, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }
}

// Cloud Storage Service
class CloudStorageService
{
    private array $credentials;
    private string $projectId;
    private array $buckets = [];
    private array $objects = [];
    
    public function __construct(array $credentials, string $projectId)
    {
        $this->credentials = $credentials;
        $this->projectId = $projectId;
        $this->initializeBuckets();
    }
    
    /**
     * Initialize sample buckets
     */
    private function initializeBuckets(): void
    {
        $this->buckets = [
            'my-app-bucket' => [
                'name' => 'my-app-bucket',
                'location' => 'US-CENTRAL1',
                'storageClass' => 'STANDARD',
                'timeCreated' => '2024-01-01T00:00:00.000Z',
                'updated' => '2024-01-20T10:30:00.000Z',
                'lifecycle' => [
                    'rule' => [
                        [
                            'action' => [
                                'type' => 'Delete',
                                'condition' => [
                                    'age' => 30
                                ]
                            ]
                        ]
                    ]
                ],
                'labels' => [
                    'environment' => 'production',
                    'application' => 'web'
                ]
            ],
            'backup-bucket' => [
                'name' => 'backup-bucket',
                'location' => 'US-CENTRAL1',
                'storageClass' => 'NEARLINE',
                'timeCreated' => '2024-01-15T12:00:00.000Z',
                'updated' => '2024-01-25T14:15:00.000Z',
                'lifecycle' => [],
                'labels' => [
                    'environment' => 'production',
                    'application' => 'backup'
                ]
            ]
        ];
        
        // Initialize sample objects
        $this->objects = [
            'my-app-bucket' => [
                'images/' => [
                    'logo.png' => [
                        'name' => 'images/logo.png',
                        'size' => 24576,
                        'updated' => '2024-01-20T10:30:00.000Z',
                        'contentType' => 'image/png',
                        'storageClass' => 'STANDARD',
                        'md5Hash' => 'd41d8cd98f00b204e9800998ecf8427e'
                    ],
                    'banner.jpg' => [
                        'name' => 'images/banner.jpg',
                        'size' => 1048576,
                        'updated' => '2024-01-21T09:15:00.000Z',
                        'contentType' => 'image/jpeg',
                        'storageClass' => 'STANDARD',
                        'md5Hash' => '098f6bcd4621d373cade4e832627b4f6'
                    ]
                ],
                'documents/' => [
                    'report.pdf' => [
                        'name' => 'documents/report.pdf',
                        'size' => 2097152,
                        'updated' => '2024-01-22T14:15:00.000Z',
                        'contentType' => 'application/pdf',
                        'storageClass' => 'STANDARD',
                        'md5Hash' => '5d41402abc4b2a76b9719d911017c592'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * List buckets
     */
    public function listBuckets(): array
    {
        return array_values($this->buckets);
    }
    
    /**
     * Create bucket
     */
    public function createBucket(array $config): array
    {
        $bucketName = $config['name'] ?? 'bucket-' . uniqid();
        
        if (isset($this->buckets[$bucketName])) {
            return ['error' => 'Bucket already exists'];
        }
        
        $bucket = [
            'name' => $bucketName,
            'location' => $config['location'] ?? 'US-CENTRAL1',
            'storageClass' => $config['storageClass'] ?? 'STANDARD',
            'timeCreated' => date('c') . 'Z',
            'updated' => date('c') . 'Z',
            'lifecycle' => $config['lifecycle'] ?? [],
            'labels' => $config['labels'] ?? []
        ];
        
        $this->buckets[$bucketName] = $bucket;
        $this->objects[$bucketName] = [];
        
        return $bucket;
    }
    
    /**
     * Delete bucket
     */
    public function deleteBucket(string $bucketName): array
    {
        if (!isset($this->buckets[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        // Check if bucket is empty
        if (!empty($this->objects[$bucketName])) {
            return ['error' => 'Bucket is not empty'];
        }
        
        unset($this->buckets[$bucketName]);
        unset($this->objects[$bucketName]);
        
        return ['success' => true, 'bucket' => $bucketName];
    }
    
    /**
     * List objects
     */
    public function listObjects(string $bucketName, string $prefix = ''): array
    {
        if (!isset($this->objects[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        $objects = $this->objects[$bucketName];
        
        if ($prefix) {
            $objects = array_filter($objects, function($folder) use ($prefix) {
                return strpos($prefix, $folder) === 0;
            }, ARRAY_FILTER_USE_KEY);
            
            $allObjects = [];
            foreach ($objects as $folder => $folderObjects) {
                foreach ($folderObjects as $object) {
                    $allObjects[] = $object;
                }
            }
        } else {
            $allObjects = [];
            foreach ($objects as $folder => $folderObjects) {
                foreach ($folderObjects as $object) {
                    $allObjects[] = $object;
                }
            }
        }
        
        return $allObjects;
    }
    
    /**
     * Upload object
     */
    public function uploadObject(string $bucketName, string $objectName, $content, array $metadata = []): array
    {
        if (!isset($this->objects[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        $object = [
            'name' => $objectName,
            'size' => strlen($content),
            'updated' => date('c') . 'Z',
            'contentType' => $metadata['contentType'] ?? 'application/octet-stream',
            'storageClass' => $metadata['storageClass'] ?? 'STANDARD',
            'md5Hash' => md5($content)
        ];
        
        // Extract folder from object name
        $folder = dirname($objectName);
        if ($folder === '.' || $folder === '') {
            $folder = 'root';
        }
        
        if (!isset($this->objects[$bucketName][$folder])) {
            $this->objects[$bucketName][$folder] = [];
        }
        
        $this->objects[$bucketName][$folder][$objectName] = $object;
        
        return $object;
    }
    
    /**
     * Get object
     */
    public function getObject(string $bucketName, string $objectName): array
    {
        if (!isset($this->objects[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        // Find object
        foreach ($this->objects[$bucketName] as $folder => $objects) {
            if (isset($objects[$objectName])) {
                $object = $objects[$objectName];
                $object['content'] = 'Sample content for ' . $objectName;
                return $object;
            }
        }
        
        return ['error' => 'Object not found'];
    }
    
    /**
     * Delete object
     */
    public function deleteObject(string $bucketName, string $objectName): array
    {
        if (!isset($this->objects[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        // Find and delete object
        foreach ($this->objects[$bucketName] as $folder => &$objects) {
            if (isset($objects[$objectName])) {
                unset($objects[$objectName]);
                
                // Remove empty folders
                if (empty($objects)) {
                    unset($this->objects[$bucketName][$folder]);
                }
                
                return ['success' => true, 'object' => $objectName];
            }
        }
        
        return ['error' => 'Object not found'];
    }
    
    /**
     * Get bucket metrics
     */
    public function getBucketMetrics(string $bucketName): array
    {
        if (!isset($this->buckets[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        $totalSize = 0;
        $objectCount = 0;
        $storageClasses = [];
        
        if (isset($this->objects[$bucketName])) {
            foreach ($this->objects[$bucketName] as $folder => $objects) {
                foreach ($objects as $object) {
                    $totalSize += $object['size'];
                    $objectCount++;
                    
                    $class = $object['storageClass'];
                    if (!isset($storageClasses[$class])) {
                        $storageClasses[$class] = 0;
                    }
                    $storageClasses[$class]++;
                }
            }
        }
        
        return [
            'bucket' => $bucketName,
            'objectCount' => $objectCount,
            'totalSize' => $totalSize,
            'averageObjectSize' => $objectCount > 0 ? round($totalSize / $objectCount) : 0,
            'storageClasses' => $storageClasses
        ];
    }
}

// Cloud Functions Service
class CloudFunctionsService
{
    private array $credentials;
    private string $projectId;
    private array $functions = [];
    
    public function __construct(array $credentials, string $projectId)
    {
        $this->credentials = $credentials;
        $this->projectId = $projectId;
        $this->initializeFunctions();
    }
    
    /**
     * Initialize sample functions
     */
    private function initializeFunctions(): void
    {
        $this->functions = [
            'process-data' => [
                'name' => 'process-data',
                'description' => 'Process incoming data and store results',
                'runtime' => 'php82',
                'entryPoint' => 'process_data',
                'timeout' => 60,
                'availableMemoryMb' => 256,
                'status' => 'ACTIVE',
                'eventTrigger' => [
                    'eventType' => 'google.cloud.pubsub.topic.v1.messagePublished',
                    'resource' => 'projects/' . $this->projectId . '/topics/data-processing'
                ],
                'httpsTrigger' => [],
                'labels' => [
                    'environment' => 'production',
                    'application' => 'data-processor'
                ],
                'createTime' => '2024-01-20T15:30:00.000Z',
                'updateTime' => '2024-01-25T10:15:00.000Z'
            ],
            'image-processor' => [
                'name' => 'image-processor',
                'description' => 'Process uploaded images and create thumbnails',
                'runtime' => 'python39',
                'entryPoint' => 'image_processor',
                'timeout' => 120,
                'availableMemoryMb' => 512,
                'status' => 'ACTIVE',
                'eventTrigger' => [
                    'eventType' => 'google.cloud.storage.object.v1.finalized',
                    'resource' => 'projects/_/buckets/image-uploads'
                ],
                'httpsTrigger' => [],
                'labels' => [
                    'environment' => 'production',
                    'application' => 'image-processor'
                ],
                'createTime' => '2024-01-18T09:45:00.000Z',
                'updateTime' => '2024-01-22T14:20:00.000Z'
            ]
        ];
    }
    
    /**
     * List functions
     */
    public function listFunctions(): array
    {
        return array_values($this->functions);
    }
    
    /**
     * Get function details
     */
    public function getFunction(string $functionName): ?array
    {
        return $this->functions[$functionName] ?? null;
    }
    
    /**
     * Create function
     */
    public function createFunction(array $config): array
    {
        $functionName = $config['name'] ?? 'function-' . uniqid();
        
        if (isset($this->functions[$functionName])) {
            return ['error' => 'Function already exists'];
        }
        
        $function = [
            'name' => $functionName,
            'description' => $config['description'] ?? '',
            'runtime' => $config['runtime'] ?? 'php82',
            'entryPoint' => $config['entryPoint'] ?? 'handler',
            'timeout' => $config['timeout'] ?? 60,
            'availableMemoryMb' => $config['availableMemoryMb'] ?? 256,
            'status' => 'DEPLOYING',
            'eventTrigger' => $config['eventTrigger'] ?? [],
            'httpsTrigger' => $config['httpsTrigger'] ?? [],
            'labels' => $config['labels'] ?? [],
            'createTime' => date('c') . 'Z',
            'updateTime' => date('c') . 'Z'
        ];
        
        $this->functions[$functionName] = $function;
        
        // Simulate deployment
        sleep(2);
        $this->functions[$functionName]['status'] = 'ACTIVE';
        
        return $function;
    }
    
    /**
     * Invoke function
     */
    public function invokeFunction(string $functionName, array $data = []): array
    {
        if (!isset($this->functions[$functionName])) {
            return ['error' => 'Function not found'];
        }
        
        $function = $this->functions[$functionName];
        
        // Simulate function execution
        $executionTime = rand(50, 500);
        $success = rand(1, 20) !== 1; // 95% success rate
        
        if ($success) {
            $response = [
                'data' => [
                    'message' => 'Function executed successfully',
                    'input' => $data,
                    'execution_time' => $executionTime,
                    'processed_at' => date('c') . 'Z'
                ]
            ];
        } else {
            $response = [
                'error' => 'Function execution failed',
                'message' => 'Internal server error'
            ];
        }
        
        return [
            'functionName' => $functionName,
            'executionId' => uniqid('execution_'),
            'status' => $success ? 'success' : 'error',
            'executionTime' => $executionTime,
            'memoryUsage' => rand(50, $function['availableMemoryMb']),
            'response' => $response,
            'logsUrl' => 'https://console.cloud.google.com/logs/viewer?project=' . $this->projectId
        ];
    }
    
    /**
     * Update function
     */
    public function updateFunction(string $functionName, array $updates): array
    {
        if (!isset($this->functions[$functionName])) {
            return ['error' => 'Function not found'];
        }
        
        $function = &$this->functions[$functionName];
        
        foreach ($updates as $key => $value) {
            if (isset($function[$key])) {
                $function[$key] = $value;
            }
        }
        
        $function['updateTime'] = date('c') . 'Z';
        
        return $function;
    }
    
    /**
     * Delete function
     */
    public function deleteFunction(string $functionName): array
    {
        if (!isset($this->functions[$functionName])) {
            return ['error' => 'Function not found'];
        }
        
        unset($this->functions[$functionName]);
        
        return ['success' => true, 'function' => $functionName];
    }
    
    /**
     * Get function metrics
     */
    public function getFunctionMetrics(string $functionName, int $hours = 24): array
    {
        if (!isset($this->functions[$functionName])) {
            return ['error' => 'Function not found'];
        }
        
        $function = $this->functions[$functionName];
        
        // Simulate Cloud Monitoring metrics
        return [
            'functionName' => $functionName,
            'invocations' => rand(100, 1000),
            'errors' => rand(0, 50),
            'errorRate' => round((rand(0, 50) / 1000) * 100, 2),
            'executionTimes' => [
                'average' => rand(50, 500),
                'minimum' => 10,
                'maximum' => 1000
            ],
            'memoryUsage' => [
                'average' => rand(50, $function['availableMemoryMb']),
                'maximum' => $function['availableMemoryMb']
            ],
            'activeInstances' => rand(0, 3),
            'coldStarts' => rand(0, 10)
        ];
    }
}

// Cloud Monitoring Service
class CloudMonitoringService
{
    private array $credentials;
    private string $projectId;
    private array $metrics = [];
    
    public function __construct(array $credentials, string $projectId)
    {
        $this->credentials = $credentials;
        $this->projectId = $projectId;
        $this->initializeMetrics();
    }
    
    /**
     * Initialize metrics
     */
    private function initializeMetrics(): void
    {
        $this->metrics = [
            'compute.googleapis.com/instance/cpu/utilization' => [
                'displayName' => 'CPU Utilization',
                'description' => 'CPU utilization of the instance',
                'type' => 'GAUGE',
                'unit' => 'Percent'
            ],
            'compute.googleapis.com/instance/network/received_bytes_count' => [
                'displayName' => 'Network In',
                'description' => 'Network bytes received by the instance',
                'type' => 'CUMULATIVE',
                'unit' => 'By'
            ],
            'compute.googleapis.com/instance/network/sent_bytes_count' => [
                'displayName' => 'Network Out',
                'description' => 'Network bytes sent by the instance',
                'type' => 'CUMULATIVE',
                'unit' => 'By'
            ],
            'storage.googleapis.com/storage/object/byte_count' => [
                'displayName' => 'Storage Object Bytes',
                'description' => 'Total bytes of objects in bucket',
                'type' => 'GAUGE',
                'unit' => 'By'
            ],
            'cloudfunctions.googleapis.com/function/invocations' => [
                'displayName' => 'Function Invocations',
                'description' => 'Number of function invocations',
                'type' => 'DELTA',
                'unit' => '1'
            ],
            'cloudfunctions.googleapis.com/function/execution_times' => [
                'displayName' => 'Function Execution Times',
                'description' => 'Execution times of function invocations',
                'type' => 'DISTRIBUTION',
                'unit' => 'ms'
            ]
        ];
    }
    
    /**
     * List metrics
     */
    public function listMetrics(): array
    {
        return $this->metrics;
    }
    
    /**
     * Get metric data
     */
    public function getMetricData(array $query): array
    {
        $metricType = $query['type'] ?? '';
        $filters = $query['filters'] ?? [];
        $startTime = $query['startTime'] ?? time() - 3600;
        $endTime = $query['endTime'] ?? time();
        $interval = $query['interval'] ?? '300s';
        
        // Generate data points
        $dataPoints = [];
        $currentTime = $startTime;
        
        while ($currentTime < $endTime) {
            $value = $this->generateMetricValue($metricType);
            
            $dataPoints[] = [
                'interval' => [
                    'startTime' => date('c', $currentTime),
                    'endTime' => date('c', $currentTime + 300)
                ],
                'value' => [
                    'doubleValue' => $value
                ]
            ];
            
            $currentTime += 300;
        }
        
        return [
            'metricType' => $metricType,
            'dataPoints' => $dataPoints
        ];
    }
    
    /**
     * Generate metric value
     */
    private function generateMetricValue(string $metricType): float
    {
        switch ($metricType) {
            case 'compute.googleapis.com/instance/cpu/utilization':
                return rand(10, 80);
            case 'compute.googleapis.com/instance/network/received_bytes_count':
                return rand(1000, 10000);
            case 'compute.googleapis.com/instance/network/sent_bytes_count':
                return rand(500, 5000);
            case 'storage.googleapis.com/storage/object/byte_count':
                return rand(1000000, 10000000);
            case 'cloudfunctions.googleapis.com/function/invocations':
                return rand(1, 100);
            case 'cloudfunctions.googleapis.com/function/execution_times':
                return rand(50, 1000);
            default:
                return rand(1, 1000);
        }
    }
    
    /**
     * Create alert policy
     */
    public function createAlertPolicy(array $config): array
    {
        $policyName = $config['name'] ?? 'alert-' . uniqid();
        
        $policy = [
            'name' => $policyName,
            'displayName' => $config['displayName'] ?? $policyName,
            'description' => $config['description'] ?? '',
            'conditions' => $config['conditions'] ?? [],
            'notificationChannels' => $config['notificationChannels'] ?? [],
            'enabled' => $config['enabled'] ?? true,
            'creationTime' => date('c') . 'Z',
            'updateTime' => date('c') . 'Z'
        ];
        
        return $policy;
    }
    
    /**
     * List alert policies
     */
    public function listAlertPolicies(): array
    {
        // Simulate alert policies
        return [
            [
                'name' => 'high-cpu-usage',
                'displayName' => 'High CPU Usage Alert',
                'description' => 'Alert when CPU usage exceeds 80%',
                'enabled' => true,
                'conditions' => [
                    [
                        'displayName' => 'CPU > 80%',
                        'condition' => [
                            'filter' => 'metric.type="compute.googleapis.com/instance/cpu/utilization" AND metric.label.instance_name="web-instance-1"',
                            'aggregations' => [
                                [
                                    'alignmentPeriod' => '60s',
                                    'perSeriesAligner' => [
                                        [
                                            'alignmentPeriod' => '60s',
                                            'series' => [
                                                [
                                                    'metric' => [
                                                        'type' => 'compute.googleapis.com/instance/cpu/utilization',
                                                        'labels' => [
                                                            'instance_name' => 'web-instance-1'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'trigger' => [
                                'threshold' => [
                                    'value' => 0.8,
                                    'comparison' => 'COMPARISON_GT',
                                    'duration' => '300s'
                                ]
                            ]
                        ]
                    ]
                ],
                'notificationChannels' => [
                    [
                        'displayName' => 'email',
                        'type' => 'email',
                        'labels' => [
                            'email_address' => 'alerts@example.com'
                        ]
                    ]
                ]
            ]
        ];
    }
}

// GCP Services Examples
class GCPServicesExamples
{
    private GCPServiceManager $gcpManager;
    
    public function __construct()
    {
        $credentials = [
            'type' => 'service_account',
            'project_id' => 'your-project-id',
            'private_key_id' => 'your-private-key-id',
            'private_key' => '-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n',
            'client_email' => 'your-service-account@example.com',
            'client_id' => 'your-client-id',
            'auth_uri' => 'https://oauth2.googleapis.com/token',
            'token_uri' => 'https://oauth2.googleapis.com/token'
        ];
        
        $projectId = 'your-project-id';
        
        $this->gcpManager = new GCPServiceManager($credentials, $projectId);
    }
    
    public function demonstrateComputeEngine(): void
    {
        echo "Google Compute Engine Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $compute = $this->gcpManager->getService('compute_engine');
        
        // List instances
        echo "Listing Compute Engine Instances:\n";
        $instances = $compute->listInstances();
        
        foreach ($instances as $instance) {
            echo "{$instance['name']} ({$instance['machineType']}): {$instance['status']}\n";
            if ($instance['externalIp']) {
                echo "  External IP: {$instance['externalIp']}\n";
            }
            echo "  Zone: {$instance['zone']}\n";
            echo "  Labels: " . json_encode(array_column($instance['tags']['items'], 'value')) . "\n\n";
        }
        
        // Create new instance
        echo "Creating New Instance:\n";
        $newInstance = $compute->createInstance([
            'name' => 'test-instance-' . uniqid(),
            'zone' => 'us-central1-a',
            'machineType' => 'e2-medium',
            'tags' => [
                ['key' => 'environment', 'value' => 'development'],
                ['key' => 'application', 'value' => 'test']
            ]
        ]);
        
        echo "Created: {$newInstance['name']}\n";
        echo "Status: {$newInstance['status']}\n";
        echo "External IP: {$newInstance['externalIp']}\n\n";
        
        // Get instance metrics
        echo "Instance Metrics (CPU Utilization):\n";
        $metrics = $compute->getInstanceMetrics($newInstance['name'], 'compute.googleapis.com/instance/cpu/utilization', 1);
        
        foreach (array_slice($metrics, 0, 5) as $metric) {
            echo "  {$metric['interval']['startTime']}: {$metric['value']['doubleValue']}%\n";
        }
        
        // Stop instance
        echo "\nStopping Instance:\n";
        $stopResult = $compute->stopInstance($newInstance['name']);
        echo "Status: {$stopResult['status']}\n";
        echo "Current Status: {$stopResult['currentStatus']}\n";
    }
    
    public function demonstrateCloudStorage(): void
    {
        echo "\nGoogle Cloud Storage Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $storage = $this->gcpManager->getService('cloud_storage');
        
        // List buckets
        echo "Listing Cloud Storage Buckets:\n";
        $buckets = $storage->listBuckets();
        
        foreach ($buckets as $bucket) {
            echo "{$bucket['name']} ({$bucket['storageClass']})\n";
            echo "  Location: {$bucket['location']}\n";
            echo "  Created: {$bucket['timeCreated']}\n";
            echo "  Labels: " . json_encode($bucket['labels']) . "\n\n";
        }
        
        // Create new bucket
        echo "Creating New Bucket:\n";
        $newBucket = $storage->createBucket([
            'name' => 'test-bucket-' . uniqid(),
            'location' => 'US-CENTRAL1',
            'storageClass' => 'STANDARD',
            'labels' => [
                'environment' => 'development',
                'application' => 'test'
            ]
        ]);
        
        echo "Created: {$newBucket['name']}\n";
        echo "Location: {$newBucket['location']}\n";
        echo "Storage Class: {$newBucket['storageClass']}\n\n";
        
        // Upload object
        echo "Uploading Object:\n";
        $content = 'This is a test file content for Google Cloud Storage';
        $object = $storage->uploadObject($newBucket['name'], 'test-file.txt', $content, [
            'contentType' => 'text/plain',
            'storageClass' => 'STANDARD'
        ]);
        
        echo "Uploaded: {$object['name']}\n";
        echo "Size: {$object['size']} bytes\n";
        echo "Content Type: {$object['contentType']}\n\n";
        
        // List objects
        echo "Listing Objects:\n";
        $objects = $storage->listObjects($newBucket['name']);
        
        foreach ($objects as $obj) {
            echo "{$obj['name']} ({$obj['size']} bytes)\n";
        }
        
        // Get bucket metrics
        echo "\nBucket Metrics:\n";
        $metrics = $storage->getBucketMetrics($newBucket['name']);
        echo "Object Count: {$metrics['objectCount']}\n";
        echo "Total Size: {$metrics['totalSize']} bytes\n";
        echo "Average Object Size: {$metrics['averageObjectSize']} bytes\n";
        echo "Storage Classes: " . json_encode($metrics['storageClasses']) . "\n";
    }
    
    public function demonstrateCloudFunctions(): void
    {
        echo "\nGoogle Cloud Functions Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $functions = $this->gcpManager->getService('cloud_functions');
        
        // List functions
        echo "Listing Cloud Functions:\n";
        $cloudFunctions = $functions->listFunctions();
        
        foreach ($cloudFunctions as $function) {
            echo "{$function['name']} ({$function['runtime']})\n";
            echo "  Description: {$function['description']}\n";
            echo "  Timeout: {$function['timeout']}s\n";
            echo "  Memory: {$function['availableMemoryMb']}MB\n";
            echo "  Status: {$function['status']}\n";
            echo "  Labels: " . json_encode($function['labels']) . "\n\n";
        }
        
        // Create new function
        echo "Creating New Function:\n";
        $newFunction = $functions->createFunction([
            'name' => 'test-function-' . uniqid(),
            'description' => 'Test PHP function for demonstration',
            'runtime' => 'php82',
            'entryPoint' => 'test_handler',
            'timeout' => 60,
            'availableMemoryMb' => 256,
            'labels' => [
                'environment' => 'development',
                'application' => 'test'
            ]
        ]);
        
        echo "Created: {$newFunction['name']}\n";
        echo "Runtime: {$newFunction['runtime']}\n";
        echo "Status: {$newFunction['status']}\n\n";
        
        // Invoke function
        echo "Invoking Function:\n";
        $payload = ['message' => 'Hello from PHP!'];
        $result = $functions->invokeFunction($newFunction['name'], $payload);
        
        echo "Status: {$result['status']}\n";
        echo "Execution Time: {$result['executionTime']}ms\n";
        echo "Memory Usage: {$result['memoryUsage']}MB\n";
        echo "Response: " . json_encode($result['response']) . "\n\n";
        
        // Get function metrics
        echo "Function Metrics:\n";
        $metrics = $functions->getFunctionMetrics($newFunction['name']);
        echo "Invocations: {$metrics['invocations']}\n";
        echo "Errors: {$metrics['errors']}\n";
        echo "Error Rate: {$metrics['errorRate']}%\n";
        echo "Avg Execution Time: {$metrics['executionTimes']['average']}ms\n";
        echo "Avg Memory Usage: {$metrics['memoryUsage']['average']}MB\n";
    }
    
    public function demonstrateCloudMonitoring(): void
    {
        echo "\nGoogle Cloud Monitoring Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        $monitoring = $this->gcpManager->getService('cloud_monitoring');
        
        // List metrics
        echo "Available Metrics:\n";
        $metrics = $monitoring->listMetrics();
        
        foreach ($metrics as $type => $metric) {
            echo "$type: {$metric['displayName']}\n";
            echo "  Description: {$metric['description']}\n";
            echo "  Type: {$metric['type']}\n";
            echo "  Unit: {$metric['unit']}\n\n";
        }
        
        // Get metric data
        echo "Getting CPU Utilization Data:\n";
        $query = [
            'type' => 'compute.googleapis.com/instance/cpu/utilization',
            'filters' => [
                [
                    'key' => 'instance_name',
                    'value' => 'web-instance-1'
                ]
            ],
            'startTime' => time() - 3600,
            'endTime' => time(),
            'interval' => '300s'
        ];
        
        $metricData = $monitoring->getMetricData($query);
        
        echo "Metric Type: {$metricData['metricType']}\n";
        echo "Data Points: " . count($metricData['dataPoints']) . "\n";
        
        foreach (array_slice($metricData['dataPoints'], 0, 3) as $point) {
            echo "  {$point['interval']['startTime']}: {$point['value']['doubleValue']}\n";
        }
        
        // Create alert policy
        echo "\nCreating Alert Policy:\n";
        $policy = $monitoring->createAlertPolicy([
            'name' => 'high-cpu-alert',
            'displayName' => 'High CPU Usage Alert',
            'description' => 'Alert when CPU usage exceeds 80%',
            'enabled' => true,
            'notificationChannels' => [
                [
                    'displayName' => 'email',
                    'type' => 'email',
                    'labels' => [
                        'email_address' => 'alerts@example.com'
                    ]
                ]
            ]
        ]);
        
        echo "Created: {$policy['name']}\n";
        echo "Display Name: {$policy['displayName']}\n";
        echo "Description: {$policy['description']}\n";
        echo "Enabled: " . ($policy['enabled'] ? 'Yes' : 'No') . "\n";
        
        // List alert policies
        echo "\nAlert Policies:\n";
        $policies = $monitoring->listAlertPolicies();
        
        foreach ($policies as $policy) {
            echo "{$policy['displayName']} ({$policy['name']})\n";
            echo "  Status: " . ($policy['enabled'] ? 'Enabled' : 'Disabled') . "\n";
            echo "  Conditions: " . count($policy['conditions']) . "\n";
            echo "  Channels: " . count($policy['notificationChannels']) . "\n\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nGoogle Cloud Platform Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Compute Engine:\n";
        echo "   • Use appropriate machine types\n";
        echo "   • Implement managed instance groups\n";
        ";
        echo "• Use preemptible VMs for cost savings\n";
        echo "   • Enable auto-scaling and load balancing\n";
        echo "   • Use custom images and startup scripts\n\n";
        
        echo "2. Cloud Storage:\n";
        echo "   • Use appropriate storage classes\n";
        echo "   • Implement lifecycle policies\n";
        echo "   • Enable object versioning\n";
        echo "   • Use signed URLs for security\n";
        echo "   • Monitor storage metrics\n\n";
        
        echo "3. Cloud Functions:\n";
        echo "   • Choose appropriate runtime and memory\n";
        echo "   • Implement proper error handling\n";
        echo "   • Use environment variables\n";
        echo "   • Monitor function performance\n";
        echo "   • Implement retry logic\n\n";
        
        echo "4. Cloud Monitoring:\n";
        echo "   • Create custom dashboards\n";
        echo "   • Set up appropriate alerting\n";
        echo "   • Use log-based metrics\n";
        echo "   • Implement uptime checks\n";
        echo "   • Regularly review alert policies\n\n";
        
        echo "5. Security:\n";
        echo "   • Use IAM roles and permissions\n";
        echo "   • Implement VPC Service Controls\n";
        echo "   • Use Cloud Armor for protection\n";
        echo "   • Enable security scanning\n";
        echo "   • Regularly update and patch";
    }
    
    public function runAllExamples(): void
    {
        echo "Google Cloud Platform Services Integration Examples\n";
        echo str_repeat("=", 55) . "\n";
        
        $this->demonstrateComputeEngine();
        $this->demonstrateCloudStorage();
        $this->demonstrateCloudFunctions();
        $this->demonstrateCloudMonitoring();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runGCPServicesDemo(): void
{
    $examples = new GCPServicesExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runGCPServicesDemo();
}
?>
