# Rust Error Handling

## Error Handling Fundamentals

### Result Type
```rust
// Result type basics
fn result_basics() {
    // Successful result
    let success: Result<i32, &str> = Ok(42);
    let error: Result<i32, &str> = Err("Something went wrong");
    
    match success {
        Ok(value) => println!("Success: {}", value),
        Err(e) => println!("Error: {}", e),
    }
    
    match error {
        Ok(value) => println!("Success: {}", value),
        Err(e) => println!("Error: {}", e),
    }
}

// Function returning Result
fn divide(a: f64, b: f64) -> Result<f64, String> {
    if b == 0.0 {
        Err("Cannot divide by zero".to_string())
    } else {
        Ok(a / b)
    }
}

fn result_function() {
    let result1 = divide(10.0, 2.0);
    let result2 = divide(10.0, 0.0);
    
    match result1 {
        Ok(value) => println!("10 / 2 = {}", value),
        Err(e) => println!("Error: {}", e),
    }
    
    match result2 {
        Ok(value) => println!("10 / 0 = {}", value),
        Err(e) => println!("Error: {}", e),
    }
}

// Result methods
fn result_methods() {
    let success: Result<i32, &str> = Ok(42);
    let error: Result<i32, &str> = Err("Error");
    
    // unwrap_or
    let value1 = success.unwrap_or(0);
    let value2 = error.unwrap_or(0);
    println!("unwrap_or: {}, {}", value1, value2);
    
    // unwrap_or_else
    let value3 = success.unwrap_or_else(|e| {
        println!("Error in unwrap_or_else: {}", e);
        0
    });
    let value4 = error.unwrap_or_else(|e| {
        println!("Error in unwrap_or_else: {}", e);
        0
    });
    println!("unwrap_or_else: {}, {}", value3, value4);
    
    // is_ok and is_err
    println!("success.is_ok(): {}", success.is_ok());
    println!("success.is_err(): {}", success.is_err());
    println!("error.is_ok(): {}", error.is_ok());
    println!("error.is_err(): {}", error.is_err());
    
    // ok and err
    let ok_value = success.ok();
    let err_value = success.err();
    println!("success.ok(): {:?}", ok_value);
    println!("success.err(): {:?}", err_value);
}
```

### Option Type
```rust
// Option type basics
fn option_basics() {
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
}

// Function returning Option
fn get_element(vec: &[i32], index: usize) -> Option<i32> {
    if index < vec.len() {
        Some(vec[index])
    } else {
        None
    }
}

fn option_function() {
    let numbers = vec![1, 2, 3, 4, 5];
    
    let result1 = get_element(&numbers, 2);
    let result2 = get_element(&numbers, 10);
    
    match result1 {
        Some(value) => println!("Element at index 2: {}", value),
        None => println!("No element at index 2"),
    }
    
    match result2 {
        Some(value) => println!("Element at index 10: {}", value),
        None => println!("No element at index 10"),
    }
}

// Option methods
fn option_methods() {
    let some_value: Option<i32> = Some(42);
    let none_value: Option<i32> = None;
    
    // unwrap_or
    let value1 = some_value.unwrap_or(0);
    let value2 = none_value.unwrap_or(0);
    println!("unwrap_or: {}, {}", value1, value2);
    
    // unwrap_or_else
    let value3 = some_value.unwrap_or_else(|| {
        println!("Using default in unwrap_or_else");
        0
    });
    let value4 = none_value.unwrap_or_else(|| {
        println!("Using default in unwrap_or_else");
        0
    });
    println!("unwrap_or_else: {}, {}", value3, value4);
    
    // map
    let mapped1 = some_value.map(|x| x * 2);
    let mapped2 = none_value.map(|x| x * 2);
    println!("map: {:?}, {:?}", mapped1, mapped2);
    
    // and_then
    let and_then1 = some_value.and_then(|x| Some(x * 2));
    let and_then2 = none_value.and_then(|x| Some(x * 2));
    println!("and_then: {:?}, {:?}", and_then1, and_then2);
    
    // filter
    let filtered1 = some_value.filter(|&x| x > 40);
    let filtered2 = some_value.filter(|&x| x < 40);
    println!("filter: {:?}, {:?}", filtered1, filtered2);
}
```

## Error Propagation

### The ? Operator
```rust
// Using ? operator for error propagation
fn read_file_content() -> Result<String, std::io::Error> {
    use std::fs;
    use std::io::Read;
    
    let mut file = fs::File::open("test.txt")?;
    let mut content = String::new();
    file.read_to_string(&mut content)?;
    
    Ok(content)
}

fn question_mark_operator() {
    match read_file_content() {
        Ok(content) => println!("File content: {}", content),
        Err(e) => println!("Error reading file: {}", e),
    }
}

// Chaining ? operator
fn parse_and_process() -> Result<i32, std::num::ParseIntError> {
    let content = read_file_content().map_err(|_| std::num::ParseIntError::ParseIntError {
        kind: std::num::IntErrorKind::InvalidDigit,
    })?;
    
    let number: i32 = content.trim().parse()?;
    let result = number * 2;
    
    Ok(result)
}

fn chaining_question_mark() {
    match parse_and_process() {
        Ok(result) => println!("Processed result: {}", result),
        Err(e) => println!("Parse error: {}", e),
    }
}

// ? operator with different error types
fn different_error_types() -> Result<(), Box<dyn std::error::Error>> {
    let content = read_file_content()?;
    let number: i32 = content.trim().parse()?;
    
    println!("Number: {}", number);
    Ok(())
}

fn different_types() {
    match different_error_types() {
        Ok(()) => println!("Success"),
        Err(e) => println!("Error: {}", e),
    }
}
```

