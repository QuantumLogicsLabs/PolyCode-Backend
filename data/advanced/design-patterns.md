# PHP Design Patterns

## Creational Patterns

### Singleton Pattern
```php
<?php
class DatabaseConnection {
    private static ?PDO $instance = null;
    private PDO $connection;
    
    private function __construct() {
        $this->connection = new PDO(
            'mysql:host=localhost;dbname=testdb',
            'username',
            'password',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true
            ]
        );
    }
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Usage
$db = DatabaseConnection::getInstance();
$stmt = $db->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll();

// Thread-safe singleton
class ThreadSafeSingleton {
    private static ?self $instance = null;
    private static $lock = null;
    
    private function __construct() {
        // Initialize
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            if (self::$lock === null) {
                self::$lock = new stdClass();
            }
            
            // Simulate lock acquisition
            if (self::$instance === null) {
                self::$instance = new self();
            }
        }
        
        return self::$instance;
    }
}

// Configuration singleton
class Config {
    private static ?self $instance = null;
    private array $settings = [];
    
    private function __construct() {
        $this->loadSettings();
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    private function loadSettings(): void {
        $this->settings = [
            'database' => [
                'host' => 'localhost',
                'name' => 'testdb',
                'username' => 'user',
                'password' => 'pass'
            ],
            'app' => [
                'name' => 'MyApp',
                'version' => '1.0.0',
                'debug' => false
            ],
            'cache' => [
                'driver' => 'redis',
                'ttl' => 3600
            ]
        ];
    }
    
    public function get(string $key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->settings;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public function set(string $key, $value): void {
        $keys = explode('.', $key);
        $settings = &$this->settings;
        
        foreach (array_slice($keys, 0, -1) as $k) {
            if (!isset($settings[$k])) {
                $settings[$k] = [];
            }
            $settings = &$settings[$k];
        }
        
        $settings[end($keys)] = $value;
    }
}

// Usage
$config = Config::getInstance();
$dbHost = $config->get('database.host');
$appName = $config->get('app.name', 'Default App');
?>
```

### Factory Pattern
```php
<?php
interface Vehicle {
    public function drive(): string;
    public function stop(): string;
    public function getFuelType(): string;
}

class Car implements Vehicle {
    private string $brand;
    
    public function __construct(string $brand) {
        $this->brand = $brand;
    }
    
    public function drive(): string {
        return "Car {$this->brand} is driving";
    }
    
    public function stop(): string {
        return "Car {$this->brand} stopped";
    }
    
    public function getFuelType(): string {
        return "Gasoline";
    }
}

class Motorcycle implements Vehicle {
    private string $brand;
    
    public function __construct(string $brand) {
        $this->brand = $brand;
    }
    
    public function drive(): string {
        return "Motorcycle {$this->brand} is driving";
    }
    
    public function stop(): string {
        return "Motorcycle {$this->brand} stopped";
    }
    
    public function getFuelType(): string {
        return "Gasoline";
    }
}

class ElectricCar implements Vehicle {
    private string $brand;
    
    public function __construct(string $brand) {
        $this->brand = $brand;
    }
    
    public function drive(): string {
        return "Electric car {$this->brand} is driving silently";
    }
    
    public function stop(): string {
        return "Electric car {$this->brand} stopped";
    }
    
    public function getFuelType(): string {
        return "Electric";
    }
}

// Simple Factory
class VehicleFactory {
    public static function createVehicle(string $type, string $brand): Vehicle {
        switch (strtolower($type)) {
            case 'car':
                return new Car($brand);
            case 'motorcycle':
                return new Motorcycle($brand);
            case 'electric':
                return new ElectricCar($brand);
            default:
                throw new InvalidArgumentException("Unknown vehicle type: $type");
        }
    }
}

// Factory Method
abstract class VehicleManufacturer {
    abstract public function createVehicle(string $brand): Vehicle;
    
    public function produceVehicle(string $brand): Vehicle {
        $vehicle = $this->createVehicle($brand);
        echo "Manufacturing " . get_class($vehicle) . " for brand: $brand\n";
        return $vehicle;
    }
}

class CarManufacturer extends VehicleManufacturer {
    public function createVehicle(string $brand): Vehicle {
        return new Car($brand);
    }
}

class ElectricVehicleManufacturer extends VehicleManufacturer {
    public function createVehicle(string $brand): Vehicle {
        return new ElectricCar($brand);
    }
}

// Abstract Factory
interface VehicleFactoryInterface {
    public function createCar(string $brand): Vehicle;
    public function createMotorcycle(string $brand): Vehicle;
}

class GasolineVehicleFactory implements VehicleFactoryInterface {
    public function createCar(string $brand): Vehicle {
        return new Car($brand);
    }
    
    public function createMotorcycle(string $brand): Vehicle {
        return new Motorcycle($brand);
    }
}

class ElectricVehicleFactory implements VehicleFactoryInterface {
    public function createCar(string $brand): Vehicle {
        return new ElectricCar($brand);
    }
    
    public function createMotorcycle(string $brand): Vehicle {
        // Electric motorcycles would be a separate class
        return new ElectricCar($brand); // For simplicity
    }
}

// Usage
// Simple Factory
$car = VehicleFactory::createVehicle('car', 'Toyota');
echo $car->drive() . "\n";

// Factory Method
$carManufacturer = new CarManufacturer();
$toyotaCar = $carManufacturer->produceVehicle('Toyota');
echo $toyotaCar->drive() . "\n";

// Abstract Factory
$factory = new GasolineVehicleFactory();
$gasCar = $factory->createCar('Honda');
$gasMotorcycle = $factory->createMotorcycle('Yamaha');

echo $gasCar->drive() . "\n";
echo $gasMotorcycle->drive() . "\n";

// Database connection factory
interface DatabaseConnectionInterface {
    public function connect(): PDO;
    public function disconnect(): void;
    public function query(string $sql, array $params = []): array;
}

class MySQLConnection implements DatabaseConnectionInterface {
    private ?PDO $connection = null;
    private array $config;
    
    public function __construct(array $config) {
        $this->config = $config;
    }
    
    public function connect(): PDO {
        if ($this->connection === null) {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']}";
            $this->connection = new PDO($dsn, $this->config['username'], $this->config['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        return $this->connection;
    }
    
    public function disconnect(): void {
        $this->connection = null;
    }
    
    public function query(string $sql, array $params = []): array {
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

class PostgreSQLConnection implements DatabaseConnectionInterface {
    private ?PDO $connection = null;
    private array $config;
    
    public function __construct(array $config) {
        $this->config = $config;
    }
    
    public function connect(): PDO {
        if ($this->connection === null) {
            $dsn = "pgsql:host={$this->config['host']};dbname={$this->config['database']}";
            $this->connection = new PDO($dsn, $this->config['username'], $this->config['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        return $this->connection;
    }
    
    public function disconnect(): void {
        $this->connection = null;
    }
    
    public function query(string $sql, array $params = []): array {
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

class DatabaseConnectionFactory {
    public static function create(string $type, array $config): DatabaseConnectionInterface {
        switch (strtolower($type)) {
            case 'mysql':
                return new MySQLConnection($config);
            case 'postgresql':
                return new PostgreSQLConnection($config);
            default:
                throw new InvalidArgumentException("Unsupported database type: $type");
        }
    }
}

// Usage
$dbConfig = [
    'host' => 'localhost',
    'database' => 'testdb',
    'username' => 'user',
    'password' => 'pass'
];

$mysqlDb = DatabaseConnectionFactory::create('mysql', $dbConfig);
$users = $mysqlDb->query('SELECT * FROM users');
?>
```

