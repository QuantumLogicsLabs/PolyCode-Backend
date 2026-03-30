<?php
/**
 * Advanced Microservices in PHP
 * 
 * Advanced microservices patterns, service mesh, and distributed systems architecture.
 */

// Advanced Microservices Architecture
class AdvancedMicroservicesArchitecture
{
    private array $services;
    private ServiceMesh $serviceMesh;
    private CircuitBreakerRegistry $circuitBreakerRegistry;
    private RateLimiterRegistry $rateLimiterRegistry;
    private DistributedTracing $tracing;
    private MetricsCollector $metrics;
    private ConfigManager $config;
    
    public function __construct()
    {
        $this->services = [];
        $this->serviceMesh = new ServiceMesh();
        $this->circuitBreakerRegistry = new CircuitBreakerRegistry();
        $this->rateLimiterRegistry = new RateLimiterRegistry();
        $this->tracing = new DistributedTracing();
        $this->metrics = new MetricsCollector();
        $this->config = new ConfigManager();
        
        $this->initializeServices();
    }
    
    private function initializeServices(): void
    {
        // Define advanced microservices
        $serviceDefinitions = [
            'api-gateway' => [
                'class' => APIGateway::class,
                'instances' => 3,
                'load_balancer' => 'round_robin',
                'health_check' => '/health',
                'dependencies' => ['user-service', 'product-service', 'order-service'],
                'circuit_breaker' => true,
                'rate_limit' => 1000
            ],
            'user-service' => [
                'class' => UserService::class,
                'instances' => 5,
                'load_balancer' => 'least_connections',
                'health_check' => '/health',
                'dependencies' => ['database', 'cache-service'],
                'circuit_breaker' => true,
                'rate_limit' => 500
            ],
            'product-service' => [
                'class' => ProductService::class,
                'instances' => 4,
                'load_balancer' => 'weighted_round_robin',
                'health_check' => '/health',
                'dependencies' => ['database', 'cache-service', 'inventory-service'],
                'circuit_breaker' => true,
                'rate_limit' => 300
            ],
            'order-service' => [
                'class' => OrderService::class,
                'instances' => 6,
                'load_balancer' => 'consistent_hash',
                'health_check' => '/health',
                'dependencies' => ['user-service', 'product-service', 'payment-service', 'notification-service'],
                'circuit_breaker' => true,
                'rate_limit' => 200
            ],
            'payment-service' => [
                'class' => PaymentService::class,
                'instances' => 8,
                'load_balancer' => 'sticky_session',
                'health_check' => '/health',
                'dependencies' => ['payment-gateway', 'audit-service'],
                'circuit_breaker' => true,
                'rate_limit' => 100
            ],
            'notification-service' => [
                'class' => NotificationService::class,
                'instances' => 3,
                'load_balancer' => 'random',
                'health_check' => '/health',
                'dependencies' => ['email-service', 'sms-service', 'push-service'],
                'circuit_breaker' => false,
                'rate_limit' => 1000
            ],
            'cache-service' => [
                'class' => CacheService::class,
                'instances' => 6,
                'load_balancer' => 'consistent_hash',
                'health_check' => '/health',
                'dependencies' => ['redis-cluster'],
                'circuit_breaker' => true,
                'rate_limit' => 10000
            ],
            'database' => [
                'class' => DatabaseService::class,
                'instances' => 1,
                'load_balancer' => 'primary_backup',
                'health_check' => '/health',
                'dependencies' => [],
                'circuit_breaker' => true,
                'rate_limit' => 5000
            ]
        ];
        
        foreach ($serviceDefinitions as $serviceName => $definition) {
            $this->createService($serviceName, $definition);
        }
    }
    
    private function createService(string $serviceName, array $definition): void
    {
        $serviceClass = $definition['class'];
        $instances = [];
        
        for ($i = 0; $i < $definition['instances']; $i++) {
            $instanceId = "{$serviceName}_{$i}";
            $instance = new $serviceClass($instanceId, $definition);
            
            // Setup circuit breaker
            if ($definition['circuit_breaker']) {
                $circuitBreaker = new CircuitBreaker($instanceId, 5, 60, 30000);
                $this->circuitBreakerRegistry->register($instanceId, $circuitBreaker);
            }
            
            // Setup rate limiter
            if ($definition['rate_limit']) {
                $rateLimiter = new RateLimiter($instanceId, $definition['rate_limit'], 60);
                $this->rateLimiterRegistry->register($instanceId, $rateLimiter);
            }
            
            $instances[] = $instance;
        }
        
        $service = new Microservice($serviceName, $instances, $definition);
        $this->services[$serviceName] = $service;
        
        // Register with service mesh
        $this->serviceMesh->registerService($service);
        
        echo "Created advanced service: $serviceName ({$definition['instances']} instances)\n";
    }
    