### Custom Error Types
```rust
// Custom error type with Debug and Display
use std::fmt;

#[derive(Debug)]
enum AppError {
    IoError(std::io::Error),
    ParseError(std::num::ParseIntError),
    ValidationError(String),
}

impl fmt::Display for AppError {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            AppError::IoError(e) => write!(f, "IO Error: {}", e),
            AppError::ParseError(e) => write!(f, "Parse Error: {}", e),
            AppError::ValidationError(msg) => write!(f, "Validation Error: {}", msg),
        }
    }
}

impl std::error::Error for AppError {}

// Implement From traits for automatic conversion
impl From<std::io::Error> for AppError {
    fn from(error: std::io::Error) -> Self {
        AppError::IoError(error)
    }
}

impl From<std::num::ParseIntError> for AppError {
    fn from(error: std::num::ParseIntError) -> Self {
        AppError::ParseError(error)
    }
}

fn custom_error_types() {
    use std::fs;
    
    fn read_number_from_file() -> Result<i32, AppError> {
        let content = fs::read_to_string("number.txt")?;
        let number: i32 = content.trim().parse()?;
        
        if number < 0 {
            return Err(AppError::ValidationError("Number must be positive".to_string()));
        }
        
        Ok(number)
    }
    
    match read_number_from_file() {
        Ok(number) => println!("Number: {}", number),
        Err(e) => println!("Error: {}", e),
    }
}

// More complex custom error
#[derive(Debug)]
enum DatabaseError {
    ConnectionError(String),
    QueryError(String),
    NotFound(String),
    PermissionError(String),
}

impl fmt::Display for DatabaseError {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            DatabaseError::ConnectionError(msg) => write!(f, "Connection Error: {}", msg),
            DatabaseError::QueryError(msg) => write!(f, "Query Error: {}", msg),
            DatabaseError::NotFound(msg) => write!(f, "Not Found: {}", msg),
            DatabaseError::PermissionError(msg) => write!(f, "Permission Error: {}", msg),
        }
    }
}

impl std::error::Error for DatabaseError {}

fn complex_custom_error() {
    fn get_user(id: u32) -> Result<String, DatabaseError> {
        if id == 0 {
            return Err(DatabaseError::NotFound("User not found".to_string()));
        }
        
        if id > 1000 {
            return Err(DatabaseError::PermissionError("Access denied".to_string()));
        }
        
        Ok(format!("User {}", id))
    }
    
    match get_user(0) {
        Ok(user) => println!("Found: {}", user),
        Err(e) => println!("Error: {}", e),
    }
    
    match get_user(1001) {
        Ok(user) => println!("Found: {}", user),
        Err(e) => println!("Error: {}", e),
    }
    
    match get_user(42) {
        Ok(user) => println!("Found: {}", user),
        Err(e) => println!("Error: {}", e),
    }
}
```

### Error Conversion
```rust
// From trait for error conversion
use std::convert::From;

#[derive(Debug)]
enum ConversionError {
    StringError(String),
    IntError(std::num::ParseIntError),
    IoError(std::io::Error),
}

impl From<String> for ConversionError {
    fn from(error: String) -> Self {
        ConversionError::StringError(error)
    }
}

impl From<std::num::ParseIntError> for ConversionError {
    fn from(error: std::num::ParseIntError) -> Self {
        ConversionError::IntError(error)
    }
}

impl From<std::io::Error> for ConversionError {
    fn from(error: std::io::Error) -> Self {
        ConversionError::IoError(error)
    }
}

fn error_conversion() {
    fn process_data() -> Result<(), ConversionError> {
        let string_data = "42".to_string();
        let int_data: i32 = string_data.parse()?;
        
        println!("Processed: {}", int_data);
        Ok(())
    }
    
    match process_data() {
        Ok(()) => println!("Success"),
        Err(e) => println!("Error: {:?}", e),
    }
}

// Into trait for error conversion
#[derive(Debug)]
enum IntoError {
    Custom(String),
}

impl Into<IntoError> for String {
    fn into(self) -> IntoError {
        IntoError::Custom(self)
    }
}

fn into_error_conversion() {
    fn process_string(s: String) -> Result<(), IntoError> {
        if s.is_empty() {
            return Err(s.into());
        }
        
        println!("Processed: {}", s);
        Ok(())
    }
    
    match process_string("Hello".to_string()) {
        Ok(()) => println!("Success"),
        Err(e) => println!("Error: {:?}", e),
    }
    
    match process_string("".to_string()) {
        Ok(()) => println!("Success"),
        Err(e) => println!("Error: {:?}", e),
    }
}
```

## Error Handling Patterns

### Match Patterns
```rust
// Comprehensive error handling with match
fn comprehensive_match() {
    fn divide_and_square(a: f64, b: f64) -> Result<f64, String> {
        if b == 0.0 {
            return Err("Division by zero".to_string());
        }
        
        let result = a / b;
        
        if result < 0.0 {
            return Err("Cannot square negative number".to_string());
        }
        
        Ok(result * result)
    }
    
    match divide_and_square(10.0, 2.0) {
        Ok(result) => println!("Result: {}", result),
        Err(e) => println!("Error: {}", e),
    }
    
    match divide_and_square(10.0, 0.0) {
        Ok(result) => println!("Result: {}", result),
        Err(e) => println!("Error: {}", e),
    }
    
    match divide_and_square(-10.0, 2.0) {
        Ok(result) => println!("Result: {}", result),
        Err(e) => println!("Error: {}", e),
    }
}

// Nested match patterns
fn nested_match() {
    fn process_data(data: Option<&str>) -> Result<i32, String> {
        let data = match data {
            Some(d) => d,
            None => return Err("No data provided".to_string()),
        };
        
        let number: i32 = match data.trim().parse() {
            Ok(n) => n,
            Err(_) => return Err("Invalid number".to_string()),
        };
        
        if number < 0 {
            return Err("Number must be positive".to_string());
        }
        
        Ok(number * 2)
    }
    
    match process_data(Some("42")) {
        Ok(result) => println!("Result: {}", result),
        Err(e) => println!("Error: {}", e),
    }
    
    match process_data(Some("invalid")) {
        Ok(result) => println!("Result: {}", result),
        Err(e) => println!("Error: {}", e),
    }
    
    match process_data(None) {
        Ok(result) => println!("Result: {}", result),
        Err(e) => println!("Error: {}", e),
    }
}
```

