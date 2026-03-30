<?php
/**
 * API Performance Optimization
 * 
 * Implementation of performance optimization techniques for RESTful and GraphQL APIs.
 */

// Response Caching
class ResponseCache
{
    private array $cache = [];
    private array $config;
    private string $storageType;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'default_ttl' => 3600, // 1 hour
            'max_size' => 1000,    // Max cache entries
            'storage' => 'memory'  // memory, redis, file
        ], $config);
        
        $this->storageType = $this->config['storage'];
    }
    
    /**
     * Generate cache key
     */
    private function generateKey(string $method, string $uri, array $params = []): string
    {
        $key = $method . ':' . $uri;
        
        if (!empty($params)) {
            ksort($params);
            $key .= ':' . md5(json_encode($params));
        }
        
        return 'cache:' . md5($key);
    }
    
    /**
     * Get cached response
     */
    public function get(string $method, string $uri, array $params = []): ?array
    {
        $key = $this->generateKey($method, $uri, $params);
        
        switch ($this->storageType) {
            case 'memory':
                return $this->getFromMemory($key);
            case 'redis':
                return $this->getFromRedis($key);
            case 'file':
                return $this->getFromFile($key);
            default:
                return null;
        }
    }
    
    /**
     * Set cached response
     */
    public function set(string $method, string $uri, array $response, int $ttl = null, array $params = []): void
    {
        $key = $this->generateKey($method, $uri, $params);
        $ttl = $ttl ?? $this->config['default_ttl'];
        
        $cacheEntry = [
            'response' => $response,
            'cached_at' => time(),
            'expires_at' => time() + $ttl,
            'ttl' => $ttl
        ];
        
        switch ($this->storageType) {
            case 'memory':
                $this->setInMemory($key, $cacheEntry);
                break;
            case 'redis':
                $this->setInRedis($key, $cacheEntry, $ttl);
                break;
            case 'file':
                $this->setInFile($key, $cacheEntry);
                break;
        }
    }
    
    /**
     * Get from memory cache
     */
    private function getFromMemory(string $key): ?array
    {
        if (!isset($this->cache[$key])) {
            return null;
        }
        
        $entry = $this->cache[$key];
        
        if (time() > $entry['expires_at']) {
            unset($this->cache[$key]);
            return null;
        }
        
        return $entry['response'];
    }
    
    /**
     * Set in memory cache
     */
    private function setInMemory(string $key, array $entry): void
    {
        // Remove oldest entries if cache is full
        if (count($this->cache) >= $this->config['max_size']) {
            $oldestKey = array_key_first($this->cache);
            unset($this->cache[$oldestKey]);
        }
        
        $this->cache[$key] = $entry;
    }
    
    /**
     * Get from Redis cache (simulated)
     */
    private function getFromRedis(string $key): ?array
    {
        // Simulate Redis get
        return $this->getFromMemory($key);
    }
    
    /**
     * Set in Redis cache (simulated)
     */
    private function setInRedis(string $key, array $entry, int $ttl): void
    {
        // Simulate Redis set with TTL
        $this->setInMemory($key, $entry);
    }
    
    /**
     * Get from file cache (simulated)
     */
    private function getFromFile(string $key): ?array
    {
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = file_get_contents($filename);
        $entry = unserialize($data);
        
        if (time() > $entry['expires_at']) {
            unlink($filename);
            return null;
        }
        
        return $entry['response'];
    }
    
    /**
     * Set in file cache
     */
    private function setInFile(string $key, array $entry): void
    {
        $filename = $this->getCacheFilename($key);
        $data = serialize($entry);
        
        file_put_contents($filename, $data);
    }
    
    /**
     * Get cache filename
     */
    private function getCacheFilename(string $key): string
    {
        return sys_get_temp_dir() . '/api_cache_' . $key . '.cache';
    }
    
    /**
     * Clear cache
     */
    public function clear(string $pattern = '*'): void
    {
        switch ($this->storageType) {
            case 'memory':
                if ($pattern === '*') {
                    $this->cache = [];
                } else {
                    foreach (array_keys($this->cache) as $key) {
                        if (fnmatch($pattern, $key)) {
                            unset($this->cache[$key]);
                        }
                    }
                }
                break;
            case 'file':
                $files = glob(sys_get_temp_dir() . '/api_cache_' . $pattern . '.cache');
                foreach ($files as $file) {
                    unlink($file);
                }
                break;
        }
    }
    
    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        return [
            'storage_type' => $this->storageType,
            'entries' => count($this->cache),
            'max_size' => $this->config['max_size'],
            'default_ttl' => $this->config['default_ttl']
        ];
    }
}

