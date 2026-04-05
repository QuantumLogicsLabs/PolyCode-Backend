# Rust Arrays and Collections

## Arrays

### Fixed-Size Arrays
```rust
// Array declaration and initialization
fn array_declaration() {
    // Explicit type annotation
    let numbers: [i32; 5] = [1, 2, 3, 4, 5];
    
    // Type inference
    let numbers = [1, 2, 3, 4, 5];
    
    // Array with same values
    let zeros = [0; 5];
    let ones = [1; 10];
    
    // Array of different types
    let floats: [f64; 3] = [1.1, 2.2, 3.3];
    let strings: [&str; 3] = ["hello", "world", "rust"];
    let booleans: [bool; 4] = [true, false, true, false];
    
    println!("Numbers: {:?}", numbers);
    println!("Zeros: {:?}", zeros);
    println!("Floats: {:?}", floats);
}

// Array access and manipulation
fn array_access() {
    let mut numbers = [1, 2, 3, 4, 5];
    
    // Access elements
    println!("First element: {}", numbers[0]);
    println!("Last element: {}", numbers[4]);
    
    // Modify elements
    numbers[0] = 10;
    numbers[4] = 50;
    
    println!("Modified array: {:?}", numbers);
    
    // Array length
    println!("Array length: {}", numbers.len());
    
    // Array bounds checking (will panic at runtime)
    // let out_of_bounds = numbers[10]; // This would panic!
    
    // Safe access with get
    match numbers.get(2) {
        Some(value) => println!("Element at index 2: {}", value),
        None => println!("Element at index 2 does not exist"),
    }
    
    match numbers.get(10) {
        Some(value) => println!("Element at index 10: {}", value),
        None => println!("Element at index 10 does not exist"),
    }
}

// Array iteration
fn array_iteration() {
    let numbers = [1, 2, 3, 4, 5];
    
    // Using for loop with indices
    println!("With indices:");
    for i in 0..numbers.len() {
        println!("numbers[{}] = {}", i, numbers[i]);
    }
    
    // Using iter()
    println!("With iter():");
    for number in numbers.iter() {
        println!("Number: {}", number);
    }
    
    // Using iter_mut() for mutable iteration
    let mut numbers = [1, 2, 3, 4, 5];
    for number in numbers.iter_mut() {
        *number *= 2;
    }
    println!("After doubling: {:?}", numbers);
    
    // Using enumerate()
    println!("With enumerate():");
    for (i, number) in numbers.iter().enumerate() {
        println!("Index {}: {}", i, number);
    }
}

// Array operations
fn array_operations() {
    let mut numbers = [1, 2, 3, 4, 5];
    
    // Find element
    let found = numbers.iter().find(|&&x| x == 3);
    println!("Found 3: {:?}", found);
    
    // Filter elements
    let evens: Vec<i32> = numbers.iter().filter(|&&x| x % 2 == 0).cloned().collect();
    println!("Even numbers: {:?}", evens);
    
    // Map elements
    let doubled: Vec<i32> = numbers.iter().map(|x| x * 2).collect();
    println!("Doubled: {:?}", doubled);
    
    // Sum elements
    let sum: i32 = numbers.iter().sum();
    println!("Sum: {}", sum);
    
    // Check if all elements satisfy condition
    let all_positive = numbers.iter().all(|&x| x > 0);
    println!("All positive: {}", all_positive);
    
    // Check if any element satisfies condition
    let any_even = numbers.iter().any(|&x| x % 2 == 0);
    println!("Any even: {}", any_even);
    
    // Find max and min
    let max = numbers.iter().max();
    let min = numbers.iter().min();
    println!("Max: {:?}, Min: {:?}", max, min);
}

// Multi-dimensional arrays
fn multidimensional_arrays() {
    // 2D array
    let matrix: [[i32; 3]; 3] = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9],
    ];
    
    println!("Matrix:");
    for row in matrix.iter() {
        println!("{:?}", row);
    }
    
    // Access 2D array elements
    println!("Element at (1, 2): {}", matrix[1][2]);
    
    // 3D array
    let cube: [[[i32; 2]; 2]; 2] = [
        [[1, 2], [3, 4]],
        [[5, 6], [7, 8]],
    ];
    
    println!("Cube:");
    for layer in cube.iter() {
        for row in layer.iter() {
            println!("{:?}", row);
        }
        println!();
    }
}

// Array slices
fn array_slices() {
    let numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    
    // Create slice
    let slice = &numbers[2..5]; // Elements at indices 2, 3, 4
    println!("Slice: {:?}", slice);
    
    // Slice from beginning
    let from_start = &numbers[..5]; // Elements at indices 0, 1, 2, 3, 4
    println!("From start: {:?}", from_start);
    
    // Slice to end
    let to_end = &numbers[5..]; // Elements at indices 5, 6, 7, 8, 9
    println!("To end: {:?}", to_end);
    
    // Entire array as slice
    let entire = &numbers[..];
    println!("Entire: {:?}", entire);
    
    // Slice operations
    let slice_sum: i32 = slice.iter().sum();
    println!("Slice sum: {}", slice_sum);
    
    // Modify through slice
    let mut numbers = [1, 2, 3, 4, 5];
    let slice = &mut numbers[1..4];
    for element in slice.iter_mut() {
        *element *= 2;
    }
    println!("Modified: {:?}", numbers);
}
```

