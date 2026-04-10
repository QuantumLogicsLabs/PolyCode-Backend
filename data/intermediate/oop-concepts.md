# PHP Object-Oriented Programming (OOP)

## Classes and Objects

### Class Definition
```php
<?php
// Basic class definition
class User {
    // Properties (attributes)
    public string $name;
    public int $age;
    public string $email;
    
    // Constructor
    public function __construct(string $name, int $age, string $email) {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
    }
    
    // Methods
    public function getFullName(): string {
        return $this->name;
    }
    
    public function setAge(int $age): void {
        if ($age >= 0 && $age <= 150) {
            $this->age = $age;
        } else {
            throw new InvalidArgumentException("Invalid age");
        }
    }
    
    public function getAge(): int {
        return $this->age;
    }
    
    // Destructor
    public function __destruct() {
        echo "User {$this->name} destroyed\n";
    }
}

// Creating objects
$user1 = new User("John Doe", 30, "john@example.com");
$user2 = new User("Jane Smith", 25, "jane@example.com");

// Accessing properties
echo $user1->name; // "John Doe"
echo $user2->age; // 25

// Calling methods
echo $user1->getFullName(); // "John Doe"
$user2->setAge(26);
echo $user2->getAge(); // 26

// Class with typed properties (PHP 7.4+)
class Product {
    public string $name;
    public float $price;
    public int $quantity;
    
    public function __construct(string $name, float $price, int $quantity) {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }
    
    public function getTotalValue(): float {
        return $this->price * $this->quantity;
    }
}

$product = new Product("Laptop", 999.99, 2);
echo $product->getTotalValue(); // 1999.98
?>
```

### Properties and Methods
```php
<?php
class BankAccount {
    // Private properties (encapsulation)
    private string $accountNumber;
    private float $balance;
    private string $ownerName;
    
    // Public properties (limited access)
    public readonly string $bankName;
    
    // Static property
    private static int $totalAccounts = 0;
    
    public function __construct(string $accountNumber, string $ownerName, float $initialBalance = 0.0) {
        $this->accountNumber = $accountNumber;
        $this->ownerName = $ownerName;
        $this->balance = $initialBalance;
        $this->bankName = "First National Bank";
        
        self::$totalAccounts++;
    }
    
    // Public methods (interface)
    public function deposit(float $amount): bool {
        if ($amount <= 0) {
            return false;
        }
        
        $this->balance += $amount;
        return true;
    }
    
    public function withdraw(float $amount): bool {
        if ($amount <= 0 || $amount > $this->balance) {
            return false;
        }
        
        $this->balance -= $amount;
        return true;
    }
    
    public function getBalance(): float {
        return $this->balance;
    }
    
    public function getAccountInfo(): array {
        return [
            'accountNumber' => $this->accountNumber,
            'ownerName' => $this->ownerName,
            'balance' => $this->balance,
            'bankName' => $this->bankName
        ];
    }
    
    // Private methods (internal logic)
    private function validateAmount(float $amount): bool {
        return $amount > 0 && $amount <= $this->balance;
    }
    
    // Static methods
    public static function getTotalAccounts(): int {
        return self::$totalAccounts;
    }
    
    public static function generateAccountNumber(): string {
        return 'ACC' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    // Magic methods
    public function __toString(): string {
        return "Account {$this->accountNumber} ({$this->ownerName}): \${$this->balance}";
    }
    
    public function __debugInfo(): array {
        return [
            'accountNumber' => $this->accountNumber,
            'ownerName' => $this->ownerName,
            'balance' => $this->balance
        ];
    }
}

// Using the class
$account = new BankAccount("123456789", "John Doe", 1000.0);
$account->deposit(500.0);
$account->withdraw(200.0);

echo $account->getBalance(); // 1300.0
echo $account; // "Account 123456789 (John Doe): $1300.0"

echo BankAccount::getTotalAccounts(); // 1
echo BankAccount::generateAccountNumber(); // "ACC123456"
?>
```

### Constructor and Destructor
```php
<?php
class DatabaseConnection {
    private string $host;
    private string $database;
    private ?PDO $connection = null;
    private bool $isConnected = false;
    
    // Constructor with multiple parameters and default values
    public function __construct(
        string $host = 'localhost',
        string $database = 'test',
        string $username = 'root',
        string $password = '',
        array $options = []
    ) {
        $this->host = $host;
        $this->database = $database;
        
        $this->connect($username, $password, $options);
    }
    
    // Private method for connection logic
    private function connect(string $username, string $password, array $options): void {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database}";
            $defaultOptions = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            $this->connection = new PDO($dsn, $username, $password, array_merge($defaultOptions, $options));
            $this->isConnected = true;
            
            echo "Connected to database: {$this->database}\n";
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage() . "\n";
        }
    }
    
    // Factory method
    public static function createFromConfig(array $config): self {
        return new self(
            $config['host'] ?? 'localhost',
            $config['database'] ?? 'test',
            $config['username'] ?? 'root',
            $config['password'] ?? '',
            $config['options'] ?? []
        );
    }
    
    // Destructor - automatically called when object is destroyed
    public function __destruct() {
        if ($this->isConnected && $this->connection !== null) {
            $this->connection = null;
            $this->isConnected = false;
            echo "Database connection closed\n";
        }
    }
    
    // Other methods
    public function query(string $sql, array $params = []): array {
        if (!$this->isConnected) {
            throw new RuntimeException("Not connected to database");
        }
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function isConnected(): bool {
        return $this->isConnected;
    }
}

// Using the class
$config = [
    'host' => 'localhost',
    'database' => 'myapp',
    'username' => 'user',
    'password' => 'password',
    'options' => [PDO::ATTR_PERSISTENT => true]
];

$db = DatabaseConnection::createFromConfig($config);
$results = $db->query("SELECT * FROM users WHERE age > ?", [18]);
echo $db->isConnected(); // true

// Destructor automatically called when script ends or object is unset
unset($db);
?>
```

## Inheritance

### Basic Inheritance
```php
<?php
// Parent class (base class)
class Vehicle {
    protected string $brand;
    protected string $model;
    protected int $year;
    protected float $price;
    
    public function __construct(string $brand, string $model, int $year, float $price) {
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->price = $price;
    }
    
    // Public methods
    public function getBrand(): string {
        return $this->brand;
    }
    
    public function getModel(): string {
        return $this->model;
    }
    
    public function getYear(): int {
        return $this->year;
    }
    
    public function getPrice(): float {
        return $this->price;
    }
    
    public function setPrice(float $price): void {
        if ($price >= 0) {
            $this->price = $price;
        }
    }
    
    // Method that can be overridden
    public function getDescription(): string {
        return "{$this->year} {$this->brand} {$this->model}";
    }
    
    // Method that must be implemented by child classes
    public function getFuelType(): string {
        return "Unknown";
    }
    
    // Final method (cannot be overridden)
    final public function getAge(): int {
        return date('Y') - $this->year;
    }
}

// Child class
class Car extends Vehicle {
    private int $doors;
    private string $color;
    private bool $isConvertible;
    
    public function __construct(
        string $brand, 
        string $model, 
        int $year, 
        float $price,
        int $doors = 4,
        string $color = 'black',
        bool $isConvertible = false
    ) {
        parent::__construct($brand, $model, $year, $price);
        $this->doors = $doors;
        $this->color = $color;
        $this->isConvertible = $isConvertible;
    }
    
    // Additional methods specific to Car
    public function getDoors(): int {
        return $this->doors;
    }
    
    public function getColor(): string {
        return $this->color;
    }
    
    public function isConvertible(): bool {
        return $this->isConvertible;
    }
    
    public function setColor(string $color): void {
        $this->color = $color;
    }
    
    // Override parent method
    public function getDescription(): string {
        $base = parent::getDescription();
        $convertible = $this->isConvertible ? "Convertible" : "Hardtop";
        return "{$base} ({$this->doors}-door {$convertible}, {$this->color})";
    }
    
    // Implement abstract method
    public function getFuelType(): string {
        return "Gasoline";
    }
    
    // Method specific to Car
    public function honk(): string {
        return "Beep beep!";
    }
}

// Another child class
class Motorcycle extends Vehicle {
    private string $type;
    private bool $hasStorage;
    
    public function __construct(
        string $brand, 
        string $model, 
        int $year, 
        float $price,
        string $type = 'street',
        bool $hasStorage = false
    ) {
        parent::__construct($brand, $model, $year, $price);
        $this->type = $type;
        $this->hasStorage = $hasStorage;
    }
    
    public function getType(): string {
        return $this->type;
    }
    
    public function hasStorage(): bool {
        return $this->hasStorage;
    }
    
    public function getDescription(): string {
        $base = parent::getDescription();
        $storage = $this->hasStorage ? "with storage" : "without storage";
        return "{$base} ({$this->type} motorcycle, {$storage})";
    }
    
    public function getFuelType(): string {
        return "Gasoline";
    }
    
    public function wheelie(): string {
        return "Doing a wheelie!";
    }
}

// Using the classes
$car = new Car("Toyota", "Camry", 2022, 25000.0, 4, "blue", false);
$motorcycle = new Motorcycle("Harley-Davidson", "Street Glide", 2021, 20000.0, "cruiser", true);

echo $car->getDescription(); // "2022 Toyota Camry (4-door Hardtop, blue)"
echo $motorcycle->getDescription(); // "2021 Harley-Davidson Street Glide (cruiser motorcycle, with storage)"
echo $car->getFuelType(); // "Gasoline"
echo $motorcycle->getFuelType(); // "Gasoline"
?>
```

