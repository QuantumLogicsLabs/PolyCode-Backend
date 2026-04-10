# PHP Control Structures and Functions

## Control Structures

Control structures direct the flow of execution in a PHP script. They allow your program to make decisions, repeat actions, and skip code based on conditions.

---

## Conditional Statements

### `if`, `elseif`, `else`

```php
<?php
$age = 20;

if ($age < 13) {
    echo "Child";
} elseif ($age < 18) {
    echo "Teenager";
} elseif ($age < 65) {
    echo "Adult";
} else {
    echo "Senior";
}
// Output: Adult
?>
```

### Ternary Operator

A shorthand for simple `if/else`:

```php
<?php
$score = 75;
$result = ($score >= 50) ? "Pass" : "Fail";
echo $result; // Pass
?>
```

### Null Coalescing Operator (`??`)

Returns the left value if it exists and is not null, otherwise returns the right value.

```php
<?php
$username = $_GET['user'] ?? "Guest";
echo $username; // "Guest" if 'user' not in URL
?>
```

### `match` Expression (PHP 8+)

Similar to `switch` but stricter and more concise:

```php
<?php
$status = 2;

$label = match($status) {
    1 => "Pending",
    2 => "Active",
    3 => "Suspended",
    default => "Unknown",
};

echo $label; // Active
?>
```

Key differences from `switch`:
- Uses strict comparison (`===`).
- No fall-through between cases.
- Must be exhaustive (or have `default`).

### `switch` Statement

```php
<?php
$day = "Monday";

switch ($day) {
    case "Monday":
    case "Tuesday":
        echo "Early week";
        break;
    case "Friday":
        echo "TGIF!";
        break;
    default:
        echo "Midweek";
}
?>
```

---

## Loops

### `while` Loop

Repeats as long as the condition is true.

```php
<?php
$i = 1;
while ($i <= 5) {
    echo "Count: $i\n";
    $i++;
}
?>
```

### `do...while` Loop

Executes the body at least once, then checks the condition.

```php
<?php
$i = 1;
do {
    echo "Number: $i\n";
    $i++;
} while ($i <= 3);
?>
```

### `for` Loop

Best when the number of iterations is known.

```php
<?php
for ($i = 0; $i < 5; $i++) {
    echo "Item $i\n";
}
?>
```

### `foreach` Loop

Designed for iterating over arrays.

```php
<?php
$fruits = ["Apple", "Banana", "Cherry"];

foreach ($fruits as $fruit) {
    echo $fruit . "\n";
}

// With key => value
$person = ["name" => "Alice", "age" => 30, "city" => "Karachi"];

foreach ($person as $key => $value) {
    echo "$key: $value\n";
}
?>
```

### Loop Control

```php
<?php
for ($i = 0; $i < 10; $i++) {
    if ($i === 3) continue; // Skip 3
    if ($i === 7) break;    // Stop at 7
    echo "$i ";
}
// Output: 0 1 2 4 5 6
?>
```

---

## Functions

Functions are reusable blocks of code that perform a specific task.

### Defining and Calling Functions

```php
<?php
function greet($name) {
    return "Hello, $name!";
}

echo greet("Alice"); // Hello, Alice!
echo greet("Bob");   // Hello, Bob!
?>
```

### Default Parameter Values

```php
<?php
function greet($name = "World") {
    return "Hello, $name!";
}

echo greet();        // Hello, World!
echo greet("PHP");   // Hello, PHP!
?>
```

### Type Declarations (PHP 7+)

You can enforce types on parameters and return values:

```php
<?php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

echo add(3, 4); // 7
// add(3.5, 4); // TypeError in strict mode
?>
```

### Named Arguments (PHP 8+)

Pass arguments by name, in any order:

```php
<?php
function createUser(string $name, int $age, string $role = "user"): string {
    return "$name ($age) - $role";
}

echo createUser(age: 25, name: "Alice", role: "admin");
// Alice (25) - admin
?>
```

---

## Variable Scope

### Local Scope

Variables declared inside a function are not accessible outside it.

