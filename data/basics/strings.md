# PHP Strings

## String Basics

### Creating Strings
```php
<?php
// Single quoted strings
$name = 'John Doe';
$message = 'Hello, World!';
$path = 'C:\Users\Documents\file.txt';

// Double quoted strings (variable interpolation)
$age = 30;
$greeting = "Hello, $name! You are $age years old.";
$email = "john@example.com";

// Escaping in double quoted strings
$quote = "He said, \"Hello, World!\"";
$tab = "First\tSecond";
$newline = "Line 1\nLine 2";
$dollar = "The price is \$100";

// Heredoc syntax
$html = <<<HTML
<!DOCTYPE html>
<html>
<head><title>My Page</title></head>
<body>
    <h1>Welcome to My Website</h1>
    <p>This is a paragraph with $name.</p>
</body>
</html>
HTML;

// Nowdoc syntax (no variable interpolation)
$text = <<<'TEXT'
This is a nowdoc string.
No variable interpolation here.
$name will not be replaced.
TEXT;

// Concatenation
$full_name = $first_name . ' ' . $last_name;
$message = "Hello, " . $name . "!";

// String interpolation with complex expressions
$price = 19.99;
$quantity = 3;
$total = "Total: $" . ($price * $quantity);

// Using curly braces for complex interpolation
$user = ["name" => "John", "age" => 30];
$greeting = "Hello, {$user['name']}!";

// Method calls in strings (PHP 8.0+)
class User {
    public function __construct(public string $name) {}
    public function getName(): string {
        return $this->name;
    }
}

$user = new User("John");
$message = "Hello, {$user->getName()}!";
?>
```

### String Length and Counting
```php
<?php
// String length
$text = "Hello, World!";
$length = strlen($text); // 13

// Multibyte string length (UTF-8)
$utf8_text = "Hello, 世界!";
$length = mb_strlen($utf8_text, 'UTF-8'); // 9

// Word count
$sentence = "Hello, World! How are you today?";
$word_count = str_word_count($sentence); // 5

// Word count with array return
$words = str_word_count($sentence, 1); // ["Hello", "World", "How", "are", "you", "today"]

// Word count with positions
$words_with_positions = str_word_count($sentence, 2); // [0 => "Hello", 7 => "World", ...]

// Character count
$char_count = count_chars($text); // Returns array with character frequencies

// Specific character count
$char_count = count_chars($text, 1); // Returns array with each character count

// Count substring occurrences
$text = "hello world hello php";
$count = substr_count($text, "hello"); // 2

// Case-insensitive count
$count = substr_count($text, "hello"); // 2 (case-sensitive)
// For case-insensitive, use strtolower first
$count = substr_count(strtolower($text), "hello"); // 2
?>
```

## String Manipulation

### Case Conversion
```php
<?php
// Convert to uppercase
$text = "Hello, World!";
$upper = strtoupper($text); // "HELLO, WORLD!"

// Convert to lowercase
$lower = strtolower($text); // "hello, world!"

// Convert first character to uppercase
$first_upper = ucfirst($text); // "Hello, World!"

// Convert first character of each word to uppercase
$words_upper = ucwords($text); // "Hello, World!"

// Multibyte case conversion (UTF-8)
$utf8_text = "hello, 世界!";
$upper = mb_strtoupper($utf8_text, 'UTF-8'); // "HELLO, 世界!"
$lower = mb_strtolower($utf8_text, 'UTF-8'); // "hello, 世界!"

// Case conversion with custom rules
function titleCase(string $string): string {
    return ucwords(strtolower($string));
}

$title = titleCase("HELLO WORLD"); // "Hello World"

// Convert string to title case with exceptions
function smartTitleCase(string $string): string {
    $exceptions = ['and', 'or', 'the', 'a', 'an', 'in', 'on', 'at', 'to', 'for'];
    $words = explode(' ', strtolower($string));
    
    foreach ($words as $key => $word) {
        if ($key === 0 || !in_array($word, $exceptions)) {
            $words[$key] = ucfirst($word);
        }
    }
    
    return implode(' ', $words);
}

$title = smartTitleCase("THE QUICK BROWN FOX JUMPS OVER THE LAZY DOG");
// "The Quick Brown Fox Jumps Over the Lazy Dog"
?>
```

### String Trimming and Padding
```php
<?php
// Remove whitespace from both ends
$text = "   Hello, World!   ";
$trimmed = trim($text); // "Hello, World!"

// Remove specific characters
$text = "***Hello, World!***";
$trimmed = trim($text, "*"); // "Hello, World!"

// Remove from left
$trimmed_left = ltrim($text); // "Hello, World!***"
$trimmed_left_chars = ltrim($text, "*"); // "Hello, World!***"

// Remove from right
$trimmed_right = rtrim($text); // "***Hello, World!"
$trimmed_right_chars = rtrim($text, "*"); // "***Hello, World!"

// Multibyte trimming
$utf8_text = "   你好，世界！   ";
$trimmed = trim($utf8_text); // "你好，世界！"

// Pad string to fixed length
$text = "Hello";
$padded = str_pad($text, 10, " ", STR_PAD_RIGHT); // "Hello     "
$padded_left = str_pad($text, 10, " ", STR_PAD_LEFT); // "     Hello"
$padded_both = str_pad($text, 10, " ", STR_PAD_BOTH); // "   Hello   "

// Pad with specific character
$padded = str_pad($text, 10, "*", STR_PAD_RIGHT); // "Hello*****"

// Multibyte padding
function mb_str_pad(string $string, int $length, string $pad_string = " ", int $pad_type = STR_PAD_RIGHT, string $encoding = 'UTF-8'): string {
    $pad_length = $length - mb_strlen($string, $encoding);
    
    if ($pad_length <= 0) {
        return $string;
    }
    
    $pad = str_repeat($pad_string, ceil($pad_length / mb_strlen($pad_string, $encoding)));
    $pad = mb_substr($pad, 0, $pad_length, $encoding);
    
    switch ($pad_type) {
        case STR_PAD_LEFT:
            return $pad . $string;
        case STR_PAD_RIGHT:
            return $string . $pad;
        case STR_PAD_BOTH:
            $left_pad = mb_substr($pad, 0, floor($pad_length / 2), $encoding);
            $right_pad = mb_substr($pad, floor($pad_length / 2), $encoding);
            return $left_pad . $string . $right_pad;
        default:
            return $string;
    }
}
?>
```

