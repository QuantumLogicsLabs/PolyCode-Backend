<?php
/**
 * Monitoring and Debugging in Microservices
 * 
 * Comprehensive monitoring, logging, and debugging strategies for microservices.
 */

// Distributed Tracing
class DistributedTracing
{
    private array $traces = [];
    private array $spans = [];
    private array $services = [];
    
    public function __construct()
    {
        $this->initializeServices();
    }
    
    /**
     * Initialize services for tracing
     */
    private function initializeServices(): void
    {
        $this->services = [
            'user-service' => [
                'name' => 'User Service',
                'version' => '1.0.0',
                'dependencies' => ['database', 'cache']
            ],
            'order-service' => [
                'name' => 'Order Service',
                'version' => '1.1.0',
                'dependencies' => ['user-service', 'product-service', 'payment-service']
            ],
            'product-service' => [
                'name' => 'Product Service',
                'version' => '2.0.0',
                'dependencies' => ['database', 'cache']
            ],
            'notification-service' => [
                'name' => 'Notification Service',
                'version' => '1.0.0',
                'dependencies' => ['message-queue']
            ],
            'payment-service' => [
                'name' => 'Payment Service',
                'version' => '1.0.0',
                'dependencies' => ['payment-gateway', 'database']
            ]
        ];
    }
    
    /**
     * Create trace
     */
    public function createTrace(string $operation, array $tags = []): string
    {
        $traceId = uniqid('trace_');
        
        $trace = [
            'trace_id' => $traceId,
            'operation' => $operation,
            'started_at' => microtime(true),
            'tags' => array_merge([
                'service.name' => 'api-gateway',
                'service.version' => '1.0.0'
            ], $tags),
            'spans' => [],
            'status' => 'active'
        ];
        
        $this->traces[$traceId] = $trace;
        
        return $traceId;
    }
    
    /**
     * Create span
     */
    public function createSpan(string $traceId, string $operation, string $serviceName, array $tags = []): string
    {
        $spanId = uniqid('span_');
        
        $span = [
            'span_id' => $spanId,
            'trace_id' => $traceId,
            'parent_span_id' => null,
            'operation' => $operation,
            'service' => $serviceName,
            'started_at' => microtime(true),
            'finished_at' => null,
            'duration' => null,
            'tags' => array_merge([
                'service.name' => $serviceName,
                'service.version' => $this->services[$serviceName]['version'] ?? 'unknown'
            ], $tags),
            'status' => 'active',
            'logs' => []
        ];
        
        $this->spans[$spanId] = $span;
        $this->traces[$traceId]['spans'][] = $spanId;
        
        return $spanId;
    }
    
    /**
     * Create child span
     */
    public function createChildSpan(string $parentSpanId, string $operation, string $serviceName, array $tags = []): string
    {
        $parentSpan = $this->spans[$parentSpanId];
        $traceId = $parentSpan['trace_id'];
        
        $spanId = uniqid('span_');
        
        $span = [
            'span_id' => $spanId,
            'trace_id' => $traceId,
            'parent_span_id' => $parentSpanId,
            'operation' => $operation,
            'service' => $serviceName,
            'started_at' => microtime(true),
            'finished_at' => null,
            'duration' => null,
            'tags' => array_merge([
                'service.name' => $serviceName,
                'service.version' => $this->services[$serviceName]['version'] ?? 'unknown'
            ], $tags),
            'status' => 'active',
            'logs' => []
        ];
        
        $this->spans[$spanId] = $span;
        $this->traces[$traceId]['spans'][] = $spanId;
        
        return $spanId;
    }
    
    /**
     * Finish span
     */
    public function finishSpan(string $spanId, array $tags = []): void
    {
        if (!isset($this->spans[$spanId])) {
            return;
        }
        
        $this->spans[$spanId]['finished_at'] = microtime(true);
        $this->spans[$spanId]['duration'] = $this->spans[$spanId]['finished_at'] - $this->spans[$spanId]['started_at'];
        $this->spans[$spanId]['status'] = 'completed';
        $this->spans[$spanId]['tags'] = array_merge($this->spans[$spanId]['tags'], $tags);
    }
    
    /**
     * Add log to span
     */
    public function addLog(string $spanId, string $level, string $message, array $context = []): void
    {
        if (!isset($this->spans[$spanId])) {
            return;
        }
        
        $log = [
            'timestamp' => microtime(true),
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];
        
        $this->spans[$spanId]['logs'][] = $log;
    }
    
    /**
     * Add error to span
     */
    public function addError(string $spanId, string $message, array $context = []): void
    {
        $this->addLog($spanId, 'ERROR', $message, $context);
        $this->spans[$spanId]['status'] = 'error';
        $this->spans[$spanId]['tags']['error'] = true;
    }
    
