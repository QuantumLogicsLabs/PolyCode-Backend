# Rust Control Flow

## Conditional Statements

### if Expressions
```rust
// Basic if statement
fn basic_if() {
    let number = 3;
    
    if number < 5 {
        println!("condition was true");
    } else {
        println!("condition was false");
    }
}

// if as an expression
fn if_expression() {
    let condition = true;
    let number = if condition { 5 } else { 6 };
    println!("The value of number is: {}", number);
}

// Multiple conditions
fn multiple_conditions() {
    let number = 6;
    
    if number % 4 == 0 {
        println!("number is divisible by 4");
    } else if number % 3 == 0 {
        println!("number is divisible by 3");
    } else if number % 2 == 0 {
        println!("number is divisible by 2");
    } else {
        println!("number is not divisible by 4, 3, or 2");
    }
}

// Nested if statements
fn nested_if() {
    let x = 10;
    let y = 20;
    
    if x > 5 {
        if y > 15 {
            println!("x > 5 and y > 15");
        } else {
            println!("x > 5 but y <= 15");
        }
    } else {
        println!("x <= 5");
    }
}

// Using let with if expressions
fn let_with_if() {
    let condition = true;
    let x = if condition { 5 } else { 6 };
    
    let y = if x > 5 {
        "Greater than 5"
    } else {
        "Not greater than 5"
    };
    
    println!("x: {}, y: {}", x, y);
}
```

### match Expressions
```rust
// Basic match
fn basic_match() {
    let number = 13;
    
    match number {
        1 => println!("One!"),
        2 => println!("Two!"),
        3 => println!("Three!"),
        _ => println!("Not 1, 2, or 3"),
    }
}

// Match with ranges
fn match_ranges() {
    let number = 13;
    
    match number {
        1 => println!("One!"),
        2 | 3 | 5 | 7 | 11 => println!("Prime!"),
        13..=19 => println!("Teenager!"),
        _ => println!("Not special"),
    }
}

// Match as expression
fn match_expression() {
    let boolean = true;
    
    let binary = match boolean {
        false => 0,
        true => 1,
    };
    
    println!("{} -> {}", boolean, binary);
}

// Match with guards
fn match_guards() {
    let number = 4;
    
    match number {
        x if x % 2 == 0 => println!("{} is even", x),
        x if x % 2 != 0 => println!("{} is odd", x),
        _ => unreachable!(),
    }
}

// Destructuring with match
fn match_destructuring() {
    let point = (0, -2);
    
    match point {
        (0, y) => println!("On the y axis at {}", y),
        (x, 0) => println!("On the x axis at {}", x),
        (x, y) => println!("On neither axis: ({}, {})", x, y),
    }
}

// Match with Option
fn match_option() {
    let some_value = Some(5);
    let none_value: Option<i32> = None;
    
    match some_value {
        Some(value) => println!("Got a value: {}", value),
        None => println!("Got nothing"),
    }
    
    match none_value {
        Some(value) => println!("Got a value: {}", value),
        None => println!("Got nothing"),
    }
}

// Match with Result
fn match_result() {
    let success = Ok(200);
    let failure = Err("Not found");
    
    match success {
        Ok(code) => println!("Success code: {}", code),
        Err(message) => println!("Error: {}", message),
    }
    
    match failure {
        Ok(code) => println!("Success code: {}", code),
        Err(message) => println!("Error: {}", message),
    }
}

// Exhaustive match
fn exhaustive_match() {
    let coin = Coin::Quarter;
    
    match coin {
        Coin::Penny => 1,
        Coin::Nickel => 5,
        Coin::Dime => 10,
        Coin::Quarter => 25,
    }
}

#[derive(Debug)]
enum Coin {
    Penny,
    Nickel,
    Dime,
    Quarter,
}

// Match with binding
fn match_binding() {
    let points = Point { x: 0, y: 7 };
    
    match points {
        Point { x, y: 0 } => println!("On the x axis at {}", x),
        Point { x: 0, y } => println!("On the y axis at {}", y),
        Point { x, y } => println!("On neither axis: ({}, {})", x, y),
    }
}

struct Point {
    x: i32,
    y: i32,
}

// Match with @ binding
fn match_at_binding() {
    let message = Message::Hello { id: 5 };
    
    match message {
        Message::Hello { id: id @ 3..=7 } => {
            println!("Found an id in range [3, 7]: {}", id)
        }
        Message::Hello { id: 10..=12 } => {
            println!("Found an id in range [10, 12]")
        }
        Message::Hello { id } => {
            println!("Found some other id: {}", id)
        }
    }
}

enum Message {
    Hello { id: i32 },
}
```