### Builder Pattern
```php
<?php
class User {
    private string $name;
    private string $email;
    private ?int $age = null;
    private ?string $phone = null;
    private array $addresses = [];
    private array $preferences = [];
    private DateTime $createdAt;
    
    public function __construct(UserBuilder $builder) {
        $this->name = $builder->name;
        $this->email = $builder->email;
        $this->age = $builder->age;
        $this->phone = $builder->phone;
        $this->addresses = $builder->addresses;
        $this->preferences = $builder->preferences;
        $this->createdAt = $builder->createdAt;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getAge(): ?int {
        return $this->age;
    }
    
    public function getPhone(): ?string {
        return $this->phone;
    }
    
    public function getAddresses(): array {
        return $this->addresses;
    }
    
    public function getPreferences(): array {
        return $this->preferences;
    }
    
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
    
    public function toArray(): array {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
            'phone' => $this->phone,
            'addresses' => $this->addresses,
            'preferences' => $this->preferences,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
}

class UserBuilder {
    public string $name;
    public string $email;
    public ?int $age = null;
    public ?string $phone = null;
    public array $addresses = [];
    public array $preferences = [];
    public DateTime $createdAt;
    
    public function __construct(string $name, string $email) {
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = new DateTime();
    }
    
    public function setAge(int $age): self {
        $this->age = $age;
        return $this;
    }
    
    public function setPhone(string $phone): self {
        $this->phone = $phone;
        return $this;
    }
    
    public function addAddress(string $type, string $street, string $city, string $country): self {
        $this->addresses[] = [
            'type' => $type,
            'street' => $street,
            'city' => $city,
            'country' => $country
        ];
        return $this;
    }
    
    public function addPreference(string $key, $value): self {
        $this->preferences[$key] = $value;
        return $this;
    }
    
    public function setCreatedAt(DateTime $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function build(): User {
        // Validation
        if (empty($this->name)) {
            throw new InvalidArgumentException("Name is required");
        }
        
        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Valid email is required");
        }
        
        if ($this->age !== null && ($this->age < 0 || $this->age > 150)) {
            throw new InvalidArgumentException("Age must be between 0 and 150");
        }
        
        return new User($this);
    }
}

// Director class for complex constructions
class UserDirector {
    private UserBuilder $builder;
    
    public function __construct(UserBuilder $builder) {
        $this->builder = $builder;
    }
    
    public function createStandardUser(string $name, string $email, int $age): User {
        return $this->builder
            ->setAge($age)
            ->addAddress('home', '123 Main St', 'New York', 'USA')
            ->addPreference('newsletter', true)
            ->addPreference('theme', 'light')
            ->build();
    }
    
    public function createPremiumUser(string $name, string $email, int $age, string $phone): User {
        return $this->builder
            ->setAge($age)
            ->setPhone($phone)
            ->addAddress('home', '123 Main St', 'New York', 'USA')
            ->addAddress('work', '456 Business Ave', 'New York', 'USA')
            ->addPreference('newsletter', true)
            ->addPreference('theme', 'dark')
            ->addPreference('notifications', 'all')
            ->addPreference('premium_features', true)
            ->build();
    }
}

// Query Builder
class QueryBuilder {
    private string $table;
    private array $columns = ['*'];
    private array $where = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];
    private array $groupBy = [];
    private array $having = [];
    
    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }
    
    public function select(string ...$columns): self {
        $this->columns = $columns;
        return $this;
    }
    
    public function where(string $column, string $operator, $value): self {
        $this->where[] = "$column $operator ?";
        return $this;
    }
    
    public function whereIn(string $column, array $values): self {
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        $this->where[] = "$column IN ($placeholders)";
        return $this;
    }
    
    public function whereBetween(string $column, $start, $end): self {
        $this->where[] = "$column BETWEEN ? AND ?";
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
    
    public function join(string $table, string $firstColumn, string $operator, string $secondColumn): self {
        $this->joins[] = "INNER JOIN $table ON $firstColumn $operator $secondColumn";
        return $this;
    }
    
    public function leftJoin(string $table, string $firstColumn, string $operator, string $secondColumn): self {
        $this->joins[] = "LEFT JOIN $table ON $firstColumn $operator $secondColumn";
        return $this;
    }
    
    public function groupBy(string ...$columns): self {
        $this->groupBy = $columns;
        return $this;
    }
    
    public function having(string $column, string $operator, $value): self {
        $this->having[] = "$column $operator ?";
        return $this;
    }
    
    public function getSQL(): string {
        $sql = "SELECT " . implode(', ', $this->columns);
        $sql .= " FROM {$this->table}";
        
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
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
        $bindings = [];
        
        // Extract bindings from where clauses
        // This is simplified - in real implementation, you'd need to parse the SQL
        // or collect bindings as you build the query
        
        return $bindings;
    }
}

// Usage
// User Builder
$builder = new UserBuilder('John Doe', 'john@example.com');
$user = $builder
    ->setAge(30)
    ->setPhone('+1234567890')
    ->addAddress('home', '123 Main St', 'New York', 'USA')
    ->addAddress('work', '456 Business Ave', 'New York', 'USA')
    ->addPreference('newsletter', true)
    ->addPreference('theme', 'dark')
    ->build();

echo "User created: " . $user->getName() . "\n";

// Using Director
$director = new UserDirector(new UserBuilder('', ''));
$standardUser = $director->createStandardUser('Jane Smith', 'jane@example.com', 25);
$premiumUser = $director->createPremiumUser('Bob Johnson', 'bob@example.com', 35, '+9876543210');

// Query Builder
$query = new QueryBuilder();
$sql = $query
    ->table('users')
    ->select('id', 'name', 'email')
    ->where('age', '>=', 18)
    ->where('status', '=', 'active')
    ->orderBy('name', 'ASC')
    ->limit(10)
    ->getSQL();

echo "Generated SQL: $sql\n";
?>
```