### String Replacement
```php
<?php
// Simple replacement
$text = "Hello, World!";
$replaced = str_replace("World", "PHP", $text); // "Hello, PHP!"

// Multiple replacements
$text = "The cat sat on the mat.";
$replacements = [
    "cat" => "dog",
    "mat" => "rug"
];
$replaced = str_replace(array_keys($replacements), array_values($replacements), $text);
// "The dog sat on the rug."

// Case-insensitive replacement
$text = "Hello, hello, HELLO!";
$replaced = str_ireplace("hello", "hi", $text); // "hi, hi, hi!"

// Replace first occurrence
$text = "Hello, World! Hello, Universe!";
$replaced = preg_replace('/hello/i', 'Hi', $text, 1); // "Hi, World! Hello, Universe!"

// Replace with limit
$text = "apple, banana, apple, cherry";
$replaced = str_replace("apple", "orange", $text, 2); // "orange, banana, orange, cherry"

// Substring replacement
$text = "Hello, World!";
$replaced = substr_replace($text, "PHP", 7, 5); // "Hello, PHP!"

// Insert at position
$text = "Hello World!";
$inserted = substr_replace($text, "Beautiful ", 6, 0); // "Hello Beautiful World!"

// Remove substring
$text = "Hello, Beautiful World!";
$removed = substr_replace($text, "", 7, 10); // "Hello, World!"

// Replace with callback (PHP 7.0+)
$text = "The price is $100";
$replaced = preg_replace_callback('/\$(\d+)/', function($matches) {
    return '$' . number_format($matches[1]);
}, $text); // "The price is $100"

// Multiple replacements with callback
$text = "user123@example.com and admin456@domain.org";
$replaced = preg_replace_callback('/(\w+)@(\w+\.\w+)/', function($matches) {
    return $matches[1] . ' at ' . $matches[2];
}, $text); // "user123 at example.com and admin456 at domain.org"
?>
```

## String Searching

### Finding Substrings
```php
<?php
// Find position of substring
$text = "Hello, World!";
$position = strpos($text, "World"); // 7
$position = strpos($text, "world"); // false (case-sensitive)

// Case-insensitive search
$position = stripos($text, "world"); // 7

// Find last occurrence
$text = "Hello, World! Hello, Universe!";
$last_position = strrpos($text, "Hello"); // 14
$last_position = strripos($text, "hello"); // 14 (case-insensitive)

// Find substring from position
$text = "Hello, World! Hello, Universe!";
$position = strpos($text, "Hello", 1); // 14 (starts searching from position 1)

// Check if substring exists
$text = "Hello, World!";
$exists = strpos($text, "World") !== false; // true
$exists = strpos($text, "world") !== false; // false

// Multibyte string search
$utf8_text = "你好，世界！";
$position = mb_strpos($utf8_text, "世界", 0, 'UTF-8'); // 3

// Find all occurrences
$text = "Hello, World! Hello, Universe!";
$positions = [];
$offset = 0;
$search = "Hello";

while (($position = strpos($text, $search, $offset)) !== false) {
    $positions[] = $position;
    $offset = $position + 1;
}

print_r($positions); // [0, 14]

// Find substring with regex
$text = "The price is $100 and $200";
preg_match('/\$(\d+)/', $text, $matches);
print_r($matches); // ["$100", "100"]

// Find all matches with regex
preg_match_all('/\$(\d+)/', $text, $matches);
print_r($matches); // [["$100", "$200"], ["100", "200"]]
?>
```

### Pattern Matching
```php
<?php
// Basic regex matching
$text = "Hello, World!";
$pattern = '/World/';
if (preg_match($pattern, $text)) {
    echo "Pattern found!";
}

// Case-insensitive matching
$pattern = '/world/i';
if (preg_match($pattern, $text)) {
    echo "Pattern found (case-insensitive)!";
}

// Extract matches
$text = "The price is $100";
$pattern = '/\$(\d+)/';
if (preg_match($pattern, $text, $matches)) {
    echo "Price: " . $matches[1]; // 100
}

// Multiple matches
$text = "Prices: $100, $200, $300";
$pattern = '/\$(\d+)/';
preg_match_all($pattern, $text, $matches);
print_r($matches[1]); // ["100", "200", "300"]

// Named groups
$text = "John Doe, Age: 30, Email: john@example.com";
$pattern = '/(?P<name>\w+ \w+), Age: (?P<age>\d+), Email: (?P<email>\S+)/';
if (preg_match($pattern, $text, $matches)) {
    echo "Name: " . $matches['name']; // John Doe
    echo "Age: " . $matches['age']; // 30
    echo "Email: " . $matches['email']; // john@example.com
}

// Email validation
$email = "user@example.com";
$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
if (preg_match($pattern, $email)) {
    echo "Valid email!";
}

// Phone number validation
$phone = "(123) 456-7890";
$pattern = '/^\(?(\d{3})\)?[-\s]?(\d{3})[-\s]?(\d{4})$/';
if (preg_match($pattern, $phone, $matches)) {
    echo "Area code: " . $matches[1]; // 123
    echo "Prefix: " . $matches[2]; // 456
    echo "Line number: " . $matches[3]; // 7890
}

// URL validation
$url = "https://www.example.com/path/to/page?param=value";
$pattern = '/^https?:\/\/(www\.)?([^\/]+)\/(.*)$/';
if (preg_match($pattern, $url, $matches)) {
    echo "Domain: " . $matches[2]; // example.com
    echo "Path: " . $matches[3]; // path/to/page?param=value
}

// Multibyte regex (UTF-8)
$utf8_text = "你好，世界！";
$pattern = '/世界/u';
if (preg_match($pattern, $utf8_text)) {
    echo "Found Chinese characters!";
}
?>
```

