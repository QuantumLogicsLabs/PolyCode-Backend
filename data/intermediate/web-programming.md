# PHP Web Programming

## HTTP Fundamentals

### HTTP Methods and Status Codes
```php
<?php
// HTTP Method handling
class HttpRequestHandler {
    public function handleRequest(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        // Log request
        $this->logRequest($method, $uri);
        
        // Route based on method
        switch ($method) {
            case 'GET':
                $this->handleGet();
                break;
            case 'POST':
                $this->handlePost();
                break;
            case 'PUT':
                $this->handlePut();
                break;
            case 'DELETE':
                $this->handleDelete();
                break;
            case 'PATCH':
                $this->handlePatch();
                break;
            case 'OPTIONS':
                $this->handleOptions();
                break;
            default:
                $this->sendResponse(405, 'Method Not Allowed');
        }
    }
    
    private function handleGet(): void {
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $this->sendResponse(200, 'Resource with ID: ' . $id);
        } else {
            $this->sendResponse(200, 'List of resources');
        }
    }
    
    private function handlePost(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $this->sendResponse(400, 'Invalid JSON');
            return;
        }
        
        // Process data and create resource
        $resourceId = $this->createResource($data);
        $this->sendResponse(201, 'Resource created with ID: ' . $resourceId);
    }
    
    private function handlePut(): void {
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$id || !$data) {
            $this->sendResponse(400, 'Missing ID or data');
            return;
        }
        
        // Update resource
        $success = $this->updateResource($id, $data);
        
        if ($success) {
            $this->sendResponse(200, 'Resource updated');
        } else {
            $this->sendResponse(404, 'Resource not found');
        }
    }
    
    private function handleDelete(): void {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->sendResponse(400, 'Missing ID');
            return;
        }
        
        $success = $this->deleteResource($id);
        
        if ($success) {
            $this->sendResponse(204, ''); // No content
        } else {
            $this->sendResponse(404, 'Resource not found');
        }
    }
    
    private function handlePatch(): void {
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$id || !$data) {
            $this->sendResponse(400, 'Missing ID or data');
            return;
        }
        
        // Partial update
        $success = $this->patchResource($id, $data);
        
        if ($success) {
            $this->sendResponse(200, 'Resource patched');
        } else {
            $this->sendResponse(404, 'Resource not found');
        }
    }
    
    private function handleOptions(): void {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        $this->sendResponse(200, 'CORS preflight');
    }
    
    private function sendResponse(int $statusCode, string $body): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'status' => $statusCode,
            'message' => $body,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response);
        exit;
    }
    
    private function logRequest(string $method, string $uri): void {
        $logEntry = sprintf(
            "[%s] %s %s - IP: %s - User-Agent: %s\n",
            date('Y-m-d H:i:s'),
            $method,
            $uri,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        );
        
        file_put_contents('requests.log', $logEntry, FILE_APPEND);
    }
    
    // Mock methods for demonstration
    private function createResource(array $data): int {
        return random_int(1, 1000);
    }
    
    private function updateResource(int $id, array $data): bool {
        return true;
    }
    
    private function deleteResource(int $id): bool {
        return true;
    }
    
    private function patchResource(int $id, array $data): bool {
        return true;
    }
}

// Status code utilities
class HttpStatus {
    // Success codes
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NO_CONTENT = 204;
    
    // Redirection codes
    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;
    const NOT_MODIFIED = 304;
    
    // Client error codes
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const CONFLICT = 409;
    const UNPROCESSABLE_ENTITY = 422;
    const TOO_MANY_REQUESTS = 429;
    
    // Server error codes
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    
    public static function getMessage(int $code): string {
        $messages = [
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::ACCEPTED => 'Accepted',
            self::NO_CONTENT => 'No Content',
            self::MOVED_PERMANENTLY => 'Moved Permanently',
            self::FOUND => 'Found',
            self::NOT_MODIFIED => 'Not Modified',
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            self::CONFLICT => 'Conflict',
            self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            self::TOO_MANY_REQUESTS => 'Too Many Requests',
            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            self::NOT_IMPLEMENTED => 'Not Implemented',
            self::BAD_GATEWAY => 'Bad Gateway',
            self::SERVICE_UNAVAILABLE => 'Service Unavailable'
        ];
        
        return $messages[$code] ?? 'Unknown Status';
    }
    
    public static function sendJsonResponse(int $code, $data = null, string $message = null): void {
        http_response_code($code);
        header('Content-Type: application/json');
        
        $response = [
            'status' => $code,
            'message' => $message ?? self::getMessage($code),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit;
    }
}

// Usage
$handler = new HttpRequestHandler();
$handler->handleRequest();
?>
```

### Headers and Cookies
```php
<?php
class HttpHeaderManager {
    // Set custom headers
    public function setSecurityHeaders(): void {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Force HTTPS
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
        
        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
    
    // CORS headers
    public function setCorsHeaders(string $origin = '*'): void {
        header("Access-Control-Allow-Origin: $origin");
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // 24 hours
    }
    
    // Cache control headers
    public function setCacheHeaders(int $maxAge = 3600, bool $mustRevalidate = false): void {
        header('Cache-Control: public, max-age=' . $maxAge);
        
        if ($mustRevalidate) {
            header('Cache-Control: public, max-age=' . $maxAge . ', must-revalidate');
        }
        
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('ETag: "' . md5(time()) . '"');
    }
    
    // No cache headers
    public function setNoCacheHeaders(): void {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
    }
    
    // Download headers
    public function setDownloadHeaders(string $filename, string $contentType = 'application/octet-stream'): void {
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));
        header('Pragma: no-cache');
        header('Expires: 0');
    }
    
    // Get all request headers
    public function getRequestHeaders(): array {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $header = substr($key, 5);
                $header = str_replace('_', ' ', ucwords(strtolower($header)));
                $headers[$header] = $value;
            }
        }
        
        return $headers;
    }
    
    // Get specific request header
    public function getRequestHeader(string $name): ?string {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$name] ?? null;
    }
}

class CookieManager {
    // Set secure cookie
    public function setSecureCookie(string $name, string $value, int $expires = 0, string $path = '/', string $domain = '', bool $secure = true, bool $httponly = true, string $samesite = 'Strict'): void {
        $options = [
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite
        ];
        
        setcookie($name, $value, $options);
    }
    
    // Set session cookie
    public function setSessionCookie(string $sessionId, int $lifetime = 86400): void {
        $this->setSecureCookie(
            'PHPSESSID',
            $sessionId,
            time() + $lifetime,
            '/',
            '',
            true, // HTTPS only
            true, // HTTP only
            'Strict'
        );
    }
    
    // Set remember me cookie
    public function setRememberMeCookie(string $token, int $expires = 0): void {
        $this->setSecureCookie(
            'remember_me',
            $token,
            $expires,
            '/',
            '',
            true,
            true,
            'Lax'
        );
    }
    
    // Get cookie value
    public function getCookie(string $name): ?string {
        return $_COOKIE[$name] ?? null;
    }
    
    // Delete cookie
    public function deleteCookie(string $name, string $path = '/', string $domain = ''): void {
        setcookie($name, '', [
            'expires' => time() - 3600,
            'path' => $path,
            'domain' => $domain
        ]);
    }
    
    // Validate cookie integrity
    public function validateCookie(string $name, string $secret): bool {
        $cookie = $this->getCookie($name);
        
        if (!$cookie) {
            return false;
        }
        
        $parts = explode('|', $cookie);
        if (count($parts) !== 2) {
            return false;
        }
        
        $value = $parts[0];
        $signature = $parts[1];
        
        return hash_hmac('sha256', $value, $secret) === $signature;
    }
    
    // Create signed cookie
    public function createSignedCookie(string $name, string $value, string $secret, int $expires = 0): void {
        $signature = hash_hmac('sha256', $value, $secret);
        $signedValue = $value . '|' . $signature;
        
        $this->setSecureCookie($name, $signedValue, $expires);
    }
}

// Usage examples
$headerManager = new HttpHeaderManager();
$cookieManager = new CookieManager();

// Set security headers
$headerManager->setSecurityHeaders();

// Set CORS headers
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
$headerManager->setCorsHeaders($origin);

// Set cache headers
$headerManager->setCacheHeaders(3600, true);

// Set no cache for dynamic content
$headerManager->setNoCacheHeaders();

// Set download headers
$filename = 'document.pdf';
$headerManager->setDownloadHeaders($filename, 'application/pdf');

// Get request headers
$headers = $headerManager->getRequestHeaders();
$userAgent = $headerManager->getRequestHeader('User-Agent');

// Set secure cookies
$cookieManager->setSecureCookie('theme', 'dark', time() + 86400 * 30);
$cookieManager->setSessionCookie(session_id());
$cookieManager->setRememberMeCookie('token123', time() + 86400 * 30);

// Create signed cookie
$secret = 'your-secret-key';
$cookieManager->createSignedCookie('user_pref', 'dark_mode', $secret);

// Validate signed cookie
if ($cookieManager->validateCookie('user_pref', $secret)) {
    echo "Cookie is valid";
} else {
    echo "Cookie is invalid or tampered";
}
?>
```