## Vectors

### Vector Basics
```rust
use std::vec::Vec;

// Vector creation and initialization
fn vector_creation() {
    // Empty vector
    let mut vec: Vec<i32> = Vec::new();
    
    // Vector with initial capacity
    let mut vec: Vec<i32> = Vec::with_capacity(10);
    
    // Vector with initial values
    let vec = vec![1, 2, 3, 4, 5];
    
    // Vector with repeated values
    let vec = vec![0; 10];
    
    // Vector from array
    let array = [1, 2, 3, 4, 5];
    let vec = array.to_vec();
    
    // Vector from iterator
    let vec: Vec<i32> = (0..10).collect();
    
    println!("Vector: {:?}", vec);
}

// Vector operations
fn vector_operations() {
    let mut vec = Vec::new();
    
    // Add elements
    vec.push(1);
    vec.push(2);
    vec.push(3);
    
    println!("After pushes: {:?}", vec);
    
    // Remove element
    let removed = vec.pop();
    println!("Removed: {:?}", removed);
    println!("After pop: {:?}", vec);
    
    // Insert at index
    vec.insert(1, 10);
    println!("After insert: {:?}", vec);
    
    // Remove at index
    let removed = vec.remove(1);
    println!("Removed at index 1: {}", removed);
    println!("After remove: {:?}", vec);
    
    // Clear vector
    vec.clear();
    println!("After clear: {:?}", vec);
    
    // Check if empty
    println!("Is empty: {}", vec.is_empty());
    
    // Get length
    vec.push(1);
    vec.push(2);
    println!("Length: {}", vec.len());
    
    // Get capacity
    println!("Capacity: {}", vec.capacity());
}

// Vector access
fn vector_access() {
    let mut vec = vec![1, 2, 3, 4, 5];
    
    // Access by index
    println!("First element: {}", vec[0]);
    println!("Last element: {}", vec[vec.len() - 1]);
    
    // Safe access with get
    match vec.get(2) {
        Some(value) => println!("Element at index 2: {}", value),
        None => println!("No element at index 2"),
    }
    
    match vec.get(10) {
        Some(value) => println!("Element at index 10: {}", value),
        None => println!("No element at index 10"),
    }
    
    // Mutable access
    vec[0] = 10;
    println!("After modification: {:?}", vec);
    
    // First and last elements
    println!("First: {:?}", vec.first());
    println!("Last: {:?}", vec.last());
    
    // Split vector
    let (left, right) = vec.split_at(2);
    println!("Left: {:?}", left);
    println!("Right: {:?}", right);
}

// Vector iteration
fn vector_iteration() {
    let vec = vec![1, 2, 3, 4, 5];
    
    // Immutable iteration
    println!("Immutable iteration:");
    for item in vec.iter() {
        println!("{}", item);
    }
    
    // Mutable iteration
    let mut vec = vec![1, 2, 3, 4, 5];
    println!("Mutable iteration:");
    for item in vec.iter_mut() {
        *item *= 2;
        println!("{}", item);
    }
    println!("After doubling: {:?}", vec);
    
    // Iteration with indices
    println!("With indices:");
    for (i, item) in vec.iter().enumerate() {
        println!("Index {}: {}", i, item);
    }
    
    // Consuming iteration
    println!("Consuming iteration:");
    for item in vec.into_iter() {
        println!("{}", item);
    }
    // vec is no longer available here
}

// Vector searching and filtering
fn vector_searching() {
    let vec = vec![1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    
    // Find element
    let found = vec.iter().find(|&&x| x == 5);
    println!("Found 5: {:?}", found);
    
    // Find position
    let position = vec.iter().position(|&x| x == 5);
    println!("Position of 5: {:?}", position);
    
    // Filter elements
    let evens: Vec<i32> = vec.iter().filter(|&&x| x % 2 == 0).cloned().collect();
    println!("Even numbers: {:?}", evens);
    
    // Filter with predicate
    let greater_than_5: Vec<i32> = vec.iter().filter(|&&x| x > 5).cloned().collect();
    println!("Greater than 5: {:?}", greater_than_5);
    
    // Check if contains
    let contains_5 = vec.contains(&5);
    println!("Contains 5: {}", contains_5);
    
    // Check if all satisfy condition
    let all_positive = vec.iter().all(|&x| x > 0);
    println!("All positive: {}", all_positive);
    
    // Check if any satisfy condition
    let any_even = vec.iter().any(|&x| x % 2 == 0);
    println!("Any even: {}", any_even);
}
```

## Strings

