<?php
/**
 * Advanced Creational Design Patterns
 * 
 * Implementation of complex creational patterns beyond basic factory and singleton.
 */

// Abstract Factory Pattern
abstract class AbstractFactory
{
    abstract public function createButton(string $type): Button;
    abstract public function createCheckbox(string $type): Checkbox;
    abstract public function createInput(string $type): Input;
}

// Concrete Factory for Light Theme
class LightThemeFactory extends AbstractFactory
{
    public function createButton(string $type): Button
    {
        return new LightButton($type);
    }
    
    public function createCheckbox(string $type): Checkbox
    {
        return new LightCheckbox($type);
    }
    
    public function createInput(string $type): Input
    {
        return new LightInput($type);
    }
}

// Concrete Factory for Dark Theme
class DarkThemeFactory extends AbstractFactory
{
    public function createButton(string $type): Button
    {
        return new DarkButton($type);
    }
    
    public function createCheckbox(string $type): Checkbox
    {
        return new DarkCheckbox($type);
    }
    
    public function createInput(string $type): Input
    {
        return new DarkInput($type);
    }
}

// Abstract Product Interfaces
interface Button
{
    public function render(): string;
    public function onClick(): void;
}

interface Checkbox
{
    public function render(): string;
    public function onToggle(): void;
}

interface Input
{
    public function render(): string;
    public function onInput(string $value): void;
}

// Light Theme Products
class LightButton implements Button
{
    private string $type;
    private string $color = '#ffffff';
    private string $bgColor = '#007bff';
    
    public function __construct(string $type)
    {
        $this->type = $type;
    }
    
    public function render(): string
    {
        return "<button class='btn btn-{$this->type}' style='color: {$this->color}; background: {$this->bgColor};'>{$this->type}</button>";
    }
    
    public function onClick(): void
    {
        echo "Light {$this->type} button clicked!\n";
    }
}

class LightCheckbox implements Checkbox
{
    private bool $checked = false;
    private string $color = '#333333';
    
    public function render(): string
    {
        $checked = $this->checked ? 'checked' : '';
        return "<input type='checkbox' $checked style='color: {$this->color};'>";
    }
    
    public function onToggle(): void
    {
        $this->checked = !$this->checked;
        echo "Light checkbox toggled: " . ($this->checked ? 'checked' : 'unchecked') . "\n";
    }
}

class LightInput implements Input
{
    private string $value = '';
    private string $color = '#333333';
    private string $borderColor = '#cccccc';
    
    public function render(): string
    {
        return "<input type='text' value='{$this->value}' style='color: {$this->color}; border: 1px solid {$this->borderColor};'>";
    }
    
    public function onInput(string $value): void
    {
        $this->value = $value;
        echo "Light input value changed: {$value}\n";
    }
}

// Dark Theme Products
class DarkButton implements Button
{
    private string $type;
    private string $color = '#000000';
    private string $bgColor = '#6c757d';
    
    public function __construct(string $type)
    {
        $this->type = $type;
    }
    
    public function render(): string
    {
        return "<button class='btn btn-{$this->type}' style='color: {$this->color}; background: {$this->bgColor};'>{$this->type}</button>";
    }
    
    public function onClick(): void
    {
        echo "Dark {$this->type} button clicked!\n";
    }
}

class DarkCheckbox implements Checkbox
{
    private bool $checked = false;
    private string $color = '#ffffff';
    
    public function render(): string
    {
        $checked = $this->checked ? 'checked' : '';
        return "<input type='checkbox' $checked style='color: {$this->color};'>";
    }
    
    public function onToggle(): void
    {
        $this->checked = !$this->checked;
        echo "Dark checkbox toggled: " . ($this->checked ? 'checked' : 'unchecked') . "\n";
    }
}

class DarkInput implements Input
{
    private string $value = '';
    private string $color = '#ffffff';
    private string $borderColor = '#495057';
    private string $bgColor = '#343a40';
    
