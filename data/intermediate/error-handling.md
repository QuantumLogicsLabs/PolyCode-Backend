# PHP Error Handling

## Error Types and Levels

### PHP Error Types
```php
<?php
// Error types in PHP
class ErrorTypes {
    // Fatal errors (E_ERROR)
    public function fatalErrorExample() {
        // This will cause a fatal error
        $result = non_existent_function(); // Fatal error: Call to undefined function
    }
    
    // Warnings (E_WARNING)
    public function warningExample() {
        // This will generate a warning
        $file = fopen('non_existent_file.txt', 'r'); // Warning: fopen failed
        return $file;
    }
    
    // Notices (E_NOTICE)
    public function noticeExample() {
        // This will generate a notice
        echo $undefined_variable; // Notice: Undefined variable
    }
    
    // Deprecated warnings (E_DEPRECATED)
    public function deprecatedExample() {
        // This will generate a deprecated warning
        $result = split(':', 'string'); // Deprecated: split() function
        return $result;
    }
    
    // Strict standards (E_STRICT)
    public function strictExample() {
        // This will generate a strict standards notice
        class ParentClass {
            public function method() {}
        }
        
        class ChildClass extends ParentClass {
            // Strict standards: Declaration should be compatible
            public function method($param = null) {}
        }
    }
    
    // User errors (E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE)
    public function userErrorExample() {
        trigger_error('This is a user error', E_USER_ERROR);
    }
    
    public function userWarningExample() {
        trigger_error('This is a user warning', E_USER_WARNING);
    }
    
    public function userNoticeExample() {
        trigger_error('This is a user notice', E_USER_NOTICE);
    }
    
    // Recoverable fatal error (E_RECOVERABLE_ERROR)
    public function recoverableErrorExample() {
        function testFunction(stdClass $obj) {
            return $obj;
        }
        
        // This will generate a recoverable fatal error
        testFunction('not an object');
    }
    
    // Parse errors (E_PARSE)
    // These are caught during compilation, not runtime
    // Example: $result = 1 + 2 +; // Parse error: syntax error
}

// Error level constants
class ErrorLevels {
    public function displayErrorLevels() {
        echo "Error Levels:\n";
        echo "E_ERROR: " . E_ERROR . "\n";
        echo "E_WARNING: " . E_WARNING . "\n";
        echo "E_PARSE: " . E_PARSE . "\n";
        echo "E_NOTICE: " . E_NOTICE . "\n";
        echo "E_CORE_ERROR: " . E_CORE_ERROR . "\n";
        echo "E_CORE_WARNING: " . E_CORE_WARNING . "\n";
        echo "E_COMPILE_ERROR: " . E_COMPILE_ERROR . "\n";
        echo "E_COMPILE_WARNING: " . E_COMPILE_WARNING . "\n";
        echo "E_USER_ERROR: " . E_USER_ERROR . "\n";
        echo "E_USER_WARNING: " . E_USER_WARNING . "\n";
        echo "E_USER_NOTICE: " . E_USER_NOTICE . "\n";
        echo "E_STRICT: " . E_STRICT . "\n";
        echo "E_RECOVERABLE_ERROR: " . E_RECOVERABLE_ERROR . "\n";
        echo "E_DEPRECATED: " . E_DEPRECATED . "\n";
        echo "E_USER_DEPRECATED: " . E_USER_DEPRECATED . "\n";
        
        // Combined error levels
        echo "E_ALL: " . E_ALL . "\n";
    }
    
    public function checkErrorReporting() {
        echo "Current error reporting level: " . error_reporting() . "\n";
        echo "Display errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "\n";
        echo "Log errors: " . (ini_get('log_errors') ? 'On' : 'Off') . "\n";
        echo "Error log file: " . ini_get('error_log') . "\n";
    }
}

// Usage
$errorTypes = new ErrorTypes();
$errorLevels = new ErrorLevels();

// Display error information
$errorLevels->displayErrorLevels();
$errorLevels->checkErrorReporting();
?>
```

## Exception Handling

### Basic Exception Handling
```php
<?php
class BasicExceptionHandler {
    public function divideNumbers($numerator, $denominator) {
        try {
            if ($denominator == 0) {
                throw new Exception("Division by zero is not allowed");
            }
            
            return $numerator / $denominator;
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    public function processFile($filename) {
        try {
            if (!file_exists($filename)) {
                throw new Exception("File not found: $filename");
            }
            
            $content = file_get_contents($filename);
            
            if (empty($content)) {
                throw new Exception("File is empty: $filename");
            }
            
            return $content;
            
        } catch (Exception $e) {
            echo "File processing error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function validateEmail($email) {
        try {
            if (empty($email)) {
                throw new Exception("Email cannot be empty");
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format: $email");
            }
            
            return true;
            
        } catch (Exception $e) {
            echo "Email validation error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function databaseOperation() {
        try {
            // Simulate database connection
            $connection = $this->connectToDatabase();
            
            // Simulate query execution
            $result = $this->executeQuery($connection, "SELECT * FROM users");
            
            return $result;
            
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage() . "\n";
            return false;
            
        } catch (Exception $e) {
            echo "General error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    private function connectToDatabase() {
        // Simulate connection failure
        throw new PDOException("Could not connect to database");
    }
    
    private function executeQuery($connection, $query) {
        // Simulate query execution
        return ['user1', 'user2', 'user3'];
    }
}

// Multiple catch blocks example
class MultipleCatchHandler {
    public function handleMultipleExceptions($input) {
        try {
            $this->processInput($input);
            
        } catch (InvalidArgumentException $e) {
            echo "Invalid argument: " . $e->getMessage() . "\n";
            
        } catch (RuntimeException $e) {
            echo "Runtime error: " . $e->getMessage() . "\n";
            
        } catch (Exception $e) {
            echo "General exception: " . $e->getMessage() . "\n";
            
        } finally {
            echo "Cleanup operations\n";
        }
    }
    
    private function processInput($input) {
        if (empty($input)) {
            throw new InvalidArgumentException("Input cannot be empty");
        }
        
        if ($input === 'error') {
            throw new RuntimeException("Simulated runtime error");
        }
        
        if ($input === 'fatal') {
            throw new Exception("Simulated fatal error");
        }
        
        echo "Input processed successfully: $input\n";
    }
}

// Exception chaining
class ExceptionChaining {
    public function processData($data) {
        try {
            $this->validateData($data);
            $this->transformData($data);
            $this->saveData($data);
            
        } catch (Exception $e) {
            throw new RuntimeException("Failed to process data", 0, $e);
        }
    }
    
    private function validateData($data) {
        if (empty($data)) {
            throw new InvalidArgumentException("Data cannot be empty");
        }
    }
    
    private function transformData($data) {
        if (!is_array($data)) {
            throw new InvalidArgumentException("Data must be an array");
        }
    }
    
    private function saveData($data) {
        throw new RuntimeException("Database save failed");
    }
}

// Usage
$basicHandler = new BasicExceptionHandler();
$multipleHandler = new MultipleCatchHandler();
$chainingHandler = new ExceptionChaining();

// Basic exception handling
$result = $basicHandler->divideNumbers(10, 0);
$content = $basicHandler->processFile('nonexistent.txt');
$isValid = $basicHandler->validateEmail('invalid-email');
$dbResult = $basicHandler->databaseOperation();

// Multiple catch blocks
$multipleHandler->handleMultipleExceptions('');
$multipleHandler->handleMultipleExceptions('error');
$multipleHandler->handleMultipleExceptions('fatal');
$multipleHandler->handleMultipleExceptions('valid');

// Exception chaining
try {
    $chainingHandler->processData([]);
} catch (Exception $e) {
    echo "Chained exception: " . $e->getMessage() . "\n";
    echo "Previous exception: " . $e->getPrevious()->getMessage() . "\n";
}
?>
```

