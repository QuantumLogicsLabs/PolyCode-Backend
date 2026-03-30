<?php
/**
 * AWS Services Integration
 * 
 * Working with Amazon Web Services using PHP SDK and API integrations.
 */

// AWS Service Manager
class AWSServiceManager
{
    private array $credentials;
    private string $region;
    private array $services = [];
    
    public function __construct(array $credentials, string $region = 'us-west-2')
    {
        $this->credentials = $credentials;
        $this->region = $region;
        $this->initializeServices();
    }
    
    /**
     * Initialize AWS services
     */
    private function initializeServices(): void
    {
        $this->services = [
            'ec2' => new EC2Service($this->credentials, $this->region),
            's3' => new S3Service($this->credentials, $this->region),
            'rds' => new RDSService($this->credentials, $this->region),
            'lambda' => new LambdaService($this->credentials, $this->region),
            'api_gateway' => new APIGatewayService($this->credentials, $this->region),
            'cloudwatch' => new CloudWatchService($this->credentials, $this->region),
            'iam' => new IAMService($this->credentials, $this->region)
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

// EC2 Service
class EC2Service
{
    private array $credentials;
    private string $region;
    private array $instances = [];
    
    public function __construct(array $credentials, string $region)
    {
        $this->credentials = $credentials;
        $this->region = $region;
        $this->initializeInstances();
    }
    
    /**
     * Initialize sample instances
     */
    private function initializeInstances(): void
    {
        $this->instances = [
            'i-1234567890abcdef0' => [
                'InstanceId' => 'i-1234567890abcdef0',
                'InstanceType' => 't3.micro',
                'State' => ['Name' => 'running'],
                'PublicIpAddress' => '52.12.34.56',
                'PrivateIpAddress' => '10.0.1.100',
                'LaunchTime' => '2024-01-15T10:30:00Z',
                'Tags' => [
                    ['Key' => 'Name', 'Value' => 'Web Server 1'],
                    ['Key' => 'Environment', 'Value' => 'Production']
                ]
            ],
            'i-0fedcba9876543210' => [
                'InstanceId' => 'i-0fedcba9876543210',
                'InstanceType' => 't3.small',
                'State' => ['Name' => 'stopped'],
                'PublicIpAddress' => null,
                'PrivateIpAddress' => '10.0.1.101',
                'LaunchTime' => '2024-01-10T08:15:00Z',
                'Tags' => [
                    ['Key' => 'Name', 'Value' => 'Database Server'],
                    ['Key' => 'Environment', 'Value' => 'Production']
                ]
            ]
        ];
    }
    
    /**
     * List instances
     */
    public function listInstances(array $filters = []): array
    {
        $instances = $this->instances;
        
        // Apply filters
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $instances = array_filter($instances, function($instance) use ($key, $value) {
                    if ($key === 'instance-state-name') {
                        return $instance['State']['Name'] === $value;
                    }
                    if ($key === 'instance-type') {
                        return $instance['InstanceType'] === $value;
                    }
                    return true;
                });
            }
        }
        
        return array_values($instances);
    }
    
    /**
     * Start instance
     */
    public function startInstance(string $instanceId): array
    {
        if (!isset($this->instances[$instanceId])) {
            return ['error' => 'Instance not found'];
        }
        
        $this->instances[$instanceId]['State']['Name'] = 'pending';
        
        // Simulate starting
        sleep(1);
        $this->instances[$instanceId]['State']['Name'] = 'running';
        
        return [
            'InstanceId' => $instanceId,
            'CurrentState' => ['Name' => 'running'],
            'PreviousState' => ['Name' => 'stopped']
        ];
    }
    
    /**
     * Stop instance
     */
    public function stopInstance(string $instanceId): array
    {
        if (!isset($this->instances[$instanceId])) {
            return ['error' => 'Instance not found'];
        }
        
        $this->instances[$instanceId]['State']['Name'] = 'stopping';
        
        // Simulate stopping
        sleep(1);
        $this->instances[$instanceId]['State']['Name'] = 'stopped';
        $this->instances[$instanceId]['PublicIpAddress'] = null;
        
        return [
            'InstanceId' => $instanceId,
            'CurrentState' => ['Name' => 'stopped'],
            'PreviousState' => ['Name' => 'running']
        ];
    }
    
    /**
     * Create instance
     */
    public function createInstance(array $config): array
    {
        $instanceId = 'i-' . bin2hex(random_bytes(8));
        
        $instance = [
            'InstanceId' => $instanceId,
            'InstanceType' => $config['InstanceType'] ?? 't3.micro',
            'State' => ['Name' => 'pending'],
            'PublicIpAddress' => null,
            'PrivateIpAddress' => '10.0.1.' . rand(100, 200),
            'LaunchTime' => date('c'),
            'Tags' => $config['Tags'] ?? []
        ];
        
        $this->instances[$instanceId] = $instance;
        
        // Simulate instance creation
        sleep(2);
        $this->instances[$instanceId]['State']['Name'] = 'running';
        $this->instances[$instanceId]['PublicIpAddress'] = $this->generatePublicIP();
        
        return $instance;
    }
    
    /**
     * Terminate instance
     */
    public function terminateInstance(string $instanceId): array
    {
        if (!isset($this->instances[$instanceId])) {
            return ['error' => 'Instance not found'];
        }
        
        $this->instances[$instanceId]['State']['Name'] = 'shutting-down';
        
        // Simulate termination
        sleep(1);
        unset($this->instances[$instanceId]);
        
        return [
            'InstanceId' => $instanceId,
            'CurrentState' => ['Name' => 'terminated'],
            'PreviousState' => ['Name' => 'shutting-down']
        ];
    }
    
    /**
     * Get instance metrics
     */
    public function getInstanceMetrics(string $instanceId, string $metric, int $hours = 1): array
    {
        if (!isset($this->instances[$instanceId])) {
            return [];
        }
        
        // Simulate CloudWatch metrics
        $dataPoints = [];
        $endTime = time();
        $startTime = $endTime - ($hours * 3600);
        
        for ($i = 0; $i < $hours * 12; $i++) {
            $timestamp = $startTime + ($i * 300); // 5-minute intervals
            
            switch ($metric) {
                case 'CPUUtilization':
                    $value = rand(10, 80);
                    break;
                case 'NetworkIn':
                    $value = rand(1000, 10000);
                    break;
                case 'NetworkOut':
                    $value = rand(500, 5000);
                    break;
                case 'DiskReadOps':
                    $value = rand(100, 1000);
                    break;
                case 'DiskWriteOps':
                    $value = rand(50, 500);
                    break;
                default:
                    $value = rand(1, 100);
            }
            
            $dataPoints[] = [
                'Timestamp' => date('c', $timestamp),
                'Value' => $value,
                'Unit' => $this->getMetricUnit($metric)
            ];
        }
        
        return $dataPoints;
    }
    
    /**
     * Get metric unit
     */
    private function getMetricUnit(string $metric): string
    {
        $units = [
            'CPUUtilization' => 'Percent',
            'NetworkIn' => 'Bytes',
            'NetworkOut' => 'Bytes',
            'DiskReadOps' => 'Count',
            'DiskWriteOps' => 'Count'
        ];
        
        return $units[$metric] ?? 'Count';
    }
    
    /**
     * Generate public IP
     */
    private function generatePublicIP(): string
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }
}

