<?php
/**
 * Edge Computing in PHP
 * 
 * Edge computing concepts, distributed edge nodes, and edge application development.
 */

// Edge Computing Framework
class EdgeComputingFramework
{
    private array $edgeNodes;
    private array $applications;
    private EdgeOrchestrator $orchestrator;
    private EdgeResourceManager $resourceManager;
    private EdgeDataProcessor $dataProcessor;
    private EdgeSecurityManager $securityManager;
    private EdgeMonitoring $monitoring;
    
    public function __construct()
    {
        $this->edgeNodes = [];
        $this->applications = [];
        $this->orchestrator = new EdgeOrchestrator();
        $this->resourceManager = new EdgeResourceManager();
        $this->dataProcessor = new EdgeDataProcessor();
        $this->securityManager = new EdgeSecurityManager();
        $this->monitoring = new EdgeMonitoring();
        
        $this->initializeEdgeNodes();
    }
    
    private function initializeEdgeNodes(): void
    {
        // Create different types of edge nodes
        $nodeTypes = [
            'cloud_edge' => [
                'count' => 2,
                'cpu' => 16,
                'memory' => 64,
                'storage' => 1000,
                'bandwidth' => 10000,
                'location' => 'cloud'
            ],
            'regional_edge' => [
                'count' => 4,
                'cpu' => 8,
                'memory' => 32,
                'storage' => 500,
                'bandwidth' => 5000,
                'location' => 'regional'
            ],
            'local_edge' => [
                'count' => 8,
                'cpu' => 4,
                'memory' => 16,
                'storage' => 200,
                'bandwidth' => 1000,
                'location' => 'local'
            ],
            'device_edge' => [
                'count' => 16,
                'cpu' => 2,
                'memory' => 8,
                'storage' => 50,
                'bandwidth' => 100,
                'location' => 'device'
            ]
        ];
        
        foreach ($nodeTypes as $nodeType => $config) {
            for ($i = 0; $i < $config['count']; $i++) {
                $nodeId = "{$nodeType}_{$i}";
                $node = new EdgeNode($nodeId, $nodeType, $config);
                $this->edgeNodes[$nodeId] = $node;
                
                echo "Created edge node: $nodeId ($nodeType)\n";
            }
        }
    }
    
    public function addEdgeNode(EdgeNode $node): void
    {
        $this->edgeNodes[$node->getId()] = $node;
        $this->orchestrator->registerNode($node);
        $this->resourceManager->registerNode($node);
        $this->monitoring->registerNode($node);
        
        echo "Added edge node: {$node->getId()}\n";
    }
    
    public function removeEdgeNode(string $nodeId): void
    {
        if (!isset($this->edgeNodes[$nodeId])) {
            return;
        }
        
        $node = $this->edgeNodes[$nodeId];
        
        // Migrate applications to other nodes
        $this->orchestrator->migrateApplications($node);
        
        // Remove from all managers
        $this->orchestrator->unregisterNode($nodeId);
        $this->resourceManager->unregisterNode($nodeId);
        $this->monitoring->unregisterNode($nodeId);
        
        unset($this->edgeNodes[$nodeId]);
        
        echo "Removed edge node: $nodeId\n";
    }
    
    public function deployApplication(EdgeApplication $application, array $requirements = []): bool
    {
        echo "Deploying application: {$application->getName()}\n";
        
        // Find suitable edge nodes
        $suitableNodes = $this->orchestrator->findSuitableNodes($application, $requirements);
        
        if (empty($suitableNodes)) {
            echo "No suitable edge nodes found for application\n";
            return false;
        }
        
        // Deploy to selected nodes
        $deployedNodes = [];
        foreach ($suitableNodes as $node) {
            if ($this->resourceManager->canDeploy($node, $application)) {
                $this->resourceManager->deployApplication($node, $application);
                $deployedNodes[] = $node;
                echo "  Deployed to: {$node->getId()}\n";
            }
        }
        
        if (empty($deployedNodes)) {
            echo "Failed to deploy application to any node\n";
            return false;
        }
        
        // Register application
        $this->applications[$application->getId()] = $application;
        $application->setDeployedNodes($deployedNodes);
        
        // Start monitoring
        $this->monitoring->monitorApplication($application);
        
        echo "Application deployed successfully to " . count($deployedNodes) . " nodes\n";
        return true;
    }
    
    public function processData(string $nodeId, array $data, string $processingType = 'stream'): array
    {
        if (!isset($this->edgeNodes[$nodeId])) {
            throw new Exception("Edge node not found: $nodeId");
        }
        
        $node = $this->edgeNodes[$nodeId];
        
        echo "Processing data on node: $nodeId (type: $processingType)\n";
        
        // Apply security
        $this->securityManager->processData($data);
        
        // Process data based on type
        switch ($processingType) {
            case 'stream':
                return $this->dataProcessor->processStreamData($node, $data);
            case 'batch':
                return $this->dataProcessor->processBatchData($node, $data);
            case 'realtime':
                return $this->dataProcessor->processRealTimeData($node, $data);
            case 'ml_inference':
                return $this->dataProcessor->processMLInference($node, $data);
            default:
                return $this->dataProcessor->processGenericData($node, $data);
        }
    }
    
    public function orchestrateWorkload(Workload $workload): array
    {
        echo "Orchestrating workload: {$workload->getName()}\n";
        
        return $this->orchestrator->orchestrate($workload, $this->edgeNodes);
    }
    
    public function getEdgeNodes(): array
    {
        return $this->edgeNodes;
    }
    
    public function getEdgeNode(string $id): ?EdgeNode
    {
        return $this->edgeNodes[$id] ?? null;
    }
    
    public function getApplications(): array
    {
        return $this->applications;
    }
    
    public function getApplication(string $id): ?EdgeApplication
    {
        return $this->applications[$id] ?? null;
    }
    
    public function getOrchestrator(): EdgeOrchestrator
    {
        return $this->orchestrator;
    }
    
    public function getResourceManager(): EdgeResourceManager
    {
        return $this->resourceManager;
    }
    
    public function getDataProcessor(): EdgeDataProcessor
    {
        return $this->dataProcessor;
    }
    
    public function getSecurityManager(): EdgeSecurityManager
    {
        return $this->securityManager;
    }
    
    public function getMonitoring(): EdgeMonitoring
    {
        return $this->monitoring;
    }
    
    public function getSystemStatus(): array
    {
        return [
            'total_nodes' => count($this->edgeNodes),
            'active_nodes' => count(array_filter($this->edgeNodes, fn($node) => $node->isActive())),
            'total_applications' => count($this->applications),
            'cpu_utilization' => $this->resourceManager->getCPUUtilization(),
            'memory_utilization' => $this->resourceManager->getMemoryUtilization(),
            'network_utilization' => $this->resourceManager->getNetworkUtilization(),
            'security_events' => $this->securityManager->getSecurityEvents(),
            'monitoring_metrics' => $this->monitoring->getMetrics()
        ];
    }
}

// Edge Node
class EdgeNode
{
    private string $id;
    private string $type;
    private string $location;
    private EdgeResources $resources;
    private array $applications;
    private array $capabilities;
    private bool $active;
    private float $lastHeartbeat;
    private array $metrics;
    
    public function __construct(string $id, string $type, array $config)
    {
        $this->id = $id;
        $this->type = $type;
        $this->location = $config['location'] ?? 'unknown';
        $this->resources = new EdgeResources($config);
        $this->applications = [];
        $this->capabilities = $this->initializeCapabilities($type);
        $this->active = true;
        $this->lastHeartbeat = microtime(true);
        $this->metrics = [];
    }
    
