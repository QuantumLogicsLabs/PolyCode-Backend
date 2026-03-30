<?php
/**
 * System Monitoring and Maintenance
 * 
 * Implementation of monitoring, logging, and maintenance procedures.
 */

// System Monitor
class SystemMonitor
{
    private array $metrics = [];
    private array $alerts = [];
    private array $thresholds = [];
    private array $services = [];
    
    public function __construct()
    {
        $this->initializeServices();
        $this->setupThresholds();
    }
    
    /**
     * Initialize services to monitor
     */
    private function initializeServices(): void
    {
        $this->services = [
            'web-server' => [
                'name' => 'Web Server',
                'type' => 'http',
                'endpoint' => 'http://localhost:80/health',
                'timeout' => 5,
                'interval' => 30
            ],
            'database' => [
                'name' => 'Database Server',
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => 3306,
                'timeout' => 5,
                'interval' => 60
            ],
            'cache' => [
                'name' => 'Redis Cache',
                'type' => 'redis',
                'host' => 'localhost',
                'port' => 6379,
                'timeout' => 3,
                'interval' => 30
            ],
            'api' => [
                'name' => 'API Service',
                'type' => 'http',
                'endpoint' => 'http://localhost:8000/api/health',
                'timeout' => 10,
                'interval' => 15
            ]
        ];
    }
    
    /**
     * Setup monitoring thresholds
     */
    private function setupThresholds(): void
    {
        $this->thresholds = [
            'cpu_usage' => 80,
            'memory_usage' => 85,
            'disk_usage' => 90,
            'response_time' => 1000, // ms
            'error_rate' => 5, // %
            'connection_count' => 100
        ];
    }
    
