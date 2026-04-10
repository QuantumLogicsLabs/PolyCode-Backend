# PHP Database Programming

## Database Connections

### PDO (PHP Data Objects)
```php
<?php
// Basic PDO connection
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=testdb;charset=utf8mb4',
        'username',
        'password',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true
        ]
    );
    echo "Connected successfully!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Database connection class
class Database {
    private static ?PDO $instance = null;
    private PDO $pdo;
    
    private function __construct() {
        $config = require __DIR__ . '/config/database.php';
        
        try {
            $this->pdo = new PDO(
                $config['dsn'],
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection(): PDO {
        return $this->pdo;
    }
    
    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }
    
    public function commit(): bool {
        return $this->pdo->commit();
    }
    
    public function rollback(): bool {
        return $this->pdo->rollback();
    }
    
    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }
}

// Connection configuration
return [
    'dsn' => 'mysql:host=localhost;dbname=myapp;charset=utf8mb4',
    'username' => 'root',
    'password' => 'password',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => true,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]
];

// Using the database class
$db = Database::getInstance();
$pdo = $db->getConnection();
?>
```

### MySQLi Extension
```php
<?php
// MySQLi object-oriented style
$mysqli = new mysqli('localhost', 'username', 'password', 'testdb');

// Check connection
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Set charset
$mysqli->set_charset('utf8mb4');

// MySQLi connection class
class MySQLiConnection {
    private static ?self $instance = null;
    private mysqli $mysqli;
    
    private function __construct() {
        $config = require __DIR__ . '/config/database.php';
        
        $this->mysqli = new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database']
        );
        
        if ($this->mysqli->connect_error) {
            throw new RuntimeException("Connection failed: " . $this->mysqli->connect_error);
        }
        
        $this->mysqli->set_charset('utf8mb4');
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection(): mysqli {
        return $this->mysqli;
    }
    
    public function query(string $sql, ?int $result_mode = null): mysqli_result|bool {
        return $this->mysqli->query($sql, $result_mode);
    }
    
    public function prepare(string $sql): mysqli_stmt|false {
        return $this->mysqli->prepare($sql);
    }
    
    public function escape(string $string): string {
        return $this->mysqli->real_escape_string($string);
    }
    
    public function beginTransaction(): bool {
        return $this->mysqli->begin_transaction();
    }
    
    public function commit(): bool {
        return $this->mysqli->commit();
    }
    
    public function rollback(): bool {
        return $this->mysqli->rollback();
    }
    
    public function insertId(): int|string {
        return $this->mysqli->insert_id;
    }
}

// Using MySQLi
$db = MySQLiConnection::getInstance();
$mysqli = $db->getConnection();
?>
```

### Connection Pooling
```php
<?php
class ConnectionPool {
    private array $connections = [];
    private array $config;
    private int $maxConnections;
    private int $currentConnections = 0;
    
    public function __construct(array $config, int $maxConnections = 10) {
        $this->config = $config;
        $this->maxConnections = $maxConnections;
    }
    
    public function getConnection(): PDO {
        // Try to get an available connection
        foreach ($this->connections as $key => $connection) {
            if ($connection['in_use'] === false) {
                $this->connections[$key]['in_use'] = true;
                return $connection['pdo'];
            }
        }
        
        // Create new connection if under limit
        if ($this->currentConnections < $this->maxConnections) {
            $pdo = $this->createConnection();
            $this->connections[] = [
                'pdo' => $pdo,
                'in_use' => true
            ];
            $this->currentConnections++;
            return $pdo;
        }
        
        throw new RuntimeException("Maximum connections reached");
    }
    
    public function releaseConnection(PDO $pdo): void {
        foreach ($this->connections as $key => $connection) {
            if ($connection['pdo'] === $pdo) {
                $this->connections[$key]['in_use'] = false;
                return;
            }
        }
    }
    
    private function createConnection(): PDO {
        try {
            return new PDO(
                $this->config['dsn'],
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to create connection: " . $e->getMessage());
        }
    }
    
    public function closeAll(): void {
        foreach ($this->connections as $connection) {
            $connection['pdo'] = null;
        }
        $this->connections = [];
        $this->currentConnections = 0;
    }
}

// Using connection pool
$pool = new ConnectionPool($config, 5);
$pdo = $pool->getConnection();
try {
    // Use connection
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll();
} finally {
    $pool->releaseConnection($pdo);
}
?>
```

## CRUD Operations

### Create Operations
```php
<?php
class UserRepository {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Create single user
    public function create(array $userData): int {
        $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        $createdAt = date('Y-m-d H:i:s');
        
        $stmt->execute([
            $userData['name'],
            $userData['email'],
            $hashedPassword,
            $createdAt
        ]);
        
        return (int)$this->pdo->lastInsertId();
    }
    
    // Create multiple users (batch insert)
    public function createBatch(array $usersData): array {
        $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        $ids = [];
        $createdAt = date('Y-m-d H:i:s');
        
        $this->pdo->beginTransaction();
        
        try {
            foreach ($usersData as $userData) {
                $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
                $stmt->execute([
                    $userData['name'],
                    $userData['email'],
                    $hashedPassword,
                    $createdAt
                ]);
                $ids[] = (int)$this->pdo->lastInsertId();
            }
            
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
        
        return $ids;
    }
    
    // Create user with validation
    public function createWithValidation(array $userData): array {
        $errors = $this->validateUserData($userData);
        
        if (!empty($errors)) {
            throw new InvalidArgumentException('Validation failed: ' . implode(', ', $errors));
        }
        
        $id = $this->create($userData);
        return $this->findById($id);
    }
    
    private function validateUserData(array $userData): array {
        $errors = [];
        
        if (empty($userData['name'])) {
            $errors[] = 'Name is required';
        }
        
        if (empty($userData['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($userData['password'])) {
            $errors[] = 'Password is required';
        } elseif (strlen($userData['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        
        return $errors;
    }
}

// Using the repository
$userRepo = new UserRepository($pdo);
$userId = $userRepo->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'securepassword123'
]);

// Batch creation
$users = [
    ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'password' => 'password123'],
    ['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'password' => 'password456']
];
$ids = $userRepo->createBatch($users);
?>
```

### Read Operations
```php
<?php
class UserRepository {
    // Find user by ID
    public function findById(int $id): ?array {
        $sql = "SELECT id, name, email, created_at FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    // Find user by email
    public function findByEmail(string $email): ?array {
        $sql = "SELECT id, name, email, created_at FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    // Find all users
    public function findAll(): array {
        $sql = "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    // Find users with pagination
    public function findAllWithPagination(int $page = 1, int $limit = 10): array {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);
        
        return $stmt->fetchAll();
    }
    
    // Search users
    public function search(string $query, array $filters = []): array {
        $sql = "SELECT id, name, email, created_at FROM users WHERE (name LIKE ? OR email LIKE ?)";
        $params = ["%$query%", "%$query%"];
        
        // Add filters
        if (!empty($filters['created_after'])) {
            $sql .= " AND created_at >= ?";
            $params[] = $filters['created_after'];
        }
        
        if (!empty($filters['created_before'])) {
            $sql .= " AND created_at <= ?";
            $params[] = $filters['created_before'];
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    // Get user count
    public function count(): int {
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->pdo->query($sql);
        return (int)$stmt->fetch()['count'];
    }
    
    // Get users with their posts (JOIN)
    public function findUsersWithPosts(): array {
        $sql = "SELECT u.id, u.name, u.email, COUNT(p.id) as post_count 
                FROM users u 
                LEFT JOIN posts p ON u.id = p.user_id 
                GROUP BY u.id, u.name, u.email 
                ORDER BY post_count DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    // Get user statistics
    public function getStatistics(): array {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    COUNT(*) FILTER (WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as users_this_week,
                    COUNT(*) FILTER (WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as users_this_month
                FROM users";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch();
    }
}

// Using read operations
$user = $userRepo->findById(1);
$users = $userRepo->findAll();
$page1Users = $userRepo->findAllWithPagination(1, 10);
$searchResults = $userRepo->search('john', ['created_after' => '2023-01-01']);
$stats = $userRepo->getStatistics();
?>
```

