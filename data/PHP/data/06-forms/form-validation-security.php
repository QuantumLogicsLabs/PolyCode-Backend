<?php
/**
 * PHP Form Validation and Security
 * 
 * Comprehensive guide to validating and securing HTML forms in PHP.
 */

echo "=== PHP Form Validation and Security ===\n\n";

// Basic Form Validation
echo "--- Basic Form Validation ---\n";

function validateRequired($value, $fieldName) {
    if (empty($value)) {
        return "$fieldName is required";
    }
    return null;
}

function validateEmail($value, $fieldName) {
    if (empty($value)) {
        return null;
    }
    
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return "$fieldName must be a valid email address";
    }
    return null;
}

function validateMinLength($value, $fieldName, $minLength) {
    if (empty($value)) {
        return null;
    }
    
    if (strlen($value) < $minLength) {
        return "$fieldName must be at least $minLength characters long";
    }
    return null;
}

function validateNumeric($value, $fieldName) {
    if (empty($value)) {
        return null;
    }
    
    if (!is_numeric($value)) {
        return "$fieldName must be a number";
    }
    return null;
}

function validateRange($value, $fieldName, $min, $max) {
    if (empty($value)) {
        return null;
    }
    
    $numeric = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($numeric === false) {
        return "$fieldName must be a number";
    }
    
    if ($numeric < $min || $numeric > $max) {
        return "$fieldName must be between $min and $max";
    }
    return null;
}

// Simulated form data
$formData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => '25',
    'password' => 'secret123',
    'phone' => '123-456-7890'
];

$errors = [];

// Validate name
$error = validateRequired($formData['name'], 'Name');
if ($error) $errors[] = $error;

$error = validateMinLength($formData['name'], 'Name', 2);
if ($error) $errors[] = $error;

// Validate email
$error = validateEmail($formData['email'], 'Email');
if ($error) $errors[] = $error;

// Validate age
$error = validateNumeric($formData['age'], 'Age');
if ($error) $errors[] = $error;

$error = validateRange($formData['age'], 'Age', 18, 120);
if ($error) $errors[] = $error;

// Validate password
$error = validateRequired($formData['password'], 'Password');
if ($error) $errors[] = $error;

$error = validateMinLength($formData['password'], 'Password', 8);
if ($error) $errors[] = $error;

