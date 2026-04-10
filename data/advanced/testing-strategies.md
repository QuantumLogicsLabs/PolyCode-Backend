# PHP Testing Strategies

## Unit Testing

### PHPUnit Unit Testing
```php
<?php
use PHPUnit\Framework\TestCase;

// Simple class to test
class Calculator {
    public function add(float $a, float $b): float {
        return $a + $b;
    }
    
    public function subtract(float $a, float $b): float {
        return $a - $b;
    }
    
    public function multiply(float $a, float $b): float {
        return $a * $b;
    }
    
    public function divide(float $a, float $b): float {
        if ($b == 0) {
            throw new InvalidArgumentException('Division by zero');
        }
        return $a / $b;
    }
    
    public function power(float $base, float $exponent): float {
        return pow($base, $exponent);
    }
    
    public function sqrt(float $number): float {
        if ($number < 0) {
            throw new InvalidArgumentException('Cannot calculate square root of negative number');
        }
        return sqrt($number);
    }
}

// Basic unit test
class CalculatorTest extends TestCase {
    private Calculator $calculator;
    
    protected function setUp(): void {
        $this->calculator = new Calculator();
    }
    
    public function testAdd(): void {
        $result = $this->calculator->add(2, 3);
        $this->assertEquals(5, $result);
        $this->assertIsFloat($result);
    }
    
    public function testSubtract(): void {
        $result = $this->calculator->subtract(5, 3);
        $this->assertEquals(2, $result);
    }
    
    public function testMultiply(): void {
        $result = $this->calculator->multiply(4, 5);
        $this->assertEquals(20, $result);
    }
    
    public function testDivide(): void {
        $result = $this->calculator->divide(10, 2);
        $this->assertEquals(5, $result);
    }
    
    public function testDivideByZero(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->calculator->divide(10, 0);
    }
    
    public function testPower(): void {
        $result = $this->calculator->power(2, 3);
        $this->assertEquals(8, $result);
    }
    
    public function testSqrt(): void {
        $result = $this->calculator->sqrt(16);
        $this->assertEquals(4, $result);
    }
    
    public function testSqrtNegative(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->calculator->sqrt(-4);
    }
    
    // Data provider for multiple test cases
    public static function additionProvider(): array {
        return [
            [1, 1, 2],
            [0, 0, 0],
            [-1, 1, 0],
            [2.5, 3.5, 6.0],
            [-2, -3, -5]
        ];
    }
    
    /**
     * @dataProvider additionProvider
     */
    public function testAddWithDataProvider(float $a, float $b, float $expected): void {
        $result = $this->calculator->add($a, $b);
        $this->assertEquals($expected, $result);
    }
    
    // Test with floating point precision
    public function testFloatingPointPrecision(): void {
        $result = $this->calculator->add(0.1, 0.2);
        $this->assertEqualsWithDelta(0.3, $result, 0.0001);
    }
}

// More complex example - User class
class User {
    private int $id;
    private string $name;
    private string $email;
    private DateTime $createdAt;
    private ?DateTime $updatedAt;
    
    public function __construct(int $id, string $name, string $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = new DateTime();
        $this->updatedAt = null;
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function setName(string $name): void {
        $this->name = $name;
        $this->updatedAt = new DateTime();
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function setEmail(string $email): void {
        $this->validateEmail($email);
        $this->email = $email;
        $this->updatedAt = new DateTime();
    }
    
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }
    
    private function validateEmail(string $email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }
    
    public function getAge(): int {
        // This would typically calculate age from birthdate
        return rand(18, 80); // Simplified for example
    }
    
    public function isAdult(): bool {
        return $this->getAge() >= 18;
    }
}

// User repository interface
interface UserRepositoryInterface {
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): User;
    public function delete(User $user): bool;
    public function findAll(): array;
}

// Mock user repository for testing
class InMemoryUserRepository implements UserRepositoryInterface {
    private array $users = [];
    private int $nextId = 1;
    
    public function findById(int $id): ?User {
        return $this->users[$id] ?? null;
    }
    
    public function findByEmail(string $email): ?User {
        foreach ($this->users as $user) {
            if ($user->getEmail() === $email) {
                return $user;
            }
        }
        return null;
    }
    
    public function save(User $user): User {
        if ($user->getId() === 0) {
            // New user
            $id = $this->nextId++;
            $user = new User($id, $user->getName(), $user->getEmail());
        }
        
        $this->users[$user->getId()] = $user;
        return $user;
    }
    
    public function delete(User $user): bool {
        if (isset($this->users[$user->getId()])) {
            unset($this->users[$user->getId()]);
            return true;
        }
        return false;
    }
    
    public function findAll(): array {
        return array_values($this->users);
    }
}

// User service
class UserService {
    private UserRepositoryInterface $repository;
    private LoggerInterface $logger;
    
    public function __construct(UserRepositoryInterface $repository, LoggerInterface $logger) {
        $this->repository = $repository;
        $this->logger = $logger;
    }
    
    public function createUser(string $name, string $email): User {
        $this->logger->info("Creating user", ['name' => $name, 'email' => $email]);
        
        // Check if user already exists
        $existingUser = $this->repository->findByEmail($email);
        if ($existingUser !== null) {
            throw new InvalidArgumentException('User with this email already exists');
        }
        
        $user = new User(0, $name, $email);
        return $this->repository->save($user);
    }
    
    public function getUser(int $id): ?User {
        $user = $this->repository->findById($id);
        
        if ($user === null) {
            $this->logger->warning("User not found", ['id' => $id]);
        }
        
        return $user;
    }
    
    public function updateUser(int $id, string $name, string $email): User {
        $user = $this->repository->findById($id);
        
        if ($user === null) {
            throw new InvalidArgumentException('User not found');
        }
        
        // Check if email is taken by another user
        $existingUser = $this->repository->findByEmail($email);
        if ($existingUser !== null && $existingUser->getId() !== $id) {
            throw new InvalidArgumentException('Email already taken by another user');
        }
        
        $user->setName($name);
        $user->setEmail($email);
        
        $this->logger->info("User updated", ['id' => $id]);
        
        return $this->repository->save($user);
    }
    
    public function deleteUser(int $id): bool {
        $user = $this->repository->findById($id);
        
        if ($user === null) {
            throw new InvalidArgumentException('User not found');
        }
        
        $result = $this->repository->delete($user);
        
        if ($result) {
            $this->logger->info("User deleted", ['id' => $id]);
        }
        
        return $result;
    }
    
    public function getAllUsers(): array {
        return $this->repository->findAll();
    }
    
    public function searchUsers(string $query): array {
        $allUsers = $this->repository->findAll();
        $results = [];
        
        foreach ($allUsers as $user) {
            if (stripos($user->getName(), $query) !== false || 
                stripos($user->getEmail(), $query) !== false) {
                $results[] = $user;
            }
        }
        
        return $results;
    }
}

// User service tests
class UserServiceTest extends TestCase {
    private UserService $userService;
    private UserRepositoryInterface $repository;
    private LoggerInterface $logger;
    
    protected function setUp(): void {
        $this->repository = new InMemoryUserRepository();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->userService = new UserService($this->repository, $this->logger);
    }
    
    public function testCreateUser(): void {
        $user = $this->userService->createUser('John Doe', 'john@example.com');
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertGreaterThan(0, $user->getId());
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john@example.com', $user->getEmail());
        $this->assertInstanceOf(DateTime::class, $user->getCreatedAt());
    }
    
    public function testCreateUserWithDuplicateEmail(): void {
        $this->userService->createUser('John Doe', 'john@example.com');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User with this email already exists');
        
        $this->userService->createUser('Jane Doe', 'john@example.com');
    }
    
    public function testGetUser(): void {
        $createdUser = $this->userService->createUser('John Doe', 'john@example.com');
        $foundUser = $this->userService->getUser($createdUser->getId());
        
        $this->assertNotNull($foundUser);
        $this->assertEquals($createdUser->getId(), $foundUser->getId());
        $this->assertEquals('John Doe', $foundUser->getName());
    }
    
    public function testGetNonExistentUser(): void {
        $user = $this->userService->getUser(999);
        $this->assertNull($user);
    }
    
    public function testUpdateUser(): void {
        $user = $this->userService->createUser('John Doe', 'john@example.com');
        
        $updatedUser = $this->userService->updateUser(
            $user->getId(),
            'John Smith',
            'john.smith@example.com'
        );
        
        $this->assertEquals($user->getId(), $updatedUser->getId());
        $this->assertEquals('John Smith', $updatedUser->getName());
        $this->assertEquals('john.smith@example.com', $updatedUser->getEmail());
        $this->assertNotNull($updatedUser->getUpdatedAt());
    }
    
    public function testUpdateNonExistentUser(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User not found');
        
        $this->userService->updateUser(999, 'John Doe', 'john@example.com');
    }
    
    public function testUpdateUserWithDuplicateEmail(): void {
        $user1 = $this->userService->createUser('John Doe', 'john@example.com');
        $user2 = $this->userService->createUser('Jane Doe', 'jane@example.com');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email already taken by another user');
        
        $this->userService->updateUser($user1->getId(), 'John Smith', 'jane@example.com');
    }
    
    public function testDeleteUser(): void {
        $user = $this->userService->createUser('John Doe', 'john@example.com');
        
        $result = $this->userService->deleteUser($user->getId());
        
        $this->assertTrue($result);
        $this->assertNull($this->userService->getUser($user->getId()));
    }
    
    public function testDeleteNonExistentUser(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User not found');
        
        $this->userService->deleteUser(999);
    }
    
    public function testGetAllUsers(): void {
        $this->userService->createUser('John Doe', 'john@example.com');
        $this->userService->createUser('Jane Doe', 'jane@example.com');
        
        $users = $this->userService->getAllUsers();
        
        $this->assertCount(2, $users);
        $this->assertContainsOnlyInstancesOf(User::class, $users);
    }
    
    public function testSearchUsers(): void {
        $this->userService->createUser('John Doe', 'john@example.com');
        $this->userService->createUser('Jane Smith', 'jane@example.com');
        $this->userService->createUser('Bob Johnson', 'bob@example.com');
        
        $results = $this->userService->searchUsers('john');
        
        $this->assertCount(2, $results);
        
        foreach ($results as $user) {
            $this->assertStringContainsStringIgnoringCase('john', $user->getName());
        }
    }
    
    public function testSearchUsersNoResults(): void {
        $this->userService->createUser('John Doe', 'john@example.com');
        
        $results = $this->userService->searchUsers('nonexistent');
        
        $this->assertCount(0, $results);
    }
}

// Test with mocks
class UserServiceWithMockTest extends TestCase {
    private UserService $userService;
    private UserRepositoryInterface $repository;
    private LoggerInterface $logger;
    
    protected function setUp(): void {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->userService = new UserService($this->repository, $this->logger);
    }
    
    public function testCreateUserWithMock(): void {
        // Configure mock repository
        $this->repository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('john@example.com')
            ->willReturn(null);
        
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturn(new User(1, 'John Doe', 'john@example.com'));
        
        // Configure mock logger
        $this->logger
            ->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Creating user'));
        
        $user = $this->userService->createUser('John Doe', 'john@example.com');
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->getId());
    }
    
    public function testGetUserWithMock(): void {
        $expectedUser = new User(1, 'John Doe', 'john@example.com');
        
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($expectedUser);
        
        $user = $this->userService->getUser(1);
        
        $this->assertSame($expectedUser, $user);
    }
    
    public function testGetUserNotFoundWithMock(): void {
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);
        
        $this->logger
            ->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('User not found'));
        
        $user = $this->userService->getUser(999);
        
        $this->assertNull($user);
    }
}

// Custom assertions
class CustomAssertions extends TestCase {
    public function assertUserEquals(User $expected, User $actual): void {
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getName(), $actual->getName());
        $this->assertEquals($expected->getEmail(), $actual->getEmail());
    }
    
    public function assertUserArrayContains(array $users, User $targetUser): void {
        $found = false;
        
        foreach ($users as $user) {
            if ($user->getId() === $targetUser->getId()) {
                $this->assertUserEquals($targetUser, $user);
                $found = true;
                break;
            }
        }
        
        $this->assertTrue($found, 'User not found in array');
    }
}

// Test utilities
class TestUtilities {
    public static function createTestUser(int $id = null, string $name = null, string $email = null): User {
        $id = $id ?? rand(1, 1000);
        $name = $name ?? 'Test User';
        $email = $email ?? 'test@example.com';
        
        return new User($id, $name, $email);
    }
    
    public static function assertArraysEqual(array $expected, array $actual): void {
        sort($expected);
        sort($actual);
        
        if ($expected !== $actual) {
            throw new AssertionError('Arrays are not equal');
        }
    }
    
    public static function captureOutput(callable $callback): string {
        ob_start();
        $callback();
        return ob_get_clean();
    }
    
    public static function withTemporaryDirectory(callable $callback): string {
        $tempDir = sys_get_temp_dir() . '/test_' . uniqid();
        mkdir($tempDir);
        
        try {
            $callback($tempDir);
            return $tempDir;
        } finally {
            $this->removeDirectory($tempDir);
        }
    }
    
    private static function removeDirectory(string $dir): void {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            
            if (is_dir($path)) {
                self::removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        rmdir($dir);
    }
}
?>
```