// Database Query Optimizer
class DatabaseOptimizer
{
    private array $queries = [];
    private array $slowQueries = [];
    private float $slowQueryThreshold = 0.1; // 100ms
    
    /**
     * Execute optimized query
     */
    public function execute(string $sql, array $params = []): array
    {
        $startTime = microtime(true);
        
        // Simulate query execution
        $result = $this->simulateQuery($sql, $params);
        
        $executionTime = microtime(true) - $startTime;
        
        // Track query performance
        $this->trackQuery($sql, $params, $executionTime);
        
        return $result;
    }
    
    /**
     * Simulate query execution
     */
    private function simulateQuery(string $sql, array $params): array
    {
        // Simulate different query types and their performance
        $queryLower = strtolower($sql);
        
        if (strpos($queryLower, 'select') !== false) {
            if (strpos($queryLower, 'where') !== false) {
                // SELECT with WHERE - moderate performance
                usleep(rand(10000, 50000)); // 10-50ms
                return [
                    'data' => [
                        ['id' => 1, 'name' => 'John'],
                        ['id' => 2, 'name' => 'Jane']
                    ],
                    'count' => 2
                ];
            } else {
                // SELECT without WHERE - potentially slow
                usleep(rand(50000, 200000)); // 50-200ms
                return [
                    'data' => array_fill(0, 100, ['id' => rand(1, 1000), 'name' => 'User']),
                    'count' => 100
                ];
            }
        } elseif (strpos($queryLower, 'insert') !== false) {
            usleep(rand(5000, 20000)); // 5-20ms
            return ['inserted_id' => rand(1, 1000)];
        } elseif (strpos($queryLower, 'update') !== false) {
            usleep(rand(10000, 30000)); // 10-30ms
            return ['affected_rows' => rand(1, 5)];
        } elseif (strpos($queryLower, 'delete') !== false) {
            usleep(rand(10000, 30000)); // 10-30ms
            return ['affected_rows' => rand(1, 3)];
        }
        
        // Default case
        usleep(rand(1000, 10000)); // 1-10ms
        return [];
    }
    
    /**
     * Track query performance
     */
    private function trackQuery(string $sql, array $params, float $executionTime): void
    {
        $queryHash = md5($sql);
        
        if (!isset($this->queries[$queryHash])) {
            $this->queries[$queryHash] = [
                'sql' => $sql,
                'count' => 0,
                'total_time' => 0,
                'avg_time' => 0,
                'min_time' => PHP_FLOAT_MAX,
                'max_time' => 0
            ];
        }
        
        $query = &$this->queries[$queryHash];
        $query['count']++;
        $query['total_time'] += $executionTime;
        $query['avg_time'] = $query['total_time'] / $query['count'];
        $query['min_time'] = min($query['min_time'], $executionTime);
        $query['max_time'] = max($query['max_time'], $executionTime);
        
        // Track slow queries
        if ($executionTime > $this->slowQueryThreshold) {
            $this->slowQueries[] = [
                'sql' => $sql,
                'params' => $params,
                'execution_time' => $executionTime,
                'timestamp' => time()
            ];
        }
    }
    