### Abstract Classes and Methods
```php
<?php
// Abstract class - cannot be instantiated directly
abstract class Animal {
    protected string $name;
    protected int $age;
    protected string $species;
    
    public function __construct(string $name, int $age, string $species) {
        $this->name = $name;
        $this->age = $age;
        $this->species = $species;
    }
    
    // Concrete method
    public function getName(): string {
        return $this->name;
    }
    
    public function getAge(): int {
        return $this->age;
    }
    
    public function getSpecies(): string {
        return $this->species;
    }
    
    // Abstract method - must be implemented by child classes
    abstract public function makeSound(): string;
    
    // Another abstract method
    abstract public function move(): string;
    
    // Concrete method that uses abstract methods
    public function performAction(): string {
        return "{$this->name} the {$this->species} {$this->move()} and {$this->makeSound()}";
    }
    
    // Final method - cannot be overridden
    final public function sleep(): string {
        return "{$this->name} is sleeping";
    }
}

// Concrete class implementing abstract methods
class Dog extends Animal {
    private string $breed;
    private bool $isTrained;
    
    public function __construct(string $name, int $age, string $breed, bool $isTrained = true) {
        parent::__construct($name, $age, "Dog");
        $this->breed = $breed;
        $this->isTrained = $isTrained;
    }
    
    public function getBreed(): string {
        return $this->breed;
    }
    
    public function isTrained(): bool {
        return $this->isTrained;
    }
    
    // Implement abstract method
    public function makeSound(): string {
        return "barks";
    }
    
    // Implement abstract method
    public function move(): string {
        return "runs";
    }
    
    // Additional method
    public function fetch(): string {
        return $this->isTrained ? "fetches the ball" : "doesn't know how to fetch";
    }
    
    // Override parent method
    public function performAction(): string {
        $base = parent::performAction();
        return $base . " and then " . $this->fetch();
    }
}

class Cat extends Animal {
    private bool $isIndoor;
    private string $favoriteFood;
    
    public function __construct(string $name, int $age, bool $isIndoor = true, string $favoriteFood = "fish") {
        parent::__construct($name, $age, "Cat");
        $this->isIndoor = $isIndoor;
        $this->favoriteFood = $favoriteFood;
    }
    
    public function isIndoor(): bool {
        return $this->isIndoor;
    }
    
    public function getFavoriteFood(): string {
        return $this->favoriteFood;
    }
    
    // Implement abstract method
    public function makeSound(): string {
        return "meows";
    }
    
    // Implement abstract method
    public function move(): string {
        return $this->isIndoor ? "prowls around the house" : "stalks prey";
    }
    
    // Additional method
    public function purr(): string {
        return "purrs contentedly";
    }
}

class Bird extends Animal {
    private bool $canFly;
    private float $wingspan;
    
    public function __construct(string $name, int $age, string $species, bool $canFly = true, float $wingspan = 0.0) {
        parent::__construct($name, $age, $species);
        $this->canFly = $canFly;
        $this->wingspan = $wingspan;
    }
    
    public function canFly(): bool {
        return $this->canFly;
    }
    
    public function getWingspan(): float {
        return $this->wingspan;
    }
    
    // Implement abstract method
    public function makeSound(): string {
        return "chirps";
    }
    
    // Implement abstract method
    public function move(): string {
        return $this->canFly ? "flies through the air" : "hops on the ground";
    }
    
    // Additional method
    public function sing(): string {
        return "sings a beautiful song";
    }
}

// Using the classes
$dog = new Dog("Buddy", 3, "Golden Retriever");
$cat = new Cat("Whiskers", 2, true, "tuna");
$bird = new Bird("Tweety", 1, "Canary", true, 0.5);

echo $dog->performAction(); // "Buddy the Dog runs and barks and then fetches the ball"
echo $cat->performAction(); // "Whiskers the Cat prowls around the house and meows"
echo $bird->performAction(); // "Tweety the Canary flies through the air and chirps"

// Type checking
if ($dog instanceof Animal) {
    echo "Dog is an Animal\n";
}

if ($dog instanceof Dog) {
    echo "Dog is a Dog\n";
}
?>
```

### Method Overriding and Parent References
```php
<?php
class Employee {
    protected string $name;
    protected float $salary;
    protected string $department;
    
    public function __construct(string $name, float $salary, string $department) {
        $this->name = $name;
        $this->salary = $salary;
        $this->department = $department;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getSalary(): float {
        return $this->salary;
    }
    
    public function getDepartment(): string {
        return $this->department;
    }
    
    public function setSalary(float $salary): void {
        if ($salary >= 0) {
            $this->salary = $salary;
        }
    }
    
    // Method that can be overridden
    public function getBonus(): float {
        return $this->salary * 0.10; // 10% bonus
    }
    
    // Method that uses getBonus()
    public function getTotalCompensation(): float {
        return $this->salary + $this->getBonus();
    }
    
    // Method that can be overridden
    public function getDescription(): string {
        return "{$this->name} from {$this->department}";
    }
}

class Manager extends Employee {
    private array $teamMembers;
    private float $bonusMultiplier;
    
    public function __construct(string $name, float $salary, string $department, float $bonusMultiplier = 1.5) {
        parent::__construct($name, $salary, $department);
        $this->teamMembers = [];
        $this->bonusMultiplier = $bonusMultiplier;
    }
    
    public function addTeamMember(Employee $employee): void {
        $this->teamMembers[] = $employee;
    }
    
    public function getTeamMembers(): array {
        return $this->teamMembers;
    }
    
    public function getTeamSize(): int {
        return count($this->teamMembers);
    }
    
    // Override parent method
    public function getBonus(): float {
        return $this->salary * 0.20 * $this->bonusMultiplier; // 20% bonus with multiplier
    }
    
    // Override parent method and call parent
    public function getDescription(): string {
        $parentDescription = parent::getDescription();
        return "Manager {$parentDescription} with {$this->getTeamSize()} team members";
    }
    
    // New method specific to Manager
    public function conductMeeting(): string {
        return "Conducting team meeting with {$this->getTeamSize()} members";
    }
}

class Developer extends Employee {
    private array $skills;
    private string $programmingLanguage;
    
    public function __construct(string $name, float $salary, string $department, string $programmingLanguage = 'PHP') {
        parent::__construct($name, $salary, $department);
        $this->programmingLanguage = $programmingLanguage;
        $this->skills = [];
    }
    
    public function addSkill(string $skill): void {
        if (!in_array($skill, $this->skills)) {
            $this->skills[] = $skill;
        }
    }
    
    public function getSkills(): array {
        return $this->skills;
    }
    
    public function getProgrammingLanguage(): string {
        return $this->programmingLanguage;
    }
    
    // Override parent method
    public function getBonus(): float {
        $baseBonus = parent::getBonus();
        $skillBonus = count($this->skills) * 1000; // $1000 per skill
        return $baseBonus + $skillBonus;
    }
    
    // Override parent method
    public function getDescription(): string {
        $parentDescription = parent::getDescription();
        return "Developer {$parentDescription} specializing in {$this->programmingLanguage}";
    }
    
    // New method specific to Developer
    public function writeCode(): string {
        return "Writing code in {$this->programmingLanguage}";
    }
}

// Using the classes with inheritance
$manager = new Manager("Alice Johnson", 80000, "Engineering", 1.8);
$developer1 = new Developer("Bob Smith", 60000, "Engineering", "JavaScript");
$developer2 = new Developer("Carol Davis", 65000, "Engineering", "Python");

$manager->addTeamMember($developer1);
$manager->addTeamMember($developer2);

$developer1->addSkill("React");
$developer1->addSkill("Node.js");
$developer2->addSkill("Django");
$developer2->addSkill("Flask");

echo $manager->getDescription(); // "Manager Alice Johnson from Engineering with 2 team members"
echo $developer1->getDescription(); // "Developer Bob Smith from Engineering specializing in JavaScript"

echo $manager->getTotalCompensation(); // Uses overridden getBonus()
echo $developer1->getTotalCompensation(); // Uses overridden getBonus()

// Polymorphism example
$employees = [$manager, $developer1, $developer2];
foreach ($employees as $employee) {
    echo $employee->getDescription();
    echo $employee->getTotalCompensation();
    
    // Type-specific behavior
    if ($employee instanceof Manager) {
        echo $employee->conductMeeting();
    } elseif ($employee instanceof Developer) {
        echo $employee->writeCode();
    }
}
?>
```