    private function initializeCapabilities(string $type): array
    {
        $capabilities = [
            'cloud_edge' => ['ml_training', 'data_aggregation', 'complex_processing', 'storage'],
            'regional_edge' => ['ml_inference', 'data_processing', 'caching', 'analytics'],
            'local_edge' => ['realtime_processing', 'sensor_data', 'control_systems', 'basic_ml'],
            'device_edge' => ['sensor_processing', 'actuator_control', 'data_filtering', 'edge_ai']
        ];
        
        return $capabilities[$type] ?? ['basic_processing'];
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getLocation(): string
    {
        return $this->location;
    }
    
    public function getResources(): EdgeResources
    {
        return $this->resources;
    }
    
    public function getApplications(): array
    {
        return $this->applications;
    }
    
    public function getCapabilities(): array
    {
        return $this->capabilities;
    }
    
    public function isActive(): bool
    {
        return $this->active && (microtime(true) - $this->lastHeartbeat) < 30;
    }
    
    public function setActive(bool $active): void
    {
        $this->active = $active;
        $this->lastHeartbeat = microtime(true);
    }
    
    public function updateHeartbeat(): void
    {
        $this->lastHeartbeat = microtime(true);
    }
    
    public function addApplication(EdgeApplication $application): bool
    {
        if (!$this->resources->canAccommodate($application->getRequirements())) {
            return false;
        }
        
        $this->applications[$application->getId()] = $application;
        $this->resources->allocate($application->getRequirements());
        
        echo "Application {$application->getId()} added to node {$this->id}\n";
        return true;
    }
    
    public function removeApplication(string $applicationId): bool
    {
        if (!isset($this->applications[$applicationId])) {
            return false;
        }
        
        $application = $this->applications[$applicationId];
        $this->resources->deallocate($application->getRequirements());
        
        unset($this->applications[$applicationId]);
        
        echo "Application $applicationId removed from node {$this->id}\n";
        return true;
    }
    
    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->capabilities);
    }
    
    public function getUtilization(): array
    {
        return [
            'cpu' => $this->resources->getCPUUtilization(),
            'memory' => $this->resources->getMemoryUtilization(),
            'storage' => $this->resources->getStorageUtilization(),
            'bandwidth' => $this->resources->getBandwidthUtilization()
        ];
    }
    
    public function getMetrics(): array
    {
        return array_merge($this->getUtilization(), [
            'active_applications' => count($this->applications),
            'last_heartbeat' => $this->lastHeartbeat,
            'uptime' => microtime(true) - ($this->metrics['start_time'] ?? microtime(true))
        ]);
    }
    
    public function updateMetrics(): void
    {
        $this->metrics = array_merge($this->metrics, $this->getMetrics());
    }
    
    public function __toString(): string
    {
        return "EdgeNode(id: {$this->id}, type: {$this->type}, location: {$this->location}, active: " . ($this->active ? 'Yes' : 'No') . ")";
    }
}

// Edge Resources
class EdgeResources
{
    private int $totalCPU;
    private int $totalMemory;
    private int $totalStorage;
    private int $totalBandwidth;
    private int $usedCPU;
    private int $usedMemory;
    private int $usedStorage;
    private int $usedBandwidth;
    
    public function __construct(array $config)
    {
        $this->totalCPU = $config['cpu'] ?? 4;
        $this->totalMemory = $config['memory'] ?? 16;
        $this->totalStorage = $config['storage'] ?? 200;
        $this->totalBandwidth = $config['bandwidth'] ?? 1000;
        
        $this->usedCPU = 0;
        $this->usedMemory = 0;
        $this->usedStorage = 0;
        $this->usedBandwidth = 0;
    }
    
    public function allocate(array $requirements): bool
    {
        if (!$this->canAccommodate($requirements)) {
            return false;
        }
        
        $this->usedCPU += $requirements['cpu'] ?? 0;
        $this->usedMemory += $requirements['memory'] ?? 0;
        $this->usedStorage += $requirements['storage'] ?? 0;
        $this->usedBandwidth += $requirements['bandwidth'] ?? 0;
        
        return true;
    }
    
    public function deallocate(array $requirements): void
    {
        $this->usedCPU -= $requirements['cpu'] ?? 0;
        $this->usedMemory -= $requirements['memory'] ?? 0;
        $this->usedStorage -= $requirements['storage'] ?? 0;
        $this->usedBandwidth -= $requirements['bandwidth'] ?? 0;
        
        // Ensure we don't go negative
        $this->usedCPU = max(0, $this->usedCPU);
        $this->usedMemory = max(0, $this->usedMemory);
        $this->usedStorage = max(0, $this->usedStorage);
        $this->usedBandwidth = max(0, $this->usedBandwidth);
    }
    
    public function canAccommodate(array $requirements): bool
    {
        return ($this->totalCPU - $this->usedCPU) >= ($requirements['cpu'] ?? 0) &&
               ($this->totalMemory - $this->usedMemory) >= ($requirements['memory'] ?? 0) &&
               ($this->totalStorage - $this->usedStorage) >= ($requirements['storage'] ?? 0) &&
               ($this->totalBandwidth - $this->usedBandwidth) >= ($requirements['bandwidth'] ?? 0);
    }
    
    public function getCPUUtilization(): float
    {
        return $this->totalCPU > 0 ? ($this->usedCPU / $this->totalCPU) * 100 : 0;
    }
    
    public function getMemoryUtilization(): float
    {
        return $this->totalMemory > 0 ? ($this->usedMemory / $this->totalMemory) * 100 : 0;
    }
    
    public function getStorageUtilization(): float
    {
        return $this->totalStorage > 0 ? ($this->usedStorage / $this->totalStorage) * 100 : 0;
    }
    
    public function getBandwidthUtilization(): float
    {
        return $this->totalBandwidth > 0 ? ($this->usedBandwidth / $this->totalBandwidth) * 100 : 0;
    }
    
    public function getAvailableResources(): array
    {
        return [
            'cpu' => $this->totalCPU - $this->usedCPU,
            'memory' => $this->totalMemory - $this->usedMemory,
            'storage' => $this->totalStorage - $this->usedStorage,
            'bandwidth' => $this->totalBandwidth - $this->usedBandwidth
        ];
    }
    
    public function getTotalResources(): array
    {
        return [
            'cpu' => $this->totalCPU,
            'memory' => $this->totalMemory,
            'storage' => $this->totalStorage,
            'bandwidth' => $this->totalBandwidth
        ];
    }
}

// Edge Application
class EdgeApplication
{
    private string $id;
    private string $name;
    private string $type;
    private array $requirements;
    private array $deployedNodes;
    private array $capabilities;
    private bool $running;
    private float $startTime;
    
    public function __construct(string $id, string $name, string $type, array $requirements = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->requirements = array_merge([
            'cpu' => 1,
            'memory' => 2,
            'storage' => 10,
            'bandwidth' => 100
        ], $requirements);
        $this->deployedNodes = [];
        $this->capabilities = $this->initializeCapabilities($type);
        $this->running = false;
        $this->startTime = 0;
    }
    