### Custom Exceptions
```php
<?php
// Base custom exception
class BaseException extends Exception {
    protected $context;
    
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }
    
    public function getContext(): array {
        return $this->context;
    }
    
    public function getDetailedMessage(): string {
        $details = $this->getMessage();
        
        if (!empty($this->context)) {
            $details .= " Context: " . json_encode($this->context);
        }
        
        return $details;
    }
}

// Validation exception
class ValidationException extends BaseException {
    private array $errors;
    
    public function __construct(array $errors, array $context = []) {
        $this->errors = $errors;
        $message = "Validation failed: " . implode(', ', $errors);
        parent::__construct($message, 422, null, $context);
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
}

// Database exception
class DatabaseException extends BaseException {
    private string $sql;
    private array $parameters;
    
    public function __construct(string $message, string $sql = '', array $parameters = [], ?Throwable $previous = null) {
        $this->sql = $sql;
        $this->parameters = $parameters;
        
        $context = [
            'sql' => $sql,
            'parameters' => $parameters
        ];
        
        parent::__construct($message, 500, $previous, $context);
    }
    
    public function getSql(): string {
        return $this->sql;
    }
    
    public function getParameters(): array {
        return $this->parameters;
    }
}

// File system exception
class FileSystemException extends BaseException {
    private string $filepath;
    
    public function __construct(string $message, string $filepath, ?Throwable $previous = null) {
        $this->filepath = $filepath;
        
        $context = [
            'filepath' => $filepath,
            'file_exists' => file_exists($filepath),
            'is_readable' => is_readable($filepath),
            'is_writable' => is_writable($filepath)
        ];
        
        parent::__construct($message, 500, $previous, $context);
    }
    
    public function getFilepath(): string {
        return $this->filepath;
    }
}

// Authentication exception
class AuthenticationException extends BaseException {
    private string $username;
    
    public function __construct(string $message, string $username = '', ?Throwable $previous = null) {
        $this->username = $username;
        
        $context = [
            'username' => $username,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        parent::__construct($message, 401, $previous, $context);
    }
    
    public function getUsername(): string {
        return $this->username;
    }
}

// Authorization exception
class AuthorizationException extends BaseException {
    private string $resource;
    private string $action;
    private string $userId;
    
    public function __construct(string $resource, string $action, string $userId, ?Throwable $previous = null) {
        $this->resource = $resource;
        $this->action = $action;
        $this->userId = $userId;
        
        $message = "Access denied to $resource for action $action";
        
        $context = [
            'resource' => $resource,
            'action' => $action,
            'user_id' => $userId,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        parent::__construct($message, 403, $previous, $context);
    }
    
    public function getResource(): string {
        return $this->resource;
    }
    
    public function getAction(): string {
        return $this->action;
    }
    
    public function getUserId(): string {
        return $this->userId;
    }
}

// Service exception
class ServiceException extends BaseException {
    private string $serviceName;
    private array $serviceData;
    
    public function __construct(string $serviceName, string $message, array $serviceData = [], ?Throwable $previous = null) {
        $this->serviceName = $serviceName;
        $this->serviceData = $serviceData;
        
        $context = array_merge([
            'service' => $serviceName,
            'timestamp' => date('Y-m-d H:i:s')
        ], $serviceData);
        
        parent::__construct($message, 500, $previous, $context);
    }
    
    public function getServiceName(): string {
        return $this->serviceName;
    }
    
    public function getServiceData(): array {
        return $this->serviceData;
    }
}

// Exception handler using custom exceptions
class UserService {
    public function createUser(array $userData): array {
        try {
            $this->validateUserData($userData);
            $this->checkUserExists($userData['email']);
            $userId = $this->saveUser($userData);
            
            return ['id' => $userId, 'status' => 'created'];
            
        } catch (ValidationException $e) {
            throw new ServiceException('UserService', 'User creation failed due to validation errors', $userData, $e);
            
        } catch (DatabaseException $e) {
            throw new ServiceException('UserService', 'User creation failed due to database error', $userData, $e);
            
        } catch (Exception $e) {
            throw new ServiceException('UserService', 'User creation failed due to unknown error', $userData, $e);
        }
    }
    
    private function validateUserData(array $userData): void {
        $errors = [];
        
        if (empty($userData['name'])) {
            $errors[] = 'Name is required';
        }
        
        if (empty($userData['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($userData['password'])) {
            $errors[] = 'Password is required';
        } elseif (strlen($userData['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors, $userData);
        }
    }
    
    private function checkUserExists(string $email): void {
        // Simulate database check
        if ($email === 'existing@example.com') {
            throw new DatabaseException('User already exists', 'SELECT id FROM users WHERE email = ?', [$email]);
        }
    }
    
    private function saveUser(array $userData): int {
        // Simulate database save
        if (rand(1, 10) === 1) { // 10% chance of failure
            throw new DatabaseException('Failed to save user', 'INSERT INTO users SET ?', [$userData]);
        }
        
        return random_int(1, 1000);
    }
}

// Exception logging
class ExceptionLogger {
    private string $logFile;
    
    public function __construct(string $logFile = 'exceptions.log') {
        $this->logFile = $logFile;
    }
    
    public function logException(Throwable $exception): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $exception instanceof BaseException ? $exception->getContext() : []
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND);
    }
    
    public function logExceptionWithDetails(Throwable $exception, array $additionalData = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $exception instanceof BaseException ? $exception->getContext() : [],
            'additional_data' => $additionalData,
            'server_data' => [
                'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
                'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
                'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? '',
                'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND);
    }
}

// Usage examples
$logger = new ExceptionLogger();
$userService = new UserService();

try {
    $result = $userService->createUser([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123'
    ]);
    
    echo "User created successfully: " . json_encode($result) . "\n";
    
} catch (ServiceException $e) {
    $logger->logExceptionWithDetails($e, ['operation' => 'create_user']);
    echo "Service error: " . $e->getMessage() . "\n";
    
} catch (Exception $e) {
    $logger->logException($e);
    echo "General error: " . $e->getMessage() . "\n";
}

// Test validation exception
try {
    $result = $userService->createUser([
        'name' => '',
        'email' => 'invalid-email',
        'password' => '123'
    ]);
    
} catch (ServiceException $e) {
    $previous = $e->getPrevious();
    if ($previous instanceof ValidationException) {
        echo "Validation errors: " . implode(', ', $previous->getErrors()) . "\n";
    }
}
?>
```

## Error Reporting

