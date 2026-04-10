# Rust Ownership System

## Ownership Fundamentals

### The Three Rules of Ownership
```rust
// Rule 1: Each value in Rust has a variable that's its owner
fn rule_one() {
    let s1 = String::from("hello"); // s1 owns the string
    let s2 = s1; // ownership moves to s2, s1 is no longer valid
    
    // println!("{}", s1); // Error: value borrowed after move
    println!("{}", s2); // This works
}

// Rule 2: There can only be one owner at a time
fn rule_two() {
    let s1 = String::from("hello");
    let s2 = s1; // s1 is moved to s2
    let s3 = s2; // s2 is moved to s3
    
    // s1 and s2 are no longer valid, only s3 is valid
    println!("{}", s3);
}

// Rule 3: When the owner goes out of scope, the value is dropped
fn rule_three() {
    {
        let s = String::from("hello"); // s owns the string
        println!("{}", s);
    } // s is dropped here, memory is freed
    // println!("{}", s); // Error: s no longer exists
}

// Ownership in action
fn ownership_in_action() {
    let s1 = String::from("hello");
    let s2 = s1.clone(); // Clone creates a new owner
    
    println!("s1: {}, s2: {}", s1, s2); // Both are valid
    
    let s3 = s1; // s1 is moved to s3
    println!("s2: {}, s3: {}", s2, s3); // s2 and s3 are valid
    // println!("s1: {}", s1); // Error: s1 was moved
}
```

### Move Semantics
```rust
// Moving values
fn moving_values() {
    let x = 5; // x owns the value 5
    let y = x; // x is moved to y (i32 implements Copy)
    
    println!("x: {}, y: {}", x, y); // Both are valid because i32 is Copy
    
    let s1 = String::from("hello");
    let s2 = s1; // s1 is moved to s2 (String doesn't implement Copy)
    
    // println!("s1: {}", s1); // Error: s1 was moved
    println!("s2: {}", s2); // s2 is valid
}

// Moving in function calls
fn moving_in_functions() {
    let s = String::from("hello");
    takes_ownership(s); // s is moved to the function
    // println!("{}", s); // Error: s was moved
    
    let s = String::from("hello");
    let s = gives_ownership(s); // s is moved and returned
    println!("{}", s); // s is valid again
}

fn takes_ownership(some_string: String) {
    println!("Got ownership of: {}", some_string);
}

fn gives_ownership(some_string: String) -> String {
    println!("Got ownership of: {}", some_string);
    some_string // Return ownership to caller
}

// Moving with references
fn moving_with_references() {
    let s1 = String::from("hello");
    
    let len = calculate_length(&s1); // Pass reference, s1 remains valid
    println!("Length: {}", len);
    println!("s1: {}", s1); // s1 is still valid
}

fn calculate_length(s: &String) -> usize {
    s.len()
}
```

### Copy Trait
```rust
// Copy vs Move
fn copy_vs_move() {
    // Types that implement Copy
    let x = 5;
    let y = x; // x is copied to y
    println!("x: {}, y: {}", x, y); // Both are valid
    
    // Types that don't implement Copy
    let s1 = String::from("hello");
    let s2 = s1; // s1 is moved to s2
    // println!("s1: {}", s1); // Error: s1 was moved
    println!("s2: {}", s2); // s2 is valid
}

// Implementing Copy trait
#[derive(Debug, Clone, Copy)]
struct Point {
    x: i32,
    y: i32,
}

#[derive(Debug, Clone)]
struct Rectangle {
    width: i32,
    height: i32,
}

fn copy_trait_examples() {
    let p1 = Point { x: 1, y: 2 };
    let p2 = p1; // p1 is copied to p2
    println!("p1: {:?}, p2: {:?}", p1, p2); // Both are valid
    
    let r1 = Rectangle { width: 10, height: 20 };
    let r2 = r1.clone(); // r1 is cloned to r2
    println!("r1: {:?}, r2: {:?}", r1, r2); // Both are valid
    
    // r2 = r1; // This would move r1 to r2
    // println!("r1: {:?}", r1); // Error: r1 was moved
}

// Manual Copy implementation
#[derive(Debug, Clone)]
struct ManualCopy {
    value: i32,
}

impl Copy for ManualCopy {}

fn manual_copy_implementation() {
    let mc1 = ManualCopy { value: 42 };
    let mc2 = mc1; // mc1 is copied to mc2
    println!("mc1: {:?}, mc2: {:?}", mc1, mc2); // Both are valid
}
```