    private function initializeCapabilities(string $type): array
    {
        $capabilities = [
            'data_processing' => ['stream_processing', 'batch_processing', 'data_filtering'],
            'ml_inference' => ['model_inference', 'prediction', 'classification'],
            'iot_gateway' => ['sensor_data', 'actuator_control', 'protocol_translation'],
            'content_delivery' => ['caching', 'compression', 'transcoding'],
            'security' => ['encryption', 'authentication', 'monitoring']
        ];
        
        return $capabilities[$type] ?? ['basic_processing'];
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getRequirements(): array
    {
        return $this->requirements;
    }
    
    public function getDeployedNodes(): array
    {
        return $this->deployedNodes;
    }
    
    public function setDeployedNodes(array $nodes): void
    {
        $this->deployedNodes = $nodes;
    }
    
    public function getCapabilities(): array
    {
        return $this->capabilities;
    }
    
    public function isRunning(): bool
    {
        return $this->running;
    }
    
    public function start(): void
    {
        $this->running = true;
        $this->startTime = microtime(true);
        
        echo "Application {$this->name} started\n";
    }
    
    public function stop(): void
    {
        $this->running = false;
        
        echo "Application {$this->name} stopped\n";
    }
    
    public function getRuntime(): float
    {
        return $this->startTime > 0 ? microtime(true) - $this->startTime : 0;
    }
    
    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->capabilities);
    }
    
    public function processRequest(array $request): array
    {
        if (!$this->running) {
            throw new Exception("Application {$this->name} is not running");
        }
        
        // Simulate processing
        $processingTime = rand(10, 100);
        usleep($processingTime * 1000);
        
        return [
            'application' => $this->name,
            'request_id' => $request['id'] ?? uniqid(),
            'processed' => true,
            'processing_time' => $processingTime,
            'timestamp' => microtime(true)
        ];
    }
    
    public function __toString(): string
    {
        return "EdgeApplication(id: {$this->id}, name: {$this->name}, type: {$this->type}, running: " . ($this->running ? 'Yes' : 'No') . ")";
    }
}

// Edge Orchestrator
class EdgeOrchestrator
{
    private array $registeredNodes;
    private array $deploymentStrategies;
    
    public function __construct()
    {
        $this->registeredNodes = [];
        $this->deploymentStrategies = [
            'round_robin' => new RoundRobinStrategy(),
            'load_balanced' => new LoadBalancedStrategy(),
            'capability_based' => new CapabilityBasedStrategy(),
            'location_aware' => new LocationAwareStrategy(),
            'latency_optimized' => new LatencyOptimizedStrategy()
        ];
    }
    
    public function registerNode(EdgeNode $node): void
    {
        $this->registeredNodes[$node->getId()] = $node;
    }
    
    public function unregisterNode(string $nodeId): void
    {
        unset($this->registeredNodes[$nodeId]);
    }
    
    public function findSuitableNodes(EdgeApplication $application, array $requirements = []): array
    {
        $strategy = $requirements['strategy'] ?? 'load_balanced';
        $strategyObj = $this->deploymentStrategies[$strategy] ?? $this->deploymentStrategies['load_balanced'];
        
        return $strategyObj->selectNodes($application, $this->registeredNodes, $requirements);
    }
    
    public function orchestrate(Workload $workload, array $edgeNodes): array
    {
        echo "Orchestrating workload: {$workload->getName()}\n";
        
        $strategy = $workload->getDeploymentStrategy() ?? 'load_balanced';
        $strategyObj = $this->deploymentStrategies[$strategy] ?? $this->deploymentStrategies['load_balanced'];
        
        $selectedNodes = $strategyObj->selectNodesForWorkload($workload, $edgeNodes);
        
        if (empty($selectedNodes)) {
            throw new Exception("No suitable nodes found for workload");
        }
        
        // Deploy workload to selected nodes
        $deploymentResults = [];
        foreach ($selectedNodes as $node) {
            $result = $this->deployWorkloadToNode($node, $workload);
            $deploymentResults[] = $result;
        }
        
        return $deploymentResults;
    }
    
    private function deployWorkloadToNode(EdgeNode $node, Workload $workload): array
    {
        echo "  Deploying workload to node: {$node->getId()}\n";
        
        // Simulate deployment
        $deploymentTime = rand(5, 30);
        sleep($deploymentTime);
        
        return [
            'node_id' => $node->getId(),
            'workload_id' => $workload->getId(),
            'deployment_time' => $deploymentTime,
            'success' => true,
            'timestamp' => microtime(true)
        ];
    }
    
    public function migrateApplications(EdgeNode $sourceNode): void
    {
        echo "Migrating applications from node: {$sourceNode->getId()}\n";
        
        $applications = $sourceNode->getApplications();
        
        foreach ($applications as $application) {
            $this->migrateApplication($application, $sourceNode);
        }
    }
    
    private function migrateApplication(EdgeApplication $application, EdgeNode $sourceNode): void
    {
        echo "  Migrating application: {$application->getId()}\n";
        
        // Find suitable target nodes
        $targetNodes = $this->findSuitableNodes($application);
        
        // Remove from source
        $sourceNode->removeApplication($application->getId());
        
        // Deploy to target
        if (!empty($targetNodes)) {
            $targetNode = $targetNodes[0];
            $targetNode->addApplication($application);
            
            echo "    Migrated to: {$targetNode->getId()}\n";
        } else {
            echo "    No suitable target node found\n";
        }
    }
    
    public function getRegisteredNodes(): array
    {
        return $this->registeredNodes;
    }
    
    public function getDeploymentStrategies(): array
    {
        return array_keys($this->deploymentStrategies);
    }
}

// Edge Resource Manager
class EdgeResourceManager
{
    private array $registeredNodes;
    private array $resourcePools;
    
    public function __construct()
    {
        $this->registeredNodes = [];
        $this->resourcePools = [];
    }
    
    public function registerNode(EdgeNode $node): void
    {
        $this->registeredNodes[$node->getId()] = $node;
    }
    
    public function unregisterNode(string $nodeId): void
    {
        unset($this->registeredNodes[$nodeId]);
    }
    
    public function canDeploy(EdgeNode $node, EdgeApplication $application): bool
    {
        return $node->getResources()->canAccommodate($application->getRequirements());
    }
    
    public function deployApplication(EdgeNode $node, EdgeApplication $application): bool
    {
        if (!$this->canDeploy($node, $application)) {
            return false;
        }
        
        return $node->addApplication($application);
    }
    
    public function getCPUUtilization(): float
    {
        $totalCPU = 0;
        $usedCPU = 0;
        
        foreach ($this->registeredNodes as $node) {
            $resources = $node->getResources();
            $totalCPU += $resources->getTotalResources()['cpu'];
            $usedCPU += $resources->getTotalResources()['cpu'] * ($resources->getCPUUtilization() / 100);
        }
        
        return $totalCPU > 0 ? ($usedCPU / $totalCPU) * 100 : 0;
    }
    
    public function getMemoryUtilization(): float
    {
        $totalMemory = 0;
        $usedMemory = 0;
        
        foreach ($this->registeredNodes as $node) {
            $resources = $node->getResources();
            $totalMemory += $resources->getTotalResources()['memory'];
            $usedMemory += $resources->getTotalResources()['memory'] * ($resources->getMemoryUtilization() / 100);
        }
        
        return $totalMemory > 0 ? ($usedMemory / $totalMemory) * 100 : 0;
    }
    
    public function getNetworkUtilization(): float
    {
        $totalBandwidth = 0;
        $usedBandwidth = 0;
        
        foreach ($this->registeredNodes as $node) {
            $resources = $node->getResources();
            $totalBandwidth += $resources->getTotalResources()['bandwidth'];
            $usedBandwidth += $resources->getTotalResources()['bandwidth'] * ($resources->getBandwidthUtilization() / 100);
        }
        
        return $totalBandwidth > 0 ? ($usedBandwidth / $totalBandwidth) * 100 : 0;
    }
    
