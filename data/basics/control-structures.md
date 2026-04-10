# PHP Control Structures

## Conditional Statements

### If Statement
```php
<?php
// Basic if statement
$age = 25;

if ($age >= 18) {
    echo "You are an adult";
}

// If-else statement
if ($age >= 18) {
    echo "You are an adult";
} else {
    echo "You are a minor";
}

// If-elseif-else statement
$grade = 85;

if ($grade >= 90) {
    echo "Grade: A";
} elseif ($grade >= 80) {
    echo "Grade: B";
} elseif ($grade >= 70) {
    echo "Grade: C";
} elseif ($grade >= 60) {
    echo "Grade: D";
} else {
    echo "Grade: F";
}

// Nested if statements
function canDrive($age, $hasLicense) {
    if ($age >= 18) {
        if ($hasLicense) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Alternative syntax (colon syntax)
if ($age >= 18):
    echo "Adult";
else:
    echo "Minor";
endif;
?>
```

### Switch Statement
```php
<?php
// Basic switch statement
$day = "Monday";

switch ($day) {
    case "Monday":
    case "Tuesday":
    case "Wednesday":
    case "Thursday":
    case "Friday":
        echo "Weekday";
        break;
    case "Saturday":
    case "Sunday":
        echo "Weekend";
        break;
    default:
        echo "Unknown day";
}

// Switch with different data types
$value = "42";

switch ($value) {
    case 42:
        echo "Integer 42";
        break;
    case "42":
        echo "String 42";
        break;
    case 42.0:
        echo "Float 42";
        break;
    default:
        echo "Other value";
}

// Switch with multiple values per case
$role = "admin";

switch ($role) {
    case "admin":
    case "moderator":
        echo "Administrator";
        break;
    case "user":
        echo "Regular user";
        break;
    default:
        echo "Unknown role";
}

// Switch with expression (PHP 8.0+)
$message = match($grade) {
    90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100 => "A",
    80, 81, 82, 83, 84, 85, 86, 87, 88, 89 => "B",
    70, 71, 72, 73, 74, 75, 76, 77, 78, 79 => "C",
    60, 61, 62, 63, 64, 65, 66, 67, 68, 69 => "D",
    default => "F"
};

echo $message;

// Alternative syntax
switch ($day):
    case "Monday":
        echo "Monday";
        break;
    case "Tuesday":
        echo "Tuesday";
        break;
    default:
        echo "Other day";
endswitch;
?>
```

### Ternary Operator
```php
<?php
// Basic ternary operator
$age = 25;
$message = $age >= 18 ? "Adult" : "Minor";
echo $message; // "Adult"

// Nested ternary operator
$score = 75;
$result = $score >= 90 ? "Excellent" : ($score >= 70 ? "Good" : "Needs improvement");
echo $result; // "Good"

// Ternary operator with assignment
$username = $_GET['username'] ?? "Guest";
echo $username; // Uses "Guest" if username not set

// Ternary operator with function calls
$isValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? true : false;

// Ternary operator in echo
echo $isLoggedIn ? "Welcome back!" : "Please log in";

// Multiple assignments with ternary
$isAdmin = $role === "admin";
$canEdit = $isAdmin ? true : false;
$canDelete = $isAdmin ? true : false;

// Ternary operator with complex expressions
$price = $isPremium ? $basePrice * 1.2 : $basePrice;
$discount = $isMember ? $price * 0.9 : $price;
?>
```

## Looping Structures

### While Loop
```php
<?php
// Basic while loop
$count = 0;
while ($count < 5) {
    echo "Count: $count\n";
    $count++;
}

// While loop with condition
$numbers = [1, 2, 3, 4, 5];
$index = 0;

while ($index < count($numbers)) {
    echo "Number: {$numbers[$index]}\n";
    $index++;
}

// While loop with boolean condition
$userInput = "";
while ($userInput !== "quit") {
    echo "Enter command (or 'quit' to exit): ";
    $userInput = trim(fgets(STDIN));
    echo "You entered: $userInput\n";
}

// While loop for validation
$age = -1;
while ($age < 0 || $age > 150) {
    echo "Enter valid age (0-150): ";
    $age = (int)fgets(STDIN);
}

echo "Valid age: $age\n";

// Alternative syntax
$count = 0;
while ($count < 5):
    echo "Count: $count\n";
    $count++;
endwhile;
?>
```

