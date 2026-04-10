# Rust Variables and Data Types

## Variables

### Variable Declaration
```rust
// Immutable variable (default)
const MAX_USERS: u32 = 1000;
let name = "Rust";

// Mutable variable
let mut counter = 0;
counter += 1;

// Variable shadowing
let x = 5;
let x = x + 1; // x is now 6, previous x is shadowed

{
    let x = x * 2; // x is 12 in this scope
}
// x is back to 6
```

### Variable Naming Conventions
```rust
// Snake case for variables and functions
let user_name = "Alice";
let user_age = 25;
let is_active = true;

// SCREAMING_SNAKE_CASE for constants
const MAX_CONNECTIONS: usize = 100;
const DEFAULT_TIMEOUT: u64 = 30000;

// PascalCase for types and structs
struct UserAccount {
    name: String,
    age: u32,
}

// Use descriptive names
let user_authentication_token = "abc123"; // Good
let token = "abc123"; // Too generic

// Use verbs for functions
fn calculate_total(items: &[i32]) -> i32 { // Good
fn calc(items: &[i32]) -> i32 { // Too short
```

## Data Types

### Primitive Types
```rust
// Signed integers
let small_number: i8 = -128;        // -128 to 127
let medium_number: i16 = -32768;     // -32,768 to 32,767
let large_number: i32 = -2147483648;  // -2,147,483,648 to 2,147,483,647
let huge_number: i64 = -9223372036854775808; // -9,223,372,036,854,775,808 to 9,223,372,036,854,775,807

// Unsigned integers
let byte: u8 = 255;                  // 0 to 255
let short: u16 = 65535;              // 0 to 65,535
let int: u32 = 4294967295;          // 0 to 4,294,967,295
let long: u64 = 18446744073709551615; // 0 to 18,446,744,073,709,551,615

// Floating point numbers
let float32: f32 = 3.14159;           // IEEE 754 single precision
let float64: f64 = 2.718281828459045;  // IEEE 754 double precision

// Boolean
let is_ready: bool = true;
let is_done: bool = false;

// Character
let letter: char = 'A';
let emoji: char = '😊';

// Unit type (only one value)
let unit: () = ();

// Never type (for functions that never return)
fn never_returns() -> ! {
    panic!("This function never returns");
}
```

### Compound Types
```rust
// Tuples (fixed-size collections of different types)
let point: (i32, i32) = (10, 20);
let coordinates: (f64, f64, f64) = (1.0, 2.0, 3.0);

// Access tuple elements
let x = point.0;
let y = point.1;

// Destructure tuples
let (x, y) = point;

// Arrays (fixed-size collections of same type)
let numbers: [i32; 5] = [1, 2, 3, 4, 5];
let months: [&str; 12] = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// Access array elements
let first = numbers[0];
let last = numbers[numbers.len() - 1];

// Slices (dynamically-sized views into arrays)
let slice = &numbers[1..4]; // Elements at indices 1, 2, 3
let all = &numbers[..];      // All elements
let last_three = &numbers[2..]; // Elements at indices 2, 3, 4

// Strings
let string_literal = "Hello, Rust!";
let string_from_literal: &str = "Hello, Rust!";
let owned_string: String = String::from("Hello, Rust!");
let string_from_str: String = "Hello, Rust!".to_string();

// String operations
let mut s = String::from("Hello");
s.push_str(", World!");
s.push('!');

// Format strings
let name = "Alice";
let age = 25;
let formatted = format!("{} is {} years old", name, age);
```

## Type Inference

### Type Inference Examples
```rust
// Rust can infer types in most cases
let number = 42;        // i32 by default
let pi = 3.14159;      // f64 by default
let greeting = "Hello"; // &str

// Type annotations are sometimes needed
let number: u32 = 42;   // Explicitly u32
let pi: f32 = 3.14159;   // Explicitly f32

// Type inference with functions
fn add(a: i32, b: i32) -> i32 {
    a + b
}

let result = add(5, 3); // result is inferred as i32

// Type coercion
let integer: i32 = 5;
let float: f64 = integer as f64; // Explicit type casting
```