    public function processRequest(string $serviceName, array $request): array
    {
        $span = $this->tracing->startSpan("request_to_$serviceName");
        
        try {
            $service = $this->services[$serviceName] ?? null;
            
            if (!$service) {
                throw new Exception("Service not found: $serviceName");
            }
            
            // Check rate limiting
            $instanceId = $service->getInstanceForRequest($request);
            $rateLimiter = $this->rateLimiterRegistry->get($instanceId);
            
            if ($rateLimiter && !$rateLimiter->allow()) {
                throw new Exception("Rate limit exceeded for $instanceId");
            }
            
            // Check circuit breaker
            $circuitBreaker = $this->circuitBreakerRegistry->get($instanceId);
            
            if ($circuitBreaker && !$circuitBreaker->allow()) {
                throw new Exception("Circuit breaker open for $instanceId");
            }
            
            // Process request
            $startTime = microtime(true);
            $result = $service->handleRequest($request);
            $duration = (microtime(true) - $startTime) * 1000;
            
            // Record metrics
            $this->metrics->recordRequest($serviceName, $duration, true);
            
            // Update circuit breaker
            if ($circuitBreaker) {
                $circuitBreaker->recordSuccess();
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->metrics->recordRequest($serviceName, 0, false);
            
            // Update circuit breaker
            $circuitBreaker = $this->circuitBreakerRegistry->get($instanceId);
            if ($circuitBreaker) {
                $circuitBreaker->recordFailure();
            }
            
            throw $e;
            
        } finally {
            $this->tracing->finishSpan($span);
        }
    }
    
    public function getServiceMesh(): ServiceMesh
    {
        return $this->serviceMesh;
    }
    
    public function getCircuitBreakerRegistry(): CircuitBreakerRegistry
    {
        return $this->circuitBreakerRegistry;
    }
    
    public function getRateLimiterRegistry(): RateLimiterRegistry
    {
        return $this->rateLimiterRegistry;
    }
    
    public function getTracing(): DistributedTracing
    {
        return $this->tracing;
    }
    
    public function getMetrics(): MetricsCollector
    {
        return $this->metrics;
    }
    
    public function getConfig(): ConfigManager
    {
        return $this->config;
    }
    
    public function getServices(): array
    {
        return $this->services;
    }
    
    public function getService(string $name): ?Microservice
    {
        return $this->services[$name] ?? null;
    }
    
    public function getHealthStatus(): array
    {
        $status = [];
        
        foreach ($this->services as $serviceName => $service) {
            $status[$serviceName] = [
                'instances' => count($service->getInstances()),
                'healthy' => $service->isHealthy(),
                'load_balancer' => $service->getLoadBalancer()->getType(),
                'circuit_breakers' => $this->getServiceCircuitBreakerStatus($serviceName),
                'rate_limiters' => $this->getServiceRateLimiterStatus($serviceName)
            ];
        }
        
        return $status;
    }
    
    private function getServiceCircuitBreakerStatus(string $serviceName): array
    {
        $status = [];
        $service = $this->services[$serviceName];
        
        foreach ($service->getInstances() as $instance) {
            $circuitBreaker = $this->circuitBreakerRegistry->get($instance->getId());
            if ($circuitBreaker) {
                $status[$instance->getId()] = [
                    'state' => $circuitBreaker->getState(),
                    'failures' => $circuitBreaker->getFailureCount(),
                    'successes' => $circuitBreaker->getSuccessCount()
                ];
            }
        }
        
        return $status;
    }
    
    private function getServiceRateLimiterStatus(string $serviceName): array
    {
        $status = [];
        $service = $this->services[$serviceName];
        
        foreach ($service->getInstances() as $instance) {
            $rateLimiter = $this->rateLimiterRegistry->get($instance->getId());
            if ($rateLimiter) {
                $status[$instance->getId()] = [
                    'allowed' => $rateLimiter->getAllowedRequests(),
                    'total' => $rateLimiter->getTotalRequests(),
                    'window' => $rateLimiter->getWindowSeconds()
                ];
            }
        }
        
        return $status;
    }
}

// Service Mesh
class ServiceMesh
{
    private array $services;
    private array $routes;
    private array $policies;
    private TrafficManager $trafficManager;
    
    public function __construct()
    {
        $this->services = [];
        $this->routes = [];
        $this->policies = [];
        $this->trafficManager = new TrafficManager();
    }
    
    public function registerService(Microservice $service): void
    {
        $this->services[$service->getName()] = $service;
        $this->setupRoutes($service);
        $this->setupPolicies($service);
    }
    
    private function setupRoutes(Microservice $service): void
    {
        $serviceName = $service->getName();
        
        // Setup internal routes
        foreach ($service->getDependencies() as $dependency) {
            $this->routes["{$serviceName}_to_{$dependency}"] = new Route($serviceName, $dependency);
        }
        
        // Setup external routes
        $this->routes["external_to_{$serviceName}"] = new Route('external', $serviceName);
    }
    
    private function setupPolicies(Microservice $service): void
    {
        $serviceName = $service->getName();
        
        // Setup security policies
        $this->policies["{$serviceName}_security"] = new SecurityPolicy($serviceName);
        
        // Setup traffic policies
        $this->policies["{$serviceName}_traffic"] = new TrafficPolicy($serviceName);
        
        // Setup retry policies
        $this->policies["{$serviceName}_retry"] = new RetryPolicy($serviceName);
    }
    
    public function routeRequest(string $from, string $to, array $request): array
    {
        $routeKey = "{$from}_to_{$to}";
        
        if (!isset($this->routes[$routeKey])) {
            throw new Exception("No route found from $from to $to");
        }
        
        $route = $this->routes[$routeKey];
        
        // Apply policies
        $this->applyPolicies($route, $request);
        
        // Apply traffic management
        $this->trafficManager->manageTraffic($route, $request);
        
        return $request;
    }
    
    private function applyPolicies(Route $route, array &$request): void
    {
        $toService = $route->getTo();
        
        // Apply security policy
        if (isset($this->policies["{$toService}_security"])) {
            $this->policies["{$toService}_security"]->apply($request);
        }
        
        // Apply traffic policy
        if (isset($this->policies["{$toService}_traffic"])) {
            $this->policies["{$toService}_traffic"]->apply($request);
        }
        
        // Apply retry policy
        if (isset($this->policies["{$toService}_retry"])) {
            $this->policies["{$toService}_retry"]->apply($request);
        }
    }
    
    public function getRoutes(): array
    {
        return $this->routes;
    }
    
    public function getPolicies(): array
    {
        return $this->policies;
    }
    
    public function getTrafficManager(): TrafficManager
    {
        return $this->trafficManager;
    }
}

// Circuit Breaker Registry
class CircuitBreakerRegistry
{
    private array $circuitBreakers;
    
    public function __construct()
    {
        $this->circuitBreakers = [];
    }
    
    public function register(string $instanceId, CircuitBreaker $circuitBreaker): void
    {
        $this->circuitBreakers[$instanceId] = $circuitBreaker;
    }
    
    public function get(string $instanceId): ?CircuitBreaker
    {
        return $this->circuitBreakers[$instanceId] ?? null;
    }
    
    public function getAll(): array
    {
        return $this->circuitBreakers;
    }
    
    public function getStates(): array
    {
        $states = [];
        
        foreach ($this->circuitBreakers as $instanceId => $circuitBreaker) {
            $states[$instanceId] = $circuitBreaker->getState();
        }
        
        return $states;
    }
}

// Circuit Breaker
class CircuitBreaker
{
    private string $instanceId;
    private int $failureThreshold;
    private int $recoveryTimeout;
    private int $timeout;
    private int $failureCount;
    private int $successCount;
    private string $state;
    private float $lastFailureTime;
    private float $lastSuccessTime;
    