    public function render(): string
    {
        return "<input type='text' value='{$this->value}' style='color: {$this->color}; border: 1px solid {$this->borderColor}; background: {$this->bgColor};'>";
    }
    
    public function onInput(string $value): void
    {
        $this->value = $value;
        echo "Dark input value changed: {$value}\n";
    }
}

// Builder Pattern for Complex Objects
class SQLQueryBuilder
{
    private array $parts = [
        'select' => [],
        'from' => '',
        'where' => [],
        'groupBy' => [],
        'having' => [],
        'orderBy' => [],
        'limit' => null,
        'offset' => null,
        'joins' => []
    ];
    
    private array $parameters = [];
    
    public function select(string ...$columns): self
    {
        $this->parts['select'] = $columns;
        return $this;
    }
    
    public function from(string $table): self
    {
        $this->parts['from'] = $table;
        return $this;
    }
    
    public function where(string $condition, $parameter = null): self
    {
        $this->parts['where'][] = $condition;
        if ($parameter !== null) {
            $this->parameters[] = $parameter;
        }
        return $this;
    }
    
    public function whereIn(string $column, array $values): self
    {
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        $this->parts['where'][] = "$column IN ($placeholders)";
        $this->parameters = array_merge($this->parameters, $values);
        return $this;
    }
    
    public function join(string $table, string $on): self
    {
        $this->parts['joins'][] = "JOIN $table ON $on";
        return $this;
    }
    
    public function leftJoin(string $table, string $on): self
    {
        $this->parts['joins'][] = "LEFT JOIN $table ON $on";
        return $this;
    }
    
    public function groupBy(string ...$columns): self
    {
        $this->parts['groupBy'] = $columns;
        return $this;
    }
    
    public function having(string $condition, $parameter = null): self
    {
        $this->parts['having'][] = $condition;
        if ($parameter !== null) {
            $this->parameters[] = $parameter;
        }
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->parts['orderBy'][] = "$column $direction";
        return $this;
    }
    
    public function limit(int $limit): self
    {
        $this->parts['limit'] = $limit;
        return $this;
    }
    
    public function offset(int $offset): self
    {
        $this->parts['offset'] = $offset;
        return $this;
    }
    
    public function build(): array
    {
        $sql = [];
        
        // SELECT
        if (!empty($this->parts['select'])) {
            $sql[] = 'SELECT ' . implode(', ', $this->parts['select']);
        } else {
            $sql[] = 'SELECT *';
        }
        
        // FROM
        if (!empty($this->parts['from'])) {
            $sql[] = 'FROM ' . $this->parts['from'];
        }
        
        // JOINs
        foreach ($this->parts['joins'] as $join) {
            $sql[] = $join;
        }
        
        // WHERE
        if (!empty($this->parts['where'])) {
            $sql[] = 'WHERE ' . implode(' AND ', $this->parts['where']);
        }
        
        // GROUP BY
        if (!empty($this->parts['groupBy'])) {
            $sql[] = 'GROUP BY ' . implode(', ', $this->parts['groupBy']);
        }
        
        // HAVING
        if (!empty($this->parts['having'])) {
            $sql[] = 'HAVING ' . implode(' AND ', $this->parts['having']);
        }
        
        // ORDER BY
        if (!empty($this->parts['orderBy'])) {
            $sql[] = 'ORDER BY ' . implode(', ', $this->parts['orderBy']);
        }
        
        // LIMIT
        if ($this->parts['limit'] !== null) {
            $sql[] = 'LIMIT ' . $this->parts['limit'];
        }
        
        // OFFSET
        if ($this->parts['offset'] !== null) {
            $sql[] = 'OFFSET ' . $this->parts['offset'];
        }
        
        return [
            'sql' => implode(' ', $sql),
            'parameters' => $this->parameters
        ];
    }
}

// Prototype Pattern
abstract class Prototype
{
    protected string $id;
    protected array $data;
    
    public function __construct(string $id, array $data = [])
    {
        $this->id = $id;
        $this->data = $data;
    }
    
    abstract public function clone(): Prototype;
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function setData(array $data): void
    {
        $this->data = $data;
    }
    