```php
<?php
function myFunc() {
    $localVar = "I'm local";
    echo $localVar;
}

myFunc(); // I'm local
// echo $localVar; // Error: undefined
?>
```

### Global Scope

Use the `global` keyword to access a variable from outside a function.

```php
<?php
$counter = 0;

function increment() {
    global $counter;
    $counter++;
}

increment();
increment();
echo $counter; // 2
?>
```

### Static Variables

A static variable retains its value between function calls.

```php
<?php
function countCalls() {
    static $count = 0;
    $count++;
    echo "Called $count time(s)\n";
}

countCalls(); // Called 1 time(s)
countCalls(); // Called 2 time(s)
countCalls(); // Called 3 time(s)
?>
```

---

## Anonymous Functions (Closures)

Functions without a name, often assigned to variables or passed as arguments.

```php
<?php
$square = function(int $n): int {
    return $n * $n;
};

echo $square(5); // 25

// Passing a closure as an argument
$numbers = [1, 2, 3, 4, 5];
$doubled = array_map(function($n) { return $n * 2; }, $numbers);
print_r($doubled); // [2, 4, 6, 8, 10]
?>
```

### Closures with `use`

To access outer scope variables inside a closure:

```php
<?php
$prefix = "Hello";

$greet = function($name) use ($prefix) {
    return "$prefix, $name!";
};

echo $greet("Alice"); // Hello, Alice!
?>
```

---

## Arrow Functions (PHP 7.4+)

A shorter syntax for closures. They automatically capture outer scope variables.

```php
<?php
$multiplier = 3;

$multiply = fn($n) => $n * $multiplier;

echo $multiply(5); // 15

// With array_map
$numbers = [1, 2, 3, 4, 5];
$tripled = array_map(fn($n) => $n * 3, $numbers);
print_r($tripled); // [3, 6, 9, 12, 15]
?>
```

---

## Recursive Functions

A function that calls itself.

```php
<?php
function factorial(int $n): int {
    if ($n <= 1) return 1;
    return $n * factorial($n - 1);
}

echo factorial(5); // 120 (5 * 4 * 3 * 2 * 1)
?>
```

```php
<?php
// Fibonacci sequence
function fibonacci(int $n): int {
    if ($n <= 1) return $n;
    return fibonacci($n - 1) + fibonacci($n - 2);
}

for ($i = 0; $i < 10; $i++) {
    echo fibonacci($i) . " ";
}
// 0 1 1 2 3 5 8 13 21 34
?>
```

---

## Built-in String Functions

```php
<?php
$str = "  Hello, PHP World!  ";

echo strlen($str);                    // 21
echo strtoupper($str);                // HELLO, PHP WORLD!
echo strtolower($str);                // hello, php world!
echo trim($str);                      // "Hello, PHP World!"
echo str_replace("PHP", "Great", $str); // Hello, Great World!
echo substr($str, 8, 3);              // PHP
echo strpos($str, "PHP");             // 8
echo str_repeat("Ha", 3);             // HaHaHa
echo str_word_count(trim($str));      // 3
echo strrev("Hello");                 // olleH
echo ucfirst("hello world");          // Hello world
echo ucwords("hello world");          // Hello World
?>
```

---

## Built-in Math Functions

```php
<?php
echo abs(-15);           // 15
echo ceil(4.3);          // 5
echo floor(4.7);         // 4
echo round(4.567, 2);    // 4.57
echo max(3, 8, 1, 5);   // 8
echo min(3, 8, 1, 5);   // 1
echo pow(2, 10);         // 1024
echo sqrt(144);          // 12
echo rand(1, 100);       // Random number 1–100
echo pi();               // 3.14159...
echo number_format(1234567.891, 2, '.', ','); // 1,234,567.89
?>
```

---

## Summary

Control structures (if/else, switch, match, loops) and functions are the backbone of any PHP application. Understanding scope, closures, and arrow functions will allow you to write clean, efficient, and maintainable PHP code. The next module covers PHP arrays in depth.