    /**
     * Get query statistics
     */
    public function getQueryStats(): array
    {
        return [
            'total_queries' => count($this->queries),
            'slow_queries' => count($this->slowQueries),
            'queries' => array_values($this->queries),
            'slow_query_threshold' => $this->slowQueryThreshold
        ];
    }
    
    /**
     * Get optimization suggestions
     */
    public function getOptimizationSuggestions(): array
    {
        $suggestions = [];
        
        foreach ($this->queries as $query) {
            if ($query['avg_time'] > 0.05) { // 50ms average
                $suggestions[] = [
                    'query' => $query['sql'],
                    'issue' => 'Slow average execution time',
                    'suggestion' => 'Consider adding indexes or optimizing WHERE clause',
                    'avg_time' => $query['avg_time']
                ];
            }
            
            if ($query['count'] > 100 && $query['avg_time'] > 0.01) { // 10ms average for frequently executed queries
                $suggestions[] = [
                    'query' => $query['sql'],
                    'issue' => 'Frequently executed query with moderate performance',
                    'suggestion' => 'Consider query result caching',
                    'count' => $query['count'],
                    'avg_time' => $query['avg_time']
                ];
            }
        }
        
        return $suggestions;
    }
}

// Response Compression
class ResponseCompressor
{
    private array $config;
    private array $supportedEncodings = ['gzip', 'deflate', 'br'];
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'min_length' => 1024,      // Minimum length to compress
            'level' => 6,              // Compression level (1-9)
            'enabled' => true          // Enable compression
        ], $config);
    }
    
    /**
     * Compress response if beneficial
     */
    public function compress(string $content, string $acceptEncoding = ''): array
    {
        if (!$this->config['enabled'] || strlen($content) < $this->config['min_length']) {
            return [
                'content' => $content,
                'compressed' => false,
                'encoding' => null
            ];
        }
        
        $preferredEncoding = $this->getPreferredEncoding($acceptEncoding);
        
        if (!$preferredEncoding) {
            return [
                'content' => $content,
                'compressed' => false,
                'encoding' => null
            ];
        }
        
        $compressed = $this->compressContent($content, $preferredEncoding);
        
        // Only return compressed if it's actually smaller
        if (strlen($compressed) < strlen($content)) {
            return [
                'content' => $compressed,
                'compressed' => true,
                'encoding' => $preferredEncoding,
                'original_size' => strlen($content),
                'compressed_size' => strlen($compressed),
                'compression_ratio' => round((1 - strlen($compressed) / strlen($content)) * 100, 2)
            ];
        }
        
        return [
            'content' => $content,
            'compressed' => false,
            'encoding' => null
        ];
    }
    
    /**
     * Get preferred encoding from Accept-Encoding header
     */
    private function getPreferredEncoding(string $acceptEncoding): ?string
    {
        if (empty($acceptEncoding)) {
            return null;
        }
        
        $encodings = explode(',', $acceptEncoding);
        $encodings = array_map('trim', $encodings);
        
        foreach ($this->supportedEncodings as $encoding) {
            if (in_array($encoding, $encodings) || in_array("$encoding;q=1", $encodings)) {
                return $encoding;
            }
        }
        
        return null;
    }
    
    /**
     * Compress content using specified encoding
     */
    private function compressContent(string $content, string $encoding): string
    {
        switch ($encoding) {
            case 'gzip':
                return gzencode($content, $this->config['level']);
            case 'deflate':
                return gzcompress($content, $this->config['level']);
            case 'br':
                // Brotli compression (not available in standard PHP)
                // Fall back to gzip
                return gzencode($content, $this->config['level']);
            default:
                return $content;
        }
    }
    
    /**
     * Get compression headers
     */
    public function getCompressionHeaders(array $result): array
    {
        $headers = [];
        
        if ($result['compressed'] && $result['encoding']) {
            $headers['Content-Encoding'] = $result['encoding'];
            $headers['Vary'] = 'Accept-Encoding';
            
            if (isset($result['original_size'])) {
                $headers['X-Original-Size'] = $result['original_size'];
                $headers['X-Compressed-Size'] = $result['compressed_size'];
                $headers['X-Compression-Ratio'] = $result['compression_ratio'] . '%';
            }
        }
        
        return $headers;
    }
}