### Prototype Pattern
```php
<?php
interface Prototype {
    public function __clone();
    public function copy(): self;
}

abstract class Shape implements Prototype {
    protected string $color;
    protected int $x;
    protected int $y;
    
    public function __construct(string $color, int $x, int $y) {
        $this->color = $color;
        $this->x = $x;
        $this->y = $y;
    }
    
    public function getColor(): string {
        return $this->color;
    }
    
    public function setColor(string $color): void {
        $this->color = $color;
    }
    
    public function getPosition(): array {
        return [$this->x, $this->y];
    }
    
    public function setPosition(int $x, int $y): void {
        $this->x = $x;
        $this->y = $y;
    }
    
    abstract public function draw(): string;
    abstract public function getArea(): float;
    
    public function copy(): self {
        return clone $this;
    }
}

class Circle extends Shape {
    private int $radius;
    
    public function __construct(string $color, int $x, int $y, int $radius) {
        parent::__construct(color: $color, x: $x, y: $y);
        $this->radius = $radius;
    }
    
    public function draw(): string {
        return "Drawing a {$this->color} circle at ({$this->x}, {$this->y}) with radius {$this->radius}";
    }
    
    public function getArea(): float {
        return pi() * $this->radius * $this->radius;
    }
    
    public function getRadius(): int {
        return $this->radius;
    }
    
    public function setRadius(int $radius): void {
        $this->radius = $radius;
    }
    
    public function __clone() {
        // Circle has only primitive properties, so default cloning is fine
    }
}

class Rectangle extends Shape {
    private int $width;
    private int $height;
    
    public function __construct(string $color, int $x, int $y, int $width, int $height) {
        parent::__construct(color: $color, x: $x, y: $y);
        $this->width = $width;
        $this->height = $height;
    }
    
    public function draw(): string {
        return "Drawing a {$this->color} rectangle at ({$this->x}, {$this->y}) with width {$this->width} and height {$this->height}";
    }
    
    public function getArea(): float {
        return $this->width * $this->height;
    }
    
    public function getWidth(): int {
        return $this->width;
    }
    
    public function setWidth(int $width): void {
        $this->width = $width;
    }
    
    public function getHeight(): int {
        return $this->height;
    }
    
    public function setHeight(int $height): void {
        $this->height = $height;
    }
    
    public function __clone() {
        // Rectangle has only primitive properties, so default cloning is fine
    }
}

// Complex object with nested objects
class Document implements Prototype {
    private string $title;
    private string $content;
    private array $metadata;
    private DateTime $createdAt;
    private Author $author;
    
    public function __construct(string $title, string $content, Author $author) {
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
        $this->createdAt = new DateTime();
        $this->metadata = [
            'version' => '1.0',
            'category' => 'general',
            'tags' => []
        ];
    }
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function setTitle(string $title): void {
        $this->title = $title;
    }
    
    public function getContent(): string {
        return $this->content;
    }
    
    public function setContent(string $content): void {
        $this->content = $content;
    }
    
    public function getAuthor(): Author {
        return $this->author;
    }
    
    public function setAuthor(Author $author): void {
        $this->author = $author;
    }
    
    public function getMetadata(): array {
        return $this->metadata;
    }
    
    public function addTag(string $tag): void {
        $this->metadata['tags'][] = $tag;
    }
    
    public function copy(): self {
        return clone $this;
    }
    
    public function __clone() {
        // Deep clone for nested objects
        $this->author = clone $this->author;
        $this->createdAt = clone $this->createdAt;
        $this->metadata = array_merge([], $this->metadata);
        $this->metadata['tags'] = array_merge([], $this->metadata['tags']);
    }
}

class Author {
    private string $name;
    private string $email;
    
    public function __construct(string $name, string $email) {
        $this->name = $name;
        $this->email = $email;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
}

// Prototype Registry
class ShapeRegistry {
    private array $shapes = [];
    
    public function addShape(string $name, Shape $shape): void {
        $this->shapes[$name] = $shape;
    }
    
    public function getShape(string $name): ?Shape {
        return $this->shapes[$name] ?? null;
    }
    
    public function createShape(string $name): ?Shape {
        $shape = $this->getShape($name);
        return $shape ? $shape->copy() : null;
    }
}

// Usage
// Create prototype shapes
$circlePrototype = new Circle('red', 0, 0, 10);
$rectanglePrototype = new Rectangle('blue', 0, 0, 20, 15);

// Create registry and add prototypes
$registry = new ShapeRegistry();
$registry->addShape('circle', $circlePrototype);
$registry->addShape('rectangle', $rectanglePrototype);

// Create new shapes from prototypes
$circle1 = $registry->createShape('circle');
$circle1->setColor('green');
$circle1->setPosition(10, 20);

$circle2 = $registry->createShape('circle');
$circle2->setColor('yellow');
$circle2->setRadius(15);
$circle2->setPosition(30, 40);

$rectangle1 = $registry->createShape('rectangle');
$rectangle1->setColor('purple');
$rectangle1->setPosition(50, 60);

echo $circle1->draw() . "\n";
echo $circle2->draw() . "\n";
echo $rectangle1->draw() . "\n";

// Complex object cloning
$author = new Author('John Doe', 'john@example.com');
$document = new Document('Original Document', 'Original content', $author);
$document->addTag('important');
$document->addTag('draft');

// Clone the document
$clonedDocument = $document->copy();
$clonedDocument->setTitle('Cloned Document');
$clonedDocument->setContent('Cloned content');
$clonedDocument->addTag('copy');

// Modify original author
$author = $document->getAuthor();
// $author->setName('Jane Doe'); // This would affect both if not deep cloned

echo "Original: " . $document->getTitle() . " by " . $document->getAuthor()->getName() . "\n";
echo "Cloned: " . $clonedDocument->getTitle() . " by " . $clonedDocument->getAuthor()->getName() . "\n";
?>
```

## Structural Patterns

