<?php
/**
 * PHP Database Connections
 * 
 * Comprehensive guide to database connections, PDO, MySQLi, and best practices.
 */

echo "=== PHP Database Connections ===\n\n";

// Database Connection Methods
echo "--- Database Connection Methods ---\n";

// 1. PDO (PHP Data Objects) - Recommended
echo "1. PDO Connection:\n";

class PDOConnection {
    private $pdo;
    private $host;
    private $dbname;
    private $username;
    private $password;
    
    public function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
    }
    
    public function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            echo "✓ PDO connection successful\n";
            return true;
        } catch (PDOException $e) {
            echo "✗ PDO connection failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function close() {
        $this->pdo = null;
        echo "PDO connection closed\n";
    }
    
    public function testConnection() {
        if ($this->pdo) {
            $stmt = $this->pdo->query("SELECT VERSION() as version");
            $result = $stmt->fetch();
            echo "MySQL version: " . $result['version'] . "\n";
        }
    }
}

// 2. MySQLi (MySQL Improved)
echo "\n2. MySQLi Connection:\n";

class MySQLiConnection {
    private $mysqli;
    private $host;
    private $dbname;
    private $username;
    private $password;
    
    public function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
    }
    
    public function connect() {
        try {
            $this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            
            if ($this->mysqli->connect_error) {
                throw new Exception("Connection failed: " . $this->mysqli->connect_error);
            }
            
            // Set charset
            $this->mysqli->set_charset("utf8mb4");
            
            echo "✓ MySQLi connection successful\n";
            return true;
        } catch (Exception $e) {
            echo "✗ MySQLi connection failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function getConnection() {
        return $this->mysqli;
    }
    
    public function close() {
        if ($this->mysqli) {
            $this->mysqli->close();
            echo "MySQLi connection closed\n";
        }
    }
    
    public function testConnection() {
        if ($this->mysqli) {
            $result = $this->mysqli->query("SELECT VERSION() as version");
            $row = $result->fetch_assoc();
            echo "MySQL version: " . $row['version'] . "\n";
        }
    }
}

// 3. Database Connection Pool
echo "\n3. Database Connection Pool:\n";

class DatabasePool {
    private static $connections = [];
    private static $config = [];
    
    public static function setConfig($config) {
        self::$config = $config;
    }
    
    public static function getConnection($name = 'default') {
        if (!isset(self::$connections[$name])) {
            $config = self::$config[$name] ?? self::$config['default'];
            
            $pdo = new PDOConnection(
                $config['host'],
                $config['dbname'],
                $config['username'],
                $config['password']
            );
            
            if ($pdo->connect()) {
                self::$connections[$name] = $pdo;
            } else {
                throw new Exception("Failed to create connection: $name");
            }
        }
        
        return self::$connections[$name]->getConnection();
    }
    
    public static function closeAll() {
        foreach (self::$connections as $name => $connection) {
            $connection->close();
        }
        self::$connections = [];
        echo "All database connections closed\n";
    }
}

// Database Configuration
echo "\n--- Database Configuration ---\n";

$databaseConfig = [
    'default' => [
        'host' => 'localhost',
        'dbname' => 'test_db',
        'username' => 'root',
        'password' => ''
    ],
    'analytics' => [
        'host' => 'localhost',
        'dbname' => 'analytics_db',
        'username' => 'analytics_user',
        'password' => 'analytics_pass'
    ]
];

DatabasePool::setConfig($databaseConfig);

// Connection Testing
echo "\n--- Connection Testing ---\n";

// Test PDO Connection
echo "Testing PDO connection:\n";
$pdoConnection = new PDOConnection('localhost', 'test_db', 'root', '');
$pdoConnection->connect();
$pdoConnection->testConnection();
$pdoConnection->close();

// Test MySQLi Connection
echo "\nTesting MySQLi connection:\n";
$mysqliConnection = new MySQLiConnection('localhost', 'test_db', 'root', '');
$mysqliConnection->connect();
$mysqliConnection->testConnection();
$mysqliConnection->close();

// Test Connection Pool
echo "\nTesting connection pool:\n";
try {
    $pdo1 = DatabasePool::getConnection('default');
    $pdo2 = DatabasePool::getConnection('analytics');
    echo "✓ Connection pool working\n";
} catch (Exception $e) {
    echo "✗ Connection pool error: " . $e->getMessage() . "\n";
}

// Database Manager Class
echo "\n--- Database Manager Class ---\n";