// Connection Pool Manager
class ConnectionPool
{
    private array $connections = [];
    private array $config;
    private int $maxConnections;
    private int $minConnections;
    private int $currentConnections = 0;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'max_connections' => 10,
            'min_connections' => 2,
            'connection_timeout' => 30,
            'idle_timeout' => 300
        ], $config);
        
        $this->maxConnections = $this->config['max_connections'];
        $this->minConnections = $this->config['min_connections'];
        
        $this->initializePool();
    }
    
    /**
     * Initialize connection pool
     */
    private function initializePool(): void
    {
        for ($i = 0; $i < $this->minConnections; $i++) {
            $this->createConnection();
        }
    }
    
    /**
     * Create new connection
     */
    private function createConnection(): string
    {
        if ($this->currentConnections >= $this->maxConnections) {
            throw new Exception('Maximum connections reached');
        }
        
        $connectionId = uniqid('conn_');
        $this->connections[$connectionId] = [
            'id' => $connectionId,
            'created_at' => time(),
            'last_used' => time(),
            'in_use' => false,
            'active' => true
        ];
        
        $this->currentConnections++;
        
        return $connectionId;
    }
    
    /**
     * Get connection from pool
     */
    public function getConnection(): string
    {
        // Find available connection
        foreach ($this->connections as $id => $conn) {
            if (!$conn['in_use'] && $conn['active']) {
                $this->connections[$id]['in_use'] = true;
                $this->connections[$id]['last_used'] = time();
                return $id;
            }
        }
        
        // Create new connection if possible
        if ($this->currentConnections < $this->maxConnections) {
            $id = $this->createConnection();
            $this->connections[$id]['in_use'] = true;
            return $id;
        }
        
        throw new Exception('No available connections in pool');
    }
    
    /**
     * Release connection back to pool
     */
    public function releaseConnection(string $connectionId): void
    {
        if (isset($this->connections[$connectionId])) {
            $this->connections[$connectionId]['in_use'] = false;
            $this->connections[$connectionId]['last_used'] = time();
        }
    }
    
    /**
     * Close connection
     */
    public function closeConnection(string $connectionId): void
    {
        if (isset($this->connections[$connectionId])) {
            unset($this->connections[$connectionId]);
            $this->currentConnections--;
        }
    }
    
    /**
     * Cleanup idle connections
     */
    public function cleanup(): void
    {
        $currentTime = time();
        $idleTimeout = $this->config['idle_timeout'];
        
        foreach ($this->connections as $id => $conn) {
            if (!$conn['in_use'] && ($currentTime - $conn['last_used']) > $idleTimeout) {
                // Keep minimum connections
                if ($this->currentConnections > $this->minConnections) {
                    $this->closeConnection($id);
                }
            }
        }
    }
    
    /**
     * Get pool statistics
     */
    public function getStats(): array
    {
        $active = 0;
        $inUse = 0;
        $idle = 0;
        
        foreach ($this->connections as $conn) {
            if ($conn['active']) {
                $active++;
                if ($conn['in_use']) {
                    $inUse++;
                } else {
                    $idle++;
                }
            }
        }
        
        return [
            'total_connections' => $this->currentConnections,
            'active_connections' => $active,
            'in_use_connections' => $inUse,
            'idle_connections' => $idle,
            'max_connections' => $this->maxConnections,
            'min_connections' => $this->minConnections
        ];
    }
}