## String Extraction

### Substring Operations
```php
<?php
// Extract substring by position
$text = "Hello, World!";
$substring = substr($text, 7, 5); // "World"
$substring = substr($text, 7); // "World!"
$substring = substr($text, -6); // "World!"
$substring = substr($text, 0, 5); // "Hello"

// Multibyte substring
$utf8_text = "Hello, 世界!";
$substring = mb_substr($utf8_text, 7, 2, 'UTF-8'); // "世界"

// Extract first n characters
$text = "Hello, World!";
$first_5 = substr($text, 0, 5); // "Hello"

// Extract last n characters
$last_6 = substr($text, -6); // "World!"

// Extract between two strings
$text = "Hello, [World]! Universe!";
$start = strpos($text, "[");
$end = strpos($text, "]");
if ($start !== false && $end !== false) {
    $extracted = substr($text, $start + 1, $end - $start - 1); // "World"
}

// Extract words
$text = "Hello, World! How are you?";
$words = str_word_count($text, 1); // ["Hello", "World", "How", "are", "you"]

// Extract numbers
$text = "The price is $100.99 and $200.50";
preg_match_all('/\d+\.?\d*/', $text, $matches);
$numbers = $matches[0]; // ["100", "99", "200", "50"]

// Extract emails
$text = "Contact us at info@example.com or support@company.org";
preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches);
$emails = $matches[0]; // ["info@example.com", "support@company.org"]

// Extract URLs
$text = "Visit https://www.example.com or http://company.org";
preg_match_all('/https?:\/\/[^\s]+/', $text, $matches);
$urls = $matches[0]; // ["https://www.example.com", "http://company.org"]
?>
```

### String Splitting
```php
<?php
// Split by delimiter
$text = "apple,banana,cherry,date";
$fruits = explode(",", $text); // ["apple", "banana", "cherry", "date"]

// Split with limit
$parts = explode(",", $text, 2); // ["apple", "banana,cherry,date"]

// Split by whitespace
$text = "Hello    World!   How are   you?";
$words = preg_split('/\s+/', $text); // ["Hello", "World!", "How", "are", "you?"]

// Split into characters
$text = "Hello";
$chars = str_split($text); // ["H", "e", "l", "l", "o"]

// Multibyte character split
$utf8_text = "你好世界";
$chars = mb_str_split($utf8_text, 1, 'UTF-8'); // ["你", "好", "世", "界"]

// Split by multiple delimiters
$text = "apple,banana;cherry date";
$fruits = preg_split('/[,; ]/', $text, -1, PREG_SPLIT_NO_EMPTY); // ["apple", "banana", "cherry", "date"]

// Split and keep delimiters
$text = "apple,banana,cherry";
$parts = preg_split('/(,)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
// ["apple", ",", "banana", ",", "cherry"]

// Split by pattern with groups
$text = "name:John,age:30,email:john@example.com";
$pairs = preg_split('/([,:])/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
// ["name", ":", "John", ",", "age", ":", "30", ",", "email", ":", "john@example.com"]

// Split lines
$text = "Line 1\nLine 2\r\nLine 3";
$lines = preg_split('/\r\n|\r|\n/', $text); // ["Line 1", "Line 2", "Line 3"]

// Split with limit and preserve empty strings
$text = "a,,b,,c";
$parts = explode(",", $text); // ["a", "", "b", "", "c"]
$parts_no_empty = explode(",", $text, -1); // ["a", "b", "c"]

// Split large string efficiently
function splitLargeString(string $string, int $chunkSize): array {
    return str_split($string, $chunkSize);
}

$large_text = str_repeat("A", 10000) . str_repeat("B", 10000);
$chunks = splitLargeString($large_text, 1000);
?>
```

## String Formatting

### Number Formatting
```php
<?php
// Basic number formatting
$number = 1234.5678;
$formatted = number_format($number); // "1,235"
$formatted = number_format($number, 2); // "1,234.57"
$formatted = number_format($number, 2, ".", ","); // "1,234.57"

// Currency formatting
$price = 1234.56;
$currency = number_format($price, 2, ".", ","); // "1,234.57"
$currency_symbol = "$" . number_format($price, 2); // "$1234.57"

// Percentage formatting
$percentage = 0.7543;
$percent = number_format($percentage * 100, 1) . "%"; // "75.4%"

// Scientific notation
$large_number = 123456789;
$formatted = number_format($large_number, 0, '.', ','); // "123,456,789"

// Custom formatting with sprintf
$number = 1234.5678;
$formatted = sprintf("%.2f", $number); // "1234.57"
$formatted = sprintf("%'.8.2f", $number); // "1234.57"
$formatted = sprintf("%'.08.2f", $number); // "1234.5700"

// Formatting with printf
$amount = 1234.56;
printf("Amount: $%.2f\n", $amount); // "Amount: $1234.57"

// International number formatting (PHP 8.0+)
$number = 1234567.89;
$formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
$formatted = $formatter->format($number); // "1,234,567.89"

// Currency formatting with locale
$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
$formatted = $formatter->formatCurrency(1234.56, 'USD'); // "$1,234.56"

// Percentage formatting with locale
$formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT);
$formatted = $formatter->format(0.7543); // "75%"

// Spell out numbers (if available)
$formatter = new NumberFormatter('en_US', NumberFormatter::SPELLOUT);
$formatted = $formatter->format(1234); // "one thousand two hundred thirty-four"

// Ordinal numbers
function ordinal(int $number): string {
    $suffix = ['th', 'st', 'nd', 'rd'];
    $value = $number % 100;
    $ordinal = $value >= 11 && $value <= 13 ? 'th' : $suffix[$value % 10] ?? 'th';
    return $number . $ordinal;
}

echo ordinal(1); // "1st"
echo ordinal(2); // "2nd"
echo ordinal(3); // "3rd"
echo ordinal(4); // "4th"
echo ordinal(11); // "11th"
echo ordinal(21); // "21st"
?>
```

