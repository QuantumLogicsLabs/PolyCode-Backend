# Rust Traits

## Trait Basics

### Defining and Implementing Traits
```rust
// Basic trait definition
trait Summary {
    fn summarize(&self) -> String;
}

// Implementing trait for a struct
struct Article {
    title: String,
    author: String,
    content: String,
}

impl Summary for Article {
    fn summarize(&self) -> String {
        format!("{}, by {} ({})", 
                self.title, 
                self.author, 
                self.content.len())
    }
}

// Using trait methods
fn using_traits() {
    let article = Article {
        title: String::from("Rust Traits"),
        author: String::from("John Doe"),
        content: String::from("Traits are awesome!"),
    };
    
    println!("Summary: {}", article.summarize());
}

// Trait with default implementation
trait DefaultSummary {
    fn summarize(&self) -> String {
        String::from("(Read more...)")
    }
}

struct Tweet {
    username: String,
    content: String,
}

impl DefaultSummary for Tweet {
    // Uses default implementation
}

impl DefaultSummary for Article {
    fn summarize(&self) -> String {
        format!("{}, by {}", self.title, self.author)
    }
}

fn default_implementations() {
    let tweet = Tweet {
        username: String::from("rustacean"),
        content: String::from("Learning traits!"),
    };
    
    let article = Article {
        title: String::from("Advanced Rust"),
        author: String::from("Jane Smith"),
        content: String::from("Deep dive into traits"),
    };
    
    println!("Tweet summary: {}", tweet.summarize());
    println!("Article summary: {}", article.summarize());
}
```

### Trait Parameters
```rust
// Traits as parameters
fn notify(item: &impl Summary) {
    println!("Breaking news! {}", item.summarize());
}

fn trait_parameters() {
    let article = Article {
        title: String::from("News Flash"),
        author: String::from("Reporter"),
        content: String::from("Important news here"),
    };
    
    notify(&article);
}

// Trait bound syntax
fn notify_trait_bound<T: Summary>(item: &T) {
    println!("Breaking news! {}", item.summarize());
}

fn trait_bounds() {
    let article = Article {
        title: String::from("Another Story"),
        author: String::from("Writer"),
        content: String::from("Content goes here"),
    };
    
    notify_trait_bound(&article);
}

// Multiple trait bounds
fn multiple_trait_bounds<T: Summary + Display>(item: &T) {
    println!("Item: {}", item);
    println!("Summary: {}", item.summarize());
}

fn multiple_bounds() {
    use std::fmt::Display;
    
    impl Display for Article {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "{} by {}", self.title, self.author)
        }
    }
    
    let article = Article {
        title: String::from("Display Article"),
        author: String::from("Author Name"),
        content: String::from("Article content"),
    };
    
    multiple_trait_bounds(&article);
}

// where clause
fn where_clause<T>(item: &T) -> String 
where 
    T: Summary + Display
{
    format!("{}: {}", item, item.summarize())
}

fn using_where() {
    use std::fmt::Display;
    
    impl Display for Article {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "{} by {}", self.title, self.author)
        }
    }
    
    let article = Article {
        title: String::from("Where Clause Article"),
        author: String::from("Where Author"),
        content: String::from("Where content"),
    };
    
    println!("{}", where_clause(&article));
}
```

### Return Types
```rust
// Returning types that implement traits
fn returns_summarizable() -> impl Summary {
    Tweet {
        username: String::from("horse_ebooks"),
        content: String::from("of course, as you probably already know, people"),
    }
}

fn trait_return_types() {
    let tweet = returns_summarizable();
    println!("Tweet summary: {}", tweet.summarize());
}

// Using trait bounds with return types
fn returns_summarizable_with_bound<T: Summary>(item: T) -> impl Summary {
    item
}

fn trait_return_with_bounds() {
    let article = Article {
        title: String::from("Return Article"),
        author: String::from("Return Author"),
        content: String::from("Return content"),
    };
    
    let returned = returns_summarizable_with_bound(article);
    println!("Returned summary: {}", returned.summarize());
}

// Conditional trait implementation
fn conditional_trait_return(condition: bool) -> impl Summary {
    if condition {
        Tweet {
            username: String::from("conditional"),
            content: String::from("Conditional tweet"),
        }
    } else {
        Article {
            title: String::from("Conditional Article"),
            author: String::from("Conditional Author"),
            content: String::from("Conditional content"),
        }
    }
}

fn conditional_return() {
    let item1 = conditional_trait_return(true);
    let item2 = conditional_trait_return(false);
    
    println!("Conditional 1: {}", item1.summarize());
    println!("Conditional 2: {}", item2.summarize());
}
```

## Standard Library Traits