## Loops

### loop Loop
```rust
// Basic loop
fn basic_loop() {
    let mut counter = 0;
    
    loop {
        println!("again!");
        counter += 1;
        
        if counter == 3 {
            break;
        }
    }
}

// loop with return value
fn loop_return_value() {
    let mut counter = 0;
    
    let result = loop {
        counter += 1;
        
        if counter == 10 {
            break counter * 2;
        }
    };
    
    println!("The result is {}", result);
}

// Nested loops with labels
fn nested_loops() {
    let mut count = 0;
    'counting_up: loop {
        println!("count = {}", count);
        let mut remaining = 10;
        
        loop {
            println!("remaining = {}", remaining);
            if remaining == 9 {
                break;
            }
            if count == 2 {
                break 'counting_up;
            }
            remaining -= 1;
        }
        
        count += 1;
    }
    
    println!("End count = {}", count);
}

// loop with continue
fn loop_continue() {
    let mut count = 0;
    
    loop {
        count += 1;
        
        if count == 2 {
            continue;
        }
        
        println!("count = {}", count);
        
        if count == 5 {
            break;
        }
    }
}
```

### while Loop
```rust
// Basic while loop
fn basic_while() {
    let mut number = 3;
    
    while number != 0 {
        println!("{}!", number);
        number -= 1;
    }
    
    println!("LIFTOFF!!!");
}

// while with conditions
fn while_conditions() {
    let a = [10, 20, 30, 40, 50];
    let mut index = 0;
    
    while index < 5 {
        println!("The value is: {}", a[index]);
        index += 1;
    }
}

// while with break
fn while_break() {
    let mut number = 3;
    
    while number != 0 {
        println!("{}!", number);
        number -= 1;
        
        if number == 1 {
            break;
        }
    }
    
    println!("Stopped early");
}

// while as condition
fn while_condition() {
    let mut done = false;
    
    while !done {
        println!("Looping...");
        done = true;
    }
}
```

### for Loop
```rust
// Basic for loop with range
fn basic_for() {
    for number in (1..4).rev() {
        println!("{}!", number);
    }
    println!("LIFTOFF!!!");
}

// for loop with range
fn for_range() {
    for number in 1..4 {
        println!("{}!", number);
    }
}

// for loop with inclusive range
fn for_inclusive_range() {
    for number in 1..=4 {
        println!("{}!", number);
    }
}

// for loop with array
fn for_array() {
    let a = [10, 20, 30, 40, 50];
    
    for element in a.iter() {
        println!("the value is: {}", element);
    }
}

// for loop with mutable iteration
fn for_mutable() {
    let mut a = [10, 20, 30, 40, 50];
    
    for element in a.iter_mut() {
        *element *= 2;
    }
    
    println!("{:?}", a);
}

// for loop with enumeration
fn for_enumerate() {
    let a = [10, 20, 30, 40, 50];
    
    for (index, element) in a.iter().enumerate() {
        println!("Index {}: value = {}", index, element);
    }
}

// for loop with break and continue
fn for_break_continue() {
    for number in 1..10 {
        if number == 5 {
            continue;
        }
        
        println!("Number: {}", number);
        
        if number == 8 {
            break;
        }
    }
}

// for loop with labels
fn for_labels() {
    'outer: for x in 0..10 {
        'inner: for y in 0..10 {
            if x == 2 && y == 2 {
                break 'outer;
            }
            
            if x == 1 && y == 1 {
                continue 'inner;
            }
            
            println!("x: {}, y: {}", x, y);
        }
    }
}

// for loop with patterns
fn for_patterns() {
    let points = vec![(0, 0), (1, 2), (3, 4)];
    
    for &(x, y) in points.iter() {
        println!("Current location: ({}, {})", x, y);
    }
}

// for loop with Result
fn for_result() {
    let lines = vec!["line 1", "line 2", "", "line 4"];
    
    for line in lines.iter() {
        if line.is_empty() {
            continue;
        }
        
        println!("{}", line);
    }
}
```