### Adapter Pattern
```php
<?php
// Target interface
interface MediaPlayer {
    public function play(string $audioType, string $fileName): void;
}

// Adaptee classes
class Mp3Player {
    public function playMp3(string $fileName): void {
        echo "Playing mp3 file: $fileName\n";
    }
}

class Mp4Player {
    public function playMp4(string $fileName): void {
        echo "Playing mp4 file: $fileName\n";
    }
}

class VlcPlayer {
    public function playVlc(string $fileName): void {
        echo "Playing vlc file: $fileName\n";
    }
}

// Adapter class
class MediaAdapter implements MediaPlayer {
    private Mp3Player $mp3Player;
    private Mp4Player $mp4Player;
    private VlcPlayer $vlcPlayer;
    
    public function __construct() {
        $this->mp3Player = new Mp3Player();
        $this->mp4Player = new Mp4Player();
        $this->vlcPlayer = new VlcPlayer();
    }
    
    public function play(string $audioType, string $fileName): void {
        switch (strtolower($audioType)) {
            case 'mp3':
                $this->mp3Player->playMp3($fileName);
                break;
            case 'mp4':
                $this->mp4Player->playMp4($fileName);
                break;
            case 'vlc':
                $this->vlcPlayer->playVlc($fileName);
                break;
            default:
                echo "Invalid media. $audioType format not supported\n";
        }
    }
}

// Client class
class AudioPlayer implements MediaPlayer {
    private MediaAdapter $mediaAdapter;
    
    public function play(string $audioType, string $fileName): void {
        // Built-in support for mp3
        if (strtolower($audioType) === 'mp3') {
            echo "Playing mp3 file: $fileName\n";
        } else if (strtolower($audioType) === 'vlc' || 
                   strtolower($audioType) === 'mp4') {
            $this->mediaAdapter = new MediaAdapter();
            $this->mediaAdapter->play($audioType, $fileName);
        } else {
            echo "Invalid media. $audioType format not supported\n";
        }
    }
}

// Database Adapter Pattern
interface DatabaseInterface {
    public function connect(): void;
    public function query(string $sql, array $params = []): array;
    public function insert(string $table, array $data): int;
    public function update(string $table, array $data, array $where): int;
    public function delete(string $table, array $where): int;
    public function disconnect(): void;
}

class MySQLDatabase implements DatabaseInterface {
    private ?PDO $connection = null;
    private array $config;
    
    public function __construct(array $config) {
        $this->config = $config;
    }
    
    public function connect(): void {
        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']}";
        $this->connection = new PDO($dsn, $this->config['username'], $this->config['password']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function query(string $sql, array $params = []): array {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insert(string $table, array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);
        
        return $this->connection->lastInsertId();
    }
    
    public function update(string $table, array $data, array $where): int {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = :where_$key";
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE " . implode(' AND ', $whereClause);
        
        $stmt = $this->connection->prepare($sql);
        
        // Bind SET parameters
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        // Bind WHERE parameters
        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }
        
        $stmt->execute();
        
        return $stmt->rowCount();
    }
    
    public function delete(string $table, array $where): int {
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = :$key";
        }
        
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereClause);
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($where);
        
        return $stmt->rowCount();
    }
    
    public function disconnect(): void {
        $this->connection = null;
    }
}

class PostgreSQLDatabase implements DatabaseInterface {
    private ?PDO $connection = null;
    private array $config;
    
    public function __construct(array $config) {
        $this->config = $config;
    }
    
    public function connect(): void {
        $dsn = "pgsql:host={$this->config['host']};dbname={$this->config['database']}";
        $this->connection = new PDO($dsn, $this->config['username'], $this->config['password']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function query(string $sql, array $params = []): array {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insert(string $table, array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders) RETURNING id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);
        
        return $stmt->fetchColumn();
    }
    
    public function update(string $table, array $data, array $where): int {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = :where_$key";
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE " . implode(' AND ', $whereClause);
        
        $stmt = $this->connection->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }
        
        $stmt->execute();
        
        return $stmt->rowCount();
    }
    
    public function delete(string $table, array $where): int {
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = :$key";
        }
        
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereClause);
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($where);
        
        return $stmt->rowCount();
    }
    
    public function disconnect(): void {
        $this->connection = null;
    }
}

// Database Adapter
class DatabaseAdapter implements DatabaseInterface {
    private DatabaseInterface $database;
    
    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }
    
    public function connect(): void {
        $this->database->connect();
    }
    
    public function query(string $sql, array $params = []): array {
        return $this->database->query($sql, $params);
    }
    
    public function insert(string $table, array $data): int {
        return $this->database->insert($table, $data);
    }
    
    public function update(string $table, array $data, array $where): int {
        return $this->database->update($table, $data, $where);
    }
    
    public function delete(string $table, array $where): int {
        return $this->database->delete($table, $where);
    }
    
    public function disconnect(): void {
        $this->database->disconnect();
    }
    
    // Additional methods for logging, caching, etc.
    public function queryWithLogging(string $sql, array $params = []): array {
        echo "Executing query: $sql\n";
        $result = $this->database->query($sql, $params);
        echo "Query executed successfully\n";
        return $result;
    }
}

// Usage
// Media Player Adapter
$audioPlayer = new AudioPlayer();
$audioPlayer->play("mp3", "song.mp3");
$audioPlayer->play("mp4", "video.mp4");
$audioPlayer->play("vlc", "movie.vlc");
$audioPlayer->play("avi", "movie.avi");

// Database Adapter
$mysqlConfig = [
    'host' => 'localhost',
    'database' => 'testdb',
    'username' => 'user',
    'password' => 'pass'
];

$mysqlDb = new MySQLDatabase($mysqlConfig);
$adapter = new DatabaseAdapter($mysqlDb);

$adapter->connect();
$users = $adapter->query("SELECT * FROM users");
$adapter->disconnect();

// Switch to PostgreSQL easily
$postgresConfig = [
    'host' => 'localhost',
    'database' => 'testdb',
    'username' => 'user',
    'password' => 'pass'
];

$postgresDb = new PostgreSQLDatabase($postgresConfig);
$adapter = new DatabaseAdapter($postgresDb);

$adapter->connect();
$users = $adapter->query("SELECT * FROM users");
$adapter->disconnect();
?>
```

