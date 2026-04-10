# PHP Arrays and Collections

## Array Basics

### Creating Arrays
```php
<?php
// Indexed arrays
$fruits = ["apple", "banana", "cherry"];
$numbers = [1, 2, 3, 4, 5];
$mixed = ["hello", 42, true, 3.14, null];

// Alternative syntax
$fruits_alt = array("apple", "banana", "cherry");

// Associative arrays
$user = [
    "name" => "John",
    "age" => 30,
    "email" => "john@example.com"
];

$user_alt = array(
    "name" => "John",
    "age" => 30,
    "email" => "john@example.com"
);

// Mixed keys
$mixed_keys = [
    0 => "zero",
    "one" => 1,
    2 => "two",
    "three" => 3
];

// Empty arrays
$empty_array = [];
$empty_assoc = [];

// Array with range
$range_1_to_10 = range(1, 10);
$range_a_to_z = range('a', 'z');

// Array with step
$even_numbers = range(0, 10, 2);
$reverse_range = range(10, 1, -1);
?>
```

### Accessing Array Elements
```php
<?php
// Accessing indexed arrays
$fruits = ["apple", "banana", "cherry"];
echo $fruits[0]; // "apple"
echo $fruits[1]; // "banana"
echo $fruits[2]; // "cherry"

// Accessing associative arrays
$user = ["name" => "John", "age" => 30, "email" => "john@example.com"];
echo $user["name"]; // "John"
echo $user["age"]; // 30
echo $user["email"]; // "john@example.com"

// Dynamic key access
$key = "name";
echo $user[$key]; // "John"

// Checking if key exists
if (isset($user["name"])) {
    echo "Name exists";
}

// Accessing with default value
echo $user["phone"] ?? "Not provided"; // "Not provided"
echo $user["phone"] ?: "Not provided"; // "Not provided" (different behavior with null)

// Accessing nested arrays
$users = [
    ["name" => "John", "age" => 30],
    ["name" => "Jane", "age" => 25]
];

echo $users[0]["name"]; // "John"
echo $users[1]["age"]; // 25

// Multidimensional arrays
$matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

echo $matrix[1][2]; // 6
echo $matrix[2][0]; // 7
?>
```

### Modifying Arrays
```php
<?php
// Adding elements to indexed arrays
$fruits = ["apple", "banana"];
$fruits[] = "cherry"; // Add to end
array_push($fruits, "orange"); // Alternative method
$fruits[5] = "grape"; // Add at specific index

// Adding elements to associative arrays
$user = ["name" => "John"];
$user["age"] = 30;
$user["email"] = "john@example.com";

// Modifying existing elements
$fruits[0] = "avocado";
$user["name"] = "Jane";

// Removing elements
unset($fruits[1]); // Remove banana
unset($user["age"]); // Remove age

// Using array functions to modify
$numbers = [1, 2, 3, 4, 5];
array_push($numbers, 6); // Add to end
$last = array_pop($numbers); // Remove from end
$first = array_shift($numbers); // Remove from beginning
array_unshift($numbers, 0); // Add to beginning

// Spreading arrays (PHP 7.4+)
$fruits = ["apple", "banana"];
$more_fruits = ["cherry", "date"];
$all_fruits = [...$fruits, ...$more_fruits];

// Merging arrays
$array1 = [1, 2, 3];
$array2 = [4, 5, 6];
$merged = $array1 + $array2; // [1, 2, 3, 4, 5, 6]

// Note: + operator keeps keys from first array for duplicates
$assoc1 = ["a" => 1, "b" => 2];
$assoc2 = ["b" => 3, "c" => 4];
$merged_assoc = $assoc1 + $assoc2; // ["a" => 1, "b" => 2, "c" => 4]
?>
```

## Array Functions

### Basic Array Functions
```php
<?php
// Counting elements
$fruits = ["apple", "banana", "cherry"];
echo count($fruits); // 3
echo sizeof($fruits); // 3 (alias of count)

// Checking if array is empty
$empty_array = [];
echo empty($empty_array); // true
echo empty($fruits); // false

// Checking if value exists
echo in_array("apple", $fruits); // true
echo in_array("orange", $fruits); // false

// Checking if key exists
$user = ["name" => "John", "age" => 30];
echo array_key_exists("name", $user); // true
echo array_key_exists("email", $user); // false

// Getting all keys and values
$keys = array_keys($user); // ["name", "age"]
$values = array_values($user); // ["John", 30]

// Flipping keys and values
$flipped = array_flip($user); // ["John" => "name", 30 => "age"]

// Reversing array
$reversed = array_reverse($fruits); // ["cherry", "banana", "apple"]
$reversed_assoc = array_reverse($user, true); // ["age" => 30, "name" => "John"]

// Shuffling array
$shuffled = $fruits;
shuffle($shuffled); // Random order

// Unique values
$duplicates = [1, 2, 2, 3, 3, 3];
$unique = array_unique($duplicates); // [1, 2, 3]

// Array chunking
$numbers = [1, 2, 3, 4, 5, 6];
$chunks = array_chunk($numbers, 2); // [[1, 2], [3, 4], [5, 6]]

// Array slice
$fruits = ["apple", "banana", "cherry", "date", "elderberry"];
$slice = array_slice($fruits, 1, 3); // ["banana", "cherry", "date"]

// Array pad
$padded = array_pad($numbers, 8, 0); // [1, 2, 3, 4, 5, 6, 0, 0]
?>
```

