# PHP Database, File Handling, Error Handling, and Security

## Working with Databases (PDO)

PHP Data Objects (PDO) is the recommended way to interact with databases. It provides a consistent interface across different database systems (MySQL, PostgreSQL, SQLite, etc.) and supports prepared statements, which protect against SQL injection.

---

### Connecting to a Database

```php
<?php
$dsn = "mysql:host=localhost;dbname=myapp;charset=utf8mb4";
$username = "root";
$password = "secret";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "Connected successfully!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

### Creating Tables

```php
<?php
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id       INT AUTO_INCREMENT PRIMARY KEY,
        name     VARCHAR(100) NOT NULL,
        email    VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created  DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");
?>
```

### INSERT with Prepared Statements

```php
<?php
$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)"
);

$stmt->execute([
    ':name'     => 'Alice',
    ':email'    => 'alice@example.com',
    ':password' => password_hash('secret123', PASSWORD_BCRYPT),
]);

echo "Inserted ID: " . $pdo->lastInsertId(); // 1

// Positional placeholders
$stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->execute(['Bob', 'bob@example.com', password_hash('pass456', PASSWORD_BCRYPT)]);
?>
```

### SELECT Queries

```php
<?php
// Fetch all rows
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(); // Array of associative arrays

foreach ($users as $user) {
    echo $user['name'] . " — " . $user['email'] . "\n";
}

// Fetch with parameters
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute(['alice@example.com']);
$user = $stmt->fetch(); // Single row or false

if ($user) {
    echo "Found: " . $user['name'];
}

// Fetch as object
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);
$user = $stmt->fetchObject(); // stdClass object
echo $user->name;
?>
```

### UPDATE and DELETE

```php
<?php
// UPDATE
$stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
$stmt->execute(['Alice Smith', 1]);
echo "Rows affected: " . $stmt->rowCount();

// DELETE
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([2]);
echo "Deleted: " . $stmt->rowCount() . " row(s)";
?>
```

### Transactions

```php
<?php
try {
    $pdo->beginTransaction();

    $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?")->execute([500, 1]);
    $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?")->execute([500, 2]);

    $pdo->commit();
    echo "Transfer successful!";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Transfer failed: " . $e->getMessage();
}
?>
```

---

## File Handling

PHP provides powerful functions for reading, writing, and manipulating files.

### Reading Files

```php
<?php
// Read entire file as string
$contents = file_get_contents("data.txt");
echo $contents;

// Read file into an array of lines
$lines = file("data.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    echo trim($line) . "\n";
}

// Using fopen for large files (memory efficient)
$handle = fopen("data.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        echo $line;
    }
    fclose($handle);
}
?>
```

### Writing Files

```php
<?php
// Write string to file (overwrites)
file_put_contents("output.txt", "Hello, PHP!\n");

// Append to file
file_put_contents("log.txt", date("Y-m-d H:i:s") . " — Event occurred\n", FILE_APPEND);

// Using fopen for more control
$handle = fopen("report.txt", "w"); // "w" = write, "a" = append
fwrite($handle, "Line 1\n");
fwrite($handle, "Line 2\n");
fclose($handle);
?>
```

### File and Directory Operations

```php
<?php
// Check existence
var_dump(file_exists("data.txt"));   // bool
var_dump(is_file("data.txt"));       // true if it's a file
var_dump(is_dir("uploads/"));        // true if directory

// File info
echo filesize("data.txt");            // Size in bytes
echo filemtime("data.txt");           // Last modified timestamp
echo pathinfo("images/photo.jpg", PATHINFO_EXTENSION); // jpg

// Copy, rename, delete
copy("source.txt", "backup.txt");
rename("old_name.txt", "new_name.txt");
unlink("temp.txt");                   // Delete file

// Directory operations
mkdir("new_folder", 0755, true);      // Recursive
rmdir("empty_folder");

// List directory contents
$files = scandir("uploads/");
foreach ($files as $file) {
    if ($file !== "." && $file !== "..") {
        echo $file . "\n";
    }
}

