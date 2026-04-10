# Rust Structs and Enums

## Structs

### Basic Structs
```rust
// Basic struct definition
struct User {
    username: String,
    email: String,
    age: u32,
    active: bool,
}

// Creating instances
fn create_user() {
    let user1 = User {
        username: String::from("alice123"),
        email: String::from("alice@example.com"),
        age: 25,
        active: true,
    };
    
    println!("User: {} ({})", user1.username, user1.email);
    
    // Using field init shorthand
    let username = String::from("bob456");
    let email = String::from("bob@example.com");
    
    let user2 = User {
        username,
        email,
        age: 30,
        active: false,
    };
    
    println!("User: {} ({})", user2.username, user2.email);
}

// Accessing struct fields
fn access_fields() {
    let user = User {
        username: String::from("charlie789"),
        email: String::from("charlie@example.com"),
        age: 35,
        active: true,
    };
    
    // Accessing fields
    println!("Username: {}", user.username);
    println!("Email: {}", user.email);
    println!("Age: {}", user.age);
    println!("Active: {}", user.active);
    
    // Mutable access
    let mut user = User {
        username: String::from("diana012"),
        email: String::from("diana@example.com"),
        age: 28,
        active: true,
    };
    
    user.age = 29;
    user.active = false;
    
    println!("Updated age: {}", user.age);
    println!("Updated active: {}", user.active);
}

// Tuple structs
fn tuple_structs() {
    // Tuple struct definition
    struct Color(i32, i32, i32);
    
    struct Point(f64, f64);
    
    struct Kilometers(i32);
    
    // Creating instances
    let black = Color(0, 0, 0);
    let origin = Point(0.0, 0.0);
    let distance = Kilometers(5);
    
    // Accessing tuple struct fields
    let Color(r, g, b) = black;
    println!("RGB: {}, {}, {}", r, g, b);
    
    let Point(x, y) = origin;
    println!("Coordinates: {}, {}", x, y);
    
    let Kilometers(km) = distance;
    println!("Distance: {} km", km);
}

// Unit-like structs
fn unit_like_structs() {
    // Unit-like struct definition
    struct AlwaysEqual;
    
    struct Marker;
    
    impl AlwaysEqual {
        fn eq(&self, other: &AlwaysEqual) -> bool {
            true
        }
    }
    
    // Creating instances
    let always_equal = AlwaysEqual;
    let marker = Marker;
    
    // Unit-like structs are often used for markers
    println!("AlwaysEqual created");
    println!("Marker created");
}
```

### Struct Methods
```rust
// Methods in impl blocks
struct Rectangle {
    width: u32,
    height: u32,
}

impl Rectangle {
    // Associated function (static method)
    fn new(width: u32, height: u32) -> Self {
        Rectangle { width, height }
    }
    
    // Instance method (immutable borrow)
    fn area(&self) -> u32 {
        self.width * self.height
    }
    
    // Instance method (mutable borrow)
    fn set_width(&mut self, width: u32) {
        self.width = width;
    }
    
    // Instance method (taking ownership)
    fn consume(self) -> (u32, u32) {
        (self.width, self.height)
    }
    
    // Method returning a reference
    fn width_ref(&self) -> &u32 {
        &self.width
    }
    
    // Method returning a mutable reference
    fn width_mut(&mut self) -> &mut u32 {
        &mut self.width
    }
}

fn struct_methods() {
    let mut rect = Rectangle::new(10, 20);
    
    // Calling methods
    println!("Area: {}", rect.area());
    
    rect.set_width(15);
    println!("New area: {}", rect.area());
    
    let width_ref = rect.width_ref();
    println!("Width via reference: {}", width_ref);
    
    // Method chaining
    let area = Rectangle::new(10, 20).area();
    println!("Chained area: {}", area);
}

// Multiple impl blocks
impl Rectangle {
    fn perimeter(&self) -> u32 {
        2 * (self.width + self.height)
    }
    
    fn is_square(&self) -> bool {
        self.width == self.height
    }
    
    fn can_hold(&self, other: &Rectangle) -> bool {
        self.width > other.width && self.height > other.height
    }
}

fn multiple_impl_blocks() {
    let rect = Rectangle::new(10, 10);
    
    println!("Perimeter: {}", rect.perimeter());
    println!("Is square: {}", rect.is_square());
    
    let other = Rectangle::new(5, 5);
    println!("Can hold other: {}", rect.can_hold(&other));
}

// Associated constants and functions
impl Rectangle {
    // Associated constant
    const DEFAULT_SIZE: (u32, u32) = (10, 10);
    
    // Associated function returning Self
    fn square(size: u32) -> Self {
        Rectangle {
            width: size,
            height: size,
        }
    }
    
    // Associated function with multiple parameters
    fn from_dimensions(width: u32, height: u32) -> Result<Self, String> {
        if width == 0 || height == 0 {
            Err("Dimensions must be positive".to_string())
        } else {
            Ok(Rectangle { width, height })
        }
    }
}

fn associated_functions() {
    let default_rect = Rectangle::new(
        Rectangle::DEFAULT_SIZE.0,
        Rectangle::DEFAULT_SIZE.1
    );
    
    let square = Rectangle::square(15);
    
    let result = Rectangle::from_dimensions(20, 30);
    
    match result {
        Ok(rect) => println!("Created rectangle: {}x{}", rect.width, rect.height),
        Err(e) => println!("Error: {}", e),
    }
    
    println!("Default: {}x{}", default_rect.width, default_rect.height);
    println!("Square: {}x{}", square.width, square.height);
}
```