## Interfaces and Traits

### Interfaces
```php
<?php
// Interface defines a contract
interface LoggerInterface {
    public function log(string $message): void;
    public function logError(string $error): void;
    public function logInfo(string $info): void;
}

interface CacheableInterface {
    public function getCacheKey(): string;
    public function getCacheTTL(): int;
}

interface SerializableInterface {
    public function toArray(): array;
    public function fromArray(array $data): self;
}

// Class can implement multiple interfaces
class FileLogger implements LoggerInterface {
    private string $logFile;
    
    public function __construct(string $logFile) {
        $this->logFile = $logFile;
    }
    
    public function log(string $message): void {
        $this->writeLog("INFO", $message);
    }
    
    public function logError(string $error): void {
        $this->writeLog("ERROR", $error);
    }
    
    public function logInfo(string $info): void {
        $this->writeLog("INFO", $info);
    }
    
    private function writeLog(string $level, string $message): void {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}\n";
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
}

class DatabaseLogger implements LoggerInterface {
    private PDO $connection;
    
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }
    
    public function log(string $message): void {
        $this->insertLog("INFO", $message);
    }
    
    public function logError(string $error): void {
        $this->insertLog("ERROR", $error);
    }
    
    public function logInfo(string $info): void {
        $this->insertLog("INFO", $info);
    }
    
    private function insertLog(string $level, string $message): void {
        $stmt = $this->connection->prepare(
            "INSERT INTO logs (level, message, created_at) VALUES (?, ?, ?)"
        );
        $stmt->execute([$level, $message, date('Y-m-d H:i:s')]);
    }
}

// Class implementing multiple interfaces
class User implements CacheableInterface, SerializableInterface {
    private int $id;
    private string $name;
    private string $email;
    private DateTime $createdAt;
    
    public function __construct(int $id, string $name, string $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = new DateTime();
    }
    
    // CacheableInterface implementation
    public function getCacheKey(): string {
        return "user_{$this->id}";
    }
    
    public function getCacheTTL(): int {
        return 3600; // 1 hour
    }
    
    // SerializableInterface implementation
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
    
    public function fromArray(array $data): self {
        $user = new self($data['id'], $data['name'], $data['email']);
        $user->createdAt = new DateTime($data['created_at']);
        return $user;
    }
    
    // Regular methods
    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
}

// Using interfaces for dependency injection
class UserService {
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function createUser(string $name, string $email): User {
        $this->logger->logInfo("Creating user: {$name}");
        
        try {
            $user = new User(random_int(1, 1000), $name, $email);
            $this->logger->log("User created successfully: {$user->getId()}");
            return $user;
        } catch (Exception $e) {
            $this->logger->logError("Failed to create user: " . $e->getMessage());
            throw $e;
        }
    }
}

// Using the service with different loggers
$fileLogger = new FileLogger('app.log');
$dbLogger = new DatabaseLogger($pdo);

$fileUserService = new UserService($fileLogger);
$dbUserService = new UserService($dbLogger);

$user1 = $fileUserService->createUser("John Doe", "john@example.com");
$user2 = $dbUserService->createUser("Jane Smith", "jane@example.com");
?>
```

### Traits
```php
<?php
// Trait for timestamp functionality
trait Timestampable {
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    public function __construct() {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }
    
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }
    
    public function updateTimestamp(): void {
        $this->updatedAt = new DateTime();
    }
    
    public function getAge(): string {
        $now = new DateTime();
        $interval = $now->diff($this->createdAt);
        return $interval->format('%y years, %m months, %d days');
    }
}

// Trait for soft delete functionality
trait SoftDeletable {
    private ?DateTime $deletedAt = null;
    
    public function delete(): void {
        $this->deletedAt = new DateTime();
    }
    
    public function restore(): void {
        $this->deletedAt = null;
    }
    
    public function isDeleted(): bool {
        return $this->deletedAt !== null;
    }
    
    public function getDeletedAt(): ?DateTime {
        return $this->deletedAt;
    }
}

// Trait for validation
trait Validatable {
    private array $errors = [];
    
    public function validate(): bool {
        $this->errors = [];
        $this->performValidation();
        return empty($this->errors);
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
    
    protected function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }
    
    abstract protected function performValidation(): void;
}

// Trait for logging
trait Loggable {
    private LoggerInterface $logger;
    
    public function setLogger(LoggerInterface $logger): void {
        $this->logger = $logger;
    }
    
    protected function log(string $message): void {
        if (isset($this->logger)) {
            $this->logger->log(get_class($this) . ": " . $message);
        }
    }
    
    protected function logError(string $error): void {
        if (isset($this->logger)) {
            $this->logger->logError(get_class($this) . ": " . $error);
        }
    }
}

// Class using multiple traits
class Product {
    use Timestampable, SoftDeletable;
    
    private int $id;
    private string $name;
    private float $price;
    private int $quantity;
    
    public function __construct(string $name, float $price, int $quantity) {
        $this->id = random_int(1, 1000);
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        
        // Call trait constructor if needed
        $this->Timestampable();
    }
    
    public function update(string $name, float $price, int $quantity): void {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->updateTimestamp(); // Method from Timestampable trait
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getPrice(): float {
        return $this->price;
    }
    
    public function getQuantity(): int {
        return $this->quantity;
    }
    
    public function getTotalValue(): float {
        return $this->price * $this->quantity;
    }
}

// Class using Validatable trait
class User {
    use Validatable, Loggable;
    
    private string $name;
    private string $email;
    private int $age;
    
    public function __construct(string $name, string $email, int $age) {
        $this->name = $name;
        $this->email = $email;
        $this->age = $age;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getAge(): int {
        return $this->age;
    }
    
    public function setName(string $name): void {
        $this->name = $name;
    }
    
    public function setEmail(string $email): void {
        $this->email = $email;
    }
    
    public function setAge(int $age): void {
        $this->age = $age;
    }
    
    // Implementation of abstract method from Validatable trait
    protected function performValidation(): void {
        if (empty($this->name)) {
            $this->addError('name', 'Name is required');
        } elseif (strlen($this->name) < 2) {
            $this->addError('name', 'Name must be at least 2 characters');
        }
        
        if (empty($this->email)) {
            $this->addError('email', 'Email is required');
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'Invalid email format');
        }
        
        if ($this->age < 0 || $this->age > 150) {
            $this->addError('age', 'Age must be between 0 and 150');
        }
    }
    
    public function save(): bool {
        if ($this->validate()) {
            $this->log("User saved: {$this->name}");
            return true;
        } else {
            $this->logError("User validation failed");
            return false;
        }
    }
}

// Using the classes
$product = new Product("Laptop", 999.99, 10);
$product->update("Gaming Laptop", 1299.99, 5);
echo $product->getAge(); // From Timestampable trait

$product->delete();
echo $product->isDeleted(); // From SoftDeletable trait

$user = new User("John Doe", "john@example.com", 30);
$user->setLogger(new FileLogger('app.log'));
$user->save(); // Uses Validatable and Loggable traits
?>
```

## Encapsulation and Visibility

