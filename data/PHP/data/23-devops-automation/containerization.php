<?php
/**
 * Containerization with Docker
 * 
 * Docker container management and orchestration for PHP applications.
 */

// Dockerfile Generator
class DockerfileGenerator
{
    private array $config;
    private array $layers;
    private string $phpVersion;
    private string $baseImage;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'php_version' => '8.2',
            'base_image' => 'alpine',
            'web_server' => 'nginx',
            'extensions' => ['pdo', 'pdo_mysql', 'mbstring', 'tokenizer', 'xml', 'curl', 'json'],
            'composer_install' => true,
            'node_build' => false,
            'xdebug' => false,
            'production' => false
        ], $config);
        
        $this->phpVersion = $this->config['php_version'];
        $this->baseImage = $this->config['base_image'];
        $this->initializeLayers();
    }
    
    /**
     * Initialize Docker layers
     */
    private function initializeLayers(): void
    {
        $this->layers = [
            'base' => [
                'FROM' => "php:{$this->phpVersion}-{$this->baseImage}",
                'LABEL' => [
                    'maintainer' => 'PHP Development Team',
                    'version' => '1.0.0',
                    'description' => 'PHP Application Container'
                ]
            ],
            'system' => [
                'RUN' => [
                    'apk add --no-cache',
                    '  nginx',
                    '  supervisor',
                    '  curl',
                    '  bash',
                    '  git'
                ]
            ],
            'php_extensions' => [
                'RUN' => [
                    'docker-php-ext-install ' . implode(' ', $this->config['extensions'])
                ]
            ],
            'composer' => [
                'COPY' => 'composer.json composer.lock /app/',
                'RUN' => [
                    'cd /app',
                    'composer install --no-dev --optimize-autoloader'
                ]
            ],
            'application' => [
                'COPY' => '. /app/',
                'RUN' => [
                    'chown -R www-data:www-data /app',
                    'chmod -R 755 /app/storage'
                ]
            ],
            'nginx_config' => [
                'COPY' => 'docker/nginx/default.conf /etc/nginx/conf.d/default.conf'
            ],
            'supervisor' => [
                'COPY' => 'docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf'
            ],
            'expose' => [
                'EXPOSE' => [80, 443]
            ],
            'cmd' => [
                'CMD' => ['supervisord', '-c', '/etc/supervisor/conf.d/supervisord.conf']
            ]
        ];
    }
    
    /**
     * Generate Dockerfile
     */
    public function generate(): string
    {
        $dockerfile = "# Generated Dockerfile for PHP Application\n";
        $dockerfile .= "# PHP Version: {$this->phpVersion}\n";
        $dockerfile .= "# Base Image: {$this->baseImage}\n\n";
        
        foreach ($this->layers as $layerName => $instructions) {
            foreach ($instructions as $instruction => $content) {
                $dockerfile .= $this->formatInstruction($instruction, $content);
            }
            $dockerfile .= "\n";
        }
        
        return $dockerfile;
    }
    
    /**
     * Format Docker instruction
     */
    private function formatInstruction(string $instruction, mixed $content): string
    {
        switch ($instruction) {
            case 'FROM':
                return "FROM $content\n";
            case 'LABEL':
                $lines = ["LABEL"];
                foreach ($content as $key => $value) {
                    $lines[] = "    \"$key\"=\"$value\"";
                }
                return implode(" \\\n", $lines) . "\n";
            case 'RUN':
                if (is_array($content)) {
                    $lines = ["RUN"];
                    foreach ($content as $command) {
                        $lines[] = "    $command";
                    }
                    return implode(" \\\n", $lines) . "\n";
                }
                return "RUN $content\n";
            case 'COPY':
                if (is_array($content)) {
                    return "COPY " . implode(' ', $content) . "\n";
                }
                return "COPY $content\n";
            case 'ADD':
                return "ADD $content\n";
            case 'WORKDIR':
                return "WORKDIR $content\n";
            case 'EXPOSE':
                if (is_array($content)) {
                    return "EXPOSE " . implode(' ', $content) . "\n";
                }
                return "EXPOSE $content\n";
            case 'ENV':
                $lines = ["ENV"];
                foreach ($content as $key => $value) {
                    $lines[] = "    $key=$value";
                }
                return implode(" \\\n", $lines) . "\n";
            case 'CMD':
                if (is_array($content)) {
                    return "CMD [" . implode(', ', array_map(fn($c) => "\"$c\"", $content)) . "]\n";
                }
                return "CMD $content\n";
            case 'ENTRYPOINT':
                if (is_array($content)) {
                    return "ENTRYPOINT [" . implode(', ', array_map(fn($c) => "\"$c\"", $content)) . "]\n";
                }
                return "ENTRYPOINT $content\n";
            case 'VOLUME':
                if (is_array($content)) {
                    return "VOLUME [" . implode(', ', array_map(fn($c) => "\"$c\"", $content)) . "]\n";
                }
                return "VOLUME $content\n";
            case 'USER':
                return "USER $content\n";
            case 'ARG':
                return "ARG $content\n";
            default:
                return "$instruction $content\n";
        }
    }
    
    /**
     * Add custom layer
     */
    public function addLayer(string $name, array $instructions): void
    {
        $this->layers[$name] = $instructions;
    }
    
    /**
     * Generate multi-stage Dockerfile
     */
    public function generateMultiStage(): string
    {
        $dockerfile = "# Multi-stage Dockerfile for PHP Application\n\n";
        
        // Build stage
        $dockerfile .= "# Build stage\n";
        $dockerfile .= "FROM node:18-alpine AS builder\n";
        $dockerfile .= "WORKDIR /app\n";
        $dockerfile .= "COPY package*.json ./\n";
        $dockerfile .= "RUN npm ci --only=production\n";
        $dockerfile .= "COPY . .\n";
        $dockerfile .= "RUN npm run build\n\n";
        
        // Runtime stage
        $dockerfile .= "# Runtime stage\n";
        $dockerfile .= "FROM php:{$this->phpVersion}-{$this->baseImage}\n\n";
        
        // Copy from build stage
        $dockerfile .= "COPY --from=builder /app/public/build /app/public/build\n";
        
        // Add remaining layers
        foreach ($this->layers as $layerName => $instructions) {
            if ($layerName !== 'base') {
                foreach ($instructions as $instruction => $content) {
                    $dockerfile .= $this->formatInstruction($instruction, $content);
                }
                $dockerfile .= "\n";
            }
        }
        
        return $dockerfile;
    }
    
    /**
     * Generate Docker Compose file
     */
    public function generateDockerCompose(array $services = []): string
    {
        $compose = [
            'version' => '3.8',
            'services' => array_merge([
                'app' => [
                    'build' => [
                        'context' => '.',
                        'dockerfile' => 'Dockerfile'
                    ],
                    'ports' => ['80:80', '443:443'],
                    'volumes' => [
                        './storage:/app/storage',
                        './logs:/app/logs'
                    ],
                    'environment' => [
                        'APP_ENV=production',
                        'APP_DEBUG=false'
                    ],
                    'depends_on' => ['mysql', 'redis']
                ],
                'mysql' => [
                    'image' => 'mysql:8.0',
                    'environment' => [
                        'MYSQL_ROOT_PASSWORD=root',
                        'MYSQL_DATABASE=app',
                        'MYSQL_USER=app',
                        'MYSQL_PASSWORD=secret'
                    ],
                    'volumes' => [
                        'mysql_data:/var/lib/mysql'
                    ],
                    'ports' => ['3306:3306']
                ],
                'redis' => [
                    'image' => 'redis:6-alpine',
                    'ports' => ['6379:6379']
                ],
                'nginx' => [
                    'image' => 'nginx:alpine',
                    'ports' => ['8080:80'],
                    'volumes' => [
                        './docker/nginx/nginx.conf:/etc/nginx/nginx.conf'
                    ],
                    'depends_on' => ['app']
                ]
            ], $services),
            'volumes' => [
                'mysql_data' => [
                    'driver' => 'local'
                ]
            ],
            'networks' => [
                'app-network' => [
                    'driver' => 'bridge'
                ]
            ]
        ];
        
        return $this->arrayToYaml($compose);
    }
    
    /**
     * Convert array to YAML
     */
    private function arrayToYaml(array $array, int $level = 0): string
    {
        $yaml = '';
        $indent = str_repeat('  ', $level);
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $yaml .= "$indent$key:\n";
                $yaml .= $this->arrayToYaml($value, $level + 1);
            } else {
                $yaml .= "$indent$key: $value\n";
            }
        }
        
        return $yaml;
    }
}