### Display and Debug
```rust
use std::fmt::{Display, Debug, Formatter, Result};

// Implementing Display trait
struct Point {
    x: f64,
    y: f64,
}

impl Display for Point {
    fn fmt(&self, f: &mut Formatter) -> Result {
        write!(f, "({}, {})", self.x, self.y)
    }
}

// Implementing Debug trait
#[derive(Debug)]
struct Line {
    start: Point,
    end: Point,
}

impl Display for Line {
    fn fmt(&self, f: &mut Formatter) -> Result {
        write!(f, "Line from {} to {}", self.start, self.end)
    }
}

fn display_debug_traits() {
    let point = Point { x: 3.14, y: 2.71 };
    let line = Line {
        start: Point { x: 0.0, y: 0.0 },
        end: point,
    };
    
    println!("Display: {}", point);
    println!("Debug: {:?}", point);
    println!("Display: {}", line);
    println!("Debug: {:?}", line);
}

// Custom Debug implementation
struct Custom {
    name: String,
    value: i32,
}

impl Debug for Custom {
    fn fmt(&self, f: &mut Formatter) -> Result {
        write!(f, "Custom {{ name: {}, value: {} }}", self.name, self.value)
    }
}

fn custom_debug() {
    let custom = Custom {
        name: String::from("test"),
        value: 42,
    };
    
    println!("Custom debug: {:?}", custom);
}
```

### Clone and Copy
```rust
// Clone trait
#[derive(Debug, Clone)]
struct Person {
    name: String,
    age: u32,
}

impl Person {
    fn new(name: String, age: u32) -> Self {
        Person { name, age }
    }
    
    fn birthday(&mut self) {
        self.age += 1;
    }
}

fn clone_trait() {
    let person1 = Person::new("Alice".to_string(), 25);
    let person2 = person1.clone();
    
    person1.birthday();
    
    println!("Person1: {:?}", person1);
    println!("Person2: {:?}", person2);
}

// Copy trait (requires Clone)
#[derive(Debug, Clone, Copy)]
struct Coordinates {
    x: i32,
    y: i32,
}

fn copy_trait() {
    let coord1 = Coordinates { x: 10, y: 20 };
    let coord2 = coord1; // coord1 is copied, not moved
    
    println!("Coord1: {:?}", coord1);
    println!("Coord2: {:?}", coord2);
}

// Manual Clone implementation
#[derive(Debug)]
struct ManualClone {
    data: Vec<i32>,
}

impl Clone for ManualClone {
    fn clone(&self) -> Self {
        ManualClone {
            data: self.data.clone(),
        }
    }
}

fn manual_clone_implementation() {
    let original = ManualClone {
        data: vec![1, 2, 3, 4, 5],
    };
    
    let cloned = original.clone();
    
    println!("Original: {:?}", original);
    println!("Cloned: {:?}", cloned);
}
```

### PartialEq and Eq
```rust
// PartialEq trait
#[derive(Debug)]
struct Book {
    title: String,
    author: String,
    pages: u32,
}

impl PartialEq for Book {
    fn eq(&self, other: &Self) -> bool {
        self.title == other.title && self.author == other.author
    }
}

fn partial_eq_trait() {
    let book1 = Book {
        title: String::from("The Rust Book"),
        author: String::from("Steve Klabnik"),
        pages: 500,
    };
    
    let book2 = Book {
        title: String::from("The Rust Book"),
        author: String::from("Steve Klabnik"),
        pages: 501,
    };
    
    let book3 = Book {
        title: String::from("Different Book"),
        author: String::from("Steve Klabnik"),
        pages: 500,
    };
    
    println!("book1 == book2: {}", book1 == book2);
    println!("book1 == book3: {}", book1 == book3);
}

// Eq trait (requires PartialEq)
impl Eq for Book {}

fn eq_trait() {
    let book1 = Book {
        title: String::from("The Rust Book"),
        author: String::from("Steve Klabnik"),
        pages: 500,
    };
    
    let book2 = Book {
        title: String::from("The Rust Book"),
        author: String::from("Steve Klabnik"),
        pages: 500,
    };
    
    // Eq allows for more efficient comparisons
    let books_equal = book1 == book2;
    println!("Books are equal: {}", books_equal);
}

// PartialOrd and Ord traits
use std::cmp::{PartialOrd, Ord, Ordering};

impl PartialOrd for Book {
    fn partial_cmp(&self, other: &Self) -> Option<Ordering> {
        Some(self.title.cmp(&other.title))
    }
}

impl Ord for Book {
    fn cmp(&self, other: &Self) -> Ordering {
        self.title.cmp(&other.title)
    }
}

fn ordering_traits() {
    let mut books = vec![
        Book {
            title: String::from("C Book"),
            author: String::from("C Author"),
            pages: 300,
        },
        Book {
            title: String::from("A Book"),
            author: String::from("A Author"),
            pages: 200,
        },
        Book {
            title: String::from("B Book"),
            author: String::from("B Author"),
            pages: 400,
        },
    ];
    
    books.sort();
    
    println!("Sorted books:");
    for book in &books {
        println!("  {}", book.title);
    }
}
```