### Access Modifiers
```php
<?php
class BankAccount {
    // Public properties - accessible from anywhere
    public readonly string $accountNumber;
    public string $bankName;
    
    // Protected properties - accessible within class and child classes
    protected float $balance;
    protected string $accountType;
    
    // Private properties - accessible only within this class
    private string $ownerName;
    private array $transactions = [];
    private bool $isActive = true;
    
    // Static properties
    private static int $totalAccounts = 0;
    private static array $allAccounts = [];
    
    public function __construct(string $accountNumber, string $ownerName, float $initialBalance = 0.0) {
        $this->accountNumber = $accountNumber;
        $this->ownerName = $ownerName;
        $this->balance = $initialBalance;
        $this->accountType = "Checking";
        $this->bankName = "First National Bank";
        
        self::$totalAccounts++;
        self::$allAccounts[] = $this;
    }
    
    // Public methods - accessible from anywhere
    public function deposit(float $amount): bool {
        if (!$this->isActive || $amount <= 0) {
            return false;
        }
        
        $this->balance += $amount;
        $this->addTransaction("DEPOSIT", $amount);
        return true;
    }
    
    public function withdraw(float $amount): bool {
        if (!$this->isActive || $amount <= 0 || $amount > $this->balance) {
            return false;
        }
        
        $this->balance -= $amount;
        $this->addTransaction("WITHDRAW", $amount);
        return true;
    }
    
    public function getBalance(): float {
        return $this->balance;
    }
    
    public function getAccountInfo(): array {
        return [
            'accountNumber' => $this->accountNumber,
            'ownerName' => $this->ownerName,
            'balance' => $this->balance,
            'accountType' => $this->accountType,
            'bankName' => $this->bankName,
            'isActive' => $this->isActive
        ];
    }
    
    // Protected methods - accessible within class and child classes
    protected function addTransaction(string $type, float $amount): void {
        $this->transactions[] = [
            'type' => $type,
            'amount' => $amount,
            'balance' => $this->balance,
            'timestamp' => new DateTime()
        ];
    }
    
    protected function canPerformTransaction(float $amount): bool {
        return $this->isActive && $amount > 0 && $amount <= $this->balance;
    }
    
    // Private methods - accessible only within this class
    private function validateTransaction(float $amount): bool {
        return $amount > 0 && $amount <= $this->balance;
    }
    
    private function logTransaction(string $type, float $amount): void {
        $log = sprintf(
            "[%s] %s $%.2f - Balance: $%.2f\n",
            date('Y-m-d H:i:s'),
            $type,
            $amount,
            $this->balance
        );
        file_put_contents("transactions.log", $log, FILE_APPEND);
    }
    
    // Static public methods
    public static function getTotalAccounts(): int {
        return self::$totalAccounts;
    }
    
    public static function getAllAccounts(): array {
        return self::$allAccounts;
    }
    
    // Static protected methods
    protected static function generateAccountNumber(): string {
        return 'ACC' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    // Static private methods
    private static function validateAccountNumber(string $accountNumber): bool {
        return preg_match('/^ACC\d{6}$/', $accountNumber) === 1;
    }
    
    // Magic methods
    public function __get(string $name) {
        if ($name === 'ownerName') {
            return $this->ownerName;
        }
        
        throw new InvalidArgumentException("Property '$name' is not accessible");
    }
    
    public function __set(string $name, $value) {
        if ($name === 'ownerName') {
            $this->ownerName = $value;
            return;
        }
        
        throw new InvalidArgumentException("Property '$name' is not writable");
    }
    
    public function __toString(): string {
        return "Account {$this->accountNumber} ({$this->ownerName}): \${$this->balance}";
    }
}

// Child class demonstrating protected access
class SavingsAccount extends BankAccount {
    private float $interestRate;
    private DateTime $lastInterestCalculation;
    
    public function __construct(string $accountNumber, string $ownerName, float $initialBalance = 0.0, float $interestRate = 0.01) {
        parent::__construct($accountNumber, $ownerName, $initialBalance);
        $this->interestRate = $interestRate;
        $this->accountType = "Savings";
        $this->lastInterestCalculation = new DateTime();
    }
    
    public function getInterestRate(): float {
        return $this->interestRate;
    }
    
    public function calculateInterest(): float {
        if ($this->balance <= 0) {
            return 0.0;
        }
        
        $days = (new DateTime())->diff($this->lastInterestCalculation)->days;
        $interest = $this->balance * ($this->interestRate / 365) * $days;
        
        $this->balance += $interest;
        $this->addTransaction("INTEREST", $interest);
        $this->lastInterestCalculation = new DateTime();
        
        return $interest;
    }
    
    // Can access protected properties and methods from parent
    public function getDetailedInfo(): array {
        return [
            'accountInfo' => $this->getAccountInfo(),
            'interestRate' => $this->interestRate,
            'lastInterestCalculation' => $this->lastInterestCalculation->format('Y-m-d H:i:s'),
            'transactionCount' => count($this->transactions) // Accessing protected property
        ];
    }
    
    // Override parent method but still use parent functionality
    public function withdraw(float $amount): bool {
        // Add additional validation
        if ($amount > $this->balance * 0.9) {
            return false; // Can't withdraw more than 90% of balance
        }
        
        // Use parent's protected method
        if (!$this->canPerformTransaction($amount)) {
            return false;
        }
        
        // Use parent's protected method
        $this->addTransaction("WITHDRAW", $amount);
        $this->balance -= $amount;
        
        return true;
    }
}

// Using the classes
$account = new BankAccount("ACC123456", "John Doe", 1000.0);
$account->deposit(500.0);
$account->withdraw(200.0);

echo $account->getBalance(); // 1300.0
echo $account->ownerName; // "John Doe" (via __get)

$savings = new SavingsAccount("ACC789012", "Jane Smith", 2000.0, 0.02);
$savings->calculateInterest();
echo $savings->getDetailedInfo(); // Can access protected properties
?>
```

### Getters and Setters
```php
<?php
class Person {
    // Private properties with getters and setters
    private string $firstName;
    private string $lastName;
    private int $age;
    private string $email;
    private ?DateTime $birthDate = null;
    
    // Constructor with validation
    public function __construct(string $firstName, string $lastName, int $age, string $email) {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setAge($age);
        $this->setEmail($email);
    }
    
    // Getters and setters for firstName
    public function getFirstName(): string {
        return $this->firstName;
    }
    
    public function setFirstName(string $firstName): void {
        if (empty(trim($firstName))) {
            throw new InvalidArgumentException("First name cannot be empty");
        }
        
        if (strlen($firstName) > 50) {
            throw new InvalidArgumentException("First name too long");
        }
        
        $this->firstName = trim($firstName);
    }
    
    // Getters and setters for lastName
    public function getLastName(): string {
        return $this->lastName;
    }
    
    public function setLastName(string $lastName): void {
        if (empty(trim($lastName))) {
            throw new InvalidArgumentException("Last name cannot be empty");
        }
        
        if (strlen($lastName) > 50) {
            throw new InvalidArgumentException("Last name too long");
        }
        
        $this->lastName = trim($lastName);
    }
    
    // Getters and setters for age
    public function getAge(): int {
        return $this->age;
    }
    
    public function setAge(int $age): void {
        if ($age < 0 || $age > 150) {
            throw new InvalidArgumentException("Age must be between 0 and 150");
        }
        
        $this->age = $age;
    }
    
    // Getters and setters for email
    public function getEmail(): string {
        return $this->email;
    }
    
    public function setEmail(string $email): void {
        if (empty(trim($email))) {
            throw new InvalidArgumentException("Email cannot be empty");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }
        
        $this->email = strtolower(trim($email));
    }
    
    // Getter and setter for birthDate
    public function getBirthDate(): ?DateTime {
        return $this->birthDate;
    }
    
    public function setBirthDate(?DateTime $birthDate): void {
        $this->birthDate = $birthDate;
        
        // Update age if birth date is set
        if ($birthDate !== null) {
            $this->age = $birthDate->diff(new DateTime())->y;
        }
    }
    
    // Computed getter (no corresponding setter)
    public function getFullName(): string {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    // Computed getter with validation
    public function getDisplayName(): string {
        return ucfirst(strtolower($this->firstName)) . ' ' . ucfirst(strtolower($this->lastName));
    }
    
    // Boolean getter
    public function isAdult(): bool {
        return $this->age >= 18;
    }
    
    // Boolean setter
    public function setAgeFromBirthDate(DateTime $birthDate): void {
        $this->setBirthDate($birthDate);
    }
    
    // Getter with default value
    public function getMiddleName(): string {
        return $this->middleName ?? '';
    }
    
    // Setter with validation and transformation
    public function setPhone(?string $phone): void {
        if ($phone !== null) {
            // Remove all non-digit characters
            $phone = preg_replace('/\D/', '', $phone);
            
            // Validate phone number format
            if (strlen($phone) !== 10) {
                throw new InvalidArgumentException("Phone number must be 10 digits");
            }
            
            // Format phone number
            $this->phone = substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        } else {
            $this->phone = null;
        }
    }
    
    public function getPhone(): ?string {
        return $this->phone ?? null;
    }
    
    // Bulk setter
    public function updateProfile(array $data): void {
        if (isset($data['firstName'])) {
            $this->setFirstName($data['firstName']);
        }
        
        if (isset($data['lastName'])) {
            $this->setLastName($data['lastName']);
        }
        
        if (isset($data['email'])) {
            $this->setEmail($data['email']);
        }
        
        if (isset($data['age'])) {
            $this->setAge($data['age']);
        }
        
        if (isset($data['birthDate'])) {
            $this->setBirthDate($data['birthDate']);
        }
    }
    
    // Getter that returns array
    public function toArray(): array {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'fullName' => $this->getFullName(),
            'age' => $this->age,
            'email' => $this->email,
            'birthDate' => $this->birthDate?->format('Y-m-d'),
            'isAdult' => $this->isAdult(),
            'phone' => $this->phone
        ];
    }
    
    // Magic methods for property access
    public function __get(string $name) {
        $getter = 'get' . ucfirst($name);
        
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        
        throw new InvalidArgumentException("Property '$name' does not exist or is not accessible");
    }
    
    public function __set(string $name, $value) {
        $setter = 'set' . ucfirst($name);
        
        if (method_exists($this, $setter)) {
            $this->$setter($value);
            return;
        }
        
        throw new InvalidArgumentException("Property '$name' does not exist or is not writable");
    }
    
    public function __isset(string $name): bool {
        $getter = 'get' . ucfirst($name);
        return method_exists($this, $getter);
    }
}

// Using the class with getters and setters
$person = new Person("John", "Doe", 30, "john@example.com");

// Using getter methods
echo $person->getFirstName(); // "John"
echo $person->getFullName(); // "John Doe"
echo $person->isAdult(); // true

// Using setter methods
$person->setAge(31);
$person->setEmail("john.doe@example.com");

// Using magic methods
echo $person->firstName; // Calls getFirstName()
$person->age = 32; // Calls setAge()

// Bulk update
$person->updateProfile([
    'firstName' => 'Jane',
    'lastName' => 'Smith',
    'email' => 'jane.smith@example.com'
]);

// Get all data as array
$personData = $person->toArray();
?>
```