    /**
     * Finish trace
     */
    public function finishTrace(string $traceId): void
    {
        if (!isset($this->traces[$traceId])) {
            return;
        }
        
        $this->traces[$traceId]['finished_at'] = microtime(true);
        $this->traces[$traceId]['duration'] = $this->traces[$traceId]['finished_at'] - $this->traces[$traceId]['started_at'];
        $this->traces[$traceId]['status'] = 'completed';
    }
    
    /**
     * Get trace
     */
    public function getTrace(string $traceId): ?array
    {
        return $this->traces[$traceId] ?? null;
    }
    
    /**
     * Get span
     */
    public function getSpan(string $spanId): ?array
    {
        return $this->spans[$spanId] ?? null;
    }
    
    /**
     * Get all traces
     */
    public function getAllTraces(): array
    {
        return $this->traces;
    }
    
    /**
     * Get all spans
     */
    public function getAllSpans(): array
    {
        return $this->spans;
    }
}

// Metrics Collection
class MetricsCollection
{
    private array $counters = [];
    private array $gauges = [];
    private array $histograms = [];
    private array $timers = [];
    
    public function __construct()
    {
        $this->initializeMetrics();
    }
    
    /**
     * Initialize default metrics
     */
    private function initializeMetrics(): void
    {
        // Counters
        $this->counters = [
            'http_requests_total' => [
                'name' => 'http_requests_total',
                'description' => 'Total number of HTTP requests',
                'labels' => ['method', 'status', 'service'],
                'value' => 0
            ],
            'database_queries_total' => [
                'name' => 'database_queries_total',
                'description' => 'Total number of database queries',
                'labels' => ['operation', 'table', 'status'],
                'value' => 0
            ],
            'cache_operations_total' => [
                'name' => 'cache_operations_total',
                'description' => 'Total number of cache operations',
                'labels' => ['operation', 'status'],
                'value' => 0
            ]
        ];
        
        // Gauges
        $this->gauges = [
            'active_connections' => [
                'name' => 'active_connections',
                'description' => 'Number of active connections',
                'labels' => ['service'],
                'value' => 0
            ],
            'memory_usage_bytes' => [
                'name' => 'memory_usage_bytes',
                'description' => 'Current memory usage in bytes',
                'labels' => ['service'],
                'value' => 0
            ],
            'cpu_usage_percent' => [
                'name' => 'cpu_usage_percent',
                'description' => 'Current CPU usage percentage',
                'labels' => ['service'],
                'value' => 0
            ]
        ];
        
        // Histograms
        $this->histograms = [
            'http_request_duration_seconds' => [
                'name' => 'http_request_duration_seconds',
                'description' => 'HTTP request duration in seconds',
                'labels' => ['method', 'endpoint', 'service'],
                'buckets' => [0.001, 0.005, 0.01, 0.05, 0.1, 0.5, 1.0, 5.0],
                'observations' => []
            ],
            'database_query_duration_seconds' => [
                'name' => 'database_query_duration_seconds',
                'description' => 'Database query duration in seconds',
                'labels' => ['operation', 'table', 'service'],
                'buckets' => [0.001, 0.005, 0.01, 0.05, 0.1, 0.5, 1.0],
                'observations' => []
            ]
        ];
    }
    
    /**
     * Increment counter
     */
    public function incrementCounter(string $name, array $labels = [], float $value = 1): void
    {
        if (!isset($this->counters[$name])) {
            return;
        }
        
        $key = $this->getLabelKey($labels);
        
        if (!isset($this->counters[$name]['values'][$key])) {
            $this->counters[$name]['values'][$key] = 0;
        }
        
        $this->counters[$name]['values'][$key] += $value;
        $this->counters[$name]['value'] += $value;
    }
    
    /**
     * Set gauge value
     */
    public function setGauge(string $name, float $value, array $labels = []): void
    {
        if (!isset($this->gauges[$name])) {
            return;
        }
        
        $key = $this->getLabelKey($labels);
        $this->gauges[$name]['values'][$key] = $value;
        $this->gauges[$name]['value'] = $value;
    }
    
    /**
     * Observe histogram
     */
    public function observeHistogram(string $name, float $value, array $labels = []): void
    {
        if (!isset($this->histograms[$name])) {
            return;
        }
        
        $key = $this->getLabelKey($labels);
        
        if (!isset($this->histograms[$name]['observations'][$key])) {
            $this->histograms[$name]['observations'][$key] = [];
        }
        
        $this->histograms[$name]['observations'][$key][] = $value;
    }
    
    /**
     * Start timer
     */
    public function startTimer(string $name, array $labels = []): string
    {
        $timerId = uniqid('timer_');
        
        $this->timers[$timerId] = [
            'name' => $name,
            'labels' => $labels,
            'started_at' => microtime(true)
        ];
        
        return $timerId;
    }
    
