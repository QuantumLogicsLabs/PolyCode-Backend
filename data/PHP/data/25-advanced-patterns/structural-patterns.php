<?php
/**
 * Advanced Structural Design Patterns
 * 
 * Implementation of complex structural patterns beyond basic adapter and decorator.
 */

// Composite Pattern for File System
interface FileSystemComponent
{
    public function getName(): string;
    public function getSize(): int;
    public function add(FileSystemComponent $component): void;
    public function remove(FileSystemComponent $component): void;
    public function isDirectory(): bool;
    public function display(int $indent = 0): string;
    public function search(string $pattern): array;
}

class File implements FileSystemComponent
{
    private string $name;
    private string $content;
    
    public function __construct(string $name, string $content = '')
    {
        $this->name = $name;
        $this->content = $content;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getSize(): int
    {
        return strlen($this->content);
    }
    
    public function add(FileSystemComponent $component): void
    {
        throw new Exception("Cannot add to file");
    }
    
    public function remove(FileSystemComponent $component): void
    {
        throw new Exception("Cannot remove from file");
    }
    
    public function isDirectory(): bool
    {
        return false;
    }
    
    public function display(int $indent = 0): string
    {
        $indentStr = str_repeat('  ', $indent);
        return $indentStr . "📄 {$this->name} ({$this->getSize()} bytes)\n";
    }
    
    public function search(string $pattern): array
    {
        $results = [];
        if (fnmatch($pattern, $this->name)) {
            $results[] = $this;
        }
        return $results;
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}

class Directory implements FileSystemComponent
{
    private string $name;
    private array $children = [];
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getSize(): int
    {
        $totalSize = 0;
        foreach ($this->children as $child) {
            $totalSize += $child->getSize();
        }
        return $totalSize;
    }
    
    public function add(FileSystemComponent $component): void
    {
        $this->children[] = $component;
    }
    
    public function remove(FileSystemComponent $component): void
    {
        $key = array_search($component, $this->children, true);
        if ($key !== false) {
            unset($this->children[$key]);
            $this->children = array_values($this->children);
        }
    }
    
    public function isDirectory(): bool
    {
        return true;
    }
    
    public function display(int $indent = 0): string
    {
        $indentStr = str_repeat('  ', $indent);
        $output = $indentStr . "📁 {$this->name}/\n";
        
        foreach ($this->children as $child) {
            $output .= $child->display($indent + 1);
        }
        
        return $output;
    }
    
    public function search(string $pattern): array
    {
        $results = [];
        
        if (fnmatch($pattern, $this->name)) {
            $results[] = $this;
        }
        
        foreach ($this->children as $child) {
            $results = array_merge($results, $child->search($pattern));
        }
        
        return $results;
    }
    
    public function getChildren(): array
    {
        return $this->children;
    }
    
    public function findChild(string $name): ?FileSystemComponent
    {
        foreach ($this->children as $child) {
            if ($child->getName() === $name) {
                return $child;
            }
            if ($child->isDirectory()) {
                $found = $child->findChild($name);
                if ($found) {
                    return $found;
                }
            }
        }
        return null;
    }
}

// Flyweight Pattern for Character Rendering
class CharacterFlyweight
{
    private string $char;
    private string $font;
    private int $size;
    private string $color;
    
    public function __construct(string $char, string $font, int $size, string $color)
    {
        $this->char = $char;
        $this->font = $font;
        $this->size = $size;
        $this->color = $color;
    }
    
    public function render(int $x, int $y): string
    {
        return "Rendering '{$this->char}' at ($x, $y) with font: {$this->font}, size: {$this->size}, color: {$this->color}";
    }
    
    public function getChar(): string
    {
        return $this->char;
    }
    
    public function getFont(): string
    {
        return $this->font;
    }
    
    public function getSize(): int
    {
        return $this->size;
    }
    
    public function getColor(): string
    {
        return $this->color;
    }
}

class CharacterFlyweightFactory
{
    private array $flyweights = [];
    
    public function getFlyweight(string $char, string $font, int $size, string $color): CharacterFlyweight
    {
        $key = "$char:$font:$size:$color";
        
        if (!isset($this->flyweights[$key])) {
            $this->flyweights[$key] = new CharacterFlyweight($char, $font, $size, $color);
            echo "Created new flyweight for: $key\n";
        }
        
        return $this->flyweights[$key];
    }
    
    public function getFlyweightCount(): int
    {
        return count($this->flyweights);
    }
    
    public function getFlyweights(): array
    {
        return $this->flyweights;
    }
}

class CharacterContext
{
    private int $x;
    private int $y;
    private CharacterFlyweight $flyweight;
    
    public function __construct(int $x, int $y, CharacterFlyweight $flyweight)
    {
        $this->x = $x;
        $this->y = $y;
        $this->flyweight = $flyweight;
    }
    
    public function render(): string
    {
        return $this->flyweight->render($this->x, $this->y);
    }
    
    public function setPosition(int $x, int $y): void
    {
        $this->x = $x;
        $this->y = $y;
    }
    
    public function getPosition(): array
    {
        return [$this->x, $this->y];
    }
    
    public function getFlyweight(): CharacterFlyweight
    {
        return $this->flyweight;
    }
}

// Proxy Pattern for Database Access
interface DatabaseInterface
{
    public function query(string $sql, array $params = []): array;
    public function insert(string $table, array $data): int;
    public function update(string $table, array $data, array $where): int;
    public function delete(string $table, array $where): int;
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
}

class RealDatabase implements DatabaseInterface
{
    private PDO $pdo;
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }
    
    private function connect(): void
    {
        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']}";
        $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Database connection established\n";
    }
    
    public function query(string $sql, array $params = []): array
    {
        echo "Executing query: $sql\n";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_combine(array_keys($data), array_values($data)));
        
        return (int) $this->pdo->lastInsertId();
    }
    