### Update Operations
```php
<?php
class UserRepository {
    // Update user
    public function update(int $id, array $userData): bool {
        $fields = [];
        $params = [];
        
        if (isset($userData['name'])) {
            $fields[] = "name = ?";
            $params[] = $userData['name'];
        }
        
        if (isset($userData['email'])) {
            $fields[] = "email = ?";
            $params[] = $userData['email'];
        }
        
        if (isset($userData['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = ?";
        $params[] = date('Y-m-d H:i:s');
        $params[] = $id;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    // Update user name
    public function updateName(int $id, string $name): bool {
        $sql = "UPDATE users SET name = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$name, date('Y-m-d H:i:s'), $id]);
    }
    
    // Update user email with uniqueness check
    public function updateEmail(int $id, string $email): bool {
        // Check if email is already used by another user
        $existingUser = $this->findByEmail($email);
        if ($existingUser && $existingUser['id'] !== $id) {
            throw new RuntimeException("Email already exists");
        }
        
        $sql = "UPDATE users SET email = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$email, date('Y-m-d H:i:s'), $id]);
    }
    
    // Update password
    public function updatePassword(int $id, string $newPassword): bool {
        if (strlen($newPassword) < 8) {
            throw new InvalidArgumentException("Password must be at least 8 characters");
        }
        
        $sql = "UPDATE users SET password = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            date('Y-m-d H:i:s'),
            $id
        ]);
    }
    
    // Update last login
    public function updateLastLogin(int $id): bool {
        $sql = "UPDATE users SET last_login = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]);
    }
    
    // Batch update
    public function updateBatch(array $updates): int {
        $this->pdo->beginTransaction();
        
        try {
            $updatedCount = 0;
            
            foreach ($updates as $id => $userData) {
                if ($this->update($id, $userData)) {
                    $updatedCount++;
                }
            }
            
            $this->pdo->commit();
            return $updatedCount;
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    // Update with condition
    public function updateWhere(array $conditions, array $data): int {
        $setClause = [];
        $params = [];
        
        // Build SET clause
        foreach ($data as $field => $value) {
            $setClause[] = "$field = ?";
            $params[] = $value;
        }
        
        $setClause[] = "updated_at = ?";
        $params[] = date('Y-m-d H:i:s');
        
        // Build WHERE clause
        $whereClause = [];
        foreach ($conditions as $field => $value) {
            $whereClause[] = "$field = ?";
            $params[] = $value;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $setClause) . 
               " WHERE " . implode(' AND ', $whereClause);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
}

// Using update operations
$success = $userRepo->update(1, [
    'name' => 'John Updated',
    'email' => 'john.updated@example.com'
]);

$userRepo->updateEmail(1, 'new.email@example.com');
$userRepo->updatePassword(1, 'newpassword123');
$userRepo->updateLastLogin(1);

// Batch update
$updates = [
    1 => ['name' => 'John Updated'],
    2 => ['name' => 'Jane Updated']
];
$updatedCount = $userRepo->updateBatch($updates);

// Conditional update
$count = $userRepo->updateWhere(
    ['last_login' => null],
    ['status' => 'inactive']
);
?>
```

### Delete Operations
```php
<?php
class UserRepository {
    // Delete user by ID
    public function delete(int $id): bool {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$id]) && $stmt->rowCount() > 0;
    }
    
    // Delete users by IDs
    public function deleteByIds(array $ids): int {
        if (empty($ids)) {
            return 0;
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "DELETE FROM users WHERE id IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute($ids);
        return $stmt->rowCount();
    }
    
    // Delete by condition
    public function deleteWhere(array $conditions): int {
        $whereClause = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $whereClause[] = "$field = ?";
            $params[] = $value;
        }
        
        $sql = "DELETE FROM users WHERE " . implode(' AND ', $whereClause);
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    // Soft delete (mark as deleted)
    public function softDelete(int $id): bool {
        $sql = "UPDATE users SET deleted_at = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]);
    }
    
    // Restore soft deleted user
    public function restore(int $id): bool {
        $sql = "UPDATE users SET deleted_at = NULL, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([date('Y-m-d H:i:s'), $id]);
    }
    
    // Delete inactive users (cleanup)
    public function deleteInactiveUsers(int $daysInactive = 365): int {
        $sql = "DELETE FROM users 
                WHERE last_login < DATE_SUB(NOW(), INTERVAL ? DAY) 
                AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([$daysInactive]);
        return $stmt->rowCount();
    }
    
    // Delete user and related data (cascade)
    public function deleteUserWithRelatedData(int $id): bool {
        $this->pdo->beginTransaction();
        
        try {
            // Delete user's posts
            $sql = "DELETE FROM posts WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            // Delete user's comments
            $sql = "DELETE FROM comments WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            // Delete user
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([$id]);
            
            $this->pdo->commit();
            return $success;
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    // Archive user before deletion
    public function archiveAndDelete(int $id): bool {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }
        
        $this->pdo->beginTransaction();
        
        try {
            // Archive user data
            $sql = "INSERT INTO user_archive (user_id, name, email, created_at, archived_at) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $user['id'],
                $user['name'],
                $user['email'],
                $user['created_at'],
                date('Y-m-d H:i:s')
            ]);
            
            // Delete user
            $this->delete($id);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
}

// Using delete operations
$success = $userRepo->delete(1);
$deletedCount = $userRepo->deleteByIds([1, 2, 3]);
$deletedCount = $userRepo->deleteWhere(['status' => 'inactive']);

// Soft delete
$userRepo->softDelete(1);
$userRepo->restore(1);

// Cleanup
$cleanupCount = $userRepo->deleteInactiveUsers(365);

// Cascade delete
$userRepo->deleteUserWithRelatedData(1);

// Archive and delete
$userRepo->archiveAndDelete(1);
?>
```

## Prepared Statements

### Basic Prepared Statements
```php
<?php
class PreparedStatements {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Insert with prepared statement
    public function insertUser(string $name, string $email, string $password): int {
        $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$name, $email, $hashedPassword, date('Y-m-d H:i:s')]);
        
        return (int)$this->pdo->lastInsertId();
    }
    
    // Select with prepared statement
    public function findUserByEmail(string $email): ?array {
        $sql = "SELECT id, name, email, created_at FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        
        return $stmt->fetch() ?: null;
    }
    
    // Update with prepared statement
    public function updateUser(int $id, string $name, string $email): bool {
        $sql = "UPDATE users SET name = ?, email = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$name, $email, date('Y-m-d H:i:s'), $id]);
    }
    
    // Delete with prepared statement
    public function deleteUser(int $id): bool {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$id]) && $stmt->rowCount() > 0;
    }
    
    // Search with multiple parameters
    public function searchUsers(string $name, string $email, int $limit = 10): array {
        $sql = "SELECT id, name, email, created_at FROM users 
                WHERE name LIKE ? AND email LIKE ? 
                ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute(["%$name%", "%$email%", $limit]);
        return $stmt->fetchAll();
    }
    
    // Insert multiple records
    public function insertMultipleUsers(array $users): array {
        $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        $this->pdo->beginTransaction();
        
        try {
            $ids = [];
            foreach ($users as $user) {
                $stmt->execute([
                    $user['name'],
                    $user['email'],
                    password_hash($user['password'], PASSWORD_DEFAULT),
                    date('Y-m-d H:i:s')
                ]);
                $ids[] = (int)$this->pdo->lastInsertId();
            }
            
            $this->pdo->commit();
            return $ids;
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    // Dynamic WHERE clause
    public function findUsersByFilters(array $filters): array {
        $sql = "SELECT id, name, email, created_at FROM users WHERE 1=1";
        $params = [];
        
        if (!empty($filters['name'])) {
            $sql .= " AND name LIKE ?";
            $params[] = "%{$filters['name']}%";
        }
        
        if (!empty($filters['email'])) {
            $sql .= " AND email LIKE ?";
            $params[] = "%{$filters['email']}%";
        }
        
        if (!empty($filters['created_after'])) {
            $sql .= " AND created_at >= ?";
            $params[] = $filters['created_after'];
        }
        
        if (!empty($filters['created_before'])) {
            $sql .= " AND created_at <= ?";
            $params[] = $filters['created_before'];
        }
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
}

// Using prepared statements
$ps = new PreparedStatements($pdo);

// Insert user
$userId = $ps->insertUser('John Doe', 'john@example.com', 'password123');

// Find user
$user = $ps->findUserByEmail('john@example.com');

// Update user
$ps->updateUser($userId, 'John Updated', 'john.updated@example.com');

// Search users
$results = $ps->searchUsers('John', 'example', 5);

// Insert multiple users
$users = [
    ['name' => 'Jane', 'email' => 'jane@example.com', 'password' => 'pass123'],
    ['name' => 'Bob', 'email' => 'bob@example.com', 'password' => 'pass456']
];
$ids = $ps->insertMultipleUsers($users);

// Dynamic search
$filters = [
    'name' => 'John',
    'created_after' => '2023-01-01',
    'limit' => 10
];
$results = $ps->findUsersByFilters($filters);
?>
```