### Structs with Lifetimes
```rust
// Struct with lifetime annotations
#[derive(Debug)]
struct RefStruct<'a> {
    name: &'a str,
    reference: &'a i32,
}

fn lifetime_structs() {
    let name = String::from("Alice");
    let number = 42;
    
    let ref_struct = RefStruct {
        name: &name,
        reference: &number,
    };
    
    println!("RefStruct: {:?}", ref_struct);
}

// Multiple lifetimes
#[derive(Debug)]
struct MultiLifetime<'a, 'b> {
    string_ref: &'a str,
    vector_ref: &'b Vec<i32>,
}

fn multiple_lifetimes() {
    let string = String::from("Hello");
    let vector = vec![1, 2, 3];
    
    let multi = MultiLifetime {
        string_ref: &string,
        vector_ref: &vector,
    };
    
    println!("MultiLifetime: {:?}", multi);
}

// Lifetime elision
#[derive(Debug)]
struct User<'a> {
    name: &'a str,
    age: u32,
}

impl<'a> User<'a> {
    fn new(name: &'a str, age: u32) -> Self {
        User { name, age }
    }
    
    fn get_name(&self) -> &str {
        self.name
    }
    
    fn update_age(&mut self, age: u32) {
        self.age = age;
    }
}

fn lifetime_elision() {
    let name = String::from("Bob");
    let mut user = User::new(&name, 25);
    
    println!("User: {:?}", user);
    println!("Name: {}", user.get_name());
    
    user.update_age(26);
    println!("Updated user: {:?}", user);
}
```

### Trait Implementations
```rust
// Implementing traits for structs
#[derive(Debug, PartialEq, Clone)]
struct Point {
    x: f64,
    y: f64,
}

impl Point {
    fn new(x: f64, y: f64) -> Self {
        Point { x, y }
    }
    
    fn distance_from_origin(&self) -> f64 {
        (self.x.powi(2) + self.y.powi(2)).sqrt()
    }
    
    fn distance_to(&self, other: &Point) -> f64 {
        ((self.x - other.x).powi(2) + (self.y - other.y).powi(2)).sqrt()
    }
}

// Implement Display trait
impl std::fmt::Display for Point {
    fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
        write!(f, "({}, {})", self.x, self.y)
    }
}

// Implement From traits
impl From<(f64, f64)> for Point {
    fn from((x, y): (f64, f64)) -> Self {
        Point { x, y }
    }
}

impl From<Point> for (f64, f64) {
    fn from(point: Point) -> Self {
        (point.x, point.y)
    }
}

// Implement Default trait
impl Default for Point {
    fn default() -> Self {
        Point { x: 0.0, y: 0.0 }
    }
}

// Implement Add trait
impl std::ops::Add for Point {
    type Output = Point;
    
    fn add(self, other: Point) -> Point {
        Point {
            x: self.x + other.x,
            y: self.y + other.y,
        }
    }
}

fn trait_implementations() {
    let point1 = Point::new(1.0, 2.0);
    let point2 = Point::new(3.0, 4.0);
    
    // Debug formatting
    println!("Debug: {:?}", point1);
    
    // Display formatting
    println!("Display: {}", point1);
    
    // PartialEq
    println!("Equal: {}", point1 == Point::new(1.0, 2.0));
    
    // Clone
    let point3 = point1.clone();
    println!("Cloned: {}", point3);
    
    // From trait
    let point4: Point = (5.0, 6.0).into();
    println!("From tuple: {}", point4);
    
    let tuple: (f64, f64) = point1.into();
    println!("Into tuple: {:?}", tuple);
    
    // Default
    let default_point = Point::default();
    println!("Default: {}", default_point);
    
    // Add operation
    let sum = point1 + point2;
    println!("Sum: {}", sum);
    
    // Methods
    println!("Distance from origin: {}", point1.distance_from_origin());
    println!("Distance to point2: {}", point1.distance_to(&point2));
}
```