    public function update(string $table, array $data, array $where): int
    {
        $setParts = [];
        $whereParts = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setParts[] = "$column = :set_$column";
            $params[":set_$column"] = $value;
        }
        
        foreach ($where as $column => $value) {
            $whereParts[] = "$column = :where_$column";
            $params[":where_$column"] = $value;
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $setParts) . " WHERE " . implode(' AND ', $whereParts);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
    
    public function delete(string $table, array $where): int
    {
        $whereParts = [];
        $params = [];
        
        foreach ($where as $column => $value) {
            $whereParts[] = "$column = :where_$column";
            $params[":where_$column"] = $value;
        }
        
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereParts);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
    
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
        echo "Transaction started\n";
    }
    
    public function commit(): void
    {
        $this->pdo->commit();
        echo "Transaction committed\n";
    }
    
    public function rollback(): void
    {
        $this->pdo->rollBack();
        echo "Transaction rolled back\n";
    }
}

class DatabaseProxy implements DatabaseInterface
{
    private ?RealDatabase $realDatabase = null;
    private array $config;
    private array $queryCache = [];
    private int $cacheHits = 0;
    private int $cacheMisses = 0;
    private array $accessLog = [];
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    private function getDatabase(): RealDatabase
    {
        if ($this->realDatabase === null) {
            $this->realDatabase = new RealDatabase($this->config);
        }
        return $this->realDatabase;
    }
    
    public function query(string $sql, array $params = []): array
    {
        $cacheKey = md5($sql . serialize($params));
        
        // Check cache first
        if (isset($this->queryCache[$cacheKey])) {
            $this->cacheHits++;
            echo "Cache hit for query: $sql\n";
            return $this->queryCache[$cacheKey];
        }
        
        $this->cacheMisses++;
        $this->logAccess('query', $sql);
        
        $result = $this->getDatabase()->query($sql, $params);
        
        // Cache the result
        $this->queryCache[$cacheKey] = $result;
        
        return $result;
    }
    
    public function insert(string $table, array $data): int
    {
        $this->logAccess('insert', $table);
        return $this->getDatabase()->insert($table, $data);
    }
    
    public function update(string $table, array $data, array $where): int
    {
        $this->logAccess('update', $table);
        return $this->getDatabase()->update($table, $data, $where);
    }
    
    public function delete(string $table, array $where): int
    {
        $this->logAccess('delete', $table);
        return $this->getDatabase()->delete($table, $where);
    }
    
    public function beginTransaction(): void
    {
        $this->logAccess('transaction', 'begin');
        $this->getDatabase()->beginTransaction();
    }
    
    public function commit(): void
    {
        $this->logAccess('transaction', 'commit');
        $this->getDatabase()->commit();
    }
    
    public function rollback(): void
    {
        $this->logAccess('transaction', 'rollback');
        $this->getDatabase()->rollback();
    }
    
    private function logAccess(string $operation, string $target): void
    {
        $this->accessLog[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'operation' => $operation,
            'target' => $target
        ];
    }
    
