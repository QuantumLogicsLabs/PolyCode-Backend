# PHP Security

## Input Validation

### Input Validation Techniques
```php
<?php
class InputValidator {
    private array $errors = [];
    private array $sanitized = [];
    
    public function validate(array $data, array $rules): bool {
        $this->errors = [];
        $this->sanitized = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule => $params) {
                if (!$this->applyRule($field, $value, $rule, $params)) {
                    // Stop on first error for this field
                    break;
                }
            }
        }
        
        return empty($this->errors);
    }
    
    private function applyRule(string $field, $value, string $rule, $params): bool {
        switch ($rule) {
            case 'required':
                return $this->validateRequired($field, $value, $params);
            case 'email':
                return $this->validateEmail($field, $value, $params);
            case 'min_length':
                return $this->validateMinLength($field, $value, $params);
            case 'max_length':
                return $this->validateMaxLength($field, $value, $params);
            case 'numeric':
                return $this->validateNumeric($field, $value, $params);
            case 'integer':
                return $this->validateInteger($field, $value, $params);
            case 'alpha':
                return $this->validateAlpha($field, $value, $params);
            case 'alphanumeric':
                return $this->validateAlphanumeric($field, $value, $params);
            case 'regex':
                return $this->validateRegex($field, $value, $params);
            case 'in':
                return $this->validateIn($field, $value, $params);
            case 'url':
                return $this->validateUrl($field, $value, $params);
            case 'date':
                return $this->validateDate($field, $value, $params);
            case 'sanitize':
                return $this->sanitizeField($field, $value, $params);
            default:
                return true;
        }
    }
    
    private function validateRequired(string $field, $value, array $params): bool {
        if ($value === null || $value === '') {
            $message = $params['message'] ?? "$field is required";
            $this->errors[$field][] = $message;
            return false;
        }
        return true;
    }
    
    private function validateEmail(string $field, $value, array $params): bool {
        if ($value !== null && $value !== '') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $message = $params['message'] ?? "$field must be a valid email address";
                $this->errors[$field][] = $message;
                return false;
            }
        }
        return true;
    }
    
    private function validateMinLength(string $field, $value, array $params): bool {
        $minLength = $params['length'] ?? 0;
        
        if ($value !== null && strlen($value) < $minLength) {
            $message = $params['message'] ?? "$field must be at least $minLength characters";
            $this->errors[$field][] = $message;
            return false;
        }
        return true;
    }
    
    private function validateMaxLength(string $field, $value, array $params): bool {
        $maxLength = $params['length'] ?? 255;
        
        if ($value !== null && strlen($value) > $maxLength) {
            $message = $params['message'] ?? "$field must not exceed $maxLength characters";
            $this->errors[$field][] = $message;
            return false;
        }
        return true;
    }
    
    private function validateNumeric(string $field, $value, array $params): bool {
        if ($value !== null && $value !== '') {
            if (!is_numeric($value)) {
                $message = $params['message'] ?? "$field must be a number";
                $this->errors[$field][] = $message;
                return false;
            }
        }
        return true;
    }
    
    private function validateInteger(string $field, $value, array $params): bool {
        if ($value !== null && $value !== '') {
            if (!filter_var($value, FILTER_VALIDATE_INT)) {
                $message = $params['message'] ?? "$field must be an integer";
                $this->errors[$field][] = $message;
                return false;
            }
        }
        return true;
    }
    
    private function validateAlpha(string $field, $value, array $params): bool {
        if ($value !== null && $value !== '') {
            if (!ctype_alpha($value)) {
                $message = $params['message'] ?? "$field must contain only letters";
                $this->errors[$field][] = $message;
                return false;
            }
        }
        return true;
    }
    
    private function validateAlphanumeric(string $field, $value, array $params): bool {
        if ($value !== null && $value !== '') {
            if (!ctype_alnum($value)) {
                $message = $params['message'] ?? "$field must contain only letters and numbers";
                $this->errors[$field][] = $message;
                return false;
            }
        }
        return true;
    }
    
    private function validateRegex(string $field, $value, array $params): bool {
        if ($value !== null && $value !== '') {
            $pattern = $params['pattern'] ?? '';
            if (!preg_match($pattern, $value)) {
                $message = $params['message'] ?? "$field format is invalid";
                $this->errors[$field][] = $message;
                return false;
            }
        }
        return true;
    }
    
    private function validateIn(string $field, $value, array $params): bool {
        if ($value !== null && $value !== '') {
            $allowedValues = $params['values'] ?? [];
            if (!in_array($value, $allowedValues)) {
                $message = $params['message'] ?? "$field must be one of: " . implode(', ', $allowedValues);
                $this->errors[$field][] = $message;
                return false;
            }
        }
        return true;
    }
    
    private function validateUrl(string $field, $value, array $params): bool {
        if ($value !== null && $value !== '') {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                $message = $params['message'] ?? "$field must be a valid URL";
                $this->errors[$field][] = $message;
                return false;
            }
        }
        return true;
    }
    
    private function validateDate(string $field, $value, array $params): bool {
        if ($value !== null && $value !== '') {
            $format = $params['format'] ?? 'Y-m-d';
            $date = DateTime::createFromFormat($format, $value);
            
            if (!$date || $date->format($format) !== $value) {
                $message = $params['message'] ?? "$field must be a valid date in format $format";
                $this->errors[$field][] = $message;
                return false;
            }
        }
        return true;
    }
    
    private function sanitizeField(string $field, $value, array $params): bool {
        $type = $params['type'] ?? 'string';
        
        switch ($type) {
            case 'string':
                $this->sanitized[$field] = filter_var($value, FILTER_SANITIZE_STRING);
                break;
            case 'email':
                $this->sanitized[$field] = filter_var($value, FILTER_SANITIZE_EMAIL);
                break;
            case 'url':
                $this->sanitized[$field] = filter_var($value, FILTER_SANITIZE_URL);
                break;
            case 'int':
                $this->sanitized[$field] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'float':
                $this->sanitized[$field] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
                break;
            case 'special_chars':
                $this->sanitized[$field] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                break;
            default:
                $this->sanitized[$field] = $value;
        }
        
        return true;
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function getSanitized(): array {
        return $this->sanitized;
    }
    
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
    
    public function getFirstError(string $field): ?string {
        return $this->errors[$field][0] ?? null;
    }
}

// Advanced input validation
class AdvancedInputValidator {
    private array $patterns = [
        'username' => '/^[a-zA-Z0-9_]{3,20}$/',
        'password' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        'phone' => '/^\+?[\d\s\-\(\)]+$/',
        'credit_card' => '/^\d{4}[\s\-]?\d{4}[\s\-]?\d{4}[\s\-]?\d{4}$/',
        'ip_address' => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',
        'slug' => '/^[a-z0-9\-]+$/',
        'hex_color' => '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
    ];
    
    public function validateUsername(string $username): bool {
        return preg_match($this->patterns['username'], $username);
    }
    
    public function validatePassword(string $password): bool {
        return preg_match($this->patterns['password'], $password);
    }
    
    public function validatePhone(string $phone): bool {
        return preg_match($this->patterns['phone'], $phone);
    }
    
    public function validateCreditCard(string $card): bool {
        // Check pattern
        if (!preg_match($this->patterns['credit_card'], $card)) {
            return false;
        }
        
        // Remove non-digits and check Luhn algorithm
        $digits = preg_replace('/\D/', '', $card);
        return $this->validateLuhn($digits);
    }
    
    private function validateLuhn(string $number): bool {
        $sum = 0;
        $alternate = false;
        
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = intval($number[$i]);
            
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
            $alternate = !$alternate;
        }
        
        return ($sum % 10) === 0;
    }
    
    public function validateIpAddress(string $ip): bool {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
    
    public function validateSlug(string $slug): bool {
        return preg_match($this->patterns['slug'], $slug);
    }
    
    public function validateHexColor(string $color): bool {
        return preg_match($this->patterns['hex_color'], $color);
    }
    
    public function sanitizeHtml(string $html): string {
        // Remove potentially dangerous HTML tags and attributes
        $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img>';
        $html = strip_tags($html, $allowedTags);
        
        // Remove dangerous attributes
        $html = preg_replace('/\s*on\w+="[^"]*"/i', '', $html);
        $html = preg_replace('/\s*javascript:/i', '', $html);
        $html = preg_replace('/\s*vbscript:/i', '', $html);
        $html = preg_replace('/\s*data:/i', '', $html);
        
        return $html;
    }
    
    public function sanitizeFilename(string $filename): string {
        // Remove dangerous characters
        $filename = preg_replace('/[^\w\-_\.]/', '', $filename);
        
        // Remove directory traversal attempts
        $filename = str_replace(['../', '..\\', '..'], '', $filename);
        
        // Limit length
        $filename = substr($filename, 0, 255);
        
        return $filename;
    }
    
    public function validateFileUpload(array $file, array $allowedTypes = [], int $maxSize = 5242880): array {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'No file uploaded or upload failed';
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = 'File type not allowed';
            }
        }
        
        // Check file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'File extension not allowed';
        }
        
        return $errors;
    }
}

// Usage examples
$validator = new InputValidator();
$advancedValidator = new AdvancedInputValidator();

// Basic validation
$userData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => '25',
    'password' => 'Secret123!',
    'website' => 'https://example.com'
];

$rules = [
    'name' => [
        'required' => ['message' => 'Name is required'],
        'min_length' => ['length' => 2, 'message' => 'Name must be at least 2 characters'],
        'sanitize' => ['type' => 'string']
    ],
    'email' => [
        'required' => ['message' => 'Email is required'],
        'email' => ['message' => 'Please enter a valid email'],
        'sanitize' => ['type' => 'email']
    ],
    'age' => [
        'required' => ['message' => 'Age is required'],
        'numeric' => ['message' => 'Age must be a number'],
        'sanitize' => ['type' => 'int']
    ],
    'password' => [
        'required' => ['message' => 'Password is required'],
        'min_length' => ['length' => 8, 'message' => 'Password must be at least 8 characters']
    ],
    'website' => [
        'url' => ['message' => 'Please enter a valid URL'],
        'sanitize' => ['type' => 'url']
    ]
];

if ($validator->validate($userData, $rules)) {
    echo "Validation passed!\n";
    $sanitized = $validator->getSanitized();
    print_r($sanitized);
} else {
    echo "Validation failed!\n";
    $errors = $validator->getErrors();
    print_r($errors);
}

// Advanced validation
echo "\nAdvanced validation examples:\n";
echo "Username valid: " . ($advancedValidator->validateUsername('john_doe') ? 'Yes' : 'No') . "\n";
echo "Password valid: " . ($advancedValidator->validatePassword('Secret123!') ? 'Yes' : 'No') . "\n";
echo "Phone valid: " . ($advancedValidator->validatePhone('+1 (555) 123-4567') ? 'Yes' : 'No') . "\n";
echo "Credit card valid: " . ($advancedValidator->validateCreditCard('4111 1111 1111 1111') ? 'Yes' : 'No') . "\n";
echo "IP address valid: " . ($advancedValidator->validateIpAddress('192.168.1.1') ? 'Yes' : 'No') . "\n";

// HTML sanitization
$maliciousHtml = '<script>alert("XSS")</script><p onclick="alert()">Safe content</p>';
$safeHtml = $advancedValidator->sanitizeHtml($maliciousHtml);
echo "Sanitized HTML: $safeHtml\n";

// Filename sanitization
$dangerousFilename = '../../../etc/passwd';
$safeFilename = $advancedValidator->sanitizeFilename($dangerousFilename);
echo "Sanitized filename: $safeFilename\n";
?>
```