    /**
     * Check service health
     */
    public function checkServiceHealth(string $serviceId): array
    {
        if (!isset($this->services[$serviceId])) {
            return [
                'service' => $serviceId,
                'status' => 'unknown',
                'error' => 'Service not found'
            ];
        }
        
        $service = $this->services[$serviceId];
        $startTime = microtime(true);
        
        try {
            switch ($service['type']) {
                case 'http':
                    $result = $this->checkHttpService($service);
                    break;
                case 'mysql':
                    $result = $this->checkMySQLService($service);
                    break;
                case 'redis':
                    $result = $this->checkRedisService($service);
                    break;
                default:
                    $result = ['status' => 'unknown', 'error' => 'Unknown service type'];
            }
            
            $responseTime = (microtime(true) - $startTime) * 1000;
            $result['response_time'] = round($responseTime, 2);
            $result['timestamp'] = time();
            
            // Store metrics
            $this->storeMetrics($serviceId, $result);
            
            // Check thresholds
            $this->checkThresholds($serviceId, $result);
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'service' => $serviceId,
                'status' => 'error',
                'error' => $e->getMessage(),
                'timestamp' => time()
            ];
        }
    }
    
    /**
     * Check HTTP service
     */
    private function checkHttpService(array $service): array
    {
        // Simulate HTTP request
        $success = rand(1, 100) > 5; // 95% success rate
        $statusCode = $success ? 200 : [500, 503, 504][rand(0, 2)];
        
        $result = [
            'status' => $success ? 'healthy' : 'unhealthy',
            'status_code' => $statusCode,
            'uptime' => rand(1, 30) . ' days'
        ];
        
        if ($success) {
            $result['response_size'] = rand(1000, 50000) . ' bytes';
            $result['memory_usage'] = rand(20, 80) . '%';
        }
        
        return $result;
    }
    
    /**
     * Check MySQL service
     */
    private function checkMySQLService(array $service): array
    {
        // Simulate MySQL connection check
        $connected = rand(1, 100) > 3; // 97% success rate
        
        $result = [
            'status' => $connected ? 'healthy' : 'unhealthy',
            'connections' => rand(5, 50),
            'max_connections' => 100,
            'uptime' => rand(1, 30) . ' days'
        ];
        
        if ($connected) {
            $result['slow_queries'] = rand(0, 10);
            $result['query_cache_hit_rate'] = rand(70, 95) . '%';
            $result['disk_usage'] = rand(10, 70) . '%';
        }
        
        return $result;
    }
    
    /**
     * Check Redis service
     */
    private function checkRedisService(array $service): array
    {
        // Simulate Redis connection check
        $connected = rand(1, 100) > 2; // 98% success rate
        
        $result = [
            'status' => $connected ? 'healthy' : 'unhealthy',
            'memory_usage' => rand(10, 60) . '%',
            'connected_clients' => rand(1, 20),
            'uptime' => rand(1, 30) . ' days'
        ];
        
        if ($connected) {
            $result['hits'] => rand(1000, 10000);
            $result['misses'] => rand(100, 1000);
            $result['hit_rate'] = round(($result['hits'] / ($result['hits'] + $result['misses'])) * 100, 2) . '%';
        }
        
        return $result;
    }
    
    /**
     * Store metrics
     */
    private function storeMetrics(string $serviceId, array $result): void
    {
        if (!isset($this->metrics[$serviceId])) {
            $this->metrics[$serviceId] = [];
        }
        
        $this->metrics[$serviceId][] = $result;
        
        // Keep only last 100 metrics per service
        if (count($this->metrics[$serviceId]) > 100) {
            array_shift($this->metrics[$serviceId]);
        }
    }
    
    /**
     * Check thresholds and trigger alerts
     */
    private function checkThresholds(string $serviceId, array $result): void
    {
        $alerts = [];
        
        // Check response time
        if (isset($result['response_time']) && $result['response_time'] > $this->thresholds['response_time']) {
            $alerts[] = [
                'type' => 'response_time',
                'severity' => 'warning',
                'message' => "Response time {$result['response_time']}ms exceeds threshold {$this->thresholds['response_time']}ms"
            ];
        }
        
        // Check memory usage
        if (isset($result['memory_usage'])) {
            $usage = (int) str_replace('%', '', $result['memory_usage']);
            if ($usage > $this->thresholds['memory_usage']) {
                $alerts[] = [
                    'type' => 'memory_usage',
                    'severity' => 'critical',
                    'message' => "Memory usage {$result['memory_usage']} exceeds threshold {$this->thresholds['memory_usage']}%"
                ];
            }
        }
        
        // Check disk usage
        if (isset($result['disk_usage'])) {
            $usage = (int) str_replace('%', '', $result['disk_usage']);
            if ($usage > $this->thresholds['disk_usage']) {
                $alerts[] = [
                    'type' => 'disk_usage',
                    'severity' => 'critical',
                    'message' => "Disk usage {$result['disk_usage']} exceeds threshold {$this->thresholds['disk_usage']}%"
                ];
            }
        }
        
        // Check connection count
        if (isset($result['connections']) && $result['connections'] > $this->thresholds['connection_count']) {
            $alerts[] = [
                'type' => 'connection_count',
                'severity' => 'warning',
                'message' => "Connection count {$result['connections']} exceeds threshold {$this->thresholds['connection_count']}"
            ];
        }
        
        // Store alerts
        foreach ($alerts as $alert) {
            $alert['service'] => $serviceId;
            $alert['timestamp'] = time();
            $this->alerts[] = $alert;
        }
    }
    
    /**
     * Check all services
     */
    public function checkAllServices(): array
    {
        $results = [];
        
        foreach (array_keys($this->services) as $serviceId) {
            $results[$serviceId] = $this->checkServiceHealth($serviceId);
        }
        
        return $results;
    }
    
    /**
     * Get service metrics
     */
    public function getServiceMetrics(string $serviceId, int $minutes = 60): array
    {
        if (!isset($this->metrics[$serviceId])) {
            return [];
        }
        
        $cutoff = time() - ($minutes * 60);
        $recentMetrics = array_filter(
            $this->metrics[$serviceId],
            function($metric) use ($cutoff) {
                return $metric['timestamp'] >= $cutoff;
            }
        );
        
        return array_values($recentMetrics);
    }
    
    /**
     * Get alerts
     */
    public function getAlerts(int $hours = 24): array
    {
        $cutoff = time() - ($hours * 3600);
        
        return array_filter(
            $this->alerts,
            function($alert) use ($cutoff) {
                return $alert['timestamp'] >= $cutoff;
            }
        );
    }
    
    /**
     * Get services
     */
    public function getServices(): array
    {
        return $this->services;
    }
    
    /**
     * Get thresholds
     */
    public function getThresholds(): array
    {
        return $this->thresholds;
    }
}

// Log Manager
class LogManager
{
    private array $loggers = [];
    private array $logLevels = ['DEBUG', 'INFO', 'WARN', 'ERROR', 'FATAL'];
    private array $logFiles = [];
    private string $logPath;
    