### Named Parameters
```php
<?php
class NamedParameters {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Insert with named parameters
    public function createUser(array $userData): int {
        $sql = "INSERT INTO users (name, email, password, created_at) 
                VALUES (:name, :email, :password, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            ':name' => $userData['name'],
            ':email' => $userData['email'],
            ':password' => password_hash($userData['password'], PASSWORD_DEFAULT),
            ':created_at' => date('Y-m-d H:i:s')
        ];
        
        $stmt->execute($params);
        return (int)$this->pdo->lastInsertId();
    }
    
    // Update with named parameters
    public function updateUser(int $id, array $userData): bool {
        $sql = "UPDATE users SET 
                name = :name, 
                email = :email, 
                updated_at = :updated_at 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            ':name' => $userData['name'],
            ':email' => $userData['email'],
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $id
        ];
        
        return $stmt->execute($params);
    }
    
    // Search with named parameters
    public function searchUsers(array $criteria): array {
        $sql = "SELECT id, name, email, created_at FROM users WHERE 1=1";
        $params = [];
        
        if (!empty($criteria['name'])) {
            $sql .= " AND name LIKE :name";
            $params[':name'] = "%{$criteria['name']}%";
        }
        
        if (!empty($criteria['email'])) {
            $sql .= " AND email LIKE :email";
            $params[':email'] = "%{$criteria['email']}%";
        }
        
        if (!empty($criteria['min_age'])) {
            $sql .= " AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= :min_age";
            $params[':min_age'] = $criteria['min_age'];
        }
        
        if (!empty($criteria['max_age'])) {
            $sql .= " AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= :max_age";
            $params[':max_age'] = $criteria['max_age'];
        }
        
        if (!empty($criteria['order_by'])) {
            $sql .= " ORDER BY :order_by";
            $params[':order_by'] = $criteria['order_by'];
        }
        
        if (!empty($criteria['limit'])) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = (int)$criteria['limit'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        
        // Bind parameters with explicit types
        foreach ($params as $key => $value) {
            $paramType = PDO::PARAM_STR;
            
            if (is_int($value)) {
                $paramType = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $paramType = PDO::PARAM_BOOL;
            }
            
            $stmt->bindValue($key, $value, $paramType);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Complex query with named parameters
    public function getUserStatistics(array $filters = []): array {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    COUNT(*) FILTER (WHERE created_at >= :start_date) as users_in_period,
                    AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())) as avg_age,
                    COUNT(DISTINCT email_domain) as unique_domains
                FROM users 
                WHERE created_at BETWEEN :start_date AND :end_date";
        
        $params = [
            ':start_date' => $filters['start_date'] ?? date('Y-m-d', strtotime('-30 days')),
            ':end_date' => $filters['end_date'] ?? date('Y-m-d')
        ];
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch();
    }
    
    // Batch insert with named parameters
    public function insertMultipleUsers(array $users): array {
        $sql = "INSERT INTO users (name, email, password, created_at) 
                VALUES (:name, :email, :password, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        
        $this->pdo->beginTransaction();
        
        try {
            $ids = [];
            foreach ($users as $userData) {
                $params = [
                    ':name' => $userData['name'],
                    ':email' => $userData['email'],
                    ':password' => password_hash($userData['password'], PASSWORD_DEFAULT),
                    ':created_at' => date('Y-m-d H:i:s')
                ];
                
                $stmt->execute($params);
                $ids[] = (int)$this->pdo->lastInsertId();
            }
            
            $this->pdo->commit();
            return $ids;
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
}

// Using named parameters
$np = new NamedParameters($pdo);

// Create user
$userId = $np->createUser([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'password123'
]);

// Update user
$np->updateUser($userId, [
    'name' => 'John Updated',
    'email' => 'john.updated@example.com'
]);

// Search with named parameters
$criteria = [
    'name' => 'John',
    'min_age' => 25,
    'max_age' => 35,
    'order_by' => 'name',
    'limit' => 10
];
$results = $np->searchUsers($criteria);

// Get statistics
$stats = $np->getUserStatistics([
    'start_date' => '2023-01-01',
    'end_date' => '2023-12-31'
]);
?>
```

### Parameter Binding
```php
<?php
class ParameterBinding {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Bind parameters with explicit types
    public function insertUserStrict(array $userData): int {
        $sql = "INSERT INTO users (name, email, age, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        // Bind parameters with explicit types
        $stmt->bindValue(1, $userData['name'], PDO::PARAM_STR);
        $stmt->bindValue(2, $userData['email'], PDO::PARAM_STR);
        $stmt->bindValue(3, $userData['age'], PDO::PARAM_INT);
        $stmt->bindValue(4, $userData['is_active'], PDO::PARAM_BOOL);
        $stmt->bindValue(5, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }
    
    // Bind parameters by reference
    public function updateUserByReference(int $id, string $name, string $email): bool {
        $sql = "UPDATE users SET name = ?, email = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        $updatedAt = date('Y-m-d H:i:s');
        
        // Bind by reference
        $stmt->bindParam(1, $name, PDO::PARAM_STR);
        $stmt->bindParam(2, $email, PDO::PARAM_STR);
        $stmt->bindParam(3, $updatedAt, PDO::PARAM_STR);
        $stmt->bindParam(4, $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // Working with NULL values
    public function updateUserPartial(int $id, array $updates): bool {
        $sql = "UPDATE users SET ";
        $params = [];
        $types = [];
        
        $clauses = [];
        foreach ($updates as $field => $value) {
            $clauses[] = "$field = ?";
            $params[] = $value;
            
            // Determine parameter type
            if ($value === null) {
                $types[] = PDO::PARAM_NULL;
            } elseif (is_int($value)) {
                $types[] = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $types[] = PDO::PARAM_BOOL;
            } else {
                $types[] = PDO::PARAM_STR;
            }
        }
        
        $sql .= implode(', ', $clauses);
        $sql .= ", updated_at = ? WHERE id = ?";
        
        $params[] = date('Y-m-d H:i:s');
        $params[] = $id;
        $types[] = PDO::PARAM_STR;
        $types[] = PDO::PARAM_INT;
        
        $stmt = $this->pdo->prepare($sql);
        
        // Bind each parameter with its type
        foreach ($params as $i => $param) {
            $stmt->bindValue($i + 1, $param, $types[$i]);
        }
        
        return $stmt->execute();
    }
    
    // Working with large data (BLOB)
    public function saveUserProfile(int $userId, string $profileData): bool {
        $sql = "INSERT INTO user_profiles (user_id, profile_data, created_at) 
                VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $profileData, PDO::PARAM_LOB);
        $stmt->bindValue(3, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    // Working with dates
    public function findUsersByDateRange(string $startDate, string $endDate): array {
        $sql = "SELECT id, name, email, created_at FROM users 
                WHERE created_at BETWEEN ? AND ?";
        $stmt = $this->pdo->prepare($sql);
        
        // Convert strings to DateTime objects for proper binding
        $startDateTime = new DateTime($startDate);
        $endDateTime = new DateTime($endDate);
        
        $stmt->bindValue(1, $startDateTime->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(2, $endDateTime->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Working with IN clauses
    public function findUsersByIds(array $ids): array {
        if (empty($ids)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT id, name, email FROM users WHERE id IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        
        // Bind all IDs as integers
        foreach ($ids as $i => $id) {
            $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Working with JSON data (MySQL 5.7+)
    public function updateUserPreferences(int $userId, array $preferences): bool {
        $sql = "UPDATE users SET preferences = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        $jsonPreferences = json_encode($preferences);
        
        $stmt->bindValue(1, $jsonPreferences, PDO::PARAM_STR);
        $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(3, $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // Working with decimal/numeric values
    public function updateProductPrice(int $productId, float $price): bool {
        $sql = "UPDATE products SET price = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        // Bind as string to preserve precision
        $stmt->bindValue(1, number_format($price, 2, '.', ''), PDO::PARAM_STR);
        $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(3, $productId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}

// Using parameter binding
$pb = new ParameterBinding($pdo);

// Insert with strict typing
$userId = $pb->insertUserStrict([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 30,
    'is_active' => true
]);

// Update by reference
$name = 'John Updated';
$email = 'john.updated@example.com';
$pb->updateUserByReference($userId, $name, $email);

// Partial update with NULL handling
$pb->updateUserPartial($userId, [
    'age' => 31,
    'phone' => null,  // This will set phone to NULL
    'is_active' => false
]);

// Save large data
$profileData = file_get_contents('user_profile.json');
$pb->saveUserProfile($userId, $profileData);

// Date range search
$users = $pb->findUsersByDateRange('2023-01-01', '2023-12-31');

// IN clause
$users = $pb->findUsersByIds([1, 2, 3, 4, 5]);

// JSON data
$preferences = [
    'theme' => 'dark',
    'notifications' => true,
    'language' => 'en'
];
$pb->updateUserPreferences($userId, $preferences);

// Decimal precision
$pb->updateProductPrice(1, 19.99);
?>
```

## Transactions