### String Types
```rust
// String literals and owned strings
fn string_types() {
    // String literal (&str)
    let string_literal: &str = "Hello, World!";
    
    // Owned String
    let owned_string: String = String::from("Hello, World!");
    
    // From string literal
    let from_literal: String = "Hello, World!".to_string();
    
    // From &str
    let from_str: String = string_literal.to_string();
    
    println!("String literal: {}", string_literal);
    println!("Owned string: {}", owned_string);
    println!("From literal: {}", from_literal);
    println!("From str: {}", from_str);
    
    // String properties
    println!("Length: {}", owned_string.len());
    println!("Capacity: {}", owned_string.capacity());
    println!("Is empty: {}", owned_string.is_empty());
}

// String operations
fn string_operations() {
    let mut string = String::from("Hello");
    
    // Append
    string.push_str(", World!");
    println!("After push_str: {}", string);
    
    // Push character
    string.push('!');
    println!("After push: {}", string);
    
    // Insert at position
    string.insert(5, ',');
    println!("After insert: {}", string);
    
    // Insert string at position
    string.insert_str(6, " Rust");
    println!("After insert_str: {}", string);
    
    // Remove character
    let removed_char = string.remove(0);
    println!("Removed char: {}", removed_char);
    println!("After remove: {}", string);
    
    // Remove range
    let removed_string = string.remove_range(0, 5);
    println!("Removed range: {}", removed_string);
    println!("After remove_range: {}", string);
    
    // Replace
    string = string.replace("World", "Rust");
    println!("After replace: {}", string);
    
    // Replace range
    string.replace_range(0..5, "Hi");
    println!("After replace_range: {}", string);
    
    // Clear string
    string.clear();
    println!("After clear: '{}'", string);
}

// String slicing and indexing
fn string_slicing() {
    let string = String::from("Hello, World!");
    
    // Get character at position
    match string.chars().nth(7) {
        Some(char) => println!("Character at position 7: {}", char),
        None => println!("No character at position 7"),
    }
    
    // Get bytes
    let bytes = string.as_bytes();
    println!("First 5 bytes: {:?}", &bytes[..5]);
    
    // Get slice
    let slice = &string[0..5];
    println!("Slice: {}", slice);
    
    // Get characters
    let chars: Vec<char> = string.chars().collect();
    println!("Characters: {:?}", chars);
    
    // Get bytes
    let byte_vec = string.as_bytes().to_vec();
    println!("Bytes: {:?}", byte_vec);
    
    // Split string
    let parts: Vec<&str> = string.split(", ").collect();
    println!("Split parts: {:?}", parts);
    
    // Lines
    let multi_line = String::from("Line 1\nLine 2\nLine 3");
    let lines: Vec<&str> = multi_line.lines().collect();
    println!("Lines: {:?}", lines);
}

// String formatting
fn string_formatting() {
    // Using format! macro
    let name = "Alice";
    let age = 30;
    let formatted = format!("{} is {} years old", name, age);
    println!("Formatted: {}", formatted);
    
    // Using println! with formatting
    println!("{} is {} years old", name, age);
    
    // Debug formatting
    let vec = vec![1, 2, 3];
    println!("Vector: {:?}", vec);
    
    // Display formatting
    #[derive(Debug)]
    struct Point {
        x: i32,
        y: i32,
    }
    
    impl std::fmt::Display for Point {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "({}, {})", self.x, self.y)
        }
    }
    
    let point = Point { x: 10, y: 20 };
    println!("Point: {}", point);
    println!("Point (debug): {:?}", point);
    
    // Padding and alignment
    let number = 42;
    println!("{:<10} left aligned", number);
    println!("{:>10} right aligned", number);
    println!("{:^10} centered", number);
    
    // Number formatting
    let pi = 3.14159;
    println!("{:.2}", pi); // 2 decimal places
    println!("{:.0}", pi); // No decimal places
    println!("{:08}", number); // Zero padding
}

// String comparison
fn string_comparison() {
    let string1 = String::from("Hello");
    let string2 = String::from("Hello");
    let string3 = String::from("World");
    
    // Equality comparison
    println!("string1 == string2: {}", string1 == string2);
    println!("string1 == string3: {}", string1 == string3);
    
    // Ordering comparison
    println!("string1 < string3: {}", string1 < string3);
    
    // Case-sensitive comparison
    let string4 = String::from("hello");
    println!("string1 == string4: {}", string1 == string4);
    
    // Case-insensitive comparison
    println!("string1.to_lowercase() == string4.to_lowercase(): {}", 
             string1.to_lowercase() == string4.to_lowercase());
    
    // Starts with
    println!("string1 starts with 'He': {}", string1.starts_with("He"));
    println!("string1 starts with 'lo': {}", string1.starts_with("lo"));
    
    // Ends with
    println!("string1 ends with 'lo': {}", string1.ends_with("lo"));
    println!("string1 ends with 'He': {}", string1.ends_with("He"));
    
    // Contains
    println!("string1 contains 'll': {}", string1.contains("ll"));
    println!("string1 contains 'xyz': {}", string1.contains("xyz"));
}
```

## HashMap