### Hash
```rust
use std::hash::{Hash, Hasher};

// Hash trait
#[derive(Debug)]
struct Product {
    id: u32,
    name: String,
    price: f64,
}

impl Hash for Product {
    fn hash<H: Hasher>(&self, state: &mut H) {
        self.id.hash(state);
        self.name.hash(state);
        // Don't hash f64 as it may have precision issues
        (self.price as u64).hash(state);
    }
}

impl PartialEq for Product {
    fn eq(&self, other: &Self) -> bool {
        self.id == other.id && self.name == other.name && self.price == other.price
    }
}

impl Eq for Product {}

fn hash_trait() {
    use std::collections::HashMap;
    
    let product1 = Product {
        id: 1,
        name: String::from("Laptop"),
        price: 999.99,
    };
    
    let product2 = Product {
        id: 2,
        name: String::from("Phone"),
        price: 699.99,
    };
    
    let mut products = HashMap::new();
    products.insert(product1, "Electronics");
    products.insert(product2, "Mobile");
    
    println!("Products: {:?}", products);
}
```

## Advanced Traits

### Trait Objects
```rust
// Trait objects
trait Draw {
    fn draw(&self);
}

struct Circle {
    radius: f64,
}

impl Draw for Circle {
    fn draw(&self) {
        println!("Drawing circle with radius: {}", self.radius);
    }
}

struct Rectangle {
    width: f64,
    height: f64,
}

impl Draw for Rectangle {
    fn draw(&self) {
        println!("Drawing rectangle {}x{}", self.width, self.height);
    }
}

fn trait_objects() {
    let circle = Circle { radius: 5.0 };
    let rectangle = Rectangle { width: 10.0, height: 20.0 };
    
    let shapes: Vec<Box<dyn Draw>> = vec![
        Box::new(circle),
        Box::new(rectangle),
    ];
    
    for shape in shapes {
        shape.draw();
    }
}

// Trait objects with lifetime
trait Process {
    fn process(&self) -> String;
}

struct Processor {
    name: String,
}

impl Process for Processor {
    fn process(&self) -> String {
        format!("Processing with {}", self.name)
    }
}

fn trait_object_lifetime() {
    let processor = Processor {
        name: String::from("Main Processor"),
    };
    
    let trait_object: &dyn Process = &processor;
    println!("{}", trait_object.process());
}
```

### Generic Trait Bounds
```rust
// Generic trait bounds
trait Printable {
    fn print(&self);
}

fn print_all<T: Printable>(items: &[T]) {
    for item in items {
        item.print();
    }
}

struct Document {
    title: String,
    content: String,
}

impl Printable for Document {
    fn print(&self) {
        println!("Document: {}", self.title);
        println!("Content: {}", self.content);
    }
}

fn generic_trait_bounds() {
    let doc = Document {
        title: String::from("Important Document"),
        content: String::from("This is important content"),
    };
    
    print_all(&[doc]);
}

// Multiple trait bounds
fn process_and_print<T: Printable + Clone>(item: &T) -> T {
    item.print();
    item.clone()
}

fn multiple_trait_bounds() {
    let doc = Document {
        title: String::from("Cloneable Document"),
        content: String::from("This can be cloned"),
    };
    
    let cloned = process_and_print(&doc);
    cloned.print();
}

// Where clause for complex bounds
fn complex_bounds<T, U>(item1: &T, item2: &U) -> String
where 
    T: Printable + Clone,
    U: Printable + std::fmt::Debug,
{
    let cloned = item1.clone();
    cloned.print();
    format!("Debug: {:?}", item2)
}

fn complex_where_clause() {
    let doc = Document {
        title: String::from("Complex Document"),
        content: String::from("Complex content"),
    };
    
    let result = complex_bounds(&doc, &doc);
    println!("Result: {}", result);
}
```

### Associated Types
```rust
// Associated types
trait Iterator {
    type Item;
    
    fn next(&mut self) -> Option<Self::Item>;
}

struct Counter {
    current: u32,
    max: u32,
}

impl Counter {
    fn new(max: u32) -> Self {
        Counter { current: 0, max }
    }
}

impl Iterator for Counter {
    type Item = u32;
    
    fn next(&mut self) -> Option<Self::Item> {
        if self.current < self.max {
            let current = self.current;
            self.current += 1;
            Some(current)
        } else {
            None
        }
    }
}

fn associated_types() {
    let mut counter = Counter::new(5);
    
    while let Some(value) = counter.next() {
        println!("Counter value: {}", value);
    }
}

// Generic associated types (GATs)
trait StreamingIterator {
    type Item<'a> where Self: 'a;
    
    fn next<'a>(&'a mut self) -> Option<Self::Item<'a>>;
}

struct StringStreamer {
    data: String,
    position: usize,
}

impl StringStreamer {
    fn new(data: String) -> Self {
        StringStreamer { data, position: 0 }
    }
}

impl StreamingIterator for StringStreamer {
    type Item<'a> = &'a str where Self: 'a;
    
    fn next<'a>(&'a mut self) -> Option<Self::Item<'a>> {
        if self.position < self.data.len() {
            let start = self.position;
            let end = self.data[start..].find(' ').map(|i| start + i).unwrap_or(self.data.len());
            let word = &self.data[start..end];
            self.position = if end < self.data.len() { end + 1 } else { end };
            Some(word)
        } else {
            None
        }
    }
}

fn generic_associated_types() {
    let mut streamer = StringStreamer::new("Hello world from Rust".to_string());
    
    while let Some(word) = streamer.next() {
        println!("Word: {}", word);
    }
}
```