    public function __construct(string $instanceId, int $failureThreshold = 5, int $recoveryTimeout = 60, int $timeout = 30)
    {
        $this->instanceId = $instanceId;
        $this->failureThreshold = $failureThreshold;
        $this->recoveryTimeout = $recoveryTimeout;
        $this->timeout = $timeout;
        $this->failureCount = 0;
        $this->successCount = 0;
        $this->state = 'closed';
        $this->lastFailureTime = 0;
        $this->lastSuccessTime = 0;
    }
    
    public function allow(): bool
    {
        $currentTime = microtime(true);
        
        switch ($this->state) {
            case 'closed':
                return true;
                
            case 'open':
                if ($currentTime - $this->lastFailureTime >= $this->recoveryTimeout) {
                    $this->state = 'half_open';
                    echo "Circuit breaker for {$this->instanceId} transitioning to half-open\n";
                    return true;
                }
                return false;
                
            case 'half_open':
                return true;
                
            default:
                return false;
        }
    }
    
    public function recordSuccess(): void
    {
        $this->successCount++;
        $this->lastSuccessTime = microtime(true);
        
        if ($this->state === 'half_open') {
            $this->state = 'closed';
            $this->failureCount = 0;
            echo "Circuit breaker for {$this->instanceId} closed after success\n";
        }
    }
    
    public function recordFailure(): void
    {
        $this->failureCount++;
        $this->lastFailureTime = microtime(true);
        
        if ($this->state === 'closed' && $this->failureCount >= $this->failureThreshold) {
            $this->state = 'open';
            echo "Circuit breaker for {$this->instanceId} opened after {$this->failureCount} failures\n";
        } elseif ($this->state === 'half_open') {
            $this->state = 'open';
            echo "Circuit breaker for {$this->instanceId} opened in half-open state\n";
        }
    }
    
    public function getState(): string
    {
        return $this->state;
    }
    
    public function getFailureCount(): int
    {
        return $this->failureCount;
    }
    
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
    
    public function getInstanceId(): string
    {
        return $this->instanceId;
    }
    
    public function reset(): void
    {
        $this->state = 'closed';
        $this->failureCount = 0;
        $this->successCount = 0;
        $this->lastFailureTime = 0;
        $this->lastSuccessTime = 0;
        
        echo "Circuit breaker for {$this->instanceId} reset\n";
    }
}

// Rate Limiter Registry
class RateLimiterRegistry
{
    private array $rateLimiters;
    
    public function __construct()
    {
        $this->rateLimiters = [];
    }
    
    public function register(string $instanceId, RateLimiter $rateLimiter): void
    {
        $this->rateLimiters[$instanceId] = $rateLimiter;
    }
    
    public function get(string $instanceId): ?RateLimiter
    {
        return $this->rateLimiters[$instanceId] ?? null;
    }
    
    public function getAll(): array
    {
        return $this->rateLimiters;
    }
}

// Rate Limiter
class RateLimiter
{
    private string $instanceId;
    private int $maxRequests;
    private int $windowSeconds;
    private array $requests;
    private float $windowStart;
    
    public function __construct(string $instanceId, int $maxRequests, int $windowSeconds)
    {
        $this->instanceId = $instanceId;
        $this->maxRequests = $maxRequests;
        $this->windowSeconds = $windowSeconds;
        $this->requests = [];
        $this->windowStart = microtime(true);
    }
    
    public function allow(): bool
    {
        $currentTime = microtime(true);
        
        // Reset window if expired
        if ($currentTime - $this->windowStart >= $this->windowSeconds) {
            $this->requests = [];
            $this->windowStart = $currentTime;
        }
        
        // Check if under limit
        if (count($this->requests) < $this->maxRequests) {
            $this->requests[] = $currentTime;
            return true;
        }
        
        return false;
    }
    
    public function getAllowedRequests(): int
    {
        return count($this->requests);
    }
    
    public function getTotalRequests(): int
    {
        return $this->maxRequests;
    }
    
    public function getWindowSeconds(): int
    {
        return $this->windowSeconds;
    }
    
    public function getInstanceId(): string
    {
        return $this->instanceId;
    }
}

// Distributed Tracing
class DistributedTracing
{
    private array $spans;
    private array $activeSpans;
    
    public function __construct()
    {
        $this->spans = [];
        $this->activeSpans = [];
    }
    
    public function startSpan(string $operationName, array $tags = []): string
    {
        $spanId = uniqid('span_');
        
        $span = [
            'id' => $spanId,
            'operation_name' => $operationName,
            'start_time' => microtime(true),
            'tags' => $tags,
            'parent_id' => null,
            'status' => 'active'
        ];
        
        $this->spans[$spanId] = $span;
        $this->activeSpans[$spanId] = $span;
        
        return $spanId;
    }
    
    public function finishSpan(string $spanId, array $tags = []): void
    {
        if (!isset($this->spans[$spanId])) {
            return;
        }
        
        $span = &$this->spans[$spanId];
        $span['end_time'] = microtime(true);
        $span['duration'] = $span['end_time'] - $span['start_time'];
        $span['status'] = 'finished';
        
        if (!empty($tags)) {
            $span['tags'] = array_merge($span['tags'], $tags);
        }
        
        unset($this->activeSpans[$spanId]);
    }
    
    public function addTag(string $spanId, string $key, $value): void
    {
        if (isset($this->spans[$spanId])) {
            $this->spans[$spanId]['tags'][$key] = $value;
        }
    }
    
    public function getSpans(): array
    {
        return $this->spans;
    }
    
    public function getActiveSpans(): array
    {
        return $this->activeSpans;
    }
    
    public function getTrace(string $spanId): array
    {
        $trace = [];
        $span = $this->spans[$spanId] ?? null;
        
        if ($span) {
            $trace[] = $span;
            
            // Add child spans
            foreach ($this->spans as $childSpan) {
                if ($childSpan['parent_id'] === $spanId) {
                    $trace[] = $childSpan;
                }
            }
        }
        
        return $trace;
    }
}

// Metrics Collector
class MetricsCollector
{
    private array $counters;
    private array $gauges;
    private array $histograms;
    private array $timers;
    
