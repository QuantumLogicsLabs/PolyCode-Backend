<?php
/**
 * API Security Implementation
 * 
 * Comprehensive security measures for RESTful and GraphQL APIs.
 */

// JWT Authentication
class JWTAuth
{
    private string $secretKey;
    private int $expiryTime;
    private string $algorithm;
    
    public function __construct(string $secretKey, int $expiryTime = 3600, string $algorithm = 'HS256')
    {
        $this->secretKey = $secretKey;
        $this->expiryTime = $expiryTime;
        $this->algorithm = $algorithm;
    }
    
    /**
     * Generate JWT token
     */
    public function generateToken(array $payload): string
    {
        $header = [
            'alg' => $this->algorithm,
            'typ' => 'JWT'
        ];
        
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expiryTime;
        
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $this->secretKey, true);
        $signatureEncoded = $this->base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
    
    /**
     * Verify JWT token
     */
    public function verifyToken(string $token): ?array
    {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return null;
        }
        
        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
        
        // Verify signature
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $this->secretKey, true);
        $expectedSignature = $this->base64UrlEncode($signature);
        
        if (!hash_equals($expectedSignature, $signatureEncoded)) {
            return null;
        }
        
        // Decode payload
        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
        
        if (!$payload || !isset($payload['exp'])) {
            return null;
        }
        
        // Check expiration
        if ($payload['exp'] < time()) {
            return null;
        }
        
        return $payload;
    }
    
    /**
     * Refresh token
     */
    public function refreshToken(string $token): ?string
    {
        $payload = $this->verifyToken($token);
        
        if (!$payload) {
            return null;
        }
        
        // Remove time-based claims
        unset($payload['iat'], $payload['exp']);
        
        return $this->generateToken($payload);
    }
    
    /**
     * Base64 URL encode
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

// API Key Authentication
class ApiKeyAuth
{
    private array $apiKeys = [];
    private array $rateLimits = [];
    
    public function __construct()
    {
        $this->initializeApiKeys();
    }
    
    /**
     * Initialize API keys
     */
    private function initializeApiKeys(): void
    {
        $this->apiKeys = [
            'sk_test_1234567890abcdef' => [
                'id' => 'key_123',
                'name' => 'Test Key',
                'permissions' => ['read', 'write'],
                'rate_limit' => 1000,
                'created_at' => '2024-01-01'
            ],
            'sk_live_0987654321fedcba' => [
                'id' => 'key_456',
                'name' => 'Production Key',
                'permissions' => ['read', 'write', 'delete'],
                'rate_limit' => 5000,
                'created_at' => '2024-01-15'
            ]
        ];
    }
    
    /**
     * Validate API key
     */
    public function validateKey(string $apiKey): ?array
    {
        return $this->apiKeys[$apiKey] ?? null;
    }
    
    /**
     * Check permissions
     */
    public function hasPermission(string $apiKey, string $permission): bool
    {
        $keyInfo = $this->validateKey($apiKey);
        
        if (!$keyInfo) {
            return false;
        }
        
        return in_array($permission, $keyInfo['permissions']);
    }
    
    /**
     * Check rate limit
     */
    public function checkRateLimit(string $apiKey): bool
    {
        $keyInfo = $this->validateKey($apiKey);
        
        if (!$keyInfo) {
            return false;
        }
        
        $keyId = $keyInfo['id'];
        $limit = $keyInfo['rate_limit'];
        $window = 60; // 1 minute window
        
        $current = time();
        $windowStart = $current - ($current % $window);
        
        if (!isset($this->rateLimits[$keyId])) {
            $this->rateLimits[$keyId] = [];
        }
        
        // Clean old windows
        $this->rateLimits[$keyId] = array_filter(
            $this->rateLimits[$keyId],
            function($timestamp) use ($windowStart) {
                return $timestamp >= $windowStart;
            }
        );
        
        // Check current count
        if (count($this->rateLimits[$keyId]) >= $limit) {
            return false;
        }
        
        // Record this request
        $this->rateLimits[$keyId][] = $current;
        
        return true;
    }
    
    /**
     * Get rate limit info
     */
    public function getRateLimitInfo(string $apiKey): array
    {
        $keyInfo = $this->validateKey($apiKey);
        
        if (!$keyInfo) {
            return ['limit' => 0, 'remaining' => 0, 'reset' => 0];
        }
        
        $keyId = $keyInfo['id'];
        $limit = $keyInfo['rate_limit'];
        $window = 60;
        
        $current = time();
        $windowStart = $current - ($current % $window);
        $windowEnd = $windowStart + $window;
        
        $count = isset($this->rateLimits[$keyId]) ? count($this->rateLimits[$keyId]) : 0;
        $remaining = max(0, $limit - $count);
        
        return [
            'limit' => $limit,
            'remaining' => $remaining,
            'reset' => $windowEnd
        ];
    }
}