### Basic Transactions
```php
<?php
class TransactionManager {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Simple transaction
    public function transferFunds(int $fromAccountId, int $toAccountId, float $amount): bool {
        try {
            $this->pdo->beginTransaction();
            
            // Check if from account has sufficient funds
            $sql = "SELECT balance FROM accounts WHERE id = ? FOR UPDATE";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fromAccountId]);
            $fromAccount = $stmt->fetch();
            
            if (!$fromAccount || $fromAccount['balance'] < $amount) {
                throw new Exception("Insufficient funds");
            }
            
            // Debit from account
            $sql = "UPDATE accounts SET balance = balance - ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$amount, $fromAccountId]);
            
            // Credit to account
            $sql = "UPDATE accounts SET balance = balance + ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$amount, $toAccountId]);
            
            // Record transaction
            $sql = "INSERT INTO transactions (from_account_id, to_account_id, amount, created_at) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fromAccountId, $toAccountId, $amount, date('Y-m-d H:i:s')]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            error_log("Transfer failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Transaction with savepoints
    public function processOrder(int $orderId): bool {
        try {
            $this->pdo->beginTransaction();
            
            // Get order details
            $sql = "SELECT * FROM orders WHERE id = ? FOR UPDATE";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$orderId]);
            $order = $stmt->fetch();
            
            if (!$order || $order['status'] !== 'pending') {
                throw new Exception("Invalid order");
            }
            
            // Create savepoint before processing items
            $this->pdo->exec("SAVEPOINT process_items");
            
            try {
                // Process each order item
                $sql = "SELECT * FROM order_items WHERE order_id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$orderId]);
                $items = $stmt->fetchAll();
                
                foreach ($items as $item) {
                    // Check inventory
                    $sql = "SELECT quantity FROM inventory WHERE product_id = ? FOR UPDATE";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$item['product_id']]);
                    $inventory = $stmt->fetch();
                    
                    if (!$inventory || $inventory['quantity'] < $item['quantity']) {
                        throw new Exception("Insufficient inventory for product {$item['product_id']}");
                    }
                    
                    // Update inventory
                    $sql = "UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                }
                
            } catch (Exception $e) {
                // Rollback to savepoint
                $this->pdo->exec("ROLLBACK TO process_items");
                throw new Exception("Failed to process items: " . $e->getMessage());
            }
            
            // Create savepoint before payment
            $this->pdo->exec("SAVEPOINT process_payment");
            
            try {
                // Process payment
                $paymentResult = $this->processPayment($order['user_id'], $order['total_amount']);
                
                if (!$paymentResult) {
                    throw new Exception("Payment failed");
                }
                
            } catch (Exception $e) {
                // Rollback to savepoint
                $this->pdo->exec("ROLLBACK TO process_payment");
                throw new Exception("Payment failed: " . $e->getMessage());
            }
            
            // Update order status
            $sql = "UPDATE orders SET status = 'completed', updated_at = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([date('Y-m-d H:i:s'), $orderId]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            error_log("Order processing failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function processPayment(int $userId, float $amount): bool {
        // Simulate payment processing
        // In real implementation, this would call payment gateway
        return true;
    }
    
    // Nested transaction simulation
    public function createUserWithProfile(array $userData, array $profileData): int {
        try {
            $this->pdo->beginTransaction();
            
            // Insert user
            $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $userData['name'],
                $userData['email'],
                password_hash($userData['password'], PASSWORD_DEFAULT),
                date('Y-m-d H:i:s')
            ]);
            
            $userId = (int)$this->pdo->lastInsertId();
            
            // Create savepoint for profile
            $this->pdo->exec("SAVEPOINT create_profile");
            
            try {
                // Insert user profile
                $sql = "INSERT INTO user_profiles (user_id, bio, avatar_url, created_at) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $userId,
                    $profileData['bio'] ?? null,
                    $profileData['avatar_url'] ?? null,
                    date('Y-m-d H:i:s')
                ]);
                
            } catch (Exception $e) {
                // Profile creation failed, but user creation succeeded
                $this->pdo->exec("ROLLBACK TO create_profile");
                error_log("Profile creation failed: " . $e->getMessage());
                // Continue without profile
            }
            
            $this->pdo->commit();
            return $userId;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    // Transaction with retry logic
    public function executeWithRetry(callable $operation, int $maxRetries = 3): mixed {
        $retries = 0;
        
        while ($retries < $maxRetries) {
            try {
                $this->pdo->beginTransaction();
                
                $result = $operation($this->pdo);
                
                $this->pdo->commit();
                return $result;
                
            } catch (PDOException $e) {
                $this->pdo->rollback();
                
                // Check if it's a deadlock or lock wait timeout
                if ($e->getCode() === '40001' || $e->getCode() === '1205') {
                    $retries++;
                    if ($retries < $maxRetries) {
                        // Wait a random amount of time before retrying
                        usleep(rand(100000, 500000)); // 100-500ms
                        continue;
                    }
                }
                
                throw $e;
            }
        }
        
        throw new RuntimeException("Operation failed after $maxRetries retries");
    }
}

// Using transactions
$tm = new TransactionManager($pdo);

// Simple transfer
$success = $tm->transferFunds(1, 2, 100.00);

// Process order with savepoints
$success = $tm->processOrder(123);

// Create user with profile
$userId = $tm->createUserWithProfile(
    ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password123'],
    ['bio' => 'Software Developer', 'avatar_url' => 'https://example.com/avatar.jpg']
);

// Execute with retry
$result = $tm->executeWithRetry(function(PDO $pdo) {
    // Perform database operations
    $stmt = $pdo->prepare("INSERT INTO logs (message, created_at) VALUES (?, ?)");
    $stmt->execute(['Test message', date('Y-m-d H:i:s')]);
    return $pdo->lastInsertId();
});
?>
```

### Transaction Isolation Levels
```php
<?php
class IsolationLevels {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Read uncommitted (lowest isolation)
    public function readUncommittedExample(): void {
        $this->pdo->beginTransaction();
        $this->pdo->exec("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
        
        // Query that can read uncommitted changes from other transactions
        $sql = "SELECT balance FROM accounts WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([1]);
        $balance = $stmt->fetch()['balance'];
        
        echo "Read uncommitted balance: $balance\n";
        
        $this->pdo->commit();
    }
    
    // Read committed (default in many databases)
    public function readCommittedExample(): void {
        $this->pdo->beginTransaction();
        $this->pdo->exec("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
        
        // Can only read committed changes
        $sql = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $count = $stmt->fetch()['count'];
        
        echo "Pending orders count: $count\n";
        
        $this->pdo->commit();
    }
    
    // Repeatable read
    public function repeatableReadExample(): void {
        $this->pdo->beginTransaction();
        $this->pdo->exec("SET TRANSACTION ISOLATION LEVEL REPEATABLE READ");
        
        // First read
        $sql = "SELECT balance FROM accounts WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([1]);
        $balance1 = $stmt->fetch()['balance'];
        
        // Simulate some time passing
        sleep(1);
        
        // Second read - will return same value even if other transactions changed it
        $stmt->execute([1]);
        $balance2 = $stmt->fetch()['balance'];
        
        echo "First read: $balance1, Second read: $balance2\n";
        
        $this->pdo->commit();
    }
    
    // Serializable (highest isolation)
    public function serializableExample(): void {
        try {
            $this->pdo->beginTransaction();
            $this->pdo->exec("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
            
            // This transaction will fail if it conflicts with other concurrent transactions
            $sql = "UPDATE accounts SET balance = balance - 100 WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([1]);
            
            $sql = "UPDATE accounts SET balance = balance + 100 WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([2]);
            
            $this->pdo->commit();
            echo "Transfer completed successfully\n";
            
        } catch (PDOException $e) {
            $this->pdo->rollback();
            
            if ($e->getCode() === '40001') {
                echo "Serialization failure - transaction rolled back\n";
            } else {
                throw $e;
            }
        }
    }
    
    // Transaction with custom isolation level
    public function executeWithIsolation(callable $operation, string $isolation = 'READ COMMITTED'): mixed {
        try {
            $this->pdo->beginTransaction();
            $this->pdo->exec("SET TRANSACTION ISOLATION LEVEL $isolation");
            
            $result = $operation($this->pdo);
            
            $this->pdo->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    // Deadlock detection and handling
    public function handleDeadlock(callable $operation): mixed {
        $maxRetries = 3;
        $retries = 0;
        
        while ($retries < $maxRetries) {
            try {
                $this->pdo->beginTransaction();
                
                $result = $operation($this->pdo);
                
                $this->pdo->commit();
                return $result;
                
            } catch (PDOException $e) {
                $this->pdo->rollback();
                
                // Check for deadlock (MySQL error code 1213)
                if ($e->getCode() == '1213') {
                    $retries++;
                    echo "Deadlock detected, retrying... ($retries/$maxRetries)\n";
                    
                    // Wait a random amount of time before retrying
                    usleep(rand(100000, 500000));
                    continue;
                }
                
                throw $e;
            }
        }
        
        throw new RuntimeException("Operation failed after $maxRetries deadlock retries");
    }
    
    // Long-running transaction with timeout
    public function executeWithTimeout(callable $operation, int $timeoutSeconds = 30): mixed {
        $startTime = time();
        
        try {
            $this->pdo->beginTransaction();
            
            // Set lock timeout
            $this->pdo->exec("SET SESSION innodb_lock_wait_timeout = $timeoutSeconds");
            
            $result = $operation($this->pdo);
            
            // Check if transaction is taking too long
            if (time() - $startTime > $timeoutSeconds) {
                throw new RuntimeException("Transaction timeout exceeded");
            }
            
            $this->pdo->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
}

// Using isolation levels
$il = new IsolationLevels($pdo);

// Different isolation levels
$il->readUncommittedExample();
$il->readCommittedExample();
$il->repeatableReadExample();
$il->serializableExample();

// Execute with custom isolation
$result = $il->executeWithIsolation(function(PDO $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    return $stmt->fetchColumn();
}, 'REPEATABLE READ');

// Handle deadlocks
$result = $il->handleDeadlock(function(PDO $pdo) {
    // Operations that might cause deadlocks
    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - 50 WHERE id = ?");
    $stmt->execute([1]);
    
    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + 50 WHERE id = ?");
    $stmt->execute([2]);
    
    return true;
});

// Execute with timeout
$result = $il->executeWithTimeout(function(PDO $pdo) {
    // Long-running operation
    sleep(5);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM large_table");
    $stmt->execute();
    return $stmt->fetchColumn();
}, 10);
?>
```