### Error Reporting Configuration
```php
<?php
class ErrorReporting {
    private array $originalSettings;
    
    public function __construct() {
        $this->originalSettings = [
            'display_errors' => ini_get('display_errors'),
            'error_reporting' => error_reporting(),
            'log_errors' => ini_get('log_errors'),
            'error_log' => ini_get('error_log')
        ];
    }
    
    public function enableDevelopmentMode(): void {
        // Show all errors for development
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        ini_set('log_errors', '1');
        ini_set('error_log', __DIR__ . '/errors.log');
        
        echo "Development mode enabled - All errors will be displayed and logged\n";
    }
    
    public function enableProductionMode(): void {
        // Hide errors from users, log them instead
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        ini_set('error_log', __DIR__ . '/production_errors.log');
        
        echo "Production mode enabled - Errors will be logged but not displayed\n";
    }
    
    public function enableTestingMode(): void {
        // Show errors but don't log them (for testing)
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        ini_set('log_errors', '0');
        
        echo "Testing mode enabled - Errors will be displayed but not logged\n";
    }
    
    public function restoreOriginalSettings(): void {
        error_reporting($this->originalSettings['error_reporting']);
        ini_set('display_errors', $this->originalSettings['display_errors']);
        ini_set('log_errors', $this->originalSettings['log_errors']);
        ini_set('error_log', $this->originalSettings['error_log']);
        
        echo "Original error reporting settings restored\n";
    }
    
    public function getCurrentSettings(): array {
        return [
            'error_reporting' => error_reporting(),
            'display_errors' => ini_get('display_errors'),
            'log_errors' => ini_get('log_errors'),
            'error_log' => ini_get('error_log'),
            'html_errors' => ini_get('html_errors'),
            'xmlrpc_errors' => ini_get('xmlrpc_errors'),
            'ignore_repeated_errors' => ini_get('ignore_repeated_errors'),
            'ignore_repeated_source' => ini_get('ignore_repeated_source'),
            'report_memleaks' => ini_get('report_memleaks'),
            'track_errors' => ini_get('track_errors'),
            'xmlrpc_error_number' => ini_get('xmlrpc_error_number')
        ];
    }
    
    public function displaySettings(): void {
        $settings = $this->getCurrentSettings();
        
        echo "Current Error Reporting Settings:\n";
        echo "================================\n";
        
        foreach ($settings as $key => $value) {
            echo "$key: $value\n";
        }
        
        echo "\nError Reporting Level Details:\n";
        echo "E_ALL: " . E_ALL . "\n";
        echo "E_ERROR: " . E_ERROR . "\n";
        echo "E_WARNING: " . E_WARNING . "\n";
        echo "E_PARSE: " . E_PARSE . "\n";
        echo "E_NOTICE: " . E_NOTICE . "\n";
        echo "E_STRICT: " . E_STRICT . "\n";
        echo "E_DEPRECATED: " . E_DEPRECATED . "\n";
    }
    
    public function setCustomErrorLog(string $logFile): void {
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        ini_set('error_log', $logFile);
        echo "Custom error log set to: $logFile\n";
    }
    
    public function enableHtmlErrors(): void {
        ini_set('html_errors', '1');
        echo "HTML errors enabled\n";
    }
    
    public function disableHtmlErrors(): void {
        ini_set('html_errors', '0');
        echo "HTML errors disabled\n";
    }
    
    public function enableErrorTracking(): void {
        ini_set('track_errors', '1');
        echo "Error tracking enabled\n";
    }
    
    public function disableErrorTracking(): void {
        ini_set('track_errors', '0');
        echo "Error tracking disabled\n";
    }
    
    public function ignoreRepeatedErrors(): void {
        ini_set('ignore_repeated_errors', '1');
        ini_set('ignore_repeated_source', '1');
        echo "Repeated errors will be ignored\n";
    }
    
    public function showRepeatedErrors(): void {
        ini_set('ignore_repeated_errors', '0');
        ini_set('ignore_repeated_source', '0');
        echo "Repeated errors will be shown\n";
    }
    
    public function enableMemoryLeakReporting(): void {
        ini_set('report_memleaks', '1');
        echo "Memory leak reporting enabled\n";
    }
    
    public function disableMemoryLeakReporting(): void {
        ini_set('report_memleaks', '0');
        echo "Memory leak reporting disabled\n";
    }
}

// Environment-based error reporting
class EnvironmentErrorReporting {
    private string $environment;
    private ErrorReporting $reporting;
    
    public function __construct(string $environment = 'development') {
        $this->environment = $environment;
        $this->reporting = new ErrorReporting();
        $this->configureForEnvironment();
    }
    
    private function configureForEnvironment(): void {
        switch ($this->environment) {
            case 'development':
                $this->reporting->enableDevelopmentMode();
                $this->reporting->enableHtmlErrors();
                $this->reporting->enableErrorTracking();
                break;
                
            case 'testing':
                $this->reporting->enableTestingMode();
                break;
                
            case 'staging':
                error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
                ini_set('display_errors', '0');
                ini_set('log_errors', '1');
                ini_set('error_log', __DIR__ . '/staging_errors.log');
                break;
                
            case 'production':
                $this->reporting->enableProductionMode();
                $this->reporting->disableHtmlErrors();
                $this->reporting->ignoreRepeatedErrors();
                break;
                
            default:
                $this->reporting->enableDevelopmentMode();
        }
    }
    
    public function getEnvironment(): string {
        return $this->environment;
    }
    
    public function setEnvironment(string $environment): void {
        $this->environment = $environment;
        $this->configureForEnvironment();
    }
    
    public function isDevelopment(): bool {
        return $this->environment === 'development';
    }
    
    public function isProduction(): bool {
        return $this->environment === 'production';
    }
    
    public function shouldDisplayErrors(): bool {
        return in_array($this->environment, ['development', 'testing']);
    }
    
    public function shouldLogErrors(): bool {
        return in_array($this->environment, ['development', 'staging', 'production']);
    }
}

// Custom error reporter
class CustomErrorReporter {
    private string $logFile;
    private array $errorCounts = [];
    
    public function __construct(string $logFile = 'custom_errors.log') {
        $this->logFile = $logFile;
        
        // Set custom error handler
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        // Don't handle errors that are suppressed with @
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $errorType = $this->getErrorType($errno);
        $this->logError($errorType, $errstr, $errfile, $errline);
        $this->incrementErrorCount($errorType);
        
        // Don't show errors in production
        if (ini_get('display_errors')) {
            echo "<div style='color: red; border: 1px solid red; padding: 10px; margin: 10px;'>";
            echo "<strong>$errorType:</strong> $errstr in $errfile on line $errline";
            echo "</div>";
        }
        
        return true;
    }
    
    public function handleException(Throwable $exception): void {
        $this->logException($exception);
        
        if (ini_get('display_errors')) {
            echo "<div style='color: red; border: 1px solid red; padding: 10px; margin: 10px;'>";
            echo "<strong>Uncaught Exception:</strong> " . get_class($exception) . "<br>";
            echo "<strong>Message:</strong> " . $exception->getMessage() . "<br>";
            echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
            echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
            echo "<strong>Trace:</strong><br><pre>" . $exception->getTraceAsString() . "</pre>";
            echo "</div>";
        }
        
        // Stop execution on uncaught exceptions
        exit(1);
    }
    
    public function handleShutdown(): void {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->logError('FATAL ERROR', $error['message'], $error['file'], $error['line']);
            
            if (ini_get('display_errors')) {
                echo "<div style='color: red; border: 1px solid red; padding: 10px; margin: 10px;'>";
                echo "<strong>FATAL ERROR:</strong> " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'];
                echo "</div>";
            }
        }
    }
    
    private function getErrorType(int $errno): string {
        $types = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];
        
        return $types[$errno] ?? 'Unknown Error';
    }
    
    private function logError(string $type, string $message, string $file, int $line): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND);
    }
    
    private function logException(Throwable $exception): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND);
    }
    
    private function incrementErrorCount(string $type): void {
        if (!isset($this->errorCounts[$type])) {
            $this->errorCounts[$type] = 0;
        }
        $this->errorCounts[$type]++;
    }
    
    public function getErrorCounts(): array {
        return $this->errorCounts;
    }
    
    public function resetErrorCounts(): void {
        $this->errorCounts = [];
    }
    
    public function generateErrorReport(): string {
        $report = "Error Report - " . date('Y-m-d H:i:s') . "\n";
        $report .= "================================\n\n";
        
        foreach ($this->errorCounts as $type => $count) {
            $report .= "$type: $count\n";
        }
        
        return $report;
    }
}

// Usage examples
$reporting = new ErrorReporting();
$envReporting = new EnvironmentErrorReporting('development');
$customReporter = new CustomErrorReporter();

// Display current settings
$reporting->displaySettings();

// Test different environments
$envReporting->setEnvironment('production');
echo "Environment: " . $envReporting->getEnvironment() . "\n";
echo "Should display errors: " . ($envReporting->shouldDisplayErrors() ? 'Yes' : 'No') . "\n";
echo "Should log errors: " . ($envReporting->shouldLogErrors() ? 'Yes' : 'No') . "\n";

// Trigger some errors to test the custom reporter
echo $undefinedVariable; // This will trigger a notice
file_get_contents('nonexistent.txt'); // This will trigger a warning

// Display error statistics
echo "\nError Statistics:\n";
echo "================\n";
print_r($customReporter->getErrorCounts());

// Generate error report
echo "\nError Report:\n";
echo $customReporter->generateErrorReport();
?>
```