### Array Searching and Filtering
```php
<?php
// Array search
$fruits = ["apple", "banana", "cherry"];
$index = array_search("banana", $fruits); // 1
$index = array_search("orange", $fruits); // false

// Filter array
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
$evens = array_filter($numbers, function($number) {
    return $number % 2 === 0;
}); // [2, 4, 6, 8, 10]

// Filter with key preservation
$filtered = array_filter($numbers, function($number) {
    return $number > 5;
}, ARRAY_FILTER_USE_KEY);

// Map array
$doubled = array_map(function($number) {
    return $number * 2;
}, $numbers); // [2, 4, 6, 8, 10, 12, 14, 16, 18, 20]

// Map with keys
$users = [
    ["name" => "John", "age" => 30],
    ["name" => "Jane", "age" => 25]
];
$names = array_map(function($user) {
    return $user["name"];
}, $users); // ["John", "Jane"]

// Walk array (modify array by reference)
$numbers = [1, 2, 3, 4, 5];
array_walk($numbers, function(&$value, $key) {
    $value = $value * 2;
});
// $numbers is now [2, 4, 6, 8, 10]

// Reduce array
$numbers = [1, 2, 3, 4, 5];
$sum = array_reduce($numbers, function($carry, $item) {
    return $carry + $item;
}, 0); // 15

// Product of all numbers
$product = array_reduce($numbers, function($carry, $item) {
    return $carry * $item;
}, 1); // 120

// Intersection of arrays
$array1 = [1, 2, 3, 4, 5];
$array2 = [3, 4, 5, 6, 7];
$intersection = array_intersect($array1, $array2); // [3, 4, 5]

// Difference of arrays
$difference = array_diff($array1, $array2); // [1, 2]

// Associative array intersection
$assoc1 = ["a" => 1, "b" => 2, "c" => 3];
$assoc2 = ["b" => 2, "c" => 3, "d" => 4];
$assoc_intersection = array_intersect_assoc($assoc1, $assoc2); // ["b" => 2, "c" => 3]

// Column extraction
$users = [
    ["name" => "John", "age" => 30, "email" => "john@example.com"],
    ["name" => "Jane", "age" => 25, "email" => "jane@example.com"]
];
$names = array_column($users, "name"); // ["John", "Jane"]
$ages = array_column($users, "age", "name"); // ["John" => 30, "Jane" => 25]
?>
```

### Sorting Arrays
```php
<?php
// Sort indexed array
$numbers = [3, 1, 4, 2, 5];
sort($numbers); // [1, 2, 3, 4, 5]
rsort($numbers); // [5, 4, 3, 2, 1]

// Sort associative array by value
$user = ["name" => "John", "age" => 30, "email" => "john@example.com"];
asort($user); // ["age" => 30, "email" => "john@example.com", "name" => "John"]
arsort($user); // ["name" => "John", "email" => "john@example.com", "age" => 30]

// Sort associative array by key
ksort($user); // ["age" => 30, "email" => "john@example.com", "name" => "John"]
krsort($user); // ["name" => "John", "email" => "john@example.com", "age" => 30]

// Custom sort
$fruits = ["apple", "banana", "cherry", "date"];
usort($fruits, function($a, $b) {
    return strlen($a) <=> strlen($b);
}); // Sort by string length

// Sort with custom comparison
$users = [
    ["name" => "John", "age" => 30],
    ["name" => "Jane", "age" => 25],
    ["name" => "Bob", "age" => 35]
];

usort($users, function($a, $b) {
    return $a["age"] <=> $b["age"];
}); // Sort by age

// Multidimensional array sort
$data = [
    ["name" => "John", "score" => 85],
    ["name" => "Jane", "score" => 92],
    ["name" => "Bob", "score" => 78]
];

array_multisort($scores = [], $names = [], $data);
// Sort by score first, then by name

// Natural sorting
$files = ["file1.txt", "file10.txt", "file2.txt"];
natsort($files); // ["file1.txt", "file2.txt", "file10.txt"]

// Case-insensitive sorting
$strings = ["Apple", "banana", "Cherry"];
natcasesort($strings); // ["Apple", "banana", "Cherry"]

// Sorting with array_multisort
$students = [
    ["name" => "John", "grade" => 85, "age" => 20],
    ["name" => "Jane", "grade" => 92, "age" => 19],
    ["name" => "Bob", "grade" => 78, "age" => 21]
];

$grades = [];
$names = [];
$ages = [];

array_multisort($grades, SORT_DESC, $names, SORT_ASC, $ages, SORT_ASC, $students);
?>
```

## Array Iteration