### Operator Overloading
```rust
use std::ops::{Add, Sub, Mul, Div};

// Operator overloading
#[derive(Debug, Clone, Copy)]
struct Vector2D {
    x: f64,
    y: f64,
}

impl Vector2D {
    fn new(x: f64, y: f64) -> Self {
        Vector2D { x, y }
    }
    
    fn magnitude(&self) -> f64 {
        (self.x * self.x + self.y * self.y).sqrt()
    }
}

impl Add for Vector2D {
    type Output = Self;
    
    fn add(self, other: Self) -> Self {
        Vector2D {
            x: self.x + other.x,
            y: self.y + other.y,
        }
    }
}

impl Sub for Vector2D {
    type Output = Self;
    
    fn sub(self, other: Self) -> Self {
        Vector2D {
            x: self.x - other.x,
            y: self.y - other.y,
        }
    }
}

impl Mul<f64> for Vector2D {
    type Output = Self;
    
    fn mul(self, scalar: f64) -> Self {
        Vector2D {
            x: self.x * scalar,
            y: self.y * scalar,
        }
    }
}

impl Div<f64> for Vector2D {
    type Output = Self;
    
    fn div(self, scalar: f64) -> Self {
        Vector2D {
            x: self.x / scalar,
            y: self.y / scalar,
        }
    }
}

fn operator_overloading() {
    let v1 = Vector2D::new(3.0, 4.0);
    let v2 = Vector2D::new(1.0, 2.0);
    
    let sum = v1 + v2;
    let diff = v1 - v2;
    let scaled = v1 * 2.0;
    let divided = v1 / 2.0;
    
    println!("V1: {:?}", v1);
    println!("V2: {:?}", v2);
    println!("Sum: {:?}", sum);
    println!("Difference: {:?}", diff);
    println!("Scaled: {:?}", scaled);
    println!("Divided: {:?}", divided);
    println!("Magnitude: {}", v1.magnitude());
}

// Index and IndexMut traits
use std::ops::{Index, IndexMut};

struct Matrix {
    data: Vec<Vec<i32>>,
    rows: usize,
    cols: usize,
}

impl Matrix {
    fn new(rows: usize, cols: usize) -> Self {
        Matrix {
            data: vec![vec![0; cols]; rows],
            rows,
            cols,
        }
    }
    
    fn set(&mut self, row: usize, col: usize, value: i32) {
        self.data[row][col] = value;
    }
}

impl Index<(usize, usize)> for Matrix {
    type Output = i32;
    
    fn index(&self, (row, col): (usize, usize)) -> &Self::Output {
        &self.data[row][col]
    }
}

impl IndexMut<(usize, usize)> for Matrix {
    fn index_mut(&mut self, (row, col): (usize, usize)) -> &mut Self::Output {
        &mut self.data[row][col]
    }
}

fn index_traits() {
    let mut matrix = Matrix::new(3, 3);
    
    matrix.set(0, 0, 1);
    matrix.set(1, 1, 5);
    matrix.set(2, 2, 9);
    
    println!("Matrix[0,0]: {}", matrix[(0, 0)]);
    println!("Matrix[1,1]: {}", matrix[(1, 1)]);
    
    matrix[(2, 2)] = 10;
    println!("Matrix[2,2]: {}", matrix[(2, 2)]);
}
```

## Trait Objects vs Generics