### String Formatting
```php
<?php
// printf formatting
$name = "John";
$age = 30;
printf("Name: %s, Age: %d\n", $name, $age); // "Name: John, Age: 30"

// sprintf (returns formatted string)
$formatted = sprintf("Name: %s, Age: %d", $name, $age); // "Name: John, Age: 30"

// Width and padding
$formatted = sprintf("%10s", "Hello"); // "     Hello"
$formatted = sprintf("%-10s", "Hello"); // "Hello     "
$formatted = sprintf("%'010s", "Hello"); // "00000Hello"

// Precision for floating point
$number = 1234.5678;
$formatted = sprintf("%.2f", $number); // "1234.57"
$formatted = sprintf("%8.2f", $number); // " 1234.57"
$formatted = sprintf("%'-8.2f", $number); // "1234.57"

// Multiple arguments
$formatted = sprintf("%s is %d years old and earns $%.2f per year", 
                    $name, $age, 50000.50); // "John is 30 years old and earns $50000.50 per year"

// Argument swapping
$formatted = sprintf("%2\$s is %1\$d years old", $age, $name); // "John is 30 years old"

// Type specifiers
$formatted = sprintf("Binary: %b, Octal: %o, Hex: %x", 42, 42, 42); // "Binary: 101010, Octal: 52, Hex: 2a"

// Date formatting
$date = new DateTime();
$formatted = $date->format('Y-m-d H:i:s'); // "2023-12-01 15:30:45"

// Custom date formatting
function formatDate(DateTime $date, string $format = 'Y-m-d'): string {
    return $date->format($format);
}

$formatted = formatDate(new DateTime(), 'F j, Y'); // "December 1, 2023"

// String padding with sprintf
$text = "Hello";
$formatted = sprintf("%'10s", $text); // "     Hello"
$formatted = sprintf("%'-10s", $text); // "Hello-----"
$formatted = sprintf("%'010s", $text); // "00000Hello"

// Complex formatting example
function formatUserInfo(array $user): string {
    return sprintf(
        "Name: %-20s | Age: %3d | Email: %-30s | Salary: $%9.2f",
        $user['name'],
        $user['age'],
        $user['email'],
        $user['salary']
    );
}

$user = [
    'name' => 'John Doe',
    'age' => 30,
    'email' => 'john@example.com',
    'salary' => 75000.50
];

echo formatUserInfo($user);
// "Name: John Doe             | Age:  30 | Email: john@example.com          | Salary: $75000.50"
?>
```

## String Validation

### Input Validation
```php
<?php
// Validate email
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate URL
function isValidUrl(string $url): bool {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Validate IP address
function isValidIP(string $ip): bool {
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

// Validate integer
function isValidInteger(string $value): bool {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

// Validate float
function isValidFloat(string $value): bool {
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
}

// Validate boolean
function isValidBoolean(string $value): bool {
    return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false;
}

// Custom validation with regex
function isValidPhone(string $phone): bool {
    $pattern = '/^\(?(\d{3})\)?[-\s]?(\d{3})[-\s]?(\d{4})$/';
    return preg_match($pattern, $phone) === 1;
}

function isValidZipCode(string $zip): bool {
    $pattern = '/^\d{5}(-\d{4})?$/';
    return preg_match($pattern, $zip) === 1;
}

function isValidCreditCard(string $card): bool {
    $pattern = '/^\d{4}[-\s]?\d{4}[-\s]?\d{4}[-\s]?\d{4}$/';
    return preg_match($pattern, $card) === 1;
}

// Validate string length
function isValidLength(string $string, int $min, int $max = null): bool {
    $length = strlen($string);
    if ($max === null) {
        return $length >= $min;
    }
    return $length >= $min && $length <= $max;
}

// Validate alphanumeric
function isAlphanumeric(string $string): bool {
    return preg_match('/^[a-zA-Z0-9]+$/', $string) === 1;
}

// Validate alphabetic
function isAlphabetic(string $string): bool {
    return preg_match('/^[a-zA-Z]+$/', $string) === 1;
}

// Validate password strength
function isStrongPassword(string $password): array {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (!preg_match('/\d/', $password)) {
        $errors[] = "Password must contain at least one digit";
    }
    
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/', $password)) {
        $errors[] = "Password must contain at least one special character";
    }
    
    return $errors;
}

// Validate username
function isValidUsername(string $username): array {
    $errors = [];
    
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long";
    }
    
    if (strlen($username) > 20) {
        $errors[] = "Username must be no more than 20 characters long";
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores";
    }
    
    if (preg_match('/^[0-9_]/', $username)) {
        $errors[] = "Username cannot start with a number or underscore";
    }
    
    return $errors;
}
?>
```