    /**
     * Stop timer and record histogram
     */
    public function stopTimer(string $timerId): void
    {
        if (!isset($this->timers[$timerId])) {
            return;
        }
        
        $timer = $this->timers[$timerId];
        $duration = microtime(true) - $timer['started_at'];
        
        $this->observeHistogram($timer['name'], $duration, $timer['labels']);
        
        unset($this->timers[$timerId]);
    }
    
    /**
     * Get label key
     */
    private function getLabelKey(array $labels): string
    {
        ksort($labels);
        return json_encode($labels);
    }
    
    /**
     * Get metrics in Prometheus format
     */
    public function getPrometheusMetrics(): string
    {
        $output = '';
        
        // Counters
        foreach ($this->counters as $counter) {
            $output .= "# HELP {$counter['name']} {$counter['description']}\n";
            $output .= "# TYPE {$counter['name']} counter\n";
            
            if (isset($counter['values'])) {
                foreach ($counter['values'] as $labels => $value) {
                    $labelStr = $this->formatLabels($labels);
                    $output .= "{$counter['name']}$labelStr $value\n";
                }
            } else {
                $output .= "{$counter['name']} {$counter['value']}\n";
            }
        }
        
        // Gauges
        foreach ($this->gauges as $gauge) {
            $output .= "# HELP {$gauge['name']} {$gauge['description']}\n";
            $output .= "# TYPE {$gauge['name']} gauge\n";
            
            if (isset($gauge['values'])) {
                foreach ($gauge['values'] as $labels => $value) {
                    $labelStr = $this->formatLabels($labels);
                    $output .= "{$gauge['name']}$labelStr $value\n";
                }
            } else {
                $output .= "{$gauge['name']} {$gauge['value']}\n";
            }
        }
        
        // Histograms
        foreach ($this->histograms as $histogram) {
            $output .= "# HELP {$histogram['name']} {$histogram['description']}\n";
            $output .= "# TYPE {$histogram['name']} histogram\n";
            
            foreach ($histogram['observations'] as $labels => $observations) {
                $labelStr = $this->formatLabels($labels);
                
                // Calculate bucket counts
                $bucketCounts = [];
                foreach ($histogram['buckets'] as $bucket) {
                    $count = count(array_filter($observations, function($obs) use ($bucket) {
                        return $obs <= $bucket;
                    }));
                    $bucketCounts[$bucket] = $count;
                }
                
                // Output buckets
                foreach ($bucketCounts as $bucket => $count) {
                    $bucketLabels = json_decode($labels, true) ?: [];
                    $bucketLabels['le'] = $bucket;
                    $bucketLabelStr = $this->formatLabels(json_encode($bucketLabels));
                    $output .= "{$histogram['name']}_bucket$bucketLabelStr $count\n";
                }
                
                // Output total count
                $output .= "{$histogram['name']}_count$labelStr " . count($observations) . "\n";
                
                // Output sum
                $sum = array_sum($observations);
                $output .= "{$histogram['name']}_sum$labelStr $sum\n";
            }
        }
        
        return $output;
    }
    
    /**
     * Format labels for Prometheus
     */
    private function formatLabels(string $labelsJson): string
    {
        if ($labelsJson === '[]') {
            return '';
        }
        
        $labels = json_decode($labelsJson, true);
        if (!$labels) {
            return '';
        }
        
        $pairs = [];
        foreach ($labels as $key => $value) {
            $pairs[] = "$key=\"$value\"";
        }
        
        return '{' . implode(',', $pairs) . '}';
    }
    
    /**
     * Get all metrics
     */
    public function getAllMetrics(): array
    {
        return [
            'counters' => $this->counters,
            'gauges' => $this->gauges,
            'histograms' => $this->histograms
        ];
    }
}

// Logging System
class LoggingSystem
{
    private array $loggers = [];
    private array $logLevels = ['DEBUG', 'INFO', 'WARN', 'ERROR', 'FATAL'];
    private array $handlers = [];
    
    public function __construct()
    {
        $this->initializeLoggers();
        $this->setupHandlers();
    }
    
    /**
     * Initialize loggers for different services
     */
    private function initializeLoggers(): void
    {
        $this->loggers = [
            'user-service' => [
                'name' => 'user-service',
                'level' => 'INFO',
                'handlers' => ['file', 'console'],
                'context' => [
                    'service' => 'user-service',
                    'version' => '1.0.0'
                ]
            ],
            'order-service' => [
                'name' => 'order-service',
                'level' => 'INFO',
                'handlers' => ['file', 'console', 'elasticsearch'],
                'context' => [
                    'service' => 'order-service',
                    'version' => '1.1.0'
                ]
            ],
            'product-service' => [
                'name' => 'product-service',
                'level' => 'DEBUG',
                'handlers' => ['file', 'console'],
                'context' => [
                    'service' => 'product-service',
                    'version' => '2.0.0'
                ]
            ],
            'notification-service' => [
                'name' => 'notification-service',
                'level' => 'WARN',
                'handlers' => ['file', 'syslog'],
                'context' => [
                    'service' => 'notification-service',
                    'version' => '1.0.0'
                ]
            ],
            'payment-service' => [
                'name' => 'payment-service',
                'level' => 'INFO',
                'handlers' => ['file', 'console', 'audit'],
                'context' => [
                    'service' => 'payment-service',
                    'version' => '1.0.0'
                ]
            ]
        ];
    }
    
