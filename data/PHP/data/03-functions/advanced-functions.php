<?php
/**
 * Advanced PHP Functions
 * 
 * Exploring advanced function concepts including closures, anonymous functions,
 * callbacks, generators, and function references.
 */

echo "=== Advanced PHP Functions ===\n\n";

// Anonymous Functions (Closures)
echo "--- Anonymous Functions (Closures) ---\n";

// Basic anonymous function
$greet = function($name) {
    return "Hello, $name!";
};

echo $greet("John") . "\n";

// Anonymous function with use clause
$multiplier = 5;
$multiply = function($number) use ($multiplier) {
    return $number * $multiplier;
};

echo "5 × 10 = " . $multiply(10) . "\n";

// Anonymous function as callback
$numbers = [1, 2, 3, 4, 5];
$squared = array_map(function($n) {
    return $n * $n;
}, $numbers);

echo "Squared numbers: " . implode(", ", $squared) . "\n\n";

// Closure Binding
echo "--- Closure Binding ---\n";

class TestClass {
    private $value = "Private Value";
    
    public function getClosure() {
        return function() {
            return $this->value;
        };
    }
    
    public function getStaticClosure() {
        return static function() {
            return "Static Closure";
        };
    }
}

$obj = new TestClass();
$closure = $obj->getClosure();

// Bind closure to object
$boundClosure = $closure->bindTo($obj);
echo "Bound closure result: " . $boundClosure() . "\n";

// Static closure
$staticClosure = $obj->getStaticClosure();
echo "Static closure result: " . $staticClosure() . "\n\n";

// Callback Functions
echo "--- Callback Functions ---\n";

// Function as callback
function processArray($array, $callback) {
    $result = [];
    foreach ($array as $item) {
        $result[] = $callback($item);
    }
    return $result;
}

function toUpper($string) {
    return strtoupper($string);
}

$words = ["hello", "world", "php"];
$upperWords = processArray($words, "toUpper");
echo "Uppercase words: " . implode(", ", $upperWords) . "\n";

// Method as callback
class StringProcessor {
    public function addExclamation($string) {
        return $string . "!";
    }
    
    public static function addQuestion($string) {
        return $string . "?";
    }
}

$processor = new StringProcessor();
$excited = processArray($words, [$processor, "addExclamation"]);
echo "Excited words: " . implode(", ", $excited) . "\n";

// Static method as callback
$questioned = processArray($words, ["StringProcessor", "addQuestion"]);
echo "Questioned words: " . implode(", ", $questioned) . "\n\n";

// Variable Functions
echo "--- Variable Functions ---\n";

function foo() {
    return "Function foo called\n";
}

function bar($arg) {
    return "Function bar called with: $arg\n";
}

$func = 'foo';
echo $func();

$func = 'bar';
echo $func("test");

// Method calling with variable
class MyClass {
    public function method1() {
        return "Method 1\n";
    }
    
    public function method2($param) {
        return "Method 2 with: $param\n";
    }
}

$obj = new MyClass();
$method = 'method1';
echo $obj->$method();

$method = 'method2';
echo $obj->$method("parameter");
echo "\n";

// Callable Type Hint
echo "--- Callable Type Hint ---\n";

function executeCallback($callback, $param) {
    if (is_callable($callback)) {
        return $callback($param);
    }
    return "Invalid callback";
}

$result = executeCallback(function($x) {
    return $x * 2;
}, 10);
echo "Callback result: $result\n";

$result = executeCallback("strtoupper", "hello");
echo "String result: $result\n\n";

// Function Parameters
echo "--- Advanced Function Parameters ---\n";

// Type hints and return types
function calculate(int $a, int $b): int {
    return $a + $b;
}

echo "Calculate: " . calculate(5, 3) . "\n";

// Union types (PHP 8.0+)
function processValue(int|string $value): string {
    if (is_int($value)) {
        return "Integer: $value";
    }
    return "String: $value";
}

echo processValue(42) . "\n";
echo processValue("hello") . "\n";

// Nullable types
function getName(?string $name): string {
    return $name ?? "Anonymous";
}

echo getName("John") . "\n";
echo getName(null) . "\n";

// Variadic functions
function sum(...$numbers): int {
    return array_sum($numbers);
}

echo "Sum of 1, 2, 3, 4, 5: " . sum(1, 2, 3, 4, 5) . "\n";

// Named arguments (PHP 8.0+)
function createUser($name, $email, $age = 18, $country = "USA") {
    return "User: $name, Email: $email, Age: $age, Country: $country";
}

echo createUser(email: "john@example.com", name: "John", age: 25) . "\n\n";

// Generators
echo "--- Generators ---\n";

function countUpTo($max) {
    for ($i = 1; $i <= $max; $i++) {
        yield $i;
    }
}

echo "Counting to 5: ";
foreach (countUpTo(5) as $number) {
    echo "$number ";
}
echo "\n";

// Generator with keys
function keyValueGenerator() {
    yield 'name' => 'John';
    yield 'age' => 30;
    yield 'city' => 'New York';
}

echo "\nKey-value generator:\n";
foreach (keyValueGenerator() as $key => $value) {
    echo "$key: $value\n";
}

// Generator delegation
function numberGenerator($start, $end) {
    yield from range($start, $end);
}

echo "\nDelegated generator (10-15): ";
foreach (numberGenerator(10, 15) as $num) {
    echo "$num ";
}
echo "\n\n";

// Higher-Order Functions
echo "--- Higher-Order Functions ---\n";