    public function __construct()
    {
        $this->counters = [];
        $this->gauges = [];
        $this->histograms = [];
        $this->timers = [];
    }
    
    public function incrementCounter(string $name, float $value = 1.0, array $tags = []): void
    {
        $key = $this->makeKey($name, $tags);
        
        if (!isset($this->counters[$key])) {
            $this->counters[$key] = 0;
        }
        
        $this->counters[$key] += $value;
    }
    
    public function setGauge(string $name, float $value, array $tags = []): void
    {
        $key = $this->makeKey($name, $tags);
        $this->gauges[$key] = $value;
    }
    
    public function recordHistogram(string $name, float $value, array $tags = []): void
    {
        $key = $this->makeKey($name, $tags);
        
        if (!isset($this->histograms[$key])) {
            $this->histograms[$key] = [];
        }
        
        $this->histograms[$key][] = $value;
    }
    
    public function recordTimer(string $name, float $duration, array $tags = []): void
    {
        $key = $this->makeKey($name, $tags);
        
        if (!isset($this->timers[$key])) {
            $this->timers[$key] = [];
        }
        
        $this->timers[$key][] = $duration;
    }
    
    public function recordRequest(string $service, float $duration, bool $success): void
    {
        $this->recordTimer("request_duration", $duration, ['service' => $service]);
        $this->incrementCounter("request_total", 1, ['service' => $service]);
        
        if ($success) {
            $this->incrementCounter("request_success", 1, ['service' => $service]);
        } else {
            $this->incrementCounter("request_error", 1, ['service' => $service]);
        }
    }
    
    private function makeKey(string $name, array $tags): string
    {
        if (empty($tags)) {
            return $name;
        }
        
        ksort($tags);
        $tagString = implode(',', array_map(fn($k, $v) => "$k:$v", array_keys($tags), $tags));
        
        return "$name{$tagString}";
    }
    
    public function getCounters(): array
    {
        return $this->counters;
    }
    
    public function getGauges(): array
    {
        return $this->gauges;
    }
    
    public function getHistograms(): array
    {
        return $this->histograms;
    }
    
    public function getTimers(): array
    {
        return $this->timers;
    }
    
    public function getServiceMetrics(string $service): array
    {
        return [
            'request_total' => $this->counters["request_totalservice:$service"] ?? 0,
            'request_success' => $this->counters["request_successservice:$service"] ?? 0,
            'request_error' => $this->counters["request_errorservice:$service"] ?? 0,
            'avg_duration' => $this->calculateAverage("request_durationservice:$service"),
            'p95_duration' => $this->calculatePercentile("request_durationservice:$service", 95),
            'p99_duration' => $this->calculatePercentile("request_durationservice:$service", 99)
        ];
    }
    
    private function calculateAverage(string $key): float
    {
        $values = $this->timers[$key] ?? [];
        
        if (empty($values)) {
            return 0;
        }
        
        return array_sum($values) / count($values);
    }
    
    private function calculatePercentile(string $key, int $percentile): float
    {
        $values = $this->timers[$key] ?? [];
        
        if (empty($values)) {
            return 0;
        }
        
        sort($values);
        $index = (count($values) - 1) * ($percentile / 100);
        
        return $values[(int)$index];
    }
}

// Config Manager
class ConfigManager
{
    private array $config;
    private array $watchers;
    
    public function __construct()
    {
        $this->config = [];
        $this->watchers = [];
        $this->loadDefaultConfig();
    }
    
    private function loadDefaultConfig(): void
    {
        $this->config = [
            'service_discovery' => [
                'type' => 'consul',
                'host' => 'localhost',
                'port' => 8500
            ],
            'tracing' => [
                'type' => 'jaeger',
                'host' => 'localhost',
                'port' => 14268,
                'sampling_rate' => 0.1
            ],
            'metrics' => [
                'type' => 'prometheus',
                'host' => 'localhost',
                'port' => 9090,
                'interval' => 15
            ],
            'circuit_breaker' => [
                'failure_threshold' => 5,
                'recovery_timeout' => 60,
                'timeout' => 30
            ],
            'rate_limiting' => [
                'default_limit' => 1000,
                'window_seconds' => 60,
                'burst_size' => 100
            ],
            'load_balancing' => [
                'default_algorithm' => 'round_robin',
                'health_check_interval' => 10,
                'unhealthy_threshold' => 3
            ]
        ];
    }
    
    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
        $this->notifyWatchers($key, $value);
    }
    
    public function watch(string $key, callable $callback): void
    {
        if (!isset($this->watchers[$key])) {
            $this->watchers[$key] = [];
        }
        
        $this->watchers[$key][] = $callback;
    }
    
    private function notifyWatchers(string $key, $value): void
    {
        if (isset($this->watchers[$key])) {
            foreach ($this->watchers[$key] as $callback) {
                $callback($key, $value);
            }
        }
    }
    
    public function getAll(): array
    {
        return $this->config;
    }
}

// Advanced Microservice
class Microservice
{
    private string $name;
    private array $instances;
    private array $definition;
    private LoadBalancer $loadBalancer;
    private HealthChecker $healthChecker;
    
    public function __construct(string $name, array $instances, array $definition)
    {
        $this->name = $name;
        $this->instances = $instances;
        $this->definition = $definition;
        $this->loadBalancer = new LoadBalancer($definition['load_balancer']);
        $this->healthChecker = new HealthChecker($definition['health_check']);
        
        $this->initializeInstances();
    }
    
    private function initializeInstances(): void
    {
        foreach ($this->instances as $instance) {
            $this->loadBalancer->addInstance($instance);
            $this->healthChecker->addInstance($instance);
        }
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getInstances(): array
    {
        return $this->instances;
    }
    
    public function getInstanceForRequest(array $request): ServiceInstance
    {
        $healthyInstances = $this->getHealthyInstances();
        
        if (empty($healthyInstances)) {
            throw new Exception("No healthy instances available for {$this->name}");
        }
        
        return $this->loadBalancer->selectInstance($healthyInstances, $request);
    }
    
    public function handleRequest(array $request): array
    {
        $instance = $this->getInstanceForRequest($request);
        
        try {
            $result = $instance->handleRequest($request);
            
            // Simulate processing
            $processingTime = rand(10, 100);
            usleep($processingTime * 1000);
            
            return [
                'success' => true,
                'data' => $result,
                'instance' => $instance->getId(),
                'processing_time' => $processingTime
            ];
            
        } catch (Exception $e) {
            $instance->markUnhealthy();
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'instance' => $instance->getId()
            ];
        }
    }
    
