# Rust Macros

## Macro Basics

### Declarative Macros
```rust
// Basic macro definition
macro_rules! say_hello {
    () => {
        println!("Hello, World!");
    };
}

fn basic_macro() {
    say_hello!();
}

// Macro with parameters
macro_rules! create_function {
    ($name:ident) => {
        fn $name() -> &'static str {
            stringify!($name)
        }
    };
}

// Create functions using macro
create_function!(foo);
create_function!(bar);

fn parameterized_macro() {
    println!("{}", foo());
    println!("{}", bar());
}

// Macro with multiple patterns
macro_rules! calculate {
    (add $a:expr, $b:expr) => {
        $a + $b
    };
    (sub $a:expr, $b:expr) => {
        $a - $b
    };
    (mul $a:expr, $b:expr) => {
        $a * $b
    };
    (div $a:expr, $b:expr) => {
        $a / $b
    };
}

fn multiple_patterns() {
    println!("2 + 3 = {}", calculate!(add 2, 3));
    println!("10 - 4 = {}", calculate!(sub 10, 4));
    println!("6 * 7 = {}", calculate!(mul 6, 7));
    println!("15 / 3 = {}", calculate!(div 15, 3));
}

// Macro with repetitions
macro_rules! vec_of_strings {
    ($($x:expr),*) => {
        {
            let mut temp_vec = Vec::new();
            $(
                temp_vec.push($x.to_string());
            )*
            temp_vec
        }
    };
}

fn repetitions_macro() {
    let strings = vec_of_strings!("hello", "world", "rust", "macros");
    for s in strings {
        println!("{}", s);
    }
}

// Macro with different fragment types
macro_rules! test_fragment_types {
    // Expression
    ($e:expr) => {
        println!("Expression: {}", $e);
    };
    // Type
    ($t:ty) => {
        println!("Type: {}", std::any::type_name::<$t>());
    };
    // Identifier
    ($i:ident) => {
        println!("Identifier: {}", stringify!($i));
    };
    // Literal
    ($l:literal) => {
        println!("Literal: {}", $l);
    };
}

fn fragment_types() {
    test_fragment_types!(42 + 10);
    test_fragment_types!(String);
    test_fragment_types!(my_variable);
    test_fragment_types!(3.14159);
}
```

### Macro Patterns and Design
```rust
// Macro with optional parameters
macro_rules! optional_params {
    ($name:expr) => {
        format!("Hello, {}!", $name)
    };
    ($name:expr, $greeting:expr) => {
        format!("{}, {}!", $greeting, $name)
    };
}

fn optional_parameters() {
    println!("{}", optional_params!("Alice"));
    println!("{}", optional_params!("Bob", "Hi"));
}

// Macro with variable number of parameters
macro_rules! calculate_sum {
    ($($x:expr),*) => {
        {
            let mut sum = 0;
            $(
                sum += $x;
            )*
            sum
        }
    };
}

fn variable_parameters() {
    let sum = calculate_sum!(1, 2, 3, 4, 5);
    println!("Sum: {}", sum);
}

// Macro with complex pattern matching
macro_rules! hashmap {
    ($($key:expr => $value:expr),*) => {
        {
            let mut map = std::collections::HashMap::new();
            $(
                map.insert($key, $value);
            )*
            map
        }
    };
}

fn complex_patterns() {
    let map = hashmap!(
        "one" => 1,
        "two" => 2,
        "three" => 3
    );
    
    for (key, value) in map {
        println!("{}: {}", key, value);
    }
}

// Macro with nested patterns
macro_rules! impl_ops {
    ($struct_name:ident, $field:ident: $field_type:ty) => {
        impl std::fmt::Debug for $struct_name {
            fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
                write!(f, "{} {{ {}: {:?} }}", stringify!($struct_name), stringify!($field), self.$field)
            }
        }
        
        impl Clone for $struct_name {
            fn clone(&self) -> Self {
                $struct_name { $field: self.$field.clone() }
            }
        }
    };
}

struct MyStruct {
    value: i32,
}

impl_ops!(MyStruct, value: i32);

fn nested_patterns() {
    let s = MyStruct { value: 42 };
    println!("{:?}", s);
    let cloned = s.clone();
    println!("{:?}", cloned);
}
```

