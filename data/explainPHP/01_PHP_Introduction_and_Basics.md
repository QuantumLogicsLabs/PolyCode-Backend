# PHP Introduction and Basics

## What is PHP?

PHP (Hypertext Preprocessor) is a widely-used, open-source, server-side scripting language designed primarily for web development. It was originally created by **Rasmus Lerdorf** in 1994. PHP code is executed on the server, and the result is sent to the client's browser as plain HTML.

PHP stands out because:
- It is embedded directly into HTML.
- It runs on the server (not the browser).
- It is free and open-source.
- It supports a vast range of databases (MySQL, PostgreSQL, SQLite, etc.).
- It powers over 75% of websites that use a known server-side language, including WordPress, Facebook (originally), and Wikipedia.

---

## How PHP Works

```
Browser Request → Web Server (Apache/Nginx) → PHP Engine → Database (optional) → HTML Response → Browser
```

1. The user visits a `.php` page in their browser.
2. The web server forwards the request to the PHP interpreter.
3. PHP processes the script (runs logic, queries databases, etc.).
4. The output (HTML) is sent back to the browser.

---

## PHP Syntax Basics

### Opening and Closing Tags

```php
<?php
  // PHP code goes here
?>
```

You can also use the short echo tag:

```php
<?= "Hello, World!" ?>
```

### Embedding PHP in HTML

```php
<!DOCTYPE html>
<html>
<body>
  <h1><?php echo "Welcome to PHP!"; ?></h1>
  <p>Today is: <?= date("Y-m-d") ?></p>
</body>
</html>
```

---

## Variables

Variables in PHP start with a `$` sign followed by the variable name.

```php
<?php
$name = "Alice";
$age  = 30;
$pi   = 3.14159;
$isActive = true;

echo $name;   // Alice
echo $age;    // 30
?>
```

### Rules for Variable Names
- Must start with a letter or underscore (`_`), not a number.
- Can contain letters, numbers, and underscores.
- Are **case-sensitive** (`$name` ≠ `$Name`).

---

## Data Types

| Type      | Example                    | Description                        |
|-----------|----------------------------|------------------------------------|
| String    | `"Hello"`                  | Text                               |
| Integer   | `42`                       | Whole numbers                      |
| Float     | `3.14`                     | Decimal numbers                    |
| Boolean   | `true` / `false`           | Logical values                     |
| Array     | `[1, 2, 3]`                | Ordered collection                 |
| Object    | `new ClassName()`          | Instance of a class                |
| NULL      | `null`                     | No value                           |
| Resource  | `fopen("file.txt", "r")`   | Reference to external resource     |

---

## Constants

Constants are defined with `define()` or the `const` keyword and cannot be changed.

```php
<?php
define("SITE_NAME", "MyWebsite");
const VERSION = "1.0.0";

echo SITE_NAME;  // MyWebsite
echo VERSION;    // 1.0.0
?>
```

---

## Comments

```php
<?php
// Single-line comment

# Also a single-line comment

/*
  Multi-line
  comment block
*/

echo "Hello!"; // Inline comment
?>
```

---

## Outputting Data

### `echo` vs `print`

| Feature       | `echo`              | `print`             |
|---------------|---------------------|---------------------|
| Speed         | Slightly faster     | Slightly slower     |
| Return value  | No return value     | Returns `1`         |
| Arguments     | Accepts multiple    | Accepts one only    |

```php
<?php
echo "Hello ", "World!";   // Outputs: Hello World!
print("Hello World!");     // Outputs: Hello World!

// var_dump - shows type and value (useful for debugging)
var_dump(42);          // int(42)
var_dump("PHP");       // string(3) "PHP"
var_dump(true);        // bool(true)

// print_r - human-readable output for arrays
print_r([1, 2, 3]);
?>
```

---

## Operators

### Arithmetic Operators

```php
$a = 10;
$b = 3;

echo $a + $b;   // 13
echo $a - $b;   // 7
echo $a * $b;   // 30
echo $a / $b;   // 3.333...
echo $a % $b;   // 1 (modulus)
echo $a ** $b;  // 1000 (exponentiation)
```

### Assignment Operators

```php
$x = 5;
$x += 3;  // $x = 8
$x -= 2;  // $x = 6
$x *= 4;  // $x = 24
$x /= 6;  // $x = 4
$x %= 3;  // $x = 1
```

### Comparison Operators

```php
$a == $b    // Equal (value only)
$a === $b   // Identical (value AND type)
$a != $b    // Not equal
$a !== $b   // Not identical
$a > $b     // Greater than
$a < $b     // Less than
$a >= $b    // Greater than or equal
$a <= $b    // Less than or equal
$a <=> $b   // Spaceship: -1, 0, or 1
```

### Logical Operators

```php
$a && $b    // AND
$a || $b    // OR
!$a         // NOT
$a and $b   // AND (lower precedence)
$a or $b    // OR  (lower precedence)
$a xor $b   // XOR (exclusive or)
```

### String Operators

```php
$greeting = "Hello" . " " . "World";  // Concatenation: Hello World
$greeting .= "!";                      // Append: Hello World!
```

---

## Type Juggling and Casting

PHP is a loosely typed language — it automatically converts types when needed.

```php
<?php
$result = "5" + 3;    // 8 (string "5" cast to int)
$result = "5 cats" + 2; // 7 (only leading number is used)

// Manual casting
$str  = "3.14";
$num  = (int) $str;     // 3
$flt  = (float) $str;   // 3.14
$bool = (bool) $str;    // true (non-empty string)
$arr  = (array) $str;   // ["3.14"]
?>
```

---

## Getting User Input

### `$_GET` and `$_POST`

```php
<!-- HTML Form -->
<form method="POST" action="process.php">
  <input type="text" name="username">
  <input type="submit">
</form>
```

```php
<?php
// process.php
$username = $_POST['username'] ?? 'Guest';
echo "Hello, " . htmlspecialchars($username);
?>
```

> ⚠️ Always sanitize user input with `htmlspecialchars()`, `filter_var()`, or prepared statements to prevent XSS and SQL injection.

---

## PHP Version History

| Version | Year | Key Features                                   |
|---------|------|------------------------------------------------|
| PHP 3   | 1997 | First widely used version                     |
| PHP 4   | 2000 | Zend Engine 1, improved performance            |
| PHP 5   | 2004 | OOP support, PDO, SimpleXML                    |
| PHP 7   | 2015 | 2x speed boost, scalar type declarations       |
| PHP 8   | 2020 | JIT compiler, named arguments, match expression|
| PHP 8.3 | 2023 | Typed class constants, readonly improvements   |

---

## Summary

PHP is a mature, powerful, and beginner-friendly language for building web applications. Its server-side execution model, deep database integration, and massive ecosystem make it a go-to choice for backend web development. This document covered the foundational concepts: syntax, variables, data types, operators, and basic I/O. The following modules build on this foundation with control structures, functions, OOP, and more.
