# PHP Variables and Data Types

## Variables in PHP

### Variable Declaration and Assignment
```php
<?php
// Variable declaration with $
$name = "John Doe";
$age = 25;
$height = 5.8;
$is_student = true;
$grades = [85, 92, 78, 96];

// Multiple assignment
$first_name = "John";
$last_name = "Doe";
$email = "john@example.com";

// Variable interpolation
echo "Hello, $name! You are $age years old.";
// Output: Hello, John Doe! You are 25 years old.

// Curly brace syntax for complex expressions
echo "Your full name is {$first_name} {$last_name}.";
// Output: Your full name is John Doe.
?>
```

### Variable Naming Conventions
```php
<?php
// Valid variable names
$user_name = "Alice";
$userAge = 30;
$is_active = true;
$MAX_LOGIN_ATTEMPTS = 5;
$private_data = "sensitive";

// Variable names must start with a letter or underscore
$_private_var = "private";
$public_var = "public";

// Camel case (common for variables)
$firstName = "John";
$lastName = "Doe";
$emailAddress = "john@example.com";

// Snake case (also common)
$first_name = "John";
$last_name = "Doe";
$email_address = "john@example.com";

// Constants (uppercase with underscores)
define("PI", 3.141592653589793);
define("MAX_USERS", 1000);
define("APP_VERSION", "1.0.0");

// Class constants (PHP 5.3+)
class MathConstants {
    const PI = 3.141592653589793;
    const E = 2.718281828459045;
}

echo MathConstants::PI; // 3.141592653589793
?>
```

### Variable Scope
```php
<?php
// Global scope
$global_var = "I am global";

function testScope() {
    // Local scope
    $local_var = "I am local";
    
    // Access global variable
    global $global_var;
    echo $global_var; // "I am global"
    
    // Alternative way to access global variables
    echo $GLOBALS['global_var']; // "I am global"
    
    // Static variable (persists across function calls)
    static $counter = 0;
    $counter++;
    echo "Counter: $counter";
}

testScope(); // Counter: 1
testScope(); // Counter: 2

// Superglobal variables
echo $_GET['name'];     // GET parameters
echo $_POST['email'];    // POST parameters
echo $_REQUEST['data'];  // GET + POST + COOKIE
echo $_SERVER['HTTP_HOST']; // Server variables
echo $_FILES['upload'];  // Uploaded files
echo $_COOKIE['session']; // Cookies
echo $_SESSION['user'];  // Session data
?>
```

## Data Types

### Primitive Data Types
```php
<?php
// Integer
$age = 25;
$temperature = -10;
$hex_value = 0x1A; // 26 in decimal
$octal_value = 012; // 10 in decimal
$binary_value = 0b1010; // 10 in decimal

// Float/Double
$price = 19.99;
$pi = 3.14159;
$scientific = 1.5e-4; // 0.00015

// String
$single_quoted = 'Hello, World!';
$double_quoted = "Hello, $name!";
$heredoc = <<<HTML
<html>
<head><title>My Page</title></head>
<body>Hello, World!</body>
</html>
HTML;

$nowdoc = <<<'TEXT'
This is a nowdoc string.
No variable parsing here.
TEXT;

// Boolean
$is_valid = true;
$is_error = false;

// NULL
$no_value = null;
$undefined = null;
?>
```

### Compound Data Types
```php
<?php
// Arrays (indexed)
$fruits = ["apple", "banana", "cherry"];
$numbers = [1, 2, 3, 4, 5];
$mixed = ["hello", 42, true, 3.14];

// Array operations
$fruits[] = "orange"; // Add element
array_push($fruits, "grape"); // Alternative way
$last_fruit = array_pop($fruits); // Remove and return last element
$first_fruit = array_shift($fruits); // Remove and return first element

// Arrays (associative)
$user = [
    "name" => "John Doe",
    "age" => 30,
    "email" => "john@example.com"
];

// Alternative syntax
$person = array(
    "first_name" => "John",
    "last_name" => "Doe",
    "age" => 30
);

// Accessing associative arrays
echo $user["name"]; // "John Doe"
echo $person["first_name"]; // "John"

// Multidimensional arrays
$matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

$users = [
    "user1" => [
        "name" => "John",
        "age" => 30
    ],
    "user2" => [
        "name" => "Jane",
        "age" => 25
    ]
];

echo $matrix[1][2]; // 6
echo $users["user1"]["name"]; // "John"
?>
```