### Performance Comparison
```rust
// Generic function (monomorphization)
fn generic_summarize<T: Summary>(item: &T) -> String {
    item.summarize()
}

// Trait object function (dynamic dispatch)
fn trait_object_summarize(item: &dyn Summary) -> String {
    item.summarize()
}

fn performance_comparison() {
    let article = Article {
        title: String::from("Performance Article"),
        author: String::from("Perf Author"),
        content: String::from("Performance comparison content"),
    };
    
    let tweet = Tweet {
        username: String::from("perf_user"),
        content: String::from("Performance tweet"),
    };
    
    // Generic version (static dispatch)
    let start = std::time::Instant::now();
    for _ in 0..1000 {
        let _ = generic_summarize(&article);
    }
    let generic_time = start.elapsed();
    
    // Trait object version (dynamic dispatch)
    let start = std::time::Instant::now();
    for _ in 0..1000 {
        let _ = trait_object_summarize(&article);
    }
    let trait_object_time = start.elapsed();
    
    println!("Generic time: {:?}", generic_time);
    println!("Trait object time: {:?}", trait_object_time);
}

// When to use each
fn when_to_use_which() {
    // Use generics when:
    // - You know all possible types at compile time
    // - Performance is critical
    // - You want static dispatch
    
    // Use trait objects when:
    // - You need to store different types in a collection
    // - Types are determined at runtime
    // - You need dynamic dispatch
    
    let items: Vec<Box<dyn Summary>> = vec![
        Box::new(Article {
            title: String::from("Mixed Article"),
            author: String::from("Mixed Author"),
            content: String::from("Mixed content"),
        }),
        Box::new(Tweet {
            username: String::from("mixed_user"),
            content: String::from("Mixed tweet"),
        }),
    ];
    
    for item in items {
        println!("Summary: {}", item.summarize());
    }
}
```

### Object Safety
```rust
// Object-safe traits
trait ObjectSafe {
    fn method(&self) -> i32;
}

trait NotObjectSafe {
    fn method(&self) -> Self; // Returns Self - not object-safe
    fn static_method(); // Static method - not object-safe
}

// Object-safe implementation
struct SafeStruct {
    value: i32,
}

impl ObjectSafe for SafeStruct {
    fn method(&self) -> i32 {
        self.value
    }
}

fn object_safety() {
    let safe = SafeStruct { value: 42 };
    
    // This works - trait object
    let trait_object: &dyn ObjectSafe = &safe;
    println!("Trait object method: {}", trait_object.method());
    
    // This doesn't work - not object-safe
    // let not_safe_object: &dyn NotObjectSafe = &safe;
}

// Making traits object-safe
trait ObjectSafeVersion {
    fn method(&self) -> i32;
}

impl ObjectSafeVersion for SafeStruct {
    fn method(&self) -> i32 {
        self.value
    }
}

fn making_object_safe() {
    let safe = SafeStruct { value: 100 };
    
    let trait_object: &dyn ObjectSafeVersion = &safe;
    println!("Object-safe method: {}", trait_object.method());
}
```

## Trait System Features

### Supertraits
```rust
// Supertraits
trait OutlinePrint {
    fn outline_print(&self);
}

trait Summary {
    fn summarize(&self) -> String;
}

// Summary is a supertrait of OutlinePrint
impl<T: Summary> OutlinePrint for T {
    fn outline_print(&self) {
        println!("Summary: {}", self.summarize());
    }
}

fn supertraits() {
    let article = Article {
        title: String::from("Supertrait Article"),
        author: String::from("Supertrait Author"),
        content: String::from("Supertrait content"),
    };
    
    article.outline_print();
}

// Multiple supertraits
trait Printable {
    fn print(&self);
}

trait DebugPrint: Printable + std::fmt::Debug {
    fn debug_print(&self) {
        println!("Debug: {:?}", self);
        self.print();
    }
}

impl Printable for Article {
    fn print(&self) {
        println!("Article: {}", self.title);
    }
}

impl DebugPrint for Article {}

fn multiple_supertraits() {
    let article = Article {
        title: String::from("Multi Supertrait Article"),
        author: String::from("Multi Author"),
        content: String::from("Multi content"),
    };
    
    article.debug_print();
}
```

### Marker Traits
```rust
// Marker traits (no methods)
trait Send: 'static {}
trait Sync: 'static {}

// Most types implement Send and Sync automatically
fn marker_traits() {
    // These are marker traits that indicate
    // Send: type can be sent between threads
    // Sync: type can be safely shared between threads
    
    // Custom marker trait
    trait MyMarker {}
    
    struct MyStruct;
    
    impl MyMarker for MyStruct {}
    
    let my_struct = MyStruct;
    
    // Use marker trait as bound
    fn process<T: MyMarker>(item: T) {
        println!("Processing marked item");
    }
    
    process(my_struct);
}

// PhantomData for marker traits
use std::marker::PhantomData;

struct Phantom<T> {
    _phantom: PhantomData<T>,
}

impl<T> Phantom<T> {
    fn new() -> Self {
        Phantom {
            _phantom: PhantomData,
        }
    }
}

fn phantom_data() {
    let phantom_i32: Phantom<i32> = Phantom::new();
    let phantom_string: Phantom<String> = Phantom::new();
    
    // Used to implement marker traits
    // or to carry type information at runtime
}
```