class DatabaseManager {
    private $pdo;
    private $queryLog = [];
    private $transactionCount = 0;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function query($sql, $params = []) {
        $startTime = microtime(true);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $executionTime = microtime(true) - $startTime;
            $this->logQuery($sql, $params, $executionTime);
            
            return $stmt;
        } catch (PDOException $e) {
            $this->logQuery($sql, $params, microtime(true) - $startTime, $e->getMessage());
            throw $e;
        }
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function fetchColumn($sql, $params = [], $column = 0) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }
    
    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClause[] = "$column = ?";
            $params[] = $value;
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $setClause);
        
        if ($where) {
            $sql .= " WHERE $where";
            $params = array_merge($params, $whereParams);
        }
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function beginTransaction() {
        if ($this->transactionCount === 0) {
            $this->pdo->beginTransaction();
        }
        $this->transactionCount++;
    }
    
    public function commit() {
        $this->transactionCount--;
        if ($this->transactionCount === 0) {
            $this->pdo->commit();
        }
    }
    
    public function rollback() {
        $this->transactionCount = 0;
        $this->pdo->rollback();
    }
    
    public function inTransaction() {
        return $this->pdo->inTransaction();
    }
    
    private function logQuery($sql, $params, $executionTime, $error = null) {
        $this->queryLog[] = [
            'sql' => $sql,
            'params' => $params,
            'execution_time' => $executionTime,
            'error' => $error,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    public function getQueryLog() {
        return $this->queryLog;
    }
    
    public function getLastQuery() {
        return end($this->queryLog);
    }
    
    public function getTotalQueryTime() {
        return array_sum(array_column($this->queryLog, 'execution_time'));
    }
}

// Database Builder (Query Builder)
echo "\n--- Database Builder (Query Builder) ---\n";

class QueryBuilder {
    private $pdo;
    private $table;
    private $select = [];
    private $where = [];
    private $orderBy = [];
    private $limit;
    private $offset;
    private $params = [];
    
    public function __construct(PDO $pdo, $table) {
        $this->pdo = $pdo;
        $this->table = $table;
    }
    
    public function select($columns) {
        if (is_array($columns)) {
            $this->select = array_merge($this->select, $columns);
        } else {
            $this->select[] = $columns;
        }
        return $this;
    }
    
    public function where($column, $operator, $value = null) {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->where[] = "$column $operator ?";
        $this->params[] = $value;
        return $this;
    }
    
    public function whereIn($column, $values) {
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        $this->where[] = "$column IN ($placeholders)";
        $this->params = array_merge($this->params, $values);
        return $this;
    }
    
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy[] = "$column $direction";
        return $this;
    }
    
    public function limit($limit, $offset = null) {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }
    
    public function get() {
        $sql = $this->buildQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetchAll();
    }
    
    public function first() {
        $this->limit(1);
        $sql = $this->buildQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetch();
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetchColumn();
    }
    
    public function insert($data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }
    
    public function update($data) {
        if (empty($this->where)) {
            throw new Exception('WHERE clause is required for UPDATE operations');
        }
        
        $setClause = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClause[] = "$column = ?";
            $params[] = $value;
        }
        
        $params = array_merge($params, $this->params);
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause);
        $sql .= " WHERE " . implode(' AND ', $this->where);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    public function delete() {
        if (empty($this->where)) {
            throw new Exception('WHERE clause is required for DELETE operations');
        }
        
        $sql = "DELETE FROM {$this->table} WHERE " . implode(' AND ', $this->where);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }
    
    private function buildQuery() {
        $sql = "SELECT ";
        
        if (empty($this->select)) {
            $sql .= "*";
        } else {
            $sql .= implode(', ', $this->select);
        }
        
        $sql .= " FROM {$this->table}";
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
            if ($this->offset !== null) {
                $sql .= " OFFSET {$this->offset}";
            }
        }
        
        return $sql;
    }
    
    public function getQuery() {
        return $this->buildQuery();
    }
    
    public function getParams() {
        return $this->params;
    }
}

// Practical Examples
echo "\n--- Practical Examples ---\n";

// Example 1: User Management System
echo "Example 1: User Management System\n";

class UserRepository {
    private $db;
    
    public function __construct(DatabaseManager $db) {
        $this->db = $db;
    }
    
    public function create($userData) {
        $sql = "INSERT INTO users (username, email, password, created_at) 
                VALUES (?, ?, ?, NOW())";
        
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        $this->db->query($sql, [
            $userData['username'],
            $userData['email'],
            $hashedPassword
        ]);
        
        return $this->db->getConnection()->lastInsertId();
    }
    
    public function findById($id) {
        return $this->db->fetch(
            "SELECT id, username, email, created_at FROM users WHERE id = ?",
            [$id]
        );
    }
    
    public function findByEmail($email) {
        return $this->db->fetch(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );
    }
    