echo "Validation Results:\n";
if (empty($errors)) {
    echo "✓ All validations passed\n";
} else {
    echo "✗ Validation errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}
echo "\n";

// Advanced Validation Class
echo "--- Advanced Validation Class ---\n";

class FormValidator {
    private $data;
    private $errors = [];
    private $rules = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function rule($field, $rules) {
        $this->rules[$field] = $rules;
        return $this;
    }
    
    public function validate() {
        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            
            foreach ($fieldRules as $rule => $params) {
                $error = $this->applyRule($field, $value, $rule, $params);
                if ($error) {
                    $this->errors[$field][] = $error;
                }
            }
        }
        
        return empty($this->errors);
    }
    
    private function applyRule($field, $value, $rule, $params) {
        switch ($rule) {
            case 'required':
                return empty($value) ? "$field is required" : null;
                
            case 'email':
                return !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL) 
                    ? "$field must be a valid email" : null;
                    
            case 'min':
                return strlen($value) < $params 
                    ? "$field must be at least $params characters" : null;
                    
            case 'max':
                return strlen($value) > $params 
                    ? "$field must not exceed $params characters" : null;
                    
            case 'numeric':
                return !empty($value) && !is_numeric($value) 
                    ? "$field must be numeric" : null;
                    
            case 'between':
                $numeric = filter_var($value, FILTER_VALIDATE_FLOAT);
                return $numeric === false || $numeric < $params[0] || $numeric > $params[1]
                    ? "$field must be between {$params[0]} and {$params[1]}" : null;
                    
            case 'regex':
                return !empty($value) && !preg_match($params, $value)
                    ? "$field format is invalid" : null;
                    
            case 'alpha':
                return !empty($value) && !preg_match('/^[a-zA-Z]+$/', $value)
                    ? "$field must contain only letters" : null;
                    
            case 'alphanum':
                return !empty($value) && !preg_match('/^[a-zA-Z0-9]+$/', $value)
                    ? "$field must contain only letters and numbers" : null;
                    
            default:
                return null;
        }
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getFirstError($field) {
        return $this->errors[$field][0] ?? null;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
}

// Test the validator
$validator = new FormValidator($formData);

$validator->rule('name', ['required' => true, 'min' => 2, 'max' => 50])
         ->rule('email', ['required' => true, 'email' => true])
         ->rule('age', ['required' => true, 'numeric' => true, 'between' => [18, 120]])
         ->rule('password', ['required' => true, 'min' => 8])
         ->rule('phone', ['regex' => '/^[\d\s\-\(\)]+$/']);

if ($validator->validate()) {
    echo "✓ Advanced validation passed\n";
} else {
    echo "✗ Advanced validation errors:\n";
    foreach ($validator->getErrors() as $field => $fieldErrors) {
        echo "  $field:\n";
        foreach ($fieldErrors as $error) {
            echo "    - $error\n";
        }
    }
}
echo "\n";

// Security Measures
echo "--- Security Measures ---\n";

// Input Sanitization
function sanitizeInput($input, $type = 'string') {
    switch ($type) {
        case 'string':
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
            
        case 'email':
            return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            
        case 'float':
            return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
            
        default:
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

// XSS Prevention
function preventXSS($input) {
    return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// CSRF Protection
class CSRFProtection {
    private static $token;
    
    public static function generateToken() {
        if (empty(self::$token)) {
            self::$token = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = self::$token;
        }
        return self::$token;
    }
    
    public static function validateToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function getHiddenField() {
        return '<input type="hidden" name="csrf_token" value="' . self::generateToken() . '">';
    }
}

// Rate Limiting
class RateLimiter {
    private static $attempts = [];
    
    public static function check($identifier, $maxAttempts = 5, $window = 300) {
        $now = time();
        
        if (!isset(self::$attempts[$identifier])) {
            self::$attempts[$identifier] = [];
        }
        
        // Clean old attempts
        self::$attempts[$identifier] = array_filter(
            self::$attempts[$identifier],
            function($timestamp) use ($now, $window) {
                return $timestamp > ($now - $window);
            }
        );
        
        // Check if limit exceeded
        if (count(self::$attempts[$identifier]) >= $maxAttempts) {
            return false;
        }
        
        // Add new attempt
        self::$attempts[$identifier][] = $now;
        return true;
    }
    
    public static function getRemainingAttempts($identifier, $maxAttempts = 5, $window = 300) {
        $now = time();
        
        if (!isset(self::$attempts[$identifier])) {
            return $maxAttempts;
        }
        
        self::$attempts[$identifier] = array_filter(
            self::$attempts[$identifier],
            function($timestamp) use ($now, $window) {
                return $timestamp > ($now - $window);
            }
        );
        
        return max(0, $maxAttempts - count(self::$attempts[$identifier]));
    }
}

// Password Security
class PasswordSecurity {
    public static function hash($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    public static function verify($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public static function checkStrength($password) {
        $score = 0;
        $feedback = [];
        
        // Length check
        if (strlen($password) >= 8) {
            $score += 1;
        } else {
            $feedback[] = "Password should be at least 8 characters";
        }
        
        if (strlen($password) >= 12) {
            $score += 1;
        }
        
        // Character variety
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = "Include lowercase letters";
        }
        
        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = "Include uppercase letters";
        }
        
        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = "Include numbers";
        }
        
        if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = "Include special characters";
        }
        
        return [
            'score' => $score,
            'max_score' => 6,
            'strength' => $score >= 4 ? 'strong' : ($score >= 2 ? 'medium' : 'weak'),
            'feedback' => $feedback
        ];
    }
}

// Test security functions
echo "Testing Security Functions:\n";

// Sanitization
$unsafeInput = '<script>alert("XSS")</script>';
$safeInput = sanitizeInput($unsafeInput);
echo "Unsafe: $unsafeInput\n";
echo "Safe: $safeInput\n";

// Password strength
$password = 'MySecure123!';
$strength = PasswordSecurity::checkStrength($password);
echo "\nPassword strength for '$password':\n";
echo "Score: {$strength['score']}/{$strength['max_score']}\n";
echo "Strength: {$strength['strength']}\n";
if (!empty($strength['feedback'])) {
    echo "Feedback: " . implode(', ', $strength['feedback']) . "\n";
}

// Rate limiting
$ipAddress = '127.0.0.1';
echo "\nRate limiting test for IP: $ipAddress\n";
for ($i = 1; $i <= 7; $i++) {
    $allowed = RateLimiter::check($ipAddress, 5, 300);
    $remaining = RateLimiter::getRemainingAttempts($ipAddress, 5, 300);
    echo "Attempt $i: " . ($allowed ? 'Allowed' : 'Blocked') . " (Remaining: $remaining)\n";
}
echo "\n";

// Form Processing Class
echo "--- Form Processing Class ---\n";

class FormProcessor {
    private $validator;
    private $sanitizedData = [];
    private $errors = [];
    
    public function __construct($data, $rules) {
        $this->validator = new FormValidator($data);
        
        foreach ($rules as $field => $fieldRules) {
            $this->validator->rule($field, $fieldRules);
        }
    }
    
    public function process() {
        if (!$this->validator->validate()) {
            $this->errors = $this->validator->getErrors();
            return false;
        }
        
        // Sanitize all data
        foreach ($this->validator->data as $key => $value) {
            if (is_string($value)) {
                $this->sanitizedData[$key] = sanitizeInput($value);
            } else {
                $this->sanitizedData[$key] = $value;
            }
        }
        
        return true;
    }
    
    public function getSanitizedData() {
        return $this->sanitizedData;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
}

// Test form processor
$registrationData = [
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'password' => 'SecurePass123!',
    'confirm_password' => 'SecurePass123!',
    'age' => '25',
    'bio' => 'I am a web developer'
];

$registrationRules = [
    'username' => ['required' => true, 'min' => 3, 'max' => 20, 'alphanum' => true],
    'email' => ['required' => true, 'email' => true],
    'password' => ['required' => true, 'min' => 8],
    'confirm_password' => ['required' => true],
    'age' => ['required' => true, 'numeric' => true, 'between' => [18, 120]],
    'bio' => ['max' => 500]
];

$processor = new FormProcessor($registrationData, $registrationRules);

if ($processor->process()) {
    echo "✓ Registration form processed successfully\n";
    $sanitized = $processor->getSanitizedData();
    echo "Sanitized data:\n";
    foreach ($sanitized as $key => $value) {
        echo "  $key: $value\n";
    }
} else {
    echo "✗ Registration form has errors:\n";
    foreach ($processor->getErrors() as $field => $fieldErrors) {
        echo "  $field:\n";
        foreach ($fieldErrors as $error) {
            echo "    - $error\n";
        }
    }
}
echo "\n";

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: Contact Form Handler
echo "Example 1: Contact Form Handler\n";
class ContactFormHandler {
    private $requiredFields = ['name', 'email', 'message'];
    private $sanitizedData = [];
    private $errors = [];
    
    public function __construct($formData) {
        $this->validateAndSanitize($formData);
    }
    
    private function validateAndSanitize($data) {
        // Check required fields
        foreach ($this->requiredFields as $field) {
            if (empty($data[$field])) {
                $this->errors[$field] = ucfirst($field) . " is required";
            }
        }
        
        // Validate email
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Please enter a valid email address";
        }
        
        // Sanitize data
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $this->sanitizedData[$key] = sanitizeInput($value);
            } else {
                $this->sanitizedData[$key] = $value;
            }
        }
        
        // Check message length
        if (!empty($this->sanitizedData['message']) && strlen($this->sanitizedData['message']) < 10) {
            $this->errors['message'] = "Message must be at least 10 characters long";
        }
    }
    
    public function isValid() {
        return empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getData() {
        return $this->sanitizedData;
    }
    
    public function sendEmail() {
        if (!$this->isValid()) {
            return false;
        }
        
        $data = $this->getData();
        
        // In a real application, you would send an email here
        echo "Email would be sent to admin@website.com\n";
        echo "From: {$data['email']}\n";
        echo "Subject: Contact from {$data['name']}\n";
        echo "Message: {$data['message']}\n";
        
        return true;
    }
}

$contactData = [
    'name' => 'Alice Johnson',
    'email' => 'alice@example.com',
    'message' => 'I would like to inquire about your services.',
    'phone' => '555-1234'
];

$contactForm = new ContactFormHandler($contactData);

if ($contactForm->isValid()) {
    echo "✓ Contact form is valid\n";
    $contactForm->sendEmail();
} else {
    echo "✗ Contact form has errors:\n";
    foreach ($contactForm->getErrors() as $field => $error) {
        echo "  $error\n";
    }
}
echo "\n";

// Example 2: User Registration with Security
echo "Example 2: User Registration with Security\n";
class UserRegistration {
    private $db; // Simulated database
    private $errors = [];
    private $userData = [];
    
    public function __construct() {
        $this->db = []; // Simulated database storage
    }
    
    public function register($data) {
        // Validate input
        if (!$this->validateInput($data)) {
            return false;
        }
        
        // Check if user already exists
        if ($this->userExists($data['email'])) {
            $this->errors['email'] = "User with this email already exists";
            return false;
        }
        
        // Hash password
        $hashedPassword = PasswordSecurity::hash($data['password']);
        
        // Create user
        $user = [
            'id' => uniqid(),
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'created_at' => date('Y-m-d H:i:s'),
            'is_active' => true
        ];
        
        // Store user
        $this->db[$user['id']] = $user;
        $this->userData = $user;
        
        return true;
    }
    
    private function validateInput($data) {
        $validator = new FormValidator($data);
        
        $validator->rule('username', ['required' => true, 'min' => 3, 'max' => 20])
                 ->rule('email', ['required' => true, 'email' => true])
                 ->rule('password', ['required' => true, 'min' => 8]);
        
        if (!$validator->validate()) {
            $this->errors = $validator->getErrors();
            return false;
        }
        
        // Check password strength
        $strength = PasswordSecurity::checkStrength($data['password']);
        if ($strength['strength'] === 'weak') {
            $this->errors['password'] = "Password is too weak. " . implode(', ', $strength['feedback']);
            return false;
        }
        
        return true;
    }
    
    private function userExists($email) {
        foreach ($this->db as $user) {
            if ($user['email'] === $email) {
                return true;
            }
        }
        return false;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getUser() {
        unset($this->userData['password']); // Don't return password
        return $this->userData;
    }
}

$registration = new UserRegistration();
$newUserData = [
    'username' => 'johndoe',
    'email' => 'john@example.com',
    'password' => 'StrongPassword123!'
];

if ($registration->register($newUserData)) {
    echo "✓ User registered successfully\n";
    $user = $registration->getUser();
    echo "User ID: {$user['id']}\n";
    echo "Username: {$user['username']}\n";
    echo "Email: {$user['email']}\n";
    echo "Registered: {$user['created_at']}\n";
} else {
    echo "✗ Registration failed:\n";
    foreach ($registration->getErrors() as $field => $errors) {
        foreach ((array)$errors as $error) {
            echo "  $error\n";
        }
    }
}
echo "\n";

// Example 3: File Upload Security
echo "Example 3: File Upload Security\n";
class SecureFileUpload {
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    private $maxSize = 5242880; // 5MB
    private $uploadDir = 'uploads/';
    private $errors = [];
    
    public function upload($file) {
        if (!$this->validateFile($file)) {
            return false;
        }
        
        // Generate secure filename
        $filename = $this->generateSecureFilename($file['name']);
        $filepath = $this->uploadDir . $filename;
        
        // Move file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'original_name' => $file['name'],
                'secure_name' => $filename,
                'size' => $file['size'],
                'type' => $file['type'],
                'path' => $filepath
            ];
        }
        
        $this->errors[] = "Failed to move uploaded file";
        return false;
    }
    
    private function validateFile($file) {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $this->errors[] = "No file uploaded or invalid upload";
            return false;
        }
        
        // Check file size
        if ($file['size'] > $this->maxSize) {
            $this->errors[] = "File size exceeds maximum allowed size";
            return false;
        }
        
        // Check file type
        if (!in_array($file['type'], $this->allowedTypes)) {
            $this->errors[] = "File type not allowed";
            return false;
        }
        
        // Additional MIME type check
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if ($detectedType !== $file['type']) {
            $this->errors[] = "File type mismatch";
            return false;
        }
        
        return true;
    }
    
    private function generateSecureFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    }
    
    public function getErrors() {
        return $this->errors;
    }
}

// Simulate file upload
$simulatedFile = [
    'name' => 'profile.jpg',
    'type' => 'image/jpeg',
    'size' => 1024000, // 1MB
    'tmp_name' => '/tmp/phpXXXXXX'
];

echo "Simulating secure file upload:\n";
$uploader = new SecureFileUpload();

// This would normally work with a real file upload
echo "File validation rules:\n";
echo "- Allowed types: " . implode(', ', $uploader->allowedTypes) . "\n";
echo "- Maximum size: " . ($uploader->maxSize / 1048576) . "MB\n";
echo "- Secure filename generation: Yes\n";
echo "- MIME type verification: Yes\n";
echo "\n";

echo "=== End of Form Validation and Security ===\n";
?>