## Session Management

### Session Configuration and Usage
```php
<?php
class SessionManager {
    private array $config;
    
    public function __construct(array $config = []) {
        $this->config = array_merge([
            'lifetime' => 86400, // 24 hours
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
            'use_strict_mode' => true,
            'use_cookies' => true,
            'use_only_cookies' => true,
            'cookie_httponly' => true,
            'cookie_secure' => true,
            'cookie_samesite' => 'Strict'
        ], $config);
        
        $this->configureSession();
    }
    
    private function configureSession(): void {
        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => $this->config['lifetime'],
            'path' => $this->config['path'],
            'domain' => $this->config['domain'],
            'secure' => $this->config['secure'],
            'httponly' => $this->config['httponly'],
            'samesite' => $this->config['samesite']
        ]);
        
        // Configure session settings
        ini_set('session.use_strict_mode', $this->config['use_strict_mode'] ? '1' : '0');
        ini_set('session.use_cookies', $this->config['use_cookies'] ? '1' : '0');
        ini_set('session.use_only_cookies', $this->config['use_only_cookies'] ? '1' : '0');
        ini_set('session.cookie_httponly', $this->config['cookie_httponly'] ? '1' : '0');
        ini_set('session.cookie_secure', $this->config['cookie_secure'] ? '1' : '0');
        ini_set('session.cookie_samesite', $this->config['cookie_samesite']);
        ini_set('session.gc_maxlifetime', $this->config['lifetime']);
        
        // Set session name
        session_name('app_session');
        
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // Session data management
    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }
    
    public function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }
    
    public function flash(string $key, $value): void {
        $_SESSION['flash'][$key] = $value;
    }
    
    public function getFlash(string $key, $default = null) {
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    
    public function hasFlash(string $key): bool {
        return isset($_SESSION['flash'][$key]);
    }
    
    // User authentication
    public function login(array $userData): void {
        $this->set('user_id', $userData['id']);
        $this->set('user_data', $userData);
        $this->set('login_time', time());
        $this->set('last_activity', time());
        
        // Regenerate session ID to prevent session fixation
        $this->regenerate();
    }
    
    public function logout(): void {
        // Clear all session data
        $_SESSION = [];
        
        // Delete session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite']
            ]);
        }
        
        // Destroy session
        session_destroy();
    }
    
    public function isLoggedIn(): bool {
        return $this->has('user_id');
    }
    
    public function getUserId(): ?int {
        return $this->get('user_id');
    }
    
    public function getUserData(): ?array {
        return $this->get('user_data');
    }
    
    // Session security
    public function regenerate(): void {
        session_regenerate_id(true);
    }
    
    public function validateSession(): bool {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Check session age
        $loginTime = $this->get('login_time');
        if ($loginTime && (time() - $loginTime) > $this->config['lifetime']) {
            return false;
        }
        
        // Check last activity
        $lastActivity = $this->get('last_activity');
        if ($lastActivity && (time() - $lastActivity) > 1800) { // 30 minutes
            return false;
        }
        
        // Update last activity
        $this->set('last_activity', time());
        
        return true;
    }
    
    // Session cleanup
    public function cleanup(): void {
        // Remove expired sessions
        $this->removeExpiredSessions();
        
        // Clean up flash messages
        if (isset($_SESSION['flash']) && empty($_SESSION['flash'])) {
            unset($_SESSION['flash']);
        }
    }
    
    private function removeExpiredSessions(): void {
        $sessionPath = session_save_path();
        $files = glob($sessionPath . '/sess_*');
        
        foreach ($files as $file) {
            if (filemtime($file) < time() - $this->config['lifetime']) {
                unlink($file);
            }
        }
    }
    
    // Session statistics
    public function getSessionInfo(): array {
        return [
            'id' => session_id(),
            'name' => session_name(),
            'status' => session_status(),
            'lifetime' => ini_get('session.gc_maxlifetime'),
            'save_path' => session_save_path(),
            'cookie_params' => session_get_cookie_params(),
            'data' => $_SESSION
        ];
    }
}

// Session middleware
class SessionMiddleware {
    private SessionManager $sessionManager;
    
    public function __construct(SessionManager $sessionManager) {
        $this->sessionManager = $sessionManager;
    }
    
    public function handle(): void {
        // Validate session
        if (!$this->sessionManager->validateSession()) {
            $this->sessionManager->logout();
            $this->redirectToLogin();
            return;
        }
        
        // Update last activity
        $this->sessionManager->set('last_activity', time());
        
        // Cleanup old sessions
        if (random_int(1, 100) === 1) { // 1% chance
            $this->sessionManager->cleanup();
        }
    }
    
    private function redirectToLogin(): void {
        header('Location: /login');
        exit;
    }
}

// Usage
$sessionConfig = [
    'lifetime' => 7200, // 2 hours
    'secure' => true,
    'httponly' => true
];

$sessionManager = new SessionManager($sessionConfig);
$sessionMiddleware = new SessionMiddleware($sessionManager);

// Handle session middleware
$sessionMiddleware->handle();

// Use session
if (!$sessionManager->isLoggedIn()) {
    header('Location: /login');
    exit;
}

// Set session data
$sessionManager->set('theme', 'dark');
$sessionManager->set('language', 'en');

// Get session data
$theme = $sessionManager->get('theme', 'light');
$language = $sessionManager->get('language', 'en');

// Flash messages
$sessionManager->flash('success', 'Profile updated successfully');
$sessionManager->flash('error', 'Invalid email format');

// Get flash messages
$successMessage = $sessionManager->getFlash('success');
$errorMessage = $sessionManager->getFlash('error');

// Login user
$userData = ['id' => 123, 'name' => 'John Doe', 'email' => 'john@example.com'];
$sessionManager->login($userData);

// Logout user
$sessionManager->logout();

// Get session info
$sessionInfo = $sessionManager->getSessionInfo();
?>
```