// Container Manager
class ContainerManager
{
    private array $containers = [];
    private array $images = [];
    private array $networks = [];
    private array $volumes = [];
    
    public function __construct()
    {
        $this->initializeContainers();
    }
    
    /**
     * Initialize containers
     */
    private function initializeContainers(): void
    {
        $this->containers = [
            'php-app' => [
                'name' => 'php-app',
                'image' => 'php-app:latest',
                'status' => 'stopped',
                'ports' => ['80:80'],
                'volumes' => ['./src:/app/src', './logs:/app/logs'],
                'environment' => [
                    'APP_ENV' => 'development',
                    'APP_DEBUG' => 'true'
                ],
                'created_at' => time()
            ],
            'mysql-db' => [
                'name' => 'mysql-db',
                'image' => 'mysql:8.0',
                'status' => 'stopped',
                'ports' => ['3306:3306'],
                'volumes' => ['mysql_data:/var/lib/mysql'],
                'environment' => [
                    'MYSQL_ROOT_PASSWORD' => 'root',
                    'MYSQL_DATABASE' => 'app'
                ],
                'created_at' => time()
            ],
            'redis-cache' => [
                'name' => 'redis-cache',
                'image' => 'redis:6-alpine',
                'status' => 'stopped',
                'ports' => ['6379:6379'],
                'created_at' => time()
            ]
        ];
    }
    