## Static Members and Constants

### Static Properties and Methods
```php
<?php
class MathUtils {
    // Static constants
    const PI = 3.141592653589793;
    const E = 2.718281828459045;
    const GOLDEN_RATIO = 1.618033988749895;
    
    // Static properties
    private static int $calculationCount = 0;
    private static array $calculationHistory = [];
    
    // Static method for basic operations
    public static function add(float $a, float $b): float {
        self::$calculationCount++;
        self::addToHistory("add", [$a, $b], $a + $b);
        return $a + $b;
    }
    
    public static function subtract(float $a, float $b): float {
        self::$calculationCount++;
        self::addToHistory("subtract", [$a, $b], $a - $b);
        return $a - $b;
    }
    
    public static function multiply(float $a, float $b): float {
        self::$calculationCount++;
        self::addToHistory("multiply", [$a, $b], $a * $b);
        return $a * $b;
    }
    
    public static function divide(float $a, float $b): float {
        if ($b == 0) {
            throw new DivisionByZeroError("Cannot divide by zero");
        }
        
        self::$calculationCount++;
        self::addToHistory("divide", [$a, $b], $a / $b);
        return $a / $b;
    }
    
    // Static method for advanced operations
    public static function power(float $base, float $exponent): float {
        self::$calculationCount++;
        self::addToHistory("power", [$base, $exponent], pow($base, $exponent));
        return pow($base, $exponent);
    }
    
    public static function factorial(int $n): int {
        if ($n < 0) {
            throw new InvalidArgumentException("Factorial is not defined for negative numbers");
        }
        
        if ($n === 0 || $n === 1) {
            return 1;
        }
        
        $result = 1;
        for ($i = 2; $i <= $n; $i++) {
            $result *= $i;
        }
        
        self::$calculationCount++;
        self::addToHistory("factorial", [$n], $result);
        return $result;
    }
    
    public static function fibonacci(int $n): int {
        if ($n < 0) {
            throw new InvalidArgumentException("Fibonacci is not defined for negative numbers");
        }
        
        if ($n === 0) {
            return 0;
        }
        
        if ($n === 1) {
            return 1;
        }
        
        $a = 0;
        $b = 1;
        
        for ($i = 2; $i <= $n; $i++) {
            $temp = $a + $b;
            $a = $b;
            $b = $temp;
        }
        
        self::$calculationCount++;
        self::addToHistory("fibonacci", [$n], $b);
        return $b;
    }
    
    // Static utility methods
    public static function getCalculationCount(): int {
        return self::$calculationCount;
    }
    
    public static function getCalculationHistory(): array {
        return self::$calculationHistory;
    }
    
    public static function resetStatistics(): void {
        self::$calculationCount = 0;
        self::$calculationHistory = [];
    }
    
    private static function addToHistory(string $operation, array $inputs, $result): void {
        self::$calculationHistory[] = [
            'operation' => $operation,
            'inputs' => $inputs,
            'result' => $result,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    // Static factory methods
    public static function createCalculator(): self {
        return new self();
    }
    
    public static function createScientificCalculator(): self {
        $instance = new self();
        // Additional initialization for scientific calculator
        return $instance;
    }
}

// Static class for configuration
class AppConfig {
    // Static constants for configuration
    const APP_NAME = "MyApplication";
    const APP_VERSION = "1.0.0";
    const DEBUG_MODE = true;
    const MAX_LOGIN_ATTEMPTS = 3;
    
    // Static properties
    private static array $config = [];
    private static bool $initialized = false;
    
    // Static method to initialize configuration
    public static function initialize(array $config = []): void {
        if (self::$initialized) {
            return;
        }
        
        self::$config = array_merge([
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
                'name' => 'myapp',
                'username' => 'root',
                'password' => ''
            ],
            'cache' => [
                'driver' => 'redis',
                'host' => 'localhost',
                'port' => 6379,
                'ttl' => 3600
            ],
            'logging' => [
                'level' => 'INFO',
                'file' => 'app.log',
                'max_files' => 10
            ]
        ], $config);
        
        self::$initialized = true;
    }
    
    // Static methods to get configuration values
    public static function get(string $key, $default = null) {
        self::initialize();
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public static function getDatabaseConfig(): array {
        return self::get('database', []);
    }
    
    public static function getCacheConfig(): array {
        return self::get('cache', []);
    }
    
    public static function getLoggingConfig(): array {
        return self::get('logging', []);
    }
    
    // Static method to set configuration
    public static function set(string $key, $value): void {
        self::initialize();
        
        $keys = explode('.', $key);
        $config = &self::$config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }
    
    // Static method to check if debug mode is enabled
    public static function isDebugEnabled(): bool {
        return self::DEBUG_MODE;
    }
    
    // Static method to get application information
    public static function getAppInfo(): array {
        return [
            'name' => self::APP_NAME,
            'version' => self::APP_VERSION,
            'debug' => self::DEBUG_MODE,
            'max_login_attempts' => self::MAX_LOGIN_ATTEMPTS
        ];
    }
}

// Static class for utilities
class StringUtils {
    // Static constants
    const VOWELS = ['a', 'e', 'i', 'o', 'u'];
    const CONSONANTS = ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'];
    
    // Static method for string manipulation
    public static function reverse(string $string): string {
        return strrev($string);
    }
    
    public static function capitalize(string $string): string {
        return ucfirst(strtolower($string));
    }
    
    public static function titleCase(string $string): string {
        return ucwords(strtolower($string));
    }
    
    public static function slugify(string $string): string {
        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower($string);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    public static function countVowels(string $string): int {
        $vowels = str_split(strtolower($string));
        return count(array_intersect($vowels, self::VOWELS));
    }
    
    public static function countConsonants(string $string): int {
        $consonants = str_split(strtolower($string));
        return count(array_intersect($consonants, self::CONSONANTS));
    }
    
    public static function isPalindrome(string $string): bool {
        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($string));
        return $cleaned === strrev($cleaned);
    }
    
    public static function truncate(string $string, int $length, string $suffix = '...'): string {
        if (strlen($string) <= $length) {
            return $string;
        }
        
        return substr($string, 0, $length - strlen($suffix)) . $suffix;
    }
    
    public static function generateRandom(int $length = 10): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
}

// Using static classes and methods
echo MathUtils::PI; // 3.141592653589793
echo MathUtils::add(5, 3); // 8
echo MathUtils::factorial(5); // 120
echo MathUtils::getCalculationCount(); // 3

AppConfig::initialize([
    'database' => [
        'host' => 'production.db.server.com',
        'password' => 'secret123'
    ]
]);

echo AppConfig::get('database.host'); // "production.db.server.com"
echo AppConfig::getDatabaseConfig(); // Database configuration array
echo AppConfig::isDebugEnabled(); // true

echo StringUtils::slugify("Hello World!"); // "hello-world"
echo StringUtils::isPalindrome("Racecar"); // true
echo StringUtils::generateRandom(8); // Random 8-character string
?>
```