// Input Validation and Sanitization
class ApiValidator
{
    private array $rules = [];
    private array $errors = [];
    
    /**
     * Add validation rule
     */
    public function addRule(string $field, array $rules): self
    {
        $this->rules[$field] = $rules;
        return $this;
    }
    
    /**
     * Validate input data
     */
    public function validate(array $data): bool
    {
        $this->errors = [];
        
        foreach ($this->rules as $field => $rules) {
            $value = $data[$field] ?? null;
            
            foreach ($rules as $rule => $params) {
                if (!$this->validateRule($field, $value, $rule, $params)) {
                    break; // Stop on first error for field
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate individual rule
     */
    private function validateRule(string $field, mixed $value, string $rule, mixed $params): bool
    {
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->errors[$field][] = "Field $field is required";
                    return false;
                }
                break;
                
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "Field $field must be a valid email";
                    return false;
                }
                break;
                
            case 'min_length':
                if ($value && strlen($value) < $params) {
                    $this->errors[$field][] = "Field $field must be at least $params characters";
                    return false;
                }
                break;
                
            case 'max_length':
                if ($value && strlen($value) > $params) {
                    $this->errors[$field][] = "Field $field must not exceed $params characters";
                    return false;
                }
                break;
                
            case 'regex':
                if ($value && !preg_match($params, $value)) {
                    $this->errors[$field][] = "Field $field format is invalid";
                    return false;
                }
                break;
                
            case 'in':
                if ($value && !in_array($value, $params)) {
                    $this->errors[$field][] = "Field $field must be one of: " . implode(', ', $params);
                    return false;
                }
                break;
                
            case 'numeric':
                if ($value && !is_numeric($value)) {
                    $this->errors[$field][] = "Field $field must be numeric";
                    return false;
                }
                break;
                
            case 'integer':
                if ($value && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->errors[$field][] = "Field $field must be an integer";
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Sanitize input data
     */
    public function sanitize(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Remove HTML tags and special characters
                $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
}

// CORS Handler
class CorsHandler
{
    private array $allowedOrigins = [];
    private array $allowedMethods = [];
    private array $allowedHeaders = [];
    private bool $allowCredentials = false;
    private int $maxAge = 86400;
    
    public function __construct()
    {
        $this->initializeCorsSettings();
    }
    
    /**
     * Initialize CORS settings
     */
    private function initializeCorsSettings(): void
    {
        $this->allowedOrigins = [
            'http://localhost:3000',
            'https://example.com',
            'https://app.example.com'
        ];
        
        $this->allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        $this->allowedHeaders = ['Content-Type', 'Authorization', 'X-Requested-With'];
        $this->allowCredentials = true;
    }
    
    /**
     * Handle CORS request
     */
    public function handle(): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        $headers = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? '';
        
        // Check if origin is allowed
        if ($this->isOriginAllowed($origin)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        
        // Handle preflight request
        if ($method === 'OPTIONS') {
            header('Access-Control-Allow-Methods: ' . implode(', ', $this->allowedMethods));
            header('Access-Control-Allow-Headers: ' . implode(', ', $this->allowedHeaders));
            header('Access-Control-Max-Age: ' . $this->maxAge);
            
            if ($this->allowCredentials) {
                header('Access-Control-Allow-Credentials: true');
            }
            
            http_response_code(204);
            exit;
        }
        
        // Add credentials header if allowed
        if ($this->allowCredentials) {
            header('Access-Control-Allow-Credentials: true');
        }
    }
    
    /**
     * Check if origin is allowed
     */
    private function isOriginAllowed(string $origin): bool
    {
        if (in_array('*', $this->allowedOrigins)) {
            return true;
        }
        
        return in_array($origin, $this->allowedOrigins);
    }
    
    /**
     * Add allowed origin
     */
    public function addAllowedOrigin(string $origin): void
    {
        $this->allowedOrigins[] = $origin;
    }
    
    /**
     * Set allowed methods
     */
    public function setAllowedMethods(array $methods): void
    {
        $this->allowedMethods = $methods;
    }
    
    /**
     * Set allowed headers
     */
    public function setAllowedHeaders(array $headers): void
    {
        $this->allowedHeaders = $headers;
    }
}

// Rate Limiting
class RateLimiter
{
    private array $limits = [];
    private array $attempts = [];
    private string $storageType;
    
    public function __construct(string $storageType = 'memory')
    {
        $this->storageType = $storageType;
    }
    
    /**
     * Set rate limit
     */
    public function setLimit(string $key, int $requests, int $window): void
    {
        $this->limits[$key] = [
            'requests' => $requests,
            'window' => $window
        ];
    }
    
    /**
     * Check rate limit
     */
    public function check(string $key, string $identifier = null): bool
    {
        $limit = $this->limits[$key] ?? null;
        
        if (!$limit) {
            return true;
        }
        
        $id = $identifier ?? $this->getClientIdentifier();
        $window = $limit['window'];
        $maxRequests = $limit['requests'];
        
        $current = time();
        $windowStart = $current - ($current % $window);
        
        if (!isset($this->attempts[$id])) {
            $this->attempts[$id] = [];
        }
        
        // Clean old attempts
        $this->attempts[$id] = array_filter(
            $this->attempts[$id],
            function($timestamp) use ($windowStart) {
                return $timestamp >= $windowStart;
            }
        );
        
        // Check if limit exceeded
        if (count($this->attempts[$id]) >= $maxRequests) {
            return false;
        }
        
        // Record attempt
        $this->attempts[$id][] = $current;
        
        return true;
    }
    
    /**
     * Get rate limit info
     */
    public function getInfo(string $key, string $identifier = null): array
    {
        $limit = $this->limits[$key] ?? null;
        
        if (!$limit) {
            return ['limit' => 0, 'remaining' => 0, 'reset' => 0];
        }
        
        $id = $identifier ?? $this->getClientIdentifier();
        $window = $limit['window'];
        $maxRequests = $limit['requests'];
        
        $current = time();
        $windowStart = $current - ($current % $window);
        $windowEnd = $windowStart + $window;
        
        $count = isset($this->attempts[$id]) ? count($this->attempts[$id]) : 0;
        $remaining = max(0, $maxRequests - $count);
        
        return [
            'limit' => $maxRequests,
            'remaining' => $remaining,
            'reset' => $windowEnd
        ];
    }
    
    /**
     * Get client identifier
     */
    private function getClientIdentifier(): string
    {
        // Try to get client IP
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
             $_SERVER['HTTP_X_REAL_IP'] ?? 
             $_SERVER['REMOTE_ADDR'] ?? 
             'unknown';
        
        // Add user agent for more specific identification
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        return md5($ip . $userAgent);
    }
}

// Security Middleware
class SecurityMiddleware
{
    private JWTAuth $jwtAuth;
    private ApiKeyAuth $apiKeyAuth;
    private ApiValidator $validator;
    private CorsHandler $cors;
    private RateLimiter $rateLimiter;
    
    public function __construct()
    {
        $this->jwtAuth = new JWTAuth('your-secret-key-here');
        $this->apiKeyAuth = new ApiKeyAuth();
        $this->validator = new ApiValidator();
        $this->cors = new CorsHandler();
        $this->rateLimiter = new RateLimiter();
        
        $this->setupRateLimits();
    }
    
    /**
     * Setup rate limits
     */
    private function setupRateLimits(): void
    {
        $this->rateLimiter->setLimit('api', 1000, 3600); // 1000 requests per hour
        $this->rateLimiter->setLimit('auth', 10, 300);   // 10 auth requests per 5 minutes
    }
    
    /**
     * Handle CORS
     */
    public function handleCors(): void
    {
        $this->cors->handle();
    }
    
    /**
     * Authenticate request
     */
    public function authenticate(): ?array
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (!$authHeader) {
            return null;
        }
        
        // Check for Bearer token (JWT)
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
            return $this->jwtAuth->verifyToken($token);
        }
        
        // Check for API key
        if (strpos($authHeader, 'ApiKey ') === 0) {
            $apiKey = substr($authHeader, 7);
            return $this->apiKeyAuth->validateKey($apiKey);
        }
        
        return null;
    }
    
    /**
     * Validate input
     */
    public function validate(array $data, array $rules): array
    {
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule => $params) {
                $this->validator->addRule($field, [$rule => $params]);
            }
        }
        
        if (!$this->validator->validate($data)) {
            return [
                'valid' => false,
                'errors' => $this->validator->getErrors()
            ];
        }
        
        return [
            'valid' => true,
            'data' => $this->validator->sanitize($data)
        ];
    }
    
    /**
     * Check rate limit
     */
    public function checkRateLimit(string $key): bool
    {
        return $this->rateLimiter->check($key);
    }
    
    /**
     * Get rate limit headers
     */
    public function getRateLimitHeaders(string $key): array
    {
        $info = $this->rateLimiter->getInfo($key);
        
        return [
            'X-RateLimit-Limit' => $info['limit'],
            'X-RateLimit-Remaining' => $info['remaining'],
            'X-RateLimit-Reset' => $info['reset']
        ];
    }
}

// Security Examples
class ApiSecurityExamples
{
    private SecurityMiddleware $middleware;
    
