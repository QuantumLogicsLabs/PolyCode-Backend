# PHP Functions

## Function Definition

### Basic Function Syntax
```php
<?php
// Simple function definition
function greet() {
    echo "Hello, World!";
}

// Function with parameters
function greetPerson($name) {
    echo "Hello, $name!";
}

// Function with default parameters
function greetWithDefault($name = "Guest") {
    echo "Hello, $name!";
}

// Function with multiple parameters
function calculateSum($a, $b) {
    return $a + $b;
}

// Function with type declarations (PHP 7+)
function addNumbers(int $a, int $b): int {
    return $a + $b;
}

// Function with nullable type (PHP 7.1+)
function getName(?string $name): ?string {
    return $name;
}

// Function with union types (PHP 8.0+)
function processValue(int|string $value): string {
    return (string)$value;
}

// Function calls
greet(); // "Hello, World!"
greetPerson("John"); // "Hello, John!"
greetWithDefault(); // "Hello, Guest!"
greetWithDefault("Alice"); // "Hello, Alice!"

$sum = calculateSum(5, 3); // 8
$result = addNumbers(10, 20); // 30
?>
```

### Function Parameters

#### Required and Optional Parameters
```php
<?php
// Required parameters
function createUser($name, $email) {
    echo "Creating user: $name with email: $email";
}

// Optional parameters with default values
function sendEmail($to, $subject = "No Subject", $body = "") {
    echo "Sending email to: $to\n";
    echo "Subject: $subject\n";
    echo "Body: $body\n";
}

// Mixed required and optional parameters
function displayUserInfo($name, $age = null, $city = "Unknown") {
    echo "Name: $name\n";
    if ($age !== null) {
        echo "Age: $age\n";
    }
    echo "City: $city\n";
}

// Type declarations with defaults
function calculatePrice(float $basePrice, float $taxRate = 0.08): float {
    return $basePrice * (1 + $taxRate);
}

// Usage
createUser("John", "john@example.com");
sendEmail("user@example.com", "Welcome!", "Thank you for joining!");
sendEmail("user@example.com"); // Uses defaults
displayUserInfo("Alice", 25, "New York");
displayUserInfo("Bob", 30); // City defaults to "Unknown"

$price = calculatePrice(100); // 108.0
$price = calculatePrice(100, 0.10); // 110.0
?>
```

#### Variable Number of Arguments
```php
<?php
// Using func_get_args() (old way)
function sumAll() {
    $args = func_get_args();
    return array_sum($args);
}

// Using variadic syntax (PHP 5.6+)
function sumAllVariadic(...$numbers) {
    return array_sum($numbers);
}

// Mixed regular and variadic parameters
function processOrder($orderId, ...$items) {
    echo "Order ID: $orderId\n";
    echo "Items: " . implode(", ", $items) . "\n";
}

// Type-safe variadic parameters (PHP 7+)
function multiplyNumbers(int ...$numbers): int {
    $result = 1;
    foreach ($numbers as $number) {
        $result *= $number;
    }
    return $result;
}

// Usage
echo sumAll(1, 2, 3, 4, 5); // 15
echo sumAllVariadic(1, 2, 3, 4, 5); // 15
processOrder(123, "Apple", "Orange", "Banana"); // Order ID: 123, Items: Apple, Orange, Banana
echo multiplyNumbers(2, 3, 4); // 24
?>
```

#### Parameter Passing
```php
<?php
// Pass by value (default)
function incrementValue($number) {
    $number++;
    return $number;
}

$value = 5;
$result = incrementValue($value);
echo $value;  // 5 (unchanged)
echo $result; // 6

// Pass by reference
function incrementByReference(&$number) {
    $number++;
}

$value = 5;
incrementByReference($value);
echo $value; // 6 (changed)

// Mixed parameter types
function processUser(string $name, int &$age, array &$hobbies): void {
    echo "Processing user: $name\n";
    $age++;
    $hobbies[] = "Reading";
}

$userName = "John";
$userAge = 25;
$userHobbies = ["Swimming", "Coding"];
processUser($userName, $userAge, $userHobbies);
echo $userAge; // 26
print_r($userHobbies); // ["Swimming", "Coding", "Reading"]
?>
```

## Return Values