### Special Data Types
```php
<?php
// Resource (file handle)
$file_handle = fopen("example.txt", "r");
if ($file_handle) {
    echo "File opened successfully";
    fclose($file_handle);
}

// Resource (database connection)
$connection = mysqli_connect("localhost", "user", "password", "database");

// Object
class User {
    public $name;
    public $age;
    
    public function __construct($name, $age) {
        $this->name = $name;
        $this->age = $age;
    }
    
    public function greet() {
        return "Hello, I'm {$this->name} and I'm {$this->age} years old.";
    }
}

$user = new User("John", 30);
echo $user->greet(); // "Hello, I'm John and I'm 30 years old."

// Callable (function reference)
function sayHello($name) {
    return "Hello, $name!";
}

$callable = "sayHello";
echo $callable("World"); // "Hello, World!"

// Anonymous function (closure)
$greet = function($name) {
    return "Hello, $name!";
};

echo $greet("PHP"); // "Hello, PHP!"

// Closure with use statement
$multiplier = 2;
$multiply = function($number) use ($multiplier) {
    return $number * $multiplier;
};

echo $multiply(5); // 10
?>
```

## Type Juggling and Comparison

### Type Juggling
```php
<?php
// PHP automatically converts types based on context
$result = "5" + 3; // 8 (string to integer)
$result = "5 apples" + 3; // 8 (string to integer)
$result = "5.5" + 3; // 8.5 (string to float)
$result = true + 1; // 2 (boolean to integer)
$result = false + 1; // 1 (boolean to integer)
$result = null + 1; // 1 (null to integer)

// String concatenation vs addition
$result = "5" . "3"; // "53" (concatenation)
$result = "5" + "3"; // 8 (addition)

// Array to string conversion
$array = [1, 2, 3];
echo "Array: " . $array; // "Array: Array"
?>
```

### Type Comparison
```php
<?php
// Loose comparison (==)
5 == "5";        // true
5 == "5 apples"; // true
0 == "false";    // true
null == "";      // true
null == false;   // true

// Strict comparison (===)
5 === "5";       // false
5 === 5;         // true
null === false;   // false
null === "";      // false

// Comparison functions
var_dump(5 == "5");        // bool(true)
var_dump(5 === "5");       // bool(false)
var_dump(5 == "5 apples"); // bool(true)
var_dump(5 === "5 apples"); // bool(false)

// Type checking
is_int(5);           // true
is_string("5");      // true
is_bool(true);       // true
is_array([]);        // true
is_object($user);    // true
is_null(null);       // true

// Type casting
$number = "42";
$int_value = (int)$number;        // 42
$float_value = (float)$number;    // 42.0
$string_value = (string)$number;  // "42"
$bool_value = (bool)$number;      // true
$array_value = (array)$number;    // [0 => "42"]
?>
```

## Type Declaration and Strict Types

### Type Declaration (PHP 7+)
```php
<?php
// Enable strict typing
declare(strict_types=1);

function addNumbers(int $a, int $b): int {
    return $a + $b;
}

// This will work
$result = addNumbers(5, 3); // 8

// This will throw TypeError
// $result = addNumbers(5, "3"); // TypeError

// Return type declaration
function getUserData(): array {
    return ["name" => "John", "age" => 30];
}

// Nullable type declaration (PHP 7.1+)
function getName(?string $name): ?string {
    return $name; // Can return string or null
}

// Union types (PHP 8.0+)
function processValue(int|string $value): string {
    return (string)$value;
}

// Mixed type (PHP 8.0+)
function processMixed(mixed $value): mixed {
    return $value;
}
?>
```