## Logging

### Error Logging Implementation
```php
<?php
class ErrorLogger {
    private string $logFile;
    private array $config;
    private array $logLevels = [
        'DEBUG' => 0,
        'INFO' => 1,
        'WARNING' => 2,
        'ERROR' => 3,
        'CRITICAL' => 4
    ];
    private string $minLevel;
    
    public function __construct(string $logFile = 'errors.log', array $config = []) {
        $this->logFile = $logFile;
        $this->config = array_merge([
            'date_format' => 'Y-m-d H:i:s',
            'max_file_size' => 10 * 1024 * 1024, // 10MB
            'backup_count' => 5,
            'include_trace' => true,
            'include_context' => true,
            'json_format' => true
        ], $config);
        
        $this->minLevel = $config['min_level'] ?? 'DEBUG';
        $this->ensureLogDirectory();
    }
    
    private function ensureLogDirectory(): void {
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    public function log(string $level, string $message, array $context = []): void {
        if (!$this->shouldLog($level)) {
            return;
        }
        
        $this->rotateLogIfNeeded();
        
        $logEntry = $this->formatLogEntry($level, $message, $context);
        $this->writeLog($logEntry);
    }
    
    public function debug(string $message, array $context = []): void {
        $this->log('DEBUG', $message, $context);
    }
    
    public function info(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }
    
    public function warning(string $message, array $context = []): void {
        $this->log('WARNING', $message, $context);
    }
    
    public function error(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }
    
    public function critical(string $message, array $context = []): void {
        $this->log('CRITICAL', $message, $context);
    }
    
    public function logException(Throwable $exception, array $context = []): void {
        $level = $this->getExceptionLevel($exception);
        $message = $this->formatExceptionMessage($exception);
        
        $exceptionContext = array_merge([
            'exception_type' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'trace' => $this->config['include_trace'] ? $exception->getTraceAsString() : null
        ], $context);
        
        $this->log($level, $message, $exceptionContext);
    }
    
    public function logError(int $errno, string $errstr, string $errfile, int $errline): void {
        $level = $this->getErrorLevel($errno);
        $message = "PHP Error: $errstr";
        
        $context = [
            'error_type' => $this->getErrorType($errno),
            'file' => $errfile,
            'line' => $errline,
            'errno' => $errno
        ];
        
        $this->log($level, $message, $context);
    }
    
    private function shouldLog(string $level): bool {
        return $this->logLevels[$level] >= $this->logLevels[$this->minLevel];
    }
    
    private function getExceptionLevel(Throwable $exception): string {
        if ($exception instanceof Error) {
            return 'CRITICAL';
        } elseif ($exception instanceof RuntimeException) {
            return 'ERROR';
        } elseif ($exception instanceof LogicException) {
            return 'ERROR';
        } else {
            return 'WARNING';
        }
    }
    
    private function getErrorLevel(int $errno): string {
        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                return 'CRITICAL';
                
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                return 'WARNING';
                
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'INFO';
                
            default:
                return 'DEBUG';
        }
    }
    
    private function getErrorType(int $errno): string {
        $types = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];
        
        return $types[$errno] ?? 'Unknown Error';
    }
    
    private function formatExceptionMessage(Throwable $exception): string {
        return get_class($exception) . ': ' . $exception->getMessage();
    }
    
    private function formatLogEntry(string $level, string $message, array $context): string {
        $timestamp = date($this->config['date_format']);
        
        if ($this->config['json_format']) {
            return $this->formatJsonLogEntry($timestamp, $level, $message, $context);
        } else {
            return $this->formatTextLogEntry($timestamp, $level, $message, $context);
        }
    }
    
    private function formatJsonLogEntry(string $timestamp, string $level, string $message, array $context): string {
        $entry = [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => $message,
            'context' => $this->config['include_context'] ? $context : []
        ];
        
        // Add request context
        if (isset($_SERVER)) {
            $entry['request'] = [
                'uri' => $_SERVER['REQUEST_URI'] ?? '',
                'method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ];
        }
        
        return json_encode($entry) . "\n";
    }
    
    private function formatTextLogEntry(string $timestamp, string $level, string $message, array $context): string {
        $entry = "[$timestamp] [$level] $message";
        
        if ($this->config['include_context'] && !empty($context)) {
            $entry .= " " . json_encode($context);
        }
        
        return $entry . "\n";
    }
    
    private function writeLog(string $logEntry): void {
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    private function rotateLogIfNeeded(): void {
        if (!file_exists($this->logFile)) {
            return;
        }
        
        if (filesize($this->logFile) < $this->config['max_file_size']) {
            return;
        }
        
        // Rotate log files
        for ($i = $this->config['backup_count']; $i > 0; $i--) {
            $oldFile = $this->logFile . '.' . $i;
            $newFile = $this->logFile . '.' . ($i + 1);
            
            if (file_exists($oldFile)) {
                if ($i == $this->config['backup_count']) {
                    unlink($oldFile);
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }
        
        // Move current log to .1
        rename($this->logFile, $this->logFile . '.1');
    }
    
    public function setMinLevel(string $level): void {
        if (!isset($this->logLevels[$level])) {
            throw new InvalidArgumentException("Invalid log level: $level");
        }
        
        $this->minLevel = $level;
    }
    
    public function getLogPath(): string {
        return $this->logFile;
    }
    
    public function clearLog(): void {
        file_put_contents($this->logFile, '');
    }
    
    public function getLogSize(): int {
        return file_exists($this->logFile) ? filesize($this->logFile) : 0;
    }
    
    public function searchLogs(string $searchTerm, int $maxResults = 100): array {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $results = [];
        
        foreach ($lines as $line) {
            if (stripos($line, $searchTerm) !== false) {
                $results[] = $line;
                
                if (count($results) >= $maxResults) {
                    break;
                }
            }
        }
        
        return $results;
    }
    
    public function getRecentLogs(int $lines = 50): array {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $allLines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_slice($allLines, -$lines);
    }
    
    public function getLogStats(): array {
        if (!file_exists($this->logFile)) {
            return [
                'total_lines' => 0,
                'file_size' => 0,
                'level_counts' => []
            ];
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $levelCounts = array_fill_keys(array_keys($this->logLevels), 0);
        
        foreach ($lines as $line) {
            foreach ($this->logLevels as $level => $value) {
                if (stripos($line, $level) !== false) {
                    $levelCounts[$level]++;
                    break;
                }
            }
        }
        
        return [
            'total_lines' => count($lines),
            'file_size' => filesize($this->logFile),
            'level_counts' => $levelCounts
        ];
    }
}

// Multi-channel logger
class MultiChannelLogger {
    private array $channels = [];
    private string $defaultChannel = 'default';
    
    public function addChannel(string $name, ErrorLogger $logger): void {
        $this->channels[$name] = $logger;
    }
    
    public function setDefaultChannel(string $name): void {
        if (!isset($this->channels[$name])) {
            throw new InvalidArgumentException("Channel '$name' does not exist");
        }
        
        $this->defaultChannel = $name;
    }
    
    public function log(string $level, string $message, array $context = [], string $channel = null): void {
        $channelName = $channel ?? $this->defaultChannel;
        
        if (!isset($this->channels[$channelName])) {
            throw new InvalidArgumentException("Channel '$channelName' does not exist");
        }
        
        $this->channels[$channelName]->log($level, $message, $context);
    }
    
    public function debug(string $message, array $context = [], string $channel = null): void {
        $this->log('DEBUG', $message, $context, $channel);
    }
    
    public function info(string $message, array $context = [], string $channel = null): void {
        $this->log('INFO', $message, $context, $channel);
    }
    
    public function warning(string $message, array $context = [], string $channel = null): void {
        $this->log('WARNING', $message, $context, $channel);
    }
    
    public function error(string $message, array $context = [], string $channel = null): void {
        $this->log('ERROR', $message, $context, $channel);
    }
    
    public function critical(string $message, array $context = [], string $channel = null): void {
        $this->log('CRITICAL', $message, $context, $channel);
    }
    
    public function logException(Throwable $exception, array $context = [], string $channel = null): void {
        $channelName = $channel ?? $this->defaultChannel;
        
        if (!isset($this->channels[$channelName])) {
            throw new InvalidArgumentException("Channel '$channelName' does not exist");
        }
        
        $this->channels[$channelName]->logException($exception, $context);
    }
    
    public function getChannel(string $name): ErrorLogger {
        if (!isset($this->channels[$name])) {
            throw new InvalidArgumentException("Channel '$name' does not exist");
        }
        
        return $this->channels[$name];
    }
    
    public function getChannels(): array {
        return $this->channels;
    }
}

// Usage examples
$logger = new ErrorLogger('application.log', [
    'min_level' => 'INFO',
    'json_format' => true,
    'include_trace' => true
]);

$multiLogger = new MultiChannelLogger();

// Add multiple channels
$multiLogger->addChannel('app', new ErrorLogger('app.log', ['min_level' => 'DEBUG']));
$multiLogger->addChannel('security', new ErrorLogger('security.log', ['min_level' => 'WARNING']));
$multiLogger->addChannel('performance', new ErrorLogger('performance.log', ['min_level' => 'INFO']));

// Set default channel
$multiLogger->setDefaultChannel('app');

// Log messages
$logger->info('Application started');
$logger->debug('Debug information', ['user_id' => 123]);
$logger->warning('Warning message', ['context' => 'test']);
$logger->error('Error occurred', ['error_code' => 500]);
$logger->critical('Critical system failure');

// Log exceptions
try {
    throw new Exception('Test exception');
} catch (Exception $e) {
    $logger->logException($e, ['user_id' => 123]);
}

// Multi-channel logging
$multiLogger->info('User logged in', ['user_id' => 123]);
$multiLogger->warning('Failed login attempt', ['email' => 'test@example.com'], 'security');
$multiLogger->info('Page load time', ['time' => 1.5, 'page' => '/dashboard'], 'performance');

// Get log statistics
$stats = $logger->getLogStats();
echo "Log Statistics:\n";
echo "Total lines: " . $stats['total_lines'] . "\n";
echo "File size: " . $stats['file_size'] . " bytes\n";
echo "Level counts: " . json_encode($stats['level_counts']) . "\n";

// Search logs
$searchResults = $logger->searchLogs('ERROR', 10);
echo "Found " . count($searchResults) . " error entries\n";

// Get recent logs
$recentLogs = $logger->getRecentLogs(5);
echo "Recent logs:\n";
foreach ($recentLogs as $log) {
    echo $log . "\n";
}
?>
```