### Do-While Loop
```php
<?php
// Basic do-while loop
$count = 0;
do {
    echo "Count: $count\n";
    $count++;
} while ($count < 5);

// Do-while loop with user input
do {
    echo "Enter a positive number (or 0 to quit): ";
    $number = (int)fgets(STDIN);
    echo "You entered: $number\n";
} while ($number > 0);

// Do-while loop for menu
do {
    echo "\nMenu:\n";
    echo "1. Add\n";
    echo "2. Subtract\n";
    echo "3. Multiply\n";
    echo "4. Quit\n";
    echo "Enter choice: ";
    
    $choice = (int)fgets(STDIN);
    
    switch ($choice) {
        case 1:
            echo "Addition selected\n";
            break;
        case 2:
            echo "Subtraction selected\n";
            break;
        case 3:
            echo "Multiplication selected\n";
            break;
        case 4:
            echo "Quitting...\n";
            break;
        default:
            echo "Invalid choice\n";
    }
} while ($choice != 4);

// Alternative syntax
$count = 0;
do {
    echo "Count: $count\n";
    $count++;
} while ($count < 5);
?>
```

### For Loop
```php
<?php
// Basic for loop
for ($i = 1; $i <= 5; $i++) {
    echo "Number: $i\n";
}

// For loop with decrement
for ($i = 10; $i >= 1; $i--) {
    echo "Countdown: $i\n";
}

// For loop with array
$fruits = ["apple", "banana", "cherry"];
for ($i = 0; $i < count($fruits); $i++) {
    echo "Fruit: {$fruits[$i]}\n";
}

// For loop with multiple expressions
for ($i = 0, $j = 10; $i < 5; $i++, $j--) {
    echo "i: $i, j: $j\n";
}

// For loop for multiplication table
for ($i = 1; $i <= 10; $i++) {
    for ($j = 1; $j <= 10; $j++) {
        echo "$i x $j = " . ($i * $j) . "\t";
    }
    echo "\n";
}

// For loop with break and continue
for ($i = 1; $i <= 10; $i++) {
    if ($i == 5) {
        continue; // Skip number 5
    }
    
    if ($i == 8) {
        break; // Stop at number 8
    }
    
    echo "Number: $i\n";
}

// Alternative syntax
for ($i = 1; $i <= 5; $i++):
    echo "Number: $i\n";
endfor;
?>
```

### Foreach Loop
```php
<?php
// Basic foreach loop
$fruits = ["apple", "banana", "cherry"];
foreach ($fruits as $fruit) {
    echo "Fruit: $fruit\n";
}

// Foreach loop with key and value
$user = [
    "name" => "John",
    "age" => 30,
    "email" => "john@example.com"
];

foreach ($user as $key => $value) {
    echo "$key: $value\n";
}

// Foreach loop by reference
$numbers = [1, 2, 3, 4, 5];
foreach ($numbers as &$number) {
    $number *= 2;
}
unset($number); // Break reference

print_r($numbers); // [2, 4, 6, 8, 10]

// Foreach loop with nested arrays
$users = [
    ["name" => "John", "age" => 30],
    ["name" => "Jane", "age" => 25],
    ["name" => "Bob", "age" => 35]
];

foreach ($users as $user) {
    echo "Name: {$user['name']}, Age: {$user['age']}\n";
}

// Foreach loop with objects
class Product {
    public function __construct(public string $name, public float $price) {}
}

$products = [
    new Product("Laptop", 999.99),
    new Product("Mouse", 29.99),
    new Product("Keyboard", 79.99)
];

foreach ($products as $product) {
    echo "Product: {$product->name} - \${$product->price}\n";
}

// Alternative syntax
foreach ($fruits as $fruit):
    echo "Fruit: $fruit\n";
endforeach;
?>
```