    public function getResourceDistribution(): array
    {
        $distribution = [];
        
        foreach ($this->registeredNodes as $node) {
            $distribution[$node->getId()] = [
                'type' => $node->getType(),
                'location' => $node->getLocation(),
                'utilization' => $node->getUtilization(),
                'applications' => count($node->getApplications())
            ];
        }
        
        return $distribution;
    }
    
    public function optimizeResourceAllocation(): array
    {
        echo "Optimizing resource allocation...\n";
        
        $optimizations = [];
        
        // Find underutilized nodes
        foreach ($this->registeredNodes as $node) {
            $utilization = $node->getUtilization();
            
            if ($utilization['cpu'] < 20 && $utilization['memory'] < 20) {
                $optimizations[] = [
                    'type' => 'consolidation',
                    'node_id' => $node->getId(),
                    'reason' => 'Underutilized resources',
                    'utilization' => $utilization
                ];
            }
        }
        
        // Find overloaded nodes
        foreach ($this->registeredNodes as $node) {
            $utilization = $node->getUtilization();
            
            if ($utilization['cpu'] > 80 || $utilization['memory'] > 80) {
                $optimizations[] = [
                    'type' => 'scale_out',
                    'node_id' => $node->getId(),
                    'reason' => 'Resource overload',
                    'utilization' => $utilization
                ];
            }
        }
        
        return $optimizations;
    }
}

// Edge Data Processor
class EdgeDataProcessor
{
    private array $processingPipelines;
    private array $mlModels;
    
    public function __construct()
    {
        $this->processingPipelines = [
            'stream' => new StreamProcessingPipeline(),
            'batch' => new BatchProcessingPipeline(),
            'realtime' => new RealTimeProcessingPipeline(),
            'ml_inference' => new MLInferencePipeline()
        ];
        
        $this->mlModels = [
            'image_classification' => new ImageClassificationModel(),
            'anomaly_detection' => new AnomalyDetectionModel(),
            'time_series' => new TimeSeriesModel()
        ];
    }
    
    public function processStreamData(EdgeNode $node, array $data): array
    {
        $pipeline = $this->processingPipelines['stream'];
        
        echo "Processing stream data on node: {$node->getId()}\n";
        
        return $pipeline->process($data, [
            'node_capabilities' => $node->getCapabilities(),
            'node_resources' => $node->getResources()
        ]);
    }
    
    public function processBatchData(EdgeNode $node, array $data): array
    {
        $pipeline = $this->processingPipelines['batch'];
        
        echo "Processing batch data on node: {$node->getId()}\n";
        
        return $pipeline->process($data, [
            'node_capabilities' => $node->getCapabilities(),
            'node_resources' => $node->getResources()
        ]);
    }
    
    public function processRealTimeData(EdgeNode $node, array $data): array
    {
        $pipeline = $this->processingPipelines['realtime'];
        
        echo "Processing real-time data on node: {$node->getId()}\n";
        
        return $pipeline->process($data, [
            'node_capabilities' => $node->getCapabilities(),
            'node_resources' => $node->getResources()
        ]);
    }
    
    public function processMLInference(EdgeNode $node, array $data): array
    {
        $pipeline = $this->processingPipelines['ml_inference'];
        
        echo "Processing ML inference on node: {$node->getId()}\n";
        
        return $pipeline->process($data, [
            'node_capabilities' => $node->getCapabilities(),
            'node_resources' => $node->getResources(),
            'ml_models' => $this->mlModels
        ]);
    }
    
    public function processGenericData(EdgeNode $node, array $data): array
    {
        echo "Processing generic data on node: {$node->getId()}\n";
        
        // Basic processing
        $processedData = [
            'original_data' => $data,
            'processed_at' => microtime(true),
            'node_id' => $node->getId(),
            'node_type' => $node->getType(),
            'processing_time' => rand(1, 10)
        ];
        
        // Apply basic transformations
        if (isset($data['values']) && is_array($data['values'])) {
            $processedData['statistics'] = [
                'count' => count($data['values']),
                'sum' => array_sum($data['values']),
                'average' => array_sum($data['values']) / count($data['values']),
                'min' => min($data['values']),
                'max' => max($data['values'])
            ];
        }
        
        return $processedData;
    }
}

// Edge Security Manager
class EdgeSecurityManager
{
    private array $securityPolicies;
    private array $encryptionKeys;
    private array $accessTokens;
    private array $securityEvents;
    
    public function __construct()
    {
        $this->securityPolicies = [
            'data_encryption' => true,
            'access_control' => true,
            'authentication' => true,
            'audit_logging' => true
        ];
        
        $this->encryptionKeys = [];
        $this->accessTokens = [];
        $this->securityEvents = [];
    }
    
    public function processData(array &$data): void
    {
        if ($this->securityPolicies['data_encryption']) {
            $this->encryptData($data);
        }
        
        if ($this->securityPolicies['audit_logging']) {
            $this->logAccess($data);
        }
    }
    
    private function encryptData(array &$data): void
    {
        // Simulate encryption
        $data['encrypted'] = true;
        $data['encryption_timestamp'] = microtime(true);
        $data['encryption_method'] = 'AES-256';
    }
    
    private function logAccess(array $data): void
    {
        $this->securityEvents[] = [
            'type' => 'data_access',
            'timestamp' => microtime(true),
            'data_type' => $data['type'] ?? 'unknown',
            'data_size' => strlen(serialize($data))
        ];
    }
    
    public function authenticate(array $credentials): bool
    {
        if (!$this->securityPolicies['authentication']) {
            return true;
        }
        
        // Simulate authentication
        $username = $credentials['username'] ?? '';
        $password = $credentials['password'] ?? '';
        
        $valid = ($username === 'admin' && $password === 'secret');
        
        $this->securityEvents[] = [
            'type' => 'authentication',
            'timestamp' => microtime(true),
            'username' => $username,
            'success' => $valid
        ];
        
        return $valid;
    }
    
    public function authorize(string $token, array $permissions): bool
    {
        if (!$this->securityPolicies['access_control']) {
            return true;
        }
        
        // Simulate authorization
        $validToken = isset($this->accessTokens[$token]);
        
        $this->securityEvents[] = [
            'type' => 'authorization',
            'timestamp' => microtime(true),
            'token' => $token,
            'permissions' => $permissions,
            'success' => $validToken
        ];
        
        return $validToken;
    }
    
    public function generateToken(string $userId): string
    {
        $token = hash('sha256', $userId . microtime(true) . rand());
        $this->accessTokens[$token] = [
            'user_id' => $userId,
            'created_at' => microtime(true),
            'expires_at' => microtime(true) + 3600
        ];
        
        return $token;
    }
    
    public function getSecurityEvents(): array
    {
        return $this->securityEvents;
    }
    
    public function getSecurityMetrics(): array
    {
        $events = $this->securityEvents;
        
        return [
            'total_events' => count($events),
            'authentication_events' => count(array_filter($events, fn($e) => $e['type'] === 'authentication')),
            'authorization_events' => count(array_filter($events, fn($e) => $e['type'] === 'authorization')),
            'data_access_events' => count(array_filter($events, fn($e) => $e['type'] === 'data_access')),
            'successful_authentications' => count(array_filter($events, fn($e) => $e['type'] === 'authentication' && $e['success'])),
            'failed_authentications' => count(array_filter($events, fn($e) => $e['type'] === 'authentication' && !$e['success']))
        ];
    }
}

// Edge Monitoring
class EdgeMonitoring
{
    private array $registeredNodes;
    private array $metrics;
    private array $alerts;
    
    public function __construct()
    {
        $this->registeredNodes = [];
        $this->metrics = [];
        $this->alerts = [];
    }
    