### HashMap Basics
```rust
use std::collections::HashMap;

// HashMap creation and initialization
fn hashmap_creation() {
    // Empty HashMap
    let mut map: HashMap<String, i32> = HashMap::new();
    
    // HashMap with initial capacity
    let mut map: HashMap<String, i32> = HashMap::with_capacity(10);
    
    // HashMap from iterator
    let map: HashMap<String, i32> = vec![
        ("one".to_string(), 1),
        ("two".to_string(), 2),
        ("three".to_string(), 3),
    ].into_iter().collect();
    
    println!("HashMap: {:?}", map);
}

// HashMap operations
fn hashmap_operations() {
    let mut scores = HashMap::new();
    
    // Insert key-value pairs
    scores.insert(String::from("Blue"), 10);
    scores.insert(String::from("Yellow"), 50);
    
    println!("Scores: {:?}", scores);
    
    // Get value
    match scores.get("Blue") {
        Some(score) => println!("Blue score: {}", score),
        None => println!("No score for Blue"),
    }
    
    // Get value or default
    let blue_score = scores.get("Blue").unwrap_or(&0);
    println!("Blue score: {}", blue_score);
    
    // Insert or update
    scores.entry(String::from("Blue")).or_insert(20);
    println!("After or_insert: {:?}", scores);
    
    scores.entry(String::from("Green")).or_insert(30);
    println!("After inserting Green: {:?}", scores);
    
    // Update based on existing value
    scores.entry(String::from("Blue")).and_modify(|e| *e += 5);
    println!("After modify: {:?}", scores);
    
    // Remove key
    let removed = scores.remove("Yellow");
    println!("Removed: {:?}", removed);
    println!("After remove: {:?}", scores);
    
    // Check if key exists
    println!("Contains Blue: {}", scores.contains_key("Blue"));
    println!("Contains Red: {}", scores.contains_key("Red"));
    
    // Get length
    println!("Length: {}", scores.len());
    
    // Check if empty
    println!("Is empty: {}", scores.is_empty());
    
    // Clear HashMap
    scores.clear();
    println!("After clear: {:?}", scores);
}

// HashMap iteration
fn hashmap_iteration() {
    let mut scores = HashMap::new();
    scores.insert(String::from("Blue"), 10);
    scores.insert(String::from("Yellow"), 50);
    scores.insert(String::from("Green"), 30);
    
    // Iterate over key-value pairs
    println!("Key-value pairs:");
    for (key, value) in scores.iter() {
        println!("{}: {}", key, value);
    }
    
    // Iterate over keys
    println!("Keys:");
    for key in scores.keys() {
        println!("{}", key);
    }
    
    // Iterate over values
    println!("Values:");
    for value in scores.values() {
        println!("{}", value);
    }
    
    // Mutable iteration
    println!("Mutable iteration:");
    for (key, value) in scores.iter_mut() {
        *value *= 2;
        println!("{}: {}", key, value);
    }
    
    println!("After doubling: {:?}", scores);
}

// HashMap searching and filtering
fn hashmap_searching() {
    let mut scores = HashMap::new();
    scores.insert(String::from("Blue"), 10);
    scores.insert(String::from("Yellow"), 50);
    scores.insert(String::from("Green"), 30);
    scores.insert(String::from("Red"), 20);
    
    // Find value
    let blue_score = scores.get("Blue");
    println!("Blue score: {:?}", blue_score);
    
    // Find maximum value
    let max_score = scores.values().max();
    println!("Max score: {:?}", max_score);
    
    // Find key with maximum value
    let max_entry = scores.iter().max_by_key(|(_, value)| *value);
    println!("Entry with max score: {:?}", max_entry);
    
    // Filter entries
    let high_scores: HashMap<String, i32> = scores.iter()
        .filter(|(_, &value)| value >= 30)
        .map(|(key, value)| (key.clone(), *value))
        .collect();
    println!("High scores: {:?}", high_scores);
    
    // Check if any value satisfies condition
    let any_high_score = scores.values().any(|&value| value >= 50);
    println!("Any score >= 50: {}", any_high_score);
    
    // Check if all values satisfy condition
    let all_positive = scores.values().all(|&value| value > 0);
    println!("All scores positive: {}", all_positive);
    
    // Sum values
    let total_score: i32 = scores.values().sum();
    println!("Total score: {}", total_score);
    
    // Count values satisfying condition
    let high_score_count = scores.values().filter(|&&value| value >= 30).count();
    println!("High score count: {}", high_score_count);
}
```

## Other Collections

