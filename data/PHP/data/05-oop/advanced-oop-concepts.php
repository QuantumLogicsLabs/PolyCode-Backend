<?php
/**
 * Advanced OOP Concepts in PHP
 * 
 * Exploring advanced object-oriented programming concepts including
 * abstract classes, interfaces, traits, namespaces, and design patterns.
 */

echo "=== Advanced OOP Concepts ===\n\n";

// Abstract Classes
echo "--- Abstract Classes ---\n";

abstract class Shape {
    protected $color;
    
    public function __construct($color = 'black') {
        $this->color = $color;
    }
    
    abstract public function calculateArea();
    abstract public function calculatePerimeter();
    
    public function getColor() {
        return $this->color;
    }
    
    public function setColor($color) {
        $this->color = $color;
    }
    
    public function describe() {
        return "A {$this->color} shape with area " . $this->calculateArea() . 
               " and perimeter " . $this->calculatePerimeter();
    }
}

class Circle extends Shape {
    private $radius;
    
    public function __construct($radius, $color = 'black') {
        parent::__construct($color);
        $this->radius = $radius;
    }
    
    public function calculateArea() {
        return pi() * $this->radius * $this->radius;
    }
    
    public function calculatePerimeter() {
        return 2 * pi() * $this->radius;
    }
    
    public function getRadius() {
        return $this->radius;
    }
}

class Rectangle extends Shape {
    private $width;
    private $height;
    
    public function __construct($width, $height, $color = 'black') {
        parent::__construct($color);
        $this->width = $width;
        $this->height = $height;
    }
    
    public function calculateArea() {
        return $this->width * $this->height;
    }
    
    public function calculatePerimeter() {
        return 2 * ($this->width + $this->height);
    }
    
    public function getDimensions() {
        return [$this->width, $this->height];
    }
}

$circle = new Circle(5, 'red');
$rectangle = new Rectangle(4, 6, 'blue');

echo "Circle: " . $circle->describe() . "\n";
echo "Rectangle: " . $rectangle->describe() . "\n\n";

// Interfaces
echo "--- Interfaces ---\n";

interface Drawable {
    public function draw();
    public function getCanvas();
}

interface Resizable {
    public function resize($factor);
    public function getSize();
}

interface Movable {
    public function move($x, $y);
    public function getPosition();
}

class Canvas {
    private $width;
    private $height;
    
    public function __construct($width = 800, $height = 600) {
        $this->width = $width;
        $this->height = $height;
    }
    
    public function getDimensions() {
        return ['width' => $this->width, 'height' => $this->height];
    }
    
    public function render() {
        echo "Rendering canvas: {$this->width}x{$this->height}\n";
    }
}

class GraphicCircle extends Circle implements Drawable, Resizable, Movable {
    private $x = 0;
    private $y = 0;
    private $canvas;
    
    public function __construct($radius, $color = 'black', Canvas $canvas = null) {
        parent::__construct($radius, $color);
        $this->canvas = $canvas ?: new Canvas();
    }
    
    public function draw() {
        echo "Drawing a {$this->color} circle at ({$this->x}, {$this->y}) with radius {$this->radius}\n";
    }
    
    public function getCanvas() {
        return $this->canvas;
    }
    
    public function resize($factor) {
        $this->radius *= $factor;
        echo "Resized circle by factor $factor, new radius: {$this->radius}\n";
    }
    
    public function getSize() {
        return $this->radius;
    }
    
    public function move($x, $y) {
        $this->x = $x;
        $this->y = $y;
        echo "Moved circle to ($x, $y)\n";
    }
    
    public function getPosition() {
        return ['x' => $this->x, 'y' => $this->y];
    }
}

$graphicCircle = new GraphicCircle(10, 'green');
$graphicCircle->draw();
$graphicCircle->move(100, 150);
$graphicCircle->resize(1.5);
$graphicCircle->draw();
echo "\n";

// Traits
echo "--- Traits ---\n";

trait Loggable {
    private $logs = [];
    
    public function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $this->logs[] = "[$timestamp] $message";
    }
    
    public function getLogs() {
        return $this->logs;
    }
    
    public function clearLogs() {
        $this->logs = [];
    }
}

trait Timestampable {
    private $createdAt;
    private $updatedAt;
    
    public function setCreatedAt($timestamp = null) {
        $this->createdAt = $timestamp ?: date('Y-m-d H:i:s');
    }
    