### Custom Session Handlers
```php
<?php
class DatabaseSessionHandler implements SessionHandlerInterface {
    private PDO $pdo;
    private string $table;
    private int $lifetime;
    
    public function __construct(PDO $pdo, string $table = 'sessions', int $lifetime = 86400) {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->lifetime = $lifetime;
        
        $this->createTable();
    }
    
    private function createTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id VARCHAR(128) PRIMARY KEY,
            data TEXT NOT NULL,
            timestamp INT(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $this->pdo->exec($sql);
    }
    
    public function open(string $savePath, string $sessionName): bool {
        return true;
    }
    
    public function close(): bool {
        return true;
    }
    
    public function read(string $sessionId): string {
        $sql = "SELECT data FROM {$this->table} WHERE id = ? AND timestamp > ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$sessionId, time() - $this->lifetime]);
        
        $result = $stmt->fetch();
        return $result ? $result['data'] : '';
    }
    
    public function write(string $sessionId, string $data): bool {
        $sql = "INSERT INTO {$this->table} (id, data, timestamp) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE data = ?, timestamp = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$sessionId, $data, time(), $data, time()]);
    }
    
    public function destroy(string $sessionId): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$sessionId]);
    }
    
    public function gc(int $maxLifetime): int {
        $sql = "DELETE FROM {$this->table} WHERE timestamp < ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([time() - $this->lifetime]);
        
        return $stmt->rowCount();
    }
}

class RedisSessionHandler implements SessionHandlerInterface {
    private Redis $redis;
    private string $prefix;
    private int $lifetime;
    
    public function __construct(Redis $redis, string $prefix = 'session:', int $lifetime = 86400) {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->lifetime = $lifetime;
    }
    
    public function open(string $savePath, string $sessionName): bool {
        return true;
    }
    
    public function close(): bool {
        return true;
    }
    
    public function read(string $sessionId): string {
        $key = $this->prefix . $sessionId;
        $data = $this->redis->get($key);
        
        return $data ?: '';
    }
    
    public function write(string $sessionId, string $data): bool {
        $key = $this->prefix . $sessionId;
        return $this->redis->setex($key, $this->lifetime, $data);
    }
    
    public function destroy(string $sessionId): bool {
        $key = $this->prefix . $sessionId;
        return $this->redis->del($key) > 0;
    }
    
    public function gc(int $maxLifetime): int {
        // Redis handles expiration automatically
        return 0;
    }
}

class MemcachedSessionHandler implements SessionHandlerInterface {
    private Memcached $memcached;
    private string $prefix;
    private int $lifetime;
    
    public function __construct(Memcached $memcached, string $prefix = 'session:', int $lifetime = 86400) {
        $this->memcached = $memcached;
        $this->prefix = $prefix;
        $this->lifetime = $lifetime;
    }
    
    public function open(string $savePath, string $sessionName): bool {
        return true;
    }
    
    public function close(): bool {
        return true;
    }
    
    public function read(string $sessionId): string {
        $key = $this->prefix . $sessionId;
        $data = $this->memcached->get($key);
        
        return $data ?: '';
    }
    
    public function write(string $sessionId, string $data): bool {
        $key = $this->prefix . $sessionId;
        return $this->memcached->set($key, $data, $this->lifetime);
    }
    
    public function destroy(string $sessionId): bool {
        $key = $this->prefix . $sessionId;
        return $this->memcached->delete($key);
    }
    
    public function gc(int $maxLifetime): int {
        // Memcached handles expiration automatically
        return 0;
    }
}

// Session handler factory
class SessionHandlerFactory {
    public static function create(string $type, array $config = []): SessionHandlerInterface {
        switch ($type) {
            case 'database':
                $pdo = $config['pdo'] ?? throw new InvalidArgumentException('PDO instance required');
                $table = $config['table'] ?? 'sessions';
                $lifetime = $config['lifetime'] ?? 86400;
                return new DatabaseSessionHandler($pdo, $table, $lifetime);
                
            case 'redis':
                $redis = $config['redis'] ?? throw new InvalidArgumentException('Redis instance required');
                $prefix = $config['prefix'] ?? 'session:';
                $lifetime = $config['lifetime'] ?? 86400;
                return new RedisSessionHandler($redis, $prefix, $lifetime);
                
            case 'memcached':
                $memcached = $config['memcached'] ?? throw new InvalidArgumentException('Memcached instance required');
                $prefix = $config['prefix'] ?? 'session:';
                $lifetime = $config['lifetime'] ?? 86400;
                return new MemcachedSessionHandler($memcached, $prefix, $lifetime);
                
            default:
                throw new InvalidArgumentException("Unsupported session handler type: $type");
        }
    }
}

// Usage examples

// Database session handler
$pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'password');
$databaseHandler = SessionHandlerFactory::create('database', ['pdo' => $pdo]);
session_set_save_handler($databaseHandler, true);
session_start();

// Redis session handler
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redisHandler = SessionHandlerFactory::create('redis', ['redis' => $redis]);
session_set_save_handler($redisHandler, true);
session_start();

// Memcached session handler
$memcached = new Memcached();
$memcached->addServer('127.0.0.1', 11211);
$memcachedHandler = SessionHandlerFactory::create('memcached', ['memcached' => $memcached]);
session_set_save_handler($memcachedHandler, true);
session_start();
?>
```

## Form Handling