    public function __construct(string $logPath = '/var/log/app')
    {
        $this->logPath = $logPath;
        $this->initializeLoggers();
    }
    
    /**
     * Initialize loggers
     */
    private function initializeLoggers(): void
    {
        $this->loggers = [
            'application' => [
                'name' => 'Application Logger',
                'level' => 'INFO',
                'handlers' => ['file', 'console'],
                'format' => '[%datetime%] %level%: %message% %context%'
            ],
            'access' => [
                'name' => 'Access Logger',
                'level' => 'INFO',
                'handlers' => ['file'],
                'format' => '%datetime% %client_ip% "%request%" %status_code% %response_size%'
            ],
            'error' => [
                'name' => 'Error Logger',
                'level' => 'ERROR',
                'handlers' => ['file', 'email'],
                'format' => '[%datetime%] %level%: %message% %context% %stack_trace%'
            ],
            'security' => [
                'name' => 'Security Logger',
                'level' => 'WARN',
                'handlers' => ['file', 'syslog'],
                'format' => '[%datetime%] %level%: %message% %context%'
            ],
            'performance' => [
                'name' => 'Performance Logger',
                'level' => 'INFO',
                'handlers' => ['file'],
                'format' => '[%datetime%] %level%: %message% %context%'
            ]
        ];
        
        // Initialize log files
        foreach ($this->loggers as $loggerName => $logger) {
            $this->logFiles[$loggerName] = $this->logPath . '/' . $loggerName . '.log';
        }
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
            'timestamp' => time(),
            'datetime' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'logger' => $loggerName
        ];
        
        // Add stack trace for errors
        if ($level === 'ERROR' || $level === 'FATAL') {
            $logEntry['stack_trace'] => $this->getStackTrace();
        }
        
        // Format log entry
        $formattedLog = $this->formatLogEntry($logEntry, $logger['format']);
        