### Class Constants
```php
<?php
class HTTPStatus {
    // Informational responses
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;
    const PROCESSING = 102;
    const EARLY_HINTS = 103;
    
    // Successful responses
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NON_AUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    
    // Redirection messages
    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    const TEMPORARY_REDIRECT = 307;
    const PERMANENT_REDIRECT = 308;
    
    // Client error responses
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const PAYLOAD_TOO_LARGE = 413;
    const URI_TOO_LONG = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const RANGE_NOT_SATISFIABLE = 416;
    const EXPECTATION_FAILED = 417;
    const MISDIRECTED_REQUEST = 421;
    const UNPROCESSABLE_ENTITY = 422;
    const LOCKED = 423;
    const FAILED_DEPENDENCY = 424;
    const TOO_EARLY = 425;
    const UPGRADE_REQUIRED = 426;
    const PRECONDITION_REQUIRED = 428;
    const TOO_MANY_REQUESTS = 429;
    const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    
    // Server error responses
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const VARIANT_ALSO_NEGOTIATES = 506;
    const INSUFFICIENT_STORAGE = 507;
    const LOOP_DETECTED = 508;
    const NOT_EXTENDED = 510;
    const NETWORK_AUTHENTICATION_REQUIRED = 511;
    
    // Static method to get status message
    public static function getMessage(int $code): string {
        $messages = [
            self::CONTINUE => 'Continue',
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::NOT_FOUND => 'Not Found',
            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::FORBIDDEN => 'Forbidden'
        ];
        
        return $messages[$code] ?? 'Unknown Status';
    }
    
    // Static method to check if status is informational
    public static function isInformational(int $code): bool {
        return $code >= 100 && $code < 200;
    }
    
    // Static method to check if status is successful
    public static function isSuccessful(int $code): bool {
        return $code >= 200 && $code < 300;
    }
    
    // Static method to check if status is redirection
    public static function isRedirection(int $code): bool {
        return $code >= 300 && $code < 400;
    }
    
    // Static method to check if status is client error
    public static function isClientError(int $code): bool {
        return $code >= 400 && $code < 500;
    }
    
    // Static method to check if status is server error
    public static function isServerError(int $code): bool {
        return $code >= 500 && $code < 600;
    }
    
    // Static method to get all constants as array
    public static function getAllConstants(): array {
        return [
            'informational' => [
                'CONTINUE' => self::CONTINUE,
                'SWITCHING_PROTOCOLS' => self::SWITCHING_PROTOCOLS,
                'PROCESSING' => self::PROCESSING,
                'EARLY_HINTS' => self::EARLY_HINTS
            ],
            'successful' => [
                'OK' => self::OK,
                'CREATED' => self::CREATED,
                'ACCEPTED' => self::ACCEPTED,
                'NO_CONTENT' => self::NO_CONTENT
            ],
            'redirection' => [
                'MOVED_PERMANENTLY' => self::MOVED_PERMANENTLY,
                'FOUND' => self::FOUND,
                'SEE_OTHER' => self::SEE_OTHER,
                'TEMPORARY_REDIRECT' => self::TEMPORARY_REDIRECT
            ],
            'client_error' => [
                'BAD_REQUEST' => self::BAD_REQUEST,
                'UNAUTHORIZED' => self::UNAUTHORIZED,
                'FORBIDDEN' => self::FORBIDDEN,
                'NOT_FOUND' => self::NOT_FOUND
            ],
            'server_error' => [
                'INTERNAL_SERVER_ERROR' => self::INTERNAL_SERVER_ERROR,
                'NOT_IMPLEMENTED' => self::NOT_IMPLEMENTED,
                'BAD_GATEWAY' => self::BAD_GATEWAY,
                'SERVICE_UNAVAILABLE' => self::SERVICE_UNAVAILABLE
            ]
        ];
    }
}

// Class with dynamic constants (using class constants with calculations)
class MathConstants {
    public const PI = 3.141592653589793;
    public const E = 2.718281828459045;
    public const GOLDEN_RATIO = 1.618033988749895;
    
    // Calculated constants
    public const PI_SQUARED = self::PI * self::PI;
    public const E_SQUARED = self::E * self::E;
    public const PHI_INVERSE = 1 / self::GOLDEN_RATIO;
    
    // Constants based on calculations
    public const DEGREES_TO_RADIANS = self::PI / 180;
    public const RADIANS_TO_DEGREES = 180 / self::PI;
    
    // Array constants
    public const FIBONACCI_FIRST_10 = [0, 1, 1, 2, 3, 5, 8, 13, 21, 34];
    public const PRIMES_UNDER_100 = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97];
    
    // Static method to get constant value
    public static function get(string $constant) {
        return constant("self::$constant");
    }
    
    // Static method to calculate circle area
    public static function circleArea(float $radius): float {
        return self::PI * $radius * $radius;
    }
    
    // Static method to calculate circle circumference
    public static function circleCircumference(float $radius): float {
        return 2 * self::PI * $radius;
    }
    
    // Static method to convert degrees to radians
    public static function degreesToRadians(float $degrees): float {
        return $degrees * self::DEGREES_TO_RADIANS;
    }
    
    // Static method to convert radians to degrees
    public static function radiansToDegrees(float $radians): float {
        return $radians * self::RADIANS_TO_DEGREES;
    }
}

// Enum-like class using constants
class UserRole {
    const GUEST = 'guest';
    const USER = 'user';
    const MODERATOR = 'moderator';
    const ADMIN = 'admin';
    const SUPER_ADMIN = 'super_admin';
    
    // Array of all roles
    public const ALL_ROLES = [
        self::GUEST,
        self::USER,
        self::MODERATOR,
        self::ADMIN,
        self::SUPER_ADMIN
    ];
    
    // Role hierarchy
    public const ROLE_HIERARCHY = [
        self::GUEST => 0,
        self::USER => 1,
        self::MODERATOR => 2,
        self::ADMIN => 3,
        self::SUPER_ADMIN => 4
    ];
    
    // Static method to check if role exists
    public static function isValid(string $role): bool {
        return in_array($role, self::ALL_ROLES);
    }
    
    // Static method to get role level
    public static function getLevel(string $role): int {
        return self::ROLE_HIERARCHY[$role] ?? -1;
    }
    
    // Static method to check if role has permission
    public static function hasPermission(string $role, string $requiredRole): bool {
        $roleLevel = self::getLevel($role);
        $requiredLevel = self::getLevel($requiredRole);
        
        return $roleLevel >= $requiredLevel;
    }
    
    // Static method to get all roles above a certain level
    public static function getRolesAbove(int $level): array {
        $roles = [];
        
        foreach (self::ROLE_HIERARCHY as $role => $roleLevel) {
            if ($roleLevel > $level) {
                $roles[] = $role;
            }
        }
        
        return $roles;
    }
}

// Using class constants
echo HTTPStatus::OK; // 200
echo HTTPStatus::getMessage(HTTPStatus::OK); // "OK"
echo HTTPStatus::isSuccessful(HTTPStatus::OK); // true

echo MathConstants::PI; // 3.141592653589793
echo MathConstants::circleArea(5); // 78.53981633974483
echo MathConstants::degreesToRadians(180); // 3.141592653589793

echo UserRole::isValid('admin'); // true
echo UserRole::hasPermission('user', 'moderator'); // false
echo UserRole::hasPermission('admin', 'user'); // true
?>
```

## Best Practices

### OOP Best Practices
```php
<?php
// Single Responsibility Principle
class UserValidator {
    public function validate(array $userData): array {
        $errors = [];
        
        if (empty($userData['name'])) {
            $errors[] = 'Name is required';
        }
        
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email';
        }
        
        return $errors;
    }
}

class UserRepository {
    private PDO $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function save(array $userData): int {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, created_at) VALUES (?, ?, ?)"
        );
        $stmt->execute([$userData['name'], $userData['email'], date('Y-m-d H:i:s')]);
        return $this->db->lastInsertId();
    }
    
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}

class User {
    private int $id;
    private string $name;
    private string $email;
    private DateTime $createdAt;
    
    public function __construct(int $id, string $name, string $email, DateTime $createdAt) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
}

// Open/Closed Principle
interface LoggerInterface {
    public function log(string $message): void;
}

class FileLogger implements LoggerInterface {
    private string $filename;
    
    public function __construct(string $filename) {
        $this->filename = $filename;
    }
    
    public function log(string $message): void {
        file_put_contents($this->filename, $message . PHP_EOL, FILE_APPEND);
    }
}

class DatabaseLogger implements LoggerInterface {
    private PDO $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function log(string $message): void {
        $stmt = $this->db->prepare("INSERT INTO logs (message, created_at) VALUES (?, ?)");
        $stmt->execute([$message, date('Y-m-d H:i:s')]);
    }
}

// Dependency Inversion Principle
class UserService {
    private LoggerInterface $logger;
    private UserRepository $repository;
    private UserValidator $validator;
    
    public function __construct(
        LoggerInterface $logger,
        UserRepository $repository,
        UserValidator $validator
    ) {
        $this->logger = $logger;
        $this->repository = $repository;
        $this->validator = $validator;
    }
    
    public function createUser(array $userData): User {
        $errors = $this->validator->validate($userData);
        
        if (!empty($errors)) {
            throw new InvalidArgumentException('Validation failed: ' . implode(', ', $errors));
        }
        
        try {
            $id = $this->repository->save($userData);
            $this->logger->log("User created with ID: $id");
            
            $userDataArray = $this->repository->findById($id);
            return new User($id, $userDataArray['name'], $userDataArray['email'], new DateTime($userDataArray['created_at']));
        } catch (Exception $e) {
            $this->logger->log("Failed to create user: " . $e->getMessage());
            throw $e;
        }
    }
}

// Interface Segregation Principle
interface ReadableRepository {
    public function findById(int $id): ?array;
    public function findAll(): array;
}

interface WritableRepository {
    public function save(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

interface UserRepository extends ReadableRepository, WritableRepository {
    public function findByEmail(string $email): ?array;
    public function findActive(): array;
}

// Liskov Substitution Principle
abstract class Bird {
    abstract public function fly(): string;
    abstract public function makeSound(): string;
}

class Sparrow extends Bird {
    public function fly(): string {
        return "Sparrow flies quickly";
    }
    
    public function makeSound(): string {
        return "Tweet tweet";
    }
}

class Penguin extends Bird {
    public function fly(): string {
        throw new RuntimeException("Penguins cannot fly");
    }
    
    public function makeSound(): string {
        return "Squawk";
    }
}

// Better solution - separate interface for flying
interface Flyable {
    public function fly(): string;
}

class Bird {
    abstract public function makeSound(): string;
}

class Sparrow extends Bird implements Flyable {
    public function fly(): string {
        return "Sparrow flies quickly";
    }
    
    public function makeSound(): string {
        return "Tweet tweet";
    }
}

class Penguin extends Bird {
    public function makeSound(): string {
        return "Squawk";
    }
}

// Factory Pattern
class UserFactory {
    public static function create(array $userData): User {
        $validator = new UserValidator();
        $errors = $validator->validate($userData);
        
        if (!empty($errors)) {
            throw new InvalidArgumentException('Validation failed: ' . implode(', ', $errors));
        }
        
        return new User(
            random_int(1, 1000),
            $userData['name'],
            $userData['email'],
            new DateTime()
        );
    }
    
    public static function createFromDatabase(int $id, UserRepository $repository): User {
        $userData = $repository->findById($id);
        
        if (!$userData) {
            throw new RuntimeException("User not found with ID: $id");
        }
        
        return new User(
            $userData['id'],
            $userData['name'],
            $userData['email'],
            new DateTime($userData['created_at'])
        );
    }
}

// Builder Pattern
class UserBuilder {
    private string $name = '';
    private string $email = '';
    private ?DateTime $birthDate = null;
    private array $addresses = [];
    private array $preferences = [];
    
    public function name(string $name): self {
        $this->name = $name;
        return $this;
    }
    
    public function email(string $email): self {
        $this->email = $email;
        return $this;
    }
    
    public function birthDate(DateTime $birthDate): self {
        $this->birthDate = $birthDate;
        return $this;
    }
    
    public function addAddress(array $address): self {
        $this->addresses[] = $address;
        return $this;
    }
    
    public function setPreference(string $key, $value): self {
        $this->preferences[$key] = $value;
        return $this;
    }
    
    public function build(): User {
        if (empty($this->name) || empty($this->email)) {
            throw new InvalidArgumentException('Name and email are required');
        }
        
        return new User(
            random_int(1, 1000),
            $this->name,
            $this->email,
            $this->birthDate,
            $this->addresses,
            $this->preferences
        );
    }
}
?>
```