### Form Processing and Validation
```php
<?php
class FormHandler {
    private array $fields;
    private array $errors = [];
    private array $data = [];
    private string $method;
    private string $action;
    
    public function __construct(string $method = 'POST', string $action = '') {
        $this->method = strtoupper($method);
        $this->action = $action;
    }
    
    public function addField(string $name, array $rules = []): self {
        $this->fields[$name] = array_merge([
            'required' => false,
            'type' => 'string',
            'min_length' => 0,
            'max_length' => 255,
            'pattern' => null,
            'sanitize' => true,
            'label' => ucfirst($name),
            'error_message' => null
        ], $rules);
        
        return $this;
    }
    
    public function validate(array $data = null): bool {
        $this->data = $data ?? $this->getRequestData();
        $this->errors = [];
        
        foreach ($this->fields as $name => $rules) {
            $value = $this->data[$name] ?? null;
            
            // Check if required
            if ($rules['required'] && ($value === null || $value === '')) {
                $this->errors[$name] = $rules['error_message'] ?? "{$rules['label']} is required";
                continue;
            }
            
            // Skip validation if field is optional and empty
            if (!$rules['required'] && ($value === null || $value === '')) {
                continue;
            }
            
            // Validate based on type
            $this->validateField($name, $value, $rules);
        }
        
        return empty($this->errors);
    }
    
    private function validateField(string $name, $value, array $rules): void {
        // Type validation
        switch ($rules['type']) {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$name] = $rules['error_message'] ?? "Invalid email format";
                }
                break;
                
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->errors[$name] = $rules['error_message'] ?? "Invalid URL format";
                }
                break;
                
            case 'int':
                if (!filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->errors[$name] = $rules['error_message'] ?? "Invalid integer";
                }
                break;
                
            case 'float':
                if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                    $this->errors[$name] = $rules['error_message'] ?? "Invalid number";
                }
                break;
                
            case 'boolean':
                if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
                    $this->errors[$name] = $rules['error_message'] ?? "Invalid boolean value";
                }
                break;
                
            case 'date':
                if (!DateTime::createFromFormat('Y-m-d', $value)) {
                    $this->errors[$name] = $rules['error_message'] ?? "Invalid date format (YYYY-MM-DD)";
                }
                break;
                
            case 'string':
            default:
                // Length validation
                if (strlen($value) < $rules['min_length']) {
                    $this->errors[$name] = $rules['error_message'] ?? "Minimum length is {$rules['min_length']} characters";
                }
                
                if (strlen($value) > $rules['max_length']) {
                    $this->errors[$name] = $rules['error_message'] ?? "Maximum length is {$rules['max_length']} characters";
                }
                
                // Pattern validation
                if ($rules['pattern'] && !preg_match($rules['pattern'], $value)) {
                    $this->errors[$name] = $rules['error_message'] ?? "Invalid format";
                }
                break;
        }
    }
    
    public function sanitize(): array {
        $sanitized = [];
        
        foreach ($this->data as $key => $value) {
            $rules = $this->fields[$key] ?? [];
            
            if ($rules['sanitize'] ?? true) {
                $sanitized[$key] = $this->sanitizeValue($value, $rules['type'] ?? 'string');
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    private function sanitizeValue($value, string $type) {
        switch ($type) {
            case 'string':
                return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
                
            case 'email':
                return filter_var($value, FILTER_SANITIZE_EMAIL);
                
            case 'url':
                return filter_var($value, FILTER_SANITIZE_URL);
                
            case 'int':
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                
            case 'float':
                return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                
            default:
                return $value;
        }
    }
    
    private function getRequestData(): array {
        switch ($this->method) {
            case 'GET':
                return $_GET;
                
            case 'POST':
                return $_POST;
                
            default:
                // For PUT, PATCH, DELETE
                $input = file_get_contents('php://input');
                $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
                
                if (strpos($contentType, 'application/json') !== false) {
                    return json_decode($input, true) ?? [];
                } else {
                    parse_str($input, $data);
                    return $data;
                }
        }
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function getError(string $field): ?string {
        return $this->errors[$field] ?? null;
    }
    
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
    
    public function getData(): array {
        return $this->data;
    }
    
    public function getValue(string $field, $default = null) {
        return $this->data[$field] ?? $default;
    }
    
    public function renderForm(array $attributes = []): string {
        $html = '<form method="' . $this->method . '"';
        
        if ($this->action) {
            $html .= ' action="' . htmlspecialchars($this->action) . '"';
        }
        
        foreach ($attributes as $attr => $value) {
            $html .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
        }
        
        $html .= '>';
        
        // Add CSRF token for POST forms
        if ($this->method === 'POST') {
            $html .= '<input type="hidden" name="csrf_token" value="' . $this->generateCsrfToken() . '">';
        }
        
        // Render fields
        foreach ($this->fields as $name => $rules) {
            $html .= $this->renderField($name, $rules);
        }
        
        $html .= '<button type="submit">Submit</button>';
        $html .= '</form>';
        
        return $html;
    }
    
    private function renderField(string $name, array $rules): string {
        $fieldHtml = '<div class="form-group">';
        $fieldHtml .= '<label for="' . $name . '">' . $rules['label'] . '</label>';
        
        // Add error message
        if (isset($this->errors[$name])) {
            $fieldHtml .= '<div class="error">' . htmlspecialchars($this->errors[$name]) . '</div>';
        }
        
        // Render input based on type
        $fieldHtml .= $this->renderInput($name, $rules);
        
        $fieldHtml .= '</div>';
        
        return $fieldHtml;
    }
    
    private function renderInput(string $name, array $rules): string {
        $value = $this->data[$name] ?? '';
        $attributes = [
            'name' => $name,
            'id' => $name,
            'value' => $value,
            'required' => $rules['required'] ? 'required' : ''
        ];
        
        switch ($rules['type']) {
            case 'textarea':
                return '<textarea ' . $this->buildAttributes($attributes) . '>' . htmlspecialchars($value) . '</textarea>';
                
            case 'select':
                $options = $rules['options'] ?? [];
                $html = '<select ' . $this->buildAttributes($attributes) . '>';
                
                foreach ($options as $optionValue => $optionLabel) {
                    $selected = $optionValue == $value ? 'selected' : '';
                    $html .= '<option value="' . htmlspecialchars($optionValue) . '" ' . $selected . '>' . htmlspecialchars($optionLabel) . '</option>';
                }
                
                $html .= '</select>';
                return $html;
                
            case 'checkbox':
                $checked = $value ? 'checked' : '';
                return '<input type="checkbox" ' . $this->buildAttributes($attributes) . ' ' . $checked . '>';
                
            case 'radio':
                $html = '';
                $options = $rules['options'] ?? [];
                
                foreach ($options as $optionValue => $optionLabel) {
                    $checked = $optionValue == $value ? 'checked' : '';
                    $html .= '<input type="radio" name="' . $name . '" value="' . htmlspecialchars($optionValue) . '" ' . $checked . '> ' . htmlspecialchars($optionLabel);
                }
                
                return $html;
                
            case 'file':
                return '<input type="file" ' . $this->buildAttributes(['name' => $name, 'id' => $name]) . '>';
                
            case 'password':
                return '<input type="password" ' . $this->buildAttributes($attributes) . '>';
                
            case 'hidden':
                return '<input type="hidden" ' . $this->buildAttributes($attributes) . '>';
                
            case 'date':
                return '<input type="date" ' . $this->buildAttributes($attributes) . '>';
                
            case 'email':
                return '<input type="email" ' . $this->buildAttributes($attributes) . '>';
                
            case 'url':
                return '<input type="url" ' . $this->buildAttributes($attributes) . '>';
                
            case 'number':
                return '<input type="number" ' . $this->buildAttributes($attributes) . '>';
                
            default:
                return '<input type="text" ' . $this->buildAttributes($attributes) . '>';
        }
    }
    
    private function buildAttributes(array $attributes): string {
        $html = '';
        
        foreach ($attributes as $name => $value) {
            if ($value !== null && $value !== '') {
                $html .= $name . '="' . htmlspecialchars($value) . '" ';
            }
        }
        
        return $html;
    }
    
    private function generateCsrfToken(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    public function validateCsrfToken(): bool {
        if ($this->method !== 'POST') {
            return true;
        }
        
        $token = $this->data['csrf_token'] ?? '';
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}

// Usage example
$form = new FormHandler('POST', '/submit');

$form->addField('name', [
    'required' => true,
    'min_length' => 2,
    'max_length' => 50,
    'pattern' => '/^[a-zA-Z\s]+$/',
    'label' => 'Full Name'
]);

$form->addField('email', [
    'required' => true,
    'type' => 'email',
    'label' => 'Email Address'
]);

$form->addField('age', [
    'type' => 'int',
    'min_length' => 18,
    'max_length' => 120,
    'label' => 'Age'
]);

$form->addField('password', [
    'required' => true,
    'min_length' => 8,
    'max_length' => 255,
    'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
    'type' => 'password',
    'label' => 'Password'
]);

$form->addField('bio', [
    'max_length' => 500,
    'type' => 'textarea',
    'label' => 'Biography'
]);

$form->addField('country', [
    'required' => true,
    'type' => 'select',
    'options' => [
        'us' => 'United States',
        'uk' => 'United Kingdom',
        'ca' => 'Canada',
        'au' => 'Australia'
    ],
    'label' => 'Country'
]);

$form->addField('newsletter', [
    'type' => 'checkbox',
    'label' => 'Subscribe to newsletter'
]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($form->validate() && $form->validateCsrfToken()) {
        $sanitizedData = $form->sanitize();
        
        // Process data
        echo "Form submitted successfully!";
        echo "<pre>" . print_r($sanitizedData, true) . "</pre>";
    } else {
        echo "Form has errors:";
        echo "<pre>" . print_r($form->getErrors(), true) . "</pre>";
    }
}

// Render form
echo $form->renderForm(['class' => 'user-form']);
?>
```