// API Performance Monitor
class PerformanceMonitor
{
    private array $metrics = [];
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'sample_rate' => 1.0,      // 100% sampling
            'max_metrics' => 10000,    // Max metrics to keep
            'alert_thresholds' => [
                'response_time' => 1.0,  // 1 second
                'error_rate' => 0.05,    // 5%
                'memory_usage' => 0.8    // 80%
            ]
        ], $config);
    }
    
    /**
     * Record API request metrics
     */
    public function recordRequest(array $data): void
    {
        if (rand(0, 100) > ($this->config['sample_rate'] * 100)) {
            return; // Skip based on sample rate
        }
        
        $metric = [
            'timestamp' => time(),
            'microtime' => microtime(true),
            'method' => $data['method'] ?? 'GET',
            'endpoint' => $data['endpoint'] ?? '/',
            'status_code' => $data['status_code'] ?? 200,
            'response_time' => $data['response_time'] ?? 0,
            'memory_usage' => $data['memory_usage'] ?? 0,
            'cpu_usage' => $data['cpu_usage'] ?? 0,
            'user_id' => $data['user_id'] ?? null,
            'request_id' => $data['request_id'] ?? uniqid('req_')
        ];
        
        $this->metrics[] = $metric;
        
        // Limit metrics storage
        if (count($this->metrics) > $this->config['max_metrics']) {
            array_shift($this->metrics);
        }
        
        // Check for alerts
        $this->checkAlerts($metric);
    }
    
    /**
     * Check performance alerts
     */
    private function checkAlerts(array $metric): void
    {
        $thresholds = $this->config['alert_thresholds'];
        
        // Response time alert
        if ($metric['response_time'] > $thresholds['response_time']) {
            $this->triggerAlert('slow_response', [
                'message' => 'Slow response time detected',
                'endpoint' => $metric['endpoint'],
                'response_time' => $metric['response_time'],
                'threshold' => $thresholds['response_time']
            ]);
        }
        
        // Error rate alert
        if ($metric['status_code'] >= 400) {
            $this->triggerAlert('error_response', [
                'message' => 'Error response detected',
                'endpoint' => $metric['endpoint'],
                'status_code' => $metric['status_code']
            ]);
        }
        
        // Memory usage alert
        if ($metric['memory_usage'] > $thresholds['memory_usage']) {
            $this->triggerAlert('high_memory', [
                'message' => 'High memory usage detected',
                'memory_usage' => $metric['memory_usage'],
                'threshold' => $thresholds['memory_usage']
            ]);
        }
    }
    
    /**
     * Trigger alert
     */
    private function triggerAlert(string $type, array $data): void
    {
        // In real implementation, this would send to monitoring system
        error_log("PERFORMANCE ALERT [$type]: " . json_encode($data));
    }
    
    /**
     * Get performance statistics
     */
    public function getStats(int $minutes = 5): array
    {
        $cutoff = time() - ($minutes * 60);
        $recentMetrics = array_filter($this->metrics, function($metric) use ($cutoff) {
            return $metric['timestamp'] >= $cutoff;
        });
        
        if (empty($recentMetrics)) {
            return [
                'period' => $minutes . ' minutes',
                'total_requests' => 0,
                'avg_response_time' => 0,
                'error_rate' => 0,
                'requests_per_minute' => 0
            ];
        }
        
        $totalRequests = count($recentMetrics);
        $totalResponseTime = array_sum(array_column($recentMetrics, 'response_time'));
        $errorCount = count(array_filter($recentMetrics, function($metric) {
            return $metric['status_code'] >= 400;
        }));
        
        return [
            'period' => $minutes . ' minutes',
            'total_requests' => $totalRequests,
            'avg_response_time' => round($totalResponseTime / $totalRequests, 3),
            'error_rate' => round(($errorCount / $totalRequests) * 100, 2),
            'requests_per_minute' => round($totalRequests / $minutes, 2),
            'status_codes' => array_count_values(array_column($recentMetrics, 'status_code')),
            'slowest_requests' => $this->getSlowestRequests($recentMetrics, 5)
        ];
    }
    
    /**
     * Get slowest requests
     */
    private function getSlowestRequests(array $metrics, int $limit): array
    {
        usort($metrics, function($a, $b) {
            return $b['response_time'] <=> $a['response_time'];
        });
        
        return array_slice($metrics, 0, $limit);
    }
    
    /**
     * Get endpoint performance
     */
    public function getEndpointStats(): array
    {
        $endpointStats = [];
        
        foreach ($this->metrics as $metric) {
            $endpoint = $metric['endpoint'];
            
            if (!isset($endpointStats[$endpoint])) {
                $endpointStats[$endpoint] = [
                    'endpoint' => $endpoint,
                    'requests' => 0,
                    'total_response_time' => 0,
                    'errors' => 0,
                    'status_codes' => []
                ];
            }
            
            $stats = &$endpointStats[$endpoint];
            $stats['requests']++;
            $stats['total_response_time'] += $metric['response_time'];
            
            if ($metric['status_code'] >= 400) {
                $stats['errors']++;
            }
            
            if (!isset($stats['status_codes'][$metric['status_code']])) {
                $stats['status_codes'][$metric['status_code']] = 0;
            }
            $stats['status_codes'][$metric['status_code']]++;
        }
        
        // Calculate averages and rates
        foreach ($endpointStats as &$stats) {
            $stats['avg_response_time'] = round($stats['total_response_time'] / $stats['requests'], 3);
            $stats['error_rate'] = round(($stats['errors'] / $stats['requests']) * 100, 2);
        }
        
        return array_values($endpointStats);
    }
}