    /**
     * Setup log handlers
     */
    private function setupHandlers(): void
    {
        $this->handlers = [
            'file' => [
                'type' => 'file',
                'path' => '/var/log/microservices/{service}.log',
                'format' => 'json',
                'rotation' => 'daily',
                'retention' => 30
            ],
            'console' => [
                'type' => 'console',
                'format' => 'console',
                'colors' => true
            ],
            'elasticsearch' => [
                'type' => 'elasticsearch',
                'hosts' => ['localhost:9200'],
                'index' => 'microservices-{service}-{date}',
                'format' => 'json'
            ],
            'syslog' => [
                'type' => 'syslog',
                'facility' => 'local0',
                'format' => 'text'
            ],
            'audit' => [
                'type' => 'file',
                'path' => '/var/log/audit/{service}.log',
                'format' => 'json',
                'rotation' => 'daily',
                'retention' => 365
            ]
        ];
    }
    
    /**
     * Log message
     */
    public function log(string $loggerName, string $level, string $message, array $context = []): void
    {
        if (!isset($this->loggers[$loggerName])) {
            return;
        }
        
        $logger = $this->loggers[$loggerName];
        
        // Check log level
        if (!$this->shouldLog($logger['level'], $level)) {
            return;
        }
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => array_merge($logger['context'], $context),
            'trace_id' => $context['trace_id'] ?? null,
            'span_id' => $context['span_id'] ?? null,
            'request_id' => $context['request_id'] ?? uniqid('req_')
        ];
        