### HashSet
```rust
use std::collections::HashSet;

// HashSet operations
fn hashset_operations() {
    // Create HashSet
    let mut set = HashSet::new();
    
    // Insert elements
    set.insert(1);
    set.insert(2);
    set.insert(3);
    set.insert(2); // Duplicate - will be ignored
    
    println!("Set: {:?}", set);
    
    // Check if contains
    println!("Contains 2: {}", set.contains(&2));
    println!("Contains 4: {}", set.contains(&4));
    
    // Remove element
    let removed = set.remove(&2);
    println!("Removed 2: {}", removed);
    println!("After remove: {:?}", set);
    
    // Get length
    println!("Length: {}", set.len());
    
    // Check if empty
    println!("Is empty: {}", set.is_empty());
    
    // Clear set
    set.clear();
    println!("After clear: {:?}", set);
    
    // HashSet from iterator
    let set: HashSet<i32> = vec![1, 2, 3, 2, 1].into_iter().collect();
    println!("From iterator: {:?}", set);
    
    // Set operations
    let set1: HashSet<i32> = vec![1, 2, 3, 4].into_iter().collect();
    let set2: HashSet<i32> = vec![3, 4, 5, 6].into_iter().collect();
    
    // Union
    let union: HashSet<i32> = set1.union(&set2).cloned().collect();
    println!("Union: {:?}", union);
    
    // Intersection
    let intersection: HashSet<i32> = set1.intersection(&set2).cloned().collect();
    println!("Intersection: {:?}", intersection);
    
    // Difference
    let difference: HashSet<i32> = set1.difference(&set2).cloned().collect();
    println!("Difference (set1 - set2): {:?}", difference);
    
    // Symmetric difference
    let sym_diff: HashSet<i32> = set1.symmetric_difference(&set2).cloned().collect();
    println!("Symmetric difference: {:?}", sym_diff);
    
    // Check if subset
    let subset: HashSet<i32> = vec![1, 2].into_iter().collect();
    println!("Subset: {:?} is subset of {:?}: {}", subset, set1, subset.is_subset(&set1));
    
    // Check if disjoint
    let disjoint: HashSet<i32> = vec![7, 8].into_iter().collect();
    println!("Disjoint: {:?} and {:?}: {}", disjoint, set1, disjoint.is_disjoint(&set1));
}
```

### Tuple
```rust
// Tuple operations
fn tuple_operations() {
    // Create tuples
    let tuple1 = (1, "hello", true);
    let tuple2: (i32, &str, bool) = (1, "hello", true);
    
    println!("Tuple1: {:?}", tuple1);
    println!("Tuple2: {:?}", tuple2);
    
    // Access tuple elements
    let first = tuple1.0;
    let second = tuple1.1;
    let third = tuple1.2;
    
    println!("First: {}, Second: {}, Third: {}", first, second, third);
    
    // Destructure tuple
    let (x, y, z) = tuple1;
    println!("Destructured: {}, {}, {}", x, y, z);
    
    // Partial destructure
    let (a, _, c) = tuple1;
    println!("Partial: {}, {}", a, c);
    
    // Tuple of tuples
    let nested = ((1, 2), (3, 4));
    let ((x1, y1), (x2, y2)) = nested;
    println!("Nested: ({}, {}), ({}, {})", x1, y1, x2, y2);
    
    // Single-element tuple
    let single_element = (5,);
    println!("Single element: {:?}", single_element);
    
    // Empty tuple
    let empty = ();
    println!("Empty tuple: {:?}", empty);
    
    // Tuple as return value
    fn calculate(a: i32, b: i32) -> (i32, i32, i32) {
        (a + b, a - b, a * b)
    }
    
    let (sum, diff, product) = calculate(10, 5);
    println!("Sum: {}, Diff: {}, Product: {}", sum, diff, product);
    
    // Compare tuples
    let tuple3 = (1, 2, 3);
    let tuple4 = (1, 2, 3);
    let tuple5 = (1, 2, 4);
    
    println!("tuple3 == tuple4: {}", tuple3 == tuple4);
    println!("tuple3 == tuple5: {}", tuple3 == tuple5);
}
```

### Option and Result
```rust
// Option operations
fn option_operations() {
    // Create Option values
    let some_value: Option<i32> = Some(5);
    let none_value: Option<i32> = None;
    
    println!("Some value: {:?}", some_value);
    println!("None value: {:?}", none_value);
    
    // Check if Some
    if let Some(value) = some_value {
        println!("Some value: {}", value);
    }
    
    // Check if None
    if none_value.is_none() {
        println!("Value is None");
    }
    
    // Unwrap or default
    let value1 = some_value.unwrap_or(0);
    let value2 = none_value.unwrap_or(0);
    
    println!("Value1: {}, Value2: {}", value1, value2);
    
    // Map operation
    let doubled = some_value.map(|x| x * 2);
    println!("Doubled: {:?}", doubled);
    
    // And then
    let and_then = some_value.and_then(|x| Some(x * 2));
    println!("And then: {:?}", and_then);
    
    // Filter
    let filtered = some_value.filter(|&x| x > 3);
    println!("Filtered: {:?}", filtered);
    
    // Option to Result
    let result = some_value.ok_or("No value".to_string());
    println!("Option to Result: {:?}", result);
}

// Result operations
fn result_operations() {
    // Create Result values
    let success: Result<i32, String> = Ok(42);
    let failure: Result<i32, String> = Err("Error message".to_string());
    
    println!("Success: {:?}", success);
    println!("Failure: {:?}", failure);
    
    // Check if Ok
    if let Ok(value) = success {
        println!("Success value: {}", value);
    }
    
    // Check if Err
    if let Err(error) = failure {
        println!("Error message: {}", error);
    }
    
    // Unwrap or default
    let value1 = success.unwrap_or(0);
    let value2 = failure.unwrap_or(0);
    
    println!("Value1: {}, Value2: {}", value1, value2);
    
    // Map operation
    let mapped_success = success.map(|x| x * 2);
    let mapped_failure = failure.map(|x| x * 2);
    
    println!("Mapped success: {:?}", mapped_success);
    println!("Mapped failure: {:?}", mapped_failure);
    
    // Map error
    let mapped_error = failure.map_err(|e| format!("Error: {}", e));
    println!("Mapped error: {:?}", mapped_error);
    
    // And then
    let and_then_success = success.and_then(|x| Ok(x * 2));
    let and_then_failure = failure.and_then(|x| Ok(x * 2));
    
    println!("And then success: {:?}", and_then_success);
    println!("And then failure: {:?}", and_then_failure);
    
    // Or else
    let or_else_success = success.or_else(|| Ok(0));
    let or_else_failure = failure.or_else(|| Ok(0));
    
    println!("Or else success: {:?}", or_else_success);
    println!("Or else failure: {:?}", or_else_failure);
}
```