### Error Recovery
```rust
// Error recovery strategies
fn error_recovery() {
    fn safe_divide(a: f64, b: f64) -> f64 {
        match divide(a, b) {
            Ok(result) => result,
            Err(_) => {
                println!("Division failed, returning 0");
                0.0
            }
        }
    }
    
    let result1 = safe_divide(10.0, 2.0);
    let result2 = safe_divide(10.0, 0.0);
    
    println!("Safe divide 10/2: {}", result1);
    println!("Safe divide 10/0: {}", result2);
    
    // Fallback strategies
    fn get_config_value(key: &str) -> String {
        match std::env::var(key) {
            Ok(value) => value,
            Err(_) => {
                println!("Environment variable {} not found, using default", key);
                match key {
                    "PORT" => "8080".to_string(),
                    "HOST" => "localhost".to_string(),
                    _ => "default".to_string(),
                }
            }
        }
    }
    
    let port = get_config_value("PORT");
    let host = get_config_value("HOST");
    let unknown = get_config_value("UNKNOWN");
    
    println!("Port: {}", port);
    println!("Host: {}", host);
    println!("Unknown: {}", unknown);
}

// Retry mechanisms
fn retry_mechanisms() {
    fn flaky_operation() -> Result<i32, String> {
        use std::time::{SystemTime, UNIX_EPOCH};
        let timestamp = SystemTime::now()
            .duration_since(UNIX_EPOCH)
            .unwrap()
            .as_secs() % 3;
        
        match timestamp {
            0 => Ok(42),
            1 => Err("Temporary failure".to_string()),
            2 => Err("Permanent failure".to_string()),
            _ => Ok(100),
        }
    }
    
    fn retry_operation(max_attempts: u32) -> Result<i32, String> {
        for attempt in 1..=max_attempts {
            match flaky_operation() {
                Ok(result) => {
                    if attempt > 1 {
                        println!("Success on attempt {}", attempt);
                    }
                    return Ok(result);
                }
                Err(e) => {
                    if e == "Permanent failure" {
                        return Err(e);
                    }
                    
                    if attempt < max_attempts {
                        println!("Attempt {} failed: {}, retrying...", attempt, e);
                        std::thread::sleep(std::time::Duration::from_millis(100));
                    } else {
                        return Err(format!("Failed after {} attempts: {}", max_attempts, e));
                    }
                }
            }
        }
        
        Err("Unexpected error".to_string())
    }
    
    match retry_operation(3) {
        Ok(result) => println!("Retry succeeded: {}", result),
        Err(e) => println!("Retry failed: {}", e),
    }
}
```

### Error Context
```rust
// Adding context to errors
fn error_context() {
    use std::path::Path;
    
    fn read_config_file(path: &Path) -> Result<String, std::io::Error> {
        std::fs::read_to_string(path)
    }
    
    fn parse_config(content: &str) -> Result<u32, std::num::ParseIntError> {
        content.trim().parse()
    }
    
    fn load_config(path: &Path) -> Result<u32, String> {
        let content = read_config_file(path)
            .map_err(|e| format!("Failed to read config file {}: {}", path.display(), e))?;
        
        let port = parse_config(&content)
            .map_err(|e| format!("Failed to parse config: {}", e))?;
        
        if port < 1024 {
            return Err("Port must be >= 1024".to_string());
        }
        
        Ok(port)
    }
    
    let config_path = Path::new("config.txt");
    match load_config(&config_path) {
        Ok(port) => println!("Config loaded successfully, port: {}", port),
        Err(e) => println!("Config error: {}", e),
    }
}

// Error chaining
fn error_chaining() {
    #[derive(Debug)]
    enum AppError {
        IoError(std::io::Error),
        ParseError(std::num::ParseIntError),
        ValidationError(String),
    }
    
    impl std::fmt::Display for AppError {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            match self {
                AppError::IoError(e) => write!(f, "IO Error: {}", e),
                AppError::ParseError(e) => write!(f, "Parse Error: {}", e),
                AppError::ValidationError(msg) => write!(f, "Validation Error: {}", msg),
            }
        }
    }
    
    impl std::error::Error for AppError {}
    
    impl From<std::io::Error> for AppError {
        fn from(error: std::io::Error) -> Self {
            AppError::IoError(error)
        }
    }
    
    impl From<std::num::ParseIntError> for AppError {
        fn from(error: std::num::ParseIntError) -> Self {
            AppError::ParseError(error)
        }
    }
    
    fn process_config_file() -> Result<u32, AppError> {
        let content = std::fs::read_to_string("config.txt")?;
        let port: u32 = content.trim().parse()?;
        
        if port < 1024 {
            return Err(AppError::ValidationError("Port must be >= 1024".to_string()));
        }
        
        Ok(port)
    }
    
    match process_config_file() {
        Ok(port) => println!("Config processed successfully, port: {}", port),
        Err(e) => println!("Config error: {}", e),
    }
}
```

## Panic vs Result

### When to Panic
```rust
// When to use panic
fn when_to_panic() {
    // Panic for unrecoverable errors
    fn critical_operation() {
        let data = vec![1, 2, 3];
        
        // This would panic - programming error
        // let value = data[10];
        
        // Better: use bounds checking
        if data.len() > 10 {
            println!("Data has enough elements");
        } else {
            panic!("Not enough elements in data"); // Programming error
        }
    }
    
    // Panic for invariants that should never be broken
    fn invariant_check(value: i32) {
        if value < 0 {
            panic!("Value must be non-negative: {}", value);
        }
        println!("Value is valid: {}", value);
    }
    
    invariant_check(42);
    // invariant_check(-1); // This would panic
}

// Panic with custom messages
fn panic_with_messages() {
    struct User {
        id: u32,
        name: String,
    }
    
    impl User {
        fn new(id: u32, name: String) -> Self {
            if id == 0 {
                panic!("User ID cannot be 0");
            }
            
            if name.is_empty() {
                panic!("User name cannot be empty");
            }
            
            User { id, name }
        }
    }
    
    // This would panic
    // let invalid_user = User::new(0, "".to_string());
    
    let valid_user = User::new(1, "Alice".to_string());
    println!("Valid user: {} ({})", valid_user.name, valid_user.id);
}

// Panic in tests
fn panic_in_tests() {
    // In tests, panic is used to indicate test failure
    fn assert_positive(value: i32) {
        if value <= 0 {
            panic!("Value must be positive, got {}", value);
        }
    }
    
    assert_positive(42);
    // assert_positive(-1); // This would panic
}
```

