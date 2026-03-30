<?php
/**
 * Cloud Native Applications
 * 
 * Building cloud-native applications with microservices, containers, and orchestration.
 */

// Cloud Native Application Builder
class CloudNativeAppBuilder
{
    private array $services = [];
    private array $configurations = [];
    private array $deployments = [];
    private array $monitoring = [];
    
    public function __construct()
    {
        $this->initializeConfigurations();
    }
    
    /**
     * Initialize cloud-native configurations
     */
    private function initializeConfigurations(): void
    {
        $this->configurations = [
            'microservice' => [
                'architecture' => 'microservices',
                'service_discovery' => 'consul',
                'api_gateway' => 'kong',
                'service_mesh' => 'istio',
                'circuit_breaker' => 'hystrix',
                'load_balancer' => 'nginx',
                'health_check' => 'enabled',
                'graceful_shutdown' => 'enabled'
            ],
            'containerization' => [
                'runtime' => 'docker',
                'orchestration' => 'kubernetes',
                'image_registry' => 'docker-hub',
                'container_limits' => [
                    'memory' => '512Mi',
                    'cpu' => '500m'
                ],
                'health_checks' => [
                    'liveness' => '/health',
                    'readiness' => '/ready'
                ],
                'environment_variables' => 'enabled',
                'config_maps' => 'enabled',
                'secrets' => 'enabled'
            ],
            'observability' => [
                'logging' => 'fluentd',
                'metrics' => 'prometheus',
                'tracing' => 'jaeger',
                'visualization' => 'grafana',
                'alerting' => 'alertmanager',
                'distributed_tracing' => 'enabled',
                'structured_logging' => 'enabled'
            ],
            'scaling' => [
                'horizontal_pod_autoscaler' => 'enabled',
                'vertical_pod_autoscaler' => 'enabled',
                'cluster_autoscaler' => 'enabled',
                'custom_metrics' => 'enabled',
                'scaling_policies' => [
                    'min_replicas' => 2,
                    'max_replicas' => 10,
                    'target_cpu_utilization' => 70
                ]
            ],
            'resilience' => [
                'retry_policy' => 'exponential_backoff',
                'timeout_policy' => 'circuit_breaker',
                'bulkhead_pattern' => 'enabled',
                'fallback_mechanism' => 'enabled',
                'rate_limiting' => 'enabled'
            ]
        ];
    }
    
    /**
     * Add microservice
     */
    public function addMicroservice(string $name, array $config): void
    {
        $service = array_merge([
            'name' => $name,
            'type' => 'microservice',
            'port' => 8080,
            'replicas' => 3,
            'resources' => [
                'requests' => [
                    'memory' => '256Mi',
                    'cpu' => '250m'
                ],
                'limits' => [
                    'memory' => '512Mi',
                    'cpu' => '500m'
                ]
            ],
            'health_checks' => [
                'liveness' => '/health',
                'readiness' => '/ready',
                'startup' => '/startup'
            ],
            'environment' => [
                'APP_NAME' => $name,
                'LOG_LEVEL' => 'info',
                'TRACING_ENABLED' => 'true'
            ],
            'dependencies' => [],
            'endpoints' => []
        ], $config);
        
        $this->services[$name] = $service;
    }
    
    /**
     * Generate Kubernetes deployment
     */
    public function generateK8sDeployment(string $serviceName): array
    {
        if (!isset($this->services[$serviceName])) {
            return ['error' => 'Service not found'];
        }
        
        $service = $this->services[$serviceName];
        
        $deployment = [
            'apiVersion' => 'apps/v1',
            'kind' => 'Deployment',
            'metadata' => [
                'name' => $serviceName,
                'labels' => [
                    'app' => $serviceName,
                    'version' => 'v1'
                ]
            ],
            'spec' => [
                'replicas' => $service['replicas'],
                'selector' => [
                    'matchLabels' => [
                        'app' => $serviceName
                    ]
                ],
                'template' => [
                    'metadata' => [
                        'labels' => [
                            'app' => $serviceName,
                            'version' => 'v1'
                        ]
                    ],
                    'spec' => [
                        'containers' => [
                            [
                                'name' => $serviceName,
                                'image' => $service['image'] ?? 'php:8.2-apache',
                                'ports' => [
                                    [
                                        'containerPort' => $service['port']
                                    ]
                                ],
                                'resources' => $service['resources'],
                                'env' => $this->generateEnvironmentVariables($service['environment']),
                                'livenessProbe' => $this->generateHealthCheck('liveness', $service['health_checks']['liveness']),
                                'readinessProbe' => $this->generateHealthCheck('readiness', $service['health_checks']['readiness']),
                                'startupProbe' => $this->generateHealthCheck('startup', $service['health_checks']['startup']),
                                'volumeMounts' => [
                                    [
                                        'name' => 'config-volume',
                                        'mountPath' => '/app/config'
                                    ],
                                    [
                                        'name' => 'logs-volume',
                                        'mountPath' => '/app/logs'
                                    ]
                                ]
                            ]
                        ],
                        'volumes' => [
                            [
                                'name' => 'config-volume',
                                'configMap' => [
                                    'name' => $serviceName . '-config'
                                ]
                            ],
                            [
                                'name' => 'logs-volume',
                                'emptyDir' => []
                            ]
                        ],
                        'restartPolicy' => 'Always',
                        'terminationGracePeriodSeconds' => 30
                    ]
                ]
            ]
        ];
        
        return $deployment;
    }
    