## Best Practices

### Error Handling Best Practices
```php
<?php
class ErrorHandlingBestPractices {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    // 1. Always use try-catch for operations that can fail
    public function databaseOperation(array $data): array {
        try {
            $connection = $this->getConnection();
            $result = $this->executeQuery($connection, $data);
            
            $this->logger->info('Database operation successful', ['data_count' => count($data)]);
            
            return $result;
            
        } catch (PDOException $e) {
            $this->logger->error('Database operation failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'data_count' => count($data)
            ]);
            
            throw new DatabaseException('Database operation failed', '', [], $e);
            
        } catch (Exception $e) {
            $this->logger->error('Unexpected error in database operation', [
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            
            throw new ServiceException('Database', 'Unexpected error occurred', [], $e);
        }
    }
    
    // 2. Validate inputs early and fail fast
    public function processUserInput(array $input): array {
        $this->validateInput($input);
        
        try {
            $processed = $this->transformInput($input);
            $this->logger->info('Input processed successfully', ['input_size' => count($input)]);
            
            return $processed;
            
        } catch (ValidationException $e) {
            $this->logger->warning('Input validation failed', [
                'errors' => $e->getErrors(),
                'input' => $input
            ]);
            
            throw $e;
            
        } catch (Exception $e) {
            $this->logger->error('Input processing failed', [
                'error' => $e->getMessage(),
                'input_size' => count($input)
            ]);
            
            throw new ProcessingException('Failed to process input', [], $e);
        }
    }
    
    private function validateInput(array $input): void {
        $errors = [];
        
        if (empty($input['name'])) {
            $errors[] = 'Name is required';
        }
        
        if (empty($input['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors, $input);
        }
    }
    
    // 3. Use specific exception types
    public function fileOperation(string $filename): string {
        try {
            if (!file_exists($filename)) {
                throw new FileSystemException('File not found', $filename);
            }
            
            if (!is_readable($filename)) {
                throw new FileSystemException('File is not readable', $filename);
            }
            
            $content = file_get_contents($filename);
            
            if ($content === false) {
                throw new FileSystemException('Failed to read file', $filename);
            }
            
            $this->logger->info('File read successfully', ['filename' => $filename]);
            
            return $content;
            
        } catch (FileSystemException $e) {
            $this->logger->error('File operation failed', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
            
        } catch (Exception $e) {
            $this->logger->error('Unexpected file error', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            
            throw new FileSystemException('Unexpected file error', $filename, $e);
        }
    }
    
    // 4. Always clean up resources in finally blocks
    public function resourceIntensiveOperation(): array {
        $connection = null;
        $fileHandle = null;
        
        try {
            $connection = $this->getConnection();
            $fileHandle = fopen('data.txt', 'r');
            
            if (!$fileHandle) {
                throw new FileSystemException('Failed to open file', 'data.txt');
            }
            
            $data = $this->processDataWithResources($connection, $fileHandle);
            
            $this->logger->info('Resource intensive operation completed');
            
            return $data;
            
        } catch (Exception $e) {
            $this->logger->error('Resource intensive operation failed', [
                'error' => $e->getMessage()
            ]);
            
            throw $e;
            
        } finally {
            // Always clean up resources
            if ($connection) {
                $connection = null; // In real scenario, close connection
            }
            
            if ($fileHandle) {
                fclose($fileHandle);
            }
            
            $this->logger->debug('Resources cleaned up');
        }
    }
    
    // 5. Use appropriate error levels for logging
    public function logBasedOnSeverity(string $operation, bool $success, array $context = []): void {
        if ($success) {
            $this->logger->info("Operation successful: $operation", $context);
        } else {
            $this->logger->error("Operation failed: $operation", $context);
        }
    }
    
    // 6. Don't swallow exceptions without logging
    public function badExample(): void {
        try {
            $this->riskyOperation();
        } catch (Exception $e) {
            // BAD: Swallowing exception without logging
            // Do nothing
        }
    }
    
    public function goodExample(): void {
        try {
            $this->riskyOperation();
        } catch (Exception $e) {
            // GOOD: Log the exception
            $this->logger->error('Risky operation failed', [
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            
            // Re-throw or handle appropriately
            throw $e;
        }
    }
    
    // 7. Provide meaningful error messages
    public function meaningfulErrorMessages(): void {
        try {
            $this->complexOperation();
        } catch (DatabaseException $e) {
            // GOOD: Provide context and actionable information
            $this->logger->error('Database operation failed during user registration', [
                'operation' => 'user_registration',
                'user_id' => $context['user_id'] ?? 'unknown',
                'database_error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'suggestion' => 'Check database connection and permissions'
            ]);
            
            throw new ServiceException('UserService', 'Unable to complete user registration. Please try again later.', [], $e);
        }
    }
    
    // 8. Use error codes for better error handling
    public function operationWithErrorCode(): array {
        try {
            return $this->performOperation();
        } catch (ValidationException $e) {
            throw new ServiceException('UserService', 'Validation failed', [], $e, 400);
        } catch (AuthenticationException $e) {
            throw new ServiceException('UserService', 'Authentication failed', [], $e, 401);
        } catch (AuthorizationException $e) {
            throw new ServiceException('UserService', 'Access denied', [], $e, 403);
        } catch (NotFoundException $e) {
            throw new ServiceException('UserService', 'Resource not found', [], $e, 404);
        } catch (Exception $e) {
            throw new ServiceException('UserService', 'Internal server error', [], $e, 500);
        }
    }
    
    // 9. Implement graceful degradation
    public function gracefulDegradation(): array {
        $primaryResult = null;
        $fallbackResult = null;
        
        try {
            $primaryResult = $this->primaryDataSource();
            $this->logger->info('Primary data source successful');
            
        } catch (Exception $e) {
            $this->logger->warning('Primary data source failed, trying fallback', [
                'error' => $e->getMessage()
            ]);
            
            try {
                $fallbackResult = $this->fallbackDataSource();
                $this->logger->info('Fallback data source successful');
                
            } catch (Exception $fallbackException) {
                $this->logger->error('Both primary and fallback data sources failed', [
                    'primary_error' => $e->getMessage(),
                    'fallback_error' => $fallbackException->getMessage()
                ]);
                
                throw new ServiceException('DataService', 'All data sources unavailable');
            }
        }
        
        return $primaryResult ?? $fallbackResult;
    }
    
    // 10. Monitor and alert on critical errors
    public function monitoredOperation(): void {
        try {
            $this->criticalOperation();
            
        } catch (CriticalException $e) {
            $this->logger->critical('Critical system error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => date('Y-m-d H:i:s'),
                'server' => $_SERVER['SERVER_NAME'] ?? 'unknown'
            ]);
            
            // Send alert (in real scenario)
            $this->sendAlert($e);
            
            throw $e;
        }
    }
    
    // Helper methods (simplified)
    private function getConnection(): object {
        return new stdClass(); // Simplified
    }
    
    private function executeQuery($connection, array $data): array {
        return ['result' => 'data']; // Simplified
    }
    
    private function transformInput(array $input): array {
        return $input; // Simplified
    }
    
    private function processDataWithResources($connection, $fileHandle): array {
        return ['data' => 'processed']; // Simplified
    }
    
    private function riskyOperation(): void {
        // Simulate risky operation
        if (rand(1, 10) === 1) {
            throw new Exception('Risky operation failed');
        }
    }
    
    private function complexOperation(): void {
        // Simulate complex operation
        throw new DatabaseException('Complex operation failed');
    }
    
    private function performOperation(): array {
        return ['operation' => 'result'];
    }
    
    private function primaryDataSource(): array {
        if (rand(1, 3) === 1) {
            throw new Exception('Primary source unavailable');
        }
        return ['source' => 'primary', 'data' => 'data'];
    }
    
    private function fallbackDataSource(): array {
        return ['source' => 'fallback', 'data' => 'data'];
    }
    
    private function criticalOperation(): void {
        throw new CriticalException('Critical system failure');
    }
    
    private function sendAlert(Exception $e): void {
        // In real scenario, send email, SMS, or push notification
        error_log('ALERT: Critical error occurred - ' . $e->getMessage());
    }
}

// Error handling configuration class
class ErrorHandlingConfig {
    public static function configureForEnvironment(string $environment): void {
        switch ($environment) {
            case 'development':
                error_reporting(E_ALL);
                ini_set('display_errors', '1');
                ini_set('log_errors', '1');
                break;
                
            case 'testing':
                error_reporting(E_ALL & ~E_DEPRECATED);
                ini_set('display_errors', '1');
                ini_set('log_errors', '0');
                break;
                
            case 'staging':
                error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
                ini_set('display_errors', '0');
                ini_set('log_errors', '1');
                break;
                
            case 'production':
                error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
                ini_set('display_errors', '0');
                ini_set('log_errors', '1');
                ini_set('log_errors_max_len', '1024');
                break;
        }
    }
    
    public static function setupCustomErrorHandler(ErrorLogger $logger): void {
        set_error_handler(function(int $errno, string $errstr, string $errfile, int $errline) use ($logger) {
            $logger->logError($errno, $errstr, $errfile, $errline);
            
            // Don't show errors in production
            if (ini_get('display_errors')) {
                return false; // Let PHP handle display
            }
            
            return true; // Prevent PHP error display
        });
        
        set_exception_handler(function(Throwable $exception) use ($logger) {
            $logger->logException($exception);
            
            if (ini_get('display_errors')) {
                // Show detailed error in development
                echo "<h1>Uncaught Exception</h1>";
                echo "<p>" . htmlspecialchars($exception->getMessage()) . "</p>";
                echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
            } else {
                // Show generic error page in production
                echo "<h1>Server Error</h1>";
                echo "<p>Something went wrong. Please try again later.</p>";
            }
            
            exit(1);
        });
        
        register_shutdown_function(function() use ($logger) {
            $error = error_get_last();
            
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                $logger->logError($error['type'], $error['message'], $error['file'], $error['line']);
                
                if (!ini_get('display_errors')) {
                    echo "<h1>Server Error</h1>";
                    echo "<p>Something went wrong. Please try again later.</p>";
                }
            }
        });
    }
}

// Usage examples
$logger = new ErrorLogger('best_practices.log');
$bestPractices = new ErrorHandlingBestPractices($logger);

// Configure error handling for environment
ErrorHandlingConfig::configureForEnvironment('development');
ErrorHandlingConfig::setupCustomErrorHandler($logger);

// Test best practices
try {
    $result = $bestPractices->databaseOperation(['test' => 'data']);
    echo "Database operation successful\n";
} catch (Exception $e) {
    echo "Database operation failed: " . $e->getMessage() . "\n";
}

try {
    $result = $bestPractices->processUserInput(['name' => 'John', 'email' => 'john@example.com']);
    echo "Input processed successfully\n";
} catch (ValidationException $e) {
    echo "Validation failed: " . implode(', ', $e->getErrors()) . "\n";
}

try {
    $content = $bestPractices->fileOperation('nonexistent.txt');
    echo "File operation successful\n";
} catch (FileSystemException $e) {
    echo "File operation failed: " . $e->getMessage() . "\n";
}

try {
    $result = $bestPractices->gracefulDegradation();
    echo "Operation completed with graceful degradation\n";
} catch (Exception $e) {
    echo "Operation failed completely: " . $e->getMessage() . "\n";
}
?>
```