### File Upload Handling
```php
<?php
class FileUploadHandler {
    private array $config;
    private array $errors = [];
    private array $files = [];
    
    public function __construct(array $config = []) {
        $this->config = array_merge([
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
            'upload_dir' => 'uploads/',
            'random_filename' => true,
            'overwrite' => false
        ], $config);
        
        $this->ensureUploadDir();
    }
    
    private function ensureUploadDir(): void {
        if (!is_dir($this->config['upload_dir'])) {
            mkdir($this->config['upload_dir'], 0755, true);
        }
    }
    
    public function upload(string $fieldName): bool {
        if (!isset($_FILES[$fieldName])) {
            $this->errors[$fieldName] = 'No file uploaded';
            return false;
        }
        
        $file = $_FILES[$fieldName];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[$fieldName] = $this->getUploadErrorMessage($file['error']);
            return false;
        }
        
        // Validate file
        if (!$this->validateFile($file)) {
            return false;
        }
        
        // Process upload
        $result = $this->processUpload($file);
        
        if ($result) {
            $this->files[$fieldName] = $result;
        }
        
        return $result;
    }
    
    public function uploadMultiple(string $fieldName): array {
        if (!isset($_FILES[$fieldName])) {
            $this->errors[$fieldName] = 'No files uploaded';
            return [];
        }
        
        $files = $_FILES[$fieldName];
        $uploadedFiles = [];
        
        // Handle multiple files
        if (is_array($files['name'])) {
            $fileCount = count($files['name']);
            
            for ($i = 0; $i < $fileCount; $i++) {
                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                if ($file['error'] === UPLOAD_ERR_OK && $this->validateFile($file)) {
                    $result = $this->processUpload($file);
                    if ($result) {
                        $uploadedFiles[] = $result;
                    }
                }
            }
        } else {
            // Single file upload
            if ($this->upload($fieldName)) {
                $uploadedFiles[] = $this->files[$fieldName];
            }
        }
        
        return $uploadedFiles;
    }
    
    private function validateFile(array $file): bool {
        $fieldName = $file['name'] ?? 'file';
        
        // Check file size
        if ($file['size'] > $this->config['max_size']) {
            $this->errors[$fieldName] = 'File size exceeds maximum limit';
            return false;
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->config['allowed_types'])) {
            $this->errors[$fieldName] = 'File type not allowed';
            return false;
        }
        
        // Check MIME type
        $allowedMimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedMimeTypes)) {
            $this->errors[$fieldName] = 'File MIME type not allowed';
            return false;
        }
        
        // Additional validation for images
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (!$this->validateImage($file['tmp_name'])) {
                $this->errors[$fieldName] = 'Invalid image file';
                return false;
            }
        }
        
        return true;
    }
    
    private function validateImage(string $tmpName): bool {
        $imageInfo = getimagesize($tmpName);
        return $imageInfo !== false;
    }
    
    private function processUpload(array $file): ?array {
        $originalName = $file['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Generate filename
        if ($this->config['random_filename']) {
            $filename = uniqid() . '_' . time() . '.' . $extension;
        } else {
            $filename = $originalName;
        }
        
        $uploadPath = $this->config['upload_dir'] . $filename;
        
        // Handle file overwrite
        if (file_exists($uploadPath) && !$this->config['overwrite']) {
            $filename = $this->generateUniqueFilename($filename, $extension);
            $uploadPath = $this->config['upload_dir'] . $filename;
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Set proper permissions
            chmod($uploadPath, 0644);
            
            return [
                'original_name' => $originalName,
                'filename' => $filename,
                'path' => $uploadPath,
                'size' => $file['size'],
                'type' => $file['type'],
                'extension' => $extension
            ];
        }
        
        return null;
    }
    
    private function generateUniqueFilename(string $filename, string $extension): string {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $counter = 1;
        
        do {
            $newFilename = $baseName . '_' . $counter . '.' . $extension;
            $counter++;
        } while (file_exists($this->config['upload_dir'] . $newFilename));
        
        return $newFilename;
    }
    
    private function getUploadErrorMessage(int $errorCode): string {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        return $errors[$errorCode] ?? 'Unknown upload error';
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function getFiles(): array {
        return $this->files;
    }
    
    public function getFile(string $fieldName): ?array {
        return $this->files[$fieldName] ?? null;
    }
    
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
    
    public function deleteFile(string $filename): bool {
        $filepath = $this->config['upload_dir'] . $filename;
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
    
    public function getFileInfo(string $filename): ?array {
        $filepath = $this->config['upload_dir'] . $filename;
        
        if (!file_exists($filepath)) {
            return null;
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filepath);
        
        return [
            'filename' => $filename,
            'path' => $filepath,
            'size' => filesize($filepath),
            'mime_type' => $mimeType,
            'extension' => strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
            'modified_time' => filemtime($filepath)
        ];
    }
}

class ImageProcessor {
    public static function resizeImage(string $sourcePath, string $destinationPath, int $maxWidth, int $maxHeight): bool {
        $imageInfo = getimagesize($sourcePath);
        
        if (!$imageInfo) {
            return false;
        }
        
        [$width, $height, $type] = $imageInfo;
        
        // Calculate new dimensions
        $ratio = $width / $height;
        
        if ($maxWidth / $maxHeight > $ratio) {
            $newWidth = $maxHeight * $ratio;
            $newHeight = $maxHeight;
        } else {
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $ratio;
        }
        
        // Create image resource
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }
        
        // Create new image
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        
        // Resize image
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save image
        $result = false;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($destination, $destinationPath, 90);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($destination, $destinationPath, 9);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($destination, $destinationPath);
                break;
        }
        
        // Free memory
        imagedestroy($source);
        imagedestroy($destination);
        
        return $result;
    }
    
    public static function createThumbnail(string $sourcePath, string $thumbnailPath, int $size = 150): bool {
        return self::resizeImage($sourcePath, $thumbnailPath, $size, $size);
    }
    
    public static function addWatermark(string $sourcePath, string $watermarkPath, string $destinationPath): bool {
        $source = imagecreatefromjpeg($sourcePath);
        $watermark = imagecreatefrompng($watermarkPath);
        
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $watermarkWidth = imagesx($watermark);
        $watermarkHeight = imagesy($watermark);
        
        // Position watermark at bottom-right corner
        $x = $sourceWidth - $watermarkWidth - 10;
        $y = $sourceHeight - $watermarkHeight - 10;
        
        // Merge watermark
        imagecopy($source, $watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight);
        
        $result = imagejpeg($source, $destinationPath, 90);
        
        imagedestroy($source);
        imagedestroy($watermark);
        
        return $result;
    }
}

// Usage examples

// Single file upload
$uploadHandler = new FileUploadHandler([
    'max_size' => 10 * 1024 * 1024, // 10MB
    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
    'upload_dir' => 'uploads/images/',
    'random_filename' => true
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($uploadHandler->upload('profile_image')) {
        $fileInfo = $uploadHandler->getFile('profile_image');
        echo "File uploaded successfully: " . $fileInfo['filename'];
        
        // Create thumbnail
        $thumbnailPath = 'uploads/thumbnails/thumb_' . $fileInfo['filename'];
        if (ImageProcessor::createThumbnail($fileInfo['path'], $thumbnailPath, 150)) {
            echo "Thumbnail created successfully";
        }
    } else {
        echo "Upload failed: " . implode(', ', $uploadHandler->getErrors());
    }
}

// Multiple files upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadedFiles = $uploadHandler->uploadMultiple('documents');
    
    if (!empty($uploadedFiles)) {
        echo "Uploaded " . count($uploadedFiles) . " files successfully";
        foreach ($uploadedFiles as $file) {
            echo "- " . $file['filename'] . "\n";
        }
    } else {
        echo "No files uploaded or upload failed";
    }
}

// File upload form
echo '<form method="POST" enctype="multipart/form-data">';
echo '<input type="file" name="profile_image" accept="image/*" required>';
echo '<input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx">';
echo '<button type="submit">Upload</button>';
echo '</form>';
?>
```

## Security

