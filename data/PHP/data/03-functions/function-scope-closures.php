<?php
/**
 * PHP Function Scope and Closures
 * 
 * Understanding variable scope, closures, and advanced scoping concepts in PHP functions.
 */

echo "=== PHP Function Scope and Closures ===\n\n";

// Variable Scope Basics
echo "--- Variable Scope Basics ---\n";

$globalVar = "I am global";

function testScope() {
    $localVar = "I am local";
    echo "Inside function - Local: $localVar\n";
    echo "Inside function - Global (not accessible): " . ($globalVar ?? 'undefined') . "\n";
}

testScope();
echo "Outside function - Global: $globalVar\n";
echo "Outside function - Local: " . ($localVar ?? 'undefined') . "\n\n";

// Global Keyword
echo "--- Global Keyword ---\n";

$counter = 0;

function incrementCounter() {
    global $counter;
    $counter++;
    echo "Counter incremented to: $counter\n";
}

incrementCounter();
incrementCounter();
echo "Final counter value: $counter\n\n";

// $GLOBALS Superglobal
echo "--- \$GLOBALS Superglobal ---\n";

$anotherCounter = 0;

function incrementAnother() {
    $GLOBALS['anotherCounter']++;
    echo "Another counter: " . $GLOBALS['anotherCounter'] . "\n";
}

incrementAnother();
incrementAnother();
echo "Final another counter: $anotherCounter\n\n";

// Static Variables
echo "--- Static Variables ---\n";

function staticCounter() {
    static $count = 0;
    $count++;
    echo "Static counter: $count\n";
}

staticCounter(); // 1
staticCounter(); // 2
staticCounter(); // 3
echo "\n";

// Static variables with initialization
function staticWithInit($initial = 0) {
    static $initialized = false;
    static $value = 0;
    
    if (!$initialized) {
        $value = $initial;
        $initialized = true;
    }
    
    $value++;
    return $value;
}

echo "Static with initialization:\n";
echo staticWithInit(10) . "\n"; // 11
echo staticWithInit(20) . "\n"; // 12 (ignores new initial value)
echo staticWithInit() . "\n";   // 13
echo "\n";

// Closures and Scope
echo "--- Closures and Scope ---\n";

function createClosure() {
    $message = "Hello from closure";
    
    return function() use ($message) {
        return $message;
    };
}

$closure = createClosure();
echo "Closure result: " . $closure() . "\n";

// Closure with mutable variables
function createMutableClosure() {
    $counter = 0;
    
    return function() use (&$counter) {
        $counter++;
        return "Count: $counter";
    };
}

$mutableClosure = createMutableClosure();
echo $mutableClosure() . "\n"; // Count: 1
echo $mutableClosure() . "\n"; // Count: 2
echo $mutableClosure() . "\n"; // Count: 3
echo "\n";

// Closure Binding
echo "--- Closure Binding ---\n";

class ClosureExample {
    private $privateVar = "Private value";
    public $publicVar = "Public value";
    
    public function getPrivateClosure() {
        return function() {
            return $this->privateVar;
        };
    }
    
    public function getPublicClosure() {
        return function() {
            return $this->publicVar;
        };
    }
}

$obj = new ClosureExample();

// Get closures
$privateClosure = $obj->getPrivateClosure();
$publicClosure = $obj->getPublicClosure();

// Bind to object
$boundPrivate = $privateClosure->bindTo($obj, 'ClosureExample');
$boundPublic = $publicClosure->bindTo($obj);

echo "Bound private closure: " . $boundPrivate() . "\n";
echo "Bound public closure: " . $boundPublic() . "\n\n";

// Static Closures
echo "--- Static Closures ---\n";

function createStaticClosure() {
    $value = "Non-static";
    
    return static function() use ($value) {
        // $this is not available in static closures
        return "Static closure with: $value";
    };
}

$staticClosure = createStaticClosure();
echo "Static closure result: " . $staticClosure() . "\n\n";

// Scope Resolution Operator
echo "--- Scope Resolution Operator ---\n";

class ScopeTest {
    const CONSTANT = "Class constant";
    public static $staticVar = "Static variable";
    
    public static function staticMethod() {
        return "Static method";
    }
    
    public function instanceMethod() {
        return "Instance method";
    }
}

echo "Class constant: " . ScopeTest::CONSTANT . "\n";
echo "Static variable: " . ScopeTest::$staticVar . "\n";
echo "Static method: " . ScopeTest::staticMethod() . "\n";

$instance = new ScopeTest();
echo "Instance method: " . $instance->instanceMethod() . "\n\n";

// Late Static Binding
echo "--- Late Static Binding ---\n";

class ParentClass {
    public static function who() {
        echo __CLASS__ . "\n";
    }
    
    public static function test() {
        self::who();
        static::who(); // Late static binding
    }
}

