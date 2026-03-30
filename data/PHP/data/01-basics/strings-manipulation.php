<?php
/**
 * PHP String Manipulation
 * 
 * Working with strings, string functions, and text processing in PHP.
 */

// Basic String Operations
echo "=== Basic String Operations ===\n";

// String concatenation
$firstName = "John";
$lastName = "Doe";
$fullName = $firstName . " " . $lastName;
echo "Full Name: $fullName\n";

// String interpolation
$age = 25;
$message = "My name is $fullName and I am $age years old.";
echo "Message: $message\n\n";

// String Length
echo "=== String Length ===\n";
$text = "Hello, World!";
echo "Text: $text\n";
echo "Length: " . strlen($text) . " characters\n";
echo "Word count: " . str_word_count($text) . " words\n\n";

// Case Conversion
echo "=== Case Conversion ===\n";
$sampleText = "Hello World!";

echo "Original: $sampleText\n";
echo "Uppercase: " . strtoupper($sampleText) . "\n";
echo "Lowercase: " . strtolower($sampleText) . "\n";
echo "First letter uppercase: " . ucfirst($sampleText) . "\n";
echo "Words uppercase: " . ucwords($sampleText) . "\n\n";

// String Trimming
echo "=== String Trimming ===\n";
$messyString = "   Hello, World!   ";

echo "Original: '$messyString'\n";
echo "Trim both ends: '" . trim($messyString) . "'\n";
echo "Trim left: '" . ltrim($messyString) . "'\n";
echo "Trim right: '" . rtrim($messyString) . "'\n\n";

// String Replacement
echo "=== String Replacement ===\n";
$originalText = "The quick brown fox jumps over the lazy dog.";

echo "Original: $originalText\n";
echo "Replace 'fox' with 'cat': " . str_replace('fox', 'cat', $originalText) . "\n";
echo "Replace multiple words: " . str_replace(['quick', 'brown', 'lazy'], ['slow', 'white', 'sleepy'], $originalText) . "\n";
echo "Case-insensitive replace: " . str_ireplace('THE', 'the', $originalText) . "\n\n";

// Substring Operations
echo "=== Substring Operations ===\n";
$longText = "PHP is a server-side scripting language designed for web development.";

echo "Original: $longText\n";
echo "First 3 characters: " . substr($longText, 0, 3) . "\n";
echo "Characters 5-15: " . substr($longText, 5, 10) . "\n";
echo "Last 10 characters: " . substr($longText, -10) . "\n";
echo "From position 10: " . substr($longText, 10) . "\n\n";

// String Search
echo "=== String Search ===\n";
$searchText = "PHP is amazing! PHP is powerful!";

echo "Text: $searchText\n";
echo "Position of 'PHP': " . strpos($searchText, 'PHP') . "\n";
echo "Position of 'PHP' (case-insensitive): " . stripos($searchText, 'PHP') . "\n";
echo "Last position of 'PHP': " . strrpos($searchText, 'PHP') . "\n";
echo "Contains 'amazing': " . (strpos($searchText, 'amazing') !== false ? 'Yes' : 'No') . "\n";
echo "Starts with 'PHP': " . (str_starts_with($searchText, 'PHP') ? 'Yes' : 'No') . "\n";
echo "Ends with 'powerful': " . (str_ends_with($searchText, 'powerful!') ? 'Yes' : 'No') . "\n\n";

// String Splitting and Joining
echo "=== String Splitting and Joining ===\n";
$sentence = "PHP,JavaScript,Python,Java";

echo "Original: $sentence\n";
$parts = explode(",", $sentence);
echo "Exploded: ";
print_r($parts);

$joined = implode(" | ", $parts);
echo "Imploded: $joined\n\n";

// String Padding
echo "=== String Padding ===\n";
$shortText = "PHP";

echo "Original: '$shortText'\n";
echo "Pad left to 10: '" . str_pad($shortText, 10, " ", STR_PAD_LEFT) . "'\n";
echo "Pad right to 10: '" . str_pad($shortText, 10, " ", STR_PAD_RIGHT) . "'\n";
echo "Pad both to 10: '" . str_pad($shortText, 10, " ", STR_PAD_BOTH) . "'\n";
echo "Pad with '*': '" . str_pad($shortText, 10, "*") . "'\n\n";

// String Comparison
echo "=== String Comparison ===\n";
$string1 = "Hello";
$string2 = "hello";
$string3 = "Hello";

echo "Compare '$string1' and '$string2': " . strcmp($string1, $string2) . "\n";
echo "Compare '$string1' and '$string3': " . strcmp($string1, $string3) . "\n";
echo "Case-insensitive compare: " . strcasecmp($string1, $string2) . "\n";
echo "Are they equal? " . ($string1 === $string3 ? 'Yes' : 'No') . "\n\n";

// HTML Special Characters
echo "=== HTML Special Characters ===\n";
$htmlText = "<script>alert('XSS')</script>";

echo "Original: $htmlText\n";
echo "HTML encoded: " . htmlspecialchars($htmlText) . "\n";
echo "HTML entities: " . htmlentities($htmlText) . "\n";
echo "Strip tags: " . strip_tags($htmlText) . "\n\n";

// String Formatting
echo "=== String Formatting ===\n";

// printf and sprintf
$name = "Alice";
$score = 95.5;
$grade = 'A';