### Basic Iteration
```php
<?php
// For loop with indexed array
$fruits = ["apple", "banana", "cherry"];
for ($i = 0; $i < count($fruits); $i++) {
    echo "Fruit $i: {$fruits[$i]}\n";
}

// For loop with associative array
$user = ["name" => "John", "age" => 30, "email" => "john@example.com"];
$keys = array_keys($user);
for ($i = 0; $i < count($keys); $i++) {
    $key = $keys[$i];
    echo "$key: {$user[$key]}\n";
}

// Foreach loop with indexed array
foreach ($fruits as $fruit) {
    echo "Fruit: $fruit\n";
}

// Foreach loop with key and value
foreach ($user as $key => $value) {
    echo "$key: $value\n";
}

// Foreach loop by reference
$numbers = [1, 2, 3, 4, 5];
foreach ($numbers as &$number) {
    $number *= 2;
}
unset($number); // Important: unset reference after loop

// Nested iteration
$matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

foreach ($matrix as $row) {
    foreach ($row as $cell) {
        echo "$cell ";
    }
    echo "\n";
}

// Iteration with index
$fruits = ["apple", "banana", "cherry"];
foreach ($fruits as $index => $fruit) {
    echo "Index $index: $fruit\n";
}

// Iteration over multidimensional array
$users = [
    ["name" => "John", "hobbies" => ["reading", "swimming"]],
    ["name" => "Jane", "hobbies" => ["coding", "gaming"]]
];

foreach ($users as $user) {
    echo "User: {$user['name']}\n";
    echo "Hobbies:\n";
    foreach ($user['hobbies'] as $hobby) {
        echo "  - $hobby\n";
    }
}
?>
```

### Iterator Functions
```php
<?php
// Array Iterator class
$fruits = ["apple", "banana", "cherry"];
$iterator = new ArrayIterator($fruits);

foreach ($iterator as $key => $value) {
    echo "$key: $value\n";
}

// Using iterator functions
function processArray(array $array, callable $callback) {
    foreach ($array as $key => $value) {
        $callback($key, $value);
    }
}

processArray($fruits, function($key, $value) {
    echo "$key: $value\n";
});

// Filter with iterator
function filterArray(array $array, callable $predicate): array {
    $result = [];
    foreach ($array as $key => $value) {
        if ($predicate($value)) {
            $result[$key] = $value;
        }
    }
    return $result;
}

$evens = filterArray([1, 2, 3, 4, 5], function($value) {
    return $value % 2 === 0;
});

// Map with iterator
function mapArray(array $array, callable $transformer): array {
    $result = [];
    foreach ($array as $key => $value) {
        $result[$key] = $transformer($value);
    }
    return $result;
}

$doubled = mapArray([1, 2, 3, 4, 5], function($value) {
    return $value * 2;
});

// Custom iterator class
class NumberIterator implements Iterator {
    private array $numbers;
    private int $position = 0;
    
    public function __construct(array $numbers) {
        $this->numbers = $numbers;
    }
    
    public function current(): mixed {
        return $this->numbers[$this->position];
    }
    
    public function key(): mixed {
        return $this->position;
    }
    
    public function next(): void {
        $this->position++;
    }
    
    public function rewind(): void {
        $this->position = 0;
    }
    
    public function valid(): bool {
        return isset($this->numbers[$this->position]);
    }
}

$iterator = new NumberIterator([1, 2, 3, 4, 5]);
foreach ($iterator as $key => $value) {
    echo "$key: $value\n";
}
?>
```

### Generator Functions
```php
<?php
// Simple generator
function countTo($max) {
    for ($i = 1; $i <= $max; $i++) {
        yield $i;
    }
}

foreach (countTo(5) as $number) {
    echo "Number: $number\n";
}

// Generator with keys
function rangeWithKeys($start, $end) {
    for ($i = $start; $i <= $end; $i++) {
        yield $i => $i * $i;
    }
}

foreach (rangeWithKeys(1, 5) as $key => $value) {
    echo "$key squared is $value\n";
}

// Generator for file processing
function readLines($filename) {
    $file = fopen($filename, 'r');
    
    if (!$file) {
        return;
    }
    
    while (($line = fgets($file)) !== false) {
        yield trim($line);
    }
    
    fclose($file);
}

foreach (readLines('example.txt') as $line) {
    echo "Line: $line\n";
}

// Generator with filtering
function evenNumbers($max) {
    for ($i = 1; $i <= $max; $i++) {
        if ($i % 2 === 0) {
            yield $i;
        }
    }
}

foreach (evenNumbers(10) as $even) {
    echo "Even: $even\n";
}

// Generator with return value (PHP 7.0+)
function findFirstEven(array $numbers) {
    foreach ($numbers as $number) {
        if ($number % 2 === 0) {
            yield $number;
            return $number;
        }
    }
}

$generator = findFirstEven([1, 3, 5, 7, 8, 9, 10]);
foreach ($generator as $number) {
    echo "First even: $number\n";
}

$returnValue = $generator->getReturn();
echo "Return value: $returnValue\n";

// Generator delegation (PHP 7.0+)
function combineGenerators() {
    yield from countTo(3);
    yield from countTo(6);
}

foreach (combineGenerators() as $number) {
    echo "Combined: $number\n";
}
?>
```

## Array Manipulation