### Generic Types
```rust
// Generic function
fn print_value<T: std::fmt::Display>(value: T) {
    println!("Value: {}", value);
}

print_value(42);           // Works with i32
print_value(3.14);         // Works with f64
print_value("Hello");       // Works with &str

// Generic struct
struct Point<T> {
    x: T,
    y: T,
}

impl<T> Point<T> {
    fn new(x: T, y: T) -> Self {
        Point { x, y }
    }
}

let int_point = Point::new(10, 20);
let float_point = Point::new(1.5, 2.5);
```

## Constants and Static Variables

### Constants
```rust
// Constants must have explicitly specified types
const MAX_POINTS: usize = 100;
const PI: f64 = 3.141592653589793;
const APP_NAME: &str = "MyApp";

// Constants can be used in patterns
match value {
    0 => println!("Zero"),
    MAX_POINTS => println!("Maximum"),
    _ => println!("Other"),
}

// Constants in impl blocks
struct Circle {
    radius: f64,
}

impl Circle {
    const PI: f64 = 3.141592653589793;
    
    fn area(&self) -> f64 {
        Self::PI * self.radius * self.radius
    }
}
```

### Static Variables
```rust
// Static variables live for the entire program duration
static GLOBAL_COUNTER: std::sync::atomic::AtomicUsize = 
    std::sync::atomic::AtomicUsize::new(0);

static CONFIG: &str = "production";

// Static variables can be mutable (unsafe)
static mut COUNTER: i32 = 0;

fn increment_counter() {
    unsafe {
        COUNTER += 1;
    }
}

// Static variables in functions
fn get_app_name() -> &'static str {
    static APP_NAME: &str = "MyApp";
    APP_NAME
}
```

## Shadowing and Scope

### Variable Shadowing
```rust
fn demonstrate_shadowing() {
    let x = 5;
    println!("Outer x: {}", x); // 5
    
    {
        let x = 10;
        println!("Inner x: {}", x); // 10
        
        {
            let x = 15;
            println!("Deepest x: {}", x); // 15
        }
        println!("Inner x: {}", x); // 10
    }
    println!("Outer x: {}", x); // 5
}

// Parameter shadowing
fn print_number(x: i32) {
    let x = x * 2; // Shadows parameter
    println!("Doubled: {}", x);
}

// Function parameter shadowing
fn demonstrate_parameter_shadowing() {
    let x = 5;
    print_number(x); // Prints "Doubled: 10"
    println!("Original x: {}", x); // Still 5
}
```

## Pattern Matching with Variables

### Destructuring Assignment
```rust
// Destructure tuples
let point = (3, 5);
let (x, y) = point;

// Destructure arrays
let numbers = [1, 2, 3, 4, 5];
let [first, second, ..rest] = numbers;

// Destructure structs
struct Person {
    name: String,
    age: u32,
}

let person = Person {
    name: "Alice".to_string(),
    age: 30,
};

let Person { name, age } = person;

// Destructure with renaming
let Person { name: person_name, age: person_age } = person;

// Destructure enums
enum Color {
    Red,
    Green,
    Blue,
    RGB(u8, u8, u8),
}

let color = Color::RGB(255, 0, 0);
match color {
    Color::Red => println!("Red"),
    Color::Green => println!("Green"),
    Color::Blue => println!("Blue"),
    Color::RGB(r, g, b) => println!("RGB({}, {}, {})", r, g, b),
}
```

### Pattern Matching in Functions
```rust
fn process_coordinates((x, y): (i32, i32)) -> i32 {
    x + y
}

fn process_array([first, second, ..rest]: [i32; 5]) -> i32 {
    first + second + rest.iter().sum()
}

fn process_person(Person { name, age }: Person) -> String {
    format!("{} is {} years old", name, age)
}

// Irrefutable patterns
fn print_first_element(slice: &[i32]) {
    match slice {
        [first, ..] => println!("First element: {}", first),
        [] => println!("Empty slice"),
    }
}
```