## Iterator Patterns

### Iterator Methods
```rust
// Using iterator methods
fn iterator_methods() {
    let v = vec![1, 2, 3];
    
    // map
    let v2: Vec<i32> = v.iter().map(|x| x * 2).collect();
    println!("v2: {:?}", v2);
    
    // filter
    let v3: Vec<i32> = v.iter().filter(|&&x| x > 1).cloned().collect();
    println!("v3: {:?}", v3);
    
    // fold/reduce
    let sum = v.iter().fold(0, |acc, x| acc + x);
    println!("Sum: {}", sum);
    
    // any and all
    let has_even = v.iter().any(|&x| x % 2 == 0);
    let all_positive = v.iter().all(|&x| x > 0);
    println!("Has even: {}, All positive: {}", has_even, all_positive);
    
    // find
    let found = v.iter().find(|&&x| x > 2);
    println!("Found: {:?}", found);
    
    // position
    let position = v.iter().position(|&x| x == 2);
    println!("Position: {:?}", position);
}

// Custom iterator
struct Counter {
    current: usize,
    max: usize,
}

impl Counter {
    fn new(max: usize) -> Counter {
        Counter { current: 0, max }
    }
}

impl Iterator for Counter {
    type Item = usize;
    
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

fn custom_iterator() {
    let counter = Counter::new(5);
    
    for number in counter {
        println!("{}", number);
    }
}

// Iterator adapters
fn iterator_adapters() {
    let v1 = vec![1, 2, 3];
    let v2: Vec<i32> = v1.iter()
        .map(|x| x * 2)
        .filter(|&&x| x > 2)
        .collect();
    
    println!("v2: {:?}", v2);
    
    // Chain iterators
    let v3: Vec<i32> = v1.iter().chain(v2.iter()).cloned().collect();
    println!("v3: {:?}", v3);
    
    // Zip iterators
    let v4: Vec<(i32, i32)> = v1.iter().zip(v2.iter()).map(|(&a, &b)| (a, b)).collect();
    println!("v4: {:?}", v4);
    
    // Take and skip
    let v5: Vec<i32> = v1.iter().skip(1).take(2).cloned().collect();
    println!("v5: {:?}", v5);
}
```

## Error Handling in Control Flow

### Using Result in Control Flow
```rust
// Result with match
fn result_match() {
    let result = divide(10.0, 2.0);
    
    match result {
        Ok(value) => println!("Result: {}", value),
        Err(error) => println!("Error: {}", error),
    }
    
    let result = divide(10.0, 0.0);
    
    match result {
        Ok(value) => println!("Result: {}", value),
        Err(error) => println!("Error: {}", error),
    }
}

fn divide(a: f64, b: f64) -> Result<f64, String> {
    if b == 0.0 {
        Err("Cannot divide by zero".to_string())
    } else {
        Ok(a / b)
    }
}

// Result with if let
fn result_if_let() {
    let result = divide(10.0, 2.0);
    
    if let Ok(value) = result {
        println!("Result: {}", value);
    }
    
    let result = divide(10.0, 0.0);
    
    if let Err(error) = result {
        println!("Error: {}", error);
    }
}

// Result with unwrap_or
fn result_unwrap_or() {
    let result = divide(10.0, 0.0);
    let value = result.unwrap_or(0.0);
    println!("Value: {}", value);
}

// Result with unwrap_or_else
fn result_unwrap_or_else() {
    let result = divide(10.0, 0.0);
    let value = result.unwrap_or_else(|e| {
        println!("Error: {}", e);
        0.0
    });
    println!("Value: {}", value);
}

// Result with ? operator
fn result_question_mark() -> Result<f64, String> {
    let result1 = divide(10.0, 2.0)?;
    let result2 = divide(result1, 5.0)?;
    Ok(result2)
}

fn test_question_mark() {
    match result_question_mark() {
        Ok(value) => println!("Result: {}", value),
        Err(error) => println!("Error: {}", error),
    }
}
```

