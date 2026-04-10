# PHP Arrays and String Handling

## Arrays in PHP

An array in PHP is an ordered map — a data structure that associates keys to values. PHP arrays are extremely flexible: they can hold any mix of data types, act as lists, dictionaries, stacks, queues, and more.

---

## Types of Arrays

### Indexed Arrays

Elements are stored with numeric keys (starting from 0 by default).

```php
<?php
$fruits = ["Apple", "Banana", "Cherry"];
// Or using the older syntax:
$fruits = array("Apple", "Banana", "Cherry");

echo $fruits[0]; // Apple
echo $fruits[1]; // Banana
echo $fruits[2]; // Cherry

// Add an element
$fruits[] = "Date";     // Appended at index 3
$fruits[10] = "Elderberry"; // Explicit index

print_r($fruits);
?>
```

### Associative Arrays

Elements are stored with named (string) keys.

```php
<?php
$person = [
    "name"  => "Alice",
    "age"   => 30,
    "city"  => "Karachi",
    "email" => "alice@example.com"
];

echo $person["name"];  // Alice
echo $person["city"];  // Karachi

// Modify a value
$person["age"] = 31;

// Add new key
$person["country"] = "Pakistan";
?>
```

### Multidimensional Arrays

Arrays that contain other arrays — ideal for tables or grids.

```php
<?php
$students = [
    ["name" => "Alice", "grade" => "A", "score" => 95],
    ["name" => "Bob",   "grade" => "B", "score" => 82],
    ["name" => "Carol", "grade" => "A", "score" => 91],
];

echo $students[0]["name"];  // Alice
echo $students[1]["score"]; // 82

// 2D grid
$matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
];

echo $matrix[1][2]; // 6
?>
```

---

## Array Functions

### Adding and Removing Elements

```php
<?php
$arr = [1, 2, 3];

// Add to end
array_push($arr, 4, 5);  // [1, 2, 3, 4, 5]
$arr[] = 6;               // [1, 2, 3, 4, 5, 6]

// Remove from end
$last = array_pop($arr);  // removes 6, returns 6

// Add to beginning
array_unshift($arr, 0);   // [0, 1, 2, 3, 4, 5]

// Remove from beginning
$first = array_shift($arr); // removes 0, returns 0

print_r($arr); // [1, 2, 3, 4, 5]
?>
```

### Searching Arrays

```php
<?php
$fruits = ["Apple", "Banana", "Cherry", "Date"];

// Check if value exists
var_dump(in_array("Cherry", $fruits));  // bool(true)
var_dump(in_array("Mango", $fruits));   // bool(false)

// Find the key of a value
$key = array_search("Banana", $fruits); // 1

// Check if key exists
var_dump(array_key_exists(2, $fruits)); // bool(true)
var_dump(isset($fruits[5]));            // bool(false)
?>
```

### Sorting Arrays

```php
<?php
$numbers = [5, 2, 8, 1, 9, 3];

sort($numbers);       // Ascending: [1, 2, 3, 5, 8, 9]
rsort($numbers);      // Descending: [9, 8, 5, 3, 2, 1]

$assoc = ["banana" => 2, "apple" => 5, "cherry" => 1];
asort($assoc);        // Sort by value, keep keys: cherry=>1, banana=>2, apple=>5
arsort($assoc);       // Sort by value descending
ksort($assoc);        // Sort by key: apple=>5, banana=>2, cherry=>1
krsort($assoc);       // Sort by key descending

// Custom sort
$names = ["Charlie", "Alice", "Bob"];
usort($names, function($a, $b) { return strcmp($a, $b); });
// ["Alice", "Bob", "Charlie"]
?>
```

### Slicing and Splicing

```php
<?php
$letters = ["a", "b", "c", "d", "e", "f"];

// Extract a portion (offset, length)
$slice = array_slice($letters, 1, 3); // ["b", "c", "d"]

// Remove and/or insert elements (offset, deleteCount, newElements...)
array_splice($letters, 2, 2, ["X", "Y"]);
// $letters is now ["a", "b", "X", "Y", "e", "f"]
?>
```

### Merging and Combining

```php
<?php
$a = ["red", "green"];
$b = ["blue", "yellow"];

$merged = array_merge($a, $b);       // ["red", "green", "blue", "yellow"]
$merged = array_merge($a, $a);       // ["red", "green", "red", "green"]

// Unique values only
$unique = array_unique(["a", "b", "a", "c", "b"]); // ["a", "b", "c"]

// Combine keys and values from two arrays
$keys   = ["name", "age", "city"];
$values = ["Alice", 30, "Karachi"];
$person = array_combine($keys, $values);
// ["name" => "Alice", "age" => 30, "city" => "Karachi"]
?>
```

### Functional Array Methods

```php
<?php
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

// array_map — apply a function to each element
$squared = array_map(fn($n) => $n ** 2, $numbers);
// [1, 4, 9, 16, 25, 36, 49, 64, 81, 100]

// array_filter — keep elements that pass a test
$evens = array_filter($numbers, fn($n) => $n % 2 === 0);
// [2, 4, 6, 8, 10]

// array_reduce — reduce array to a single value
$sum = array_reduce($numbers, fn($carry, $item) => $carry + $item, 0);
// 55

// array_walk — apply function to each element in place
array_walk($numbers, function(&$value, $key) {
    $value = $value * 2;
});
// [2, 4, 6, 8, 10, ...]
?>
```