## Integration Testing

### Database and API Integration Tests
```php
<?php
use PHPUnit\Framework\TestCase;

// Database integration test base class
abstract class DatabaseIntegrationTest extends TestCase {
    protected PDO $pdo;
    
    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createDatabaseSchema();
        $this->seedDatabase();
    }
    
    protected function tearDown(): void {
        $this->pdo = null;
    }
    
    abstract protected function createDatabaseSchema(): void;
    
    protected function seedDatabase(): void {
        // Override in child classes if needed
    }
    
    protected function executeSql(string $sql): void {
        $this->pdo->exec($sql);
    }
    
    protected function insert(string $table, array $data): void {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }
    
    protected function select(string $table, array $where = []): array {
        $sql = "SELECT * FROM $table";
        $params = [];
        
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                $conditions[] = "$column = :$column";
                $params[":$column"] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// User repository integration test
class UserRepositoryIntegrationTest extends DatabaseIntegrationTest {
    private UserRepository $repository;
    
    protected function setUp(): void {
        parent::setUp();
        $this->repository = new DatabaseUserRepository($this->pdo);
    }
    
    protected function createDatabaseSchema(): void {
        $this->executeSql("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME
            )
        ");
    }
    
    protected function seedDatabase(): void {
        $this->insert('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'created_at' => '2023-01-01 10:00:00'
        ]);
        
        $this->insert('users', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'created_at' => '2023-01-02 11:00:00'
        ]);
    }
    
    public function testFindById(): void {
        $user = $this->repository->findById(1);
        
        $this->assertNotNull($user);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john@example.com', $user->getEmail());
    }
    
    public function testFindByIdNotFound(): void {
        $user = $this->repository->findById(999);
        $this->assertNull($user);
    }
    
    public function testFindByEmail(): void {
        $user = $this->repository->findByEmail('jane@example.com');
        
        $this->assertNotNull($user);
        $this->assertEquals(2, $user->getId());
        $this->assertEquals('Jane Smith', $user->getName());
        $this->assertEquals('jane@example.com', $user->getEmail());
    }
    
    public function testFindByEmailNotFound(): void {
        $user = $this->repository->findByEmail('nonexistent@example.com');
        $this->assertNull($user);
    }
    
    public function testSaveNewUser(): void {
        $user = new User(0, 'Bob Johnson', 'bob@example.com');
        $savedUser = $this->repository->save($user);
        
        $this->assertGreaterThan(0, $savedUser->getId());
        $this->assertEquals('Bob Johnson', $savedUser->getName());
        $this->assertEquals('bob@example.com', $savedUser->getEmail());
        
        // Verify in database
        $dbUser = $this->select('users', ['id' => $savedUser->getId()]);
        $this->assertCount(1, $dbUser);
        $this->assertEquals('Bob Johnson', $dbUser[0]['name']);
    }
    
    public function testSaveExistingUser(): void {
        $user = $this->repository->findById(1);
        $user->setName('John Updated');
        
        $savedUser = $this->repository->save($user);
        
        $this->assertEquals(1, $savedUser->getId());
        $this->assertEquals('John Updated', $savedUser->getName());
        $this->assertNotNull($savedUser->getUpdatedAt());
    }
    
    public function testDelete(): void {
        $user = $this->repository->findById(1);
        $result = $this->repository->delete($user);
        
        $this->assertTrue($result);
        
        $deletedUser = $this->repository->findById(1);
        $this->assertNull($deletedUser);
    }
    
    public function testDeleteNonExistentUser(): void {
        $user = new User(999, 'Nonexistent', 'nonexistent@example.com');
        $result = $this->repository->delete($user);
        
        $this->assertFalse($result);
    }
    
    public function testFindAll(): void {
        $users = $this->repository->findAll();
        
        $this->assertCount(2, $users);
        $this->assertContainsOnlyInstancesOf(User::class, $users);
    }
}

// Database user repository implementation
class DatabaseUserRepository implements UserRepositoryInterface {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function findById(int $id): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return $this->hydrateUser($data);
    }
    
    public function findByEmail(string $email): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return $this->hydrateUser($data);
    }
    
    public function save(User $user): User {
        if ($user->getId() === 0) {
            return $this->insert($user);
        } else {
            return $this->update($user);
        }
    }
    
    public function delete(User $user): bool {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $result = $stmt->execute([$user->getId()]);
        
        return $result && $stmt->rowCount() > 0;
    }
    
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'hydrateUser'], $data);
    }
    
    private function insert(User $user): User {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email, created_at) VALUES (?, ?, ?)"
        );
        
        $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $user->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        
        $user = new User(
            $this->pdo->lastInsertId(),
            $user->getName(),
            $user->getEmail()
        );
        
        return $user;
    }
    
    private function update(User $user): User {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET name = ?, email = ?, updated_at = ? WHERE id = ?"
        );
        
        $updatedAt = $user->getUpdatedAt() ?? new DateTime();
        
        $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $updatedAt->format('Y-m-d H:i:s'),
            $user->getId()
        ]);
        
        return $user;
    }
    
    private function hydrateUser(array $data): User {
        $user = new User($data['id'], $data['name'], $data['email']);
        
        if (isset($data['created_at'])) {
            $user->setCreatedAt(new DateTime($data['created_at']));
        }
        
        if (isset($data['updated_at']) && $data['updated_at'] !== null) {
            $user->setUpdatedAt(new DateTime($data['updated_at']));
        }
        
        return $user;
    }
}

// API integration test
class ApiIntegrationTest extends TestCase {
    private HttpClientInterface $httpClient;
    private string $baseUrl;
    
    protected function setUp(): void {
        $this->baseUrl = 'http://localhost:8000/api';
        $this->httpClient = new GuzzleHttpClient();
    }
    
    public function testGetUsers(): void {
        $response = $this->httpClient->get($this->baseUrl . '/users');
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }
    
    public function testGetUser(): void {
        $response = $this->httpClient->get($this->baseUrl . '/users/1');
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('email', $data);
    }
    
    public function testGetUserNotFound(): void {
        $response = $this->httpClient->get($this->baseUrl . '/users/999');
        
        $this->assertEquals(404, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('error', $data);
    }
    
    public function testCreateUser(): void {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];
        
        $response = $this->httpClient->post($this->baseUrl . '/users', json_encode($userData));
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('Test User', $data['name']);
        $this->assertEquals('test@example.com', $data['email']);
    }
    
    public function testCreateUserInvalidData(): void {
        $userData = [
            'name' => '',
            'email' => 'invalid-email'
        ];
        
        $response = $this->httpClient->post($this->baseUrl . '/users', json_encode($userData));
        
        $this->assertEquals(400, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('error', $data);
    }
    
    public function testUpdateUser(): void {
        $userData = [
            'name' => 'Updated User',
            'email' => 'updated@example.com'
        ];
        
        $response = $this->httpClient->put($this->baseUrl . '/users/1', json_encode($userData));
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Updated User', $data['name']);
        $this->assertEquals('updated@example.com', $data['email']);
    }
    
    public function testUpdateUserNotFound(): void {
        $userData = [
            'name' => 'Updated User',
            'email' => 'updated@example.com'
        ];
        
        $response = $this->httpClient->put($this->baseUrl . '/users/999', json_encode($userData));
        
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testDeleteUser(): void {
        $response = $this->httpClient->delete($this->baseUrl . '/users/1');
        
        $this->assertEquals(204, $response->getStatusCode());
    }
    
    public function testDeleteUserNotFound(): void {
        $response = $this->httpClient->delete($this->baseUrl . '/users/999');
        
        $this->assertEquals(404, $response->getStatusCode());
    }
}

// HTTP client interface
interface HttpClientInterface {
    public function get(string $url): HttpResponse;
    public function post(string $url, string $body): HttpResponse;
    public function put(string $url, string $body): HttpResponse;
    public function delete(string $url): HttpResponse;
}

// HTTP response
class HttpResponse {
    private int $statusCode;
    private string $body;
    private array $headers;
    
    public function __construct(int $statusCode, string $body, array $headers = []) {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }
    
    public function getStatusCode(): int {
        return $this->statusCode;
    }
    
    public function getBody(): string {
        return $this->body;
    }
    
    public function getHeaders(): array {
        return $this->headers;
    }
    
    public function getHeader(string $name): ?string {
        return $this->headers[$name] ?? null;
    }
}

// Mock HTTP client for testing
class MockHttpClient implements HttpClientInterface {
    private array $responses = [];
    private array $requests = [];
    
    public function setResponse(string $method, string $url, HttpResponse $response): void {
        $key = strtoupper($method) . ' ' . $url;
        $this->responses[$key] = $response;
    }
    
    public function get(string $url): HttpResponse {
        return $this->makeRequest('GET', $url);
    }
    
    public function post(string $url, string $body): HttpResponse {
        return $this->makeRequest('POST', $url, $body);
    }
    
    public function put(string $url, string $body): HttpResponse {
        return $this->makeRequest('PUT', $url, $body);
    }
    
    public function delete(string $url): HttpResponse {
        return $this->makeRequest('DELETE', $url);
    }
    
    private function makeRequest(string $method, string $url, string $body = null): HttpResponse {
        $key = strtoupper($method) . ' ' . $url;
        
        $this->requests[] = [
            'method' => $method,
            'url' => $url,
            'body' => $body
        ];
        
        if (isset($this->responses[$key])) {
            return $this->responses[$key];
        }
        
        // Default response
        return new HttpResponse(404, '{"error": "Not found"}');
    }
    
    public function getRequests(): array {
        return $this->requests;
    }
    
    public function getLastRequest(): ?array {
        return end($this->requests) ?: null;
    }
    
    public function assertRequestMade(string $method, string $url): void {
        $key = strtoupper($method) . ' ' . $url;
        
        foreach ($this->requests as $request) {
            if (strtoupper($request['method']) . ' ' . $request['url'] === $key) {
                return;
            }
        }
        
        throw new AssertionError("Request $method $url was not made");
    }
    
    public function reset(): void {
        $this->requests = [];
        $this->responses = [];
    }
}

// Service integration test with mock HTTP client
class UserServiceIntegrationTest extends TestCase {
    private UserService $userService;
    private MockHttpClient $httpClient;
    private UserRepositoryInterface $repository;
    
    protected function setUp(): void {
        $this->httpClient = new MockHttpClient();
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->repository, new NullLogger());
    }
    
    public function testGetUserFromExternalApi(): void {
        // Set up mock HTTP response
        $this->httpClient->setResponse(
            'GET',
            'https://api.example.com/users/1',
            new HttpResponse(200, json_encode([
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com'
            ]))
        );
        
        // Set up mock repository
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn(null); // User not in local database
        
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturn(new User(1, 'John Doe', 'john@example.com'));
        
        $user = $this->userService->getUserFromExternalApi(1);
        
        $this->assertNotNull($user);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('John Doe', $user->getName());
        
        $this->httpClient->assertRequestMade('GET', 'https://api.example.com/users/1');
    }
    
    public function testGetUserFromExternalApiNotFound(): void {
        // Set up mock HTTP response
        $this->httpClient->setResponse(
            'GET',
            'https://api.example.com/users/999',
            new HttpResponse(404, json_encode(['error' => 'User not found']))
        );
        
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);
        
        $user = $this->userService->getUserFromExternalApi(999);
        
        $this->assertNull($user);
        
        $this->httpClient->assertRequestMade('GET', 'https://api.example.com/users/999');
    }
}

// Integration test with real database
class RealDatabaseIntegrationTest extends TestCase {
    private static ?PDO $pdo = null;
    private UserRepository $repository;
    
    public static function setUpBeforeClass(): void {
        // Use a test database
        self::$pdo = new PDO('mysql:host=localhost;dbname=test_db', 'test_user', 'test_pass');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public static function tearDownAfterClass(): void {
        self::$pdo = null;
    }
    
    protected function setUp(): void {
        $this->repository = new DatabaseUserRepository(self::$pdo);
        $this->cleanupDatabase();
        $this->seedDatabase();
    }
    
    private function cleanupDatabase(): void {
        self::$pdo->exec("DELETE FROM users");
    }
    
    private function seedDatabase(): void {
        $stmt = self::$pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute(['John Doe', 'john@example.com']);
        $stmt->execute(['Jane Smith', 'jane@example.com']);
    }
    
    public function testRealDatabaseOperations(): void {
        // Test find
        $user = $this->repository->findById(1);
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->getName());
        
        // Test save
        $newUser = new User(0, 'Bob Johnson', 'bob@example.com');
        $savedUser = $this->repository->save($newUser);
        $this->assertGreaterThan(0, $savedUser->getId());
        
        // Test update
        $savedUser->setName('Bob Updated');
        $this->repository->save($savedUser);
        
        $updatedUser = $this->repository->findById($savedUser->getId());
        $this->assertEquals('Bob Updated', $updatedUser->getName());
        
        // Test delete
        $result = $this->repository->delete($updatedUser);
        $this->assertTrue($result);
        
        $deletedUser = $this->repository->findById($savedUser->getId());
        $this->assertNull($deletedUser);
    }
}

// Test data factory
class UserFactory {
    public static function create(array $attributes = []): User {
        $defaults = [
            'id' => rand(1, 1000),
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];
        
        $attributes = array_merge($defaults, $attributes);
        
        return new User($attributes['id'], $attributes['name'], $attributes['email']);
    }
    
    public static function createMany(int $count, array $attributes = []): array {
        $users = [];
        
        for ($i = 0; $i < $count; $i++) {
            $userAttributes = $attributes;
            
            if (!isset($userAttributes['id'])) {
                $userAttributes['id'] = $i + 1;
            }
            
            if (!isset($userAttributes['email'])) {
                $userAttributes['email'] = "user$i@example.com";
            }
            
            $users[] = self::create($userAttributes);
        }
        
        return $users;
    }
}

// Test scenario builder
class UserTestScenario {
    private array $users = [];
    private UserRepositoryInterface $repository;
    
    public function __construct(UserRepositoryInterface $repository) {
        $this->repository = $repository;
    }
    
    public function withUser(string $name, string $email): self {
        $user = new User(0, $name, $email);
        $this->users[] = $this->repository->save($user);
        return $this;
    }
    
    public function withUsers(array $userData): self {
        foreach ($userData as $data) {
            $this->withUser($data['name'], $data['email']);
        }
        return $this;
    }
    
    public function getUsers(): array {
        return $this->users;
    }
    
    public function getFirstUser(): ?User {
        return $this->users[0] ?? null;
    }
    
    public function getLastUser(): ?User {
        return $this->users[count($this->users) - 1] ?? null;
    }
    
    public function getUserByEmail(string $email): ?User {
        foreach ($this->users as $user) {
            if ($user->getEmail() === $email) {
                return $user;
            }
        }
        return null;
    }
}
?>
```