    /**
     * Generate Kubernetes service
     */
    public function generateK8sService(string $serviceName): array
    {
        if (!isset($this->services[$serviceName])) {
            return ['error' => 'Service not found'];
        }
        
        $service = $this->services[$serviceName];
        
        return [
            'apiVersion' => 'v1',
            'kind' => 'Service',
            'metadata' => [
                'name' => $serviceName,
                'labels' => [
                    'app' => $serviceName
                ]
            ],
            'spec' => [
                'selector' => [
                    'app' => $serviceName
                ],
                'ports' => [
                    [
                        'protocol' => 'TCP',
                        'port' => 80,
                        'targetPort' => $service['port']
                    ]
                ],
                'type' => 'ClusterIP'
            ]
        ];
    }
    
    /**
     * Generate environment variables
     */
    private function generateEnvironmentVariables(array $envVars): array
    {
        $env = [];
        
        foreach ($envVars as $key => $value) {
            $env[] = [
                'name' => $key,
                'value' => $value
            ];
        }
        
        return $env;
    }
    
    /**
     * Generate health check configuration
     */
    private function generateHealthCheck(string $type, string $path): array
    {
        return [
            'httpGet' => [
                'path' => $path,
                'port' => 8080
            ],
            'initialDelaySeconds' => $type === 'startup' ? 30 : 10,
            'periodSeconds' => 10,
            'timeoutSeconds' => 5,
            'failureThreshold' => 3,
            'successThreshold' => 1
        ];
    }
    