## XSS Prevention

### Cross-Site Scripting Prevention
```php
<?php
class XSSProtection {
    private array $allowedTags = [
        'p', 'br', 'strong', 'em', 'u', 'i', 'b',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li', 'a', 'img',
        'blockquote', 'code', 'pre'
    ];
    
    private array $allowedAttributes = [
        'a' => ['href', 'title', 'target'],
        'img' => ['src', 'alt', 'width', 'height'],
        'blockquote' => ['cite'],
        'code' => ['class']
    ];
    
    public function escape(string $input): string {
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    public function escapeAttr(string $input): string {
        return htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    
    public function escapeJs(string $input): string {
        return json_encode($input, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
    
    public function escapeCss(string $input): string {
        // Remove dangerous CSS content
        $input = preg_replace('/expression\s*\(/i', '', $input);
        $input = preg_replace('/javascript\s*:/i', '', $input);
        $input = preg_replace('/vbscript\s*:/i', '', $input);
        $input = preg_replace('/data\s*:/i', '', $input);
        
        return $input;
    }
    
    public function sanitizeHtml(string $html): string {
        // Use HTML Purifier if available, otherwise use basic sanitization
        if (class_exists('HTMLPurifier')) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.Allowed', implode(',', $this->allowedTags));
            $config->set('HTML.AllowedAttributes', $this->getAllowedAttributesString());
            
            $purifier = new HTMLPurifier($config);
            return $purifier->purify($html);
        }
        
        return $this->basicHtmlSanitization($html);
    }
    
    private function basicHtmlSanitization(string $html): string {
        // Remove script tags and their content
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        
        // Remove dangerous event handlers
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/', '', $html);
        $html = preg_replace('/\s*on\w+\s*=\s*[^>\s]*/', '', $html);
        
        // Remove javascript: and vbscript: protocols
        $html = preg_replace('/\s*javascript\s*:/i', '', $html);
        $html = preg_replace('/\s*vbscript\s*:/i', '', $html);
        $html = preg_replace('/\s*data\s*:/i', '', $html);
        
        // Remove dangerous CSS
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
        
        // Remove HTML comments
        $html = preg_replace('/<!--.*?-->/s', '', $html);
        
        // Allow only safe tags
        $allowedTagsPattern = '<(?:' . implode('|', $this->allowedTags) . ')\b[^>]*>';
        $html = strip_tags($html, '<' . implode('><', $this->allowedTags) . '>');
        
        return $html;
    }
    
    private function getAllowedAttributesString(): string {
        $attributes = [];
        
        foreach ($this->allowedAttributes as $tag => $attrs) {
            foreach ($attrs as $attr) {
                $attributes[] = "$tag.$attr";
            }
        }
        
        return implode(',', $attributes);
    }
    
    public function validateUrl(string $url): bool {
        // Check if URL is valid
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Check for dangerous protocols
        $dangerousProtocols = ['javascript', 'vbscript', 'data', 'mailto'];
        $parsed = parse_url($url);
        
        if (isset($parsed['scheme']) && in_array(strtolower($parsed['scheme']), $dangerousProtocols)) {
            return false;
        }
        
        return true;
    }
    
    public function sanitizeUrl(string $url): string {
        // Remove dangerous protocols
        $url = preg_replace('/^(javascript|vbscript|data):/i', '', $url);
        
        // Validate and return safe URL
        if ($this->validateUrl($url)) {
            return $url;
        }
        
        return '';
    }
    
    public function generateNonce(): string {
        return base64_encode(random_bytes(16));
    }
    
    public function setNonceCookie(string $nonce): void {
        setcookie('xss_nonce', $nonce, [
            'expires' => time() + 3600,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    public function validateNonce(string $nonce): bool {
        return isset($_COOKIE['xss_nonce']) && hash_equals($_COOKIE['xss_nonce'], $nonce);
    }
    
    public function setContentSecurityPolicy(array $options = []): void {
        $defaultOptions = [
            'default-src' => "'self'",
            'script-src' => "'self'",
            'style-src' => "'self' 'unsafe-inline'",
            'img-src' => "'self' data: https:",
            'font-src' => "'self'",
            'connect-src' => "'self'",
            'frame-ancestors' => "'none'",
            'base-uri' => "'self'",
            'form-action' => "'self'"
        ];
        
        $cspOptions = array_merge($defaultOptions, $options);
        $cspHeader = [];
        
        foreach ($cspOptions as $directive => $value) {
            $cspHeader[] = "$directive $value";
        }
        
        header('Content-Security-Policy: ' . implode('; ', $cspHeader));
    }
    
    public function setXssProtectionHeaders(): void {
        // XSS Protection header
        header('X-XSS-Protection: 1; mode=block');
        
        // Content Type Options
        header('X-Content-Type-Options: nosniff');
        
        // Frame Options
        header('X-Frame-Options: SAMEORIGIN');
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Feature Policy
        header('Feature-Policy: geolocation \'none\'; microphone \'none\'; camera \'none\'');
    }
}

// XSS detection and logging
class XSSDetector {
    private array $patterns = [
        '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
        '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
        '/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi',
        '/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi',
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/data\s*:/i',
        '/on\w+\s*=\s*["\'][^"\']*["\']/i',
        '/on\w+\s*=\s*[^>\s]*/i',
        '/expression\s*\(/i'
    ];
    
    public function detectXSS(string $input): array {
        $threats = [];
        
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                $threats[] = [
                    'pattern' => $pattern,
                    'input' => $input,
                    'severity' => $this->getSeverity($pattern)
                ];
            }
        }
        
        return $threats;
    }
    
    private function getSeverity(string $pattern): string {
        if (strpos($pattern, 'script') !== false) {
            return 'high';
        } elseif (strpos($pattern, 'javascript') !== false || strpos($pattern, 'vbscript') !== false) {
            return 'high';
        } elseif (strpos($pattern, 'on\w+') !== false) {
            return 'medium';
        } else {
            return 'low';
        }
    }
    
    public function logXSSAttempt(array $threat, array $context = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'threat' => $threat,
            'context' => array_merge([
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
            ], $context)
        ];
        
        error_log('XSS Attempt Detected: ' . json_encode($logEntry));
    }
    
    public function sanitizeInput(array $input): array {
        $sanitized = [];
        
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } elseif (is_string($value)) {
                $threats = $this->detectXSS($value);
                
                if (!empty($threats)) {
                    foreach ($threats as $threat) {
                        $this->logXSSAttempt($threat, ['input_key' => $key]);
                    }
                    
                    // Sanitize the value
                    $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                } else {
                    $sanitized[$key] = $value;
                }
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
}

// Secure template rendering
class SecureTemplate {
    private XSSProtection $xssProtection;
    private array $context = [];
    
    public function __construct(XSSProtection $xssProtection) {
        $this->xssProtection = $xssProtection;
    }
    
    public function setContext(array $context): void {
        $this->context = $context;
    }
    
    public function render(string $template): string {
        // Replace template variables with escaped values
        foreach ($this->context as $key => $value) {
            if (is_string($value)) {
                $template = str_replace('{{ ' . $key . ' }}', $this->xssProtection->escape($value), $template);
                $template = str_replace('{{ ' . $key . '|raw }}', $value, $template);
                $template = str_replace('{{ ' . $key . '|js }}', $this->xssProtection->escapeJs($value), $template);
                $template = str_replace('{{ ' . $key . '|attr }}', $this->xssProtection->escapeAttr($value), $template);
            }
        }
        
        return $template;
    }
    
    public function renderHtml(string $template): string {
        return $this->xssProtection->sanitizeHtml($this->render($template));
    }
}

// Usage examples
$xssProtection = new XSSProtection();
$xssDetector = new XSSDetector();
$template = new SecureTemplate($xssProtection);

// Set security headers
$xssProtection->setXssProtectionHeaders();
$xssProtection->setContentSecurityPolicy();

// Test XSS protection
$maliciousInput = '<script>alert("XSS")</script><p onclick="alert()">Click me</p>';
echo "Escaped output: " . $xssProtection->escape($maliciousInput) . "\n";

// Test HTML sanitization
$maliciousHtml = '<script>alert("XSS")</script><p>Safe content</p><img src="x" onerror="alert()">';
echo "Sanitized HTML: " . $xssProtection->sanitizeHtml($maliciousHtml) . "\n";

// Test XSS detection
$threats = $xssDetector->detectXSS($maliciousInput);
echo "XSS threats detected: " . count($threats) . "\n";

// Test URL validation
$dangerousUrl = 'javascript:alert("XSS")';
echo "Dangerous URL valid: " . ($xssProtection->validateUrl($dangerousUrl) ? 'Yes' : 'No') . "\n";
echo "Sanitized URL: " . $xssProtection->sanitizeUrl($dangerousUrl) . "\n";

// Test secure template rendering
$template->setContext([
    'username' => '<script>alert("XSS")</script>',
    'message' => 'Hello, world!',
    'user_id' => 123
]);

$unsafeTemplate = '<h1>{{ username }}</h1><p>{{ message }}</p><script>var id = {{ user_id|js }};</script>';
echo "Secure template: " . $template->render($unsafeTemplate) . "\n";

// Sanitize user input array
$userInput = [
    'name' => '<script>alert("XSS")</script>John',
    'email' => 'john@example.com',
    'comment' => '<img src="x" onerror="alert()">Nice post!',
    'profile' => [
        'bio' => '<p onclick="alert()">Click me!</p>',
        'website' => 'javascript:alert("XSS")'
    ]
];

$sanitizedInput = $xssDetector->sanitizeInput($userInput);
echo "Sanitized input:\n";
print_r($sanitizedInput);
?>
```