## Loop Control

### Break Statement
```php
<?php
// Break from while loop
$count = 0;
while (true) {
    echo "Count: $count\n";
    $count++;
    
    if ($count >= 5) {
        break;
    }
}

// Break from for loop
for ($i = 1; $i <= 10; $i++) {
    echo "Number: $i\n";
    
    if ($i == 5) {
        break;
    }
}

// Break from foreach loop
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
foreach ($numbers as $number) {
    echo "Number: $number\n";
    
    if ($number > 5) {
        break;
    }
}

// Break with levels (nested loops)
for ($i = 1; $i <= 3; $i++) {
    for ($j = 1; $j <= 3; $j++) {
        echo "i: $i, j: $j\n";
        
        if ($i == 2 && $j == 2) {
            break 2; // Break out of both loops
        }
    }
}

// Break in switch statement
$day = "Monday";
switch ($day) {
    case "Monday":
        echo "It's Monday!\n";
        break;
    case "Tuesday":
        echo "It's Tuesday!\n";
        break;
    default:
        echo "It's another day!\n";
}

// Break with label (PHP 8.0+)
outer:
for ($i = 1; $i <= 3; $i++) {
    for ($j = 1; $j <= 3; $j++) {
        echo "i: $i, j: $j\n";
        
        if ($i == 2 && $j == 2) {
            break outer;
        }
    }
}
?>
```

### Continue Statement
```php
<?php
// Continue in for loop
for ($i = 1; $i <= 10; $i++) {
    if ($i % 2 == 0) {
        continue; // Skip even numbers
    }
    
    echo "Odd number: $i\n";
}

// Continue in while loop
$count = 0;
while ($count < 10) {
    $count++;
    
    if ($count % 3 == 0) {
        continue; // Skip multiples of 3
    }
    
    echo "Count: $count\n";
}

// Continue in foreach loop
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
foreach ($numbers as $number) {
    if ($number < 5) {
        continue; // Skip numbers less than 5
    }
    
    echo "Number: $number\n";
}

// Continue with levels (nested loops)
for ($i = 1; $i <= 3; $i++) {
    for ($j = 1; $j <= 3; $j++) {
        if ($j == 2) {
            continue 2; // Skip to next iteration of outer loop
        }
        
        echo "i: $i, j: $j\n";
    }
}

// Continue in switch with while loop
$i = 0;
while ($i < 10) {
    $i++;
    
    switch ($i % 3) {
        case 0:
            echo "$i is divisible by 3\n";
            break;
        case 1:
            continue 2; // Continue outer while loop
        case 2:
            echo "$i has remainder 2\n";
            break;
    }
}
?>
```

### Goto Statement (PHP 5.3+)
```php
<?php
// Basic goto usage
goto start;
echo "This will be skipped\n";
start:
echo "This will be executed\n";

// Goto in loop
for ($i = 0; $i < 10; $i++) {
    if ($i == 5) {
        goto end;
    }
    echo "Number: $i\n";
}

end:
echo "Loop ended\n";

// Goto for error handling
function processFile($filename) {
    if (!file_exists($filename)) {
        goto error;
    }
    
    $content = file_get_contents($filename);
    if ($content === false) {
        goto error;
    }
    
    return $content;
    
    error:
    return false;
}

// Goto for complex logic
$i = 0;
start_loop:
if ($i < 5) {
    echo "Iteration: $i\n";
    $i++;
    goto start_loop;
}

echo "Loop completed\n";

// Note: Goto should be used sparingly as it can make code hard to read
?>
```

## Exception Handling