### Trait Aliases
```rust
// Trait aliases
use std::fmt::{Debug, Display};

trait Print: Debug + Display {}

impl<T: Debug + Display> Print for T {}

fn trait_aliases() {
    struct MyStruct {
        name: String,
    }
    
    impl Debug for MyStruct {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "MyStruct {{ name: {} }}", self.name)
        }
    }
    
    impl Display for MyStruct {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "{}", self.name)
        }
    }
    
    let my_struct = MyStruct {
        name: String::from("Test"),
    };
    
    // Can use Print trait alias
    fn print_item<T: Print>(item: &T) {
        println!("Debug: {:?}", item);
        println!("Display: {}", item);
    }
    
    print_item(&my_struct);
}

// Complex trait aliases
trait IteratorExt: Iterator {
    fn collect_vec(self) -> Vec<Self::Item>
    where 
        Self: Sized,
    {
        self.collect()
    }
}

impl<I: Iterator> IteratorExt for I {}

fn trait_alias_extensions() {
    let numbers = vec![1, 2, 3, 4, 5];
    let evens: Vec<i32> = numbers.into_iter()
        .filter(|&n| n % 2 == 0)
        .collect_vec();
    
    println!("Even numbers: {:?}", evens);
}
```

## Best Practices

### Trait Design Best Practices
```rust
// 1. Keep traits focused and cohesive
trait focused_trait() {
    trait Read {
        fn read(&mut self, buffer: &mut [u8]) -> Result<usize, std::io::Error>;
    }
    
    trait Write {
        fn write(&mut self, buffer: &[u8]) -> Result<usize, std::io::Error>;
    }
    
    // Avoid: too many responsibilities
    trait ReadWrite {
        fn read(&mut self, buffer: &mut [u8]) -> Result<usize, std::io::Error>;
        fn write(&mut self, buffer: &[u8]) -> Result<usize, std::io::Error>;
        fn flush(&mut self) -> Result<(), std::io::Error>;
        fn seek(&mut self, pos: std::io::SeekFrom) -> Result<u64, std::io::Error>;
    }
}

// 2. Provide default implementations when possible
trait default_implementations() {
    trait Process {
        fn process(&self) -> String {
            format!("Processed: {:?}", self)
        }
        
        fn validate(&self) -> bool {
            true
        }
    }
    
    struct Data {
        value: i32,
    }
    
    impl Process for Data {
        // Uses default process implementation
        fn validate(&self) -> bool {
            self.value > 0
        }
    }
}

// 3. Use associated types for type relationships
trait associated_types() {
    trait Container {
        type Item;
        
        fn get(&self, index: usize) -> Option<&Self::Item>;
        fn len(&self) -> usize;
    }
    
    struct VecContainer<T> {
        data: Vec<T>,
    }
    
    impl<T> Container for VecContainer<T> {
        type Item = T;
        
        fn get(&self, index: usize) -> Option<&Self::Item> {
            self.data.get(index)
        }
        
        fn len(&self) -> usize {
            self.data.len()
        }
    }
}

// 4. Use generic parameters for flexibility
trait generic_parameters() {
    trait Comparable<T> {
        fn compare(&self, other: &T) -> std::cmp::Ordering;
    }
    
    struct Point {
        x: f64,
        y: f64,
    }
    
    impl Comparable<Point> for Point {
        fn compare(&self, other: &Point) -> std::cmp::Ordering {
            self.x.partial_cmp(&other.x).unwrap_or(std::cmp::Ordering::Equal)
        }
    }
}

// 5. Implement common standard library traits
trait standard_traits() {
    use std::fmt::{Debug, Display};
    use std::cmp::{PartialEq, Eq};
    
    #[derive(Debug, PartialEq, Eq)]
    struct MyStruct {
        name: String,
    }
    
    impl Display for MyStruct {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "{}", self.name)
        }
    }
}

// 6. Use trait objects for runtime polymorphism
trait trait_objects() {
    trait Draw {
        fn draw(&self);
    }
    
    struct Circle {
        radius: f64,
    }
    
    impl Draw for Circle {
        fn draw(&self) {
            println!("Drawing circle with radius: {}", self.radius);
        }
    }
    
    struct Rectangle {
        width: f64,
        height: f64,
    }
    
    impl Draw for Rectangle {
        fn draw(&self) {
            println!("Drawing rectangle {}x{}", self.width, self.height);
        }
    }
    
    fn draw_all(shapes: Vec<Box<dyn Draw>>) {
        for shape in shapes {
            shape.draw();
        }
    }
    
    let shapes: Vec<Box<dyn Draw>> = vec![
        Box::new(Circle { radius: 5.0 }),
        Box::new(Rectangle { width: 10.0, height: 20.0 }),
    ];
    
    draw_all(shapes);
}

// 7. Use generics for compile-time polymorphism
trait generics() {
    trait Process {
        fn process(&self) -> String;
    }
    
    struct Data {
        value: i32,
    }
    
    impl Process for Data {
        fn process(&self) -> String {
            format!("Processed data: {}", self.value)
        }
    }
    
    fn process_item<T: Process>(item: &T) -> String {
        item.process()
    }
    
    let data = Data { value: 42 };
    let result = process_item(&data);
    println!("{}", result);
}
```