## Borrowing

### References
```rust
// Immutable references
fn immutable_references() {
    let s1 = String::from("hello");
    
    let len = calculate_length(&s1); // Pass immutable reference
    println!("Length: {}", len);
    println!("s1: {}", s1); // s1 is still valid
    
    // Multiple references
    let r1 = &s1;
    let r2 = &s1;
    let r3 = &s1;
    
    println!("r1: {}, r2: {}, r3: {}", r1, r2, r3);
}

// Mutable references
fn mutable_references() {
    let mut s = String::from("hello");
    
    change_string(&mut s); // Pass mutable reference
    println!("Changed: {}", s);
    
    // Only one mutable reference at a time
    let r1 = &mut s;
    // let r2 = &mut s; // Error: cannot borrow s mutably more than once at a time
    println!("Mutable reference: {}", r1);
}

fn change_string(s: &mut String) {
    s.push_str(", world");
}

// Reference rules
fn reference_rules() {
    let mut s = String::from("hello");
    
    // Multiple immutable references are allowed
    let r1 = &s;
    let r2 = &s;
    let r3 = &s;
    println!("r1: {}, r2: {}, r3: {}", r1, r2, r3);
    
    // Cannot have mutable reference while immutable references exist
    drop(r1);
    drop(r2);
    drop(r3);
    
    let r4 = &mut s; // Now mutable reference is allowed
    println!("Mutable reference: {}", r4);
    
    // Cannot have any other references while mutable reference exists
    drop(r4);
    
    // Now we can have immutable references again
    let r5 = &s;
    println!("Immutable reference: {}", r5);
}

fn drop<T>(_value: T) {
    // Explicit drop to end lifetime
}
```

### Slices
```rust
// String slices
fn string_slices() {
    let s = String::from("hello world");
    
    let hello = &s[0..5]; // Slice of first 5 characters
    let world = &s[6..11]; // Slice of last 5 characters
    let all = &s[..]; // Slice of entire string
    
    println!("Hello: {}", hello);
    println!("World: {}", world);
    println!("All: {}", all);
    
    // Slices don't own the data
    let string_literal = "hello world";
    let slice = &string_literal[0..5];
    println!("Slice: {}", slice);
}

// Array slices
fn array_slices() {
    let a = [1, 2, 3, 4, 5];
    
    let first_three = &a[0..3]; // Elements 0, 1, 2
    let last_three = &a[2..5]; // Elements 2, 3, 4
    let middle = &a[1..4]; // Elements 1, 2, 3
    
    println!("First three: {:?}", first_three);
    println!("Last three: {:?}", last_three);
    println!("Middle: {:?}", middle);
}

// Vector slices
fn vector_slices() {
    let v = vec![1, 2, 3, 4, 5];
    
    let slice = &v[1..4]; // Elements 1, 2, 3
    println!("Slice: {:?}", slice);
    
    // Slices can be passed to functions
    print_slice(&v[1..4]);
}

fn print_slice(slice: &[i32]) {
    println!("Slice: {:?}", slice);
}
```

### Dangling References
```rust
// Dangling references
fn dangling_references() {
    // Bad: returns reference to local variable
    // fn bad_reference() -> &String {
    //     let s = String::from("hello");
    //     &s // Error: s will be dropped when function returns
    // }
    
    // Good: return owned value
    fn good_reference() -> String {
        let s = String::from("hello");
        s // s is moved to caller
    }
    
    let owned = good_reference();
    println!("Owned: {}", owned);
}

// Lifetime annotations
fn lifetime_annotations() {
    // Explicit lifetime annotation
    fn longest<'a>(x: &'a str, y: &'a str) -> &'a str {
        if x.len() > y.len() {
            x
        } else {
            y
        }
    }
    
    let string1 = String::from("hello");
    let string2 = "world";
    
    let result = longest(&string1, string2);
    println!("Longest: {}", result);
}

// Lifetime in structs
fn lifetime_in_structs() {
    #[derive(Debug)]
    struct ImportantExcerpt<'a> {
        part: &'a str,
    }
    
    impl<'a> ImportantExcerpt<'a> {
        fn announce_and_return_self(&self) -> &'a str {
            println!("Announcing: {}", self.part);
            self.part
        }
    }
    
    let novel = String::from("Call me Ishmael. Some years ago...");
    let first_sentence = novel.split('.').next().unwrap_or("");
    
    let i = ImportantExcerpt {
        part: first_sentence,
    };
    
    println!("Important excerpt: {:?}", i);
    
    let announcement = i.announce_and_return_self();
    println!("Announcement: {}", announcement);
}
```

