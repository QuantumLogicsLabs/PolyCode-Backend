# Rust Functions

## Function Basics

### Function Declaration
```rust
// Basic function without parameters
fn greet() {
    println!("Hello, World!");
}

// Function with parameters
fn greet_person(name: &str) {
    println!("Hello, {}!", name);
}

// Function with return value
fn add(a: i32, b: i32) -> i32 {
    a + b
}

// Function with multiple return values using tuple
fn calculate(a: i32, b: i32) -> (i32, i32, i32) {
    (a + b, a - b, a * b)
}

// Function with no return value (unit type)
fn print_sum(a: i32, b: i32) {
    println!("Sum: {}", a + b);
}

// Explicit return with unit type
fn do_nothing() -> () {
    // Function body
}

// Function with lifetime parameter
fn longest<'a>(s1: &'a str, s2: &'a str) -> &'a str {
    if s1.len() > s2.len() {
        s1
    } else {
        s2
    }
}
```

### Function Parameters
```rust
// Parameters with type annotations
fn multiply(x: f64, y: f64) -> f64 {
    x * y
}

// Reference parameters
fn print_length(s: &String) {
    println!("Length: {}", s.len());
}

// Mutable reference parameters
fn append_exclamation(s: &mut String) {
    s.push_str("!");
}

// Slice parameters
fn sum_slice(numbers: &[i32]) -> i32 {
    numbers.iter().sum()
}

// Optional parameters using Option
fn greet_optional(name: Option<&str>) {
    match name {
        Some(n) => println!("Hello, {}!", n),
        None => println!("Hello, stranger!"),
    }
}

// Default parameters using trait implementations
trait DefaultName {
    fn default_name() -> &'static str {
        "Guest"
    }
}

fn greet_with_default<T: DefaultName + ?Sized>(name: Option<&T>) {
    let name_str = match name {
        Some(n) => "Custom",
        None => T::default_name(),
    };
    println!("Hello, {}!", name_str);
}
```

## Return Values

### Return Value Expressions
```rust
// Implicit return (no semicolon)
fn square(x: i32) -> i32 {
    x * x
}

// Explicit return with return keyword
fn cube(x: i32) -> i32 {
    return x * x * x;
}

// Return early with conditional
fn absolute_value(x: i32) -> i32 {
    if x >= 0 {
        return x;
    }
    -x
}

// Return different types based on condition
fn check_number(x: i32) -> Result<String, String> {
    if x > 0 {
        Ok("Positive".to_string())
    } else if x < 0 {
        Ok("Negative".to_string())
    } else {
        Err("Zero is not allowed".to_string())
    }
}

// Return complex types
fn create_point(x: f64, y: f64) -> (f64, f64) {
    (x, y)
}

fn create_user(name: String, age: u32) -> User {
    User {
        name,
        age,
        active: true,
    }
}

struct User {
    name: String,
    age: u32,
    active: bool,
}
```

### Early Returns and Control Flow
```rust
// Early return for validation
fn divide(a: f64, b: f64) -> Result<f64, String> {
    if b == 0.0 {
        return Err("Cannot divide by zero".to_string());
    }
    Ok(a / b)
}

// Guard clauses
fn process_user(id: u32, users: &Vec<User>) -> Result<String, String> {
    let user = match users.iter().find(|u| u.id == id) {
        Some(u) => u,
        None => return Err("User not found".to_string()),
    };
    
    if !user.active {
        return Err("User is not active".to_string());
    }
    
    Ok(format!("Processing user: {}", user.name))
}

impl User {
    fn id(&self) -> u32 {
        self.id
    }
}

// Match expressions with returns
fn describe_number(x: i32) -> String {
    match x {
        0 => "Zero".to_string(),
        1..=9 => "Single digit".to_string(),
        10..=99 => "Two digits".to_string(),
        _ => "Many digits".to_string(),
    }
}
```

## Function Pointers and Closures