## Best Practices

### Collection Best Practices
```rust
// 1. Use appropriate collection types
fn appropriate_collections() {
    // Use arrays for fixed-size collections
    let days_of_week: [&str; 7] = [
        "Monday", "Tuesday", "Wednesday", "Thursday", 
        "Friday", "Saturday", "Sunday"
    ];
    
    // Use Vec for dynamic collections
    let mut numbers = Vec::new();
    numbers.push(1);
    numbers.push(2);
    numbers.push(3);
    
    // Use HashMap for key-value pairs
    let mut scores = HashMap::new();
    scores.insert("Alice".to_string(), 100);
    scores.insert("Bob".to_string(), 85);
    
    // Use HashSet for unique values
    let mut unique_numbers: HashSet<i32> = HashSet::new();
    unique_numbers.insert(1);
    unique_numbers.insert(2);
    unique_numbers.insert(1); // Duplicate ignored
}

// 2. Use iterators instead of manual loops
fn use_iterators() {
    let numbers = vec![1, 2, 3, 4, 5];
    
    // Good: use iterator methods
    let sum: i32 = numbers.iter().sum();
    let evens: Vec<i32> = numbers.iter().filter(|&&x| x % 2 == 0).cloned().collect();
    
    // Avoid: manual loops
    let mut sum = 0;
    for &number in numbers.iter() {
        sum += number;
    }
    
    let mut evens = Vec::new();
    for &number in numbers.iter() {
        if number % 2 == 0 {
            evens.push(number);
        }
    }
}

// 3. Use appropriate string types
fn string_types() {
    // Use &str for string slices (no ownership)
    fn process_string(s: &str) {
        println!("Processing: {}", s);
    }
    
    // Use String for owned strings
    fn create_string() -> String {
        "Hello".to_string()
    }
    
    // Use &str for function parameters when possible
    fn print_length(s: &str) {
        println!("Length: {}", s.len());
    }
    
    let owned_string = String::from("Hello, World!");
    print_length(&owned_string); // Pass as &str
}

// 4. Use Vec::with_capacity for performance
fn vector_capacity() {
    // Good: pre-allocate capacity
    let mut vec = Vec::with_capacity(1000);
    for i in 0..1000 {
        vec.push(i);
    }
    
    // Avoid: letting Vec grow dynamically
    let mut vec = Vec::new();
    for i in 0..1000 {
        vec.push(i); // May cause multiple reallocations
    }
}

// 5. Use HashMap::with_capacity for performance
fn hashmap_capacity() {
    // Good: pre-allocate capacity
    let mut map = HashMap::with_capacity(100);
    for i in 0..100 {
        map.insert(i.to_string(), i * 2);
    }
    
    // Avoid: letting HashMap grow dynamically
    let mut map = HashMap::new();
    for i in 0..100 {
        map.insert(i.to_string(), i * 2);
    }
}

// 6. Use appropriate error handling
fn error_handling() {
    // Good: use Result for operations that can fail
    fn divide(a: f64, b: f64) -> Result<f64, String> {
        if b == 0.0 {
            Err("Cannot divide by zero".to_string())
        } else {
            Ok(a / b)
        }
    }
    
    // Use Option for operations that may not return a value
    fn get_first_element(vec: &[i32]) -> Option<i32> {
        vec.first().copied()
    }
    
    // Use ? operator for error propagation
    fn calculate(a: f64, b: f64, c: f64) -> Result<f64, String> {
        let result1 = divide(a, b)?;
        let result2 = divide(result1, c)?;
        Ok(result2)
    }
}

// 7. Use appropriate collection methods
fn collection_methods() {
    let numbers = vec![1, 2, 3, 4, 5];
    
    // Good: use contains
    let has_three = numbers.contains(&3);
    
    // Good: use find
    let found = numbers.iter().find(|&&x| x > 3);
    
    // Good: use any/all
    let any_even = numbers.iter().any(|&x| x % 2 == 0);
    let all_positive = numbers.iter().all(|&x| x > 0);
    
    // Good: use sum/max/min
    let sum: i32 = numbers.iter().sum();
    let max = numbers.iter().max();
    let min = numbers.iter().min();
}

// 8. Use appropriate string operations
fn string_operations() {
    let string = String::from("Hello, World!");
    
    // Good: use starts_with/ends_with
    let starts_hello = string.starts_with("Hello");
    let ends_world = string.ends_with("World!");
    
    // Good: use contains
    let contains_comma = string.contains(",");
    
    // Good: use split
    let parts: Vec<&str> = string.split(", ").collect();
    
    // Good: use trim
    let trimmed = "  hello  ".trim();
    
    // Good: use to_lowercase/to_uppercase
    let lower = string.to_lowercase();
    let upper = string.to_uppercase();
}
```

