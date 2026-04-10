# PHP Object-Oriented Programming (OOP)

## Introduction to OOP in PHP

Object-Oriented Programming (OOP) is a paradigm that organizes code into **objects** — self-contained units that combine data (properties) and behavior (methods). PHP has supported OOP since PHP 3, with major improvements in PHP 5, 7, and 8.

OOP's four pillars:
1. **Encapsulation** — bundling data and methods, restricting direct access.
2. **Inheritance** — a class can extend another, reusing and overriding its behavior.
3. **Polymorphism** — objects of different classes can be used through a common interface.
4. **Abstraction** — hiding complex implementation details behind simple interfaces.

---

## Classes and Objects

### Defining a Class

```php
<?php
class Car {
    // Properties (attributes)
    public string $make;
    public string $model;
    public int $year;
    private int $mileage = 0;

    // Constructor — called when object is created
    public function __construct(string $make, string $model, int $year) {
        $this->make  = $make;
        $this->model = $model;
        $this->year  = $year;
    }

    // Methods (behaviors)
    public function drive(int $km): void {
        $this->mileage += $km;
        echo "{$this->make} {$this->model} drove {$km}km.\n";
    }

    public function getMileage(): int {
        return $this->mileage;
    }

    public function getInfo(): string {
        return "{$this->year} {$this->make} {$this->model}";
    }
}

// Creating objects (instances)
$car1 = new Car("Toyota", "Corolla", 2022);
$car2 = new Car("Honda", "Civic", 2023);

$car1->drive(150);
echo $car1->getInfo();     // 2022 Toyota Corolla
echo $car1->getMileage();  // 150
?>
```

---

## Access Modifiers

| Modifier    | Accessible From                  |
|-------------|----------------------------------|
| `public`    | Anywhere                         |
| `protected` | Class itself and subclasses      |
| `private`   | Only within the defining class   |

```php
<?php
class BankAccount {
    private float $balance;
    protected string $owner;

    public function __construct(string $owner, float $initialBalance) {
        $this->owner   = $owner;
        $this->balance = $initialBalance;
    }

    public function deposit(float $amount): void {
        if ($amount > 0) {
            $this->balance += $amount;
        }
    }

    public function withdraw(float $amount): bool {
        if ($amount <= $this->balance) {
            $this->balance -= $amount;
            return true;
        }
        return false;
    }

    public function getBalance(): float {
        return $this->balance;
    }
}

$account = new BankAccount("Alice", 1000.00);
$account->deposit(500);
$account->withdraw(200);
echo $account->getBalance(); // 1300
// echo $account->balance; // Fatal Error: private property
?>
```

---

## Constructor Property Promotion (PHP 8+)

Shorthand syntax to declare and assign properties directly in the constructor.

```php
<?php
class Product {
    public function __construct(
        public readonly string $name,
        public readonly float  $price,
        private int            $stock = 0,
    ) {}

    public function addStock(int $qty): void {
        $this->stock += $qty;
    }

    public function getStock(): int {
        return $this->stock;
    }
}

$p = new Product("Laptop", 75000.00, 10);
echo $p->name;       // Laptop
echo $p->price;      // 75000
echo $p->getStock(); // 10
// $p->name = "PC";  // Error: readonly property
?>
```

---

## Static Properties and Methods

Belong to the class itself, not to any instance.

```php
<?php
class Counter {
    private static int $count = 0;

    public static function increment(): void {
        self::$count++;
    }

    public static function getCount(): int {
        return self::$count;
    }
}

Counter::increment();
Counter::increment();
Counter::increment();
echo Counter::getCount(); // 3
?>
```

### Singleton Pattern (using static)

```php
<?php
class Database {
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct() {
        $this->pdo = new \PDO("sqlite::memory:");
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getPdo(): \PDO {
        return $this->pdo;
    }
}

$db1 = Database::getInstance();
$db2 = Database::getInstance();
var_dump($db1 === $db2); // bool(true) — same object
?>
```

---

## Inheritance

A child class inherits all public and protected properties and methods from its parent.

```php
<?php
class Animal {
    public function __construct(
        protected string $name,
        protected string $sound
    ) {}

    public function speak(): string {
        return "{$this->name} says: {$this->sound}!";
    }

    public function getName(): string {
        return $this->name;
    }
}

class Dog extends Animal {
    public function __construct(string $name) {
        parent::__construct($name, "Woof");
    }

    public function fetch(string $item): string {
        return "{$this->name} fetches the {$item}!";
    }

    // Override parent method
    public function speak(): string {
        return parent::speak() . " *wags tail*";
    }
}

class Cat extends Animal {
    public function __construct(string $name) {
        parent::__construct($name, "Meow");
    }
}

$dog = new Dog("Rex");
$cat = new Cat("Whiskers");

echo $dog->speak();        // Rex says: Woof! *wags tail*
echo $cat->speak();        // Whiskers says: Meow!
echo $dog->fetch("ball");  // Rex fetches the ball!
?>
```

---

## Abstract Classes and Methods