    public function registerNode(EdgeNode $node): void
    {
        $this->registeredNodes[$node->getId()] = $node;
        $this->metrics[$node->getId()] = [];
    }
    
    public function unregisterNode(string $nodeId): void
    {
        unset($this->registeredNodes[$nodeId]);
        unset($this->metrics[$nodeId]);
    }
    
    public function monitorApplication(EdgeApplication $application): void
    {
        echo "Monitoring application: {$application->getName()}\n";
        
        // Collect application metrics
        $appMetrics = [
            'application_id' => $application->getId(),
            'name' => $application->getName(),
            'type' => $application->getType(),
            'running' => $application->isRunning(),
            'runtime' => $application->getRuntime(),
            'deployed_nodes' => count($application->getDeployedNodes()),
            'timestamp' => microtime(true)
        ];
        
        $this->metrics['applications'][$application->getId()] = $appMetrics;
        
        // Check for alerts
        $this->checkApplicationAlerts($application, $appMetrics);
    }
    
    public function collectMetrics(): void
    {
        echo "Collecting edge node metrics...\n";
        
        foreach ($this->registeredNodes as $node) {
            $node->updateMetrics();
            $this->metrics[$node->getId()][] = $node->getMetrics();
            
            // Keep only last 100 metric entries
            if (count($this->metrics[$node->getId()]) > 100) {
                array_shift($this->metrics[$node->getId()]);
            }
            
            // Check for alerts
            $this->checkNodeAlerts($node);
        }
    }
    
    private function checkNodeAlerts(EdgeNode $node): void
    {
        $utilization = $node->getUtilization();
        
        // CPU alert
        if ($utilization['cpu'] > 90) {
            $this->createAlert('high_cpu', $node->getId(), "CPU utilization is {$utilization['cpu']}%");
        }
        
        // Memory alert
        if ($utilization['memory'] > 90) {
            $this->createAlert('high_memory', $node->getId(), "Memory utilization is {$utilization['memory']}%");
        }
        
        // Storage alert
        if ($utilization['storage'] > 90) {
            $this->createAlert('high_storage', $node->getId(), "Storage utilization is {$utilization['storage']}%");
        }
        
        // Node down alert
        if (!$node->isActive()) {
            $this->createAlert('node_down', $node->getId(), "Node is not responding");
        }
    }
    
    private function checkApplicationAlerts(EdgeApplication $application, array $metrics): void
    {
        // Runtime alert
        if ($metrics['runtime'] > 3600 && !$application->isRunning()) {
            $this->createAlert('application_stopped', $application->getId(), "Application has been stopped for over an hour");
        }
        
        // Node count alert
        if ($metrics['deployed_nodes'] === 0 && $application->isRunning()) {
            $this->createAlert('no_deployment', $application->getId(), "Application is running but not deployed to any node");
        }
    }
    
    private function createAlert(string $type, string $target, string $message): void
    {
        $alert = [
            'id' => uniqid('alert_'),
            'type' => $type,
            'target' => $target,
            'message' => $message,
            'timestamp' => microtime(true),
            'severity' => $this->getAlertSeverity($type)
        ];
        
        $this->alerts[] = $alert;
        
        echo "ALERT: [{$alert['severity']}] {$message}\n";
    }
    
    private function getAlertSeverity(string $type): string
    {
        $severities = [
            'high_cpu' => 'warning',
            'high_memory' => 'warning',
            'high_storage' => 'warning',
            'node_down' => 'critical',
            'application_stopped' => 'warning',
            'no_deployment' => 'error'
        ];
        
        return $severities[$type] ?? 'info';
    }
    
    public function getMetrics(): array
    {
        return $this->metrics;
    }
    
    public function getAlerts(): array
    {
        return $this->alerts;
    }
    
    public function getSystemHealth(): array
    {
        $totalNodes = count($this->registeredNodes);
        $activeNodes = count(array_filter($this->registeredNodes, fn($node) => $node->isActive()));
        
        $criticalAlerts = count(array_filter($this->alerts, fn($alert) => $alert['severity'] === 'critical'));
        $warningAlerts = count(array_filter($this->alerts, fn($alert) => $alert['severity'] === 'warning'));
        
        return [
            'total_nodes' => $totalNodes,
            'active_nodes' => $activeNodes,
            'node_availability' => $totalNodes > 0 ? ($activeNodes / $totalNodes) * 100 : 0,
            'critical_alerts' => $criticalAlerts,
            'warning_alerts' => $warningAlerts,
            'health_status' => $criticalAlerts > 0 ? 'critical' : ($warningAlerts > 0 ? 'warning' : 'healthy')
        ];
    }
}

// Deployment Strategy Interface
interface DeploymentStrategy
{
    public function selectNodes(EdgeApplication $application, array $nodes, array $requirements): array;
    public function selectNodesForWorkload(Workload $workload, array $nodes): array;
}

// Round Robin Strategy
class RoundRobinStrategy implements DeploymentStrategy
{
    private int $currentIndex = 0;
    
    public function selectNodes(EdgeApplication $application, array $nodes, array $requirements): array
    {
        $selectedNodes = [];
        $nodeCount = $requirements['node_count'] ?? 1;
        
        $activeNodes = array_filter($nodes, fn($node) => $node->isActive());
        $nodeIds = array_keys($activeNodes);
        
        if (empty($nodeIds)) {
            return [];
        }
        
        for ($i = 0; $i < $nodeCount && $i < count($nodeIds); $i++) {
            $nodeId = $nodeIds[($this->currentIndex + $i) % count($nodeIds)];
            $selectedNodes[] = $activeNodes[$nodeId];
        }
        
        $this->currentIndex = ($this->currentIndex + $nodeCount) % count($nodeIds);
        
        return $selectedNodes;
    }
    
    public function selectNodesForWorkload(Workload $workload, array $nodes): array
    {
        return $this->selectNodes($workload, $nodes, ['node_count' => 1]);
    }
}

// Load Balanced Strategy
class LoadBalancedStrategy implements DeploymentStrategy
{
    public function selectNodes(EdgeApplication $application, array $nodes, array $requirements): array
    {
        $selectedNodes = [];
        $nodeCount = $requirements['node_count'] ?? 1;
        
        // Sort nodes by CPU utilization
        usort($nodes, function($a, $b) {
            $utilA = $a->getUtilization()['cpu'];
            $utilB = $b->getUtilization()['cpu'];
            return $utilA <=> $utilB;
        });
        
        foreach ($nodes as $node) {
            if (!$node->isActive()) {
                continue;
            }
            
            if ($node->getResources()->canAccommodate($application->getRequirements())) {
                $selectedNodes[] = $node;
                
                if (count($selectedNodes) >= $nodeCount) {
                    break;
                }
            }
        }
        
        return $selectedNodes;
    }
    
    public function selectNodesForWorkload(Workload $workload, array $nodes): array
    {
        return $this->selectNodes($workload, $nodes, ['node_count' => 1]);
    }
}

// Capability Based Strategy
class CapabilityBasedStrategy implements DeploymentStrategy
{
    public function selectNodes(EdgeApplication $application, array $nodes, array $requirements): array
    {
        $selectedNodes = [];
        $nodeCount = $requirements['node_count'] ?? 1;
        
        foreach ($nodes as $node) {
            if (!$node->isActive()) {
                continue;
            }
            
            // Check if node has required capabilities
            $hasCapabilities = true;
            foreach ($application->getCapabilities() as $capability) {
                if (!$node->hasCapability($capability)) {
                    $hasCapabilities = false;
                    break;
                }
            }
            
            if ($hasCapabilities && $node->getResources()->canAccommodate($application->getRequirements())) {
                $selectedNodes[] = $node;
                
                if (count($selectedNodes) >= $nodeCount) {
                    break;
                }
            }
        }
        
        return $selectedNodes;
    }
    