printf("Student: %s, Score: %.1f, Grade: %s\n", $name, $score, $grade);

$formatted = sprintf("Student: %s, Score: %.1f, Grade: %s", $name, $score, $grade);
echo "Formatted string: $formatted\n";

// Number formatting
$price = 1234.56;
echo "Price: $" . number_format($price, 2) . "\n";
echo "Price with commas: $" . number_format($price) . "\n\n";

// Regular Expressions
echo "=== Regular Expressions ===\n";

// Pattern matching
$email = "user@example.com";
$pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

echo "Email: $email\n";
echo "Valid email: " . (preg_match($pattern, $email) ? 'Yes' : 'No') . "\n";

// Find all matches
$text = "The prices are $10, $25, and $50";
preg_match_all('/\$\d+/', $text, $matches);
echo "Prices found: " . implode(", ", $matches[0]) . "\n";

// Replace with regex
$cleaned = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
echo "Cleaned text: $cleaned\n\n";

// Multibyte String Functions
echo "=== Multibyte String Functions ===\n";
$unicodeText = "Hello 世界! 🌍";

echo "Unicode text: $unicodeText\n";
echo "Length (mb_strlen): " . mb_strlen($unicodeText) . "\n";
echo "Substring (mb_substr): " . mb_substr($unicodeText, 6, 2) . "\n";
echo "Uppercase (mb_strtoupper): " . mb_strtoupper($unicodeText) . "\n\n";

// Practical Examples
echo "=== Practical Examples ===\n";

// Example 1: Slug Generator
function createSlug($text) {
    // Convert to lowercase and replace spaces with hyphens
    $slug = strtolower($text);
    $slug = str_replace(' ', '-', $slug);
    // Remove special characters
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    // Replace multiple hyphens with single hyphen
    $slug = preg_replace('/\-+/', '-', $slug);
    // Remove hyphens from start and end
    $slug = trim($slug, '-');
    
    return $slug;
}

$articleTitle = "Hello World! This is a Great Article";
echo "Article title: $articleTitle\n";
echo "URL slug: " . createSlug($articleTitle) . "\n\n";

// Example 2: Password Generator
function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    return $password;
}

echo "Generated passwords:\n";
for ($i = 0; $i < 3; $i++) {
    echo "  " . generatePassword() . "\n";
}
echo "\n";

// Example 3: Text Analyzer
function analyzeText($text) {
    $wordCount = str_word_count($text);
    $charCount = strlen($text);
    $charCountNoSpaces = strlen(str_replace(' ', '', $text));
    $sentenceCount = preg_match_all('/[.!?]+/', $text);
    $avgWordLength = $wordCount > 0 ? $charCountNoSpaces / $wordCount : 0;
    
    return [
        'word_count' => $wordCount,
        'char_count' => $charCount,
        'char_count_no_spaces' => $charCountNoSpaces,
        'sentence_count' => $sentenceCount,
        'avg_word_length' => round($avgWordLength, 2)
    ];
}

$sampleText = "The quick brown fox jumps over the lazy dog. This sentence contains multiple words. It's a good example for text analysis.";
$analysis = analyzeText($sampleText);

echo "Text Analysis:\n";
echo "  Word count: {$analysis['word_count']}\n";
echo "  Character count: {$analysis['char_count']}\n";
echo "  Character count (no spaces): {$analysis['char_count_no_spaces']}\n";
echo "  Sentence count: {$analysis['sentence_count']}\n";
echo "  Average word length: {$analysis['avg_word_length']}\n\n";

// Example 4: String Validator
class StringValidator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePhone($phone) {
        $pattern = '/^[\d\s\-\+\(\)]+$/';
        return preg_match($pattern, $phone) && strlen(preg_replace('/\D/', '', $phone)) >= 10;
    }
    
    public static function validatePassword($password) {
        return strlen($password) >= 8 &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[0-9]/', $password);
    }
    
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

echo "String Validation Examples:\n";
echo "Email 'test@example.com': " . (StringValidator::validateEmail('test@example.com') ? 'Valid' : 'Invalid') . "\n";
echo "Phone '(555) 123-4567': " . (StringValidator::validatePhone('(555) 123-4567') ? 'Valid' : 'Invalid') . "\n";
echo "Password 'Secure123': " . (StringValidator::validatePassword('Secure123') ? 'Valid' : 'Invalid') . "\n";
echo "URL 'https://example.com': " . (StringValidator::validateURL('https://example.com') ? 'Valid' : 'Invalid') . "\n\n";

// Example 5: Text Template System
class TemplateEngine {
    private $template;
    
    public function __construct($template) {
        $this->template = $template;
    }
    
    public function render($data) {
        $result = $this->template;
        
        foreach ($data as $key => $value) {
            $placeholder = "{{" . $key . "}}";
            $result = str_replace($placeholder, $value, $result);
        }
        
        return $result;
    }
}

$template = new TemplateEngine("Hello {{name}}, welcome to {{website}}! Your order #{{order_id}} has been {{status}}.");
$orderData = [
    'name' => 'John Doe',
    'website' => 'Our Store',
    'order_id' => '12345',
    'status' => 'shipped'
];

echo "Template Rendering:\n";
echo $template->render($orderData) . "\n\n";

echo "=== End of String Manipulation ===\n";
?>
