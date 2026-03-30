<?php
/**
 * Microsoft Azure Services Integration
 * 
 * Working with Azure cloud services using PHP SDK and REST APIs.
 */

// Azure Service Manager
class AzureServiceManager
{
    private array $credentials;
    private string $subscriptionId;
    private string $resourceGroup;
    private array $services = [];
    
    public function __construct(array $credentials, string $subscriptionId, string $resourceGroup)
    {
        $this->credentials = $credentials;
        $this->subscriptionId = $subscriptionId;
        $this->resourceGroup = $resourceGroup;
        $this->initializeServices();
    }
    
    /**
     * Initialize Azure services
     */
    private function initializeServices(): void
    {
        $this->services = [
            'virtual_machines' => new VirtualMachinesService($this->credentials, $this->subscriptionId, $this->resourceGroup),
            'blob_storage' => new BlobStorageService($this->credentials, $this->subscriptionId, $this->resourceGroup),
            'sql_database' => new SQLDatabaseService($this->credentials, $this->subscriptionId, $this->resourceGroup),
            'functions' => new FunctionsService($this->credentials, $this->subscriptionId, $this->resourceGroup),
            'app_service' => new AppService($this->credentials, $this->subscriptionId, $this->resourceGroup),
            'monitor' => new MonitorService($this->credentials, $this->subscriptionId, $this->resourceGroup)
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

// Virtual Machines Service
class VirtualMachinesService
{
    private array $credentials;
    private string $subscriptionId;
    private string $resourceGroup;
    private array $virtualMachines = [];
    
    public function __construct(array $credentials, string $subscriptionId, string $resourceGroup)
    {
        $this->credentials = $credentials;
        $this->subscriptionId = $subscriptionId;
        $this->resourceGroup = $resourceGroup;
        $this->initializeVirtualMachines();
    }
    
    /**
     * Initialize sample virtual machines
     */
    private function initializeVirtualMachines(): void
    {
        $this->virtualMachines = [
            'web-vm-01' => [
                'name' => 'web-vm-01',
                'location' => 'East US',
                'vmSize' => 'Standard_B1s',
                'osType' => 'Linux',
                'provisioningState' => 'Succeeded',
                'powerState' => 'VM running',
                'publicIpAddress' => '52.168.1.100',
                'privateIpAddress' => '10.0.0.4',
                'osDiskSizeGB' => 30,
                'dataDisks' => [
                    [
                        'name' => 'web-vm-01-data-disk-1',
                        'diskSizeGB' => 100,
                        'lun' => 0
                    ]
                ],
                'tags' => [
                    'Environment' => 'Production',
                    'Application' => 'WebApp'
                ],
                'createdTime' => '2024-01-15T10:30:00Z'
            ],
            'db-vm-01' => [
                'name' => 'db-vm-01',
                'location' => 'East US',
                'vmSize' => 'Standard_B2s',
                'osType' => 'Linux',
                'provisioningState' => 'Succeeded',
                'powerState' => 'VM deallocated',
                'publicIpAddress' => null,
                'privateIpAddress' => '10.0.0.5',
                'osDiskSizeGB' => 30,
                'dataDisks' => [
                    [
                        'name' => 'db-vm-01-data-disk-1',
                        'diskSizeGB' => 500,
                        'lun' => 0
                    ]
                ],
                'tags' => [
                    'Environment' => 'Production',
                    'Application' => 'Database'
                ],
                'createdTime' => '2024-01-10T08:15:00Z'
            ]
        ];
    }
    
    /**
     * List virtual machines
     */
    public function listVirtualMachines(): array
    {
        return array_values($this->virtualMachines);
    }
    
    /**
     * Get virtual machine details
     */
    public function getVirtualMachine(string $vmName): ?array
    {
        return $this->virtualMachines[$vmName] ?? null;
    }
    
    /**
     * Create virtual machine
     */
    public function createVirtualMachine(array $config): array
    {
        $vmName = $config['name'] ?? 'vm-' . uniqid();
        
        if (isset($this->virtualMachines[$vmName])) {
            return ['error' => 'Virtual machine already exists'];
        }
        
        $vm = [
            'name' => $vmName,
            'location' => $config['location'] ?? 'East US',
            'vmSize' => $config['vmSize'] ?? 'Standard_B1s',
            'osType' => $config['osType'] ?? 'Linux',
            'provisioningState' => 'Creating',
            'powerState' => 'VM starting',
            'publicIpAddress' => null,
            'privateIpAddress' => '10.0.0.' . rand(10, 200),
            'osDiskSizeGB' => $config['osDiskSizeGB'] ?? 30,
            'dataDisks' => [],
            'tags' => $config['tags'] ?? [],
            'createdTime' => date('c')
        ];
        
        $this->virtualMachines[$vmName] = $vm;
        
        // Simulate VM creation
        sleep(2);
        $this->virtualMachines[$vmName]['provisioningState'] = 'Succeeded';
        $this->virtualMachines[$vmName]['powerState'] = 'VM running';
        $this->virtualMachines[$vmName]['publicIpAddress'] = $this->generatePublicIP();
        
        return $vm;
    }
    
    /**
     * Start virtual machine
     */
    public function startVirtualMachine(string $vmName): array
    {
        if (!isset($this->virtualMachines[$vmName])) {
            return ['error' => 'Virtual machine not found'];
        }
        
        $vm = &$this->virtualMachines[$vmName];
        
        if ($vm['powerState'] === 'VM running') {
            return ['error' => 'Virtual machine is already running'];
        }
        
        $vm['powerState'] = 'VM starting';
        
        // Simulate starting
        sleep(1);
        $vm['powerState'] = 'VM running';
        
        return [
            'name' => $vmName,
            'status' => 'Started',
            'powerState' => $vm['powerState']
        ];
    }
    
    /**
     * Stop virtual machine
     */
    public function stopVirtualMachine(string $vmName): array
    {
        if (!isset($this->virtualMachines[$vmName])) {
            return ['error' => 'Virtual machine not found'];
        }
        
        $vm = &$this->virtualMachines[$vmName];
        
        if ($vm['powerState'] === 'VM stopped') {
            return ['error' => 'Virtual machine is already stopped'];
        }
        
        $vm['powerState'] = 'VM stopping';
        
        // Simulate stopping
        sleep(1);
        $vm['powerState'] = 'VM stopped';
        $vm['publicIpAddress'] = null;
        
        return [
            'name' => $vmName,
            'status' => 'Stopped',
            'powerState' => $vm['powerState']
        ];
    }
    
    /**
     * Delete virtual machine
     */
    public function deleteVirtualMachine(string $vmName): array
    {
        if (!isset($this->virtualMachines[$vmName])) {
            return ['error' => 'Virtual machine not found'];
        }
        
        unset($this->virtualMachines[$vmName]);
        
        return [
            'name' => $vmName,
            'status' => 'Deleted'
        ];
    }
    
    /**
     * Get VM metrics
     */
    public function getVMMetrics(string $vmName, string $metric, int $hours = 1): array
    {
        if (!isset($this->virtualMachines[$vmName])) {
            return ['error' => 'Virtual machine not found'];
        }
        
        // Simulate Azure Monitor metrics
        $dataPoints = [];
        $endTime = time();
        $startTime = $endTime - ($hours * 3600);
        
        for ($i = 0; $i < $hours * 12; $i++) {
            $timestamp = $startTime + ($i * 300); // 5-minute intervals
            
            switch ($metric) {
                case 'Percentage CPU':
                    $value = rand(10, 80);
                    break;
                case 'Network In':
                    $value = rand(1000, 10000);
                    break;
                case 'Network Out':
                    $value = rand(500, 5000);
                    break;
                case 'Disk Read Bytes/Sec':
                    $value = rand(100, 1000);
                    break;
                case 'Disk Write Bytes/Sec':
                    $value = rand(50, 500);
                    break;
                default:
                    $value = rand(1, 100);
            }
            
            $dataPoints[] = [
                'timestamp' => date('c', $timestamp),
                'value' => $value,
                'unit' => $this->getMetricUnit($metric)
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
            'Percentage CPU' => 'Percent',
            'Network In' => 'BytesPerSecond',
            'Network Out' => 'BytesPerSecond',
            'Disk Read Bytes/Sec' => 'BytesPerSecond',
            'Disk Write Bytes/Sec' => 'BytesPerSecond'
        ];
        
        return $units[$metric] ?? 'Count';
    }
    
    /**
     * Generate public IP
     */
    private function generatePublicIP(): string
    {
        return rand(52, 52) . '.' . rand(168, 168) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }
}

// Blob Storage Service
class BlobStorageService
{
    private array $credentials;
    private string $subscriptionId;
    private string $resourceGroup;
    private array $storageAccounts = [];
    private array $containers = [];
    private array $blobs = [];
    
    public function __construct(array $credentials, string $subscriptionId, string $resourceGroup)
    {
        $this->credentials = $credentials;
        $this->subscriptionId = $subscriptionId;
        $this->resourceGroup = $resourceGroup;
        $this->initializeStorageAccounts();
    }
    
    /**
     * Initialize storage accounts
     */
    private function initializeStorageAccounts(): void
    {
        $this->storageAccounts = [
            'webappstorage' => [
                'name' => 'webappstorage',
                'location' => 'East US',
                'accountType' => 'Standard_LRS',
                'provisioningState' => 'Succeeded',
                'creationTime' => '2024-01-01T00:00:00Z',
                'primaryLocation' => 'East US',
                'tags' => [
                    'Environment' => 'Production',
                    'Application' => 'WebApp'
                ]
            ],
            'backupstorage' => [
                'name' => 'backupstorage',
                'location' => 'East US',
                'accountType' => 'Standard_GRS',
                'provisioningState' => 'Succeeded',
                'creationTime' => '2024-01-15T12:00:00Z',
                'primaryLocation' => 'East US',
                'tags' => [
                    'Environment' => 'Production',
                    'Application' => 'Backup'
                ]
            ]
        ];
        
        // Initialize containers
        $this->containers = [
            'webappstorage' => [
                'images' => [
                    'name' => 'images',
                    'publicAccess' => 'Blob',
                    'lastModified' => '2024-01-20T10:30:00Z',
                    'etag' => '0x8D7B5CC3E5F8A3F'
                ],
                'documents' => [
                    'name' => 'documents',
                    'publicAccess' => 'None',
                    'lastModified' => '2024-01-22T14:15:00Z',
                    'etag' => '0x8D7B5CC3E5F8A4G'
                ]
            ]
        ];
        
        // Initialize blobs
        $this->blobs = [
            'webappstorage' => [
                'images' => [
                    'logo.png' => [
                        'name' => 'logo.png',
                        'size' => 24576,
                        'lastModified' => '2024-01-20T10:30:00Z',
                        'contentType' => 'image/png',
                        'blobType' => 'BlockBlob'
                    ],
                    'banner.jpg' => [
                        'name' => 'banner.jpg',
                        'size' => 1048576,
                        'lastModified' => '2024-01-21T09:15:00Z',
                        'contentType' => 'image/jpeg',
                        'blobType' => 'BlockBlob'
                    ]
                ],
                'documents' => [
                    'report.pdf' => [
                        'name' => 'report.pdf',
                        'size' => 2097152,
                        'lastModified' => '2024-01-22T14:15:00Z',
                        'contentType' => 'application/pdf',
                        'blobType' => 'BlockBlob'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * List storage accounts
     */
    public function listStorageAccounts(): array
    {
        return array_values($this->storageAccounts);
    }
    
    /**
     * Create storage account
     */
    public function createStorageAccount(array $config): array
    {
        $accountName = $config['name'] ?? 'storage' . uniqid();
        
        if (isset($this->storageAccounts[$accountName])) {
            return ['error' => 'Storage account already exists'];
        }
        
        $account = [
            'name' => $accountName,
            'location' => $config['location'] ?? 'East US',
            'accountType' => $config['accountType'] ?? 'Standard_LRS',
            'provisioningState' => 'Creating',
            'creationTime' => date('c'),
            'primaryLocation' => $config['location'] ?? 'East US',
            'tags' => $config['tags'] ?? []
        ];
        
        $this->storageAccounts[$accountName] = $account;
        $this->containers[$accountName] = [];
        $this->blobs[$accountName] = [];
        
        // Simulate creation
        sleep(2);
        $this->storageAccounts[$accountName]['provisioningState'] = 'Succeeded';
        
        return $account;
    }
    
    /**
     * List containers
     */
    public function listContainers(string $storageAccountName): array
    {
        if (!isset($this->containers[$storageAccountName])) {
            return ['error' => 'Storage account not found'];
        }
        
        return array_values($this->containers[$storageAccountName]);
    }
    
    /**
     * Create container
     */
    public function createContainer(string $storageAccountName, string $containerName, array $options = []): array
    {
        if (!isset($this->containers[$storageAccountName])) {
            return ['error' => 'Storage account not found'];
        }
        
        if (isset($this->containers[$storageAccountName][$containerName])) {
            return ['error' => 'Container already exists'];
        }
        
        $container = [
            'name' => $containerName,
            'publicAccess' => $options['publicAccess'] ?? 'None',
            'lastModified' => date('c'),
            'etag' => '0x' . bin2hex(random_bytes(8))
        ];
        
        $this->containers[$storageAccountName][$containerName] = $container;
        $this->blobs[$storageAccountName][$containerName] = [];
        
        return $container;
    }
    
    /**
     * List blobs
     */
    public function listBlobs(string $storageAccountName, string $containerName): array
    {
        if (!isset($this->blobs[$storageAccountName][$containerName])) {
            return ['error' => 'Container not found'];
        }
        
        return array_values($this->blobs[$storageAccountName][$containerName]);
    }
    
    /**
     * Upload blob
     */
    public function uploadBlob(string $storageAccountName, string $containerName, string $blobName, $content, array $metadata = []): array
    {
        if (!isset($this->blobs[$storageAccountName][$containerName])) {
            return ['error' => 'Container not found'];
        }
        
        $blob = [
            'name' => $blobName,
            'size' => strlen($content),
            'lastModified' => date('c'),
            'contentType' => $metadata['contentType'] ?? 'application/octet-stream',
            'blobType' => $metadata['blobType'] ?? 'BlockBlob',
            'etag' => '0x' . bin2hex(random_bytes(8))
        ];
        
        $this->blobs[$storageAccountName][$containerName][$blobName] = $blob;
        
        return $blob;
    }
    
    /**
     * Download blob
     */
    public function downloadBlob(string $storageAccountName, string $containerName, string $blobName): array
    {
        if (!isset($this->blobs[$storageAccountName][$containerName][$blobName])) {
            return ['error' => 'Blob not found'];
        }
        
        $blob = $this->blobs[$storageAccountName][$containerName][$blobName];
        
        // Simulate content retrieval
        $blob['content'] = 'Sample content for ' . $blobName;
        
        return $blob;
    }
    
    /**
     * Delete blob
     */
    public function deleteBlob(string $storageAccountName, string $containerName, string $blobName): array
    {
        if (!isset($this->blobs[$storageAccountName][$containerName][$blobName])) {
            return ['error' => 'Blob not found'];
        }
        
        unset($this->blobs[$storageAccountName][$containerName][$blobName]);
        
        return ['success' => true, 'blob' => $blobName];
    }
    
    /**
     * Get storage account metrics
     */
    public function getStorageAccountMetrics(string $storageAccountName): array
    {
        if (!isset($this->storageAccounts[$storageAccountName])) {
            return ['error' => 'Storage account not found'];
        }
        
        $totalBlobs = 0;
        $totalSize = 0;
        
        if (isset($this->blobs[$storageAccountName])) {
            foreach ($this->blobs[$storageAccountName] as $containerBlobs) {
                $totalBlobs += count($containerBlobs);
                $totalSize += array_sum(array_column($containerBlobs, 'size'));
            }
        }
        
        return [
            'storageAccount' => $storageAccountName,
            'totalBlobs' => $totalBlobs,
            'totalSize' => $totalSize,
            'containerCount' => count($this->containers[$storageAccountName] ?? []),
            'averageBlobSize' => $totalBlobs > 0 ? round($totalSize / $totalBlobs) : 0
        ];
    }
}

// App Service
class AppService
{
    private array $credentials;
    private string $subscriptionId;
    private string $resourceGroup;
    private array $webApps = [];
    
    public function __construct(array $credentials, string $subscriptionId, string $resourceGroup)
    {
        $this->credentials = $credentials;
        $this->subscriptionId = $subscriptionId;
        $this->resourceGroup = $resourceGroup;
        $this->initializeWebApps();
    }
    
    /**
     * Initialize web apps
     */
    private function initializeWebApps(): void
    {
        $this->webApps = [
            'mywebapp-prod' => [
                'name' => 'mywebapp-prod',
                'location' => 'East US',
                'state' => 'Running',
                'hostName' => 'mywebapp-prod.azurewebsites.net',
                'appServicePlan' => 'myapp-plan-prod',
                'runtime' => 'php',
                'version' => '8.2',
                'alwaysOn' => true,
                'httpsOnly' => true,
                'tags' => [
                    'Environment' => 'Production',
                    'Application' => 'WebApp'
                ],
                'createdTime' => '2024-01-15T10:30:00Z'
            ],
            'mywebapp-dev' => [
                'name' => 'mywebapp-dev',
                'location' => 'East US',
                'state' => 'Stopped',
                'hostName' => 'mywebapp-dev.azurewebsites.net',
                'appServicePlan' => 'myapp-plan-dev',
                'runtime' => 'php',
                'version' => '8.2',
                'alwaysOn' => false,
                'httpsOnly' => false,
                'tags' => [
                    'Environment' => 'Development',
                    'Application' => 'WebApp'
                ],
                'createdTime' => '2024-01-10T08:15:00Z'
            ]
        ];
    }
    
    /**
     * List web apps
     */
    public function listWebApps(): array
    {
        return array_values($this->webApps);
    }
    
    /**
     * Create web app
     */
    public function createWebApp(array $config): array
    {
        $appName = $config['name'] ?? 'webapp-' . uniqid();
        
        if (isset($this->webApps[$appName])) {
            return ['error' => 'Web app already exists'];
        }
        
        $webApp = [
            'name' => $appName,
            'location' => $config['location'] ?? 'East US',
            'state' => 'Creating',
            'hostName' => $appName . '.azurewebsites.net',
            'appServicePlan' => $config['appServicePlan'] ?? 'Default-Plan',
            'runtime' => $config['runtime'] ?? 'php',
            'version' => $config['version'] ?? '8.2',
            'alwaysOn' => $config['alwaysOn'] ?? false,
            'httpsOnly' => $config['httpsOnly'] ?? false,
            'tags' => $config['tags'] ?? [],
            'createdTime' => date('c')
        ];
        
        $this->webApps[$appName] = $webApp;
        
        // Simulate creation
        sleep(2);
        $this->webApps[$appName]['state'] = 'Running';
        
        return $webApp;
    }
    
    /**
     * Start web app
     */
    public function startWebApp(string $appName): array
    {
        if (!isset($this->webApps[$appName])) {
            return ['error' => 'Web app not found'];
        }
        
        $webApp = &$this->webApps[$appName];
        
        if ($webApp['state'] === 'Running') {
            return ['error' => 'Web app is already running'];
        }
        
        $webApp['state'] = 'Starting';
        
        // Simulate starting
        sleep(1);
        $webApp['state'] = 'Running';
        
        return [
            'name' => $appName,
            'status' => 'Started',
            'state' => $webApp['state']
        ];
    }
    
    /**
     * Stop web app
     */
    public function stopWebApp(string $appName): array
    {
        if (!isset($this->webApps[$appName])) {
            return ['error' => 'Web app not found'];
        }
        
        $webApp = &$this->webApps[$appName];
        
        if ($webApp['state'] === 'Stopped') {
            return ['error' => 'Web app is already stopped'];
        }
        
        $webApp['state'] = 'Stopping';
        
        // Simulate stopping
        sleep(1);
        $webApp['state'] = 'Stopped';
        
        return [
            'name' => $appName,
            'status' => 'Stopped',
            'state' => $webApp['state']
        ];
    }
    
    /**
     * Delete web app
     */
    public function deleteWebApp(string $appName): array
    {
        if (!isset($this->webApps[$appName])) {
            return ['error' => 'Web app not found'];
        }
        
        unset($this->webApps[$appName]);
        
        return [
            'name' => $appName,
            'status' => 'Deleted'
        ];
    }
    
    /**
     * Get web app metrics
     */
    public function getWebAppMetrics(string $appName, int $hours = 24): array
    {
        if (!isset($this->webApps[$appName])) {
            return ['error' => 'Web app not found'];
        }
        
        $webApp = $this->webApps[$appName];
        
        // Simulate Azure Monitor metrics
        return [
            'webApp' => $appName,
            'requests' => rand(1000, 10000),
            'errors' => rand(0, 100),
            'responseTime' => [
                'average' => rand(50, 500),
                'minimum' => 10,
                'maximum' => 1000
            ],
            'cpuTime' => rand(100, 1000),
            'memoryUsage' => rand(50, 512),
            'bandwidth' => [
                'in' => rand(1000000, 10000000),
                'out' => rand(500000, 5000000)
            ]
        ];
    }
    
    /**
     * Get web app logs
     */
    public function getWebAppLogs(string $appName, int $lines = 50): array
    {
        if (!isset($this->webApps[$appName])) {
            return ['error' => 'Web app not found'];
        }
        
        // Simulate log entries
        $logs = [];
        
        for ($i = 0; $i < $lines; $i++) {
            $timestamp = date('Y-m-d H:i:s', time() - ($i * 60));
            $level = ['INFO', 'WARNING', 'ERROR'][rand(0, 2)];
            $message = 'Sample log message ' . ($i + 1) . ' for ' . $appName;
            
            $logs[] = [
                'timestamp' => $timestamp,
                'level' => $level,
                'message' => $message,
                'source' => 'Application'
            ];
        }
        
        return $logs;
    }
}

// Azure Services Examples
class AzureServicesExamples
{
    private AzureServiceManager $azureManager;
    
    public function __construct()
    {
        $credentials = [
            'clientId' => 'your-client-id',
            'clientSecret' => 'your-client-secret',
            'tenantId' => 'your-tenant-id'
        ];
        
        $subscriptionId = 'your-subscription-id';
        $resourceGroup = 'your-resource-group';
        
        $this->azureManager = new AzureServiceManager($credentials, $subscriptionId, $resourceGroup);
    }
    
    public function demonstrateVirtualMachines(): void
    {
        echo "Azure Virtual Machines Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $vms = $this->azureManager->getService('virtual_machines');
        
        // List VMs
        echo "Listing Virtual Machines:\n";
        $virtualMachines = $vms->listVirtualMachines();
        
        foreach ($virtualMachines as $vm) {
            echo "{$vm['name']} ({$vm['vmSize']}): {$vm['powerState']}\n";
            if ($vm['publicIpAddress']) {
                echo "  Public IP: {$vm['publicIpAddress']}\n";
            }
            echo "  OS Type: {$vm['osType']}\n";
            echo "  Tags: " . json_encode($vm['tags']) . "\n\n";
        }
        
        // Create new VM
        echo "Creating New Virtual Machine:\n";
        $newVM = $vms->createVirtualMachine([
            'name' => 'test-vm-' . uniqid(),
            'location' => 'East US',
            'vmSize' => 'Standard_B1s',
            'osType' => 'Linux',
            'osDiskSizeGB' => 30,
            'tags' => [
                'Environment' => 'Development',
                'Application' => 'Test'
            ]
        ]);
        
        echo "Created: {$newVM['name']}\n";
        echo "State: {$newVM['provisioningState']}\n";
        echo "Power State: {$newVM['powerState']}\n";
        echo "Public IP: {$newVM['publicIpAddress']}\n\n";
        
        // Get VM metrics
        echo "VM Metrics (CPU Utilization):\n";
        $metrics = $vms->getVMMetrics($newVM['name'], 'Percentage CPU', 1);
        
        foreach (array_slice($metrics, 0, 5) as $metric) {
            echo "  {$metric['timestamp']}: {$metric['value']} {$metric['unit']}\n";
        }
        
        // Stop VM
        echo "\nStopping Virtual Machine:\n";
        $stopResult = $vms->stopVirtualMachine($newVM['name']);
        echo "Status: {$stopResult['status']}\n";
        echo "Power State: {$stopResult['powerState']}\n";
    }
    
    public function demonstrateBlobStorage(): void
    {
        echo "\nAzure Blob Storage Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $storage = $this->azureManager->getService('blob_storage');
        
        // List storage accounts
        echo "Listing Storage Accounts:\n";
        $accounts = $storage->listStorageAccounts();
        
        foreach ($accounts as $account) {
            echo "{$account['name']} ({$account['accountType']})\n";
            echo "  Location: {$account['location']}\n";
            echo "  State: {$account['provisioningState']}\n";
            echo "  Tags: " . json_encode($account['tags']) . "\n\n";
        }
        
        // List containers
        echo "Listing Containers:\n";
        $containers = $storage->listContainers('webappstorage');
        
        foreach ($containers as $container) {
            echo "{$container['name']} ({$container['publicAccess']})\n";
        }
        
        // Create new container
        echo "\nCreating New Container:\n";
        $newContainer = $storage->createContainer('webappstorage', 'test-container-' . uniqid(), [
            'publicAccess' => 'Blob'
        ]);
        
        echo "Created: {$newContainer['name']}\n";
        echo "Public Access: {$newContainer['publicAccess']}\n\n";
        
        // Upload blob
        echo "Uploading Blob:\n";
        $content = 'This is a test file content for Azure Blob Storage';
        $blob = $storage->uploadBlob('webappstorage', $newContainer['name'], 'test-file.txt', $content, [
            'contentType' => 'text/plain',
            'blobType' => 'BlockBlob'
        ]);
        
        echo "Uploaded: {$blob['name']}\n";
        echo "Size: {$blob['size']} bytes\n";
        echo "Content Type: {$blob['contentType']}\n\n";
        
        // List blobs
        echo "Listing Blobs:\n";
        $blobs = $storage->listBlobs('webappstorage', $newContainer['name']);
        
        foreach ($blobs as $blob) {
            echo "{$blob['name']} ({$blob['size']} bytes)\n";
        }
        
        // Get storage metrics
        echo "\nStorage Account Metrics:\n";
        $metrics = $storage->getStorageAccountMetrics('webappstorage');
        echo "Total Blobs: {$metrics['totalBlobs']}\n";
        echo "Total Size: {$metrics['totalSize']} bytes\n";
        echo "Container Count: {$metrics['containerCount']}\n";
        echo "Average Blob Size: {$metrics['averageBlobSize']} bytes\n";
    }
    
    public function demonstrateAppService(): void
    {
        echo "\nAzure App Service Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $appService = $this->azureManager->getService('app_service');
        
        // List web apps
        echo "Listing Web Apps:\n";
        $webApps = $appService->listWebApps();
        
        foreach ($webApps as $app) {
            echo "{$app['name']} ({$app['runtime']} {$app['version']})\n";
            echo "  State: {$app['state']}\n";
            echo "  Host Name: {$app['hostName']}\n";
            echo "  HTTPS Only: " . ($app['httpsOnly'] ? 'Yes' : 'No') . "\n";
            echo "  Always On: " . ($app['alwaysOn'] ? 'Yes' : 'No') . "\n\n";
        }
        
        // Create new web app
        echo "Creating New Web App:\n";
        $newApp = $appService->createWebApp([
            'name' => 'test-webapp-' . uniqid(),
            'location' => 'East US',
            'runtime' => 'php',
            'version' => '8.2',
            'alwaysOn' => false,
            'httpsOnly' => true,
            'tags' => [
                'Environment' => 'Development',
                'Application' => 'Test'
            ]
        ]);
        
        echo "Created: {$newApp['name']}\n";
        echo "State: {$newApp['state']}\n";
        echo "Host Name: {$newApp['hostName']}\n";
        echo "Runtime: {$newApp['runtime']} {$newApp['version']}\n\n";
        
        // Get web app metrics
        echo "Web App Metrics:\n";
        $metrics = $appService->getWebAppMetrics($newApp['name']);
        echo "Requests: {$metrics['requests']}\n";
        echo "Errors: {$metrics['errors']}\n";
        echo "Avg Response Time: {$metrics['responseTime']['average']}ms\n";
        echo "CPU Time: {$metrics['cpuTime']}ms\n";
        echo "Memory Usage: {$metrics['memoryUsage']}MB\n";
        echo "Bandwidth In: {$metrics['bandwidth']['in']} bytes\n";
        echo "Bandwidth Out: {$metrics['bandwidth']['out']} bytes\n\n";
        
        // Get web app logs
        echo "Web App Logs (last 5):\n";
        $logs = $appService->getWebAppLogs($newApp['name'], 5);
        
        foreach ($logs as $log) {
            echo "[{$log['timestamp']}] {$log['level']}: {$log['message']}\n";
        }
        
        // Stop web app
        echo "\nStopping Web App:\n";
        $stopResult = $appService->stopWebApp($newApp['name']);
        echo "Status: {$stopResult['status']}\n";
        echo "State: {$stopResult['state']}\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nAzure Services Best Practices\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. Virtual Machines:\n";
        echo "   • Use appropriate VM sizes\n";
        echo "   • Implement availability sets\n";
        echo "   • Use managed disks\n";
        echo "   • Enable backup and monitoring\n";
        echo "   • Use Azure Advisor recommendations\n\n";
        
        echo "2. Blob Storage:\n";
        echo "   • Use appropriate storage tiers\n";
        echo "   • Implement lifecycle policies\n";
        echo "   • Enable soft delete\n";
        echo "   • Use encryption at rest\n";
        echo "   • Monitor storage metrics\n\n";
        
        echo "3. App Service:\n";
        echo "   • Use appropriate app service plans\n";
        echo "   • Enable auto-scaling\n";
        echo "   • Implement deployment slots\n";
        echo "   • Use custom domains and SSL\n";
        echo "   • Enable backup and monitoring\n\n";
        
        echo "4. Security:\n";
        echo "   • Use Azure AD for authentication\n";
        echo "   • Implement network security groups\n";
        echo "   • Use Key Vault for secrets\n";
        echo "   • Enable Azure Security Center\n";
        echo "   • Regularly update and patch\n\n";
        
        echo "5. Cost Management:\n";
        echo "   • Use Azure Cost Management\n";
        echo "   • Set up budget alerts\n";
        echo "   • Use Azure Advisor for optimization\n";
        echo "   • Implement resource tagging\n";
        echo "   • Regularly review usage patterns";
    }
    
    public function runAllExamples(): void
    {
        echo "Microsoft Azure Services Integration Examples\n";
        echo str_repeat("=", 45) . "\n";
        
        $this->demonstrateVirtualMachines();
        $this->demonstrateBlobStorage();
        $this->demonstrateAppService();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runAzureServicesDemo(): void
{
    $examples = new AzureServicesExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAzureServicesDemo();
}
?>