### Trait Implementation Best Practices
```rust
// 1. Implement traits logically
trait logical_implementation() {
    trait Display {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result;
    }
    
    struct Person {
        name: String,
        age: u32,
    }
    
    impl Display for Person {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "{} ({} years old)", self.name, self.age)
        }
    }
}

// 2. Follow naming conventions
trait naming_conventions() {
    // Trait names should be descriptive and use PascalCase
    trait Serializable {
        fn serialize(&self) -> String;
        fn deserialize(data: &str) -> Result<Self, serde_json::Error>
        where 
            Self: Sized;
    }
    
    // Method names should be clear and use snake_case
    trait Calculator {
        fn add(&self, a: i32, b: i32) -> i32;
        fn subtract(&self, a: i32, b: i32) -> i32;
        fn multiply(&self, a: i32, b: i32) -> i32;
        fn divide(&self, a: i32, b: i32) -> Result<i32, String>;
    }
}

// 3. Handle edge cases appropriately
trait edge_cases() {
    trait Divide {
        fn divide(&self, a: i32, b: i32) -> Result<i32, String>;
    }
    
    struct SafeCalculator;
    
    impl Divide for SafeCalculator {
        fn divide(&self, a: i32, b: i32) -> Result<i32, String> {
            if b == 0 {
                Err("Cannot divide by zero".to_string())
            } else {
                Ok(a / b)
            }
        }
    }
}

// 4. Use appropriate error handling
trait error_handling() {
    trait Process {
        type Error;
        
        fn process(&self) -> Result<String, Self::Error>;
    }
    
    struct SafeProcess;
    
    impl Process for SafeProcess {
        type Error = String;
        
        fn process(&self) -> Result<String, Self::Error> {
            Ok("Processed successfully".to_string())
        }
    }
}

// 5. Consider performance implications
trait performance() {
    // Use generics for better performance (static dispatch)
    fn generic_process<T: Process>(item: &T) -> String {
        item.process().unwrap_or_else(|e| format!("Error: {}", e))
    }
    
    // Use trait objects when needed (dynamic dispatch)
    fn trait_object_process(item: &dyn Process) -> String {
        item.process().unwrap_or_else(|e| format!("Error: {}", e))
    }
    
    trait Process {
        fn process(&self) -> Result<String, String>;
    }
}

// 6. Document trait contracts
trait documentation() {
    /// A trait for serializing and deserializing data
    /// 
    /// # Examples
    /// ```
    /// use serde::{Serialize, Deserialize};
    /// 
    /// #[derive(Serialize, Deserialize)]
    /// struct Data {
    ///     value: i32,
    /// }
    /// 
    /// let data = Data { value: 42 };
    /// let serialized = data.serialize();
    /// ```
    trait Serializable {
        /// Serialize the object to a string
        /// 
        /// # Returns
        /// A string representation of the object
        fn serialize(&self) -> String;
        
        /// Deserialize from a string
        /// 
        /// # Arguments
        /// * `data` - The string to deserialize from
        /// 
        /// # Returns
        /// The deserialized object or an error
        fn deserialize(data: &str) -> Result<Self, serde_json::Error>
        where 
            Self: Sized;
    }
}
```

## Common Pitfalls

### Common Trait Mistakes
```rust
// 1. Not implementing required traits
trait missing_traits() {
    struct MyStruct {
        value: i32,
    }
    
    // Bad: not implementing common traits
    // Good: implement Display, Debug, PartialEq, etc.
    use std::fmt::{Debug, Display};
    use std::cmp::PartialEq;
    
    #[derive(Debug, PartialEq)]
    struct BetterStruct {
        value: i32,
    }
    
    impl Display for BetterStruct {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "{}", self.value)
        }
    }
}

// 2. Using trait objects incorrectly
trait trait_object_mistakes() {
    trait Process {
        fn process(&self) -> String;
        fn get_type(&self) -> &'static str; // Returns static str
    }
    
    struct Processor;
    
    impl Process for Processor {
        fn process(&self) -> String {
            "Processed".to_string()
        }
        
        fn get_type(&self) -> &'static str {
            "Processor"
        }
    }
    
    // This works
    fn use_trait_object(item: &dyn Process) {
        println!("Type: {}", item.get_type());
        println!("Result: {}", item.process());
    }
    
    let processor = Processor;
    use_trait_object(&processor);
}

// 3. Not understanding object safety
trait object_safety_mistakes() {
    trait NotObjectSafe {
        fn new() -> Self; // Static method
        fn clone(&self) -> Self; // Returns Self
    }
    
    trait ObjectSafe {
        fn process(&self) -> String;
    }
    
    struct MyStruct;
    
    impl ObjectSafe for MyStruct {
        fn process(&self) -> String {
            "Processed".to_string()
        }
    }
    
    // This works
    let trait_object: &dyn ObjectSafe = &MyStruct;
    