// API Performance Examples
class ApiPerformanceExamples
{
    private ResponseCache $cache;
    private DatabaseOptimizer $db;
    private ResponseCompressor $compressor;
    private ConnectionPool $pool;
    private PerformanceMonitor $monitor;
    
    public function __construct()
    {
        $this->cache = new ResponseCache(['storage' => 'memory', 'max_size' => 100]);
        $this->db = new DatabaseOptimizer();
        $this->compressor = new ResponseCompressor();
        $this->pool = new ConnectionPool(['max_connections' => 5, 'min_connections' => 2]);
        $this->monitor = new PerformanceMonitor();
    }
    
    public function demonstrateCaching(): void
    {
        echo "Response Caching Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Simulate API requests
        $requests = [
            ['method' => 'GET', 'uri' => '/users', 'params' => ['page' => 1]],
            ['method' => 'GET', 'uri' => '/users', 'params' => ['page' => 1]], // Same request
            ['method' => 'GET', 'uri' => '/users', 'params' => ['page' => 2]], // Different params
            ['method' => 'POST', 'uri' => '/users', 'params' => []] // Different method
        ];
        
        foreach ($requests as $i => $request) {
            echo "Request " . ($i + 1) . ": {$request['method']} {$request['uri']}\n";
            
            // Check cache first
            $cached = $this->cache->get($request['method'], $request['uri'], $request['params']);
            
            if ($cached) {
                echo "  Result: CACHE HIT\n";
                echo "  Data: " . json_encode($cached) . "\n";
            } else {
                echo "  Result: CACHE MISS\n";
                
                // Simulate API response
                $response = [
                    'data' => ['id' => 1, 'name' => 'John'],
                    'timestamp' => time()
                ];
                
                // Cache the response
                $this->cache->set($request['method'], $request['uri'], $response, 3600, $request['params']);
                
                echo "  Data: " . json_encode($response) . "\n";
            }
            echo "\n";
        }
        
        // Show cache statistics
        $stats = $this->cache->getStats();
        echo "Cache Statistics:\n";
        print_r($stats);
    }
    