### Design Patterns
```php
<?php
// Singleton Pattern
class DatabaseConnection {
    private static ?self $instance = null;
    private PDO $connection;
    
    private function __construct() {
        $this->connection = new PDO('mysql:host=localhost;dbname=test', 'user', 'password');
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function getConnection(): PDO {
        return $this->connection;
    }
    
    private function __clone() {}
    public function __wakeup() {}
}

// Observer Pattern
interface ObserverInterface {
    public function update(string $event, array $data): void;
}

interface SubjectInterface {
    public function attach(ObserverInterface $observer): void;
    public function detach(ObserverInterface $observer): void;
    public function notify(string $event, array $data): void;
}

class User implements SubjectInterface {
    private array $observers = [];
    private string $name;
    private string $status = 'active';
    
    public function __construct(string $name) {
        $this->name = $name;
    }
    
    public function attach(ObserverInterface $observer): void {
        $this->observers[] = $observer;
    }
    
    public function detach(ObserverInterface $observer): void {
        $this->observers = array_filter($this->observers, fn($o) => $o !== $observer);
    }
    
    public function notify(string $event, array $data): void {
        foreach ($this->observers as $observer) {
            $observer->update($event, array_merge(['user' => $this], $data));
        }
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getStatus(): string {
        return $this->status;
    }
    
    public function setStatus(string $status): void {
        $oldStatus = $this->status;
        $this->status = $status;
        $this->notify('status_changed', ['old_status' => $oldStatus, 'new_status' => $status]);
    }
}

class EmailNotifier implements ObserverInterface {
    public function update(string $event, array $data): void {
        if ($event === 'status_changed') {
            $user = $data['user'];
            echo "Email sent to {$user->getName()}: Status changed from {$data['old_status']} to {$data['new_status']}\n";
        }
    }
}

class Logger implements ObserverInterface {
    public function update(string $event, array $data): void {
        echo "Log: $event for user {$data['user']->getName()}\n";
    }
}

// Strategy Pattern
interface PaymentStrategyInterface {
    public function pay(float $amount): bool;
}

class CreditCardPayment implements PaymentStrategyInterface {
    private string $cardNumber;
    private string $expiry;
    private string $cvv;
    
    public function __construct(string $cardNumber, string $expiry, string $cvv) {
        $this->cardNumber = $cardNumber;
        $this->expiry = $expiry;
        $this->cvv = $cvv;
    }
    
    public function pay(float $amount): bool {
        echo "Processing credit card payment of $amount\n";
        // Process credit card payment
        return true;
    }
}

class PayPalPayment implements PaymentStrategyInterface {
    private string $email;
    private string $password;
    
    public function __construct(string $email, string $password) {
        $this->email = $email;
        $this->password = $password;
    }
    
    public function pay(float $amount): bool {
        echo "Processing PayPal payment of $amount\n";
        // Process PayPal payment
        return true;
    }
}

class ShoppingCart {
    private array $items = [];
    private PaymentStrategyInterface $paymentStrategy;
    
    public function addItem(string $item, float $price): void {
        $this->items[] = ['item' => $item, 'price' => $price];
    }
    
    public function setPaymentStrategy(PaymentStrategyInterface $strategy): void {
        $this->paymentStrategy = $strategy;
    }
    
    public function checkout(): bool {
        $total = array_sum(array_column($this->items, 'price'));
        return $this->paymentStrategy->pay($total);
    }
}

// Decorator Pattern
interface NotifierInterface {
    public function send(string $message): void;
}

class BasicNotifier implements NotifierInterface {
    public function send(string $message): void {
        echo "Sending message: $message\n";
    }
}

abstract class NotifierDecorator implements NotifierInterface {
    protected NotifierInterface $notifier;
    
    public function __construct(NotifierInterface $notifier) {
        $this->notifier = $notifier;
    }
    
    public function send(string $message): void {
        $this->notifier->send($message);
    }
}

class SMSNotifier extends NotifierDecorator {
    private string $phoneNumber;
    
    public function __construct(NotifierInterface $notifier, string $phoneNumber) {
        parent::__construct($notifier);
        $this->phoneNumber = $phoneNumber;
    }
    
    public function send(string $message): void {
        parent::send($message);
        echo "Sending SMS to {$this->phoneNumber}: $message\n";
    }
}

class FacebookNotifier extends NotifierDecorator {
    private string $facebookId;
    
    public function __construct(NotifierInterface $notifier, string $facebookId) {
        parent::__construct($notifier);
        $this->facebookId = $facebookId;
    }
    
    public function send(string $message): void {
        parent::send($message);
        echo "Posting to Facebook ({$this->facebookId}): $message\n";
    }
}

// Using the patterns
// Singleton
$db1 = DatabaseConnection::getInstance();
$db2 = DatabaseConnection::getInstance();
echo $db1 === $db2; // true

// Observer
$user = new User("John Doe");
$user->attach(new EmailNotifier());
$user->attach(new Logger());
$user->setStatus("inactive");

// Strategy
$cart = new ShoppingCart();
$cart->addItem("Laptop", 999.99);
$cart->addItem("Mouse", 29.99);
$cart->setPaymentStrategy(new CreditCardPayment("1234-5678-9012-3456", "12/25", "123"));
$cart->checkout();

$cart->setPaymentStrategy(new PayPalPayment("user@example.com", "password"));
$cart->checkout();

// Decorator
$notifier = new BasicNotifier();
$smsNotifier = new SMSNotifier($notifier, "+1234567890");
$facebookNotifier = new FacebookNotifier($smsNotifier, "user123");

$facebookNotifier->send("Hello World!");
?>
```

## Common Pitfalls

