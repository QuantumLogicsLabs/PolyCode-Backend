<?php
/**
 * PHP Array Functions and Methods
 * 
 * Comprehensive guide to PHP array functions, methods, and array manipulation techniques.
 */

echo "=== PHP Array Functions and Methods ===\n\n";

// Basic Array Functions
echo "--- Basic Array Functions ---\n";

// array() and []
$fruits = array("Apple", "Banana", "Orange");
$vegetables = ["Carrot", "Broccoli", "Spinach"];

echo "Fruits: " . implode(", ", $fruits) . "\n";
echo "Vegetables: " . implode(", ", $vegetables) . "\n";

// count() and sizeof()
echo "Number of fruits: " . count($fruits) . "\n";
echo "Number of vegetables: " . sizeof($vegetables) . "\n";

// range()
$numbers = range(1, 10);
echo "Numbers 1-10: " . implode(", ", $numbers) . "\n";

$letters = range('a', 'e');
echo "Letters a-e: " . implode(", ", $letters) . "\n\n";

// Array Adding and Removing
echo "--- Array Adding and Removing ---\n";

$array = [1, 2, 3];

// array_push() and array_unshift()
array_push($array, 4, 5);
echo "After push: " . implode(", ", $array) . "\n";

array_unshift($array, 0);
echo "After unshift: " . implode(", ", $array) . "\n";

// array_pop() and array_shift()
$last = array_pop($array);
echo "Popped element: $last\n";
echo "After pop: " . implode(", ", $array) . "\n";

$first = array_shift($array);
echo "Shifted element: $first\n";
echo "After shift: " . implode(", ", $array) . "\n\n";

// Array Searching
echo "--- Array Searching ---\n";

$colors = ["red", "green", "blue", "yellow", "green"];

// in_array()
echo "Contains 'green': " . (in_array("green", $colors) ? "Yes" : "No") . "\n";
echo "Contains 'purple': " . (in_array("purple", $colors) ? "Yes" : "No") . "\n";

// array_search()
$position = array_search("blue", $colors);
echo "Position of 'blue': $position\n";

$position = array_search("green", $colors);
echo "First position of 'green': $position\n";

// array_keys()
$keys = array_keys($colors);
echo "All keys: " . implode(", ", $keys) . "\n";

// array_values()
$values = array_values($colors);
echo "All values: " . implode(", ", $values) . "\n\n";

// Array Sorting
echo "--- Array Sorting ---\n";

$numbers = [5, 2, 8, 1, 9, 3];

// sort() and rsort()
sort($numbers);
echo "Sorted ascending: " . implode(", ", $numbers) . "\n";

rsort($numbers);
echo "Sorted descending: " . implode(", ", $numbers) . "\n";

// asort() and arsort() (maintain key association)
$assoc = ["b" => 2, "a" => 1, "d" => 4, "c" => 3];
asort($assoc);
echo "Associative sorted (asort): ";
foreach ($assoc as $key => $value) {
    echo "$key=>$value ";
}
echo "\n";

// ksort() and krsort() (sort by keys)
ksort($assoc);
echo "Associative sorted by keys (ksort): ";
foreach ($assoc as $key => $value) {
    echo "$key=>$value ";
}
echo "\n\n";

// Array Filtering and Mapping
echo "--- Array Filtering and Mapping ---\n";

$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

// array_filter()
$even = array_filter($numbers, function($n) {
    return $n % 2 === 0;
});
echo "Even numbers: " . implode(", ", $even) . "\n";

// array_map()
$squared = array_map(function($n) {
    return $n * $n;
}, $numbers);
echo "Squared numbers: " . implode(", ", $squared) . "\n";

// array_walk()
array_walk($numbers, function(&$value, $key) {
    $value = "Item $key: $value";
});
echo "After array_walk: " . implode(", ", $numbers) . "\n\n";

// Array Reduction
echo "--- Array Reduction ---\n";

$numbers = [1, 2, 3, 4, 5];

// array_sum()
echo "Sum: " . array_sum($numbers) . "\n";

// array_product()
echo "Product: " . array_product($numbers) . "\n";

// array_reduce()
$sum = array_reduce($numbers, function($carry, $item) {
    return $carry + $item;
}, 0);
echo "Reduce sum: $sum\n";

$concatenated = array_reduce($numbers, function($carry, $item) {
    return $carry . $item;
}, "");
echo "Reduce concatenation: '$concatenated'\n\n";

// Array Slicing and Splicing
echo "--- Array Slicing and Splicing ---\n";

$array = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

// array_slice()
$slice = array_slice($array, 2, 4);
echo "Slice from index 2, 4 elements: " . implode(", ", $slice) . "\n";

$slice = array_slice($array, -5);
echo "Last 5 elements: " . implode(", ", $slice) . "\n";

// array_splice()
$removed = array_splice($array, 3, 2, [99, 100]);
echo "Removed elements: " . implode(", ", $removed) . "\n";
echo "Array after splice: " . implode(", ", $array) . "\n\n";