### Function Pointers
```rust
// Function pointers
fn add(a: i32, b: i32) -> i32 {
    a + b
}

fn multiply(a: i32, b: i32) -> i32 {
    a * b
}

fn apply_operation(a: i32, b: i32, op: fn(i32, i32) -> i32) -> i32 {
    op(a, b)
}

// Using function pointers
fn main() {
    let result1 = apply_operation(5, 3, add);
    let result2 = apply_operation(5, 3, multiply);
    
    println!("Add: {}", result1);
    println!("Multiply: {}", result2);
}

// Function pointers as struct fields
struct Calculator {
    operation: fn(i32, i32) -> i32,
}

impl Calculator {
    fn new(operation: fn(i32, i32) -> i32) -> Self {
        Calculator { operation }
    }
    
    fn calculate(&self, a: i32, b: i32) -> i32 {
        (self.operation)(a, b)
    }
}
```

### Closures
```rust
// Basic closure
let add_one = |x| x + 1;
let result = add_one(5);

// Closure with type annotations
let multiply: fn(i32, i32) -> i32 = |x, y| x * y;

// Closure capturing environment
let factor = 2;
let multiply_by_factor = |x| x * factor;

// Closure as parameter
fn apply_closure<F>(x: i32, f: F) -> i32 
where 
    F: Fn(i32) -> i32 
{
    f(x)
}

// Using closures
let double = |x| x * 2;
let result = apply_closure(5, double);

// Closure with mutable capture
let mut counter = 0;
let mut increment = || {
    counter += 1;
    counter
};

// Closure returning a closure
fn create_adder(n: i32) -> impl Fn(i32) -> i32 {
    move |x| x + n
}

let add_five = create_adder(5);
let result = add_five(10);

// Closure traits
// Fn: can be called multiple times, doesn't mutate captured values
// FnMut: can be called multiple times, can mutate captured values
// FnOnce: can be called only once
```

## Generic Functions

### Generic Function Basics
```rust
// Generic function with type parameter
fn print_value<T: std::fmt::Display>(value: T) {
    println!("Value: {}", value);
}

// Multiple generic parameters
fn compare<T, U>(a: T, b: U) 
where 
    T: std::fmt::Display + PartialEq,
    U: std::fmt::Display + PartialEq 
{
    if a == b {
        println!("{} and {} are equal", a, b);
    } else {
        println!("{} and {} are different", a, b);
    }
}

// Generic function with lifetime
fn first_word<'a>(s: &'a str) -> &'a str {
    let bytes = s.as_bytes();
    
    for (i, &item) in bytes.iter().enumerate() {
        if item == b' ' {
            return &s[0..i];
        }
    }
    
    s
}

// Generic function with trait bounds
fn find_max<T: Ord + Copy>(slice: &[T]) -> T {
    let mut max = slice[0];
    
    for &item in slice.iter() {
        if item > max {
            max = item;
        }
    }
    
    max
}

// Generic function with multiple trait bounds
fn process_data<T, U>(data: T, processor: U) -> String 
where 
    T: std::fmt::Debug,
    U: Fn(T) -> String 
{
    println!("Processing data: {:?}", data);
    processor(data)
}
```

### Generic Structs and Impl Blocks
```rust
// Generic struct
struct Point<T> {
    x: T,
    y: T,
}

impl<T> Point<T> {
    fn new(x: T, y: T) -> Self {
        Point { x, y }
    }
    
    fn x(&self) -> &T {
        &self.x
    }
    
    fn y(&self) -> &T {
        &self.y
    }
}

// Implementation for specific type
impl Point<f64> {
    fn distance_from_origin(&self) -> f64 {
        (self.x.powi(2) + self.y.powi(2)).sqrt()
    }
}

// Generic trait
trait Summary {
    fn summarize(&self) -> String;
}

struct Article {
    title: String,
    content: String,
}

impl Summary for Article {
    fn summarize(&self) -> String {
        format!("{}: {}", self.title, &self.content[..50])
    }
}

// Generic function using trait
fn notify(item: &impl Summary) {
    println!("Breaking news! {}", item.summarize());
}

// Trait bound syntax
fn notify_trait_bound<T: Summary>(item: &T) {
    println!("Breaking news! {}", item.summarize());
}
```

## Higher-Order Functions