    public function __construct()
    {
        $this->middleware = new SecurityMiddleware();
    }
    
    public function demonstrateJWT(): void
    {
        echo "JWT Authentication Demo\n";
        echo str_repeat("-", 28) . "\n";
        
        $jwt = new JWTAuth('demo-secret-key');
        
        // Generate token
        $payload = [
            'user_id' => 123,
            'email' => 'user@example.com',
            'role' => 'admin'
        ];
        
        $token = $jwt->generateToken($payload);
        echo "Generated Token: $token\n\n";
        
        // Verify token
        $verified = $jwt->verifyToken($token);
        echo "Verified Payload:\n";
        print_r($verified);
        echo "\n";
        
        // Refresh token
        $refreshed = $jwt->refreshToken($token);
        echo "Refreshed Token: $refreshed\n\n";
        
        // Test invalid token
        $invalid = $jwt->verifyToken('invalid.token.here');
        echo "Invalid Token Result: " . ($invalid ? 'Valid' : 'Invalid') . "\n";
    }
    
    public function demonstrateApiKey(): void
    {
        echo "\nAPI Key Authentication Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        $apiKey = 'sk_test_1234567890abcdef';
        $auth = new ApiKeyAuth();
        
        // Validate key
        $keyInfo = $auth->validateKey($apiKey);
        echo "Key Info:\n";
        print_r($keyInfo);
        echo "\n";
        
        // Check permissions
        $hasRead = $auth->hasPermission($apiKey, 'read');
        $hasDelete = $auth->hasPermission($apiKey, 'delete');
        echo "Has Read Permission: " . ($hasRead ? 'Yes' : 'No') . "\n";
        echo "Has Delete Permission: " . ($hasDelete ? 'Yes' : 'No') . "\n\n";
        
        // Check rate limit
        for ($i = 0; $i < 5; $i++) {
            $allowed = $auth->checkRateLimit($apiKey);
            echo "Request " . ($i + 1) . ": " . ($allowed ? 'Allowed' : 'Blocked') . "\n";
        }
        
        // Get rate limit info
        $rateInfo = $auth->getRateLimitInfo($apiKey);
        echo "\nRate Limit Info:\n";
        print_r($rateInfo);
    }
    