## End-to-End Testing

### Browser Automation and Full Stack Testing
```php
<?php
use PHPUnit\Framework\TestCase;

// Web driver interface
interface WebDriverInterface {
    public function get(string $url): void;
    public function findElement(string $selector): WebElement;
    public function findElements(string $selector): array;
    public function click(string $selector): void;
    public function type(string $selector, string $text): void;
    public function getText(string $selector): string;
    public function waitForElement(string $selector, int $timeout = 10): WebElement;
    public function waitForElementToDisappear(string $selector, int $timeout = 10): void;
    public function takeScreenshot(string $filename): void;
    public function getCurrentUrl(): string;
    public function getTitle(): string;
    public function refresh(): void;
    public function back(): void;
    public function forward(): void;
    public function quit(): void;
}

// Web element interface
interface WebElement {
    public function click(): void;
    public function sendKeys(string $text): void;
    public function getText(): string;
    public function getAttribute(string $name): string;
    public function isDisplayed(): bool;
    public function isEnabled(): bool;
    public function isSelected(): bool;
    public function findElement(string $selector): WebElement;
    public function findElements(string $selector): array;
}

// Page object pattern
abstract class PageObject {
    protected WebDriverInterface $driver;
    protected string $baseUrl;
    
    public function __construct(WebDriverInterface $driver, string $baseUrl) {
        $this->driver = $driver;
        $this->baseUrl = $baseUrl;
    }
    
    abstract public function waitForPageLoad(): void;
    
    protected function findElement(string $selector): WebElement {
        return $this->driver->findElement($selector);
    }
    
    protected function click(string $selector): void {
        $this->driver->click($selector);
    }
    
    protected function type(string $selector, string $text): void {
        $this->driver->type($selector, $text);
    }
    
    protected function getText(string $selector): string {
        return $this->driver->getText($selector);
    }
    
    protected function waitForElement(string $selector, int $timeout = 10): WebElement {
        return $this->driver->waitForElement($selector, $timeout);
    }
    
    protected function waitForElementToDisappear(string $selector, int $timeout = 10): void {
        $this->driver->waitForElementToDisappear($selector, $timeout);
    }
    
    public function takeScreenshot(string $filename): void {
        $this->driver->takeScreenshot($filename);
    }
}

// Login page
class LoginPage extends PageObject {
    private string $usernameField = '#username';
    private string $passwordField = '#password';
    private string $loginButton = '#login-button';
    private string $errorMessage = '.error-message';
    private string $successMessage = '.success-message';
    
    public function waitForPageLoad(): void {
        $this->waitForElement($this->usernameField);
        $this->waitForElement($this->passwordField);
        $this->waitForElement($this->loginButton);
    }
    
    public function login(string $username, string $password): DashboardPage {
        $this->type($this->usernameField, $username);
        $this->type($this->passwordField, $password);
        $this->click($this->loginButton);
        
        $dashboard = new DashboardPage($this->driver, $this->baseUrl);
        $dashboard->waitForPageLoad();
        
        return $dashboard;
    }
    
    public function loginWithInvalidCredentials(string $username, string $password): void {
        $this->type($this->usernameField, $username);
        $this->type($this->passwordField, $password);
        $this->click($this->loginButton);
        
        $this->waitForElement($this->errorMessage);
    }
    
    public function getErrorMessage(): string {
        return $this->getText($this->errorMessage);
    }
    
    public function getSuccessMessage(): string {
        return $this->getText($this->successMessage);
    }
    
    public function isLoginFormDisplayed(): bool {
        try {
            $this->findElement($this->usernameField);
            $this->findElement($this->passwordField);
            $this->findElement($this->loginButton);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Dashboard page
class DashboardPage extends PageObject {
    private string $welcomeMessage = '.welcome-message';
    private string $userMenu = '.user-menu';
    private string $logoutButton = '#logout-button';
    private string $usersLink = '#users-link';
    private string $settingsLink = '#settings-link';
    
    public function waitForPageLoad(): void {
        $this->waitForElement($this->welcomeMessage);
    }
    
    public function getWelcomeMessage(): string {
        return $this->getText($this->welcomeMessage);
    }
    
    public function logout(): LoginPage {
        $this->click($this->userMenu);
        $this->waitForElement($this->logoutButton);
        $this->click($this->logoutButton);
        
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        
        return $loginPage;
    }
    
    public function navigateToUsers(): UsersPage {
        $this->click($this->usersLink);
        
        $usersPage = new UsersPage($this->driver, $this->baseUrl);
        $usersPage->waitForPageLoad();
        
        return $usersPage;
    }
    
    public function navigateToSettings(): SettingsPage {
        $this->click($this->settingsLink);
        
        $settingsPage = new SettingsPage($this->driver, $this->baseUrl);
        $settingsPage->waitForPageLoad();
        
        return $settingsPage;
    }
    
    public function isLoggedIn(): bool {
        try {
            $this->findElement($this->welcomeMessage);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Users page
class UsersPage extends PageObject {
    private string $addUserButton = '#add-user-button';
    private string $userTable = '#users-table';
    private string $userRows = '#users-table tbody tr';
    private string $searchField = '#search-field';
    private string $searchButton = '#search-button';
    
    public function waitForPageLoad(): void {
        $this->waitForElement($this->userTable);
    }
    
    public function clickAddUser(): UserFormPage {
        $this->click($this->addUserButton);
        
        $userFormPage = new UserFormPage($this->driver, $this->baseUrl);
        $userFormPage->waitForPageLoad();
        
        return $userFormPage;
    }
    
    public function searchUsers(string $query): void {
        $this->type($this->searchField, $query);
        $this->click($this->searchButton);
        $this->waitForPageLoad(); // Wait for table to update
    }
    
    public function getUserCount(): int {
        $rows = $this->driver->findElements($this->userRows);
        return count($rows);
    }
    
    public function getUserNames(): array {
        $names = [];
        $rows = $this->driver->findElements($this->userRows);
        
        foreach ($rows as $row) {
            $nameCell = $row->findElement('td:nth-child(2)');
            $names[] = $nameCell->getText();
        }
        
        return $names;
    }
    
    public function hasUser(string $name): bool {
        return in_array($name, $this->getUserNames());
    }
    
    public function editUser(string $name): UserFormPage {
        $rows = $this->driver->findElements($this->userRows);
        
        foreach ($rows as $row) {
            $nameCell = $row->findElement('td:nth-child(2)');
            
            if ($nameCell->getText() === $name) {
                $editButton = $row->findElement('.edit-button');
                $editButton->click();
                
                $userFormPage = new UserFormPage($this->driver, $this->baseUrl);
                $userFormPage->waitForPageLoad();
                
                return $userFormPage;
            }
        }
        
        throw new Exception("User '$name' not found");
    }
    
    public function deleteUser(string $name): UsersPage {
        $rows = $this->driver->findElements($this->userRows);
        
        foreach ($rows as $row) {
            $nameCell = $row->findElement('td:nth-child(2)');
            
            if ($nameCell->getText() === $name) {
                $deleteButton = $row->findElement('.delete-button');
                $deleteButton->click();
                
                // Confirm deletion
                $this->driver->waitForElement('.confirm-delete');
                $this->driver->click('.confirm-delete');
                
                $this->waitForPageLoad();
                
                return $this;
            }
        }
        
        throw new Exception("User '$name' not found");
    }
}

// User form page
class UserFormPage extends PageObject {
    private string $nameField = '#name';
    private string $emailField = '#email';
    private string $saveButton = '#save-button';
    private string $cancelButton = '#cancel-button';
    private string $formTitle = '.form-title';
    
    public function waitForPageLoad(): void {
        $this->waitForElement($this->nameField);
        $this->waitForElement($this->emailField);
        $this->waitForElement($this->saveButton);
    }
    
    public function getFormTitle(): string {
        return $this->getText($this->formTitle);
    }
    
    public function fillForm(string $name, string $email): void {
        $this->type($this->nameField, $name);
        $this->type($this->emailField, $email);
    }
    
    public function save(): UsersPage {
        $this->click($this->saveButton);
        
        $usersPage = new UsersPage($this->driver, $this->baseUrl);
        $usersPage->waitForPageLoad();
        
        return $usersPage;
    }
    
    public function cancel(): UsersPage {
        $this->click($this->cancelButton);
        
        $usersPage = new UsersPage($this->driver, $this->baseUrl);
        $usersPage->waitForPageLoad();
        
        return $usersPage;
    }
    
    public function getFieldValue(string $field): string {
        return $this->getText($field);
    }
    
    public function isFormValid(): bool {
        // Check if save button is enabled
        $saveButton = $this->findElement($this->saveButton);
        return $saveButton->isEnabled();
    }
}

// Settings page
class SettingsPage extends PageObject {
    private string $themeSelect = '#theme-select';
    private string $languageSelect = '#language-select';
    private string $saveButton = '#save-settings';
    private string $successMessage = '.success-message';
    
    public function waitForPageLoad(): void {
        $this->waitForElement($this->themeSelect);
        $this->waitForElement($this->languageSelect);
    }
    
    public function selectTheme(string $theme): void {
        $this->driver->findElement($this->themeSelect)
            ->findElement("option[value='$theme']")
            ->click();
    }
    
    public function selectLanguage(string $language): void {
        $this->driver->findElement($this->languageSelect)
            ->findElement("option[value='$language']")
            ->click();
    }
    
    public function saveSettings(): void {
        $this->click($this->saveButton);
        $this->waitForElement($this->successMessage);
    }
    
    public function getSuccessMessage(): string {
        return $this->getText($this->successMessage);
    }
    
    public function getSelectedTheme(): string {
        return $this->driver->findElement($this->themeSelect)
            ->getAttribute('value');
    }
    
    public function getSelectedLanguage(): string {
        return $this->driver->findElement($this->languageSelect)
            ->getAttribute('value');
    }
}

// End-to-end test base class
abstract class EndToEndTest extends TestCase {
    protected WebDriverInterface $driver;
    protected string $baseUrl;
    
    protected function setUp(): void {
        $this->baseUrl = $_ENV['BASE_URL'] ?? 'http://localhost:8000';
        $this->driver = $this->createWebDriver();
        $this->driver->get($this->baseUrl);
    }
    
    protected function tearDown(): void {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
    
    abstract protected function createWebDriver(): WebDriverInterface;
    
    protected function takeScreenshot(string $name): void {
        $filename = __DIR__ . '/screenshots/' . $name . '_' . date('Y-m-d_H-i-s') . '.png';
        $this->driver->takeScreenshot($filename);
    }
    
    protected function onNotSuccessfulTest(Throwable $t): void {
        $this->takeScreenshot('failure');
        parent::onNotSuccessfulTest($t);
    }
}

// Login end-to-end test
class LoginEndToEndTest extends EndToEndTest {
    protected function createWebDriver(): WebDriverInterface {
        return new ChromeWebDriver();
    }
    
    public function testSuccessfulLogin(): void {
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        
        $dashboardPage = $loginPage->login('admin', 'password');
        
        $this->assertStringContainsString('Welcome', $dashboardPage->getWelcomeMessage());
        $this->assertTrue($dashboardPage->isLoggedIn());
    }
    
    public function testInvalidLogin(): void {
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        
        $loginPage->loginWithInvalidCredentials('admin', 'wrongpassword');
        
        $this->assertStringContainsString('Invalid', $loginPage->getErrorMessage());
        $this->assertTrue($loginPage->isLoginFormDisplayed());
    }
    
    public function testLogout(): void {
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        
        $dashboardPage = $loginPage->login('admin', 'password');
        $loginPageAfterLogout = $dashboardPage->logout();
        
        $this->assertTrue($loginPageAfterLogout->isLoginFormDisplayed());
    }
}

// User management end-to-end test
class UserManagementEndToEndTest extends EndToEndTest {
    protected function createWebDriver(): WebDriverInterface {
        return new ChromeWebDriver();
    }
    
    public function testCreateUser(): void {
        // Login first
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        $dashboardPage = $loginPage->login('admin', 'password');
        
        // Navigate to users page
        $usersPage = $dashboardPage->navigateToUsers();
        $initialCount = $usersPage->getUserCount();
        
        // Add new user
        $userFormPage = $usersPage->clickAddUser();
        $this->assertStringContainsString('Add User', $userFormPage->getFormTitle());
        
        $usersPage = $userFormPage
            ->fillForm('John Doe', 'john@example.com')
            ->save();
        
        // Verify user was added
        $this->assertEquals($initialCount + 1, $usersPage->getUserCount());
        $this->assertTrue($usersPage->hasUser('John Doe'));
        
        // Logout
        $dashboardPage = $usersPage->navigateToDashboard();
        $dashboardPage->logout();
    }
    
    public function testEditUser(): void {
        // Login and navigate to users
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        $dashboardPage = $loginPage->login('admin', 'password');
        $usersPage = $dashboardPage->navigateToUsers();
        
        // Edit user
        $userFormPage = $usersPage->editUser('John Doe');
        $this->assertStringContainsString('Edit User', $userFormPage->getFormTitle());
        
        $usersPage = $userFormPage
            ->fillForm('John Smith', 'john.smith@example.com')
            ->save();
        
        // Verify user was updated
        $this->assertFalse($usersPage->hasUser('John Doe'));
        $this->assertTrue($usersPage->hasUser('John Smith'));
        
        // Logout
        $dashboardPage = $usersPage->navigateToDashboard();
        $dashboardPage->logout();
    }
    
    public function testDeleteUser(): void {
        // Login and navigate to users
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        $dashboardPage = $loginPage->login('admin', 'password');
        $usersPage = $dashboardPage->navigateToUsers();
        
        $initialCount = $usersPage->getUserCount();
        
        // Delete user
        $usersPage = $usersPage->deleteUser('John Smith');
        
        // Verify user was deleted
        $this->assertEquals($initialCount - 1, $usersPage->getUserCount());
        $this->assertFalse($usersPage->hasUser('John Smith'));
        
        // Logout
        $dashboardPage = $usersPage->navigateToDashboard();
        $dashboardPage->logout();
    }
    
    public function testSearchUsers(): void {
        // Login and navigate to users
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        $dashboardPage = $loginPage->login('admin', 'password');
        $usersPage = $dashboardPage->navigateToUsers();
        
        // Search for user
        $usersPage->searchUsers('Jane');
        
        // Verify search results
        $this->assertTrue($usersPage->hasUser('Jane Smith'));
        $this->assertFalse($usersPage->hasUser('John Doe')); // Assuming John exists but doesn't match search
        
        // Logout
        $dashboardPage = $usersPage->navigateToDashboard();
        $dashboardPage->logout();
    }
}

// Settings end-to-end test
class SettingsEndToEndTest extends EndToEndTest {
    protected function createWebDriver(): WebDriverInterface {
        return new ChromeWebDriver();
    }
    
    public function testUpdateSettings(): void {
        // Login first
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        $dashboardPage = $loginPage->login('admin', 'password');
        
        // Navigate to settings
        $settingsPage = $dashboardPage->navigateToSettings();
        
        // Update settings
        $settingsPage->selectTheme('dark');
        $settingsPage->selectLanguage('fr');
        $settingsPage->saveSettings();
        
        // Verify settings were saved
        $this->assertStringContainsString('Settings saved', $settingsPage->getSuccessMessage());
        $this->assertEquals('dark', $settingsPage->getSelectedTheme());
        $this->assertEquals('fr', $settingsPage->getSelectedLanguage());
        
        // Logout
        $dashboardPage = $settingsPage->navigateToDashboard();
        $dashboardPage->logout();
    }
}

// Test data builder for end-to-end tests
class UserDataBuilder {
    private string $name = 'Test User';
    private string $email = 'test@example.com';
    private string $password = 'password';
    private array $roles = ['user'];
    
    public function withName(string $name): self {
        $this->name = $name;
        return $this;
    }
    
    public function withEmail(string $email): self {
        $this->email = $email;
        return $this;
    }
    
    public function withPassword(string $password): self {
        $this->password = $password;
        return $this;
    }
    
    public function withRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }
    
    public function build(): array {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles
        ];
    }
    
    public static function anAdmin(): self {
        return (new self())
            ->withName('Admin User')
            ->withEmail('admin@example.com')
            ->withPassword('admin123')
            ->withRoles(['admin', 'user']);
    }
    
    public static function aRegularUser(): self {
        return (new self())
            ->withName('Regular User')
            ->withEmail('user@example.com')
            ->withPassword('user123')
            ->withRoles(['user']);
    }
}

// Test scenario for end-to-end tests
class TestScenario {
    private WebDriverInterface $driver;
    private LoginPage $loginPage;
    private DashboardPage $dashboardPage;
    
    public function __construct(WebDriverInterface $driver, string $baseUrl) {
        $this->driver = $driver;
        $this->loginPage = new LoginPage($driver, $baseUrl);
    }
    
    public function loginAsAdmin(): self {
        $this->dashboardPage = $this->loginPage->login('admin', 'password');
        return $this;
    }
    
    public function loginAsUser(): self {
        $this->dashboardPage = $this->loginPage->login('user', 'password');
        return $this;
    }
    
    public function navigateToUsers(): UsersPage {
        return $this->dashboardPage->navigateToUsers();
    }
    
    public function navigateToSettings(): SettingsPage {
        return $this->dashboardPage->navigateToSettings();
    }
    
    public function logout(): LoginPage {
        $this->loginPage = $this->dashboardPage->logout();
        $this->dashboardPage = null;
        return $this->loginPage;
    }
    
    public function isLoggedIn(): bool {
        return $this->dashboardPage !== null && $this->dashboardPage->isLoggedIn();
    }
}

// Performance test for end-to-end
class PerformanceEndToEndTest extends EndToEndTest {
    protected function createWebDriver(): WebDriverInterface {
        return new ChromeWebDriver();
    }
    
    public function testPageLoadPerformance(): void {
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        
        $startTime = microtime(true);
        $loginPage->waitForPageLoad();
        $loadTime = microtime(true) - $startTime;
        
        $this->assertLessThan(3.0, $loadTime, 'Page should load within 3 seconds');
    }
    
    public function testUserCreationPerformance(): void {
        // Login
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        $dashboardPage = $loginPage->login('admin', 'password');
        
        // Navigate to users
        $usersPage = $dashboardPage->navigateToUsers();
        
        // Measure user creation time
        $startTime = microtime(true);
        $userFormPage = $usersPage->clickAddUser();
        $usersPage = $userFormPage
            ->fillForm('Performance Test User', 'perf@example.com')
            ->save();
        $creationTime = microtime(true) - $startTime;
        
        $this->assertLessThan(2.0, $creationTime, 'User creation should complete within 2 seconds');
        
        // Logout
        $dashboardPage = $usersPage->navigateToDashboard();
        $dashboardPage->logout();
    }
}

// Accessibility test
class AccessibilityEndToEndTest extends EndToEndTest {
    protected function createWebDriver(): WebDriverInterface {
        return new ChromeWebDriver();
    }
    
    public function testKeyboardNavigation(): void {
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        
        // Test Tab navigation
        $this->driver->getKeyboard()->pressKey('\t'); // Tab to username field
        $activeElement = $this->driver->getActiveElement();
        $this->assertEquals('username', $activeElement->getAttribute('id'));
        
        $this->driver->getKeyboard()->pressKey('\t'); // Tab to password field
        $activeElement = $this->driver->getActiveElement();
        $this->assertEquals('password', $activeElement->getAttribute('id'));
        
        $this->driver->getKeyboard()->pressKey('\t'); // Tab to login button
        $activeElement = $this->driver->getActiveElement();
        $this->assertEquals('login-button', $activeElement->getAttribute('id'));
    }
    
    public function testAriaLabels(): void {
        $loginPage = new LoginPage($this->driver, $this->baseUrl);
        $loginPage->waitForPageLoad();
        
        $usernameField = $this->driver->findElement('#username');
        $this->assertNotEmpty($usernameField->getAttribute('aria-label'));
        
        $passwordField = $this->driver->findElement('#password');
        $this->assertNotEmpty($passwordField->getAttribute('aria-label'));
        
        $loginButton = $this->driver->findElement('#login-button');
        $this->assertNotEmpty($loginButton->getAttribute('aria-label'));
    }
}
?>
```