### Using Option in Control Flow
```rust
// Option with match
fn option_match() {
    let some_value = Some(5);
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

// Option with if let
fn option_if_let() {
    let some_value = Some(5);
    let none_value: Option<i32> = None;
    
    if let Some(value) = some_value {
        println!("Some: {}", value);
    }
    
    if let None = none_value {
        println!("None");
    }
}

// Option with unwrap_or
fn option_unwrap_or() {
    let some_value = Some(5);
    let none_value: Option<i32> = None;
    
    let value1 = some_value.unwrap_or(0);
    let value2 = none_value.unwrap_or(0);
    
    println!("Value1: {}, Value2: {}", value1, value2);
}

// Option with map
fn option_map() {
    let some_value = Some(5);
    let none_value: Option<i32> = None;
    
    let mapped1 = some_value.map(|x| x * 2);
    let mapped2 = none_value.map(|x| x * 2);
    
    println!("Mapped1: {:?}, Mapped2: {:?}", mapped1, mapped2);
}

// Option with and_then
fn option_and_then() {
    let some_value = Some(5);
    let none_value: Option<i32> = None;
    
    let result1 = some_value.and_then(|x| Some(x * 2));
    let result2 = none_value.and_then(|x| Some(x * 2));
    
    println!("Result1: {:?}, Result2: {:?}", result1, result2);
}
```

## Advanced Control Flow

### Guard Clauses
```rust
// Using guard clauses
fn guard_clauses() {
    let user = get_user(1);
    
    if user.is_none() {
        println!("User not found");
        return;
    }
    
    let user = user.unwrap();
    
    if !user.is_active {
        println!("User is not active");
        return;
    }
    
    println!("Processing user: {}", user.name);
}

fn get_user(id: u32) -> Option<User> {
    match id {
        1 => Some(User {
            id: 1,
            name: "Alice".to_string(),
            is_active: true,
        }),
        2 => Some(User {
            id: 2,
            name: "Bob".to_string(),
            is_active: false,
        }),
        _ => None,
    }
}

struct User {
    id: u32,
    name: String,
    is_active: bool,
}

// Early returns
fn early_returns() {
    let result = calculate(10, 0);
    
    match result {
        Ok(value) => println!("Result: {}", value),
        Err(error) => {
            println!("Error: {}", error);
            return;
        }
    }
    
    println!("Continuing with result");
}

fn calculate(a: i32, b: i32) -> Result<i32, String> {
    if b == 0 {
        return Err("Division by zero".to_string());
    }
    
    Ok(a / b)
}
```

### Conditional Compilation
```rust
// Conditional compilation
fn conditional_compilation() {
    #[cfg(target_os = "windows")]
    {
        println!("Running on Windows");
    }
    
    #[cfg(target_os = "linux")]
    {
        println!("Running on Linux");
    }
    
    #[cfg(not(target_os = "windows"))]
    {
        println!("Not running on Windows");
    }
    
    #[cfg(debug_assertions)]
    {
        println!("Debug mode");
    }
    
    #[cfg(not(debug_assertions))]
    {
        println!("Release mode");
    }
}

// Feature gates
#[cfg(feature = "advanced")]
fn advanced_feature() {
    println!("Advanced feature enabled");
}

#[cfg(not(feature = "advanced"))]
fn advanced_feature() {
    println!("Advanced feature disabled");
}

// Custom attributes
#[allow(dead_code)]
fn unused_function() {
    println!("This function is never used");
}

#[deprecated(since = "1.0.0", note = "Use new_function instead")]
fn old_function() {
    println!("This is deprecated");
}

#[test]
fn test_function() {
    assert_eq!(2 + 2, 4);
}
```

## Practical Examples

### Menu System
```rust
// Simple menu system
fn menu_system() {
    loop {
        println!("1. Add");
        println!("2. Subtract");
        println!("3. Multiply");
        println!("4. Divide");
        println!("5. Exit");
        
        let choice = get_input("Enter your choice: ");
        
        match choice.trim() {
            "1" => calculator_operation("add"),
            "2" => calculator_operation("subtract"),
            "3" => calculator_operation("multiply"),
            "4" => calculator_operation("divide"),
            "5" => break,
            _ => println!("Invalid choice"),
        }
    }
}

fn calculator_operation(operation: &str) {
    let a = get_input("Enter first number: ");
    let b = get_input("Enter second number: ");
    
    let a: f64 = match a.trim().parse() {
        Ok(num) => num,
        Err(_) => {
            println!("Invalid number");
            return;
        }
    };
    
    let b: f64 = match b.trim().parse() {
        Ok(num) => num,
        Err(_) => {
            println!("Invalid number");
            return;
        }
    };
    
    let result = match operation {
        "add" => a + b,
        "subtract" => a - b,
        "multiply" => a * b,
        "divide" => {
            if b == 0.0 {
                println!("Cannot divide by zero");
                return;
            }
            a / b
        },
        _ => return,
    };
    
    println!("Result: {}", result);
}

fn get_input(prompt: &str) -> String {
    println!("{}", prompt);
    let mut input = String::new();
    std::io::stdin().read_line(&mut input).unwrap();
    input
}
```