### When to Use Result
```rust
// When to use Result
fn when_to_use_result() {
    // Use Result for recoverable errors
    fn connect_to_server(url: &str) -> Result<String, String> {
        if url.is_empty() {
            return Err("URL cannot be empty".to_string());
        }
        
        if !url.starts_with("http") {
            return Err("URL must start with http".to_string());
        }
        
        // Simulate connection
        Ok(format!("Connected to {}", url))
    }
    
    match connect_to_server("https://example.com") {
        Ok(message) => println!("{}", message),
        Err(e) => println!("Connection error: {}", e),
    }
    
    // Use Result for operations that can fail
    fn calculate_square_root(number: f64) -> Result<f64, String> {
        if number < 0.0 {
            return Err("Cannot calculate square root of negative number".to_string());
        }
        
        Ok(number.sqrt())
    }
    
    match calculate_square_root(16.0) {
        Ok(result) => println!("Square root: {}", result),
        Err(e) => println!("Error: {}", e),
    }
    
    match calculate_square_root(-4.0) {
        Ok(result) => println!("Square root: {}", result),
        Err(e) => println!("Error: {}", e),
    }
}

// Result for validation
fn result_for_validation() {
    #[derive(Debug)]
    struct User {
        name: String,
        age: u32,
        email: String,
    }
    
    impl User {
        fn new(name: String, age: u32, email: String) -> Result<Self, String> {
            if name.is_empty() {
                return Err("Name cannot be empty".to_string());
            }
            
            if age < 0 {
                return Err("Age cannot be negative".to_string());
            }
            
            if age > 120 {
                return Err("Age cannot be greater than 120".to_string());
            }
            
            if !email.contains('@') {
                return Err("Invalid email format".to_string());
            }
            
            Ok(User { name, age, email })
        }
    }
    
    match User::new("Alice".to_string(), 25, "alice@example.com".to_string()) {
        Ok(user) => println!("Valid user: {:?}", user),
        Err(e) => println!("Invalid user: {}", e),
    }
    
    match User::new("".to_string(), 25, "alice@example.com".to_string()) {
        Ok(user) => println!("Valid user: {:?}", user),
        Err(e) => println!("Invalid user: {}", e),
    }
}
```

### Error Handling Guidelines
```rust
// Error handling guidelines
fn error_handling_guidelines() {
    // 1. Use Result for recoverable errors
    fn recoverable_error() -> Result<i32, String> {
        let input = "42";
        input.parse().map_err(|e| format!("Parse error: {}", e))
    }
    
    // 2. Use panic for programming errors
    fn programming_error() {
        let data = vec![1, 2, 3];
        
        // Programming error - should never happen
        if data.is_empty() {
            panic!("Data should never be empty");
        }
        
        println!("Data has {} elements", data.len());
    }
    
    // 3. Use Option for optional values
    fn optional_value(index: usize) -> Option<i32> {
        let data = vec![1, 2, 3, 4, 5];
        
        if index < data.len() {
            Some(data[index])
        } else {
            None
        }
    }
    
    // 4. Use custom error types for domain errors
    #[derive(Debug)]
    enum DomainError {
        InvalidInput(String),
        NetworkError(String),
        DatabaseError(String),
    }
    
    impl std::fmt::Display for DomainError {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            match self {
                DomainError::InvalidInput(msg) => write!(f, "Invalid input: {}", msg),
                DomainError::NetworkError(msg) => write!(f, "Network error: {}", msg),
                DomainError::DatabaseError(msg) => write!(f, "Database error: {}", msg),
            }
        }
    }
    
    impl std::error::Error for DomainError {}
    
    fn domain_operation(input: &str) -> Result<String, DomainError> {
        if input.is_empty() {
            return Err(DomainError::InvalidInput("Input cannot be empty".to_string()));
        }
        
        Ok(format!("Processed: {}", input))
    }
    
    // Examples
    match recoverable_error() {
        Ok(result) => println!("Recovered: {}", result),
        Err(e) => println!("Error: {}", e),
    }
    
    programming_error();
    
    match optional_value(2) {
        Some(value) => println!("Optional value: {}", value),
        None => println!("No value at index"),
    }
    
    match domain_operation("test") {
        Ok(result) => println!("Domain result: {}", result),
        Err(e) => println!("Domain error: {}", e),
    }
}
```

## Advanced Error Handling

### Error Kind and Source
```rust
// Error with kind and source
use std::error::Error;

#[derive(Debug)]
enum ErrorKind {
    IoError,
    ParseError,
    ValidationError,
}

#[derive(Debug)]
struct AppError {
    kind: ErrorKind,
    source: Option<Box<dyn Error + Send + Sync>>,
    message: String,
}

impl AppError {
    fn new(kind: ErrorKind, message: &str) -> Self {
        AppError {
            kind,
            source: None,
            message: message.to_string(),
        }
    }
    
    fn with_source<E>(kind: ErrorKind, source: E, message: &str) -> Self 
    where 
        E: Error + Send + Sync + 'static 
    {
        AppError {
            kind,
            source: Some(Box::new(source)),
            message: message.to_string(),
        }
    }
}

impl fmt::Display for AppError {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        write!(f, "{}: {}", self.kind, self.message)
    }
}

impl fmt::Display for ErrorKind {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            ErrorKind::IoError => write!(f, "IO Error"),
            ErrorKind::ParseError => write!(f, "Parse Error"),
            ErrorKind::ValidationError => write!(f, "Validation Error"),
        }
    }
}

impl Error for AppError {
    fn source(&self) -> Option<&(dyn Error + 'static)> {
        self.source.as_ref().map(|e| e.as_ref())
    }
}

fn error_kind_and_source() {
    fn read_config() -> Result<String, AppError> {
        use std::fs;
        
        match fs::read_to_string("config.txt") {
            Ok(content) => Ok(content),
            Err(e) => Err(AppError::with_source(
                ErrorKind::IoError,
                e,
                "Failed to read config file"
            )),
        }
    }
    
    fn parse_config(content: &str) -> Result<u32, AppError> {
        match content.trim().parse() {
            Ok(port) => Ok(port),
            Err(e) => Err(AppError::with_source(
                ErrorKind::ParseError,
                e,
                "Failed to parse port number"
            )),
        }
    }
    
    fn validate_port(port: u32) -> Result<u32, AppError> {
        if port < 1024 {
            Err(AppError::new(
                ErrorKind::ValidationError,
                "Port must be >= 1024"
            ))
        } else {
            Ok(port)
        }
    }
    
    fn load_config() -> Result<u32, AppError> {
        let content = read_config()?;
        let port = parse_config(&content)?;
        validate_port(port)
    }
    
    match load_config() {
        Ok(port) => println!("Config loaded successfully: {}", port),
        Err(e) => {
            println!("Error: {}", e);
            if let Some(source) = e.source() {
                println!("Caused by: {}", source);
            }
        }
    }
}
```

