# PHP Performance Optimization

## Code Optimization

### Efficient Coding Practices
```php
<?php
// Memory-efficient string concatenation
class StringOptimization {
    public function inefficientConcatenation(array $words): string {
        $result = '';
        foreach ($words as $word) {
            $result .= $word . ' '; // Creates new string each iteration
        }
        return trim($result);
    }
    
    public function efficientConcatenation(array $words): string {
        return implode(' ', $words); // Much more efficient
    }
    
    public function stringBuilderConcatenation(array $words): string {
        $parts = [];
        foreach ($words as $word) {
            $parts[] = $word;
        }
        return implode(' ', $parts);
    }
    
    // Array optimization
    public function inefficientArrayAccess(array $data, string $key) {
        // Multiple lookups
        if (isset($data[$key])) {
            return $data[$key];
        }
        return null;
    }
    
    public function efficientArrayAccess(array $data, string $key) {
        // Single lookup
        return $data[$key] ?? null;
    }
    
    // Loop optimization
    public function inefficientLoop(array $items): array {
        $result = [];
        for ($i = 0; $i < count($items); $i++) { // count() called each iteration
            $result[] = strtoupper($items[$i]);
        }
        return $result;
    }
    
    public function efficientLoop(array $items): array {
        $result = [];
        $count = count($items); // Store count once
        for ($i = 0; $i < $count; $i++) {
            $result[] = strtoupper($items[$i]);
        }
        return $result;
    }
    
    public function foreachLoop(array $items): array {
        $result = [];
        foreach ($items as $item) { // Most readable and efficient for most cases
            $result[] = strtoupper($item);
        }
        return $result;
    }
    
    // Function call optimization
    public function inefficientFunctionCalls(array $numbers): array {
        $result = [];
        foreach ($numbers as $number) {
            if (intval($number) % 2 == 0) { // Function call in loop
                $result[] = $number;
            }
        }
        return $result;
    }
    
    public function efficientFunctionCalls(array $numbers): array {
        $result = [];
        foreach ($numbers as $number) {
            $intVal = (int)$number; // Cast instead of function call
            if ($intVal % 2 == 0) {
                $result[] = $number;
            }
        }
        return $result;
    }
    
    // Regular expression optimization
    public function inefficientRegex(string $text): array {
        $matches = [];
        // Multiple preg_match calls
        preg_match('/\b[A-Z][a-z]+\b/', $text, $matches);
        preg_match('/\b[a-z]+\b/', $text, $matches);
        preg_match('/\b\d+\b/', $text, $matches);
        return $matches;
    }
    
    public function efficientRegex(string $text): array {
        // Single preg_match_all with multiple patterns
        $pattern = '/\b[A-Z][a-z]+\b|\b[a-z]+\b|\b\d+\b/';
        preg_match_all($pattern, $text, $matches);
        return $matches[0];
    }
    
    // Memory-efficient processing
    public function inefficientMemoryProcessing(string $filename): array {
        $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $processed = [];
        
        foreach ($lines as $line) {
            $processed[] = strtoupper(trim($line));
        }
        
        return $processed; // Loads entire file into memory
    }
    
    public function efficientMemoryProcessing(string $filename): \Generator {
        $handle = fopen($filename, 'r');
        
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                yield strtoupper(trim($line));
            }
            fclose($handle);
        }
    }
}

// Caching optimization
class CacheOptimization {
    private array $cache = [];
    private int $maxCacheSize = 1000;
    
    public function expensiveCalculation(int $number): int {
        $cacheKey = "calc_$number";
        
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        
        // Simulate expensive calculation
        $result = 0;
        for ($i = 0; $i < $number; $i++) {
            $result += $i * $i;
        }
        
        // Cache result
        if (count($this->cache) >= $this->maxCacheSize) {
            // Remove oldest entry (simple LRU)
            $firstKey = array_key_first($this->cache);
            unset($this->cache[$firstKey]);
        }
        
        $this->cache[$cacheKey] = $result;
        return $result;
    }
    
    public function memoize(callable $function): callable {
        $cache = [];
        
        return function (...$args) use ($function, &$cache) {
            $key = serialize($args);
            
            if (!isset($cache[$key])) {
                $cache[$key] = $function(...$args);
            }
            
            return $cache[$key];
        };
    }
    
    public function clearCache(): void {
        $this->cache = [];
    }
    
    public function getCacheStats(): array {
        return [
            'size' => count($this->cache),
            'max_size' => $this->maxCacheSize,
            'usage_percent' => (count($this->cache) / $this->maxCacheSize) * 100
        ];
    }
}

// Database optimization
class DatabaseOptimization {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function inefficientQueries(array $userIds): array {
        $results = [];
        
        // N+1 query problem
        foreach ($userIds as $userId) {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $results[] = $stmt->fetch();
        }
        
        return $results;
    }
    
    public function efficientQueries(array $userIds): array {
        // Single query with IN clause
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id IN ($placeholders)");
        $stmt->execute($userIds);
        
        return $stmt->fetchAll();
    }
    
    public function batchInsert(array $records): bool {
        // Batch insert instead of individual inserts
        $values = [];
        $placeholders = [];
        
        foreach ($records as $record) {
            $values = array_merge($values, array_values($record));
            $placeholders[] = '(' . str_repeat('?,', count($record) - 1) . '?)';
        }
        
        $sql = "INSERT INTO users (name, email, age) VALUES " . implode(',', $placeholders);
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    public function useTransactions(array $operations): bool {
        try {
            $this->pdo->beginTransaction();
            
            foreach ($operations as $operation) {
                $stmt = $this->pdo->prepare($operation['sql']);
                $stmt->execute($operation['params']);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    public function prepareStatementsReuse(): array {
        // Prepare once, execute multiple times
        $selectStmt = $this->pdo->prepare("SELECT * FROM users WHERE status = ?");
        $updateStmt = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        
        $activeUsers = [];
        $selectStmt->execute(['active']);
        $users = $selectStmt->fetchAll();
        
        foreach ($users as $user) {
            $activeUsers[] = $user;
            $updateStmt->execute([$user['id']]);
        }
        
        return $activeUsers;
    }
}

// Usage examples
$stringOpt = new StringOptimization();
$cacheOpt = new CacheOptimization();

// Test string concatenation
$words = ['Hello', 'World', 'PHP', 'Performance', 'Optimization'];
echo "Inefficient: " . $stringOpt->inefficientConcatenation($words) . "\n";
echo "Efficient: " . $stringOpt->efficientConcatenation($words) . "\n";

// Test caching
$calculation = $cacheOpt->memoize(function($n) {
    $result = 0;
    for ($i = 0; $i < $n; $i++) {
        $result += $i * $i;
    }
    return $result;
});

echo "First call: " . $calculation(1000) . "\n";
echo "Second call (cached): " . $calculation(1000) . "\n";

// Test memory-efficient processing
$generator = $stringOpt->efficientMemoryProcessing('large_file.txt');
foreach ($generator as $line) {
    echo "Processed: " . substr($line, 0, 20) . "...\n";
    if (count($generator) > 10) break; // Limit for example
}
?>
```

## Memory Management