## Procedural Macros

### Function-like Procedural Macros
```rust
// Procedural macro definition (requires proc-macro crate)
/*
use proc_macro::TokenStream;
use quote::quote;
use syn::{parse_macro_input, ItemFn};

#[proc_macro]
pub fn timed_function(input: TokenStream) -> TokenStream {
    let input_fn = parse_macro_input!(input as ItemFn);
    
    let name = &input_fn.sig.ident;
    let inputs = &input_fn.sig.inputs;
    let output = &input_fn.sig.output;
    let block = &input_fn.block;
    
    let expanded = quote! {
        fn #name(#inputs) #output {
            let start = std::time::Instant::now();
            let result = #block;
            let duration = start.elapsed();
            println!("Function {} took {:?}", stringify!(#name), duration);
            result
        }
    };
    
    expanded.into()
}
*/

// Using the procedural macro
/*
use my_macros::timed_function;

timed_function! {
    fn fibonacci(n: u32) -> u64 {
        match n {
            0 => 0,
            1 => 1,
            n => fibonacci(n - 1) + fibonacci(n - 2),
        }
    }
}

fn use_timed_function() {
    let result = fibonacci(10);
    println!("Fibonacci(10) = {}", result);
}
*/
```

### Attribute-like Procedural Macros
```rust
// Attribute-like procedural macro
/*
use proc_macro::TokenStream;
use quote::quote;
use syn::{parse_macro_input, AttributeArgs, ItemFn};

#[proc_macro_attribute]
pub fn trace_function(args: TokenStream, input: TokenStream) -> TokenStream {
    let _args = parse_macro_input!(args as AttributeArgs);
    let input_fn = parse_macro_input!(input as ItemFn);
    
    let name = &input_fn.sig.ident;
    let inputs = &input_fn.sig.inputs;
    let output = &input_fn.sig.output;
    let block = &input_fn.block;
    
    let expanded = quote! {
        fn #name(#inputs) #output {
            println!("Entering function: {}", stringify!(#name));
            let result = #block;
            println!("Exiting function: {}", stringify!(#name));
            result
        }
    };
    
    expanded.into()
}
*/

// Using the attribute macro
/*
use my_macros::trace_function;

#[trace_function]
fn add(a: i32, b: i32) -> i32 {
    a + b
}

fn use_trace_attribute() {
    let result = add(5, 3);
    println!("Result: {}", result);
}
*/
```

### Derive-like Procedural Macros
```rust
// Derive macro implementation
/*
use proc_macro::TokenStream;
use quote::quote;
use syn::{parse_macro_input, DeriveInput};

#[proc_macro_derive(Builder)]
pub fn derive_builder(input: TokenStream) -> TokenStream {
    let input = parse_macro_input!(input as DeriveInput);
    
    let name = &input.ident;
    let builder_name = format!("{}Builder", name);
    let builder_ident = syn::Ident::new(&builder_name, proc_macro2::Span::call_site());
    
    let fields = if let syn::Data::Struct(syn::DataStruct { fields, .. }) = &input.data {
        fields
    } else {
        panic!("Builder can only be derived for structs");
    };
    
    let field_names: Vec<_> = fields.iter().map(|f| &f.ident).collect();
    let field_types: Vec<_> = fields.iter().map(|f| &f.ty).collect();
    
    let expanded = quote! {
        pub struct #builder_ident {
            #(
                #field_names: Option<#field_types>,
            )*
        }
        
        impl #builder_ident {
            pub fn new() -> Self {
                #builder_ident {
                    #(
                        #field_names: None,
                    )*
                }
            }
            
            #(
                pub fn #field_names(mut self, #field_names: #field_types) -> Self {
                    self.#field_names = Some(#field_names);
                    self
                }
            )*
            
            pub fn build(self) -> Result<#name, String> {
                Ok(#name {
                    #(
                        #field_names: self.#field_names.ok_or_else(|| 
                            format!("Missing field: {}", stringify!(#field_names))
                        )?,
                    )*
                })
            }
        }
        
        impl #name {
            pub fn builder() -> #builder_ident {
                #builder_ident::new()
            }
        }
    };
    
    expanded.into()
}
*/

// Using the derive macro
/*
use my_macros::Builder;

#[derive(Builder)]
struct User {
    name: String,
    age: u32,
    email: String,
}

fn use_builder_derive() {
    let user = User::builder()
        .name("Alice".to_string())
        .age(30)
        .email("alice@example.com".to_string())
        .build()
        .unwrap();
    
    println!("User: {} ({})", user.name, user.age);
}
*/
```