    public function setUpdatedAt($timestamp = null) {
        $this->updatedAt = $timestamp ?: date('Y-m-d H:i:s');
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    public function touch() {
        $this->setUpdatedAt();
    }
}

trait Serializable {
    public function toArray() {
        $data = [];
        foreach ($this as $key => $value) {
            if (!is_object($value) || method_exists($value, 'toArray')) {
                $data[$key] = is_object($value) ? $value->toArray() : $value;
            }
        }
        return $data;
    }
    
    public function toJson() {
        return json_encode($this->toArray());
    }
}

class User {
    use Loggable, Timestampable, Serializable;
    
    private $id;
    private $name;
    private $email;
    
    public function __construct($id, $name, $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->setCreatedAt();
        $this->log("User created: $name");
    }
    
    public function updateName($newName) {
        $this->name = $newName;
        $this->touch();
        $this->log("User name updated to: $newName");
    }
    
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { 
        $this->email = $email; 
        $this->touch();
        $this->log("Email updated to: $email");
    }
}

$user = new User(1, 'John Doe', 'john@example.com');
$user->updateName('John Smith');
$user->setEmail('john.smith@example.com');

echo "User logs:\n";
foreach ($user->getLogs() as $log) {
    echo "  $log\n";
}

echo "\nUser as array:\n";
print_r($user->toArray());

echo "\nUser as JSON:\n";
echo $user->toJson() . "\n\n";

// Namespaces and Autoloading
echo "--- Namespaces ---\n";

namespace App\Models {
    class Product {
        private $id;
        private $name;
        private $price;
        
        public function __construct($id, $name, $price) {
            $this->id = $id;
            $this->name = $name;
            $this->price = $price;
        }
        
        public function getDetails() {
            return "Product: {$this->name}, Price: \${$this->price}";
        }
    }
}

namespace App\Services {
    class ProductService {
        private $products = [];
        
        public function addProduct($product) {
            $this->products[] = $product;
        }
        
        public function getProductCount() {
            return count($this->products);
        }
        
        public function getTotalValue() {
            $total = 0;
            foreach ($this->products as $product) {
                $total += $product->price;
            }
            return $total;
        }
    }
}

namespace {
    use App\Models\Product;
    use App\Services\ProductService;
    
    $product1 = new Product(1, 'Laptop', 999.99);
    $product2 = new Product(2, 'Mouse', 25.50);
    
    $service = new ProductService();
    $service->addProduct($product1);
    $service->addProduct($product2);
    
    echo "Product count: " . $service->getProductCount() . "\n";
    echo "Total value: $" . $service->getTotalValue() . "\n\n";
}

// Static Methods and Properties
echo "--- Static Methods and Properties ---\n";

class Database {
    private static $instance = null;
    private static $connectionCount = 0;
    private $connection;
    
    private function __construct() {
        $this->connection = "Database connection #" . ++self::$connectionCount;
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public static function getConnectionCount() {
        return self::$connectionCount;
    }
    
    public static function reset() {
        self::$instance = null;
        self::$connectionCount = 0;
    }
}

// Singleton pattern
$db1 = Database::getInstance();
$db2 = Database::getInstance();

echo "Database 1: " . $db1->getConnection() . "\n";
echo "Database 2: " . $db2->getConnection() . "\n";
echo "Same instance: " . ($db1 === $db2 ? 'Yes' : 'No') . "\n";
echo "Connection count: " . Database::getConnectionCount() . "\n\n";

// Magic Methods
echo "--- Magic Methods ---\n";

class MagicObject {
    private $data = [];
    private $hidden = ['password', 'secret'];
    
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
    }
    
    public function __get($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }
    
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }
    
    public function __isset($name) {
        return isset($this->data[$name]);
    }
    
    public function __unset($name) {
        unset($this->data[$name]);
    }
    
    public function __call($name, $arguments) {
        if (strpos($name, 'get') === 0) {
            $property = strtolower(substr($name, 3));
            return $this->data[$property] ?? null;
        }
        
        if (strpos($name, 'set') === 0) {
            $property = strtolower(substr($name, 3));
            $this->data[$property] = $arguments[0] ?? null;
            return $this;
        }
        
        throw new \BadMethodCallException("Method $name does not exist");
    }
    
    public static function __callStatic($name, $arguments) {
        if ($name === 'create') {
            return new self($arguments[0] ?? []);
        }
        
        throw new \BadMethodCallException("Static method $name does not exist");
    }
    
    public function __toString() {
        return json_encode($this->data);
    }
    