### Sanitization
```php
<?php
// Sanitize for HTML output
function sanitizeHTML(string $input): string {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Sanitize for database (prepared statements are better)
function sanitizeSQL(string $input): string {
    return addslashes($input);
}

// Sanitize filename
function sanitizeFilename(string $filename): string {
    // Remove dangerous characters
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    
    // Remove multiple dots
    $filename = preg_replace('/\.+/', '.', $filename);
    
    // Remove leading/trailing dots
    $filename = trim($filename, '.');
    
    // Ensure filename is not empty
    if (empty($filename)) {
        $filename = 'file';
    }
    
    return $filename;
}

// Sanitize phone number
function sanitizePhone(string $phone): string {
    // Remove all non-digit characters
    return preg_replace('/\D/', '', $phone);
}

// Sanitize URL
function sanitizeURL(string $url): string {
    // Remove dangerous characters
    $url = filter_var($url, FILTER_SANITIZE_URL);
    
    // Ensure protocol is present
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = 'http://' . $url;
    }
    
    return $url;
}

// Clean whitespace
function cleanWhitespace(string $input): string {
    // Remove leading/trailing whitespace
    $input = trim($input);
    
    // Replace multiple spaces with single space
    $input = preg_replace('/\s+/', ' ', $input);
    
    return $input;
}

// Remove line breaks
function removeLineBreaks(string $input): string {
    return str_replace(["\r\n", "\r", "\n"], ' ', $input);
}

// Sanitize for JSON
function sanitizeJSON(string $input): string {
    // Remove control characters except newlines and tabs
    return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
}

// Clean string for search
function cleanSearchString(string $search): string {
    // Remove special characters
    $search = preg_replace('/[^a-zA-Z0-9\s]/', '', $search);
    
    // Convert to lowercase
    $search = strtolower($search);
    
    // Remove extra spaces
    $search = trim(preg_replace('/\s+/', ' ', $search));
    
    return $search;
}

// Comprehensive sanitization
function sanitizeInput(string $input, string $type = 'general'): string {
    switch ($type) {
        case 'html':
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        case 'filename':
            return sanitizeFilename($input);
        case 'phone':
            return sanitizePhone($input);
        case 'url':
            return sanitizeURL($input);
        case 'search':
            return cleanSearchString($input);
        case 'email':
            return strtolower(trim($input));
        case 'general':
        default:
            return trim($input);
    }
}
?>
```

## String Security

### XSS Prevention
```php
<?php
// Basic XSS prevention
function escapeHTML(string $input): string {
    return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Escape for JavaScript
function escapeJS(string $input): string {
    return json_encode($input);
}

// Escape for CSS
function escapeCSS(string $input): string {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Escape for URLs
function escapeURL(string $input): string {
    return urlencode($input);
}

// Comprehensive XSS prevention
function preventXSS(string $input): string {
    // Remove null bytes
    $input = str_replace("\0", '', $input);
    
    // Remove control characters
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
    
    // Escape HTML entities
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    return $input;
}

// Content Security Policy header
function setCSPHeader(): void {
    $csp = "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline'; " .
            "style-src 'self' 'unsafe-inline'; " .
            "img-src 'self' data: https:; " .
            "font-src 'self'; " .
            "connect-src 'self'; " .
            "frame-src 'none'; " .
            "object-src 'none';";
    
    header("Content-Security-Policy: $csp");
}

// Sanitize user input for display
function sanitizeUserInput(string $input, string $context = 'html'): string {
    switch ($context) {
        case 'html':
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        case 'attribute':
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        case 'javascript':
            return json_encode($input);
        case 'css':
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        case 'url':
            return urlencode($input);
        default:
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

// Validate and sanitize HTML (using HTML Purifier if available)
function sanitizeHTMLContent(string $html): string {
    // Basic HTML sanitization without external library
    $html = strip_tags($html, '<p><br><strong><em><u><ol><ul><li>');
    $html = htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
    
    return $html;
}

// Check for suspicious patterns
function containsSuspiciousContent(string $input): bool {
    $suspicious = [
        '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
        '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
        '/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi',
        '/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi',
        '/javascript:/i',
        '/vbscript:/i',
        '/onload\s*=/i',
        '/onerror\s*=/i',
        '/onclick\s*=/i',
    ];
    
    foreach ($suspicious as $pattern) {
        if (preg_match($pattern, $input)) {
            return true;
        }
    }
    
    return false;
}

// Log security events
function logSecurityEvent(string $event, string $details = ''): void {
    $log_entry = date('Y-m-d H:i:s') . " - SECURITY: $event";
    if (!empty($details)) {
        $log_entry .= " - Details: $details";
    }
    error_log($log_entry);
}
?>
```

### SQL Injection Prevention
```php
<?php
// Use prepared statements instead of string concatenation
function getUserById(int $userId): ?array {
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'password');
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->execute(['id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Escape for legacy code (use prepared statements instead)
function escapeSQL(string $input): string {
    return addslashes($input);
}

// Validate input before database operations
function validateUserId($id): int {
    if (!is_numeric($id) || $id <= 0) {
        throw new InvalidArgumentException('Invalid user ID');
    }
    return (int)$id;
}

// Sanitize for LIKE queries
function escapeLikePattern(string $value): string {
    $value = str_replace(['%', '_'], ['\\%', '\\_'], $value);
    return $value;
}

// Example of safe LIKE query
function searchUsers(string $searchTerm): array {
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'password');
    $pattern = '%' . escapeLikePattern($searchTerm) . '%';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE name LIKE :pattern');
    $stmt->execute(['pattern' => $pattern]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Input validation for database operations
function validateUserData(array $data): array {
    $errors = [];
    
    if (empty($data['name']) || strlen($data['name']) > 100) {
        $errors[] = 'Invalid name';
    }
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email';
    }
    
    if (isset($data['age']) && (!is_numeric($data['age']) || $data['age'] < 0 || $data['age'] > 150)) {
        $errors[] = 'Invalid age';
    }
    
    return $errors;
}

// Safe database query builder
class SafeQueryBuilder {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function select(string $table, array $conditions = [], array $bindings = []): array {
        $sql = "SELECT * FROM $table";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "$column = :$column";
                $bindings[$column] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insert(string $table, array $data): bool {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
}
?>
```