### Other Useful Array Functions

```php
<?php
$arr = [3, 1, 4, 1, 5, 9, 2, 6];

echo count($arr);          // 8
echo array_sum($arr);      // 31
echo array_product([1,2,3,4]); // 24

$flipped = array_flip(["a" => 1, "b" => 2]); // [1 => "a", 2 => "b"]
$reversed = array_reverse([1, 2, 3]);         // [3, 2, 1]
$filled = array_fill(0, 5, "x");              // ["x","x","x","x","x"]
$chunk = array_chunk([1,2,3,4,5], 2);         // [[1,2],[3,4],[5]]

// Get keys and values
$keys = array_keys(["a" => 1, "b" => 2]);   // ["a", "b"]
$vals = array_values(["a" => 1, "b" => 2]); // [1, 2]

// Column extraction from 2D array
$records = [
    ["id" => 1, "name" => "Alice"],
    ["id" => 2, "name" => "Bob"],
];
$names = array_column($records, "name"); // ["Alice", "Bob"]
?>
```

---

## Strings in PHP

Strings are sequences of characters. PHP provides a rich library of string functions.

### String Literals

```php
<?php
// Single quotes — no variable interpolation
$name = 'Alice';
$msg  = 'Hello, $name!'; // Hello, $name!

// Double quotes — variables are interpolated
$msg = "Hello, $name!";  // Hello, Alice!
$msg = "Hello, {$name}!"; // Same, with curly braces for clarity

// Heredoc — like double quotes, multiline
$text = <<<EOT
Dear $name,
Welcome to PHP.
EOT;

// Nowdoc — like single quotes, multiline
$text = <<<'EOT'
Dear $name,
No interpolation here.
EOT;
?>
```

### Common String Functions

```php
<?php
$str = "The quick brown fox jumps over the lazy dog";

// Length
echo strlen($str);         // 43

// Case
echo strtoupper($str);     // THE QUICK BROWN FOX...
echo strtolower($str);     // the quick brown fox...
echo ucfirst("hello");     // Hello
echo ucwords("hello world"); // Hello World

// Trim whitespace
echo trim("  hello  ");    // "hello"
echo ltrim("  hello  ");   // "hello  "
echo rtrim("  hello  ");   // "  hello"

// Searching
echo strpos($str, "fox");  // 16
echo strrpos($str, "o");   // 41 (last occurrence)
echo substr_count($str, "the"); // 1 (case-sensitive)

// Extracting substrings
echo substr($str, 4, 5);   // quick
echo substr($str, -3);     // dog

// Replacing
echo str_replace("fox", "cat", $str);
// The quick brown cat jumps over the lazy dog

echo str_ireplace("THE", "A", $str); // Case-insensitive replace

// Splitting and joining
$words = explode(" ", "one two three"); // ["one", "two", "three"]
$joined = implode(", ", $words);        // "one, two, three"

// Padding
echo str_pad("42", 5, "0", STR_PAD_LEFT);  // "00042"
echo str_pad("Hi", 10, "-", STR_PAD_BOTH); // "----Hi----"

// Repeat
echo str_repeat("AB", 4); // ABABABAB

// Reverse
echo strrev("Hello"); // olleH

// Count words
echo str_word_count("Hello World PHP"); // 3
?>
```

### String Formatting

```php
<?php
// printf / sprintf — formatted output
$name = "Alice";
$score = 95.6789;

printf("Name: %-10s Score: %.2f\n", $name, $score);
// Name: Alice      Score: 95.68

$formatted = sprintf("$%,.2f", 1234567.891);
echo $formatted; // $1,234,567.89

// number_format
echo number_format(9876543.21, 2, '.', ','); // 9,876,543.21

// date formatting
echo date("d-m-Y H:i:s"); // e.g. 10-04-2026 14:30:00
?>
```

### Regular Expressions

PHP uses PCRE (Perl Compatible Regular Expressions).

```php
<?php
$str = "My phone is +92-300-1234567 or 021-1234567";

// Test if pattern matches
if (preg_match('/\+\d{2}-\d{3}-\d{7}/', $str)) {
    echo "Found a phone number!\n";
}

// Get all matches
preg_match_all('/\d{3,7}/', $str, $matches);
print_r($matches[0]); // ["92", "300", "1234567", "021", "1234567"]

// Replace
$clean = preg_replace('/\s+/', ' ', "Hello   World  PHP");
echo $clean; // "Hello World PHP"

// Split
$parts = preg_split('/[\s,]+/', "one, two,  three four");
print_r($parts); // ["one", "two", "three", "four"]
?>
```

---

## Summary

PHP arrays are one of its most powerful features — combining the behavior of lists, maps, and objects in a single flexible structure. The rich set of built-in array and string functions eliminates the need for complex manual implementations. Understanding these tools is essential for processing data, building APIs, and handling user input effectively.