## Type Conversions

### Primitive Type Conversions
```rust
// Numeric conversions
let integer: i32 = 42;
let float: f64 = integer as f64;
let back_to_int: i32 = float as i32;

// Lossy conversions
let large_float: f64 = 123.456;
let truncated: i32 = large_float as i32; // 123

// Checked conversions (safe alternative)
use std::convert::TryFrom;

let safe_float: f64 = 123.456;
match i32::try_from(safe_float) {
    Ok(value) => println!("Converted: {}", value),
    Err(e) => println!("Conversion failed: {}", e),
}

// From and Into traits
impl From<i32> for f64 {
    fn from(value: i32) -> Self {
        value as f64
    }
}

let converted: f64 = f64::from(42);

impl Into<i32> for f64 {
    fn into(self) -> i32 {
        self as i32
    }
}

let converted_back: i32 = 42.0.into();
```

### String Conversions
```rust
// From &str to String
let string_slice = "Hello";
let owned_string: String = string_slice.to_string();

// From String to &str
let owned_string = String::from("Hello");
let string_slice: &str = owned_string.as_str();

// From bytes to String
let bytes = b"Hello";
let string_from_bytes = String::from_utf8(bytes.to_vec());

// From String to bytes
let string_to_bytes = owned_string.into_bytes();

// Character conversions
let char_val: char = 'A';
let string_from_char: String = char_val.to_string();

let string_val = "A";
let char_from_string: char = string_val.chars().next().unwrap();
```

## Memory and Ownership

### Ownership Rules
```rust
// 1. Each value in Rust has a variable that's its owner
let s1 = String::from("hello");
let s2 = s1; // s1 is moved to s2, s1 is no longer valid

// 2. There can only be one owner at a time
// let s3 = s1; // This would cause a compile error
let s3 = s2; // This works

// 3. When the owner goes out of scope, the value is dropped
fn demonstrate_ownership() {
    let s = String::from("hello"); // s owns the string
    println!("{}", s); // s is dropped here
}

// Copy trait
fn copy_trait_example() {
    let x = 5;
    let y = x; // x is copied, y is a new i32
    println!("x: {}, y: {}", x, y); // Both are valid
    
    let s1 = String::from("hello");
    let s2 = s1.clone(); // Explicit clone for non-copy types
    println!("s1: {}, s2: {}", s1, s2); // Both are valid
}

// Borrowing
fn borrowing_example() {
    let s1 = String::from("hello");
    let len = calculate_length(&s1); // Borrow s1
    println!("Length: {}", len);
    println!("Original: {}", s1); // s1 is still valid
}

fn calculate_length(s: &String) -> usize {
    s.len()
}
```

## Best Practices

### Variable Best Practices
```rust
// 1. Use `const` for constants that never change
const MAX_RETRIES: u32 = 3;
const API_VERSION: &str = "v1.0";

// 2. Use descriptive names
let user_authentication_token = "abc123";
let database_connection_pool_size = 10;

// 3. Prefer immutable variables
let items = vec![1, 2, 3]; // Immutable
let mut items = vec![1, 2, 3]; // Mutable only when needed

// 4. Use type annotations when type inference is unclear
let items = vec![1, 2, 3]; // Type is Vec<i32>
let items: Vec<i32> = vec![1, 2, 3]; // Explicit when helpful

// 5. Use appropriate integer sizes
let count: u32 = 100; // Use unsigned when value is never negative
let temperature: i32 = -10; // Use signed when value can be negative
let large_number: u64 = 1000000; // Use larger types when needed

// 6. Use meaningful variable names
let user_name = "Alice"; // Good
let n = "Alice"; // Too short

// 7. Group related variables
struct UserConfig {
    name: String,
    email: String,
    age: u32,
    is_active: bool,
}

// 8. Use type aliases for complex types
type UserId = u64;
type Result<T> = std::result::Result<T, Box<dyn std::error::Error>>;

// 9. Use constants for magic numbers
const SECONDS_IN_MINUTE: u64 = 60;
const MINUTES_IN_HOUR: u64 = 60;
const HOURS_IN_DAY: u64 = 24;

// 10. Use default values where appropriate
#[derive(Debug)]
struct Config {
    timeout: u64,
    retries: u32,
    debug: bool,
}

impl Default for Config {
    fn default() -> Self {
        Config {
            timeout: 30000,
            retries: 3,
            debug: false,
        }
    }
}

fn create_config() -> Config {
    Config::default()
}

fn create_custom_config() -> Config {
    Config {
        timeout: 60000,
        retries: 5,
        debug: true,
    }
}
```