### Generic Structs
```rust
// Generic struct definition
#[derive(Debug)]
struct Container<T> {
    data: T,
    metadata: String,
}

impl<T> Container<T> {
    fn new(data: T, metadata: String) -> Self {
        Container { data, metadata }
    }
    
    fn get_data(&self) -> &T {
        &self.data
    }
    
    fn get_metadata(&self) -> &str {
        &self.metadata
    }
    
    fn set_data(&mut self, data: T) {
        self.data = data;
    }
    
    fn set_metadata(&mut self, metadata: String) {
        self.metadata = metadata;
    }
}

// Implementation for specific types
impl Container<i32> {
    fn sum_digits(&self) -> i32 {
        let mut sum = 0;
        let mut n = self.data;
        
        while n > 0 {
            sum += n % 10;
            n /= 10;
        }
        
        sum
    }
}

impl Container<String> {
    fn word_count(&self) -> usize {
        self.data.split_whitespace().count()
    }
    
    fn char_count(&self) -> usize {
        self.data.chars().count()
    }
}

fn generic_structs() {
    let int_container = Container::new(123, "Integer data".to_string());
    println!("Integer container: {:?}", int_container);
    println!("Sum of digits: {}", int_container.sum_digits());
    
    let string_container = Container::new("Hello World Rust".to_string(), "String data".to_string());
    println!("String container: {:?}", string_container);
    println!("Word count: {}", string_container.word_count());
    println!("Char count: {}", string_container.char_count());
}

// Generic struct with multiple type parameters
#[derive(Debug)]
struct Pair<T, U> {
    first: T,
    second: U,
}

impl<T, U> Pair<T, U> {
    fn new(first: T, second: U) -> Self {
        Pair { first, second }
    }
    
    fn swap(self) -> Pair<U, T> {
        Pair {
            first: self.second,
            second: self.first,
        }
    }
    
    fn get_first(&self) -> &T {
        &self.first
    }
    
    fn get_second(&self) -> &U {
        &self.second
    }
}

fn multiple_generic_types() {
    let pair = Pair::new("Hello", 42);
    println!("Original pair: {:?}", pair);
    
    let swapped = pair.swap();
    println!("Swapped pair: {:?}", swapped);
    
    let pair2 = Pair::new(1, "World");
    println!("Second pair: {:?}", pair2);
    println!("First: {}", pair2.get_first());
    println!("Second: {}", pair2.get_second());
}
```

## Enums

### Basic Enums
```rust
// Basic enum definition
#[derive(Debug)]
enum Color {
    Red,
    Green,
    Blue,
}

fn basic_enums() {
    let color = Color::Red;
    
    match color {
        Color::Red => println!("The color is Red"),
        Color::Green => println!("The color is Green"),
        Color::Blue => println!("The color is Blue"),
    }
    
    let colors = vec![Color::Red, Color::Green, Color::Blue];
    for color in colors {
        println!("Color: {:?}", color);
    }
}

// Enum with data
#[derive(Debug)]
enum IpAddr {
    V4(u8, u8, u8, u8),
    V6(String),
}

fn enum_with_data() {
    let home = IpAddr::V4(127, 0, 0, 1);
    let loopback = IpAddr::V6("::1".to_string());
    
    println!("Home: {:?}", home);
    println!("Loopback: {:?}", loopback);
    
    let ip = IpAddr::V4(192, 168, 1, 1);
    match ip {
        IpAddr::V4(a, b, c, d) => {
            println!("IPv4 address: {}.{}.{}.{}", a, b, c, d);
        }
        IpAddr::V6(address) => {
            println!("IPv6 address: {}", address);
        }
    }
}

// Enum with different data types
#[derive(Debug)]
enum Message {
    Quit,
    Move { x: i32, y: i32 },
    Write(String),
    ChangeColor(IpAddr),
    Echo(String),
}

fn enum_different_types() {
    let messages = vec![
        Message::Quit,
        Message::Move { x: 10, y: 20 },
        Message::Write("Hello, World!".to_string()),
        Message::ChangeColor(IpAddr::V4(255, 255, 255, 255)),
        Message::Echo("Echo message".to_string()),
    ];
    
    for message in messages {
        process_message(message);
    }
}

fn process_message(message: Message) {
    match message {
        Message::Quit => println!("Quit message"),
        Message::Move { x, y } => println!("Move to ({}, {})", x, y),
        Message::Write(text) => println!("Write: {}", text),
        Message::ChangeColor(ip) => println!("Change color to {:?}", ip),
        Message::Echo(text) => println!("Echo: {}", text),
    }
}
```