## Performance Considerations

### String Performance
```php
<?php
// Use single quotes for static strings
$fast = 'Hello, World!';
$slow = "Hello, World!"; // Slightly slower due to variable parsing

// Use concatenation operator instead of interpolation for simple cases
$name = 'John';
$message = 'Hello, ' . $name . '!'; // Faster than "Hello, $name!"

// Use str_replace instead of preg_replace for simple replacements
$text = "Hello, World!";
$fast = str_replace("World", "PHP", $text);
$slow = preg_replace('/World/', 'PHP', $text);

// Use strpos instead of preg_match for simple string search
$fast = strpos($text, "World") !== false;
$slow = preg_match('/World/', $text);

// Use strlen for length checking instead of regex
$fast = strlen($text) > 10;
$slow = preg_match('/^.{10,}$/', $text);

// Use array functions for multiple operations
$replacements = ['a' => 'b', 'c' => 'd'];
$text = str_replace(array_keys($replacements), array_values($replacements), $text);

// Use string buffers for large concatenations
function buildLargeString(array $parts): string {
    return implode('', $parts);
}

// Avoid string concatenation in loops
function badConcatenation(array $items): string {
    $result = '';
    foreach ($items as $item) {
        $result .= $item; // Creates new string each iteration
    }
    return $result;
}

function goodConcatenation(array $items): string {
    $parts = [];
    foreach ($items as $item) {
        $parts[] = $item;
    }
    return implode('', $parts);
}

// Use strtr for multiple character replacements
$replacements = ['a' => 'b', 'c' => 'd', 'e' => 'f'];
$text = strtr($text, $replacements);

// Use isset for string length checking
function fastLengthCheck(string $string, int $min): bool {
    return isset($string[$min - 1]);
}

// Benchmark string operations
function benchmarkStringOperations(): void {
    $iterations = 100000;
    $text = "Hello, World!";
    
    // Benchmark strpos vs preg_match
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        strpos($text, "World");
    }
    $strpos_time = microtime(true) - $start;
    
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        preg_match('/World/', $text);
    }
    $preg_time = microtime(true) - $start;
    
    echo "strpos: {$strpos_time}s\n";
    echo "preg_match: {$preg_time}s\n";
    
    // Benchmark str_replace vs preg_replace
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        str_replace("World", "PHP", $text);
    }
    $str_replace_time = microtime(true) - $start;
    
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        preg_replace('/World/', 'PHP', $text);
    }
    $preg_replace_time = microtime(true) - $start;
    
    echo "str_replace: {$str_replace_time}s\n";
    echo "preg_replace: {$preg_replace_time}s\n";
}
?>
```

### Memory Optimization
```php
<?php
// Use generators for processing large strings
function processLargeFile(string $filename): Generator {
    $handle = fopen($filename, 'r');
    
    if (!$handle) {
        return;
    }
    
    while (($line = fgets($handle)) !== false) {
        yield trim($line);
    }
    
    fclose($handle);
}

// Use str_split for memory-efficient character processing
function processCharacters(string $text): Generator {
    $length = strlen($text);
    for ($i = 0; $i < $length; $i++) {
        yield $text[$i];
    }
}

// Use stream functions for large file processing
function processLargeFileStream(string $filename): void {
    $stream = fopen($filename, 'r');
    
    while (!feof($stream)) {
        $chunk = fread($stream, 8192); // 8KB chunks
        // Process chunk
    }
    
    fclose($stream);
}

// Memory-efficient string building
class StringBuilder {
    private array $parts = [];
    
    public function append(string $string): self {
        $this->parts[] = $string;
        return $this;
    }
    
    public function build(): string {
        return implode('', $this->parts);
    }
    
    public function clear(): void {
        $this->parts = [];
    }
}

// Use unset for large strings when done
function processLargeString(): void {
    $large_string = str_repeat('A', 1000000);
    
    // Process string
    
    unset($large_string); // Free memory
}

// Use memory limit for string operations
function safeStringOperation(string $string): string {
    $memory_limit = 50 * 1024 * 1024; // 50MB
    
    if (strlen($string) > $memory_limit) {
        throw new RuntimeException('String too large for processing');
    }
    
    // Process string
    return $string;
}

// Monitor memory usage
function monitorMemoryUsage(callable $operation): void {
    $before = memory_get_usage();
    
    $operation();
    
    $after = memory_get_usage();
    $peak = memory_get_peak_usage();
    
    echo "Memory used: " . ($after - $before) . " bytes\n";
    echo "Peak memory: " . $peak . " bytes\n";
}

// Use string functions that don't create copies
function compareStrings(string $a, string $b): int {
    return strcmp($a, $b); // Doesn't create new strings
}

function compareStringsCaseInsensitive(string $a, string $b): int {
    return strcasecmp($a, $b); // Doesn't create new strings
}

// Use references for large string modifications
function modifyStringByReference(string &$string): void {
    $string = strtoupper($string);
}

// Lazy string evaluation
class LazyString {
    private callable $generator;
    private ?string $value = null;
    
    public function __construct(callable $generator) {
        $this->generator = $generator;
    }
    
    public function __toString(): string {
        if ($this->value === null) {
            $this->value = ($this->generator)();
        }
        return $this->value;
    }
}

$lazy_string = new LazyString(function() {
    return str_repeat('Hello, World! ', 1000);
});

// String is only generated when actually used
echo $lazy_string;
?>
```