### Number Guessing Game
```rust
// Number guessing game
fn guessing_game() {
    let secret_number = rand::thread_rng().gen_range(1..=100);
    
    println!("Guess the number!");
    
    loop {
        println!("Please input your guess.");
        
        let mut guess = String::new();
        std::io::stdin().read_line(&mut guess).unwrap();
        
        let guess: u32 = match guess.trim().parse() {
            Ok(num) => num,
            Err(_) => {
                println!("Please type a number!");
                continue;
            }
        };
        
        match guess.cmp(&secret_number) {
            std::cmp::Ordering::Less => println!("Too small!"),
            std::cmp::Ordering::Greater => println!("Too big!"),
            std::cmp::Ordering::Equal => {
                println!("You win!");
                break;
            }
        }
    }
}

// Using external crate for random numbers
use rand::Rng;
```

### FizzBuzz
```rust
// FizzBuzz implementation
fn fizzbuzz() {
    for number in 1..=100 {
        if number % 3 == 0 && number % 5 == 0 {
            println!("FizzBuzz");
        } else if number % 3 == 0 {
            println!("Fizz");
        } else if number % 5 == 0 {
            println!("Buzz");
        } else {
            println!("{}", number);
        }
    }
}

// FizzBuzz with match
fn fizzbuzz_match() {
    for number in 1..=100 {
        let output = match (number % 3, number % 5) {
            (0, 0) => "FizzBuzz",
            (0, _) => "Fizz",
            (_, 0) => "Buzz",
            _ => &number.to_string(),
        };
        
        println!("{}", output);
    }
}

// FizzBuzz with if-else
fn fizzbuzz_if_else() {
    for number in 1..=100 {
        let mut output = String::new();
        
        if number % 3 == 0 {
            output.push_str("Fizz");
        }
        
        if number % 5 == 0 {
            output.push_str("Buzz");
        }
        
        if output.is_empty() {
            output = number.to_string();
        }
        
        println!("{}", output);
    }
}
```

## Best Practices

### Control Flow Best Practices
```rust
// 1. Prefer match over long if-else chains
fn prefer_match() {
    let value = 5;
    
    // Good: use match
    let result = match value {
        1 => "one",
        2 => "two",
        3 => "three",
        _ => "other",
    };
    
    // Avoid: long if-else chain
    let result = if value == 1 {
        "one"
    } else if value == 2 {
        "two"
    } else if value == 3 {
        "three"
    } else {
        "other"
    };
}

// 2. Use guard clauses to reduce nesting
fn guard_clauses() {
    // Good: guard clauses
    fn process_user(user: Option<User>) -> Result<String, String> {
        let user = match user {
            Some(user) => user,
            None => return Err("User not found".to_string()),
        };
        
        if !user.is_active {
            return Err("User not active".to_string());
        }
        
        Ok(format!("Processing {}", user.name))
    }
    
    // Avoid: deep nesting
    fn process_user_nested(user: Option<User>) -> Result<String, String> {
        if let Some(user) = user {
            if user.is_active {
                Ok(format!("Processing {}", user.name))
            } else {
                Err("User not active".to_string())
            }
        } else {
            Err("User not found".to_string())
        }
    }
}

// 3. Use meaningful variable names in loops
fn meaningful_loop_names() {
    // Good: meaningful names
    for user in users.iter() {
        println!("User: {}", user.name);
    }
    
    // Avoid: generic names
    for item in items.iter() {
        println!("Item: {}", item.name);
    }
}

// 4. Use appropriate loop types
fn appropriate_loops() {
    let numbers = vec![1, 2, 3, 4, 5];
    
    // Good: use for loop for known iteration count
    for number in numbers.iter() {
        println!("{}", number);
    }
    
    // Good: use while loop for unknown iteration count
    let mut counter = 0;
    while counter < 10 {
        println!("{}", counter);
        counter += 1;
    }
    
    // Good: use loop for infinite loops or when you need to break early
    loop {
        println!("Infinite loop");
        break;
    }
}

// 5. Handle all cases in match
fn exhaustive_match() {
    let value = Some(5);
    
    // Good: handle all cases
    match value {
        Some(value) => println!("Some: {}", value),
        None => println!("None"),
    }
    
    // Good: use _ for catch-all
    let number = 5;
    match number {
        1 => println!("One"),
        2 => println!("Two"),
        _ => println!("Other"),
    }
}

// 6. Use early returns for error handling
fn early_returns() {
    fn validate_user(user: &User) -> Result<(), String> {
        if user.name.is_empty() {
            return Err("Name cannot be empty".to_string());
        }
        
        if user.age < 18 {
            return Err("User must be 18 or older".to_string());
        }
        
        Ok(())
    }
}

// 7. Use iterators instead of manual loops when possible
fn use_iterators() {
    let numbers = vec![1, 2, 3, 4, 5];
    
    // Good: use iterator
    let sum: i32 = numbers.iter().sum();
    
    // Avoid: manual loop
    let mut sum = 0;
    for number in numbers.iter() {
        sum += number;
    }
}

// 8. Use appropriate error handling
fn error_handling() {
    // Good: use Result
    fn divide(a: f64, b: f64) -> Result<f64, String> {
        if b == 0.0 {
            Err("Cannot divide by zero".to_string())
        } else {
            Ok(a / b)
        }
    }
    
    // Avoid: panic in production code
    fn divide_panic(a: f64, b: f64) -> f64 {
        if b == 0.0 {
            panic!("Cannot divide by zero");
        }
        a / b
    }
}
```