## Common Pitfalls

### Error Handling Pitfalls
```php
<?php
// Pitfall 1: Swallowing exceptions without proper handling
class BadErrorHandling1 {
    public function riskyOperation() {
        try {
            $this->doSomethingRisky();
        } catch (Exception $e) {
            // BAD: Swallowing exception without logging or handling
            // This hides errors and makes debugging impossible
        }
    }
}

// Solution: Always handle or log exceptions
class GoodErrorHandling1 {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    public function riskyOperation() {
        try {
            $this->doSomethingRisky();
        } catch (Exception $e) {
            // GOOD: Log the exception and handle appropriately
            $this->logger->error('Risky operation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Either re-throw or handle gracefully
            throw new ServiceException('Operation failed', [], $e);
        }
    }
    
    private function doSomethingRisky(): void {
        throw new Exception('Something went wrong');
    }
}

// Pitfall 2: Using generic catch blocks
class BadErrorHandling2 {
    public function processUser(array $userData) {
        try {
            $this->validateUser($userData);
            $this->saveUser($userData);
        } catch (Exception $e) {
            // BAD: Catching all exceptions with same handling
            echo "An error occurred";
        }
    }
}

// Solution: Use specific exception types
class GoodErrorHandling2 {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    public function processUser(array $userData) {
        try {
            $this->validateUser($userData);
            $this->saveUser($userData);
            
        } catch (ValidationException $e) {
            $this->logger->warning('User validation failed', [
                'errors' => $e->getErrors(),
                'user_data' => $userData
            ]);
            
            throw new ServiceException('UserService', 'Invalid user data', $userData, $e);
            
        } catch (DatabaseException $e) {
            $this->logger->error('Database error during user save', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql()
            ]);
            
            throw new ServiceException('UserService', 'Failed to save user', $userData, $e);
            
        } catch (Exception $e) {
            $this->logger->error('Unexpected error in user processing', [
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            
            throw new ServiceException('UserService', 'Unexpected error', $userData, $e);
        }
    }
    
    private function validateUser(array $userData): void {
        if (empty($userData['email'])) {
            throw new ValidationException(['Email is required']);
        }
    }
    
    private function saveUser(array $userData): void {
        throw new DatabaseException('Save failed');
    }
}

// Pitfall 3: Not cleaning up resources
class BadErrorHandling3 {
    public function processFile(string $filename) {
        $handle = fopen($filename, 'r');
        
        try {
            $data = fread($handle, 1024);
            $this->processData($data);
            
        } catch (Exception $e) {
            // BAD: File handle is not closed on exception
            throw $e;
        }
        
        fclose($handle); // This might not be reached if exception occurs
    }
}

// Solution: Use finally blocks for cleanup
class GoodErrorHandling3 {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    public function processFile(string $filename) {
        $handle = null;
        
        try {
            $handle = fopen($filename, 'r');
            
            if (!$handle) {
                throw new FileSystemException('Failed to open file', $filename);
            }
            
            $data = fread($handle, 1024);
            $this->processData($data);
            
        } catch (Exception $e) {
            $this->logger->error('File processing failed', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
            
        } finally {
            // GOOD: Always clean up resources
            if ($handle) {
                fclose($handle);
                $this->logger->debug('File handle closed', ['filename' => $filename]);
            }
        }
    }
    
    private function processData(string $data): void {
        throw new Exception('Data processing failed');
    }
}

// Pitfall 4: Not providing context in error messages
class BadErrorHandling4 {
    public function updateUser(int $userId, array $data) {
        try {
            $this->saveUser($userId, $data);
        } catch (Exception $e) {
            // BAD: Generic error message without context
            throw new Exception('Update failed');
        }
    }
}

// Solution: Provide meaningful context
class GoodErrorHandling4 {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    public function updateUser(int $userId, array $data) {
        try {
            $this->saveUser($userId, $data);
            
        } catch (DatabaseException $e) {
            $this->logger->error('User update failed', [
                'user_id' => $userId,
                'data_fields' => array_keys($data),
                'database_error' => $e->getMessage(),
                'sql' => $e->getSql()
            ]);
            
            throw new ServiceException('UserService', 
                "Failed to update user $userId: " . $e->getMessage(), 
                ['user_id' => $userId, 'data' => $data], 
                $e);
                
        } catch (Exception $e) {
            $this->logger->error('Unexpected error in user update', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            throw new ServiceException('UserService', 
                "Unexpected error updating user $userId", 
                ['user_id' => $userId], 
                $e);
        }
    }
    
    private function saveUser(int $userId, array $data): void {
        throw new DatabaseException('Database constraint violation');
    }
}

// Pitfall 5: Not using appropriate error levels
class BadErrorHandling5 {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    public function loginUser(string $email, string $password) {
        try {
            $user = $this->authenticate($email, $password);
            
            // BAD: Using error level for successful operation
            $this->logger->error('User logged in', ['email' => $email]);
            
            return $user;
            
        } catch (AuthenticationException $e) {
            // BAD: Using info level for authentication failure
            $this->logger->info('Authentication failed', ['email' => $email]);
            
            throw $e;
        }
    }
}

// Solution: Use appropriate error levels
class GoodErrorHandling5 {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    public function loginUser(string $email, string $password) {
        try {
            $user = $this->authenticate($email, $password);
            
            // GOOD: Using info level for successful operation
            $this->logger->info('User logged in successfully', [
                'email' => $email,
                'user_id' => $user['id']
            ]);
            
            return $user;
            
        } catch (AuthenticationException $e) {
            // GOOD: Using warning level for authentication failure
            $this->logger->warning('Authentication failed', [
                'email' => $email,
                'reason' => $e->getMessage(),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            
            throw $e;
        }
    }
    
    private function authenticate(string $email, string $password): array {
        throw new AuthenticationException('Invalid credentials', $email);
    }
}

// Pitfall 6: Not handling edge cases
class BadErrorHandling6 {
    public function divideNumbers(int $a, int $b): float {
        // BAD: Not handling division by zero
        return $a / $b;
    }
}

// Solution: Handle edge cases explicitly
class GoodErrorHandling6 {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    public function divideNumbers(int $a, int $b): float {
        if ($b === 0) {
            $this->logger->warning('Division by zero attempted', [
                'numerator' => $a,
                'denominator' => $b
            ]);
            
            throw new InvalidArgumentException('Division by zero is not allowed');
        }
        
        $result = $a / $b;
        
        $this->logger->debug('Division performed', [
            'numerator' => $a,
            'denominator' => $b,
            'result' => $result
        ]);
        
        return $result;
    }
}

// Pitfall 7: Not implementing graceful degradation
class BadErrorHandling7 {
    public function getData(): array {
        try {
            return $this->getFromPrimarySource();
        } catch (Exception $e) {
            // BAD: No fallback mechanism
            throw new ServiceException('Data unavailable');
        }
    }
}

// Solution: Implement graceful degradation
class GoodErrorHandling7 {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    public function getData(): array {
        try {
            $data = $this->getFromPrimarySource();
            $this->logger->info('Primary data source successful');
            return $data;
            
        } catch (Exception $e) {
            $this->logger->warning('Primary data source failed, trying fallback', [
                'error' => $e->getMessage()
            ]);
            
            try {
                $fallbackData = $this->getFromFallbackSource();
                $this->logger->info('Fallback data source successful');
                return $fallbackData;
                
            } catch (Exception $fallbackException) {
                $this->logger->error('All data sources failed', [
                    'primary_error' => $e->getMessage(),
                    'fallback_error' => $fallbackException->getMessage()
                ]);
                
                // Return cached data if available
                $cachedData = $this->getCachedData();
                if ($cachedData) {
                    $this->logger->info('Returning cached data');
                    return $cachedData;
                }
                
                throw new ServiceException('Data', 'All data sources unavailable');
            }
        }
    }
    
    private function getFromPrimarySource(): array {
        throw new Exception('Primary source unavailable');
    }
    
    private function getFromFallbackSource(): array {
        return ['source' => 'fallback', 'data' => 'cached_data'];
    }
    
    private function getCachedData(): array {
        return ['source' => 'cache', 'data' => 'old_data'];
    }
}

// Pitfall 8: Not testing error handling paths
class BadErrorHandling8 {
    public function processPayment(array $paymentData): bool {
        // BAD: No error handling, assumes everything works
        $gateway = new PaymentGateway();
        return $gateway->process($paymentData);
    }
}

// Solution: Test and handle error paths
class GoodErrorHandling8 {
    private ErrorLogger $logger;
    
    public function __construct(ErrorLogger $logger) {
        $this->logger = $logger;
    }
    
    public function processPayment(array $paymentData): bool {
        try {
            $this->validatePaymentData($paymentData);
            
            $gateway = new PaymentGateway();
            $result = $gateway->process($paymentData);
            
            if ($result['success']) {
                $this->logger->info('Payment processed successfully', [
                    'payment_id' => $result['payment_id'],
                    'amount' => $paymentData['amount']
                ]);
                
                return true;
            } else {
                $this->logger->error('Payment processing failed', [
                    'gateway_response' => $result,
                    'payment_data' => $paymentData
                ]);
                
                throw new PaymentException('Payment failed: ' . $result['message']);
            }
            
        } catch (ValidationException $e) {
            $this->logger->warning('Payment validation failed', [
                'errors' => $e->getErrors(),
                'payment_data' => $paymentData
            ]);
            
            throw $e;
            
        } catch (PaymentException $e) {
            $this->logger->error('Payment processing error', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);
            
            throw $e;
            
        } catch (Exception $e) {
            $this->logger->error('Unexpected payment error', [
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            
            throw new PaymentException('Unexpected payment error', [], $e);
        }
    }
    
    private function validatePaymentData(array $paymentData): void {
        if (empty($paymentData['amount']) || $paymentData['amount'] <= 0) {
            throw new ValidationException(['Invalid amount']);
        }
        
        if (empty($paymentData['card_number'])) {
            throw new ValidationException(['Card number is required']);
        }
    }
}

// Usage examples
$logger = new ErrorLogger('pitfalls.log');

// Test bad examples
echo "Testing bad error handling examples:\n";
$bad1 = new BadErrorHandling1();
$bad1->riskyOperation(); // No indication of error

// Test good examples
echo "\nTesting good error handling examples:\n";
$good1 = new GoodErrorHandling1($logger);
try {
    $good1->riskyOperation();
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}

$good2 = new GoodErrorHandling2($logger);
try {
    $good2->processUser(['email' => '']);
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}

$good3 = new GoodErrorHandling3($logger);
try {
    $good3->processFile('nonexistent.txt');
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}

$good5 = new GoodErrorHandling5($logger);
try {
    $good5->loginUser('test@example.com', 'wrongpassword');
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}

$good6 = new GoodErrorHandling6($logger);
try {
    $result = $good6->divideNumbers(10, 0);
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}

$good7 = new GoodErrorHandling7($logger);
try {
    $data = $good7->getData();
    echo "Got data: " . json_encode($data) . "\n";
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}
?>
```

## Summary

PHP Error Handling provides:

**Error Types and Levels:**
- Fatal errors, warnings, notices
- Parse errors and strict standards
- User-defined errors
- Error level constants and reporting

**Exception Handling:**
- Try-catch-finally blocks
- Multiple exception types
- Exception chaining
- Custom exception classes

**Error Reporting:**
- Environment-specific configuration
- Development vs production settings
- Custom error handlers
- Error display and logging

**Logging:**
- Structured error logging
- Multiple log levels
- Log rotation and management
- Multi-channel logging

**Best Practices:**
- Always handle exceptions properly
- Use specific exception types
- Provide meaningful error messages
- Clean up resources in finally blocks
- Use appropriate log levels
- Implement graceful degradation
- Monitor critical errors

**Common Pitfalls:**
- Swallowing exceptions without handling
- Using generic catch blocks
- Not cleaning up resources
- Missing error context
- Wrong log levels
- No fallback mechanisms
- Untested error paths

PHP provides comprehensive error handling capabilities that, when used correctly, create robust and maintainable applications with proper debugging and monitoring capabilities.