### Class Properties and Methods
```php
<?php
class User {
    // Type declarations for properties (PHP 7.4+)
    public string $name;
    public int $age;
    public ?string $email;
    
    // Constructor with type declarations
    public function __construct(string $name, int $age, ?string $email = null) {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
    }
    
    // Method with return type declaration
    public function getName(): string {
        return $this->name;
    }
    
    // Method with parameter and return type declarations
    public function setAge(int $age): void {
        $this->age = $age;
    }
    
    // Static method with type declarations
    public static function createFromData(array $data): self {
        return new self(
            $data['name'],
            $data['age'],
            $data['email'] ?? null
        );
    }
}

$user = new User("John", 30, "john@example.com");
echo $user->getName(); // "John"
?>
```

## Variable Variables and References

### Variable Variables
```php
<?php
// Variable variables (variables with variable names)
$name = "John";
$$name = "Doe"; // Creates variable $john = "Doe"

echo $name;    // "John"
echo $john;    // "Doe"

// Practical example
$field_name = "username";
$_POST[$field_name] = "john_doe";
echo $_POST['username']; // "john_doe"

// Variable functions
function sayHello() {
    return "Hello!";
}

$function_name = "sayHello";
echo $function_name(); // "Hello!"

// Variable classes
class MyClass {
    public function method() {
        return "Method called";
    }
}

$class_name = "MyClass";
$object = new $class_name();
echo $object->method(); // "Method called"
?>
```

### References
```php
<?php
// Assign by reference
$a = 10;
$b = &$a; // $b is a reference to $a
$b = 20;
echo $a; // 20 (changed because $b references $a)

// Function parameters by reference
function increment(&$value) {
    $value++;
}

$number = 5;
increment($number);
echo $number; // 6

// Function returning reference
function &getReference() {
    static $value = 10;
    return $value;
}

$ref = &getReference();
$ref = 20;
echo getReference(); // 20

// Unsetting references
$a = 10;
$b = &$a;
unset($b); // Only removes the reference, not $a
echo $a; // 10
?>
```

## Constants and Magic Constants

### User-defined Constants
```php
<?php
// Define constants
define("SITE_NAME", "My Website");
define("MAX_USERS", 1000);
define("DEBUG_MODE", true);

// Case-insensitive constants (PHP 8.0+)
define("API_VERSION", "v1.0", true);

// Class constants
class Config {
    const DB_HOST = "localhost";
    const DB_USER = "root";
    const DB_PASS = "password";
    
    // Class constant arrays (PHP 8.2+)
    public const COLORS = ["red", "green", "blue"];
}

echo Config::DB_HOST; // "localhost"
echo Config::COLORS[0]; // "red"

// Interface constants
interface LoggerInterface {
    const LEVEL_DEBUG = 1;
    const LEVEL_INFO = 2;
    const LEVEL_WARNING = 3;
    const LEVEL_ERROR = 4;
}

// Magic constants
class MyClass {
    public function showConstants() {
        echo __CLASS__ . "\n";    // ClassName
        echo __METHOD__ . "\n";   // ClassName::methodName
        echo __FUNCTION__ . "\n"; // methodName
        echo __LINE__ . "\n";     // Current line number
        echo __FILE__ . "\n";     // Current file path
        echo __DIR__ . "\n";      // Current directory path
        echo __NAMESPACE__ . "\n"; // Current namespace
    }
}

$obj = new MyClass();
$obj->showConstants();
?>
```

## Best Practices