## Best Practices

### String Handling Best Practices
```php
<?php
// Always validate input strings
function processUserInput(string $input): string {
    if (empty($input)) {
        throw new InvalidArgumentException('Input cannot be empty');
    }
    
    if (strlen($input) > 1000) {
        throw new InvalidArgumentException('Input too long');
    }
    
    // Process input
    return trim($input);
}

// Use appropriate string functions
function formatName(string $firstName, string $lastName): string {
    return ucfirst(strtolower($firstName)) . ' ' . ucfirst(strtolower($lastName));
}

// Use prepared statements for database operations
function getUserByEmail(string $email): ?array {
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'password');
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Escape output properly
function displayUserData(array $user): void {
    echo 'Name: ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
    echo 'Email: ' . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
}

// Use constants for repeated strings
class Messages {
    const SUCCESS = 'Operation completed successfully';
    const ERROR = 'An error occurred';
    const INVALID_INPUT = 'Invalid input provided';
}

// Use string templates for complex formatting
function formatUserCard(array $user): string {
    $template = <<<HTML
<div class="user-card">
    <h3>%s</h3>
    <p>Email: %s</p>
    <p>Age: %d</p>
</div>
HTML;
    
    return sprintf($template,
        htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'),
        $user['age']
    );
}

// Use type hints for string parameters
function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Use return type declarations
function formatCurrency(float $amount, string $currency = 'USD'): string {
    return number_format($amount, 2) . ' ' . $currency;
}

// Handle multibyte strings properly
function getShortDescription(string $text, int $maxLength = 100): string {
    if (mb_strlen($text, 'UTF-8') <= $maxLength) {
        return $text;
    }
    
    return mb_substr($text, 0, $maxLength - 3, 'UTF-8') . '...';
}

// Use string builder for complex concatenation
function buildQuery(array $conditions): string {
    $builder = new StringBuilder();
    $builder->append('SELECT * FROM users');
    
    if (!empty($conditions)) {
        $builder->append(' WHERE ');
        $clauses = [];
        
        foreach ($conditions as $field => $value) {
            $clauses[] = "$field = " . (is_string($value) ? "'$value'" : $value);
        }
        
        $builder->append(implode(' AND ', $clauses));
    }
    
    return $builder->build();
}

// Log string operations for debugging
function logStringOperation(string $operation, string $input, ?string $output = null): void {
    $log = date('Y-m-d H:i:s') . " - STRING: $operation";
    $log .= " - Input: " . substr($input, 0, 100);
    
    if ($output !== null) {
        $log .= " - Output: " . substr($output, 0, 100);
    }
    
    error_log($log);
}
?>
```

### Security Best Practices
```php
<?php
// Always escape user input for output
function displayUserMessage(string $message): void {
    echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
}

// Validate and sanitize all input
function processContactForm(array $data): array {
    $errors = [];
    
    // Validate name
    if (empty($data['name']) || strlen($data['name']) > 100) {
        $errors[] = 'Invalid name';
    }
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email';
    }
    
    // Sanitize message
    $message = htmlspecialchars(trim($data['message']), ENT_QUOTES, 'UTF-8');
    
    if (empty($message) || strlen($message) > 1000) {
        $errors[] = 'Invalid message';
    }
    
    return ['errors' => $errors, 'sanitized' => compact('message')];
}

// Use Content Security Policy
function setSecurityHeaders(): void {
    // XSS Protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
    
    // Frame options
    header('X-Frame-Options: DENY');
    
    // Content type options
    header('X-Content-Type-Options: nosniff');
}

// Implement rate limiting
function checkRateLimit(string $identifier, int $maxRequests = 10, int $timeWindow = 60): bool {
    $cacheKey = "rate_limit_{$identifier}";
    
    if (!isset($_SESSION[$cacheKey])) {
        $_SESSION[$cacheKey] = ['count' => 0, 'first_request' => time()];
    }
    
    $limit = $_SESSION[$cacheKey];
    
    if (time() - $limit['first_request'] > $timeWindow) {
        $_SESSION[$cacheKey] = ['count' => 1, 'first_request' => time()];
        return true;
    }
    
    if ($limit['count'] >= $maxRequests) {
        return false;
    }
    
    $_SESSION[$cacheKey]['count']++;
    return true;
}

// Log security events
function logSecurityEvent(string $event, array $context = []): void {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'context' => $context
    ];
    
    error_log(json_encode($log));
}

// Implement CSRF protection
function generateCSRFToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Implement input filtering
function filterInput(string $input, string $type = 'string'): mixed {
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT);
        case 'string':
        default:
            return filter_var($input, FILTER_SANITIZE_STRING);
    }
}
?>
```

## Common Pitfalls