## Smart Pointers

### Box
```rust
// Using Box for heap allocation
fn using_box() {
    // Allocate on heap instead of stack
    let b = Box::new(5);
    println!("Box contains: {}", b);
    
    // Box can store large data
    let large_data = vec![1, 2, 3, 4, 5];
    let boxed_data = Box::new(large_data);
    println!("Boxed data length: {}", boxed_data.len());
    
    // Box for recursive types
    #[derive(Debug)]
    enum List {
        Cons(i32, Box<List>),
        Nil,
    }
    
    let list = List::Cons(1, Box::new(List::Cons(2, Box::new(List::Cons(3, Box::new(List::Nil))))));
    println!("List: {:?}", list);
    
    // Box enables recursive types
    impl List {
        fn new() -> List {
            List::Nil
        }
        
        fn cons(value: i32, list: List) -> List {
            List::Cons(value, Box::new(list))
        }
    }
    
    let recursive_list = List::new()
        .cons(1)
        .cons(2)
        .cons(3);
    
    println!("Recursive list: {:?}", recursive_list);
}

// Box with trait objects
fn box_trait_objects() {
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
    
    let shapes: Vec<Box<dyn Draw>> = vec![
        Box::new(Circle { radius: 5.0 }),
        Box::new(Rectangle { width: 10.0, height: 20.0 }),
    ];
    
    for shape in shapes {
        shape.draw();
    }
}
```

### Rc
```rust
use std::rc::Rc;

// Using Rc for reference counting
fn using_rc() {
    // Multiple owners of same data
    let s = Rc::new(String::from("hello"));
    let s2 = Rc::clone(&s);
    let s3 = Rc::clone(&s);
    
    println!("s: {}, s2: {}, s3: {}", s, s2, s3);
    println!("Reference count: {}", Rc::strong_count(&s));
    
    // Rc::weak for avoiding cycles
    use std::rc::Weak;
    
    #[derive(Debug)]
    struct Node {
        value: i32,
        next: Option<Rc<Node>>,
        prev: Option<Weak<Node>>,
    }
    
    impl Node {
        fn new(value: i32) -> Self {
            Node {
                value,
                next: None,
                prev: None,
            }
        }
        
        fn set_next(&mut self, node: Rc<Node>) {
            self.next = Some(Rc::clone(&node));
            node.prev = Some(Rc::downgrade(&self));
        }
    }
    
    let node1 = Rc::new(Node::new(1));
    let node2 = Rc::new(Node::new(2));
    let node3 = Rc::new(Node::new(3));
    
    node1.set_next(node2.clone());
    node2.set_next(node3.clone());
    
    println!("Node1: {:?}", node1);
    println!("Node2: {:?}", node2);
    println!("Node3: {:?}", node3);
    
    // Check reference counts
    println!("Node1 count: {}", Rc::strong_count(&node1));
    println!("Node2 count: {}", Rc::strong_count(&node2));
    println!("Node3 count: {}", Rc::strong_count(&node3));
}

// Rc with cyclic references
fn cyclic_references() {
    use std::cell::RefCell;
    
    #[derive(Debug)]
    struct Cycle {
        value: RefCell<i32>,
        next: RefCell<Option<Rc<Cycle>>>,
    }
    
    impl Cycle {
        fn new(value: i32) -> Self {
            Cycle {
                value: RefCell::new(value),
                next: RefCell::new(None),
            }
        }
        
        fn set_next(&self, cycle: Rc<Cycle>) {
            *self.next.borrow_mut() = Some(cycle);
        }
    }
    
    let cycle1 = Rc::new(Cycle::new(1));
    let cycle2 = Rc::new(Cycle::new(2));
    
    cycle1.set_next(cycle2.clone());
    cycle2.set_next(cycle1.clone()); // Creates a cycle
    
    println!("Cycle1: {:?}", cycle1);
    println!("Cycle2: {:?}", cycle2);
    
    // Reference count never reaches 0 due to cycle
    println!("Cycle1 count: {}", Rc::strong_count(&cycle1));
    println!("Cycle2 count: {}", Rc::strong_count(&cycle2));
}
```