### Decorator Pattern
```php
<?php
interface ComponentInterface {
    public function operation(): string;
}

class ConcreteComponent implements ComponentInterface {
    public function operation(): string {
        return "ConcreteComponent";
    }
}

class Decorator implements ComponentInterface {
    protected ComponentInterface $component;
    
    public function __construct(ComponentInterface $component) {
        $this->component = $component;
    }
    
    public function operation(): string {
        return $this->component->operation();
    }
}

class ConcreteDecoratorA extends Decorator {
    public function operation(): string {
        return "ConcreteDecoratorA(" . parent::operation() . ")";
    }
}

class ConcreteDecoratorB extends Decorator {
    public function operation(): string {
        return "ConcreteDecoratorB(" . parent::operation() . ")";
    }
}

// Real-world example: Coffee ordering
interface Coffee {
    public function getCost(): float;
    public function getDescription(): string;
}

class SimpleCoffee implements Coffee {
    public function getCost(): float {
        return 2.50;
    }
    
    public function getDescription(): string {
        return "Simple coffee";
    }
}

class CoffeeDecorator implements Coffee {
    protected Coffee $coffee;
    
    public function __construct(Coffee $coffee) {
        $this->coffee = $coffee;
    }
    
    public function getCost(): float {
        return $this->coffee->getCost();
    }
    
    public function getDescription(): string {
        return $this->coffee->getDescription();
    }
}

class MilkDecorator extends CoffeeDecorator {
    public function getCost(): float {
        return parent::getCost() + 0.50;
    }
    
    public function getDescription(): string {
        return parent::getDescription() . ", milk";
    }
}

class SugarDecorator extends CoffeeDecorator {
    public function getCost(): float {
        return parent::getCost() + 0.20;
    }
    
    public function getDescription(): string {
        return parent::getDescription() . ", sugar";
    }
}

class WhippedCreamDecorator extends CoffeeDecorator {
    public function getCost(): float {
        return parent::getCost() + 0.75;
    }
    
    public function getDescription(): string {
        return parent::getDescription() . ", whipped cream";
    }
}

// Web Request Decorator
interface RequestInterface {
    public function getData(): array;
    public function getHeaders(): array;
    public function getMethod(): string;
}

class HttpRequest implements RequestInterface {
    private array $data;
    private array $headers;
    private string $method;
    
    public function __construct(string $method, array $data = [], array $headers = []) {
        $this->method = $method;
        $this->data = $data;
        $this->headers = $headers;
    }
    
    public function getData(): array {
        return $this->data;
    }
    
    public function getHeaders(): array {
        return $this->headers;
    }
    
    public function getMethod(): string {
        return $this->method;
    }
}

abstract class RequestDecorator implements RequestInterface {
    protected RequestInterface $request;
    
    public function __construct(RequestInterface $request) {
        $this->request = $request;
    }
    
    public function getData(): array {
        return $this->request->getData();
    }
    
    public function getHeaders(): array {
        return $this->request->getHeaders();
    }
    
    public function getMethod(): string {
        return $this->request->getMethod();
    }
}

class AuthenticationDecorator extends RequestDecorator {
    private string $token;
    
    public function __construct(RequestInterface $request, string $token) {
        parent::__construct($request);
        $this->token = $token;
    }
    
    public function getHeaders(): array {
        $headers = parent::getHeaders();
        $headers['Authorization'] = "Bearer {$this->token}";
        return $headers;
    }
}

class LoggingDecorator extends RequestDecorator {
    public function getData(): array {
        $data = parent::getData();
        $this->logRequest();
        return $data;
    }
    
    private function logRequest(): void {
        echo "Logging request: " . $this->request->getMethod() . " " . json_encode($this->request->getData()) . "\n";
    }
}

class ValidationDecorator extends RequestDecorator {
    private array $rules;
    
    public function __construct(RequestInterface $request, array $rules) {
        parent::__construct($request);
        $this->rules = $rules;
    }
    
    public function getData(): array {
        $data = parent::getData();
        $this->validate($data);
        return $data;
    }
    
    private function validate(array $data): void {
        foreach ($this->rules as $field => $rule) {
            if (!isset($data[$field])) {
                throw new InvalidArgumentException("Field $field is required");
            }
            
            if ($rule === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Field $field must be a valid email");
            }
        }
    }
}

class CachingDecorator extends RequestDecorator {
    private array $cache = [];
    
    public function getData(): array {
        $cacheKey = md5($this->request->getMethod() . json_encode($this->request->getData()));
        
        if (isset($this->cache[$cacheKey])) {
            echo "Returning cached response\n";
            return $this->cache[$cacheKey];
        }
        
        $data = parent::getData();
        $this->cache[$cacheKey] = $data;
        
        return $data;
    }
}

// Data Stream Decorator
interface DataSource {
    public function writeData(string $data): void;
    public function readData(): string;
}

class FileDataSource implements DataSource {
    private string $filename;
    
    public function __construct(string $filename) {
        $this->filename = $filename;
    }
    
    public function writeData(string $data): void {
        file_put_contents($this->filename, $data);
    }
    
    public function readData(): string {
        return file_get_contents($this->filename);
    }
}

abstract class DataSourceDecorator implements DataSource {
    protected DataSource $dataSource;
    
    public function __construct(DataSource $dataSource) {
        $this->dataSource = $dataSource;
    }
    
    public function writeData(string $data): void {
        $this->dataSource->writeData($data);
    }
    
    public function readData(): string {
        return $this->dataSource->readData();
    }
}

class EncryptionDecorator extends DataSourceDecorator {
    private string $key;
    
    public function __construct(DataSource $dataSource, string $key) {
        parent::__construct($dataSource);
        $this->key = $key;
    }
    
    public function writeData(string $data): void {
        $encryptedData = $this->encrypt($data);
        parent::writeData($encryptedData);
    }
    
    public function readData(): string {
        $encryptedData = parent::readData();
        return $this->decrypt($encryptedData);
    }
    
    private function encrypt(string $data): string {
        // Simple encryption for demonstration
        return base64_encode($data . $this->key);
    }
    
    private function decrypt(string $data): string {
        $decoded = base64_decode($data);
        return str_replace($this->key, '', $decoded);
    }
}

class CompressionDecorator extends DataSourceDecorator {
    public function writeData(string $data): void {
        $compressedData = gzcompress($data);
        parent::writeData($compressedData);
    }
    
    public function readData(): string {
        $compressedData = parent::readData();
        return gzuncompress($compressedData);
    }
}

// Usage
// Basic decorator example
$component = new ConcreteComponent();
$decoratorA = new ConcreteDecoratorA($component);
$decoratorB = new ConcreteDecoratorB($decoratorA);

echo $decoratorB->operation() . "\n";

// Coffee ordering example
$coffee = new SimpleCoffee();
echo $coffee->getDescription() . " costs $" . $coffee->getCost() . "\n";

$coffee = new MilkDecorator($coffee);
echo $coffee->getDescription() . " costs $" . $coffee->getCost() . "\n";

$coffee = new SugarDecorator($coffee);
echo $coffee->getDescription() . " costs $" . $coffee->getCost() . "\n";

$coffee = new WhippedCreamDecorator($coffee);
echo $coffee->getDescription() . " costs $" . $coffee->getCost() . "\n";

// Web request decorator
$request = new HttpRequest('POST', ['email' => 'user@example.com', 'name' => 'John']);

$request = new AuthenticationDecorator($request, 'jwt_token_here');
$request = new LoggingDecorator($request);
$request = new ValidationDecorator($request, ['email' => 'email', 'name' => 'string']);
$request = new CachingDecorator($request);

try {
    $data = $request->getData();
    $headers = $request->getHeaders();
    echo "Request processed successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Data stream decorator
$fileDataSource = new FileDataSource('data.txt');
$encryptedDataSource = new EncryptionDecorator($fileDataSource, 'secret_key');
$compressedEncryptedDataSource = new CompressionDecorator($encryptedDataSource);

$compressedEncryptedDataSource->writeData('This is some sensitive data');

$readData = $compressedEncryptedDataSource->readData();
echo "Read data: $readData\n";
?>
```