## SQL Injection Prevention

### SQL Injection Protection
```php
<?php
class SQLInjectionProtection {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    
    // Safe query with prepared statements
    public function getUserById(int $userId): ?array {
        $sql = "SELECT id, username, email, created_at FROM users WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $this->logQuery($sql, ['id' => $userId], 'success');
                return $user;
            } else {
                $this->logQuery($sql, ['id' => $userId], 'not_found');
                return null;
            }
            
        } catch (PDOException $e) {
            $this->logQuery($sql, ['id' => $userId], 'error', $e->getMessage());
            throw new DatabaseException('Failed to fetch user', $sql, ['id' => $userId], $e);
        }
    }
    
    // Safe login query
    public function authenticateUser(string $email, string $password): ?array {
        $sql = "SELECT id, username, email, password_hash FROM users WHERE email = :email";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Remove password hash from returned data
                unset($user['password_hash']);
                
                $this->logQuery($sql, ['email' => $email], 'success');
                return $user;
            } else {
                $this->logQuery($sql, ['email' => $email], 'authentication_failed');
                return null;
            }
            
        } catch (PDOException $e) {
            $this->logQuery($sql, ['email' => $email], 'error', $e->getMessage());
            throw new DatabaseException('Authentication failed', $sql, ['email' => $email], $e);
        }
    }
    
    // Safe insert query
    public function createUser(array $userData): int {
        $sql = "INSERT INTO users (username, email, password_hash, created_at) VALUES (:username, :email, :password_hash, NOW())";
        
        try {
            $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $userData['username'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $userData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
            $stmt->execute();
            
            $userId = $this->pdo->lastInsertId();
            
            $this->logQuery($sql, $userData, 'success');
            
            return (int)$userId;
            
        } catch (PDOException $e) {
            $this->logQuery($sql, $userData, 'error', $e->getMessage());
            throw new DatabaseException('Failed to create user', $sql, $userData, $e);
        }
    }
    
    // Safe update query
    public function updateUser(int $userId, array $userData): bool {
        $sql = "UPDATE users SET username = :username, email = :email, updated_at = NOW() WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $userData['username'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $userData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            $rowCount = $stmt->rowCount();
            
            if ($rowCount > 0) {
                $this->logQuery($sql, array_merge($userData, ['id' => $userId]), 'success');
                return true;
            } else {
                $this->logQuery($sql, array_merge($userData, ['id' => $userId]), 'not_found');
                return false;
            }
            
        } catch (PDOException $e) {
            $this->logQuery($sql, array_merge($userData, ['id' => $userId]), 'error', $e->getMessage());
            throw new DatabaseException('Failed to update user', $sql, array_merge($userData, ['id' => $userId]), $e);
        }
    }
    
    // Safe delete query
    public function deleteUser(int $userId): bool {
        $sql = "DELETE FROM users WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            $rowCount = $stmt->rowCount();
            
            if ($rowCount > 0) {
                $this->logQuery($sql, ['id' => $userId], 'success');
                return true;
            } else {
                $this->logQuery($sql, ['id' => $userId], 'not_found');
                return false;
            }
            
        } catch (PDOException $e) {
            $this->logQuery($sql, ['id' => $userId], 'error', $e->getMessage());
            throw new DatabaseException('Failed to delete user', $sql, ['id' => $userId], $e);
        }
    }
    
    // Safe search query
    public function searchUsers(string $searchTerm, int $limit = 10, int $offset = 0): array {
        $sql = "SELECT id, username, email FROM users WHERE username LIKE :search OR email LIKE :search ORDER BY username LIMIT :limit OFFSET :offset";
        
        try {
            $searchPattern = '%' . $searchTerm . '%';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':search', $searchPattern, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->logQuery($sql, ['search' => $searchTerm, 'limit' => $limit, 'offset' => $offset], 'success');
            
            return $users;
            
        } catch (PDOException $e) {
            $this->logQuery($sql, ['search' => $searchTerm, 'limit' => $limit, 'offset' => $offset], 'error', $e->getMessage());
            throw new DatabaseException('Search failed', $sql, ['search' => $searchTerm, 'limit' => $limit, 'offset' => $offset], $e);
        }
    }
    
    // Safe batch insert
    public function createPosts(array $postsData): array {
        $sql = "INSERT INTO posts (user_id, title, content, created_at) VALUES ";
        $placeholders = [];
        $values = [];
        
        // Build placeholders and values
        foreach ($postsData as $index => $post) {
            $placeholders[] = "(:user_id_$index, :title_$index, :content_$index, NOW())";
            $values["user_id_$index"] = $post['user_id'];
            $values["title_$index"] = $post['title'];
            $values["content_$index"] = $post['content'];
        }
        
        $sql .= implode(', ', $placeholders);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Bind all values
            foreach ($values as $key => $value) {
                $paramType = strpos($key, 'user_id') !== false ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue(":$key", $value, $paramType);
            }
            
            $stmt->execute();
            
            $insertedIds = [];
            for ($i = 0; $i < count($postsData); $i++) {
                $insertedIds[] = $this->pdo->lastInsertId();
            }
            
            $this->logQuery($sql, $postsData, 'success');
            
            return $insertedIds;
            
        } catch (PDOException $e) {
            $this->logQuery($sql, $postsData, 'error', $e->getMessage());
            throw new DatabaseException('Batch insert failed', $sql, $postsData, $e);
        }
    }
    
    // Safe transaction with multiple queries
    public function transferFunds(int $fromUserId, int $toUserId, float $amount): bool {
        $this->pdo->beginTransaction();
        
        try {
            // Check sender's balance
            $checkSql = "SELECT balance FROM accounts WHERE user_id = :user_id FOR UPDATE";
            $stmt = $this->pdo->prepare($checkSql);
            $stmt->bindParam(':user_id', $fromUserId, PDO::PARAM_INT);
            $stmt->execute();
            
            $senderBalance = $stmt->fetchColumn();
            
            if ($senderBalance === false || $senderBalance < $amount) {
                $this->pdo->rollBack();
                $this->logQuery($checkSql, ['user_id' => $fromUserId], 'insufficient_funds');
                return false;
            }
            
            // Deduct from sender
            $deductSql = "UPDATE accounts SET balance = balance - :amount WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($deductSql);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $fromUserId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Add to receiver
            $addSql = "UPDATE accounts SET balance = balance + :amount WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($addSql);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $toUserId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Record transaction
            $recordSql = "INSERT INTO transactions (from_user_id, to_user_id, amount, created_at) VALUES (:from_user_id, :to_user_id, :amount, NOW())";
            $stmt = $this->pdo->prepare($recordSql);
            $stmt->bindParam(':from_user_id', $fromUserId, PDO::PARAM_INT);
            $stmt->bindParam(':to_user_id', $toUserId, PDO::PARAM_INT);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->execute();
            
            $this->pdo->commit();
            
            $this->logQuery('Funds Transfer', [
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'amount' => $amount
            ], 'success');
            
            return true;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            
            $this->logQuery('Funds Transfer', [
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'amount' => $amount
            ], 'error', $e->getMessage());
            
            throw new DatabaseException('Fund transfer failed', 'Funds Transfer', [
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'amount' => $amount
            ], $e);
        }
    }
    
    private function logQuery(string $sql, array $params, string $status, string $error = null): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'sql' => $sql,
            'params' => $params,
            'status' => $status,
            'error' => $error,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        
        error_log('Database Query: ' . json_encode($logEntry));
    }
}

// SQL Injection Detection
class SQLInjectionDetector {
    private array $patterns = [
        '/\b(union|select|insert|update|delete|drop|create|alter|exec|execute)\b/i',
        '/\b(or|and)\s+\d+\s*=\s*\d+/i',
        '/\b(or|and)\s+["\'][^"\']*["\']\s*=\s*["\'][^"\']*["\']/i',
        '/\b(or|and)\s+\d+\s*=\s*["\'][^"\']*["\']/i',
        '/\b(or|and)\s+["\'][^"\']*["\']\s*=\s*\d+/i',
        '/\b(waitfor\s+delay|sleep\s*\()\b/i',
        '/\b(benchmark|load_file|outfile)\b/i',
        '/\b(information_schema|sysobjects|syscolumns)\b/i',
        '/\b(char|varchar|nvarchar|cast|convert)\s*\(/i',
        '/\b(concat|substring|ascii|ord)\s*\(/i',
        '/\b(xor|not|between|like|regexp)\b/i'
    ];
    
    public function detectSQLInjection(string $input): array {
        $threats = [];
        
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                $threats[] = [
                    'pattern' => $pattern,
                    'input' => $input,
                    'severity' => $this->getSeverity($pattern)
                ];
            }
        }
        
        return $threats;
    }
    
    private function getSeverity(string $pattern): string {
        if (strpos($pattern, 'union|select') !== false) {
            return 'high';
        } elseif (strpos($pattern, 'drop|delete') !== false) {
            return 'high';
        } elseif (strpos($pattern, 'waitfor|sleep') !== false) {
            return 'medium';
        } else {
            return 'low';
        }
    }
    
    public function sanitizeInput(array $input): array {
        $sanitized = [];
        
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } elseif (is_string($value)) {
                $threats = $this->detectSQLInjection($value);
                
                if (!empty($threats)) {
                    foreach ($threats as $threat) {
                        $this->logSQLInjectionAttempt($threat, ['input_key' => $key]);
                    }
                    
                    // Remove dangerous characters
                    $sanitized[$key] = $this->removeDangerousCharacters($value);
                } else {
                    $sanitized[$key] = $value;
                }
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    private function removeDangerousCharacters(string $input): string {
        // Remove or escape dangerous SQL characters
        $dangerousChars = ["'", '"', ';', '--', '/*', '*/', 'xp_', 'sp_'];
        
        foreach ($dangerousChars as $char) {
            $input = str_replace($char, '', $input);
        }
        
        return $input;
    }
    
    private function logSQLInjectionAttempt(array $threat, array $context = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'threat' => $threat,
            'context' => array_merge([
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
            ], $context)
        ];
        
        error_log('SQL Injection Attempt Detected: ' . json_encode($logEntry));
    }
}

// Usage examples
try {
    // Create database connection
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'username', 'password');
    
    $sqlProtection = new SQLInjectionProtection($pdo);
    $sqlDetector = new SQLInjectionDetector();
    
    // Test safe queries
    $user = $sqlProtection->getUserById(1);
    echo "User fetched: " . ($user ? $user['username'] : 'Not found') . "\n";
    
    // Test authentication
    $authUser = $sqlProtection->authenticateUser('john@example.com', 'password123');
    echo "Authentication: " . ($authUser ? 'Success' : 'Failed') . "\n";
    
    // Test search
    $searchResults = $sqlProtection->searchUsers('john');
    echo "Search results: " . count($searchResults) . " users found\n";
    
    // Test SQL injection detection
    $maliciousInput = "1' OR '1'='1";
    $threats = $sqlDetector->detectSQLInjection($maliciousInput);
    echo "SQL injection threats detected: " . count($threats) . "\n";
    
    // Test input sanitization
    $userInput = [
        'user_id' => "1' OR '1'='1",
        'search' => "john'; DROP TABLE users; --",
        'email' => 'john@example.com'
    ];
    
    $sanitizedInput = $sqlDetector->sanitizeInput($userInput);
    echo "Sanitized input:\n";
    print_r($sanitizedInput);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
```