### Input Validation and Sanitization
```php
<?php
class SecurityValidator {
    private array $config;
    private array $errors = [];
    
    public function __construct(array $config = []) {
        $this->config = array_merge([
            'max_input_length' => 10000,
            'allow_html' => false,
            'strip_tags' => true,
            'encode_special_chars' => true
        ], $config);
    }
    
    public function validateInput(string $input, array $rules = []): array {
        $result = [
            'valid' => true,
            'sanitized' => $input,
            'errors' => []
        ];
        
        // Check input length
        if (strlen($input) > $this->config['max_input_length']) {
            $result['valid'] = false;
            $result['errors'][] = 'Input too long';
        }
        
        // Apply validation rules
        foreach ($rules as $rule => $params) {
            switch ($rule) {
                case 'required':
                    if (empty(trim($input))) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Field is required';
                    }
                    break;
                    
                case 'min_length':
                    if (strlen($input) < $params) {
                        $result['valid'] = false;
                        $result['errors'][] = "Minimum length is {$params} characters";
                    }
                    break;
                    
                case 'max_length':
                    if (strlen($input) > $params) {
                        $result['valid'] = false;
                        $result['errors'][] = "Maximum length is {$params} characters";
                    }
                    break;
                    
                case 'pattern':
                    if (!preg_match($params, $input)) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Invalid format';
                    }
                    break;
                    
                case 'email':
                    if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Invalid email format';
                    }
                    break;
                    
                case 'url':
                    if (!filter_var($input, FILTER_VALIDATE_URL)) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Invalid URL format';
                    }
                    break;
                    
                case 'numeric':
                    if (!is_numeric($input)) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Must be numeric';
                    }
                    break;
                    
                case 'alpha':
                    if (!ctype_alpha($input)) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Must contain only letters';
                    }
                    break;
                    
                case 'alphanumeric':
                    if (!ctype_alnum($input)) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Must contain only letters and numbers';
                    }
                    break;
            }
        }
        
        // Sanitize input
        $result['sanitized'] = $this->sanitizeInput($input);
        
        return $result;
    }
    
    public function sanitizeInput(string $input): string {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Remove control characters except newlines and tabs
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        // Strip tags if not allowed
        if (!$this->config['allow_html'] && $this->config['strip_tags']) {
            $input = strip_tags($input);
        }
        
        // Encode special characters
        if ($this->config['encode_special_chars']) {
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        // Trim whitespace
        $input = trim($input);
        
        return $input;
    }
    
    public function validateArray(array $data, array $schema): array {
        $result = [
            'valid' => true,
            'sanitized' => [],
            'errors' => []
        ];
        
        foreach ($schema as $field => $rules) {
            $value = $data[$field] ?? null;
            
            if ($value !== null) {
                $validation = $this->validateInput($value, $rules);
                
                if (!$validation['valid']) {
                    $result['valid'] = false;
                    $result['errors'][$field] = $validation['errors'];
                }
                
                $result['sanitized'][$field] = $validation['sanitized'];
            } elseif (in_array('required', $rules)) {
                $result['valid'] = false;
                $result['errors'][$field] = ['Field is required'];
            }
        }
        
        return $result;
    }
    
    public function validatePassword(string $password): array {
        $result = [
            'valid' => true,
            'score' => 0,
            'errors' => []
        ];
        
        // Length check
        if (strlen($password) < 8) {
            $result['valid'] = false;
            $result['errors'][] = 'Password must be at least 8 characters long';
        } else {
            $result['score'] += 1;
        }
        
        // Uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'Password must contain at least one uppercase letter';
        } else {
            $result['score'] += 1;
        }
        
        // Lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'Password must contain at least one lowercase letter';
        } else {
            $result['score'] += 1;
        }
        
        // Number
        if (!preg_match('/\d/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'Password must contain at least one number';
        } else {
            $result['score'] += 1;
        }
        
        // Special character
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'Password must contain at least one special character';
        } else {
            $result['score'] += 1;
        }
        
        // Common patterns
        if (preg_match('/^(.)\1+$/', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'Password cannot contain repeated characters';
        }
        
        if (preg_match('/^(123|password|qwerty|admin)/i', $password)) {
            $result['valid'] = false;
            $result['errors'][] = 'Password is too common';
        }
        
        return $result;
    }
    
    public function checkXSS(string $input): bool {
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
            '/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi',
            '/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/onfocus\s*=/i',
            '/onblur\s*=/i',
            '/onchange\s*=/i',
            '/onsubmit\s*=/i'
        ];
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function checkSQLInjection(string $input): bool {
        $sqlPatterns = [
            '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|SCRIPT)\b)/i',
            '/(\'|\")(.*)(\1|\1)/i',
            '/(OR|AND)\s+\d+\s*=\s*\d+/i',
            '/(OR|AND)\s+\w+\s*=\s*\w+/i',
            '/\s*--/i',
            '/\/\*.*\*\//i'
        ];
        
        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function generateSecureToken(int $length = 32): string {
        return bin2hex(random_bytes($length));
    }
    
    public function verifyToken(string $token, string $storedToken): bool {
        return hash_equals($storedToken, $token);
    }
    
    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 17, // 128MB
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    public function encryptData(string $data, string $key): string {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    public function decryptData(string $encryptedData, string $key): string {
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
    
    public function rateLimitCheck(string $identifier, int $maxRequests = 5, int $timeWindow = 300): bool {
        $cacheKey = "rate_limit_{$identifier}";
        
        if (!isset($_SESSION[$cacheKey])) {
            $_SESSION[$cacheKey] = ['requests' => 0, 'first_request' => time()];
        }
        
        $rateData = $_SESSION[$cacheKey];
        
        // Reset if time window has passed
        if (time() - $rateData['first_request'] > $timeWindow) {
            $rateData = ['requests' => 0, 'first_request' => time()];
        }
        
        if ($rateData['requests'] >= $maxRequests) {
            return false;
        }
        
        $rateData['requests']++;
        $_SESSION[$cacheKey] = $rateData;
        
        return true;
    }
    
    public function sanitizeFilename(string $filename): string {
        // Remove directory traversal attempts
        $filename = preg_replace('/[\/\\\\]/', '', $filename);
        
        // Remove potentially dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'file_' . time();
        }
        
        return $filename;
    }
    
    public function validateFileUpload(array $file, array $allowedTypes = [], int $maxSize = 5242880): array {
        $result = [
            'valid' => true,
            'errors' => []
        ];
        
        // Check if file was uploaded
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['valid'] = false;
            $result['errors'][] = 'File upload error: ' . $file['error'];
            return $result;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $result['valid'] = false;
            $result['errors'][] = 'File size exceeds maximum limit';
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedTypes)) {
                $result['valid'] = false;
                $result['errors'][] = 'File type not allowed';
            }
        }
        
        // Check MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        $allowedMimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf'
        ];
        
        if (!in_array($mimeType, $allowedMimeTypes)) {
            $result['valid'] = false;
            $result['errors'][] = 'File MIME type not allowed';
        }
        
        return $result;
    }
}

// Usage examples
$validator = new SecurityValidator();

// Validate single input
$emailValidation = $validator->validateInput($_POST['email'], [
    'required' => true,
    'email' => true,
    'max_length' => 255
]);

if (!$emailValidation['valid']) {
    echo "Email validation failed: " . implode(', ', $emailValidation['errors']);
}

// Validate array of data
$schema = [
    'name' => ['required' => true, 'min_length' => 2, 'max_length' => 50],
    'email' => ['required' => true, 'email' => true],
    'age' => ['numeric' => true, 'min_length' => 18, 'max_length' => 120],
    'message' => ['max_length' => 1000]
];

$validationResult = $validator->validateArray($_POST, $schema);

if (!$validationResult['valid']) {
    echo "Validation failed: ";
    foreach ($validationResult['errors'] as $field => $errors) {
        echo "$field: " . implode(', ', $errors) . "\n";
    }
}

// Password validation
$passwordValidation = $validator->validatePassword($_POST['password']);

if (!$passwordValidation['valid']) {
    echo "Password validation failed: " . implode(', ', $passwordValidation['errors']);
} else {
    echo "Password strength score: " . $passwordValidation['score'] . "/5";
}

// XSS check
if ($validator->checkXSS($_POST['comment'])) {
    echo "Potential XSS attack detected!";
}

// SQL injection check
if ($validator->checkSQLInjection($_POST['search'])) {
    echo "Potential SQL injection detected!";
}

// Rate limiting
if (!$validator->rateLimitCheck($_SERVER['REMOTE_ADDR'], 5, 300)) {
    echo "Rate limit exceeded!";
}

// File upload validation
$fileValidation = $validator->validateFileUpload($_FILES['document'], ['pdf', 'doc', 'docx'], 10 * 1024 * 1024);

if (!$fileValidation['valid']) {
    echo "File validation failed: " . implode(', ', $fileValidation['errors']);
}
?>
```

### CSRF Protection
```php
<?php
class CsrfProtection {
    private string $tokenName = 'csrf_token';
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
        
        $storedData = $_SESSION[$this->tokenName];
        
        // Check if token matches
        if (!hash_equals($storedData['token'], $token)) {
            return false;
        }
        
        // Check if token has expired
        if (time() - $storedData['timestamp'] > $this->tokenExpiration) {
            return false;
        }
        
        return true;
    }
    
    public function getToken(): string {
        if (!isset($_SESSION[$this->tokenName])) {
            return $this->generateToken();
        }
        
        return $_SESSION[$this->tokenName]['token'];
    }
    
    public function getHiddenField(): string {
        $token = $this->getToken();
        return '<input type="hidden" name="' . $this->tokenName . '" value="' . htmlspecialchars($token) . '">';
    }
    
    public function getMetaTag(): string {
        $token = $this->getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
    
    public function getHeader(): string {
        $token = $this->getToken();
        return 'X-CSRF-Token: ' . $token;
    }
    
    public function clearToken(): void {
        unset($_SESSION[$this->tokenName]);
    }
    
    public function validateRequest(): bool {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'GET' || $method === 'HEAD' || $method === 'OPTIONS') {
            return true; // CSRF protection not needed for safe methods
        }
        
        $token = $_POST[$this->tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        return $this->validateToken($token);
    }
    
    public function regenerateToken(): void {
        $this->clearToken();
        $this->generateToken();
    }
    
    public function isExpired(): bool {
        if (!isset($_SESSION[$this->tokenName])) {
            return true;
        }
        
        return time() - $_SESSION[$this->tokenName]['timestamp'] > $this->tokenExpiration;
    }
}

// CSRF Middleware
class CsrfMiddleware {
    private CsrfProtection $csrf;
    private array $excludedRoutes;
    
    public function __construct(CsrfProtection $csrf, array $excludedRoutes = []) {
        $this->csrf = $csrf;
        $this->excludedRoutes = $excludedRoutes;
    }
    
    public function handle(): void {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Skip CSRF check for excluded routes
        foreach ($this->excludedRoutes as $route) {
            if (strpos($uri, $route) === 0) {
                return;
            }
        }
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Only validate for state-changing methods
        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            if (!$this->csrf->validateRequest()) {
                http_response_code(419);
                echo '<h1>CSRF Token Validation Failed</h1>';
                echo '<p>Your session has expired or the CSRF token is invalid.</p>';
                echo '<p>Please refresh the page and try again.</p>';
                exit;
            }
        }
        
        // Regenerate token if expired
        if ($this->csrf->isExpired()) {
            $this->csrf->regenerateToken();
        }
    }
}

// Usage examples
$csrf = new CsrfProtection();

// Generate token for forms
$token = $csrf->generateToken();
echo "CSRF Token: $token";

// Get hidden field for forms
echo $csrf->getHiddenField();

// Get meta tag for AJAX requests
echo $csrf->getMetaTag();

// Get header for API requests
echo $csrf->getHeader();

// Validate token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($csrf->validateRequest()) {
        echo "CSRF validation passed";
    } else {
        echo "CSRF validation failed";
    }
}

// CSRF Middleware
$middleware = new CsrfMiddleware($csrf, ['/api/', '/webhook/']);
$middleware->handle();
?>
```