### Performance Considerations
```rust
// 1. Use appropriate data structures
fn performance_data_structures() {
    // Use arrays for fixed-size, stack-allocated data
    let matrix: [[f64; 100]; 100] = [[0.0; 100]; 100];
    
    // Use Vec for dynamic, heap-allocated data
    let mut dynamic_data = Vec::new();
    dynamic_data.push(1.0);
    
    // Use HashMap for O(1) lookup
    let mut lookup = HashMap::new();
    lookup.insert("key".to_string(), "value".to_string());
    
    // Use HashSet for O(1) membership testing
    let mut unique_values = HashSet::new();
    unique_values.insert(1);
}

// 2. Pre-allocate capacity when possible
fn pre_allocation() {
    // Good: pre-allocate Vec
    let mut vec = Vec::with_capacity(1000);
    for i in 0..1000 {
        vec.push(i);
    }
    
    // Good: pre-allocate HashMap
    let mut map = HashMap::with_capacity(100);
    for i in 0..100 {
        map.insert(i.to_string(), i);
    }
    
    // Good: pre-allocate String
    let mut string = String::with_capacity(100);
    string.push_str("Hello");
}

// 3. Use iterators efficiently
fn efficient_iterators() {
    let numbers = vec![1, 2, 3, 4, 5];
    
    // Good: use iterator chain
    let result: Vec<i32> = numbers.iter()
        .filter(|&&x| x % 2 == 0)
        .map(|x| x * 2)
        .collect();
    
    // Avoid: multiple allocations
    let evens: Vec<i32> = numbers.iter().filter(|&&x| x % 2 == 0).cloned().collect();
    let doubled: Vec<i32> = evens.iter().map(|x| x * 2).collect();
}

// 4. Use appropriate string operations
fn efficient_strings() {
    // Good: use format! for string building
    let name = "Alice";
    let age = 30;
    let formatted = format!("{} is {} years old", name, age);
    
    // Good: use String::with_capacity for building strings
    let mut string = String::with_capacity(100);
    string.push_str("Hello");
    string.push_str(", ");
    string.push_str("World!");
    
    // Avoid: repeated string concatenation
    let mut string = String::new();
    string += "Hello";
    string += ", ";
    string += "World!"; // Multiple allocations
}

// 5. Use appropriate collection methods
fn efficient_collection_methods() {
    let numbers = vec![1, 2, 3, 4, 5];
    
    // Good: use contains for membership testing
    let has_three = numbers.contains(&3);
    
    // Avoid: linear search with iter().find()
    let has_three = numbers.iter().find(|&&x| x == 3).is_some();
    
    // Good: use binary search for sorted data
    let mut sorted_numbers = numbers.clone();
    sorted_numbers.sort();
    let index = sorted_numbers.binary_search(&3);
    
    // Good: use HashSet for frequent lookups
    let number_set: HashSet<i32> = numbers.iter().cloned().collect();
    let has_three = number_set.contains(&3);
}
```

## Common Pitfalls