// Using glob for pattern matching
$phpFiles = glob("src/*.php");
foreach ($phpFiles as $file) {
    echo $file . "\n";
}
?>
```

### File Upload Handling

```php
<?php
// HTML: <input type="file" name="avatar">

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["avatar"])) {
    $file = $_FILES["avatar"];

    // Validate
    $allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    $maxSize = 2 * 1024 * 1024; // 2 MB

    if ($file["error"] !== UPLOAD_ERR_OK) {
        die("Upload error: " . $file["error"]);
    }

    if (!in_array($file["type"], $allowedTypes)) {
        die("Invalid file type.");
    }

    if ($file["size"] > $maxSize) {
        die("File too large.");
    }

    // Generate safe filename
    $ext      = pathinfo($file["name"], PATHINFO_EXTENSION);
    $filename = bin2hex(random_bytes(16)) . "." . $ext;
    $dest     = "uploads/" . $filename;

    if (move_uploaded_file($file["tmp_name"], $dest)) {
        echo "File uploaded: $filename";
    } else {
        die("Failed to move file.");
    }
}
?>
```

---

## Error and Exception Handling

### Error Levels

| Constant        | Level | Description                      |
|-----------------|-------|----------------------------------|
| `E_ERROR`       | 1     | Fatal error — stops execution    |
| `E_WARNING`     | 2     | Non-fatal warning                |
| `E_NOTICE`      | 8     | Minor issue (undefined var, etc.)|
| `E_ALL`         | 32767 | All errors and warnings          |

### Configuring Error Reporting

```php
<?php
// Development (show all errors)
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Production (log errors, hide from users)
error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", "/var/log/php_errors.log");
?>
```

### Try / Catch / Finally

```php
<?php
function divide(float $a, float $b): float {
    if ($b === 0.0) {
        throw new InvalidArgumentException("Division by zero is not allowed.");
    }
    return $a / $b;
}

try {
    echo divide(10, 2) . "\n";  // 5
    echo divide(10, 0) . "\n";  // throws
} catch (InvalidArgumentException $e) {
    echo "Caught: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
} finally {
    echo "This always runs.\n";
}
?>
```

### Custom Exceptions

```php
<?php
class ValidationException extends RuntimeException {
    private array $errors;

    public function __construct(array $errors, string $message = "", int $code = 0) {
        $this->errors = $errors;
        parent::__construct($message ?: "Validation failed", $code);
    }

    public function getErrors(): array {
        return $this->errors;
    }
}

function validateUser(array $data): void {
    $errors = [];
    if (empty($data['name'])) {
        $errors['name'] = "Name is required.";
    }
    if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address.";
    }
    if (!empty($errors)) {
        throw new ValidationException($errors);
    }
}

try {
    validateUser(['name' => '', 'email' => 'not-an-email']);
} catch (ValidationException $e) {
    echo $e->getMessage() . "\n";
    print_r($e->getErrors());
}
?>
```

---

## Security Best Practices

### 1. Preventing SQL Injection

Always use **prepared statements** — never concatenate user input into queries.

```php
// ❌ DANGEROUS
$id = $_GET['id'];
$result = $pdo->query("SELECT * FROM users WHERE id = $id");

// ✅ SAFE
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);
```

### 2. Preventing XSS (Cross-Site Scripting)

Escape all output before displaying it in HTML.

```php
<?php
$userInput = '<script>alert("hacked!")</script>';

// ❌ DANGEROUS
echo $userInput;

// ✅ SAFE
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
// &lt;script&gt;alert(&quot;hacked!&quot;)&lt;/script&gt;
?>
```

### 3. Password Hashing

Never store plain text passwords.

```php
<?php
// Hashing (on registration)
$hash = password_hash('mypassword123', PASSWORD_BCRYPT);

// Verifying (on login)
if (password_verify('mypassword123', $hash)) {
    echo "Password correct!";
}

// Rehashing when algorithm changes
if (password_needs_rehash($hash, PASSWORD_BCRYPT)) {
    $newHash = password_hash('mypassword123', PASSWORD_BCRYPT);
    // Save $newHash to database
}
?>
```

### 4. CSRF Protection

```php
<?php
session_start();

// Generate token (e.g., on page load)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// In form
echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';

// Validate on form submission
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die("CSRF token mismatch.");
}
?>
```

### 5. Input Validation with `filter_var`

```php
<?php
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) {
    die("Invalid email.");
}

$age = filter_var($_POST['age'] ?? 0, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 120]
]);

$url = filter_var($_POST['url'] ?? '', FILTER_VALIDATE_URL);

$clean = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
?>
```

### 6. Sessions and Cookies

```php
<?php
session_start();

// Set session
$_SESSION['user_id'] = 42;
$_SESSION['role']    = 'admin';

// Regenerate session ID after login to prevent fixation
session_regenerate_id(true);

// Destroy session on logout
$_SESSION = [];
session_destroy();

// Secure cookies
setcookie(
    name:     "remember_token",
    value:    $token,
    expires:  time() + 86400 * 30, // 30 days
    path:     "/",
    domain:   "",
    secure:   true,   // HTTPS only
    httponly: true,   // Not accessible via JavaScript
    samesite: "Strict"
);
?>
```

---

## Summary

This module covered the four most critical areas of professional PHP development: database access with PDO (using prepared statements and transactions), file system operations, robust error and exception handling, and essential security practices. Ignoring any of these in a production application can lead to data breaches, data loss, or system failure. Always validate input, escape output, hash passwords, and use prepared statements.