## Advanced Macro Techniques

### Macro Hygiene
```rust
// Macro hygiene example
macro_rules! bad_macro {
    ($x:expr) => {
        let y = $x * 2;
        println!("y = {}", y);
    };
}

macro_rules! good_macro {
    ($x:expr) => {
        let y = $x * 2;
        println!("y = {}", y);
    };
}

fn macro_hygiene() {
    let y = 10;
    
    // Bad macro - can cause variable shadowing
    bad_macro!(5);
    
    // Good macro - uses local scope
    good_macro!(5);
    
    println!("Original y = {}", y);
}

// Macro with local variables
macro_rules! calculate_and_print {
    ($x:expr) => {
        let result = $x * 2;
        println!("Result: {}", result);
    };
}

fn local_variables() {
    let x = 10;
    calculate_and_print!(x);
    calculate_and_print!(5);
}

// Macro with hygiene issues and solutions
macro_rules! problematic_macro {
    ($x:expr) => {
        let x = $x + 1;
        println!("x = {}", x);
    };
}

macro_rules! safe_macro {
    ($x:expr) => {
        let result = $x + 1;
        println!("result = {}", result);
    };
}

fn hygiene_solutions() {
    let x = 10;
    
    // Problematic - shadows the input variable
    problematic_macro!(x);
    
    // Safe - doesn't shadow
    safe_macro!(x);
    
    println!("Original x = {}", x);
}
```

### Recursive Macros
```rust
// Recursive macro for counting
macro_rules! count {
    () => { 0 };
    ($head:tt $($tail:tt)*) => { 1 + count!($($tail)*) };
}

fn recursive_counting() {
    let count = count!(a b c d e);
    println!("Count: {}", count);
}

// Recursive macro for operations
macro_rules! max {
    ($x:expr) => { $x };
    ($x:expr, $y:expr) => {
        if $x > $y { $x } else { $y }
    };
    ($x:expr, $y:expr, $($rest:expr),+) => {
        max!(max!($x, $y), $($rest),+)
    };
}

fn recursive_operations() {
    let max_val = max!(10, 20, 5, 15, 30, 25);
    println!("Max: {}", max_val);
}

// Recursive macro for nested structures
macro_rules! create_nested {
    ($value:expr) => { $value };
    ($value:expr, $($rest:expr),+) => {
        {
            let nested = create_nested!($($rest),+);
            ($value, nested)
        }
    };
}

fn nested_structures() {
    let nested = create_nested!(1, 2, 3, 4, 5);
    println!("Nested: {:?}", nested);
}

// Recursive macro with termination condition
macro_rules! factorial {
    (0) => { 1 };
    (1) => { 1 };
    ($n:expr) => {
        $n * factorial!($n - 1)
    };
}

fn recursive_factorial() {
    let fact = factorial!(5);
    println!("5! = {}", fact);
}
```

### Macro Composition
```rust
// Composing multiple macros
macro_rules! debug_print {
    ($($arg:tt)*) => {
        {
            println!("DEBUG: {}", format!($($arg)*));
        }
    };
}

macro_rules! error_print {
    ($($arg:tt)*) => {
        {
            eprintln!("ERROR: {}", format!($($arg)*));
        }
    };
}

macro_rules! log_message {
    (debug, $($arg:tt)*) => {
        debug_print!($($arg)*);
    };
    (error, $($arg:tt)*) => {
        error_print!($($arg)*);
    };
}

fn macro_composition() {
    log_message!(debug, "This is a debug message: {}", 42);
    log_message!(error, "This is an error message: {}", "Something went wrong");
}

// Macro that generates other macros
macro_rules! create_math_macros {
    ($name:ident, $op:tt) => {
        macro_rules! $name {
            ($a:expr, $b:expr) => {
                $a $op $b
            };
        }
    };
}

create_math_macros!(add_macro, +);
create_math_macros!(sub_macro, -);
create_math_macros!(mul_macro, *);
create_math_macros!(div_macro, /);

fn macro_generation() {
    println!("5 + 3 = {}", add_macro!(5, 3));
    println!("10 - 4 = {}", sub_macro!(10, 4));
    println!("6 * 7 = {}", mul_macro!(6, 7));
    println!("20 / 5 = {}", div_macro!(20, 5));
}

// Macro with conditional compilation
macro_rules! conditional_print {
    ($($arg:tt)*) => {
        {
            #[cfg(debug_assertions)]
            {
                println!("DEBUG: {}", format!($($arg)*));
            }
            #[cfg(not(debug_assertions))]
            {
                // In release mode, don't print
            }
        }
    };
}

fn conditional_compilation() {
    conditional_print!("This prints in debug mode");
    conditional_print!("This also prints in debug mode: {}", 42);
}
```