### Enum Methods
```rust
// Enum with methods
#[derive(Debug)]
enum State {
    Start,
    Stop,
    Pause,
}

impl State {
    fn is_active(&self) -> bool {
        match self {
            State::Start => true,
            State::Stop => false,
            State::Pause => false,
        }
    }
    
    fn transition(&mut self, new_state: State) {
        println!("Transitioning from {:?} to {:?}", self, new_state);
        *self = new_state;
    }
    
    fn get_name(&self) -> &'static str {
        match self {
            State::Start => "Start",
            State::Stop => "Stop",
            State::Pause => "Pause",
        }
    }
}

fn enum_methods() {
    let mut state = State::Start;
    
    println!("Current state: {} (active: {})", state.get_name(), state.is_active());
    
    state.transition(State::Pause);
    println!("Current state: {} (active: {})", state.get_name(), state.is_active());
    
    state.transition(State::Stop);
    println!("Current state: {} (active: {})", state.get_name(), state.is_active());
}

// Enum with associated functions
#[derive(Debug)]
enum Temperature {
    Celsius(f64),
    Fahrenheit(f64),
    Kelvin(f64),
}

impl Temperature {
    fn new_celsius(celsius: f64) -> Self {
        Temperature::Celsius(celsius)
    }
    
    fn new_fahrenheit(fahrenheit: f64) -> Self {
        Temperature::Fahrenheit(fahrenheit)
    }
    
    fn new_kelvin(kelvin: f64) -> Self {
        Temperature::Kelvin(kelvin)
    }
    
    fn to_celsius(&self) -> f64 {
        match self {
            Temperature::Celsius(c) => *c,
            Temperature::Fahrenheit(f) => (f - 32.0) * 5.0 / 9.0,
            Temperature::Kelvin(k) => k - 273.15,
        }
    }
    
    fn to_fahrenheit(&self) -> f64 {
        match self {
            Temperature::Celsius(c) => c * 9.0 / 5.0 + 32.0,
            Temperature::Fahrenheit(f) => *f,
            Temperature::Kelvin(k) => (k - 273.15) * 9.0 / 5.0 + 32.0,
        }
    }
    
    fn to_kelvin(&self) -> f64 {
        match self {
            Temperature::Celsius(c) => c + 273.15,
            Temperature::Fahrenheit(f) => (f - 32.0) * 5.0 / 9.0 + 273.15,
            Temperature::Kelvin(k) => *k,
        }
    }
    
    fn is_freezing(&self) -> bool {
        self.to_celsius() <= 0.0
    }
    
    fn is_boiling(&self) -> bool {
        self.to_celsius() >= 100.0
    }
}

fn enum_associated_functions() {
    let temp_c = Temperature::new_celsius(25.0);
    let temp_f = Temperature::new_fahrenheit(77.0);
    let temp_k = Temperature::new_kelvin(298.15);
    
    println!("Celsius: {}°C", temp_c.to_celsius());
    println!("Fahrenheit: {}°F", temp_f.to_fahrenheit());
    println!("Kelvin: {}K", temp_k.to_kelvin());
    
    println!("25°C in Fahrenheit: {}°F", temp_c.to_fahrenheit());
    println!("77°F in Celsius: {}°C", temp_f.to_celsius());
    println!("298.15K in Celsius: {}°C", temp_k.to_celsius());
    
    println!("Is 25°C freezing? {}", temp_c.is_freezing());
    println!("Is 77°F boiling? {}", temp_f.is_boiling());
}
```