An abstract class cannot be instantiated — it must be subclassed. Abstract methods have no body and must be implemented in child classes.

```php
<?php
abstract class Shape {
    abstract public function area(): float;
    abstract public function perimeter(): float;

    public function describe(): string {
        return sprintf(
            "%s — Area: %.2f, Perimeter: %.2f",
            get_class($this),
            $this->area(),
            $this->perimeter()
        );
    }
}

class Circle extends Shape {
    public function __construct(private float $radius) {}

    public function area(): float {
        return M_PI * $this->radius ** 2;
    }

    public function perimeter(): float {
        return 2 * M_PI * $this->radius;
    }
}

class Rectangle extends Shape {
    public function __construct(
        private float $width,
        private float $height
    ) {}

    public function area(): float {
        return $this->width * $this->height;
    }

    public function perimeter(): float {
        return 2 * ($this->width + $this->height);
    }
}

$shapes = [new Circle(5), new Rectangle(4, 6)];
foreach ($shapes as $shape) {
    echo $shape->describe() . "\n";
}
// Circle — Area: 78.54, Perimeter: 31.42
// Rectangle — Area: 24.00, Perimeter: 20.00
?>
```

---

## Interfaces

An interface defines a contract — the methods a class must implement. A class can implement multiple interfaces.

```php
<?php
interface Printable {
    public function print(): void;
}

interface Exportable {
    public function exportToPdf(): string;
    public function exportToCsv(): string;
}

class Invoice implements Printable, Exportable {
    public function __construct(
        private string $invoiceNo,
        private float  $amount
    ) {}

    public function print(): void {
        echo "Invoice #{$this->invoiceNo}: PKR {$this->amount}\n";
    }

    public function exportToPdf(): string {
        return "invoice_{$this->invoiceNo}.pdf";
    }

    public function exportToCsv(): string {
        return "{$this->invoiceNo},{$this->amount}";
    }
}

$inv = new Invoice("INV-001", 15000.00);
$inv->print();
echo $inv->exportToPdf(); // invoice_INV-001.pdf
?>
```

---

## Traits

Traits allow reuse of methods across multiple classes, solving the problem of single inheritance.

```php
<?php
trait Timestamps {
    private ?DateTime $createdAt = null;
    private ?DateTime $updatedAt = null;

    public function setCreatedAt(): void {
        $this->createdAt = new DateTime();
    }

    public function setUpdatedAt(): void {
        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt?->format("Y-m-d H:i:s");
    }
}

trait SoftDelete {
    private bool $deleted = false;

    public function delete(): void {
        $this->deleted = true;
    }

    public function isDeleted(): bool {
        return $this->deleted;
    }
}

class User {
    use Timestamps, SoftDelete;

    public function __construct(public string $name) {
        $this->setCreatedAt();
    }
}

$user = new User("Alice");
echo $user->name;          // Alice
echo $user->getCreatedAt(); // 2026-04-10 ...
$user->delete();
var_dump($user->isDeleted()); // bool(true)
?>
```

---

## Magic Methods

PHP magic methods start with `__` and are called automatically in certain situations.

```php
<?php
class MagicBox {
    private array $data = [];

    // Called when getting an inaccessible property
    public function __get(string $name): mixed {
        return $this->data[$name] ?? null;
    }

    // Called when setting an inaccessible property
    public function __set(string $name, mixed $value): void {
        $this->data[$name] = $value;
    }

    // Called when object is used as a string
    public function __toString(): string {
        return json_encode($this->data);
    }

    // Called when unset() is used on inaccessible property
    public function __unset(string $name): void {
        unset($this->data[$name]);
    }

    // Called when isset() is used on inaccessible property
    public function __isset(string $name): bool {
        return isset($this->data[$name]);
    }
}

$box = new MagicBox();
$box->name = "Widget";      // __set
$box->price = 99.99;        // __set
echo $box->name;            // __get → Widget
echo $box;                  // __toString → {"name":"Widget","price":99.99}
var_dump(isset($box->name)); // __isset → bool(true)
unset($box->price);          // __unset
?>
```

---

## Enumerations (PHP 8.1+)

Enums provide a type-safe way to represent a fixed set of values.

```php
<?php
enum Status {
    case Pending;
    case Active;
    case Suspended;
    case Deleted;
}

enum Color: string {
    case Red   = '#FF0000';
    case Green = '#00FF00';
    case Blue  = '#0000FF';
}

function processUser(Status $status): string {
    return match($status) {
        Status::Pending   => "Awaiting approval",
        Status::Active    => "User is active",
        Status::Suspended => "Access revoked",
        Status::Deleted   => "User deleted",
    };
}

echo processUser(Status::Active); // User is active
echo Color::Red->value;           // #FF0000
?>
```

---

## Summary

OOP in PHP allows you to build well-structured, maintainable, and reusable code. Classes, interfaces, abstract classes, traits, and enums each solve specific design problems. PHP 8.x additions like readonly properties, constructor promotion, named arguments, and enums have modernized the language significantly. Mastering OOP is essential for working with PHP frameworks like Laravel, Symfony, and Yii.