## Practical Macro Examples

### Logging Macro
```rust
// Logging macro with different levels
macro_rules! log {
    (debug, $($arg:tt)*) => {
        #[cfg(debug_assertions)]
        {
            println!("[DEBUG] [{}:{}] {}", 
                     file!(), line!(), format!($($arg)*));
        }
    };
    (info, $($arg:tt)*) => {
        println!("[INFO] [{}:{}] {}", 
                 file!(), line!(), format!($($arg)*));
    };
    (warn, $($arg:tt)*) => {
        println!("[WARN] [{}:{}] {}", 
                 file!(), line!(), format!($($arg)*));
    };
    (error, $($arg:tt)*) => {
        eprintln!("[ERROR] [{}:{}] {}", 
                  file!(), line!(), format!($($arg)*));
    };
}

fn logging_example() {
    log!(debug, "This is a debug message");
    log!(info, "This is an info message");
    log!(warn, "This is a warning message");
    log!(error, "This is an error message");
}

// Structured logging macro
macro_rules! structured_log {
    ($level:ident, $($field:ident = $value:expr),*) => {
        {
            let timestamp = chrono::Utc::now().format("%Y-%m-%d %H:%M:%S");
            println!("[{}] [{}] [{}:{}] {}", 
                     timestamp, 
                     stringify!($level), 
                     file!(), 
                     line!(), 
                     format!($($field = $value),*)
            );
        }
    };
}

fn structured_logging() {
    // Note: This would require the chrono crate
    // structured_log!(info, user = "Alice", action = "login", status = "success");
    println!("Structured logging example (requires chrono crate)");
}
```

### Testing Macro
```rust
// Simple assertion macro
macro_rules! assert_approx_eq {
    ($left:expr, $right:expr, $tolerance:expr) => {
        {
            let diff = ($left - $right).abs();
            if diff > $tolerance {
                panic!("assertion failed: `{} ≈ {}` (tolerance: {}), actual difference: {}", 
                       stringify!($left), 
                       stringify!($right), 
                       $tolerance, 
                       diff);
            }
        }
    };
}

fn testing_macro() {
    let a = 3.14159;
    let b = 3.14160;
    
    assert_approx_eq!(a, b, 0.001);
    println!("Approximately equal assertion passed");
}

// Test case macro
macro_rules! test_case {
    ($name:ident, $input:expr, $expected:expr) => {
        #[test]
        fn $name() {
            assert_eq!($input, $expected);
        }
    };
}

// Generate test cases
test_case!(test_add_1, 2 + 3, 5);
test_case!(test_add_2, 10 + 20, 30);
test_case!(test_add_3, 100 + 200, 300);

// Benchmark macro
macro_rules! benchmark {
    ($name:ident, $code:block) => {
        fn $name() {
            let start = std::time::Instant::now();
            for _ in 0..1000 {
                $code
            }
            let duration = start.elapsed();
            println!("Benchmark {}: {:?}", stringify!($name), duration);
        }
    };
}

benchmark!(benchmark_loop, {
    let mut sum = 0;
    for i in 0..1000 {
        sum += i;
    }
});

fn run_benchmark() {
    benchmark_loop();
}
```