    public function getHealthyInstances(): array
    {
        return array_filter($this->instances, fn($instance) => $instance->isHealthy());
    }
    
    public function isHealthy(): bool
    {
        return !empty($this->getHealthyInstances());
    }
    
    public function getLoadBalancer(): LoadBalancer
    {
        return $this->loadBalancer;
    }
    
    public function getHealthChecker(): HealthChecker
    {
        return $this->healthChecker;
    }
    
    public function getDependencies(): array
    {
        return $this->definition['dependencies'] ?? [];
    }
    
    public function getDefinition(): array
    {
        return $this->definition;
    }
}

// Service Instance
class ServiceInstance
{
    private string $id;
    private bool $healthy;
    private float $lastHealthCheck;
    private array $metadata;
    
    public function __construct(string $id, array $definition)
    {
        $this->id = $id;
        $this->healthy = true;
        $this->lastHealthCheck = microtime(true);
        $this->metadata = $definition['metadata'] ?? [];
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function isHealthy(): bool
    {
        return $this->healthy;
    }
    
    public function markHealthy(): void
    {
        $this->healthy = true;
        $this->lastHealthCheck = microtime(true);
    }
    
    public function markUnhealthy(): void
    {
        $this->healthy = false;
        $this->lastHealthCheck = microtime(true);
    }
    
    public function getLastHealthCheck(): float
    {
        return $this->lastHealthCheck;
    }
    
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    public function handleRequest(array $request): array
    {
        // Simulate request handling
        return [
            'instance_id' => $this->id,
            'request' => $request,
            'timestamp' => microtime(true),
            'processed' => true
        ];
    }
}

// Load Balancer
class LoadBalancer
{
    private string $algorithm;
    private array $instances;
    private int $currentIndex;
    
    public function __construct(string $algorithm = 'round_robin')
    {
        $this->algorithm = $algorithm;
        $this->instances = [];
        $this->currentIndex = 0;
    }
    
    public function addInstance(ServiceInstance $instance): void
    {
        $this->instances[] = $instance;
    }
    
    public function selectInstance(array $instances, array $request): ServiceInstance
    {
        switch ($this->algorithm) {
            case 'round_robin':
                return $this->roundRobin($instances);
            case 'least_connections':
                return $this->leastConnections($instances);
            case 'weighted_round_robin':
                return $this->weightedRoundRobin($instances);
            case 'random':
                return $this->random($instances);
            case 'sticky_session':
                return $this->stickySession($instances, $request);
            case 'consistent_hash':
                return $this->consistentHash($instances, $request);
            default:
                return $instances[0];
        }
    }
    
    private function roundRobin(array $instances): ServiceInstance
    {
        if (empty($instances)) {
            throw new Exception("No instances available");
        }
        
        $instance = $instances[$this->currentIndex % count($instances)];
        $this->currentIndex++;
        
        return $instance;
    }
    
    private function leastConnections(array $instances): ServiceInstance
    {
        // Simulate connection counting
        $connections = [];
        
        foreach ($instances as $instance) {
            $connections[$instance->getId()] = rand(0, 10);
        }
        
        asort($connections);
        $instanceId = array_key_first($connections);
        
        foreach ($instances as $instance) {
            if ($instance->getId() === $instanceId) {
                return $instance;
            }
        }
        
        return $instances[0];
    }
    
    private function weightedRoundRobin(array $instances): ServiceInstance
    {
        // Simulate weights
        $weights = [];
        
        foreach ($instances as $instance) {
            $weights[$instance->getId()] = rand(1, 5);
        }
        
        $totalWeight = array_sum($weights);
        $random = rand(0, $totalWeight);
        
        $currentWeight = 0;
        foreach ($weights as $instanceId => $weight) {
            $currentWeight += $weight;
            
            if ($random <= $currentWeight) {
                foreach ($instances as $instance) {
                    if ($instance->getId() === $instanceId) {
                        return $instance;
                    }
                }
            }
        }
        
        return $instances[0];
    }
    
    private function random(array $instances): ServiceInstance
    {
        if (empty($instances)) {
            throw new Exception("No instances available");
        }
        
        return $instances[array_rand($instances)];
    }
    
    private function stickySession(array $instances, array $request): ServiceInstance
    {
        $sessionId = $request['session_id'] ?? null;
        
        if ($sessionId) {
            // Simulate session affinity
            $instanceIndex = crc32($sessionId) % count($instances);
            return $instances[$instanceIndex];
        }
        
        return $this->roundRobin($instances);
    }
    
    private function consistentHash(array $instances, array $request): ServiceInstance
    {
        $key = $request['user_id'] ?? 'default';
        
        if (empty($instances)) {
            throw new Exception("No instances available");
        }
        
        $hash = crc32($key);
        $index = abs($hash) % count($instances);
        
        return $instances[$index];
    }
    
    public function getType(): string
    {
        return $this->algorithm;
    }
}

// Health Checker
class HealthChecker
{
    private string $healthCheckEndpoint;
    private array $instances;
    private float $checkInterval;
    private int $unhealthyThreshold;
    
    public function __construct(string $healthCheckEndpoint = '/health')
    {
        $this->healthCheckEndpoint = $healthCheckEndpoint;
        $this->instances = [];
        $this->checkInterval = 10;
        $this->unhealthyThreshold = 3;
    }
    
    public function addInstance(ServiceInstance $instance): void
    {
        $this->instances[$instance->getId()] = $instance;
    }
    
    public function checkAll(): void
    {
        foreach ($this->instances as $instance) {
            $this->checkInstance($instance);
        }
    }
    
    private function checkInstance(ServiceInstance $instance): void
    {
        $currentTime = microtime(true);
        
        // Simulate health check
        $isHealthy = rand(0, 100) > 10; // 90% chance of being healthy
        
        if ($isHealthy) {
            $instance->markHealthy();
        } else {
            $instance->markUnhealthy();
        }
    }
    
    public function getHealthyInstances(): array
    {
        return array_filter($this->instances, fn($instance) => $instance->isHealthy());
    }
    