### Map, Filter, and Fold
```rust
// Map function
fn map<T, U, F>(vec: Vec<T>, f: F) -> Vec<U>
where 
    F: Fn(T) -> U 
{
    vec.into_iter().map(f).collect()
}

// Filter function
fn filter<T, F>(vec: Vec<T>, f: F) -> Vec<T>
where 
    F: Fn(&T) -> bool 
{
    vec.into_iter().filter(f).collect()
}

// Fold (reduce) function
fn fold<T, F>(vec: Vec<T>, initial: T, f: F) -> T
where 
    F: Fn(T, T) -> T 
{
    vec.into_iter().fold(initial, f)
}

// Using higher-order functions
fn main() {
    let numbers = vec![1, 2, 3, 4, 5];
    
    // Map
    let doubled = map(numbers.clone(), |x| x * 2);
    
    // Filter
    let evens = filter(numbers.clone(), |x| x % 2 == 0);
    
    // Fold
    let sum = fold(numbers.clone(), 0, |acc, x| acc + x);
    
    println!("Doubled: {:?}", doubled);
    println!("Evens: {:?}", evens);
    println!("Sum: {}", sum);
}
```

### Function Composition
```rust
// Function composition
fn compose<F, G, A, B, C>(f: F, g: G) -> impl Fn(A) -> C
where 
    F: Fn(A) -> B,
    G: Fn(B) -> C 
{
    move |x| g(f(x))
}

// Usage
fn add_one(x: i32) -> i32 {
    x + 1
}

fn multiply_by_two(x: i32) -> i32 {
    x * 2
}

fn main() {
    let add_then_multiply = compose(add_one, multiply_by_two);
    let result = add_then_multiply(5); // (5 + 1) * 2 = 12
    
    println!("Result: {}", result);
}

// Multiple composition
fn compose3<F, G, H, A, B, C, D>(f: F, g: G, h: H) -> impl Fn(A) -> D
where 
    F: Fn(A) -> B,
    G: Fn(B) -> C,
    H: Fn(C) -> D 
{
    move |x| h(g(f(x)))
}

fn main() {
    let pipeline = compose3(
        |x| x + 1,
        |x| x * 2,
        |x| x - 3
    );
    
    let result = pipeline(10); // ((10 + 1) * 2) - 3 = 19
    println!("Pipeline result: {}", result);
}
```

## Recursion

### Recursive Functions
```rust
// Basic recursion
fn factorial(n: u32) -> u32 {
    if n <= 1 {
        1
    } else {
        n * factorial(n - 1)
    }
}

// Fibonacci sequence (inefficient recursive version)
fn fibonacci(n: u32) -> u32 {
    match n {
        0 => 0,
        1 => 1,
        n => fibonacci(n - 1) + fibonacci(n - 2),
    }
}

// Tail recursion optimization
fn factorial_tail(n: u32, accumulator: u32) -> u32 {
    if n <= 1 {
        accumulator
    } else {
        factorial_tail(n - 1, n * accumulator)
    }
}

fn factorial_optimized(n: u32) -> u32 {
    factorial_tail(n, 1)
}

// Recursive data structures
#[derive(Debug)]
enum List {
    Cons(i32, Box<List>),
    Nil,
}

use List::{Cons, Nil};

fn sum_list(list: &List) -> i32 {
    match list {
        Cons(value, rest) => value + sum_list(rest),
        Nil => 0,
    }
}

// Tree traversal
#[derive(Debug)]
struct TreeNode {
    value: i32,
    left: Option<Box<TreeNode>>,
    right: Option<Box<TreeNode>>,
}

fn inorder_traversal(node: &TreeNode, result: &mut Vec<i32>) {
    if let Some(ref left) = node.left {
        inorder_traversal(left, result);
    }
    
    result.push(node.value);
    
    if let Some(ref right) = node.right {
        inorder_traversal(right, result);
    }
}
```

## Method Syntax