## CSRF Protection

### Cross-Site Request Forgery Prevention
```php
<?php
class CSRFProtection {
    private string $tokenName = '_csrf_token';
    private int $tokenLength = 32;
    private int $tokenExpiration = 3600; // 1 hour
    
    public function generateToken(): string {
        $token = bin2hex(random_bytes($this->tokenLength));
        $timestamp = time();
        
        $_SESSION[$this->tokenName] = [
            'token' => $token,
            'timestamp' => $timestamp
        ];
        
        return $token;
    }
    
    public function validateToken(string $token): bool {
        if (!isset($_SESSION[$this->tokenName])) {
            return false;
        }
        
        $storedToken = $_SESSION[$this->tokenName];
        
        // Check if token matches
        if (!hash_equals($storedToken['token'], $token)) {
            return false;
        }
        
        // Check if token has expired
        if (time() - $storedToken['timestamp'] > $this->tokenExpiration) {
            $this->clearToken();
            return false;
        }
        
        return true;
    }
    
    public function clearToken(): void {
        unset($_SESSION[$this->tokenName]);
    }
    
    public function getHiddenField(): string {
        $token = $this->generateToken();
        return '<input type="hidden" name="' . $this->tokenName . '" value="' . $token . '">';
    }
    
    public function getMetaTag(): string {
        $token = $this->generateToken();
        return '<meta name="csrf-token" content="' . $token . '">';
    }
    
    public function getHeader(): string {
        $token = $this->generateToken();
        return 'X-CSRF-Token: ' . $token;
    }
    
    public function validateRequest(): bool {
        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        
        // Only validate state-changing requests
        if (!in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            return true;
        }
        
        // Check token in POST data
        if (isset($_POST[$this->tokenName])) {
            return $this->validateToken($_POST[$this->tokenName]);
        }
        
        // Check token in headers
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            return $this->validateToken($_SERVER['HTTP_X_CSRF_TOKEN']);
        }
        
        return false;
    }
    
    public function setSameSiteCookie(string $token): void {
        setcookie($this->tokenName, $token, [
            'expires' => time() + $this->tokenExpiration,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    public function validateCookieToken(): bool {
        if (!isset($_COOKIE[$this->tokenName])) {
            return false;
        }
        
        return $this->validateToken($_COOKIE[$this->tokenName]);
    }
    
    public function logCSRFAttempt(array $context = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'CSRF_ATTEMPT',
            'context' => array_merge([
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                'referer' => $_SERVER['HTTP_REFERER'] ?? 'unknown'
            ], $context)
        ];
        
        error_log('CSRF Attempt Detected: ' . json_encode($logEntry));
    }
}

class SecureForm {
    private CSRFProtection $csrf;
    private array $fields = [];
    private string $action = '';
    private string $method = 'POST';
    
    public function __construct(CSRFProtection $csrf) {
        $this->csrf = $csrf;
    }
    
    public function setAction(string $action): void {
        $this->action = $action;
    }
    
    public function setMethod(string $method): void {
        $this->method = strtoupper($method);
    }
    
    public function addField(string $name, string $type, array $attributes = []): void {
        $this->fields[] = [
            'name' => $name,
            'type' => $type,
            'attributes' => $attributes
        ];
    }
    
    public function render(): string {
        $form = '<form action="' . htmlspecialchars($this->action) . '" method="' . $this->method . '">';
        
        // Add CSRF token
        $form .= $this->csrf->getHiddenField();
        
        // Add fields
        foreach ($this->fields as $field) {
            $form .= $this->renderField($field);
        }
        
        $form .= '<button type="submit">Submit</button>';
        $form .= '</form>';
        
        return $form;
    }
    
    private function renderField(array $field): string {
        $attributes = '';
        
        foreach ($field['attributes'] as $name => $value) {
            $attributes .= ' ' . $name . '="' . htmlspecialchars($value) . '"';
        }
        
        switch ($field['type']) {
            case 'text':
            case 'email':
            case 'password':
                return '<input type="' . $field['type'] . '" name="' . $field['name'] . '"' . $attributes . '>';
                
            case 'textarea':
                return '<textarea name="' . $field['name'] . '"' . $attributes . '></textarea>';
                
            case 'select':
                $options = $field['attributes']['options'] ?? [];
                unset($field['attributes']['options']);
                
                $html = '<select name="' . $field['name'] . '"' . $attributes . '>';
                
                foreach ($options as $value => $label) {
                    $html .= '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($label) . '</option>';
                }
                
                $html .= '</select>';
                return $html;
                
            default:
                return '<input type="' . $field['type'] . '" name="' . $field['name'] . '"' . $attributes . '>';
        }
    }
}

class CSRFMiddleware {
    private CSRFProtection $csrf;
    
    public function __construct(CSRFProtection $csrf) {
        $this->csrf = $csrf;
    }
    
    public function handle(): void {
        if (!$this->csrf->validateRequest()) {
            $this->csrf->logCSRFAttempt();
            
            header('HTTP/1.1 403 Forbidden');
            echo 'CSRF token validation failed';
            exit;
        }
    }
    
    public function generateTokenForAjax(): void {
        header('Content-Type: application/json');
        
        $token = $this->csrf->generateToken();
        
        echo json_encode([
            'csrf_token' => $token,
            'csrf_header' => 'X-CSRF-Token'
        ]);
    }
}

// Usage examples
session_start();

$csrf = new CSRFProtection();
$middleware = new CSRFMiddleware($csrf);
$form = new SecureForm($csrf);

// Set up form
$form->setAction('/process-form');
$form->setMethod('POST');
$form->addField('username', 'text', ['placeholder' => 'Username', 'required' => true]);
$form->addField('email', 'email', ['placeholder' => 'Email', 'required' => true]);
$form->addField('password', 'password', ['placeholder' => 'Password', 'required' => true]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $middleware->handle();
    
    // Process form data safely
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "Form submitted successfully!\n";
    echo "Username: " . htmlspecialchars($username) . "\n";
    echo "Email: " . htmlspecialchars($email) . "\n";
} else {
    // Display form
    echo "<!DOCTYPE html>\n";
    echo "<html>\n<head>\n";
    echo "<title>Secure Form</title>\n";
    echo $csrf->getMetaTag() . "\n";
    echo "</head>\n<body>\n";
    echo "<h1>Secure Form</h1>\n";
    echo $form->render();
    echo "</body>\n</html>\n";
}

// AJAX token endpoint
if (isset($_GET['get_csrf_token'])) {
    $middleware->generateTokenForAjax();
}

// JavaScript for AJAX requests
$ajaxScript = "
<script>
// Get CSRF token
fetch('/?get_csrf_token=1')
    .then(response => response.json())
    .then(data => {
        const csrfToken = data.csrf_token;
        const csrfHeader = data.csrf_header;
        
        // Make AJAX request with CSRF token
        fetch('/api/data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                [csrfHeader]: csrfToken
            },
            body: JSON.stringify({
                action: 'update',
                data: 'example'
            })
        })
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
    });
</script>
";

echo $ajaxScript;
?>
```