### Serialization Macro
```rust
// Simple serialization macro
macro_rules! impl_serialize {
    ($struct_name:ident, $($field:ident),*) => {
        impl $struct_name {
            fn to_json(&self) -> String {
                let mut json = String::from("{");
                $(
                    json.push_str(&format!("\"{}\": \"{}\"", stringify!($field), self.$field));
                    json.push_str(", ");
                )*
                json.pop(); // Remove last comma
                json.pop(); // Remove last space
                json.push_str("}");
                json
            }
        }
    };
}

struct Person {
    name: String,
    age: u32,
    city: String,
}

impl_serialize!(Person, name, age, city);

fn serialization_example() {
    let person = Person {
        name: "Alice".to_string(),
        age: 30,
        city: "New York".to_string(),
    };
    
    let json = person.to_json();
    println!("JSON: {}", json);
}

// Deserialization macro
macro_rules! impl_deserialize {
    ($struct_name:ident, $($field:ident: $field_type:ty),*) => {
        impl $struct_name {
            fn from_json(json: &str) -> Result<Self, String> {
                // Simplified JSON parsing
                let mut result = $struct_name {
                    $(
                        $field: Default::default(),
                    )*
                };
                
                // In a real implementation, you'd parse the JSON string
                // For this example, we'll use placeholder values
                
                Ok(result)
            }
        }
    };
}

impl_deserialize!(Person, name: String, age: u32, city: String);

fn deserialization_example() {
    let json = r#"{"name": "Bob", "age": 25, "city": "Boston"}"#;
    
    match Person::from_json(json) {
        Ok(person) => println!("Deserialized: {:?}", person),
        Err(e) => println!("Error: {}", e),
    }
}
```

### Database Macro
```rust
// SQL query macro
macro_rules! sql_query {
    ($table:ident, $($field:ident),*; $($condition:tt)*) => {
        {
            let mut query = String::from("SELECT ");
            
            // Add fields
            $(
                query.push_str(&format!("{}, ", stringify!($field)));
            )*
            query.pop(); // Remove last comma
            query.pop(); // Remove last space
            
            query.push_str(&format!(" FROM {}", stringify!($table)));
            
            // Add conditions if any
            if !stringify!($($condition)*).is_empty() {
                query.push_str(" WHERE ");
                query.push_str(&stringify!($($condition)*));
            }
            
            query
        }
    };
}

fn sql_query_example() {
    let query = sql_query!(users, name, age, email; age > 18);
    println!("SQL Query: {}", query);
    
    let query2 = sql_query!(products, name, price;);
    println!("SQL Query: {}", query2);
}

// Insert statement macro
macro_rules! sql_insert {
    ($table:ident, $($field:ident = $value:expr),*) => {
        {
            let mut query = String::from(&format!("INSERT INTO {} (", stringify!($table)));
            
            // Add field names
            $(
                query.push_str(&format!("{}, ", stringify!($field)));
            )*
            query.pop(); // Remove last comma
            query.pop(); // Remove last space
            
            query.push_str(") VALUES (");
            
            // Add values
            $(
                query.push_str(&format!("{}, ", $value));
            )*
            query.pop(); // Remove last comma
            query.pop(); // Remove last space
            
            query.push_str(")");
            
            query
        }
    };
}

fn sql_insert_example() {
    let query = sql_insert!(users, 
        name = "Alice", 
        age = 30, 
        email = "alice@example.com"
    );
    println!("SQL Insert: {}", query);
}
```

## Macro Debugging