### Method Definitions
```rust
// Methods are defined in impl blocks
struct Rectangle {
    width: u32,
    height: u32,
}

impl Rectangle {
    // Associated function (static method)
    fn new(width: u32, height: u32) -> Self {
        Rectangle { width, height }
    }
    
    // Method with &self (immutable borrow)
    fn area(&self) -> u32 {
        self.width * self.height
    }
    
    // Method with &mut self (mutable borrow)
    fn set_width(&mut self, width: u32) {
        self.width = width;
    }
    
    // Method taking ownership
    fn consume(self) -> (u32, u32) {
        (self.width, self.height)
    }
    
    // Method returning a reference
    fn width_ref(&self) -> &u32 {
        &self.width
    }
}

// Multiple impl blocks for the same type
impl Rectangle {
    fn can_hold(&self, other: &Rectangle) -> bool {
        self.width > other.width && self.height > other.height
    }
}

// Method chaining
struct Circle {
    radius: f64,
}

impl Circle {
    fn new(radius: f64) -> Self {
        Circle { radius }
    }
    
    fn set_radius(mut self, radius: f64) -> Self {
        self.radius = radius;
        self
    }
    
    fn area(&self) -> f64 {
        std::f64::consts::PI * self.radius * self.radius
    }
}

fn main() {
    let circle = Circle::new(1.0)
        .set_radius(2.0);
    
    println!("Area: {}", circle.area());
}
```

### Associated Functions and Constants
```rust
struct MathUtils;

impl MathUtils {
    // Associated function (static method)
    fn add(a: i32, b: i32) -> i32 {
        a + b
    }
    
    // Associated constant
    const PI: f64 = 3.141592653589793;
    
    // Associated function returning Self
    fn zero() -> Self {
        MathUtils
    }
}

// Using associated functions
fn main() {
    let sum = MathUtils::add(5, 3);
    let pi = MathUtils::PI;
    let instance = MathUtils::zero();
    
    println!("Sum: {}", sum);
    println!("PI: {}", pi);
}

// Generic associated functions (GATs)
trait Container {
    type Item;
    
    fn new() -> Self;
    fn add(&mut self, item: Self::Item);
    fn get(&self, index: usize) -> Option<&Self::Item>;
}

struct VecContainer<T> {
    items: Vec<T>,
}

impl<T> Container for VecContainer<T> {
    type Item = T;
    
    fn new() -> Self {
        VecContainer { items: Vec::new() }
    }
    
    fn add(&mut self, item: Self::Item) {
        self.items.push(item);
    }
    
    fn get(&self, index: usize) -> Option<&Self::Item> {
        self.items.get(index)
    }
}
```

## Error Handling in Functions

### Result Type
```rust
// Functions returning Result
fn divide(a: f64, b: f64) -> Result<f64, String> {
    if b == 0.0 {
        Err("Cannot divide by zero".to_string())
    } else {
        Ok(a / b)
    }
}

// Using Result with match
fn handle_division() {
    match divide(10.0, 2.0) {
        Ok(result) => println!("Result: {}", result),
        Err(error) => println!("Error: {}", error),
    }
    
    match divide(10.0, 0.0) {
        Ok(result) => println!("Result: {}", result),
        Err(error) => println!("Error: {}", error),
    }
}

// Using ? operator
fn chain_operations() -> Result<f64, String> {
    let result1 = divide(10.0, 2.0)?;
    let result2 = divide(result1, 5.0)?;
    Ok(result2)
}

// Custom error types
#[derive(Debug)]
enum MathError {
    DivisionByZero,
    NegativeSquareRoot,
    InvalidInput,
}

impl std::fmt::Display for MathError {
    fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
        match self {
            MathError::DivisionByZero => write!(f, "Division by zero"),
            MathError::NegativeSquareRoot => write!(f, "Negative square root"),
            MathError::InvalidInput => write!(f, "Invalid input"),
        }
    }
}

fn safe_divide(a: f64, b: f64) -> Result<f64, MathError> {
    if b == 0.0 {
        Err(MathError::DivisionByZero)
    } else {
        Ok(a / b)
    }
}

fn safe_sqrt(x: f64) -> Result<f64, MathError> {
    if x < 0.0 {
        Err(MathError::NegativeSquareRoot)
    } else {
        Ok(x.sqrt())
    }
}
```