### Basic Return Values
```php
<?php
// Function with return value
function add($a, $b) {
    return $a + $b;
}

// Function with conditional return
function getGrade($score) {
    if ($score >= 90) {
        return "A";
    } elseif ($score >= 80) {
        return "B";
    } elseif ($score >= 70) {
        return "C";
    } elseif ($score >= 60) {
        return "D";
    } else {
        return "F";
    }
}

// Function with early return
function processUser($user) {
    if (!$user) {
        return "Invalid user";
    }
    
    if (!$user['active']) {
        return "User is inactive";
    }
    
    return "User {$user['name']} processed successfully";
}

// Function returning multiple values as array
function getUserDetails($userId) {
    // Simulate database query
    $users = [
        1 => ["name" => "John", "age" => 30, "email" => "john@example.com"],
        2 => ["name" => "Jane", "age" => 25, "email" => "jane@example.com"]
    ];
    
    return $users[$userId] ?? null;
}

// Usage
$sum = add(5, 3); // 8
$grade = getGrade(85); // "B"
$status = processUser(["name" => "Alice", "active" => true]); // "User Alice processed successfully"
$details = getUserDetails(1); // ["name" => "John", "age" => 30, "email" => "john@example.com"]
?>
```

### Return Type Declarations
```php
<?php
// Return type declaration (PHP 7+)
function calculateTotal(float $price, int $quantity): float {
    return $price * $quantity;
}

// Void return type (PHP 7.1+)
function logMessage(string $message): void {
    echo "Log: $message\n";
}

// Never return type (PHP 8.0+)
function alwaysThrows(): never {
    throw new Exception("This function always throws");
}

// Array return type
function getEvenNumbers(int $limit): array {
    $evens = [];
    for ($i = 1; $i <= $limit; $i++) {
        if ($i % 2 === 0) {
            $evens[] = $i;
        }
    }
    return $evens;
}

// Object return type
class User {
    public function __construct(public string $name, public int $age) {}
}

function createUser(string $name, int $age): User {
    return new User($name, $age);
}

// Nullable return type
function findUser(int $id): ?User {
    // Simulate database lookup
    if ($id === 1) {
        return new User("John", 30);
    }
    return null;
}

// Union return type (PHP 8.0+)
function getValue(int $type): int|string {
    return match($type) {
        1 => 42,
        2 => "answer",
        default => "unknown"
    };
}

// Usage
$total = calculateTotal(19.99, 3); // 59.97
logMessage("Test message"); // "Log: Test message"
$evens = getEvenNumbers(10); // [2, 4, 6, 8, 10]
$user = createUser("Alice", 25); // User object
$foundUser = findUser(1); // User object
$notFound = findUser(99); // null
?>
```

### Returning References
```php
<?php
// Function returning reference
function &getReference() {
    static $value = 10;
    return $value;
}

// Modifying the returned reference
$ref = &getReference();
$ref = 20;
echo getReference(); // 20

// Practical example: Configuration manager
class Config {
    private static array $settings = [
        'debug' => true,
        'version' => '1.0.0'
    ];
    
    public static function &getSetting(string $key) {
        return self::$settings[$key] ?? null;
    }
}

// Modify configuration by reference
$debugSetting = &Config::getSetting('debug');
$debugSetting = false;

// Reference return in array context
function &getArrayElement(array &$array, string $key) {
    return $array[$key] ?? null;
}

$config = ['timeout' => 30];
$timeout = &getArrayElement($config, 'timeout');
$timeout = 60;
echo $config['timeout']; // 60
?>
```

## Advanced Function Features

### Anonymous Functions (Closures)
```php
<?php
// Basic anonymous function
$greet = function($name) {
    return "Hello, $name!";
};

echo $greet("World"); // "Hello, World!"

// Anonymous function as callback
$numbers = [1, 2, 3, 4, 5];
$squared = array_map(function($number) {
    return $number * $number;
}, $numbers);

print_r($squared); // [1, 4, 9, 16, 25]

// Closure with use keyword
$multiplier = 2;
$multiply = function($number) use ($multiplier) {
    return $number * $multiplier;
};

echo $multiply(5); // 10

// Closure by reference
$counter = 0;
$increment = function() use (&$counter) {
    return ++$counter;
};

echo $increment(); // 1
echo $increment(); // 2
echo $counter; // 2

// Closure with multiple variables
$message = "Hello";
$format = function($name) use ($message, $format) {
    return "$message, $name!";
};

echo $format("John"); // "Hello, John!"

// Closure as class method
class Greeter {
    private string $prefix = "Hi";
    
    public function getGreeting(): Closure {
        return function($name) {
            return "$this->prefix, $name!";
        };
    }
}

$greeter = new Greeter();
$greet = $greeter->getGreeting();
echo $greet("Alice"); // "Hi, Alice!"
?>
```