    /**
     * Build image
     */
    public function buildImage(string $imageName, string $dockerfilePath = '.'): array
    {
        echo "Building image: $imageName from $dockerfilePath\n";
        
        // Simulate build process
        $buildSteps = [
            'Sending build context to Docker daemon',
            'Step 1/10 : FROM php:8.2-alpine',
            'Step 2/10 : LABEL maintainer="PHP Development Team"',
            'Step 3/10 : RUN apk add --no-cache nginx',
            'Step 4/10 : RUN docker-php-ext-install pdo pdo_mysql',
            'Step 5/10 : COPY composer.json composer.lock /app/',
            'Step 6/10 : RUN composer install --no-dev',
            'Step 7/10 : COPY . /app/',
            'Step 8/10 : EXPOSE 80',
            'Step 9/10 : CMD ["supervisord"]',
            'Step 10/10 : Successfully built ' . uniqid(),
            'Successfully tagged ' . $imageName . ':latest'
        ];
        
        foreach ($buildSteps as $step) {
            echo "  $step\n";
            usleep(100000); // Simulate build time
        }
        
        $this->images[$imageName] = [
            'name' => $imageName,
            'tag' => 'latest',
            'size' => rand(100, 500) . 'MB',
            'created_at' => time(),
            'build_time' => rand(30, 120)
        ];
        
        return $this->images[$imageName];
    }
    