### Pattern Matching with Enums
```rust
// Complex enum for pattern matching
#[derive(Debug)]
enum Result<T, E> {
    Ok(T),
    Err(E),
}

fn pattern_matching_enums() {
    let success: Result<i32, String> = Result::Ok(42);
    let failure: Result<i32, String> = Result::Err("Error message".to_string());
    
    match success {
        Result::Ok(value) => println!("Success: {}", value),
        Result::Err(error) => println!("Error: {}", error),
    }
    
    match failure {
        Result::Ok(value) => println!("Success: {}", value),
        Result::Err(error) => println!("Error: {}", error),
    }
    
    // Pattern matching with guards
    let number = 42;
    match number {
        0 => println!("Zero"),
        1..=9 => println!("Single digit"),
        10..=99 => println!("Two digits"),
        _ => println!("Many digits"),
    }
    
    // Destructuring enum variants
    let ip = IpAddr::V4(192, 168, 1, 1);
    match ip {
        IpAddr::V4(a, b, c, d) => {
            if a == 192 && b == 168 {
                println!("Private network: {}.{}.{}.{}", a, b, c, d);
            } else {
                println!("Public network: {}.{}.{}.{}", a, b, c, d);
            }
        }
        IpAddr::V6(address) => println!("IPv6: {}", address),
    }
}

// Option enum pattern matching
fn option_pattern_matching() {
    let some_value: Option<i32> = Some(42);
    let none_value: Option<i32> = None;
    
    match some_value {
        Some(value) => println!("Some: {}", value),
        None => println!("None"),
    }
    
    match none_value {
        Some(value) => println!("Some: {}", value),
        None => println!("None"),
    }
    
    // Using if let
    if let Some(value) = some_value {
        println!("Value: {}", value);
    }
    
    // Using unwrap_or
    let value = none_value.unwrap_or(0);
    println!("Value: {}", value);
}

// Enum with generic parameters
#[derive(Debug)]
enum Option<T> {
    Some(T),
    None,
}

fn generic_enums() {
    let some_int: Option<i32> = Option::Some(42);
    let some_string: Option<String> = Option::Some("Hello".to_string());
    
    match some_int {
        Option::Some(value) => println!("Some integer: {}", value),
        Option::None => println!("None"),
    }
    
    match some_string {
        Option::Some(value) => println!("Some string: {}", value),
        Option::None => println!("None"),
    }
    
    // Generic function with Option
    fn process_option<T: std::fmt::Display>(option: Option<T>) {
        match option {
            Option::Some(value) => println!("Value: {}", value),
            Option::None => println!("No value"),
        }
    }
    
    process_option(some_int);
    process_option(some_string);
    process_option(Option::<i32>::None);
}
```

### Advanced Enum Patterns
```rust
// Enum with methods and implementations
#[derive(Debug, Clone, Copy, PartialEq)]
enum Status {
    Active,
    Inactive,
    Pending,
}

impl Status {
    fn from_string(s: &str) -> Self {
        match s.to_lowercase().as_str() {
            "active" => Status::Active,
            "inactive" => Status::Inactive,
            "pending" => Status::Pending,
            _ => Status::Pending, // Default
        }
    }
    
    fn to_string(&self) -> &'static str {
        match self {
            Status::Active => "active",
            Status::Inactive => "inactive",
            Status::Pending => "pending",
        }
    }
    
    fn is_transition_allowed(&self, new_status: Status) -> bool {
        match (self, new_status) {
            (Status::Active, Status::Inactive) => true,
            (Status::Inactive, Status::Active) => true,
            (Status::Pending, Status::Active) => true,
            (Status::Active, Status::Pending) => true,
            (Status::Inactive, Status::Pending) => true,
            _ => false,
        }
    }
}

fn advanced_enum_patterns() {
    let mut status = Status::Active;
    
    println!("Current status: {}", status.to_string());
    
    let new_status = Status::Inactive;
    if status.is_transition_allowed(new_status) {
        status = new_status;
        println!("Transitioned to: {}", status.to_string());
    } else {
        println!("Transition not allowed");
    }
    
    let status_from_string = Status::from_string("pending");
    println!("Status from string: {}", status_from_string.to_string());
}

// Enum with lifetimes
#[derive(Debug)]
enum Message<'a> {
    Text(&'a str),
    Image(&'a [u8]),
    Audio(&'a [u8]),
}

fn enum_with_lifetimes() {
    let text = "Hello, World!";
    let image = [255, 0, 0, 255];
    let audio = [1, 2, 3, 4];
    
    let messages = vec![
        Message::Text(text),
        Message::Image(&image),
        Message::Audio(&audio),
    ];
    
    for message in messages {
        process_message(message);
    }
}

fn process_message(message: Message) {
    match message {
        Message::Text(text) => println!("Text: {}", text),
        Message::Image(data) => println!("Image data: {:?}", data),
        Message::Audio(data) => println!("Audio data: {:?}", data),
    }
}

// Recursive enum
#[derive(Debug)]
enum List {
    Cons(i32, Box<List>),
    Nil,
}

impl List {
    fn new() -> Self {
        List::Nil
    }
    
    fn cons(head: i32, tail: List) -> Self {
        List::Cons(head, Box::new(tail))
    }
    
    fn len(&self) -> usize {
        match self {
            List::Cons(_, tail) => 1 + tail.len(),
            List::Nil => 0,
        }
    }
    
    fn sum(&self) -> i32 {
        match self {
            List::Cons(head, tail) => head + tail.sum(),
            List::Nil => 0,
        }
    }
}

fn recursive_enums() {
    let list = List::cons(1, List::cons(2, List::cons(3, List::new())));
    
    println!("List: {:?}", list);
    println!("Length: {}", list.len());
    println!("Sum: {}", list.sum());
}
```