### Performance Considerations
```rust
// 1. Use break instead of continue when possible
fn break_vs_continue() {
    // Good: break early when condition is met
    for item in items.iter() {
        if item.is_important() {
            process_item(item);
            break;
        }
    }
    
    // Avoid: unnecessary iterations
    for item in items.iter() {
        if item.is_important() {
            process_item(item);
            continue;
        }
        // ... other processing
    }
}

// 2. Use appropriate loop constructs
fn efficient_loops() {
    // Good: use for loop for iteration
    for item in items.iter() {
        process_item(item);
    }
    
    // Good: use while loop for condition-based loops
    while has_items() {
        process_next_item();
    }
    
    // Good: use loop for infinite loops with break
    loop {
        let item = get_next_item();
        if item.is_none() {
            break;
        }
        process_item(item.unwrap());
    }
}

// 3. Use iterators for functional programming
fn functional_programming() {
    // Good: use iterator methods
    let result: Vec<i32> = numbers.iter()
        .filter(|&&x| x > 0)
        .map(|x| x * 2)
        .collect();
    
    // Avoid: manual loops
    let mut result = Vec::new();
    for &x in numbers.iter() {
        if x > 0 {
            result.push(x * 2);
        }
    }
}

// 4. Use match for pattern matching
fn pattern_matching() {
    // Good: use match for complex patterns
    let result = match value {
        Some(x) if x > 0 => x * 2,
        Some(x) => x,
        None => 0,
    };
    
    // Avoid: nested if-else
    let result = if let Some(x) = value {
        if x > 0 {
            x * 2
        } else {
            x
        }
    } else {
        0
    };
}
```

## Common Pitfalls