    public function __invoke($key) {
        return $this->data[$key] ?? null;
    }
    
    public function __sleep() {
        return array_diff(array_keys($this->data), $this->hidden);
    }
    
    public function __wakeup() {
        echo "Object unserialized\n";
    }
    
    public function __clone() {
        echo "Object cloned\n";
    }
}

$magic = MagicObject::create(['name' => 'John', 'age' => 30, 'password' => 'secret']);

echo "Name: " . $magic->name . "\n";
echo "Age: " . $magic->getage() . "\n";

$magic->setemail('john@example.com');
echo "Email: " . $magic->email . "\n";

echo "Has password: " . (isset($magic->password) ? 'Yes' : 'No') . "\n";
unset($magic->password);
echo "Has password after unset: " . (isset($magic->password) ? 'Yes' : 'No') . "\n";

echo "Object as string: $magic\n";
echo "Invoke method: " . $magic('name') . "\n";

$cloned = clone $magic;
echo "\n";

// Design Patterns
echo "--- Design Patterns ---\n";

// Factory Pattern
abstract class VehicleFactory {
    abstract public function createVehicle($type);
    
    public function deliverVehicle($type) {
        $vehicle = $this->createVehicle($type);
        $vehicle->startEngine();
        $vehicle->drive();
        return $vehicle;
    }
}

class CarFactory extends VehicleFactory {
    public function createVehicle($type) {
        switch ($type) {
            case 'sedan':
                return new Sedan();
            case 'suv':
                return new SUV();
            default:
                throw new \Exception("Unknown car type: $type");
        }
    }
}

interface Vehicle {
    public function startEngine();
    public function drive();
}

class Sedan implements Vehicle {
    public function startEngine() {
        echo "Sedan engine started\n";
    }
    
    public function drive() {
        echo "Sedan is driving\n";
    }
}

class SUV implements Vehicle {
    public function startEngine() {
        echo "SUV engine started\n";
    }
    
    public function drive() {
        echo "SUV is driving\n";
    }
}

$carFactory = new CarFactory();
$sedan = $carFactory->deliverVehicle('sedan');
$suv = $carFactory->deliverVehicle('suv');

// Observer Pattern
interface Observer {
    public function update($event, $data);
}

interface Subject {
    public function attach(Observer $observer);
    public function detach(Observer $observer);
    public function notify($event, $data);
}

class WeatherStation implements Subject {
    private $observers = [];
    private $temperature;
    private $humidity;
    
    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }
    
    public function detach(Observer $observer) {
        $this->observers = array_filter($this->observers, function($obs) use ($observer) {
            return $obs !== $observer;
        });
    }
    
    public function notify($event, $data) {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
    
    public function setMeasurements($temperature, $humidity) {
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->notify('measurement', [
            'temperature' => $temperature,
            'humidity' => $humidity
        ]);
    }
}

class WeatherDisplay implements Observer {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function update($event, $data) {
        if ($event === 'measurement') {
            echo "{$this->name} - Temperature: {$data['temperature']}°C, Humidity: {$data['humidity']}%\n";
        }
    }
}

$weatherStation = new WeatherStation();
$display1 = new WeatherDisplay('Display 1');
$display2 = new WeatherDisplay('Display 2');

$weatherStation->attach($display1);
$weatherStation->attach($display2);
$weatherStation->setMeasurements(25, 60);
$weatherStation->setMeasurements(22, 65);

// Strategy Pattern
interface PaymentStrategy {
    public function pay($amount);
}

class CreditCardPayment implements PaymentStrategy {
    private $cardNumber;
    
    public function __construct($cardNumber) {
        $this->cardNumber = $cardNumber;
    }
    
    public function pay($amount) {
        echo "Paid $amount using Credit Card ending in " . substr($this->cardNumber, -4) . "\n";
    }
}

class PayPalPayment implements PaymentStrategy {
    private $email;
    
    public function __construct($email) {
        $this->email = $email;
    }
    
    public function pay($amount) {
        echo "Paid $amount using PayPal account {$this->email}\n";
    }
}

class ShoppingCart {
    private $paymentStrategy;
    private $amount;
    
    public function __construct($amount) {
        $this->amount = $amount;
    }
    
    public function setPaymentStrategy(PaymentStrategy $strategy) {
        $this->paymentStrategy = $strategy;
    }
    
    public function checkout() {
        if ($this->paymentStrategy) {
            $this->paymentStrategy->pay($this->amount);
        } else {
            echo "No payment strategy set\n";
        }
    }
}