### Arc
```rust
use std::sync::Arc;
use std::thread;

// Using Arc for thread-safe sharing
fn using_arc() {
    // Arc is like Rc but thread-safe
    let data = Arc::new(vec![1, 2, 3, 4, 5]);
    
    let mut handles = vec![];
    
    for i in 0..5 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            println!("Thread {} sees: {:?}", i, data_clone);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Original data: {:?}", data);
}

// Arc with Mutex for mutable shared state
fn arc_with_mutex() {
    use std::sync::Mutex;
    
    let counter = Arc::new(Mutex::new(0));
    let mut handles = vec![];
    
    for i in 0..10 {
        let counter_clone = Arc::clone(&counter);
        let handle = thread::spawn(move || {
            for _ in 0..1000 {
                let mut num = counter_clone.lock().unwrap();
                *num += 1;
            }
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Final counter: {}", *counter.lock().unwrap());
}

// Arc with RwLock for multiple readers
fn arc_with_rwlock() {
    use std::sync::RwLock;
    
    let data = Arc::new(RwLock::new(vec![
        "message 1".to_string(),
        "message 2".to_string(),
        "message 3".to_string(),
    ]);
    
    let mut handles = vec![];
    
    // Spawn readers
    for i in 0..3 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            let data = data_clone.read().unwrap();
            println!("Reader {} sees: {:?}", i, data);
        });
        handles.push(handle);
    }
    
    // Spawn writer
    let data_clone = Arc::clone(&data);
    let writer_handle = thread::spawn(move || {
        let mut data = data_clone.write().unwrap();
        data.push("message 4".to_string());
        println!("Writer added message 4");
    });
    
    writer_handle.join().unwrap();
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    let final_data = data.read().unwrap();
    println!("Final data: {:?}", final_data);
}
```

## Lifetime Elision