    public function demonstrateDatabaseOptimization(): void
    {
        echo "\nDatabase Optimization Demo\n";
        echo str_repeat("-", 32) . "\n";
        
        // Execute various queries
        $queries = [
            'SELECT * FROM users WHERE id = 1',
            'SELECT * FROM users',  // Potentially slow
            'SELECT * FROM users WHERE email = ?',
            'INSERT INTO users (name, email) VALUES (?, ?)',
            'UPDATE users SET name = ? WHERE id = ?',
            'DELETE FROM users WHERE id = ?'
        ];
        
        foreach ($queries as $sql) {
            echo "Executing: $sql\n";
            
            $result = $this->db->execute($sql, [1, 'test']);
            echo "Result: " . json_encode($result) . "\n\n";
        }
        
        // Show query statistics
        $stats = $this->db->getQueryStats();
        echo "Query Statistics:\n";
        echo "Total Queries: {$stats['total_queries']}\n";
        echo "Slow Queries: {$stats['slow_queries']}\n\n";
        
        echo "Query Performance:\n";
        foreach ($stats['queries'] as $query) {
            echo "  {$query['sql']}\n";
            echo "    Count: {$query['count']}\n";
            echo "    Avg Time: " . round($query['avg_time'] * 1000, 2) . "ms\n";
            echo "    Min Time: " . round($query['min_time'] * 1000, 2) . "ms\n";
            echo "    Max Time: " . round($query['max_time'] * 1000, 2) . "ms\n\n";
        }
        
        // Show optimization suggestions
        $suggestions = $this->db->getOptimizationSuggestions();
        echo "Optimization Suggestions:\n";
        foreach ($suggestions as $suggestion) {
            echo "  Query: {$suggestion['query']}\n";
            echo "  Issue: {$suggestion['issue']}\n";
            echo "  Suggestion: {$suggestion['suggestion']}\n";
            if (isset($suggestion['avg_time'])) {
                echo "  Avg Time: " . round($suggestion['avg_time'] * 1000, 2) . "ms\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateCompression(): void
    {
        echo "\nResponse Compression Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test with different content sizes
        $contents = [
            'Small' => str_repeat('Small content', 10),           // ~140 bytes
            'Medium' => str_repeat('Medium content ', 100),        // ~1.5KB
            'Large' => str_repeat('Large content ', 1000),         // ~15KB
            'Huge' => str_repeat('Huge content ', 10000)           // ~150KB
        ];
        
        $acceptEncoding = 'gzip, deflate, br';
        
        foreach ($contents as $size => $content) {
            echo "$size Content (" . strlen($content) . " bytes):\n";
            
            $result = $this->compressor->compress($content, $acceptEncoding);
            
            echo "  Compressed: " . ($result['compressed'] ? 'Yes' : 'No') . "\n";
            echo "  Encoding: " . ($result['encoding'] ?? 'None') . "\n";
            
            if ($result['compressed']) {
                echo "  Original Size: {$result['original_size']} bytes\n";
                echo "  Compressed Size: {$result['compressed_size']} bytes\n";
                echo "  Compression Ratio: {$result['compression_ratio']}%\n";
            }
            
            echo "  Final Size: " . strlen($result['content']) . " bytes\n\n";
        }
    }
    
    public function demonstrateConnectionPool(): void
    {
        echo "Connection Pool Demo\n";
        echo str_repeat("-", 23) . "\n";
        
        // Get connections
        echo "Getting connections from pool:\n";
        
        $connections = [];
        for ($i = 0; $i < 3; $i++) {
            try {
                $connId = $this->pool->getConnection();
                $connections[] = $connId;
                echo "  Got connection: $connId\n";
            } catch (Exception $e) {
                echo "  Error: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\nPool Statistics:\n";
        $stats = $this->pool->getStats();
        print_r($stats);
        
        // Release connections
        echo "\nReleasing connections:\n";
        foreach ($connections as $connId) {
            $this->pool->releaseConnection($connId);
            echo "  Released: $connId\n";
        }
        
        echo "\nPool Statistics after release:\n";
        $stats = $this->pool->getStats();
        print_r($stats);
        
        // Cleanup
        $this->pool->cleanup();
        echo "\nAfter cleanup:\n";
        $stats = $this->pool->getStats();
        print_r($stats);
    }
    
    public function demonstratePerformanceMonitoring(): void
    {
        echo "\nPerformance Monitoring Demo\n";
        echo str_repeat("-", 32) . "\n";
        
        // Simulate API requests with different performance characteristics
        $requests = [
            ['method' => 'GET', 'endpoint' => '/users', 'status_code' => 200, 'response_time' => 0.05],
            ['method' => 'GET', 'endpoint' => '/users', 'status_code' => 200, 'response_time' => 0.08],
            ['method' => 'POST', 'endpoint' => '/users', 'status_code' => 201, 'response_time' => 0.12],
            ['method' => 'GET', 'endpoint' => '/posts', 'status_code' => 200, 'response_time' => 0.03],
            ['method' => 'GET', 'endpoint' => '/posts', 'status_code' => 500, 'response_time' => 1.5], // Slow error
            ['method' => 'GET', 'endpoint' => '/users', 'status_code' => 200, 'response_time' => 0.06],
            ['method' => 'GET', 'endpoint' => '/users', 'status_code' => 404, 'response_time' => 0.02], // Error
            ['method' => 'GET', 'endpoint' => '/posts', 'status_code' => 200, 'response_time' => 0.04]
        ];
        
        foreach ($requests as $request) {
            $this->monitor->recordRequest($request);
        }
        
        // Show performance statistics
        echo "Performance Statistics (last 5 minutes):\n";
        $stats = $this->monitor->getStats(5);
        print_r($stats);
        
        // Show endpoint statistics
        echo "\nEndpoint Performance:\n";
        $endpointStats = $this->monitor->getEndpointStats();
        foreach ($endpointStats as $stat) {
            echo "  {$stat['endpoint']}:\n";
            echo "    Requests: {$stat['requests']}\n";
            echo "    Avg Response Time: {$stat['avg_response_time']}s\n";
            echo "    Error Rate: {$stat['error_rate']}%\n";
            echo "    Status Codes: " . json_encode($stat['status_codes']) . "\n\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nAPI Performance Best Practices\n";
        echo str_repeat("-", 38) . "\n";
        
        echo "1. Caching Strategies:\n";
        echo "   • Implement response caching for static data\n";
        echo "   • Use appropriate cache TTL values\n";
        echo "   • Implement cache invalidation\n";
        echo "   • Use CDN for global caching\n";
        echo "   • Cache database query results\n\n";
        
        echo "2. Database Optimization:\n";
        echo "   • Use proper indexing\n";
        echo "   • Implement query result caching\n";
        echo "   • Use connection pooling\n";
        echo "   • Optimize slow queries\n";
        echo "   • Use read replicas for scaling\n\n";
        
        echo "3. Response Compression:\n";
        echo "   • Enable gzip compression\n";
        echo "   • Compress only large responses\n";
        echo "   • Use appropriate compression levels\n";
        echo "   • Consider client capabilities\n";
        echo "   • Monitor compression ratios\n\n";
        
        echo "4. Connection Management:\n";
        echo "   • Use connection pooling\n";
        echo "   • Implement keep-alive connections\n";
        echo "   • Set appropriate timeouts\n";
        echo "   • Monitor connection usage\n";
        echo "   • Handle connection failures gracefully\n\n";
        
        echo "5. Monitoring and Alerting:\n";
        echo "   • Track response times\n";
        echo "   • Monitor error rates\n";
        echo "   • Set up performance alerts\n";
        echo "   • Use APM tools\n";
        echo "   • Implement distributed tracing";
    }
    
    public function runAllExamples(): void
    {
        echo "API Performance Optimization Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateCaching();
        $this->demonstrateDatabaseOptimization();
        $this->demonstrateCompression();
        $this->demonstrateConnectionPool();
        $this->demonstratePerformanceMonitoring();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runApiPerformanceDemo(): void
{
    $examples = new ApiPerformanceExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runApiPerformanceDemo();
}
?>