    public function getCacheStats(): array
    {
        return [
            'cache_hits' => $this->cacheHits,
            'cache_misses' => $this->cacheMisses,
            'cache_size' => count($this->queryCache),
            'hit_rate' => $this->cacheHits + $this->cacheMisses > 0 
                ? round(($this->cacheHits / ($this->cacheHits + $this->cacheMisses)) * 100, 2) 
                : 0
        ];
    }
    
    public function getAccessLog(): array
    {
        return $this->accessLog;
    }
    
    public function clearCache(): void
    {
        $this->queryCache = [];
        echo "Cache cleared\n";
    }
}

// Bridge Pattern for File Formats
interface FileFormatter
{
    public function format(array $data): string;
    public function parse(string $content): array;
}

class JSONFormatter implements FileFormatter
{
    public function format(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
    
    public function parse(string $content): array
    {
        return json_decode($content, true);
    }
}

class XMLFormatter implements FileFormatter
{
    public function format(array $data): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= $this->arrayToXml($data);
        return $xml;
    }
    
    private function arrayToXml(array $data, string $root = 'root'): string
    {
        $xml = "<$root>\n";
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $xml .= $this->arrayToXml($value, $key);
            } else {
                $xml .= "  <$key>" . htmlspecialchars($value) . "</$key>\n";
            }
        }
        $xml .= "</$root>\n";
        return $xml;
    }
    
    public function parse(string $content): array
    {
        // Simplified XML parsing
        $data = [];
        $xml = simplexml_load_string($content);
        
        if ($xml) {
            foreach ($xml->children() as $child) {
                $data[$child->getName()] = (string) $child;
            }
        }
        
        return $data;
    }
}

class CSVFormatter implements FileFormatter
{
    public function format(array $data): string
    {
        $csv = '';
        
        if (!empty($data)) {
            // Header
            $csv .= implode(',', array_keys($data[0])) . "\n";
            
            // Rows
            foreach ($data as $row) {
                $csv .= implode(',', array_map(function($value) {
                    return is_string($value) ? '"' . str_replace('"', '""', $value) . '"' : $value;
                }, $row)) . "\n";
            }
        }
        
        return $csv;
    }
    
    public function parse(string $content): array
    {
        $lines = explode("\n", trim($content));
        $headers = str_getcsv(array_shift($lines));
        $data = [];
        
        foreach ($lines as $line) {
            $row = str_getcsv($line);
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }
        
        return $data;
    }
}

abstract class FileHandler
{
    protected FileFormatter $formatter;
    protected string $filename;
    
    public function __construct(string $filename, FileFormatter $formatter)
    {
        $this->filename = $filename;
        $this->formatter = $formatter;
    }
    
    abstract public function save(array $data): void;
    abstract public function load(): array;
    
    public function getFilename(): string
    {
        return $this->filename;
    }
    
    public function getFormatter(): FileFormatter
    {
        return $this->formatter;
    }
}

class TextFileHandler extends FileHandler
{
    private array $metadata = [];
    
    public function __construct(string $filename, FileFormatter $formatter, array $metadata = [])
    {
        parent::__construct($filename, $formatter);
        $this->metadata = $metadata;
    }
    
    public function save(array $data): void
    {
        $content = $this->formatter->format($data);
        
        // Add metadata
        if (!empty($this->metadata)) {
            $metadataBlock = "# Metadata:\n";
            foreach ($this->metadata as $key => $value) {
                $metadataBlock .= "# $key: $value\n";
            }
            $content = $metadataBlock . "\n" . $content;
        }
        
        file_put_contents($this->filename, $content);
        echo "Data saved to {$this->filename}\n";
    }
    
    public function load(): array
    {
        if (!file_exists($this->filename)) {
            return [];
        }
        
        $content = file_get_contents($this->filename);
        
        // Remove metadata
        $content = preg_replace('/^#.*$\n/m', '', $content);
        $content = trim($content);
        
        return $this->formatter->parse($content);
    }
    
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}

class BinaryFileHandler extends FileHandler
{
    private bool $compressed = false;
    
    public function __construct(string $filename, FileFormatter $formatter, bool $compressed = false)
    {
        parent::__construct($filename, $formatter);
        $this->compressed = $compressed;
    }
    
    public function save(array $data): void
    {
        $content = $this->formatter->format($data);
        
        if ($this->compressed) {
            $content = gzencode($content);
        }
        
        file_put_contents($this->filename, $content);
        echo "Data saved to {$this->filename} (" . ($this->compressed ? 'compressed' : 'uncompressed') . ")\n";
    }
    