## Database Abstraction

### Query Builder
```php
<?php
class QueryBuilder {
    private PDO $pdo;
    private string $table;
    private array $wheres = [];
    private array $orders = [];
    private array $selects = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];
    private array $params = [];
    
    public function __construct(PDO $pdo, string $table) {
        $this->pdo = $pdo;
        $this->table = $table;
    }
    
    // SELECT clauses
    public function select(array $columns): self {
        $this->selects = $columns;
        return $this;
    }
    
    public function selectRaw(string $sql): self {
        $this->selects[] = $sql;
        return $this;
    }
    
    // WHERE clauses
    public function where(string $column, string $operator, $value): self {
        $this->wheres[] = "$column $operator ?";
        $this->params[] = $value;
        return $this;
    }
    
    public function whereRaw(string $sql, array $params = []): self {
        $this->wheres[] = $sql;
        $this->params = array_merge($this->params, $params);
        return $this;
    }
    
    public function whereIn(string $column, array $values): self {
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        $this->wheres[] = "$column IN ($placeholders)";
        $this->params = array_merge($this->params, $values);
        return $this;
    }
    
    public function whereBetween(string $column, string $start, string $end): self {
        $this->wheres[] = "$column BETWEEN ? AND ?";
        $this->params[] = $start;
        $this->params[] = $end;
        return $this;
    }
    
    public function whereNull(string $column): self {
        $this->wheres[] = "$column IS NULL";
        return $this;
    }
    
    public function whereNotNull(string $column): self {
        $this->wheres[] = "$column IS NOT NULL";
        return $this;
    }
    
    // JOIN clauses
    public function join(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "INNER JOIN $table ON $first $operator $second";
        return $this;
    }
    
    public function leftJoin(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";
        return $this;
    }
    
    public function rightJoin(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "RIGHT JOIN $table ON $first $operator $second";
        return $this;
    }
    
    // ORDER BY clauses
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orders[] = "$column $direction";
        return $this;
    }
    
    public function orderByRaw(string $sql): self {
        $this->orders[] = $sql;
        return $this;
    }
    
    // LIMIT and OFFSET
    public function limit(int $limit): self {
        $this->limit = $limit;
        return $this;
    }
    
    public function offset(int $offset): self {
        $this->offset = $offset;
        return $this;
    }
    
    // Build and execute queries
    public function get(): array {
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetchAll();
    }
    
    public function first(): ?array {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }
    
    public function find(int $id): ?array {
        return $this->where('id', '=', $id)->first();
    }
    
    public function count(): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return (int)$stmt->fetch()['count'];
    }
    
    public function exists(): bool {
        return $this->count() > 0;
    }
    
    // INSERT operations
    public function insert(array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = str_repeat('?,', count($data) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        
        return (int)$this->pdo->lastInsertId();
    }
    
    public function insertGetId(array $data): array {
        $id = $this->insert($data);
        return $this->find($id);
    }
    
    // UPDATE operations
    public function update(array $data): int {
        $setClause = [];
        foreach ($data as $column => $value) {
            $setClause[] = "$column = ?";
            $this->params[] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause);
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        
        return $stmt->rowCount();
    }
    
    // DELETE operations
    public function delete(): int {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        
        return $stmt->rowCount();
    }
    
    // Aggregate functions
    public function sum(string $column): float {
        $sql = "SELECT SUM($column) as sum FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return (float)$stmt->fetch()['sum'];
    }
    
    public function avg(string $column): float {
        $sql = "SELECT AVG($column) as avg FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return (float)$stmt->fetch()['avg'];
    }
    
    public function min(string $column): mixed {
        $sql = "SELECT MIN($column) as min FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetch()['min'];
    }
    
    public function max(string $column): mixed {
        $sql = "SELECT MAX($column) as max FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetch()['max'];
    }
    
    // Private methods
    private function buildSelectQuery(): string {
        $sql = "SELECT ";
        
        if (empty($this->selects)) {
            $sql .= "*";
        } else {
            $sql .= implode(', ', $this->selects);
        }
        
        $sql .= " FROM {$this->table}";
        
        // Add joins
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        
        // Add where clauses
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        // Add order by
        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }
        
        // Add limit and offset
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
            
            if ($this->offset !== null) {
                $sql .= " OFFSET {$this->offset}";
            }
        }
        
        return $sql;
    }
    
    // Reset builder
    public function reset(): self {
        $this->wheres = [];
        $this->orders = [];
        $this->selects = [];
        $this->limit = null;
        $this->offset = null;
        $this->joins = [];
        $this->params = [];
        
        return $this;
    }
}

// Using the query builder
$users = new QueryBuilder($pdo, 'users');

// Basic queries
$allUsers = $users->get();
$user = $users->find(1);
$count = $users->count();

// Complex queries
$results = $users->select(['id', 'name', 'email'])
    ->where('age', '>=', 18)
    ->where('status', '=', 'active')
    ->whereIn('country', ['US', 'CA', 'UK'])
    ->whereBetween('created_at', '2023-01-01', '2023-12-31')
    ->orderBy('created_at', 'DESC')
    ->orderBy('name', 'ASC')
    ->limit(10)
    ->offset(20)
    ->get();

// JOIN queries
$results = $users->select(['users.id', 'users.name', 'posts.title'])
    ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
    ->where('posts.status', '=', 'published')
    ->orderBy('posts.created_at', 'DESC')
    ->get();

// Aggregate queries
$totalBalance = $users->sum('balance');
$avgAge = $users->avg('age');
$maxAge = $users->max('age');

// INSERT/UPDATE/DELETE
$newUserId = $users->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 30
]);

$updatedCount = $users->where('id', '=', 1)
    ->update(['name' => 'John Updated']);

$deletedCount = $users->where('status', '=', 'inactive')
    ->delete();
?>
```