### Array Operations
```php
<?php
// Array union
$array1 = [1, 2, 3];
$array2 = [3, 4, 5];
$union = $array1 + $array2; // [1, 2, 3, 4, 5] (keeps keys from first array)

// Array merge (numeric keys reindexed)
$merged = array_merge($array1, $array2); // [1, 2, 3, 3, 4, 5]

// Array merge recursive (associative arrays)
$config1 = ["db" => ["host" => "localhost", "port" => 3306]];
$config2 = ["db" => ["username" => "root", "password" => "secret"]];
$merged_config = array_merge_recursive($config1, $config2);
// ["db" => ["host" => "localhost", "port" => 3306, "username" => "root", "password" => "secret"]]

// Replace array elements
$original = ["a", "b", "c"];
$replacement = [1, 2, 3];
$replaced = array_replace($original, $replacement); // [1, 2, 3]

// Replace recursively
$base = ["a" => ["x", "y"], "b" => ["z"]];
$addition = ["a" => ["y", "w"], "c" => ["u", "v"]];
$replaced_recursive = array_replace_recursive($base, $addition);
// ["a" => ["x", "y", "w"], "b" => ["z"], "c" => ["u", "v"]]

// Intersect array values
$array1 = [1, 2, 3, 4, 5];
$array2 = [3, 4, 5, 6, 7];
$intersect = array_intersect($array1, $array2); // [3, 4, 5]

// Intersect with keys
$assoc1 = ["a" => 1, "b" => 2, "c" => 3];
$assoc2 = ["b" => 2, "c" => 3, "d" => 4];
$intersect_assoc = array_intersect_assoc($assoc1, $assoc2); // ["b" => 2, "c" => 3]

// Difference array values
$difference = array_diff($array1, $array2); // [1, 2]

// Difference with keys
$diff_assoc = array_diff_assoc($assoc1, $assoc2); // ["a" => 1]

// Unique keys
$assoc1 = ["a" => 1, "b" => 2];
$assoc2 = ["b" => 3, "c" => 4];
$unique_keys = array_intersect_key($assoc1, $assoc2); // ["b" => 2]

// Unique values with keys
$unique_assoc = array_intersect_uassoc($assoc1, $assoc2, function($a, $b) {
    return $a === $b;
});
?>
```

### Array Transformation
```php
<?php
// Transform array values
$numbers = [1, 2, 3, 4, 5];
$squared = array_map(function($n) {
    return $n * $n;
}, $numbers); // [1, 4, 9, 16, 25]

// Transform with keys
$users = ["user1" => ["name" => "John"], "user2" => ["name" => "Jane"]];
$names = array_map(function($user) {
    return $user["name"];
}, $users); // ["user1" => "John", "user2" => "Jane"]

// Transform both keys and values
$assoc = ["a" => 1, "b" => 2, "c" => 3];
$transformed = array_map(function($value, $key) {
    return strtoupper($key) . $value;
}, $assoc, array_keys($assoc));
// ["A" => "1", "B" => "2", "C" => "3"]

// Filter array
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
$evens = array_filter($numbers, function($n) {
    return $n % 2 === 0;
}); // [2, 4, 6, 8, 10]

// Filter with key preservation
$filtered = array_filter($numbers, function($n) {
    return $n > 5;
}, ARRAY_FILTER_USE_KEY);

// Reduce array
$numbers = [1, 2, 3, 4, 5];
$sum = array_reduce($numbers, function($carry, $item) {
    return $carry + $item;
}, 0); // 15

// Product with initial value
$product = array_reduce($numbers, function($carry, $item) {
    return $carry * $item;
}, 1); // 120

// Custom reduce with keys
$assoc = ["a" => 1, "b" => 2, "c" => 3];
$concatenated = array_reduce($assoc, function($carry, $item, $key) {
    return $carry . $key . $item;
}, ""); // "a1b2c3"

// Walk array (modify by reference)
$numbers = [1, 2, 3, 4, 5];
array_walk($numbers, function(&$value, $key) {
    $value = $value * 2;
});
// $numbers is now [2, 4, 6, 8, 10]

// Walk with keys
$user = ["name" => "John", "age" => 30];
array_walk($user, function($value, $key) {
    echo "$key: $value\n";
});

// Recursive array transformation
function transformArrayRecursive(array $array, callable $transformer): array {
    $result = [];
    
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $result[$key] = transformArrayRecursive($value, $transformer);
        } else {
            $result[$key] = $transformer($value);
        }
    }
    
    return $result;
}

$nested = ["a" => [1, 2, 3], "b" => [4, 5, 6]];
$transformed = transformArrayRecursive($nested, function($value) {
    return $value * 2;
});
// ["a" => [2, 4, 6], "b" => [8, 10, 12]]
?>
```

## Array Utilities