### Lifetime Elision Rules
```rust
// Lifetime elision in functions
fn lifetime_elision_rules() {
    // Rule 1: Each input parameter that is a reference gets its own lifetime parameter
    fn rule1<'a>(x: &'a str) -> &'a str {
        x
    }
    
    // Rule 2: If there is exactly one input lifetime parameter, that lifetime is assigned to all output lifetime parameters
    fn rule2<'a>(x: &'a str) -> &'a str {
        x
    }
    
    // Rule 3: If there are multiple input lifetime parameters, but one of them is &self or &mut self, the lifetime of self is assigned to all output lifetime parameters
    struct Example<'a> {
        data: &'a str,
    }
    
    impl<'a> Example<'a> {
        fn rule3(&self) -> &'a str {
            self.data
        }
    }
    
    // Explicit lifetime annotation when elision doesn't work
    fn explicit_lifetime<'a, 'b>(x: &'a str, y: &'b str) -> &'a str {
        if x.len() > y.len() {
            x
        } else {
            y
        }
    }
}

// Lifetime elision in methods
fn lifetime_elision_in_methods() {
    #[derive(Debug)]
    struct Holder<'a> {
        data: &'a str,
    }
    
    impl<'a> Holder<'a> {
        // Lifetime elision: &self -> &self.data is inferred as &'a str
        fn get_data(&self) -> &str {
            self.data
        }
        
        // Lifetime elision: &mut self -> &mut self.data is inferred as &'a str
        fn get_data_mut(&mut self) -> &str {
            self.data
        }
        
        // No elision: need explicit lifetime
        fn compare_data<'b>(&self, other: &'b str) -> bool {
            self.data.len() > other.len()
        }
    }
    
    let holder = Holder {
        data: "hello world",
    };
    
    println!("Data: {}", holder.get_data());
    
    let mut holder = Holder {
        data: "mutable",
    };
    holder.set_data("changed");
    println!("Mutable data: {}", holder.get_data_mut());
    
    let other = "comparison string";
    println!("Is longer: {}", holder.compare_data(other));
}

// Lifetime elision in structs
fn lifetime_elision_in_structs() {
    // No lifetime annotation needed when all references have the same lifetime
    #[derive(Debug)]
    struct RefStruct<'a> {
        data: &'a str,
    }
    
    impl<'a> RefStruct<'a> {
        fn new(data: &'a str) -> Self {
            RefStruct { data }
        }
        
        fn get_data(&self) -> &str {
            self.data
        }
    }
    
    let string = String::from("hello");
    let ref_struct = RefStruct::new(&string);
    println!("Data: {}", ref_struct.get_data());
}

// Lifetime bounds in generics
fn lifetime_bounds() {
    // Generic function with lifetime bounds
    fn print_ref<'a>(s: &'a str) {
        println!("Reference: {}", s);
    }
    
    // Generic struct with lifetime parameter
    #[derive(Debug)]
    struct RefWrapper<'a> {
        data: &'a str,
    }
    
    impl<'a> RefWrapper<'a> {
        fn new(data: &'a str) -> Self {
            RefWrapper { data }
        }
        
        fn print(&self) {
            println!("Wrapped data: {}", self.data);
        }
    }
    
    let string = String::from("hello");
    let wrapper = RefWrapper::new(&string);
    wrapper.print();
}
```

### Static Lifetime
```str
// Static lifetime
fn static_lifetime() {
    // Static string slice
    static HELLO_WORLD: &str = "Hello, world!";
    static COUNTER: std::sync::atomic::AtomicUsize = std::sync::atomic::AtomicUsize::new(0);
    
    println!("Static string: {}", HELLO_WORLD);
    
    // Static variables
    COUNTER.fetch_add(1, std::sync::atomic::Ordering::SeqCst);
    println!("Counter: {}", COUNTER.load(std::sync::atomic::Ordering::SeqCst));
    
    // Static functions
    fn static_function() -> &'static str {
        "This is a static string"
    }
    
    println!("Static function: {}", static_function());
}

// Static variables in structs
fn static_in_structs() {
    #[derive(Debug)]
    struct Config {
        max_connections: u32,
        timeout: u64,
    }
    
    impl Config {
        // Static method
        fn default() -> Self {
            Config {
                max_connections: 100,
                timeout: 30000,
            }
        }
    }
    
    let config = Config::default();
    println!("Default config: {:?}", config);
}

// Static references
fn static_references() {
    // Static references to constants
    static STRING: &str = "This is a static string";
    
    // Static references to functions
    fn get_static_reference() -> &'static str {
        "Static reference"
    }
    
    println!("Static string: {}", STRING);
    println!("Static reference: {}", get_static_reference());
    
    // Static references to const functions
    const CONST_STRING: &str = "Const string";
    
    fn get_const_reference() -> &'static str {
        CONST_STRING
    }
    
    println!("Const string: {}", get_const_reference());
}

// 'static lifetime in function signatures
fn static_lifetime_in_functions() {
    // Function returning static reference
    fn get_static_string() -> &'static str {
        "This lives forever"
    }
    
    // Function taking static reference
    fn print_static(s: &'static str) {
        println!("Static: {}", s);
    }
    
    let static_ref = get_static_string();
    print_static(static_ref);
}
```

## Best Practices