    /**
     * Run container
     */
    public function runContainer(string $containerName, array $options = []): string
    {
        if (!isset($this->containers[$containerName])) {
            throw new Exception("Container '$containerName' not found");
        }
        
        $container = &$this->containers[$containerName];
        
        if ($container['status'] === 'running') {
            throw new Exception("Container '$containerName' is already running");
        }
        
        echo "Starting container: $containerName\n";
        
        // Simulate container startup
        $containerId = uniqid('container_');
        $container['container_id'] = $containerId;
        $container['status'] = 'running';
        $container['started_at'] = time();
        
        // Apply options
        if (isset($options['ports'])) {
            $container['ports'] = array_merge($container['ports'], $options['ports']);
        }
        
        if (isset($options['environment'])) {
            $container['environment'] = array_merge($container['environment'], $options['environment']);
        }
        
        if (isset($options['volumes'])) {
            $container['volumes'] = array_merge($container['volumes'], $options['volumes']);
        }
        
        echo "Container started with ID: $containerId\n";
        
        return $containerId;
    }
    
    /**
     * Stop container
     */
    public function stopContainer(string $containerName): bool
    {
        if (!isset($this->containers[$containerName])) {
            return false;
        }
        
        $container = &$this->containers[$containerName];
        
        if ($container['status'] !== 'running') {
            return false;
        }
        
        echo "Stopping container: $containerName\n";
        
        $container['status'] = 'stopped';
        $container['stopped_at'] = time();
        unset($container['container_id']);
        
        return true;
    }
    
    /**
     * Remove container
     */
    public function removeContainer(string $containerName): bool
    {
        if (!isset($this->containers[$containerName])) {
            return false;
        }
        
        $container = $this->containers[$containerName];
        
        if ($container['status'] === 'running') {
            $this->stopContainer($containerName);
        }
        
        echo "Removing container: $containerName\n";
        unset($this->containers[$containerName]);
        
        return true;
    }
    
    /**
     * Get container logs
     */
    public function getContainerLogs(string $containerName, int $lines = 50): array
    {
        if (!isset($this->containers[$containerName])) {
            return [];
        }
        
        $container = $this->containers[$containerName];
        
        // Simulate log generation
        $logs = [];
        $currentTime = time();
        
        for ($i = 0; $i < $lines; $i++) {
            $timestamp = date('Y-m-d H:i:s', $currentTime - ($lines - $i));
            $logTypes = ['INFO', 'WARNING', 'ERROR', 'DEBUG'];
            $type = $logTypes[array_rand($logTypes)];
            
            $logs[] = [
                'timestamp' => $timestamp,
                'type' => $type,
                'message' => "Sample log message $i from {$container['name']}"
            ];
        }
        
        return $logs;
    }
    
    /**
     * Get container stats
     */
    public function getContainerStats(string $containerName): array
    {
        if (!isset($this->containers[$containerName])) {
            return [];
        }
        
        $container = $this->containers[$containerName];
        
        // Simulate container stats
        return [
            'container_id' => $container['container_id'] ?? 'N/A',
            'name' => $container['name'],
            'status' => $container['status'],
            'cpu_usage' => rand(0, 100) . '%',
            'memory_usage' => rand(50, 512) . 'MB',
            'network_io' => rand(0, 1000) . 'B/s',
            'disk_io' => rand(0, 500) . 'B/s',
            'uptime' => $container['started_at'] ? (time() - $container['started_at']) . 's' : 'N/A'
        ];
    }
    
    /**
     * List containers
     */
    public function listContainers(bool $all = false): array
    {
        $containers = [];
        
        foreach ($this->containers as $name => $container) {
            if (!$all && $container['status'] === 'stopped') {
                continue;
            }
            
            $containers[] = [
                'name' => $name,
                'image' => $container['image'],
                'status' => $container['status'],
                'ports' => implode(', ', $container['ports']),
                'created' => date('Y-m-d H:i:s', $container['created_at'])
            ];
        }
        
        return $containers;
    }
    
    /**
     * List images
     */
    public function listImages(): array
    {
        $images = [];
        
        foreach ($this->images as $name => $image) {
            $images[] = [
                'name' => $name,
                'tag' => $image['tag'],
                'size' => $image['size'],
                'created' => date('Y-m-d H:i:s', $image['created_at'])
            ];
        }
        
        return $images;
    }
    