### Memory Optimization Techniques
```php
<?php
class MemoryManager {
    private int $memoryLimit;
    private array $memoryUsage = [];
    
    public function __construct(int $memoryLimit = 128 * 1024 * 1024) { // 128MB default
        $this->memoryLimit = $memoryLimit;
    }
    
    public function getMemoryUsage(): array {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => $this->memoryLimit,
            'usage_percent' => (memory_get_usage(true) / $this->memoryLimit) * 100
        ];
    }
    
    public function checkMemoryLimit(): bool {
        return memory_get_usage(true) < $this->memoryLimit;
    }
    
    public function logMemoryUsage(string $operation): void {
        $this->memoryUsage[] = [
            'operation' => $operation,
            'memory' => memory_get_usage(true),
            'timestamp' => microtime(true)
        ];
    }
    
    public function getMemoryReport(): array {
        if (empty($this->memoryUsage)) {
            return ['message' => 'No memory usage data recorded'];
        }
        
        $usage = array_column($this->memoryUsage, 'memory');
        $operations = array_column($this->memoryUsage, 'operation');
        
        return [
            'operations' => count($this->memoryUsage),
            'min_memory' => min($usage),
            'max_memory' => max($usage),
            'avg_memory' => array_sum($usage) / count($usage),
            'peak_operation' => $operations[array_search(max($usage), $usage)],
            'timeline' => $this->memoryUsage
        ];
    }
    
    public function optimizeMemory(): void {
        // Force garbage collection
        gc_collect_cycles();
        
        // Clear internal caches
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
    
    public function unsetVariables(array $variables): void {
        foreach ($variables as $var) {
            unset($var);
        }
    }
}

class MemoryEfficientCollection implements \IteratorAggregate, \ArrayAccess, \Countable {
    private array $items = [];
    private int $maxItems;
    
    public function __construct(int $maxItems = 1000) {
        $this->maxItems = $maxItems;
    }
    
    public function add($item): void {
        $this->items[] = $item;
        
        // Remove oldest items if limit exceeded
        if (count($this->items) > $this->maxItems) {
            array_shift($this->items);
        }
    }
    
    public function getIterator(): \ArrayIterator {
        return new \ArrayIterator($this->items);
    }
    
    public function offsetExists($offset): bool {
        return isset($this->items[$offset]);
    }
    
    public function offsetGet($offset) {
        return $this->items[$offset] ?? null;
    }
    
    public function offsetSet($offset, $value): void {
        if ($offset === null) {
            $this->add($value);
        } else {
            $this->items[$offset] = $value;
        }
    }
    
    public function offsetUnset($offset): void {
        unset($this->items[$offset]);
    }
    
    public function count(): int {
        return count($this->items);
    }
    
    public function clear(): void {
        $this->items = [];
    }
    
    public function getMemoryUsage(): int {
        return strlen(serialize($this->items));
    }
}

class StreamProcessor {
    private string $filename;
    private int $bufferSize;
    
    public function __construct(string $filename, int $bufferSize = 8192) {
        $this->filename = $filename;
        $this->bufferSize = $bufferSize;
    }
    
    public function processLargeFile(callable $processor): void {
        $handle = fopen($this->filename, 'r');
        
        if (!$handle) {
            throw new \Exception("Cannot open file: {$this->filename}");
        }
        
        try {
            while (!feof($handle)) {
                $chunk = fread($handle, $this->bufferSize);
                
                if ($chunk !== false) {
                    $processor($chunk);
                }
                
                // Optional: Check memory usage
                if (memory_get_usage(true) > 100 * 1024 * 1024) { // 100MB
                    gc_collect_cycles();
                }
            }
        } finally {
            fclose($handle);
        }
    }
    
    public function processLineByLine(callable $processor): void {
        $handle = fopen($this->filename, 'r');
        
        if (!$handle) {
            throw new \Exception("Cannot open file: {$this->filename}");
        }
        
        try {
            while (($line = fgets($handle)) !== false) {
                $processor(trim($line));
            }
        } finally {
            fclose($handle);
        }
    }
    
    public function countLines(): int {
        $handle = fopen($this->filename, 'r');
        $lines = 0;
        
        if ($handle) {
            while (!feof($handle)) {
                fgets($handle);
                $lines++;
            }
            fclose($handle);
        }
        
        return $lines;
    }
}

class ObjectPool {
    private array $pool = [];
    private string $className;
    private int $maxSize;
    
    public function __construct(string $className, int $maxSize = 100) {
        $this->className = $className;
        $this->maxSize = $maxSize;
    }
    
    public function get(): object {
        if (!empty($this->pool)) {
            $object = array_pop($this->pool);
            $this->resetObject($object);
            return $object;
        }
        
        return new $this->className();
    }
    
    public function release(object $object): void {
        if (count($this->pool) < $this->maxSize && get_class($object) === $this->className) {
            $this->pool[] = $object;
        }
    }
    
    private function resetObject(object $object): void {
        // Reset object state if needed
        if (method_exists($object, 'reset')) {
            $object->reset();
        }
    }
    
    public function getPoolSize(): int {
        return count($this->pool);
    }
    
    public function clearPool(): void {
        $this->pool = [];
    }
}

class WeakReferenceContainer {
    private array $references = [];
    
    public function add(object $object, string $key = null): string {
        $key = $key ?? spl_object_hash($object);
        $this->references[$key] = \WeakReference::create($object);
        return $key;
    }
    
    public function get(string $key): ?object {
        if (isset($this->references[$key])) {
            $object = $this->references[$key]->get();
            if ($object !== null) {
                return $object;
            }
            unset($this->references[$key]);
        }
        return null;
    }
    
    public function remove(string $key): void {
        unset($this->references[$key]);
    }
    
    public function cleanup(): void {
        foreach ($this->references as $key => $reference) {
            if ($reference->get() === null) {
                unset($this->references[$key]);
            }
        }
    }
    
    public function count(): int {
        $this->cleanup();
        return count($this->references);
    }
}

// Memory profiling
class MemoryProfiler {
    private array $snapshots = [];
    private bool $enabled = false;
    
    public function enable(): void {
        $this->enabled = true;
        $this->takeSnapshot('start');
    }
    
    public function disable(): void {
        if ($this->enabled) {
            $this->takeSnapshot('end');
            $this->enabled = false;
        }
    }
    
    public function takeSnapshot(string $label): void {
        if (!$this->enabled) {
            return;
        }
        
        $this->snapshots[] = [
            'label' => $label,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'timestamp' => microtime(true),
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
        ];
    }
    
    public function getReport(): array {
        if (empty($this->snapshots)) {
            return ['message' => 'No profiling data available'];
        }
        
        $start = $this->snapshots[0];
        $end = $this->snapshots[count($this->snapshots) - 1];
        
        $memoryGrowth = $end['memory_usage'] - $start['memory_usage'];
        $peakGrowth = $end['memory_peak'] - $start['memory_peak'];
        $duration = $end['timestamp'] - $start['timestamp'];
        
        return [
            'start_memory' => $start['memory_usage'],
            'end_memory' => $end['memory_usage'],
            'memory_growth' => $memoryGrowth,
            'peak_memory' => $end['memory_peak'],
            'peak_growth' => $peakGrowth,
            'duration' => $duration,
            'snapshots' => $this->snapshots
        ];
    }
    
    public function findMemoryLeaks(): array {
        $leaks = [];
        
        for ($i = 1; $i < count($this->snapshots); $i++) {
            $prev = $this->snapshots[$i - 1];
            $curr = $this->snapshots[$i];
            
            $growth = $curr['memory_usage'] - $prev['memory_usage'];
            
            if ($growth > 1024 * 1024) { // More than 1MB growth
                $leaks[] = [
                    'label' => $curr['label'],
                    'growth' => $growth,
                    'backtrace' => $curr['backtrace']
                ];
            }
        }
        
        return $leaks;
    }
}

// Usage examples
$memoryManager = new MemoryManager();
$profiler = new MemoryProfiler();

// Enable profiling
$profiler->enable();

// Memory-efficient collection
$collection = new MemoryEfficientCollection(100);
for ($i = 0; $i < 1000; $i++) {
    $collection->add("Item $i");
}

$profiler->takeSnapshot('after_collection');

echo "Collection size: " . $collection->count() . "\n";
echo "Collection memory: " . $collection->getMemoryUsage() . " bytes\n";

// Stream processing
$streamProcessor = new StreamProcessor('large_file.txt');
$lineCount = 0;

$streamProcessor->processLineByLine(function($line) use (&$lineCount) {
    $lineCount++;
    if ($lineCount % 1000 === 0) {
        echo "Processed $lineCount lines\n";
    }
});

$profiler->takeSnapshot('after_stream_processing');

// Object pooling
class HeavyObject {
    public array $data = [];
    
    public function __construct() {
        // Simulate heavy initialization
        for ($i = 0; $i < 1000; $i++) {
            $this->data[] = str_repeat('x', 100);
        }
    }
    
    public function reset(): void {
        $this->data = [];
    }
}

$pool = new ObjectPool(HeavyObject::class, 10);

// Get objects from pool
$objects = [];
for ($i = 0; $i < 15; $i++) {
    $objects[] = $pool->get();
}

// Return objects to pool
foreach ($objects as $object) {
    $pool->release($object);
}

$profiler->takeSnapshot('after_object_pooling');

// Generate report
$profiler->disable();
$report = $profiler->getReport();

echo "\n=== Memory Profiling Report ===\n";
echo "Memory growth: " . number_format($report['memory_growth'] / 1024 / 1024, 2) . " MB\n";
echo "Peak memory: " . number_format($report['peak_memory'] / 1024 / 1024, 2) . " MB\n";
echo "Duration: " . number_format($report['duration'] * 1000, 2) . " ms\n";

// Check for memory leaks
$leaks = $profiler->findMemoryLeaks();
if (!empty($leaks)) {
    echo "\nPotential memory leaks detected:\n";
    foreach ($leaks as $leak) {
        echo "- {$leak['label']}: " . number_format($leak['growth'] / 1024 / 1024, 2) . " MB\n";
    }
}

// Memory manager report
echo "\n=== Memory Manager Report ===\n";
$memReport = $memoryManager->getMemoryReport();
if (isset($memReport['message'])) {
    echo $memReport['message'] . "\n";
} else {
    echo "Operations: {$memReport['operations']}\n";
    echo "Average memory: " . number_format($memReport['avg_memory'] / 1024 / 1024, 2) . " MB\n";
    echo "Peak operation: {$memReport['peak_operation']}\n";
}
?>
```