### Common Collection Mistakes
```rust
// 1. Array bounds errors
fn array_bounds() {
    let numbers = [1, 2, 3, 4, 5];
    
    // Bad: out of bounds access (panics at runtime)
    // let out_of_bounds = numbers[10];
    
    // Good: use get for safe access
    match numbers.get(10) {
        Some(value) => println!("Value: {}", value),
        None => println!("Index out of bounds"),
    }
}

// 2. Vector reallocation issues
fn vector_reallocation() {
    // Bad: not pre-allocating capacity
    let mut vec = Vec::new();
    for i in 0..1000 {
        vec.push(i); // May cause multiple reallocations
    }
    
    // Good: pre-allocate capacity
    let mut vec = Vec::with_capacity(1000);
    for i in 0..1000 {
        vec.push(i); // Single allocation
    }
}

// 3. String vs &str confusion
fn string_vs_str() {
    // Bad: forcing String allocation
    fn process_string(s: String) {
        println!("Processing: {}", s);
    }
    
    let string_literal = "Hello";
    process_string(string_literal.to_string()); // Unnecessary allocation
    
    // Good: accept &str when possible
    fn process_string_better(s: &str) {
        println!("Processing: {}", s);
    }
    
    process_string_better(string_literal); // No allocation
}

// 4. HashMap key ownership issues
fn hashmap_ownership() {
    let mut map = HashMap::new();
    let key = String::from("key");
    
    // Bad: inserting owned string when you could use &str
    map.insert(key.clone(), 1); // Unnecessary clone
    
    // Good: use references when possible
    map.insert(&key, 1);
    
    // Note: HashMap needs owned keys, so cloning is sometimes necessary
    // But avoid unnecessary cloning
}

// 5. Iterator invalidation
fn iterator_invalidation() {
    let mut vec = vec![1, 2, 3, 4, 5];
    
    // Bad: modifying collection while iterating
    for item in vec.iter() {
        if *item == 3 {
            vec.push(6); // Error: cannot borrow vec as mutable
        }
    }
    
    // Good: collect first, then modify
    let items_to_add: Vec<i32> = vec.iter().filter(|&&x| x > 3).cloned().collect();
    for item in items_to_add {
        vec.push(item);
    }
}

// 6. Unnecessary cloning
fn unnecessary_cloning() {
    let vec = vec![1, 2, 3, 4, 5];
    
    // Bad: unnecessary cloning
    let cloned_vec = vec.clone();
    let sum: i32 = cloned_vec.iter().sum();
    
    // Good: use references
    let sum: i32 = vec.iter().sum();
    
    // Bad: unnecessary string cloning
    let string = String::from("Hello");
    let length = string.clone().len();
    
    // Good: use reference
    let length = string.len();
}

// 7. Using unwrap() without checking
fn unsafe_unwrap() {
    let option: Option<i32> = None;
    let result: Result<i32, String> = Err("Error".to_string());
    
    // Bad: can panic
    let value = option.unwrap();
    let value = result.unwrap();
    
    // Good: handle None/Error cases
    let value = option.unwrap_or(0);
    let value = result.unwrap_or(0);
    
    // Better: use match or if let
    match option {
        Some(value) => println!("Value: {}", value),
        None => println!("No value"),
    }
}

// 8. Not using appropriate collection methods
fn inappropriate_methods() {
    let numbers = vec![1, 2, 3, 4, 5];
    
    // Bad: manual implementation of built-in methods
    let mut sum = 0;
    for number in numbers.iter() {
        sum += number;
    }
    
    // Good: use built-in method
    let sum: i32 = numbers.iter().sum();
    
    // Bad: manual search
    let mut found = false;
    for number in numbers.iter() {
        if *number == 3 {
            found = true;
            break;
        }
    }
    
    // Good: use built-in method
    let found = numbers.contains(&3);
}

// 9. String indexing issues
fn string_indexing() {
    let string = String::from("Hello");
    
    // Bad: byte indexing doesn't work for Unicode
    // let byte = string.as_bytes()[0];
    
    // Good: use chars() for character indexing
    let char = string.chars().nth(0);
    
    // Bad: direct indexing doesn't work for Unicode
    // let character = string[0];
    
    // Good: use chars() for Unicode-safe access
    let chars: Vec<char> = string.chars().collect();
    let character = chars[0];
}

// 10. HashMap key type issues
fn hashmap_key_types() {
    // Bad: using non-hashable types as keys
    // let mut map: HashMap<Vec<i32>, String> = HashMap::new(); // Vec<i32] doesn't implement Hash
    
    // Good: use hashable types as keys
    let mut map: HashMap<String, String> = HashMap::new();
    map.insert("key".to_string(), "value".to_string());
    
    // Good: use custom types with Hash and Eq traits
    #[derive(Hash, Eq, PartialEq, Debug)]
    struct CustomKey {
        id: u32,
        name: String,
    }
    
    let mut map: HashMap<CustomKey, String> = HashMap::new();
    map.insert(CustomKey { id: 1, name: "key".to_string() }, "value".to_string());
}
```

## Summary

Rust collections provide comprehensive data structure capabilities:

**Arrays:**
- Fixed-size collections known at compile time
- Stack-allocated for performance
- Type-safe with bounds checking
- Slices for flexible views

**Vectors:**
- Dynamic, growable arrays
- Heap-allocated for flexibility
- Rich iterator interface
- Efficient push/pop operations

**Strings:**
- `&str` for borrowed string slices
- `String` for owned strings
- UTF-8 encoded Unicode text
- Rich string manipulation methods

**HashMap:**
- Key-value pairs with O(1) lookup
- Hash-based storage
- Flexible key and value types
- Efficient insertion and deletion

**HashSet:**
- Unique value collections
- O(1) membership testing
- Set operations (union, intersection, difference)
- Hash-based storage

**Other Collections:**
- Tuples for heterogeneous fixed-size collections
- Option for optional values
- Result for error handling
- Custom collection types

**Key Features:**
- Memory safety with ownership
- Zero-cost abstractions
- Rich iterator patterns
- Comprehensive error handling
- Performance optimizations

**Best Practices:**
- Choose appropriate collection types
- Pre-allocate capacity when possible
- Use iterators for functional programming
- Handle errors gracefully
- Avoid unnecessary cloning
- Use appropriate string types

**Common Pitfalls:**
- Array bounds errors
- Vector reallocation issues
- String vs &str confusion
- Iterator invalidation
- Unsafe unwrap usage
- Unicode string indexing

Rust's collection system, combined with its ownership model and type system, provides safe, efficient, and expressive ways to organize and manipulate data while preventing common programming errors at compile time.