    /**
     * Generate horizontal pod autoscaler
     */
    public function generateHPA(string $serviceName): array
    {
        $scaling = $this->configurations['scaling'];
        
        return [
            'apiVersion' => 'autoscaling/v2',
            'kind' => 'HorizontalPodAutoscaler',
            'metadata' => [
                'name' => $serviceName . '-hpa',
                'labels' => [
                    'app' => $serviceName
                ]
            ],
            'spec' => [
                'scaleTargetRef' => [
                    'apiVersion' => 'apps/v1',
                    'kind' => 'Deployment',
                    'name' => $serviceName
                ],
                'minReplicas' => $scaling['scaling_policies']['min_replicas'],
                'maxReplicas' => $scaling['scaling_policies']['max_replicas'],
                'metrics' => [
                    [
                        'type' => 'Resource',
                        'resource' => [
                            'name' => 'cpu',
                            'target' => [
                                'type' => 'Utilization',
                                'averageUtilization' => $scaling['scaling_policies']['target_cpu_utilization']
                            ]
                        ]
                    ],
                    [
                        'type' => 'Resource',
                        'resource' => [
                            'name' => 'memory',
                            'target' => [
                                'type' => 'Utilization',
                                'averageUtilization' => 80
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Generate config map
     */
    public function generateConfigMap(string $serviceName, array $config): array
    {
        return [
            'apiVersion' => 'v1',
            'kind' => 'ConfigMap',
            'metadata' => [
                'name' => $serviceName . '-config',
                'labels' => [
                    'app' => $serviceName
                ]
            ],
            'data' => $config
        ];
    }
    
    /**
     * Generate secret
     */
    public function generateSecret(string $serviceName, array $secrets): array
    {
        $encodedSecrets = [];
        
        foreach ($secrets as $key => $value) {
            $encodedSecrets[$key] = base64_encode($value);
        }
        
        return [
            'apiVersion' => 'v1',
            'kind' => 'Secret',
            'metadata' => [
                'name' => $serviceName . '-secrets',
                'labels' => [
                    'app' => $serviceName
                ]
            ],
            'type' => 'Opaque',
            'data' => $encodedSecrets
        ];
    }
    
    /**
     * Generate service mesh configuration
     */
    public function generateServiceMeshConfig(string $serviceName): array
    {
        return [
            'apiVersion' => 'networking.istio.io/v1alpha3',
            'kind' => 'VirtualService',
            'metadata' => [
                'name' => $serviceName,
                'namespace' => 'default'
            ],
            'spec' => [
                'hosts' => [$serviceName],
                'http' => [
                    [
                        'match' => [
                            [
                                'uri' => [
                                    'prefix' => '/api'
                                ]
                            ]
                        ],
                        'fault' => [
                            'delay' => [
                                'percentage' => [
                                    'value' => 0.1
                                ],
                                'fixedDelay' => '5s'
                            ]
                        ],
                        'route' => [
                            [
                                'destination' => [
                                    'host' => $serviceName,
                                    'subset' => 'v1'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Get services
     */
    public function getServices(): array
    {
        return $this->services;
    }
    
    /**
     * Get configurations
     */
    public function getConfigurations(): array
    {
        return $this->configurations;
    }
}

// Service Mesh Manager
class ServiceMeshManager
{
    private array $services = [];
    private array $trafficRules = [];
    private array 'securityPolicies = [];
    
    public function __construct()
    {
        $this->initializeServices();
    }
    
    /**
     * Initialize service mesh services
     */
    private function initializeServices(): void
    {
        $this->services = [
            'user-service' => [
                'name' => 'user-service',
                'version' => 'v1',
                'port' => 8080,
                'endpoints' => [
                    '/api/users',
                    '/api/users/{id}',
                    '/health'
                ],
                'dependencies' => ['database-service'],
                'circuitBreaker' => [
                    'enabled' => true,
                    'failureThreshold' => 5,
                    'timeout' => 30
                ],
                'retry' => [
                    'enabled' => true,
                    'attempts' => 3,
                    'backoff' => 'exponential'
                ]
            ],
            'order-service' => [
                'name' => 'order-service',
                'version' => 'v1',
                'port' => 8081,
                'endpoints' => [
                    '/api/orders',
                    '/api/orders/{id}',
                    '/health'
                ],
                'dependencies' => ['user-service', 'payment-service'],
                'circuitBreaker' => [
                    'enabled' => true,
                    'failureThreshold' => 3,
                    'timeout' => 15
                ],
                'retry' => [
                    'enabled' => true,
                    'attempts' => 2,
                    'backoff' => 'linear'
                ]
            ],
            'payment-service' => [
                'name' => 'payment-service',
                'version' => 'v1',
                'port' => 8082,
                'endpoints' => [
                    '/api/payments',
                    '/api/payments/{id}',
                    '/health'
                ],
                'dependencies' => ['database-service'],
                'circuitBreaker' => [
                    'enabled' => true,
                    'failureThreshold' => 2,
                    'timeout' => 10
                ],
                'retry' => [
                    'enabled' => false,
                    'attempts' => 1,
                    'backoff' => 'none'
                ]
            ]
        ];
    }
    
    /**
     * Add traffic rule
     */
    public function addTrafficRule(string $serviceName, array $rule): void
    {
        if (!isset($this->trafficRules[$serviceName])) {
            $this->trafficRules[$serviceName] = [];
        }
        
        $this->trafficRules[$serviceName][] = $rule;
    }
    
    /**
     * Generate virtual service
     */
    public function generateVirtualService(string $serviceName): array
    {
        if (!isset($this->services[$serviceName])) {
            return ['error' => 'Service not found'];
        }
        
        $service = $this->services[$serviceName];
        $routes = [];
        
        foreach ($service['endpoints'] as $endpoint) {
            $routes[] = [
                'match' => [
                    [
                        'uri' => [
                            'prefix' => $endpoint
                        ]
                    ]
                ],
                'route' => [
                    [
                        'destination' => [
                            'host' => $serviceName,
                            'subset' => $service['version']
                        ],
                        'weight' => 100
                    ]
                ]
            ];
        }
        
        return [
            'apiVersion' => 'networking.istio.io/v1alpha3',
            'kind' => 'VirtualService',
            'metadata' => [
                'name' => $serviceName,
                'namespace' => 'default'
            ],
            'spec' => [
                'hosts' => [$serviceName],
                'http' => $routes
            ]
        ];
    }
    
    /**
     * Generate destination rule
     */
    public function generateDestinationRule(string $serviceName): array
    {
        if (!isset($this->services[$serviceName])) {
            return ['error' => 'Service not found'];
        }
        
        $service = $this->services[$serviceName];
        
        return [
            'apiVersion' => 'networking.istio.io/v1alpha3',
            'kind' => 'DestinationRule',
            'metadata' => [
                'name' => $serviceName,
                'namespace' => 'default'
            ],
            'spec' => [
                'host' => $serviceName,
                'trafficPolicy' => [
                    'connectionPool' => [
                        'tcp' => [
                            'maxConnections' => 100
                        ],
                        'http' => [
                            'http1MaxPendingRequests' => 50,
                            'maxRequestsPerConnection' => 10
                        ]
                    ],
                    'loadBalancer' => [
                        'simple' => 'LEAST_CONN'
                    ],
                    'circuitBreaker' => [
                        'consecutiveErrors' => $service['circuitBreaker']['failureThreshold'],
                        'interval' => '30s',
                        'baseEjectionTime' => '30s'
                    ]
                ],
                'subsets' => [
                    [
                        'name' => $service['version'],
                        'labels' => [
                            'version' => $service['version']
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Generate gateway
     */
    public function generateGateway(): array
    {
        return [
            'apiVersion' => 'networking.istio.io/v1alpha3',
            'kind' => 'Gateway',
            'metadata' => [
                'name' => 'microservices-gateway',
                'namespace' => 'default'
            ],
            'spec' => [
                'selector' => [
                    'istio' => 'ingressgateway'
                ],
                'servers' => [
                    [
                        'port' => [
                            'number' => 80,
                            'name' => 'http',
                            'protocol' => 'HTTP'
                        ],
                        'hosts' => ['*']
                    ],
                    [
                        'port' => [
                            'number' => 443,
                            'name' => 'https',
                            'protocol' => 'HTTPS'
                        ],
                        'tls' => [
                            'mode' => 'SIMPLE',
                            'credentialName' => 'microservices-tls'
                        ],
                        'hosts' => ['*']
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Generate service mesh policies
     */
    public function generateSecurityPolicies(): array
    {
        return [
            'authentication_policy' => [
                'apiVersion' => 'security.istio.io/v1beta1',
                'kind' => 'RequestAuthentication',
                'metadata' => [
                    'name' => 'jwt-auth',
                    'namespace' => 'default'
                ],
                'spec' => [
                    'selector' => [
                        'matchLabels' => [
                            'app' => 'api-gateway'
                        ]
                    ],
                    'jwtRules' => [
                        [
                            'issuer' => 'https://auth.example.com',
                            'jwksUri' => 'https://auth.example.com/.well-known/jwks.json',
                            'forwardOriginalToken' => true
                        ]
                    ]
                ]
            ],
            'authorization_policy' => [
                'apiVersion' => 'security.istio.io/v1beta1',
                'kind' => 'AuthorizationPolicy',
                'metadata' => [
                    'name' => 'allow-all',
                    'namespace' => 'default'
                ],
                'spec' => [
                    'selector' => [
                        'matchLabels' => [
                            'app' => 'api-gateway'
                        ]
                    ],
                    'rules' => [
                        [
                            'from' => [
                                [
                                    'source' => [
                                        'requestPrincipals' => [
                                            [
                                                'issuer' => 'https://auth.example.com'
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'to' => [
                                [
                                    'operation' => [
                                        'methods' => ['GET', 'POST', 'PUT', 'DELETE']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'peer_authentication' => [
                'apiVersion' => 'security.istio.io/v1beta1',
                'kind' => 'PeerAuthentication',
                'metadata' => [
                    'name' => 'default',
                    'namespace' => 'default'
                ],
                'spec' => [
                    'mtls' => [
                        'mode' => 'STRICT'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Get services
     */
    public function getServices(): array
    {
        return $this->services;
    }
    
    /**
     * Get traffic rules
     */
    public function getTrafficRules(): array
    {
        return $this->trafficRules;
    }
}

// Cloud Native Monitoring
class CloudNativeMonitoring
{
    private array $metrics = [];
    private array 'alerts = [];
    private array 'dashboards = [];
    
    public function __construct()
    {
        $this->initializeMetrics();
    }
    
    /**
     * Initialize monitoring metrics
     */
    private function initializeMetrics(): void
    {
        $this->metrics = [
            'request_count' => [
                'name' => 'http_requests_total',
                'type' => 'counter',
                'description' => 'Total number of HTTP requests',
                'labels' => ['method', 'endpoint', 'status_code']
            ],
            'request_duration' => [
                'name' => 'http_request_duration_seconds',
                'type' => 'histogram',
                'description' => 'HTTP request duration in seconds',
                'labels' => ['method', 'endpoint']
            ],
            'error_rate' => [
                'name' => 'http_errors_total',
                'type' => 'counter',
                'description' => 'Total number of HTTP errors',
                'labels' => ['method', 'endpoint', 'error_type']
            ],
            'active_connections' => [
                'name' => 'active_connections',
                'type' => 'gauge',
                'description' => 'Number of active connections',
                'labels' => ['service']
            ],
            'memory_usage' => [
                'name' => 'memory_usage_bytes',
                'type' => 'gauge',
                'description' => 'Memory usage in bytes',
                'labels' => ['service', 'instance']
            ],
            'cpu_usage' => [
                'name' => 'cpu_usage_percent',
                'type' => 'gauge',
                'description' => 'CPU usage percentage',
                'labels' => ['service', 'instance']
            ]
        ];
    }
    
    /**
     * Add custom metric
     */
    public function addMetric(string $name, array $config): void
    {
        $this->metrics[$name] = $config;
    }
    
    /**
     * Generate Prometheus configuration
     */
    public function generatePrometheusConfig(): array
    {
        $scrapeConfigs = [];
        
        foreach ($this->metrics as $metric) {
            $scrapeConfigs[] = [
                'job_name' => $metric['name'],
                'static_configs' => [
                    [
                        'targets' => [
                            'localhost:8080',
                            'localhost:8081',
                            'localhost:8082'
                        ],
                        'labels' => [
                            'environment' => 'production',
                            'service' => 'microservices'
                        ]
                    ]
                ],
                'metrics_path' => '/metrics',
                'scrape_interval' => '15s',
                'scrape_timeout' => '10s'
            ];
        }
        
        return [
            'global' => [
                'scrape_interval' => '15s',
                'evaluation_interval' => '15s'
            ],
            'rule_files' => [
                'rules/*.yml'
            ],
            'scrape_configs' => $scrapeConfigs,
            'alerting' => [
                'alertmanagers' => [
                    [
                        'static_configs' => [
                            [
                                'targets' => ['alertmanager:9093']
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Generate Grafana dashboard
     */
    public function generateGrafanaDashboard(): array
    {
        return [
            'dashboard' => [
                'title' => 'Microservices Dashboard',
                'tags' => ['microservices', 'production'],
                'timezone' => 'browser',
                'panels' => [
                    [
                        'title' => 'Request Rate',
                        'type' => 'graph',
                        'targets' => [
                            [
                                'expr' => 'rate(http_requests_total[5m])',
                                'legendFormat' => '{{method}} {{endpoint}}'
                            ]
                        ],
                        'gridPos' => [
                            'h' => 8,
                            'w' => 12,
                            'x' => 0,
                            'y' => 0
                        ]
                    ],
                    [
                        'title' => 'Request Duration',
                        'type' => 'graph',
                        'targets' => [
                            [
                                'expr' => 'histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m]))',
                                'legendFormat' => '95th percentile'
                            ],
                            [
                                'expr' => 'histogram_quantile(0.50, rate(http_request_duration_seconds_bucket[5m]))',
                                'legendFormat' => '50th percentile'
                            ]
                        ],
                        'gridPos' => [
                            'h' => 8,
                            'w' => 12,
                            'x' => 12,
                            'y' => 0
                        ]
                    ],
                    [
                        'title' => 'Error Rate',
                        'type' => 'graph',
                        'targets' => [
                            [
                                'expr' => 'rate(http_errors_total[5m]) / rate(http_requests_total[5m]) * 100',
                                'legendFormat' => 'Error Rate %'
                            ]
                        ],
                        'gridPos' => [
                            'h' => 8,
                            'w' => 12,
                            'x' => 0,
                            'y' => 8
                        ]
                    ],
                    [
                        'title' => 'Memory Usage',
                        'type' => 'graph',
                        'targets' => [
                            [
                                'expr' => 'memory_usage_bytes / 1024 / 1024',
                                'legendFormat' => '{{service}}'
                            ]
                        ],
                        'gridPos' => [
                            'h' => 8,
                            'w' => 12,
                            'x' => 12,
                            'y' => 8
                        ]
                    ]
                ],
                'time' => [
                    'from' => 'now-1h',
                    'to' => 'now'
                ],
                'refresh' => '30s'
            ]
        ];
    }
    
    /**
     * Generate alert rules
     */
    public function generateAlertRules(): array
    {
        return [
            'groups' => [
                [
                    'name' => 'microservices.rules',
                    'rules' => [
                        [
                            'alert' => 'HighErrorRate',
                            'expr' => 'rate(http_errors_total[5m]) / rate(http_requests_total[5m]) * 100 > 5',
                            'for' => '5m',
                            'labels' => [
                                'severity' => 'warning'
                            ],
                            'annotations' => [
                                'summary' => 'High error rate detected',
                                'description' => 'Error rate is {{ $value | printf "%.2f" }}% for {{ $labels.service }}'
                            ]
                        ],
                        [
                            'alert' => 'HighLatency',
                            'expr' => 'histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m])) > 1',
                            'for' => '5m',
                            'labels' => [
                                'severity' => 'warning'
                            ],
                            'annotations' => [
                                'summary' => 'High latency detected',
                                'description' => '95th percentile latency is {{ $value }}s for {{ $labels.service }}'
                            ]
                        ],
                        [
                            'alert' => 'ServiceDown',
                            'expr' => 'up == 0',
                            'for' => '1m',
                            'labels' => [
                                'severity' => 'critical'
                            ],
                            'annotations' => [
                                'summary' => 'Service is down',
                                'description' => 'Service {{ $labels.service }} has been down for more than 1 minute'
                            ]
                        ],
                        [
                            'alert' => 'HighMemoryUsage',
                            'expr' => 'memory_usage_bytes / 1024 / 1024 / 1024 > 1',
                            'for' => '10m',
                            'labels' => [
                                'severity' => 'warning'
                            ],
                            'annotations' => [
                                'summary' => 'High memory usage',
                                'description' => 'Memory usage is {{ $value | printf "%.2f" }}GB for {{ $labels.service }}'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Generate distributed tracing configuration
     */
    public function generateTracingConfig(): array
    {
        return [
            'jaeger' => [
                'collector' => [
                    'host' => 'jaeger-collector',
                    'port' => 14267,
                    'grpc' => [
                        'host' => 'jaeger-collector',
                        'port' => 14250
                    ]
                ],
                'query' => [
                    'host' => 'jaeger-query',
                    'port' => 16686
                ],
                'agent' => [
                    'host' => 'jaeger-agent',
                    'port' => 6831
                ]
            ],
            'sampling' => [
                'type' => 'probabilistic',
                'param' => 0.1
            ],
            'services' => [
                'user-service' => [
                    'host' => 'user-service',
                    'port' => 8080,
                    'tracing' => [
                        'enabled' => true,
                        'sampling_rate' => 0.1
                    ]
                ],
                'order-service' => [
                    'host' => 'order-service',
                    'port' => 8081,
                    'tracing' => [
                        'enabled' => true,
                        'sampling_rate' => 0.1
                    ]
                ],
                'payment-service' => [
                    'host' => 'payment-service',
                    'port' => 8082,
                    'tracing' => [
                        'enabled' => true,
                        'sampling_rate' => 0.1
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Get metrics
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }
    
    /**
     * Get alerts
     */
    public function getAlerts(): array
    {
        return $this->alerts;
    }
    
    /**
     * Get dashboards
     */
    public function getDashboards(): array
    {
        return $this->dashboards;
    }
}

// Cloud Native Examples
class CloudNativeExamples
{
    private CloudNativeAppBuilder $appBuilder;
    private ServiceMeshManager $serviceMesh;
    private CloudNativeMonitoring $monitoring;
    
    public function __construct()
    {
        $this->appBuilder = new CloudNativeAppBuilder();
        $this->serviceMesh = new ServiceMeshManager();
        $this->monitoring = new CloudNativeMonitoring();
        
        $this->setupSampleApplication();
    }
    
    /**
     * Setup sample cloud-native application
     */
    private function setupSampleApplication(): void
    {
        // Add microservices
        $this->appBuilder->addMicroservice('user-service', [
            'image' => 'php:8.2-apache',
            'replicas' => 3,
            'port' => 8080,
            'dependencies' => ['database-service'],
            'endpoints' => ['/api/users', '/api/users/{id}', '/health']
        ]);
        
        $this->appBuilder->addMicroservice('order-service', [
            'image' => 'php:8.2-apache',
            'replicas' => 2,
            'port' => 8081,
            'dependencies' => ['user-service', 'payment-service'],
            'endpoints' => ['/api/orders', '/api/orders/{id}', '/health']
        ]);
        
        $this->appBuilder->addMicroservice('payment-service', [
            'image' => 'php:8.2-apache',
            'replicas' => 2,
            'port' => 8082,
            'dependencies' => ['database-service'],
            'endpoints' => ['/api/payments', '/api/payments/{id}', '/health']
        ]);
    }
    
    public function demonstrateKubernetesDeployment(): void
    {
        echo "Kubernetes Deployment Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Generate deployments
        echo "Generating Kubernetes Deployments:\n";
        $services = $this->appBuilder->getServices();
        
        foreach ($services as $serviceName => $service) {
            echo "\n$serviceName:\n";
            
            // Deployment
            $deployment = $this->appBuilder->generateK8sDeployment($serviceName);
            echo "  Deployment: {$deployment['metadata']['name']}\n";
            echo "  Replicas: {$deployment['spec']['replicas']}\n";
            echo "  Image: {$deployment['spec']['template']['spec']['containers'][0]['image']}\n";
            
            // Service
            $k8sService = $this->appBuilder->generateK8sService($serviceName);
            echo "  Service: {$k8sService['metadata']['name']}\n";
            echo "  Type: {$k8sService['spec']['type']}\n";
            
            // HPA
            $hpa = $this->appBuilder->generateHPA($serviceName);
            echo "  HPA: {$hpa['metadata']['name']}\n";
            echo "  Min Replicas: {$hpa['spec']['minReplicas']}\n";
            echo "  Max Replicas: {$hpa['spec']['maxReplicas']}\n";
        }
        
        // Config maps and secrets
        echo "\nConfig Maps and Secrets:\n";
        $configMap = $this->appBuilder->generateConfigMap('user-service', [
            'database_host' => 'mysql.default.svc.cluster.local',
            'database_name' => 'userdb',
            'log_level' => 'info'
        ]);
        
        $secret = $this->appBuilder->generateSecret('user-service', [
            'database_password' => 'secure_password_123',
            'api_key' => 'api_key_456'
        ]);
        
        echo "Config Map: {$configMap['metadata']['name']}\n";
        echo "Secret: {$secret['metadata']['name']}\n";
    }
    
    public function demonstrateServiceMesh(): void
    {
        echo "\nService Mesh Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Generate service mesh configurations
        echo "Generating Service Mesh Configurations:\n";
        $services = $this->serviceMesh->getServices();
        
        foreach ($services as $serviceName => $service) {
            echo "\n$serviceName:\n";
            
            // Virtual Service
            $virtualService = $this->serviceMesh->generateVirtualService($serviceName);
            echo "  Virtual Service: {$virtualService['metadata']['name']}\n";
            echo "  Hosts: " . implode(', ', $virtualService['spec']['hosts']) . "\n";
            
            // Destination Rule
            $destinationRule = $this->serviceMesh->generateDestinationRule($serviceName);
            echo "  Destination Rule: {$destinationRule['metadata']['name']}\n";
            echo "  Load Balancer: {$destinationRule['spec']['trafficPolicy']['loadBalancer']['simple']}\n";
        }
        
        // Gateway
        echo "\nGateway:\n";
        $gateway = $this->serviceMesh->generateGateway();
        echo "  Gateway: {$gateway['metadata']['name']}\n";
        echo "  Ports: " . implode(', ', array_column($gateway['spec']['servers'], 'port')) . "\n";
        
        // Security policies
        echo "\nSecurity Policies:\n";
        $policies = $this->serviceMesh->generateSecurityPolicies();
        foreach ($policies as $policyType => $policy) {
            echo "  $policyType: {$policy['metadata']['name']}\n";
        }
    }
    
    public function demonstrateMonitoring(): void
    {
        echo "\nCloud Native Monitoring Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Prometheus configuration
        echo "Prometheus Configuration:\n";
        $prometheusConfig = $this->monitoring->generatePrometheusConfig();
        echo "  Scrape Jobs: " . count($prometheusConfig['scrape_configs']) . "\n";
        echo "  Evaluation Interval: {$prometheusConfig['global']['evaluation_interval']}\n";
        echo "  Alert Manager: " . $prometheusConfig['alerting']['alertmanagers'][0]['static_configs'][0]['targets'][0] . "\n";
        
        // Grafana dashboard
        echo "\nGrafana Dashboard:\n";
        $dashboard = $this->monitoring->generateGrafanaDashboard();
        echo "  Title: {$dashboard['dashboard']['title']}\n";
        echo "  Panels: " . count($dashboard['dashboard']['panels']) . "\n";
        echo "  Refresh: {$dashboard['dashboard']['refresh']}\n";
        
        // Alert rules
        echo "\nAlert Rules:\n";
        $alertRules = $this->monitoring->generateAlertRules();
        foreach ($alertRules['groups'][0]['rules'] as $rule) {
            echo "  {$rule['alert']} ({$rule['labels']['severity']})\n";
            echo "    Expression: {$rule['expr']}\n";
            echo "    For: {$rule['for']}\n";
        }
        
        // Tracing configuration
        echo "\nDistributed Tracing:\n";
        $tracingConfig = $this->monitoring->generateTracingConfig();
        echo "  Jaeger Collector: {$tracingConfig['jaeger']['collector']['host']}:{$tracingConfig['jaeger']['collector']['port']}\n";
        echo "  Sampling Rate: {$tracingConfig['sampling']['param']}\n";
        echo "  Traced Services: " . implode(', ', array_keys($tracingConfig['services'])) . "\n";
    }
    
    public function demonstrateResiliencePatterns(): void
    {
        echo "\nResilience Patterns Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "Circuit Breaker Pattern:\n";
        echo "  • Automatically fails fast when a service is down\n";
        echo "  • Prevents cascading failures\n";
        echo "  • Provides fallback mechanisms\n";
        echo "  • Configurable failure thresholds\n\n";
        
        echo "Retry Pattern:\n";
        echo "  • Exponential backoff retry strategy\n";
        echo "  • Configurable retry attempts\n";
        echo "  • Idempotent operations only\n";
        echo "  • Timeout handling\n\n";
        
        echo "Bulkhead Pattern:\n";
        echo "  • Isolates resources for different services\n";
        echo "  • Prevents resource exhaustion\n";
        echo "  • Thread pool isolation\n";
        echo "  • Semaphore-based limiting\n\n";
        
        echo "Timeout Pattern:\n";
        echo "  • Configurable timeouts for operations\n";
        echo "  • Prevents hanging requests\n";
        echo "  • Graceful degradation\n";
        echo "  • Timeout propagation\n\n";
        
        echo "Fallback Pattern:\n";
        echo "  • Alternative responses when services fail\n";
        echo "  • Cached data fallbacks\n";
        echo "  • Default value fallbacks\n";
        echo "  • Graceful error handling\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nCloud Native Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Microservices Design:\n";
        echo "   • Single responsibility principle\n";
        echo "   • Bounded contexts\n";
        echo "   • API-first design\n";
        echo "   • Event-driven architecture\n";
        echo "   • Loose coupling\n\n";
        
        echo "2. Containerization:\n";
        echo "   • Small, focused containers\n";
        echo "   • Immutable infrastructure\n";
        echo "   • Multi-stage builds\n";
        echo "   • Security scanning\n";
        echo "   • Health checks\n\n";
        
        echo "3. Kubernetes Deployment:\n";
        echo "   • Declarative configuration\n";
        echo "   • Resource limits and requests\n";
        echo "   • Liveness and readiness probes\n";
        echo "   • Graceful shutdown\n";
        echo "   • Rolling updates\n\n";
        
        echo "4. Service Mesh:\n";
        echo "   • Centralized traffic management\n";
        echo "   • Security policies\n";
        echo "   • Observability\n";
        echo "   • Resilience patterns\n";
        echo "   • Traffic splitting\n\n";
        
        echo "5. Monitoring and Observability:\n";
        echo "   • Structured logging\n";
        echo "   • Metrics collection\n";
        echo "   • Distributed tracing\n";
        echo "   • Alerting\n";
        echo "   • Dashboards\n\n";
        
        echo "6. Security:\n";
        echo "   • Zero-trust networking\n";
        echo "   • Secrets management\n";
        echo "   • Network policies\n";
        echo "   • RBAC\n";
        echo "   • Compliance";
    }
    
    public function runAllExamples(): void
    {
        echo "Cloud Native Applications Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateKubernetesDeployment();
        $this->demonstrateServiceMesh();
        $this->demonstrateMonitoring();
        $this->demonstrateResiliencePatterns();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runCloudNativeAppsDemo(): void
{
    $examples = new CloudNativeExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCloudNativeAppsDemo();
}
?>