### String Pitfalls
```php
<?php
// Pitfall: Confusing single and double quotes
$name = 'John';
$message1 = 'Hello, $name!'; // "Hello, $name!" (literal)
$message2 = "Hello, $name!"; // "Hello, John!" (interpolated)

// Solution: Use appropriate quotes
$literal = 'Hello, $name!'; // When you want literal string
$interpolated = "Hello, $name!"; // When you want variable interpolation

// Pitfall: Not handling multibyte characters
$utf8_string = "你好，世界！";
$length = strlen($utf8_string); // Wrong: counts bytes, not characters

// Solution: Use multibyte functions
$length = mb_strlen($utf8_string, 'UTF-8'); // Correct: counts characters

// Pitfall: String comparison issues
$string1 = "123";
$string2 = "123abc";
if ($string1 == $string2) {
    echo "Equal"; // This will be true!
}

// Solution: Use strict comparison
if ($string1 === $string2) {
    echo "Equal"; // This will be false
}

// Pitfall: Not escaping HTML output
$user_input = "<script>alert('XSS')</script>";
echo $user_input; // Dangerous: XSS vulnerability

// Solution: Always escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// Pitfall: SQL injection vulnerability
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = '$email'"; // Dangerous

// Solution: Use prepared statements
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);

// Pitfall: Inefficient string concatenation in loops
$result = '';
for ($i = 0; $i < 1000; $i++) {
    $result .= $item; // Creates new string each iteration
}

// Solution: Use array and implode
$parts = [];
for ($i = 0; $i < 1000; $i++) {
    $parts[] = $item;
}
$result = implode('', $parts);

// Pitfall: Not handling encoding properly
$text = "Café";
$lower = strtolower($text); // May not work correctly with multibyte

// Solution: Use multibyte functions
$lower = mb_strtolower($text, 'UTF-8');

// Pitfall: Assuming string functions return boolean
$position = strpos($text, "substring");
if ($position == false) { // Wrong: 0 is falsy
    echo "Not found";
}

// Solution: Use strict comparison
if ($position === false) {
    echo "Not found";
}

// Pitfall: Not handling empty strings properly
if (empty($string)) {
    echo "String is empty"; // But what about "0"?
}

// Solution: Be explicit about what you want
if ($string === '') {
    echo "String is empty";
}

// Pitfall: Using regex when simple functions suffice
$contains = preg_match('/hello/', $text); // Overkill

// Solution: Use strpos for simple searches
$contains = strpos($text, 'hello') !== false;

// Pitfall: Not validating input before processing
$email = $_POST['email'];
// Process email without validation

// Solution: Always validate input
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Process email
}
?>
```

### Performance Pitfalls
```php
<?php
// Pitfall: Using preg_replace for simple replacements
$text = "Hello, World!";
$result = preg_replace('/World/', 'PHP', $text); // Slower

// Solution: Use str_replace for simple cases
$result = str_replace("World", "PHP", $text); // Faster

// Pitfall: Creating unnecessary string copies
function processString(string $string): string {
    $trimmed = trim($string); // Creates new string
    $upper = strtoupper($trimmed); // Creates another new string
    return $upper;
}

// Solution: Minimize string copies
function processStringBetter(string $string): string {
    return strtoupper(trim($string)); // One operation

// Pitfall: Not using string buffers for large operations
function buildLargeString(array $items): string {
    $result = '';
    foreach ($items as $item) {
        $result .= $item; // Inefficient
    }
    return $result;
}

// Solution: Use array and implode
function buildLargeStringBetter(array $items): string {
    return implode('', $items);
}

// Pitfall: Using string functions in tight loops
function processText(array $texts): array {
    $results = [];
    foreach ($texts as $text) {
        $results[] = preg_replace('/\s+/', ' ', $text); // Slow in loops
    }
    return $results;
}

// Solution: Use array_map or optimize the operation
function processTextBetter(array $texts): array {
    return array_map(function($text) {
        return preg_replace('/\s+/', ' ', $text);
    }, $texts);
}

// Pitfall: Not using generators for large datasets
function processLargeFile(string $filename): array {
    $lines = file($filename); // Loads entire file into memory
    $processed = [];
    foreach ($lines as $line) {
        $processed[] = trim($line);
    }
    return $processed;
}

// Solution: Use generators
function processLargeFileBetter(string $filename): Generator {
    $handle = fopen($filename, 'r');
    while (($line = fgets($handle)) !== false) {
        yield trim($line);
    }
    fclose($handle);
}

// Pitfall: Not caching expensive string operations
function expensiveStringOperation(string $input): string {
    // Expensive regex operation
    return preg_replace('/complex_pattern/', 'replacement', $input);
}

// Solution: Cache results
$cache = [];
function expensiveStringOperationCached(string $input): string {
    global $cache;
    
    if (!isset($cache[$input])) {
        $cache[$input] = preg_replace('/complex_pattern/', 'replacement', $input);
    }
    
    return $cache[$input];
}

// Pitfall: Using string functions when array functions are better
function findLongestWord(string $text): string {
    $words = explode(' ', $text);
    $longest = '';
    foreach ($words as $word) {
        if (strlen($word) > strlen($longest)) {
            $longest = $word;
        }
    }
    return $longest;
}

// Solution: Use array functions
function findLongestWordBetter(string $text): string {
    $words = explode(' ', $text);
    usort($words, function($a, $b) {
        return strlen($b) - strlen($a);
    });
    return $words[0] ?? '';
}
?>
```

## Summary

PHP strings provide:

**String Creation:**
- Single and double quoted strings
- Heredoc and nowdoc syntax
- Variable interpolation
- Concatenation and escaping

**String Manipulation:**
- Case conversion functions
- Trimming and padding
- Replacement operations
- Substring extraction

**String Searching:**
- Position-based searching
- Pattern matching with regex
- Case-sensitive/insensitive search
- Multiple occurrence handling

**String Operations:**
- Splitting and joining
- Length counting
- Word counting
- Character extraction

**String Formatting:**
- Number formatting
- Printf-style formatting
- Date/time formatting
- Custom formatting functions

**String Security:**
- XSS prevention
- SQL injection prevention
- Input sanitization
- Output escaping

**Performance Optimization:**
- Memory-efficient operations
- Generator usage
- String buffers
- Benchmarking techniques

**Best Practices:**
- Input validation and sanitization
- Proper escaping for output
- Efficient string operations
- Security considerations

**Common Pitfalls:**
- Quote confusion
- Multibyte character issues
- Comparison problems
- Performance bottlenecks

PHP's string functions provide comprehensive tools for text processing, making it essential for web development to understand both basic operations and advanced security considerations when working with string data.