### Ownership Best Practices
```rust
// 1. Use references when you don't need ownership
fn use_references() {
    let s = String::from("hello");
    
    // Good: use reference
    let len = calculate_length(&s);
    println!("Length: {}", len);
    
    // Bad: unnecessary move
    let owned = s.clone();
    let len = calculate_length(owned);
    println!("Length: {}", len);
}

// 2. Use Rc for shared ownership
fn use_rc_for_sharing() {
    use std::rc::Rc;
    
    let s = Rc::new(String::from("shared"));
    let s2 = Rc::clone(&s);
    
    println!("Shared: {}, {}", s, s2);
    println!("Reference count: {}", Rc::strong_count(&s));
}

// 3. Use Arc for thread-safe sharing
fn use_arc_for_thread_safe() {
    use std::sync::Arc;
    use std::thread;
    
    let data = Arc::new(vec![1, 2, 3]);
    
    let data_clone = Arc::clone(&data);
    let handle = thread::spawn(move || {
        println!("Thread data: {:?}", data_clone);
    });
    
    handle.join().unwrap();
}

// 4. Use Box for large data or recursive types
fn use_box_for_large_data() {
    // Large data on heap
    let large_vec = vec![0; 1000000];
    let boxed_vec = Box::new(large_vec);
    
    println!("Boxed vector length: {}", boxed_vec.len());
    
    // Recursive type
    #[derive(Debug)]
    enum List {
        Cons(i32, Box<List>),
        Nil,
    }
    
    impl List {
        fn new() -> Self {
            List::Nil
        }
        
        fn cons(value: i32, list: List) -> Self {
            List::Cons(value, Box::new(list))
        }
    }
    
    let list = List::new()
        .cons(1)
        .cons(2)
        .cons(3);
    
    println!("List: {:?}", list);
}

// 5. Avoid unnecessary cloning
fn avoid_unnecessary_cloning() {
    // Bad: unnecessary clone
    fn process_string(s: String) -> usize {
        s.len()
    }
    
    let s = String::from("hello");
    let length = process_string(s.clone()); // Unnecessary clone
    
    // Good: use reference
    fn process_string_better(s: &str) -> usize {
        s.len()
    }
    
    let s = String::from("hello");
    let length = process_string_better(&s); // No clone needed
    
    println!("Length: {}", length);
}

// 6. Use appropriate smart pointers
fn appropriate_smart_pointers() {
    // Use Box for single ownership
    let unique = Box::new(5);
    
    // Use Rc for shared ownership in single-threaded code
    use std::rc::Rc;
    let shared = Rc::new(String::from("shared"));
    
    // Use Arc for shared ownership in multi-threaded code
    use std::sync::Arc;
    let thread_safe = Arc::new(String::from("thread safe"));
    
    // Use Weak to break reference cycles
    use std::rc::Weak;
    let weak_ref = Rc::downgrade(&shared);
}

// 7. Use lifetime annotations when necessary
fn use_lifetime_annotations() {
    // Use explicit annotations when elision doesn't work
    fn compare_strings<'a, 'b>(s1: &'a str, s2: &'b str) -> &'a str {
        if s1.len() > s2.len() {
            s1
        } else {
            s2
        }
    }
    
    let s1 = String::from("hello");
    let s2 = String::from("world");
    let longer = compare_strings(&s1, &s2);
    println!("Longer: {}", longer);
}

// 8. Use static for global constants
fn use_static_for_constants() {
    // Good: static for global constants
    static MAX_CONNECTIONS: u32 = 100;
    static DEFAULT_TIMEOUT: u64 = 30000;
    
    println!("Max connections: {}", MAX_CONNECTIONS);
    println!("Default timeout: {}", DEFAULT_TIMEOUT);
    
    // Use const for compile-time constants
    const PI: f64 = 3.141592653589793;
    
    println!("PI: {}", PI);
}
```