    public function selectNodesForWorkload(Workload $workload, array $nodes): array
    {
        return $this->selectNodes($workload, $nodes, ['node_count' => 1]);
    }
}

// Location Aware Strategy
class LocationAwareStrategy implements DeploymentStrategy
{
    public function selectNodes(EdgeApplication $application, array $nodes, array $requirements): array
    {
        $selectedNodes = [];
        $nodeCount = $requirements['node_count'] ?? 1;
        $preferredLocation = $requirements['location'] ?? 'local';
        
        // Filter nodes by location preference
        $locationNodes = array_filter($nodes, function($node) use ($preferredLocation) {
            return $node->isActive() && $node->getLocation() === $preferredLocation;
        });
        
        // If no nodes in preferred location, fall back to any location
        if (empty($locationNodes)) {
            $locationNodes = array_filter($nodes, fn($node) => $node->isActive());
        }
        
        // Sort by proximity (simplified - just use any available nodes)
        foreach ($locationNodes as $node) {
            if ($node->getResources()->canAccommodate($application->getRequirements())) {
                $selectedNodes[] = $node;
                
                if (count($selectedNodes) >= $nodeCount) {
                    break;
                }
            }
        }
        
        return $selectedNodes;
    }
    
    public function selectNodesForWorkload(Workload $workload, array $nodes): array
    {
        return $this->selectNodes($workload, $nodes, ['node_count' => 1]);
    }
}

// Latency Optimized Strategy
class LatencyOptimizedStrategy implements DeploymentStrategy
{
    public function selectNodes(EdgeApplication $application, array $nodes, array $requirements): array
    {
        $selectedNodes = [];
        $nodeCount = $requirements['node_count'] ?? 1;
        $clientLocation = $requirements['client_location'] ?? null;
        
        if (!$clientLocation) {
            // Fall back to load balanced strategy
            $loadBalanced = new LoadBalancedStrategy();
            return $loadBalanced->selectNodes($application, $nodes, $requirements);
        }
        
        // Simulate latency calculation based on node type
        $latencyMap = [
            'device_edge' => 1,
            'local_edge' => 5,
            'regional_edge' => 20,
            'cloud_edge' => 50
        ];
        
        // Sort nodes by latency (lower is better)
        usort($nodes, function($a, $b) use ($latencyMap) {
            $latencyA = $latencyMap[$a->getType()] ?? 100;
            $latencyB = $latencyMap[$b->getType()] ?? 100;
            return $latencyA <=> $latencyB;
        });
        
        foreach ($nodes as $node) {
            if (!$node->isActive()) {
                continue;
            }
            
            if ($node->getResources()->canAccommodate($application->getRequirements())) {
                $selectedNodes[] = $node;
                
                if (count($selectedNodes) >= $nodeCount) {
                    break;
                }
            }
        }
        
        return $selectedNodes;
    }
    
    public function selectNodesForWorkload(Workload $workload, array $nodes): array
    {
        return $this->selectNodes($workload, $nodes, ['node_count' => 1]);
    }
}

// Workload
class Workload
{
    private string $id;
    private string $name;
    private string $type;
    private array $requirements;
    private ?string $deploymentStrategy;
    
    public function __construct(string $id, string $name, string $type, array $requirements = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->requirements = array_merge([
            'cpu' => 1,
            'memory' => 2,
            'storage' => 10,
            'bandwidth' => 100
        ], $requirements);
        $this->deploymentStrategy = null;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getRequirements(): array
    {
        return $this->requirements;
    }
    
    public function getDeploymentStrategy(): ?string
    {
        return $this->deploymentStrategy;
    }
    
    public function setDeploymentStrategy(string $strategy): void
    {
        $this->deploymentStrategy = $strategy;
    }
}

// Processing Pipeline Base Class
abstract class ProcessingPipeline
{
    protected string $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    abstract public function process(array $data, array $context): array;
}

// Stream Processing Pipeline
class StreamProcessingPipeline extends ProcessingPipeline
{
    public function __construct()
    {
        parent::__construct('stream');
    }
    
    public function process(array $data, array $context): array
    {
        echo "  Stream processing pipeline\n";
        
        // Simulate stream processing
        $processedData = [
            'pipeline' => $this->name,
            'input_data' => $data,
            'processing_steps' => ['filtering', 'transformation', 'aggregation'],
            'processed_at' => microtime(true),
            'window_size' => $data['window_size'] ?? 1000,
            'throughput' => rand(1000, 10000)
        ];
        
        // Apply stream-specific processing
        if (isset($data['stream'])) {
            $processedData['stream_stats'] = [
                'events_processed' => count($data['stream']),
                'processing_rate' => rand(100, 1000),
                'latency' => rand(1, 50)
            ];
        }
        
        return $processedData;
    }
}

// Batch Processing Pipeline
class BatchProcessingPipeline extends ProcessingPipeline
{
    public function __construct()
    {
        parent::__construct('batch');
    }
    
    public function process(array $data, array $context): array
    {
        echo "  Batch processing pipeline\n";
        
        // Simulate batch processing
        $processedData = [
            'pipeline' => $this->name,
            'input_data' => $data,
            'processing_steps' => ['validation', 'transformation', 'aggregation', 'storage'],
            'processed_at' => microtime(true),
            'batch_size' => $data['batch_size'] ?? 100,
            'processing_time' => rand(10, 60)
        ];
        
        // Apply batch-specific processing
        if (isset($data['batch'])) {
            $processedData['batch_stats'] = [
                'records_processed' => count($data['batch']),
                'success_rate' => rand(0.95, 1.0),
                'error_count' => rand(0, 5)
            ];
        }
        
        return $processedData;
    }
}

// Real-time Processing Pipeline
class RealTimeProcessingPipeline extends ProcessingPipeline
{
    public function __construct()
    {
        parent::__construct('realtime');
    }
    
    public function process(array $data, array $context): array
    {
        echo "  Real-time processing pipeline\n";
        
        // Simulate real-time processing
        $processedData = [
            'pipeline' => $this->name,
            'input_data' => $data,
            'processing_steps' => ['ingestion', 'filtering', 'analysis', 'action'],
            'processed_at' => microtime(true),
            'latency' => rand(1, 10),
            'deadline' => $data['deadline'] ?? 100
        ];
        
        // Apply real-time-specific processing
        if (isset($data['event'])) {
            $processedData['event_processing'] = [
                'event_id' => $data['event']['id'] ?? uniqid(),
                'processing_latency' => rand(1, 5),
                'action_taken' => rand(0, 1) === 1
            ];
        }
        
        return $processedData;
    }
}

// ML Inference Pipeline
class MLInferencePipeline extends ProcessingPipeline
{
    public function __construct()
    {
        parent::__construct('ml_inference');
    }
    
    public function process(array $data, array $context): array
    {
        echo "  ML inference pipeline\n";
        
        // Simulate ML inference
        $processedData = [
            'pipeline' => $this->name,
            'input_data' => $data,
            'processing_steps' => ['preprocessing', 'inference', 'postprocessing'],
            'processed_at' => microtime(true),
            'model_type' => $data['model_type'] ?? 'image_classification',
            'inference_time' => rand(10, 100)
        ];
        
        // Apply ML-specific processing
        if (isset($context['ml_models'])) {
            $modelType = $data['model_type'] ?? 'image_classification';
            $model = $context['ml_models'][$modelType] ?? null;
            
            if ($model) {
                $processedData['inference_result'] = $model->predict($data);
            }
        }
        
        return $processedData;
    }
}

// ML Model Base Class
abstract class MLModel
{
    protected string $name;
    protected array $parameters;
    