### Option Type
```rust
// Functions returning Option
fn get_first_element(vec: &[i32]) -> Option<&i32> {
    vec.first()
}

fn find_even_number(numbers: &[i32]) -> Option<i32> {
    numbers.iter().find(|&&n| n % 2 == 0).copied()
}

// Using Option with match
fn handle_option() {
    let numbers = vec![1, 3, 5, 7];
    
    match get_first_element(&numbers) {
        Some(first) => println!("First element: {}", first),
        None => println!("Empty vector"),
    }
    
    match find_even_number(&numbers) {
        Some(even) => println!("First even: {}", even),
        None => println!("No even numbers found"),
    }
}

// Option methods
fn option_methods() {
    let maybe_value = Some(5);
    
    // map
    let doubled = maybe_value.map(|x| x * 2);
    
    // and_then
    let maybe_string = maybe_value.and_then(|x| Some(x.to_string()));
    
    // unwrap_or
    let value = maybe_value.unwrap_or(0);
    
    // unwrap_or_else
    let value = maybe_value.unwrap_or_else(|| 0);
    
    // is_some and is_none
    let has_value = maybe_value.is_some();
    let is_none = maybe_value.is_none();
}
```

## Function Attributes

### Common Function Attributes
```rust
// Test attribute
#[test]
fn test_addition() {
    assert_eq!(add(2, 3), 5);
}

// Should panic attribute
#[test]
#[should_panic(expected = "Division by zero")]
fn test_divide_by_zero() {
    divide(10.0, 0.0).unwrap();
}

// Ignore attribute
#[test]
#[ignore]
fn slow_test() {
    // This test will be ignored
}

// Inline attribute
#[inline]
fn fast_function(x: i32) -> i32 {
    x * 2
}

// No_mangle attribute for FFI
#[no_mangle]
pub extern "C" fn hello_from_rust() {
    println!("Hello from Rust!");
}

// Deprecated attribute
#[deprecated(since = "1.0.0", note = "Use new_function instead")]
fn old_function() {
    println!("This is deprecated");
}

// Allow attribute
#[allow(dead_code)]
fn unused_function() {
    println!("This function is never used");
}

// Doc attribute
/// Adds two numbers together
/// 
/// # Arguments
/// 
/// * `a` - First number
/// * `b` - Second number
/// 
/// # Returns
/// 
/// The sum of a and b
#[doc]
fn documented_function(a: i32, b: i32) -> i32 {
    a + b
}

// Conditional compilation
#[cfg(target_os = "windows")]
fn windows_specific_function() {
    println!("Windows-specific code");
}

#[cfg(not(target_os = "windows"))]
fn non_windows_function() {
    println!("Non-Windows code");
}

// Feature gates
#[cfg(feature = "advanced")]
fn advanced_feature() {
    println!("Advanced feature enabled");
}
```

## Best Practices

### Function Design Best Practices
```rust
// 1. Use descriptive function names
fn calculate_circle_area(radius: f64) -> f64 { // Good
fn calc(r: f64) -> f64 { // Bad - too short
fn calculate_the_area_of_a_circle_given_its_radius(radius: f64) -> f64 { // Bad - too long
}

// 2. Keep functions small and focused
fn process_user_data(user: &User) -> Result<ProcessedUser, Error> { // Good - single responsibility
fn process_user_data_and_send_email_and_log_to_database(user: &User) -> Result<(), Error> { // Bad - multiple responsibilities
}

// 3. Use appropriate parameter types
fn print_user_name(user: &User) { // Good - borrow when possible
fn print_user_name(user: User) { // Bad - unnecessary ownership
fn print_user_name(user: &mut User) { // Bad - mutable when not needed
}

// 4. Return appropriate types
fn find_user(id: u32) -> Option<User> { // Good - Option for optional values
fn find_user(id: u32) -> Result<User, Error> { // Good - Result for operations that can fail
fn find_user(id: u32) -> User { // Bad - panic if not found

// 5. Use meaningful parameter names
fn calculate_distance(point1: (f64, f64), point2: (f64, f64)) -> f64 { // Good
fn calculate_distance(p1: (f64, f64), p2: (f64, f64)) -> f64 { // Bad - unclear parameter names

// 6. Document public functions
/// Calculates the distance between two points in 2D space.
/// 
/// # Arguments
/// 
/// * `point1` - First point as (x, y) coordinates
/// * `point2` - Second point as (x, y) coordinates
/// 
/// # Returns
/// 
/// The Euclidean distance between the two points
/// 
/// # Examples
/// 
/// ```
/// let distance = calculate_distance((0.0, 0.0), (3.0, 4.0));
/// assert_eq!(distance, 5.0);
/// ```
fn calculate_distance(point1: (f64, f64), point2: (f64, f64)) -> f64 {
    let dx = point2.0 - point1.0;
    let dy = point2.1 - point1.1;
    (dx * dx + dy * dy).sqrt()
}