### Try-Catch Blocks
```php
<?php
// Basic try-catch
try {
    $result = 10 / 0;
} catch (DivisionByZeroError $e) {
    echo "Error: " . $e->getMessage();
}

// Multiple catch blocks
try {
    $file = fopen("nonexistent.txt", "r");
    $content = fread($file, 1024);
    fclose($file);
} catch (FileNotFoundException $e) {
    echo "File not found: " . $e->getMessage();
} catch (Exception $e) {
    echo "General error: " . $e->getMessage();
}

// Try-catch-finally
try {
    $connection = new PDO("mysql:host=localhost", "user", "pass");
    $connection->exec("SELECT * FROM users");
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} finally {
    if (isset($connection)) {
        $connection = null;
    }
    echo "Cleanup completed\n";
}

// Throwing exceptions
function divide($a, $b) {
    if ($b == 0) {
        throw new InvalidArgumentException("Cannot divide by zero");
    }
    return $a / $b;
}

try {
    $result = divide(10, 0);
} catch (InvalidArgumentException $e) {
    echo "Caught exception: " . $e->getMessage();
}

// Custom exception class
class CustomException extends Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function getDetailedMessage() {
        return "Custom Error: " . $this->getMessage() . " (Code: " . $this->getCode() . ")";
    }
}

try {
    throw new CustomException("Something went wrong");
} catch (CustomException $e) {
    echo $e->getDetailedMessage();
}

// Exception handling in functions
function processUser($userData) {
    try {
        if (empty($userData['name'])) {
            throw new InvalidArgumentException("Name is required");
        }
        
        if (empty($userData['email'])) {
            throw new InvalidArgumentException("Email is required");
        }
        
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }
        
        return "User processed successfully";
        
    } catch (InvalidArgumentException $e) {
        return "Validation error: " . $e->getMessage();
    } catch (Exception $e) {
        return "Processing error: " . $e->getMessage();
    }
}
?>
```

## Advanced Control Structures

### Match Expression (PHP 8.0+)
```php
<?php
// Basic match expression
$statusCode = 200;
$message = match($statusCode) {
    200 => "OK",
    404 => "Not Found",
    500 => "Internal Server Error",
    default => "Unknown Status"
};

echo $message; // "OK"

// Match with multiple values
$role = "admin";
$permissions = match($role) {
    "admin", "moderator" => "Full access",
    "editor" => "Edit access",
    "viewer" => "Read access",
    default => "No access"
};

// Match with conditions
$age = 25;
$category = match(true) {
    $age < 18 => "Minor",
    $age >= 18 && $age < 65 => "Adult",
    $age >= 65 => "Senior",
    default => "Unknown"
};

// Match with expressions
$price = 100;
$discount = match($price) {
    $price >= 1000 => 0.2,
    $price >= 500 => 0.1,
    $price >= 100 => 0.05,
    default => 0
};

$finalPrice = $price * (1 - $discount);

// Match in functions
function calculateShipping($weight, $distance) {
    return match(true) {
        $weight <= 1 && $distance <= 100 => 5.99,
        $weight <= 5 && $distance <= 100 => 9.99,
        $weight <= 10 && $distance <= 100 => 14.99,
        default => 19.99
    };
}

// Match with strict comparison
$value = "42";
$result = match($value) {
    42 => "Integer 42",
    "42" => "String 42",
    42.0 => "Float 42",
    default => "Other"
};
?>
```

### Yield and Generators
```php
<?php
// Basic generator function
function countTo($max) {
    for ($i = 1; $i <= $max; $i++) {
        yield $i;
    }
}

foreach (countTo(5) as $number) {
    echo "Number: $number\n";
}

// Generator with key-value pairs
function rangeWithKeys($start, $end) {
    for ($i = $start; $i <= $end; $i++) {
        yield $i => $i * $i;
    }
}

foreach (rangeWithKeys(1, 5) as $key => $value) {
    echo "$key squared is $value\n";
}

// Generator with file processing
function readLines($filename) {
    $file = fopen($filename, 'r');
    
    if (!$file) {
        return;
    }
    
    while (($line = fgets($file)) !== false) {
        yield trim($line);
    }
    
    fclose($file);
}

// Generator for memory-efficient processing
function processLargeDataset() {
    for ($i = 1; $i <= 1000000; $i++) {
        if ($i % 1000 === 0) {
            yield $i; // Only yield every 1000th number
        }
    }
}

// Generator with return value (PHP 7.0+)
function getFirstEvenNumber(array $numbers) {
    foreach ($numbers as $number) {
        if ($number % 2 === 0) {
            yield $number;
            return $number;
        }
    }
}

$generator = getFirstEvenNumber([1, 3, 5, 7, 8, 9, 10]);
foreach ($generator as $number) {
    echo "First even number: $number\n";
}

$returnValue = $generator->getReturn();
echo "Return value: $returnValue\n";

// Generator delegation (PHP 7.0+)
function combineGenerators() {
    yield from countTo(3);
    yield from countTo(6);
}

foreach (combineGenerators() as $number) {
    echo "Combined: $number\n";
}
?>
```