### Error Aggregation
```rust
// Error aggregation
fn error_aggregation() {
    use std::collections::HashMap;
    
    #[derive(Debug)]
    struct ValidationError {
        field: String,
        message: String,
    }
    
    #[derive(Debug)]
    struct ValidationErrors {
        errors: Vec<ValidationError>,
    }
    
    impl ValidationErrors {
        fn new() -> Self {
            ValidationErrors { errors: Vec::new() }
        }
        
        fn add(&mut self, field: &str, message: &str) {
            self.errors.push(ValidationError {
                field: field.to_string(),
                message: message.to_string(),
            });
        }
        
        fn is_empty(&self) -> bool {
            self.errors.is_empty()
        }
        
        fn first(&self) -> Option<&ValidationError> {
            self.errors.first()
        }
        
        fn all(&self) -> &Vec<ValidationError> {
            &self.errors
        }
    }
    
    impl fmt::Display for ValidationErrors {
        fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
            if self.errors.is_empty() {
                return write!(f, "No validation errors");
            }
            
            write!(f, "Validation errors:\n")?;
            for (i, error) in self.errors.iter().enumerate() {
                write!(f, "  {}. {}: {}\n", i + 1, error.field, error.message)?;
            }
            Ok(())
        }
    }
    
    impl std::error::Error for ValidationErrors {}
    
    fn validate_user(data: &HashMap<String, String>) -> Result<(), ValidationErrors> {
        let mut errors = ValidationErrors::new();
        
        if !data.contains_key("name") {
            errors.add("name", "Name is required");
        } else if let Some(name) = data.get("name") {
            if name.is_empty() {
                errors.add("name", "Name cannot be empty");
            } else if name.len() < 2 {
                errors.add("name", "Name must be at least 2 characters");
            }
        }
        
        if !data.contains_key("age") {
            errors.add("age", "Age is required");
        } else if let Some(age) = data.get("age") {
            match age.parse::<u32>() {
                Ok(age) => {
                    if age < 0 {
                        errors.add("age", "Age cannot be negative");
                    } else if age > 120 {
                        errors.add("age", "Age cannot be greater than 120");
                    }
                }
                Err(_) => {
                    errors.add("age", "Age must be a valid number");
                }
            }
        }
        
        if !data.contains_key("email") {
            errors.add("email", "Email is required");
        } else if let Some(email) = data.get("email") {
            if !email.contains('@') {
                errors.add("email", "Email must contain @");
            }
        }
        
        if errors.is_empty() {
            Ok(())
        } else {
            Err(errors)
        }
    }
    
    let mut user_data = HashMap::new();
    user_data.insert("name".to_string(), "A".to_string());
    user_data.insert("age".to_string(), "25".to_string());
    user_data.insert("email".to_string(), "invalid-email".to_string());
    
    match validate_user(&user_data) {
        Ok(()) => println!("User data is valid"),
        Err(errors) => println!("{}", errors),
    }
}
```

### Error Backtraces
```rust
// Error backtraces (requires backtrace crate)
fn error_backtraces() {
    // Note: This would require the backtrace crate
    // Add to Cargo.toml: backtrace = "0.3"
    
    /*
    use backtrace::Backtrace;
    
    #[derive(Debug)]
    struct AppError {
        message: String,
        backtrace: Backtrace,
    }
    
    impl AppError {
        fn new(message: &str) -> Self {
            AppError {
                message: message.to_string(),
                backtrace: Backtrace::new(),
            }
        }
    }
    
    impl fmt::Display for AppError {
        fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
            write!(f, "{}\nBacktrace:\n{}", self.message, self.backtrace)
        }
    }
    
    impl std::error::Error for AppError {}
    
    fn create_error() -> Result<(), AppError> {
        Err(AppError::new("Something went wrong"))
    }
    
    match create_error() {
        Ok(()) => println!("Success"),
        Err(e) => println!("Error: {}", e),
    }
    */
    
    // Alternative: Use std::backtrace in Rust 1.65+
    /*
    use std::backtrace::Backtrace;
    
    #[derive(Debug)]
    struct AppError {
        message: String,
        backtrace: Backtrace,
    }
    
    impl AppError {
        fn new(message: &str) -> Self {
            AppError {
                message: message.to_string(),
                backtrace: Backtrace::capture(),
            }
        }
    }
    
    impl fmt::Display for AppError {
        fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
            write!(f, "{}\nBacktrace:\n{}", self.message, self.backtrace)
        }
    }
    
    impl std::error::Error for AppError {}
    
    fn create_error() -> Result<(), AppError> {
        Err(AppError::new("Something went wrong"))
    }
    
    match create_error() {
        Ok(()) => println!("Success"),
        Err(e) => println!("Error: {}", e),
    }
    */
    
    println!("Error backtraces would require backtrace crate");
    println!("Add backtrace = \"0.3\" to Cargo.toml");
}
```

## Best Practices