    /**
     * Create network
     */
    public function createNetwork(string $networkName, string $driver = 'bridge'): string
    {
        $networkId = uniqid('network_');
        
        $this->networks[$networkName] = [
            'id' => $networkId,
            'name' => $networkName,
            'driver' => $driver,
            'created_at' => time()
        ];
        
        echo "Network created: $networkName (ID: $networkId)\n";
        
        return $networkId;
    }
    
    /**
     * Create volume
     */
    public function createVolume(string $volumeName): string
    {
        $volumeId = uniqid('volume_');
        
        $this->volumes[$volumeName] = [
            'id' => $volumeId,
            'name' => $volumeName,
            'created_at' => time()
        ];
        
        echo "Volume created: $volumeName (ID: $volumeId)\n";
        
        return $volumeId;
    }
}

// Docker Compose Manager
class DockerComposeManager
{
    private array $services = [];
    private array $networks = [];
    private array $volumes = [];
    private string $composeFile;
    
    public function __construct(string $composeFile = 'docker-compose.yml')
    {
        $this->composeFile = $composeFile;
        $this->initializeServices();
    }
    
    /**
     * Initialize services
     */
    private function initializeServices(): void
    {
        $this->services = [
            'web' => [
                'build' => [
                    'context' => '.',
                    'dockerfile' => 'Dockerfile'
                ],
                'ports' => ['80:80'],
                'volumes' => [
                    './src:/app/src',
                    './logs:/app/logs'
                ],
                'environment' => [
                    'APP_ENV' => 'development',
                    'APP_DEBUG' => 'true'
                ],
                'depends_on' => ['db', 'redis'],
                'restart' => 'unless-stopped'
            ],
            'db' => [
                'image' => 'mysql:8.0',
                'environment' => [
                    'MYSQL_ROOT_PASSWORD' => 'root',
                    'MYSQL_DATABASE' => 'app',
                    'MYSQL_USER' => 'app',
                    'MYSQL_PASSWORD' => 'secret'
                ],
                'volumes' => [
                    'mysql_data:/var/lib/mysql'
                ],
                'ports' => ['3306:3306'],
                'restart' => 'unless-stopped'
            ],
            'redis' => [
                'image' => 'redis:6-alpine',
                'ports' => ['6379:6379'],
                'restart' => 'unless-stopped'
            ],
            'nginx' => [
                'image' => 'nginx:alpine',
                'ports' => ['8080:80'],
                'volumes' => [
                    './docker/nginx/nginx.conf:/etc/nginx/nginx.conf'
                ],
                'depends_on' => ['web'],
                'restart' => 'unless-stopped'
            ]
        ];
        
        $this->networks = [
            'app-network' => [
                'driver' => 'bridge'
            ]
        ];
        
        $this->volumes = [
            'mysql_data' => [
                'driver' => 'local'
            ]
        ];
    }
    
    /**
     * Add service
     */
    public function addService(string $name, array $config): void
    {
        $this->services[$name] = $config;
    }
    
    /**
     * Remove service
     */
    public function removeService(string $name): void
    {
        unset($this->services[$name]);
    }
    
    /**
     * Up services
     */
    public function up(array $services = []): array
    {
        echo "Starting Docker Compose services...\n";
        
        $results = [];
        
        if (empty($services)) {
            $services = array_keys($this->services);
        }
        
        foreach ($services as $serviceName) {
            if (!isset($this->services[$serviceName])) {
                echo "Service '$serviceName' not found\n";
                continue;
            }
            
            echo "Starting service: $serviceName\n";
            
            // Simulate service startup
            $results[$serviceName] = [
                'service' => $serviceName,
                'status' => 'running',
                'container_id' => uniqid('container_'),
                'started_at' => time()
            ];
            
            // Handle dependencies
            $dependsOn = $this->services[$serviceName]['depends_on'] ?? [];
            foreach ($dependsOn as $dependency) {
                if (!isset($results[$dependency])) {
                    echo "  Waiting for dependency: $dependency\n";
                }
            }
        }
        
        echo "All services started successfully!\n";
        
        return $results;
    }
    