### Borrowing Best Practices
```rust
// 1. Use immutable references when possible
fn immutable_references() {
    let data = vec![1, 2, 3, 4, 5];
    
    // Good: immutable reference
    let sum = calculate_sum(&data);
    println!("Sum: {}", sum);
    
    // Only use mutable references when modification is needed
    let mut data = vec![1, 2, 3, 4, 5];
    modify_data(&mut data);
    println!("Modified: {:?}", data);
}

fn calculate_sum(data: &[i32]) -> i32 {
    data.iter().sum()
}

fn modify_data(data: &mut Vec<i32>) {
    for item in data.iter_mut() {
        *item *= 2;
    }
}

// 2. Limit scope of mutable references
fn limit_mutable_scope() {
    let mut data = vec![1, 2, 3, 4, 5];
    
    {
        let slice = &mut data[1..4];
        for item in slice.iter_mut() {
            *item *= 3;
        }
        // slice is dropped here
    }
    
    println!("Modified slice: {:?}", data);
}

// 3. Avoid data races in multi-threaded code
fn avoid_data_races() {
    use std::sync::{Arc, Mutex};
    use std::thread;
    
    let data = Arc::new(Mutex::new(vec![1, 2, 3, 4, 5]));
    let mut handles = vec![];
    
    for i in 0..5 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            let mut data = data_clone.lock().unwrap();
            data[i] *= 2;
            println!("Thread {} modified data", i);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Final data: {:?}", *data.lock().unwrap());
}

// 4. Use slices instead of full references when possible
fn use_slices() {
    let data = vec![1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    
    // Good: use slice
    let slice = &data[2..8];
    let sum: i32 = slice.iter().sum();
    
    println!("Slice sum: {}", sum);
    
    // Avoid: taking full reference when slice would work
    let full_reference = &data;
    let full_sum: i32 = full_reference.iter().sum();
    println!("Full sum: {}", full_sum);
}

// 5. Use Cow for write-on-read optimization
fn use_cow() {
    use std::borrow::Cow;
    
    let data = vec![1, 2, 3, 4, 5];
    
    // Cow can be either owned or borrowed
    let cow_data: Cow<[i32]> = Cow::Borrowed(&data);
    println!("Cow data: {:?}", cow_data);
    
    // Convert to owned when modification is needed
    let mut cow_data = Cow::Borrowed(&data);
    cow_data.to_mut().push(6);
    println!("Modified cow data: {:?}", cow_data);
}
```

## Common Pitfalls