// Function that returns a function
function createMultiplier($factor) {
    return function($number) use ($factor) {
        return $number * $factor;
    };
}

$double = createMultiplier(2);
$triple = createMultiplier(3);

echo "Double 10: " . $double(10) . "\n";
echo "Triple 10: " . $triple(10) . "\n";

// Function composition
function compose($f, $g) {
    return function($x) use ($f, $g) {
        return $f($g($x));
    };
}

$addOne = function($x) { return $x + 1; };
$double = function($x) { return $x * 2; };

$composed = compose($double, $addOne);
echo "Composed function (f(g(5))): " . $composed(5) . "\n\n";

// Currying
echo "--- Currying ---\n";

function curry($function) {
    return function(...$args) use ($function) {
        return function(...$moreArgs) use ($function, $args) {
            return $function(...$args, ...$moreArgs);
        };
    };
}

function add($a, $b, $c) {
    return $a + $b + $c;
}

$curriedAdd = curry('add');
$add5 = $curriedAdd(5);
$add5and3 = $add5(3);
$result = $add5and3(2);

echo "Curried add(5, 3, 2): $result\n\n";

// Memoization
echo "--- Memoization ---\n";

function memoize($function) {
    $cache = [];
    
    return function(...$args) use ($function, &$cache) {
        $key = serialize($args);
        
        if (!isset($cache[$key])) {
            $cache[$key] = $function(...$args);
        }
        
        return $cache[$key];
    };
}

// Expensive function simulation
function fibonacci($n) {
    if ($n <= 1) {
        return $n;
    }
    return fibonacci($n - 1) + fibonacci($n - 2);
}

$memoizedFib = memoize('fibonacci');

echo "Fibonacci(10): " . $memoizedFib(10) . "\n";
echo "Fibonacci(15): " . $memoizedFib(15) . "\n";
echo "Fibonacci(10) (cached): " . $memoizedFib(10) . "\n\n";

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: Array Pipeline
echo "Example 1: Array Pipeline\n";
function pipeline(...$functions) {
    return function($initial) use ($functions) {
        $result = $initial;
        
        foreach ($functions as $function) {
            $result = $function($result);
        }
        
        return $result;
    };
}

$data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

$process = pipeline(
    function($arr) { return array_filter($arr, fn($n) => $n % 2 === 0); },
    function($arr) { return array_map(fn($n) => $n * $n, $arr); },
    function($arr) { return array_sum($arr); }
);

$result = $process($data);
echo "Pipeline result: $result\n\n";

// Example 2: Event System
echo "Example 2: Event System\n";
class EventEmitter {
    private $listeners = [];
    
    public function on($event, $listener) {
        $this->listeners[$event][] = $listener;
    }
    
    public function emit($event, ...$args) {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                $listener(...$args);
            }
        }
    }
}

$emitter = new EventEmitter();

$emitter->on('user.login', function($username) {
    echo "User $username logged in\n";
});

$emitter->on('user.login', function($username) {
    echo "Sending welcome email to $username\n";
});

$emitter->emit('user.login', 'john_doe');
echo "\n";

// Example 3: Validation Chain
echo "Example 3: Validation Chain\n";
class Validator {
    private $rules = [];
    
    public function rule($name, $callback) {
        $this->rules[$name] = $callback;
        return $this;
    }
    
    public function validate($data) {
        $errors = [];
        
        foreach ($this->rules as $field => $rule) {
            if (!$rule($data[$field] ?? null)) {
                $errors[] = "Field $field is invalid";
            }
        }
        
        return $errors;
    }
}

$validator = new Validator();
$validator
    ->rule('name', fn($value) => !empty($value) && strlen($value) >= 2)
    ->rule('email', fn($value) => filter_var($value, FILTER_VALIDATE_EMAIL))
    ->rule('age', fn($value) => is_numeric($value) && $value >= 18);

$userData = [
    'name' => 'John',
    'email' => 'john@example.com',
    'age' => 25
];

$errors = $validator->validate($userData);
echo empty($errors) ? "Validation passed\n" : "Validation errors: " . implode(", ", $errors) . "\n\n";

// Example 4: Function Decorator
echo "Example 4: Function Decorator\n";
function timer($function) {
    return function(...$args) use ($function) {
        $start = microtime(true);
        $result = $function(...$args);
        $end = microtime(true);
        
        echo "Execution time: " . round(($end - $start) * 1000, 2) . "ms\n";
        return $result;
    };
}

function slowOperation() {
    usleep(100000); // 100ms delay
    return "Operation completed";
}

$timedOperation = timer('slowOperation');
echo $timedOperation() . "\n\n";

// Example 5: Lazy Evaluation
echo "Example 5: Lazy Evaluation\n";
class LazyValue {
    private $factory;
    private $value = null;
    private $computed = false;
    
    public function __construct($factory) {
        $this->factory = $factory;
    }
    
    public function getValue() {
        if (!$this->computed) {
            $this->value = ($this->factory)();
            $this->computed = true;
        }
        return $this->value;
    }
}

$expensiveValue = new LazyValue(function() {
    echo "Computing expensive value...\n";
    usleep(50000); // 50ms delay
    return "Expensive Result";
});

echo "Lazy value created (not computed yet)\n";
echo "Getting value: " . $expensiveValue->getValue() . "\n";
echo "Getting value again: " . $expensiveValue->getValue() . "\n";
echo "(Second call was cached)\n\n";

echo "=== End of Advanced Functions ===\n";
?>