## Caching Strategies

### Multi-Level Caching Implementation
```php
<?php
interface CacheInterface {
    public function get(string $key, $default = null);
    public function set(string $key, $value, int $ttl = 3600): bool;
    public function delete(string $key): bool;
    public function clear(): bool;
    public function has(string $key): bool;
}

class InMemoryCache implements CacheInterface {
    private array $cache = [];
    private array $ttl = [];
    private int $maxSize;
    private array $accessTimes = [];
    
    public function __construct(int $maxSize = 1000) {
        $this->maxSize = $maxSize;
    }
    
    public function get(string $key, $default = null) {
        if (!$this->has($key)) {
            return $default;
        }
        
        $this->accessTimes[$key] = time();
        return $this->cache[$key];
    }
    
    public function set(string $key, $value, int $ttl = 3600): bool {
        // Evict if cache is full
        if (count($this->cache) >= $this->maxSize && !isset($this->cache[$key])) {
            $this->evictLeastRecentlyUsed();
        }
        
        $this->cache[$key] = $value;
        $this->ttl[$key] = time() + $ttl;
        $this->accessTimes[$key] = time();
        
        return true;
    }
    
    public function delete(string $key): bool {
        unset($this->cache[$key]);
        unset($this->ttl[$key]);
        unset($this->accessTimes[$key]);
        return true;
    }
    
    public function clear(): bool {
        $this->cache = [];
        $this->ttl = [];
        $this->accessTimes = [];
        return true;
    }
    
    public function has(string $key): bool {
        return isset($this->cache[$key]) && 
               isset($this->ttl[$key]) && 
               $this->ttl[$key] > time();
    }
    
    private function evictLeastRecentlyUsed(): void {
        if (empty($this->accessTimes)) {
            return;
        }
        
        $oldestKey = array_keys($this->accessTimes, min($this->accessTimes))[0];
        $this->delete($oldestKey);
    }
    
    public function getStats(): array {
        return [
            'size' => count($this->cache),
            'max_size' => $this->maxSize,
            'hit_rate' => $this->calculateHitRate(),
            'memory_usage' => strlen(serialize($this->cache))
        ];
    }
    
    private function calculateHitRate(): float {
        // Simplified hit rate calculation
        return 0.85; // Would need to track hits/misses
    }
}

class FileCache implements CacheInterface {
    private string $cacheDir;
    private string $extension;
    
    public function __construct(string $cacheDir = '/tmp/cache', string $extension = '.cache') {
        $this->cacheDir = $cacheDir;
        $this->extension = $extension;
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function get(string $key, $default = null) {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        if ($data['expires'] < time()) {
            $this->delete($key);
            return $default;
        }
        
        return $data['value'];
    }
    
    public function set(string $key, $value, int $ttl = 3600): bool {
        $filename = $this->getFilename($key);
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        return file_put_contents($filename, serialize($data)) !== false;
    }
    
    public function delete(string $key): bool {
        $filename = $this->getFilename($key);
        return file_exists($filename) && unlink($filename);
    }
    
    public function clear(): bool {
        $files = glob($this->cacheDir . '*' . $this->extension);
        $success = true;
        
        foreach ($files as $file) {
            $success = $success && unlink($file);
        }
        
        return $success;
    }
    
    public function has(string $key): bool {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        if ($data['expires'] < time()) {
            $this->delete($key);
            return false;
        }
        
        return true;
    }
    
    private function getFilename(string $key): string {
        return $this->cacheDir . md5($key) . $this->extension;
    }
    
    public function cleanup(): int {
        $files = glob($this->cacheDir . '*' . $this->extension);
        $cleaned = 0;
        
        foreach ($files as $file) {
            $data = unserialize(file_get_contents($file));
            
            if ($data['expires'] < time()) {
                unlink($file);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
}

class RedisCache implements CacheInterface {
    private \Redis $redis;
    private string $prefix;
    
    public function __construct(\Redis $redis, string $prefix = 'cache:') {
        $this->redis = $redis;
        $this->prefix = $prefix;
    }
    
    public function get(string $key, $default = null) {
        $value = $this->redis->get($this->prefix . $key);
        
        if ($value === false) {
            return $default;
        }
        
        return unserialize($value);
    }
    
    public function set(string $key, $value, int $ttl = 3600): bool {
        return $this->redis->setex($this->prefix . $key, $ttl, serialize($value));
    }
    
    public function delete(string $key): bool {
        return $this->redis->del($this->prefix . $key) > 0;
    }
    
    public function clear(): bool {
        $keys = $this->redis->keys($this->prefix . '*');
        
        if (empty($keys)) {
            return true;
        }
        
        return $this->redis->del($keys) > 0;
    }
    
    public function has(string $key): bool {
        return $this->redis->exists($this->prefix . $key);
    }
    
    public function increment(string $key, int $value = 1): int {
        return $this->redis->incrBy($this->prefix . $key, $value);
    }
    
    public function decrement(string $key, int $value = 1): int {
        return $this->redis->decrBy($this->prefix . $key, $value);
    }
}

class MultiLevelCache implements CacheInterface {
    private array $caches = [];
    private array $stats = ['hits' => 0, 'misses' => 0];
    
    public function __construct(CacheInterface ...$caches) {
        $this->caches = $caches;
    }
    
    public function get(string $key, $default = null) {
        // Try each cache level
        foreach ($this->caches as $index => $cache) {
            $value = $cache->get($key);
            
            if ($value !== null) {
                $this->stats['hits']++;
                
                // Promote to higher levels
                $this->promoteToHigherLevels($key, $value, $index);
                
                return $value;
            }
        }
        
        $this->stats['misses']++;
        return $default;
    }
    
    public function set(string $key, $value, int $ttl = 3600): bool {
        $success = true;
        
        // Set in all cache levels
        foreach ($this->caches as $cache) {
            $success = $success && $cache->set($key, $value, $ttl);
        }
        
        return $success;
    }
    
    public function delete(string $key): bool {
        $success = true;
        
        foreach ($this->caches as $cache) {
            $success = $success && $cache->delete($key);
        }
        
        return $success;
    }
    
    public function clear(): bool {
        $success = true;
        
        foreach ($this->caches as $cache) {
            $success = $success && $cache->clear();
        }
        
        return $success;
    }
    
    public function has(string $key): bool {
        foreach ($this->caches as $cache) {
            if ($cache->has($key)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function promoteToHigherLevels(string $key, $value, int $foundAt): void {
        // Promote to higher (earlier) cache levels
        for ($i = 0; $i < $foundAt; $i++) {
            $this->caches[$i]->set($key, $value);
        }
    }
    
    public function getStats(): array {
        $total = $this->stats['hits'] + $this->stats['misses'];
        
        return array_merge($this->stats, [
            'hit_rate' => $total > 0 ? ($this->stats['hits'] / $total) * 100 : 0,
            'total_requests' => $total
        ]);
    }
}

class CacheWarmer {
    private CacheInterface $cache;
    private array $warmupData = [];
    
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
    }
    
    public function addWarmupData(string $key, callable $dataGenerator, int $ttl = 3600): void {
        $this->warmupData[$key] = [
            'generator' => $dataGenerator,
            'ttl' => $ttl
        ];
    }
    
    public function warmup(): void {
        foreach ($this->warmupData as $key => $data) {
            if (!$this->cache->has($key)) {
                $value = ($data['generator'])();
                $this->cache->set($key, $value, $data['ttl']);
            }
        }
    }
    
    public function warmupAsync(): void {
        // Simulate async warmup (would use actual async in production)
        foreach ($this->warmupData as $key => $data) {
            register_shutdown_function(function() use ($key, $data) {
                if (!$this->cache->has($key)) {
                    $value = ($data['generator'])();
                    $this->cache->set($key, $value, $data['ttl']);
                }
            });
        }
    }
}

class CacheInvalidator {
    private CacheInterface $cache;
    private array $invalidationRules = [];
    
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
    }
    
    public function addInvalidationRule(string $pattern, callable $callback): void {
        $this->invalidationRules[$pattern] = $callback;
    }
    
    public function invalidate(string $event, array $context = []): void {
        foreach ($this->invalidationRules as $pattern => $callback) {
            if (fnmatch($pattern, $event)) {
                $keys = $callback($context);
                
                if (is_array($keys)) {
                    foreach ($keys as $key) {
                        $this->cache->delete($key);
                    }
                }
            }
        }
    }
    
    public function invalidateByTag(string $tag): void {
        // Tag-based invalidation (would need tag support in cache)
        $this->invalidate("tag:$tag");
    }
}

// Smart cache with automatic refresh
class SmartCache {
    private CacheInterface $cache;
    private array $refreshCallbacks = [];
    
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
    }
    
    public function get(string $key, callable $refreshCallback, int $ttl = 3600) {
        $value = $this->cache->get($key);
        
        if ($value === null) {
            $value = $refreshCallback();
            $this->cache->set($key, $value, $ttl);
            $this->refreshCallbacks[$key] = $refreshCallback;
        }
        
        return $value;
    }
    
    public function refresh(string $key): bool {
        if (!isset($this->refreshCallbacks[$key])) {
            return false;
        }
        
        $value = ($this->refreshCallbacks[$key])();
        return $this->cache->set($key, $value);
    }
    
    public function refreshAll(): int {
        $refreshed = 0;
        
        foreach ($this->refreshCallbacks as $key => $callback) {
            if ($this->refresh($key)) {
                $refreshed++;
            }
        }
        
        return $refreshed;
    }
}

// Usage examples
// Setup multi-level cache
$memoryCache = new InMemoryCache(100);
$fileCache = new FileCache('/tmp/php_cache');
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redisCache = new RedisCache($redis);

$multiCache = new MultiLevelCache($memoryCache, $fileCache, $redisCache);

// Cache warming
$warmer = new CacheWarmer($multiCache);
$warmer->addWarmupData('user:1', function() {
    return ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];
});

$warmer->addWarmupData('config:app', function() {
    return ['name' => 'MyApp', 'version' => '1.0.0', 'debug' => false];
});

$warmer->warmup();

// Smart cache usage
$smartCache = new SmartCache($multiCache);

$userData = $smartCache->get('user:123', function() {
    // Simulate database query
    return ['id' => 123, 'name' => 'Jane Smith', 'email' => 'jane@example.com'];
}, 1800); // 30 minutes

echo "User data: " . json_encode($userData) . "\n";

// Cache invalidation
$invalidator = new CacheInvalidator($multiCache);
$invalidator->addInvalidationRule('user:*', function($context) {
    $userId = $context['user_id'] ?? null;
    return $userId ? ["user:$userId"] : [];
});

$invalidator->invalidate('user:123', ['user_id' => 123]);

// Performance testing
$start = microtime(true);
for ($i = 0; $i < 1000; $i++) {
    $multiCache->get("test:$i", "value $i");
}
$duration = microtime(true) - $start;

echo "Cache performance test: " . number_format($duration * 1000, 2) . " ms for 1000 operations\n";

// Cache statistics
$stats = $multiCache->getStats();
echo "Cache hit rate: " . number_format($stats['hit_rate'], 2) . "%\n";
echo "Total requests: {$stats['total_requests']}\n";
?>
```