### ORM (Object-Relational Mapping)
```php
<?php
// Base Model class
abstract class Model {
    protected static PDO $pdo;
    protected static string $table;
    protected array $attributes = [];
    protected array $dirty = [];
    protected bool $exists = false;
    
    public function __construct(array $attributes = []) {
        $this->fill($attributes);
    }
    
    public static function setConnection(PDO $pdo): void {
        self::$pdo = $pdo;
    }
    
    public static function getTable(): string {
        return static::$table ?? strtolower(class_basename(static::class)) . 's';
    }
    
    // Find methods
    public static function find(int $id): ?static {
        $sql = "SELECT * FROM " . static::getTable() . " WHERE id = ?";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([$id]);
        
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }
        
        $model = new static();
        $model->fill($data);
        $model->exists = true;
        $model->dirty = [];
        
        return $model;
    }
    
    public static function findOrFail(int $id): static {
        $model = self::find($id);
        if (!$model) {
            throw new ModelNotFoundException("Model not found with ID: $id");
        }
        return $model;
    }
    
    public static function all(): array {
        $sql = "SELECT * FROM " . static::getTable();
        $stmt = self::$pdo->query($sql);
        
        $models = [];
        foreach ($stmt->fetchAll() as $data) {
            $model = new static();
            $model->fill($data);
            $model->exists = true;
            $model->dirty = [];
            $models[] = $model;
        }
        
        return $models;
    }
    
    public static function where(string $column, string $operator, $value): QueryBuilder {
        return (new QueryBuilder(self::$pdo, static::getTable()))
            ->where($column, $operator, $value);
    }
    
    // Create and save
    public static function create(array $attributes): static {
        $model = new static($attributes);
        $model->save();
        return $model;
    }
    
    // Fill attributes
    public function fill(array $attributes): self {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }
    
    public function setAttribute(string $key, $value): void {
        if (!isset($this->attributes[$key]) || $this->attributes[$key] !== $value) {
            $this->attributes[$key] = $value;
            $this->dirty[$key] = $value;
        }
    }
    
    public function getAttribute(string $key) {
        return $this->attributes[$key] ?? null;
    }
    
    // Magic methods for property access
    public function __get(string $key) {
        return $this->getAttribute($key);
    }
    
    public function __set(string $key, $value): void {
        $this->setAttribute($key, $value);
    }
    
    public function __isset(string $key): bool {
        return isset($this->attributes[$key]);
    }
    
    public function __unset(string $key): void {
        unset($this->attributes[$key]);
        unset($this->dirty[$key]);
    }
    
    // Save model
    public function save(): bool {
        if ($this->exists) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
    
    private function insert(): bool {
        $columns = implode(', ', array_keys($this->dirty));
        $placeholders = str_repeat('?,', count($this->dirty) - 1) . '?';
        
        $sql = "INSERT INTO " . static::getTable() . " ($columns) VALUES ($placeholders)";
        $stmt = self::$pdo->prepare($sql);
        
        if ($stmt->execute(array_values($this->dirty))) {
            $this->attributes['id'] = (int)self::$pdo->lastInsertId();
            $this->exists = true;
            $this->dirty = [];
            return true;
        }
        
        return false;
    }
    
    private function update(): bool {
        if (empty($this->dirty)) {
            return true;
        }
        
        $setClause = [];
        foreach (array_keys($this->dirty) as $column) {
            $setClause[] = "$column = ?";
        }
        
        $sql = "UPDATE " . static::getTable() . " SET " . implode(', ', $setClause) . " WHERE id = ?";
        $stmt = self::$pdo->prepare($sql);
        
        $params = array_values($this->dirty);
        $params[] = $this->attributes['id'];
        
        if ($stmt->execute($params)) {
            $this->dirty = [];
            return true;
        }
        
        return false;
    }
    
    // Delete model
    public function delete(): bool {
        if (!$this->exists) {
            return false;
        }
        
        $sql = "DELETE FROM " . static::getTable() . " WHERE id = ?";
        $stmt = self::$pdo->prepare($sql);
        
        if ($stmt->execute([$this->attributes['id']])) {
            $this->exists = false;
            return true;
        }
        
        return false;
    }
    
    // Check if model exists
    public function exists(): bool {
        return $this->exists;
    }
    
    // Get dirty attributes
    public function getDirty(): array {
        return $this->dirty;
    }
    
    // Check if model is dirty
    public function isDirty(string $attribute = null): bool {
        if ($attribute === null) {
            return !empty($this->dirty);
        }
        
        return isset($this->dirty[$attribute]);
    }
    
    // Convert to array
    public function toArray(): array {
        return $this->attributes;
    }
    
    // Convert to JSON
    public function toJson(): string {
        return json_encode($this->toArray());
    }
}

// User model
class User extends Model {
    protected static string $table = 'users';
    
    protected array $fillable = ['name', 'email', 'password'];
    protected array $hidden = ['password'];
    
    // Relationships
    public function posts(): array {
        return Post::where('user_id', '=', $this->attributes['id'])->get();
    }
    
    public function profile(): ?Profile {
        return Profile::where('user_id', '=', $this->attributes['id'])->first();
    }
    
    // Scopes
    public static function active(): QueryBuilder {
        return self::where('status', '=', 'active');
    }
    
    public static function adults(): QueryBuilder {
        return self::where('age', '>=', 18);
    }
    
    // Custom methods
    public function getFullName(): string {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }
    
    public function isActive(): bool {
        return ($this->attributes['status'] ?? '') === 'active';
    }
    
    public function activate(): void {
        $this->setAttribute('status', 'active');
    }
    
    public function deactivate(): void {
        $this->setAttribute('status', 'inactive');
    }
}

// Post model
class Post extends Model {
    protected static string $table = 'posts';
    
    protected array $fillable = ['title', 'content', 'user_id', 'status'];
    protected array $hidden = ['user_id'];
    
    public function user(): ?User {
        return User::find($this->attributes['user_id']);
    }
    
    public function comments(): array {
        return Comment::where('post_id', '=', $this->attributes['id'])->get();
    }
    
    public function isPublished(): bool {
        return ($this->attributes['status'] ?? '') === 'published';
    }
    
    public function publish(): void {
        $this->setAttribute('status', 'published');
    }
    
    public function unpublish(): void {
        $this->setAttribute('status', 'draft');
    }
}

// Using the ORM
Model::setConnection($pdo);

// Find users
$user = User::find(1);
$allUsers = User::all();

// Query with scopes
$activeUsers = User::active()->get();
$adultUsers = User::adults()->get();

// Chain scopes and conditions
$users = User::active()
    ->where('age', '>=', 18)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Create user
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => password_hash('password', PASSWORD_DEFAULT)
]);

// Update user
$user->setAttribute('name', 'John Updated');
$user->save();

// Delete user
$user->delete();

// Access relationships
$user = User::find(1);
$posts = $user->posts();
$profile = $user->profile();

// Custom methods
echo $user->getFullName();
$user->activate();
$user->save();

// Post operations
$post = Post::create([
    'title' => 'My First Post',
    'content' => 'This is the content...',
    'user_id' => 1,
    'status' => 'draft'
]);

$post->publish();
$post->save();

// Get posts with author
$posts = Post::where('status', '=', 'published')->get();
foreach ($posts as $post) {
    echo $post->title . ' by ' . $post->user()->name;
}
?>
```

## Best Practices

### Database Security
```php
<?php
class DatabaseSecurity {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Always use prepared statements
    public function safeQuery(string $sql, array $params = []): array {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new RuntimeException("Database operation failed");
        }
    }
    
    // Input validation
    public function validateInput(array $data, array $rules): array {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (isset($rule['required']) && $rule['required'] && ($value === null || $value === '')) {
                $errors[$field] = "{$field} is required";
                continue;
            }
            
            if ($value !== null) {
                if (isset($rule['type'])) {
                    switch ($rule['type']) {
                        case 'int':
                            if (!filter_var($value, FILTER_VALIDATE_INT)) {
                                $errors[$field] = "{$field} must be an integer";
                            }
                            break;
                        case 'email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $errors[$field] = "{$field} must be a valid email";
                            }
                            break;
                        case 'url':
                            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                                $errors[$field] = "{$field} must be a valid URL";
                            }
                            break;
                    }
                }
                
                if (isset($rule['min']) && strlen($value) < $rule['min']) {
                    $errors[$field] = "{$field} must be at least {$rule['min']} characters";
                }
                
                if (isset($rule['max']) && strlen($value) > $rule['max']) {
                    $errors[$field] = "{$field} must not exceed {$rule['max']} characters";
                }
                
                if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
                    $errors[$field] = "{$field} format is invalid";
                }
            }
        }
        
        return $errors;
    }
    
    // SQL injection prevention
    public function preventSQLInjection(string $input): string {
        // Remove potential SQL injection patterns
        $patterns = [
            '/\b(UNION|SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE)\b/i',
            '/[\'";]/',
            '/--/',
            '/\/\*.*\*\//',
            '/\b(OR|AND)\s+\d+\s*=\s*\d+/i'
        ];
        
        return preg_replace($patterns, '', $input);
    }
    
    // Data sanitization
    public function sanitizeData(array $data, array $schema): array {
        $sanitized = [];
        
        foreach ($schema as $field => $type) {
            $value = $data[$field] ?? null;
            
            if ($value !== null) {
                switch ($type) {
                    case 'string':
                        $sanitized[$field] = $this->sanitizeString($value);
                        break;
                    case 'int':
                        $sanitized[$field] = $this->sanitizeInt($value);
                        break;
                    case 'float':
                        $sanitized[$field] = $this->sanitizeFloat($value);
                        break;
                    case 'email':
                        $sanitized[$field] = $this->sanitizeEmail($value);
                        break;
                    case 'url':
                        $sanitized[$field] = $this->sanitizeUrl($value);
                        break;
                    case 'html':
                        $sanitized[$field] = $this->sanitizeHTML($value);
                        break;
                    default:
                        $sanitized[$field] = $value;
                }
            }
        }
        
        return $sanitized;
    }
    
    private function sanitizeString(string $value): string {
        return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }
    
    private function sanitizeInt($value): int {
        return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
    
    private function sanitizeFloat($value): float {
        return (float)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    private function sanitizeEmail(string $value): string {
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }
    
    private function sanitizeUrl(string $value): string {
        return filter_var($value, FILTER_SANITIZE_URL);
    }
    
    private function sanitizeHTML(string $value): string {
        return strip_tags($value);
    }
    
    // Access control
    public function checkUserPermission(int $userId, string $permission): bool {
        $sql = "SELECT COUNT(*) as count FROM user_permissions 
                WHERE user_id = ? AND permission = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $permission]);
        
        return (int)$stmt->fetch()['count'] > 0;
    }
    
    // Rate limiting
    public function checkRateLimit(string $identifier, int $maxAttempts = 5, int $windowMinutes = 15): bool {
        $sql = "SELECT COUNT(*) as count FROM rate_limits 
                WHERE identifier = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$identifier, $windowMinutes]);
        
        $attempts = (int)$stmt->fetch()['count'];
        
        if ($attempts >= $maxAttempts) {
            return false;
        }
        
        // Log this attempt
        $sql = "INSERT INTO rate_limits (identifier, created_at) VALUES (?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$identifier]);
        
        return true;
    }
    
    // Audit logging
    public function logAuditEvent(int $userId, string $action, array $details = []): void {
        $sql = "INSERT INTO audit_log (user_id, action, details, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            $userId,
            $action,
            json_encode($details),
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }
    
    // Data encryption for sensitive fields
    public function encryptSensitiveData(string $data): string {
        $key = openssl_digest('your-secret-key', 'sha256', true);
        $iv = openssl_random_pseudo_bytes(16);
        
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        return base64_encode($iv . $encrypted);
    }
    
    public function decryptSensitiveData(string $encryptedData): string {
        $key = openssl_digest('your-secret-key', 'sha256', true);
        $data = base64_decode($encryptedData);
        
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }
}

// Using database security
$security = new DatabaseSecurity($pdo);

// Validate input
$rules = [
    'name' => ['required' => true, 'type' => 'string', 'min' => 2, 'max' => 100],
    'email' => ['required' => true, 'type' => 'email'],
    'age' => ['type' => 'int', 'min' => 0, 'max' => 150]
];

$errors = $security->validateInput($_POST, $rules);
if (!empty($errors)) {
    throw new InvalidArgumentException('Validation failed: ' . implode(', ', $errors));
}

// Sanitize data
$schema = [
    'name' => 'string',
    'email' => 'email',
    'age' => 'int',
    'bio' => 'html'
];

$sanitized = $security->sanitizeData($_POST, $schema);

// Check permissions
if (!$security->checkUserPermission($userId, 'create_user')) {
    throw new UnauthorizedException('Insufficient permissions');
}

// Rate limiting
if (!$security->checkRateLimit('create_user_' . $userId)) {
    throw new TooManyRequestsException('Rate limit exceeded');
}

// Log audit event
$security->logAuditEvent($userId, 'user_created', ['user_id' => $newUserId]);

// Safe query with all security measures
$users = $security->safeQuery(
    "SELECT id, name, email FROM users WHERE status = ? ORDER BY created_at DESC LIMIT ?",
    ['active', 10]
);
?>
```