### Declare Statements
```php
<?php
// Strict types
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

// This will work
$result = add(5, 3);

// This will throw TypeError
// $result = add(5, "3");

// Ticks (for debugging)
declare(ticks=1);

function tickHandler() {
    echo "Tick executed\n";
}

register_tick_function('tickHandler');

// This will trigger tick handler after each statement
$a = 1;
$b = 2;
$c = $a + $b;

// Encoding declaration
declare(encoding='UTF-8');

// This ensures string encoding is handled correctly
$string = "Hello, 世界!";
echo $string;

// Combined declare statements
declare(strict_types=1, encoding='UTF-8');

function processString(string $text): string {
    return strtoupper($text);
}

// Enable assertions (PHP 7.0+)
ini_set('zend.assertions', 1);
assert_options(ASSERT_ACTIVE, 1);

function divide($a, $b) {
    assert($b != 0, "Division by zero not allowed");
    return $a / $b;
}

// This will throw AssertionError if assertion fails
// $result = divide(10, 0);
?>
```

## Best Practices

### Conditional Best Practices
```php
<?php
// Good: Use guard clauses
function processUser($user) {
    if (!$user) {
        return "Invalid user";
    }
    
    if (!$user['active']) {
        return "User is inactive";
    }
    
    if (!$user['verified']) {
        return "User is not verified";
    }
    
    return "User processed successfully";
}

// Bad: Deep nesting
function processUserBad($user) {
    if ($user) {
        if ($user['active']) {
            if ($user['verified']) {
                return "User processed successfully";
            } else {
                return "User is not verified";
            }
        } else {
            return "User is inactive";
        }
    } else {
        return "Invalid user";
    }
}

// Good: Use match expression (PHP 8.0+)
function getUserStatus($user) {
    return match(true) {
        !$user => "Invalid user",
        !$user['active'] => "User is inactive",
        !$user['verified'] => "User is not verified",
        default => "User processed successfully"
    };
}

// Good: Use strict comparison when appropriate
function checkValue($value) {
    if ($value === "0") {
        return "Exactly zero";
    }
    
    if ($value == 0) {
        return "Zero-like value";
    }
    
    return "Non-zero value";
}

// Good: Use type declarations
function calculateTotal(float $price, int $quantity, float $taxRate = 0.08): float {
    $subtotal = $price * $quantity;
    $tax = $subtotal * $taxRate;
    return $subtotal + $tax;
}

// Good: Handle all cases in switch/match
function getDayType($day) {
    return match(strtolower($day)) {
        'monday', 'tuesday', 'wednesday', 'thursday', 'friday' => 'Weekday',
        'saturday', 'sunday' => 'Weekend',
        default => 'Unknown day'
    };
}
?>
```