$cart = new ShoppingCart(100);
$cart->setPaymentStrategy(new CreditCardPayment('1234567890123456'));
$cart->checkout();

$cart->setPaymentStrategy(new PayPalPayment('user@example.com'));
$cart->checkout();

echo "\n";

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: Dependency Injection Container
echo "Example 1: Dependency Injection Container\n";
class Container {
    private $bindings = [];
    private $instances = [];
    
    public function bind($abstract, $concrete = null) {
        $this->bindings[$abstract] = $concrete ?: $abstract;
    }
    
    public function make($abstract) {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        
        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("No binding for $abstract");
        }
        
        $concrete = $this->bindings[$abstract];
        
        if ($concrete === $abstract) {
            $instance = new $concrete();
        } else {
            $instance = is_callable($concrete) ? $concrete($this) : new $concrete();
        }
        
        $this->instances[$abstract] = $instance;
        return $instance;
    }
}

interface LoggerInterface {
    public function log($message);
}

class FileLogger implements LoggerInterface {
    public function log($message) {
        echo "File logger: $message\n";
    }
}

class DatabaseLogger implements LoggerInterface {
    public function log($message) {
        echo "Database logger: $message\n";
    }
}

class UserService {
    private $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function createUser($name) {
        $this->logger->log("User created: $name");
        return "User $name created";
    }
}

$container = new Container();
$container->bind(LoggerInterface::class, FileLogger::class);
$container->bind(UserService::class, function($container) {
    return new UserService($container->make(LoggerInterface::class));
});

$userService = $container->make(UserService::class);
echo $userService->createUser('John') . "\n";

// Switch logger implementation
$container->bind(LoggerInterface::class, DatabaseLogger::class);
$userService2 = $container->make(UserService::class);
echo $userService2->createUser('Jane') . "\n\n";

// Example 2: Event System
echo "Example 2: Event System\n";
class EventDispatcher {
    private $listeners = [];
    
    public function listen($event, $listener) {
        $this->listeners[$event][] = $listener;
    }
    
    public function dispatch($event, $data = null) {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                $listener($data);
            }
        }
    }
}

class User {
    public $name;
    public $email;
    
    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }
}

$dispatcher = new EventDispatcher();

$dispatcher->listen('user.created', function($user) {
    echo "Sending welcome email to {$user->email}\n";
});

$dispatcher->listen('user.created', function($user) {
    echo "Creating user profile for {$user->name}\n";
});

$dispatcher->listen('user.deleted', function($user) {
    echo "Cleaning up data for user {$user->name}\n";
});

$user = new User('Alice', 'alice@example.com');
$dispatcher->dispatch('user.created', $user);
$dispatcher->dispatch('user.deleted', $user);

echo "\n";

// Example 3: Repository Pattern
echo "Example 3: Repository Pattern\n";
interface RepositoryInterface {
    public function find($id);
    public function findAll();
    public function save($entity);
    public function delete($id);
}

class InMemoryRepository implements RepositoryInterface {
    private $entities = [];
    private $nextId = 1;
    
    public function find($id) {
        return $this->entities[$id] ?? null;
    }
    
    public function findAll() {
        return $this->entities;
    }
    
    public function save($entity) {
        if (!isset($entity->id)) {
            $entity->id = $this->nextId++;
        }
        $this->entities[$entity->id] = $entity;
        return $entity;
    }
    
    public function delete($id) {
        unset($this->entities[$id]);
    }
}

class Product {
    public $id;
    public $name;
    public $price;
    
    public function __construct($name, $price) {
        $this->name = $name;
        $this->price = $price;
    }
}

$repository = new InMemoryRepository();

$product1 = new Product('Laptop', 999);
$product2 = new Product('Mouse', 25);

$saved1 = $repository->save($product1);
$saved2 = $repository->save($product2);

echo "Saved products:\n";
foreach ($repository->findAll() as $product) {
    echo "ID: {$product->id}, Name: {$product->name}, Price: \${$product->price}\n";
}

$found = $repository->find(1);
echo "\nFound product: " . ($found ? $found->name : 'Not found') . "\n";

$repository->delete(2);
echo "Products after deletion:\n";
foreach ($repository->findAll() as $product) {
    echo "ID: {$product->id}, Name: {$product->name}\n";
}

echo "\n";

echo "=== End of Advanced OOP Concepts ===\n";
?>