### Facade Pattern
```php
<?php
// Complex subsystem classes
class CPU {
    public function freeze(): void {
        echo "CPU freezing\n";
    }
    
    public function jump(string $position): void {
        echo "CPU jumping to $position\n";
    }
    
    public function execute(): void {
        echo "CPU executing\n";
    }
}

class Memory {
    public function load(long $position, string $data): void {
        echo "Memory loading data at position $position\n";
    }
}

class HardDrive {
    public function read(string $sector, string $size): string {
        echo "Hard drive reading sector $sector with size $size\n";
        return "data from sector $sector";
    }
}

class GPU {
    public function render(): void {
        echo "GPU rendering graphics\n";
    }
}

class SoundCard {
    public function playSound(string $sound): void {
        echo "Sound card playing: $sound\n";
    }
}

class PowerSupply {
    public function turnOn(): void {
        echo "Power supply turning on\n";
    }
    
    public function turnOff(): void {
        echo "Power supply turning off\n";
    }
}

// Facade class
class ComputerFacade {
    private CPU $cpu;
    private Memory $memory;
    private HardDrive $hardDrive;
    private GPU $gpu;
    private SoundCard $soundCard;
    private PowerSupply $powerSupply;
    
    public function __construct() {
        $this->cpu = new CPU();
        $this->memory = new Memory();
        $this->hardDrive = new HardDrive();
        $this->gpu = new GPU();
        $this->soundCard = new SoundCard();
        $this->powerSupply = new PowerSupply();
    }
    
    public function startComputer(): void {
        echo "Starting computer...\n";
        
        $this->powerSupply->turnOn();
        $this->cpu->freeze();
        
        $bootData = $this->hardDrive->read("BOOT", "1024");
        $this->memory->load(0, $bootData);
        
        $this->cpu->jump("0");
        $this->cpu->execute();
        
        $this->gpu->render();
        $this->soundCard->playSound("startup.wav");
        
        echo "Computer started successfully!\n";
    }
    
    public function shutdownComputer(): void {
        echo "Shutting down computer...\n";
        
        $this->cpu->freeze();
        $this->gpu->render();
        $this->soundCard->playSound("shutdown.wav");
        $this->powerSupply->turnOff();
        
        echo "Computer shut down successfully!\n";
    }
}

// E-commerce System Facade
class ProductCatalog {
    public function searchProducts(string $query): array {
        echo "Searching products with query: $query\n";
        return ['Product 1', 'Product 2', 'Product 3'];
    }
    
    public function getProductDetails(int $productId): array {
        echo "Getting details for product ID: $productId\n";
        return ['id' => $productId, 'name' => 'Product Name', 'price' => 99.99];
    }
}

class ShoppingCart {
    private array $items = [];
    
    public function addItem(int $productId, int $quantity): void {
        echo "Adding product $productId to cart with quantity $quantity\n";
        $this->items[] = ['product_id' => $productId, 'quantity' => $quantity];
    }
    
    public function removeItem(int $productId): void {
        echo "Removing product $productId from cart\n";
        $this->items = array_filter($this->items, fn($item) => $item['product_id'] !== $productId);
    }
    
    public function getItems(): array {
        return $this->items;
    }
    
    public function getTotalAmount(): float {
        echo "Calculating total amount\n";
        return 199.98; // Simplified
    }
}

class PaymentProcessor {
    public function processPayment(array $paymentDetails): bool {
        echo "Processing payment with card ending in " . substr($paymentDetails['card_number'], -4) . "\n";
        return true;
    }
}

class ShippingService {
    public function calculateShipping(array $address): float {
        echo "Calculating shipping for address\n";
        return 10.00;
    }
    
    public function scheduleDelivery(array $address): string {
        echo "Scheduling delivery for address\n";
        return 'tracking-12345';
    }
}

class InventoryManager {
    public function checkStock(int $productId, int $quantity): bool {
        echo "Checking stock for product $productId, quantity $quantity\n";
        return true;
    }
    
    public function reserveStock(int $productId, int $quantity): void {
        echo "Reserving stock for product $productId, quantity $quantity\n";
    }
    
    public function releaseStock(int $productId, int $quantity): void {
        echo "Releasing stock for product $productId, quantity $quantity\n";
    }
}

class OrderManager {
    public function createOrder(array $items, array $customerInfo): int {
        echo "Creating order for customer\n";
        return 12345;
    }
    
    public function updateOrderStatus(int $orderId, string $status): void {
        echo "Updating order $orderId status to $status\n";
    }
    
    public function getOrderDetails(int $orderId): array {
        echo "Getting details for order $orderId\n";
        return ['id' => $orderId, 'status' => 'pending', 'total' => 209.98];
    }
}

class NotificationService {
    public function sendEmail(string $to, string $subject, string $message): void {
        echo "Sending email to $to with subject: $subject\n";
    }
    
    public function sendSMS(string $to, string $message): void {
        echo "Sending SMS to $to: $message\n";
    }
}

// E-commerce Facade
class ECommerceFacade {
    private ProductCatalog $catalog;
    private ShoppingCart $cart;
    private PaymentProcessor $paymentProcessor;
    private ShippingService $shippingService;
    private InventoryManager $inventoryManager;
    private OrderManager $orderManager;
    private NotificationService $notificationService;
    
    public function __construct() {
        $this->catalog = new ProductCatalog();
        $this->cart = new ShoppingCart();
        $this->paymentProcessor = new PaymentProcessor();
        $this->shippingService = new ShippingService();
        $this->inventoryManager = new InventoryManager();
        $this->orderManager = new OrderManager();
        $this->notificationService = new NotificationService();
    }
    
    public function searchAndAddToCart(string $query, int $productId, int $quantity): array {
        // Search products
        $products = $this->catalog->searchProducts($query);
        
        // Check stock
        if (!$this->inventoryManager->checkStock($productId, $quantity)) {
            throw new Exception("Product not available in requested quantity");
        }
        
        // Add to cart
        $this->cart->addItem($productId, $quantity);
        $this->inventoryManager->reserveStock($productId, $quantity);
        
        return $products;
    }
    
    public function checkout(array $paymentDetails, array $shippingAddress): array {
        // Calculate totals
        $subtotal = $this->cart->getTotalAmount();
        $shippingCost = $this->shippingService->calculateShipping($shippingAddress);
        $total = $subtotal + $shippingCost;
        
        // Process payment
        if (!$this->paymentProcessor->processPayment($paymentDetails)) {
            throw new Exception("Payment failed");
        }
        
        // Create order
        $orderItems = $this->cart->getItems();
        $customerInfo = [
            'shipping_address' => $shippingAddress,
            'payment_details' => $paymentDetails
        ];
        
        $orderId = $this->orderManager->createOrder($orderItems, $customerInfo);
        
        // Schedule delivery
        $trackingNumber = $this->shippingService->scheduleDelivery($shippingAddress);
        
        // Update order status
        $this->orderManager->updateOrderStatus($orderId, 'confirmed');
        
        // Send notifications
        $this->notificationService->sendEmail(
            $customerInfo['email'] ?? 'customer@example.com',
            'Order Confirmed',
            "Your order #$orderId has been confirmed. Tracking: $trackingNumber"
        );
        
        // Clear cart
        $this->cart = new ShoppingCart();
        
        return [
            'order_id' => $orderId,
            'tracking_number' => $trackingNumber,
            'total' => $total
        ];
    }
    
    public function getOrderStatus(int $orderId): array {
        return $this->orderManager->getOrderDetails($orderId);
    }
}

// File System Facade
class FileReader {
    public function readText(string $filename): string {
        echo "Reading text file: $filename\n";
        return "Content of $filename";
    }
    
    public function readBinary(string $filename): string {
        echo "Reading binary file: $filename\n";
        return "Binary content of $filename";
    }
}

class FileWriter {
    public function writeText(string $filename, string $content): void {
        echo "Writing text to file: $filename\n";
    }
    
    public function writeBinary(string $filename, string $content): void {
        echo "Writing binary to file: $filename\n";
    }
}

class FileCompressor {
    public function compress(string $content): string {
        echo "Compressing content\n";
        return gzcompress($content);
    }
    
    public function decompress(string $content): string {
        echo "Decompressing content\n";
        return gzuncompress($content);
    }
}

class FileEncryptor {
    public function encrypt(string $content, string $key): string {
        echo "Encrypting content\n";
        return base64_encode($content . $key);
    }
    
    public function decrypt(string $content, string $key): string {
        echo "Decrypting content\n";
        $decoded = base64_decode($content);
        return str_replace($key, '', $decoded);
    }
}

class FileSystemFacade {
    private FileReader $reader;
    private FileWriter $writer;
    private FileCompressor $compressor;
    private FileEncryptor $encryptor;
    
    public function __construct() {
        $this->reader = new FileReader();
        $this->writer = new FileWriter();
        $this->compressor = new FileCompressor();
        $this->encryptor = new FileEncryptor();
    }
    
    public function saveEncryptedCompressedFile(string $filename, string $content, string $key): void {
        $compressed = $this->compressor->compress($content);
        $encrypted = $this->encryptor->encrypt($compressed, $key);
        $this->writer->writeBinary($filename, $encrypted);
    }
    
    public function loadEncryptedCompressedFile(string $filename, string $key): string {
        $encrypted = $this->reader->readBinary($filename);
        $decrypted = $this->encryptor->decrypt($encrypted, $key);
        return $this->compressor->decompress($decrypted);
    }
    
    public function saveSimpleTextFile(string $filename, string $content): void {
        $this->writer->writeText($filename, $content);
    }
    
    public function loadSimpleTextFile(string $filename): string {
        return $this->reader->readText($filename);
    }
}

// Usage
// Computer Facade
$computer = new ComputerFacade();
$computer->startComputer();
$computer->shutdownComputer();

// E-commerce Facade
$ecommerce = new ECommerceFacade();

// Search and add products
$products = $ecommerce->searchAndAddToCart('laptop', 1, 1);

// Checkout
$paymentDetails = [
    'card_number' => '1234567890123456',
    'expiry' => '12/24',
    'cvv' => '123'
];

$shippingAddress = [
    'street' => '123 Main St',
    'city' => 'New York',
    'country' => 'USA'
];

$orderResult = $ecommerce->checkout($paymentDetails, $shippingAddress);
echo "Order placed: #" . $orderResult['order_id'] . "\n";

// File System Facade
$fileSystem = new FileSystemFacade();
$fileSystem->saveSimpleTextFile('test.txt', 'Hello, World!');
$content = $fileSystem->loadSimpleTextFile('test.txt');
echo "File content: $content\n";

$fileSystem->saveEncryptedCompressedFile('secret.dat', 'Secret message', 'key123');
$secretContent = $fileSystem->loadEncryptedCompressedFile('secret.dat', 'key123');
echo "Secret content: $secretContent\n";
?>
```