    public function getUnhealthyInstances(): array
    {
        return array_filter($this->instances, fn($instance) => !$instance->isHealthy());
    }
}

// Traffic Manager
class TrafficManager
{
    private array $policies;
    
    public function __construct()
    {
        $this->policies = [];
    }
    
    public function manageTraffic(Route $route, array $request): void
    {
        $toService = $route->getTo();
        
        if (isset($this->policies[$toService])) {
            $this->policies[$toService]->apply($request);
        }
    }
    
    public function addPolicy(string $service, TrafficPolicy $policy): void
    {
        $this->policies[$service] = $policy;
    }
}

// Route
class Route
{
    private string $from;
    private string $to;
    private array $metadata;
    
    public function __construct(string $from, string $to, array $metadata = [])
    {
        $this->from = $from;
        $this->to = $to;
        $this->metadata = $metadata;
    }
    
    public function getFrom(): string
    {
        return $this->from;
    }
    
    public function getTo(): string
    {
        return $this->to;
    }
    
    public function getMetadata(): array
    {
        return $this->metadata;
    }
}

// Policy Classes
class SecurityPolicy
{
    private string $service;
    private array $rules;
    
    public function __construct(string $service)
    {
        $this->service = $service;
        $this->rules = [
            'authentication' => true,
            'authorization' => true,
            'encryption' => true,
            'rate_limit' => true
        ];
    }
    
    public function apply(array &$request): void
    {
        // Apply security rules
        if ($this->rules['authentication']) {
            $request['authenticated'] = true;
        }
        
        if ($this->rules['encryption']) {
            $request['encrypted'] = true;
        }
    }
}

class TrafficPolicy
{
    private string $service;
    private array $rules;
    
    public function __construct(string $service)
    {
        $this->service = $service;
        $this->rules = [
            'throttling' => true,
            'circuit_breaker' => true,
            'timeout' => 30
        ];
    }
    
    public function apply(array &$request): void
    {
        // Apply traffic rules
        if ($this->rules['throttling']) {
            $request['throttled'] = false;
        }
        
        $request['timeout'] = $this->rules['timeout'];
    }
}

class RetryPolicy
{
    private string $service;
    private array $rules;
    
    public function __construct(string $service)
    {
        $this->service = $service;
        $this->rules = [
            'max_retries' => 3,
            'backoff_strategy' => 'exponential',
            'timeout' => 5
        ];
    }
    
    public function apply(array &$request): void
    {
        $request['max_retries'] = $this->rules['max_retries'];
        $request['backoff_strategy'] = $this->rules['backoff_strategy'];
        $request['timeout'] = $this->rules['timeout'];
    }
}

// Service Implementations (simplified)
class APIGateway
{
    private string $id;
    private array $definition;
    
    public function __construct(string $id, array $definition)
    {
        $this->id = $id;
        $this->definition = $definition;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getDefinition(): array
    {
        return $this->definition;
    }
}

class UserService
{
    private string $id;
    private array $definition;
    
    public function __construct(string $id, array $definition)
    {
        $this->id = $id;
        $this->definition = $definition;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getDefinition(): array
    {
        return $this->definition;
    }
}

class ProductService
{
    private string $id;
    private array $definition;
    
    public function __construct(string $id, array $definition)
    {
        $this->id = $id;
        $this->definition = $definition;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getDefinition(): array
    {
        return $this->definition;
    }
}

class OrderService
{
    private string $id;
    private array $definition;
    
    public function __construct(string $id, array $definition)
    {
        $this->id = $id;
        $this->definition = $definition;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getDefinition(): array
    {
        return $this->definition;
    }
}

class PaymentService
{
    private string $id;
    private array $definition;
    
    public function __construct(string $id, array $definition)
    {
        $this->id = $id;
        $this->definition = $definition;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getDefinition(): array
    {
        return $this->definition;
    }
}

class NotificationService
{
    private string $id;
    private array $definition;
    
    public function __construct(string $id, array $definition)
    {
        $this->id = $id;
        $this->definition = $definition;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getDefinition(): array
    {
        return $this->definition;
    }
}

class CacheService
{
    private string $id;
    private array $definition;
    
    public function __construct(string $id, array $definition)
    {
        $this->id = $id;
        $this->definition = $definition;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getDefinition(): array
    {
        return $this->definition;
    }
}

class DatabaseService
{
    private string $id;
    private array $definition;
    
    public function __construct(string $id, array $definition)
    {
        $this->id = $id;
        $this->definition = $definition;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getDefinition(): array
    {
        return $this->definition;
    }
}

// Advanced Microservices Examples
class AdvancedMicroservicesExamples
{
    public function demonstrateAdvancedArchitecture(): void
    {
        echo "Advanced Microservices Architecture Demo\n";
        echo str_repeat("-", 45) . "\n";
        
        $architecture = new AdvancedMicroservicesArchitecture();
        
        echo "Created advanced microservices architecture:\n";
        
        // Show service configuration
        $services = $architecture->getServices();
        foreach ($services as $serviceName => $service) {
            echo "  $serviceName:\n";
            echo "    Instances: " . count($service->getInstances()) . "\n";
            echo "    Load Balancer: " . $service->getLoadBalancer()->getType() . "\n";
            echo "    Dependencies: " . implode(', ', $service->getDependencies()) . "\n";
            echo "    Healthy: " . ($service->isHealthy() ? 'Yes' : 'No') . "\n\n";
        }
        
        // Show service mesh
        echo "Service Mesh:\n";
        $serviceMesh = $architecture->getServiceMesh();
        $routes = $serviceMesh->getRoutes();
        
        echo "  Routes: " . count($routes) . "\n";
        foreach ($routes as $routeKey => $route) {
            echo "    $routeKey: {$route->getFrom()} -> {$route->getTo()}\n";
        }
        
        echo "  Policies: " . count($serviceMesh->getPolicies()) . "\n";
        
        // Show circuit breakers
        echo "\nCircuit Breakers:\n";
        $circuitBreakers = $architecture->getCircuitBreakerRegistry()->getAll();
        
        foreach ($circuitBreakers as $instanceId => $circuitBreaker) {
            echo "  $instanceId: {$circuitBreaker->getState()}\n";
            echo "    Failures: {$circuitBreaker->getFailureCount()}\n";
            echo "    Successes: {$circuitBreaker->getSuccessCount()}\n";
        }
        
        // Show rate limiters
        echo "\nRate Limiters:\n";
        $rateLimiters = $architecture->getRateLimiterRegistry()->getAll();
        
        foreach ($rateLimiters as $instanceId => $rateLimiter) {
            echo "  $instanceId:\n";
            echo "    Allowed: {$rateLimiter->getAllowedRequests()}/{$rateLimiter->getTotalRequests()}\n";
            echo "    Window: {$rateLimiter->getWindowSeconds()}s\n";
        }
    }
    