### Common Ownership Mistakes
```rust
// 1. Trying to use moved values
fn moved_values() {
    let s = String::from("hello");
    let s2 = s; // s is moved to s2
    
    // Bad: trying to use s after move
    // println!("{}", s); // Error: value borrowed after move
    
    // Good: use s2 instead
    println!("{}", s2);
}

// 2. Creating reference cycles
fn reference_cycles() {
    use std::rc::Rc;
    use std::cell::RefCell;
    
    #[derive(Debug)]
    struct Node {
        value: i32,
        next: RefCell<Option<Rc<Node>>>,
    }
    
    impl Node {
        fn new(value: i32) -> Self {
            Node {
                value,
                next: RefCell::new(None),
            }
        }
        
        fn set_next(&self, node: Rc<Node>) {
            *self.next.borrow_mut() = Some(node);
        }
    }
    
    let node1 = Rc::new(Node::new(1));
    let node2 = Rc::new(Node::new(2));
    
    node1.set_next(node2.clone());
    node2.set_next(node1.clone()); // Creates a cycle
    
    // Reference count never reaches 0, causing memory leak
    println!("Node1 count: {}", Rc::strong_count(&node1));
    println!("Node2 count: {}", Rc::strong_count(&node2));
}

// 3. Mixing borrowed and owned data incorrectly
fn mixed_borrowed_owned() {
    let mut vec = vec![1, 2, 3, 4, 5];
    
    // Bad: mixing borrowed and owned data
    let slice = &vec[1..3];
    vec.push(6); // Error: cannot borrow while mutable borrow exists
    
    // Good: use separate scopes
    let sum: i32 = slice.iter().sum();
    vec.push(6);
    println!("Sum: {}, Extended: {:?}", sum, vec);
}

// 4. Not understanding lifetime annotations
fn lifetime_annotations() {
    // Bad: missing lifetime annotation when needed
    // fn bad_function(x: &str, y: &str) -> &str {
    //     if x.len() > y.len() { x } else { y } // Error: lifetime mismatch
    // }
    
    // Good: explicit lifetime annotation
    fn good_function<'a>(x: &'a str, y: &'a str) -> &'a str {
        if x.len() > y.len() { x } else { y }
    }
    
    let s1 = String::from("hello");
    let s2 = String::world();
    
    let longer = good_function(&s1, &s2);
    println!("Longer: {}", longer);
}

// 5. Using Box when not needed
fn unnecessary_box() {
    // Bad: boxing small data
    let boxed_int = Box::new(42);
    println!("Boxed int: {}", boxed_int);
    
    // Good: use stack allocation for small data
    let stack_int = 42;
    println!("Stack int: {}", stack_int);
    
    // Good: use Box for large data
    let large_vec = vec![0; 1000000];
    let boxed_vec = Box::new(large_vec);
    println!("Boxed vec length: {}", boxed_vec.len());
}

// 6. Not understanding Copy trait
fn copy_trait_misunderstanding() {
    // Bad: assuming all types are Copy
    let s1 = String::from("hello");
    let s2 = s1; // s1 is moved, not copied
    
    // Good: understand which types implement Copy
    let i1 = 42;
    let i2 = i1; // i1 is copied because i32 implements Copy
    
    #[derive(Debug, Clone, Copy)]
    struct Point {
        x: i32,
        y: i32,
    }
    
    let p1 = Point { x: 1, y: 2 };
    let p2 = p1; // p1 is copied because Point implements Copy
    println!("Point 1: {:?}, Point 2: {:?}", p1, p2);
}

// 7. Not using appropriate smart pointer
fn inappropriate_smart_pointer() {
    // Bad: using Rc in multi-threaded code
    use std::rc::Rc;
    use std::thread;
    
    let data = Rc::new(vec![1, 2, 3]);
    let handles: Vec<_> = (0..10).map(|_| {
        let data_clone = data.clone();
        thread::spawn(move || {
            println!("Thread sees: {:?}", data_clone);
        })
    }).collect();
    
    // This can cause data races
    for handle in handles {
        handle.join().unwrap();
    }
    
    // Good: use Arc in multi-threaded code
    use std::sync::Arc;
    
    let data = Arc::new(vec![1, 2, 3]);
    let handles: Vec<_> = (0..10).map(|_| {
        let data_clone = Arc::clone(&data);
        thread::spawn(move || {
            println!("Thread sees: {:?}", data_clone);
        })
    }).collect();
    
    for handle in handles {
        handle.join().unwrap();
    }
}

// 8. Not using static lifetime correctly
fn static_lifetime_misuse() {
    // Bad: trying to return reference to local variable
    // fn bad_function() -> &str {
    //     let s = String::from("hello");
    //     &s // Error: s will be dropped
    // }
    
    // Good: return owned value or static reference
    fn good_function() -> String {
        "hello".to_string()
    }
    
    fn static_function() -> &'static str {
        "hello"
    }
    
    let owned = good_function();
    let static_ref = static_function();
    
    println!("Owned: {}", owned);
    println!("Static: {}", static_ref);
}
```

## Summary

Rust's ownership system provides memory safety without garbage collection:

**Core Concepts:**
- Three rules of ownership
- Move semantics for transferring ownership
- Borrowing for temporary access
- Reference counting for shared ownership

**Ownership Rules:**
1. Each value has exactly one owner
2. Owner can transfer ownership
3. Value is dropped when owner goes out of scope

**Borrowing:**
- Immutable references (&T) for read-only access
- Mutable references (&mut T) for modification
- Slices for partial views
- Lifetime annotations for complex scenarios

**Smart Pointers:**
- Box for heap allocation and single ownership
- Rc for shared ownership (single-threaded)
- Arc for shared ownership (multi-threaded)
- Weak for breaking reference cycles

**Lifetime Features:**
- Lifetime annotations for explicit control
- Lifetime elision rules for common cases
- Static lifetime for program-wide data
- Lifetime bounds in generics

**Best Practices:**
- Use references to avoid unnecessary moves
- Use Rc for shared ownership when needed
- Use Arc for thread-safe sharing
- Use Box for large or recursive data
- Avoid unnecessary cloning
- Use appropriate smart pointers

**Common Pitfalls:**
- Using moved values after transfer
- Creating reference cycles
- Mixing borrowed and owned data
- Misunderstanding lifetime annotations
- Using Box when not needed
- Not understanding Copy trait
- Inappropriate smart pointer choice
- Static lifetime misuse

Rust's ownership system, combined with its borrowing and lifetime features, ensures memory safety and prevents common programming errors like use-after-free, double-free, and data races, all without requiring garbage collection.