// S3 Service
class S3Service
{
    private array $credentials;
    private string $region;
    private array $buckets = [];
    private array $objects = [];
    
    public function __construct(array $credentials, string $region)
    {
        $this->credentials = $credentials;
        $this->region = $region;
        $this->initializeBuckets();
    }
    
    /**
     * Initialize sample buckets
     */
    private function initializeBuckets(): void
    {
        $this->buckets = [
            'my-app-bucket' => [
                'Name' => 'my-app-bucket',
                'CreationDate' => '2024-01-01T00:00:00Z',
                'Region' => $this->region,
                'Versioning' => ['Status' => 'Enabled'],
                'Encryption' => ['SSEAlgorithm' => 'AES256'],
                'Lifecycle' => true
            ],
            'backup-bucket' => [
                'Name' => 'backup-bucket',
                'CreationDate' => '2024-01-15T12:00:00Z',
                'Region' => $this->region,
                'Versioning' => ['Status' => 'Suspended'],
                'Encryption' => ['SSEAlgorithm' => 'AES256'],
                'Lifecycle' => false
            ]
        ];
        
        // Initialize sample objects
        $this->objects = [
            'my-app-bucket' => [
                'images/logo.png' => [
                    'Key' => 'images/logo.png',
                    'Size' => 24576,
                    'LastModified' => '2024-01-20T10:30:00Z',
                    'ETag' => '"d41d8cd98f00b204e9800998ecf8427e"',
                    'StorageClass' => 'STANDARD'
                ],
                'documents/report.pdf' => [
                    'Key' => 'documents/report.pdf',
                    'Size' => 1048576,
                    'LastModified' => '2024-01-22T14:15:00Z',
                    'ETag' => '"098f6bcd4621d373cade4e832627b4f6"',
                    'StorageClass' => 'STANDARD'
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
    public function createBucket(string $bucketName, array $config = []): array
    {
        if (isset($this->buckets[$bucketName])) {
            return ['error' => 'Bucket already exists'];
        }
        
        $bucket = [
            'Name' => $bucketName,
            'CreationDate' => date('c'),
            'Region' => $this->region,
            'Versioning' => ['Status' => $config['Versioning'] ?? 'Suspended'],
            'Encryption' => ['SSEAlgorithm' => $config['Encryption'] ?? 'AES256'],
            'Lifecycle' => $config['Lifecycle'] ?? false
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
            $objects = array_filter($objects, function($object) use ($prefix) {
                return strpos($object['Key'], $prefix) === 0;
            });
        }
        
        return array_values($objects);
    }
    
    /**
     * Upload object
     */
    public function putObject(string $bucketName, string $key, $content, array $metadata = []): array
    {
        if (!isset($this->objects[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        $object = [
            'Key' => $key,
            'Size' => strlen($content),
            'LastModified' => date('c'),
            'ETag' => '"' . md5($content) . '"',
            'StorageClass' => $metadata['StorageClass'] ?? 'STANDARD',
            'ContentType' => $metadata['ContentType'] ?? 'application/octet-stream',
            'Metadata' => $metadata
        ];
        
        $this->objects[$bucketName][$key] = $object;
        
        return $object;
    }
    
    /**
     * Get object
     */
    public function getObject(string $bucketName, string $key): array
    {
        if (!isset($this->objects[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        if (!isset($this->objects[$bucketName][$key])) {
            return ['error' => 'Object not found'];
        }
        
        $object = $this->objects[$bucketName][$key];
        
        // Simulate content retrieval
        $object['Body'] = 'Sample content for ' . $key;
        
        return $object;
    }
    
    /**
     * Delete object
     */
    public function deleteObject(string $bucketName, string $key): array
    {
        if (!isset($this->objects[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        if (!isset($this->objects[$bucketName][$key])) {
            return ['error' => 'Object not found'];
        }
        
        unset($this->objects[$bucketName][$key]);
        
        return ['success' => true, 'key' => $key];
    }
    
    /**
     * Get bucket metrics
     */
    public function getBucketMetrics(string $bucketName): array
    {
        if (!isset($this->buckets[$bucketName])) {
            return ['error' => 'Bucket not found'];
        }
        
        $objects = $this->objects[$bucketName] ?? [];
        $totalSize = array_sum(array_column($objects, 'Size'));
        $objectCount = count($objects);
        
        return [
            'BucketName' => $bucketName,
            'ObjectCount' => $objectCount,
            'TotalSize' => $totalSize,
            'AverageObjectSize' => $objectCount > 0 ? round($totalSize / $objectCount) : 0,
            'StorageClasses' => array_count_values(array_column($objects, 'StorageClass'))
        ];
    }
}

// Lambda Service
class LambdaService
{
    private array $credentials;
    private string $region;
    private array $functions = [];
    
    public function __construct(array $credentials, string $region)
    {
        $this->credentials = $credentials;
        $this->region = $region;
        $this->initializeFunctions();
    }
    
    /**
     * Initialize sample functions
     */
    private function initializeFunctions(): void
    {
        $this->functions = [
            'my-php-function' => [
                'FunctionName' => 'my-php-function',
                'Runtime' => 'php8.2',
                'Handler' => 'index.handler',
                'CodeSize' => 1024,
                'Description' => 'PHP Lambda function for processing data',
                'Timeout' => 30,
                'MemorySize' => 128,
                'LastModified' => '2024-01-20T15:30:00Z',
                'State' => 'Active',
                'Environment' => [
                    'Variables' => [
                        'APP_ENV' => 'production',
                        'DB_HOST' => 'localhost'
                    ]
                ]
            ],
            'image-processor' => [
                'FunctionName' => 'image-processor',
                'Runtime' => 'python3.9',
                'Handler' => 'lambda_function.lambda_handler',
                'CodeSize' => 2048,
                'Description' => 'Image processing Lambda function',
                'Timeout' => 60,
                'MemorySize' => 256,
                'LastModified' => '2024-01-18T09:15:00Z',
                'State' => 'Active',
                'Environment' => [
                    'Variables' => [
                        'BUCKET_NAME' => 'image-bucket',
                        'THUMBNAIL_SIZE' => '200x200'
                    ]
                ]
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
     * Create function
     */
    public function createFunction(array $config): array
    {
        $functionName = $config['FunctionName'];
        
        if (isset($this->functions[$functionName])) {
            return ['error' => 'Function already exists'];
        }
        
        $function = [
            'FunctionName' => $functionName,
            'Runtime' => $config['Runtime'] ?? 'php8.2',
            'Handler' => $config['Handler'] ?? 'index.handler',
            'CodeSize' => $config['CodeSize'] ?? 1024,
            'Description' => $config['Description'] ?? '',
            'Timeout' => $config['Timeout'] ?? 30,
            'MemorySize' => $config['MemorySize'] ?? 128,
            'LastModified' => date('c'),
            'State' => 'Active',
            'Environment' => [
                'Variables' => $config['Environment']['Variables'] ?? []
            ]
        ];
        
        $this->functions[$functionName] = $function;
        
        return $function;
    }
    
    /**
     * Invoke function
     */
    public function invokeFunction(string $functionName, array $payload = []): array
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
                'statusCode' => 200,
                'body' => json_encode([
                    'message' => 'Function executed successfully',
                    'input' => $payload,
                    'execution_time' => $executionTime
                ])
            ];
        } else {
            $response = [
                'statusCode' => 500,
                'body' => json_encode([
                    'error' => 'Function execution failed',
                    'message' => 'Internal server error'
                ])
            ];
        }
        
        return [
            'FunctionName' => $functionName,
            'StatusCode' => $response['statusCode'],
            'Payload' => $payload,
            'Response' => $response,
            'ExecutionTime' => $executionTime,
            'MemoryUsed' => rand(50, $function['MemorySize']),
            'LogResult' => 'Log stream name: ' . uniqid('aws/lambda/')
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
        
        $function['LastModified'] = date('c');
        
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
        
        // Simulate CloudWatch metrics
        $invocations = rand(100, 1000);
        $errors = rand(0, 50);
        $duration = rand(50, 500);
        
        return [
            'FunctionName' => $functionName,
            'Invocations' => $invocations,
            'Errors' => $errors,
            'ErrorRate' => round(($errors / $invocations) * 100, 2),
            'Duration' => [
                'Average' => $duration,
                'Minimum' => $duration - 50,
                'Maximum' => $duration + 100
            ],
            'Throttles' => rand(0, 10),
            'MemoryUsage' => [
                'Average' => rand(50, $function['MemorySize']),
                'Maximum' => $function['MemorySize']
            ]
        ];
    }
}

// CloudWatch Service
class CloudWatchService
{
    private array $credentials;
    private string $region;
    private array $metrics = [];
    
    public function __construct(array $credentials, string $region)
    {
        $this->credentials = $credentials;
        $this->region = $region;
        $this->initializeMetrics();
    }
    
    /**
     * Initialize metrics
     */
    private function initializeMetrics(): void
    {
        $this->metrics = [
            'CPUUtilization' => [
                'Namespace' => 'AWS/EC2',
                'MetricName' => 'CPUUtilization',
                'Unit' => 'Percent',
                'Statistics' => ['Average', 'Maximum', 'Minimum']
            ],
            'NetworkIn' => [
                'Namespace' => 'AWS/EC2',
                'MetricName' => 'NetworkIn',
                'Unit' => 'Bytes',
                'Statistics' => ['Sum', 'Average']
            ],
            'NetworkOut' => [
                'Namespace' => 'AWS/EC2',
                'MetricName' => 'NetworkOut',
                'Unit' => 'Bytes',
                'Statistics' => ['Sum', 'Average']
            ],
            'LambdaInvocations' => [
                'Namespace' => 'AWS/Lambda',
                'MetricName' => 'Invocations',
                'Unit' => 'Count',
                'Statistics' => ['Sum']
            ],
            'LambdaErrors' => [
                'Namespace' => 'AWS/Lambda',
                'MetricName' => 'Errors',
                'Unit' => 'Count',
                'Statistics' => ['Sum']
            ],
            'S3BucketSizeBytes' => [
                'Namespace' => 'AWS/S3',
                'MetricName' => 'BucketSizeBytes',
                'Unit' => 'Bytes',
                'Statistics' => ['Average']
            ]
        ];
    }
    
    /**
     * Get metric data
     */
    public function getMetricData(array $query): array
    {
        $metricName = $query['MetricName'] ?? '';
        $namespace = $query['Namespace'] ?? '';
        $dimensions = $query['Dimensions'] ?? [];
        $startTime = $query['StartTime'] ?? time() - 3600;
        $endTime = $query['EndTime'] ?? time();
        $period = $query['Period'] ?? 300;
        $statistics = $query['Statistics'] ?? ['Average'];
        
        // Generate data points
        $dataPoints = [];
        $currentTime = $startTime;
        
        while ($currentTime < $endTime) {
            $value = $this->generateMetricValue($metricName);
            
            $dataPoints[] = [
                'Timestamp' => date('c', $currentTime),
                'Value' => $value,
                'Unit' => $this->getMetricUnit($metricName)
            ];
            
            $currentTime += $period;
        }
        
        return [
            'Label' => $this->buildMetricLabel($query),
            'Datapoints' => $dataPoints
        ];
    }
    
    /**
     * Generate metric value
     */
    private function generateMetricValue(string $metricName): float
    {
        switch ($metricName) {
            case 'CPUUtilization':
                return rand(10, 80);
            case 'NetworkIn':
            case 'NetworkOut':
                return rand(1000, 10000);
            case 'Invocations':
            case 'Errors':
                return rand(1, 100);
            case 'BucketSizeBytes':
                return rand(1000000, 10000000);
            default:
                return rand(1, 1000);
        }
    }
    
    /**
     * Get metric unit
     */
    private function getMetricUnit(string $metricName): string
    {
        if (isset($this->metrics[$metricName])) {
            return $this->metrics[$metricName]['Unit'];
        }
        
        return 'Count';
    }
    
    /**
     * Build metric label
     */
    private function buildMetricLabel(array $query): string
    {
        $label = $query['MetricName'] ?? '';
        
        if (isset($query['Dimensions'])) {
            $dimensionLabels = [];
            foreach ($query['Dimensions'] as $dimension) {
                $dimensionLabels[] = $dimension['Name'] . '=' . $dimension['Value'];
            }
            $label .= ' (' . implode(', ', $dimensionLabels) . ')';
        }
        
        return $label;
    }
    
    /**
     * Put metric data
     */
    public function putMetricData(array $metricData): array
    {
        $namespace = $metricData['Namespace'] ?? '';
        $metricDataArray = $metricData['MetricData'] ?? [];
        
        foreach ($metricDataArray as $data) {
            $metricName = $data['MetricName'] ?? '';
            
            if (!isset($this->metrics[$metricName])) {
                $this->metrics[$metricName] = [
                    'Namespace' => $namespace,
                    'MetricName' => $metricName,
                    'Unit' => $data['Unit'] ?? 'Count',
                    'Statistics' => $data['Statistics'] ?? ['Average']
                ];
            }
        }
        
        return ['success' => true, 'count' => count($metricDataArray)];
    }
    
    /**
     * Create alarm
     */
    public function putMetricAlarm(array $alarmConfig): array
    {
        $alarmName = $alarmConfig['AlarmName'] ?? uniqid('alarm_');
        
        $alarm = [
            'AlarmName' => $alarmName,
            'AlarmDescription' => $alarmConfig['AlarmDescription'] ?? '',
            'MetricName' => $alarmConfig['MetricName'] ?? '',
            'Namespace' => $alarmConfig['Namespace'] ?? '',
            'Statistic' => $alarmConfig['Statistic'] ?? 'Average',
            'Period' => $alarmConfig['Period'] ?? 300,
            'EvaluationPeriods' => $alarmConfig['EvaluationPeriods'] ?? 2,
            'Threshold' => $alarmConfig['Threshold'] ?? 80,
            'ComparisonOperator' => $alarmConfig['ComparisonOperator'] ?? 'GreaterThanThreshold',
            'AlarmActions' => $alarmConfig['AlarmActions'] ?? [],
            'StateValue' => 'OK',
            'StateReason' => 'Threshold not crossed',
            'StateUpdatedTimestamp' => date('c')
        ];
        
        return $alarm;
    }
    
    /**
     * List alarms
     */
    public function describeAlarms(array $filters = []): array
    {
        // Simulate alarm data
        $alarms = [
            [
                'AlarmName' => 'HighCPUUtilization',
                'AlarmDescription' => 'CPU utilization is too high',
                'MetricName' => 'CPUUtilization',
                'Namespace' => 'AWS/EC2',
                'Threshold' => 80,
                'StateValue' => 'ALARM',
                'StateReason' => 'Threshold crossed',
                'StateUpdatedTimestamp' => date('c', time() - 3600)
            ],
            [
                'AlarmName' => 'LambdaErrors',
                'AlarmDescription' => 'Lambda function errors detected',
                'MetricName' => 'Errors',
                'Namespace' => 'AWS/Lambda',
                'Threshold' => 10,
                'StateValue' => 'OK',
                'StateReason' => 'Threshold not crossed',
                'StateUpdatedTimestamp' => date('c', time() - 7200)
            ]
        ];
        
        return $alarms;
    }
}

// AWS Services Examples
class AWSServicesExamples
{
    private AWSServiceManager $awsManager;
    
    public function __construct()
    {
        $credentials = [
            'key' => 'AKIAIOSFODNN7EXAMPLE',
            'secret' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY',
            'token' => 'temporary_session_token'
        ];
        
        $this->awsManager = new AWSServiceManager($credentials);
    }
    
    public function demonstrateEC2(): void
    {
        echo "AWS EC2 Service Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $ec2 = $this->awsManager->getService('ec2');
        
        // List instances
        echo "Listing EC2 Instances:\n";
        $instances = $ec2->listInstances();
        
        foreach ($instances as $instance) {
            echo "{$instance['InstanceId']} ({$instance['InstanceType']}): {$instance['State']['Name']}\n";
            if ($instance['PublicIpAddress']) {
                echo "  Public IP: {$instance['PublicIpAddress']}\n";
            }
            echo "  Tags: " . implode(', ', array_column($instance['Tags'], 'Value')) . "\n\n";
        }
        
        // Create new instance
        echo "Creating New Instance:\n";
        $newInstance = $ec2->createInstance([
            'InstanceType' => 't3.micro',
            'Tags' => [
                ['Key' => 'Name', 'Value' => 'Test Instance'],
                ['Key' => 'Environment', 'Value' => 'Development']
            ]
        ]);
        
        echo "Created: {$newInstance['InstanceId']}\n";
        echo "State: {$newInstance['State']['Name']}\n";
        echo "Public IP: {$newInstance['PublicIpAddress']}\n\n";
        
        // Get instance metrics
        echo "Instance Metrics (CPU Utilization):\n";
        $metrics = $ec2->getInstanceMetrics($newInstance['InstanceId'], 'CPUUtilization', 1);
        
        foreach (array_slice($metrics, 0, 5) as $metric) {
            echo "  {$metric['Timestamp']}: {$metric['Value']} {$metric['Unit']}\n";
        }
        
        // Stop instance
        echo "\nStopping Instance:\n";
        $stopResult = $ec2->stopInstance($newInstance['InstanceId']);
        echo "Status: {$stopResult['CurrentState']['Name']}\n";
    }
    
    public function demonstrateS3(): void
    {
        echo "\nAWS S3 Service Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $s3 = $this->awsManager->getService('s3');
        
        // List buckets
        echo "Listing S3 Buckets:\n";
        $buckets = $s3->listBuckets();
        
        foreach ($buckets as $bucket) {
            echo "{$bucket['Name']} ({$bucket['Region']})\n";
            echo "  Created: {$bucket['CreationDate']}\n";
            echo "  Versioning: {$bucket['Versioning']['Status']}\n";
            echo "  Encryption: {$bucket['Encryption']['SSEAlgorithm']}\n\n";
        }
        
        // Create new bucket
        echo "Creating New Bucket:\n";
        $newBucket = $s3->createBucket('test-bucket-' . uniqid(), [
            'Versioning' => 'Enabled',
            'Encryption' => 'AES256'
        ]);
        
        echo "Created: {$newBucket['Name']}\n";
        echo "Region: {$newBucket['Region']}\n\n";
        
        // Upload object
        echo "Uploading Object:\n";
        $content = 'This is a test file content';
        $object = $s3->putObject($newBucket['Name'], 'test-file.txt', $content, [
            'ContentType' => 'text/plain',
            'StorageClass' => 'STANDARD'
        ]);
        
        echo "Uploaded: {$object['Key']}\n";
        echo "Size: {$object['Size']} bytes\n";
        echo "ETag: {$object['ETag']}\n\n";
        
        // List objects
        echo "Listing Objects:\n";
        $objects = $s3->listObjects($newBucket['Name']);
        
        foreach ($objects as $obj) {
            echo "{$obj['Key']} ({$obj['Size']} bytes)\n";
        }
        
        // Get bucket metrics
        echo "\nBucket Metrics:\n";
        $metrics = $s3->getBucketMetrics($newBucket['Name']);
        echo "Object Count: {$metrics['ObjectCount']}\n";
        echo "Total Size: {$metrics['TotalSize']} bytes\n";
        echo "Average Size: {$metrics['AverageObjectSize']} bytes\n";
    }
    
    public function demonstrateLambda(): void
    {
        echo "\nAWS Lambda Service Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $lambda = $this->awsManager->getService('lambda');
        
        // List functions
        echo "Listing Lambda Functions:\n";
        $functions = $lambda->listFunctions();
        
        foreach ($functions as $function) {
            echo "{$function['FunctionName']} ({$function['Runtime']})\n";
            echo "  Handler: {$function['Handler']}\n";
            echo "  Memory: {$function['MemorySize']} MB\n";
            echo "  Timeout: {$function['Timeout']}s\n";
            echo "  State: {$function['State']}\n\n";
        }
        
        // Create new function
        echo "Creating New Function:\n";
        $newFunction = $lambda->createFunction([
            'FunctionName' => 'test-function-' . uniqid(),
            'Runtime' => 'php8.2',
            'Handler' => 'index.handler',
            'Description' => 'Test PHP Lambda function',
            'MemorySize' => 256,
            'Timeout' => 60,
            'Environment' => [
                'Variables' => [
                    'APP_ENV' => 'test',
                    'DEBUG' => 'true'
                ]
            ]
        ]);
        
        echo "Created: {$newFunction['FunctionName']}\n";
        echo "Runtime: {$newFunction['Runtime']}\n";
        echo "Memory: {$newFunction['MemorySize']} MB\n\n";
        
        // Invoke function
        echo "Invoking Function:\n";
        $payload = ['message' => 'Hello from PHP!'];
        $result = $lambda->invokeFunction($newFunction['FunctionName'], $payload);
        
        echo "Status Code: {$result['StatusCode']}\n";
        echo "Execution Time: {$result['ExecutionTime']}ms\n";
        echo "Memory Used: {$result['MemoryUsed']} MB\n";
        echo "Response: {$result['Response']['body']}\n\n";
        
        // Get function metrics
        echo "Function Metrics:\n";
        $metrics = $lambda->getFunctionMetrics($newFunction['FunctionName']);
        echo "Invocations: {$metrics['Invocations']}\n";
        echo "Errors: {$metrics['Errors']}\n";
        echo "Error Rate: {$metrics['ErrorRate']}%\n";
        echo "Avg Duration: {$metrics['Duration']['Average']}ms\n";
    }
    
    public function demonstrateCloudWatch(): void
    {
        echo "\nAWS CloudWatch Service Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $cloudwatch = $this->awsManager->getService('cloudwatch');
        
        // Get metric data
        echo "Getting CPU Utilization Metrics:\n";
        $query = [
            'Namespace' => 'AWS/EC2',
            'MetricName' => 'CPUUtilization',
            'Dimensions' => [
                ['Name' => 'InstanceId', 'Value' => 'i-1234567890abcdef0']
            ],
            'StartTime' => time() - 3600,
            'EndTime' => time(),
            'Period' => 300,
            'Statistics' => ['Average', 'Maximum']
        ];
        
        $metricData = $cloudwatch->getMetricData($query);
        
        echo "Label: {$metricData['Label']}\n";
        echo "Data Points: " . count($metricData['Datapoints']) . "\n";
        
        foreach (array_slice($metricData['Datapoints'], 0, 3) as $point) {
            echo "  {$point['Timestamp']}: {$point['Value']} {$point['Unit']}\n";
        }
        
        // Create alarm
        echo "\nCreating CloudWatch Alarm:\n";
        $alarm = $cloudwatch->putMetricAlarm([
            'AlarmName' => 'HighCPUUtilization-Test',
            'AlarmDescription' => 'CPU utilization exceeds 80%',
            'MetricName' => 'CPUUtilization',
            'Namespace' => 'AWS/EC2',
            'Threshold' => 80,
            'ComparisonOperator' => 'GreaterThanThreshold',
            'Period' => 300,
            'EvaluationPeriods' => 2
        ]);
        
        echo "Created: {$alarm['AlarmName']}\n";
        echo "Threshold: {$alarm['Threshold']}%\n";
        echo "State: {$alarm['StateValue']}\n\n";
        
        // List alarms
        echo "Listing Alarms:\n";
        $alarms = $cloudwatch->describeAlarms();
        
        foreach ($alarms as $alarm) {
            $stateColor = $alarm['StateValue'] === 'ALARM' ? '🔴' : '🟢';
            echo "$stateColor {$alarm['AlarmName']} ({$alarm['StateValue']})\n";
            echo "  {$alarm['AlarmDescription']}\n";
            echo "  Threshold: {$alarm['Threshold']}\n\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nAWS Services Best Practices\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. EC2 Best Practices:\n";
        echo "   • Use appropriate instance types\n";
        echo "   • Implement auto-scaling groups\n";
        echo "   • Use security groups properly\n";
        echo "   • Enable monitoring and logging\n";
        echo "   • Use EBS-optimized instances\n\n";
        
        echo "2. S3 Best Practices:\n";
        echo "   • Use appropriate storage classes\n";
        echo "   • Enable versioning for important data\n";
        echo "   • Implement lifecycle policies\n";
        echo "   • Use encryption at rest and in transit\n";
        echo "   • Monitor storage costs\n\n";
        
        echo "3. Lambda Best Practices:\n";
        echo "   • Optimize memory allocation\n";
        echo "   • Use appropriate timeout values\n";
        echo "   • Implement error handling\n";
        echo "   • Monitor function performance\n";
        echo "   • Use environment variables for configuration\n\n";
        
        echo "4. CloudWatch Best Practices:\n";
        echo "   • Set up appropriate alarms\n";
        echo "   • Use custom metrics for application monitoring\n";
        echo "   • Implement log aggregation\n";
        echo "   • Use metric filters for efficient monitoring\n";
        echo "   • Regularly review alarm thresholds\n\n";
        
        echo "5. Security Best Practices:\n";
        echo "   • Use IAM roles and policies\n";
        echo "   • Implement least privilege access\n";
        echo "   • Enable MFA for root account\n";
        echo "   • Use VPC for network isolation\n";
        echo "   • Regularly rotate access keys";
    }
    
    public function runAllExamples(): void
    {
        echo "AWS Services Integration Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateEC2();
        $this->demonstrateS3();
        $this->demonstrateLambda();
        $this->demonstrateCloudWatch();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runAWSServicesDemo(): void
{
    $examples = new AWSServicesExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAWSServicesDemo();
}
?>