## Database Optimization

### Query and Connection Optimization
```php
<?php
class DatabaseOptimizer {
    private PDO $pdo;
    private array $queryLog = [];
    private array $slowQueries = [];
    private float $slowQueryThreshold = 0.1; // 100ms
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    
    public function optimizedQuery(string $sql, array $params = []): array {
        $start = microtime(true);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $duration = microtime(true) - $start;
            $this->logQuery($sql, $params, $duration, $result);
            
            return $result;
        } catch (PDOException $e) {
            $this->logQuery($sql, $params, microtime(true) - $start, [], $e);
            throw $e;
        }
    }
    
    public function optimizedInsert(string $table, array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $start = microtime(true);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            $id = $this->pdo->lastInsertId();
            
            $duration = microtime(true) - $start;
            $this->logQuery($sql, $data, $duration, ['inserted_id' => $id]);
            
            return (int)$id;
        } catch (PDOException $e) {
            $this->logQuery($sql, $data, microtime(true) - $start, [], $e);
            throw $e;
        }
    }
    
    public function batchInsert(string $table, array $records): array {
        if (empty($records)) {
            return [];
        }
        
        $columns = array_keys($records[0]);
        $columnList = implode(', ', $columns);
        
        $values = [];
        $placeholders = [];
        
        foreach ($records as $i => $record) {
            $recordPlaceholders = [];
            foreach ($columns as $column) {
                $placeholder = ":{$column}_{$i}";
                $values[$placeholder] = $record[$column];
                $recordPlaceholders[] = $placeholder;
            }
            $placeholders[] = '(' . implode(', ', $recordPlaceholders) . ')';
        }
        
        $sql = "INSERT INTO $table ($columnList) VALUES " . implode(', ', $placeholders);
        
        $start = microtime(true);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            
            $duration = microtime(true) - $start;
            $this->logQuery($sql, [], $duration, ['batch_size' => count($records)]);
            
            // Get inserted IDs (MySQL specific)
            $firstId = $this->pdo->lastInsertId();
            $ids = [];
            
            for ($i = 0; $i < count($records); $i++) {
                $ids[] = $firstId + $i;
            }
            
            return $ids;
        } catch (PDOException $e) {
            $this->logQuery($sql, [], microtime(true) - $start, [], $e);
            throw $e;
        }
    }
    
    public function optimizedUpdate(string $table, array $data, array $where): int {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :set_$key";
        }
        
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = :where_$key";
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE " . implode(' AND ', $whereClause);
        
        $params = [];
        foreach ($data as $key => $value) {
            $params[":set_$key"] = $value;
        }
        foreach ($where as $key => $value) {
            $params[":where_$key"] = $value;
        }
        
        $start = microtime(true);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $affected = $stmt->rowCount();
            
            $duration = microtime(true) - $start;
            $this->logQuery($sql, $params, $duration, ['affected_rows' => $affected]);
            
            return $affected;
        } catch (PDOException $e) {
            $this->logQuery($sql, $params, microtime(true) - $start, [], $e);
            throw $e;
        }
    }
    
    public function optimizedDelete(string $table, array $where): int {
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = :$key";
        }
        
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereClause);
        
        $start = microtime(true);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($where);
            $affected = $stmt->rowCount();
            
            $duration = microtime(true) - $start;
            $this->logQuery($sql, $where, $duration, ['affected_rows' => $affected]);
            
            return $affected;
        } catch (PDOException $e) {
            $this->logQuery($sql, $where, microtime(true) - $start, [], $e);
            throw $e;
        }
    }
    
    private function logQuery(string $sql, array $params, float $duration, array $result = [], ?PDOException $error = null): void {
        $logEntry = [
            'sql' => $sql,
            'params' => $params,
            'duration' => $duration,
            'timestamp' => microtime(true),
            'result_count' => is_array($result) ? count($result) : ($result['affected_rows'] ?? 0),
            'error' => $error ? $error->getMessage() : null
        ];
        
        $this->queryLog[] = $logEntry;
        
        if ($duration > $this->slowQueryThreshold) {
            $this->slowQueries[] = $logEntry;
        }
        
        // Keep only last 1000 queries in memory
        if (count($this->queryLog) > 1000) {
            $this->queryLog = array_slice($this->queryLog, -1000);
        }
    }
    
    public function getQueryStats(): array {
        if (empty($this->queryLog)) {
            return ['message' => 'No queries logged'];
        }
        
        $durations = array_column($this->queryLog, 'duration');
        $resultCounts = array_column($this->queryLog, 'result_count');
        
        return [
            'total_queries' => count($this->queryLog),
            'total_duration' => array_sum($durations),
            'avg_duration' => array_sum($durations) / count($durations),
            'min_duration' => min($durations),
            'max_duration' => max($durations),
            'slow_queries' => count($this->slowQueries),
            'total_results' => array_sum($resultCounts),
            'avg_results' => array_sum($resultCounts) / count($resultCounts)
        ];
    }
    
    public function getSlowQueries(): array {
        return $this->slowQueries;
    }
    
    public function analyzeQuery(string $sql): array {
        // Basic query analysis
        $analysis = [
            'type' => $this->getQueryType($sql),
            'has_join' => stripos($sql, 'JOIN') !== false,
            'has_subquery' => $this->hasSubquery($sql),
            'has_aggregate' => $this->hasAggregate($sql),
            'has_where' => stripos($sql, 'WHERE') !== false,
            'has_order_by' => stripos($sql, 'ORDER BY') !== false,
            'has_group_by' => stripos($sql, 'GROUP BY') !== false,
            'has_limit' => stripos($sql, 'LIMIT') !== false,
            'estimated_complexity' => $this->estimateComplexity($sql)
        ];
        
        return $analysis;
    }
    
    private function getQueryType(string $sql): string {
        $sql = strtoupper(trim($sql));
        
        if (strpos($sql, 'SELECT') === 0) return 'SELECT';
        if (strpos($sql, 'INSERT') === 0) return 'INSERT';
        if (strpos($sql, 'UPDATE') === 0) return 'UPDATE';
        if (strpos($sql, 'DELETE') === 0) return 'DELETE';
        
        return 'OTHER';
    }
    
    private function hasSubquery(string $sql): bool {
        return preg_match('/\(\s*SELECT\b/i', $sql) > 0;
    }
    
    private function hasAggregate(string $sql): bool {
        $aggregates = ['COUNT', 'SUM', 'AVG', 'MIN', 'MAX'];
        
        foreach ($aggregates as $agg) {
            if (stripos($sql, $agg) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    private function estimateComplexity(string $sql): string {
        $complexity = 0;
        
        if ($this->hasSubquery($sql)) $complexity += 3;
        if (stripos($sql, 'JOIN') !== false) $complexity += 2;
        if ($this->hasAggregate($sql)) $complexity += 1;
        if (stripos($sql, 'ORDER BY') !== false) $complexity += 1;
        if (stripos($sql, 'GROUP BY') !== false) $complexity += 1;
        
        if ($complexity >= 4) return 'HIGH';
        if ($complexity >= 2) return 'MEDIUM';
        return 'LOW';
    }
}

class ConnectionPool {
    private array $connections = [];
    private array $available = [];
    private array $inUse = [];
    private int $maxConnections;
    private string $dsn;
    private string $username;
    private string $password;
    private array $options;
    
    public function __construct(string $dsn, string $username, string $password, array $options = [], int $maxConnections = 10) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
        $this->maxConnections = $maxConnections;
    }
    
    public function getConnection(): PDO {
        // Try to get an available connection
        if (!empty($this->available)) {
            $connectionId = array_pop($this->available);
            $this->inUse[$connectionId] = true;
            return $this->connections[$connectionId];
        }
        
        // Create new connection if under limit
        if (count($this->connections) < $this->maxConnections) {
            $connection = new PDO($this->dsn, $this->username, $this->password, $this->options);
            $connectionId = uniqid();
            $this->connections[$connectionId] = $connection;
            $this->inUse[$connectionId] = true;
            return $connection;
        }
        
        // Wait for available connection (simplified)
        throw new \Exception('Maximum connections reached');
    }
    
    public function releaseConnection(PDO $connection): void {
        foreach ($this->connections as $id => $conn) {
            if ($conn === $connection) {
                unset($this->inUse[$id]);
                $this->available[] = $id;
                break;
            }
        }
    }
    
    public function getPoolStats(): array {
        return [
            'total_connections' => count($this->connections),
            'available_connections' => count($this->available),
            'in_use_connections' => count($this->inUse),
            'max_connections' => $this->maxConnections
        ];
    }
    
    public function closeAll(): void {
        foreach ($this->connections as $connection) {
            $connection = null;
        }
        
        $this->connections = [];
        $this->available = [];
        $this->inUse = [];
    }
}

class QueryBuilder {
    private array $select = ['*'];
    private array $from = [];
    private array $where = [];
    private array $join = [];
    private array $groupBy = [];
    private array $having = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $bindings = [];
    
    public function select(string ...$columns): self {
        $this->select = $columns;
        return $this;
    }
    
    public function from(string $table, string $alias = null): self {
        $this->from = ['table' => $table, 'alias' => $alias];
        return $this;
    }
    
    public function where(string $column, string $operator, $value): self {
        $this->where[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }
    
    public function whereIn(string $column, array $values): self {
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        $this->where[] = "$column IN ($placeholders)";
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }
    
    public function join(string $table, string $firstColumn, string $operator, string $secondColumn, string $type = 'INNER'): self {
        $this->join[] = "$type JOIN $table ON $firstColumn $operator $secondColumn";
        return $this;
    }
    
    public function leftJoin(string $table, string $firstColumn, string $operator, string $secondColumn): self {
        return $this->join($table, $firstColumn, $operator, $secondColumn, 'LEFT');
    }
    
    public function groupBy(string ...$columns): self {
        $this->groupBy = $columns;
        return $this;
    }
    
    public function having(string $column, string $operator, $value): self {
        $this->having[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orderBy[] = "$column $direction";
        return $this;
    }
    
    public function limit(int $limit): self {
        $this->limit = $limit;
        return $this;
    }
    
    public function offset(int $offset): self {
        $this->offset = $offset;
        return $this;
    }
    
    public function getSQL(): string {
        $sql = "SELECT " . implode(', ', $this->select);
        
        if (!empty($this->from)) {
            $sql .= " FROM {$this->from['table']}";
            if ($this->from['alias']) {
                $sql .= " AS {$this->from['alias']}";
            }
        }
        
        if (!empty($this->join)) {
            $sql .= " " . implode(' ', $this->join);
        }
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        
        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY " . implode(', ', $this->groupBy);
        }
        
        if (!empty($this->having)) {
            $sql .= " HAVING " . implode(' AND ', $this->having);
        }
        
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        return $sql;
    }
    
    public function getBindings(): array {
        return $this->bindings;
    }
    
    public function reset(): self {
        $this->select = ['*'];
        $this->from = [];
        $this->where = [];
        $this->join = [];
        $this->groupBy = [];
        $this->having = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->bindings = [];
        
        return $this;
    }
}

// Usage examples
$pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'password');
$optimizer = new DatabaseOptimizer($pdo);

// Optimized queries
$users = $optimizer->optimizedQuery(
    "SELECT * FROM users WHERE status = ? AND created_at > ?",
    ['active', '2023-01-01']
);

// Batch insert
$records = [
    ['name' => 'John', 'email' => 'john@example.com', 'age' => 30],
    ['name' => 'Jane', 'email' => 'jane@example.com', 'age' => 25],
    ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 35]
];

$insertedIds = $optimizer->batchInsert('users', $records);

// Query analysis
$query = "SELECT u.*, p.title FROM users u LEFT JOIN posts p ON u.id = p.user_id WHERE u.status = 'active' ORDER BY u.created_at DESC LIMIT 10";
$analysis = $optimizer->analyzeQuery($query);

echo "Query analysis:\n";
print_r($analysis);

// Query statistics
$stats = $optimizer->getQueryStats();
echo "\nQuery statistics:\n";
print_r($stats);

// Connection pooling
$pool = new ConnectionPool('mysql:host=localhost;dbname=test', 'user', 'password');

try {
    $connection = $pool->getConnection();
    // Use connection
    $pool->releaseConnection($connection);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Query builder
$queryBuilder = new QueryBuilder();
$sql = $queryBuilder
    ->select('u.id', 'u.name', 'COUNT(p.id) as post_count')
    ->from('users', 'u')
    ->leftJoin('posts', 'u.id', '=', 'p.user_id')
    ->where('u.status', '=', 'active')
    ->groupBy('u.id', 'u.name')
    ->having('post_count', '>', 0)
    ->orderBy('post_count', 'DESC')
    ->limit(10)
    ->getSQL();

echo "\nGenerated SQL: $sql\n";
echo "Bindings: " . json_encode($queryBuilder->getBindings()) . "\n";
?>
```