        // Send to handlers
        foreach ($logger['handlers'] as $handlerName) {
            $this->sendToHandler($handlerName, $logEntry);
        }
    }
    
    /**
     * Check if should log based on level
     */
    private function shouldLog(string $loggerLevel, string $messageLevel): bool
    {
        $loggerIndex = array_search($loggerLevel, $this->logLevels);
        $messageIndex = array_search($messageLevel, $this->logLevels);
        
        return $messageIndex >= $loggerIndex;
    }
    
    /**
     * Send log entry to handler
     */
    private function sendToHandler(string $handlerName, array $logEntry): void
    {
        if (!isset($this->handlers[$handlerName])) {
            return;
        }
        
        $handler = $this->handlers[$handlerName];
        
        switch ($handler['type']) {
            case 'file':
                $this->writeToFile($handler, $logEntry);
                break;
            case 'console':
                $this->writeToConsole($handler, $logEntry);
                break;
            case 'elasticsearch':
                $this->sendToElasticsearch($handler, $logEntry);
                break;
            case 'syslog':
                $this->sendToSyslog($handler, $logEntry);
                break;
        }
    }
    
    /**
     * Write to file handler
     */
    private function writeToFile(array $handler, array $logEntry): void
    {
        $path = str_replace('{service}', $logEntry['context']['service'], $handler['path']);
        
        if ($handler['format'] === 'json') {
            $logLine = json_encode($logEntry) . "\n";
        } else {
            $logLine = "[{$logEntry['timestamp']}] {$logEntry['level']}: {$logEntry['message']}\n";
        }
        
        // Simulate file write
        error_log("FILE: $path - $logLine");
    }
    
    /**
     * Write to console handler
     */
    private function writeToConsole(array $handler, array $logEntry): void
    {
        if ($handler['colors']) {
            $colors = [
                'DEBUG' => "\033[36m", // Cyan
                'INFO' => "\033[32m",  // Green
                'WARN' => "\033[33m",  // Yellow
                'ERROR' => "\033[31m", // Red
                'FATAL' => "\033[35m"  // Magenta
            ];
            
            $reset = "\033[0m";
            $color = $colors[$logEntry['level']] ?? '';
            
            echo "$color[{$logEntry['timestamp']}] {$logEntry['level']}: {$logEntry['message']}$reset\n";
        } else {
            echo "[{$logEntry['timestamp']}] {$logEntry['level']}: {$logEntry['message']}\n";
        }
    }
    
    /**
     * Send to Elasticsearch
     */
    private function sendToElasticsearch(array $handler, array $logEntry): void
    {
        $index = str_replace(
            ['{service}', '{date}'],
            [$logEntry['context']['service'], date('Y.m.d')],
            $handler['index']
        );
        
        // Simulate Elasticsearch indexing
        error_log("ELASTICSEARCH: $index - " . json_encode($logEntry));
    }
    
    /**
     * Send to syslog
     */
    private function sendToSyslog(array $handler, array $logEntry): void
    {
        $priority = $this->getSyslogPriority($logEntry['level']);
        $message = "[{$logEntry['context']['service']}] {$logEntry['message']}";
        
        // Simulate syslog
        error_log("SYSLOG: $priority - $message");
    }
    
    /**
     * Get syslog priority
     */
    private function getSyslogPriority(string $level): string
    {
        $priorities = [
            'DEBUG' => 'LOG_DEBUG',
            'INFO' => 'LOG_INFO',
            'WARN' => 'LOG_WARNING',
            'ERROR' => 'LOG_ERR',
            'FATAL' => 'LOG_CRIT'
        ];
        
        return $priorities[$level] ?? 'LOG_INFO';
    }
    
    /**
     * Get all loggers
     */
    public function getLoggers(): array
    {
        return $this->loggers;
    }
    
    /**
     * Get all handlers
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}

// Alerting System
class AlertingSystem
{
    private array $rules = [];
    private array $alerts = [];
    private array $channels = [];
    
    public function __construct()
    {
        $this->initializeRules();
        $this->setupChannels();
    }
    
    /**
     * Initialize alerting rules
     */
    private function initializeRules(): void
    {
        $this->rules = [
            'high_error_rate' => [
                'name' => 'High Error Rate',
                'condition' => 'error_rate > 0.1',
                'duration' => '5m',
                'severity' => 'critical',
                'message' => 'Error rate is {{error_rate}}% for service {{service}}',
                'labels' => ['service', 'error_rate'],
                'channels' => ['email', 'slack', 'pagerduty']
            ],
            'high_response_time' => [
                'name' => 'High Response Time',
                'condition' => 'response_time_p95 > 1.0',
                'duration' => '10m',
                'severity' => 'warning',
                'message' => '95th percentile response time is {{response_time_p95}}s for service {{service}}',
                'labels' => ['service', 'response_time_p95'],
                'channels' => ['email', 'slack']
            ],
            'service_down' => [
                'name' => 'Service Down',
                'condition' => 'up == 0',
                'duration' => '1m',
                'severity' => 'critical',
                'message' => 'Service {{service}} is down',
                'labels' => ['service'],
                'channels' => ['email', 'slack', 'pagerduty', 'sms']
            ],
            'memory_usage_high' => [
                'name' => 'High Memory Usage',
                'condition' => 'memory_usage_percent > 85',
                'duration' => '15m',
                'severity' => 'warning',
                'message' => 'Memory usage is {{memory_usage_percent}}% for service {{service}}',
                'labels' => ['service', 'memory_usage_percent'],
                'channels' => ['email', 'slack']
            ]
        ];
    }
    
    /**
     * Setup alert channels
     */
    private function setupChannels(): void
    {
        $this->channels = [
            'email' => [
                'type' => 'email',
                'recipients' => ['devops@example.com', 'team@example.com'],
                'template' => 'alert_email.html'
            ],
            'slack' => [
                'type' => 'slack',
                'webhook_url' => 'https://hooks.slack.com/services/...',
                'channel' => '#alerts',
                'username' => 'AlertBot'
            ],
            'pagerduty' => [
                'type' => 'pagerduty',
                'integration_key' => '...',
                'severity_mapping' => [
                    'critical' => 'critical',
                    'warning' => 'warning',
                    'info' => 'info'
                ]
            ],
            'sms' => [
                'type' => 'sms',
                'phone_numbers' => ['+1234567890', '+0987654321'],
                'provider' => 'twilio'
            ]
        ];
    }
    
    /**
     * Evaluate alert rules
     */
    public function evaluateRules(array $metrics): array
    {
        $triggeredAlerts = [];
        
        foreach ($this->rules as $ruleId => $rule) {
            if ($this->evaluateCondition($rule['condition'], $metrics)) {
                $alert = $this->createAlert($ruleId, $rule, $metrics);
                $triggeredAlerts[] = $alert;
                $this->alerts[] = $alert;
            }
        }
        
        return $triggeredAlerts;
    }
    
    /**
     * Evaluate condition
     */
    private function evaluateCondition(string $condition, array $metrics): bool
    {
        // Simple condition evaluation (in real implementation, use proper expression parser)
        if (strpos($condition, 'error_rate >') !== false) {
            $threshold = (float) substr($condition, strpos($condition, '>') + 1);
            $errorRate = $metrics['error_rate'] ?? 0;
            return $errorRate > $threshold;
        }
        
        if (strpos($condition, 'response_time_p95 >') !== false) {
            $threshold = (float) substr($condition, strpos($condition, '>') + 1);
            $responseTime = $metrics['response_time_p95'] ?? 0;
            return $responseTime > $threshold;
        }
        
        if (strpos($condition, 'up == 0') !== false) {
            $up = $metrics['up'] ?? 1;
            return $up == 0;
        }
        
        if (strpos($condition, 'memory_usage_percent >') !== false) {
            $threshold = (float) substr($condition, strpos($condition, '>') + 1);
            $memoryUsage = $metrics['memory_usage_percent'] ?? 0;
            return $memoryUsage > $threshold;
        }
        
        return false;
    }
    
    /**
     * Create alert
     */
    private function createAlert(string $ruleId, array $rule, array $metrics): array
    {
        $alertId = uniqid('alert_');
        
        // Replace template variables
        $message = $rule['message'];
        foreach ($rule['labels'] as $label) {
            $value = $metrics[$label] ?? 'unknown';
            $message = str_replace("{{$label}}", $value, $message);
        }
        
        $alert = [
            'id' => $alertId,
            'rule_id' => $ruleId,
            'rule_name' => $rule['name'],
            'severity' => $rule['severity'],
            'message' => $message,
            'labels' => array_intersect_key($metrics, array_flip($rule['labels'])),
            'created_at' => time(),
            'status' => 'firing',
            'channels' => $rule['channels']
        ];
        
        // Send to channels
        foreach ($rule['channels'] as $channelName) {
            $this->sendAlertToChannel($channelName, $alert);
        }
        
        return $alert;
    }
    
    /**
     * Send alert to channel
     */
    private function sendAlertToChannel(string $channelName, array $alert): void
    {
        if (!isset($this->channels[$channelName])) {
            return;
        }
        
        $channel = $this->channels[$channelName];
        
        switch ($channel['type']) {
            case 'email':
                $this->sendEmailAlert($channel, $alert);
                break;
            case 'slack':
                $this->sendSlackAlert($channel, $alert);
                break;
            case 'pagerduty':
                $this->sendPagerDutyAlert($channel, $alert);
                break;
            case 'sms':
                $this->sendSMSAlert($channel, $alert);
                break;
        }
    }
    
    /**
     * Send email alert
     */
    private function sendEmailAlert(array $channel, array $alert): void
    {
        $subject = "Alert: {$alert['rule_name']} [{$alert['severity']}]";
        
        // Simulate email sending
        error_log("EMAIL ALERT: $subject - {$alert['message']}");
    }
    
    /**
     * Send Slack alert
     */
    private function sendSlackAlert(array $channel, array $alert): void
    {
        $color = $alert['severity'] === 'critical' ? 'danger' : 'warning';
        
        // Simulate Slack webhook
        error_log("SLACK ALERT: {$channel['channel']} - {$alert['message']}");
    }
    
    /**
     * Send PagerDuty alert
     */
    private function sendPagerDutyAlert(array $channel, array $alert): void
    {
        $severity = $channel['severity_mapping'][$alert['severity']] ?? 'warning';
        
        // Simulate PagerDuty
        error_log("PAGERDUTY ALERT: $severity - {$alert['message']}");
    }
    
    /**
     * Send SMS alert
     */
    private function sendSMSAlert(array $channel, array $alert): void
    {
        // Simulate SMS sending
        error_log("SMS ALERT: {$alert['message']}");
    }
    
    /**
     * Get all alerts
     */
    public function getAlerts(): array
    {
        return $this->alerts;
    }
    
    /**
     * Get all rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }
    
    /**
     * Get all channels
     */
    public function getChannels(): array
    {
        return $this->channels;
    }
}