### Debugging Techniques
```rust
// Macro that expands to show its own expansion
macro_rules! debug_macro {
    ($($tts:tt)*) => {
        {
            println!("Macro input: {}", stringify!($($tts)*));
            // The actual macro expansion would go here
            println!("Macro expanded successfully");
        }
    };
}

fn debugging_example() {
    debug_macro!(1 + 2 * 3);
    debug_macro!(vec![1, 2, 3]);
}

// Macro with debugging information
macro_rules! debug_expand {
    ($($tts:tt)*) => {
        {
            println!("File: {}", file!());
            println!("Line: {}", line!());
            println!("Column: {}", column!());
            println!("Module: {}", module_path!());
            println!("Input: {}", stringify!($($tts)*));
            // The actual macro expansion
            $($tts)*
        }
    };
}

fn debugging_info() {
    debug_expand!(println!("Hello, World!"));
}

// Macro that shows token tree structure
macro_rules! show_tokens {
    ($($tts:tt)*) => {
        {
            println!("Tokens: {:?}", stringify!($($tts)*));
        }
    };
}

fn token_debugging() {
    show_tokens!(a + b * c);
    show_tokens!(vec![1, 2, 3]);
    show_tokens!(struct Point { x: f64, y: f64 });
}

// Macro compilation debugging
macro_rules! compile_check {
    ($($tts:tt)*) => {
        // This macro helps check if the syntax is correct
        // by attempting to parse the input
        let _ = || {
            $($tts)*
        };
    };
}

fn compilation_debugging() {
    compile_check!(let x = 42);
    compile_check!(fn test() { println!("Hello"); });
    
    // This would cause a compilation error
    // compile_check!(let x = );
}
```

### Common Macro Errors
```rust
// Common error: Missing comma in repetitions
macro_rules! bad_repetition {
    ($($x:expr)*) => {
        vec![$x] // Missing comma
    };
}

macro_rules! good_repetition {
    ($($x:expr)*) => {
        vec![$x,] // Correct comma
    };
}

fn repetition_error() {
    // bad_repetition!(1 2 3); // Would cause error
    let vec = good_repetition!(1 2 3);
    println!("Vector: {:?}", vec);
}

// Common error: Incorrect fragment specifier
macro_rules! bad_fragment {
    ($x:ident) => {
        println!("{}", $x); // $x is an identifier, not a string
    };
}

macro_rules! good_fragment {
    ($x:ident) => {
        println!("{}", stringify!($x)); // Convert to string
    };
}

fn fragment_error() {
    good_fragment!(my_variable);
}

// Common error: Macro hygiene violation
macro_rules! bad_hygiene {
    () => {
        let x = 42;
        println!("x = {}", x);
    };
}

fn hygiene_error() {
    let x = 10;
    bad_hygiene!(); // This creates a new x, doesn't use the outer one
    println!("Outer x = {}", x);
}

// Common error: Recursive macro without termination
macro_rules! infinite_recursion {
    ($x:expr) => {
        infinite_recursion!($x); // No termination condition
    };
}

// Good recursive macro with termination
macro_rules! finite_recursion {
    (0) => { 0 };
    ($n:expr) => {
        1 + finite_recursion!($n - 1)
    };
}

fn recursion_error() {
    let result = finite_recursion!(5);
    println!("Result: {}", result);
}
```

## Best Practices

### Macro Design Guidelines
```rust
// 1. Use descriptive macro names
macro_rules! calculate_area_of_circle {
    ($radius:expr) => {
        3.14159 * $radius * $radius
    };
}

fn descriptive_names() {
    let area = calculate_area_of_circle!(5.0);
    println!("Area: {}", area);
}

// 2. Document macros with comments
/// Calculates the area of a circle given its radius
/// 
/// # Arguments
/// 
/// * `$radius:expr` - The radius of the circle
/// 
/// # Examples
/// 
/// ```
/// let area = circle_area!(5.0);
/// ```
macro_rules! circle_area {
    ($radius:expr) => {
        3.14159 * $radius * $radius
    };
}

fn documented_macros() {
    let area = circle_area!(3.0);
    println!("Area: {}", area);
}

// 3. Use appropriate fragment specifiers
macro_rules! create_function {
    ($name:ident) => {
        fn $name() -> &'static str {
            stringify!($name)
        }
    };
}

fn appropriate_fragments() {
    create_function!(my_function);
    println!("{}", my_function());
}

// 4. Handle edge cases
macro_rules! safe_divide {
    ($a:expr, $b:expr) => {
        {
            let a = $a;
            let b = $b;
            if b == 0 {
                panic!("Division by zero");
            }
            a / b
        }
    };
}

fn edge_cases() {
    let result = safe_divide!(10, 2);
    println!("Result: {}", result);
    
    // This would panic
    // let result = safe_divide!(10, 0);
}

// 5. Use local variables to avoid hygiene issues
macro_rules! safe_increment {
    ($var:ident) => {
        {
            let temp = $var;
            $var = temp + 1;
        }
    };
}