class ChildClass extends ParentClass {
    public static function who() {
        echo __CLASS__ . "\n";
    }
}

echo "Late static binding demonstration:\n";
ChildClass::test();
echo "\n";

// Anonymous Classes and Scope
echo "--- Anonymous Classes and Scope ---\n";

function createAnonymousClass($value) {
    return new class($value) {
        private $value;
        
        public function __construct($value) {
            $this->value = $value;
        }
        
        public function getValue() {
            return $this->value;
        }
        
        public function getClassName() {
            return get_class($this);
        }
    };
}

$anonymous = createAnonymousClass("Test value");
echo "Anonymous class value: " . $anonymous->getValue() . "\n";
echo "Anonymous class name: " . $anonymous->getClassName() . "\n\n";

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: Configuration Manager with Scope
echo "Example 1: Configuration Manager\n";
class ConfigManager {
    private static $config = [];
    private static $loaded = false;
    
    public static function load($configFile) {
        if (!self::$loaded) {
            // Simulate loading config
            self::$config = [
                'database' => [
                    'host' => 'localhost',
                    'port' => 3306
                ],
                'app' => [
                    'name' => 'MyApp',
                    'version' => '1.0'
                ]
            ];
            self::$loaded = true;
        }
    }
    
    public static function get($key, $default = null) {
        self::load('config.php');
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public static function set($key, $value) {
        self::load('config.php');
        
        $keys = explode('.', $key);
        $config = &self::$config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }
}

echo "Database host: " . ConfigManager::get('database.host') . "\n";
echo "App version: " . ConfigManager::get('app.version') . "\n";
ConfigManager::set('app.debug', true);
echo "Debug mode: " . (ConfigManager::get('app.debug') ? 'true' : 'false') . "\n\n";

// Example 2: Function Factory with Closures
echo "Example 2: Function Factory\n";
class FunctionFactory {
    public static function createValidator($type, $options = []) {
        switch ($type) {
            case 'email':
                return function($value) {
                    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                };
                
            case 'length':
                $min = $options['min'] ?? 0;
                $max = $options['max'] ?? PHP_INT_MAX;
                return function($value) use ($min, $max) {
                    $length = strlen($value);
                    return $length >= $min && $length <= $max;
                };
                
            case 'regex':
                $pattern = $options['pattern'] ?? '/.*/';
                return function($value) use ($pattern) {
                    return preg_match($pattern, $value) === 1;
                };
                
            default:
                return function($value) {
                    return true; // Always valid
                };
        }
    }
    
    public static function createTransformer($type) {
        switch ($type) {
            case 'uppercase':
                return function($value) {
                    return strtoupper($value);
                };
                
            case 'lowercase':
                return function($value) {
                    return strtolower($value);
                };
                
            case 'trim':
                return function($value) {
                    return trim($value);
                };
                
            case 'slugify':
                return function($value) {
                    $value = strtolower($value);
                    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
                    return trim($value, '-');
                };
                
            default:
                return function($value) {
                    return $value;
                };
        }
    }
}

// Create validators
$emailValidator = FunctionFactory::createValidator('email');
$lengthValidator = FunctionFactory::createValidator('length', ['min' => 5, 'max' => 10]);
$regexValidator = FunctionFactory::createValidator('regex', ['pattern' => '/^[A-Z]+$/']);

echo "Validation examples:\n";
echo "Email 'test@example.com': " . ($emailValidator('test@example.com') ? 'valid' : 'invalid') . "\n";
echo "Length 'hello': " . ($lengthValidator('hello') ? 'valid' : 'invalid') . "\n";
echo "Length 'hello world': " . ($lengthValidator('hello world') ? 'valid' : 'invalid') . "\n";
echo "Regex 'HELLO': " . ($regexValidator('HELLO') ? 'valid' : 'invalid') . "\n";

// Create transformers
$upperTransformer = FunctionFactory::createTransformer('uppercase');
$slugTransformer = FunctionFactory::createTransformer('slugify');

echo "\nTransformation examples:\n";
echo "Uppercase 'hello world': " . $upperTransformer('hello world') . "\n";
echo "Slugify 'Hello World!': " . $slugTransformer('Hello World!') . "\n\n";

// Example 3: Memoization with Scope
echo "Example 3: Memoization with Scope\n";
class Memoizer {
    private static $cache = [];
    
    public static function memoize($function, $key = null) {
        return function(...$args) use ($function, $key) {
            $cacheKey = $key ?? md5(serialize($args));
            
            if (!isset(self::$cache[$cacheKey])) {
                self::$cache[$cacheKey] = $function(...$args);
            }
            
            return self::$cache[$cacheKey];
        };
    }
    
    public static function clearCache() {
        self::$cache = [];
    }
    