### OOP Pitfalls
```php
<?php
// Pitfall: God Class
class UserManager {
    // This class does too many things
    public function createUser(array $data) { /* ... */ }
    public function validateUser(array $data) { /* ... */ }
    public function sendWelcomeEmail(User $user) { /* ... */ }
    public function logUserActivity(User $user, string $action) { /* ... */ }
    public function backupUserData(User $user) { /* ... */ }
    public function generateUserReport(User $user) { /* ... */ }
    public function calculateUserAge(User $user) { /* ... */ }
}

// Solution: Single Responsibility Principle
class UserValidator {
    public function validate(array $data): array { /* ... */ }
}

class UserRepository {
    public function save(User $user): void { /* ... */ }
}

class EmailService {
    public function sendWelcomeEmail(User $user): void { /* ... */ }
}

class ActivityLogger {
    public function logUserActivity(User $user, string $action): void { /* ... */ }
}

// Pitfall: Tight Coupling
class OrderProcessor {
    private DatabaseConnection $db;
    private EmailService $email;
    private PaymentGateway $payment;
    
    public function __construct() {
        $this->db = new DatabaseConnection(); // Hard-coded dependency
        $this->email = new EmailService(); // Hard-coded dependency
        $this->payment = new PaymentGateway(); // Hard-coded dependency
    }
    
    public function processOrder(Order $order): void {
        // All dependencies are hard-coded
    }
}

// Solution: Dependency Injection
class OrderProcessor {
    private DatabaseConnection $db;
    private EmailService $email;
    private PaymentGateway $payment;
    
    public function __construct(
        DatabaseConnection $db,
        EmailService $email,
        PaymentGateway $payment
    ) {
        $this->db = $db;
        $this->email = $email;
        $this->payment = $payment;
    }
    
    public function processOrder(Order $order): void {
        // Dependencies are injected
    }
}

// Pitfall: Not following Liskov Substitution Principle
class Bird {
    public function fly(): string {
        return "Flying";
    }
}

class Penguin extends Bird {
    public function fly(): string {
        throw new Exception("Penguins can't fly");
    }
}

function makeBirdFly(Bird $bird): void {
    echo $bird->fly(); // Will fail for Penguin
}

// Solution: Separate interfaces
interface Flyable {
    public function fly(): string;
}

class Bird {
    // Common bird methods
}

class Sparrow extends Bird implements Flyable {
    public function fly(): string {
        return "Flying";
    }
}

class Penguin extends Bird {
    // Penguin-specific methods, no fly method
}

function makeBirdFly(Flyable $bird): void {
    echo $bird->fly(); // Type-safe
}

// Pitfall: Breaking encapsulation
class User {
    public string $name; // Direct access
    public string $email; // Direct access
    public int $age; // Direct access
}

$user = new User();
$user->name = "John"; // No validation
$user->age = -5; // No validation

// Solution: Proper encapsulation
class User {
    private string $name;
    private string $email;
    private int $age;
    
    public function setName(string $name): void {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("Name cannot be empty");
        }
        $this->name = trim($name);
    }
    
    public function setAge(int $age): void {
        if ($age < 0 || $age > 150) {
            throw new InvalidArgumentException("Invalid age");
        }
        $this->age = $age;
    }
    
    // Getters...
}

// Pitfall: Static overuse
class UserService {
    public static $users = []; // Global state
    
    public static function createUser(array $data): User {
        $user = new User($data);
        self::$users[] = $user;
        return $user;
    }
    
    public static function getUser(int $id): ?User {
        foreach (self::$users as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }
        return null;
    }
}

// Solution: Instance-based approach
class UserService {
    private array $users = [];
    
    public function createUser(array $data): User {
        $user = new User($data);
        $this->users[] = $user;
        return $user;
    }
    
    public function getUser(int $id): ?User {
        foreach ($this->users as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }
        return null;
    }
}

// Pitfall: Inheritance over composition
class Car extends Engine {
    // Car IS-A Engine? No, Car HAS-A Engine
}

// Solution: Composition
class Car {
    private Engine $engine;
    
    public function __construct(Engine $engine) {
        $this->engine = $engine;
    }
}

class Engine {
    public function start(): void {
        echo "Engine started";
    }
}

// Pitfall: Not using interfaces
class FileLogger {
    public function log(string $message): void {
        file_put_contents('app.log', $message . PHP_EOL, FILE_APPEND);
    }
}

class DatabaseLogger {
    public function log(string $message): void {
        // Database logging logic
    }
}

function processWithLogger($logger) {
    // Can't guarantee logger has log method
    $logger->log("Processing...");
}

// Solution: Use interfaces
interface LoggerInterface {
    public function log(string $message): void;
}

class FileLogger implements LoggerInterface {
    public function log(string $message): void {
        file_put_contents('app.log', $message . PHP_EOL, FILE_APPEND);
    }
}

class DatabaseLogger implements LoggerInterface {
    public function log(string $message): void {
        // Database logging logic
    }
}

function processWithLogger(LoggerInterface $logger): void {
    $logger->log("Processing..."); // Type-safe
}
?>
```

### Performance Pitfalls
```php
<?php
// Pitfall: Creating too many objects
class User {
    public function __construct(public string $name, public string $email) {}
}

// Bad: Creating objects in a loop
$users = [];
for ($i = 0; $i < 10000; $i++) {
    $users[] = new User("User $i", "user$i@example.com");
}

// Solution: Use object pooling or lazy loading
class UserPool {
    private static array $pool = [];
    
    public static function get(string $name, string $email): User {
        $key = md5($name . $email);
        
        if (!isset(self::$pool[$key])) {
            self::$pool[$key] = new User($name, $email);
        }
        
        return self::$pool[$key];
    }
}

// Pitfall: Not using dependency injection properly
class Service {
    private DatabaseConnection $db;
    
    public function __construct() {
        $this->db = new DatabaseConnection(); // Creates new connection every time
    }
}

// Solution: Use dependency injection or singleton pattern
class Service {
    private DatabaseConnection $db;
    
    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }
}

// Pitfall: Heavy inheritance chains
class Animal { }
class Mammal extends Animal { }
class Dog extends Mammal { }
class GoldenRetriever extends Dog { }
class ServiceDog extends GoldenRetriever { }
class GuideDog extends ServiceDog { }

// Too many levels of inheritance make code hard to maintain

// Solution: Use composition and interfaces
interface AnimalInterface {
    public function makeSound(): string;
}

interface TrainableInterface {
    public function train(): void;
}

class Dog implements AnimalInterface, TrainableInterface {
    public function makeSound(): string {
        return "Woof!";
    }
    
    public function train(): void {
        echo "Training dog...\n";
    }
}

class ServiceDog implements TrainableInterface {
    private AnimalInterface $dog;
    
    public function __construct(AnimalInterface $dog) {
        $this->dog = $dog;
    }
    
    public function train(): void {
        echo "Training service dog...\n";
    }
}

// Pitfall: Not using caching for expensive operations
class ExpensiveCalculator {
    public function calculate(array $data): array {
        // Expensive calculation
        sleep(2);
        return array_map('sqrt', $data);
    }
}

// Solution: Use caching
class CachedCalculator {
    private array $cache = [];
    
    public function calculate(array $data): array {
        $key = md5(serialize($data));
        
        if (!isset($this->cache[$key])) {
            $this->cache[$key] = $this->performCalculation($data);
        }
        
        return $this->cache[$key];
    }
    
    private function performCalculation(array $data): array {
        sleep(2); // Expensive operation
        return array_map('sqrt', $data);
    }
}

// Pitfall: Not using lazy loading
class UserWithPosts {
    private array $posts = [];
    
    public function __construct(private int $userId) {
        $this->posts = $this->loadAllPosts(); // Loads all posts immediately
    }
    
    private function loadAllPosts(): array {
        // Expensive database query
        return [];
    }
}

// Solution: Lazy loading
class UserWithLazyPosts {
    private ?array $posts = null;
    
    public function __construct(private int $userId) {
        // Posts not loaded yet
    }
    
    public function getPosts(): array {
        if ($this->posts === null) {
            $this->posts = $this->loadPosts(); // Load only when needed
        }
        
        return $this->posts;
    }
    
    private function loadPosts(): array {
        // Expensive database query
        return [];
    }
}

// Pitfall: Not using factories for complex object creation
class ComplexObject {
    public function __construct(
        private DatabaseConnection $db,
        private LoggerInterface $logger,
        private CacheInterface $cache,
        private EmailService $email,
        private PaymentGateway $payment
    ) {
        // Complex initialization
    }
}

// Solution: Use factory pattern
class ComplexObjectFactory {
    public static function create(): ComplexObject {
        $db = DatabaseConnection::getInstance();
        $logger = new FileLogger('app.log');
        $cache = new RedisCache();
        $email = new EmailService($logger);
        $payment = new PaymentGateway($cache);
        
        return new ComplexObject($db, $logger, $cache, $email, $payment);
    }
}

// Pitfall: Not using autoloading
require_once 'User.php';
require_once 'Product.php';
require_once 'Order.php';
require_once 'Database.php';
require_once 'Logger.php';

// Solution: Use autoloading
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Or better yet, use Composer autoloader
?>
```

## Summary

PHP Object-Oriented Programming provides:

**Classes and Objects:**
- Class definition with properties and methods
- Constructors and destructors
- Object instantiation and usage
- Type hints and return types

**Inheritance:**
- Parent-child class relationships
- Method overriding and extension
- Abstract classes and methods
- Polymorphism and type checking

**Interfaces and Traits:**
- Interface contracts and implementation
- Multiple interface implementation
- Traits for code reuse
- Composition over inheritance

**Encapsulation:**
- Access modifiers (public, protected, private)
- Getters and setters
- Data hiding and validation
- Magic methods for property access

**Static Members:**
- Static properties and methods
- Class constants
- Static factory methods
- Utility classes

**Design Patterns:**
- Singleton, Factory, Builder patterns
- Observer, Strategy, Decorator patterns
- SOLID principles implementation
- Dependency injection

**Best Practices:**
- Single Responsibility Principle
- Open/Closed Principle
- Liskov Substitution Principle
- Interface Segregation Principle
- Dependency Inversion Principle

**Common Pitfalls:**
- God classes and tight coupling
- Breaking encapsulation
- Static overuse
- Inheritance abuse
- Performance issues

PHP's OOP features provide powerful tools for building maintainable, scalable applications when following established design principles and patterns.