### Validation and Checking
```php
<?php
// Check if array contains only numeric values
function isNumericArray(array $array): bool {
    return array_reduce($array, function($carry, $item) {
        return $carry && is_numeric($item);
    }, true);
}

$numeric_array = [1, 2, 3, 4.5];
$non_numeric_array = [1, 2, "three", 4];

echo isNumericArray($numeric_array); // true
echo isNumericArray($non_numeric_array); // false

// Check if array is associative
function isAssociativeArray(array $array): bool {
    return array_keys($array) !== range(0, count($array) - 1);
}

$indexed = [1, 2, 3];
$assoc = ["a" => 1, "b" => 2];

echo isAssociativeArray($indexed); // false
echo isAssociativeArray($assoc); // true

// Check if array is multidimensional
function isMultidimensionalArray(array $array): bool {
    foreach ($array as $value) {
        if (is_array($value)) {
            return true;
        }
    }
    return false;
}

$flat = [1, 2, 3];
$nested = [1, [2, 3], 4];

echo isMultidimensionalArray($flat); // false
echo isMultidimensionalArray($nested); // true

// Validate array structure
function validateUserArray(array $user): array {
    $errors = [];
    
    if (!isset($user['name']) || !is_string($user['name'])) {
        $errors[] = "Name is required and must be a string";
    }
    
    if (!isset($user['age']) || !is_int($user['age'])) {
        $errors[] = "Age is required and must be an integer";
    }
    
    if (isset($user['email']) && !filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email must be valid";
    }
    
    return $errors;
}

$user_data = ["name" => "John", "age" => 30, "email" => "invalid"];
$errors = validateUserArray($user_data);

if (!empty($errors)) {
    echo "Validation errors:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}

// Check array depth
function getArrayDepth(array $array): int {
    if (empty($array)) {
        return 0;
    }
    
    $maxDepth = 1;
    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = getArrayDepth($value) + 1;
            if ($depth > $maxDepth) {
                $maxDepth = $depth;
            }
        }
    }
    
    return $maxDepth;
}

$deep = [1, [2, [3, [4]], 5], 6];
echo getArrayDepth($deep); // 4
?>
```

### Array Helpers
```php
<?php
// Convert array to string
function arrayToString(array $array, string $separator = ", "): string {
    return implode($separator, $array);
}

$fruits = ["apple", "banana", "cherry"];
echo arrayToString($fruits); // "apple, banana, cherry"

// Convert string to array
function stringToArray(string $string, string $separator = ", "): array {
    return explode($separator, $string);
}

$csv = "apple,banana,cherry";
$fruits = stringToArray($csv); // ["apple", "banana", "cherry"]

// Convert array to JSON
function arrayToJson(array $array): string {
    return json_encode($array, JSON_PRETTY_PRINT);
}

$user = ["name" => "John", "age" => 30];
echo arrayToJson($user);

// Convert JSON to array
function jsonToArray(string $json): array {
    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : [];
}

$json = '{"name":"John","age":30}';
$user = jsonToArray($json);

// Flatten multidimensional array
function flattenArray(array $array): array {
    $result = [];
    
    foreach ($array as $value) {
        if (is_array($value)) {
            $result = array_merge($result, flattenArray($value));
        } else {
            $result[] = $value;
        }
    }
    
    return $result;
}

$nested = [1, [2, [3, 4]], 5];
echo implode(", ", flattenArray($nested)); // "1, 2, 3, 4, 5"

// Group array by key
function groupArrayBy(array $array, string $key): array {
    $grouped = [];
    
    foreach ($array as $item) {
        $groupKey = $item[$key];
        $grouped[$groupKey][] = $item;
    }
    
    return $grouped;
}

$users = [
    ["name" => "John", "department" => "IT"],
    ["name" => "Jane", "department" => "IT"],
    ["name" => "Bob", "department" => "HR"]
];
$grouped = groupArrayBy($users, "department");

// Extract column from array of arrays
function extractColumn(array $array, string $column): array {
    return array_column($array, $column);
}

$names = extractColumn($users, "name"); // ["John", "Jane", "Bob"]

// Array to object conversion
function arrayToObject(array $array): object {
    return (object)$array;
}

$user_array = ["name" => "John", "age" => 30];
$user_object = arrayToObject($user_array);
echo $user_object->name; // "John"

// Object to array conversion
function objectToArray(object $object): array {
    return (array)$object;
}

$user_back = objectToArray($user_object);
echo $user_back["name"]; // "John"

// Remove empty values from array
function removeEmptyValues(array $array): array {
    return array_filter($array, function($value) {
        return !empty($value);
    });
}

$with_empty = ["name" => "John", "age" => 30, "email" => "", "phone" => null];
$clean = removeEmptyValues($with_empty); // ["name" => "John", "age" => 30]

// Recursively remove empty values
function removeEmptyValuesRecursive(array $array): array {
    $result = [];
    
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $cleaned = removeEmptyValuesRecursive($value);
            if (!empty($cleaned)) {
                $result[$key] = $cleaned;
            }
        } elseif (!empty($value)) {
            $result[$key] = $value;
        }
    }
    
    return $result;
}
?>
```

## Performance Considerations