## Password Security

### Password Hashing and Security
```php
<?php
class PasswordSecurity {
    private array $algorithms = [
        'bcrypt' => PASSWORD_BCRYPT,
        'argon2i' => PASSWORD_ARGON2I,
        'argon2id' => PASSWORD_ARGON2ID
    ];
    
    private string $algorithm;
    private array $options;
    
    public function __construct(string $algorithm = 'bcrypt', array $options = []) {
        $this->algorithm = $algorithm;
        $this->options = array_merge($this->getDefaultOptions($algorithm), $options);
    }
    
    private function getDefaultOptions(string $algorithm): array {
        switch ($algorithm) {
            case 'bcrypt':
                return ['cost' => 12];
                
            case 'argon2i':
                return [
                    'memory_cost' => 65536,
                    'time_cost' => 4,
                    'threads' => 1
                ];
                
            case 'argon2id':
                return [
                    'memory_cost' => 65536,
                    'time_cost' => 4,
                    'threads' => 1
                ];
                
            default:
                return [];
        }
    }
    
    public function hashPassword(string $password): string {
        if (empty($password)) {
            throw new InvalidArgumentException('Password cannot be empty');
        }
        
        $hash = password_hash($password, $this->algorithms[$this->algorithm], $this->options);
        
        if ($hash === false) {
            throw new RuntimeException('Password hashing failed');
        }
        
        return $hash;
    }
    
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    public function needsRehash(string $hash): bool {
        return password_needs_rehash($hash, $this->algorithms[$this->algorithm], $this->options);
    }
    
    public function getPasswordInfo(string $hash): array {
        return password_get_info($hash);
    }
    
    public function validatePasswordStrength(string $password): array {
        $issues = [];
        $score = 0;
        
        // Length check
        if (strlen($password) < 8) {
            $issues[] = 'Password must be at least 8 characters long';
        } else {
            $score += 1;
        }
        
        // Uppercase letter check
        if (!preg_match('/[A-Z]/', $password)) {
            $issues[] = 'Password must contain at least one uppercase letter';
        } else {
            $score += 1;
        }
        
        // Lowercase letter check
        if (!preg_match('/[a-z]/', $password)) {
            $issues[] = 'Password must contain at least one lowercase letter';
        } else {
            $score += 1;
        }
        
        // Number check
        if (!preg_match('/\d/', $password)) {
            $issues[] = 'Password must contain at least one number';
        } else {
            $score += 1;
        }
        
        // Special character check
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
            $issues[] = 'Password must contain at least one special character';
        } else {
            $score += 1;
        }
        
        // Common password check
        if ($this->isCommonPassword($password)) {
            $issues[] = 'Password is too common and easily guessable';
            $score -= 2;
        }
        
        // Repeated characters check
        if (preg_match('/(.)\1{2,}/', $password)) {
            $issues[] = 'Password contains repeated characters';
            $score -= 1;
        }
        
        // Sequential characters check
        if ($this->hasSequentialChars($password)) {
            $issues[] = 'Password contains sequential characters';
            $score -= 1;
        }
        
        return [
            'score' => max(0, min(5, $score)),
            'issues' => $issues,
            'strength' => $this->getStrengthLabel($score)
        ];
    }
    
    private function isCommonPassword(string $password): bool {
        $commonPasswords = [
            'password', '123456', '123456789', '12345678', '12345',
            '1234567', '1234567890', 'qwerty', 'abc123', 'password123',
            'admin', 'letmein', 'welcome', 'monkey', '1234',
            'dragon', 'master', 'hello', 'freedom', 'whatever'
        ];
        
        return in_array(strtolower($password), $commonPasswords);
    }
    
    private function hasSequentialChars(string $password): bool {
        $length = strlen($password);
        
        for ($i = 0; $i < $length - 2; $i++) {
            $char1 = ord($password[$i]);
            $char2 = ord($password[$i + 1]);
            $char3 = ord($password[$i + 2]);
            
            // Check for sequential characters (abc, 123, etc.)
            if ($char2 == $char1 + 1 && $char3 == $char2 + 1) {
                return true;
            }
            
            // Check for reverse sequential characters (cba, 321, etc.)
            if ($char2 == $char1 - 1 && $char3 == $char2 - 1) {
                return true;
            }
        }
        
        return false;
    }
    
    private function getStrengthLabel(int $score): string {
        switch ($score) {
            case 0:
            case 1:
                return 'Very Weak';
            case 2:
                return 'Weak';
            case 3:
                return 'Fair';
            case 4:
                return 'Strong';
            case 5:
                return 'Very Strong';
            default:
                return 'Unknown';
        }
    }
    
    public function generateSecurePassword(int $length = 16): string {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        $password = '';
        
        // Ensure at least one character from each category
        $password .= $this->getRandomChar('abcdefghijklmnopqrstuvwxyz');
        $password .= $this->getRandomChar('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $password .= $this->getRandomChar('0123456789');
        $password .= $this->getRandomChar('!@#$%^&*()_+-=[]{}|;:,.<>?');
        
        // Fill the rest
        for ($i = 4; $i < $length; $i++) {
            $password .= $this->getRandomChar($characters);
        }
        
        // Shuffle the password
        return str_shuffle($password);
    }
    
    private function getRandomChar(string $characters): string {
        return $characters[random_int(0, strlen($characters) - 1)];
    }
    
    public function generatePasswordResetToken(): string {
        return bin2hex(random_bytes(32));
    }
    
    public function validatePasswordResetToken(string $token, string $storedToken, int $expiration = 3600): bool {
        return hash_equals($token, $storedToken) && 
               isset($_SESSION['password_reset_timestamp']) && 
               (time() - $_SESSION['password_reset_timestamp']) < $expiration;
    }
    
    public function logPasswordEvent(string $event, array $context = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'context' => array_merge([
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ], $context)
        ];
        
        error_log('Password Security Event: ' . json_encode($logEntry));
    }
}

class PasswordPolicy {
    private array $rules = [];
    
    public function __construct(array $config = []) {
        $this->rules = array_merge([
            'min_length' => 8,
            'max_length' => 128,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_special_chars' => true,
            'forbidden_words' => ['password', 'admin', 'user'],
            'max_consecutive_chars' => 2,
            'password_history' => 5,
            'expiration_days' => 90
        ], $config);
    }
    
    public function validate(string $password, array $userContext = []): array {
        $errors = [];
        
        // Length validation
        if (strlen($password) < $this->rules['min_length']) {
            $errors[] = "Password must be at least {$this->rules['min_length']} characters long";
        }
        
        if (strlen($password) > $this->rules['max_length']) {
            $errors[] = "Password must not exceed {$this->rules['max_length']} characters";
        }
        
        // Character requirements
        if ($this->rules['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if ($this->rules['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if ($this->rules['require_numbers'] && !preg_match('/\d/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if ($this->rules['require_special_chars'] && !preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        // Forbidden words
        foreach ($this->rules['forbidden_words'] as $word) {
            if (stripos($password, $word) !== false) {
                $errors[] = "Password cannot contain the word '$word'";
            }
        }
        
        // Consecutive characters
        if ($this->hasTooManyConsecutiveChars($password)) {
            $errors[] = "Password cannot contain more than {$this->rules['max_consecutive_chars']} consecutive identical characters";
        }
        
        // Password history (would need database access in real implementation)
        if (isset($userContext['password_history'])) {
            foreach ($userContext['password_history'] as $oldPassword) {
                if (password_verify($password, $oldPassword)) {
                    $errors[] = 'Password cannot be the same as any of your previous passwords';
                    break;
                }
            }
        }
        
        return $errors;
    }
    
    private function hasTooManyConsecutiveChars(string $password): bool {
        $maxConsecutive = $this->rules['max_consecutive_chars'];
        
        for ($i = 0; $i < strlen($password) - $maxConsecutive; $i++) {
            $char = $password[$i];
            $consecutive = 1;
            
            for ($j = $i + 1; $j < strlen($password); $j++) {
                if ($password[$j] === $char) {
                    $consecutive++;
                    if ($consecutive > $maxConsecutive) {
                        return true;
                    }
                } else {
                    break;
                }
            }
        }
        
        return false;
    }
    
    public function isPasswordExpired(string $lastChangedDate): bool {
        $lastChanged = new DateTime($lastChangedDate);
        $expiration = new DateTime();
        $expiration->modify("-{$this->rules['expiration_days']} days");
        
        return $lastChanged < $expiration;
    }
    
    public function getExpirationDays(): int {
        return $this->rules['expiration_days'];
    }
}

// Usage examples
$passwordSecurity = new PasswordSecurity('bcrypt', ['cost' => 14]);
$passwordPolicy = new PasswordPolicy();

// Test password hashing
$password = 'MySecurePassword123!';
$hash = $passwordSecurity->hashPassword($password);
echo "Password hashed: " . $hash . "\n";

// Test password verification
$isValid = $passwordSecurity->verifyPassword($password, $hash);
echo "Password verification: " . ($isValid ? 'Valid' : 'Invalid') . "\n";

// Test password strength
$strength = $passwordSecurity->validatePasswordStrength($password);
echo "Password strength: {$strength['strength']} (Score: {$strength['score']})\n";
if (!empty($strength['issues'])) {
    echo "Issues: " . implode(', ', $strength['issues']) . "\n";
}

// Test secure password generation
$securePassword = $passwordSecurity->generateSecurePassword(12);
echo "Generated secure password: $securePassword\n";

// Test password policy validation
$policyErrors = $passwordPolicy->validate($password);
if (empty($policyErrors)) {
    echo "Password meets policy requirements\n";
} else {
    echo "Policy violations: " . implode(', ', $policyErrors) . "\n";
}

// Test password expiration
$lastChanged = '2023-01-01';
$isExpired = $passwordPolicy->isPasswordExpired($lastChanged);
echo "Password expired: " . ($isExpired ? 'Yes' : 'No') . "\n";

// Log password events
$passwordSecurity->logPasswordEvent('password_changed', ['user_id' => 123]);
$passwordSecurity->logPasswordEvent('password_reset_requested', ['email' => 'user@example.com']);

// Test password reset token
$resetToken = $passwordSecurity->generatePasswordResetToken();
echo "Password reset token: $resetToken\n";

// Store token and timestamp in session
$_SESSION['password_reset_token'] = $resetToken;
$_SESSION['password_reset_timestamp'] = time();

// Validate token (would be done on the reset page)
$tokenValid = $passwordSecurity->validatePasswordResetToken($resetToken, $resetToken);
echo "Reset token valid: " . ($tokenValid ? 'Yes' : 'No') . "\n";
?>
```