### Performance Optimization
```php
<?php
class DatabasePerformance {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    // Connection pooling simulation
    private static array $connections = [];
    private static int $maxConnections = 10;
    
    public static function getConnection(): PDO {
        if (empty(self::$connections)) {
            for ($i = 0; $i < self::$maxConnections; $i++) {
                self::$connections[] = new PDO(
                    'mysql:host=localhost;dbname=test',
                    'user',
                    'password',
                    [
                        PDO::ATTR_PERSISTENT => true,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]
                );
            }
        }
        
        return array_pop(self::$connections);
    }
    
    public static function releaseConnection(PDO $pdo): void {
        if (count(self::$connections) < self::$maxConnections) {
            self::$connections[] = $pdo;
        }
    }
    
    // Query optimization
    public function optimizedUserQuery(array $filters = []): array {
        $sql = "SELECT u.id, u.name, u.email, u.created_at,
                       COUNT(p.id) as post_count,
                       MAX(p.created_at) as last_post_date
                FROM users u
                LEFT JOIN posts p ON u.id = p.user_id";
        
        $params = [];
        $conditions = [];
        
        // Add filters efficiently
        if (!empty($filters['name'])) {
            $conditions[] = "u.name LIKE ?";
            $params[] = "%{$filters['name']}%";
        }
        
        if (!empty($filters['created_after'])) {
            $conditions[] = "u.created_at >= ?";
            $params[] = $filters['created_after'];
        }
        
        if (!empty($filters['min_posts'])) {
            $conditions[] = "post_count >= ?";
            $params[] = $filters['min_posts'];
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql .= " GROUP BY u.id, u.name, u.email, u.created_at";
        
        // Add indexes to ORDER BY
        $sql .= " ORDER BY u.created_at DESC";
        
        // Use LIMIT for pagination
        $limit = $filters['limit'] ?? 50;
        $offset = $filters['offset'] ?? 0;
        $sql .= " LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    // Batch operations
    public function batchInsert(string $table, array $records): int {
        if (empty($records)) {
            return 0;
        }
        
        $columns = array_keys($records[0]);
        $columnList = implode(', ', $columns);
        $valuePlaceholders = '(' . str_repeat('?,', count($columns) - 1) . '?)';
        $allPlaceholders = str_repeat($valuePlaceholders . ',', count($records) - 1) . $valuePlaceholders;
        
        $sql = "INSERT INTO $table ($columnList) VALUES $allPlaceholders";
        $stmt = $this->pdo->prepare($sql);
        
        // Flatten all values
        $values = [];
        foreach ($records as $record) {
            foreach ($columns as $column) {
                $values[] = $record[$column] ?? null;
            }
        }
        
        $stmt->execute($values);
        return $stmt->rowCount();
    }
    
    // Efficient pagination with cursor-based pagination
    public function cursorPaginateUsers(int $lastId = 0, int $limit = 50): array {
        $sql = "SELECT id, name, email, created_at 
                FROM users 
                WHERE id > ? 
                ORDER BY id ASC 
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$lastId, $limit]);
        
        return $stmt->fetchAll();
    }
    
    // Caching query results
    private array $queryCache = [];
    private int $cacheTimeout = 300; // 5 minutes
    
    public function cachedQuery(string $sql, array $params = [], int $ttl = null): array {
        $cacheKey = md5($sql . serialize($params));
        $ttl = $ttl ?? $this->cacheTimeout;
        
        // Check cache
        if (isset($this->queryCache[$cacheKey])) {
            $cached = $this->queryCache[$cacheKey];
            if (time() - $cached['timestamp'] < $ttl) {
                return $cached['data'];
            }
        }
        
        // Execute query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        // Cache result
        $this->queryCache[$cacheKey] = [
            'data' => $data,
            'timestamp' => time()
        ];
        
        return $data;
    }
    
    // Index analysis
    public function analyzeIndexes(string $table): array {
        $sql = "SHOW INDEX FROM $table";
        $stmt = $this->pdo->query($sql);
        $indexes = $stmt->fetchAll();
        
        $indexInfo = [];
        foreach ($indexes as $index) {
            $indexInfo[$index['Key_name']][] = $index['Column_name'];
        }
        
        return $indexInfo;
    }
    
    // Query execution plan
    public function explainQuery(string $sql, array $params = []): array {
        $explainSql = "EXPLAIN " . $sql;
        $stmt = $this->pdo->prepare($explainSql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    // Slow query logging
    public function logSlowQuery(string $sql, array $params, float $executionTime): void {
        if ($executionTime > 1.0) { // Log queries taking more than 1 second
            $logData = [
                'sql' => $sql,
                'params' => $params,
                'execution_time' => $executionTime,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            error_log("Slow query detected: " . json_encode($logData));
        }
    }
    
    // Query with performance monitoring
    public function monitoredQuery(string $sql, array $params = []): array {
        $startTime = microtime(true);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll();
            
            $executionTime = microtime(true) - $startTime;
            $this->logSlowQuery($sql, $params, $executionTime);
            
            return $result;
        } catch (PDOException $e) {
            $executionTime = microtime(true) - $startTime;
            error_log("Query failed after {$executionTime}s: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Bulk update with CASE statement
    public function bulkUpdate(string $table, array $updates, string $keyColumn = 'id'): int {
        if (empty($updates)) {
            return 0;
        }
        
        $keys = array_keys($updates[0]);
        unset($keys[array_search($keyColumn, $keys)]);
        
        $sql = "UPDATE $table SET ";
        $params = [];
        
        // Build CASE statements for each column
        foreach ($keys as $column) {
            $sql .= "$column = CASE $keyColumn ";
            foreach ($updates as $update) {
                $sql .= "WHEN ? THEN ? ";
                $params[] = $update[$keyColumn];
                $params[] = $update[$column];
            }
            $sql .= "ELSE $column END, ";
        }
        
        $sql = rtrim($sql, ', ');
        $sql .= " WHERE $keyColumn IN (" . str_repeat('?,', count($updates) - 1) . '?)';
        
        foreach ($updates as $update) {
            $params[] = $update[$keyColumn];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
    
    // Memory-efficient large result processing
    public function processLargeResults(string $sql, array $params, callable $processor): void {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        // Use unbuffered queries for large results
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        while ($row = $stmt->fetch()) {
            $processor($row);
        }
    }
    
    // Connection health check
    public function healthCheck(): array {
        $startTime = microtime(true);
        
        try {
            $stmt = $this->pdo->query("SELECT 1");
            $stmt->fetch();
            
            $responseTime = microtime(true) - $startTime;
            
            return [
                'status' => 'healthy',
                'response_time' => $responseTime,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (PDOException $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    }
}

// Using performance optimizations
$perf = new DatabasePerformance($pdo);

// Optimized query with filters
$users = $perf->optimizedUserQuery([
    'name' => 'John',
    'created_after' => '2023-01-01',
    'min_posts' => 5,
    'limit' => 20,
    'offset' => 0
]);

// Batch insert
$records = [
    ['name' => 'User 1', 'email' => 'user1@example.com'],
    ['name' => 'User 2', 'email' => 'user2@example.com'],
    // ... more records
];
$insertedCount = $perf->batchInsert('users', $records);

// Cursor pagination
$batch1 = $perf->cursorPaginateUsers(0, 50);
$lastId = end($batch1)['id'];
$batch2 = $perf->cursorPaginateUsers($lastId, 50);

// Cached query
$cachedUsers = $perf->cachedQuery(
    "SELECT * FROM users WHERE status = ? ORDER BY created_at DESC LIMIT 10",
    ['active'],
    600 // 10 minutes cache
);

// Analyze indexes
$indexes = $perf->analyzeIndexes('users');

// Query execution plan
$plan = $perf->explainQuery("SELECT * FROM users WHERE name = 'John'");

// Monitored query
$results = $perf->monitoredQuery(
    "SELECT * FROM large_table WHERE complex_condition = ?",
    ['value']
);

// Bulk update
$updates = [
    ['id' => 1, 'status' => 'active'],
    ['id' => 2, 'status' => 'inactive'],
    ['id' => 3, 'status' => 'active']
];
$updatedCount = $perf->bulkUpdate('users', $updates);

// Process large results efficiently
$perf->processLargeResults(
    "SELECT * FROM large_table",
    [],
    function($row) {
        // Process each row without loading all into memory
        echo "Processing row: " . $row['id'] . "\n";
    }
);

// Health check
$health = $perf->healthCheck();
?>
```