## Performance Monitoring

### Application Performance Monitoring
```php
<?php
class PerformanceMonitor {
    private array $metrics = [];
    private array $timers = [];
    private array $counters = [];
    private array $gauges = [];
    private bool $enabled = true;
    
    public function enable(): void {
        $this->enabled = true;
    }
    
    public function disable(): void {
        $this->enabled = false;
    }
    
    public function startTimer(string $name): void {
        if (!$this->enabled) return;
        
        $this->timers[$name] = [
            'start' => microtime(true),
            'start_memory' => memory_get_usage(true)
        ];
    }
    
    public function endTimer(string $name): void {
        if (!$this->enabled || !isset($this->timers[$name])) return;
        
        $timer = $this->timers[$name];
        $duration = microtime(true) - $timer['start'];
        $memoryUsed = memory_get_usage(true) - $timer['start_memory'];
        
        $this->metrics[$name] = [
            'duration' => $duration,
            'memory_used' => $memoryUsed,
            'timestamp' => microtime(true)
        ];
        
        unset($this->timers[$name]);
    }
    
    public function incrementCounter(string $name, int $value = 1): void {
        if (!$this->enabled) return;
        
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = 0;
        }
        
        $this->counters[$name] += $value;
    }
    
    public function setGauge(string $name, $value): void {
        if (!$this->enabled) return;
        
        $this->gauges[$name] = [
            'value' => $value,
            'timestamp' => microtime(true)
        ];
    }
    
    public function recordExecutionTime(string $operation, callable $callback) {
        if (!$this->enabled) {
            return $callback();
        }
        
        $this->startTimer($operation);
        
        try {
            $result = $callback();
            $this->incrementCounter("{$operation}_success");
            return $result;
        } catch (Exception $e) {
            $this->incrementCounter("{$operation}_error");
            throw $e;
        } finally {
            $this->endTimer($operation);
        }
    }
    
    public function getMetrics(): array {
        return [
            'timers' => $this->metrics,
            'counters' => $this->counters,
            'gauges' => $this->gauges,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'timestamp' => microtime(true)
        ];
    }
    
    public function getReport(): array {
        $report = [
            'summary' => $this->generateSummary(),
            'slow_operations' => $this->getSlowOperations(),
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true)
            ],
            'counters' => $this->counters,
            'gauges' => $this->gauges
        ];
        
        return $report;
    }
    
    private function generateSummary(): array {
        $summary = [];
        
        foreach ($this->metrics as $name => $metric) {
            $summary[$name] = [
                'avg_duration' => $metric['duration'],
                'avg_memory' => $metric['memory_used'],
                'executions' => 1
            ];
        }
        
        return $summary;
    }
    
    private function getSlowOperations(): array {
        $slow = [];
        
        foreach ($this->metrics as $name => $metric) {
            if ($metric['duration'] > 0.1) { // 100ms threshold
                $slow[] = [
                    'name' => $name,
                    'duration' => $metric['duration'],
                    'memory_used' => $metric['memory_used']
                ];
            }
        }
        
        // Sort by duration
        usort($slow, fn($a, $b) => $b['duration'] <=> $a['duration']);
        
        return array_slice($slow, 0, 10); // Top 10 slow operations
    }
    
    public function reset(): void {
        $this->metrics = [];
        $this->timers = [];
        $this->counters = [];
        $this->gauges = [];
    }
}

class RequestMonitor {
    private PerformanceMonitor $monitor;
    private array $requestStart;
    
    public function __construct(PerformanceMonitor $monitor) {
        $this->monitor = $monitor;
        $this->requestStart = [
            'time' => microtime(true),
            'memory' => memory_get_usage(true)
        ];
    }
    
    public function startRequest(): void {
        $this->monitor->startTimer('request_total');
        $this->monitor->setGauge('request_start_time', $this->requestStart['time']);
        $this->monitor->setGauge('request_start_memory', $this->requestStart['memory']);
    }
    
    public function endRequest(): void {
        $this->monitor->endTimer('request_total');
        
        $duration = microtime(true) - $this->requestStart['time'];
        $memoryUsed = memory_get_usage(true) - $this->requestStart['memory'];
        
        $this->monitor->setGauge('request_duration', $duration);
        $this->monitor->setGauge('request_memory_used', $memoryUsed);
        $this->monitor->setGauge('request_memory_peak', memory_get_peak_usage(true));
    }
    
    public function monitorDatabase(callable $callback) {
        return $this->monitor->recordExecutionTime('database', $callback);
    }
    
    public function monitorCache(callable $callback) {
        return $this->monitor->recordExecutionTime('cache', $callback);
    }
    
    public function monitorExternal(callable $callback) {
        return $this->monitor->recordExecutionTime('external_api', $callback);
    }
    
    public function incrementCacheHit(): void {
        $this->monitor->incrementCounter('cache_hits');
    }
    
    public function incrementCacheMiss(): void {
        $this->monitor->incrementCounter('cache_misses');
    }
    
    public function incrementError(string $type = 'general'): void {
        $this->monitor->incrementCounter("errors_{$type}");
    }
    
    public function getRequestReport(): array {
        $metrics = $this->monitor->getMetrics();
        
        return [
            'request_id' => uniqid(),
            'timestamp' => date('Y-m-d H:i:s'),
            'duration' => $metrics['gauges']['request_duration']['value'] ?? 0,
            'memory_used' => $metrics['gauges']['request_memory_used']['value'] ?? 0,
            'memory_peak' => $metrics['gauges']['request_memory_peak']['value'] ?? 0,
            'cache_hits' => $metrics['counters']['cache_hits'] ?? 0,
            'cache_misses' => $metrics['counters']['cache_misses'] ?? 0,
            'cache_hit_rate' => $this->calculateCacheHitRate($metrics),
            'errors' => $this->getErrorCount($metrics)
        ];
    }
    
    private function calculateCacheHitRate(array $metrics): float {
        $hits = $metrics['counters']['cache_hits'] ?? 0;
        $misses = $metrics['counters']['cache_misses'] ?? 0;
        $total = $hits + $misses;
        
        return $total > 0 ? ($hits / $total) * 100 : 0;
    }
    
    private function getErrorCount(array $metrics): int {
        $errors = 0;
        
        foreach ($metrics['counters'] as $key => $count) {
            if (strpos($key, 'errors_') === 0) {
                $errors += $count;
            }
        }
        
        return $errors;
    }
}

class AlertManager {
    private array $alerts = [];
    private array $thresholds = [];
    
    public function setThreshold(string $metric, $value, string $operator = '>', int $window = 60): void {
        $this->thresholds[$metric] = [
            'value' => $value,
            'operator' => $operator,
            'window' => $window,
            'last_triggered' => 0
        ];
    }
    
    public function checkAlerts(array $metrics): array {
        $triggeredAlerts = [];
        
        foreach ($this->thresholds as $metric => $threshold) {
            if (!isset($metrics[$metric])) {
                continue;
            }
            
            $currentValue = $metrics[$metric];
            $shouldAlert = false;
            
            switch ($threshold['operator']) {
                case '>':
                    $shouldAlert = $currentValue > $threshold['value'];
                    break;
                case '<':
                    $shouldAlert = $currentValue < $threshold['value'];
                    break;
                case '>=':
                    $shouldAlert = $currentValue >= $threshold['value'];
                    break;
                case '<=':
                    $shouldAlert = $currentValue <= $threshold['value'];
                    break;
                case '==':
                    $shouldAlert = $currentValue == $threshold['value'];
                    break;
            }
            
            if ($shouldAlert && (time() - $threshold['last_triggered']) > $threshold['window']) {
                $alert = [
                    'metric' => $metric,
                    'current_value' => $currentValue,
                    'threshold' => $threshold['value'],
                    'operator' => $threshold['operator'],
                    'timestamp' => time(),
                    'message' => "Alert: $metric {$threshold['operator']} {$threshold['value']} (current: $currentValue)"
                ];
                
                $triggeredAlerts[] = $alert;
                $this->alerts[] = $alert;
                
                $threshold['last_triggered'] = time();
            }
        }
        
        return $triggeredAlerts;
    }
    
    public function getAlerts(): array {
        return $this->alerts;
    }
    
    public function clearAlerts(): void {
        $this->alerts = [];
    }
}

class PerformanceProfiler {
    private array $profiles = [];
    private bool $enabled = false;
    
    public function enable(): void {
        $this->enabled = true;
    }
    
    public function disable(): void {
        $this->enabled = false;
    }
    
    public function profile(string $name, callable $callback) {
        if (!$this->enabled) {
            return $callback();
        }
        
        $start = microtime(true);
        $startMemory = memory_get_usage(true);
        $startCpu = $this->getCpuUsage();
        
        try {
            $result = $callback();
            
            $profile = [
                'name' => $name,
                'duration' => microtime(true) - $start,
                'memory_used' => memory_get_usage(true) - $startMemory,
                'cpu_used' => $this->getCpuUsage() - $startCpu,
                'success' => true,
                'timestamp' => microtime(true)
            ];
        } catch (Exception $e) {
            $profile = [
                'name' => $name,
                'duration' => microtime(true) - $start,
                'memory_used' => memory_get_usage(true) - $startMemory,
                'cpu_used' => $this->getCpuUsage() - $startCpu,
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => microtime(true)
            ];
            
            throw $e;
        }
        
        $this->profiles[] = $profile;
        
        return $result;
    }
    
    private function getCpuUsage(): float {
        // Simplified CPU usage calculation
        $usage = getrusage();
        return $usage['ru_utime.tv_sec'] + $usage['ru_utime.tv_usec'] / 1000000;
    }
    
    public function getProfiles(): array {
        return $this->profiles;
    }
    
    public function getProfileReport(): array {
        if (empty($this->profiles)) {
            return ['message' => 'No profiles available'];
        }
        
        $report = [
            'total_profiles' => count($this->profiles),
            'total_duration' => array_sum(array_column($this->profiles, 'duration')),
            'avg_duration' => array_sum(array_column($this->profiles, 'duration')) / count($this->profiles),
            'total_memory' => array_sum(array_column($this->profiles, 'memory_used')),
            'avg_memory' => array_sum(array_column($this->profiles, 'memory_used')) / count($this->profiles),
            'success_rate' => (array_sum(array_column($this->profiles, 'success')) / count($this->profiles)) * 100,
            'slowest' => $this->getSlowestProfiles(5),
            'memory_intensive' => $this->getMemoryIntensiveProfiles(5)
        ];
        
        return $report;
    }
    
    private function getSlowestProfiles(int $limit): array {
        $sorted = $this->profiles;
        usort($sorted, fn($a, $b) => $b['duration'] <=> $a['duration']);
        
        return array_slice($sorted, 0, $limit);
    }
    
    private function getMemoryIntensiveProfiles(int $limit): array {
        $sorted = $this->profiles;
        usort($sorted, fn($a, $b) => $b['memory_used'] <=> $a['memory_used']);
        
        return array_slice($sorted, 0, $limit);
    }
    
    public function clearProfiles(): void {
        $this->profiles = [];
    }
}

// Usage examples
$monitor = new PerformanceMonitor();
$requestMonitor = new RequestMonitor($monitor);
$alertManager = new AlertManager();
$profiler = new PerformanceProfiler();

// Set up alerts
$alertManager->setThreshold('request_duration', 1.0, '>', 300); // Alert if request > 1s
$alertManager->setThreshold('memory_usage', 100 * 1024 * 1024, '>', 300); // Alert if memory > 100MB

// Start monitoring
$requestMonitor->startRequest();
$profiler->enable();

// Simulate some operations
$profiler->profile('database_query', function() use ($requestMonitor) {
    return $requestMonitor->monitorDatabase(function() {
        usleep(50000); // Simulate 50ms database query
        return ['user' => 'John Doe'];
    });
});

$profiler->profile('cache_operation', function() use ($requestMonitor) {
    return $requestMonitor->monitorCache(function() {
        usleep(10000); // Simulate 10ms cache operation
        $requestMonitor->incrementCacheHit();
        return 'cached_data';
    });
});

$profiler->profile('external_api', function() use ($requestMonitor) {
    return $requestMonitor->monitorExternal(function() {
        usleep(200000); // Simulate 200ms API call
        return ['status' => 'success'];
    });
});

// Simulate some cache misses
for ($i = 0; $i < 3; $i++) {
    $requestMonitor->incrementCacheMiss();
}

// End monitoring
$requestMonitor->endRequest();

// Generate reports
$requestReport = $requestMonitor->getRequestReport();
$profileReport = $profiler->getProfileReport();

echo "=== Request Report ===\n";
echo "Request ID: {$requestReport['request_id']}\n";
echo "Duration: " . number_format($requestReport['duration'] * 1000, 2) . " ms\n";
echo "Memory Used: " . number_format($requestReport['memory_used'] / 1024 / 1024, 2) . " MB\n";
echo "Cache Hit Rate: " . number_format($requestReport['cache_hit_rate'], 2) . "%\n";

echo "\n=== Profile Report ===\n";
echo "Total Profiles: {$profileReport['total_profiles']}\n";
echo "Total Duration: " . number_format($profileReport['total_duration'] * 1000, 2) . " ms\n";
echo "Success Rate: " . number_format($profileReport['success_rate'], 2) . "%\n";

echo "\n=== Slowest Operations ===\n";
foreach ($profileReport['slowest'] as $profile) {
    echo "- {$profile['name']}: " . number_format($profile['duration'] * 1000, 2) . " ms\n";
}

// Check alerts
$metrics = $monitor->getMetrics();
$alerts = $alertManager->checkAlerts([
    'request_duration' => $metrics['gauges']['request_duration']['value'] ?? 0,
    'memory_usage' => $metrics['gauges']['request_memory_used']['value'] ?? 0
]);

if (!empty($alerts)) {
    echo "\n=== Alerts ===\n";
    foreach ($alerts as $alert) {
        echo $alert['message'] . "\n";
    }
}
?>
```

## Summary

PHP Performance Optimization provides:

**Code Optimization:**
- Efficient string concatenation and array access
- Optimized loops and function calls
- Regular expression optimization
- Memory-efficient processing with generators

**Memory Management:**
- Memory usage monitoring and profiling
- Efficient collection classes
- Stream processing for large files
- Object pooling and weak references

**Caching Strategies:**
- Multi-level caching (memory, file, Redis)
- Cache warming and invalidation
- Smart caching with automatic refresh
- Performance monitoring and statistics

**Database Optimization:**
- Query optimization and analysis
- Batch operations and transactions
- Connection pooling
- Query builders for efficient SQL generation

**Performance Monitoring:**
- Real-time performance metrics
- Request monitoring and profiling
- Alert management for thresholds
- Comprehensive reporting and analysis

**Key Benefits:**
- Reduced memory usage
- Faster execution times
- Better resource utilization
- Improved scalability
- Enhanced user experience

**Best Practices:**
- Profile before optimizing
- Use appropriate data structures
- Implement caching strategies
- Monitor performance continuously
- Optimize database queries
- Use connection pooling

Performance optimization is an ongoing process that requires continuous monitoring, profiling, and refinement to maintain optimal application performance.