### Loop Best Practices
```php
<?php
// Good: Use appropriate loop type
$numbers = [1, 2, 3, 4, 5];

// For indexed arrays
for ($i = 0; $i < count($numbers); $i++) {
    echo "Index $i: {$numbers[$i]}\n";
}

// For associative arrays
$user = ['name' => 'John', 'age' => 30];
foreach ($user as $key => $value) {
    echo "$key: $value\n";
}

// For unknown iteration count
$handle = fopen('file.txt', 'r');
while (($line = fgets($handle)) !== false) {
    echo trim($line) . "\n";
}
fclose($handle);

// Good: Use generators for large datasets
function processLargeFile($filename) {
    $file = fopen($filename, 'r');
    
    while (($line = fgets($file)) !== false) {
        yield trim($line);
    }
    
    fclose($file);
}

foreach (processLargeFile('large_file.txt') as $line) {
    // Process line without loading entire file into memory
}

// Good: Use break and continue appropriately
foreach ($items as $item) {
    if ($item['skip']) {
        continue; // Skip processing this item
    }
    
    if ($item['stop']) {
        break; // Stop processing entirely
    }
    
    // Process item
}

// Good: Use meaningful loop variables
for ($userIndex = 0; $userIndex < count($users); $userIndex++) {
    $currentUser = $users[$userIndex];
    // Process current user
}

// Good: Limit loop iterations
function findUser(array $users, $searchId) {
    foreach ($users as $index => $user) {
        if ($user['id'] === $searchId) {
            return $user;
        }
        
        if ($index > 1000) { // Prevent infinite loops
            break;
        }
    }
    
    return null;
}
?>
```

### Exception Handling Best Practices
```php
<?php
// Good: Use specific exception types
class DatabaseException extends Exception {}
class ValidationException extends Exception {}

function saveUser($userData) {
    try {
        validateUserData($userData);
        saveToDatabase($userData);
    } catch (ValidationException $e) {
        return "Validation error: " . $e->getMessage();
    } catch (DatabaseException $e) {
        return "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        return "Unexpected error: " . $e->getMessage();
    }
}

// Good: Create custom exceptions
class UserNotFoundException extends Exception {
    public function __construct($userId, $message = null) {
        $message = $message ?? "User with ID $userId not found";
        parent::__construct($message);
    }
}

function getUser($userId) {
    $user = findUserInDatabase($userId);
    
    if (!$user) {
        throw new UserNotFoundException($userId);
    }
    
    return $user;
}

// Good: Use finally for cleanup
function processFile($filename) {
    $file = null;
    
    try {
        $file = fopen($filename, 'r');
        $content = fread($file, filesize($filename));
        return $content;
    } finally {
        if ($file) {
            fclose($file);
        }
    }
}

// Good: Log exceptions
function handleException(Exception $e) {
    error_log("Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    // Return user-friendly message
    return "An error occurred. Please try again later.";
}

// Good: Don't catch exceptions you can't handle
function riskyOperation() {
    // Let exceptions bubble up to where they can be properly handled
    return $externalService->call();
}
?>
```

## Common Pitfalls

### Conditional Pitfalls
```php
<?php
// Pitfall: Assignment instead of comparison
if ($x = 5) { // Always true if assignment succeeds
    echo "This will always execute";
}

// Solution: Use explicit comparison
if ($x == 5) {
    echo "This executes only if x equals 5";
}

// Pitfall: Loose comparison issues
if ("0" == false) {
    echo "This is true in PHP!"; // Unexpected
}

// Solution: Use strict comparison
if ("0" === false) {
    echo "This is false";
}

// Pitfall: Missing break in switch
$day = "Monday";
switch ($day) {
    case "Monday":
        echo "Monday";
    case "Tuesday":
        echo "Tuesday"; // This will also execute!
        break;
}

// Solution: Always use break
switch ($day) {
    case "Monday":
        echo "Monday";
        break;
    case "Tuesday":
        echo "Tuesday";
        break;
}

// Pitfall: Complex nested conditions
if ($user && $user->isActive() && $user->hasPermission() && $user->isValid() && $user->isVerified()) {
    // Too many conditions
}

// Solution: Break down into smaller conditions
function canUserAccessResource($user) {
    return $user && 
           $user->isActive() && 
           $user->hasPermission() && 
           $user->isValid() && 
           $user->isVerified();
}

if (canUserAccessResource($user)) {
    // Process resource
}
?>
```