## Testing Best Practices

### Test Organization and Maintenance
```php
<?php
// Test configuration
class TestConfig {
    public static function get(string $key, $default = null) {
        $config = [
            'database' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'name' => $_ENV['DB_NAME'] ?? 'test_db',
                'user' => $_ENV['DB_USER'] ?? 'test_user',
                'password' => $_ENV['DB_PASSWORD'] ?? 'test_pass'
            ],
            'api' => [
                'base_url' => $_ENV['API_BASE_URL'] ?? 'http://localhost:8000/api',
                'timeout' => $_ENV['API_TIMEOUT'] ?? 30
            ],
            'browser' => [
                'headless' => $_ENV['BROWSER_HEADLESS'] ?? true,
                'width' => $_ENV['BROWSER_WIDTH'] ?? 1920,
                'height' => $_ENV['BROWSER_HEIGHT'] ?? 1080
            ]
        ];
        
        return $this->getNestedValue($config, $key, $default);
    }
    
    private static function getNestedValue(array $array, string $key, $default = null) {
        $keys = explode('.', $key);
        $value = $array;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

// Test base class with common functionality
abstract class BaseTestCase extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        $this->configureTestEnvironment();
    }
    
    protected function tearDown(): void {
        $this->cleanupTestEnvironment();
        parent::tearDown();
    }
    
    protected function configureTestEnvironment(): void {
        // Set error reporting for tests
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        
        // Set timezone
        date_default_timezone_set('UTC');
        
        // Configure any test-specific settings
    }
    
    protected function cleanupTestEnvironment(): void {
        // Clean up any test artifacts
    }
    
    protected function assertArraysSimilar(array $expected, array $actual, string $message = ''): void {
        $this->assertEquals(count($expected), count($actual), $message ?: 'Array sizes differ');
        
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $actual, $message ?: "Missing key '$key' in actual array");
            $this->assertEquals($value, $actual[$key], $message ?: "Value for key '$key' differs");
        }
    }
    
    protected function assertDateTimeEquals(DateTime $expected, DateTime $actual, string $message = ''): void {
        $this->assertEquals($expected->format('Y-m-d H:i:s'), $actual->format('Y-m-d H:i:s'), $message);
    }
    
    protected function assertJsonResponse(array $expected, string $actualJson, string $message = ''): void {
        $actual = json_decode($actualJson, true);
        $this->assertNotNull($actual, $message ?: 'Invalid JSON response');
        $this->assertArraysSimilar($expected, $actual, $message);
    }
    
    protected function createTempFile(string $content = ''): string {
        $filename = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($filename, $content);
        return $filename;
    }
    
    protected function createTempDirectory(): string {
        $dirname = sys_get_temp_dir() . '/test_' . uniqid();
        mkdir($dirname);
        return $dirname;
    }
    
    protected function removeTempFile(string $filename): void {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
    protected function removeTempDirectory(string $dirname): void {
        if (is_dir($dirname)) {
            $files = array_diff(scandir($dirname), ['.', '..']);
            
            foreach ($files as $file) {
                $path = $dirname . '/' . $file;
                
                if (is_dir($path)) {
                    $this->removeTempDirectory($path);
                } else {
                    unlink($path);
                }
            }
            
            rmdir($dirname);
        }
    }
}

// Test trait for database operations
trait DatabaseTestTrait {
    protected PDO $pdo;
    
    protected function setupDatabase(): void {
        $config = TestConfig::get('database');
        
        $this->pdo = new PDO(
            "mysql:host={$config['host']};dbname={$config['name']}",
            $config['user'],
            $config['password']
        );
        
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->beginTransaction();
    }
    
    protected function teardownDatabase(): void {
        if (isset($this->pdo)) {
            $this->pdo->rollBack();
            $this->pdo = null;
        }
    }
    
    protected function insertTestData(string $table, array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        
        return $this->pdo->lastInsertId();
    }
    
    protected function selectTestData(string $table, array $where = []): array {
        $sql = "SELECT * FROM $table";
        $params = [];
        
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                $conditions[] = "$column = :$column";
                $params[":$column"] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    protected function assertDatabaseHas(string $table, array $data): void {
        $results = $this->selectTestData($table, $data);
        $this->assertNotEmpty($results, "Database table '$table' should have matching record");
    }
    
    protected function assertDatabaseMissing(string $table, array $data): void {
        $results = $this->selectTestData($table, $data);
        $this->assertEmpty($results, "Database table '$table' should not have matching record");
    }
    
    protected function assertDatabaseCount(string $table, int $expected): void {
        $sql = "SELECT COUNT(*) as count FROM $table";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        $count = $stmt->fetchColumn();
        $this->assertEquals($expected, $count, "Database table '$table' should have $expected records");
    }
}

// Test trait for HTTP operations
trait HttpTestTrait {
    protected HttpClientInterface $httpClient;
    
    protected function setupHttpClient(): void {
        $this->httpClient = new GuzzleHttpClient([
            'base_uri' => TestConfig::get('api.base_url'),
            'timeout' => TestConfig::get('api.timeout')
        ]);
    }
    
    protected function get(string $uri, array $headers = []): HttpResponse {
        return $this->httpClient->get($uri, $headers);
    }
    
    protected function post(string $uri, array $data = [], array $headers = []): HttpResponse {
        return $this->httpClient->post($uri, json_encode($data), $headers);
    }
    
    protected function put(string $uri, array $data = [], array $headers = []): HttpResponse {
        return $this->httpClient->put($uri, json_encode($data), $headers);
    }
    
    protected function delete(string $uri, array $headers = []): HttpResponse {
        return $this->httpClient->delete($uri, $headers);
    }
    
    protected function assertResponseSuccessful(HttpResponse $response): void {
        $this->assertGreaterThanOrEqual(200, $response->getStatusCode());
        $this->assertLessThan(300, $response->getStatusCode());
    }
    
    protected function assertResponseError(HttpResponse $response, int $expectedCode = null): void {
        $this->assertGreaterThanOrEqual(400, $response->getStatusCode());
        $this->assertLessThan(600, $response->getStatusCode());
        
        if ($expectedCode !== null) {
            $this->assertEquals($expectedCode, $response->getStatusCode());
        }
    }
    
    protected function assertResponseContains(HttpResponse $response, string $content): void {
        $this->assertStringContainsString($content, $response->getBody());
    }
    
    protected function assertResponseJson(HttpResponse $response, array $expectedData = null): void {
        $this->assertStringContainsString('application/json', $response->getHeader('Content-Type'));
        
        if ($expectedData !== null) {
            $data = json_decode($response->getBody(), true);
            $this->assertNotNull($data, 'Response should contain valid JSON');
            $this->assertArraysSimilar($expectedData, $data);
        }
    }
}

// Test data provider
class TestDataProvider {
    public static function validUserDataProvider(): array {
        return [
            ['John Doe', 'john@example.com'],
            ['Jane Smith', 'jane@example.com'],
            ['Bob Johnson', 'bob@example.com'],
            ['Alice Brown', 'alice@example.com']
        ];
    }
    
    public static function invalidEmailProvider(): array {
        return [
            ['invalid-email'],
            ['@example.com'],
            ['user@'],
            ['user@.com'],
            ['user@com.']
        ];
    }
    
    public static function validPasswordProvider(): array {
        return [
            ['Password123!'],
            ['SecurePass456@'],
            ['StrongPass789#'],
            ['ComplexPass012$']
        ];
    }
    
    public static function invalidPasswordProvider(): array {
        return [
            [''],
            ['123'],
            ['password'],
            ['Password'],
            ['PASSWORD123']
        ];
    }
    
    public static function edgeCaseNumbers(): array {
        return [
            [0],
            [-1],
            [1],
            [PHP_INT_MAX],
            [PHP_INT_MIN]
        ];
    }
    
    public static function booleanProvider(): array {
        return [
            [true],
            [false]
        ];
    }
}

// Test helper class
class TestHelper {
    public static function captureStdout(callable $callback): string {
        ob_start();
        $callback();
        return ob_get_clean();
    }
    
    public static function captureStderr(callable $callback): string {
        $stderr = fopen('php://stderr', 'w');
        $original = ini_set('log_errors', 0);
        
        ob_start();
        $callback();
        $output = ob_get_clean();
        
        ini_set('log_errors', $original);
        
        return $output;
    }
    
    public static function withEnv(array $env, callable $callback) {
        $originalEnv = $_ENV;
        
        try {
            $_ENV = array_merge($originalEnv, $env);
            return $callback();
        } finally {
            $_ENV = $originalEnv;
        }
    }
    
    public static function withServer(array $server, callable $callback) {
        $originalServer = $_SERVER;
        
        try {
            $_SERVER = array_merge($originalServer, $server);
            return $callback();
        } finally {
            $_SERVER = $originalServer;
        }
    }
    
    public static function withTimezone(string $timezone, callable $callback) {
        $originalTimezone = date_default_timezone_get();
        
        try {
            date_default_timezone_set($timezone);
            return $callback();
        } finally {
            date_default_timezone_set($originalTimezone);
        }
    }
    
    public static function measureTime(callable $callback): float {
        $start = microtime(true);
        $callback();
        return microtime(true) - $start;
    }
    
    public static function measureMemory(callable $callback): int {
        $before = memory_get_usage();
        $callback();
        return memory_get_usage() - $before;
    }
    
    public static function retry(int $times, callable $callback) {
        $lastException = null;
        
        for ($i = 0; $i < $times; $i++) {
            try {
                return $callback();
            } catch (Exception $e) {
                $lastException = $e;
                
                if ($i < $times - 1) {
                    usleep(100000); // Wait 100ms
                }
            }
        }
        
        throw $lastException;
    }
    
    public static function waitFor(callable $condition, int $timeout = 10, int $interval = 1): bool {
        $start = time();
        
        while (time() - $start < $timeout) {
            if ($condition()) {
                return true;
            }
            
            sleep($interval);
        }
        
        return false;
    }
    
    public static function assertExceptionThrown(callable $callback, string $expectedException = null): void {
        $exceptionThrown = false;
        $actualException = null;
        
        try {
            $callback();
        } catch (Exception $e) {
            $exceptionThrown = true;
            $actualException = $e;
        }
        
        if (!$exceptionThrown) {
            throw new AssertionError('Expected exception was not thrown');
        }
        
        if ($expectedException !== null && !($actualException instanceof $expectedException)) {
            throw new AssertionError("Expected exception of type $expectedException, but got " . get_class($actualException));
        }
    }
}

// Test suite class
class TestSuite {
    private array $tests = [];
    private array $results = [];
    
    public function addTest(string $name, callable $test): void {
        $this->tests[$name] = $test;
    }
    
    public function run(): array {
        foreach ($this->tests as $name => $test) {
            try {
                $start = microtime(true);
                $test();
                $duration = microtime(true) - $start;
                
                $this->results[$name] = [
                    'status' => 'passed',
                    'duration' => $duration,
                    'message' => null
                ];
            } catch (Exception $e) {
                $this->results[$name] = [
                    'status' => 'failed',
                    'duration' => 0,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $this->results;
    }
    
    public function getResults(): array {
        return $this->results;
    }
    
    public function getPassedCount(): int {
        return count(array_filter($this->results, fn($result) => $result['status'] === 'passed'));
    }
    
    public function getFailedCount(): int {
        return count(array_filter($this->results, fn($result) => $result['status'] === 'failed'));
    }
    
    public function getTotalDuration(): float {
        return array_sum(array_column($this->results, 'duration'));
    }
}

// Test report generator
class TestReportGenerator {
    public static function generateHtmlReport(array $results): string {
        $passedCount = count(array_filter($results, fn($result) => $result['status'] === 'passed'));
        $failedCount = count(array_filter($results, fn($result) => $result['status'] === 'failed'));
        $totalDuration = array_sum(array_column($results, 'duration'));
        
        $html = '<html><head><title>Test Report</title>';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; margin: 20px; }';
        $html .= '.summary { background: #f5f5f5; padding: 20px; margin-bottom: 20px; border-radius: 5px; }';
        $html .= '.passed { color: green; }';
        $html .= '.failed { color: red; }';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        $html .= 'th { background-color: #f2f2f2; }';
        $html .= '</style></head><body>';
        
        $html .= '<div class="summary">';
        $html .= "<h1>Test Report</h1>";
        $html .= "<p>Total Tests: " . count($results) . "</p>";
        $html .= "<p class='passed'>Passed: $passedCount</p>";
        $html .= "<p class='failed'>Failed: $failedCount</p>";
        $html .= "<p>Total Duration: " . number_format($totalDuration, 2) . "s</p>";
        $html .= '</div>';
        
        $html .= '<table>';
        $html .= '<tr><th>Test</th><th>Status</th><th>Duration</th><th>Message</th></tr>';
        
        foreach ($results as $name => $result) {
            $statusClass = $result['status'] === 'passed' ? 'passed' : 'failed';
            $html .= "<tr>";
            $html .= "<td>$name</td>";
            $html .= "<td class='$statusClass'>{$result['status']}</td>";
            $html .= "<td>" . number_format($result['duration'], 3) . "s</td>";
            $html .= "<td>" . htmlspecialchars($result['message'] ?? '') . "</td>";
            $html .= "</tr>";
        }
        
        $html .= '</table>';
        $html .= '</body></html>';
        
        return $html;
    }
    
    public static function generateJsonReport(array $results): string {
        return json_encode([
            'summary' => [
                'total' => count($results),
                'passed' => count(array_filter($results, fn($result) => $result['status'] === 'passed')),
                'failed' => count(array_filter($results, fn($result) => $result['status'] === 'failed')),
                'duration' => array_sum(array_column($results, 'duration'))
            ],
            'tests' => $results
        ], JSON_PRETTY_PRINT);
    }
    
    public static function generateJunitXmlReport(array $results): string {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<testsuite>';
        
        foreach ($results as $name => $result) {
            $xml .= '<testcase';
            $xml .= ' name="' . htmlspecialchars($name) . '"';
            $xml .= ' time="' . $result['duration'] . '"';
            $xml .= '>';
            
            if ($result['status'] === 'failed') {
                $xml .= '<failure message="' . htmlspecialchars($result['message']) . '"></failure>';
            }
            
            $xml .= '</testcase>';
        }
        
        $xml .= '</testsuite>';
        
        return $xml;
    }
}

// Usage examples of test utilities
class ExampleTest extends BaseTestCase {
    use DatabaseTestTrait, HttpTestTrait;
    
    protected function setUp(): void {
        parent::setUp();
        $this->setupDatabase();
        $this->setupHttpClient();
    }
    
    protected function tearDown(): void {
        $this->teardownDatabase();
        parent::tearDown();
    }
    
    /**
     * @dataProvider validUserDataProvider
     */
    public function testCreateUser(string $name, string $email): void {
        $userData = ['name' => $name, 'email' => $email];
        
        $response = $this->post('/users', $userData);
        
        $this->assertResponseSuccessful($response);
        $this->assertResponseJson($response);
        
        $this->assertDatabaseHas('users', $userData);
    }
    
    public function testCreateUserWithInvalidEmail(): void {
        $userData = ['name' => 'John Doe', 'email' => 'invalid-email'];
        
        $response = $this->post('/users', $userData);
        
        $this->assertResponseError($response, 400);
        $this->assertDatabaseMissing('users', $userData);
    }
    
    public function testPerformanceCriticalOperation(): void {
        $duration = TestHelper::measureTime(function() {
            // Perform operation
            usleep(100000); // 100ms
        });
        
        $this->assertLessThan(0.2, $duration, 'Operation should complete within 200ms');
    }
    
    public function testMemoryUsage(): void {
        $memoryUsed = TestHelper::measureMemory(function() {
            // Perform memory-intensive operation
            $largeArray = array_fill(0, 10000, 'test');
        });
        
        $this->assertLessThan(1024 * 1024, $memoryUsed, 'Operation should use less than 1MB memory');
    }
    
    public function testAsyncOperation(): void {
        $result = TestHelper::waitFor(function() {
            // Check if async operation completed
            return true; // Simulate completion
        }, 5);
        
        $this->assertTrue($result, 'Async operation should complete within 5 seconds');
    }
    
    public function testExceptionHandling(): void {
        TestHelper::assertExceptionThrown(function() {
            throw new InvalidArgumentException('Test exception');
        }, InvalidArgumentException::class);
    }
}
?>
```

## Summary

PHP Testing Strategies provides:

**Unit Testing:**
- PHPUnit framework usage
- Test doubles and mocks
- Data providers for parameterized tests
- Custom assertions and utilities
- Test isolation and setup/teardown

**Integration Testing:**
- Database integration testing
- API integration testing
- Mock HTTP clients
- Test data factories
- Scenario builders

**End-to-End Testing:**
- Page object pattern
- Browser automation
- Full stack testing
- Performance testing
- Accessibility testing

**Testing Best Practices:**
- Test organization and structure
- Test configuration management
- Reusable test traits
- Test data management
- Report generation

**Key Benefits:**
- Improved code quality
- Early bug detection
- Regression prevention
- Documentation through tests
- Confidence in refactoring

**Implementation Considerations:**
- Test pyramid strategy
- Test environment setup
- Continuous integration
- Test maintenance
- Performance optimization

Comprehensive testing strategies ensure robust, maintainable, and reliable PHP applications through systematic validation of functionality at all levels.