    public function update($id, $data) {
        $setClause = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $setClause[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        $params[] = $id;
        
        $sql = "UPDATE users SET " . implode(', ', $setClause) . " WHERE id = ?";
        return $this->db->query($sql, $params)->rowCount();
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM users WHERE id = ?", [$id])->rowCount();
    }
    
    public function getAll($limit = 10, $offset = 0) {
        return $this->db->fetchAll(
            "SELECT id, username, email, created_at FROM users 
             ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }
    
    public function search($query) {
        return $this->db->fetchAll(
            "SELECT id, username, email FROM users 
             WHERE username LIKE ? OR email LIKE ?",
            ["%$query%", "%$query%"]
        );
    }
}

// Example 2: Transaction Management
echo "\nExample 2: Transaction Management\n";

class OrderService {
    private $db;
    
    public function __construct(DatabaseManager $db) {
        $this->db = $db;
    }
    
    public function createOrder($orderData, $items) {
        try {
            $this->db->beginTransaction();
            
            // Create order
            $orderId = $this->db->insert('orders', [
                'user_id' => $orderData['user_id'],
                'total' => $orderData['total'],
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Add order items
            foreach ($items as $item) {
                $this->db->insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
                
                // Update product stock
                $this->db->query(
                    "UPDATE products SET stock = stock - ? WHERE id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}

// Example 3: Database Backup
echo "\nExample 3: Database Backup\n";

class DatabaseBackup {
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function backupTables($tables = []) {
        if (empty($tables)) {
            $tables = $this->getAllTables();
        }
        
        $backup = [];
        $backup[] = "-- Database Backup - " . date('Y-m-d H:i:s') . "\n";
        $backup[] = "-- Generated by PHP Database Backup\n\n";
        
        foreach ($tables as $table) {
            $backup[] = $this->backupTable($table);
        }
        
        return implode("\n", $backup);
    }
    
    private function getAllTables() {
        $stmt = $this->pdo->query("SHOW TABLES");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    private function backupTable($table) {
        $backup = [];
        
        // Get create table statement
        $stmt = $this->pdo->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch();
        $backup[] = "-- Table structure for `$table`\n";
        $backup[] = "DROP TABLE IF EXISTS `$table`;\n";
        $backup[] = $row['Create Table'] . ";\n\n";
        
        // Get table data
        $stmt = $this->pdo->query("SELECT * FROM `$table`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($rows)) {
            $backup[] = "-- Data for table `$table`\n";
            
            foreach ($rows as $row) {
                $values = array_map(function($value) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return $this->pdo->quote($value);
                }, $row);
                
                $backup[] = "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
            }
            
            $backup[] = "\n";
        }
        
        return implode("", $backup);
    }
    
    public function saveBackupToFile($filename, $tables = []) {
        $backup = $this->backupTables($tables);
        return file_put_contents($filename, $backup) !== false;
    }
}

// Example 4: Connection Health Monitor
echo "\nExample 4: Connection Health Monitor\n";

class DatabaseHealthMonitor {
    private $pdo;
    private $metrics = [];
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function checkConnection() {
        $start = microtime(true);
        
        try {
            $stmt = $this->pdo->query("SELECT 1");
            $result = $stmt->fetchColumn();
            
            $responseTime = microtime(true) - $start;
            
            $this->metrics['connection_status'] = 'healthy';
            $this->metrics['response_time'] = $responseTime;
            $this->metrics['last_check'] = date('Y-m-d H:i:s');
            
            return $result === 1;
        } catch (PDOException $e) {
            $this->metrics['connection_status'] = 'error';
            $this->metrics['error'] = $e->getMessage();
            $this->metrics['last_check'] = date('Y-m-d H:i:s');
            
            return false;
        }
    }
    
    public function getServerInfo() {
        $info = [];
        
        $stmt = $this->pdo->query("SELECT VERSION() as version");
        $info['mysql_version'] = $stmt->fetchColumn();
        
        $stmt = $this->pdo->query("SHOW STATUS LIKE 'Connections'");
        $info['total_connections'] = $stmt->fetchColumn(1);
        
        $stmt = $this->pdo->query("SHOW STATUS LIKE 'Threads_connected'");
        $info['active_connections'] = $stmt->fetchColumn(1);
        
        $stmt = $this->pdo->query("SHOW STATUS LIKE 'Uptime'");
        $info['uptime'] = $stmt->fetchColumn(1);
        
        return $info;
    }
    
    public function getMetrics() {
        return $this->metrics;
    }
}

echo "Database connections and management system demonstrated with:\n";
echo "1. PDO and MySQLi connection classes\n";
echo "2. Connection pooling\n";
echo "3. Database manager with query logging\n";
echo "4. Query builder for fluent SQL generation\n";
echo "5. Repository pattern for data access\n";
echo "6. Transaction management\n";
echo "7. Database backup functionality\n";
echo "8. Health monitoring system\n\n";

echo "=== End of Database Connections ===\n";
?>