### Variable Naming and Organization
```php
<?php
// Good: Descriptive variable names
$user_name = "John";
$user_age = 30;
$is_admin_user = true;
$max_login_attempts = 3;

// Bad: Non-descriptive names
$x = "John";
$y = 30;
$z = true;
$a = 3;

// Good: Group related variables
$user = [
    "name" => "John",
    "age" => 30,
    "email" => "john@example.com",
    "is_active" => true
];

// Good: Use constants for magic numbers
class PriceCalculator {
    const TAX_RATE = 0.08;
    const DISCOUNT_THRESHOLD = 100;
    const DISCOUNT_RATE = 0.1;
    
    public function calculateTotal(float $price, int $quantity): float {
        $subtotal = $price * $quantity;
        $discount = $subtotal >= self::DISCOUNT_THRESHOLD ? $subtotal * self::DISCOUNT_RATE : 0;
        $tax = ($subtotal - $discount) * self::TAX_RATE;
        return $subtotal - $discount + $tax;
    }
}

// Good: Use meaningful method names
function calculateUserAge(string $birth_year): int {
    return date("Y") - (int)$birth_year;
}

function isValidEmailFormat(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>
```

### Type Safety and Validation
```php
<?php
// Good: Type declarations and validation
class User {
    private string $name;
    private int $age;
    private ?string $email;
    
    public function __construct(string $name, int $age, ?string $email = null) {
        $this->setName($name);
        $this->setAge($age);
        $this->setEmail($email);
    }
    
    public function setName(string $name): void {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("Name cannot be empty");
        }
        if (strlen($name) > 100) {
            throw new InvalidArgumentException("Name too long");
        }
        $this->name = trim($name);
    }
    
    public function setAge(int $age): void {
        if ($age < 0 || $age > 150) {
            throw new InvalidArgumentException("Invalid age");
        }
        $this->age = $age;
    }
    
    public function setEmail(?string $email): void {
        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }
        $this->email = $email;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getAge(): int {
        return $this->age;
    }
    
    public function getEmail(): ?string {
        return $this->email;
    }
}

// Input validation function
function sanitizeInput(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Type checking
function processValue(mixed $value): string {
    if (is_string($value)) {
        return "String: " . $value;
    } elseif (is_int($value)) {
        return "Integer: " . $value;
    } elseif (is_float($value)) {
        return "Float: " . $value;
    } elseif (is_bool($value)) {
        return "Boolean: " . ($value ? 'true' : 'false');
    } elseif (is_array($value)) {
        return "Array with " . count($value) . " elements";
    } elseif (is_null($value)) {
        return "Null";
    } else {
        return "Unknown type: " . gettype($value);
    }
}
?>
```

### Memory Management
```php
<?php
// Good: Use references for large arrays
function processLargeArray(array &$data): void {
    foreach ($data as &$item) {
        $item = strtoupper($item);
    }
    unset($item); // Break reference
}

// Good: Unset large variables when done
$large_array = range(1, 100000);
// Process the array
unset($large_array);

// Good: Use generators for memory-efficient iteration
function generateNumbers(int $start, int $end): Generator {
    for ($i = $start; $i <= $end; $i++) {
        yield $i;
    }
}

foreach (generateNumbers(1, 1000000) as $number) {
    // Process number without loading all into memory
}

// Good: Use static variables for persistence
class Counter {
    private static int $count = 0;
    
    public static function increment(): int {
        return ++self::$count;
    }
    
    public static function getCount(): int {
        return self::$count;
    }
}

// Good: Use constants for configuration
class Database {
    private const HOST = "localhost";
    private const USER = "root";
    private const PASS = "password";
    private const NAME = "database";
    
    public static function connect(): mysqli {
        return new mysqli(self::HOST, self::USER, self::PASS, self::NAME);
    }
}
?>
```

## Common Pitfalls