    public function __clone()
    {
        // Deep clone data array
        $this->data = array_map(function($item) {
            return is_object($item) ? clone $item : $item;
        }, $this->data);
    }
}

class UserPrototype extends Prototype
{
    private string $name;
    private string $email;
    private array $roles;
    
    public function __construct(string $id, string $name, string $email, array $roles = [])
    {
        parent::__construct($id);
        $this->name = $name;
        $this->email = $email;
        $this->roles = $roles;
    }
    
    public function clone(): Prototype
    {
        return clone $this;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    public function getRoles(): array
    {
        return $this->roles;
    }
    
    public function addRole(string $role): void
    {
        $this->roles[] = $role;
    }
    
    public function __clone()
    {
        parent::__clone();
        // Clone roles array
        $this->roles = array_merge([], $this->roles);
    }
}

// Prototype Manager
class PrototypeManager
{
    private array $prototypes = [];
    
    public function addPrototype(string $name, Prototype $prototype): void
    {
        $this->prototypes[$name] = $prototype;
    }
    
    public function getPrototype(string $name): ?Prototype
    {
        return isset($this->prototypes[$name]) ? $this->prototypes[$name]->clone() : null;
    }
    
    public function listPrototypes(): array
    {
        return array_keys($this->prototypes);
    }
}

// Object Pool Pattern
class DatabaseConnection
{
    private string $connectionString;
    private bool $inUse = false;
    private ?PDO $pdo = null;
    
    public function __construct(string $connectionString)
    {
        $this->connectionString = $connectionString;
    }
    
    public function connect(): void
    {
        if (!$this->pdo) {
            // Simulate database connection
            $this->pdo = new PDO($this->connectionString);
            echo "Database connection established: {$this->connectionString}\n";
        }
    }
    
    public function disconnect(): void
    {
        if ($this->pdo) {
            $this->pdo = null;
            echo "Database connection closed: {$this->connectionString}\n";
        }
    }
    
    public function query(string $sql, array $params = []): array
    {
        if (!$this->pdo) {
            throw new Exception('Database not connected');
        }
        
        // Simulate query execution
        echo "Executing query: $sql\n";
        return ['result' => 'simulated_data'];
    }
    
    public function isInUse(): bool
    {
        return $this->inUse;
    }
    
    public function setInUse(bool $inUse): void
    {
        $this->inUse = $inUse;
    }
    
    public function getConnectionString(): string
    {
        return $this->connectionString;
    }
}

class DatabaseConnectionPool
{
    private array $connections = [];
    private string $connectionString;
    private int $maxConnections;
    private int $currentConnections = 0;
    
    public function __construct(string $connectionString, int $maxConnections = 10)
    {
        $this->connectionString = $connectionString;
        $this->maxConnections = $maxConnections;
    }
    
    public function getConnection(): DatabaseConnection
    {
        // Try to find an available connection
        foreach ($this->connections as $connection) {
            if (!$connection->isInUse()) {
                $connection->setInUse(true);
                echo "Reusing existing connection\n";
                return $connection;
            }
        }
        
        // Create new connection if under limit
        if ($this->currentConnections < $this->maxConnections) {
            $connection = new DatabaseConnection($this->connectionString);
            $connection->connect();
            $connection->setInUse(true);
            $this->connections[] = $connection;
            $this->currentConnections++;
            echo "Created new connection ({$this->currentConnections}/{$this->maxConnections})\n";
            return $connection;
        }
        
        // Pool is full, wait or throw exception
        throw new Exception('Connection pool is full');
    }
    
    public function releaseConnection(DatabaseConnection $connection): void
    {
        foreach ($this->connections as $conn) {
            if ($conn === $connection) {
                $conn->setInUse(false);
                echo "Connection released\n";
                return;
            }
        }
        
        throw new Exception('Connection not found in pool');
    }
    