// Array Merging and Combining
echo "--- Array Merging and Combining ---\n";

$array1 = ["a", "b", "c"];
$array2 = ["d", "e", "f"];

// array_merge()
$merged = array_merge($array1, $array2);
echo "Merged: " . implode(", ", $merged) . "\n";

// array_combine()
$keys = ["first", "second", "third"];
$values = ["one", "two", "three"];
$combined = array_combine($keys, $values);
echo "Combined: ";
foreach ($combined as $key => $value) {
    echo "$key=>$value ";
}
echo "\n";

// array_intersect()
$common = array_intersect($array1, ["b", "c", "x"]);
echo "Intersection: " . implode(", ", $common) . "\n";

// array_diff()
$difference = array_diff($array1, ["b", "x"]);
echo "Difference: " . implode(", ", $difference) . "\n\n";

// Array Unique and Fill
echo "--- Array Unique and Fill ---\n";

$withDuplicates = [1, 2, 2, 3, 3, 3, 4, 4, 4, 4];

// array_unique()
$unique = array_unique($withDuplicates);
echo "Unique: " . implode(", ", $unique) . "\n";

// array_fill()
$filled = array_fill(0, 5, "test");
echo "Filled array: " . implode(", ", $filled) . "\n";

// array_pad()
$padded = array_pad([1, 2, 3], 5, 0);
echo "Padded to 5: " . implode(", ", $padded) . "\n\n";

// Array Flip and Reverse
echo "--- Array Flip and Reverse ---\n";

$assoc = ["a" => "apple", "b" => "banana", "c" => "cherry"];

// array_flip()
$flipped = array_flip($assoc);
echo "Flipped: ";
foreach ($flipped as $key => $value) {
    echo "$key=>$value ";
}
echo "\n";

// array_reverse()
$reversed = array_reverse([1, 2, 3, 4, 5]);
echo "Reversed: " . implode(", ", $reversed) . "\n\n";

// Array Chunk
echo "--- Array Chunk ---\n";

$array = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

$chunks = array_chunk($array, 3);
echo "Chunks of 3:\n";
foreach ($chunks as $index => $chunk) {
    echo "Chunk $index: " . implode(", ", $chunk) . "\n";
}
echo "\n";

// Array Column (for multidimensional arrays)
echo "--- Array Column ---\n";

$users = [
    ["id" => 1, "name" => "John", "email" => "john@example.com"],
    ["id" => 2, "name" => "Jane", "email" => "jane@example.com"],
    ["id" => 3, "name" => "Bob", "email" => "bob@example.com"]
];

$names = array_column($users, "name");
echo "Names: " . implode(", ", $names) . "\n";

$emails = array_column($users, "email", "id");
echo "Emails by ID: ";
foreach ($emails as $id => $email) {
    echo "$id=>$email ";
}
echo "\n\n";

// Array Key and Value Operations
echo "--- Array Key and Value Operations ---\n";

$assoc = ["name" => "John", "age" => 30, "city" => "New York"];

// array_key_exists()
echo "Key 'name' exists: " . (array_key_exists("name", $assoc) ? "Yes" : "No") . "\n";
echo "Key 'salary' exists: " . (array_key_exists("salary", $assoc) ? "Yes" : "No") . "\n";

// array_keys() with filter
$numericKeys = array_keys($assoc);
echo "All keys: " . implode(", ", $numericKeys) . "\n";

// array_values()
$values = array_values($assoc);
echo "All values: " . implode(", ", $values) . "\n\n";

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: Data Processing Pipeline
echo "Example 1: Data Processing Pipeline\n";
function processNumbers($numbers) {
    // Filter out odd numbers
    $even = array_filter($numbers, fn($n) => $n % 2 === 0);
    
    // Double each number
    $doubled = array_map(fn($n) => $n * 2, $even);
    
    // Sort in descending order
    rsort($doubled);
    
    // Take top 5
    $top5 = array_slice($doubled, 0, 5);
    
    return $top5;
}

$data = range(1, 20);
$processed = processNumbers($data);
echo "Processed data: " . implode(", ", $processed) . "\n\n";

// Example 2: Configuration Merger
echo "Example 2: Configuration Merger\n";
function mergeConfigs($default, $custom) {
    return array_replace_recursive($default, $custom);
}

$defaultConfig = [
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'charset' => 'utf8'
    ],
    'app' => [
        'name' => 'MyApp',
        'debug' => false,
        'version' => '1.0'
    ]
];

$customConfig = [
    'database' => [
        'host' => 'remote-server.com',
        'password' => 'secret'
    ],
    'app' => [
        'debug' => true,
        'version' => '2.0'
    ]
];

$merged = mergeConfigs($defaultConfig, $customConfig);
echo "Merged configuration:\n";
print_r($merged);
echo "\n";