### Arrow Functions (PHP 7.4+)
```php
<?php
// Basic arrow function
$add = fn($a, $b) => $a + $b;
echo $add(5, 3); // 8

// Arrow function with multiple expressions
$process = fn($data) => [
    'processed' => true,
    'count' => count($data),
    'sum' => array_sum($data)
];

$result = $process([1, 2, 3, 4, 5]);
print_r($result);

// Arrow function capturing variables
$prefix = "Product";
$formatName = fn($name) => "$prefix: $name";
echo $formatName("Widget"); // "Product: Widget"

// Arrow function in array operations
$users = [
    ['name' => 'John', 'age' => 30],
    ['name' => 'Jane', 'age' => 25],
    ['name' => 'Bob', 'age' => 35]
];

$names = array_map(fn($user) => $user['name'], $users);
$adults = array_filter($users, fn($user) => $user['age'] >= 18);

print_r($names); // ['John', 'Jane', 'Bob']
print_r($adults); // All users (all are adults)

// Arrow function vs regular function comparison
$regular = function($x) { return $x * 2; };
$arrow = fn($x) => $x * 2;

echo $regular(5); // 10
echo $arrow(5); // 10
?>
```

### Higher-Order Functions
```php
<?php
// Function that accepts another function as parameter
function applyOperation(callable $operation, $a, $b) {
    return $operation($a, $b);
}

$sum = fn($a, $b) => $a + $b;
$product = fn($a, $b) => $a * $b;

echo applyOperation($sum, 5, 3); // 8
echo applyOperation($product, 5, 3); // 15

// Function that returns another function
function createMultiplier(int $factor): Closure {
    return function($number) use ($factor) {
        return $number * $factor;
    };
}

$double = createMultiplier(2);
$triple = createMultiplier(3);

echo $double(10); // 20
echo $triple(10); // 30

// Function composition
function compose(callable $f, callable $g): Closure {
    return function($x) use ($f, $g) {
        return $f($g($x));
    };
}

$addOne = fn($x) => $x + 1;
$double = fn($x) => $x * 2;

$addThenDouble = compose($double, $addOne);
$doubleThenAdd = compose($addOne, $double);

echo $addThenDouble(5); // 12 ((5 + 1) * 2)
echo $doubleThenAdd(5); // 11 ((5 * 2) + 1)

// Currying
function curry(callable $function): Closure {
    return function($arg) use ($function) {
        return function($arg2) use ($function, $arg) {
            return $function($arg, $arg2);
        };
    };
}

$add = fn($a, $b) => $a + $b;
$curriedAdd = curry($add);
$addFive = $curriedAdd(5);
echo $addFive(3); // 8

// Memoization
function memoize(callable $function): Closure {
    static $cache = [];
    
    return function(...$args) use ($function, &$cache) {
        $key = serialize($args);
        
        if (!isset($cache[$key])) {
            $cache[$key] = $function(...$args);
        }
        
        return $cache[$key];
    };
}

$slowFunction = function($n) {
    sleep(1); // Simulate slow operation
    return $n * $n;
};

$memoized = memoize($slowFunction);

$start = microtime(true);
echo $memoized(5); // Takes 1 second
$end = microtime(true);
echo "Time: " . ($end - $start) . "\n";

$start = microtime(true);
echo $memoized(5); // Instant (cached)
$end = microtime(true);
echo "Time: " . ($end - $start) . "\n";
?>
```

## Built-in Functions