### Memory-Efficient Array Operations
```php
<?php
// Use generators for large datasets
function processLargeDataset(): Generator {
    for ($i = 0; $i < 1000000; $i++) {
        yield $i;
    }
}

foreach (processLargeDataset() as $number) {
    // Process number without loading all into memory
}

// Use references for large array modifications
function modifyLargeArray(array &$array): void {
    foreach ($array as &$value) {
        $value = strtoupper($value);
    }
}

// Avoid unnecessary array copies
function processArray(array $array): array {
    // This creates a copy
    $result = array_map('strtoupper', $array);
    return $result;
}

function processArrayByReference(array &$array): void {
    // This modifies the original array
    foreach ($array as &$value) {
        $value = strtoupper($value);
    }
}

// Use array functions instead of loops when possible
$numbers = [1, 2, 3, 4, 5];
$sum = array_sum($numbers); // Faster than manual loop
$average = array_sum($numbers) / count($numbers);

// Pre-allocate array size when possible
$large_array = array_fill(0, 1000000, 0);

// Use SplFixedArray for fixed-size arrays
$fixed_array = new SplFixedArray(1000);
for ($i = 0; $i < 1000; $i++) {
    $fixed_array[$i] = $i * 2;
}

// Use array_intersect for common elements
$array1 = range(1, 1000);
$array2 = range(500, 1500);
$common = array_intersect($array1, $array2); // More efficient than manual comparison

// Use array_key_exists instead of isset for dynamic keys
$key = "dynamic_key";
if (array_key_exists($key, $array)) {
    echo "Key exists";
}

// Use in_array with strict comparison when appropriate
if (in_array($value, $array, true)) {
    echo "Value found with strict comparison";
}
?>
```

### Benchmarking Arrays
```php
<?php
// Benchmark array operations
function benchmarkArrayOperations() {
    $iterations = 10000;
    
    // Benchmark array creation
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $array = range(1, 1000);
    }
    $end = microtime(true);
    echo "Array creation: " . ($end - $start) . " seconds\n";
    
    // Benchmark array iteration
    $array = range(1, 1000);
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $sum = 0;
        foreach ($array as $value) {
            $sum += $value;
        }
    }
    $end = microtime(true);
    echo "Array iteration: " . ($end - $start) . " seconds\n";
    
    // Benchmark array_map
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $doubled = array_map(function($n) { return $n * 2; }, $array);
    }
    $end = microtime(true);
    echo "Array map: " . ($end - $start) . " seconds\n";
    
    // Benchmark array_filter
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $evens = array_filter($array, function($n) { return $n % 2 === 0; });
    }
    $end = microtime(true);
    echo "Array filter: " . ($end - $start) . " seconds\n";
}

// Memory usage comparison
function compareMemoryUsage() {
    $memory_before = memory_get_usage();
    
    // Large array
    $large_array = range(1, 1000000);
    
    $memory_after = memory_get_usage();
    $used = $memory_after - $memory_before;
    
    echo "Memory used by large array: " . ($used / 1024 / 1024) . " MB\n";
    
    unset($large_array);
    
    // Generator
    $generator = function() {
        for ($i = 0; $i < 1000000; $i++) {
            yield $i;
        }
    };
    
    foreach ($generator() as $value) {
        // Process value
        if ($value > 100) break; // Limit for demonstration
    }
    
    $memory_after_generator = memory_get_usage();
    $used_generator = $memory_after_generator - $memory_after;
    
    echo "Memory used by generator: " . ($used_generator / 1024) . " KB\n";
}

// Test different array types
function testArrayTypes() {
    $iterations = 10000;
    
    // Indexed array
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $array = [];
        for ($j = 0; $j < 100; $j++) {
            $array[] = $j;
        }
    }
    $end = microtime(true);
    echo "Indexed array creation: " . ($end - $start) . " seconds\n";
    
    // Associative array
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $array = [];
        for ($j = 0; $j < 100; $j++) {
            $array["key_$j"] = $j;
        }
    }
    $end = microtime(true);
    echo "Associative array creation: " . ($end - $start) . " seconds\n";
    
    // SplFixedArray
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $array = new SplFixedArray(100);
        for ($j = 0; $j < 100; $j++) {
            $array[$j] = $j;
        }
    }
    $end = microtime(true);
    echo "SplFixedArray creation: " . ($end - $start) . " seconds\n";
}
?>
```

## Best Practices