// 7. Use generics for reusable code
fn find_max<T: Ord + Copy>(slice: &[T]) -> T { // Good - generic and reusable
fn find_max_int(slice: &[i32]) -> i32 { // Bad - only works for i32

// 8. Handle errors gracefully
fn read_file_content(path: &str) -> Result<String, std::io::Error> { // Good - returns Result
fn read_file_content(path: &str) -> String { // Bad - panics on error

// 9. Use builder pattern for complex construction
struct Config {
    timeout: u64,
    retries: u32,
    debug: bool,
}

impl Config {
    fn new() -> ConfigBuilder {
        ConfigBuilder::new()
    }
}

struct ConfigBuilder {
    timeout: u64,
    retries: u32,
    debug: bool,
}

impl ConfigBuilder {
    fn new() -> Self {
        ConfigBuilder {
            timeout: 30000,
            retries: 3,
            debug: false,
        }
    }
    
    fn timeout(mut self, timeout: u64) -> Self {
        self.timeout = timeout;
        self
    }
    
    fn retries(mut self, retries: u32) -> Self {
        self.retries = retries;
        self
    }
    
    fn debug(mut self, debug: bool) -> Self {
        self.debug = debug;
        self
    }
    
    fn build(self) -> Config {
        Config {
            timeout: self.timeout,
            retries: self.retries,
            debug: self.debug,
        }
    }
}

// 10. Use appropriate visibility
pub mod utils {
    pub fn public_function() { // Public function
        println!("Public function");
    }
    
    fn private_function() { // Private function
        println!("Private function");
    }
    
    pub(crate) fn crate_visible_function() { // Visible within crate
        println!("Crate visible function");
    }
}
```

### Performance Considerations
```rust
// 1. Use inline for small, frequently called functions
#[inline]
fn add_one(x: i32) -> i32 {
    x + 1
}

// 2. Avoid unnecessary allocations
fn process_string_slice(s: &str) -> String { // Good - takes &str
fn process_string_owned(s: String) -> String { // Bad - forces allocation

// 3. Use iterators instead of loops when possible
fn sum_vec(vec: &[i32]) -> i32 {
    vec.iter().sum() // Good - uses iterator
}

fn sum_vec_loop(vec: &[i32]) -> i32 {
    let mut sum = 0;
    for &item in vec { // Less efficient
        sum += item;
    }
    sum
}

// 4. Use const functions for compile-time evaluation
const fn multiply_const(a: i32, b: i32) -> i32 {
    a * b
}

const RESULT: i32 = multiply_const(6, 7);

// 5. Use specialized functions for hot paths
#[inline(always)]
fn critical_path_function(x: i32) -> i32 {
    x * 2 + 1
}

// 6. Avoid boxing when possible
fn process_value(value: i32) -> i32 { // Good - no allocation
fn process_value_boxed(value: Box<i32>) -> i32 { // Bad - unnecessary allocation

// 7. Use appropriate data structures
fn lookup_with_hashmap(map: &std::collections::HashMap<String, i32>, key: &str) -> Option<i32> { // Good - O(1)
fn lookup_with_vec(vec: &[(String, i32)], key: &str) -> Option<i32> { // Bad - O(n)
```

## Common Pitfalls

### Common Function Mistakes
```rust
// 1. Forgetting to mark mutable parameters as mutable
fn increment(x: i32) -> i32 {
    x + 1 // Correct
}

fn increment_mut(x: i32) -> i32 {
    x += 1; // Error: cannot assign to immutable argument
    x
}

// 2. Mixing owned and borrowed values incorrectly
fn process_string(s: String) {
    println!("{}", s);
}

fn main() {
    let s = String::from("hello");
    process_string(s);
    println!("{}", s); // Error: value borrowed after move
}

// 3. Incorrect lifetime annotations
fn longest_string(s1: &str, s2: &str) -> &str { // Error: missing lifetime
    if s1.len() > s2.len() {
        s1
    } else {
        s2
    }
}

fn longest_string_fixed<'a>(s1: &'a str, s2: &'a str) -> &'a str { // Correct
    if s1.len() > s2.len() {
        s1
    } else {
        s2
    }
}

// 4. Using panic instead of Result
fn divide_panic(a: f64, b: f64) -> f64 {
    if b == 0.0 {
        panic!("Division by zero"); // Bad - panics in production
    }
    a / b
}

fn divide_result(a: f64, b: f64) -> Result<f64, String> { // Good
    if b == 0.0 {
        Err("Division by zero".to_string())
    } else {
        Ok(a / b)
    }
}

// 5. Recursive functions without tail optimization
fn factorial_bad(n: u32) -> u32 {
    if n <= 1 {
        1
    } else {
        n * factorial_bad(n - 1) // Can cause stack overflow
    }
}

fn factorial_good(n: u32) -> u32 {
    factorial_tail(n, 1)
}

fn factorial_tail(n: u32, acc: u32) -> u32 {
    if n <= 1 {
        acc
    } else {
        factorial_tail(n - 1, n * acc)
    }
}

// 6. Not handling all enum variants
fn process_option(opt: Option<i32>) -> i32 {
    match opt {
        Some(value) => value,
        // Missing None case - compiler will warn
    }
}

fn process_option_fixed(opt: Option<i32>) -> i32 {
    match opt {
        Some(value) => value,
        None => 0, // Handle all cases
    }
}

// 7. Using unwrap() without checking
fn dangerous_unwrap() -> String {
    let result: Result<String, String> = Err("Error".to_string());
    result.unwrap() // Panics!
}

fn safe_unwrap() -> String {
    let result: Result<String, String> = Err("Error".to_string());
    result.unwrap_or_else(|e| format!("Default: {}", e)) // Safe
}

// 8. Incorrect trait bounds
fn generic_function<T>(x: T) -> T {
    x + x // Error: cannot add T
}

fn generic_function_fixed<T: std::ops::Add<Output = T>>(x: T) -> T {
    x + x
}

// 9. Not considering move semantics
fn consume_vector(vec: Vec<i32>) -> i32 {
    vec.iter().sum()
}

fn main() {
    let vec = vec![1, 2, 3];
    let sum = consume_vector(vec);
    println!("Sum: {}", sum);
    println!("Vector: {:?}", vec); // Error: vec was moved
}

// 10. Overusing unwrap() in production code
fn production_code() -> String {
    let file_content = std::fs::read_to_string("config.txt").unwrap(); // Bad
    file_content
}

fn production_code_safe() -> Result<String, std::io::Error> { // Good
    let file_content = std::fs::read_to_string("config.txt")?;
    Ok(file_content)
}
```

## Summary

Rust functions provide powerful and safe programming capabilities:

**Function Basics:**
- Function declaration with parameters and return types
- Type annotations for clarity
- Multiple return values with tuples
- Lifetime parameters for references

**Return Values:**
- Implicit returns (no semicolon)
- Explicit returns with `return` keyword
- Early returns for validation
- Result and Option types for error handling

**Advanced Features:**
- Function pointers and closures
- Generic functions with type parameters
- Higher-order functions
- Method syntax in impl blocks

**Error Handling:**
- Result type for recoverable errors
- Option type for optional values
- `?` operator for error propagation
- Custom error types

**Best Practices:**
- Descriptive function names
- Small, focused functions
- Appropriate parameter types
- Meaningful return types
- Proper error handling
- Documentation for public functions

**Performance:**
- Inline attributes for optimization
- Iterator methods for efficiency
- Const functions for compile-time evaluation
- Appropriate data structures

**Common Pitfalls:**
- Ownership and borrowing issues
- Missing lifetime annotations
- Panic vs Result misuse
- Incomplete pattern matching
- Stack overflow in recursion

Rust's function system, combined with its ownership model and type system, enables writing safe, efficient, and maintainable code while preventing common programming errors at compile time.