### String Functions
```php
<?php
// String manipulation functions
$text = "Hello, World!";

echo strlen($text); // 13
echo str_word_count($text); // 2
echo strrev($text); // "!dlroW ,olleH"
echo strtoupper($text); // "HELLO, WORLD!"
echo strtolower($text); // "hello, world!"
echo ucfirst($text); // "Hello, World!"
echo ucwords("hello world"); // "Hello World"

// String search and replace
echo strpos($text, "World"); // 7
echo str_replace("World", "PHP", $text); // "Hello, PHP!"
echo substr($text, 0, 5); // "Hello"
echo trim("  Hello  "); // "Hello"

// String formatting
$name = "John";
$age = 30;
echo sprintf("Name: %s, Age: %d", $name, $age); // "Name: John, Age: 30"
echo "Name: $name, Age: $age"; // "Name: John, Age: 30"

// Regular expressions
$pattern = "/\b[A-Za-z]+\b/";
$text = "Hello World 123";
preg_match_all($pattern, $text, $matches);
print_r($matches[0]); // ["Hello", "World"]
?>
```

### Array Functions
```php
<?php
// Array creation and manipulation
$fruits = ["apple", "banana", "cherry"];
$numbers = [1, 2, 3, 4, 5];

echo count($fruits); // 3
echo array_push($fruits, "orange"); // Adds to end
echo array_pop($fruits); // Removes from end
echo array_shift($fruits); // Removes from beginning
echo array_unshift($fruits, "grape"); // Adds to beginning

// Array search and filtering
echo in_array("apple", $fruits); // true
echo array_search("banana", $fruits); // 1
$evens = array_filter($numbers, fn($n) => $n % 2 === 0);
print_r($evens); // [1 => 2, 3 => 4]

// Array transformation
$doubled = array_map(fn($n) => $n * 2, $numbers);
print_r($doubled); // [2, 4, 6, 8, 10]

$sum = array_reduce($numbers, fn($carry, $n) => $carry + $n, 0);
echo $sum; // 15

// Array sorting
$sorted = $numbers;
sort($sorted); // Sort ascending
rsort($sorted); // Sort descending
asort($fruits); // Sort associative array by value
ksort($fruits); // Sort associative array by key

// Array merging
$combined = array_merge($fruits, $numbers);
print_r($combined); // ["apple", "banana", "cherry", 1, 2, 3, 4, 5]

// Array difference and intersection
$a = [1, 2, 3, 4, 5];
$b = [3, 4, 5, 6, 7];

$diff = array_diff($a, $b); // [1, 2]
$intersect = array_intersect($a, $b); // [3, 4, 5]
?>
```

### Mathematical Functions
```php
<?php
// Basic math functions
echo abs(-5); // 5
echo round(3.14159, 2); // 3.14
echo ceil(3.14); // 4
echo floor(3.14); // 3
echo pow(2, 3); // 8
echo sqrt(16); // 4
echo rand(1, 100); // Random number between 1 and 100

// Trigonometric functions
echo sin(pi() / 2); // 1
echo cos(0); // 1
echo tan(pi() / 4); // 1

// Logarithmic functions
echo log(10); // Natural logarithm
echo log10(100); // Base-10 logarithm
echo exp(1); // e^1

// Number formatting
echo number_format(1234.567, 2); // "1,234.57"
echo money_format("%i", 1234.56); // "1,235.56"

// Conversion functions
echo bindec("1010"); // 10
echo decbin(10); // "1010"
echo hexdec("FF"); // 255
echo dechex(255); // "ff"
echo octdec("10"); // 8
echo decoct(8); // "10"
?>
```

### Date and Time Functions
```php
<?php
// Current date and time
echo date("Y-m-d H:i:s"); // Current date and time
echo time(); // Unix timestamp
echo microtime(true); // Microtime with float

// Date formatting
$timestamp = time();
echo date("Y-m-d", $timestamp); // "2023-12-01"
echo date("l, F j, Y", $timestamp); // "Friday, December 1, 2023"

// Creating dates
$date = date_create("2023-12-01");
echo date_format($date, "Y-m-d"); // "2023-12-01"

// Date manipulation
$date = new DateTime("2023-12-01");
$date->add(new DateInterval("P1D")); // Add 1 day
echo $date->format("Y-m-d"); // "2023-12-02"

$date->sub(new DateInterval("P1M")); // Subtract 1 month
echo $date->format("Y-m-d"); // "2023-11-02"

// Date difference
$date1 = new DateTime("2023-12-01");
$date2 = new DateTime("2023-12-15");
$interval = $date1->diff($date2);
echo $interval->days; // 14

// Time zones
$date = new DateTime("now", new DateTimeZone("America/New_York"));
echo $date->format("Y-m-d H:i:s T"); // Date with timezone

// String to time conversion
$timestamp = strtotime("2023-12-01 15:30:00");
echo date("Y-m-d H:i:s", $timestamp); // "2023-12-01 15:30:00"

// Relative time
echo strtotime("+1 week"); // Timestamp for next week
echo strtotime("next Monday"); // Timestamp for next Monday
?>
```