### Array Usage Best Practices
```php
<?php
// Use descriptive array keys
$user = [
    "first_name" => "John",
    "last_name" => "Doe",
    "email_address" => "john@example.com"
];

// Use constants for array keys
class UserFields {
    const NAME = "name";
    const EMAIL = "email";
    const AGE = "age";
}

$user = [
    UserFields::NAME => "John",
    UserFields::EMAIL => "john@example.com",
    UserFields::AGE => 30
];

// Validate array structure before use
function validateUser(array $user): bool {
    $required = ["name", "email", "age"];
    
    foreach ($required as $field) {
        if (!isset($user[$field]) || empty($user[$field])) {
            return false;
        }
    }
    
    return true;
}

// Use type hints for array parameters
function processUser(array $user): string {
    if (!validateUser($user)) {
        throw new InvalidArgumentException("Invalid user data");
    }
    
    return "User: {$user['name']} ({$user['email']})";
}

// Use array destructuring (PHP 7.1+)
function extractUserInfo(array $user): void {
    ["name" => $name, "email" => $email, "age" => $age] = $user;
    
    echo "Name: $name, Email: $email, Age: $age\n";
}

// Use null coalescing for defaults
function getUserField(array $user, string $field, $default = null) {
    return $user[$field] ?? $default;
}

// Use array spread for combining arrays
function combineArrays(array ...$arrays): array {
    $result = [];
    
    foreach ($arrays as $array) {
        $result = [...$result, ...$array];
    }
    
    return $result;
}

// Use array_column for extracting data
function getUserNames(array $users): array {
    return array_column($users, "name");
}

// Use array_filter for cleaning data
function cleanArray(array $data): array {
    return array_filter($data, function($value) {
        return $value !== null && $value !== "" && $value !== false;
    });
}

// Use array_map for transformations
function uppercaseNames(array $users): array {
    return array_map(function($user) {
        $user["name"] = strtoupper($user["name"]);
        return $user;
    }, $users);
}

// Use array_reduce for aggregations
function calculateTotal(array $items): float {
    return array_reduce($items, function($total, $item) {
        return $total + ($item["price"] * $item["quantity"]);
    }, 0.0);
}
?>
```

### Security Considerations
```php
<?php
// Sanitize array data
function sanitizeArray(array $data): array {
    return array_map(function($value) {
        if (is_string($value)) {
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        return $value;
    }, $data);
}

// Validate array structure
function validateInputArray(array $input, array $schema): array {
    $errors = [];
    
    foreach ($schema as $key => $rules) {
        if (isset($rules['required']) && $rules['required'] && !isset($input[$key])) {
            $errors[] = "Required field '$key' is missing";
        }
        
        if (isset($input[$key])) {
            $value = $input[$key];
            
            if (isset($rules['type']) && gettype($value) !== $rules['type']) {
                $errors[] = "Field '$key' must be of type {$rules['type']}";
            }
            
            if (isset($rules['min']) && strlen($value) < $rules['min']) {
                $errors[] = "Field '$key' must be at least {$rules['min']} characters";
            }
            
            if (isset($rules['max']) && strlen($value) > $rules['max']) {
                $errors[] = "Field '$key' must be no more than {$rules['max']} characters";
            }
            
            if (isset($rules['pattern']) && !preg_match($rules['pattern'], $value)) {
                $errors[] = "Field '$key' format is invalid";
            }
        }
    }
    
    return $errors;
}

// Example schema
$schema = [
    "name" => [
        "required" => true,
        "type" => "string",
        "min" => 2,
        "max" => 50,
        "pattern" => "/^[a-zA-Z\s]+$/"
    ],
    "email" => [
        "required" => true,
        "type" => "string",
        "pattern" => "/^[^\w@\-\.]+\.@[a-zA-Z\-\.]+\.[a-zA-Z]{2,}$/"
    ],
    "age" => [
        "required" => false,
        "type" => "integer",
        "min" => 0,
        "max" => 150
    ]
];

$input = [
    "name" => "John Doe",
    "email" => "john@example.com",
    "age" => 30
];

$errors = validateInputArray($input, $schema);
if (!empty($errors)) {
    echo "Validation errors:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}

// Prevent injection in array keys
function sanitizeArrayKeys(array $array): array {
    $sanitized = [];
    
    foreach ($array as $key => $value) {
        $sanitized_key = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
        $sanitized[$sanitized_key] = $value;
    }
    
    return $sanitized;
}

// Limit array size
function limitArraySize(array $array, int $maxSize): array {
    return array_slice($array, 0, $maxSize);
}

// Validate array depth
function validateArrayDepth(array $array, int $maxDepth): bool {
    return getArrayDepth($array) <= $maxDepth;
}
?>
```

## Common Pitfalls