// Monitoring Examples
class MonitoringExamples
{
    private DistributedTracing $tracing;
    private MetricsCollection $metrics;
    private LoggingSystem $logging;
    private AlertingSystem $alerting;
    
    public function __construct()
    {
        $this->tracing = new DistributedTracing();
        $this->metrics = new MetricsCollection();
        $this->logging = new LoggingSystem();
        $this->alerting = new AlertingSystem();
    }
    
    public function demonstrateDistributedTracing(): void
    {
        echo "Distributed Tracing Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create trace for user order flow
        $traceId = $this->tracing->createTrace('user_order_flow', [
            'user_id' => 123,
            'request_id' => uniqid('req_')
        ]);
        
        echo "Created trace: $traceId\n";
        
        // User service span
        $userSpanId = $this->tracing->createSpan($traceId, 'get_user', 'user-service');
        $this->tracing->addLog($userSpanId, 'INFO', 'Fetching user information', ['user_id' => 123]);
        usleep(50000); // Simulate work
        $this->tracing->finishSpan($userSpanId);
        
        // Order service span (child of user service)
        $orderSpanId = $this->tracing->createChildSpan($userSpanId, 'create_order', 'order-service');
        $this->tracing->addLog($orderSpanId, 'INFO', 'Creating order', ['total' => 99.99]);
        
        // Product service span (child of order service)
        $productSpanId = $this->tracing->createChildSpan($orderSpanId, 'check_inventory', 'product-service');
        $this->tracing->addLog($productSpanId, 'INFO', 'Checking product inventory', ['product_id' => 456]);
        usleep(30000); // Simulate work
        $this->tracing->finishSpan($productSpanId);
        
        // Payment service span (child of order service)
        $paymentSpanId = $this->tracing->createChildSpan($orderSpanId, 'process_payment', 'payment-service');
        $this->tracing->addLog($paymentSpanId, 'INFO', 'Processing payment', ['amount' => 99.99]);
        
        // Simulate payment error
        $this->tracing->addError($paymentSpanId, 'Payment gateway timeout', ['gateway' => 'stripe']);
        $this->tracing->finishSpan($paymentSpanId);
        
        $this->tracing->finishSpan($orderSpanId);
        $this->tracing->finishTrace($traceId);
        
        // Show trace details
        $trace = $this->tracing->getTrace($traceId);
        echo "\nTrace Details:\n";
        echo "Operation: {$trace['operation']}\n";
        echo "Duration: " . round($trace['duration'] * 1000, 2) . "ms\n";
        echo "Spans: " . count($trace['spans']) . "\n";
        
        echo "\nSpan Details:\n";
        foreach ($trace['spans'] as $spanId) {
            $span = $this->tracing->getSpan($spanId);
            echo "  {$span['operation']} ({$span['service']}): " . round($span['duration'] * 1000, 2) . "ms\n";
            
            foreach ($span['logs'] as $log) {
                echo "    {$log['level']}: {$log['message']}\n";
            }
        }
    }
    
