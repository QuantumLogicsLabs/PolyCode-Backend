<?php
/**
 * PHP Loops and Iterations
 * 
 * Understanding different types of loops and iteration patterns in PHP.
 */

echo "=== PHP Loops and Iterations ===\n\n";

// For Loop
echo "--- For Loop ---\n";

echo "Basic for loop:\n";
for ($i = 1; $i <= 5; $i++) {
    echo "Iteration $i\n";
}

echo "\nCountdown with for loop:\n";
for ($i = 10; $i >= 0; $i--) {
    echo "$i ";
}
echo "Blast off!\n\n";

// Nested for loops
echo "Nested for loops (multiplication table):\n";
for ($row = 1; $row <= 3; $row++) {
    for ($col = 1; $col <= 3; $col++) {
        echo sprintf("%2d x %2d = %2d  ", $row, $col, $row * $col);
    }
    echo "\n";
}
echo "\n";

// While Loop
echo "--- While Loop ---\n";

$count = 1;
echo "While loop counting to 5:\n";
while ($count <= 5) {
    echo "Count: $count\n";
    $count++;
}

echo "\nWhile loop with condition:\n";
$number = 100;
while ($number > 0) {
    echo "Number: $number\n";
    $number = floor($number / 2);
    if ($number < 10) break;
}
echo "\n";

// Do-While Loop
echo "--- Do-While Loop ---\n";

$attempt = 1;
do {
    echo "Attempt $attempt\n";
    $attempt++;
} while ($attempt <= 3);

echo "\nDo-while with condition that's initially false:\n";
$counter = 10;
do {
    echo "This will execute once even though condition is false\n";
} while ($counter < 5);
echo "\n";

// Foreach Loop
echo "--- Foreach Loop ---\n";

// Foreach with indexed array
$fruits = ["Apple", "Banana", "Orange", "Grape"];
echo "Fruits:\n";
foreach ($fruits as $fruit) {
    echo "- $fruit\n";
}

echo "\nFruits with index:\n";
foreach ($fruits as $index => $fruit) {
    echo "$index: $fruit\n";
}

// Foreach with associative array
$person = [
    "name" => "John Doe",
    "age" => 30,
    "city" => "New York",
    "email" => "john@example.com"
];

echo "\nPerson details:\n";
foreach ($person as $key => $value) {
    echo ucfirst($key) . ": $value\n";
}

// Foreach with nested arrays
echo "\nNested array iteration:\n";
$students = [
    ["name" => "Alice", "grade" => 85, "subjects" => ["Math", "Science"]],
    ["name" => "Bob", "grade" => 92, "subjects" => ["English", "History"]],
    ["name" => "Charlie", "grade" => 78, "subjects" => ["Art", "Music"]]
];

foreach ($students as $student) {
    echo "Student: {$student['name']}, Grade: {$student['grade']}\n";
    echo "Subjects: " . implode(", ", $student['subjects']) . "\n\n";
}

// Loop Control Statements
echo "--- Loop Control Statements ---\n";

echo "Break statement (stop at 3):\n";
for ($i = 1; $i <= 10; $i++) {
    if ($i == 4) break;
    echo "$i ";
}
echo "\n\n";

echo "Continue statement (skip even numbers):\n";
for ($i = 1; $i <= 10; $i++) {
    if ($i % 2 == 0) continue;
    echo "$i ";
}
echo "\n\n";

// Nested loops with break and continue
echo "Nested loops with break (outer loop):\n";
for ($i = 1; $i <= 3; $i++) {
    echo "Outer loop $i:\n";
    for ($j = 1; $j <= 3; $j++) {
        if ($i == 2 && $j == 2) break 2;
        echo "  Inner loop $j\n";
    }
}
echo "\n";

// Advanced Loop Patterns
echo "--- Advanced Loop Patterns ---\n";

// Loop with multiple conditions
echo "Loop with multiple conditions:\n";
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
foreach ($numbers as $num) {
    if ($num % 2 == 0 && $num > 5) {
        echo "$num (even and > 5)\n";
    }
}

// Loop with accumulator
echo "\nLoop with accumulator (sum of numbers):\n";
$sum = 0;
foreach ($numbers as $num) {
    $sum += $num;
}
echo "Sum: $sum\n";

// Loop with conditional processing
echo "\nLoop with conditional processing:\n";
$grades = [85, 92, 78, 95, 88, 72, 90];
$passed = [];
$failed = [];

foreach ($grades as $grade) {
    if ($grade >= 80) {
        $passed[] = $grade;
    } else {
        $failed[] = $grade;
    }
}

echo "Passed grades: " . implode(", ", $passed) . "\n";
echo "Failed grades: " . implode(", ", $failed) . "\n\n";

// Iterator Functions
echo "--- Iterator Functions ---\n";

$colors = ["Red", "Green", "Blue", "Yellow", "Purple"];

echo "Using array iterator:\n";
$iterator = new ArrayIterator($colors);
foreach ($iterator as $color) {
    echo "- $color\n";
}

echo "\nUsing iterator with seek:\n";
$iterator->seek(2);
echo "Current element: " . $iterator->current() . "\n";
$iterator->next();
echo "Next element: " . $iterator->current() . "\n\n";

// Generator Functions
echo "--- Generator Functions ---\n";