    public function demonstrateCircuitBreaker(): void
    {
        echo "\nCircuit Breaker Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $circuitBreaker = new CircuitBreaker('test_service', 3, 5, 2);
        
        echo "Testing circuit breaker:\n";
        echo "  Failure threshold: 3\n";
        echo "  Recovery timeout: 5s\n";
        echo "  Timeout: 2s\n\n";
        
        // Test normal operation
        echo "Normal operation:\n";
        for ($i = 0; $i < 2; $i++) {
            $allowed = $circuitBreaker->allow();
            echo "  Request $i: " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
            $circuitBreaker->recordSuccess();
            echo "  State: {$circuitBreaker->getState()}\n";
        }
        
        // Test failure threshold
        echo "\nTesting failure threshold:\n";
        for ($i = 0; $i < 3; $i++) {
            $allowed = $circuitBreaker->allow();
            echo "  Request " . ($i + 3) . ": " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
            $circuitBreaker->recordFailure();
            echo "  State: {$circuitBreaker->getState()}\n";
        }
        
        // Test open state
        echo "\nTesting open state:\n";
        for ($i = 0; $i < 3; $i++) {
            $allowed = $circuitBreaker->allow();
            echo "  Request " . ($i + 6) . ": " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
            echo "  State: {$circuitBreaker->getState()}\n";
        }
        
        // Test recovery
        echo "\nTesting recovery:\n";
        sleep(6); // Wait for recovery timeout
        
        $allowed = $circuitBreaker->allow();
        echo "  Request after recovery: " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
        echo "  State: {$circuitBreaker->getState()}\n";
        
        if ($allowed) {
            $circuitBreaker->recordSuccess();
            echo "  State after success: {$circuitBreaker->getState()}\n";
        }
        
        // Test half-open state
        echo "\nTesting half-open state:\n";
        $circuitBreaker->recordFailure(); // Back to open
        sleep(6); // Recovery timeout
        
        $allowed = $circuitBreaker->allow();
        echo "  Half-open request: " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
        echo "  State: {$circuitBreaker->getState()}\n";
        
        if ($allowed) {
            $circuitBreaker->recordSuccess();
            echo "  State after success: {$circuitBreaker->getState()}\n";
        } else {
            $circuitBreaker->recordFailure();
            echo "  State after failure: {$circuitBreaker->getState()}\n";
        }
    }
    
    public function demonstrateRateLimiting(): void
    {
        echo "\nRate Limiting Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $rateLimiter = new RateLimiter('test_service', 5, 10);
        
        echo "Testing rate limiter:\n";
        echo "  Max requests: 5\n";
        echo "  Window: 10s\n\n";
        
        // Test normal operation
        echo "Testing within limits:\n";
        for ($i = 0; $i < 5; $i++) {
            $allowed = $rateLimiter->allow();
            echo "  Request $i: " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
            echo "  Allowed: {$rateLimiter->getAllowedRequests()}/5\n";
        }
        
        // Test exceeding limits
        echo "\nTesting rate limit exceeded:\n";
        for ($i = 5; $i < 8; $i++) {
            $allowed = $rateLimiter->allow();
            echo "  Request $i: " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
            echo "  Allowed: {$rateLimiter->getAllowedRequests()}/5\n";
        }
        
        // Test window reset
        echo "\nTesting window reset:\n";
        sleep(11); // Wait for window to reset
        
        $allowed = $rateLimiter->allow();
        echo "  Request after reset: " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
        echo "  Allowed: {$rateLimiter->getAllowedRequests()}/5\n";
        
        // Test burst capacity
        echo "\nTesting burst capacity:\n";
        for ($i = 0; $i < 3; $i++) {
            $allowed = $rateLimiter->allow();
            echo "  Request $i: " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
        }
    }
    
    public function demonstrateDistributedTracing(): void
    {
        echo "\nDistributed Tracing Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $tracing = new DistributedTracing();
        
        echo "Creating distributed trace:\n";
        
        // Create root span
        $rootSpan = $tracing->startSpan('api_request', [
            'service' => 'api-gateway',
            'method' => 'POST',
            'path' => '/api/orders'
        ]);
        
        echo "  Root span: $rootSpan\n";
        
        // Create child spans
        $authSpan = $tracing->startSpan('authentication', [
            'service' => 'user-service',
            'user_id' => '12345'
        ]);
        
        $orderSpan = $tracing->startSpan('order_processing', [
            'service' => 'order-service',
            'order_id' => 'order_6789'
        ]);
        
        $paymentSpan = $tracing->startSpan('payment_processing', [
            'service' => 'payment-service',
            'amount' => 99.99
        ]);
        
        echo "  Child spans: $authSpan, $orderSpan, $paymentSpan\n";
        
        // Add tags
        $tracing->addTag($rootSpan, 'user_agent', 'Mozilla/5.0');
        $tracing->addTag($authSpan, 'auth_method', 'jwt');
        $tracing->addTag($orderSpan, 'order_type', 'purchase');
        $tracing->addTag($paymentSpan, 'payment_method', 'credit_card');
        
        // Finish spans
        $tracing->finishSpan($authSpan);
        $tracing->finishSpan($paymentSpan);
        $tracing->finishSpan($orderSpan);
        $tracing->finishSpan($rootSpan);
        
        // Show trace
        echo "\nTrace details:\n";
        $trace = $tracing->getTrace($rootSpan);
        
        foreach ($trace as $span) {
            echo "  {$span['operation_name']}:\n";
            echo "    ID: {$span['id']}\n";
            echo "    Duration: " . round($span['duration'] * 1000, 2) . "ms\n";
            echo "    Tags: " . json_encode($span['tags']) . "\n";
        }
        
        // Show all spans
        echo "\nAll spans: " . count($tracing->getSpans()) . "\n";
        echo "Active spans: " . count($tracing->getActiveSpans()) . "\n";
    }
    