### Error Handling Best Practices
```rust
// 1. Use Result for recoverable errors
fn use_result_for_recoverable() {
    fn read_file(path: &str) -> Result<String, std::io::Error> {
        std::fs::read_to_string(path)
    }
    
    // Good: returns Result for recoverable error
    match read_file("nonexistent.txt") {
        Ok(content) => println!("File content: {}", content),
        Err(e) => println!("Error reading file: {}", e),
    }
}

// 2. Use panic for programming errors
fn use_panic_for_programming() {
    fn divide_by_two(x: i32) -> i32 {
        if x % 2 != 0 {
            panic!("Input must be even: {}", x);
        }
        x / 2
    }
    
    // Good: panic for programming error
    // divide_by_two(3); // This would panic
    let result = divide_by_two(4);
    println!("Result: {}", result);
}

// 3. Create meaningful error messages
fn meaningful_error_messages() {
    #[derive(Debug)]
    struct ConfigError {
        field: String,
        message: String,
    }
    
    impl std::fmt::Display for ConfigError {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "Config error in field '{}': {}", self.field, self.message)
        }
    }
    
    impl std::error::Error for ConfigError {}
    
    fn validate_port(port: &str) -> Result<u32, ConfigError> {
        port.parse().map_err(|_| ConfigError {
            field: "port".to_string(),
            message: format!("'{}' is not a valid number", port),
        }).and_then(|p| {
            if p < 1024 {
                Err(ConfigError {
                    field: "port".to_string(),
                    message: format!("Port {} must be >= 1024", p),
                })
            } else {
                Ok(p)
            }
        })
    }
    
    match validate_port("8080") {
        Ok(port) => println!("Valid port: {}", port),
        Err(e) => println!("Error: {}", e),
    }
    
    match validate_port("80") {
        Ok(port) => println!("Valid port: {}", port),
        Err(e) => println!("Error: {}", e),
    }
}

// 4. Use appropriate error types
fn appropriate_error_types() {
    use std::io;
    use std::num::ParseIntError;
    
    #[derive(Debug)]
    enum AppError {
        Io(io::Error),
        Parse(ParseIntError),
        Validation(String),
    }
    
    impl std::fmt::Display for AppError {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            match self {
                AppError::Io(e) => write!(f, "IO error: {}", e),
                AppError::Parse(e) => write!(f, "Parse error: {}", e),
                AppError::Validation(msg) => write!(f, "Validation error: {}", msg),
            }
        }
    }
    
    impl std::error::Error for AppError {}
    
    impl From<io::Error> for AppError {
        fn from(error: io::Error) -> Self {
            AppError::Io(error)
        }
    }
    
    impl From<ParseIntError> for AppError {
        fn from(error: ParseIntError) -> Self {
            AppError::Parse(error)
        }
    }
    
    fn process_config() -> Result<u32, AppError> {
        let content = std::fs::read_to_string("config.txt")?;
        let port: u32 = content.trim().parse()?;
        
        if port < 1024 {
            return Err(AppError::Validation("Port must be >= 1024".to_string()));
        }
        
        Ok(port)
    }
    
    match process_config() {
        Ok(port) => println!("Config loaded: {}", port),
        Err(e) => println!("Error: {}", e),
    }
}

// 5. Handle errors at appropriate levels
fn handle_errors_at_appropriate_levels() {
    // Low level: detailed technical errors
    fn read_file_bytes(path: &str) -> Result<Vec<u8>, std::io::Error> {
        std::fs::read(path)
    }
    
    // Mid level: domain-specific errors
    fn load_config_bytes() -> Result<Vec<u8>, String> {
        read_file_bytes("config.txt")
            .map_err(|e| format!("Failed to load config: {}", e))
    }
    
    // High level: user-friendly errors
    fn get_config_value(key: &str) -> Result<String, String> {
        let bytes = load_config_bytes()?;
        let content = String::from_utf8(bytes)
            .map_err(|_| "Config file is not valid UTF-8")?;
        
        // Parse key-value pairs
        let lines: Vec<&str> = content.lines().collect();
        for line in lines {
            if let Some((k, v)) = line.split_once('=') {
                if k.trim() == key {
                    return Ok(v.trim().to_string());
                }
            }
        }
        
        Err(format!("Key '{}' not found in config", key))
    }
    
    match get_config_value("port") {
        Ok(value) => println!("Config value: {}", value),
        Err(e) => println!("Error: {}", e),
    }
}

// 6. Use Option for optional values
fn use_option_for_optional() {
    fn find_user_by_id(users: &[(u32, String)], id: u32) -> Option<&str> {
        users.iter().find(|(user_id, _)| *user_id == id).map(|(_, name)| name.as_str())
    }
    
    let users = vec![
        (1, "Alice".to_string()),
        (2, "Bob".to_string()),
        (3, "Charlie".to_string()),
    ];
    
    match find_user_by_id(&users, 2) {
        Some(name) => println!("Found user: {}", name),
        None => println!("User not found"),
    }
    
    match find_user_by_id(&users, 99) {
        Some(name) => println!("Found user: {}", name),
        None => println!("User not found"),
    }
}

// 7. Provide fallback values
fn provide_fallback_values() {
    fn get_timeout() -> u64 {
        std::env::var("TIMEOUT")
            .ok()
            .and_then(|s| s.parse().ok())
            .unwrap_or(30000) // Default timeout
    }
    
    let timeout = get_timeout();
    println!("Timeout: {}", timeout);
}

// 8. Use logging for debugging
fn use_logging_for_debugging() {
    fn process_data(data: &str) -> Result<i32, String> {
        println!("Processing data: {}", data);
        
        let result = data.trim().parse()
            .map_err(|e| format!("Parse error: {}", e))?;
        
        println!("Parsed result: {}", result);
        Ok(result)
    }
    
    match process_data("42") {
        Ok(result) => println!("Success: {}", result),
        Err(e) => println!("Error: {}", e),
    }
    
    match process_data("invalid") {
        Ok(result) => println!("Success: {}", result),
        Err(e) => println!("Error: {}", e),
    }
}
```