// Example 3: Array Statistics
echo "Example 3: Array Statistics\n";
function calculateStats($numbers) {
    if (empty($numbers)) {
        return [];
    }
    
    $stats = [
        'count' => count($numbers),
        'sum' => array_sum($numbers),
        'average' => array_sum($numbers) / count($numbers),
        'min' => min($numbers),
        'max' => max($numbers)
    ];
    
    sort($numbers);
    $count = count($numbers);
    $middle = floor($count / 2);
    
    if ($count % 2 === 0) {
        $stats['median'] = ($numbers[$middle - 1] + $numbers[$middle]) / 2;
    } else {
        $stats['median'] = $numbers[$middle];
    }
    
    return $stats;
}

$testNumbers = [3, 1, 4, 1, 5, 9, 2, 6, 5, 3];
$statistics = calculateStats($testNumbers);

echo "Statistics for [" . implode(", ", $testNumbers) . "]:\n";
foreach ($statistics as $stat => $value) {
    echo ucfirst($stat) . ": " . (is_float($value) ? round($value, 2) : $value) . "\n";
}
echo "\n";

// Example 4: Array Group By
echo "Example 4: Array Group By\n";
function groupBy($array, $key) {
    $grouped = [];
    
    foreach ($array as $item) {
        $groupKey = $item[$key];
        $grouped[$groupKey][] = $item;
    }
    
    return $grouped;
}

$products = [
    ['name' => 'Laptop', 'category' => 'Electronics', 'price' => 999],
    ['name' => 'Mouse', 'category' => 'Electronics', 'price' => 25],
    ['name' => 'Book', 'category' => 'Books', 'price' => 15],
    ['name' => 'Pen', 'category' => 'Books', 'price' => 2],
    ['name' => 'Keyboard', 'category' => 'Electronics', 'price' => 75]
];

$grouped = groupBy($products, 'category');
echo "Products grouped by category:\n";
foreach ($grouped as $category => $items) {
    echo "$category:\n";
    foreach ($items as $item) {
        echo "  - {$item['name']}: \${$item['price']}\n";
    }
}
echo "\n";

// Example 5: Array Tree Operations
echo "Example 5: Array Tree Operations\n";
function buildTree($items, $parentId = 0) {
    $tree = [];
    
    foreach ($items as $item) {
        if ($item['parent_id'] === $parentId) {
            $children = buildTree($items, $item['id']);
            if (!empty($children)) {
                $item['children'] = $children;
            }
            $tree[] = $item;
        }
    }
    
    return $tree;
}

$menuItems = [
    ['id' => 1, 'title' => 'Home', 'parent_id' => 0],
    ['id' => 2, 'title' => 'Products', 'parent_id' => 0],
    ['id' => 3, 'title' => 'Electronics', 'parent_id' => 2],
    ['id' => 4, 'title' => 'Books', 'parent_id' => 2],
    ['id' => 5, 'title' => 'About', 'parent_id' => 0],
    ['id' => 6, 'title' => 'Contact', 'parent_id' => 0]
];

$menuTree = buildTree($menuItems);

function printTree($tree, $level = 0) {
    foreach ($tree as $item) {
        echo str_repeat("  ", $level) . "- {$item['title']}\n";
        if (isset($item['children'])) {
            printTree($item['children'], $level + 1);
        }
    }
}

echo "Menu tree:\n";
printTree($menuTree);
echo "\n";

// Performance Considerations
echo "--- Performance Considerations ---\n";

// Large array operations
echo "Large array performance test:\n";
$largeArray = range(1, 10000);

$startTime = microtime(true);
$sum = array_sum($largeArray);
$endTime = microtime(true);
echo "array_sum() time: " . round(($endTime - $startTime) * 1000, 2) . "ms\n";

$startTime = microtime(true);
$filtered = array_filter($largeArray, fn($n) => $n % 2 === 0);
$endTime = microtime(true);
echo "array_filter() time: " . round(($endTime - $startTime) * 1000, 2) . "ms\n";

$startTime = microtime(true);
$mapped = array_map(fn($n) => $n * 2, $largeArray);
$endTime = microtime(true);
echo "array_map() time: " . round(($endTime - $startTime) * 1000, 2) . "ms\n\n";

// Array Optimization Tips
echo "--- Array Optimization Tips ---\n";
echo "1. Use foreach() instead of for() with count() for large arrays\n";
echo "2. Use array_filter() instead of manual filtering loops\n";
echo "3. Use array_map() instead of manual transformation loops\n";
echo "4. Use array_reduce() for complex aggregations\n";
echo "5. Avoid unnecessary array copying\n";
echo "6. Use references (&) for large arrays when modifying\n";
echo "7. Consider using SplFixedArray for fixed-size numeric arrays\n";
echo "8. Use array_key_exists() instead of isset() when you need to distinguish null values\n";
echo "9. Use array_column() for extracting data from multidimensional arrays\n";
echo "10. Use array_chunk() for processing large arrays in batches\n\n";

echo "=== End of Array Functions and Methods ===\n";
?>