## Common Pitfalls

### Database Pitfalls
```php
<?php
// Pitfall: SQL Injection
class BadExample {
    public function getUserUnsafe(string $email): array {
        // VULNERABLE: Direct string concatenation
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->pdo->query($sql);
        return $result->fetch();
    }
}

// Solution: Use prepared statements
class GoodExample {
    public function getUserSafe(string $email): ?array {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
}

// Pitfall: Not using transactions for related operations
class BadTransactionExample {
    public function transferMoneyUnsafe(int $fromId, int $toId, float $amount): bool {
        // No transaction - if second update fails, first one still happens
        $sql1 = "UPDATE accounts SET balance = balance - ? WHERE id = ?";
        $stmt1 = $this->pdo->prepare($sql1);
        $stmt1->execute([$amount, $fromId]);
        
        $sql2 = "UPDATE accounts SET balance = balance + ? WHERE id = ?";
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute([$amount, $toId]);
        
        return true;
    }
}

// Solution: Use transactions
class GoodTransactionExample {
    public function transferMoneySafe(int $fromId, int $toId, float $amount): bool {
        try {
            $this->pdo->beginTransaction();
            
            // Check balance first
            $sql = "SELECT balance FROM accounts WHERE id = ? FOR UPDATE";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fromId]);
            $balance = $stmt->fetchColumn();
            
            if ($balance < $amount) {
                throw new Exception("Insufficient funds");
            }
            
            // Debit
            $sql1 = "UPDATE accounts SET balance = balance - ? WHERE id = ?";
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt1->execute([$amount, $fromId]);
            
            // Credit
            $sql2 = "UPDATE accounts SET balance = balance + ? WHERE id = ?";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->execute([$amount, $toId]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            error_log("Transfer failed: " . $e->getMessage());
            return false;
        }
    }
}

// Pitfall: N+1 query problem
class BadNPlusOneExample {
    public function getUsersWithPosts(): array {
        $sql = "SELECT * FROM users";
        $users = $this->pdo->query($sql)->fetchAll();
        
        foreach ($users as &$user) {
            // This creates N+1 queries!
            $sql = "SELECT * FROM posts WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user['id']]);
            $user['posts'] = $stmt->fetchAll();
        }
        
        return $users;
    }
}

// Solution: Use JOIN or eager loading
class GoodNPlusOneExample {
    public function getUsersWithPosts(): array {
        // Single query with JOIN
        $sql = "SELECT u.*, p.id as post_id, p.title, p.content 
                FROM users u 
                LEFT JOIN posts p ON u.id = p.user_id 
                ORDER BY u.id, p.id";
        
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll();
        
        // Group results by user
        $users = [];
        foreach ($results as $row) {
            $userId = $row['id'];
            
            if (!isset($users[$userId])) {
                $users[$userId] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'posts' => []
                ];
            }
            
            if ($row['post_id']) {
                $users[$userId]['posts'][] = [
                    'id' => $row['post_id'],
                    'title' => $row['title'],
                    'content' => $row['content']
                ];
            }
        }
        
        return array_values($users);
    }
}

// Pitfall: Not handling database errors properly
class BadErrorHandling {
    public function createUser(array $data): int {
        $sql = "INSERT INTO users (name, email) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$data['name'], $data['email']]);
        
        return $this->pdo->lastInsertId();
        // No error handling - if email is duplicate, this will crash
    }
}

// Solution: Proper error handling
class GoodErrorHandling {
    public function createUser(array $data): int {
        try {
            $sql = "INSERT INTO users (name, email) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$data['name'], $data['email']]);
            
            return (int)$this->pdo->lastInsertId();
            
        } catch (PDOException $e) {
            // Handle specific error codes
            if ($e->getCode() == 23000) { // Duplicate entry
                throw new InvalidArgumentException("Email already exists");
            } else {
                error_log("Database error: " . $e->getMessage());
                throw new RuntimeException("Failed to create user");
            }
        }
    }
}

// Pitfall: Not using connection pooling efficiently
class BadConnectionManagement {
    public function processUsers(array $userIds): void {
        foreach ($userIds as $userId) {
            // Creates new connection for each user!
            $pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'pass');
            
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId]);
            
            $user = $stmt->fetch();
            // Process user
        }
    }
}

// Solution: Reuse connections
class GoodConnectionManagement {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function processUsers(array $userIds): array {
        // Single query for all users
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $sql = "SELECT * FROM users WHERE id IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($userIds);
        
        return $stmt->fetchAll();
    }
}

// Pitfall: Not using indexes effectively
class BadIndexUsage {
    public function findUsersByName(string $name): array {
        // This won't use index if name is not indexed
        $sql = "SELECT * FROM users WHERE name LIKE '%$name%'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}

// Solution: Use indexes properly
class GoodIndexUsage {
    public function findUsersByName(string $name): array {
        // This can use index if name is indexed
        $sql = "SELECT * FROM users WHERE name LIKE ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["$name%"]);
        
        return $stmt->fetchAll();
    }
    
    public function findUsersByMultipleCriteria(array $criteria): array {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        // Build query with indexed columns first
        if (!empty($criteria['id'])) {
            $sql .= " AND id = ?";
            $params[] = $criteria['id'];
        }
        
        if (!empty($criteria['email'])) {
            $sql .= " AND email = ?";
            $params[] = $criteria['email'];
        }
        
        if (!empty($criteria['name'])) {
            $sql .= " AND name LIKE ?";
            $params[] = $criteria['name'] . "%";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
}

// Pitfall: Memory issues with large datasets
class BadMemoryUsage {
    public function exportAllUsers(): string {
        $sql = "SELECT * FROM users";
        $stmt = $this->pdo->query($sql);
        $users = $stmt->fetchAll(); // Loads all users into memory!
        
        $csv = "id,name,email\n";
        foreach ($users as $user) {
            $csv .= "{$user['id']},{$user['name']},{$user['email']}\n";
        }
        
        return $csv;
    }
}

// Solution: Process data in chunks
class GoodMemoryUsage {
    public function exportAllUsers(): string {
        $sql = "SELECT id, name, email FROM users";
        $stmt = $this->pdo->query($sql);
        
        $csv = "id,name,email\n";
        
        // Process one row at a time
        while ($user = $stmt->fetch()) {
            $csv .= "{$user['id']},{$user['name']},{$user['email']}\n";
        }
        
        return $csv;
    }
    
    public function exportLargeDataset(string $filename): void {
        $handle = fopen($filename, 'w');
        fputcsv($handle, ['id', 'name', 'email']);
        
        $sql = "SELECT id, name, email FROM users";
        $stmt = $this->pdo->query($stmt);
        
        while ($user = $stmt->fetch()) {
            fputcsv($handle, $user);
        }
        
        fclose($handle);
    }
}
?>
```

## Summary

PHP Database Programming provides:

**Database Connections:**
- PDO for database abstraction
- MySQLi for MySQL-specific operations
- Connection pooling and management
- Error handling and configuration

**CRUD Operations:**
- Create operations with validation
- Read operations with pagination and filtering
- Update operations with partial updates
- Delete operations with soft delete and cascade

**Prepared Statements:**
- Parameter binding for security
- Named parameters for readability
- Type-safe parameter handling
- SQL injection prevention

**Transactions:**
- Basic transaction management
- Savepoints for nested operations
- Isolation levels and locking
- Deadlock handling and retry logic

**Database Abstraction:**
- Query builders for dynamic queries
- ORM patterns for object mapping
- Database independence
- Relationship management

**Security Practices:**
- Input validation and sanitization
- SQL injection prevention
- Access control and permissions
- Audit logging and rate limiting

**Performance Optimization:**
- Connection pooling
- Query optimization
- Caching strategies
- Batch operations
- Index usage analysis

**Best Practices:**
- Prepared statements always
- Transaction management
- Error handling
- Connection reuse
- Memory efficiency

**Common Pitfalls:**
- SQL injection vulnerabilities
- N+1 query problems
- Poor transaction handling
- Connection misuse
- Memory issues with large datasets

PHP provides robust database programming capabilities with PDO offering a modern, secure, and efficient way to interact with databases when following established best practices.