## Behavioral Patterns

### Observer Pattern
```php
<?php
interface Observer {
    public function update(string $event, array $data = []): void;
}

interface Subject {
    public function attach(Observer $observer): void;
    public function detach(Observer $observer): void;
    public function notify(string $event, array $data = []): void;
}

class WeatherStation implements Subject {
    private array $observers = [];
    private float $temperature;
    private float $humidity;
    private float $pressure;
    
    public function __construct(float $temperature = 20.0, float $humidity = 50.0, float $pressure = 1013.25) {
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->pressure = $pressure;
    }
    
    public function attach(Observer $observer): void {
        $this->observers[] = $observer;
    }
    
    public function detach(Observer $observer): void {
        $this->observers = array_filter($this->observers, fn($obs) => $obs !== $observer);
    }
    
    public function notify(string $event, array $data = []): void {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
    
    public function setTemperature(float $temperature): void {
        $oldTemp = $this->temperature;
        $this->temperature = $temperature;
        
        $this->notify('temperature_changed', [
            'old_temperature' => $oldTemp,
            'new_temperature' => $temperature
        ]);
    }
    
    public function setHumidity(float $humidity): void {
        $oldHumidity = $this->humidity;
        $this->humidity = $humidity;
        
        $this->notify('humidity_changed', [
            'old_humidity' => $oldHumidity,
            'new_humidity' => $humidity
        ]);
    }
    
    public function setPressure(float $pressure): void {
        $oldPressure = $this->pressure;
        $this->pressure = $pressure;
        
        $this->notify('pressure_changed', [
            'old_pressure' => $oldPressure,
            'new_pressure' => $pressure
        ]);
    }
    
    public function getTemperature(): float {
        return $this->temperature;
    }
    
    public function getHumidity(): float {
        return $this->humidity;
    }
    
    public function getPressure(): float {
        return $this->pressure;
    }
}

class TemperatureDisplay implements Observer {
    public function update(string $event, array $data = []): void {
        if ($event === 'temperature_changed') {
            echo "Temperature Display: Temperature changed from {$data['old_temperature']}°C to {$data['new_temperature']}°C\n";
        }
    }
}

class HumidityDisplay implements Observer {
    public function update(string $event, array $data = []): void {
        if ($event === 'humidity_changed') {
            echo "Humidity Display: Humidity changed from {$data['old_humidity']}% to {$data['new_humidity']}%\n";
        }
    }
}

class WeatherLogger implements Observer {
    public function update(string $event, array $data = []): void {
        $timestamp = date('Y-m-d H:i:s');
        echo "Weather Logger [$timestamp]: $event - " . json_encode($data) . "\n";
    }
}

class WeatherAlert implements Observer {
    private float $maxTemperature;
    private float $minTemperature;
    
    public function __construct(float $maxTemperature = 30.0, float $minTemperature = 10.0) {
        $this->maxTemperature = $maxTemperature;
        $this->minTemperature = $minTemperature;
    }
    
    public function update(string $event, array $data = []): void {
        if ($event === 'temperature_changed') {
            $newTemp = $data['new_temperature'];
            
            if ($newTemp > $this->maxTemperature) {
                echo "ALERT: High temperature! {$newTemp}°C exceeds maximum of {$this->maxTemperature}°C\n";
            } elseif ($newTemp < $this->minTemperature) {
                echo "ALERT: Low temperature! {$newTemp}°C below minimum of {$this->minTemperature}°C\n";
            }
        }
    }
}

// Event System
class EventManager implements Subject {
    private array $observers = [];
    private static ?self $instance = null;
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function attach(Observer $observer): void {
        $this->observers[] = $observer;
    }
    
    public function detach(Observer $observer): void {
        $this->observers = array_filter($this->observers, fn($obs) => $obs !== $observer);
    }
    
    public function notify(string $event, array $data = []): void {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
    
    public function trigger(string $event, array $data = []): void {
        $this->notify($event, $data);
    }
}

class User implements Observer {
    private string $name;
    private array $notifications = [];
    
    public function __construct(string $name) {
        $this->name = $name;
    }
    
    public function update(string $event, array $data = []): void {
        $this->notifications[] = [
            'event' => $event,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo "{$this->name} received notification: $event\n";
    }
    
    public function getNotifications(): array {
        return $this->notifications;
    }
}

class EmailNotifier implements Observer {
    public function update(string $event, array $data = []): void {
        echo "Sending email notification for event: $event\n";
        echo "To: {$data['email']}\n";
        echo "Subject: New Event: $event\n";
        echo "Message: " . json_encode($data) . "\n\n";
    }
}

class SMSNotifier implements Observer {
    public function update(string $event, array $data = []): void {
        echo "Sending SMS notification for event: $event\n";
        echo "To: {$data['phone']}\n";
        echo "Message: Event occurred: $event\n\n";
    }
}

// Stock Market Observer
class StockMarket implements Subject {
    private array $observers = [];
    private array $stocks = [];
    
    public function attach(Observer $observer): void {
        $this->observers[] = $observer;
    }
    
    public function detach(Observer $observer): void {
        $this->observers = array_filter($this->observers, fn($obs) => $obs !== $observer);
    }
    
    public function notify(string $event, array $data = []): void {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
    
    public function updateStockPrice(string $symbol, float $newPrice): void {
        $oldPrice = $this->stocks[$symbol] ?? 0;
        $this->stocks[$symbol] = $newPrice;
        
        $this->notify('stock_price_updated', [
            'symbol' => $symbol,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'change' => $newPrice - $oldPrice,
            'change_percent' => $oldPrice > 0 ? (($newPrice - $oldPrice) / $oldPrice) * 100 : 0
        ]);
    }
    
    public function getStockPrice(string $symbol): float {
        return $this->stocks[$symbol] ?? 0;
    }
}

class StockTrader implements Observer {
    private string $name;
    private array $portfolio = [];
    
    public function __construct(string $name) {
        $this->name = $name;
    }
    
    public function update(string $event, array $data = []): void {
        if ($event === 'stock_price_updated') {
            $symbol = $data['symbol'];
            $newPrice = $data['new_price'];
            $changePercent = $data['change_percent'];
            
            echo "{$this->name} watching $symbol: Price changed by {$changePercent}%\n";
            
            // Simple trading logic
            if ($changePercent > 5 && isset($this->portfolio[$symbol])) {
                echo "{$this->name}: Selling $symbol - price increased significantly\n";
                unset($this->portfolio[$symbol]);
            } elseif ($changePercent < -5 && !isset($this->portfolio[$symbol])) {
                echo "{$this->name}: Buying $symbol - price dropped significantly\n";
                $this->portfolio[$symbol] = $newPrice;
            }
        }
    }
    
    public function getPortfolio(): array {
        return $this->portfolio;
    }
}

class StockAnalyst implements Observer {
    private array $analysis = [];
    
    public function update(string $event, array $data = []): void {
        if ($event === 'stock_price_updated') {
            $symbol = $data['symbol'];
            $changePercent = $data['change_percent'];
            
            if (!isset($this->analysis[$symbol])) {
                $this->analysis[$symbol] = ['updates' => 0, 'total_change' => 0];
            }
            
            $this->analysis[$symbol]['updates']++;
            $this->analysis[$symbol]['total_change'] += $changePercent;
            $this->analysis[$symbol]['avg_change'] = $this->analysis[$symbol]['total_change'] / $this->analysis[$symbol]['updates'];
            
            echo "Analyst: $symbol analysis updated - Average change: {$this->analysis[$symbol]['avg_change']}%\n";
        }
    }
    
    public function getAnalysis(): array {
        return $this->analysis;
    }
}

// Usage
// Weather Station Example
$weatherStation = new WeatherStation();

$temperatureDisplay = new TemperatureDisplay();
$humidityDisplay = new HumidityDisplay();
$weatherLogger = new WeatherLogger();
$weatherAlert = new WeatherAlert();

$weatherStation->attach($temperatureDisplay);
$weatherStation->attach($humidityDisplay);
$weatherStation->attach($weatherLogger);
$weatherStation->attach($weatherAlert);

echo "=== Weather Station Updates ===\n";
$weatherStation->setTemperature(25.5);
$weatherStation->setHumidity(65.0);
$weatherStation->setTemperature(35.0); // Should trigger alert

// Event System Example
echo "\n=== Event System ===\n";
$eventManager = EventManager::getInstance();

$user1 = new User('Alice');
$user2 = new User('Bob');
$emailNotifier = new EmailNotifier();
$smsNotifier = new SMSNotifier();

$eventManager->attach($user1);
$eventManager->attach($user2);
$eventManager->attach($emailNotifier);
$eventManager->attach($smsNotifier);

$eventManager->trigger('user_registered', [
    'email' => 'alice@example.com',
    'phone' => '+1234567890',
    'user_id' => 1
]);

$eventManager->trigger('order_placed', [
    'email' => 'bob@example.com',
    'order_id' => 12345,
    'total' => 99.99
]);

// Stock Market Example
echo "\n=== Stock Market Updates ===\n";
$stockMarket = new StockMarket();

$trader1 = new StockTrader('Trader Joe');
$trader2 = new StockTrader('Trader Jane');
$analyst = new StockAnalyst();

$stockMarket->attach($trader1);
$stockMarket->attach($trader2);
$stockMarket->attach($analyst);

$stockMarket->updateStockPrice('AAPL', 150.00);
$stockMarket->updateStockPrice('AAPL', 158.00); // +5.33% - might trigger sell
$stockMarket->updateStockPrice('GOOGL', 2800.00);
$stockMarket->updateStockPrice('GOOGL', 2640.00); // -5.71% - might trigger buy
?>
```

## Summary

PHP Design Patterns provide:

**Creational Patterns:**
- Singleton: Ensure single instance of classes
- Factory: Create objects without specifying exact classes
- Builder: Construct complex objects step by step
- Prototype: Create new objects by copying existing ones

**Structural Patterns:**
- Adapter: Make incompatible interfaces work together
- Decorator: Add functionality to objects dynamically
- Facade: Provide simplified interface to complex systems

**Behavioral Patterns:**
- Observer: Define one-to-many dependency between objects

**Key Benefits:**
- Code reusability and maintainability
- Loose coupling between components
- Easier testing and debugging
- Clear separation of concerns
- Scalable architecture

**Implementation Considerations:**
- Choose appropriate patterns for specific problems
- Avoid over-engineering simple solutions
- Consider performance implications
- Follow SOLID principles
- Document pattern usage clearly

Design patterns provide proven solutions to common programming problems and help create more maintainable, scalable, and robust PHP applications.