## Best Practices

### Struct Best Practices
```rust
// 1. Use descriptive field names
fn descriptive_field_names() {
    // Good: descriptive names
    struct UserProfile {
        username: String,
        email_address: String,
        account_age_days: u32,
        is_premium_user: bool,
    }
    
    // Avoid: generic names
    struct BadUserProfile {
        name: String,
        email: String,
        age: u32,
        premium: bool,
    }
}

// 2. Use appropriate visibility
fn visibility() {
    // Public struct with private fields
    mod module {
        pub struct User {
            id: u32,
            name: String,
            email: String,
        }
        
        impl User {
            pub fn new(id: u32, name: String, email: String) -> Self {
                User { id, name, email }
            }
            
            pub fn id(&self) -> u32 {
                self.id
            }
            
            // Private method
            fn validate_email(&self) -> bool {
                self.email.contains('@')
            }
        }
    }
    
    // Use public methods to access private fields
    let user = module::User::new(1, "Alice".to_string(), "alice@example.com".to_string());
    println!("User ID: {}", user.id());
}

// 3. Implement useful traits
fn implement_traits() {
    #[derive(Debug, Clone, PartialEq, Eq)]
    struct Point {
        x: f64,
        y: f64,
    }
    
    impl Default for Point {
        fn default() -> Self {
            Point { x: 0.0, y: 0.0 }
        }
    
    impl std::fmt::Display for Point {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "({}, {})", self.x, self.y)
        }
    }
    
    let point = Point::default();
    println!("Point: {}", point);
}

// 4. Use builder pattern for complex structs
fn builder_pattern() {
    struct User {
        username: String,
        email: String,
        age: u32,
        active: bool,
    }
    
    impl User {
        fn new(username: String) -> UserBuilder {
            UserBuilder {
                username,
                email: String::new(),
                age: 0,
                active: true,
            }
        }
    }
    
    struct UserBuilder {
        username: String,
        email: String,
        age: u32,
        active: bool,
    }
    
    impl UserBuilder {
        fn email(mut self, email: String) -> Self {
            self.email = email;
            self
        }
        
        fn age(mut self, age: u32) -> Self {
            self.age = age;
            self
        }
        
        fn active(mut self, active: bool) -> Self {
            self.active = active;
            self
        }
        
        fn build(self) -> User {
            User {
                username: self.username,
                email: self.email,
                age: self.age,
                active: self.active,
            }
        }
    }
    
    let user = User::new("alice".to_string())
        .email("alice@example.com".to_string())
        .age(25)
        .active(true)
        .build();
    
    println!("User: {} ({})", user.username, user.email);
}

// 5. Use appropriate lifetimes
fn appropriate_lifetimes() {
    // Good: minimize lifetime annotations
    struct Ref<'a> {
        data: &'a str,
    }
    
    impl<'a> Ref<'a> {
        fn new(data: &'a str) -> Self {
            Ref { data }
        }
        
        fn get_data(&self) -> &str {
            self.data
        }
    }
    
    let string = String::from("Hello");
    let r = Ref::new(&string);
    println!("Data: {}", r.get_data());
}
```