### Common Pitfalls
```rust
// 1. Forgetting to use `mut` for mutable variables
// let x = 5;
// x = 10; // Error: cannot assign twice to immutable variable

// Correct:
let mut x = 5;
x = 10;

// 2. Using moved values
// let s1 = String::from("hello");
// let s2 = s1;
// println!("{}", s1); // Error: value borrowed after move

// Correct:
let s1 = String::from("hello");
let s2 = s1.clone();
println!("s1: {}, s2: {}", s1, s2);

// 3. Type inference issues
// let x = 5.0; // Inferred as f64
// let y = x / 2; // Error: cannot divide f64 by i32

// Correct:
let x: f64 = 5.0;
let y = x / 2.0;

// 4. String vs &str confusion
// fn process_string(s: String) {
//     println!("Length: {}", s.len());
// }

// This forces the caller to allocate a String
// process_string("hello".to_string());

// Better:
fn process_string(s: &str) {
    println!("Length: {}", s.len());
}

// This allows the caller to use &str directly
process_string("hello");

// 5. Array vs Vec confusion
// let arr = [1, 2, 3];
// arr.push(4); // Error: arrays have fixed size

// Correct:
let mut vec = vec![1, 2, 3];
vec.push(4);

// 6. Panic vs Result
// let index = vec.len();
// let value = vec[index]; // Panic if index out of bounds

// Correct:
let index = vec.len();
let value = vec.get(index); // Returns Option<&i32>

// 7. Uninitialized variables
// let x: i32;
// println!("{}", x); // Error: use of possibly uninitialized variable

// Correct:
let x: i32 = 0;
println!("{}", x);

// Or better:
let x = 0;
println!("{}", x);
```

## Summary

Rust variables and data types provide:

**Variable Declaration:**
- `let` for immutable variables (default)
- `let mut` for mutable variables
- `const` for compile-time constants
- `static` for program-wide variables

**Primitive Types:**
- Signed integers: `i8`, `i16`, `i32`, `i64`, `isize`
- Unsigned integers: `u8`, `u16`, `u32`, `u64`, `usize`
- Floating point: `f32`, `f64`
- Boolean: `bool`
- Character: `char`
- Unit type: `()`
- Never type: `!`

**Compound Types:**
- Tuples: fixed-size heterogeneous collections
- Arrays: fixed-size homogeneous collections
- Slices: dynamic views into arrays
- Strings: `&str` (borrowed) and `String` (owned)

**Key Features:**
- Type inference for cleaner code
- Pattern matching for destructuring
- Ownership system for memory safety
- Borrowing for safe references
- Generic types for flexibility

**Best Practices:**
- Use descriptive naming conventions
- Prefer immutable variables
- Use appropriate integer sizes
- Use constants for magic numbers
- Handle errors gracefully with Result
- Avoid panics in production code
- Use type annotations when helpful

Rust's type system and ownership model provide memory safety without sacrificing performance, making it ideal for systems programming where safety and performance are critical.