## Function Scope and Namespaces

### Function Scope
```php
<?php
// Global functions (available everywhere)
function globalFunction() {
    return "I am global!";
}

// Namespace functions (PHP 5.3+)
namespace MyNamespace {
    function namespacedFunction() {
        return "I am in a namespace!";
    }
    
    function helper() {
        return "Helper function";
    }
}

namespace AnotherNamespace {
    function helper() {
        return "Another helper function";
    }
}

// Calling functions
echo globalFunction(); // "I am global!"
echo MyNamespace\namespacedFunction(); // "I am in a namespace!"
echo MyNamespace\helper(); // "Helper function"
echo AnotherNamespace\helper(); // "Another helper function"

// Use statement for namespace
use function MyNamespace\namespacedFunction;
echo namespacedFunction(); // "I am in a namespace!"

// Use with alias
use function MyNamespace\helper as myHelper;
echo myHelper(); // "Helper function"

// Global function in namespace
namespace {
    function globalInNamespace() {
        return "Global in namespace";
    }
}

namespace MyNamespace {
    function callGlobal() {
        return \globalInNamespace(); // Use backslash for global
    }
}

echo MyNamespace\callGlobal(); // "Global in namespace"
?>
```

### Namespaced Functions
```php
<?php
namespace Math {
    function add($a, $b) {
        return $a + $b;
    }
    
    function multiply($a, $b) {
        return $a * $b;
    }
    
    class Calculator {
        public static function divide($a, $b) {
            return $a / $b;
        }
    }
}

namespace String {
    function reverse($str) {
        return strrev($str);
    }
    
    function length($str) {
        return strlen($str);
    }
}

// Using namespaced functions
use function Math\add;
use function Math\multiply;
use function String\reverse;
use function String\length;

echo add(5, 3); // 8
echo multiply(4, 5); // 20
echo reverse("hello"); // "olleh"
echo length("world"); // 5

// Fully qualified names
echo Math\add(10, 20); // 30
echo String\reverse("PHP"); // "PHP"

// Multiple functions from same namespace
use function Math\{add, multiply};
echo add(2, 3); // 5
echo multiply(4, 6); // 24

// Namespace aliasing
namespace Math {
    use function String\reverse as strrev;
    
    function reverseAndAdd($str, $num) {
        return strrev($str) . $num;
    }
}

echo Math\reverseAndAdd("123", 456); // "321456"
?>
```

## Best Practices

### Function Design
```php
<?php
// Good: Single responsibility
function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function sanitizeInput(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function calculateAge(string $birthDate): int {
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    return $today->diff($birth)->y;
}

// Good: Descriptive function names
function getUserById(int $userId): ?array {
    // Database query logic
    return null;
}

function isUserActive(array $user): bool {
    return ($user['status'] ?? 'inactive') === 'active';
}

function formatCurrency(float $amount, string $currency = 'USD'): string {
    return number_format($amount, 2) . ' ' . $currency;
}

// Good: Type declarations and return types
function calculateTotalPrice(float $basePrice, int $quantity, float $taxRate = 0.08): float {
    return $basePrice * $quantity * (1 + $taxRate);
}

function createUser(array $userData): array {
    // Validation
    if (empty($userData['name']) || empty($userData['email'])) {
        throw new InvalidArgumentException("Name and email are required");
    }
    
    // Create user
    return [
        'id' => uniqid(),
        'name' => trim($userData['name']),
        'email' => strtolower($userData['email']),
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// Good: Error handling
function divideNumbers(float $a, float $b): float {
    if ($b === 0.0) {
        throw new DivisionByZeroError("Cannot divide by zero");
    }
    return $a / $b;
}

function readFileContent(string $filename): string {
    if (!file_exists($filename)) {
        throw new RuntimeException("File not found: $filename");
    }
    
    $content = file_get_contents($filename);
    if ($content === false) {
        throw new RuntimeException("Failed to read file: $filename");
    }
    
    return $content;
}
?>
```