### Loop Pitfalls
```php
<?php
// Pitfall: Infinite loop
$count = 0;
while ($count < 10) {
    echo $count;
    // Forgot to increment $count!
}

// Solution: Ensure loop termination
$count = 0;
while ($count < 10) {
    echo $count;
    $count++; // Don't forget to increment!
}

// Pitfall: Modifying array while iterating
$numbers = [1, 2, 3, 4, 5];
foreach ($numbers as $key => $value) {
    if ($value % 2 == 0) {
        unset($numbers[$key]); // This can cause issues
    }
}

// Solution: Create new array or use array_filter
$numbers = [1, 2, 3, 4, 5];
$filtered = array_filter($numbers, fn($n) => $n % 2 !== 0);

// Pitfall: Using count() in loop condition
$array = [1, 2, 3, 4, 5];
for ($i = 0; $i < count($array); $i++) {
    echo $array[$i];
    $array[] = $i + 10; // This can cause infinite loop!
}

// Solution: Store count before loop
$array = [1, 2, 3, 4, 5];
$count = count($array);
for ($i = 0; $i < $count; $i++) {
    echo $array[$i];
    $array[] = $i + 10;
}

// Pitfall: Off-by-one errors
$items = [1, 2, 3, 4, 5];
for ($i = 0; $i <= count($items); $i++) { // Should be < not <=
    echo $items[$i]; // Error on last iteration
}

// Solution: Use correct bounds
for ($i = 0; $i < count($items); $i++) {
    echo $items[$i];
}
?>
```

### Exception Handling Pitfalls
```php
<?php
// Pitfall: Catching too broadly
try {
    riskyOperation();
} catch (Exception $e) {
    // Swallowing all exceptions
}

// Solution: Catch specific exceptions
try {
    riskyOperation();
} catch (SpecificException $e) {
    // Handle specific exception
}

// Pitfall: Not handling exceptions properly
try {
    riskyOperation();
} catch (Exception $e) {
    echo "Error occurred"; // Not logging or reporting
}

// Solution: Proper error handling
try {
    riskyOperation();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    return "An error occurred. Please try again.";
}

// Pitfall: Using exceptions for control flow
function findUser($id) {
    $user = getUserFromDatabase($id);
    
    if (!$user) {
        throw new Exception("User not found"); // Don't use exceptions for normal flow
    }
    
    return $user;
}

// Solution: Return null or special value
function findUser($id) {
    $user = getUserFromDatabase($id);
    return $user; // Return null if not found
}

// Pitfall: Not cleaning up resources
function processFile($filename) {
    $file = fopen($filename, 'r');
    
    try {
        $content = fread($file, filesize($filename));
        return $content;
    } catch (Exception $e) {
        return null;
    }
    // File handle not closed!
}

// Solution: Use finally clause
function processFile($filename) {
    $file = null;
    
    try {
        $file = fopen($filename, 'r');
        $content = fread($file, filesize($filename));
        return $content;
    } catch (Exception $e) {
        return null;
    } finally {
        if ($file) {
            fclose($file);
        }
    }
}
?>
```

## Summary

PHP control structures provide:

**Conditional Statements:**
- If, if-else, if-elseif-else statements
- Switch statements with multiple cases
- Ternary operator for concise conditions
- Match expressions (PHP 8.0+)

**Looping Structures:**
- While loops for condition-based iteration
- Do-while loops for post-condition loops
- For loops for counter-based iteration
- Foreach loops for array iteration

**Loop Control:**
- Break statement for exiting loops
- Continue statement for skipping iterations
- Goto statement (use sparingly)
- Nested loop control with levels

**Exception Handling:**
- Try-catch blocks for error handling
- Multiple catch blocks
- Finally clauses for cleanup
- Custom exception classes

**Advanced Features:**
- Match expressions for pattern matching
- Generators for memory-efficient iteration
- Yield statements for lazy evaluation
- Declare statements for configuration

**Best Practices:**
- Use guard clauses to reduce nesting
- Choose appropriate loop types
- Handle exceptions specifically and properly
- Use strict comparisons when needed
- Log errors and provide user feedback

**Common Pitfalls:**
- Assignment vs comparison confusion
- Loose comparison surprises
- Infinite loops and off-by-one errors
- Improper exception handling
- Resource cleanup issues

PHP's control structures provide comprehensive tools for managing program flow, making it possible to write clean, efficient, and maintainable code when following established best practices.