### Enum Best Practices
```rust
// 1. Use descriptive variant names
fn descriptive_variant_names() {
    // Good: descriptive names
    enum OrderStatus {
        Pending,
        Processing,
        Shipped,
        Delivered,
        Cancelled,
        Returned,
    }
    
    // Avoid: generic names
    enum BadOrderStatus {
        Status1,
        Status2,
        Status3,
        Status4,
        Status5,
    }
}

// 2. Use appropriate data in variants
fn appropriate_variant_data() {
    // Good: meaningful data
    enum ApiResponse {
        Success { data: String },
        Error { code: u32, message: String },
        Redirect { url: String },
    }
    
    // Avoid: unnecessary data
    enum BadApiResponse {
        Success(String),
        Error(u32, String),
        Redirect(String),
    }
}

// 3. Implement useful methods
fn enum_methods() {
    #[derive(Debug, Clone, Copy)]
    enum Status {
        Active,
        Inactive,
    }
    
    impl Status {
        fn is_active(&self) -> bool {
            matches!(self, Status::Active)
        }
        
        fn toggle(&self) -> Self {
            match self {
                Status::Active => Status::Inactive,
                Status::Inactive => Status::Active,
            }
        }
    }
    
    let status = Status::Active;
    println!("Is active: {}", status.is_active());
    println!("Toggled: {:?}", status.toggle());
}

// 4. Use Option instead of null
fn use_option_instead_of_null() {
    // Good: use Option for optional values
    struct User {
        name: String,
        email: Option<String>,
    }
    
    impl User {
        fn get_email(&self) -> Option<&str> {
            self.email.as_deref()
        }
    }
    
    let user = User {
        name: "Alice".to_string(),
        email: Some("alice@example.com".to_string()),
    };
    
    match user.get_email() {
        Some(email) => println!("Email: {}", email),
        None => println!("No email"),
    }
}

// 5. Use Result for error handling
fn use_result_for_errors() {
    #[derive(Debug)]
    enum Error {
        InvalidInput(String),
        NetworkError,
        ParseError,
    }
    
    fn parse_number(s: &str) -> Result<i32, Error> {
        s.parse().map_err(|_| Error::ParseError)
    }
    
    fn validate_input(s: &str) -> Result<String, Error> {
        if s.is_empty() {
            Err(Error::InvalidInput("Empty input".to_string()))
        } else {
            Ok(s.to_string())
        }
    }
    
    match parse_number("42") {
        Ok(number) => println!("Parsed: {}", number),
        Err(error) => println!("Error: {:?}", error),
    }
    
    match validate_input("") {
        Ok(input) => println!("Valid: {}", input),
        Err(error) => println!("Error: {:?}", error),
    }
}
```

## Common Pitfalls

### Common Struct Mistakes
```rust
// 1. Forgetting to implement required traits
fn missing_traits() {
    // Bad: struct without Debug, Clone, etc.
    struct User {
        name: String,
        age: u32,
    }
    
    // Good: derive useful traits
    #[derive(Debug, Clone, PartialEq, Eq)]
    struct BetterUser {
        name: String,
        age: u32,
    }
}

// 2. Using public fields unnecessarily
fn public_fields() {
    // Bad: making all fields public
    pub struct BadUser {
        pub name: String,
        pub age: u32,
        pub email: String,
    }
    
    // Good: use private fields with public methods
    pub struct GoodUser {
        name: String,
        age: u32,
        email: String,
    }
    
    impl GoodUser {
        pub fn new(name: String, age: u32, email: String) -> Self {
            GoodUser { name, age, email }
        }
        
        pub fn name(&self) -> &str {
            &self.name
        }
        
        pub fn age(&self) -> u32 {
            self.age
        }
        
        pub fn email(&self) -> &str {
            &self.email
        }
    }
}

// 3. Not implementing Default when appropriate
fn missing_default() {
    // Bad: no Default implementation
    struct Config {
        timeout: u64,
        retries: u32,
        debug: bool,
    }
    
    // Good: implement Default
    #[derive(Debug, Default)]
    struct BetterConfig {
        timeout: u64,
        retries: u32,
        debug: bool,
    }
    
    let config = BetterConfig::default();
    println!("Default config: {:?}", config);
}

// 4. Using tuple structs for complex data
fn tuple_structs_abuse() {
    // Bad: too many fields in tuple struct
    struct BadPoint(f64, f64, f64, f64, f64);
    
    // Good: use regular struct for complex data
    #[derive(Debug)]
    struct BetterPoint {
        x: f64,
        y: f64,
        z: f64,
        color: String,
        timestamp: u64,
    }
}

// 5. Not implementing Copy when appropriate
fn missing_copy() {
    // Bad: struct that could be Copy but isn't
    struct Point {
        x: i32,
        y: i32,
    }
    
    // Good: implement Copy for simple structs
    #[derive(Debug, Clone, Copy)]
    struct BetterPoint {
        x: i32,
        y: i32,
    }
}
```