### Performance Considerations
```php
<?php
// Good: Use built-in functions when possible
function sumArray(array $numbers): int {
    return array_sum($numbers); // Faster than manual loop
}

function filterEvenNumbers(array $numbers): array {
    return array_filter($numbers, fn($n) => $n % 2 === 0);
}

// Good: Avoid unnecessary function calls
function processLargeArray(array $data): array {
    $result = [];
    
    foreach ($data as $item) {
        // Process item
        $result[] = $processedItem;
    }
    
    return $result;
}

// Good: Use generators for memory efficiency
function generateNumbers(int $start, int $end): Generator {
    for ($i = $start; $i <= $end; $i++) {
        yield $i;
    }
}

// Usage: foreach (generateNumbers(1, 1000000) as $number) { ... }

// Good: Cache expensive function results
function expensiveCalculation(int $n): int {
    static $cache = [];
    
    if (!isset($cache[$n])) {
        // Expensive calculation
        $cache[$n] = $n * $n;
    }
    
    return $cache[$n];
}

// Good: Use strict types for better performance and reliability
declare(strict_types=1);

function addStrict(int $a, int $b): int {
    return $a + $b;
}

// Good: Early return for better readability
function processUser(array $user): string {
    if (empty($user)) {
        return "User data is empty";
    }
    
    if (!isset($user['id'])) {
        return "User ID is missing";
    }
    
    if (!$user['active']) {
        return "User is inactive";
    }
    
    return "User {$user['id']} processed successfully";
}
?>
```

### Documentation
```php
<?php
/**
 * Calculates the area of a rectangle.
 *
 * @param float $width The width of the rectangle
 * @param float $height The height of the rectangle
 * @return float The area of the rectangle
 * @throws InvalidArgumentException If width or height is negative
 */
function calculateRectangleArea(float $width, float $height): float {
    if ($width < 0 || $height < 0) {
        throw new InvalidArgumentException("Width and height must be positive");
    }
    
    return $width * $height;
}

/**
 * Validates and sanitizes user input.
 *
 * @param string $input The input string to validate
 * @param int $maxLength Maximum allowed length
 * @param bool $allowHtml Whether HTML tags are allowed
 * @return string The sanitized input
 * @throws InvalidArgumentException If input is too long
 */
function validateInput(string $input, int $maxLength = 255, bool $allowHtml = false): string {
    if (strlen($input) > $maxLength) {
        throw new InvalidArgumentException("Input too long");
    }
    
    $trimmed = trim($input);
    
    if (!$allowHtml) {
        $trimmed = htmlspecialchars($trimmed, ENT_QUOTES, 'UTF-8');
    }
    
    return $trimmed;
}

/**
 * Formats a date for display.
 *
 * @param string|int|DateTime $date The date to format
 * @param string $format The desired format (default: 'Y-m-d')
 * @return string The formatted date
 */
function formatDate($date, string $format = 'Y-m-d'): string {
    if ($date instanceof DateTime) {
        return $date->format($format);
    }
    
    if (is_numeric($date)) {
        return date($format, (int)$date);
    }
    
    if (is_string($date)) {
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    }
    
    throw new InvalidArgumentException("Invalid date format");
}
?>
```

## Common Pitfalls

### Function Definition Issues
```php
<?php
// Pitfall: Function name conflicts
function calculate($a, $b) {
    return $a + $b;
}

function calculate($a, $b, $c) {
    return $a + $b + $c;
}
// Fatal error: Cannot redeclare calculate()

// Solution: Use different names or namespaces
namespace Math {
    function calculateSum($a, $b) {
        return $a + $b;
    }
}

namespace Advanced {
    function calculateSum($a, $b, $c) {
        return $a + $b + $c;
    }
}

// Pitfall: Optional parameters before required ones
function badFunction($optional = "default", $required) {
    return "$optional - $required";
}
// Fatal error when called: badFunction("value")

// Solution: Put required parameters first
function goodFunction($required, $optional = "default") {
    return "$required - $optional";
}

// Pitfall: Not using return type declarations
function add($a, $b) {
    return $a + $b; // Could return int, float, or string
}

// Solution: Use return type declarations
function addTyped(int $a, int $b): int {
    return $a + $b;
}

// Pitfall: Variable function name issues
function getFunctionName($type) {
    $functionName = $type . "Function";
    return $functionName(); // Might not exist
}

// Solution: Check if function exists
function getFunctionName($type) {
    $functionName = $type . "Function";
    if (function_exists($functionName)) {
        return $functionName();
    }
    throw new BadFunctionCallException("Function $functionName does not exist");
}
?>
```