### Variable Scope Issues
```php
<?php
// Pitfall: Variable shadowing
function calculateTotal(float $price, float $tax_rate): float {
    $total = $price * (1 + $tax_rate);
    $total = $total + 10;  // Shadowing the original total
    return $total;
}

// Better: Use different variable names
function calculateTotalBetter(float $price, float $tax_rate): float {
    $subtotal = $price * (1 + $tax_rate);
    $total = $subtotal + 10;
    return $total;
}

// Pitfall: Global variable abuse
$global_counter = 0;

function incrementCounter(): void {
    global $global_counter;
    $global_counter++;
}

// Better: Use static variables or class properties
class Counter {
    private static int $counter = 0;
    
    public static function increment(): int {
        return ++self::$counter;
    }
}

// Pitfall: Undefined variables
function getUserName(int $user_id): string {
    // $user = getUserFromDatabase($user_id); // Missing line
    return $user['name']; // Notice: Undefined variable $user
}

// Better: Initialize variables
function getUserNameBetter(int $user_id): string {
    $user = getUserFromDatabase($user_id);
    return $user['name'] ?? 'Unknown';
}
?>
```

### Type Juggling Issues
```php
<?php
// Pitfall: Unexpected type conversion
function addNumbers(int $a, int $b): int {
    return $a + $b;
}

$result = addNumbers(5, "3 apples"); // 8, but might be unexpected

// Better: Use strict types
declare(strict_types=1);

function addNumbersStrict(int $a, int $b): int {
    return $a + $b;
}

// $result = addNumbersStrict(5, "3 apples"); // TypeError

// Pitfall: Loose comparison issues
function checkValue($value): string {
    if ($value == "0") {
        return "Zero";
    }
    return "Not zero";
}

checkValue(0);        // "Zero"
checkValue(false);    // "Zero" (unexpected)
checkValue(null);     // "Zero" (unexpected)

// Better: Use strict comparison
function checkValueStrict($value): string {
    if ($value === "0") {
        return "Zero";
    }
    return "Not zero";
}

checkValueStrict(0);        // "Not zero"
checkValueStrict(false);    // "Not zero"
checkValueStrict(null);     // "Not zero"

// Pitfall: Array to string conversion
$array = [1, 2, 3];
echo "Array: " . $array; // "Array: Array" (notice)

// Better: Explicit conversion
echo "Array: " . implode(", ", $array); // "Array: 1, 2, 3"
?>
```

### Reference Issues
```php
<?php
// Pitfall: Unintended reference behavior
function modifyArray(array $data): void {
    $data[] = "new item";
}

$array = [1, 2, 3];
modifyArray($array);
print_r($array); // Still [1, 2, 3] - array was passed by value

// Better: Pass by reference if modification is intended
function modifyArrayByReference(array &$data): void {
    $data[] = "new item";
}

modifyArrayByReference($array);
print_r($array); // [1, 2, 3, "new item"]

// Pitfall: Reference persistence
function getReference(): int {
    static $value = 10;
    return $value;
}

$ref = &getReference();
$ref = 20;
echo getReference(); // 20 (reference persists)

// Better: Be explicit about reference usage
function getValue(): int {
    static $value = 10;
    return $value;
}

function setValue(int $newValue): void {
    static $value = 10;
    $value = $newValue;
}
?>
```

## Summary

PHP variables and data types provide:

**Variable Types:**
- Local variables (prefix with $)
- Global variables (accessible with global keyword)
- Static variables (persist across function calls)
- Superglobal variables ($_GET, $_POST, $_SERVER, etc.)
- Constants (define() and const)

**Data Types:**
- Scalar types: integer, float, string, boolean
- Compound types: array, object
- Special types: resource, null
- Callable (functions and closures)

**Type Features:**
- Type juggling (automatic conversion)
- Strict typing (PHP 7+)
- Type declarations and return types
- Union types and mixed types (PHP 8+)

**Advanced Features:**
- Variable variables (variables with variable names)
- References (pass by reference)
- Magic constants (__LINE__, __FILE__, etc.)
- Generators for memory efficiency

**Best Practices:**
- Descriptive naming conventions
- Type safety and validation
- Memory management techniques
- Proper scope management

**Common Pitfalls:**
- Variable scope confusion
- Type juggling surprises
- Reference behavior issues
- Global variable abuse

PHP's flexible type system and rich set of data types make it suitable for web development while providing modern features for type safety when needed.