### Common Control Flow Mistakes
```rust
// 1. Forgetting break in loops
fn forgetting_break() {
    // Bad: infinite loop
    loop {
        println!("Infinite loop");
        // Missing break
    }
    
    // Good: include break
    loop {
        println!("Loop with break");
        break;
    }
}

// 2. Using = instead of == in conditions
fn assignment_vs_comparison() {
    // Bad: assignment instead of comparison
    let x = 5;
    if x = 3 { // Error: expected expression, found statement
        println!("This won't compile");
    }
    
    // Good: comparison
    if x == 3 {
        println!("This is correct");
    }
}

// 3. Not handling all match cases
fn incomplete_match() {
    let value = Some(5);
    
    // Bad: missing None case
    match value {
        Some(x) => println!("Some: {}", x),
        // Missing None case - compiler will warn
    }
    
    // Good: handle all cases
    match value {
        Some(x) => println!("Some: {}", x),
        None => println!("None"),
    }
}

// 4. Using unwrap() without checking
fn unsafe_unwrap() {
    // Bad: can panic
    let value = Some(5).unwrap();
    
    // Good: handle None case
    let value = Some(5).unwrap_or(0);
    
    // Better: use match or if let
    let value = match Some(5) {
        Some(x) => x,
        None => 0,
    };
}

// 5. Infinite loops without exit condition
fn infinite_loops() {
    // Bad: no way to exit
    loop {
        println!("Stuck forever");
    }
    
    // Good: include break condition
    loop {
        println!("Can exit");
        break;
    }
}

// 6. Off-by-one errors in loops
fn off_by_one() {
    // Bad: off-by-one error
    for i in 0..=10 { // Includes 10, might be unintended
        println!("{}", i);
    }
    
    // Good: be explicit about range
    for i in 0..10 { // Excludes 10
        println!("{}", i);
    }
    
    // Good: inclusive range when intended
    for i in 0..=10 { // Explicitly inclusive
        println!("{}", i);
    }
}

// 7. Mutable state in loops
fn mutable_in_loops() {
    // Bad: modifying collection while iterating
    let mut vec = vec![1, 2, 3, 4, 5];
    for item in vec.iter() {
        vec.push(*item); // Error: cannot borrow vec as mutable
    }
    
    // Good: collect results first
    let mut vec = vec![1, 2, 3, 4, 5];
    let mut new_items = Vec::new();
    for item in vec.iter() {
        new_items.push(*item);
    }
    vec.extend(new_items);
}

// 8. Not using early returns
fn nested_conditions() {
    // Bad: deep nesting
    fn process(value: Option<i32>) {
        if let Some(val) = value {
            if val > 0 {
                if val < 100 {
                    println!("Valid: {}", val);
                }
            }
        }
    }
    
    // Good: early returns
    fn process_better(value: Option<i32>) {
        let val = match value {
            Some(val) => val,
            None => return,
        };
        
        if val <= 0 {
            return;
        }
        
        if val >= 100 {
            return;
        }
        
        println!("Valid: {}", val);
    }
}

// 9. Using panic! in production code
fn panic_in_production() {
    // Bad: panic in production
    fn divide(a: f64, b: f64) -> f64 {
        if b == 0.0 {
            panic!("Cannot divide by zero");
        }
        a / b
    }
    
    // Good: return Result
    fn divide_safe(a: f64, b: f64) -> Result<f64, String> {
        if b == 0.0 {
            Err("Cannot divide by zero".to_string())
        } else {
            Ok(a / b)
        }
    }
}

// 10. Not using iterators when appropriate
fn manual_loops() {
    // Bad: manual loop
    let numbers = vec![1, 2, 3, 4, 5];
    let mut sum = 0;
    for number in numbers.iter() {
        sum += number;
    }
    
    // Good: use iterator
    let sum: i32 = numbers.iter().sum();
}
```

## Summary

Rust control flow provides comprehensive and safe programming constructs:

**Conditional Statements:**
- `if` expressions with values
- `match` expressions for pattern matching
- Guard clauses for early returns
- Exhaustive pattern matching

**Loops:**
- `loop` for infinite loops with break values
- `while` for condition-based loops
- `for` loops with ranges and iterators
- Labeled loops for nested control

**Iterator Patterns:**
- Rich iterator methods (map, filter, fold)
- Custom iterator implementations
- Iterator adapters and chains
- Functional programming patterns

**Error Handling:**
- `Result` type for recoverable errors
- `Option` type for optional values
- `?` operator for error propagation
- Safe error handling patterns

**Advanced Features:**
- Conditional compilation
- Feature gates
- Custom attributes
- Pattern matching with bindings

**Best Practices:**
- Prefer `match` over long `if-else` chains
- Use guard clauses to reduce nesting
- Handle all cases in `match`
- Use appropriate loop types
- Use iterators when possible
- Handle errors gracefully

**Common Pitfalls:**
- Forgetting break conditions
- Assignment vs comparison errors
- Incomplete pattern matching
- Unsafe unwrap usage
- Off-by-one errors
- Mutable state in loops

Rust's control flow constructs, combined with its type system and ownership model, provide safe, expressive, and efficient ways to control program execution while preventing common programming errors at compile time.