    public function demonstrateValidation(): void
    {
        echo "\nInput Validation Demo\n";
        echo str_repeat("-", 26) . "\n";
        
        $validator = new ApiValidator();
        
        // Add validation rules
        $validator->addRule('name', ['required' => true, 'min_length' => 2, 'max_length' => 50]);
        $validator->addRule('email', ['required' => true, 'email' => true]);
        $validator->addRule('age', ['integer' => true, 'min_length' => 18]);
        $validator->addRule('status', ['in' => ['active', 'inactive', 'pending']]);
        
        // Test valid data
        $validData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 25,
            'status' => 'active'
        ];
        
        $isValid = $validator->validate($validData);
        echo "Valid Data Result: " . ($isValid ? 'Valid' : 'Invalid') . "\n";
        
        if (!$isValid) {
            print_r($validator->getErrors());
        }
        
        // Test invalid data
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'age' => 15,
            'status' => 'unknown'
        ];
        
        $isValid = $validator->validate($invalidData);
        echo "\nInvalid Data Result: " . ($isValid ? 'Valid' : 'Invalid') . "\n";
        print_r($validator->getErrors());
        
        // Sanitization
        $sanitized = $validator->sanitize([
            'name' => '  <script>alert("xss")</script> John  ',
            'email' => '  JOHN@EXAMPLE.COM  '
        ]);
        echo "\nSanitized Data:\n";
        print_r($sanitized);
    }
    
    public function demonstrateRateLimiting(): void
    {
        echo "\nRate Limiting Demo\n";
        echo str_repeat("-", 22) . "\n";
        
        $rateLimiter = new RateLimiter();
        $rateLimiter->setLimit('api', 5, 60); // 5 requests per minute
        
        $clientId = 'test-client-123';
        
        echo "Testing rate limit for client: $clientId\n";
        echo "Limit: 5 requests per minute\n\n";
        
        for ($i = 0; $i < 7; $i++) {
            $allowed = $rateLimiter->check('api', $clientId);
            $info = $rateLimiter->getInfo('api', $clientId);
            
            echo "Request " . ($i + 1) . ": " . ($allowed ? 'Allowed' : 'Blocked');
            echo " (Remaining: {$info['remaining']})\n";
        }
        
        echo "\nRate Limit Info:\n";
        print_r($info);
    }
    
    public function demonstrateSecurityMiddleware(): void
    {
        echo "\nSecurity Middleware Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Simulate request with JWT
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $this->middleware->jwtAuth->generateToken([
            'user_id' => 123,
            'email' => 'user@example.com'
        ]);
        
        $user = $this->middleware->authenticate();
        echo "JWT Authentication Result:\n";
        print_r($user);
        
        // Simulate validation
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];
        
        $rules = [
            'name' => ['required' => true, 'min_length' => 2],
            'email' => ['required' => true, 'email' => true]
        ];
        
        $validation = $this->middleware->validate($data, $rules);
        echo "\nValidation Result:\n";
        print_r($validation);
        
        // Rate limiting
        $rateAllowed = $this->middleware->checkRateLimit('api');
        $rateHeaders = $this->middleware->getRateLimitHeaders('api');
        
        echo "\nRate Limit Check: " . ($rateAllowed ? 'Allowed' : 'Blocked') . "\n";
        echo "Rate Limit Headers:\n";
        foreach ($rateHeaders as $name => $value) {
            echo "  $name: $value\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nAPI Security Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Authentication:\n";
        echo "   • Use JWT for stateless authentication\n";
        echo "   • Implement API key authentication for services\n";
        echo "   • Use secure token storage\n";
        echo "   • Implement token expiration and refresh\n";
        echo "   • Use HTTPS for all API communications\n\n";
        
        echo "2. Authorization:\n";
        echo "   • Implement role-based access control\n";
        echo "   • Check permissions on each request\n";
        echo "   • Use principle of least privilege\n";
        echo "   • Implement resource-based permissions\n";
        echo "   • Log authorization decisions\n\n";
        
        echo "3. Input Validation:\n";
        echo "   • Validate all input data\n";
        echo "   • Sanitize user input\n";
        echo "   • Use parameterized queries\n";
        echo "   • Implement XSS protection\n";
        echo "   • Validate file uploads\n\n";
        
        echo "4. Rate Limiting:\n";
        echo "   • Implement per-client rate limits\n";
        echo "   • Use different limits for different endpoints\n";
        echo "   • Provide rate limit headers\n";
        echo "   • Implement progressive backoff\n";
        echo "   • Monitor for abuse patterns\n\n";
        
        echo "5. CORS:\n";
        echo "   • Configure allowed origins carefully\n";
        echo "   • Specify allowed methods and headers\n";
        echo "   • Use credentials only when necessary\n";
        echo "   • Implement preflight handling\n";
        echo "   • Monitor CORS requests\n\n";
        
        echo "6. General Security:\n";
        echo "   • Keep dependencies updated\n";
        echo "   • Implement security headers\n";
        echo "   • Use security testing tools\n";
        echo "   • Monitor for security events\n";
        echo "   • Have an incident response plan";
    }
    
    public function runAllExamples(): void
    {
        echo "API Security Implementation Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateJWT();
        $this->demonstrateApiKey();
        $this->demonstrateValidation();
        $this->demonstrateRateLimiting();
        $this->demonstrateSecurityMiddleware();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runApiSecurityDemo(): void
{
    $examples = new ApiSecurityExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runApiSecurityDemo();
}
?>