fn hygiene_best_practices() {
    let mut x = 10;
    safe_increment!(x);
    println!("x = {}", x);
}

// 6. Test macros thoroughly
macro_rules! max_three {
    ($a:expr, $b:expr, $c:expr) => {
        {
            let a = $a;
            let b = $b;
            let c = $c;
            
            if a >= b && a >= c {
                a
            } else if b >= a && b >= c {
                b
            } else {
                c
            }
        }
    };
}

fn test_macros() {
    assert_eq!(max_three!(1, 2, 3), 3);
    assert_eq!(max_three!(3, 2, 1), 3);
    assert_eq!(max_three!(2, 3, 1), 3);
    assert_eq!(max_three!(1, 1, 1), 1);
    
    println!("All macro tests passed");
}

// 7. Use macros for repetitive code patterns
macro_rules! impl_debug {
    ($struct_name:ident, $($field:ident),*) => {
        impl std::fmt::Debug for $struct_name {
            fn fmt(&self, f: &mut std::fmt::Formatter) -> std::fmt::Result {
                write!(f, "{} {{ ", stringify!($struct_name))?;
                $(
                    write!(f, "{}: {:?}, ", stringify!($field), self.$field)?;
                )*
                write!(f, " }}")
            }
        }
    };
}

struct Point {
    x: f64,
    y: f64,
}

impl_debug!(Point, x, y);

fn repetitive_patterns() {
    let point = Point { x: 1.0, y: 2.0 };
    println!("{:?}", point);
}
```

### Performance Considerations
```rust
// 1. Avoid expensive operations in macros
macro_rules! bad_performance {
    ($x:expr) => {
        {
            let start = std::time::Instant::now();
            let result = $x;
            let duration = start.elapsed();
            println!("Operation took: {:?}", duration);
            result
        }
    };
}

macro_rules! good_performance {
    ($x:expr) => {
        {
            // Only measure when needed
            #[cfg(feature = "benchmark")]
            {
                let start = std::time::Instant::now();
                let result = $x;
                let duration = start.elapsed();
                println!("Operation took: {:?}", duration);
                result
            }
            #[cfg(not(feature = "benchmark"))]
            {
                $x
            }
        }
    };
}

fn performance_considerations() {
    let result = good_performance!(2 + 3);
    println!("Result: {}", result);
}

// 2. Use compile-time evaluation when possible
macro_rules! compile_time_calc {
    ($a:expr, $b:expr) => {
        // This will be evaluated at compile time if possible
        const RESULT: i32 = $a + $b;
        RESULT
    };
}

fn compile_time_evaluation() {
    let result = compile_time_calc!(10, 20);
    println!("Compile-time result: {}", result);
}

// 3. Minimize code bloat
macro_rules! minimal_bloat {
    ($name:ident, $body:block) => {
        fn $name() {
            $body
        }
    };
}

minimal_bloat!(test_function, {
    println!("This is a test function");
});

fn code_bloat() {
    test_function();
}

// 4. Use macros for zero-cost abstractions
macro_rules! zero_cost {
    ($operation:expr) => {
        // This should compile to the same code as the direct operation
        $operation
    };
}

fn zero_cost_abstractions() {
    let result = zero_cost!(2 + 3);
    println!("Zero-cost result: {}", result);
}
```

## Common Pitfalls

### Common Macro Mistakes
```rust
// 1. Variable capture issues
macro_rules! bad_capture {
    ($x:expr) => {
        println!("x = {}", x); // Captures x from outside
    };
}

macro_rules! good_capture {
    ($x:expr) => {
        {
            let x = $x; // Create local variable
            println!("x = {}", x);
        }
    };
}

fn variable_capture_issues() {
    let x = 10;
    good_capture!(x);
}

// 2. Multiple evaluation issues
macro_rules! bad_multiple_eval {
    ($x:expr) => {
        $x + $x // $x is evaluated twice
    };
}

macro_rules! good_multiple_eval {
    ($x:expr) => {
        {
            let x = $x; // Evaluate once
            x + x
        }
    };
}

fn multiple_evaluation_issues() {
    let mut counter = 0;
    let increment = || {
        counter += 1;
        counter
    };
    
    let result = good_multiple_eval!(increment());
    println!("Result: {}, Counter: {}", result, counter);
}