## Best Practices

### Security Best Practices
```php
<?php
class SecurityBestPractices {
    private array $config;
    
    public function __construct(array $config = []) {
        $this->config = array_merge([
            'session_timeout' => 3600,
            'max_login_attempts' => 5,
            'lockout_duration' => 900,
            'password_min_length' => 8,
            'require_https' => true,
            'enable_csrf' => true,
            'enable_xss_protection' => true,
            'enable_sql_injection_protection' => true
        ], $config);
    }
    
    // 1. Always use HTTPS in production
    public function enforceHttps(): void {
        if ($this->config['require_https'] && !isset($_SERVER['HTTPS'])) {
            $httpsUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . $httpsUrl);
            exit;
        }
        
        // Set HSTS header
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
    
    // 2. Use secure session configuration
    public function configureSecureSession(): void {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', 1);
        ini_set('session.gc_maxlifetime', $this->config['session_timeout']);
        
        // Start session
        session_start();
        
        // Regenerate session ID on login
        if (!isset($_SESSION['initialized'])) {
            session_regenerate_id(true);
            $_SESSION['initialized'] = true;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity']) > $this->config['session_timeout']) {
            session_destroy();
            header('Location: /login?timeout=1');
            exit;
        }
        
        $_SESSION['last_activity'] = time();
    }
    
    // 3. Implement rate limiting
    public function checkRateLimit(string $identifier, int $limit = 100, int $window = 3600): bool {
        $key = 'rate_limit_' . md5($identifier);
        $current = $this->getRateLimitCount($key);
        
        if ($current >= $limit) {
            $this->logSecurityEvent('rate_limit_exceeded', [
                'identifier' => $identifier,
                'limit' => $limit,
                'window' => $window
            ]);
            return false;
        }
        
        $this->incrementRateLimitCount($key, $window);
        return true;
    }
    
    private function getRateLimitCount(string $key): int {
        // In production, use Redis or database
        return $_SESSION[$key] ?? 0;
    }
    
    private function incrementRateLimitCount(string $key, int $window): void {
        // In production, use Redis with expiration
        $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
    }
    
    // 4. Implement login attempt limiting
    public function checkLoginAttempts(string $email): bool {
        $key = 'login_attempts_' . md5($email);
        $attempts = $_SESSION[$key] ?? ['count' => 0, 'lockout_until' => 0];
        
        // Check if user is locked out
        if ($attempts['lockout_until'] > time()) {
            $this->logSecurityEvent('account_locked_out', [
                'email' => $email,
                'lockout_until' => $attempts['lockout_until']
            ]);
            return false;
        }
        
        // Check if max attempts reached
        if ($attempts['count'] >= $this->config['max_login_attempts']) {
            $attempts['lockout_until'] = time() + $this->config['lockout_duration'];
            $_SESSION[$key] = $attempts;
            
            $this->logSecurityEvent('max_login_attempts_exceeded', [
                'email' => $email,
                'attempts' => $attempts['count']
            ]);
            
            return false;
        }
        
        return true;
    }
    
    public function recordLoginAttempt(string $email, bool $success): void {
        $key = 'login_attempts_' . md5($email);
        $attempts = $_SESSION[$key] ?? ['count' => 0, 'lockout_until' => 0];
        
        if ($success) {
            // Reset on successful login
            unset($_SESSION[$key]);
            $this->logSecurityEvent('login_success', ['email' => $email]);
        } else {
            // Increment failed attempts
            $attempts['count']++;
            $_SESSION[$key] = $attempts;
            
            $this->logSecurityEvent('login_failed', [
                'email' => $email,
                'attempt_count' => $attempts['count']
            ]);
        }
    }
    
    // 5. Validate and sanitize all input
    public function validateAndSanitizeInput(array $input): array {
        $sanitized = [];
        
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->validateAndSanitizeInput($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    private function sanitizeString(string $input): string {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // Check for suspicious patterns
        if ($this->containsSuspiciousPatterns($input)) {
            $this->logSecurityEvent('suspicious_input_detected', ['input' => $input]);
            $input = $this->removeDangerousContent($input);
        }
        
        return $input;
    }
    
    private function containsSuspiciousPatterns(string $input): bool {
        $patterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/on\w+\s*=/i',
            '/expression\s*\(/i',
            '/union\s+select/i',
            '/drop\s+table/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/delete\s+from/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function removeDangerousContent(string $input): string {
        // Remove script tags
        $input = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $input);
        
        // Remove dangerous protocols
        $input = preg_replace('/(javascript|vbscript|data):/i', '', $input);
        
        // Remove event handlers
        $input = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/', '', $input);
        
        return $input;
    }
    
    // 6. Implement proper access control
    public function checkPermissions(string $resource, string $action, ?int $userId = null): bool {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? null;
        }
        
        if (!$userId) {
            return false;
        }
        
        // Check user permissions (would typically use database)
        $userPermissions = $this->getUserPermissions($userId);
        
        $requiredPermission = $resource . '.' . $action;
        
        return in_array($requiredPermission, $userPermissions) || 
               in_array('*', $userPermissions); // Super admin
    }
    
    private function getUserPermissions(int $userId): array {
        // In production, fetch from database
        // This is just an example
        return [
            'users.read',
            'users.update',
            'posts.read',
            'posts.create',
            'posts.update'
        ];
    }
    
    // 7. Implement secure file uploads
    public function handleFileUpload(array $file, array $allowedTypes = [], int $maxSize = 5242880): array {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'Invalid file upload';
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            $errors[] = 'File type not allowed';
        }
        
        // Check file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'File extension not allowed';
        }
        
        // Scan for malware (in production, use antivirus)
        if ($this->scanForMalware($file['tmp_name'])) {
            $errors[] = 'File appears to contain malicious content';
        }
        
        if (!empty($errors)) {
            $this->logSecurityEvent('file_upload_blocked', [
                'filename' => $file['name'],
                'mime_type' => $mimeType,
                'size' => $file['size'],
                'errors' => $errors
            ]);
            
            return ['success' => false, 'errors' => $errors];
        }
        
        // Generate safe filename
        $safeFilename = $this->generateSafeFilename($file['name'], $extension);
        
        // Move file to secure location
        $uploadPath = $this->getSecureUploadPath() . '/' . $safeFilename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $this->logSecurityEvent('file_upload_success', [
                'original_filename' => $file['name'],
                'safe_filename' => $safeFilename,
                'path' => $uploadPath
            ]);
            
            return [
                'success' => true,
                'filename' => $safeFilename,
                'path' => $uploadPath
            ];
        } else {
            $errors[] = 'Failed to save file';
            return ['success' => false, 'errors' => $errors];
        }
    }
    
    private function scanForMalware(string $filePath): bool {
        // In production, integrate with antivirus software
        // This is a basic check for suspicious content
        $content = file_get_contents($filePath);
        
        $suspiciousPatterns = [
            '/eval\s*\(/i',
            '/base64_decode\s*\(/i',
            '/shell_exec\s*\(/i',
            '/system\s*\(/i',
            '/passthru\s*\(/i'
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function generateSafeFilename(string $originalName, string $extension): string {
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $basename = preg_replace('/[^a-zA-Z0-9]/', '_', $basename);
        $basename = substr($basename, 0, 50);
        
        return $basename . '_' . uniqid() . '.' . $extension;
    }
    
    private function getSecureUploadPath(): string {
        $path = __DIR__ . '/uploads';
        
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        
        return $path;
    }
    
    // 8. Implement security headers
    public function setSecurityHeaders(): void {
        // XSS Protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Content Type Options
        header('X-Content-Type-Options: nosniff');
        
        // Frame Options
        header('X-Frame-Options: SAMEORIGIN');
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline'; " .
               "style-src 'self' 'unsafe-inline'; " .
               "img-src 'self' data: https:; " .
               "font-src 'self'; " .
               "connect-src 'self'; " .
               "frame-ancestors 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self'";
        
        header("Content-Security-Policy: $csp");
        
        // Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()');
    }
    
    // 9. Log security events
    public function logSecurityEvent(string $event, array $context = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'context' => array_merge([
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                'user_id' => $_SESSION['user_id'] ?? null
            ], $context)
        ];
        
        error_log('Security Event: ' . json_encode($logEntry));
        
        // In production, also send to security monitoring service
        $this->sendToSecurityMonitoring($logEntry);
    }
    
    private function sendToSecurityMonitoring(array $logEntry): void {
        // Integration with security monitoring service
        // This could be Splunk, ELK stack, or specialized security service
    }
    
    // 10. Implement secure password recovery
    public function handlePasswordReset(string $email): array {
        $errors = [];
        
        // Rate limiting for password reset requests
        if (!$this->checkRateLimit('password_reset_' . $email, 3, 3600)) {
            $errors[] = 'Too many password reset requests. Please try again later.';
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if user exists (would use database in production)
        if (!$this->userExists($email)) {
            // Don't reveal if user exists or not
            $this->logSecurityEvent('password_reset_requested_nonexistent_user', ['email' => $email]);
            return ['success' => true, 'message' => 'If an account exists, a reset link has been sent.'];
        }
        
        // Generate secure reset token
        $token = bin2hex(random_bytes(32));
        $expires = time() + 3600; // 1 hour
        
        // Store token (would use database in production)
        $_SESSION['password_reset_token'] = $token;
        $_SESSION['password_reset_expires'] = $expires;
        $_SESSION['password_reset_email'] = $email;
        
        // Send reset email (would use email service in production)
        $this->sendPasswordResetEmail($email, $token);
        
        $this->logSecurityEvent('password_reset_requested', ['email' => $email]);
        
        return ['success' => true, 'message' => 'If an account exists, a reset link has been sent.'];
    }
    
    private function userExists(string $email): bool {
        // In production, check database
        return true; // For example
    }
    
    private function sendPasswordResetEmail(string $email, string $token): void {
        // In production, use proper email service
        $resetLink = "https://example.com/reset-password?token=$token";
        
        // Log instead of sending for example
        $this->logSecurityEvent('password_reset_email_sent', [
            'email' => $email,
            'reset_link' => $resetLink
        ]);
    }
}

// Usage examples
$security = new SecurityBestPractices();

// Enforce HTTPS
$security->enforceHttps();

// Configure secure session
$security->configureSecureSession();

// Set security headers
$security->setSecurityHeaders();

// Check rate limiting
if (!$security->checkRateLimit($_SERVER['REMOTE_ADDR'], 100, 3600)) {
    http_response_code(429);
    echo 'Rate limit exceeded';
    exit;
}

// Validate and sanitize input
$userInput = [
    'name' => '<script>alert("XSS")</script>John',
    'email' => 'john@example.com',
    'message' => 'Hello, world!'
];

$sanitizedInput = $security->validateAndSanitizeInput($userInput);
echo "Sanitized input:\n";
print_r($sanitizedInput);

// Check login attempts
$email = 'user@example.com';
if ($security->checkLoginAttempts($email)) {
    // Process login
    $success = true; // Would check actual credentials
    $security->recordLoginAttempt($email, $success);
    
    if ($success) {
        echo "Login successful\n";
    } else {
        echo "Login failed\n";
    }
} else {
    echo "Account locked out due to too many failed attempts\n";
}

// Check permissions
$userId = 123;
if ($security->checkPermissions('users', 'read', $userId)) {
    echo "Permission granted\n";
} else {
    echo "Permission denied\n";
}

// Handle file upload
if (isset($_FILES['avatar'])) {
    $uploadResult = $security->handleFileUpload($_FILES['avatar'], ['image/jpeg', 'image/png'], 1048576);
    
    if ($uploadResult['success']) {
        echo "File uploaded successfully: " . $uploadResult['filename'] . "\n";
    } else {
        echo "Upload failed: " . implode(', ', $uploadResult['errors']) . "\n";
    }
}

// Handle password reset
$resetResult = $security->handlePasswordReset('user@example.com');
echo "Password reset: " . $resetResult['message'] . "\n";
?>
```

## Summary

PHP Security provides:

**Input Validation:**
- Comprehensive validation rules and patterns
- Input sanitization and filtering
- File upload security
- Custom validation patterns

**XSS Prevention:**
- HTML escaping and sanitization
- Content Security Policy headers
- XSS detection and logging
- Secure template rendering

**SQL Injection Prevention:**
- Prepared statements and parameter binding
- SQL injection detection
- Safe query construction
- Transaction security

**CSRF Protection:**
- Token generation and validation
- Form protection middleware
- AJAX token handling
- SameSite cookie configuration

**Password Security:**
- Modern hashing algorithms (bcrypt, Argon2)
- Password strength validation
- Secure password generation
- Password reset tokens

**Best Practices:**
- HTTPS enforcement
- Secure session management
- Rate limiting
- Access control
- Security headers
- Event logging
- File upload security

**Common Pitfalls:**
- Insufficient input validation
- Weak password policies
- Missing security headers
- Improper error handling
- Inadequate logging
- Outdated dependencies

PHP provides comprehensive security features that, when implemented correctly, create robust and secure web applications protected against common attack vectors.