    public function __construct(string $name, array $parameters = [])
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    abstract public function predict(array $data): array;
}

// Image Classification Model
class ImageClassificationModel extends MLModel
{
    public function __construct()
    {
        parent::__construct('image_classification');
    }
    
    public function predict(array $data): array
    {
        // Simulate image classification
        $classes = ['cat', 'dog', 'bird', 'car', 'person'];
        $predictions = [];
        
        foreach ($classes as $class) {
            $predictions[$class] = rand(0, 100) / 100;
        }
        
        // Normalize predictions
        $total = array_sum($predictions);
        foreach ($predictions as $class => $prob) {
            $predictions[$class] = $prob / $total;
        }
        
        arsort($predictions);
        
        return [
            'predictions' => $predictions,
            'top_class' => array_key_first($predictions),
            'confidence' => array_values($predictions)[0]
        ];
    }
}

// Anomaly Detection Model
class AnomalyDetectionModel extends MLModel
{
    public function __construct()
    {
        parent::__construct('anomaly_detection');
    }
    
    public function predict(array $data): array
    {
        // Simulate anomaly detection
        $features = $data['features'] ?? [rand(0, 100), rand(0, 100), rand(0, 100)];
        $anomalyScore = 0;
        
        foreach ($features as $feature) {
            if ($feature > 80 || $feature < 20) {
                $anomalyScore += 25;
            }
        }
        
        $isAnomaly = $anomalyScore > 50;
        
        return [
            'anomaly_score' => $anomalyScore,
            'is_anomaly' => $isAnomaly,
            'confidence' => abs($anomalyScore - 50) / 50
        ];
    }
}

// Time Series Model
class TimeSeriesModel extends MLModel
{
    public function __construct()
    {
        parent::__construct('time_series');
    }
    
    public function predict(array $data): array
    {
        // Simulate time series prediction
        $history = $data['history'] ?? [rand(10, 50), rand(10, 50), rand(10, 50)];
        $trend = (end($history) - $history[0]) / count($history);
        
        $predictions = [];
        for ($i = 1; $i <= 5; $i++) {
            $predictions[$i] = end($history) + ($trend * $i) + rand(-5, 5);
        }
        
        return [
            'predictions' => $predictions,
            'trend' => $trend,
            'confidence' => rand(0.7, 0.95)
        ];
    }
}

// Edge Computing Examples
class EdgeComputingExamples
{
    public function demonstrateBasicEdgeSetup(): void
    {
        echo "Basic Edge Computing Setup Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        $edgeFramework = new EdgeComputingFramework();
        
        echo "Edge computing framework initialized\n";
        
        // Show edge nodes
        echo "\nEdge nodes:\n";
        $nodes = $edgeFramework->getEdgeNodes();
        
        foreach ($nodes as $node) {
            echo "  {$node->getId()}: {$node->getType()} ({$node->getLocation()})\n";
            echo "    Capabilities: " . implode(', ', $node->getCapabilities()) . "\n";
            echo "    Resources: CPU={$node->getResources()->getTotalResources()['cpu']}, ";
            echo "Memory={$node->getResources()->getTotalResources()['memory']}GB\n";
        }
        
        // Show system status
        echo "\nSystem status:\n";
        $status = $edgeFramework->getSystemStatus();
        
        foreach ($status as $key => $value) {
            if (is_bool($value)) {
                echo "  $key: " . ($value ? 'Yes' : 'No') . "\n";
            } elseif (is_array($value)) {
                echo "  $key: " . json_encode($value) . "\n";
            } else {
                echo "  $key: $value\n";
            }
        }
    }
    
    public function demonstrateApplicationDeployment(): void
    {
        echo "\nApplication Deployment Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $edgeFramework = new EdgeComputingFramework();
        
        // Create different types of applications
        $applications = [
            new EdgeApplication('iot_gateway', 'IoT Gateway', 'iot_gateway', [
                'cpu' => 1,
                'memory' => 2,
                'storage' => 5,
                'bandwidth' => 200
            ]),
            new EdgeApplication('ml_inference', 'ML Inference', 'ml_inference', [
                'cpu' => 4,
                'memory' => 8,
                'storage' => 20,
                'bandwidth' => 500
            ]),
            new EdgeApplication('content_cache', 'Content Cache', 'content_delivery', [
                'cpu' => 2,
                'memory' => 4,
                'storage' => 50,
                'bandwidth' => 1000
            ]),
            new EdgeApplication('data_processor', 'Data Processor', 'data_processing', [
                'cpu' => 3,
                'memory' => 6,
                'storage' => 15,
                'bandwidth' => 300
            ])
        ];
        
        // Deploy applications
        echo "Deploying applications:\n";
        
        foreach ($applications as $app) {
            $success = $edgeFramework->deployApplication($app, [
                'strategy' => 'capability_based',
                'node_count' => 2
            ]);
            
            echo "  {$app->getName()}: " . ($success ? 'Success' : 'Failed') . "\n";
        }
        
        // Show deployment results
        echo "\nDeployment results:\n";
        
        foreach ($applications as $app) {
            echo "  {$app->getName()}:\n";
            echo "    Deployed to: " . count($app->getDeployedNodes()) . " nodes\n";
            echo "    Capabilities: " . implode(', ', $app->getCapabilities()) . "\n";
            
            foreach ($app->getDeployedNodes() as $node) {
                echo "      - {$node->getId()} ({$node->getType()})\n";
            }
        }
        
        // Show resource utilization
        echo "\nResource utilization:\n";
        $status = $edgeFramework->getSystemStatus();
        
        echo "  CPU: " . round($status['cpu_utilization'], 2) . "%\n";
        echo "  Memory: " . round($status['memory_utilization'], 2) . "%\n";
        echo "  Network: " . round($status['network_utilization'], 2) . "%\n";
    }
    
    public function demonstrateDataProcessing(): void
    {
        echo "\nEdge Data Processing Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $edgeFramework = new EdgeComputingFramework();
        
        // Get different types of edge nodes
        $deviceNode = null;
        $localNode = null;
        $cloudNode = null;
        
        foreach ($edgeFramework->getEdgeNodes() as $node) {
            if ($node->getType() === 'device_edge' && !$deviceNode) {
                $deviceNode = $node;
            } elseif ($node->getType() === 'local_edge' && !$localNode) {
                $localNode = $node;
            } elseif ($node->getType() === 'cloud_edge' && !$cloudNode) {
                $cloudNode = $node;
            }
        }
        
        // Test different processing types
        $dataTypes = [
            'stream' => [
                'stream' => range(1, 100),
                'window_size' => 1000,
                'source' => 'sensor'
            ],
            'batch' => [
                'batch' => range(1, 1000),
                'batch_size' => 1000,
                'source' => 'database'
            ],
            'realtime' => [
                'event' => ['id' => uniqid(), 'type' => 'alert', 'value' => rand(1, 100)],
                'deadline' => 100,
                'source' => 'monitor'
            ],
            'ml_inference' => [
                'model_type' => 'image_classification',
                'image_data' => 'base64_encoded_image_data',
                'features' => [rand(0, 255), rand(0, 255), rand(0, 255)]
            ]
        ];
        
        echo "Processing data on different edge nodes:\n";
        
        foreach ($dataTypes as $processingType => $data) {
            echo "\n$processingType processing:\n";
            
            if ($deviceNode && $processingType === 'stream') {
                $result = $edgeFramework->processData($deviceNode->getId(), $data, $processingType);
                echo "  Device Edge: {$result['pipeline']}, throughput: {$result['throughput']}\n";
            }
            
            if ($localNode && in_array($processingType, ['batch', 'realtime'])) {
                $result = $edgeFramework->processData($localNode->getId(), $data, $processingType);
                echo "  Local Edge: {$result['pipeline']}, processing_time: {$result['processing_time']}ms\n";
            }
            
            if ($cloudNode && $processingType === 'ml_inference') {
                $result = $edgeFramework->processData($cloudNode->getId(), $data, $processingType);
                echo "  Cloud Edge: {$result['pipeline']}, inference_time: {$result['inference_time']}ms\n";
                
                if (isset($result['inference_result'])) {
                    $inference = $result['inference_result'];
                    echo "    Prediction: {$inference['top_class']} (confidence: " . round($inference['confidence'], 2) . ")\n";
                }
            }
        }
    }
    