### Common Enum Mistakes
```rust
// 1. Not handling all enum variants
fn incomplete_pattern_matching() {
    #[derive(Debug)]
    enum Color {
        Red,
        Green,
        Blue,
    }
    
    let color = Color::Red;
    
    // Bad: not handling all variants
    match color {
        Color::Red => println!("Red"),
        // Missing Green and Blue - compiler will warn
    }
    
    // Good: handle all variants
    match color {
        Color::Red => println!("Red"),
        Color::Green => println!("Green"),
        Color::Blue => println!("Blue"),
    }
}

// 2. Using generic data inappropriately
fn generic_data_abuse() {
    // Bad: using generic data when specific types would be better
    enum GenericMessage {
        Text(String),
        Number(i32),
        Boolean(bool),
        Data(Vec<u8>),
    }
    
    // Good: use specific types for specific purposes
    #[derive(Debug)]
    enum Message {
        Text(String),
        Number(i32),
        Boolean(bool),
        Image(Vec<u8>),
    }
}

// 3. Not implementing useful methods
fn missing_enum_methods() {
    // Bad: enum without methods
    enum Status {
        Active,
        Inactive,
    }
    
    // Good: implement useful methods
    #[derive(Debug, Clone, Copy, PartialEq)]
    enum BetterStatus {
        Active,
        Inactive,
    }
    
    impl BetterStatus {
        fn is_active(&self) -> bool {
            matches!(self, BetterStatus::Active)
        }
        
        fn toggle(&self) -> Self {
            match self {
                BetterStatus::Active => BetterStatus::Inactive,
                BetterStatus::Inactive => BetterStatus::Active,
            }
        }
    }
}

// 4. Using enums for things that should be structs
fn enum_vs_struct() {
    // Bad: using enum for things with identity
    enum BadUser {
        User(String, u32, String),
    }
    
    // Good: use struct for things with identity
    #[derive(Debug, PartialEq)]
    struct GoodUser {
        name: String,
        age: u32,
        email: String,
    }
}

// 5. Not implementing From/Into for conversions
fn missing_conversions() {
    // Bad: no conversion traits
    enum Temperature {
        Celsius(f64),
        Fahrenheit(f64),
    }
    
    // Good: implement conversion traits
    #[derive(Debug, Clone, Copy)]
    enum BetterTemperature {
        Celsius(f64),
        Fahrenheit(f64),
    }
    
    impl From<f64> for BetterTemperature {
        fn from(celsius: f64) -> Self {
            BetterTemperature::Celsius(celsius)
        }
    }
    
    impl BetterTemperature {
        fn to_fahrenheit(&self) -> f64 {
            match self {
                BetterTemperature::Celsius(c) => c * 9.0 / 5.0 + 32.0,
                BetterTemperature::Fahrenheit(f) => *f,
            }
        }
    }
}
```

## Summary

Rust structs and enums provide powerful data modeling capabilities:

**Structs:**
- Classic structs with named fields
- Tuple structs with positional fields
- Unit-like structs for markers
- Methods and associated functions
- Generic structs with type parameters
- Lifetime annotations for references
- Trait implementations for functionality

**Key Struct Features:**
- Field access and mutation
- Method definitions in impl blocks
- Multiple impl blocks per struct
- Associated functions and constants
- Builder pattern for complex construction
- Proper visibility control

**Enums:**
- Basic enums with no data
- Enums with tuple variants
- Enums with struct variants
- Methods and associated functions
- Pattern matching capabilities
- Generic enums with type parameters
- Recursive enums for data structures

**Key Enum Features:**
- Pattern matching with match
- Option and Result types
- Data carrying variants
- Method implementations
- Conversion traits
- Lifetime annotations

**Best Practices:**
- Descriptive naming conventions
- Appropriate visibility control
- Trait implementations (Debug, Clone, etc.)
- Builder patterns for complex structs
- Option instead of null
- Result for error handling
- Proper lifetime management

**Common Pitfalls:**
- Missing trait implementations
- Unnecessary public fields
- Incomplete pattern matching
- Generic data misuse
- Missing conversion methods
- Enum vs struct confusion

**Advanced Patterns:**
- Generic programming with structs and enums
- Lifetime-aware design
- Trait objects and dynamic dispatch
- Builder and factory patterns
- State machine modeling with enums
- Error handling with Result

Rust's struct and enum systems, combined with its type system and ownership model, provide safe, expressive, and efficient ways to model data and behavior while preventing common programming errors at compile time.