    public function demonstrateMetricsCollection(): void
    {
        echo "\nMetrics Collection Demo\n";
        echo str_repeat("-", 28) . "\n";
        
        // Simulate HTTP requests
        for ($i = 0; $i < 100; $i++) {
            $method = rand(0, 1) ? 'GET' : 'POST';
            $status = rand(1, 10) > 9 ? '500' : '200';
            $service = ['user-service', 'order-service', 'product-service'][rand(0, 2)];
            
            $this->metrics->incrementCounter('http_requests_total', [
                'method' => $method,
                'status' => $status,
                'service' => $service
            ]);
            
            // Simulate request duration
            $duration = (rand(10, 500) / 1000); // 0.01 to 0.5 seconds
            $this->metrics->observeHistogram('http_request_duration_seconds', $duration, [
                'method' => $method,
                'endpoint' => '/api/v1/users',
                'service' => $service
            ]);
        }
        
        // Simulate database queries
        for ($i = 0; $i < 50; $i++) {
            $operation = ['SELECT', 'INSERT', 'UPDATE'][rand(0, 2)];
            $table = ['users', 'orders', 'products'][rand(0, 2)];
            $status = rand(1, 20) > 19 ? 'error' : 'success';
            
            $this->metrics->incrementCounter('database_queries_total', [
                'operation' => $operation,
                'table' => $table,
                'status' => $status
            ]);
            
            // Simulate query duration
            $duration = (rand(1, 100) / 1000); // 0.001 to 0.1 seconds
            $this->metrics->observeHistogram('database_query_duration_seconds', $duration, [
                'operation' => $operation,
                'table' => $table,
                'service' => 'user-service'
            ]);
        }
        
        // Simulate gauge metrics
        $this->metrics->setGauge('active_connections', 25, ['service' => 'user-service']);
        $this->metrics->setGauge('memory_usage_bytes', 134217728, ['service' => 'user-service']); // 128MB
        $this->metrics->setGauge('cpu_usage_percent', 45.5, ['service' => 'user-service']);
        
        // Show metrics
        echo "Collected Metrics:\n";
        $allMetrics = $this->metrics->getAllMetrics();
        
        echo "\nCounters:\n";
        foreach ($allMetrics['counters'] as $counter) {
            echo "  {$counter['name']}: {$counter['value']}\n";
        }
        
        echo "\nGauges:\n";
        foreach ($allMetrics['gauges'] as $gauge) {
            echo "  {$gauge['name']}: {$gauge['value']}\n";
        }
        
        echo "\nPrometheus Format:\n";
        echo substr($this->metrics->getPrometheusMetrics(), 0, 500) . "...\n";
    }
    