    public function getPoolStats(): array
    {
        return [
            'max_connections' => $this->maxConnections,
            'current_connections' => $this->currentConnections,
            'available_connections' => $this->maxConnections - $this->currentConnections,
            'in_use_connections' => count(array_filter($this->connections, fn($c) => $c->isInUse()))
        ];
    }
}

// Creational Patterns Examples
class CreationalPatternsExamples
{
    public function demonstrateAbstractFactory(): void
    {
        echo "Abstract Factory Pattern Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Create light theme UI
        echo "Creating Light Theme UI:\n";
        $lightFactory = new LightThemeFactory();
        
        $button = $lightFactory->createButton('primary');
        $checkbox = $lightFactory->createCheckbox('terms');
        $input = $lightFactory->createInput('username');
        
        echo $button->render() . "\n";
        echo $checkbox->render() . "\n";
        echo $input->render() . "\n\n";
        
        $button->onClick();
        $checkbox->onToggle();
        $input->onInput('john_doe');
        
        // Create dark theme UI
        echo "\nCreating Dark Theme UI:\n";
        $darkFactory = new DarkThemeFactory();
        
        $darkButton = $darkFactory->createButton('secondary');
        $darkCheckbox = $darkFactory->createCheckbox('newsletter');
        $darkInput = $darkFactory->createInput('email');
        
        echo $darkButton->render() . "\n";
        echo $darkCheckbox->render() . "\n";
        echo $darkInput->render() . "\n\n";
        
        $darkButton->onClick();
        $darkCheckbox->onToggle();
        $darkInput->onInput('user@example.com');
    }
    
    public function demonstrateBuilder(): void
    {
        echo "\nBuilder Pattern Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Build complex SQL query
        echo "Building SQL Query:\n";
        $query = (new SQLQueryBuilder())
            ->select('u.id', 'u.name', 'u.email', 'COUNT(o.id) as order_count')
            ->from('users u')
            ->leftJoin('orders o', 'u.id = o.user_id')
            ->where('u.active = ?', 1)
            ->whereIn('u.status', ['active', 'pending'])
            ->groupBy('u.id', 'u.name', 'u.email')
            ->having('COUNT(o.id) > ?', 0)
            ->orderBy('order_count', 'DESC')
            ->limit(10)
            ->offset(0)
            ->build();
        
        echo "SQL: " . $query['sql'] . "\n";
        echo "Parameters: " . json_encode($query['parameters']) . "\n\n";
        
        // Another query example
        echo "Another Query Example:\n";
        $query2 = (new SQLQueryBuilder())
            ->select('p.*', 'c.name as category_name')
            ->from('products p')
            ->join('categories c', 'p.category_id = c.id')
            ->where('p.price > ?', 100)
            ->where('c.name LIKE ?', '%electronics%')
            ->orderBy('p.price', 'ASC')
            ->build();
        
        echo "SQL: " . $query2['sql'] . "\n";
        echo "Parameters: " . json_encode($query2['parameters']) . "\n";
    }
    
    public function demonstratePrototype(): void
    {
        echo "\nPrototype Pattern Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create prototype manager
        $manager = new PrototypeManager();
        
        // Create original user prototype
        $originalUser = new UserPrototype('user_1', 'John Doe', 'john@example.com', ['user']);
        $manager->addPrototype('default_user', $originalUser);
        
        echo "Original User:\n";
        echo "ID: " . $originalUser->getId() . "\n";
        echo "Name: " . $originalUser->getName() . "\n";
        echo "Email: " . $originalUser->getEmail() . "\n";
        echo "Roles: " . implode(', ', $originalUser->getRoles()) . "\n\n";
        
        // Clone user multiple times
        echo "Cloned Users:\n";
        for ($i = 1; $i <= 3; $i++) {
            $clonedUser = $manager->getPrototype('default_user');
            $clonedUser->setName("User Clone $i");
            $clonedUser->setEmail("clone$i@example.com");
            $clonedUser->addRole('cloned_user');
            
            echo "Clone $i:\n";
            echo "  ID: " . $clonedUser->getId() . "\n";
            echo "  Name: " . $clonedUser->getName() . "\n";
            echo "  Email: " . $clonedUser->getEmail() . "\n";
            echo "  Roles: " . implode(', ', $clonedUser->getRoles()) . "\n\n";
        }
        
        // Verify original is unchanged
        echo "Original User (after cloning):\n";
        echo "Name: " . $originalUser->getName() . "\n";
        echo "Email: " . $originalUser->getEmail() . "\n";
        echo "Roles: " . implode(', ', $originalUser->getRoles()) . "\n";
    }
    