    public function demonstrateWorkloadOrchestration(): void
    {
        echo "\nWorkload Orchestration Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $edgeFramework = new EdgeComputingFramework();
        
        // Create different workloads
        $workloads = [
            new Workload('web_server', 'Web Server', 'web', [
                'cpu' => 2,
                'memory' => 4,
                'storage' => 10,
                'bandwidth' => 500
            ]),
            new Workload('analytics', 'Analytics', 'analytics', [
                'cpu' => 4,
                'memory' => 8,
                'storage' => 50,
                'bandwidth' => 200
            ]),
            new Workload('ml_training', 'ML Training', 'ml_training', [
                'cpu' => 8,
                'memory' => 16,
                'storage' => 100,
                'bandwidth' => 1000
            ])
        ];
        
        // Set deployment strategies
        $workloads[0]->setDeploymentStrategy('latency_optimized');
        $workloads[1]->setDeploymentStrategy('load_balanced');
        $workloads[2]->setDeploymentStrategy('capability_based');
        
        echo "Orchestrating workloads:\n";
        
        foreach ($workloads as $workload) {
            echo "\n{$workload->getName()}:\n";
            echo "  Type: {$workload->getType()}\n";
            echo "  Requirements: CPU={$workload->getRequirements()['cpu']}, ";
            echo "Memory={$workload->getRequirements()['memory']}GB\n";
            echo "  Strategy: {$workload->getDeploymentStrategy()}\n";
            
            try {
                $results = $edgeFramework->orchestrateWorkload($workload);
                
                echo "  Deployment results:\n";
                foreach ($results as $result) {
                    echo "    {$result['node_id']}: {$result['deployment_time']}s\n";
                }
            } catch (Exception $e) {
                echo "  Error: {$e->getMessage()}\n";
            }
        }
        
        // Show resource optimization
        echo "\nResource optimization:\n";
        $optimizations = $edgeFramework->getResourceManager()->optimizeResourceAllocation();
        
        foreach ($optimizations as $opt) {
            echo "  {$opt['type']}: {$opt['node_id']} - {$opt['reason']}\n";
        }
    }
    
    public function demonstrateSecurityAndMonitoring(): void
    {
        echo "\nSecurity and Monitoring Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        $edgeFramework = new EdgeComputingFramework();
        
        // Test security
        echo "Testing security features:\n";
        
        $securityManager = $edgeFramework->getSecurityManager();
        
        // Authentication
        echo "  Authentication:\n";
        $authResult = $securityManager->authenticate(['username' => 'admin', 'password' => 'secret']);
        echo "    Admin login: " . ($authResult ? 'Success' : 'Failed') . "\n";
        
        $authResult = $securityManager->authenticate(['username' => 'user', 'password' => 'wrong']);
        echo "    Invalid login: " . ($authResult ? 'Success' : 'Failed') . "\n";
        
        // Token generation
        echo "  Token generation:\n";
        $token = $securityManager->generateToken('user_123');
        echo "    Generated token: " . substr($token, 0, 20) . "...\n";
        
        // Authorization
        echo "  Authorization:\n";
        $authzResult = $securityManager->authorize($token, ['read', 'write']);
        echo "    Token authorization: " . ($authzResult ? 'Success' : 'Failed') . "\n";
        
        // Data processing with security
        echo "  Data processing with security:\n";
        $testData = ['type' => 'sensor_data', 'values' => [1, 2, 3, 4, 5]];
        $securityManager->processData($testData);
        echo "    Data encrypted and logged\n";
        
        // Show security metrics
        echo "\nSecurity metrics:\n";
        $securityMetrics = $securityManager->getSecurityMetrics();
        foreach ($securityMetrics as $key => $value) {
            echo "  $key: $value\n";
        }
        
        // Test monitoring
        echo "\nTesting monitoring features:\n";
        
        $monitoring = $edgeFramework->getMonitoring();
        
        // Deploy an application to monitor
        $app = new EdgeApplication('test_app', 'Test App', 'data_processing');
        $edgeFramework->deployApplication($app);
        $app->start();
        
        // Collect metrics
        echo "  Collecting metrics:\n";
        $monitoring->collectMetrics();
        
        // Show system health
        echo "\nSystem health:\n";
        $health = $monitoring->getSystemHealth();
        foreach ($health as $key => $value) {
            if (is_float($value)) {
                echo "  $key: " . round($value, 2) . "\n";
            } else {
                echo "  $key: $value\n";
            }
        }
        
        // Show alerts
        echo "\nAlerts:\n";
        $alerts = $monitoring->getAlerts();
        
        if (empty($alerts)) {
            echo "  No alerts\n";
        } else {
            foreach (array_slice($alerts, -3) as $alert) {
                echo "  [{$alert['severity']}] {$alert['message']}\n";
            }
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nEdge Computing Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Edge Node Management:\n";
        echo "   • Use hierarchical edge topology\n";
        echo "   • Implement proper node discovery\n";
        echo "   • Monitor node health continuously\n";
        echo "   • Use auto-scaling for resources\n";
        echo "   • Implement graceful degradation\n\n";
        
        echo "2. Application Deployment:\n";
        echo "   • Use capability-based deployment\n";
        echo "   • Implement rolling updates\n";
        echo "   • Use canary deployments\n";
        echo "   • Monitor application performance\n";
        echo "   • Implement automatic failover\n\n";
        
        echo "3. Data Processing:\n";
        echo "   • Choose appropriate processing pipelines\n";
        echo "   • Implement data filtering at edge\n";
        echo "   • Use streaming for real-time data\n";
        echo "   • Implement data compression\n";
        echo "   • Cache frequently accessed data\n\n";
        
        echo "4. Security:\n";
        echo "   • Implement end-to-end encryption\n";
        echo "   • Use secure communication protocols\n";
        echo "   • Implement proper authentication\n";
        echo "   • Monitor security events\n";
        echo "   • Use secure boot mechanisms\n\n";
        
        echo "5. Resource Management:\n";
        echo "   • Monitor resource utilization\n";
        echo "   • Implement resource quotas\n";
        echo "   • Use load balancing strategies\n";
        echo "   • Implement resource optimization\n";
        echo "   • Plan for resource scaling";
    }
    
    public function runAllExamples(): void
    {
        echo "Edge Computing Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateBasicEdgeSetup();
        $this->demonstrateApplicationDeployment();
        $this->demonstrateDataProcessing();
        $this->demonstrateWorkloadOrchestration();
        $this->demonstrateSecurityAndMonitoring();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runEdgeComputingDemo(): void
{
    $examples = new EdgeComputingExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runEdgeComputingDemo();
}
?>