## Best Practices

### Security Best Practices
```php
<?php
class SecurityBestPractices {
    private PDO $pdo;
    private array $config;
    
    public function __construct(PDO $pdo, array $config = []) {
        $this->pdo = $pdo;
        $this->config = array_merge([
            'max_login_attempts' => 5,
            'lockout_duration' => 900, // 15 minutes
            'session_timeout' => 3600, // 1 hour
            'password_min_length' => 8,
            'require_https' => true
        ], $config);
    }
    
    // Secure password hashing
    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 17, // 128MB
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    // Secure password verification
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    // Rate limiting for login attempts
    public function checkLoginRateLimit(string $email): array {
        $cacheKey = "login_attempts_{$email}";
        
        if (!isset($_SESSION[$cacheKey])) {
            $_SESSION[$cacheKey] = [
                'attempts' => 0,
                'first_attempt' => time(),
                'locked_until' => null
            ];
        }
        
        $data = $_SESSION[$cacheKey];
        
        // Check if account is locked
        if ($data['locked_until'] && time() < $data['locked_until']) {
            return [
                'allowed' => false,
                'remaining_time' => $data['locked_until'] - time(),
                'attempts_remaining' => 0
            ];
        }
        
        // Reset if lockout period has passed
        if ($data['locked_until'] && time() >= $data['locked_until']) {
            $data['attempts'] = 0;
            $data['first_attempt'] = time();
            $data['locked_until'] = null;
        }
        
        // Check if max attempts reached
        if ($data['attempts'] >= $this->config['max_login_attempts']) {
            $data['locked_until'] = time() + $this->config['lockout_duration'];
            $_SESSION[$cacheKey] = $data;
            
            return [
                'allowed' => false,
                'remaining_time' => $this->config['lockout_duration'],
                'attempts_remaining' => 0
            ];
        }
        
        $data['attempts']++;
        $_SESSION[$cacheKey] = $data;
        
        return [
            'allowed' => true,
            'remaining_time' => 0,
            'attempts_remaining' => $this->config['max_login_attempts'] - $data['attempts']
        ];
    }
    
    // Secure session management
    public function secureSession(): void {
        // Use secure session settings
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_samesite', 'Strict');
        
        // Regenerate session ID on login
        session_regenerate_id(true);
        
        // Set session timeout
        $_SESSION['last_activity'] = time();
        $_SESSION['timeout'] = $this->config['session_timeout'];
    }
    
    // Check session timeout
    public function checkSessionTimeout(): bool {
        if (!isset($_SESSION['last_activity'])) {
            return false;
        }
        
        $inactiveTime = time() - $_SESSION['last_activity'];
        
        if ($inactiveTime > $_SESSION['timeout']) {
            session_destroy();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    // Secure input validation
    public function validateInput(array $data, array $rules): array {
        $result = [
            'valid' => true,
            'sanitized' => [],
            'errors' => []
        ];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            if ($value !== null) {
                // Sanitize input
                $sanitized = $this->sanitizeInput($value);
                
                // Validate based on rules
                $validation = $this->validateField($sanitized, $fieldRules);
                
                if (!$validation['valid']) {
                    $result['valid'] = false;
                    $result['errors'][$field] = $validation['errors'];
                }
                
                $result['sanitized'][$field] = $sanitized;
            } elseif (in_array('required', $fieldRules)) {
                $result['valid'] = false;
                $result['errors'][$field] = ['Field is required'];
            }
        }
        
        return $result;
    }
    
    private function sanitizeInput(string $input): string {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Remove control characters
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        // Escape HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Trim whitespace
        return trim($input);
    }
    
    private function validateField(string $input, array $rules): array {
        $result = ['valid' => true, 'errors' => []];
        
        foreach ($rules as $rule => $param) {
            switch ($rule) {
                case 'required':
                    if (empty($input)) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Field is required';
                    }
                    break;
                    
                case 'min_length':
                    if (strlen($input) < $param) {
                        $result['valid'] = false;
                        $result['errors'][] = "Minimum length is {$param}";
                    }
                    break;
                    
                case 'max_length':
                    if (strlen($input) > $param) {
                        $result['valid'] = false;
                        $result['errors'][] = "Maximum length is {$param}";
                    }
                    break;
                    
                case 'email':
                    if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Invalid email format';
                    }
                    break;
                    
                case 'pattern':
                    if (!preg_match($param, $input)) {
                        $result['valid'] = false;
                        $result['errors'][] = 'Invalid format';
                    }
                    break;
            }
        }
        
        return $result;
    }
    
    // SQL injection prevention
    public function secureQuery(string $sql, array $params = []): array {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new RuntimeException("Database operation failed");
        }
    }
    
    // XSS prevention
    public function preventXSS(string $input): string {
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    // HTTPS enforcement
    public function enforceHttps(): void {
        if ($this->config['require_https'] && !isset($_SERVER['HTTPS'])) {
            $httpsUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location: $httpsUrl");
            exit;
        }
    }
    
    // Content Security Policy
    public function setCSPHeader(): void {
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
               "style-src 'self' 'unsafe-inline'; " .
               "img-src 'self' data: https:; " .
               "font-src 'self' https:; " .
               "connect-src 'self' https:; " .
               "frame-ancestors 'none'; " .
               "form-action 'self'; " .
               "base-uri 'self'; " .
               "upgrade-insecure-requests";
        
        header("Content-Security-Policy: $csp");
    }
    
    // Security headers
    public function setSecurityHeaders(): void {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Force HTTPS
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        
        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Permissions policy
        header("Permissions-Policy: geolocation 'none', microphone 'none', camera 'none'");
    }
    
    // Audit logging
    public function logSecurityEvent(string $event, array $context = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? null,
            'context' => $context
        ];
        
        error_log("SECURITY: " . json_encode($logEntry));
    }
    
    // Input sanitization for database
    public function sanitizeForDatabase(array $data): array {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeForDatabase($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    // Password strength checker
    public function checkPasswordStrength(string $password): array {
        $strength = 0;
        $feedback = [];
        
        // Length
        if (strlen($password) >= 12) {
            $strength += 2;
        } elseif (strlen($password) >= 8) {
            $strength += 1;
        } else {
            $feedback[] = 'Password should be at least 8 characters long';
        }
        
        // Complexity
        if (preg_match('/[A-Z]/', $password)) $strength++;
        else $feedback[] = 'Include uppercase letters';
        
        if (preg_match('/[a-z]/', $password)) $strength++;
        else $feedback[] = 'Include lowercase letters';
        
        if (preg_match('/\d/', $password)) $strength++;
        else $feedback[] = 'Include numbers';
        
        if (preg_match('/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/', $password)) $strength++;
        else $feedback[] = 'Include special characters';
        
        // Common patterns
        if (preg_match('/^(.)\1+$/', $password)) {
            $strength -= 2;
            $feedback[] = 'Avoid repeated characters';
        }
        
        if (preg_match('/^(123|password|qwerty|admin)/i', $password)) {
            $strength -= 2;
            $feedback[] = 'Avoid common passwords';
        }
        
        return [
            'strength' => max(0, $strength),
            'feedback' => $feedback,
            'strong' => $strength >= 4
        ];
    }
    
    // Secure file handling
    public function secureFileUpload(array $file, array $allowedTypes = [], int $maxSize = 5242880): array {
        $result = ['valid' => true, 'errors' => []];
        
        // Check upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['valid'] = false;
            $result['errors'][] = 'Upload error: ' . $file['error'];
            return $result;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $result['valid'] = false;
            $result['errors'][] = 'File too large';
        }
        
        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes) && !in_array($extension, $allowedTypes)) {
            $result['valid'] = false;
            $result['errors'][] = 'File type not allowed';
        }
        
        // Check MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        $allowedMimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf'
        ];
        
        if (!in_array($mimeType, $allowedMimeTypes)) {
            $result['valid'] = false;
            $result['errors'][] = 'File MIME type not allowed';
        }
        
        return $result;
    }
}

// Usage examples
$security = new SecurityBestPractices($pdo);

// Set security headers
$security->setSecurityHeaders();
$security->setCSPHeader();
$security->enforceHttps();

// Secure session
$security->secureSession();

// Check session timeout
if (!$security->checkSessionTimeout()) {
    header('Location: /login?expired=1');
    exit;
}

// Validate input with security rules
$rules = [
    'email' => ['required', 'email', 'max_length' => 255],
    'password' => ['required', 'min_length' => 8],
    'name' => ['required', 'min_length' => 2, 'max_length' => 50]
];

$validation = $security->validateInput($_POST, $rules);

if (!$validation['valid']) {
    echo "Validation failed: " . implode(', ', array_merge(...array_values($validation['errors'])));
}

// Check login rate limit
$rateLimit = $security->checkLoginRateLimit($_POST['email']);
if (!$rateLimit['allowed']) {
    echo "Too many login attempts. Try again in " . ceil($rateLimit['remaining_time'] / 60) . " minutes";
}

// Secure database query
$users = $security->secureQuery(
    "SELECT * FROM users WHERE email = ? AND password = ?",
    [$email, $hashedPassword]
);

// Log security event
$security->logSecurityEvent('login_attempt', [
    'email' => $email,
    'success' => !empty($users)
]);

// Check password strength
$passwordStrength = $security->checkPasswordStrength($password);
if (!$passwordStrength['strong']) {
    echo "Password is not strong enough: " . implode(', ', $passwordStrength['feedback']);
}
?>
```