    public static function getCacheSize() {
        return count(self::$cache);
    }
}

// Expensive function
function expensiveCalculation($x, $y) {
    echo "Performing expensive calculation for $x and $y...\n";
    usleep(50000); // Simulate work
    return $x * $y + $x + $y;
}

$memoizedCalc = Memoizer::memoize('expensiveCalculation');

echo "First call: " . $memoizedCalc(10, 20) . "\n";
echo "Second call (cached): " . $memoizedCalc(10, 20) . "\n";
echo "Different arguments: " . $memoizedCalc(5, 15) . "\n";
echo "Cached call: " . $memoizedCalc(5, 15) . "\n";
echo "Cache size: " . Memoizer::getCacheSize() . "\n\n";

// Example 4: Scope-Based Logger
echo "Example 4: Scope-Based Logger\n";
class Logger {
    private static $logs = [];
    private static $level = 'info';
    
    public static function setLevel($level) {
        self::$level = $level;
    }
    
    public static function log($level, $message) {
        $levels = ['debug' => 0, 'info' => 1, 'warning' => 2, 'error' => 3];
        
        if ($levels[$level] >= $levels[self::$level]) {
            $timestamp = date('Y-m-d H:i:s');
            self::$logs[] = "[$timestamp] [$level] $message";
        }
    }
    
    public static function debug($message) {
        self::log('debug', $message);
    }
    
    public static function info($message) {
        self::log('info', $message);
    }
    
    public static function warning($message) {
        self::log('warning', $message);
    }
    
    public static function error($message) {
        self::log('error', $message);
    }
    
    public static function getLogs() {
        return self::$logs;
    }
    
    public static function clearLogs() {
        self::$logs = [];
    }
}

// Create scoped logger function
function createScopedLogger($context) {
    return function($level, $message) use ($context) {
        Logger::log($level, "[$context] $message");
    };
}

Logger::setLevel('debug');
$scopedLogger = createScopedLogger('UserService');

$scopedLogger('info', 'User created');
$scopedLogger('debug', 'Validating user data');
$scopedLogger('warning', 'User already exists');
$scopedLogger('error', 'Failed to create user');

echo "Logger output:\n";
foreach (Logger::getLogs() as $log) {
    echo "$log\n";
}
echo "\n";

// Example 5: Scope-Based State Machine
echo "Example 5: Scope-Based State Machine\n";
class StateMachine {
    private $states = [];
    private $currentState = null;
    private $context = [];
    
    public function __construct($initialState) {
        $this->currentState = $initialState;
    }
    
    public function addState($name, $handlers = []) {
        $this->states[$name] = $handlers;
    }
    
    public function setState($state) {
        if (isset($this->states[$state])) {
            $this->currentState = $state;
            if (isset($this->states[$state]['onEnter'])) {
                $this->states[$state]['onEnter']($this->context);
            }
        }
    }
    
    public function handle($event, $data = null) {
        if (isset($this->states[$this->currentState][$event])) {
            $handler = $this->states[$this->currentState][$event];
            return $handler($data, $this->context);
        }
        return null;
    }
    
    public function getContext($key = null) {
        if ($key) {
            return $this->context[$key] ?? null;
        }
        return $this->context;
    }
    
    public function setContext($key, $value) {
        $this->context[$key] = $value;
    }
    
    public function getCurrentState() {
        return $this->currentState;
    }
}

// Create a user login state machine
$loginStateMachine = new StateMachine('logged_out');

$loginStateMachine->addState('logged_out', [
    'onEnter' => function(&$context) {
        echo "User is logged out\n";
    },
    'login' => function($credentials, &$context) {
        if ($credentials['username'] === 'admin' && $credentials['password'] === 'secret') {
            $context['user'] = $credentials['username'];
            $context['loginTime'] = date('H:i:s');
            return 'logged_in';
        }
        return 'logged_out';
    }
]);

$loginStateMachine->addState('logged_in', [
    'onEnter' => function(&$context) {
        echo "User {$context['user']} is logged in at {$context['loginTime']}\n";
    },
    'logout' => function($data, &$context) {
        $context = [];
        return 'logged_out';
    },
    'access' => function($resource, &$context) {
        echo "User {$context['user']} accessing $resource\n";
        return 'logged_in';
    }
]);

echo "State machine demonstration:\n";
$loginStateMachine->handle('login', ['username' => 'admin', 'password' => 'wrong']);
echo "Current state: " . $loginStateMachine->getCurrentState() . "\n";

$loginStateMachine->handle('login', ['username' => 'admin', 'password' => 'secret']);
echo "Current state: " . $loginStateMachine->getCurrentState() . "\n";

$loginStateMachine->handle('access', 'dashboard');
$loginStateMachine->handle('logout', null);
echo "Current state: " . $loginStateMachine->getCurrentState() . "\n\n";

echo "=== End of Function Scope and Closures ===\n";
?>