### Parameter and Return Value Issues
```php
<?php
// Pitfall: Not validating parameters
function divide($a, $b) {
    return $a / $b; // Division by zero error
}

// Solution: Validate parameters
function divideSafe($a, $b) {
    if ($b == 0) {
        throw new DivisionByZeroError("Cannot divide by zero");
    }
    return $a / $b;
}

// Pitfall: Not handling null returns
function getUser($id) {
    // Database query that might return null
    return null;
}

$user = getUser(1);
echo $user['name']; // Error: Trying to access array offset on null

// Solution: Handle null returns
function getUserSafe($id): ?array {
    // Database query
    return null;
}

$user = getUserSafe(1);
if ($user !== null) {
    echo $user['name'];
}

// Pitfall: Mixed return types
function getValue($type) {
    switch ($type) {
        case 'int':
            return 42;
        case 'string':
            return "answer";
        case 'array':
            return [1, 2, 3];
        default:
            return null;
    }
}

// Solution: Use union type or consistent return type
function getValueTyped($type): int|string|array|null {
    switch ($type) {
        case 'int':
            return 42;
        case 'string':
            return "answer";
        case 'array':
            return [1, 2, 3];
        default:
            return null;
    }
}

// Pitfall: Not using references when needed
function modifyArray($array) {
    $array[] = "new item";
    return $array;
}

$original = [1, 2, 3];
$result = modifyArray($original);
echo $original[3]; // Notice: Undefined offset

// Solution: Use references when modification is needed
function modifyArrayByReference(array &$array): void {
    $array[] = "new item";
}

modifyArrayByReference($original);
echo $original[3]; // "new item"
?>
```

### Scope and Namespace Issues
```php
<?php
// Pitfall: Assuming global scope
$global_var = "global";

function testScope() {
    echo $global_var; // Notice: Undefined variable
}

// Solution: Use global keyword or $GLOBALS
function testScopeFixed() {
    global $global_var;
    echo $global_var; // "global"
}

// Pitfall: Namespace confusion
namespace A {
    function helper() {
        return "Helper A";
    }
}

namespace B {
    function helper() {
        return "Helper B";
    }
    
    function test() {
        return helper(); // Always calls B\helper()
    }
}

// Solution: Use fully qualified names or use statements
namespace B {
    function testFixed() {
        return \A\helper(); // Calls A\helper()
    }
}

// Pitfall: Function name conflicts with built-in functions
function strlen($str) {
    return "Custom strlen";
}

echo strlen("hello"); // Calls custom function, not built-in

// Solution: Use namespaces
namespace Custom {
    function strlen($str) {
        return "Custom strlen: " . \strlen($str);
    }
}

namespace {
    echo Custom\strlen("hello"); // "Custom strlen: 5"
    echo \strlen("hello"); // 5 (built-in)
}
?>
```

## Summary

PHP functions provide:

**Function Definition:**
- Basic syntax with parameters and return values
- Type declarations and return types (PHP 7+)
- Default and optional parameters
- Variadic parameters (PHP 5.6+)
- Parameter passing by value and reference

**Advanced Features:**
- Anonymous functions and closures
- Arrow functions (PHP 7.4+)
- Higher-order functions
- Function composition and currying
- Memoization and caching

**Built-in Functions:**
- String manipulation functions
- Array functions
- Mathematical functions
- Date and time functions
- File system functions

**Scope and Namespaces:**
- Global and local scope
- Namespaced functions (PHP 5.3+)
- Use statements and aliases
- Function visibility and accessibility

**Best Practices:**
- Single responsibility principle
- Descriptive naming conventions
- Type safety and validation
- Error handling and exceptions
- Performance optimization
- Documentation standards

**Common Pitfalls:**
- Function name conflicts
- Parameter validation issues
- Return type inconsistencies
- Scope and namespace confusion
- Reference behavior misunderstandings

PHP's function system provides powerful tools for organizing code, promoting reusability, and building maintainable applications when following established best practices.