### Array Pitfalls
```php
<?php
// Pitfall: Array copy vs reference
function modifyArray(array $array): array {
    $array[] = "new item";
    return $array;
}

$original = [1, 2, 3];
$result = modifyArray($original);
echo $original[3]; // Notice: Undefined offset - original not modified

// Solution: Pass by reference
function modifyArrayByReference(array &$array): void {
    $array[] = "new item";
}

modifyArrayByReference($original);
echo $original[3]; // "new item"

// Pitfall: Modifying array during iteration
$numbers = [1, 2, 3, 4, 5];
foreach ($numbers as $key => $value) {
    if ($value % 2 == 0) {
        unset($numbers[$key]); // Can cause unexpected behavior
    }
}

// Solution: Create new array or use iterator
$numbers = [1, 2, 3, 4, 5];
$filtered = array_filter($numbers, function($n) {
    return $n % 2 !== 0;
});

// Pitfall: Array vs string confusion
$string = "hello";
$array = ["hello"];

echo is_array($string); // false
echo is_array($array); // true

// Pitfall: Using array functions on non-arrays
$not_array = "not an array";
// $count = count($not_array); // Warning: count(): Parameter must be an array or an object

// Solution: Check type first
if (is_array($not_array)) {
    $count = count($not_array);
}

// Pitfall: Array key type confusion
$array = [
    0 => "zero",
    "0" => "string zero",
    1 => "one",
    "1" => "string one"
];

echo $array[0]; // "zero"
echo $array["0"]; // "string zero" (different key!)

// Pitfall: Array merge with numeric keys
$array1 = [1, 2, 3];
$array2 = [4, 5, 6];
$merged = $array1 + $array2; // [1, 2, 3, 4, 5, 6]

$array1 = [1, 2, 3];
$array2 = [3, 4, 5];
$merged = $array1 + $array2; // [1, 2, 3, 4, 5] (key 3 from $array1 is kept)

// Solution: Use array_merge for numeric keys
$merged = array_merge($array1, $array2); // [1, 2, 3, 3, 4, 5]

// Pitfall: Forgetting to unset reference
foreach ($array as &$value) {
    $value = $value * 2;
}
// $value still references last element

// Solution: Always unset references
foreach ($array as &$value) {
    $value = $value * 2;
}
unset($value);

// Pitfall: Assuming array order
$array = ["a" => 1, "b" => 2, "c" => 3];
foreach ($array as $key => $value) {
    echo "$key: $value\n";
}

// Order is not guaranteed for associative arrays
// Use ksort() if order matters
ksort($array);
foreach ($array as $key => $value) {
    echo "$key: $value\n";
}

// Pitfall: Using in_array with loose comparison
if (in_array("0", [0, false, null])) {
    echo "Found!"; // Always true with loose comparison
}

// Solution: Use strict comparison when appropriate
if (in_array("0", [0, false, null], true)) {
    echo "Found exactly 0";
}
?>
```

### Performance Pitfalls
```php
<?php
// Pitfall: Creating large arrays in loops
function createLargeArrays() {
    $arrays = [];
    for ($i = 0; $i < 1000; $i++) {
        $arrays[] = range(1, 1000); // Very memory intensive
    }
    return $arrays;
}

// Solution: Use generators or process incrementally
function processLargeDataset(): Generator {
    for ($i = 0; $i < 1000; $i++) {
        yield range(1, 1000);
    }
}

// Pitfall: Using array functions in loops unnecessarily
function processItems(array $items) {
    $results = [];
    foreach ($items as $item) {
        $filtered = array_filter($item, function($value) {
            return $value > 0;
        });
        $results = array_merge($results, $filtered);
    }
    return $results;
}

// Solution: Process all at once
function processItemsBetter(array $items): array {
    $all_values = [];
    foreach ($items as $item) {
        $all_values = array_merge($all_values, $item);
    }
    return array_filter($all_values, function($value) {
        return $value > 0;
    });
}

// Pitfall: Not using built-in functions
function sumNumbers(array $numbers): int {
    $sum = 0;
    foreach ($numbers as $number) {
        $sum += $number;
    }
    return $sum;
}

// Solution: Use built-in functions
function sumNumbersBetter(array $numbers): int {
    return array_sum($numbers);
}

// Pitfall: Inefficient array searching
function findItem(array $array, $search) {
    foreach ($array as $item) {
        if ($item === $search) {
            return true;
        }
    }
    return false;
}

// Solution: Use in_array or array_search
function findItemBetter(array $array, $search): bool {
    return in_array($search, $array);
}

// Pitfall: Not using array keys for lookups
function getUserByName(array $users, string $name) {
    foreach ($users as $user) {
        if ($user['name'] === $name) {
            return $user;
        }
    }
    return null;
}

// Solution: Create lookup table
function createNameLookup(array $users): array {
    $lookup = [];
    foreach ($users as $user) {
        $lookup[$user['name']] = $user;
    }
    return $lookup;
}

function getUserByNameBetter(array $users, string $name) {
    static $lookup = null;
    if ($lookup === null) {
        $lookup = createNameLookup($users);
    }
    return $lookup[$name] ?? null;
}
?>
```

## Summary

PHP arrays provide:

**Array Types:**
- Indexed arrays (numeric keys)
- Associative arrays (string keys)
- Multidimensional arrays
- Mixed type arrays

**Array Creation:**
- Array literal syntax
- Range generation
- Dynamic creation
- Empty arrays

**Array Operations:**
- Adding and removing elements
- Modifying values
- Merging and combining
- Intersection and difference

**Array Functions:**
- Counting and checking
- Searching and filtering
- Mapping and transforming
- Sorting and ordering
- Reduction and aggregation

**Iteration Methods:**
- For loops
- Foreach loops
- Iterator classes
- Generator functions
- Custom iterators

**Advanced Features:**
- Array destructuring
- Array spreading
- Generator delegation
- Array iterators
- Memory-efficient processing

**Performance Considerations:**
- Memory usage optimization
- Generator usage for large datasets
- Built-in function efficiency
- Benchmarking techniques

**Best Practices:**
- Descriptive naming conventions
- Type hints and validation
- Security considerations
- Error handling patterns

**Common Pitfalls:**
- Reference vs copy confusion
- Modification during iteration
- Type comparison issues
- Performance bottlenecks

PHP's array system provides powerful and flexible data structures that are essential for web development, offering both simplicity for basic use cases and advanced features for complex data manipulation when following best practices.