        // Send to handlers
        foreach ($logger['handlers'] as $handler) {
            $this->sendToHandler($handler, $formattedLog, $logEntry);
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
     * Format log entry
     */
    private function formatLogEntry(array $entry, string $format): string
    {
        $formatted = $format;
        
        $formatted = str_replace('%datetime%', $entry['datetime'], $formatted);
        $formatted = str_replace('%level%', $entry['level'], $formatted);
        $formatted = str_replace('%message%', $entry['message'], $formatted);
        $formatted = str_replace('%logger%', $entry['logger'], $formatted);
        
        // Add context
        if (!empty($entry['context'])) {
            $contextStr = json_encode($entry['context']);
            $formatted = str_replace('%context%', $contextStr, $formatted);
        } else {
            $formatted = str_replace('%context%', '', $formatted);
        }
        
        // Add stack trace
        if (isset($entry['stack_trace'])) {
            $formatted = str_replace('%stack_trace%', $entry['stack_trace'], $formatted);
        } else {
            $formatted = str_replace('%stack_trace%', '', $formatted);
        }
        
        // Add access log specific fields
        if (isset($entry['context']['client_ip'])) {
            $formatted = str_replace('%client_ip%', $entry['context']['client_ip'], $formatted);
            $formatted = str_replace('%request%', $entry['context']['request'] ?? '', $formatted);
            $formatted = str_replace('%status_code%', $entry['context']['status_code'] ?? '', $formatted);
            $formatted = str_replace('%response_size%', $entry['context']['response_size'] ?? '', $formatted);
        }
        
        return $formatted;
    }
    
    /**
     * Send to handler
     */
    private function sendToHandler(string $handler, string $formattedLog, array $entry): void
    {
        switch ($handler) {
            case 'file':
                $this->writeToFile($entry['logger'], $formattedLog);
                break;
            case 'console':
                $this->writeToConsole($formattedLog, $entry['level']);
                break;
            case 'email':
                $this->sendEmail($formattedLog, $entry);
                break;
            case 'syslog':
                $this->sendToSyslog($formattedLog, $entry['level']);
                break;
        }
    }
    
    /**
     * Write to file
     */
    private function writeToFile(string $loggerName, string $log): void
    {
        $file = $this->logFiles[$loggerName];
        
        // Simulate file write
        error_log("FILE: $file - $log");
    }
    
    /**
     * Write to console
     */
    private function writeToConsole(string $log, string $level): void
    {
        $colors = [
            'DEBUG' => "\033[36m", // Cyan
            'INFO' => "\033[32m",  // Green
            'WARN' => "\033[33m",  // Yellow
            'ERROR' => "\033[31m", // Red
            'FATAL' => "\033[35m"  // Magenta
        ];
        
        $reset = "\033[0m";
        $color = $colors[$level] ?? '';
        
        echo "$color$log$reset\n";
    }
    
    /**
     * Send email (for critical errors)
     */
    private function sendEmail(string $log, array $entry): void
    {
        if ($entry['level'] === 'FATAL') {
            // Simulate email sending
            error_log("EMAIL ALERT: $log");
        }
    }
    
    /**
     * Send to syslog
     */
    private function sendToSyslog(string $log, string $level): void
    {
        $priority = $this->getSyslogPriority($level);
        // Simulate syslog
        error_log("SYSLOG: $priority - $log");
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
     * Get stack trace
     */
    private function getStackTrace(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $stack = [];
        
        foreach ($trace as $frame) {
            $stack[] = "{$frame['file']}:{$frame['line']} - {$frame['function']}()";
        }
        
        return implode("\n", $stack);
    }
    
    /**
     * Log application event
     */
    public function logApplication(string $level, string $message, array $context = []): void
    {
        $this->log('application', $level, $message, $context);
    }
    
    /**
     * Log access
     */
    public function logAccess(string $clientIp, string $request, int $statusCode, int $responseSize): void
    {
        $this->log('access', 'INFO', '', [
            'client_ip' => $clientIp,
            'request' => $request,
            'status_code' => $statusCode,
            'response_size' => $responseSize
        ]);
    }
    
    /**
     * Log error
     */
    public function logError(string $message, array $context = []): void
    {
        $this->log('error', 'ERROR', $message, $context);
    }
    
    /**
     * Log security event
     */
    public function logSecurity(string $level, string $message, array $context = []): void
    {
        $this->log('security', $level, $message, $context);
    }
    
    /**
     * Log performance metric
     */
    public function logPerformance(string $metric, float $value, array $context = []): void
    {
        $this->log('performance', 'INFO', "$metric: $value", $context);
    }
    
    /**
     * Get log statistics
     */
    public function getLogStats(string $loggerName, int $hours = 24): array
    {
        // Simulate log statistics
        return [
            'total_logs' => rand(1000, 10000),
            'error_count' => rand(10, 100),
            'warning_count' => rand(50, 500),
            'info_count' => rand(500, 5000),
            'debug_count' => rand(100, 1000),
            'file_size' => rand(1000000, 10000000) . ' bytes'
        ];
    }
    
    /**
     * Get loggers
     */
    public function getLoggers(): array
    {
        return $this->loggers;
    }
}

// Maintenance Scheduler
class MaintenanceScheduler
{
    private array $tasks = [];
    private array $schedule = [];
    private array $history = [];
    
    public function __construct()
    {
        $this->initializeTasks();
    }
    
    /**
     * Initialize maintenance tasks
     */
    private function initializeTasks(): void
    {
        $this->tasks = [
            'database_backup' => [
                'name' => 'Database Backup',
                'description' => 'Backup MySQL database',
                'schedule' => '0 2 * * *', // Daily at 2 AM
                'command' => 'mysqldump -u root -p app > /backups/app_$(date +%Y%m%d).sql',
                'timeout' => 1800, // 30 minutes
                'retry_count' => 3,
                'enabled' => true
            ],
            'log_rotation' => [
                'name' => 'Log Rotation',
                'description' => 'Rotate and compress log files',
                'schedule' => '0 0 * * 0', // Weekly on Sunday at midnight
                'command' => 'logrotate /etc/logrotate.conf',
                'timeout' => 600, // 10 minutes
                'retry_count' => 2,
                'enabled' => true
            ],
            'cache_cleanup' => [
                'name' => 'Cache Cleanup',
                'description' => 'Clear Redis cache and temporary files',
                'schedule' => '0 3 * * *', // Daily at 3 AM
                'command' => 'redis-cli FLUSHALL && rm -rf /tmp/*',
                'timeout' => 300, // 5 minutes
                'retry_count' => 1,
                'enabled' => true
            ],
            'security_scan' => [
                'name' => 'Security Scan',
                'description' => 'Run security vulnerability scan',
                'schedule' => '0 1 * * 1', // Weekly on Monday at 1 AM
                'command' => 'security-scanner --scan-all',
                'timeout' => 3600, // 1 hour
                'retry_count' => 2,
                'enabled' => true
            ],
            'performance_optimization' => [
                'name' => 'Performance Optimization',
                'description' => 'Optimize database and clear caches',
                'schedule' => '0 4 * * 0', // Weekly on Sunday at 4 AM
                'command' => 'mysqlcheck -o app --auto-repair && redis-cli --eval optimize.lua',
                'timeout' => 1800, // 30 minutes
                'retry_count' => 1,
                'enabled' => true
            ],
            'ssl_renewal' => [
                'name' => 'SSL Certificate Renewal',
                'description' => 'Check and renew SSL certificates',
                'schedule' => '0 5 1 * *', // Monthly on 1st at 5 AM
                'command' => 'certbot renew --quiet',
                'timeout' => 600, // 10 minutes
                'retry_count' => 3,
                'enabled' => true
            ]
        ];
    }
    
    /**
     * Add maintenance task
     */
    public function addTask(string $taskId, array $config): void
    {
        $this->tasks[$taskId] = array_merge([
            'name' => 'Unnamed Task',
            'description' => '',
            'schedule' => '0 0 * * *',
            'command' => '',
            'timeout' => 300,
            'retry_count' => 1,
            'enabled' => true
        ], $config);
    }
    
    /**
     * Execute maintenance task
     */
    public function executeTask(string $taskId): array
    {
        if (!isset($this->tasks[$taskId])) {
            return [
                'task_id' => $taskId,
                'status' => 'error',
                'message' => 'Task not found'
            ];
        }
        
        $task = $this->tasks[$taskId];
        
        if (!$task['enabled']) {
            return [
                'task_id' => $taskId,
                'status' => 'skipped',
                'message' => 'Task is disabled'
            ];
        }
        
        $execution = [
            'task_id' => $taskId,
            'task_name' => $task['name'],
            'started_at' => time(),
            'status' => 'running',
            'command' => $task['command'],
            'timeout' => $task['timeout'],
            'retry_count' => 0,
            'output' => '',
            'error' => ''
        ];
        
        try {
            // Simulate task execution
            $execution = $this->simulateTaskExecution($execution, $task);
            
        } catch (Exception $e) {
            $execution['status'] = 'error';
            $execution['error'] = $e->getMessage();
            $execution['finished_at'] = time();
        }
        
        // Store in history
        $this->history[] = $execution;
        
        return $execution;
    }
    
    /**
     * Simulate task execution
     */
    private function simulateTaskExecution(array $execution, array $task): array
    {
        $executionTime = rand(10, $task['timeout'] - 10);
        $success = rand(1, 20) !== 1; // 95% success rate
        
        // Simulate execution time
        sleep(1); // Simulate some work
        
        $execution['output'] = "Task executed successfully";
        $execution['finished_at'] = time();
        $execution['duration'] = $execution['finished_at'] - $execution['started_at'];
        
        if ($success) {
            $execution['status'] = 'completed';
            $execution['output'] .= "\nExecution time: {$execution['duration']}s";
        } else {
            $execution['status'] = 'failed';
            $execution['error'] = 'Task execution failed';
            
            // Retry logic
            if ($execution['retry_count'] < $task['retry_count']) {
                $execution['retry_count']++;
                $execution['status'] => 'retrying';
                return $this->simulateTaskExecution($execution, $task);
            }
        }
        
        return $execution;
    }
    
    /**
     * Get due tasks
     */
    public function getDueTasks(): array
    {
        $dueTasks = [];
        $currentTime = time();
        
        foreach ($this->tasks as $taskId => $task) {
            if (!$task['enabled']) {
                continue;
            }
            
            // Simplified cron check - in real implementation, use proper cron parser
            $nextRun = $this->getNextRunTime($task['schedule']);
            
            if ($nextRun <= $currentTime) {
                $dueTasks[] = [
                    'task_id' => $taskId,
                    'task' => $task,
                    'next_run' => $nextRun
                ];
            }
        }
        
        return $dueTasks;
    }
    
    /**
     * Get next run time (simplified)
     */
    private function getNextRunTime(string $cronSchedule): int
    {
        // Simplified cron schedule parsing
        // In real implementation, use proper cron library
        $parts = explode(' ', $cronSchedule);
        $minute = (int) $parts[0];
        $hour = (int) $parts[1];
        
        $now = getdate();
        $nextRun = mktime($hour, $minute, 0, $now['mon'], $now['mday'], $now['year']);
        
        // If time has passed, schedule for next day
        if ($nextRun <= time()) {
            $nextRun += 86400; // Add 24 hours
        }
        
        return $nextRun;
    }
    
    /**
     * Run maintenance scheduler
     */
    public function runScheduler(): array
    {
        $results = [];
        $dueTasks = $this->getDueTasks();
        
        foreach ($dueTasks as $dueTask) {
            echo "Running task: {$dueTask['task']['name']}\n";
            $result = $this->executeTask($dueTask['task_id']);
            $results[] = $result;
        }
        
        return $results;
    }
    
    /**
     * Get task history
     */
    public function getTaskHistory(int $limit = 50): array
    {
        return array_slice(array_reverse($this->history), 0, $limit);
    }
    
    /**
     * Get tasks
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
    
    /**
     * Enable/disable task
     */
    public function toggleTask(string $taskId, bool $enabled): bool
    {
        if (!isset($this->tasks[$taskId])) {
            return false;
        }
        
        $this->tasks[$taskId]['enabled'] = $enabled;
        return true;
    }
}

// Monitoring Examples
class MonitoringMaintenanceExamples
{
    private SystemMonitor $monitor;
    private LogManager $logger;
    private MaintenanceScheduler $scheduler;
    
    public function __construct()
    {
        $this->monitor = new SystemMonitor();
        $this->logger = new LogManager();
        $this->scheduler = new MaintenanceScheduler();
    }
    
    public function demonstrateSystemMonitoring(): void
    {
        echo "System Monitoring Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Show services
        echo "Monitored Services:\n";
        $services = $this->monitor->getServices();
        foreach ($services as $serviceId => $service) {
            echo "$serviceId: {$service['name']} ({$service['type']})\n";
            echo "  Endpoint: {$service['endpoint'] ?? $service['host'] . ':' . $service['port']}\n";
            echo "  Interval: {$service['interval']}s\n\n";
        }
        
        // Check all services
        echo "Checking All Services:\n";
        $results = $this->monitor->checkAllServices();
        
        foreach ($results as $serviceId => $result) {
            echo "$serviceId: {$result['status']}\n";
            if (isset($result['response_time'])) {
                echo "  Response Time: {$result['response_time']}ms\n";
            }
            if (isset($result['error'])) {
                echo "  Error: {$result['error']}\n";
            }
            if (isset($result['memory_usage'])) {
                echo "  Memory Usage: {$result['memory_usage']}\n";
            }
            echo "\n";
        }
        
        // Show alerts
        echo "Recent Alerts:\n";
        $alerts = $this->monitor->getAlerts(1); // Last hour
        if (empty($alerts)) {
            echo "No alerts in the last hour.\n";
        } else {
            foreach ($alerts as $alert) {
                echo "[{$alert['severity']}] {$alert['service']}: {$alert['message']}\n";
            }
        }
        
        // Show thresholds
        echo "\nMonitoring Thresholds:\n";
        $thresholds = $this->monitor->getThresholds();
        foreach ($thresholds as $metric => $value) {
            echo "$metric: $value\n";
        }
    }
    
    public function demonstrateLogging(): void
    {
        echo "\nLogging System Demo\n";
        echo str_repeat("-", 22) . "\n";
        
        // Show loggers
        echo "Available Loggers:\n";
        $loggers = $this->logger->getLoggers();
        foreach ($loggers as $loggerName => $logger) {
            echo "$loggerName: {$logger['name']}\n";
            echo "  Level: {$logger['level']}\n";
            echo "  Handlers: " . implode(', ', $logger['handlers']) . "\n\n";
        }
        
        // Log different types of messages
        echo "Logging Sample Messages:\n";
        
        $this->logger->logApplication('INFO', 'Application started successfully', [
            'version' => '1.0.0',
            'environment' => 'production'
        ]);
        
        $this->logger->logAccess('192.168.1.100', 'GET /api/users HTTP/1.1', 200, 1024);
        
        $this->logger->logError('Database connection failed', [
            'host' => 'localhost',
            'port' => 3306,
            'error_code' => 'ECONNREFUSED'
        ]);
        
        $this->logger->logSecurity('WARN', 'Failed login attempt', [
            'username' => 'admin',
            'ip_address' => '192.168.1.200',
            'user_agent' => 'Mozilla/5.0'
        ]);
        
        $this->logger->logPerformance('api_response_time', 150.5, [
            'endpoint' => '/api/users',
            'method' => 'GET',
            'status' => 200
        ]);
        
        // Show log statistics
        echo "\nLog Statistics (last 24 hours):\n";
        foreach (array_keys($loggers) as $loggerName) {
            $stats = $this->logger->getLogStats($loggerName);
            echo "$loggerName:\n";
            echo "  Total Logs: {$stats['total_logs']}\n";
            echo "  Errors: {$stats['error_count']}\n";
            echo "  Warnings: {$stats['warning_count']}\n";
            echo "  File Size: {$stats['file_size']}\n\n";
        }
    }
    
    public function demonstrateMaintenanceScheduler(): void
    {
        echo "\nMaintenance Scheduler Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Show tasks
        echo "Maintenance Tasks:\n";
        $tasks = $this->scheduler->getTasks();
        foreach ($tasks as $taskId => $task) {
            echo "$taskId: {$task['name']}\n";
            echo "  Description: {$task['description']}\n";
            echo "  Schedule: {$task['schedule']}\n";
            echo "  Enabled: " . ($task['enabled'] ? 'Yes' : 'No') . "\n";
            echo "  Timeout: {$task['timeout']}s\n\n";
        }
        
        // Run scheduler
        echo "Running Maintenance Scheduler:\n";
        $results = $this->scheduler->runScheduler();
        
        if (empty($results)) {
            echo "No tasks due for execution.\n";
        } else {
            foreach ($results as $result) {
                echo "{$result['task_name']}: {$result['status']}\n";
                if (isset($result['duration'])) {
                    echo "  Duration: {$result['duration']}s\n";
                }
                if (isset($result['error'])) {
                    echo "  Error: {$result['error']}\n";
                }
                echo "\n";
            }
        }
        
        // Execute a specific task
        echo "Executing Database Backup Task:\n";
        $backupResult = $this->scheduler->executeTask('database_backup');
        echo "Status: {$backupResult['status']}\n";
        if (isset($backupResult['duration'])) {
            echo "Duration: {$backupResult['duration']}s\n";
        }
        if (isset($backupResult['output'])) {
            echo "Output: {$backupResult['output']}\n";
        }
        
        // Show task history
        echo "\nTask History (last 5):\n";
        $history = $this->scheduler->getTaskHistory(5);
        foreach ($history as $task) {
            echo "{$task['task_name']}: {$task['status']} (" . date('Y-m-d H:i:s', $task['started_at']) . ")\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nMonitoring and Maintenance Best Practices\n";
        echo str_repeat("-", 45) . "\n";
        
        echo "1. System Monitoring:\n";
        echo "   • Monitor all critical services\n";
        echo "   • Set appropriate alert thresholds\n";
        echo "   • Use multiple monitoring tools\n";
        echo "   • Implement proper alerting\n";
        echo "   • Monitor system resources\n\n";
        
        echo "2. Logging:\n";
        echo "   • Use structured logging\n";
        echo "   • Log at appropriate levels\n";
        echo "   • Include relevant context\n";
        echo "   • Implement log rotation\n";
        echo "   • Centralize log collection\n\n";
        
        echo "3. Maintenance:\n";
        echo "   • Schedule regular backups\n";
        echo "   • Implement log rotation\n";
        echo "   • Perform security scans\n";
        echo "   • Optimize performance regularly\n";
        echo "   • Update dependencies\n\n";
        
        echo "4. Alerting:\n";
        echo "   • Use meaningful alert messages\n";
        echo "   • Implement alert escalation\n";
        echo "   • Avoid alert fatigue\n";
        echo "   • Use multiple notification channels\n";
        echo "   • Document alert procedures\n\n";
        
        echo "5. Automation:\n";
        echo "   • Automate routine maintenance\n";
        echo "   • Use configuration management\n";
        echo "   • Implement self-healing\n";
        echo "   • Use infrastructure as code\n";
        echo "   • Document all procedures";
    }
    
    public function runAllExamples(): void
    {
        echo "System Monitoring and Maintenance Examples\n";
        echo str_repeat("=", 45) . "\n";
        
        $this->demonstrateSystemMonitoring();
        $this->demonstrateLogging();
        $this->demonstrateMaintenanceScheduler();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runMonitoringMaintenanceDemo(): void
{
    $examples = new MonitoringMaintenanceExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runMonitoringMaintenanceDemo();
}
?>