    /**
     * Down services
     */
    public function down(array $services = []): array
    {
        echo "Stopping Docker Compose services...\n";
        
        $results = [];
        
        if (empty($services)) {
            $services = array_keys($this->services);
        }
        
        foreach ($services as $serviceName) {
            echo "Stopping service: $serviceName\n";
            
            $results[$serviceName] = [
                'service' => $serviceName,
                'status' => 'stopped',
                'stopped_at' => time()
            ];
        }
        
        echo "All services stopped!\n";
        
        return $results;
    }
    
    /**
     * Generate compose file
     */
    public function generateComposeFile(): string
    {
        $compose = [
            'version' => '3.8',
            'services' => $this->services,
            'networks' => $this->networks,
            'volumes' => $this->volumes
        ];
        
        return $this->arrayToYaml($compose);
    }
    
    /**
     * Save compose file
     */
    public function saveComposeFile(): void
    {
        $content = $this->generateComposeFile();
        file_put_contents($this->composeFile, $content);
        echo "Docker Compose file saved to: {$this->composeFile}\n";
    }
    
    /**
     * Get service status
     */
    public function getStatus(): array
    {
        $status = [];
        
        foreach ($this->services as $name => $config) {
            $status[$name] = [
                'service' => $name,
                'state' => 'running', // Simulated
                'health' => 'healthy',   // Simulated
                'ports' => $config['ports'] ?? []
            ];
        }
        
        return $status;
    }
    
    /**
     * Convert array to YAML
     */
    private function arrayToYaml(array $array, int $level = 0): string
    {
        $yaml = '';
        $indent = str_repeat('  ', $level);
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $yaml .= "$indent$key:\n";
                $yaml .= $this->arrayToYaml($value, $level + 1);
            } else {
                $yaml .= "$indent$key: $value\n";
            }
        }
        
        return $yaml;
    }
}

// Containerization Examples
class ContainerizationExamples
{
    private DockerfileGenerator $dockerfileGen;
    private ContainerManager $containerManager;
    private DockerComposeManager $composeManager;
    
    public function __construct()
    {
        $this->dockerfileGen = new DockerfileGenerator();
        $this->containerManager = new ContainerManager();
        $this->composeManager = new DockerComposeManager();
    }
    
    public function demonstrateDockerfile(): void
    {
        echo "Dockerfile Generation Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Generate basic Dockerfile
        echo "Basic Dockerfile:\n";
        $dockerfile = $this->dockerfileGen->generate();
        echo substr($dockerfile, 0, 1000) . "...\n\n";
        
        // Generate multi-stage Dockerfile
        echo "Multi-stage Dockerfile:\n";
        $multiStageDockerfile = $this->dockerfileGen->generateMultiStage();
        echo substr($multiStageDockerfile, 0, 1000) . "...\n\n";
        
        // Generate Docker Compose
        echo "Docker Compose Configuration:\n";
        $dockerCompose = $this->dockerfileGen->generateDockerCompose();
        echo substr($dockerCompose, 0, 1000) . "...\n";
    }
    