    public function load(): array
    {
        if (!file_exists($this->filename)) {
            return [];
        }
        
        $content = file_get_contents($this->filename);
        
        if ($this->compressed) {
            $content = gzdecode($content);
        }
        
        return $this->formatter->parse($content);
    }
    
    public function isCompressed(): bool
    {
        return $this->compressed;
    }
    
    public function setCompressed(bool $compressed): void
    {
        $this->compressed = $compressed;
    }
}

// Structural Patterns Examples
class StructuralPatternsExamples
{
    public function demonstrateComposite(): void
    {
        echo "Composite Pattern Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create file system structure
        $root = new Directory('/');
        
        $home = new Directory('home');
        $user = new Directory('user');
        $documents = new Directory('documents');
        $pictures = new Directory('pictures');
        
        $file1 = new File('readme.txt', 'Welcome to the system!');
        $file2 = new File('config.json', '{"theme": "dark", "language": "en"}');
        $file3 = new File('notes.txt', 'Meeting notes from today');
        $file4 = new File('photo1.jpg', 'binary_image_data_here');
        $file5 = new File('resume.pdf', 'resume_content_here');
        
        // Build hierarchy
        $root->add($home);
        $home->add($user);
        $user->add($documents);
        $user->add($pictures);
        
        $documents->add($file1);
        $documents->add($file2);
        $pictures->add($file4);
        $pictures->add($file5);
        $home->add($file3);
        
        // Display structure
        echo "File System Structure:\n";
        echo $root->display();
        
        // Calculate total size
        echo "\nTotal size: " . $root->getSize() . " bytes\n";
        
        // Search for files
        echo "\nSearching for files matching '*.txt':\n";
        $results = $root->search('*.txt');
        foreach ($results as $result) {
            echo "Found: " . $result->getName() . " (" . $result->getSize() . " bytes)\n";
        }
        
        // Find specific file
        echo "\nFinding file 'config.json':\n";
        $config = $root->findChild('config.json');
        if ($config) {
            echo "Found: " . $config->getName() . "\n";
            echo "Content: " . $config->getContent() . "\n";
        }
    }
    
    public function demonstrateFlyweight(): void
    {
        echo "\nFlyweight Pattern Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $factory = new CharacterFlyweightFactory();
        $contexts = [];
        
        // Create text document with many characters
        $text = "HELLO WORLD! This is a demonstration of the flyweight pattern.";
        
        $x = 0;
        $y = 0;
        
        echo "Processing text: \"$text\"\n\n";
        
        foreach (str_split($text) as $char) {
            if ($char === ' ') {
                $x += 10;
                continue;
            }
            
            if ($char === '!') {
                $y += 20;
                $x = 0;
                continue;
            }
            
            // Get flyweight with shared properties
            $flyweight = $factory->getFlyweight(
                $char,
                'Arial',
                12,
                'black'
            );
            
            // Create context with position
            $context = new CharacterContext($x, $y, $flyweight);
            $contexts[] = $context;
            
            echo $context->render() . "\n";
            
            $x += 15;
        }
        
        echo "\nFlyweight Statistics:\n";
        echo "Total characters: " . count($contexts) . "\n";
        echo "Unique flyweights: " . $factory->getFlyweightCount() . "\n";
        echo "Memory saved: " . (count($contexts) - $factory->getFlyweightCount()) . " objects\n";
    }
    
    public function demonstrateProxy(): void
    {
        echo "\nProxy Pattern Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $config = [
            'host' => 'localhost',
            'database' => 'test',
            'username' => 'user',
            'password' => 'password'
        ];
        
        $db = new DatabaseProxy($config);
        
        echo "First query (cache miss):\n";
        $result1 = $db->query('SELECT * FROM users WHERE id = ?', [1]);
        
        echo "\nSecond query (cache hit):\n";
        $result2 = $db->query('SELECT * FROM users WHERE id = ?', [1]);
        
        echo "\nThird query (different):\n";
        $result3 = $db->query('SELECT * FROM users WHERE id = ?', [2]);
        
        echo "\nCache Statistics:\n";
        $stats = $db->getCacheStats();
        foreach ($stats as $key => $value) {
            echo "  $key: $value\n";
        }
        
        echo "\nAccess Log:\n";
        $log = $db->getAccessLog();
        foreach (array_slice($log, -3) as $entry) {
            echo "  {$entry['timestamp']} - {$entry['operation']} - {$entry['target']}\n";
        }
        