    public function demonstrateLogging(): void
    {
        echo "\nLogging System Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Log different levels
        $this->logging->log('user-service', 'INFO', 'User login successful', [
            'user_id' => 123,
            'ip_address' => '192.168.1.100',
            'trace_id' => uniqid('trace_'),
            'span_id' => uniqid('span_')
        ]);
        
        $this->logging->log('order-service', 'WARN', 'Order processing delayed', [
            'order_id' => 'ORD-001',
            'delay_seconds' => 30,
            'trace_id' => uniqid('trace_')
        ]);
        
        $this->logging->log('payment-service', 'ERROR', 'Payment gateway timeout', [
            'payment_id' => 'PAY-001',
            'gateway' => 'stripe',
            'timeout_seconds' => 30,
            'trace_id' => uniqid('trace_')
        ]);
        
        $this->logging->log('product-service', 'DEBUG', 'Cache hit for product', [
            'product_id' => 456,
            'cache_key' => 'product_456',
            'trace_id' => uniqid('trace_')
        ]);
        
        // Show loggers configuration
        echo "Logger Configurations:\n";
        $loggers = $this->logging->getLoggers();
        
        foreach ($loggers as $name => $logger) {
            echo "$name:\n";
            echo "  Level: {$logger['level']}\n";
            echo "  Handlers: " . implode(', ', $logger['handlers']) . "\n";
            echo "  Context: " . json_encode($logger['context']) . "\n\n";
        }
        
        // Show handlers configuration
        echo "Handler Configurations:\n";
        $handlers = $this->logging->getHandlers();
        
        foreach ($handlers as $name => $handler) {
            echo "$name:\n";
            echo "  Type: {$handler['type']}\n";
            if (isset($handler['path'])) {
                echo "  Path: {$handler['path']}\n";
            }
            if (isset($handler['format'])) {
                echo "  Format: {$handler['format']}\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateAlerting(): void
    {
        echo "\nAlerting System Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Simulate metrics that would trigger alerts
        $metrics = [
            'error_rate' => 0.15, // 15% error rate
            'response_time_p95' => 1.2, // 1.2 seconds
            'up' => 1, // Service is up
            'memory_usage_percent' => 90, // 90% memory usage
            'service' => 'user-service'
        ];
        
        echo "Current Metrics:\n";
        foreach ($metrics as $key => $value) {
            echo "  $key: $value\n";
        }
        
        echo "\nEvaluating Alert Rules:\n";
        $alerts = $this->alerting->evaluateRules($metrics);
        
        if (empty($alerts)) {
            echo "No alerts triggered.\n";
        } else {
            echo "Triggered Alerts:\n";
            foreach ($alerts as $alert) {
                echo "  {$alert['rule_name']} [{$alert['severity']}]: {$alert['message']}\n";
                echo "    Channels: " . implode(', ', $alert['channels']) . "\n";
            }
        }
        
        // Show alert rules
        echo "\nAlert Rules:\n";
        $rules = $this->alerting->getRules();
        
        foreach ($rules as $ruleId => $rule) {
            echo "$ruleId:\n";
            echo "  Name: {$rule['name']}\n";
            echo "  Condition: {$rule['condition']}\n";
            echo "  Duration: {$rule['duration']}\n";
            echo "  Severity: {$rule['severity']}\n";
            echo "  Channels: " . implode(', ', $rule['channels']) . "\n\n";
        }
        
        // Show alert channels
        echo "Alert Channels:\n";
        $channels = $this->alerting->getChannels();
        
        foreach ($channels as $name => $channel) {
            echo "$name:\n";
            echo "  Type: {$channel['type']}\n";
            if (isset($channel['recipients'])) {
                echo "  Recipients: " . implode(', ', $channel['recipients']) . "\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nMonitoring Best Practices\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. Distributed Tracing:\n";
        echo "   • Use unique trace IDs for request flows\n";
        echo "   • Include correlation IDs in logs\n";
        echo "   • Add context to spans (user_id, request_id)\n";
        echo "   • Track service dependencies\n";
        echo "   • Monitor span performance\n\n";
        
        echo "2. Metrics Collection:\n";
        echo "   • Track business metrics (orders, users)\n";
        echo "   • Monitor system metrics (CPU, memory)\n";
        echo "   • Use appropriate metric types\n";
        echo "   • Include relevant labels\n";
        echo "   • Set up alerting thresholds\n\n";
        
        echo "3. Logging:\n";
        echo "   • Use structured logging (JSON)\n";
        echo "   • Include correlation IDs\n";
        echo "   • Log at appropriate levels\n";
        echo "   • Avoid logging sensitive data\n";
        echo "   • Use multiple handlers\n\n";
        
        echo "4. Alerting:\n";
        echo "   • Define clear alert conditions\n";
        echo "   • Use appropriate severity levels\n";
        echo "   • Configure multiple channels\n";
        echo "   • Include actionable information\n";
        echo "   • Avoid alert fatigue\n\n";
        
        echo "5. Dashboarding:\n";
        echo "   • Create service-specific dashboards\n";
        echo "   • Include key performance indicators\n";
        echo "   • Use time-series visualizations\n";
        echo "   • Set up anomaly detection\n";
        echo "   • Make dashboards accessible";
    }
    
    public function runAllExamples(): void
    {
        echo "Monitoring and Debugging Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateDistributedTracing();
        $this->demonstrateMetricsCollection();
        $this->demonstrateLogging();
        $this->demonstrateAlerting();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runMonitoringDebuggingDemo(): void
{
    $examples = new MonitoringExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runMonitoringDebuggingDemo();
}
?>