### Error Recovery Strategies
```rust
// 1. Graceful degradation
fn graceful_degradation() {
    fn load_config() -> Result<String, std::io::Error> {
        std::fs::read_to_string("config.txt")
    }
    
    fn get_config_with_default() -> String {
        match load_config() {
            Ok(config) => config,
            Err(_) => {
                println!("Using default config");
                "default_config".to_string()
            }
        }
    
    let config = get_config_with_default();
    println!("Config: {}", config);
}

// 2. Retry mechanisms
fn retry_mechanisms() {
    fn flaky_operation() -> Result<i32, String> {
        use std::time::{SystemTime, UNIX_EPOCH};
        let timestamp = SystemTime::now()
            .duration_since(UNIX_EPOCH)
            .unwrap()
            .as_secs() % 3;
        
        match timestamp {
            0 => Ok(42),
            1 => Err("Temporary failure".to_string()),
            2 => Err("Permanent failure".to_string()),
            _ => Ok(100),
        }
    }
    
    fn retry_with_backoff<F, T, E>(operation: F, max_retries: u32) -> Result<T, E>
    where 
        F: Fn() -> Result<T, E>,
        E: std::fmt::Display,
    {
        let mut delay = 100;
        
        for attempt in 1..=max_retries {
            match operation() {
                Ok(result) => return Ok(result),
                Err(e) => {
                    if attempt == max_retries {
                        return Err(e);
                    }
                    
                    println!("Attempt {} failed: {}, retrying in {}ms", attempt, e, delay);
                    std::thread::sleep(std::time::Duration::from_millis(delay));
                    delay *= 2; // Exponential backoff
                }
            }
        }
        
        unreachable!()
    }
    
    match retry_with_backoff(flaky_operation, 3) {
        Ok(result) => println!("Success: {}", result),
        Err(e) => println!("Failed after retries: {}", e),
    }
}

// 3. Circuit breaker pattern
fn circuit_breaker_pattern() {
    use std::sync::{Arc, Mutex};
    use std::time::{Duration, Instant};
    
    #[derive(Debug)]
    enum CircuitState {
        Closed,
        Open,
        HalfOpen,
    }
    
    struct CircuitBreaker {
        state: Arc<Mutex<CircuitState>>,
        failure_count: Arc<Mutex<u32>>,
        last_failure_time: Arc<Mutex<Option<Instant>>>,
        failure_threshold: u32,
        timeout: Duration,
    }
    
    impl CircuitBreaker {
        fn new(failure_threshold: u32, timeout: Duration) -> Self {
            CircuitBreaker {
                state: Arc::new(Mutex::new(CircuitState::Closed)),
                failure_count: Arc::new(Mutex::new(0)),
                last_failure_time: Arc::new(Mutex::new(None)),
                failure_threshold,
                timeout,
            }
        }
        
        fn call<F, T, E>(&self, operation: F) -> Result<T, E>
        where 
            F: FnOnce() -> Result<T, E>,
        {
            let state = self.state.lock().unwrap();
            
            match *state {
                CircuitState::Open => {
                    let last_failure = self.last_failure_time.lock().unwrap();
                    
                    if let Some(time) = *last_failure {
                        if time.elapsed() > self.timeout {
                            // Try half-open state
                            drop(state);
                            *self.state.lock().unwrap() = CircuitState::HalfOpen;
                            return self.call(operation);
                        }
                    }
                    
                    Err("Circuit is open".into())
                }
                CircuitState::HalfOpen => {
                    // Allow one request through
                    drop(state);
                    let result = operation();
                    
                    match result {
                        Ok(_) => {
                            // Success - close circuit
                            *self.state.lock().unwrap() = CircuitState::Closed;
                            *self.failure_count.lock().unwrap() = 0;
                        }
                        Err(_) => {
                            // Failure - open circuit
                            *self.state.lock().unwrap() = CircuitState::Open;
                            *self.last_failure_time.lock().unwrap() = Some(Instant::now());
                        }
                    }
                    result
                }
                CircuitState::Closed => {
                    drop(state);
                    let result = operation();
                    
                    match result {
                        Ok(_) => {
                            // Success - reset failure count
                            *self.failure_count.lock().unwrap() = 0;
                        }
                        Err(_) => {
                            // Failure - increment count
                            let mut count = self.failure_count.lock().unwrap();
                            *count += 1;
                            
                            if *count >= self.failure_threshold {
                                // Open circuit
                                *self.state.lock().unwrap() = CircuitState::Open;
                                *self.last_failure_time.lock().unwrap() = Some(Instant::now());
                            }
                        }
                    }
                    result
                }
            }
        }
    }
    
    let circuit = CircuitBreaker::new(3, Duration::from_secs(5));
    
    fn external_service() -> Result<String, String> {
        use std::time::{SystemTime, UNIX_EPOCH};
        let timestamp = SystemTime::now()
            .duration_since(UNIX_EPOCH)
            .unwrap()
            .as_secs() % 3;
        
        match timestamp {
            0 => Ok("Service response".to_string()),
            1 => Err("Service error".to_string()),
            2 => Err("Service timeout".to_string()),
            _ => Ok("Service response".to_string()),
        }
    }
    
    for i in 0..10 {
        match circuit.call(external_service) {
            Ok(response) => println!("Call {}: {}", i, response),
            Err(e) => println!("Call {}: {}", i, e),
        }
        
        std::thread::sleep(Duration::from_millis(500));
    }
}

// 4. Bulkhead pattern
fn bulkhead_pattern() {
    use std::sync::{Arc, Mutex};
    use std::thread;
    
    struct Bulkhead {
        semaphore: Arc<Mutex<()>>,
        max_concurrent: usize,
    }
    
    impl Bulkhead {
        fn new(max_concurrent: usize) -> Self {
            Bulkhead {
                semaphore: Arc::new(Mutex::new(())),
                max_concurrent,
            }
        }
        
        fn execute<F, T>(&self, operation: F) -> Result<T, String>
        where 
            F: FnOnce() -> T + Send + 'static,
            T: Send + 'static,
        {
            // Try to acquire the semaphore
            let semaphore = self.semaphore.clone();
            
            // This is a simplified implementation
            // In practice, you'd use a proper semaphore
            thread::spawn(move || {
                // Simulate semaphore acquisition
                std::thread::sleep(Duration::from_millis(100));
                
                let result = operation();
                
                // Release semaphore
                drop(semaphore);
                
                result
            }).join().map_err(|_| "Thread panicked".to_string())
        }
    }
    
    let bulkhead = Bulkhead::new(3);
    
    for i in 0..10 {
        match bulkhead.execute(move || {
            println!("Processing request {}", i);
            std::thread::sleep(Duration::from_millis(200));
            i * 2
        }) {
            Ok(result) => println!("Request {} completed: {}", i, result),
            Err(e) => println!("Request {} failed: {}", i, e),
        }
    }
}

// 5. Timeout handling
fn timeout_handling() {
    use std::time::Duration;
    
    fn operation_with_timeout() -> Result<String, String> {
        let start = std::time::Instant::now();
        let timeout = Duration::from_secs(2);
        
        // Simulate long operation
        std::thread::sleep(Duration::from_secs(3));
        
        if start.elapsed() > timeout {
            Err("Operation timed out".to_string())
        } else {
            Ok("Operation completed".to_string())
        }
    }
    
    match operation_with_timeout() {
        Ok(result) => println!("Success: {}", result),
        Err(e) => println!("Error: {}", e),
    }
}
```

## Common Pitfalls