    public function demonstrateContainerManagement(): void
    {
        echo "\nContainer Management Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Build image
        echo "Building Docker image...\n";
        $image = $this->containerManager->buildImage('php-app:latest');
        echo "Image built: {$image['name']}:{$image['tag']} ({$image['size']})\n\n";
        
        // Run container
        echo "Running container...\n";
        $containerId = $this->containerManager->runContainer('php-app', [
            'ports' => ['8080:80'],
            'environment' => ['APP_ENV=development']
        ]);
        echo "Container started: $containerId\n\n";
        
        // List containers
        echo "Container List:\n";
        $containers = $this->containerManager->listContainers();
        foreach ($containers as $container) {
            echo "  {$container['name']}: {$container['status']} ({$container['ports']})\n";
        }
        echo "\n";
        
        // Get container stats
        echo "Container Stats:\n";
        $stats = $this->containerManager->getContainerStats('php-app');
        foreach ($stats as $key => $value) {
            echo "  $key: $value\n";
        }
        echo "\n";
        
        // Get container logs
        echo "Container Logs (last 5 lines):\n";
        $logs = $this->containerManager->getContainerLogs('php-app', 5);
        foreach (array_slice($logs, -5) as $log) {
            echo "  [{$log['timestamp']}] {$log['type']}: {$log['message']}\n";
        }
        echo "\n";
        
        // Stop container
        echo "Stopping container...\n";
        $this->containerManager->stopContainer('php-app');
        echo "Container stopped.\n";
    }
    
    public function demonstrateDockerCompose(): void
    {
        echo "\nDocker Compose Demo\n";
        echo str_repeat("-", 22) . "\n";
        
        // Show services
        echo "Docker Compose Services:\n";
        $services = $this->composeManager->getStatus();
        foreach ($services as $service) {
            echo "  {$service['service']}: {$service['state']} ({$service['health']})\n";
            if (!empty($service['ports'])) {
                echo "    Ports: " . implode(', ', $service['ports']) . "\n";
            }
        }
        echo "\n";
        
        // Generate and save compose file
        echo "Generating Docker Compose file...\n";
        $this->composeManager->saveComposeFile();
        
        // Up services
        echo "\nStarting services with Docker Compose...\n";
        $upResults = $this->composeManager->up();
        foreach ($upResults as $service) {
            echo "  {$service['service']}: {$service['status']} (Container: {$service['container_id']})\n";
        }
        
        // Show status
        echo "\nService Status:\n";
        $status = $this->composeManager->getStatus();
        foreach ($status as $service) {
            echo "  {$service['service']}: {$service['state']} ({$service['health']})\n";
        }
        
        // Down services
        echo "\nStopping services...\n";
        $downResults = $this->composeManager->down();
        foreach ($downResults as $service) {
            echo "  {$service['service']}: {$service['status']}\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nContainerization Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Dockerfile Optimization:\n";
        echo "   • Use multi-stage builds\n";
        echo "   • Minimize layer count\n";
        echo "   • Use .dockerignore\n";
        echo "   • Leverage layer caching\n";
        echo "   • Use specific base images\n\n";
        
        echo "2. Image Security:\n";
        echo "   • Use official base images\n";
        echo "   • Scan for vulnerabilities\n";
        echo "   • Use non-root users\n";
        echo "   • Remove unnecessary packages\n";
        echo "   • Keep images updated\n\n";
        
        echo "3. Container Management:\n";
        echo "   • Use resource limits\n";
        echo "   • Implement health checks\n";
        echo "   • Use proper logging\n";
        echo "   • Monitor container metrics\n";
        echo "   • Implement graceful shutdown\n\n";
        
        echo "4. Docker Compose:\n";
        echo "   • Use environment files\n";
        echo "   • Implement proper networking\n";
        echo "   • Use named volumes\n";
        echo "   • Set restart policies\n";
        echo "   • Use service dependencies\n\n";
        
        echo "5. Production Deployment:\n";
        echo "   • Use container orchestration\n";
        echo "   • Implement secrets management\n";
        echo "   • Use load balancing\n";
        echo "   • Implement monitoring\n";
        echo "   • Use rolling updates";
    }
    
    public function runAllExamples(): void
    {
        echo "Containerization with Docker Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateDockerfile();
        $this->demonstrateContainerManagement();
        $this->demonstrateDockerCompose();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runContainerizationDemo(): void
{
    $examples = new ContainerizationExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runContainerizationDemo();
}
?>