function fibonacci($n) {
    $a = 0;
    $b = 1;
    
    for ($i = 0; $i < $n; $i++) {
        yield $a;
        $temp = $a + $b;
        $a = $b;
        $b = $temp;
    }
}

echo "First 10 Fibonacci numbers:\n";
foreach (fibonacci(10) as $number) {
    echo "$number ";
}
echo "\n\n";

// Range generator
function rangeGenerator($start, $end, $step = 1) {
    for ($i = $start; $i <= $end; $i += $step) {
        yield $i;
    }
}

echo "Custom range generator:\n";
foreach (rangeGenerator(1, 10, 2) as $num) {
    echo "$num ";
}
echo "\n\n";

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: Array Filter with Loop
echo "Example 1: Filter even numbers:\n";
$numbers = range(1, 20);
$evenNumbers = [];

foreach ($numbers as $number) {
    if ($number % 2 === 0) {
        $evenNumbers[] = $number;
    }
}

echo "Even numbers: " . implode(", ", $evenNumbers) . "\n\n";

// Example 2: Find Maximum Value
echo "Example 2: Find maximum value:\n";
$values = [45, 23, 67, 89, 12, 34, 56];
$maxValue = $values[0];

foreach ($values as $value) {
    if ($value > $maxValue) {
        $maxValue = $value;
    }
}

echo "Values: " . implode(", ", $values) . "\n";
echo "Maximum value: $maxValue\n\n";

// Example 3: Word Frequency Counter
echo "Example 3: Word frequency counter:\n";
$text = "the quick brown fox jumps over the lazy dog the fox is quick";
$words = str_word_count($text, 1);
$wordCount = [];

foreach ($words as $word) {
    $word = strtolower($word);
    $wordCount[$word] = ($wordCount[$word] ?? 0) + 1;
}

echo "Word frequencies:\n";
foreach ($wordCount as $word => $count) {
    echo "$word: $count\n";
}
echo "\n";

// Example 4: Directory Listing
echo "Example 4: Directory listing simulation:\n";
$files = [
    ["name" => "index.php", "size" => 1024, "type" => "file"],
    ["name" => "style.css", "size" => 2048, "type" => "file"],
    ["name" => "images", "size" => 0, "type" => "directory"],
    ["name" => "script.js", "size" => 512, "type" => "file"]
];

$totalSize = 0;
$fileCount = 0;
$dirCount = 0;

foreach ($files as $file) {
    echo "- {$file['name']} ({$file['type']})";
    if ($file['type'] === 'file') {
        echo " - {$file['size']} bytes";
        $totalSize += $file['size'];
        $fileCount++;
    } else {
        $dirCount++;
    }
    echo "\n";
}

echo "\nSummary:\n";
echo "Files: $fileCount\n";
echo "Directories: $dirCount\n";
echo "Total size: $totalSize bytes\n\n";

// Example 5: Nested Data Processing
echo "Example 5: Sales data processing:\n";
$salesData = [
    "January" => ["product1" => 100, "product2" => 150, "product3" => 200],
    "February" => ["product1" => 120, "product2" => 180, "product3" => 220],
    "March" => ["product1" => 140, "product2" => 200, "product3" => 250]
];

$productTotals = [];

foreach ($salesData as $month => $products) {
    echo "$month:\n";
    $monthTotal = 0;
    
    foreach ($products as $product => $sales) {
        echo "  $product: $sales\n";
        $monthTotal += $sales;
        $productTotals[$product] = ($productTotals[$product] ?? 0) + $sales;
    }
    
    echo "  Month total: $monthTotal\n\n";
}

echo "Product totals:\n";
foreach ($productTotals as $product => $total) {
    echo "$product: $total\n";
}
echo "\n";

// Performance Considerations
echo "--- Performance Considerations ---\n";

// Large array iteration
echo "Large array iteration (10,000 elements):\n";
$startTime = microtime(true);
$largeArray = range(1, 10000);

$count = 0;
foreach ($largeArray as $value) {
    $count += $value;
}

$endTime = microtime(true);
$executionTime = $endTime - $startTime;

echo "Sum of 1 to 10,000: $count\n";
echo "Execution time: " . round($executionTime * 1000, 2) . " ms\n\n";

// Memory efficient iteration with generator
echo "Memory efficient iteration with generator:\n";

function numberGenerator($max) {
    for ($i = 1; $i <= $max; $i++) {
        yield $i;
    }
}

$startTime = microtime(true);
$genCount = 0;

foreach (numberGenerator(10000) as $value) {
    $genCount += $value;
}

$endTime = microtime(true);
$genExecutionTime = $endTime - $startTime;

echo "Sum using generator: $genCount\n";
echo "Execution time: " . round($genExecutionTime * 1000, 2) . " ms\n\n";

// Loop Optimization Tips
echo "--- Loop Optimization Tips ---\n";
echo "1. Use foreach for array iteration instead of for with count()\n";
echo "2. Pre-calculate loop limits when possible\n";
echo "3. Avoid function calls in loop conditions\n";
echo "4. Use generators for large datasets\n";
echo "5. Consider array functions for simple operations\n";
echo "6. Use break and continue to skip unnecessary iterations\n";
echo "7. Minimize operations inside loops\n";
echo "8. Use references for large objects in loops\n\n";

echo "=== End of Loops and Iterations ===\n";
?>