    public function demonstrateMetricsCollection(): void
    {
        echo "\nMetrics Collection Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $metrics = new MetricsCollector();
        
        echo "Collecting metrics:\n";
        
        // Simulate requests
        $services = ['api-gateway', 'user-service', 'order-service', 'payment-service'];
        
        foreach ($services as $service) {
            for ($i = 0; $i < 100; $i++) {
                $duration = rand(10, 200);
                $success = rand(0, 100) > 10; // 90% success rate
                
                $metrics->recordRequest($service, $duration, $success);
                
                // Add custom metrics
                $metrics->incrementCounter('custom_counter', 1, ['service' => $service]);
                $metrics->setGauge('active_connections', rand(50, 200), ['service' => $service]);
                $metrics->recordHistogram('response_size', rand(100, 10000), ['service' => $service]);
            }
        }
        
        echo "  Recorded " . count($services) * 100 . " requests\n";
        
        // Show metrics
        echo "\nMetrics summary:\n";
        
        foreach ($services as $service) {
            $serviceMetrics = $metrics->getServiceMetrics($service);
            
            echo "  $service:\n";
            echo "    Total requests: {$serviceMetrics['request_total']}\n";
            echo "    Success rate: " . round(($serviceMetrics['request_success'] / $serviceMetrics['request_total']) * 100, 1) . "%\n";
            echo "    Error rate: " . round(($serviceMetrics['request_error'] / $serviceMetrics['request_total']) * 100, 1) . "%\n";
            echo "    Avg duration: " . round($serviceMetrics['avg_duration'], 2) . "ms\n";
            echo "    P95 duration: " . round($serviceMetrics['p95_duration'], 2) . "ms\n";
            echo "    P99 duration: " . round($serviceMetrics['p99_duration'], 2) . "ms\n";
        }
        
        // Show all metrics
        echo "\nAll metrics:\n";
        echo "  Counters: " . count($metrics->getCounters()) . "\n";
        echo "  Gauges: " . count($metrics->getGauges()) . "\n";
        echo "  Histograms: " . count($metrics->getHistograms()) . "\n";
        echo "  Timers: " . count($getMetrics->getTimers()) . "\n";
    }
    
    public function demonstrateConfigurationManagement(): void
    {
        echo "\nConfiguration Management Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        $config = new ConfigManager();
        
        echo "Default configuration:\n";
        $defaultConfig = $config->getAll();
        
        foreach ($defaultConfig as $section => $values) {
            echo "  $section:\n";
            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    echo "    $key:\n";
                    foreach ($value as $subKey => $subValue) {
                        echo "      $subKey: $subValue\n";
                    }
                } else {
                    echo "    $key: $value\n";
                }
            }
        }
        
        // Test configuration changes
        echo "\nTesting configuration changes:\n";
        
        $config->set('circuit_breaker.failure_threshold', 10);
        echo "  Changed circuit_breaker.failure_threshold to 10\n";
        
        $config->set('rate_limiting.default_limit', 2000);
        echo "  Changed rate_limiting.default_limit to 2000\n";
        
        $config->set('new_section.new_value', 'test');
        echo "  Added new_section.new_value = 'test'\n";
        
        // Test configuration watching
        echo "\nTesting configuration watching:\n";
        
        $config->watch('circuit_breaker.failure_threshold', function($key, $value) {
            echo "  Configuration changed: $key = $value\n";
        });
        
        $config->set('circuit_breaker.failure_threshold', 15);
        echo "  Triggered change for circuit_breaker.failure_threshold\n";
        
        // Test nested configuration
        echo "\nNested configuration access:\n";
        echo "  service_discovery.type: " . $config->get('service_discovery.type') . "\n";
        echo "  tracing.sampling_rate: " . $config->get('tracing.sampling_rate') . "\n";
        echo "  non_existent: " . ($config->get('non_existent', 'default') ?? 'default') . "\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nAdvanced Microservices Best Practices\n";
        echo str_repeat("-", 45) . "\n";
        
        echo "1. Service Design:\n";
        echo "   • Keep services small and focused\n";
        echo "   • Use domain-driven design\n";
        echo "   • Implement proper API versioning\n";
        echo "   • Use async communication patterns\n";
        echo "   • Design for failure\n\n";
        
        echo "2. Service Mesh:\n";
        echo "   • Implement service discovery\n";
        echo "   • Use traffic management\n";
        echo "   • Apply security policies\n";
        echo "   • Implement observability\n";
        echo "   • Use sidecar patterns\n\n";
        
        echo "3. Circuit Breaker:\n";
        echo "   • Set appropriate thresholds\n";
        echo "   • Implement proper recovery\n";
        echo "   • Use half-open state\n";
        echo "   • Monitor circuit state\n";
        echo "   • Configure timeouts properly\n\n";
        
        echo "4. Rate Limiting:\n";
        echo "   • Use sliding windows\n";
        echo "   • Implement burst capacity\n";
        echo "   • Use distributed limiting\n";
        echo "   • Monitor rate limit status\n";
        echo "   • Handle limit exceeded gracefully\n\n";
        
        echo "5. Observability:\n";
        echo "   • Implement distributed tracing\n";
        echo "   • Use structured logging\n";
        echo "   • Collect comprehensive metrics\n";
        echo "   • Use health checks\n";
        echo "   • Implement alerting\n\n";
        
        echo "6. Configuration:\n";
        echo "   • Use centralized configuration\n";
        echo "   • Implement configuration watching\n";
        echo "   • Use environment-specific configs\n";
        echo "   • Validate configuration\n";
        echo "   • Use feature flags";
    }
    
    public function runAllExamples(): void
    {
        echo "Advanced Microservices Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateAdvancedArchitecture();
        $this->demonstrateCircuitBreaker();
        $this->demonstrateRateLimiting();
        $this->demonstrateDistributedTracing();
        $this->demonstrateMetricsCollection();
        $this->demonstrateConfigurationManagement();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runAdvancedMicroservicesDemo(): void
{
    $examples = new AdvancedMicroservicesExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAdvancedMicroservicesDemo();
}
?>