    public function demonstrateObjectPool(): void
    {
        echo "\nObject Pool Pattern Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create connection pool
        $pool = new DatabaseConnectionPool('mysql:host=localhost;dbname=test', 3);
        
        echo "Initial Pool Stats:\n";
        $stats = $pool->getPoolStats();
        foreach ($stats as $key => $value) {
            echo "  $key: $value\n";
        }
        echo "\n";
        
        // Get connections
        echo "Getting Connections:\n";
        $connections = [];
        
        for ($i = 1; $i <= 4; $i++) {
            try {
                $conn = $pool->getConnection();
                $connections[] = $conn;
                echo "Connection $i obtained\n";
                
                // Execute a query
                $result = $conn->query("SELECT * FROM users WHERE id = ?", [$i]);
                
            } catch (Exception $e) {
                echo "Error getting connection $i: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\nPool Stats After Getting Connections:\n";
        $stats = $pool->getPoolStats();
        foreach ($stats as $key => $value) {
            echo "  $key: $value\n";
        }
        
        // Release connections
        echo "\nReleasing Connections:\n";
        foreach ($connections as $i => $conn) {
            $pool->releaseConnection($conn);
            echo "Connection " . ($i + 1) . " released\n";
        }
        
        echo "\nFinal Pool Stats:\n";
        $stats = $pool->getPoolStats();
        foreach ($stats as $key => $value) {
            echo "  $key: $value\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nCreational Patterns Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Abstract Factory:\n";
        echo "   • Use when you need to create families of related objects\n";
        echo "   • Ensure all factories implement the same interface\n";
        echo "   • Keep factories focused on one product family\n";
        echo "   • Use dependency injection to inject the factory\n";
        echo "   • Consider adding factory methods to each factory\n\n";
        
        echo "2. Builder Pattern:\n";
        echo "   • Use for complex objects with many optional parameters\n";
        echo "   • Provide fluent interface for method chaining\n";
        echo "   • Validate parameters in the build() method\n";
        echo "   • Make the builder immutable after creation\n";
        echo "   • Use separate builders for different configurations\n\n";
        
        echo "3. Prototype Pattern:\n";
        echo "   • Use when object creation is expensive\n";
        echo "   • Implement proper deep cloning\n";
        echo "   • Use prototype manager for organized prototypes\n";
        echo "   • Consider clone performance implications\n";
        echo "   • Use for objects with similar configurations\n\n";
        
        echo "4. Object Pool Pattern:\n";
        echo "   • Use for expensive-to-create objects\n";
        echo "   • Implement proper connection lifecycle management\n";
        echo "   • Handle pool exhaustion gracefully\n";
        echo "   • Monitor pool performance and statistics\n";
        echo "   • Consider time-based connection cleanup\n\n";
        
        echo "5. General Guidelines:\n";
        echo "   • Choose the right pattern for your use case\n";
        echo "   • Keep patterns simple and focused\n";
        echo "   • Document pattern usage and intent\n";
        echo "   • Test pattern implementations thoroughly\n";
        echo "   • Avoid over-engineering simple problems";
    }
    
    public function runAllExamples(): void
    {
        echo "Advanced Creational Design Patterns Examples\n";
        echo str_repeat("=", 45) . "\n";
        
        $this->demonstrateAbstractFactory();
        $this->demonstrateBuilder();
        $this->demonstratePrototype();
        $this->demonstrateObjectPool();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runCreationalPatternsDemo(): void
{
    $examples = new CreationalPatternsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCreationalPatternsDemo();
}
?>