        echo "\nTransaction Example:\n";
        $db->beginTransaction();
        $db->insert('users', ['name' => 'John', 'email' => 'john@example.com']);
        $db->commit();
        
        echo "\nFinal Cache Statistics:\n";
        $stats = $db->getCacheStats();
        foreach ($stats as $key => $value) {
            echo "  $key: $value\n";
        }
    }
    
    public function demonstrateBridge(): void
    {
        echo "\nBridge Pattern Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Test different formatter combinations
        $formatters = [
            'JSON' => new JSONFormatter(),
            'XML' => new XMLFormatter(),
            'CSV' => new CSVFormatter()
        ];
        
        $sampleData = [
            ['name' => 'John', 'age' => 30, 'city' => 'New York'],
            ['name' => 'Jane', 'age' => 25, 'city' => 'Los Angeles'],
            ['name' => 'Bob', 'age' => 35, 'city' => 'Chicago']
        ];
        
        foreach ($formatters as $formatName => $formatter) {
            echo "\nTesting $formatName format:\n";
            
            // Text file handler
            $textHandler = new TextFileHandler("data.$formatName", $formatter, [
                'author' => 'System',
                'created' => date('Y-m-d')
            ]);
            
            $textHandler->save($sampleData);
            $loadedData = $textHandler->load();
            
            echo "Metadata: " . json_encode($textHandler->getMetadata()) . "\n";
            echo "Loaded rows: " . count($loadedData) . "\n";
            
            // Binary file handler
            $binaryHandler = new BinaryFileHandler("data_binary.$formatName", $formatter, true);
            $binaryHandler->save($sampleData);
            $loadedBinaryData = $binaryHandler->load();
            
            echo "Binary compressed: " . ($binaryHandler->isCompressed() ? 'Yes' : 'No') . "\n";
            echo "Binary loaded rows: " . count($loadedBinaryData) . "\n";
        }
        
        // Demonstrate switching formatters
        echo "\nSwitching Formatters:\n";
        $handler = new TextFileHandler('switchable.txt', new JSONFormatter());
        
        echo "Saving as JSON:\n";
        $handler->save($sampleData);
        
        echo "\nSwitching to XML:\n";
        $handler = new TextFileHandler('switchable.txt', new XMLFormatter());
        $handler->save($sampleData);
        
        echo "\nSwitching to CSV:\n";
        $handler = new TextFileHandler('switchable.txt', new CSVFormatter());
        $handler->save($sampleData);
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nStructural Patterns Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Composite Pattern:\n";
        echo "   • Use for tree-like structures\n";
        echo "   • Ensure consistent interface for all components\n";
        echo "   • Handle edge cases for leaf vs composite objects\n";
        echo "   • Consider performance for large trees\n";
        echo "   • Use for UI components, file systems, etc.\n\n";
        
        echo "2. Flyweight Pattern:\n";
        echo "   • Use when many similar objects are needed\n";
        echo "   • Separate intrinsic from extrinsic state\n";
        echo "   • Use factory to manage flyweights\n";
        echo "   • Consider thread safety for shared objects\n";
        echo "   • Monitor memory usage and cache efficiency\n\n";
        
        echo "3. Proxy Pattern:\n";
        echo "   • Use for lazy loading of expensive objects\n";
        echo "   • Implement proper access control\n";
        echo "   • Add caching for performance optimization\n";
        echo "   • Maintain same interface as real object\n";
        echo "   • Use for logging, monitoring, security\n\n";
        
        echo "4. Bridge Pattern:\n";
        echo "   • Separate abstraction from implementation\n";
        echo "   • Allow both to vary independently\n";
        echo "   • Use for multiple platforms or formats\n";
        echo "   • Keep interfaces focused and minimal\n";
        echo "   • Consider composition over inheritance\n\n";
        
        echo "5. General Guidelines:\n";
        echo "   • Choose patterns based on problem context\n";
        echo "   • Keep patterns simple and focused\n";
        echo "   • Document pattern usage and intent\n";
        echo "   • Test pattern implementations thoroughly\n";
        echo "   • Avoid anti-patterns and over-engineering";
    }
    
    public function runAllExamples(): void
    {
        echo "Advanced Structural Design Patterns Examples\n";
        echo str_repeat("=", 45) . "\n";
        
        $this->demonstrateComposite();
        $this->demonstrateFlyweight();
        $this->demonstrateProxy();
        $this->demonstrateBridge();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runStructuralPatternsDemo(): void
{
    $examples = new StructuralPatternsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runStructuralPatternsDemo();
}
?>