    // This doesn't work
    // let not_safe: &dyn NotObjectSafe = &MyStruct;
}

// 4. Not using appropriate trait bounds
trait bound_mistakes() {
    // Bad: too restrictive
    fn too_restrictive<T: Clone + Send + Sync + Display + Debug>(item: T) {
        println!("{}", item);
    }
    
    // Good: only what's needed
    fn just_enough<T: Display>(item: T) {
        println!("{}", item);
    }
    
    // Bad: not restrictive enough
    fn not_restrictive_enough<T>(item: T) {
        // Can't do anything with item
    }
    
    // Good: specify what you need
    fn restrictive_enough<T: Display>(item: T) {
        println!("{}", item);
    }
}

// 5. Not considering performance
trait performance_mistakes() {
    trait Process {
        fn process(&self) -> String;
    }
    
    struct Data {
        value: i32,
    }
    
    impl Process for Data {
        fn process(&self) -> String {
            format!("Data: {}", self.value)
        }
    }
    
    // Bad: using trait objects in tight loop
    fn slow_processing(items: Vec<Box<dyn Process>>) {
        for item in items {
            println!("{}", item.process()); // Dynamic dispatch overhead
        }
    }
    
    // Good: using generics in tight loop
    fn fast_processing<T: Process>(items: Vec<T>) {
        for item in items {
            println!("{}", item.process()); // Static dispatch
        }
    }
}

// 6. Not implementing associated types correctly
trait associated_type_mistakes() {
    trait Container {
        type Item;
        
        fn get(&self, index: usize) -> Option<&Self::Item>;
    }
    
    struct MyContainer<T> {
        data: Vec<T>,
    }
    
    // Bad: incorrect associated type
    impl<T> Container for MyContainer<T> {
        type Item = String; // Should be T
        
        fn get(&self, index: usize) -> Option<&Self::Item> {
            self.data.get(index).map(|_| &String::new()) // Wrong
        }
    }
    
    // Good: correct associated type
    impl<T> Container for MyContainer<T> {
        type Item = T;
        
        fn get(&self, index: usize) -> Option<&Self::Item> {
            self.data.get(index)
        }
    }
}

// 7. Not using supertraits appropriately
trait supertrait_mistakes() {
    trait Read {
        fn read(&mut self) -> String;
    }
    
    trait Write {
        fn write(&mut self, data: &str);
    }
    
    // Bad: duplicating functionality
    trait ReadWrite {
        fn read(&mut self) -> String;
        fn write(&mut self, data: &str);
    }
    
    // Good: using supertraits
    trait ReadWrite: Read + Write {}
    
    // Or implementing one trait in terms of another
    trait Write {
        fn write(&mut self, data: &str);
    }
    
    trait ReadWrite: Write {
        fn read(&mut self) -> String;
    }
}

// 8. Not considering trait coherence
trait coherence_mistakes() {
    trait Process {
        fn process(&self) -> String;
    }
    
    struct Data;
    
    impl Process for Data {
        fn process(&self) -> String {
            "Data processed".to_string()
        }
    }
    
    // This is fine - different parameter types
    impl Process for &Data {
        fn process(&self) -> String {
            "Reference processed".to_string()
        }
    }
    
    // This would cause coherence issues
    // impl<T> Process for T where T: Process {
    //     fn process(&self) -> String {
    //         "Generic processed".to_string()
    //     }
    // }
}
```

## Summary

Rust traits provide powerful abstraction capabilities:

**Core Concepts:**
- Trait definitions and implementations
- Default implementations
- Trait parameters and bounds
- Associated types and methods

**Standard Library Traits:**
- Display and Debug for formatting
- Clone and Copy for duplication
- PartialEq and Eq for equality
- PartialOrd and Ord for ordering
- Hash for hashing

**Advanced Features:**
- Trait objects for dynamic dispatch
- Generic trait bounds
- Supertraits for trait inheritance
- Operator overloading
- Marker traits

**Trait System:**
- Associated types for type relationships
- Generic associated types (GATs)
- Trait aliases for convenience
- Object safety requirements
- PhantomData for type markers

**Performance Considerations:**
- Static dispatch with generics
- Dynamic dispatch with trait objects
- Monomorphization overhead
- Runtime polymorphism costs

**Best Practices:**
- Keep traits focused and cohesive
- Provide default implementations
- Use appropriate trait bounds
- Implement common standard traits
- Consider performance implications
- Document trait contracts

**Common Pitfalls:**
- Not implementing required traits
- Misusing trait objects
- Object safety violations
- Inappropriate trait bounds
- Performance issues
- Associated type mistakes
- Supertrait misuse
- Coherence rule violations

Rust's trait system enables powerful abstraction, code reuse, and polymorphism while maintaining type safety and performance. The combination of traits, generics, and trait objects provides flexible ways to write generic, reusable code that can be optimized for performance when needed.