### Common Error Handling Mistakes
```rust
// 1. Using panic! for recoverable errors
fn panic_for_recoverable() {
    // Bad: using panic for recoverable error
    fn divide_panic(a: f64, b: f64) -> f64 {
        if b == 0.0 {
            panic!("Cannot divide by zero!");
        }
        a / b
    }
    
    // Good: using Result for recoverable error
    fn divide_result(a: f64, b: f64) -> Result<f64, String> {
        if b == 0.0 {
            Err("Cannot divide by zero".to_string())
        } else {
            Ok(a / b)
        }
    }
    
    // This would panic
    // let result = divide_panic(10.0, 0.0);
    
    // This handles the error gracefully
    match divide_result(10.0, 0.0) {
        Ok(result) => println!("Result: {}", result),
        Err(e) => println!("Error: {}", e),
    }
}

// 2. Not handling errors at all
fn not_handling_errors() {
    // Bad: ignoring errors
    fn bad_read_file() {
        let _ = std::fs::read_to_string("file.txt");
        // Error is completely ignored
    }
    
    // Good: handling errors
    fn good_read_file() -> Result<String, std::io::Error> {
        std::fs::read_to_string("file.txt")
    }
    
    match good_read_file() {
        Ok(content) => println!("File content: {}", content),
        Err(e) => println!("Error reading file: {}", e),
    }
}

// 3. Using unwrap() in production code
fn unwrap_in_production() {
    // Bad: using unwrap() in production
    fn bad_config() -> u32 {
        let content = std::fs::read_to_string("config.txt").unwrap();
        content.trim().parse().unwrap()
    }
    
    // Good: handling errors properly
    fn good_config() -> Result<u32, Box<dyn std::error::Error>> {
        let content = std::fs::read_to_string("config.txt")?;
        let port: u32 = content.trim().parse()?;
        Ok(port)
    }
    
    // unwrap() is acceptable in tests or examples
    let _ = std::fs::read_to_string("test.txt").unwrap();
}

// 4. Creating overly generic error types
fn overly_generic_errors() {
    // Bad: too generic
    #[derive(Debug)]
    enum GenericError {
        Error(String),
    }
    
    // Good: specific error types
    #[derive(Debug)]
    enum SpecificError {
        IoError(std::io::Error),
        ParseError(std::num::ParseIntError),
        ValidationError(String),
    }
    
    impl std::fmt::Display for SpecificError {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            match self {
                SpecificError::IoError(e) => write!(f, "IO error: {}", e),
                SpecificError::ParseError(e) => write!(f, "Parse error: {}", e),
                SpecificError::ValidationError(msg) => write!(f, "Validation error: {}", msg),
            }
        }
    }
    
    impl std::error::Error for SpecificError {}
}

// 5. Not providing enough context in errors
fn insufficient_error_context() {
    // Bad: not enough context
    fn bad_parse_config() -> Result<u32, std::num::ParseIntError> {
        let content = std::fs::read_to_string("config.txt")?;
        content.trim().parse()
    }
    
    // Good: providing context
    fn good_parse_config() -> Result<u32, Box<dyn std::error::Error>> {
        let content = std::fs::read_to_string("config.txt")
            .map_err(|e| format!("Failed to read config file: {}", e))?;
        
        let port: u32 = content.trim()
            .parse()
            .map_err(|e| format!("Failed to parse port from '{}': {}", content.trim(), e))?;
        
        if port < 1024 {
            return Err(format!("Port {} must be >= 1024", port).into());
        }
        
        Ok(port)
    }
}

// 6. Mixing different error handling patterns
fn mixed_error_handling() {
    // Bad: inconsistent error handling
    fn inconsistent_error_handling() {
        let result = std::fs::read_to_string("file.txt");
        
        if result.is_ok() {
            println!("File read successfully");
        } else {
            // Different error handling pattern
            match result {
                Ok(content) => println!("Content: {}", content),
                Err(e) => println!("Error: {}", e),
            }
        }
    }
    
    // Good: consistent error handling
    fn consistent_error_handling() -> Result<String, std::io::Error> {
        std::fs::read_to_string("file.txt")
    }
    
    match consistent_error_handling() {
        Ok(content) => println!("Content: {}", content),
        Err(e) => println!("Error: {}", e),
    }
}

// 7. Not using appropriate error types for the domain
fn inappropriate_error_types() {
    // Bad: using generic error for domain-specific logic
    fn bad_user_validation(name: &str, age: i32) -> Result<(), String> {
        if name.is_empty() {
            return Err("Name is empty".to_string());
        }
        
        if age < 0 {
            return Err("Age is negative".to_string());
        }
        
        Ok(())
    }
    
    // Good: using domain-specific error types
    #[derive(Debug)]
    enum UserError {
        InvalidName(String),
        InvalidAge(i32),
    }
    
    impl std::fmt::Display for UserError {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            match self {
                UserError::InvalidName(msg) => write!(f, "Invalid name: {}", msg),
                UserError::InvalidAge(age) => write!(f, "Invalid age: {}", age),
            }
        }
    }
    
    impl std::error::Error for UserError {}
    
    fn good_user_validation(name: &str, age: i32) -> Result<(), UserError> {
        if name.is_empty() {
            return Err(UserError::InvalidName("Name cannot be empty".to_string()));
        }
        
        if age < 0 {
            return Err(UserError::InvalidAge(age));
        }
        
        Ok(())
    }
}

// 8. Not considering performance in error handling
fn performance_in_error_handling() {
    // Bad: expensive error creation
    fn expensive_error_creation() -> Result<(), String> {
        let large_data = vec![0; 1000000];
        
        if some_condition() {
            return Err(format!("Error with large data: {:?}", large_data));
        }
        
        Ok(())
    }
    
    fn some_condition() -> bool {
        false
    }
    
    // Good: lazy error creation
    fn lazy_error_creation() -> Result<(), Box<dyn std::error::Error>> {
        if some_condition() {
            return Err("Simple error message".into());
        }
        
        Ok(())
    }
    
    // Good: using custom error types with minimal overhead
    #[derive(Debug)]
    struct SimpleError {
        message: &'static str,
    }
    
    impl std::fmt::Display for SimpleError {
        fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
            write!(f, "{}", self.message)
        }
    }
    
    impl std::error::Error for SimpleError {}
    
    fn efficient_error_creation() -> Result<(), SimpleError> {
        if some_condition() {
            return Err(SimpleError {
                message: "Efficient error message",
            });
        }
        
        Ok(())
    }
}
```

## Summary

Rust error handling provides robust and safe ways to manage failures:

**Core Types:**
- `Result<T, E>` for recoverable errors
- `Option<T>` for optional values
- Custom error types with `Display` and `Debug`
- Error propagation with `?` operator

**Error Patterns:**
- Comprehensive `match` patterns for error handling
- Error recovery and fallback strategies
- Retry mechanisms with exponential backoff
- Circuit breaker patterns for resilience

**Advanced Features:**
- Custom error types with context
- Error chaining and source tracking
- Error aggregation for multiple failures
- Error backtraces for debugging

**Best Practices:**
- Use `Result` for recoverable errors
- Use `panic!` for programming errors
- Provide meaningful error messages
- Use appropriate error types
- Handle errors at the right level
- Use `Option` for optional values

**Error Recovery:**
- Graceful degradation
- Retry with backoff
- Circuit breaker pattern
- Bulkhead pattern
- Timeout handling

**Common Pitfalls:**
- Using `panic!` for recoverable errors
- Not handling errors at all
- Using `unwrap()` in production
- Overly generic error types
- Insufficient error context
- Mixed error handling patterns
- Inappropriate error types
- Performance issues

**Guidelines:**
- Be explicit about error conditions
- Provide context and helpful messages
- Use type-safe error handling
- Consider performance implications
- Implement proper error propagation
- Use appropriate recovery strategies

Rust's error handling system, combined with its type system and ownership model, ensures that errors are handled explicitly and safely, preventing many common runtime errors while maintaining performance and expressiveness.