## Common Pitfalls

### Web Programming Pitfalls
```php
<?php
// Pitfall: Not using HTTPS
class BadSecurity {
    public function handleLogin() {
        // No HTTPS enforcement
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        // Plain text password transmission
        $this->authenticate($username, $password);
    }
}

// Solution: Always use HTTPS
class GoodSecurity {
    public function handleLogin() {
        // Enforce HTTPS
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            $httpsUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location: $httpsUrl");
            exit;
        }
        
        $username = $this->sanitizeInput($_POST['username']);
        $password = $_POST['password']; // Will be hashed
        
        $this->authenticateSecure($username, $password);
    }
}

// Pitfall: SQL Injection vulnerability
class BadDatabase {
    public function getUser($id) {
        // VULNERABLE: Direct SQL injection
        $sql = "SELECT * FROM users WHERE id = $id";
        return $this->pdo->query($sql)->fetch();
    }
}

// Solution: Use prepared statements
class GoodDatabase {
    public function getUser($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

// Pitfall: XSS vulnerability
class BadOutput {
    public function displayUser($name) {
        // VULNERABLE: Direct output without escaping
        echo "Welcome, $name!";
    }
}

// Solution: Always escape output
class GoodOutput {
    public function displayUser($name) {
        echo "Welcome, " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "!";
    }
}

// Pitfall: Weak session management
class BadSession {
    public function login($userId) {
        // No session regeneration
        $_SESSION['user_id'] = $userId;
    }
}

// Solution: Secure session management
class GoodSession {
    public function login($userId) {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['last_activity'] = time();
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
    
    public function validateSession() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session expiration
        if (time() - $_SESSION['last_activity'] > 3600) {
            session_destroy();
            return false;
        }
        
        // Check IP and User-Agent consistency
        if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] || 
            $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            session_destroy();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
}

// Pitfall: No CSRF protection
class BadForm {
    public function renderForm() {
        echo '<form method="POST" action="/process">';
        echo '<input type="text" name="data">';
        echo '<button type="submit">Submit</button>';
        echo '</form>';
    }
}

// Solution: CSRF protection
class GoodForm {
    private $csrf;
    
    public function __construct($csrf) {
        $this->csrf = $csrf;
    }
    
    public function renderForm() {
        echo '<form method="POST" action="/process">';
        echo $this->csrf->getHiddenField();
        echo '<input type="text" name="data">';
        echo '<button type="submit">Submit</button>';
        echo '</form>';
    }
    
    public function processForm() {
        if (!$this->csrf->validateRequest()) {
            http_response_code(419);
            echo 'CSRF token validation failed';
            return;
        }
        
        // Process form data
        $data = $_POST['data'];
        $this->processData($data);
    }
}

// Pitfall: No input validation
class BadValidation {
    public function processUser($data) {
        // No validation
        $this->saveUser($data);
    }
}

// Solution: Comprehensive validation
class GoodValidation {
    public function processUser($data) {
        $rules = [
            'name' => ['required', 'min_length' => 2, 'max_length' => 50],
            'email' => ['required', 'email'],
            'age' => ['numeric', 'min_length' => 0, 'max_length' => 150]
        ];
        
        $validation = $this->validateInput($data, $rules);
        
        if (!$validation['valid']) {
            throw new InvalidArgumentException('Validation failed');
        }
        
        $this->saveUser($validation['sanitized']);
    }
}

// Pitfall: Insecure file uploads
class BadFileUpload {
    public function uploadFile($file) {
        // No validation, direct move
        move_uploaded_file($file['tmp_name'], 'uploads/' . $file['name']);
    }
}

// Solution: Secure file upload
class GoodFileUpload {
    public function uploadFile($file) {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Validate file
        $validation = $this->validateFile($file, $allowedTypes, $maxSize);
        
        if (!$validation['valid']) {
            throw new RuntimeException('File validation failed');
        }
        
        // Generate secure filename
        $filename = $this->generateSecureFilename($file['name']);
        $uploadPath = 'uploads/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $filename;
        }
        
        throw new RuntimeException('File upload failed');
    }
    
    private function validateFile($file, $allowedTypes, $maxSize) {
        // Implementation of file validation
        return ['valid' => true, 'errors' => []];
    }
    
    private function generateSecureFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '_' . time() . '.' . $extension;
    }
}

// Pitfall: No error handling
class BadErrorHandling {
    public function riskyOperation() {
        // No error handling
        $result = $this->database->query("SELECT * FROM users");
        return $result;
    }
}

// Solution: Proper error handling
class GoodErrorHandling {
    public function riskyOperation() {
        try {
            $result = $this->database->query("SELECT * FROM users");
            return $result;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new RuntimeException("Database operation failed");
        }
    }
}

// Pitfall: Hardcoded credentials
class BadCredentials {
    public function connect() {
        // Hardcoded credentials
        return new PDO('mysql:host=localhost;dbname=test', 'root', 'password');
    }
}

// Solution: Environment variables
class GoodCredentials {
    public function connect() {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $database = $_ENV['DB_NAME'] ?? 'test';
        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        
        return new PDO("mysql:host=$host;dbname=$database", $username, $password);
    }
}

// Pitfall: No rate limiting
class BadRateLimit {
    public function handleLogin() {
        // No rate limiting
        $this->login($_POST['username'], $_POST['password']);
    }
}

// Solution: Rate limiting
class GoodRateLimit {
    private $maxAttempts = 5;
    private $lockoutTime = 900; // 15 minutes
    
    public function handleLogin() {
        $ip = $_SERVER['REMOTE_ADDR'];
        
        if (!$this->checkRateLimit($ip)) {
            http_response_code(429);
            echo 'Too many login attempts. Try again later.';
            return;
        }
        
        $this->login($_POST['username'], $_POST['password']);
    }
    
    private function checkRateLimit($ip) {
        $key = "login_attempts_$ip";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['attempts' => 0, 'first_attempt' => time()];
        }
        
        $data = $_SESSION[$key];
        
        if ($data['attempts'] >= $this->maxAttempts) {
            if (time() - $data['first_attempt'] < $this->lockoutTime) {
                return false;
            }
            
            // Reset after lockout period
            $data['attempts'] = 0;
            $data['first_attempt'] = time();
        }
        
        $data['attempts']++;
        $_SESSION[$key] = $data;
        
        return true;
    }
}
?>
```

## Summary

PHP Web Programming provides:

**HTTP Fundamentals:**
- HTTP methods and status codes
- Request and response handling
- Header and cookie management
- Content negotiation

**Session Management:**
- Secure session configuration
- Custom session handlers
- Session security and validation
- Session middleware

**Form Handling:**
- Form validation and sanitization
- File upload handling
- CSRF protection
- Multi-step forms

**Security:**
- Input validation and sanitization
- XSS and SQL injection prevention
- CSRF protection
- Security headers and CSP

**Best Practices:**
- HTTPS enforcement
- Secure password handling
- Rate limiting
- Audit logging
- Error handling

**Common Pitfalls:**
- SQL injection vulnerabilities
- XSS vulnerabilities
- Weak session management
- No CSRF protection
- Insecure file uploads

PHP provides comprehensive web programming capabilities with robust security features when following established best practices and using modern security techniques.