// 3. Pattern matching limitations
macro_rules! limited_pattern {
    ($x:pat) => {
        match 42 {
            $x => println!("Matched"),
            _ => println!("No match"),
        }
    };
}

fn pattern_limitations() {
    // This works
    limited_pattern!(n if n > 0);
    
    // This might not work in all cases
    // limited_pattern!((a, b));
}

// 4. Hygiene violations
macro_rules! hygiene_violation {
    ($x:ident) => {
        let $x = 42; // Creates new variable
        println!("x = {}", $x);
    };
}

fn hygiene_violations() {
    let x = 10;
    hygiene_violation!(x); // Doesn't affect outer x
    println!("Outer x = {}", x);
}

// 5. Expansion order issues
macro_rules! expansion_order {
    ($x:expr) => {
        println!("Before: {}", $x);
        println!("After: {}", $x); // Might not see changes
    };
}

fn expansion_order_issues() {
    let mut x = 10;
    expansion_order!(x);
    x = 20;
    expansion_order!(x);
}

// 6. Recursive macro depth limits
macro_rules! deep_recursion {
    (0) => { 0 };
    ($n:expr) => {
        1 + deep_recursion!($n - 1)
    };
}

fn recursion_depth_limits() {
    // This might hit recursion limits
    // let result = deep_recursion!(1000);
    
    // Better to use iteration for deep recursion
    let result = (0..1000).sum::<i32>();
    println!("Result: {}", result);
}

// 7. Macro naming conflicts
macro_rules! println {
    ($($arg:tt)*) => {
        std::println!("Custom: {}", format!($($arg)*));
    };
}

fn naming_conflicts() {
    // This shadows the standard println!
    println!("Custom message");
    
    // To use the original println:
    std::println!("Standard message");
}

// 8. Macro expansion in unexpected contexts
macro_rules! context_issue {
    ($x:expr) => {
        // This might not work in all contexts
        let y = $x;
        y
    };
}

fn context_issues() {
    // This works
    let result = context_issue!(42);
    println!("Result: {}", result);
    
    // This might not work in certain contexts
    // const CONST: i32 = context_issue!(42);
}
```

## Summary

Rust macros provide powerful metaprogramming capabilities:

**Declarative Macros:**
- `macro_rules!` for pattern-based code generation
- Fragment specifiers for different token types
- Repetitions for variable arguments
- Pattern matching and recursion

**Procedural Macros:**
- Function-like macros for code transformation
- Attribute-like macros for code annotation
- Derive macros for automatic trait implementation
- Custom derive macros for boilerplate reduction

**Advanced Techniques:**
- Macro hygiene for variable scoping
- Recursive macros for complex patterns
- Macro composition and nesting
- Conditional compilation in macros

**Practical Applications:**
- Logging and debugging macros
- Testing and benchmarking macros
- Serialization/deserialization macros
- Database query builders
- Code generation for repetitive patterns

**Debugging:**
- Macro expansion debugging
- Token tree visualization
- Compilation error analysis
- Common error patterns and solutions

**Best Practices:**
- Use descriptive names and documentation
- Handle edge cases properly
- Avoid variable capture issues
- Minimize code bloat
- Test macros thoroughly
- Consider performance implications

**Common Pitfalls:**
- Variable capture problems
- Multiple evaluation issues
- Pattern matching limitations
- Hygiene violations
- Expansion order problems
- Recursion depth limits
- Naming conflicts
- Context-dependent behavior

**Guidelines:**
- Use macros for repetitive code patterns
- Prefer functions when macros aren't necessary
- Document macro behavior and usage
- Test macros with various inputs
- Consider compile-time vs runtime costs
- Use appropriate fragment specifiers

**Tools and Techniques:**
- `cargo expand` for macro expansion
- `rustc --pretty expanded` for debugging
- IDE support for macro development
- Testing frameworks for macro verification
- Benchmarking tools for performance analysis

Rust macros enable powerful metaprogramming while maintaining safety and performance. They provide a way to generate code at compile time, reduce boilerplate, and create domain-specific languages within Rust. Understanding macro rules, hygiene, and best practices is essential for effective macro development.
